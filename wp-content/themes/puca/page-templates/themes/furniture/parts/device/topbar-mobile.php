<?php   
	global $woocommerce; 
	$_id = puca_tbay_random_key();
?>
<div class="topbar-device-mobile visible-xxs clearfix">
	<?php
		$mobilelogo 	= puca_tbay_get_config('mobile-logo');
		$active_theme 	= puca_tbay_get_theme(); 

		$logo_all_page 	= puca_tbay_get_config('logo_all_page', false);
	?>
	<?php if( puca_tbay_is_home_page() || $logo_all_page) : ?>
		<div class="active-mobile">

		<?php echo apply_filters( 'puca_get_menu_mobile_icon', 10,2 ); ?>

		</div> 
		<div class="mobile-logo">
			<?php if( isset($mobilelogo['url']) && !empty($mobilelogo['url']) ): ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<img src="<?php echo esc_url( $mobilelogo['url'] ); ?>" alt="<?php bloginfo( 'name' ); ?>">
				</a>
			<?php else: ?>
				<div class="logo-theme">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<img src="<?php echo esc_url_raw( get_template_directory_uri().'/images/'.$active_theme.'/mobile-logo.png'); ?>" alt="<?php bloginfo( 'name' ); ?>">
					</a>
				</div>
			<?php endif; ?>
		</div>
		
		<div class="search-device-mobile visible-xxs">
            <span class="show-search"><i class="icon-magnifier icons"></i></span>
            <?php puca_tbay_get_page_templates_parts('productsearchform','mobile'); ?>
        </div>

	<?php else: ?>

	<div class="topbar-post">
		<div class="active-mobile">
			<?php echo apply_filters( 'puca_get_menu_mobile_icon', 10,2 ); ?>
		</div> 

		<div class="topbar-title">
			<?php $title = apply_filters( 'puca_get_filter_title_mobile', 10,2 ); ?>
			<?php echo trim($title);?>
		</div>

		<div class="search-device-mobile visible-xxs">
            <span class="show-search"><i class="icon-magnifier icons"></i></span>
            <?php puca_tbay_get_page_templates_parts('productsearchform','mobile'); ?>
        </div>

		</div>
	<?php endif; ?>

</div>
