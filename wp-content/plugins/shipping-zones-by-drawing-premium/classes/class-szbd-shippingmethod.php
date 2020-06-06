<?php
if (!defined('ABSPATH'))
  {
  exit;
  }

if (is_plugin_active_for_network('woocommerce/woocommerce.php' ) || in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
  {

  function szbd_shipping_method_init()
    {
    if (!class_exists('WC_SZBD_Shipping_Method'))
      {
      class WC_SZBD_Shipping_Method extends WC_Shipping_Method
        {

            protected $api;
			static $store_address;
        /**
         * Constructor for shipping class
         *
         * @access public
         * @return void
         */
        public function __construct($instance_id = 0)
          {
          $this->id                 = 'szbd-shipping-method';
          $this->instance_id        = absint($instance_id);
          $this->method_title       = __('Shipping Zones by Drawing', SZBD::TEXT_DOMAIN);
          $this->method_description = __('Shipping method to be used with a drawn delivery zone', SZBD::TEXT_DOMAIN);
          $this->supports           = array(
            'shipping-zones',
            'instance-settings',
            'instance-settings-modal'
          );

add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ));

          $this->init();

          }

        /**
         * Init your settings
         *
         * @access public
         * @return void
         */
        function init()
          {

          // Load the settings API
          $this->init_form_fields();
          $this->init_settings();
          $this->enabled = $this->get_option('enabled');
//Check old options for BW compatibility
 $args   = array(
            'numberposts' => 1,
            'post_type' => 'szbdzones',
           'include' => array(intval($this->get_option('title')))
          );
          $a_zone = get_posts($args);

          if ((is_array($a_zone) || is_object($a_zone)) && !empty($a_zone))
            {
			 $title_pre = $a_zone[0] -> post_title;
			}


		    $title2 = is_string(($this->get_option('title'))) && $this->get_option('title') != ''  ? ($this->get_option('title')) :  __('Shipping Zones by Drawing', SZBD::TEXT_DOMAIN);
			 $title = isset($title_pre)  ? $title_pre: $title2;

		  $map = isset($title_pre)  ? ($this->get_option('title')) : 'none';
          $this->title   = $this->get_option('title2', $title);
          $this->info    = $this->get_option('info');
          $this->rate    = $this->get_option('rate');
		  $this->type     = $this->get_option( 'type', 'class' );
		  $this->rate_mode    = $this->get_option('rate_mode');
		  $this->rate_fixed    = $this->get_option('rate_fixed');
		  $this->rate_distance    = $this->get_option('rate_distance');
          $this->tax_status = $this->get_option( 'tax_status' );
          $this->minamount    = $this->get_option('minamount',0);
		  $this->map    = $this->get_option('map',$map);
          $this->max_radius    = $this->get_option('max_radius');
          $this->max_driving_distance    = $this->get_option('max_driving_distance');
          $this->max_driving_time    = $this->get_option('max_driving_time');
          $this->driving_mode    = $this->get_option('driving_mode');
          $this->distance_unit          = $this->get_option( 'distance_unit', 'metric' );

           add_action('woocommerce_update_options_shipping_' . $this->id, array(
            $this,
            'process_admin_options'
          ));
           	add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'clear_transients' ) );

          }
          public function clear_transients() {
			global $wpdb;

			$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_szbd-shipping-method_%') OR `option_name` LIKE ('_transient_timeout_szbd-shipping-method_%')" );
		}
        function init_form_fields()
          {
          $args           = array(
            'numberposts' => 100,
            'post_type' => 'szbdzones',
            'post_status'      => 'publish',
            'orderby'          => 'title',
          );
          $delivery_zoons = get_posts($args);
          if (is_array($delivery_zoons) || is_object($delivery_zoons))
            {
            $attr_option = array();
            $calc_1      = array();
            foreach ($delivery_zoons as $calc_2)
              {
              $calc_3 = get_the_title($calc_2);
              $calc_1 += array(
                $calc_2->ID => ($calc_3)
              );
              $attr_option = $calc_1;
              }
            $attr_option += array(
              "radius" => esc_html__("By Radius", SZBD::TEXT_DOMAIN),
              "none" => esc_html__("None", SZBD::TEXT_DOMAIN),

            );
            }
          else
            {
            $attr_option = array(
              "radius" => esc_html__("By Radius", SZBD::TEXT_DOMAIN),
              "none" => esc_html__("None", SZBD::TEXT_DOMAIN),

            );
            }
          $settings = array(
            'title2' => array(
              'title' => __('Title', SZBD::TEXT_DOMAIN),
              'type' => 'text',
              'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
              'desc_tip'    => true,
              'default' => '',
 ),
				'title' => array('class' => 'szbd_hide'),

			 'distance_unit'  => array(
					'title'           => __( 'Distance Unit', SZBD::TEXT_DOMAIN ),
					'type'            => 'select',
					 'desc_tip'    => true,
					  'description' => __('Choose what distance unit to use.', SZBD::TEXT_DOMAIN),
					'default'         => 'metric',
					'options'         => array(
						'metric'      => __( 'Metric (km)', SZBD::TEXT_DOMAIN),
						'imperial'    => __( 'Imperial (miles)', SZBD::TEXT_DOMAIN),
					),
				),
			'rate_mode' => array(
                'title'   => __( 'Shipping Rate', SZBD::TEXT_DOMAIN ),
                'type'    => 'select',
                'class'   => 'wc-enhanced-select',
                'default' => 'flat',
				 'desc_tip'    => true,
                'options' => array(
                    'flat' => __( 'Flat Rate', SZBD::TEXT_DOMAIN ),
                    'distance'    => __( 'By transportation distance', SZBD::TEXT_DOMAIN ),
					'fixed_and_distance'    => __( 'By fixed rate + transportation distance', SZBD::TEXT_DOMAIN ),
                ),
            ),
            'rate' => array(
              'title' => __('Flat Rate', SZBD::TEXT_DOMAIN),
              'type' => 'text',
              'description' => __('Enter a shipping flat rate.', SZBD::TEXT_DOMAIN),
              'desc_tip'    => true,
              'default' => '0',
			   'sanitize_callback' => array( $this, 'sanitize_cost' ),
            ),
			 'rate_fixed' => array(
              'title' => __('Fixed Rate', SZBD::TEXT_DOMAIN),
              'type' => 'text',
              'description' => __('Enter a fixed shipping rate.', SZBD::TEXT_DOMAIN),
              'desc_tip'    => true,
              'default' => '0'
            ),
			  'rate_distance' => array(
              'title' => __('Distance Unit Rate', SZBD::TEXT_DOMAIN),
              'type' => 'text',
              'description' => __('Enter the rate per shipping distance unit.', SZBD::TEXT_DOMAIN),
              'desc_tip'    => true,
              'default' => '0'
            ),
            'tax_status' => array(
                'title'   => __( 'Tax status', 'woocommerce' ),
                'type'    => 'select',
                'class'   => 'wc-enhanced-select',
                'default' => 'taxable',
                'options' => array(
                    'taxable' => __( 'Taxable', 'woocommerce' ),
                    'none'    => _x( 'None', 'Tax status', 'woocommerce' ),
                ),
            ),
            'minamount' => array(
              'title' => __('Minimum order amount', SZBD::TEXT_DOMAIN),
              'type' => 'text',
              'description' => __('Select a minimum order amount.', SZBD::TEXT_DOMAIN),
              'desc_tip'    => true,
              'default' => '0'
            ),
			 'driving_mode' => array(
                'title'   => __( 'Transport mode', SZBD::TEXT_DOMAIN ),
                'type'    => 'select',
                'class'   => 'wc-enhanced-select',
                'default' => 'car',
                'options' => array(
                    'car' => __( 'By Car', SZBD::TEXT_DOMAIN ),
                    'bike'    => __( 'By Bike', SZBD::TEXT_DOMAIN ),
                ),
                ),

            array(
		'title'       => __( 'Restrict by Zone', SZBD::TEXT_DOMAIN ),
		'type'        => 'title',
         'description' => __('Mark the restriction as critical if it must be fullfilled. Otherwise, other restrictions will be sufficient', SZBD::TEXT_DOMAIN),

	),
            'map' => array(
              'title' => __('Delivery Zone', SZBD::TEXT_DOMAIN),
              'type' => 'select',
              'description' => __('Select a drawn delivery area or specify the area by a radius', SZBD::TEXT_DOMAIN),
              'desc_tip'    => true,
              'options' => ($attr_option),
               'default' => '',
            ),

             'max_radius' => array(
              'title' => __('Maximum radius', SZBD::TEXT_DOMAIN),
              'type' => 'text',
              'description' => __('Maximum radius (km/miles) from shop address.', SZBD::TEXT_DOMAIN),
              'desc_tip'    => true,
              'default' => '0'
            ),
             'zone_critical' => array(
                 'title' => __('Make critical', SZBD::TEXT_DOMAIN),

                'type'    => 'checkbox',
                'class'   => 'szbd_box',
                'default' => '',


                ),
              array(
		'title'       => __( 'Restrict by Transportation Distance', SZBD::TEXT_DOMAIN ),
		'type'        => 'title',

	),
             'max_driving_distance' => array(
              'title' => __('Maximum transportation distance', SZBD::TEXT_DOMAIN),
              'type' => 'text',
              'description' => __('Limit shipping by maximum transportation distance (km/miles) and the selected mode (car/bike)', SZBD::TEXT_DOMAIN),
              'desc_tip'    => true,
              'default' => '0'
            ),
              'distance_critical' => array(
                 'title' => __('Make critical', SZBD::TEXT_DOMAIN),

                'type'    => 'checkbox',
                'class'   => 'szbd_box',
                'default' => '',


                ),
              array(
		'title'       => __( 'Restrict by Transportation Time', SZBD::TEXT_DOMAIN ),
		'type'        => 'title',

	),
             'max_driving_time' => array(
              'title' => __('Max transportation time', SZBD::TEXT_DOMAIN),
              'type' => 'text',
              'description' => __('Limit shipping by maximum transportation time (minutes) and the selected mode (car/bike)', SZBD::TEXT_DOMAIN),
              'desc_tip'    => true,
              'default' => '0'
            ),
              'time_critical' => array(
                 'title' => __('Make critical', SZBD::TEXT_DOMAIN),

                'type'    => 'checkbox',
                'class'   => 'szbd_box',
                'default' => '',


                ),

          );
		   $shipping_classes = WC()->shipping()->get_shipping_classes();

if ( ! empty( $shipping_classes ) ) {
	$settings['class_costs'] = array(
		'title'       => __( 'Shipping class costs', 'woocommerce' ),
		'type'        => 'title',
		'default'     => '',
		/* translators: %s: URL for link. */
		'description' => sprintf( __( 'These costs can optionally be added based on the <a href="%s">product shipping class</a>.', 'woocommerce' ), admin_url( 'admin.php?page=wc-settings&tab=shipping&section=classes' ) ),
	);
	foreach ( $shipping_classes as $shipping_class ) {
		if ( ! isset( $shipping_class->term_id ) ) {
			continue;
		}
		$settings[ 'class_cost_' . $shipping_class->term_id ] = array(
			/* translators: %s: shipping class name */
			'title'             => sprintf( __( '"%s" shipping class cost', 'woocommerce' ), esc_html( $shipping_class->name ) ),
			'type'              => 'text',
			'placeholder'       => __( 'N/A', 'woocommerce' ),
			'description'       => '',//$cost_desc,
			'default'           => $this->get_option( 'class_cost_' . $shipping_class->slug ), // Before 2.5.0, we used slug here which caused issues with long setting names.
			'desc_tip'          => true,
			'sanitize_callback' => array( $this, 'sanitize_cost' ),
		);
	}

	$settings['no_class_cost'] = array(
		'title'             => __( 'No shipping class cost', 'woocommerce' ),
		'type'              => 'text',
		'placeholder'       => __( 'N/A', 'woocommerce' ),
		'description'       => '',//$cost_desc,
		'default'           => '',
		'desc_tip'          => true,
		'sanitize_callback' => array( $this, 'sanitize_cost' ),
	);

	$settings['type'] = array(
		'title'   => __( 'Calculation type', 'woocommerce' ),
		'type'    => 'select',
		'class'   => 'wc-enhanced-select',
		'default' => 'class',
		'options' => array(
			'class' => __( 'Per class: Charge shipping for each shipping class individually', 'woocommerce' ),
			'order' => __( 'Per order: Charge shipping for the most expensive shipping class', 'woocommerce' ),
		),
	);
}
$this->instance_form_fields = $settings;
          }

        public function calculate_shipping($package = array())
          {

 $google_api_key = get_option('szbd_google_api_key_2', '') !== '' ? get_option('szbd_google_api_key_2', '') : get_option('szbd_google_api_key', '');
if($this->rate_mode !== 'flat' && $google_api_key !== ''){
	if(WC()->session->get( 'szbd_distance_'.$this->driving_mode , null ) !== null ){
		$distance_value      = WC()->session->get( 'szbd_distance_'.$this->driving_mode ,null );
		$fixed_rate = $this->rate_mode == 'fixed_and_distance' ? floatval($this->rate_fixed) : 0;
$unit_converter = $this->distance_unit == 'imperial' ? 1/1.609344 : 1;
$rate =(floatval($distance_value)/1000) * $unit_converter * floatval($this->rate_distance) + $fixed_rate;

	}else{
		$ok_types = array("street_address", "subpremise", "premise","establishment", "route");
$driving_mode = $this->driving_mode == 'car' ? 'driving' : 'bicycling';
$region = empty( $package['destination']['country'] ) ? '' : strtolower( $package['destination']['country'] );
			if ( 'gb' === $region ) {
				$region = 'uk';
			}

			if ( 'ie' === $region ) {
				$ok_types = array("street_address", "subpremise", "premise", "postal_code","establishment");
			}


          $distance = $this->get_api()->get_distance( $this->get_shipping_address_string(), $this->get_customer_address_string( $package ), $driving_mode , 'none', $this->distance_unit, $region );
if(is_array($distance->geocoded_waypoints[0]->types) && is_array($distance->geocoded_waypoints[1]->types )){
$is_geo_types_ok_0 = array_intersect($ok_types, $distance->geocoded_waypoints[0]->types );
$is_geo_types_ok_1 = array_intersect($ok_types, $distance->geocoded_waypoints[1]->types );
$types_is_ok = count($is_geo_types_ok_0) > 0 &&  count($is_geo_types_ok_1) > 0   ? true : false;
}else{
	return;

}



			// Check if a valid response was received.
			if (  !('OK' == $distance->status &&  $types_is_ok)) {


				return;

			}else{


$distance_value      = $distance->routes[0]->legs[0]->distance->value;
 WC()->session->set( 'szbd_distance_'.$this->driving_mode, floatval($distance_value));

WC()->session->set( 'szbd_store_address',  $distance->routes[0]->legs[0]->start_location);
 WC()->session->set( 'szbd_delivery_address',  $distance->routes[0]->legs[0]->end_location) ;
 WC()->session->set( 'szbd_delivery_address_string',  $distance->routes[0]->legs[0]->end_address) ;
 WC()->session->set( 'szbd_delivery_duration_'.$this->driving_mode, floatval( $distance->routes[0]->legs[0]->duration->value)) ;

$fixed_rate = $this->rate_mode == 'fixed_and_distance' ? (floatval($this->rate_fixed)) : 0;
$unit_converter = $this->distance_unit == 'imperial' ? 1/1.6093 : 1;
$rate =(floatval($distance_value)/1000) * $unit_converter * floatval($this->rate_distance) + $fixed_rate;

            }
	} }else if($this->rate_mode == 'flat'){

			$rate = (floatval($this->rate));

		  }else{

			return;
		  }

          $rate = array(
            'label' => $this->title,

             'cost' => isset($rate) ? $rate : null,
             'package' => $package,
            'calc_tax' => 'per_order',

          );
		     // Add shipping class costs.
        $shipping_classes = WC()->shipping()->get_shipping_classes();

        if ( ! empty( $shipping_classes ) ) {
            $found_shipping_classes = $this->find_shipping_classes( $package );
            $highest_class_cost     = 0;

            foreach ( $found_shipping_classes as $shipping_class => $products ) {
                // Also handles BW compatibility when slugs were used instead of ids.
                $shipping_class_term = get_term_by( 'slug', $shipping_class, 'product_shipping_class' );
                $class_cost_string   = $shipping_class_term && $shipping_class_term->term_id ? $this->get_option( 'class_cost_' . $shipping_class_term->term_id, $this->get_option( 'class_cost_' . $shipping_class, '' ) ) : $this->get_option( 'no_class_cost', '' );

                if ( '' === $class_cost_string ) {
                    continue;
                }

                $has_costs  = true;
                $class_cost = $this->evaluate_cost(
                    $class_cost_string,
                    array(
                        'qty'  => array_sum( wp_list_pluck( $products, 'quantity' ) ),
                        'cost' => array_sum( wp_list_pluck( $products, 'line_total' ) ),
                    )
                );

                if ( 'class' === $this->type ) {
                    $rate['cost'] += $class_cost;
                } else {
                    $highest_class_cost = $class_cost > $highest_class_cost ? $class_cost : $highest_class_cost;
                }
            }

            if ( 'order' === $this->type && $highest_class_cost ) {
                $rate['cost'] += $highest_class_cost;
            }
        }

          $this->add_rate($rate);


          }
		   protected function evaluate_cost( $sum, $args = array() ) {
        include_once WC()->plugin_path() . '/includes/libraries/class-wc-eval-math.php';

        // Allow 3rd parties to process shipping cost arguments.
        $args           = apply_filters( 'woocommerce_evaluate_shipping_cost_args', $args, $sum, $this );
        $locale         = localeconv();
        $decimals       = array( wc_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point'], ',' );
        $this->fee_cost = $args['cost'];

        // Expand shortcodes.
        add_shortcode( 'fee', array( $this, 'fee' ) );

        $sum = do_shortcode(
            str_replace(
                array(
                    '[qty]',
                    '[cost]',
                ),
                array(
                    $args['qty'],
                    $args['cost'],
                ),
                $sum
            )
        );

        remove_shortcode( 'fee', array( $this, 'fee' ) );

        // Remove whitespace from string.
        $sum = preg_replace( '/\s+/', '', $sum );

        // Remove locale from string.
        $sum = str_replace( $decimals, '.', $sum );

        // Trim invalid start/end characters.
        $sum = rtrim( ltrim( $sum, "\t\n\r\0\x0B+*/" ), "\t\n\r\0\x0B+-*/" );

        // Do the math.
        return $sum ? WC_Eval_Math::evaluate( $sum ) : 0;
    }
	 public function sanitize_cost( $value ) {
        $value = is_null( $value ) ? '' : $value;
        $value = wp_kses_post( trim( wp_unslash( $value ) ) );
        $value = str_replace( array( get_woocommerce_currency_symbol(), html_entity_decode( get_woocommerce_currency_symbol() ) ), '', $value );
        return $value;
    }
 public function find_shipping_classes( $package ) {
        $found_shipping_classes = array();

        foreach ( $package['contents'] as $item_id => $values ) {
            if ( $values['data']->needs_shipping() ) {
                $found_class = $values['data']->get_shipping_class();

                if ( ! isset( $found_shipping_classes[ $found_class ] ) ) {
                    $found_shipping_classes[ $found_class ] = array();
                }

                $found_shipping_classes[ $found_class ][ $item_id ] = $values;
            }
        }

        return $found_shipping_classes;
    }
public function get_api() {
			if ( is_object( $this->api ) ) {
				return $this->api;
			}


               $google_api_key = get_option('szbd_google_api_key_2', '') !== '' ? get_option('szbd_google_api_key_2', '') : get_option('szbd_google_api_key', '');


			return $this->api = new SZBD_Google_Server_Requests( $google_api_key);
		}
        public function get_shipping_address_string() {
			$address = WC_SZBD_Shipping_Method::get_store_address();
            $address_string   =  implode(',', array_values($address)) ;
            $address_sanitazied = preg_replace("/,+/", ",", $address_string );


			return $address_sanitazied;
		}
		 static function get_store_address(){
if(!isset(self::$store_address)){
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
		self::$store_address = $store_loc;
}else{
	$store_loc = self::$store_address;
}
        return self::$store_address;
    }
        public function get_customer_address_string( $package ) {
			$address = array();

			if ( is_checkout() ) {
				if ( isset( $package['destination']['address'] ) && ! empty( $package['destination']['address'] ) ) {
					$address[] = $package['destination']['address'];
				}

				if ( isset( $package['destination']['address_2'] ) && ! empty( $package['destination']['address_2'] ) ) {
					$address[] = $package['destination']['address_2'];
				}

			if ( isset( $package['destination']['postcode'] ) && ! empty( $package['destination']['postcode'] ) ) {
				$address[] = $package['destination']['postcode'];
			}

				if ( isset( $package['destination']['city'] ) && ! empty( $package['destination']['city'] ) ) {
					$address[] = $package['destination']['city'];
				}
				if ( isset( $package['destination']['state'] ) && ! empty( $package['destination']['state'] ) ) {
				$state = $package['destination']['state'];
				$country = $package['destination']['country'];

				// Convert state code to full name if available
				if ( isset( WC()->countries->states[ $country ], WC()->countries->states[ $country ][ $state ] ) ) {
					$state = WC()->countries->states[ $country ][ $state ];
					$country = WC()->countries->countries[ $country ];
				}
				$address[] = $state;
			}

			if ( isset( $package['destination']['country'] ) && ! empty( $package['destination']['country'] ) ) {
				$country = $package['destination']['country'];

				// Convert country code to full name if available
				if ( isset( WC()->countries->countries[ $country ] ) ) {
					$country = WC()->countries->countries[ $country ];
				}
				$address[] = $country;
			}

			}else{

				// Cart page only has country, state and zipcodes.
			if ( isset( $package['destination']['postcode'] ) && ! empty( $package['destination']['postcode'] ) ) {
				$address[] = $package['destination']['postcode'];
			}

			if ( isset( $package['destination']['state'] ) && ! empty( $package['destination']['state'] ) ) {
				$state = $package['destination']['state'];
				$country = $package['destination']['country'];

				// Convert state code to full name if available
				if ( isset( WC()->countries->states[ $country ], WC()->countries->states[ $country ][ $state ] ) ) {
					$state = WC()->countries->states[ $country ][ $state ];
					$country = WC()->countries->countries[ $country ];
				}
				$address[] = $state;
			}

			if ( isset( $package['destination']['country'] ) && ! empty( $package['destination']['country'] ) ) {
				$country = $package['destination']['country'];

				// Convert country code to full name if available
				if ( isset( WC()->countries->countries[ $country ] ) ) {
					$country = WC()->countries->countries[ $country ];
				}
				$address[] = $country;
			}


			}

			return implode( ', ', $address );
		}
        }
     // }
    }
  }
  add_action('woocommerce_shipping_init', 'szbd_shipping_method_init');
  add_action('woocommerce_checkout_update_order_review', 'szbd_clear_session');
   add_action('szbd_clear_session', 'szbd_clear_session');
  add_action('woocommerce_checkout_update_order_review', 'clear_wc_shipping_rates_cache');

function clear_wc_shipping_rates_cache(){
    $packages = WC()->cart->get_shipping_packages();

    foreach ($packages as $key => $value) {
        $shipping_session = "shipping_for_package_$key";

        unset(WC()->session->$shipping_session);
    }
}

  function szbd_clear_session(){

	 WC()->session->__unset( 'szbd_distance_car');
	  WC()->session->__unset( 'szbd_distance_bike');
 WC()->session->__unset( 'szbd_store_address');
 WC()->session->__unset( 'szbd_delivery_address');
 WC()->session->__unset( 'szbd_delivery_address_string');
 WC()->session->__unset( 'szbd_delivery_duration_car');
 WC()->session->__unset( 'szbd_delivery_duration_bike');
  WC()->session->__unset( 'fdoe_min_shipping_is_szbd');
  }

  function szbd_add_shipping_method($methods)
    {
        if (class_exists('WC_SZBD_Shipping_Method')){
    $methods['szbd-shipping-method'] = new WC_SZBD_Shipping_Method();
    return $methods;
        }
    }
  add_filter('woocommerce_shipping_methods', 'szbd_add_shipping_method');

  function szbd_in_array_field($needle, $needle_field, $haystack, $strict = false)
    {
    if ($strict)
      {
      foreach ($haystack as $item)
        if (isset($item->$needle_field) && $item->$needle_field === $needle)
          return true;
      }
    else
      {
      foreach ($haystack as $item)
        if (isset($item->$needle_field) && $item->$needle_field == $needle)
          return true;
      }
    return false;
    }



  function check_address_2()
    {

    global $wpdb;
    $country            = strtoupper(wc_clean(WC()->customer->get_shipping_country()));
    $state              = strtoupper(wc_clean(WC()->customer->get_shipping_state()));
    $continent          = strtoupper(wc_clean(WC()->countries->get_continent_code_for_country($country)));
    $postcode           = wc_normalize_postcode(wc_clean(WC()->customer->get_shipping_postcode()));
    // Work out criteria for our zone search
    $criteria           = array();
    $criteria[]         = $wpdb->prepare("( ( location_type = 'country' AND location_code = %s )", $country);
    $criteria[]         = $wpdb->prepare("OR ( location_type = 'state' AND location_code = %s )", $country . ':' . $state);
    $criteria[]         = $wpdb->prepare("OR ( location_type = 'continent' AND location_code = %s )", $continent);
    $criteria[]         = "OR ( location_type IS NULL ) )";
    // Postcode range and wildcard matching
    $postcode_locations = $wpdb->get_results("SELECT zone_id, location_code FROM {$wpdb->prefix}woocommerce_shipping_zone_locations WHERE location_type = 'postcode';");
    if ($postcode_locations)
      {
      $zone_ids_with_postcode_rules = array_map('absint', wp_list_pluck($postcode_locations, 'zone_id'));
      $matches                      = wc_postcode_location_matcher($postcode, $postcode_locations, 'zone_id', 'location_code', $country);
      $do_not_match                 = array_unique(array_diff($zone_ids_with_postcode_rules, array_keys($matches)));
      if (!empty($do_not_match))
        {
        $criteria[] = "AND zones.zone_id NOT IN (" . implode(',', $do_not_match) . ")";
        }
      }
    // Get matching zones
    $szbd_zoons = $wpdb->get_results("

            SELECT zones.zone_id FROM {$wpdb->prefix}woocommerce_shipping_zones as zones

            LEFT OUTER JOIN {$wpdb->prefix}woocommerce_shipping_zone_locations as locations ON zones.zone_id = locations.zone_id AND location_type != 'postcode'

            WHERE " . implode(' ', $criteria) . "

           ORDER BY zone_order ASC, zone_id ASC LIMIT 1

        ");

    if ((isset($szbd_zoons) || is_array($szbd_zoons)) && !empty($szbd_zoons) )
      {
      $delivery_zones = WC_Shipping_Zones::get_zones();

      $szbd_zone      = array();

      foreach ((array) $delivery_zones as $p => $a_zone)
        {

        if (szbd_in_array_field($a_zone['zone_id'], 'zone_id', $szbd_zoons))
          {
          foreach ((array) $a_zone['shipping_methods'] as $value)
            {
            $array_latlng = array();
            $value_id     = $value->id;
            $enabled      = $value->enabled;

            if ($enabled == 'yes' && $value_id == 'szbd-shipping-method' )
              {
                 $min_amount   = (float) $value-> minamount;

                // Check if drawn zone
                $do_drawn_map = false;
                $do_radius = false;
                $zone_id = $value->instance_settings['map'];
            if($zone_id !== 'radius' && $zone_id !== 'none' ){
                  $do_drawn_map = true;
                    $zoon_bool =  $value->instance_settings['zone_critical'] == 'yes';


              $meta    = get_post_meta(intval($zone_id), 'szbdzones_metakey', true);
			   // Compatibility with shipping methods created in version 1.1 and lower
			  if($zone_id == ''){ $meta    = get_post_meta(intval($value->instance_settings['title']), 'szbdzones_metakey', true);}
			  //
              if (is_array($meta['geo_coordinates']) && count($meta['geo_coordinates']) > 0)
                {
                $i2 = 0;
                foreach ($meta['geo_coordinates'] as $geo_coordinates)
                  {
                  if ($geo_coordinates[0] != '' && $geo_coordinates[1] != '')
                    {
                    $array_latlng[$i2] = array(
                      $geo_coordinates[0],
                      $geo_coordinates[1]
                    );
                    $i2++;
                    }
                  }
                }
              else
                {
                $array_latlng = null;
                }
                // Check if maximum radius
            }else if($zone_id == 'radius'){
                $zoon_bool =  $value->instance_settings['zone_critical'] == 'yes' ;
                  $do_radius = true;
                    $do_radius_flag = true;
                 $max_radius = floatval(sanitize_text_field( $value->instance_settings['max_radius']));

                 // Collect the store address

            }

                $do_driving_distance = false;
				 $do_bike_distance = false;
                if( $value->instance_settings['max_driving_distance'] !== '0' && $value->instance_settings['max_driving_distance'] !== ''){
                     $do_driving_distance = true;

                      $max_driving_distance = floatval(sanitize_text_field( $value->instance_settings['max_driving_distance']));
                      $driving_distance_bool = $value->instance_settings['distance_critical'] == 'yes';

					   $driving_mode =  $value->instance_settings['driving_mode'];
                       if($driving_mode== 'car'){
                         $do_driving_distance = true;
                        $do_driving_distance_flag = true;
                       }else if($driving_mode== 'bike'){
                         $do_bike_distance_flag = true;
                          $do_bike_distance  = true;
                       }
                }
				if($value->instance_settings['rate_mode'] !== 'flat'){
					  $driving_mode =  $value->instance_settings['driving_mode'];
                       if($driving_mode== 'car'){

                         $do_car_dynamic_rate_flag = true;
                       }else if($driving_mode== 'bike'){
                         $do_bike_dynamic_rate_flag = true;
                       }}

                 $do_driving_time_car = false;
                  $do_driving_time_bike = false;
                 if( $value->instance_settings['max_driving_time'] !== '0' && $value->instance_settings['max_driving_time'] !== ''){


                      $max_driving_time = floatval(sanitize_text_field( $value->instance_settings['max_driving_time']));
                      $driving_time_bool =  $value->instance_settings['time_critical'] == 'yes';
                       $driving_mode =  $value->instance_settings['driving_mode'];
                       if($driving_mode== 'car'){
                         $do_driving_time_car = true;
                        $do_driving_time_car_flag = true;
                       }else if($driving_mode== 'bike'){
                         $do_driving_time_bike_flag = true;
                          $do_driving_time_bike = true;
                       }
                }








              $szbd_zone[] = array(
                'zone_id' => $value->instance_id ,
                'cost' => $value->rate,
                'wc_price_cost' => wc_price($value->rate),
                'geo_coordinates' => $array_latlng,
                'value_id' => $value->get_rate_id(),
                'min_amount' => (float) $value-> minamount,
                 'min_amount_formatted' => wc_price( $value-> minamount),


                 'max_radius' => $do_radius ? array('radius' => $max_radius, 'bool' => $zoon_bool ) : false,
                   'drawn_map' => $do_drawn_map ? array( 'geo_coordinates' => $array_latlng,'bool' => $zoon_bool) : false,

                 'max_driving_distance' => $do_driving_distance ? array( 'distance' => $max_driving_distance, 'bool' => $driving_distance_bool) : false,
				   'max_bike_distance' => $do_bike_distance ? array( 'distance' => $max_driving_distance, 'bool' => $driving_distance_bool) : false,
                  'max_driving_time_car' => $do_driving_time_car ? array( 'time' => $max_driving_time, 'bool' => $driving_time_bool) : false,
                   'max_driving_time_bike' => $do_driving_time_bike ? array( 'time' => $max_driving_time, 'bool' => $driving_time_bool) : false,
				   'distance_unit' => $value->instance_settings['distance_unit'] == 'metric' ? 'km' : 'miles',
				   'transport_mode' =>  $value->instance_settings['driving_mode'],
				    'rate_mode' =>  $value->instance_settings['rate_mode'],
					 'rate_fixed' =>  $value->instance_settings['rate_fixed'],
					  'rate_distance' =>  $value->instance_settings['rate_distance'],





              );
              }
           // }
          }
          }
        }

      wp_send_json(array(
        'szbd_zones' => $szbd_zone,
        'status' => true,
         'exclude' => get_option('szbd_exclude_shipping_methods', 'no'),
         'tot_amount' =>  (float) WC()->cart->get_cart_contents_total() + WC()->cart->get_cart_contents_tax(),

         'do_driving_time_car' =>  isset($do_driving_time_car_flag),
          'do_driving_time_bike' =>  isset($do_driving_time_bike_flag),
           'do_radius' =>  isset( $do_radius_flag),
           'do_driving_dist' => isset($do_driving_distance_flag),
		    'do_bike_dist' => isset($do_bike_distance_flag),
			 'do_dynamic_rate_car' => isset($do_car_dynamic_rate_flag),
			  'do_dynamic_rate_bike' => isset($do_bike_dynamic_rate_flag),

		     'store_address' => WC()->session->get( 'szbd_store_address',false),
			  'delivery_address' => WC()->session->get( 'szbd_delivery_address',false),
			   'delivery_address_string' => WC()->session->get( 'szbd_delivery_address_string',false),

			   'delivery_duration_driving' => WC()->session->get( 'szbd_delivery_duration_car',false),
			    'distance_driving' => WC()->session->get( 'szbd_distance_car',false),

				 'delivery_duration_bicycle' => WC()->session->get( 'szbd_delivery_duration_bike',false),
			    'distance_bicycle' => WC()->session->get( 'szbd_distance_bike',false),







      ));
      }
    else
      {
      wp_send_json(array(
        'szbd_zones' => array(),
        'status' => true,
         'exclude' => get_option('szbd_exclude_shipping_methods', 'no'),
          'tot_amount' =>  (float) WC()->cart->get_total('float'),


      ));
      }
    }


  add_action('wp_ajax_nopriv_check_address_2', 'check_address_2');
  add_action('wp_ajax_check_address_2', 'check_address_2');

  add_action('wp_enqueue_scripts', 'enqueue_scripts_aro',999);
  function enqueue_scripts_aro()
   {
	if(WC()-> cart-> needs_shipping() === false)
	{
		return;
	}
        if(is_checkout() && get_option( 'szbd_deactivate_google', 'no' ) == 'no'){

    $google_api_key = get_option('szbd_google_api_key', '');

    wp_enqueue_script('szbd-google-autocomplete-2', 'https://maps.googleapis.com/maps/api/js?v=3&libraries=geometry,places&types=address' . '' . '&key=' . $google_api_key);

    wp_enqueue_script('shipping-del-aro', SZBD_PREM_PLUGINDIRURL . 'assets/szbd-prem.js', array(
      'jquery',
      'wc-checkout',
      'szbd-google-autocomplete-2'
    ),SZBD_PREM_VERSION, true);
    wp_localize_script( 'shipping-del-aro', 'szbd',
                       array(
                             'checkout_string_1'=> __( 'There are no shipping options available. Please ensure that your address has been entered correctly, or contact us if you need any help.', 'woocommerce' ),
                             'checkout_string_2'=> __('Minimum order value is','szbd'),
							    'checkout_string_3'=> __('Your are to far away. We only make deliverys within','szbd'),
                              'store_address' => WC_SZBD_Shipping_Method::get_store_address(),
							  'debug' => get_option('szbd_debug','no') == 'yes' ? 1 : 0,
							   'deactivate_postcode' => get_option('szbd_deactivate_postcode','no') == 'yes' ? 1 : 0,

                      ) );
      wp_enqueue_style('shipping-del-aro-style', SZBD_PREM_PLUGINDIRURL . 'assets/szbd.css',SZBD_PREM_VERSION);
	   /* wp_enqueue_script('fdoe-autocomplete',  SZBD_PREM_PLUGINDIRURL . 'assets/autocomplete.js',
						   array(
      'jquery',
      'wc-checkout',
      'szbd-google-autocomplete-2',
	  'shipping-del-aro'
    ),
						  true);*/
    }else if(is_checkout() && get_option( 'szbd_deactivate_google', 'no' ) == 'yes'){

         wp_enqueue_script('shipping-del-aro', SZBD_PREM_PLUGINDIRURL . '/assets/szbd-prem.js', array(
      'jquery',
      'wc-checkout',

    ),SZBD_PREM_VERSION, true);
          wp_localize_script( 'shipping-del-aro', 'szbd',
                       array(
                             'checkout_string_1'=> __( 'There are no shipping options available. Please ensure that your address has been entered correctly, or contact us if you need any help.', 'woocommerce' ),
                             'checkout_string_2'=> __('Minimum order value is','szbd'),
							   'checkout_string_3'=> __('Your are to far away. We only make deliverys within','szbd'),
                             'store_address' => WC_SZBD_Shipping_Method::get_store_address(),
							   'debug' => get_option('szbd_debug','no') == 'yes' ? 1 : 0,
							   'deactivate_postcode' => get_option('szbd_deactivate_postcode','no') == 'yes' ? 1 : 0,
                      ) );
          wp_enqueue_style('shipping-del-aro-style', SZBD_PREM_PLUGINDIRURL . '/assets/szbd.css',SZBD_PREM_VERSION);





    }
    }
    function disable_shipping_calc_on_cart( $show_shipping ) {


    if( is_cart() && get_option('szbd_hide_shipping_cart','no') == 'yes' ) {
        return false;
    }
    return $show_shipping;
}
add_filter( 'woocommerce_cart_ready_to_calc_shipping', 'disable_shipping_calc_on_cart', 999 );

  }




if ( ! class_exists( 'SZBD_Google_Server_Requests' ) ) {


	class SZBD_Google_Server_Requests {


      const API_URL = 'https://maps.googleapis.com/maps/api/directions/json';


		public $api_key;


		public $debug;


		public function __construct( $api_key) {
			$this->api_key = $api_key;

		}


		private function perform_request( $params ) {
			$args = array(
				'timeout'     => 4, // Default to 3 seconds.
				'redirection' => 0,
				'httpversion' => '1.0',
				'sslverify'   => false,
				'blocking'    => true,
				'user-agent'  => 'PHP ' . PHP_VERSION . '/WooCommerce ' . get_option( 'woocommerce_db_version' ),
			);

			$response = wp_remote_get( self::API_URL . '?' . ( ! empty( $this->api_key ) ? 'key=' . $this->api_key . '&' : '' ) . $params, $args );

			if ( get_option('szbd_debug','no') == 'yes') {
				parse_str( $params, $params_debug );
				wc_clear_notices();
				wc_add_notice( 'SERVER to GOOGLE CALL: <br/>'.'Request: <br/>' . '<pre>' . print_r( $params_debug, true ) . '</pre>', 'notice' );
				wc_add_notice( 'Response: <br/>' . '<pre>' . print_r( $response['body'], true ) . '</pre>', 'notice' );

			}

			if ( is_wp_error( $response ) ) {
				throw new Exception( $response );
			}

			return $response;
		}


		public function get_distance( $origin, $destination, $mode , $avoid = '', $units = 'metric', $region = false ) {

				$params = array();
				$params['origin'] = $origin;
				$params['destination'] = $destination;
				$params['mode'] = $mode;
				if ( ! empty( $avoid ) ) {
					$params['avoid'] = $avoid;
				}
				$params['units'] = $units;

				if ( ! empty( $region ) ) {
					$params['region'] = $region;
				}

				$params   = http_build_query( $params );
				$response = $this->perform_request( $params );
				$distance = json_decode( $response['body'] );


            return $distance;
		}
	}
}
