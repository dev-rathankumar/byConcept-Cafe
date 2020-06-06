<?php
/**
 * Filter functions for General Section of Theme Options
 */

if( ! function_exists( 'redux_toggle_logo_svg' ) ) {
	function redux_toggle_logo_svg() {
		global $pizzaro_options;

		if( isset( $pizzaro_options['logo_svg'] ) && $pizzaro_options['logo_svg'] == '1' ) {
			$logo_svg = true;
		} else {
			$logo_svg = false;
		}

		return $logo_svg;
	}
}

if( ! function_exists( 'redux_toggle_scrollup' ) ) {
	function redux_toggle_scrollup() {
		global $pizzaro_options;

		if( isset( $pizzaro_options['scrollup'] ) && $pizzaro_options['scrollup'] == '1' ) {
			$scrollup = true;
		} else {
			$scrollup = false;
		}

		return $scrollup;
	}
}

if ( ! function_exists( 'redux_apply_newsletter_form' ) ) {
	function redux_apply_newsletter_form( $form ) {
		global $pizzaro_options;

		if( isset( $pizzaro_options['newsletter_signup_form'] ) && $pizzaro_options['newsletter_signup_form'] != '' ) {
			$form = do_shortcode( $pizzaro_options['newsletter_signup_form'] );
		}

		return $form;
	}
}

if ( ! function_exists( 'redux_apply_map_content' ) ) {
	function redux_apply_map_content( $map_content ) {
		global $pizzaro_options;

		if( ! empty( $pizzaro_options['contact_map_content'] ) ) {
			$map_content = $pizzaro_options['contact_map_content'];
		}

		return $map_content;
	}
}