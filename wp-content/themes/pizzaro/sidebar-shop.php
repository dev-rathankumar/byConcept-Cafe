<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package pizzaro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div id="secondary" class="widget-area" role="complementary">
<?php
	if ( is_active_sidebar( 'shop-sidebar-1' ) ) {

		dynamic_sidebar( 'shop-sidebar-1' );

	} else {

		do_action( 'pizzaro_default_shop_sidebar_widgets' );
	}
?>
</div><!-- /.sidebar-shop -->