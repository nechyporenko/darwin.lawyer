<?php
    global $TS_ADVANCED_TABLESWP;
	global $wpdb;
	
	// Retrieve Pre-Defined Cats + Tags
	$TS_TablesWP_Define_Cats			= get_option("ts_tablesplus_extend_settings_categories", '');
	$TS_TablesWP_Define_Cats			= rawurldecode($TS_TablesWP_Define_Cats);
	$TS_TablesWP_Define_Cats			= json_decode($TS_TablesWP_Define_Cats);
	if ((!is_array($TS_TablesWP_Define_Cats)) || ($TS_TablesWP_Define_Cats == '')) {
		$TS_TablesWP_Define_Cats 		= array();
	}
	$TS_TablesWP_Define_Tags			= array();
	
	// General Variables
    $TS_TablesWP_Editor_Tables          = array();
	$TS_TablesWP_Editor_Missing			= array();
    $TS_TablesWP_Editor_Name            = "";
    if (count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables) == 0) {
        $TS_TablesWP_Editor_ID          = 1;
    } else {
        foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables as $tables => $table) {
            array_push($TS_TablesWP_Editor_Tables, $table['id']);
        }
		for ($i = 1; $i < max($TS_TablesWP_Editor_Tables); $i++) {
			if (!in_array($i, $TS_TablesWP_Editor_Tables)) {
				array_push($TS_TablesWP_Editor_Missing, $i);
			}
		}
        if ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_ReuseIDs == true) {
            if (count($TS_TablesWP_Editor_Missing) > 0) {
				$TS_TablesWP_Editor_ID	= min($TS_TablesWP_Editor_Missing);
			} else {
				$TS_TablesWP_Editor_ID	= max($TS_TablesWP_Editor_Tables) + 1;
			}
        } else {
            $TS_TablesWP_Editor_ID		= max($TS_TablesWP_Editor_Tables) + 1;
        }
    }
    $TS_TablesWP_Editor_Action          = "new";
    $TS_TablesWP_Editor_Date            = current_time('timestamp', 0);
    $TS_TablesWP_Editor_Import          = "";
    $TS_TablesWP_Editor_Info            = "";
	$TS_TablesWP_Editor_Data			= json_encode(array());
	$TS_TablesWP_Editor_Meta			= json_encode(array());
	$TS_TablesWP_Editor_Merged			= json_encode(array());
	$TS_TablesWP_Editor_Defaults		= json_encode(array());
	$TS_TablesWP_Editor_Other			= json_encode(array());
	$TS_TablesWP_Editor_SaveMeta		= "true";
	$TS_TablesWP_Editor_Formulas		= "false";
	$TS_TablesWP_Editor_Search			= "false";
	$TS_TablesWP_Editor_Context			= "true";
	$TS_TablesWP_Editor_Validator		= "true";
	$TS_TablesWP_Editor_Charts			= "false";
	$TS_TablesWP_Editor_External		= "false";
	$TS_TablesWP_Editor_Path			= "";
	$TS_TablesWP_Editor_FixRow			= "false";
	$TS_TablesWP_Editor_FixColumn		= "false";
	$TS_TablesWP_Editor_Cats			= array();
	$TS_TablesWP_Editor_Tags			= array();
	$TS_TablesWP_Editor_Section			= 1;
	
	// New Table Row + Column Count
	if (isset($_GET["rows"]) && isset($_GET["columns"])) {
		$TS_TablesWP_Editor_Render		= "true";
	} else {
		$TS_TablesWP_Editor_Render		= "false";
	}
    $TS_TablesWP_Editor_NewRows			= (isset($_GET["rows"]) ? $_GET["rows"] : 10);
    if ($TS_TablesWP_Editor_NewRows < 4) {
        $TS_TablesWP_Editor_NewRows		= 4;
    }
	$TS_TablesWP_Editor_Rows			= $TS_TablesWP_Editor_NewRows;
    $TS_TablesWP_Editor_NewColumns		= (isset($_GET["columns"]) ? $_GET["columns"] : 5);
    if ($TS_TablesWP_Editor_NewColumns < 2) {
        $TS_TablesWP_Editor_NewColumns	= 2;
    }
	$TS_TablesWP_Editor_Columns			= $TS_TablesWP_Editor_NewColumns;

	// Table Save Routine
    if (isset($_POST['Save'])) {
        // Render Preloader Animation
        echo '<div id="ts_vcsc_extend_settings_save" style="position: relative; margin: 20px auto 20px auto; width: 128px; height: 128px;">';
            echo TS_TablesWP_CreatePreloaderCSS("ts-settings-panel-loader", "", 6, "false");
        echo '</div>';
        // Retrieve Form Contents
        $TS_TablesWP_Editor_ID          = trim ($_POST['ts-advanced-table-number-input']);
        $TS_TablesWP_Editor_Name        = trim ($_POST['ts-advanced-table-name-input']);
        $TS_TablesWP_Editor_Date        = trim ($_POST['ts-advanced-table-date-stamp']);	
		$TS_TablesWP_Editor_Rows      	= trim ($_POST['ts-advanced-table-transfer-rows']);
		$TS_TablesWP_Editor_Columns		= trim ($_POST['ts-advanced-table-transfer-columns']);
		$TS_TablesWP_Editor_Merged      = trim ($_POST['ts-advanced-table-transfer-merged']);		
        $TS_TablesWP_Editor_Info        = trim ($_POST['ts-advanced-table-info-input']);
		$TS_TablesWP_Editor_Defaults	= trim ($_POST['ts-advanced-table-transfer-defaults']);
		$TS_TablesWP_Editor_Data		= trim ($_POST['ts-advanced-table-transfer-data']);
		$TS_TablesWP_Editor_Meta		= trim ($_POST['ts-advanced-table-transfer-meta']);
		$TS_TablesWP_Editor_Other		= trim ($_POST['ts-advanced-table-transfer-other']);
		$TS_TablesWP_Editor_Cats		= trim ($_POST['ts-advanced-table-transfer-cats']);
		$TS_TablesWP_Editor_Tags		= trim ($_POST['ts-advanced-table-transfer-tags']);
		$TS_TablesWP_Editor_ID			= (string)$TS_TablesWP_Editor_ID;
		// Add New Table to Database
		$wpdb->insert($wpdb->prefix . "ts_advancedtables", array(
			"number" 					=> $TS_TablesWP_Editor_ID,
			"name" 						=> $TS_TablesWP_Editor_Name,
			"cols" 					    => $TS_TablesWP_Editor_Columns,
			"rows" 					    => $TS_TablesWP_Editor_Rows,
			"created"					=> date("Y-m-d H:i:s", current_time('timestamp', 0)),
			"merged" 					=> stripcslashes($TS_TablesWP_Editor_Merged),
			"defaults" 					=> stripcslashes($TS_TablesWP_Editor_Defaults),
			"info" 						=> stripcslashes($TS_TablesWP_Editor_Info),
			"data" 						=> stripcslashes($TS_TablesWP_Editor_Data),
			"meta" 						=> stripcslashes($TS_TablesWP_Editor_Meta),
			"other" 					=> stripcslashes($TS_TablesWP_Editor_Other),
		));
		// Add New Table for Basic Listing
        $TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables["table" . $TS_TablesWP_Editor_ID] = array(
            "id"                        => $TS_TablesWP_Editor_ID,
            "name"                      => $TS_TablesWP_Editor_Name,
            "create"                    => intval($TS_TablesWP_Editor_Date),
            "update"                    => intval($TS_TablesWP_Editor_Date),
            "info"                      => base64_encode($TS_TablesWP_Editor_Info),			
			"rows"						=> $TS_TablesWP_Editor_Rows,
			"columns"					=> $TS_TablesWP_Editor_Columns,
			"merged"					=> $TS_TablesWP_Editor_Merged,
			"charts"					=> "false",
			"categories"				=> $TS_TablesWP_Editor_Cats,
			"tags"						=> $TS_TablesWP_Editor_Tags,
        );
        update_option("ts_tablesplus_extend_settings_tables", $TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables);
        // Update Global Table ID Collector
        array_push($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_UsedIDs, $TS_TablesWP_Editor_ID);
        update_option("ts_tablesplus_extend_settings_usedids", $TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_UsedIDs);
        // Redirect to Listing Page
        if ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_SaveRedirect == true) { 
			echo'<script>window.location="' . admin_url('admin.php?page=TS_TablesWP_Tables') . '";</script>';
        } else {
			echo'<script>window.location="' . admin_url('admin.php?page=TS_TablesWP_Modify&tableid=' . $TS_TablesWP_Editor_ID . '&action=edit') . '";</script>';
        }
		Exit();
    }
?>
	<div class="ts-vcsc-settings-page-header">
		<h2><span class="dashicons dashicons-editor-table"></span><?php echo __("Tablenator - Advanced Tables for WordPress", "ts_visual_composer_extend"); ?> v<?php echo TS_TablesWP_GetPluginVersion(); ?> ... <?php echo __("Add New Table", "ts_visual_composer_extend"); ?></h2>
		<div class="clear"></div>
	</div>
	<form id="ts-advanced-table-edit-form" class="ts-advanced-table-edit-form" name="ts-advanced-table-edit-form" autocomplete="off" method="post" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>">
        <span id="ts-advanced-table-check-wrapper" style="display: none !important; margin-bottom: 20px;">
            <input type="text" style="width: 20%;" id="ts-advanced-table-check-true" name="ts-advanced-table-check-true" value="0" size="100">
        </span>
		<div id="ts-advanced-table-controls-wrapper" class="ts-advanced-table-controls-wrapper">
			<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder" style="margin: 0 20px 0 0;">
				<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to abandon all changes and return to the table listings page.", "ts_visual_composer_extend"); ?></span>
				<a href="<?php echo admin_url('admin.php?page=TS_TablesWP_Tables'); ?>" target="_parent" class="ts-advanced-tables-button-main ts-advanced-tables-button-turquoise ts-advanced-tables-button-table" style="margin: 0;">
					<?php echo __("Back to Listing", "ts_visual_composer_extend"); ?>
				</a>
			</div>
			<?php
				if (current_user_can('manage_options')) {
					echo '<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder" style="margin: 0 20px 0 0;">
						<span class="ts-advanced-table-tooltip-content">' . __("Click here to abandon all changes and return to the plugins settings page.", "ts_visual_composer_extend") . '</span>
						<a href="' . admin_url('admin.php?page=TS_TablesWP_Settings') . '" target="_parent" class="ts-advanced-tables-button-main ts-advanced-tables-button-grey ts-advanced-tables-button-settings">'. __("Back to Settings", "ts_visual_composer_extend") . '</a>
					</div>';
				}
			?>
			<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder ts-advanced-table-tooltip-right" style="float: right; margin: 0 0 0 20px;">
				<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to save your table.", "ts_visual_composer_extend"); ?></span>
				<button id="ts-advanced-table-save-button" class="ts-advanced-tables-button-main ts-advanced-tables-button-blue ts-advanced-tables-button-save" style="margin: 0;">
					<?php echo __("Save Table", "ts_visual_composer_extend"); ?>
				</button>
			</div>
            <div class="ts-advanced-tables-button-wrapper" style="display: none;">
                <button id="ts-advanced-table-trigger-button" type="submit" name="Save"><?php echo __("Save Table", "ts_visual_composer_extend"); ?></button>
            </div>
		</div>
		<?php
			// Include Table Editor
			include($TS_ADVANCED_TABLESWP->assets_dir . 'ts_tableswp_assets_editor.php');
		?>
	</form>