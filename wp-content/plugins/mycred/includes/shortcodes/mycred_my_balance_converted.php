<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED Shortcode: my_balance_converted
 * Returns the current users balance.
 * @see http://codex.mycred.me/shortcodes/mycred_my_balance_converted/
 * @since 1.8.6
 * @version 1.0
 */
if ( ! function_exists( 'mycred_render_shortcode_my_balance_converted' ) ) :
	function mycred_render_shortcode_my_balance_converted( $atts, $content = '' ) {

		extract( shortcode_atts( array(
			'ctype'  => MYCRED_DEFAULT_TYPE_KEY,
			'rate'   => 1,
			'prefix' => '',
			'suffix' => ''
		), $atts, MYCRED_SLUG . '_my_balance_converted' ) );

		$output = '';

		// Not logged in
		if ( ! is_user_logged_in() )
			return $content;

		// Get user ID
		$user_id = mycred_get_user_id( get_current_user_id() );

		// Make sure we have a valid point type
		if ( ! mycred_point_type_exists( $ctype ) )
			$ctype = MYCRED_DEFAULT_TYPE_KEY;

		// Get the users myCRED account object
		$account = mycred_get_account( $user_id );
		if ( $account === false ) return;

		// Check for exclusion
		if ( empty( $account->balance ) || ! array_key_exists( $ctype, $account->balance ) || $account->balance[ $ctype ] === false ) return;

		$balance = $account->balance[ $ctype ];

		$output = '<div class="mycred-my-balance-converted-wrapper">';

		if ( ! empty( $prefix ) )
			$output .= '<span class="mycred-my-balance-converted-prefix">'.$prefix.'</span>';

		if( floatval( $rate ) == 0 ) $rate = 1;

		$output .= floatval( $balance->current ) * floatval( $rate );

		if ( ! empty( $suffix ) )
			$output .= '<span class="mycred-my-balance-converted-suffix">'.$suffix.'</span>';


		$output .= '</div>';

		return $output;

	}
endif;
add_shortcode( MYCRED_SLUG . '_my_balance_converted', 'mycred_render_shortcode_my_balance_converted' );
