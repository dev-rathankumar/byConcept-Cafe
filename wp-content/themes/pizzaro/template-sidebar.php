<?php
/**
 * The template for displaying sidebar pages.
 *
 * Template Name: Sidebar
 *
 * @package pizzaro
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php while ( have_posts() ) : the_post();

				do_action( 'pizzaro_page_before' );

				get_template_part( 'content', 'page' );

				/**
				 * Functions hooked in to pizzaro_page_after action
				 *
				 * @hooked pizzaro_display_comments - 10
				 */
				do_action( 'pizzaro_page_after' );

			endwhile; // End of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
do_action( 'pizzaro_sidebar' );
get_footer();
