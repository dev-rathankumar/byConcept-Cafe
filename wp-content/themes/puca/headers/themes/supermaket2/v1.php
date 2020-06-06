<?php 
	$enable_categoires 	= puca_tbay_get_config('enable_categoires', true);
	$class_search  		= ($enable_categoires) ? '6' : '9';
?>

<header id="tbay-header" class="site-header header-default header-v1 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">
	<div class="tbay-topbar clearfix hidden-xs">
		<div class="container">
			<div class="header-inner">
				<div class="row">
					<?php if(is_active_sidebar('top-contact')) : ?>
						<div class="top-contact col-md-4">
							<?php dynamic_sidebar('top-contact'); ?>
						</div><!-- End Top Contact Widget -->
					<?php endif;?>
					<!-- LOGO -->
					<div class="logo-in-theme col-md-4">
						<?php 
                        	puca_tbay_get_page_templates_parts('logo'); 
                        ?>
					</div>
					<div class="col-md-4 top-right">
						<?php puca_tbay_get_page_templates_parts('topbar-account'); ?>
					</div>	
				</div>
			</div>
		</div>
	</div>
	<div id="tbay-mainmenu" class="tbay-mainmenu">
		<div class="container">
			<?php puca_tbay_get_page_templates_parts('nav'); ?>
		</div>
	</div>
	<div class="tbay-aftermenu">
		<div class="container">
			<div class="row">

				<?php if( $enable_categoires ) : ?>
					<div class="col-md-3 sticky-off">
						<?php puca_tbay_get_page_templates_parts('categorymenu'); ?>
					</div>
				<?php endif; ?>

				<div class="col-md-<?php echo esc_attr( $class_search ); ?> sticky-off">
					<div class="search">
					<?php puca_tbay_get_page_templates_parts( 'productsearchform'); ?>
					</div>
				</div>
				<div class="col-md-3 sticky-pos">
					<div class="header-setting clearfix">
					<?php if ( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED ): ?>
						
						<div class="top-cart">
							<?php puca_tbay_get_woocommerce_mini_cart(); ?>
						</div>	
						<div class="top-wishlist">
							<?php puca_tbay_get_page_templates_parts( 'wishlist' ); ?>
						</div>
					<?php endif; ?>
					
					<?php puca_tbay_get_page_templates_parts('menu-account'); ?>
				</div>

			</div>
		</div>		
	</div>
</header>