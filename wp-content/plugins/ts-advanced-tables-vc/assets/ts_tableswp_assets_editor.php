<?php
    global $TS_ADVANCED_TABLESWP;
	
	function TS_TablesWP_FontIconPicker($settings, $value) {
		global $TS_ADVANCED_TABLESWP;
		$param_name     	= isset($settings['param_name']) ? $settings['param_name'] : '';
		$type           	= isset($settings['type']) ? $settings['type'] : '';
		$default			= isset($settings['default']) ? $settings['default'] : '';
		$parameters			= isset($settings['settings']) ? $settings['settings'] : array();
		// Extract Custom Icon Picker Settings
		$icons_type			= isset($parameters['type']) ? $parameters['type'] : "extensions";
		$icons_source		= $TS_ADVANCED_TABLESWP->TS_TablesWP_List_Icons_Compliant;
		// Check Value
		if (($value == "") && ($default != "")) {
			$value			= $default;
		}
		// Retrieve Settings
		$icons_override		= isset($parameters['override']) ? $parameters['override'] : "false";
		if ($icons_override == true) {
			$icons_override	= "true";
		} else if ($icons_override == false) {
			$icons_override	= "false";
		}
		$icons_empty		= isset($parameters['emptyIcon']) ? $parameters['emptyIcon'] : "true";
		if ($icons_empty == true) {
			$icons_empty	= "true";
		} else if ($icons_empty == false) {
			$icons_empty	= "false";
		}
		$icons_transparent 	= isset($parameters['emptyIconValue']) ? $parameters['emptyIconValue'] : "";
		$icons_search		= isset($parameters['hasSearch']) ? $parameters['hasSearch'] : "true";
		if ($icons_search == true) {
			$icons_search	= "true";
		} else if ($icons_search == false) {
			$icons_search	= "false";
		}				
		$icons_pagination	= isset($parameters['iconsPerPage']) ? $parameters['iconsPerPage'] : 240;
		// Other Settings
		$randomizer			= mt_rand(999999, 9999999);
		$output         	= '';
		// Icon Picker Output
		$output .= '<div id="ts-font-icons-picker-parent-' . $randomizer . '" class="ts-font-icons-picker-parent">';
			$output .= '<div id="ts-font-icons-picker-' . $param_name . '" class="ts-visual-selector ts-font-icons-picker" data-value="' . $value . '" data-theme="inverted" data-empty="' . $icons_empty . '" data-transparent="' . $icons_transparent . '" data-search="' . $icons_search . '" data-pagecount="' . $icons_pagination . '">';
				$iconGroups 			= array();
				$output .= '<select id="' . $param_name . '" name="' . $param_name . '" class="wpb_vc_param_value ' . $param_name . ' ' . $type . '" value="' . $value . '">';            
					foreach ($icons_source as $group => $icons) {									
						if (!is_array($icons) || !is_array(current($icons))) {
							$font		= "";
						} else {									
							$font		= str_replace("(", "", esc_attr($group));
							$font		= str_replace(")", "", $font);
						}
						if (($font != "") && (!in_array($font, $iconGroups))) {
							$output .= '<optgroup label="' . $font . '">';
						}									
						if (!is_array($icons) || !is_array(current($icons))) {
							$class_key      = key($icons);
							$class_label	= (isset($icons[$class_key]) ? $icons[$class_key] : $class_key);
							$class_group    = explode('-', esc_attr($class_key));
							if (($class_group[0] != "dashicons") && ($class_group[0] != "transparent")) {
								if ($value == esc_attr($class_key)) {
									$output .= '<option value="' . esc_attr($class_key) . '" selected="selected">' . esc_attr($class_label) . '</option>';
								} else {
									$output .= '<option value="' . esc_attr($class_key) . '">' . esc_attr($class_label) . '</option>';
								}
							} else {
								if ($value == esc_attr($class_key)) {
									$output .= '<option value="' . esc_attr($class_key) . '" selected="selected">' . esc_attr($class_label) . '</option>';
								} else {
									$output .= '<option value="' . esc_attr($class_key) . '">' . esc_attr($class_label) . '</option>';
								}
							}
						} else {
							foreach ($icons as $key => $label) {
								$class_key      = key($label);
								$class_label	= (isset($label[$class_key]) ? $label[$class_key] : $class_key);
								$class_group    = explode('-', esc_attr($class_key));
								$font           = str_replace("(", "", strtolower(strtolower(esc_attr($group))));
								$font           = str_replace(")", "", strtolower($font));
								if (($class_group[0] != "dashicons") && ($class_group[0] != "transparent")) {
									if ($value == esc_attr($class_key)) {
										$output .= '<option value="' . esc_attr($class_key) . '" selected="selected">' . esc_attr($class_label) . '</option>';
									} else {
										$output .= '<option value="' . esc_attr($class_key) . '">' . esc_attr($class_label) . '</option>';
									}
								} else {
									if ($value == esc_attr($class_key)) {
										$output .= '<option value="' . esc_attr($class_key) . '" selected="selected">' . esc_attr($class_label) . '</option>';
									} else {
										$output .= '<option value="' . esc_attr($class_key) . '">' . esc_attr($class_label) . '</option>';
									}
								}
							}
						}									
						if (($font != "") && (!in_array($font, $iconGroups))) {
							$output .= '</optgroup>';
							array_push($iconGroups, $font);
						}
					}
				$output .= '</select>';
			$output .= '</div>';
		$output .= '</div>';

		return $output;
	}
?>
<div id="ts-advanced-table-container" class="ts-advanced-table-container">
    <div id="ts-advanced-table-preloader" class="ts-advanced-table-preloader" style="display: none;">
        <?php echo TS_TablesWP_CreatePreloaderCSS("ts-settings-panel-loader", "", 0, "false"); ?>
    </div>
	<div id="ts-advanced-table-overlay-wrapper" class="ts-advanced-table-overlay-wrapper">
		<div id="ts-advanced-table-overlay-layer" class="ts-advanced-table-overlay-layer"></div>
		<div id="ts-advanced-table-hidden-wrapper" class="ts-advanced-table-hidden-wrapper" style="display: none;">
			<div id="ts-advanced-table-link-wrapper" class="ts-advanced-table-link-wrapper" style="display: none;">
				<input id="ts-advanced-table-link-input" class="ts-advanced-table-link-input" name="ts-advanced-table-link-input" type="text">        
			</div>
			<div id="ts-advanced-table-transfer-wrapper" class="ts-advanced-table-transfer-wrapper" style="display: none;">
				<textarea id="ts-advanced-table-transfer-info" class="ts-advanced-table-transfer-info" name="ts-advanced-table-transfer-info" rows="20"><?php echo $TS_TablesWP_Editor_Info; ?></textarea>
				<textarea id="ts-advanced-table-transfer-data" class="ts-advanced-table-transfer-data" name="ts-advanced-table-transfer-data" rows="20"><?php echo $TS_TablesWP_Editor_Data; ?></textarea>
				<textarea id="ts-advanced-table-transfer-meta" class="ts-advanced-table-transfer-meta" name="ts-advanced-table-transfer-meta" rows="20"><?php echo $TS_TablesWP_Editor_Meta; ?></textarea>
				<textarea id="ts-advanced-table-transfer-defaults" class="ts-advanced-table-transfer-defaults" name="ts-advanced-table-transfer-defaults" rows="20"><?php echo $TS_TablesWP_Editor_Defaults; ?></textarea>
				<textarea id="ts-advanced-table-transfer-merged" class="ts-advanced-table-transfer-merged" name="ts-advanced-table-transfer-merged" rows="20"><?php echo $TS_TablesWP_Editor_Merged; ?></textarea>
				<textarea id="ts-advanced-table-transfer-other" class="ts-advanced-table-transfer-other" name="ts-advanced-table-transfer-other" rows="20"><?php echo $TS_TablesWP_Editor_Other; ?></textarea>
				<input id="ts-advanced-table-transfer-section" class="ts-advanced-table-transfer-section" name="ts-advanced-table-transfer-section" type="text" value="<?php echo $TS_TablesWP_Editor_Section; ?>">
				<input id="ts-advanced-table-transfer-rows" class="ts-advanced-table-transfer-rows" name="ts-advanced-table-transfer-rows" type="text" value="<?php echo $TS_TablesWP_Editor_Rows; ?>">
				<input id="ts-advanced-table-transfer-columns" class="ts-advanced-table-transfer-columns" name="ts-advanced-table-transfer-columns" type="text" value="<?php echo $TS_TablesWP_Editor_Columns; ?>">
				<input id="ts-advanced-table-transfer-savemeta" class="ts-advanced-table-transfer-savemeta" name="ts-advanced-table-transfer-savemeta" type="text" value="<?php echo $TS_TablesWP_Editor_SaveMeta; ?>">
				<input id="ts-advanced-table-transfer-formulas" class="ts-advanced-table-transfer-formulas" name="ts-advanced-table-transfer-formulas" type="text" value="<?php echo $TS_TablesWP_Editor_Formulas; ?>">
				<input id="ts-advanced-table-transfer-search" class="ts-advanced-table-transfer-search" name="ts-advanced-table-transfer-search" type="text" value="<?php echo $TS_TablesWP_Editor_Search; ?>">
				<input id="ts-advanced-table-transfer-context" class="ts-advanced-table-transfer-context" name="ts-advanced-table-transfer-context" type="text" value="<?php echo $TS_TablesWP_Editor_Context; ?>">
				<input id="ts-advanced-table-transfer-validator" class="ts-advanced-table-transfer-validator" name="ts-advanced-table-transfer-validator" type="text" value="<?php echo $TS_TablesWP_Editor_Validator; ?>">
				<input id="ts-advanced-table-transfer-charts" class="ts-advanced-table-transfer-charts" name="ts-advanced-table-transfer-charts" type="text" value="<?php echo $TS_TablesWP_Editor_Charts; ?>">		
				<input id="ts-advanced-table-transfer-fixrow" class="ts-advanced-table-transfer-fixrow" name="ts-advanced-table-transfer-fixrow" type="text" value="<?php echo $TS_TablesWP_Editor_FixRow; ?>">
				<input id="ts-advanced-table-transfer-fixcolumn" class="ts-advanced-table-transfer-fixcolumn" name="ts-advanced-table-transfer-fixcolumn" type="text" value="<?php echo $TS_TablesWP_Editor_FixColumn; ?>">		
				<input id="ts-advanced-table-transfer-database" class="ts-advanced-table-transfer-database" name="ts-advanced-table-transfer-database" type="text" value="<?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Migrated == true ? "true" : "false"); ?>">
				<input id="ts-advanced-table-transfer-cats" class="ts-advanced-table-transfer-cats" name="ts-advanced-table-transfer-cats" type="text" value="<?php echo implode(",", $TS_TablesWP_Editor_Cats); ?>">
				<input id="ts-advanced-table-transfer-tags" class="ts-advanced-table-transfer-tags" name="ts-advanced-table-transfer-tags" type="text" value="<?php echo implode(",", $TS_TablesWP_Editor_Tags); ?>">	
			</div>
			<div id="ts-advanced-table-language-wrapper" class="ts-advanced-table-language-wrapper" style="display: none;">
				<span id="ts-advanced-table-language-select-image" class="ts-advanced-table-language-select-image"><?php echo __("Select Image", "ts_visual_composer_extend"); ?></span>
				<span id="ts-advanced-table-language-submit-image" class="ts-advanced-table-language-submit-image"><?php echo __("Insert Image", "ts_visual_composer_extend"); ?></span>
				<span id="ts-advanced-table-language-yes" class="ts-advanced-table-language-yes"><?php echo __("Yes", "ts_visual_composer_extend"); ?></span>
				<span id="ts-advanced-table-language-no" class="ts-advanced-table-language-no"><?php echo __("No", "ts_visual_composer_extend"); ?></span>
				<span id="ts-advanced-table-language-submit" class="ts-advanced-table-language-submit"><?php echo __("Submit", "ts_visual_composer_extend"); ?></span>
				<span id="ts-advanced-table-language-cancel" class="ts-advanced-table-language-cancel"><?php echo __("Cancel", "ts_visual_composer_extend"); ?></span>
				<span id="ts-advanced-table-language-understood" class="ts-advanced-table-language-understood"><?php echo __("Understood!", "ts_visual_composer_extend"); ?></span>
				<span id="ts-advanced-table-language-ok" class="ts-advanced-table-language-ok"><?php echo __("OK", "ts_visual_composer_extend"); ?></span>
				<span id="ts-advanced-table-language-continue" class="ts-advanced-table-language-continue"><?php echo __("Continue", "ts_visual_composer_extend"); ?></span>
				<span id="ts-advanced-table-language-plugin" class="ts-advanced-table-language-plugin"><?php echo __("Tablenator - Advanced Tables for WordPress", "ts_visual_composer_extend"); ?></span>
				<span id="ts-advanced-table-language-validtext" class="ts-advanced-table-language-validtext"><?php echo __("Your table validated correctly. Do you want to save this table?", "ts_visual_composer_extend"); ?></span>
				<span id="ts-advanced-table-language-errortext" class="ts-advanced-table-language-errortext"><?php echo __("You forgot to provide a name for this table; the table can not be saved without a name.", "ts_visual_composer_extend"); ?></span>
				<span id="ts-advanced-table-language-deletetitle" class="ts-advanced-table-language-deletetitle"><?php echo __("Delete Table #%d", "ts_visual_composer_extend"); ?></span>
				<span id="ts-advanced-table-language-deletetext" class="ts-advanced-table-language-deletetext"><?php echo __("Do you really want to delete table #%d (%s)?", "ts_visual_composer_extend"); ?></span>
				<span id="ts-advanced-table-language-clonetitle" class="ts-advanced-table-language-clonetitle"><?php echo __("Clone Table #%d", "ts_visual_composer_extend"); ?></span>        
				<span id="ts-advanced-table-language-clonetext" class="ts-advanced-table-language-clonetext"><?php echo __("Please provide a new and unique name for the cloned version of table #%d (%s)?", "ts_visual_composer_extend"); ?></span>
				<span id="ts-advanced-table-language-clonesingle" class="ts-advanced-table-language-clonesingle"><?php echo __("Clone", "ts_visual_composer_extend"); ?></span>
                <span id="ts-advanced-table-language-blank" class="ts-advanced-table-language-blank"></span>
			</div>
			<div id="ts-advanced-table-linkpicker-wrapper" class="ts-advanced-table-linkpicker-wrapper" style="display: none;" data-title="<?php echo __("Link Information", "ts_visual_composer_extend"); ?>" data-confirm="<?php echo __("Submit", "ts_visual_composer_extend"); ?>" data-cancel="<?php echo __("Cancel", "ts_visual_composer_extend"); ?>" data-other="<?php echo __("Understood!", "ts_visual_composer_extend"); ?>">
				<div id="ts-advanced-table-link-container" class="ts-advanced-table-link-container">
					<div id="ts-advanced-table-link-href" class="ts-advanced-table-link-href">
						<label class="ts-advanced-table-link-label" for="ts-advanced-table-link-href-input"><?php echo __("Link URL *:", "ts_visual_composer_extend"); ?></label>
						<input type="text" id="ts-advanced-table-link-href-input" class="ts-advanced-table-link-input" name="ts-advanced-table-link-href-input">
					</div>
					<div id="ts-advanced-table-link-text" class="ts-advanced-table-link-text">
						<label class="ts-advanced-table-link-label" for="ts-advanced-table-link-text-input"><?php echo __("Link Text *:", "ts_visual_composer_extend"); ?></label>
						<input type="text" id="ts-advanced-table-link-text-input" class="ts-advanced-table-link-input" name="ts-advanced-table-link-text-input">
					</div>
					<span id="ts-advanced-table-link-required" class="ts-advanced-table-link-required"><?php echo __("* Required", "ts_visual_composer_extend"); ?></span>
					<div id="ts-advanced-table-link-title" class="ts-advanced-table-link-title">
						<label class="ts-advanced-table-link-label" for="ts-advanced-table-link-title-input"><?php echo __("Link Title:", "ts_visual_composer_extend"); ?></label>
						<input type="text" id="ts-advanced-table-link-title-input" class="ts-advanced-table-link-input" name="ts-advanced-table-link-title-input">
					</div>
					<div id="ts-advanced-table-link-insert" class="ts-advanced-table-link-insert">
						<label class="ts-advanced-table-link-label" for="ts-advanced-table-link-insert-select"><?php echo __("Link Insert:", "ts_visual_composer_extend"); ?></label>
						<select id="ts-advanced-table-link-insert-select" class="ts-advanced-table-link-insert-select" name="ts-advanced-table-link-insert-select">
							<option data-value="newline" selected="selected"><?php echo __("Create New Line for Link (After)", "ts_visual_composer_extend"); ?></option>
							<option data-value="addline"><?php echo __("Create New Line for Link (Before)", "ts_visual_composer_extend"); ?></option>
							<option data-value="inline"><?php echo __("Add Link to Existing Line (After)", "ts_visual_composer_extend"); ?></option>
							<option data-value="before"><?php echo __("Add Link to Existing Line (Before)", "ts_visual_composer_extend"); ?></option>
						</select>                
					</div>
					<div id="ts-advanced-table-link-target" class="ts-advanced-table-link-target">
						<div class="ts-switch-button ts-codestar-field-switcher" data-value="0">
							<div class="ts-codestar-fieldset">
								<label class="ts-codestar-label">
									<input id="ts-advanced-table-link-target-check" value="0" class="ts-codestar-checkbox ts-advanced-table-link-target-check" name="ts-advanced-table-link-target-check" type="checkbox">
									<em data-on="<?php echo __("Yes", "ts_visual_composer_extend"); ?>" data-off="<?php echo __("No", "ts_visual_composer_extend"); ?>"></em>
									<span></span>
								</label>
							</div>
						</div>
						<label class="labelToggleBox" for="ts-advanced-table-link-target-check"><?php echo __("Open In New Window/Tab", "ts_visual_composer_extend"); ?></label>
					</div>
					<div id="ts-advanced-table-link-follow" class="ts-advanced-table-link-follow">
						<div class="ts-switch-button ts-codestar-field-switcher" data-value="0">
							<div class="ts-codestar-fieldset">
								<label class="ts-codestar-label">
									<input id="ts-advanced-table-link-follow-check" value="0" class="ts-codestar-checkbox ts-advanced-table-link-follow-check" name="ts-advanced-table-link-follow-check" type="checkbox">
									<em data-on="<?php echo __("Yes", "ts_visual_composer_extend"); ?>" data-off="<?php echo __("No", "ts_visual_composer_extend"); ?>"></em>
									<span></span>
								</label>
							</div>
						</div>
						<label class="labelToggleBox" for="ts-advanced-table-link-follow-check"><?php echo __("Set 'No Follow' Attribute", "ts_visual_composer_extend"); ?></label>
					</div>
				</div>
			</div>
			<div id="ts-advanced-table-imagepicker-wrapper" class="ts-advanced-table-imagepicker-wrapper" style="display: none;" data-title="<?php echo __("Image Information", "ts_visual_composer_extend"); ?>" data-confirm="<?php echo __("Submit", "ts_visual_composer_extend"); ?>" data-cancel="<?php echo __("Cancel", "ts_visual_composer_extend"); ?>" data-other="<?php echo __("Understood!", "ts_visual_composer_extend"); ?>">
				<div id="ts-advanced-table-image-container" class="ts-advanced-table-image-container">
					<div id="ts-advanced-table-image-href" class="ts-advanced-table-image-href">
						<label class="ts-advanced-table-image-label" for="ts-advanced-table-image-href-input"><?php echo __("Path *:", "ts_visual_composer_extend"); ?></label>
						<input type="text" id="ts-advanced-table-image-href-input" class="ts-advanced-table-image-input" name="ts-advanced-table-image-href-input">
					</div>
					<span id="ts-advanced-table-image-required" class="ts-advanced-table-image-required"><?php echo __("* Required", "ts_visual_composer_extend"); ?></span>
					<div id="ts-advanced-table-image-alt" class="ts-advanced-table-image-alt">
						<label class="ts-advanced-table-image-label" for="ts-advanced-table-image-alt-input"><?php echo __("ALT:", "ts_visual_composer_extend"); ?></label>
						<input type="text" id="ts-advanced-table-image-alt-input" class="ts-advanced-table-image-input" name="ts-advanced-table-image-alt-input">
					</div>
				</div>
			</div>
			<div id="ts-advanced-table-defaults-wrapper" class="ts-advanced-table-defaults-wrapper" style="display: none;">
				<input id="ts-advanced-table-defaults-newrows" data-default="newrows" class="ts-advanced-table-defaults-newrows ts-advanced-table-defaults-format" name="ts-advanced-table-defaults-newrows" type="text" value="<?php echo $TS_TablesWP_Editor_NewRows; ?>">
				<input id="ts-advanced-table-defaults-newcolumns" data-default="newcolumns" class="ts-advanced-table-defaults-newcolumns ts-advanced-table-defaults-format" name="ts-advanced-table-defaults-newcolumns" type="text" value="<?php echo $TS_TablesWP_Editor_NewColumns; ?>">
				<input id="ts-advanced-table-defaults-alignhorizontal" data-default="alignhorizontal" class="ts-advanced-table-defaults-alignhorizontal ts-advanced-table-defaults-format" name="ts-advanced-table-defaults-alignhorizontal" type="text" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_AlignHorizontal; ?>">
				<input id="ts-advanced-table-defaults-alignvertical" data-default="alignvertical" class="ts-advanced-table-defaults-alignvertical ts-advanced-table-defaults-format" name="ts-advanced-table-defaults-alignvertical" type="text" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_AlignVertical; ?>">        
				<input id="ts-advanced-table-defaults-editorlocale" data-default="editorlocale" class="ts-advanced-table-defaults-editorlocale ts-advanced-table-defaults-format" name="ts-advanced-table-defaults-editorlocale" type="text" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_EditorLocale; ?>">        
				<input id="ts-advanced-table-defaults-formattimehours" data-default="formattimehours" class="ts-advanced-table-defaults-formattimehours ts-advanced-table-defaults-format" name="ts-advanced-table-defaults-formattimehours" type="text" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTimeHours; ?>">
				<input id="ts-advanced-table-defaults-formattimeminutes" data-default="formattimeminutes" class="ts-advanced-table-defaults-formattimeminutes ts-advanced-table-defaults-format" name="ts-advanced-table-defaults-formattimeminutes" type="text" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTimeMinutes; ?>">
				<input id="ts-advanced-table-defaults-formattimeseconds" data-default="formattimeseconds" class="ts-advanced-table-defaults-formattimeseconds ts-advanced-table-defaults-format" name="ts-advanced-table-defaults-formattimeseconds" type="text" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTimeSeconds; ?>">
				<input id="ts-advanced-table-defaults-formattimemeridiem" data-default="formattimemeridiem" class="ts-advanced-table-defaults-formattimemeridiem ts-advanced-table-defaults-format" name="ts-advanced-table-defaults-formattimemeridiem" type="text" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTimeMeridiem; ?>">
				<input id="ts-advanced-table-defaults-numericfull" data-default="numericfull" class="ts-advanced-table-defaults-numericfull ts-advanced-table-defaults-format" name="ts-advanced-table-defaults-numericfull" type="text" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatNumeric; ?>">
				<input id="ts-advanced-table-defaults-currencyfull" data-default="currencyfull" class="ts-advanced-table-defaults-currencyfull ts-advanced-table-defaults-format" name="ts-advanced-table-defaults-currencyfull" type="text" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatCurrency; ?>">
				<input id="ts-advanced-table-defaults-percentfull" data-default="percentfull" class="ts-advanced-table-defaults-percentfull ts-advanced-table-defaults-format" name="ts-advanced-table-defaults-percentfull" type="text" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatPercent; ?>">
				<input id="ts-advanced-table-defaults-datefull" data-default="datefull" class="ts-advanced-table-defaults-datefull ts-advanced-table-defaults-format" name="ts-advanced-table-defaults-datefull" type="text" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatDate; ?>">
				<input id="ts-advanced-table-defaults-timefull" data-default="timefull" class="ts-advanced-table-defaults-timefull ts-advanced-table-defaults-format" name="ts-advanced-table-defaults-timefull" type="text" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTime; ?>">        
				<input id="ts-advanced-table-defaults-decimalsnumeric" data-default="decimalsnumeric" class="ts-advanced-table-defaults-decimalsnumeric ts-advanced-table-defaults-format" name="ts-advanced-table-defaults-decimalsnumeric" type="text" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DecimalsNumeric; ?>">
				<input id="ts-advanced-table-defaults-decimalscurrency" data-default="decimalscurrency" class="ts-advanced-table-defaults-decimalscurrency ts-advanced-table-defaults-format" name="ts-advanced-table-defaults-decimalscurrency" type="text" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DecimalsCurrency; ?>">
				<input id="ts-advanced-table-defaults-decimalspercent" data-default="decimalspercent" class="ts-advanced-table-defaults-decimalspercent ts-advanced-table-defaults-format" name="ts-advanced-table-defaults-decimalspercent" type="text" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DecimalsPercent; ?>">
				<textarea id="ts-advanced-table-defaults-formatsdate" data-default="dateFormat" class="ts-advanced-table-defaults-formatsdate" name="ts-advanced-table-defaults-formatsdate"><?php echo base64_encode(implode("*", $TS_ADVANCED_TABLESWP->TS_TablesWP_Allowable_Dates)); ?></textarea>
				<textarea id="ts-advanced-table-defaults-formatstime" data-default="timeFormat" class="ts-advanced-table-defaults-formatstime" name="ts-advanced-table-defaults-formatstime"><?php echo base64_encode(implode("*", $TS_ADVANCED_TABLESWP->TS_TablesWP_Allowable_Times)); ?></textarea>
			</div>
		</div>
		<div id="ts-advanced-table-name-repeater" class="ts-advanced-table-name-repeater" data-empty="<?php echo __("Please enter a table name in the 'Information' tab below.", "ts_visual_composer_extend"); ?>"><?php echo ($TS_TablesWP_Editor_Name != "" ? $TS_TablesWP_Editor_Name : __("Please enter a table name in the 'Information' tab below.", "ts_visual_composer_extend")); ?></div>
		<div id="ts-advanced-table-tabs-container" class="ts-advanced-table-tabs-container">
			<nav id="ts-advanced-table-tabs-navigation" class="ts-advanced-table-tabs-navigation">
				<ul>
					<li class="<?php echo ($TS_TablesWP_Editor_Section == 0 ? 'tab-current' : ''); ?>"><a href="#ts-advanced-table-tabs-section-editor" class="ts-advanced-table-tabs-tab-editor"><span><?php echo __("Editor", "ts_visual_composer_extend"); ?></span></a></li>
					<li class="<?php echo ($TS_TablesWP_Editor_Section == 1 ? 'tab-current' : ''); ?>"><a href="#ts-advanced-table-tabs-section-info" class="ts-advanced-table-tabs-tab-info"><span><?php echo __("Information", "ts_visual_composer_extend"); ?></span></a></li>					
					<li class="<?php echo ($TS_TablesWP_Editor_Section == 2 ? 'tab-current' : ''); ?>"><a href="#ts-advanced-table-tabs-section-categories" class="ts-advanced-table-tabs-tab-categories"><span><?php echo __("Categories", "ts_visual_composer_extend"); ?></span></a></li>
					<li class="<?php echo ($TS_TablesWP_Editor_Section == 3 ? 'tab-current' : ''); ?>"><a href="#ts-advanced-table-tabs-section-settings" class="ts-advanced-table-tabs-tab-settings"><span><?php echo __("Settings", "ts_visual_composer_extend"); ?></span></a></li>
					<li class="<?php echo ($TS_TablesWP_Editor_Section == 4 ? 'tab-current' : ''); ?>"><a href="#ts-advanced-table-tabs-section-import" class="ts-advanced-table-tabs-tab-import"><span><?php echo __("Import", "ts_visual_composer_extend"); ?></span></a></li>
				</ul>
			</nav>
			<div id="ts-advanced-table-tabs-content" class="content">
				<section id="ts-advanced-table-tabs-section-editor" class="<?php echo ($TS_TablesWP_Editor_Section == 0 ? 'content-current' : ''); ?>">
					<div id="ts-advanced-table-fullscreen-wrapper" class="ts-advanced-table-fullscreen-wrapper" style="overflow: hidden;">
						<div id="ts-advanced-table-title-wrapper" class="ts-advanced-table-title-wrapper" style="display: none;">
							<h2></h2>
							<div class="clear"></div>
						</div>
						<div id="ts-advanced-table-search-wrapper" class="ts-advanced-table-search-wrapper" style="display: none; margin: 0 auto 20px auto;">
							<span id="ts-advanced-table-search-label" class="ts-advanced-table-label-input ts-advanced-table-label-holder"><?php echo __("Search Table:", "ts_visual_composer_extend"); ?></span>
							<input id="ts-advanced-table-search-input" class="ts-advanced-table-search-input" name="ts-advanced-table-search-input" type="text">
							<div class="clear"></div>
						</div>
						<div id="ts-advanced-table-icondata-wrapper" class="ts-advanced-table-icondata-wrapper" style="display: none;" data-active="false">	
							<div id="ts-advanced-table-iconpicker-wrapper" class="ts-advanced-table-iconpicker-wrapper" style="display: block;" data-title="<?php echo __("Icon Settings", "ts_visual_composer_extend"); ?>" data-confirm="<?php echo __("Submit", "ts_visual_composer_extend"); ?>" data-cancel="<?php echo __("Cancel", "ts_visual_composer_extend"); ?>" data-other="<?php echo __("Understood!", "ts_visual_composer_extend"); ?>">
								<div id="ts-advanced-table-icon-container" class="ts-advanced-table-icon-container">
									<div id="ts-advanced-table-separator-fonticon" class="ts-advanced-table-separator" style="margin-top: 0px;"></div>
									<div style="font-weight: bold; font-size: 16px; margin: 0 0 20px 0; padding: 0; width: 100%;"><?php echo __("Font Icon Editor", "ts_visual_composer_extend"); ?></div>
									<div id="ts-advanced-table-icon-picker" class="ts-advanced-table-icon-picker">
										<label class="ts-advanced-table-icon-label" for="ts-advanced-table-icon-picker-select"><?php echo __("Select Icon:", "ts_visual_composer_extend"); ?></label>
										<?php
											$settings = array(
												"param_name"			=> "ts-advanced-table-icon-picker-select",
												"value"					=> "",
											);
											echo TS_TablesWP_FontIconPicker($settings, "");
										?>
									</div>
									<div id="ts-advanced-table-icon-color" class="ts-advanced-table-icon-color" style="margin-top: 10px;">
										<label class="ts-advanced-table-icon-label" for="ts-advanced-table-icon-color-picker"><?php echo __("Icon Color:", "ts_visual_composer_extend"); ?></label>
										<div id="ts-advanced-table-icon-color-picker" class="ts-advanced-table-icon-color-picker"></div>
									</div>
									<div id="ts-advanced-table-icon-size" class="ts-advanced-table-icon-size" style="margin: 10px auto;">
										<label class="ts-advanced-table-icon-label" for="ts-advanced-table-icon-size-picker"><?php echo __("Icon Size:", "ts_visual_composer_extend"); ?></label>
										<input id="ts-advanced-table-icon-size-picker" class="ts-advanced-table-icon-size-picker" type="number" min="8" max="100" step="1" value="16"/>
										<span style="display: inline-block; margin: 0 0 0 6px; padding: 0;">px</span>
									</div>
									<div id="ts-advanced-table-icon-display" class="ts-advanced-table-icon-display" style="margin: 10px auto;">
										<label class="ts-advanced-table-icon-label" for="ts-advanced-table-icon-display-select"><?php echo __("Icon Display:", "ts_visual_composer_extend"); ?></label>
										<select id="ts-advanced-table-icon-display-select" class="ts-advanced-table-icon-display-select" name="ts-advanced-table-icon-display-select">
											<option data-value="inline-block" selected="selected"><?php echo __("Inline Block", "ts_visual_composer_extend"); ?></option>
											<option data-value="inline"><?php echo __("Inline", "ts_visual_composer_extend"); ?></option>
											<option data-value="block"><?php echo __("Block", "ts_visual_composer_extend"); ?></option>
										</select>    
									</div>
									<div id="ts-advanced-table-icon-align" class="ts-advanced-table-icon-align" style="margin: 10px auto;">
										<label class="ts-advanced-table-icon-label" for="ts-advanced-table-icon-align-select"><?php echo __("Icon Align:", "ts_visual_composer_extend"); ?></label>
										<select id="ts-advanced-table-icon-align-select" class="ts-advanced-table-icon-align-select" name="ts-advanced-table-icon-align-select">
											<option data-value="left" selected="selected"><?php echo __("Left", "ts_visual_composer_extend"); ?></option>
											<option data-value="center"><?php echo __("Center", "ts_visual_composer_extend"); ?></option>
											<option data-value="right"><?php echo __("Right", "ts_visual_composer_extend"); ?></option>
										</select>    
									</div>
									<div id="ts-advanced-table-icon-float" class="ts-advanced-table-icon-float" style="margin: 10px auto;">
										<label class="ts-advanced-table-icon-label" for="ts-advanced-table-icon-float-select"><?php echo __("Icon Float:", "ts_visual_composer_extend"); ?></label>
										<select id="ts-advanced-table-icon-float-select" class="ts-advanced-table-icon-float-select" name="ts-advanced-table-icon-float-select">
											<option data-value="none" selected="selected"><?php echo __("None", "ts_visual_composer_extend"); ?></option>
											<option data-value="left"><?php echo __("Float Left", "ts_visual_composer_extend"); ?></option>
											<option data-value="right"><?php echo __("Float Right", "ts_visual_composer_extend"); ?></option>
										</select>    
									</div>
									<div id="ts-advanced-table-icon-insert" class="ts-advanced-table-icon-insert" style="margin: 10px auto;">
										<label class="ts-advanced-table-icon-label" for="ts-advanced-table-icon-insert-select"><?php echo __("Icon Insert:", "ts_visual_composer_extend"); ?></label>
										<select id="ts-advanced-table-icon-insert-select" class="ts-advanced-table-icon-insert-select" name="ts-advanced-table-icon-insert-select">
											<option data-value="inline" selected="selected"><?php echo __("Add Icon to Existing Line (After)", "ts_visual_composer_extend"); ?></option>
											<option data-value="before"><?php echo __("Add Icon to Existing Line (Before)", "ts_visual_composer_extend"); ?></option>
											<option data-value="newline"><?php echo __("Create New Line for Icon (After)", "ts_visual_composer_extend"); ?></option>
											<option data-value="addline"><?php echo __("Create New Line for Icon (Before)", "ts_visual_composer_extend"); ?></option>
										</select>                
									</div>
									<div id="ts-advanced-table-icon-link" class="ts-advanced-table-icon-link" style="margin: 10px auto;">
										<label class="ts-advanced-table-icon-label" for="ts-advanced-table-icon-link-select"><?php echo __("Icon Link:", "ts_visual_composer_extend"); ?></label>
										<select id="ts-advanced-table-icon-link-select" class="ts-advanced-table-icon-link-select" name="ts-advanced-table-icon-link-select">
											<option data-value="false" selected="selected"><?php echo __("No Link", "ts_visual_composer_extend"); ?></option>
											<option data-value="true"><?php echo __("Add Link to Icon", "ts_visual_composer_extend"); ?></option>							
										</select>                
									</div>
									<div id="ts-advanced-table-icon-url" class="ts-advanced-table-icon-url" style="margin: 10px auto; display: none;">
										<label class="ts-advanced-table-icon-label" for="ts-advanced-table-icon-url-input"><?php echo __("Link URL:", "ts_visual_composer_extend"); ?></label>
										<input id="ts-advanced-table-icon-url-input" class="ts-advanced-table-icon-url-input" type="text"/>
									</div>
									<div id="ts-advanced-table-icon-target" class="ts-advanced-table-icon-target" style="margin: 10px auto; display: none;">
										<label class="ts-advanced-table-icon-label" for="ts-advanced-table-icon-target-select"><?php echo __("Link Target:", "ts_visual_composer_extend"); ?></label>
										<select id="ts-advanced-table-icon-target-select" class="ts-advanced-table-icon-target-select" name="ts-advanced-table-icon-target-select">
											<option data-value="_parent" selected="selected"><?php echo __("Same Window / Tab", "ts_visual_composer_extend"); ?></option>
											<option data-value="_blank"><?php echo __("New Window / Tab", "ts_visual_composer_extend"); ?></option>							
										</select> 
									</div>
									<div id="ts-advanced-table-icon-title" class="ts-advanced-table-icon-title" style="margin: 10px auto; display: none;">
										<label class="ts-advanced-table-icon-label" for="ts-advanced-table-icon-title-input"><?php echo __("Link Title:", "ts_visual_composer_extend"); ?></label>
										<input id="ts-advanced-table-icon-title-input" class="ts-advanced-table-icon-title-input" type="text"/>
									</div>
									<div id="ts-advanced-table-icon-rel" class="ts-advanced-table-icon-rel" style="margin: 10px auto; display: none;">
										<label class="ts-advanced-table-icon-label" for="ts-advanced-table-icon-rel-select"><?php echo __("Link REL:", "ts_visual_composer_extend"); ?></label>
										<select id="ts-advanced-table-icon-rel-select" class="ts-advanced-table-icon-rel-select" name="ts-advanced-table-icon-rel-select">
											<option data-value="" selected="selected"><?php echo __("No REL Attribute", "ts_visual_composer_extend"); ?></option>
											<option data-value="nofollow">nofollow</option>
											<option data-value="noreferrer">noreferrer</option>
											<option data-value="prefetch">prefetch</option>
											<option data-value="bookmark">bookmark</option>
											<option data-value="alternate">alternate</option>
											<option data-value="author">author</option>
											<option data-value="help">help</option>
											<option data-value="search">search</option>
											<option data-value="license">license</option>						
										</select> 
									</div>
									<div id="ts-advanced-table-icon-embed" class="ts-advanced-table-icon-embed" style="display: none;">
										<label class="ts-advanced-table-icon-label" for="ts-advanced-table-icon-embed-select"><?php echo __("Icon Code:", "ts_visual_composer_extend"); ?></label>
										<select id="ts-advanced-table-icon-embed-select" class="ts-advanced-table-icon-embed-select" name="ts-advanced-table-icon-embed-select">
											<option data-value="htmlcode" selected="selected"><?php echo __("Add As HTML Code", "ts_visual_composer_extend"); ?></option>
											<option data-value="shortcode"><?php echo __("Add As Shortcode", "ts_visual_composer_extend"); ?></option>
										</select>                
									</div>
									<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder" style="margin-top: 20px; margin-right: 20px;">
										<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to insert the icon into the selected cell.", "ts_visual_composer_extend"); ?></span>
										<div id="ts-advanced-table-iconinsert-button" class="ts-advanced-tables-button-main ts-advanced-tables-button-green ts-advanced-tables-button-fonticon" data-toggle="ts-advanced-table-scope-controls" data-visible="false" style="margin: 0;">
											<?php echo __("Insert Font Icon", "ts_visual_composer_extend"); ?>
										</div>
									</div>
									<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder" style="margin-top: 20px;">
										<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to close the font icon editor.", "ts_visual_composer_extend"); ?></span>
										<div id="ts-advanced-table-iconclose-button" class="ts-advanced-tables-button-main ts-advanced-tables-button-red ts-advanced-tables-button-dismiss" data-toggle="ts-advanced-table-scope-controls" data-visible="false" style="margin: 0;">
											<?php echo __("Close Icon Editor", "ts_visual_composer_extend"); ?>
										</div>
									</div>
								</div>
							</div>		
						</div>
						<div id="ts-advanced-table-celldata-wrapper" class="ts-advanced-table-celldata-wrapper" style="display: block; margin: 0 auto 10px auto;">
							<span id="ts-advanced-table-celldata-label" class="ts-advanced-table-label-input ts-advanced-table-label-holder"><?php echo __("Raw Cell Editor:", "ts_visual_composer_extend"); ?></span>
							<textarea id="ts-advanced-table-celldata-editor" class="ts-advanced-table-celldata-editor" rows="4" disabled="disabled"></textarea>
							<div class="clear"></div>
							<span id="ts-advanced-table-handsontable-label" class="ts-advanced-table-label-input ts-advanced-table-label-holder"><?php echo __("Table Editor:", "ts_visual_composer_extend"); ?></span>
						</div>
						<div id="ts-advanced-table-toolbar-wrapper" class="ts-advanced-table-toolbar-wrapper">
							<div id="ts-advanced-table-toolbar-inactive" class="ts-advanced-table-toolbar-inactive"></div>
							<ul id="ts-advanced-table-toolbar-structure" class="ts-advanced-table-toolbar-section">
								<li>
									<button id="ts-advanced-table-button-rows" class="ts-advanced-table-tooltip-holder ts-advanced-table-submenu-holder" data-submenu="ts-advanced-table-submenu-rows" data-visible="false">
										<span class="ts-advanced-table-tooltip-content"><?php echo __("Rows", "ts_visual_composer_extend"); ?></span>
										<i class="ts-tableeditor-icon ts-tableeditor-table2"></i>
									</button>
									<div id="ts-advanced-table-submenu-rows" class="ts-advanced-table-submenu-content ts-advanced-table-submenu-horizontal ts-advanced-table-submenu-hidden">
										<div class="ts-advanced-table-submenu-items" style="width: 80px;">
											<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-tooltip-holder ts-advanced-table-submenu-add" data-callback="row">
												<span class="ts-advanced-table-tooltip-content ts-advanced-table-tooltip-success"><?php echo __("Add Row", "ts_visual_composer_extend"); ?></span>
												<i class="ts-tableeditor-icon ts-tableeditor-plus2"></i>
											</a>
											<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-tooltip-holder ts-advanced-table-submenu-remove" data-callback="remove_row">
												<span class="ts-advanced-table-tooltip-content ts-advanced-table-tooltip-critical"><?php echo __("Delete Row", "ts_visual_composer_extend"); ?></span>
												<i class="ts-tableeditor-icon ts-tableeditor-trash1"></i>
											</a>
										</div>
									</div>
								</li>
								<li>                
									<button id="ts-advanced-table-button-columns" class="ts-advanced-table-tooltip-holder ts-advanced-table-submenu-holder" data-submenu="ts-advanced-table-submenu-columns" data-visible="false">
										<span class="ts-advanced-table-tooltip-content"><?php echo __("Columns", "ts_visual_composer_extend"); ?></span>
										<i class="ts-tableeditor-icon ts-tableeditor-table7"></i>
									</button>
									<div id="ts-advanced-table-submenu-columns" class="ts-advanced-table-submenu-content ts-advanced-table-submenu-horizontal ts-advanced-table-submenu-hidden">
										<div class="ts-advanced-table-submenu-items" style="width: 80px;">
											<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-tooltip-holder ts-advanced-table-submenu-add" data-callback="column">
												<span class="ts-advanced-table-tooltip-content ts-advanced-table-tooltip-success"><?php echo __("Add Column", "ts_visual_composer_extend"); ?></span>
												<i class="ts-tableeditor-icon ts-tableeditor-plus2"></i>
											</a>
											<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-tooltip-holder ts-advanced-table-submenu-remove" data-callback="remove_col">
												<span class="ts-advanced-table-tooltip-content ts-advanced-table-tooltip-critical"><?php echo __("Delete Column", "ts_visual_composer_extend"); ?></span>
												<i class="ts-tableeditor-icon ts-tableeditor-trash1"></i>
											</a>
										</div>
									</div>
								</li>
							</ul>
							<ul id="ts-advanced-table-toolbar-styling" class="ts-advanced-table-toolbar-section">
								<li>                
									<button id="ts-advanced-table-button-bold" class="ts-advanced-table-tooltip-holder" data-submenu="false" data-callback="bold">
										<span class="ts-advanced-table-tooltip-content"><?php echo __("Bold", "ts_visual_composer_extend"); ?></span>
										<i class="ts-tableeditor-icon ts-tableeditor-bold"></i>
									</button>
								</li>
								<li>                
									<button id="ts-advanced-table-button-italic" class="ts-advanced-table-tooltip-holder" data-submenu="false" data-callback="italic">
										<span class="ts-advanced-table-tooltip-content"><?php echo __("Italic", "ts_visual_composer_extend"); ?></span>
										<i class="ts-tableeditor-icon ts-tableeditor-italic"></i>
									</button>
								</li>
								<li>                
									<button id="ts-advanced-table-button-decoration" class="ts-advanced-table-tooltip-holder ts-advanced-table-submenu-holder" data-submenu="ts-advanced-table-submenu-decoration" data-visible="false">
										<span class="ts-advanced-table-tooltip-content"><?php echo __("Text Decoration", "ts_visual_composer_extend"); ?></span>
										<i class="ts-tableeditor-icon ts-tableeditor-underline"></i>
									</button>
									<div id="ts-advanced-table-submenu-decoration" class="ts-advanced-table-submenu-content ts-advanced-table-submenu-horizontal ts-advanced-table-submenu-hidden">
										<div class="ts-advanced-table-submenu-items" style="width: 160px;">
											<a href="#" id="ts-advanced-table-button-nodecoration" class="ts-advanced-table-submenu-single ts-advanced-table-tooltip-holder" data-callback="nodecoration">
												<span class="ts-advanced-table-tooltip-content"><?php echo __("No Decoration", "ts_visual_composer_extend"); ?></span>
												<i class="ts-advanced-table-text-decoration">D</i>
											</a>
											<a href="#" id="ts-advanced-table-button-underline" class="ts-advanced-table-submenu-single ts-advanced-table-tooltip-holder" data-callback="underline">
												<span class="ts-advanced-table-tooltip-content"><?php echo __("Underline", "ts_visual_composer_extend"); ?></span>
												<i class="ts-advanced-table-text-decoration" style="text-decoration: underline;">D</i>
											</a>
											<a href="#" id="ts-advanced-table-button-overline" class="ts-advanced-table-submenu-single ts-advanced-table-tooltip-holder" data-callback="overline">
												<span class="ts-advanced-table-tooltip-content"><?php echo __("Overline", "ts_visual_composer_extend"); ?></span>
												<i class="ts-advanced-table-text-decoration" style="text-decoration: overline;">D</i>
											</a>
											<a href="#" id="ts-advanced-table-button-linethrough" class="ts-advanced-table-submenu-single ts-advanced-table-tooltip-holder" data-callback="linethrough">
												<span class="ts-advanced-table-tooltip-content"><?php echo __("Line-Through", "ts_visual_composer_extend"); ?></span>
												<i class="ts-advanced-table-text-decoration" style="text-decoration: line-through;">D</i>
											</a>
										</div>
									</div>
								</li>
								<li>                
									<button id="ts-advanced-table-button-color" class="ts-advanced-table-tooltip-holder" data-submenu="false" data-callback="color">
										<span class="ts-advanced-table-tooltip-content"><?php echo __("Font Color", "ts_visual_composer_extend"); ?></span>
										<i class="ts-tableeditor-icon ts-tableeditor-textcolor"></i>
									</button>
								</li>
								<li>                
									<button id="ts-advanced-table-button-background" class="ts-advanced-table-tooltip-holder" data-submenu="false" data-callback="background">
										<span class="ts-advanced-table-tooltip-content"><?php echo __("Background Color", "ts_visual_composer_extend"); ?></span>
										<i class="ts-tableeditor-icon ts-tableeditor-styling1"></i>
									</button>
								</li>
								<li>                
									<button id="ts-advanced-table-button-resetcolors" class="ts-advanced-table-tooltip-holder" data-submenu="false" data-callback="resetColors">
										<span class="ts-advanced-table-tooltip-content ts-advanced-table-tooltip-critical"><?php echo __("Reset Font + Background Colors", "ts_visual_composer_extend"); ?></span>
										<i class="ts-tableeditor-icon ts-tableeditor-erase1" style="color: #ed6f6f;"></i>
									</button>
								</li>
							</ul>
							<ul id="ts-advanced-table-toolbar-alignments" class="ts-advanced-table-toolbar-section">
								<li>                
									<button id="ts-advanced-table-button-horizontal" class="ts-advanced-table-tooltip-holder ts-advanced-table-submenu-holder" data-submenu="ts-advanced-table-submenu-alignment" data-visible="false">
										<span class="ts-advanced-table-tooltip-content"><?php echo __("Horizontal Alignment", "ts_visual_composer_extend"); ?></span>
										<i class="ts-tableeditor-icon ts-tableeditor-horizontal3"></i>
									</button>
									<div id="ts-advanced-table-submenu-alignment" class="ts-advanced-table-submenu-content ts-advanced-table-submenu-horizontal ts-advanced-table-submenu-hidden">
										<div class="ts-advanced-table-submenu-items" style="width: 160px;">
											<a href="#" id="ts-advanced-table-button-left" class="ts-advanced-table-submenu-single ts-advanced-table-tooltip-holder" data-callback="left">
												<span class="ts-advanced-table-tooltip-content"><?php echo __("Left", "ts_visual_composer_extend"); ?></span>
												<i class="ts-tableeditor-icon ts-tableeditor-alignleft2"></i>
											</a>
											<a href="#" id="ts-advanced-table-button-center" class="ts-advanced-table-submenu-single ts-advanced-table-tooltip-holder" data-callback="center">
												<span class="ts-advanced-table-tooltip-content"><?php echo __("Center", "ts_visual_composer_extend"); ?></span>
												<i class="ts-tableeditor-icon ts-tableeditor-aligncenter2"></i>
											</a>
											<a href="#" id="ts-advanced-table-button-right" class="ts-advanced-table-submenu-single ts-advanced-table-tooltip-holder" data-callback="right">
												<span class="ts-advanced-table-tooltip-content"><?php echo __("Right", "ts_visual_composer_extend"); ?></span>
												<i class="ts-tableeditor-icon ts-tableeditor-alignright2"></i>
											</a>
											<a href="#" id="ts-advanced-table-button-justify" class="ts-advanced-table-submenu-single ts-advanced-table-tooltip-holder" data-callback="justify">
												<span class="ts-advanced-table-tooltip-content"><?php echo __("Justify", "ts_visual_composer_extend"); ?></span>
												<i class="ts-tableeditor-icon ts-tableeditor-alignjustify2"></i>
											</a>
										</div>
									</div>
								</li>
								<li>                
									<button id="ts-advanced-table-button-vertical" class="ts-advanced-table-tooltip-holder ts-advanced-table-submenu-holder" data-submenu="ts-advanced-table-submenu-vertical" data-visible="false">
										<span class="ts-advanced-table-tooltip-content"><?php echo __("Vertical Alignment", "ts_visual_composer_extend"); ?></span>
										<i class="ts-tableeditor-icon ts-tableeditor-vertical3"></i>
									</button>
									<div id="ts-advanced-table-submenu-vertical" class="ts-advanced-table-submenu-content ts-advanced-table-submenu-horizontal ts-advanced-table-submenu-hidden">
										<div class="ts-advanced-table-submenu-items" style="width: 120px;">
											<a href="#" id="ts-advanced-table-button-top" class="ts-advanced-table-submenu-single ts-advanced-table-tooltip-holder" data-callback="top">
												<span class="ts-advanced-table-tooltip-content"><?php echo __("Top", "ts_visual_composer_extend"); ?></span>
												<i class="ts-tableeditor-icon ts-tableeditor-verticaltop"></i>
											</a>
											<a href="#" id="ts-advanced-table-button-middle" class="ts-advanced-table-submenu-single ts-advanced-table-tooltip-holder" data-callback="middle">
												<span class="ts-advanced-table-tooltip-content"><?php echo __("Middle", "ts_visual_composer_extend"); ?></span>
												<i class="ts-tableeditor-icon ts-tableeditor-verticalmiddle"></i>
											</a>
											<a href="#" id="ts-advanced-table-button-bottom" class="ts-advanced-table-submenu-single ts-advanced-table-tooltip-holder" data-callback="bottom">
												<span class="ts-advanced-table-tooltip-content"><?php echo __("Bottom", "ts_visual_composer_extend"); ?></span>
												<i class="ts-tableeditor-icon ts-tableeditor-verticalbottom"></i>
											</a>
										</div>
									</div>
								</li>
							</ul>
							<ul id="ts-advanced-table-toolbar-links" class="ts-advanced-table-toolbar-section">    
								<li>
									<button id="ts-advanced-table-button-link" class="ts-advanced-table-tooltip-holder" data-submenu="false" data-callback="linkURL">
										<span class="ts-advanced-table-tooltip-content"><?php echo __("Add Link", "ts_visual_composer_extend"); ?></span>
										<i class="ts-tableeditor-icon ts-tableeditor-link1"></i>
									</button>
								</li>
							</ul>
							<ul id="ts-advanced-table-toolbar-media"" class="ts-advanced-table-toolbar-section">
								<li>                
									<button id="ts-advanced-table-button-picture" class="ts-advanced-table-tooltip-holder" data-submenu="ts-advanced-table-submenu-picture" data-visible="false">
										<span class="ts-advanced-table-tooltip-content"><?php echo __("Add Image", "ts_visual_composer_extend"); ?></span>
										<i class="ts-tableeditor-icon ts-tableeditor-image1"></i>
									</button>
									<div id="ts-advanced-table-submenu-picture" class="ts-advanced-table-submenu-content ts-advanced-table-submenu-horizontal ts-advanced-table-submenu-hidden">
										<div class="ts-advanced-table-submenu-items" style="width: 80px;">
											<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-tooltip-holder ts-advanced-table-submenu-imagewp" data-callback="imageWP">
												<span class="ts-advanced-table-tooltip-content"><?php echo __("Add WordPress Image", "ts_visual_composer_extend"); ?></span>
												<i class="ts-tableeditor-icon ts-tableeditor-wordpress1"></i>
											</a>
											<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-tooltip-holder ts-advanced-table-submenu-imageurl" data-callback="imageURL">
												<span class="ts-advanced-table-tooltip-content"><?php echo __("Add External Image", "ts_visual_composer_extend"); ?></span>
												<i class="ts-tableeditor-icon ts-tableeditor-external4"></i>
											</a>
										</div>
									</div>
								</li>
							</ul>
							<ul id="ts-advanced-table-toolbar-icon"" class="ts-advanced-table-toolbar-section">
								<li>
									<button id="ts-advanced-table-button-icon" class="ts-advanced-table-tooltip-holder" data-submenu="false" data-callback="fontIcon">
										<span class="ts-advanced-table-tooltip-content"><?php echo __("Add Font Icon", "ts_visual_composer_extend"); ?></span>
										<i class="ts-tableeditor-icon ts-tableeditor-fonticon"></i>
									</button>
								</li>
							</ul>
							<ul id="ts-advanced-table-toolbar-comments" class="ts-advanced-table-toolbar-section">
								<li>                
									<button id="ts-advanced-table-button-comment" class="ts-advanced-table-tooltip-holder ts-advanced-table-submenu-holder" data-submenu="ts-advanced-table-submenu-comment" data-visible="false">
										<span class="ts-advanced-table-tooltip-content"><?php echo __("Cell Comment", "ts_visual_composer_extend"); ?></span>
										<i class="ts-tableeditor-icon ts-tableeditor-comment3"></i>
									</button>
									<div id="ts-advanced-table-submenu-comment" class="ts-advanced-table-submenu-content ts-advanced-table-submenu-horizontal ts-advanced-table-submenu-hidden">
										<div class="ts-advanced-table-submenu-items" style="width: 80px;">
											<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-tooltip-holder ts-advanced-table-submenu-add" data-callback="addEditComment">
												<span class="ts-advanced-table-tooltip-content"><?php echo __("Add/Edit Comment", "ts_visual_composer_extend"); ?></span>
												<i class="ts-tableeditor-icon ts-tableeditor-edit4"></i>
											</a>
											<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-tooltip-holder ts-advanced-table-submenu-remove" data-callback="removeComment">
												<span class="ts-advanced-table-tooltip-content"><?php echo __("Delete Comment", "ts_visual_composer_extend"); ?></span>
												<i class="ts-tableeditor-icon ts-tableeditor-trash1"></i>
											</a>
										</div>
									</div>
								</li>
							</ul>
							<ul id="ts-advanced-table-toolbar-formats" class="ts-advanced-table-toolbar-section">
								<li>                
									<button id="ts-advanced-table-button-format" class="ts-advanced-table-tooltip-holder ts-advanced-table-submenu-holder" data-submenu="ts-advanced-table-submenu-formats" data-visible="false">
										<span class="ts-advanced-table-tooltip-content"><?php echo __("Cell Format", "ts_visual_composer_extend"); ?></span>
										<i class="ts-tableeditor-icon ts-tableeditor-hash1"></i>
									</button>
									<div id="ts-advanced-table-submenu-formats" class="ts-advanced-table-submenu-content ts-advanced-table-submenu-vertical ts-advanced-table-submenu-hidden">
										<div class="ts-advanced-table-submenu-items" style="width: 120px;">
											<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-submenu-format text-format" data-callback="setFormat" data-type="text" data-format="" data-string="Text"><i class="ts-tableeditor-icon ts-tableeditor-icon-inline ts-tableeditor-text3"></i><?php echo __("Text", "ts_visual_composer_extend"); ?></a>
											<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-submenu-format number-format" data-callback="setFormat" data-type="number" data-format="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatNumeric; ?>" data-string="Number"><i class="ts-tableeditor-icon ts-tableeditor-icon-inline ts-tableeditor-numbers1"></i><?php echo __("Number", "ts_visual_composer_extend"); ?></a>
											<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-submenu-format currency-format" data-callback="setFormat" data-type="currency" data-format="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatCurrency; ?>" data-string="Currency"><i class="ts-tableeditor-icon ts-tableeditor-icon-inline ts-tableeditor-currency7"></i><?php echo __("Currency", "ts_visual_composer_extend"); ?></a>
											<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-submenu-format percent-format" data-callback="setFormat" data-type="percent" data-format="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatPercent; ?>" data-string="Percent"><i class="ts-tableeditor-icon ts-tableeditor-icon-inline ts-tableeditor-percent"></i><?php echo __("Percent", "ts_visual_composer_extend"); ?></a>
											<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-submenu-format date-format" data-callback="setFormat" data-type="date" data-format="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatDate; ?>" data-string="Date"><i class="ts-tableeditor-icon ts-tableeditor-icon-inline ts-tableeditor-calendar2"></i><?php echo __("Date", "ts_visual_composer_extend"); ?></a>
											<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-submenu-format time-format" data-callback="setFormat" data-type="time" data-format="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTime; ?>" data-string="Time"><i class="ts-tableeditor-icon ts-tableeditor-icon-inline ts-tableeditor-time2"></i><?php echo __("Time", "ts_visual_composer_extend"); ?></a>
										</div>
									</div>
								</li>
							</ul>
							<ul id="ts-advanced-table-toolbar-dates" class="ts-advanced-table-toolbar-section">
								<li>
									<button id="ts-advanced-table-button-dates" class="ts-advanced-table-tooltip-holder ts-advanced-table-submenu-holder" data-submenu="ts-advanced-table-submenu-dates" data-visible="false">
										<span class="ts-advanced-table-tooltip-content"><?php echo __("Date Format", "ts_visual_composer_extend"); ?></span>
										<i class="ts-tableeditor-icon ts-tableeditor-calendar2"></i>
									</button>
									<div id="ts-advanced-table-submenu-dates" class="ts-advanced-table-submenu-content ts-advanced-table-submenu-vertical ts-advanced-table-submenu-hidden">
										<div class="ts-advanced-table-submenu-items" style="width: 150px;  max-height: 300px; overflow-x: hidden; overflow-y: auto;">
											<?php
												foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_Allowable_Dates as $key => $value) {
													echo '<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-submenu-date ' . $key . '-date" data-callback="setDate" data-type="date" data-format="' . $value . '">' . $value . '</a>';
												}
											?>
										</div>
									</div>    
								</li>
							</ul>
							<ul id="ts-advanced-table-toolbar-times" class="ts-advanced-table-toolbar-section">
								<li>
									<button id="ts-advanced-table-button-times" class="ts-advanced-table-tooltip-holder ts-advanced-table-submenu-holder" data-submenu="ts-advanced-table-submenu-times" data-visible="false">
										<span class="ts-advanced-table-tooltip-content"><?php echo __("Time Format", "ts_visual_composer_extend"); ?></span>
										<i class="ts-tableeditor-icon ts-tableeditor-time2"></i>
									</button>
									<div id="ts-advanced-table-submenu-times" class="ts-advanced-table-submenu-content ts-advanced-table-submenu-vertical ts-advanced-table-submenu-hidden">
										<div class="ts-advanced-table-submenu-items" style="width: 150px;  max-height: 300px; overflow-x: hidden; overflow-y: auto;">
											<?php
												foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_Allowable_Times as $key => $value) {
													echo '<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-submenu-time ' . $key . '-time" data-callback="setTime" data-type="time" data-format="' . $value . '">' . $value . '</a>';
												}
											?>
										</div>
									</div>
								</li>
							</ul>
							<ul id="ts-advanced-table-toolbar-decimals" class="ts-advanced-table-toolbar-section">
								<li>
									<button id="ts-advanced-table-button-decimals" class="ts-advanced-table-tooltip-holder ts-advanced-table-submenu-holder" data-submenu="ts-advanced-table-submenu-decimals" data-visible="false">
										<span class="ts-advanced-table-tooltip-content"><?php echo __("Decimals Count", "ts_visual_composer_extend"); ?></span>
										<i class="ts-tableeditor-icon ts-tableeditor-numbers2"></i>
									</button>
									<div id="ts-advanced-table-submenu-decimals" class="ts-advanced-table-submenu-content ts-advanced-table-submenu-vertical ts-advanced-table-submenu-hidden">
										<div class="ts-advanced-table-submenu-items" style="width: 180px;  max-height: 300px; overflow-x: hidden; overflow-y: auto;">
											<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-submenu-decimal 0-decimals" data-callback="setDecimals" data-decimals="0" data-type="decimals"><?php echo __("No Decimals", "ts_visual_composer_extend"); ?></a>
											<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-submenu-decimal 1-decimals" data-callback="setDecimals" data-decimals="1" data-type="decimals"><?php echo __("1 Decimal", "ts_visual_composer_extend"); ?></a>
											<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-submenu-decimal 2-decimals" data-callback="setDecimals" data-decimals="2" data-type="decimals"><?php echo __("2 Decimals", "ts_visual_composer_extend"); ?></a>
											<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-submenu-decimal 3-decimals" data-callback="setDecimals" data-decimals="3" data-type="decimals"><?php echo __("3 Decimals", "ts_visual_composer_extend"); ?></a>
											<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-submenu-decimal 4-decimals" data-callback="setDecimals" data-decimals="4" data-type="decimals"><?php echo __("4 Decimals", "ts_visual_composer_extend"); ?></a>
											<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-submenu-decimal 5-decimals" data-callback="setDecimals" data-decimals="5" data-type="decimals"><?php echo __("5 Decimals", "ts_visual_composer_extend"); ?></a>
											<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-submenu-decimal 6-decimals" data-callback="setDecimals" data-decimals="6" data-type="decimals"><?php echo __("6 Decimals", "ts_visual_composer_extend"); ?></a>
										</div>
									</div>
								</li>
							</ul>
							<ul id="ts-advanced-table-toolbar-symbols" class="ts-advanced-table-toolbar-section">
								<li>
									<button id="ts-advanced-table-button-symbols" class="ts-advanced-table-tooltip-holder ts-advanced-table-submenu-holder" data-submenu="ts-advanced-table-submenu-symbols" data-visible="false">
										<span class="ts-advanced-table-tooltip-content"><?php echo __("Currency Symbol", "ts_visual_composer_extend"); ?></span>
										<i class="ts-tableeditor-icon ts-tableeditor-currency2"></i>
									</button>
									<div id="ts-advanced-table-submenu-symbols" class="ts-advanced-table-submenu-content ts-advanced-table-submenu-vertical ts-advanced-table-submenu-hidden">
										<div class="ts-advanced-table-submenu-items" style="width: 300px;  max-height: 300px; overflow-x: hidden; overflow-y: auto;">
											<?php
												foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_Currency_HTML_Codes as $key => $value) {
													echo '<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-submenu-symbol ' . strtolower($key) . '-symbol" data-callback="setSymbol" data-type="symbol" data-symbol="' . $key . '">' . __($value, "ts_visual_composer_extend") . '</a>';
												}
												foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_Currency_CountryCodes as $key => $value) {
													echo '<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-submenu-symbol ' . strtolower($key) . '-symbol" data-callback="setSymbol" data-type="symbol" data-symbol="' . $key . '">' . __($value, "ts_visual_composer_extend") . '</a>';
												}
											?>
										</div>
									</div>                
								</li>
							</ul>
							<ul id="ts-advanced-table-toolbar-placement" class="ts-advanced-table-toolbar-section">
								<li>
									<button id="ts-advanced-table-button-placement" class="ts-advanced-table-tooltip-holder ts-advanced-table-submenu-holder" data-submenu="ts-advanced-table-submenu-placement" data-visible="false">
										<span class="ts-advanced-table-tooltip-content"><?php echo __("Symbol Placement", "ts_visual_composer_extend"); ?></span>
										<i class="ts-tableeditor-icon ts-tableeditor-currency11"></i>
									</button>
									<div id="ts-advanced-table-submenu-placement" class="ts-advanced-table-submenu-content ts-advanced-table-submenu-vertical ts-advanced-table-submenu-hidden">
										<div class="ts-advanced-table-submenu-items" style="width: 300px; max-height: 300px; overflow-x: hidden; overflow-y: auto;">
											<?php
												echo '<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-submenu-placement left-none-placement" data-callback="setPlacement" data-type="placement" data-placement="left-none">' . __("Left (No Space)", "ts_visual_composer_extend") . '</a>';
												echo '<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-submenu-placement left-space-placement" data-callback="setPlacement" data-type="placement" data-placement="left-space">' . __("Left (With Space)", "ts_visual_composer_extend") . '</a>';
												echo '<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-submenu-placement right-none-placement" data-callback="setPlacement" data-type="placement" data-placement="right-none">' . __("Right (No Space)", "ts_visual_composer_extend") . '</a>';
												echo '<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-submenu-placement right-space-placement" data-callback="setPlacement" data-type="placement" data-placement="right-space">' . __("Right (With Space)", "ts_visual_composer_extend") . '</a>';
											?>
										</div>
									</div>
								</li>
							</ul>
							<ul id="ts-advanced-table-toolbar-locales" class="ts-advanced-table-toolbar-section">
								<li>
									<button id="ts-advanced-table-button-locales" class="ts-advanced-table-tooltip-holder ts-advanced-table-submenu-holder" data-submenu="ts-advanced-table-submenu-locales" data-visible="false">
										<span class="ts-advanced-table-tooltip-content"><?php echo __("Format Locale", "ts_visual_composer_extend"); ?></span>
										<i class="ts-tableeditor-icon ts-tableeditor-language1"></i>
									</button>
									<div id="ts-advanced-table-submenu-locales" class="ts-advanced-table-submenu-content ts-advanced-table-submenu-vertical ts-advanced-table-submenu-hidden">
										<div class="ts-advanced-table-submenu-items" style="width: 300px; max-height: 300px; overflow-x: hidden; overflow-y: auto;">
											<?php
												foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_NumbroJS_Locales as $key => $value) {
													echo '<a href="#" class="ts-advanced-table-submenu-single ts-advanced-table-submenu-locale ' . strtolower($key) . '-locale" data-callback="setLocale" data-type="locale" data-locale="' . $key . '">' . __($value, "ts_visual_composer_extend") . '</a>';
												}
											?>
										</div>
									</div>
								</li>
							</ul>
							<ul id="ts-advanced-table-toolbar-reset" class="ts-advanced-table-toolbar-section">
								<li>                
									<button id="ts-advanced-table-button-resetall" class="ts-advanced-table-tooltip-holder" data-submenu="false" data-callback="resetAll">
										<span class="ts-advanced-table-tooltip-content ts-advanced-table-tooltip-critical"><?php echo __("Remove Custom Cell Styles", "ts_visual_composer_extend"); ?></span>
										<i class="ts-tableeditor-icon ts-tableeditor-dismiss1" style="color: #ed6f6f;"></i>
									</button>
								</li>
							</ul>
							<ul id="ts-advanced-table-toolbar-fullscreen" class="ts-advanced-table-toolbar-section">
								<li>                
									<button id="ts-advanced-table-button-fullscreen" class="ts-advanced-table-tooltip-holder" data-submenu="false" data-callback="fullScreen" data-fullscreen-state="false" data-fullscreen-enter="<?php echo __("Use Editor in Fullscreen Mode", "ts_visual_composer_extend"); ?>" data-fullscreen-exit="<?php echo __("Leave Fullscreen Mode", "ts_visual_composer_extend"); ?>">
										<span class="ts-advanced-table-tooltip-content"><?php echo __("Use Editor in Fullscreen Mode", "ts_visual_composer_extend"); ?></span>
										<i class="ts-tableeditor-icon ts-tableeditor-fullscreen2" data-icon-enter="ts-tableeditor-fullscreen2" data-icon-exit="ts-tableeditor-contract2"></i>
									</button>
								</li>
							</ul>
						</div>
						<div id="ts-advanced-table-handsontable-preloader" class="ts-advanced-table-handsontable-preloader">
							<?php echo TS_TablesWP_CreatePreloaderCSS("ts-settings-panel-loader", "", 22, "false"); ?>
						</div>
						<div id="ts-advanced-table-handsontable-wrapper" class="ts-advanced-table-handsontable-wrapper" data-height-max="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_TableMaxHeight; ?>" data-height-min="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_TableMinHeight; ?>" style="height: 100%; max-height: <?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_TableMaxHeight; ?>px; min-height: <?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_TableMinHeight; ?>px;">
							<div id="ts-advanced-table-handsontable-editor" class="ts-advanced-table-handsontable-editor"></div>
						</div>
					</div>
				</section>
				<section id="ts-advanced-table-tabs-section-info" class="<?php echo ($TS_TablesWP_Editor_Section == 1 ? 'content-current' : ''); ?>">
					<div id="ts-advanced-table-name-wrapper" class="ts-advanced-table-name-wrapper" style="margin: 0 auto 10px auto;">
						<span id="ts-advanced-table-name-label" class="ts-advanced-table-name-label ts-advanced-table-label-holder"><?php echo __("Table Name:", "ts_visual_composer_extend"); ?></span>
						<input id="ts-advanced-table-name-input" class="ts-advanced-table-name-input validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("Table Name", "ts_visual_composer_extend"); ?>" name="ts-advanced-table-name-input" type="text" value="<?php echo $TS_TablesWP_Editor_Name; ?>">
					</div>
					<div id="ts-advanced-table-number-wrapper" class="ts-advanced-table-number-wrapper">
						<span id="ts-advanced-table-number-label" class="ts-advanced-table-number-label ts-advanced-table-label-holder"><?php echo __("Table ID:", "ts_visual_composer_extend"); ?></span>
						<input id="ts-advanced-table-number-input" class="ts-advanced-table-number-input" name="ts-advanced-table-number-input" type="text" readonly="readonly" value="<?php echo $TS_TablesWP_Editor_ID; ?>">        
					</div>
					<div id="ts-advanced-table-date-wrapper" class="ts-advanced-table-date-wrapper">
						<span id="ts-advanced-table-date-label" class="ts-advanced-table-date-label ts-advanced-table-label-holder"><?php echo __("Created At:", "ts_visual_composer_extend"); ?></span>
						<input id="ts-advanced-table-date-input" class="ts-advanced-table-date-input" name="ts-advanced-table-date-input" type="text" readonly="readonly" value="<?php echo date($TS_ADVANCED_TABLESWP->TS_TablesWP_WordPress_Date . '  - ' . $TS_ADVANCED_TABLESWP->TS_TablesWP_WordPress_Time, $TS_TablesWP_Editor_Date); ?>"> 
						<input id="ts-advanced-table-date-stamp" class="ts-advanced-table-date-stamp" name="ts-advanced-table-date-stamp" type="hidden" style="display: none;" value="<?php echo $TS_TablesWP_Editor_Date; ?>">
					</div>
					<div id="ts-advanced-table-info-wrapper" class="ts-advanced-table-info-wrapper">
						<span id="ts-advanced-table-info-label" class="ts-advanced-table-info-label ts-advanced-table-label-holder"><?php echo __("Table Description:", "ts_visual_composer_extend"); ?></span>
						<div id="ts-advanced-table-info-tinymce" class="ts-advanced-table-info-tinymce" style="display: block;">
							<div class="ts-vcsc-notice-field ts-vcsc-success" style="margin-top: 0px; margin-bottom: 20px; font-size: 13px; text-align: justify;">
								<?php echo __("You can use the text editor below to provide a short description to this table. Information entered here will be shown in the overall listing of all tables, and can also optionally be shown alongside this table, when rendered on the frontend. Keep the description short and it is not advised to use complex HTML code for the content either; the text editor also does not support shortcode processing.", "ts_visual_composer_extend"); ?>                
							</div>
							<?php
								$settings = array(
									'editor_height'     => 200,
									'wpautop' 			=> false, 										// use wpautop?
									'media_buttons' 	=> false, 										// show insert/upload button(s)
									'tabindex' 			=> '',
									'editor_css' 		=> '', 											// intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
									'editor_class' 		=> '', 											// add extra class(es) to the editor textarea
									'teeny' 			=> false, 										// output the minimal editor config used in Press This
									'dfw' 				=> false, 										// replace the default fullscreen with DFW (needs specific css)
									'tinymce' 			=> true, 										// load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
									'quicktags' 		=> true, 										// load Quicktags, can be used to pass settings directly to Quicktags using an array()
									'sanitize' 			=> false,
								);
								wp_editor($TS_TablesWP_Editor_Info, 'ts-advanced-table-info-input', $settings);
							?>
						</div>        
					</div>
				</section>
				<section id="ts-advanced-table-tabs-section-categories" class="<?php echo ($TS_TablesWP_Editor_Section == 2 ? 'content-current' : ''); ?>">
					<div id="ts-advanced-table-cattags-wrapper" class="ts-advanced-table-cattags-wrapper" style="margin-top: 0px;">
						<span id="ts-advanced-table-selected-label" class="ts-advanced-table-label-input ts-advanced-table-label-holder"><?php echo __("Selected Categories:", "ts_visual_composer_extend"); ?></span>
						<div id="ts-advanced-table-cattags-output" class="ts-advanced-table-cattags-output" data-empty="<?php echo __("No Categories", "ts_visual_composer_extend"); ?>" style="margin-top: 10px;">							
							<?php
								if (count($TS_TablesWP_Define_Cats) > 0) {
									$TS_TablesWP_Define_Count = 0;
									foreach ($TS_TablesWP_Define_Cats as $categories => $category) {
										if (in_array($category->id, $TS_TablesWP_Editor_Cats)) {
											echo '<span class="ts-advanced-table-span-cats ts-advanced-table-category-filtered" data-slug="' . $category->id . '" data-level="0">' . $category->name . '<i class="ts-tableeditor-dismiss2"></i></span>';
											$TS_TablesWP_Define_Count++;
										}
										if (isset($category->children)) {
											foreach ($category->children as $sublevels => $level1) {
												if (in_array($level1->id, $TS_TablesWP_Editor_Cats)) {
													echo '<span class="ts-advanced-table-span-cats ts-advanced-table-category-filtered" data-slug="' . $level1->id . '" data-level="1">' . $level1->name . '<i class="ts-tableeditor-dismiss2"></i></span>';
													$TS_TablesWP_Define_Count++;
												}
												if (isset($level1->children)) {
													foreach ($level1->children as $sublevels => $level2) {
														if (in_array($level2->id, $TS_TablesWP_Editor_Cats)) {
															echo '<span class="ts-advanced-table-span-cats ts-advanced-table-category-filtered" data-slug="' . $level2->id . '" data-level="2">' . $level2->name . '<i class="ts-tableeditor-dismiss2"></i></span>';
															$TS_TablesWP_Define_Count++;
														}
													}
												}
											}
										}
									}
									if ($TS_TablesWP_Define_Count == 0) {
										echo '<span class="ts-advanced-table-span-none" data-slug="ts-advanced-table-categories-none" data-level="0">' . __("No Categories", "ts_visual_composer_extend") . '</span>';
									}
								} else {
									echo '<span class="ts-advanced-table-span-none" data-slug="ts-advanced-table-categories-none" data-level="0">' . __("No Categories", "ts_visual_composer_extend") . '</span>';
								}
							?>
						</div>
						<div id="ts-advanced-table-cattags-manage" class="ts-advanced-table-cattags-manage" style="margin-top: 20px;">
							<span id="ts-advanced-table-available-label" class="ts-advanced-table-label-input ts-advanced-table-label-holder"><?php echo __("Available Categories:", "ts_visual_composer_extend"); ?></span>
							<div class="ts-vcsc-notice-field ts-vcsc-warning" style="margin-top: 20px; margin-bottom: 20px; font-size: 13px; text-align: justify;">
								<?php echo __("You can assign any of the following predefined categories to this table. In order to create a new category, you need to do so by using the category manager, before the category will become available here in the table editor. Categories will only be relevant for any filter or search routines on the general table listing page.", "ts_visual_composer_extend"); ?>                
							</div>
							<?php
								if (count($TS_TablesWP_Define_Cats) == 0) {
									echo '<span style="display: block; color: #d40000;">' . __("You did not yet create any pre-defined categories that could be assigned to this table!", "ts_visual_composer_extend") . '</span>';
								}
								// Render Pre-Defined Categories
								echo '<div id="ts-advanced-table-categories-selector" class="ts-advanced-table-categories-selector" name="ts-advanced-table-categories-selector" data-none="' . __("No Categories", "ts_visual_composer_extend") . '" style="max-height: 600px; display: ' . (count($TS_TablesWP_Define_Cats) > 0 ? "block" : "none") . ';">';
									echo '<ul id="ts-advanced-table-categories-listing" class="ts-advanced-table-categories-listing">';
										if (count($TS_TablesWP_Define_Cats) > 0) {
											foreach ($TS_TablesWP_Define_Cats as $categories => $category) {
												echo '<li>';
													if (in_array($category->id, $TS_TablesWP_Editor_Cats)) {
														$TS_TablesWP_Editor_Checked = 'checked="checked"';
													} else {
														$TS_TablesWP_Editor_Checked = '';
													}
													echo '<div class="ts-advanced-table-categories-handle">';
														echo '<input type="checkbox" ' . $TS_TablesWP_Editor_Checked . ' id="' . $category->id . '" data-id="' . $category->id . '" data-name="' . $category->name . '" data-level="0"/><label for="' . $category->id . '">' . $category->name . '</label>';
													echo '</div>';
													// First Sublevel
													if (isset($category->children)) {
														echo '<ul>';
															foreach ($category->children as $sublevels => $level1) {
																echo '<li>';
																	if (in_array($level1->id, $TS_TablesWP_Editor_Cats)) {
																		$TS_TablesWP_Editor_Checked = 'checked="checked"';
																	} else {
																		$TS_TablesWP_Editor_Checked = '';
																	}
																	echo '<div class="ts-advanced-table-categories-handle">';
																		echo '<input type="checkbox" ' . $TS_TablesWP_Editor_Checked . ' id="' . $level1->id . '" data-id="' . $level1->id . '" data-name="' . $level1->name . '" data-level="1"/><label for="' . $level1->id . '">' . $level1->name . '</label>';
																	echo '</div>';
																	// Second Sublevel
																	if (isset($level1->children)) {
																		echo '<ul>';
																			foreach ($level1->children as $sublevels => $level2) {
																				echo '<li>';
																					if (in_array($level2->id, $TS_TablesWP_Editor_Cats)) {
																						$TS_TablesWP_Editor_Checked = 'checked="checked"';
																					} else {
																						$TS_TablesWP_Editor_Checked = '';
																					}
																					echo '<div class="ts-advanced-table-categories-handle">';
																						echo '<input type="checkbox" ' . $TS_TablesWP_Editor_Checked . ' id="' . $level2->id . '" data-id="' . $level2->id . '" data-name="' . $level2->name . '" data-level="2"/><label for="' . $level2->id . '">' . $level2->name . '</label>';
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
					</div>
				</section>
				<section id="ts-advanced-table-tabs-section-settings" class="<?php echo ($TS_TablesWP_Editor_Section == 3 ? 'content-current' : ''); ?>">
					<div id="ts-advanced-table-metadata-wrapper" class="ts-advanced-table-metadata-wrapper" style="margin: 0 auto 10px auto;">
						<span id="ts-advanced-table-metadata-label" class="ts-advanced-table-metadata-label ts-advanced-table-label-holder"><?php echo __("Advanced Toolbar:", "ts_visual_composer_extend"); ?></span>
						<div id="ts-advanced-table-metadata-controls" class="ts-advanced-table-metadata-controls">
							<div class="ts-vcsc-notice-field ts-vcsc-warning" style="float: left; margin-top: 10px; margin-bottom: 0px; font-size: 13px; text-align: justify;">
								<?php echo __("When using advanced editor features associated with the editor toolbar, any such settings applied to table cells will be saved as separate meta-data. The larger the table (number of cells), the more meta data must be generated and saved, which can cause performance problems when saving the table itself, or when rendering the table while the meta-data is getting re-applied to the table. If you do not require the editor toolbar, or want to improve overall performance, you can disable the toolbar and the associated meta-data generation.", "ts_visual_composer_extend"); ?>
							</div>
							<div id="ts-advanced-table-scope-metadata" style="float: left; margin-top: 20px;">
								<div class="ts-switch-button ts-codestar-field-switcher ts-advanced-table-tooltip-holder" data-value="0">
									<span class="ts-advanced-table-tooltip-content"><?php echo __("Provides additional advanced features for the table editor toolbar.", "ts_visual_composer_extend"); ?></span>
									<div class="ts-codestar-fieldset">
										<label class="ts-codestar-label">
											<input id="ts-advanced-table-scope-metadata-check" value="1" class="ts-codestar-checkbox ts-advanced-table-scope-metadata-check" name="ts-advanced-table-scope-metadata-check" type="checkbox" checked="checked"> 
											<em data-on="<?php echo __("Yes", "ts_visual_composer_extend"); ?>" data-off="<?php echo __("No", "ts_visual_composer_extend"); ?>"></em>
											<span></span>
										</label>
									</div>
								</div>
								<label class="labelToggleBox" for="ts-advanced-table-scope-metadata-check"><?php echo __("Use Advanced Toolbar", "ts_visual_composer_extend"); ?></label>
							</div>
						</div>
					</div>
					<div id="ts-advanced-table-features-wrapper" class="ts-advanced-table-features-wrapper">
						<span id="ts-advanced-table-scope-label" class="ts-advanced-table-scope-label ts-advanced-table-label-holder"><?php echo __("Editor Setup:", "ts_visual_composer_extend"); ?></span>
						<div id="ts-advanced-table-scope-controls" class="ts-advanced-table-scope-controls" style="display: block; margin-top: 10px; padding: 0;">            
							<div class="ts-vcsc-notice-field ts-vcsc-warning" style="margin-bottom: 30px; font-size: 13px; text-align: justify;">
								<?php echo __("While the table editor below might somewhat look and feel like some of the advanced spreadsheet solutions you might be using at home or at your office (Microsoft Excel, Apple Numbers, etc.), it is naturally not able to provide you with the same scope of advanced features you are accustomed to from those applications. The sole purpose of the provided table editor is to help you to quickly create static content for HTML tables, using a familiar and easy to use interface, but it is obviously not designed to make complex calculations, statistics and/or manipulations.", "ts_visual_composer_extend"); ?>
							</div>
							<div id="ts-advanced-table-scope-context" style="margin-bottom: 20px;">
								<div class="ts-switch-button ts-codestar-field-switcher ts-advanced-table-tooltip-holder" data-value="0">
									<span class="ts-advanced-table-tooltip-content"><?php echo __("Provides a right click context menu for the editor.", "ts_visual_composer_extend"); ?></span>
									<div class="ts-codestar-fieldset">
										<label class="ts-codestar-label">
											<input id="ts-advanced-table-scope-context-check" value="0" class="ts-codestar-checkbox ts-advanced-table-scope-context-check" name="ts-advanced-table-scope-context-check" type="checkbox"> 
											<em data-on="<?php echo __("Yes", "ts_visual_composer_extend"); ?>" data-off="<?php echo __("No", "ts_visual_composer_extend"); ?>"></em>
											<span></span>
										</label>
									</div>
								</div>
								<label class="labelToggleBox" for="ts-advanced-table-scope-context-check"><?php echo __("Provide Context Menu", "ts_visual_composer_extend"); ?></label>
							</div>
							<div id="ts-advanced-table-scope-search" style="margin-bottom: 20px;">
								<div class="ts-switch-button ts-codestar-field-switcher ts-advanced-table-tooltip-holder" data-value="0">
									<span class="ts-advanced-table-tooltip-content"><?php echo __("Provides a search input for the table editor.", "ts_visual_composer_extend"); ?></span>
									<div class="ts-codestar-fieldset">
										<label class="ts-codestar-label">
											<input id="ts-advanced-table-scope-search-check" value="0" class="ts-codestar-checkbox ts-advanced-table-scope-search-check" name="ts-advanced-table-scope-search-check" type="checkbox"> 
											<em data-on="<?php echo __("Yes", "ts_visual_composer_extend"); ?>" data-off="<?php echo __("No", "ts_visual_composer_extend"); ?>"></em>
											<span></span>
										</label>
									</div>
								</div>
								<label class="labelToggleBox" for="ts-advanced-table-scope-search-check"><?php echo __("Provide Editor Cell Search", "ts_visual_composer_extend"); ?></label>
							</div>	
							<div id="ts-advanced-table-scope-comments" style="margin-bottom: 20px;">            
								<div class="ts-switch-button ts-codestar-field-switcher ts-advanced-table-tooltip-holder" data-value="0">
									<span class="ts-advanced-table-tooltip-content"><?php echo __("Allows you to add an internal comment to a cell (can be shown via tooltip in frontend).", "ts_visual_composer_extend"); ?></span>
									<div class="ts-codestar-fieldset">
										<label class="ts-codestar-label">
											<input id="ts-advanced-table-scope-comments-check" value="0" class="ts-codestar-checkbox ts-advanced-table-scope-comments-check" name="ts-advanced-table-scope-comments-check" type="checkbox"> 
											<em data-on="<?php echo __("Yes", "ts_visual_composer_extend"); ?>" data-off="<?php echo __("No", "ts_visual_composer_extend"); ?>"></em>
											<span></span>
										</label>
									</div>
								</div>
								<label class="labelToggleBox" for="ts-advanced-table-scope-comments-check"><?php echo __("Provide Cell Comment Support", "ts_visual_composer_extend"); ?></label>
							</div>
							<div id="ts-advanced-table-scope-validator" style="margin-bottom: 20px;">            
								<div class="ts-switch-button ts-codestar-field-switcher ts-advanced-table-tooltip-holder" data-value="0">
									<span class="ts-advanced-table-tooltip-content"><?php echo __("Adds a cell content validation check based on assigned cell type.", "ts_visual_composer_extend"); ?></span>
									<div class="ts-codestar-fieldset">
										<label class="ts-codestar-label">
											<input id="ts-advanced-table-scope-validator-check" value="0" class="ts-codestar-checkbox ts-advanced-table-scope-validator-check" name="ts-advanced-table-scope-validator-check" type="checkbox"> 
											<em data-on="<?php echo __("Yes", "ts_visual_composer_extend"); ?>" data-off="<?php echo __("No", "ts_visual_composer_extend"); ?>"></em>
											<span></span>
										</label>
									</div>
								</div>
								<label class="labelToggleBox" for="ts-advanced-table-scope-validator-check"><?php echo __("Provide Cell Validation", "ts_visual_composer_extend"); ?></label>
							</div>
							<div id="ts-advanced-table-scope-moving" style="margin-bottom: 20px;">            
								<div class="ts-switch-button ts-codestar-field-switcher ts-advanced-table-tooltip-holder" data-value="0">
									<span class="ts-advanced-table-tooltip-content"><?php echo __("Allows you to drag and move rows and columns into other positions.", "ts_visual_composer_extend"); ?></span>
									<div class="ts-codestar-fieldset">
										<label class="ts-codestar-label">
											<input id="ts-advanced-table-scope-moving-check" value="0" class="ts-codestar-checkbox ts-advanced-table-scope-moving-check" name="ts-advanced-table-scope-moving-check" type="checkbox"> 
											<em data-on="<?php echo __("Yes", "ts_visual_composer_extend"); ?>" data-off="<?php echo __("No", "ts_visual_composer_extend"); ?>"></em>
											<span></span>
										</label>
									</div>
								</div>
								<label class="labelToggleBox" for="ts-advanced-table-scope-moving-check"><?php echo __("Provide Row + Column Drag Support (BETA)", "ts_visual_composer_extend"); ?></label>
							</div>
							<div id="ts-advanced-table-scope-formulas" style="margin-bottom: 20px; display: none;">
								<div class="ts-switch-button ts-codestar-field-switcher ts-advanced-table-tooltip-holder" data-value="0">
									<span class="ts-advanced-table-tooltip-content"><?php echo __("Allows you to use basic formulas to calculate cell values.", "ts_visual_composer_extend"); ?></span>
									<div class="ts-codestar-fieldset">
										<label class="ts-codestar-label">
											<input id="ts-advanced-table-scope-formulas-check" value="0" class="ts-codestar-checkbox ts-advanced-table-scope-formulas-check" name="ts-advanced-table-scope-formulas-check" type="checkbox"> 
											<em data-on="<?php echo __("Yes", "ts_visual_composer_extend"); ?>" data-off="<?php echo __("No", "ts_visual_composer_extend"); ?>"></em>
											<span></span>
										</label>
									</div>
								</div>
								<label class="labelToggleBox" for="ts-advanced-table-scope-formulas-check"><?php echo __("Provide Basic Formula Support (BETA)", "ts_visual_composer_extend"); ?></label>
							</div>
							<div id="ts-advanced-table-scope-merge" style="margin-bottom: 20px;">
								<div class="ts-switch-button ts-codestar-field-switcher ts-advanced-table-tooltip-holder" data-value="0">
									<span class="ts-advanced-table-tooltip-content"><?php echo __("Allows you to merge cells with each other (will negatively impact any responsive behavior and not work with some advanced table features).", "ts_visual_composer_extend"); ?></span>
									<div class="ts-codestar-fieldset">
										<label class="ts-codestar-label">
											<input id="ts-advanced-table-scope-merge-check" value="0" class="ts-codestar-checkbox ts-advanced-table-scope-merge-check" name="ts-advanced-table-scope-merge-check" type="checkbox"> 
											<em data-on="<?php echo __("Yes", "ts_visual_composer_extend"); ?>" data-off="<?php echo __("No", "ts_visual_composer_extend"); ?>"></em>
											<span></span>
										</label>
									</div>
								</div>
								<label class="labelToggleBox" for="ts-advanced-table-scope-merge-check"><?php echo __("Provide Cell Merge Support (BETA)", "ts_visual_composer_extend"); ?></label>
							</div>
							<div id="ts-advanced-table-scope-fixrow" style="margin-bottom: 20px;">
								<div class="ts-switch-button ts-codestar-field-switcher ts-advanced-table-tooltip-holder" data-value="0">
									<span class="ts-advanced-table-tooltip-content"><?php echo __("Fix the first row to the top of the editor when scrolling the table.", "ts_visual_composer_extend"); ?></span>
									<div class="ts-codestar-fieldset">
										<label class="ts-codestar-label">
											<input id="ts-advanced-table-scope-fixrow-check" value="0" class="ts-codestar-checkbox ts-advanced-table-scope-fixrow-check" name="ts-advanced-table-scope-fixrow-check" type="checkbox"> 
											<em data-on="<?php echo __("Yes", "ts_visual_composer_extend"); ?>" data-off="<?php echo __("No", "ts_visual_composer_extend"); ?>"></em>
											<span></span>
										</label>
									</div>
								</div>
								<label class="labelToggleBox" for="ts-advanced-table-scope-fixrow-check"><?php echo __("Scroll Fix First Row (BETA)", "ts_visual_composer_extend"); ?></label>
							</div>
							<div id="ts-advanced-table-scope-fixcolumn" style="margin-bottom: 20px;">
								<div class="ts-switch-button ts-codestar-field-switcher ts-advanced-table-tooltip-holder" data-value="0">
									<span class="ts-advanced-table-tooltip-content"><?php echo __("Fix the first column to the left of the editor when scrolling the table.", "ts_visual_composer_extend"); ?></span>
									<div class="ts-codestar-fieldset">
										<label class="ts-codestar-label">
											<input id="ts-advanced-table-scope-fixcolumn-check" value="0" class="ts-codestar-checkbox ts-advanced-table-scope-fixcolumn-check" name="ts-advanced-table-scope-fixcolumn-check" type="checkbox"> 
											<em data-on="<?php echo __("Yes", "ts_visual_composer_extend"); ?>" data-off="<?php echo __("No", "ts_visual_composer_extend"); ?>"></em>
											<span></span>
										</label>
									</div>
								</div>
								<label class="labelToggleBox" for="ts-advanced-table-scope-fixcolumn-check"><?php echo __("Scroll Fix First Column (BETA)", "ts_visual_composer_extend"); ?></label>
							</div>	
						</div>
					</div>
					<div id="ts-advanced-table-formats-wrapper" class="ts-advanced-table-formats-wrapper">
						<span id="ts-advanced-table-input-label" class="ts-advanced-table-input-label ts-advanced-table-label-holder"><?php echo __("Format Defaults:", "ts_visual_composer_extend"); ?></span>
						<div id="ts-advanced-table-input-controls" class="ts-advanced-table-input-controls" style="display: block; margin-top: 10px; padding: 0;">
							<div class="ts-vcsc-notice-field ts-vcsc-warning" style="margin-bottom: 20px; font-size: 13px; text-align: justify;">
								<?php echo __("The following formats represent the default formats for a variety of cell content types. Most content types allow you to somewhat modify the final format output by using the corresponding controls (currency symbol, number of digits, locale, etc.) in the table editor toolbar. You can always change the default formats in the plugin settings page.", "ts_visual_composer_extend"); ?>
							</div>
							<div class="ts-advanced-table-input-section-wrapper">
								<div class="ts-advanced-table-input-section-left"><?php echo __("Editor Locale:", "ts_visual_composer_extend"); ?></div>
								<div class="ts-advanced-table-input-section-right"><?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_EditorLocale; ?></div>
							</div>
							<div class="ts-advanced-table-input-section-wrapper">
								<div class="ts-advanced-table-input-section-left"><?php echo __("Horizontal Alignment:", "ts_visual_composer_extend"); ?></div>
								<div class="ts-advanced-table-input-section-right"><?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Alignment_Options[$TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_AlignHorizontal]; ?></div>
							</div>
							<div class="ts-advanced-table-input-section-wrapper">
								<div class="ts-advanced-table-input-section-left"><?php echo __("Vertical Alignment:", "ts_visual_composer_extend"); ?></div>
								<div class="ts-advanced-table-input-section-right"><?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Alignment_Options[$TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_AlignVertical]; ?></div>
							</div>
							<div class="ts-advanced-table-input-section-wrapper">
								<div class="ts-advanced-table-input-section-left"><?php echo __("Date Format:", "ts_visual_composer_extend"); ?></div>
								<div class="ts-advanced-table-input-section-right"><?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatDate; ?></div>
							</div>
							<div class="ts-advanced-table-input-section-wrapper">
								<div class="ts-advanced-table-input-section-left"><?php echo __("Time Format:", "ts_visual_composer_extend"); ?></div>
								<div class="ts-advanced-table-input-section-right"><?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTime; ?></div>
							</div>
							<div class="ts-advanced-table-input-section-wrapper">
								<div class="ts-advanced-table-input-section-left"><?php echo __("Number Format:", "ts_visual_composer_extend"); ?></div>
								<div class="ts-advanced-table-input-section-right"><?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatNumeric; ?></div>
							</div>
							<div class="ts-advanced-table-input-section-wrapper">
								<div class="ts-advanced-table-input-section-left"><?php echo __("Currency Format:", "ts_visual_composer_extend"); ?></div>
								<div class="ts-advanced-table-input-section-right"><?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatCurrency; ?></div>
							</div>
							<div class="ts-advanced-table-input-section-wrapper">
								<div class="ts-advanced-table-input-section-left"><?php echo __("Percent Format:", "ts_visual_composer_extend"); ?></div>
								<div class="ts-advanced-table-input-section-right"><?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatPercent; ?></div>
							</div>
						</div>
					</div>
				</section>
				<section id="ts-advanced-table-tabs-section-import" class="<?php echo ($TS_TablesWP_Editor_Section == 4 ? 'content-current' : ''); ?>">
					<div id="ts-advanced-table-import-wrapper" class="ts-advanced-table-import-wrapper" style="margin: 0 auto 10px auto;">
						<span id="ts-advanced-table-import-label" class="ts-advanced-table-import-label ts-advanced-table-label-holder"><?php echo __("Table CSV Import:", "ts_visual_composer_extend"); ?></span>
						<div id="ts-advanced-table-csv-upload" class="ts-advanced-table-csv-upload" style="display: block;">
							<div class="ts-vcsc-notice-field ts-vcsc-warning" style="margin-bottom: 20px; font-size: 13px; text-align: justify;">
								<?php echo __("Importing a CSV file into this table will replace all existing table data and will reset all custom table formatting. Please note that a CSV file does not contain any cell formatting information (including information about merged cells), so you will have to apply any desired formatting after the import has been completed.", "ts_visual_composer_extend"); ?>
							</div>
							<div class="ts-vcsc-notice-field ts-vcsc-critical" style="margin-bottom: 20px; font-size: 13px; text-align: justify;">
								<?php echo __("The CSV Table import has been tested and confirmed with tables that include up to 1,000 rows and 12 columns (12,000 cells). Since all table data is stored and processed through the browser before it is sent to the server for actual storage, any larger tables can easily cause memory overflow issues, preventing the table data from actually getting saved or causing the browser itself to crash.", "ts_visual_composer_extend"); ?>
							</div>
							<div id="ts-advanced-table-csv-box" class="ts-advanced-table-csv-box">
								<input type="file" accept=".csv" name="ts-advanced-table-csv-file" id="ts-advanced-table-csv-file" class="ts-advanced-table-csv-file" data-text="<?php echo __("Select CSV File", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("An error occured; the editor was unable to read the following file:", "ts_visual_composer_extend"); ?>" data-success="<?php echo __("The following file has been imported to your table:", "ts_visual_composer_extend"); ?>" data-rows="<?php echo __("Rows:", "ts_visual_composer_extend"); ?>" data-columns="<?php echo __("Columns:", "ts_visual_composer_extend"); ?>" data-cells="<?php echo __("Cells:", "ts_visual_composer_extend"); ?>" data-name="<?php echo __("File Name:", "ts_visual_composer_extend"); ?>" data-size="<?php echo __("File Size:", "ts_visual_composer_extend"); ?>" data-updated="<?php echo __("Last Updated:", "ts_visual_composer_extend"); ?>"/>
								<label id="ts-advanced-table-csv-label" class="ts-advanced-table-csv-label" for="ts-advanced-table-csv-file" style="display: inline-block;">
									<span id="ts-advanced-table-csv-name" class="ts-advanced-table-csv-name"></span>
									<span id="ts-advanced-table-csv-select" class="ts-advanced-table-csv-select ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
										<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to select the CSV file to be imported from your computer.", "ts_visual_composer_extend"); ?></span>
										<span class="ts-advanced-tables-button-main ts-advanced-tables-button-red ts-advanced-tables-button-text" style="margin: 0;">
											<?php echo __("Select CSV File", "ts_visual_composer_extend"); ?>
										</span>
									</span>
								</label>
								<span id="ts-advanced-table-csv-import" class="ts-advanced-table-csv-import ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder" style="display: none;">
									<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to import the selected CSV file into this table.", "ts_visual_composer_extend"); ?></span>
									<span class="ts-advanced-tables-button-main ts-advanced-tables-button-green ts-advanced-tables-button-upload" style="margin: 0;">
										<?php echo __("Import CSV File", "ts_visual_composer_extend"); ?>
									</span>
								</span>
							</div>
						</div>
					</div>
				</section>
			</div>
		</div>
    </div>
</div>