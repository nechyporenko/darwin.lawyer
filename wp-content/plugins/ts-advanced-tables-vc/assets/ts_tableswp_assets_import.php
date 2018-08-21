<?php
	global $TS_ADVANCED_TABLESWP;
	global $wpdb;

	$TS_TablesWP_WordPress_FOpen						= ini_get('allow_url_fopen');
	
	// Table Import Routine
    if (isset($_POST['Import'])) {
        // Render Preloader Animation
        echo '<div id="ts_vcsc_extend_settings_save" style="position: relative; margin: 20px auto 20px auto; width: 128px; height: 128px;">';
            echo TS_TablesWP_CreatePreloaderCSS("ts-settings-panel-loader", "", 6, "false");
        echo '</div>';
		// Read Import Data
		$TS_TablesWP_Editor_Date            			= current_time('timestamp', 0);
		$TS_TablesWP_Editor_Import          			= trim ($_POST['ts-advanced-table-import-content']);
		if (isset($_FILES['ts-advanced-table-text-file'])) {
			$TS_TablesWP_Editor_FileObject				= $_FILES['ts-advanced-table-text-file'];
			$TS_TablesWP_Editor_FileName				= $TS_TablesWP_Editor_FileObject["name"];
			$TS_TablesWP_Editor_FileSource				= $TS_TablesWP_Editor_FileObject["tmp_name"];
			$TS_TablesWP_Editor_FileType				= $TS_TablesWP_Editor_FileObject["type"];	
		} else {
			$TS_TablesWP_Editor_FileObject				= "";
			$TS_TablesWP_Editor_FileName				= "";
			$TS_TablesWP_Editor_FileSource				= "";
			$TS_TablesWP_Editor_FileType				= "";	
		}
		if (($TS_TablesWP_Editor_FileName != "") && ($TS_TablesWP_WordPress_FOpen)) {			
			if ($TS_TablesWP_Editor_FileType == 'text/plain') {
				$TS_TablesWP_Editor_FilePassed			= true;
			} else {
				$TS_TablesWP_Editor_FilePassed			= false;
			}
			if ($TS_TablesWP_Editor_FilePassed) {
				$TS_TablesWP_Editor_FileData			= file_get_contents($TS_TablesWP_Editor_FileSource);
				$TS_TablesWP_Editor_Import				= json_decode(base64_decode($TS_TablesWP_Editor_FileData));
			} else {
				echo'<script>window.location="' . admin_url('admin.php?page=TS_TablesWP_Tables') . '";</script>';
				exit;
			}
		} else if ($TS_TablesWP_Editor_Import != "") {
			$TS_TablesWP_Editor_Import					= json_decode(base64_decode($TS_TablesWP_Editor_Import));
		} else {
			echo'<script>window.location="' . admin_url('admin.php?page=TS_TablesWP_Tables') . '";</script>';
			Exit();
		}
		// Process Import Data
		if ((!isset($TS_TablesWP_Editor_Import->data)) || (!isset($TS_TablesWP_Editor_Import->name))) {
			echo'<script>window.location="' . admin_url('admin.php?page=TS_TablesWP_Tables') . '";</script>';
			Exit();
		} else {
			// Generate Import Table ID
			$TS_TablesWP_Editor_Tables          		= array();
			$TS_TablesWP_Editor_Missing					= array();
			$TS_TablesWP_Editor_Name            		= "";
			if (isset($TS_TablesWP_Editor_Import->number)) {
				$TS_TablesWP_Editor_Number          	= $TS_TablesWP_Editor_Import->number;
			} else {
				$TS_TablesWP_Editor_Number          	= "";
			}
			if (count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables) == 0) {			
				if ($TS_TablesWP_Editor_Number != "") {
					$TS_TablesWP_Editor_ID          	= $TS_TablesWP_Editor_Number;
				} else {
					$TS_TablesWP_Editor_ID          	= 1;
				}
			} else {
				foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables as $tables => $table) {
					array_push($TS_TablesWP_Editor_Tables, $table['id']);
				}
				for ($i = 1; $i < max($TS_TablesWP_Editor_Tables); $i++) {
					if (!in_array($i, $TS_TablesWP_Editor_Tables)) {
						array_push($TS_TablesWP_Editor_Missing, $i);
					}
				}
				if (($TS_TablesWP_Editor_Number != "") && (in_array($TS_TablesWP_Editor_Number, $TS_TablesWP_Editor_Missing)) && ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_ReuseIDs == true)) {
					$TS_TablesWP_Editor_ID				= (string)$TS_TablesWP_Editor_Number;
				} else if ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_ReuseIDs == true) {
					if (count($TS_TablesWP_Editor_Missing) > 0) {
						$TS_TablesWP_Editor_ID			= min($TS_TablesWP_Editor_Missing);
					} else {
						$TS_TablesWP_Editor_ID			= max($TS_TablesWP_Editor_Tables) + 1;
					}
				} else {
					$TS_TablesWP_Editor_ID				= max($TS_TablesWP_Editor_Tables) + 1;
				}
			}
			$TS_TablesWP_Editor_ID						= (string)$TS_TablesWP_Editor_ID;
			$TS_TablesWP_Editor_Import->number			= $TS_TablesWP_Editor_ID;
			// Retrieve Table Data
			if (isset($TS_TablesWP_Editor_Import->info)) {
				$TS_TablesWP_Editor_Info				= $TS_TablesWP_Editor_Import->info;
			} else {
				$TS_TablesWP_Editor_Info				= "";
			}
			if (isset($TS_TablesWP_Editor_Import->name)) {
				$TS_TablesWP_Editor_Name				= $TS_TablesWP_Editor_Import->name;
			} else {
				$TS_TablesWP_Editor_Name				= "";
			}
			if (isset($TS_TablesWP_Editor_Import->rows)) {
				$TS_TablesWP_Editor_Rows				= $TS_TablesWP_Editor_Import->rows;
			} else {
				$TS_TablesWP_Editor_Rows				= 0;
			}
			if (isset($TS_TablesWP_Editor_Import->cols)) {
				$TS_TablesWP_Editor_Columns				= $TS_TablesWP_Editor_Import->cols;
			} else {
				$TS_TablesWP_Editor_Columns				= 0;
			}
			if (isset($TS_TablesWP_Editor_Import->defaults)) {
				$TS_TablesWP_Editor_Defaults			= json_encode(json_decode($TS_TablesWP_Editor_Import->defaults));
			} else {
				$TS_TablesWP_Editor_Defaults			= json_encode(new stdClass());
			}
			if (isset($TS_TablesWP_Editor_Import->merged)) {
				$TS_TablesWP_Editor_Merged				= json_encode(json_decode($TS_TablesWP_Editor_Import->merged));
			} else {
				$TS_TablesWP_Editor_Merged				= json_encode(array());
			}
			if (isset($TS_TablesWP_Editor_Import->data)) {
				$TS_TablesWP_Editor_Data				= $TS_TablesWP_Editor_Import->data; //json_encode(json_decode($TS_TablesWP_Editor_Import->data));
			} else {
				$TS_TablesWP_Editor_Data				= ""; //json_encode(array());
			}
			if (isset($TS_TablesWP_Editor_Import->meta)) {
				$TS_TablesWP_Editor_Meta				= json_encode(json_decode($TS_TablesWP_Editor_Import->meta));
			} else {
				$TS_TablesWP_Editor_Meta				= json_encode(array());
			}
			if (isset($TS_TablesWP_Editor_Import->other)) {
				$TS_TablesWP_Editor_Other				= json_encode(json_decode($TS_TablesWP_Editor_Import->other));
			} else {
				$TS_TablesWP_Editor_Other				= json_encode(array());
			}
			// Add Imported Table to Database
			$wpdb->insert($wpdb->prefix . "ts_advancedtables", array(
				"number" 								=> $TS_TablesWP_Editor_ID,
				"name" 									=> $TS_TablesWP_Editor_Name,
				"cols" 									=> $TS_TablesWP_Editor_Columns,
				"rows" 									=> $TS_TablesWP_Editor_Rows,
				"created"								=> date("Y-m-d H:i:s", $TS_TablesWP_Editor_Date),
				"info" 									=> $TS_TablesWP_Editor_Info,
				"merged" 								=> $TS_TablesWP_Editor_Merged,
				"defaults" 								=> $TS_TablesWP_Editor_Defaults,
				"data" 									=> $TS_TablesWP_Editor_Data,
				"meta" 									=> $TS_TablesWP_Editor_Meta,
				"other" 								=> $TS_TablesWP_Editor_Other,			
			));
			// Add Imported Table for Basic Listing
			$TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables["table" . $TS_TablesWP_Editor_ID] = array(
				"id"                        			=> $TS_TablesWP_Editor_ID,
				"name"                      			=> $TS_TablesWP_Editor_Name,
				"create"                    			=> $TS_TablesWP_Editor_Date,
				"update"                    			=> $TS_TablesWP_Editor_Date,
				"info"                      			=> base64_encode($TS_TablesWP_Editor_Info),			
				"rows"									=> $TS_TablesWP_Editor_Rows,
				"columns"								=> $TS_TablesWP_Editor_Columns,
				"merged"								=> $TS_TablesWP_Editor_Merged,
				"charts"								=> "false",
				"categories"							=> "",
				"tags"									=> "",
			);
			update_option("ts_tablesplus_extend_settings_tables", $TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables);
			// Update Global Table ID Collector
			array_push($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_UsedIDs, $TS_TablesWP_Editor_ID);
			update_option("ts_tablesplus_extend_settings_usedids", $TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_UsedIDs);
			// Redirect to Listing Page
			echo'<script>window.location="' . admin_url('admin.php?page=TS_TablesWP_Tables') . '";</script>';
			Exit();
		}
	}
?>
	<div class="ts-vcsc-settings-page-header">
		<h2><span class="dashicons dashicons-grid-view"></span><?php echo __("Tablenator - Advanced Tables for WordPress", "ts_visual_composer_extend"); ?> v<?php echo TS_TablesWP_GetPluginVersion(); ?> ... <?php echo __("Import Table", "ts_visual_composer_extend"); ?></h2>
		<div class="clear"></div>
	</div>
	<form id="ts-advanced-table-migrate-form" class="ts-advanced-table-migrate-form" name="ts-advanced-table-migrate-form" autocomplete="off" enctype="multipart/form-data" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<div id="ts-advanced-table-import-preloader-wrapper">
			<?php
				echo '<div id="ts-advanced-table-import-preloader-holder">';
					echo TS_TablesWP_CreatePreloaderCSS("ts-advanced-table-import-preloader-type", "", 11, "false");
				echo '</div>';  
			?>
		</div>
		<div class="ts-settings-import-links" style="border-bottom: 1px dashed #cccccc; display: block; width: 100%; margin: 5px 0 20px;">
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
		<img id="ts-advanced-table-import-banner" style="display: block; width: 100%; max-width: 800px; height: auto; margin: 0px auto 0px auto;" src="<?php echo TS_TablesWP_GetResourceURL('images/banners/banner_import.jpg'); ?>">
		<div style="display: block; width: 100%;">
			<div class="ts-vcsc-notice-field ts-vcsc-warning" style="display: <?php echo ($TS_TablesWP_WordPress_FOpen ? "block" : "none"); ?>; margin-top: 0px; font-size: 13px; text-align: justify;">
				<?php echo __("You can import a table by either using the clipboard function to paste the data directly into a provided textarea, or by uploading the exported .txt file containing the table data via a provided upload button. Please use the select option below to define how you want to import your table. After the import routine has been completed, the browser will automatically redirect to the listing page for all tables.", "ts_visual_composer_extend"); ?>
			</div>
			<div class="ts-vcsc-notice-field ts-vcsc-warning" style="display: <?php echo ($TS_TablesWP_WordPress_FOpen ? "none" : "block"); ?>; margin-top: 0px; font-size: 13px; text-align: justify;">
				<?php echo __("You can import a table by using the clipboard function to paste the data directly into the textarea provided below. After the import routine has been completed, the browser will automatically redirect to the listing page for all tables.", "ts_visual_composer_extend"); ?>
			</div>
			<div style="display: <?php echo ($TS_TablesWP_WordPress_FOpen ? "block" : "none"); ?>; margin-bottom: 20px;">
				<select id="ts-advanced-table-import-routine" class="ts-advanced-table-import-routine">
					<option value="file" selected="selected"><?php echo __("Import Table via TXT File", "ts_visual_composer_extend"); ?></option>
					<option value="copy"><?php echo __("Import Table via Clipboard", "ts_visual_composer_extend"); ?></option>
				</select>
			</div>			
			<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
				<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to import the table based on the the selected .txt file or the data in the textarea below.", "ts_visual_composer_extend"); ?></span>
				<button id="ts-advanced-table-import-button" class="ts-advanced-tables-button-main ts-advanced-tables-button-orange ts-advanced-tables-button-update" type="submit" name="Import" disabled="disabled" style="margin: 0 0 20px 0;">
					<?php echo __("Import Table Data", "ts_visual_composer_extend"); ?>
				</button>
			</div>
			<div id="ts-advanced-table-text-upload" class="ts-advanced-table-text-upload" style="display: <?php echo ($TS_TablesWP_WordPress_FOpen ? "block" : "none"); ?>; margin-top: 0px; margin-bottom: 20px;">
				<div id="ts-advanced-table-text-box" class="ts-advanced-table-text-box">
					<input type="file" accept=".txt" name="ts-advanced-table-text-file" id="ts-advanced-table-text-file" class="ts-advanced-table-text-file"/>
					<label id="ts-advanced-table-text-label" class="ts-advanced-table-text-label" for="ts-advanced-table-text-file" style="display: inline-block;">
						<span id="ts-advanced-table-text-name" class="ts-advanced-table-text-name"></span>
						<span id="ts-advanced-table-text-select" class="ts-advanced-table-text-select ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
							<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to select the text file to be imported from your computer.", "ts_visual_composer_extend"); ?></span>
							<span class="ts-advanced-tables-button-main ts-advanced-tables-button-red ts-advanced-tables-button-text" style="margin: 0;">
								<?php echo __("Select Text File", "ts_visual_composer_extend"); ?>
							</span>
						</span>
					</label>
				</div>
			</div>
			<textarea id="ts-advanced-table-import-content" class="ts-advanced-table-import-content" name="ts-advanced-table-import-content" style="display: <?php echo ($TS_TablesWP_WordPress_FOpen ? "none" : "block"); ?>; margin-bottom: 10px;"></textarea>			
		</div>
	</form>