<?php
	global $TS_ADVANCED_TABLESWP;
	
	// Render Preloader Animation
	echo '<div id="ts_vcsc_extend_settings_save" style="position: relative; margin: 20px auto 20px auto; width: 128px; height: 128px;">';
		echo TS_TablesWP_CreatePreloaderCSS("ts-settings-panel-loader", "", 16, "false");
	echo '</div>';
	
	// Delete All Tables from Database	
	global $wpdb;
	$table  	= $wpdb->prefix . "ts_advancedtables";
	$delete 	= $wpdb->query("TRUNCATE TABLE `$table`");
	$optimize 	= $wpdb->query("OPTIMIZE TABLE `$table`");

	// Reset Table Sumamry Option
	update_option("ts_tablesplus_extend_settings_tables", array());
	
	// Reset Table Used ID's Storage
	update_option("ts_tablesplus_extend_settings_usedids", array());
	
	// Redirect to Listing Page
	echo '<script>window.location="' . admin_url('admin.php?page=TS_TablesWP_Tables') . '";</script>';
	
	Exit();
?>