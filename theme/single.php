<?php
/**
 * The template for displaying single posts.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<main id="primary" class="site-main">
	<div class="container page-content-wrap">
		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content' );
		endwhile;
		?>
	</div>
</main>
<?php get_footer(); ?>
