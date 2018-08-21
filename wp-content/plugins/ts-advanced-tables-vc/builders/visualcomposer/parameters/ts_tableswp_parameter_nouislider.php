<?php
    /*
     No Additional Setting Options
    */
    if (!class_exists('TS_Parameter_NoUiSlider')) {
        class TS_Parameter_NoUiSlider {
            function __construct() {	
                if (function_exists('vc_add_shortcode_param')) {
                    vc_add_shortcode_param('nouislider', array(&$this, 'nouislider_settings_field'));
				} else if (function_exists('add_shortcode_param')) {
                    add_shortcode_param('nouislider', array(&$this, 'nouislider_settings_field'));
				}
            }        
            function nouislider_settings_field($settings, $value) {
                $param_name     		= isset($settings['param_name']) ? $settings['param_name'] : '';
                $type           		= isset($settings['type']) ? $settings['type'] : '';
                $min            		= isset($settings['min']) ? $settings['min'] : '';
                $max            		= isset($settings['max']) ? $settings['max'] : '';
                $step           		= isset($settings['step']) ? $settings['step'] : '';
                $unit           		= isset($settings['unit']) ? $settings['unit'] : '';
                $decimals				= isset($settings['decimals']) ? $settings['decimals'] : 0;
				$callback				= isset($settings['callback']) ? $settings['callback'] : "";
				$extraction				= isset($settings['extraction']) ? $settings['extraction'] : 'false';
				// Range Additions
				$range					= isset($settings['range']) ? $settings['range'] : "false";
				$start					= isset($settings['start']) ? $settings['start'] : $min;
				$end					= isset($settings['end']) ? $settings['end'] : $max;				
				// Other Settings
			    $suffix         		= isset($settings['suffix']) ? $settings['suffix'] : '';
                $class          		= isset($settings['class']) ? $settings['class'] : '';
                $output         		= '';
				$randomizer             = mt_rand(999999, 9999999);
				// Contingency Checks
				if (($extraction == "true") && ($range == "false")) {
					$slidervalue		= preg_replace("/[^0-9]/", "", $value);
				} else {
					$slidervalue		= $value;
				}
				if ($range == "false") {
					$output .= '<div id="ts-nouislider-input-slider-wrapper-' . $randomizer . '" class="ts-nouislider-input-slider-wrapper clearFixMe ts-settings-parameter-gradient-grey">';
						$output .= '<div id="ts-nouislider-input-slider-' . $randomizer . '" class="ts-nouislider-input-slider">';
							if ($extraction == "true") {
								$output .= '<input id="ts-nouislider-input-serial-' . $randomizer . '" class="ts-nouislider-input-serial nouislider-input-selector nouislider-input-composer" type="text" min="' . $min . '" max="' . $max . '" step="' . $step . '" value="' . $slidervalue . '"/>';								
							} else {
								$output .= '<input id="ts-nouislider-input-serial-' . $randomizer . '" name="' . $param_name . '" class="ts-nouislider-input-serial nouislider-input-selector nouislider-input-composer wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" type="text" min="' . $min . '" max="' . $max . '" step="' . $step . '" value="' . $value . '"/>';
							}
							$output .= '<span class="ts-nouislider-input-unit">' . $unit . '</span>';
							$output .= '<span class="ts-nouislider-input-min">' . number_format_i18n($min, $decimals) . '</span>';
							$output .= '<span class="ts-nouislider-input-down dashicons-arrow-left"></span>';
							$output .= '<div id="ts-nouislider-input-element-' . $randomizer . '" class="ts-nouislider-input ts-nouislider-input-element" data-init="false" data-extract="' . $extraction . '" data-callback="' . $callback . '" data-pips="false" data-tooltip="false" data-value="' . $slidervalue . '" data-min="' . $min . '" data-max="' . $max . '" data-decimals="' . $decimals . '" data-step="' . $step . '" data-class="general" data-unit="' . $unit . '" style="width: 280px; float: left; margin-top: 10px;"></div>';
							$output .= '<span class="ts-nouislider-input-up dashicons-arrow-right"></span>';
							$output .= '<span class="ts-nouislider-input-max">' . number_format_i18n($max, $decimals) . '</span>';
						$output .= '</div>';
						if ($extraction == "true") {
							$output .= '<input id=ts-nouislider-input-value-' . $randomizer . '" name="' . $param_name . '" class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . ' ts-nouislider-input-value" style="display: none;"  value="' . $value . '"/>';
						}
					$output .= '</div>';
				} else if ($range == "true") {
					$output .= '<div id="ts-nouislider-range-slider-wrapper-' . $randomizer . '" class="ts-nouislider-range-slider-wrapper clearFixMe ts-settings-parameter-gradient-grey" style="min-height: 150px;">';
						$output .= '<div id="ts-nouislider-range-slider-' . $randomizer . '" class="ts-nouislider-range-slider">';
							$output .= '<div id="ts-nouislider-range-output-' . $randomizer . '" class="ts-nouislider-range-output" data-controls="ts-nouislider-range-controls-' . $randomizer . '">';
								$output .= '<div id="ts-nouislider-range-human-' . $randomizer . '" class="ts-nouislider-range-human">';	
									$output .= '<span class="ts-nouislider-range-start"></span> - <span class="ts-nouislider-range-end"></span>';							
								$output .= '</div>';
							$output .= '</div>';
							$output .= '<div id="ts-nouislider-range-controls-' . $randomizer . '" class="ts-nouislider-range-controls" data-output="ts-nouislider-range-output-' . $randomizer . '">';
								$output .= '<input id="ts-nouislider-input-serial-' . $randomizer . '" name="' . $param_name . '" class="ts-nouislider-input-serial nouislider-range-selector nouislider-input-composer wpb_vc_param_value ' . $param_name . ' ' . $type . '" type="hidden" value="' . $value . '" style="display: none;"/>';
								$output .= '<span class="ts-nouislider-range-lower-down dashicons-arrow-left"></span>';
								$output .= '<span class="ts-nouislider-range-lower-up dashicons-arrow-right"></span>';						
								$output .= '<div id="ts-nouislider-range-element-' . $randomizer . '" class="ts-nouislider-range ts-nouislider-range-element" data-callback="' . $callback . '" data-value="' . $value . '" data-start="' . $start . '" data-end="' . $end . '" data-min="' . $min . '" data-max="' . $max . '" data-decimals="' . $decimals . '" data-step="' . $step . '" style="width: 400px; float: left; margin: 10px auto;"></div>';
								$output .= '<span class="ts-nouislider-range-upper-down dashicons-arrow-left"></span>';
								$output .= '<span class="ts-nouislider-range-upper-up dashicons-arrow-right"></span>';
							$output .= '</div>';
						$output .= '</div>';
					$output .= '</div>';
				}
                return $output;
            }
        }
    }
    if (class_exists('TS_Parameter_NoUiSlider')) {
        $TS_Parameter_NoUiSlider = new TS_Parameter_NoUiSlider();
    }
?>