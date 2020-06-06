<?php
if ( ! function_exists( 'mycred_render_woo_product_referral_link' ) ) :
	function mycred_render_woo_product_referral_link( $atts, $content = '' ) {
		extract( shortcode_atts( array(
			'type' => MYCRED_DEFAULT_TYPE_KEY
		), $atts,   'mycred_woocommerce_referral' ) );

                
		return apply_filters( 'mycred_woocommerce_referral_' . $type, '', $atts, $content );

	}
endif;
add_shortcode('mycred_woocommerce_referral', 'mycred_render_woo_product_referral_link' );

