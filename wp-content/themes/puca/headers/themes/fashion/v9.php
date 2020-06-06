<?php if ( is_active_sidebar( 'top-slider' ) ) : ?>
	<div class="top-slider  hidden-sm hidden-xs">
		<?php dynamic_sidebar( 'top-slider' ); ?>
	</div>
<?php endif; ?> 

<header id="tbay-header" class="site-header header-default header-v9 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">

    <div class="header-main clearfix">
        <div class="container">
            <div class="header-inner">
                <div class="row">

					<div class="header-left col-md-4  hidden-sm hidden-xs">
						<?php if ( is_active_sidebar( 'top-contact' ) ) : ?>
							<div class="top-contact-wrapper">
								<?php dynamic_sidebar( 'top-contact' ); ?>
							</div>
						<?php endif; ?> 
					</div>

					<!-- //LOGO -->
                    <div class="header-center col-md-4 text-center">

                    	<div class="logo-in-theme">
	                        <?php 
	                        	puca_tbay_get_page_templates_parts('logo'); 
	                        ?> 
	                    </div>
	                    
                    </div>
					
                    <!-- SEARCH -->
                    <div class="header-right col-md-4 hidden-sm hidden-xs">
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

    <div class="header-mainmenu text-center clearfix hidden-xs hidden-sm">
        <div class="container">	
			<div class="tbay-mainmenu">
				
					<?php puca_tbay_get_page_templates_parts('nav'); ?>

		    </div>
        </div>
    </div>    

    <div class="search-full">	

    	<div class="container">
			<?php puca_tbay_get_page_templates_parts('productsearchform','home8'); ?>
		</div>
    </div>

</header>