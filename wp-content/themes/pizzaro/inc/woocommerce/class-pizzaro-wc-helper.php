<?php
/**
 * Pizzaro Helper Class for WooCommerce
 */

class Pizzaro_WC_Helper {

	public static function init() {

		// Terms orderby slug order.
		add_filter( 'get_terms_orderby',								array( 'Pizzaro_WC_Helper', 'terms_orderby_slug_order' ), 10, 2 );

		// Automatically apply a coupon passed via URL to the cart.
		add_action( 'wp_loaded',										array( 'Pizzaro_WC_Helper', 'generate_coupon_links' ) );
		add_action( 'woocommerce_add_to_cart', 							array( 'Pizzaro_WC_Helper', 'generate_coupon_links' ) );

		add_filter( 'woocommerce_product_tabs',							array( 'Pizzaro_WC_Helper', 'modify_product_tabs' ) );
		
		// Add options on General Tab
		add_action( 'woocommerce_product_options_general_product_data',	array( 'Pizzaro_WC_Helper', 'product_options_general_product_data' ) );

		// Save options on General Tab
		add_action( 'woocommerce_process_product_meta_simple', 			array( 'Pizzaro_WC_Helper', 'save_product_style_to_product_options' ) );
		add_action( 'woocommerce_process_product_meta_variable', 		array( 'Pizzaro_WC_Helper', 'save_product_style_to_product_options' ) );
		add_action( 'woocommerce_process_product_meta_grouped', 		array( 'Pizzaro_WC_Helper', 'save_product_style_to_product_options' ) );
		add_action( 'woocommerce_process_product_meta_external', 		array( 'Pizzaro_WC_Helper', 'save_product_style_to_product_options' ) );

		// Add Nutritions Tab
		add_action( 'woocommerce_product_write_panel_tabs',				array( 'Pizzaro_WC_Helper', 'add_product_nutrition_panel_tab' ) );
		add_action( 'woocommerce_product_data_panels',					array( 'Pizzaro_WC_Helper', 'add_product_nutrition_panel_data' ) );

		// Save Nutritions Tab
		add_action( 'woocommerce_process_product_meta_simple',			array( 'Pizzaro_WC_Helper',	'save_product_nutrition_panel_data' ) );
		add_action( 'woocommerce_process_product_meta_variable',		array( 'Pizzaro_WC_Helper',	'save_product_nutrition_panel_data' ) );
		add_action( 'woocommerce_process_product_meta_grouped',			array( 'Pizzaro_WC_Helper',	'save_product_nutrition_panel_data' ) );
		add_action( 'woocommerce_process_product_meta_external',		array( 'Pizzaro_WC_Helper',	'save_product_nutrition_panel_data' ) );

	}

	/**
	 * Terms orderby slug order.
	 *
	 * @since 1.0.0
	 */
	public static function terms_orderby_slug_order( $orderby, $args ) {
		if ( isset( $args['orderby'] ) && 'include' == $args['orderby'] ) {
			$include = implode( ',', array_map( 'sanitize_text_field', $args['include'] ) );
			$orderby = "FIELD( t.slug, $include )";
		}

		return $orderby;
	}

	/**
	 * Automatically apply a coupon passed via URL to the cart.
	 *
	 * @since 1.0.0
	 */
	public static function generate_coupon_links() {
		
		if ( ! is_ajax() ) {
			// Bail if WooCommerce or sessions aren't available.
			if ( ! function_exists( 'WC' ) || ! WC()->session ) {
				return;
			}
			
			/**
			 * Filter the coupon code query variable name.
			 *
			 */
			$query_var = apply_filters( 'pizzaro_coupon_links_query_var', 'coupon_code' );
			
			// Bail if a coupon code isn't in the query string.
			if ( empty( $_GET[ $query_var ] ) ) {
				return;
			}
			
			// Set a session cookie to persist the coupon in case the cart is empty.
			WC()->session->set_customer_session_cookie( true );
			
			// Apply the coupon to the cart if necessary.
			if ( ! WC()->cart->has_discount( $_GET[ $query_var ] ) ) {
				// WC_Cart::add_discount() sanitizes the coupon code.
				WC()->cart->add_discount( $_GET[ $query_var ] );
			}
		}
	}

	public static function modify_product_tabs( $tabs ) {
		
		global $product, $post;

		if ( isset( $tabs['description'] ) ) {
			$tabs['description']['callback'] = 'pizzaro_product_description_tab';
		}

		if ( isset( $tabs['reviews'] ) ) {
			$tabs['reviews']['title'] = esc_html__( 'Reviews', 'pizzaro' );
		}

		// Nutritions tab - shows attributes
		$product_id = pizzaro_wc_get_product_id( $product );
		$nutritions = get_post_meta( $product_id, '_nutritions', true );
		if ( $product && ! empty( $nutritions ) ) {
			$tabs['nutrition'] = array(
				'title'    => esc_html__( 'Nutritions', 'pizzaro' ),
				'priority' => 20,
				'callback' => 'pizzaro_product_nutrition_tab'
			);
		}

		return $tabs;
	}

	public static function product_options_general_product_data() {
		echo '<div class="options_group">';
			woocommerce_wp_select( array(
				'id' => '_product_style',
				'label' => esc_html__( 'Product Style', 'pizzaro' ),
				'options' => array(
					''          => esc_html__( 'Default', 'pizzaro' ),
					'style-1'   => esc_html__( 'Style 1', 'pizzaro' ),
					'style-2'   => esc_html__( 'Style 2', 'pizzaro' ),
					'style-3'   => esc_html__( 'Style 3', 'pizzaro' )
				),
				'desc_tip' => true,
				'description' => esc_html__( 'Select product style to display on product page.', 'pizzaro' )
			) );
		echo '</div>';
		echo '<div class="options_group">';
			woocommerce_wp_text_input( array(
				'id'                => '_delivery_time',
				'label'             => esc_html__( 'Delivery Time', 'pizzaro' ),
				'desc_tip'          => true,
				'description'       => esc_html__( 'Set delivery time of product.', 'pizzaro' )
			) );
		echo '</div>';
		echo '<div class="options_group">';
			woocommerce_wp_text_input( array(
				'id'                => '_allergy_alerts',
				'label'             => esc_html__( 'Allergy Alerts', 'pizzaro' ),
				'desc_tip'          => true,
				'description'       => esc_html__( 'Set allergy alerts of product.', 'pizzaro' )
			) );
		echo '</div>';
	}

	public static function save_product_style_to_product_options( $post_id ) {
		$product_style = isset( $_POST['_product_style'] ) ? wc_clean( $_POST['_product_style'] ) : '' ;
		update_post_meta( $post_id, '_product_style', $product_style );

		$delivery_time = isset( $_POST['_delivery_time'] ) ? wc_clean( $_POST['_delivery_time'] ) : '' ;
		update_post_meta( $post_id, '_delivery_time', $delivery_time );

		$allergy_alerts = isset( $_POST['_allergy_alerts'] ) ? wc_clean( $_POST['_allergy_alerts'] ) : '' ;
		update_post_meta( $post_id, '_allergy_alerts', $allergy_alerts );
	}

	public static function add_product_nutrition_panel_tab() {
		?>
		<li class="nutrition_options nutrition_tab">
			<a href="#nutrition_product_data"><?php echo esc_html__( 'Nutritions', 'pizzaro' ); ?></a>
		</li>
		<?php
	}

	public static function add_product_nutrition_panel_data() {
		global $post;
		?>
		<div id="nutrition_product_data" class="panel woocommerce_options_panel">
			<div class="options_group">
				<?php
					$nutritions = get_post_meta( $post->ID, '_nutritions', true );
					wp_editor( htmlspecialchars_decode( $nutritions ), '_nutritions', array() );
				?>
			</div>
		</div>
		<?php
	}

	public static function save_product_nutrition_panel_data( $post_id ) {
		$nutritions = isset( $_POST['_nutritions'] ) ? $_POST['_nutritions'] : '';
		update_post_meta( $post_id, '_nutritions', $nutritions );
	}
}

Pizzaro_WC_Helper::init();