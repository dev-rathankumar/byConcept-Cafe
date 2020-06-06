<?php

global $wpdb;

$table_prefix = $wpdb->prefix . "esign_";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');



$collate = '';

if ($wpdb->has_cap('collation')) {
    $collate = $wpdb->get_charset_collate();
}

// Documents Table
$sql = "CREATE TABLE IF NOT EXISTS `" . $table_prefix . "documents`(
				`document_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`user_id` int(11) NOT NULL,
				`post_id` int(11) NOT NULL,
				`document_title` varchar(200) NOT NULL,
				`document_content` longtext NOT NULL,
				`notify` tinyint(1) NOT NULL DEFAULT 0,
				`add_signature` tinyint(1) NOT NULL DEFAULT 0,
				`document_type` enum('stand_alone','normal','esig_template','esig-gravity') NOT NULL DEFAULT 'normal',
				`document_status` varchar(24) NOT NULL,
				`document_checksum` text NOT NULL,
				`document_uri` text NULL,
				`ip_address` varchar(100) NOT NULL DEFAULT '0.0.0.0',
				`date_created` datetime NOT NULL,
				`last_modified` datetime NOT NULL,
                                KEY document_title (document_title(191)),
                                KEY document_type (document_type),
                                KEY document_status (document_status),
                                KEY last_modified (last_modified),
                                KEY document_checksum (document_checksum(100))
                                )".$collate;

dbDelta($sql);


// Generic Settings Table
$sql = "CREATE TABLE IF NOT EXISTS `" . $table_prefix . "settings`(
			  `setting_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			  `user_id` int(11) NOT NULL,
			  `setting_name` varchar(55) NOT NULL,
			  `setting_value` longtext NOT NULL,
                          KEY setting_name (setting_name)
                          )".$collate;
dbDelta($sql);


// Set initialized to 'false'
//$sql = "INSERT INTO " . $table_prefix . "settings VALUES(null, 1, 'initialized', 'false')";
//dbDelta($sql);
// Signatures Table
$sql = "CREATE TABLE IF NOT EXISTS `" . $table_prefix . "signatures`(
			  `signature_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			  `user_id` int(11) NOT NULL,
			  `signature_type` varchar(20) NOT NULL DEFAULT 'full',
			  `signature_hash` char(64) NOT NULL,
			  `signature_salt` char(40) NOT NULL,
			  `signature_data` longtext NOT NULL,
			  `signature_added` datetime NOT NULL,
                          KEY user_id (user_id),
                          KEY signature_type (signature_type)) ".$collate;
dbDelta($sql);


// Documents / Signatures Join Table
$sql = "CREATE TABLE IF NOT EXISTS `" . $table_prefix . "documents_signatures`(
			  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			  `document_id` int(11) NOT NULL,
			  `signature_id` int(11) NOT NULL,
			  `ip_address` varchar(100) NOT NULL,
			  `sign_date` datetime NOT NULL,
                          `signer_type` varchar(100) NULL,
                          KEY document_id (document_id),
                          KEY signature_id (signature_id)) ".$collate;
dbDelta($sql);

// Documents / Signatures Join Table
$sql = "CREATE TABLE IF NOT EXISTS `" . $table_prefix . "documents_meta`(
			  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
			  `document_id` bigint(20) unsigned NOT NULL,
			  `meta_key` varchar(255) NOT NULL,
			  `meta_value` longtext NOT NULL,
                          KEY document_id (document_id),
                          KEY meta_key (meta_key(191))
			  ) ".$collate;
dbDelta($sql);


// Documents Events Join Table
$sql = "CREATE TABLE IF NOT EXISTS `" . $table_prefix . "documents_events`(
			  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			  `document_id` int(11) NOT NULL,
			  `event` varchar(20) NOT NULL,
			  `event_data` varchar(255) NOT NULL,
			  `date` datetime NOT NULL,
                          `ip_address` varchar(100) NOT NULL,
                          KEY document_id (document_id),
                          KEY event (event),
                          KEY event_data (event_data(191)),
                          KEY date (date)
                          ) ".$collate;
dbDelta($sql);


// Users Table
$sql = "CREATE TABLE IF NOT EXISTS `" . $table_prefix . "users`(
			  `user_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			  `wp_user_id` int(11) NULL,
			  `uuid` char(36) NOT NULL,
			  `user_email` varchar(100) NOT NULL,
			  `user_title` varchar(55) NOT NULL DEFAULT '',
			  `first_name` varchar(45) NOT NULL,
			  `last_name` varchar(65) NOT NULL,
                          `is_admin` SMALLINT(6) NOT NULL,
                          `is_signer` SMALLINT(6) NOT NULL,
                          `is_sa` SMALLINT(6) NOT NULL,
                          `is_inactive` SMALLINT(6) NOT NULL,
                          KEY user_email (user_email)
                          ) ".$collate;
dbDelta($sql);

// create a document users table . 

$sql = "CREATE TABLE IF NOT EXISTS `" . $table_prefix . "document_users`(
			  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			  `user_id` int(11) NOT NULL,
			  `document_id` int(11) NOT NULL,
			  `signer_name` varchar(64) NOT NULL,
                          `signer_email` varchar(64) NOT NULL,
                          `company_name` varchar(64) NOT NULL,
                          KEY document_id (document_id),
                          KEY signer_name (signer_name),
                          KEY signer_email (signer_email),
                          KEY user_id (user_id)
			  ) ".$collate;
dbDelta($sql);


// Invitation table
$sql = "CREATE TABLE IF NOT EXISTS `" . $table_prefix . "invitations`(
			  `invitation_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			  `user_id` int(11) NOT NULL,
			  `document_id` int(11) NOT NULL,
			  `invite_hash` text NOT NULL,
			  `invite_message` longtext NOT NULL,
			  `invite_sent` tinyint(1) NOT NULL DEFAULT 0,
			  `sender_ip` varchar(100) NOT NULL DEFAULT '0.0.0.0',
			  `invite_sent_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                          KEY document_id (document_id),
                          KEY invite_hash (invite_hash(100))
                          ) ".$collate;
dbDelta($sql);

// stand alone tables 
$sql = "CREATE TABLE IF NOT EXISTS `" . $table_prefix . "documents_stand_alone_docs`(
			`document_id` int(11) NOT NULL PRIMARY KEY,
			`page_id` int(11) NOT NULL,
			`date_created` datetime NOT NULL,
			`date_modified` datetime NOT NULL,
                        KEY page_id (page_id)) ".$collate;
dbDelta($sql);
//$wpdb->query($sql);
// create signer input fields table 
$sql = "CREATE TABLE IF NOT EXISTS `" . $table_prefix . "documents_signer_field_data`(
			    `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			    `signature_id` int(11) NOT NULL,
			    `document_id` int(11) NOT NULL,
			    `input_fields` longtext NOT NULL,
			    `date_created` datetime NOT NULL,
			    `date_modified` datetime NOT NULL,
                            KEY document_id (document_id)) ".$collate;
dbDelta($sql);
//$wpdb->query($sql);
// document fields  data tables 
$sql = "CREATE TABLE IF NOT EXISTS `" . $table_prefix . "documents_fields_data`(
			    `id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                            `field_id` varchar(100) NOT NULL,
                            `recipient_id` bigint(20) NOT NULL,
                            `document_id` bigint(20) NOT NULL,
                            `value` longtext NOT NULL,
			    `created_at` datetime NOT NULL,
                            KEY field_id (field_id),
                            KEY recipient_id (recipient_id),
                            KEY document_id (document_id)
			    ) ".$collate;


dbDelta($sql);
