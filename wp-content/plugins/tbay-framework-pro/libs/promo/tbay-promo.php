<?php
/**
 * This file belongs to the Tbay Framework Pro.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if( ! function_exists( 'simplexml_load_string' ) ){
    return false;
}

 
add_action( 'admin_notices', 'tbay_plugin_regenerate_transient' );
add_action( 'admin_notices', 'tbay_plugin_promo_notices', 20 );
add_action( 'admin_enqueue_scripts', 'tbay_plugin_notice_dismiss', 25 );

if( ! function_exists( 'tbay_plugin_promo_notices' ) ){ 
	function tbay_plugin_promo_notices(){
		$xml                        = apply_filters( 'tbay_framework_plugin_promo_xml_url', 'https://bitbucket.org/devthembay/update-plugin/raw/master/promo/tbay-framework-promo.xml' );
		$transient                  = "tbay_promo_message";
		$remote_data                = get_site_transient( $transient );
		$regenerate_promo_transient = isset( $_GET['tbay_regenerate_promo_transient'] ) && 'yes' == $_GET['tbay_regenerate_promo_transient'] ? $_GET['tbay_regenerate_promo_transient'] : '';
		$promo_data                 = false;
		$create_transient           = false; 

		    $remote_data = wp_remote_get( $xml );
		    $create_transient = true;

		if ( ! is_wp_error( $remote_data ) && isset( $remote_data['response']['code'] ) && '200' == $remote_data['response']['code'] ) {
			$promo_data = @simplexml_load_string( $remote_data['body'] );

			if( true === $create_transient ){
			    $xml_expiry_date = ! empty( $promo_data->expiry_date ) ? $promo_data->expiry_date : '';
				//Set Site Transient
				set_site_transient( $transient, $remote_data, tbay_plugin_get_promo_transient_expiry_date( $xml_expiry_date ) );
            }

			if ( $promo_data && ! empty( $promo_data->promo ) ) {
			   $now = strtotime( current_time( 'Y-m-d' ), 1 );
			   foreach ($promo_data as $promo ){

				   $start_date = isset( $promo->start_date ) ? $promo->start_date : '';
				   $end_date   = isset( $promo->end_date ) ? $promo->end_date : '';

				   if( ! empty( $start_date ) && ! empty( $end_date ) ){
					   $start_date = strtotime( $start_date );
					   $end_date   = strtotime( $end_date );



					   if( $end_date >= $start_date && $now >= $start_date && $now <= $end_date ){
					       //is valid promo
						   $title            = isset( $promo->title ) ? $promo->title : '';
						   $description      = isset( $promo->description ) ? $promo->description : '';
						   $url              = isset( $promo->link->url ) ? $promo->link->url : '';
						   $url_label        = isset( $promo->link->label ) ? $promo->link->label : '';
						   $border_color     = isset( $promo->style->border_color ) ? $promo->style->border_color : '';
						   $background_color = isset( $promo->style->background_color ) ? $promo->style->background_color : '';
						   $promo_id         = isset( $promo->promo_id ) ? $promo->promo_id : '';
						   $style = $link    = '';
						   $show_notice      = false;

						   if( ! empty( $border_color ) ){
							   $style .= "border-left-color: {$border_color};";
						   }

						   if( ! empty( $background_color ) ){
							   $style .= "background-color: {$background_color};";
						   }

						   if( ! empty( $title ) ) {
						       $promo_id .= $title;
							   $title = sprintf( '<strong>%s</strong>: ', $title );
							   $show_notice = true;
						   }

						   if( ! empty( $description ) ) {
						       $promo_id .= $description;
							   $description = sprintf( '%s', $description );
							   $show_notice = true;
						   }

						   if( ! empty( $url ) && ! empty( $url_label )) {
						       $promo_id .= $url . $url_label;
							   $link = sprintf( '<a href="%s" target="_blank">%s</a>', $url, $url_label );
							   $show_notice = true;
						   }

						   $unique_promo_id = "tbay-notice-" . md5 ( $promo_id );

						   if( ! empty( $_COOKIE[ 'hide_' . $unique_promo_id ] ) && 'yes' == $_COOKIE[ 'hide_' . $unique_promo_id ] ){
						       $show_notice = false;
                           }

						   if( true === $show_notice ) :
							   ?>
                               <div id="<?php echo $unique_promo_id; ?>" class="tbay-notice-is-dismissible notice notice-tbay notice-alt is-dismissible" style="<?php echo $style; ?>" data-expiry = <?php echo $promo->end_date; ?>>
                                   <p>
									   <?php printf( "%s %s %s", $title, $description, $link ); ?>
                                   </p>
                               </div>
						   <?php endif;
                       }
                   }
			   }
            }
	    }
	}
}

if( ! function_exists( 'tbay_plugin_notice_dismiss' ) ){
	function tbay_plugin_notice_dismiss(){
	    wp_enqueue_script( 'tbay-framework-promo', TBAY_FRAMEWORK_URL . 'assets/promo.js', array( 'jquery'  ), '1.0.0', true );
	    wp_enqueue_style( 'tbay-framework-promo', TBAY_FRAMEWORK_URL . 'assets/promo.css', '1.0.0', true );
	    wp_enqueue_script( 'tbay-framework-promo' );
	}
}

if( ! function_exists( 'tbay_plugin_get_promo_transient_expiry_date' ) ){
	function tbay_plugin_get_promo_transient_expiry_date( $expiry_date ) {
		$xml_expiry_date = ! empty( $expiry_date ) ? $expiry_date : '+6 hours';
		$current     = strtotime( current_time( 'Y-m-d H:i:s', 1 ) );
		$expiry_date = strtotime( $xml_expiry_date, $current );

		if( $expiry_date <= $current ){
			$expiry_date = strtotime( '+24 hours', $current );
        }

		return $expiry_date;
	}
}

if( ! function_exists( 'tbay_plugin_regenerate_transient' ) ){
    function tbay_plugin_regenerate_transient(){
        if( false === get_option( 'tbay_plugin_promo_2019', false ) ){
	        delete_site_transient( 'tbay_promo_message' );
	        update_option( 'tbay_plugin_promo_2019', true );
        }
    }
}