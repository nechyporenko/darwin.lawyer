<?php
/*
Plugin Name:    Tablenator - Advanced Tables for WordPress & WP Bakery Page Builder
Plugin URI:     https://codecanyon.net/item/tablenator-advanced-tables-for-visual-composer/18560899
Version:        2.0.1
Description:    A plugin to easily create and display advanced and responsive tables and to use them within WordPress, fully supporting WP Bakery Page Builder plugin (formerly Visual Composer).
Author:         Tekanewa Scripts by Kraut Coding
Author URI:     http://www.tablenatorvc.krautcoding.com
Text Domain:    ts_visual_composer_extend
Domain Path:	/locale
*/


// Do NOT Load Directly
// --------------------
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}
if (!defined('ABSPATH')) exit;


// Define Global Variables
// -----------------------
if (!defined('TABLESWP_PATH')){
	define('TABLESWP_PATH', 			    dirname(__FILE__));
}
if (!defined('TABLESWP_VERSION')){
	define('TABLESWP_VERSION', 			    '2.0.1');
}
if (!defined('TABLESWP_NAME')){
	define('TABLESWP_NAME', 				'Tablenator - Advanced Tables for WordPress & WP Bakery Page Builder');
}
if (!defined('TABLESWP_SLUG')){
	define('TABLESWP_SLUG', 				plugin_basename(__FILE__));
}
if (!defined('TABLESWP_MYSQL')){
	define('TABLESWP_MYSQL', 				'ts_advancedtables');
}
if (!defined('TABLESWP_AGGREGATE')){
	define('TABLESWP_AGGREGATE',			'ts_advancedaggregate');
}


// Functions that need to be available immediately
// -----------------------------------------------
if (!function_exists('TS_TablesWP_GetResourceURL')){
	function TS_TablesWP_GetResourceURL($relativePath){
		return plugins_url($relativePath, plugin_basename(__FILE__));
	}
}
if (!function_exists('TS_TablesWP_GetPluginVersion')){
	function TS_TablesWP_GetPluginVersion() {
		$plugin_data 						= get_plugin_data( __FILE__ );
		$plugin_version 					= $plugin_data['Version'];
		return $plugin_version;
	}
}


// Database Table Routines
// -----------------------
if (!function_exists('TS_TablesWP_Datebase_TableCheck')){
	function TS_TablesWP_Datebase_TableCheck() {
		global $wpdb;
		$charset_tablename					= $wpdb->prefix . TABLESWP_MYSQL;
		if ($wpdb->get_var("SHOW TABLES LIKE '$charset_tablename'") != $charset_tablename) {
			return false;
		} else {
			return true;
		}
	}
}
if (!function_exists('TS_TablesWP_Database_TableCreate')){
	function TS_TablesWP_Database_TableCreate($update) {
		global $wpdb;
		// Define Database Access
		$database_host						= DB_HOST;
		$database_user						= DB_USER;
		$database_pass						= DB_PASSWORD;
		$database_name						= DB_NAME;
		$database_mysqli					= null;
		$charset_tablename					= $wpdb->prefix . TABLESWP_MYSQL;
		if ($wpdb->get_var("SHOW TABLES LIKE '$charset_tablename'") != $charset_tablename) {
			$charset_collate 				= $wpdb->get_charset_collate();
			$charset_sql                    = "CREATE TABLE `$charset_tablename` (
				`id` mediumint(9) NOT NULL AUTO_INCREMENT,
				`number` mediumint(9) NOT NULL,
				`name` text NOT NULL,
				`cols` mediumint(9) NOT NULL,
				`rows` mediumint(9) NOT NULL,
				`created` timestamp DEFAULT '0000-00-00 00:00:00' NOT NULL,
				`updated` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				`merged` text NOT NULL,
				`defaults` text NOT NULL,
				`info` text NOT NULL,
				`data` longtext NOT NULL,
				`meta` longtext NOT NULL,
				`other` text NOT NULL,
				PRIMARY KEY (id)
			) $charset_collate;";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($charset_sql);
			if ($update) {
				update_option("ts_tablesplus_extend_settings_database", "true");
			}
		}
	}
}
if (!function_exists('TS_TablesWP_Database_TableDelete')){
	function TS_TablesWP_Database_TableDelete() {
		global $wpdb;
        $charset_tablename					= $wpdb->prefix . TABLESWP_MYSQL;
        $wpdb->query("DROP TABLE IF EXISTS `$charset_tablename`");
    }
}
if (!function_exists('TS_TablesWP_Database_TableColumn')){
	function TS_TablesWP_Database_TableColumn($table_column) {
		global $wpdb;
		$table_name							= $wpdb->prefix . TABLESWP_MYSQL;
		$table_check                        = $wpdb->get_results($wpdb->prepare(
			"SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s ",
			DB_NAME, $table_name, $table_column
		));
		if (!empty($table_check)) {
			return true;
		}
		return false;
	}
}


// Ensure that Function for Network Activate is Ready
// --------------------------------------------------
if (!function_exists('is_plugin_active_for_network')) {
	require_once(ABSPATH . '/wp-admin/includes/plugin.php');
}


// Main Class for Advanced Tables for VC
// -------------------------------------
if (!class_exists('TS_ADVANCED_TABLESWP')) {
	// Register / Remove Plugin Settings on Plugin Activation / Removal
	// ----------------------------------------------------------------
	require_once('registrations/ts_tableswp_registrations_plugin.php');
    
    
	// Load Global Helper Functions
	// ----------------------------
	require_once('registrations/ts_tableswp_registrations_functions.php');
    
    
	// WordPres Register Hooks
	// -----------------------
	register_activation_hook(__FILE__, 		array('TS_ADVANCED_TABLESWP', 	    'TS_TablesWP_On_Activation'));
	register_deactivation_hook(__FILE__, 	array('TS_ADVANCED_TABLESWP', 	    'TS_TablesWP_On_Deactivation'));
	register_uninstall_hook(__FILE__, 		array('TS_ADVANCED_TABLESWP', 	    'TS_TablesWP_On_Uninstall'));
    
    
	// Create Plugin Class
	// -------------------
	class TS_ADVANCED_TABLESWP {		
		// Functions for Plugin Activation / Deactivation / Uninstall
		// ----------------------------------------------------------
		public static function TS_TablesWP_On_Activation($network_wide) {
			if (!isset($wpdb)) $wpdb = $GLOBALS['wpdb'];
			global $wpdb;
			if (!current_user_can('activate_plugins')) {
				return;
			}	
			if (function_exists('is_multisite') && is_multisite()) {
				// Check if it is a Network Activation - if so, run the Activation Function for each Blog ID
				if ($network_wide) {
					$old_blog = $wpdb->blogid;
					// Get all Blog ID's
					$blogids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
					foreach ($blogids as $blog_id) {
						switch_to_blog($blog_id);
						TS_TablesWP_Callback_Activation();
						TS_TablesWP_Database_TableCreate(false);
					}
					switch_to_blog($old_blog);
					return;
				}	
			} 
			TS_TablesWP_Callback_Activation();
			TS_TablesWP_Database_TableCreate(false);
		}	
		public static function TS_TablesWP_On_Deactivation($network_wide) {
			if (!isset($wpdb)) $wpdb = $GLOBALS['wpdb'];
			global $wpdb;
			if (!current_user_can('activate_plugins')) {
				return;
			}
		}
		public static function TS_TablesWP_On_Uninstall($network_wide) {
			if (!isset($wpdb)) $wpdb = $GLOBALS['wpdb'];
			global $wpdb;
			if (!current_user_can('activate_plugins')) {
				return;
			}
			if ( __FILE__ != WP_UNINSTALL_PLUGIN) {
				return;
			}
			if (function_exists('is_multisite') && is_multisite()) {
				if ($network_wide) {
					$old_blog 	= $wpdb->blogid;
					$blogids 	= $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
					foreach ($blogids as $blog_id) {
						switch_to_blog($blog_id);
						TS_TablesWP_Callback_Uninstall();
					}
					switch_to_blog($old_blog);
					return;
				}
			}
			TS_TablesWP_Callback_Uninstall();
		}
		
		
		// Define Global Plugin Variables
		// ------------------------------
		// General Variables
		public $TS_TablesWP_PluginSlug						= "";
		public $TS_TablesWP_PluginPath						= "";
		public $TS_TablesWP_PluginDir						= "";
		public $TS_TablesWP_PluginPHP						= "";
		public $TS_TablesWP_PluginAJAX						= "false";
		public $TS_TablesWP_PluginAlways					= "false";
		public $TS_TablesWP_PluginMYSQL						= "";
		
		// Shortcode Generator Variables
		public $TS_TablesWP_GeneratorEdit					= "false";
		public $TS_TablesWP_GeneratorPost					= "";
		public $TS_TablesWP_GeneratorPage					= "false";
		public $TS_TablesWP_GeneratorAjax					= "false";
		public $TS_TablesWP_GeneratorRefer					= "false";
		public $TS_TablesWP_GeneratorLoad					= "false";
		
		// Visual Composer Variables
        public $TS_TablesWP_VisualComposer_Backend			= "false";
        public $TS_TablesWP_VisualComposer_Frontend			= "false";	
        public $TS_TablesWP_VisualComposer_Loading      	= "false";
		public $TS_TablesWP_VisualComposer_Element			= array();
		public $TS_TablesWP_VisualComposer_LeanMap			= "false";
		public $TS_TablesWP_VisualComposer_Version			= "";
		public $TS_TablesWP_VisualComposer_Active			= "false";
		
		// Cornerstone Variables
        public $TS_TablesWP_Cornerstone_Backend				= "false";
        public $TS_TablesWP_Cornerstone_Frontend			= "false";	
		public $TS_TablesWP_Cornerstone_Loading      		= "false";
		public $TS_TablesWP_Cornerstone_Version				= "";
		public $TS_TablesWP_Cornerstone_Active				= "false";
		
		// Mobile Detector Variables
		public $TS_TablesWP_MobileDetector_Global			= array();
		public $TS_TablesWP_MobileDetector_Desktop			= "true";
		public $TS_TablesWP_MobileDetector_Mobile			= "false";
		public $TS_TablesWP_MobileDetector_Tablet			= "false";
		
		// Other Variables
		public $TS_TablesWP_PluginMultiSite					= false;
		public $TS_TablesWP_PluginUsage						= true;
		public $TS_TablesWP_PluginSupport					= true;
		public $TS_TablesWP_PluginKeystring					= "";
		public $TS_TablesWP_PluginLicense					= "";
		public $TS_TablesWP_PluginEnvato					= "";
		public $TS_TablesWP_PluginValid						= false;
		public $TS_TablesWP_PluginAutoUpdate				= true;
        
        function __construct() {
			global $wpdb;
			$this->assets_js 							= plugin_dir_path( __FILE__ ) . 'js/';
			$this->assets_css 							= plugin_dir_path( __FILE__ ) . 'css/';
			$this->assets_dir 							= plugin_dir_path( __FILE__ ) . 'assets/';
            $this->assets_reg 							= plugin_dir_path( __FILE__ ) . 'registrations/';
			$this->assets_shortcodes					= plugin_dir_path( __FILE__ ) . 'shortcodes/';
			$this->assets_codestar 						= plugin_dir_path( __FILE__ ) . 'codestar/';
			$this->assets_fonts							= plugin_dir_path( __FILE__ ) . 'fonts/';
			$this->assets_builders						= plugin_dir_path( __FILE__ ) . 'builders/';
			
			$this->TS_TablesWP_PluginMYSQL 				= $wpdb->prefix . TABLESWP_MYSQL; 
			$this->TS_TablesWP_PluginSlug				= plugin_basename(__FILE__);
			$this->TS_TablesWP_PluginPath				= plugin_dir_url(__FILE__);
			$this->TS_TablesWP_PluginDir 				= plugin_dir_path( __FILE__ );			
			$this->TS_TablesWP_PluginPHP				= (TS_TABLESWP_VERSIONCompare(PHP_VERSION, '5.3.0') >= 0) ? "true" : "false";
			$this->TS_TablesWP_PluginActive				= get_option('active_plugins');
			
			//echo $wpdb->get_blog_prefix(0) . ' / ' . $wpdb->base_prefix . ' / ' . $wpdb->prefix;
			//echo 'Custom Database Table Exists: ' . (TS_TablesWP_Datebase_TableCheck() == true ? "true" : "false");
			
            // Retrieve + Validate Plugin Settings
            // -----------------------------------
			require_once($this->assets_reg . 'ts_tableswp_registrations_variables.php');
			
			// Check and Store Visual Composer Version + LeanMap
			// -------------------------------------------------
			add_action('plugins_loaded',				array($this, 	'TS_TablesWP_VisualComposerCheck'),				11);
			
			// Check for Standalone Composium Plugin
			// -------------------------------------
			if ((in_array('ts-visual-composer-extend/ts-visual-composer-extend.php', apply_filters('active_plugins', $this->TS_TablesWP_PluginActive))) || (class_exists('VISUAL_COMPOSER_EXTENSIONS'))) {
				$this->TS_TablesWP_ComposiumStandard	= "true";
			} else {
				$this->TS_TablesWP_ComposiumStandard	= "false";
			}
			
			// Check for Standalone Google Maps PLUS Plugin
			// --------------------------------------------
			if ((in_array('ts-googlemaps-for-vc/ts-googlemaps-for-vc.php', apply_filters('active_plugins', $this->TS_TablesWP_PluginActive))) || (class_exists('GOOGLEMAPS_PLUS_VC'))) {
				$this->TS_TablesWP_GoogleMapsPLUS		= "true";
			} else {
				$this->TS_TablesWP_GoogleMapsPLUS		= "false";
			}
			
			// Check for Standalone UserPro Elements for VC Plugin
			// ---------------------------------------------------
			if ((in_array('ts-userpro-for-vc/ts-userpro-for-vc.php', apply_filters('active_plugins', $this->TS_TablesWP_PluginActive))) || (class_exists('VISUAL_COMPOSER_USERPRO'))) {
				$this->TS_TablesWP_UserProVC			= "true";
			} else {
				$this->TS_TablesWP_UserProVC			= "false";
			}

			// Load Language / Translation Files
			// ---------------------------------
			if (($this->TS_TablesWP_Settings_LoadLanguage == true) && (substr(get_bloginfo('language'), 0, 2) != "en")) {
				add_action('after_setup_theme',			array($this,	'TS_TablesWP_LoadTextDomains'),					777777777);
			}
			
			// Load Global Data Arrays for Visual Composer + Shortcode Generator
			// -----------------------------------------------------------------
			add_action('after_setup_theme',				array($this, 	'TS_TablesWP_GetGlobalSelections'), 			888888888);

			// Create Custom Admin Menu for Plugin
			// -----------------------------------
			require_once($this->assets_reg . 'ts_tableswp_registrations_menu.php');
			
			// Determine Visual Composer Editor Status
			// ---------------------------------------
			add_action('init',							array($this, 	'TS_TablesWP_VisualComposer_EditorStatus'),		1);
			
			// Determine Internal Loading Status of Admin Pages
			// ------------------------------------------------
			add_action('init',							array($this, 	'TS_TablesWP_GetInternalLoadingStatus'),		1);
			
			// Determine User Role Status
			// --------------------------
			add_action('init',							array($this, 	'TS_TablesWP_GetUserRoleStatus'),				2);
			
			// Load Arrays of Font Settings
			// ----------------------------
			add_action('init',							array($this, 	'TS_TablesWP_IconFontsRequired'), 				3);	
			
			// Initialize CodeStar for Shortcode Generator
			// -------------------------------------------
			add_action('init', 							array($this, 	'TS_TablesWP_Codestar_Init'),					9);
			
			// Add Additional Links to Plugin Page
			// -----------------------------------
			$plugin 									= plugin_basename( __FILE__ );
			add_filter("plugin_action_links_$plugin", 	array($this, 	"TS_TablesWP_PluginAddSettingsLink"));
			
			// Load and Initialize the Auto-Update Class
			// -----------------------------------------
			if (($this->TS_TablesWP_PluginUsage == "true") && ($this->TS_TablesWP_PluginExtended == false) && ($this->TS_TablesWP_PluginValid == true) && (strlen($this->TS_TablesWP_PluginLicense) != 0) && (is_admin()) && (function_exists('get_plugin_data'))) {
				if ($this->TS_TablesWP_PluginAutoUpdate == true) {
					if (!class_exists('PluginUpdateChecker_2_0')) {
						require_once ('assets/ts_tableswp_assets_update.php');
					}
					$this->TS_TablesWP_PluginKernl					= new PluginUpdateChecker_2_0 ('https://kernl.us/api/v1/updates/591ce856947f0a1f7fe58f23/', __FILE__, 'ts-advanced-tables-vc', 1);
					$this->TS_TablesWP_PluginKernl->purchaseCode	= $this->TS_TablesWP_PluginLicense;
                    //$this->TS_TablesWP_PluginKernl->license       = $this->TS_TablesWP_PluginLicense;
				}
			}

			// Function to Register / Load External Files on Back-End
			// ------------------------------------------------------
			add_action('admin_enqueue_scripts',         array($this, 	'TS_TablesWP_Backend_Files'),					999999999);
			add_action('admin_head',                    array($this, 	'TS_TablesWP_Backend_Variables'),				999999999);
			add_action('admin_head',                    array($this, 	'TS_TablesWP_Backend_Head'),					999999999);
            
			// Function to Register / Load External Files on Front-End
			// -------------------------------------------------------
			add_action('wp_head',            			array($this, 	'TS_TablesWP_Frontend_Head'), 					999999999);
			
			// Detect Mobile Device Status
			// ---------------------------
			//add_action('init',						array($this, 	'TS_TablesWP_MobileDetect'), 					777777777);
			
			// Register Composer Parameters
			// ----------------------------
			add_action('init',							array($this, 	'TS_TablesWP_VisualComposer_LoadParameters'),	999999999);
			
			// Register Composer Elements
			// --------------------------
			add_action('init',							array($this, 	'TS_TablesWP_VisualComposer_LoadElements'),		999999999);
            
            // Register Shortode with WordPress
            // --------------------------------            
			add_action('init',							array($this, 	'TS_TablesWP_LoadShortcodes'), 					888888888);
			
			// Export AJAX Callback
			// --------------------
			add_action('wp_ajax_ts_export_table',		array($this, 	'TS_TablesWP_ExportTablesSingle'));
			add_action('wp_ajax_ts_export_change',		array($this, 	'TS_TablesWP_ExportTablesAjax'));
			add_action('wp_ajax_ts_backup_tables',		array($this, 	'TS_TablesWP_ExportTablesFull'));
			add_action('wp_ajax_ts_restore_reference',	array($this, 	'TS_TablesWP_RestoreAllReference'));
			
			// Other Routines
			// --------------
			$this->TS_TablesWP_PluginAlways				= ($this->TS_TablesWP_Settings_ShortcodeAlways == true ? "true" : "false");
        }
		
		// Load + Process Global Variables
		// -------------------------------
		function TS_TablesWP_GetGlobalSelections() {
			require_once($this->assets_reg . 'ts_tableswp_registrations_selections.php');
		}
		
		// Check Visual Composer Internals
		// -------------------------------
		function TS_TablesWP_VisualComposerCheck() {
			if (defined('WPB_VC_VERSION')) {
				$this->TS_TablesWP_VisualComposer_Active			= "true";
				$this->TS_TablesWP_VisualComposer_Version 			= WPB_VC_VERSION;
				if (TS_TABLESWP_VERSIONCompare(WPB_VC_VERSION, '4.9.0') >= 0) {
					$this->TS_TablesWP_VisualComposer_Required		= "true";
					if (function_exists('vc_lean_map')) {
						$this->TS_TablesWP_VisualComposer_LeanMap	= "true";
					}
				} else {
					$this->TS_TablesWP_VisualComposer_Required		= "false";
					$this->TS_TablesWP_VisualComposer_LeanMap		= "false";
				}
			} else {
				$this->TS_TablesWP_VisualComposer_Version			= '0.0.0';
				$this->TS_TablesWP_VisualComposer_Active			= "false";
				$this->TS_TablesWP_VisualComposer_Required			= "false";
				$this->TS_TablesWP_VisualComposer_LeanMap			= "false";
			}
            // Register Icon Fonts Based On Visual Composer
            require_once($this->assets_reg . 'ts_tableswp_registrations_iconfonts.php');
		}
		
		// Get Current User Role(s)
		// ------------------------
		function TS_TablesWP_GetUserRoleStatus() {
			$this->TS_TablesWP_CurrentUserRoles			= wp_get_current_user();
			$this->TS_TablesWP_CurrentUserRoles			= $this->TS_TablesWP_CurrentUserRoles->roles;
			$this->TS_TablesWP_CurrentUserEditor		= (count(array_intersect($this->TS_TablesWP_CurrentUserRoles, $this->TS_TablesWP_Settings_EditorAccess)));
		}
		
		// Initialize CodeStar Framework
		// -----------------------------
		function TS_TablesWP_Codestar_Init() {
			$this->TS_TablesWP_GeneratorEdit				= (TS_TablesWP_IsEditPagePost() == 1 ? "true" : "false");			
			$this->TS_TablesWP_GeneratorAjax				= strpos('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], 'admin-ajax.php');
			if ($this->TS_TablesWP_GeneratorAjax !== false) {
				if ($this->TS_TablesWP_GeneratorEdit == "false") {
					$this->TS_TablesWP_GeneratorRefer		= strpos('http://' . $_SERVER['HTTP_REFERER'], '?page=TS_TablesWP_Tables');
					if (($this->TS_TablesWP_GeneratorRefer === false) && ($this->TS_TablesWP_Settings_TinyMCEAllow == "true")) {
						$this->TS_TablesWP_GeneratorRefer	= (preg_match('/post-new.php|post.php/i', 'http://' . $_SERVER['HTTP_REFERER']));
					}
				} else {
					$this->TS_TablesWP_GeneratorRefer		= false;
				}
			} else {
				$this->TS_TablesWP_GeneratorPost			= TS_TablesWP_GetCurrentPostType();
				$this->TS_TablesWP_GeneratorRefer			= false;
			}
			$this->TS_TablesWP_GeneratorPage 				= strpos('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], '?page=TS_TablesWP_Tables');
			// Check Conditions
			if (is_admin()) {
				if (($this->TS_TablesWP_GeneratorEdit == "false") && (($this->TS_TablesWP_GeneratorPage !== false) || ($this->TS_TablesWP_GeneratorRefer !== false))) {
					$this->TS_TablesWP_GeneratorLoad		= "true";
				} else if (($this->TS_TablesWP_GeneratorEdit == "true") && ($this->TS_TablesWP_Settings_TinyMCEAllow == "true")) {
					$this->TS_TablesWP_GeneratorLoad		= "true";
				}
			} else {
				$this->TS_TablesWP_GeneratorLoad			= "false";
			}
			// Load + Initialize Codestar
			if ($this->TS_TablesWP_GeneratorLoad == "true") {
				if (!defined('CS_ACTIVE_SHORTCODE')) {
					define('CS_ACTIVE_SHORTCODE',			true);
				}
				if (!function_exists('cs_framework_init') && !class_exists('CSFramework')) {
					require_once($this->assets_codestar . 'cs-framework.php');
				}
				require_once($this->assets_reg . 'ts_tableswp_registrations_generator.php');
				add_filter('cs_shortcode_exclude',			array($this, 	'TS_TablesWP_ExcludedPostTypes'));
				add_action('cs_load_option_fields', 		array($this, 	'TS_TablesWP_Codestar_CustomFields'));
			}
		}
		function TS_TablesWP_Codestar_CustomFields() {
			require_once($this->assets_reg . 'ts_tableswp_registrations_codestar.php');
		}
		
		
		// Add additional "Settings" Link to Plugin Listing Page
		// -----------------------------------------------------
		function TS_TablesWP_PluginAddSettingsLink($links) {
			if (current_user_can('manage_options')) {
				$links[] = '<a href="admin.php?page=TS_TablesWP_Settings" target="_parent">' . __( "Settings", "ts_visual_composer_extend" ) . '</a>';
			}
			$links[] = '<a href="http://www.tablenatorvc.krautcoding.com/documentation" target="_blank">' . __( "Documentation", "ts_visual_composer_extend" ) . '</a>';
			$links[] = '<a href="http://helpdesk.krautcoding.com/changelog-advanced-tables-for-visual-composer/" target="_blank">' . __( "Changelog", "ts_visual_composer_extend" ) . '</a>';
			return $links;
		}
		
		// Check + Initialize Mobile Detector
		// ----------------------------------
		function TS_TablesWP_MobileDetect() {
			if (!class_exists('Mobile_Detect')) {
				require_once($this->assets_dir . 'ts_tableswp_assets_mobiledetect.php');
			}
			// Check Device Type
			if (class_exists('Mobile_Detect')) {
				$this->TS_TablesWP_MobileDetector_Global		= new Mobile_Detect;
				$this->TS_TablesWP_MobileDetector_Mobile		= ($this->TS_TablesWP_MobileDetector_Global->isMobile() == 1 ? 'true' : 'false');
				$this->TS_TablesWP_MobileDetector_Tablet		= ($this->TS_TablesWP_MobileDetector_Global->isTablet() == 1 ? 'true' : 'false');
				if ($this->TS_TablesWP_MobileDetector_Mobile == 'false') {
					$this->TS_TablesWP_MobileDetector_Desktop	= 'true';
				} else {
					$this->TS_TablesWP_MobileDetector_Desktop	= 'false';
				}
				unset($this->TS_TablesWP_MobileDetector_Global);
			}
		}
		
		// Exclude All Post Types from Generator
		// -------------------------------------
		function TS_TablesWP_ExcludedPostTypes() {
			$posttypes 									= get_post_types();
			$postexclude 								= array();
			foreach ($posttypes as $type) {
				if (($this->TS_TablesWP_Settings_TinyMCEAllow == "true") && (in_array($type, $this->TS_TablesWP_Settings_TinyMCEPostTypes))) {
					continue;
				} else {
					$postexclude[]						= $type;
				}				
			}			
			return $postexclude;
		}
		
		// Get Core Data of Created Tables
		// -------------------------------
		function TS_TablesWP_GetTablesCoreData() {
			$this->TS_TablesWP_Custom_Tables			= get_option("ts_tablesplus_extend_settings_tables", array());
			if (!is_array($this->TS_TablesWP_Custom_Tables)) {
				$this->TS_TablesWP_Custom_Tables		= $this->TS_TablesWP_Settings_Tables;
			} else if(count($this->TS_TablesWP_Custom_Tables) == 0) {
				$this->TS_TablesWP_Custom_Tables		= $this->TS_TablesWP_Settings_Tables;
			}
			if (is_array($this->TS_TablesWP_Custom_Tables)) {
				if ($this->TS_TablesWP_Settings_InitialSort == 1) {
					TS_TablesWP_SortMultiArray($this->TS_TablesWP_Custom_Tables, 'id', true);
				} else if ($this->TS_TablesWP_Settings_InitialSort == 2) {
					TS_TablesWP_SortMultiArray($this->TS_TablesWP_Custom_Tables, 'name', true);
				} else if ($this->TS_TablesWP_Settings_InitialSort == 3) {
					TS_TablesWP_SortMultiArray($this->TS_TablesWP_Custom_Tables, 'create', true);
				} else if ($this->TS_TablesWP_Settings_InitialSort == 4) {
					TS_TablesWP_SortMultiArray($this->TS_TablesWP_Custom_Tables, 'update', true);
				}
			}
		}
		
		// Get Listing of Utilized Table IDs
		// ---------------------------------
		function TS_TablesWP_GetTablesUsedIDs() {
			$this->TS_TablesWP_Custom_UsedIDs           = get_option("ts_tablesplus_extend_settings_usedids", array());
			if (!is_array($this->TS_TablesWP_Custom_UsedIDs)) {
				$this->TS_TablesWP_Custom_UsedIDs       = $this->TS_TablesWP_Settings_UsedIDs;
			} else if(count($this->TS_TablesWP_Custom_UsedIDs) == 0) {
				$this->TS_TablesWP_Custom_UsedIDs       = $this->TS_TablesWP_Settings_UsedIDs;
			}
		}
        
		// Load Language Domain
		// --------------------
		function TS_TablesWP_LoadTextDomains() {
			load_plugin_textdomain('ts_visual_composer_extend', false, dirname(plugin_basename( __FILE__ )) . '/locale');
		}
		
		// Declare Arrays with Icon Font Data
		// ----------------------------------
		function TS_TablesWP_IconFontsRequired() {
			if ($this->TS_TablesWP_IconsCompliant_Loading == "true") {
				$this->TS_TablesWP_IconFontsArrays();
			}
		}
		function TS_TablesWP_IconFontsArrays() {
			// Define Arrays for Font Icons
			// ----------------------------
			$this->TS_TablesWP_Active_Icon_Fonts          	= 0;
			$this->TS_TablesWP_Active_Icon_Count          	= 0;
			$this->TS_TablesWP_Total_Icon_Count           	= 0;
			$this->TS_TablesWP_Default_Icon_Fonts         	= array();

			// Define Global Font Arrays
			$this->TS_TablesWP_Icons_Blank 					= array(
				'' 						=> '',
			);
			$this->TS_TablesWP_Fonts_Blank 					= array(
				'All Fonts' 			=> '',
			);
			
			// Set Array for Full Icon List based on Icon Picker
			$this->TS_TablesWP_List_Icons_Compliant			= array();	
			
			$this->TS_TablesWP_List_Active_Fonts          	= array();
			$this->TS_TablesWP_List_Select_Fonts          	= $this->TS_TablesWP_Fonts_Blank;
			
			$this->TS_TablesWP_List_Initial_Icons         	= $this->TS_TablesWP_Icons_Blank;
			
			$this->TS_TABLESWP_Name_Initial_Font          	= "";
			$this->TS_TablesWP_Class_Initial_Font         	= "";
			
			// Add "Tablenator" Internal Fonts
			foreach ($this->TS_TablesWP_Icon_Font_Settings as $Icon_Font => $iconfont) {
				if ($iconfont['setting'] != 'Custom') {
					$this->TS_TablesWP_Default_Icon_Fonts[$Icon_Font] 								= $iconfont['setting'];
					// Check if Font is enabled
					$default 																		= ($iconfont['default'] == "true" ? 1 : 0);
					$this->{'TS_TablesWP_tinymce' . $iconfont['setting'] . ''}              		= $iconfont['active'];
					// Load Font Arrays					
					if ((!isset($this->{'TS_TablesWP_Icons_' . $iconfont['setting'] . ''})) && (($this->{'TS_TablesWP_tinymce' . $iconfont['setting'] . ''} == "true"))) {						
						require_once($this->assets_fonts . ('ts_tableswp_font_' . strtolower($iconfont['setting']) . '.php'));
					} else {
						$this->{'TS_TablesWP_Icons_Compliant_' . $iconfont['setting'] . ''}			= array();
					}
					// Get Icon Count in Font
					$this->{'TS_TablesWP_tinymce' . $iconfont['setting'] . 'Count'}					= $iconfont['count'];
					// Add Font Icons to Global Arrays
					if (($this->{'TS_TablesWP_tinymce' . $iconfont['setting'] . ''} == "true")) {
						$this->TS_TablesWP_Active_Icon_Fonts++;
						$this->TS_TablesWP_List_Active_Fonts[$Icon_Font] 							= $iconfont['setting'];
						uksort($this->{'TS_TablesWP_Icons_Compliant_' . $iconfont['setting'] . ''}, "TS_TablesWP_CaseInsensitiveSort");
						$this->TS_TablesWP_Active_Icon_Count  										= $this->TS_TablesWP_Active_Icon_Count + $this->{'TS_TablesWP_tinymce' . $iconfont['setting'] . 'Count'};
						if ($this->TS_TablesWP_Active_Icon_Fonts == 1) {
							$this->TS_TablesWP_List_Initial_Icons 									= $this->TS_TablesWP_List_Initial_Icons + $this->{'TS_TablesWP_Icons_Compliant_' . $iconfont['setting'] . ''};
							$this->TS_TABLESWP_Name_Initial_Font 									= $Icon_Font;
							$this->TS_TablesWP_Class_Initial_Font 									= $iconfont['setting'];
						}
					}
					$this->TS_TablesWP_List_Icons_Compliant											= $this->TS_TablesWP_List_Icons_Compliant + $this->{'TS_TablesWP_Icons_Compliant_' . $iconfont['setting'] . ''};
					$this->TS_TablesWP_Total_Icon_Count       										= $this->TS_TablesWP_Total_Icon_Count + $this->{'TS_TablesWP_tinymce' . $iconfont['setting'] . 'Count'};
				}
			}
			
			// Add Visual Composer Internal Fonts (VC v4.4.0+)
			foreach ($this->TS_TablesWP_VisualComposer_Font_Settings as $Icon_Font => $iconfont) {
				$this->TS_TablesWP_Default_Icon_Fonts[$Icon_Font] 									= $iconfont['setting'];
				// Check if Font is enabled
				$default 																			= ($iconfont['default'] == "true" ? 1 : 0);					
				$this->{'TS_TablesWP_tinymce' . $iconfont['setting'] . ''}              			= $iconfont['active'];
				// Load Font Arrays
				if ((!isset($this->{'TS_TablesWP_Icons_' . $iconfont['setting'] . ''})) && (($this->{'TS_TablesWP_tinymce' . $iconfont['setting'] . ''} == "true"))) {
					require_once($this->assets_fonts . ('ts_tableswp_font_vc_' . strtolower($iconfont['setting']) . '.php'));
				} else {
					$this->{'TS_TablesWP_Icons_Compliant_VC_' . $iconfont['setting'] . ''}			= array();
				}
				// Get Icon Count in Font
				$this->{'TS_TablesWP_tinymce' . $iconfont['setting'] . 'Count'}						= $iconfont['count'];
				// Add Font Icons to Global Arrays					
				if (($this->{'TS_TablesWP_tinymce' . $iconfont['setting'] . ''} == "true")) {
					$this->TS_TablesWP_Active_Icon_Fonts++;
					$this->TS_TablesWP_List_Active_Fonts[$Icon_Font] 								= $iconfont['setting'];
					uksort($this->{'TS_TablesWP_Icons_Compliant_VC_' . $iconfont['setting'] . ''}, "TS_TablesWP_CaseInsensitiveSort");
					$this->TS_TablesWP_Active_Icon_Count  											= $this->TS_TablesWP_Active_Icon_Count + $this->{'TS_TablesWP_tinymce' . $iconfont['setting'] . 'Count'};
					if ($this->TS_TablesWP_Active_Icon_Fonts == 1) {
						$this->TS_TablesWP_List_Initial_Icons 										= $this->TS_TablesWP_List_Initial_Icons + $this->{'TS_TablesWP_Icons_Compliant_VC_' . $iconfont['setting'] . ''};
						$this->TS_TABLESWP_Name_Initial_Font 										= $Icon_Font;
						$this->TS_TablesWP_Class_Initial_Font 										= $iconfont['setting'];
					}
				}
				$this->TS_TablesWP_List_Icons_Compliant												= $this->TS_TablesWP_List_Icons_Compliant + $this->{'TS_TablesWP_Icons_Compliant_VC_' . $iconfont['setting'] . ''};
				$this->TS_TablesWP_Total_Icon_Count       											= $this->TS_TablesWP_Total_Icon_Count + $this->{'TS_TablesWP_tinymce' . $iconfont['setting'] . 'Count'};
			}
			
			$this->TS_TablesWP_List_Select_Fonts          											= $this->TS_TablesWP_List_Select_Fonts + $this->TS_TablesWP_List_Active_Fonts;
		}
		function TS_TablesWP_IconFontsEnqueue($forceload = false) {
			// Enqueue Internal Tablenator Fonts
			foreach ($this->TS_TablesWP_Icon_Font_Settings as $Icon_Font => $iconfont) {
				if (($iconfont['active'] == 'true') && ($iconfont['type'] != "Custom") && ($iconfont['type'] != "WordPress")) {
					wp_enqueue_style('ts-font-' . strtolower($iconfont['setting']));
				} else if (($iconfont['active'] == 'true') && ($iconfont['type'] != "Custom") && ($iconfont['type'] == "WordPress")) {
					wp_enqueue_style('dashicons');
					wp_enqueue_style('ts-font-' . strtolower($iconfont['setting']));
				} else if (($iconfont['active'] == 'true') && ($iconfont['type'] == "Custom")) {
					wp_enqueue_style('ts-font-' . strtolower($iconfont['setting']) . 'vcsc');
				}
			}
			// Enqueue Internal Visual Composer Fonts
			foreach ($this->TS_TablesWP_VisualComposer_Font_Settings as $Icon_Font => $iconfont) {
				if ($iconfont['active'] == 'true') {
					wp_enqueue_style(strtolower($iconfont['handle']));
				}
			}
		}

		// Function to load External Files on Back-End when Editing
		// --------------------------------------------------------
		function TS_TablesWP_Backend_Files($hook_suffix) {
			global $pagenow, $typenow;
			if (!function_exists('get_current_screen')) {
				require_once(ABSPATH . '/wp-admin/includes/screen.php');
			}
			$screen 						= get_current_screen();
			require_once($this->assets_reg . 'ts_tableswp_registrations_files.php');
			if (empty($typenow) && !empty($_GET['post'])) {
				$post 						= get_post($_GET['post']);
				$typenow 					= $post->post_type;
			}
			$url							= plugin_dir_url( __FILE__ );
			$TS_TablesWP_IsEditPagePost     = TS_TablesWP_IsEditPagePost();
			$TS_TablesWP_IsEditPostType     = TS_TablesWP_GetCurrentPostType();
			$TS_TablesWP_IsEditCustomPost   = false;
			// Files to be loaded with Visual Composer
			if (($this->TS_TablesWP_VisualComposer_Frontend == "true") || ($this->TS_TablesWP_VisualComposer_Loading == "true")) {
				wp_enqueue_style('ts-extend-preloaders');
				wp_enqueue_style('ts-extend-nouislider');
				wp_enqueue_script('ts-extend-nouislider');
				wp_enqueue_style('ts-advanced-tables-composer');
				if (($this->TS_TablesWP_ComposiumStandard == "false") && ($this->TS_TablesWP_GoogleMapsPLUS == "false")) {
					wp_enqueue_style('ts-advanced-tables-parameters');
					wp_enqueue_script('ts-advanced-tables-parameters');
				}
			}
			// Files for Injected tinyMCE Generator
			if ((($TS_TablesWP_IsEditPagePost) && $this->TS_TablesWP_Settings_TinyMCEAllow == "true")) {
				if (in_array($TS_TablesWP_IsEditPostType, $this->TS_TablesWP_Settings_TinyMCEPostTypes)) {
					wp_enqueue_style('ts-advanced-tables-generator');
					wp_enqueue_script('ts-advanced-tables-generator');
				}
			}
            // Files to be loaded for Plugin Pages
            global $TS_TablesWP_Page_Main;        
            global $TS_TablesWP_Page_Tables;
			global $TS_TablesWP_Page_Initial;
            global $TS_TablesWP_Page_AddNew;
            global $TS_TablesWP_Page_Modify;
			global $TS_TablesWP_Page_Export;
			global $TS_TablesWP_Page_Import;
            global $TS_TablesWP_Page_Rebuild;
			global $TS_TablesWP_Page_Migrate;
			global $TS_TablesWP_Page_Delete;
			global $TS_TablesWP_Page_Maintain;
            global $TS_TablesWP_Page_Settings;
			global $TS_TablesWP_Page_License;
			global $TS_TablesWP_Page_CatTags;
            if (($TS_TablesWP_Page_Main == $hook_suffix) || ($TS_TablesWP_Page_Tables == $hook_suffix) || ($TS_TablesWP_Page_Initial == $hook_suffix) || ($TS_TablesWP_Page_AddNew == $hook_suffix) || ($TS_TablesWP_Page_Modify == $hook_suffix) || ($TS_TablesWP_Page_Delete == $hook_suffix) || ($TS_TablesWP_Page_Maintain == $hook_suffix) || ($TS_TablesWP_Page_Export == $hook_suffix) || ($TS_TablesWP_Page_Import == $hook_suffix) || ($TS_TablesWP_Page_Rebuild == $hook_suffix) || ($TS_TablesWP_Page_Migrate == $hook_suffix) || ($TS_TablesWP_Page_CatTags == $hook_suffix) || ($TS_TablesWP_Page_Settings == $hook_suffix) || ($TS_TablesWP_Page_License == $hook_suffix)) {
                if (!wp_script_is('jquery')) {
					wp_enqueue_script('jquery');
				}
                wp_enqueue_style('dashicons');
            }
			if ($TS_TablesWP_Page_Migrate == $hook_suffix) {
				// Retrieve Required Settings
				$this->TS_TablesWP_GetTablesCoreData();
				$this->TS_TablesWP_GetTablesUsedIDs();
				wp_enqueue_style('ts-extend-preloaders');
				wp_enqueue_style('ts-extend-sweetalert');
				wp_enqueue_script('ts-extend-sweetalert');
				wp_enqueue_style('ts-advanced-tables-font');
                wp_enqueue_style('ts-advanced-tables-settings');
			}
			if ($TS_TablesWP_Page_CatTags == $hook_suffix) {
				// Retrieve Required Settings
				$this->TS_TablesWP_GetTablesCoreData();
				$this->TS_TablesWP_GetTablesUsedIDs();
				wp_enqueue_script('jquery-ui-sortable');
				wp_enqueue_style('ts-extend-preloaders');
				wp_enqueue_style('ts-extend-sweetalert');
				wp_enqueue_script('ts-extend-sweetalert');
				wp_enqueue_style('ts-extend-krauttipster');
				wp_enqueue_script('ts-extend-krauttipster');
				wp_enqueue_style('ts-advanced-tables-font');
                wp_enqueue_style('ts-advanced-tables-settings');
				wp_enqueue_script('validation-engine');
				wp_enqueue_style('validation-engine');
				wp_enqueue_script('validation-engine-en');
				wp_enqueue_style('ts-advanced-tables-cattags');
				wp_enqueue_script('ts-advanced-tables-cattags');
			}
			if (($TS_TablesWP_Page_Delete == $hook_suffix) || ($TS_TablesWP_Page_Rebuild == $hook_suffix)) {
				wp_enqueue_style('ts-extend-preloaders');
			}
			if ($TS_TablesWP_Page_License == $hook_suffix) {
				wp_enqueue_style('ts-extend-preloaders');
				wp_enqueue_style('ts-extend-sweetalert');
				wp_enqueue_script('ts-extend-sweetalert');
				wp_enqueue_style('ts-advanced-tables-font');
				wp_enqueue_script('ts-advanced-tables-settings');
                wp_enqueue_style('ts-advanced-tables-settings');
			}
			if (($TS_TablesWP_Page_Maintain == $hook_suffix) || ($TS_TablesWP_Page_Export == $hook_suffix) || ($TS_TablesWP_Page_Import == $hook_suffix)) {
				// Retrieve Required Settings
				$this->TS_TablesWP_GetTablesCoreData();
				$this->TS_TablesWP_GetTablesUsedIDs();
				// Plugin JS/CSS
				wp_enqueue_style('ts-extend-preloaders');
				wp_enqueue_script('ts-extend-clipboard');
				wp_enqueue_style('ts-extend-sweetalert');
				wp_enqueue_script('ts-extend-sweetalert');
				wp_enqueue_style('ts-advanced-tables-font');
                wp_enqueue_style('ts-advanced-tables-settings');
				wp_enqueue_script('ts-advanced-tables-settings');
			}
			if (($TS_TablesWP_Page_Export == $hook_suffix) || ($TS_TablesWP_Page_Import == $hook_suffix)) {
				wp_enqueue_style('ts-extend-sumo');
				wp_enqueue_script('ts-extend-sumo');
			}
			if ($TS_TablesWP_Page_Settings == $hook_suffix) {
				// Retrieve Required Settings
				$this->TS_TablesWP_GetTablesCoreData();
				$this->TS_TablesWP_GetTablesUsedIDs();
				// Plugin JS/CSS
				wp_enqueue_style('ts-extend-preloaders');
				wp_enqueue_style('ts-extend-sweetalert');
				wp_enqueue_script('ts-extend-sweetalert');
				wp_enqueue_style('ts-extend-nouislider');
				wp_enqueue_script('ts-extend-nouislider');
				wp_enqueue_style('ts-extend-multiselect');
				wp_enqueue_script('ts-extend-multiselect');
				wp_enqueue_style('ts-extend-sumo');
				wp_enqueue_script('ts-extend-sumo');
				wp_enqueue_style('ts-advanced-tables-font');
				wp_enqueue_script('validation-engine');
				wp_enqueue_style('validation-engine');
				wp_enqueue_script('validation-engine-en');
                wp_enqueue_style('ts-advanced-tables-settings');
                wp_enqueue_script('ts-advanced-tables-settings');
			}
            if (($TS_TablesWP_Page_Main == $hook_suffix) || ($TS_TablesWP_Page_Tables == $hook_suffix)) {
				// Retrieve Required Settings
				$this->TS_TablesWP_GetTablesCoreData();
				$this->TS_TablesWP_GetTablesUsedIDs();
				// Plugin JS/CSS
				wp_enqueue_style('ts-extend-preloaders');
				wp_enqueue_script('ts-extend-clipboard');
				wp_enqueue_style('ts-extend-sweetalert');
				wp_enqueue_script('ts-extend-sweetalert');
				wp_enqueue_style('ts-extend-datatables-full');
				wp_enqueue_style('ts-extend-datatables-custom');
				wp_enqueue_script('ts-extend-datatables-full');
				wp_enqueue_script('ts-extend-datatables-jszip');
				wp_enqueue_script('ts-extend-datatables-pdfmaker');
				wp_enqueue_script('ts-extend-datatables-pdffonts');
				wp_enqueue_style('ts-advanced-tables-font');
				wp_enqueue_script('validation-engine');
				wp_enqueue_style('validation-engine');
				wp_enqueue_script('validation-engine-en');
				wp_enqueue_style('ts-advanced-tables-generator');
				wp_enqueue_script('ts-advanced-tables-generator');
                wp_enqueue_style('ts-advanced-tables-settings');
                wp_enqueue_script('ts-advanced-tables-settings');
            }
            if ($TS_TablesWP_Page_Initial == $hook_suffix) {
				// Retrieve Required Settings
				$this->TS_TablesWP_GetTablesCoreData();
				$this->TS_TablesWP_GetTablesUsedIDs();
                // Plugin JS/CSS
                wp_enqueue_style('ts-extend-preloaders');
				wp_enqueue_style('ts-extend-sweetalert');
				wp_enqueue_script('ts-extend-sweetalert');
				wp_enqueue_style('ts-advanced-tables-font');
				wp_enqueue_style('ts-advanced-tables-editor');
				wp_enqueue_script('ts-advanced-tables-grid');
                wp_enqueue_style('ts-advanced-tables-settings');
			}
			if (($TS_TablesWP_Page_AddNew == $hook_suffix) || ($TS_TablesWP_Page_Modify == $hook_suffix)) {
				// Retrieve Required Settings
				$this->TS_TablesWP_GetTablesCoreData();
				$this->TS_TablesWP_GetTablesUsedIDs();
                // Adjust tinyMCE Toolbar Buttons
                add_filter("mce_buttons",               array($this, 	"TS_TablesWP_TinyMceButtons1"),                 99999999);
                add_filter("mce_buttons_2",             array($this, 	"TS_TablesWP_TinyMceButtons2"),                 99999999);
                add_filter("mce_buttons_3",             array($this, 	"TS_TablesWP_TinyMceButtons3"),                 99999999);
				// Icon Font Files
				$this->TS_TablesWP_IconFontsEnqueue(false);
                // WordPress Internal JS/CSS
                wp_enqueue_media();
                wp_enqueue_script('jquery-ui-autocomplete');
                // Plugin JS/CSS
                wp_enqueue_style('ts-extend-preloaders');
				wp_enqueue_script('validation-engine');
				wp_enqueue_style('validation-engine');
				wp_enqueue_script('validation-engine-en');
				wp_enqueue_style('ts-extend-sweetalert');
				wp_enqueue_script('ts-extend-sweetalert');
				wp_enqueue_script('ts-extend-csvparser');
				wp_enqueue_script('ts-extend-iconpicker');
				wp_enqueue_style('ts-extend-iconpicker');
                wp_enqueue_script('ts-extend-momentjs');
				wp_enqueue_style('ts-extend-pikaday');
				wp_enqueue_script('ts-extend-pikaday');
				wp_enqueue_script('ts-extend-zeroclipboard');
                wp_enqueue_script('ts-extend-numbro');
                wp_enqueue_script('ts-extend-languages');                
				wp_enqueue_style('ts-extend-handsontable');
				wp_enqueue_script('ts-extend-handsontable');
                wp_enqueue_script('ts-extend-rulejsmain');
				wp_enqueue_script('ts-extend-rulejsformula');
				wp_enqueue_script('ts-extend-rulejsparser');
				wp_enqueue_style('ts-extend-colorpicker');
				wp_enqueue_script('ts-extend-colorpicker');
				wp_enqueue_style('ts-advanced-tables-font');
				wp_enqueue_style('ts-advanced-tables-editor');
				wp_enqueue_script('ts-advanced-tables-editor');
                wp_enqueue_style('ts-advanced-tables-settings');
            }
        }
        function TS_TablesWP_Backend_Head() {
            
        }
        function TS_TablesWP_Backend_Variables() {
            
        }
        
		// Function to load External Files on Front-End
		// --------------------------------------------
		function TS_TablesWP_Frontend_Files($hook_suffix) {
			
		}
		function TS_TablesWP_Frontend_Head() {
			global $post;
			global $wp_query;
			$url 									= plugin_dir_url( __FILE__ );
            require_once($this->assets_reg . 'ts_tableswp_registrations_files.php');
			// Check For Standard Frontend Page
			if (!is_404() && !is_search() && !is_archive()) {
				$TS_TablesWP_StandardFrontendPage	= "true";
			} else {
				$TS_TablesWP_StandardFrontendPage	= "false";
			}
			if (!empty($post)){
				if ($this->TS_TablesWP_Settings_LoadFilesAlways == true) {
					if ($this->TS_TablesWP_VisualComposer_Frontend == "false") {
						$this->TS_TablesWP_IconFontsEnqueue(false);
                        if ($this->TS_TablesWP_Settings_LoadDataTable == true) {
							wp_enqueue_style('ts-extend-datatables-full');
							wp_enqueue_style('ts-extend-datatables-custom');
							wp_enqueue_script('ts-extend-datatables-full');
							wp_enqueue_script('ts-extend-datatables-jszip');
							wp_enqueue_script('ts-extend-datatables-pdfmaker');
							wp_enqueue_script('ts-extend-datatables-pdffonts');
						}
						if ($this->TS_TablesWP_Settings_LoadTableSaw == true) {
							wp_enqueue_style('ts-extend-tablesaw');
							wp_enqueue_script('ts-extend-tablesaw');
						}
						if ($this->TS_TablesWP_Settings_LoadFooTable == true) {
							wp_enqueue_style('ts-extend-footable');
							wp_enqueue_script('ts-extend-footable');
						}
						if ($this->TS_TablesWP_Settings_LoadToolTipster) {
							wp_enqueue_style('ts-extend-tooltipster');
							wp_enqueue_script('ts-extend-tooltipster');
						}
						if ($this->TS_TablesWP_Settings_LoadHelpers == true) {
							wp_enqueue_script('ts-extend-numbro');
							wp_enqueue_script('ts-extend-momentjs');
							wp_enqueue_script('ts-extend-languages');
						}
						wp_enqueue_style('ts-advanced-tables-font');
						wp_enqueue_style('ts-extend-preloaders');
						wp_enqueue_style('ts-extend-advancedtables');
						wp_enqueue_script('ts-extend-advancedtables');                        
					}					
				}
			}
        }
        function TS_TablesWP_Frontend_Variables() {
            
        }
        
        // Functions to Control Button Output in tinyMCE Toolbar
        // -----------------------------------------------------
        function TS_TablesWP_TinyMceButtons1($buttons) {
            return array(
                "bold", 
                "italic", 
                "underline", 
                "strikethrough", 
                "separator",
                "bullist",
                "numlist",
                "separator",
                "alignleft",
                "aligncenter",
                "alignright",
                "alignjustify",
                "separator",
                "forecolor",
                "separator",
                /*"link",
                "unlink",
                "separator",*/
                "removeformat",
                "separator",
                "undo", 
                "redo", 
                "separator",
            );
        }
        function TS_TablesWP_TinyMceButtons2($buttons) {
            return array();
        }
        function TS_TablesWP_TinyMceButtons3($buttons) {
            return array();
        }
		
		// Determine Internal Loading Status of Admin Pages
		// ------------------------------------------------
		function TS_TablesWP_GetInternalLoadingStatus() {
			// Check AJAX Request Status
			$this->TS_TablesWP_PluginAJAX						= ($this->TS_TablesWP_RequestIsFrontendAJAX() == true ? "true" : "false");
			// Check Icon Picker Status
			$this->TS_TablesWP_IconsCompliant_Loading			= "false";
			if ((strpos('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], '?page=TS_TablesWP_AddNew') !== false) || (strpos('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], '?page=TS_TablesWP_Modify') !== false)) {
				$this->TS_TablesWP_IconsCompliant_Loading		= "true";
			}
		}
        
		// Determine Visual Composer Editor Status
		// ---------------------------------------
		function TS_TablesWP_VisualComposer_EditorStatus() {
			// Check for Visual Composer Roles Manager
			if (strpos('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], '?page=vc-roles') !== false) {
				$TS_TablesWP_Extension_RoleManager				= "true";		
			} else {
				$TS_TablesWP_Extension_RoleManager				= "false";
			}
			// Check for Elements for Users - Addon for Visual Composer
			if (strpos('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], '?page=mcw_elements_for_users') !== false) {
				$TS_TablesWP_Extension_ElementsUser				= "true";		
			} else {
				$TS_TablesWP_Extension_ElementsUser				= "false";
			}
			// Determine if Visual Composer Form Request
			if (array_key_exists('action', $_REQUEST)) {
				$TS_TablesWP_Extension_Request					= ($_REQUEST["action"] != "vc_edit_form" ? "false" : "true");
			} else {
				$TS_TablesWP_Extension_Request					= "false";
			}
			// Determine Standard Page Editor
			$this->TS_TablesWP_VisualComposer_Backend			= (TS_TablesWP_IsEditPagePost() == 1 ? "true" : "false");
			// Determine Frontend Editor Status
			if (function_exists('vc_is_inline')){
				if (vc_is_inline() == true) {
					$this->TS_TablesWP_VisualComposer_Frontend 			= "true";
				} else {
					if ((vc_is_inline() == NULL) || (vc_is_inline() == '')) {
						if (TS_TablesWP_CheckFrontEndEditor() == true) {
							$this->TS_TablesWP_VisualComposer_Frontend 	= "true";
						} else {
							$this->TS_TablesWP_VisualComposer_Frontend 	= "false";
						}	
					} else {
						$this->TS_TablesWP_VisualComposer_Frontend 		= "false";
					}
				}
			} else {
				$this->TS_TablesWP_VisualComposer_Frontend 				= "false";
			}
			// Set Global Load Status
			if (($this->TS_TablesWP_VisualComposer_Backend == "false") && ($TS_TablesWP_Extension_Request == "false") && (is_admin()) && ($TS_TablesWP_Extension_RoleManager == "false") && ($TS_TablesWP_Extension_ElementsUser == "false") && (defined('WPB_VC_VERSION'))) {
				$this->TS_TablesWP_VisualComposer_Loading		= "false";
			} else if (defined('WPB_VC_VERSION')) {
				$this->TS_TablesWP_VisualComposer_Loading		= "true";
			} else {
				$this->TS_TablesWP_VisualComposer_Loading		= "false";
			}
		}
		
		// Load Visual Composer Parameters
		// -------------------------------
		function TS_TablesWP_VisualComposer_LoadParameters() {			
			if (($this->TS_TablesWP_VisualComposer_Frontend == "true") || ($this->TS_TablesWP_VisualComposer_Loading == "true")) {
				// Retrieve Required Settings
				if ($this->TS_TablesWP_Settings_ComposerIntegrate) {
					$this->TS_TablesWP_GetTablesCoreData();
					$this->TS_TablesWP_GetTablesUsedIDs();
				}
				// Load Shared Custom Settings Parameters
				if (($this->TS_TablesWP_ComposiumStandard == "false") && ($this->TS_TablesWP_GoogleMapsPLUS == "false")) {
					if (($this->TS_TablesWP_Settings_ComposerIntegrate) || ($this->TS_TablesWP_UserProVC == "true")) {
						foreach ($this->TS_TablesWP_VisualComposer_Parameters as $ParameterName => $parameter) {
							require_once($this->assets_builders . 'visualcomposer/parameters/ts_tableswp_parameter_' . $parameter['file'] . '.php');
						}
					}
				}
				// Load Mandatory Custom Settings Parameters
				if ($this->TS_TablesWP_Settings_ComposerIntegrate) {
					require_once($this->assets_builders . 'visualcomposer/parameters/ts_tableswp_parameter_tables.php');
				}
			}
		}
		
		// Load Visual Composer Element(s)
		// -------------------------------
		function TS_TablesWP_VisualComposer_LoadElements() {
			if ($this->TS_TablesWP_Settings_ComposerIntegrate) {
				if (($this->TS_TablesWP_VisualComposer_Frontend == "true") || ($this->TS_TablesWP_VisualComposer_Loading == "true")) {
					if ($this->TS_TablesWP_VisualComposer_LeanMap == "true") {
						vc_lean_map('TS_Advanced_Tables', null, $this->assets_builders . 'visualcomposer/elements/ts_tableswp_element_table.php');
						//vc_lean_map('TS_Advanced_Charts', null, $this->assets_builders . 'visualcomposer/elements/ts_tableswp_element_chart.php');
					} else {
						require_once($this->assets_builders . 'visualcomposer/elements/ts_tableswp_element_table.php');
						//require_once($this->assets_builders . 'visualcomposer/elements/ts_tableswp_element_chart.php');
					}
				}
				// Add Mapped Shortcodes to VC (AJAX Callabck Fix)
				if (class_exists("WPBMap") && method_exists("WPBMap", "addAllMappedShortcodes")) {
					WPBMap::addAllMappedShortcodes();
				}
			}						
		}		
		
		// Load Cornerstone Element(s)
		// ---------------------------
		function TS_TablesWP_Cornerstone_LoadElements() {
			require_once($this->assets_builders . 'cornerstone/ts_tableswp_element_table.php');
		}
		
		// Load Element(s) Shortcode(s)
		// ----------------------------
		function TS_TablesWP_LoadShortcodes() {
			if ((is_admin() == false) || ($this->TS_TablesWP_VisualComposer_Frontend == "true") || ($this->TS_TablesWP_PluginAJAX == "true") || ($this->TS_TablesWP_PluginAlways == "true")) {
				require_once($this->assets_shortcodes . 'ts_tableswp_shortcode_table.php');
				//require_once($this->assets_shortcodes . 'ts_tableswp_shortcode_chart.php');
			}
		}
		
		// Function to Check if AJAX Request Originates in Frontend
		// --------------------------------------------------------
		function TS_TablesWP_RequestIsFrontendAJAX() {
			$script_filename = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '';		   
			// Try to figure out if frontend AJAX request... If we are DOING_AJAX; let's look closer
			if ((defined('DOING_AJAX') && DOING_AJAX)) {
				$ref = '';
				if (!empty($_REQUEST['_wp_http_referer'])) {
					$ref = wp_unslash( $_REQUEST['_wp_http_referer'] );
				} elseif (!empty($_SERVER['HTTP_REFERER'])) {
					$ref = wp_unslash( $_SERVER['HTTP_REFERER']);
				}		   
				// If referer does not contain admin URL and we are using the admin-ajax.php endpoint, this is likely a frontend AJAX request
				if (((strpos($ref, admin_url()) === false) && (basename($script_filename) === 'admin-ajax.php'))) {
					return true;
				}
			}
			// If no checks triggered, we end up here - not an AJAX request.
			return false;
		}
		
		// Function to Prepare Table Data
		// ------------------------------
		function TS_TablesWP_PrepareAllTables() {
			global $wpdb;
			$TS_TablesWP_Editor_BaseName				= $this->TS_TablesWP_PluginMYSQL;
			// Get All Tables From Database
			$TS_TablesWP_Editor_Results 				= $wpdb->get_results("SELECT `id`, `number`, `name`, `cols`, `rows`, `created`, `updated`, `merged`, `info`, `other` FROM $TS_TablesWP_Editor_BaseName ORDER BY number", ARRAY_A);
			$TS_TablesWP_Editor_ID						= null;
			$TS_TablesWP_Editor_Number					= null;
			$TS_TablesWP_Editor_Name					= null;
			$TS_TablesWP_Editor_Other					= array();
			$TS_TablesWP_Editor_Count					= 0;
			foreach ($TS_TablesWP_Editor_Results as $table) {
				// Get Table Information
				$TS_TablesWP_Editor_ID					= $table['id'];
				$TS_TablesWP_Editor_Number				= $table['number'];
				$TS_TablesWP_Editor_Name				= $table['name'];
				$TS_TablesWP_Editor_Other				= $table['other'];
				$TS_TablesWP_Editor_Other				= json_decode($TS_TablesWP_Editor_Other);
				$TS_TablesWP_Editor_Count				= 0;
				// Add Table Categories + Tags
				if (isset($this->TS_TablesWP_Custom_Tables['table' . $TS_TablesWP_Editor_Number])) {
					if ($TS_TablesWP_Editor_Other->categories != $this->TS_TablesWP_Custom_Tables['table' . $TS_TablesWP_Editor_Number]['categories']) {
						$TS_TablesWP_Editor_Other->categories	= $this->TS_TablesWP_Custom_Tables['table' . $TS_TablesWP_Editor_Number]['categories'];
						$TS_TablesWP_Editor_Count++;
					}
					if ($TS_TablesWP_Editor_Other->tags != $this->TS_TablesWP_Custom_Tables['table' . $TS_TablesWP_Editor_Number]['tags']) {
						$TS_TablesWP_Editor_Other->tags			= $this->TS_TablesWP_Custom_Tables['table' . $TS_TablesWP_Editor_Number]['tags'];
						$TS_TablesWP_Editor_Count++;
					}
				}				
				// Update Table In Database
				if ($TS_TablesWP_Editor_Count > 0) {
					$TS_TablesWP_Editor_Other			= json_encode($TS_TablesWP_Editor_Other);
					$wpdb->update($TS_TablesWP_Editor_BaseName,
						array(
							"other" 					=> stripcslashes($TS_TablesWP_Editor_Other),			
						), array(
							'id' 						=> $TS_TablesWP_Editor_ID
						), null, array('%d')
					);
				}
			}
			return true;
		}
		function TS_TablesWP_RestoreAllReference() {
            if (!isset($_GET['secret']) || $_GET['secret'] != md5( md5( AUTH_KEY . SECURE_AUTH_KEY ) . '-' . 'ts-advanced-tables') ) {
                header("Content-Type: text/html");
				echo __("Invalid Secret for AJAX request!", "ts_visual_composer_extend");
                Exit();
            }
			global $wpdb;
			$TS_TablesWP_Editor_BaseName				= $this->TS_TablesWP_PluginMYSQL;
			// Get All Tables From Database
			$TS_TablesWP_Editor_Results 				= $wpdb->get_results("SELECT `id`, `number`, `name`, `cols`, `rows`, `created`, `updated`, `merged`, `info`, `other` FROM $TS_TablesWP_Editor_BaseName ORDER BY number", ARRAY_A);
			$TS_TablesWP_Editor_Number					= null;
			$TS_TablesWP_Editor_Other					= array();
			$TS_TablesWP_Editor_Option					= array();
			foreach ($TS_TablesWP_Editor_Results as $table) {
				// Get Table Information
				$TS_TablesWP_Editor_Number				= $table['number'];
				$TS_TablesWP_Editor_Other				= $table['other'];
				$TS_TablesWP_Editor_Other				= json_decode($TS_TablesWP_Editor_Other, true);
				$TS_TablesWP_Editor_Option['table' . $TS_TablesWP_Editor_Number]	= array(
					"id"								=> $TS_TablesWP_Editor_Number,
					"name"								=> $table['name'],
					"create"							=> intval(strtotime($table['created'])),
					"update"							=> intval(strtotime($table['updated'])),
					"info"								=> base64_encode($table['info']),
					"rows"								=> $table['rows'],
					"columns"							=> $table['cols'],
					"merged"							=> $table['merged'],
					"charts"							=> "false",
					"categories"						=> $TS_TablesWP_Editor_Other['categories'],
					"tags"								=> $TS_TablesWP_Editor_Other['tags'],
				);
			}
			// Save Rebuild Reference Option
			update_option("ts_tablesplus_extend_settings_tables", $TS_TablesWP_Editor_Option);
			// Return Reference Data
			header("Content-Type: application/json");
			$TS_TablesWP_Editor_Option					= (array) $TS_TablesWP_Editor_Option;
			echo json_encode($TS_TablesWP_Editor_Option);
			Exit();
		}
		
		// Function to Export Table Data
		// -----------------------------
		function TS_TablesWP_ExportTablesSingle() {
            if (!isset($_GET['secret']) || $_GET['secret'] != md5( md5( AUTH_KEY . SECURE_AUTH_KEY ) . '-' . 'ts-advanced-tables') ) {
                wp_die(__("Invalid secret for export request!", "ts_visual_composer_extend"));
                Exit();
            }
			global $wpdb;
			if (isset($_GET['tableid'])) {
				$TS_TablesWP_Editor_ID              	= $_GET['tableid'];
			} else {
				$TS_TablesWP_Editor_ID					= '';
			}				
			if ((isset($_GET['action']) && $_GET['action'] == 'ts_export_table') && ($TS_TablesWP_Editor_ID != '')) {
				$TS_TablesWP_Editor_BaseName			= $this->TS_TablesWP_PluginMYSQL;
				$TS_TablesWP_Editor_BaseData			= $wpdb->get_row("SELECT * FROM $TS_TablesWP_Editor_BaseName WHERE number = $TS_TablesWP_Editor_ID");
				if ($TS_TablesWP_Editor_BaseData == null) {
					wp_redirect(admin_url('admin.php?page=TS_TablesWP_Tables'));
					Exit();
				}
				if (isset($TS_TablesWP_Editor_BaseData->name)) {
					$TS_TablesWP_Editor_Name			= $TS_TablesWP_Editor_BaseData->name;
				} else {
					$TS_TablesWP_Editor_Name			= "";
				}
				$TS_TablesWP_Editor_Name 				= str_replace(' ', '_', $TS_TablesWP_Editor_Name);
				$TS_TablesWP_Editor_Date				= current_time('timestamp', 0);
				$TS_TablesWP_Editor_Date				= date('Y-m-d @H-i-s', $TS_TablesWP_Editor_Date);
				$TS_TablesWP_Editor_Date 				= str_replace(' ', '_', $TS_TablesWP_Editor_Date);
				$TS_TablesWP_Editor_BaseData			= base64_encode(json_encode($TS_TablesWP_Editor_BaseData));
				header('Content-Description: File Transfer');
				header('Content-Type: application/txt');
				header('Content-Disposition: attachment; filename="Tablenator_Export_-_' . $TS_TablesWP_Editor_Name . '_(' . $TS_TablesWP_Editor_Date . ').txt"');
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				echo $TS_TablesWP_Editor_BaseData;
				Exit();
			} else {
				wp_redirect(admin_url('admin.php?page=TS_TablesWP_Tables'));
				Exit();
			}
		}
		function TS_TablesWP_ExportTablesAjax() {			
            if (!isset($_GET['secret']) || $_GET['secret'] != md5( md5( AUTH_KEY . SECURE_AUTH_KEY ) . '-' . 'ts-advanced-tables') ) {
                header("Content-Type: text/html");
				echo __("Invalid secret for AJAX request!", "ts_visual_composer_extend");
				Exit();
            }		
			if ((!isset($_GET['tableid'])) || ($_GET['tableid'] == "")) {
				header("Content-Type: application/json");
				echo base64_encode(json_encode(array()));
				Exit();
			}			
			global $wpdb;
			$TS_TablesWP_Editor_ID						= (int) $_GET['tableid'];
			$TS_TablesWP_Editor_BaseName				= $this->TS_TablesWP_PluginMYSQL;
			$TS_TablesWP_Editor_BaseData				= $wpdb->get_row("SELECT * FROM $TS_TablesWP_Editor_BaseName WHERE number = $TS_TablesWP_Editor_ID");
			if ($TS_TablesWP_Editor_BaseData == null) {
				$TS_TablesWP_Editor_BaseData			= array();
			}
			// Return Table Data
			header("Content-Type: text/html");
			echo base64_encode(json_encode($TS_TablesWP_Editor_BaseData));
			Exit();
		}
		function TS_TablesWP_ExportTablesFull() {
            if (!isset($_GET['secret']) || $_GET['secret'] != md5( md5( AUTH_KEY . SECURE_AUTH_KEY ) . '-' . 'ts-advanced-tables') ) {
                wp_die(__("Invalid secret for export request!", "ts_visual_composer_extend"));
                Exit();
            }
			global $wpdb;
			if ((isset($_GET['action']) && $_GET['action'] == 'ts_backup_tables')) {
				// Define Database Access
				$database_host							= DB_HOST;
				$database_user							= DB_USER;
				$database_pass							= DB_PASSWORD;
				$database_name							= DB_NAME;
				$database_mysqli						= null;
				// Define Database Table
				if (empty($table_prefix)) {
					$table_prefix						= $wpdb->prefix;
				}
				$table_base								= TABLESWP_MYSQL;			
				$table_name								= $this->TS_TablesWP_PluginMYSQL;
				$table_create							= $table_prefix . $table_base;
				// Prepare Table Data in Database
				$this->TS_TablesWP_PrepareAllTables();
				// Get/Create File Information
				$file_content							= "";
				$file_date								= date('Y-m-d @H-i-s', current_time('timestamp', 0));
				$file_name 								= $table_create . '_(' . $file_date . ').sql';				
				// Store Last Backup Storage
				update_option("ts_tablesplus_extend_settings_lastbackup", $file_date);				
				// Set Time Limit
				set_time_limit(3000);
				// Initialize Database Connection
				$database_mysqli						= new mysqli($database_host, $database_user, $database_pass, $database_name);
				$database_mysqli->select_db($database_name);
				$database_mysqli->query("SET NAMES 'utf8'");
				// Get Table Columns
				$table_columns							= array();
				$table_describe							= $wpdb->get_col("DESC {$table_name}", 0);
				foreach ($table_describe as $column) {
					$table_columns[] 					= "`" . $column . "`";
				}
				$table_columns 							= implode(', ', $table_columns);
				$table_columns 							= "(" . $table_columns . ")";
				unset($table_describe);
				// Get Table Rows
				$table_result							= $database_mysqli->query('SELECT * FROM `' . $table_name . '`');
				$table_fields 							= $table_result->field_count;
				$table_rowcount							= $database_mysqli->affected_rows;
				$table_restore							= $database_mysqli->query('SHOW CREATE TABLE ' . $table_create);
				$table_structure						= $table_restore->fetch_row(); 			
				$table_structure[1]						= str_ireplace('CREATE TABLE `', 'CREATE TABLE IF NOT EXISTS `', $table_structure[1]);
				// Create SQL Output
				$file_content							.= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\r\nSET time_zone = \"+00:00\";\r\n\r\n\r\n/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\r\n/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\r\n/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\r\n/*!40101 SET NAMES utf8 */;\r\n--\r\n-- Database: `" . $table_create . "`\r\n--\r\n\r\n\r\n";
				$file_content 							.= "\n\n" . $table_structure[1] . ";\n\n";
				for ($i = 0, $st_counter = 0; $i < $table_fields; $i++, $st_counter = 0) {
					while ($row = $table_result->fetch_row())	{
						// When started (and every after 5 command cycle):
						if ($st_counter%5 == 0 || $st_counter == 0 ) {
							$file_content 				.= "\nINSERT INTO `" . $table_create . "` " . $table_columns . " VALUES";
						}
						$file_content 					.= "\n(";
						for ($j = 0; $j < $table_fields; $j++){
							$row[$j] 					= str_replace("\n","\\n", addslashes($row[$j]));
							if (isset($row[$j])){
								$file_content 			.= '"' . $row[$j] . '"' ;
							}  else{
								$file_content 			.= '""';
							}
							if ($j < ($table_fields - 1)){
								$file_content			.= ',';
							}
						}
						$file_content 					.=")";
						// Every after 5 command cycle [or at last line] ... but should be inserted 1 cycle earlier
						if ((($st_counter+1)%5 == 0 && $st_counter != 0) || $st_counter + 1 == $table_rowcount) {
							$file_content 				.= ";";
						} else {
							$file_content 				.= ",";
						}
						$st_counter						= $st_counter + 1;
					}
				}
				$file_content 							.="\n\n\n";
				$file_content 							.= "\r\n\r\n/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\r\n/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\r\n/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;";			
				$database_mysqli->close();
				ob_get_clean();
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header("Content-Transfer-Encoding: Binary");
				header('Content-Length: '. (function_exists('mb_strlen') ? mb_strlen($file_content, '8bit'): strlen($file_content)) );
				header("Content-Disposition: attachment; filename=\"" . $file_name . "\"");
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				echo $file_content;				
				Exit();
			} else {
				wp_redirect(admin_url('admin.php?page=TS_TablesWP_Maintain'));
				Exit();
			}
		}
    }
}
global $TS_ADVANCED_TABLESWP;
if (class_exists('TS_ADVANCED_TABLESWP')) {
	$TS_ADVANCED_TABLESWP = new TS_ADVANCED_TABLESWP;
}
?>