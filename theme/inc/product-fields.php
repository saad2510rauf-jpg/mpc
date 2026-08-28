<?php
/**
 * Peptide specification fields for products, plus the single-product layout
 * additions that render them.
 *
 * Everything here is driven by WooCommerce hooks rather than template
 * overrides, so WooCommerce can update its own templates without this theme
 * silently shipping a stale copy.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The spec fields, in display order.
 *
 * @return array<string, array{label: string, type: string, placeholder: string}>
 */
function mpc_get_product_spec_fields() {
	return array(
		'_mpc_size' => array(
			'label'       => __( 'Size / presentation', 'my-peptide-core' ),
			'type'        => 'text',
			'placeholder' => __( 'e.g. 10 mg lyophilised vial', 'my-peptide-core' ),
		),
		'_mpc_purity' => array(
			'label'       => __( 'Purity', 'my-peptide-core' ),
			'type'        => 'text',
			'placeholder' => __( 'e.g. 99.2', 'my-peptide-core' ),
		),
		'_mpc_cas' => array(
			'label'       => __( 'CAS number', 'my-peptide-core' ),
			'type'        => 'text',
			'placeholder' => __( 'e.g. 137525-51-0', 'my-peptide-core' ),
		),
		'_mpc_formula' => array(
			'label'       => __( 'Molecular formula', 'my-peptide-core' ),
			'type'        => 'text',
			'placeholder' => __( 'e.g. C62H98N16O22', 'my-peptide-core' ),
		),
		'_mpc_mol_weight' => array(
			'label'       => __( 'Molecular weight', 'my-peptide-core' ),
			'type'        => 'text',
			'placeholder' => __( 'e.g. 1419.5 g/mol', 'my-peptide-core' ),
		),
		'_mpc_sequence' => array(
			'label'       => __( 'Sequence', 'my-peptide-core' ),
			'type'        => 'textarea',
			'placeholder' => __( 'e.g. Gly-Glu-Pro-Pro-Pro-Gly-Lys-Pro-Ala-Asp-Asp-Ala-Gly-Leu-Val', 'my-peptide-core' ),
		),
		'_mpc_storage' => array(
			'label'       => __( 'Storage', 'my-peptide-core' ),
			'type'        => 'text',
			'placeholder' => __( 'e.g. Store lyophilised at -20°C, protected from light', 'my-peptide-core' ),
		),
		'_mpc_coa_url' => array(
			'label'       => __( 'Certificate of Analysis URL', 'my-peptide-core' ),
			'type'        => 'url',
			'placeholder' => __( 'https://…', 'my-peptide-core' ),
		),
	);
}

/**
 * Read a spec value for a product.
 */
function mpc_get_product_spec( $product_id, $key ) {
	$value = get_post_meta( $product_id, $key, true );
	return is_string( $value ) ? trim( $value ) : '';
}

/**
 * All non-empty specs for a product, keyed by field.
 */
function mpc_get_product_specs( $product_id ) {
	$specs = array();
	foreach ( mpc_get_product_spec_fields() as $key => $field ) {
		$value = mpc_get_product_spec( $product_id, $key );
		if ( '' !== $value ) {
			$specs[ $key ] = $value;
		}
	}
	return $specs;
}

/* -------------------------------------------------------------
   ADMIN: product data panel
------------------------------------------------------------- */

add_filter( 'woocommerce_product_data_tabs', 'mpc_product_data_tab' );
function mpc_product_data_tab( $tabs ) {
	$tabs['mpc_specs'] = array(
		'label'    => __( 'Peptide specs', 'my-peptide-core' ),
		'target'   => 'mpc_specs_product_data',
		'class'    => array(),
		'priority' => 25,
	);
	return $tabs;
}

add_action( 'woocommerce_product_data_panels', 'mpc_product_data_panel' );
function mpc_product_data_panel() {
	global $post;
	echo '<div id="mpc_specs_product_data" class="panel woocommerce_options_panel hidden">';
	echo '<div class="options_group">';

	foreach ( mpc_get_product_spec_fields() as $key => $field ) {
		$args = array(
			'id'          => $key,
			'label'       => $field['label'],
			'placeholder' => $field['placeholder'],
			'desc_tip'    => false,
			'value'       => mpc_get_product_spec( $post->ID, $key ),
		);

		if ( 'textarea' === $field['type'] ) {
			woocommerce_wp_textarea_input( $args );
		} else {
			woocommerce_wp_text_input( $args );
		}
	}

	echo '</div></div>';
}

add_action( 'woocommerce_process_product_meta', 'mpc_save_product_specs' );
function mpc_save_product_specs( $post_id ) {
	// WooCommerce verifies its own nonce before firing this hook.
	foreach ( mpc_get_product_spec_fields() as $key => $field ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}
		$raw = wp_unslash( $_POST[ $key ] );

		switch ( $field['type'] ) {
			case 'url':
				$value = esc_url_raw( $raw );
				break;
			case 'textarea':
				$value = sanitize_textarea_field( $raw );
				break;
			default:
				$value = sanitize_text_field( $raw );
		}

		if ( '' === $value ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}
}

/* -------------------------------------------------------------
   FRONT END: single product layout
------------------------------------------------------------- */

/**
 * RUO badge above the product title.
 */
add_action( 'woocommerce_single_product_summary', 'mpc_single_product_ruo_badge', 4 );
function mpc_single_product_ruo_badge() {
	echo '<span class="mpc-ruo-tag">' . esc_html__( 'Research Use Only', 'my-peptide-core' ) . '</span>';
}

/**
 * Key specs as chips directly under the price, where they carry the most
 * weight for a buyer comparing listings.
 */
add_action( 'woocommerce_single_product_summary', 'mpc_single_product_spec_chips', 11 );
function mpc_single_product_spec_chips() {
	global $product;
	if ( ! $product ) {
		return;
	}

	$chips = array();
	$purity = mpc_get_product_spec( $product->get_id(), '_mpc_purity' );
	$size   = mpc_get_product_spec( $product->get_id(), '_mpc_size' );

	if ( '' !== $purity ) {
		/* translators: %s: purity percentage. */
		$chips[] = sprintf( __( '≥%s%% HPLC purity', 'my-peptide-core' ), $purity );
	}
	if ( '' !== $size ) {
		$chips[] = $size;
	}
	if ( empty( $chips ) ) {
		return;
	}

	echo '<div class="mpc-spec-chips">';
	foreach ( $chips as $chip ) {
		echo '<span class="mpc-spec-chip">' . esc_html( $chip ) . '</span>';
	}
	echo '</div>';
}

/**
 * Certificate of Analysis link, shown under the add-to-cart area.
 */
add_action( 'woocommerce_single_product_summary', 'mpc_single_product_coa_link', 34 );
function mpc_single_product_coa_link() {
	global $product;
	if ( ! $product ) {
		return;
	}
	$url = mpc_get_product_spec( $product->get_id(), '_mpc_coa_url' );
	if ( '' === $url ) {
		return;
	}
	printf(
		'<a class="mpc-coa-link" href="%s" target="_blank" rel="noopener noreferrer">%s<span class="screen-reader-text">%s</span></a>',
		esc_url( $url ),
		esc_html__( 'View Certificate of Analysis', 'my-peptide-core' ),
		esc_html__( '(opens in a new tab)', 'my-peptide-core' )
	);
}

/**
 * Category eyebrow and stock state above the product title.
 */
add_action( 'woocommerce_single_product_summary', 'mpc_single_product_eyebrow', 3 );
function mpc_single_product_eyebrow() {
	global $product;
	if ( ! $product ) {
		return;
	}

	echo '<div class="mpc-product-eyebrow">';

	$terms = get_the_terms( $product->get_id(), 'product_cat' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		$term = reset( $terms );
		printf(
			'<a class="mpc-product-cat" href="%s">%s</a>',
			esc_url( get_term_link( $term ) ),
			esc_html( $term->name )
		);
	}

	printf(
		'<span class="mpc-stock %s">%s</span>',
		$product->is_in_stock() ? 'in-stock' : 'out-of-stock',
		$product->is_in_stock()
			? esc_html__( 'In stock', 'my-peptide-core' )
			: esc_html__( 'Out of stock', 'my-peptide-core' )
	);

	echo '</div>';
}

/**
 * Fulfilment reassurances under the add-to-cart button.
 *
 * Deliberately states dispatch commitments only — no delivery-date estimate,
 * since that depends on carrier and destination and would be a promise the
 * store cannot verify from here.
 */
add_action( 'woocommerce_single_product_summary', 'mpc_single_product_assurances', 33 );
function mpc_single_product_assurances() {
	$items = apply_filters( 'mpc_product_assurances', array(
		__( 'Dispatched same day on orders placed before 15:00', 'my-peptide-core' ),
		__( 'Tracked, door-to-door delivery', 'my-peptide-core' ),
		__( 'Discreet, temperature-appropriate packaging', 'my-peptide-core' ),
		__( 'Batch-specific Certificate of Analysis', 'my-peptide-core' ),
	) );

	if ( empty( $items ) ) {
		return;
	}

	echo '<ul class="mpc-assurances">';
	foreach ( $items as $item ) {
		echo '<li>' . esc_html( $item ) . '</li>';
	}
	echo '</ul>';
}

/**
 * Replace the tabbed area with a linear, full-width body: specifications,
 * then the long-form description, then the compliance notice.
 */
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
add_action( 'woocommerce_after_single_product_summary', 'mpc_single_product_body', 10 );
function mpc_single_product_body() {
	global $product;
	if ( ! $product ) {
		return;
	}

	echo '<div class="mpc-product-body">';

	mpc_render_product_specs();

	$description = $product->get_description();
	if ( $description ) {
		echo '<div class="mpc-product-description">' . wp_kses_post( wpautop( $description ) ) . '</div>';
	}

	echo '<div class="mpc-product-ruo">';
	echo '<h3>' . esc_html__( 'Research Use Disclaimer', 'my-peptide-core' ) . '</h3>';
	echo '<p>' . esc_html__(
		'This product is supplied strictly for in-vitro laboratory research by qualified professionals. It is not a drug, food, cosmetic, or dietary supplement, and it is not approved for human or veterinary consumption, administration, diagnostic use, or therapeutic use of any kind. Nothing on this page describes or recommends use in humans or animals. Purchasers are responsible for handling, storing, and disposing of research materials in accordance with the laws and institutional requirements of their jurisdiction.',
		'my-peptide-core'
	) . '</p>';
	echo '</div>';

	echo '</div>';
}

/**
 * The specification table.
 */
function mpc_render_product_specs() {
	global $product;
	$specs  = mpc_get_product_specs( $product->get_id() );
	$fields = mpc_get_product_spec_fields();

	if ( empty( $specs ) ) {
		return;
	}

	echo '<section class="mpc-product-section">';
	echo '<h3>' . esc_html__( 'Specifications', 'my-peptide-core' ) . '</h3>';
	echo '<table class="mpc-spec-table">';
	foreach ( $specs as $key => $value ) {
		if ( '_mpc_coa_url' === $key ) {
			continue; // Surfaced as its own link, not a raw URL in a table.
		}
		if ( '_mpc_purity' === $key && is_numeric( $value ) ) {
			/* translators: %s: purity percentage. */
			$value = sprintf( __( '≥%s%% (HPLC)', 'my-peptide-core' ), $value );
		}

		printf(
			'<tr><th>%s</th><td>%s</td></tr>',
			esc_html( $fields[ $key ]['label'] ),
			esc_html( $value )
		);
	}
	echo '</table>';

	echo '<p class="mpc-spec-note">' . esc_html__(
		'Analytical values refer to the specific batch shipped and are documented in that batch’s Certificate of Analysis. Figures are provided for research characterisation only and are not a claim of suitability for any use in humans or animals.',
		'my-peptide-core'
	) . '</p>';
	echo '</section>';
}
