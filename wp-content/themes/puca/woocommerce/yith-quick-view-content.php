<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

 
while ( have_posts() ) : the_post(); ?>

	<div class="product">

	<?php if ( !post_password_required() ) { ?>

		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-6">
				<?php
					/**
					 * woocommerce_before_single_product_summary hook
					 *
					 * @hooked woocommerce_show_product_sale_flash - 10
					 * @hooked woocommerce_show_product_images - 20
					 */

					wc_get_template( 'single-product/product-image-carousel.php' );
				?>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-6">
                <div class="summary entry-summary">

                	<?php 

                		if( class_exists( 'TA_WC_Variation_Swatches' ) ) {
	                	   	add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'puca_get_swatch_html' , 10, 2 ); 
							add_filter( 'tawcvs_swatch_html', 'puca_swatch_html' , 5, 4 );
						}

                	?>

                	<?php remove_action('woocommerce_after_add_to_cart_button', 'puca_product_buttons', 10); ?>
                    <?php do_action( 'yith_wcqv_product_summary' ); ?>
                    <?php add_action('woocommerce_after_add_to_cart_button', 'puca_product_buttons', 10); ?>
                </div>
			</div>
            <?php do_action( 'yith_wcqv_after_product_summary' ); ?>
		</div>

		<?php

	} else {
		echo get_the_password_form();
	}
	?>

	</div>


<?php endwhile; // end of the loop.
?>

<?php if( class_exists( 'TA_WC_Variation_Swatches' ) ) : ?>
<script type="text/javascript">
    jQuery(function ($) {
		$( '#yith-quick-view-modal .variations_form' ).tawcvs_variation_swatches_form();
		$( document.body ).trigger( 'tawcvs_initialized' );
    });
</script>
<?php endif; ?>