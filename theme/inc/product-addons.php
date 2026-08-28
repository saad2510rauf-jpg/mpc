<?php
/**
 * Optional purchase add-ons (reconstitution water, needles, swabs).
 *
 * Implemented as cart-item add-ons rather than product variations: four
 * option groups would otherwise produce a large matrix of variations to
 * create and keep in stock for every product.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add-on groups.
 *
 * Each option's `price` is added to the line item in store currency.
 * Filter `mpc_product_addons` to change these, or return an empty array
 * (optionally per product) to switch add-ons off.
 *
 * @param int $product_id Product being displayed.
 * @return array<string, array{label: string, options: array<string, array{label: string, price: float}>}>
 */
function mpc_get_product_addons( $product_id = 0 ) {
	$addons = array(
		'bac_water' => array(
			'label'   => __( 'Bacteriostatic water', 'my-peptide-core' ),
			'options' => array(
				'none'  => array( 'label' => __( 'None', 'my-peptide-core' ), 'price' => 0.0 ),
				'3ml'   => array( 'label' => __( '3 ml', 'my-peptide-core' ), 'price' => 4.0 ),
				'10ml'  => array( 'label' => __( '10 ml', 'my-peptide-core' ), 'price' => 9.0 ),
			),
		),
		'needles' => array(
			'label'   => __( 'Insulin needles', 'my-peptide-core' ),
			'options' => array(
				'none'      => array( 'label' => __( 'None', 'my-peptide-core' ), 'price' => 0.0 ),
				'10x0_5ml'  => array( 'label' => __( '10 × 0.5 ml', 'my-peptide-core' ), 'price' => 5.0 ),
				'10x1ml'    => array( 'label' => __( '10 × 1 ml', 'my-peptide-core' ), 'price' => 5.0 ),
			),
		),
		'swabs' => array(
			'label'   => __( 'Alcohol swabs', 'my-peptide-core' ),
			'options' => array(
				'none'  => array( 'label' => __( 'None', 'my-peptide-core' ), 'price' => 0.0 ),
				'10'    => array( 'label' => __( 'Pack of 10', 'my-peptide-core' ), 'price' => 3.0 ),
			),
		),
	);

	return apply_filters( 'mpc_product_addons', $addons, $product_id );
}

/**
 * Resolve a submitted option to its definition, or null when invalid.
 */
function mpc_resolve_addon_option( $group_key, $option_key, $product_id = 0 ) {
	$addons = mpc_get_product_addons( $product_id );
	if ( ! isset( $addons[ $group_key ]['options'][ $option_key ] ) ) {
		return null;
	}
	return $addons[ $group_key ]['options'][ $option_key ];
}

/* -------------------------------------------------------------
   FRONT END: selectors on the product page
------------------------------------------------------------- */

add_action( 'woocommerce_before_add_to_cart_button', 'mpc_render_product_addons' );
function mpc_render_product_addons() {
	global $product;
	if ( ! $product ) {
		return;
	}

	$addons = mpc_get_product_addons( $product->get_id() );
	if ( empty( $addons ) ) {
		return;
	}

	echo '<div class="mpc-addons">';
	foreach ( $addons as $group_key => $group ) {
		$field_id = 'mpc_addon_' . $group_key;
		printf( '<p class="mpc-addon"><label for="%s">%s</label>', esc_attr( $field_id ), esc_html( $group['label'] ) );
		printf( '<select id="%s" name="mpc_addon[%s]">', esc_attr( $field_id ), esc_attr( $group_key ) );
		foreach ( $group['options'] as $option_key => $option ) {
			printf(
				'<option value="%s">%s%s</option>',
				esc_attr( $option_key ),
				esc_html( $option['label'] ),
				$option['price'] > 0 ? ' (+' . wp_strip_all_tags( wc_price( $option['price'] ) ) . ')' : ''
			);
		}
		echo '</select></p>';
	}
	echo '</div>';
}

/* -------------------------------------------------------------
   CART: capture, validate, price
------------------------------------------------------------- */

add_filter( 'woocommerce_add_cart_item_data', 'mpc_add_addons_to_cart_item', 10, 3 );
function mpc_add_addons_to_cart_item( $cart_item_data, $product_id, $variation_id ) {
	if ( empty( $_POST['mpc_addon'] ) || ! is_array( $_POST['mpc_addon'] ) ) {
		return $cart_item_data;
	}

	$selected = array();
	foreach ( wp_unslash( $_POST['mpc_addon'] ) as $group_key => $option_key ) {
		$group_key  = sanitize_key( $group_key );
		$option_key = sanitize_text_field( $option_key );

		$option = mpc_resolve_addon_option( $group_key, $option_key, $product_id );
		if ( null === $option || $option['price'] <= 0 ) {
			continue; // Unknown option, or a free "None" that needs no record.
		}

		$selected[ $group_key ] = array(
			'option' => $option_key,
			'label'  => $option['label'],
			'price'  => (float) $option['price'],
		);
	}

	if ( ! empty( $selected ) ) {
		$cart_item_data['mpc_addons'] = $selected;
	}

	return $cart_item_data;
}

/**
 * Add the selected add-on prices to the line item price.
 *
 * The new price is derived from the product's own stored price rather than
 * the cart item's current price, which makes this idempotent: WooCommerce
 * fires this hook several times per request (cart, mini-cart, AJAX) and a
 * relative adjustment would compound with each pass.
 */
add_action( 'woocommerce_before_calculate_totals', 'mpc_apply_addon_prices', 20 );
function mpc_apply_addon_prices( $cart ) {
	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return;
	}

	foreach ( $cart->get_cart() as $cart_item ) {
		if ( empty( $cart_item['mpc_addons'] ) ) {
			continue;
		}

		$extra = 0.0;
		foreach ( $cart_item['mpc_addons'] as $group_key => $selection ) {
			// Re-resolve against the definition so a stale cart cannot carry a
			// price that no longer exists, and a forged POST cannot set one.
			$option = mpc_resolve_addon_option( $group_key, $selection['option'], $cart_item['product_id'] );
			if ( null !== $option ) {
				$extra += (float) $option['price'];
			}
		}

		if ( $extra <= 0 ) {
			continue;
		}

		$source_id = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];
		$source    = wc_get_product( $source_id );
		if ( ! $source ) {
			continue;
		}

		$cart_item['data']->set_price( (float) $source->get_price( 'edit' ) + $extra );
	}
}

/**
 * Show the selections in the cart and checkout line items.
 */
add_filter( 'woocommerce_get_item_data', 'mpc_display_addons_in_cart', 10, 2 );
function mpc_display_addons_in_cart( $item_data, $cart_item ) {
	if ( empty( $cart_item['mpc_addons'] ) ) {
		return $item_data;
	}

	$addons = mpc_get_product_addons( $cart_item['product_id'] );
	foreach ( $cart_item['mpc_addons'] as $group_key => $selection ) {
		$item_data[] = array(
			'key'   => isset( $addons[ $group_key ]['label'] ) ? $addons[ $group_key ]['label'] : $group_key,
			'value' => $selection['label'],
		);
	}

	return $item_data;
}

/**
 * Persist the selections onto the order line item.
 */
add_action( 'woocommerce_checkout_create_order_line_item', 'mpc_save_addons_to_order_item', 10, 4 );
function mpc_save_addons_to_order_item( $item, $cart_item_key, $values, $order ) {
	if ( empty( $values['mpc_addons'] ) ) {
		return;
	}

	$addons = mpc_get_product_addons( $values['product_id'] );
	foreach ( $values['mpc_addons'] as $group_key => $selection ) {
		$label = isset( $addons[ $group_key ]['label'] ) ? $addons[ $group_key ]['label'] : $group_key;
		$item->add_meta_data( $label, $selection['label'], true );
	}
}
