<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * Override this template by copying it to yourtheme/woocommerce/content-single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

global $product;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$styles   = 	apply_filters( 'woo_class_single_product', 10, 2 );

$images_layout   =  apply_filters( 'woo_images_layout_single_product', 10, 2 );

$sidebar_configs = puca_tbay_get_woocommerce_layout_configs();

$active_full = false;

$tabs_position   =  apply_filters( 'woo_tabs_position_layout_single_product', 10, 2 );

if( !isset($sidebar_configs['left']) && !isset($sidebar_configs['right']) ) {
	$active_full = true;
}

$show_product_releated 	= puca_tbay_get_config('show_product_releated', true);
$show_product_upsells 	= puca_tbay_get_config('show_product_upsells', true);

$upsells = $product->get_upsell_ids();

if( (!$show_product_releated && !$show_product_upsells) || ( !$show_product_releated && sizeof( $upsells ) == 0 ) ) {
	$active_full = false;
}

?>

<?php
	/**
	 * woocommerce_before_single_product hook
	 *
	 * @hooked wc_print_notices - 10
	 * @hooked puca_woo_product_single_time_countdown - 20
	 */
	 do_action( 'woocommerce_before_single_product' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
?>

<div id="product-<?php the_ID(); ?>" <?php post_class($styles); ?>>

<?php if( isset($images_layout) && $images_layout !='carousel' ) : ?>

<?php 

if($active_full) {
	echo '<div class="row row-active-full"><div class="col-md-12 col-xlg-9">';
}

?>

<div class="row">
	<div class="image-mains">
		<?php
			/**
			 * woocommerce_before_single_product_summary hook
			 *
			 * @hooked woocommerce_show_product_sale_flash - 10
			 * @hooked woocommerce_show_product_images - 20
			 */
			do_action( 'woocommerce_before_single_product_summary' );
		?>
	</div>
	<div class="information">
		<div class="summary entry-summary ">

			<?php
				/**
				 * woocommerce_single_product_summary hook
				 *
				 * @hooked woocommerce_template_single_title - 5
				 * @hooked woocommerce_template_single_rating - 10
				 * @hooked woocommerce_template_single_price - 10
				 * @hooked woocommerce_template_single_excerpt - 20
				 * @hooked woocommerce_template_single_add_to_cart - 30
				 * @hooked woocommerce_template_single_meta - 40
				 * @hooked woocommerce_template_single_sharing - 50
				 */
				do_action( 'woocommerce_single_product_summary' );
			?>

		</div><!-- .summary -->
	</div>
</div>

<?php
	/**
	 * woocommerce_after_row_full_single_product_summary hook
	 *
	 * @hooked woocommerce_output_product_data_tabs - 10
	 */

	if($active_full) {

		if($tabs_position != 'right') {
			add_action( 'woocommerce_after_row_full_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
		}


		do_action( 'woocommerce_after_row_full_single_product_summary' ); 
		echo '</div>';    
	}


?>

	<?php
		/**
		 * woocommerce_after_single_product_summary hook
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_upsell_display - 15
		 * @hooked woocommerce_output_related_products - 20
		 */

		if($active_full) {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );  
			echo '<div class="col-md-12 col-xlg-3">';  
		}

		do_action( 'woocommerce_after_single_product_summary' );

		if($active_full) {
			echo '</div>';
			echo '</div>';
			add_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );   
		}
	?>

<?php else : ?>

	<div class="image-mains">
		<?php
			/**
			 * woocommerce_before_single_product_summary hook
			 *
			 * @hooked woocommerce_show_product_sale_flash - 10
			 * @hooked woocommerce_show_product_images - 20
			 */
			do_action( 'woocommerce_before_single_product_summary' );
		?>
	</div>

	<div class="container">

		<div class="information">
			<div class="summary entry-summary ">

				<?php
					/**
					 * woocommerce_single_product_summary hook
					 *
					 * @hooked woocommerce_template_single_title - 5
					 * @hooked woocommerce_template_single_rating - 10
					 * @hooked woocommerce_template_single_price - 10
					 * @hooked woocommerce_template_single_excerpt - 20
					 * @hooked woocommerce_template_single_add_to_cart - 30
					 * @hooked woocommerce_template_single_meta - 40
					 * @hooked woocommerce_template_single_sharing - 50
					 */
					do_action( 'woocommerce_single_product_summary' );
				?>

			</div><!-- .summary -->
		</div>

			<?php
				/**
				 * woocommerce_after_single_product_summary hook
				 *
				 * @hooked woocommerce_output_product_data_tabs - 10
				 * @hooked woocommerce_upsell_display - 15
				 * @hooked woocommerce_output_related_products - 20
				 */
				do_action( 'woocommerce_after_single_product_summary' );
			?>

	</div>


<?php endif; ?>

</div><!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'woocommerce_after_single_product' ); ?>
