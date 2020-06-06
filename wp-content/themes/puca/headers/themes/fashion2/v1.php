<header id="tbay-header" class="site-header header-default header-v1 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">
    <div class="header-main clearfix">
        <div class="container container-full">
            <div class="header-inner">
                <div class="row">
					<!-- //LOGO -->
                    <div class="header-left col-md-2 col-xlg-2 text-left"> 

                        <?php 
                        	puca_tbay_get_page_templates_parts('logo'); 
                        ?> 
                    </div>
					
				    <div class="header-center tbay-mainmenu col-md-8 col-xlg-7 hidden-xs hidden-sm">
						
							<?php puca_tbay_get_page_templates_parts('nav'); ?>

				    </div>

                    <div class="header-right col-md-2 col-xlg-3 hidden-sm hidden-xs">

						<?php puca_tbay_get_page_templates_parts('topbar-account'); ?>

						<?php if ( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED ): ?>
							<div class="top-cart-wishlist">

								<!-- Cart -->
								<div class="top-cart hidden-xs">
									<?php puca_tbay_get_woocommerce_mini_cart(); ?>
								</div>

							</div>
						<?php endif; ?>

						<div class="search-full">
                        	<?php puca_tbay_get_page_templates_parts('productsearchform','full'); ?>
						</div>

					</div>
					
                </div> 
            </div>
        </div>
    </div>
</header>