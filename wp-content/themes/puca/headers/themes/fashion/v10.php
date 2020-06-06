<header id="tbay-header" class="site-header header-default header-v10 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">
    

	<div class="top-bar clearfix">
        <div class="container container-full">
            <div class="shipping-contact-inner text-center">

            	<div class="shipping-main">

            		<?php if(is_active_sidebar('top-shipping')) : ?>
						<?php dynamic_sidebar('top-shipping'); ?>
					<?php endif;?>

            	</div>
            </div>
        </div>
    </div>

    <div class="header-main clearfix">
        <div class="container container-full">
            <div class="header-inner">
                <div class="row">
					<!-- //LOGO -->
                    <div class="header-left col-md-2 text-left">

                    	<div class="logo-in-theme">
	                        <?php 
	                        	puca_tbay_get_page_templates_parts('logo'); 
	                        ?> 
	                    </div>

                    </div>
					
				    <div class="tbay-mainmenu col-md-7 hidden-xs hidden-sm">
						<?php puca_tbay_get_page_templates_parts('nav'); ?>

				    </div>


                    <div class="header-right col-md-3 hidden-sm hidden-xs">
						
						<div class="search-full">
                        	<?php puca_tbay_get_page_templates_parts('productsearchform','home10'); ?>
						</div>
						
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