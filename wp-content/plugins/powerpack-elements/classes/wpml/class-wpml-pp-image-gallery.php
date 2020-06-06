<?php

class WPML_PP_Image_Gallery extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'gallery_images';
	}

	public function get_fields() {
		return array( 
			'filter_label',
		);
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'filter_label':
				return esc_html__( 'Image Gallery - Filter Label', 'powerpack' );
			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'filter_label':
				return 'LINE';
			default:
				return '';
		}
	}

}
