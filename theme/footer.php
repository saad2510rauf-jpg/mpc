<?php
/**
 * The footer for our theme.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
	<footer class="site-footer">
		<div class="container">
			<div class="footer-grid">
				<div class="footer-brand">
					<p class="site-title" style="color:#fff;">My Peptide<span class="accent">Core</span></p>
					<p><?php esc_html_e( 'Research-grade peptides, third-party tested and supplied strictly for laboratory research use.', 'my-peptide-core' ); ?></p>
				</div>
				<div>
					<h4><?php esc_html_e( 'Shop', 'my-peptide-core' ); ?></h4>
					<ul>
						<?php if ( class_exists( 'WooCommerce' ) && function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'shop' ) > 0 ) : ?>
							<li><a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>"><?php esc_html_e( 'All Products', 'my-peptide-core' ); ?></a></li>
						<?php endif; ?>
						<li><a href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>"><?php esc_html_e( 'About Us', 'my-peptide-core' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'my-peptide-core' ); ?></a></li>
					</ul>
				</div>
				<div>
					<h4><?php esc_html_e( 'Policies', 'my-peptide-core' ); ?></h4>
					<ul>
						<li><a href="<?php echo esc_url( home_url( '/research-use-disclaimer/' ) ); ?>"><?php esc_html_e( 'Research Use Disclaimer', 'my-peptide-core' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/terms-and-conditions/' ) ); ?>"><?php esc_html_e( 'Terms & Conditions', 'my-peptide-core' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/shipping-and-returns/' ) ); ?>"><?php esc_html_e( 'Shipping & Returns', 'my-peptide-core' ); ?></a></li>
						<li><a href="<?php echo esc_url( get_privacy_policy_url() ); ?>"><?php esc_html_e( 'Privacy Policy', 'my-peptide-core' ); ?></a></li>
					</ul>
				</div>
				<div>
					<h4><?php esc_html_e( 'Newsletter', 'my-peptide-core' ); ?></h4>
					<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
						<?php dynamic_sidebar( 'footer-1' ); ?>
					<?php else : ?>
						<p><?php esc_html_e( 'Sign up for restock alerts and new COA releases.', 'my-peptide-core' ); ?></p>
					<?php endif; ?>
				</div>
			</div>
			<div class="footer-bottom">
				<span>&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> My Peptide Core. <?php esc_html_e( 'All rights reserved.', 'my-peptide-core' ); ?></span>
				<span><?php esc_html_e( 'For research use only. Not for human consumption.', 'my-peptide-core' ); ?></span>
			</div>
		</div>
	</footer>

	<div class="mpc-footer-disclaimer">
		<div class="container">
			<?php esc_html_e( 'My Peptide Core products are sold exclusively as research chemicals for in-vitro laboratory use by qualified professionals. They are not intended for human or animal consumption, use, diagnosis, treatment, or cure of any disease.', 'my-peptide-core' ); ?>
		</div>
	</div>

<?php wp_footer(); ?>
</body>
</html>
