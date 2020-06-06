<?php puca_tbay_get_page_templates_parts('offcanvas-main-menu'); ?>

<header id="tbay-header" class="site-header header-v5 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">

	<div class="header-bottom-main clearfix">
        <div class="container">
            <div class="top-shipping clearfix row">
				<?php if(is_active_sidebar('top-shipping')) : ?>
					<div class="col-md-12 shipping-main">
						<?php dynamic_sidebar('top-shipping'); ?>
					</div><!-- End Bottom Header Widget -->
				<?php endif;?>
            </div>
        </div>
    </div>

    <div class="header-main clearfix">
        <div class="container container-full">
            <div class="header-inner">
                <div class="row">

				    <div class="header-left col-md-4">

						<!-- Main menu -->
						<div class="tbay-mainmenu pull-left ">
							 <div class="top active-mobile">
								<button data-toggle="offcanvas-main" class="btn btn-sm btn-toggle-canvas" type="button">
								   <i class="icon-menu icons"></i>
								</button>
							 </div>
							
						</div>

						<div class="pull-left">
						   	<?php puca_tbay_get_page_templates_parts('productsearchform', 'home5'); ?>
						</div>

					</div>

                    <!-- LOGO -->
                    <div class="header-center pull-left logo-in-theme col-md-4">
               			<?php puca_tbay_get_page_templates_parts('logo'); ?>
                    </div>	
					

					<div class="header-right col-md-4">
						<?php
							if( class_exists( 'WOOCS' ) ) {
								wp_enqueue_style('sumoselect');
								wp_enqueue_script('jquery-sumoselect');	
								?>
								<div class="tbay-currency">
								<?php
									echo do_shortcode( '[woocs]' );

								?>
								</div>
								<?php
							}
	                    ?>	
	                    
						<?php puca_tbay_get_page_templates_parts('topbar-account'); ?>

						<?php if ( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED ): ?>
							<div class="top-cart-wishlist">

								<!-- Cart -->
								<div class="top-cart hidden-xs">
									<?php puca_tbay_get_woocommerce_mini_cart(); ?>
								</div>
							</div>
						<?php endif; ?>
							
					</div>
					
					
					
                </div>
            </div>
        </div>
    </div>
</header>