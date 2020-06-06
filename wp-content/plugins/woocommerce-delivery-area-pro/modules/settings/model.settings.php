<?php
/**
 * Class: WDAP_Model_Settings
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 1.0.0
 * @package woo-delivery-area-pro
 */

if ( ! class_exists( 'WDAP_Model_Settings' ) ) {

	/**
	 * Setting model for Plugin Options.
	 *
	 * @package woo-delivery-area-pro
	 * @author Flipper Code <hello@flippercode.com>
	 */

	class WDAP_Model_Settings extends FlipperCode_Model_Base {

		function __construct() {}

		/**
		 * Admin menu for Settings Operation
		 *
		 * @return array Admin menu navigation(s).
		 */
		function navigation() {
			return array(
				'wdap_setting_settings' => esc_html__( 'Plugin Settings', 'woo-delivery-area-pro' ),
			);
		}

		/**
		 * Add or Edit Operation.
		 */
		function save() {

			$entityID = '';

			if ( isset( $_REQUEST['_wpnonce'] ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) );
			}

			// Nonce Verification
			if ( isset( $nonce ) and ! wp_verify_nonce( $nonce, 'wpgmp-nonce' ) ) {
				die( 'Please reload page and submit the form again...' );
			}

			// Permission Verification
			if ( ! current_user_can( 'administrator' ) ) {
				die( 'You are not allowed to save changes!' );
			}

			// Perform Validations
			$this->verify( $_POST );
			if ( empty( $this->errors ) ) {
				if ( isset( $_POST['enable_retrict_country'] ) && empty( $_POST['wdap_country_restriction_listing'] ) ) {
					$this->errors[] = esc_html__( 'Please select at least one country.', 'woo-delivery-area-pro' );
				}
				if ( empty( $_POST['wdap_map_height'] ) ) {
					$this->errors[] = esc_html__( 'Please enter map height.', 'woo-delivery-area-pro' );
				}
				if ( empty( $_POST['shortcode_map_height'] ) ) {
					$this->errors[] = esc_html__( 'Please enter shortcode map height.', 'woo-delivery-area-pro' );
				}
			}

			if ( is_array( $this->errors ) and ! empty( $this->errors ) ) {
				$this->throw_errors();
			}
			if ( isset( $_POST['entityID'] ) ) {
				$entityID = intval( wp_unslash( $_POST['entityID'] ) );
			}

			if ( $entityID > 0 ) {
				$where[ $this->unique ] = $entityID;
			} else {
				$where = '';
			}

			$temp_data = $_POST;

			$fields_to_sanitize = array( 'wdap_map_width', 'wdap_map_height', 'wdap_map_zoom_level', 'wdap_map_center_lat', 'wdap_map_center_lng', 'wdap_map_style', 'wdap_empty_zip_code', 'wdap_order_restrict_error', 'wdap_check_buttonlbl', 'wdap_frontend_desc', 'avl_button_color', 'avl_button_bgcolor', 'success_msg_color', 'error_msg_color', 'shortcode_form_title', 'check_buttonPlaceholder', 'shortcode_form_description', 'wdap_address_empty', 'address_not_shipable', 'address_shipable', 'form_success_msg_color', 'form_error_msg_color', 'wdap_form_buttonlbl', 'form_button_color', 'form_button_bgcolor', 'product_listing_error', 'shortcode_map_title', 'shortcode_map_description', 'shortcode_map_width', 'shortcode_map_height', 'shortcode_map_zoom_level', 'shortcode_map_center_lat', 'shortcode_map_center_lng', 'shortcode_map_style', 'wdap_error_invalid','wdap_checkout_buttonlbl', 'wdap_shop_error_notavailable','wdap_shop_error_available','wdap_shop_error_invalid','wdap_product_error_notavailable','wdap_product_error_available','wdap_product_error_invalid','wdap_cart_error_notavailable','wdap_cart_error_available','wdap_cart_error_invalid','wdap_cart_error_th','wdap_cart_error_summary','wdap_checkout_error_notavailable','wdap_checkout_error_available','wdap_checkout_error_invalid','wdap_checkout_error_th','wdap_checkout_error_summary' );

			$toSave = array();
			foreach ( $fields_to_sanitize as $field ) {
				if ( isset( $temp_data[ $field ] ) && ! empty( $temp_data[ $field ] ) ) {
					$toSave[ $field ] = sanitize_text_field( $temp_data[ $field ] );
					unset( $temp_data[ $field ] );
				}
			}

			if ( ! empty( $temp_data['can_be_delivered_redirect_url'] ) ) {
				  $toSave['can_be_delivered_redirect_url'] = esc_url( $temp_data['can_be_delivered_redirect_url'] );
			}
			if ( ! empty( $temp_data['cannot_be_delivered_redirect_url'] ) ) {
				  $toSave['can_be_delivered_redirect_url'] = esc_url( $temp_data['cannot_be_delivered_redirect_url'] );

			}

			$data = array_merge( $toSave, $temp_data );
			if ( empty( $data['default_templates']['zipcode'] ) ) {
				$data['default_templates']['zipcode'] = $data['hidden_zip_template'];
			}
			if ( empty( $data['default_templates']['shortcode'] ) ) {
				$data['default_templates']['shortcode'] = $data['hidden_shortcode_template'];
			}
			if ( empty( $data['enable_map_bound'] ) ) {
				$data['enable_map_bound'] = 'no';
			}
			if ( empty( $data['enable_polygon_on_map'] ) ) {
				$data['enable_polygon_on_map'] = 'no';
			}
			if ( empty( $data['enable_markers_on_map'] ) ) {
				$data['enable_markers_on_map'] = 'no';
			}

			update_option( 'wp-delivery-area-pro', wp_unslash( $data ) );
			$response['success'] = esc_html__( 'Setting(s) saved successfully.', 'woo-delivery-area-pro' );
			return $response;
		}

			/**
			 * Delete rule object by id.
			 */
		public function delete() {
			if ( isset( $_GET['id'] ) ) {
				$id = intval( wp_unslash( $_GET['id'] ) );
				$connection = FlipperCode_Database::connect();
				$this->query = $connection->prepare( "DELETE FROM $this->table WHERE $this->unique='%d'", $id );
				return FlipperCode_Database::non_query( $this->query, $connection );
			}
		}
	}
}
