<?php if( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && has_nav_menu( 'nav-account' )) : ?>

	<div class="tbay-login">	
	
		<?php if (is_user_logged_in() ) { ?>
			<?php $current_user = wp_get_current_user(); ?>
			<div class="dropdown">
				<span class="account-button"><i class="icon-user icons"></i></span>
				<div class="account-menu">
				<?php if ( has_nav_menu( 'nav-account' ) ) { ?>
					<?php
					$args = array(
						'theme_location'  => 'nav-account',
						'container_class' => '',
						'menu_class'      => 'menu-topbar',
						'walker' => new Puca_Tbay_Nav_Menu()
					);
					wp_nav_menu($args);
					?>
				<?php } ?>
				</div>
				<span class="well-come"><?php esc_html_e('Hello,','puca'); ?></span>
				<span class="account-name"><?php echo esc_html( $current_user->display_name); ?></span>
			</div>
		<?php } elseif( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED && !empty(get_option('woocommerce_myaccount_page_id')) ) { ?>  
				<a href="<?php echo esc_url( get_permalink( get_option('woocommerce_myaccount_page_id') ) ); ?>" title="<?php esc_html_e('Sign me up','puca'); ?>">
					<span class="account-button"><i class="icon-user icons"></i></span>
					<span class="well-come"><?php esc_html_e('Hello,','puca'); ?></span>
					<span class="account-name"><?php esc_html_e('Sign me up','puca'); ?></span>
				</a>          	
		<?php } ?>
	</div>
	
<?php endif; ?> 