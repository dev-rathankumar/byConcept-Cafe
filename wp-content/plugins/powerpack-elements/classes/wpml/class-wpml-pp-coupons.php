<?php

class WPML_PP_Coupons extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'pp_coupons';
	}

	public function get_fields() {
		return array( 
			'discount',
			'coupon_code',
			'title',
			'description',
			'link' => array( 'url' ),
		);
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'discount':
				return esc_html__( 'Coupons - Discount', 'powerpack' );
			case 'coupon_code':
				return esc_html__( 'Coupons - Coupon Code', 'powerpack' );
			case 'title':
				return esc_html__( 'Coupons - Coupon Title', 'powerpack' );
			case 'description':
				return esc_html__( 'Coupons - Coupon Description', 'powerpack' );
			case 'url':
				return esc_html__( 'Coupons - Coupon Link', 'powerpack' );
			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'discount':
				return 'LINE';
			case 'coupon_code':
				return 'LINE';
			case 'title':
				return 'LINE';
			case 'description':
				return 'VISUAL';
			case 'url':
				return 'LINE';
			default:
				return '';
		}
	}

}
