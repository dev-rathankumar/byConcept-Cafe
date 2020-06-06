<?php
/**
 * Filter functions for Footer Section of Theme Options
 */

if ( ! function_exists( 'redux_apply_footer_style' ) ) {
	function redux_apply_footer_style( $footer_style ) {
		global $pizzaro_options;

		if( ! empty( $pizzaro_options['footer_style'] ) ) {
			$footer_style = $pizzaro_options['footer_style'];
		}

		return $footer_style;
	}
}

if( ! function_exists( 'redux_apply_footer_static_block_id' ) ) {
	function redux_apply_footer_static_block_id( $static_block_id ) {
		global $pizzaro_options;

		if( isset( $pizzaro_options['footer_static_block_id'] ) ) {
			$static_block_id = $pizzaro_options['footer_static_block_id'];
		}

		return $static_block_id;
	}
}

if ( ! function_exists( 'redux_apply_footer_store_timings' ) ) {
	function redux_apply_footer_store_timings( $store_timings ) {
		global $pizzaro_options;

		if( ! empty( $pizzaro_options['footer_store_timings_labels'] ) && ! empty( $pizzaro_options['footer_store_timings_times'] ) ) {

			$timings = array();

			foreach( $pizzaro_options['footer_store_timings_labels'] as $key => $labels ) {
				if( ! empty( $pizzaro_options['footer_store_timings_times'][$key] ) ) {
					$timings[] = array(
						'label'  => $labels,
						'timing' => $pizzaro_options['footer_store_timings_times'][$key],
					);
				}
			}

			if( ! empty( $timings ) ) {
				$store_timings = $timings;
			}
		}

		return $store_timings;
	}
}

if ( ! function_exists( 'redux_apply_footer_contact_form_title' ) ) {
	function redux_apply_footer_contact_form_title( $title ) {
		global $pizzaro_options;

		if( isset( $pizzaro_options['footer_contact_form_title'] ) ) {
			$title = $pizzaro_options['footer_contact_form_title'];
		}

		return $title;
	}
}

if ( ! function_exists( 'redux_apply_footer_contact_form_content' ) ) {
	function redux_apply_footer_contact_form_content( $form ) {
		global $pizzaro_options;

		if( isset( $pizzaro_options['footer_contact_form_content'] ) && $pizzaro_options['footer_contact_form_content'] != '' ) {
			$form = $pizzaro_options['footer_contact_form_content'];
		}

		return $form;
	}
}

if ( ! function_exists( 'redux_apply_footer_contact_info_args' ) ) {
	function redux_apply_footer_contact_info_args( $contact_info ) {
		global $pizzaro_options;

		if( ! empty( $pizzaro_options['footer_contact_info_text'] ) ) {

			$info = array();

			foreach( $pizzaro_options['footer_contact_info_text'] as $key => $text ) {
				if( ! empty( $pizzaro_options['footer_contact_info_icon'][$key] ) ) {
					$info[] = array(
						'text' => $text,
						'icon' => $pizzaro_options['footer_contact_info_icon'][$key],
					);
				} else {
					$info[] = array(
						'text' => $text,
						'icon' => '',
					);
				}
			}

			if( ! empty( $info ) ) {
				$contact_info = $info;
			}
		}

		return $contact_info;
	}
}

if ( ! function_exists( 'redux_apply_footer_site_address' ) ) {
	function redux_apply_footer_site_address( $site_address ) {
		global $pizzaro_options;

		if( ! empty( $pizzaro_options['footer_site_address_text'] ) ) {
			$site_address = $pizzaro_options['footer_site_address_text'];
		}

		return $site_address;
	}
}

if ( ! function_exists( 'redux_apply_footer_payment_icons' ) ) {
	function redux_apply_footer_payment_icons( $payment_icons ) {
		global $pizzaro_options;

		if( ! empty( $pizzaro_options['footer_payment_icons'] ) ) {

			$icons = array();
			
			foreach( $pizzaro_options['footer_payment_icons'] as $key => $icon ) {
				$icons[] = array( 'icon' => true, 'icon_class' => $icon );
			}

			if( ! empty( $icons ) ) {
				$payment_icons = $icons;
			}
		}

		return $payment_icons;
	}
}

if ( ! function_exists( 'redux_apply_footer_about_info_args' ) ) {
	function redux_apply_footer_about_info_args( $args ) {
		global $pizzaro_options;

		if ( ! empty( $pizzaro_options['footer_about_image']['url'] ) ) {
			$args['img_src'] = $pizzaro_options['footer_about_image']['url'];
		}

		if( isset( $pizzaro_options['footer_about_title'] ) ) {
			$args['title'] = $pizzaro_options['footer_about_title'];
		}

		if( isset( $pizzaro_options['footer_about_description'] ) ) {
			$args['description'] = $pizzaro_options['footer_about_description'];
		}

		if( isset( $pizzaro_options['footer_about_button_text'] ) ) {
			$args['button_text'] = $pizzaro_options['footer_about_button_text'];
		}

		if( isset( $pizzaro_options['footer_about_button_link'] ) ) {
			$args['button_link'] = $pizzaro_options['footer_about_button_link'];
		}

		return $args;
	}
}

if ( ! function_exists( 'redux_apply_footer_action_button_args' ) ) {
	function redux_apply_footer_action_button_args( $args ) {
		global $pizzaro_options;

		if( isset( $pizzaro_options['footer_action_button_text'] ) ) {
			$args['text'] = $pizzaro_options['footer_action_button_text'];
		}

		if( isset( $pizzaro_options['footer_action_icon_class'] ) ) {
			$args['icon'] = $pizzaro_options['footer_action_icon_class'];
		}

		return $args;
	}
}

if ( ! function_exists( 'redux_apply_footer_copyright_text' ) ) {
	function redux_apply_footer_copyright_text( $text ) {
		global $pizzaro_options;

		if( isset( $pizzaro_options['footer_credit'] ) ) {
			$text = $pizzaro_options['footer_credit'];
		}

		return $text;
	}
}