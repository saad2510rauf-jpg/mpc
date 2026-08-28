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
 * Cache-busting version for a theme asset.
 *
 * Uses the file's modification time so the URL changes whenever the file
 * changes. The theme version alone is not enough: a CDN caches by full URL,
 * so edits shipped without a version bump keep serving the stale file until
 * the cache expires.
 *
 * @param string $relative_path Path relative to the theme root, e.g. '/style.css'.
 */
function mpc_asset_version( $relative_path ) {
	$file = MPC_THEME_DIR . $relative_path;
	$mtime = file_exists( $file ) ? filemtime( $file ) : false;

	return $mtime ? MPC_THEME_VERSION . '.' . $mtime : MPC_THEME_VERSION;
}

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
	// Depend on WooCommerce's stylesheet where present, so the theme's rules
	// are enqueued after it and win without needing !important everywhere.
	$style_deps = wp_style_is( 'woocommerce-general', 'registered' ) ? array( 'woocommerce-general' ) : array();
	wp_enqueue_style( 'my-peptide-core-style', get_stylesheet_uri(), $style_deps, mpc_asset_version( '/style.css' ) );
	wp_enqueue_script(
		'my-peptide-core-navigation',
		MPC_THEME_URI . '/assets/js/navigation.js',
		array(),
		mpc_asset_version( '/assets/js/navigation.js' ),
		true
	);

	if ( is_front_page() ) {
		wp_enqueue_script(
			'my-peptide-core-landing',
			MPC_THEME_URI . '/assets/js/landing.js',
			array(),
			mpc_asset_version( '/assets/js/landing.js' ),
			true
		);
		wp_localize_script( 'my-peptide-core-landing', 'mpcLanding', array(
			'saleEnd'      => mpc_get_countdown_iso(),
			'repeatDays'   => 'weekly' === mpc_get_countdown_mode() ? 7 : 0,
			'baseCurrency' => class_exists( 'WooCommerce' ) ? get_woocommerce_currency() : 'EUR',
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
 * Turn *asterisk-wrapped* words into glowing highlight spans.
 *
 * The text is escaped first, so anything an editor types is treated as plain
 * text — only the markers this function adds ever become real markup.
 */
function mpc_highlight_text( $text ) {
	$escaped = esc_html( $text );

	return preg_replace(
		'/\*([^*]+)\*/',
		'<span class="mpc-hl">$1</span>',
		$escaped
	);
}

/**
 * Countdown mode: 'weekly' (default), 'fixed', or 'off'.
 */
function mpc_get_countdown_mode() {
	$mode = mpc_get_theme_mod_or_option( 'mpc_countdown_mode', 'weekly' );
	return in_array( $mode, array( 'weekly', 'fixed', 'off' ), true ) ? $mode : 'weekly';
}

/**
 * Fixed sale end timestamp from the Customizer, or false when unset/past.
 */
function mpc_get_sale_end_timestamp() {
	$raw = mpc_get_theme_mod_or_option( 'mpc_sale_end', '' );
	if ( ! $raw ) {
		return false;
	}
	try {
		$dt = new DateTimeImmutable( $raw, wp_timezone() );
	} catch ( Exception $e ) {
		return false;
	}
	$ts = $dt->getTimestamp();
	return $ts > time() ? $ts : false;
}

/**
 * Timestamp of the next recurring weekly deadline, in the site's timezone.
 *
 * Computed server-side so every visitor counts down to the same moment.
 * The day shift is applied before the time is set, so the deadline lands on
 * the intended wall-clock time even across a daylight-saving boundary.
 */
function mpc_get_weekly_deadline_timestamp() {
	$day  = (int) mpc_get_theme_mod_or_option( 'mpc_sale_weekly_day', 0 );
	$time = mpc_get_theme_mod_or_option( 'mpc_sale_weekly_time', '23:59' );

	$day = max( 0, min( 6, $day ) );
	if ( ! preg_match( '/^([01]?\d|2[0-3]):([0-5]\d)$/', $time, $m ) ) {
		$m = array( '', '23', '59' );
	}
	$hour   = (int) $m[1];
	$minute = (int) $m[2];

	$tz  = wp_timezone();
	$now = new DateTimeImmutable( 'now', $tz );

	$delta     = ( $day - (int) $now->format( 'w' ) + 7 ) % 7;
	$candidate = $now->modify( '+' . $delta . ' days' )->setTime( $hour, $minute, 0 );

	if ( $candidate->getTimestamp() <= $now->getTimestamp() ) {
		$candidate = $now->modify( '+' . ( $delta + 7 ) . ' days' )->setTime( $hour, $minute, 0 );
	}

	return $candidate->getTimestamp();
}

/**
 * The deadline the hero should count down to, or false when there is none.
 */
function mpc_get_countdown_timestamp() {
	switch ( mpc_get_countdown_mode() ) {
		case 'off':
			return false;
		case 'fixed':
			return mpc_get_sale_end_timestamp();
		default:
			return mpc_get_weekly_deadline_timestamp();
	}
}

/**
 * The countdown deadline as an ISO 8601 string, or '' when there is none.
 */
function mpc_get_countdown_iso() {
	$ts = mpc_get_countdown_timestamp();
	return $ts ? gmdate( 'c', $ts ) : '';
}

/**
 * Whether the hero countdown should render at all.
 */
function mpc_countdown_is_active() {
	return (bool) mpc_get_countdown_timestamp();
}

/**
 * Customizer: homepage hero / sale settings.
 */
function mpc_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'mpc_hero', array(
		'title'    => __( 'Homepage Hero', 'my-peptide-core' ),
		'priority' => 30,
	) );

	$wp_customize->add_setting( 'mpc_hero_headline', array(
		'default'           => __( '*Precision-Made* Research Peptides You Can *Trust*', 'my-peptide-core' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'mpc_hero_headline', array(
		'label'       => __( 'Hero headline', 'my-peptide-core' ),
		'description' => __( 'Wrap any words in *asterisks* to make them glow, e.g. *Precision-Made* Research Peptides.', 'my-peptide-core' ),
		'section'     => 'mpc_hero',
		'type'        => 'text',
	) );

	$wp_customize->add_setting( 'mpc_hero_subhead', array(
		'default'           => __( 'Save more the more you order — savings are applied automatically in your cart.', 'my-peptide-core' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'mpc_hero_subhead', array(
		'label'       => __( 'Hero subheading', 'my-peptide-core' ),
		'description' => __( '*Asterisks* make words glow here too.', 'my-peptide-core' ),
		'section'     => 'mpc_hero',
		'type'        => 'text',
	) );

	$wp_customize->add_setting( 'mpc_countdown_mode', array(
		'default'           => 'weekly',
		'sanitize_callback' => 'mpc_sanitize_countdown_mode',
	) );
	$wp_customize->add_control( 'mpc_countdown_mode', array(
		'label'       => __( 'Hero countdown', 'my-peptide-core' ),
		'description' => __( 'How the flash-sale countdown behaves. All visitors always see the same deadline, calculated in your site timezone.', 'my-peptide-core' ),
		'section'     => 'mpc_hero',
		'type'        => 'select',
		'choices'     => array(
			'weekly' => __( 'Recurring weekly deadline', 'my-peptide-core' ),
			'fixed'  => __( 'One fixed end date', 'my-peptide-core' ),
			'off'    => __( 'No countdown', 'my-peptide-core' ),
		),
	) );

	$wp_customize->add_setting( 'mpc_sale_weekly_day', array(
		'default'           => 0,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'mpc_sale_weekly_day', array(
		'label'       => __( 'Weekly deadline — day', 'my-peptide-core' ),
		'description' => __( 'Used when the countdown is set to “Recurring weekly deadline”.', 'my-peptide-core' ),
		'section'     => 'mpc_hero',
		'type'        => 'select',
		'choices'     => array(
			0 => __( 'Sunday', 'my-peptide-core' ),
			1 => __( 'Monday', 'my-peptide-core' ),
			2 => __( 'Tuesday', 'my-peptide-core' ),
			3 => __( 'Wednesday', 'my-peptide-core' ),
			4 => __( 'Thursday', 'my-peptide-core' ),
			5 => __( 'Friday', 'my-peptide-core' ),
			6 => __( 'Saturday', 'my-peptide-core' ),
		),
	) );

	$wp_customize->add_setting( 'mpc_sale_weekly_time', array(
		'default'           => '23:59',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'mpc_sale_weekly_time', array(
		'label'       => __( 'Weekly deadline — time', 'my-peptide-core' ),
		'description' => __( '24-hour format, e.g. 23:59.', 'my-peptide-core' ),
		'section'     => 'mpc_hero',
		'type'        => 'time',
	) );

	$wp_customize->add_setting( 'mpc_sale_end', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'mpc_sale_end', array(
		'label'       => __( 'Fixed end date/time', 'my-peptide-core' ),
		'description' => __( 'Used when the countdown is set to “One fixed end date”. Format: YYYY-MM-DD HH:MM, site timezone. Once this moment passes, the countdown and flash badge disappear.', 'my-peptide-core' ),
		'section'     => 'mpc_hero',
		'type'        => 'text',
	) );
}

/**
 * Sanitize the countdown mode select.
 */
function mpc_sanitize_countdown_mode( $value ) {
	return in_array( $value, array( 'weekly', 'fixed', 'off' ), true ) ? $value : 'weekly';
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
