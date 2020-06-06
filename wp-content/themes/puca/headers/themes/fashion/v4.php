<header id="tbay-header" class="site-header header-v4 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">
	<div id="tbay-topbar" class="hidden-sm hidden-xs">
			<div class="top-bar clearfix">
	        <div class="container container-full">
	            <div class="shipping-contact-inner">

	            	<div class="shipping-main">

	            		<?php if(is_active_sidebar('top-shipping')) : ?>
							<?php dynamic_sidebar('top-shipping'); ?>
						<?php endif;?>

	            	</div>
	            </div>
	        </div>
	    </div>

      <div class="container-full container">

          <div class="header-inner clearfix">
              <div class="row">

					<div class="header-left col-md-2">
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
				
					<div class="header-center col-md-8 text-center">
						
						<?php puca_tbay_get_page_templates_parts( 'logo' ); ?>

						<?php puca_tbay_get_page_templates_parts('nav'); ?>

					</div>

                	<div class="header-right col-md-2 hidden-sm hidden-xs">
				
						<div class="content">

							<div class="search">
	                        	<?php puca_tbay_get_page_templates_parts('search-modal','totop'); ?>
							</div>

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
    
	 	<div class="search-totop-content-wrapper">
		    <div class="search-totop-content">
			    <?php puca_tbay_get_page_templates_parts( 'productsearchform', 'full'); ?>
			</div>
		</div>

	</div> <!--end tbay topbar-->

</header>