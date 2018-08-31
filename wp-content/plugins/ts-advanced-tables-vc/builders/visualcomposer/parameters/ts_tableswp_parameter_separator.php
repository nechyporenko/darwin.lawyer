<?php
    /*
     No Additional Setting Options
    */
    if (!class_exists('TS_Parameter_Separator')) {
        class TS_Parameter_Separator {
            function __construct() {	
                if (function_exists('vc_add_shortcode_param')) {
                    vc_add_shortcode_param('seperator', array(&$this, 'seperator_settings_field'));
				} else if (function_exists('add_shortcode_param')) {
                    add_shortcode_param('seperator', array(&$this, 'seperator_settings_field'));
				}
            }        
            function seperator_settings_field($settings, $value) {
                $param_name     = isset($settings['param_name']) ? $settings['param_name'] : '';
                $type           = isset($settings['type']) ? $settings['type'] : '';
                $seperator		= isset($settings['seperator']) ? $settings['seperator'] : '';
				$align			= isset($settings['align']) ? $settings['align'] : 'center';
				$uppercase		= isset($settings['uppercase']) ? $settings['uppercase'] : 'true';
				$fontsize		= isset($settings['fontsize']) ? $settings['fontsize'] : 20;
				$fullwidth		= isset($settings['fullwidth']) ? $settings['fullwidth'] : 'true';
				$borderwidth	= isset($settings['borderwidth']) ? $settings['borderwidth'] : 2;
				$bordertype		= isset($settings['bordertype']) ? $settings['bordertype'] : "solid";
				$bordercolor	= isset($settings['bordercolor']) ? $settings['bordercolor'] : "#dddddd";
                $output         = '';
				$style			= '';
				$style			.= 'text-transform: ' . ($uppercase == "true" ? "uppercase" : "none") . ';';
				$style			.= 'font-size: ' . $fontsize . 'px;';
				$style			.= 'border-bottom: ' . $borderwidth . 'px ' . $bordertype . ' ' . $bordercolor . ';';
				$style			.= ($fullwidth == "false" ? "margin: 10px auto;" : "");
                if ($seperator != '') {
                    $output		.= '<div id="' . $param_name . '" class="ts-settings-panel-separator wpb_vc_param_value ' . $param_name . ' ' . $type . '" name="' . $param_name . '" style="' . $style . '"><div style="text-align: ' . $align . ';">' . $seperator . '</div></div>';
                } else {
                    $output		.= '<div id="' . $param_name . '" class="ts-settings-panel-separator wpb_vc_param_value ' . $param_name . ' ' . $type . '" name="' . $param_name . '" style="' . $style . '"><div style="text-align: ' . $align . ';">' . $value . '</div></div>';
                }
                return $output;
            }
        }
    }
    if (class_exists('TS_Parameter_Separator')) {
        $TS_Parameter_Separator = new TS_Parameter_Separator();
    }
?>