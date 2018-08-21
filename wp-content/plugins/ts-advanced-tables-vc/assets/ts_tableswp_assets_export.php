<?php
	global $TS_ADVANCED_TABLESWP;
	global $wpdb;
	
	// General Variables	
	if ((isset($_GET['direct'])) && (($_GET['direct'] === true) || ($_GET['direct'] === "true"))) {
		$TS_TablesWP_Editor_Direct				= true;
	} else {
		$TS_TablesWP_Editor_Direct				= false;
	}	
	if (isset($_GET['tableid'])) {
		$TS_TablesWP_Editor_ID					= $_GET['tableid'];
		$TS_TablesWP_Editor_Direct				= false;
	} else {
		$TS_TablesWP_Editor_ID					= __("N/A", "ts_visual_composer_extend");
		$TS_TablesWP_Editor_Direct				= true;
	}
	
	// Check for Valid Table
	$TS_TablesWP_Editor_Valid					= isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables['table' . $TS_TablesWP_Editor_ID]);
	$TS_TablesWP_Editor_Summary					= $TS_TablesWP_Editor_Valid ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables['table' . $TS_TablesWP_Editor_ID] : array();
	$TS_TablesWP_Editor_Secret 					= md5(md5(AUTH_KEY . SECURE_AUTH_KEY) . '-' . 'ts-advanced-tables');
	$TS_TablesWP_Editor_Link					= admin_url('admin-ajax.php?action=ts_export_table&secret=' . $TS_TablesWP_Editor_Secret);
	
	// Retrieve Table from Database
	if ($TS_TablesWP_Editor_Valid) {
		$TS_TablesWP_Editor_BaseName			= $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginMYSQL;
		$TS_TablesWP_Editor_BaseData			= $wpdb->get_row("SELECT * FROM $TS_TablesWP_Editor_BaseName WHERE `number` = $TS_TablesWP_Editor_ID");
		if ($TS_TablesWP_Editor_BaseData == null) {
			$TS_TablesWP_Editor_BaseData		= array();
		}
		$TS_TablesWP_Editor_BaseData			= base64_encode(json_encode($TS_TablesWP_Editor_BaseData));
	} else {
		$TS_TablesWP_Editor_BaseData			= "";
	}
?>
	<div class="ts-vcsc-settings-page-header">
		<h2><span class="dashicons dashicons-editor-table"></span><?php echo __("Tablenator - Advanced Tables for WordPress", "ts_visual_composer_extend"); ?> v<?php echo TS_TablesWP_GetPluginVersion(); ?> ... <?php echo __("Export Single Table", "ts_visual_composer_extend"); ?> #<?php echo $TS_TablesWP_Editor_ID; ?></h2>
		<div class="clear"></div>
	</div>
	<form id="ts-advanced-table-migrate-form" class="ts-advanced-table-migrate-form" name="ts-advanced-table-migrate-form" autocomplete="off" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" style="position: relative;">
		<div id="ts-advanced-table-change-preloader-wrapper">
			<?php
				echo '<div id="ts-advanced-table-change-preloader-holder">';
					echo TS_TablesWP_CreatePreloaderCSS("ts-advanced-table-change-preloader-type", "", 11, "false");
				echo '</div>';  
			?>
		</div>
		<div class="ts-advanced-table-change-links" style="border-bottom: 1px dashed #cccccc; display: block; width: 100%; margin: 5px 0 20px;">
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
		<img id="ts-advanced-table-export-banner" style="display: block; width: 100%; max-width: 800px; height: auto; margin: 0 auto 40px auto;" src="<?php echo TS_TablesWP_GetResourceURL('images/banners/banner_export.jpg'); ?>">
		<div style="display: <?php echo ($TS_TablesWP_Editor_Direct ? "block" : "none"); ?>; width: 100%; margin: 5px 0 20px;">
			<div class="ts-advanced-table-change-label"><?php echo __("Table Selection:", "ts_visual_composer_extend"); ?></div>
			<div class="ts-vcsc-notice-field ts-vcsc-critical" style="margin-top: 20px; margin-bottom: 20px; font-size: 13px; text-align: justify;">
				<?php echo __("Please note that the more extensive the content of the requested table is (number of rows or columns and individual cell contents), the longer it will take to retrieve and process the table data from the database. Please do not refresh this page or use the browser back button while the table data is getting retrieved.", "ts_visual_composer_extend"); ?>
			</div>
			<select id="ts-advanced-table-change-select" class="" data-link="<?php echo $TS_TablesWP_Editor_Link; ?>" data-secret="<?php echo $TS_TablesWP_Editor_Secret; ?>" data-search="<?php echo __("Search ...", "ts_visual_composer_extend"); ?>" data-placeholder="<?php echo __("Select a table for export ...", "ts_visual_composer_extend"); ?>">				
				<?php
					echo '<option value="" disabled="disabled" ' . ($TS_TablesWP_Editor_Direct ? 'selected="selected"' : '') . '>' . __("Select a table for export ...", "ts_visual_composer_extend") . '</option>';
					foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables as $tables => $table) {
						$TS_TablesWP_Editor_Number 		= trim(strip_tags($table['id']));
						$TS_TablesWP_Editor_Name 		= $table['name'];
						$TS_TablesWP_Editor_Created 	= date($TS_ADVANCED_TABLESWP->TS_TablesWP_WordPress_Date . ' - ' . $TS_ADVANCED_TABLESWP->TS_TablesWP_WordPress_Time, $table['create']);
						$TS_TablesWP_Editor_Updated 	= date($TS_ADVANCED_TABLESWP->TS_TablesWP_WordPress_Date . ' - ' . $TS_ADVANCED_TABLESWP->TS_TablesWP_WordPress_Time, $table['update']);
						$TS_TablesWP_Editor_Rows 		= $table['rows'];
						$TS_TablesWP_Editor_Columns 	= $table['columns'];
						echo '<option value="' . $TS_TablesWP_Editor_Number . '" data-id="' . $TS_TablesWP_Editor_Number . '" data-name="' . $TS_TablesWP_Editor_Name . '" data-create="' . $TS_TablesWP_Editor_Created . '" data-update="' . $TS_TablesWP_Editor_Updated . '" data-rows="' . $TS_TablesWP_Editor_Rows . '" data-columns="' . $TS_TablesWP_Editor_Columns . '" ' . selected($TS_TablesWP_Editor_Number, $TS_TablesWP_Editor_ID) . '>' . $TS_TablesWP_Editor_Name . ' (#' . $TS_TablesWP_Editor_Number . ')</option>';
					}
				?>
			</select>
		</div>
		<div id="ts-advanced-table-change-summary">
			<div class="ts-advanced-table-change-label"><?php echo __("Table Summary:", "ts_visual_composer_extend"); ?></div>
			<div id="ts-advanced-table-change-id"><span><?php echo __("Table ID:", "ts_visual_composer_extend"); ?></span> <span><?php echo $TS_TablesWP_Editor_ID; ?></span></div>
			<div id="ts-advanced-table-change-name"><span><?php echo __("Table Name:", "ts_visual_composer_extend"); ?></span> <span><?php echo (isset($TS_TablesWP_Editor_Summary['name']) ? $TS_TablesWP_Editor_Summary['name'] : 'N/A'); ?></span></div>
			<div id="ts-advanced-table-change-create"><span><?php echo __("Created At:", "ts_visual_composer_extend"); ?></span> <span><?php echo (isset($TS_TablesWP_Editor_Summary['create']) ? date($TS_ADVANCED_TABLESWP->TS_TablesWP_WordPress_Date . ' - ' . $TS_ADVANCED_TABLESWP->TS_TablesWP_WordPress_Time, $TS_TablesWP_Editor_Summary['create']) : 'N/A'); ?></span></div>
			<div id="ts-advanced-table-change-update"><span><?php echo __("Last Updated:", "ts_visual_composer_extend"); ?></span> <span><?php echo (isset($TS_TablesWP_Editor_Summary['update']) ? date($TS_ADVANCED_TABLESWP->TS_TablesWP_WordPress_Date . ' - ' . $TS_ADVANCED_TABLESWP->TS_TablesWP_WordPress_Time, $TS_TablesWP_Editor_Summary['update']) : 'N/A'); ?></span></div>
			<div id="ts-advanced-table-change-rows"><span><?php echo __("Rows:", "ts_visual_composer_extend"); ?></span> <span><?php echo (isset($TS_TablesWP_Editor_Summary['rows']) ? $TS_TablesWP_Editor_Summary['rows'] : 'N/A'); ?></span></div>
			<div id="ts-advanced-table-change-columns"><span><?php echo __("Columns:", "ts_visual_composer_extend"); ?></span> <span><?php echo (isset($TS_TablesWP_Editor_Summary['columns']) ? $TS_TablesWP_Editor_Summary['columns'] : 'N/A'); ?></span></div>
		</div>
		<div style="display: <?php echo (($TS_TablesWP_Editor_Valid || $TS_TablesWP_Editor_Direct) ? "none" : "block");?>; width: 100%; margin: 5px 0 20px;">
			<div class="ts-vcsc-notice-field ts-vcsc-critical" style="margin-top: 0px; margin-bottom: 0px; font-size: 13px; text-align: justify;">
				<?php echo __("A table with the selected ID does not exist or could not be found and can therefore not be exported! Please check the provided table ID!", "ts_visual_composer_extend"); ?>
			</div>
		</div>
		<div id="ts-advanced-table-change-results" style="display: <?php echo (($TS_TablesWP_Editor_Valid && !$TS_TablesWP_Editor_Direct) ? "block" : "none");?>; width: 100%; margin: 5px 0 20px;">
			<div class="ts-vcsc-notice-field ts-vcsc-warning" style="margin-top: 0px; margin-bottom: 25px; font-size: 13px; text-align: justify;">
				<?php echo __("Use the buttons below to either save the table data within a portable .txt file, or to copy it directly into the browser clipboard. Please keep in mind that based on the size of the table data to be safed, the clipboard might not be able to store all information. The exported data includes all cell content as well as any meta data and formatting applied to cells and is encoded via base64 for security purposes.", "ts_visual_composer_extend"); ?>
			</div>
			<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
				<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to save this table as TXT file so you can import the table on another site.", "ts_visual_composer_extend"); ?></span>
				<a href="<?php echo $TS_TablesWP_Editor_Link . '&tableid=' . $TS_TablesWP_Editor_ID; ?>" target="_parent" id="ts-advanced-table-file-button" class="ts-advanced-tables-button-main ts-advanced-tables-button-orange ts-advanced-tables-button-text">
					<?php echo __("Save as .TXT File", "ts_visual_composer_extend"); ?>
				</a>
			</div>
			<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
				<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to copy the table data into the browser clipboard.", "ts_visual_composer_extend"); ?></span>
				<a href="javascript:void(0);" target="_parent" id="ts-advanced-table-copy-button" class="ts-advanced-tables-button-main ts-advanced-tables-button-orange ts-advanced-tables-button-copy" data-clipboard-target="#ts-advanced-table-export-content">
					<?php echo __("Copy to Clipboard", "ts_visual_composer_extend"); ?>
				</a>
			</div>			
			<div id="ts-settings-statistics-copy-success" class="ts-vcsc-notice-field ts-vcsc-success" style="display: none; font-size: 13px; text-align: justify; width: 100%; float: left;">
				<?php echo __("The data for the table has been copied to your clipboard!", "ts_visual_composer_extend"); ?>
			</div>
			<div id="ts-settings-statistics-copy-error" class="ts-vcsc-notice-field ts-vcsc-critical" style="display: none; font-size: 13px; text-align: justify; width: 100%; float: left;">
				<?php echo __("The data for the table could NOT be copied to your clipboard!", "ts_visual_composer_extend"); ?>
			</div>			
			<textarea id="ts-advanced-table-export-content" class="ts-advanced-table-export-content" name="ts-advanced-table-export-content"><?php echo $TS_TablesWP_Editor_BaseData; ?></textarea>
		</div>
	</form>