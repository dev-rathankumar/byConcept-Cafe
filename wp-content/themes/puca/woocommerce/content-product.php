<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;


global $product, $woocommerce_loop;

$columns = 12;
$classes[] = 'col-lg-'.$columns.' col-md-'.$columns.' col-sm-'.$columns.' col-xs-12 list';

$woo_display = puca_tbay_woocommerce_get_display_mode();

$active_theme = puca_tbay_get_part_theme();

if( isset($mode) && !empty($mode) ) $woo_display = $mode;

if ( $woo_display == 'list' ) { 	
?>
	<div <?php wc_product_class( $classes, $product ); ?>>
	 	<?php wc_get_template_part( 'item-product/'.$active_theme.'/inner-list' ); ?>
	</div>
<?php 
} else {
	
	// Store loop count we're currently on
	if ( empty( $woocommerce_loop['loop'] ) ) {
		$woocommerce_loop['loop'] = 0;
	}

	// Store column count for displaying the grid
	if ( isset($pagi_columns) && !empty( $pagi_columns ) ) {
		$woocommerce_loop['columns'] = $pagi_columns;
	} else {
		$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
	}
	
	// Ensure visibility
	if ( ! $product || ! $product->is_visible() ) {
		return;
	}

	// Increase loop count
	 
	// Extra post classes
	$classes = array();

	if($woocommerce_loop['columns'] == 5) {
		$columns = '2-4';
	} else {
		$columns = 12/$woocommerce_loop['columns'];
	}

	$classes[] = 'col-xs-12 col-lg-'.$columns.' col-md-'.$columns.' col-sm-6 grid';
	?>

	<div <?php wc_product_class( $classes, $product ); ?>>
		 	<?php wc_get_template_part( 'item-product/'.$active_theme.'/inner' ); ?>
	</div>

<?php } ?>