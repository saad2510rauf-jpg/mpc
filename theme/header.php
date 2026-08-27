<?php
/**
 * The header for our theme.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="mpc-announcement">
	<strong><?php esc_html_e( 'Research Use Only', 'my-peptide-core' ); ?></strong>
	&mdash; <?php esc_html_e( 'Not for human or veterinary consumption. Sold exclusively for laboratory research.', 'my-peptide-core' ); ?>
</div>

<header class="site-header">
	<div class="container">
		<div class="site-branding">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<?php
				if ( has_custom_logo() ) {
					the_custom_logo();
				} else {
					echo '<p class="site-title">' . esc_html( get_bloginfo( 'name', 'display' ) !== '' ? get_bloginfo( 'name', 'display' ) : 'My Peptide<span class="accent">Core</span>' ) . '</p>';
				}
				?>
			</a>
		</div>

		<nav class="main-navigation" aria-label="<?php esc_attr_e( 'Primary menu', 'my-peptide-core' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'fallback_cb'    => 'mpc_fallback_menu',
			) );
			?>
		</nav>

		<div class="header-actions">
			<?php if ( class_exists( 'WooCommerce' ) ) : ?>
				<a class="header-cart" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="<?php esc_attr_e( 'Cart', 'my-peptide-core' ); ?>">
					<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h2l2.4 12.4a2 2 0 0 0 2 1.6h7.2a2 2 0 0 0 2-1.6L20 8H6"/><circle cx="9" cy="20" r="1.4"/><circle cx="17" cy="20" r="1.4"/></svg>
					<?php echo mpc_cart_count_markup(); ?>
				</a>
			<?php endif; ?>
			<button class="menu-toggle" aria-controls="mobile-menu" aria-expanded="false">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
			</button>
		</div>
	</div>

	<nav id="mobile-menu" class="mobile-navigation" aria-label="<?php esc_attr_e( 'Mobile menu', 'my-peptide-core' ); ?>" hidden>
		<div class="container">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'fallback_cb'    => 'mpc_fallback_menu',
			) );
			?>
		</div>
	</nav>
</header>
