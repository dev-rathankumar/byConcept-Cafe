<header id="tbay-header" class="site-header header-default header-v6 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">
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
					
				    <div class="tbay-mainmenu text-center col-md-8 hidden-xs hidden-sm">
							<div class="tbay-mainmenu">
								<?php puca_tbay_get_page_templates_parts('nav', 'no-black'); ?>
							</div>

				    </div>

                    <div class="header-right col-md-2 hidden-sm hidden-xs">
						
						<?php if ( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED ): ?>
							<div class="pull-right top-cart-wishlist">

								<!-- Cart -->
								<div class="top-cart hidden-xs pull-right">
									<?php puca_tbay_get_woocommerce_mini_cart('style2'); ?>
								</div>

							</div>
						<?php endif; ?>
						
						<div class="pull-right">
							<?php puca_tbay_get_page_templates_parts('topbar-account', '02'); ?>
						</div>

					</div>
					
                </div>
            </div>
        </div>
    </div>

    <div class="search-totop-tags-wrapper text-center">
	    <div class="search-totop-content-tags">
		  	<?php puca_tbay_get_page_templates_parts('productsearchform','home6'); ?>
		</div>
	</div>

</header>