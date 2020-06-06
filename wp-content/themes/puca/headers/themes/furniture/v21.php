
<header id="tbay-header" class="site-header header-v21 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">
	<div class="header-main">
		<div class="container">
			<div class="row">
				<div class="col-md-3">
					<?php 
		            	puca_tbay_get_page_templates_parts('logo'); 
		            ?>	
				</div>	
				<div class="header-search col-md-6">
					<div class="search-full">
                    	<?php puca_tbay_get_page_templates_parts('productsearchform','full'); ?>
					</div>
				</div>
				<div class="col-md-3 header-right">
					<?php
						if( class_exists( 'WOOCS' ) ) {
							?>
							<div class="tbay-currency">
								<?php echo do_shortcode( '[woocs]' ); ?>
							</div>
							<?php
						}
				    ?>

				    <?php puca_tbay_get_page_templates_parts('menu-account', '2'); ?>

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
    <div class="header-mainmenu clearfix">
        <div class="container">
            <div class="header-inner">
                <div class="row">
					<!-- //LOGO -->
                    <div class="header-left col-md-3">
		                <?php puca_tbay_get_page_templates_parts('categorymenu'); ?>
                    </div>
					

				    <div class="tbay-mainmenu col-md-9">
					
						<?php puca_tbay_get_page_templates_parts('nav'); ?>

				    </div>
                </div>
            </div>
        </div>
    </div>
</header>