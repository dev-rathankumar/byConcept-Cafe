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
