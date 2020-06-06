
<header id="tbay-header" class="site-header header-v10 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">
    <div class="header-main clearfix">
        <div class="container">
            <div class="row">
            	<div class="header-left col-md-3">
            		<?php puca_tbay_get_page_templates_parts('menu-account'); ?>
            	</div>	
				<!-- //LOGO -->
                <div class="header-top-logo col-md-6">

                    <?php 
                    	puca_tbay_get_page_templates_parts('logo'); 
                    ?> 
                </div>
				
				<div class="header-right col-md-3">
					<div class="search-min hidden-sm hidden-xs"> 
						<?php puca_tbay_get_page_templates_parts('productsearchform','min'); ?>
					</div>
					
					<?php if ( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED ): ?>
						<!-- Cart -->
						<div class="top-cart hidden-xs">
							<?php puca_tbay_get_woocommerce_mini_cart(); ?>
						</div>
					<?php endif; ?>
				</div>
				
            </div>
        </div>
    </div>
    <div class="header-mainmenu clearfix">
    	<div class="container">
    		<div class="row">
					
				<?php puca_tbay_get_page_templates_parts('nav'); ?>
				
    		</div>
    	</div>
    </div>			
</header>