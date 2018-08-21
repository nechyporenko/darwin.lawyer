<?php
    global $TS_ADVANCED_TABLESWP;
	global $wpdb;
	
	// Retrieve Pre-Defined Cats + Tags
	$TS_TablesWP_Define_Cats				= get_option("ts_tablesplus_extend_settings_categories", '');
	$TS_TablesWP_Define_Cats				= rawurldecode($TS_TablesWP_Define_Cats);	
	$TS_TablesWP_Define_Copy				= $TS_TablesWP_Define_Cats;
	$TS_TablesWP_Define_Cats				= json_decode($TS_TablesWP_Define_Cats);
	$TS_TablesWP_Define_Copy				= json_decode($TS_TablesWP_Define_Copy);
	if ((!is_array($TS_TablesWP_Define_Cats)) || ($TS_TablesWP_Define_Cats == '')) {
		$TS_TablesWP_Define_Cats 			= array();
	}
	if ((!is_array($TS_TablesWP_Define_Copy)) || ($TS_TablesWP_Define_Copy == '')) {
		$TS_TablesWP_Define_Copy 			= array();
	}
	$TS_TablesWP_Define_Tags				= array();
	
	// Flatten Pre-Defined Categories
	if (count($TS_TablesWP_Define_Copy) > 0) {
		$TS_TablesWP_Define_Copy 			= TS_TablesWP_FlattenObject($TS_TablesWP_Define_Copy);
	}
	
	// General Variables
    $TS_TablesWP_Editor_ID              	= $_GET['tableid'];
	$TS_TablesWP_Editor_ID					= (string)$TS_TablesWP_Editor_ID;
	$TS_TablesWP_Editor_Table  				= $wpdb->prefix . "ts_advancedtables";
	$TS_TablesWP_Editor_Section				= 0;
	
	// Domain Variables
    $TS_TablesWP_Editor_Action          	= $_GET['action'];
	$TS_TablesWP_Editor_Page          		= $_GET['page'];
	$TS_TablesWP_Editor_URI					= str_replace( '%7E', '~', strtok($_SERVER["REQUEST_URI"], '?'));
	$TS_TablesWP_Editor_Target				= $TS_TablesWP_Editor_URI . '?page=' . $TS_TablesWP_Editor_Page . '&tableid=' . $TS_TablesWP_Editor_ID . '&action=';
	
	// New Table Row + Column Count
    $TS_TablesWP_Editor_NewRows				= (isset($_GET["rows"]) ? $_GET["rows"] : 10);
    if ($TS_TablesWP_Editor_NewRows < 4) {
        $TS_TablesWP_Editor_NewRows			= 4;
    }
    $TS_TablesWP_Editor_NewColumns			= (isset($_GET["columns"]) ? $_GET["columns"] : 5);
    if ($TS_TablesWP_Editor_NewColumns < 2) {
        $TS_TablesWP_Editor_NewColumns		= 2;
    }
    
    // Process Based on Action
    if ($TS_TablesWP_Editor_Action == "clone") {
        // Render Preloader Animation
        echo '<div id="ts_vcsc_extend_settings_save" style="position: relative; margin: 20px auto 20px auto; width: 128px; height: 128px;">';
            echo TS_TablesWP_CreatePreloaderCSS("ts-settings-panel-loader", "", 2, "false");
        echo '</div>';
		// Get Cloned Table Name + Date
        $TS_TablesWP_Editor_Name			= (isset($_GET["newname"]) ? base64_decode($_GET['newname']) : "");
		$TS_TablesWP_Editor_Date			= current_time('timestamp', 0);
		// Retrieve Table from Database
		$TS_TablesWP_Editor_BaseName		= $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginMYSQL;
		$TS_TablesWP_Editor_BaseData		= $wpdb->get_row("SELECT * FROM $TS_TablesWP_Editor_BaseName WHERE `number` = $TS_TablesWP_Editor_ID");
		// Get New ID for Cloned Table
		$TS_TablesWP_Editor_Tables          = array();
		$TS_TablesWP_Editor_Missing			= array();
		if (count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables) == 0) {
			$TS_TablesWP_Editor_ID			= 1;
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
		$TS_TablesWP_Editor_ID				= (string)$TS_TablesWP_Editor_ID;
        $TS_TablesWP_Editor_Target          = $TS_TablesWP_Editor_URI . '?page=' . $TS_TablesWP_Editor_Page . '&tableid=' . $TS_TablesWP_Editor_ID . '&action=';
		// Retrieve Old Table Data
		if ($TS_TablesWP_Editor_BaseData == null) {
			$TS_TablesWP_Editor_BaseData	= array();
		}
		if (isset($TS_TablesWP_Editor_BaseData->info)) {
			$TS_TablesWP_Editor_Info		= $TS_TablesWP_Editor_BaseData->info;
		} else {
			$TS_TablesWP_Editor_Info		= "";
		}
		if (isset($TS_TablesWP_Editor_BaseData->rows)) {
			$TS_TablesWP_Editor_Rows		= $TS_TablesWP_Editor_BaseData->rows;
		} else {
			$TS_TablesWP_Editor_Rows		= 0;
		}
		if (isset($TS_TablesWP_Editor_BaseData->cols)) {
			$TS_TablesWP_Editor_Columns		= $TS_TablesWP_Editor_BaseData->cols;
		} else {
			$TS_TablesWP_Editor_Columns		= 0;
		}
		if (isset($TS_TablesWP_Editor_BaseData->data)) {
			$TS_TablesWP_Editor_Data		= $TS_TablesWP_Editor_BaseData->data;
		} else {
			$TS_TablesWP_Editor_Data		= json_encode(array());
		}
		if (isset($TS_TablesWP_Editor_BaseData->meta)) {
			$TS_TablesWP_Editor_Meta		= $TS_TablesWP_Editor_BaseData->meta;
		} else {
			$TS_TablesWP_Editor_Meta		= json_encode(array());
		}
		if (isset($TS_TablesWP_Editor_BaseData->defaults)) {
			$TS_TablesWP_Editor_Defaults	= $TS_TablesWP_Editor_BaseData->defaults;
		} else {
			$TS_TablesWP_Editor_Defaults	= json_encode(array());
		}
		if (isset($TS_TablesWP_Editor_BaseData->merged)) {
			$TS_TablesWP_Editor_Merged		= $TS_TablesWP_Editor_BaseData->merged;
		} else {
			$TS_TablesWP_Editor_Merged		= json_encode(array());
		}
		if (isset($TS_TablesWP_Editor_BaseData->other)) {
			$TS_TablesWP_Editor_Other		= $TS_TablesWP_Editor_BaseData->other;
		} else {
			$TS_TablesWP_Editor_Other		= json_encode(array());
		}
		// Add New Table to Database
		$wpdb->insert($wpdb->prefix . "ts_advancedtables", array(
			"number" 						=> $TS_TablesWP_Editor_ID,
			"name" 							=> $TS_TablesWP_Editor_Name,
			"cols" 						    => $TS_TablesWP_Editor_Columns,
			"rows" 						    => $TS_TablesWP_Editor_Rows,
			"created"						=> date("Y-m-d H:i:s", $TS_TablesWP_Editor_Date),
			"merged" 						=> $TS_TablesWP_Editor_Merged,
			"defaults" 						=> $TS_TablesWP_Editor_Defaults,
			"info" 							=> $TS_TablesWP_Editor_Info,
			"data" 							=> $TS_TablesWP_Editor_Data,
			"meta" 							=> $TS_TablesWP_Editor_Meta,
			"other" 						=> $TS_TablesWP_Editor_Other,			
		));
		// Add Cloned Table to Basic Listing
        $TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables["table" . $TS_TablesWP_Editor_ID] = array(
            "id"                        	=> $TS_TablesWP_Editor_ID,
            "name"                      	=> $TS_TablesWP_Editor_Name,
            "create"                    	=> intval($TS_TablesWP_Editor_Date),
            "update"                    	=> intval($TS_TablesWP_Editor_Date),
            "info"                      	=> base64_encode($TS_TablesWP_Editor_Info),			
			"rows"							=> $TS_TablesWP_Editor_Rows,
			"columns"						=> $TS_TablesWP_Editor_Columns,
			"merged"						=> $TS_TablesWP_Editor_Merged,
			"charts"						=> "false",
			"categories"					=> "",
			"tags"							=> "",
        );
        update_option("ts_tablesplus_extend_settings_tables", $TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables);
        // Redirect to Listing Page
        if ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_SaveRedirect == true) { 
			echo'<script>window.location="' . admin_url('admin.php?page=TS_TablesWP_Tables') . '";</script>';
        } else {
			echo'<script>window.location="' . $TS_TablesWP_Editor_Target . 'edit' . '";</script>';
		}
		Exit();
    } else if ($TS_TablesWP_Editor_Action == "delete") {
        // Render Preloader Animation
        echo '<div id="ts_vcsc_extend_settings_save" style="position: relative; margin: 20px auto 20px auto; width: 128px; height: 128px;">';
            echo TS_TablesWP_CreatePreloaderCSS("ts-settings-panel-loader", "", 16, "false");
        echo '</div>';
        // Remove Table Reference
        unset($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables['table' . $TS_TablesWP_Editor_ID]);
        update_option("ts_tablesplus_extend_settings_tables", $TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables);
        // Adjust Global Table ID Collector
        unset($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_UsedIDs[$TS_TablesWP_Editor_ID]);
        update_option("ts_tablesplus_extend_settings_usedids", $TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_UsedIDs);
		// Remove Table from Database
		$wpdb->delete($wpdb->prefix . "ts_advancedtables",
			array(
				'number' 					=> $TS_TablesWP_Editor_ID
			), array('%d')
		);
		$TS_TablesWP_Editor_Optimize 		= $wpdb->query("OPTIMIZE TABLE $TS_TablesWP_Editor_Table");
        // Redirect to Listing Page       
		echo'<script>window.location="' . admin_url('admin.php?page=TS_TablesWP_Tables') . '";</script>';
		Exit();
    } else if ($TS_TablesWP_Editor_Action == "edit") {
		// Retrieve Table from Database
		$TS_TablesWP_Editor_BaseName		= $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginMYSQL;
		$TS_TablesWP_Editor_BaseData		= $wpdb->get_row("SELECT * FROM $TS_TablesWP_Editor_BaseName WHERE `number` = $TS_TablesWP_Editor_ID");
		$TS_TablesWP_Editor_Validated		= "false";
		if ($TS_TablesWP_Editor_BaseData == null) {
			$TS_TablesWP_Editor_BaseData	= array();
		}
		if (isset($TS_TablesWP_Editor_BaseData->name)) {
			$TS_TablesWP_Editor_Name		= $TS_TablesWP_Editor_BaseData->name;
		} else {
			$TS_TablesWP_Editor_Name		= "";
		}
		if (isset($TS_TablesWP_Editor_BaseData->created)) {
			$TS_TablesWP_Editor_Date		= strtotime($TS_TablesWP_Editor_BaseData->created);			
		} else {
			$TS_TablesWP_Editor_Date		= current_time('timestamp', 0);
		}
		if (isset($TS_TablesWP_Editor_BaseData->updated)) {
			$TS_TablesWP_Editor_Updated		= strtotime($TS_TablesWP_Editor_BaseData->updated);		
		} else {
			$TS_TablesWP_Editor_Updated		= $TS_TablesWP_Editor_Date;
		}
		if (isset($TS_TablesWP_Editor_BaseData->info)) {
			$TS_TablesWP_Editor_Info		= $TS_TablesWP_Editor_BaseData->info;
		} else {
			$TS_TablesWP_Editor_Info		= "";
		}
		if (isset($TS_TablesWP_Editor_BaseData->rows)) {
			$TS_TablesWP_Editor_Rows		= $TS_TablesWP_Editor_BaseData->rows;
		} else {
			$TS_TablesWP_Editor_Rows		= 0;
		}
		if (isset($TS_TablesWP_Editor_BaseData->cols)) {
			$TS_TablesWP_Editor_Columns		= $TS_TablesWP_Editor_BaseData->cols;
		} else {
			$TS_TablesWP_Editor_Columns		= 0;
		}
		if (isset($TS_TablesWP_Editor_BaseData->data)) {
			$TS_TablesWP_Editor_Data		= $TS_TablesWP_Editor_BaseData->data;
		} else {
			$TS_TablesWP_Editor_Data		= json_encode(array());
		}
		if (isset($TS_TablesWP_Editor_BaseData->meta)) {
			$TS_TablesWP_Editor_Meta		= $TS_TablesWP_Editor_BaseData->meta;
		} else {
			$TS_TablesWP_Editor_Meta		= json_encode(array());
		}
		if (isset($TS_TablesWP_Editor_BaseData->defaults)) {
			$TS_TablesWP_Editor_Defaults	= $TS_TablesWP_Editor_BaseData->defaults;
		} else {
			$TS_TablesWP_Editor_Defaults	= json_encode(array());
		}
		if (isset($TS_TablesWP_Editor_BaseData->merged)) {
			$TS_TablesWP_Editor_Merged		= $TS_TablesWP_Editor_BaseData->merged;
		} else {
			$TS_TablesWP_Editor_Merged		= json_encode(array());
		}
		if (isset($TS_TablesWP_Editor_BaseData->other)) {
			$TS_TablesWP_Editor_Other		= $TS_TablesWP_Editor_BaseData->other;
		} else {
			$TS_TablesWP_Editor_Other		= json_encode(array());
		}
		// Retrieve Other/Various Data
		$TS_TablesWP_Editor_Various			= json_decode($TS_TablesWP_Editor_Other);
		if (isset($TS_TablesWP_Editor_Various->urlencoded)) {
			$TS_TablesWP_Editor_URLEncoded	= $TS_TablesWP_Editor_Various->urlencoded;
		} else {
			$TS_TablesWP_Editor_URLEncoded	= "false";
		}
		if (isset($TS_TablesWP_Editor_Various->savemeta)) {
			$TS_TablesWP_Editor_SaveMeta	= $TS_TablesWP_Editor_Various->savemeta;
		} else {
			$TS_TablesWP_Editor_SaveMeta	= "true";
		}
		if (isset($TS_TablesWP_Editor_Various->useformulas)) {
			$TS_TablesWP_Editor_Formulas	= $TS_TablesWP_Editor_Various->useformulas;
		} else {
			$TS_TablesWP_Editor_Formulas	= "false";
		}
		if (isset($TS_TablesWP_Editor_Various->usecharts)) {
			$TS_TablesWP_Editor_Charts		= $TS_TablesWP_Editor_Various->usecharts;
		} else {
			$TS_TablesWP_Editor_Charts		= "false";
		}
		if (isset($TS_TablesWP_Editor_Various->usesearch)) {
			$TS_TablesWP_Editor_Search		= $TS_TablesWP_Editor_Various->usesearch;
		} else {
			$TS_TablesWP_Editor_Search		= "false";
		}
		if (isset($TS_TablesWP_Editor_Various->usecontext)) {
			$TS_TablesWP_Editor_Context		= $TS_TablesWP_Editor_Various->usecontext;
		} else {
			$TS_TablesWP_Editor_Context		= "true";
		}
		if (isset($TS_TablesWP_Editor_Various->usevalidator)) {
			$TS_TablesWP_Editor_Validator	= $TS_TablesWP_Editor_Various->usevalidator;
		} else {
			$TS_TablesWP_Editor_Validator	= "true";
		}
		if (isset($TS_TablesWP_Editor_Various->csvexternal)) {
			$TS_TablesWP_Editor_External	= $TS_TablesWP_Editor_Various->csvexternal;
		} else {
			$TS_TablesWP_Editor_External	= "false";
		}		
		if (isset($TS_TablesWP_Editor_Various->fixrow)) {
			$TS_TablesWP_Editor_FixRow		= $TS_TablesWP_Editor_Various->fixrow;
		} else {
			$TS_TablesWP_Editor_FixRow		= "true";
		}
		if (isset($TS_TablesWP_Editor_Various->fixcolumn)) {
			$TS_TablesWP_Editor_FixColumn	= $TS_TablesWP_Editor_Various->fixcolumn;
		} else {
			$TS_TablesWP_Editor_FixColumn	= "false";
		}		
		if (isset($TS_TablesWP_Editor_Various->csvpath)) {
			$TS_TablesWP_Editor_Path		= $TS_TablesWP_Editor_Various->csvpath;
		} else {
			$TS_TablesWP_Editor_Path		= "";
		}
		if (isset($TS_TablesWP_Editor_Various->categories)) {
			$TS_TablesWP_Editor_Cats		= $TS_TablesWP_Editor_Various->categories;
			$TS_TablesWP_Editor_Cats 		= explode(',', $TS_TablesWP_Editor_Cats);
		} else {
			$TS_TablesWP_Editor_Cats		= array();
		}
		// Retrieve Categories and Tags
		if ((isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables["table" . $TS_TablesWP_Editor_ID]['categories'])) && (empty($TS_TablesWP_Editor_Cats))) {
			$TS_TablesWP_Editor_Cats		= $TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables["table" . $TS_TablesWP_Editor_ID]['categories'];
			$TS_TablesWP_Editor_Cats 		= explode(',', $TS_TablesWP_Editor_Cats);
		}
		$TS_TablesWP_Editor_Tags			= array();
		$TS_TablesWP_Editor_Cross			= array();
		// Flatten Multidimensional Cats/Tags
		foreach ($TS_TablesWP_Define_Copy as $categories => $category) {
			array_push($TS_TablesWP_Editor_Cross, $category->id);
		}
		// Check For Deleted Categories
		foreach ($TS_TablesWP_Editor_Cats as $categories => $category) {
			if (!in_array($category, $TS_TablesWP_Editor_Cross)) {
				unset($TS_TablesWP_Editor_Cats[$categories]);
			}
		}
		// Check for Properly Encoded Data
		if ($TS_TablesWP_Editor_URLEncoded == "false") {
			if (preg_match('~%[0-9A-F]{2}~i', $TS_TablesWP_Editor_Data)) {
				$TS_TablesWP_Editor_Validated	= "true";
			} else {
				$TS_TablesWP_Editor_Validated	= "false";
			}
			// Check + Remove Breaking '\' Characters
			if ($TS_TablesWP_Editor_Validated == "false") {
				// Check Main Data
				$TS_TablesWP_Editor_Data	= rawurldecode($TS_TablesWP_Editor_Data);
				$TS_TablesWP_Editor_Data	= json_decode($TS_TablesWP_Editor_Data);			
				foreach ($TS_TablesWP_Editor_Data as $rows => $row) {
					foreach ($row as $cells => $cell) {
						$TS_TablesWP_Editor_Data[$rows][$cells] = str_replace(array('&#92;"', '&#92;\"', '\"', '\\"'), '', $cell);
					}
				}
				$TS_TablesWP_Editor_Data	= json_encode($TS_TablesWP_Editor_Data);
				// Check Meta Data
				$TS_TablesWP_Editor_Meta	= rawurldecode($TS_TablesWP_Editor_Meta);
				$TS_TablesWP_Editor_Meta	= json_decode($TS_TablesWP_Editor_Meta);
				foreach ($TS_TablesWP_Editor_Meta as $rows => &$row) {
					$row->value				= rawurlencode(str_replace(array('&#92;"', '&#92;\"', '\"', '\\"'), '', $row->value));
				}
				$TS_TablesWP_Editor_Meta	= json_encode($TS_TablesWP_Editor_Meta);
			} else {
				$TS_TablesWP_Editor_Data	= rawurldecode($TS_TablesWP_Editor_Data);
			}
		} else {
			$TS_TablesWP_Editor_Data		= rawurldecode($TS_TablesWP_Editor_Data);
		}
    } else if ($TS_TablesWP_Editor_Action == "save") {
        // Render Preloader Animation
        echo '<div id="ts_vcsc_extend_settings_save" style="position: relative; margin: 20px auto 20px auto; width: 128px; height: 128px;">';
            echo TS_TablesWP_CreatePreloaderCSS("ts-settings-panel-loader", "", 6, "false");
        echo '</div>';
	}
    
    if (isset($_POST['Save'])) {
		// Retrieve Form Contents
        $TS_TablesWP_Editor_ID          	= trim ($_POST['ts-advanced-table-number-input']);
        $TS_TablesWP_Editor_Name        	= trim ($_POST['ts-advanced-table-name-input']);
        $TS_TablesWP_Editor_Date        	= trim ($_POST['ts-advanced-table-date-stamp']);	
		$TS_TablesWP_Editor_Rows      		= trim ($_POST['ts-advanced-table-transfer-rows']);
		$TS_TablesWP_Editor_Columns			= trim ($_POST['ts-advanced-table-transfer-columns']);
		$TS_TablesWP_Editor_Merged      	= trim ($_POST['ts-advanced-table-transfer-merged']);		
        $TS_TablesWP_Editor_Info        	= trim ($_POST['ts-advanced-table-info-input']);
		$TS_TablesWP_Editor_Defaults		= trim ($_POST['ts-advanced-table-transfer-defaults']);
		$TS_TablesWP_Editor_Data			= trim ($_POST['ts-advanced-table-transfer-data']);
		$TS_TablesWP_Editor_Meta			= trim ($_POST['ts-advanced-table-transfer-meta']);
		$TS_TablesWP_Editor_Other			= trim ($_POST['ts-advanced-table-transfer-other']);
		$TS_TablesWP_Editor_Cats			= trim ($_POST['ts-advanced-table-transfer-cats']);
		$TS_TablesWP_Editor_Tags			= trim ($_POST['ts-advanced-table-transfer-tags']);
		$TS_TablesWP_Editor_ID				= (string)$TS_TablesWP_Editor_ID;
		// Update Table In Database
		$wpdb->update($wpdb->prefix . "ts_advancedtables",
			array(
				"name" 						=> $TS_TablesWP_Editor_Name,
				"cols" 						=> $TS_TablesWP_Editor_Columns,
				"rows" 						=> $TS_TablesWP_Editor_Rows,
				"merged" 					=> stripcslashes($TS_TablesWP_Editor_Merged),
				"defaults" 					=> stripcslashes($TS_TablesWP_Editor_Defaults),
				"info" 						=> stripcslashes($TS_TablesWP_Editor_Info),
				"data" 						=> stripcslashes($TS_TablesWP_Editor_Data),
				"meta" 						=> stripcslashes($TS_TablesWP_Editor_Meta),
				"other" 					=> stripcslashes($TS_TablesWP_Editor_Other),			
			), array(
				'number' 					=> $TS_TablesWP_Editor_ID
			), null, array('%d')
		);
		// Update Table In Basic Listing
        $TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables["table" . $TS_TablesWP_Editor_ID] = array(
            "id"							=> $TS_TablesWP_Editor_ID,
            "name"							=> $TS_TablesWP_Editor_Name,
            "create"						=> intval($TS_TablesWP_Editor_Date),
            "update"						=> (int)current_time('timestamp', 0),
            "info"							=> base64_encode($TS_TablesWP_Editor_Info),
			"rows"							=> $TS_TablesWP_Editor_Rows,
			"columns"						=> $TS_TablesWP_Editor_Columns,
			"merged"						=> $TS_TablesWP_Editor_Merged,
			"charts"						=> "false",
			"categories"					=> $TS_TablesWP_Editor_Cats,
			"tags"							=> $TS_TablesWP_Editor_Tags,
        );
        update_option("ts_tablesplus_extend_settings_tables", $TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables);
        // Redirect to Listing Page
		if ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_SaveRedirect == true) {
			echo'<script>window.location="' . admin_url('admin.php?page=TS_TablesWP_Tables') . '";</script>';
        } else {
			echo'<script>window.location="' . $TS_TablesWP_Editor_Target . 'edit' . '";</script>';
        }
		Exit();
    }
?>
	<div class="ts-vcsc-settings-page-header">
		<h2><span class="dashicons dashicons-editor-table"></span><?php echo __("Tablenator - Advanced Tables for WordPress", "ts_visual_composer_extend"); ?> v<?php echo TS_TablesWP_GetPluginVersion(); ?> ... <?php echo __("Edit Table", "ts_visual_composer_extend"); ?> #<?php echo $TS_TablesWP_Editor_ID; ?></h2>
		<div class="clear"></div>
	</div>
    <form id="ts-advanced-table-edit-form" class="ts-advanced-table-edit-form" name="ts-advanced-table-edit-form" autocomplete="off" method="post" action="<?php echo $TS_TablesWP_Editor_Target . 'save'; ?>">
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
            <div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder" style="float: right; margin: 0 0 0 20px;">
                <span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to delete your table.", "ts_visual_composer_extend"); ?></span>
                <a href="<?php echo admin_url('admin.php?page=TS_TablesWP_Modify&tableid=' . $TS_TablesWP_Editor_ID . '&action=delete'); ?>" target="_parent" id="ts-advanced-tables-button-delete" class="ts-advanced-tables-button-main ts-advanced-tables-button-red ts-advanced-tables-button-delete" type="submit" name="Delete" data-table-id="' . $TS_TablesWP_Editor_ID . '" style="margin: 0;">
					<?php echo __("Delete Table", "ts_visual_composer_extend"); ?>
				</a>
            </div>
            <div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder" style="float: right; margin: 0 0 0 20px;">
                <span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to clone your table.", "ts_visual_composer_extend"); ?></span>
                <a href="<?php echo admin_url('admin.php?page=TS_TablesWP_Modify&tableid=' . $TS_TablesWP_Editor_ID . '&action=clone'); ?>" target="_parent" id="ts-advanced-tables-button-copy" class="ts-advanced-tables-button-main ts-advanced-tables-button-green ts-advanced-tables-button-copy" type="submit" name="Clone" style="margin: 0;">
					<?php echo __("Clone Table", "ts_visual_composer_extend"); ?>
				</a>
            </div>
            <div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder" style="float: right; margin: 0 0 0 20px;">
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