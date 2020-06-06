<?php puca_tbay_get_page_templates_parts('offcanvas-main-menu'); ?>

<header id="tbay-header" class="site-header header-v5 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">

    <div class="header-main clearfix">
        <div class="container container-full">
            <div class="header-inner">
                <div class="row">

                	                    <!-- LOGO -->
                    <div class="header-left pull-left logo-in-theme col-md-2 col-xlg-2">
               			<?php puca_tbay_get_page_templates_parts('logo'); ?>
                    </div>	

				    <div class="header-center col-md-7 col-xlg-8">

						<!-- Main menu -->
						<div class="header-menu">
							<?php puca_tbay_get_page_templates_parts('nav'); ?>
						</div>

					</div>


					

	                <div class="header-right col-md-3 col-xlg-2 hidden-sm hidden-xs">
						

						<div class="search-min hidden-sm hidden-xs"> 
							<?php puca_tbay_get_page_templates_parts('search','modal-totop'); ?>
							<div class="search-totop-content-wrapper"> 
							    <div class="search-totop-content">
								    <?php puca_tbay_get_page_templates_parts( 'productsearchform', 'home5'); ?>
								</div>
							</div>
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