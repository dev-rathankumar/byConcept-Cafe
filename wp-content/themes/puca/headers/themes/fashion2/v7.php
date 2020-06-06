<?php puca_tbay_get_page_templates_parts('offcanvas-main-menu'); ?> 

<header id="tbay-header" class="site-header header-default header-v7 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">
    <div class="header-main clearfix">
        <div class="container container-full">
            <div class="header-inner">
                <div class="row">

                	<div class="header-left col-md-5 col-xlg-4">

						<!-- Main menu -->
						<div class="tbay-mainmenu topbar-mobile pull-left ">
							 <div class="top active-mobile">
								<button data-toggle="offcanvas-main" class="btn btn-sm btn-toggle-canvas" type="button">
								   <i class="icon-menu icons"></i>
								   <?php esc_html_e('Menu', 'puca'); ?>
								</button>
							 </div>
						</div>

						<div class="pull-left">
							<?php if(is_active_sidebar('top-contact')) : ?>
								<div class="top-contact">
									<?php dynamic_sidebar('top-contact'); ?>
								</div>
							<?php endif;?>

						</div>

					</div>

					<!-- //LOGO -->
                    <div class="header-center col-md-2 col-xlg-4 text-center">
 						<div class="logo-in-theme">
	                        <?php 
	                        	puca_tbay_get_page_templates_parts('logo'); 
	                        ?> 
	                    </div>
                    </div>

                    <div class="header-right col-md-5 col-xlg-4 hidden-sm hidden-xs">
						<?php if ( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED ): ?>
							<div class="top-cart-wishlist">
								
								<!-- Cart -->
								<div class="top-cart hidden-xs">
									<?php puca_tbay_get_woocommerce_mini_cart('style2'); ?>
								</div>
							</div>
						<?php endif; ?>
						<div class="search-full">
							<?php puca_tbay_get_page_templates_parts('productsearchform','home7'); ?>
						</div>
					</div>
					
                </div>
            </div>
        </div>
    </div>
</header>