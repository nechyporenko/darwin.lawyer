<?php
    global $TS_ADVANCED_TABLESWP;
	global $wpdb;
    
	// Check if Database Table Exists
	$TS_TablesWP_DatabaseCheck          = TS_TablesWP_Datebase_TableCheck();
    $TS_TablesWP_DatabaseName		    = $wpdb->prefix . TABLESWP_MYSQL;
	
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
	$TS_TablesWP_Editor_Formulas		= "false";
	$TS_TablesWP_Editor_Search			= "false";
	$TS_TablesWP_Editor_Charts			= "false";
	
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

?>
<div id="ts-advanced-table-tableblank-wrapper" class="ts-advanced-table-tableblank-wrapper" style="display: none;" data-type="<?php echo ($TS_TablesWP_DatabaseCheck == true ? "info" : "error"); ?>" data-title="<?php echo ($TS_TablesWP_DatabaseCheck == true ? __("Initial Table Grid", "ts_visual_composer_extend") : __("SQL Database Table Missing", "ts_visual_composer_extend")); ?>" data-confirm="<?php echo ($TS_TablesWP_DatabaseCheck == true ? __("Create Table", "ts_visual_composer_extend") : __("Go To Maintenance", "ts_visual_composer_extend")); ?>" data-cancel="<?php echo __("Cancel", "ts_visual_composer_extend"); ?>" data-yes="<?php echo __("Yes", "ts_visual_composer_extend"); ?>" data-no="<?php echo __("No", "ts_visual_composer_extend"); ?>" data-other="<?php echo __("Understood!", "ts_visual_composer_extend"); ?>">
	<div id="ts-advanced-table-tableblank-container" class="ts-advanced-table-tableblank-container">
		<div class="ts-vcsc-notice-field ts-vcsc-<?php echo ($TS_TablesWP_DatabaseCheck == true ? __("info", "ts_visual_composer_extend") : __("critical", "ts_visual_composer_extend")); ?>" style="margin-top: 0px; margin-bottom: 20px; font-size: 14px; text-align: justify;">
			<?php
                if ($TS_TablesWP_DatabaseCheck == true) {
                    echo __("Please define the number of rows and columns your table should initially start out with. You can always add or remove (more) rows and columns later.", "ts_visual_composer_extend");
                } else {
                    echo sprintf(_("It appears that the required SQL database table '%s' to store all the data for each HTML table created through this plugin is missing."), $TS_TablesWP_DatabaseName);
					echo "<br/><br/>" . __("Usually, the missing table should have been created automatically when you first activated this plugin, however, for some reason, that process must have failed.", "ts_visual_composer_extend");
                    echo "<br/><br/>" . __("Please go to the maintenance page in order to attempt to create the missing table one more time.", "ts_visual_composer_extend");
                }
            ?>
		</div>
		<div id="ts-advanced-table-tableblank-rows" class="ts-advanced-table-tableblank-rows" style="display: <?php echo ($TS_TablesWP_DatabaseCheck == true ? "block" : "none"); ?>;">
			<label class="ts-advanced-table-tableblank-label" for="ts-advanced-table-tableblank-rows-input"><?php echo __("Rows:", "ts_visual_composer_extend"); ?></label>
			<input type="number" id="ts-advanced-table-tableblank-rows-input" class="ts-advanced-table-tableblank-input" name="ts-advanced-table-tableblank-rows-input" min="4" max="1000" step="1" required="true" value="10">
		</div>
		<div id="ts-advanced-table-tableblank-columns" class="ts-advanced-table-tableblank-columns" style="display: <?php echo ($TS_TablesWP_DatabaseCheck == true ? "block" : "none"); ?>;">
			<label class="ts-advanced-table-tableblank-label" for="ts-advanced-table-tableblank-columns-input"><?php echo __("Columns:", "ts_visual_composer_extend"); ?></label>
			<input type="number" id="ts-advanced-table-tableblank-columns-input" class="ts-advanced-table-tableblank-input" name="ts-advanced-table-tableblank-columns-input" min="2" max="20" step="1" required="true" value="5">
		</div>
		<div id="ts-advanced-table-tableblank-links" class="ts-advanced-table-tableblank-links" style="display: none;">
            <input id="ts-advanced-table-tableblank-link-maintain" class="ts-advanced-table-tableblank-link-maintain" type="hidden" style="display: none;" value="<?php echo admin_url('admin.php?page=TS_TablesWP_Maintain'); ?>">
			<input id="ts-advanced-table-tableblank-link-redo" class="ts-advanced-table-tableblank-link-redo" type="hidden" style="display: none;" value="<?php echo admin_url('admin.php?page=TS_TablesWP_AddNew'); ?>">
			<input id="ts-advanced-table-tableblank-link-valid" class="ts-advanced-table-tableblank-link-valid" type="hidden" style="display: none;" value="<?php echo admin_url('admin.php?page=TS_TablesWP_AddNew&rows=%d&columns=%d'); ?>">
			<input id="ts-advanced-table-tableblank-link-cancel" class="ts-advanced-table-tableblank-link-cancel" type="hidden" style="display: none;" value="<?php echo admin_url('admin.php?page=TS_TablesWP_Tables'); ?>">
		</div>
	</div>
</div>