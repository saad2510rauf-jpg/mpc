<?php
/**
 * Homepage template.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();

$shop_url = ( class_exists( 'WooCommerce' ) && function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'shop' ) > 0 )
	? get_permalink( wc_get_page_id( 'shop' ) )
	: home_url( '/shop/' );

$mpc_show_countdown = mpc_countdown_is_active();

$mpc_hero_headline = mpc_get_theme_mod_or_option( 'mpc_hero_headline', __( '*Precision-Made* Research Peptides You Can *Trust*', 'my-peptide-core' ) );
$mpc_hero_subhead  = mpc_get_theme_mod_or_option( 'mpc_hero_subhead', __( 'Save more the more you order — savings are applied automatically in your cart.', 'my-peptide-core' ) );

$mpc_cat_icons = array(
	'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 8l-9-5-9 5 9 5 9-5z"/><path d="M3 8v8l9 5 9-5V8"/><path d="M12 13v8"/></svg>',
	'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2c2 3-1 4-1 7a4 4 0 108 0c0-1.5-1-2.5-1-2.5.5 3-1 4-2 4-2 0-2-2-2-3.5C14 5 12 2 12 2z"/><path d="M8 14a4 4 0 108 0c0-2-2-3-2-5-2 1-6 3-6 5z"/></svg>',
	'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 17l6-6 4 4 8-8"/><path d="M15 6h6v6"/></svg>',
	'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="9" width="18" height="6" rx="2"/><path d="M9 9V7a3 3 0 016 0v2M9 15v2a3 3 0 006 0v-2"/></svg>',
	'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 3h12M6 21h12M8 3c0 5 8 5 8 9s-8 4-8 9M16 3c0 5-8 5-8 9s8 4 8 9"/></svg>',
	'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 4a3 3 0 00-3 3 3 3 0 00-1 5.8A3.5 3.5 0 007 19a3 3 0 002-.8A3 3 0 0012 16V7a3 3 0 00-3-3z"/><path d="M15 4a3 3 0 013 3 3 3 0 011 5.8A3.5 3.5 0 0117 19a3 3 0 01-2-.8A3 3 0 0112 16"/></svg>',
	'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3l1.8 4.6L18 9.5l-4.2 1.9L12 16l-1.8-4.6L6 9.5l4.2-1.9L12 3z"/><path d="M19 15l.8 2 2 .8-2 .8-.8 2-.8-2-2-.8 2-.8.8-2z"/></svg>',
	'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 3c0 6 12 12 12 18M18 3c0 6-12 12-12 18"/><path d="M7 8h10M7 16h10"/></svg>',
	'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="2.3"/><ellipse cx="12" cy="12" rx="9" ry="4"/><ellipse cx="12" cy="12" rx="9" ry="4" transform="rotate(60 12 12)"/><ellipse cx="12" cy="12" rx="9" ry="4" transform="rotate(120 12 12)"/></svg>',
);
$mpc_viewall_icon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 5v14M5 12h14"/></svg>';
?>

<div class="mpc-hero-outer">
<section class="mpc-hero-banner" style="--mpc-hero-image:url('<?php echo esc_url( MPC_THEME_URI . '/assets/images/hero-lab.jpg' ); ?>');">
	<div class="container inner">
		<?php if ( $mpc_show_countdown ) : ?>
			<div class="mpc-flash-badge">
				<svg viewBox="0 0 24 24" fill="currentColor"><path d="M13 2L4.5 13.5H11l-1 8.5L19.5 10H13l1-8z"/></svg>
				<span><?php esc_html_e( 'Flash sale', 'my-peptide-core' ); ?></span>
			</div>
			<div class="mpc-countdown-row" id="mpc-countdown">
				<span class="mpc-cd-lead"><?php esc_html_e( 'Ends in', 'my-peptide-core' ); ?></span>
				<div class="mpc-cd-unit"><span class="mpc-cd-num" id="mpc-cd-d">00</span><span class="mpc-cd-label">D</span></div>
				<div class="mpc-cd-unit"><span class="mpc-cd-num" id="mpc-cd-h">00</span><span class="mpc-cd-label">H</span></div>
				<div class="mpc-cd-unit"><span class="mpc-cd-num" id="mpc-cd-m">00</span><span class="mpc-cd-label">M</span></div>
				<div class="mpc-cd-unit"><span class="mpc-cd-num" id="mpc-cd-s">00</span><span class="mpc-cd-label">S</span></div>
			</div>
		<?php endif; ?>

		<h1><?php echo mpc_highlight_text( $mpc_hero_headline ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside mpc_highlight_text() ?></h1>
		<p class="lede"><?php echo mpc_highlight_text( $mpc_hero_subhead ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside mpc_highlight_text() ?></p>

		<?php if ( class_exists( 'WooCommerce' ) ) : ?>
			<div class="mpc-tier-row">
				<?php foreach ( mpc_get_sale_tiers() as $tier ) : ?>
					<div class="mpc-tier">
						<span class="pct"><?php echo esc_html( $tier['percent'] ); ?>%</span>
						<b><?php esc_html_e( 'from', 'my-peptide-core' ); ?> <?php echo wp_kses_post( wc_price( $tier['threshold'] ) ); ?></b>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<a class="btn" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Shop Research Peptides', 'my-peptide-core' ); ?> <span>&#8594;</span></a>
	</div>
</section>
</div>

<div class="mpc-trust-marquee">
	<div class="mpc-marquee-track">
		<?php for ( $i = 0; $i < 2; $i++ ) : ?>
			<span><?php esc_html_e( 'For research use only', 'my-peptide-core' ); ?></span>
			<span><?php esc_html_e( 'Independent third-party purity testing', 'my-peptide-core' ); ?></span>
			<span><?php esc_html_e( 'Certificate of analysis with every batch', 'my-peptide-core' ); ?></span>
			<span><?php esc_html_e( 'Fast, tracked dispatch', 'my-peptide-core' ); ?></span>
		<?php endfor; ?>
	</div>
</div>

<main id="primary" class="site-main">

	<!-- CATEGORIES -->
	<section class="bg-alt" id="categories">
		<div class="container">
			<div class="section-head">
				<h2><?php esc_html_e( 'Shop by Category', 'my-peptide-core' ); ?></h2>
				<a class="view-all" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'View all →', 'my-peptide-core' ); ?></a>
			</div>
			<?php
			$categories = array();
			if ( class_exists( 'WooCommerce' ) ) {
				$categories = get_terms( array(
					'taxonomy'   => 'product_cat',
					'hide_empty' => false,
					'exclude'    => array( get_option( 'default_product_cat' ) ),
					'number'     => 9,
				) );
				if ( is_wp_error( $categories ) ) {
					$categories = array();
				}
			}
			?>
			<div class="mpc-cat-grid">
				<?php if ( ! empty( $categories ) ) : ?>
					<?php foreach ( $categories as $mpc_i => $cat ) : ?>
						<a class="mpc-cat-card" href="<?php echo esc_url( get_term_link( $cat ) ); ?>">
							<div class="ic"><?php echo $mpc_cat_icons[ $mpc_i % count( $mpc_cat_icons ) ]; // phpcs:ignore WordPress.Security.EscapeOutput -- static trusted SVG array ?></div>
							<div class="name"><?php echo esc_html( $cat->name ); ?></div>
						</a>
					<?php endforeach; ?>
				<?php else : ?>
					<?php
					$placeholders = array(
						__( 'Bundles', 'my-peptide-core' ),
						__( 'Metabolic Research', 'my-peptide-core' ),
						__( 'Growth Hormone', 'my-peptide-core' ),
						__( 'Tissue Regeneration', 'my-peptide-core' ),
						__( 'Longevity Research', 'my-peptide-core' ),
						__( 'Cognitive Research', 'my-peptide-core' ),
						__( 'Cosmetic Peptides', 'my-peptide-core' ),
						__( 'Bioregulators', 'my-peptide-core' ),
						__( 'Peptide Blends', 'my-peptide-core' ),
					);
					foreach ( $placeholders as $mpc_i => $label ) :
						?>
						<a class="mpc-cat-card" href="<?php echo esc_url( $shop_url ); ?>">
							<div class="ic"><?php echo $mpc_cat_icons[ $mpc_i % count( $mpc_cat_icons ) ]; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
							<div class="name"><?php echo esc_html( $label ); ?></div>
						</a>
					<?php endforeach; ?>
				<?php endif; ?>
				<a class="mpc-cat-card" href="<?php echo esc_url( $shop_url ); ?>">
					<div class="ic"><?php echo $mpc_viewall_icon; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
					<div class="name"><?php esc_html_e( 'View all', 'my-peptide-core' ); ?></div>
				</a>
			</div>
		</div>
	</section>

	<!-- FEATURED PRODUCTS -->
	<section id="shop">
		<div class="container">
			<div class="section-head">
				<h2><?php esc_html_e( 'Featured Research Peptides', 'my-peptide-core' ); ?></h2>
				<a class="view-all" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'View all →', 'my-peptide-core' ); ?></a>
			</div>
			<?php
			$mpc_products = array();
			if ( class_exists( 'WooCommerce' ) ) {
				$mpc_products = wc_get_products( array(
					'status'  => 'publish',
					'limit'   => 8,
					'featured' => true,
				) );
				if ( empty( $mpc_products ) ) {
					$mpc_products = wc_get_products( array(
						'status'  => 'publish',
						'limit'   => 8,
						'orderby' => 'date',
						'order'   => 'DESC',
					) );
				}
			}
			?>
			<?php if ( ! empty( $mpc_products ) ) : ?>
				<div class="mpc-prod-grid">
					<?php foreach ( $mpc_products as $product ) : ?>
						<div class="mpc-prod-card">
							<a class="mpc-prod-img" href="<?php echo esc_url( $product->get_permalink() ); ?>">
								<?php echo wp_kses_post( $product->get_image( 'medium' ) ); ?>
							</a>
							<div class="mpc-prod-body">
								<span class="mpc-ruo-tag"><?php esc_html_e( 'RUO', 'my-peptide-core' ); ?></span>
								<div class="mpc-prod-name"><a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></div>
								<div class="mpc-prod-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<p class="mpc-bundle-empty">
					<?php
					echo class_exists( 'WooCommerce' )
						? esc_html__( 'Add products in WooCommerce and mark them "Featured" to have them appear here automatically.', 'my-peptide-core' )
						: esc_html__( 'Install and activate WooCommerce to display products here.', 'my-peptide-core' );
					?>
				</p>
			<?php endif; ?>
		</div>
	</section>

	<!-- BUNDLE DEALS -->
	<section class="mpc-bundle-section" id="bundles">
		<div class="container">
			<div class="section-head">
				<h2><?php esc_html_e( 'Bundle Deals', 'my-peptide-core' ); ?></h2>
				<a class="view-all" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'View all →', 'my-peptide-core' ); ?></a>
			</div>
			<?php
			$mpc_bundles = array();
			if ( class_exists( 'WooCommerce' ) ) {
				$mpc_bundles = wc_get_products( array(
					'status'   => 'publish',
					'limit'    => 4,
					'category' => array( 'bundles' ),
				) );
			}
			?>
			<?php if ( ! empty( $mpc_bundles ) ) : ?>
				<div class="mpc-bundle-grid">
					<?php foreach ( $mpc_bundles as $bundle ) : ?>
						<div class="mpc-bundle-card">
							<?php if ( $bundle->is_on_sale() && $bundle->get_regular_price() > 0 ) : ?>
								<?php $mpc_save_pct = round( ( 1 - ( floatval( $bundle->get_sale_price() ) / floatval( $bundle->get_regular_price() ) ) ) * 100 ); ?>
								<span class="mpc-save-badge"><?php echo esc_html( sprintf( __( 'Save %d%%', 'my-peptide-core' ), $mpc_save_pct ) ); ?></span>
							<?php endif; ?>
							<a class="mpc-prod-img" style="height:90px;" href="<?php echo esc_url( $bundle->get_permalink() ); ?>">
								<?php echo wp_kses_post( $bundle->get_image( 'thumbnail' ) ); ?>
							</a>
							<div class="mpc-bundle-name"><a href="<?php echo esc_url( $bundle->get_permalink() ); ?>"><?php echo esc_html( $bundle->get_name() ); ?></a></div>
							<div class="mpc-bundle-price"><?php echo wp_kses_post( $bundle->get_price_html() ); ?></div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<p class="mpc-bundle-empty">
					<?php esc_html_e( 'Bundle deals coming soon. Create a "Bundles" product category in WooCommerce and add multi-product listings to feature them here automatically.', 'my-peptide-core' ); ?>
				</p>
			<?php endif; ?>
		</div>
	</section>

	<!-- WHY US -->
	<section>
		<div class="container">
			<div class="section-head">
				<h2><?php esc_html_e( 'Why Choose My Peptide Core', 'my-peptide-core' ); ?></h2>
			</div>
			<div class="mpc-cards">
				<div class="mpc-card">
					<div class="ico">A</div>
					<h3><?php esc_html_e( 'Verified Purity', 'my-peptide-core' ); ?></h3>
					<p><?php esc_html_e( 'Every batch is tested by an independent third-party lab, with the certificate of analysis published alongside the product.', 'my-peptide-core' ); ?></p>
				</div>
				<div class="mpc-card">
					<div class="ico">B</div>
					<h3><?php esc_html_e( 'Careful Handling', 'my-peptide-core' ); ?></h3>
					<p><?php esc_html_e( 'Temperature-sensitive compounds ship in insulated, cold-chain packaging to protect stability in transit.', 'my-peptide-core' ); ?></p>
				</div>
				<div class="mpc-card">
					<div class="ico">C</div>
					<h3><?php esc_html_e( 'Research Guidance', 'my-peptide-core' ); ?></h3>
					<p><?php esc_html_e( 'Reconstitution, storage, and stability notes are provided on every product listing.', 'my-peptide-core' ); ?></p>
				</div>
				<div class="mpc-card">
					<div class="ico">D</div>
					<h3><?php esc_html_e( 'Responsible Access', 'my-peptide-core' ); ?></h3>
					<p><?php esc_html_e( 'We sell strictly for legitimate research use, with clear labeling and consent at every checkout.', 'my-peptide-core' ); ?></p>
				</div>
			</div>
		</div>
	</section>

	<!-- NEWSLETTER / CTA -->
	<section class="mpc-newsletter">
		<div class="container">
			<h2><?php esc_html_e( 'Get Restock & Deal Alerts', 'my-peptide-core' ); ?></h2>
			<p><?php esc_html_e( 'Join the list for new batch releases, certificates of analysis, and research notes.', 'my-peptide-core' ); ?></p>
			<form action="#" method="post">
				<input type="email" name="mpc_newsletter_email" placeholder="<?php esc_attr_e( 'you@lab.com', 'my-peptide-core' ); ?>" required>
				<button type="submit" class="btn"><?php esc_html_e( 'Subscribe', 'my-peptide-core' ); ?></button>
			</form>
			<p style="font-size:.75rem;margin-top:14px;color:#8b96a2;"><?php esc_html_e( 'Connect this form to your email provider (e.g. Mailchimp, Klaviyo) — it is a placeholder form and does not submit anywhere yet.', 'my-peptide-core' ); ?></p>
		</div>
	</section>

</main>

<?php get_footer(); ?>
