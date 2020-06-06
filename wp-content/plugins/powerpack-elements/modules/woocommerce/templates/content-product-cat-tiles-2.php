<?php
/**
 * PowerPack WooCommerce Category - Template.
 *
 * @package PowerPack
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $i == 1 || $i == 2 || $i == 3) {
	$pp_grid_class = '';
} else {
	$pp_grid_class = 'pp-grid-item-wrap';
}

if ( $i == 1) {
	echo '<div class="pp-woo-cat-tiles pp-woo-cat-tiles-2">';
	echo '<div class="pp-woo-cat-tiles-left">';
}
if ( $i == 2) {
	echo '<div class="pp-woo-cat-tiles-right">';
}
if ( $i == 4) {
	echo '<div class="pp-elementor-grid">';
}
?>
<div <?php wc_product_cat_class( 'product ' . $pp_grid_class . ' pp-woo-cat-' . $i, $category ); ?>>
    <div class="pp-grid-item">
	<?php
	/**
	 * Link Open
	 * woocommerce_before_subcategory hook.
	 *
	 * @hooked woocommerce_template_loop_category_link_open - 10
	 */
	do_action( 'woocommerce_before_subcategory', $category );

	/**
	 * Subcategory Title
	 * woocommerce_before_subcategory_title hook.
	 *
	 * @hooked woocommerce_subcategory_thumbnail - 10
	 */
	do_action( 'woocommerce_before_subcategory_title', $category );

	/**
	 * Subcategory Title
	 * woocommerce_shop_loop_subcategory_title hook.
	 *
	 * @hooked woocommerce_template_loop_category_title - 10
	 */
	do_action( 'woocommerce_shop_loop_subcategory_title', $category );

	/**
	 * Subcategory Title
	 * woocommerce_after_subcategory_title hook.
	 */
	do_action( 'woocommerce_after_subcategory_title', $category );

	/**
	 * Link CLose
	 * woocommerce_after_subcategory hook.
	 *
	 * @hooked woocommerce_template_loop_category_link_close - 10
	 */
	do_action( 'woocommerce_after_subcategory', $category );
	?>
    </div>
</div>
<?php
if ( $i == 1 || $i == 3 ) {
	echo '</div>';
}
if ($i == 3 ) {
	echo '</div>';
}
?>