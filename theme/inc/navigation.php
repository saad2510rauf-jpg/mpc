<?php
/**
 * Header navigation: the icon set, the dropdown structure, and the
 * language switcher data.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Inline SVGs used for product-category icons, keyed by category slug.
 *
 * `default` is used for any category without its own entry. Filter
 * `mpc_category_icons` to add or replace icons.
 *
 * @return array<string, string>
 */
function mpc_category_icons() {
	$icons = array(
		'bundles'              => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 8l-9-5-9 5 9 5 9-5z"/><path d="M3 8v8l9 5 9-5V8"/><path d="M12 13v8"/></svg>',
		'metabolic-research'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2c2 3-1 4-1 7a4 4 0 108 0c0-1.5-1-2.5-1-2.5.5 3-1 4-2 4-2 0-2-2-2-3.5C14 5 12 2 12 2z"/><path d="M8 14a4 4 0 108 0c0-2-2-3-2-5-2 1-6 3-6 5z"/></svg>',
		'growth-hormone'       => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 20V10M9 20V4M14 20v-7M19 20V7"/></svg>',
		'tissue-regeneration'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 21s-7-4.4-7-9.5A4.5 4.5 0 0112 8a4.5 4.5 0 017 3.5c0 5.1-7 9.5-7 9.5z"/></svg>',
		'longevity-research'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 2h10M7 22h10M8 2c0 5 8 5 8 10s-8 5-8 10M16 2c0 5-8 5-8 10s8 5 8 10"/></svg>',
		'cognitive-research'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 4a3 3 0 00-3 3 3 3 0 00-1 5.8A3.5 3.5 0 007 19a3 3 0 002-.8A3 3 0 0012 16V7a3 3 0 00-3-3z"/><path d="M15 4a3 3 0 013 3 3 3 0 011 5.8A3.5 3.5 0 0117 19a3 3 0 01-2-.8A3 3 0 0112 16"/></svg>',
		'cosmetic-peptides'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3c3 4 5 6.5 5 9a5 5 0 01-10 0c0-2.5 2-5 5-9z"/></svg>',
		'bioregulators'        => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 3c0 6 12 12 12 18M18 3c0 6-12 12-12 18"/><path d="M7 8h10M7 16h10"/></svg>',
		'peptide-blends'       => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="2.3"/><ellipse cx="12" cy="12" rx="9" ry="4"/><ellipse cx="12" cy="12" rx="9" ry="4" transform="rotate(60 12 12)"/><ellipse cx="12" cy="12" rx="9" ry="4" transform="rotate(120 12 12)"/></svg>',
		'accessories'          => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 3h6v4l-2 2v11a2 2 0 01-4 0V9L7 7V3z"/></svg>',
		'glossary'             => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 4h7a3 3 0 013 3v13a2.5 2.5 0 00-2.5-2.5H4z"/><path d="M20 4h-7a3 3 0 00-3 3v13a2.5 2.5 0 012.5-2.5H20z"/></svg>',
		'reconstitution-guide' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 2h6M10 2v5l-4 9a3 3 0 002.7 4.3h6.6A3 3 0 0018 16l-4-9V2"/><path d="M7.5 15h9"/></svg>',
		'stability-calculator' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3v11"/><circle cx="12" cy="17.5" r="3.5"/><path d="M9.5 5.5h5"/></svg>',
		'default'              => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="8"/><path d="M12 8v8M8 12h8"/></svg>',
	);

	return apply_filters( 'mpc_category_icons', $icons );
}

/**
 * Fetch one icon by key, falling back to the default glyph.
 */
function mpc_category_icon( $key ) {
	$icons = mpc_category_icons();
	return isset( $icons[ $key ] ) ? $icons[ $key ] : $icons['default'];
}

/**
 * Product categories for the Shop dropdown.
 *
 * @return array<int, array{label: string, url: string, icon: string}>
 */
function mpc_shop_dropdown_items() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return array();
	}

	$terms = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => false,
		'exclude'    => array( get_option( 'default_product_cat' ) ),
		'orderby'    => 'name',
		'number'     => 12,
	) );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return array();
	}

	$items = array();
	foreach ( $terms as $term ) {
		$items[] = array(
			'label' => $term->name,
			'url'   => get_term_link( $term ),
			'icon'  => mpc_category_icon( $term->slug ),
		);
	}

	return $items;
}

/**
 * Resource pages for the Resources dropdown.
 *
 * Only pages that actually exist are listed, so the menu never points at
 * a 404.
 *
 * @return array<int, array{label: string, url: string, icon: string}>
 */
function mpc_resources_dropdown_items() {
	$candidates = apply_filters( 'mpc_resource_pages', array(
		'glossary'             => __( 'Glossary', 'my-peptide-core' ),
		'reconstitution-guide' => __( 'Reconstitution Guide', 'my-peptide-core' ),
		'stability-calculator' => __( 'Stability Calculator', 'my-peptide-core' ),
	) );

	$items = array();
	foreach ( $candidates as $slug => $label ) {
		$page = get_page_by_path( $slug );
		if ( ! $page || 'publish' !== $page->post_status ) {
			continue;
		}
		$items[] = array(
			'label' => $label,
			'url'   => get_permalink( $page ),
			'icon'  => mpc_category_icon( $slug ),
		);
	}

	return $items;
}

/**
 * The header's top-level navigation.
 *
 * Defined in the theme rather than read from a WordPress menu because the
 * Shop entry is generated from live product categories and each item
 * carries an icon — structure a menu screen cannot express. Filter
 * `mpc_header_nav` to change it.
 *
 * @return array<int, array{label: string, url: string, dropdown?: array, footer?: array}>
 */
function mpc_get_header_nav() {
	$shop_url = ( class_exists( 'WooCommerce' ) && function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'shop' ) > 0 )
		? get_permalink( wc_get_page_id( 'shop' ) )
		: home_url( '/shop/' );

	$nav = array();

	$nav[] = array(
		'label' => __( 'Home', 'my-peptide-core' ),
		'url'   => home_url( '/' ),
	);

	$shop = array(
		'label'    => __( 'Shop', 'my-peptide-core' ),
		'url'      => $shop_url,
		'dropdown' => mpc_shop_dropdown_items(),
	);
	if ( ! empty( $shop['dropdown'] ) ) {
		$shop['footer'] = array(
			'label' => __( 'View All Products', 'my-peptide-core' ),
			'url'   => $shop_url,
		);
	}
	$nav[] = $shop;

	$resources = mpc_resources_dropdown_items();
	if ( ! empty( $resources ) ) {
		$nav[] = array(
			'label'    => __( 'Resources', 'my-peptide-core' ),
			'url'      => '',
			'dropdown' => $resources,
		);
	}

	$calculator = get_page_by_path( 'peptide-calculator' );
	if ( $calculator && 'publish' === $calculator->post_status ) {
		$nav[] = array(
			'label' => __( 'Peptide Calculator', 'my-peptide-core' ),
			'url'   => get_permalink( $calculator ),
		);
	}

	$contact = get_page_by_path( 'contact' );
	if ( $contact && 'publish' === $contact->post_status ) {
		$nav[] = array(
			'label' => __( 'Support', 'my-peptide-core' ),
			'url'   => get_permalink( $contact ),
		);
	}

	return apply_filters( 'mpc_header_nav', $nav );
}

/**
 * Two-letter region badge for a locale, e.g. en_GB → GB.
 */
function mpc_locale_region( $locale ) {
	if ( preg_match( '/[_-]([A-Za-z]{2})/', $locale, $m ) ) {
		return strtoupper( $m[1] );
	}

	// Locales without a region: fall back to a sensible default per language.
	$defaults = array( 'en' => 'GB', 'de' => 'DE', 'es' => 'ES', 'it' => 'IT', 'fr' => 'FR' );
	$lang     = strtolower( substr( $locale, 0, 2 ) );

	return isset( $defaults[ $lang ] ) ? $defaults[ $lang ] : strtoupper( $lang );
}

/**
 * The language currently being shown, as a label plus region badge.
 *
 * @return array{label: string, region: string}
 */
function mpc_current_language() {
	$locale = determine_locale();
	$names  = array( 'en' => 'English', 'de' => 'Deutsch', 'es' => 'Español', 'it' => 'Italiano', 'fr' => 'Français' );
	$lang   = strtolower( substr( $locale, 0, 2 ) );

	return array(
		'label'  => isset( $names[ $lang ] ) ? $names[ $lang ] : strtoupper( $lang ),
		'region' => mpc_locale_region( $locale ),
	);
}
