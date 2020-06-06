<?php
/**
 * Template Name: Blank Page Template
 *
 * @package pizzaro
 */
remove_action( 'pizzaro_content_top', 'pizzaro_breadcrumb', 10 );

get_header( 'blank' ); ?>

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
get_footer( 'blank' );