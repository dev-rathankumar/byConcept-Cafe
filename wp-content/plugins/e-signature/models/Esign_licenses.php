<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Esign_licenses extends WP_E_Model {

    public static $approveme_url     = "http://www.approveme.com/";
    public static $approveme_item_id = '2660';

    public function __construct() {
        parent::__construct();
    }

    public static function wpRemoteRequest($params, $url = false) {

        if (!$url) {
            $url = self::$approveme_url;
        }

        $https = $http = $url;

        if (wp_http_supports(array('ssl'))) {
            $https = set_url_scheme($https, 'https');
        }

        $response = wp_remote_post($https, $params);

        if (is_wp_error($response)) {
            error_log(__FILE__ . " WP E-Signature license activation error " . $response->get_error_code() . " : " . $response->get_error_message());
            $response = wp_remote_post($http, $params);
        }

        if (is_wp_error($response)) {
            error_log(__FILE__ . " WP E-Signature license activation error " . $response->get_error_code() . " : " . $response->get_error_message());
        }

        return $response;
    }

    public static function is_license_active() {

        $result = self::check_license();

        if (!is_object($result)) {
            return false;
        }

        if ($result->license == "valid" || $result->license == 'inactive') { // Inactive means it's a valid license, just not on any sites
            return true;
        } else {
            return false;
        }
    }

    public static function is_valid_license() {
        if (WP_E_Sig()->setting->get_generic("esig_wp_esignature_license_active") == "valid" && self::get_license_type() == "business-license") {
            return true;
        } elseif (WP_E_Sig()->setting->get_generic("esig_wp_esignature_license_active") == "valid" && self::get_license_type() == "Business License") {
            return true;
        }
        return false;
    }

    public static function is_license_valid() {
		return true;
        if (WP_E_Sig()->setting->get_generic("esig_wp_esignature_license_active") == "valid") {
            return true;
        }
        return false;
    }

    public static function is_business_license() {

        if (self::get_license_type() == 'business-license') {
            return true;
        } elseif (self::get_license_type() == "Business License") {

            return true;
        } else {
            return false;
        }
    }

    public static function get_site_url() {
        return site_url('', 'http');
    }

    public static function get_license_key() {
        return WP_E_Sig()->setting->get_generic("esig_wp_esignature_license_key");
    }

    public static function get_license_name() {
        return WP_E_Sig()->setting->get_generic("esig_wp_esignature_license_name");
    }

    public static function get_license_type() {
        return WP_E_Sig()->setting->get_generic("esig_wp_esignature_license_type");
    }

    public static function get_expire_date() {
        return WP_E_Sig()->setting->get_generic("esig_wp_esignature_license_expires");
    }

    public static function check_license() {
        global $esign_licenses_check_license;

        // Allows us to only make one HTTP request per page load.
        if (!is_null($esign_licenses_check_license)) {
            return $esign_licenses_check_license;
        }

        $cached_data = json_decode(WP_E_Sig()->setting->get_generic('esig_license_info'));
        if (!empty($cached_data) && current_time('timestamp') < $cached_data->timeout) {
            $esign_licenses_check_license = $cached_data->license_info;
        } else {
            $api_params = array(
                'edd_action' => 'check_license',
                'item_id' => self::$approveme_item_id,
                'item_name' => self::get_license_name(),
                'license' => self::get_license_key(),
                'url' => self::get_site_url(),
            );

            $request = self::wpRemoteRequest(array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));

            if (!is_wp_error($request)) {

                $esign_licenses_check_license = $esign_licenses_check_license = json_decode(wp_remote_retrieve_body($request));

                // Account for a non-entered license key
                if ( is_null( $esign_licenses_check_license ) ) {
                    return false;
                }

                $cache_data = array(
                    'license_info' => $esign_licenses_check_license,
                    'timeout' => current_time('timestamp') + DAY_IN_SECONDS, // Let's just check license info once a day to save the server
                );

                WP_E_Sig()->setting->set_generic('esig_license_info', json_encode($cache_data));

                $allowed_status = array('active', 'valid');
                $esig_status = in_array($esign_licenses_check_license->license, $allowed_status) ? 'valid' : 'invalid';
                WP_E_Sig()->setting->set_generic("esig_wp_esignature_license_active", $esig_status);
            } else {
                $esign_licenses_check_license = false;
            }
        }

        return $esign_licenses_check_license;
    }

    public static function invalidLicense() {
        $license_info = self::check_license();
        $invalid_status = array('revoked', 'expired');
        if (in_array($license_info->license, $invalid_status)) {
            return true;
        }
        return false;
    }

    public static function latestVersion() {

        if ($esig_license_check = wp_cache_get('esig_latest_version_info', 'esig_latest_version_info')) {
            return $esig_license_check;
        }

        $api_params = array(
            'edd_action' => 'get_version',
            'item_id' => self::$approveme_item_id,
            'item_name' => self::get_license_name(),
            'license' => self::get_license_key(),
            'url' => self::get_site_url(),
        );

        $request = self::wpRemoteRequest(array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));

        if (!is_wp_error($request)) {
            $request = json_decode(wp_remote_retrieve_body($request));
            // setting cache
            wp_cache_set('esig_latest_version_info', $request, 'esig_latest_version_info');
        } else {
            return false;
        }

        return $request;
    }

    public static function esig_super_admin($echo = false) {
        $super_admin_id = WP_E_Sig()->user->esig_get_super_admin_id();
        $user_details = WP_E_Sig()->user->getUserByWPID($super_admin_id);
        if ($echo)
            echo $user_details->first_name;

        return $user_details->first_name;
    }

    public static function get_strip_license_key($status) {

        $valid_status = array('valid', 'active');

        $license_key  = self::get_license_key();
        if ( in_array( $status, $valid_status ) && ! empty( $license_key ) ) {
            return str_repeat( '*', ( strlen( $license_key ) - 4 ) ) . substr( $license_key, -4, 4 );

        } else {
            return false;
        }
    }

    public static function is_readonly($status) {
        if ($status == "valid") {
            return "readonly";
        } else {
            return false;
        }
    }

    public static function get_renew_button() {

        return '<a href="' . self::get_renewal_link() . '" class="esig-btn-pro esig-renew-button" target="_blank">Renew Your License</a>';
    }

    public static function get_renewal_link() {
        return self::$approveme_url . 'checkout/?edd_license_key=' . self::get_license_key() . '&download_id=' . self::$approveme_item_id;
    }

    public static function get_license_form($result) {
		$licenseKey = 'valid';
		$result->license = "valid";
        $item_plugshortname = str_replace("WP E-Signature ", "", $result->item_name);
        $item_pluginname = 'esig_' . preg_replace('/[^a-zA-Z0-9_\s]/', '', str_replace(' ', '_', strtolower($item_plugshortname)));

        $html = '';

        $button_text = '';
        $licenseKey = self::get_license_key();
        if ( ! empty( $licenseKey ) && ( $result->license == "valid" || $result->license == "inactive" || $result->license == "active" ) ) {

            $html .= '<tr>
        <th><label for="license_key" id="license_key_label"> License Status</label></th>
        <td> <span class="license-active-status"> ACTIVE </span> &nbsp;&nbsp;&nbsp;–&nbsp;&nbsp; You are receiving updates. </td><tr>';

            //$html .='<tr><td colspan="2"><span class="license-active-msg"> active msg</span> </td></tr>' ;
            $button_text = ' <input type="submit" class="button-appme button" name="esig_wp_esignature_license_key_deactivate" value="Deactivate License">';
        } elseif ($result->license == "expired") {

            $html .= '<tr><td colspan="2"><div class="esig-add-on-block esig-pro-pack open">

                <h3>URGENT: Your License has Expired!</h3>
                <p style="display:block;"> A valid WP E-Signature license is required for access to critical updates and support. Without a license you are putting you and your signers at risk. To protect yourself and your documents please update your license.</p>
                ' . self::get_renew_button() . '
            </div><div class="license-expired-msg"> <strong>Your are not receiving critical updates.  WP E-Signature license expired on ' . date(get_option('date_format'), strtotime($result->expires)) . '.</strong></div></td></tr>';

            $html .= '<tr>
        <th><label for="license_key" id="license_key_label"> License Status</label></th>
        <td> <span class="license-inactive-status">' . strtoupper($result->license) . '</span> - You are <strong>not</strong> receiving updates and your signers are currently at risk!</td><tr>';



            $button_text = ' <input type="submit" class="button-appme button" name="esig_wp_esignature_license_key_activate" value="Activate License">';
        } else {

            $html .= '<tr><td colspan="2"><div class="license-inactive-msg"> Please enter your valid <a href="https://www.approveme.com">WP E-Signature</a> license below.  If you forgot your license you can login to <a href="https://www.approveme.com/profile">your account here</a> or <a href="https://www.approveme.com/email-limited-pricing/">purchase a license</a> here.</div></td></tr>';

            $html .= '<tr>
        <th><label for="license_key" id="license_key_label"> License Status</label></th>
        <td> <span class="license-inactive-status">' . strtoupper($result->license) . '</span> - You are not receiving updates and your signers are currently at risk!</td><tr>';


            $button_text = ' <input type="submit" class="button-appme button" name="esig_wp_esignature_license_key_activate" value="Activate License">';
        }


        $html .= '<tr class="esig-settings-wrap">
        <th><label for="license_key" id="license_key_label"> License Key <span class="description"> (required)</span></label></th>
        <td><input type="text" name="esig_wp_esignature_license_key' . '" id="first_name" value="' . self::get_strip_license_key($result->license) . '" class="regular-text" ' . self::is_readonly($result->license) . ' /> ' . $button_text . '</tr>';

        if ($result->license == "valid") {
            if ($result->expires == 'lifetime') {
                $html .= __('<tr><td colspan="3">You are awesome! You have a forever license with no expiration.  </td></tr>', 'esign');
            } else {
                $html .= sprintf(__('<tr><td colspan="3">Your e-signature license will expire on %s </td></tr>', 'esign'), $result->expires);
            }
        }

        echo $html;
    }

}
