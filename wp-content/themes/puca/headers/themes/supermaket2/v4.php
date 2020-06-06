<?php 
	$enable_categoires 	= puca_tbay_get_config('enable_categoires', true);
?>
<header id="tbay-header" class="site-header header-v4 hidden-sm hidden-xs <?php echo (puca_tbay_get_config('keep_header', false) ? 'main-sticky-header' : ''); ?>">
	<div id="tbay-topbar" class="tbay-topbar hidden-sm hidden-xs clearfix">
		<?php if(is_active_sidebar('top-shipping')) : ?>
		<div class="pull-left">
			<?php dynamic_sidebar('top-shipping'); ?>
		</div><!-- End Top Shipping Widget -->
		<?php endif;?>
		<div class="pull-right top-right">
			<?php puca_tbay_get_page_templates_parts( 'topmenu' ); ?>
			<?php puca_tbay_get_page_templates_parts( 'topbar-account' ); ?>
		</div>
	</div>

  <div class="header-main">

	  <div class="header-inner clearfix">
			<div class="col-md-3">
				<div class="logo-in-theme">
					<?php if( $enable_categoires ) : ?>
						<?php puca_tbay_get_page_templates_parts( 'categorymenuimg', 'v4' ); ?>
					<?php endif; ?>
					<?php puca_tbay_get_page_templates_parts( 'logo', '02' ); ?>
				</div>
			</div>
			<div class="col-md-6 mainmenu">
				<?php puca_tbay_get_page_templates_parts( 'nav'); ?>
			</div>

			<div class="col-md-3 hidden-sm hidden-xs no-padding">
		
				<div class="content clearfix">
					<div class="header-setting">
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
					<?php puca_tbay_get_page_templates_parts('search-modal','totop'); ?>
				</div>

			</div>
		</div>
	</div>

	<div class="search-totop-content-wrapper">
		<div class="search-totop-content">
			<?php puca_tbay_get_page_templates_parts( 'productsearchform', 'full'); ?>
		</div>
	</div>

</header>