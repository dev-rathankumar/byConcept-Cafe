<?php
/**
 * Single Product Up-Sells
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop;

if ( sizeof( $upsells ) == 0 ) {
	return;
}


if( isset($_GET['releated_columns']) ) { 
	$woocommerce_loop['columns'] = $_GET['releated_columns']; 
} else {
	$woocommerce_loop['columns'] = puca_tbay_get_config('releated_product_columns', 4);
}

$columns_desktopsmall = 3;
$columns_tablet = 2;
$columns_mobile = 2;

$rows = apply_filters( 'puca_supermaket_woo_row_single_full', false,2 ); 
if($rows) {
	$rows = 4;
	$woocommerce_loop['columns'] = 1;
	$columns_desktopsmall = 1;
	$columns_tablet = 1;
	$columns_mobile = 1;
} else {
	$rows = 1;
}

$active_theme = puca_tbay_get_part_theme();

$show_product_upsells = puca_tbay_get_config('show_product_upsells', true);

if ( $upsells && $show_product_upsells ) : ?>

	<div class="related upsells widget products">
		<h3 class="widget-title"><?php esc_html_e( 'You may also like&hellip;', 'puca' ) ?></h3>
		<?php  wc_get_template( 'layout-products/'.$active_theme.'/carousel-related.php' , array( 'loops'=>$upsells,'rows' => $rows, 'pagi_type' => 'no', 'nav_type' => 'yes','columns'=>$woocommerce_loop['columns'],'screen_desktop'=>$woocommerce_loop['columns'],'screen_desktopsmall'=>$columns_desktopsmall,'screen_tablet'=>$columns_tablet,'screen_mobile'=>$columns_mobile ) ); ?>
	</div>

<?php endif;

wp_reset_postdata();
