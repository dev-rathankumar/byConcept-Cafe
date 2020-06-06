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
					
				    <div class="tbay-mainmenu text-center col-md-7 hidden-xs hidden-sm">
							<div class="tbay-mainmenu">
								<?php puca_tbay_get_page_templates_parts('nav'); ?>
							</div>

				    </div>

                    <div class="header-right col-md-3 hidden-sm hidden-xs">
						
						<?php if ( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED ): ?>
							<div class="pull-right top-cart-wishlist">

								<!-- Cart -->
								<div class="top-cart hidden-xs pull-right">
									<?php puca_tbay_get_woocommerce_mini_cart(); ?>
								</div>

							</div>
						<?php endif; ?>
						
						<div class="pull-right">
							<?php puca_tbay_get_page_templates_parts('topbar-account'); ?>
						</div>

						<div class="pull-right">
                        	<?php puca_tbay_get_page_templates_parts('search-modal','totop'); ?>
						</div>

					</div>
					
                </div>
            </div>
        </div>
    </div>

    <div class="search-totop-content-wrapper">
	    <div class="search-totop-content">
		    <?php puca_tbay_get_page_templates_parts( 'productsearchform', 'full'); ?>
		</div>
	</div>

</header>