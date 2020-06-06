<?php
/**
 * Plugin Name: Shipping Zones by Drawing Premium for WooCommerce
 * Plugin URI: https://arosoft.se/product/shipping-zones-drawing-premium/
 * Description: Limit shipping with drawn zones or transportation distances and times. Premium version.
 * Version: 2.1.4
 * Author: Arosoft.se
 * Author URI: https://arosoft.se
 * Developer: Arosoft.se
 * Developer URI: https://arosoft.se
 * Text Domain: szbd
 * Domain Path: /languages
 * WC requires at least: 3.3
 * WC tested up to: 4.0
 * Copyright: Arosoft.se 2020
 * License: GPL v2 or later
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */
if (!defined('ABSPATH'))
  {
  exit;
  }
define('SZBD_PREM_VERSION', '2.1.4');
define('SZBD_PREM_PLUGINDIRURL', plugin_dir_url(__FILE__));
define('SZBD_PREM_PLUGINDIRPATH', plugin_dir_path(__FILE__));

register_activation_hook(__FILE__, array(
  'SZBD',
  'activate'
));
register_uninstall_hook(__FILE__, array(
  'SZBD',
  'uninstall'
));
if ( !class_exists( 'SZBD' ) ) {
class SZBD
  {
  const TEXT_DOMAIN = 'szbd';
  const POST_TITLE = 'szbdzones';
  protected static $_instance = null;
  public $notices;

  static public function activate()
    {
    $admin = get_role('administrator');
    flush_rewrite_rules();
    $admin_capabilities = array(
      'delete_szbdzones',
      'delete_others_szbdzones',
      'delete_private_szbdzones',
      'delete_published_szbdzones',
      'edit_szbdzones',
      'edit_others_szbdzones',
      'edit_private_szbdzones',
      'edit_published_szbdzones',
      'publish_szbdzones',
      'read_private_szbdzones'
    );
    foreach ($admin_capabilities as $capability)
      {
      $admin->add_cap($capability);
      }
    }
  // to be run on plugin uninstallation
  public static function uninstall()
    {

    unregister_post_type('szbdzones');
    flush_rewrite_rules();
    }
  public static function instance()
    {
    NULL === self::$_instance and self::$_instance = new self;
        return self::$_instance;

    }
  public function __construct()
    {

    add_filter('plugin_action_links_' . plugin_basename(__FILE__), array(
      $this,
      'add_action_links'
    ));
    add_action('init', array(
      $this,
      'load_text_domain'
    ));
    add_action('admin_init', array(
      $this,
      'check_environment'
    ));
    add_action('plugins_loaded', array(
      $this,
      'init'
    ), 10);
     add_action( 'admin_notices', array(
                 $this,
                'admin_notices'
            ), 15 );
      add_action( 'wp_enqueue_scripts', array(
			$this,
			'enqueue_scripts'
			) );
        add_action( 'admin_enqueue_scripts', array(
			$this,
			'admin_enqueue_scripts'
			) );
       add_action( 'wp', array(
			$this,
			'init_shortcode'
			) );

        add_filter('manage_edit-szbdzones_columns', array($this,'posts_columns_id'), 2);

    add_action('manage_posts_custom_column', array($this,'posts_custom_id_columns'), 5, 2);



    }
     public function admin_enqueue_scripts(){
          wp_enqueue_style( 'szbd-style-admin', SZBD_PREM_PLUGINDIRURL. '/assets/style-admin.css' ,array(), SZBD_PREM_VERSION );
    }
public function enqueue_scripts(){

if(wc_post_content_has_shortcode( 'szbd' )){
    $deps= array('jquery','underscore');
    if(get_option( 'szbd_deactivate_google', 'no' ) == 'no'){
	$google_api_key = get_option( 'szbd_google_api_key', '' );
	 wp_enqueue_script( 'szbd-script', '//maps.googleapis.com/maps/api/js?key=' . $google_api_key . '&libraries=geometry,places,drawing', array(
         'jquery'
      ), false, true );
     $deps[] = 'szbd-script';
    }
      wp_register_script( 'szbd-script-short', SZBD_PREM_PLUGINDIRURL. '/assets/szbd-shortcode.js', $deps, SZBD_PREM_VERSION,  true );

      wp_enqueue_script( 'szbd-script-short' );
      wp_enqueue_style( 'szbd-style-shortcode', SZBD_PREM_PLUGINDIRURL. '/assets/style-shortcode.css' ,array(), SZBD_PREM_VERSION );
}
}
    // Includes plugin files

        public function includes()

        {
            if (is_admin())
      {
      require_once(SZBD_PREM_PLUGINDIRPATH. 'classes/class-szbd-settings.php');
      require_once(SZBD_PREM_PLUGINDIRPATH. 'classes/class-szbd-admin.php');
      $this->admin = new SZBD_Admin();
      }
    require_once(SZBD_PREM_PLUGINDIRPATH. 'classes/class-szbd-shippingmethod.php');
    require_once(SZBD_PREM_PLUGINDIRPATH. 'classes/class-szbd-the-post.php');

 }

public function init_shortcode(){


 if( !is_admin()  && !is_ajax() && !self::get_environment_warning()){
     require_once(SZBD_PREM_PLUGINDIRPATH. 'classes/class-szbd-shortcode.php');
      $this->shortcode = SZBD_Shortcode::instance();


 }
}


  // For use in future versions. Loads text domain files
  public function load_text_domain()
    {
    load_plugin_textdomain(SZBD::TEXT_DOMAIN, false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }
  // Add setting link to Plugins page
  public function add_action_links($links)
    {
    if (plugin_basename(__FILE__) == "shipping-zones-by-drawing-for-woocommerce/shipping-zones-by-drawing.php")
      {
      $links_add = array(
        '<a href="' . admin_url('admin.php?page=wc-settings&tab=szbdtab') . '">Settings</a>',
        '<a href="https://arosoft.se/product/shipping-zones-by-drawing">Go Premium</a>'
      );
      }
    else
      {
      $links_add = array(
        '<a href="' . admin_url('admin.php?page=wc-settings&tab=szbdtab') . '">Settings</a>'
      );
      }
    return array_merge($links, $links_add);
    }
  // Checks if WooCommerce is active and if not returns error message
  static function get_environment_warning()
    {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    if (!defined('WC_VERSION'))
      {
      return __('Shipping Zones by Drawing requires WooCommerce to be activated to work.', SZBD::TEXT_DOMAIN);
      die();
      }
    //if this is Premium
    else if (is_plugin_active('shipping-zones-by-drawing-for-woocommerce/shipping-zones-by-drawing.php'))
      {
      return __('Shipping Zones by Drawing Premium can not be activated when the free version is active.', SZBD::TEXT_DOMAIN);
      die();
      }
    // If this is free version
    /*    else if ( is_plugin_active( 'shipping-zones-by-drawing-premium/shipping-zones-by-drawing.php') ) {

    return __( 'Shipping Zones by Drawing can not be activated when the premuim version is active.', SZBD::TEXT_DOMAIN );

    die();

    }*/
    return false;
    }
  // Checks if environment is ok
  public function check_environment()
    {
    $environment_warning = self::get_environment_warning();
    if ($environment_warning && is_plugin_active(plugin_basename(__FILE__)))
      {
      $this->add_admin_notice('bad_environment', 'error', $environment_warning);
      deactivate_plugins(plugin_basename(__FILE__));
      }
    }
     public function add_admin_notice( $slug, $class, $message )
        {
            $this->notices[ $slug ] = array(
                 'class' => $class,
                'message' => $message
            );
        }
        public function admin_notices()
        {
            foreach ( (array) $this->notices as $notice_key => $notice ) {
                echo "<div class='" . esc_attr( $notice[ 'class' ] ) . "'><p>";
                echo wp_kses( $notice[ 'message' ], array(
                     'a' => array(
                         'href' => array ()
                    )
                ) );
                echo '</p></div>';
            }
            unset( $notice_key );
        }

    function posts_columns_id($defaults){

    $defaults['szbd_post_id'] = __('ID');
    return $defaults;
    }

    function posts_custom_id_columns($column_name, $id){
    if($column_name === 'szbd_post_id'){
            echo $id;
    }
}
  public function init()
    {
    // check if environment is ok
    if (self::get_environment_warning())
      {
      return;
      }
       $this->includes();
    }
  }
}
$GLOBALS['szbd_item'] = SZBD::instance();
