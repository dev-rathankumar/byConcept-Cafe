<header id="tbay-header" class="site-header header-default header-v13 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">
    
	<div class="header-category-logo list-category-fix">

         <div class="logo-in-theme">
            <?php 
            	puca_tbay_get_page_templates_parts('logo'); 
            ?> 
        </div>

		<?php if(is_active_sidebar('list-category-fix')) : ?>
			<?php dynamic_sidebar('list-category-fix'); ?>
		<?php endif;?>
 
	</div>

    <div class="header-main clearfix">
        <div class="container container-full">
            <div class="header-inner">
                <div class="row">

                    <div class="col-md-8 tbay-mainmenu"> 
						<?php puca_tbay_get_page_templates_parts('nav'); ?>
                    </div>					

					
                    <div class="header-right col-md-4 hidden-sm hidden-xs">
						
						<div class="search-full">
                        	<?php puca_tbay_get_page_templates_parts('productsearchform','home10'); ?>
						</div>
						
						<?php puca_tbay_get_page_templates_parts('topbar-account'); ?>

						<?php if (!(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED ): ?>
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