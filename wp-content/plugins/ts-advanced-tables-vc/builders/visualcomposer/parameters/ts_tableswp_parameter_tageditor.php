<?php
    if (!class_exists('TS_Parameter_TagEditor')) {
        class TS_Parameter_TagEditor {
            function __construct() {	
                if (function_exists('vc_add_shortcode_param')) {
                    vc_add_shortcode_param('tag_editor', array(&$this, 'tag_editor_settings_field'));
				} else if (function_exists('add_shortcode_param')) {
					add_shortcode_param('tag_editor', array(&$this, 'tag_editor_settings_field'));
				}
            }        
            function tag_editor_settings_field($settings, $value) {
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
				$output .= '<div id="ts-tag-editor-wrapper-' . $randomizer . '"class="ts-tag-editor-wrapper" data-value="' . $value . '" data-sortable="' . $sortable . '" data-clickdelete="' . $clickdelete . '" data-delimiter="' . $delimiter . '" data-lowercase="' . $lowercase . '" data-numbersonly="' . $numbersonly . '" data-placeholder="' . $placeholder . '">';
					$output .= '<input id="ts-tag-editor-input-' . $randomizer . '" class="wpb_vc_param_value ts-tag-editor-input ' . $param_name . ' ' . $type . '" name="' . $param_name . '" type="text" value="' . $value . '"/>';
				$output .= '</div>';
                return $output;
            }            
        }
    }
    if (class_exists('TS_Parameter_TagEditor')) {
        $TS_Parameter_TagEditor = new TS_Parameter_TagEditor();
    }
?>