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

<header class="site-header">
	<div class="header-pill">
		<div class="container header-pill-inner">
			<div class="site-branding">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<?php
					if ( has_custom_logo() ) {
						the_custom_logo();
					} else {
						echo '<img class="site-logo-fallback" src="' . esc_url( MPC_THEME_URI . '/assets/images/logo.png' ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
						echo '<p class="site-title">' . esc_html( get_bloginfo( 'name', 'display' ) !== '' ? get_bloginfo( 'name', 'display' ) : 'My Peptide Core' ) . '</p>';
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
				<a class="header-icon-link" href="<?php echo esc_url( function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'myaccount' ) > 0 ? get_permalink( wc_get_page_id( 'myaccount' ) ) : wp_login_url() ); ?>" aria-label="<?php esc_attr_e( 'My Account', 'my-peptide-core' ); ?>">
					<svg class="header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 4-6 8-6s8 2 8 6"/></svg>
				</a>
				<?php if ( class_exists( 'WooCommerce' ) ) : ?>
					<a class="header-icon-link header-cart" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="<?php esc_attr_e( 'Cart', 'my-peptide-core' ); ?>">
						<svg class="header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="9" cy="21" r="1.4"/><circle cx="18" cy="21" r="1.4"/><path d="M2.5 3h2.5l2.5 12.5h10.5l2-9H6.3"/></svg>
						<?php echo mpc_cart_count_markup(); ?>
					</a>
				<?php endif; ?>
				<button type="button" class="header-icon-link search-toggle" aria-controls="mpc-search-form" aria-expanded="false" aria-label="<?php esc_attr_e( 'Search', 'my-peptide-core' ); ?>">
					<svg class="header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="10.5" cy="10.5" r="6.5"/><path d="M20 20l-4.7-4.7"/></svg>
				</button>

				<?php
				/*
				 * The currency and language pickers only render when a plugin
				 * can actually honour the choice. A picker that restyles prices
				 * client-side while checkout still charges the store currency
				 * shows shoppers a number they will not be billed — so it stays
				 * hidden until real multi-currency / translation support exists.
				 */
				$mpc_currencies = mpc_get_available_currencies();
				$mpc_languages  = mpc_get_available_languages();
				?>
				<?php if ( count( $mpc_currencies ) > 1 || count( $mpc_languages ) > 1 ) : ?>
					<div class="nav-divider"></div>
					<div class="locale-picker">
						<?php if ( count( $mpc_currencies ) > 1 ) : ?>
							<select class="locale-select" id="mpc-currency-select" aria-label="<?php esc_attr_e( 'Currency', 'my-peptide-core' ); ?>">
								<?php foreach ( $mpc_currencies as $mpc_code => $mpc_label ) : ?>
									<option value="<?php echo esc_attr( $mpc_code ); ?>" <?php selected( $mpc_code, get_woocommerce_currency() ); ?>><?php echo esc_html( $mpc_label ); ?></option>
								<?php endforeach; ?>
							</select>
						<?php endif; ?>
						<?php if ( count( $mpc_languages ) > 1 ) : ?>
							<select class="locale-select" id="mpc-lang-select" aria-label="<?php esc_attr_e( 'Language', 'my-peptide-core' ); ?>">
								<?php foreach ( $mpc_languages as $mpc_lang ) : ?>
									<option value="<?php echo esc_url( $mpc_lang['url'] ); ?>" <?php selected( ! empty( $mpc_lang['current'] ) ); ?>><?php echo esc_html( $mpc_lang['name'] ); ?></option>
								<?php endforeach; ?>
							</select>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<button class="menu-toggle" aria-controls="mobile-menu" aria-expanded="false">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
				</button>
			</div>
		</div>

		<form role="search" method="get" class="mpc-search-form" id="mpc-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" hidden>
			<div class="container">
				<label class="screen-reader-text" for="mpc-search-input"><?php esc_html_e( 'Search for:', 'my-peptide-core' ); ?></label>
				<input type="search" id="mpc-search-input" name="s" placeholder="<?php esc_attr_e( 'Search products…', 'my-peptide-core' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
				<button type="submit" class="btn"><?php esc_html_e( 'Search', 'my-peptide-core' ); ?></button>
			</div>
		</form>
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

<?php
$mpc_show_announcement = apply_filters( 'mpc_show_top_announcement', false );
if ( $mpc_show_announcement ) :
	?>
	<div class="mpc-announcement">
		<strong><?php esc_html_e( 'Research Use Only', 'my-peptide-core' ); ?></strong>
		&mdash; <?php esc_html_e( 'Not for human or veterinary consumption. Sold exclusively for laboratory research.', 'my-peptide-core' ); ?>
	</div>
<?php endif; ?>
