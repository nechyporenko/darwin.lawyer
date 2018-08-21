<?php
    global $TS_ADVANCED_TABLESWP;
    
    // Create Custom Admin Menu for Plugin
    add_action('admin_menu', 'TS_TablesWP_CreateMenu', 9999);
    function TS_TablesWP_CreateMenu() {
        global $TS_ADVANCED_TABLESWP;
        global $TS_TablesWP_Page_Main;        
        global $TS_TablesWP_Page_Tables;
        global $TS_TablesWP_Page_Initial;
        global $TS_TablesWP_Page_AddNew;
        global $TS_TablesWP_Page_Modify;
        global $TS_TablesWP_Page_Delete;
        global $TS_TablesWP_Page_Maintain;
        global $TS_TablesWP_Page_Backup;
        global $TS_TablesWP_Page_Restore;
        global $TS_TablesWP_Page_Export;
        global $TS_TablesWP_Page_Import;
        global $TS_TablesWP_Page_Rebuild;
        global $TS_TablesWP_Page_Migrate;
        global $TS_TablesWP_Page_Settings;
        global $TS_TablesWP_Page_License;
        global $TS_TablesWP_Page_CatTags;
        if ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_MainMenu == true) {
            $TS_TablesWP_Page_Main =            add_menu_page(      "Tablenator WP",            "Tablenator WP",    	    "read",                         "TS_TablesWP_Tables", 	    "TS_TablesWP_PageTables", 	    TS_TablesWP_GetResourceURL("images/logos/ts_vcsc_menu_icon_16x16.png"),     "79.987654321");
        } else {
            $TS_TablesWP_Page_Main =            add_options_page(   "Tablenator WP",            "Tablenator WP",    	    "read",                         "TS_TablesWP_Tables", 	    "TS_TablesWP_PageTables");
        }
        $TS_TablesWP_Page_Tables =              add_submenu_page( 	"TS_TablesWP_Tables",       __("Tables List", "ts_visual_composer_extend"),             __("Tables List", "ts_visual_composer_extend"),             "read", 	                "TS_TablesWP_Tables", 	        "TS_TablesWP_PageTables");
        if ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Migrated == true) {
            if ($TS_ADVANCED_TABLESWP->TS_TablesWP_CurrentUserEditor == true) {
                $TS_TablesWP_Page_Initial =     add_submenu_page( 	"TS_TablesWP_Tables",       __("Add New Table", "ts_visual_composer_extend"),           __("Add New Table", "ts_visual_composer_extend"),           "read", 	                "TS_TablesWP_Grid",             "TS_TablesWP_PageGrid");
                $TS_TablesWP_Page_CatTags =     add_submenu_page( 	"TS_TablesWP_Tables", 	    __("Categories Manager", "ts_visual_composer_extend"),      __("Categories Manager", "ts_visual_composer_extend"),      "read", 	                "TS_TablesWP_Categories",       "TS_TablesWP_PageCategories");
                $TS_TablesWP_Page_Maintain =    add_submenu_page( 	"TS_TablesWP_Tables",       __("Tables Maintenance", "ts_visual_composer_extend"),      __("Tables Maintenance", "ts_visual_composer_extend"),      "read", 	                "TS_TablesWP_Maintain",         "TS_TablesWP_PageMaintain");                
                $TS_TablesWP_Page_AddNew =      add_submenu_page( 	null, 	                    __("Add New Table", "ts_visual_composer_extend"),           __("Add New Table", "ts_visual_composer_extend"),           "read", 	                "TS_TablesWP_AddNew", 	        "TS_TablesWP_PageAddNew");
                $TS_TablesWP_Page_Modify =      add_submenu_page( 	null, 	                    __("Edit Table", "ts_visual_composer_extend"),              __("Edit Table", "ts_visual_composer_extend"),              "read", 	                "TS_TablesWP_Modify", 	        "TS_TablesWP_PageModify");
                $TS_TablesWP_Page_Delete =      add_submenu_page( 	null, 	                    __("Delete Tables", "ts_visual_composer_extend"),           __("Delete Tables", "ts_visual_composer_extend"),           "read", 	                "TS_TablesWP_Delete", 	        "TS_TablesWP_PageDelete");                
                $TS_TablesWP_Page_Backup =      add_submenu_page( 	null, 	                    __("Backup Tables", "ts_visual_composer_extend"),           __("Backup Tables", "ts_visual_composer_extend"),           "read", 	                "TS_TablesWP_Backup", 	        "TS_TablesWP_PageBackup");                
                $TS_TablesWP_Page_Restore =     add_submenu_page( 	null, 	                    __("Restore Tables", "ts_visual_composer_extend"),          __("Restore Tables", "ts_visual_composer_extend"),          "read", 	                "TS_TablesWP_Restore", 	        "TS_TablesWP_PageRestore");                
                $TS_TablesWP_Page_Export =      add_submenu_page( 	null, 	                    __("Export Table", "ts_visual_composer_extend"),            __("Export Table", "ts_visual_composer_extend"),            "read", 	                "TS_TablesWP_Export", 	        "TS_TablesWP_PageExport");                
                $TS_TablesWP_Page_Import =      add_submenu_page( 	null,                       __("Import Table", "ts_visual_composer_extend"),            __("Import Table", "ts_visual_composer_extend"),            "read", 	                "TS_TablesWP_Import", 	        "TS_TablesWP_PageImport");
                $TS_TablesWP_Page_Rebuild =     add_submenu_page( 	null,                       __("Create SQL Table", "ts_visual_composer_extend"),        __("Create SQL Table", "ts_visual_composer_extend"),        "read", 	                "TS_TablesWP_Rebuild", 	        "TS_TablesWP_PageRebuild");
            }
        }
        if (($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_Migrated == false) && (current_user_can('manage_options'))) {
            $TS_TablesWP_Page_Migrate =         add_submenu_page( 	"TS_TablesWP_Tables", 	    __("Migrate Tables", "ts_visual_composer_extend"),          __("Migrate Tables", "ts_visual_composer_extend"),          "manage_options", 	        "TS_TablesWP_Migrate",          "TS_TablesWP_PageMigrate");
        }
        if (current_user_can('manage_options')) {
            $TS_TablesWP_Page_Settings = 		add_submenu_page( 	"TS_TablesWP_Tables", 	    __("Settings", "ts_visual_composer_extend"),                __("Settings", "ts_visual_composer_extend"),                "manage_options", 	        "TS_TablesWP_Settings", 	    "TS_TablesWP_PageSettings");
            $TS_TablesWP_Page_License = 		add_submenu_page( 	"TS_TablesWP_Tables", 	    __("License Key", "ts_visual_composer_extend"),             __("License Key", "ts_visual_composer_extend"),             "manage_options", 	        "TS_TablesWP_License", 	        "TS_TablesWP_PageLicense");        
        }
    }
    
    // Callback Function for Menu Entries
    function TS_TablesWP_PageTables() {
        global $TS_ADVANCED_TABLESWP;
        echo '<div class="wrap ts-settings" id="ts_vcsc_extend_frame" style="direction: ltr; margin-top: 20px;">' . "\n";
            include($TS_ADVANCED_TABLESWP->assets_dir . 'ts_tableswp_assets_listing.php');
        echo '</div>' . "\n";
    }
    function TS_TablesWP_PageGrid() {
        global $TS_ADVANCED_TABLESWP;
        if ($TS_ADVANCED_TABLESWP->TS_TablesWP_CurrentUserEditor == true) {
            echo '<div class="wrap ts-settings" id="ts_vcsc_extend_frame" style="direction: ltr; margin-top: 20px;">' . "\n";
                include($TS_ADVANCED_TABLESWP->assets_dir . 'ts_tableswp_assets_initial.php');                
            echo '</div>' . "\n";
        } else {
            wp_die('You do not have sufficient permissions to access this page.');
        }
    }
    function TS_TablesWP_PageAddNew() {
        global $TS_ADVANCED_TABLESWP;
        if ($TS_ADVANCED_TABLESWP->TS_TablesWP_CurrentUserEditor == true) {
            echo '<div class="wrap ts-settings" id="ts_vcsc_extend_frame" style="direction: ltr; margin-top: 20px;">' . "\n";
                include($TS_ADVANCED_TABLESWP->assets_dir . 'ts_tableswp_assets_addnew.php');                
            echo '</div>' . "\n";
        } else {
            wp_die('You do not have sufficient permissions to access this page.');
        }
    }
    function TS_TablesWP_PageModify() {
        global $TS_ADVANCED_TABLESWP;
        if ($TS_ADVANCED_TABLESWP->TS_TablesWP_CurrentUserEditor == true) {
            echo '<div class="wrap ts-settings" id="ts_vcsc_extend_frame" style="direction: ltr; margin-top: 20px;">' . "\n";
                include($TS_ADVANCED_TABLESWP->assets_dir . 'ts_tableswp_assets_modify.php');
            echo '</div>' . "\n";
        } else {
            wp_die('You do not have sufficient permissions to access this page.');
        }
    }
    function TS_TablesWP_PageDelete() {
        global $TS_ADVANCED_TABLESWP;
        if ($TS_ADVANCED_TABLESWP->TS_TablesWP_CurrentUserEditor == true) {
            echo '<div class="wrap ts-settings" id="ts_vcsc_extend_frame" style="direction: ltr; margin-top: 20px;">' . "\n";
                include($TS_ADVANCED_TABLESWP->assets_dir . 'ts_tableswp_assets_deleteall.php');
            echo '</div>' . "\n";
        } else {
            wp_die('You do not have sufficient permissions to access this page.');
        }
    }
    function TS_TablesWP_PageMaintain() {
        global $TS_ADVANCED_TABLESWP;
        if ($TS_ADVANCED_TABLESWP->TS_TablesWP_CurrentUserEditor == true) {
            echo '<div class="wrap ts-settings" id="ts_vcsc_extend_frame" style="direction: ltr; margin-top: 20px;">' . "\n";
                include($TS_ADVANCED_TABLESWP->assets_dir . 'ts_tableswp_assets_maintenance.php');
            echo '</div>' . "\n";
        } else {
            wp_die('You do not have sufficient permissions to access this page.');
        }
    }
    function TS_TablesWP_PageBackup() {
        global $TS_ADVANCED_TABLESWP;
        if ($TS_ADVANCED_TABLESWP->TS_TablesWP_CurrentUserEditor == true) {
            echo '<div class="wrap ts-settings" id="ts_vcsc_extend_frame" style="direction: ltr; margin-top: 20px;">' . "\n";
                include($TS_ADVANCED_TABLESWP->assets_dir . 'ts_tableswp_assets_backup.php');
            echo '</div>' . "\n";
        } else {
            wp_die('You do not have sufficient permissions to access this page.');
        }
    }
    function TS_TablesWP_PageRestore() {
        global $TS_ADVANCED_TABLESWP;
        if ($TS_ADVANCED_TABLESWP->TS_TablesWP_CurrentUserEditor == true) {
            echo '<div class="wrap ts-settings" id="ts_vcsc_extend_frame" style="direction: ltr; margin-top: 20px;">' . "\n";
                include($TS_ADVANCED_TABLESWP->assets_dir . 'ts_tableswp_assets_restore.php');
            echo '</div>' . "\n";
        } else {
            wp_die('You do not have sufficient permissions to access this page.');
        }
    }
    function TS_TablesWP_PageExport() {
        global $TS_ADVANCED_TABLESWP;
        if ($TS_ADVANCED_TABLESWP->TS_TablesWP_CurrentUserEditor == true) {
            echo '<div class="wrap ts-settings" id="ts_vcsc_extend_frame" style="direction: ltr; margin-top: 20px;">' . "\n";
                include($TS_ADVANCED_TABLESWP->assets_dir . 'ts_tableswp_assets_export.php');
            echo '</div>' . "\n";
        } else {
            wp_die('You do not have sufficient permissions to access this page.');
        }
    }
    function TS_TablesWP_PageImport() {
        global $TS_ADVANCED_TABLESWP;
        if ($TS_ADVANCED_TABLESWP->TS_TablesWP_CurrentUserEditor == true) {
            echo '<div class="wrap ts-settings" id="ts_vcsc_extend_frame" style="direction: ltr; margin-top: 20px;">' . "\n";
                include($TS_ADVANCED_TABLESWP->assets_dir . 'ts_tableswp_assets_import.php');
            echo '</div>' . "\n";
        } else {
            wp_die('You do not have sufficient permissions to access this page.');
        }
    }
    function TS_TablesWP_PageRebuild() {
        global $TS_ADVANCED_TABLESWP;
        if ($TS_ADVANCED_TABLESWP->TS_TablesWP_CurrentUserEditor == true) {
            echo '<div class="wrap ts-settings" id="ts_vcsc_extend_frame" style="direction: ltr; margin-top: 20px;">' . "\n";
                include($TS_ADVANCED_TABLESWP->assets_dir . 'ts_tableswp_assets_databasenew.php');
            echo '</div>' . "\n";
        } else {
            wp_die('You do not have sufficient permissions to access this page.');
        }
    }
    function TS_TablesWP_PageMigrate() {
        global $TS_ADVANCED_TABLESWP;
        if (current_user_can('manage_options')) {
            echo '<div class="wrap ts-settings" id="ts_vcsc_extend_frame" style="direction: ltr; margin-top: 20px;">' . "\n";
                include($TS_ADVANCED_TABLESWP->assets_dir . 'ts_tableswp_assets_migrate.php');
            echo '</div>' . "\n";
        } else {
            wp_die('You do not have sufficient permissions to access this page.');
        }
    }
    function TS_TablesWP_PageCategories() {
        global $TS_ADVANCED_TABLESWP;
        if ($TS_ADVANCED_TABLESWP->TS_TablesWP_CurrentUserEditor == true) {
            echo '<div class="wrap ts-settings" id="ts_vcsc_extend_frame" style="direction: ltr; margin-top: 20px;">' . "\n";
                include($TS_ADVANCED_TABLESWP->assets_dir . 'ts_tableswp_assets_cattags.php');
            echo '</div>' . "\n";
        } else {
            wp_die('You do not have sufficient permissions to access this page.');
        }
    }
    function TS_TablesWP_PageSettings() {
        global $TS_ADVANCED_TABLESWP;
        if (current_user_can('manage_options')) {
            echo '<div class="wrap ts-settings" id="ts_vcsc_extend_frame" style="direction: ltr; margin-top: 20px;">' . "\n";
                include($TS_ADVANCED_TABLESWP->assets_dir . 'ts_tableswp_assets_settings.php');
            echo '</div>' . "\n";
        } else {
            wp_die('You do not have sufficient permissions to access this page.');
        }
    }
    function TS_TablesWP_PageLicense() {
        global $TS_ADVANCED_TABLESWP;
        if (current_user_can('manage_options')) {
            echo '<div class="wrap ts-settings" id="ts_vcsc_extend_frame" style="direction: ltr; margin-top: 20px;">' . "\n";
                include($TS_ADVANCED_TABLESWP->assets_dir . 'ts_tableswp_assets_license.php');
            echo '</div>' . "\n";
        } else {
            wp_die('You do not have sufficient permissions to access this page.');
        }
    }
?>