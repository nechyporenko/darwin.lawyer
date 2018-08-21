<?php
	global $TS_ADVANCED_TABLESWP;
	global $wpdb;

	$TS_TablesWP_WordPress_FOpen						= ini_get('allow_url_fopen');
	
	//var_dump($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables);	
	//$TS_ADVANCED_TABLESWP->TS_TablesWP_PrepareAllOption();
	
	// Function to Restore Database Table(s) from Backup
	function TS_TablesWP_RestoreAllTables($database_backup) {
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
		global $wpdb;
		$TS_TablesWP_Editor_BaseName					= $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginMYSQL;
		// Get All Tables From Database
		$TS_TablesWP_Editor_Results 					= $wpdb->get_results("SELECT `id`, `number`, `name`, `cols`, `rows`, `created`, `updated`, `merged`, `info`, `other` FROM $TS_TablesWP_Editor_BaseName ORDER BY number", ARRAY_A);
		$TS_TablesWP_Editor_ID							= null;
		$TS_TablesWP_Editor_Number						= null;
		$TS_TablesWP_Editor_Other						= array();
		$TS_TablesWP_Editor_Count						= 0;
		$TS_TablesWP_Editor_Option						= array();
		foreach ($TS_TablesWP_Editor_Results as $table) {
			// Get Table Information
			$TS_TablesWP_Editor_ID						= $table['id'];
			$TS_TablesWP_Editor_Number					= $table['number'];
			$TS_TablesWP_Editor_Other					= $table['other'];
			$TS_TablesWP_Editor_Other					= json_decode($TS_TablesWP_Editor_Other, true);
			$TS_TablesWP_Editor_Option['table' . $TS_TablesWP_Editor_Number]	= array(
				"id"									=> $table['id'],
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
			echo'<script>window.location="' . admin_url('admin.php?page=TS_TablesWP_Restore') . '";</script>';
			Exit();
		}
	}
?>
	<div class="ts-vcsc-settings-page-header">
		<h2><span class="dashicons dashicons-grid-view"></span><?php echo __("Tablenator - Advanced Tables for WordPress", "ts_visual_composer_extend"); ?> v<?php echo TS_TablesWP_GetPluginVersion(); ?> ... <?php echo __("Restore Tables", "ts_visual_composer_extend"); ?></h2>
		<div class="clear"></div>
	</div>
	<form id="ts-advanced-table-migrate-form" class="ts-advanced-table-migrate-form" name="ts-advanced-table-migrate-form" autocomplete="off" enctype="multipart/form-data" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<div id="ts-advanced-table-restore-preloader-wrapper">
			<?php
				echo '<div id="ts-advanced-table-restore-preloader-holder">';
					echo TS_TablesWP_CreatePreloaderCSS("ts-advanced-table-restore-preloader-type", "", 11, "false");
				echo '</div>';  
			?>
		</div>
		<div class="ts-settings-restore-links" style="border-bottom: 1px dashed #cccccc; display: block; width: 100%; margin: 5px 0 20px;">
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
			<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
				<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to return to the tables maintenance page.", "ts_visual_composer_extend"); ?></span>
				<a href="<?php echo admin_url('admin.php?page=TS_TablesWP_Maintain'); ?>" target="_parent" class="ts-advanced-tables-button-main ts-advanced-tables-button-purple ts-advanced-tables-button-wrench">
					<?php echo __("Back To Maintenance", "ts_visual_composer_extend"); ?>
				</a>
			</div>
		</div>
		<div class="clearFixMe"></div>
		<img id="ts-advanced-table-restore-banner" style="display: block; width: 100%; max-width: 800px; height: auto; margin: 0px auto 0px auto;" src="<?php echo TS_TablesWP_GetResourceURL('images/banners/banner_import.jpg'); ?>">
		<div style="display: block; width: 100%;">
			<div class="ts-vcsc-notice-field ts-vcsc-warning" style="margin-top: 0px; font-size: 13px; text-align: justify;">
				<?php echo __("In order to restore a prior table backup, you need to upload the SQL backup file that was created for you at the time of a former backup call. The restoration process will delete all tables already existing on this site and replace them with the tables defined in the backup data.", "ts_visual_composer_extend"); ?>
			</div>
			<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
				<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to import the backup files for your tables.", "ts_visual_composer_extend"); ?></span>
				<button id="ts-advanced-table-restore-button" class="ts-advanced-tables-button-main ts-advanced-tables-button-orange ts-advanced-tables-button-update" disabled="disabled" type="submit" name="Restore" style="margin: 0 0 20px 0;">
					<?php echo __("Import Backup Data", "ts_visual_composer_extend"); ?>
				</button>
			</div>
			<div style="font-weight: bold; margin-bottom: 20px;"><?php echo __("Please select the SQL file containing your backup:", "ts_visual_composer_extend"); ?></div>
			<div id="ts-advanced-table-mysql-upload" class="ts-advanced-table-mysql-upload" style="display: <?php echo ($TS_TablesWP_WordPress_FOpen ? "block" : "none"); ?>; margin-top: 0px; margin-bottom: 20px;">
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
		</div>
	</form>