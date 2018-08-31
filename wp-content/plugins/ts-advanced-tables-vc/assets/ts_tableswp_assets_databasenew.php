<?php
	global $TS_ADVANCED_TABLESWP;
	
	// Render Preloader Animation
	echo '<div id="ts_vcsc_extend_settings_save" style="position: relative; margin: 20px auto 20px auto; width: 128px; height: 128px;">';
		echo TS_TablesWP_CreatePreloaderCSS("ts-settings-panel-loader", "", 19, "false");
	echo '</div>';
	
	// Delete Table Database
	TS_TablesWP_Database_TableDelete();
	
	// Recreate Table Database
	TS_TablesWP_Database_TableCreate(false);

	// Reset Table Sumamry Option
	update_option("ts_tablesplus_extend_settings_tables", array());
	
	// Reset Table Used ID's Storage
	update_option("ts_tablesplus_extend_settings_usedids", array());
	
	// Redirect to Maintenance Page
	echo '<script>window.location="' . admin_url('admin.php?page=TS_TablesWP_Tables') . '";</script>';
	
	Exit();
?>