<?php
 
//license system
add_filter( 'pre_set_site_transient_update_plugins', 'check_for_plugin_update_mycred_woo_plus' , 80 );
/**
 * Plugin Update Check
 * @since 1.0
 * @version 1.1
 */
 
	if( get_option( 'mycred-premium-mycred-partial-woo-expires' ) ){
		
		$MYCRED_WOOPLUS_SLUG 	=  'mycredpartwoo';
		$MYCRED_WOOPLUS_VERSION =  '1.5.1';
	
	}else{
		
		$MYCRED_WOOPLUS_SLUG 	=  MYCRED_WOOPLUS_SLUG;
		$MYCRED_WOOPLUS_VERSION =  MYCRED_WOOPLUS_VERSION;
	
	}
	
 
function check_for_plugin_update_mycred_woo_plus( $checked_data ) {
	
	if( get_option( 'mycred-premium-mycred-partial-woo-expires' ) ){
		
		$MYCRED_WOOPLUS_SLUG 	=  'mycredpartwoo';
		$MYCRED_WOOPLUS_VERSION =  '1.5.1';
	
	}else{
		
		$MYCRED_WOOPLUS_SLUG 	=  MYCRED_WOOPLUS_SLUG;
		$MYCRED_WOOPLUS_VERSION =  MYCRED_WOOPLUS_VERSION;
	
	}

	global $wp_version;

	if ( empty( $checked_data->checked ) )
		return $checked_data;

	$args = array(
		'slug'    => $MYCRED_WOOPLUS_SLUG,
		'version' => $MYCRED_WOOPLUS_VERSION,
		'site'    => site_url()
	);
	$request_string = array(
		'body'       => array(
			'action'     => 'version', 
			'request'    => serialize( $args ),
			'api-key'    => md5( get_bloginfo( 'url' ) )
		),
		'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
	);
	
	// Start checking for an update
	$response = wp_remote_post( 'http://mycred.me/api/plugins/', $request_string );

	if ( ! is_wp_error( $response ) ) {

		$result = maybe_unserialize( $response['body'] );

		if ( is_object( $result ) && ! empty( $result ) )
			$checked_data->response[ MYCRED_WOOPLUS_SLUG . '/' . MYCRED_WOOPLUS_SLUG . '.php' ] = $result;

	}

	return $checked_data;

}

add_filter( 'plugins_api', 'plugin_api_call_mycred_woo_plus', 80, 3 );

/**
 * Plugin New Version Update
 * @since 1.0
 * @version 1.1
 */
function plugin_api_call_mycred_woo_plus( $result, $action, $args ) {
	
	if( get_option( 'mycred-premium-mycred-partial-woo-expires' ) ){
		
		$MYCRED_WOOPLUS_SLUG 	=  'mycredpartwoo';
		$MYCRED_WOOPLUS_VERSION =  '1.5.1';
	
	}else{
		
		$MYCRED_WOOPLUS_SLUG 	=  MYCRED_WOOPLUS_SLUG;
		$MYCRED_WOOPLUS_VERSION =  MYCRED_WOOPLUS_VERSION;
	
	}
  
	global $wp_version;

	if ( ! isset( $args->slug ) || ( $args->slug != $MYCRED_WOOPLUS_SLUG ) )
		return $result;

	// Get the current version
	$args = array(
		'slug'    => $MYCRED_WOOPLUS_SLUG,
		'version' => $MYCRED_WOOPLUS_VERSION,
		'site'    => site_url()
	);
	 
	$request_string = array(
		'body'       => array(
			'action'     => 'info', 
			'request'    => serialize( $args ),
			'api-key'    => md5( get_bloginfo( 'url' ) )
		),
		'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
	);
	

	$request = wp_remote_post( 'http://mycred.me/api/plugins/', $request_string );

	if ( ! is_wp_error( $request ) )
		$result = maybe_unserialize( $request['body'] );

	if ( ! empty( $result->license_expires ) )
		update_option( 'mycred-premium-' . $MYCRED_WOOPLUS_SLUG . '-expires', $result->license_expires );

	if ( ! empty( $result->license_renew ) )
		update_option( 'mycred-premium-' . $MYCRED_WOOPLUS_SLUG . '-renew',   $result->license_renew );

	return $result;

}

add_filter( 'plugin_row_meta', 'plugin_view_info_mycred_woo_plus' , 80, 3 );

/**
 * Plugin View Info
 * @since 1.1
 * @version 1.0
 */
function plugin_view_info_mycred_woo_plus( $plugin_meta, $file, $plugin_data ) {
	
	if( get_option( 'mycred-premium-mycred-partial-woo-expires' ) ){
		
		$MYCRED_WOOPLUS_SLUG 	=  'mycred-partial-woo';
		$MYCRED_WOOPLUS_VERSION =  '1.5.1';
	
	}else{
		
		$MYCRED_WOOPLUS_SLUG 	=  MYCRED_WOOPLUS_SLUG;
		$MYCRED_WOOPLUS_VERSION =  MYCRED_WOOPLUS_VERSION;
	
	}

	if ( $file != plugin_basename( MYCRED_WOOPLUS_THIS ) ) return $plugin_meta;

	$plugin_meta[] = sprintf( '<a href="%s" class="thickbox" aria-label="%s" data-title="%s">%s</a>',
		esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $MYCRED_WOOPLUS_SLUG .
		'&TB_iframe=true&width=600&height=550' ) ),
		esc_attr( __( 'More information about this plugin', 'mycredpartwoo' ) ),
		esc_attr( 'myCRED WooPlus' ),
		__( 'View details', 'mycredpartwoo' )
	);

	$url     = str_replace( array( 'https://', 'http://' ), '', get_bloginfo( 'url' ) );
	$expires = get_option( 'mycred-premium-' . $MYCRED_WOOPLUS_SLUG . '-expires', '' );
	
	
	if(empty($expires)){
		$args = new stdClass;
		$args->slug = $MYCRED_WOOPLUS_SLUG;
		$args->version = $MYCRED_WOOPLUS_VERSION;
		$args->site = site_url();
		
		$action = '';
		$result = '';   
		plugin_api_call_mycred_woo_plus( $result, $action, $args );
		
		$expires = get_option( 'mycred-premium-' . $MYCRED_WOOPLUS_SLUG . '-expires', '' );
	}
	
	if ( $expires != '' ) {

		if ( $expires == 'never' )
			$plugin_meta[] = 'Unlimited License';

		elseif ( absint( $expires ) > 0 ) {

			$days = ceil( ( $expires - current_time( 'timestamp' ) ) / DAY_IN_SECONDS );
			if ( $days > 0 )
				$plugin_meta[] = sprintf(
					'License Expires in <strong%s>%s</strong>',
					( ( $days < 30 ) ? ' style="color:red;"' : '' ),
					sprintf( _n( '1 day', '%d days', $days ), $days )
				);

			$renew = get_option( 'mycred-premium-' . $MYCRED_WOOPLUS_SLUG . '-renew', '' );
			if ( $days < 30 && $renew != '' )
				$plugin_meta[] = '<a href="' . esc_url( $renew ) . '" target="_blank" class="">Renew License</a>';

		}

	}

	else $plugin_meta[] = '<a href="http://mycred.me/about/terms/#product-licenses" target="_blank">No license found for - ' . $url . '</a>';

	return $plugin_meta;

}