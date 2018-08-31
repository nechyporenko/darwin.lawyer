<?php
    /*
     No Additional Setting Options
    */
    if (!class_exists('TS_Parameter_LivePreview')) {
        class TS_Parameter_LivePreview {
            function __construct() {	
                if (function_exists('vc_add_shortcode_param')) {
                    vc_add_shortcode_param('livepreview', array(&$this, 'livepreview_settings_field'));
				} else if (function_exists('add_shortcode_param')) {
                    add_shortcode_param('livepreview', array(&$this, 'livepreview_settings_field'));
				}
            }        
            function livepreview_settings_field($settings, $value) {
                global $TS_ADVANCED_TABLESWP;
                $param_name     	= isset($settings['param_name']) ? $settings['param_name'] : '';
                $type           	= isset($settings['type']) ? $settings['type'] : '';
				$class 				= isset($settings['class']) ? $settings['class'] : '';
				$preview			= isset($settings['preview']) ? $settings['preview'] : 'preloaders';
				$shownone			= isset($settings['shownone']) ? $settings['shownone'] : 'true';
				$prefix				= isset($settings['prefix']) ? $settings['prefix'] : '';
				$connector			= isset($settings['connector']) ? $settings['connector'] : '';
				$randomizer			= rand(100000, 999999);
                $output         	= '';				
                $output .= '<div id="ts-live-review-wrapper-' . $randomizer . '" class="ts-live-preview-wrapper clearFixMe ts-settings-parameter-gradient-grey" data-preview="' . $preview . '" data-connector="' . $connector . '" data-prefix="' . $prefix . '">';
					$output .= '<div class="ts-live-preview-selector">';
                        $output .= '<select name="' . $param_name . '" class="wpb_vc_param_value wpb-input wpb-select dropdown ' . $param_name . ' ' . $type . ' ' . $class . ' ' . $value . '" data-class="' . $class . '" data-type="' . $type . '" data-name="' . $param_name . '" data-option="' . $value . '" value="' . $value . '">';
                            if ($preview == "preloaders") {								
								foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_Preloader_Styles as $key => $index) {
									if ($index == -1) {
										if ($shownone == "true") {
											$output .= '<option class="" value="' . $index . '" data-name="' . $key . '" data-value="' . $index . '" ' . selected($index, $value, false) . '>' . $key . '</option>';
										}
									} else {
										$output .= '<option class="" value="' . $index . '" data-name="' . $key . '" data-value="' . $index . '" ' . selected($index, $value, false) . '>' . $key . '</option>';
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

                return $output;
            }
        }
    }
    if (class_exists('TS_Parameter_LivePreview')) {
        $TS_Parameter_LivePreview = new TS_Parameter_LivePreview();
    }
?>