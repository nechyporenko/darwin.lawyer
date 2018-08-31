<?php
    global $TS_ADVANCED_TABLESWP;
	// Register Container and Child Shortcode with Visual Composer
	if ((class_exists('WPBakeryShortCode')) && (!class_exists('WPBakeryShortCode_TS_Advanced_Charts'))) {
		class WPBakeryShortCode_TS_Advanced_Charts extends WPBakeryShortCode {};
	}
	// Create Data Array for Currency Symbols
	$TS_Currency_Codes_Array 			= array();
    foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_Currency_HTML_Codes as $key => $value) {
		$value 							= __($value, "ts_visual_composer_extend");
		$code							= TS_TablesWP_GetStringBetween ($value, "&#", ";");
		$search							= "&#" . $code . ";";
		$replace						= $TS_ADVANCED_TABLESWP->TS_TablesWP_Currency_HTML_Inverted[$code];
		$value							= str_replace($search, $replace, $value);
		$TS_Currency_Codes_Array[$value] = $key;
	}
	foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_Currency_CountryCodes as $key => $value) {
		$value 							= __($value, "ts_visual_composer_extend");
		$TS_Currency_Codes_Array[$value] = $key;
    }	
	// Create Data Array for Locales
	$TS_Locales_Codes_Array 			= array();
	foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_NumbroJS_Locales as $key => $value) {
		$value 							= __($value, "ts_visual_composer_extend");
		$TS_Locales_Codes_Array[$value] = $key;
    }
	// Create Data Array for Element
	$TS_ADVANCED_TABLESWP->TS_TablesWP_VisualComposer_Element = array(
		"name" 							=> __("TS Advanced Chart", "ts_visual_composer_extend"),
		"base" 							=> "TS_Advanced_Charts",
		"icon" 							=> "ts-composer-element-icon-advanced-charts",
		"category" 						=> __('VC Extensions', "ts_visual_composer_extend"),
		"description"					=> __("Place an advanced chart element", "ts_visual_composer_extend"),
		"admin_enqueue_js"				=> "",
		"admin_enqueue_css"				=> "",
		"show_settings_on_create" 		=> true,
		"params" 						=> array(
			// Table Selector
			array(
				"type"              	=> "seperator",
				"param_name"        	=> "seperator_a01",
				"seperator"				=>  __( "Table Selection", "ts_visual_composer_extend" ),
			),
			array(
				"type"              	=> "advanced_tables",
				"heading"           	=> __( "Table: ID", "ts_visual_composer_extend" ),
				"param_name"        	=> "id",
				"value" 				=> "",
				"admin_label"       	=> true,
				"description"       	=> __( "Select the table you want to use as datasource for the chart.", "ts_visual_composer_extend" ),
			),
			// Chart Scope
			array(
				"type"              	=> "dropdown",
				"heading"           	=> __( "Chart: Type", "ts_visual_composer_extend" ),
				"param_name"        	=> "chart_type",
				"value"             	=> array(
					__( "Pie Chart", "ts_visual_composer_extend" )									=> "pie",
					__( "Doughnut Chart", "ts_visual_composer_extend" )								=> "doughnut",
					__( "Vertical Bar Chart", "ts_visual_composer_extend" )							=> "bar",
					__( "Horizontal Bar Chart", "ts_visual_composer_extend" )						=> "horizontalBar",
					__( "Line Chart", "ts_visual_composer_extend" )									=> "line",
					__( "Radar Chart", "ts_visual_composer_extend" )								=> "radar",
					__( "Polar Area Chart", "ts_visual_composer_extend" )							=> "polarArea",
					//__( "Bubble Chart", "ts_visual_composer_extend" )								=> "bubble",
				),
				"admin_label"       	=> true,
				"description"			=> __( "Define what type of chart you want to render.", "ts_visual_composer_extend" ),
			),
			// Global Chart Settings
			array(
				"type"					=> "switch_button",
				"heading"           	=> __( "Chart: Multiple Datasets", "ts_visual_composer_extend" ),
				"param_name"        	=> "chart_multiple",
				"value"             	=> "false",
				"admin_label"       	=> true,
				"description"       	=> __( "Switch the toggle if you want to use multiple datasets for this chart.", "ts_visual_composer_extend" ),
			),
			array(
				"type"					=> "switch_button",
				"heading"           	=> __( "Chart: Debug Output", "ts_visual_composer_extend" ),
				"param_name"        	=> "chart_debug",
				"value"             	=> "false",
				"admin_label"       	=> true,
				"description"       	=> __( "Switch the toggle if you want output a textarea containing the generated chart data for easier debugging.", "ts_visual_composer_extend" ),
			),
			array(
				"type"					=> "switch_button",
				"heading"           	=> __( "Chart: Stack Data", "ts_visual_composer_extend" ),
				"param_name"        	=> "chart_stacked",
				"value"             	=> "false",
				"dependency"        	=> array( 'element' => "chart_type", 'value' => array('bar', 'horizontalBar') ),
				"description"       	=> __( "Switch the toggle if you want to stack the chart data, instead of displaying it separated.", "ts_visual_composer_extend" ),
			),
			array(
				"type"					=> "switch_button",
				"heading"           	=> __( "Chart: Fill Area", "ts_visual_composer_extend" ),
				"param_name"        	=> "chart_filled",
				"value"             	=> "false",
				"dependency"        	=> array( 'element' => "chart_type", 'value' => array('line', 'radar') ),
				"description"       	=> __( "Switch the toggle if you want to fill the area under or between the lines.", "ts_visual_composer_extend" ),
			),
			// Number Formatting
			array(
				"type"              	=> "seperator",
				"param_name"        	=> "seperator_b01",
				"seperator"				=>  __( "Numbers Formatting", "ts_visual_composer_extend" ),
				"group" 				=> __( "Number Formats", "ts_visual_composer_extend" ),
			),
			array(
				"type"              	=> "dropdown",
				"heading"           	=> __( "Numbers: Type", "ts_visual_composer_extend" ),
				"param_name"        	=> "numbers_type",
				"value"             	=> array(
					__( "No Formatting", "ts_visual_composer_extend" )								=> "none",
					__( "Number", "ts_visual_composer_extend" )										=> "number",
					__( "Currency", "ts_visual_composer_extend" )									=> "currency",
					__( "Percent", "ts_visual_composer_extend" )									=> "percent",
				),
				"description"			=> __( "Define what type of number format you want to apply to tooltip and/or scale values.", "ts_visual_composer_extend" ),
				"group" 				=> __( "Number Formats", "ts_visual_composer_extend" ),
			),
			array(
				"type"              	=> "dropdown",
				"heading"           	=> __( "Numbers: Locale", "ts_visual_composer_extend" ),
				"param_name"        	=> "numbers_locale",
				"value"             	=> $TS_Locales_Codes_Array,
				"description"			=> __( "Define the locale that should be used for all number formatting.", "ts_visual_composer_extend" ),
				"default"				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_EditorLocale,
				"standard"				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_EditorLocale,
				"std"					=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_EditorLocale,
				"dependency"        	=> array( 'element' => "numbers_type", 'value' => array('number', 'currency', 'percent') ),
				"group" 				=> __( "Number Formats", "ts_visual_composer_extend" ),
			),
			array(
				"type"              	=> "dropdown",
				"heading"           	=> __( "Numbers: Symbol", "ts_visual_composer_extend" ),
				"param_name"        	=> "numbers_symbol",
				"value"             	=> $TS_Currency_Codes_Array,
				"description"			=> __( "Define the symbol or abbrevation to be used for all currency values.", "ts_visual_composer_extend" ),
				"dependency"        	=> array( 'element' => "numbers_type", 'value' => 'currency' ),
				"group" 				=> __( "Number Formats", "ts_visual_composer_extend" ),
			),
			array(
				"type"              	=> "messenger",
				"param_name"        	=> "messenger_b01",
				"color"					=> "#006BB7",
				"size"					=> "13",
				"layout"				=> "notice",
				"level"					=> "warning",
				"message"            	=> __( "The actual currency format and currency symbol used will depend upon the locale setting and your selections above.", "ts_visual_composer_extend" ),
				"dependency"        	=> array( 'element' => "numbers_type", 'value' => 'currency' ),
				"group" 				=> __( "Number Formats", "ts_visual_composer_extend" ),
			),
			array(
				"type"              	=> "dropdown",
				"heading"           	=> __( "Numbers: Placement", "ts_visual_composer_extend" ),
				"param_name"        	=> "numbers_placement",
				"value"             	=> array(
					__( "Before ($0.00)", "ts_visual_composer_extend" )								=> "prefix",
					__( "After (0.00$)", "ts_visual_composer_extend" )								=> "postfix",
				),
				"default"				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_CurrencyPlacement,
				"standard"				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_CurrencyPlacement,
				"std"					=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_CurrencyPlacement,
				"description"			=> __( "Define where the currency symbol should be placed in relation to the value.", "ts_visual_composer_extend" ),
				"dependency"        	=> array( 'element' => "numbers_type", 'value' => 'currency' ),
				"group" 				=> __( "Number Formats", "ts_visual_composer_extend" ),
			),
			array(
				"type"					=> "switch_button",
				"heading"				=> __( "Numbers: Space Placeholder", "ts_visual_composer_extend" ),
				"param_name"			=> "numbers_space_currency",
				"value"					=> ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_CurrencySpace == true ? 'true' : 'false'),
				"description"			=> __( "Switch the toggle if there should be a space placeholder between the currency symbol and the numeric value.", "ts_visual_composer_extend" ),
				"dependency"        	=> array( 'element' => "numbers_type", 'value' => 'currency' ),
				"group" 				=> __( "Number Formats", "ts_visual_composer_extend" ),
			),
			array(
				"type"					=> "switch_button",
				"heading"				=> __( "Numbers: Space Placeholder", "ts_visual_composer_extend" ),
				"param_name"			=> "numbers_space_percent",
				"value"					=> ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_PercentSpace == true ? 'true' : 'false'),
				"description"			=> __( "Switch the toggle if there should be a space placeholder between the percentage symbol and the numeric value.", "ts_visual_composer_extend" ),
				"dependency"        	=> array( 'element' => "numbers_type", 'value' => 'percent' ),
				"group" 				=> __( "Number Formats", "ts_visual_composer_extend" ),
			),
			// Other Settings
			array(
				"type"              	=> "seperator",
				"param_name"        	=> "seperator_f01",
				"seperator"				=> __( "Other Settings", "ts_visual_composer_extend" ),
				"group" 				=> __( "Other Settings", "ts_visual_composer_extend" ),
			),
			array(
				"type"              	=> "nouislider",
				"heading"           	=> __( "Margin: Top", "ts_visual_composer_extend" ),
				"param_name"        	=> "margin_top",
				"value"             	=> "0",
				"min"               	=> "-50",
				"max"               	=> "200",
				"step"              	=> "1",
				"unit"              	=> 'px',
				"description"       	=> __( "Select the top margin for the element.", "ts_visual_composer_extend" ),
				"group" 				=> __( "Other Settings", "ts_visual_composer_extend" ),
			),
			array(
				"type"              	=> "nouislider",
				"heading"           	=> __( "Margin: Bottom", "ts_visual_composer_extend" ),
				"param_name"        	=> "margin_bottom",
				"value"             	=> "0",
				"min"               	=> "-50",
				"max"               	=> "200",
				"step"              	=> "1",
				"unit"              	=> 'px',
				"description"       	=> __( "Select the bottom margin for the element.", "ts_visual_composer_extend" ),
				"group" 				=> __( "Other Settings", "ts_visual_composer_extend" ),
			),
			array(
				"type"              	=> "textfield",
				"heading"           	=> __( "Define ID Name", "ts_visual_composer_extend" ),
				"param_name"        	=> "el_id",
				"value"             	=> "",
				"description"       	=> __( "Enter an unique ID for the element.", "ts_visual_composer_extend" ),
				"group" 				=> __( "Other Settings", "ts_visual_composer_extend" ),
			),
			array(
				"type"					=> "tag_editor",
				"heading"				=> __( "Extra Class Names", "ts_visual_composer_extend" ),
				"param_name"			=> "el_class",
				"value"					=> "",
				"description"			=> __( "Enter additional class names for the element.", "ts_visual_composer_extend" ),
				"group"					=> __( "Other Settings", "ts_visual_composer_extend" ),
			),
		)
	);	
	if ($TS_ADVANCED_TABLESWP->TS_TablesWP_VisualComposer_LeanMap == "true") {
		return $TS_ADVANCED_TABLESWP->TS_TablesWP_VisualComposer_Element;
	} else {			
		vc_map($TS_ADVANCED_TABLESWP->TS_TablesWP_VisualComposer_Element);
	};
?>