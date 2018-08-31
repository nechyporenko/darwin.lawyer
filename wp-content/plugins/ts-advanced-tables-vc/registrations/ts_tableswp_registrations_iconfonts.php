<?php
	// Adjust Global Settings
	$this->TS_TablesWP_Settings_Globals['fontmanager']['internalAwesome'] = ($this->TS_TablesWP_VisualComposer_Active == "true" ? false : true);
	$this->TS_TablesWP_Settings_Globals['fontmanager']['composerAwesome'] = ($this->TS_TablesWP_VisualComposer_Active == "true" ? true : false);
	
	// Icon Font Information
	// ---------------------
    $this->TS_TablesWP_Installed_Icon_Fonts = array(
        "Font Awesome"                  	        => "Awesome",
        "Brankic 1979 Font"            		 	    => "Brankic",
        "Countricons Font"              		    => "Countricons",
        "Currencies Font"               		    => "Currencies",
        "Elegant Icons Font"            		    => "Elegant",
        "Entypo Font"                   		    => "Entypo",
        "Foundation Font"               		    => "Foundation",
        "Genericons Font"               		    => "Genericons",
        "IcoMoon Font"                  		    => "IcoMoon",
        "Ionicons Font"                  		    => "Ionicons",
        "MapIcons Font"                		        => "MapIcons",
        "Metrize Font"                		        => "Metrize",
        "Monuments Font"                		    => "Monuments",
        "Social Media Font"             		    => "SocialMedia",
        "Themify Font"                              => "Themify",
        "Typicons Font"                 		    => "Typicons",
        "Dashicons"                                 => "Dashicons",
        //"Custom User Font"              		    => "Custom",
    );
    $this->TS_TablesWP_Icon_Font_Settings = array(
        "Font Awesome"                  	        => array("setting" => "Awesome",                "author" => "Dave Gandy",                           "link" => "http://fontawesome.io/icons/",                                       "type" => "Standard",       "default" => "false",		"active" => "false",    "class" => "ts-awesome-",               "count" => "675"),
        "Brankic 1979 Font"            		 	    => array("setting" => "Brankic",                "author" => "Brankic1979",                          "link" => "http://www.brankic1979.com/icons/",                                  "type" => "Standard",       "default" => "false",		"active" => "false",    "class" => "ts-brankic-",               "count" => "350"),
        "Countricons Font"              		    => array("setting" => "Countricons",            "author" => "Freepik",                              "link" => "http://www.flaticon.com/packs/countricons",                          "type" => "Specialty",      "default" => "false",		"active" => "false",    "class" => "ts-countricons-",           "count" => "194"),
        "Currencies Font"               		    => array("setting" => "Currencies",             "author" => "Freepik",                              "link" => "http://www.flaticon.com/packs/currency-icons-fill",                  "type" => "Specialty",      "default" => "false",		"active" => "false",    "class" => "ts-currencies-",            "count" => "89"),
        "Elegant Icons Font"            		    => array("setting" => "Elegant",                "author" => "Elegant Themes",                       "link" => "https://www.elegantthemes.com/blog/resources/elegant-icon-font",     "type" => "Standard",       "default" => "false",		"active" => "false",    "class" => "ts-elegant-",               "count" => "360"),
        "Entypo Font"                   		    => array("setting" => "Entypo",                 "author" => "Entypo",                               "link" => "http://www.entypo.com/",                                             "type" => "Standard",       "default" => "false",		"active" => "false",    "class" => "ts-entypo-",                "count" => "284"),
        "Foundation Font"               		    => array("setting" => "Foundation",             "author" => "ZURB University",                      "link" => "http://zurb.com/playground/foundation-icon-fonts-3",                 "type" => "Standard",       "default" => "false",		"active" => "false",    "class" => "ts-foundation-",            "count" => "283"),
        "Genericons Font"               		    => array("setting" => "Genericons",             "author" => "Automatic",                            "link" => "https://genericons.com/",                                            "type" => "Standard",       "default" => "false",		"active" => "false",    "class" => "ts-genericons-",            "count" => "122"),
        "IcoMoon Font"                  		    => array("setting" => "IcoMoon",                "author" => "Keyamoon",                             "link" => "https://icomoon.io/#preview-free",                                   "type" => "Standard",       "default" => "false",		"active" => "false",    "class" => "ts-icomoon-",               "count" => "451"),
        "Ionicons Font"                  		    => array("setting" => "Ionicons",               "author" => "Ionic Framework",                      "link" => "http://ionicons.com/",                                               "type" => "Standard",       "default" => "false",		"active" => "false",    "class" => "ts-ionicons-",              "count" => "601"),
        "MapIcons Font"                		        => array("setting" => "MapIcons",               "author" => "Scott De Jonge",                       "link" => "http://map-icons.com/",                                              "type" => "Specialty",      "default" => "false",		"active" => "false",    "class" => "ts-mapicons-",              "count" => "176"),
        "Metrize Font"                		        => array("setting" => "Metrize",                "author" => "Alessio Atzeni",                       "link" => "http://www.alessioatzeni.com/metrize-icons/",                        "type" => "Standard",       "default" => "false",		"active" => "false",    "class" => "ts-metrize-",               "count" => "300"),
        "Monuments Font"                		    => array("setting" => "Monuments",              "author" => "Freepik",                              "link" => "http://www.flaticon.com/packs/monuments",                            "type" => "Specialty",      "default" => "false",		"active" => "false",    "class" => "ts-monuments-",             "count" => "255"),
        "Social Media Font"             		    => array("setting" => "SocialMedia",            "author" => "SimpleIcon",                           "link" => "http://www.flaticon.com/packs/simpleicon-social-media",              "type" => "Specialty",      "default" => "false",		"active" => "false",    "class" => "ts-socialmedia-",           "count" => "190"),
        "Themify Font"                 		        => array("setting" => "Themify",                "author" => "Themify",                              "link" => "https://themify.me/themify-icons",                                   "type" => "Standard",       "default" => "false",		"active" => "false",    "class" => "ts-themify-",               "count" => "352"),
        "Typicons Font"                 		    => array("setting" => "Typicons",               "author" => "Stephen Hutchings",                    "link" => "http://www.typicons.com/",                                           "type" => "Standard",       "default" => "false",		"active" => "false",    "class" => "ts-typicons-",              "count" => "308"),
        "Dashicons Font"              		        => array("setting" => "Dashicons",              "author" => "WordPress",                            "link" => "https://developer.wordpress.org/resource/dashicons",                 "type" => "WordPress",      "default" => "false",		"active" => "false",    "class" => "dashicons-",                "count" => "201"),
        "Custom User Font"              		    => array("setting" => "Custom",                 "author" => "Custom User Font",                     "link" => "https://icomoon.io/app/",                                            "type" => "Custom",         "default" => "false",		"active" => "false",    "class" => "",                          "count" => "0"),
    );
    $this->TS_TablesWP_VisualComposer_Icon_Fonts = array(
        "Font Awesome (VC)"                  	    => "VC_Awesome",
        "Entypo Font (VC)"                          => "VC_Entypo",
        "Linecons Font (VC)"                        => "VC_Linecons",
        "Open Iconic Font (VC)"                     => "VC_OpenIconic",
        "Typicons Font (VC)"                        => "VC_Typicons",
    );
    $this->TS_TablesWP_VisualComposer_Font_Settings = array(
        "Font Awesome (VC)"                  	    => array("setting" => "Awesome",             	"author" => "Dave Gandy",                           "link" => "http://fontawesome.io/icons/",                                       "type" => "Composer",       "default" => "true",		"active" => "true",     "class" => "fa fa-",                    "count" => "634",       "function" => "vc_iconpicker_type_fontawesome",     "handle" => "font-awesome"),
        "Entypo Font (VC)"                          => array("setting" => "Entypo",              	"author" => "Entypo",                               "link" => "http://www.entypo.com/",                                             "type" => "Composer",       "default" => "false",		"active" => "false",    "class" => "entypo-icon entypo-",       "count" => "284",       "function" => "vc_iconpicker_type_entypo",          "handle" => "vc_entypo"),
        "Linecons Font (VC)"                        => array("setting" => "Linecons",            	"author" => "Smashing Magazine",                    "link" => "http://www.flaticon.com/packs/linecons",                             "type" => "Composer",       "default" => "false",		"active" => "false",    "class" => "vc_li vc_li-",              "count" => "48",        "function" => "vc_iconpicker_type_linecons",        "handle" => "vc_linecons"),
        "Open Iconic Font (VC)"                     => array("setting" => "OpenIconic",          	"author" => "Iconic",                               "link" => "https://useiconic.com/open/",                                        "type" => "Composer",       "default" => "false",		"active" => "false",    "class" => "vc-oi vc-oi-",              "count" => "151",       "function" => "vc_iconpicker_type_openiconic",      "handle" => "vc_openiconic"),
        "Typicons Font (VC)"                        => array("setting" => "Typicons",            	"author" => "Stephen Hutchings",                    "link" => "http://www.typicons.com/",                                           "type" => "Composer",       "default" => "false",		"active" => "false",    "class" => "typcn typcn-",              "count" => "336",       "function" => "vc_iconpicker_type_typicons",        "handle" => "vc_typicons"),
    );
    if (($this->TS_TablesWP_VisualComposer_Active == "true") && ($this->TS_TablesWP_Settings_ComposerIntegrate == true)) {
        if (TS_TABLESWP_VERSIONCompare(WPB_VC_VERSION, '4.11.0') >= 0) {
            $this->TS_TablesWP_VisualComposer_Icon_Fonts["Mono Social Font (VC)"]         = "VC_MonoSocial";
            $this->TS_TablesWP_VisualComposer_Font_Settings["Mono Social Font (VC)"]      = array("setting" => "MonoSocial",   "author" => "Ivan Drinchev",    "link" => "http://drinchev.github.io/monosocialiconsfont/",                     "type" => "Composer",       "default" => "false",       "active" => "false",    "always" => "false",        "class" => "vc-mono vc-mono-",          "count" => "100",       "function" => "vc_iconpicker_type_monosocial",      "handle" => "vc_monosocialiconsfont");
        }
        if (TS_TABLESWP_VERSIONCompare(WPB_VC_VERSION, '5.0.0') >= 0) {
            $this->TS_TablesWP_VisualComposer_Icon_Fonts["Material Font (VC)"]            = "VC_Material";
            $this->TS_TablesWP_VisualComposer_Font_Settings["Material Font (VC)"]         = array("setting" => "Material",     "author" => "Google",           "link" => "https://design.google.com/icons/",                                   "type" => "Composer",       "default" => "false",       "active" => "false",    "always" => "false",        "class" => "vc-material vc-material-",  "count" => "845",       "function" => "vc_iconpicker_type_material",        "handle" => "vc_material");
        }
    }

    // Icon Font Manage Settings
	$this->TS_TablesWP_Settings_FontManager					= array(
		'internalAwesome'									=> ((isset($this->TS_TablesWP_Custom_Globals['fontmanager']['internalAwesome'])) ? $this->TS_TablesWP_Custom_Globals['fontmanager']['internalAwesome'] : $this->TS_TablesWP_Settings_Globals['fontmanager']['internalAwesome']),
		'internalBrankic'									=> ((isset($this->TS_TablesWP_Custom_Globals['fontmanager']['internalBrankic'])) ? $this->TS_TablesWP_Custom_Globals['fontmanager']['internalBrankic'] : $this->TS_TablesWP_Settings_Globals['fontmanager']['internalBrankic']),
		'internalCountricons'								=> ((isset($this->TS_TablesWP_Custom_Globals['fontmanager']['internalCountricons'])) ? $this->TS_TablesWP_Custom_Globals['fontmanager']['internalCountricons'] : $this->TS_TablesWP_Settings_Globals['fontmanager']['internalCountricons']),
		'internalCurrencies'								=> ((isset($this->TS_TablesWP_Custom_Globals['fontmanager']['internalCurrencies'])) ? $this->TS_TablesWP_Custom_Globals['fontmanager']['internalCurrencies'] : $this->TS_TablesWP_Settings_Globals['fontmanager']['internalCurrencies']),
		'internalElegant'									=> ((isset($this->TS_TablesWP_Custom_Globals['fontmanager']['internalElegant'])) ? $this->TS_TablesWP_Custom_Globals['fontmanager']['internalElegant'] : $this->TS_TablesWP_Settings_Globals['fontmanager']['internalElegant']),
		'internalEntypo'									=> ((isset($this->TS_TablesWP_Custom_Globals['fontmanager']['internalEntypo'])) ? $this->TS_TablesWP_Custom_Globals['fontmanager']['internalEntypo'] : $this->TS_TablesWP_Settings_Globals['fontmanager']['internalEntypo']),
		'internalFoundation'								=> ((isset($this->TS_TablesWP_Custom_Globals['fontmanager']['internalFoundation'])) ? $this->TS_TablesWP_Custom_Globals['fontmanager']['internalFoundation'] : $this->TS_TablesWP_Settings_Globals['fontmanager']['internalFoundation']),
		'internalGenericons'								=> ((isset($this->TS_TablesWP_Custom_Globals['fontmanager']['internalGenericons'])) ? $this->TS_TablesWP_Custom_Globals['fontmanager']['internalGenericons'] : $this->TS_TablesWP_Settings_Globals['fontmanager']['internalGenericons']),
		'internalIcoMoon'									=> ((isset($this->TS_TablesWP_Custom_Globals['fontmanager']['internalIcoMoon'])) ? $this->TS_TablesWP_Custom_Globals['fontmanager']['internalIcoMoon'] : $this->TS_TablesWP_Settings_Globals['fontmanager']['internalIcoMoon']),
		'internalIonicons'									=> ((isset($this->TS_TablesWP_Custom_Globals['fontmanager']['internalIonicons'])) ? $this->TS_TablesWP_Custom_Globals['fontmanager']['internalIonicons'] : $this->TS_TablesWP_Settings_Globals['fontmanager']['internalIonicons']),
		'internalMapIcons'									=> ((isset($this->TS_TablesWP_Custom_Globals['fontmanager']['internalMapIcons'])) ? $this->TS_TablesWP_Custom_Globals['fontmanager']['internalMapIcons'] : $this->TS_TablesWP_Settings_Globals['fontmanager']['internalMapIcons']),
		'internalMetrize'									=> ((isset($this->TS_TablesWP_Custom_Globals['fontmanager']['internalMetrize'])) ? $this->TS_TablesWP_Custom_Globals['fontmanager']['internalMetrize'] : $this->TS_TablesWP_Settings_Globals['fontmanager']['internalMetrize']),
		'internalMonuments'									=> ((isset($this->TS_TablesWP_Custom_Globals['fontmanager']['internalMonuments'])) ? $this->TS_TablesWP_Custom_Globals['fontmanager']['internalMonuments'] : $this->TS_TablesWP_Settings_Globals['fontmanager']['internalMonuments']),
		'internalSocialMedia'								=> ((isset($this->TS_TablesWP_Custom_Globals['fontmanager']['internalSocialMedia'])) ? $this->TS_TablesWP_Custom_Globals['fontmanager']['internalSocialMedia'] : $this->TS_TablesWP_Settings_Globals['fontmanager']['internalSocialMedia']),
		'internalThemify'									=> ((isset($this->TS_TablesWP_Custom_Globals['fontmanager']['internalThemify'])) ? $this->TS_TablesWP_Custom_Globals['fontmanager']['internalThemify'] : $this->TS_TablesWP_Settings_Globals['fontmanager']['internalThemify']),
		'internalTypicons'									=> ((isset($this->TS_TablesWP_Custom_Globals['fontmanager']['internalTypicons'])) ? $this->TS_TablesWP_Custom_Globals['fontmanager']['internalTypicons'] : $this->TS_TablesWP_Settings_Globals['fontmanager']['internalTypicons']),
		'internalCustom'									=> ((isset($this->TS_TablesWP_Custom_Globals['fontmanager']['internalCustom'])) ? $this->TS_TablesWP_Custom_Globals['fontmanager']['internalCustom'] : $this->TS_TablesWP_Settings_Globals['fontmanager']['internalCustom']),
		'internalDashicons'									=> ((isset($this->TS_TablesWP_Custom_Globals['fontmanager']['internalDashicons'])) ? $this->TS_TablesWP_Custom_Globals['fontmanager']['internalDashicons'] : $this->TS_TablesWP_Settings_Globals['fontmanager']['internalDashicons']),
		'composerAwesome'									=> ((isset($this->TS_TablesWP_Custom_Globals['fontmanager']['composerAwesome'])) ? $this->TS_TablesWP_Custom_Globals['fontmanager']['composerAwesome'] : $this->TS_TablesWP_Settings_Globals['fontmanager']['composerAwesome']),
		'composerEntypo'									=> ((isset($this->TS_TablesWP_Custom_Globals['fontmanager']['composerEntypo'])) ? $this->TS_TablesWP_Custom_Globals['fontmanager']['composerEntypo'] : $this->TS_TablesWP_Settings_Globals['fontmanager']['composerEntypo']),
		'composerLinecons'									=> ((isset($this->TS_TablesWP_Custom_Globals['fontmanager']['composerLinecons'])) ? $this->TS_TablesWP_Custom_Globals['fontmanager']['composerLinecons'] : $this->TS_TablesWP_Settings_Globals['fontmanager']['composerLinecons']),
		'composerOpenIconic'								=> ((isset($this->TS_TablesWP_Custom_Globals['fontmanager']['composerOpenIconic'])) ? $this->TS_TablesWP_Custom_Globals['fontmanager']['composerOpenIconic'] : $this->TS_TablesWP_Settings_Globals['fontmanager']['composerOpenIconic']),
		'composerTypicons'									=> ((isset($this->TS_TablesWP_Custom_Globals['fontmanager']['composerTypicons'])) ? $this->TS_TablesWP_Custom_Globals['fontmanager']['composerTypicons'] : $this->TS_TablesWP_Settings_Globals['fontmanager']['composerTypicons']),
		'composerMonoSocial'								=> ((isset($this->TS_TablesWP_Custom_Globals['fontmanager']['composerTypicons'])) ? $this->TS_TablesWP_Custom_Globals['fontmanager']['composerTypicons'] : $this->TS_TablesWP_Settings_Globals['fontmanager']['composerTypicons']),
		'composerMaterial'									=> ((isset($this->TS_TablesWP_Custom_Globals['fontmanager']['composerTypicons'])) ? $this->TS_TablesWP_Custom_Globals['fontmanager']['composerTypicons'] : $this->TS_TablesWP_Settings_Globals['fontmanager']['composerTypicons']),
	);
	$this->TS_TablesWP_Total_Icon_Fonts						= 0;
	$this->TS_TablesWP_Total_Icon_Count						= 0;
	$this->TS_TablesWP_Active_Icon_Fonts					= 0;
	$this->TS_TablesWP_Active_Icon_Count					= 0;
	// Check Internal Icon Fonts
	foreach ($this->TS_TablesWP_Icon_Font_Settings as $Icon_Font => $iconfont) {
		$this->TS_TablesWP_Total_Icon_Fonts++;
		$this->TS_TablesWP_Total_Icon_Count					= $this->TS_TablesWP_Total_Icon_Count + $this->TS_TablesWP_Icon_Font_Settings[$Icon_Font]['count'];
		$this->TS_TablesWP_Icon_Font_Settings[$Icon_Font]['active'] 					= ($this->TS_TablesWP_Settings_FontManager['internal' . $this->TS_TablesWP_Icon_Font_Settings[$Icon_Font]['setting']] == true ? "true" : "false");
		if ($this->TS_TablesWP_Icon_Font_Settings[$Icon_Font]['active'] == "true") {
			$this->TS_TablesWP_Active_Icon_Fonts++;
			$this->TS_TablesWP_Active_Icon_Count			= $this->TS_TablesWP_Active_Icon_Count + $this->TS_TablesWP_Icon_Font_Settings[$Icon_Font]['count'];
		}
	}
	// Check Composer Icon Fonts
    foreach ($this->TS_TablesWP_VisualComposer_Font_Settings as $Icon_Font => $iconfont) {
		if (($this->TS_TablesWP_VisualComposer_Active == "false") || ($this->TS_TablesWP_Settings_ComposerIntegrate == false)) {
			$this->TS_TablesWP_VisualComposer_Font_Settings[$Icon_Font]['active']				= "false";
		} else {
			$this->TS_TablesWP_Total_Icon_Fonts++;
			$this->TS_TablesWP_Total_Icon_Count				= $this->TS_TablesWP_Total_Icon_Count + $this->TS_TablesWP_VisualComposer_Font_Settings[$Icon_Font]['count'];
			$this->TS_TablesWP_VisualComposer_Font_Settings[$Icon_Font]['active']				= ($this->TS_TablesWP_Settings_FontManager['composer' . $this->TS_TablesWP_VisualComposer_Font_Settings[$Icon_Font]['setting']] == true ? "true" : "false");
		}
		if ($this->TS_TablesWP_VisualComposer_Font_Settings[$Icon_Font]['active'] == "true") {
			$this->TS_TablesWP_Active_Icon_Fonts++;
			$this->TS_TablesWP_Active_Icon_Count			= $this->TS_TablesWP_Active_Icon_Count + $this->TS_TablesWP_VisualComposer_Font_Settings[$Icon_Font]['count'];
		}
	}
	// Force Activate If No Active Font
	if ($this->TS_TablesWP_Active_Icon_Fonts == 0) {
		if (($this->TS_TablesWP_VisualComposer_Active == "false") || ($this->TS_TablesWP_Settings_ComposerIntegrate == false)) {
			$this->TS_TablesWP_Icon_Font_Settings['Font Awesome']['active']				= "true";
			$this->TS_TablesWP_Active_Icon_Fonts++;
			$this->TS_TablesWP_Active_Icon_Count			= $this->TS_TablesWP_Active_Icon_Count + $this->TS_TablesWP_Icon_Font_Settings['Font Awesome']['count'];
		} else {
			$this->TS_TablesWP_VisualComposer_Font_Settings['Font Awesome (VC)']['active']	= "true";
			$this->TS_TablesWP_Active_Icon_Fonts++;
			$this->TS_TablesWP_Active_Icon_Count			= $this->TS_TablesWP_Active_Icon_Count + $this->TS_TablesWP_VisualComposer_Font_Settings['Font Awesome (VC)']['count'];
		}
	}
?>