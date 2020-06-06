<header id="tbay-header" class="site-header header-default header-v14 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">
    <div class="header-main clearfix">
        <div class="container">
            <div class="header-inner">
                <div class="row">
					<!-- //LOGO -->
                    <div class="header-left text-right">

                    	<div class="content clearfix">
	                    	<div class="logo-in-theme">
		                        <?php 
		                        	puca_tbay_get_page_templates_parts('logo'); 
		                        ?> 
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

						<div class="search search-full">
							<?php puca_tbay_get_page_templates_parts('productsearchform', 'home14'); ?>
						</div>

                    </div>
					

				    <div class="header-right hidden-xs hidden-sm">
						
						<div class="tbay-mainmenu">

					      <div class="visible-xlg tbay-offcanvas-main verticle-menu active-desktop">
        						<?php puca_tbay_get_page_templates_parts('nav-vertical'); ?>
						    </div>					        

						    <div class="hidden-xlg">
						        <?php puca_tbay_get_page_templates_parts('nav'); ?>
						    </div>
						</div>

				    </div>

					<div class="top-newsletter visible-xlg clearfix">
						<?php if(is_active_sidebar('top-newsletter')) : ?>
							<div class="col-md-12">
								<?php dynamic_sidebar('top-newsletter'); ?>
							</div><!-- End Bottom Header Widget -->
						<?php endif;?>
		            </div>

					
                </div>
            </div>
        </div>
    </div>
</header>