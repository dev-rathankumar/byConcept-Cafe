<?php 
global $product,$woocommerce_loop;

if( !isset($columns) ) {
	$columns = 4;
} 

$woocommerce_loop['columns'] = $columns;

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

$classes[] 		= 'col-xlg-'.$largedesktop.' col-lg-'.$desktop.' col-xs-'. $mobile .' col-md-'.$desktopsmall.' col-sm-'.$tablet;

?>
<div <?php post_class( $classes ); ?> >
	<div class="product-block product-special product">
		<div class="product-content">
			<div class="block-inner">
				<figure class="image">
					<a title="<?php the_title_attribute(); ?>" href="<?php echo the_permalink(); ?>" class="product-image">
						<?php
							/**
							* woocommerce_before_shop_loop_item_title hook
							*
							* @hooked woocommerce_show_product_loop_sale_flash - 10
							* @hooked woocommerce_template_loop_product_thumbnail - 10
							*/
							remove_action('woocommerce_before_shop_loop_item_title','woocommerce_show_product_loop_sale_flash', 10);
							do_action( 'woocommerce_before_shop_loop_item_title' );
						?>
					</a>

					<?php 
						do_action( 'woocommerce_before_shop_loop_item_title_2' );
					?>

					<?php (class_exists( 'YITH_WCBR' )) ? puca_brands_get_name($product->get_id()) : ''; ?>
				</figure>
				
			</div>
			<div class="caption">
				<h3 class="name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>			
				<?php
					/**
					* woocommerce_after_shop_loop_item_title hook
					*
					* @hooked woocommerce_template_loop_rating - 5
					* @hooked woocommerce_template_loop_price - 10
					*/
					add_action('woocommerce_after_shop_loop_item_title','woocommerce_show_product_loop_sale_flash', 15);
					do_action( 'woocommerce_after_shop_loop_item_title');

				?>
				<div class="groups-button-image clearfix">	

					<div class="button-wishlist">
						<?php
							$enabled_on_loop = 'yes' == get_option( 'yith_wcwl_show_on_loop', 'no' );
								if( class_exists( 'YITH_WCWL' ) || $enabled_on_loop ) {
								echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
							}
						?>  
					</div>

					<?php if (class_exists('YITH_WCQV_Frontend')) { ?>
						<div>
							<a href="#" class="button yith-wcqv-button" title="<?php echo esc_html__('Quick view', 'puca'); ?>"  data-product_id="<?php echo esc_attr($product->get_id()); ?>">
								<span>
									<i class="icon-eye icons"></i>
								</span>
							</a>
						</div>	
					<?php } ?>

					<?php if( class_exists( 'YITH_Woocompare' ) ) { ?>
						<?php
							$action_add = 'yith-woocompare-add-product';
							$url_args = array(
								'action' => $action_add,
								'id' => $product->get_id()
							);
						?>
						<div class="yith-compare">
							<a href="<?php echo wp_nonce_url( add_query_arg( $url_args ), $action_add ); ?>" title="<?php echo esc_html__('Compare', 'puca'); ?>" class="compare" data-product_id="<?php echo esc_attr($product->get_id()); ?>">
								<i class="icon-shuffle icons"></i>
							</a>
						</div>
					<?php } ?> 
			    </div>
			    <?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
				<?php
					$action_add = 'yith-woocompare-add-product';
					$url_args = array(
						'action' => $action_add,
						'id' => $product->get_id()
					);
				?>
			</div>
	    </div>
	</div>	
</div>	