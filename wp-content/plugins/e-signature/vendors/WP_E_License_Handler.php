<?php

/**
 * License handler for E-Signature
 *
 * This class should simplify the process of adding license information
 * to new ESIG extensions.
 *
 * @version 1.1
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

if (!class_exists('ESIG_License')) :

    /**
     * ESIG_License Class
     */
    class ESIG_License {

        private $file;
        private $license;
        private $item_name;
        private $item_id;
        private $item_shortname;
        private $version;
        private $author;
        private $api_url = 'https://www.approveme.com/';

        /**
         * Class constructor
         *
         * @global  array $edd_options
         * @param string  $_file
         * @param string  $_item_name
         * @param string  $_version
         * @param string  $_author
         * @param string  $_optname
         * @param string  $_api_url
         */
        function __construct($_file, $_item_name,$_item_id, $_version, $_author, $_optname = null, $_api_url = null) {

            $this->file = $_file;
            $this->item_name = $_item_name;
            $this->item_id = $_item_id;
            $this->item_shortname = 'esig_' . preg_replace('/[^a-zA-Z0-9_\s]/', '', str_replace(' ', '_', strtolower($this->item_name)));
            $this->version = $_version;
            $this->license = Esign_licenses::get_license_key();
            $this->author = $_author;
            $this->api_url = is_null($_api_url) ? $this->api_url : $_api_url;
            // Setup hooks
            $this->includes();
            $this->hooks();
            $this->auto_updater();
        }

        /**
         * Include the updater class
         *
         * @access  private
         * @return  void
         */
        private function includes() {

            if (!class_exists('ESIG_Plugin_Updater'))
                require_once 'WP_E_Plugin_Updater.php';
        }

        /**
         * Setup hooks
         *
         * @access  private
         * @return  void
         */
        private function hooks() {
            // Register settings
            add_filter('esig_settings_licenses', array($this, 'settings'), 1);

            // Activate license key on settings save
            add_action('admin_init', array($this, 'activate_license'));

            // Deactivate license key
            add_action('admin_init', array($this, 'deactivate_license'));

            add_action( "in_plugin_update_message-" . ESIGN_PLUGIN_BASENAME, array( $this, 'expired_notice' ), 10, 2 );
        }

        /**
         * Auto updater
         *
         * @access  private
         * @global  array $edd_options
         * @return  void
         */
        private function auto_updater() {

            // Setup the updater
            $esig_updater = new ESIG_Plugin_Updater(
                    $this->api_url, $this->file, array(
                    'version' => $this->version,
                    'license' => $this->license,
                    'item_name' => $this->item_name,
                    'item_id'=> $this->item_id,
                    'item_shortname' => $this->item_shortname,
                    'author' => $this->author
                    )
            );
        }

        /**
         * Add license field to settings
         *
         * @access  public
         * @param array   $settings
         * @return  array
         */
        public function settings($settings) {
            $esig_license_settings = array(
                array(
                    'id' => $this->item_shortname . '_license_key',
                    'name' => sprintf(__('%1$s License Key', 'esig'), $this->item_name),
                    'desc' => '',
                    'type' => 'license_key',
                    'options' => array('is_valid_license_option' => $this->item_shortname . '_license_active'),
                    'size' => 'regular'
                )
            );

            return array_merge($settings, $esig_license_settings);
        }

        /**
         * Activate the license key
         *
         * @access  public
         * @return  void
         */
        public function activate_license() {

            if (!function_exists('WP_E_Sig'))
                return;

            $esig = WP_E_Sig();
            //$esig_options = $esig->setting;

            if (!isset($_POST[$this->item_shortname . '_license_key_activate']))
                return;

            if (!isset($_POST[$this->item_shortname . '_license_key']))
                return;

            //if ('valid' == $esig->setting->get_generic($this->item_shortname . '_license_active'))
            // return;

            $license = sanitize_text_field($_POST[$this->item_shortname . '_license_key']);
            if ($license)
                $esig->setting->set_generic($this->item_shortname . '_license_key', $_POST[$this->item_shortname . '_license_key']);

            // Data to send to the API
            $api_params = array(
                'edd_action' => 'activate_license',
                'license' => $license,
                //'item_name' => urlencode($this->item_name),
                'item_id'=> $this->item_id,
                'url' => home_url()
            );

            $response = Esign_licenses::wpRemoteRequest(array('timeout' => 15,'body' => $api_params,'sslverify' => false));

            // Make sure there are no errors
            if (is_wp_error($response)) {
                error_log(__FILE__ . " WP E-Signature license activation error " . $response->get_error_code() . " : " . $response->get_error_message() );
                return;
            }


            // Decode license data
            $license_data = json_decode(wp_remote_retrieve_body($response));

            if ( $license_data->success && $license_data->license == "valid") {
                $esig->setting->set_generic($this->item_shortname . '_license_active', $license_data->license);
                $esig->setting->set_generic($this->item_shortname . '_license_key', $license);
                $esig->setting->set_generic($this->item_shortname . '_license_expires', $license_data->expires);
                $esig->setting->set_generic($this->item_shortname . '_customer_email', $license_data->customer_email);
                $esig->setting->set_generic($this->item_shortname . '_license_type', $license_data->license_type);
                $esig->setting->set_generic($this->item_shortname . '_license_name', $license_data->item_name);
                // deletes cache for license check
                $esig->setting->delete_generic('esig_license_info');
                WP_E_Sig()->notice->set('e-sign-alert notice notice-success', Esign_licenses::esig_super_admin() . ' Your license key has been Activated');
            } else {

                WP_E_Sig()->notice->set('e-sign-red-alert license', Esign_licenses::esig_super_admin() . ' It looks like the license you entered no longer exists.  Please <a href="https://www.approveme.com/support">contact support</a> or <a href="https://www.approveme.com/email-limited-pricing/">purchase a new license here</a>');
            }

            //add_option('esig_license_msg',$license_data->license) ;
        }

        /**
         * Deactivate the license key
         *
         * @access  public
         * @return  void
         */
        public function deactivate_license() {

            if (!function_exists('WP_E_Sig'))
                return;

            $esig = WP_E_Sig();



            if (!isset($_POST[$this->item_shortname . '_license_key']))
                return;



            // Run on deactivate button press
            if (isset($_POST[$this->item_shortname . '_license_key_deactivate'])) {


                // Data to send to the API
                $api_params = array(
                    'edd_action' => 'deactivate_license',
                    'license' => Esign_licenses::get_license_key(),
                    //'item_name' => urlencode($this->item_name),
                    'item_id'=> $this->item_id,
                    'url' => home_url()
                );

                // Call the API

                $response = Esign_licenses::wpRemoteRequest(array('timeout' => 15,'body' => $api_params,'sslverify' => false));

                // Make sure there are no errors
                if (is_wp_error($response)) {
                    error_log(__FILE__ . " WP E-Signature license Deactivation error " . $response->get_error_code() . " : " . $response->get_error_message() );
                    return;
                }

                // Decode the license data
                $license_data = json_decode(wp_remote_retrieve_body($response));


                if ($license_data->license == 'deactivated') {
                    $esig->setting->delete_generic($this->item_shortname . '_license_active');
                    $esig->setting->delete_generic($this->item_shortname . '_license_key');
                    $esig->setting->delete_generic($this->item_shortname . '_customer_email');
                    $esig->setting->delete_generic($this->item_shortname . '_license_type');
                    $esig->setting->delete_generic($this->item_shortname . '_license_name');
                    $esig->setting->delete_generic($this->item_shortname . '_license_expires');
                    $esig->setting->delete_generic('esig_license_info');

                    // add_option('esig_license_msg', $license_data->license);
                    WP_E_Sig()->notice->set('e-sign-alert notice notice-success', Esign_licenses::esig_super_admin() . ' Your license key has been Deactivated.');
                } else {
                    WP_E_Sig()->notice->set('e-sign-red-alert license', Esign_licenses::esig_super_admin() . ' It looks like the license you entered no longer active.  Please <a href="https://www.approveme.com/support">contact support</a>');
                }
            }
        }

        public function expired_notice( $plugin, $version_info ) {
            $license_data = Esign_licenses::check_license();
            if ( empty( $version_info->download_link ) && 'expired' === $license_data->license ) {
                ?>
                <span class="esig-expired-license-plugin-row-wrapper">
                    <span>
                        <span class="esig-expired-license-heading"><strong><?php _e( 'Update Unavailable!', 'esig' ); ?></strong></span>
                        <span class="esig-expired-license-message">
                            <?php
                                printf(
                                    __( 'Your license expired %s ago. To re-enable automatic updates <a href="%s" target="_blank">renew it now</a>.', 'esig' ),
                                    human_time_diff( strtotime( $license_data->expires ) ),
                                    $this->api_url . 'checkout/?download_id=2660&edd_license_key=' . Esign_licenses::get_license_key() . '&utm_campaign=admin&utm_source=plugin_row&utm_medium=plugins'
                                );
                            ?>
                        </span>
                    </span>
                </span>
                <?php
            }
        }

    }







endif; // end class_exists check


