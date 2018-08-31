<?php
	if (!class_exists('TS_Tablenator_ColorManipulator')){
		class TS_Tablenator_ColorManipulator {
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
			
			function __construct() {
				global $TS_ADVANCED_TABLESWP;
			}
			

			/* ---------------------------- */
			/* Color Maker Helper Functions */
			/* ---------------------------- */
			static public function TS_TablesWP_ColorMakerSingle($options = array()) {
				$h = self::_pickHue($options);
				$s = self::_pickSaturation($h, $options);
				$v = self::_pickBrightness($h, $s, $options);    
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
			static private function _pickHue($options) {
				$range = self::_getHueRange($options);    
				if (empty($range)) {
					return 0;
				}    
				$hue = self::_rand($range, $options);    
				// Instead of storing red as two separate ranges,
				// we group them, using negative numbers
				if ($hue < 0) {
					$hue = 360 + $hue;
				}    
				return $hue;
			}	  
			static private function _pickSaturation($h, $options) {
				if (@$options['luminosity'] === 'random') {
					return self::_rand(array(0, 100), $options);
				}
				if (@$options['hue'] === 'monochrome') {
					return 0;
				}    
				$colorInfo = self::_getColorInfo($h);
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
				return self::_rand($range, $options);
			}	  
			static private function _pickBrightness($h, $s, $options) {
				if (@$options['luminosity'] === 'random') {
					$range = array(0, 100);
				} else {
					$range = array(
						self::_getMinimumBrightness($h, $s),
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
				return self::_rand($range, $options);
			}	  
			static private function _getHueRange($options) {
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
					return $ranges[self::_rand(array(0, $l-1), $options)];
				}
			}	  
			static private function _getMinimumBrightness($h, $s) {
				$colorInfo = self::_getColorInfo($h);
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
			static private function _getColorInfo($h) {
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
			static private function _rand($bounds, $options) {
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
	if (class_exists('TS_Tablenator_ColorManipulator')) {
		$TS_Tablenator_ColorManipulator = new TS_Tablenator_ColorManipulator;
	}
?>