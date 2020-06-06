<?php

class WPML_PP_Card_Slider extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'items';
	}

	public function get_fields() {
		return array( 
			'card_date',
			'card_title',
			'card_content',
			'link' => array( 'url' ),
		);
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'card_date':
				return esc_html__( 'Card Slider - Discount', 'powerpack' );
			case 'card_title':
				return esc_html__( 'Card Slider - Card Title', 'powerpack' );
			case 'card_content':
				return esc_html__( 'Card Slider - Card Content', 'powerpack' );
			case 'url':
				return esc_html__( 'Card Slider - Card Link', 'powerpack' );
			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'card_date':
				return 'LINE';
			case 'card_title':
				return 'LINE';
			case 'card_content':
				return 'VISUAL';
			case 'url':
				return 'LINE';
			default:
				return '';
		}
	}

}
