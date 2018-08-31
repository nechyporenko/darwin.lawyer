<?php
	if (!defined('WP_UNINSTALL_PLUGIN')) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
	}	
	
	if (!is_user_logged_in()) {
		wp_die('You must be logged in to run this script.');
	}
	
	if (!current_user_can('install_plugins')) {
		wp_die('You do not have permission to run this script.');
	}

	if (!function_exists('TS_TablesWP_DeleteOptionsPrefixed')){
		function TS_TablesWP_DeleteOptionsPrefixed($prefix) {
			global $wpdb;
			$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '{$prefix}%'" );
		}
	}

	global $TS_ADVANCED_TABLESWP;
	global $wpdb;
	
	if (function_exists('is_multisite') && is_multisite()) {
		if ($network_wide) {
			global $wpdb;
			global $TS_ADVANCED_TABLESWP;
			$old_blog 	= $wpdb->blogid;
			$blogids 	= $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);				
				$TS_TablesWP_Uninstall_Globals 			= get_option("ts_tablesplus_extend_settings_globals", array());
				$TS_TablesWP_Uninstall_DeleteTables		= ((isset($TS_TablesWP_Uninstall_Globals['general']['deletetables'])) ? $TS_TablesWP_Uninstall_Globals['general']['deletetables'] : true);
				if ($TS_TablesWP_Uninstall_DeleteTables) {
					// Delete Plugin Option Settings
					delete_option('ts_tablesplus_extend_settings_tables');
					delete_option('ts_tablesplus_extend_settings_usedids');
					delete_option('ts_tablesplus_extend_settings_migrated');
					delete_option('ts_tablesplus_extend_settings_database');
					// Drop Database Table
					$charset_tablename 					= $wpdb->prefix . "ts_advancedtables"; 
					$sql 								= "DROP TABLE IF EXISTS $charset_tablename";
					$wpdb->query($sql);
					// Delete Old Table Data Option Settings
					TS_TablesWP_DeleteOptionsPrefixed('ts_tablesplus_data_singletable_');
				}
				delete_option('ts_tablesplus_extend_settings_globals');
			}
			switch_to_blog($old_blog);
			return;
		}
	}
	
	$TS_TablesWP_Uninstall_Globals 			= get_option("ts_tablesplus_extend_settings_globals", array());
	$TS_TablesWP_Uninstall_DeleteTables		= ((isset($TS_TablesWP_Uninstall_Globals['general']['deletetables'])) ? $TS_TablesWP_Uninstall_Globals['general']['deletetables'] : true);
	if ($TS_TablesWP_Uninstall_DeleteTables) {
		// Delete Plugin Option Settings
		delete_option('ts_tablesplus_extend_settings_tables');
		delete_option('ts_tablesplus_extend_settings_usedids');
		delete_option('ts_tablesplus_extend_settings_migrated');
		delete_option('ts_tablesplus_extend_settings_database');
		// Drop Database Table
		$charset_tablename 					= $wpdb->prefix . "ts_advancedtables"; 
		$sql 								= "DROP TABLE IF EXISTS $charset_tablename";
		$wpdb->query($sql);
		// Delete Old Table Data Option Settings
		TS_TablesWP_DeleteOptionsPrefixed('ts_tablesplus_data_singletable_');
	}
	delete_option('ts_tablesplus_extend_settings_globals');
?>