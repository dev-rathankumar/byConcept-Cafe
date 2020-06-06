
<header id="tbay-header" class="site-header header-default header-v3 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">
	<div class="header-mainmenu clearfix">
        <div class="container container-full">
            <div class="header-inner">
                <div class="row">
					<div class="top-welcome col-md-3">
						<?php puca_tbay_get_page_templates_parts('topbar-welcome'); ?>
					</div>

                    <div class="header-topmenu col-md-9">
						
						<!-- Wishlist -->
						<div class="top-wishlist">
							<?php puca_tbay_get_page_templates_parts('wishlist','text'); ?>
						</div>
						
						<?php puca_tbay_get_page_templates_parts('topmenu'); ?>
						
						<?php puca_tbay_get_page_templates_parts('topbar-account','2'); ?>
						
						<?php
							if( class_exists( 'WOOCS' ) ) {
								wp_enqueue_style('sumoselect');
								wp_enqueue_script('jquery-sumoselect');	
								?>
								<div class="tbay-currency">
								<?php
									echo do_shortcode( '[woocs]' );

								?>
								</div>
								<?php
							}
	                    ?>
						
						
					</div>
					
                </div> 
            </div>
        </div>
    </div>
    <div class="header-main clearfix">
        <div class="container container-full">
            <div class="header-inner">
                <div class="row">
					<!-- //LOGO -->
                    <div class="header-logo col-md-2">

                        <?php 
                        	puca_tbay_get_page_templates_parts('logo'); 
                        ?> 
                    </div>
					
					<div class="header-searh col-md-4">

						<div class="search-full">
                        	<?php puca_tbay_get_page_templates_parts('productsearchform'); ?>
						</div>

					</div>
					

                    <div class="header-right col-md-1 pull-right">
					<?php if ( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED ): ?>
						<div class="top-cart-wishlist">

							<!-- Cart -->
							<div class="top-cart hidden-xs">
								<?php puca_tbay_get_woocommerce_mini_cart(); ?>
							</div>

						</div>
					<?php endif; ?>
				    </div>
					
					<div class="tbay-mainmenu col-md-5">
						<?php puca_tbay_get_page_templates_parts('nav'); ?>
				    </div>
					
                </div> 
            </div>
        </div>
    </div>
</header>