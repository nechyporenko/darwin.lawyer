<?php
	if (class_exists('CSFramework_Options')) {
		// Custom Switcher Field ("true"/"false" Output)
		class CSFramework_Option_switch_button extends CSFramework_Options {		
			public function __construct($field, $value = '', $unique = '') {
				parent::__construct($field, $value, $unique);
			}  
			public function output(){
				$output         	= '';
				$output .= $this->element_before();
				$output .= '<div class="ts-switch-button ts-codestar-custom-switcher" data-value="' . $this->element_value() . '">';
					$output .= '<input type="hidden" style="display: none;" class="ts-codestar-value ' . $this->element_name() . '" value="' . $this->element_value() . '" name="' . $this->element_name() . '" ' . $this->element_class() . $this->element_attributes() . '/>';
					$output .= '<div class="ts-codestar-fieldset">';
						$output .= '<label class="ts-codestar-label">';										
							$output .= '<input id="' . $this->element_name() . '-checkbox" value="' . $this->element_value() . '" class="ts-codestar-switcher-check ts-codestar-checkbox" type="checkbox" ' . ($this->element_value() == "true" ? 'checked="checked"' : '') . '> ';
							$output .= '<em data-on="' . __("Yes", "ts_visual_composer_extend") . '" data-off="' . __("No", "ts_visual_composer_extend") . '"></em>';
							$output .= '<span></span>';
						$output .= '</label>';
					$output .= '</div>';
				$output .= '</div>';
				$output .= $this->element_after();
				echo $output;
			}		
		}		
		// Custom Live Preview Field
		class CSFramework_Option_livepreview extends CSFramework_Options {		
			public function __construct($field, $value = '', $unique = '') {
				parent::__construct($field, $value, $unique);
			}  
			public function output(){
				global $TS_ADVANCED_TABLESWP;
				$preview			= isset($this->field['preview']) 	? $this->field['preview'] 	: 'preloaders';
				$shownone			= isset($this->field['shownone']) 	? $this->field['shownone'] 	: 'true';
				$prefix				= isset($this->field['prefix']) 	? $this->field['prefix'] : '';
				$connector			= isset($this->field['connector']) 	? $this->field['connector'] : '';
				$randomizer 		= rand(100000, 999999);
                $output         	= '';
				$output .= $this->element_before();
                $output .= '<div id="ts-live-review-wrapper-' . $randomizer . '" class="ts-live-preview-wrapper clearFixMe" data-initialized="false" data-preview="' . $preview . '" data-connector="' . $connector . '" data-prefix="' . $prefix . '">';
					$output .= '<div class="ts-live-preview-selector">';
                        $output .= '<select name="' . $this->element_name() . '" class="ts-live-preview-selectbox ' . $this->element_name() . '" ' . $this->element_class() . $this->element_attributes() . ' data-name="' . $this->element_name() . '" data-option="' . $this->element_value() . '" value="' . $this->element_value() . '">';
                            if ($preview == "preloaders") {								
								foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_Preloader_Styles as $key => $index) {
									if ($index == -1) {
										if ($shownone == "true") {
											$output .= '<option class="" value="' . $index . '" data-name="' . $key . '" data-value="' . $index . '" ' . selected($index, 	$this->element_value(), false) . '>' . $key . '</option>';
										}
									} else {
										$output .= '<option class="" value="' . $index . '" data-name="' . $key . '" data-value="' . $index . '" ' . selected($index, 	$this->element_value(), false) . '>' . $key . '</option>';
									}
								}
							}
                        $output .= '</select>';
                    $output .= '</div>';
                    $output .= '<div class="ts-live-preview-display">';
						if ($preview == "preloaders") {
							foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_Preloader_Styles as $key => $index) {
								if ($index != "-1") {
									$output .= TS_TablesWP_CreatePreloaderCSS("ts-live-preview-preloader-" . $randomizer . "-" . $index, "ts-live-preview-hidden", $index, "true");
								}
							}
						}
                    $output .= '</div>';
                $output .= '</div>';
				$output .= $this->element_after();
				echo $output;
			}		
		}		
		// Custom Tag Editor Field
		class CSFramework_Option_tag_editor extends CSFramework_Options {		
			public function __construct($field, $value = '', $unique = '') {
				parent::__construct($field, $value, $unique);
			}  
			public function output(){
				// Tag Editor Settings
				$delimiter			= isset($this->field['delimiter'])		? $this->field['delimiter'] 	: ' ';
				$lowercase			= isset($this->field['lowercase'])		? $this->field['lowercase']		: 'true';
				$numbersonly		= isset($this->field['numbersonly'])	? $this->field['numbersonly']	: 'false';
				$sortable			= isset($this->field['sortable'])		? $this->field['sortable']		: 'true';
				$clickdelete		= isset($this->field['clickdelete'])	? $this->field['clickdelete']	: 'false';
				$placeholder		= isset($this->field['placeholder'])	? $this->field['placeholder'] 	: '';
				$randomizer			= rand(100000, 999999);
                $output         	= '';
				$delimiter			= '' . $delimiter . ';';
				$output         	= '';
				$output .= $this->element_before();
				$output .= '<div id="ts-tag-editor-wrapper-' . $randomizer . '"class="ts-tag-editor-wrapper" data-initialized="false" data-value="' . $this->element_value() . '" data-sortable="' . $sortable . '" data-clickdelete="' . $clickdelete . '" data-delimiter="' . $delimiter . '" data-lowercase="' . $lowercase . '" data-numbersonly="' . $numbersonly . '" data-placeholder="' . $placeholder . '">';
					$output .= '<input id="ts-tag-editor-input-' . $randomizer . '" class="ts-tag-editor-input ' . $this->element_name() . '" ' . $this->element_class() . $this->element_attributes() . ' name="' . $this->element_name() . '" type="text" value="' . $this->element_value() . '"/>';
				$output .= '</div>';
				$output .= $this->element_after();
				echo $output;
			}		
		}
		// Custom Table Selector Field
		class CSFramework_Option_advanced_tables extends CSFramework_Options {		
			public function __construct($field, $value = '', $unique = '') {
				parent::__construct($field, $value, $unique);
			}  
			public function output(){
                global $TS_ADVANCED_TABLESWP;
				// String Settings
				$string_rows				= isset($this->field['string_rows'])	? $this->field['string_rows']		: __( "Rows:", "ts_visual_composer_extend" );
				$string_cols				= isset($this->field['string_cols'])	? $this->field['string_cols']		: __( "Columns:", "ts_visual_composer_extend" );
				$string_create				= isset($this->field['string_create'])	? $this->field['string_create']		: __( "Created:", "ts_visual_composer_extend" );
				$string_update				= isset($this->field['string_update'])	? $this->field['string_update']		: __( "Updated:", "ts_visual_composer_extend" );
				// Other Settings
				$wordpress_date				= get_option('date_format');
				$wordpress_time				= get_option('time_format');
                $output         			= '';
				$randomizer					= rand(100000, 999999);
				// Get Existing Tables
				$output .= $this->element_before();
				$output .= '<div id="ts-advanced-tables-wrapper-' . $randomizer . '" class="ts-advanced-tables-wrapper clearFixMe" data-initialized="false" data-name="' . $this->element_name() . '" data-value="' . $this->element_value() . '">';
					$output .= '<div id="ts-advanced-tables-select-' . $randomizer . '" class="ts-advanced-tables-select">';
						$output .= '<select id="' . $this->element_name() . '" class="ts-advanced-tables-select-input ' . $this->element_name() . '" name="' . $this->element_name() . '" ' . $this->element_class() . $this->element_attributes() . '>';
							if ($this->element_value() == "") {
								$output .= '<option value="" disabled="disabled" ' . selected('', $this->element_value(), false) . '>' . __( "Select Your Table", "ts_visual_composer_extend" ) . '</option>';
							}
							if (count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables) > 0) {
								foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables as $tables => $table) {
									$output .= '<option value="' . $table['id'] . '" ' . selected($table['id'], $this->element_value()) . ' data-value="' . $table['id'] . '" data-info="' . base64_encode(preg_replace('/\\\"/',"\"", (rawurldecode(base64_decode(strip_tags($table['info'])))))) . '" data-rows="' . $table['rows'] . '" data-columns="' . $table['columns'] . '" data-merged="' . $table['merged'] . '" data-created="' . date($wordpress_date . ' - ' . $wordpress_time, $table['create']) . '" data-updated="' . date($wordpress_date . ' - ' . $wordpress_time, $table['update']) . '">' . $table['name'] . ' (ID#' . $table['id'] . ')</option>';
								}
							}
						$output .= '</select>';
					$output .= '</div>';
					$output .= '<div id="ts-advanced-tables-summary-' . $randomizer . '" class="ts-advanced-tables-summary" data-string-rows="' . $string_rows . '" data-string-columns="' . $string_cols . '" data-string-created="' . $string_create . '" data-string-updated="' . $string_update . '">';
						if (($this->element_value() != "") && (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables["table" . $this->element_value()]))) {
							$table = $TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables["table" . $this->element_value()];
							$output .= '<div id="ts-advanced-tables-summary-rows-' . $randomizer . '" class="ts-advanced-tables-summary-rows">' . $string_rows . ' ' . $table['rows'] . '</div>';
							$output .= '<div id="ts-advanced-tables-summary-columns-' . $randomizer . '" class="ts-advanced-tables-summary-columns">' . $string_cols . ' ' . $table['columns'] . '</div>';
							$output .= '<div id="ts-advanced-tables-summary-created-' . $randomizer . '" class="ts-advanced-tables-summary-created">' . $string_create . ' ' . date($wordpress_date . ' - ' . $wordpress_time, $table['create']) . '</div>';
							$output .= '<div id="ts-advanced-tables-summary-updated-' . $randomizer . '" class="ts-advanced-tables-summary-updated">' . $string_update . ' ' . date($wordpress_date . ' - ' . $wordpress_time, $table['update']) . '</div>';
							$output .= '<div id="ts-advanced-tables-summary-info-' . $randomizer . '" class="ts-advanced-tables-summary-info">';
								$output .= preg_replace('/\\\"/',"\"", (rawurldecode(base64_decode(strip_tags($table['info'])))));
							$output .= '</div>';
						} else {
							$output .= '<div id="ts-advanced-tables-summary-rows-' . $randomizer . '" class="ts-advanced-tables-summary-rows">' . $string_rows . ' N/A</div>';
							$output .= '<div id="ts-advanced-tables-summary-columns-' . $randomizer . '" class="ts-advanced-tables-summary-columns">' . $string_cols . ' N/A</div>';
							$output .= '<div id="ts-advanced-tables-summary-created-' . $randomizer . '" class="ts-advanced-tables-summary-created">' . $string_create . ' N/A</div>';
							$output .= '<div id="ts-advanced-tables-summary-updated-' . $randomizer . '" class="ts-advanced-tables-summary-updated">' . $string_update . ' N/A</div>';
							$output .= '<div id="ts-advanced-tables-summary-info-' . $randomizer . '" class="ts-advanced-tables-summary-info"></div>';
						}
					$output .= '</div>';
				$output .= '</div>';
				$output .= $this->element_after();
				echo $output;
			}		
		}
	}
?>