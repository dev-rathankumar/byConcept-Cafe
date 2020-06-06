<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package pizzaro
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
	<?php
	do_action( 'pizzaro_before_header' ); ?>

	<?php $header_bg_version = pizzaro_get_header_bg_version(); ?>

	<header id="masthead" class="site-header header-v2 <?php echo esc_attr( $header_bg_version ); ?>" role="banner" style="<?php pizzaro_header_styles(); ?>">
		<div class="site-header-wrap">
		<div class="col-full">

			<?php
			/**
			 * Functions hooked into pizzaro_header_v1 action
			 *
			 * @hooked pizzaro_skip_links                       - 0
			 * @hooked pizzaro_primary_navigation               - 20
			 * @hooked pizzaro_site_branding                    - 30
			 * @hooked pizzaro_header_navigation_link           - 40
			 * @hooked pizzaro_header_cart                      - 50
			 */
			do_action( 'pizzaro_header_v2' ); ?>

		</div>
		</div>
	</header><!-- #masthead -->

	<?php
	/**
	 * Functions hooked in to pizzaro_before_content
	 *
	 * @hooked pizzaro_header_widget_region - 10
	 */
	do_action( 'pizzaro_before_content' ); ?>

	<div id="content" class="site-content" tabindex="-1" <?php pizzaro_site_content_style(); ?>>
		<div class="col-full">

		<?php
		/**
		 * Functions hooked in to pizzaro_content_top
		 *
		 * @hooked woocommerce_breadcrumb - 10
		 */
		do_action( 'pizzaro_content_top' );
