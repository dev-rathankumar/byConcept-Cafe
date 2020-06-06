<?php

if (!class_exists('esig_personal_data_export')):

    class esig_personal_data_export {

        private static $instance;

        public static function instance() {
            if (!isset(self::$instance) && !( self::$instance instanceof esig_personal_data_export )) {
                self::$instance = new esig_personal_data_export;
            }
            return self::$instance;
        }

        public static function init() {
            add_filter('wp_privacy_personal_data_exporters', array(__CLASS__, 'esig_register_exporters'));
        }

        public static function esig_register_exporters($exporters) {
            $exporters[] = array(
                'exporter_friendly_name' => __('WP E-Signature'),
                'callback' => array(__CLASS__, 'esig_personal_data_exporter'),
            );
            return $exporters;
        }

        public static function data_response($export_data) {
            return array(
                'data' => $export_data,
                'done' => true,
            );
        }

        public static function signer_input_data($signature_id, $document_id) {

            $result = Esign_Query::_row(Esign_Query::$table_signer_fields_data, array("signature_id" => $signature_id, "document_id" => $document_id), array("%d", "%d"));


            $inputData = esigget("input_fields", $result);
            if (!$inputData) {
                return false;
            }

            $decrypt_fields = WP_E_Sig()->signature->decrypt("esig_sif", $result->input_fields);

            $fields = json_decode($decrypt_fields, true);
            $value = false;
            foreach ($fields as $key => $val) {
                if (!empty($val)) {
                    $value .= $val . ", ";
                }
            }
            return rtrim($value, ',');
        }

        public static function documentEvents($document_id, $emailAddress) {

            $results = Esign_Query::_results(Esign_Query::$table_documents_events, array("document_id" => $document_id), array("%d"));

            if (!$results) {
                return false;
            }

            $esigData = array();

            foreach ($results as $event) {

                $pos = strpos($event->event_data, $emailAddress);
                if ($pos === false) {
                    continue;
                }

                /* if($event->event == "document_sent"){
                  continue;
                  } */

                //if($)

                /* $esigData[] = array(
                  'name' => 'Event Data',
                  'value' => "Document viewed " . WP_E_Sig()->document->esig_date_format($event->date) . " " . date(get_option('time_format'), strtotime($event->date)) . " - " . self::extractIpAddress($event->event_data, $event->ip_address),
                  ); */

                $esigData[] = array(
                    'name' => 'Event Data',
                    'value' => $event->event_data . " " . WP_E_Sig()->document->esig_date_format($event->date) . "  " . date(get_option('time_format'), strtotime($event->date)),
                );
            }
            return $esigData;
        }

        public static function extractIpAddress($eventData, $ipAddress) {
            if ($ipAddress) {
                return $ipAddress;
            }
            $pieces = explode('IP', $eventData);
            return array_pop($pieces);
        }

        public static function esig_personal_data_exporter($email_address, $page = 1) {

            $export_data = array();

            $esig_user = WP_E_Sig()->user->getUserBy("user_email", $email_address);

            if (!$esig_user) {

                return self::data_response($export_data);
            }

            $esign_user_id = $esig_user->user_id;

            $signers_data = WP_E_Sig()->signer->all_signer_documents($esign_user_id);

            if ($signers_data) {

                $group_label = __('E-Signature Documents', 'esig');
                $group_id = 'esig';


                $item_id = "esig-";
                foreach ($signers_data as $signer) {

                    $esig_data = array();
                    $item_id = $item_id . $signer->id;

                    $document = WP_E_Sig()->document->getDocument($signer->document_id);

                    if (empty($document)) {
                        continue;
                    }

                    $esig_data[] = array(
                        'name' => 'Signer Name',
                        'value' => $signer->signer_name,
                    );

                    $esig_data[] = array(
                        'name' => 'Document Name',
                        'value' => $document->document_title,
                    );

                    $esig_data[] = array(
                        'name' => 'Party ID',
                        'value' => $esig_user->uuid,
                    );

                    $esig_data[] = array(
                        'name' => 'E-mail Address',
                        'value' => $signer->signer_email,
                    );

                    if (WP_E_Sig()->signature->userHasSignedDocument($esign_user_id, $signer->document_id)) {

                        $esig_data[] = array(
                            'name' => 'Signed Date',
                            'value' => WP_E_Sig()->document->esig_date_format(WP_E_Sig()->signature->GetSignatureDate($esign_user_id, $signer->document_id), $signer->document_id),
                        );

                        $esig_data[] = array(
                            'name' => 'Completed Date',
                            'value' => WP_E_Sig()->document->esig_date_format(WP_E_Sig()->document->getSignedresult_eventdate($signer->document_id), $signer->document_id),
                        );

                        $signature_id = WP_E_Sig()->signature->GetSignatureId($esign_user_id, $signer->document_id);

                        $inputData = self::signer_input_data($signature_id, $signer->document_id);
                        if ($inputData) {
                            $esig_data[] = array(
                                'name' => 'Signer Input Data',
                                'value' => $inputData,
                            );
                        }

                        $esig_data[] = array(
                            'name' => 'Ip Address',
                            'value' => WP_E_Sig()->signature->signatureIpAddress($esign_user_id, $signer->document_id),
                        );
                    }

                  

                    $viwed_data = self::documentEvents($signer->document_id, $email_address);
                    if (is_array($viwed_data)) {
                        $esig_data = array_merge($esig_data, $viwed_data);
                    }

                    $export_data[] = array(
                        'group_id' => $group_id,
                        'group_label' => $group_label,
                        'item_id' => $item_id,
                        'data' => $esig_data,
                    );
                }


                // update_option("rupom", serialize($esig_data));
            }

            return self::data_response($export_data);
        }

      

    }

    endif;

esig_personal_data_export::instance()->init();
