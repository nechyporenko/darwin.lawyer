<?php
    global $TS_ADVANCED_TABLESWP;
    
    // Icon Font Files
    // ---------------
    foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_Installed_Icon_Fonts as $Icon_Font => $iconfont) {
        if ($iconfont != "Custom") {
            wp_register_style('ts-font-' . strtolower($iconfont),				$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'css/fonts/ts-font-' . strtolower($iconfont) . '.css', null, false, 'all');
        } else if ($iconfont == "Custom") {
            $Custom_Font_CSS                                                    = get_option('ts_tablesplus_extend_settings_tinymceCustomPath', '');
            wp_register_style('ts-font-' . strtolower($iconfont) . 'vcsc', 		$Custom_Font_CSS, null, false, 'all');
        }
    }
    // Check if VC Internal Font Files are Registered
    if ((function_exists('vc_asset_url')) && (defined('WPB_VC_VERSION'))) {
        foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_VisualComposer_Icon_Fonts as $Icon_Font => $iconfont) {
            if (strtolower($iconfont) == "vc_awesome") {
                if (!wp_style_is('font-awesome', 'registered')) {
                    wp_register_style('font-awesome', vc_asset_url('lib/bower/font-awesome/css/font-awesome.min.css'), array(), WPB_VC_VERSION);
                }
            } else if (strtolower($iconfont) == "vc_entypo") {
                if (!wp_style_is('vc_entypo', 'registered')) {
                    wp_register_style('vc_entypo', vc_asset_url('css/lib/vc-entypo/vc_entypo.min.css'), false, WPB_VC_VERSION);
                }
            } else if (strtolower($iconfont) == "vc_linecons") {
                if (!wp_style_is('vc_linecons', 'registered')) {
                    wp_register_style('vc_linecons', vc_asset_url('css/lib/vc-linecons/vc_linecons_icons.min.css'), false, WPB_VC_VERSION);
                }
            } else if (strtolower($iconfont) == "vc_openiconic") {
                if (!wp_style_is('vc_openiconic', 'registered')) {
                    wp_register_style('vc_openiconic', vc_asset_url('css/lib/vc-open-iconic/vc_openiconic.min.css'), false, WPB_VC_VERSION);
                }
            } else if (strtolower($iconfont) == "vc_typicons") {
                if (!wp_style_is('vc_typicons', 'registered')) {
                    wp_register_style('vc_typicons', vc_asset_url('css/lib/typicons/src/font/typicons.min.css'), false, WPB_VC_VERSION);
                }
            } else if (strtolower($iconfont) == "vc_monosocial") {
                if ((!wp_style_is('vc_monosocialiconsfont', 'registered')) && (TS_TablesWP_VersionCompare(WPB_VC_VERSION, '4.11.0') >= 0)) {
                    wp_register_style('vc_monosocialiconsfont', vc_asset_url('css/lib/monosocialiconsfont/monosocialiconsfont.min.css'), false, WPB_VC_VERSION);
                }
            } else if (strtolower($iconfont) == "vc_material") {
                if ((!wp_style_is('vc_material', 'registered')) && (TS_TablesWP_VersionCompare(WPB_VC_VERSION, '5.0.0') >= 0)) {
                    wp_register_style('vc_material', vc_asset_url('css/lib/vc-material/vc_material.min.css'), false, WPB_VC_VERSION);
                }
            }
        }
    }

    // Backend Files
    // -------------
    wp_register_style('ts-advanced-tables-composer',                            $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'css/jquery.vcsc.admin.composer.min.css', null, TABLESWP_VERSION, 'all');
    wp_register_style('ts-advanced-tables-generator',                           $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'css/jquery.vcsc.admin.generator.min.css', null, TABLESWP_VERSION, 'all');
    wp_register_script('ts-advanced-tables-generator',                          $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.admin.generator.min.js', array('jquery'), TABLESWP_VERSION, true);
    wp_register_style('ts-advanced-tables-parameters',                          $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'css/jquery.vcsc.admin.parameters.min.css', null, TABLESWP_VERSION, 'all');
    wp_register_script('ts-advanced-tables-parameters',                         $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.admin.parameters.min.js', array('jquery'), TABLESWP_VERSION, true);
    wp_register_style('ts-advanced-tables-settings',							$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'css/jquery.vcsc.admin.settings.min.css', null, TABLESWP_VERSION, 'all');
    wp_register_script('ts-advanced-tables-settings',							$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.admin.settings.min.js', array('jquery'), TABLESWP_VERSION, true);
    wp_register_style('ts-advanced-tables-editor',								$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'css/jquery.vcsc.admin.tableeditor.min.css', null, TABLESWP_VERSION, 'all');
    wp_register_script('ts-advanced-tables-editor',								$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.admin.tableeditor.min.js', array('jquery'), TABLESWP_VERSION, true);
    wp_register_script('ts-advanced-tables-grid',								$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.admin.tablegrid.min.js', array('jquery'), TABLESWP_VERSION, true);
    wp_register_style('ts-advanced-tables-font',								$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'css/jquery.vcsc.admin.tablefont.min.css', null, TABLESWP_VERSION, 'all');
    wp_register_style('ts-advanced-tables-cattags',								$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'css/jquery.vcsc.admin.cattags.min.css', null, TABLESWP_VERSION, 'all');
    wp_register_script('ts-advanced-tables-cattags',                            $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.admin.cattags.min.js', array('jquery'), TABLESWP_VERSION, true);
    
    // Frontend Files
    // --------------
    wp_register_style('ts-extend-advancedcustom',						        false, null, TABLESWP_VERSION, 'all');
    wp_register_script('ts-extend-advancedcustom',						        false, array('jquery'), TABLESWP_VERSION, true);
    wp_register_style('ts-extend-advancedtables',                               $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'css/jquery.vcsc.advancedtables.min.css', null, TABLESWP_VERSION, 'all');
    wp_register_script('ts-extend-advancedtables',                              $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.advancedtables.min.js', array('jquery'), TABLESWP_VERSION, true);
    wp_register_style('ts-extend-advancedcharts',                               $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'css/jquery.vcsc.advancedcharts.min.css', null, TABLESWP_VERSION, 'all');
    wp_register_script('ts-extend-advancedcharts',                              $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.advancedcharts.min.js', array('jquery'), TABLESWP_VERSION, true);
    
    // Table Editor Support Files
    // --------------------------
    // CSV Parser
    wp_register_script('ts-extend-csvparser',									$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.csvparser.min.js', array('jquery'), false, true);
    // RuleJS Main
    wp_register_script('ts-extend-rulejsmain',									$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.rulejsmain.min.js', array('jquery'), false, true);
    // RuleJS Formula
    wp_register_script('ts-extend-rulejsformula',                               $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.rulejsformula.min.js', array('jquery'), false, true);
    // RuleJS Parser
    wp_register_script('ts-extend-rulejsparser',                                $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.rulejsparser.min.js', array('jquery'), false, true);
    // ZeroClipboard (Flash)
    wp_register_script('ts-extend-zeroclipboard',                               $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.zeroclipboard.min.js', array('jquery'), false, true);
    // HTML5 Clipboard
    wp_register_script('ts-extend-clipboard',									$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.clipboard.min.js', array('jquery'), false, true);
    // MomentJS
    wp_register_script('ts-extend-momentjs',									$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.momentjs.min.js', array('jquery'), false, true);
    // Numbro
    wp_register_script('ts-extend-numbro',                                      $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.numbro.min.js', array('jquery'), false, true);
    // Languages
    wp_register_script('ts-extend-languages',                                   $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.languages.min.js', array('jquery', 'ts-extend-numbro'), false, true);
    // PikADay
    wp_register_style('ts-extend-pikaday',									    $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'css/jquery.vcsc.pikaday.min.css', null, false, 'all');
    wp_register_script('ts-extend-pikaday',									    $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.pikaday.min.js', array('jquery'), false, true);
    // Dropdown
    wp_register_style('ts-extend-dropdown',									    $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'css/jquery.vcsc.dropdown.min.css', null, false, 'all');
    wp_register_script('ts-extend-dropdown',									$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.dropdown.min.js', array('jquery'), false, true);
    // Advanced Colorpicker
    wp_register_style('ts-extend-colorpicker',					                $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'css/jquery.vcsc.colorpicker.min.css', null, false, 'all');
    wp_register_script('ts-extend-colorpicker',					                $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.colorpicker.min.js', array('jquery'), false, true);
    // HandsOnTable
    wp_register_style('ts-extend-handsontable',                                 $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'css/jquery.vcsc.handsontable.min.css', null, false, 'all');
    wp_register_script('ts-extend-handsontable',                                $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.handsontable.min.js', array('jquery'), false, true);
    
    // General Backend Files
    // ---------------------
    // NoUiSlider
    wp_register_style('ts-extend-nouislider',									$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'css/jquery.vcsc.nouislider.min.css', null, false, 'all');
    wp_register_script('ts-extend-nouislider',									$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.nouislider.min.js', array('jquery'), false, true);
    // MultiSelect
    wp_register_style('ts-extend-multiselect',									$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'css/jquery.vcsc.multi.select.min.css', null, false, 'all');
    wp_register_script('ts-extend-multiselect',									$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.multi.select.min.js', array('jquery'), false, true); 
    // Validation Engine
    wp_register_script('validation-engine', 									$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.validationengine.min.js', array('jquery'), false, true);
    wp_register_style('validation-engine',										$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'css/jquery.vcsc.validationengine.min.css', null, false, 'all');
    wp_register_script('validation-engine-en', 									$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.validationengine.en.min.js', array('jquery'), false, true);  
    // Alpha Colorpicker
    wp_register_script('ts-extend-colorpickeralpha',                            $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.colorpickeralpha.min.js', array('jquery','wp-color-picker'), false, true);
    // Font Icon Picker
    wp_register_style('ts-extend-iconpicker',					                $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'css/jquery.vcsc.fonticonpicker.min.css', null, false, 'all');
    wp_register_script('ts-extend-iconpicker',					                $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.fonticonpicker.min.js', array('jquery'), false, true);
    // Krautipster Tooltips
    wp_register_style('ts-extend-krauttipster',                 				$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'css/jquery.vcsc.krauttipster.min.css', null, false, 'all');
    wp_register_script('ts-extend-krauttipster',								$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.krauttipster.min.js', array('jquery'), false, true);
    // SweetAlert Popup
    wp_register_style('ts-extend-sweetalert',				        			$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'css/jquery.vcsc.sweetalert.min.css', null, false, 'all');
    wp_register_script('ts-extend-sweetalert',			            			$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.sweetalert.min.js', array('jquery'), false, true);
    // Sumo Select
    wp_register_style('ts-extend-sumo', 				        				$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'css/jquery.vcsc.sumoselect.min.css', null, false, 'all');
    wp_register_script('ts-extend-sumo', 										$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.sumoselect.min.js', array('jquery'), false, true);
    // Preloader Setyles
    wp_register_style('ts-extend-preloaders', 				        			$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'css/jquery.vcsc.preloaders.min.css', null, false, 'all');
    // Tagmanager
    wp_register_style('ts-extend-tagmanager',									$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'css/jquery.vcsc.tagmanager.min.css', null, false, 'all');
    wp_register_script('ts-extend-tagmanager',									$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'js/jquery.vcsc.tagmanager.min.js', array('jquery'), false, true);
    
    // TableSaw Files
    // --------------
    wp_register_script('ts-extend-tablesaw',					                $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'tablesaw/tablesaw.jquery.min.js', array('jquery'), false, true);
    wp_register_style('ts-extend-tablesaw',                                     $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'tablesaw/tablesaw.jquery.min.css', null, false, 'all');
    
    // Footable Files
    // --------------
    wp_register_script('ts-extend-footable',					                $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'footable/footable.standalone.min.js', array('jquery'), false, true);
    wp_register_style('ts-extend-footable',                                     $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'footable/footable.standalone.min.css', null, false, 'all');
    
    // Tabulator Files
    // ---------------
    wp_register_script('ts-extend-tabulator',					                $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'tabulator/js/tabulator.min.js', array('jquery'), false, true);
    wp_register_style('ts-extend-tabulator',                                    $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'tabulator/css/tabulator.min.css', null, false, 'all');
    
    // DataTables Files
    // ----------------
    // Full Files (Core + Responsive + Fixed Header + Buttons General API + Buttons HTML + Buttons Visibility + Browser Print)
    wp_register_script('ts-extend-datatables-full',					            $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/js/datatables.full.min.js', array('jquery'), false, true);
    wp_register_style('ts-extend-datatables-full',                              $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/css/datatables.full.min.css', null, false, 'all');
    // Core Files
    wp_register_script('ts-extend-datatables-core',					            $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/js/datatables.core.min.js', array('jquery'), false, true);
    wp_register_style('ts-extend-datatables-core',                              $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/css/datatables.core.min.css', null, false, 'all');
    // Custom Styling
    wp_register_style('ts-extend-datatables-custom',                            $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/css/datatables.custom.min.css', null, false, 'all');
    // Responsive Tables
    wp_register_script('ts-extend-datatables-responsive',                       $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/js/datatables.responsive.min.js', array('jquery'), false, true);
    wp_register_style('ts-extend-datatables-responsive',                        $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/css/datatables.responsive.min.css', null, false, 'all');
    // Fixed Header
    wp_register_script('ts-extend-datatables-fixedheader',                      $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/js/datatables.fixedheader.min.js', array('jquery'), false, true);
    wp_register_style('ts-extend-datatables-fixedheader',                       $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/css/datatables.fixedheader.min.css', null, false, 'all');
    // Fixed Columns
    wp_register_script('ts-extend-datatables-fixedcolumns',                     $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/js/datatables.fixedcolumns.min.js', array('jquery'), false, true);
    wp_register_style('ts-extend-datatables-fixedcolumns',                      $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/css/datatables.fixedcolumns.min.css', null, false, 'all');
    // Column Reorder
    wp_register_script('ts-extend-datatables-columnreorder',                    $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/js/datatables.columnreorder.min.js', array('jquery'), false, true);
    wp_register_style('ts-extend-datatables-columnreorder',                     $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/css/datatables.columnreorder.min.css', null, false, 'all');
    // Row Reorder
    wp_register_script('ts-extend-datatables-rowreorder',                       $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/js/datatables.rowreorder.min.js', array('jquery'), false, true);
    wp_register_style('ts-extend-datatables-rowreorder',                        $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/css/datatables.rowreorder.min.css', null, false, 'all');
    // Table Scroller
    wp_register_script('ts-extend-datatables-tablescroller',                    $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/js/datatables.tablescroller.min.js', array('jquery'), false, true);
    wp_register_style('ts-extend-datatables-tablescroller',                     $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/css/datatables.tablescroller.min.css', null, false, 'all');
    // Table Select
    wp_register_script('ts-extend-datatables-tableselect',                      $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/js/datatables.tableselect.min.js', array('jquery'), false, true);
    wp_register_style('ts-extend-datatables-tableselect',                       $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/css/datatables.tableselect.min.css', null, false, 'all');
    // Buttons: General API
    wp_register_script('ts-extend-datatables-buttons',                          $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/js/datatables.buttons.min.js', array('jquery'), false, true);
    wp_register_style('ts-extend-datatables-buttons',                           $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/css/datatables.buttons.min.css', null, false, 'all');
    // Buttons: Columns Visibility
    wp_register_script('ts-extend-datatables-visibility',                       $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/js/datatables.visibility.min.js', array('jquery'), false, true);
    // Buttons: HTML5 Copy, CSV, Excel, PDF (Main)
    wp_register_script('ts-extend-datatables-html5',                            $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/js/datatables.html5.min.js', array('jquery'), false, true);
    // Buttons: Flash Copy, CSV, Excel, PDF (Main)
    wp_register_script('ts-extend-datatables-flash',                            $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/js/datatables.flash.min.js', array('jquery'), false, true);
    // Buttons: Brower Print
    wp_register_script('ts-extend-datatables-print',                            $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/js/datatables.print.min.js', array('jquery'), false, true);
    // Buttons: Excel Export
    wp_register_script('ts-extend-datatables-jszip',                            $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/js/datatables.jszip.min.js', array('jquery'), false, true);
    // Buttons: PDF Export
    wp_register_script('ts-extend-datatables-pdfmaker',                         $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/js/datatables.pdfmaker.min.js', array('jquery'), false, true);
    wp_register_script('ts-extend-datatables-pdffonts',                         $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'datatables/js/datatables.pdffonts.min.js', array('jquery'), false, true);
    
    // jQuery UI Files
    // ---------------
    wp_register_script('ts-extend-jqueryui-widget',                             $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'jquery-ui/jquery-ui.min.js', array('jquery'), false, true);
    wp_register_style('ts-extend-jqueryui-widget',                              $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'jquery-ui/jquery-ui.min.css', null, false, 'all');
    
    // ChartJS Files
    // -------------
    wp_register_script('ts-extend-chartjs',                                     $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginPath . 'chartjs/jquery.vcsc.chartjs.min.js', array('jquery'), false, true);
?>