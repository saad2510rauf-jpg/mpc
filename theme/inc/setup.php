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
		'mpc-google-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Sora:wght@500;600;700&display=swap',
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

	if ( is_front_page() ) {
		wp_enqueue_script(
			'my-peptide-core-landing',
			MPC_THEME_URI . '/assets/js/landing.js',
			array(),
			MPC_THEME_VERSION,
			true
		);
		wp_localize_script( 'my-peptide-core-landing', 'mpcLanding', array(
			'saleEnd'       => mpc_get_sale_end_iso(),
			'rollingHours'  => mpc_get_rolling_sale_hours(),
			'baseCurrency'  => class_exists( 'WooCommerce' ) ? get_woocommerce_currency() : 'EUR',
		) );
	}
}
add_action( 'wp_enqueue_scripts', 'mpc_enqueue_assets' );

/**
 * Small wrapper so theme mods have sane fallbacks.
 */
function mpc_get_theme_mod_or_option( $key, $default = '' ) {
	$value = get_theme_mod( $key, '' );
	return '' !== $value ? $value : $default;
}

/**
 * Hours the rolling countdown runs for when no fixed end date is set.
 * Set to 0 (or filter to 0) to hide the countdown unless a real date exists.
 */
function mpc_get_rolling_sale_hours() {
	return (int) apply_filters( 'mpc_rolling_sale_hours', (int) mpc_get_theme_mod_or_option( 'mpc_sale_rolling_hours', 48 ) );
}

/**
 * Fixed sale end timestamp from the Customizer, or false when unset/past.
 */
function mpc_get_sale_end_timestamp() {
	$raw = mpc_get_theme_mod_or_option( 'mpc_sale_end', '' );
	if ( ! $raw ) {
		return false;
	}
	$ts = strtotime( $raw . ' ' . wp_timezone_string() );
	return ( $ts && $ts > time() ) ? $ts : false;
}

/**
 * Fixed sale end as an ISO 8601 string for the front-end, or '' when unset.
 */
function mpc_get_sale_end_iso() {
	$ts = mpc_get_sale_end_timestamp();
	return $ts ? gmdate( 'c', $ts ) : '';
}

/**
 * Whether the hero countdown should render at all.
 *
 * A fixed Customizer end date always wins. Otherwise the rolling window
 * runs, unless it has been set to zero hours.
 */
function mpc_countdown_is_active() {
	return (bool) mpc_get_sale_end_timestamp() || mpc_get_rolling_sale_hours() > 0;
}

/**
 * Customizer: homepage hero / sale settings. Countdown + flash badge only
 * ever show when an admin has actually set a real, future end date —
 * no fake "always 48h left" urgency.
 */
function mpc_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'mpc_hero', array(
		'title'    => __( 'Homepage Hero', 'my-peptide-core' ),
		'priority' => 30,
	) );

	$wp_customize->add_setting( 'mpc_hero_headline', array(
		'default'           => __( 'Precision-Made Research Peptides You Can Trust', 'my-peptide-core' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'mpc_hero_headline', array(
		'label'   => __( 'Hero headline', 'my-peptide-core' ),
		'section' => 'mpc_hero',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'mpc_hero_subhead', array(
		'default'           => __( 'Save more the more you order — savings are applied automatically in your cart.', 'my-peptide-core' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'mpc_hero_subhead', array(
		'label'   => __( 'Hero subheading', 'my-peptide-core' ),
		'section' => 'mpc_hero',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'mpc_sale_end', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'mpc_sale_end', array(
		'label'       => __( 'Fixed flash sale end date/time (optional)', 'my-peptide-core' ),
		'description' => __( 'Format: YYYY-MM-DD HH:MM, site timezone. Set this for a real deadline that counts down to the same moment for every visitor. Leave blank to use the rolling window below instead.', 'my-peptide-core' ),
		'section'     => 'mpc_hero',
		'type'        => 'text',
	) );

	$wp_customize->add_setting( 'mpc_sale_rolling_hours', array(
		'default'           => 48,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'mpc_sale_rolling_hours', array(
		'label'       => __( 'Rolling countdown window (hours)', 'my-peptide-core' ),
		'description' => __( 'Used only when no fixed end date is set above. The countdown starts from each visitor\'s first visit and is remembered in their browser, so it keeps ticking down across page loads instead of restarting. Set to 0 to hide the countdown entirely.', 'my-peptide-core' ),
		'section'     => 'mpc_hero',
		'type'        => 'number',
		'input_attrs' => array( 'min' => 0, 'max' => 720, 'step' => 1 ),
	) );
}
add_action( 'customize_register', 'mpc_customize_register' );

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
 * Currencies the store can genuinely charge in.
 *
 * Only returns more than one when a multi-currency plugin has registered
 * them through the `woocommerce_currencies` ecosystem via this filter, so
 * the header never advertises a currency checkout cannot honour.
 */
function mpc_get_available_currencies() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return array();
	}
	$base = get_woocommerce_currency();
	$list = array( $base => $base );
	return apply_filters( 'mpc_available_currencies', $list );
}

/**
 * Translated versions of the current page.
 *
 * Reads Polylang / WPML when present; otherwise returns just the site
 * language, which hides the picker.
 */
function mpc_get_available_languages() {
	$languages = array();

	if ( function_exists( 'pll_the_languages' ) ) {
		$raw = pll_the_languages( array( 'raw' => 1 ) );
		if ( is_array( $raw ) ) {
			foreach ( $raw as $lang ) {
				$languages[] = array(
					'name'    => $lang['name'],
					'url'     => $lang['url'],
					'current' => ! empty( $lang['current_lang'] ),
				);
			}
		}
	} elseif ( function_exists( 'icl_get_languages' ) ) {
		$raw = icl_get_languages( 'skip_missing=0' );
		if ( is_array( $raw ) ) {
			foreach ( $raw as $lang ) {
				$languages[] = array(
					'name'    => $lang['native_name'],
					'url'     => $lang['url'],
					'current' => ! empty( $lang['active'] ),
				);
			}
		}
	}

	return apply_filters( 'mpc_available_languages', $languages );
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
