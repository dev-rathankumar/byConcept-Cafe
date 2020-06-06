<header id="tbay-header" class="site-header header-v3 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">
    <section id="tbay-mainmenu" class="tbay-mainmenu hidden-xs hidden-sm">
        <div class="container-full container header-main"> 
		
			<div class="header-inner clearfix">
			
				<div class="row"> 
					
					<div class="header-left col-md-3 col-xlg-4">
							<!-- LOGO -->
							<div class="logo-in-theme">
								<?php puca_tbay_get_page_templates_parts('logo'); ?>
							</div>
						    
							<div class="search-full hidden-sm hidden-xs"> 
									<?php puca_tbay_get_page_templates_parts('productsearchform','home3'); ?>
							</div>
						
					</div>
					
					<div class="header-right col-md-9 col-xlg-8">


						<div class="header-menu">
							<?php puca_tbay_get_page_templates_parts('nav'); ?>
						</div>

						<div class="top-account">
							<?php puca_tbay_get_page_templates_parts('topbar-account'); ?>
						</div>


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
    </section>
</header>