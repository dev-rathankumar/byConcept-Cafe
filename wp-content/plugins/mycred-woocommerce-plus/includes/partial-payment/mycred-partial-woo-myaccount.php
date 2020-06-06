<?php
// No dirrect access
if ( ! defined( 'MYCRED_WOOPLUS_VERSION' ) ) exit;

/**
 * Setup Custom Endpoint
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_woo_partial_setup_my_account' ) ) :
	function mycred_woo_partial_setup_my_account( $flush = false ) {

		$my_account_setup = mycred_part_woo_account_settings();
		if ( $my_account_setup['slug'] == '' ) return;

		add_rewrite_endpoint( $my_account_setup['slug'], EP_ROOT | EP_PAGES );

		if ( $flush ) {
			flush_rewrite_rules();
			return;
		}

		add_filter( 'woocommerce_account_menu_items',                                 'mycred_woo_partial_account_menu' );
		add_action( 'woocommerce_account_' . $my_account_setup['slug'] . '_endpoint', 'mycred_woo_partial_account_menu_content' );
		add_filter( 'the_title',                                                      'mycred_woo_partial_account_title' );

	}
endif;
add_action( 'mycred_init', 'mycred_woo_partial_setup_my_account' );

/**
 * Add My Account Menu
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_woo_partial_account_menu' ) ) :
	function mycred_woo_partial_account_menu( $items ) {

		$my_account_setup = mycred_part_woo_account_settings();
		if ( $my_account_setup['slug'] == '' ) return $items;

		// Remove the logout menu item.
		$logout = $items['customer-logout'];
		unset( $items['customer-logout'] );

		// Insert your custom endpoint.
		$items[ $my_account_setup['slug'] ] = $my_account_setup['title'];

		// Insert back the logout item.
		$items['customer-logout'] = $logout;

		return $items;

	}
endif;

/**
 * Add My Account Menu Content
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_woo_partial_account_menu_content' ) ) :
	function mycred_woo_partial_account_menu_content() {

		$my_account_setup = mycred_part_woo_account_settings();
		if ( $my_account_setup['slug'] == '' ) return;

		$args = array(
			'user_id'  => get_current_user_id(),
			'number'   => $my_account_setup['number'],
			'page_arg' => 'sheet'
		);

		$reward_ref = apply_filters( 'mycred_woo_reward_reference', 'reward', 0, MYCRED_DEFAULT_TYPE_KEY );
		$references = apply_filters( 'mycred_woo_references', array( 'partial_payment', 'partial_payment_refund', 'woocommerce_payment', 'store_sale', 'woocommerce_refund', 'store_sale_refund', $reward_ref ) );

		if ( $my_account_setup['show'] == 'store' )
			$args['ref'] = array(
				'ids'     => $references,
				'compare' => 'IN'
			);

		if ( $my_account_setup['show'] != '' )
			$args['ref'] = $my_account_setup['show'];

		$account_history  = new myCRED_Query_Log( apply_filters( 'mycred_woo_account_args', $args ) );

		echo '<style type="text/css">.mycred-history-wrapper ul li { list-style-type: none; display: inline; padding: 0 6px; }</style>';

		if ( $my_account_setup['desc'] != '' )
			echo '<div id="account-history-description">' . wpautop( wptexturize( $my_account_setup['desc'] ) ) . '</div>';

?>
<div class="mycred-history-wrapper">
<form class="form-inline" role="form" method="get" action="">

	<?php if ( $my_account_setup['nav'] == 1 ) $account_history->front_navigation( 'top', 10 ); ?>

	<?php $account_history->display(); ?>

	<?php if ( $my_account_setup['nav'] == 1 ) $account_history->front_navigation( 'bottom', 10 ); ?>

</form>
</div>
<?php

	}
endif;

/**
 * Add My Account Menu Title
 * @since 1.0
 * @version 1.0
 */
if ( ! function_exists( 'mycred_woo_partial_account_title' ) ) :
	function mycred_woo_partial_account_title( $title ) {

		$my_account_setup = mycred_part_woo_account_settings();
		if ( $my_account_setup['slug'] == '' || ! function_exists( 'is_account_page' ) ) return $title;

		global $wp_query;

		$is_endpoint = array_key_exists( $my_account_setup['slug'], $wp_query->query_vars );

		if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {

			$title = $my_account_setup['title'];

			remove_filter( 'the_title', 'mycred_woo_partial_account_title' );

		}

		return $title;

	}
endif;
