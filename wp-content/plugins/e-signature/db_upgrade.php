<?php

global $wpdb;

$table_prefix = $wpdb->prefix . "esign_";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

// UPgrade Documents Table
// db upgrade version : 6.0 
// existing user table is being updated. 

function esig_table_column_exists($table_name, $column_name) {
    global $wpdb;
    $column = $wpdb->get_results($wpdb->prepare(
                    "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s ", DB_NAME, $table_name, $column_name
            ));
    if (!empty($column)) {
        return true;
    }
    return false;
}

$sql_documents_table = "ALTER TABLE " . $table_prefix . "documents
CHANGE `ip_address` `ip_address` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;";
$wpdb->query($sql_documents_table);

$sql_documents_signature_table = "ALTER TABLE " . $table_prefix . "documents_signatures
CHANGE `ip_address` `ip_address` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;";
$wpdb->query($sql_documents_signature_table);


if (!esig_table_column_exists($table_prefix . "documents_signatures", "signer_type")) {

    $sql_documents_signature_table1 = "ALTER TABLE " . $table_prefix . "documents_signatures
ADD COLUMN `signer_type` VARCHAR(55) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL;";
    $wpdb->query($sql_documents_signature_table1);
}


$sql_documents_event_table = "ALTER TABLE " . $table_prefix . "documents_events
CHANGE `ip_address` `ip_address` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;";
$wpdb->query($sql_documents_event_table);


$sql_documents_invitations_table = "ALTER TABLE " . $table_prefix . "invitations
CHANGE `sender_ip` `sender_ip` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;";
$wpdb->query($sql_documents_invitations_table);




// document fields  data tables 
$sql = "CREATE TABLE IF NOT EXISTS `" . $table_prefix . "documents_fields_data`(
			    `id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                            `field_id` varchar(100) NOT NULL,
                            `recipient_id` bigint(20) NOT NULL,
                            `document_id` bigint(20) NOT NULL,
                            `value` longtext NOT NULL,
			    `created_at` datetime NOT NULL
			    ) ENGINE = INNODB DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci";


dbDelta($sql);


$index_exists = $wpdb->get_row("SHOW INDEX FROM {$table_prefix}documents WHERE column_name = 'document_title' and key_name = 'document_title'");

if (is_null($index_exists)) {
    // documents table adding index 
    $wpdb->query("ALTER TABLE {$table_prefix}documents ADD INDEX document_title (document_title)");
    $wpdb->query("ALTER TABLE {$table_prefix}documents ADD INDEX document_type (document_type)");
    $wpdb->query("ALTER TABLE {$table_prefix}documents ADD INDEX document_status (document_status)");
    $wpdb->query("ALTER TABLE {$table_prefix}documents ADD INDEX last_modified (last_modified)");
    $wpdb->query("ALTER TABLE {$table_prefix}documents ADD INDEX document_checksum (document_checksum(100))");

    // documents events table adding index. 
    $wpdb->query("ALTER TABLE {$table_prefix}documents_events ADD INDEX document_id (document_id)");
    $wpdb->query("ALTER TABLE {$table_prefix}documents_events ADD INDEX event (event)");
    $wpdb->query("ALTER TABLE {$table_prefix}documents_events ADD INDEX event_data (event_data)");
    $wpdb->query("ALTER TABLE {$table_prefix}documents_events ADD INDEX date (date)");

    // adding index into documents_field_data table 

    $wpdb->query("ALTER TABLE {$table_prefix}documents_fields_data ADD INDEX field_id (field_id)");
    $wpdb->query("ALTER TABLE {$table_prefix}documents_fields_data ADD INDEX recipient_id (recipient_id)");
    $wpdb->query("ALTER TABLE {$table_prefix}documents_fields_data ADD INDEX document_id (document_id)");

    // adding index into documents_meta table. 
    $wpdb->query("ALTER TABLE {$table_prefix}documents_meta ADD INDEX document_id (document_id)");
    $wpdb->query("ALTER TABLE {$table_prefix}documents_meta ADD INDEX meta_key (meta_key)");

    // adding index into documents_signature table.
    $wpdb->query("ALTER TABLE {$table_prefix}documents_signatures ADD INDEX document_id (document_id)");
    $wpdb->query("ALTER TABLE {$table_prefix}documents_signatures ADD INDEX signature_id (signature_id)");

    // adding index into documents_signer_field_data
    $wpdb->query("ALTER TABLE {$table_prefix}documents_signer_field_data ADD INDEX document_id (document_id)");

    // adding index into documents_stand_alone_docs
    $wpdb->query("ALTER TABLE {$table_prefix}documents_stand_alone_docs ADD INDEX page_id (page_id)");


    // adding index into documents_user table
    $wpdb->query("ALTER TABLE {$table_prefix}document_users ADD INDEX document_id (document_id)");
    $wpdb->query("ALTER TABLE {$table_prefix}document_users ADD INDEX signer_name (signer_name)");
    $wpdb->query("ALTER TABLE {$table_prefix}document_users ADD INDEX signer_email (signer_email)");
    $wpdb->query("ALTER TABLE {$table_prefix}document_users ADD INDEX user_id (user_id)");

    // adding index into settings table 
    $wpdb->query("ALTER TABLE {$table_prefix}settings ADD INDEX setting_name (setting_name)");

    // adding index in signatures table
    $wpdb->query("ALTER TABLE {$table_prefix}signatures ADD INDEX signature_type (signature_type)");
    $wpdb->query("ALTER TABLE {$table_prefix}signatures ADD INDEX user_id (user_id)");

    // adding index in user table
    $wpdb->query("ALTER TABLE {$table_prefix}users ADD INDEX user_email (user_email)");

    // adding index in invitations table
    $wpdb->query("ALTER TABLE {$table_prefix}invitations ADD INDEX document_id (document_id)");
    $wpdb->query("ALTER TABLE {$table_prefix}invitations ADD INDEX invite_hash (invite_hash(100))");
}


