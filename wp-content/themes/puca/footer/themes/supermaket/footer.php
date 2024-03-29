<?php

$footer 	= apply_filters( 'puca_tbay_get_footer_layout', 'default' );
$copyright 	= puca_tbay_get_config('copyright_text', '');

?>

	</div><!-- .site-content -->

	<?php if ( is_active_sidebar( 'newsletter-popup-sidebar' ) && in_array( 'mailchimp-for-wp/mailchimp-for-wp.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) : ?>
		<div id="newsletter-popup-sidebar" class="newsletter-popup-sidebar">
			<?php dynamic_sidebar( 'newsletter-popup-sidebar' ); ?>
		</div>
	<?php endif; ?>

	<footer id="tbay-footer" class="tbay-footer <?php echo (!empty($footer)) ? esc_attr($footer) : ''; ?>">
		<?php if ( !empty($footer) ): ?>
			
			<div class="footer">
				<div class="container">
					<?php puca_tbay_display_footer_builder($footer); ?>
				</div>
			</div>

		<?php else: ?>
			<?php if ( is_active_sidebar( 'footer' ) ) : ?>
				<div class="footer">
					<div class="container">
						<div class="row">
							<?php dynamic_sidebar( 'footer' ); ?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if( !empty($copyright) ) : ?>
				<div class="tbay-copyright">
					<div class="container">
						<div class="copyright-content">
							<div class="text-copyright text-center">
							
								<?php echo trim($copyright); ?>

							</div> 
						</div>
					</div>
				</div>

			<?php else: ?>
				<div class="tbay-copyright">
					<div class="container">
						<div class="copyright-content">
							<div class="text-copyright text-center">
							<?php
									$allowed_html_array = array( 'a' => array('href' => array() ) );
									echo wp_kses(__('Copyright &copy; 2019 - puca. All Rights Reserved. <br/> Powered by <a href="//thembay.com">ThemBay</a>', 'puca'), $allowed_html_array);
								
							?>

							</div> 
						</div>
					</div>
				</div>

			<?php endif; ?>	 

			
		<?php endif; ?>			
	</footer><!-- .site-footer -->

	<?php $tbay_header = apply_filters( 'puca_tbay_get_header_layout', puca_tbay_get_config('header_type', 'v1') ); ?>
	
	<?php 

	$_id = puca_tbay_random_key();

	?>

	<?php
	if ( puca_tbay_get_config('back_to_top') ) { ?>
		<div class="tbay-to-top <?php echo esc_attr($tbay_header); ?>">

			<div class="more-to-top">
				<?php if ( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED ): ?>
				<div class="device-account <?php echo is_account_page() ? 'active' : '' ?>">
					<a href="<?php echo esc_url( get_permalink( get_option('woocommerce_myaccount_page_id') ) ); ?>" title="<?php esc_html_e('Login','puca'); ?>">
						<i class="icon-user icons"></i>
					</a>
				</div>
				<?php endif; ?>
				<?php if( class_exists( 'YITH_WCWL' ) ) { ?>
				<a class="text-skin wishlist-icon" href="<?php $wishlist_url = YITH_WCWL()->get_wishlist_url(); echo esc_url($wishlist_url); ?>"><i class="icon-heart icons"></i><span class="count_wishlist"><?php $wishlist_count = YITH_WCWL()->count_products(); echo esc_html($wishlist_count); ?></span></a>
				<?php } ?>
				
				
				<?php if ( !(defined('PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && PUCA_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('PUCA_WOOCOMMERCE_ACTIVED') && PUCA_WOOCOMMERCE_ACTIVED ): ?>
				<!-- Setting -->
				<div class="tbay-cart top-cart hidden-xs">
					<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="mini-cart">
						<i class="icon-bag icons"></i>
						<span class="mini-cart-items-fixed">
						<?php echo sprintf( '%d', WC()->cart->cart_contents_count );?>
						</span>
					</a>
				</div>
				<?php endif; ?>
			</div>
			
			<a href="javascript:void(0);" id="back-to-top">
				<i class="icon-arrow-up-circle icons"></i>
			</a>
		</div>
	<?php
	}
	if ( puca_tbay_get_config('category_fixed') ) { ?>
		<?php if ( has_nav_menu( 'nav-category-img' ) ): 
		$_id = puca_tbay_random_key();
		?>
		<div class="tbay-category-fixed">
			 <nav class="tbay-topmenu" role="navigation">
				<?php
					$args = array(
						'theme_location' => 'nav-category-img',
						'menu_class' => 'tbay-menu-category list-inline',
						'fallback_cb' => '',
						'menu_id' => 'nav-category-img'.$_id,
						'walker' => new Puca_Tbay_Nav_Menu()
					);
					wp_nav_menu($args);
				?>
			</nav>
		</div><!-- End Category Menu -->
		<?php endif;?>
		
		
	<?php
	}
	?>

	<?php
	if ( puca_tbay_get_config('mobile_back_to_top') ) { ?>
		<div class="tbay-to-top-mobile tbay-to-top <?php echo esc_attr($tbay_header); ?>">

			<div class="more-to-top">
			
				<a href="javascript:void(0);" id="back-to-top-mobile">
					<i class="icon-arrow-up"></i>
				</a>
			</div>
		</div>
		
		
	<?php
	}
	?>
	
	

</div><!-- .site -->

<?php wp_footer(); ?>

</body>
</html>

