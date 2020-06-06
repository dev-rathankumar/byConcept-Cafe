<?php puca_tbay_get_page_templates_parts('offcanvas-cart','top'); ?>

<header id="tbay-header" class="site-header header-default header-v11 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">
    <div class="header-main clearfix">
        <div class="container container-full">
            <div class="header-inner">
                <div class="row">
					<!-- //LOGO -->
                    <div class="header-left col-md-2 text-left">

                    	<div class="logo-in-theme">
	                        <?php 
	                        	puca_tbay_get_page_templates_parts('logo'); 
	                        ?> 
	                    </div>

                    </div>
					
				    <div class="tbay-mainmenu col-md-7 hidden-xs hidden-sm">
						<?php puca_tbay_get_page_templates_parts('nav'); ?>
				    </div>

                    <!-- SEARCH -->
                    <div class="header-right col-md-3 hidden-sm hidden-xs">
						
						<?php puca_tbay_get_page_templates_parts('topbar-account'); ?>


						<?php if ( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED ): ?>
							<div class="top-cart-wishlist">
								
								<!-- Cart -->
								<div class="top-cart hidden-xs">
									<?php puca_tbay_get_woocommerce_mini_cart(); ?>
								</div>

							</div>
						<?php endif; ?>

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

    <div class="search-full text-center">	
    	<div class="container">
			<?php puca_tbay_get_page_templates_parts('productsearchform','home8'); ?>
		</div>
    </div>

</header>