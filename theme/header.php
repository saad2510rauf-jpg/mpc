<?php
/**
 * The header for our theme.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mpc_nav      = mpc_get_header_nav();
$mpc_lang     = mpc_current_language();
$mpc_languages = mpc_get_available_languages();
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

<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to main content', 'my-peptide-core' ); ?></a>

<header class="site-header">
	<div class="container">
		<div class="header-pill">
			<div class="site-branding">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<?php
					if ( has_custom_logo() ) {
						the_custom_logo();
					} else {
						echo '<img class="site-logo-fallback" src="' . esc_url( MPC_THEME_URI . '/assets/images/logo.png' ) . '" alt="">';
						echo '<span class="site-title">' . esc_html( get_bloginfo( 'name', 'display' ) ) . '</span>';
					}
					?>
				</a>
			</div>

			<nav class="main-navigation" aria-label="<?php esc_attr_e( 'Primary menu', 'my-peptide-core' ); ?>">
				<ul>
					<?php foreach ( $mpc_nav as $mpc_i => $mpc_item ) : ?>
						<?php if ( empty( $mpc_item['dropdown'] ) ) : ?>
							<li>
								<a href="<?php echo esc_url( $mpc_item['url'] ); ?>"><?php echo esc_html( $mpc_item['label'] ); ?></a>
							</li>
						<?php else : ?>
							<?php $mpc_panel_id = 'mpc-menu-' . $mpc_i; ?>
							<li class="has-dropdown">
								<button type="button" class="dropdown-toggle" aria-expanded="false" aria-controls="<?php echo esc_attr( $mpc_panel_id ); ?>">
									<?php echo esc_html( $mpc_item['label'] ); ?>
									<svg class="caret" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
								</button>
								<div class="mpc-dropdown" id="<?php echo esc_attr( $mpc_panel_id ); ?>">
									<ul>
										<?php foreach ( $mpc_item['dropdown'] as $mpc_sub ) : ?>
											<li>
												<a href="<?php echo esc_url( $mpc_sub['url'] ); ?>">
													<span class="ic" aria-hidden="true"><?php echo $mpc_sub['icon']; // phpcs:ignore WordPress.Security.EscapeOutput -- trusted inline SVG ?></span>
													<span><?php echo esc_html( $mpc_sub['label'] ); ?></span>
												</a>
											</li>
										<?php endforeach; ?>
									</ul>
									<?php if ( ! empty( $mpc_item['footer'] ) ) : ?>
										<a class="mpc-dropdown-footer" href="<?php echo esc_url( $mpc_item['footer']['url'] ); ?>">
											<?php echo esc_html( $mpc_item['footer']['label'] ); ?>
											<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
										</a>
									<?php endif; ?>
								</div>
							</li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</nav>

			<div class="header-actions">
				<a class="header-icon-link" href="<?php echo esc_url( function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'myaccount' ) > 0 ? get_permalink( wc_get_page_id( 'myaccount' ) ) : wp_login_url() ); ?>" aria-label="<?php esc_attr_e( 'My account', 'my-peptide-core' ); ?>">
					<svg class="header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 4-6 8-6s8 2 8 6"/></svg>
				</a>
				<?php if ( class_exists( 'WooCommerce' ) ) : ?>
					<a class="header-icon-link header-cart" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="<?php esc_attr_e( 'Cart', 'my-peptide-core' ); ?>">
						<svg class="header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="9" cy="21" r="1.4"/><circle cx="18" cy="21" r="1.4"/><path d="M2.5 3h2.5l2.5 12.5h10.5l2-9H6.3"/></svg>
						<?php echo mpc_cart_count_markup(); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped within ?>
					</a>
				<?php endif; ?>
				<button type="button" class="header-icon-link search-toggle" aria-controls="mpc-search-form" aria-expanded="false" aria-label="<?php esc_attr_e( 'Search', 'my-peptide-core' ); ?>">
					<svg class="header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="10.5" cy="10.5" r="6.5"/><path d="M20 20l-4.7-4.7"/></svg>
				</button>

				<span class="nav-divider" aria-hidden="true"></span>

				<div class="mpc-lang">
					<?php if ( count( $mpc_languages ) > 1 ) : ?>
						<button type="button" class="mpc-lang-toggle dropdown-toggle" aria-expanded="false" aria-controls="mpc-lang-menu">
							<span class="region"><?php echo esc_html( $mpc_lang['region'] ); ?></span>
							<span class="label"><?php echo esc_html( $mpc_lang['label'] ); ?></span>
							<svg class="caret" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
						</button>
						<div class="mpc-dropdown mpc-lang-menu" id="mpc-lang-menu">
							<ul>
								<?php foreach ( $mpc_languages as $mpc_l ) : ?>
									<li>
										<a href="<?php echo esc_url( $mpc_l['url'] ); ?>"<?php echo ! empty( $mpc_l['current'] ) ? ' aria-current="true"' : ''; ?>>
											<span class="region"><?php echo esc_html( mpc_locale_region( isset( $mpc_l['locale'] ) ? $mpc_l['locale'] : $mpc_l['name'] ) ); ?></span>
											<span><?php echo esc_html( $mpc_l['name'] ); ?></span>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php else : ?>
						<span class="mpc-lang-static">
							<span class="region"><?php echo esc_html( $mpc_lang['region'] ); ?></span>
							<span class="label"><?php echo esc_html( $mpc_lang['label'] ); ?></span>
						</span>
					<?php endif; ?>
				</div>

				<button class="menu-toggle" aria-controls="mobile-menu" aria-expanded="false" aria-label="<?php esc_attr_e( 'Menu', 'my-peptide-core' ); ?>">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
				</button>
			</div>
		</div>

		<form role="search" method="get" class="mpc-search-form" id="mpc-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" hidden>
			<label class="screen-reader-text" for="mpc-search-input"><?php esc_html_e( 'Search for:', 'my-peptide-core' ); ?></label>
			<input type="search" id="mpc-search-input" name="s" placeholder="<?php esc_attr_e( 'Search products…', 'my-peptide-core' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
			<button type="submit" class="btn"><?php esc_html_e( 'Search', 'my-peptide-core' ); ?></button>
		</form>

		<nav id="mobile-menu" class="mobile-navigation" aria-label="<?php esc_attr_e( 'Mobile menu', 'my-peptide-core' ); ?>" hidden>
			<ul>
				<?php foreach ( $mpc_nav as $mpc_item ) : ?>
					<li>
						<?php if ( ! empty( $mpc_item['url'] ) ) : ?>
							<a href="<?php echo esc_url( $mpc_item['url'] ); ?>"><?php echo esc_html( $mpc_item['label'] ); ?></a>
						<?php else : ?>
							<span class="mobile-group"><?php echo esc_html( $mpc_item['label'] ); ?></span>
						<?php endif; ?>
						<?php if ( ! empty( $mpc_item['dropdown'] ) ) : ?>
							<ul class="mobile-sub">
								<?php foreach ( $mpc_item['dropdown'] as $mpc_sub ) : ?>
									<li><a href="<?php echo esc_url( $mpc_sub['url'] ); ?>"><?php echo esc_html( $mpc_sub['label'] ); ?></a></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>
	</div>
</header>
