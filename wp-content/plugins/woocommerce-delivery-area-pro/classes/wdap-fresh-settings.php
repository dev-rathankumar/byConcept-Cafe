<?php 


Class WDAP_Fresh_Settings{

	public static $showzipcodesearch  = array(
				'wdap_map_width' => 750,
				'wdap_map_height' => 700,
				'wdap_map_zoom_level' => 5,
				'wdap_map_center_lat' => 40.730610,
				'wdap_map_center_lng' => -73.935242,
				'wdap_check_buttonlbl' => 'Check Availability',
				'wdap_frontend_desc' => 'Verify your pincode for correct delivery details',
				'apply_on' => array(
					'checkedvalue' => array(
						'0' => 'product_page',
						'1' => 'shop_page',
					),
				),
				
				'wdap_shop_error_notavailable' => 'Product Not Available',
				'wdap_shop_error_available' => 'Product Available',
				'wdap_shop_error_invalid' => 'Invalid Zipcode',	

				'wdap_category_error_notavailable' => 'Product Not Available',
				'wdap_category_error_available' => 'Product Available',
				'wdap_category_error_invalid' => 'Invalid Zipcode',	

				'wdap_product_error_notavailable' => 'Product Not Available',
				'wdap_product_error_available' => 'Product Available',
				'wdap_product_error_invalid' => 'Invalid Zipcode',		

				'wdap_cart_error_notavailable' => 'Product Not Available',
				'wdap_cart_error_available' => 'Product Available',
				'wdap_cart_error_invalid' => 'Invalid Zipcode',			
				'wdap_cart_error_th' => 'Availability Status',			
				'wdap_cart_error_summary' => '{no_products_available} Available, {no_products_unavailable} Unavailable',			

				'wdap_checkout_error_notavailable' => 'Product Not Available',
				'wdap_checkout_error_available' => 'Product Available',
				'wdap_checkout_error_invalid' => 'Invalid Zipcode',
				'wdap_checkout_error_th' => ' Availability Status',
				'wdap_checkout_error_summary' => '{no_products_available} Available, {no_products_unavailable} Unavailable',


				'avl_button_color' => '#ffffff',
				'avl_button_bgcolor' => '#a46497',
				'success_msg_color' => '#209620',
				'error_msg_color' => '#ff0000',
				'wdap_order_restrict_error' => 'Apology! Your order cannot be placed as one or more products present in the cart cannot be delivered by us at the specified zipcode or address.',
				'wdap_empty_zip_code' => 'Please enter zip code.',
				'wdap_address_empty'  => 'Please enter your address.',
				'address_not_shipable' => 'Sorry, We do not provide shipping in this area.',
				'address_shipable' => 'Yes, We provide shipping in this area.',
				'form_success_msg_color' => '#209620',
				'form_error_msg_color' => '#ff0000',
				'wdap_form_buttonlbl' => 'Check Availability',
				'form_button_color' => '#fff',
				'check_buttonPlaceholder' => 'Type Delivery Location (Landmark, Road or Area)',
				'form_button_bgcolor' => '#a46497',
				'wdap_form_locateme' => 'Locate Me',
				'product_listing_error' => 'Please select at least one product.',
				'enable_locate_me_btn' => 'true',
				'enable_bound' => 'yes',
				'enable_markers_on_map' => 'true',
				'enable_polygon_on_map' => 'true',
				'enable_map_bound' => 'true',
				'enable_auto_suggest_checkout' => 'false',
				'wdap_checkout_avality_method' => 'via_zipcode',
				'shortcode_map_width' => 750,
				'shortcode_map_height' => 700,
				'shortcode_map_zoom_level' => 5,
				'shortcode_map_center_lat' => 40.730610,
				'shortcode_map_center_lng' => -73.935242,
				'default_templates' => array(
					'zipcode' => 'default',
					'shortcode' => 'default',
				),
				'wpdap_language'=>'en',
				'search_box_placeholder'=>'Enter Zipcode'
			);


    public static function get_fresh_settings(){

    	return self::$showzipcodesearch;
    }


}