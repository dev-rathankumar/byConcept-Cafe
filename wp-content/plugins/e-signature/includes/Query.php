<?php

abstract class Esign_Query {

    //tables name 
    public static $table_documents = 'documents';
    public static $table_documents_events = 'documents_events';
    public static $table_documents_meta = 'documents_meta';
    public static $table_documents_signature = 'documents_signatures';
    public static $table_recipients = 'recipients';
    public static $table_recipients_meta = 'recipients_meta';
    public static $table_settings = 'settings';
    public static $table_signatures = 'signatures';
    public static $table_fields ='documents_fields';
    public static $table_fields_meta = 'documents_fields_meta';
    public static $table_fields_data = 'documents_fields_data';
    public static $table_signer_fields_data = 'documents_signer_field_data';
    public static $table_users = 'users' ; 
    public static $table_sad = 'documents_stand_alone_docs';

    public static function dbconnect() {
        global $wpdb;
        return $wpdb;
    }

    protected static function prefix() {
        return self::dbconnect()->prefix . "esign_";
    }

    public static function table_name($table_name) {
        return self::prefix() . $table_name;
    }

    public static function _insert($table_name, $data, $format) {
        self::dbconnect()->insert(self::table_name($table_name), $data, $format);
        return self::dbconnect()->insert_id;
    }

    public static function _update($table, $data, $where, $format = null, $where_format = null) {
       self::dbconnect()->update(self::table_name($table), $data, $where, $format, $where_format);
       return self::dbconnect()->insert_id; 
    }

    public static function _delete($table, $where, $where_format = null) {
        return self::dbconnect()->delete(self::table_name($table), $where, $where_format);
    }

    public static function _var($table, $select_name, $where, $format) {

        $where = self::make_format($where, $format);
        $fields = $conditions = $values = array();
        foreach ($where as $field => $value) {
            if (is_null($value['value'])) {
                $conditions[] = "`$field` IS NULL";
                continue;
            }

            $conditions[] = "`$field` = " . $value['format'];
            $values[] = $value['value'];
        }

        $fields = implode(', ', $fields);
        $conditions = implode(' AND ', $conditions);

        $sql = "SELECT $select_name FROM " . self::table_name($table) . " WHERE " . $conditions;

        $result = self::dbconnect()->get_var(self::dbconnect()->prepare($sql, $values));
        return array($select_name => $result);
    }

    public static function _row($table, $where, $format) {

        $where = self::make_format($where, $format);
        $fields = $conditions = $values = array();
        foreach ($where as $field => $value) {
            if (is_null($value['value'])) {
                $conditions[] = "`$field` IS NULL";
                continue;
            }

            $conditions[] = "`$field` = " . $value['format'];
            $values[] = $value['value'];
        }

        $fields = implode(', ', $fields);
        $conditions = implode(' AND ', $conditions);

        $sql = "SELECT * FROM " . self::table_name($table) . " WHERE " . $conditions . " LIMIT 1";

        return self::dbconnect()->get_row(self::dbconnect()->prepare($sql, $values), OBJECT);
    }

    public static function _results($table, $where, $format) {

        $where = self::make_format($where, $format);
        $fields = $conditions = $values = array();
        foreach ($where as $field => $value) {
            if (is_null($value['value'])) {
                $conditions[] = "`$field` IS NULL";
                continue;
            }

            $conditions[] = "`$field` = " . $value['format'];
            $values[] = $value['value'];
        }

        $fields = implode(', ', $fields);
        $conditions = implode(' AND ', $conditions);

        $sql = "SELECT * FROM " . self::table_name($table) . " WHERE " . $conditions;

        return self::dbconnect()->get_results(self::dbconnect()->prepare($sql, $values), OBJECT);
    }
    
    public static function _select_results($table,$selects, $where, $format,$implode='AND'){
        
        $where = self::make_format($where, $format);
        $fields = $conditions = $values = array();
        foreach ($where as $field => $value) {
            if (is_null($value['value'])) {
                $conditions[] = "`$field` IS NULL";
                continue;
            }

            $conditions[] = "`$field` = " . $value['format'];
            $values[] = $value['value'];
        }

        $fields = implode(', ', $fields);
        $conditions = implode(' '. $implode .' ', $conditions);
        
         $data_list = implode(",", $selects);
         
        $sql = "SELECT ". $data_list ." FROM " . self::table_name($table) . " WHERE " . $conditions;

        return self::dbconnect()->get_results(self::dbconnect()->prepare($sql, $values), OBJECT);
    }

    public static function _all_results($table) {

        $sql = "SELECT * FROM " . self::table_name($table);

        return self::dbconnect()->get_results($sql, OBJECT);
    }

    protected static function make_format($data, $format) {
        $formats = $original_formats = (array) $format;

        foreach ($data as $field => $value) {
            $value = array(
                'value' => $value,
                'format' => '%s',
            );

            if (!empty($format)) {
                $value['format'] = array_shift($formats);
                if (!$value['format']) {
                    $value['format'] = reset($original_formats);
                }
            } 

            $data[$field] = $value;
        }

        return $data;
    } 
}
