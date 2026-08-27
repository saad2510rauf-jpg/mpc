<?php
/**
 * Core theme setup: supports, menus, widget areas, assets, navigation walker helpers.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme support.
 */
function mpc_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
	add_theme_support( 'custom-logo', array(
		'height'      => 60,
		'width'       => 220,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );

	// WooCommerce.
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'my-peptide-core' ),
		'footer'  => __( 'Footer Menu', 'my-peptide-core' ),
	) );
}
add_action( 'after_setup_theme', 'mpc_theme_setup' );

/**
 * Enqueue styles and scripts.
 */
function mpc_enqueue_assets() {
	wp_enqueue_style(
		'mpc-google-font-inter',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'my-peptide-core-style', get_stylesheet_uri(), array(), MPC_THEME_VERSION );
	wp_enqueue_script(
		'my-peptide-core-navigation',
		MPC_THEME_URI . '/assets/js/navigation.js',
		array(),
		MPC_THEME_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'mpc_enqueue_assets' );

/**
 * Register footer widget area.
 */
function mpc_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Footer', 'my-peptide-core' ),
		'id'            => 'footer-1',
		'description'   => __( 'Add widgets to the footer.', 'my-peptide-core' ),
		'before_widget' => '<div class="footer-widget">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4>',
		'after_title'   => '</h4>',
	) );
}
add_action( 'widgets_init', 'mpc_widgets_init' );

/**
 * Fallback menu when no primary menu is assigned yet.
 */
function mpc_fallback_menu() {
	echo '<ul>';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'my-peptide-core' ) . '</a></li>';
	if ( class_exists( 'WooCommerce' ) && function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'shop' ) > 0 ) {
		echo '<li><a href="' . esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ) . '">' . esc_html__( 'Shop', 'my-peptide-core' ) . '</a></li>';
	}
	echo '<li><a href="' . esc_url( home_url( '/research-use-disclaimer/' ) ) . '">' . esc_html__( 'Research Use Disclaimer', 'my-peptide-core' ) . '</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/contact/' ) ) . '">' . esc_html__( 'Contact', 'my-peptide-core' ) . '</a></li>';
	echo '</ul>';
}

/**
 * Cart count fragment for the header cart icon (AJAX add-to-cart friendly).
 */
function mpc_cart_count_markup() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return '';
	}
	$count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
	return '<span class="count">' . intval( $count ) . '</span>';
}

function mpc_cart_fragments( $fragments ) {
	ob_start();
	echo mpc_cart_count_markup();
	$fragments['span.count'] = ob_get_clean();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'mpc_cart_fragments' );
