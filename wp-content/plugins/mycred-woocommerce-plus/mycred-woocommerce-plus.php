<?php
/**
 * Plugin Name: myCRED WooCommerce Plus - WooCommerce
 * Description: Allows WooCommerce Plus of a WooCommerce orders using Coupons , Restrict Products , Points History , Partial Payments.
 * Version: 1.7.0
 * Tags: points history, ristrict products, Coupons, woocommerce
 * Author: myCRED
 * Author URI: http://mycred.me
 * Author Email: support@mycred.me
 * Requires at least: WP 4.8
 * Tested up to: WP 5.3.2
 * Text Domain: mycredpartwoo
 * Domain Path: /lang
 */
if ( ! class_exists( 'myCRED_WooCommerce_Plus' ) ) :
	final class myCRED_WooCommerce_Plus {

		// Plugin Version
		public $version             = '1.7.0';

		public $slug                = 'mycred-woocommerce-plus';

		public $plugin               = NULL;

		// Instnace
		protected static $_instance = NULL;

		// Current session
		public $session             = NULL;

		public $domain              = 'mycredpartwoo';
		public $update_url          = 'https://mycred.me/api/plugins/';

		/**
		 * Setup Instance
		 * @since 1.0
		 * @version 1.0
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Not allowed
		 * @since 1.0
		 * @version 1.0
		 */
		public function __clone() { _doing_it_wrong( __FUNCTION__, 'Cheatin&#8217; huh?', $this->version ); }

		/**
		 * Not allowed
		 * @since 1.0
		 * @version 1.0
		 */
		public function __wakeup() { _doing_it_wrong( __FUNCTION__, 'Cheatin&#8217; huh?', $this->version ); }

		/**
		 * Define
		 * @since 1.0
		 * @version 1.0
		 */
		private function define( $name, $value, $definable = true ) {
			if ( ! defined( $name ) )
				define( $name, $value );
			elseif ( ! $definable && defined( $name ) )
				_doing_it_wrong( 'myCRED_WooCommerce_Plus->define()', 'Could not define: ' . $name . ' as it is already defined somewhere else!', $this->version );
		}

		/**
		 * Require File
		 * @since 1.0
		 * @version 1.0
		 */
		public function file( $required_file ) {
			if ( file_exists( $required_file ) )
				require_once $required_file;
			else
				_doing_it_wrong( 'myCRED_WooCommerce_Plus->file()', 'Requested file ' . $required_file . ' not found.', $this->version );
		}

		/**
		 * Construct
		 * @since 1.0
		 * @version 1.0
		 */
		public function __construct() {
			
		add_action( 'admin_init', array( $this,'deactivate_previous_partial_payment_plugin' ))	;

		$this->plugin = plugin_basename( __FILE__ );
		$this->define_constants();
		$this->includes();	
		$this->mycred(); 		

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_styles' ),20 );
		add_filter( 'plugin_action_links',  array( $this,  'disable_plugin_deactivation'), 10, 4 );
		 		
		}
		
		public function deactivate_previous_partial_payment_plugin() {

		  if ( is_plugin_active( 'mycred-partial-woo/mycred-partial-woo.php' ) ) {

				deactivate_plugins( 'mycred-partial-woo/mycred-partial-woo.php' );

			} 

		}
		
		
		public function disable_plugin_deactivation( $actions, $plugin_file, $plugin_data, $context ) {

		if ( array_key_exists( 'activate', $actions ) && in_array( $plugin_file, array(
		'mycred-partial-woo/mycred-partial-woo.php' 
		)))
			unset( $actions['activate'] );
		return $actions;

		}
		 
		 /**
		 * Define Constants
		 * First, we start with defining all requires constants if they are not defined already.
		 * @since 1.0
		 * @version 1.0
		 */
		private function define_constants() {

			$this->define( 'MYCRED_WOOPLUS_VERSION',       $this->version );
			$this->define( 'MYCRED_WOOPLUS_SLUG',          $this->slug );
			$this->define( 'MYCRED_WOOPLUS_THIS',          __FILE__ );
			$this->define( 'MYCRED_WOOPLUS_ROOT_DIR',      plugin_dir_path( MYCRED_WOOPLUS_THIS ) );
			$this->define( 'MYCRED_WOOPLUS_INCLUDES_DIR',  MYCRED_WOOPLUS_ROOT_DIR . 'includes/' );
			$this->define( 'MYCRED_WOOPLUS_TEMPLATES_DIR', MYCRED_WOOPLUS_ROOT_DIR . 'templates/' );
			$this->define( 'MYCRED_WOOPLUS_LICENSING_DIR', MYCRED_WOOPLUS_ROOT_DIR . 'Licensing/' );
                        $this->define('myCRED_WOOPLUS_SHORTCODES_DIR', MYCRED_WOOPLUS_INCLUDES_DIR . 'shortcodes/');

		}

		/**
		 * Include Plugin Files
		 * @since 1.0
		 * @version 1.0
		 */
		public function includes() {
			
			// add woocommerce tab settings 
		 	$this->file( MYCRED_WOOPLUS_INCLUDES_DIR . 'mycred-wooplus-settings.php' );
			
			// add product ristrict code
		 	$this->file( MYCRED_WOOPLUS_INCLUDES_DIR . 'mycred-ristrict-product.php' );
			
			// add badge rank coupons code
		 	$this->file( MYCRED_WOOPLUS_INCLUDES_DIR . 'mycred-badge-rank-coupons.php' );

			// add points history code			
		 	$this->file( MYCRED_WOOPLUS_INCLUDES_DIR . 'mycred-points-history.php' );
	
			
			// add partial payment code			
			$this->file( MYCRED_WOOPLUS_ROOT_DIR . 'mycred-partial-woo.php' );
			
			// Reward points product and checkout and global reward option		
		 	$this->file( MYCRED_WOOPLUS_INCLUDES_DIR . 'mycred-reward-product.php' );			
                        //wp-experts-product-refferal
                        $this->file(myCRED_WOOPLUS_SHORTCODES_DIR . 'mycred-woocommerce-referrals-shortcode.php');
                        $this->file(MYCRED_WOOPLUS_INCLUDES_DIR . 'mycred-product-referral-hook.php');
      
		}

		/**
		 * myCRED
		 * @since 1.0
		 * @version 1.0
		 */
		public function mycred() {

			add_action( 'mycred_init',          array( $this, 'start_up' ) );

		}

		/**
		 * Start
		 * @since 1.0
		 * @version 1.0
		 */
		public function start_up() {
			
			// add licensing
			if ( function_exists('mycred_is_membership_active') && mycred_is_membership_active() ) {
				$this->file( MYCRED_WOOPLUS_LICENSING_DIR . 'license.php' );
			}
			else {
				$this->file( MYCRED_WOOPLUS_LICENSING_DIR . 'license-old.php' );
			}

			// Bail if WooCommerce is not installed
			if ( ! class_exists( 'WooCommerce' ) ) return;

		}

			/**
		 * Enqueue Scripts
		 * @since 1.0
		 * @version 1.0
		 */

		public static function enqueue_styles() {

			wp_register_style(
				'woo-plus',
				plugins_url( 'assets/css/woo-plus.css', MYCRED_WOOPLUS_THIS )
			);

			wp_enqueue_style( 'woo-plus' );

		}
 
	}
endif;

function mycred_woo_plus_addon() {
	return myCRED_WooCommerce_Plus::instance();
}
mycred_woo_plus_addon();
