<?php

//set_site_transient( 'update_plugins', null );
if (!class_exists('Esig_AddOn_Updater')) :

    class Esig_AddOn_Updater {

        private $api_url = '';
        private $api_data = array();
        private $addon_id = '';
        private $name = '';
        private $itemname = '';
        private $slug = '';
        private $version = '';
        private $do_check = false;

        /**
         * Class constructor.
         *
         * @uses plugin_basename()
         * @uses hook()
         *
         * @param string $_api_url The URL pointing to the custom API endpoint.
         * @param string $_plugin_file Path to the plugin file.
         * @param array $_api_data Optional data to send with API calls.
         * @return void
         */
        function __construct($_item_name, $_addon_id, $_plugin_file, $_version) {
            $this->api_url = Esign_licenses::$approveme_url;
            $this->addon_id = $_addon_id;
            $this->itemname = $_item_name;
            $this->name = plugin_basename($_plugin_file);
            $this->slug = basename($_plugin_file, '.php');
            $this->version = $_version;
            // Set up hooks.
            $this->hook();
        }

        /**
         * Set up Wordpress filters to hook into WP's update process.
         *
         * @uses add_filter()
         *
         * @return void
         */
        private function hook() {

            add_filter('pre_set_site_transient_update_plugins', array($this, 'check_esig_update'));
            add_filter('plugins_api', array($this, 'plugins_api_filter'), 10, 3);

            // add_action( 'after_plugin_row_' . $this->name, array( $this, 'show_update_notification' ), 10, 2 );
        }

        /**
         * Check for Updates at the defined API endpoint and modify the update array.
         *
         * This function dives into the update api just when Wordpress creates its update array,
         * then adds a custom API call and injects the custom plugin data retrieved from the API.
         * It is reassembled from parts of the native Wordpress plugin update code.
         * See wp-includes/update.php line 121 for the original wp_update_plugins() function.
         *
         * @uses api_request()
         *
         * @param array $_transient_data Update array build by Wordpress.
         * @return array Modified update array with custom plugin data.
         */
        function check_esig_update($_transient_data) {


            if (!is_object($_transient_data)) {
                $_transient_data = new stdClass;
            }

            if (empty($_transient_data->response)) {
                return $_transient_data;
            }

            // print_r($_transient_data->response) . "<br>";


            if (!array_key_exists($this->name, $_transient_data->response)) {

                if (!get_site_transient('esig-update-' . $this->name)) {
                    $version_info = $this->api_request('plugin_latest_version', array('slug' => $this->slug));
                    set_site_transient('esig-update-' . $this->name, $version_info, 10 * HOUR_IN_SECONDS);
                } else {
                    $version_info = get_site_transient('esig-update-' . $this->name);
                }


                if (false !== $version_info && is_object($version_info) && isset($version_info->new_version)) {


                    if (version_compare($this->version, $version_info->new_version, '<')) {


                        $_transient_data->response[$this->name] = $version_info;
                    }

                    $_transient_data->last_checked = time();
                    $_transient_data->checked[$this->name] = $this->version;
                }
            }

            return $_transient_data;
        }

        /**
         * Updates information on the "View version x.x details" page with custom data.
         *
         * @uses api_request()
         *
         * @param mixed $_data
         * @param string $_action
         * @param object $_args
         * @return object $_data
         */
        function plugins_api_filter($_data, $_action = '', $_args = null) {
            if (( $_action != 'plugin_information' ) || !isset($_args->slug) || ( $_args->slug != $this->slug )) {
                return $_data;
            }

            $to_send = array('slug' => $this->slug);

            $api_response = $this->api_request('plugin_information', $to_send);
            if (false !== $api_response) {
                $_data = $api_response;
            }
            return $_data;
        }

        /**
         * Calls the API and, if successful, returns the object delivered by the API.
         *
         * @uses get_bloginfo()
         * @uses wp_remote_post()
         * @uses is_wp_error()
         *
         * @param string $_action The requested action.
         * @param array $_data Parameters for the API action.
         * @return false||object
         */
        private function api_request($_action, $_data) {

            global $wp_version;

            $data = $_data;

            if (!function_exists('WP_E_Sig'))
                return;

            $data['license_key'] = WP_E_Sig()->shortcode->setting->get('esig_wp_esignature_license_key');

            $data['license_type'] = WP_E_Sig()->shortcode->setting->get('esig_wp_esignature_license_type');

            if (isset($data['name']) && $data['name'] != $this->name)
                return;

            if (empty($data['license_key']))
                return;

            $api_params = array(
                'esig_action' => 'get_version',
                'license_key' => $data['license_key'],
                'license_type' => $data['license_type'],
                'addon_id' => $this->addon_id,
                'name' => $this->name,
                'itemname' => $this->itemname,
                'slug' => $this->slug,
                'url' => home_url()
            );

            $request = Esign_licenses::wpRemoteRequest(array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));



            if (!is_wp_error($request)) {
                $request = json_decode(wp_remote_retrieve_body($request));
            }

            if ($request && isset($request->sections)) {
                $request->sections = maybe_unserialize($request->sections);
            } else {
                $request = false;
            }

            return $request;
        }

    }

    

    

endif; // end class_exists check