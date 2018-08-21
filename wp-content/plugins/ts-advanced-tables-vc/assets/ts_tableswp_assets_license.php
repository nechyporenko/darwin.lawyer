<?php
	global $TS_ADVANCED_TABLESWP;
	
	// Global Variables
	// ----------------
	$ts_tablesplus_extend_settings_licenseKeyed				= '';
	$ts_tablesplus_extend_settings_licenseInfo				= '';
	$ts_tablesplus_extend_settings_licenseMessage			= '';
	$ts_tablesplus_extend_settings_licenseUpdate			= 0;
	$ts_tablesplus_extend_settings_licenseRemove			= 'false';
	$ts_tablesplus_extend_settings_licenseValid				= 0;
	
	
	// Class with Envato API Routines
	// ------------------------------
	if (!class_exists('TS_TablesWP_EnvatoAPIRoutines')) {
		class TS_TablesWP_EnvatoAPIRoutines {
			// Private Class Variables
			private $envato_item							= "18560899";
			private $envato_token 							= "QnFOMlI0b3NOU1ZiTTBmS3VOTjNBbFJHdk53c3Q2RUY=";
			
			// Public Class Variables
			public $envato_code								= '';			
			public $envato_protocol							= "";
			public $envato_domain							= "";
			public $envato_url								= "";
			public $envato_success 							= false;
			public $envato_result 							= false;
			public $envato_response							= "";
			public $envato_error							= "";
			public $envato_message							= "";
			public $envato_status							= "";
			
			// Initialize Class
			function __construct($envato_item, $envato_token) {
				// Populate Global Variables
				if ($envato_item != "") {
					$this->envato_item						= $envato_item;
				}
				if ($envato_token != "") {
					$this->envato_token						= $envato_token;
				}
				$this->envato_protocol						= $this->TS_TablesWP_HelperSiteProtocol() . '://';
				$this->envato_domain						= $this->envato_protocol . preg_replace('#^https?://#', '', site_url());
			}
			
			// Initialize License Code Routine
			function TS_TablesWP_LicenseCodeInit($envato_type, $envato_code) {
				// Reset Global Variables
				$this->envato_success 						= false;
				$this->envato_result 						= false;
				$this->envato_error 						= '';
				$this->envato_message 						= '';
				$this->envato_status 						= '';
				if ($envato_type == "newlicense") {
					// Get New License Code from Form
					$this->TS_TablesWP_LicenseCodeNew($envato_code);
				} else if ($envato_type == "unlicense") {
					// Remove (Unlicense) License Code
					$this->TS_TablesWP_LicenseCodeRemove();
				} else if ($envato_type == "checklicense") {
					// Retrieve Stored License Code
					$this->TS_TablesWP_LicenseCodeRetrieval();
					// Check License Code via API
					$this->TS_TablesWP_LicenseCodeCheckup();
					// Build License Code HTML Summary
					$this->TS_TablesWP_LicenseCodeSummary();	
					// Store License Code Data
					$this->TS_TablesWP_LicenceCodeStorage();
				} else if ($envato_type == "getlicense") {
					// Retrieve Stored License Code
					$this->TS_TablesWP_LicenseCodeRetrieval();
				} else if ($envato_type == "cleanup") {
					
				}
			}
			
			// Retrieve and Store New License Key
			function TS_TablesWP_LicenseCodeNew($envato_code) {
				global $TS_ADVANCED_TABLESWP;
				$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['data'] 	= $envato_code;
				$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['update']	= true;
				if ($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginMultiSite == true) {
					update_site_option('ts_tablesplus_extend_settings_licenseData',	$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent);
				} else {
					update_option('ts_tablesplus_extend_settings_licenseData',		$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent);
				}
			}
			
			// Remove License Code (Unlicensing)
			function TS_TablesWP_LicenseCodeRemove() {
				global $TS_ADVANCED_TABLESWP;		
				$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['data']		= "";
				$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['keyed']	= "unlicenseinprogress";
				$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['update']	= true;
				if ($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginMultiSite == true) {
					update_site_option('ts_tablesplus_extend_settings_licenseData',	$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent);
				} else {
					update_option('ts_tablesplus_extend_settings_licenseData',		$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent);
				}
			}
			
			// Retrieve Stored License Code
			function TS_TablesWP_LicenseCodeRetrieval() {
				global $TS_ADVANCED_TABLESWP;
				$this->envato_code							= $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['data'];
			}
			
			// Check Validity of License Code
			function TS_TablesWP_LicenseCodeCheckup() {
				global $TS_ADVANCED_TABLESWP;
				if (!in_array(base64_encode($this->envato_code), $TS_ADVANCED_TABLESWP->TS_TablesWP_API_Duplications)) {
					if ((function_exists('curl_init')) && (strlen($this->envato_code) != 0)) {
						$envato_url 						= "https://api.envato.com/v1/market/private/user/verify-purchase:" . preg_replace('/[^a-zA-Z0-9_ -]/s', '', $this->envato_code) . ".json";
						$envato_timeout						= 60;
						$envato_header 						= array();
						$envato_header[] 					= 'Content-length: 0';
						$envato_header[] 					= 'Content-type: application/json; charset=utf-8';
						$envato_header[] 					= 'Authorization: Bearer '. base64_decode($this->envato_token);					
						$envato_agent						= $this->TS_TablesWP_HelperRandomUserAgent();
						$envato_curl						= curl_init();
						curl_setopt($envato_curl, CURLOPT_URL,               	$envato_url);
						curl_setopt($envato_curl, CURLOPT_CUSTOMREQUEST, 		'GET');					
						curl_setopt($envato_curl, CURLOPT_RETURNTRANSFER,    	true);
						curl_setopt($envato_curl, CURLOPT_CONNECTTIMEOUT,    	$envato_timeout);
						curl_setopt($envato_curl, CURLOPT_MAXREDIRS,         	3);
						curl_setopt($envato_curl, CURLOPT_SSL_VERIFYHOST, 		false);
						curl_setopt($envato_curl, CURLOPT_SSL_VERIFYPEER, 		false);
						curl_setopt($envato_curl, CURLOPT_USERAGENT,         	$envato_agent);
						curl_setopt($envato_curl, CURLOPT_HTTPHEADER,        	$envato_header);
						$this->envato_response				= curl_exec($envato_curl);
						if (!curl_errno($envato_curl)) {
							$this->envato_success			= true;
							$this->envato_error				= '';
						} else {
							$this->envato_success			= false;
							$this->envato_error				= curl_errno($envato_curl);
						}
						curl_close($envato_curl);
						if (!empty($this->envato_response)) {
							$this->envato_response			= json_decode($this->envato_response, true);
							if (!is_array($this->envato_response)) {
								$this->envato_success		= false;
								$this->envato_response		= false;
							} else if (empty($this->envato_response['verify-purchase'])) {
								$this->envato_success		= false;
								$this->envato_response		= false;
							} else {
								$this->envato_response		= $this->envato_response['verify-purchase'];
								if ($this->envato_response['item_id'] != (int)$this->envato_item) {
									$this->envato_success	= false;
									$this->envato_response	= false;
								}
							}
						} else {
							$this->envato_success			= false;
							$this->envato_response			= false;
						}
					}
				}
			}
			
			// Compile License Code Summary
			function TS_TablesWP_LicenseCodeSummary() {
				global $TS_ADVANCED_TABLESWP;				
				if ($this->envato_error != '') {
					$this->envato_message .= '<br><br/>';
					$this->envato_message .= '<span id="ts-license-check-error" class="ts-license-check-message">' . __("The Envato API appeared to be unresponsive during the last license check or another problem occured. The following error has been returned:", "ts_visual_composer_extend");
					if ($this->envato_error != '') {
						$this->envato_message .= '<br><br/><strong>' . $this->TS_TablesWP_HelperErrorsCURL($this->envato_error) . '</strong>';
					} else {
						$this->envato_message = '<br><br/><strong>' . __("No error message available.", "ts_visual_composer_extend")  . '</strong></span>';
					}
					$this->envato_message .= '<br><br/>' . __("Please attempt the license check at a later time again.", "ts_visual_composer_extend");
				} else if (($this->envato_response != false) && ($this->envato_code != "")) {
					if (($this->envato_domain != "http://tablenatorvc.krautcoding.com") && ($this->envato_domain != "http://www.tablenatorvc.krautcoding.com") && ($this->envato_domain != "http://testdrive.krautcoding.com/advancedtablesforvc")) {
						$this->envato_message .= '<div id="ts-license-check-confirmed" class="ts-license-check-message" data-key="' . $this->envato_code . '">';
							$this->envato_message .= '<br/><br/>';
							$this->envato_message .= '<div class="clearFixMe">';						
								$this->envato_message .= '<div style="float: left;">';
									$this->envato_message .= '<img src="' . TS_TablesWP_GetResourceURL('images/envato/envato_logo.png') . '" width="111" height="125" style="float: left; margin-right: 20px;">';
								$this->envato_message .= '</div>';						
								$this->envato_message .= '<div style="float: left;">';
									$this->envato_message .= '<strong>' . __("Thank you for your Purchase!", "ts_visual_composer_extend")  . '</strong><br/>';
									$this->envato_message .= '<span>' . __("License Type:", "ts_visual_composer_extend")  . ' ' . $this->envato_response['licence'] . '</span><br/>';
									$this->envato_message .= '<span style="display: none;">' . __("License Key:", "ts_visual_composer_extend")  . ' ' . $this->envato_code . '</span><br/>';
									$this->envato_message .= '<span>' . __("Item Name (ID):", "ts_visual_composer_extend")  . ' ' . $this->envato_response['item_name'] . ' (' . $this->envato_response['item_id'] . ')</span><br/>';
									$this->envato_message .= '<span>' . __("Buyer User Name:", "ts_visual_composer_extend")  . ' ' . $this->envato_response['buyer'] . '</span><br/>';
									$this->envato_message .= '<span>' . __("Purchase Date:", "ts_visual_composer_extend")  . ' ' . date('Y/m/d - h:i:s A', strtotime($this->envato_response['created_at'])) . '</span><br/>';
									$this->envato_message .= '<span>' . __("Support Until:", "ts_visual_composer_extend")  . ' ' . date('Y/m/d - h:i:s A', strtotime($this->envato_response['supported_until'])) . '</span><br/>';
									$this->envato_message .= '<span>' . __("Licensed Domain:", "ts_visual_composer_extend")  . ' ' . $this->envato_domain . '</span><br/>';
									$this->envato_message .= '<span>' . __("Last Check:", "ts_visual_composer_extend")  . ' ' . date('Y/m/d - h:i:s A') . '</span><br/>';
									$this->envato_message .= '<br/>';
									$this->envato_message .= '<a href="http://www.tablenatorvc.krautcoding.com/documentation/" target="_blank">' . __("Documentation", "ts_visual_composer_extend")  . '</a> | ';
									$this->envato_message .= '<a href="http://helpdesk.krautcoding.com/forums/forum/wordpress-plugins/advanced-tables-for-visual-composer/" target="_blank">' . __("Support Forum", "ts_visual_composer_extend")  . '</a> | ';
									$this->envato_message .= '<a href="http://www.tablenatorvc.krautcoding.com/contact-us/" target="_blank">' . __("Author Contact", "ts_visual_composer_extend")  . '</a> | ';
									$this->envato_message .= '<a href="https://codecanyon.net/item/tablenator-advanced-tables-for-visual-composer/18560899" target="_blank">' . __("Buy More Licenses", "ts_visual_composer_extend")  . '</a> | ';
									$this->envato_message .= '<a href="http://codecanyon.net/user/Tekanewa/portfolio" target="_blank">' . __("Other Products", "ts_visual_composer_extend")  . '</a>';
								$this->envato_message .= '</div>';
							$this->envato_message .= '</div>';
						$this->envato_message .= '</div>';
						$this->envato_success				= true;
					} else {
						$this->envato_message .= '<br/><br/><span id="ts-license-check-restricted" class="ts-license-check-message">' . __("You can not use your license key on this demo website!", "ts_visual_composer_extend") . '</span>';
						$this->envato_success				= false;
					}
				} else if (($this->envato_response == false) && ($this->envato_code != "")) {
					$this->envato_message .= '<br/><br/><span id="ts-license-check-invalid" class="ts-license-check-message">' . __("The provided license key could not be confirmed!", "ts_visual_composer_extend") . '</span>';
					$this->envato_success					= false;
				} else {
					$this->envato_message .= '<br/><br/><span id="ts-license-check-missing" class="ts-license-check-message">' . __("Please enter a valid license key!", "ts_visual_composer_extend") . '</span>';
					$this->envato_success					= false;
				}				
			}
			
			// Store License Code Data
			function TS_TablesWP_LicenceCodeStorage() {
				global $TS_ADVANCED_TABLESWP;
				if ((strlen($this->envato_message) != 0 && ($this->envato_success == true))) {
					if ((strlen($this->envato_code) == 0) || (strpos($this->envato_message, $this->envato_code) === FALSE)) {
						$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['data'] 		= $this->envato_code;
						$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['info'] 		= ((strlen($this->envato_code) != 0) ? $this->envato_message : '');
						$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['keyed'] 		= "emptydelimiterfix";
						$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['valid'] 		= false;
						$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['remove'] 		= false;
						$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['update'] 		= false;
						$this->envato_status													= '<div id="ts-license-check-status" class="clearFixMe" style="color: red; font-weight: bold; padding-bottom: 10px;">' . __("License check has been initiated but was unsuccessful!", "ts_visual_composer_extend") . '</div>';
						$LicenseCheckStatus 													= $this->envato_status;
						$LicenseCheckSuccess 													= 0;
					} else if ((strlen($this->envato_code) != 0) && (strpos($this->envato_message, $this->envato_code) != FALSE)) {
						$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['data'] 		= $this->envato_code;
						$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['info'] 		= $this->envato_message;
						$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['keyed'] 		= $this->envato_code;
						$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['valid'] 		= true;
						$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['remove'] 		= false;
						$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['update'] 		= false;
						$this->envato_status													= '<div id="ts-license-check-status" class="clearFixMe" style="color: green; font-weight: bold; padding-bottom: 10px;">' . __("License check has been succesfully completed!", "ts_visual_composer_extend") . '</div>';
						$LicenseCheckStatus 													= $this->envato_status;
						$LicenseCheckSuccess 													= 1;
					} else {
						$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['data'] 		= $this->envato_code;
						$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['info'] 		= ((strlen($this->envato_code) != 0) ? $this->envato_message : '');
						$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['keyed'] 		= "emptydelimiterfix";
						$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['valid'] 		= false;
						$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['remove'] 		= false;
						$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['update'] 		= false;
						$this->envato_status													= '<div id="ts-license-check-status" class="clearFixMe" style="color: red; font-weight: bold; padding-bottom: 10px;">' . __("License check has been initiated but was unsuccessful!", "ts_visual_composer_extend") . '</div>';
						$LicenseCheckStatus 													= $this->envato_status;
						$LicenseCheckSuccess 													= 0;
					}
				} else {
					$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['data'] 			= $this->envato_code;
					$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['info'] 			= "";
					$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['keyed'] 			= "emptydelimiterfix";
					$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['valid'] 			= false;
					$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['remove'] 			= false;
					$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['update'] 			= false;
					if (in_array(base64_encode($this->envato_code), $TS_ADVANCED_TABLESWP->TS_TablesWP_API_Duplications)) {
						$this->envato_status													= '<div id="ts-license-check-status" class="clearFixMe" style="color: red; font-weight: bold; padding-bottom: 10px;">' . __("The license key has been revoked by Envato due to a full refund of the purchase price!", "ts_visual_composer_extend") . '</div>';
					} else {
						$this->envato_status													= '<div id="ts-license-check-status" class="clearFixMe" style="color: red; font-weight: bold; padding-bottom: 10px;">' . __("License check could not be initiated - Missing license key!", "ts_visual_composer_extend") . '</div>';
					}
					$LicenseCheckStatus 														= $this->envato_status;
					$LicenseCheckSuccess 														= 0;
				}
				if ($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginMultiSite == true) {
					update_site_option('ts_tablesplus_extend_settings_licenseData',				$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent);
				} else {
					update_option('ts_tablesplus_extend_settings_licenseData',					$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent);
				}
			}
			
			// Initialize Item Info Routine
			function TS_TablesWP_ItemInfoInit() {
				// Reset Global Variables
				$this->envato_success 						= false;
				$this->envato_result 						= false;
				$this->envato_error 						= '';
				$this->envato_message 						= '';
				// Retrieve Item Data via API
				$this->TS_TablesWP_ItemInfoSummary();
				// Return HTML Output
				return $this->envato_message;
			}
			
			// Retrieve Item Information from Envato
			function TS_TablesWP_ItemInfoSummary() {
				global $TS_ADVANCED_TABLESWP;
				if (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Envato_Globals['migrate'])) {
					$envato_migrate 						= $TS_ADVANCED_TABLESWP->TS_TablesWP_Envato_Globals['migrate'];
				} else if (isset($TS_ADVANCED_TABLESWP->TS_TablesWP_Envato_Defaults['migrate'])) {
					$envato_migrate 						= $TS_ADVANCED_TABLESWP->TS_TablesWP_Envato_Defaults['migrate'];
				} else {
					$envato_migrate							= false;
				}
				$envato_storage								= get_option("ts_tablesplus_extend_settings_envato", array());
				if (isset($envato_storage['last'])) {
					$envato_last							= $envato_storage['last'];
				} else {
					$envato_last							= 0;
				}
				$envato_current								= time();
				if (isset($envato_storage['data'])) {
					$envato_data							= $envato_storage['data'];
				} else {
					$envato_data							= "";
				}
				if (($envato_data == "") || (($envato_last + 3600) < $envato_current) || ($envato_migrate == false)) {
					if ((function_exists('curl_init')) && (strlen($this->envato_item) != 0)) {
						$envato_url 						= "https://api.envato.com/v2/market/catalog/item?id=" . $this->envato_item;
						$envato_timeout						= 60;
						$envato_header 						= array();
						$envato_header[] 					= 'Content-length: 0';
						$envato_header[] 					= 'Content-type: application/json; charset=utf-8';
						$envato_header[] 					= 'Authorization: Bearer '. base64_decode($this->envato_token);	
						$envato_agent						= $this->TS_TablesWP_HelperRandomUserAgent();
						$envato_curl						= curl_init();
						curl_setopt($envato_curl, CURLOPT_URL,               	$envato_url);
						curl_setopt($envato_curl, CURLOPT_CUSTOMREQUEST, 		'GET');					
						curl_setopt($envato_curl, CURLOPT_RETURNTRANSFER,    	true);
						curl_setopt($envato_curl, CURLOPT_CONNECTTIMEOUT,    	$envato_timeout);
						curl_setopt($envato_curl, CURLOPT_MAXREDIRS,         	3);
						curl_setopt($envato_curl, CURLOPT_SSL_VERIFYHOST, 		false);
						curl_setopt($envato_curl, CURLOPT_SSL_VERIFYPEER, 		false);
						curl_setopt($envato_curl, CURLOPT_USERAGENT,         	$envato_agent);
						curl_setopt($envato_curl, CURLOPT_HTTPHEADER,        	$envato_header);
						$this->envato_response				= curl_exec($envato_curl);						
						if (!curl_errno($envato_curl)) {
							$this->envato_success			= true;
							$this->envato_error				= '';
						} else {
							$this->envato_success			= false;
							$this->envato_error				= curl_errno($envato_curl);
						}
						curl_close($envato_curl);						
						if (!empty($this->envato_response)) {
							$this->envato_response			= json_decode($this->envato_response, true);
							if (!is_array($this->envato_response)) {
								$this->envato_success		= false;
								$this->envato_response		= false;
							} else {
								if ($this->envato_response['id'] != (int)$this->envato_item) {
									$this->envato_success	= false;
									$this->envato_response	= false;
								}
							}
						} else {
							$this->envato_success			= false;
							$this->envato_response			= false;
						}
					} else {
						$this->envato_success				= false;
						$this->envato_response				= false;
					}
					if (($this->envato_response == false) && ($envato_data != "")) {
						$this->envato_response				= $envato_data;
					}
				} else {
					$this->envato_success					= false;
					$this->envato_response					= $envato_data;
				}
				if (($this->envato_response == false) || ($this->envato_response == 'false')) {
					$this->envato_message .= '<p style="text-align: justify;">' . __( "Oops... Something went wrong. Could not retrieve item information from Envato.", "ts_visual_composer_extend" ) . '</p>';
				} else {
					// Parse Item Data					
					$TS_Kraut_Envato_Item_Name     						= (isset($this->envato_response["name"]) 				? $this->envato_response["name"] 				: "N/A");
					$TS_Kraut_Envato_Item_User							= (isset($this->envato_response["author_username"]) 	? $this->envato_response["author_username"] 	: "N/A");
					$TS_Kraut_Envato_Item_Rating						= (isset($this->envato_response["rating"]["rating"]) 	? $this->envato_response["rating"]["rating"] 	: "N/A");
					$TS_Kraut_Envato_Item_Votes							= (isset($this->envato_response["rating"]["count"]) 	? $this->envato_response["rating"]["count"] 	: "N/A");
					$TS_Kraut_Envato_Item_Sales							= (isset($this->envato_response["number_of_sales"]) 	? $this->envato_response["number_of_sales"] 	: "N/A");
					$TS_Kraut_Envato_Item_Price							= (isset($this->envato_response["price_cents"]) 		? $this->envato_response["price_cents"] 		: "N/A");
					$TS_Kraut_Envato_Item_Thumb							= (isset($this->envato_response["thumbnail_url"]) 		? $this->envato_response["thumbnail_url"] 		: "N/A");
					$TS_Kraut_Envato_Item_Link							= (isset($this->envato_response["url"]) 				? $this->envato_response["url"] 				: "N/A");
					$TS_Kraut_Envato_Item_Release						= (isset($this->envato_response["published_at"]) 		? $this->envato_response["published_at"] 		: "N/A");
					$TS_Kraut_Envato_Item_Update						= (isset($this->envato_response["updated_at"]) 			? $this->envato_response["updated_at"] 			: "N/A");
					$TS_Kraut_Envato_Item_Check							= time();
					// Populate Data Array
					$envato_array							= array();
					$envato_array["name"] 					= $TS_Kraut_Envato_Item_Name;
					$envato_array["author_username"]		= $TS_Kraut_Envato_Item_User;
					$envato_array["rating"]["rating"]		= $TS_Kraut_Envato_Item_Rating;
					$envato_array["rating"]["count"]		= $TS_Kraut_Envato_Item_Votes;
					$envato_array["number_of_sales"]		= $TS_Kraut_Envato_Item_Sales;
					$envato_array["price_cents"]			= $TS_Kraut_Envato_Item_Price;
					$envato_array["thumbnail_url"]			= $TS_Kraut_Envato_Item_Thumb;
					$envato_array["url"]					= $TS_Kraut_Envato_Item_Link;
					$envato_array["published_at"]			= $TS_Kraut_Envato_Item_Release;
					$envato_array["updated_at"]				= $TS_Kraut_Envato_Item_Update;
					// Create HTML Output					
					$this->envato_message .= '<div class="ts-envato-item-info-wrapper">';
						$this->envato_message .= '<div class="ts-envato-item-info-title">' . $TS_Kraut_Envato_Item_Name . '</div>';
						$this->envato_message .= '<div class="ts-envato-item-info-main">';
							$this->envato_message .= '<div class="ts-envato-item-info-top">';
								$this->envato_message .= '<div class="ts-envato-item-info-rating"><span class="ts-envato-item-info-desc">' . __( "Rating", "ts_visual_composer_extend" ) . '</span>' . $this->TS_TablesWP_ItemInfoRating(round($TS_Kraut_Envato_Item_Rating)) . '</div>';
							$this->envato_message .= '</div>';
							$this->envato_message .= '<div class="ts-envato-item-info-middle">';
								$this->envato_message .= '<div class="ts-envato-item-info-sales">';
									$this->envato_message .= '<span class="ts-envato-item-info-imgsales"></span>';
									$this->envato_message .= '<div class="ts-envato-item-info-text">';
										$this->envato_message .= '<span class="ts-envato-item-info-num">' . number_format(floatval($TS_Kraut_Envato_Item_Sales), 0) . '</span>';
										$this->envato_message .= '<span class="ts-envato-item-info-desc">' . __( "Sales", "ts_visual_composer_extend" ) . '</span>';
									$this->envato_message .= '</div>';
								$this->envato_message .= '</div>';
								$this->envato_message .= '<div class="ts-envato-item-info-thumb">';
									$this->envato_message .= '<img src="' . $TS_Kraut_Envato_Item_Thumb . '" alt="' . $TS_Kraut_Envato_Item_Name . '" width="80" height="80"/>';
								$this->envato_message .= '</div>';
								$this->envato_message .= '<div class="ts-envato-item-info-price">';
									$this->envato_message .= '<span class="ts-envato-item-info-imgprice"></span>';
									$this->envato_message .= '<div class="ts-envato-item-info-text">';
										$this->envato_message .= '<span class="ts-envato-item-info-num"><span>$</span>' . round($TS_Kraut_Envato_Item_Price / 100) . '</span>';
										$this->envato_message .= '<span class="ts-envato-item-info-desc">' . __( "Only", "ts_visual_composer_extend" ) . '</span>';
									$this->envato_message .= '</div>';
								$this->envato_message .= '</div>';
							$this->envato_message .= '</div>';
							$this->envato_message .= '<div class="ts-envato-item-info-bottom">';
								$this->envato_message .= '<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder" style="display: inline-block;">';
									$this->envato_message .= '<span class="ts-advanced-table-tooltip-content">' . __( "Click here to purchase a license for the plugin.", "ts_visual_composer_extend" ) . '</span>';
									$this->envato_message .= '<a href="' . $TS_Kraut_Envato_Item_Link . '" target="_blank" class="ts-advanced-tables-button-main ts-advanced-tables-button-orange ts-advanced-tables-button-money" style="min-width: 250px;">' . __( "Purchase", "ts_visual_composer_extend" ) . '</a>';
								$this->envato_message .= '</div>';
							$this->envato_message .= '</div>';
						$this->envato_message .= '</div>';
					$this->envato_message .= '</div>';
					// Store Item Data
					if (($this->envato_success) && ($this->envato_response != false)) {
						$TS_ADVANCED_TABLESWP->TS_TablesWP_Envato_Globals	= array(
							'last'										=> $TS_Kraut_Envato_Item_Check,
							'data'										=> $envato_array,
							'name'										=> $TS_Kraut_Envato_Item_Name,
							'info'										=> $this->envato_message,
							'link'										=> $TS_Kraut_Envato_Item_Link,
							'price'										=> $TS_Kraut_Envato_Item_Price,
							'sales'										=> $TS_Kraut_Envato_Item_Sales,
							'rating'									=> $this->TS_TablesWP_ItemInfoRating($TS_Kraut_Envato_Item_Rating),
							'votes'										=> $TS_Kraut_Envato_Item_Votes,
							'check'										=> $TS_Kraut_Envato_Item_Check,
							'migrate'									=> true,
						);
						update_option("ts_tablesplus_extend_settings_envato", $TS_ADVANCED_TABLESWP->TS_TablesWP_Envato_Globals);
					}
				}
			}
			
			// Create HTML Output for Rating Stars
			function TS_TablesWP_ItemInfoRating($envato_rating) {
				if ((int) $envato_rating == 0) {
					return '<div class="ts-envato-item-info-norating">' . __('Not Rated Yet.', 'ts_visual_composer_extend') . '</div>';
				}
				$envato_stars = '<ul class="ts-envato-item-info-stars">';
					$i									= 1;
					while ((--$envato_rating) >= 0) {
						$envato_stars .= '<li class="ts-envato-item-info-fullstar"></li>';
						$i++;
					}
					if ($envato_rating == -0.5) {
						$envato_stars .= '<li class="ts-envato-item-info-fullstar"></li>';
						$i++;
					}
					while ($i <= 5) {
						$envato_stars .= '<li class="ts-envato-item-info-emptystar"></li>';
						$i++;
					}
				$envato_stars .= '</ul>';
				return $envato_stars;
			}
			
			// Listing of Browser User Agents
			function TS_TablesWP_HelperRandomUserAgent() {
				$envato_agents 						= array(
					'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0',
					'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1',
					'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0',
					'Mozilla/5.0 (Windows NT 6.1; rv:27.3) Gecko/20130101 Firefox/27.3',
					'Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201',
					'Mozilla/5.0 (Windows; U; Windows NT 5.1; pl; rv:1.9.2.3) Gecko/20100401 Lightningquail/3.6.3',
					'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36',
					'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
					'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1944.0 Safari/537.36',				
					'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.1 Safari/537.36',
					'Opera/12.80 (Windows NT 5.1; U; en) Presto/2.10.289 Version/12.02',
					'Opera/9.80 (Windows NT 6.0) Presto/2.12.388 Version/12.14',
					'Opera/9.80 (X11; Linux i686; Ubuntu/14.10) Presto/2.12.388 Version/12.16',
					'Mozilla/5.0 (Windows NT 6.0; rv:2.0) Gecko/20100101 Firefox/4.0 Opera 12.14'
				);
				return $envato_agents[rand(0, count($envato_agents) - 1)];
			}
			
			// Get Server Protocoll of Site
			function TS_TablesWP_HelperSiteProtocol() {
				if (stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true) {
					return 'https';
				} else if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) {
					return 'https';
				} else {
					return 'http';
				}
			}
			
			// Convert CURL Error Code to String
			function TS_TablesWP_HelperErrorsCURL($envato_error) {
				$envato_errors = array (
					0 	=> 'CURLE_OK',
					1 	=> 'CURLE_UNSUPPORTED_PROTOCOL',
					2 	=> 'CURLE_FAILED_INIT',
					3 	=> 'CURLE_URL_MALFORMAT',
					4 	=> 'CURLE_NOT_BUILT_IN',
					5 	=> 'CURLE_COULDNT_RESOLVE_PROXY',
					6 	=> 'CURLE_COULDNT_RESOLVE_HOST',
					7 	=> 'CURLE_COULDNT_CONNECT',
					8 	=> 'CURLE_FTP_WEIRD_SERVER_REPLY',
					9 	=> 'CURLE_REMOTE_ACCESS_DENIED',
					10 	=> 'CURLE_FTP_ACCEPT_FAILED',
					11 	=> 'CURLE_FTP_WEIRD_PASS_REPLY',
					12 	=> 'CURLE_FTP_ACCEPT_TIMEOUT',
					13 	=> 'CURLE_FTP_WEIRD_PASV_REPLY',
					14 	=> 'CURLE_FTP_WEIRD_227_FORMAT',
					15 	=> 'CURLE_FTP_CANT_GET_HOST',
					17 	=> 'CURLE_FTP_COULDNT_SET_TYPE',
					18 	=> 'CURLE_PARTIAL_FILE',
					19 	=> 'CURLE_FTP_COULDNT_RETR_FILE',
					21 	=> 'CURLE_QUOTE_ERROR',
					22 	=> 'CURLE_HTTP_RETURNED_ERROR',
					23 	=> 'CURLE_WRITE_ERROR',
					25 	=> 'CURLE_UPLOAD_FAILED',
					26 	=> 'CURLE_READ_ERROR',
					27 	=> 'CURLE_OUT_OF_MEMORY',
					28 	=> 'CURLE_OPERATION_TIMEDOUT',
					30 	=> 'CURLE_FTP_PORT_FAILED',
					31 	=> 'CURLE_FTP_COULDNT_USE_REST',
					33 	=> 'CURLE_RANGE_ERROR',
					34 	=> 'CURLE_HTTP_POST_ERROR',
					35 	=> 'CURLE_SSL_CONNECT_ERROR',
					36 	=> 'CURLE_BAD_DOWNLOAD_RESUME',
					37 	=> 'CURLE_FILE_COULDNT_READ_FILE',
					38 	=> 'CURLE_LDAP_CANNOT_BIND',
					39 	=> 'CURLE_LDAP_SEARCH_FAILED',
					41 	=> 'CURLE_FUNCTION_NOT_FOUND',
					42 	=> 'CURLE_ABORTED_BY_CALLBACK',
					43 	=> 'CURLE_BAD_FUNCTION_ARGUMENT',
					45 	=> 'CURLE_INTERFACE_FAILED',
					47 	=> 'CURLE_TOO_MANY_REDIRECTS',
					48 	=> 'CURLE_UNKNOWN_OPTION',
					49 	=> 'CURLE_TELNET_OPTION_SYNTAX',
					51 	=> 'CURLE_PEER_FAILED_VERIFICATION',
					52 	=> 'CURLE_GOT_NOTHING',
					53 	=> 'CURLE_SSL_ENGINE_NOTFOUND',
					54 	=> 'CURLE_SSL_ENGINE_SETFAILED',
					55 	=> 'CURLE_SEND_ERROR',
					56 	=> 'CURLE_RECV_ERROR',
					58 	=> 'CURLE_SSL_CERTPROBLEM',
					59 	=> 'CURLE_SSL_CIPHER',
					60 	=> 'CURLE_SSL_CACERT',
					61 	=> 'CURLE_BAD_CONTENT_ENCODING',
					62 	=> 'CURLE_LDAP_INVALID_URL',
					63 	=> 'CURLE_FILESIZE_EXCEEDED',
					64 	=> 'CURLE_USE_SSL_FAILED',
					65 	=> 'CURLE_SEND_FAIL_REWIND',
					66 	=> 'CURLE_SSL_ENGINE_INITFAILED',
					67 	=> 'CURLE_LOGIN_DENIED',
					68 	=> 'CURLE_TFTP_NOTFOUND',
					69 	=> 'CURLE_TFTP_PERM',
					70 	=> 'CURLE_REMOTE_DISK_FULL',
					71 	=> 'CURLE_TFTP_ILLEGAL',
					72 	=> 'CURLE_TFTP_UNKNOWNID',
					73 	=> 'CURLE_REMOTE_FILE_EXISTS',
					74 	=> 'CURLE_TFTP_NOSUCHUSER',
					75 	=> 'CURLE_CONV_FAILED',
					76 	=> 'CURLE_CONV_REQD',
					77 	=> 'CURLE_SSL_CACERT_BADFILE',
					78 	=> 'CURLE_REMOTE_FILE_NOT_FOUND',
					79 	=> 'CURLE_SSH',
					80 	=> 'CURLE_SSL_SHUTDOWN_FAILED',
					81 	=> 'CURLE_AGAIN',
					82 	=> 'CURLE_SSL_CRL_BADFILE',
					83 	=> 'CURLE_SSL_ISSUER_ERROR',
					84 	=> 'CURLE_FTP_PRET_FAILED',
					85 	=> 'CURLE_RTSP_CSEQ_ERROR',
					86 	=> 'CURLE_RTSP_SESSION_ERROR',
					87 	=> 'CURLE_FTP_BAD_FILE_LIST',
					88 	=> 'CURLE_CHUNK_FAILED',
					89 	=> 'CURLE_NO_CONNECTION_AVAILABLE'
				);
				return $envato_errors[$envato_error];
			}
		}
	}
	
	
	// Initialize Envato API Class
	// ---------------------------
	$TS_TablesWP_EnvatoClassInit							= new TS_TablesWP_EnvatoAPIRoutines("18560899", "QnFOMlI0b3NOU1ZiTTBmS3VOTjNBbFJHdk53c3Q2RUY=");
	
	
	// Save / Load Parameters
	// ----------------------
	if (isset($_POST['License'])) {
		// Load Preloader
		echo '<div id="ts_tablesplus_extend_settings_save" style="position: relative; margin: 20px auto 20px auto; width: 128px; height: 128px;">';
			echo TS_TablesWP_CreatePreloaderCSS("ts-settings-panel-loader", "", 4, "false");
		echo '</div>';
		// Init New license Routine
		$TS_TablesWP_EnvatoClassInit->TS_TablesWP_LicenseCodeInit("newlicense", 	trim($_POST['ts_tablesplus_extend_settings_license']));
		// Reload License Page
		echo '<script> window.location="' . $_SERVER['REQUEST_URI'] . '"; </script> ';
		//Header('Location: '.$_SERVER['REQUEST_URI']);
		Exit();
	} else if (isset($_POST['Unlicense'])) {
		// Load Preloader
		echo '<div id="ts_tablesplus_extend_settings_save" style="position: relative; margin: 20px auto 20px auto; width: 128px; height: 128px;">';
			echo TS_TablesWP_CreatePreloaderCSS("ts-settings-panel-loader", "", 4, "false");
		echo '</div>';
		// Init Unlicense Routine
		$TS_TablesWP_EnvatoClassInit->TS_TablesWP_LicenseCodeInit("unlicense", "");
		// Reload License Page
		echo '<script> window.location="' . $_SERVER['REQUEST_URI'] . '"; </script> ';
		//Header('Location: '.$_SERVER['REQUEST_URI']);
		Exit();
	} else {
		// Check For Pending Processes
		if ($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['keyed'] == 'unlicenseinprogress') {
			$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['keyed']	= "emptydelimiterfix";
			$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['remove']	= true;			
			if ($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginMultiSite == true) {
				update_site_option('ts_tablesplus_extend_settings_licenseData',	$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent);
			} else {
				update_option('ts_tablesplus_extend_settings_licenseData',		$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent);
			}
		}		
		// Process License Data
		if ($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['update']) {
			if ($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['remove'] == false) {
				$TS_TablesWP_EnvatoClassInit->TS_TablesWP_LicenseCodeInit("checklicense", "");
			}
			echo "\n";
			echo "<script type='text/javascript'>" . "\n";
			echo "SettingsLicenseUpdate = true;" . "\n";
			if ($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['remove']) {
				echo "SettingsUnLicensing = true;" . "\n";
				echo 'Settings_LicenseStatus = false;' . "\n";
				echo "SettingsLicenseKey = false;" . "\n";
			} else {
				echo "SettingsUnLicensing = false;" . "\n";
				if ($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['valid']) {
					echo 'Settings_LicenseStatus = true;' . "\n";
				} else {
					echo 'Settings_LicenseStatus = false;' . "\n";
				}
				if (strlen($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['data']) != 0) {
					echo "SettingsLicenseKey = true;" . "\n";
				} else {
					echo "SettingsLicenseKey = false;" . "\n";
				}
			}
			echo "</script>" . "\n";			
			if ($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['remove']) {
				$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent			= $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseBlank;
				if ($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginMultiSite == true) {
					update_site_option('ts_tablesplus_extend_settings_licenseData',	$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent);
				} else {
					update_option('ts_tablesplus_extend_settings_licenseData',		$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent);
				}
			}
		} else {
			$TS_TablesWP_EnvatoClassInit->TS_TablesWP_LicenseCodeInit("getlicense", "");
			echo "\n";
			echo "<script type='text/javascript'>" . "\n";
			echo "SettingsLicenseUpdate = false;" . "\n";
			if ($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['valid']) {
				echo 'Settings_LicenseStatus = true;' . "\n";
			} else {
				echo 'Settings_LicenseStatus = false;' . "\n";
			}
			if (strlen($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['data']) != 0) {
				echo "SettingsLicenseKey = true;" . "\n";
			} else {
				echo "SettingsLicenseKey = false;" . "\n";
			}
			echo "SettingsUnLicensing = false;" . "\n";
			echo "</script>" . "\n";
		}
		// Other Followup Routines
		$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['update'] 		= false;
		if ($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginMultiSite == true) {
			update_site_option('ts_tablesplus_extend_settings_licenseData',		$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent);
		} else {
			update_option('ts_tablesplus_extend_settings_licenseData',			$TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent);
		}
		$LicenseCheckStatus 													= "";
	}
	echo '<div class="ts-vcsc-settings-group-header">';
		echo '<div class="display_header">';
			echo '<h2><span class="dashicons dashicons-admin-network"></span>' . __("Tablenator - Advanced Tables for WordPress", "ts_visual_composer_extend") . ' v' . TS_TablesWP_GetPluginVersion() . ' ... ' . __("License Information", "ts_visual_composer_extend") . '</h2>';
		echo '</div>';
		echo '<div class="clear"></div>';
	echo '</div>';
?>
<form id="ts-advanced-table-license-form" class="ts-advanced-table-license-form" name="oscimp_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<div class="ts-advanced-table-license-strings" style="display: none;">
		<div id="ts-advanced-table-license-confirm"><?php echo __('Your Envato license key has been confirmed! Thank you for your purchase!', 'ts_visual_composer_extend'); ?></div>
		<div id="ts-advanced-table-license-unlicense"><?php echo __('"Tablenator - Advanced Tables for WordPress" has been unlicensed for this site and the auto-update feature has been removed accordingly!', 'ts_visual_composer_extend'); ?></div>
		<div id="ts-advanced-table-license-invalid"><?php echo __('Problem: Your Envato license key could not be confirmed!', 'ts_visual_composer_extend'); ?></div>
		<div id="ts-advanced-table-license-missing"><?php echo __('Problem: You did not provide a license key to check!', 'ts_visual_composer_extend'); ?></div>
		<div id="ts-advanced-table-license-close"><?php echo __('Close', 'ts_visual_composer_extend'); ?></div>
	</div>
	<div class="ts-advanced-table-license-info">
		<div class="ts-settings-statistics-links" style="border-bottom: 1px dashed #cccccc; display: block; width: 100%; margin: 5px 0 20px;">
			<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
				<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to return to the table listings page.", "ts_visual_composer_extend"); ?></span>
				<a href="<?php echo admin_url('admin.php?page=TS_TablesWP_Tables'); ?>" target="_parent" class="ts-advanced-tables-button-main ts-advanced-tables-button-turquoise ts-advanced-tables-button-listing">
					<?php echo __("Back to Listing", "ts_visual_composer_extend"); ?>
				</a>
			</div>
			<?php
				if (current_user_can('manage_options')) {
					echo '<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
						<span class="ts-advanced-table-tooltip-content">' . __("Click here to return to the plugins settings page.", "ts_visual_composer_extend") . '</span>
						<a href="' . admin_url('admin.php?page=TS_TablesWP_Settings') . '" target="_parent" class="ts-advanced-tables-button-main ts-advanced-tables-button-grey ts-advanced-tables-button-settings">'. __("Back to Settings", "ts_visual_composer_extend") . '</a>
					</div>';
				}
			?>
		</div>
		<?php
			if ($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginMultiSite == true) {
				if ($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginValid == false) {
					echo '<div class="ts-vcsc-info-field ts-vcsc-warning" style="margin-top: 10px; font-size: 13px; text-align: justify;">' . __("Please enter your license key in order to activate the auto-update feature of the plugin!", "ts_visual_composer_extend") . '</div>';
				}
			} else {
				if ($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginValid == false) {
					echo '<div class="ts-vcsc-info-field ts-vcsc-warning" style="margin-top: 10px; font-size: 13px; text-align: justify;">' . __("Please enter your license key in order to activate the auto-update feature of the plugin!", "ts_visual_composer_extend") . '</div>';
				}
			}
		?>			
		<?php
			if ($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginMultiSite == true) {
				$envato_code = get_site_option('ts_tablesplus_extend_settings_license', '');
				echo '<div class="ts-vcsc-info-field ts-vcsc-critical" style="margin-top: 10px; font-size: 13px; text-align: justify; font-weight: bold;">';
					echo __("This plugin has been activated network-wide in a WordPress MultiSite environment. Please consider purchasing additional licenses for the plugin as Envato license rules restrict usage to one domain only! Thank you!", "ts_visual_composer_extend");
				echo '</div>';
			} else {
				$envato_code = get_option('ts_tablesplus_extend_settings_license', '');
			}
			if (in_array(base64_encode($envato_code), $TS_ADVANCED_TABLESWP->TS_TablesWP_API_Duplications)) {
				echo '<div class="ts-vcsc-info-field ts-vcsc-critical" style="margin-top: 10px; font-size: 13px; text-align: justify; font-weight: bold;">';
					echo __("The license key you are attempting to use has been revoked by Envato due to the fact that the buyer received a full refund of the purchase price. Continued usage of the product is illegal!", "ts_visual_composer_extend");
				echo '</div>';
			}
		?>
	</div>
	<?php
		if (!function_exists('curl_init')) {
			echo '<div class="ts-advanced-table-license-curl">';
				echo '<div class="ts-vcsc-info-field ts-vcsc-critical" style="margin-top: 10px; font-size: 13px; text-align: justify;">';
					echo __("In order to check your license key and to retrieve any information from Envato, this plugin requires cURL to be enabled on and support by your server, which does not seem to be the case. Without cURL support, you will not be able to confirm your license key. Please check your server settings and/or contact your hosting service in order to enable and use cURL.", "ts_visual_composer_extend");
				echo '</div>';
			echo '</div>';
		}
	?>
	<div class="ts-advanced-table-license-help" style="width: 100%; margin-top: 30px;">
		<div class="ts-vcsc-section-main">
			<div class="ts-vcsc-section-title ts-vcsc-section-hide"><i class="ts-tableeditor-search3"></i><?php echo __("How To Find Your License Key", "ts_visual_composer_extend"); ?></div>
			<div class="ts-vcsc-section-content slideFade" style="display: none;">
				<iframe class="wistia_embed" src="//fast.wistia.net/embed/iframe/gqsrs2assi" name="wistia_embed" width="640" height="400" frameborder="0" scrolling="no" allowfullscreen="allowfullscreen" mozallowfullscreen="mozallowfullscreen" webkitallowfullscreen="webkitallowfullscreen" oallowfullscreen="oallowfullscreen" msallowfullscreen="msallowfullscreen"></iframe>
			</div>
		</div>
	</div>
	<div class="ts-advanced-table-license-content" style="min-height: 100px; width: 100%; margin-top: 20px;">
		<table style="border: 1px solid #ededed; min-height: 100px; width: 100%;">
			<tr>
				<td style="width: 250px; padding: 0px 20px 0px 20px; border-right: 1px solid #ededed;">
					<?php echo $TS_TablesWP_EnvatoClassInit->TS_TablesWP_ItemInfoInit(); ?>
				</td>
				<td>
					<div>
						<h4 style="margin-top: 20px;"><span style="margin-left: 10px;"><?php echo __("Envato License Key Check:", "ts_visual_composer_extend"); ?></span></h4>
						<p style="margin-top: 5px; margin-left: 10px; margin-bottom: 15px;"><?php echo __("Please enter your Envato license key in the input provided below:", "ts_visual_composer_extend"); ?></p>
						<?php echo $TS_TablesWP_EnvatoClassInit->envato_status; ?>
						<label style="margin-left: 10px;" class="Uniform" for="ts_tablesplus_extend_settings_license"><?php echo __("Envato License Key:", "ts_visual_composer_extend"); ?></label>
						<input class="<?php
							if ($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['valid'] == false) {
								echo "Required";
							}
						?>" type="input" style="width: 20%; height: 30px; margin: 0 10px;" id="ts_tablesplus_extend_settings_license" name="ts_tablesplus_extend_settings_license" value="<?php echo $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['data']; ?>" size="100">
						<?php
							if (strlen($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['data']) != 0) {
								echo $TS_ADVANCED_TABLESWP->TS_TablesWP_PluginLicenseCurrent['info'];
							} else {
								echo '<br/><br/><span id="ts-license-check-missing">' . __("Please enter a valid license key!", "ts_visual_composer_extend") . '</span>';
							}
						?>
						<div style="height: 20px; display: block;"></div>
					</div>
				</td>
			</tr>
		</table>
		<?php
			if ($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginMultiSite == true) {
				echo '<div id="ts-settings-summary" style="display: none;" data-extended="' . get_site_option('ts_tablesplus_extend_settings_extended', 0) . '" data-summary="' . get_site_option('ts_tablesplus_extend_settings_licenseKeyed', 'emptydelimiterfix') . '">' . get_site_option('ts_tablesplus_extend_settings_licenseInfo', '') . '</div>';
			} else {
				echo '<div id="ts-settings-summary" style="display: none;" data-extended="' . get_option('ts_tablesplus_extend_settings_extended', 0) . '" data-summary="' . get_option('ts_tablesplus_extend_settings_licenseKeyed', 'emptydelimiterfix') . '">' . get_option('ts_tablesplus_extend_settings_licenseInfo', '') . '</div>';
			}
		?>
	</div>
	<div class="ts-advanced-table-license-controls" style="width: 100%; margin-top: 20px;">		
		<?php		
			if ($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginMultiSite == true) {
				if ($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginValid == true) {
					echo '<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
                        <span class="ts-advanced-table-tooltip-content">' . __("Click here to check your license for Tablenator - Advanced Tables for WordPress.", "ts_visual_composer_extend") . '</span>
                        <button type="submit" name="License" class="ts-advanced-tables-button-main ts-advanced-tables-button-blue ts-advanced-tables-button-unlock" style="margin: 0;">' . __("Re-Check License", "ts_visual_composer_extend") . '</button>
                    </div>';
					echo '<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder ts-advanced-table-tooltip-right" style="float: right;">
                        <span class="ts-advanced-table-tooltip-content">' . __("Click here to unlicense this installation of Tablenator - Advanced Tables for WordPress.", "ts_visual_composer_extend") . '</span>
                        <button type="submit" name="Unlicense" class="ts-advanced-tables-button-main ts-advanced-tables-button-red ts-advanced-tables-button-lock" style="margin: 0;">' . __("Unlicense Plugin", "ts_visual_composer_extend") . '</button>
                    </div>';
				} else {
					echo '<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
                        <span class="ts-advanced-table-tooltip-content">' . __("Click here to check your license for Tablenator - Advanced Tables for WordPress.", "ts_visual_composer_extend") . '</span>
                        <button type="submit" name="License" class="ts-advanced-tables-button-main ts-advanced-tables-button-blue ts-advanced-tables-button-unlock" style="margin: 0;">' . __("Check License", "ts_visual_composer_extend") . '</button>
                    </div>';
				}
			} else {
				if ($TS_ADVANCED_TABLESWP->TS_TablesWP_PluginValid == true) {
					echo '<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
                        <span class="ts-advanced-table-tooltip-content">' . __("Click here to check your license for Tablenator - Advanced Tables for WordPress.", "ts_visual_composer_extend") . '</span>
                        <button type="submit" name="License" class="ts-advanced-tables-button-main ts-advanced-tables-button-blue ts-advanced-tables-button-unlock" style="margin: 0;">' . __("Re-Check License", "ts_visual_composer_extend") . '</button>
                    </div>';
					echo '<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder ts-advanced-table-tooltip-right" style="float: right;">
                        <span class="ts-advanced-table-tooltip-content">' . __("Click here to unlicense this installation of Tablenator - Advanced Tables for WordPress.", "ts_visual_composer_extend") . '</span>
                        <button type="submit" name="Unlicense" class="ts-advanced-tables-button-main ts-advanced-tables-button-red ts-advanced-tables-button-lock" style="margin: 0;">' . __("Unlicense Plugin", "ts_visual_composer_extend") . '</button>
                    </div>';
				} else {
					echo '<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
                        <span class="ts-advanced-table-tooltip-content">' . __("Click here to check your license for Tablenator - Advanced Tables for WordPress.", "ts_visual_composer_extend") . '</span>
                        <button type="submit" name="License" class="ts-advanced-tables-button-main ts-advanced-tables-button-blue ts-advanced-tables-button-unlock" style="margin: 0;">' . __("Check License", "ts_visual_composer_extend") . '</button>
                    </div>';
				}
			}
		?>
	</div>
</form>
