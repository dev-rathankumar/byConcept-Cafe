<?php
/**
 * @author Mohammad Mursaleen
 */
class myCred_wooplus_Settings {
	
	public static function init() {
		add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
		add_action( 'woocommerce_settings_tabs_settings_wooplus', __CLASS__ . '::settings_tab' );
		add_action( 'woocommerce_update_options_settings_wooplus', __CLASS__ . '::update_settings' );
		add_action( 'woocommerce_sections_settings_wooplus', __CLASS__ . '::get_sections' );
		add_action( 'admin_enqueue_scripts', __CLASS__ .'::add_js_for_rewards_points_tab' );
	}

	public static function add_js_for_rewards_points_tab() {
		
        wp_enqueue_script( 'mycred_reward_points_tab_js', plugins_url('assets/js/reward_points_backend.js', MYCRED_WOOPLUS_THIS), array(), '1.0' );
    }
  
	public static function get_sections() {
			global $current_section;
			 
			//var_dump($current_section);
			
			if($current_section == ''){$current_section = 'my_coupons';}
			
			
			$sections = array(
				'my_coupons' 		=> __( 'Coupons', 'mycredpartwoo' ),
				'ristrict_products' => __( 'Restrict Products', 'mycredpartwoo' ),
			 	'points_history' 	=> __( 'Points History', 'mycredpartwoo' ),
			 	'partial_payments' 	=> __( 'Partial Payments', 'mycredpartwoo' ),
			 	'reward_points' 	=> __( 'Display Rewards', 'mycredpartwoo' ),
                                'product_referral_cookie' => __( 'Product Referral Cookie ', 'mycredpartwoo' )

                            );
			if ( empty( $sections ) || 1 === sizeof( $sections ) ) {
				return;
			}
			
			echo '<ul class="subsubsub">';
			
				$array_keys = array_keys( $sections );
				
				foreach ( $sections as $id => $label ) {
							
					echo '<li><a href="' . admin_url( 'admin.php?page=wc-settings&tab=settings_wooplus&section=' . sanitize_title( $id ) ) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . $label . '</a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . ' </li>';
				
				}
			echo '</ul><br class="clear" />';	
			
      }    
    /**
     * Add a new settings tab to the WooCommerce settings tabs array.
     *
     * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Custom Fee tab.
     * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Custom Fee tab.
     */
    public static function add_settings_tab( $settings_tabs ) {
		
        $settings_tabs['settings_wooplus'] = __( 'myCred', 'mycredpartwoo' );
		
        return $settings_tabs;
    }
	
    /**
     * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
     *
     * @uses woocommerce_admin_fields()
     * @uses self::get_settings()
     */
    public static function settings_tab() {
		
        woocommerce_admin_fields( self::get_settings() );
		
    }

    /**
     * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
     *
     * @uses woocommerce_update_options()
     * @uses self::get_settings()
     */

    public static function update_settings() {
		
        woocommerce_update_options( self::get_settings() );
		
    }
    /**
     * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
     *
     * @return array Array of settings for @see woocommerce_admin_fields() function.
     */
    public static function get_settings( $section = null) {
		
		global $current_section , $mycred_partial_payment;
		 
		$prefs  = mycred_part_woo_account_settings();
		
       switch( $current_section ){

            case 'my_coupons' :
		$settings = array(
						'section_title' => array(
							'name'     => __( 'Badges and ranks coupon settings', 'mycredpartwoo' ),
							'type'     => 'title',
							'desc'     => '',
							'id'       => 'wdvc_tab_demo_section_title',
							'desc_tip' => true,
						),
						'mycred_wooplus_show_ranks' => array(	
							'name'     => __( 'Ranks', 'mycredpartwoo' ),
							'type'     => 'checkbox',
							'desc'     => __( 'Enable this option to reward users on achieving ranks. Settings will appear in ranks edit window once enabled', 'mycredpartwoo' ),
							'id'       => 'mycred_wooplus_show_ranks',
							'desc_tip' => true,
						),
						'mycred_wooplus_show_badges' => array(	
							'name'     => __( 'Badges', 'mycredpartwoo' ),
							'type'     => 'checkbox',
							'desc'     => __( 'Enable this option to reward users on achieving Badges. Settings will appear in Badge edit window once enabled', 'mycredpartwoo' ),
							'id'       => 'mycred_wooplus_show_badges',
							'desc_tip' => true,
						),
						'section_end' => array(
							 'type'   => 'sectionend',
							 'id'     => 'wooplus_section_end',
						) ,
					);

            break;
			case 'reward_points' :
				$store_currency = get_option('woocommerce_currency');
				$settings = array(
						'section_title' => array(
							'name'     => __( 'Reward Point settings', 'mycredpartwoo' ),
							'type'     => 'title',
							'desc'     => '',
							'id'       => 'wdvc_tab_demo_section_title',
							'desc_tip' => true,
						),
						'reward_single_page_product' => array(	
							'name'     => __( 'Single Page Product', 'mycredpartwoo' ),
							'type'     => 'checkbox',
							'desc'     => esc_html__( 'Show earn points on single product page', 'mycredpartwoo' ),
							'id'       => 'reward_single_page_product',
							'desc_tip' => true,
						),
						'reward_checkout_product_meta' => array(
							'name'     => __( 'Checkout Product Meta', 'mycredpartwoo' ),
							'type'     => 'checkbox',
							'desc'     => esc_html__( 'Show earn points on per product on checkout page', 'mycredpartwoo' ),
							'id'       => 'reward_checkout_product_meta',
							'desc_tip' => true,
						),
						'reward_checkout_product_total' => array(	
							'name'     => __( 'Checkout Product Total', 'mycredpartwoo' ),
							'type' 	   => 'checkbox',
							'desc'     => esc_html__( 'Enable this option to show total earn points on top of the checkout page', 'mycredpartwoo' ),
							'id'       => 'reward_checkout_product_total',
							'desc_tip' => true,
						),
						'reward_cart_product_meta' => array(
							'name'     => __( 'Cart Product Meta', 'mycredpartwoo' ),
							'type'     => 'checkbox',
							'desc'     => esc_html__( 'Show earn points on per product on cart page', 'mycredpartwoo' ),
							'id'       => 'reward_cart_product_meta',
							'desc_tip' => true,
						),
						'reward_cart_product_total' => array(	
							'name'     => __( 'Cart Product Total', 'mycredpartwoo' ),
							'type' 	   => 'checkbox',
							'desc'     => esc_html__( 'Enable this option to show total earn points on top of the cart page', 'mycredpartwoo' ),
							'id'       => 'reward_cart_product_total',
							'desc_tip' => true,
						),
						'section_end' => array(
							'type'   => 'sectionend',
							'id'     => 'wooplus_section_end',
					   	) ,
						'section_title_1' => array(
							'name'     => __( 'Global Reward Setting', 'mycredpartwoo' ),
							'type'     => 'title',
							'desc'     => '',
							'id'       => 'wdvc_tab_global_reward_setting',
							'desc_tip' => true,
						),
						'reward_points_global' => array(	
							'name'     => __( 'Reward Points on cart total', 'mycredpartwoo' ),
							'type'     => 'checkbox',
							'desc'     => esc_html__( 'Reward Points on cart total instead of single product', 'mycredpartwoo' ),
							'id'       => 'reward_points_global',
							'desc_tip' => true,
						),
						'mycred_point_type' => array(
							'title'       => __( 'Point Type', 'mycredpartwoo' ),
							'desc'        => __( 'Select the point type a user can reward with.', 'mycredpartwoo' ),
							'id'          => 'mycred_point_type',
							'class'       => 'wc-enhanced-select',
							'css'         => 'min-width:300px;',
							'type'        => 'select',
							'options'     => mycred_get_types( true ),
							'desc_tip'    => false,
						),
						'reward_points_global_type' => array(
							'title'       => __( 'Reward Point In', 'mycredpartwoo' ),
							'desc'        => __( 'Select the point type in which user will reward. fixed or percentage', 'mycredpartwoo' ),
							'id'          => 'reward_points_global_type',
							'class'       => 'wc-enhanced-select',
							'css'         => 'min-width:300px;',
							'type'        => 'select',
							'options'     => array(
								'fixed' => 'Fixed',
								'percentage' => 'Percentage',
								'exchange' => 'Exchange Rate'
							),
							'desc_tip'    => false,
						),
						'reward_points_global_type_val' => array(
							'title'       => __( 'Reward point Value', 'mycredpartwoo' ),
							'desc'        => __( 'This value will worked as fixed or percentage depend on above value.', 'mycredpartwoo' ),
							'id'          => 'reward_points_global_type_val',
							'type'        => 'text',
							'placeholder' => __( 'Required', 'mycredpartwoo' ),
							'desc_tip'    => false
						),
						'reward_points_exchange_rate' => array(
							'title'       => __( 'Exchange Rate', 'mycredpartwoo' ),
							'desc'        => __( 'How much is 1 point worth in your store currency ('. $store_currency .')?', 'mycredpartwoo' ),
							'id'          => 'reward_points_exchange_rate',
							'type'        => 'text',
							'placeholder' => __( 'Required only if you selected exchange rate', 'mycredpartwoo' ),
							'desc_tip'    => false
						),
						'reward_points_global_message' => array(
							'title'       => __( 'Reward Points Message', 'mycredpartwoo' ),
							'desc'        => __( 'Custom message to show on checkout page. use {points} to replace with points and {type} to replace with point type.', 'mycredpartwoo' ),
							'id'          => 'reward_points_global_message',
							'type'        => 'textarea',
							'placeholder' => __( 'Required', 'mycredpartwoo' ),
							'desc_tip'    => true
						),
						'section_end_1' => array(
							 'type'   => 'sectionend',
							 'id'     => 'wooplus_section_end',
						) ,
					);

            break;
            case 'ristrict_products':
              
			$settings = array(
							'section_title' => array(
								'name'     => __( 'Restrict Product Based on Ranks or Badges', 'mycredpartwoo' ),
								'type'     => 'title',
								'desc'     => '',
								'id'       => 'wdvc_tab_demo_section_title',
								'desc_tip' => true,
							),

							'show_product_ristrict_product' => array(
								'name'     => __( 'Enable', 'mycredpartwoo' ),
								'type'     => 'checkbox',
								'desc'     => __( 'With this option you will have new settings in single product edit window to restrict
								products based on either ranks or badges.', 'mycredpartwoo' ),
								'id'       => 'wooplus_ristrict_product',
								'desc_tip' => true,
							),
							'section_end' => array(
								 'type'   => 'sectionend',
								 'id'     => 'wooplus_section_end',
							)
				); 
			  
			  
            break;
            case 'points_history':
            
			$settings = array(
							'section_title' => array(
								'name'     => __( 'Points History', 'mycredpartwoo' ),
								'type'     => 'title',
								'desc'     => '',
								'id'       => 'wdvc_tab_demo_section_title',
								'desc_tip' => true,
							),

							'show_product_points_history' => array(
								'name'     => __( 'Enable', 'mycredpartwoo' ),
								'type' 	   => 'checkbox',
								'desc'     => __( 'This will display points history in my account page of logged in user.', 'mycredpartwoo' ),
								'id'       => 'wooplus_points_history',
								'desc_tip' => true,
							),
							'section_end'  => array(
								 'type'	   => 'sectionend',
								 'id'      => 'wooplus_section_end',
							)
        ); 
		
            break;
			case 'partial_payments':
              
		$settings = array(
			array(
				'title'       => __( 'Partial Payments', 'mycredpartwoo' ),
				'desc'        => __( 'Partial payments allows your users to pay for parts of the order using points. The remaining amount is paid using one of your active gateways.', 'mycredpartwoo' ),
				'type'        => 'title',
				'id'          => 'partial_payment_options',
			),
			array(
				'title'       => __( 'Do you want to use Partial Payment', 'mycredpartwoo' ),
				'desc'        => __( 'This option disabled the functionality of partial payment from checkout', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payment_switch',
				'default'     => 'enable',
				'type'        => 'radio',
				'options'     => array(
					'enable'      => __( 'Enable', 'mycredpartwoo' ),
					'disable'       => __( 'Disabled', 'mycredpartwoo' ),
				),
				'autoload'    => false,
				'desc_tip'    => true
			),
			array(
				'title'       => __( 'Position', 'mycredpartwoo' ),
				'desc'        => __( 'This controls where the partial payment form is shown on the checkout page.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[position]',
				'default'     => $mycred_partial_payment['position'],
				'type'        => 'radio',
				'options'     => array(
					'none'        => __( 'Disabled - I will insert the form myself using my theme template files.', 'mycredpartwoo' ),
					'before'      => __( 'Before Order Total', 'mycredpartwoo' ),
					'after'       => __( 'After Order Total', 'mycredpartwoo' ),
				),
				'autoload'    => false,
				'desc_tip'    => true
			),
			array(
				'title'       => __( 'Point Type', 'mycredpartwoo' ),
				'desc'        => __( 'Select the point type a user can pay with.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[point_type]',
				'class'       => 'wc-enhanced-select',
				'css'         => 'min-width:300px;',
				'default'     => $mycred_partial_payment['point_type'],
				'type'        => 'select',
				'options'     => mycred_get_types( true ),
				'desc_tip'    => true,
			),
			array(
				'title'       => __( 'Exchange Rate', 'mycredpartwoo' ),
				'desc'        => __( 'How much is 1 point worth in your store currency?', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[exchange]',
				'type'        => 'text',
				'placeholder' => __( 'Required', 'mycredpartwoo' ),
				'default'     => $mycred_partial_payment['exchange'],
				'desc_tip'    => false
			),
			array(
				'title'       => __( 'Minimum', 'mycredpartwoo' ),
				'desc'        => __( 'The minimum amount of points a user must pay. Use zero to disable and allow users to checkout without any point payment.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[min]',
				'type'        => 'text',
				'placeholder' => __( 'Required', 'mycredpartwoo' ),
				'default'     => $mycred_partial_payment['min'],
				'desc_tip'    => false
			),
			array(
				'title'       => __( 'Maximum', 'mycredpartwoo' ),
				'desc'        => __( 'The maximum percentage of the cart total a user can pay using points.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[max]',
				'type'        => 'text',
				'placeholder' => __( 'Required', 'mycredpartwoo' ),
				'default'     => $mycred_partial_payment['max'],
				'desc_tip'    => false
			),
			array(
				'title'       => __( 'Multiple Payment', 'mycredpartwoo' ),
				'desc'        => __( 'Can users, if they can afford it, make multiple payments?', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[multiple]',
				'class'       => 'wc-enhanced-select',
				'css'         => 'min-width:300px;',
				'default'     => $mycred_partial_payment['multiple'],
				'type'        => 'select',
				'options'     => array(
					'no'          => __( 'No', 'mycredpartwoo' ),
					'yes'         => __( 'Yes', 'mycredpartwoo' )
				),
				'desc_tip'    => true
			),
			array(
				'title'       => __( 'Allow Undo', 'mycredpartwoo' ),
				'desc'        => __( 'Can users undo a partial payment? Undoing a partial payment will refund the amount the user paid.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[undo]',
				'class'       => 'wc-enhanced-select',
				'css'         => 'min-width:300px;',
				'default'     => $mycred_partial_payment['undo'],
				'type'        => 'select',
				'options'     => array(
					'no'          => __( 'No', 'mycredpartwoo' ),
					'yes'         => __( 'Yes', 'mycredpartwoo' )
				),
				'desc_tip'    => true
			),
			array(
				'title'       => __( 'Store Rewards', 'mycredpartwoo' ),
				'desc'        => __( 'If you are rewarding your users with points for store purchases, how do you want to handle partial point payments?', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[rewards]',
				'class'       => 'wc-enhanced-select',
				'css'         => 'min-width:300px;',
				'default'     => $mycred_partial_payment['rewards'],
				'type'        => 'select',
				'options'     => array(
					1             => __( 'I do not use store rewards', 'mycredpartwoo' ),
					2             => __( 'Deduct the partial payment amount from the amount the user gets in reward', 'mycredpartwoo' ),
					3             => __( 'Ignore partial payments and let rewards payout the appropriate amount', 'mycredpartwoo' )
				)
			),
			array(
				'title'       => __( 'Pay for: Tax', 'mycredpartwoo' ),
				'desc'        => __( 'Can points to be used to pay for taxes? This should be set to "No" if prices includes taxes.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[before_tax]',
				'class'       => 'wc-enhanced-select',
				'default'     => $mycred_partial_payment['before_tax'],
				'type'        => 'select',
				'options'     => array(
					'no'          => __( 'No', 'mycredpartwoo' ),
					'yes'         => __( 'Yes', 'mycredpartwoo' )
				)
			),
			array(
				'title'       => __( 'Pay for: Shipping', 'mycredpartwoo' ),
				'desc'        => __( 'Can points be used to pay for shipping costs? Only applicable for orders that needs to be shipped. If you only sell virtual products, this should be set to "No".', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[free_shipping]',
				'class'       => 'wc-enhanced-select',
				'css'         => 'min-width:300px; display: block;',
				'default'     => $mycred_partial_payment['free_shipping'],
				'type'        => 'select',
				'options'     => array(
					'no'          => __( 'No', 'mycredpartwoo' ),
					'yes'         => __( 'Yes', 'mycredpartwoo' )
				)
			),
			array(
				'title'       => __( 'Pay for: Sale Items', 'mycredpartwoo' ),
				'desc'        => __( 'Can points be used to pay for items that are "on sale"?', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[sale_items]',
				'class'       => 'wc-enhanced-select',
				'css'         => 'min-width:300px;',
				'default'     => $mycred_partial_payment['sale_items'],
				'type'        => 'select',
				'options'     => array(
					'no'          => __( 'No', 'mycredpartwoo' ),
					'yes'         => __( 'Yes', 'mycredpartwoo' )
				)
			),
			array( 'type'     => 'separator' ),
			array(
				'title'       => __( 'Selector Type', 'mycredpartwoo' ),
				'desc'        => __( 'This controls how a user indicates the amount they want to pay for the order.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[selecttype]',
				'default'     => $mycred_partial_payment['selecttype'],
				'type'        => 'radio',
				'options'     => array(
					'input'       => __( 'Input Field - The amount needs to be typed in.', 'mycredpartwoo' ),
					'slider'      => __( 'Slider - The amount changes based on the sliders position.', 'mycredpartwoo' ),
				),
				'autoload'    => false,
				'desc_tip'    => true
			),
			array(
				'title'       => __( 'Step', 'mycredpartwoo' ),
				'desc'        => __( 'The point amount that each step increment when using the slider. Ignored with input fields.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[step]',
				'type'        => 'text',
				'default'     => $mycred_partial_payment['step'],
				'desc_tip'    => false
			),
			array( 'type'     => 'separator' ),
			array(
				'title'       => __( 'Title', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[title]',
				'type'        => 'text',
				'placeholder' => __( 'Required', 'mycredpartwoo' ),
				'default'     => $mycred_partial_payment['title'],
				'css'         => 'min-width:300px;'
			),
			array(
				'title'       => __( 'Description', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[desc]',
				'css'         => 'width:50%; height: 100px;',
				'placeholder' => __( 'Optional', 'mycredpartwoo' ),
				'type'        => 'textarea',
				'default'     => $mycred_partial_payment['desc'],
				'autoload'    => false
			),
			array(
				'title'       => __( 'Button Label', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[button]',
				'type'        => 'text',
				'placeholder' => __( 'Required', 'mycredpartwoo' ),
				'default'     => $mycred_partial_payment['button'],
				'css'         => 'min-width:300px;'
			),
			array( 'type'     => 'separator' ),
			array(
				'title'       => __( 'Log Template', 'mycredpartwoo' ),
				'desc'        => __( 'The log template used for partial payments.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[log]',
				'type'        => 'text',
				'default'     => $mycred_partial_payment['log'],
				'placeholder' => __( 'Required', 'mycredpartwoo' ),
				'css'         => 'min-width:300px;'
			),
			array(
				'title'       => __( 'Refund Log Template', 'mycredpartwoo' ),
				'desc'        => __( 'The log template used for partial payment refunds. This is used when you manually remove a coupon in an order or if you allow users to undo their payment before placing the order.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[log_refund]',
				'type'        => 'text',
				'placeholder' => __( 'Required', 'mycredpartwoo' ),
				'default'     => $mycred_partial_payment['log_refund'],
				'css'         => 'min-width:300px;'
			),
			array(
				'title'       => __( 'Refund Message', 'mycredpartwoo' ),
				'desc'        => __( 'Optional message to show to user when a partial payment was refunded.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[refund_message]',
				'type'        => 'text',
				'placeholder' => __( 'Required', 'mycredpartwoo' ),
				'default'     => $mycred_partial_payment['refund_message'],
				'css'         => 'min-width:300px;'
			),
			array(
				'type'        => 'sectionend',
				'id'          => 'partial_payment_options',
			),
			array(
				'title'       => __( 'Checkout Totals', 'mycredpartwoo' ),
				'desc'        => __( 'Select what point related details you would like to show on the checkout page.', 'mycredpartwoo' ),
				'type'        => 'title',
				'id'          => 'partial_payment_checkout_option',
			),
			array(
				'title'       => __( 'Point Cost', 'mycredpartwoo' ),
				'desc'        => __( 'Show the cart cost in points based on our exchange rate.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[checkout_total]',
				'class'       => 'wc-enhanced-select',
				'css'         => 'min-width:300px;',
				'default'     => $mycred_partial_payment['checkout_total'],
				'type'        => 'select',
				'options'     => array(
					'no'          => __( 'No', 'mycredpartwoo' ),
					'cart'        => __( 'In Cart', 'mycredpartwoo' ),
					'checkout'    => __( 'In Checkout', 'mycredpartwoo' ),
					'both'        => __( 'Both Cart and Checkout', 'mycredpartwoo' )
				)
			),
			array(
				'title'       => __( 'Total Label', 'mycredpartwoo' ),
				'desc'        => __( 'The label to use for the total cost. Supports General Template tags.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[checkout_total_label]',
				'type'        => 'text',
				'placeholder' => __( 'Required if enabled', 'mycredpartwoo' ),
				'default'     => $mycred_partial_payment['checkout_total_label'],
				'css'         => 'min-width:300px;'
			),
			array(
				'title'       => __( 'Users Balance', 'mycredpartwoo' ),
				'desc'        => __( 'Show the current users balance.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[checkout_balance]',
				'class'       => 'wc-enhanced-select',
				'css'         => 'min-width:300px;',
				'default'     => $mycred_partial_payment['checkout_balance'],
				'type'        => 'select',
				'options'     => array(
					'no'          => __( 'No', 'mycredpartwoo' ),
					'cart'        => __( 'In Cart', 'mycredpartwoo' ),
					'checkout'    => __( 'In Checkout', 'mycredpartwoo' ),
					'both'        => __( 'Both Cart and Checkout', 'mycredpartwoo' )
				)
			),
			array(
				'title'       => __( 'Balance Label', 'mycredpartwoo' ),
				'desc'        => __( 'The label to use for the balance. Supports General Template tags.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_woo[checkout_balance_label]',
				'type'        => 'text',
				'placeholder' => __( 'Required if enabled', 'mycredpartwoo' ),
				'default'     => $mycred_partial_payment['checkout_balance_label'],
				'css'         => 'min-width:300px;'
			),
			array(
				'type'        => 'sectionend',
				'id'          => 'partial_payment_checkout_option',
			),
			
			// Points History for partial payment section 
			 
			 array(
				'title'       => __( 'Points History', 'mycredpartwoo' ),
				'desc'        => __( 'Option to include your users points related history in the account page. When enabled or if you change the SLUG you will need to reset your permalinks.', 'mycredpartwoo' ),
				'type'        => 'title',
				'id'          => 'partial_payment_options',
			),
			array(
				'title'       => __( 'Page SLUG', 'mycredpartwoo' ),
				'desc'        => __( 'The page slug / endpoint to use. Leave empty to disable.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_account_woo[slug]',
				'type'        => 'text',
				'placeholder' => __( 'Leave empty to disable', 'mycredpartwoo' ),
				'default'     => $prefs['slug'],
				'desc_tip'    => true
			),
			array(
				'title'       => __( 'Page Title', 'mycredpartwoo' ),
				'desc'        => __( 'The page title.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_account_woo[title]',
				'type'        => 'text',
				'placeholder' => __( 'Required', 'mycredpartwoo' ),
				'default'     => $prefs['title'],
				'desc_tip'    => true
			),
			array(
				'title'       => __( 'Description', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_account_woo[desc]',
				'css'         => 'width:50%; height: 100px;',
				'placeholder' => __( 'Optional', 'mycredpartwoo' ),
				'type'        => 'textarea',
				'default'     => $prefs['desc'],
				'autoload'    => false
			),
			array( 'type'     => 'separator' ),
			array(
				'title'       => __( 'Number', 'mycredpartwoo' ),
				'desc'        => __( 'The number of log entries to show per page.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_account_woo[number]',
				'type'        => 'text',
				'placeholder' => __( 'Required', 'mycredpartwoo' ),
				'default'     => $prefs['number'],
				'desc_tip'    => true
			),
			array(
				'title'       => __( 'References', 'mycredpartwoo' ),
				'desc'        => __( 'Option to filter log entries to only show certain references. This can be either a single reference or a comma separated list of references. Leave empty to show everything. If you want to show partial payments / store payments or refunds, use "store".', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_account_woo[show]',
				'type'        => 'text',
				'placeholder' => __( 'Optional', 'mycredpartwoo' ),
				'default'     => $prefs['show'],
				'desc_tip'    => false
			),
			array(
				'title'       => __( 'Navigation', 'mycredpartwoo' ),
				'desc'        => __( 'Show navigation?', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_account_woo[nav]',
				'class'       => 'wc-enhanced-select',
				'css'         => 'min-width:300px;',
				'default'     => $prefs['nav'],
				'type'        => 'select',
				'options'     => array(
					0             => __( 'No', 'mycredpartwoo' ),
					1             => __( 'Yes', 'mycredpartwoo' )
				),
				'desc_tip'    =>  true
			),
			array(
				'title'       => __( 'Point Type', 'mycredpartwoo' ),
				'desc'        => __( 'Select the point type to show.', 'mycredpartwoo' ),
				'id'          => 'mycred_partial_payments_account_woo[point_type]',
				'class'       => 'wc-enhanced-select',
				'css'         => 'min-width:300px;',
				'default'     => $prefs['point_type'],
				'type'        => 'select',
				'options'     => mycred_get_types( true ),
				'desc_tip'    => true,
			),
			array(
				'type'        => 'sectionend',
				'id'          => 'partial_payment_options',
			),
		);
		
		break;
            case 'product_referral_cookie':
                $settings=array(
                    'section_title' => array(
							'name'     => __( 'product referral cookie', 'mycredpartwoo' ),
							'type'     => 'title',
							'desc'     => '',
							'id'       => 'mycred_wooplus_referral_cookie_title',
							'desc_tip' => true,
						),
                    'mycred_wooplus_referral_cookie_name'=>array(
                        'name'     => __( 'Referral Cookie name', 'mycredpartwoo' ),
							'type'     => 'text',
							'desc'     => '',
							'id'       => 'mycred_wooplus_referral_cookie_name',
							'desc_tip' => true,
                                                        'default'  =>'mycred_woo_product_ref',
                                                        'desc'=>__('value of mycred point type will be added to cookie name','mycredpartwoo')
                    ),
                    'mycred_wooplus_referral_cookie_is_expire'=>array(
                        'name'     => __( 'make cookie expire', 'mycredpartwoo' ),
							'type'     => 'checkbox',
//							'desc'     => '',
							'id'       => 'mycred_wooplus_referral_cookie_is_expire',
							'desc_tip' => true,
                                                        'desc'=>__('check this option if you want to make referral cookie expire','mycredpartwoo')
                    ),
                    'mycred_wooplus_referral_cookie_expiration'=>array(
                        'name'     => __( 'Cookie Expiration in days', 'mycredpartwoo' ),
							'type'     => 'number',
							'desc'     => '',
							'id'       => 'mycred_wooplus_referral_cookie_expiration',
//							'desc_tip' => true,
//                                                        'desc'=>__('value in days','mycredpartwoo'),
                                                        'min'=>1
                        
                    )
                    
                );
                break;
		default:
        $settings = array(
						'section_title' => array(
							'name'     => __( 'Badges and ranks coupon settings', 'mycredpartwoo' ),
							'type'     => 'title',
							'desc'     => '',
							'id'       => 'wdvc_tab_demo_section_title',
							'desc_tip' => true,
						),
						'mycred_wooplus_show_ranks' => array(	
							'name'     => __( 'Ranks', 'mycredpartwoo' ),
							'type' => 'checkbox',
							'desc'     => __( 'Enable this option to reward users on achieving ranks. Settings will appear in ranks edit window once enabled', 'mycredpartwoo' ),
							'id'       => 'mycred_wooplus_show_ranks',
							'desc_tip' => true,
						),
						'mycred_wooplus_show_badges' => array(	
							'name'     => __( 'Badges', 'mycredpartwoo' ),
							'type' => 'checkbox',
							'desc'     => __( 'Enable this option to reward users on achieving Badges. Settings will appear in Badge edit window once enabled', 'mycredpartwoo' ),
							'id'       => 'mycred_wooplus_show_badges',
							'desc_tip' => true,
						),
						'section_end' => array(
							 'type'   => 'sectionend',
							 'id'     => 'wooplus_section_end',
						) ,
					);
			}
 
		return apply_filters( 'wc_settings_wdvc_settings', $settings ,$section);
    }



}
myCred_wooplus_Settings::init();