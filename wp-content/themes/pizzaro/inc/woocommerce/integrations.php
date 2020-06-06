<?php
/**
 * WooCommerce Extensions Integrations
 *
 * @package pizzaro
 */

if ( is_yith_wapo_activated() ) {

	if ( ! function_exists( 'pizzaro_wapo_check_required_addons' ) ) {
		/**
		 * pizzaro_wapo_check_required_addons function.
		 *
		 * @param mixed $product_id
		 * @return void
		 */
		function pizzaro_wapo_check_required_addons( $product_id ) {
			$types_list = YITH_WAPO_Type::getAllowedGroupTypes( $product_id );

			if ( ! empty( $types_list ) ) {
				return true;
			}

			return false;
		}
	}

	/**
	 * Adds extra post classes for products.
	 *
	 * @since 2.1.0
	 * @param array $classes
	 * @param string|array $class
	 * @param int $post_id
	 * @return array
	 */
	function pizzaro_wapo_woocommerce_post_class( $classes, $class = '', $post_id = '' ) {
		if ( ! $post_id || 'product' !== get_post_type( $post_id ) ) {
			return $classes;
		}

		$product = wc_get_product( $post_id );

		if ( $product && pizzaro_wapo_check_required_addons( $post_id ) ) {
			$classes[] = 'addon-product';
		}

		return $classes;
	}

	add_filter( 'post_class', 'pizzaro_wapo_woocommerce_post_class', 30, 3 );

	if ( ! function_exists( 'pizzaro_wapo_display_on_loop' ) ) {
		function pizzaro_wapo_display_on_loop() {
			global $product;

			$product_id = pizzaro_wc_get_product_id( $product );
			$product_type = pizzaro_wc_get_product_type( $product );
			$show_on_archive_radio_ids = pizzaro_redux_wapo_show_archive_radio_ids_list();
			
			if( is_object($product) && $product_id > 0 ) {

				$product_type_list = YITH_WAPO::getAllowedProductTypes();

				if( in_array( $product_type, $product_type_list ) ) {

					$types_list = YITH_WAPO_Type::getAllowedGroupTypes( $product_id );

					if( ! empty( $types_list ) ) {
						$radio_groups_html = '';

						$yith_wapo_frontend = new YITH_WAPO_Frontend( YITH_WAPO_VERSION );
						
						ob_start();
						foreach( $types_list as $single_type ) {
							if( in_array( $single_type->id, $show_on_archive_radio_ids ) && $single_type->type == 'radio' ) {
								$yith_wapo_frontend->printSingleGroupType( $product , $single_type );
							}
						}
						$radio_groups_html = ob_get_clean();

						if( ! empty( $radio_groups_html ) ) {
							$search 		= array( '<h3>', '</h3>', 'ywapo_group_container', 'name="ywapo_radio_' );
							$replace 		= array( '<h3><span>', '</span></h3>', 'pz_ywapo_group_container', 'name="ywapo_radio_' . $product_id . '' );
							$radio_groups_html 	= str_replace( $search, $replace, $radio_groups_html );
							echo '<div class="yith_wapo_groups_container">' . $radio_groups_html . '</div>';
						}
					}

				}
			}
		}
	}

	add_action( 'woocommerce_shop_loop_item_title', 'pizzaro_wapo_display_on_loop', 20 );

	if ( ! function_exists( 'pizzaro_wapo_radio_ids_list' ) ) {
		function pizzaro_wapo_radio_ids_list() {
			global $wpdb;
			$radio_ids = array();
			
			$rows = $wpdb->get_results( "SELECT id, label FROM {$wpdb->prefix}yith_wapo_types WHERE type='radio' AND del='0' ORDER BY id ASC" );

			if ( $rows ) {
				foreach ( $rows as $row ) {
					$radio_ids[$row->id] = '#' . $row->id . ' ' . $row->label;
				}
			}
			
			return $radio_ids;
		}
	}

	if ( ! function_exists( 'pizzaro_redux_wapo_default_radio_ids_list' ) ) {
		function pizzaro_redux_wapo_default_radio_ids_list() {
			global $pizzaro_options;

			return isset( $pizzaro_options['wapo_radio_default_style_ids'] ) ? $pizzaro_options['wapo_radio_default_style_ids'] : array();
		}
	}

	if ( ! function_exists( 'pizzaro_redux_wapo_show_archive_radio_ids_list' ) ) {
		function pizzaro_redux_wapo_show_archive_radio_ids_list() {
			global $pizzaro_options;

			return isset( $pizzaro_options['wapo_radio_show_on_shop_archive'] ) ? $pizzaro_options['wapo_radio_show_on_shop_archive'] : array();
		}
	}

	if ( ! function_exists( 'pizzaro_wapo_redux_shop_options' ) ) {
		function pizzaro_wapo_redux_shop_options( $shop_options ) {
			$radio_ids = pizzaro_wapo_radio_ids_list();
			
			if( ! empty( $radio_ids ) ) {
				$shop_options['fields'][] = array(
					'title'		=> esc_html__( 'YITH Add-ons Options', 'pizzaro' ),
					'id'		=> 'wapo_options_start',
					'type'		=> 'section',
					'indent'	=> true
				);
				$shop_options['fields'][] = array(
					'title'     => esc_html__('Default Radio Style', 'pizzaro'),
					'subtitle'  => esc_html__('Select the radio options for the default style.', 'pizzaro'),
					'id'        => 'wapo_radio_default_style_ids',
					'type'      => 'select',
					'multi'     => true,
					'options'   => $radio_ids
				);
				$shop_options['fields'][] = array(
					'title'     => esc_html__('Show on Shop Archive', 'pizzaro'),
					'subtitle'  => esc_html__('Select the radio options to display on shop archive.', 'pizzaro'),
					'id'        => 'wapo_radio_show_on_shop_archive',
					'type'      => 'select',
					'multi'     => true,
					'options'   => $radio_ids
				);
				$shop_options['fields'][] = array(
					'id'		=> 'wapo_options_end',
					'type'		=> 'section',
					'indent'	=> false
				);
			}

			return $shop_options;
		}
	}

	add_filter( 'pizzaro_shop_options_args', 'pizzaro_wapo_redux_shop_options' );

	/**
	 * YITH product addons compatibility issue fix with WC 3.2.0.
	 *
	 */
	if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.2.0', '>=' ) ) {

		function pizzaro_yith_wapo_wc_comp_fix() {
			wp_register_script( 'select2', WC()->plugin_url() . '/assets/js/select2/select2.full.min.js', array( 'jquery' ) );
			wp_enqueue_script( 'select2' );

			wp_register_script( 'selectWoo', WC()->plugin_url() . '/assets/js/selectWoo/selectWoo.full.min.js', array( 'jquery' ) );
			wp_enqueue_script( 'selectWoo' );

			wp_register_script( 'wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select.min.js', array( 'jquery', 'selectWoo' ) );
			wp_enqueue_script( 'wc-enhanced-select' );
		}

		add_action( 'admin_enqueue_scripts', 'pizzaro_yith_wapo_wc_comp_fix' );
	}
}

if ( is_yith_wcqv_activated() ) {
	$yith_wcqv = YITH_WCQV_Frontend();
	remove_action( 'woocommerce_after_shop_loop_item', array( $yith_wcqv, 'yith_add_quick_view_button' ), 15 );
	add_action( 'pizzaro_product_item_hover_area', array( $yith_wcqv, 'yith_add_quick_view_button' ), 30 );
}

if( class_exists( 'WC_Email_Cart' ) ) {
	$cxecrt = WC_Email_Cart::get_instance();
	if ( 'yes' == cxecrt_get_option( 'cxecrt_show_cart_page_button' ) ) {
		remove_action( 'woocommerce_cart_collaterals', array( $cxecrt, 'cart_page_call_to_action' ) );
		add_action( 'woocommerce_cart_collaterals', array( $cxecrt, 'cart_page_call_to_action' ) );
	}
}