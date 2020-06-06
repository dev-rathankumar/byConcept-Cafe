<?php

if (!class_exists('ESIG_WPFORM_SETTING')):

    class ESIG_WPFORM_SETTING {

        const ESIG_WPFORM_COOKIE = 'esig-wpform-redirect';
        const WPF_COOKIE = 'esig-caldera-temp-data';
        const WPF_FORM_ID_META = 'esig_wp_form_id';
        const WPF_ENTRY_ID_META = 'esig_wp_entry_id';

        public static function is_wpf_requested_agreement($document_id) {
            $wpf_form_id = WP_E_Sig()->meta->get($document_id, self::WPF_FORM_ID_META);
            $wpf_entry_id = WP_E_Sig()->meta->get($document_id, self::WPF_ENTRY_ID_META);
            if ($wpf_form_id && $wpf_entry_id) {
                return true;
            }
            return false;
        }

        public static function is_wpf_esign_required() {
            if (self::get_temp_settings()) {
                return true;
            } else {
                return false;
            }
        }

        public static function get_temp_settings() {
            if (ESIG_COOKIE(self::WPF_COOKIE)) {
                return json_decode(stripslashes(ESIG_COOKIE(self::WPF_COOKIE)), true);
            }
            return false;
        }

        public static function save_esig_wpf_meta($meta_key, $meta_index, $meta_value) {

            $temp_settings = self::get_temp_settings();
            if (!$temp_settings) {
                $temp_settings = array();
                $temp_settings[$meta_key] = array($meta_index => $meta_value);
                // finally save slv settings . 
                self::save_temp_settings($temp_settings);
            } else {

                if (array_key_exists($meta_key, $temp_settings)) {
                    $temp_settings[$meta_key][$meta_index] = $meta_value;
                    self::save_temp_settings($temp_settings);
                } else {
                    $temp_settings[$meta_key] = array($meta_index => $meta_value);
                    self::save_temp_settings($temp_settings);
                }
            }
        }

        public static function save_temp_settings($value) {
            $json = json_encode($value);
            esig_setcookie(self::CF_COOKIE, $json, 600);
            // for instant cookie load. 
            $_COOKIE[self::CF_COOKIE] = $json;
        }

        public static function save_invite_url($invite_hash, $document_checksum) {
            $invite_url = WP_E_Invite::get_invite_url($invite_hash, $document_checksum);
            esig_setcookie(self::ESIG_WPFORM_COOKIE, $invite_url, 600);
            $_COOKIE[self::ESIG_WPFORM_COOKIE] = $invite_url;
        }

        public static function get_invite_url() {
            return esigget(self::ESIG_WPFORM_COOKIE, $_COOKIE);
        }

        public static function remove_invite_url() {
            setcookie(self::ESIG_WPFORM_COOKIE, null, time() - YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);
        }

        public static function save_submission_value($document_id, $fields) {
            WP_E_Sig()->meta->add($document_id, "esig_wpform_submission_value", json_encode($fields));
        }

        public static function field_type($formId, $fieldId) {
            $fields = wpforms()->form->get_field($formId, $fieldId);
            return $fields['type'];
        }

        public static function getField($formId, $fieldId) {
            return wpforms()->form->get_field($formId, $fieldId);
        }

        public static function field_label($formId, $fieldId) {
            $fields = wpforms()->form->get_field($formId, $fieldId);
            return esigget("label", $fields);
        }

        public static function get_checkbox($value, $formId, $fieldId, $option) {

            $html = '';


            if (!is_array($value)) {
                return $value;
            }

            if ($option == "check") {
                $items = '';
                foreach ($value as $key => $item) {
                    if ($item) {
                        $items .= '<li><span style="font-size:16px;">&#10003;</span>' . $item . '</li>';
                    }
                }
                return "<ul class='esig-checkbox-tick'>$items</ul>";
            }


            foreach ($value as $key => $val) {
                $html .= '<input type="checkbox" disabled readonly checked="checked"> ' . $val . "<br>";
            }
            return $html;
        }

        public static function get_email($value) {
            $html = '';

            if (is_array($value)) {
                $html .= ' <a href="mailto:' . $value['primary'] . '" target="_blank">' . $value['primary'] . '</a>';
            } else {
                $html .= ' <a href="mailto:' . $value . '" target="_blank">' . $value . '</a>';
            }
            return $html;
        }

        public static function get_html($formId, $fieldId) {
            $Form_Handler = new WPForms_Form_Handler();
            $get_field = $Form_Handler->get_field($formId, $fieldId);
            $html = esigStripTags($get_field['code'], 'body');
            return $html;
        }

        public static function get_address($formId, $fieldId, $documentId, $value, $display, $fieldLabel) {
            $wpform_value = json_decode(WP_E_Sig()->meta->get($documentId, "esig_wpform_submission_value"), true);
            $address = false;
            if (array_key_exists($fieldId, $wpform_value)) {
                $address = $wpform_value[$fieldId];
            }
            $address1 = esigget('address1', $address);
            $address2 = esigget('address2', $address);
            $city = esigget('city', $address);
            $state = esigget('state', $address);


            $listOfCountries = wpforms_countries();

            if (!empty($state)) {
                $listOfStates = wpforms_us_states();
                $state = esigget($state, $listOfStates);
            }

            $region = esigget('region', $address);
            $postal = esigget('postal', $address);
            $country = esigget('country', $address);

            if (!empty($country)) {
                $listOfCountries = wpforms_countries();
                $country = esigget($country, $listOfStates);
            }

            $value = '';
            $value .= !empty($address1) ? "$address1\n" : '';
            $value .= !empty($address2) ? "$address2\n" : '';
            if (!empty($city) && !empty($state)) {
                $value .= "$city, $state\n";
            } elseif (!empty($state)) {
                $value .= "$state\n";
            } elseif (!empty($city)) {
                $value .= "$city\n";
            }
            $value .= !empty($postal) ? "$postal\n" : '';
            $value .= !empty($country) ? "$country\n" : '';
            $value = implode("\n", array_map('sanitize_text_field', explode("\n", $value)));

            if ($display == "value") {
                return $value;
            }
            if ($display == "label_value") {
                return $fieldLabel . ":\n" . $value;
            }
            return $value;
        }

        public static function returnValue($display, $label, $value) {
            if ($display == "label") {
                return $label;
            } elseif ($display == "label_value") {
                return $label . ": " . $value;
            } else {
                return $value;
            }
        }

        public static function generate_value($documentId, $formId, $fieldId, $display = "value", $option) {

            $fieldType = self::field_type($formId, $fieldId);
            $fieldLabel = self::field_label($formId, $fieldId);

            if ($display == "label") {
                return $fieldLabel;
            }

            $value = self::get_submission_value($documentId, $formId, $fieldId, $fieldType, $display);

            switch ($fieldType) {
                case 'checkbox':
                    $result = self::get_checkbox($value, $formId, $fieldId, $option);
                    return self::returnValue($display, $fieldLabel, $result);
                    break;
                case 'email':
                    $result = self::get_email($value);
                    return self::returnValue($display, $fieldLabel, $result);
                    break;
                case 'url':
                    $result = '<a href="' . $value . '" target="_blank">' . $value . '</a>';
                    return self::returnValue($display, $fieldLabel, $result);
                    break;
                case 'file-upload':
                    $file = basename($value);
                    $result = '<a href="' . $value . '" target="_blank">' . $file . '</a>';
                    return self::returnValue($display, $fieldLabel, $result);
                    break;
                case 'html':
                    return self::get_html($formId, $fieldId);
                    break;
                case 'address':
                    return self::get_address($formId, $fieldId, $documentId, $value, $display, $fieldLabel);
                    break;
                default :
                    if ($display == "value") {
                        return $value;
                    } elseif ($display == "label_value") {
                        return $fieldLabel . ": " . $value;
                    }
            }
        }

        public static function get_submission_value($document_id, $form_id, $field_id, $fieldType, $display) {
            $wpform_value = json_decode(WP_E_Sig()->meta->get($document_id, "esig_wpform_submission_value"), true);
            if (array_key_exists($field_id, $wpform_value)) {
                if ($fieldType == "checkbox" || $fieldType == "radio" || $fieldType == "select") {
                    if ($display == "label") {
                        return $wpform_value[$field_id]['value'];
                    } else {
                        return $wpform_value[$field_id]['value_raw'];
                    }
                } else {
                    return $wpform_value[$field_id]['value'];
                }
            }
        }

        public static function get_wpform_settings($form_id) {

            $settings = get_post_meta($form_id, 'esig-wpform-settings', true);
            if (is_array($settings)) {
                return $settings;
            }
            return false;
        }

        public static function display_value($underline_data, $form_id, $wpform_value) {

            $result = '';
            if ($underline_data == "underline") {
                $result .= '<u>' . $wpform_value . '</u>';
            } else {
                $result .= $wpform_value;
            }
            return $result;
        }

    }

    

    

    

    

    

    

    

    

    

    

    

endif;