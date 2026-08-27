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
?>

<main id="primary" class="site-main">

	<!-- HERO -->
	<section class="mpc-hero">
		<div class="container">
			<div>
				<span class="mpc-hero-eyebrow">&#9679; <?php esc_html_e( 'Third-Party Tested · Research Use Only', 'my-peptide-core' ); ?></span>
				<h1><?php esc_html_e( 'Precision-Made Research Peptides You Can Trust', 'my-peptide-core' ); ?></h1>
				<p class="lede"><?php esc_html_e( 'My Peptide Core supplies high-purity peptides for laboratory research, backed by batch-specific certificates of analysis and fast, discreet shipping.', 'my-peptide-core' ); ?></p>
				<div class="cta-row">
					<a class="btn" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Shop Research Peptides', 'my-peptide-core' ); ?></a>
					<a class="btn btn-outline on-dark" href="<?php echo esc_url( home_url( '/research-use-disclaimer/' ) ); ?>"><?php esc_html_e( 'Read Our RUO Policy', 'my-peptide-core' ); ?></a>
				</div>
			</div>
			<div class="mpc-hero-panel">
				<ul>
					<li><span class="ico">&#10003;</span><?php esc_html_e( 'Every batch shipped with a certificate of analysis (COA)', 'my-peptide-core' ); ?></li>
					<li><span class="ico">&#10003;</span><?php esc_html_e( 'Independent third-party purity testing', 'my-peptide-core' ); ?></li>
					<li><span class="ico">&#10003;</span><?php esc_html_e( 'Cold-chain packaging on temperature-sensitive items', 'my-peptide-core' ); ?></li>
					<li><span class="ico">&#10003;</span><?php esc_html_e( 'For qualified researchers and institutions only', 'my-peptide-core' ); ?></li>
				</ul>
			</div>
		</div>
	</section>

	<!-- TRUST BAR -->
	<section class="mpc-trust">
		<div class="container grid">
			<div class="item"><span class="num">99%+</span><span class="label"><?php esc_html_e( 'Avg. reported purity', 'my-peptide-core' ); ?></span></div>
			<div class="item"><span class="num">100%</span><span class="label"><?php esc_html_e( 'Batches lab-tested', 'my-peptide-core' ); ?></span></div>
			<div class="item"><span class="num">24–48h</span><span class="label"><?php esc_html_e( 'Order processing time', 'my-peptide-core' ); ?></span></div>
			<div class="item"><span class="num">RUO</span><span class="label"><?php esc_html_e( 'Research use only, always', 'my-peptide-core' ); ?></span></div>
		</div>
	</section>

	<!-- FEATURED CATEGORIES -->
	<section class="bg-alt">
		<div class="container">
			<div class="section-head">
				<span class="kicker"><?php esc_html_e( 'Catalog', 'my-peptide-core' ); ?></span>
				<h2><?php esc_html_e( 'Shop by Category', 'my-peptide-core' ); ?></h2>
				<p><?php esc_html_e( 'Browse our core research categories. Add your own product categories in WooCommerce to expand this section automatically.', 'my-peptide-core' ); ?></p>
			</div>
			<?php
			if ( class_exists( 'WooCommerce' ) ) {
				$categories = get_terms( array(
					'taxonomy'   => 'product_cat',
					'hide_empty' => false,
					'exclude'    => array( get_option( 'default_product_cat' ) ),
					'number'     => 4,
				) );
			} else {
				$categories = array();
			}
			?>
			<div class="mpc-cards">
				<?php if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) : ?>
					<?php foreach ( $categories as $cat ) : ?>
						<div class="mpc-card">
							<div class="ico">&#9673;</div>
							<h3><?php echo esc_html( $cat->name ); ?></h3>
							<p><?php echo esc_html( wp_trim_words( $cat->description ? $cat->description : __( 'Explore this category of research peptides.', 'my-peptide-core' ), 14 ) ); ?></p>
							<a class="card-link" href="<?php echo esc_url( get_term_link( $cat ) ); ?>"><?php esc_html_e( 'Browse →', 'my-peptide-core' ); ?></a>
						</div>
					<?php endforeach; ?>
				<?php else : ?>
					<?php
					$placeholders = array(
						array( 'title' => 'Growth & Repair Peptides', 'desc' => 'Research compounds studied for tissue repair and recovery pathways.' ),
						array( 'title' => 'Metabolic Research Peptides', 'desc' => 'Compounds used in metabolic and weight-regulation research models.' ),
						array( 'title' => 'Cognitive Research Peptides', 'desc' => 'Peptides studied in neurological and cognitive research settings.' ),
						array( 'title' => 'Blends & Accessories', 'desc' => 'Combination research blends, bacteriostatic water, and lab supplies.' ),
					);
					foreach ( $placeholders as $p ) :
						?>
						<div class="mpc-card">
							<div class="ico">&#9673;</div>
							<h3><?php echo esc_html( $p['title'] ); ?></h3>
							<p><?php echo esc_html( $p['desc'] ); ?></p>
							<a class="card-link" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'Browse →', 'my-peptide-core' ); ?></a>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<!-- FEATURED PRODUCTS -->
	<section>
		<div class="container">
			<div class="section-head">
				<span class="kicker"><?php esc_html_e( 'Featured', 'my-peptide-core' ); ?></span>
				<h2><?php esc_html_e( 'Popular Research Peptides', 'my-peptide-core' ); ?></h2>
				<p><?php esc_html_e( 'Add products in WooCommerce and mark them "Featured" to have them appear here automatically.', 'my-peptide-core' ); ?></p>
			</div>
			<?php
			if ( class_exists( 'WooCommerce' ) ) {
				echo do_shortcode( '[products limit="8" columns="4" orderby="date" order="DESC"]' );
			} else {
				echo '<p style="text-align:center;color:var(--mpc-muted);">' . esc_html__( 'Install and activate WooCommerce to display products here.', 'my-peptide-core' ) . '</p>';
			}
			?>
		</div>
	</section>

	<!-- WHY US -->
	<section class="bg-alt">
		<div class="container">
			<div class="section-head">
				<span class="kicker"><?php esc_html_e( 'Why My Peptide Core', 'my-peptide-core' ); ?></span>
				<h2><?php esc_html_e( 'Built for Serious Research', 'my-peptide-core' ); ?></h2>
			</div>
			<div class="mpc-cards cols-3">
				<div class="mpc-card">
					<div class="ico">1</div>
					<h3><?php esc_html_e( 'Verified Purity', 'my-peptide-core' ); ?></h3>
					<p><?php esc_html_e( 'Every batch is tested by an independent third-party lab, with the certificate of analysis published alongside the product.', 'my-peptide-core' ); ?></p>
				</div>
				<div class="mpc-card">
					<div class="ico">2</div>
					<h3><?php esc_html_e( 'Careful Handling', 'my-peptide-core' ); ?></h3>
					<p><?php esc_html_e( 'Temperature-sensitive compounds ship in insulated, cold-chain packaging to protect stability in transit.', 'my-peptide-core' ); ?></p>
				</div>
				<div class="mpc-card">
					<div class="ico">3</div>
					<h3><?php esc_html_e( 'Responsible Access', 'my-peptide-core' ); ?></h3>
					<p><?php esc_html_e( 'We sell strictly for legitimate research use, with clear labeling and consent at every checkout.', 'my-peptide-core' ); ?></p>
				</div>
			</div>
		</div>
	</section>

	<!-- NEWSLETTER -->
	<section class="mpc-newsletter">
		<div class="container">
			<h2><?php esc_html_e( 'Get Restock & COA Alerts', 'my-peptide-core' ); ?></h2>
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
