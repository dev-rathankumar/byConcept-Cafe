<?php

class WPML_PP_Advanced_Tabs extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'tab_features';
	}

	public function get_fields() {
		return array( 
			'tab_title',
			'content',
			'link_video',
		);
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'tab_title':
				return esc_html__( 'Advanced Tabs - Item Title', 'powerpack' );
			case 'content':
				return esc_html__( 'Advanced Tabs - Item Content', 'powerpack' );
			case 'link_video':
				return esc_html__( 'Advanced Tabs - Video Link', 'powerpack' );
			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'tab_title':
				return 'LINE';
			case 'content':
				return 'VISUAL';
			case 'link_video':
				return 'LINE';
			default:
				return '';
		}
	}

}
