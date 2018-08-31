<?php
    if (!class_exists('TS_Parameter_Tables')) {
        class TS_Parameter_Tables {
            function __construct() {	
                if (function_exists('vc_add_shortcode_param')) {
                    vc_add_shortcode_param('advanced_tables', array(&$this, 'advanced_tables_settings_field'));
				} else if (function_exists('add_shortcode_param')) {
					add_shortcode_param('advanced_tables', array(&$this, 'advanced_tables_settings_field'));
				}
            }        
            function advanced_tables_settings_field($settings, $value) {
                global $TS_ADVANCED_TABLESWP;
                $param_name     			= isset($settings['param_name'])    ? $settings['param_name']   	: '';
                $type           			= isset($settings['type'])          ? $settings['type']         	: '';
				// Global Settings
                $suffix         			= isset($settings['suffix'])        ? $settings['suffix']       	: '';
                $class          			= isset($settings['class'])         ? $settings['class']        	: '';
				// String Settings
				$string_rows				= isset($settings['string_rows'])	? $settings['string_rows']		: __( "Rows:", "ts_visual_composer_extend" );
				$string_cols				= isset($settings['string_cols'])	? $settings['string_cols']		: __( "Columns:", "ts_visual_composer_extend" );
				$string_create				= isset($settings['string_create'])	? $settings['string_create']	: __( "Created:", "ts_visual_composer_extend" );
				$string_update				= isset($settings['string_update'])	? $settings['string_update']	: __( "Updated:", "ts_visual_composer_extend" );
				// Other Settings
				$wordpress_date				= get_option('date_format');
				$wordpress_time				= get_option('time_format');
                $output         			= '';
				$randomizer					= rand(100000, 999999);
				// Check if Table Still Exists				
				if (($value != "") && (count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables) > 0)) {
					$tablecheck				= false;
					foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables as $tables => $table) {
						if ($table['id'] == $value) {
							$tablecheck 	= true;
							break;
						}
					}
					if ($tablecheck == false) {
						$value				= '';
					}
				} else if (($value != "") && (count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables) == 0)) {
					$value					= '';
				}
				// Get Existing Tables
				$output .= '<div id="ts-advanced-tables-wrapper-' . $randomizer . '" class="ts-advanced-tables-wrapper clearFixMe ts-settings-parameter-gradient-grey" data-name="' . $param_name . '" data-value="' . $value . '">';
					$output .= '<div id="ts-advanced-tables-select-' . $randomizer . '" class="ts-advanced-tables-select">';
						$output .= '<select id="' . $param_name . '" class="wpb_vc_param_value ts-advanced-tables-select-input ' . $param_name . ' ' . $type . '" name="' . $param_name . '">';
							if ($value == "") {
								$output .= '<option value="" disabled="disabled" ' . selected('', $value) . '>' . __( "Select Your Table", "ts_visual_composer_extend" ) . '</option>';
							}
							if (count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables) > 0) {
								foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables as $tables => $table) {
									$output .= '<option value="' . $table['id'] . '" ' . selected($table['id'], $value, false) . ' data-value="' . $table['id'] . '" data-info="' . base64_encode(preg_replace('/\\\"/',"\"", (rawurldecode(base64_decode(strip_tags($table['info'])))))) . '" data-rows="' . $table['rows'] . '" data-columns="' . $table['columns'] . '" data-merged="' . $table['merged'] . '" data-created="' . date($wordpress_date . ' - ' . $wordpress_time, $table['create']) . '" data-updated="' . date($wordpress_date . ' - ' . $wordpress_time, $table['update']) . '">' . $table['name'] . ' (ID#' . $table['id'] . ')</option>';
								}
							}
						$output .= '</select>';
					$output .= '</div>';
					$output .= '<div id="ts-advanced-tables-summary-' . $randomizer . '" class="ts-advanced-tables-summary" data-string-rows="' . $string_rows . '" data-string-columns="' . $string_cols . '" data-string-created="' . $string_create . '" data-string-updated="' . $string_update . '">';
						if (($value != "") && (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables["table" . $value]))) {
							$table = $TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables["table" . $value];
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
                return $output;
            }            
        }
    }
    if (class_exists('TS_Parameter_Tables')) {
        $TS_Parameter_Tables = new TS_Parameter_Tables();
    }
?>