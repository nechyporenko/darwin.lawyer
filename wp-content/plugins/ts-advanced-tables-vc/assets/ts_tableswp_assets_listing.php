<?php
    global $TS_ADVANCED_TABLESWP;
	global $wpdb;

	// Retrieve Pre-Defined Categories
	$TS_TablesWP_Define_Data			    = get_option("ts_tablesplus_extend_settings_categories", '');
	$TS_TablesWP_Define_Data			    = rawurldecode($TS_TablesWP_Define_Data);
	$TS_TablesWP_Define_Cats			    = json_decode($TS_TablesWP_Define_Data);
	$TS_TablesWP_Define_Copy			    = json_decode($TS_TablesWP_Define_Data);
	$TS_TablesWP_Define_Cross			    = array();
	if ((!is_array($TS_TablesWP_Define_Cats)) || ($TS_TablesWP_Define_Cats == '')) {
		$TS_TablesWP_Define_Cats 		    = array();
	}
	if ((!is_array($TS_TablesWP_Define_Copy)) || ($TS_TablesWP_Define_Copy == '')) {
		$TS_TablesWP_Define_Copy 		    = array();
	}
	
	// Flatten Pre-Defined Categories
	if (count($TS_TablesWP_Define_Copy) > 0) {
		$TS_TablesWP_Define_Copy 		    = TS_TablesWP_FlattenObject($TS_TablesWP_Define_Copy);
	}
	
	// Create Simple Slug Array
	foreach ($TS_TablesWP_Define_Copy as $categories => $category) {
		array_push($TS_TablesWP_Define_Cross, $category->id);
	}
	
	// Read All Assigned Categories from Existing Tables
	$TS_TablesWP_Holder_Counter			    = 0;
	$TS_TablesWP_Holder_NoCats			    = 0;
	$TS_TablesWP_Holder_Single			    = array();
	$TS_TablesWP_Holder_Categories		    = array();
	$TS_TablesWP_Holder_Matched			    = array();
	$TS_TablesWP_Holder_Defined			    = $TS_TablesWP_Define_Cats;		
	if (count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables) > 0) {
		foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables as $tables => $table) {
			if ((isset($table['categories'])) && ($table['categories'] != '')) {
				$TS_TablesWP_Holder_Single			= explode(',', $table['categories']);
				$TS_TablesWP_Holder_Categories		= array_merge($TS_TablesWP_Holder_Categories, $TS_TablesWP_Holder_Single);
			} else {
				$TS_TablesWP_Holder_NoCats++;
			}
		}
		$TS_TablesWP_Holder_Matched		    = array_count_values($TS_TablesWP_Holder_Categories);
		foreach ($TS_TablesWP_Holder_Matched as $categories => $category) {
			$TS_TablesWP_Holder_Counter	    = $TS_TablesWP_Holder_Counter + $category;
		}
	}
	
	// Function to Get Category Name From Slug
	function TS_TablesWP_GetSlugData($categories, $slug, $data) {
		if ($data == 'level') {
			$default = 0;
		} else {
			$default = '';
		}
		if (count($categories) > 0) {
			foreach ($categories as $categories => $category) {
				if ($category->id == $slug) {
					return $category->$data;
				}
			}
			return $default;
		} else {
			return $default;
		}
	}
	
	// Retrieve Pre-Defined Tags
	$TS_TablesWP_Define_Tags			    = array();
	
	// Function to Create 'Disabled' Attribute for Checkboxes
	function TS_TablesWP_GetCheckboxDisabled($count) {
		if ($count == 0) {
			return 'disabled="disabled"';
		} else {
			return '';
		}
	}
	
	
	// Check if Database Table Exists
	$TS_TablesWP_DatabaseCheck              = TS_TablesWP_Datebase_TableCheck();
    
    // Retrieve Database Storage Size
    if ($TS_TablesWP_DatabaseCheck) {
        $TS_TablesWP_DatabaseName		    = $wpdb->dbname;
        $TS_TablesWP_DatabaseTable		    = $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginMYSQL;
        $TS_TablesWP_DatabaseSize 		    = $wpdb->get_results("SELECT table_name AS 'Table', round(((data_length + index_length) / 1024 / 1024), 2) 'Size' FROM information_schema.TABLES WHERE table_schema = '$TS_TablesWP_DatabaseName' AND table_name = '$TS_TablesWP_DatabaseTable'");
        if (is_array($TS_TablesWP_DatabaseSize)) {
            if ((isset($TS_TablesWP_DatabaseSize[0])) && (is_object($TS_TablesWP_DatabaseSize[0]))) {
                $TS_TablesWP_DatabaseSize	= $TS_TablesWP_DatabaseSize[0];
                $TS_TablesWP_DatabaseSize	= $TS_TablesWP_DatabaseSize->Size;
            } else {
                $TS_TablesWP_DatabaseSize	= __("N/A", "ts_visual_composer_extend");
            }		
        } else {
            $TS_TablesWP_DatabaseSize		= __("N/A", "ts_visual_composer_extend");
        }
    } else {
        $TS_TablesWP_DatabaseName		    = $wpdb->prefix . TABLESWP_MYSQL;
        $TS_TablesWP_DatabaseSize		    = __("N/A", "ts_visual_composer_extend");
    }
	
	// Generate Length Menu
	$TS_TablesWP_LengthOptions			    = '5,10,15,25,50,75,100,150,200';
	$TS_TablesWP_LengthOptions			    = explode(",", $TS_TablesWP_LengthOptions);
	$TS_TablesWP_LengthDefault			    = 10;
	sort($TS_TablesWP_LengthOptions);
	$TS_TablesWP_LengthSelect			    = array();
	foreach ($TS_TablesWP_LengthOptions as $option) {
		if ($option < count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables)) {
			array_push($TS_TablesWP_LengthSelect, $option);
		}
	}
	if (count($TS_TablesWP_LengthSelect) > 0) {
		if (max($TS_TablesWP_LengthSelect) < $TS_TablesWP_LengthDefault) {
			$TS_TablesWP_LengthDefault	    = -1;
		}
	}
	$TS_TablesWP_LengthSelect			    = implode(",", $TS_TablesWP_LengthSelect);

	// Check for Migration Requirement
	if ((count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables) > 0) && ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Migrated == false)) {
		$TS_TablesWP_NeedMigration		    = true;
	} else {
		$TS_TablesWP_NeedMigration		    = false;
	}

	// DataTable Language Strings
	$TS_TablesWP_DataTable				    = 'data-datatable-exportlist="' . ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_ExportList == true ? "true" : "false") . '" data-datatable-exportoptions="' . $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_ExportOptions . '" data-datatable-paginationuse="' . ($TS_TablesWP_LengthSelect != "" ? "true" : "false") . '" data-datatable-length="' . $TS_TablesWP_LengthDefault . '" data-datatable-pageselect="' . $TS_TablesWP_LengthSelect . '"';
	$TS_TablesWP_DataTable 				    .= ' data-datatable-textprint="' . __("Print", "ts_visual_composer_extend") . '" data-datatable-textpdf="' . __("PDF", "ts_visual_composer_extend") . '" data-datatable-textcsv="' . __("CSV", "ts_visual_composer_extend") . '" data-datatable-textexcel="' . __("Excel", "ts_visual_composer_extend") . '" data-datatable-textcopy="' . __("Copy", "ts_visual_composer_extend") . '"';
	$TS_TablesWP_DataTable				    .= ' data-datatable-textprocessing="' . __("Processing ...", "ts_visual_composer_extend") . '" data-datatable-textlengthmenu="' . __("Show _MENU_ Entries", "ts_visual_composer_extend") . '" data-datatable-textlengthall="' . __("All", "ts_visual_composer_extend") . '"';
	$TS_TablesWP_DataTable				    .= ' data-datatable-textinfomain="' . __("Showing _START_ to _END_ of _TOTAL_ Tables", "ts_visual_composer_extend") . '" data-datatable-textinfoempty="' . __("No Entries To Show!", "ts_visual_composer_extend") . '" data-datatable-textinfofiltered="' . __(" - filtered from _MAX_ records.", "ts_visual_composer_extend") . '"';
	$TS_TablesWP_DataTable				    .= ' data-datatable-textsearch="' . __("Search All Tables:", "ts_visual_composer_extend") . '" data-datatable-textplaceholder="' . __("Enter keyword here ...", "ts_visual_composer_extend") . '" data-datatable-textzerorecords="' . __("No Entries To Show!", "ts_visual_composer_extend") . '"';
	$TS_TablesWP_DataTable				    .= ' data-datatable-textfirst="' . __("First", "ts_visual_composer_extend") . '" data-datatable-textprevious="' . __("Previous", "ts_visual_composer_extend") . '" data-datatable-textnext="' . __("Next", "ts_visual_composer_extend") . '" data-datatable-textlast="' . __("Last", "ts_visual_composer_extend") . '"';
?>
	<div class="ts-vcsc-settings-page-header">
		<h2><span class="dashicons dashicons-grid-view"></span><?php echo __("Tablenator - Advanced Tables for WordPress", "ts_visual_composer_extend"); ?> v<?php echo TS_TablesWP_GetPluginVersion(); ?> ... <?php echo __("Tables List", "ts_visual_composer_extend"); ?></h2>
		<div class="clear"></div>
	</div>
    <form id="ts-advanced-table-list-form" class="ts-advanced-table-list-form" name="ts-advanced-table-list-form" autocomplete="off" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" style="position: relative;">
		<div id="ts-advanced-table-language-wrapper" class="ts-advanced-table-language-wrapper" style="display: none;">
			<span id="ts-advanced-table-language-blank" class="ts-advanced-table-language-blank"></span>
			<span id="ts-advanced-table-language-yes" class="ts-advanced-table-language-yes"><?php echo __("Yes", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-no" class="ts-advanced-table-language-no"><?php echo __("No", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-submit" class="ts-advanced-table-language-submit"><?php echo __("Submit", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-cancel" class="ts-advanced-table-language-cancel"><?php echo __("Cancel", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-understood" class="ts-advanced-table-language-understood"><?php echo __("Understood!", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-continue" class="ts-advanced-table-language-continue"><?php echo __("Continue", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-plugin" class="ts-advanced-table-language-plugin"><?php echo __("Tablenator - Advanced Tables for WordPress & WP Bakery Page Builder", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-validtext" class="ts-advanced-table-language-validtext"><?php echo __("Your table validated correctly. Do you want to save this table?", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-errortext" class="ts-advanced-table-language-errortext"><?php echo __("You forgot to provide a name for this table; the table can not be saved without a name.", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-rebuildtitle" class="ts-advanced-table-language-rebuildtitle"><?php echo __("Create Database Table", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-rebuildtext" class="ts-advanced-table-language-rebuildtext"><?php echo __("Do you really want to attempt to create the missing SQL database table '%s' one more time?", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-deletetitle" class="ts-advanced-table-language-deletetitle"><?php echo __("Delete Table #%d", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-deletetext" class="ts-advanced-table-language-deletetext"><?php echo __("Do you really want to delete table #%d (%s)?", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-clonetitle" class="ts-advanced-table-language-clonetitle"><?php echo __("Clone Table #%d", "ts_visual_composer_extend"); ?></span>        
			<span id="ts-advanced-table-language-clonetext" class="ts-advanced-table-language-clonetext"><?php echo __("Please provide a new and unique name for the cloned version of table #%d (%s)?", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-clonesingle" class="ts-advanced-table-language-clonesingle"><?php echo __("Clone", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-generator" class="ts-advanced-table-language-generator"><?php echo __("Generate Shortcode", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-addtable" class="ts-advanced-table-language-addtable"><?php echo __("Add Table Shortcode", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-clearalltitle" class="ts-advanced-table-language-clearalltitle"><?php echo __("Delete ALL %d Table(s)", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advanced-table-language-clearalltext" class="ts-advanced-table-language-clearalltext"><?php echo __("Do you really want to permanently delete ALL %d table(s)?", "ts_visual_composer_extend"); ?></span>
		</div>
		<div class="ts-settings-statistics-links" style="border-bottom: 1px dashed #cccccc; display: block; width: 100%; margin: 5px 0 20px; float: left;">
			<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder" style="display: <?php echo ((($TS_TablesWP_NeedMigration == false) && ($TS_TablesWP_DatabaseCheck == true) && ($TS_ADVANCED_TABLESWP->TS_TablesWP_CurrentUserEditor == true)) ? "inline-block" : "none"); ?>;">
				<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to create a new table.", "ts_visual_composer_extend"); ?></span>
				<a href="<?php echo admin_url('admin.php?page=TS_TablesWP_Grid'); ?>" target="_parent" class="ts-advanced-tables-button-main ts-advanced-tables-button-green ts-advanced-tables-button-table">
					<?php echo __("Add New Table", "ts_visual_composer_extend"); ?>
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
			<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder" style="display: <?php echo ((($TS_TablesWP_NeedMigration == false) && ($TS_TablesWP_DatabaseCheck == true) && ($TS_ADVANCED_TABLESWP->TS_TablesWP_CurrentUserEditor == true)) ? "inline-block" : "none"); ?>;">
				<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to conduct table maintenance such as imports and exports.", "ts_visual_composer_extend"); ?></span>
				<a href="<?php echo admin_url('admin.php?page=TS_TablesWP_Maintain'); ?>" target="_parent" class="ts-advanced-tables-button-main ts-advanced-tables-button-purple ts-advanced-tables-button-wrench">
					<?php echo __("Tables Maintenance", "ts_visual_composer_extend"); ?>
				</a>
			</div>
			<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder" style="display: <?php echo ((($TS_TablesWP_NeedMigration == false) && ($TS_TablesWP_DatabaseCheck == true) && (count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables) > 0)) ? "inline-block" : "none"); ?>;">
				<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to generate the full shortcode for any of the tables listed below.", "ts_visual_composer_extend"); ?></span>
				<a href="#" target="_parent" class="ts-advanced-tables-button-main ts-advanced-tables-button-orange ts-advanced-tables-button-code cs-shortcode ts-advanced-table-generator-trigger" data-table-id="" data-editor-id="ts-advanced-table-generator-input">
					<?php echo __("Generate Shortcode", "ts_visual_composer_extend"); ?>
				</a>
			</div>
		</div>		
		<div id="ts-settings-statistics-shortcode-wrapper" class="ts-settings-statistics-shortcode-wrapper" style="border-bottom: 1px dashed #cccccc; display: none; width: 100%; margin: 0 0 20px 0; padding: 0 0 20px 0; float: left;">
			<div id="ts-settings-statistics-clipboard-success" class="ts-vcsc-notice-field ts-vcsc-success" style="display: none; font-size: 13px; text-align: justify;">
				<?php echo __("The shortcode for the table has been copied to your clipboard!", "ts_visual_composer_extend"); ?>
			</div>
			<div id="ts-settings-statistics-clipboard-error" class="ts-vcsc-notice-field ts-vcsc-critical" style="display: none; font-size: 13px; text-align: justify;">
				<?php echo __("The shortcode for the table could NOT be copied to your clipboard!", "ts_visual_composer_extend"); ?>
			</div>
			<textarea id="ts-advanced-table-generator-input" class="ts-advanced-table-generator-input" name="ts-advanced-table-generator-input" readonly="readonly" style="width: 100%; height: 100px;"></textarea>
			<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder" style="margin-top: 10px;">
				<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to copy the shortcode to the browser clipboard.", "ts_visual_composer_extend"); ?></span>
				<a href="#" target="_parent" id="ts-advanced-table-clipboard-button" class="ts-advanced-tables-button-main ts-advanced-tables-button-orange ts-advanced-tables-button-copy" style="margin: 0;" data-clipboard-target="#ts-advanced-table-generator-input">
					<?php echo __("Copy to Clipboard", "ts_visual_composer_extend"); ?>
				</a>
			</div>
		</div>
		<div id="ts-settings-statistics-migrate-wrapper" class="ts-settings-statistics-migrate-wrapper" style="width: 100%; border-bottom: 1px dashed #cccccc; display: block; width: 100%; margin: 5px 0 20px; float: left; display: <?php echo ((($TS_TablesWP_NeedMigration == false) || ($TS_ADVANCED_TABLESWP->TS_TablesWP_CurrentUserEditor == false) || ($TS_TablesWP_DatabaseCheck == false)) ? "none" : "block"); ?>;">
			<div class="ts-vcsc-info-field ts-vcsc-critical" style="margin-top: 10px; font-size: 13px; text-align: justify;">
				<?php echo __("The update to v1.1.0 of this plugin changed how the table data is stored within the WordPress database, moving the table data from an option storage to its own dedicated database table. This step has become necessary in order to improve overall database performance, particularly when storing large tables with a lot of formatting. Before you can create a new table or take any action on existing tables, you need to migrate the existing tables to their new storage type.", "ts_visual_composer_extend"); ?>
			</div>
			<div class="ts-vcsc-notice-field ts-vcsc-warning" style="margin-top: 10px; font-size: 13px; text-align: justify;">
				<?php echo __("The migration process has been automated but needs to be manually triggered. Please use the button below to go to the migration page and follow the instructions provided there. After the migration has been completed, you will be able to edit your existing table again, as well as create new ones. The migration process occurs only once.", "ts_visual_composer_extend"); ?>
			</div>			
			<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
				<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to go to the table migration page.", "ts_visual_composer_extend"); ?></span>
				<a href="<?php echo admin_url('admin.php?page=TS_TablesWP_Migrate'); ?>" target="_parent" class="ts-advanced-tables-button-main ts-advanced-tables-button-red ts-advanced-tables-button-update" data-table-id="" data-editor-id="ts-advanced-table-generator-input">
					<?php echo __("Tables Migration", "ts_visual_composer_extend"); ?>
				</a>
			</div>
		</div>
        <div id="ts-settings-statistics-missing-wrapper" class="ts-settings-statistics-missing-wrapper" style="display: <?php echo ($TS_TablesWP_DatabaseCheck == false ? "block" : "none"); ?>; width: 100%; float: left;">
            <div class="ts-vcsc-notice-field ts-vcsc-critical" style="margin-top: 10px; margin-bottom: 20px; font-size: 14px; text-align: justify;">
                <?php
                    echo "<strong>" . sprintf(_("It appears that the required SQL database table '%s' to store all the data for each HTML table created through this plugin is missing."), $TS_TablesWP_DatabaseName) . "</strong>";
					echo "<br/><br/>" . __("Usually, the missing table should have been created automatically when you first activated this plugin, however, for some reason, that process must have failed.", "ts_visual_composer_extend");
					echo "<br/><br/>" . __("Please use the button below to attempt to create the missing SQL database table one more time. If still unsuccessful, please contact plugin support for any further assistance.", "ts_visual_composer_extend");
                ?>
            </div>
            <div id="ts-settings-maintenance-rebuild-wrapper" style="margin: 0 0 10px 0;"  data-name="<?php echo $TS_TablesWP_DatabaseName; ?>" data-link="<?php echo admin_url('admin.php?page=TS_TablesWP_Rebuild'); ?>">
                <div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
                    <span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to attempt to create the missing SQL database table.", "ts_visual_composer_extend"); ?></span>
                    <div class="ts-advanced-tables-button-main ts-advanced-tables-button-red ts-advanced-tables-button-wrench" style="margin: 0;">
                        <?php echo __("Create SQL Database Table", "ts_visual_composer_extend"); ?>
                    </div>
                </div>
            </div>
        </div>
		<div id="ts-settings-statistics-clear-wrapper" class="ts-settings-statistics-clear-wrapper" style="display: <?php echo ($TS_TablesWP_DatabaseCheck == true ? "block" : "none"); ?>;">
			<div class="ts-settings-statistics-databasesize-wrapper" style="display: <?php echo ((count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables) > 0) ? "block" : "block"); ?>; width: 100%; float: left;">
				<div class="ts-vcsc-notice-field ts-vcsc-warning" style="margin-top: 10px; margin-bottom: 30px; font-size: 14px; text-align: justify;">
					<?php printf(__("For your information, all %d tables combined require %s MB of storage capacity within the WordPress database.", "ts_visual_composer_extend"), count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables), $TS_TablesWP_DatabaseSize); ?>
				</div>
			</div>	
			<div id="ts-settings-statistics-filter-wrapper" style="display: <?php echo ($TS_TablesWP_Holder_Counter > 0 ? "block" : "none") ?>; float: left; margin: 0 0 20px 0;" data-show="<?php echo __("Show Categories Filter", "ts_visual_composer_extend"); ?>" data-hide="<?php echo __("Hide Categories Filter", "ts_visual_composer_extend"); ?>" data-visible="false" data-target="ts-settings-statistics-categories-wrapper">
				<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
					<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to filter the table listing by categories.", "ts_visual_composer_extend"); ?></span>
					<div class="ts-advanced-tables-button-main ts-advanced-tables-button-silver ts-advanced-tables-button-search" style="margin: 0;">
						<?php echo __("Show Categories Filter", "ts_visual_composer_extend"); ?>
					</div>
				</div>
			</div>
			<div id="ts-settings-statistics-categories-wrapper" class="ts-settings-statistics-categories-wrapper" style="display: none;">
				<?php
					// Render Pre-Defined Categories
					echo '<div id="ts-advanced-table-categories-selector" class="ts-advanced-table-categories-selector" name="ts-advanced-table-categories-selector">';
						echo '<ul id="ts-advanced-table-categories-listing" class="ts-advanced-table-categories-level-0" data-visible="true">';
							if (count($TS_TablesWP_Define_Cats) > 0) {							
								echo '<li data-count="' . $TS_TablesWP_Holder_NoCats . '" data-visible="true" data-children="false">';
									echo '<div class="ts-advanced-table-categories-handle" style="background: rgba(237, 111, 111, 0.25);">';
										echo '<input type="checkbox" ' . TS_TablesWP_GetCheckboxDisabled($TS_TablesWP_Holder_NoCats) . ' id="ts-advanced-table-categories-none" data-id="ts-advanced-table-categories-none" data-name="' . __("No Categories", "ts_visual_composer_extend") . '" data-level="0" /><label for="ts-advanced-table-categories-none"><strong>' . __("No Categories", "ts_visual_composer_extend") . ' (' . $TS_TablesWP_Holder_NoCats . ')</strong></label>';
									echo '</div>';
								echo '</li>';
								foreach ($TS_TablesWP_Define_Cats as $categories => $category) {
									echo '<li data-count="' . (isset($TS_TablesWP_Holder_Matched[$category->id]) ? $TS_TablesWP_Holder_Matched[$category->id] : '0') . '" data-visible="true" data-children="false">';
										echo '<div class="ts-advanced-table-categories-handle">';
											echo '<input type="checkbox" ' . TS_TablesWP_GetCheckboxDisabled((isset($TS_TablesWP_Holder_Matched[$category->id]) ? $TS_TablesWP_Holder_Matched[$category->id] : 0)) . ' id="' . $category->id . '" data-id="' . $category->id . '" data-name="' . $category->name . '" data-level="' . TS_TablesWP_GetSlugData($TS_TablesWP_Define_Copy, $category->id, 'level') . '" /><label for="' . $category->id . '">' . $category->name . ' (' . (isset($TS_TablesWP_Holder_Matched[$category->id]) ? $TS_TablesWP_Holder_Matched[$category->id] : '0') . ')</label>';
										echo '</div>';
										// First Sublevel
										if (isset($category->children)) {
											echo '<ul class="ts-advanced-table-categories-level-1" data-visible="true">';
												foreach ($category->children as $sublevels => $level1) {
													echo '<li data-count="' . (isset($TS_TablesWP_Holder_Matched[$level1->id]) ? $TS_TablesWP_Holder_Matched[$level1->id] : '0') . '" data-visible="true" data-children="false">';
														echo '<div class="ts-advanced-table-categories-handle">';
															echo '<input type="checkbox" ' . TS_TablesWP_GetCheckboxDisabled((isset($TS_TablesWP_Holder_Matched[$level1->id]) ? $TS_TablesWP_Holder_Matched[$level1->id] : 0)) . ' id="' . $level1->id . '" data-id="' . $level1->id . '" data-name="' . $level1->name . '" data-level="' . TS_TablesWP_GetSlugData($TS_TablesWP_Define_Copy, $level1->id, 'level') . '" /><label for="' . $level1->id . '">' . $level1->name . ' (' . (isset($TS_TablesWP_Holder_Matched[$level1->id]) ? $TS_TablesWP_Holder_Matched[$level1->id] : '0') . ')</label>';
														echo '</div>';
														// Second Sublevel
														if (isset($level1->children)) {
															echo '<ul class="ts-advanced-table-categories-level-2" data-visible="true">';
																foreach ($level1->children as $sublevels => $level2) {
																	echo '<li data-count="' . (isset($TS_TablesWP_Holder_Matched[$level2->id]) ? $TS_TablesWP_Holder_Matched[$level2->id] : '0') . '" data-visible="true" data-children="false">';
																		echo '<div class="ts-advanced-table-categories-handle">';
																			echo '<input type="checkbox" ' . TS_TablesWP_GetCheckboxDisabled((isset($TS_TablesWP_Holder_Matched[$level2->id]) ? $TS_TablesWP_Holder_Matched[$level2->id] : 0)) . ' id="' . $level2->id . '" data-id="' . $level2->id . '" data-name="' . $level2->name . '" data-level="' . TS_TablesWP_GetSlugData($TS_TablesWP_Define_Copy, $level2->id, 'level') . '" /><label for="' . $level2->id . '">' . $level2->name . ' (' . (isset($TS_TablesWP_Holder_Matched[$level2->id]) ? $TS_TablesWP_Holder_Matched[$level2->id] : '0') . ')</label>';
																		echo '</div>';
																	echo '</li>';
																}
															echo '</ul>';
														}
													echo '</li>';
												}
											echo '</ul>';
										}
									echo '</li>';
								}
							}
						echo '</ul>';
					echo '</div>';
				?>
			</div>
			<div id="ts-settings-statistics-categories-selected" class="ts-settings-statistics-categories-selected" style="display: <?php echo ($TS_TablesWP_Holder_Counter > 0 ? "block" : "none") ?>;" data-empty="<?php echo __("No Categories Selected.", "ts_visual_composer_extend"); ?>">
				<?php echo __("No Categories Selected.", "ts_visual_composer_extend"); ?>
			</div>
			<div id="ts-settings-statistics-preloader" class="ts-settings-statistics-preloader">
				<?php echo TS_TablesWP_CreatePreloaderCSS("ts-settings-panel-loader", "", 22, "false"); ?>
			</div>
			<div class="ts-settings-statistics-table ts-datatables-container ts-datatables-theme-yellow">
				<table id="ts-advanced-table-list-modify" class="ts-advanced-table-list-modify ts-datatables-tablemain display responsive" style="width: 100%; max-width: 100%;" <?php echo $TS_TablesWP_DataTable; ?> data-initial-sort="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_InitialSort; ?>" data-initial-order="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_InitialOrder; ?>">
					<thead>
						<tr class="ts-datatables-column-header">
							<th data-priority="2" data-orderable="false" data-searchable="false" class="all"></th>
							<th data-priority="1" data-type="num" data-orderable="true" data-searchable="true" class="all"><?php echo __("ID", "ts_visual_composer_extend"); ?></th>
							<th data-priority="3" data-type="string" data-orderable="true" data-searchable="true"><?php echo __("Name", "ts_visual_composer_extend"); ?></th>
							<th data-priority="5" data-type="num" data-orderable="true"data-searchable="true"><?php echo __("Created At", "ts_visual_composer_extend"); ?></th>
							<th data-priority="3" data-type="num" data-orderable="true"data-searchable="true"><?php echo __("Last Updated", "ts_visual_composer_extend"); ?></th>
							<th data-priority="3" data-type="html" data-orderable="false"data-searchable="false" class="none"><?php echo __("Actions", "ts_visual_composer_extend"); ?></th>
							<th data-priority="4" data-type="html" data-orderable="false" data-searchable="false" class="none"><?php echo __("Shortcode", "ts_visual_composer_extend"); ?></th>
							<th data-priority="5" data-type="html" data-orderable="false" data-searchable="false" class="none"><?php echo __("Summary", "ts_visual_composer_extend"); ?></th>
							<th data-priority="6" data-type="html" data-orderable="false" data-searchable="true" class="none"><?php echo __("Description", "ts_visual_composer_extend"); ?></th>
							<th data-priority="7" data-type="html" data-orderable="false" data-searchable="true" class="none"><?php echo __("Categories", "ts_visual_composer_extend"); ?></th>
							<th data-priority="8" data-type="html" data-orderable="false" data-searchable="true" data-visible="false" class="none"><?php echo __("Categories", "ts_visual_composer_extend"); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr class="ts-datatables-column-filter">
							<th><span class="ts-datatables-iconsearch"></span></th>
							<th><select class="ts-datatables-column-select"><option value=""><?php echo __("All", "ts_visual_composer_extend"); ?></option></select></th>
							<th><input class="ts-datatables-column-search" type="text" placeholder="<?php echo __("Search Names ...", "ts_visual_composer_extend"); ?>"/></th>
							<th><input class="ts-datatables-column-search" type="text" placeholder="<?php echo __("Search Dates ...", "ts_visual_composer_extend"); ?>"/></th>
							<th><input class="ts-datatables-column-search" type="text" placeholder="<?php echo __("Search Dates ...", "ts_visual_composer_extend"); ?>"/></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
						</tr>
					</tfoot>
					<tbody>                    
						<?php
							if ((count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables) > 0) && ($TS_TablesWP_DatabaseCheck == true)) {
								foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables as $tables => $table) {
									if ((isset($table['id'])) && (isset($table['name']))) {
										echo '<tr>';
											echo '<td></td>';
											echo '<td data-order="' . trim(strip_tags($table['id'])) . '">' . trim(strip_tags($table['id'])) . '</td>';
											echo '<td>' . $table['name'] . '</td>';
											echo '<td data-order="' . $table['create'] . '">' . date($TS_ADVANCED_TABLESWP->TS_TablesWP_WordPress_Date . ' - ' . $TS_ADVANCED_TABLESWP->TS_TablesWP_WordPress_Time, $table['create']) . '</td>';
											echo '<td data-order="' . $table['update'] . '">' . date($TS_ADVANCED_TABLESWP->TS_TablesWP_WordPress_Date . ' - ' . $TS_ADVANCED_TABLESWP->TS_TablesWP_WordPress_Time, $table['update']) . '</td>';
											echo '<td>';
												if (($TS_TablesWP_NeedMigration == false) && ($TS_ADVANCED_TABLESWP->TS_TablesWP_CurrentUserEditor == true)) {
													echo '<a class="ts-advanced-table-action-control ts-advanced-table-action-edit ts-advanced-table-tooltip-holder" href="' . admin_url('admin.php?page=TS_TablesWP_Modify&tableid=' . $table['id'] . '&action=edit') . '" target="_parent" data-table-id="' . $table['id'] . '" data-table-name="' . $table['name'] . '">';
														echo '<span class="ts-advanced-table-tooltip-content">' . __("Edit Table", "ts_visual_composer_extend") . ' #' . $table['id'] . '</span>';
														echo '<i class="ts-tableeditor-icon ts-tableeditor-edit5"></i>';
													echo '</a>';
													echo '<a class="ts-advanced-table-action-control ts-advanced-table-action-clone ts-advanced-table-tooltip-holder" href="' . admin_url('admin.php?page=TS_TablesWP_Modify&tableid=' . $table['id'] . '&action=clone') . '" target="_parent" data-table-id="' . $table['id'] . '" data-table-name="' . $table['name'] . '">';
														echo '<span class="ts-advanced-table-tooltip-content">' . __("Clone Table", "ts_visual_composer_extend") . ' #' . $table['id'] . '</span>';
														echo '<i class="ts-tableeditor-icon ts-tableeditor-copy4"></i>';
													echo '</a>';
													echo '<a class="ts-advanced-table-action-control ts-advanced-table-action-delete ts-advanced-table-tooltip-holder" href="' . admin_url('admin.php?page=TS_TablesWP_Modify&tableid=' . $table['id'] . '&action=delete') . '" target="_parent" data-table-id="' . $table['id'] . '" data-table-name="' . $table['name'] . '">';
														echo '<span class="ts-advanced-table-tooltip-content">' . __("Delete Table", "ts_visual_composer_extend") . ' #' . $table['id'] . '</span>';
														echo '<i class="ts-tableeditor-icon ts-tableeditor-trash4"></i>';
													echo '</a>';
													echo '<a class="ts-advanced-table-action-control ts-advanced-table-action-export ts-advanced-table-tooltip-holder" href="' . admin_url('admin.php?page=TS_TablesWP_Export&direct=false&tableid=' . $table['id']) . '" target="_parent" data-table-id="' . $table['id'] . '" data-table-name="' . $table['name'] . '">';
														echo '<span class="ts-advanced-table-tooltip-content">' . __("Export Table", "ts_visual_composer_extend") . ' #' . $table['id'] . '</span>';
														echo '<i class="ts-tableeditor-icon ts-tableeditor-external4"></i>';
													echo '</a>';
													echo '<a class="ts-advanced-table-action-control ts-advanced-table-action-shortcode ts-advanced-table-tooltip-holder cs-shortcode ts-advanced-table-generator-trigger" href="#" target="_parent" data-table-id="' . $table['id'] . '" data-table-name="' . $table['name'] . '" data-editor-id="ts-advanced-table-generator-input">';
														echo '<span class="ts-advanced-table-tooltip-content">' . __("Generate Full Shortcode for Table", "ts_visual_composer_extend") . ' #' . $table['id'] . '</span>';
														echo '<i class="ts-tableeditor-icon ts-tableeditor-shortcode1"></i>';
													echo '</a>';
												} else if (($TS_TablesWP_NeedMigration == false) && ($TS_ADVANCED_TABLESWP->TS_TablesWP_CurrentUserEditor == false)) {
													echo '<a class="ts-advanced-table-action-control ts-advanced-table-action-shortcode ts-advanced-table-tooltip-holder cs-shortcode ts-advanced-table-generator-trigger" href="#" target="_parent" data-table-id="' . $table['id'] . '" data-table-name="' . $table['name'] . '" data-editor-id="ts-advanced-table-generator-input" style="margin-left: 0;">';
														echo '<span class="ts-advanced-table-tooltip-content">' . __("Generate Full Shortcode for Table", "ts_visual_composer_extend") . ' #' . $table['id'] . '</span>';
														echo '<i class="ts-tableeditor-icon ts-tableeditor-shortcode1"></i>';
													echo '</a>';
												} else if (($TS_TablesWP_NeedMigration == true) && ($TS_ADVANCED_TABLESWP->TS_TablesWP_CurrentUserEditor == true)) {
													echo '<span style="color: #ff0000;">' . __("Please complete the table migration process, before any other actions can be taken on this table.", "ts_visual_composer_extend") . '</span>';
												} else {
													echo '<span style="color: #ff0000;">' . __("There are currently no table actions available.", "ts_visual_composer_extend") . '</span>';
												}
											echo '</td>';
											echo '<td>';
												echo '<span class="ts-advanced-table-shortcode-basic">[TS_Advanced_Tables id="' . $table['id'] . '"]</span>';
											echo '</td>';
											echo '<td>' . __("Rows:", "ts_visual_composer_extend") . ' ' . $table['rows'] . ' / ' . __("Columns:", "ts_visual_composer_extend") . ' ' . $table['columns'] . ' / ' . __("Merged Cells:", "ts_visual_composer_extend") . ' ' . ($table['merged'] > 0 ? __("Yes", "ts_visual_composer_extend") :__("No", "ts_visual_composer_extend")) . '</td>';
											echo '<td>';
												if ($table['info'] != '') {
													echo preg_replace('/\\\"/',"\"", (rawurldecode(base64_decode(strip_tags($table['info'])))));
												} else {
													echo __("No Description available.", "ts_visual_composer_extend");
												}
											echo '</td>';
											echo '<td data-categories="' . (((isset($table['categories'])) && ($table['categories'] != '')) ? $table['categories'] : 'ts-advanced-table-categories-none') . '">';
												if ((isset($table['categories'])) && ($table['categories'] != '')) {
													$TS_TablesWP_Categories = explode(",", $table['categories']);
												} else {
													$TS_TablesWP_Categories = array();
												}
												foreach ($TS_TablesWP_Categories as $categories => $category) {
													if (!in_array($category, $TS_TablesWP_Define_Cross)) {
														unset($TS_TablesWP_Categories[$categories]);
													}
												}												
												if (count($TS_TablesWP_Categories) > 0) {
													sort($TS_TablesWP_Categories);
													foreach ($TS_TablesWP_Categories as $categories => $category) {
														$TS_TABLESWP_SLUGToName		= TS_TablesWP_GetSlugData($TS_TablesWP_Define_Copy, $category, 'name');
														$TS_TABLESWP_SLUGToLevel	= TS_TablesWP_GetSlugData($TS_TablesWP_Define_Copy, $category, 'level');
														if ($TS_TABLESWP_SLUGToName != '') {
															echo '<span class="ts-advanced-table-span-cats" data-slug="' . $category . '" data-level="' . $TS_TABLESWP_SLUGToLevel . '">' . $TS_TABLESWP_SLUGToName . '</span>';
														}											
													}
												} else {
													echo '<span class="ts-advanced-table-span-none" data-slug="ts-advanced-table-categories-none">' . __("No Categories", "ts_visual_composer_extend") . '</span>';
												}
											echo '</td>';
											echo '<td>';
												echo (((isset($table['categories'])) && ($table['categories'] != '')) ? $table['categories'] : 'ts-advanced-table-categories-none');
											echo '</td>';
										echo '</tr>';
									}
								}
							}
						?>
					</tbody>
				</table>
			</div>
		</div>
    </form>