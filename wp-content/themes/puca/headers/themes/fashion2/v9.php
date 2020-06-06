<header id="tbay-header" class="site-header header-default header-v9 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">

    <div class="header-main clearfix">
        <div class="container container-full">
            <div class="header-inner">
                <div class="row">


					<!-- //LOGO -->
                    <div class="header-center col-md-2 col-xlg-3">

                    	<div class="logo-in-theme">
	                        <?php 
	                        	puca_tbay_get_page_templates_parts('logo'); 
	                        ?> 
	                    </div>
	                    
                    </div>
					
                    <!-- SEARCH -->
                    <div class="header-right col-xlg-9 col-md-10 hidden-sm hidden-xs">

			            <div class="tbay-mainmenu">
							<?php puca_tbay_get_page_templates_parts('nav', 'no-black'); ?>
					    </div>

	                    <?php puca_tbay_get_page_templates_parts('topbar-account', '02'); ?>

						<?php if ( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED ): ?>
							<div class="top-cart-wishlist">

								<!-- Cart -->
								<div class="top-cart hidden-xs">
									<?php puca_tbay_get_woocommerce_mini_cart('style2'); ?>
								</div>

							</div>
						<?php endif; ?>
							
					</div>
					
                </div>
            </div>
        </div>
    </div> 

    <div class="search-full">	

    	<div class="container">
			<?php puca_tbay_get_page_templates_parts('productsearchform','home9'); ?>
		</div>
    </div>

</header>