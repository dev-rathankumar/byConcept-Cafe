<?php
/**
 * The template for displaying the homepage.
 *
 * This page template will display any functions hooked into the `pizzaro_contactpage` action.
 * By default this includes a variety of product displays and the page content itself. To change the order or toggle these components
 * use the Homepage Control plugin.
 * https://wordpress.org/plugins/homepage-control/
 *
 * Template name: Contactpage
 *
 * @package pizzaro
 */

remove_action( 'pizzaro_content_top', 'pizzaro_breadcrumb', 10 );

do_action( 'pizzaro_before_contactpage' );

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">

			<?php
			/**
			 * @hooked pizzaro_homepage_content - 10
			 */
			do_action( 'pizzaro_contactpage' ); ?>

		</main><!-- #main -->
	</div><!-- #primary -->

	<?php 
get_footer();
