<?php
	// Function for Full Variable Output With Highlighting
	// ---------------------------------------------------
	if (!function_exists('TS_TablesWP_HighlightText')) {
		function TS_TablesWP_HighlightText($text, $export = true) {
			if (($export == true) || ($export == 'true')) {
				$text 						= var_export($text, true);
			}
			$text 							= trim($text);
			$text 							= highlight_string("<?php " . $text, true);  // highlight_string() requires opening PHP tag or otherwise it will not colorize the text
			$text 							= trim($text);
			$text 							= preg_replace("|^\\<code\\>\\<span style\\=\"color\\: #[a-fA-F0-9]{0,6}\"\\>|", "", $text, 1);  // remove prefix
			$text 							= preg_replace("|\\</code\\>\$|", "", $text, 1);  // remove suffix 1
			$text 							= trim($text);  // remove line breaks
			$text 							= preg_replace("|\\</span\\>\$|", "", $text, 1);  // remove suffix 2
			$text 							= trim($text);  // remove line breaks
			$text 							= preg_replace("|^(\\<span style\\=\"color\\: #[a-fA-F0-9]{0,6}\"\\>)(&lt;\\?php&nbsp;)(.*?)(\\</span\\>)|", "\$1\$3\$4", $text);  // remove custom added "<?php "
			return $text;
		}
	}

	// Functions to Retrieve Before/After Strings
	// ------------------------------------------
	if (!function_exists('TS_TablesWP_GetStringBefore')){
		function TS_TablesWP_GetStringBefore($string, $substring) {
			$pos = strpos($string, $substring);
			if ($pos === false) {
				return $string;
			} else {
				return(substr($string, 0, $pos));
			}
		}
	}
	if (!function_exists('TS_TablesWP_GetStringAfter')){
		function TS_TablesWP_GetStringAfter($string, $substring) {
			$pos = strpos($string, $substring);
			if ($pos === false) {
				return $string;
			} else {
				return(substr($string, $pos+strlen($substring)));
			}
		}
	}
	
	// Functions to Flatten Objects/Arrays
	// -----------------------------------
	if (!function_exists('TS_TablesWP_FlattenRecursive')){
		function TS_TablesWP_FlattenRecursive($array, &$flattened, &$level) {
			foreach ($array as $key => $member) {
				$insert 					= $member;
				$children 					= null;
				if ((isset($insert->children)) && (is_array($insert->children))) {
					$children 				= $insert->children;
					$insert->children		= true;
				} else {
					$insert->children		= false;
				}
				$insert->level 				= $level;
				$flattened[] 				= $insert;
				if ($children !== null) {
					$level++;
					TS_TablesWP_FlattenRecursive($children, $flattened, $level);
					$level--;
				}
			}
		}
	}
	if (!function_exists('TS_TablesWP_FlattenObject')){
		function TS_TablesWP_FlattenObject($array) {
			$flattened 						= [];
			$level 							= 0;
			TS_TablesWP_FlattenRecursive($array, $flattened, $level);
			return $flattened;
		}
	}

	// Function to Retrieve Image MetaData
	// -----------------------------------
	if (!function_exists('TS_TablesWP_GetImageMetaData')){
		function TS_TablesWP_GetImageMetaData($img_id) {
			$image_array					= array();
			if ($img_id != "") {
				$image_data 				= get_post($img_id);
				$image_array['alt']			= get_post_meta($img_id, '_wp_attachment_image_alt', true);
				$image_array['caption']		= $image_data->post_excerpt;
				$image_array['content']		= $image_data->post_content;
				$image_array['title']		= $image_data->post_title;
				$image_array['type']		= $image_data->post_mime_type;
			}
			return $image_array;
		}
	}
	
	// Function to Parse File Paths
	// ----------------------------
	if (!function_exists('TS_TablesWP_GetPathInfo')){
		function TS_TablesWP_GetPathInfo($filepath) {
			preg_match('%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im', $filepath, $m);
			if ($m[1]) $ret['dirname']		= $m[1];
			if ($m[2]) $ret['basename']		= $m[2];
			if ($m[5]) $ret['extension']	= $m[5];
			if ($m[3]) $ret['filename']		= $m[3];
			return $ret;
		}
	}

	// Function to Compare + Check Version Numbers
	// -------------------------------------------
	if (!function_exists('TS_TABLESWP_VERSIONCompare')){
		function TS_TABLESWP_VERSIONCompare($a, $b) {
			//Compare two sets of versions, where major/minor/etc. releases are separated by dots. 
			//Returns 0 if both are equal, 1 if A > B, and -1 if B < A.
			$a = trim($a);
			$b = trim($b);
			$a = preg_replace("/[^0-9.]/", "", $a);
			$b = preg_replace("/[^0-9.]/", "", $b);
			$a = explode(".", TS_TablesWP_CustomSTRrTrim($a, ".0")); //Split version into pieces and remove trailing .0 
			$b = explode(".", TS_TablesWP_CustomSTRrTrim($b, ".0")); //Split version into pieces and remove trailing .0 
			//Iterate over each piece of A 
			foreach ($a as $depth => $aVal) {
				if (isset($b[$depth])) {
				//If B matches A to this depth, compare the values 
					if ($aVal > $b[$depth]) {
						return 1; //Return A > B
						//break;
					} else if ($aVal < $b[$depth]) {
						return -1; //Return B > A
						//break;
					}
				//An equal result is inconclusive at this point 
				} else  {
					//If B does not match A to this depth, then A comes after B in sort order 
					return 1; //so return A > B
					//break;
				} 
			} 
			//At this point, we know that to the depth that A and B extend to, they are equivalent. 
			//Either the loop ended because A is shorter than B, or both are equal. 
			return (count($a) < count($b)) ? -1 : 0; 
		}
	}
	
	// Function to Trim trailing .0 from Version Numbers
	// -------------------------------------------------
	if (!function_exists('TS_TablesWP_CustomSTRrTrim')){
		function TS_TablesWP_CustomSTRrTrim($message, $strip) {
			$lines = explode($strip, $message); 
			$last  = ''; 
			do { 
				$last = array_pop($lines); 
			} while (empty($last) && (count($lines)));
			return implode($strip, array_merge($lines, array($last))); 
		}
	}
	
	// Function to Compare + Check WordPress Versions
	// ----------------------------------------------
	if (!function_exists('TS_TablesWP_WordPressCheckup')) {
		function TS_TablesWP_WordPressCheckup($version = '3.8') {
			global $wp_version;		
			if (version_compare($wp_version, $version, '>=')) {
				return "true";
			} else {
				return "false";
			}
		}
	}
	
	// Function to Check if Currently Editing Page + Post
	// --------------------------------------------------
	if (!function_exists('TS_TablesWP_IsEditPagePost')){
		function TS_TablesWP_IsEditPagePost($new_edit = null){
			global $pagenow, $typenow;
			$frontend = TS_TablesWP_CheckFrontEndEditor();
			if (function_exists('vc_is_inline')){
				$vc_is_inline = vc_is_inline();
				if ((vc_is_inline() == false) && (vc_is_inline() != '') && (vc_is_inline() != true) && (!is_admin())) {
					return false;
				} else if ((vc_is_inline() == true) && (vc_is_inline() != '') && (vc_is_inline() != true) && (!is_admin())) {
					return true;
				} else if (((vc_is_inline() == NULL) || (vc_is_inline() == '')) && (!is_admin())) {
					if ($frontend == true) {
						$vc_is_inline = true;
						return true;
					} else {
						$vc_is_inline = false;
						return false;
					}
				}
			} else {
				$vc_is_inline = false;
				if (!is_admin()) return false;
			}
			if (($frontend == true) && (!is_admin())) {
				return true;
			} else if ($new_edit == "edit") {
				return in_array($pagenow, array('post.php'));
			} else if ($new_edit == "new") {
				return in_array($pagenow, array('post-new.php'));
			} else if ($vc_is_inline == true) {
				return true;
			} else {
				return in_array($pagenow, array('post.php', 'post-new.php'));
			}
		}
	}
	
	// Function to Check for VC Frontend Editor
	// ----------------------------------------
	if (!function_exists('TS_TablesWP_CheckFrontEndEditor')){
		function TS_TablesWP_CheckFrontEndEditor() {
			$url 		= 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
			if ((strpos($url, "vc_editable=true") !== false) || (strpos($url, "vc_action=vc_inline") !== false)) {
				return true;
			} else {
				return false;
			}
		}
	}
	
    // Function to check Current User Role
    // -----------------------------------
    if (!function_exists('TS_TablesWP_CheckUserRole')){
        function TS_TablesWP_CheckUserRole($roles, $user_id = NULL) {
            // Get user by ID, else get current user
            if ($user_id) {
                $user = get_userdata($user_id);
            } else {
                $user = wp_get_current_user();
            }
            // No user found, return
            if (empty($user)) {
                return false;
            }
            // Append administrator to roles, if necessary
            if (!in_array('administrator', $roles)) {
                $roles[] = 'administrator';
            }
            // Loop through user roles
            foreach ($user->roles as $role) {
                // Does user have role
                if (in_array($role, $roles)) {
                    return true;
                }
            }
            // User not in roles
            return false;
        }
    }
	if (!function_exists('TS_TablesWP_CheckCurrentUserRoles')){
		function TS_TablesWP_CheckCurrentUserRoles($role, $user_id = null) {
			if (is_numeric($user_id)) {
				$user = get_userdata($user_id);
			} else {
				$user = wp_get_current_user();
			}			 
			if (empty($user)) {
				return false;
			}
			return in_array($role, (array) $user->roles);
		}
	}
		
	// Function to Retrieve Categories for Custom Post
	// -----------------------------------------------
	if (!function_exists('TS_TablesWP_GetCategoriesCustomPost')){
		function TS_TablesWP_GetCategoriesCustomPost($id = false, $tcat = 'category') {
			$categories = get_the_terms( $id, $tcat );
			if (!$categories) {
				$categories = array();
			}	
			$categories = array_values( $categories );	
			foreach ( array_keys( $categories ) as $key ) {
				_make_cat_compat( $categories[$key] );
			}	
			return apply_filters('get_the_categories', $categories);
		}
	}
	
    // Function to retrieve Attachment ID from Link
    // --------------------------------------------
    if (!function_exists('TS_TablesWP_GetAttachmentIDFromLink')){
        function TS_TablesWP_GetAttachmentIDFromLink ($image_src) {
	    global $wpdb;
	    $query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$image_src'";
	    $id = $wpdb->get_var($query);
	    return $id;
        }
    }
	
    // Function to extract String in Between Strings
    // ---------------------------------------------
    if (!function_exists('TS_TablesWP_GetStringBetween')){
        function TS_TablesWP_GetStringBetween ($string, $start, $finish) {
            $string = " " . $string;
            $position = strpos($string, $start);
            if ($position == 0) return "";
            $position += strlen($start);
            $length = strpos($string, $finish, $position) - $position;
            return substr($string, $position, $length);
        }
    }
	if (!function_exists('TS_TablesWP_GetContentsBetween')){
		function TS_TablesWP_GetContentsBetween($str, $startDelimiter, $endDelimiter) {
			$contents = array();
			$startDelimiterLength = strlen($startDelimiter);
			$endDelimiterLength = strlen($endDelimiter);
			$startFrom = $contentStart = $contentEnd = 0;
			while (false !== ($contentStart = strpos($str, $startDelimiter, $startFrom))) {
			  $contentStart += $startDelimiterLength;
			  $contentEnd = strpos($str, $endDelimiter, $contentStart);
			  if (false === $contentEnd) {
				break;
			  }
			  $contents[] = substr($str, $contentStart, $contentEnd - $contentStart);
			  $startFrom = $contentEnd + $endDelimiterLength;
			}  
			return $contents;
		}
	}
    
    // Function to retrieve Current Post Type
    // --------------------------------------
    if (!function_exists('TS_TablesWP_GetCurrentPostType')){
        function TS_TablesWP_GetCurrentPostType() {
            global $post, $typenow, $current_screen;
            if ($post && $post->post_type) {
                // We have a post so we can just get the post type from that
                return $post->post_type;		
            } else if ($typenow) {
                // Check the global $typenow
                return $typenow;
            } else if ($current_screen && $current_screen->post_type) {
                // Check the global $current_screen Object
                return $current_screen->post_type;	
            } else if (isset($_REQUEST['post_type'])) {
                // Check the Post Type QueryString
                return sanitize_key($_REQUEST['post_type']);
			} else if (empty($typenow) && !empty($_GET['post'])) {
				// Try to get via get_post(); Attempt A
				$post 		= get_post($_GET['post']);
				$typenow 	= $post->post_type;
				return $typenow;
			} else if (empty($typenow) && !empty($_POST['post_ID'])) {
				// Try to get via get_post(); Attempt B
				$post 		= get_post($_POST['post_ID']);
				$typenow 	= $post->post_type;
				return $typenow;
			} else if (function_exists('get_current_screen')) {
				// Try to get via get_current_screen()
				$current 	= get_current_screen();
				if (isset($current) && ($current != false) && ($current->post_type)) {
					return $current->post_type;
				} else {
					return null;
				}
			}
            // We Do Not Know The Post Type!!!
            return null;
        }
    }
    
	// Function to Minify HTML/JS/CSS Code
	// ------------------------------------
	if (!function_exists('TS_TablesWP_MinifyHTML')) {
		function TS_TablesWP_MinifyHTML($input) {
			if (trim($input) === "") return $input;
			// Remove extra white-space(s) between HTML Attribute(s)
			/*$input = preg_replace_callback('#<([^\/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(\/?)>#s', function($matches) {
				return '<' . $matches[1] . preg_replace('#([^\s=]+)(\=([\'"]?)(.*?)\3)?(\s+|$)#s', ' $1$2', $matches[2]) . $matches[3] . '>';
			}, str_replace("\r", "", $input));*/		
			$input = preg_replace_callback('#<([^\/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(\/?)>#s', 'TS_TablesWP_CleanUpHTML', str_replace("\r", "", $input));	
			// Minify Inline CSS Declaration(s)
			if(strpos($input, ' style=') !== false) {
				/*$input = preg_replace_callback('#<([^<]+?)\s+style=([\'"])(.*?)\2(?=[\/\s>])#s', function($matches) {
					return '<' . $matches[1] . ' style=' . $matches[2] . TS_TablesWP_MinifyCSS($matches[3]) . $matches[2];
				}, $input);*/
				$input = preg_replace_callback('#<([^<]+?)\s+style=([\'"])(.*?)\2(?=[\/\s>])#s', 'TS_TablesWP_CleanUpSTYLE', $input);
			}
			return preg_replace(
				array(
					// t = text
					// o = tag open
					// c = tag close
					// Keep important white-space(s) after self-closing HTML tag(s)
					'#<(img|input)(>| .*?>)#s',
					// Remove a line break and two or more white-space(s) between tag(s)
					'#(<!--.*?-->)|(>)(?:\n*|\s{2,})(<)|^\s*|\s*$#s',
					'#(<!--.*?-->)|(?<!\>)\s+(<\/.*?>)|(<[^\/]*?>)\s+(?!\<)#s', // t+c || o+t
					'#(<!--.*?-->)|(<[^\/]*?>)\s+(<[^\/]*?>)|(<\/.*?>)\s+(<\/.*?>)#s', // o+o || c+c
					'#(<!--.*?-->)|(<\/.*?>)\s+(\s)(?!\<)|(?<!\>)\s+(\s)(<[^\/]*?\/?>)|(<[^\/]*?\/?>)\s+(\s)(?!\<)#s', // c+t || t+o || o+t -- separated by long white-space(s)
					'#(<!--.*?-->)|(<[^\/]*?>)\s+(<\/.*?>)#s', // empty tag
					'#<(img|input)(>| .*?>)<\/\1>#s', // reset previous fix
					'#(&nbsp;)&nbsp;(?![<\s])#', // clean up ...
					'#(?<=\>)(&nbsp;)(?=\<)#', // --ibid
					// Remove HTML comment(s) except IE comment(s)
					'#\s*<!--(?!\[if\s).*?-->\s*|(?<!\>)\n+(?=\<[^!])#s'
				),
				array(
					'<$1$2</$1>',
					'$1$2$3',
					'$1$2$3',
					'$1$2$3$4$5',
					'$1$2$3$4$5$6$7',
					'$1$2$3',
					'<$1$2',
					'$1 ',
					'$1',
					""
				),
			$input);
		}
	}
	if (!function_exists('TS_TablesWP_CleanUpHTML')) {
		function TS_TablesWP_CleanUpHTML($matches) {
			return '<' . $matches[1] . preg_replace('#([^\s=]+)(\=([\'"]?)(.*?)\3)?(\s+|$)#s', ' $1$2', $matches[2]) . $matches[3] . '>';
		}
	}
	if (!function_exists('TS_TablesWP_CleanUpSTYLE')) {
		function TS_TablesWP_CleanUpSTYLE($matches) {
			return '<' . $matches[1] . ' style=' . $matches[2] . TS_TablesWP_MinifyCSS($matches[3]) . $matches[2];
		}
	}
	if (!function_exists('TS_TablesWP_MinifyCSS')) {
		// Based On: https://gist.github.com/tovic/d7b310dea3b33e4732c0
		function TS_TablesWP_MinifyCSS($input) {
			if (trim($input) === "") return $input;
			return preg_replace(
				array(
					// Remove comment(s)
					'#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
					// Remove unused white-space(s)
					'#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~+]|\s*+-(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
					// Replace '0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)' with '0'
					'#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
					// Replace ':0 0 0 0' with ':0'
					'#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
					// Replace 'background-position:0' with 'background-position:0 0'
					'#(background-position):0(?=[;\}])#si',
					// Replace '0.6' with '.6', but only when preceded by ':', ',', '-' or a white-space
					'#(?<=[\s:,\-])0+\.(\d+)#s',
					// Minify string value
					'#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][a-z0-9\-_]*?)\2(?=[\s\{\}\];,])#si',
					'#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
					// Minify HEX color code
					'#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
					// Replace '(border|outline):none' with '(border|outline):0'
					'#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
					// Remove empty selector(s)
					'#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s'
				),
				array(
					'$1',
					'$1$2$3$4$5$6$7',
					'$1',
					':0',
					'$1:0 0',
					'.$1',
					'$1$3',
					'$1$2$4$5',
					'$1$2$3',
					'$1:0',
					'$1$2'
				),
			$input);
		}
	}
	if (!function_exists('TS_TablesWP_MinifyJS')) {
		function TS_TablesWP_MinifyJS($input) {
			if (trim($input) === "") return $input;
			return preg_replace(
				array(
					// Remove comment(s)
					'#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
					// Remove white-space(s) outside the string and regex
					'#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
					// Remove the last semicolon
					'#;+\}#',
					// Minify object attribute(s) except JSON attribute(s). From '{'foo':'bar'}' to '{foo:'bar'}'
					'#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i',
					// --ibid. From 'foo['bar']' to 'foo.bar'
					'#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i'
				),
				array(
					'$1',
					'$1$2',
					'}',
					'$1$3',
					'$1.$3'
				),
			$input);
		}
	}
    	
	// Other Utilized Functions
    // ------------------------
	if (!function_exists('TS_TablesWP_RemoveEmptyParagraphs')){
		function TS_TablesWP_RemoveEmptyParagraphs($content) {
			$content 						= force_balance_tags($content);
			$content 						= preg_replace('#<p>\s*+(<br\s*/*>)?\s*</p>#i', '', $content);
			$content 						= preg_replace('~\s?<p>(\s|&nbsp;)+</p>\s?~', '', $content);
			return $content;
		}
	}
	if (!function_exists('TS_TablesWP_TrueFalseEqualizer')){
		function TS_TablesWP_TrueFalseEqualizer($value, $default) {
			if (isset($value)) {
				if (($value === "false") || ($value === false) || ($value === "0") || ($value === 0)) {
					return "false";
				} else {
					return "true";
				}
			} else {
				return $default;
			}
		}
	}
	if (!function_exists('TS_TablesWP_ChildElementsActive')){
		function TS_TablesWP_ChildElementsActive($array, $key, $val) {
			foreach ($array as $item) {
				if (is_array($item)) {
				   TS_TablesWP_ChildElementsActive($item, $key, $val);
				}
				if (isset($item[$key]) && $item[$key] == $val) {
					return $item;
				}
			};
			return false;
		}
	}
	if (!function_exists('TS_TablesWP_STRRPOS_String')){
		function TS_TablesWP_STRRPOS_String($haystack, $needle, $offset = 0) { 
			if (trim($haystack) != "" && trim($needle) != "" && $offset <= strlen($haystack)) { 
				$last_pos 		= $offset; 
				$found 			= false; 
				while (($curr_pos = strpos($haystack, $needle, $last_pos)) !== false) { 
					$found 		= true; 
					$last_pos 	= $curr_pos + 1; 
				} 
				if ($found) { 
					return $last_pos - 1; 
				} else { 
					return false; 
				} 
			} else { 
				return false; 
			} 
		} 
	}
    if (!function_exists('TS_TablesWP_Color_Average')){
        function TS_TablesWP_Color_Average($color1, $color2, $factor) {
            // extract RGB values for color1.
            list($r1, $g1, $b1) = str_split(ltrim($color1, '#'), 2);
            // extract RGB values for color2.
            list($r2, $g2, $b2) = str_split(ltrim($color2, '#'), 2);
            // get the average RGB values.
            $r_avg = (hexdec($r1) * (1-$factor) + hexdec($r2) * $factor);
            $g_avg = (hexdec($g1) * (1-$factor) + hexdec($g2) * $factor);
            $b_avg = (hexdec($b1) * (1-$factor) + hexdec($b2) * $factor);  
            $color_avg = '#' . sprintf("%02s", dechex($r_avg)) . sprintf("%02s", dechex($g_avg)) . sprintf("%02s", dechex($b_avg));
            return $color_avg;
        }
    }
    if (!function_exists('TS_TablesWP_CountArrayMatches')){
        function TS_TablesWP_CountArrayMatches(array $arr, $arg, $filterValue) {
            $count = 0;
            foreach ($arr as $elem) {
                if (is_array($elem) && isset($elem[$arg]) && $elem[$arg] == $filterValue) {
                    $count++;
                }
            }
            return $count;
        }
    }
    if (!function_exists('TS_TablesWP_Memory_Usage')){
        function TS_TablesWP_Memory_Usage($decimals = 2) {
            $result = 0;
            if (function_exists('memory_get_usage')) {
                $result = memory_get_usage() / 1024;
            } else {
                if (function_exists('exec')) {
                    $output = array();
                    if (substr(strtoupper(PHP_OS), 0, 3) == 'WIN') {
                        exec('tasklist /FI "PID eq ' . getmypid() . '" /FO LIST', $output);
                        $result = preg_replace('/[\D]/', '', $output[5]);
                    } else {
                        exec('ps -eo%mem,rss,pid | grep ' . getmypid(), $output);
                        $output = explode('  ', $output[0]);
                        $result = $output[1];
                    }
                }
            }
            return number_format(intval($result) / 1024, $decimals, '.', '');
        }
    }
    if (!function_exists('TS_TablesWP_LetToNumber')){
        function TS_TablesWP_LetToNumber( $v ) {
            $l   = substr( $v, -1 );
            $ret = substr( $v, 0, -1 );
            switch ( strtoupper( $l ) ) {
                case 'P': // fall-through
                case 'T': // fall-through
                case 'G': // fall-through
                case 'M': // fall-through
                case 'K': // fall-through
                    $ret *= 1024;
                    break;
                default:
                    break;
            }
            return $ret;
        }
    }
    if (!function_exists('TS_TablesWP_CleanNumberData')){
        function TS_TablesWP_CleanNumberData($a) {
            if(is_numeric($a)) {
                $a = preg_replace('/[^0-9,]/s', '', $a);
            }
            return $a;
        }
    }
    if (!function_exists('TS_TablesWP_FormatSizeUnits')){
        function TS_TablesWP_FormatSizeUnits($bytes) {
            if ($bytes >= 1073741824) {
                $bytes = number_format($bytes / 1073741824, 2) . ' GB';
            } elseif ($bytes >= 1048576) {
                $bytes = number_format($bytes / 1048576, 2) . ' MB';
            } elseif ($bytes >= 1024) {
                $bytes = number_format($bytes / 1024, 2) . ' KB';
            } elseif ($bytes > 1) {
                $bytes = $bytes . ' Bytes';
            } elseif ($bytes == 1) {
                $bytes = $bytes . ' Byte';
            } else {
                $bytes = '0 Bytes';
            }
            return $bytes;
        }
    }
    if (!function_exists('TS_TablesWP_TruncateHTML')){
        /**
        * Truncates text.
        *
        * Cuts a string to the length of $length and replaces the last characters
        * with the ending if the text is longer than length.
        *
        * @param string  $text String to truncate.
        * @param integer $length Length of returned string, including ellipsis.
        * @param string  $ending Ending to be appended to the trimmed string.
        * @param boolean $exact If false, $text will not be cut mid-word
        * @param boolean $considerHtml If true, HTML tags would be handled correctly
        * @return string Trimmed string.
        */
        function TS_TablesWP_TruncateHTML($text, $length = 100, $ending = '...', $exact = true, $considerHtml = false) {
            if ($considerHtml) {
                // if the plain text is shorter than the maximum length, return the whole text
                if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
                    return $text;
                }
                // splits all html-tags to scanable lines
                preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
                $total_length 	= 0;
                $open_tags 		= array();
                $truncate 		= '';
                foreach ($lines as $line_matchings) {
                    // if there is any html-tag in this line, handle it and add it (uncounted) to the output
                    if (!empty($line_matchings[1])) {
                        // if it's an "empty element" with or without xhtml-conform closing slash (f.e. <br/>)
                        if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
                            // do nothing
                        // if tag is a closing tag (f.e. </b>)
                        } else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
                            // delete tag from $open_tags list
                            $pos = array_search($tag_matchings[1], $open_tags);
                            if ($pos !== false) {
                                unset($open_tags[$pos]);
                            }
                        // if tag is an opening tag (f.e. <b>)
                        } else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
                            // add tag to the beginning of $open_tags list
                            array_unshift($open_tags, strtolower($tag_matchings[1]));
                        }
                        // add html-tag to $truncate'd text
                        $truncate .= $line_matchings[1];
                    }
                    // calculate the length of the plain text part of the line; handle entities as one character
                    $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
                    if (($total_length + $content_length) > $length) {
                        // the number of characters which are left
                        $left 				= $length - $total_length;
                        $entities_length 	= 0;
                        // search for html entities
                        if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                            // calculate the real length of all entities in the legal range
                            foreach ($entities[0] as $entity) {
                                if ($entity[1] + 1 - $entities_length <= $left) {
                                    $left--;
                                    $entities_length += strlen($entity[0]);
                                } else {
                                    // no more characters left
                                    break;
                                }
                            }
                        }
                        $truncate .= substr($line_matchings[2], 0, $left+$entities_length);
                        // maximum lenght is reached, so get off the loop
                        break;
                    } else {
                        $truncate .= $line_matchings[2];
                        $total_length += $content_length;
                    }
                    // if the maximum length is reached, get off the loop
                    if ($total_length >= $length) {
                        break;
                    }
                }
            } else {
                if (strlen($text) <= $length) {
                    return $text;
                } else {
                    $truncate = substr($text, 0, $length);
                }
            }
            // if the words shouldn't be cut in the middle...
            if (!$exact) {
                // ...search the last occurance of a space...
                $spacepos = strrpos($truncate, ' ');
                if (isset($spacepos)) {
                    // ...and cut the text in this position
                    $truncate = substr($truncate, 0, $spacepos);
                }
            }
            // add the defined ending to the text
            $truncate .= ' ' . $ending;
            if ($considerHtml) {
                // close all unclosed html-tags
                foreach ($open_tags as $tag) {
                    $truncate .= '</' . $tag . '>';
                }
            }
            return $truncate;
        }
    }
    if (!function_exists('TS_TablesWP_CurrentPageURL')){
        function TS_TablesWP_CurrentPageURL() {
            $pageURL = 'http';
            if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
            $pageURL .= "://";
            if ($_SERVER["SERVER_PORT"] != "80") {
                $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
            } else {
                $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
            }
            return $pageURL;
        }
    }
    if (!function_exists('TS_TablesWP_CurrentPageName')){
        function TS_TablesWP_CurrentPageName() {
            return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
        }
    }
    if (!function_exists('TS_TablesWP_GetPostOptions')){
        function TS_TablesWP_GetPostOptions($query_args, $simple = false) {
			//remove_all_filters('posts_orderby');
            $args = wp_parse_args($query_args, array(
                'post_type' 		=> 'post',
                'posts_per_page'	=> -1,
				'offset'			=> 0,
                'orderby' 			=> 'title',
                'order' 			=> 'ASC',
				'post_status'      	=> 'publish',
            ) );
			$post_options 			= array();
			$post_data				= get_post_type_object($args['post_type']);
			// Retrieve Post Data
            $posts 					= get_posts($args);
            if ($posts) {
                foreach ($posts as $post) {
					if ($simple) {
						$post_options[$post->ID] 	= $post->post_title;
					} else {
						$post_options[] = array(
							'name' 					=> $post->post_title,
							'value' 				=> $post->ID,
							'type'					=> $post_data->labels->singular_name,
							'link'					=> urlencode(get_permalink($post->ID)),
						);
					}
                }
            }
            //TS_TablesWP_SortMultiArray($post_options, 'name');
            return $post_options;
        }
    }
    if (!function_exists('TS_TablesWP_GetTheCategoryByTax')){
        function TS_TablesWP_GetTheCategoryByTax($id = false, $tcat = 'category') {
            $categories = get_the_terms($id, $tcat);
            if ((!$categories) || is_wp_error($categories)) {
                $categories = array();
            }
            $categories = array_values($categories);
            foreach (array_keys($categories) as $key) {
                _make_cat_compat($categories[$key]);
            }
            return apply_filters('get_the_categories', $categories);
        }
    }
    if (!function_exists('TS_TablesWP_PluginIsActive')){
        function TS_TablesWP_PluginIsActive($plugin_path) {
            $return_var = in_array($plugin_path, apply_filters('active_plugins', get_option('active_plugins')));
            return $return_var;
        }
    }
    if (!function_exists('TS_TablesWP_CheckShortcode')){
        function TS_TablesWP_CheckShortcode($shortcode = '') {
            $post_to_check = get_post(get_the_ID());
            // false because we have to search through the post content first
            $found = false;
            // if no short code was provided, return false
            if (!$shortcode) {
                return $found;
            }
            // check the post content for the short code
            if (stripos($post_to_check->post_content, '[' . $shortcode) !== false) {
                // we have found the short code
                $found = true;
            }
            // return our final results
            return $found;
        }
    }
    if (!function_exists('TS_TablesWP_CheckString')){
        function TS_TablesWP_CheckString($string = '') {
            $post_to_check = get_post(get_the_ID());
            // false because we have to search through the post content first
            $found = false;
            // if no string was provided, return false
            if (!$string) {
                return $found;
            }
            // check the post content for the short code
            if (stripos($post_to_check->post_content, '' . $string) !== false) {
                // we have found the string
                $found = true;
            }
            // return our final results
            return $found;
        }
    }
    if (!function_exists('TS_TablesWP_GetExtraClass')){
        function TS_TablesWP_GetExtraClass($el_class) {
            $output = '';
            if ( $el_class != '' ) {
                $output = " " . str_replace(".", "", $el_class);
            }
            return $output;
        }
    }
    if (!function_exists('TS_TablesWP_endBlockComment')){
        function TS_TablesWP_endBlockComment($string) {
            return (!empty($_GET['wpb_debug']) && $_GET['wpb_debug']=='true' ? '<!-- END '.$string.' -->' : '');
        }
    }
    if (!function_exists('TS_TablesWP_DeleteOptionsPrefixed')){
        function TS_TablesWP_DeleteOptionsPrefixed($prefix) {
            global $wpdb;
            $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '{$prefix}%'" );
        }
    }
	if (!function_exists('TS_TablesWP_SortMultiArray')){
        function TS_TablesWP_SortMultiArray(&$array, $key, $maintain) {
            foreach($array as &$value) {
                $value['__________'] = $value[$key];
            }
            /* Note, if your functions are inside of a class, use: 
                uasort($array, array("My_Class", 'TS_TablesWP_SortByDummyKey'));
            */
			if ($maintain) {
				uasort($array, 'TS_TablesWP_SortByDummyKey');
			} else {
				usort($array, 'TS_TablesWP_SortByDummyKey');
			}            
            foreach($array as &$value) {   // removes the dummy key from your array
                unset($value['__________']);
            }
            return $array;
        }
    }
    if (!function_exists('TS_TablesWP_SortByDummyKey')){
        function TS_TablesWP_SortByDummyKey($a, $b) {
            if($a['__________'] == $b['__________']) return 0;
            if($a['__________'] < $b['__________']) return -1;
            return 1;
        }
    }
    if (!function_exists('TS_TablesWP_CaseInsensitiveSort')){
        function TS_TablesWP_CaseInsensitiveSort($a,$b) { 
            return strtolower($b) < strtolower($a); 
        }
    }
    if (!function_exists('TS_TablesWP_getRemoteFile')){
        function TS_TablesWP_getRemoteFile($url) {
            // get the host name and url path
            $parsedUrl = parse_url($url);
            $host = $parsedUrl['host'];
            if (isset($parsedUrl['path'])) {
                $path = $parsedUrl['path'];
            } else {
                // the url is pointing to the host like http://www.mysite.com
                $path = '/';
            }
            if (isset($parsedUrl['query'])) {
                $path .= '?' . $parsedUrl['query'];
            }
            if (isset($parsedUrl['port'])) {
                $port = $parsedUrl['port'];
            } else {
                // most sites use port 80
                $port = '80';
            }
            $timeout = 10;
            $response = '';
            // connect to the remote server
            $fp = @fsockopen($host, '80', $errno, $errstr, $timeout );
            if( !$fp ) {
                echo "Cannot retrieve $url";
            } else {
                // send the necessary headers to get the file
                fputs($fp, "GET $path HTTP/1.0\r\n" .
                "Host: $host\r\n" .
                "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.0.3) Gecko/20060426 Firefox/1.5.0.3\r\n" .
                "Accept: */*\r\n" .
                "Accept-Language: en-us,en;q=0.5\r\n" .
                "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n" .
                "Keep-Alive: 300\r\n" .
                "Connection: keep-alive\r\n" .
                "Referer: http://$host\r\n\r\n");
                // retrieve the response from the remote server
                while ( $line = fread( $fp, 4096 ) ) {
                    $response .= $line;
                }
                fclose( $fp );
                // strip the headers
                $pos = strpos($response, "\r\n\r\n");
                $response = substr($response, $pos + 4);
            }
            // return the file content
            return $response;
        }
    }
    if (!function_exists('TS_TablesWP_retrieveExternalData')){
        function TS_TablesWP_retrieveExternalData($url){
            if (function_exists('curl_init')) {
                //echo 'Using CURL';
                // initialize a new curl resource
				$ch                         = curl_init();
				$timeout                    = 60;                             
				curl_setopt($ch, CURLOPT_URL,               $url);
				curl_setopt($ch, CURLOPT_HEADER, 			0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,    true);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,    $timeout);
				curl_setopt($ch, CURLOPT_MAXREDIRS,         3);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 	false);
				curl_setopt($ch, CURLOPT_USERAGENT,         'Tablenator - Advanced Tables for WordPress & WP Bakery Page Builder (18560899) by Kraut Coding');
				$content					= curl_exec($ch);
				if (!curl_errno($ch)) {
					$success				= true;
				} else {
					$error					= curl_errno($ch);
				}
				curl_close($ch);
            } else if (ini_get('allow_url_fopen') == '1') {
                //echo 'Using file_get_contents';
                $content = @file_get_contents($url);
                if ($content !== false) {
                    $content = $content;
                } else {
                    $content = '';
                }
            } else {
                //echo 'Using Others';
                $content = TS_TablesWP_getRemoteFile($url);
            }
            return $content;
        }
    }
    if (!function_exists('TS_TablesWP_cURLcheckBasicFunctions')){
        function TS_TablesWP_cURLcheckBasicFunctions() {
            if( !function_exists("curl_init") &&
                !function_exists("curl_setopt") &&
                !function_exists("curl_exec") &&
                !function_exists("curl_close") ) return false;
            else return true;
        }
    }
    if (!function_exists('TS_TablesWP_checkValidURL')){
        function TS_TablesWP_checkValidURL($url) {
            if (preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $url)) {
                return true;
            } else {
                return false;
            }
        }
    }
    if (!function_exists('TS_TablesWP_makeValidURL')){
        function TS_TablesWP_makeValidURL($url) {
            if (preg_match("~^(?:f|ht)tps?://~i", $url)) {
                return $url;
            } else {
                return 'http://' . $url;
            }
        }
    }
    if (!function_exists('TS_TablesWP_numberOfDecimals')){
        function TS_TablesWP_numberOfDecimals($value) {
            if ((int)$value == $value) {
                return 0;
            } else if (!is_numeric($value)) {
                // throw new Exception('numberOfDecimals: ' . $value . ' is not a number!');
                return false;
            }
            return strlen($value) - strrpos($value, '.') - 1;
        }
    }
    if (!function_exists('TS_TablesWP_RemoveDirectory')){
        function TS_TablesWP_RemoveDirectory($directory, $empty = false) { 
            if (substr($directory, -1) == "/") { 
                $directory = substr($directory, 0, -1); 
            } 
            if (!file_exists($directory) || !is_dir($directory)) { 
                return false;
            } elseif (!is_readable($directory)) { 
                return false; 
            } else { 
                $directoryHandle = opendir($directory); 
                while ($contents = readdir($directoryHandle)) { 
                    if ($contents != '.' && $contents != '..') { 
                        $path = $directory . "/" . $contents;
                        if (is_dir($path)) { 
                            TS_TablesWP_RemoveDirectory($path); 
                        } else { 
                            unlink($path); 
                        } 
                    } 
                } 
                closedir($directoryHandle); 
                if ($empty == false) { 
                    if (!rmdir($directory)) { 
                        return false; 
                    } 
                }
                return true; 
            } 
        }
    }    
    if (!function_exists('TS_TablesWP_GetRandomColorHex')){
        function TS_TablesWP_GetRandomColorHex($max_r = 255, $max_g = 255, $max_b = 255) {
            // ensure that values are in the range between 0 and 255
            $max_r = max(0, min($max_r, 255));
            $max_g = max(0, min($max_g, 255));
            $max_b = max(0, min($max_b, 255));
            // generate and return the random color
            return str_pad(dechex(rand(0, $max_r)), 2, '0', STR_PAD_LEFT) . str_pad(dechex(rand(0, $max_g)), 2, '0', STR_PAD_LEFT) . str_pad(dechex(rand(0, $max_b)), 2, '0', STR_PAD_LEFT);
        }
    }
	if (!function_exists('TS_TablesWP_ConverToRoman')) {
		function TS_TablesWP_ConverToRoman($num){ 
			$n = intval($num); 
			$res = '';		
			//array of roman numbers
			$romanNumber_Array = array( 
				'M'  => 1000, 
				'CM' => 900, 
				'D'  => 500, 
				'CD' => 400, 
				'C'  => 100, 
				'XC' => 90, 
				'L'  => 50, 
				'XL' => 40, 
				'X'  => 10, 
				'IX' => 9, 
				'V'  => 5, 
				'IV' => 4, 
				'I'  => 1); 		
			foreach ($romanNumber_Array as $roman => $number){ 
				//divide to get  matches
				$matches = intval($n / $number); 		
				//assign the roman char * $matches
				$res .= str_repeat($roman, $matches); 		
				//substract from the number
				$n = $n % $number; 
			} 		
			// return the result
			return $res; 
		}
	}
	if (!function_exists('TS_TablesWP_ConvertToAlpha')) {
		function TS_TablesWP_ConvertToAlpha($num){
			return chr(substr("000".($num+65),-3));
		}
	}
	if (!function_exists('TS_TablesWP_ConvertPlaceholderComma')) {
		function TS_TablesWP_ConvertPlaceholderComma($string){
			$string = str_replace(array("|comma|", "/comma/", "{comma}", "[comma]"), ",", $string);
			return $string;
		}
	}
	if (!function_exists('TS_TablesWP_CreatePreloaderCSS')){
		function TS_TablesWP_CreatePreloaderCSS($id, $class, $style, $enqueue) {
			$preloader 						= '';
			$style 							= intval($style);			
			if ($style > -1) {
				if ($enqueue == "true") {
					wp_enqueue_style('ts-extend-preloaders');
				}
				$spancount 					= 0;
				$spandatas					= array(
					0 => 0, 1 => 5, 2 => 4, 3 => 0, 4 => 5, 5 => 0, 6 => 4, 7 => 4, 8 => 0, 9 => 4,
					10 => 0, 11 => 2, 12 => 2, 13 => 1, 14 => 5, 15 => 3, 16 => 6, 17 => 6, 18 => 3, 19 => 0,
					20 => 4, 21 => 5, 22 => 0,
				);
				$spancount 					= (isset($spandatas[$style]) ? $spandatas[$style] : 0);
				$preloader .= '<div id="' . $id . '" class="' . $class . ' ts-preloader-animation-main ts-preloader-animation-' . $style . '">';
					for ($x = 1; $x <= $spancount; $x++) {
						$preloader .= '<span></span>';
					}
				$preloader .= '</div>';
			}
			return $preloader;
		}
	}
	if (!function_exists('TS_TablesWP_CheckRegisteredFileStatus')) {
		function TS_TablesWP_CheckRegisteredFileStatus($file, $type) {
			if (($type == "style") && ($file != '')) {
				return $filestatus = array(
					'registered'		=> wp_style_is($file, 'registered'),
					'enqueued'			=> wp_style_is($file, 'enqueued'),
					'done'				=> wp_style_is($file, 'done'),
					'to_do'				=> wp_style_is($file, 'to_do'),
				);
			} else if (($type == "script") && ($file != '')) {
				return $filestatus = array(
					'registered'		=> wp_script_is($file, 'registered'),
					'enqueued'			=> wp_script_is($file, 'enqueued'),
					'done'				=> wp_script_is($file, 'done'),
					'to_do'				=> wp_script_is($file, 'to_do'),
				);
			} else {
				return $filestatus = array(
					'registered'		=> false,
					'enqueued'			=> false,
					'done'				=> false,
					'to_do'				=> false,
				);
			}
		}
	}
	if (!function_exists('TS_TablesWP_FrontendAppendCustomRules')) {
		function TS_TablesWP_FrontendAppendCustomRules($type) {
			if ($type == "style") {
				wp_enqueue_style('ts-extend-advancedcustom');
				return "true";
			} else if ($type == "script") {
				wp_enqueue_style('ts-extend-advancedcustom');
				return "true";
			} else {
				return "false";
			}
		}
	}
?>