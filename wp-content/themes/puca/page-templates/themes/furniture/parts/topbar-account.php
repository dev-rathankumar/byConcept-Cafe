<?php if( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && has_nav_menu( 'nav-account' )) : ?>

	<div class="tbay-login">

		<?php if (is_user_logged_in() ) { ?>
			<?php $current_user = wp_get_current_user(); ?>
			<div class="dropdown">
				<a class="account-button" href="javascript:void(0);"><i class="icon-lock-open"></i><span class="hidden-xs"><?php esc_html_e('Welcome ','puca'); ?><?php echo esc_html( $current_user->display_name); ?>!</span></a>
				<div class="account-menu">
				<?php if ( has_nav_menu( 'nav-account' ) ) { ?>
					<?php
					$args = array(
						'theme_location'  => 'nav-account',
						'container_class' => '',
						'menu_class'      => 'menu-topbar'
					);
					wp_nav_menu($args);
					?>
				<?php } ?>
				</div>
			</div>
		<?php } elseif( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED && !empty(get_option('woocommerce_myaccount_page_id')) ) { ?>  
				<a href="<?php echo esc_url( get_permalink( get_option('woocommerce_myaccount_page_id') ) ); ?>" title="<?php esc_html_e('Login / Sign up','puca'); ?>"><i class="icon-lock"></i><span><?php esc_html_e('Login / Sign up', 'puca'); ?></span></a>          	
		<?php } ?> 

	</div>
	
<?php endif; ?> 