<?php puca_tbay_get_page_templates_parts('offcanvas-main-menu'); ?>

<header id="tbay-header" class="site-header header-v14 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">

    <div class="header-main clearfix">
        <div class="container container-full">
            <div class="header-inner">
                <div class="row">
                	<div class="header-left col-md-6">
                		<!-- Main menu -->
						<div class="tbay-mainmenu topbar-mobile">
							 <div class="top active-mobile">
								<button data-toggle="offcanvas-main" class="btn btn-sm btn-toggle-canvas" type="button">
								   <i class="icon-menu icons"></i>
								</button>
							 </div>
						</div>

						<!-- LOGO -->
                    <div class="logo-in-theme">
               			<?php puca_tbay_get_page_templates_parts('logo', '02'); ?>
                    </div>

                	</div>	
                		
	                <div class="header-right col-md-6 hidden-sm hidden-xs">
						
						<?php if ( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED ): ?>

							<!-- Cart -->
							<div class="top-cart hidden-xs">
								<?php puca_tbay_get_woocommerce_mini_cart(); ?>
							</div>
							
						<?php endif; ?>
						
						<?php puca_tbay_get_page_templates_parts('productsearchform','min'); ?>
						
					</div>
					
					
                </div>
            </div>
        </div>
    </div>

</header>