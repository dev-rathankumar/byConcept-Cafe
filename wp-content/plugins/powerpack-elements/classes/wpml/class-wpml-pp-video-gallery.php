<?php

class WPML_PP_Video_Gallery extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'gallery_videos';
	}

	public function get_fields() {
		return array( 
			'youtube_url',
			'vimeo_url',
			'dailymotion_url',
			'video_title',
			'filter_label',
		);
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'youtube_url':
				return esc_html__( 'Video Gallery - YouTube URL', 'powerpack' );
			case 'vimeo_url':
				return esc_html__( 'Video Gallery - Vimeo URL', 'powerpack' );
			case 'dailymotion_url':
				return esc_html__( 'Video Gallery - Dailymotion URL', 'powerpack' );
			case 'video_title':
				return esc_html__( 'Video Gallery - Video Title', 'powerpack' );
			case 'filter_label':
				return esc_html__( 'Video Gallery - Filter Label', 'powerpack' );
			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'youtube_url':
				return 'LINE';
			case 'vimeo_url':
				return 'LINE';
			case 'dailymotion_url':
				return 'LINE';
			case 'video_title':
				return 'LINE';
			case 'filter_label':
				return 'LINE';
			default:
				return '';
		}
	}

}
