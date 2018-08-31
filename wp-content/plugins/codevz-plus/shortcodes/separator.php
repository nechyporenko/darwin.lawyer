<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
/**
 *
 * Separator
 * 
 * @author Codevz
 * @link http://codevz.com/
 * 
 */
class CDVZ_separator extends Codevz_Plus {

	public function __construct( $name ) {
		$this->name = $name;
	}

	/**
	 *
	 * Shortcode settings ( vc_map )
	 * 
	 * @return array
	 * 
	 */
	public function in() {
		add_shortcode( $this->name, array( $this, 'out' ) );
		
		Codevz_Plus::vc_map( array(
			'category'		=> CDVZ_VC_CAT,
			'base'			=> $this->name,
			'name'			=> esc_html__( 'Separator', 'codevz' ),
			'description'	=> esc_html__( 'Row separator space', 'codevz' ),
			'icon'			=> 'czi',
			"weight"         => CDVZ_VC_WEIGHT,
			'params'		=> array(
				array(
					'type' 			=> 'cz_sc_id',
					'param_name' 	=> 'id',
					'save_always' 	=> true
				),
				array(
					'type' 			=> 'dropdown',
					'heading' 		=> esc_html__('Style', 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'param_name' 	=> 'style',
					'value'		=> array(
						'Style 1 (Triangle Down)' 	=> 'cz_sep_1',
						'Style 2 (Triangle Up)' 	=> 'cz_sep_2',
						'Style 3 (Slanted Right)' 	=> 'cz_sep_3',
						'Style 4 (Slanted Left)' 	=> 'cz_sep_4',
						'Style 5 (Halfcircle Top)' 	=> 'cz_sep_5',
						'Style 6 (Halfcircle Bottom)' 	=> 'cz_sep_6',
						'Style 7 (Big Triangle Down)' 	=> 'cz_sep_7',
						'Style 8 (Big Triangle Up)' 	=> 'cz_sep_8',
						'Style 9 (Big Curve Up)' 	=> 'cz_sep_9',
						'Style 10 (Big Curve Down)' 	=> 'cz_sep_10',
						'Style 11 (Roundedsplit Down)' 	=> 'cz_sep_11',
						'Style 12 (Roundedsplit Up)' 	=> 'cz_sep_12',
						'Style 13 (ZigZag Up)' 	=> 'cz_sep_13',
						'Style 14 (ZigZag Down)' 	=> 'cz_sep_14',
						'Style 15 (Roundedges Top)' 	=> 'cz_sep_15',
						'Style 16 (Roundedges Top)' 	=> 'cz_sep_16',
						'Style 17 (Spikey Top)' 	=> 'cz_sep_17',
						'Style 18 (Spikey Down)' 	=> 'cz_sep_18',
						'Style 19 (Saw left)' 	=> 'cz_sep_19',
						'Style 20 (Saw Right)' 	=> 'cz_sep_20',
						'Style 21 (Alternating Squares)' 	=> 'cz_sep_21',
						'Style 22 (Castle)' 	=> 'cz_sep_22',
						'Style 23 (Clouds Up)' 	=> 'cz_sep_23',
						'Style 24 (Clouds Down)' 	=> 'cz_sep_24',
						'Style 25 (SVG)' 	=> 'cz_sep_25',
						'Style 26 (SVG)' 	=> 'cz_sep_26',
						'Style 27 (SVG)' 	=> 'cz_sep_27',
						'Style 28 (SVG)' 	=> 'cz_sep_28',
						'Style 29 (SVG)' 	=> 'cz_sep_29',
						'Style 30 (SVG)' 	=> 'cz_sep_30',
					),
					'std'	=> 'cz_sep_25'
				),
				array(
					'type' 			=> 'dropdown',
					'heading' 		=> esc_html__('Rotate', 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'param_name' 	=> 'rotate',
					'value'		=> array(
						'Select' 			=> '',
						'Rotate Full' 		=> 'cz_sep_rotate',
						'Rotate Horizontal' => 'cz_sep_rotatey',
						'Rotate Vertical' 	=> 'cz_sep_rotatex'
					)
				),
				array(
					"type"        	=> "textfield",
					"heading"     	=> esc_html__("Extra class", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"  	=> "class"
				),
				array(
					"type"        	=> "cz_slider",
					"heading"     	=> esc_html__("Height", 'codevz'),
					"param_name"  	=> "sep_height",
					"description"   => "eg. 100px or 5em",
					'edit_field_class' => 'vc_col-xs-99',
					'dependency'	=> array(
						'element'		=> 'style',
						'value'			=> array( 'cz_sep_7','cz_sep_8','cz_sep_9','cz_sep_10','cz_sep_13','cz_sep_14' ,'cz_sep_19' ,'cz_sep_20','cz_sep_21','cz_sep_22','cz_sep_23','cz_sep_24')
					)
				),
				array(
					"type"        	=> "cz_slider",
					"heading"     	=> esc_html__("Width", 'codevz'),
					"param_name"  	=> "sep_width",
					"description"   => "eg. 100px or 5em",
					'edit_field_class' => 'vc_col-xs-99',
					'dependency'	=> array(
						'element'		=> 'style',
						'value'			=> array('cz_sep_21','cz_sep_22')
					)
				),
				array(
					"type"        	=> "colorpicker",
					"heading"     	=> esc_html__("Top Color", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"  	=> "top_color"
				),
				array(
					"type"        	=> "colorpicker",
					"heading"     	=> esc_html__("Bottom Color", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"  	=> "bottom_color",
					'dependency'	=> array(
						'element'		=> 'style',
						'value'			=> array( 'cz_sep_1','cz_sep_2','cz_sep_3','cz_sep_4','cz_sep_5','cz_sep_6','cz_sep_7','cz_sep_8','cz_sep_9','cz_sep_10','cz_sep_15','cz_sep_16','cz_sep_17','cz_sep_18','cz_sep_19','cz_sep_20','cz_sep_21','cz_sep_22')
					)
				),

				array(
					'type' 			=> 'cz_title',
					'param_name' 	=> 'cz_title',
					'class' 		=> '',
					'content' 		=> esc_html__( 'Advanced Position', 'codevz' ),
				),
				array(
					'type' 			=> 'dropdown',
					'heading' 		=> esc_html__('Layer Position', 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'param_name' 	=> 'css_position',
					'value'		=> array(
						__( 'Select', 'codevz' ) => '',
						'relative' => 'relative',
						'absolute' => 'absolute;width:100%',
						'fixed' => 'fixed',
						'static' => 'static',
						'sticky' => 'sticky',
						'inherit' => 'inherit',
						'initial' => 'initial',
						'unset' => 'unset'

					),
				),
				array(
					'type' 			=> 'dropdown',
					'heading' 		=> esc_html__('Layer Priority', 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'param_name' 	=> 'css_z-index',
					'value'		=> array(
						'-2' => '-2',
						'-1' => '-1',
						'0' => '0',
						'1'	=> '1',
						'2' => '2',
						'3'	=> '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
						'7' => '7',
						'8' => '8',
						'9' => '9',
						'10' => '10',
						'99' => '99',
						'999' => '999',
					),
					'std'			=> '0',
				),
				array(
					'type' => 'cz_slider',
					'heading' => esc_html__( 'Top offset', 'codevz' ),
					'description' => 'e.g. 20px or 20%',
					'edit_field_class' => 'vc_col-xs-99',
					'param_name' => 'css_top',
				),
				array(
					'type' => 'cz_slider',
					'heading' => esc_html__( 'Left offset', 'codevz' ),
					'edit_field_class' => 'vc_col-xs-99',
					'param_name' => 'css_left',
				)
			)

		));
	}

	public function out( $atts, $content = '' ) {
		$atts = vc_map_get_attributes( $this->name, $atts );

		// ID
		if ( ! $atts['id'] ) {
			$atts['id'] = Codevz_Plus::uniqid();
			$public = 1;
		}
		$css_id = '#' . $atts['id'];

		$style = $atts['style'];
		$top_color = $atts['top_color'] ? $atts['top_color'] : '';
		$bottom_color = $atts['bottom_color'] ? ' style="background-color: ' . $atts['bottom_color'] . '"' : '';

		$out = '<div class="cz_sep ' . $style . '_par ' . $atts['rotate'] . '">';

		if ( $style==='cz_sep_25' ) {
			$out .= '<svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 213"><path fill="' . $top_color . '" d="M0,193S392.385-118.6,838,49c462.98,171.608,1083,143,1082,144v19H0Z" /></svg>';
		} else if ( $style==='cz_sep_26' ) {
			$out .= '<svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 180"><g style="isolation: isolate"><g><path fill="' . $top_color . '" style="fill-rule: evenodd" d="M0,0V27S513.4-16.386,995,129c519.509,155.418,926-101,927-102V0Z"/></g></g></svg>';
		} else if ( $style==='cz_sep_27' ) {
			$out .= '<svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 146"><path fill="' . $top_color . '" d="M1182,48C633.293-88.421,0,115,0,116v30H1920V116S1612.972,156.488,1183,48Z" /></svg>';
		} else if ( $style==='cz_sep_28' ) {
			$out .= '<svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 150"><path fill="' . $top_color . '" d="M0,0V52s198.63,76.671,575,94C1156.292,175.308,1921,35,1921,36V0Z" /></svg>';
		} else if ( $style==='cz_sep_29' ) {
			$out .= '<svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 176"><path fill="' . $top_color . '" d="M1249,2C708.25-18.224,0,147,0,146v30H1920V146S1663.461,17.267,1247,2Z" /></svg>';
		} else if ( $style==='cz_sep_30' ) {
			$out .= '<svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 109"><path fill="' . $top_color . '" d="M0,0V48S265.751,172.965,856,65c512.379-93.672,1066-18,1066-17V0Z" /></svg>';
		} else if ($style==='cz_sep_7' || $style==='cz_sep_8') {
			$top_color = $top_color ? ' style="fill: ' . $top_color . ';stroke:' . $top_color .';stroke-width:2"' : '';
			$sep_height = $atts['sep_height'] ?  $atts['sep_height'] : '100';
			$out .= '<div class="cz_sep_svg '. $style . '" id="'.$atts['id'].'" ' . self::separator_style( $atts ) .'><svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" version="1.1" '. $bottom_color .' width="100%" height="'.$sep_height.'" viewBox="0 0 100 105" preserveAspectRatio="none" ><path d="M0 0 L50 100 L100 0 Z" '.$top_color.'/></svg></div>';
		} else if ($style==='cz_sep_9' || $style==='cz_sep_10') {
			$top_color = $top_color ? ' style="fill: ' . $top_color . ';stroke:' . $top_color .';"' : '';
			$sep_height = $atts['sep_height'] ? $atts['sep_height'] : '100';
			$out .= '<div class="cz_sep_svg '. $style . '" id="'.$atts['id'].'"' . self::separator_style( $atts ) .'><svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" version="1.1" '. $bottom_color .' width="100%" height="'.$sep_height.'" viewBox="0 0 100 100" preserveAspectRatio="none" ><path d="M0 100 C30 0 70 0 100 100 Z" '.$top_color.'/></svg></div>';

		} else if ($style==='cz_sep_23' || $style==='cz_sep_24') {
			$top_color = $top_color ? ' style="fill: ' . $top_color . ';stroke:' . $top_color .';"' : '';
			$sep_height = $atts['sep_height'] ? $atts['sep_height'] : '100';
			$out .= '<div class="cz_sep_svg '. $style . '" id="'.$atts['id'].'" ' . self::separator_style( $atts ) .'><svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" version="1.1" '. $bottom_color .' width="100%" height="'.$sep_height.'" viewBox="0 0 100 100" preserveAspectRatio="none" ><path d="M-5 100 Q 0 20 5 100 Z M0 100 Q 5 0 10 100 M5 100 Q 10 30 15 100 M10 100 Q 15 10 20 100 M15 100 Q 20 30 25 100 M20 100 Q 25 -10 30 100 M25 100 Q 30 10 35 100 M30 100 Q 35 30 40 100 M35 100 Q 40 10 45 100 M40 100 Q 45 50 50 100 M45 100 Q 50 20 55 100 M50 100 Q 55 40 60 100 M55 100 Q 60 60 65 100 M60 100 Q 65 50 70 100 M65 100 Q 70 20 75 100 M70 100 Q 75 45 80 100 M75 100 Q 80 30 85 100 M80 100 Q 85 20 90 100 M85 100 Q 90 50 95 100 M90 100 Q 95 25 100 100 M95 100 Q 100 15 105 100 Z" '.$top_color.'/></svg></div>';

		} else {

			$after_color ='';
			if ($style==='cz_sep_11' || $style==='cz_sep_12' || $style==='cz_sep_15' || $style==='cz_sep_16') {
				$after_color ='data-after-color=":after{background-color:'.$top_color.';}"';
			}

			if ($style==='cz_sep_21' || $style==='cz_sep_22') {
				$bottom_color = $atts['bottom_color'] ? $atts['bottom_color'] : '#FFFFFF';
				$top_color = $atts['top_color'] ? $atts['top_color'] : '#CCCCCC';
				$sep_height = $atts['sep_height'] ? $atts['sep_height'] : '130px';
				$sep_width = $atts['sep_width'] ? $atts['sep_width'] : '50px';
				$after_color ='data-after-color=":before{background-image: linear-gradient(to right, '.$top_color.' 50%, '.$bottom_color.' 50%);background-size: '.$sep_width.' 100%;height: '.$sep_height.';}" style="height:'.$sep_height.'"';

				if ($style==='cz_sep_22'){$after_color =str_replace('to right', '40deg', $after_color);}
			}

			if ($style==='cz_sep_13' || $style==='cz_sep_14') {
				$sep_height = $atts['sep_height'] ? $atts['sep_height'] : '50px';
				$sep_inline_style = self::separator_style( $atts, array(
					'extra'		=> 'background-image: linear-gradient(315deg, '.$top_color.' 24%, transparent 24%), linear-gradient(45deg, '.$top_color.' 24%, transparent 24%);background-size: '.$sep_height.' 100%;height: '.$sep_height.';' ));
				$out .= '<div id="'.$atts['id'].'" class="cz_separator2 '. $style . '" ' . $sep_inline_style .'></div>';
			} else {
				if ($style==='cz_sep_17' || $style==='cz_sep_18') {
					$after_color ='data-after-color=":after{background-color:'.$top_color.';}'.$css_id.':before{box-shadow: -50px 50px 0 '.$top_color.', 50px -50px 0 '.$top_color.';}"';
				}
				if ($style==='cz_sep_19' || $style==='cz_sep_20'){
					$bottom_color = $atts['bottom_color'] ? $atts['bottom_color'] : '#CCCCCC';
					$top_color = $atts['top_color'] ? $atts['top_color'] : '#FFFFFF';
					$sep_height = $atts['sep_height'] ? $atts['sep_height'] : '50px';
					$after_color ='data-after-color=":after{background-size: '.$sep_height.' 100%;height: '.$sep_height.';}'.$css_id.':before{background-image: linear-gradient(15deg, '.$bottom_color.' 50%, '.$top_color.' 50%); background-size: '.$sep_height.' 100%;height: '.$sep_height.';top:0;}" style="height:'.$sep_height.'"';
					if ($style==='cz_sep_20'){$after_color =str_replace('15deg', '165deg', $after_color);}
				}
				
				$out .= '<div id="'.$atts['id'].'" class="cz_separator '. $style . ' '. $atts['class'] . '" '. self::separator_style( $atts ) . $bottom_color . ' data-before-color=":before{background-color:'.$top_color.';}" '.$after_color.'></div>';
			}
		}

		if ($style==='cz_sep_25' || $style==='cz_sep_26' || $style==='cz_sep_27' || $style==='cz_sep_28' || $style==='cz_sep_29' || $style==='cz_sep_30'){
			$out = '<div id="'.$atts['id'].'" class="cz_separator3 '. $style . '" ' . self::separator_style( $atts ) .'>'.$out.'</div>';
		}


		$out .= '</div>';

		return Codevz_Plus::_out( $atts, $out, 'separator' );
	}

	/**
	 *
	 * TEMP: Generate style in tag mode or inline
	 * 
	 */
	public static function separator_style( $a = array(), $s = array() ) {
		if ( empty( $a ) ) {
			return;
		}

		// Prepare
		$a = array_filter( (array) $a );
		$s = wp_parse_args( $s, array(
			'prefix' 	=> 'css_',
			'important' => ';',
			'before' 	=> ' style="',
			'after' 	=> '"',
			'extra' 	=> ''
		));

		$prefix = $s['prefix'];
		$out = array();

		// Start split styles with their values
		foreach ( $a as $key => $val ) {
			if ( ! empty( $val ) && strpos( $key, $prefix ) === 0 ) {

				// Define key
				$key = str_replace( $prefix, '', $key );

				// Continue to next, if its VC CSS box value
				if ( Codevz_Plus::contains( $val, 'vc_custom' ) ) {
					continue;
				}

				// Out
				$out[] = $key . ': ' . $val . $s['important'];
			}
		}

		// Output plus extra styles
		$out = empty( $out ) ? '' : implode( '', $out );
		$out .= str_replace( ' !important', '', $s['extra'] );

		return $out ? $s['before'] . $out . $s['after'] : '';
	}

}