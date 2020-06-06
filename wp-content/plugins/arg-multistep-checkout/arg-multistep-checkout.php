<?php
/*
Plugin Name: ARG MultiStep Checkout for WooCommerce
Plugin URI:  http://argthemes.com/woocommerce-multistep-checkout/
Description: Extension for WooCommerce which helps you change the Checkout page into an easy and modern step-by-step page.
Version:     4.0.2
Author:      ARG Themes
Author URI:  http://argthemes.com
Domain Path: /languages
Text Domain: argMC
WC tested up to: 4.0.1
*/

defined('ABSPATH') or die('No script kiddies please!');

define('ARG_MC_PLUGIN_NAME', 'ARG MultiStep Checkout');
define('ARG_MC_VERSION', '4.0.2');
define('ARG_MC_DIR_PATH', plugin_dir_path(__FILE__));
define('ARG_MC_DIR_URL', plugin_dir_url(__FILE__));
define('ARG_MC_BASENAME', plugin_basename(__FILE__));
define('ARG_MC_MENU_SLUG', 'arg-multistep-checkout');
define('ARG_MC_DOCUMENTATION_URL', 'http://argthemes.com/woocommerce-multistep-checkout/documentation/');
define('ARG_MC_PLUGIN_URL', 'http://argthemes.com/woocommerce-multistep-checkout/');


include_once(ARG_MC_DIR_PATH . 'class-arg-multistep-checkout.php');


//Activate plugin
register_activation_hook(__FILE__, array('argMC\WooCommerceCheckout', 'activate'));

//Init hooks
\argMC\WooCommerceCheckout::initHooks();