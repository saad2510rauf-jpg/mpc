<?php
/**
 * Cart upsell: offer lab accessories as real products before checkout.
 *
 * These are ordinary WooCommerce products rather than synthetic line-item
 * add-ons, so they carry their own SKU, stock, tax class and shipping
 * weight, and appear on orders and reports like anything else sold.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Slug of the category holding upsell accessories.
 */
function mpc_upsell_category_slug() {
	return apply_filters( 'mpc_upsell_category_slug', 'accessories' );
}

/**
 * Accessory products not already in the cart.
 *
 * @return WC_Product[]
 */
function mpc_get_upsell_products() {
	if ( ! function_exists( 'wc_get_products' ) || ! WC()->cart ) {
		return array();
	}

	$in_cart = array();
	foreach ( WC()->cart->get_cart() as $cart_item ) {
		$in_cart[] = (int) $cart_item['product_id'];
	}

	// Nothing to upsell against an empty cart.
	if ( empty( $in_cart ) ) {
		return array();
	}

	$products = wc_get_products( array(
		'status'   => 'publish',
		'limit'    => 4,
		'category' => array( mpc_upsell_category_slug() ),
		'exclude'  => $in_cart,
		'orderby'  => 'menu_order',
		'order'    => 'ASC',
	) );

	return array_filter( $products, function ( $product ) {
		return $product->is_purchasable() && $product->is_in_stock();
	} );
}

/**
 * Render the upsell above the cart totals, before checkout.
 */
add_action( 'woocommerce_before_cart_collaterals', 'mpc_render_cart_upsell' );
function mpc_render_cart_upsell() {
	$products = mpc_get_upsell_products();
	if ( empty( $products ) ) {
		return;
	}

	echo '<section class="mpc-upsell">';
	echo '<h2>' . esc_html__( 'Frequently added with your order', 'my-peptide-core' ) . '</h2>';
	echo '<p class="mpc-upsell-intro">' . esc_html__( 'Laboratory consumables for reconstitution and handling.', 'my-peptide-core' ) . '</p>';
	echo '<div class="mpc-upsell-grid">';

	foreach ( $products as $product ) {
		echo '<div class="mpc-upsell-card">';

		printf(
			'<a class="mpc-upsell-img" href="%s">%s</a>',
			esc_url( $product->get_permalink() ),
			wp_kses_post( $product->get_image( 'woocommerce_thumbnail' ) )
		);

		echo '<div class="mpc-upsell-info">';
		printf(
			'<a class="mpc-upsell-name" href="%s">%s</a>',
			esc_url( $product->get_permalink() ),
			esc_html( $product->get_name() )
		);
		echo '<div class="mpc-upsell-price">' . wp_kses_post( $product->get_price_html() ) . '</div>';
		echo '</div>';

		printf(
			'<a href="%s" class="button mpc-upsell-add" data-product_id="%d" rel="nofollow">%s</a>',
			esc_url( $product->add_to_cart_url() ),
			esc_attr( $product->get_id() ),
			esc_html__( 'Add', 'my-peptide-core' )
		);

		echo '</div>';
	}

	echo '</div></section>';
}

/**
 * Send the shopper back to the cart after adding an upsell item, rather than
 * to the accessory's own product page.
 */
add_filter( 'woocommerce_add_to_cart_redirect', 'mpc_upsell_redirect_back_to_cart' );
function mpc_upsell_redirect_back_to_cart( $url ) {
	if ( empty( $_REQUEST['add-to-cart'] ) ) {
		return $url;
	}

	$referer = wp_get_referer();
	if ( $referer && function_exists( 'wc_get_cart_url' ) && false !== strpos( $referer, wc_get_cart_url() ) ) {
		return wc_get_cart_url();
	}

	return $url;
}
