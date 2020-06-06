<?php
/*
Plugin Name: Live Chat by Customerly - Free Live Chat Suite
Description: The Live Chat to help your Customers. A Live Chat, Contact form and Email Sender for your Marketing Automations for your beautiful website or e-commerce.
Version: 1.8.2
Author: Customerly.io
Author URI: https://www.customerly.io/en/customer-support-live-chat-software?utm_medium=referral&utm_source=wordpress&utm_campaign=wordpressAuthorURI
*/


if ( ! defined('ABSPATH')){
    die();
}

class Customerly
{

    static function create_leads($email, $name = "", $data){
        $ch = curl_init();

        $attributes = '';

        foreach ($data as $param_name => $param_val) {
            $param_val = str_replace('"', "'", $param_val);
            $attributes.="\"$param_name\":\"$param_val\",";
        }
        $attributes = substr($attributes, 0, strlen($attributes)-1);

        $user = "{\"leads\":[{\"email\":\"".$email."\",\"name\":\"".$name."\",\"attributes\":{ $attributes }}]}";


        curl_setopt($ch, CURLOPT_URL, "https://api.customerly.io/v1/leads");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_POST, TRUE);

        curl_setopt($ch, CURLOPT_POSTFIELDS,$user );


        $options = get_option('customerly_settings');
        $api_key = $options['customerly_text_field_appkey'];


        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authentication: AccessToken: $api_key"
        ));

        $response = curl_exec($ch);
        curl_close($ch);
        return $response ;
    }
    static function create_users(){
        $ch = curl_init();


        $attributes = '';

        foreach ($_POST as $param_name => $param_val) {
            $param_val = str_replace('"', "'", $param_val);
            $attributes.="\"$param_name\":\"$param_val\",";
        }
        $attributes = substr($attributes, 0, strlen($attributes)-1);

        $user = "{\"users\":[{\"email\":\"".$_POST['email']."\",\"name\":\"".$_POST['name']."\",\"attributes\":{ $attributes }}]}";

        curl_setopt($ch, CURLOPT_URL, "https://api.customerly.io/v1/users");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_POST, TRUE);

        curl_setopt($ch, CURLOPT_POSTFIELDS,$user );


        $options = get_option('customerly_settings');
        $api_key = $options['customerly_text_field_appkey'];


        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authentication: AccessToken: $api_key"
        ));

        $response = curl_exec($ch);
        curl_close($ch);
        return $response ;
    }
}



add_action('wp_enqueue_scripts', 'customerly_output_widget');
add_action('admin_menu', 'customerly_add_admin_menu');
add_action('admin_init', 'customerly_settings_init');
add_action('activated_plugin', 'customerly_activation');


/*
 * Function that redirect people on Customerly Admin when activated
 */
function customerly_activation($plugin)
{
    if ($plugin == plugin_basename(__FILE__)) {
        exit(wp_redirect(admin_url('admin.php?page=Customerly&utm_source=wordpress&utm_campaign=afterinstallredirect')));
    }
}

/*
 * Function that add a link in the description of the plugin list
 */
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'plugin_add_settings_link');

function plugin_add_settings_link($links)
{
    $settings_link = '<a href="admin.php?page=Customerly&utm_source=wordpress&utm_campaign=pluginlisthowto"> How to go live?</a>';
    array_unshift($links, $settings_link);
    return $links;
}


/*
 * Function that add warning error notice when is not configured
 */

global $pagenow;

if (!customerly_is_configured() &&
    (isset($_GET['page']) && $_GET['page'] != "Customerly")
    || (!isset($_GET['page']) && !customerly_is_configured()) ) {
    add_action('admin_notices', 'sample_admin_notice__error');

}
/*
 * Function that check if customerly has been configured with an appid
 */
function customerly_is_configured()
{

    $options = get_option('customerly_settings');

    //IF is not configured return false

    if (!isset($options['customerly_text_field_appid']) || strlen($options['customerly_text_field_appid']) < 8) {
        if (isset($_GET['appid'])) {
            return true;
        }
        return false;
    }
    return true;

}

function sample_admin_notice__error()
{
    include_once("warning.php");
}


/*
 * Returns a url to connfigure customerly with a Redirect URL
 */
function customerly_setup_url()
{

    $current_user = wp_get_current_user();
    $blogName = get_bloginfo('name');
    $email = $current_user->user_email;
    $redirectUrl = urlencode(admin_url() . "admin.php?page=Customerly&appid={{appid}}");
    return "https://app.customerly.io/registration/register?email=$email&title=$blogName&page=support&redirect=$redirectUrl";
}


/*
 * Function that Render the actual widget in all the web pages
 */
function customerly_output_widget()
{
    global $user_ID;
    $options = get_option('customerly_settings');
    $appid = isset($options['customerly_text_field_appid']) ? $options['customerly_text_field_appid'] : "";
    $trackOP = false;

    if (isset($options['customerly_checkbox_optimizepress'])) {
        $trackOP = $options['customerly_checkbox_optimizepress'];
    }


    $current_user = wp_get_current_user();

    $username = $current_user->user_login;
    $email = $current_user->user_email;
    $name = $current_user->display_name;


    print('</script><script type="text/javascript">
	 	 !function(){function e(){var e=t.createElement("script");e.type="text/javascript",e.async=!0,e.src="https://widget.customerly.io/widget/' . $appid . '";var r=t.getElementsByTagName("script")[0];r.parentNode.insertBefore(e,r)}var r=window,t=document,n=function(){n.c(arguments)};r.customerly_queue=[],n.c=function(e){r.customerly_queue.push(e)},r.customerly=n,r.attachEvent?r.attachEvent("onload",e):r.addEventListener("load",e,!1)}();
				</script>');


    if ('' == $user_ID) {
        //no user logged in
        print('<script type="text/javascript">
			         			window.customerlySettings = {
									    app_id: "' . $appid . '"
								  };
			   </script>');
    } else {

        $OPData = '';

        if ($trackOP) {
            $OPData = 'op_logged_as_member: ' . OPTIMIZEMEMBER_CURRENT_USER_IS_LOGGED_IN_AS_MEMBER . ',
					   op_current_access_level: ' . OPTIMIZEMEMBER_CURRENT_USER_ACCESS_LEVEL . ",";

        }
        print('<script type="text/javascript">
	     			window.customerlySettings = {
									    app_id: "' . $appid . '",
									    user_id: "' . $user_ID . '",
										name: "' . $name . '",
									    email: "' . $email . '",
									    custom_data: {
										    ' . $OPData . '
										    username: "' . $username . '"
										}
								  };
			   </script>');
    }
}


//Function that add Customerly Menu on the left sidebar
// Will add a notification if is not setup yet
function customerly_add_admin_menu()
{
    add_menu_page('Customerly',
        customerly_is_configured() ? 'Customerly Live Chat' : 'Live Chat <span class="awaiting-mod">1</span>',
        'manage_options',
        'Customerly',
        'customerly_options_page',
        plugins_url('assets/img/blue_fill_notification.svg', __FILE__),
        3);

    if (customerly_is_configured()){
        add_submenu_page('Customerly', 'Live Chat PRO Features', '<div class="dashicons dashicons-star-filled"></div> PRO Features', 'manage_options', 'profeatures', 'customerly_pro');
        add_submenu_page('Customerly', 'Live Chat Mobile App', '<div class="dashicons dashicons-smartphone"></div> Download App', 'manage_options', 'mobileapp', 'customerly_download_app');
        add_submenu_page('Customerly', 'Live Chat Integrations', '<div class="dashicons dashicons-buddicons-pm"></div> Integrations', 'manage_options', 'integrations', 'cutomerly_integrations');
    }
    global $menu;

}

function customerly_download_app()
{
    include_once("mobile.php");
}

function customerly_pro()
{
    include_once("profeatures.php");
}

function cutomerly_integrations()
{
    include_once("integrations.php");
}


/*
 * Plugin Settings Form Render
 *
 *
 *
 */
function customerly_settings_init()
{


    register_setting('pluginPage', 'customerly_settings');

    if (is_admin()) {
        // for Admin Dashboard Only
        // Embed the Script on our Plugin's Option Page Only
        if (isset($_GET['page']) && $_GET['page'] == 'Customerly') {
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-form');
        }
    }


    add_settings_field(
        'customerly_text_field_appid',
        __('Application ID', 'customerly.io'),
        'customerly_text_field_appid_render',
        'pluginPage',
        'customerly_pluginPage_section'
    );


    add_settings_field(
        'customerly_text_field_appkey',
        __('Application Access Token', 'customerly.io'),
        'customerly_text_field_appkey_render',
        'pluginPage',
        'customerly_pluginPage_section'
    );


}

function customerly_text_field_appid_render()
{
    $options = get_option('customerly_settings');
    $appid = "";
    if (isset($_GET['appid'])) {
        $appid = $_GET['appid'];
    } else {
        if (isset($options['customerly_text_field_appid'])) {
            $appid = $options['customerly_text_field_appid'];
        }
    }

    ?>
    <input id="appID" type='text' name='customerly_settings[customerly_text_field_appid]' style="display: none"
           value='<?php echo $appid; ?>'>

    <?php
}

function customerly_text_field_session_token_render()
{
    $options = get_option('customerly_settings');
    $token = "";
    if (isset($options['customerly_text_field_session_token'])) {
        $token = $options['customerly_text_field_session_token'];
    }
    ?>
    <input id="sessionToken" type='hidden'
           name='customerly_settings[customerly_text_field_session_token]'
           value='<?php echo $token; ?>'>

    <?php
}


function customerly_text_field_appkey_render()
{
    $options = get_option('customerly_settings');
    $appkey = "";
    if (isset($_GET['appkey'])) {
        $appkey = $_GET['appkey'];
    } else {
        if (isset($options['customerly_text_field_appkey'])) {
            $appkey = $options['customerly_text_field_appkey'];
        }
    }
    ?>
    <input class="integration-field" id="appkey" type='text'
           name='customerly_settings[customerly_text_field_appkey]'
           value='<?php echo $appkey; ?>'>

    <?php
}


function customerly_options_page()
{
    include_once("headers.php");
    ?>


    <form id="customerlySettings" action='options.php' method='post' style="display: none">


        <?php

        settings_fields('pluginPage');
        do_settings_sections('pluginPage');

        customerly_text_field_session_token_render();
        customerly_text_field_appid_render();
        customerly_text_field_appkey_render();
        ?>

    </form>

    <?php

    if (customerly_is_configured()) {
        include_once("configured.php");
    } else {
        include_once("welcome.php");
    }
    ?>
    <?php
}

?>
