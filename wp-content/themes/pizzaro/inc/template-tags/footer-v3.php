<?php
/**
 * Template Tags for Footer v3
 */

if ( ! function_exists( 'pizzaro_footer_menu' ) ) {
	/**
	 * Displays a footer menu
	 */
	function pizzaro_footer_menu() {
		wp_nav_menu( array(
			'theme_location'	=> 'footer',
			'container'			=> false,
			'menu_class'		=> 'footer-menu',
		) );
	}
}

if ( ! function_exists( 'pizzaro_payment_icons' ) ) {
	/**
	 * Prints Footer Payment Icons
	 */
	function pizzaro_payment_icons() {
		$payment_icons = apply_filters( 'pizzaro_footer_payment_icons_args', array(
			'paypal' => array(
				'icon' 		 => true,
				'icon_class' => 'fa fa-cc-paypal',
			),
			'visa' => array(
				'icon' 		 => true,
				'icon_class' => 'fa fa-cc-visa',
			),
			'mastercard' => array(
				'icon' 		 => true,
				'icon_class' => 'fa fa-cc-mastercard',
			),
		) );
		if ( apply_filters( 'pizzaro_show_footer_payment_icons', true ) && ! empty( $payment_icons ) ) : ?>
		<div class="footer-payment-icons"><?php if( ! empty( $payment_icons ) ) : 
			?><ul class="list-payment-icons"><?php foreach( $payment_icons as $key => $payment_icon ) : 
				?><li><?php if( isset( $payment_icon['icon'] ) && $payment_icon['icon'] ) : 
					?><i class="<?php echo esc_attr( $payment_icon['icon_class'] ); ?>"></i><?php elseif( isset( $payment_icon['image'] ) && $payment_icon['image'] ) : 
					?><img src="<?php echo esc_url( $payment_icon['img_src'] ); ?>" alt="<?php echo esc_attr( $key ); ?>" /><?php endif ; ?>
				</li><?php endforeach; ?>
			</ul><?php endif; ?>
		</div>
		<?php endif;
	}
}

if ( ! function_exists( 'pizzaro_footer_row_start' ) ) {
	/**
	 * Prints Footer row start
	 */
	function pizzaro_footer_row_start() {
		?><div class="footer-row row vertical-align"><?php
	}
}

if ( ! function_exists( 'pizzaro_footer_row_close' ) ) {
	/**
	 * Prints close of footer row
	 */
	function pizzaro_footer_row_close() {
		?></div><!-- /.footer-row --><?php
	}
}