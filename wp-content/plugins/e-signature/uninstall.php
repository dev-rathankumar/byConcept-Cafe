<?php
	if( !defined('WP_UNINSTALL_PLUGIN') ) exit();
		global $wpdb;

		$table_prefix = $wpdb->prefix . "esign_";

		$setting_table=$table_prefix . "settings";
		$dbname= "Tables_in_" .  DB_NAME ;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');



		$esign_remove_all_data = $wpdb->get_var("SELECT setting_value FROM $setting_table where setting_name='esign_remove_all_data'" );

		if($esign_remove_all_data==1)
					{
		  
					$results=$wpdb->get_results("SHOW TABLES");
		
	
		
					foreach($results as $tablename) 
						{
		
							foreach($tablename as $table=>$value) 
					{
		  
							if (preg_match('/'. $table_prefix .'/',$value))
								{
		
										$sql = "DROP TABLE IF EXISTS `" . $value . "`";
										$wpdb->query($sql);
								}
		
					}
			}
		
		delete_option( "esig_db_version");	
	} 
	
	
