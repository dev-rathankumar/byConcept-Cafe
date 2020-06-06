<header id="tbay-header" class="site-header header-v3 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">
    <section id="tbay-mainmenu" class="tbay-mainmenu hidden-xs hidden-sm">
        <div class="container-full container header-main"> 
		
			<div class="header-inner clearfix">
			
				<div class="row"> 
					
					<div class="header-left col-md-2 col-xlg-2">
							<!-- LOGO -->
							<div class="pull-left logo-in-theme">
								<?php puca_tbay_get_page_templates_parts('logo'); ?>
							</div>
						    
						
					</div>
					
					<div class="header-center col-md-8 col-xlg-8">

						<div class="header-menu-search">
							<div class="header-menu">
								<?php puca_tbay_get_page_templates_parts('nav','no-black'); ?>
							</div>

							<div class="search-min hidden-sm hidden-xs"> 
								<?php puca_tbay_get_page_templates_parts('search','modal-totop'); ?>
								<div class="search-totop-content-wrapper"> 
								    <div class="search-totop-content">
									    <?php puca_tbay_get_page_templates_parts( 'productsearchform', 'min'); ?>
									</div>
								</div>
							</div>
						</div>
						

					</div>

					<div class="header-right col-md-2 col-xlg-2">

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
    </section>
</header>