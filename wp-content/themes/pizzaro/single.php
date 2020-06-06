<?php
/**
 * The template for displaying all single posts.
 *
 * @package pizzaro
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php while ( have_posts() ) : the_post();

			do_action( 'pizzaro_single_post_before' );

			get_template_part( 'content', 'single' );

			do_action( 'pizzaro_single_post_after' );

		endwhile; // End of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
if ( apply_filters( 'pizzaro_show_sidebar', true ) ) {
	do_action( 'pizzaro_sidebar' );
}
get_footer();
