<?php
/**
 * Plugin Name: myCred Rest API
 * Description: myCred Rest API endpoints to provide access to thirdparty apps to interact with points data.
 * Author: myCred
 * Author URI: https://www.mycred.me
 * Version: 1.2.1
 */

if (!defined('ABSPATH'))
exit;

define('MYCRED_RESET_API_SLUG', 'mycred-rest');
define('MYCRED_RESET_API_VERSION', '1.2.1');
define('MYCRED_RESET_API_THIS', __FILE__);

add_action('admin_init', 'MCRA_check_plugin_dependencies');
add_action('mycred_init', 'MCRA_init');

function MCRA_init(){

    add_action('rest_api_init', 'MCRA_RegisterCustomRestRoutes');
    add_action('admin_enqueue_scripts', 'MCRA_LoadAdminAssets');

}

function MCRA_check_plugin_dependencies(){

    if (!is_plugin_active('mycred/mycred.php')) {

        add_action( 'admin_notices', 'MCRA_mycred_not_exist_notice' );
        deactivate_plugins( '/mycred-rest/mycred-rest.php' );

    } 

}

/* Show error notice if MyCred Plugin is not activated */

function MCRA_mycred_not_exist_notice() {

	$class = 'notice notice-error';
	$message =  ( 'Irks! MyCred Needs to be activated for MyCred Rest API to work' );
	printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ));

}

/* Loading scripts and styles */

function MCRA_LoadAdminAssets(){

    wp_enqueue_script( 'MCRA_SCRIPTS', plugins_url('assets/myCredRestScripts.js',__FILE__ ));

    $current_user = wp_get_current_user();
    $array_opts = array (
        'user' => $current_user->user_nicename,
        'pass' => $current_user->user_pass
    );
    wp_localize_script( 'MCRA_SCRIPTS', 'MCRA_CREDENTIALS', $array_opts );

}


/* Registering Custom Routes/Endpoints for REST API */

function MCRA_RegisterCustomRestRoutes(){

    $rest_api_settings = get_option('mycred_rest_api');
    $namespace = $rest_api_settings['api_url'];

    register_rest_route($namespace, 'points', array(

        'methods' => WP_REST_SERVER::CREATABLE,
        'callback' => 'MCRA_PointsCallbackHandling'

    ));

    register_rest_route($namespace, 'badges', array(

        'methods' => WP_REST_SERVER::CREATABLE,
        'callback' => 'MCRA_BadgesCallbackHandling'

    ));

    register_rest_route($namespace, 'ranks', array(

        'methods' => WP_REST_SERVER::CREATABLE,
        'callback' => 'MCRA_RanksCallbackHandling'

    ));

    register_rest_route($namespace, 'references', array(

        'methods' => WP_REST_SERVER::CREATABLE,
        'callback' => 'MCRA_ReferencesCallbackHandling'

    ));

}

/* myCred License System*/
require_once('license/license.php');

/* myCred Rest API Settings tab file*/
require_once('includes/mycred-rest-settings.php');

/* GetPointsByUserID, AddPointsByUserID and all other callback functions are written in this file */
require_once('includes/mycred-rest-endpoints-callbacks.php');