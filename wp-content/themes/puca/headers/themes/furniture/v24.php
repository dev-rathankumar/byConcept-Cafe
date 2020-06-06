
<header id="tbay-header" class="site-header header-v24 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">
	<div id="tbay-topbar">
		<div class="container">
	        <div class="row">
				
			    <div class="top-info">
					<?php if(is_active_sidebar('top-info')) : ?>
						<?php dynamic_sidebar('top-info'); ?>
						<!-- End Bottom Header Widget -->
					<?php endif;?>
				</div>
	        </div>
        </div>
	</div>	
    <div class="header-main clearfix">
    	<div class="container">
    		<div class="row">
    			<!-- //LOGO -->
                <div class="header-logo col-md-2">

                    <?php 
                    	puca_tbay_get_page_templates_parts('logo', '02'); 
                    ?> 
                </div>

                <div class="col-md-10 tbay-mainmenu">
					
					<?php puca_tbay_get_page_templates_parts('nav'); ?>
					
					<div class="header-right">
					
						<?php puca_tbay_get_page_templates_parts('menu-account'); ?>

						<?php if ( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED ): ?>
							<!-- Cart -->
							<div class="top-cart hidden-xs">
								<?php puca_tbay_get_woocommerce_mini_cart(); ?>
							</div>
						<?php endif; ?>

						<?php puca_tbay_get_page_templates_parts( 'productsearchform', 'min'); ?>
						
					</div>

				</div>
    			
    		</div>	
    	</div>
    </div>	
</header>