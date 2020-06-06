<header id="tbay-header" class="site-header header-v2 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">
	<div id="tbay-topbar" class="tbay-topbar hidden-sm hidden-xs">
        <div class="container">
	
            <div class="topbar-inner clearfix">
                <div class="row">

					<?php if(is_active_sidebar('top-contact')) : ?>
						<div class="col-md-4 top-contact">
							<?php dynamic_sidebar('top-contact'); ?>
						</div>
					<?php endif;?>

                	<div class="logo-in-theme col-md-4 text-center">

                        <?php 
                        	puca_tbay_get_page_templates_parts('logo'); 
                        ?> 
                    </div>


	                <div class="header-right col-md-4 hidden-sm hidden-xs">
						
						<?php puca_tbay_get_page_templates_parts('topbar-account','02'); ?>

						<?php if ( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED ): ?>
							
							<div class="top-cart-wishlist">
								
								<!-- Wishlist -->
								<div class="top-wishlist">
									<?php puca_tbay_get_page_templates_parts('wishlist'); ?>
								</div>

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
	
	<div class="header-main clearfix">
        <div class="container">
            <div class="header-inner clearfix">
					
					<!-- Main menu -->
					<div class="tbay-mainmenu">
						<?php puca_tbay_get_page_templates_parts('nav','no-black'); ?>
						<?php puca_tbay_get_page_templates_parts('search','horizontal'); ?>

	                </div>
				
            </div>
        </div>
        <div class="container container-search-horizontal">
			<div class="search-horizontal-wrapper">
			    <div class="search-horizontal-content">
				    <?php puca_tbay_get_page_templates_parts( 'productsearchform', 'horizontal'); ?>
				</div>
			</div>
        </div>
    </div>




</header>