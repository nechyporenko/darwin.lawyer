<?php
	global $TS_ADVANCED_TABLESWP;
	global $wpdb;

	$TS_TablesWP_WordPress_FOpen						= ini_get('allow_url_fopen');
	
	// Check if Database Table Exists
	$charset_tablename									= $wpdb->prefix . TABLESWP_MYSQL;
	$charset_checkname									= TS_TablesWP_Datebase_TableCheck();
	
	// Function to Restore Database Table(s) from Backup
	function TS_TablesWP_RestoreAllTables($database_backup) {
		global $TS_ADVANCED_TABLESWP;
		global $wpdb;
		// Define Database Access
		$database_host									= DB_HOST;
		$database_user									= DB_USER;
		$database_pass									= DB_PASSWORD;
		$database_name									= DB_NAME;
		$database_mysqli								= null;
		$database_lines									= explode("\n", $database_backup);
		$database_errors								= array();
		// Set Time Limit
		set_time_limit(3000);
		// Initialize Database Connection
		$database_mysqli								= new mysqli($database_host, $database_user, $database_pass, $database_name);
		// Empty/Drop Existing Table
		$database_zzzzzz 								= $database_mysqli->query('SET foreign_key_checks = 0');
		preg_match_all("/\nCREATE TABLE(.*?)\`(.*?)\`/si", "\n". $database_backup, $database_tables);
		foreach ($database_tables[2] as $table) {
			$table 										= trim($table);
			if (TS_TablesWP_Datebase_TableCheck()) {
				//$database_mysqli->query("DROP TABLE $table");
				$database_mysqli->query("TRUNCATE TABLE $table");
				$database_mysqli->query("OPTIMIZE TABLE $table");
			}
		}
		$database_zzzzzz 								= $database_mysqli->query('SET foreign_key_checks = 1');
		$database_mysqli->query("SET NAMES 'utf8'");
		// Loop Backup Data
		$database_templine 								= '';
		foreach ($database_lines as $line)	{
			if (substr($line, 0, 2) != '--' && $line != '') {
				$database_templine 						.= $line;
				if (substr(trim($line), -1, 1) == ';') {
					if (!$database_mysqli->query($database_templine)) {
						$database_errors[]				= $database_mysqli->error;
					}
					$database_templine 					= '';
				}
			}
		}
		$database_mysqli->close();
		ob_get_clean();
		return $database_errors;
	}
	
	// Function to Restore Table(s) Reference Option
	function TS_TablesWP_RestoreAllOption() {
		global $TS_ADVANCED_TABLESWP;
		global $wpdb;
		$TS_TablesWP_Editor_BaseName					= $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginMYSQL;
		// Get All Tables From Database
		$TS_TablesWP_Editor_Results 					= $wpdb->get_results("SELECT `id`, `number`, `name`, `cols`, `rows`, `created`, `updated`, `merged`, `info`, `other` FROM $TS_TablesWP_Editor_BaseName ORDER BY number", ARRAY_A);
		$TS_TablesWP_Editor_Number						= null;
		$TS_TablesWP_Editor_Other						= array();
		$TS_TablesWP_Editor_Count						= 0;
		$TS_TablesWP_Editor_Option						= array();
		foreach ($TS_TablesWP_Editor_Results as $table) {
			// Get Table Information
			$TS_TablesWP_Editor_Number					= $table['number'];
			$TS_TablesWP_Editor_Other					= $table['other'];
			$TS_TablesWP_Editor_Other					= json_decode($TS_TablesWP_Editor_Other, true);
			$TS_TablesWP_Editor_Option['table' . $TS_TablesWP_Editor_Number]	= array(
				"id"									=> $TS_TablesWP_Editor_Number,
				"name"									=> $table['name'],
				"create"								=> intval(strtotime($table['created'])),
				"update"								=> intval(strtotime($table['updated'])),
				"info"									=> base64_encode($table['info']),
				"rows"									=> $table['rows'],
				"columns"								=> $table['cols'],
				"merged"								=> $table['merged'],
				"charts"								=> "false",
				"categories"							=> $TS_TablesWP_Editor_Other['categories'],
				"tags"									=> $TS_TablesWP_Editor_Other['tags'],
			);
		}
		unset($TS_TablesWP_Editor_Results);
		// Save Rebuild Reference Option
		update_option("ts_tablesplus_extend_settings_tables", $TS_TablesWP_Editor_Option);
		return true;
	}
	
	// Table Import Routine
    if (isset($_POST['Restore'])) {
        // Render Preloader Animation
        echo '<div id="ts_vcsc_extend_settings_save" style="position: relative; margin: 20px auto 20px auto; width: 128px; height: 128px;">';
            echo TS_TablesWP_CreatePreloaderCSS("ts-settings-panel-loader", "", 6, "false");
        echo '</div>';
		// Declare File Variables
		$TS_TablesWP_Editor_SQLObject					= "";
		$TS_TablesWP_Editor_SQLName						= "";
		$TS_TablesWP_Editor_SQLSource					= "";
		$TS_TablesWP_Editor_SQLType						= "";
		$TS_TablesWP_Editor_SQLPassed					= false;
		// Check for Backup Files
		if (isset($_FILES['ts-advanced-table-mysql-file'])) {			
			// Get SQL File Information
			$TS_TablesWP_Editor_SQLObject				= $_FILES['ts-advanced-table-mysql-file'];
			$TS_TablesWP_Editor_SQLName					= $TS_TablesWP_Editor_SQLObject["name"];
			$TS_TablesWP_Editor_SQLSource				= $TS_TablesWP_Editor_SQLObject["tmp_name"];
			$TS_TablesWP_Editor_SQLType					= $TS_TablesWP_Editor_SQLObject["type"];
		}
		// Validate SQL File
		if (($TS_TablesWP_Editor_SQLName != "") && ($TS_TablesWP_Editor_SQLType == 'application/octet-stream') && ($TS_TablesWP_WordPress_FOpen)) {
			$TS_TablesWP_Editor_SQLPassed				= true;
		} else {
			$TS_TablesWP_Editor_SQLPassed				= false;
		}
		// Get File Contents
		if ($TS_TablesWP_Editor_SQLPassed) {
			// Read SQL File
			$TS_TablesWP_Editor_SQLData					= file_get_contents($TS_TablesWP_Editor_SQLSource);
			// Get Old Table Name from Backup
			preg_match_all("/\nCREATE TABLE(.*?)\`(.*?)\`/si", "\n". $TS_TablesWP_Editor_SQLData, $TS_TablesWP_Editor_SQLFixOld);
			$TS_TablesWP_Editor_SQLFixOld				= trim($TS_TablesWP_Editor_SQLFixOld[2][0]);
			// Get New Table Name from Site
			$TS_TablesWP_Editor_SQLFixNew				= trim($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginMYSQL);
			// Adjust Table Name if Required
			if ($TS_TablesWP_Editor_SQLFixOld != $TS_TablesWP_Editor_SQLFixNew) {
				$TS_TablesWP_Editor_SQLData				= str_replace($TS_TablesWP_Editor_SQLFixOld, $TS_TablesWP_Editor_SQLFixNew, $TS_TablesWP_Editor_SQLData);
			}			
			// Remove C Style and Inline Comments
			$TS_TablesWP_Editor_SQLPattern 				= array('/\/\*.*(\n)*.*(\*\/)?/', '/\s*--.*\n/', '/\s*#.*\n/',);
			$TS_TablesWP_Editor_SQLExtract 				= preg_replace($TS_TablesWP_Editor_SQLPattern, "\n", $TS_TablesWP_Editor_SQLData);
			$TS_TablesWP_Editor_SQLImport				= array();
			// Retrieve SQL Statements
			$TS_TablesWP_Editor_SQLExtract 				= explode(";", $TS_TablesWP_Editor_SQLExtract);
			$TS_TablesWP_Editor_SQLExtract 				= preg_replace("/\s/", ' ', $TS_TablesWP_Editor_SQLExtract);
			foreach ($TS_TablesWP_Editor_SQLExtract as $entries) {
				$entries 								= trim($entries);
				if (strpos($entries, "INSERT INTO") === 0) {
					$TS_TablesWP_Editor_SQLImport[]		= $entries;
				}
			}
			// Add Tables to Database
			$TS_TablesWP_Editor_SQLProcess				= TS_TablesWP_RestoreAllTables($TS_TablesWP_Editor_SQLData);
			// Create Table Reference Data
			$TS_TablesWP_Editor_SQLReference			= TS_TablesWP_RestoreAllOption();
			// Redirect to Tables Listing Page
			echo'<script>window.location="' . admin_url('admin.php?page=TS_TablesWP_Tables') . '";</script>';
			Exit();
		} else {
			// Redirect to Tables Listing Page
			echo'<script>window.location="' . admin_url('admin.php?page=TS_TablesWP_Maintain') . '";</script>';
			Exit();
		}
	} else {
		$TS_TablesWP_Editor_BaseName					= $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginMYSQL;
		$TS_TablesWP_Editor_Results 					= $wpdb->get_results("SELECT `id`, `name` FROM $TS_TablesWP_Editor_BaseName ORDER BY id", ARRAY_A);
	}
?>
	<div class="ts-vcsc-settings-page-header">
		<h2><span class="dashicons dashicons-editor-table"></span><?php echo __("Tablenator - Advanced Tables for WordPress", "ts_visual_composer_extend"); ?> v<?php echo TS_TablesWP_GetPluginVersion(); ?> ... <?php echo __("Tables Maintenance", "ts_visual_composer_extend"); ?></h2>
		<div class="clear"></div>
	</div>
	<form id="ts-advanced-table-migrate-form" class="ts-advanced-table-migrate-form" name="ts-advanced-table-migrate-form" autocomplete="off" enctype="multipart/form-data" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<div id="ts-advanced-table-maintain-preloader-wrapper">
			<?php
				echo '<div id="ts-advanced-table-maintain-preloader-holder">';
					echo TS_TablesWP_CreatePreloaderCSS("ts-advanced-table-maintain-preloader-type", "", 11, "false");
				echo '</div>';  
			?>
		</div>
		<div class="ts-advanced-table-maintenance-links" style="border-bottom: 1px dashed #cccccc; display: block; width: 100%; margin: 5px 0 20px; float: left;">
			<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
				<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to return to the table listings page.", "ts_visual_composer_extend"); ?></span>
				<a href="<?php echo admin_url('admin.php?page=TS_TablesWP_Tables'); ?>" target="_parent" class="ts-advanced-tables-button-main ts-advanced-tables-button-turquoise ts-advanced-tables-button-listing">
					<?php echo __("Back to Listing", "ts_visual_composer_extend"); ?>
				</a>
			</div>
			<?php
				if (current_user_can('manage_options')) {
					echo '<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
						<span class="ts-advanced-table-tooltip-content">' . __("Click here to return to the plugins settings page.", "ts_visual_composer_extend") . '</span>
						<a href="' . admin_url('admin.php?page=TS_TablesWP_Settings') . '" target="_parent" class="ts-advanced-tables-button-main ts-advanced-tables-button-grey ts-advanced-tables-button-settings">'. __("Back to Settings", "ts_visual_composer_extend") . '</a>
					</div>';
				}
			?>
		</div>
		<div id="ts-advanced-table-language-wrapper" class="ts-advanced-table-language-wrapper" style="display: none;">
			<span id="ts-advanced-table-language-blank" class="ts-advanced-table-language-blank"></span>
			<span id="ts-advanced-table-language-yes" class="ts-advanced-table-language-yes"><?php echo __("Yes", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-no" class="ts-advanced-table-language-no"><?php echo __("No", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-submit" class="ts-advanced-table-language-submit"><?php echo __("Submit", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-cancel" class="ts-advanced-table-language-cancel"><?php echo __("Cancel", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-understood" class="ts-advanced-table-language-understood"><?php echo __("Understood!", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-continue" class="ts-advanced-table-language-continue"><?php echo __("Continue", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-okay" class="ts-advanced-table-language-okay"><?php echo __("Okay", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-plugin" class="ts-advanced-table-language-plugin"><?php echo __("Advanced Tables for WordPress", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-validtext" class="ts-advanced-table-language-validtext"><?php echo __("Your table validated correctly. Do you want to save this table?", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-errortext" class="ts-advanced-table-language-errortext"><?php echo __("You forgot to provide a name for this table; the table can not be saved without a name.", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-deletetitle" class="ts-advanced-table-language-deletetitle"><?php echo __("Delete Table #%d", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-deletetext" class="ts-advanced-table-language-deletetext"><?php echo __("Do you really want to delete table #%d (%s)?", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-clonetitle" class="ts-advanced-table-language-clonetitle"><?php echo __("Clone Table #%d", "ts_visual_composer_extend"); ?></span>        
			<span id="ts-advanced-table-language-clonetext" class="ts-advanced-table-language-clonetext"><?php echo __("Please provide a new and unique name for the cloned version of table #%d (%s)?", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-rebuildtitle" class="ts-advanced-table-language-rebuildtitle"><?php echo __("Create Database Table", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-rebuildtext" class="ts-advanced-table-language-rebuildtext"><?php echo __("Do you really want to attempt to create the missing SQL database table '%s' one more time?", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-clonesingle" class="ts-advanced-table-language-clonesingle"><?php echo __("Clone", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-generator" class="ts-advanced-table-language-generator"><?php echo __("Generate Shortcode", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-addtable" class="ts-advanced-table-language-addtable"><?php echo __("Add Table Shortcode", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-clearalltitle" class="ts-advanced-table-language-clearalltitle"><?php echo __("Delete ALL %d Table(s)", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-clearalltext" class="ts-advanced-table-language-clearalltext"><?php echo __("Do you really want to permanently delete ALL %d table(s)?", "ts_visual_composer_extend"); ?></span>			
			<span id="ts-advanced-table-language-referencetext" class="ts-advanced-table-language-referencetext"><?php echo __("Do you really want to rebuild the table reference data from the WordPress database?", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-referenceyes" class="ts-advanced-table-language-referenceyes"><?php echo __("The reference file for all %d tables found in the database has succesfully been created and saved.", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-referenceno" class="ts-advanced-table-language-referenceno"><?php echo __("The request could not be sent or was unsuccessful. The following error has been found:", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-referencezero" class="ts-advanced-table-language-referencezero"><?php echo __("Unfortunately, no data for any table could be found within the database.", "ts_visual_composer_extend"); ?></span>
		</div>		
		<div class="clearFixMe"></div>
		<img id="ts-advanced-table-maintenance-banner" style="display: block; width: 100%; max-width: 800px; height: auto; margin: 0 auto 0px auto;" src="<?php echo TS_TablesWP_GetResourceURL('images/banners/banner_maintenance.jpg'); ?>">
		<div id="ts-advanced-table-database-wrapper" style="display: <?php echo ($charset_checkname === true ? "none" : "block"); ?>; width: 100%; margin: 0 auto;">
			<div class="ts-vcsc-section-main">
				<div class="ts-vcsc-section-title ts-vcsc-section-show"><i class="dashicons dashicons-vault"></i><?php echo __("Database Actions", "ts_visual_composer_extend"); ?></div>
				<div class="ts-vcsc-section-content" style="display: block;">
					<div class="ts-vcsc-notice-field ts-vcsc-critical" style="margin-top: 20px; margin-bottom: 20px; font-size: 14px; text-align: justify;">
						<?php
							echo "<strong>" . sprintf(_("It appears that the required SQL database table '%s' to store all the data for each HTML table created through this plugin is missing."), $charset_tablename) . "</strong>";
							echo "<br/><br/>" . __("Usually, the missing table should have been created automatically when you first activated this plugin, however, for some reason, that process must have failed.", "ts_visual_composer_extend");
							echo "<br/><br/>" . __("Please use the button below to attempt to create the missing SQL database table one more time. If still unsuccessful, please contact plugin support for any further assistance.", "ts_visual_composer_extend");
						?>
					</div>
					<div id="ts-settings-maintenance-rebuild-wrapper" style="margin: 0 0 20px 0;"  data-name="<?php echo $charset_tablename; ?>" data-link="<?php echo admin_url('admin.php?page=TS_TablesWP_Rebuild'); ?>">
						<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
							<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to attempt to create the missing SQL database table.", "ts_visual_composer_extend"); ?></span>
							<div class="ts-advanced-tables-button-main ts-advanced-tables-button-red ts-advanced-tables-button-wrench" style="margin: 0;">
								<?php echo __("Create SQL Database Table", "ts_visual_composer_extend"); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="ts-advanced-table-maintenance-wrapper" style="display: <?php echo ($charset_checkname === true ? "block" : "none"); ?>; width: 100%; margin: 0 auto;">			
			<div class="ts-vcsc-section-main">
				<div class="ts-vcsc-section-title ts-vcsc-section-show"><i class="dashicons dashicons-media-default"></i><?php echo __("Single Table Actions", "ts_visual_composer_extend"); ?></div>
				<div class="ts-vcsc-section-content" style="display: block;">
					<div style="margin-top: 20px; margin-bottom: 0; font-size: 14px; text-align: justify; font-weight: bold;">
						<?php echo __("The following links will provide you with access to export and/or import routines for a single table, particularly useful if you need to quickly migrate a single or selected tables between different websites.", "ts_visual_composer_extend"); ?>
					</div>
					<div class="ts-vcsc-notice-field ts-vcsc-popup" style="display: <?php echo ((count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables) > 0) ? "block" : "none"); ?>; margin-top: 40px; margin-bottom: 20px; font-size: 14px; text-align: justify;">
						<?php echo __("If you want to create a export file for a single table, to be used for this site or to import on a different site, you can create the export file by using the button below.", "ts_visual_composer_extend"); ?>
					</div>
					<div id="ts-settings-maintenance-exportone-wrapper" style="display: <?php echo ((count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables) > 0) ? "block" : "none"); ?>; margin: 0 0 20px 0;"  data-count="<?php echo count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables); ?>" data-link="<?php echo admin_url('admin.php?page=TS_TablesWP_Export'); ?>">
						<a href="<?php echo admin_url('admin.php?page=TS_TablesWP_Export&direct=true'); ?>" target="_parent" class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
							<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to export a single table via single table export file.", "ts_visual_composer_extend"); ?></span>
							<div class="ts-advanced-tables-button-main ts-advanced-tables-button-blue ts-advanced-tables-button-export" style="margin: 0;">
								<?php echo __("Export Single Table", "ts_visual_composer_extend"); ?>
							</div>
						</a>
					</div>
					<div class="ts-vcsc-notice-field ts-vcsc-popup" style="margin-top: 40px; margin-bottom: 20px; font-size: 14px; text-align: justify;">
						<?php echo __("If you have an export file for a single table from either this site or another site that is utilizing this plugin, you can import the table by using the button below.", "ts_visual_composer_extend"); ?>
					</div>
					<div id="ts-settings-maintenance-importone-wrapper" style="display: block; margin: 0 0 20px 0;"  data-count="<?php echo count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables); ?>" data-link="<?php echo admin_url('admin.php?page=TS_TablesWP_Import'); ?>">
						<a href="<?php echo admin_url('admin.php?page=TS_TablesWP_Import'); ?>" target="_parent" class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
							<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to import a single table from a single table export file.", "ts_visual_composer_extend"); ?></span>
							<div class="ts-advanced-tables-button-main ts-advanced-tables-button-green ts-advanced-tables-button-update" style="margin: 0;">
								<?php echo __("Import Single Table", "ts_visual_composer_extend"); ?>
							</div>
						</a>
					</div>		
				</div>
			</div>
		</div>
		<div id="ts-advanced-table-categories-wrapper" style="display: <?php echo ($charset_checkname === true ? "block" : "none"); ?>; width: 100%; margin-top: 30px;">
			<div class="ts-vcsc-section-main">
				<div class="ts-vcsc-section-title ts-vcsc-section-show"><i class="dashicons dashicons-admin-page"></i><?php echo __("All Tables Actions", "ts_visual_composer_extend"); ?></div>
				<div class="ts-vcsc-section-content" style="display: block;">
					<div style="margin-top: 20px; margin-bottom: 0; font-size: 14px; text-align: justify; font-weight: bold;">
						<?php echo __("The following links will provide you with access to backup and/or restore routines for all tables currently created on this site, particularly useful for a complete backup of all tables. If required, you can also easily delete all existing tables.", "ts_visual_composer_extend"); ?>
					</div>					
					<div class="ts-vcsc-notice-field ts-vcsc-popup" style="display: <?php echo ((count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables) > 0) ? "block" : "none"); ?>; margin-top: 40px; margin-bottom: 20px; font-size: 14px; text-align: justify;">
						<?php echo __("If you want to create a full backup of all existing tables, to be used for this site or to restore on a different site, you can create the backup by using the button below. The backup package which will consist of a single SQL database file, containing all existing tables. The more tables exist or the more extensive the table content, the larger the backup file will be and the more time it will take to create it.", "ts_visual_composer_extend"); ?>
					</div>					
					<?php
						$last		= get_option("ts_tablesplus_extend_settings_lastbackup", "");
						$secret 	= md5(md5(AUTH_KEY . SECURE_AUTH_KEY) . '-' . 'ts-advanced-tables');
						$link 		= admin_url('admin-ajax.php?action=ts_backup_tables&secret=' . $secret . '');
					?>
					<div style="display: <?php echo ((count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables) > 0) ? "block" : "none"); ?>; padding: 0; margin: 20px auto;"><span style="display: inline-block;"><?php echo __("Last Backup:", "ts_visual_composer_extend") . '</span> <span id="ts-settings-maintenance-backupall-lastbackup" style="display: inline-block;">' . ($last != '' ? $last : __("N/A", "ts_visual_composer_extend")) ; ?></span></div>
					<div id="ts-settings-maintenance-backupall-wrapper" style="display: <?php echo ((count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables) > 0) ? "block" : "none"); ?>; margin: 0 0 20px 0;"  data-count="<?php echo count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables); ?>" data-link="<?php echo admin_url('admin.php?page=TS_TablesWP_Backup'); ?>">
						<div data-link="<?php echo $link ?>" data-timezone="<?php echo get_option('gmt_offset'); ?>" class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
							<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to create a full backup file for all tables.", "ts_visual_composer_extend"); ?></span>
							<div class="ts-advanced-tables-button-main ts-advanced-tables-button-blue ts-advanced-tables-button-export" style="margin: 0;">
								<?php printf(__("Backup ALL %d Table(s)", "ts_visual_composer_extend"), count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables)); ?>
							</div>
						</div>
					</div>
					<div class="ts-vcsc-notice-field ts-vcsc-popup" style="margin-top: 40px; margin-bottom: 20px; font-size: 14px; text-align: justify;">
						<?php echo __("If you have a full backup file from either this site or another site that is utilizing this plugin, you can restore the backup by using the button below.", "ts_visual_composer_extend"); ?>
					</div>
					<div id="ts-settings-maintenance-restoreall-wrapper" style="display: block; margin: 0 0 20px 0;"  data-count="<?php echo count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables); ?>" data-link="<?php echo admin_url('admin.php?page=TS_TablesWP_Restore'); ?>">
						<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
							<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to restore all tables from a full backup.", "ts_visual_composer_extend"); ?></span>
							<div class="ts-advanced-tables-button-main ts-advanced-tables-button-green ts-advanced-tables-button-update" style="margin: 0;">
								<?php echo __("Restore Full Backup", "ts_visual_composer_extend"); ?>
							</div>
						</div>
					</div>
					<div id="ts-settings-maintenance-restoreall-controls" style="display: none;" data-visible="false">
						<div class="ts-vcsc-notice-field ts-vcsc-warning" style="margin-top: 0px; font-size: 13px; text-align: justify; background: #f9f9f9;">
							<?php echo __("In order to restore a prior table backup, you need to upload the SQL backup file that was created for you at the time of a former backup call. The restoration process will delete all tables already existing on this site and replace them with the tables defined in the backup data.", "ts_visual_composer_extend"); ?>
						</div>
						<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
							<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to import the backup files for your tables.", "ts_visual_composer_extend"); ?></span>
							<button id="ts-advanced-table-restore-button" class="ts-advanced-tables-button-main ts-advanced-tables-button-orange ts-advanced-tables-button-update" disabled="disabled" type="submit" name="Restore" style="margin: 0 0 20px 0;">
								<?php echo __("Import Backup Data", "ts_visual_composer_extend"); ?>
							</button>
						</div>
						<div style="font-weight: bold; margin-bottom: 20px;"><?php echo __("Please select the SQL file containing your backup:", "ts_visual_composer_extend"); ?></div>
						<div id="ts-advanced-table-mysql-upload" class="ts-advanced-table-mysql-upload" style="display: <?php echo ($TS_TablesWP_WordPress_FOpen ? "block" : "none"); ?>; margin-top: 0px; margin-bottom: 0px;">
							<div id="ts-advanced-table-text-mysql" class="ts-advanced-table-mysql-box">
								<input type="file" accept=".sql" name="ts-advanced-table-mysql-file" id="ts-advanced-table-mysql-file" class="ts-advanced-table-mysql-file"/>
								<label id="ts-advanced-table-mysql-label" class="ts-advanced-table-mysql-label" for="ts-advanced-table-mysql-file" style="display: inline-block;">
									<span id="ts-advanced-table-mysql-name" class="ts-advanced-table-mysql-name"></span>
									<span id="ts-advanced-table-mysql-select" class="ts-advanced-table-mysql-select ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
										<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to select the SQL database file to be imported from your computer.", "ts_visual_composer_extend"); ?></span>
										<span class="ts-advanced-tables-button-main ts-advanced-tables-button-red ts-advanced-tables-button-text" style="margin: 0; min-width: 280px;">
											<?php echo __("Select SQL Database File", "ts_visual_composer_extend"); ?>
										</span>
									</span>
								</label>
							</div>
						</div>
						<div class="clearFixMe"></div>
					</div>
					<div class="ts-vcsc-notice-field ts-vcsc-popup" style="display: <?php echo ((count($TS_TablesWP_Editor_Results) > 0) ? "block" : "none"); ?>; margin-top: 40px; margin-bottom: 20px; font-size: 14px; text-align: justify;">
						<?php echo __("If the plugin listing page is for some reason not showing any or all tables, even though they are stored in the WordPress database, you can attempt to rebuild the internal reference file for all tables by using the button below.", "ts_visual_composer_extend"); ?>
					</div>
					<div id="ts-settings-maintenance-referenceall-wrapper" style="display: <?php echo ((count($TS_TablesWP_Editor_Results) > 0) ? "block" : "none"); ?>; margin: 0 0 20px 0;"  data-count="<?php echo count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables); ?>" data-link="<?php echo admin_url('admin.php?page=TS_TablesWP_Delete'); ?>">
						<div data-secret="<?php echo $secret; ?>" data-link="<?php echo admin_url('admin.php?page=TS_TablesWP_Tables'); ?>" class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
							<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to rebuild a corrupt reference file for all existing tables.", "ts_visual_composer_extend"); ?></span>
							<div class="ts-advanced-tables-button-main ts-advanced-tables-button-silver ts-advanced-tables-button-edit" style="margin: 0;">
								<?php echo __("Rebuild Table Reference", "ts_visual_composer_extend"); ?>
							</div>
						</div>
					</div>
					<div class="ts-vcsc-notice-field ts-vcsc-popup" style="display: <?php echo ((count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables) > 0) ? "block" : "none"); ?>; margin-top: 40px; margin-bottom: 20px; font-size: 14px; text-align: justify;">
						<?php echo __("If you want to quickly delete all existing tables and to clear all references to them in the WordPress database, you can do so by using the button below.", "ts_visual_composer_extend"); ?>
					</div>
					<div id="ts-settings-maintenance-deleteall-wrapper" style="display: <?php echo ((count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables) > 0) ? "block" : "none"); ?>; margin: 0 0 20px 0;"  data-count="<?php echo count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables); ?>" data-link="<?php echo admin_url('admin.php?page=TS_TablesWP_Delete'); ?>">
						<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
							<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to delete all existing tables from the database.", "ts_visual_composer_extend"); ?></span>
							<div class="ts-advanced-tables-button-main ts-advanced-tables-button-red ts-advanced-tables-button-delete" style="margin: 0;">
								<?php printf(__("Delete ALL %d Table(s)", "ts_visual_composer_extend"), count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables)); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>