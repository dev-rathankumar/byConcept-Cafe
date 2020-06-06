<?php
/**
 * Plugin Setting page for wp-delivery-area-pro.
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 2.0.0
 * @package woo-delivery-area-pro
 */
?>
<div class="se-pre-con"></div>

<?php
		$form  = new WDAP_FORM();
		$form->set_header( esc_html__( 'Delivery Area Enquiry Form Settings', 'woo-delivery-area-pro' ), $response );
		$data = maybe_unserialize( get_option( 'wp-delivery-area-pro' ) );
		$apply_on = array(
			'product_page' => esc_html__( 'On Product Page', 'woo-delivery-area-pro' ),
			'shop_page' => esc_html__( 'On Shop Page ', 'woo-delivery-area-pro' ),
			'category_page' => esc_html__( 'On Category Page ', 'woo-delivery-area-pro' ),
			'cart_page' => esc_html__( 'On Cart Page', 'woo-delivery-area-pro' ),
			'checkout_page' => esc_html__( 'On Checkout Page', 'woo-delivery-area-pro' ),

		);

		$form->add_element(
			'multiple_checkbox', 'apply_on[checkedvalue][]', array(
				'lable' => esc_html__( 'Display Delivery Enquiry Form', 'woo-delivery-area-pro' ),
				'value' => $apply_on,
				'current' =>isset($data['apply_on']['checkedvalue']) ? $data['apply_on']['checkedvalue']:'',
				'class' => 'chkbox_class switch_onoffs',
				'desc' => esc_html__( 'Please select woocommerce pages.', 'woo-delivery-area-pro' ),
				'default_value' => 'product_page',
				'data' => array( 'target' => '.exclude_form_categories' ),

			)
		);

		$choose_categories = isset( $_POST['excludecategories'] ) ? $_POST['excludecategories'] : '';
		$form->add_element(
			'category_selector', 'excludecategories', array(
				'lable' => esc_html__( 'Exclude Categories', 'woo-delivery-area-pro' ),
				'value' => '',
				'current' => isset( $data ['excludecategories'] ) ? maybe_unserialize( $data ['excludecategories'] ) : $choose_categories,
				'class' => 'chkbox_class exclude_form_categories_excludecategories',
				'data_type' => 'taxonomy=product_cat',
				'show' => 'false',
				'desc' => esc_html__( 'Delivery area enquiry form will exclude from all products which falling in above selected categories.', 'woo-delivery-area-pro' ),
			)
		);

		$form->add_element(
			'group', 'map_general_settings', array(
				'value' => esc_html__( 'Product Availability Map ( On Product Page )', 'woo-delivery-area-pro' ),
				'before' => '<div class="fc-12">',
				'after' => '</div>',
				'desc' => esc_html__( 'This map will be displayed on product page in a new tab to display all the locations where the current product can be delivered.', 'woo-delivery-area-pro' ),

			)
		);

		 $desc = sprintf( esc_html__( 'You need to get an api key for google map to work with your website. You can read and follow from %s link to get api keys.', 'woo-delivery-area-pro' ), '<a href ="https://www.linkedin.com/pulse/important-changes-google-maps-api-v3-website-owners-sandeep-kumar" target="_blank" >This</a>' );
		$form->add_element(
			'text', 'wdap_googleapikey', array(
				'lable' => esc_html__( ' Enter Google Map API Key', 'woo-delivery-area-pro' ),
				'value' => isset( $data['wdap_googleapikey'] ) ? $data['wdap_googleapikey'] : '',
				'desc' => $desc,
				'class' => 'form-control',
				'placeholder' => esc_html__( 'Enter Google Map Key', 'woo-delivery-area-pro' ),
				'before' => '<div class="fc-6" >',
				'after' => '</div>',
			)
		);



		$form->add_element(
			'text', 'wdap_map_width', array(
				'lable' => esc_html__( ' Enter Google Map Width', 'woo-delivery-area-pro' ),
				'value' => isset( $data['wdap_map_width'] ) ? stripslashes( wp_strip_all_tags( $data['wdap_map_width'] ) ) : '',
				'class' => 'form-control',
				'placeholder' => esc_html__( 'Enter Google Map Width', 'woo-delivery-area-pro' ),
				'before' => '<div class="fc-6" >',
				'after' => '</div>',
			)
		);

		$form->add_element(
			'text', 'wdap_map_height', array(
				'lable' => esc_html__( ' Enter Google Map height', 'woo-delivery-area-pro' ),
				'value' => isset( $data['wdap_map_height'] ) ? stripslashes( wp_strip_all_tags( $data['wdap_map_height'] ) ) : '',
				'class' => 'form-control',
				'required' => true,
				'placeholder' => esc_html__( 'Enter Google Map Height', 'woo-delivery-area-pro' ),
				'before' => '<div class="fc-6" >',
				'after' => '</div>',

			)
		);

		$form->add_element(
			'number', 'wdap_map_zoom_level', array(
				'lable' => esc_html__( 'Enter Google Map Zoom Level', 'woo-delivery-area-pro' ),
				'value' => isset( $data['wdap_map_zoom_level'] ) ? stripslashes( wp_strip_all_tags( $data['wdap_map_zoom_level'] ) ) : '',
				'class' => 'form-control',
				'placeholder' => esc_html__( 'Enter Google Map Zoom Level', 'woo-delivery-area-pro' ),
				'before' => '<div class="fc-6" >',
				'after' => '</div>',
				'default_value' => 5,
			)
		);

		$form->add_element(
			'text', 'wdap_map_center_lat', array(
				'lable' => esc_html__( 'Enter Map Center Latitude', 'woo-delivery-area-pro' ),
				'value' => isset( $data['wdap_map_center_lat'] ) ? stripslashes( wp_strip_all_tags( $data['wdap_map_center_lat'] ) ) : '',
				'class' => 'form-control',
				'placeholder' => esc_html__( 'Enter Map Center Latitude', 'woo-delivery-area-pro' ),
				'before' => '<div class="fc-6" >',
				'after' => '</div>',
				'default_value' => 40.730610,
			)
		);

		$form->add_element(
			'text', 'wdap_map_center_lng', array(
				'lable' => esc_html__( 'Enter Map Center Longitude', 'woo-delivery-area-pro' ),
				'value' => isset( $data['wdap_map_center_lng'] ) ? stripslashes( wp_strip_all_tags( $data['wdap_map_center_lng'] ) ) : '',
				'class' => 'form-control',
				'placeholder' => esc_html__( 'Enter Map Center Longitude', 'woo-delivery-area-pro' ),
				'before' => '<div class="fc-6" >',
				'after' => '</div>',
				'default_value' => -73.935242,
			)
		);

		$form->add_element(
			'textarea', 'wdap_map_style', array(
				'lable' => esc_html__( 'Enter Snazzy Map Google Map Style', 'woo-delivery-area-pro' ),
				'value' => isset( $data['wdap_map_style'] ) ? stripslashes( wp_strip_all_tags( $data['wdap_map_style'] ) ) : '',
				'class' => 'form-control',
				'placeholder' => esc_html__( 'Enter Snazzy Map Google Map Style', 'woo-delivery-area-pro' ),
				'before' => '<div class="fc-6" >',
				'after' => '</div>',
			)
		);
		$form->add_element(
			'checkbox', 'enable_map_bound', array(
				'lable' => esc_html__( 'Enable Map Bound', 'woo-delivery-area-pro' ),
				'value' => 'true',
				'current' => isset( $data['enable_map_bound'] ) ? $data['enable_map_bound'] : '',
				'desc' => esc_html__( 'YES', 'woo-delivery-area-pro' ),
				'default_value' => 'true',
			)
		);
		$form->add_element(
			'checkbox', 'enable_markers_on_map', array(
				'lable' => esc_html__( 'Enable Markers on Map', 'woo-delivery-area-pro' ),
				'value' => 'true',
				'current' => isset( $data['enable_markers_on_map'] ) ? $data['enable_markers_on_map'] : '',
				'desc' => esc_html__( 'YES', 'woo-delivery-area-pro' ),
				'default_value' => 'true',
			)
		);
		$form->add_element(
			'checkbox', 'enable_polygon_on_map', array(
				'lable' => esc_html__( 'Enable Polygons on Map', 'woo-delivery-area-pro' ),
				'value' => 'true',
				'current' => isset( $data['enable_polygon_on_map'] ) ? $data['enable_polygon_on_map'] : '',
				'desc' => esc_html__( 'YES', 'woo-delivery-area-pro' ),
				'default_value' => 'true',
			)
		);

		$form->add_element(
			'group', 'wdap_countries_restriction', array(
				'value' => esc_html__( 'Perform Searching WithIn A Specific Country', 'woo-delivery-area-pro' ),
				'before' => '<div class="fc-12">',
				'after' => '</div>',
			)
		);

		$form->add_element(
			'checkbox', 'enable_retrict_country', array(
				'lable' => esc_html__( 'Enable Country Restriction', 'woo-delivery-area-pro' ),
				'value' => 'true',
				'id' => 'date_filters',
				'current' => isset( $data['enable_retrict_country'] ) ? $data['enable_retrict_country'] : '',
				'desc' => esc_html__( 'YES', 'woo-delivery-area-pro' ),
				'class' => 'chkbox_class keep_aspect_ratio switch_onoff',
				'data' => array( 'target' => '.enable_retrict_countries' ),
				'default_value' => 'true',
			)
		);

		 $countries_obj   = new WC_Countries();
		 $countries   = $countries_obj->__get( 'countries' );
		 $newchoose_continent = array();
		foreach ( $countries as  $key => $values ) {

			$newchoose_continent[] = array(
				'id' => $key,
				'text' => $values,
			);

		}
			$selected_restricted_countries = isset( $data['wdap_country_restriction_listing'] ) ? $data['wdap_country_restriction_listing'] : '';

			$form->add_element(
				'category_selector', 'wdap_country_restriction_listing', array(
					'lable' => esc_html__( 'Choose Country', 'woo-delivery-area-pro' ),
					'data' => $newchoose_continent,
					'current' => ( isset( $selected_restricted_countries ) and ! empty( $selected_restricted_countries ) ) ? $selected_restricted_countries : '',
					'desc' => esc_html__( 'Some places of different counties have same zipcodes. If your product delivery area falls under such category, you can specify your country here. By this google api will provide quick and more accurate results without confliction with similar zipcode of other country. Useful only if you are not specifying zipcodes directly in textbox.', 'woo-delivery-area-pro' ),

					'class' => 'enable_retrict_countries',
					'before' => '<div class="fc-9">',
					'after' => '</div>',
					'multiple' => 'false',
					'show' => 'false',

				)
			);

			$form->add_element(
				'checkbox', 'enable_places_to_retrict_country_only', array(
					'lable' => esc_html__( 'Display Places Of Restricted Country Only', 'woo-delivery-area-pro' ),
					'value' => 'true',
					'id' => 'enable_places_to_retrict_country_only',
					'current' => isset( $data['enable_places_to_retrict_country_only'] ) ? $data['enable_places_to_retrict_country_only'] : '',
					'desc' => esc_html__( 'When country restriction is enabled, display places of restricted country only in autosuggest textbox to user for only shortcode form.', 'woo-delivery-area-pro' ),
					'class' => 'chkbox_class enable_retrict_countries',
					'show' => 'false',
				)
			);

			$form->add_element(
				'checkbox', 'restrict_places_of_country_checkout', array(
					'lable' => esc_html__( 'Display Places Of Restricted Country Only ( Checkout Page )', 'woo-delivery-area-pro' ),
					'value' => 'true',
					'id' => 'restrict_places_of_country_checkout',
					'current' => isset( $data['restrict_places_of_country_checkout'] ) ? $data['restrict_places_of_country_checkout'] : '',
					'desc' => esc_html__( 'When country restriction is enabled, display places of restricted country only in autosuggest textbox to user for only checkout page.', 'woo-delivery-area-pro' ),
					'class' => 'chkbox_class enable_retrict_countries',
					'show' => 'false',
				)
			);

			$form->add_element(
				'group', 'wdap_order_restriction', array(
					'value' => esc_html__( 'Enable Order Restriction On Checkout Form', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-12">',
					'after' => '</div>',

				)
			);

			$form->add_element(
				'checkbox', 'enable_order_restriction', array(
					'lable' => esc_html__( 'Enable Order Restriction ', 'woo-delivery-area-pro' ),
					'value' => 'true',
					'id' => 'date_filters',
					'current' => isset( $data['enable_order_restriction'] ) ? $data['enable_order_restriction'] : '',
					'desc' => esc_html__( 'YES', 'woo-delivery-area-pro' ),
					'class' => 'chkbox_class keep_aspect_ratio ',
					'default_value' => 'true',
				)
			);


			$checkout_method = array(
				'via_zipcode' => esc_html__( 'Via Zipcode', 'woo-delivery-area-pro' ),
			);
			if ( ! empty( $data['wdap_googleapikey'] ) ) {
				$checkout_method['via_address'] = esc_html__( 'Via Address', 'woo-delivery-area-pro' );
			}

			$post_checkout_avality_method = isset($_POST['wdap_checkout_avality_method']) ? $_POST['wdap_checkout_avality_method'] :'';

			$form->add_element(
				'radio', 'wdap_checkout_avality_method', array(
					'lable' => esc_html__( 'Zipcode/Address For Checking On Checkout Page', 'woo-delivery-area-pro' ),
					'current' => ( isset( $data ['wdap_checkout_avality_method'] ) and ! empty( $data ['wdap_checkout_avality_method'] ) ) ? $data ['wdap_checkout_avality_method'] : $post_checkout_avality_method,
					'radio-val-label' => $checkout_method,
					'default_value' => 'via_zipcode',
					'desc' => esc_html__( 'Checking of delivery will be decided based on this option. If via zipcode is selected, zipcode will be taken from the default woocommerce zipcode field and will be used in testing and message will be shown accordingly. if via address is selected, billing address is used for checking delivery status in that area(address). Via Zipcode is recommended way to check for delivery on checkout page.', 'woo-delivery-area-pro' ),
				)
			);

			$form->add_element(
				'checkbox', 'enable_auto_suggest_checkout', array(
					'lable' => esc_html__( 'Enable Auto Suggest On Checkout Page ', 'woo-delivery-area-pro' ),
					'value' => 'true',
					'current' => isset( $data['enable_auto_suggest_checkout'] ) ? $data['enable_auto_suggest_checkout'] : '',
					'class' => 'chkbox_class keep_aspect_ratio ',
					'default_value' => 'true',
					'desc' => esc_html__( 'Google Autosuggest functionality will enable on billing and shipping address field. Checkout form fields autofill on select of address.', 'woo-delivery-area-pro' ),
				)
			);



			$form->add_element(
				'group', 'wdap_error_message', array(
					'value' => esc_html__( 'Manageable Messages For WooPages', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-12">',
					'after' => '</div>',
				)
			);


			$form->add_element(
				'group', 'wdap_shop_message', array(
					'value' => esc_html__( 'For Shop Page', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-10">',
					'after' => '</div>',
				)
			);


			$errormessage = array(
				'notavailable' => esc_html__( 'Product Not Available ', 'woo-delivery-area-pro' ),
				'available' => esc_html__( 'Product Available ', 'woo-delivery-area-pro' ),
				'invalid' => esc_html__( 'Invalid Zipcode ', 'woo-delivery-area-pro' )			);
			foreach ( $errormessage as $key => $message ) {
				$placeholder = $message;
				$desc = '';
	
				$form->add_element(
					'text', 'wdap_shop_error_' . $key, array(
						'lable' => sprintf( esc_html__( '%s', 'woo-delivery-area-pro' ), $message ),
						'value' => isset( $data[ 'wdap_shop_error_' . $key ] ) ? $data[ 'wdap_shop_error_' . $key ] : '',
						'desc' => $desc,
						'class' => 'form-control',
						'placeholder' => $placeholder,
						'before' => '<div class="fc-6" >',
						'after' => '</div>',
						'default_value' => $message,
					)
				);
			}

			$form->add_element(
				'group', 'wdap_category_message', array(
					'value' => esc_html__( 'For Category Page', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-10">',
					'after' => '</div>',
				)
			);


			$errormessage = array(
				'notavailable' => esc_html__( 'Product Not Available ', 'woo-delivery-area-pro' ),
				'available' => esc_html__( 'Product Available ', 'woo-delivery-area-pro' ),
				'invalid' => esc_html__( 'Invalid Zipcode ', 'woo-delivery-area-pro' )			);
			foreach ( $errormessage as $key => $message ) {
				$placeholder = $message;
				$desc = '';
	
				$form->add_element(
					'text', 'wdap_category_error_' . $key, array(
						'lable' => sprintf( esc_html__( '%s', 'woo-delivery-area-pro' ), $message ),
						'value' => isset( $data[ 'wdap_category_error_' . $key ] ) ? $data[ 'wdap_category_error_' . $key ] : '',
						'desc' => $desc,
						'class' => 'form-control',
						'placeholder' => $placeholder,
						'before' => '<div class="fc-6" >',
						'after' => '</div>',
						'default_value' => $message,
					)
				);
			}

			$form->add_element(
				'group', 'wdap_product_message', array(
					'value' => esc_html__( 'For Product Page', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-10">',
					'after' => '</div>',
				)
			);

			foreach ( $errormessage as $key => $message ) {
				$placeholder = $message;
				$desc = '';
				$form->add_element(
					'text', 'wdap_product_error_' . $key, array(
						'lable' => sprintf( esc_html__( '%s', 'woo-delivery-area-pro' ), $message ),
						'value' => isset( $data[ 'wdap_product_error_' . $key ] ) ? $data[ 'wdap_product_error_' . $key ] : '',
						'desc' => $desc,
						'class' => 'form-control',
						'placeholder' => $placeholder,
						'before' => '<div class="fc-6" >',
						'after' => '</div>',
						'default_value' => $message,
					)
				);
			}

			$form->add_element(
				'group', 'wdap_cart_message', array(
					'value' => esc_html__( 'For Cart Page', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-10">',
					'after' => '</div>',
				)
			);


			$errormessage = array(
				'notavailable' => esc_html__( 'Product Not Available ', 'woo-delivery-area-pro' ),
				'available' => esc_html__( 'Product Available ', 'woo-delivery-area-pro' ),
				'invalid' => esc_html__( 'Invalid Zipcode ', 'woo-delivery-area-pro' ),
				'th' => esc_html__( ' Product Availability Status', 'woo-delivery-area-pro' ),
				'summary' => esc_html__( 'Summary Message', 'woo-delivery-area-pro' ),

			);
			foreach ( $errormessage as $key => $message ) {
				$placeholder = $message;
				$desc = '';
				if ( $key == 'th' ) {
					$placeholder = esc_html__( 'Availability Status', 'woo-delivery-area-pro' );
					$desc = esc_html__( 'Shop Table Heading', 'woo-delivery-area-pro' );
				}
				if ( $key == 'summary' ) {
					$placeholder = esc_html__( '{no_products_available} Available, {no_products_unavailable} Unavailable', 'woo-delivery-area-pro' );
					$desc = esc_html__( 'Use placeholders {no_products_available} = for number of available products , {no_products_unavailable} = for number of unavailable products ', 'woo-delivery-area-pro' );
				}

				$form->add_element(
					'text', 'wdap_cart_error_' . $key, array(
						'lable' => sprintf( esc_html__( '%s', 'woo-delivery-area-pro' ), $message ),
						'value' => isset( $data[ 'wdap_cart_error_' . $key ] ) ? $data[ 'wdap_cart_error_' . $key ] : '',
						'desc' => $desc,
						'class' => 'form-control',
						'placeholder' => $placeholder,
						'before' => '<div class="fc-6" >',
						'after' => '</div>',
						'default_value' => $message,
					)
				);
			}

			$form->add_element(
				'group', 'wdap_checkout_message', array(
					'value' => esc_html__( 'For Checkout Page', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-10">',
					'after' => '</div>',
				)
			);

			foreach ( $errormessage as $key => $message ) {
				$placeholder = $message;
				$desc = '';
				if ( $key == 'th' ) {
					$placeholder = esc_html__( 'Availability Status', 'woo-delivery-area-pro' );
					$desc = esc_html__( 'Shop Table Heading', 'woo-delivery-area-pro' );
				}
				if ( $key == 'summary' ) {
					$placeholder = esc_html__( '{no_products_available} Available, {no_products_unavailable} Unavailable', 'woo-delivery-area-pro' );
					$desc = esc_html__( 'Use placeholders {no_products_available} = for number of available products , {no_products_unavailable} = for number of unavailable products ', 'woo-delivery-area-pro' );
				}

				$form->add_element(
					'text', 'wdap_checkout_error_' . $key, array(
						'lable' => sprintf( esc_html__( '%s', 'woo-delivery-area-pro' ), $message ),
						'value' => isset( $data[ 'wdap_checkout_error_' . $key ] ) ? $data[ 'wdap_checkout_error_' . $key ] : '',
						'desc' => $desc,
						'class' => 'form-control',
						'placeholder' => $placeholder,
						'before' => '<div class="fc-6" >',
						'after' => '</div>',
						'default_value' => $message,
					)
				);
			}

			$form->add_element(
				'text', 'wdap_empty_zip_code', array(
					'lable' => esc_html__( 'Empty Zipcode Error', 'woo-delivery-area-pro' ),
					'value' => isset( $data['wdap_empty_zip_code'] ) ? $data['wdap_empty_zip_code'] : '',
					'class' => 'form-control',
					'placeholder' => esc_html__( 'Please enter zip code.', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-6" >',
					'after' => '</div>',
					'default_value' => esc_html__( 'Please enter zip code.', 'woo-delivery-area-pro' ),
				)
			);

			$form->add_element(
				'text', 'wdap_order_restrict_error', array(
					'lable' => esc_html__( 'Order Restriction Error Message', 'woo-delivery-area-pro' ),
					'value' => isset( $data['wdap_order_restrict_error'] ) ? $data['wdap_order_restrict_error'] : '',
					'class' => 'form-control',
					'placeholder' => esc_html__( 'We could not complete your order due to Zip Code Unavailability.', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-6" >',
					'after' => '</div>',
					'default_value' => esc_html__( 'We could not complete your order due to Zip Code Unavailability.', 'woo-delivery-area-pro' ),
				)
			);

			// End of Delivery Notifications
			$form->add_element(
				'group', 'wdap_avl_button_settings', array(
					'value' => esc_html__( 'Delivery Area Form UI Settings', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-12">',
					'after' => '</div>',
				)
			);


			$form->add_element(
				'checkbox', 'disable_availability_tab', array(
					'lable' => esc_html__( 'Disable Product Availability', 'woo-delivery-area-pro' ),
					'value' => 'true',
					'current' => isset( $data['disable_availability_tab'] ) ? $data['disable_availability_tab'] : '',
					'desc' => esc_html__( 'Disable product availability tab on all products', 'woo-delivery-area-pro' ),
					'class' => 'chkbox_class  ',
				)
			);

			$form->add_element('checkbox', 'disable_zipcode_listing', array(
					'lable' => esc_html__( 'Hide zipcode list', 'woo-delivery-area-pro' ),
					'value' => 'true',
					'current' => isset( $data['disable_zipcode_listing'] ) ? $data['disable_zipcode_listing'] : '',
					'desc' => esc_html__( 'Hides the listing of zipcodes that is displayed on top of map on product availibility map.', 'woo-delivery-area-pro' ),
					'class' => 'chkbox_class  ',
				)
			);

			$custom_marker_img_id = (isset( $data[ 'custom_marker_img_attachment_id' ] ) ) ? $data[ 'custom_marker_img_attachment_id' ]  : '';
			$desc =   esc_html__('Upload custom marker icon which show on map in product availability tab.','woo-delivery-area-pro');

			$form->add_element( 'image_picker', 'custom_marker_img', array(
				'id' => 'custom_marker_img',
				'lable' => esc_html__( 'Custom marker icon', 'woo-delivery-area-pro' ),
				'src' => (isset( $data['custom_marker_img'] ) ) ? $data['custom_marker_img']  : '',
				'attachment_id' => $custom_marker_img_id,
				'required' => false,
				'choose_button' => esc_html__( 'Upload Icon Image', 'woo-delivery-area-pro' ),
				'remove_button' => esc_html__( 'Remove Icon','woo-delivery-area-pro' ),
				'desc' => $desc

			)); 

			$form->add_element(
				'text', 'search_box_placeholder', array(
					'lable' => esc_html__( 'Search Box Placeholder', 'woo-delivery-area-pro' ),
					'value' => isset( $data['search_box_placeholder'] ) ? $data['search_box_placeholder'] : '',
					'class' => 'form-control',
					'desc' => esc_html__( 'Delivey Search Box Placeholder on WooPages ', 'woo-delivery-area-pro' ),
					'placeholder' => esc_html__( 'Enter Zipcode', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-6" >',
					'after' => '</div>',
				)
			);

			$form->add_element(
				'text', 'wdap_check_buttonlbl', array(
					'lable' => esc_html__( 'Button Label', 'woo-delivery-area-pro' ),
					'value' => isset( $data['wdap_check_buttonlbl'] ) ? $data['wdap_check_buttonlbl'] : '',
					'class' => 'form-control',
					'placeholder' => esc_html__( 'Check Availability', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-6" >',
					'after' => '</div>',
					'default_value' => esc_html__( 'Check Availability', 'woo-delivery-area-pro' ),
				)
			);

			$form->add_element(
				'text', 'wdap_checkout_buttonlbl', array(
					'lable' => esc_html__( 'Place Order Button Label', 'woo-delivery-area-pro' ),
					'value' => isset( $data['wdap_checkout_buttonlbl'] ) ? $data['wdap_checkout_buttonlbl'] : '',
					'class' => 'form-control',
					'placeholder' => esc_html__( 'Place Order', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-6" >',
					'after' => '</div>',
					'default_value' => esc_html__( 'Place Order', 'woo-delivery-area-pro' ),
					'desc' => esc_html__( 'Please enter place order button label on checkout page if label not translated.', 'woo-delivery-area-pro' ),

				)
			);


			$form->add_element(
				'text', 'wdap_frontend_desc', array(
					'lable' => esc_html__( 'Description', 'woo-delivery-area-pro' ),
					'value' => isset( $data['wdap_frontend_desc'] ) ? $data['wdap_frontend_desc'] : '',
					'class' => 'form-control',
					'placeholder' => esc_html__( 'Verify your pincode for correct delivery details', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-6" >',
					'after' => '</div>',
					'default_value' => esc_html__( 'Verify your pincode for correct delivery details', 'woo-delivery-area-pro' ),
				)
			);



			$form->add_element(
				'text', 'avl_button_color', array(

					'lable' => esc_html__( 'Button Text Color', 'woo-delivery-area-pro' ),
					'value' => isset( $data['avl_button_color'] ) ? $data['avl_button_color'] : '',
					'class' => 'form-control scolor color',
					'before' => '<div class="fc-6" >',
					'after' => '</div>',
					'default_value' => '#fff',

				)
			);

			$form->add_element(
				'text', 'avl_button_bgcolor', array(
					'lable' => esc_html__( 'Button Background Color', 'woo-delivery-area-pro' ),
					'value' => isset( $data['avl_button_bgcolor'] ) ? $data['avl_button_bgcolor'] : '',
					'class' => 'form-control scolor color',
					'before' => '<div class="fc-6" >',
					'after' => '</div>',
					'default_value' => '#a46497',

				)
			);

			$form->add_element(
				'text', 'success_msg_color', array(
					'lable' => esc_html__( 'Success Message Color', 'woo-delivery-area-pro' ),
					'value' => isset( $data['success_msg_color'] ) ? $data['success_msg_color'] : '',
					'class' => 'form-control scolor color ',
					'before' => '<div class="fc-6" >',
					'after' => '</div>',
					'default_value' => '#209620',

				)
			);

			$form->add_element(
				'text', 'error_msg_color', array(
					'lable' => esc_html__( 'Error Message Color', 'woo-delivery-area-pro' ),
					'value' => isset( $data['error_msg_color'] ) ? $data['error_msg_color'] : '',
					'class' => 'form-control scolor color',
					'before' => '<div class="fc-6" >',
					'after' => '</div>',
					'default_value' => '#ff0000',
				)
			);

			$form->add_element(
				'group', 'product_delivery_area-zipcode', array(
					'value' => esc_html__( 'Choose Template For Delivery Area Enquiry Form', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-12">',
					'after' => '</div>',
				)
			);

			$form->add_element(
				'templates', 'wdap_zip_form_design', array(
					'id' => 'wdap_zip_form_design',
					'before' => '<div class="fc-12">',
					'after' => '</div>',
					'product' => 'wp-delivery-area-pro',
					'instance' => 'wdap',
					'tempcol' => '4',
					'dboption' => 'wp-delivery-area-pro',
					'template_types' => array( 'zipcode' ),
					'templatePath' => WDAP_TEMPLATES,
					'templateURL' => WDAP_TEMPLATES_URL,
					'settingPage' => 'wdap_setting_settings',
					'customiser' => 'false',
				)
			);

			$form->add_element(
				'group', 'product_delivery_area', array(
					'value' => esc_html__( 'Choose Template For Delivery Area Enquiry Form (Shortcode)', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-12">',
					'after' => '</div>',
				)
			);

			$form->add_element(
				'templates', 'wdap_shortcode_form_design', array(
					'id' => 'wdap_shortcode_form_design',
					'before' => '<div class="fc-12">',
					'after' => '</div>',
					'product' => 'wp-delivery-area-pro',
					'instance' => 'wdap',
					'tempcol' => '4',
					'dboption' => 'wp-delivery-area-pro',
					'template_types' => array( 'shortcode' ),
					'templatePath' => WDAP_TEMPLATES,
					'templateURL' => WDAP_TEMPLATES_URL,
					'settingPage' => 'wdap_setting_settings',
					'customiser' => 'false',
				)
			);

			ob_start();
			echo do_shortcode( '[delivery_area_form]' );
			$preview = ob_get_contents();
			ob_clean();
			$form->add_element(
				'html', 'shortcode_preview', array(
					'lable' => esc_html__( 'Form Preview', 'woo-delivery-area-pro' ),
					'html' => $preview,
					'before' => '<div class="fc-9">',
					'after' => '</div>',
					'class' => 'email_template_preview custom_email_template_control',
					'desc' => esc_html__( 'Form Preview Will Appear Here.', 'woo-delivery-area-pro' ),
				)
			);

			$form->add_element(
				'text', 'shortcode_form_title', array(
					'lable' => esc_html__( 'Delivey Area Search Title', 'woo-delivery-area-pro' ),
					'value' => isset( $data['shortcode_form_title'] ) ? $data['shortcode_form_title'] : '',
					'class' => 'form-control',
					'placeholder' => esc_html__( 'Enter Delivery Area Form Title', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-6" >',
					'after' => '</div>',
				)
			);
			$form->add_element(
				'text', 'check_buttonPlaceholder', array(
					'lable' => esc_html__( 'Delivey Area Search Placeholder', 'woo-delivery-area-pro' ),
					'value' => isset( $data['check_buttonPlaceholder'] ) ? $data['check_buttonPlaceholder'] : '',
					'class' => 'form-control',
					'placeholder' => esc_html__( 'Delivey Area Search Placeholder ', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-6" >',
					'after' => '</div>',
				)
			);
			$form->add_element(
				'text', 'shortcode_form_description', array(
					'lable' => esc_html__( 'Delivery Area Form Description', 'woo-delivery-area-pro' ),
					'value' => isset( $data['shortcode_form_description'] ) ? $data['shortcode_form_description'] : '',
					'class' => 'form-control',
					'placeholder' => esc_html__( 'Enter Delivery Area Form Description', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-6" >',
					'after' => '</div>',
				)
			);


			$form->add_element(
				'text', 'wdap_address_empty', array(

					'lable' => esc_html__( 'Empty Address Message', 'woo-delivery-area-pro' ),
					'value' => isset( $data['wdap_address_empty'] ) ? $data['wdap_address_empty'] : '',
					'class' => 'form-control',
					'placeholder' => esc_html__( 'Please enter your address.', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-6" >',
					'after' => '</div>',
					'default_value' => esc_html__( 'Please enter your address.', 'woo-delivery-area-pro' ),
				)
			);
			$form->add_element(
				'text', 'address_not_shipable', array(

					'lable' => esc_html__( 'Not Shipping Area Message', 'woo-delivery-area-pro' ),
					'value' => isset( $data['address_not_shipable'] ) ? $data['address_not_shipable'] : '',
					'class' => 'form-control',
					'placeholder' => esc_html__( 'Sorry, We do not provide shipping in this area.', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-6" >',
					'after' => '</div>',
					'default_value' => esc_html__( 'Sorry, We do not provide shipping in this area.', 'woo-delivery-area-pro' ),

				)
			);


			$form->add_element(
				'text', 'address_shipable', array(
					'lable' => esc_html__( 'Shipping Area Message', 'woo-delivery-area-pro' ),
					'value' => isset( $data['address_shipable'] ) ? $data['address_shipable'] : '',
					'class' => 'form-control',
					'placeholder' => esc_html__( 'Yes, We provide shipping in this area.', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-6" >',
					'after' => '</div>',
					'default_value' => esc_html__( 'Yes, We provide shipping in this area.', 'woo-delivery-area-pro' ),
				)
			);

			$form->add_element(
				'text', 'wdap_form_buttonlbl', array(
					'lable' => esc_html__( 'Button Label', 'woo-delivery-area-pro' ),
					'value' => isset( $data['wdap_form_buttonlbl'] ) ? $data['wdap_form_buttonlbl'] : '',
					'class' => 'form-control',
					'placeholder' => esc_html__( 'Check Availability', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-6" >',
					'after' => '</div>',
					'default_value' => esc_html__( 'Check Availability', 'woo-delivery-area-pro' ),
				)
			);



			$form->add_element(
				'text', 'form_success_msg_color', array(
					'lable' => esc_html__( 'Success Message Color', 'woo-delivery-area-pro' ),
					'value' => isset( $data['form_success_msg_color'] ) ? $data['form_success_msg_color'] : '',
					'class' => 'form-control scolor color ',
					'before' => '<div class="fc-6" >',
					'after' => '</div>',
					'default_value' => '#209620',

				)
			);
			$form->add_element(
				'text', 'form_error_msg_color', array(

					'lable' => esc_html__( 'Error Message Color', 'woo-delivery-area-pro' ),
					'value' => isset( $data['form_error_msg_color'] ) ? $data['form_error_msg_color'] : '',
					'class' => 'form-control scolor color ',
					'before' => '<div class="fc-6" >',
					'after' => '</div>',
					'default_value' => '#ff0000',

				)
			);

			$form->add_element(
				'text', 'form_button_color', array(

					'lable' => esc_html__( 'Button Text Color', 'woo-delivery-area-pro' ),
					'value' => isset( $data['form_button_color'] ) ? $data['form_button_color'] : '',
					'class' => 'form-control scolor color ',
					'before' => '<div class="fc-6" >',
					'id'    => 'form_button_color',
					'after' => '</div>',
					'default_value' => '#fff',
				)
			);

			$form->add_element(
				'text', 'form_button_bgcolor', array(

					'lable' => esc_html__( 'Button Background Color', 'woo-delivery-area-pro' ),
					'value' => isset( $data['form_button_bgcolor'] ) ? $data['form_button_bgcolor'] : '',
					'class' => 'form-control scolor color ',
					'before' => '<div class="fc-6" >',
					'id'    => 'form_button_bgcolor',
					'after' => '</div>',
					'default_value' => '#a46497',
				)
			);
			$form->add_element(
				'checkbox', 'enable_locate_me_btn', array(

					'lable' => esc_html__( 'Enable Locate Me Button ', 'woo-delivery-area-pro' ),
					'value' => 'true',
					'current' => isset( $data['enable_locate_me_btn'] ) ? $data['enable_locate_me_btn'] : '',
					'class' => 'chkbox_class ',
					'default_value' => 'true',

				)
			);

			$form->add_element(
				'checkbox', 'enable_product_listing', array(

					'lable' => esc_html__( 'Enable Product Listing ', 'woo-delivery-area-pro' ),
					'value' => 'true',
					'current' => isset( $data['enable_product_listing'] ) ? $data['enable_product_listing'] : '',
					'class' => 'chkbox_class ',
					'default_value' => 'true',

				)
			);
			$form->add_element(
				'text', 'product_listing_error', array(

					'lable' => esc_html__( 'Product Listing Error Message ', 'woo-delivery-area-pro' ),
					'value' => isset( $data['product_listing_error'] ) ? $data['product_listing_error'] : '',
					'class' => 'chkbox_class enable_product_listing ',
					'show'  => false,
					'placeholder' => esc_html__( 'Please select at least one product.', 'woo-delivery-area-pro' ),
					'default_value' => esc_html__( 'Please select at least one product.', 'woo-delivery-area-pro' ),
				)
			);

			$form->add_element(
				'text', 'can_be_delivered_redirect_url', array(

					'lable' => esc_html__( 'Delivery Availalble Redirect URL', 'woo-delivery-area-pro' ),
					'value' => isset( $data['can_be_delivered_redirect_url'] ) ? $data['can_be_delivered_redirect_url'] : '',
					'class' => 'chkbox_class can_be_delivered_redirect_url',
					'show'  => false,
					'desc' => esc_html__( 'Please enter URL where site needs to redirect when area specified by user is available for delivery i.e it comes under your delivery area. For eg. you can set URL of your shop page here. This redirection works on global shortcode form only not from default woocommerce pages. If redirect url is not specified the notifiction message is displayed by default.', 'woo-delivery-area-pro' ),
					'default_value' => '',
					'placeholder' => esc_html__( 'Enter URL for redirecting when delivery is possible.', 'woo-delivery-area-pro' ),
				)
			);

			$form->add_element(
				'text', 'cannot_be_delivered_redirect_url', array(

					'lable' => esc_html__( 'Delivery Not Availalble Redirect URL', 'woo-delivery-area-pro' ),
					'value' => isset( $data['cannot_be_delivered_redirect_url'] ) ? $data['cannot_be_delivered_redirect_url'] : '',
					'class' => 'chkbox_class cannot_be_delivered_redirect_url',
					'show'  => false,
					'desc' => esc_html__( 'Please enter URL where site needs to redirect when delivery is not possible in the area specified by user. For eg. you can set URL of your any custom page here displaying a sorry message. This redirection works on global shortcode form only not from default woocommerce pages.  If redirect url is not specified the notifiction message is displayed by default.', 'woo-delivery-area-pro' ),
					'default_value' => '',
					'placeholder' => esc_html__( 'Enter URL for redirecting when delivery is not possible.', 'woo-delivery-area-pro' ),
				)
			);

			$form->add_element(
				'text', 'product_listing_error', array(

					'lable' => esc_html__( 'Product Listing Error Message ', 'woo-delivery-area-pro' ),
					'value' => isset( $data['product_listing_error'] ) ? $data['product_listing_error'] : '',
					'class' => 'chkbox_class enable_product_listing ',
					'show'  => false,
					'placeholder' => esc_html__( 'Please select at least one product.', 'woo-delivery-area-pro' ),
					'default_value' => esc_html__( 'Please select at least one product.', 'woo-delivery-area-pro' ),
				)
			);


			$form->add_element(
				'group', 'product_delivery_area_shortcode', array(

					'value' => esc_html__( 'Global Delivery Area Map ( Using Shortcode ) Settings', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-12">',
					'after' => '</div>',
				)
			);

			
		$language = array(
			'en' => esc_html__( 'ENGLISH', 'woo-delivery-area-pro' ),
			'ar' => esc_html__( 'ARABIC', 'woo-delivery-area-pro' ),
			'eu' => esc_html__( 'BASQUE', 'woo-delivery-area-pro' ),
			'bg' => esc_html__( 'BULGARIAN', 'woo-delivery-area-pro' ),
			'bn' => esc_html__( 'BENGALI', 'woo-delivery-area-pro' ),
			'ca' => esc_html__( 'CATALAN', 'woo-delivery-area-pro' ),
			'cs' => esc_html__( 'CZECH', 'woo-delivery-area-pro' ),
			'da' => esc_html__( 'DANISH', 'woo-delivery-area-pro' ),
			'de' => esc_html__( 'GERMAN', 'woo-delivery-area-pro' ),
			'el' => esc_html__( 'GREEK', 'woo-delivery-area-pro' ),
			'en-AU' => esc_html__( 'ENGLISH (AUSTRALIAN)', 'woo-delivery-area-pro' ),
			'en-GB' => esc_html__( 'ENGLISH (GREAT BRITAIN)', 'woo-delivery-area-pro' ),
			'es' => esc_html__( 'SPANISH', 'woo-delivery-area-pro' ),
			'fa' => esc_html__( 'FARSI', 'woo-delivery-area-pro' ),
			'fi' => esc_html__( 'FINNISH', 'woo-delivery-area-pro' ),
			'fil' => esc_html__( 'FILIPINO', 'woo-delivery-area-pro' ),
			'fr' => esc_html__( 'FRENCH', 'woo-delivery-area-pro' ),
			'gl' => esc_html__( 'GALICIAN', 'woo-delivery-area-pro' ),
			'gu' => esc_html__( 'GUJARATI', 'woo-delivery-area-pro' ),
			'hi' => esc_html__( 'HINDI', 'woo-delivery-area-pro' ),
			'hr' => esc_html__( 'CROATIAN', 'woo-delivery-area-pro' ),
			'hu' => esc_html__( 'HUNGARIAN', 'woo-delivery-area-pro' ),
			'id' => esc_html__( 'INDONESIAN', 'woo-delivery-area-pro' ),
			'it' => esc_html__( 'ITALIAN', 'woo-delivery-area-pro' ),
			'iw' => esc_html__( 'HEBREW', 'woo-delivery-area-pro' ),
			'ja' => esc_html__( 'JAPANESE', 'woo-delivery-area-pro' ),
			'kn' => esc_html__( 'KANNADA', 'woo-delivery-area-pro' ),
			'ko' => esc_html__( 'KOREAN', 'woo-delivery-area-pro' ),
			'lt' => esc_html__( 'LITHUANIAN', 'woo-delivery-area-pro' ),
			'lv' => esc_html__( 'LATVIAN', 'woo-delivery-area-pro' ),
			'ml' => esc_html__( 'MALAYALAM', 'woo-delivery-area-pro' ),
			'it' => esc_html__( 'ITALIAN', 'woo-delivery-area-pro' ),
			'mr' => esc_html__( 'MARATHI', 'woo-delivery-area-pro' ),
			'nl' => esc_html__( 'DUTCH', 'woo-delivery-area-pro' ),
			'no' => esc_html__( 'NORWEGIAN', 'woo-delivery-area-pro' ),
			'pl' => esc_html__( 'POLISH', 'woo-delivery-area-pro' ),
			'pt' => esc_html__( 'PORTUGUESE', 'woo-delivery-area-pro' ),
			'pt-BR' => esc_html__( 'PORTUGUESE (BRAZIL)', 'woo-delivery-area-pro' ),
			'pt-PT' => esc_html__( 'PORTUGUESE (PORTUGAL)', 'woo-delivery-area-pro' ),
			'ro' => esc_html__( 'ROMANIAN', 'woo-delivery-area-pro' ),
			'ru' => esc_html__( 'RUSSIAN', 'woo-delivery-area-pro' ),
			'sk' => esc_html__( 'SLOVAK', 'woo-delivery-area-pro' ),
			'sl' => esc_html__( 'SLOVENIAN', 'woo-delivery-area-pro' ),
			'sr' => esc_html__( 'SERBIAN', 'woo-delivery-area-pro' ),
			'sv' => esc_html__( 'SWEDISH', 'woo-delivery-area-pro' ),
			'tl' => esc_html__( 'TAGALOG', 'woo-delivery-area-pro' ),
			'ta' => esc_html__( 'TAMIL', 'woo-delivery-area-pro' ),
			'te' => esc_html__( 'TELUGU', 'woo-delivery-area-pro' ),
			'th' => esc_html__( 'THAI', 'woo-delivery-area-pro' ),
			'tr' => esc_html__( 'TURKISH', 'woo-delivery-area-pro' ),
			'uk' => esc_html__( 'UKRAINIAN', 'woo-delivery-area-pro' ),
			'vi' => esc_html__( 'VIETNAMESE', 'woo-delivery-area-pro' ),
			'zh-CN' => esc_html__( 'CHINESE (SIMPLIFIED)', 'woo-delivery-area-pro' ),
			'zh-TW' => esc_html__( 'CHINESE (TRADITIONAL)', 'woo-delivery-area-pro' ),
			);

			$form->add_element( 'select', 'wpdap_language', array(
				'lable' => esc_html__( 'Map Language', 'woo-delivery-area-pro' ),
				'current' => isset($data[ 'wpdap_language' ]) ? $data[ 'wpdap_language' ] : 'en',
				'desc' => esc_html__( 'Choose your language for map. Default is English.', 'woo-delivery-area-pro' ),
				'options' => $language,
				'before' => '<div class="fc-4">',
				'after' => '</div>',
			));

			$form->add_element(
				'text', 'shortcode_map_title', array(
					'lable' => esc_html__( 'Delivey Area Map Title', 'woo-delivery-area-pro' ),
					'value' => isset( $data['shortcode_map_title'] ) ? $data['shortcode_map_title'] : '',
					'class' => 'form-control',
					'placeholder' => esc_html__( 'Enter Delivery Area Map Title', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-6" >',
					'after' => '</div>',
					'default_value' => '',
				)
			);
			$form->add_element(
				'text', 'shortcode_map_description', array(
					'lable' => esc_html__( 'Delivery Area Map Description', 'woo-delivery-area-pro' ),
					'value' => isset( $data['shortcode_map_description'] ) ? $data['shortcode_map_description'] : '',
					'class' => 'form-control',
					'placeholder' => esc_html__( 'Enter Delivery Area Map Description', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-6" >',
					'after' => '</div>',
				)
			);

			$form->add_element(
				'text', 'shortcode_map_width', array(

					'lable' => esc_html__( ' Enter Google Map Width', 'woo-delivery-area-pro' ),
					'value' => isset( $data['shortcode_map_width'] ) ? $data['shortcode_map_width'] : '',
					'class' => 'form-control',
					'placeholder' => esc_html__( 'Enter Google Map Width', 'woo-delivery-area-pro' ),
					'desc' => esc_html__( 'Enter here the map width in pixel. Leave it blank for 100% width.', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-6" >',
					'after' => '</div>',
				)
			);
			$form->add_element(
				'text', 'shortcode_map_height', array(
					'lable' => esc_html__( ' Enter Google Map height', 'woo-delivery-area-pro' ),
					'value' => isset( $data['shortcode_map_height'] ) ? $data['shortcode_map_height'] : '',
					'class' => 'form-control',
					'placeholder' => esc_html__( 'Enter Google Map Height', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-6" >',
					'after' => '</div>',
				)
			);

			$form->add_element(
				'number', 'shortcode_map_zoom_level', array(
					'lable' => esc_html__( ' Enter Google Map Zoom Level', 'woo-delivery-area-pro' ),
					'value' => isset( $data['shortcode_map_zoom_level'] ) ? $data['shortcode_map_zoom_level'] : '',
					'class' => 'form-control',
					'placeholder' => esc_html__( 'Enter Google Map Zoom Level', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-6" >',
					'after' => '</div>',
					'default_value' => 5,
				)
			);
			$form->add_element(
				'text', 'shortcode_map_center_lat', array(

					'lable' => esc_html__( ' Enter Map Center Latitude', 'woo-delivery-area-pro' ),
					'value' => isset( $data['shortcode_map_center_lat'] ) ? $data['shortcode_map_center_lat'] : '',
					'class' => 'form-control',
					'placeholder' => esc_html__( 'Enter Map Center Latitude', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-6" >',
					'after' => '</div>',
					'default_value' => 40.730610,
				)
			);
			$form->add_element(
				'text', 'shortcode_map_center_lng', array(
					'lable' => esc_html__( ' Enter Map Center Longitude', 'woo-delivery-area-pro' ),
					'value' => isset( $data['shortcode_map_center_lng'] ) ? $data['shortcode_map_center_lng'] : '',
					'class' => 'form-control',
					'placeholder' => esc_html__( 'Enter Map Center Longitude', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-6" >',
					'after' => '</div>',
					'default_value' => -73.935242,
				)
			);
			$form->add_element(
				'textarea', 'shortcode_map_style', array(
					'lable' => esc_html__( ' Enter Snazzy Map Google Map Style', 'woo-delivery-area-pro' ),
					'value' => isset( $data['shortcode_map_style'] ) ? $data['shortcode_map_style'] : '',
					'class' => 'form-control',
					'placeholder' => esc_html__( 'Enter Snazzy Map Google Map Style', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-6" >',
					'after' => '</div>',
				)
			);
			$marker_img_id = (isset( $data[ 'marker_img_attachment_id' ] ) ) ? $data[ 'marker_img_attachment_id' ]  : '';
			$marker_desc =   esc_html__('Upload custom marker icon which show on global delivery area map.','woo-delivery-area-pro');

			$form->add_element( 'image_picker', 'marker_img', array(
				'id' => 'marker_img',
				'lable' => esc_html__( 'Marker icon', 'woo-delivery-area-pro' ),
				'src' => (isset( $data['marker_img'] ) ) ? $data['marker_img']  : '',
				'attachment_id' => $marker_img_id,
				'required' => false,
				'choose_button' => esc_html__( 'Upload Icon Image', 'woo-delivery-area-pro' ),
				'remove_button' => esc_html__( 'Remove Icon','woo-delivery-area-pro' ),
				'desc' => $marker_desc

			)); 

			$form->add_element(
				'submit', 'WCRP_save_settings', array(
					'value' => esc_html__( 'Save Settings ', 'woo-delivery-area-pro' ),
					'before' => '<div class="fc-2">',
					'after' => '</div>',
				)
			);

			$form->add_element(
				'hidden', 'operation', array(
					'value' => 'save',
				)
			);
			$form->add_element(
				'hidden', 'hidden_zip_template', array(
					'value' => !empty($data['default_templates']['zipcode']) ? $data['default_templates']['zipcode'] : 'default',
					'id' => 'hidden_zip_template',
				)
			);
			$form->add_element(
				'hidden', 'hidden_shortcode_template', array(
					'value' => !empty($data['default_templates']['shortcode']) ? $data['default_templates']['shortcode'] : 'default'  ,
					'id' => 'hidden_shortcode_template',
				)
			);
			if ( isset( $_GET['doaction'] ) && 'edit' == sanitize_text_field( $_GET['doaction'] ) ) {

				$form->add_element(
					'hidden', 'entityID', array(
						'value' => intval( wp_unslash( sanitize_text_field( $_GET['id'] ) ) ),
					)
				);
			}
			$form->render();
