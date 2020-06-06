<?php 
	$enable_categoires 		= puca_tbay_get_config('enable_categoires', true);
	$class_mainmenu  		= ($enable_categoires) ? '9' : '12';
?>
<header id="tbay-header" class="site-header header-v2 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">
	<div id="tbay-topbar" class="tbay-topbar hidden-sm hidden-xs">
        <div class="container">
	
            <div class="topbar-inner clearfix row">
				<?php if(is_active_sidebar('top-contact')) : ?>
					<div class="col-md-6 top-contact">
						<?php dynamic_sidebar('top-contact'); ?>
					</div><!-- End Top Contact Widget -->
				<?php endif;?>
				
				<div class="col-md-6 top-right ">
					<?php puca_tbay_get_page_templates_parts( 'topmenu' ); ?>
					<?php puca_tbay_get_page_templates_parts( 'topbar-account' ); ?>

				</div>
            </div>
        </div> 
    </div>
	
	<div class="header-main clearfix sticky-off">
        <div class="container">
            <div class="header-inner clearfix row">
                <!-- LOGO -->
                <div class="logo-in-theme col-md-3">
                    <?php puca_tbay_get_page_templates_parts( 'logo', '02' ); ?>
                </div>
				<div class="col-md-5">
					<?php puca_tbay_get_page_templates_parts( 'productsearchform' ); ?>
					<!-- //Cart -->
					
				</div>
				<div class="col-md-4 hidden-sm hidden-xs">
					<div class="header-setting clearfix">
						<?php if ( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED ): ?>
							<?php puca_tbay_get_woocommerce_mini_cart('style2'); ?>
							<?php puca_tbay_get_page_templates_parts( 'wishlist', '02' ); ?>
						<?php endif; ?>
						<?php puca_tbay_get_page_templates_parts( 'account' ); ?>
					</div>
				</div>
            </div>
        </div>
    </div>

	<div class="tbay-mainmenu">
		<div class="container">
			<div class="row">
				<div class="mainmenu clearfix">

					<?php if( $enable_categoires ) : ?>
						<div class="col-md-3">
							<?php puca_tbay_get_page_templates_parts( 'categorymenuimg', 'v2' ); ?>
						</div>
					<?php endif; ?>

					<!-- Main menu -->
					<div class="tbay-mainmenu col-md-<?php echo esc_attr( $class_mainmenu ); ?>">

						<?php puca_tbay_get_page_templates_parts( 'nav' ); ?>

					</div>
				</div>	
			</div>	
		</div>	
	</div>

</header>