<?php if( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED)) : ?>
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
	<ul class="list-inline account">
		<?php if( !is_user_logged_in() ){ ?>
			<li> <a href="<?php echo esc_url( get_permalink( get_option('woocommerce_myaccount_page_id') ) ); ?>" title="<?php esc_html_e('Login','puca'); ?>"><?php esc_html_e('Login', 'puca'); ?> </a></li>
		<?php }else{ ?>
		<?php $current_user = wp_get_current_user(); ?>
		  <li><a href="<?php echo wp_logout_url(home_url()); ?>"><?php esc_html_e('Logout ','puca'); ?></a></li>
		<?php } ?>
	</ul>
	
<?php endif; ?> 