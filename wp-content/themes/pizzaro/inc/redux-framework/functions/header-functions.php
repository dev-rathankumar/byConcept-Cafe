<?php
/**
 * Filter functions for Header Section of Theme Options
 */

if ( ! function_exists( 'redux_apply_header_style' ) ) {
	function redux_apply_header_style( $header_style ) {
		global $pizzaro_options;

		if( ! empty( $pizzaro_options['header_style'] ) ) {
			$header_style = $pizzaro_options['header_style'];
		}

		return $header_style;
	}
}

if ( ! function_exists( 'redux_apply_header_background' ) ) {
	function redux_apply_header_background( $header_background ) {
		global $pizzaro_options;

		if( isset( $pizzaro_options['header_background'] ) ) {
			$header_background = $pizzaro_options['header_background'];
		}

		return $header_background;
	}
}

if( ! function_exists( 'redux_toggle_sticky_header' ) ) {
	function redux_toggle_sticky_header() {
		global $pizzaro_options;

		if( isset( $pizzaro_options['sticky_header'] ) && $pizzaro_options['sticky_header'] == '1' ) {
			$sticky_header = true;
		} else {
			$sticky_header = false;
		}

		return $sticky_header;
	}
}

if( ! function_exists( 'redux_toggle_header_navigation_link' ) ) {
	function redux_toggle_header_navigation_link() {
		global $pizzaro_options;

		if( isset( $pizzaro_options['show_header_nav_links'] ) && $pizzaro_options['show_header_nav_links'] == '1' ) {
			$header_nav_links = true;
		} else {
			$header_nav_links = false;
		}

		return $header_nav_links;
	}
}

if( ! function_exists( 'redux_toggle_header_cart' ) ) {
	function redux_toggle_header_cart() {
		global $pizzaro_options;

		if( isset( $pizzaro_options['show_header_cart'] ) && $pizzaro_options['show_header_cart'] == '1' ) {
			$header_cart = true;
		} else {
			$header_cart = false;
		}

		return $header_cart;
	}
}

if ( ! function_exists( 'redux_apply_header_cart_v2_args' ) ) {
	function redux_apply_header_cart_v2_args( $args ) {
		global $pizzaro_options;

		if( isset( $pizzaro_options['header_cart_v2_icon'] ) ) {
			$args['icon'] = $pizzaro_options['header_cart_v2_icon'];
		}

		if( isset( $pizzaro_options['header_cart_v2_label'] ) ) {
			$args['label'] = $pizzaro_options['header_cart_v2_label'];
		}

		if( isset( $pizzaro_options['header_cart_v2_empty_label'] ) ) {
			$args['empty_label'] = $pizzaro_options['header_cart_v2_empty_label'];
		}

		return $args;
	}
}

if( ! function_exists( 'redux_toggle_header_phone_numbers' ) ) {
	function redux_toggle_header_phone_numbers() {
		global $pizzaro_options;

		if( isset( $pizzaro_options['show_header_phone_numbers'] ) && $pizzaro_options['show_header_phone_numbers'] == '1' ) {
			$phone_numbers = true;
		} else {
			$phone_numbers = false;
		}

		return $phone_numbers;
	}
}

if ( ! function_exists( 'redux_apply_header_phone_args' ) ) {
	function redux_apply_header_phone_args( $phone_args ) {
		global $pizzaro_options;

		if( ! empty( $pizzaro_options['header_phone_city'] ) && ! empty( $pizzaro_options['header_phone_number'] ) ) {

			$phone_numbers = array();

			foreach( $pizzaro_options['header_phone_city'] as $key => $city ) {
				if( ! empty( $pizzaro_options['header_phone_number'][$key] ) ) {
					$phone_numbers[] = array(
						'city'  => $city,
						'number' => $pizzaro_options['header_phone_number'][$key],
					);
				}
			}

			if( ! empty( $phone_numbers ) ) {
				$phone_args['phone_numbers'] = $phone_numbers;
			}
		}

		if( isset( $pizzaro_options['header_phone_text'] ) ) {
			$phone_args['text'] = $pizzaro_options['header_phone_text'];
		}

		return $phone_args;
	}
}
