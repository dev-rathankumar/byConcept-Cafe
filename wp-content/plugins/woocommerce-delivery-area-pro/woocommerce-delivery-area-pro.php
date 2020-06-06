<?php
/**
 * Woocommerce Delivery Area Pro
 *
 * @package woo-delivery-area-pro
 * @author Flipper Code <hello@flippercode.com>
 * @copyright 2020 flippercode
 *
 * @wordpress-plugin
 * Plugin Name: Woocommerce Delivery Area Pro
 * Plugin URI: http://www.flippercode.com/
 * Description:  A woocommerce extention that allows site users to check product delivery / shipping availablity of  products by specifying zipcode. It also displays your woocommerce shop delivery areas on frontend using google maps in form of markers and drawings.
 * Version: 2.0.6
 * Author: flippercode
 * Author URI: http://www.flippercode.com/
 * Text Domain: woo-delivery-area-pro
 * Domain Path: /lang/
 */

if ( ! class_exists( 'FC_Plugin_Base' ) ) {
	$pluginClass = plugin_dir_path( __FILE__ ) . '/core/class.plugin.php';
	if ( file_exists( $pluginClass ) ) {
		include( $pluginClass );
	}
}



if ( ! class_exists( 'WDAP_Delivery_Area' ) && class_exists( 'FC_Plugin_Base' ) ) {

	/**
	 * Main plugin class
	 *
	 * @author Flipper Code <hello@flippercode.com>
	 * @package woo-delivery-area-pro
	 */

	class WDAP_Delivery_Area extends FC_Plugin_Base {

		/**
		 * Class Vars
		 */
		private $dboptions;
		private $applyOn;
		static  $continent_list;
		static  $sub_continent_list;
		static  $ctrycodewithcont;
		private $collections;
		private $current_request_response;
		private $is_country_restrict = false;
		private $ajax_params = array();
		private $is_via_shortcode = false;
		private $is_shortcode_filter_enable;
		private $is_shop_filter_enable;
		private $is_product_filter_enable;
		private $is_search_addon_active;

		/**
		 * Class Constructor
		 */
		public function __construct() {

			$wooInstalled = in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins' ) );
			if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
			}
			$networkActive = ( is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) ? true : false;
			$is_woocommerce_not_installed = ( ! $wooInstalled && ! $networkActive ) ? true : false;
			if ( $is_woocommerce_not_installed ) {
				 add_action( 'admin_notices', array( $this, 'wdap_woocommerce_missing' ) );

			} else {

				parent::__construct( $this->_plugin_definition() );
				$this->dboptions = maybe_unserialize( get_option( 'wp-delivery-area-pro' ) );
				$this->applyOn   = isset($this->dboptions['apply_on']['checkedvalue']) ? $this->dboptions['apply_on']['checkedvalue'] : array();
				$search_key = 'woocommerce-delivery-area-search-results/woocommerce-delivery-area-search-results.php';
				$search_addon_active = in_array( $search_key, get_option( 'active_plugins' ) );
				$network_search_addon_active = ( is_plugin_active_for_network( $search_key ) ) ? true : false;
				$is_search_addon_installed = ( $search_addon_active || $network_search_addon_active ) ? true : false;
				$this->is_shortcode_filter_enable = ( isset( $this->dboptions['enable_filter_by_zipcode'] ) && ( $is_search_addon_installed ) ) ? true : false;
				$this->is_shop_filter_enable = ( isset( $this->dboptions['enable_shop_filter_zipcode'] ) && ( $is_search_addon_installed ) ) ? true : false;
				$this->is_product_filter_enable = ( isset( $this->dboptions['enable_product_filter_zipcode'] ) && ( $is_search_addon_installed ) ) ? true : false;
				$this->wdap_setup_class_vars();
				$this->register_plugin_hooks();
			}
		}

		function _plugin_definition() {

			$this->pluginPrefix = 'wdap';
			$pluginClasses = array( 'wdap-form.php', 'wdap-controller.php', 'wdap-model.php', 'wdap-fresh-settings.php' );
			$pluginModules = array( 'overview', 'collection', 'settings', 'backup', 'tools' );
			$pluginCssFilesFrontEnd = array( 'wdap-frontend.css', 'wdap-template.css', 'select2.css', 'select2-bootstrap.css' );
			$pluginCssFilesBackendEnd = array(
				'font-awesome.min.css',
				'wdap-backend.css',
				'backend.css',
				'select2.css',
				'select2-bootstrap.css',
				'wdap-template.css',
			);
			$pluginJsFilesFrontEnd = array( 'wdap-frontend.js', 'select2.js' );
			$pluginJsFilesBackEnd = array( 'select2.js', 'wdap-backend.js' );
			$pluginData = array(
				'childFileRefrence' => __FILE__,
				'childClassRefrence' => __CLASS__,
				'pluginPrefix' => 'wdap',
				'pluginDirectory' => plugin_dir_path( __FILE__ ),
				'pluginDirectoryBaseName' => basename( dirname( __FILE__ ) ),
				'pluginTextDomain' => 'woo-delivery-area-pro',
				'pluginURL' => plugin_dir_url( __FILE__ ),
				'dboptions' => 'wp-delivery-area-pro',
				'controller' => 'WDAP_Controller',
				'model' => 'WDAP_Model',
				'pluginLabel' => 'WP Delivery Area Pro',
				'pluginClasses' => $pluginClasses,
				'pluginmodules' => $pluginModules,
				'pluginmodulesprefix' => 'WDAP_Model_',
				'pluginCssFilesFrontEnd' => $pluginCssFilesFrontEnd,
				'pluginCssFilesBackEnd' => $pluginCssFilesBackendEnd,
				'pluginJsFilesFrontEnd' => $pluginJsFilesFrontEnd,
				'pluginJsFilesBackEnd' => $pluginJsFilesBackEnd,
			);
			return $pluginData;

		}

		function register_plugin_hooks() {

			if ( is_admin() ) {

				add_action( 'admin_init', array( $this, 'wpdap_coordinate_forward_in_js' ) );
				add_action( 'admin_enqueue_scripts', array( $this, 'wdap_admin_enqueue' ) );
				add_action( 'load-post.php', array( $this, 'wdap_collection_meta_boxes_setup' ) );
				add_action( 'load-post-new.php', array( $this, 'wdap_collection_meta_boxes_setup' ) );
				add_action( 'save_post', array( $this, 'wdap_save_delivery_metabox' ), 1, 2 );
				add_action( 'admin_notices', array( $this, 'wdap_feedback_notice' ) );

			} else {
				add_action( 'wp_head', array( $this, 'wdap_dynamic_css' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'wdap_googlemap_api_key' ) );
				add_filter( 'woocommerce_product_tabs', array( $this, 'wdap_woo_extra_tabs' ) );

				if ( ! empty( $this->applyOn ) ) {

					if ( in_array( 'product_page', $this->applyOn ) ) {
						add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'wdap_zipcode_field' ) );
					}

					if ( in_array( 'cart_page', $this->applyOn ) ) {
						add_action( 'woocommerce_cart_totals_before_order_total', array( $this, 'markup_on_cartpage' ) );
					}

					if ( in_array( 'checkout_page', $this->applyOn ) || ( ! empty( $this->dboptions['enable_order_restriction'] ) && $this->dboptions['enable_order_restriction'] ) ) {
						add_action( 'woocommerce_after_checkout_billing_form', array( $this, 'wdap_custom_checkout_field' ) );
						if ( ! empty( $this->dboptions['enable_order_restriction'] ) ) {
							add_action( 'woocommerce_checkout_process', array( $this, 'wdap_custom_checkout_field_process' ) );
						}
					}

					if ( in_array( 'shop_page', $this->applyOn ) ) {
						add_action( 'woocommerce_after_shop_loop_item', array( $this, 'wdap_after_shop_loop_item' ), 10 );
					}

					if ( in_array( 'category_page', $this->applyOn ) ) {
						add_action( 'woocommerce_after_shop_loop_item', array( $this, 'wdap_after_category_loop_item' ), 10 );
					}
					add_filter( 'woocommerce_cart_item_class', array( $this, 'wdap_woocommerce_cart_item_class' ), 10, 3 );

					if ( ! empty( $this->dboptions['enable_order_restriction'] ) ) {
						add_filter( 'woocommerce_order_button_html', array( $this, 'wdap_change_placeOrder_html' ) );
					}
				}
			}
			add_shortcode( 'delivery_area_form', array( $this, 'wdap_custom_checking_form' ) );
			add_shortcode( 'delivery_areas', array( $this, 'wdap_polygon_markup' ) );
			add_action( 'init', array( $this, 'wpdap_get_collections' ) );
			add_action( 'wp_ajax_wdap_ajax_call', array( $this, 'wdap_ajax_call' ) );
			add_action( 'wp_ajax_nopriv_wdap_ajax_call', array( $this, 'wdap_ajax_call' ) );

		}



		function wdap_save_delivery_metabox( $post_id, $post ) {

			if ( empty( $post_id ) || empty( $post ) ) {
				return;
			}

			if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
				return;
			}

			// Nonce Verification
			if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( $_POST['woocommerce_meta_nonce'], 'woocommerce_save_data' ) ) {
				return;
			}

			if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
				return;
			}

			// Authorization
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			$checked_collection_id = array();
			$selected_collections_id = $_POST['collection_listing'];
			$all_collections_id = $_POST['apply_on_all_products'];
			$all_collections_id_array = explode( ',', $all_collections_id );
			$checked_collection_id = array_merge( $all_collections_id_array, $selected_collections_id );
			$need_to_save = array(
				'zip_form' => $_POST['enable_zipcode_form'],
				'avl_tab'  => $_POST['enable_product_avalibility'],
				'checked_collection' => $checked_collection_id,
			);
			update_post_meta( $post_id, 'wdap_current_post_setup', serialize( $need_to_save ) );
			$collections = $this->collections;
			$selected_collections = array();
			$existing_selected_collection = array();

			foreach ( $collections as $collection ) {
				if ( $collection->applyon == 'Selected Products' ) {
					$selected_collections[ $collection->id ] = $collection;
					if ( in_array( $post_id, maybe_unserialize( $collection->chooseproducts ) ) ) {
						$existing_selected_collection[] = $collection->id;
					}
				}
			}
			$existing_collection_length = count( $existing_selected_collection );
			$selected_collection_length = count( $selected_collections_id );
			$collections_to_remove_product = array_diff( $existing_selected_collection, $selected_collections_id );
			$collections_to_add_product = array_diff( $selected_collections_id, $existing_selected_collection );
			if ( $selected_collection_length > $existing_collection_length ) {

				if ( $existing_collection_length > 0 ) {
					if ( empty( $collections_to_remove_product ) ) {
						$this->wdap_add_product_collection( $post_id, $selected_collections_id, $selected_collections );
					}
					if ( ! empty( $collections_to_remove_product ) && ! empty( $collections_to_add_product ) ) {
						$this->wdap_remove_product_collection( $post_id, $collections_to_remove_product, $selected_collections );
						$this->wdap_add_product_collection( $post_id, $collections_to_add_product, $selected_collections );
					}
				}
				// Add
				if ( $existing_collection_length == 0 ) {
					$this->wdap_add_product_collection( $post_id, $selected_collections_id, $selected_collections );
				}
			} elseif ( $selected_collection_length < $existing_collection_length ) {
				// Remove
				if ( $selected_collection_length > 0 ) {
					$differ_collections_id = array_diff( $existing_selected_collection, $selected_collections_id );
					$this->wdap_remove_product_collection( $post_id, $differ_collections_id, $selected_collections );
				}
				if ( ! empty( $collections_to_add_product ) ) {
					$this->wdap_add_product_collection( $post_id, $collections_to_add_product, $selected_collections );
				}
				if ( empty( $selected_collections_id ) ) {
					$this->wdap_remove_product_collection( $post_id, $existing_selected_collection, $selected_collections );
				}
			} else {
				// Remove
				$this->wdap_remove_product_collection( $post_id, $collections_to_remove_product, $selected_collections );
				// Add
				$this->wdap_add_product_collection( $post_id, $collections_to_add_product, $selected_collections );
			}
		}

		function wdap_add_product_collection( $id, $collections_id, $selected_collections ) {

			global $wpdb;
			foreach ( $collections_id as $key => $collection_id ) {
				if ( array_key_exists( $collection_id, $selected_collections ) ) {
					$actual_collection = $selected_collections[ $collection_id ];
					$chooseproducts = maybe_unserialize( $actual_collection->chooseproducts );
					if ( ! in_array( $id, $chooseproducts ) ) {
						$chooseproducts[] = $id;
						$wpdb->update( WDAP_TBL_FORM, array( 'chooseproducts' => serialize( $chooseproducts ) ), array( 'id' => $collection_id ) );
					}
				}
			}

		}

		function wdap_remove_product_collection( $id, $collections_id, $selected_collections ) {

			global $wpdb;
			foreach ( $collections_id as $key => $collection_id ) {
				if ( array_key_exists( $collection_id, $selected_collections ) ) {
					$actual_collection = $selected_collections[ $collection_id ];
					$chooseproducts = maybe_unserialize( $actual_collection->chooseproducts );
					if ( in_array( $id, $chooseproducts ) ) {
						$key = array_search( $id, $chooseproducts );
						unset( $chooseproducts[ $key ] );

						$wpdb->update( WDAP_TBL_FORM, array( 'chooseproducts' => serialize( $chooseproducts ) ), array( 'id' => $collection_id ) );
					}
				}
			}
		}

		function wdap_dynamic_css() {

			$style = '';
			$settings = $this->dboptions;
			$class = ! empty( $settings['default_templates']['zipcode'] ) ? $settings['default_templates']['zipcode'] : 'default';
			$error_messgae_color = isset( $settings['error_msg_color'] ) ? stripslashes( wp_strip_all_tags( $settings['error_msg_color'] ) ) : '';
			$success_message_color  = isset( $settings['success_msg_color'] ) ? stripslashes( wp_strip_all_tags( $settings['success_msg_color'] ) ) : '';
			$avl_buttonbg_color = isset( $settings['avl_button_bgcolor'] ) ? stripslashes( wp_strip_all_tags( $settings['avl_button_bgcolor'] ) ) : '';
			$avl_button_color = isset( $settings['avl_button_color'] ) ? stripslashes( wp_strip_all_tags( $settings['avl_button_color'] ) ) : '';
			$style .= '<style type="text/css">';
			if ( is_cart() || is_checkout() ) {
				$style .= 'span.notavilable{color:' . $error_messgae_color . ';}
				span.avilable{color:' . $success_message_color . ';}
				body.woocommerce-checkout .classic #wdapzipsumit { background:' . $avl_buttonbg_color . ';}';
			}
			if ( ( ! empty( $avl_button_color ) || ! empty( $avl_buttonbg_color ) ) ) {
				$style .= '.' . $class . ' input#wdapzipsumit {color:' . $avl_button_color . ';
				  background-color:' . $avl_buttonbg_color . ';}';
			}
			if ( $class == 'smart' ) {
				$style .= 'input#wdapziptextbox {border: 2px solid ' . $avl_buttonbg_color . ';}';
			}
			if ( $class == 'standard' ) {
				$style .= '.standard input.wdapziptextbox {background-color:' . $avl_buttonbg_color . ';}';
				$style .= '.standard .wdapziptextbox::-webkit-input-placeholder, .standard input.wdapziptextbox {color: #ffffff;}';
				$style .= '.standard input.wdapziptextbox:focus {background: ' . $avl_buttonbg_color . ';
				color: ' . $avl_button_color . ';
				}';
				$style .= 'body.woocommerce-checkout .standard #wdapzipsumit {background-color:' . $avl_buttonbg_color . ' !important;}';
			}
			$style .= '.classic .wdapziptextbox {border-color:' . $avl_buttonbg_color . ';}';
			$style .= ' .default #wdapziptextbox{border:1px solid' . $avl_buttonbg_color . ';}';
			$style .= '</style>';
			?>
			<style type="text/css"><?php echo wp_kses_post( $style ); ?></style>
			<?php
			global $wpdb,$post;
			$this->collections = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'wdap_collection' );
							$store_locations = array();

			if ( ! empty( $this->collections ) ) {
				foreach ( $this->collections as $collection ) {
					if ( $collection->wdap_map_region == 'by_distance' ) {
						 $address = unserialize( $collection->wdap_map_region_value );
						 $address['address'] = array( json_decode( $address['address'] ) );
						 $avl_products = $this->wdap_get_collection_product_id( $collection );
						 $location = array(
							 'range' => $address['range'],
							 'lat' => $address['address'][0]->lat,
							 'lng' => $address['address'][0]->lng,
							 'product_id' => $avl_products,
						 );
						 $store_locations[] = $location;
					}
				}
			}

			$disable_click_on_woopages = false;
			$check_shop_filter_enable = ( is_shop() && $this->is_shop_filter_enable ) ? true : false;
			$check_product_filter_enable = ( (isset($post) && ($post->post_type == 'product') && is_single() ) && $this->is_product_filter_enable ) ? true : false;
			if ( $check_shop_filter_enable ) {
				$disable_click_on_woopages = true;
			}
			if ( $check_product_filter_enable ) {
				$disable_click_on_woopages = true;
			}

			?>
			<script>
				var store_locations = <?php echo json_encode( $store_locations ); ?>;
				var enable_filter_by_zipcode = <?php echo json_encode( $this->is_shortcode_filter_enable ); ?>;
				var disable_zipcode_checking = <?php echo json_encode( $disable_click_on_woopages ); ?>;
			</script>
			<?php

		}

		/*
		 Sending products id into js for store test
		*/
		function wdap_get_collection_product_id( $collection ) {

			if ( $collection->applyon == 'All Products' ) {
				return 'all';
			}
			if ( $collection->applyon == 'Selected Products' ) {
				$products  = maybe_unserialize( $collection->chooseproducts );
				return $products;
			}

			if ( $collection->applyon == 'all_products_excluding_some' ) {

				$exclude_products  = maybe_unserialize( $collection->exclude_products );
				$remaining_products = array();
				$all_product_args = array(
					'numberposts' => -1,
					'post_status' => array( 'publish' ),
					'post_type' => array( 'product' ),
				);

				$all_products = get_posts( $all_product_args );
				if ( $all_products ) {
					foreach ( $all_products as $product ) {
						if ( ! ( in_array( $product->ID, $exclude_products ) ) ) {
							$remaining_products[] = $product->ID;
						}
					}
				}
				wp_reset_postdata();
				return $remaining_products;

			}

			if ( $collection->applyon == 'selected_categories' ) {

				$categories  = maybe_unserialize( $collection->selectedcategories );
				$cat_products = array();
				$prod_categories = $categories; // category IDs
				$product_args = array(
					'numberposts' => -1,
					'post_status' => array( 'publish' ),
					'post_type' => array( 'product' ),
					'tax_query' => array(
						array(
							'taxonomy' => 'product_cat',
							'field' => 'id',
							'terms' => $prod_categories,
							'operator' => 'IN',
						),
					),

				);

				$products = get_posts( $product_args );
				if ( $products ) {
					foreach ( $products as $product ) {
						$cat_products[] = $product->ID;
					}
				}
				wp_reset_postdata();
				return $cat_products;
			}

		}


		// Adding meta boxes at order meta screen
		function wdap_collection_meta_boxes_setup() {
			add_action( 'add_meta_boxes', array( $this, 'wdap_add_collection_meta_boxes' ) );
		}

		function wdap_add_collection_meta_boxes() {

			global $woocommerce, $post;
			if ( ! empty( $post->post_type ) && ( $post->post_type == 'product' ) ) {
				add_meta_box( 'woo-delivery-area-pro', esc_html__( 'Woo Delivery Area Pro', 'woo-delivery-area-pro' ), array( $this, 'wdap_choose_collection_meta_box' ), 'product', 'side', 'high' );
			}
		}

		function wdap_choose_collection_meta_box( $object, $box ) {

			$product_id    = $object->ID;
			$saved_setting = get_post_meta( $product_id, 'wdap_current_post_setup' );
			$saved_collection_id = array();
			$saved_setting = isset( $saved_setting[0] ) ? maybe_unserialize( $saved_setting[0] ) : array();
			if ( ! empty( $saved_setting['checked_collection'] ) ) {
				$saved_collection_id = isset( $saved_setting['checked_collection'] ) ? $saved_setting['checked_collection'] : array();
			}
			if ( ! empty( $saved_setting ) ) {
				$form_enable = ( $saved_setting['zip_form'] == 'on' ) ? 'checked="checked"' : '';
				$tab_enable  = ( $saved_setting['avl_tab'] == 'on' ) ? 'checked="checked"' : '';
			}
			?>
		   <form action="" method="POST" enctype="multipart/form-data" id="collection_settings">
			   <div class="fc-form-group ">
				   <div class="fc-3">
					   <label for="enable_zipcode_form"><?php echo esc_html__( 'Disable Shipping Enquiry Form', 'woo-delivery-area-pro' ); ?></label>
					   <span class="checkbox ">
						   <input type="checkbox" id="enable_zipcode_form" name="enable_zipcode_form"  class="chkbox_class" 
						   <?php
							if ( isset( $form_enable ) ) {
								echo esc_attr( $form_enable );}
							?>
							 >
					   </span>
				   </div>
			  </div>
			  <div class="fc-form-group ">
				   <div class="fc-3">
					   <label for="enable_product_avalibility"><?php echo esc_html__( 'Disable Product Avalibility Tab', 'woo-delivery-area-pro' ); ?> </label>
					   <span class="checkbox ">
						   <input type="checkbox" id="enable_product_avalibility" name="enable_product_avalibility" class="chkbox_class" 
						   <?php
							if ( isset( $tab_enable ) ) {
								echo esc_attr( $tab_enable );}
							?>
							 >
					   </span>
				   </div>
			  </div>	
			<?php
			if ( ! empty( $this->collections ) ) {
				?>
				<div class="fc-form-group ">
				   <div class="fc-3">
					   <label for="enable_product_listing"><h4><?php echo esc_html__( 'Assign Product To Collection', 'woo-delivery-area-pro' ); ?> </h4></label>
				   </div>
			  </div>
				<?php
			}

			$collections = $this->collections;
			$all_products = array();
			if ( ! empty( $collections ) ) {
				foreach ( $collections as $key => $collection ) {
					$checked  = false;
					$disabled = '';
					if ( $collection->applyon == 'Selected Products' ) {
						$selected_products = isset( $collection->chooseproducts ) ? maybe_unserialize( $collection->chooseproducts ) : array();
						if ( count( $selected_products ) > 0 && in_array( $product_id, $selected_products ) ) {
							$checked = true;
						}
						if ( $checked ) {
							$collectionchecked = ( $checked ) ? ' checked="checked"' : '';
						}
						?>
					<div class="fc-form-group ">
					   <div class="fc-3">
					   <label for="enable_product_listing"><?php echo esc_html( $collection->title ); ?></label>
				       <input type="checkbox" id="enable_product_avalibility" value="<?php echo esc_attr( $collection->id ); ?>" name="collection_listing[]" class="chkbox_class"<?php if ( $checked ) { echo esc_attr( $collectionchecked );}?> >
					   </div>
					</div>
						<?php
					}
				}
			}
			?>
			<input type="hidden" name="apply_on_all_products" value="
			<?php
			if ( ! empty( $all_products ) ) {
				$product_string = implode( ',', $all_products );
				echo esc_attr( $product_string );}
			?>
			">	
		   </form>
			<?php
		}

		function wdap_woocommerce_missing() {
			?>
			<div class="notice notice-error">
				<p><?php esc_html_e( 'WooCommerce is required for Woocommerce Delivery Area Pro plugin to work. Please install and configure woocommerce first.', 'woo-delivery-area-pro' ); ?>
				</p>
			</div>
			<?php
		}

		function wdap_custom_checking_form( $atts ) {
			

			$txtPlaceholder = !empty($this->dboptions['check_buttonPlaceholder']) ? stripslashes( wp_strip_all_tags( $this->dboptions['check_buttonPlaceholder'] ) ) : esc_html__( 'Type Delivery Location (Landmark, Road or Area)', 'woo-delivery-area-pro' );

			$locatlbl = !empty($this->dboptions['wdap_form_locateme']) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_form_locateme'] ) ) : esc_html__( 'Locate Me', 'woo-delivery-area-pro' );

			$locate_me_image = esc_url( WDAP_IMAGES . 'loc.png' );

			$btnlbl = !empty($this->dboptions['wdap_form_buttonlbl']) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_form_buttonlbl'] ) ) : esc_html__( 'Check Availability', 'woo-delivery-area-pro' );

			$design_class = !empty( $this->dboptions['default_templates']['shortcode'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['default_templates']['shortcode'] ) ) : 'default';

			$form_button_bgcolor = !empty($this->dboptions['form_button_bgcolor']) ? stripslashes( wp_strip_all_tags( $this->dboptions['form_button_bgcolor'] ) ) : '';

			$form_button_color = !empty($this->dboptions['form_button_color']) ? stripslashes( wp_strip_all_tags( $this->dboptions['form_button_color'] ) ) : '';
			$html = '';
			$html .= '<style type="text/css">';
			$html .= '.wdap_product_availity_form button.check_availability{background-color:' . $form_button_bgcolor . '; color:' . $form_button_color . '; }
				  .smart .clearfix.first-column{background:' . $form_button_bgcolor . ';}
				  .smart .select2-container .select2-choice,
				  .smart .select2-container--default .select2-selection--single{border-left: 0.2em solid ' . $form_button_bgcolor . ';}
			   .wdap_standard_design.select2-drop-active,
			   .select2-dropdown.wdap_standard_design,
			   .standard .clearfix.first-column, 
			   .standard .select2-container .select2-choice,
			   .standard .select2-dropdown-open.select2-drop-above [class^="select2-choice"],
			   .standard .select2-container--default .select2-selection--single {
   					 background-color:' . $form_button_bgcolor . ';
				}
				.standard div#s2id_wdap_product_list,
				.standard .select2-container--default .select2-selection--single{border-left: 1px solid ' . $form_button_bgcolor . ';}
				.default .select2-container .select2-choice,
				.default.enable_product_listing #wdap_type_location,
				.classic.enable_product_listing #wdap_type_location,
				.default .select2-container--default .select2-selection--single,
				.classic .select2-container--default .select2-selection--single,

				.classic .select2-container .select2-choice{border:1px solid ' . $form_button_bgcolor . ';border-right: 0;}

				';
			$html .= '</style>';
			$random_id = uniqid();
			if ( $design_class == 'standard' ) {
				$locate_me_image = esc_url( WDAP_IMAGES . '352557-321.png' );
			}

			if ( ! empty( $this->dboptions['enable_locate_me_btn'] ) ) {
				$design_class .= ' enable_locate_me_btn';
			}
			if ( ! empty( $this->dboptions['enable_product_listing'] ) ) {
				$design_class .= ' enable_product_listing';
			}
			$html .= '<div class="wdap_product_availity_form ' . esc_attr( $design_class ) .' '.$random_id.'">';
			if ( ! empty( $this->dboptions['shortcode_form_title'] ) ) {
				$html .= '<h1 class="wdap-hero-title">' . stripslashes( wp_strip_all_tags( $this->dboptions['shortcode_form_title'] ) ) . '</h1>';
			}
			$html .= '<div class="clearfix first-column">';
			$html .= '<input  type="text" id="wdap_type_location" class="type-location" name="location" placeholder="' . esc_attr( $txtPlaceholder ) . '" >';
			if ( isset( $this->dboptions['enable_locate_me_btn'] ) ) {

				$html .= '<img src="' . esc_url( $locate_me_image ) . '" class="locate-me locate-me-text" >';
			}
			$html .= '<input type="hidden" name="nonce" value="' . wp_create_nonce( 'wdap_create_nonce' ) . '">
			        <input type="hidden" value="" class="zipcode_check_params"  name="zipcode_check_params" />
			        <input type="hidden" name="convertedzipcode" value="" id="convertedzipcode" class="convertedzipcode">';
			if ( isset( $this->dboptions['enable_product_listing'] ) ) {

				 $args = array(
					 'post_type' => 'product',
					 'posts_per_page' => -1,
				 );
				 $loop = new WP_Query( $args );
				 $html .= '<select class="form_product_list" id="wdap_product_list">
			            <option  value="">' . esc_html__( 'Select Product', 'woo-delivery-area-pro' ) . '</option>';
				 while ( $loop->have_posts() ) :
					$loop->the_post();
					$html .= '<option  value="' . esc_attr( $loop->post->ID ) . '">' . get_the_title( '', '', false ) . '</option>';
				  endwhile;
					wp_reset_postdata();
					$html .= '</select>';
			}
				$html .= '<button name="check_availability" class="check_availability">' . $btnlbl . '</button>
						<input type="hidden" class="unique_form_id" value="'.$random_id.'" />
			    </div>';
				$html .= '<div class="error-container second-column" style="display: none;" >
			    	<div class="error-message">' . esc_html__( 'Please enter your address.', 'woo-delivery-area-pro' ) . '></div>
			    </div>';

			if ( ! empty( $this->dboptions['shortcode_form_description'] ) ) {
				$html .= '<div class="wdap-shortcode-desc" ><span>' . stripslashes( wp_strip_all_tags( $this->dboptions['shortcode_form_description'] ) ) . '</span></div>';
			}

			
			$html .= '</div>';
			return $html;
		}

		function wdap_change_placeOrder_html( $html ) {

			$place_order = !empty($this->dboptions['wdap_checkout_buttonlbl']) ? sanitize_text_field($this->dboptions['wdap_checkout_buttonlbl']) :  esc_html__( 'Place order', 'woo-delivery-area-pro' );

			$place_order_button = apply_filters( 'wdap_change_translation_order_button_text', $place_order );

			return '<input type="submit" class="button new_submit alt" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $place_order_button ) . '" data-value="' . esc_attr( $place_order_button ) . '">';

		}

		function wdap_after_shop_loop_item() {

			global $product;
			if ( is_shop() ) {
				$id = $product->get_id();
				$wdap_current_post_setup = get_post_meta( $id, 'wdap_current_post_setup' );
				if ( ! empty( $wdap_current_post_setup[0] ) ) {
					$wdap_current_post_setup = maybe_unserialize( $wdap_current_post_setup[0] );
					if ( ! ( ! empty( $wdap_current_post_setup['zip_form'] ) && $wdap_current_post_setup['zip_form'] == 'on' ) ) {
						$this->wdap_zip_search_markup( $id, '' );
					}
				} else {
					$this->wdap_zip_search_markup( $id, '' );
				}
			}
		}


		function wdap_after_category_loop_item() {

			global $product,$wp_query;
			if ( is_product_category() ) {

				$id = $product->get_id();
				$wdap_current_post_setup = get_post_meta( $id, 'wdap_current_post_setup',true );
				if(!empty($wdap_current_post_setup)){
					$wdap_current_post_setup = maybe_unserialize( $wdap_current_post_setup );
				}
				$term_id = $wp_query->get_queried_object()->term_id;

				$exclude_categories = !empty($this->dboptions['excludecategories']) ? $this->dboptions['excludecategories'] : array();

				if(count($exclude_categories)>0){
					if(!(in_array($term_id, $exclude_categories))){
						
						if ( ! empty( $wdap_current_post_setup )) {

							if ( ! ( ! empty( $wdap_current_post_setup['zip_form'] ) && $wdap_current_post_setup['zip_form'] == 'on' ) ) {
								$this->wdap_zip_search_markup( $id, '' );
							}
						}else{
						   $this->wdap_zip_search_markup( $id, '' );
						}

					}

				}else{

					if ( ! empty( $wdap_current_post_setup )) {
						if ( ! ( ! empty( $wdap_current_post_setup['zip_form'] ) && $wdap_current_post_setup['zip_form'] == 'on' ) ) {
							$this->wdap_zip_search_markup( $id, '' );
						}
					}else{
					  $this->wdap_zip_search_markup( $id, '' );
					}
				}
			}
		}

		function wdap_woocommerce_cart_item_class( $cart_item, $cart_item1, $cart_item_key ) {

			if($cart_item1['data']->is_virtual()){
				return $cart_item;
			}
			$id='';
			if(!empty($cart_item1['variation_id'])){
				$variation_id = $cart_item1['variation_id'];
				$v = new WC_Product_Variation($variation_id);
				if(!empty($v->get_parent_id())){

					$id = $v->get_parent_id();

				}else{

				 $id = $cart_item1['product_id'];

				}
			}else{
				$id = $cart_item1['product_id'];
			}
			return $id. ' ' . $cart_item;
		}

		function markup_on_cartpage() {

			$cartdata = WC()->cart->get_cart();
			$ids = array();
			foreach ( $cartdata as $key => $item ) {
				
				if($item['data']->is_virtual() ) { continue; }


				if(!empty($item['variation_id'])){
					$variation_id = $item['variation_id'];
					$v = new WC_Product_Variation($variation_id);

					if(!empty($v->get_parent_id())){

						$ids[] = $v->get_parent_id();

					}else{

					$ids[] = $item['product_id'];

					}
				}else{
					$ids[] = $item['product_id'];
				}

			}
			$this->wdap_zip_search_markup( $ids, '' );
		}

		function wdap_googlemap_api_key( $hook_suffix ) {

			global $post;
			if ( ! empty( $this->dboptions['wdap_googleapikey'] ) ) {
				$googlemapkey = $this->dboptions['wdap_googleapikey'];

				$language = isset($this->dboptions['wpdap_language']) ? $this->dboptions[ 'wpdap_language' ] : 'en';
				$language = apply_filters('wdap_map_lang',$language );


				if ( $googlemapkey ) {
					wp_enqueue_script( 'front-gmaps', 'https://maps.googleapis.com/maps/api/js?key=' . stripslashes( wp_strip_all_tags( $googlemapkey ) ) . '&libraries=drawing,geometry,places&language='.$language, '', '', false );
				}
			}
		}

		function wdap_admin_enqueue( $hook_suffix ) {

			global $post_type;
			if ( ! empty( $this->dboptions['wdap_googleapikey'] ) ) {
				$googlemapkey = $this->dboptions['wdap_googleapikey'];
				if ( $googlemapkey ) {

					$language = isset($this->dboptions['wpdap_language']) ? $this->dboptions[ 'wpdap_language' ] : 'en';
					$language = apply_filters('wdap_map_lang',$language );
					wp_enqueue_script( 'backend-gmaps', 'https://maps.googleapis.com/maps/api/js?key=' . stripslashes( wp_strip_all_tags( $googlemapkey ) ) . '&libraries=drawing,places,geometry&language='.$language, '', '', false );
				}
			}
		}

		function save_polygon_cordinates() {

			if ( ! empty( $_POST['store_address_json'] ) ) {
				$_POST['store_address_json'] = sanitize_text_field( $_POST['store_address_json'] );
				$_POST['store_address_json'] = str_replace( "'", '"', $_POST['store_address_json'] );
				$_POST['store_address_json'] = stripcslashes( $_POST['store_address_json'] );
			}
			$entityID = '';
			if ( isset( $_REQUEST['_wpnonce'] ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) );
			}

			// Nonce Verification
			if ( isset( $nonce ) && ! wp_verify_nonce( $nonce, 'wpgmp-nonce' ) ) {
				die( 'Please reload page and submit the form again...' );
			}

			// Permission Verification
			if ( ! current_user_can( 'administrator' ) ) {
				die( 'You are not allowed to save changes!' );
			}

			$form_errors = array();
			$mandatory_fields = array( 'wdap_collection_title' => 'Collection Title' );
			foreach ( $mandatory_fields as $key => $field ) {
				if ( empty( $_POST[ $key ] ) ) {
					$form_errors[] = sprintf( esc_html__( '%s is a required field', 'woo-delivery-area-pro' ), $field );
				}
			}
			if ( $_POST['wdap_applyonRadio'] == 'Selected Products' && empty( $_POST['wdap_select_product'] ) ) {
				$form_errors[] = esc_html__( 'Please Select at least one product', 'woo-delivery-area-pro' );
			}

			if ( $_POST['wdap_applyonRadio'] == 'all_products_excluding_some' && empty( $_POST['exclude_products'] ) ) {
				$form_errors[] = esc_html__( 'Please Select at least one product', 'woo-delivery-area-pro' );
			}

			if ( $_POST['wdap_applyonRadio'] == 'selected_categories' && empty( $_POST['selectedcategories'] ) ) {
				$form_errors[] = esc_html__( 'Please Select at least one Category.', 'woo-delivery-area-pro' );
			}
			$polygonlen = strlen( $_POST['polygons_json'] );

			if ( $_POST['wdap_map_region'] == 'zipcode' && ( $polygonlen == 2 || $polygonlen == 0 ) && empty( $_POST['wdap_zip_codearea'] ) ) {
				if ( empty( $_POST['hasGoogleAPI'] ) ) {
					$form_errors[] = esc_html__( 'Please enter comma seperated zipcodes in textarea.', 'woo-delivery-area-pro' );
				} else {
					$form_errors[] = esc_html__( 'Please enter either zipcodes or draw a polygon on google map that represents your delivery area.', 'woo-delivery-area-pro' );
				}
			}

			if ( $_POST['wdap_map_region'] == 'country' && ( $polygonlen == 2 || $polygonlen == 0 ) && empty( $_POST['wdap_map_region_setting'] ) ) {
				$form_errors[] = esc_html__( 'Please select any country or draw any polygon.', 'woo-delivery-area-pro' );
			}
			if ( $_POST['wdap_map_region'] == 'sub-continents' && ( $polygonlen == 2 || $polygonlen == 0 ) && empty( $_POST['wdap_map_region_setting']['sub_continent'] ) ) {
				$form_errors[] = esc_html__( 'Please select any sub-continent or draw any polygon.', 'woo-delivery-area-pro' );
			}
			if ( isset($_POST['wdap_map_region']) && ($_POST['wdap_map_region'] == 'continents') && ( $polygonlen == 2 || $polygonlen == 0 ) && empty( $_POST['wdap_map_region_setting']['continent'] ) ) {
				$form_errors[] = esc_html__( 'Please select any continent or draw any polygon.', 'woo-delivery-area-pro' );
			}
			if ( $_POST['wdap_map_region'] == 'by_distance' ) {

				if ( empty( $_POST['wdap_store_address'] ) || empty( $_POST['store_address_json'] ) ) {
					$form_errors[] = esc_html__( 'Please specify nearest location to your store.', 'woo-delivery-area-pro' );
				}
				if ( empty( $_POST['wdap_store_address_range'] ) ) {
					$form_errors[] = esc_html__( 'Please specify distance range in kilometers where you allow / do delivery for orders.', 'woo-delivery-area-pro' );
				}
			}

			if ( count( $form_errors ) == 0 ) {
				if ( isset( $_POST['entityID'] ) ) {
					$entityID = intval( wp_unslash( $_POST['entityID'] ) );
				}
				if ( $entityID > 0 ) {
					$where['id'] = $entityID;
				} else {
					$where = '';
				}
				$data = array();
				$data['title']   = sanitize_text_field( wp_unslash( $_POST['wdap_collection_title'] ) );
				$data['applyon'] = sanitize_text_field( wp_unslash( $_POST['wdap_applyonRadio'] ) );
				if ( $_POST['wdap_applyonRadio'] == 'Selected Products' ) {
					if ( $_POST['wdap_select_product'] ) {
						$data['chooseproducts'] = serialize( $_POST['wdap_select_product'] );
					}
				}
				if ( $_POST['wdap_map_region'] == 'zipcode' ) {
					$allzipcodes      = sanitize_text_field( wp_unslash( $_POST['wdap_zip_codearea'] ) );
					$allzipcodesarray = explode( ',', $allzipcodes );
					$filteredallzipcodes = array_filter( $allzipcodesarray );
					$data['wdap_map_region_value'] = serialize( $filteredallzipcodes );
				} else if ( $_POST['wdap_map_region'] == 'by_distance' ) {
					$address = serialize(
						array(
							'range' => sanitize_text_field( wp_unslash( $_POST['wdap_store_address_range'] ) ),
							'address' => sanitize_text_field( wp_unslash( $_POST['store_address_json'] ) ),
						)
					);
					$data['wdap_map_region_value'] = $address;
				} else {
					$data['wdap_map_region_value'] = isset($_POST['wdap_map_region_setting']) ? serialize( $_POST['wdap_map_region_setting'] ):'';
				}
				if ( ! empty( $_POST['selectedcategories'] ) ) {
					$data['selectedcategories'] = serialize( $_POST['selectedcategories'] );
				}
				if ( ! empty( $_POST['exclude_products'] ) ) {
					$data['exclude_products'] = serialize( $_POST['exclude_products'] );
				}
				$data['assignploygons']  = wp_unslash( $_POST['polygons_json'] );
				$data['wdap_map_region'] = sanitize_text_field( wp_unslash( $_POST['wdap_map_region'] ) );
				$result = FlipperCode_Database::insert_or_update( WDAP_TBL_FORM, $data, $where );
				if ( false === $result ) {
					 $response['error'] = esc_html__( 'Something went wrong. Please try again.', 'woo-delivery-area-pro' );
				} elseif ( $entityID > 0 ) {
					$response['success'] = esc_html__( 'Collection Updated successfully.', 'woo-delivery-area-pro' );
				} else {
					$response['success'] = esc_html__( 'Collection Saved successfully.', 'woo-delivery-area-pro' );
				}
				unset( $_POST );
				if ( $entityID > 0 ) {
					$_POST['entityID'] = $entityID;
				}
				$_POST['operation'] = 'save';
				return $response;
			} else {
				$_POST['polygon_submission_error'] = $form_errors;
			}
		}

		/**
		 * Provide Saved Drawing Coordinates To JS.
		 */
		function wpdap_coordinate_forward_in_js() {

			if ( isset( $_GET['page'] ) && ( sanitize_text_field( $_GET['page'] ) == 'wdap_add_collection' ) ) {

				if ( isset( $_POST['deliverypro_submission'] ) && ! empty( $_POST['deliverypro_submission'] ) ) {
					$this->save_polygon_cordinates();
				}
					$modelFactory = new WDAP_Model();
					$ques_obj = $modelFactory->create_object( 'collection' );
					$wdap_js_lang['ajax_url'] = admin_url( 'admin-ajax.php' );
					$wdap_js_lang['nonce'] = wp_create_nonce( 'wdap-call-nonce' );
				if ( isset( $_GET['doaction'] ) && 'edit' == sanitize_text_field( $_GET['doaction'] ) && isset( $_GET['id'] ) ) {
					$ques_obj = $ques_obj->fetch( array( array( 'id', '=', intval( wp_unslash( sanitize_text_field( $_GET['id'] ) ) ) ) ) );
					$data = isset($ques_obj[0]) ? (array) $ques_obj[0] : array();
					$save_polygons = isset($data['assignploygons']) ? $data['assignploygons'] : array();
					$final_all_poligons = $this->wdap_change_poly_coordinates_in_Js_Object($save_polygons);
					$wdap_js_lang['polygons'] = $final_all_poligons;

					if(!empty($data['wdap_map_region']) && ($data['wdap_map_region']=='by_distance')){

						$zipcode_of_store_address = maybe_unserialize( $data['wdap_map_region_value'] );
						$zipaddress = json_decode( $zipcode_of_store_address['address'] );

						$range_circl_ui = array(
							'strokeColor'=>'#FF0000',
							'strokeOpacity'=>1,
							'strokeWeight'=>1,
							'fillColor'=>'#FF0000',
							'fillOpacity'=>0.5,
						);

						$range_circl_ui = apply_filters('range_circl_ui',$range_circl_ui);

						$location = array(
							'lat' => isset( $zipaddress->lat ) ? $zipaddress->lat : '',
							'lng' => isset( $zipaddress->lng ) ? $zipaddress->lng : '',
							'placezipcode' => isset( $zipaddress->placezipcode ) ? $zipaddress->placezipcode : '',
							'place_country_name' => isset( $zipaddress->place_country_name ) ? $zipaddress->place_country_name : '',
							'range' => isset( $zipcode_of_store_address['range'] ) ? $zipcode_of_store_address['range'] : '',
							'format'=>$range_circl_ui 

						);
						$wdap_js_lang['store_information'] = $location;
					}

				}
				if ( $this->dboptions ) {
					$wdap_js_lang['mapsettings']['zoom']      = ! empty( $this->dboptions['wdap_map_zoom_level'] ) ? $this->dboptions['wdap_map_zoom_level'] : '';
					$wdap_js_lang['mapsettings']['centerlat'] = ! empty( $this->dboptions['wdap_map_center_lat'] ) ? $this->dboptions['wdap_map_center_lat'] : '';
					$wdap_js_lang['mapsettings']['centerlng'] = ! empty( $this->dboptions['wdap_map_center_lng'] ) ? $this->dboptions['wdap_map_center_lng'] : '';
					$wdap_js_lang['mapsettings']['style']     = ! empty( $this->dboptions['wdap_map_style'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_map_style'] ) ) : '';
				}
					$icon_url = WDAP_IMAGES . '/pin_blue.png';
					$icon_url = apply_filters( 'wdap_map_icon', $icon_url );
					$wdap_js_lang['icon_url'] = esc_url( $icon_url );
					wp_enqueue_script( 'jquery' );
					wp_enqueue_script( 'polygonsdraw', esc_url( WDAP_JS . 'polygonsdraw.js' ), array( 'jquery' ) );
					wp_localize_script( 'polygonsdraw', 'wdap_backend_obj', $wdap_js_lang );
			}
		}

		/**
		 * Validate custom field on checkout page.
		 */
		function wdap_custom_checkout_field_process() {

			global $woocommerce;
			$order_restriction_error = isset( $this->dboptions['wdap_order_restrict_error'] ) ? $this->dboptions['wdap_order_restrict_error'] : esc_html__( 'We could not complete your order due to Zip Code Unavailability.', 'woo-delivery-area-pro' );
			$result = isset( $_POST['Chkziptestresult'] ) ? stripslashes( $_POST['Chkziptestresult'] ) : '';
			$getallvalues = json_decode( $result );
			$nofound = false;
			if ( is_array( $getallvalues ) && count( $getallvalues ) > 0 ) {
				foreach ( $getallvalues as $key => $value ) {
					if ( $value->value == 'NO' ) {
						$nofound = true;
					}
				}
			}
			if ( $nofound ) {
				wc_add_notice( sprintf( esc_html__( '%s', 'woo-delivery-area-pro' ), $order_restriction_error ), 'error' );
			}

		}

		function wdap_zip_search_markup( $id, $zipcode ) {

			global $post;
			if ( is_array( $id ) ) {
				$id = implode( ',', $id );
			}

			$pagtype = '';
			if ( is_cart() ) {
				$pagtype = 'cart';
			}

			if ( is_checkout() ) {
				$pagtype = 'checkout';
			}

			if ( is_shop() ) {
				$pagtype = 'shop';
			}

			if ( is_product_category() ) {
				$pagtype = 'category';
			}

			if ( is_single() && $post->post_type == 'product' ) {
				$pagtype = 'single';
			}

			$dynamic_zipcode_placeholder = !empty($this->dboptions['search_box_placeholder']) ? $this->dboptions['search_box_placeholder'] : esc_html__( 'Enter Zipcode', 'woo-delivery-area-pro' );


			$zipcode_placeholder = apply_filters( 'wdap_provide_zipcode_placeholder', $dynamic_zipcode_placeholder);
			$empty_zip_error = $this->dboptions['wdap_empty_zip_code'];

			$empty_zip_error = ! empty( $this->dboptions['wdap_empty_zip_code'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_empty_zip_code'] ) ) : esc_html__( 'Please enter zip code.', 'woo-delivery-area-pro' );

			$class = ! empty( $this->dboptions['default_templates']['zipcode'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['default_templates']['zipcode'] ) ) : 'default';
			?>
			<div id="wdap_zip_check" class="wdap_zip_form_container <?php echo esc_attr( $class ); ?>">
				<div class="wdap_notification_message" style="display: none;">
					<?php echo sprintf( esc_html__( '%s', 'woo-delivery-area-pro' ), $empty_zip_error ); ?>
				</div>
				<?php
				if ( ! is_checkout() ) {

					?>
				 <input type="text" value="<?php echo esc_attr( $zipcode ); ?>" name="zipcode_check" id="wdapziptextbox" class="wdapziptextbox"  placeholder="<?php echo esc_attr( $zipcode_placeholder ); ?>">
				<?php } ?>
				<input type="hidden" data-pagetype="<?php echo esc_attr( $pagtype ); ?>" value="<?php echo esc_attr( $id ); ?>" id="checkproductid" class="checkproductid" name="wdapcheckproductid" />
				<input type="hidden"  value="yes" id="wdap_start" class="wdap_start" name="wdap_start"/>
				<input type="hidden" value="" class="Chkziptestresult" id="Chkziptestresult" name="Chkziptestresult" />
				<input type="hidden" value="" class="zipcode_check_params"  name="zipcode_check_params" />
				<?php
				$arrow = '';
				if ( $class == 'standard' ) {
					$arrow = 'wdap_arrow';
				}
					$style = '';
				if ( is_checkout() ) {
					if ( ! empty( $this->dboptions['enable_order_restriction'] ) ) {
						$style .= 'display:none;';
					}
				}

				 $submit_button_label = ! empty( $this->dboptions['wdap_check_buttonlbl'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_check_buttonlbl'] ) ) : apply_filters( 'wdap_submit_btn_lbl', esc_html__( 'Check Avalibility', 'woo-delivery-area-pro' ) );
				?>
				  
				<input type="button" style="<?php echo esc_attr( $style ); ?>" value ="<?php echo esc_attr( $submit_button_label ); ?>" id="wdapzipsumit" class="wdapzipsumit single_add_to_cart_button button alt <?php echo esc_attr( $arrow ); ?>"  />
				
				<?php
				if ( ( $pagtype == 'single' ) && ! empty( $this->dboptions['wdap_frontend_desc'] ) ) {
					?>
				 <p class="zipcode_test_desc"><?php echo stripslashes( wp_strip_all_tags( $this->dboptions['wdap_frontend_desc'] ) ); ?></p><?php } ?>
		 </div>
			<?php
		}
		/**
		 * Create Custom field on checkout page.
		 */
		function wdap_custom_checkout_field( $checkout ) {

			global $woocommerce;
			$cartdata = WC()->cart->get_cart();
			$ids = array();
			foreach ( $cartdata as $key => $item ) {
				
				if($item['data']->is_virtual() ) { continue; }


				if(!empty($item['variation_id'])){
					$variation_id = $item['variation_id'];
					$v = new WC_Product_Variation($variation_id);

					if(!empty($v->get_parent_id())){

						$ids[] = $v->get_parent_id();

					}else{

					$ids[] = $item['product_id'];

					}
				}else{
					$ids[] = $item['product_id'];
				}

			}
			 echo '<div id="wdap_custom_checkout_field">';
				   $this->wdap_zip_search_markup( $ids, '' );
			 echo '</div>';
		}
		/**
		 * Create  Product avalibility tab
		 *
		 * @param  $tabs
		 * @return $tabs
		 */
		function wdap_woo_extra_tabs( $tabs ) {

			global $post,$post_type;
			$enable_tab = isset( $this->dboptions['disable_availability_tab'] ) ? $this->dboptions['disable_availability_tab'] : '';

			if ( is_single() && $post_type == 'product' && ! ( $enable_tab ) ) {
				$id = $post->ID;
				$wdap_current_post_setup = get_post_meta( $id, 'wdap_current_post_setup' );
				if ( ! empty( $wdap_current_post_setup[0] ) ) {
					$wdap_current_post_setup = maybe_unserialize( $wdap_current_post_setup[0] );
					if ( ! ( ! empty( $wdap_current_post_setup['avl_tab'] ) && $wdap_current_post_setup['avl_tab'] == 'on' ) ) {
						$tabs = $this->wdap_woo_extra_tabs_handlers( $id, $tabs );
					}
				} else {
					$tabs = $this->wdap_woo_extra_tabs_handlers( $id, $tabs );
				}
			}
			return $tabs;
		}

		function wdap_woo_extra_tabs_handlers( $postid, $tabs ) {

			$get_all_zipcodes = $this->get_all_zipcodes( $postid );
			if ( count( $get_all_zipcodes['allpolycoordinates'] ) == 0 && count( $get_all_zipcodes['allzipcodes'] ) == 0 ) {
				return $tabs;
			}
			$tabs['avalibility_map'] = array(
				'title'  => apply_filters( 'wdap_pa_tab_heading', esc_html__( 'Product Availability', 'woo-delivery-area-pro' ) ),
				'priority' => 50,
				'callback' => array( $this, 'wdap_woo_avalibility_map_content' ),
			);
			return $tabs;
		}

		/**
		 * [Zip and polygon on product avialibility tab]
		 *
		 * @return [type] [description]
		 */

		function wdap_woo_avalibility_map_content() {

			global $post;
			$allzipcode = array();
			$get_all_zipcodes = $this->get_all_zipcodes( $post->ID );
			$get_all_zipcodesarray = $get_all_zipcodes['allzipcodes'];
			foreach ( $get_all_zipcodesarray as $key => $collection ) {
				foreach ( $collection as $key => $zipcode ) {
					$allzipcode[] = $zipcode;
				}
			}
			$allzipcode    = array_unique( $allzipcode );
			$Newallzipcode = array_values( $allzipcode );
			asort( $Newallzipcode );

			if ( count($Newallzipcode)>0 && empty($this->dboptions['disable_zipcode_listing'])   ) {
				?>
			<div class="wdap_zip_table">
				<table>
					<?php
					$zipcode_listing_heading = apply_filters( 'wdap_zipcode_listing_heading', esc_html__( 'Product is avaialble in below zipcode areas.', 'woo-delivery-area-pro' ) );
					?>
					<thead><th align="center"><?php echo esc_html( $zipcode_listing_heading ); ?></th></thead>
					<tbody></tbody>
					<tr><td>
					<?php
					foreach ( $Newallzipcode as $key => $zipcode ) { ?>
						<span class="wdap_zip"><?php echo esc_html( $zipcode ); ?></span> 
						<?php
					}
					?>
					</td></tr>
				</table>
			</div>
				<?php
			}
			$googlemapkey = isset( $this->dboptions['wdap_googleapikey'] ) ? $this->dboptions['wdap_googleapikey'] : '';
			if ( $googlemapkey ) {
				echo do_shortcode( '[delivery_areas from_tab="yes" product_id="' . $post->ID . '"]' );
			}
		}

		public static function get_all_zipcodes( $productid, $exclude_collections = array() ) {

			global $wpdb;
			$collections = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'wdap_collection' );
			$collectionzipcodes = array();
			$collectioncoodinates = array();
			$storelocations = array();
			foreach ( $collections as $collection ) { // Loop for testing every collection

				$c_id = $collection->id;
				if ( ! empty( $exclude_collections ) && in_array( $c_id, $exclude_collections ) ) {
					continue;
				}
				if ( $collection->applyon == 'All Products' || empty( $productid ) ) {
					if ( $collection->wdap_map_region == 'zipcode' ) {
						$collectionzipcodes[] = unserialize( $collection->wdap_map_region_value );
					}

					if ( $collection->wdap_map_region == 'by_distance' ) {

						$zipcode_of_store_address = unserialize( $collection->wdap_map_region_value );
						$zipaddress = json_decode( $zipcode_of_store_address['address'] );
						$location = array(
							'lat' => isset( $zipaddress->lat ) ? $zipaddress->lat : '',
							'lng' => isset( $zipaddress->lng ) ? $zipaddress->lng : '',
							'placezipcode' => isset( $zipaddress->placezipcode ) ? $zipaddress->placezipcode : '',
							'place_country_name' => isset( $zipaddress->place_country_name ) ? $zipaddress->place_country_name : '',
							'range' => isset( $zipcode_of_store_address['range'] ) ? $zipcode_of_store_address['range'] : '',

						);
						$storelocations[] = $location;
					}
					if ( strlen( $collection->assignploygons ) > 2 ) {
						$collectioncoodinates[] = $collection->assignploygons;
					}
				} else if ( $collection->applyon == 'Selected Products' ) {
					if ( in_array( $productid, (array) unserialize( $collection->chooseproducts ) ) ) {
						if ( $collection->wdap_map_region == 'zipcode' ) {
							$collectionzipcodes[] = unserialize( $collection->wdap_map_region_value );
						}
						if ( strlen( $collection->assignploygons ) > 2 ) {
							$collectioncoodinates[] = $collection->assignploygons;
						}
					}
				} else if ( $collection->applyon == 'all_products_excluding_some' ) {

					if ( ! ( in_array( $productid, (array) unserialize( $collection->exclude_products ) ) ) ) {

						if ( $collection->wdap_map_region == 'zipcode' ) {
							$collectionzipcodes[] = unserialize( $collection->wdap_map_region_value );
						}
						if ( strlen( $collection->assignploygons ) > 2 ) {
							$collectioncoodinates[] = $collection->assignploygons;
						}
					}
				} else {
					if ( is_array( $productid ) ) {
						$productid = $productid['0'];
					}
					$terms = get_the_terms( $productid, 'product_cat' );
					$products_category = array();
					if ( ! empty( $terms ) ) {
						foreach ( $terms as $key => $term ) {
							$products_category[] = $term->term_id;
						}
					}
					$collection_category = unserialize( $collection->selectedcategories );
					$matched_category = array_intersect( $products_category, $collection_category );
					if ( ! empty( $matched_category ) ) {
						if ( strlen( $collection->assignploygons ) > 2 ) {
							$collectioncoodinates[] = $collection->assignploygons;
						}
						if ( $collection->wdap_map_region == 'zipcode' ) {
							$collectionzipcodes[] = unserialize( $collection->wdap_map_region_value );
						}
					}
				}
			}
			$allzipcoordinates = array();
			if ( is_array( $collectionzipcodes ) && count( $collectionzipcodes ) > 0 ) {
				foreach ( $collectionzipcodes as $key => $value ) {
					foreach ( $value as $key => $onezip ) {
						$allzipcoordinates[] = $onezip;
					}
				}
			}


			$data = array(
				'allpolycoordinates' => isset( $collectioncoodinates ) ? $collectioncoodinates : array(), // Polygon
				'allzipcodes' => isset( $collectionzipcodes ) ? $collectionzipcodes : array(),
				'allzipcoordinates' => isset( $allzipcoordinates ) ? $allzipcoordinates : array(), // Marker Lat Lng array
				'allstorelocations' => isset( $storelocations ) ? $storelocations : array(),
			);
			$wdap_js_lang['allzipcode'] = isset( $allzipcoordinates ) ? $allzipcoordinates : array();
			$wdap_js_lang['allstorelocations'] = isset( $storelocations ) ? $storelocations : array();
			$processdata = array();
			$instance = new WDAP_Delivery_Area();

			if ( is_array( $collectioncoodinates ) && count( $collectioncoodinates ) > 0 ) {
				foreach ( $collectioncoodinates as $key => $value ) {
					$processdata[] = $instance->wdap_change_poly_coordinates_in_Js_Object( $value );
				}
			}

			$wdap_js_lang['allpolycoordinates'] = isset( $processdata ) ? $processdata : array();
			$wdap_js_lang['allzipcodes'] = isset( $allzipcoordinates ) ? $allzipcoordinates : array();
			$icon_url = !empty($instance->dboptions['custom_marker_img']) ?$instance->dboptions['custom_marker_img'] :WDAP_IMAGES . '/pin_blue.png';
			$icon_url = apply_filters( 'wdap_map_icon', esc_url( $icon_url ) );
			$wdap_js_lang['icon_url'] = esc_url( $icon_url );
			$data['map_data'] = isset( $wdap_js_lang ) ? $wdap_js_lang : array();
			return $data;
		}

		function wdap_ajax_call() {

			// Nonce Verificatoin
			if ( isset( $_POST['noncevalue'] ) && ! wp_verify_nonce( $_POST['noncevalue'], 'wdap-call-nonce' ) ) {
				return;
			}

			$operation = isset( $_POST['operation'] ) ? sanitize_text_field( $_POST['operation'] ) : '';
			if ( empty( $operation ) ) {
				return;
			}

			$response = $this->$operation( $_POST );
			echo json_encode( $response );
			exit;
		}

		function wdap_search_in_coordinate( $data ) {

			$allcoodinates = $data;
			$final_all_poligons = array();
			foreach ( $data as $key => $value ) {
				$final_all_poligons[] = json_decode( $value );
			}
			$final_all_poligons = (array) $final_all_poligons;
			$requirepolyset = array();
			if ( count( $final_all_poligons ) > 0 ) {

				foreach ( $final_all_poligons as $key => $onepolygonsettings ) {

					if ( is_array( $onepolygonsettings ) && count( $onepolygonsettings ) > 0 ) {
						foreach ( $onepolygonsettings as $key1 => $onepolygonvalues ) {
							$removequote = isset( $onepolygonvalues[0]->coordinate ) ? $onepolygonvalues[0]->coordinate : array();
							if ( is_array( $removequote ) && count( $removequote ) > 0 ) {
								foreach ( $removequote as $key2 => $obj ) {
									$temp_obj = array();
									$temp_obj['lat'] = isset( $obj->lat ) ? (double) $obj->lat : '';
									$temp_obj['lng'] = isset( $obj->lng ) ? (double) $obj->lng : '';
									$removequote[ $key2 ] = (object) $temp_obj;
								}
							}

							$requirepolyset[] = isset( $removequote ) ? $removequote : array();
						}
					}
				}
			}

			return $requirepolyset;
		}

		function Check_for_zipmatch( $data ) {

			$zip_response = isset( $data['zip_response'] ) ? $data['zip_response'] : '';
			$tempData = str_replace( '\\', '', $zip_response );
			$decoded = json_decode( $tempData );
			if ( ! empty( $decoded ) ) {
				$json  = json_encode( $decoded );
				$array = json_decode( $json, true );
				$this->current_request_response = $array;
			}			
			$this->ajax_params = $data;
			$shortcode = isset( $data['shortcode'] ) ? $data['shortcode'] : '';
			$pagetype = isset( $data['pagetype'] ) ? $data['pagetype'] : '';
			$response = array();
			$cartproductidcheck = array();
			if ( $pagetype == 'single' || $pagetype == 'shop' || $pagetype=='category' ) {
				$response = $this->wdap_get_zipcodematch( $data );
				if ( $response['status'] == 'found' ) {
					$response['pagetype'] = $pagetype;
					unset( $this->current_request_response );
					$this->ajax_params = array();
					return $response;
				}
			}
			if ( $pagetype == 'cart' || $pagetype == 'checkout' ) {
				$is_local_pickup_enable = false;
				$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
				if ( ! empty( $chosen_methods ) ) {
					preg_match( '/(local_pickup)/', $chosen_methods[0], $is_local_pickup, PREG_OFFSET_CAPTURE );
					if ( ! empty( $is_local_pickup ) ) {
						$is_local_pickup_enable = true;
					}
				}

				$productsid = isset( $data['productid'] ) ? $data['productid'] : array();
				foreach ( $productsid as  $productid ) {

					$data['productid'] = $productid;
					$dataToStore = array();
					
					$_product =  wc_get_product($productid);

					if ( $is_local_pickup_enable ||  ($_product->is_type( 'virtual' )) ) {
						$dataToStore['id'] = $productid;
						$dataToStore['status'] = 'found';
						$dataToStore['coordinatematch'] = array();
					} else {
						$responsecart = $this->wdap_get_zipcodematch( $data );
						$dataToStore['id'] = $productid;
						$dataToStore['status'] = $responsecart['status'];
						$dataToStore['coordinatematch'] = isset($responsecart['coordinatematch']) ? $responsecart['coordinatematch'] :array();
					}
					$cartproductidcheck[] = $dataToStore;
				}
				$response['cartdata'] = $cartproductidcheck;
			}
			if ( $shortcode ) {
				$this->is_via_shortcode = true;
				$response = $this->wdap_get_zipcodematch( $data );
				if ( $response['status'] == 'found' ) {
					return $response;
				} else {
					$zipcode1 = $this->get_zip_code_from_response();
					if ( ! empty( $data['zipcode'] ) && ! ( $data['zipcode'] == $zipcode1 ) && ! empty( $zipcode1 ) ) {
						$data['zipcode'] = $zipcode1;
						$response = $this->wdap_get_zipcodematch( $data );
					}
				}
			}
			$response['zipcodestring'] = $this->wpdap_get_lat_lng_without_restrict();
			$response['pagetype'] = $pagetype;
			unset( $this->current_request_response );
			$this->ajax_params = array();
			$this->is_via_shortcode = false;
			return $response;

		}

		function get_zip_code_from_response() {

			$result  = isset( $this->current_request_response ) ? $this->current_request_response : array();
			if ( count( $result ) > 0 ) {
				foreach ( $result as $key => $value ) {
					$lastkey = isset( $value['address_components'] ) ? count( $value['address_components'] ) - 1 : '';
					if ( is_array( $value['address_components'] ) && count( $value['address_components'] ) > 0 ) {
						foreach ( $value['address_components'] as $key => $countryname ) {
							$conditionFirst = ( is_array( $countryname['types'] ) && ( count( $countryname['types'] ) > 0 ) && in_array( 'postal_code', $countryname['types'] ) ) ? true : false;
							if ( $conditionFirst ) {
								return isset( $countryname['long_name'] ) ? $countryname['long_name'] : '';
							}
						}
					}
				}
			}

		}

		// get list of country from respone.
		function wpdap_get_country_list( $zip ) {

			$result  = isset( $this->current_request_response ) ? $this->current_request_response : array();
			$countrylist = array();
			if ( count( $result ) > 0 ) {
				foreach ( $result as $key => $value ) {
					$lastkey = isset( $value['address_components'] ) ? count( $value['address_components'] ) - 1 : '';
					if ( is_array( $value['address_components'] ) && count( $value['address_components'] ) > 0 ) {
						foreach ( $value['address_components'] as $key => $countryname ) {
							$conditionthree = ( is_array( $value['address_components'][ $key ]['types'] ) && count( $value['address_components'][ $key ]['types'] ) > 0 && in_array( 'country', $value['address_components'][ $key ]['types'] ) ) ? true : false;
							if ( $conditionthree ) {
								$countrylist[] = isset( $value['address_components'][ $key ]['short_name'] ) ? $value['address_components'][ $key ]['short_name'] : '';
							}
						}
					}
				}
			}

			$countrylist = ( count( $countrylist ) > 0 ) ? array_unique( $countrylist ) : $countrylist;
			return $countrylist;
		}

		function wpdap_match_in_zip_country( $data ) {

			$zip = isset( $data['zipcode'] ) ? $data['zipcode'] : '';

			if(!empty($zip)){
				$backup_zip = $zip;
				$cleaned_zip = preg_replace('/[^A-Za-z0-9 ]/', '', $backup_zip);
				$zip =$cleaned_zip;
			}


			$mapregion = isset( $data['mapregion'] ) ? $data['mapregion'] : '';
			$countrylistfromdb = isset( $data['mapregionvalue'] ) ? maybe_unserialize( $data['mapregionvalue'] ) : array();
			if ( $mapregion == 'country' ) {
				$countrylistfromzip = $this->wpdap_get_country_list( $zip );
				if ( isset( $countrylistfromzip ) && isset($countrylistfromdb['country']) ) {
					$result = array_intersect( $countrylistfromzip, $countrylistfromdb['country'] );
					if ( count( $result ) > 0 ) {
						return true;
					}
				}
			} else {
				$partial_code_array = array();
				if ( count( $countrylistfromdb ) > 0 ) {

					if ( in_array( $zip, $countrylistfromdb ) ) {
						return true;
					}
					foreach ( $countrylistfromdb as $key => $dbzip ) {
						if ( stristr( $dbzip, '*' ) ) {
							$dbzip = str_replace( '*', '', $dbzip );
							$partial_code_array[] = $dbzip;
						}
					}
				}

				if ( ! empty( $partial_code_array ) ) {
					$matches = $this->partial_zip_find( $zip, $partial_code_array );
					return $matches;
				}
			}
				return false;
		}

		function partial_zip_find( $needle, $haystack ) {

			if ( count( $haystack ) > 0 ) {
				foreach ( $haystack as $key => $value ) {
					if ( 0 === stripos( $needle, ($value) ) ) {
						return true;
					}
				}
			}
			return false;
		}

		function wpdap_check_in_continent_and_sub( $data ) {

			$mapregion = isset( $data['mapregion'] ) ? $data['mapregion'] : '';
			$zip = isset( $data['zipcode'] ) ? $data['zipcode'] : '';
			$mapregionvalue = isset( $data['mapregionvalue'] ) ? maybe_unserialize( $data['mapregionvalue'] ) : array();
			$countrylistfromzip = $this->wpdap_get_country_list( $zip );
			$contrelate = (array) json_decode( self::$ctrycodewithcont );
			if ( is_array( $countrylistfromzip ) && count( $countrylistfromzip ) > 0 ) {
				foreach ( $countrylistfromzip as $onezip ) {
					if ( array_key_exists( $onezip, $contrelate ) ) {
						$actualcontient    = isset( $contrelate[ $onezip ]->continent ) ? $contrelate[ $onezip ]->continent : '';
						$actualsubcontient = isset( $contrelate[ $onezip ]->sub_continent ) ? $contrelate[ $onezip ]->sub_continent : '';
						if ( $mapregion == 'continents' ) {
							if ( is_array( $mapregionvalue['continent'] ) && count( $mapregionvalue['continent'] ) > 0 && in_array( $actualcontient, $mapregionvalue['continent'] ) ) {
								return true;
							}
						} else {
							if ( is_array( $mapregionvalue['sub_continent'] ) && count( $mapregionvalue['sub_continent'] ) > 0 && in_array( $actualsubcontient, $mapregionvalue['sub_continent'] ) ) {
								return true;
							}
						}
					}
				}
			}
			return false;

		}

		function wpdap_get_lat_lng_without_restrict() {

			$result  = isset( $this->current_request_response ) ? $this->current_request_response : array();
			$latlngcollection = array();
			if ( count( $result ) > 0 ) {

				if ( ! empty( $result['geometry']['location'] ) ) {
					$latlngcollection[] = $result['geometry']['location'];
				} else {
					foreach ( $result as $key => $value ) {
						if ( ! empty( $value['geometry']['location'] ) ) {
							$latlngcollection[] = $value['geometry']['location'];
						}
					}
				}
			}
			return $latlngcollection;

		}

		// Search function for zip code match in all collections
		function wdap_get_zipcodematch( $data ) {

			global $wpdb;
			$retrictcountrydata = array();
			$retrictziplatlng   = array();
			$zipcode = isset( $data['zipcode'] ) ? $data['zipcode'] : '';
			if ( isset( $zipcode ) ) {
				$collections  = $this->collections;
				$startsearch  = false;
				$match        = false;
				$collectionid = array();
				$productmatch = false;
				$collectioncoodinates = array();

				if ( is_array( $collections ) && count( $collections ) > 0 ) {

					foreach ( $collections as $collection ) {
						// Loop for testing every collection
						$map_region = isset( $collection->wdap_map_region ) ? $collection->wdap_map_region : '';
						$map_region_value = isset( $collection->wdap_map_region_value ) ? $collection->wdap_map_region_value : '';
						$c_id = isset( $collection->id ) ? $collection->id : '';
						$applyon = isset( $collection->applyon ) ? $collection->applyon : '';

						$assignploygons = isset( $collection->assignploygons ) ? $collection->assignploygons : '';

						$checkinmapregiondata = array(
							'mapregion' => $map_region,
							'mapregionvalue' => $map_region_value,
							'zipcode' => $zipcode,
							'id' => $c_id,
						);
						$getmatch = false;
						if ( $applyon == 'All Products' ) {
							if ( $map_region == 'country' || $map_region == 'zipcode' ) {
								$getmatch = $this->wpdap_match_in_zip_country( $checkinmapregiondata );
							}
							if ( ( $map_region == 'continents' || $map_region == 'sub-continents' ) && ! $getmatch ) {
								$getmatch = $this->wpdap_check_in_continent_and_sub( $checkinmapregiondata );
							}
							if ( $getmatch ) {
								$startsearch = true;
								$collectionid[] = $c_id;
								 break;
							} else {
								 $collectioncoodinates[] = $assignploygons;
							}
						} else if ( $applyon == 'Selected Products' ) {

							$productid = isset( $data['productid'] ) ? $data['productid'] : '';
							if ( is_array( $productid ) && count( $productid ) > 0 ) {
								$productid = $productid['0'];
							}
							$product_match = true;

							$saved_products = isset( $collection->chooseproducts ) ? maybe_unserialize( $collection->chooseproducts ) : array();

							if ( ! empty( $data['shortcode'] ) ) {
								if ( ! empty( $productid ) && is_array( $saved_products ) && count( $saved_products ) > 0 ) {
									$product_match  = ( in_array( $productid, $saved_products ) ) ? true : false;
								} else {
									$product_match = true;
								}
							} else {
								if ( is_array( $saved_products ) && count( $saved_products ) > 0 ) {
									$product_match = in_array( $productid, $saved_products );
								}
							}
							if ( $product_match ) {
								if ( $map_region == 'country' || $map_region == 'zipcode' ) {
									$getmatch = $this->wpdap_match_in_zip_country( $checkinmapregiondata );
								}
								if ( ( $map_region == 'continents' || $map_region == 'sub-continents' ) && ! $getmatch ) {
									$getmatch = $this->wpdap_check_in_continent_and_sub( $checkinmapregiondata );
								}
								if ( $getmatch ) {
									$startsearch = true;
									$collectionid[] = $c_id;
									break;
								} else {
									$collectioncoodinates[] = $assignploygons;
								}
							}
						} else if ( $applyon == 'all_products_excluding_some' ) {
							$productid = isset( $data['productid'] ) ? $data['productid'] : '';
							if ( is_array( $productid ) && count( $productid ) > 0 ) {
								$productid = $productid['0'];
							}
							$product_match;
							$exclude_products = isset( $collection->exclude_products ) ? maybe_unserialize( $collection->exclude_products ) : array();
							if ( ! empty( $data['shortcode'] ) ) {

								if ( ! empty( $productid ) && is_array( $exclude_products ) && count( $exclude_products ) > 0 ) {
									$product_match  = ( in_array( $productid, $exclude_products ) ) ? true : false;
								} else {
									$product_match = true;
								}
							} else {
								if ( is_array( $exclude_products ) && count( $exclude_products ) > 0 ) {
									$product_match = in_array( $productid, $exclude_products );
								}
							}
							if ( ! ( $product_match ) ) {
								if ( $map_region == 'country' || $map_region == 'zipcode' ) {
									$getmatch = $this->wpdap_match_in_zip_country( $checkinmapregiondata );
								}
								if ( ( $map_region == 'continents' || $map_region == 'sub-continents' ) && ! $getmatch ) {
									$getmatch = $this->wpdap_check_in_continent_and_sub( $checkinmapregiondata );
								}
								if ( $getmatch ) {
									$startsearch = true;
									$collectionid[] = $c_id;
								} else {
									$collectioncoodinates[] = $assignploygons;
								}
							}
						} else {
								$matched_category = array();
								$productid = isset( $data['productid'] ) ? $data['productid'] : '';
								if ( is_array( $productid ) && count( $productid ) > 0 ) {
									$productid = $productid['0'];
								}

								$terms = get_the_terms($productid, 'product_cat' );


								$products_category = array();
								if ( ! empty( $terms ) && is_array( $terms ) ) {
									foreach ( $terms as $key => $term ) {
										$products_category[] = isset( $term->term_id ) ? $term->term_id : '';
									}
								}
								$collection_category = isset( $collection->selectedcategories ) ? unserialize( $collection->selectedcategories ) : '';
								$matched_category = array_intersect( $products_category, $collection_category );
								$product_match = true;

								if ( ! empty( $data['shortcode'] ) ) {

									if(!empty($productid)){

										if(count($matched_category)>0){
											$product_match = true;
										}else{
											$product_match = false;
										}

									}else{
										$product_match = true;
									}

								} else {

									if(count( $matched_category )>0) {
										$product_match = true;
									}else{
										$product_match = false;
									}
								}

							if($product_match){

								if ( $map_region == 'country' || $map_region == 'zipcode' ) {
									$getmatch = $this->wpdap_match_in_zip_country( $checkinmapregiondata );
								}
								if ( ( $map_region == 'continents' || $map_region == 'sub-continents' ) && ! $getmatch ) {
									$getmatch = $this->wpdap_check_in_continent_and_sub( $checkinmapregiondata );
								}
								if($getmatch){

									if(count($matched_category)>0){
										$matched_category = array_values( $matched_category );
										$matched_id = $matched_category[0];
										$matched_category_obj = get_term_by( 'id', $matched_id, 'product_cat' );
										$matched_category_name = $matched_category_obj->name;
									}

									$startsearch = true;
									$collectionid[] = $c_id;
									break;
								} else {
									$collectioncoodinates[] = $assignploygons;
								}
							}
						}
					}
				}
			}

			if ( $startsearch ) {
					$response = array(
						'status' => 'found',
						'collectionid' => $collectionid,
					);
			} else {
				if ( ! empty( $collectioncoodinates ) ) {
					$allcoordinates = $this->wdap_search_in_coordinate( $collectioncoodinates );
					$response = array(
						'status' => 'notfound',
						'coordinatematch' => $allcoordinates,
					);
				} else {
					$response = array(
						'status' => 'notfound',
						'coordinatematch' => array(),
					);
				}
			}
			return $response;
		}

		function wdap_zipcode_field() {

			 global $product,$post_type;
			 $zipcode = isset( $_POST['wdapziptextbox'] ) ? $_POST['wdapziptextbox'] : '';

			 $id = $product->get_id();

			if ( is_single() && $post_type == 'product' ) {

				$wdap_current_post_setup = get_post_meta( $id, 'wdap_current_post_setup' );

				if ( ! empty( $wdap_current_post_setup[0] ) ) {

					$wdap_current_post_setup = maybe_unserialize( $wdap_current_post_setup[0] );

					if ( ! ( ! empty( $wdap_current_post_setup['zip_form'] ) && $wdap_current_post_setup['zip_form'] == 'on' ) ) {

						$this->wdap_zip_search_markup( $id, $zipcode );

					}
				} else {
					$this->wdap_zip_search_markup( $id, $zipcode );
				}
			}
		}

		function wdap_setup_class_vars() {
			$this->is_country_restrict = isset( $this->dboptions['enable_retrict_country'] ) ? true : false;
			include( WDAP_INC_DIR . 'ctrycodewithcont.php' );
		}

		function define_admin_menu() {

			$pagehook = add_menu_page(
				esc_html__( 'Woocommerce Delivery Area Pro', 'woo-delivery-area-pro' ),
				esc_html__( 'Woocommerce Delivery Area Pro', 'woo-delivery-area-pro' ),
				'wdap_admin_overview',
				WDAP_SLUG,
				array( $this, 'processor' ),
				esc_url( WDAP_IMAGES . 'fc-small-logo.png' )
			);
			return $pagehook;
		}

		function plugin_activation_work() {

			$showzipcodesearch = WDAP_Fresh_Settings::get_fresh_settings();
			$drs = maybe_unserialize( get_option( 'wp-delivery-area-pro' ) );
			if ( ! empty( $drs ) ) {
				foreach ( $drs as $key => $settings ) {
					if ( ! empty( $drs[ $key ] ) ) {
						$showzipcodesearch[ $key ] = $drs[ $key ];
					}
				}
			}
			if ( ( ! $drs ) || array_key_exists( 'version', $drs ) ) {
				update_option( 'wp-delivery-area-pro', wp_unslash( $showzipcodesearch ) );
			}
		}

		function wdap_polygon_markup( $atts ) {

			$factoryObject = new WDAP_Controller();
			$viewObject = $factoryObject->create_object( 'shortcode' );
			$output = $viewObject->display( 'delivery_area', $atts );
			return $output;
		}

		function wdap_localisation_parameter() {

			global $post;
			$wdap_js_lang = array();
			$wdap_js_lang['ajax_url'] = admin_url( 'admin-ajax.php' );
			$wdap_js_lang['nonce'] = wp_create_nonce( 'wdap-call-nonce' );
			$wdap_js_lang['exclude_countries'] = apply_filters( 'wdap_exclude_countries', array() );
			$wdap_js_lang['marker_country_restrict'] = apply_filters( 'wdap_enable_marker_country_restrict', true );
			$wdap_js_lang['is_api_key'] = ! empty( $this->dboptions['wdap_googleapikey'] ) ? 'yes' : '';
			$range_circle_ui = array(
							'strokeColor'=>'#FF0000',
							'strokeOpacity'=>1,
							'strokeWeight'=>1,
							'fillColor'=>'#FF0000',
							'fillOpacity'=>0.5,
						);

			$wdap_js_lang['range_circle_ui'] = apply_filters('range_circle_ui',$range_circle_ui);
			if ( ! empty( $this->dboptions['wdap_country_restriction_listing'] ) && isset( $this->dboptions['enable_places_to_retrict_country_only'] ) && ( $this->dboptions['enable_places_to_retrict_country_only'] == 'true' ) ) {
				$wdap_js_lang['autosuggest_country_restrict'] = $this->dboptions['wdap_country_restriction_listing'][0];
			}

			if ( ! empty( $this->dboptions['wdap_country_restriction_listing'] ) && isset( $this->dboptions['restrict_places_of_country_checkout'] ) && ( $this->dboptions['restrict_places_of_country_checkout'] == 'true' ) ) {
				$wdap_js_lang['autosuggest_country_restrict_checkout'] = $this->dboptions['wdap_country_restriction_listing'][0];
			}

			if ( is_checkout() ) {

				$wdap_js_lang['wdap_checkout_avality_method'] = ! empty( $this->dboptions['wdap_checkout_avality_method'] ) ? $this->dboptions['wdap_checkout_avality_method'] : '';

				$is_shipping = ( ! empty( $this->dboptions['wdap_checkout_avality_method'] ) && $this->dboptions['wdap_checkout_avality_method'] == 'via_shipping' ) ? true : false;

				$is_billing = ( ! empty( $this->dboptions['wdap_checkout_avality_method'] ) && $this->dboptions['wdap_checkout_avality_method'] == 'via_billing' ) ? true : false;

				$is_shipping_address = ( $is_shipping && ( ( ! empty( $this->dboptions['wdap_checkout_avality_shipping'] ) ) && $this->dboptions['wdap_checkout_avality_shipping'] == 'via_address' ) ) ? true : false;

				$is_billing_address = ( $is_billing && ( ( ! empty( $this->dboptions['wdap_checkout_avality_billing'] ) ) && $this->dboptions['wdap_checkout_avality_billing'] == 'via_address' ) ) ? true : false;

				$is_shipping_zipcode = ( $is_shipping && ( ( ! empty( $this->dboptions['wdap_checkout_avality_shipping'] ) ) && $this->dboptions['wdap_checkout_avality_shipping'] == 'via_zipcode' ) ) ? true : false;

				$is_billing_zipcode = ( $is_billing && ( ( ! empty( $this->dboptions['wdap_checkout_avality_billing'] ) ) && $this->dboptions['wdap_checkout_avality_billing'] == 'via_zipcode' ) ) ? true : false;

				if ( $is_shipping_address || $is_billing_address ) {
					$wdap_js_lang['wdap_checkout_avality_method'] = 'via_address';
				}
				if ( $is_shipping_zipcode || $is_billing_zipcode ) {
					$wdap_js_lang['wdap_checkout_avality_method'] = 'via_zipcode';
				}
			}

			if ( $this->dboptions ) {

				$wdap_js_lang['mapsettings']['zoom']      = ! empty( $this->dboptions['wdap_map_zoom_level'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_map_zoom_level'] ) ) : '';
				
				$wdap_js_lang['mapsettings']['centerlat'] = ! empty( $this->dboptions['wdap_map_center_lat'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_map_center_lat'] ) ) : '';
				
				$wdap_js_lang['mapsettings']['sicon_url'] =  !empty($this->dboptions['marker_img']) ?$this->dboptions['marker_img'] : WDAP_IMAGES . '/pin_blue.png';

				$wdap_js_lang['mapsettings']['centerlng'] = ! empty( $this->dboptions['wdap_map_center_lng'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_map_center_lng'] ) ) : '';
				$wdap_js_lang['mapsettings']['style']     = ! empty( $this->dboptions['wdap_map_style'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_map_style'] ) ) : '';
				$wdap_js_lang['mapsettings']['enable_restrict']     = ! empty( $this->dboptions['enable_retrict_country'] ) ? true : '';
				if ( ! empty( $this->dboptions['enable_markers_on_map'] ) ) {
					$wdap_js_lang['mapsettings']['enable_markers_on_map'] = ! empty( $this->dboptions['enable_markers_on_map'] ) ? $this->dboptions['enable_markers_on_map'] : 'no';
				} elseif ( WDAP_VERSION == '1.0.3' ) {
					$wdap_js_lang['mapsettings']['enable_markers_on_map'] = true;
				}
				if ( ! empty( $this->dboptions['enable_map_bound'] ) ) {
					$wdap_js_lang['mapsettings']['enable_bound']     = ! empty( $this->dboptions['enable_map_bound'] ) ? $this->dboptions['enable_map_bound'] : 'no';
				} elseif ( WDAP_VERSION == '1.0.3' ) {
					$wdap_js_lang['mapsettings']['enable_map_bound'] = true;
				}
				if ( ! empty( $this->dboptions['enable_polygon_on_map'] ) ) {
					$wdap_js_lang['mapsettings']['enable_polygon_on_map']     = ! empty( $this->dboptions['enable_polygon_on_map'] ) ? $this->dboptions['enable_polygon_on_map'] : 'no';
				} elseif ( WDAP_VERSION == '1.0.3' ) {
					$wdap_js_lang['mapsettings']['enable_polygon_on_map'] = true;
				}
				$wdap_js_lang['mapsettings']['restrict_country']     = ! empty( $this->dboptions['wdap_country_restriction_listing'][0] ) ? $this->dboptions['wdap_country_restriction_listing'][0] : '';
			}
			$shop_error_message = array(
				'na'=> !empty( $this->dboptions['wdap_shop_error_notavailable'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_shop_error_notavailable'] ) ) : esc_html__( ' Product Not Available ', 'woo-delivery-area-pro' ),
				'a'=> !empty( $this->dboptions['wdap_shop_error_available'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_shop_error_available'] ) ) : esc_html__( ' Product Available ', 'woo-delivery-area-pro' ),
				'invld' => ! empty( $this->dboptions['wdap_shop_error_invalid'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_shop_error_invalid'] ) ) : esc_html__( 'Invalid Zipcode.', 'woo-delivery-area-pro' ),

			);

			$category_error_message = array(
				'na'=> !empty( $this->dboptions['wdap_category_error_notavailable'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_category_error_notavailable'] ) ) : esc_html__( ' Product Not Available ', 'woo-delivery-area-pro' ),
				'a'=> !empty( $this->dboptions['wdap_category_error_available'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_category_error_available'] ) ) : esc_html__( ' Product Available ', 'woo-delivery-area-pro' ),
				'invld' => ! empty( $this->dboptions['wdap_category_error_invalid'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_category_error_invalid'] ) ) : esc_html__( 'Invalid Zipcode.', 'woo-delivery-area-pro' ),

			);
			$product_error_message = array(
				'na'=> !empty( $this->dboptions['wdap_product_error_notavailable'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_product_error_notavailable'] ) ) : esc_html__( ' Product Not Available ', 'woo-delivery-area-pro' ),
				'a'=> !empty( $this->dboptions['wdap_product_error_available'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_product_error_available'] ) ) : esc_html__( ' Product Available ', 'woo-delivery-area-pro' ),
				'invld' => ! empty( $this->dboptions['wdap_product_error_invalid'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_product_error_invalid'] ) ) : esc_html__( 'Invalid Zipcode.', 'woo-delivery-area-pro' ),
			);

			$cart_error_message = array(
				'na'=> !empty( $this->dboptions['wdap_cart_error_notavailable'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_cart_error_notavailable'] ) ) : esc_html__( ' Product Not Available ', 'woo-delivery-area-pro' ),
				'a'=> !empty( $this->dboptions['wdap_cart_error_available'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_cart_error_available'] ) ) : esc_html__( ' Product Available ', 'woo-delivery-area-pro' ),
				'invld' => ! empty( $this->dboptions['wdap_cart_error_invalid'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_cart_error_invalid'] ) ) : esc_html__( 'Invalid Zipcode.', 'woo-delivery-area-pro' ),
				'th' => ! empty( $this->dboptions['wdap_cart_error_th'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_cart_error_th'] ) ) : esc_html__( ' Availability Status ', 'woo-delivery-area-pro' ),
				'summary' => ! empty( $this->dboptions['wdap_cart_error_summary'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_cart_error_summary'] ) ) : esc_html__( '{no_products_available} Available, {no_products_unavailable} Unavailable', 'woo-delivery-area-pro' ),

			);

			$checkout_error_message = array(
				'na'=> !empty( $this->dboptions['wdap_checkout_error_notavailable'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_cart_error_notavailable'] ) ) : esc_html__( ' Product Not Available ', 'woo-delivery-area-pro' ),
				'a'=> !empty( $this->dboptions['wdap_checkout_error_available'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_checkout_error_available'] ) ) : esc_html__( ' Product Available ', 'woo-delivery-area-pro' ),
				'invld' => ! empty( $this->dboptions['wdap_checkout_error_invalid'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_checkout_error_invalid'] ) ) : esc_html__( 'Invalid Zipcode.', 'woo-delivery-area-pro' ),
				'th' => ! empty( $this->dboptions['wdap_checkout_error_th'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_checkout_error_th'] ) ) : esc_html__( ' Availability Status ', 'woo-delivery-area-pro' ),
				'summary' => ! empty( $this->dboptions['wdap_checkout_error_summary'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_checkout_error_summary'] ) ) : esc_html__( '{no_products_available} Available, {no_products_unavailable} Unavailable', 'woo-delivery-area-pro' ),

			);

			$errormessage = array(
				'empty' => ! empty( $this->dboptions['wdap_empty_zip_code'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_empty_zip_code'] ) ) : esc_html__( ' Please enter zip code. ', 'woo-delivery-area-pro' ),
				'na' => esc_html__( ' Product Not Available ', 'woo-delivery-area-pro' ),
				'a' =>  esc_html__( ' Product Available', 'woo-delivery-area-pro' ),
				'invld' =>  esc_html__( 'Invalid Zipcode.', 'woo-delivery-area-pro' ),
				'p' => esc_html__( 'Products are ', 'woo-delivery-area-pro' ),
				'th' => esc_html__( 'Availability Status ', 'woo-delivery-area-pro' ),
				'pr' => esc_html__( 'Products ', 'woo-delivery-area-pro' ),
				'summary' => esc_html__( '{no_products_available} Available, {no_products_unavailable} Unavailable ', 'woo-delivery-area-pro' ),
				'error_msg_color' => ! empty( $this->dboptions['error_msg_color'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['error_msg_color'] ) ) : '#ff0000',

				'success_msg_color' => ! empty( $this->dboptions['success_msg_color'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['success_msg_color'] ) ) : '#77a464',
			);

			if(is_shop() ){
				$errormessage = array_merge($errormessage, $shop_error_message);
			}

			if(is_product_category()){
				$errormessage = array_merge($errormessage, $category_error_message);
			}

			if ( is_cart() ) {
				$errormessage = array_merge($errormessage, $cart_error_message);
			}

			if ( is_checkout() ) {
				$errormessage = array_merge($errormessage, $checkout_error_message);
			}
			if ( is_single() && $post->post_type == 'product' ) {
				$errormessage = array_merge($errormessage, $product_error_message);
			}

			$wdap_js_lang['errormessages'] = $errormessage;
			if ( ! empty( $this->dboptions['enable_order_restriction'] ) && is_checkout() ) {
				$wdap_js_lang['order_restriction'] = ! empty( $this->dboptions['enable_order_restriction'] ) ? $this->dboptions['enable_order_restriction'] : '';
			}
			if ( ! empty( $this->dboptions['wdap_checkout_avality'] ) && ( $this->dboptions['wdap_checkout_avality'] == 'via_address' ) ) {
				$wdap_js_lang['wdap_checkout_avality'] = $this->dboptions['wdap_checkout_avality'];
			}
			$shortcode_settings = array(
				'wdap_address_empty'     => ! empty( $this->dboptions['wdap_address_empty'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['wdap_address_empty'] ) ) : esc_html__( 'Please enter your address', 'woo-delivery-area-pro' ),

				'address_not_shipable'   => ! empty( $this->dboptions['address_not_shipable'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['address_not_shipable'] ) ) : esc_html__( 'Sorry, We do not provide shipping in this area.', 'woo-delivery-area-pro' ),

				'address_shipable'       => ! empty( $this->dboptions['address_shipable'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['address_shipable'] ) ) : esc_html__( 'Yes, We provide shipping in this area.', 'woo-delivery-area-pro' ),

				'prlist_error'       => ! empty( $this->dboptions['product_listing_error'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['product_listing_error'] ) ) : esc_html__( 'Please select at least one product.', 'woo-delivery-area-pro' ),

				'form_success_msg_color' => ! empty( $this->dboptions['form_success_msg_color'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['form_success_msg_color'] ) ) : '',

				'form_error_msg_color'   => ! empty( $this->dboptions['form_error_msg_color'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['form_error_msg_color'] ) ) : '',
			);
			$wdap_js_lang['shortcode_settings'] = $shortcode_settings;
			$wdap_js_lang['shortcode_map']['enable'] = true;
			$wdap_js_lang['shortcode_map']['zoom'] = ! empty( $this->dboptions['shortcode_map_zoom_level'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['shortcode_map_zoom_level'] ) ) : '';
			$wdap_js_lang['shortcode_map']['centerlat'] = ! empty( $this->dboptions['shortcode_map_center_lat'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['shortcode_map_center_lat'] ) ) : '';
			$wdap_js_lang['shortcode_map']['centerlng'] = ! empty( $this->dboptions['shortcode_map_center_lng'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['shortcode_map_center_lng'] ) ) : '';
			$wdap_js_lang['shortcode_map']['style']  = ! empty( $this->dboptions['shortcode_map_style'] ) ? stripslashes( wp_strip_all_tags( $this->dboptions['shortcode_map_style'] ) ) : '';

			if ( isset( $this->dboptions['enable_product_listing'] ) ) {
				$wdap_js_lang['shortcode_settings']['check_product'] = isset( $this->dboptions['enable_product_listing'] ) ? $this->dboptions['enable_product_listing'] : '';
			}

			$wdap_js_lang['can_be_delivered_redirect_url'] = isset( $this->dboptions['can_be_delivered_redirect_url'] ) ? esc_url( $this->dboptions['can_be_delivered_redirect_url'] ) : '';
			$wdap_js_lang['cannot_be_delivered_redirect_url'] = isset( $this->dboptions['cannot_be_delivered_redirect_url'] ) ? esc_url( $this->dboptions['cannot_be_delivered_redirect_url'] ) : '';
			$wdap_js_lang['loader_image'] = esc_url( WDAP_IMAGES . 'loader.gif' );
			$wdap_js_lang['enable_autosuggest_checkout']     = ! empty( $this->dboptions['enable_auto_suggest_checkout'] ) ? $this->dboptions['enable_auto_suggest_checkout'] : false;

			return $wdap_js_lang;
		}

		function frontend_script_localisation() {

			global $post;
			$wdap_js_lang = $this->wdap_localisation_parameter();
			wp_localize_script( 'wdap-frontend.js', 'wdap_settings_obj', $wdap_js_lang );

		}

		function backend_script_localisation() {

			$deletemessage = array();
			$deletemessage['deleltemessage'] = esc_html__( 'Are you sure you want to delete this?', 'woo-delivery-area-pro' );
			$deletemessage['ajax_url'] = admin_url( 'admin-ajax.php' );
			$deletemessage['nonce'] = wp_create_nonce( 'wdap-call-nonce' );
			wp_localize_script( 'wdap-backend.js', 'errormessage', $deletemessage );

		}

		function wdap_update_notice( $data ) {

			// Nonce & Security Verification
			if ( ! wp_verify_nonce( $data['noncevalue'], 'wdap-call-nonce' ) || ! current_user_can( 'administrator' ) ) {
				return;
			}

			$userID = isset( $data['userID'] ) ? $data['userID'] : false;
			if ( $userID ) {
				$updated = update_user_meta( $userID, 'delivery_rating_disabled', 'yes' );
			}
			return $data;
		}

		function wdap_feedback_notice() {

			$screen = get_current_screen();
			if ( $screen->parent_base == 'wdap_view_overview' ) {
				$user_id = get_current_user_id();
				$is_already_disabled = get_user_meta( $user_id, 'delivery_rating_disabled' );
				if ( ! $is_already_disabled ) {
					?>
									
				<div class="notice notice-success is-dismissible fc-disable-rating-notice" data-userID='<?php echo esc_attr( $user_id ); ?>' style="margin-left: 0px;margin-top: 15px;">

						<p>
						<?php
						echo sprintf( esc_html__( 'If this plugin is useful for you, please provide us a %s. Also please provide us your valuable suggestions & feedbacks so that we can make this plugin even better for you.', 'woo-delivery-area-pro' ), '<a href="https://codecanyon.net/item/woo-delivery-area-pro/reviews/19476751" target="_blank">Star Rating & Review</a>' );
						?>
						</p>
				</div>
					<?php
				}
			}

		}

		function wdap_change_poly_coordinates_in_Js_Object( $data ) {

			$final_all_polygons = str_replace( '', '', $data );
			$final_all_polygons = json_decode( $final_all_polygons );
			$final_all_polygons = (array) $final_all_polygons;
			$onepolyset = array();
			$requirepolyset = array();
			if ( is_array( $final_all_polygons ) && count( $final_all_polygons ) > 0 ) {
				foreach ( $final_all_polygons as $key => $onepolygonsettings ) {

					if ( is_array( $onepolygonsettings ) && count( $onepolygonsettings ) > 0 ) {

						foreach ( $onepolygonsettings as $key1 => $onepolygonvalues ) {
							$onepolyset['id'] = isset( $onepolygonvalues->id ) ? $onepolygonvalues->id : '';
							$removequote = isset( $onepolygonvalues->coordinate ) ? $onepolygonvalues->coordinate : array();
							$onepolyset['coordinate'] = $removequote;

							if ( is_array( $removequote ) && count( $removequote ) > 0 ) {
								foreach ( $removequote as $key2 => $obj ) {
									$temp_obj = array();
									$temp_obj['lat'] = isset( $obj->lat ) ? (double) $obj->lat : '';
									$temp_obj['lng'] = isset( $obj->lng ) ? (double) $obj->lng : '';
									$onepolyset['coordinate'][ $key2 ] = (object) $temp_obj;
								}
							}
							$onepolyset['format'] = $onepolygonvalues->popygon_all_properties;
						}

					}

					if(!empty($onepolyset['coordinate'])){
						$requirepolyset[] = $onepolyset;
					}

				}
			}
			return $requirepolyset;
		}

		function wpdap_get_collections() {

			global $wpdb;
			$this->collections = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'wdap_collection' );

		}

		 /**
		  * Define all constants.
		  */
		function _define_constants() {

			global $wpdb;
			if ( ! defined( 'WDAP_SLUG' ) ) {
				define( 'WDAP_SLUG', 'wdap_view_overview' );
			}
			if ( ! defined( 'WDAP_PREMIUM' ) ) {
				define( 'WDAP_PREMIUM', 'true' );
			}
			if ( ! defined( 'WDAP_VERSION' ) ) {
				define( 'WDAP_VERSION', '2.0.6' );
			}

			if ( ! defined( 'WDAP_FOLDER' ) ) {
				define( 'WDAP_FOLDER', basename( dirname( __FILE__ ) ) );
			}
			if ( ! defined( 'WDAP_DIR' ) ) {
				define( 'WDAP_DIR', plugin_dir_path( __FILE__ ) );
			}
			if ( ! defined( 'WDAP_URL' ) ) {
				define( 'WDAP_URL', plugin_dir_url( WDAP_FOLDER ) . WDAP_FOLDER . '/' );
			}
			if ( ! defined( 'WDAP_CORE_URL' ) ) {
				define( 'WDAP_CORE_URL', WDAP_URL . 'core/' );
			}
			if ( ! defined( 'WDAP_PLUGIN_CLASSES' ) ) {
				define( 'WDAP_PLUGIN_CLASSES', WDAP_DIR . 'classes/' );
			}
			if ( ! defined( 'WDAP_CONTROLLER' ) ) {
				define( 'WDAP_CONTROLLER', WDAP_CORE_URL );
			}
			if ( ! defined( 'WDAP_CORE_CONTROLLER_CLASS' ) ) {
				define( 'WDAP_CORE_CONTROLLER_CLASS', WDAP_CORE_URL . 'class.controller.php' );
			}
			if ( ! defined( 'WDAP_MODEL' ) ) {
				define( 'WDAP_MODEL', WDAP_DIR . 'modules/' );
			}
			if ( ! defined( 'WDAP_TEMPLATES' ) ) {
				define( 'WDAP_TEMPLATES', WDAP_DIR . 'templates/' );
			}
			if ( ! defined( 'WDAP_TEMPLATES_URL' ) ) {
				define( 'WDAP_TEMPLATES_URL', WDAP_URL . 'templates/' );
			}
			if ( ! defined( 'WDAP_INC_DIR' ) ) {
				define( 'WDAP_INC_DIR', WDAP_DIR . 'includes/' );
			}
			if ( ! defined( 'WDAP_CSS' ) ) {
				define( 'WDAP_CSS', WDAP_URL . '/assets/css/' );
			}
			if ( ! defined( 'WDAP_JS' ) ) {
				define( 'WDAP_JS', WDAP_URL . 'assets/js/' );
			}
			if ( ! defined( 'WDAP_IMAGES' ) ) {
				define( 'WDAP_IMAGES', WDAP_URL . 'assets/images/' );
			}
			if ( ! defined( 'WDAP_FONTS' ) ) {
				define( 'WDAP_FONTS', WDAP_URL . 'fonts/' );
			}
			if ( ! defined( 'WDAP_TBL_FORM' ) ) {
				define( 'WDAP_TBL_FORM', $wpdb->prefix . 'wdap_collection' );
			}
			if ( ! defined( 'WDAP_TBL_BACKUP' ) ) {
				define( 'WDAP_TBL_BACKUP', $wpdb->prefix . 'wdap_backups' );
			}
			$upload_dir = wp_upload_dir();
			if ( ! defined( 'WDAP_BACKUP' ) ) {

				if ( ! empty( $upload_dir['basedir'] ) && ! is_dir( $upload_dir['basedir'] . '/collections-backup' ) ) {
					wp_mkdir_p( $upload_dir['basedir'] . '/collections-backup' );
				}
				define( 'WDAP_BACKUP', $upload_dir['basedir'] . '/collections-backup/' );
				define( 'WDAP_BACKUP_URL', $upload_dir['baseurl'] . '/collections-backup/' );

			}

		}
	}
	new WDAP_Delivery_Area();

}
