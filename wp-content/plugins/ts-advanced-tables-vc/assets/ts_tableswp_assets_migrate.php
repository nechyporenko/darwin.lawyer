<?php
	global $TS_ADVANCED_TABLESWP;
	global $wpdb;
	
	$TS_TablesWP_Migrate_Count						= 0;
	$TS_TablesWP_Migrate_Total						= count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables);
	
	// Create Database Table
	TS_TablesWP_Database_TableCreate(true);
	// Check Database Table
	if (TS_TablesWP_Datebase_TableCheck() == false) {
		$TS_TablesWP_Migrate_Proceed				= "false";
		update_option("ts_tablesplus_extend_settings_database", "false");
	} else {
		$TS_TablesWP_Migrate_Proceed				= "true";		
	}
	
	// Table Migrate Routine
    if (isset($_POST['Migrate'])) {
		// Get Migration Delete Setting
		$TS_TablesWP_Migrate_Delete 				= ((intval(((isset($_POST['ts-advanced-table-migrate-delete'])) ? $_POST['ts-advanced-table-migrate-delete'] : 0))) == 0 ? false : true);
		// Load Progressbar Class
		require_once("ts_tableswp_assets_progressbar.php");
        // Render Preloader Animation
        echo '<div id="ts_vcsc_extend_settings_save" style="position: relative; margin: 20px auto 20px auto; width: 128px; height: 128px;">';
            echo TS_TablesWP_CreatePreloaderCSS("ts-settings-panel-loader", "", 6, "false");
        echo '</div>';
		// Create Database Table
		TS_TablesWP_Database_TableCreate(true);
		// Empty Database Table
		$TS_TablesWP_WordPressTable  				= $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginMYSQL;
		$wpdb->query("TRUNCATE TABLE $TS_TablesWP_WordPressTable");		
		// Migrate Option Tables
		$TS_TablesWP_Migrate_Progress				= '';
		if ($TS_TablesWP_Migrate_Total > 0) {
			// Initialize Progressbar
			$TS_TablesWP_Migrate_Progress 			= new TS_TablesWP_Animated_Progressbar();
			$TS_TablesWP_Migrate_Progress->TS_TablesWP_ProgressbarNewText(__("Processing all existing tables ...", "ts_visual_composer_extend"));
			$TS_TablesWP_Migrate_Progress->TS_TablesWP_ProgressbarCreate();
			// Loop + Process All Tables
			foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables as $tables => $table) {
				// Increase Counter
				$TS_TablesWP_Migrate_Count++;
				// Retrieve Table Data
				$TS_TablesWP_Editor_ID				= $table['id'];
				$TS_TablesWP_Editor_Name        	= $table['name'];
				$TS_TablesWP_Editor_Export			= "";
				$TS_TablesWP_Editor_Import			= get_option('ts_tablesplus_data_singletable_' . $TS_TablesWP_Editor_ID, '');
				$TS_TablesWP_Editor_Import			= (rawurldecode(base64_decode(strip_tags($TS_TablesWP_Editor_Import))));
				$TS_TablesWP_Editor_Decode			= json_decode($TS_TablesWP_Editor_Import);
				// Build Table Dates
				if (isset($table['create'])) {
					$TS_TablesWP_Editor_Create		= intval($table['create']);
				} else {
					$TS_TablesWP_Editor_Create		= current_time('timestamp', 0);
				}
				$TS_TablesWP_Editor_Create			= date("Y/m/d H:i:s", $TS_TablesWP_Editor_Create);
				if (isset($table['update'])) {
					$TS_TablesWP_Editor_Update		= intval($table['update']);
				} else {
					$TS_TablesWP_Editor_Update		= current_time('timestamp', 0);
				}
				$TS_TablesWP_Editor_Update			= date("Y/m/d H:i:s", $TS_TablesWP_Editor_Update);
				// Assign Table Data
				if (isset($TS_TablesWP_Editor_Decode->info)) {
					$TS_TablesWP_Editor_Info		= $TS_TablesWP_Editor_Decode->info;
				} else {
					$TS_TablesWP_Editor_Info		= "";
				}
				if (isset($TS_TablesWP_Editor_Decode->rows)) {
					$TS_TablesWP_Editor_Rows		= $TS_TablesWP_Editor_Decode->rows;
				} else {
					$TS_TablesWP_Editor_Rows		= 0;
				}
				if (isset($TS_TablesWP_Editor_Decode->columns)) {
					$TS_TablesWP_Editor_Columns		= $TS_TablesWP_Editor_Decode->columns;
				} else {
					$TS_TablesWP_Editor_Columns		= 0;
				}
				if (isset($TS_TablesWP_Editor_Decode->defaults)) {
					$TS_TablesWP_Editor_Defaults	= json_encode($TS_TablesWP_Editor_Decode->defaults);
				} else {
					$TS_TablesWP_Editor_Defaults	= json_encode(new stdClass());
				}
				if (isset($TS_TablesWP_Editor_Decode->merged)) {
					$TS_TablesWP_Editor_Merged		= json_encode($TS_TablesWP_Editor_Decode->merged);
				} else {
					$TS_TablesWP_Editor_Merged		= json_encode(array());
				}
				if (isset($TS_TablesWP_Editor_Decode->data)) {
					$TS_TablesWP_Editor_Data		= json_encode($TS_TablesWP_Editor_Decode->data);
				} else {
					$TS_TablesWP_Editor_Data		= json_encode(array());
				}
				if (isset($TS_TablesWP_Editor_Decode->meta)) {
					$TS_TablesWP_Editor_Meta		= json_encode($TS_TablesWP_Editor_Decode->meta);
				} else {
					$TS_TablesWP_Editor_Meta		= json_encode(array());
				}				
				if (isset($TS_TablesWP_Editor_Decode->formulas)) {
					$TS_TablesWP_Editor_Formulas	= $TS_TablesWP_Editor_Decode->formulas;
				} else {
					$TS_TablesWP_Editor_Formulas	= "";
				}
				if (isset($TS_TablesWP_Editor_Decode->search)) {
					$TS_TablesWP_Editor_Search		= $TS_TablesWP_Editor_Decode->search;
				} else {
					$TS_TablesWP_Editor_Search		= "";
				}
				if (isset($TS_TablesWP_Editor_Decode->charts)) {
					$TS_TablesWP_Editor_Charts		= $TS_TablesWP_Editor_Decode->charts;
				} else {
					$TS_TablesWP_Editor_Charts		= "";
				}				
				$TS_TablesWP_Editor_Other			= array(
					"csvexternal"					=> "false",
					"csvpath"						=> "",
					"savemeta"						=> "true",
					"usecharts"						=> $TS_TablesWP_Editor_Charts,
					"useformulas"					=> $TS_TablesWP_Editor_Formulas,
					"usesearch"						=> $TS_TablesWP_Editor_Search,
					"usecontext"					=> "true",
					"usevalidator"					=> "true",
					"fixrow"						=> "true",
					"fixcolumn"						=> "false",
					"categories"					=> "",
					"tags"							=> "",
				);
				$TS_TablesWP_Editor_Other			= json_encode($TS_TablesWP_Editor_Other);
				// Add New Table to Database
				$wpdb->insert($wpdb->prefix . "ts_advancedtables", array(
					"number" 						=> $TS_TablesWP_Editor_ID,
					"name" 							=> $TS_TablesWP_Editor_Name,
					"created"						=> $TS_TablesWP_Editor_Create,
					"updated"						=> $TS_TablesWP_Editor_Update,
					"cols" 							=> $TS_TablesWP_Editor_Columns,
					"rows" 							=> $TS_TablesWP_Editor_Rows,
					"info" 							=> $TS_TablesWP_Editor_Info,
					"merged" 						=> $TS_TablesWP_Editor_Merged,
					"defaults" 						=> $TS_TablesWP_Editor_Defaults,
					"data" 							=> $TS_TablesWP_Editor_Data,
					"meta" 							=> $TS_TablesWP_Editor_Meta,
					"other" 						=> $TS_TablesWP_Editor_Other,	
				));
				// Delete Option Table
				if ($TS_TablesWP_Migrate_Delete) {
					delete_option('ts_tablesplus_data_singletable_' . $TS_TablesWP_Editor_ID);
				}
				// Update Progressbar
				$TS_TablesWP_Migrate_Progress->TS_TablesWP_ProgressbarNewText("Processing " . $TS_TablesWP_Editor_Name . " ID #" . $TS_TablesWP_Editor_ID . " (" . $TS_TablesWP_Migrate_Count . " of " . $TS_TablesWP_Migrate_Total . ") ...");
				$TS_TablesWP_Migrate_Progress->TS_TablesWP_ProgressbarCalculate($TS_TablesWP_Migrate_Total);
				$TS_TablesWP_Migrate_Progress->TS_TablesWP_ProgressbarAnimate();
			}
			// Mark Migration Complete
			update_option("ts_tablesplus_extend_settings_migrated", "true");
			// Remove Progressbar
			$TS_TablesWP_Migrate_Progress->TS_TablesWP_ProgressbarHide(2000);
			// Go Back to Table Listing
			$TS_TablesWP_Migrate_Progress->TS_TablesWP_ProgressbarURL(admin_url('admin.php?page=TS_TablesWP_Tables'), 4000);
		}
		Exit();
	}
?>
	<div class="ts-vcsc-settings-page-header">
		<h2><span class="dashicons dashicons-grid-view"></span><?php echo __("Tablenator - Advanced Tables for WordPress & WP Bakery Page Builder", "ts_visual_composer_extend"); ?> v<?php echo TS_TablesWP_GetPluginVersion(); ?> ... <?php echo __("Tables Migration", "ts_visual_composer_extend"); ?></h2>
		<div class="clear"></div>
	</div>
	<form id="ts-advanced-table-migrate-form" class="ts-advanced-table-migrate-form" name="ts-advanced-table-migrate-form" autocomplete="off" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<div class="ts-settings-statistics-links" style="border-bottom: 1px dashed #cccccc; display: block; width: 100%; margin: 5px 0 20px; float: left;">
			<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
				<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to go to a listing of all created tables.", "ts_visual_composer_extend"); ?></span>
				<a href="<?php echo admin_url('admin.php?page=TS_TablesWP_Tables'); ?>" target="_parent" class="ts-advanced-tables-button-main ts-advanced-tables-button-turquoise ts-advanced-tables-button-listing" style="margin: 0 20px 0 0;">
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
        <div id="ts-advanced-table-controls-wrapper" class="ts-advanced-table-controls-wrapper">
			<div style="width: 200px; text-align: left; float: left; margin: 0 40px 0 0; padding: 0; display: block;">
				<img src="<?php echo TS_TablesWP_GetResourceURL('images/logos/migration_logo.png'); ?>" style="max-width: 100%; height: auto;">
            </div>
			<div style="width: auto; float: none; margin: 0 0 0 240px; padding: 0; display: block;">
				<div style="display: block; width: 100%; margin: 0; padding: 0; font-weight: bold; font-size: 14px;">
					<?php echo __("Database table could be found:", "ts_visual_composer_extend") . ' ' . ($TS_TablesWP_Migrate_Proceed == "true" ? __("Yes", "ts_visual_composer_extend") : __("No", "ts_visual_composer_extend")); ?>
				</div>
				<div style="display: block; width: 100%; margin: 0; padding: 0; font-weight: bold; font-size: 14px;">
					<?php echo __("Number of tables to be migrated:", "ts_visual_composer_extend") . ' ' . $TS_TablesWP_Migrate_Total; ?>
				</div>
				<?php if ($TS_TablesWP_Migrate_Proceed == "false") { ?>
					<div class="ts-vcsc-notice-field ts-vcsc-critical" style="margin-top: 20px; margin-bottom: 20px; font-size: 13px; text-align: justify;">
						<?php echo __("The plugin was unable to create the required custom database table, which is meant to be used to store all table data. The migration process can not be initiated as long as the database table is missing. Please contact support for more information and further assistance. In the meantime and in order to continue using this plugin at this point, please revert back to v1.0.2 of the plugin.", "ts_visual_composer_extend"); ?>
					</div>
					<div class="ts-vcsc-notice-field ts-vcsc-warning" style="margin-top: 20px; margin-bottom: 20px; font-size: 13px; text-align: justify;">
						<?php echo __("If your server maintains an error.log/debug.log file, it might contain information as to why the database table could not be created. Alternatively, it is recommended to install and activate the free 'Query Monitor' plugin, which might be able to provide you with more in-depth information about the underlying cause of the issue.", "ts_visual_composer_extend"); ?>
					</div>
					<div class="ts-advanced-table-controls-wrapper" style="float: none; display: block; width: 100%; min-height: 40px; margin: 0; padding: 0;">
						<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder" style="margin-top: 10px; margin-right: 20px;">
							<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to go to the official support forum for the plugin.", "ts_visual_composer_extend"); ?></span>
							<a href="http://helpdesk.krautcoding.com/forums/forum/wordpress-plugins/advanced-tables-for-visual-composer/" target="_blank" class="ts-advanced-tables-button-main ts-advanced-tables-button-red ts-advanced-tables-button-wrench" style="margin: 0;">
								<?php echo __("Support Forum", "ts_visual_composer_extend"); ?>
							</a>
						</div>
						<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
							<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to view more information about the 'Query Monitor' plugin.", "ts_visual_composer_extend"); ?></span>
							<a href="https://wordpress.org/plugins/query-monitor/" target="_blank" class="ts-advanced-tables-button-main ts-advanced-tables-button-orange ts-advanced-tables-button-link" style="margin: 0 20px 0 0;">
								<?php echo __("Query Monitor Plugin", "ts_visual_composer_extend"); ?>
							</a>
						</div>	
					</div>
				<?php } else { ?>
					<div class="ts-vcsc-notice-field ts-vcsc-critical" style="margin-top: 20px; margin-bottom: 20px; font-size: 13px; text-align: justify;">
						<?php echo __("By clicking on the button below, the automatic migration process for your existing tables will be initialized. Based on the number and scope of the tables to be migrated, the process can take a short while. Please do not refresh your browser or go to a different page while the migration process is still active. Once the process has been completed, the browser will go back to the overall table listing page automatically.", "ts_visual_composer_extend"); ?>
					</div>
					<div style="margin-top: 10px; margin-bottom: 20px;">
						<h4><?php echo __("Delete Old Table Data During Migration", "ts_visual_composer_extend"); ?></h4>
						<p style="font-size: 12px; margin: 10px auto;"><?php echo __("Define if the plugin should delete all old table data in the WordPress options database table during the migration:", "ts_visual_composer_extend"); ?></p>					
						<div class="ts-vcsc-notice-field ts-vcsc-warning" style="margin-bottom: 20px; font-size: 13px; text-align: justify;">
							<?php echo __("In order to relieve the WordPress options database table, where the table data has been stored up until now, all data entries will be removed during the migration process, which means going back to an older plugin version while still being able to re-edit your existing table will be impossible. If you want to keep the old database entries for your tables, use the setting option provided below.", "ts_visual_composer_extend"); ?>
						</div>
						<div class="ts-switch-button ts-codestar-field-switcher" data-value="1">
							<div class="ts-codestar-fieldset">
								<label class="ts-codestar-label">
									<input id="ts-advanced-table-migrate-delete" data-order="1" value="1" class="ts-codestar-checkbox ts-advanced-table-migrate-delete" name="ts-advanced-table-migrate-delete" type="checkbox" <?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DeleteTables == true ? 'checked="checked"' : ''); ?>> 
									<em data-on="<?php echo __("Yes", "ts_visual_composer_extend"); ?>" data-off="<?php echo __("No", "ts_visual_composer_extend"); ?>"></em>
									<span></span>
								</label>
							</div>
						</div>
						<label class="labelToggleBox" for="ts-advanced-table-migrate-delete"><?php echo __("Delete Old Table Data During Migration", "ts_visual_composer_extend"); ?></label>
					</div>
					<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder" style="margin-top: 20px;">
						<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to start the automated migration process for all existing tables.", "ts_visual_composer_extend"); ?></span>
						<button id="ts-advanced-table-migrate-button" class="ts-advanced-tables-button-main ts-advanced-tables-button-red ts-advanced-tables-button-update" type="submit" name="Migrate">
							<?php echo __("Start Migration", "ts_visual_composer_extend"); ?>
						</button>
					</div>				
				<?php } ?>
			</div>
        </div>
	</form>