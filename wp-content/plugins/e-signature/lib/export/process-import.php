<?php

if (!class_exists('esigImportProcess')):

    class esigImportProcess {

        private static $instance;

        public static function instance() {
            if (!isset(self::$instance) && !( self::$instance instanceof esigImportProcess )) {
                self::$instance = new esigImportProcess;
            }
            return self::$instance;
        }

        /**
         *  process documents table import data 
         *  @since 1.5.1
         */
        public function process_documents($table, $records) {

            if (!esig_is_func_disabled('set_time_limit') && !ini_get('safe_mode')) {
                set_time_limit(0);
            }

            foreach ($records as $key => $record) {

                if ($key && $key == 'nextQuery') {
                    continue;
                }
                if($key && $key == 'totalRecord'){
                    continue;   
                }

                if (is_array($record)) {
                    $data = $record;
                } else {
                    $data = (array) $record;
                }
                $document_id = esigget('document_id', $data);
                $select = Esign_Query::_var($table, 'document_id', array("document_id" => $document_id), array('%d'));

                $exists = esigget("document_id", $select);
                if ($exists) {
                    unset($data['document_id']);
                    Esign_Query::_update($table, $data, array('document_id' => $document_id), array('%d', '%d', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s'), array('%d'));
                } else {
                    Esign_Query::_insert($table, $data, array('%d', '%d', '%d', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s'));
                }
            }
        }

        /**
         * Process settings table import data. 
         * @param type $table
         * @param type $records
         */
        public function process_settings($table, $records) {

            if (!esig_is_func_disabled('set_time_limit') && !ini_get('safe_mode')) {
                set_time_limit(0);
            }
            foreach ($records as $key => $record) {

               if ($key && $key == 'nextQuery') {
                    continue;
                }
                if($key && $key == 'totalRecord'){
                    continue;   
                }
                if (is_array($record)) {
                    $data = $record;
                } else {
                    $data = (array) $record;
                }
                $setting_id = esigget('setting_id', $data);
                $select = Esign_Query::_var($table, 'setting_id', array("setting_id" => $setting_id), array('%d'));

                $exists = esigget("setting_id", $select);
                if ($exists) {
                    unset($data['setting_id']);
                    Esign_Query::_update($table, $data, array('setting_id' => $setting_id), array('%d', '%s', '%s'), array('%d'));
                } else {
                    Esign_Query::_insert($table, $data, array('%d', '%d', '%s', '%s'));
                }
            }
        }

        /**
         * Process signatures table import data. 
         * @param type $table
         * @param type $records
         */
        public function process_signatures($table, $records) {

            if (!esig_is_func_disabled('set_time_limit') && !ini_get('safe_mode')) {
                set_time_limit(0);
            }

            foreach ($records as $key => $record) {

               if ($key && $key == 'nextQuery') {
                    continue;
                }
                if($key && $key == 'totalRecord'){
                    continue;   
                }
                
                if (is_array($record)) {
                    $data = $record;
                } else {
                    $data = (array) $record;
                }
                $signature_id = esigget('signature_id', $data);
                $select = Esign_Query::_var($table, 'signature_id', array("signature_id" => $signature_id), array('%d'));

                $exists = esigget("signature_id", $select);
                if ($exists) {
                    unset($data['signature_id']);
                    Esign_Query::_update($table, $data, array('signature_id' => $signature_id), array('%d', '%s', '%s', '%s', '%s', '%s'), array('%d'));
                } else {
                    Esign_Query::_insert($table, $data, array('%d', '%d', '%s', '%s', '%s', '%s', '%s'));
                }
            }
        }

        /**
         * Process document signature data import
         * @param type $table
         * @param type $records
         */
        public function process_documents_signatures($table, $records) {

            if (!esig_is_func_disabled('set_time_limit') && !ini_get('safe_mode')) {
                set_time_limit(0);
            }
            
            foreach ($records as $key => $record) {

                
                if ($key && $key == 'nextQuery') {
                    continue;
                }
                if($key && $key == 'totalRecord'){
                    continue;   
                }
                if (is_array($record)) {
                    $data = $record;
                } else {
                    $data = (array) $record;
                }
                $id = esigget('id', $data);
               
                $select = Esign_Query::_var($table, 'id', array("id" => $id), array('%d'));

                $exists = esigget("id", $select);
                $signer_type=false;
                if(array_key_exists('signer_type', $data))
                {
                    $signer_type=esigget('signer_type', $data);
                    if($signer_type !="admin_signature")
                    {
                        unset($data['signer_type']);
                    }
                    
                }
                if ($exists) {
                    unset($data['id']);
                    if($signer_type){
                        Esign_Query::_update($table, $data, array('id' => $id), array('%d', '%d', '%s', '%s'), array('%d'));
                    }
                    else {
                        Esign_Query::_update($table, $data, array('id' => $id), array('%d', '%d', '%s', '%s','%s'), array('%d'));
                    }
                    
                } else {
                  
                    if($signer_type){
                        Esign_Query::_insert($table, $data, array('%d', '%d', '%d', '%s', '%s','%s'));
                    }
                    else {
                       Esign_Query::_insert($table, $data, array('%d', '%d', '%d', '%s', '%s')); 
                    }
                     
                    
                }
            }
        }

        /**
         * Process document meta data import
         * @param type $table
         * @param type $records
         */
        public function process_documents_meta($table, $records) {

            if (!esig_is_func_disabled('set_time_limit') && !ini_get('safe_mode')) {
                set_time_limit(0);
            }
            foreach ($records as $key => $record) {

                if ($key && $key == 'nextQuery') {
                    continue;
                }
                if($key && $key == 'totalRecord'){
                    continue;   
                }

                if (is_array($record)) {
                    $data = $record;
                } else {
                    $data = (array) $record;
                }
                $id = esigget('id', $data);
                $select = Esign_Query::_var($table, 'id', array("id" => $id), array('%d'));

                $exists = esigget("id", $select);
                if ($exists) {
                    unset($data['id']);
                    Esign_Query::_update($table, $data, array('id' => $id), array('%d', '%s', '%s'), array('%d'));
                } else {
                    Esign_Query::_insert($table, $data, array('%d', '%d', '%s', '%s'));
                }
            }
        }

        public function parse_event_data($eventData) {

            if (strpos($eventData, "CC'd")) {
                return $eventData;
            }

            if (strpos($eventData, "'") !== FALSE) {
                list($firstPart, $secondPart) = explode("by", $eventData);
                $eventText = $firstPart . "by" . esc_html($secondPart);
                return $eventText;
            }
            return $eventData;
        }

        /**
         * process document_event data import from a export file. 
         * @param type $table
         * @param type $records
         */
        public function process_documents_events($table, $records) {

            if (!esig_is_func_disabled('set_time_limit') && !ini_get('safe_mode')) {
                set_time_limit(0);
            }
            foreach ($records as $key => $record) {

               if ($key && $key == 'nextQuery') {
                    continue;
                }
                if($key && $key == 'totalRecord'){
                    continue;   
                }
                
                if (is_array($record)) {
                    $data = $record;
                } else {
                    $data = (array) $record;
                }
                $id = esigget('id', $data);
                $select = Esign_Query::_var($table, 'id', array("id" => $id), array('%d'));
                $exists = esigget("id", $select);

                $data['event_data'] = $this->parse_event_data($data['event_data']);

                if ($exists) {
                    unset($data['id']);
                    Esign_Query::_update($table, $data, array('id' => $id), array('%d', '%s', '%s', '%s', '%s'), array('%d'));
                } else {
                    Esign_Query::_insert($table, $data, array('%d', '%d', '%s', '%s', '%s', '%s'));
                }
            }
        }

        /**
         * Process User/recepeint data import from export xml flie.  
         * @param type $table
         * @param type $records
         */
        public function process_users($table, $records) {

            if (!esig_is_func_disabled('set_time_limit') && !ini_get('safe_mode')) {
                set_time_limit(0);
            }
            foreach ($records as $key => $record) {

               if ($key && $key == 'nextQuery') {
                    continue;
                }
                if($key && $key == 'totalRecord'){
                    continue;   
                }
                
                if (is_array($record)) {
                    $data = $record;
                } else {
                    $data = (array) $record;
                }
                $user_id = esigget('user_id', $data);
                $select = Esign_Query::_var($table, 'user_id', array("user_id" => $user_id), array('%d'));

                $exists = esigget("user_id", $select);
                if ($exists) {
                    unset($data['user_id']);
                    Esign_Query :: _update($table, $data, array('user_id' => $user_id), array('%d', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d'), array('%d'));
                } else {
                    Esign_Query::_insert($table, $data, array('%d', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d'));
                }
            }
        }

        /**
         * Process document_usres data from import xml file. 
         * @param type $table
         * @param type $records
         */
        public function process_document_users($table, $records) {

            if (!esig_is_func_disabled('set_time_limit') && !ini_get('safe_mode')) {
                set_time_limit(0);
            }

            foreach ($records as $key => $record) {

               if ($key && $key == 'nextQuery') {
                    continue;
                }
                if($key && $key == 'totalRecord'){
                    continue;   
                }
                
                if (is_array($record)) {
                    $data = $record;
                } else {
                    $data = (array) $record;
                }
                $id = esigget('id', $data);
                $select = Esign_Query::_var($table, 'id', array("id" => $id), array('%d'));

                $exists = esigget("id", $select);
                if ($exists) {
                    unset($data['id']);
                    Esign_Query::_update($table, $data, array('id' => $id), array('%d', '%d', '%s', '%s', '%s'), array('%d'));
                } else {
                    Esign_Query::_insert($table, $data, array('%d', '%d', '%d', '%s', '%s', '%s'));
                }
            }
        }

        /**
         * Process invitaitons data form import xml file. 
         * @param type $table
         * @param type $records
         */
        public function process_invitations($table, $records) {

            if (!esig_is_func_disabled('set_time_limit') && !ini_get('safe_mode')) {
                set_time_limit(0);
            }
            foreach ($records as $key => $record) {

                if ($key && $key == 'nextQuery') {
                    continue;
                }
                if($key && $key == 'totalRecord'){
                    continue;   
                }
                
                if (is_array($record)) {
                    $data = $record;
                } else {
                    $data = (array) $record;
                }
                $invitation_id = esigget('invitation_id', $data);
                $select = Esign_Query::_var($table, 'invitation_id', array("invitation_id" => $invitation_id), array('%d'));

                $exists = esigget("invitation_id", $select);
                if ($exists) {
                    unset($data['invitation_id']);
                    Esign_Query::_update($table, $data, array('invitation_id' => $invitation_id), array('%d', '%d', '%s', '%s', '%d', '%s', '%s'), array('%d'));
                } else {
                    Esign_Query::_insert($table, $data, array('%d', '%d', '%d', '%s', '%s', '%d', '%s', '%s'));
                }
            }
        }

        /**
         *  Proces data import for documents_stand_alone_docs table from xml import file. 
         * @param type $table
         * @param type $records
         */
        public function process_documents_stand_alone_docs($table, $records) {

            if (!esig_is_func_disabled('set_time_limit') && !ini_get('safe_mode')) {
                set_time_limit(0);
            }

            foreach ($records as $key => $record) {

                if ($key && $key == 'nextQuery') {
                    continue;
                }
                if($key && $key == 'totalRecord'){
                    continue;   
                }
                
                if (is_array($record)) {
                    $data = $record;
                } else {
                    $data = (array) $record;
                }
                $document_id = esigget('document_id', $data);

                $select = Esign_Query::_var($table, 'document_id', array("document_id" => $document_id), array('%d'));

                $exists = esigget("document_id", $select);
                if ($exists) {
                    unset($data['document_id']);
                    Esign_Query::_update($table, $data, array('document_id' => $document_id), array('%d', '%s', '%s'), array('%d'));
                } else {
                    Esign_Query::_insert($table, $data, array('%d', '%d', '%s', '%s'));
                }
            }
        }

        /**
         * Process documents signer input field data from import xml file. 
         * @param type $table
         * @param type $records
         */
        public function process_documents_signer_field_data($table, $records) {

            if (!esig_is_func_disabled('set_time_limit') && !ini_get('safe_mode')) {
                set_time_limit(0);
            }

            foreach ($records as $key => $record) {

               if ($key && $key == 'nextQuery') {
                    continue;
                }
                if($key && $key == 'totalRecord'){
                    continue;   
                }
                
                if (is_array($record)) {
                    $data = $record;
                } else {
                    $data = (array) $record;
                }
                $id = esigget('id', $data);
                $select = Esign_Query::_var($table, 'id', array("id" => $id), array('%d'));

                $exists = esigget("id", $select);
                if ($exists) {
                    unset($data['id']);
                    Esign_Query::_update($table, $data, array('id' => $id), array('%d', '%d', '%s', '%s', '%s'), array('%d'));
                } else {
                    Esign_Query::_insert($table, $data, array('%d', '%d', '%d', '%s', '%s', '%s'));
                }
            }
        }

        /**
         * Process document fields data from import xml file.
         * @param type $table
         * @param type $records
         */
        public function process_documents_fields_data($table, $records) {

            if (!esig_is_func_disabled('set_time_limit') && !ini_get('safe_mode')) {
                set_time_limit(0);
            }

            foreach ($records as $key => $record) {

                if ($key && $key == 'nextQuery') {
                    continue;
                }
                if($key && $key == 'totalRecord'){
                    continue;   
                }
                

                if (is_array($record)) {
                    $data = $record;
                } else {
                    $data = (array) $record;
                }
                $id = esigget('id', $data);
                $select = Esign_Query::_var($table, 'id', array("id" => $id), array('%d'));

                $exists = esigget("id", $select);
                if ($exists) {
                    unset($data['id']);
                    Esign_Query::_update($table, $data, array('id' => $id), array('%s', '%d', '%d', '%s', '%s'), array('%d'));
                } else {
                    
                    Esign_Query::_insert($table, $data, array('%d', '%s', '%d', '%d', '%s', '%s'));
                }
            }
        }

    }

    

    

    

    

    

    

    

    

    

    

    

    

    

    

    
 endif;