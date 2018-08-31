<?php
    // Envato Item Information
    // -----------------------
    $this->TS_TablesWP_Envato_Defaults                      = array(
        'data'                                              => array(),
        'name'                                              => "N/A",
        'info'                                              => "N/A",
        'link'                                              => "N/A",
        'price'                                             => 0,
        'sales'                                             => 0,
        'rating'                                            => 0,
        'votes'                                             => 0,
        'check'                                             => time(),
    );
    $this->TS_TablesWP_Envato_Globals                       = get_option("ts_tablesplus_extend_settings_envato", array());
    if (!is_array($this->TS_TablesWP_Envato_Globals)) {
        $this->TS_TablesWP_Envato_Globals                   = $this->TS_TablesWP_Envato_Defaults;
    } else if(count($this->TS_TablesWP_Envato_Globals) == 0) {
        $this->TS_TablesWP_Envato_Globals                   = $this->TS_TablesWP_Envato_Defaults;
    }

    // Check for MultiSite Activation
    // ------------------------------
    $this->TS_TablesWP_PluginMultiSite                      = (is_plugin_active_for_network(TABLESWP_SLUG) == true ? true : false);

    // Visual Composer Custom Parameter Files
    // --------------------------------------
    $this->TS_TablesWP_VisualComposer_Parameters 			= array(
        "Hidden Input Parameter"                            => array("file" => "hiddeninput"),
        "Live Preview Parameter"                            => array("file" => "livepreview"),
        "Messenger Parameter"                               => array("file" => "messenger"),
        "NoUiSlider Parameter"                              => array("file" => "nouislider"),        
        "Separator Parameter"            		            => array("file" => "separator"),        
        "Switch Parameter"                                  => array("file" => "switch"),
        "Tag Input Parameter"                               => array("file" => "tageditor"),
    );
    
    // WordPress Date + Time Formatting
    // --------------------------------
	$this->TS_TablesWP_WordPress_Date         	            = get_option('date_format');
	$this->TS_TablesWP_WordPress_Time         	            = get_option('time_format');
    
    // Check for Database Migrations
    // -----------------------------
    $this->TS_TablesWP_Settings_Migrated                    = get_option("ts_tablesplus_extend_settings_migrated", "false");
    if ($this->TS_TablesWP_Settings_Migrated == "true") {
        $this->TS_TablesWP_Settings_Migrated                = true;
    } else {
        $this->TS_TablesWP_Settings_Migrated                = false;
    }
    $this->TS_TablesWP_Settings_Database                    = get_option("ts_tablesplus_extend_settings_database", "false");
    if ($this->TS_TablesWP_Settings_Database == "true") {
        $this->TS_TablesWP_Settings_Database                = true;
    } else {
        $this->TS_TablesWP_Settings_Database                = false;
    }
    $this->TS_TablesWP_Settings_CatTags                    	= get_option("ts_tablesplus_extend_settings_cattags", "false");
    if ($this->TS_TablesWP_Settings_CatTags == "true") {
        $this->TS_TablesWP_Settings_CatTags                	= true;
    } else {
        $this->TS_TablesWP_Settings_CatTags                	= false;
    }
	
	// Contingency Check for Database Table
	// ------------------------------------
	if (!$this->TS_TablesWP_Settings_Database) {					
		TS_TablesWP_Database_TableCreate(true);
	}
	
	// Contingency Check for Fresh Installs
	// ------------------------------------
	if ($this->TS_TablesWP_Settings_Migrated == false) {
		$this->TS_TablesWP_Custom_Tables 					= get_option("ts_tablesplus_extend_settings_tables", '');
		if (empty($this->TS_TablesWP_Custom_Tables)) {
			$this->TS_TablesWP_Settings_Migrated			= true;
			update_option("ts_tablesplus_extend_settings_tables", array());
			update_option("ts_tablesplus_extend_settings_migrated", "true");
		}
	}

    // Define Default Settings
    // -----------------------
    $this->TS_TablesWP_Settings_Tables                      = array();
    $this->TS_TablesWP_Settings_UsedIDs                     = array();
    $this->TS_TablesWP_Settings_Globals                     = array(
        // General Settings
        'general'								            => array(
            'reuseids'              			            => false,
            'saveredirect'          			            => false,
            'mainmenu'              			            => true,
            'initialsort'           			            => '1',
            'initialorder'						            => 'asc',
            'exportlist'                                    => false,
            'exportoptions'                                 => 'print,pdf,csv,excel,copy',
            'loadlanguage'                                  => true,
            'composerintegrate'                             => true,
            'deletetables'                                  => true,
            'shortcodealways'                               => false,
            'autoupdate'                                    => true,
            'editoraccess'                                  => 'administrator,editor',
        ),
        // File Loading
        'filestatus'                                        => array(
            'loadalways'                                    => false,
            'loadtablesaw'                                  => true,
            'loadfootable'                                  => true,
            'loaddatatable'                                 => true,
            'loadtooltipster'                               => true,
            'loadhelpers'                                   => true,
        ),
        // tinyMCE Injection
        'tinymce'                                           => array(
            'allowinject'                                   => false,
            'posttypes'                                     => '',
        ),
        // Table Editor
        'tableeditor'							            => array(
            'minheight'							            => '200',
            'maxheight'							            => '800',
            'locale'							            => 'en-US',
            'newrows'                                       => 10,
            'newcolumns'                                    => 5,
        ),
        // Cell Content Alignments
        'alignments'            				            => array(
            'horizontal'        				            => 'htLeft',
            'vertical'          				            => 'htMiddle',
        ),
        // Cell Format Settings
        'formats'               				            => array(
            'currencyPlacement'					            => 'prefix',
            'currencySpacer'					            => false,
            'percentSpacer'						            => false,
            'formatDate'						            => 'MM-DD-YYYY',
            'formatTimeHours'					            => 'HH',
            'formatTimeMinutes'					            => 'mm',
            'formatTimeSeconds'					            => '',
            'formatTimeMeridiem'				            => 'A',
            'decimalsNumeric'					            => '2',
            'decimalsCurrency'					            => '2',
            'decimalsPercent'					            => '2',
            'commentsDates'                                 => 'MM-DD-YYYY',
            'commentsTimes'                                 => 'HH:mm',
        ),
        // Breakpoint Settings
        'breakpoints'                                       => array(
            'footableLarge'                                 => 1200,
            'footableMedium'                                => 992,
            'footableSmall'                                 => 768,
            'footableTiny'                                  => 480,
            'datatableDesktop'                              => 'Infinity',
            'datatableTabletL'                              => 1024,
            'datatableTabletP'                              => 768,
            'datatableMobileL'                              => 480,
            'datatableMobileP'                              => 320,
        ),
		// Icon Font Settings
		'fontmanager'										=> array(
			'internalAwesome'								=> true,
			'internalBrankic'								=> false,
			'internalCountricons'							=> false,
			'internalCurrencies'							=> false,
			'internalElegant'								=> false,
			'internalEntypo'								=> false,
			'internalFoundation'							=> false,
			'internalGenericons'							=> false,
			'internalIcoMoon'								=> false,
			'internalIonicons'								=> false,
			'internalMapIcons'								=> false,
			'internalMetrize'								=> false,
			'internalMonuments'								=> false,
			'internalSocialMedia'							=> false,
			'internalThemify'								=> false,
			'internalTypicons'								=> false,
			'internalDashicons'								=> false,
			'internalCustom'								=> false,
			'composerAwesome'								=> false,
			'composerEntypo'								=> false,
			'composerLinecons'								=> false,
			'composerOpenIconic'							=> false,
			'composerTypicons'								=> false,
			'composerMonoSocial'							=> false,
			'composerMaterial'								=> false,			
		),
        // Global Translations
        'translations'							            => array(
            // TableSaw Text Strings
            'tablesawStack'						            => __("Stack", "ts_visual_composer_extend"),
            'tablesawSwipe'						            => __("Swipe", "ts_visual_composer_extend"),
            'tablesawToggle'					            => __("Toggle", "ts_visual_composer_extend"),
            'tablesawResponsive'				            => __("Table Mode", "ts_visual_composer_extend"),
            'tablesawColumns'					            => __("Columns", "ts_visual_composer_extend"),
            'tablesawError'						            => __("No eligible columns.", "ts_visual_composer_extend"),
            'tablesawSort'						            => __("Sort Columns", "ts_visual_composer_extend"),
            // FooTable Text Strings
            'footableLengthMenu'				            => __("Show {LM} Entries", "ts_visual_composer_extend"),
            'footableLengthAll'                             => __("All", "ts_visual_composer_extend"),
            'footableCountFormat'				            => __("{CP} of {TP}", "ts_visual_composer_extend"),
            'footablePlaceholder'				            => __("Search ...", "ts_visual_composer_extend"),
            'footableNoResults'					            => __("No Results!", "ts_visual_composer_extend"),
            // DataTable Text Strings
            'datatableProcessing'				            => __("Processing ...", "ts_visual_composer_extend"),
            'datatableLengthMenu'				            => __("Show _MENU_ Entries", "ts_visual_composer_extend"),
            'datatableLengthAll'                            => __("All", "ts_visual_composer_extend"),
            'datatableInfoMain'					            => __("Showing _START_ to _END_ of _TOTAL_ Entries", "ts_visual_composer_extend"),
            'datatableInfoEmpty'				            => __("No Entries To Show!", "ts_visual_composer_extend"),
            'datatableInfoFiltered'				            => __(" - filtered from _MAX_ records.", "ts_visual_composer_extend"),
            'datatableSearch'					            => __("Search All Entries:", "ts_visual_composer_extend"),
            'datatablePlaceholder'				            => __("Enter keyword here ...", "ts_visual_composer_extend"),
            'datatableZeroRecords'				            => __("No Entries To Show!", "ts_visual_composer_extend"),
            'datatableFirst'					            => __("First", "ts_visual_composer_extend"),
            'datatablePrevious'					            => __("Previous", "ts_visual_composer_extend"),
            'datatableNext'						            => __("Next", "ts_visual_composer_extend"),
            'datatableLast'						            => __("Last", "ts_visual_composer_extend"),
            'datatablePrint'					            => __("Print", "ts_visual_composer_extend"),
            'datatablePDF'						            => __("PDF", "ts_visual_composer_extend"),
            'datatableExcel'					            => __("Excel", "ts_visual_composer_extend"),
            'datatableCSV'						            => __("CSV", "ts_visual_composer_extend"),
            'datatableCopy'						            => __("Copy", "ts_visual_composer_extend"),
        ),
		// Internal Settings
		'internals'											=> array(
			'completeMigration'								=> $this->TS_TablesWP_Settings_Migrated,
			'completeDatabase'								=> $this->TS_TablesWP_Settings_Database,
			'completeCategories'							=> $this->TS_TablesWP_Settings_CatTags,
		),
    );
        
    // Check for Extended License Usage
    // --------------------------------
    $this->TS_TablesWP_PluginExtended                       = get_option("ts_tablesplus_extend_settings_extended", "false");
    if ($this->TS_TablesWP_PluginExtended == "true") {
        $this->TS_TablesWP_PluginExtended                   = true;
    } else {
        $this->TS_TablesWP_PluginExtended                   = false;
    }

    // Retrieve Custom Global Settings
    // -------------------------------
    $this->TS_TablesWP_Custom_Globals                       = get_option("ts_tablesplus_extend_settings_globals", array());
    if (!is_array($this->TS_TablesWP_Custom_Globals)) {
        $this->TS_TablesWP_Custom_Globals                   = $this->TS_TablesWP_Settings_Globals;
    } else if(count($this->TS_TablesWP_Custom_Globals) == 0) {
        $this->TS_TablesWP_Custom_Globals                   = $this->TS_TablesWP_Settings_Globals;
    }
    
    // Create Global Setting Variables
    // -------------------------------    
    $this->TS_TablesWP_Settings_ReuseIDs                    = ((isset($this->TS_TablesWP_Custom_Globals['general']['reuseids'])) ? $this->TS_TablesWP_Custom_Globals['general']['reuseids'] : $this->TS_TablesWP_Settings_Globals['general']['reuseids']);
    $this->TS_TablesWP_Settings_SaveRedirect                = ((isset($this->TS_TablesWP_Custom_Globals['general']['saveredirect'])) ? $this->TS_TablesWP_Custom_Globals['general']['saveredirect'] : $this->TS_TablesWP_Settings_Globals['general']['saveredirect']);
    $this->TS_TablesWP_Settings_MainMenu                    = ((isset($this->TS_TablesWP_Custom_Globals['general']['mainmenu'])) ? $this->TS_TablesWP_Custom_Globals['general']['mainmenu'] : $this->TS_TablesWP_Settings_Globals['general']['mainmenu']);
    $this->TS_TablesWP_Settings_InitialSort                 = ((isset($this->TS_TablesWP_Custom_Globals['general']['initialsort'])) ? $this->TS_TablesWP_Custom_Globals['general']['initialsort'] : $this->TS_TablesWP_Settings_Globals['general']['initialsort']);
    $this->TS_TablesWP_Settings_InitialOrder                = ((isset($this->TS_TablesWP_Custom_Globals['general']['initialorder'])) ? $this->TS_TablesWP_Custom_Globals['general']['initialorder'] : $this->TS_TablesWP_Settings_Globals['general']['initialorder']);
    $this->TS_TablesWP_Settings_ExportList                  = ((isset($this->TS_TablesWP_Custom_Globals['general']['exportlist'])) ? $this->TS_TablesWP_Custom_Globals['general']['exportlist'] : $this->TS_TablesWP_Settings_Globals['general']['exportlist']);
    $this->TS_TablesWP_Settings_ExportOptions               = ((isset($this->TS_TablesWP_Custom_Globals['general']['exportoptions'])) ? $this->TS_TablesWP_Custom_Globals['general']['exportoptions'] : $this->TS_TablesWP_Settings_Globals['general']['exportoptions']);
    $this->TS_TablesWP_Settings_LoadLanguage                = ((isset($this->TS_TablesWP_Custom_Globals['general']['loadlanguage'])) ? $this->TS_TablesWP_Custom_Globals['general']['loadlanguage'] : $this->TS_TablesWP_Settings_Globals['general']['loadlanguage']);
    $this->TS_TablesWP_Settings_ComposerIntegrate           = ((isset($this->TS_TablesWP_Custom_Globals['general']['composerintegrate'])) ? $this->TS_TablesWP_Custom_Globals['general']['composerintegrate'] : $this->TS_TablesWP_Settings_Globals['general']['composerintegrate']);
    $this->TS_TablesWP_Settings_DeleteTables                = ((isset($this->TS_TablesWP_Custom_Globals['general']['deletetables'])) ? $this->TS_TablesWP_Custom_Globals['general']['deletetables'] : $this->TS_TablesWP_Settings_Globals['general']['deletetables']);
    $this->TS_TablesWP_Settings_ShortcodeAlways             = ((isset($this->TS_TablesWP_Custom_Globals['general']['shortcodealways'])) ? $this->TS_TablesWP_Custom_Globals['general']['shortcodealways'] : $this->TS_TablesWP_Settings_Globals['general']['shortcodealways']);
    $this->TS_TablesWP_Settings_AutoUpdate                  = ((isset($this->TS_TablesWP_Custom_Globals['general']['autoupdate'])) ? $this->TS_TablesWP_Custom_Globals['general']['autoupdate'] : $this->TS_TablesWP_Settings_Globals['general']['autoupdate']);
    // Table Editor Access
    $this->TS_TablesWP_Settings_EditorAccess                = ((isset($this->TS_TablesWP_Custom_Globals['general']['editoraccess'])) ? $this->TS_TablesWP_Custom_Globals['general']['editoraccess'] : $this->TS_TablesWP_Settings_Globals['general']['editoraccess']);
    $this->TS_TablesWP_Settings_EditorAccess                = explode(",", $this->TS_TablesWP_Settings_EditorAccess);
    // Files Frontend Loading
    $this->TS_TablesWP_Settings_LoadFilesAlways             = ((isset($this->TS_TablesWP_Custom_Globals['filestatus']['loadalways'])) ? $this->TS_TablesWP_Custom_Globals['filestatus']['loadalways'] : $this->TS_TablesWP_Settings_Globals['filestatus']['loadalways']);
    $this->TS_TablesWP_Settings_LoadTableSaw                = ((isset($this->TS_TablesWP_Custom_Globals['filestatus']['loadtablesaw'])) ? $this->TS_TablesWP_Custom_Globals['filestatus']['loadtablesaw'] : $this->TS_TablesWP_Settings_Globals['filestatus']['loadtablesaw']);
    $this->TS_TablesWP_Settings_LoadFooTable                = ((isset($this->TS_TablesWP_Custom_Globals['filestatus']['loadfootable'])) ? $this->TS_TablesWP_Custom_Globals['filestatus']['loadfootable'] : $this->TS_TablesWP_Settings_Globals['filestatus']['loadfootable']);
    $this->TS_TablesWP_Settings_LoadDataTable               = ((isset($this->TS_TablesWP_Custom_Globals['filestatus']['loaddatatable'])) ? $this->TS_TablesWP_Custom_Globals['filestatus']['loaddatatable'] : $this->TS_TablesWP_Settings_Globals['filestatus']['loaddatatable']);
    $this->TS_TablesWP_Settings_LoadToolTipster             = ((isset($this->TS_TablesWP_Custom_Globals['filestatus']['loadtooltipster'])) ? $this->TS_TablesWP_Custom_Globals['filestatus']['loadtooltipster'] : $this->TS_TablesWP_Settings_Globals['filestatus']['loadtooltipster']);
    $this->TS_TablesWP_Settings_LoadHelpers                 = ((isset($this->TS_TablesWP_Custom_Globals['filestatus']['loadhelpers'])) ? $this->TS_TablesWP_Custom_Globals['filestatus']['loadhelpers'] : $this->TS_TablesWP_Settings_Globals['filestatus']['loadhelpers']);
    // tinyMCE Post Type Injection
    $this->TS_TablesWP_Settings_TinyMCEAllow                = ((isset($this->TS_TablesWP_Custom_Globals['tinymce']['allowinject'])) ? $this->TS_TablesWP_Custom_Globals['tinymce']['allowinject'] : $this->TS_TablesWP_Settings_Globals['tinymce']['allowinject']);
    $this->TS_TablesWP_Settings_TinyMCEPostTypes            = ((isset($this->TS_TablesWP_Custom_Globals['tinymce']['posttypes'])) ? $this->TS_TablesWP_Custom_Globals['tinymce']['posttypes'] : $this->TS_TablesWP_Settings_Globals['tinymce']['posttypes']);
    $this->TS_TablesWP_Settings_TinyMCEPostTypes            = explode(",", $this->TS_TablesWP_Settings_TinyMCEPostTypes);
    // Table Dimensions
    $this->TS_TablesWP_Settings_TableMinHeight              = ((isset($this->TS_TablesWP_Custom_Globals['tableeditor']['minheight'])) ? $this->TS_TablesWP_Custom_Globals['tableeditor']['minheight'] : $this->TS_TablesWP_Settings_Globals['tableeditor']['minheight']);
    $this->TS_TablesWP_Settings_TableMaxHeight              = ((isset($this->TS_TablesWP_Custom_Globals['tableeditor']['maxheight'])) ? $this->TS_TablesWP_Custom_Globals['tableeditor']['maxheight'] : $this->TS_TablesWP_Settings_Globals['tableeditor']['maxheight']);
    // Table Editor
    $this->TS_TablesWP_Settings_EditorLocale                = ((isset($this->TS_TablesWP_Custom_Globals['tableeditor']['locale'])) ? $this->TS_TablesWP_Custom_Globals['tableeditor']['locale'] : $this->TS_TablesWP_Settings_LocaleWP);
    // Cell Content Alignments
    $this->TS_TablesWP_Settings_AlignHorizontal             = ((isset($this->TS_TablesWP_Custom_Globals['alignments']['horizontal'])) ? $this->TS_TablesWP_Custom_Globals['alignments']['horizontal'] : $this->TS_TablesWP_Settings_Globals['alignments']['horizontal']);
    $this->TS_TablesWP_Settings_AlignVertical               = ((isset($this->TS_TablesWP_Custom_Globals['alignments']['vertical'])) ? $this->TS_TablesWP_Custom_Globals['alignments']['vertical'] : $this->TS_TablesWP_Settings_Globals['alignments']['vertical']);
    // Breakpoint Settings
    $this->TS_TablesWP_Settings_FootableLarge               = ((isset($this->TS_TablesWP_Custom_Globals['breakpoints']['footableLarge'])) ? $this->TS_TablesWP_Custom_Globals['breakpoints']['footableLarge'] : $this->TS_TablesWP_Settings_Globals['breakpoints']['footableLarge']);
    $this->TS_TablesWP_Settings_FootableMedium              = ((isset($this->TS_TablesWP_Custom_Globals['breakpoints']['footableMedium'])) ? $this->TS_TablesWP_Custom_Globals['breakpoints']['footableMedium'] : $this->TS_TablesWP_Settings_Globals['breakpoints']['footableMedium']);
    $this->TS_TablesWP_Settings_FootableSmall               = ((isset($this->TS_TablesWP_Custom_Globals['breakpoints']['footableSmall'])) ? $this->TS_TablesWP_Custom_Globals['breakpoints']['footableSmall'] : $this->TS_TablesWP_Settings_Globals['breakpoints']['footableSmall']);
    $this->TS_TablesWP_Settings_FootableTiny                = ((isset($this->TS_TablesWP_Custom_Globals['breakpoints']['footableTiny'])) ? $this->TS_TablesWP_Custom_Globals['breakpoints']['footableTiny'] : $this->TS_TablesWP_Settings_Globals['breakpoints']['footableTiny']);
    $this->TS_TablesWP_Settings_DatatableDesktop            = ((isset($this->TS_TablesWP_Custom_Globals['breakpoints']['datatableDesktop'])) ? $this->TS_TablesWP_Custom_Globals['breakpoints']['datatableDesktop'] : $this->TS_TablesWP_Settings_Globals['breakpoints']['datatableDesktop']);
    $this->TS_TablesWP_Settings_DatatableTabletL            = ((isset($this->TS_TablesWP_Custom_Globals['breakpoints']['datatableTabletL'])) ? $this->TS_TablesWP_Custom_Globals['breakpoints']['datatableTabletL'] : $this->TS_TablesWP_Settings_Globals['breakpoints']['datatableTabletL']);
    $this->TS_TablesWP_Settings_DatatableTabletP            = ((isset($this->TS_TablesWP_Custom_Globals['breakpoints']['datatableTabletP'])) ? $this->TS_TablesWP_Custom_Globals['breakpoints']['datatableTabletP'] : $this->TS_TablesWP_Settings_Globals['breakpoints']['datatableTabletP']);
    $this->TS_TablesWP_Settings_DatatableMobileL            = ((isset($this->TS_TablesWP_Custom_Globals['breakpoints']['datatableMobileL'])) ? $this->TS_TablesWP_Custom_Globals['breakpoints']['datatableMobileL'] : $this->TS_TablesWP_Settings_Globals['breakpoints']['datatableMobileL']);
    $this->TS_TablesWP_Settings_DatatableMobileP            = ((isset($this->TS_TablesWP_Custom_Globals['breakpoints']['datatableMobileP'])) ? $this->TS_TablesWP_Custom_Globals['breakpoints']['datatableMobileP'] : $this->TS_TablesWP_Settings_Globals['breakpoints']['datatableMobileP']);
	// Number Format Settings
    $this->TS_TablesWP_Settings_DecimalsNumeric             = ((isset($this->TS_TablesWP_Custom_Globals['formats']['decimalsNumeric'])) ? $this->TS_TablesWP_Custom_Globals['formats']['decimalsNumeric'] : $this->TS_TablesWP_Settings_Globals['formats']['decimalsNumeric']);
    $this->TS_TablesWP_Settings_DecimalsCurrency            = ((isset($this->TS_TablesWP_Custom_Globals['formats']['decimalsCurrency'])) ? $this->TS_TablesWP_Custom_Globals['formats']['decimalsCurrency'] : $this->TS_TablesWP_Settings_Globals['formats']['decimalsCurrency']);
    $this->TS_TablesWP_Settings_DecimalsPercent             = ((isset($this->TS_TablesWP_Custom_Globals['formats']['decimalsPercent'])) ? $this->TS_TablesWP_Custom_Globals['formats']['decimalsPercent'] : $this->TS_TablesWP_Settings_Globals['formats']['decimalsPercent']);
    $this->TS_TablesWP_Settings_CurrencyPlacement           = ((isset($this->TS_TablesWP_Custom_Globals['formats']['currencyPlacement'])) ? $this->TS_TablesWP_Custom_Globals['formats']['currencyPlacement'] : $this->TS_TablesWP_Settings_Globals['formats']['currencyPlacement']);
    $this->TS_TablesWP_Settings_CurrencySpace               = ((isset($this->TS_TablesWP_Custom_Globals['formats']['currencySpacer'])) ? $this->TS_TablesWP_Custom_Globals['formats']['currencySpacer'] : $this->TS_TablesWP_Settings_Globals['formats']['currencySpacer']);
    $this->TS_TablesWP_Settings_PercentSpace                = ((isset($this->TS_TablesWP_Custom_Globals['formats']['percentSpacer'])) ? $this->TS_TablesWP_Custom_Globals['formats']['percentSpacer'] : $this->TS_TablesWP_Settings_Globals['formats']['percentSpacer']);
    $this->TS_TablesWP_Settings_FormatDateFull              = ((isset($this->TS_TablesWP_Custom_Globals['formats']['formatDate'])) ? $this->TS_TablesWP_Custom_Globals['formats']['formatDate'] : $this->TS_TablesWP_Settings_Globals['formats']['formatDate']);
    $this->TS_TablesWP_Settings_FormatTimeHours             = ((isset($this->TS_TablesWP_Custom_Globals['formats']['formatTimeHours'])) ? $this->TS_TablesWP_Custom_Globals['formats']['formatTimeHours'] : $this->TS_TablesWP_Settings_Globals['formats']['formatTimeHours']);
    $this->TS_TablesWP_Settings_FormatTimeMinutes           = ((isset($this->TS_TablesWP_Custom_Globals['formats']['formatTimeMinutes'])) ? $this->TS_TablesWP_Custom_Globals['formats']['formatTimeMinutes'] : $this->TS_TablesWP_Settings_Globals['formats']['formatTimeMinutes']);
    $this->TS_TablesWP_Settings_FormatTimeSeconds           = ((isset($this->TS_TablesWP_Custom_Globals['formats']['formatTimeSeconds'])) ? $this->TS_TablesWP_Custom_Globals['formats']['formatTimeSeconds'] : $this->TS_TablesWP_Settings_Globals['formats']['formatTimeSeconds']);
    $this->TS_TablesWP_Settings_FormatTimeMeridiem          = ((isset($this->TS_TablesWP_Custom_Globals['formats']['formatTimeMeridiem'])) ? $this->TS_TablesWP_Custom_Globals['formats']['formatTimeMeridiem'] : $this->TS_TablesWP_Settings_Globals['formats']['formatTimeMeridiem']);
    // Comments Date + Time Overrides
    $this->TS_TablesWP_Settings_CommentsDates               = ((isset($this->TS_TablesWP_Custom_Globals['formats']['commentsDates'])) ? $this->TS_TablesWP_Custom_Globals['formats']['commentsDates'] : $this->TS_TablesWP_Settings_Globals['formats']['commentsDates']);
    $this->TS_TablesWP_Settings_CommentsTimes               = ((isset($this->TS_TablesWP_Custom_Globals['formats']['commentsTimes'])) ? $this->TS_TablesWP_Custom_Globals['formats']['commentsTimes'] : $this->TS_TablesWP_Settings_Globals['formats']['commentsTimes']);
    // Build Format for Numeric Values
    if ($this->TS_TablesWP_Settings_DecimalsNumeric > 0) {
        $this->TS_TablesWP_Settings_FormatNumeric           = '0,0.';
        for ($x = 0; $x < $this->TS_TablesWP_Settings_DecimalsNumeric; $x++) {
            $this->TS_TablesWP_Settings_FormatNumeric       .= '0';
        }
    } else {
        $this->TS_TablesWP_Settings_FormatNumeric           = '0' . $this->TS_TablesWP_Settings_ThousandsIndicator . '0';
    }    
    // Build Format for Currency Values
    if ($this->TS_TablesWP_Settings_DecimalsCurrency > 0) {
        $this->TS_TablesWP_Settings_FormatCurrency          = '0,0.';
        for ($x = 0; $x < $this->TS_TablesWP_Settings_DecimalsCurrency; $x++) {
            $this->TS_TablesWP_Settings_FormatCurrency      .= '0';
        }
    } else {
        $this->TS_TablesWP_Settings_FormatCurrency          .= '0' . $this->TS_TablesWP_Settings_ThousandsIndicator . '0';
    }
    if ($this->TS_TablesWP_Settings_CurrencyPlacement == "prefix") {
        $this->TS_TablesWP_Settings_FormatCurrency          = '$' . ($this->TS_TablesWP_Settings_CurrencySpace == 'true' ? ' ' : '') . $this->TS_TablesWP_Settings_FormatCurrency;
    } else if ($this->TS_TablesWP_Settings_CurrencyPlacement == "postfix") {
        $this->TS_TablesWP_Settings_FormatCurrency          = $this->TS_TablesWP_Settings_FormatCurrency . ($this->TS_TablesWP_Settings_CurrencySpace == 'true' ? ' ' : '') . '$';
    }
    // Build Format for Percent Values
    if ($this->TS_TablesWP_Settings_DecimalsPercent > 0) {
        $this->TS_TablesWP_Settings_FormatPercent           = '0.';
        for ($x = 0; $x < $this->TS_TablesWP_Settings_DecimalsPercent; $x++) {
            $this->TS_TablesWP_Settings_FormatPercent       .= '0';
        }
        $this->TS_TablesWP_Settings_FormatPercent           .= ($this->TS_TablesWP_Settings_PercentSpace == 'true' ? ' ' : '') . '%';
    } else {
        $this->TS_TablesWP_Settings_FormatPercent           = '0' . ($this->TS_TablesWP_Settings_PercentSpace == 'true' ? ' ' : '') . '%';
    }    
    // Build Format for Date Values
    $this->TS_TablesWP_Settings_FormatDate                  = $this->TS_TablesWP_Settings_FormatDateFull;    
    // Build Format for Time Values
    $this->TS_TablesWP_Settings_FormatTime                  = $this->TS_TablesWP_Settings_FormatTimeHours . ':' . $this->TS_TablesWP_Settings_FormatTimeMinutes;
    if ($this->TS_TablesWP_Settings_FormatTimeSeconds != '') {
        $this->TS_TablesWP_Settings_FormatTime              .= ':' . $this->TS_TablesWP_Settings_FormatTimeSeconds;
    }
    if (($this->TS_TablesWP_Settings_FormatTimeHours == "hh") || ($this->TS_TablesWP_Settings_FormatTimeHours == "h")) {
        $this->TS_TablesWP_Settings_FormatTime              .= ' ' . $this->TS_TablesWP_Settings_FormatTimeMeridiem;
    }    
    // 3rd Party Script Language Strings
    $this->TS_TablesWP_Language_TableSaw                    = array(
        'tablesawStack'						                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['tablesawStack'])) ? $this->TS_TablesWP_Custom_Globals['translations']['tablesawStack'] : $this->TS_TablesWP_Settings_Globals['translations']['tablesawStack']),
        'tablesawSwipe'						                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['tablesawSwipe'])) ? $this->TS_TablesWP_Custom_Globals['translations']['tablesawSwipe'] : $this->TS_TablesWP_Settings_Globals['translations']['tablesawSwipe']),
        'tablesawToggle'					                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['tablesawToggle'])) ? $this->TS_TablesWP_Custom_Globals['translations']['tablesawToggle'] : $this->TS_TablesWP_Settings_Globals['translations']['tablesawToggle']),
        'tablesawResponsive'				                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['tablesawResponsive'])) ? $this->TS_TablesWP_Custom_Globals['translations']['tablesawResponsive'] : $this->TS_TablesWP_Settings_Globals['translations']['tablesawResponsive']),
        'tablesawColumns'					                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['tablesawColumns'])) ? $this->TS_TablesWP_Custom_Globals['translations']['tablesawColumns'] : $this->TS_TablesWP_Settings_Globals['translations']['tablesawColumns']),
        'tablesawError'						                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['tablesawError'])) ? $this->TS_TablesWP_Custom_Globals['translations']['tablesawError'] : $this->TS_TablesWP_Settings_Globals['translations']['tablesawError']),
        'tablesawSort'						                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['tablesawSort'])) ? $this->TS_TablesWP_Custom_Globals['translations']['tablesawSort'] : $this->TS_TablesWP_Settings_Globals['translations']['tablesawSort']),
    );
    $this->TS_TablesWP_Language_FooTable                    = array(
        'footableLengthMenu'				                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['footableLengthMenu'])) ? $this->TS_TablesWP_Custom_Globals['translations']['footableLengthMenu'] : $this->TS_TablesWP_Settings_Globals['translations']['footableLengthMenu']),
        'footableLengthAll'				                    => ((isset($this->TS_TablesWP_Custom_Globals['translations']['footableLengthAll'])) ? $this->TS_TablesWP_Custom_Globals['translations']['footableLengthAll'] : $this->TS_TablesWP_Settings_Globals['translations']['footableLengthAll']),
        'footableCountFormat'				                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['footableCountFormat'])) ? $this->TS_TablesWP_Custom_Globals['translations']['footableCountFormat'] : $this->TS_TablesWP_Settings_Globals['translations']['footableCountFormat']),
        'footablePlaceholder'				                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['footablePlaceholder'])) ? $this->TS_TablesWP_Custom_Globals['translations']['footablePlaceholder'] : $this->TS_TablesWP_Settings_Globals['translations']['footablePlaceholder']),
        'footableNoResults'					                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['footableNoResults'])) ? $this->TS_TablesWP_Custom_Globals['translations']['footableNoResults'] : $this->TS_TablesWP_Settings_Globals['translations']['footableNoResults']),
    );
    $this->TS_TablesWP_Language_DataTable                   = array(
        'datatableProcessing'				                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['datatableProcessing'])) ? $this->TS_TablesWP_Custom_Globals['translations']['datatableProcessing'] : $this->TS_TablesWP_Settings_Globals['translations']['datatableProcessing']),
        'datatableLengthAll'                                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['datatableLengthAll'])) ? $this->TS_TablesWP_Custom_Globals['translations']['datatableLengthAll'] : $this->TS_TablesWP_Settings_Globals['translations']['datatableLengthAll']),
        'datatableLengthMenu'				                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['datatableLengthMenu'])) ? $this->TS_TablesWP_Custom_Globals['translations']['datatableLengthMenu'] : $this->TS_TablesWP_Settings_Globals['translations']['datatableLengthMenu']),
        'datatableInfoMain'					                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['datatableInfoMain'])) ? $this->TS_TablesWP_Custom_Globals['translations']['datatableInfoMain'] : $this->TS_TablesWP_Settings_Globals['translations']['datatableInfoMain']),
        'datatableInfoEmpty'				                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['datatableInfoEmpty'])) ? $this->TS_TablesWP_Custom_Globals['translations']['datatableInfoEmpty'] : $this->TS_TablesWP_Settings_Globals['translations']['datatableInfoEmpty']),
        'datatableInfoFiltered'				                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['datatableInfoFiltered'])) ? $this->TS_TablesWP_Custom_Globals['translations']['datatableInfoFiltered'] : $this->TS_TablesWP_Settings_Globals['translations']['datatableInfoFiltered']),
        'datatableSearch'					                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['datatableSearch'])) ? $this->TS_TablesWP_Custom_Globals['translations']['datatableSearch'] : $this->TS_TablesWP_Settings_Globals['translations']['datatableSearch']),
        'datatablePlaceholder'				                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['datatablePlaceholder'])) ? $this->TS_TablesWP_Custom_Globals['translations']['datatablePlaceholder'] : $this->TS_TablesWP_Settings_Globals['translations']['datatablePlaceholder']),
        'datatableZeroRecords'				                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['datatableZeroRecords'])) ? $this->TS_TablesWP_Custom_Globals['translations']['datatableZeroRecords'] : $this->TS_TablesWP_Settings_Globals['translations']['datatableZeroRecords']),
        'datatableFirst'					                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['datatableFirst'])) ? $this->TS_TablesWP_Custom_Globals['translations']['datatableFirst'] : $this->TS_TablesWP_Settings_Globals['translations']['datatableFirst']),
        'datatablePrevious'					                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['datatablePrevious'])) ? $this->TS_TablesWP_Custom_Globals['translations']['datatablePrevious'] : $this->TS_TablesWP_Settings_Globals['translations']['datatablePrevious']),
        'datatableNext'						                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['datatableNext'])) ? $this->TS_TablesWP_Custom_Globals['translations']['datatableNext'] : $this->TS_TablesWP_Settings_Globals['translations']['datatableNext']),
        'datatableLast'						                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['datatableLast'])) ? $this->TS_TablesWP_Custom_Globals['translations']['datatableLast'] : $this->TS_TablesWP_Settings_Globals['translations']['datatableLast']),
        'datatablePrint'					                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['datatablePrint'])) ? $this->TS_TablesWP_Custom_Globals['translations']['datatablePrint'] : $this->TS_TablesWP_Settings_Globals['translations']['datatablePrint']),
        'datatablePDF'						                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['datatablePDF'])) ? $this->TS_TablesWP_Custom_Globals['translations']['datatablePDF'] : $this->TS_TablesWP_Settings_Globals['translations']['datatablePDF']),
        'datatableExcel'					                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['datatableExcel'])) ? $this->TS_TablesWP_Custom_Globals['translations']['datatableExcel'] : $this->TS_TablesWP_Settings_Globals['translations']['datatableExcel']),
        'datatableCSV'						                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['datatableCSV'])) ? $this->TS_TablesWP_Custom_Globals['translations']['datatableCSV'] : $this->TS_TablesWP_Settings_Globals['translations']['datatableCSV']),
        'datatableCopy'						                => ((isset($this->TS_TablesWP_Custom_Globals['translations']['datatableCopy'])) ? $this->TS_TablesWP_Custom_Globals['translations']['datatableCopy'] : $this->TS_TablesWP_Settings_Globals['translations']['datatableCopy']),
    );
    
    // Envato API Information
    // ----------------------
    $this->TS_TablesWP_API_ExternalURL                      = "http://maintenance.krautcoding.com/licenses/ts-envato-api-check-vc-tablenator.php?license=";
    $this->TS_TablesWP_API_Token                            = "ecrfCDjNSGTFOKIkpJBpNXBlhoddLAst";
    $this->TS_TablesWP_API_ItemID                           = "18560899";
    $this->TS_TablesWP_API_Duplications                     = array(
		'NjYzNjUzNzYtMWNjYy00ZDAwLWI4ZDktMDBlNjcyZTlmZTgz',
		'YTJhY2U3MGQtOGNhYi00NzdhLWJmNTgtMjA3N2E2MjFmN2Y1',
	);
    
    // Plugin License Data
    // -------------------
    $this->TS_TablesWP_PluginLicenseBlank                   = array(
        'data'                                              => "",
        'info'                                              => "",
        'keyed'                                             => "emptydelimiterfix",
        'valid'                                             => false,
        'remove'                                            => false,
        'update'                                            => false,
    );
    if ($this->TS_TablesWP_PluginMultiSite == "true") {
        $this->TS_TablesWP_PluginLicenseSave                = get_site_option('ts_tablesplus_extend_settings_licenseData', array());
    } else {
        $this->TS_TablesWP_PluginLicenseSave                = get_option('ts_tablesplus_extend_settings_licenseData', array());
    }
    $this->TS_TablesWP_PluginLicenseCurrent                 = array(
        'data'                                              => ((isset($this->TS_TablesWP_PluginLicenseSave['data'])) ? $this->TS_TablesWP_PluginLicenseSave['data'] : $this->TS_TablesWP_PluginLicenseBlank['data']),
        'info'                                              => ((isset($this->TS_TablesWP_PluginLicenseSave['info'])) ? $this->TS_TablesWP_PluginLicenseSave['info'] : $this->TS_TablesWP_PluginLicenseBlank['info']),
        'keyed'                                             => ((isset($this->TS_TablesWP_PluginLicenseSave['keyed'])) ? $this->TS_TablesWP_PluginLicenseSave['keyed'] : $this->TS_TablesWP_PluginLicenseBlank['keyed']),
        'valid'                                             => ((isset($this->TS_TablesWP_PluginLicenseSave['valid'])) ? $this->TS_TablesWP_PluginLicenseSave['valid'] : $this->TS_TablesWP_PluginLicenseBlank['valid']),
        'remove'                                            => ((isset($this->TS_TablesWP_PluginLicenseSave['remove'])) ? $this->TS_TablesWP_PluginLicenseSave['remove'] : $this->TS_TablesWP_PluginLicenseBlank['remove']),
        'update'                                            => ((isset($this->TS_TablesWP_PluginLicenseSave['update'])) ? $this->TS_TablesWP_PluginLicenseSave['update'] : $this->TS_TablesWP_PluginLicenseBlank['update']),
    );
    
    // Other Routine Checks
    // --------------------
    if (($this->TS_TablesWP_PluginLicenseCurrent['keyed'] != '') && ($this->TS_TablesWP_PluginLicenseCurrent['keyed'] != 'emptydelimiterfix') && (in_array(base64_encode($this->TS_TablesWP_PluginLicenseCurrent['keyed']), $this->TS_TablesWP_API_Duplications))) {
        $this->TS_TablesWP_PluginUsage                      = false;
    } else {
        $this->TS_TablesWP_PluginUsage                      = true;
    }
    if ($this->TS_TablesWP_PluginUsage == false) {
        $this->TS_TablesWP_PluginLicenseCurrent['info']     = "";
        $this->TS_TablesWP_PluginLicenseCurrent['keyed']    = "emptydelimiterfix";
        $this->TS_TablesWP_PluginLicenseCurrent['valid']    = false;
        if ($this->TS_TablesWP_PluginMultiSite == true) {
            update_site_option('ts_tablesplus_extend_settings_licenseData', $this->TS_TablesWP_PluginLicenseCurrent);
        } else {
            update_option('ts_tablesplus_extend_settings_licenseData', $this->TS_TablesWP_PluginLicenseCurrent);
        }
    }
    $this->TS_TablesWP_PluginKeystring                      = $this->TS_TablesWP_PluginLicenseCurrent['data'];
    $this->TS_TablesWP_PluginLicense                        = $this->TS_TablesWP_PluginLicenseCurrent['keyed'];
    $this->TS_TablesWP_PluginValid                          = $this->TS_TablesWP_PluginLicenseCurrent['valid'];
    $this->TS_TablesWP_PluginEnvato                         = $this->TS_TablesWP_PluginLicenseCurrent['info'];
    if (($this->TS_TablesWP_PluginKeystring != '') && (in_array(base64_encode($this->TS_TablesWP_PluginKeystring), $this->TS_TablesWP_API_Duplications))) {
        $this->TS_TablesWP_PluginSupport                    = false;
    } else {
        $this->TS_TablesWP_PluginSupport                    = true;
    }
    if (($this->TS_TablesWP_PluginLicenseCurrent == true) && ($this->TS_TablesWP_PluginValid == true) && ($this->TS_TablesWP_PluginSupport == true) && ($this->TS_TablesWP_PluginExtended == false)) {
        if ($this->TS_TablesWP_Settings_AutoUpdate) {        
            $this->TS_TablesWP_PluginAutoUpdate                = true;
        } else {
            $this->TS_TablesWP_PluginAutoUpdate                = false;
        }
    } else {
        $this->TS_TablesWP_PluginAutoUpdate                    = false;
    }
?>