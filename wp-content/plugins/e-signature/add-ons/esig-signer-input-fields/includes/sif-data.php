<?php

class esignSifData extends Esign_Query {

    public static function addValue($field_id, $recipient_id, $document_id, $value) {
        
        $date = WP_E_Sig()->document->esig_date($document_id);
        if (!self::exists($field_id, $recipient_id, $document_id)) {
            self::_insert(self::$table_fields_data, array('field_id' => $field_id,
                'recipient_id' => $recipient_id,
                'document_id' => $document_id,
                'value' => $value,
                'created_at' => $date
                    ), array('%s', '%s', '%s', '%s', '%s'));
            
            return true;
        } 
        return false;
    }

    public static function exists($field_id, $recipient_id, $document_id) {
        $value = self::_var(self::$table_fields_data, 'id', array('field_id' => $field_id, 'recipient_id' => $recipient_id, 'document_id' => $document_id), array('%s', '%d', '%s'));
        if ($value['id']) {
            return true;
        } else {
            return false;
        }
    }

    public static function getSingleValue($field_id, $recipient_id, $document_id) {
        $value = self::_var(self::$table_fields_data, 'value', array('field_id' => $field_id, 'recipient_id' => $recipient_id, 'document_id' => $document_id), array('%s', '%d', '%s'));
        return $value['value'];
    }
    
     public static function getFieldValue($field_id, $document_id) {
        $value = self::_var(self::$table_fields_data, 'value', array('field_id' => $field_id,'document_id' => $document_id), array('%s','%s'));
        return $value['value'];
    }

    public static function getAllValue($document_id, $recipient_id) {
        return self::_results(self::$table_fields_data, array('recipient_id' => $recipient_id, 'document_id' => $document_id), array('%d', '%s'));
    }

    public static function getDocValue($document_id) {
        return self::_results(self::$table_fields_data, array('document_id' => $document_id), array('%s'));
    }
    
    public static function deleteValue($documentId){
         return self::_delete(self::$table_fields_data,array('document_id'=>$documentId),array('%s'));
    }
    
}

