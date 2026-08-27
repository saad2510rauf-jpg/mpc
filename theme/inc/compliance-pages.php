<?php
/**
 * Auto-provision the core marketing/legal pages this store needs on first
 * activation, so the site launches with real (if placeholder) content
 * instead of blank pages. Safe to run multiple times — skips pages that
 * already exist by title.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function mpc_get_starter_pages() {
	return array(
		'Research Use Disclaimer' => array(
			'slug'    => 'research-use-disclaimer',
			'content' => "<p>All products sold by My Peptide Core are intended strictly for laboratory and research use only (\"RUO\"). They are not drugs, dietary supplements, cosmetics, or food, and are not approved by any regulatory body for human or animal consumption, use, or administration in any form.</p>" .
				"<p>By purchasing from this site, you represent that:</p>" .
				"<ul>" .
				"<li>You are at least 18 years of age and are purchasing on behalf of a qualified laboratory, research institution, or company engaged in legitimate scientific research;</li>" .
				"<li>You will not use, resell, or represent these products as suitable for human or veterinary consumption, injection, diagnostic use, or therapeutic treatment of any kind;</li>" .
				"<li>You are solely responsible for complying with all applicable local, state, and federal laws and regulations governing the handling, storage, and use of research chemicals in your jurisdiction; and</li>" .
				"<li>You understand these products have not been evaluated by any regulatory authority for safety or efficacy in humans or animals.</li>" .
				"</ul>" .
				"<p>My Peptide Core reserves the right to refuse or cancel any order it believes, in its sole discretion, is intended for improper use. Please replace this placeholder text with disclaimer language reviewed by qualified legal counsel in your jurisdiction before launching the store.</p>",
		),
		'Terms & Conditions' => array(
			'slug'    => 'terms-and-conditions',
			'content' => "<p>These Terms &amp; Conditions govern your use of this website and your purchase of any products listed for sale. By placing an order, you agree to be bound by these terms, including the <a href=\"/research-use-disclaimer/\">Research Use Disclaimer</a>.</p>" .
				"<p>This is placeholder content. Replace it with terms of sale, warranty disclaimers, limitation of liability, governing law, and dispute resolution language reviewed by qualified legal counsel before launch.</p>",
		),
		'Shipping & Returns' => array(
			'slug'    => 'shipping-and-returns',
			'content' => "<p>Orders are typically processed within 1–2 business days. Tracking details are emailed once your order ships.</p>" .
				"<p>Because our products are intended for laboratory use, unopened items in original packaging may be returned within 14 days of delivery for a refund or exchange. Contact us before sending anything back.</p>" .
				"<p>This is placeholder content — update shipping carriers, timelines, and return policy details to match your actual fulfillment process.</p>",
		),
		'About Us' => array(
			'slug'    => 'about-us',
			'content' => "<p>My Peptide Core supplies rigorously tested research peptides to laboratories, universities, and research organizations. Every batch is manufactured to a documented purity standard and accompanied by a certificate of analysis (COA).</p>" .
				"<p>This is placeholder copy — replace it with your real company story, sourcing standards, and lab credentials.</p>",
		),
		'Contact' => array(
			'slug'    => 'contact',
			'content' => "<p>Have a question about an order, a certificate of analysis, or bulk research pricing? Reach our team at <a href=\"mailto:support@example.com\">support@example.com</a>.</p>" .
				"<p>Replace this placeholder with your real support email, phone number, or contact form.</p>",
		),
	);
}

/**
 * Create the starter pages (and a Home page using the front-page template)
 * once, on theme activation.
 */
function mpc_create_starter_content() {
	foreach ( mpc_get_starter_pages() as $title => $data ) {
		if ( get_page_by_path( $data['slug'] ) ) {
			continue;
		}
		wp_insert_post( array(
			'post_title'   => $title,
			'post_name'    => $data['slug'],
			'post_content' => $data['content'],
			'post_status'  => 'publish',
			'post_type'    => 'page',
		) );
	}

	if ( ! get_page_by_path( 'home' ) ) {
		$home_id = wp_insert_post( array(
			'post_title'   => 'Home',
			'post_name'    => 'home',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '',
		) );
		if ( $home_id && ! is_wp_error( $home_id ) ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $home_id );
		}
	}
}
add_action( 'after_switch_theme', 'mpc_create_starter_content' );
