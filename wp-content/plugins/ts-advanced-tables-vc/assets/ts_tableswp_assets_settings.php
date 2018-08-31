<?php
	global $TS_ADVANCED_TABLESWP;

	function TS_TablesWP_WordPress_Roles_Array() {
		$editable_roles 		= get_editable_roles();
		foreach ($editable_roles as $role => $details) {
			$sub['role'] 		= esc_attr($role);
			$sub['name'] 		= translate_user_role($details['name']);
			$roles[] 			= $sub;
		}
		return $roles;
	}
	function TS_TablesWP_NoUiSlider_Settings_Field($settings, $value, $callback) {
		global $TS_ADVANCED_TABLESWP;
		$param_name     		= isset($settings['param_name']) ? $settings['param_name'] : '';
		$type           		= isset($settings['type']) ? $settings['type'] : '';
		$min            		= isset($settings['min']) ? $settings['min'] : '';
		$max            		= isset($settings['max']) ? $settings['max'] : '';
		$step           		= isset($settings['step']) ? $settings['step'] : '';
		$unit           		= isset($settings['unit']) ? $settings['unit'] : '';
		$decimals				= isset($settings['decimals']) ? $settings['decimals'] : 0;
		$default           		= isset($settings['default']) ? $settings['default'] : '';
		$group           		= isset($settings['group']) ? $settings['group'] : '';
		// Single Input Only
		$pips					= isset($settings['pips']) ? $settings['pips'] : "true";
		$tooltip				= isset($settings['tooltip']) ? $settings['tooltip'] : "false";
		// Range Additions
		$range					= isset($settings['range']) ? $settings['range'] : "false";
		$start					= isset($settings['start']) ? $settings['start'] : $min;
		$end					= isset($settings['end']) ? $settings['end'] : $max;				
		// Other Settings
		$suffix         		= isset($settings['suffix']) ? $settings['suffix'] : '';
		$class          		= isset($settings['class']) ? $settings['class'] : '';				
		$url            		= $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath;
		$output         		= '';
		$randomizer             = mt_rand(999999, 9999999);
		$containerclass			= '';
		if ($range == "false") {
			if ($tooltip == "true") {
				$containerclass	.= " ts-nouislider-input-slider-tooltip";
			}
			if ($pips == "true") {
				$containerclass	.= " ts-nouislider-input-slider-pips";
			}
			if (($tooltip == "false") && ($pips == "false")) {
				$containerclass	= "ts-nouislider-input-slider-basic";
			}
			$output .= '<div id="ts-nouislider-input-slider-wrapper' . $randomizer . '" class="ts-nouislider-input-slider-wrapper clearFixMe ts-settings-parameter-gradient-grey ' . $containerclass . '" style="height: ' . ($pips == "true" ? "100" : "35") . 'px; margin-top: 10px;">';
				$output .= '<div id="ts-nouislider-input-slider-' . $randomizer . '" class="ts-nouislider-input-slider">';
					$output .= '<input id="' . $param_name . '" style="width: 100px; float: left; margin-left: 0px; margin-right: 10px; background: #f5f5f5; color: #666666;" name="' . $param_name . '"  class="ts-nouislider-serial nouislider-input-selector nouislider-input-composer wpb_vc_param_value ' . $param_name . ' ' . $type . '" type="text" min="' . $min . '" max="' . $max . '" step="' . $step . '" value="' . $value . '"/>';
					$output .= '<span style="float: left; margin-right: 20px; margin-top: 10px; min-width: 10px;" class="unit">' . $unit . '</span>';
					$output .= '<span class="ts-nouislider-input-down dashicons-arrow-left" style="position: relative; float: left; display: inline-block; font-size: 30px; top: 5px; cursor: pointer; margin: 0;"></span>';
					$output .= '<div id="ts-nouislider-input-element-' . $randomizer . '" class="ts-nouislider-input ts-nouislider-input-element" data-pips="' . $pips . '" data-tooltip="' . $tooltip . '" data-value="' . $value . '" data-min="' . $min . '" data-max="' . $max . '" data-decimals="' . $decimals . '" data-step="' . $step . '" data-unit="' . $unit . '" data-callback="' . $callback . '" data-default="' . $default . '" data-group="' . $group . '" style="width: 320px; float: left; margin-top: 10px;"></div>';
					$output .= '<span class="ts-nouislider-input-up dashicons-arrow-right" style="position: relative; float: left; display: inline-block; font-size: 30px; top: 5px; cursor: pointer; margin: 0 20px 0 0;"></span>';					
				$output .= '</div>';
			$output .= '</div>';
		} else if ($range == "true") {
			$output .= '<div id="ts-nouislider-range-slider-wrapper-' . $randomizer . '" class="ts-nouislider-range-slider-wrapper clearFixMe ts-settings-parameter-gradient-grey" style="height: 150px;">';
				$output .= '<div id="ts-nouislider-range-slider-' . $randomizer . '" class="ts-nouislider-range-slider">';
					$output .= '<div id="ts-nouislider-range-output-' . $randomizer . '" class="ts-nouislider-range-output" data-controls="ts-nouislider-range-controls-' . $randomizer . '">';
						$output .= '<div id="ts-nouislider-range-human-' . $randomizer . '" class="ts-nouislider-range-human">';	
							$output .= '<span class="ts-nouislider-range-start"></span> - <span class="ts-nouislider-range-end"></span>';							
						$output .= '</div>';
					$output .= '</div>';
					$output .= '<div id="ts-nouislider-range-controls-' . $randomizer . '" class="ts-nouislider-range-controls" data-output="ts-nouislider-range-output-' . $randomizer . '">';
						$output .= '<input style="width: 100px; float: left; margin-left: 0px; margin-right: 10px;" name="' . $param_name . '"  class="ts-nouislider-serial nouislider-range-selector nouislider-input-composer wpb_vc_param_value ' . $param_name . ' ' . $type . '" type="hidden" value="' . $value . '" style="display: none;"/>';
						$output .= '<span class="ts-nouislider-range-lower-down dashicons-arrow-left" style="position: relative; float: left; display: inline-block; font-size: 30px; top: 30px; cursor: pointer; margin: 0;"></span>';
						$output .= '<span class="ts-nouislider-range-lower-up dashicons-arrow-right" style="position: relative; float: left; display: inline-block; font-size: 30px; top: 30px; cursor: pointer; margin: 0 20px 0 0;"></span>';						
						$output .= '<div id="ts-nouislider-range-element-' . $randomizer . '" class="ts-nouislider-range ts-nouislider-range-element" data-value="' . $value . '" data-start="' . $start . '" data-end="' . $end . '" data-min="' . $min . '" data-max="' . $max . '" data-decimals="' . $decimals . '" data-step="' . $step . '" style="width: 400px; float: left; margin: 10px auto;"></div>';
						$output .= '<span class="ts-nouislider-range-upper-down dashicons-arrow-left" style="position: relative; float: none; display: inline-block; font-size: 30px; top: 30px; cursor: pointer; margin: 0 0 0 20px;"></span>';
						$output .= '<span class="ts-nouislider-range-upper-up dashicons-arrow-right" style="position: relative; float: none; display: inline-block; font-size: 30px; top: 30px; cursor: pointer; margin: 0;"></span>';
					$output .= '</div>';
				$output .= '</div>';
			$output .= '</div>';
		}
		return $output;
	}
	function TS_TablesWP_PostTypes_Settings_Field($settings, $value) {
		global $TS_ADVANCED_TABLESWP;
		$param_name     		= isset($settings['param_name']) ? $settings['param_name'] : '';
		$type           		= isset($settings['type']) ? $settings['type'] : '';
		$url            		= $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath;
		$output         		= '';
		$randomizer				= rand(100000, 999999);
		$value_arr 				= $value;
		if (!is_array($value_arr)) {
			$value_arr      	= array_map('trim', explode(',', $value_arr));
		}
		$output .= '<div id="ts-posttypes-selector-holder-' . $randomizer . '" class="ts-posttypes-selector-holder ts-settings-parameter-gradient-grey ts-singleselect-holder">';
			$output .= '<textarea name="' . $param_name . '" id="' . $param_name . '" class="wpb_vc_param_value ' . $param_name . ' ' . $type . '" style="display: none;">' . $value . '</textarea >';
			$output .= '<select multiple="multiple" name="' . $param_name . '_multiple" id="' . $param_name . '_multiple" data-holder="' . $param_name . '" class="ts-multiple-options-selector wpb-input wpb-select dropdown ' . $param_name . '_multiple" value=" ' . $value . '" style="margin-bottom: 20px;" data-selectable="' . __( "Available Post Types:", "ts_visual_composer_extend" ) . '" data-selection="' . __( "Applied Post Types:", "ts_visual_composer_extend" ) . '">';
				$args = array(
					'public'   	=> true,
				 );
				$posttypes 		= get_post_types($args, 'names');
				foreach ($posttypes as $type) {
					if ($type != "attachment") {
						$object	= get_post_type_object($type);
						$output .= '<option value="' . $type . '" ' . selected(in_array($type, $value_arr), true, false) . '>' . $object->labels->singular_name . '</option>';
					}
				}
			$output .= '</select>';
			$output .= '<span class="ts-posttypes-selector-message">' . __( "Click on a name in 'Available Post Types' to add a shortcode generator button to all tinyMCE editors in that post type; click on a name in 'Applied Post Types' to remove generator from that post type.", "ts_visual_composer_extend" ) . '</span>';
		$output .= '</div>';
		return $output;
	}
	function TS_TablesWP_UserRoles_Settings_Field($settings, $value) {
		global $TS_ADVANCED_TABLESWP;
		$param_name     		= isset($settings['param_name']) ? $settings['param_name'] : '';
		$type           		= isset($settings['type']) ? $settings['type'] : '';
		$url            		= $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath;
		$output         		= '';
		$randomizer				= rand(100000, 999999);
		$value_arr 				= $value;
		if (!is_array($value_arr)) {
			$value_arr      	= array_map('trim', explode(',', $value_arr));
		}
		$output .= '<div id="ts-userroles-selector-holder-' . $randomizer . '" class="ts-userroles-selector-holder ts-settings-parameter-gradient-grey ts-singleselect-holder">';
			$output .= '<textarea name="' . $param_name . '" id="' . $param_name . '" class="wpb_vc_param_value ' . $param_name . ' ' . $type . '" style="display: none;">' . $value . '</textarea >';
			$output .= '<select multiple="multiple" name="' . $param_name . '_multiple" id="' . $param_name . '_multiple" data-holder="' . $param_name . '" class="ts-multiple-options-selector wpb-input wpb-select dropdown ' . $param_name . '_multiple" value=" ' . $value . '" style="margin-bottom: 20px;" data-selectable="' . __( "Available User Roles:", "ts_visual_composer_extend" ) . '" data-selection="' . __( "Applied User Roles:", "ts_visual_composer_extend" ) . '">';
				$userroles 		= TS_TablesWP_WordPress_Roles_Array();
				foreach ($userroles as $role) {
					if ($role['role'] != "administrator") {
						$output .= '<option value="' . $role['role'] . '" ' . selected(in_array($role['role'], $value_arr), true, false) . '>' . $role['name'] . '</option>';
					}
				}
			$output .= '</select>';
			$output .= '<span class="ts-userroles-selector-message">' . __( "Click on a role in 'Available User Roles' to allow acces to the table editor for that user role; click on a role in 'Applied User Roles' to revoke the table editor access for that user role.", "ts_visual_composer_extend" ) . '</span>';
		$output .= '</div>';
		return $output;
	}

    // Save / Load Parameters
	// ----------------------
	if (isset($_POST['Submit'])) {
		// Render Preloader Animation
		echo '<div id="ts_vcsc_extend_settings_save" style="position: relative; margin: 20px auto 20px auto; width: 128px; height: 128px;">';
			echo TS_TablesWP_CreatePreloaderCSS("ts-settings-panel-loader", "", 4, "false");
		echo '</div>';        
        // Retrieve Custom Settings
        $TS_TablesWP_Settings_UserDefined           = array(
            // General Settings
            'general'								=> array(
                'reuseids'              			=> ((intval(((isset($_POST['ts_vcsc_extend_settings_defaultReuseIDs'])) ? $_POST['ts_vcsc_extend_settings_defaultReuseIDs'] : 0))) == 0 ? false : true),
                'saveredirect'          			=> false,
                'mainmenu'              			=> ((intval(((isset($_POST['ts_vcsc_extend_settings_defaultMainMenu'])) ? $_POST['ts_vcsc_extend_settings_defaultMainMenu'] : 0))) == 0 ? false : true),
                'initialsort'           			=> trim($_POST['ts_vcsc_extend_settings_defaultInitialSortValue']),
                'initialorder'						=> trim($_POST['ts_vcsc_extend_settings_defaultInitialOrderValue']),
				'exportlist'						=> false,
				'exportoptions'						=> 'print,pdf,csv,excel,copy',
				'loadlanguage'						=> ((intval(((isset($_POST['ts_vcsc_extend_settings_defaultLoadLanguage'])) ? $_POST['ts_vcsc_extend_settings_defaultLoadLanguage'] : 0))) == 0 ? false : true),
				'composerintegrate'					=> ((intval(((isset($_POST['ts_vcsc_extend_settings_defaultComposerIntegrate'])) ? $_POST['ts_vcsc_extend_settings_defaultComposerIntegrate'] : 0))) == 0 ? false : true),
				'deletetables'						=> ((intval(((isset($_POST['ts_vcsc_extend_settings_defaultDeleteTables'])) ? $_POST['ts_vcsc_extend_settings_defaultDeleteTables'] : 0))) == 0 ? false : true),
				'shortcodealways'					=> ((intval(((isset($_POST['ts_vcsc_extend_settings_defaultShortcodeAlways'])) ? $_POST['ts_vcsc_extend_settings_defaultShortcodeAlways'] : 0))) == 0 ? false : true),
				'autoupdate'                        => ((intval(((isset($_POST['ts_vcsc_extend_settings_defaultAutoUpdate'])) ? $_POST['ts_vcsc_extend_settings_defaultAutoUpdate'] : 0))) == 0 ? false : true),
				'editoraccess'						=> (trim($_POST['ts_vcsc_extend_settings_defaultUserRolesEditor']) != "" ? "administrator," . trim($_POST['ts_vcsc_extend_settings_defaultUserRolesEditor']) : "administrator"),
            ),
			// File Loading
			'filestatus'							=> array(
				'loadalways'						=> ((intval(((isset($_POST['ts_vcsc_extend_settings_defaultLoadFilesAlways'])) ? $_POST['ts_vcsc_extend_settings_defaultLoadFilesAlways'] : 0))) == 0 ? false : true),
				'loadtablesaw'						=> true,
				'loadfootable'						=> true,
				'loaddatatable'						=> true,
				'loadtooltipster'					=> true,
				'loadhelpers'						=> true,
			),
			// tinyMCE Injection
			'tinymce'								=> array(
				'allowinject'						=> ((intval(((isset($_POST['ts_vcsc_extend_settings_defaultTinyMCEAllow'])) ? $_POST['ts_vcsc_extend_settings_defaultTinyMCEAllow'] : 0))) == 0 ? false : true),
				'posttypes'							=> trim($_POST['ts_vcsc_extend_settings_defaultTinyMCEPostTypes']),
			),
            // Table Editor
            'tableeditor'							=> array(
                'minheight'							=> '200',
                'maxheight'							=> '800',
                'locale'							=> trim($_POST['ts_vcsc_extend_settings_defaultEditorLocaleValue']),
            ),
            // Cell Content Alignments
            'alignments'            				=> array(
                'horizontal'        				=> trim($_POST['ts_vcsc_extend_settings_defaultAlignHorizontalValue']),
                'vertical'          				=> trim($_POST['ts_vcsc_extend_settings_defaultAlignVerticalValue']),
            ),
            // Cell Format Settings
            'formats'               				=> array(
                'decimalsNumeric'					=> trim($_POST['ts_vcsc_extend_settings_defaultDecimalsNumeric']),
                'decimalsCurrency'					=> trim($_POST['ts_vcsc_extend_settings_defaultDecimalsCurrency']),
                'decimalsPercent'					=> trim($_POST['ts_vcsc_extend_settings_defaultDecimalsPercent']),
                'currencyPlacement'					=> trim($_POST['ts_vcsc_extend_settings_defaultPlacementCurrencyValue']),
                'currencySpacer'					=> ((intval(((isset($_POST['ts_vcsc_extend_settings_defaultCurrencySpacer'])) ? $_POST['ts_vcsc_extend_settings_defaultCurrencySpacer'] : 0))) == 0 ? false : true),
				'percentSpacer'						=> ((intval(((isset($_POST['ts_vcsc_extend_settings_defaultPercentSpacer'])) ? $_POST['ts_vcsc_extend_settings_defaultPercentSpacer'] : 0))) == 0 ? false : true),
                'formatDate'						=> trim($_POST['ts_vcsc_extend_settings_defaultFormatDateValue']),
                'formatTimeHours'					=> trim($_POST['ts_vcsc_extend_settings_defaultFormatTimeHourValue']),
                'formatTimeMinutes'					=> trim($_POST['ts_vcsc_extend_settings_defaultFormatTimeMinuteValue']),
                'formatTimeSeconds'					=> trim($_POST['ts_vcsc_extend_settings_defaultFormatTimeSecondValue']),
                'formatTimeMeridiem'				=> 'A',
            ),
			// Breakpoint Settings
			'breakpoints'							=> array(
				'footableLarge'						=> trim($_POST['ts_vcsc_extend_settings_defaultFootableLarge']),
				'footableMedium'					=> trim($_POST['ts_vcsc_extend_settings_defaultFootableMedium']),
				'footableSmall'						=> trim($_POST['ts_vcsc_extend_settings_defaultFootableSmall']),
				'footableTiny'						=> trim($_POST['ts_vcsc_extend_settings_defaultFootableTiny']),
				'datatableDesktop'					=> 'Infinity',
				'datatableTabletL'					=> trim($_POST['ts_vcsc_extend_settings_defaultDatatableTabletL']),
				'datatableTabletP'					=> trim($_POST['ts_vcsc_extend_settings_defaultDatatableTabletP']),
				'datatableMobileL'					=> trim($_POST['ts_vcsc_extend_settings_defaultDatatableMobileL']),
				'datatableMobileP'					=> trim($_POST['ts_vcsc_extend_settings_defaultDatatableMobileP']),
			),
			// Icon Font Settings
			'fontmanager'							=> array(
				'internalAwesome'					=> ((intval(((isset($_POST['ts_vcsc_extend_tinymce_internalAwesome'])) ? $_POST['ts_vcsc_extend_tinymce_internalAwesome'] : 0))) == 0 ? false : true),
				'internalBrankic'					=> ((intval(((isset($_POST['ts_vcsc_extend_tinymce_internalBrankic'])) ? $_POST['ts_vcsc_extend_tinymce_internalBrankic'] : 0))) == 0 ? false : true),
				'internalCountricons'				=> ((intval(((isset($_POST['ts_vcsc_extend_tinymce_internalCountricons'])) ? $_POST['ts_vcsc_extend_tinymce_internalCountricons'] : 0))) == 0 ? false : true),
				'internalCurrencies'				=> ((intval(((isset($_POST['ts_vcsc_extend_tinymce_internalCurrencies'])) ? $_POST['ts_vcsc_extend_tinymce_internalCurrencies'] : 0))) == 0 ? false : true),
				'internalElegant'					=> ((intval(((isset($_POST['ts_vcsc_extend_tinymce_internalElegant'])) ? $_POST['ts_vcsc_extend_tinymce_internalElegant'] : 0))) == 0 ? false : true),
				'internalEntypo'					=> ((intval(((isset($_POST['ts_vcsc_extend_tinymce_internalEntypo'])) ? $_POST['ts_vcsc_extend_tinymce_internalEntypo'] : 0))) == 0 ? false : true),
				'internalFoundation'				=> ((intval(((isset($_POST['ts_vcsc_extend_tinymce_internalFoundation'])) ? $_POST['ts_vcsc_extend_tinymce_internalFoundation'] : 0))) == 0 ? false : true),
				'internalGenericons'				=> ((intval(((isset($_POST['ts_vcsc_extend_tinymce_internalGenericons'])) ? $_POST['ts_vcsc_extend_tinymce_internalGenericons'] : 0))) == 0 ? false : true),
				'internalIcoMoon'					=> ((intval(((isset($_POST['ts_vcsc_extend_tinymce_internalIcoMoon'])) ? $_POST['ts_vcsc_extend_tinymce_internalIcoMoon'] : 0))) == 0 ? false : true),
				'internalIonicons'					=> ((intval(((isset($_POST['ts_vcsc_extend_tinymce_internalIonicons'])) ? $_POST['ts_vcsc_extend_tinymce_internalIonicons'] : 0))) == 0 ? false : true),
				'internalMapIcons'					=> ((intval(((isset($_POST['ts_vcsc_extend_tinymce_internalMapIcons'])) ? $_POST['ts_vcsc_extend_tinymce_internalMapIcons'] : 0))) == 0 ? false : true),
				'internalMetrize'					=> ((intval(((isset($_POST['ts_vcsc_extend_tinymce_internalMetrize'])) ? $_POST['ts_vcsc_extend_tinymce_internalMetrize'] : 0))) == 0 ? false : true),
				'internalMonuments'					=> ((intval(((isset($_POST['ts_vcsc_extend_tinymce_internalMonuments'])) ? $_POST['ts_vcsc_extend_tinymce_internalMonuments'] : 0))) == 0 ? false : true),
				'internalSocialMedia'				=> ((intval(((isset($_POST['ts_vcsc_extend_tinymce_internalSocialMedia'])) ? $_POST['ts_vcsc_extend_tinymce_internalSocialMedia'] : 0))) == 0 ? false : true),
				'internalThemify'					=> ((intval(((isset($_POST['ts_vcsc_extend_tinymce_internalThemify'])) ? $_POST['ts_vcsc_extend_tinymce_internalThemify'] : 0))) == 0 ? false : true),
				'internalTypicons'					=> ((intval(((isset($_POST['ts_vcsc_extend_tinymce_internalTypicons'])) ? $_POST['ts_vcsc_extend_tinymce_internalTypicons'] : 0))) == 0 ? false : true),				
				'internalCustom'					=> ((intval(((isset($_POST['ts_vcsc_extend_tinymce_internalCustom'])) ? $_POST['ts_vcsc_extend_tinymce_internalCustom'] : 0))) == 0 ? false : true),
				'internalDashicons'					=> ((intval(((isset($_POST['ts_vcsc_extend_tinymce_internalDashicons'])) ? $_POST['ts_vcsc_extend_tinymce_internalDashicons'] : 0))) == 0 ? false : true),
				'composerAwesome'					=> ((intval(((isset($_POST['ts_vcsc_extend_tinymce_composerAwesome'])) ? $_POST['ts_vcsc_extend_tinymce_composerAwesome'] : 0))) == 0 ? false : true),
				'composerEntypo'					=> ((intval(((isset($_POST['ts_vcsc_extend_tinymce_composerEntypo'])) ? $_POST['ts_vcsc_extend_tinymce_composerEntypo'] : 0))) == 0 ? false : true),
				'composerLinecons'					=> ((intval(((isset($_POST['ts_vcsc_extend_tinymce_composerLinecons'])) ? $_POST['ts_vcsc_extend_tinymce_composerLinecons'] : 0))) == 0 ? false : true),
				'composerOpenIconic'				=> ((intval(((isset($_POST['ts_vcsc_extend_tinymce_composerOpenIconic'])) ? $_POST['ts_vcsc_extend_tinymce_composerOpenIconic'] : 0))) == 0 ? false : true),
				'composerTypicons'					=> ((intval(((isset($_POST['ts_vcsc_extend_tinymce_composerTypicons'])) ? $_POST['ts_vcsc_extend_tinymce_composerTypicons'] : 0))) == 0 ? false : true),
				'composerMonoSocial'				=> ((intval(((isset($_POST['ts_vcsc_extend_tinymce_composerMonoSocial'])) ? $_POST['ts_vcsc_extend_tinymce_composerMonoSocial'] : 0))) == 0 ? false : true),
				'composerMaterial'					=> ((intval(((isset($_POST['ts_vcsc_extend_tinymce_composerMaterial'])) ? $_POST['ts_vcsc_extend_tinymce_composerMaterial'] : 0))) == 0 ? false : true),	
			),
            // Global Translations
            'translations'							=> array(
				// TableSaw Text Strings
				'tablesawStack'						=> trim($_POST['ts_vcsc_extend_settings_languageTableSawStack']),
				'tablesawSwipe'						=> trim($_POST['ts_vcsc_extend_settings_languageTableSawSwipe']),
				'tablesawToggle'					=> trim($_POST['ts_vcsc_extend_settings_languageTableSawToggle']),
				'tablesawResponsive'				=> trim($_POST['ts_vcsc_extend_settings_languageTableSawResponsiveness']),
				'tablesawColumns'					=> trim($_POST['ts_vcsc_extend_settings_languageTableSawColumns']),
				'tablesawError'						=> trim($_POST['ts_vcsc_extend_settings_languageTableSawError']),
				'tablesawSort'						=> trim($_POST['ts_vcsc_extend_settings_languageTableSawSort']),
				// FooTable Text Strings
				'footableLengthMenu'				=> trim($_POST['ts_vcsc_extend_settings_languageFootableLengthMenu']),
				'footableLengthAll'					=> trim($_POST['ts_vcsc_extend_settings_languageFootableLengthAll']),
				'footableCountFormat'				=> trim($_POST['ts_vcsc_extend_settings_languageFootableCountFormat']),
				'footablePlaceholder'				=> trim($_POST['ts_vcsc_extend_settings_languageFootablePlaceholder']),
				'footableNoResults'					=> trim($_POST['ts_vcsc_extend_settings_languageFootableNoResults']),
				// DataTable Text Strings
				'datatableProcessing'				=> trim($_POST['ts_vcsc_extend_settings_languageDatatableProcessing']),
				'datatableLengthMenu'				=> trim($_POST['ts_vcsc_extend_settings_languageDatatableLengthMenu']),
				'datatableLengthAll'				=> trim($_POST['ts_vcsc_extend_settings_languageDatatableLengthAll']),
				'datatableInfoMain'					=> trim($_POST['ts_vcsc_extend_settings_languageDatatableInfoMain']),
				'datatableInfoEmpty'				=> trim($_POST['ts_vcsc_extend_settings_languageDatatableInfoEmpty']),
				'datatableInfoFiltered'				=> trim($_POST['ts_vcsc_extend_settings_languageDatatableInfoFiltered']),
				'datatableSearch'					=> trim($_POST['ts_vcsc_extend_settings_languageDatatableSearch']),
				'datatablePlaceholder'				=> trim($_POST['ts_vcsc_extend_settings_languageDatatablePlaceholder']),
				'datatableZeroRecords'				=> trim($_POST['ts_vcsc_extend_settings_languageDatatableZeroRecords']),
				'datatableFirst'					=> trim($_POST['ts_vcsc_extend_settings_languageDatatableFirst']),
				'datatablePrevious'					=> trim($_POST['ts_vcsc_extend_settings_languageDatatablePrevious']),
				'datatableNext'						=> trim($_POST['ts_vcsc_extend_settings_languageDatatableNext']),
				'datatableLast'						=> trim($_POST['ts_vcsc_extend_settings_languageDatatableLast']),
				'datatablePrint'					=> trim($_POST['ts_vcsc_extend_settings_languageDatatablePrint']),
				'datatablePDF'						=> trim($_POST['ts_vcsc_extend_settings_languageDatatablePDF']),
				'datatableExcel'					=> trim($_POST['ts_vcsc_extend_settings_languageDatatableExcel']),
				'datatableCSV'						=> trim($_POST['ts_vcsc_extend_settings_languageDatatableCSV']),
				'datatableCopy'						=> trim($_POST['ts_vcsc_extend_settings_languageDatatableCopy']),
            ),
        );
        update_option("ts_tablesplus_extend_settings_globals", $TS_TablesWP_Settings_UserDefined);
		// Form Confirmation Check
		update_option('ts_tablesplus_extend_settings_updated', 1);
        // Reload Settings Page
		echo '<script> window.location="' . $_SERVER['REQUEST_URI'] . '"; </script> ';
		//Header('Location: '.$_SERVER['REQUEST_URI']);
		Exit();
	} else {
		// Check License Key Message
		if (($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent == true) && ($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginValid == true) && ($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginSupport == true) && ($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginExtended == false)) {
			$TS_TablesWP_Settings_ShowUpdate		= true;
		} else {
			$TS_TablesWP_Settings_ShowUpdate		= false;
		}
		// Check for Migration Requirement
		if ((count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables) > 0) && ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Migrated == false)) {
			$TS_TablesWP_NeedMigration				= true;
		} else {
			$TS_TablesWP_NeedMigration				= false;
		}
		// Form Confirmation Check
		if (get_option('ts_tablesplus_extend_settings_updated') == 1) {
			echo "\n";
			echo "<script type='text/javascript'>" . "\n";
				echo "var SettingsSaved = true;" . "\n";
			echo "</script>" . "\n";
		} else {
			echo "\n";
			echo "<script type='text/javascript'>" . "\n";
				echo "var SettingsSaved = false;" . "\n";
			echo "</script>" . "\n";
		}
		update_option('ts_tablesplus_extend_settings_updated', 0);
	}
?>
<div id="ts_vcsc_extend_errors" style="display: none;">
	<div class="ts-vcsc-section-main">
		<div class="ts-vcsc-section-title ts-vcsc-section-show"><i class="dashicons-hammer ts-vcsc-section-title-icon"></i><span class="ts-vcsc-section-title-header"></span></div>
		<div class="ts-vcsc-section-content"></div>
	</div>
</div>
<form id="ts-vcsc-advancedtables-check-wrap" data-type="advancedtables" class="ts-vcsc-advancedtables-check-wrap" name="ts-vcsc-advancedtables-check-wrap" autocomplete="off" style="margin-top: 25px; width: 100%;" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']) . ''; ?>">
	<span id="ts-vcsc-advancedtables-check-true" style="display: none !important; margin-bottom: 20px;">
		<input type="text" style="width: 30%;" id="ts_vcsc_extend_settings_true" name="ts_vcsc_extend_settings_true" value="0" size="100">
		<input type="text" style="width: 30%;" id="ts_vcsc_extend_settings_count" name="ts_vcsc_extend_settings_count" value="0" size="100">
	</span>
	<div id="ts-vcsc-advancedtables-validation-messages" style="display: none !important;">
		<span id="ts-advancedtables-validation-plugin-name" style="display: none;"><?php echo __("Tablenator - Advanced Tables for WordPress & WP Bakery Page Builder", "ts_visual_composer_extend"); ?></span>
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
		<span id="ts-advancedtables-validation-popup-saved" style="display: none;"><?php echo __("All settings have been successfully saved!", "ts_visual_composer_extend"); ?></span>
		<span id="ts-advancedtables-validation-popup-partial" style="display: none;"><?php echo __("Problem: Settings have been saved but are not complete. Please fix the following Errors:", "ts_visual_composer_extend"); ?></span>
	</div>
	<div class="wrapper ts-vcsc-settings-group-container">		
		<div class="ts-vcsc-settings-group-header">
			<div class="display_header">
				<h2><span class="dashicons dashicons-editor-table"></span><?php echo __("Tablenator - Advanced Tables for WordPress", "ts_visual_composer_extend"); ?> v<?php echo TS_TablesWP_GetPluginVersion(); ?> ... <?php echo __("Settings Panel", "ts_visual_composer_extend"); ?></h2>
			</div>
			<div class="clear"></div>
		</div>
		<div class="ts-vcsc-settings-group-topbar ts-vcsc-settings-group-buttonbar">
			<a href="javascript:void(0);" id="ts-vcsc-settings-group-toggle" class="ts-vcsc-settings-group-toggle" data-toggle-status="false">Expand</a>
			<div class="ts-vcsc-settings-group-actionbar">
				<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder ts-advanced-table-tooltip-right ts-advanced-table-tooltip-bottom">
					<span class="ts-advanced-table-tooltip-content"><?php _e("Click here to save your plugin settings.", "ts_visual_composer_extend"); ?></span>
					<button type="submit" name="Submit" id="ts_vcsc_extend_settings_submit_1" class="ts-advanced-tables-button-main ts-advanced-tables-button-blue ts-advanced-tables-button-save" style="margin: 0;">
						<?php echo __("Save Settings", "ts_visual_composer_extend"); ?>
					</button>
				</div>				
			</div>
			<div class="clear"></div>
		</div>		
		<div id="v-nav" class="ts-vcsc-settings-group-tabs">
			<ul id="v-nav-main" data-type="settings">
				<li id="link-ts-settings-logo" class="first" style="border-bottom: 1px solid #DDD; height: 260px;">
					<img style="width: 100%; height: auto; margin: 0 auto;" src="<?php echo TS_TablesWP_GetResourceURL('images/logos/tablenator_logo.png'); ?>">
				</li>
				<li id="link-ts-advancedtables-general" 		data-tab="ts-advancedtables-general" 			data-order="1"		data-name="<?php echo __("General Settings", "ts_visual_composer_extend"); ?>"			style="display: block;"     class="link-data current"><i class="dashicons-admin-generic"></i><?php echo __("General Settings", "ts_visual_composer_extend"); ?><span id="errorTab1" class="errorMarker"></span></li>
				<li id="link-ts-advancedtables-format" 		    data-tab="ts-advancedtables-format"			    data-order="2"		data-name="<?php echo __("Format Settings", "ts_visual_composer_extend"); ?>"			style="display: block;" 	class="link-data"><i class="dashicons-editor-customchar"></i><?php echo __("Format Settings", "ts_visual_composer_extend"); ?><span id="errorTab2" class="errorMarker"></span></li>
				<li id="link-ts-advancedtables-translations"	data-tab="ts-advancedtables-translations"		data-order="3"		data-name="<?php echo __("Translation Settings", "ts_visual_composer_extend"); ?>"		style="display: block;" 	class="link-data"><i class="dashicons-translation"></i><?php echo __("Translation Settings", "ts_visual_composer_extend"); ?><span id="errorTab3" class="errorMarker"></span></li>
				<li id="link-ts-advancedtables-breakpoints"		data-tab="ts-advancedtables-breakpoints"		data-order="4"		data-name="<?php echo __("Breakpoint Settings", "ts_visual_composer_extend"); ?>"		style="display: block;" 	class="link-data"><i class="dashicons-laptop"></i><?php echo __("Breakpoint Settings", "ts_visual_composer_extend"); ?><span id="errorTab4" class="errorMarker"></span></li>
				<li id="link-ts-advancedtables-iconfonts"		data-tab="ts-advancedtables-iconfonts"			data-order="5"		data-name="<?php echo __("Icon Fonts Manager", "ts_visual_composer_extend"); ?>"		style="display: block;" 	class="link-data"><i class="dashicons-index-card"></i><?php echo __("Icon Fonts Manager", "ts_visual_composer_extend"); ?><span id="errorTab5" class="errorMarker"></span></li>
				<li id="link-ts-advancedtables-changelog" 		data-tab="ts-advancedtables-changelog"			data-order="6"		data-name="<?php echo __("Changelog", "ts_visual_composer_extend"); ?>"					style="display: block;" 	class="link-data"><i class="dashicons-media-text"></i><?php echo __("Changelog", "ts_visual_composer_extend"); ?><span id="errorTab6" class="errorMarker"></span></li>
				<?php
					if ($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginExtended == false) {
						echo '<a href="admin.php?page=TS_TablesWP_License" target="_parent" style="color: #000000;">';
							echo '<li id="link-ts-advancedtables-license" 		data-tab="ts-advancedtables-license"	data-order="7"		data-name="' . __("License Key", "ts_visual_composer_extend") . '"				style="display: block;" 	class="link-url"><i class="dashicons-admin-network"></i>' . __("License Key", "ts_visual_composer_extend") . '<span id="errorTab7" class="errorMarker"></span></li>';
						echo '</a>';
					}
				?>
			</ul>
		</div>
		<div class="ts-vcsc-settings-group-main">
			<div id="ts-advancedtables-general" class="tab-content">
				<div class="ts-vcsc-section-main">
					<div class="ts-vcsc-section-title ts-vcsc-section-show"><i class="dashicons-admin-links"></i><?php echo __("Quick Links", "ts_visual_composer_extend"); ?></div>
					<div class="ts-vcsc-section-content">
                        <div class="ts-advanced-table-controls-wrapper" style="float: none; display: block; width: 100%; min-height: 40px; margin: 0; padding: 10px 0;">
                            <div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
                                <span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to go to a listing of all created tables.", "ts_visual_composer_extend"); ?></span>
                                <a href="<?php echo admin_url('admin.php?page=TS_TablesWP_Tables'); ?>" target="_parent" class="ts-advanced-tables-button-main ts-advanced-tables-button-turquoise ts-advanced-tables-button-listing" style="margin: 0 20px 0 0;">
									<?php echo __("View Tables List", "ts_visual_composer_extend"); ?>
								</a>
                            </div>
							<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder" style="display: <?php echo (($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Migrated == false) ? "none" : "inline-block"); ?>;">
                                <span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to create a new table.", "ts_visual_composer_extend"); ?></span>
                                <a href="<?php echo admin_url('admin.php?page=TS_TablesWP_Grid'); ?>" target="_parent" class="ts-advanced-tables-button-main ts-advanced-tables-button-green ts-advanced-tables-button-table" style="margin: 0 20px 0 0;">
									<?php echo __("Add New Table", "ts_visual_composer_extend"); ?>
								</a>
                            </div>
                            <div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
                                <span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to go to the official manual for the plugin.", "ts_visual_composer_extend"); ?></span>
                                <a href="http://www.tablenatorvc.krautcoding.com/documentation/" target="_blank" class="ts-advanced-tables-button-main ts-advanced-tables-button-orange ts-advanced-tables-button-file" style="margin: 0 20px 0 0;">
									<?php echo __("Plugin Manual", "ts_visual_composer_extend"); ?>
								</a>
                            </div>	
                            <div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
                                <span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to go to the official support forum for the plugin.", "ts_visual_composer_extend"); ?></span>
                                <a href="http://helpdesk.krautcoding.com/forums/forum/wordpress-plugins/advanced-tables-for-visual-composer/" target="_blank" class="ts-advanced-tables-button-main ts-advanced-tables-button-red ts-advanced-tables-button-wrench" style="margin: 0;">
									<?php echo __("Support Forum", "ts_visual_composer_extend"); ?>
								</a>
                            </div>
                        </div>
                    </div>
                </div>
				<div class="ts-vcsc-section-main">
					<div class="ts-vcsc-section-title ts-vcsc-section-show"><i class="dashicons-admin-generic"></i><?php echo __("General Settings", "ts_visual_composer_extend"); ?></div>
					<div class="ts-vcsc-section-content">
						<div style="margin-top: 10px; margin-bottom: 20px; display: <?php echo ($TS_TablesWP_Settings_ShowUpdate == true ? "block" : "none"); ?>">
							<h4><?php echo __("Auto-Update Routine", "ts_visual_composer_extend"); ?></h4>
							<div class="ts-vcsc-notice-field ts-vcsc-warning" style="margin-top: 10px; font-size: 13px; text-align: justify;">
								<?php echo __("The auto-update routine requires a valid license key to be entered on the license key page and confirmed via the Envato API.", "ts_visual_composer_extend"); ?>
							</div>	
							<p style="font-size: 12px; margin: 10px auto;"><?php echo __("Define if you want to enable the auto-update routine for this plugin:", "ts_visual_composer_extend"); ?></p>					
                            <div class="ts-switch-button ts-codestar-field-switcher" data-value="<?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_AutoUpdate == true ? '1' : '0'); ?>">
								<div class="ts-codestar-fieldset">
									<label class="ts-codestar-label">
										<input id="ts_vcsc_extend_settings_defaultAutoUpdate" data-order="1" value="<?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_AutoUpdate == true ? '1' : '0'); ?>" class="ts-codestar-checkbox ts_vcsc_extend_settings_defaultAutoUpdate" name="ts_vcsc_extend_settings_defaultAutoUpdate" type="checkbox" <?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_AutoUpdate == true ? 'checked="checked"' : ''); ?>> 
										<em data-on="<?php echo __("Yes", "ts_visual_composer_extend"); ?>" data-off="<?php echo __("No", "ts_visual_composer_extend"); ?>"></em>
										<span></span>
									</label>
								</div>
							</div>
							<label class="labelToggleBox" for="ts_vcsc_extend_settings_defaultAutoUpdate"><?php echo __("Use Auto-Update Routine", "ts_visual_composer_extend"); ?></label>
						</div>
						<div style="margin-top: 10px; margin-bottom: 20px;">
							<h4><?php echo __("Placement of Plugin Menu", "ts_visual_composer_extend"); ?></h4>
							<p style="font-size: 12px; margin: 10px auto;"><?php echo __("Define where the menu for this plugin should be placed in WordPress; if disabled, the main menu will be placed in the 'Settings' section:", "ts_visual_composer_extend"); ?></p>					
                            <div class="ts-switch-button ts-codestar-field-switcher" data-value="<?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_MainMenu == true ? '1' : '0'); ?>">
								<div class="ts-codestar-fieldset">
									<label class="ts-codestar-label">
										<input id="ts_vcsc_extend_settings_defaultMainMenu" data-order="1" value="<?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_MainMenu == true ? '1' : '0'); ?>" class="ts-codestar-checkbox ts_vcsc_extend_settings_defaultMainMenu" name="ts_vcsc_extend_settings_defaultMainMenu" type="checkbox" <?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_MainMenu == true ? 'checked="checked"' : ''); ?>> 
										<em data-on="<?php echo __("Yes", "ts_visual_composer_extend"); ?>" data-off="<?php echo __("No", "ts_visual_composer_extend"); ?>"></em>
										<span></span>
									</label>
								</div>
							</div>
							<label class="labelToggleBox" for="ts_vcsc_extend_settings_defaultMainMenu"><?php echo __("Give Plugin its own menu", "ts_visual_composer_extend"); ?></label>
						</div>
						<div style="margin-top: 10px; margin-bottom: 20px;">
							<h4><?php echo __("Delete Table Data On Uninstall", "ts_visual_composer_extend"); ?></h4>
							<p style="font-size: 12px; margin: 10px auto;"><?php echo __("Define if the plugin should remove all table data from the WordPress database once you uninstall the plugin:", "ts_visual_composer_extend"); ?></p>					
                            <div class="ts-switch-button ts-codestar-field-switcher" data-value="<?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DeleteTables == true ? '1' : '0'); ?>">
								<div class="ts-codestar-fieldset">
									<label class="ts-codestar-label">
										<input id="ts_vcsc_extend_settings_defaultDeleteTables" data-order="1" value="<?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DeleteTables == true ? '1' : '0'); ?>" class="ts-codestar-checkbox ts_vcsc_extend_settings_defaultDeleteTables" name="ts_vcsc_extend_settings_defaultDeleteTables" type="checkbox" <?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DeleteTables == true ? 'checked="checked"' : ''); ?>> 
										<em data-on="<?php echo __("Yes", "ts_visual_composer_extend"); ?>" data-off="<?php echo __("No", "ts_visual_composer_extend"); ?>"></em>
										<span></span>
									</label>
								</div>
							</div>
							<label class="labelToggleBox" for="ts_vcsc_extend_settings_defaultDeleteTables"><?php echo __("Delete Table Data On Uninstall", "ts_visual_composer_extend"); ?></label>
						</div>
						<div style="margin-top: 10px; margin-bottom: 20px; display: <?php echo ((substr(get_bloginfo('language'), 0, 2) != "en") ? "block" : "none"); ?>">
							<h4><?php echo __("Load Language File", "ts_visual_composer_extend"); ?></h4>
							<p style="font-size: 12px; margin: 10px auto;"><?php echo __("Define if the plugin should load a language (translation) file for the backend if available:", "ts_visual_composer_extend"); ?></p>
                            <div class="ts-switch-button ts-codestar-field-switcher" data-value="<?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_LoadLanguage == true ? '1' : '0'); ?>">
								<div class="ts-codestar-fieldset">
									<label class="ts-codestar-label">
										<input id="ts_vcsc_extend_settings_defaultLoadLanguage" data-order="1" value="<?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_LoadLanguage == true ? '1' : '0'); ?>" class="ts-codestar-checkbox ts_vcsc_extend_settings_defaultLoadLanguage" name="ts_vcsc_extend_settings_defaultLoadLanguage" type="checkbox" <?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_LoadLanguage == true ? 'checked="checked"' : ''); ?>> 
										<em data-on="<?php echo __("Yes", "ts_visual_composer_extend"); ?>" data-off="<?php echo __("No", "ts_visual_composer_extend"); ?>"></em>
										<span></span>
									</label>
								</div>
							</div>
							<label class="labelToggleBox" for="ts_vcsc_extend_settings_defaultLoadLanguage"><?php echo __("Load Language File", "ts_visual_composer_extend"); ?></label>
						</div>
						<div style="margin-top: 10px; margin-bottom: 20px;">
							<h4><?php echo __("Always Load Shortcode", "ts_visual_composer_extend"); ?></h4>							
							<div class="ts-vcsc-notice-field ts-vcsc-warning" style="margin-top: 10px; font-size: 13px; text-align: justify;">
								<?php echo __("Usually, the shortcode to actually render a table is only needed on the frontend, but not when using the WordPress backend. If for whatever reason you require the table shortcode to be loaded and registered beyond a normal frontend page, you can make the shortcode available at all times by using the option below.", "ts_visual_composer_extend"); ?>
							</div>							
							<p style="font-size: 12px; margin: 10px auto;"><?php echo __("Define if the plugin should always load and register the table shortcode, even within the WordPress backend:", "ts_visual_composer_extend"); ?></p>					
                            <div class="ts-switch-button ts-codestar-field-switcher" data-value="<?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_ShortcodeAlways == true ? '1' : '0'); ?>">
								<div class="ts-codestar-fieldset">
									<label class="ts-codestar-label">
										<input id="ts_vcsc_extend_settings_defaultShortcodeAlways" data-order="1" value="<?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_ShortcodeAlways == true ? '1' : '0'); ?>" class="ts-codestar-checkbox ts_vcsc_extend_settings_defaultShortcodeAlways" name="ts_vcsc_extend_settings_defaultShortcodeAlways" type="checkbox" <?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_ShortcodeAlways == true ? 'checked="checked"' : ''); ?>> 
										<em data-on="<?php echo __("Yes", "ts_visual_composer_extend"); ?>" data-off="<?php echo __("No", "ts_visual_composer_extend"); ?>"></em>
										<span></span>
									</label>
								</div>
							</div>
							<label class="labelToggleBox" for="ts_vcsc_extend_settings_defaultShortcodeAlways"><?php echo __("Always Load Shortcode", "ts_visual_composer_extend"); ?></label>
						</div>
						<div style="margin-top: 10px; margin-bottom: 20px;">
							<h4><?php echo __("Reuse Table ID's", "ts_visual_composer_extend"); ?></h4>							
							<div class="ts-vcsc-notice-field ts-vcsc-warning" style="margin-top: 10px; font-size: 13px; text-align: justify;">
								<?php echo __("By default, the plugin will ignore the ID's that used to belong to tables that have since been deleted. If you want to start reusing those ID's, simply use the option provided below and the plugin will start filling discarded ID's whenever you create a new table.", "ts_visual_composer_extend"); ?>
							</div>							
							<p style="font-size: 12px; margin: 10px auto;"><?php echo __("Define if the plugin should reuse discarded ID's from deleted tables when creating a new table:", "ts_visual_composer_extend"); ?></p>					
                            <div class="ts-switch-button ts-codestar-field-switcher" data-value="<?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_ReuseIDs == true ? '1' : '0'); ?>">
								<div class="ts-codestar-fieldset">
									<label class="ts-codestar-label">
										<input id="ts_vcsc_extend_settings_defaultReuseIDs" data-order="1" value="<?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_ReuseIDs == true ? '1' : '0'); ?>" class="ts-codestar-checkbox ts_vcsc_extend_settings_defaultReuseIDs" name="ts_vcsc_extend_settings_defaultReuseIDs" type="checkbox" <?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_ReuseIDs == true ? 'checked="checked"' : ''); ?>> 
										<em data-on="<?php echo __("Yes", "ts_visual_composer_extend"); ?>" data-off="<?php echo __("No", "ts_visual_composer_extend"); ?>"></em>
										<span></span>
									</label>
								</div>
							</div>
							<label class="labelToggleBox" for="ts_vcsc_extend_settings_defaultReuseIDs"><?php echo __("Reuse Table ID's", "ts_visual_composer_extend"); ?></label>
						</div>
					</div>		
				</div>
				<div class="ts-vcsc-section-main">
					<div class="ts-vcsc-section-title ts-vcsc-section-hide"><i class="dashicons-editor-table"></i><?php echo __("Table Listing Settings", "ts_visual_composer_extend"); ?></div>
					<div class="ts-vcsc-section-content slideFade" style="display: none;">
                        <div style="margin-top: 10px;">
                            <h4><?php echo __("Initial Sort Column for Table Listing:", "ts_visual_composer_extend"); ?></h4>
                            <p style="font-size: 12px;"><?php echo __("Select the initial column to sort the listing of all created tables after.", "ts_visual_composer_extend"); ?></p>
                            <input name="ts_vcsc_extend_settings_defaultInitialSortValue" id="ts_vcsc_extend_settings_defaultInitialSortValue" class="" style="display: none;" type="hidden" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_InitialSort; ?>">
                            <select id="ts_vcsc_extend_settings_defaultInitialSortSelect" name="ts_vcsc_extend_settings_defaultInitialSortSelect" class="ts-single-options-selector" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_InitialSort; ?>" data-callback="" data-holder="ts_vcsc_extend_settings_defaultInitialSortValue">
                                <option value="1" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_InitialSort, '1'); ?>><?php echo __("Table ID", "ts_visual_composer_extend"); ?></option>
                                <option value="2" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_InitialSort, '2'); ?>><?php echo __("Table Name", "ts_visual_composer_extend"); ?></option>
                                <option value="3" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_InitialSort, '3'); ?>><?php echo __("Date Created", "ts_visual_composer_extend"); ?></option>
                                <option value="4" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_InitialSort, '4'); ?>><?php echo __("Date Last Updated", "ts_visual_composer_extend"); ?></option>
                            </select>
                        </div>
                        <div style="margin-top: 10px;">
                            <h4><?php echo __("Initial Sort Order for Table Listing:", "ts_visual_composer_extend"); ?></h4>
                            <p style="font-size: 12px;"><?php echo __("Select the initial sort order of the listing of all created tables.", "ts_visual_composer_extend"); ?></p>
                            <input name="ts_vcsc_extend_settings_defaultInitialOrderValue" id="ts_vcsc_extend_settings_defaultInitialOrderValue" class="" style="display: none;" type="hidden" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_InitialOrder; ?>">
                            <select id="ts_vcsc_extend_settings_defaultInitialOrderSelect" name="ts_vcsc_extend_settings_defaultInitialOrderSelect" class="ts-single-options-selector" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_InitialOrder; ?>" data-callback="" data-holder="ts_vcsc_extend_settings_defaultInitialOrderValue">
                                <option value="asc" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_InitialOrder, 'asc'); ?>><?php echo __("Ascending", "ts_visual_composer_extend"); ?></option>
                                <option value="desc" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_InitialOrder, 'desc'); ?>><?php echo __("Descending", "ts_visual_composer_extend"); ?></option>
                            </select>
                        </div>
					</div>
				</div>
				<div class="ts-vcsc-section-main">
					<div class="ts-vcsc-section-title ts-vcsc-section-hide"><i class="dashicons-edit"></i><?php echo __("Table Editor Access", "ts_visual_composer_extend"); ?></div>
					<div class="ts-vcsc-section-content slideFade" style="display: none;">
						<div style="margin-top: 10px; margin-bottom: 20px;">
							<h4><?php echo __("Table Editor Access by User Roles", "ts_visual_composer_extend"); ?></h4>
							<div class="ts-vcsc-notice-field ts-vcsc-warning" style="margin-top: 10px; font-size: 13px; text-align: justify;">
								<?php echo __("The table editor and other table related actions are always accessible for users with administrator priviliges, and are by default also enabled for users with editor roles. If required, you can allow other user roles to have access to those routines as well, using the selection options below.", "ts_visual_composer_extend"); ?>
							</div>
						</div>
						<div style="margin-top: 10px; margin-bottom: 10px;">
							<?php
								$settings = array(
									"param_name"			=> "ts_vcsc_extend_settings_defaultUserRolesEditor",
									"value"					=> implode(",", $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_EditorAccess),
								);
								echo TS_TablesWP_UserRoles_Settings_Field($settings, implode(",", $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_EditorAccess));
							?>
						</div>
					</div>
				</div>
				<div class="ts-vcsc-section-main">
					<div class="ts-vcsc-section-title ts-vcsc-section-hide"><i class="dashicons-editor-kitchensink"></i><?php echo __("tinyMCE Editor Injection", "ts_visual_composer_extend"); ?></div>
					<div class="ts-vcsc-section-content slideFade" style="display: none;">
						<div style="margin-top: 10px; margin-bottom: 20px;">
							<h4><?php echo __("Add Shortcode Generator to tinyMCE Editors", "ts_visual_composer_extend"); ?></h4>
							<div class="ts-vcsc-notice-field ts-vcsc-success" style="margin-top: 10px; font-size: 13px; text-align: justify;">
								<?php echo __("If you want to embed the table shortcodes in public post types that are not set to use WP Bakery Page Builder (formerly Visual Composer), you can set the plugin to create an additional 'Add Shortcode' button to the default WordPress tinyMCE editor, which will open the shortcode generator for the tables. If you are more concerned about WordPress backend performance and do not want to add any more to the the page editor in WordPress, you can always access that shortcode generator on the page that lists all created tables.", "ts_visual_composer_extend"); ?>
							</div>
							<p style="font-size: 12px; margin: 10px auto;"><?php echo __("Define if the plugin should add a shortcode generator to all WordPress tinyMCE editors; enabling this option will require you to select the corresponding post types you want to apply this routine to:", "ts_visual_composer_extend"); ?></p>
							<div class="ts-switch-button ts-codestar-field-switcher" data-value="<?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_TinyMCEAllow == true ? '1' : '0'); ?>">
								<div class="ts-codestar-fieldset">
									<label class="ts-codestar-label">
										<input id="ts_vcsc_extend_settings_defaultTinyMCEAllow" data-order="1" value="<?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_TinyMCEAllow == true ? '1' : '0'); ?>" class="ts-codestar-checkbox ts_vcsc_extend_settings_defaultTinyMCEAllow" name="ts_vcsc_extend_settings_defaultTinyMCEAllow" type="checkbox" <?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_TinyMCEAllow == true ? 'checked="checked"' : ''); ?>> 
										<em data-on="<?php echo __("Yes", "ts_visual_composer_extend"); ?>" data-off="<?php echo __("No", "ts_visual_composer_extend"); ?>"></em>
										<span></span>
									</label>
								</div>
							</div>
							<label class="labelToggleBox" for="ts_vcsc_extend_settings_defaultTinyMCEAllow"><?php echo __("Add Shortcode Generator to tinyMCE Editors", "ts_visual_composer_extend"); ?></label>
						</div>
						<div id="ts_vcsc_extend_settings_defaultTinyMCEAllow_true" style="display: <?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_TinyMCEAllow == true ? 'block' : 'none'); ?>; margin-top: 10px; margin-bottom: 10px;">
							<?php
								$settings = array(
									"param_name"			=> "ts_vcsc_extend_settings_defaultTinyMCEPostTypes",
									"value"					=> implode(",", $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_TinyMCEPostTypes),
								);
								echo TS_TablesWP_PostTypes_Settings_Field($settings, implode(",", $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_TinyMCEPostTypes));
							?>
						</div>
					</div>
				</div>
				<div class="ts-vcsc-section-main" style="display: <?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_VisualComposer_Active == "true" ? "block" : "none"); ?>">
					<div class="ts-vcsc-section-title ts-vcsc-section-hide"><i class="dashicons-lightbulb"></i><?php echo __("WP Bakery Page Builder Integration", "ts_visual_composer_extend"); ?></div>
					<div class="ts-vcsc-section-content slideFade" style="display: none;">
						<div style="margin-top: 10px; margin-bottom: 20px;">										
							<h4><?php echo __("Integrate with WP Bakery Page Builder", "ts_visual_composer_extend"); ?></h4>
							<?php
								echo '<div class="ts-vcsc-notice-field ts-vcsc-warning" style="margin-top: 10px; font-size: 13px; text-align: justify;">';
									echo __("When wanting to use this plugin within the WP Bakery Page Builder plugin via its dedicated elements, please ensure that your WP Bakery Page Builder version has not been modified beyond a point where it becomes incompatible with 3rd party add-ons like this. This is particularly important if WP Bakery Page Builder came bundled with your theme, as some theme developers heavily modify WP Bakery Page Builder in order to allow for certain theme functions. Unfortunately, some of these modification can prevent add-ons from working correctly.", "ts_visual_composer_extend");			
								echo '</div>';
							?>	
							<p style="font-size: 12px; margin: 10px auto;"><?php echo __("Define if the plugin should integrate itself with the WP Bakery Page Builder plugin:", "ts_visual_composer_extend"); ?></p>
							<div class="ts-switch-button ts-codestar-field-switcher" data-value="<?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_ComposerIntegrate == true ? '1' : '0'); ?>">
								<div class="ts-codestar-fieldset">
									<label class="ts-codestar-label">
										<input id="ts_vcsc_extend_settings_defaultComposerIntegrate" data-order="1" value="<?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_ComposerIntegrate == true ? '1' : '0'); ?>" class="ts-codestar-checkbox ts_vcsc_extend_settings_defaultComposerIntegrate" name="ts_vcsc_extend_settings_defaultComposerIntegrate" type="checkbox" <?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_ComposerIntegrate == true ? 'checked="checked"' : ''); ?>> 
										<em data-on="<?php echo __("Yes", "ts_visual_composer_extend"); ?>" data-off="<?php echo __("No", "ts_visual_composer_extend"); ?>"></em>
										<span></span>
									</label>
								</div>
							</div>
							<label class="labelToggleBox" for="ts_vcsc_extend_settings_defaultComposerIntegrate"><?php echo __("Integrate with WP Bakery Page Builder", "ts_visual_composer_extend"); ?></label>
						</div>
					</div>
				</div>
				<div class="ts-vcsc-section-main">
					<div class="ts-vcsc-section-title ts-vcsc-section-hide"><i class="dashicons-download"></i><?php echo __("JS/CSS Files Load Behavior", "ts_visual_composer_extend"); ?></div>
					<div class="ts-vcsc-section-content slideFade" style="display: none;">
						<div style="margin-top: 10px; margin-bottom: 20px;">
							<h4><?php echo __("Load Plugin Files At All Times", "ts_visual_composer_extend"); ?></h4>
							<div class="ts-vcsc-notice-field ts-vcsc-warning" style="margin-top: 10px; font-size: 13px; text-align: justify;">
								<?php echo __("By default, the plugin will load its underlying JS/CSS files only if a matching shortcode has been detected, based on shortcode setting and provided the page or post is loaded normally. But whenever your theme is loading your page or post content via AJAX call, those files are neither loaded, or if already present from the initial direct page load, not executed again, due to an inherit limitation in AJAX routines. This is preventing the tables and their advanced features to actually work correctly. By using the option below, you can tell the plugin to always load all of its JS/CSS files and to listen to a global AJAX event being triggered by your theme, attempting to initialize the tables that way. Please be aware that based on how your theme is actually handling its AJAX load requests, the tables might still not work, even with the option below enabled. Enabling this option will naturally also increase the load time for the initial page which your site is first loaded with.", "ts_visual_composer_extend"); ?>
							</div>
							<p style="font-size: 12px; margin: 10px auto;"><?php echo __("Define if the plugin should load its required JS/CSS files on all pages and posts:", "ts_visual_composer_extend"); ?></p>
							<div class="ts-switch-button ts-codestar-field-switcher" data-value="<?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_LoadFilesAlways == true ? '1' : '0'); ?>">
								<div class="ts-codestar-fieldset">
									<label class="ts-codestar-label">
										<input id="ts_vcsc_extend_settings_defaultLoadFilesAlways" data-order="1" value="<?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_LoadFilesAlways == true ? '1' : '0'); ?>" class="ts-codestar-checkbox ts_vcsc_extend_settings_defaultLoadFilesAlways" name="ts_vcsc_extend_settings_defaultLoadFilesAlways" type="checkbox" <?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_LoadFilesAlways == true ? 'checked="checked"' : ''); ?>> 
										<em data-on="<?php echo __("Yes", "ts_visual_composer_extend"); ?>" data-off="<?php echo __("No", "ts_visual_composer_extend"); ?>"></em>
										<span></span>
									</label>
								</div>
							</div>
							<label class="labelToggleBox" for="ts_vcsc_extend_settings_defaultLoadFilesAlways"><?php echo __("Load Plugin Files At All Times", "ts_visual_composer_extend"); ?></label>
						</div>
					</div>
				</div>
			</div>
			<div id="ts-advancedtables-format" class="tab-content">
				<div id="ts-vcsc-advancedtables-check-pages" class="ts-vcsc-section-main">
					<div class="ts-vcsc-section-title ts-vcsc-section-show"><i class="dashicons-editor-customchar"></i><?php echo __("Format Settings", "ts_visual_composer_extend"); ?></div>
					<div class="ts-vcsc-section-content">
                        <h2><?php echo __("Table Editor Locale", "ts_visual_composer_extend"); ?></h2>
                        <div class="ts-vcsc-notice-field ts-vcsc-success" style="margin-top: 10px; font-size: 13px; text-align: justify;">
							<?php echo __("The locale setting for the table editor determines the format of numbers and currencies that can be assigned to cells, based on the standard norms applied in the selected country. You can still assign a different locale and/or currency symbol to each cell individually, but the selection below will represent the overall default locale for the table editor.", "ts_visual_composer_extend"); ?>
                        </div>  
                        <div style="margin-top: 10px;">
                            <h4><?php echo __("Editor Default Locale:", "ts_visual_composer_extend"); ?></h4>                                                
                            <p style="font-size: 12px;"><?php echo __("Select the default locale for the various cell formats available in the table editor:", "ts_visual_composer_extend"); ?></p>
                            <input name="ts_vcsc_extend_settings_defaultEditorLocaleValue" id="ts_vcsc_extend_settings_defaultEditorLocaleValue" class="" style="display: none;" type="hidden" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_EditorLocale; ?>">
                            <select id="ts_vcsc_extend_settings_defaultEditorLocaleSelect" name="ts_vcsc_extend_settings_defaultEditorLocaleSelect" class="ts-single-options-selector" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_EditorLocale; ?>" data-callback="" data-holder="ts_vcsc_extend_settings_defaultEditorLocaleValue">
                                <?php
                                    foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_NumbroJS_Locales as $key => $value) {
                                        echo '<option value="' . $key . '" ' . selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_EditorLocale, $key, false) . '>' . __($value, "ts_visual_composer_extend") . '</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <h2><?php echo __("Cell Content Alignment Settings", "ts_visual_composer_extend"); ?></h2>
                        <div style="margin-top: 10px;">
                            <h4><?php echo __("Horizontal Cell Content Alignment:", "ts_visual_composer_extend"); ?></h4>
                            <p style="font-size: 12px;"><?php echo __("Select the global default horizontal cell content alignment:", "ts_visual_composer_extend"); ?></p>
                            <input name="ts_vcsc_extend_settings_defaultAlignHorizontalValue" id="ts_vcsc_extend_settings_defaultAlignHorizontalValue" class="" style="display: none;" type="hidden" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_AlignHorizontal; ?>">
                            <select id="ts_vcsc_extend_settings_defaultAlignHorizontalSelect" name="ts_vcsc_extend_settings_defaultAlignHorizontalSelect" class="ts-single-options-selector" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_AlignHorizontal; ?>" data-callback="" data-holder="ts_vcsc_extend_settings_defaultAlignHorizontalValue">
                                <option value="htLeft" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_AlignHorizontal, 'htLeft'); ?>><?php echo __("Left", "ts_visual_composer_extend"); ?></option>
                                <option value="htCenter" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_AlignHorizontal, 'htCenter'); ?>><?php echo __("Center", "ts_visual_composer_extend"); ?></option>
                                <option value="htRight" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_AlignHorizontal, 'htRight'); ?>><?php echo __("Right", "ts_visual_composer_extend"); ?></option>
                                <option value="htJustify" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_AlignHorizontal, 'htJustify'); ?>><?php echo __("Justify", "ts_visual_composer_extend"); ?></option>
                            </select>
                        </div>
                        <div style="margin-top: 10px;">
                            <h4><?php echo __("Vertical Cell Content Alignment:", "ts_visual_composer_extend"); ?></h4>
                            <p style="font-size: 12px;"><?php echo __("Select the global default vertical cell content alignment:", "ts_visual_composer_extend"); ?></p>
                            <input name="ts_vcsc_extend_settings_defaultAlignVerticalValue" id="ts_vcsc_extend_settings_defaultAlignVerticalValue" class="" style="display: none;" type="hidden" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_AlignVertical; ?>">
                            <select id="ts_vcsc_extend_settings_defaultAlignVerticalSelect" name="ts_vcsc_extend_settings_defaultAlignVerticalSelect" class="ts-single-options-selector" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_AlignVertical; ?>" data-callback="" data-holder="ts_vcsc_extend_settings_defaultAlignVerticalValue">
                                <option value="htTop" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_AlignVertical, 'htTop'); ?>><?php echo __("Top", "ts_visual_composer_extend"); ?></option>
                                <option value="htMiddle" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_AlignVertical, 'htMiddle'); ?>><?php echo __("Middle", "ts_visual_composer_extend"); ?></option>
                                <option value="htBottom" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_AlignVertical, 'htBottom'); ?>><?php echo __("Bottom", "ts_visual_composer_extend"); ?></option>
                            </select>
                        </div>
                        <h2><?php echo __("Date + Time Settings", "ts_visual_composer_extend"); ?></h2>
                        <div style="margin-top: 10px;">
                            <h4><?php echo __("Date Format:", "ts_visual_composer_extend"); ?></h4>
                            <p style="font-size: 12px;"><?php echo __("Select the global default date format:", "ts_visual_composer_extend"); ?></p>
                            <input name="ts_vcsc_extend_settings_defaultFormatDateValue" id="ts_vcsc_extend_settings_defaultFormatDateValue" class="" style="display: none;" type="hidden" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatDateFull; ?>">
                            <select id="ts_vcsc_extend_settings_defaultFormatDateSelect" name="ts_vcsc_extend_settings_defaultFormatDateSelect" class="ts-single-options-selector" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatDateFull; ?>" data-callback="" data-holder="ts_vcsc_extend_settings_defaultFormatDateValue">
                                <option value="MM-DD-YYYY" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatDateFull, 'MM-DD-YYYY'); ?>>MM-DD-YYYY</option>
                                <option value="DD-MM-YYYY" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatDateFull, 'DD-MM-YYYY'); ?>>DD-MM-YYYY</option>
                                <option value="YYYY-MM-DD" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatDateFull, 'YYYY-MM-DD'); ?>>YYYY-MM-DD</option>
                                <option value="MM.DD.YYYY" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatDateFull, 'MM.DD.YYYY'); ?>>MM.DD.YYYY</option>
                                <option value="DD.MM.YYYY" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatDateFull, 'DD.MM.YYYY'); ?>>DD.MM.YYYY</option>
                                <option value="YYYY.MM.DD" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatDateFull, 'YYYY.MM.DD'); ?>>YYYY.MM.DD</option>
                            </select>
                        </div>
                        <div style="margin-top: 20px;">
                            <h4><?php echo __("Hours Format:", "ts_visual_composer_extend"); ?></h4>
                            <p style="font-size: 12px;"><?php echo __("Select the global default hours format:", "ts_visual_composer_extend"); ?></p>
                            <input name="ts_vcsc_extend_settings_defaultFormatTimeHourValue" id="ts_vcsc_extend_settings_defaultFormatTimeHourValue" class="" style="display: none;" type="hidden" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTimeHours; ?>">
                            <select id="ts_vcsc_extend_settings_defaultFormatTimeHourSelect" name="ts_vcsc_extend_settings_defaultFormatTimeHourSelect" class="ts-single-options-selector" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTimeHours; ?>" data-callback="" data-holder="ts_vcsc_extend_settings_defaultFormatTimeHourValue">
                                <option value="HH" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTimeHours, 'HH'); ?>><?php echo __("24 Hour Time (leading Zeroes)", "ts_visual_composer_extend"); ?></option>
                                <option value="H" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTimeHours, 'H'); ?>><?php echo __("24 Hour Time (no leading Zeroes)", "ts_visual_composer_extend"); ?></option>
                                <option value="hh" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTimeHours, 'hh'); ?>><?php echo __("12 Hour Time (leading Zeroes)", "ts_visual_composer_extend"); ?></option>
                                <option value="h" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTimeHours, 'h'); ?>><?php echo __("12 Hour Time (no leading Zeroes)", "ts_visual_composer_extend"); ?></option>
                            </select>
                        </div>
                        <div style="margin-top: 20px;">
                            <h4><?php echo __("Minutes Format:", "ts_visual_composer_extend"); ?></h4>
                            <p style="font-size: 12px;"><?php echo __("Select the global default minutes format:", "ts_visual_composer_extend"); ?></p>
                            <input name="ts_vcsc_extend_settings_defaultFormatTimeMinuteValue" id="ts_vcsc_extend_settings_defaultFormatTimeMinuteValue" class="" style="display: none;" type="hidden" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTimeMinutes; ?>">
                            <select id="ts_vcsc_extend_settings_defaultFormatTimeMinuteSelect" name="ts_vcsc_extend_settings_defaultFormatTimeMinuteSelect" class="ts-single-options-selector" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTimeMinutes; ?>" data-callback="" data-holder="ts_vcsc_extend_settings_defaultFormatTimeMinuteValue">
                                <option value="mm" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTimeMinutes, 'mm'); ?>><?php echo __("Leading Zeroes", "ts_visual_composer_extend"); ?></option>
                                <option value="m" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTimeMinutes, 'm'); ?>><?php echo __("No leading Zeroes", "ts_visual_composer_extend"); ?></option>
                            </select>
                        </div>
                        <div style="margin-top: 20px;">
                            <h4><?php echo __("Seconds Format:", "ts_visual_composer_extend"); ?></h4>
                            <p style="font-size: 12px;"><?php echo __("Select the global default seconds format:", "ts_visual_composer_extend"); ?></p>
                            <input name="ts_vcsc_extend_settings_defaultFormatTimeSecondValue" id="ts_vcsc_extend_settings_defaultFormatTimeSecondValue" class="" style="display: none;" type="hidden" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTimeSeconds; ?>">
                            <select id="ts_vcsc_extend_settings_defaultFormatTimeSecondSelect" name="ts_vcsc_extend_settings_defaultFormatTimeSecondSelect" class="ts-single-options-selector" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTimeSeconds; ?>" data-callback="" data-holder="ts_vcsc_extend_settings_defaultFormatTimeSecondValue">
                                <option value="" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTimeSeconds, ''); ?>><?php echo __("No Seconds", "ts_visual_composer_extend"); ?></option>
                                <option value="ss" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTimeSeconds, 'ss'); ?>><?php echo __("Leading Zeroes", "ts_visual_composer_extend"); ?></option>
                                <option value="s" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTimeSeconds, 's'); ?>><?php echo __("No leading Zeroes", "ts_visual_composer_extend"); ?></option>
                            </select>
                        </div>
                        <h2><?php echo __("Currency Settings", "ts_visual_composer_extend"); ?></h2>
                        <div class="ts-vcsc-notice-field ts-vcsc-success" style="margin-top: 10px; font-size: 13px; text-align: justify;">
							<?php echo __("The actual currency format and currency symbol used will depend upon the locale setting that is assigned to the table cell.", "ts_visual_composer_extend"); ?>
                        </div>   
                        <div style="margin-top: 10px;">
                            <h4><?php echo __("Currency Symbol Placement:", "ts_visual_composer_extend"); ?></h4>
                            <p style="font-size: 12px;"><?php echo __("Select the global default placement for the currency symbol:", "ts_visual_composer_extend"); ?></p>
                            <input name="ts_vcsc_extend_settings_defaultPlacementCurrencyValue" id="ts_vcsc_extend_settings_defaultPlacementCurrencyValue" class="" style="display: none;" type="hidden" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_CurrencyPlacement; ?>">
                            <select id="ts_vcsc_extend_settings_defaultPlacementCurrencySelect" name="ts_vcsc_extend_settings_defaultPlacementCurrencySelect" class="ts-single-options-selector" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_CurrencyPlacement; ?>" data-callback="" data-holder="ts_vcsc_extend_settings_defaultPlacementCurrencyValue">
                                <option value="prefix" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_CurrencyPlacement, 'prefix'); ?>><?php echo __("Before ($0.00)", "ts_visual_composer_extend"); ?></option>
                                <option value="postfix" <?php selected($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_CurrencyPlacement, 'postfix'); ?>><?php echo __("After (0.00$)", "ts_visual_composer_extend"); ?></option>
                            </select>
                        </div>
						<div style="margin-top: 20px;">
							<h4><?php echo __("Space Between Currency Symbol And Value", "ts_visual_composer_extend"); ?></h4>
							<p style="font-size: 12px; margin: 10px auto;"><?php echo __("Define if there should be a space between the currency symbol and the numeric value:", "ts_visual_composer_extend"); ?></p>					
							<div class="ts-switch-button ts-codestar-field-switcher" data-value="<?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_CurrencySpace == true ? '1' : '0'); ?>">
								<div class="ts-codestar-fieldset">
									<label class="ts-codestar-label">
										<input id="ts_vcsc_extend_settings_defaultCurrencySpacer" data-order="2" value="<?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_CurrencySpace == true ? '1' : '0'); ?>" class="ts-codestar-checkbox ts_vcsc_extend_settings_defaultCurrencySpacer" name="ts_vcsc_extend_settings_defaultCurrencySpacer" type="checkbox" <?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_CurrencySpace == true ? 'checked="checked"' : ''); ?>> 
										<em data-on="<?php echo __("Yes", "ts_visual_composer_extend"); ?>" data-off="<?php echo __("No", "ts_visual_composer_extend"); ?>"></em>
										<span></span>
									</label>
								</div>
							</div>
							<label class="labelToggleBox" for="ts_vcsc_extend_settings_defaultCurrencySpacer"><?php echo __("Add Space between Currency Symbol and Value", "ts_visual_composer_extend"); ?></label>
						</div>
                        <div style="margin-top: 20px; margin-bottom: 20px;">
                            <h4><?php echo __("Number of Decimals (Currency):", "ts_visual_composer_extend"); ?></h4>
                            <p style="font-size: 12px;"><?php echo __("Define the number of decimals to be used for currency values:", "ts_visual_composer_extend"); ?></p>
                            <?php
                                $settings = array(
                                    "param_name"		=> "ts_vcsc_extend_settings_defaultDecimalsCurrency",
                                    "min"				=> "0",
                                    "max"				=> "6",
                                    "step"				=> "1",
                                    "range"				=> "false",
                                    "value"				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DecimalsCurrency,
									"default"			=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['formats']['decimalsCurrency'],
									"group"				=> "decimals",
                                );
                                echo TS_TablesWP_NoUiSlider_Settings_Field($settings, $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DecimalsCurrency, '');
                            ?>
                        </div>
                        <h2><?php echo __("Percent Settings", "ts_visual_composer_extend"); ?></h2>
						<div style="margin-top: 10px;">
							<h4><?php echo __("Space Between Percent Symbol And Value", "ts_visual_composer_extend"); ?></h4>
							<p style="font-size: 12px; margin: 10px auto;"><?php echo __("Define if there should be a space between the percentage symbol and the numeric value:", "ts_visual_composer_extend"); ?></p>				
							<div class="ts-switch-button ts-codestar-field-switcher" data-value="<?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_PercentSpace == true ? '1' : '0'); ?>">
								<div class="ts-codestar-fieldset">
									<label class="ts-codestar-label">
										<input id="ts_vcsc_extend_settings_defaultPercentSpacer" data-order="2" value="<?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_PercentSpace == true ? '1' : '0'); ?>" class="ts-codestar-checkbox ts_vcsc_extend_settings_defaultPercentSpacer" name="ts_vcsc_extend_settings_defaultPercentSpacer" type="checkbox" <?php echo ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_PercentSpace == true ? 'checked="checked"' : ''); ?>> 
										<em data-on="<?php echo __("Yes", "ts_visual_composer_extend"); ?>" data-off="<?php echo __("No", "ts_visual_composer_extend"); ?>"></em>
										<span></span>
									</label>
								</div>
							</div>
							<label class="labelToggleBox" for="ts_vcsc_extend_settings_defaultPercentSpacer"><?php echo __("Add Space between Percent Symbol and Value", "ts_visual_composer_extend"); ?></label>
						</div>
                        <div style="margin-top: 20px; margin-bottom: 20px;">
                            <h4><?php echo __("Number of Decimals (Percent):", "ts_visual_composer_extend"); ?></h4>
                            <p style="font-size: 12px;"><?php echo __("Define the number of decimals to be used for percent values:", "ts_visual_composer_extend"); ?></p>
                            <?php
                                $settings = array(
                                    "param_name"		=> "ts_vcsc_extend_settings_defaultDecimalsPercent",
                                    "min"				=> "0",
                                    "max"				=> "6",
                                    "step"				=> "1",
                                    "range"				=> "false",
                                    "value"				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DecimalsPercent,
									"default"			=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['formats']['decimalsPercent'],
									"group"				=> "decimals",
                                );
                                echo TS_TablesWP_NoUiSlider_Settings_Field($settings, $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DecimalsPercent, '');
                            ?>
                        </div>
						<h2><?php echo __("Numeric Settings", "ts_visual_composer_extend"); ?></h2>
                        <div style="margin-top: 10px; margin-bottom: 20px;">
                            <h4><?php echo __("Number of Decimals (Numeric):", "ts_visual_composer_extend"); ?></h4>
                            <p style="font-size: 12px;"><?php echo __("Define the number of decimals to be used for numeric values:", "ts_visual_composer_extend"); ?></p>
                            <?php
                                $settings = array(
                                    "param_name"		=> "ts_vcsc_extend_settings_defaultDecimalsNumeric",
                                    "min"				=> "0",
                                    "max"				=> "6",
                                    "step"				=> "1",
                                    "range"				=> "false",
                                    "value"				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DecimalsNumeric,
									"default"			=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['formats']['decimalsNumeric'],
									"group"				=> "decimals",
                                );
                                echo TS_TablesWP_NoUiSlider_Settings_Field($settings, $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DecimalsNumeric, '');
                            ?>
                        </div>
					</div>
				</div>
			</div>
			<div id="ts-advancedtables-translations" class="tab-content">
				<div class="ts-vcsc-section-main">
					<div class="ts-vcsc-section-title ts-vcsc-section-show"><i class="dashicons-translation"></i><?php echo __("Quick Actions", "ts_visual_composer_extend"); ?></div>
					<div class="ts-vcsc-section-content">
                        <div class="ts-advanced-table-controls-wrapper" style="float: none; display: block; width: 100%; min-height: 40px; margin: 0; padding: 10px 0;">
                            <div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder" style="margin: 0 20px 0 0;">
                                <span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to restore all default text strings.", "ts_visual_composer_extend"); ?></span>
                                <div id="ts-advanced-table-restore-button" class="ts-advanced-tables-button-main ts-advanced-tables-button-silver ts-advanced-tables-button-default" style="margin: 0;">
									<?php echo __("Restore Defaults", "ts_visual_composer_extend"); ?>
								</div>
                            </div>
                            <div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder" style="display: <?php echo ((($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_LoadLanguage == true) && (substr(get_bloginfo('language'), 0, 2) != "en")) ? "inline-block" : "none"); ?>; margin: 0;">
                                <span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to apply all recommended text strings based on your WordPress language setting.", "ts_visual_composer_extend"); ?></span>
                                <div id="ts-advanced-table-recommend-button" class="ts-advanced-tables-button-main ts-advanced-tables-button-silver ts-advanced-tables-button-language" style="margin: 0;">
									<?php echo __("Apply Locale Strings", "ts_visual_composer_extend"); ?>
								</div>
                            </div>		
                        </div>
                        <div class="ts-vcsc-notice-field ts-vcsc-success" style="display: block; margin-top: 10px; font-size: 13px; text-align: justify;">
							<?php echo __("The following text strings relate to all table layouts that use any of the advanced features (TableSaw / FooTable / DataTable). Text strings defined here will serve as default values for all tables using those features, but can be changed for each table individually, using the provided options in the table settings panel in WP Bakery Page Builder (formerly Visual Composer) or the shortcode generator.", "ts_visual_composer_extend"); ?>
                        </div>
					</div>
				</div>
				<div class="ts-vcsc-section-main">
					<div class="ts-vcsc-section-title ts-vcsc-section-show"><i class="dashicons-editor-table"></i><?php echo __("TableSaw Text Strings", "ts_visual_composer_extend"); ?></div>
					<div class="ts-vcsc-section-content">
						<div class="ts-advanced-table-controls-wrapper" style="float: none; display: block; width: 100%; min-height: 40px; margin: 0; padding: 10px 0;">
							<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
								<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to go to the official external page for the TableSaw script.", "ts_visual_composer_extend"); ?></span>
								<a href="https://github.com/filamentgroup/tablesaw" target="_blank" class="ts-advanced-tables-button-main ts-advanced-tables-button-silver ts-advanced-tables-button-link" style="margin: 0;">
									<?php echo __("TableSaw Script", "ts_visual_composer_extend"); ?>
								</a>
							</div>
						</div>
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['tablesawStack'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawStack']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawStack'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageTableSawStack">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("TableSaw Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageTableSawStack" name="ts_vcsc_extend_settings_languageTableSawStack" value="<?php echo $string_custom; ?>" size="100">
						</div>
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['tablesawSwipe'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawSwipe']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawSwipe'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageTableSawSwipe">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("TableSaw Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageTableSawSwipe" name="ts_vcsc_extend_settings_languageTableSawSwipe" value="<?php echo $string_custom; ?>" size="100">
						</div>
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['tablesawToggle'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawToggle']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawToggle'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageTableSawToggle">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("TableSaw Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageTableSawToggle" name="ts_vcsc_extend_settings_languageTableSawToggle" value="<?php echo $string_custom; ?>" size="100">
						</div>
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['tablesawResponsive'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawResponsive']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawResponsive'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageTableSawResponsiveness">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("TableSaw Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageTableSawResponsiveness" name="ts_vcsc_extend_settings_languageTableSawResponsiveness" value="<?php echo $string_custom; ?>" size="100">
						</div>
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['tablesawColumns'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawColumns']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawColumns'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageTableSawColumns">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("TableSaw Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageTableSawColumns" name="ts_vcsc_extend_settings_languageTableSawColumns" value="<?php echo $string_custom; ?>" size="100">
						</div>
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['tablesawError'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawError']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawError'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageTableSawError">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("Translation Settings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageTableSawError" name="ts_vcsc_extend_settings_languageTableSawError" value="<?php echo $string_custom; ?>" size="100">
						</div>
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['tablesawSort'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawSort']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawSort'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageTableSawSort">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("Translation Settings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageTableSawSort" name="ts_vcsc_extend_settings_languageTableSawSort" value="<?php echo $string_custom; ?>" size="100">
						</div>
					</div>
				</div>
				<div class="ts-vcsc-section-main">
					<div class="ts-vcsc-section-title ts-vcsc-section-show"><i class="dashicons-editor-table"></i><?php echo __("FooTable Text Strings", "ts_visual_composer_extend"); ?></div>
					<div class="ts-vcsc-section-content">
						<div class="ts-advanced-table-controls-wrapper" style="float: none; display: block; width: 100%; min-height: 40px; margin: 0; padding: 10px 0;">
							<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
								<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to go to the official external page for the FooTable script.", "ts_visual_composer_extend"); ?></span>
								<a href="http://fooplugins.github.io/FooTable/" target="_blank" class="ts-advanced-tables-button-main ts-advanced-tables-button-silver ts-advanced-tables-button-link" style="margin: 0;">
									<?php echo __("FooTable Script", "ts_visual_composer_extend"); ?>
								</a>
							</div>
						</div>				
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['footableLengthAll'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['footableLengthAll']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['footableLengthAll'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageFootableLengthAll">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("Footable Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageFootableLengthAll" name="ts_vcsc_extend_settings_languageFootableLengthAll" value="<?php echo $string_custom; ?>" size="100">
						</div>
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['footableLengthMenu'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_FooTable['footableLengthMenu']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_FooTable['footableLengthMenu'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageFootableLengthMenu">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("Footable Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageFootableLengthMenu" name="ts_vcsc_extend_settings_languageFootableLengthMenu" value="<?php echo $string_custom; ?>" size="100">
							<i class="ts-advancedtables-helptoggle dashicons dashicons-editor-help ts-advanced-table-tooltip-holder" data-status="false"><span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to view or hide additional information.", "ts_visual_composer_extend"); ?></span></i>
							<div class="ts-advancedtables-helpsection ts-vcsc-notice-field ts-vcsc-info">
								<?php echo __("The {LM} placeholder in this language string will be replaced with the possible page length options of (5, 10, 25, ... etc.) and is required for the page length select option to be rendered correctly.", "ts_visual_composer_extend"); ?>	
							</div>
						</div>						
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['footableCountFormat'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_FooTable['footableCountFormat']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_FooTable['footableCountFormat'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageFootableCountFormat">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("Footable Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageFootableCountFormat" name="ts_vcsc_extend_settings_languageFootableCountFormat" value="<?php echo $string_custom; ?>" size="100">
							<i class="ts-advancedtables-helptoggle dashicons dashicons-editor-help ts-advanced-table-tooltip-holder" data-status="false"><span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to view or hide additional information.", "ts_visual_composer_extend"); ?></span></i>
							<div class="ts-advancedtables-helpsection ts-vcsc-notice-field ts-vcsc-info">
								<?php echo __("The default count format uses the current page and total pages placeholders which are substituted at run time for the actual values. The below lists all available placeholders that you can use when supplying your own format.<br/><br/>{CP} - The current page.<br/>{TP} - The total number of pages available.<br/>{PF} - The first row number of the current page.<br/>{PL} - The last row number of the current page.<br/>{TR} - The total number of rows available.", "ts_visual_composer_extend"); ?>								
							</div>
						</div>
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['footablePlaceholder'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_FooTable['footablePlaceholder']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_FooTable['footablePlaceholder'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageFootablePlaceholder">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("Footable Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageFootablePlaceholder" name="ts_vcsc_extend_settings_languageFootablePlaceholder" value="<?php echo $string_custom; ?>" size="100">
						</div>
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['footableNoResults'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_FooTable['footableNoResults']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_FooTable['footableNoResults'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageFootableNoResults">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("Footable Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageFootableNoResults" name="ts_vcsc_extend_settings_languageFootableNoResults" value="<?php echo $string_custom; ?>" size="100">
						</div>
					</div>
				</div>
				<div class="ts-vcsc-section-main">
					<div class="ts-vcsc-section-title ts-vcsc-section-show"><i class="dashicons-editor-table"></i><?php echo __("DataTable Text Strings", "ts_visual_composer_extend"); ?></div>
					<div class="ts-vcsc-section-content">
						<div class="ts-advanced-table-controls-wrapper" style="float: none; display: block; width: 100%; min-height: 40px; margin: 0; padding: 10px 0;">
							<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
								<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to go to the official external page for the DataTable script.", "ts_visual_composer_extend"); ?></span>
								<a href="https://datatables.net/" target="_blank" class="ts-advanced-tables-button-main ts-advanced-tables-button-silver ts-advanced-tables-button-link" style="margin: 0;">
									<?php echo __("DataTable Script", "ts_visual_composer_extend"); ?>
								</a>
							</div>
						</div>
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableProcessing'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableProcessing']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableProcessing'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageDatatableProcessing">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("DataTable Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageDatatableProcessing" name="ts_vcsc_extend_settings_languageDatatableProcessing" value="<?php echo $string_custom; ?>" size="100">
						</div>
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableLengthAll'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableLengthAll']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableLengthAll'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageDatatableLengthAll">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("DataTable Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageDatatableLengthAll" name="ts_vcsc_extend_settings_languageDatatableLengthAll" value="<?php echo $string_custom; ?>" size="100">
						</div>
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableLengthMenu'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableLengthMenu']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableLengthMenu'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageDatatableLengthMenu">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("DataTable Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageDatatableLengthMenu" name="ts_vcsc_extend_settings_languageDatatableLengthMenu" value="<?php echo $string_custom; ?>" size="100">
							<i class="ts-advancedtables-helptoggle dashicons dashicons-editor-help ts-advanced-table-tooltip-holder" data-status="false"><span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to view or hide additional information.", "ts_visual_composer_extend"); ?></span></i>
							<div class="ts-advancedtables-helpsection ts-vcsc-notice-field ts-vcsc-info">
								<?php echo __("The _MENU_ placeholder in this language string will be replaced with the possible page length options of (5, 10, 25, ... etc.) and is required for the page length select option to be rendered correctly.", "ts_visual_composer_extend"); ?>	
							</div>
						</div>
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableInfoMain'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableInfoMain']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableInfoMain'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageDatatableInfoMain">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("DataTable Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageDatatableInfoMain" name="ts_vcsc_extend_settings_languageDatatableInfoMain" value="<?php echo $string_custom; ?>" size="100">
							<i class="ts-advancedtables-helptoggle dashicons dashicons-editor-help ts-advanced-table-tooltip-holder" data-status="false"><span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to view or hide additional information.", "ts_visual_composer_extend"); ?></span></i>
							<div class="ts-advancedtables-helpsection ts-vcsc-notice-field ts-vcsc-info">
								<?php echo __("This string gives information to the end user about the information that is currently on display on the page. The following tokens can be used in the string and will be dynamically replaced as the table display updates.<br/><br/>_START_ - Display index of the first record on the current page.<br/>_END_ - Display index of the last record on the current page.<br/>_TOTAL_ - Number of records in the table after filtering.<br/>_MAX_ - Number of records in the table without filtering.<br/>_PAGE_ - Current page number.<br/>_PAGES_ - Total number of pages of data in the table.", "ts_visual_composer_extend"); ?>								
							</div>
						</div>
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableInfoEmpty'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableInfoEmpty']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableInfoEmpty'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageDatatableInfoEmpty">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("DataTable Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageDatatableInfoEmpty" name="ts_vcsc_extend_settings_languageDatatableInfoEmpty" value="<?php echo $string_custom; ?>" size="100">
						</div>
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableInfoFiltered'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableInfoFiltered']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableInfoFiltered'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageDatatableInfoFiltered">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("DataTable Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageDatatableInfoFiltered" name="ts_vcsc_extend_settings_languageDatatableInfoFiltered" value="<?php echo $string_custom; ?>" size="100">
							<i class="ts-advancedtables-helptoggle dashicons dashicons-editor-help ts-advanced-table-tooltip-holder" data-status="false"><span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to view or hide additional information.", "ts_visual_composer_extend"); ?></span></i>
							<div class="ts-advancedtables-helpsection ts-vcsc-notice-field ts-vcsc-info">
								<?php echo __("When a user filters the information in a table via search, this string is appended to the information summary to give an idea of how strong the filtering is. The token _MAX_ will be replaced with the number of total records in the table.", "ts_visual_composer_extend"); ?>								
							</div>
						</div>
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableSearch'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableSearch']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableSearch'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageDatatableSearch">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("DataTable Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageDatatableSearch" name="ts_vcsc_extend_settings_languageDatatableSearch" value="<?php echo $string_custom; ?>" size="100">
						</div>
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatablePlaceholder'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatablePlaceholder']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatablePlaceholder'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageDatatablePlaceholder">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("DataTable Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageDatatablePlaceholder" name="ts_vcsc_extend_settings_languageDatatablePlaceholder" value="<?php echo $string_custom; ?>" size="100">
						</div>
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableZeroRecords'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableZeroRecords']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableZeroRecords'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageDatatableZeroRecords">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("DataTable Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageDatatableZeroRecords" name="ts_vcsc_extend_settings_languageDatatableZeroRecords" value="<?php echo $string_custom; ?>" size="100">
						</div>						
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableFirst'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableFirst']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableFirst'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageDatatableFirst">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("DataTable Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageDatatableFirst" name="ts_vcsc_extend_settings_languageDatatableFirst" value="<?php echo $string_custom; ?>" size="100">
						</div>
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatablePrevious'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatablePrevious']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatablePrevious'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageDatatablePrevious">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("DataTable Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageDatatablePrevious" name="ts_vcsc_extend_settings_languageDatatablePrevious" value="<?php echo $string_custom; ?>" size="100">
						</div>
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableNext'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableNext']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableNext'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageDatatableNext">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("DataTable Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageDatatableNext" name="ts_vcsc_extend_settings_languageDatatableNext" value="<?php echo $string_custom; ?>" size="100">
						</div>
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableLast'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableLast']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableLast'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageDatatableLast">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("DataTable Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageDatatableLast" name="ts_vcsc_extend_settings_languageDatatableLast" value="<?php echo $string_custom; ?>" size="100">
						</div>
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatablePrint'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatablePrint']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatablePrint'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageDatatablePrint">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("DataTable Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageDatatablePrint" name="ts_vcsc_extend_settings_languageDatatablePrint" value="<?php echo $string_custom; ?>" size="100">
						</div>
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatablePDF'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatablePDF']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatablePDF'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageDatatablePDF">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("DataTable Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageDatatablePDF" name="ts_vcsc_extend_settings_languageDatatablePDF" value="<?php echo $string_custom; ?>" size="100">
						</div>
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableExcel'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableExcel']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableExcel'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageDatatableExcel">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("DataTable Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageDatatableExcel" name="ts_vcsc_extend_settings_languageDatatableExcel" value="<?php echo $string_custom; ?>" size="100">
						</div>
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableCSV'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableCSV']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableCSV'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageDatatableCSV">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("DataTable Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageDatatableCSV" name="ts_vcsc_extend_settings_languageDatatableCSV" value="<?php echo $string_custom; ?>" size="100">
						</div>
						<div class="ts-advancedtables-translation-option">
							<?php
								$string_default 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableCopy'];
								$string_custom 		= (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableCopy']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableCopy'] : $string_default);
							?>
							<label class="Uniform" style="display: inline-block;" for="ts_vcsc_extend_settings_languageDatatableCopy">"<?php echo $string_default; ?>":</label>
							<input class="validate[required]" data-errormessage="<?php echo __("* This field is required.", "ts_visual_composer_extend"); ?>" data-error="<?php echo __("DataTable Text Strings", "ts_visual_composer_extend"); ?> - <?php echo $string_default; ?>" data-locale="<?php echo __($string_default, "ts_visual_composer_extend"); ?>" data-default="<?php echo $string_default; ?>" data-order="3" type="text" style="width: 30%;" id="ts_vcsc_extend_settings_languageDatatableCopy" name="ts_vcsc_extend_settings_languageDatatableCopy" value="<?php echo $string_custom; ?>" size="100">
						</div>
					</div>
				</div>
			</div>
			<div id="ts-advancedtables-breakpoints" class="tab-content">
				<div class="ts-vcsc-section-main">
					<div class="ts-vcsc-section-title ts-vcsc-section-show"><i class="dashicons-translation"></i><?php echo __("Quick Actions", "ts_visual_composer_extend"); ?></div>
					<div class="ts-vcsc-section-content">
                        <div class="ts-advanced-table-controls-wrapper" style="float: none; display: block; width: 100%; min-height: 40px; margin: 0; padding: 10px 0;">
                            <div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder" style="margin: 0 20px 0 0;">
                                <span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to restore all default breakpoints.", "ts_visual_composer_extend"); ?></span>
                                <div id="ts-advanced-table-breakpoints-button" class="ts-advanced-tables-button-main ts-advanced-tables-button-silver ts-advanced-tables-button-default" style="margin: 0;">
									<?php echo __("Restore Defaults", "ts_visual_composer_extend"); ?>
								</div>
                            </div>
                        </div>
                        <div class="ts-vcsc-notice-field ts-vcsc-success" style="display: block; margin-top: 10px; font-size: 13px; text-align: justify;">
							<?php echo __("In order to adjust the table to different screen sizes (available table widths) by collapsing pre-defined columns, the FooTable and DataTable scripts use internal breakpoints, which represent table widths at which assigned columns are to be collapsed. You can adjust those breakpoints by using the controls provided below.", "ts_visual_composer_extend"); ?>
                        </div>
					</div>
				</div>
				<div class="ts-vcsc-section-main">
					<div class="ts-vcsc-section-title ts-vcsc-section-show"><i class="dashicons-editor-table"></i><?php echo __("FooTable Breakpoints", "ts_visual_composer_extend"); ?></div>
					<div class="ts-vcsc-section-content">
                        <div class="ts-vcsc-notice-field ts-vcsc-warning" style="display: block; margin-top: 10px; font-size: 13px; text-align: justify;">
							<?php echo __("The breakpoint value given represents the minimum table width at which this breakpoint will be applied, with the exception of the breakpoint for extra small devices, which represents the maximum screen size in order to be considered an extra small device. For example, if using the default breakpoint values, the breakpoint for medium sized devices will be applied for 992 < x <= 1200 (where x is the table width).", "ts_visual_composer_extend"); ?>
                        </div>
                        <div style="margin-top: 20px; margin-bottom: 20px;">
                            <h4><?php echo __("Large Devices:", "ts_visual_composer_extend"); ?></h4>
                            <p style="font-size: 12px;"><?php echo __("Define the minimum table width at which a large sized device should be assumed:", "ts_visual_composer_extend"); ?></p>
                            <?php
                                $settings = array(
                                    "param_name"		=> "ts_vcsc_extend_settings_defaultFootableLarge",
                                    "min"				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FootableMedium + 1,
                                    "max"				=> "1440",
                                    "step"				=> "1",
									"pips"				=> "false",
                                    "range"				=> "false",
									"unit"				=> "px",
                                    "value"				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FootableLarge,
									"default"			=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['breakpoints']['footableLarge'],
									"group"				=> "footable",
                                );
                                echo TS_TablesWP_NoUiSlider_Settings_Field($settings, $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FootableLarge, 'ts_vcsc_extend_settings_defaultFootableLarge');
                            ?>
                        </div>
                        <div style="margin-top: 20px; margin-bottom: 20px;">
                            <h4><?php echo __("Medium Devices:", "ts_visual_composer_extend"); ?></h4>
                            <p style="font-size: 12px;"><?php echo __("Define the minimum table width at which a medium sized device should be assumed:", "ts_visual_composer_extend"); ?></p>
                            <?php
                                $settings = array(
                                    "param_name"		=> "ts_vcsc_extend_settings_defaultFootableMedium",
                                    "min"				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FootableSmall + 1,
                                    "max"				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FootableLarge - 20,
                                    "step"				=> "1",
									"pips"				=> "false",
                                    "range"				=> "false",
									"unit"				=> "px",
                                    "value"				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FootableMedium,
									"default"			=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['breakpoints']['footableMedium'],
									"group"				=> "footable",
                                );
                                echo TS_TablesWP_NoUiSlider_Settings_Field($settings, $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FootableMedium, 'ts_vcsc_extend_settings_defaultFootableMedium');
                            ?>
                        </div>
                        <div style="margin-top: 20px; margin-bottom: 20px;">
                            <h4><?php echo __("Small Devices:", "ts_visual_composer_extend"); ?></h4>
                            <p style="font-size: 12px;"><?php echo __("Define the minimum table width at which a small sized device should be assumed:", "ts_visual_composer_extend"); ?></p>
                            <?php
                                $settings = array(
                                    "param_name"		=> "ts_vcsc_extend_settings_defaultFootableSmall",
                                    "min"				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FootableTiny + 1,
                                    "max"				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FootableMedium - 20,
                                    "step"				=> "1",
									"pips"				=> "false",
                                    "range"				=> "false",
									"unit"				=> "px",
                                    "value"				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FootableSmall,
									"default"			=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['breakpoints']['footableSmall'],
									"group"				=> "footable",
                                );
                                echo TS_TablesWP_NoUiSlider_Settings_Field($settings, $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FootableSmall, 'ts_vcsc_extend_settings_defaultFootableSmall');
                            ?>
                        </div>
                        <div style="margin-top: 20px; margin-bottom: 20px;">
                            <h4><?php echo __("Extra Small Devices:", "ts_visual_composer_extend"); ?></h4>
                            <p style="font-size: 12px;"><?php echo __("Define the maximum table width at which an extra small sized device should be assumed:", "ts_visual_composer_extend"); ?></p>
                            <?php
                                $settings = array(
                                    "param_name"		=> "ts_vcsc_extend_settings_defaultFootableTiny",
                                    "min"				=> "240",
                                    "max"				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FootableSmall - 20,
                                    "step"				=> "1",
									"pips"				=> "false",
                                    "range"				=> "false",
									"unit"				=> "px",
                                    "value"				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FootableTiny,
									"default"			=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['breakpoints']['footableTiny'],
									"group"				=> "footable",
                                );
                                echo TS_TablesWP_NoUiSlider_Settings_Field($settings, $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FootableTiny, 'ts_vcsc_extend_settings_defaultFootableTiny');
                            ?>
                        </div>
					</div>
				</div>
				<div class="ts-vcsc-section-main">
					<div class="ts-vcsc-section-title ts-vcsc-section-show"><i class="dashicons-editor-table"></i><?php echo __("DataTable Breakpoints", "ts_visual_composer_extend"); ?></div>
					<div class="ts-vcsc-section-content">
                        <div class="ts-vcsc-notice-field ts-vcsc-warning" style="display: block; margin-top: 10px; font-size: 13px; text-align: justify;">
							<?php echo __("The breakpoint value given is the maximum screen size at which this breakpoint will be applied, and it will be used until the next breakpoint is found. For example, if using the default breakpoint values, the tablet in landscape breakpoint will be applied for 768 < x <= 1024 (where x is the viewport width). All devices with a size larger than the one specified for tablets in landscape format will automatically be treated as desktop devices. All breakpoints will be applied solely based on the assigned screen width, no matter the actual device type used.", "ts_visual_composer_extend"); ?>
                        </div>
                        <div style="margin-top: 20px; margin-bottom: 20px;">
                            <h4><?php echo __("Tablet Devices - Landscape:", "ts_visual_composer_extend"); ?></h4>
                            <p style="font-size: 12px;"><?php echo __("Define the maximum table width at which a tablet device in landscape format should be assumed:", "ts_visual_composer_extend"); ?></p>
                            <?php
                                $settings = array(
                                    "param_name"		=> "ts_vcsc_extend_settings_defaultDatatableTabletL",
                                    "min"				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DatatableTabletP + 1,
                                    "max"				=> "1280",
                                    "step"				=> "1",
									"pips"				=> "false",
                                    "range"				=> "false",
									"unit"				=> "px",
                                    "value"				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DatatableTabletL,
									"default"			=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['breakpoints']['datatableTabletL'],
									"group"				=> "datatable",
                                );
                                echo TS_TablesWP_NoUiSlider_Settings_Field($settings, $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DatatableTabletL, 'ts_vcsc_extend_settings_defaultDatatableTabletL');
                            ?>
                        </div>
                        <div style="margin-top: 20px; margin-bottom: 20px;">
                            <h4><?php echo __("Tablet Devices - Portrait:", "ts_visual_composer_extend"); ?></h4>
                            <p style="font-size: 12px;"><?php echo __("Define the maximum table width at which a tablet device in portrait format should be assumed:", "ts_visual_composer_extend"); ?></p>
                            <?php
                                $settings = array(
                                    "param_name"		=> "ts_vcsc_extend_settings_defaultDatatableTabletP",
                                    "min"				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DatatableMobileL + 1,
                                    "max"				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DatatableTabletL - 20,
                                    "step"				=> "1",
									"pips"				=> "false",
                                    "range"				=> "false",
									"unit"				=> "px",
                                    "value"				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DatatableTabletP,
									"default"			=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['breakpoints']['datatableTabletP'],
									"group"				=> "datatable",
                                );
                                echo TS_TablesWP_NoUiSlider_Settings_Field($settings, $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DatatableTabletP, 'ts_vcsc_extend_settings_defaultDatatableTabletP');
                            ?>
                        </div>
                        <div style="margin-top: 20px; margin-bottom: 20px;">
                            <h4><?php echo __("Mobile Devices - Landscape:", "ts_visual_composer_extend"); ?></h4>
                            <p style="font-size: 12px;"><?php echo __("Define the maximum table width at which a mobile device in landscape format should be assumed:", "ts_visual_composer_extend"); ?></p>
                            <?php
                                $settings = array(
                                    "param_name"		=> "ts_vcsc_extend_settings_defaultDatatableMobileL",
                                    "min"				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DatatableMobileP + 1,
                                    "max"				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DatatableTabletP - 20,
                                    "step"				=> "1",
									"pips"				=> "false",
                                    "range"				=> "false",
									"unit"				=> "px",
                                    "value"				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DatatableMobileL,
									"default"			=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['breakpoints']['datatableMobileL'],
									"group"				=> "datatable",
                                );
                                echo TS_TablesWP_NoUiSlider_Settings_Field($settings, $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DatatableMobileL, 'ts_vcsc_extend_settings_defaultDatatableMobileL');
                            ?>
                        </div>
                        <div style="margin-top: 20px; margin-bottom: 20px;">
                            <h4><?php echo __("Mobile Devices - Portrait:", "ts_visual_composer_extend"); ?></h4>
                            <p style="font-size: 12px;"><?php echo __("Define the maximum table width at which a mobile device in portrait format should be assumed:", "ts_visual_composer_extend"); ?></p>
                            <?php
                                $settings = array(
                                    "param_name"		=> "ts_vcsc_extend_settings_defaultDatatableMobileP",
                                    "min"				=> "240",
                                    "max"				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DatatableMobileL - 20,
                                    "step"				=> "1",
									"pips"				=> "false",
                                    "range"				=> "false",
									"unit"				=> "px",
                                    "value"				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DatatableMobileP,
									"default"			=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['breakpoints']['datatableMobileP'],
									"group"				=> "datatable",
                                );
                                echo TS_TablesWP_NoUiSlider_Settings_Field($settings, $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DatatableMobileP, 'ts_vcsc_extend_settings_defaultDatatableMobileP');
                            ?>
                        </div>
					</div>
				</div>
			</div>
			<?php
				if (function_exists('file_get_contents')) {
					include('ts_tableswp_assets_iconfonts.php');
					include('ts_tableswp_assets_changelog.php');
				}
			?>
        </div>
		<div class="ts-vcsc-settings-group-bottombar ts-vcsc-settings-group-buttonbar" style="">
			<div class="ts-vcsc-settings-group-actionbar">
				<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder ts-advanced-table-tooltip-right">
					<span class="ts-advanced-table-tooltip-content"><?php _e("Click here to save your plugin settings.", "ts_visual_composer_extend"); ?></span>
					<button type="submit" name="Submit" id="ts_vcsc_extend_settings_submit_2" class="ts-advanced-tables-button-main ts-advanced-tables-button-blue ts-advanced-tables-button-save" style="margin: 0;">
						<?php _e("Save Settings", "ts_visual_composer_extend"); ?>
					</button>
				</div>
			</div>
			<div class="clear"></div>
		</div>
	</div>
</form>