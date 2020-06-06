
<header id="tbay-header" class="site-header header-v18 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">
	<div class="visible-xlg">
	    <div class="header-main clearfix">
	        <div class="container">
	            <div class="header-inner">
	                <div class="row">
						<!-- //LOGO -->
	                    <div class="header-left">
	                    	<div class="search-full v2">
			                	<?php puca_tbay_get_page_templates_parts('productsearchform'); ?>
							</div>
	                    	<div class="logo-in-theme">
		                        <?php 
		                        	puca_tbay_get_page_templates_parts('logo', '02'); 
		                        ?> 
		                    </div>

	                    </div>
						

					    <div class="header-right">
							
							<div class="tbay-mainmenu">

						      <div class="visible-xlg tbay-offcanvas-main verticle-menu active-desktop">
	        						<?php puca_tbay_get_page_templates_parts('nav-vertical', '2'); ?>
							    </div>
							</div>

					    </div>

					    <?php if(is_active_sidebar('top-social')) : ?>
						<div class="top-social clearfix">
							<?php dynamic_sidebar('top-social'); ?>
							<!-- End Bottom Header Widget -->
			            </div>
			            <?php endif;?>

						<?php if(is_active_sidebar('top-copyright')) : ?>
						<div class="top-copyright clearfix">
							<?php dynamic_sidebar('top-copyright'); ?>
							<!-- End Bottom Header Widget -->
			            </div>
			            <?php endif;?>
						
	                </div>
	            </div>
	        </div>
	    </div>
	</div>
    <div class="hidden-xlg">
		<div class="header-main clearfix">
	        <div class="container container-full">
	            <div class="row">
					<!-- //LOGO -->
	                <div class="header-logo col-md-2">

	                    <?php 
	                    	puca_tbay_get_page_templates_parts('logo'); 
	                    ?> 
	                </div>

	                <div class="tbay-mainmenu col-md-8">
						
						<?php puca_tbay_get_page_templates_parts('nav'); ?>
						
					</div>
					
					<div class="header-right col-md-2">
						

						<?php puca_tbay_get_page_templates_parts('productsearchform','min'); ?>

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
	</div>
</header>