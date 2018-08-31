<?php
	if (!class_exists('TS_Tablenator_Shortcode_Table')){
		class TS_Tablenator_Shortcode_Table {			
			/* ---------------- */
			/* Initialize Class */
			/* ---------------- */
			function __construct() {
				global $TS_ADVANCED_TABLESWP;
				add_shortcode('TS_Advanced_Tables', array($this, 'TS_Advanced_Tables_Function'));
			}
			
			
			/* ------------------------------------------------------ */
			/* Function to Retrieve and Parse Google Spreadsheet Data */
			/* ------------------------------------------------------ */
			function TS_Advanced_Tables_Google ($sp_key, $sp_sheet) {		
				// Construct Google spreadsheet URL's:
				$json 			= "https://spreadsheets.google.com/feeds/cells/{$sp_key}/{$sp_sheet}/public/basic?alt=json";
				$xml 			= "https://spreadsheets.google.com/feeds/cells/{$sp_key}/{$sp_sheet}/public/basic/";				
				// User Agent
				$userAgent 		= "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.9) Gecko/20100315 Firefox/3.5.9";
				// Initialize cURL
				$curl 			= curl_init();				
				// Set cURL Options
				curl_setopt($curl, CURLOPT_URL, 			$json);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 	1);// return page to the variable
				curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 	1);// allow redirects
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 	1); // return into a variable
				curl_setopt($curl, CURLOPT_TIMEOUT, 		40000); // times out after 4s
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 	FALSE);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 	2);
				curl_setopt($curl, CURLOPT_USERAGENT, 		$userAgent);		
				// Execute cURL
				$str 			= curl_exec($curl);
				curl_close($curl);				
				// Extract JSON from cURL Response
				$data 			= json_decode($str, true);
				$entries   		= $data["feed"]["entry"];
				$title			= $data["feed"]["title"]['$t'];
				$updated		= $data["feed"]["updated"]['$t'];
				$res 			= array();
				// Process JSON Data
				foreach($entries as $entry) {
					$content 							= $entry["content"];
					$ind 								= str_replace($xml . "R", "", $entry["id"]['$t']);
					$ii  								= explode("C", $ind);
					$res[$ii[0]-1][$ii[1]-1] 			= trim($entry["content"]['$t']);
				}
				// Return Data
				return $res;
			}
			
			
			/* --------------------------------- */
			/* Function to Convert Tooltip Style */
			/* --------------------------------- */
			function TS_Advances_Tables_Tooltip($style) {
				return str_replace('tooltipster-', 'krauttipster-', $style);
			}

	
			/* --------------------------------- */
			/* Table Shortcode Callback Function */
			/* --------------------------------- */
			function TS_Advanced_Tables_Function ($atts) {
				global $TS_ADVANCED_TABLESWP;
				global $wpdb;
				ob_start();
		
				extract( shortcode_atts( array(
					'source'							=> 'internal',
					'id'								=> '',
					// Google Spreadsheet
					'google_id'							=> '',
					'google_sheet'						=> 1,
					'google_locale'						=> 'en-US',
					'google_thousand'					=> ',',
					'google_decpoint'					=> '.',
					// Show Additional Table Data
					'show_name'							=> 'false',
					'show_info'							=> 'false',
					'show_comments'				 		=> 'false',
					'show_indicator'					=> 'true',
					// Comment Date + Time Holder
					'comments_date_check'				=> 'false',		// ddd:
					'comments_date_tooltip'				=> 'false',
					'comments_time_check'				=> 'false',		// ttt:
					'comments_time_tooltip'				=> 'false',
					// Preloader Settings
					'preloader_use'						=> 'false',
					'preloader_style'					=> 0,
					'preloader_background'				=> '#ffffff',
					// Exclusions
					'exclude_rows'						=> '',
					'exclude_columns'					=> '',
					// Header Settings
					'header_use'						=> 'true',
					'header_rows'						=> 1,
					// Footer Settings
					'footer_use'						=> 'false',
					'footer_rows'						=> 1,
					// Table Scope
					'table_formatting'					=> 'metadata',	// metadata, custom, none
					'table_scope'						=> 'none',
					'table_merged'						=> 'false',
					'table_shortcodes'					=> 'false',
					// Width Settings
					'width_type'						=> 'percent',	// percent, fixed, maximum, none
					'width_percent'						=> 100,
					'width_fixed'						=> 800,
					'width_maximum'						=> 800,
					// Responsive Settings
					'responsive_type'					=> 'none',
					'responsive_switch'					=> 640,
					'responsive_heighttype'				=> 'none',
					'responsive_heightpixel'			=> 800,
					'responsive_heightpercent'			=> 75,			
					// Column Widths
					'columns_widthlimit'				=> 'true',
					'columns_widthmin'					=> 120,
					'columns_widthmax'					=> 300,
					// Column Formats
					'format_html'						=> '',
					'format_date'						=> '',
					'format_time'						=> '',
					'format_number'						=> '',
					'format_currency'					=> '',
					'format_percent'					=> '',
					'format_numeric_html'				=> '',
					'format_natural_simple'				=> '',
					'format_natural_nohtml'				=> '',
					'format_natural_nocase'				=> '',
					// Custom Format Settings
					'numbers_horizontal'				=> '',
					'numbers_vertical'					=> '',					
					'numbers_locale'					=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_EditorLocale,
					'numbers_symbol'					=> '36',
					'numbers_space_percent'				=> ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_PercentSpace == true ? 'true' : 'false'),
					'numbers_space_currency'			=> ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_CurrencySpace == true ? 'true' : 'false'),
					'numbers_placement'					=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_CurrencyPlacement,
					'numbers_decimals_percent'			=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DecimalsPercent,
					'numbers_decimals_numeric'			=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DecimalsNumeric,
					'numbers_decimals_currency'			=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DecimalsCurrency,
					'numbers_date'						=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatDate,
					'numbers_time_hours'				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTimeHours,
					'numbers_time_minutes'				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTimeMinutes,
					'numbers_time_seconds'				=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTimeSeconds,
					'numbers_header'					=> 'false',
					'numbers_footer'					=> 'true',
					'numbers_exclude'					=> '',
					// Tooltip Settings
					'tooltip_position'					=> 'top',
					'tooltip_style'						=> 'krauttipster-black',
					'tooltip_animation'					=> 'fade',
					'tooltip_offsetx'					=> 0,
					'tooltip_offsety'					=> 0,
					// TableSaw Settings
					'tablesaw_scope'					=> 'swipe',
					'tablesaw_persist'					=> '1',
					'tablesaw_fixwidth'					=> 0,
					'tablesaw_sort'						=> 'false',
					'tablesaw_sortswitch'				=> 'false',
					'tablesaw_initial'					=> 1,
					'tablesaw_order'					=> 'ascending',
					'tablesaw_noorder'					=> '',
					'tablesaw_modeswitch'				=> 'false',
					'tablesaw_modeexclude'				=> '',
					'tablesaw_minimap'					=> 'true',
					'tablesaw_text_reponsive'			=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawResponsive']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawResponsive'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['tablesawResponsive']),
					'tablesaw_text_stack'				=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawStack']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawStack'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['tablesawStack']),
					'tablesaw_text_swipe'				=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawSwipe']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawSwipe'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['tablesawSwipe']),
					'tablesaw_text_toggle'				=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawToggle']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawToggle'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['tablesawToggle']),
					'tablesaw_text_columns'				=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawColumns']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawColumns'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['tablesawColumns']),
					'tablesaw_text_error'				=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawError']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawError'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['tablesawError']),
					'tablesaw_text_sort'				=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawSort']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_TableSaw['tablesawSort'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['tablesawSort']),
					// FooTable Settings
					'footable_sort'						=> 'false',
					'footable_initial'					=> 1,
					'footable_order'					=> 'ASC',
					'footable_noorder'					=> '',
					'footable_lengthmenu'				=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_FooTable['footableLengthMenu']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_FooTable['footableLengthMenu'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['footableLengthMenu']),
					'footable_lengthall'				=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_FooTable['footableLengthAll']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_FooTable['footableLengthAll'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['footableLengthAll']),
					'footable_pages'					=> 'false',
					'footable_pagesspot'				=> 'center',
					'footable_countformat'				=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_FooTable['footableCountFormat']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_FooTable['footableCountFormat'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['footableCountFormat']),
					'footable_length'					=> 10,
					'footable_lengthcustom'				=> 10,
					'footable_lengthoptions'			=> '5,10,15,25,50,75,100,150,200',
					'footable_search'					=> 'false',
					'footable_searchspot'				=> 'right',
					'footable_placeholder'				=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_FooTable['footablePlaceholder']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_FooTable['footablePlaceholder'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['footablePlaceholder']),
					'footable_noresults'				=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_FooTable['footableNoResults']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_FooTable['footableNoResults'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['footableNoResults']),
					'footable_nosearch'					=> '',
					'footable_cascade'					=> 'true',
					'footable_notever'					=> '',
					'footable_collapsed'				=> '',
					'footable_breakxs'					=> '',
					'footable_breaksm'					=> '',
					'footable_breakmd'					=> '',
					'footable_breaklg'					=> '',
					// Tabulator Settings
					'tabulator_layout'					=> 'fitColumns',
					'tabulator_responsive'				=> 'collapse',
					'tabulator_priority'				=> '1,5,4,2,3',
					'tabulator_notever'					=> '',
					'tabulator_freezerows'				=> '',
					'tabulator_freezecols'				=> '',
					'tabulator_virtualdom'				=> 'true',
					'tabulator_sorting'					=> 'false',
					'tabulator_nosearch'				=> '',
					'tabulator_paginate'				=> 'false',
					'tabulator_pagesize'				=> 10,
					// Datatables Settings
					'datatables_theme'					=> 'default',
					'datatables_search'					=> 'false',
					'datatables_nosearch'				=> '',
					'datatables_sort'					=> 'false',
					'datatables_inital'					=> 1,
					'datatables_order'					=> 'asc',
					'datatables_noorder'				=> '',
					'datatables_info'					=> 'true',
					'datatables_pages'					=> 'false',
					'datatables_length'					=> 10,
					'datatables_lengthcustom'			=> 10,
					'datatables_lengthoptions'			=> '5,10,15,25,50,75,100,150,200',
					'datatables_pagetype'				=> 'simple_numbers',
					'datatables_topfixed'				=> 'false',
					'datatables_topoffset'				=> 0,
					'datatables_bottomfixed'			=> 'false',
					'datatables_bottomoffset'			=> 0,
					'datatables_buttons'				=> 'false',
					'datatables_printshow'				=> 'true',
					'datatables_printtext'				=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatablePrint']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatablePrint'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatablePrint']),
					'datatables_pdfshow'				=> 'true',
					'datatables_pdftext'				=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatablePDF']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatablePDF'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatablePDF']),
					'datatables_csvshow'				=> 'true',
					'datatables_csvtext'				=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableCSV']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableCSV'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableCSV']),
					'datatables_excelshow'				=> 'true',
					'datatables_exceltext'				=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableExcel']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableExcel'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableExcel']),
					'datatables_copyshow'				=> 'true',
					'datatables_copytext'				=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableCopy']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableCopy'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableCopy']),
					'datatables_togglerow'				=> 'false',
					'datatables_always'					=> '',
					'datatables_collapsed'				=> '',
					'datatables_notever'				=> '',
					'datatables_groupit'				=> 'true',
					'datatables_desktopnot'				=> '',
					'datatables_tabletnot'				=> '',
					'datatables_tabletnot_l'			=> '',
					'datatables_tabletnot_p'			=> '',
					'datatables_mobilenot'				=> '',
					'datatables_mobilenot_l'			=> '',
					'datatables_mobilenot_p'			=> '',
					'datatable_text_processing'			=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableProcessing']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableProcessing'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableProcessing']),
					'datatable_text_lengthmenu'			=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableLengthMenu']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableLengthMenu'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableLengthMenu']),
					'datatable_text_lengthall'			=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableLengthAll']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableLengthAll'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableLengthAll']),
					'datatable_text_infomain'			=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableInfoMain']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableInfoMain'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableInfoMain']),
					'datatable_text_infoempty'			=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableInfoEmpty']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableInfoEmpty'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableInfoEmpty']),
					'datatable_text_infofiltered'		=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableInfoFiltered']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableInfoFiltered'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableInfoFiltered']),
					'datatable_text_search'				=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableSearch']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableSearch'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableSearch']),
					'datatable_text_placeholder'		=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatablePlaceholder']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatablePlaceholder'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatablePlaceholder']),
					'datatable_text_zerorecords'		=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableZeroRecords']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableZeroRecords'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableZeroRecords']),
					'datatable_text_first'				=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableFirst']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableFirst'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableFirst']),
					'datatable_text_previous'			=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatablePrevious']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatablePrevious'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatablePrevious']),
					'datatable_text_next'				=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableNext']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableNext'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableNext']),
					'datatable_text_last'				=> (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableLast']) ? $TS_ADVANCED_TABLESWP->TS_TablesWP_Language_DataTable['datatableLast'] : $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Globals['translations']['datatableLast']),
					// Styling Settings
					'styling_customize'					=> 'false',
					'styling_size_head'					=> 16,
					'styling_size_body'					=> 13,
					'styling_size_foot'					=> 16,					
					'styling_weight_head'				=> 'bold',
					'styling_weight_body'				=> 'normal',
					'styling_weight_foot'				=> 'bold',					
					'styling_transform_head'			=> 'none',
					'styling_transform_body'			=> 'none',
					'styling_transform_foot'			=> 'none',					
					'styling_color_head'				=> '#696969',
					'styling_color_bodyodd'				=> '#696969',
					'styling_color_bodyeven'			=> '#696969',					
					'styling_color_foot'				=> '#696969',
					'styling_background_head'			=> '#e6e6e6',
					'styling_background_bodyodd'		=> '#ffffff',
					'styling_background_bodyeven'		=> '#f9f9f9',
					'styling_background_foot'			=> '#e6e6e6',
					// Other Settings
					'margin_bottom'						=> 0,
					'margin_top' 						=> 0,
					'el_id' 							=> '',
					'el_class'                  		=> '',
					'css'								=> '',
				), $atts ));
				
				$randomizer                    			= mt_rand(999999, 9999999);
				$output 								= '';
				$styles									= '';
				$validator								= "true";
				$inline									= TS_TablesWP_FrontendAppendCustomRules("style");
				
				// Generate (Random) Table ID
				if (!empty($el_id)) {
					$container_id						= $el_id;
				} else {
					$container_id						= 'ts-advanced-tables-container-' . $randomizer;
				}
				
				// Frontend Editor Adjustments
				if ($TS_ADVANCED_TABLESWP->TS_TablesWP_VisualComposer_Frontend == "true") {
					$table_scope						= "none";
					$preloader_use						= "false";
				}
				
				// Load Required CSS/JS Files
				if ($TS_ADVANCED_TABLESWP->TS_TablesWP_VisualComposer_Frontend == "false") {
					$TS_ADVANCED_TABLESWP->TS_TablesWP_IconFontsEnqueue(false);
					if ($table_scope == "datatable") {
						wp_enqueue_style('ts-extend-datatables-full');
						wp_enqueue_style('ts-extend-datatables-custom');
						wp_enqueue_script('ts-extend-datatables-full');
						if ($datatables_buttons == "true") {
							wp_enqueue_script('ts-extend-datatables-jszip');
							wp_enqueue_script('ts-extend-datatables-pdfmaker');
							wp_enqueue_script('ts-extend-datatables-pdffonts');
						}
					} else if ($table_scope == "tablesaw") {
						wp_enqueue_style('ts-extend-tablesaw');
						wp_enqueue_script('ts-extend-tablesaw');
					} else if ($table_scope == "footable") {
						wp_enqueue_style('ts-extend-footable');
						wp_enqueue_script('ts-extend-footable');
					} else if ($table_scope == "tabulator") {
						wp_enqueue_style('ts-extend-jqueryui-widget');
						wp_enqueue_script('ts-extend-jqueryui-widget');
						wp_enqueue_style('ts-extend-tabulator');
						wp_enqueue_script('ts-extend-tabulator');
					}
					if ($show_comments == "true") {
						wp_enqueue_style('ts-extend-krauttipster');
						wp_enqueue_script('ts-extend-krauttipster');
					}
					wp_enqueue_style('ts-advanced-tables-font');
					wp_enqueue_style('ts-extend-preloaders');
					wp_enqueue_script('ts-extend-numbro');
					wp_enqueue_script('ts-extend-momentjs');
					wp_enqueue_script('ts-extend-languages');			
					wp_enqueue_script('ts-extend-advancedtables');
				}
				wp_enqueue_style('ts-extend-advancedtables');
				
				// 1st Round of Contingency Checks
				if ($datatables_groupit == "true") {
					$datatables_tabletnot_l				= '';
					$datatables_tabletnot_p				= '';
					$datatables_mobilenot_l				= '';
					$datatables_mobilenot_p				= '';
				} else {
					$datatables_tabletnot				= '';
					$datatables_mobilenot				= '';
				}
				
				// Tooltip (Comment) Attributes
				if (($show_comments == "true") && ($source == "internal")) {
					$TS_TablesWP_TooltipContent			= 'data-krauttipster-html="true" data-krauttipster-title="" data-krauttipster-image="" data-krauttipster-position="' . $tooltip_position . '" data-krauttipster-touch="false" data-krauttipster-arrow="true" data-krauttipster-theme="' . $tooltip_style . '" data-krauttipster-animation="' . $tooltip_animation . '" data-krauttipster-trigger="hover" data-krauttipster-offsetx="' . $tooltip_offsetx . '" data-krauttipster-offsety="' . $tooltip_offsety . '"';
				} else {
					$TS_TablesWP_TooltipContent			= "";
				}
						
				// Retrieve Table Data
				$TS_TablesWP_Editor_Listing 			= get_option("ts_tablesplus_extend_settings_tables", array());
				$TS_TablesWP_Editor_ID					= $id;
				$TS_TablesWP_Editor_CountCells			= 0;
				$TS_TablesWP_Editor_CountRows			= 0;
				$TS_TablesWP_Editor_CountCols			= 0;
				$TS_TablesWP_Editor_CellValue			= "";
				$TS_TablesWP_Editor_CellCheck			= false;
				if (($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Migrated == false) && ($source == "internal")) {
					$TS_TablesWP_Editor_Import          = get_option('ts_tablesplus_data_singletable_' . $TS_TablesWP_Editor_ID, array());
					if (count($TS_TablesWP_Editor_Import) == 0) {
						$validator						= "false";
					}
				} else if ($source == "internal") {
					$TS_TablesWP_Editor_BaseName		= $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginMYSQL;
					$TS_TablesWP_Editor_BaseData		= $wpdb->get_row("SELECT * FROM $TS_TablesWP_Editor_BaseName WHERE `number` = $TS_TablesWP_Editor_ID");
					if ($TS_TablesWP_Editor_BaseData == null) {
						$validator						= "false";
					}
				} else if (($source == "google") && ($google_id != '')) {
					$TS_TablesWP_Editor_Import 			= $this->TS_Advanced_Tables_Google($google_id, $google_sheet);
					if (($TS_TablesWP_Editor_Import == null) || ($TS_TablesWP_Editor_Import == "") || (!is_array($TS_TablesWP_Editor_Import))) {
						$validator						= "false";
					}
				} else {
					$validator							= "false";
				}
				if (($validator == "false") || (($source == "internal") && ($id == "")) || (($source == "google") && ($google_id == ""))) {
					echo $output;
					$myvariable 						= ob_get_clean();
					return $myvariable;
				}
				
				// Retrieve Global Default Data
				if ($source == "google") {
					$TS_TablesWP_Editor_Globals 				= (object)null;
					$TS_TablesWP_Editor_Globals->numericfull 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatNumeric;
					$TS_TablesWP_Editor_Globals->currencyfull 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatCurrency;
					$TS_TablesWP_Editor_Globals->percentfull 	= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatPercent;
					$TS_TablesWP_Editor_Globals->datefull 		= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTime;
					$TS_TablesWP_Editor_Globals->timefull 		= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTime;
				}
				
				// Process Retrieved Table Data
				if (($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Migrated == false) && ($source == "internal")) {					
					$TS_TablesWP_Editor_Import          = (rawurldecode(base64_decode(strip_tags($TS_TablesWP_Editor_Import))));
					$TS_TablesWP_Editor_Decode          = json_decode($TS_TablesWP_Editor_Import);
					if (isset($TS_TablesWP_Editor_Decode->name)) {
						$TS_TablesWP_Editor_Name        = $TS_TablesWP_Editor_Decode->name;
					} else {
						$TS_TablesWP_Editor_Name        = "";
					}
					if (isset($TS_TablesWP_Editor_Decode->info)) {
						$TS_TablesWP_Editor_Info        = $TS_TablesWP_Editor_Decode->info;
					} else {
						$TS_TablesWP_Editor_Info        = "";
					}
					if (isset($TS_TablesWP_Editor_Decode->date)) {
						$TS_TablesWP_Editor_Date        = date($TS_ADVANCED_TABLESWP->TS_TablesWP_WordPress_Date . ' @' . $TS_ADVANCED_TABLESWP->TS_TablesWP_WordPress_Time, $TS_TablesWP_Editor_Decode->date);
					} else {
						$TS_TablesWP_Editor_Date        = "N/A";
					}
					if (isset($TS_TablesWP_Editor_Decode->merged)) {
						$TS_TablesWP_Editor_Merged		= $TS_TablesWP_Editor_Decode->merged;
					} else {
						$TS_TablesWP_Editor_Merged		= array();
					}
					$TS_TablesWP_Editor_Rows 			= $TS_TablesWP_Editor_Decode->rows;
					$TS_TablesWP_Editor_Columns			= $TS_TablesWP_Editor_Decode->columns;
					$TS_TablesWP_Editor_Data 			= $TS_TablesWP_Editor_Decode->data;
					$TS_TablesWP_Editor_Meta 			= $TS_TablesWP_Editor_Decode->meta;
					$TS_TablesWP_Editor_Defaults 		= $TS_TablesWP_Editor_Decode->defaults;
					$TS_TablesWP_Editor_Other 			= array();
					$TS_TablesWP_Editor_SaveMeta		= "true";
				} else if ($source == "internal") {
					$TS_TablesWP_Editor_Import			= array();
					$TS_TablesWP_Editor_Decode			= array();
					if (isset($TS_TablesWP_Editor_BaseData->name)) {
						$TS_TablesWP_Editor_Name        = $TS_TablesWP_Editor_BaseData->name;
					} else {
						$TS_TablesWP_Editor_Name        = "";
					}
					if (isset($TS_TablesWP_Editor_BaseData->info)) {
						$TS_TablesWP_Editor_Info        = $TS_TablesWP_Editor_BaseData->info;
					} else {
						$TS_TablesWP_Editor_Info        = "";
					}
					if (isset($TS_TablesWP_Editor_BaseData->created)) {
						$TS_TablesWP_Editor_Date		= strtotime($TS_TablesWP_Editor_BaseData->created);
						$TS_TablesWP_Editor_Date        = date($TS_ADVANCED_TABLESWP->TS_TablesWP_WordPress_Date . ' @' . $TS_ADVANCED_TABLESWP->TS_TablesWP_WordPress_Time, $TS_TablesWP_Editor_Date);
					} else {
						$TS_TablesWP_Editor_Date        = "N/A";
					}
					if (isset($TS_TablesWP_Editor_BaseData->merged)) {
						$TS_TablesWP_Editor_Merged		= json_decode($TS_TablesWP_Editor_BaseData->merged);
					} else {
						$TS_TablesWP_Editor_Merged		= array();
					}
					$TS_TablesWP_Editor_Rows 			= $TS_TablesWP_Editor_BaseData->rows;
					$TS_TablesWP_Editor_Columns			= $TS_TablesWP_Editor_BaseData->cols;					
					$TS_TablesWP_Editor_Data			= rawurldecode($TS_TablesWP_Editor_BaseData->data);					
					$TS_TablesWP_Editor_Data 			= json_decode($TS_TablesWP_Editor_Data);
					$TS_TablesWP_Editor_Meta 			= json_decode($TS_TablesWP_Editor_BaseData->meta);
					$TS_TablesWP_Editor_Defaults 		= json_decode($TS_TablesWP_Editor_BaseData->defaults);
					$TS_TablesWP_Editor_Other 			= json_decode($TS_TablesWP_Editor_BaseData->other);					
					$TS_TablesWP_Editor_SaveMeta		= (isset($TS_TablesWP_Editor_Other->savemeta) ? $TS_TablesWP_Editor_Other->savemeta : $TS_TablesWP_Editor_Other->savemeta);
				} else if ($source == "google") {
					$TS_TablesWP_Editor_Decode			= $TS_TablesWP_Editor_Import;
					$TS_TablesWP_Editor_Name        	= "";
					$TS_TablesWP_Editor_Info        	= "";
					$TS_TablesWP_Editor_Date        	= "N/A";
					$TS_TablesWP_Editor_Merged			= array();
					$TS_TablesWP_Editor_Rows			= 0;
					$TS_TablesWP_Editor_Columns			= 0;
					$TS_TablesWP_Editor_Data			= $TS_TablesWP_Editor_Import;
					$TS_TablesWP_Editor_Meta			= array();
					foreach($TS_TablesWP_Editor_Decode as $entries) {
						$TS_TablesWP_Editor_Rows++;
						$TS_TablesWP_Editor_CountRows++;						
						$TS_TablesWP_Editor_CountCols	= 0;						
						foreach($entries as $entry) {
							$TS_TablesWP_Editor_CountCols++;
							$TS_TablesWP_Editor_CountCells++;
							if (preg_match('/^[0-9.,]+$/', $entry)) {
								$TS_TablesWP_Editor_CellCheck			= true;
								$entry 									= preg_replace('/(?<=\d),(?=\d{3}\b)/', '', $entry);
								$entry 									= preg_replace('/(?<=\d).(?=\d{3}\b)/', '', $entry);
							} else {
								$TS_TablesWP_Editor_CellCheck			= false;
							}
							$TS_TablesWP_Editor_CellData				= (object)null;
							$TS_TablesWP_Editor_CellData->count 		= $TS_TablesWP_Editor_CountCells - 1;
							$TS_TablesWP_Editor_CellData->row 			= $TS_TablesWP_Editor_CountRows - 1;
							$TS_TablesWP_Editor_CellData->column 		= $TS_TablesWP_Editor_CountCols - 1;
							$TS_TablesWP_Editor_CellData->value			= $entry;
							$TS_TablesWP_Editor_CellData->momentdate	= '';
							$TS_TablesWP_Editor_CellData->momenttime	= '';
							$TS_TablesWP_Editor_CellData->formula		= false;
							$TS_TablesWP_Editor_CellData->className		= '';
							$TS_TablesWP_Editor_CellData->locale		= $google_locale;
							$TS_TablesWP_Editor_CellData->symbol		= '$';
							$TS_TablesWP_Editor_CellData->dateFormat	= 'MM-DD-YYYY';
							$TS_TablesWP_Editor_CellData->timeFormat	= 'HH:mm';
							$TS_TablesWP_Editor_CellData->type			= ($TS_TablesWP_Editor_CellCheck ? 'number' : 'text');
							$TS_TablesWP_Editor_CellData->dataType		= 'text';
							$TS_TablesWP_Editor_CellData->format		= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatNumeric;
							$TS_TablesWP_Editor_CellData->formatType	= ($TS_TablesWP_Editor_CellCheck ? 'number' : 'text');
							$TS_TablesWP_Editor_CellData->decimals		= $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DecimalsNumeric;
							array_push($TS_TablesWP_Editor_Meta, $TS_TablesWP_Editor_CellData);
						}
						if ($TS_TablesWP_Editor_CountCols > $TS_TablesWP_Editor_Columns) {
							$TS_TablesWP_Editor_Columns	= $TS_TablesWP_Editor_CountCols;
						}
					}					
					$TS_TablesWP_Editor_Defaults		= $TS_TablesWP_Editor_Globals;
					$TS_TablesWP_Editor_Other 			= array();
					$TS_TablesWP_Editor_SaveMeta		= "false";
					$TS_TablesWP_Editor_CountCells		= 0;
					$TS_TablesWP_Editor_CountRows		= 0;
					$TS_TablesWP_Editor_CountCols		= 0;
				}

				// Generate Global Variables
				$TS_TablesWP_Editor_Colors				= array();
				$TS_TablesWP_Editor_Backgrounds			= array();
				$TS_TablesWP_Headers_Labels				= array();
				$TS_TablesWP_Headers_Persist			= array();
				$TS_TablesWP_Headers_Styling			= array();
				if ($table_scope == "datatable") {
					$TS_TablesWP_Headers_NoSearch		= ($datatables_nosearch == "" ? array() : explode(",", $datatables_nosearch));
					$TS_TablesWP_Headers_NoSort			= ($datatables_noorder == "" ? array() : explode(",", $datatables_noorder));
					$TS_TablesWP_Headers_NoShow			= ($datatables_collapsed == "" ? array() : explode(",", $datatables_collapsed));
					$TS_TablesWP_Headers_NoView			= ($datatables_notever == "" ? array() : explode(",", $datatables_notever));
					$TS_TablesWP_Headers_NoHide			= ($datatables_always == "" ? array() : explode(",", $datatables_always));
					$TS_TablesWP_Headers_NoMerge		= array_intersect($TS_TablesWP_Headers_NoShow, $TS_TablesWP_Headers_NoView);
					foreach ($TS_TablesWP_Headers_NoMerge as $column) {
						foreach (array_keys($TS_TablesWP_Headers_NoShow, $column, true) as $key) {
							unset($TS_TablesWP_Headers_NoShow[$key]);
						}
					}
					$TS_TablesWP_Headers_NoDesktop		= ($datatables_desktopnot == "" ? array() : explode(",", $datatables_desktopnot));
					$TS_TablesWP_Headers_NoTablet		= ($datatables_tabletnot == "" ? array() : explode(",", $datatables_tabletnot));
					$TS_TablesWP_Headers_NoTablet_L		= ($datatables_tabletnot_l == "" ? array() : explode(",", $datatables_tabletnot_l));
					$TS_TablesWP_Headers_NoTablet_P		= ($datatables_tabletnot_p == "" ? array() : explode(",", $datatables_tabletnot_p));
					$TS_TablesWP_Headers_NoMobile		= ($datatables_mobilenot == "" ? array() : explode(",", $datatables_mobilenot));
					$TS_TablesWP_Headers_NoMobile_L		= ($datatables_mobilenot_l == "" ? array() : explode(",", $datatables_mobilenot_l));
					$TS_TablesWP_Headers_NoMobile_P		= ($datatables_mobilenot_p == "" ? array() : explode(",", $datatables_mobilenot_p));
					$TS_TablesWP_Headers_NoMerge		= array_intersect($TS_TablesWP_Headers_NoDesktop, $TS_TablesWP_Headers_NoTablet, $TS_TablesWP_Headers_NoMobile);
					foreach ($TS_TablesWP_Headers_NoMerge as $column) {
						array_push($TS_TablesWP_Headers_NoView, $column);
						foreach (array_keys($TS_TablesWP_Headers_NoDesktop, $column, true) as $key) {
							unset($TS_TablesWP_Headers_NoDesktop[$key]);
						}
						foreach (array_keys($TS_TablesWP_Headers_NoTablet, $column, true) as $key) {
							unset($TS_TablesWP_Headers_NoTablet[$key]);
						}
						foreach (array_keys($TS_TablesWP_Headers_NoMobile, $column, true) as $key) {
							unset($TS_TablesWP_Headers_NoMobile[$key]);
						}
					}
				} else if ($table_scope == "tablesaw") {
					$TS_TablesWP_Headers_NoSort			= ($tablesaw_noorder == "" ? array() : explode(",", $tablesaw_noorder));
					$TS_TablesWP_Headers_Persist		= ($tablesaw_persist == "" ? array() : explode(",", $tablesaw_persist));
				} else if ($table_scope == "footable") {
					$TS_TablesWP_Headers_NoSearch		= ($footable_nosearch == "" ? array() : explode(",", $footable_nosearch));
					$TS_TablesWP_Headers_NoSort			= ($footable_noorder == "" ? array() : explode(",", $footable_noorder));
					$TS_TablesWP_Headers_NoShow			= ($footable_collapsed == "" ? array() : explode(",", $footable_collapsed));
					$TS_TablesWP_Headers_NoView			= ($footable_notever == "" ? array() : explode(",", $footable_notever));
					$TS_TablesWP_Headers_NotLarge		= ($footable_breaklg == "" ? array() : explode(",", $footable_breaklg));
					$TS_TablesWP_Headers_NotMedium		= ($footable_breakmd == "" ? array() : explode(",", $footable_breakmd));
					$TS_TablesWP_Headers_NotSmall		= ($footable_breaksm == "" ? array() : explode(",", $footable_breaksm));
					$TS_TablesWP_Headers_NotExtra		= ($footable_breakxs == "" ? array() : explode(",", $footable_breakxs));
				} else if ($table_scope == "tabulator") {
					$TS_TablesWP_Headers_Tabulator		= ($tabulator_priority == "" ? array() : explode(",", $tabulator_priority));
				}
				$TS_TablesWP_Headers_Others				= "";
				$TS_TablesWP_Headers_Sortable			= "";
				$TS_TablesWP_Headers_Searchable			= "";
				$TS_TablesWP_Headers_Priority			= "";
				$TS_TablesWP_Headers_Collapsed			= "";
				$TS_TablesWP_Headers_Hidden				= "";
				$TS_TablesWP_Headers_Always				= "";
				$TS_TablesWP_Headers_Desktop			= "";
				$TS_TablesWP_Headers_Tablet				= "";				
				$TS_TablesWP_Headers_Tablet_L			= "";
				$TS_TablesWP_Headers_Tablet_P			= "";
				$TS_TablesWP_Headers_Mobile				= "";
				$TS_TablesWP_Headers_Mobile_L			= "";
				$TS_TablesWP_Headers_Mobile_P			= "";
				$TS_TablesWP_Headers_Viewable			= "";
				$TS_TablesWP_Headers_Cells				= "";
				$TS_TablesWP_Headers_Value				= "";
				
				// Content Exclusion
				$TS_TablesWP_Exlude_Rows				= explode(",", $exclude_rows);
				$TS_TablesWP_Exlude_Columns				= explode(",", $exclude_columns);
				
				// Format Assignments
				$TS_TablesWP_Format_Table				= 'data-columns-html="' . $format_html . '" data-columns-date="' . $format_date . '" data-columns-time="' . $format_time . '" data-columns-numeric="' . $format_number . '" data-columns-currency="' . $format_currency . '" data-columns-percent="' . $format_percent . '"';
				$TS_TablesWP_Format_HTML				= ($format_html == "" ? array() : explode(",", $format_html));
				$TS_TablesWP_Format_Date				= ($format_date == "" ? array() : explode(",", $format_date));
				$TS_TablesWP_Format_Time				= ($format_time == "" ? array() : explode(",", $format_time));
				$TS_TablesWP_Format_Number				= ($format_number == "" ? array() : explode(",", $format_number));
				$TS_TablesWP_Format_Currency			= ($format_currency == "" ? array() : explode(",", $format_currency));
				$TS_TablesWP_Format_Percent				= ($format_percent == "" ? array() : explode(",", $format_percent));
				$TS_TablesWP_Format_NumberHTML			= ($format_numeric_html == "" ? array() : explode(",", $format_numeric_html));
				$TS_TablesWP_Format_Natural_Simple		= ($format_natural_simple == "" ? array() : explode(",", $format_natural_simple));
				$TS_TablesWP_Format_Natural_NoHTML		= ($format_natural_nohtml == "" ? array() : explode(",", $format_natural_nohtml));
				$TS_TablesWP_Format_Natural_NoCase		= ($format_natural_nocase == "" ? array() : explode(",", $format_natural_nocase));				
				$TS_TablesWP_Format_Exclude				= ($numbers_exclude == "" ? array() : explode(",", $numbers_exclude));
		
				// 2nd Round of Contingency Checks
				if ($table_scope == "datatable") {
					if ($header_use == "false") {
						$tdatatables_sort				= "false";
					}
					if (($datatables_inital < 1) || ($datatables_inital > $TS_TablesWP_Editor_Columns)) {
						$datatables_inital				= 1;
					}
					if (($datatables_search == "true") && ($header_use == "true")) {
						if (in_array($datatables_inital, $TS_TablesWP_Headers_NoView)) {
							unset($TS_TablesWP_Headers_NoView[array_search($datatables_inital, $TS_TablesWP_Headers_NoView)]);
						}
						if (in_array($datatables_inital, $TS_TablesWP_Headers_NoShow)) {
							unset($TS_TablesWP_Headers_NoShow[array_search($datatables_inital, $TS_TablesWP_Headers_NoShow)]);
						}
					}
					$responsive_type					= "none";
					$footer_rows						= 1;
				} else if ($table_scope == "tablesaw") {
					if ($header_use == "false") {
						$header_use						= "true";
						$header_rows					= 1;
					}
					if (($tablesaw_initial < 1) || ($tablesaw_initial > $TS_TablesWP_Editor_Columns)) {
						$tablesaw_initial				= 1;
					}
					$responsive_type					= "none";
					$footer_rows						= 1;
				} else if ($table_scope == "footable") {
					if ($header_use == "false") {
						$footable_sort					= "false";
					}
					if (($footable_initial < 1) || ($footable_initial > $TS_TablesWP_Editor_Columns)) {
						$footable_initial				= 1;
					}
					if (($footable_search == "true") && ($header_use == "true")) {
						if (in_array($footable_initial, $TS_TablesWP_Headers_NoView)) {
							unset($TS_TablesWP_Headers_NoView[array_search($footable_initial, $TS_TablesWP_Headers_NoView)]);
						}
						if (in_array($footable_initial, $TS_TablesWP_Headers_NoShow)) {
							unset($TS_TablesWP_Headers_NoShow[array_search($footable_initial, $TS_TablesWP_Headers_NoShow)]);
						}
					}
					$responsive_type					= "none";
					$footer_use 						= "false";
					$footer_rows						= 1;
				} else if ($table_scope == "tabulator") {
					
				}
				
				// Header + Footer Retrievals
				if (($header_use == "true") && ($header_rows > 0) && ($TS_TablesWP_Editor_Rows > 0)) {
					$TS_TablesWP_Headers_Rows			= array();
					if ($header_rows > $TS_TablesWP_Editor_Rows) {
						$header_rows					= $TS_TablesWP_Editor_Rows;
					}
					for ($header = 0; $header < $header_rows; $header++) {
						array_push($TS_TablesWP_Headers_Rows, $header);
					}			
					for ($col = 0; $col < $TS_TablesWP_Editor_Columns; $col++) {
						$TS_TablesWP_Headers_Cells		= ($header_rows * $col);
						$TS_TablesWP_Headers_Value		= (isset($TS_TablesWP_Editor_Meta[$TS_TablesWP_Headers_Cells]->value) ? $TS_TablesWP_Editor_Meta[$TS_TablesWP_Headers_Cells]->value : '');
						$TS_TablesWP_Headers_Value		= rawurldecode($TS_TablesWP_Headers_Value);
						if ($table_shortcodes == "true") {
							$TS_TablesWP_Headers_Value	= do_shortcode($TS_TablesWP_Headers_Value);
						}
						array_push($TS_TablesWP_Headers_Labels, strip_tags($TS_TablesWP_Headers_Value));
					}
				} else {
					$TS_TablesWP_Headers_Rows			= array();
				}
				if (($footer_use == "true") && ($footer_rows > 0) && ($TS_TablesWP_Editor_Rows > 0)) {
					$TS_TablesWP_Footers_Rows			= array();
					if ($footer_rows > ($TS_TablesWP_Editor_Rows - $header_rows)) {
						$footer_rows					= ($TS_TablesWP_Editor_Rows - $header_rows);
					}
					for ($footer = 0; $footer < $footer_rows; $footer++) {
						array_push($TS_TablesWP_Footers_Rows, $TS_TablesWP_Editor_Rows - $footer - 1);
					}
					$TS_TablesWP_Footers_Rows 			= array_reverse($TS_TablesWP_Footers_Rows);
				} else {
					$TS_TablesWP_Footers_Rows			= array();
				}
				
				// Determine Merged Cells Data
				$TS_TablesWP_Merged_Print				= array();
				$TS_TablesWP_Merged_Skip				= array();
				$TS_TablesWP_Merged_Mirror				= array();
				$TS_TablesWP_Merged_Start				= 0;
				$TS_TablesWP_Merged_Loop				= 0;
				$TS_TablesWP_Merged_Attr				= "";
				foreach ($TS_TablesWP_Editor_Merged as $merged) {
					$TS_TablesWP_Merged_Start			= $merged->row * $TS_TablesWP_Editor_Columns + $merged->col;
					$TS_TablesWP_Merged_Vertical		= $TS_TablesWP_Merged_Start;
					$TS_TablesWP_Merged_Horizontal		= $TS_TablesWP_Merged_Start;
					array_push($TS_TablesWP_Merged_Print, $TS_TablesWP_Merged_Start);
					$TS_TablesWP_Merged_Mirror[$TS_TablesWP_Merged_Start] = $merged;
					for ($row = $merged->row; $row < $merged->rowspan + $merged->row; $row++) {
						for ($cols = $merged->col; $cols < $merged->colspan + $merged->col; $cols++) {
							$TS_TablesWP_Merged_Loop	= ($row * $TS_TablesWP_Editor_Columns) + $cols;
							if ($TS_TablesWP_Merged_Loop != $TS_TablesWP_Merged_Start) {
								array_push($TS_TablesWP_Merged_Skip, $TS_TablesWP_Merged_Loop);
							}
						}
					}			
				}
				unset($TS_TablesWP_Merged_Start);
				unset($TS_TablesWP_Merged_Loop);
				
				// 3rd Round of Contingency Checks
				if ($table_scope == "datatable") {
					if ($datatables_length == "custom") {
						$datatables_length				= $datatables_lengthcustom;
					}
					$datatables_lengthoptions			= explode(",", $datatables_lengthoptions);
					sort($datatables_lengthoptions);
					$datatable_lengthselect				= array();
					foreach ($datatables_lengthoptions as $option) {
						if ($option < ($TS_TablesWP_Editor_Rows - count($TS_TablesWP_Headers_Rows))) {
							if (!in_array($option, $datatable_lengthselect)) {
								array_push($datatable_lengthselect, $option);
							}
						}
					}
					if (!in_array($datatables_length, $datatable_lengthselect)) {
						array_push($datatable_lengthselect, $datatables_length);
					}
					sort($datatable_lengthselect);
					if (max($datatable_lengthselect) < $datatables_length) {
						$datatables_length				= max($datatable_lengthselect);
					}
					$datatable_lengthselect				= implode(",", $datatable_lengthselect);
				} else if ($table_scope == "footable") {
					if ($footable_length == "custom") {
						$footable_length				= $footable_lengthcustom;
					}
					$footable_lengthoptions				= explode(",", $footable_lengthoptions);
					sort($footable_lengthoptions);
					$footable_lengthselect				= array();
					foreach ($footable_lengthoptions as $option) {
						if ($option < ($TS_TablesWP_Editor_Rows - count($TS_TablesWP_Headers_Rows))) {
							if (!in_array($option, $footable_lengthselect)) {
								array_push($footable_lengthselect, $option);
							}
						}
					}
					if (!in_array($footable_length, $footable_lengthselect)) {
						array_push($footable_lengthselect, $footable_length);
					}
					sort($footable_lengthselect);
					if (max($footable_lengthselect) < $footable_length) {
						$footable_length				= max($footable_lengthselect);
					}
				} else if ($table_scope == "tabulator") {
					
				}
		
				// Replace HandsOnTables Classes
				$TS_TablesWP_Classes_ReferenceOld		= array(
					"color-", "background-", "htMiddle", "htTop", "htBottom", "htCenter", "htLeft", "htRight", "htJustify", "bold", "italic", "nodecoration", "linethrough", "overline", "underline"
				);
				$TS_TablesWP_Classes_ReferenceNew		= array(
					"ts-cell-fontcolor-", "ts-cell-background-", "ts-cell-vertical-middle", "ts-cell-vertical-top", "ts-cell-vertical-bottom", "ts-cell-horizontal-center", "ts-cell-horizontal-left", "ts-cell-horizontal-right", "ts-cell-horizontal-justify", "ts-cell-weight-bold", "ts-cell-style-italic", "ts-cell-decoration-none", "ts-cell-decoration-line", "ts-cell-decoration-over", "ts-cell-decoration-under",
				);		
				foreach ($TS_TablesWP_Editor_Meta as $meta) {
					preg_match_all('/(?<!\w)color-\S+/', $meta->className, $matches);
					if ((count($matches[0]) > 0) && (!in_array($matches[0][0], $TS_TablesWP_Editor_Colors))) {
						array_push($TS_TablesWP_Editor_Colors, $matches[0][0]);
					}
					preg_match_all('/(?<!\w)background-\S+/', $meta->className, $matches);
					if ((count($matches[0]) > 0) && (!in_array($matches[0][0], $TS_TablesWP_Editor_Backgrounds))) {
						array_push($TS_TablesWP_Editor_Backgrounds, $matches[0][0]);
					}
					$meta->className					= str_replace($TS_TablesWP_Classes_ReferenceOld, $TS_TablesWP_Classes_ReferenceNew, $meta->className);
					$meta->value						= rawurldecode($meta->value);
				}
				$numbers_vertical						= str_replace($TS_TablesWP_Classes_ReferenceOld, $TS_TablesWP_Classes_ReferenceNew, $numbers_vertical);
				$numbers_horizontal						= str_replace($TS_TablesWP_Classes_ReferenceOld, $TS_TablesWP_Classes_ReferenceNew, $numbers_horizontal);
				unset($TS_TablesWP_Classes_ReferenceOld);
				unset($TS_TablesWP_Classes_ReferenceNew);
				
				// Visual Composer Style Overrides
				if (function_exists('vc_shortcode_custom_css_class')) {
					$css_class 							= apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, ' ' . vc_shortcode_custom_css_class($css, ' '), 'TS_Advanced_Tables', $atts);
				} else {
					$css_class							= '';
				}
				
				// Generate Styles
				if ($inline == "false") {
					$styles .= '<style id="ts-advanced-tables-styles-' . $container_id . '" type="text/css">';
				}					
					$styles .= '#' . $container_id . '.ts-advanced-tables-container table.ts-advanced-tables-datatable {';
						if (($table_scope != "tablesaw") && ($table_scope != "footable") && ($table_scope != "datatable") && ($table_scope != "tabulator")) {
							if ($width_type == "percent") {
								$styles .= 'width: ' . $width_percent . '%;';
							} else if ($width_type == "maximum") {
								$styles .= 'width: 100%;';
								$styles .= 'max-width: ' . $width_maximum . 'px;';
							} else if ($width_type == "fixed") {
								$styles .= 'width: ' . $width_fixed . 'px;';							
							} else if ($width_type == "none") {
								$styles .= 'width: auto;';
							}
						} else {
							$styles .= 'width: 100% !important;';
						}
					$styles .= '}';
					if ($columns_widthlimit == "true") {
						$styles .= '#' . $container_id . ' table:not(.ts-advanced-tables-' . $responsive_type . '):not(.tablesaw-stack):not(.tablesaw-columntoggle) thead tr th:not(.control),';
						$styles .= '#' . $container_id . ' table:not(.ts-advanced-tables-' . $responsive_type . '):not(.tablesaw-stack):not(.tablesaw-columntoggle) tbody tr td:not(.control),';
						$styles .= '#' . $container_id . ' table:not(.ts-advanced-tables-' . $responsive_type . '):not(.tablesaw-stack):not(.tablesaw-columntoggle) tfoot tr th:not(.control) {';
							$styles .= 'min-width: ' . $columns_widthmin . 'px !important;';
							$styles .= 'max-width: ' . $columns_widthmax . 'px !important;';
						$styles .= '}';
					}
					if (($responsive_heighttype == "fixed") && ($table_scope != "tablesaw") && ($table_scope != "footable") && ($table_scope != "datatable") && (($responsive_type == "tiles") || ($responsive_type == "stack"))) {
						$styles .= '#' . $container_id . ' .ts-advanced-tables-wrapper.ts-advanced-tables-scroller {';
							$styles .= 'max-height: ' . $responsive_heightpixel . 'px !important;';
						$styles .= '}';
					}
					if ($table_scope == "datatable") {
						$styles .= '#' . $container_id . ' table thead tr th.control,';
						$styles .= '#' . $container_id . ' table tbody tr td.control,';
						$styles .= '#' . $container_id . ' table tfoot tr th.control {';
							$styles .= 'width: 40px !important;';
							$styles .= 'min-width: 40px !important;';
							$styles .= 'max-width: 40px !important;';
						$styles .= '}';
					} else if ($table_scope == "tablesaw") {
						if (($tablesaw_fixwidth > 0) && (count($TS_TablesWP_Headers_Persist) > 0)) {
							foreach ($TS_TablesWP_Headers_Persist as $i => $persistent) {
								array_push($TS_TablesWP_Headers_Styling, '#' . $container_id . ' table.tablesaw-swipe .tablesaw-cell-persist:nth-child(' . ($i + 1) . ')');
							}
							$styles .= implode(",", $TS_TablesWP_Headers_Styling);
							$styles .= ' {';
								$styles .= 'width: ' . $tablesaw_fixwidth . 'px !important;';
							$styles .= '}';
						}
					}
					foreach ($TS_TablesWP_Editor_Colors as $fontcolor) {
						$styles .= '#' . $container_id . ' .ts-cell-font' . $fontcolor . ' {';
							$styles .= 'color: #' . str_replace("color-", "", $fontcolor) . ' !important;';
						$styles .= '}';
					}
					foreach ($TS_TablesWP_Editor_Backgrounds as $background) {
						$styles .= '#' . $container_id . ' .ts-cell-' . $background . ' {';
							$styles .= 'background-color: #' . str_replace("background-", "", $background) . ' !important;';
						$styles .= '}';
					}
					if ($styling_customize == "true") {
						$styles .= '#' . $container_id . ' table thead tr th:not(.control) {';
							$styles .= 'font-size: ' . $styling_size_head . 'px;';
							$styles .= 'font-weight: ' . $styling_weight_head . ';';
							$styles .= 'text-transform: ' . $styling_transform_head . ';';
						$styles .= '}';
						$styles .= '#' . $container_id . ' table tbody tr td:not(.control) {';
							$styles .= 'font-size: ' . $styling_size_body . 'px;';
							$styles .= 'font-weight: ' . $styling_weight_body . ';';
							$styles .= 'text-transform: ' . $styling_transform_body . ';';
						$styles .= '}';
						$styles .= '#' . $container_id . ' table tfoot tr th:not(.control) {';
							$styles .= 'font-size: ' . $styling_size_foot . 'px;';
							$styles .= 'font-weight: ' . $styling_weight_foot . ';';
							$styles .= 'text-transform: ' . $styling_transform_foot . ';';
						$styles .= '}';
					}
				if ($inline == "false") {
					$styles .= '</style>';
				}
				if ($styles != "") {
					wp_add_inline_style('ts-extend-advancedtables', TS_TablesWP_MinifyCSS($styles));
				}
				unset($TS_TablesWP_Editor_Colors);
				unset($TS_TablesWP_Editor_Backgrounds);		
		
				// Table Attributes
				$TS_TablesWP_FormatDefaults				= 'data-default-numeric="' . $TS_TablesWP_Editor_Defaults->numericfull . '" data-default-currency="' . $TS_TablesWP_Editor_Defaults->currencyfull . '" data-default-percent="' . $TS_TablesWP_Editor_Defaults->percentfull . '" data-default-date="' . $TS_TablesWP_Editor_Defaults->datefull . '" data-default-time="' . $TS_TablesWP_Editor_Defaults->timefull . '" data-comment-date="' . $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_CommentsDates . '" data-comment-time="' . $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_CommentsTimes . '"';
				if ($table_formatting == "custom") {					
					// Format: Percent
					if ($numbers_decimals_percent > 0) {
						$numbers_percent				= '0.';
						for ($x = 0; $x < $numbers_decimals_percent; $x++) {
							$numbers_percent       		.= '0';
						}
					} else {
						$numbers_percent				= '0';
					}
					$numbers_percent           			.= ($numbers_space_percent == 'true' ? ' ' : '') . '%';
					// Format: Numeric
					if ($numbers_decimals_numeric > 0) {
						$numbers_numeric 				= '0,0.';
						for ($x = 0; $x < $numbers_decimals_numeric; $x++) {
							$numbers_numeric       		.= '0';
						}
					} else {
						$numbers_numeric				= '0,0';
					}
					// Format: Currency
					if ($numbers_decimals_currency > 0) {
						$numbers_currency 				= '0,0.';
						for ($x = 0; $x < $numbers_decimals_currency; $x++) {
							$numbers_currency       	.= '0';
						}
					} else {
						$numbers_currency 				= '0,0';
					}
					if ($numbers_placement == "prefix") {
						$numbers_currency				= "$" . ($numbers_space_currency == 'true' ? ' ' : '') . $numbers_currency;
					} else if ($numbers_placement == "postfix") {
						$numbers_currency				= $numbers_currency . ($numbers_space_currency == 'true' ? ' ' : '') . "$";
					}
					if (is_numeric($numbers_symbol)) {
						$numbers_symbol					= $TS_ADVANCED_TABLESWP->TS_TablesWP_Currency_HTML_Inverted[$numbers_symbol];
					}
					// Format: Time
					$numbers_time						= $numbers_time_hours . ':' . $numbers_time_minutes;
					if ($numbers_time_seconds != '') {
						$numbers_time              		.= ':' . $numbers_time_seconds;
					}
					if (($numbers_time_hours == "hh") || ($numbers_time_hours == "h")) {
						$numbers_time              		.= ' ' . $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTimeMeridiem;
					}
					// Create Data Attributes
					$TS_TablesWP_FormatCustoms			= 'data-format-savemeta="' . $TS_TablesWP_Editor_SaveMeta . '" data-format-source="' . $table_formatting . '" data-format-vertical="' . $numbers_vertical . '" data-format-horizontal="' . $numbers_horizontal . '" data-format-locale="' . $numbers_locale . '"';
					$TS_TablesWP_FormatCustoms			.= ' data-format-symbol="' . $numbers_symbol . '" data-format-date="' . $numbers_date . '" data-format-time="' . $numbers_time . '"';
					$TS_TablesWP_FormatCustoms			.= ' data-format-numeric="' . $numbers_numeric . '" data-format-currency="' . $numbers_currency . '" data-format-percent="' . $numbers_percent . '"';	
				} else {
					$TS_TablesWP_FormatCustoms			= 'data-format-savemeta="' . $TS_TablesWP_Editor_SaveMeta . '" data-format-source="' . $table_formatting . '"';
				}
				if ($table_scope == "datatable") {
					$TS_TablesWP_DataTable				= 'data-datatable-use="true" data-datatable-columns="' . $TS_TablesWP_Editor_Columns . '" data-datatable-rows="' . $TS_TablesWP_Editor_Rows . '" data-datatable-search="' . $datatables_search . '" data-datatable-info="' . $datatables_info . '" data-datatable-togglerow="' . $datatables_togglerow . '"';
					$TS_TablesWP_DataTable 				.= ' data-datatable-pages="' . $datatables_pages . '" data-datatable-pagetype="' . $datatables_pagetype  . '" data-datatable-pageselect="' . $datatable_lengthselect . '" data-datatable-length="' . $datatables_length . '"';
					$TS_TablesWP_DataTable 				.= ' data-datatable-sort="' . $datatables_sort . '" data-datatable-initial="' . $datatables_inital . '" data-datatable-order="' . $datatables_order . '"';
					$TS_TablesWP_DataTable 				.= ' data-datatable-topfixed="' . $datatables_topfixed . '" data-datatable-topoffset="' . $datatables_topoffset . '" data-datatable-bottomfixed="' . $datatables_bottomfixed . '" data-datatable-bottomoffset="' . $datatables_bottomoffset . '"';
					$TS_TablesWP_DataTable 				.= ' data-datatable-buttons="' . $datatables_buttons . '" data-datatable-printshow="' . $datatables_printshow . '" data-datatable-pdfshow="' . $datatables_pdfshow . '" data-datatable-csvshow="' . $datatables_csvshow . '" data-datatable-excelshow="' . $datatables_excelshow . '" data-datatable-copyshow="' . $datatables_copyshow . '"';
					$TS_TablesWP_DataTable 				.= ' data-datatable-printtext="' . $datatables_printtext . '" data-datatable-pdftext="' . $datatables_pdftext . '" data-datatable-csvtext="' . $datatables_csvtext . '" data-datatable-exceltext="' . $datatables_exceltext . '" data-datatable-copytext="' . $datatables_copytext . '"';
					$TS_TablesWP_DataTable				.= ' data-datatable-textprocessing="' . $datatable_text_processing . '" data-datatable-textlengthmenu="' . $datatable_text_lengthmenu . '" data-datatable-textlengthall="' . $datatable_text_lengthall . '" data-datatable-textinfomain="' . $datatable_text_infomain . '" data-datatable-textinfoempty="' . $datatable_text_infoempty . '" data-datatable-textinfofiltered="' . $datatable_text_infofiltered . '"';
					$TS_TablesWP_DataTable				.= ' data-datatable-textsearch="' . $datatable_text_search . '" data-datatable-textplaceholder="' . $datatable_text_placeholder . '" data-datatable-textzerorecords="' . $datatable_text_zerorecords . '"';
					$TS_TablesWP_DataTable				.= ' data-datatable-textfirst="' . $datatable_text_first . '" data-datatable-textprevious="' . $datatable_text_previous . '" data-datatable-textnext="' . $datatable_text_next . '" data-datatable-textlast="' . $datatable_text_last . '"';
					$TS_TablesWP_TableSaw				= 'data-tablesaw-use="false"';
					$TS_TablesWP_FooTable				= 'data-footable-use="false"';
					$TS_TablesWP_Tabulator				= 'data-tabulator-use="false"';
					$TS_TablesWP_ClassesTheme			= 'ts-datatables-theme-' . $datatables_theme;
					$TS_TablesWP_ClassesTable			= 'display responsive';
					$TS_TablesWP_ClassesWraper			= 'ts-datatables-container';
				} else if ($table_scope == "tablesaw") {
					$TS_TablesWP_DataTable				= 'data-datatable-use="false"';
					$TS_TablesWP_FooTable				= 'data-footable-use="false"';
					$TS_TablesWP_Tabulator				= 'data-tabulator-use="false"';
					$TS_TablesWP_TableSaw				= 'data-tablesaw-use="true" data-tablesaw-columns="' . $TS_TablesWP_Editor_Columns . '" data-tablesaw-rows="' . $TS_TablesWP_Editor_Rows . '" data-tablesaw-mode="' . $tablesaw_scope . '" data-tablesaw-initial="' . $tablesaw_initial . '" data-tablesaw-order="' . $tablesaw_order . '"';
					$TS_TablesWP_TableSaw				.= ' data-tablesaw-mode-exclude="' . $tablesaw_modeexclude . '" data-tablesaw-persist-width="' . $tablesaw_fixwidth . '" data-tablesaw-string-modes="' . $tablesaw_text_swipe . ',' . $tablesaw_text_toggle . ',' . $tablesaw_text_stack . '"';
					$TS_TablesWP_TableSaw				.= ' data-tablesaw-string-columns="' . $tablesaw_text_reponsive . '" data-tablesaw-string-button="'  .$tablesaw_text_columns . '" data-tablesaw-string-error="' . $tablesaw_text_error . '" data-tablesaw-string-sort="' . $tablesaw_text_sort . '"';
					if ($tablesaw_sort =="true") {
						$TS_TablesWP_TableSaw			.= ' data-tablesaw-sortable';
						if ($tablesaw_sortswitch == "true") {
							$TS_TablesWP_TableSaw		.= ' data-tablesaw-sortable-switch';
						}
					}
					if ($tablesaw_minimap == "true") {
						$TS_TablesWP_TableSaw			.= ' data-tablesaw-minimap';
					}
					if ($tablesaw_modeswitch == "true") {
						$TS_TablesWP_TableSaw			.= ' data-tablesaw-mode-switch';
					}
					$TS_TablesWP_ClassesTheme			= 'ts-datatables-theme-tablesaw';
					$TS_TablesWP_ClassesTable			= 'tablesaw tablesaw-' . $tablesaw_scope;
					$TS_TablesWP_ClassesWraper			= 'ts-tablesaw-container';
				} else if ($table_scope == "footable") {
					$TS_TablesWP_DataTable				= 'data-datatable-use="false"';
					$TS_TablesWP_TableSaw				= 'data-tablesaw-use="false"';
					$TS_TablesWP_Tabulator				= 'data-tabulator-use="false"';
					$TS_TablesWP_FooTable				= ' data-footable-use="true" data-footable-columns="' . $TS_TablesWP_Editor_Columns . '" data-footable-rows="' . $TS_TablesWP_Editor_Rows . '" data-footable-nosearch="' . $footable_nosearch . '" data-footable-footeruse="' . $footer_use . '" data-footable-footerrows="' . $footer_rows . '"';
					$TS_TablesWP_FooTable				.= ' data-sorting="' . $footable_sort . '" data-footable-nosort="' . $footable_noorder . '" data-footable-initial="' . $footable_initial . '" data-footable-order="' . $footable_order .'"';
					$TS_TablesWP_FooTable				.= ' data-paging="' . $footable_pages . '" data-paging-size="' . $footable_length . '" data-paging-limit="5" data-paging-select="' . implode(",", $footable_lengthselect) . '" data-paging-position="' . $footable_pagesspot . '" data-paging-count-format=" ' . $footable_countformat . '"';
					$TS_TablesWP_FooTable				.= ' data-filtering="' . $footable_search . '" data-filter-min="3" data-filter-position="' . $footable_searchspot . '" data-filter-placeholder="' . $footable_placeholder . '" data-filter-empty="' . $footable_noresults . '"';
					$TS_TablesWP_FooTable				.= ' data-show-toggle="true" data-toggle-column="first" data-cascade="' . $footable_cascade . '" data-stop-propagation="false" data-use-parent-width="true"';
					$TS_TablesWP_FooTable				.= ' data-footable-noview="' . $footable_notever . '" data-footable-collapsed="' . $footable_collapsed . '" data-footable-breaktiny="' . $footable_breakxs . '" data-footable-breaksmall="' . $footable_breaksm . '" data-footable-breakmedium="' . $footable_breakmd . '" data-footable-breaklarge="' . $footable_breaklg . '"';
					$TS_TablesWP_FooTable				.= ' data-state="false" data-state-filtering="true" data-state-paging="true" data-state-sorting="true"';
					$TS_TablesWP_ClassesTheme			= 'ts-footable-theme-footable';
					$TS_TablesWP_ClassesTable			= 'table';
					$TS_TablesWP_ClassesWraper			= 'ts-footable-container';
				} else if ($table_scope == "tabulator") {
					$TS_TablesWP_DataTable				= 'data-datatable-use="false"';
					$TS_TablesWP_TableSaw				= 'data-tablesaw-use="false"';
					$TS_TablesWP_FooTable				= 'data-footable-use="false"';
					$TS_TablesWP_Tabulator				= 'data-tabulator-use="true"';
					$TS_TablesWP_ClassesTheme			= 'ts-advanced-tables-theme-tabulator';
					$TS_TablesWP_ClassesTable			= '';
					$TS_TablesWP_ClassesWraper			= '';
				} else {
					$TS_TablesWP_DataTable				= 'data-datatable-use="false"';
					$TS_TablesWP_TableSaw				= 'data-tablesaw-use="false"';
					$TS_TablesWP_FooTable				= 'data-footable-use="false"';
					$TS_TablesWP_Tabulator				= 'data-tabulator-use="false"';
					$TS_TablesWP_ClassesTheme			= 'ts-advanced-tables-theme-' . $table_scope;
					$TS_TablesWP_ClassesTable			= '';
					$TS_TablesWP_ClassesWraper			= '';
				}
				$TS_TablesWP_DataCell					= '';
				
				// Responsive Attributes
				$TS_TableVC_Responsive					= 'data-responsive-type="ts-advanced-tables-' . $responsive_type . '" data-responsive-switch="' . $responsive_switch . '" data-responsive-active="false" data-responsive-heighttype="' . $responsive_heighttype . '" data-responsive-heightpixel="' . $responsive_heightpixel . '" data-responsive-heightpercent="' . $responsive_heightpercent . '"';
		
				// Breakpoint Attributes
				if ($table_scope == "datatable") {
					$TS_TablesWP_Breakpoints			= 'data-breakpoint-desktop="' . $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DatatableDesktop . '" data-breakpoint-tabletl="' . $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DatatableTabletL . '" data-breakpoint-tabletp="' . $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DatatableTabletP . '" data-breakpoint-mobilel="' . $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DatatableMobileL . '" data-breakpoint-mobilep="' . $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_DatatableMobileP . '"';
				} else if ($table_scope == "footable") {
					$TS_TablesWP_Breakpoints			= 'data-breakpoint-large="' . $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FootableLarge . '" data-breakpoint-medium="' . $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FootableMedium . '" data-breakpoint-small="' . $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FootableSmall . '" data-breakpoint-tiny="' . $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FootableTiny . '"';
				} else {
					$TS_TablesWP_Breakpoints			= '';
				}
		
				// Generate Final Output
				$output .= '<div id="' . $container_id . '" class="ts-advanced-tables-container ts-advanced-tables-processing ' . $css_class . ' ' . $el_class . ' ' . $TS_TablesWP_ClassesWraper . ' ' . (($table_scope == "datatable") ? $TS_TablesWP_ClassesTheme : "") . '">';
					// Custom Style Rules
					if (($styles != "") && ($inline == "false")) {
						$output .= TS_TablesWP_MinifyCSS($styles);
					}
					// Frontend Editor Message
					if ($TS_ADVANCED_TABLESWP->TS_TablesWP_VisualComposer_Frontend == "true") {
						$output .= '<div id="ts-advanced-tables-frontend-message-' . $randomizer . '" class="ts-advanced-tables-frontend-message">';
							$output .= __('For performance and compatibility reasons, the table will be rendered without any advanced features and number format processing when using the WP Bakery Page Builder (formerly Visual Composer) frontend editor. Table number of rows within the table body will be limited to a maximum of 10 rows.', 'ts_visual_composer_extend');
						$output .= '</div>';
					}
					// Table Name
					if (($show_name == "true") && ($TS_TablesWP_Editor_Name != "")) {
						$output .= '<div id="ts-advanced-tables-name-' . $randomizer . '" class="ts-advanced-tables-name">';
							$output .= $TS_TablesWP_Editor_Name;
						$output .= '</div>';
					}
					unset($TS_TablesWP_Editor_Name);
					// Table Information
					if (($show_info == "true") && ($TS_TablesWP_Editor_Info != "")) {
						$output .= '<div id="ts-advanced-tables-info-' . $randomizer . '" class="ts-advanced-tables-info">';
							$output .= $TS_TablesWP_Editor_Info;
						$output .= '</div>';
					}
					unset($TS_TablesWP_Editor_Info);
					// Table Preloader
					if (($TS_ADVANCED_TABLESWP->TS_TablesWP_VisualComposer_Frontend == "false") && ($preloader_use == "true")) {
						$output .= '<div id="ts-advanced-tables-preloader-wrapper-' . $randomizer . '" class="ts-advanced-tables-preloader-wrapper" style="background: ' . $preloader_background . ';">';
							$output .= TS_TablesWP_CreatePreloaderCSS("ts-advanced-tables-preloader-"  . $randomizer, "", $preloader_style, "false");
						$output .= '</div>';
					}
					// Table Element
					$output .= '<div id="ts-advanced-tables-wrapper-' . $randomizer . '" class="ts-advanced-tables-wrapper">';
						$output .= '<table id="ts-advanced-tables-datatable-' . $randomizer . '" class="ts-advanced-tables-datatable ' . (($table_scope == "datatable") ? "" : $TS_TablesWP_ClassesTheme) . ' ' . $TS_TablesWP_ClassesTable . '" data-preloader="ts-advanced-tables-preloader-wrapper-' . $randomizer . '" ' . $TS_TablesWP_Format_Table . ' ' . $TS_TablesWP_FormatDefaults . ' ' . $TS_TablesWP_FormatCustoms . ' ' . $TS_TablesWP_Breakpoints . ' ' . $TS_TableVC_Responsive . ' ' . $TS_TablesWP_DataTable . ' ' . $TS_TablesWP_TableSaw . ' ' . $TS_TablesWP_FooTable . ' ' . $TS_TablesWP_Tabulator . '>';
							if ($header_use == "true") {
								$output .= '<thead>';
									if (($table_scope == "footable") && ($footable_pages == "true") && (strpos($footable_lengthmenu, "{LM}") > -1) && ($TS_ADVANCED_TABLESWP->TS_TablesWP_VisualComposer_Frontend == "false")) {
										$output .= '<tr class="footable-paginator">';
											$output .= '<td colspan="' . $TS_TablesWP_Editor_Columns . '">';
												$output .= '<label>';
													$output .= TS_TablesWP_GetStringBefore($footable_lengthmenu, "{LM}");
													$output .= '<select id="footable-paginator-select-' . $randomizer . '" class="footable-paginator-select form-control">';
														foreach ($footable_lengthselect as $option) {
															if ($option < ($TS_TablesWP_Editor_Rows - count($TS_TablesWP_Headers_Rows))) {
																$output .= '<option value="' . $option . '" ' . (selected($footable_length, $option, false)) . '>' . $option . '</option>';
															}
														}
														$output .= '<option value="' . ($TS_TablesWP_Editor_Rows - count($TS_TablesWP_Headers_Rows)) . '" ' . (selected($footable_length, ($TS_TablesWP_Editor_Rows - count($TS_TablesWP_Headers_Rows)))) . '>' . $footable_lengthall . '</option>';
													$output .= '</select>';
													$output .= TS_TablesWP_GetStringAfter($footable_lengthmenu, "{LM}");
												$output .= '</label>';
											$output .= '</td>';
										$output .= '</tr>';
									}
									foreach ($TS_TablesWP_Headers_Rows as $header) {
										if (in_array($header + 1, $TS_TablesWP_Exlude_Rows)) {
											for ($col = 0; $col < $TS_TablesWP_Editor_Columns; $col++) {
												$TS_TablesWP_Editor_CountCells++;
											}
											$TS_TablesWP_Editor_CountRows++;
											continue;
										}
										$output .= '<tr>';
											if ($table_scope == "datatable") {
												$output .= '<th class="control" data-cell-scope="control" data-priority="1" data-orderable="false" data-searchable="false" style="display: none;"></th>';
											}
											for ($col = 0; $col < $TS_TablesWP_Editor_Columns; $col++) {
												if (in_array($col + 1, $TS_TablesWP_Exlude_Columns)) {
													$TS_TablesWP_Editor_CountCells++;
													continue;
												}
												if (in_array($TS_TablesWP_Editor_CountCells, $TS_TablesWP_Merged_Skip)) {
													if ($table_merged == "true") {
														$TS_TablesWP_Merged_Attr 				= 'style="display: none; width: 0; height: 0;"';
													} else {
														$TS_TablesWP_Editor_CountCells++;
														continue;
													}
												} else if (in_array($TS_TablesWP_Editor_CountCells, $TS_TablesWP_Merged_Print)) {
													$TS_TablesWP_Merged_Attr 					= 'colspan="' . ($TS_TablesWP_Merged_Mirror[$TS_TablesWP_Editor_CountCells]->colspan) . '" rowspan="' . ($TS_TablesWP_Merged_Mirror[$TS_TablesWP_Editor_CountCells]->rowspan) . '"';
												} else {
													$TS_TablesWP_Merged_Attr 					= '';
												}
												// Extract Cell Data
												$TS_TablesWP_Cell_Value							= (isset($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->value) ? $TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->value : '');
												if ($table_shortcodes == "true") {
													$TS_TablesWP_Cell_Value						= do_shortcode($TS_TablesWP_Cell_Value);
												}
												if (($table_formatting == "custom") && ($numbers_header == "true") && (!in_array($col + 1, $TS_TablesWP_Format_Exclude))) {
													if ((in_array($col + 1, $TS_TablesWP_Format_Date)) && ($TS_TablesWP_Cell_Value == strip_tags($TS_TablesWP_Cell_Value)) && (preg_match("/\\d/", $TS_TablesWP_Cell_Value) > 0)) {
														$TS_TablesWP_Cell_Type					= "date";
														$TS_TablesWP_Cell_Format				= $numbers_date;
													} else if ((in_array($col + 1, $TS_TablesWP_Format_Time)) && ($TS_TablesWP_Cell_Value == strip_tags($TS_TablesWP_Cell_Value)) && (preg_match("/\\d/", $TS_TablesWP_Cell_Value) > 0)) {
														$TS_TablesWP_Cell_Type					= "time";
														$TS_TablesWP_Cell_Format				= $numbers_time;
													} else if ((in_array($col + 1, $TS_TablesWP_Format_Number)) && (is_numeric($TS_TablesWP_Cell_Value))) {
														$TS_TablesWP_Cell_Type					= "number";
														$TS_TablesWP_Cell_Format				= $numbers_numeric;
													} else if ((in_array($col + 1, $TS_TablesWP_Format_Currency)) && (is_numeric($TS_TablesWP_Cell_Value))) {
														$TS_TablesWP_Cell_Type					= "currency";
														$TS_TablesWP_Cell_Format				= $numbers_currency;
													} else if ((in_array($col + 1, $TS_TablesWP_Format_Percent)) && (is_numeric($TS_TablesWP_Cell_Value))) {
														$TS_TablesWP_Cell_Type					= "percent";
														$TS_TablesWP_Cell_Format				= $numbers_percent;														
													} else if ((in_array($col + 1, $TS_TablesWP_Format_Natural_Simple)) && ($table_scope == "datatable")) {
														$TS_TablesWP_Cell_Type					= "text";
														$TS_TablesWP_Cell_Format				= "text";
													} else if ((in_array($col + 1, $TS_TablesWP_Format_Natural_NoHTML)) && ($table_scope == "datatable")) {
														$TS_TablesWP_Cell_Type					= "text";
														$TS_TablesWP_Cell_Format				= "text";
													} else if ((in_array($col + 1, $TS_TablesWP_Format_Natural_NoCase)) && ($table_scope == "datatable")) {
														$TS_TablesWP_Cell_Type					= "text";
														$TS_TablesWP_Cell_Format				= "text";
													} else if ((in_array($col + 1, $TS_TablesWP_Format_NumberHTML)) && ($table_scope == "datatable")) {
														$TS_TablesWP_Cell_Type					= "text";
														$TS_TablesWP_Cell_Format				= "text";
													} else {
														$TS_TablesWP_Cell_Type					= "text";
														$TS_TablesWP_Cell_Format				= "text";
													}
													$TS_TablesWP_Cell_Locale					= $numbers_locale;
													$TS_TablesWP_Cell_Symbol					= $numbers_symbol;
													$TS_TablesWP_Cell_Classes					= trim($numbers_vertical . ' ' . $numbers_horizontal);
												} else {
													$TS_TablesWP_Cell_Type						= (isset($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->formatType) ? $TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->formatType : '');
													$TS_TablesWP_Cell_Format					= (isset($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->format) ? $TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->format : '');												
													$TS_TablesWP_Cell_Locale					= (isset($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->locale) ? $TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->locale : '');
													$TS_TablesWP_Cell_Symbol					= (isset($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->symbol) ? $TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->symbol : '');
													$TS_TablesWP_Cell_Classes					= (isset($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->className) ? $TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->className : '');
												}
												$TS_TablesWP_Cell_Comment						= '';
												$TS_TablesWP_Cell_Override						= '';
												$TS_TablesWP_Cell_Indicator						= '';
												if ((isset($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->comment)) && ($show_comments == "true")) {
													if ($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->comment != "") {
														if (($comments_date_check == "true") && (substr($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->comment, 0, 4) === "ddd:")) {
															$TS_TablesWP_Cell_Override			= trim(substr($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->comment, 4));
															if ($comments_date_tooltip == "true") {
																$TS_TablesWP_Cell_Comment		= $TS_TablesWP_Cell_Override;
															}
														} else if (($comments_time_check == "true") && (substr($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->comment, 0, 4) === "ttt:")) {
															$TS_TablesWP_Cell_Override			= trim(substr($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->comment, 4));
															if ($comments_time_tooltip == "true") {
																$TS_TablesWP_Cell_Comment		= $TS_TablesWP_Cell_Override;
															}
														} else {
															$TS_TablesWP_Cell_Comment			= $TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->comment;
														}
														if ($TS_TablesWP_Cell_Comment != '') {
															$TS_TablesWP_Cell_Comment			= $TS_TablesWP_TooltipContent . ' data-krauttipster-text="' . (base64_encode($TS_TablesWP_Cell_Comment)) . '"';
															if ($show_indicator == "true") {
																$TS_TablesWP_Cell_Indicator		= 'ts-advanced-tables-tooltip';
															}
														}
													}
												}
												if (($TS_TablesWP_Cell_Type == "text") || ($TS_TablesWP_Cell_Type == "html") || ($TS_TablesWP_Cell_Type == "")) {
													$TS_TablesWP_Cell_Base64					= base64_encode($TS_TablesWP_Cell_Value);
												} else {
													$TS_TablesWP_Cell_Base64					= $TS_TablesWP_Cell_Value;
												}
												if ($table_scope == "datatable") {
													$TS_TablesWP_Headers_Collapsed 				= (in_array($col + 1, $TS_TablesWP_Headers_NoShow) ? "none" : "");
													$TS_TablesWP_Headers_Hidden					= (in_array($col + 1, $TS_TablesWP_Headers_NoView) ? "never" : "");
													$TS_TablesWP_Headers_Always					= (in_array($col + 1, $TS_TablesWP_Headers_NoHide) ? "all" : "");
													$TS_TablesWP_Headers_Sortable 				= 'data-orderable="' . (in_array($col + 1, $TS_TablesWP_Headers_NoSort) ? "false" : (in_array($col + 1, $TS_TablesWP_Headers_NoView) ? "false" : "true")) . '"';
													$TS_TablesWP_Headers_Searchable 			= 'data-searchable="' . (in_array($col + 1, $TS_TablesWP_Headers_NoSearch) ? "false" : (in_array($col + 1, $TS_TablesWP_Headers_NoView) ? "false" : "true")) . '"';
													$TS_TablesWP_Headers_Desktop				= (in_array($col + 1, $TS_TablesWP_Headers_NoDesktop) ? "not-desktop" : "");
													$TS_TablesWP_Headers_Tablet					= (in_array($col + 1, $TS_TablesWP_Headers_NoTablet) ? "not-tablet" : "");
													$TS_TablesWP_Headers_Tablet_L				= (in_array($col + 1, $TS_TablesWP_Headers_NoTablet_L) ? "not-tablet-l" : "");
													$TS_TablesWP_Headers_Tablet_P				= (in_array($col + 1, $TS_TablesWP_Headers_NoTablet_P) ? "not-tablet-p" : "");
													$TS_TablesWP_Headers_Mobile					= (in_array($col + 1, $TS_TablesWP_Headers_NoMobile) ? "not-mobile" : "");
													$TS_TablesWP_Headers_Mobile_L				= (in_array($col + 1, $TS_TablesWP_Headers_NoMobile_L) ? "not-mobile-l" : "");
													$TS_TablesWP_Headers_Mobile_P				= (in_array($col + 1, $TS_TablesWP_Headers_NoMobile_P) ? "not-mobile-p" : "");
												} else if ($table_scope == "tablesaw") {
													$TS_TablesWP_Headers_Sortable				= (in_array($col + 1, $TS_TablesWP_Headers_NoSort) ? '' : 'data-tablesaw-sortable-col') . (($col + 1 == $tablesaw_initial) ? ' data-tablesaw-sortable-default-col' : '') . '';
													$TS_TablesWP_Headers_Priority				= 'data-tablesaw-priority="' . (in_array($col + 1, $TS_TablesWP_Headers_Persist) ? 'persist' : ($col + 1)) . '"';
													$TS_TablesWP_Headers_Others					= (($col + 1 == $tablesaw_initial) ? 'tablesaw-sortable-' . $tablesaw_order : '');
												} else if ($table_scope == "footable") {
													$TS_TablesWP_Headers_Sortable				= (in_array($col + 1, $TS_TablesWP_Headers_NoSort) ? 'data-sortable="false"' : 'data-sortable="true"') . (($col + 1 == $footable_initial) ? ' data-sorted="true" data-direction="' . $footable_order . '"' : '');
													$TS_TablesWP_Headers_Searchable				= (in_array($col + 1, $TS_TablesWP_Headers_NoSearch) ? 'data-filterable="false"' : 'data-filterable="true"');
													$TS_TablesWP_Headers_Breakpoints			= array();
													if (in_array($col + 1, $TS_TablesWP_Headers_NoView)) {
														$TS_TablesWP_Headers_Viewable			= 'data-visible="false"';
													} else {											
														if ((count($TS_TablesWP_Headers_NoShow) == 0) && (count($TS_TablesWP_Headers_NotLarge) == 0) && (count($TS_TablesWP_Headers_NotMedium) == 0) && (count($TS_TablesWP_Headers_NotSmall) == 0) && (count($TS_TablesWP_Headers_NotExtra) == 0)) {
															$TS_TablesWP_Headers_Breakpoints 	= array();
														} else if (in_array($col + 1, $TS_TablesWP_Headers_NoShow)) {
															$TS_TablesWP_Headers_Breakpoints	= array('all', 'lg', 'md', 'sm', 'xs');
														} else {
															if (in_array($col + 1, $TS_TablesWP_Headers_NotLarge)) {
																array_push($TS_TablesWP_Headers_Breakpoints, "lg");
															}
															if (in_array($col + 1, $TS_TablesWP_Headers_NotMedium)) {
																array_push($TS_TablesWP_Headers_Breakpoints, "md");
															}
															if (in_array($col + 1, $TS_TablesWP_Headers_NotSmall)) {
																array_push($TS_TablesWP_Headers_Breakpoints, "sm");
															}
															if (in_array($col + 1, $TS_TablesWP_Headers_NotExtra)) {
																array_push($TS_TablesWP_Headers_Breakpoints, "xs");
															}
														}
														if (count($TS_TablesWP_Headers_Breakpoints) > 0) {
															$TS_TablesWP_Headers_Viewable		= 'data-breakpoints="' . (implode(" ", $TS_TablesWP_Headers_Breakpoints)) . '"';
														} else {
															$TS_TablesWP_Headers_Viewable		= '';
														}
													}
												} else if ($table_scope == "tabulator") {
													if (in_array($col + 1, $TS_TablesWP_Headers_Tabulator)) {
														$TS_TablesWP_Headers_Priority			= 'tabulator-responsive="' . (array_search($col + 1, $TS_TablesWP_Headers_Tabulator) + 1) . '"';
													}
												}
												if ($table_scope == "datatable") {
													if (in_array($col + 1, $TS_TablesWP_Format_HTML)) {
														$TS_TablesWP_Format_Callback			= 'data-type="html"';
													} else if (in_array($col + 1, $TS_TablesWP_Format_Date)) {
														$TS_TablesWP_Format_Callback			= 'data-type="num"';
													} else if (in_array($col + 1, $TS_TablesWP_Format_Time)) {
														$TS_TablesWP_Format_Callback			= 'data-type="html"';
													} else if (in_array($col + 1, $TS_TablesWP_Format_Number)) {
														$TS_TablesWP_Format_Callback			= 'data-type="num"';
													} else if (in_array($col + 1, $TS_TablesWP_Format_Currency)) {
														$TS_TablesWP_Format_Callback			= 'data-type="num"';
													} else if (in_array($col + 1, $TS_TablesWP_Format_Percent)) {
														$TS_TablesWP_Format_Callback			= 'data-type="num"';														
													} else if (in_array($col + 1, $TS_TablesWP_Format_Natural_Simple)) {
														$TS_TablesWP_Format_Callback			= 'data-type="natural"';
													} else if (in_array($col + 1, $TS_TablesWP_Format_Natural_NoHTML)) {
														$TS_TablesWP_Format_Callback			= 'data-type="natural-nohtml"';
													} else if (in_array($col + 1, $TS_TablesWP_Format_Natural_NoCase)) {
														$TS_TablesWP_Format_Callback			= 'data-type="natural-nocase"';
													} else if (in_array($col + 1, $TS_TablesWP_Format_NumberHTML)) {
														$TS_TablesWP_Format_Callback			= 'data-type="num-html"';	
													} else {
														$TS_TablesWP_Format_Callback			= 'data-type="html"';
													}
												} else if ($table_scope == "footable") {
													if (in_array($col + 1, $TS_TablesWP_Format_HTML)) {
														$TS_TablesWP_Format_Callback			= 'data-type="html" data-footable-callback="html"';
													} else if (in_array($col + 1, $TS_TablesWP_Format_Date)) {
														$TS_TablesWP_Format_Callback			= 'data-type="html" data-footable-callback="date"';
													} else if (in_array($col + 1, $TS_TablesWP_Format_Time)) {
														$TS_TablesWP_Format_Callback			= 'data-type="html" data-footable-callback="time"';
													} else if (in_array($col + 1, $TS_TablesWP_Format_Number)) {
														$TS_TablesWP_Format_Callback			= 'data-type="html" data-footable-callback="number"';
													} else if (in_array($col + 1, $TS_TablesWP_Format_Currency)) {
														$TS_TablesWP_Format_Callback			= 'data-type="html" data-footable-callback="currency"';
													} else if (in_array($col + 1, $TS_TablesWP_Format_Percent)) {
														$TS_TablesWP_Format_Callback			= 'data-type="html" data-footable-callback="percent"';
													} else {
														$TS_TablesWP_Format_Callback			= 'data-type="html" data-footable-callback=""';
													}
												} else if ($table_scope == "tablesaw") {
													$TS_TablesWP_Headers_Others					.= ' tablesaw-callback-sort';
													if (in_array($col + 1, $TS_TablesWP_Format_HTML)) {
														$TS_TablesWP_Format_Callback			= 'data-sortable-numeric="false"';
														$TS_TablesWP_Headers_Others				.= ' tablesaw-type-html';
													} else if (in_array($col + 1, $TS_TablesWP_Format_Date)) {
														$TS_TablesWP_Format_Callback			= 'data-sortable-numeric="true"';
														$TS_TablesWP_Headers_Others				.= ' tablesaw-type-date';
													} else if (in_array($col + 1, $TS_TablesWP_Format_Time)) {
														$TS_TablesWP_Format_Callback			= 'data-sortable-numeric="false"';
														$TS_TablesWP_Headers_Others				.= ' tablesaw-type-time';
													} else if (in_array($col + 1, $TS_TablesWP_Format_Number)) {
														$TS_TablesWP_Format_Callback			= 'data-sortable-numeric="true"';
														$TS_TablesWP_Headers_Others				.= ' tablesaw-type-number';
													} else if (in_array($col + 1, $TS_TablesWP_Format_Currency)) {
														$TS_TablesWP_Format_Callback			= 'data-sortable-numeric="true"';
														$TS_TablesWP_Headers_Others				.= ' tablesaw-type-currency';
													} else if (in_array($col + 1, $TS_TablesWP_Format_Percent)) {
														$TS_TablesWP_Format_Callback			= 'data-sortable-numeric="true"';
														$TS_TablesWP_Headers_Others				.= ' tablesaw-type-percent';
													} else {
														$TS_TablesWP_Format_Callback			= 'data-sortable-numeric="false"';
														$TS_TablesWP_Headers_Others				.= ' tablesaw-type-html';
													}
												} else if ($table_scope == "tabulator") {
													$TS_TablesWP_Format_Callback				= '';
												} else {
													$TS_TablesWP_Format_Callback				= '';
												}
												$output .= '<th ' . $TS_TablesWP_Headers_Viewable . ' ' . $TS_TablesWP_Format_Callback . ' data-cell-scope="col" scope="col" ' . $TS_TablesWP_Headers_Sortable . ' ' . $TS_TablesWP_Headers_Searchable . ' ' . $TS_TablesWP_Headers_Priority . ' class="ts-advanced-tables-cell ' . $TS_TablesWP_Cell_Classes . ' ' .$TS_TablesWP_Cell_Indicator . ' ' . $TS_TablesWP_Headers_Collapsed . ' ' . $TS_TablesWP_Headers_Hidden . ' ' . $TS_TablesWP_Headers_Always . ' ' . $TS_TablesWP_Headers_Desktop . ' ' . $TS_TablesWP_Headers_Tablet . ' ' . $TS_TablesWP_Headers_Tablet_L . ' ' . $TS_TablesWP_Headers_Tablet_P . ' ' . $TS_TablesWP_Headers_Mobile . ' ' . $TS_TablesWP_Headers_Mobile_L . ' ' . $TS_TablesWP_Headers_Mobile_P . ' ' . $TS_TablesWP_Headers_Others . '" ' . $TS_TablesWP_Merged_Attr . ' data-cell-type="' . $TS_TablesWP_Cell_Type . '" data-cell-format="' . $TS_TablesWP_Cell_Format . '" data-cell-column="' . ($col + 1) . '" data-cell-row="' . ($TS_TablesWP_Editor_CountRows + 1) . '" data-cell-count="' . $TS_TablesWP_Editor_CountCells . '" data-cell-value="' . $TS_TablesWP_Cell_Base64 . '" data-cell-override="' . $TS_TablesWP_Cell_Override . '" data-cell-locale="' . $TS_TablesWP_Cell_Locale . '" data-cell-symbol="' . $TS_TablesWP_Cell_Symbol . '" ' . $TS_TablesWP_Cell_Comment . '>' . $TS_TablesWP_Cell_Value . '</th>';
												$TS_TablesWP_Editor_CountCells++;
											}
											$TS_TablesWP_Editor_CountRows++;
										$output .= '</tr>';							
									}
								$output .= '</thead>';
							}
							$output .= '<tbody>';
								for ($row = 0; $row < $TS_TablesWP_Editor_Rows; $row++) {
									if ((in_array($row, $TS_TablesWP_Headers_Rows)) || (in_array($row, $TS_TablesWP_Footers_Rows))) {
										continue;
									} else if (in_array($row + 1, $TS_TablesWP_Exlude_Rows)) {
										for ($col = 0; $col < $TS_TablesWP_Editor_Columns; $col++) {
											$TS_TablesWP_Editor_CountCells++;
										}
										$TS_TablesWP_Editor_CountRows++;
										continue;
									} else if (($TS_ADVANCED_TABLESWP->TS_TablesWP_VisualComposer_Frontend == "true") && (($row - count($TS_TablesWP_Headers_Rows) - count($TS_TablesWP_Footers_Rows)) >= 10)) {
										continue;
									}
									$output .= '<tr>';
										if ($table_scope == "datatable") {
											$output .= '<td style="display: none;"></td>';
										}
										for ($col = 0; $col < $TS_TablesWP_Editor_Columns; $col++) {
											if (in_array($col + 1, $TS_TablesWP_Exlude_Columns)) {
												$TS_TablesWP_Editor_CountCells++;
												continue;
											}
											if (in_array($TS_TablesWP_Editor_CountCells, $TS_TablesWP_Merged_Skip)) {
												if ($table_merged == "true") {
													$TS_TablesWP_Merged_Attr 					= 'style="display: none; width: 0; height: 0;"';
												} else {
													$TS_TablesWP_Editor_CountCells++;
													continue;
												}
											} else if (in_array($TS_TablesWP_Editor_CountCells, $TS_TablesWP_Merged_Print)) {
												$TS_TablesWP_Merged_Attr 						= 'colspan="' . ($TS_TablesWP_Merged_Mirror[$TS_TablesWP_Editor_CountCells]->colspan) . '" rowspan="' . ($TS_TablesWP_Merged_Mirror[$TS_TablesWP_Editor_CountCells]->rowspan) . '"';
											} else {
												$TS_TablesWP_Merged_Attr 						= '';
											}
											// Extract Cell Data
											$TS_TablesWP_Cell_Value								= (isset($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->value) ? $TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->value : '');
											if ($table_shortcodes == "true") {
												$TS_TablesWP_Cell_Value							= do_shortcode($TS_TablesWP_Cell_Value);
											}
											if (($table_formatting == "custom") && (!in_array($col + 1, $TS_TablesWP_Format_Exclude))) {
												if ((in_array($col + 1, $TS_TablesWP_Format_Date)) && ($TS_TablesWP_Cell_Value == strip_tags($TS_TablesWP_Cell_Value)) && (preg_match("/\\d/", $TS_TablesWP_Cell_Value) > 0)) {
													$TS_TablesWP_Cell_Type						= "date";
													$TS_TablesWP_Cell_Format					= $numbers_date;
												} else if ((in_array($col + 1, $TS_TablesWP_Format_Time)) && ($TS_TablesWP_Cell_Value == strip_tags($TS_TablesWP_Cell_Value)) && (preg_match("/\\d/", $TS_TablesWP_Cell_Value) > 0)) {
													$TS_TablesWP_Cell_Type						= "time";
													$TS_TablesWP_Cell_Format					= $numbers_time;
												} else if ((in_array($col + 1, $TS_TablesWP_Format_Number)) && (is_numeric($TS_TablesWP_Cell_Value))) {
													$TS_TablesWP_Cell_Type						= "number";
													$TS_TablesWP_Cell_Format					= $numbers_numeric;
												} else if ((in_array($col + 1, $TS_TablesWP_Format_Currency)) && (is_numeric($TS_TablesWP_Cell_Value))) {
													$TS_TablesWP_Cell_Type						= "currency";
													$TS_TablesWP_Cell_Format					= $numbers_currency;
												} else if ((in_array($col + 1, $TS_TablesWP_Format_Percent)) && (is_numeric($TS_TablesWP_Cell_Value))) {
													$TS_TablesWP_Cell_Type						= "percent";
													$TS_TablesWP_Cell_Format					= $numbers_percent;
												} else if ((in_array($col + 1, $TS_TablesWP_Format_Natural_Simple)) && ($table_scope == "datatable")) {
													$TS_TablesWP_Cell_Type					= "text";
													$TS_TablesWP_Cell_Format				= "text";
												} else if ((in_array($col + 1, $TS_TablesWP_Format_Natural_NoHTML)) && ($table_scope == "datatable")) {
													$TS_TablesWP_Cell_Type					= "text";
													$TS_TablesWP_Cell_Format				= "text";
												} else if ((in_array($col + 1, $TS_TablesWP_Format_Natural_NoCase)) && ($table_scope == "datatable")) {
													$TS_TablesWP_Cell_Type					= "text";
													$TS_TablesWP_Cell_Format				= "text";
												} else if ((in_array($col + 1, $TS_TablesWP_Format_NumberHTML)) && ($table_scope == "datatable")) {
													$TS_TablesWP_Cell_Type					= "text";
													$TS_TablesWP_Cell_Format				= "text";
												} else {
													$TS_TablesWP_Cell_Type					= "text";
													$TS_TablesWP_Cell_Format				= "text";
												}
												$TS_TablesWP_Cell_Locale						= $numbers_locale;
												$TS_TablesWP_Cell_Symbol						= $numbers_symbol;
												$TS_TablesWP_Cell_Classes						= trim($numbers_vertical . ' ' . $numbers_horizontal);
											} else {
												$TS_TablesWP_Cell_Type							= (isset($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->formatType) ? $TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->formatType : '');
												$TS_TablesWP_Cell_Format						= (isset($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->format) ? $TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->format : '');												
												$TS_TablesWP_Cell_Locale						= (isset($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->locale) ? $TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->locale : '');
												$TS_TablesWP_Cell_Symbol						= (isset($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->symbol) ? $TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->symbol : '');
												$TS_TablesWP_Cell_Classes						= (isset($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->className) ? $TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->className : '');
											}
											$TS_TablesWP_Cell_Comment							= '';
											$TS_TablesWP_Cell_Override							= '';
											$TS_TablesWP_Cell_Indicator							= '';
											if ((isset($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->comment)) && ($show_comments == "true")) {
												if ($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->comment != "") {
													if (($comments_date_check == "true") && (substr($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->comment, 0, 4) === "ddd:")) {
														$TS_TablesWP_Cell_Override				= trim(substr($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->comment, 4));
														if ($comments_date_tooltip == "true") {
															$TS_TablesWP_Cell_Comment			= $TS_TablesWP_Cell_Override;
														}
													} else if (($comments_time_check == "true") && (substr($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->comment, 0, 4) === "ttt:")) {
														$TS_TablesWP_Cell_Override				= trim(substr($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->comment, 4));
														if ($comments_time_tooltip == "true") {
															$TS_TablesWP_Cell_Comment			= $TS_TablesWP_Cell_Override;
														}
													} else {
														$TS_TablesWP_Cell_Comment				= $TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->comment;
													}
													if ($TS_TablesWP_Cell_Comment != '') {
														$TS_TablesWP_Cell_Comment				= $TS_TablesWP_TooltipContent . ' data-krauttipster-text="' . (base64_encode($TS_TablesWP_Cell_Comment)) . '"';
														if ($show_indicator == "true") {
															$TS_TablesWP_Cell_Indicator			= 'ts-advanced-tables-tooltip';
														}
													}
												}										
											}
											if (($TS_TablesWP_Cell_Type == "text") || ($TS_TablesWP_Cell_Type == "html") || ($TS_TablesWP_Cell_Type == "")) {
												$TS_TablesWP_Cell_Base64						= base64_encode($TS_TablesWP_Cell_Value);
											} else {
												$TS_TablesWP_Cell_Base64						= $TS_TablesWP_Cell_Value;
											}
											if ($table_scope == "footable") {
												$TS_TablesWP_Body_Data							= 'data-title="' . (isset($TS_TablesWP_Headers_Labels[$col]) ? $TS_TablesWP_Headers_Labels[$col] : '#' . ($col + 1)) . '" data-value="' . $TS_TablesWP_Cell_Base64 . '" data-sort-value="' . $TS_TablesWP_Cell_Base64 . '"';
												if (($header_use == "false") && ($row == 0)) {
													$TS_TablesWP_Headers_Searchable				= (in_array($col + 1, $TS_TablesWP_Headers_NoSearch) ? 'data-filterable="false"' : 'data-filterable="true"');										
													$TS_TablesWP_Headers_Breakpoints			= array();
													if (in_array($col + 1, $TS_TablesWP_Headers_NoView)) {
														$TS_TablesWP_Headers_Viewable			= 'data-visible="false"';
													} else {
														if ((count($TS_TablesWP_Headers_NoShow) == 0) && (count($TS_TablesWP_Headers_NotLarge) == 0) && (count($TS_TablesWP_Headers_NotMedium) == 0) && (count($TS_TablesWP_Headers_NotSmall) == 0) && (count($TS_TablesWP_Headers_NotExtra) == 0)) {
															$TS_TablesWP_Headers_Breakpoints 	= array();
														} else if (in_array($col + 1, $TS_TablesWP_Headers_NoShow)) {
															$TS_TablesWP_Headers_Breakpoints	= array('all', 'lg', 'md', 'sm', 'xs');
														} else {
															if (in_array($col + 1, $TS_TablesWP_Headers_NotLarge)) {
																array_push($TS_TablesWP_Headers_Breakpoints, "lg");
															}
															if (in_array($col + 1, $TS_TablesWP_Headers_NotMedium)) {
																array_push($TS_TablesWP_Headers_Breakpoints, "md");
															}
															if (in_array($col + 1, $TS_TablesWP_Headers_NotSmall)) {
																array_push($TS_TablesWP_Headers_Breakpoints, "sm");
															}
															if (in_array($col + 1, $TS_TablesWP_Headers_NotExtra)) {
																array_push($TS_TablesWP_Headers_Breakpoints, "xs");
															}
														}										
														$TS_TablesWP_Headers_Viewable			= 'data-breakpoints="' . (implode(" ", $TS_TablesWP_Headers_Breakpoints)) . '"';
													}
												} else {
													$TS_TablesWP_Headers_Viewable				= '';
												}
											} else if ($table_scope == "datatable") {
												if (($TS_TablesWP_Cell_Type == "number") || ($TS_TablesWP_Cell_Type == "currency") || ($TS_TablesWP_Cell_Type == "percent") || ($TS_TablesWP_Cell_Type == "date")) {
													$TS_TablesWP_Body_Data						= 'data-order="' . $TS_TablesWP_Cell_Value . '"';
												} else {
													$TS_TablesWP_Body_Data						= '';
												}
												$TS_TablesWP_Headers_Viewable					= '';										
											} else if ($table_scope == "tablesaw") {
												if (($TS_TablesWP_Cell_Type == "number") || ($TS_TablesWP_Cell_Type == "currency") || ($TS_TablesWP_Cell_Type == "percent") || ($TS_TablesWP_Cell_Type == "date")) {
													$TS_TablesWP_Body_Data						= 'data-order="' . $TS_TablesWP_Cell_Value . '"';
												} else {
													$TS_TablesWP_Body_Data						= '';
												}
												$TS_TablesWP_Headers_Viewable					= '';	
											} else {
												$TS_TablesWP_Body_Data							= '';
												$TS_TablesWP_Headers_Viewable					= '';
											}
											$output .= '<td ' . $TS_TablesWP_Headers_Viewable . ' ' . $TS_TablesWP_Body_Data . ' ' . ($col == 0 ? 'data-cell-scope="row"' : '') . ' data-cell-label="' . (isset($TS_TablesWP_Headers_Labels[$col]) ? $TS_TablesWP_Headers_Labels[$col] : "") . '" class="ts-advanced-tables-cell ' . $TS_TablesWP_Cell_Classes . ' ' . $TS_TablesWP_Cell_Indicator . ' ' . ((($table_scope == "tablesaw") && ($col == 0)) ? "title" : "") . '" ' . $TS_TablesWP_Merged_Attr . ' data-cell-type="' . $TS_TablesWP_Cell_Type . '" data-cell-format="' . $TS_TablesWP_Cell_Format. '" data-cell-column="' . ($col + 1) . '" data-cell-row="' . ($TS_TablesWP_Editor_CountRows + 1) . '" data-cell-count="' . $TS_TablesWP_Editor_CountCells . '" data-cell-value="' . $TS_TablesWP_Cell_Base64 . '" data-cell-override="' . $TS_TablesWP_Cell_Override . '" data-cell-locale="' . $TS_TablesWP_Cell_Locale . '" data-cell-symbol="' . $TS_TablesWP_Cell_Symbol . '" ' . $TS_TablesWP_Cell_Comment . '>' . $TS_TablesWP_Cell_Value . '</td>';
											$TS_TablesWP_Editor_CountCells++;
										}
										$TS_TablesWP_Editor_CountRows++;
									$output .= '</tr>';						
								}
								if (($TS_ADVANCED_TABLESWP->TS_TablesWP_VisualComposer_Frontend == "true") && (($TS_TablesWP_Editor_Rows - count($TS_TablesWP_Headers_Rows) - count($TS_TablesWP_Footers_Rows)) > 10)) {
									$output .= '<tr><td class="ts-advanced-tables-suppressor" colspan="' . $TS_TablesWP_Editor_Columns . '">... ' . ($TS_TablesWP_Editor_Rows - count($TS_TablesWP_Headers_Rows) - count($TS_TablesWP_Footers_Rows) - 10) . ' ' . __('additional rows currently suppressed', 'ts_visual_composer_extend') . ' ...</td></tr>';
								}
							$output .= '</tbody>';
							if ($footer_use == "true") {
								$output .= '<tfoot>';
									foreach ($TS_TablesWP_Footers_Rows as $footer) {
										if (in_array($footer + 1, $TS_TablesWP_Exlude_Rows)) {
											for ($col = 0; $col < $TS_TablesWP_Editor_Columns; $col++) {
												$TS_TablesWP_Editor_CountCells++;
											}
											$TS_TablesWP_Editor_CountRows++;
											continue;
										}
										$output .= '<tr>';
											if ($table_scope == "datatable") {
												$output .= '<th class="control" style="display: none;"></th>';
											}
											for ($col = 0; $col < $TS_TablesWP_Editor_Columns; $col++) {
												if (in_array($col + 1, $TS_TablesWP_Exlude_Columns)) {
													$TS_TablesWP_Editor_CountCells++;
													continue;
												}
												if (in_array($TS_TablesWP_Editor_CountCells, $TS_TablesWP_Merged_Skip)) {
													if ($table_merged == "true") {
														$TS_TablesWP_Merged_Attr = 'style="display: none; width: 0; height: 0;"';
													} else {
														$TS_TablesWP_Editor_CountCells++;
														continue;
													}
												} else if (in_array($TS_TablesWP_Editor_CountCells, $TS_TablesWP_Merged_Print)) {
													$TS_TablesWP_Merged_Attr = 'colspan="' . ($TS_TablesWP_Merged_Mirror[$TS_TablesWP_Editor_CountCells]->colspan) . '" rowspan="' . ($TS_TablesWP_Merged_Mirror[$TS_TablesWP_Editor_CountCells]->rowspan) . '"';
												} else {
													$TS_TablesWP_Merged_Attr = '';
												}
												if ($table_scope == "footable") {
													$TS_TablesWP_Foot_Data							= 'data-sort-value="' . $TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->value . '"';
												} else {
													$TS_TablesWP_Foot_Data							= '';
												}
												$TS_TablesWP_Cell_Value							= (isset($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->value) ? $TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->value : '');
												if ($table_shortcodes == "true") {
													$TS_TablesWP_Cell_Value						= do_shortcode($TS_TablesWP_Cell_Value);
												}
												// Extract Cell Data
												if (($table_formatting == "custom") && ($numbers_footer == "true") && (!in_array($col + 1, $TS_TablesWP_Format_Exclude))) {
													if ((in_array($col + 1, $TS_TablesWP_Format_Date)) && ($TS_TablesWP_Cell_Value == strip_tags($TS_TablesWP_Cell_Value)) && (preg_match("/\\d/", $TS_TablesWP_Cell_Value) > 0)) {													
														$TS_TablesWP_Cell_Type					= "date";
														$TS_TablesWP_Cell_Format				= $numbers_date;
													} else if ((in_array($col + 1, $TS_TablesWP_Format_Time)) && ($TS_TablesWP_Cell_Value == strip_tags($TS_TablesWP_Cell_Value)) && (preg_match("/\\d/", $TS_TablesWP_Cell_Value) > 0)) {
														$TS_TablesWP_Cell_Type					= "time";
														$TS_TablesWP_Cell_Format				= $numbers_time;
													} else if ((in_array($col + 1, $TS_TablesWP_Format_Number)) && (is_numeric($TS_TablesWP_Cell_Value))) {
														$TS_TablesWP_Cell_Type					= "number";
														$TS_TablesWP_Cell_Format				= $numbers_numeric;
													} else if ((in_array($col + 1, $TS_TablesWP_Format_Currency)) && (is_numeric($TS_TablesWP_Cell_Value))) {
														$TS_TablesWP_Cell_Type					= "currency";
														$TS_TablesWP_Cell_Format				= $numbers_currency;
													} else if ((in_array($col + 1, $TS_TablesWP_Format_Percent)) && (is_numeric($TS_TablesWP_Cell_Value))) {
														$TS_TablesWP_Cell_Type					= "percent";
														$TS_TablesWP_Cell_Format				= $numbers_percent;
													} else if ((in_array($col + 1, $TS_TablesWP_Format_Natural_Simple)) && ($table_scope == "datatable")) {
														$TS_TablesWP_Cell_Type					= "text";
														$TS_TablesWP_Cell_Format				= "text";
													} else if ((in_array($col + 1, $TS_TablesWP_Format_Natural_NoHTML)) && ($table_scope == "datatable")) {
														$TS_TablesWP_Cell_Type					= "text";
														$TS_TablesWP_Cell_Format				= "text";
													} else if ((in_array($col + 1, $TS_TablesWP_Format_Natural_NoCase)) && ($table_scope == "datatable")) {
														$TS_TablesWP_Cell_Type					= "text";
														$TS_TablesWP_Cell_Format				= "text";
													} else if ((in_array($col + 1, $TS_TablesWP_Format_NumberHTML)) && ($table_scope == "datatable")) {
														$TS_TablesWP_Cell_Type					= "text";
														$TS_TablesWP_Cell_Format				= "text";
													} else {
														$TS_TablesWP_Cell_Type					= "text";
														$TS_TablesWP_Cell_Format				= "text";
													}
													$TS_TablesWP_Cell_Locale					= $numbers_locale;
													$TS_TablesWP_Cell_Symbol					= $numbers_symbol;
													$TS_TablesWP_Cell_Classes					= trim($numbers_vertical . ' ' . $numbers_horizontal);
												} else {
													$TS_TablesWP_Cell_Type						= (isset($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->formatType) ? $TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->formatType : '');
													$TS_TablesWP_Cell_Format					= (isset($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->format) ? $TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->format : '');												
													$TS_TablesWP_Cell_Locale					= (isset($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->locale) ? $TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->locale : '');
													$TS_TablesWP_Cell_Symbol					= (isset($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->symbol) ? $TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->symbol : '');
													$TS_TablesWP_Cell_Classes					= (isset($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->className) ? $TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->className : '');
												}
												$TS_TablesWP_Cell_Comment						= '';
												$TS_TablesWP_Cell_Override						= '';
												$TS_TablesWP_Cell_Indicator						= '';
												if ((isset($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->comment)) && ($show_comments == "true")) {
													if ($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->comment != "") {
														if (($comments_date_check == "true") && (substr($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->comment, 0, 4) === "ddd:")) {
															$TS_TablesWP_Cell_Override				= trim(substr($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->comment, 4));
															if ($comments_date_tooltip == "true") {
																$TS_TablesWP_Cell_Comment			= $TS_TablesWP_Cell_Override;
															}
														} else if (($comments_time_check == "true") && (substr($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->comment, 0, 4) === "ttt:")) {
															$TS_TablesWP_Cell_Override				= trim(substr($TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->comment, 4));
															if ($comments_time_tooltip == "true") {
																$TS_TablesWP_Cell_Comment			= $TS_TablesWP_Cell_Override;
															}
														} else {
															$TS_TablesWP_Cell_Comment				= $TS_TablesWP_Editor_Meta[$TS_TablesWP_Editor_CountCells]->comment;
														}
														if ($TS_TablesWP_Cell_Comment != '') {
															$TS_TablesWP_Cell_Comment				= $TS_TablesWP_TooltipContent . ' data-krauttipster-text="' . (base64_encode($TS_TablesWP_Cell_Comment)) . '"';
															if ($show_indicator == "true") {
																$TS_TablesWP_Cell_Indicator			= 'ts-advanced-tables-tooltip';
															}
														}
													}
												}
												if (($TS_TablesWP_Cell_Type == "text") || ($TS_TablesWP_Cell_Type == "html") || ($TS_TablesWP_Cell_Type == "")) {
													$TS_TablesWP_Cell_Base64						= base64_encode($TS_TablesWP_Cell_Value);
												} else {
													$TS_TablesWP_Cell_Base64						= $TS_TablesWP_Cell_Value;
												}
												$output .= '<th ' . $TS_TablesWP_Foot_Data . ' class="ts-advanced-tables-cell ' . $TS_TablesWP_Cell_Classes . ' ' . $TS_TablesWP_Cell_Indicator . '" ' . $TS_TablesWP_Merged_Attr . ' data-cell-type="' . $TS_TablesWP_Cell_Type . '" data-cell-format="' . $TS_TablesWP_Cell_Format . '" data-cell-column="' . ($col + 1) . '" data-cell-row="' . ($TS_TablesWP_Editor_CountRows + 1) . '" data-cell-count="' . $TS_TablesWP_Editor_CountCells . '" data-cell-value="' . $TS_TablesWP_Cell_Base64 . '" data-cell-override="' . $TS_TablesWP_Cell_Override . '" data-cell-locale="' . $TS_TablesWP_Cell_Locale . '" data-cell-symbol="' . $TS_TablesWP_Cell_Symbol . '" ' . $TS_TablesWP_Cell_Comment . '>' . $TS_TablesWP_Cell_Value . '</th>';
												$TS_TablesWP_Editor_CountCells++;
											}
											$TS_TablesWP_Editor_CountRows++;
										$output .= '</tr>';							
									}
								$output .= '</tfoot>';
							}
						$output .= '</table>';
					$output .= '</div>';
				$output .= '</div>';
				
				echo $output;
				
				// Clear Variables from Memory
				unset($output);
				unset($TS_TablesWP_Merged_Print);
				unset($TS_TablesWP_Merged_Skip);
				unset($TS_TablesWP_Merged_Mirror);
				unset($TS_TablesWP_Editor_Decode);
				unset($TS_TablesWP_Editor_Merged);
				unset($TS_TablesWP_Headers_Rows);
				unset($TS_TablesWP_Headers_Labels);
				unset($TS_TablesWP_Footers_Rows);
				unset($TS_TablesWP_Editor_Meta);
				
				$myvariable = ob_get_clean();
				return $myvariable;
			}			
		}
	}
	if (class_exists('TS_Tablenator_Shortcode_Table')) {
		$TS_Tablenator_Shortcode_Table = new TS_Tablenator_Shortcode_Table;
	}
?>