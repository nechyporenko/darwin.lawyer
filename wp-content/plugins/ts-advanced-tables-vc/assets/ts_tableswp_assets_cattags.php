<?php
	global $TS_ADVANCED_TABLESWP;
	global $wpdb;
	
	// Function to Generate Tagmanager Input
	function TS_TablesWP_TagManager_Settings_Field($settings, $value) {
		$param_name     	= isset($settings['param_name'])    ? $settings['param_name']   : '';
		$type           	= isset($settings['type'])          ? $settings['type']         : '';
		// Global Settings
		$suffix         	= isset($settings['suffix'])        ? $settings['suffix']       : '';
		$class          	= isset($settings['class'])         ? $settings['class']        : '';
		// Tag Editor Settings
		$delimiter			= isset($settings['delimiter'])		? $settings['delimiter'] 	: ' ';
		$lowercase			= isset($settings['lowercase'])		? $settings['lowercase']	: 'true';
		$numbersonly		= isset($settings['numbersonly'])	? $settings['numbersonly']	: 'false';
		$sortable			= isset($settings['sortable'])		? $settings['sortable']		: 'true';
		$clickdelete		= isset($settings['clickdelete'])	? $settings['clickdelete']	: 'false';
		$placeholder		= isset($settings['placeholder'])	? $settings['placeholder'] 	: '';
		$randomizer			= rand(100000, 999999);
		$output         	= '';
		$delimiter			= '' . $delimiter . ';';
		$output .= '<div id="ts-tag-editor-wrapper-' . $randomizer . '"class="ts-tag-editor-wrapper" data-initialized="false" data-value="' . $value . '" data-sortable="' . $sortable . '" data-clickdelete="' . $clickdelete . '" data-delimiter="' . $delimiter . '" data-lowercase="' . $lowercase . '" data-numbersonly="' . $numbersonly . '" data-placeholder="' . $placeholder . '">';
			$output .= '<input id="ts-tag-editor-input-' . $randomizer . '" class="ts-tag-editor-input ' . $param_name . ' ' . $type . '" name="' . $param_name . '" type="text" value="' . $value . '"/>';
		$output .= '</div>';
		return $output;
	}
	
	// Page Load/Save Routines
	if (isset($_POST['Save'])) {
		// Render Preloader Animation
		echo '<div id="ts_vcsc_extend_settings_save" style="position: relative; margin: 20px auto 20px auto; width: 128px; height: 128px;">';
			echo TS_TablesWP_CreatePreloaderCSS("ts-settings-panel-loader", "", 4, "false");
		echo '</div>';   
        // Retrieve Categories Settings
        $TS_TablesWP_Categories_Holder          		= trim ($_POST['ts-advanced-table-categories-holder']);		
		$TS_TablesWP_Categories_Changed          		= trim ($_POST['ts-advanced-table-categories-changed']);
		$TS_TablesWP_Categories_Deleted          		= trim ($_POST['ts-advanced-table-categories-deleted']);
		// Get All Tables From Database
		$TS_TablesWP_Categories_BaseName				= $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginMYSQL;			
		$TS_TablesWP_Categories_Results 				= $wpdb->get_results("SELECT id, number, other FROM $TS_TablesWP_Categories_BaseName ORDER BY number", ARRAY_A);
		$TS_TablesWP_Categories_Count					= 0;
		$TS_TablesWP_Categories_Other					= array();
		// Remove All Deleted Categories From Tables
		$TS_TablesWP_Categories_Changed					= urldecode($TS_TablesWP_Categories_Changed);
		$TS_TablesWP_Categories_Changed					= json_decode($TS_TablesWP_Categories_Changed);
		if ((count($TS_TablesWP_Categories_Changed) > 0) && (count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables) > 0)) {
			foreach ($TS_TablesWP_Categories_Changed as $categories => $category) {
				// Loop Table Reference Data
				foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables as $references => &$reference) {
					if ((isset($reference['categories'])) && ($reference['categories'] != '')) {
						$TS_TablesWP_Holder_Categories	= explode(',', $reference['categories']);
						if (!is_array($TS_TablesWP_Holder_Categories)) {
							$TS_TablesWP_Holder_Categories = array();
						}
						$TS_TablesWP_Holder_Position	= array_search($category->slug_old, $TS_TablesWP_Holder_Categories);						
						if ($TS_TablesWP_Holder_Position !== false) {
							$TS_TablesWP_Holder_Categories[$TS_TablesWP_Holder_Position] = $category->slug_new;
							$reference['categories']	= implode(',', $TS_TablesWP_Holder_Categories);
						}						
					}
				}
				// Loop Table Database Data
				foreach ($TS_TablesWP_Categories_Results as &$table) {
					$TS_TablesWP_Categories_Count		= (isset($table['change']) ? (int)$table['change'] : 0);
					$TS_TablesWP_Categories_Other		= json_decode($table['other']);
					if ((isset($TS_TablesWP_Categories_Other->categories)) && ($TS_TablesWP_Categories_Other->categories != '')) {
						$TS_TablesWP_Holder_Categories	= explode(',', $TS_TablesWP_Categories_Other->categories);
						if (!is_array($TS_TablesWP_Holder_Categories)) {
							$TS_TablesWP_Holder_Categories = array();
						}
						$TS_TablesWP_Holder_Position	= array_search($category->slug_old, $TS_TablesWP_Holder_Categories);						
						if ($TS_TablesWP_Holder_Position !== false) {
							$TS_TablesWP_Holder_Categories[$TS_TablesWP_Holder_Position] = $category->slug_new;
							$TS_TablesWP_Categories_Other->categories		= implode(',', $TS_TablesWP_Holder_Categories);
							$table['other']				= json_encode($TS_TablesWP_Categories_Other);
							$TS_TablesWP_Categories_Count++;
						}
					}
					$table['change']					= $TS_TablesWP_Categories_Count;
				}
			}
		}
		// Rename All Changed Categories For Tables
		$TS_TablesWP_Categories_Deleted					= urldecode($TS_TablesWP_Categories_Deleted);
		$TS_TablesWP_Categories_Deleted					= json_decode($TS_TablesWP_Categories_Deleted);
		if ((count($TS_TablesWP_Categories_Deleted) > 0) && (count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables) > 0)) {
			foreach ($TS_TablesWP_Categories_Deleted as $categories => $category) {
				// Loop Table Reference Data
				foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables as $references => &$reference) {
					if ((isset($reference['categories'])) && ($reference['categories'] != '')) {
						$TS_TablesWP_Holder_Categories	= explode(',', $reference['categories']);
						if (!is_array($TS_TablesWP_Holder_Categories)) {
							$TS_TablesWP_Holder_Categories = array();
						}
						$TS_TablesWP_Holder_Position	= array_search($category->slug, $TS_TablesWP_Holder_Categories);						
						if ($TS_TablesWP_Holder_Position !== false) {
							unset($TS_TablesWP_Holder_Categories[$TS_TablesWP_Holder_Position]);
							$reference['categories']	= implode(',', $TS_TablesWP_Holder_Categories);
						}
					}
				}
				// Loop Table Database Data
				foreach ($TS_TablesWP_Categories_Results as &$table) {
					$TS_TablesWP_Categories_Count		= (isset($table['change']) ? (int)$table['change'] : 0);
					$TS_TablesWP_Categories_Other		= json_decode($table['other']);
					if ((isset($TS_TablesWP_Categories_Other->categories)) && ($TS_TablesWP_Categories_Other->categories != '')) {
						$TS_TablesWP_Holder_Categories	= explode(',', $TS_TablesWP_Categories_Other->categories);
						if (!is_array($TS_TablesWP_Holder_Categories)) {
							$TS_TablesWP_Holder_Categories = array();
						}
						$TS_TablesWP_Holder_Position	= array_search($category->slug, $TS_TablesWP_Holder_Categories);						
						if ($TS_TablesWP_Holder_Position !== false) {
							unset($TS_TablesWP_Holder_Categories[$TS_TablesWP_Holder_Position]);
							$TS_TablesWP_Categories_Other->categories		= implode(',', $TS_TablesWP_Holder_Categories);
							$table['other']				= json_encode($TS_TablesWP_Categories_Other);
							$TS_TablesWP_Categories_Count++;
						}												
					}
					$table['change']					= $TS_TablesWP_Categories_Count;
				}
			}
		}
		// Update Categories Data
		update_option("ts_tablesplus_extend_settings_categories", $TS_TablesWP_Categories_Holder);
		// Update Table Reference Data
		update_option("ts_tablesplus_extend_settings_tables", $TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables);
		// Update Table Database Data
		if ((count($TS_TablesWP_Categories_Changed) > 0) || (count($TS_TablesWP_Categories_Deleted) > 0)) {
			foreach ($TS_TablesWP_Categories_Results as $table) {				
				if ((isset($table['change'])) && ((int)$table['change'] > 0)) {
					$wpdb->update($TS_TablesWP_Categories_BaseName,
						array(
							"other" 					=> stripcslashes($table['other']),			
						), array(
							'id' 						=> $table['id']
						), null, array('%d')
					);
				}
			}
		}
        // Reload Page
		echo'<script>window.location="' . admin_url('admin.php?page=TS_TablesWP_Categories') . '";</script>';
		Exit();
	} else {
		// Retrieve Pre-Defined Categories from Database
		$TS_TablesWP_Categories_Holder					= get_option("ts_tablesplus_extend_settings_categories", '');	
		// Read All Assigned Categories from Existing Tables	
		$TS_TablesWP_Holder_Single						= array();
		$TS_TablesWP_Holder_Categories 					= array();
		$TS_TablesWP_Holder_Matched 					= array();
		$TS_TablesWP_Holder_Defined 					= json_decode(rawurldecode($TS_TablesWP_Categories_Holder));
		if (!is_array($TS_TablesWP_Holder_Defined)) {
			$TS_TablesWP_Holder_Defined					= array();
		}
		if (count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables) > 0) {
			foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables as $tables => $table) {
				if ((isset($table['categories'])) && ($table['categories'] != '')) {
					$TS_TablesWP_Holder_Single			= explode(',', $table['categories']);
					$TS_TablesWP_Holder_Categories		= array_merge($TS_TablesWP_Holder_Categories, $TS_TablesWP_Holder_Single);
				}			
			}
			$TS_TablesWP_Holder_Matched					= array_count_values($TS_TablesWP_Holder_Categories);
		}
		// Extend Pre-Defined Categories with Usage Count
		foreach ($TS_TablesWP_Holder_Defined as $categories => $category) {
			if (isset($TS_TablesWP_Holder_Matched[$category->id])) {
				$category->count 						= $TS_TablesWP_Holder_Matched[$category->id];
			} else {
				$category->count						= 0;
			}
			if (isset($category->children)) {
				foreach ($category->children as $sublevels => $level1) {
					if (isset($TS_TablesWP_Holder_Matched[$level1->id])) {
						$level1->count					= $TS_TablesWP_Holder_Matched[$level1->id];
					} else {
						$level1->count					= 0;
					}
					if (isset($level1->children)) {
						foreach ($level1->children as $sublevels => $level2) {
							if (isset($TS_TablesWP_Holder_Matched[$level2->id])) {
								$level2->count			= $TS_TablesWP_Holder_Matched[$level2->id];
							} else {
								$level2->count			= 0;
							}
						}
					}
				}
			}
		}
		$TS_TablesWP_Categories_Holder					= rawurlencode(json_encode($TS_TablesWP_Holder_Defined));
	}
?>
	<div class="ts-vcsc-settings-page-header">
		<h2><span class="dashicons dashicons-grid-view"></span><?php echo __("Tablenator - Advanced Tables for WordPress", "ts_visual_composer_extend"); ?> v<?php echo TS_TablesWP_GetPluginVersion(); ?> ... <?php echo __("Categories Manager", "ts_visual_composer_extend"); ?></h2>
		<div class="clear"></div>
	</div>
	<form id="ts-advanced-table-cattags-form" class="ts-advanced-table-cattags-form" name="ts-advanced-table-cattags-form" autocomplete="off" enctype="multipart/form-data" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
        <span id="ts-advanced-table-check-wrapper" style="display: none !important; margin-bottom: 20px;">
            <input type="text" style="width: 20%;" id="ts-advanced-table-check-true" name="ts-advanced-table-check-true" value="0" size="100">
        </span>
		<div id="ts-vcsc-advancedtables-validation-messages" style="display: none !important;">
			<span id="ts-advancedtables-validation-plugin-name" style="display: none;"><?php echo __("Tablenator - Advanced Tables for WordPress", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advancedtables-validation-errors-single" style="display: none;"><?php echo __("Please fix the following Error:", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advancedtables-validation-errors-multiple" style="display: none;"><?php echo __("Please fix the following %d Errors:", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advancedtables-validation-popup-success" style="display: none;"><?php echo __("Your settings validated correctly. Do you want to save your settings?", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advancedtables-validation-popup-errors" style="display: none;"><?php echo __("Please correct all errors that have been found before your settings can be saved!", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advancedtables-validation-popup-close" style="display: none;"><?php echo __("Close", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advancedtables-validation-popup-confirm" style="display: none;"><?php echo __("Confirm", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advancedtables-validation-popup-understood" style="display: none;"><?php echo __("Understood", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advancedtables-validation-popup-yes" style="display: none;"><?php echo __("Yes", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advancedtables-validation-popup-no" style="display: none;"><?php echo __("No", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advancedtables-validation-popup-gotit" style="display: none;"><?php echo __("Got It!", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advancedtables-validation-popup-cancel" style="display: none;"><?php echo __("Cancel", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advancedtables-validation-popup-saved" style="display: none;"><?php echo __("All settings have been successfully saved!", "ts_visual_composer_extend"); ?></span>
			<span id="ts-advancedtables-validation-popup-partial" style="display: none;"><?php echo __("Problem: Settings have been saved but are not complete. Please fix the following Errors:", "ts_visual_composer_extend"); ?></span>
		</div>
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
				<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to save your pre-defined table categories.", "ts_visual_composer_extend"); ?></span>
				<button id="ts-advanced-table-save-button" class="ts-advanced-tables-button-main ts-advanced-tables-button-blue ts-advanced-tables-button-save" style="margin: 0;">
					<?php echo __("Save Categories", "ts_visual_composer_extend"); ?>
				</button>
			</div>
            <div class="ts-advanced-tables-button-wrapper" style="display: none;">
                <button id="ts-advanced-table-trigger-button" type="submit" name="Save"><?php echo __("Save Categories", "ts_visual_composer_extend"); ?></button>
            </div>
		</div>		
		<div class="clearFixMe"></div>
		<img id="ts-advanced-table-categories-banner" style="display: block; width: 100%; max-width: 800px; height: auto; margin: 20px auto 20px auto;" src="<?php echo TS_TablesWP_GetResourceURL('images/banners/banner_categories.jpg'); ?>">
		<div id="ts-advanced-table-categories-wrapper" style="width: 100%; margin-top: 0px;">
			<div class="ts-vcsc-section-main">
				<div class="ts-vcsc-section-title ts-vcsc-section-show"><i class="dashicons dashicons-category"></i><?php echo __("Table Categories Manager", "ts_visual_composer_extend"); ?></div>
				<div class="ts-vcsc-section-content" style="display: block;">
					<div class="ts-vcsc-notice-field ts-vcsc-warning" style="margin-top: 10px; margin-bottom: 20px; font-size: 13px; text-align: justify;">
						<?php echo __("You can manage your pre-defined table categories by using the category manager below. Categories can be dragged and dropped for a custom order and to place them as a sub-category for another category. In total, the editor allows you to create up to 2 nested levels of sub-categories.", "ts_visual_composer_extend"); ?>
					</div>
					<div class="ts-vcsc-notice-field ts-vcsc-critical" style="margin-top: 0px; margin-bottom: 20px; font-size: 13px; text-align: justify;">
						<?php echo __("Table categories can only be added, changed or deleted by using the category manager provided below. You will not be able to create any new categories from within the table editor, as the editor will only provide a category selection based on the definitions below. Deleting a category will also remove it from any table the category has been assigned to; renaming a category will also rename the category for any table it has been used with.", "ts_visual_composer_extend"); ?>
					</div>
					<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder" style="margin: 0 20px 10px 0;">
						<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to add a new pre-defined category, so it can be assigned to any table.", "ts_visual_composer_extend"); ?></span>
						<button id="ts-advanced-table-addnew-button" class="ts-advanced-tables-button-main ts-advanced-tables-button-green ts-advanced-tables-button-plus" style="margin: 0;">
							<?php echo __("Add New Category", "ts_visual_composer_extend"); ?>
						</button>
					</div>
					<div id="ts-advanced-table-categories-alert" class="ts-advanced-table-categories-alert" data-title="<?php echo __("Add New Category", "ts_visual_composer_extend"); ?>" data-submit="<?php echo __("Submit", "ts_visual_composer_extend"); ?>" data-cancel="<?php echo __("Cancel", "ts_visual_composer_extend"); ?>" data-other="" style="display: none;">
						<label for="ts-advanced-table-categories-addnew"><?php echo __("Please provide the name for the new category:", "ts_visual_composer_extend"); ?></label>
						<input id="ts-advanced-table-categories-addnew" class="ts-advanced-table-categories-addnew" name="ts-advanced-table-categories-addnew" type="text" value="" data-value=""/>
						<label for="ts-advanced-table-categories-summary" style="display: block; margin-top: 20px;"><?php echo __("Please enter an optional description for the category:", "ts_visual_composer_extend"); ?></label>
						<textarea id="ts-advanced-table-categories-summary" class="ts-advanced-table-categories-summary" name="ts-advanced-table-categories-summary" max-length="500"></textarea>
						<div class="ts-vcsc-notice-field ts-vcsc-warning" style="margin-top: 20px; margin-bottom: 0px; font-size: 14px; text-align: justify; display: none;">
							<?php echo __("A category with this name or underlying slug already exists; please use a different name for the category.", "ts_visual_composer_extend"); ?>
						</div>
					</div>				
					<div id="ts-advanced-table-categories-edit" class="ts-advanced-table-categories-edit" data-title="<?php echo __("Edit Category", "ts_visual_composer_extend"); ?>" data-submit="<?php echo __("Submit", "ts_visual_composer_extend"); ?>" data-cancel="<?php echo __("Cancel", "ts_visual_composer_extend"); ?>" data-other="" style="display: none;">
						<label for="ts-advanced-table-categories-change"><?php echo __("Please adjust the name for the category:", "ts_visual_composer_extend"); ?></label>
						<input id="ts-advanced-table-categories-change" class="ts-advanced-table-categories-change" name="ts-advanced-table-categories-change" type="text" value="" data-value=""/>
						<label for="ts-advanced-table-categories-describe" style="display: block; margin-top: 20px;"><?php echo __("Please adjust the optional description for the category:", "ts_visual_composer_extend"); ?></label>
						<textarea id="ts-advanced-table-categories-describe" class="ts-advanced-table-categories-describe" name="ts-advanced-table-categories-describe" max-length="500"></textarea>
						<div class="ts-vcsc-notice-field ts-vcsc-warning" style="margin-top: 20px; margin-bottom: 0px; font-size: 14px; text-align: justify; display: none;">
							<?php echo __("A category with this name or underlying slug already exists; please use a different name for the category.", "ts_visual_composer_extend"); ?>
						</div>
					</div>
					<div id="ts-advanced-table-categories-messages" class="ts-advanced-table-categories-messages" style="display: none;">
						<span id="ts-advanced-table-categories-noinfo" class="ts-advanced-table-categories-noinfo"><?php echo __("No description available.", "ts_visual_composer_extend"); ?></span>
						<span id="ts-advanced-table-categories-leveltitle" class="ts-advanced-table-categories-leveltitle"><?php echo __("Can not delete this category:", "ts_visual_composer_extend"); ?></span>
						<span id="ts-advanced-table-categories-levelcheck" class="ts-advanced-table-categories-levelcheck"><?php echo __("This category includes one or more sublevels and can not be deleted until all sub-categories have been removed first.", "ts_visual_composer_extend"); ?></span>
						<span id="ts-advanced-table-categories-deletetitle" class="ts-advanced-table-categories-deletetitle"><?php echo __("Delete this category?", "ts_visual_composer_extend"); ?></span>
						<span id="ts-advanced-table-categories-deletecheck" class="ts-advanced-table-categories-deletecheck"><?php echo __("Do you really want to delete this category? Once saved, a deleted category will automatically be removed from any table it has been assigned to.", "ts_visual_composer_extend"); ?></span>
					</div>
					<div id="ts-advanced-table-categories-transfer" class="ts-advanced-table-categories-transfer" style="display: none;">
						<textarea id="ts-advanced-table-categories-changed" class="ts-advanced-table-categories-changed" name="ts-advanced-table-categories-changed"></textarea>
						<textarea id="ts-advanced-table-categories-deleted" class="ts-advanced-table-categories-deleted" name="ts-advanced-table-categories-deleted"></textarea>
						<textarea id="ts-advanced-table-categories-holder" class="ts-advanced-table-categories-holder" name="ts-advanced-table-categories-holder"><?php echo $TS_TablesWP_Categories_Holder; ?></textarea>
					</div>					
					<ol id="ts-advanced-table-categories-listing" class="ts-advanced-table-categories-listing sortable"></ol>
				</div>
			</div>
		</div>
		<div id="ts-advanced-table-tags-wrapper" style="width: 100%; margin-top: 30px; display: none;">
			<div class="ts-vcsc-section-main">
				<div class="ts-vcsc-section-title ts-vcsc-section-show"><i class="dashicons dashicons-tag"></i><?php echo __("Table Tags", "ts_visual_composer_extend"); ?></div>
				<div class="ts-vcsc-section-content" style="display: block;">
					<div class="ts-vcsc-notice-field ts-vcsc-warning" style="margin-top: 10px; margin-bottom: 20px; font-size: 13px; text-align: justify;">
						<?php echo __("Please provide your desired predefined table tags by using the input below. Tags are limited to alphanumeric characters aA-Zz and 0-9; any special characters will be removed automatically. You can drag the tags in order to create a custom order in which the tags will be provided in the table editor.", "ts_visual_composer_extend"); ?>
					</div>
					<?php
						/*$settings = array(
							"param_name"		=> "ts-advanced-table-tags-holder",
							"delimiter"			=> ",",
							"lowercase"			=> "true",
							"numbersonly"		=> 'false',
							"sortable"			=> 'true',
							"clickdelete"		=> 'true',
							"value"				=> $TS_TablesWP_Tags_Holder,
							"default"			=> "",
						);
						echo TS_TablesWP_TagManager_Settings_Field($settings, $TS_TablesWP_Tags_Holder, '');*/
					?>
				</div>
			</div>
		</div>
	</form>