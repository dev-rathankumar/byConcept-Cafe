<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop;
	
// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) ) {
	$woocommerce_loop['loop'] = 0;
}

// Store column count for displaying the grid

if( !empty($columns) ) {
	$woocommerce_loop['columns'] = $columns;
} else if ( empty( $woocommerce_loop['columns'] ) ) {
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
}

// Ensure visibility
if ( ! $product || ! $product->is_visible() ) {
	return;
}

$active_theme = puca_tbay_get_part_theme();

// Increase loop count

// Extra post classes
$classes = array();

if($woocommerce_loop['columns'] == 5) {
	$largedesktop = '2-4';
}else {
	$largedesktop = 12/$woocommerce_loop['columns'];
}

if( isset($screen_desktop) &&  $screen_desktop == 5) {
	$desktop = '2-4';
} elseif( isset($screen_desktop) ) {
	$desktop = 12/$screen_desktop;
}

if( isset($screen_desktopsmall) &&  $screen_desktopsmall == 5) {
	$desktopsmall = '2-4';
} elseif( isset($screen_desktopsmall) ) {
	$desktopsmall = 12/$screen_desktopsmall;
}

if( isset($screen_tablet) &&  $screen_tablet == 5) {
	$tablet = '2-4';
} elseif( isset($screen_tablet) ) {
	$tablet = 12/$screen_tablet;
}

if( isset($screen_mobile) &&  $screen_mobile == 5) {
	$mobile = '2-4';
} elseif( isset($screen_mobile) ) {
	$mobile = 12/$screen_mobile;
}

$classes[] = 'col-xlg-'.$largedesktop.' col-lg-'.$desktop.' col-xs-'. $mobile .' col-md-'.$desktopsmall.' col-sm-'.$tablet;

?>
<div <?php post_class( $classes ); ?> >
	<?php $product_item = isset($product_item) ? $product_item : 'inner'; ?>
 	<?php wc_get_template_part( 'item-product/'.$active_theme.'/'.$product_item ); ?>
</div>
