<header id="tbay-header" class="site-header header-v4 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">


	<div id="tbay-topbar" class="hidden-sm hidden-xs">

		<div class="header-menu">
			<?php puca_tbay_get_page_templates_parts('nav', 'no-black'); ?>
		</div>

      	<div class="container">

          <div class="header-inner clearfix">
              <div class="row">

					<div class="header-left col-md-3">
						<?php puca_tbay_get_page_templates_parts( 'logo' ); ?>
                	</div>
				
					<div class="header-center col-md-6 text-center">
						
						<div class="search">
                        	<?php puca_tbay_get_page_templates_parts('productsearchform','home4'); ?>
						</div>

					</div> 

                	<div class="header-right col-md-3 hidden-sm hidden-xs">
				
						
						<?php if ( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED ): ?>
							
							<?php puca_tbay_get_page_templates_parts('topbar-account','02'); ?>

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
    

	</div> <!--end tbay topbar-->

</header>