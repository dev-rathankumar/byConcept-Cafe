
<header id="tbay-header" class="site-header header-v19 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">
	<div id="tbay-topbar">
		<div class="container">
			<div class="row">
				<div class="col-md-8 top-info">
					<?php if(is_active_sidebar('top-info')) : ?>
						<?php dynamic_sidebar('top-info'); ?>
						<!-- End Bottom Header Widget -->
					<?php endif;?>
				</div>
				<div class="col-md-4 header-right">
					<?php
						if( class_exists( 'WOOCS' ) ) {
							?>
							<div class="tbay-currency">
								<?php echo do_shortcode( '[woocs]' ); ?>
							</div>
							<?php
						}
				    ?>

				    <?php puca_tbay_get_page_templates_parts('topbar-account'); ?>

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
	<div class="header-logo header-center clearfix">
		<div class="container logo-in-theme">
			<div class="row">
            <?php 
            	puca_tbay_get_page_templates_parts('logo'); 
            ?> 
        	</div>
        </div>
	</div>	
    <div class="header-main clearfix">
        <div class="container">
            <div class="header-inner">
                <div class="row">
					<!-- //LOGO -->
                    <div class="header-left col-md-3">
		                <?php puca_tbay_get_page_templates_parts('categorymenu'); ?>
                    </div>
					

				    <div class="header-menu col-md-9">
					
						<?php puca_tbay_get_page_templates_parts('nav'); ?>

				    	<div class="search-min hidden-sm hidden-xs"> 
							<?php puca_tbay_get_page_templates_parts('productsearchform','min'); ?>
						</div>
				    </div>
                </div>
            </div>
        </div>
    </div>
</header>