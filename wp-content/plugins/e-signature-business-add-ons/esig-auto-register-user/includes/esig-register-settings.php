<?php

if (!class_exists('Esig_Register_Settings')):

    class Esig_Register_Settings {

        const ACTION_TYPE = 'esig-registration-action';
        const REGISTRATION_GLOBAL_SETTINGS = 'esig-auto-reg-global';
        const REGISTRATION_EMAIL_TEMP_SETTING = 'esig-auto-reg-email-temp';
        const AUTO_REGISTER_SETTINGS = 'esig_auto_register_settings';

        public static function is_force_login() {
            $force_login = WP_E_Sig()->setting->get_generic('esig-force-login');
            if (!$force_login) {
                return true;
            } else {
                return false;
            }
        }

        public static function is_force_update_password() {
            $force_login = WP_E_Sig()->setting->get_generic('esig_force_password_updates');
            if ($force_login) {
                return true;
            } else {
                return false;
            }
        }

        public static function get_email_password($registration_action,$email_address, $wp_password) {
            if ($registration_action == "both") {

                if (self::wp_users_email_exists($email_address)) {
                    if (self::is_force_update_password()) {
                        return $wp_password;
                    }
                } else {
                    return $wp_password;
                }
            } else if ($registration_action == "create") {
                return $wp_password;
            } else if ($registration_action == "update") {
                if (self::is_force_update_password()) {
                    return $wp_password;
                }
            }
            
            return __("Use existing password","esig");
        }

        public static function save_registration_action_setting($value) {
            WP_E_Sig()->setting->set_generic(self::ACTION_TYPE, json_encode($value));
        }

        public static function get_registration_action_setting() {
            $settings = json_decode(WP_E_Sig()->setting->get_generic(self::ACTION_TYPE), true);
            if (is_array($settings)) {
                return $settings;
            }
            return false;
        }

        public static function get_registration_action() {

            $setings = self::get_registration_action_setting();
            if (!is_array($setings)) {
                return false;
            }
            if (in_array("create", $setings) && in_array("update", $setings)) {
                return "both";
            } elseif (in_array("create", $setings) && !in_array("update", $setings)) {
                return "create";
            } elseif (!in_array("create", $setings) && in_array("update", $setings)) {
                return "update";
            } else {
                return false;
            }
        }

        public static function wp_users_email_exists($user_email) {

            if (email_exists($user_email)) {
                return true;
            } else {
                return false;
            }
        }

        /* public static function wp_users_email_exists() {
          global $wpdb;
          $wp_users = $wpdb->get_results( "SELECT id, user_email FROM wp_users" );
          return $wp_users;
          } */

        public static function create_user($user_data) {

            $user_data = apply_filters("esig_user_register_data", $user_data);
            $user_id = wp_insert_user($user_data);
            return $user_id;
        }

        public static function get_current_wp_user_id($email_address) {

            $the_user = get_user_by('email', $email_address);
            return $the_user->ID;
        }

        public static function update_user($user_data, $email_address) {
            $data = self::remove_password_field_for_updates($user_data);
           
            // get user id by email address 
            $data['ID'] = self::get_current_wp_user_id($email_address);
            $update_data = apply_filters("esig_user_register_data", $data);
            $user_id = wp_update_user($update_data);
            return $user_id;
        }

        public static function remove_password_field_for_updates($data) {

            if (self::is_force_update_password()) {
                return $data;
            }
            
            if (array_key_exists('user_pass', $data)) {
                
                unset($data['user_pass']);
            }
           
            return $data;
        }

        public static function execute_action($registration_action, $user_data, $email_address) {

            if ($registration_action == "both") {

                if (self::wp_users_email_exists($email_address)) {
                    return self::update_user($user_data, $email_address);
                } else {
                    return self::create_user($user_data);
                }
            } else if ($registration_action == "create") {

                if (self::wp_users_email_exists($email_address)) {
                    return false;
                }
                return self::create_user($user_data);
            } else if ($registration_action == "update") {
                if (self::wp_users_email_exists($email_address)) {
                    return self::update_user($user_data, $email_address);
                }

                return false;
            } else {
                if (self::wp_users_email_exists($email_address)) {
                    return false;
                }
                return self::create_user($user_data);
            }
        }

        public static function esig_auto_login($user_id, $user_login, $user_pass) {

            if ($user_id < 1)
                return;

            wp_clear_auth_cookie();
            wp_set_auth_cookie($user_id);
            wp_set_current_user($user_id, $user_login);
            do_action('wp_login', $user_login, get_userdata($user_id));
        }

        public static function this_admin_can_create_user() {
            if (is_multisite()) {
                if (!current_user_can('create_users') && !current_user_can('promote_users')) {
                    return false;
                }
            } elseif (!current_user_can('create_users')) {
                return false;
            }

            return true;
        }

        public static function esig_get_user_roles($selected = '') {
            $p = '';
            $r = '';

            $editable_roles = array_reverse(get_editable_roles());

            foreach ($editable_roles as $role => $details) {
                $name = translate_user_role($details['name']);
                if ($selected == $role) // preselect specified role
                    $p = "\n\t<option selected='selected' value='" . esc_attr($role) . "'>$name</option>";
                else
                    $r .= "\n\t<option value='" . esc_attr($role) . "'>$name</option>";
            }
            return $p . $r;
        }

    }

 endif;