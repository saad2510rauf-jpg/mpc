<?php
/**
 * My Peptide Core theme bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MPC_THEME_VERSION', '2.0.0' );
define( 'MPC_THEME_DIR', get_template_directory() );
define( 'MPC_THEME_URI', get_template_directory_uri() );

require_once MPC_THEME_DIR . '/inc/setup.php';
require_once MPC_THEME_DIR . '/inc/navigation.php';
require_once MPC_THEME_DIR . '/inc/woocommerce.php';
require_once MPC_THEME_DIR . '/inc/product-fields.php';
require_once MPC_THEME_DIR . '/inc/cart-upsell.php';
require_once MPC_THEME_DIR . '/inc/compliance-pages.php';
