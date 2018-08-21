<?php
    /*
    Additional Setting Options:
        array(
            "type"          => "switch_button",
			"value"         => "true",
			"on"            => __( 'Yes', "ts_visual_composer_extend" ),
			"off"           => __( 'No', "ts_visual_composer_extend" ),
        )
    */
    if (!class_exists('TS_Parameter_Switch')) {
        class TS_Parameter_Switch {
            function __construct() {	
                if (function_exists('vc_add_shortcode_param')) {
                    vc_add_shortcode_param('switch_button', array(&$this, 'switch_button_settings_field'));
				} else if (function_exists('add_shortcode_param')) {
					add_shortcode_param('switch_button', array(&$this, 'switch_button_settings_field'));
				}
            }        
            function switch_button_settings_field($settings, $value) {
                $param_name     = isset($settings['param_name'])    ? $settings['param_name']   : '';
                $type           = isset($settings['type'])          ? $settings['type']         : '';
                $on            	= isset($settings['on'])            ? $settings['on']           : __( "Yes", "ts_visual_composer_extend" );
                $off            = isset($settings['off'])           ? $settings['off']          : __( "No", "ts_visual_composer_extend" );
				$render			= isset($settings['render'])		? $settings['render']		: 'string';
				// Global Settings
                $suffix         = isset($settings['suffix'])        ? $settings['suffix']       : '';
                $class          = isset($settings['class'])         ? $settings['class']        : '';
				$render			= isset($settings['render'])        ? $settings['render']       : 'string';
				$output         = '';
				// Contingency Check
				if ($render == "numeric") {
					if ($value == "true") {
						$value	= "1";
					} else if ($value == "false") {
						$value	= "0";
					}
				} else if ($render == "string") {
					if ($value == "1") {
						$value	= "true";
					} else if ($value == "0") {
						$value	= "false";
					}
				}
				// Final Output                
				$output .= '<div class="ts-switch-button ts-codestar-field-switcher" data-value="' . $value . '" data-render="' . $render . '">';
					$output .= '<input type="hidden" style="display: none;" class="ts-codestar-value toggle-input wpb_vc_param_value ' . $param_name . ' ' . $type . '" value="' . $value . '" name="' . $param_name . '"/>';
					$output .= '<div class="ts-codestar-fieldset">';
						$output .= '<label class="ts-codestar-label">';										
							$output .= '<input id="' . $param_name . '-checkbox" value="' . $value . '" class="ts-codestar-checkbox" type="checkbox" ' . ((($value == "true") || ($value == "1")) ? 'checked="checked"' : '') . '> ';
							$output .= '<em data-on="'. $on .'" data-off="'. $off .'"></em>';
							$output .= '<span></span>';
						$output .= '</label>';
					$output .= '</div>';
				$output .= '</div>';
                return $output;
            }            
        }
    }
    if (class_exists('TS_Parameter_Switch')) {
        $TS_Parameter_Switch = new TS_Parameter_Switch();
    }
?>