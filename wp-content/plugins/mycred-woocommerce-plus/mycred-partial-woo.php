<?php

if ( ! class_exists( 'myCRED_WooCommerce_Partial' ) ) :
	final class myCRED_WooCommerce_Partial {

	 	// Plugin Version
		public $version             = MYCRED_WOOPLUS_VERSION;
		
		
		public $slug                = 'mycredpartwoo';

		// Instnace
		protected static $_instance = NULL;

		// Current session
		public $session             = NULL;
 

		/**
		 * Setup Instance
		 * @since 1.0
		 * @version 1.2
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
 
 

	 
		public function file( $required_file ) {
			if ( file_exists( $required_file ) )
				require_once $required_file;
			else
				_doing_it_wrong( 'myCRED_WooCommerce_Partial->file()', 'Requested file ' . $required_file . ' not found.', $this->version );
		}

		/**
		 * Construct
		 * @since 1.0
		 * @version 1.2
		 */
		public function __construct() {

			if ( !is_admin() && null !==get_option('mycred_partial_payment_switch') && 'disable'==get_option('mycred_partial_payment_switch') )  return;

			$this->define_constants();
			$this->includes();

			$this->mycred();
			$this->woocommerce();
		}

		/**
		 * Define Constants
		 * First, we start with defining all requires constants if they are not defined already.
		 * @since 1.0
		 * @version 1.2
		 */
		private function define_constants() {
			
			
			 
			$this->define( 'MYCRED_PARTWOO_THIS',          __FILE__ );
			$this->define( 'MYCRED_PARTWOO_ROOT_DIR',      plugin_dir_path( MYCRED_PARTWOO_THIS ) );
			$this->define( 'MYCRED_PARTWOO_INCLUDES_DIR',  MYCRED_PARTWOO_ROOT_DIR . 'includes/partial-payment/' );
			$this->define( 'MYCRED_PARTWOO_TEMPLATES_DIR', MYCRED_PARTWOO_ROOT_DIR . 'templates/' );

		}
		
		/**
		 * Define
		 * @since 1.0
		 * @version 1.2
		 */
		private function define( $name, $value, $definable = true ) {
			if ( ! defined( $name ) )
				define( $name, $value );
			elseif ( ! $definable && defined( $name ) )
				_doing_it_wrong( 'myCRED_WooCommerce_Partial->define()', 'Could not define: ' . $name . ' as it is already defined somewhere else!', $this->version );
		}

		/**
		 * Include Plugin Files
		 * @since 1.0
		 * @version 1.2
		 */
		public function includes() {

			$this->file( MYCRED_PARTWOO_INCLUDES_DIR . 'mycred-partial-woo-functions.php' );
			
			$this->file( MYCRED_PARTWOO_INCLUDES_DIR . 'mycred-partial-woo-checkout.php' );
			$this->file( MYCRED_PARTWOO_INCLUDES_DIR . 'mycred-partial-woo-orders.php' );
			$this->file( MYCRED_PARTWOO_INCLUDES_DIR . 'mycred-partial-woo-settings.php' );
			$this->file( MYCRED_PARTWOO_INCLUDES_DIR . 'mycred-partial-woo-myaccount.php' );

			
		}

		/**
		 * myCRED
		 * @since 1.0
		 * @version 1.2
		 */
		public function mycred() {

			add_action( 'init',              	array( $this, 'load_textdomain' ), 5 );
			add_action( 'mycred_init',          array( $this, 'start_up' ) );
			add_action( 'mycred_front_enqueue', array( $this, 'enqueue_scripts' ) );
			add_filter( 'mycred_run_this',      array( $this, 'reward_adjustment' ) );

		}

		/**
		 * Start
		 * @since 1.0
		 * @version 1.2
		 */
		public function start_up() {

			// Bail if WooCommerce is not installed
			if ( ! class_exists( 'WooCommerce' ) ) return;

			global $mycred_partial_payment, $mycred_remove_partial_payment;

			$mycred_partial_payment        = mycred_part_woo_settings();
			$mycred_remove_partial_payment = false;

			mycred_woo_partial_setup_my_account();

		}

		/**
		 * Enqueue Scripts
		 * @since 1.0
		 * @version 1.2
		 */
		public function enqueue_scripts() {

			if ( ! is_user_logged_in() ) return;

			global $mycred_partial_payment;

			wp_register_script(
				'mycred-partial-payment-woo',
				plugins_url( 'assets/js/mycred-partial-payment.js', MYCRED_PARTWOO_THIS ),
				array( 'jquery' ),
				MYCRED_WOOPLUS_VERSION,
				true
			);

			if ( function_exists( 'is_checkout' ) && is_checkout() ) {

				$user_id = get_current_user_id();

				$mycred  = mycred( $mycred_partial_payment['point_type'] );
				if ( $mycred->exclude_user( $user_id ) ) return;

				$total   = mycred_part_woo_get_total();

				$balance = $mycred->get_users_balance( $user_id );
				$max     = $mycred->number( $total / $mycred_partial_payment['exchange'] );
				if ( $balance < $max )
					$max = $balance;

				$min     = ( ( $mycred_partial_payment['min'] > 0 ) ? $mycred_partial_payment['min'] : 0 );
				$format  = sprintf( get_woocommerce_price_format(), get_woocommerce_currency_symbol(), 'COST' );

				wp_localize_script(
					'mycred-partial-payment-woo',
					'myCREDPartial',
					array(
						'ajaxurl'  => get_permalink( get_option( 'woocommerce_checkout_page_id' ) ),
						'token'    => wp_create_nonce( 'mycred-partial-payment-new' ),
						'reload'   => wp_create_nonce( 'mycred-partial-payment-reload' ),
						'rate'     => $mycred_partial_payment['exchange'],
						'max'      => $max,
						'min'      => $min,
						'total'    => $total,
						'step'     => $mycred->number( $mycred_partial_payment['step'] ),
						'decimals' => $mycred->format['decimals'],
						'format'   => $format
					)
				);

				wp_enqueue_script( 'mycred-partial-payment-woo' );

			}

		}

		/**
		 * Reward Adjustments
		 * When you make a partial payment in points AND you set your store to reward
		 * store purchases using points, this partial payment can in certain setups cause 
		 * a user to get their points back or get more back due to rewards.
		 * This filter will deduct the amount of points a user made as a partial payment (if they made one)
		 * and deduct this amount from the reward amount to prevent the user to ever gaining more than they paid.
		 * @since 1.0
		 * @version 1.2
		 */
		public function reward_adjustment( $run_this ) {

			// We need WooCommerce for this
			if ( ! function_exists( 'wc_get_order' ) ) return $run_this;

			extract( $run_this );

			$prefs = mycred_part_woo_settings();
			if ( ! array_key_exists( 'rewards', $prefs ) || $prefs['rewards'] != 2 ) return $run_this;

			// Only applicable for store rewards payouts
			if ( apply_filters( 'mycred_woo_reward_reference', 'reward', 0, $type ) == $ref && apply_filters( 'mycred_woo_reward_mycred_payment', false, 0 ) === false ) {

				$order_id = absint( $ref_id );
				$order    = wc_get_order( $order_id );

				$discount = $order->get_cart_discount_total();

				// No discount used = nothing for us to do
				if ( $discount <= 0 ) return $run_this;

				if ( $prefs['exchange'] != 1 )
					$discount = $discount / $prefs['exchange'];

				// Stop transaction if the user is getting more than they
				if ( ( $amount - $discount ) <= 0 ) {
					$run_this['amount'] = NULL;
					$run_this['entry']  = '';
				}

				// Deduct the amount the user paid from the reward
				else {
					$run_this['amount'] = ( $amount - $discount );
				}

			}

			return $run_this;

		}

		/**
		 * WooCommerce
		 * @since 1.0
		 * @version 1.2
		 */
		public function woocommerce() {

			add_filter( 'woocommerce_available_payment_gateways', array( $this, 'available_gateways' ) );
			add_filter( 'woocommerce_locate_template',            array( $this, 'locate_templates' ), 10, 3 );

		}

		/**
		 * Available Gateways
		 * Remove the myCRED gateway if it exists as we will replace it
		 * with our own.
		 * @since 1.0
		 * @version 1.2
		 */
		public function available_gateways( $gateways ) {

			if ( ! isset( $gateways['mycred'] ) ) return $gateways;

			unset( $gateways['mycred'] );

			return $gateways;

		}

		/**
		 * Locate Template
		 * Since we are using WooCommerce functions to locate template files,
		 * we need to make sure we always provide our own default template.
		 * @since 1.0
		 * @version 1.2
		 */
		public function locate_templates( $template, $template_name, $template_path ) {


			if ( str_replace( 'woocommerce/templates/', '', $template ) !== $template  && $template_name == 'checkout/mycred-partial-payments.php' ) {


				$default   = MYCRED_PARTWOO_TEMPLATES_DIR . 'mycred-partial-payments.php';

				// Check if the theme has a file we should be using instead
				$_template = locate_template( array( $this->slug . '/mycred-partial-payments.php' ) );
				if ( ! $_template && file_exists( $default ) )
					$_template = $default;

				return $_template;

			}

			return $template;

		}

		/**
		 * Load Textdomain
		 * @since 1.0
		 * @version 1.2
		 */
		public function load_textdomain() {

			// Load Translation
			$locale = apply_filters( 'plugin_locale', get_locale(), $this->slug );

			//load_textdomain( $this->slug, WP_LANG_DIR . '/' . $this->slug . '/' . $this->slug . '-' . $locale . '.mo' );
			load_textdomain( $this->slug, WP_LANG_DIR . '/' . $this->slug . '-' . $locale . '.mo' );
			load_plugin_textdomain( $this->slug, false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

		}
	 
	 

	}
endif;

if ( ! function_exists( 'mycred_woo_partial_payments' ) ) :
function mycred_woo_partial_payments() {
	return myCRED_WooCommerce_Partial::instance();
}
mycred_woo_partial_payments();
endif;

