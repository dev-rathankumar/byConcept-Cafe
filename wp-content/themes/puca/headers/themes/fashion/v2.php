<header id="tbay-header" class="site-header header-v2 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">
	<div id="tbay-topbar" class="tbay-topbar hidden-sm hidden-xs">
        <div class="container">
	
            <div class="topbar-inner clearfix">
                <div class="row">

                	<div class="logo-in-theme col-md-12 text-center">

                        <?php 
                        	puca_tbay_get_page_templates_parts('logo'); 
                        ?> 
                    </div>

				</div>
				
            </div>
        </div> 
    </div>
	
	<div class="header-main clearfix">
        <div class="container">
            <div class="header-inner clearfix">
            	<div class="row">
	                <!-- LOGO -->
	                <div class="col-md-2">
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
					
					<!-- Main menu -->
					<div class="tbay-mainmenu col-md-7">
						<?php puca_tbay_get_page_templates_parts('nav'); ?>
	                </div>
					

	                <div class="search col-md-3 hidden-sm hidden-xs">
						
						<?php puca_tbay_get_page_templates_parts('search-modal','totop'); ?>

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

	<div class="header-bottom-main clearfix">
        <div class="container">
            <div class="top-shipping clearfix row">
				<?php if(is_active_sidebar('top-shipping')) : ?>
					<div class="col-md-12 shipping-main">
						<?php dynamic_sidebar('top-shipping'); ?>
					</div><!-- End Bottom Header Widget -->
				<?php endif;?>
            </div>
        </div>
    </div>

    <div class="search-totop-content-wrapper">
	    <div class="search-totop-content">
		    <?php puca_tbay_get_page_templates_parts( 'productsearchform', 'full'); ?>
		</div>
	</div>

</header>