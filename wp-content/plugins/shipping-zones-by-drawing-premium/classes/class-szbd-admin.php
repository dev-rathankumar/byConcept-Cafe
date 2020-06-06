<?php
class SZBD_Admin
  {
  function __construct()
    {
    add_action( 'admin_enqueue_scripts', array(
       $this,
      'enqueue_scripts'
    ) );
    add_action( 'add_meta_boxes', array(
       $this,
      'add_meta_boxes'
    ) );
    add_action( 'save_post_szbdzones', array(
       $this,
      'save_post'
    ), 10, 3 );
     add_action('wp_ajax_test_store_address', array(
       $this,
       'test_store_address'));
    }
      function test_store_address(){

        $store_address     = get_option( 'woocommerce_store_address' ,'');
$store_address_2   = get_option( 'woocommerce_store_address_2','' );
$store_city        = get_option( 'woocommerce_store_city','' );
$store_postcode    = get_option( 'woocommerce_store_postcode','' );
$store_raw_country = get_option( 'woocommerce_default_country','' );
$split_country = explode( ":", $store_raw_country );
// Country and state
$store_country = $split_country[0];
// Convert country code to full name if available
				if ( isset( WC()->countries->countries[ $store_country ] ) ) {
					$store_country = WC()->countries->countries[ $store_country ];
				}
$store_state   = isset($split_country[1]) ?  $split_country[1] : '';
        $store_loc = array(
                      'store_address' => $store_address,
                     'store_address_2' => $store_address_2,
                      'store_postcode' => $store_postcode,
					  'store_city'	=> $store_city,

                       'store_state'	=> $store_state,
					  'store_country'	=> $store_country,

                      );
		wp_send_json(
                 array(
                       'store_address' =>  $store_loc,




                       ));
    }
  public function enqueue_scripts()
    {
      if ( isset ($_GET['tab']) && $_GET['tab'] == 'szbdtab' ) {
					 wp_enqueue_script( 'shipping-del-aro-admin-settings', SZBD_PREM_PLUGINDIRURL . '/assets/szbd-admin-settings.js', array(
         'jquery'
      ), true );
						wp_localize_script( 'shipping-del-aro-admin-settings', 'szbd_settings', array( 'ajax_url' => admin_url('admin-ajax.php')) );


      wp_enqueue_script( 'szbd-script', '//maps.googleapis.com/maps/api/js?key=' . get_option( 'szbd_google_api_key', '' ), array(
         'jquery'
      ), false, true );

	}
         wp_enqueue_script( 'shipping-del-aro-admin-method', SZBD_PREM_PLUGINDIRURL . '/assets/szbd-admin-method.js', array(
         'jquery'
      ), true );
    global $pagenow, $post;
    if ( isset( get_current_screen()->id ) && ( get_current_screen()->id == 'edit-' . SZBD::POST_TITLE || get_current_screen()->id == SZBD::POST_TITLE ) )
      {
      wp_enqueue_script( 'shipping-del-aro-admin', SZBD_PREM_PLUGINDIRURL . '/assets/szbd-admin.js', array(
         'jquery'
      ), true );
      $args = array(
         'screen' => null !== get_current_screen() ? get_current_screen() : false
      );
      wp_localize_script( 'shipping-del-aro-admin', 'szbd', $args );
      }
    if ( get_post_type() !== 'szbdzones' || !in_array( $pagenow, array(
       'post-new.php',
      'edit.php',
      'post.php'
    ) ) )
      {
      return;
      }
    $google_api_key = get_option( 'szbd_google_api_key', '' );
    if ( $google_api_key != '' && get_current_screen()->id == SZBD::POST_TITLE && get_option( 'szbd_deactivate_google', 'no' ) == 'no')
      {
      wp_enqueue_script( 'szbd-script', '//maps.googleapis.com/maps/api/js?key=' . $google_api_key . '&libraries=geometry,places,drawing', array(
         'jquery'
      ), false, true );
      wp_register_script( 'szbd-script-2', SZBD_PREM_PLUGINDIRURL. '/assets/szbd-admin-map.js', array(
         'szbd-script',
        'jquery'
      ), false, true );
      $this->szbdzones_js( $post->ID );
      wp_enqueue_script( 'szbd-script-2' );
      }
    }
  public function add_meta_boxes()
    {
    add_meta_box( 'szbdzones_mapmeta', 'Map', array(
       $this,
      'input_map'
    ), 'szbdzones', 'normal', 'high' );
    }
  public function input_map()
    {
    global $post;
    $google_api_key = get_option( 'szbd_google_api_key', '' );
    if ( $google_api_key != '' || get_option( 'szbd_deactivate_google', 'no' ) == 'yes' )
      {
      include SZBD_PREM_PLUGINDIRPATH . '/includes/admin-map-template.php';
      }
    else
     { echo sprintf( __( 'Please enter a Google Maps API Key in the <a href="%s" title="settings page">settings page.</a>', SZBD::TEXT_DOMAIN ), admin_url( 'admin.php?page=wc-settings&tab=szbdtab' ) );
    }
		 echo '<div class="notice notice-success is-dismissible">

            <div class="fdoe_premium">

            	<table>

                	<tbody><tr>

                    	<td width="100%">

                        	<p style="font-size:1.3em"><strong><i>Show a delivery map to customers </i></strong>with [szbd] shortcode</p>

                            <ul class="fa-ul" id="fdoe_premium_ad">

								<li ><span class="fa-li" ><i class="fas fa-check" style="color:green"></i></span>	Add drawn maps by post ids, like ids="id1, id2, id3"</li>
								 	<li ><span class="fa-li" ><i class="fas fa-check" style="color:green"></i></span>	Add a title to the map by title="Title"</li>
											<li ><span class="fa-li" ><i class="fas fa-check" style="color:green"></i></span>	Set the color of delivery areas by color="blue"</li>

                            	<li ><span class="fa-li" ><i class="fas fa-check" style="color:green"></i></span>	Example [szbd ids="id1,id2" title="Delivery Zones" color="#c87f93"]</li>

                            </ul>

                        </td>



                    </tr>

                </tbody></table>

            </div>

         </div>';
		}
  public function szbdzones_js( $post_id )
    {
    $settings     = get_post_meta( $post_id, 'szbdzones_metakey', true );
    $lat          = isset( $settings['lat'] ) ? $settings['lat'] : '';
    $lng          = isset( $settings['lng'] ) ? $settings['lng'] : '';
    $zoom         = isset( $settings['zoom'] ) ? $settings['zoom'] : '1.3';
    $geo_coordinates_array = is_array( $settings ) && is_array( $settings['geo_coordinates'] ) ? $settings['geo_coordinates'] : array();
    if ( count( $geo_coordinates_array ) > 0 )
      {
      foreach ( $geo_coordinates_array as $geo_coordinates )
        {
        if ( $geo_coordinates[0] != '' && $geo_coordinates[1] != '' )
          $array_latlng[] = array(
             $geo_coordinates[0],
            $geo_coordinates[1]
          );
        }
      }
    else
      {
      $array_latlng = array();
      }
    $args = array(
       'lat' => $lat,
      'lng' => $lng,
      'zoom' => intval( $zoom ),
      'array_latlng' => $array_latlng
    );
    wp_localize_script( 'szbd-script-2', 'szbd_map', $args );
    //    }
    }
  public function save_post( $post_id, $post, $update )
    {
        if ( is_multisite() && ms_is_switched() ){
    return FALSE;
        }
    if ( $post->post_type != 'szbdzones' )
      return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
      return;
    if ( wp_is_post_revision( $post_id ) )
      return;
    if ( !current_user_can( 'edit_post', $post_id ) )
      return;
    if ( isset( $_POST['szbdzones_geo_coordinates'] ) && !empty( $_POST['szbdzones_geo_coordinates'] ) )
      {
      $array_geo_coordinates = explode( '),(', $_POST['szbdzones_geo_coordinates'] );
      if ( is_array( $array_geo_coordinates ) && count( $array_geo_coordinates ) > 0 )
        {
        foreach ( $array_geo_coordinates as $value_geo_coordinates )
          {
          $latlng         = str_replace( array(
             "(",
            ")"
          ), array(
             "",
            ""
          ), $value_geo_coordinates );
          $array_latlng[] = array_map( 'sanitize_text_field', explode( ',', $latlng ) );
          }
        }
      else
        $array_latlng = array();
      $array_save_post = array(
         'lcolor' => !empty( $_POST['szbdzones_lcolor'] ) ? sanitize_text_field( $_POST['szbdzones_lcolor'] ) : '#0c6e9e',
        'lat' => !empty( $_POST['szbdzones_lat'] ) ? sanitize_text_field( $_POST['szbdzones_lat'] ) : 0,
        'lng' => !empty( $_POST['szbdzones_lng'] ) ? sanitize_text_field( $_POST['szbdzones_lng'] ) : 65,
        'geo_coordinates' => $array_latlng,
        'zoom' => !empty( $_POST['szbdzones_zoom'] ) ? sanitize_text_field( $_POST['szbdzones_zoom'] ) : 1.3
      );
      update_post_meta( $post_id, 'szbdzones_metakey', $array_save_post );
      }
    return $post_id;
    }
  }
?>
