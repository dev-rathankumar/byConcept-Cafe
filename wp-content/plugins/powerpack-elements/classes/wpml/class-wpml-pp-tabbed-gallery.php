<?php

class WPML_PP_Tabbed_Gallery extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'gallery_images';
	}

	public function get_fields() {
		return array( 
			'tab_label',
		);
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'tab_label':
				return esc_html__( 'Tabbed Gallery - Tab Label', 'powerpack' );
			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'tab_label':
				return 'LINE';
			default:
				return '';
		}
	}

}
