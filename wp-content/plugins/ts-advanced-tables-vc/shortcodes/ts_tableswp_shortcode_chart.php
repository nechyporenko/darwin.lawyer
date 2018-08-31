<?php
	if (!class_exists('TS_Tablenator_Shortcode_Chart')){
		class TS_Tablenator_Shortcode_Chart {
			/* ------------------------------------- */
			/* Helper Dictionary for Color Generator */
			/* ------------------------------------- */
			static public $TS_TablesWP_ColorDictionary = array(
				'monochrome'    => array(
					'bounds'    => array(array(0,0), array(100,0)),
					'h'         => NULL,
					's'         => array(0,100)
				),
				'red'           => array(
					'bounds'    => array(array(20,100), array(30,92), array(40,89), array(50,85), array(60,78), array(70,70), array(80,60), array(90,55), array(100,50)),
					'h'         => array(-26,18),
					's'         => array(20,100)
				),
				'orange'        => array(
					'bounds'    => array(array(20,100), array(30,93), array(40,88), array(50,86), array(60,85), array(70,70), array(100,70)),
					'h'         => array(19,46),
					's'         => array(20,100)
				),
				'yellow'        => array(
					'bounds'    => array(array(25,100), array(40,94), array(50,89), array(60,86), array(70,84), array(80,82), array(90,80), array(100,75)),
					'h'         => array(47,62),
					's'         => array(25,100)
				),
				'green'         => array(
					'bounds'    => array(array(30,100), array(40,90), array(50,85), array(60,81), array(70,74), array(80,64), array(90,50), array(100,40)),
					'h'         => array(63,178),
					's'         => array(30,100)
				),
				'blue'          => array(
					'bounds'    => array(array(20,100), array(30,86), array(40,80), array(50,74), array(60,60), array(70,52), array(80,44), array(90,39), array(100,35)),
					'h'         => array(179,257),
					's'         => array(20,100)
				),
				'purple'        => array(
					'bounds'    => array(array(20,100), array(30,87), array(40,79), array(50,70), array(60,65), array(70,59), array(80,52), array(90,45), array(100,42)),
					'h'         => array(258,282),
					's'         => array(20,100)
				),
				'pink'          => array(
					'bounds'    => array(array(20,100), array(30,90), array(40,86), array(60,84), array(80,80), array(90,75), array(100,73)),
					'h'         => array(283,334),
					's'         => array(20,100)
				),
			);
			
			
			/* ---------------- */
			/* Initialize Class */
			/* ---------------- */
			function __construct() {
				global $TS_ADVANCED_TABLESWP;
				add_shortcode('TS_Advanced_Charts', 	array($this, 'TS_Advanced_Charts_Function'));
			}
			
			
			/* --------------------------------- */
			/* Chart Shortcode Callback Function */
			/* --------------------------------- */
			function TS_Advanced_Charts_Function ($atts) {
				global $TS_ADVANCED_TABLESWP;
				global $wpdb;
				ob_start();
		
				extract( shortcode_atts( array(
					// Table ID
					'id'								=> '',
					// Global Chart Settings
					'chart_type'						=> 'pie',
					'chart_multiple'					=> 'false',	
					'chart_height'						=> 400,
					'chart_debug'						=> 'false',
					'chart_background'					=> '#ffffff',
					'chart_padding'						=> 0,
					'chart_colors'						=> 'tablecheck',	// tablecheck, allrandom, customized
					'chart_shortcodes'					=> 'false',
					// Random Color Settings
					'colors_luminosity'					=> 'random',		// bright, light, dark, random
					'colors_hue'						=> 'random',		// red, orange, yellow, green, blue, purple, pink, monochrome, random
					// Preloader Settings
					'preloader_use'						=> 'false',
					'preloader_style'					=> 0,
					'preloader_background'				=> '#ffffff',
					// Legend Settings
					'legend_show'						=> 'true',
					'legend_position'					=> 'top',
					'legend_fullwidth'					=> 'true',
					// Axis Labels
					'label_xaxis_show'					=> 'true',
					'label_xaxis_text'					=> 'VGhpcyBpcyB0aGUgWC1BeGlzIExhYmVsLg==',
					'label_yaxis_show'					=> 'true',
					'label_yaxis_text'					=> 'VGhpcyBpcyB0aGUgWS1BeGlzIExhYmVsLg==',
					// Single Dataset
					'single_labelnames'					=> '1',				// Column (Single)
					'single_labelcolors'				=> '2',				// Column (Single)
					'single_labelvalues'				=> '3',				// Column (Single)
					'single_labelexclude'				=> '1,2',			// Rows (Multiple)
					// Multiple Datasets
					'multiple_labelnames'				=> '1',				// Column (Single)
					'multiple_labelcolors'				=> '2',				// Column (Single)
					'multiple_labelvalues'				=> '3,4,5',			// Columns (Multiple)
					'multiple_labelexclude'				=> '',				// Rows (Multiple)
					'multiple_setcolors'				=> '1',				// Row (Single)
					'multiple_setnames'					=> '2',				// Row (Single)
					// Bubble Datasets
					'bubble_labelnames'					=> '',				// Column (Single)
					'bubble_labelcolors'				=> '',				// Column (Single)
					'bubble_labelvaluesx'				=> '',				// Column (Single)
					'bubble_labelvaluesy'				=> '',				// Column (Single)
					'bubble_labelvaluesr'				=> '',				// Column (Single)
					'bubble_labelexclude'				=> '',				// Rows (Multiple)
					// Exclusions
					'exclude_rows'						=> '',
					'exclude_columns'					=> '',
					// Chart Type Specific Settings
					'chart_borderwidth'					=> 2,
					'chart_stacked'						=> 'false',
					'chart_filled'						=> 'false',
					'chart_stepped'						=> 'false',
					'chart_pointstyle'					=> 'circle',		// circle, triangle, rect, rectRot, cross, crossRot, star, line, dash
					'chart_pointradius'					=> 4,
					'chart_borderdash'					=> 'false',
					'chart_bordercapstyle'				=> 'butt',			// butt, round, square
					'chart_borderjoinstyle'				=> 'miter',			// miter, round, bevel
					'chart_linetension'					=> 20,
					'chart_startzero'					=> 'true',
					// Global Number Settings					
					'numbers_type'						=> 'none',			// none, number, currency, percent, data, time
					'numbers_locale'					=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_EditorLocale,
					'numbers_symbol'					=> '$',
					'numbers_space_percent'				=> ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_PercentSpace == true ? 'true' : 'false'),
					'numbers_space_currency'			=> ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_CurrencySpace == true ? 'true' : 'false'),
					'numbers_placement'					=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_CurrencyPlacement,
					'numbers_date'						=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatDate,
					'numbers_time'						=> $TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_FormatTime,
					// Tooltip Format Settings					
					'tooltip_format'					=> 'true',					
					'tooltip_decimals'					=> 2,
					// Tick Format Settings
					'ticks_format'						=> 'true',
					'ticks_decimals'					=> 0,
					// Global Opacity Settings
					'opacity_background'				=> 25,
					'opacity_border'					=> 75,
					'opacity_line'						=> 75,
					'opacity_points'					=> 90,
					// Other Settings
					'margin_bottom'						=> 0,
					'margin_top' 						=> 0,
					'el_id' 							=> '',
					'el_class'                  		=> '',
					'css'								=> '',
				), $atts ));
				
				$randomizer                    				= mt_rand(999999, 9999999);
				$output 									= '';
				$styles										= '';
		
				// Opacity Conversions
				$opacity_background 						= ($opacity_background / 100);
				$opacity_border 							= ($opacity_border / 100);
				$opacity_line 								= ($opacity_line / 100);
				$opacity_points 							= ($opacity_points / 100);
				
				// Generate Random ID
				if (!empty($el_id)) {
					$container_id							= $el_id;
				} else {
					$container_id							= 'ts-advanced-charts-container-' . $randomizer;
				}
				
				// Load Required CSS/JS Files
				if ($TS_ADVANCED_TABLESWP->TS_TablesWP_VisualComposer_Frontend == "false") {
					wp_enqueue_style('ts-extend-preloaders');
					wp_enqueue_script('ts-extend-numbro');
					wp_enqueue_script('ts-extend-momentjs');
					wp_enqueue_script('ts-extend-languages');	
					wp_enqueue_script('ts-extend-chartjs');					
					wp_enqueue_script('ts-extend-advancedcharts');
				}
				wp_enqueue_style('ts-extend-advancedcharts');
		
				if ($TS_ADVANCED_TABLESWP->TS_TablesWP_VisualComposer_Frontend == "false") {
					// Retriev Table Data
					$TS_ChartsVC_Editor_ID					= $id;		
					$TS_ChartsVC_Editor_Import          	= get_option('ts_tablesplus_data_singletable_' . $TS_ChartsVC_Editor_ID, array());		
					if ((count($TS_ChartsVC_Editor_Import) == 0) || ($id == "")) {
						echo $output;
						$myvariable 						= ob_get_clean();
						return $myvariable;
					}
					
					$TS_ChartsVC_Editor_Other 				= get_option("ts_tablesplus_extend_settings_tables", array());
					$TS_ChartsVC_Editor_Import          	= (rawurldecode(base64_decode(strip_tags($TS_ChartsVC_Editor_Import))));
					$TS_ChartsVC_Editor_Decode          	= json_decode($TS_ChartsVC_Editor_Import);
					$TS_ChartsVC_Editor_Basic          		= base64_encode(json_encode($TS_ChartsVC_Editor_Decode->data));

					// Generate Global Variables
					if (isset($TS_ChartsVC_Editor_Decode->name)) {
						$TS_ChartsVC_Editor_Name        	= $TS_ChartsVC_Editor_Decode->name;
					} else {
						$TS_ChartsVC_Editor_Name        	= "";
					}
					if (isset($TS_ChartsVC_Editor_Decode->info)) {
						$TS_ChartsVC_Editor_Info        	= $TS_ChartsVC_Editor_Decode->info;
					} else {
						$TS_ChartsVC_Editor_Info        	= "";
					}
					$TS_ChartsVC_Editor_Cells				= 0;
					$TS_ChartsVC_Editor_Rows 				= $TS_ChartsVC_Editor_Decode->rows;
					$TS_ChartsVC_Editor_Columns				= $TS_ChartsVC_Editor_Decode->columns;
					$TS_TablesWP_Editor_Data				= urldecode($TS_ChartsVC_Editor_Decode->data);
					$TS_ChartsVC_Editor_Meta 				= $TS_ChartsVC_Editor_Decode->meta;
					$TS_ChartsVC_Editor_Defaults 			= $TS_ChartsVC_Editor_Decode->defaults;
					
					// Excluded Sources
					$TS_ChartsVC_Exlude_Rows				= explode(",", $exclude_rows);
					$TS_ChartsVC_Exlude_Columns				= explode(",", $exclude_columns);
					
					// Contingency Checks
					foreach ($TS_TablesWP_Editor_Meta as $meta) {
						$meta->value						= urldecode($meta->value);
					}
					
					// Data Sources		
					if (($chart_multiple == "false") && ($chart_type != "bubble")) {
						$TS_ChartsVC_Source_Ignore			= explode(",", $single_labelexclude);
						$TS_ChartsVC_Source_LabelNames		= explode(",", $single_labelnames);
						$TS_ChartsVC_Source_LabelColors		= explode(",", $single_labelcolors);
						$TS_ChartsVC_Source_DataSets		= explode(",", $single_labelvalues);
						$TS_ChartsVC_Source_SetNames		= array();
						$TS_ChartsVC_Source_SetColors		= array();
					} else if (($chart_multiple == "true") && ($chart_type != "bubble")) {
						$TS_ChartsVC_Source_Ignore			= explode(",", $multiple_labelexclude);
						$TS_ChartsVC_Source_LabelNames		= explode(",", $multiple_labelnames);
						$TS_ChartsVC_Source_LabelColors		= explode(",", $multiple_labelcolors);
						$TS_ChartsVC_Source_DataSets		= explode(",", $multiple_labelvalues);
						$TS_ChartsVC_Source_SetNames		= explode(",", $multiple_setnames);
						$TS_ChartsVC_Source_SetColors		= explode(",", $multiple_setcolors);
					} else {
						
					}
					
					$TS_ChartsVC_Source_Stacks				= count($TS_ChartsVC_Source_DataSets);		
					$TS_ChartsVC_Source_Full				= array();
					$TS_ChartsVC_Source_Single				= array();
					
					$TS_ChartsVC_Loops_SetNames				= 0;
					$TS_ChartsVC_Loops_DataSets				= 0;
					$TS_ChartsVC_Loops_Colors				= 0;
					
					// Create Chart Data Object
					for ($stack = 0; $stack < $TS_ChartsVC_Source_Stacks; $stack++) {
						// General DataSet Settings
						$TS_ChartsVC_Source_Single['label']								= '';
						$TS_ChartsVC_Source_Single['data']								= array();
						$TS_ChartsVC_Source_Single['backgroundColor']					= array();
						$TS_ChartsVC_Source_Single['borderColor']						= array();
						$TS_ChartsVC_Source_Single['borderWidth']						= $chart_borderwidth;
						// Chart Type Specific DataSet Settings
						if (($chart_type == "line") || ($chart_type == "radar")) {
							$TS_ChartsVC_Source_Single['fill']							= ($chart_filled == "true" ? true : false);
							$TS_ChartsVC_Source_Single['pointBackgroundColor']			= array();
							$TS_ChartsVC_Source_Single['pointBorderColor']				= array();
							$TS_ChartsVC_Source_Single['pointBorderWidth']				= $chart_borderwidth;
							$TS_ChartsVC_Source_Single['showLine']						= true;
							$TS_ChartsVC_Source_Single['lineTension']					= ($chart_linetension / 100);
							$TS_ChartsVC_Source_Single['borderDash']					= ($chart_borderdash == "true" ? [5, 10] : []);
							$TS_ChartsVC_Source_Single['borderCapStyle']				= $chart_bordercapstyle;
							$TS_ChartsVC_Source_Single['borderJoinStyle']				= $chart_borderjoinstyle;
							$TS_ChartsVC_Source_Single['pointStyle']					= $chart_pointstyle;				
							$TS_ChartsVC_Source_Single['pointRadius']					= $chart_pointradius;
							$TS_ChartsVC_Source_Single['spanGaps']						= false;
							$TS_ChartsVC_Source_Single['steppedLine']					= ($chart_stepped == "true" ? true : false);
						} else if (($chart_type == "bar") || ($chart_type == "horizontalBar")) {
							$TS_ChartsVC_Source_Single['borderSkipped']					= '';
						} else if ($chart_type == "bubble") {
							$TS_ChartsVC_Source_Single['hoverRadius']					= 10;
						}
						array_push($TS_ChartsVC_Source_Full, $TS_ChartsVC_Source_Single);
					}
					
					// Contingency Checks
					if ((($chart_multiple == "false") || (($chart_multiple == "true") && ($TS_ChartsVC_Source_Stacks < 2))) && (($chart_type == "radar") || ($chart_type == "line") || ($chart_type == "bar") || ($chart_type == "horizontalBar"))) {
						$legend_show						= "false";
					}
					if (!preg_match('#[^0-9]#', $numbers_symbol)) {
						$numbers_symbol						= $TS_ADVANCED_TABLESWP->TS_TablesWP_Currency_HTML_Inverted[$numbers_symbol];
					}
			
					// Chart Data Holder
					$TS_ChartsVC_Data_Labels				= array();
					$TS_ChartsVC_Data_Values				= array();
					$TS_ChartsVC_Data_Tooltips				= array();
					$TS_ChartsVC_Data_Colors				= array();
					$TS_ChartsVC_Data_Transfer				= array();
					$TS_ChartsVC_Data_Holder				= array();
					$TS_ChartsVC_Data_Keys					= array();
				
					// Generate Chart DataSet
					for ($row = 0; $row < $TS_ChartsVC_Editor_Rows; $row++) {
						if (in_array($row + 1, $TS_ChartsVC_Exlude_Rows)) {
							for ($col = 0; $col < $TS_ChartsVC_Editor_Columns; $col++) {
								$TS_ChartsVC_Editor_Cells++;
							}
							continue;
						}
						for ($col = 0; $col < $TS_ChartsVC_Editor_Columns; $col++) {
							if (in_array($col + 1, $TS_ChartsVC_Exlude_Columns)) {
								$TS_ChartsVC_Editor_Cells++;
								continue;
							}
							// Retrieve DataSet Names
							if ((in_array($row + 1, $TS_ChartsVC_Source_SetNames)) && (in_array($col + 1, $TS_ChartsVC_Source_DataSets)) && (count($TS_ChartsVC_Source_DataSets) > 1) && (!in_array($row + 1, $TS_ChartsVC_Source_Ignore))) {
								$TS_ChartsVC_Source_Full[$TS_ChartsVC_Loops_SetNames]['label']				= $TS_ChartsVC_Editor_Meta[$TS_ChartsVC_Editor_Cells]->value;
								$TS_ChartsVC_Loops_SetNames++;
								if ($TS_ChartsVC_Loops_SetNames >= $TS_ChartsVC_Source_Stacks) {
									$TS_ChartsVC_Loops_SetNames 											= 0;
								}				
							}
							// Retrieve Value Labels
							if ((in_array($col + 1, $TS_ChartsVC_Source_LabelNames)) && (!in_array($row + 1, $TS_ChartsVC_Source_SetNames)) && (!in_array($row + 1, $TS_ChartsVC_Source_SetColors)) && (!in_array($row + 1, $TS_ChartsVC_Source_Ignore))) {
								array_push($TS_ChartsVC_Data_Labels, $TS_ChartsVC_Editor_Meta[$TS_ChartsVC_Editor_Cells]->value);				
							}
							// Retrieve Custom Colors
							if (($chart_multiple == "false") || ($chart_type == "bubble")) {
								if ((in_array($col + 1, $TS_ChartsVC_Source_LabelColors)) && (!in_array($row + 1, $TS_ChartsVC_Source_SetNames)) && (!in_array($row + 1, $TS_ChartsVC_Source_SetColors)) && (!in_array($row + 1, $TS_ChartsVC_Source_Ignore))) {
									if ($chart_colors == "tablecheck") {
										$TS_ChartsVC_Data_Colors											= preg_replace('/\s/', ' ', $TS_ChartsVC_Editor_Meta[$TS_ChartsVC_Editor_Cells]->className);
										$TS_ChartsVC_Data_Colors											= explode(" ", $TS_ChartsVC_Data_Colors);							
										$TS_ChartsVC_Data_Colors 											= preg_grep('/\\bbackground-?\\b/i', $TS_ChartsVC_Data_Colors);
										$TS_ChartsVC_Data_Keys    											= array_keys($TS_ChartsVC_Data_Colors);
									}
									if (($chart_colors == "allrandom") || ((count($TS_ChartsVC_Data_Colors) == 0) || (count($TS_ChartsVC_Data_Keys) == 0))) {
										if (($colors_luminosity == "random") && ($colors_hue == "random")) {
											$TS_ChartsVC_Data_Colors										= $this->TS_TablesWP_ColorMakerRandomHEX();
										} else {
											$TS_ChartsVC_Data_Colors										= $this->TS_TablesWP_ColorMakerSingle(array('luminosity' => $colors_luminosity,'hue' => $colors_hue));
										}									
									} else {
										$TS_ChartsVC_Data_Colors											= str_replace("background-", "", $TS_ChartsVC_Data_Colors[$TS_ChartsVC_Data_Keys[0]]);
									}
									// Set Background Color
									$TS_ChartsVC_Data_Holder 												= $TS_ChartsVC_Source_Full[$TS_ChartsVC_Loops_DataSets]['backgroundColor'];
									if (!is_array($TS_ChartsVC_Data_Holder)) {
										$TS_ChartsVC_Data_Holder 											= array();
									}
									array_push($TS_ChartsVC_Data_Holder, $this->TS_TablesWP_ColorMakerCreateRGBA($TS_ChartsVC_Data_Colors, $opacity_background));
									$TS_ChartsVC_Source_Full[$TS_ChartsVC_Loops_Colors]['backgroundColor'] 	= $TS_ChartsVC_Data_Holder;
									// Set Border Color
									$TS_ChartsVC_Data_Holder 												= $TS_ChartsVC_Source_Full[$TS_ChartsVC_Loops_DataSets]['backgroundColor'];
									if (!is_array($TS_ChartsVC_Data_Holder)) {
										$TS_ChartsVC_Data_Holder 											= array();
									}
									array_push($TS_ChartsVC_Data_Holder, $this->TS_TablesWP_ColorMakerCreateRGBA($TS_ChartsVC_Data_Colors, $opacity_border));
									$TS_ChartsVC_Source_Full[$TS_ChartsVC_Loops_Colors]['borderColor'] 		= $TS_ChartsVC_Data_Holder;
									// Set Line + Points Color
									if (($chart_type == "line") || ($chart_type == "radar")) {
										$TS_ChartsVC_Data_Holder 											= $TS_ChartsVC_Source_Full[$TS_ChartsVC_Loops_DataSets]['pointBorderColor'];
										if (!is_array($TS_ChartsVC_Data_Holder)) {
											$TS_ChartsVC_Data_Holder 										= array();
										}
										array_push($TS_ChartsVC_Data_Holder, $this->TS_TablesWP_ColorMakerCreateRGBA($TS_ChartsVC_Data_Colors, $opacity_line));
										$TS_ChartsVC_Source_Full[$TS_ChartsVC_Loops_Colors]['pointBorderColor']	= $TS_ChartsVC_Data_Holder;
										$TS_ChartsVC_Data_Holder 											= $TS_ChartsVC_Source_Full[$TS_ChartsVC_Loops_DataSets]['pointBackgroundColor'];
										if (!is_array($TS_ChartsVC_Data_Holder)) {
											$TS_ChartsVC_Data_Holder 										= array();
										}
										array_push($TS_ChartsVC_Data_Holder, $this->TS_TablesWP_ColorMakerCreateRGBA($TS_ChartsVC_Data_Colors, $opacity_points));
										$TS_ChartsVC_Source_Full[$TS_ChartsVC_Loops_Colors]['pointBackgroundColor']	= $TS_ChartsVC_Data_Holder;
									}
									$TS_ChartsVC_Loops_Colors++;
									if ($TS_ChartsVC_Loops_Colors >= $TS_ChartsVC_Source_Stacks) {
										$TS_ChartsVC_Loops_Colors 											= 0;
									}	
								}
							} else if (($chart_multiple == "true") && (($chart_type == "pie") || ($chart_type == "doughnut") || ($chart_type == "polarArea"))) {
								if (($TS_ChartsVC_Loops_DataSets == 0) && (in_array($col + 1, $TS_ChartsVC_Source_LabelColors)) && (!in_array($row + 1, $TS_ChartsVC_Source_SetNames)) && (!in_array($row + 1, $TS_ChartsVC_Source_SetColors)) && (!in_array($row + 1, $TS_ChartsVC_Source_Ignore))) {
									if ($chart_colors == "tablecheck") {
										$TS_ChartsVC_Data_Colors											= preg_replace('/\s/', ' ', $TS_ChartsVC_Editor_Meta[$TS_ChartsVC_Editor_Cells]->className);
										$TS_ChartsVC_Data_Colors											= explode(" ", $TS_ChartsVC_Data_Colors);							
										$TS_ChartsVC_Data_Colors 											= preg_grep('/\\bbackground-?\\b/i', $TS_ChartsVC_Data_Colors);
										$TS_ChartsVC_Data_Keys    											= array_keys($TS_ChartsVC_Data_Colors);
									}
									if (($chart_colors == "allrandom") || ((count($TS_ChartsVC_Data_Colors) == 0) || (count($TS_ChartsVC_Data_Keys) == 0))) {
										if (($colors_luminosity == "random") && ($colors_hue == "random")) {
											$TS_ChartsVC_Data_Colors										= $this->TS_TablesWP_ColorMakerRandomHEX();
										} else {
											$TS_ChartsVC_Data_Colors										= $this->TS_TablesWP_ColorMakerSingle(array('luminosity' => $colors_luminosity,'hue' => $colors_hue));
										}	
									} else {
										$TS_ChartsVC_Data_Colors											= str_replace("background-", "", $TS_ChartsVC_Data_Colors[$TS_ChartsVC_Data_Keys[0]]);
									}
									// Set Background Color
									$TS_ChartsVC_Data_Holder 												= $TS_ChartsVC_Source_Full[$TS_ChartsVC_Loops_DataSets]['backgroundColor'];
									if (!is_array($TS_ChartsVC_Data_Holder)) {
										$TS_ChartsVC_Data_Holder 											= array();
									}
									array_push($TS_ChartsVC_Data_Holder, $this->TS_TablesWP_ColorMakerCreateRGBA($TS_ChartsVC_Data_Colors, $opacity_background));
									for ($stack = 0; $stack < $TS_ChartsVC_Source_Stacks; $stack++) {
										$TS_ChartsVC_Source_Full[$stack]['backgroundColor'] 				= $TS_ChartsVC_Data_Holder;
									}
									// Set Border Color
									$TS_ChartsVC_Data_Holder 												= $TS_ChartsVC_Source_Full[$TS_ChartsVC_Loops_DataSets]['borderColor'];
									if (!is_array($TS_ChartsVC_Data_Holder)) {
										$TS_ChartsVC_Data_Holder 											= array();
									}
									array_push($TS_ChartsVC_Data_Holder, $this->TS_TablesWP_ColorMakerCreateRGBA($TS_ChartsVC_Data_Colors, $opacity_border));
									for ($stack = 0; $stack < $TS_ChartsVC_Source_Stacks; $stack++) {
										$TS_ChartsVC_Source_Full[$stack]['borderColor'] 					= $TS_ChartsVC_Data_Holder;
									}
									// Set Line + Points Color
									if (($chart_type == "line") || ($chart_type == "radar")) {							
										$TS_ChartsVC_Data_Holder 											= $TS_ChartsVC_Source_Full[$TS_ChartsVC_Loops_DataSets]['pointBorderColor'];
										if (!is_array($TS_ChartsVC_Data_Holder)) {
											$TS_ChartsVC_Data_Holder 										= array();
										}
										array_push($TS_ChartsVC_Data_Holder, $this->TS_TablesWP_ColorMakerCreateRGBA($TS_ChartsVC_Data_Colors, $opacity_line));
										for ($stack = 0; $stack < $TS_ChartsVC_Source_Stacks; $stack++) {
											$TS_ChartsVC_Source_Full[$stack]['pointBorderColor']			= $TS_ChartsVC_Data_Holder;
										}
										$TS_ChartsVC_Data_Holder 											= $TS_ChartsVC_Source_Full[$TS_ChartsVC_Loops_DataSets]['pointBackgroundColor'];
										if (!is_array($TS_ChartsVC_Data_Holder)) {
											$TS_ChartsVC_Data_Holder 										= array();
										}
										array_push($TS_ChartsVC_Data_Holder, $this->TS_TablesWP_ColorMakerCreateRGBA($TS_ChartsVC_Data_Colors, $opacity_points));
										for ($stack = 0; $stack < $TS_ChartsVC_Source_Stacks; $stack++) {
											$TS_ChartsVC_Source_Full[$stack]['pointBackgroundColor']		= $TS_ChartsVC_Data_Holder;
										}
									}
									$TS_ChartsVC_Loops_Colors++;
									if ($TS_ChartsVC_Loops_Colors >= $TS_ChartsVC_Source_Stacks) {
										$TS_ChartsVC_Loops_Colors 											= 0;
									}	
								}
							} else if (($chart_multiple == "true") && ($chart_type != "pie") && ($chart_type != "doughnut")) {
								if ((in_array($row + 1, $TS_ChartsVC_Source_SetColors)) && (in_array($col + 1, $TS_ChartsVC_Source_DataSets)) && (!in_array($row + 1, $TS_ChartsVC_Source_SetNames)) && (!in_array($row + 1, $TS_ChartsVC_Source_Ignore))) {
									if ($chart_colors == "tablecheck") {
										$TS_ChartsVC_Data_Colors											= preg_replace('/\s/', ' ', $TS_ChartsVC_Editor_Meta[$TS_ChartsVC_Editor_Cells]->className);
										$TS_ChartsVC_Data_Colors											= explode(" ", $TS_ChartsVC_Data_Colors);							
										$TS_ChartsVC_Data_Colors 											= preg_grep('/\\bbackground-?\\b/i', $TS_ChartsVC_Data_Colors);
										$TS_ChartsVC_Data_Keys    											= array_keys($TS_ChartsVC_Data_Colors);
									}
									if (($chart_colors == "allrandom") || ((count($TS_ChartsVC_Data_Colors) == 0) || (count($TS_ChartsVC_Data_Keys) == 0))) {
										if (($colors_luminosity == "random") && ($colors_hue == "random")) {
											$TS_ChartsVC_Data_Colors										= $this->TS_TablesWP_ColorMakerRandomHEX();
										} else {
											$TS_ChartsVC_Data_Colors										= $this->TS_TablesWP_ColorMakerSingle(array('luminosity' => $colors_luminosity,'hue' => $colors_hue));
										}	
									} else {
										$TS_ChartsVC_Data_Colors											= str_replace("background-", "", $TS_ChartsVC_Data_Colors[$TS_ChartsVC_Data_Keys[0]]);
									}
									array_push($TS_ChartsVC_Data_Transfer, $TS_ChartsVC_Data_Colors);
								}
							}
							// Retrieve Chart Values
							if ((in_array($col + 1, $TS_ChartsVC_Source_DataSets)) && (!in_array($row + 1, $TS_ChartsVC_Source_SetNames)) && (!in_array($row + 1, $TS_ChartsVC_Source_SetColors)) && (!in_array($row + 1, $TS_ChartsVC_Source_Ignore))) {
								$TS_ChartsVC_Data_Values													= $TS_ChartsVC_Source_Full[$TS_ChartsVC_Loops_DataSets]['data'];
								array_push($TS_ChartsVC_Data_Values, $TS_ChartsVC_Editor_Meta[$TS_ChartsVC_Editor_Cells]->value);
								$TS_ChartsVC_Source_Full[$TS_ChartsVC_Loops_DataSets]['data'] 				= $TS_ChartsVC_Data_Values;
								$TS_ChartsVC_Loops_DataSets++;
								if ($TS_ChartsVC_Loops_DataSets >= $TS_ChartsVC_Source_Stacks) {
									$TS_ChartsVC_Loops_DataSets 											= 0;
								}
							}
							$TS_ChartsVC_Editor_Cells++;
						}
					}
			
					// Retrieve DataSet Colors
					$TS_ChartsVC_Loops_DataSets																= 0;
					if (($chart_multiple == "true") && ($chart_type != "pie") && ($chart_type != "doughnut") && ($chart_type != "polarArea") && ($TS_ChartsVC_Source_Stacks > 1)) {
						for ($stack = 0; $stack < $TS_ChartsVC_Source_Stacks; $stack++) {
							$TS_ChartsVC_Source_Full[$stack]['backgroundColor'] 							= $this->TS_TablesWP_ColorMakerCreateRGBA($TS_ChartsVC_Data_Transfer[$stack], $opacity_background);
							$TS_ChartsVC_Source_Full[$stack]['borderColor'] 								= $this->TS_TablesWP_ColorMakerCreateRGBA($TS_ChartsVC_Data_Transfer[$stack], $opacity_border);
							if (($chart_type == "line") || ($chart_type == "radar")) {
								$TS_ChartsVC_Source_Full[$stack]['pointBorderColor']						= $this->TS_TablesWP_ColorMakerCreateRGBA($TS_ChartsVC_Data_Transfer[$stack], $opacity_line);
								$TS_ChartsVC_Source_Full[$stack]['pointBorderColor']						= $this->TS_TablesWP_ColorMakerCreateRGBA($TS_ChartsVC_Data_Transfer[$stack], $opacity_points);
							}
						}
					}
					
					// Chart Data Attributes
					$TS_ChartsVC_Data_Attributes				= 'data-chart-type="' . $chart_type . '" data-chart-height="400"';
					$TS_ChartsVC_Data_Attributes				.= ' data-chart-labels="' . base64_encode(json_encode($TS_ChartsVC_Data_Labels)) . '"';
					$TS_ChartsVC_Data_Attributes				.= ' data-chart-datasets="' . base64_encode(json_encode($TS_ChartsVC_Source_Full)) . '"';
					$TS_ChartsVC_Data_Attributes				.= ' data-chart-tooltips="' . base64_encode(json_encode($TS_ChartsVC_Data_Tooltips)) . '"';		
					$TS_ChartsVC_Data_Attributes				.= ' data-chart-legendshow="' . $legend_show . '" data-chart-legendposition="' . $legend_position . '" data-chart-legendfullwidth="' . $legend_fullwidth . '"';
					$TS_ChartsVC_Data_Attributes				.= ' data-chart-stacked="' . $chart_stacked . '" data-chart-filled="' . $chart_filled . '" data-chart-startzero="' . $chart_startzero . '"';
					$TS_ChartsVC_Data_Attributes				.= ' data-chart-labelxshow="' . $label_xaxis_show . '" data-chart-labelxaxis="' . $label_xaxis_text . '" data-chart-labelyshow="' . $label_yaxis_show . '" data-chart-labelyaxis="' . $label_yaxis_text . '"';
					
					// Tooltip Attributes
					$tooltip_base								= '';
					if (($numbers_type == "currency") && ($numbers_placement == "prefix")) {
						$tooltip_base							.= '$';
						if ($numbers_space_currency == "true") {
							$tooltip_base						.= ' ';
						}
					}
					$tooltip_base								.= '0,0';
					if ($tooltip_decimals > 0) {
						$tooltip_base							.= '.'; 
					}
					for ($decimals = 0; $decimals < $tooltip_decimals; $decimals++) {
						$tooltip_base							.= '0';
					}
					if ($numbers_type == "percent") {
						if ($numbers_space_percent == "true") {
							$tooltip_base						.= ' ';
						}
						$tooltip_base							.= '%';
					} else if (($numbers_type == "currency") && ($numbers_placement == "postfix")) {
						if ($numbers_space_currency == "true") {
							$tooltip_base						.= ' ';
						}
						$tooltip_base							.= '$';
					}
					$TS_ChartsVC_Data_Attributes				.= ' data-tooltip-format="' . $tooltip_format . '" data-tooltip-type="' . $numbers_type . '" data-tooltip-symbol="' . $numbers_symbol . '"';
					$TS_ChartsVC_Data_Attributes				.= ' data-tooltip-base="' . $tooltip_base . '" data-tooltip-date="' . $numbers_date . '" data-tooltip-locale="' . $numbers_locale . '"';
					
					// Ticks Attributes
					$ticks_base									= '';
					if (($numbers_type == "currency") && ($numbers_placement == "prefix")) {
						$ticks_base								.= '$';
						if ($numbers_space_currency == "true") {
							$ticks_base							.= ' ';
						}
					}
					$ticks_base									.= '0,0';
					if ($ticks_decimals > 0) {
						$ticks_base								.= '.'; 
					}
					for ($decimals = 0; $decimals < $ticks_decimals; $decimals++) {
						$ticks_base								.= '0';
					}
					if ($numbers_type == "percent") {
						if ($numbers_space_percent == "true") {
							$ticks_base							.= ' ';
						}
						$ticks_base								.= '%';
					} else if (($numbers_type == "currency") && ($numbers_placement == "postfix")) {
						if ($numbers_space_currency == "true") {
							$ticks_base							.= ' ';
						}
						$ticks_base								.= '$';
					}
					$TS_ChartsVC_Data_Attributes				.= ' data-ticks-format="' . $ticks_format . '" data-ticks-type="' . $numbers_type . '" data-ticks-symbol="' . $numbers_symbol . '"';
					$TS_ChartsVC_Data_Attributes				.= ' data-ticks-base="' . $ticks_base . '" data-ticks-date="' . $numbers_date . '" data-ticks-locale="' . $numbers_locale . '"';
				}
		
				// Visual Composer Override
				if (function_exists('vc_shortcode_custom_css_class')) {
					$css_class 								= apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, ' ' . vc_shortcode_custom_css_class($css, ' '), 'TS_Advanced_Charts', $atts);
				} else {
					$css_class								= '';
				}
				
				// Generate Final Output
				if ($TS_ADVANCED_TABLESWP->TS_TablesWP_VisualComposer_Frontend == "true") {
					$output .= '<div id="' . $container_id . '" class="ts-advanced-charts-container ts-advanced-charts-frontendeditor ' . $css_class . ' ' . $el_class . '">';
						// Frontend Editor Message
						$output .= '<div id="ts-advanced-charts-frontend-message-' . $randomizer . '" class="ts-advanced-charts-frontend-message">';
							$output .= __('For performance and compatibility reasons, this chart will not be rendered when using the WP Bakery Page Builder (formerly Visual Composer) frontend editor.', 'ts_visual_composer_extend');
						$output .= '</div>';
						// Frontend Editor Details
						$output .= '<div id="ts-advanced-charts-frontend-data-' . $randomizer . '" class="ts-advanced-charts-frontend-data">';
							if ($id != '') {
								$output .= __('Table ID:', 'ts_visual_composer_extend') . ' ' . $id . '</br>';
								$output .= __('Chart Type:', 'ts_visual_composer_extend') . ' ' . ucfirst($chart_type) . '</br>';
							} else {
								$output .=  __('Table ID:', 'ts_visual_composer_extend') . ' ' . __('Please select a table as data source first!', 'ts_visual_composer_extend');
							}					
						$output .= '</div>';
					$output .= '</div>';
				} else {
					$output .= '<div id="' . $container_id . '" class="ts-advanced-charts-container ts-advanced-charts-processing ' . $css_class . ' ' . $el_class . '">';
						// Chart Preloader
						if (($TS_ADVANCED_TABLESWP->TS_TablesWP_VisualComposer_Frontend == "false") && ($preloader_use == "true")) {
							$output .= '<div id="ts-advanced-charts-preloader-wrapper-' . $randomizer . '" class="ts-advanced-charts-preloader-wrapper" style="background: ' . $preloader_background . ';">';
								$output .= TS_TablesWP_CreatePreloaderCSS("ts-advanced-charts-preloader-"  . $randomizer, "", $preloader_style, "false");
							$output .= '</div>';
						}
						// Debug Output
						if ($chart_debug == "true") {
							$output .= '<div id="ts-advanced-charts-debug-wrapper-' . $randomizer . '" class="ts-advanced-charts-debug-wrapper">';
								$output .= '<textarea id="ts-advanced-charts-debug-holder-' . $randomizer . '" class="ts-advanced-charts-debug-holder">';
									$output .= 'labels: ' . json_encode($TS_ChartsVC_Data_Labels) . ',&#013;';
									$output .= 'datasets: ' . json_encode($TS_ChartsVC_Source_Full);
								$output .= '</textarea>';
							$output .= '</div>';
						}
						// Chart Element
						$output .= '<div id="ts-advanced-charts-wrapper-' . $randomizer . '" class="ts-advanced-charts-wrapper ts-advanced-charts-' . $chart_type . '" style="background: ' . $chart_background . '; padding: ' . $chart_padding . 'px; height: ' . $chart_height . 'px;" ' . $TS_ChartsVC_Data_Attributes . '>';
							$output .= '<canvas id="ts-advanced-charts-holder-' . $randomizer . '" class="ts-advanced-charts-holder" width="100%" height="' . $chart_height . '" style="background: ' . $chart_background . '; width: 100%; height: ' . $chart_height . 'px;"></canvas>';
						$output .= '</div>';
					$output .= '</div>';
				}
				
				echo $output;
				
				$myvariable = ob_get_clean();
				return $myvariable;
			}
			
			
			/* ---------------------------- */
			/* Color Maker Helper Functions */
			/* ---------------------------- */			
			// hue: 			red, orange, yellow, green, blue, purple, pink and monochrome (array of multiple hues or specific hue 0 - 360)
			// luminosity: 		bright, light or dark
			// format:			hsv, hsl, hslCss, rgb, rgbCss, and hex			
			static public function TS_TablesWP_ColorMakerSingle($options = array()) {
				$h = self::TS_TablesWP_ColorMakerPickHue($options);
				$s = self::TS_TablesWP_ColorMakerPickSaturation($h, $options);
				$v = self::TS_TablesWP_ColorMakerPickBrightness($h, $s, $options);    
				return self::TS_TablesWP_ColorMakerFormat(compact('h','s','v'), @$options['format']);
			}	  
			static public function TS_TablesWP_ColorMakerMany($count, $options = array()) {
				$colors = array();    
				for ($i = 0; $i < $count; $i++) {
					$colors[] = self::TS_TablesWP_ColorMakerSingle($options);
				}    
				return $colors;
			}	  
			static public function TS_TablesWP_ColorMakerFormat($hsv, $format='hex') {
				switch ($format) {
					case 'hsv':
						return $hsv;      
					case 'hsl':
						return self::TS_TablesWP_ColorMakerHSV2HSL($hsv);      
					case 'hslCss':
						$hsl = self::TS_TablesWP_ColorMakerHSV2HSL($hsv);
						return 'hsl(' . $hsl['h'] . ',' . $hsl['s'] . '%,' . $hsl['l'] . '%)';      
					case 'rgb':
						return self::TS_TablesWP_ColorMakerHSV2RGB($hsv);      
					case 'rgbCss':
						return 'rgb(' . implode(',', self::TS_TablesWP_ColorMakerHSV2RGB($hsv)) . ')';      
					case 'hex':
					default:
						return self::TS_TablesWP_ColorMakerHSV2HEX($hsv);
				}
			}	  
			static private function TS_TablesWP_ColorMakerPickHue($options) {
				$range = self::TS_TablesWP_ColorMakerGetHueRange($options);    
				if (empty($range)) {
					return 0;
				}    
				$hue = self::TS_TablesWP_ColorMakerRandomizer($range, $options);    
				// Instead of storing red as two separate ranges,
				// we group them, using negative numbers
				if ($hue < 0) {
					$hue = 360 + $hue;
				}    
				return $hue;
			}	  
			static private function TS_TablesWP_ColorMakerPickSaturation($h, $options) {
				if (@$options['luminosity'] === 'random') {
					return self::TS_TablesWP_ColorMakerRandomizer(array(0, 100), $options);
				}
				if (@$options['hue'] === 'monochrome') {
					return 0;
				}    
				$colorInfo = self::TS_TablesWP_ColorMakerGetColorInfo($h);
				$range = $colorInfo['s'];    
				switch (@$options['luminosity']) {
					case 'bright':
						$range[0] = 55;
						break;      
					case 'dark':
						$range[0] = $range[1] - 10;
						break;      
					case 'light':
						$range[1] = 55;
						break;
				}    
				return self::TS_TablesWP_ColorMakerRandomizer($range, $options);
			}	  
			static private function TS_TablesWP_ColorMakerPickBrightness($h, $s, $options) {
				if (@$options['luminosity'] === 'random') {
					$range = array(0, 100);
				} else {
					$range = array(
						self::TS_TablesWP_ColorMakerGetMinBrightness($h, $s),
						100
					);      
					switch (@$options['luminosity']) {
						case 'dark':
							$range[1] = $range[0] + 20;
							break;        
						case 'light':
							$range[0] = ($range[1] + $range[0]) / 2;
							break;
					}
				}    
				return self::TS_TablesWP_ColorMakerRandomizer($range, $options);
			}	  
			static private function TS_TablesWP_ColorMakerGetHueRange($options) {
				$ranges = array();    
				if (isset($options['hue'])) {
					if (!is_array($options['hue'])) {
						$options['hue'] = array($options['hue']);
					}      
					foreach ($options['hue'] as $hue) {
						if ($hue === 'random') {
							$ranges[] = array(0, 360);
						} else if (isset(self::$TS_TablesWP_ColorDictionary[$hue])) {
							$ranges[] = self::$TS_TablesWP_ColorDictionary[$hue]['h'];
						} else if (is_numeric($hue)) {
							$hue = intval($hue);        
							if ($hue <= 360 && $hue >= 0) {
								$ranges[] = array($hue, $hue);
							}
						}
					}
				}    
				if (($l = count($ranges)) === 0) {
					return array(0, 360);
				} else if ($l === 1) {
					return $ranges[0];
				} else {
					return $ranges[self::TS_TablesWP_ColorMakerRandomizer(array(0, $l-1), $options)];
				}
			}	  
			static private function TS_TablesWP_ColorMakerGetMinBrightness($h, $s) {
				$colorInfo = self::TS_TablesWP_ColorMakerGetColorInfo($h);
				$bounds = $colorInfo['bounds'];    
				for ($i = 0, $l = count($bounds); $i < $l - 1; $i++) {
					$s1 = $bounds[$i][0];
					$v1 = $bounds[$i][1];
					$s2 = $bounds[$i+1][0];
					$v2 = $bounds[$i+1][1];      
					if ($s >= $s1 && $s <= $s2) {
						$m = ($v2 - $v1) / ($s2 - $s1);
						$b = $v1 - $m * $s1;
						return $m * $s + $b;
					}
				}    
				return 0;
			}	  
			static private function TS_TablesWP_ColorMakerGetColorInfo($h) {
				// Maps red colors to make picking hue easier
				if ($h >= 334 && $h <= 360) {
					$h-= 360;
				}    
				foreach (self::$TS_TablesWP_ColorDictionary as $color) {
					if ($color['h'] !== null && $h >= $color['h'][0] && $h <= $color['h'][1]) {
						return $color;
					}
				}
			}		  
			static private function TS_TablesWP_ColorMakerRandomizer($bounds, $options) {
				if (isset($options['prng'])) {
					return $options['prng']($bounds[0], $bounds[1]);
				} else {
					return mt_rand($bounds[0], $bounds[1]);
				}
			}	  
			static public function TS_TablesWP_ColorMakerHSV2HEX($hsv) {
				$rgb = self::TS_TablesWP_ColorMakerHSV2RGB($hsv);
				$hex = '#';    
				foreach ($rgb as $c) {
					$hex.= str_pad(dechex($c), 2, '0', STR_PAD_LEFT);
				}    
				return $hex;
			}		  
			static public function TS_TablesWP_ColorMakerHSV2HSL($hsv) {
				extract($hsv);    
				$s/= 100;
				$v/= 100;
				$k = (2-$s)*$v;    
				return array(
					'h' => $h,
					's' => round($s*$v / ($k < 1 ? $k : 2-$k), 4) * 100,
					'l' => $k/2 * 100,
				);
			}	  
			static public function TS_TablesWP_ColorMakerHSV2RGB($hsv) {
				extract($hsv);    
				$h/= 360;
				$s/= 100;
				$v/= 100;    
				$i = floor($h * 6);
				$f = $h * 6 - $i;    
				$m = $v * (1 - $s);
				$n = $v * (1 - $s * $f);
				$k = $v * (1 - $s * (1 - $f));    
				$r = 1;
				$g = 1;
				$b = 1;    
				switch ($i) {
					case 0:
						list($r,$g,$b) = array($v,$k,$m);
						break;
					case 1:
						list($r,$g,$b) = array($n,$v,$m);
						break;
					case 2:
						list($r,$g,$b) = array($m,$v,$k);
						break;
					case 3:
						list($r,$g,$b) = array($m,$n,$v);
						break;
					case 4:
						list($r,$g,$b) = array($k,$m,$v);
						break;
					case 5:
					case 6:
						list($r,$g,$b) = array($v,$m,$n);
						break;
				}    
				return array(
					'r' => floor($r*255),
					'g' => floor($g*255),
					'b' => floor($b*255),
				);
			}
			static public function TS_TablesWP_ColorMakerHSV2RGBA($hsv, $opacity) {
				extract($hsv);    
				$h/= 360;
				$s/= 100;
				$v/= 100;    
				$i = floor($h * 6);
				$f = $h * 6 - $i;    
				$m = $v * (1 - $s);
				$n = $v * (1 - $s * $f);
				$k = $v * (1 - $s * (1 - $f));    
				$r = 1;
				$g = 1;
				$b = 1;    
				switch ($i) {
					case 0:
						list($r,$g,$b) = array($v,$k,$m);
						break;
					case 1:
						list($r,$g,$b) = array($n,$v,$m);
						break;
					case 2:
						list($r,$g,$b) = array($m,$v,$k);
						break;
					case 3:
						list($r,$g,$b) = array($m,$n,$v);
						break;
					case 4:
						list($r,$g,$b) = array($k,$m,$v);
						break;
					case 5:
					case 6:
						list($r,$g,$b) = array($v,$m,$n);
						break;
				}    
				return array(
					'r' => floor($r*255),
					'g' => floor($g*255),
					'b' => floor($b*255),
					'a' => $opacity,
				);
			}
			static public function TS_TablesWP_ColorMakerRandomHEX() {
				$possibilities 								= array(1, 2, 3, 4, 5, 6, 7, 8, 9, "A", "B", "C", "D", "E", "F");
				shuffle($possibilities);
				$color 										= "#";
				for ($i = 1; $i <= 6; $i++){
					$color .= $possibilities[rand(0, 14)];
				}
				return $color;
			}
			static public function TS_TablesWP_ColorMakerCreateRGBA($color, $opacity = false) {
				// Return Random if no Color provided
				if ((empty($color)) || ($color == "")) {
					$possibilities = array(1, 2, 3, 4, 5, 6, 7, 8, 9, "A", "B", "C", "D", "E", "F" );
					shuffle($possibilities);
					$color = "#";
					for ($i=1;$i<=6;$i++){
						$color .= $possibilities[rand(0,14)];
					}
				}
				// Sanitize $color if "#" is provided 
				if ($color[0] == '#' ) {
					$color = substr( $color, 1 );
				} 
				// Check if color has 6 or 3 characters and get values
				if (strlen($color) == 6) {
					$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
				} elseif (strlen( $color ) == 3) {
					$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
				} else {
					return $default;
				} 
				// Convert hexadec to rgb
				$rgb =  array_map('hexdec', $hex); 
				// Check if opacity is set(rgba or rgb)
				if ($opacity) {
					if (abs($opacity) > 1) {
						$opacity = 1.0;
					}
					$output = 'rgba(' . implode(",", $rgb) . ',' . $opacity . ')';
				} else {
					$output = 'rgb(' . implode(",", $rgb) . ')';
				} 
				// Return rgb(a) color string
				return $output;
			}
			static public function TS_TablesWP_ColorMakerName2HEX($colorname) {
				$colorname 									= strtolower($colorname);
				// Standard 147 HTML Color Names
				$colors = array(
					'aliceblue'								=> 'F0F8FF',
					'antiquewhite'							=> 'FAEBD7',
					'aqua'									=> '00FFFF',
					'aquamarine'							=> '7FFFD4',
					'azure'									=> 'F0FFFF',
					'beige'									=> 'F5F5DC',
					'bisque'								=> 'FFE4C4',
					'black'									=> '000000',
					'blanchedalmond '						=> 'FFEBCD',
					'blue'									=> '0000FF',
					'blueviolet'							=> '8A2BE2',
					'brown'									=> 'A52A2A',
					'burlywood'								=> 'DEB887',
					'cadetblue'								=> '5F9EA0',
					'chartreuse'							=> '7FFF00',
					'chocolate'								=> 'D2691E',
					'coral'									=> 'FF7F50',
					'cornflowerblue'						=> '6495ED',
					'cornsilk'								=> 'FFF8DC',
					'crimson'								=> 'DC143C',
					'cyan'									=> '00FFFF',
					'darkblue'								=> '00008B',
					'darkcyan'								=> '008B8B',
					'darkgoldenrod'							=> 'B8860B',
					'darkgray'								=> 'A9A9A9',
					'darkgreen'								=> '006400',
					'darkgrey'								=> 'A9A9A9',
					'darkkhaki'								=> 'BDB76B',
					'darkmagenta'							=> '8B008B',
					'darkolivegreen'						=> '556B2F',
					'darkorange'							=> 'FF8C00',
					'darkorchid'							=> '9932CC',
					'darkred'								=> '8B0000',
					'darksalmon'							=> 'E9967A',
					'darkseagreen'							=> '8FBC8F',
					'darkslateblue'							=> '483D8B',
					'darkslategray'							=> '2F4F4F',
					'darkslategrey'							=> '2F4F4F',
					'darkturquoise'							=> '00CED1',
					'darkviolet'							=> '9400D3',
					'deeppink'								=> 'FF1493',
					'deepskyblue'							=> '00BFFF',
					'dimgray'								=> '696969',
					'dimgrey'								=> '696969',
					'dodgerblue'							=> '1E90FF',
					'firebrick'								=> 'B22222',
					'floralwhite'							=> 'FFFAF0',
					'forestgreen'							=> '228B22',
					'fuchsia'								=> 'FF00FF',
					'gainsboro'								=> 'DCDCDC',
					'ghostwhite'							=> 'F8F8FF',
					'gold'									=> 'FFD700',
					'goldenrod'								=> 'DAA520',
					'gray'									=> '808080',
					'green'									=> '008000',
					'greenyellow'							=> 'ADFF2F',
					'grey'									=> '808080',
					'honeydew'								=> 'F0FFF0',
					'hotpink'								=> 'FF69B4',
					'indianred'								=> 'CD5C5C',
					'indigo'								=> '4B0082',
					'ivory'									=> 'FFFFF0',
					'khaki'									=> 'F0E68C',
					'lavender'								=> 'E6E6FA',
					'lavenderblush'							=> 'FFF0F5',
					'lawngreen'								=> '7CFC00',
					'lemonchiffon'							=> 'FFFACD',
					'lightblue'								=> 'ADD8E6',
					'lightcoral'							=> 'F08080',
					'lightcyan'								=> 'E0FFFF',
					'lightgoldenrodyellow'					=> 'FAFAD2',
					'lightgray'								=> 'D3D3D3',
					'lightgreen'							=> '90EE90',
					'lightgrey'								=> 'D3D3D3',
					'lightpink'								=> 'FFB6C1',
					'lightsalmon'							=> 'FFA07A',
					'lightseagreen'							=> '20B2AA',
					'lightskyblue'							=> '87CEFA',
					'lightslategray'						=> '778899',
					'lightslategrey'						=> '778899',
					'lightsteelblue'						=> 'B0C4DE',
					'lightyellow'							=> 'FFFFE0',
					'lime'									=> '00FF00',
					'limegreen'								=> '32CD32',
					'linen'									=> 'FAF0E6',
					'magenta'								=> 'FF00FF',
					'maroon'								=> '800000',
					'mediumaquamarine'						=> '66CDAA',
					'mediumblue'							=> '0000CD',
					'mediumorchid'							=> 'BA55D3',
					'mediumpurple'							=> '9370D0',
					'mediumseagreen'						=> '3CB371',
					'mediumslateblue'						=> '7B68EE',
					'mediumspringgreen'						=> '00FA9A',
					'mediumturquoise'						=> '48D1CC',
					'mediumvioletred'						=> 'C71585',
					'midnightblue'							=> '191970',
					'mintcream'								=> 'F5FFFA',
					'mistyrose'								=> 'FFE4E1',
					'moccasin'								=> 'FFE4B5',
					'navajowhite'							=> 'FFDEAD',
					'navy'									=> '000080',
					'oldlace'								=> 'FDF5E6',
					'olive'									=> '808000',
					'olivedrab'								=> '6B8E23',
					'orange'								=> 'FFA500',
					'orangered'								=> 'FF4500',
					'orchid'								=> 'DA70D6',
					'palegoldenrod'							=> 'EEE8AA',
					'palegreen'								=> '98FB98',
					'paleturquoise'							=> 'AFEEEE',
					'palevioletred'							=> 'DB7093',
					'papayawhip'							=> 'FFEFD5',
					'peachpuff'								=> 'FFDAB9',
					'peru'									=> 'CD853F',
					'pink'									=> 'FFC0CB',
					'plum'									=> 'DDA0DD',
					'powderblue'							=> 'B0E0E6',
					'purple'								=> '800080',
					'red'									=> 'FF0000',
					'rosybrown'								=> 'BC8F8F',
					'royalblue'								=> '4169E1',
					'saddlebrown'							=> '8B4513',
					'salmon'								=> 'FA8072',
					'sandybrown'							=> 'F4A460',
					'seagreen'								=> '2E8B57',
					'seashell'								=> 'FFF5EE',
					'sienna'								=> 'A0522D',
					'silver'								=> 'C0C0C0',
					'skyblue'								=> '87CEEB',
					'slateblue'								=> '6A5ACD',
					'slategray'								=> '708090',
					'slategrey'								=> '708090',
					'snow'									=> 'FFFAFA',
					'springgreen'							=> '00FF7F',
					'steelblue'								=> '4682B4',
					'tan'									=> 'D2B48C',
					'teal'									=> '008080',
					'thistle'								=> 'D8BFD8',
					'tomato'								=> 'FF6347',
					'turquoise'								=> '40E0D0',
					'violet'								=> 'EE82EE',
					'wheat'									=> 'F5DEB3',
					'white'									=> 'FFFFFF',
					'whitesmoke'							=> 'F5F5F5',
					'yellow'								=> 'FFFF00',
					'yellowgreen'							=> '9ACD32',
				);
				if (isset($colors[$colorname])) {
					return ('#' . $colors[$colorname]);
				} else {
					return false;
				}
			}
			static public function TS_TablesWP_ColorMakerRGB2HEX2RGB($color){ 
				if (!$color) return false; 
				$color 										= trim($color); 
				$result 									= false; 
				if (preg_match("/^[0-9ABCDEFabcdef\#]+$/i", $color)){
					$hex 									= str_replace('#','', $color);
					if (!$hex) return false;
					if(strlen($hex) == 3):
						$result['r'] 						= hexdec(substr($hex,0,1).substr($hex,0,1));
						$result['g'] 						= hexdec(substr($hex,1,1).substr($hex,1,1));
						$result['b'] 						= hexdec(substr($hex,2,1).substr($hex,2,1));
					else:
						$result['r'] 						= hexdec(substr($hex,0,2));
						$result['g'] 						= hexdec(substr($hex,2,2));
						$result['b'] 						= hexdec(substr($hex,4,2));
					endif;       
				} elseif (preg_match("/^[0-9]+(,| |.)+[0-9]+(,| |.)+[0-9]+$/i", $color)) { 
					$rgbstr 								= str_replace(array(',', ' ', '.'), ':', $color); 
					$rgbarr 								= explode(":", $rgbstr);
					$result 								= '#';
					$result 								.= str_pad(dechex($rgbarr[0]), 2, "0", STR_PAD_LEFT);
					$result 								.= str_pad(dechex($rgbarr[1]), 2, "0", STR_PAD_LEFT);
					$result 								.= str_pad(dechex($rgbarr[2]), 2, "0", STR_PAD_LEFT);
					$result 								= strtoupper($result); 
				} else {
					$result 								= false;
				}
				return $result; 
			}
		}
	}
	if (class_exists('TS_Tablenator_Shortcode_Chart')) {
		$TS_Tablenator_Shortcode_Chart = new TS_Tablenator_Shortcode_Chart;
	}
?>