<?php
    global $TS_ADVANCED_TABLESWP;    

	// Check for Visual Composer and Minimum Version
	// ---------------------------------------------
	add_action('admin_init',				'TS_TablesWP_Init_Addon');	
    function TS_TablesWP_Init_Addon() {
        add_action('admin_notices', 'TS_TablesWP_Admin_Notice_Version');
    }
    function TS_TablesWP_Admin_Notice_Version() {
		global $TS_ADVANCED_TABLESWP;
		if (($TS_ADVANCED_TABLESWP->TS_TablesWP_VisualComposer_Active == "true") && ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_ComposerIntegrate == true) && ($TS_ADVANCED_TABLESWP->TS_TablesWP_VisualComposer_Required == "false")) {
			echo '<div class="ts-advanced-tables-admin-notice notice notice-error is-dismissible"><p>' . __("The 'Tablenator - Advanced Tables for WordPress & WP Bakery Page Builder' plugin requires a WP Bakery Page Builder version of 4.9.0 or greater.", "ts_visual_composer_extend") . '</p></div>';
		}
    }
    function TS_TablesWP_Admin_Notice_Network() {
        echo '<div class="ts-advanced-tables-admin-notice notice notice-warning is-dismissible"><p>' . __("The 'Tablenator - Advanced Tables for WordPress & WP Bakery Page Builder' plugin can not be activated network-wide but only on individual sub-sites.", "ts_visual_composer_extend") . '</p></div>';
    }
	
	
	// Function to Delete All Option with Prefix
	// -----------------------------------------
	if (!function_exists('TS_TablesWP_DeleteOptionsPrefixed')){
		function TS_TablesWP_DeleteOptionsPrefixed($prefix) {
			global $wpdb;
			$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '{$prefix}%'" );
		}
	}
    
    
	// Callback Functions for Plugin Activation / Deactivation / Uninstall
	// -------------------------------------------------------------------
    function TS_TablesWP_Callback_Activation() {

    }
    function TS_TablesWP_Callback_Deactivation() {
        
    }
    function TS_TablesWP_Callback_Uninstall() {
		global $wpdb;
		$TS_TablesWP_Settings_Globals 			= get_option("ts_tablesplus_extend_settings_globals", array());
		$TS_TablesWP_Settings_DeleteTables		= ((isset($TS_TablesWP_Settings_Globals['general']['deletetables'])) ? $TS_TablesWP_Settings_Globals['general']['deletetables'] : true);		
		if ($TS_TablesWP_Settings_DeleteTables == true) {
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
    
    
    // Function to run if new Blog created on MutliSite
    // ------------------------------------------------	
    add_action('wpmu_new_blog', 			'TS_TablesWP_On_New_BlogSite', 10, 6);
    function TS_TablesWP_On_New_BlogSite($blog_id, $user_id, $domain, $path, $site_id, $meta) {
        global $wpdb;
        if (function_exists('is_multisite') && is_multisite()) {
            if (is_plugin_active_for_network('ts-advanced-tables-vc/ts-advanced-tables-vc.php')) {
                $old_blog = $wpdb->blogid;
                switch_to_blog($blog_id);
                TS_TablesWP_Callback_Activation();
                switch_to_blog($old_blog);
            }
        }
    }
?>