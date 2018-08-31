<?php
    /*
     No Additional Setting Options
    */
    if (!class_exists('TS_Parameter_HiddenInput')) {
        class TS_Parameter_HiddenInput {
            function __construct() {	
                if (function_exists('vc_add_shortcode_param')) {
					vc_add_shortcode_param('hidden_input', array(&$this, 'hiddeninput_setting_field'));
				} else if (function_exists('add_shortcode_param')) {
                    add_shortcode_param('hidden_input', array(&$this, 'hiddeninput_setting_field'));
				}
            }        
            function hiddeninput_setting_field($settings, $value){
                $param_name     = isset($settings['param_name']) ? $settings['param_name'] : '';
                $type           = isset($settings['type']) ? $settings['type'] : '';
                $radios         = isset($settings['options']) ? $settings['options'] : '';
				$randomizer		= rand(100000, 999999);
                $output 		= '';
				$output .= '<div id="ts-hidden-input-wrapper-' . $randomizer . '" class="ts-hidden-input-wrapper" data-name="' . $param_name . '" style="display: none;">';
					$output .= '<input name="' . $param_name . '" id="' . $param_name . '" class="wpb_vc_param_value ts_shortcode_hidden ' . $param_name . ' ' . $type . '" type="hidden" value="' . $value . '"/>';
                $output .= '</div>';
				return $output;
            }
        }
    }
    if (class_exists('TS_Parameter_HiddenInput')) {
        $TS_Parameter_HiddenInput = new TS_Parameter_HiddenInput();
    }
?>