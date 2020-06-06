<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package pizzaro
 */

global $post;
$page_meta_values = get_post_meta( $post->ID, '_pizzaro_page_metabox', true );

$header_style = '';
if ( isset( $page_meta_values['site_header_style'] ) && ! empty( $page_meta_values['site_header_style'] ) ) {
	$header_style = $page_meta_values['site_header_style'];
}

$footer_style = '';
if ( isset( $page_meta_values['site_footer_style'] ) && ! empty( $page_meta_values['site_footer_style'] ) ) {
	$footer_style = $page_meta_values['site_footer_style'];
}

get_header( $header_style ); ?>

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
get_footer( $footer_style );
