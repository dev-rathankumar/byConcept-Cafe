<?php puca_tbay_get_page_templates_parts('offcanvas-main-menu','right'); ?>

<header id="tbay-header" class="site-header header-default header-v8 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">
    <div class="header-main clearfix">
        <div class="container container-full">
            <div class="header-inner">
                <div class="row">

                	<div class="header-left col-md-4">


 						<div class="logo-in-theme">
	                        <?php 
	                        	puca_tbay_get_page_templates_parts('logo'); 
	                        ?> 
	                    </div>


					</div>


                    <div class="header-right col-md-8 hidden-sm hidden-xs">

						<div class="search-min hidden-sm hidden-xs"> 
							<?php puca_tbay_get_page_templates_parts('search','modal-totop'); ?>
							<div class="search-totop-content-wrapper"> 
							    <div class="search-totop-content">
								    <?php puca_tbay_get_page_templates_parts( 'productsearchform', 'home8'); ?>
								</div>
							</div>
						</div>

						<?php if ( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED ): ?>
							<div class="top-cart-wishlist">
								
								<!-- Cart -->
								<div class="top-cart hidden-xs">
									<?php puca_tbay_get_woocommerce_mini_cart(); ?>
								</div>
							</div>
						<?php endif; ?>

						<!-- Main menu -->
						<div class="tbay-mainmenu topbar-mobile pull-right ">
							 <div class="top active-mobile">
								<button data-toggle="offcanvas-main" class="btn btn-sm btn-toggle-canvas" type="button">
								   <i class="icon-menu icons"></i>
								</button>
							 </div>
						</div>


					</div>
					
                </div>
            </div>
        </div>
    </div>
</header>