<?php
/**
 * WooCommerce integration: layout hooks, product grid settings, and
 * research-use-only compliance touches (product notice + checkout consent).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Replace WooCommerce's default wrappers with theme-matching markup.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

function mpc_wc_wrapper_start() {
	echo '<main id="primary" class="site-main container" style="padding:56px 0;">';
}
function mpc_wc_wrapper_end() {
	echo '</main>';
}
add_action( 'woocommerce_before_main_content', 'mpc_wc_wrapper_start', 10 );
add_action( 'woocommerce_after_main_content', 'mpc_wc_wrapper_end', 10 );

/**
 * Four products per row, 12 per page on the shop archive.
 */
add_filter( 'loop_shop_columns', function () {
	return 4;
} );
add_filter( 'loop_shop_per_page', function () {
	return 12;
} );

/**
 * Small "Research Use Only" tag above each product card in loops.
 */
add_action( 'woocommerce_before_shop_loop_item_title', function () {
	echo '<span class="mpc-ruo-tag">' . esc_html__( 'RUO', 'my-peptide-core' ) . '</span>';
}, 5 );

/**
 * Disclaimer block under the Add to Cart button on single product pages.
 */
add_action( 'woocommerce_single_product_summary', 'mpc_single_product_disclaimer', 35 );
function mpc_single_product_disclaimer() {
	echo '<div class="mpc-single-disclaimer">' .
		esc_html__(
			'For laboratory and research use only. Not for human or veterinary consumption, diagnostic, or therapeutic use. Not a drug, food, or cosmetic. Sale restricted to qualified researchers and institutions.',
			'my-peptide-core'
		) .
		'</div>';
}

/**
 * Required "research use" consent checkbox at checkout.
 */
add_action( 'woocommerce_review_order_before_submit', 'mpc_checkout_ruo_consent_field' );
function mpc_checkout_ruo_consent_field() {
	echo '<div class="mpc-ruo-consent">
		<label for="mpc_ruo_consent">
			<input type="checkbox" id="mpc_ruo_consent" name="mpc_ruo_consent" />
			<span>' . esc_html__(
				'I confirm I am at least 18 years old and am purchasing these research chemical products strictly for in-vitro laboratory research. I understand they are not approved for human or animal use, consumption, or treatment of any kind, and I agree to the Research Use Disclaimer.',
				'my-peptide-core'
			) . '</span>
		</label>
	</div>';
}

add_action( 'woocommerce_checkout_process', 'mpc_validate_ruo_consent_field' );
function mpc_validate_ruo_consent_field() {
	if ( ! isset( $_POST['mpc_ruo_consent'] ) ) {
		wc_add_notice(
			__( 'Please confirm the Research Use Only agreement before placing your order.', 'my-peptide-core' ),
			'error'
		);
	}
}

add_action( 'woocommerce_checkout_update_order_meta', 'mpc_save_ruo_consent_field' );
function mpc_save_ruo_consent_field( $order_id ) {
	if ( isset( $_POST['mpc_ruo_consent'] ) ) {
		update_post_meta( $order_id, '_mpc_ruo_consent', 'yes' );
	}
}

/**
 * Volume-based discount tiers, in the store's own currency (no fake FX
 * conversion involved). These are real, and are actually applied to the
 * cart below — the homepage hero only ever advertises numbers that match
 * what a shopper actually gets at checkout.
 *
 * Adjust via the `mpc_sale_tiers` filter.
 */
function mpc_get_sale_tiers() {
	return apply_filters( 'mpc_sale_tiers', array(
		array( 'threshold' => 120, 'percent' => 10 ),
		array( 'threshold' => 250, 'percent' => 15 ),
		array( 'threshold' => 450, 'percent' => 20 ),
	) );
}

/**
 * Apply the highest tier the current cart subtotal qualifies for, as a
 * negative fee so it's visible as its own line at checkout.
 */
add_action( 'woocommerce_cart_calculate_fees', 'mpc_apply_tiered_discount' );
function mpc_apply_tiered_discount( $cart ) {
	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return;
	}
	$subtotal = $cart->get_subtotal();
	$percent  = 0;
	foreach ( mpc_get_sale_tiers() as $tier ) {
		if ( $subtotal >= $tier['threshold'] && $tier['percent'] > $percent ) {
			$percent = $tier['percent'];
		}
	}
	if ( $percent > 0 ) {
		$cart->add_fee(
			sprintf( __( 'Volume discount (%d%%)', 'my-peptide-core' ), $percent ),
			-1 * ( $subtotal * ( $percent / 100 ) )
		);
	}
}

/**
 * Admin notice if WooCommerce isn't active — the theme depends on it for shop/cart/checkout.
 */
add_action( 'admin_notices', function () {
	if ( ! class_exists( 'WooCommerce' ) ) {
		echo '<div class="notice notice-error"><p>' .
			esc_html__( 'My Peptide Core requires the WooCommerce plugin to be installed and activated.', 'my-peptide-core' ) .
			'</p></div>';
	}
} );
