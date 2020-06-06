<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "site-content" div.
 *
 * @package WordPress
 * @subpackage Puca
 * @since Puca 1.3.6
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php $tbay_header = apply_filters( 'puca_tbay_get_header_layout', puca_tbay_get_config('header_type', 'v1') );
	  if ( empty($tbay_header) ) {
		$tbay_header = 'v1';
	  }
	  $active_theme = puca_tbay_get_theme();
	?>
<div id="wrapper-container" class="wrapper-container <?php echo esc_attr($tbay_header); ?>">


	<?php

		if ( !wp_is_mobile() ) {
			if ( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED ):

				$position = apply_filters( 'puca_cart_position', 10,2 );

				if( $position == 'top' ) {
					puca_tbay_get_page_templates_parts('offcanvas-cart','top');
				} else if( $position == 'bottom' ) {
					puca_tbay_get_page_templates_parts('offcanvas-cart','bottom');
				}

			endif;
		} else {
			if ( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED ):
				puca_tbay_get_page_templates_parts('offcanvas-cart','right');
			endif;
		}

	?>
 
	<?php puca_tbay_get_page_templates_parts('offcanvas-menu'); ?>
	<?php puca_tbay_get_page_templates_parts('offcanvas-smartmenu'); ?>

	<?php puca_tbay_get_page_templates_parts('device/topbar-mobile'); ?>
	<?php 
		if( puca_tbay_get_config('mobile_footer_icon',true) ) {
			puca_tbay_get_page_templates_parts('device/footer-mobile');
		}
	 ?>

	<?php puca_tbay_get_page_templates_parts('topbar-mobile'); ?>

	<?php get_template_part( 'headers/themes/'.$active_theme.'/'.$tbay_header ); ?>

	<div id="tbay-main-content">
