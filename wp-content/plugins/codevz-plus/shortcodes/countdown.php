<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.

/**
 *
 * Countdown
 * 
 * @author Codevz
 * @copyright Codevz
 * @link http://codevz.com/
 * 
 */
class CDVZ_countdown extends Codevz_Plus {

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

		Codevz_Plus::vc_map(array(
			'category'		=> CDVZ_VC_CAT,
			'base'			=> $this->name,
			'name'			=> esc_html__( 'Countdown', 'codevz' ),
			'description'	=> esc_html__( 'Time reminder for event', 'codevz' ),
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
					'heading' 		=> esc_html__('Type', 'codevz'),
					'param_name' 	=> 'type',
					'edit_field_class' => 'vc_col-xs-99',
					'value'		=> array(
						esc_html__('Count down', 'codevz') 			=> 'down',
						esc_html__('Count up', 'codevz') 			=> 'up',
						esc_html__('Loop count down', 'codevz') 	=> 'loop',
					),
					'std' => 'down',
				),
				array(
					"type"        	=> "textfield",
					"heading"     	=> esc_html__( "Minutes", 'codevz' ),
					'value'			=> '120',
					"param_name"  	=> "loop",
					'edit_field_class' => 'vc_col-xs-99',
					'dependency'	=> array(
						'element'		=> 'type',
						'value'			=> array('loop')
					),
				),
				array(
					"type"        	=> "textfield",
					"heading"     	=> esc_html__( "Date", 'codevz' ),
					'value'			=> date( 'Y/m/j H:i', strtotime("1 year") ),
					'description'	=> 'e.g. ' . date( 'Y/m/j H:i', strtotime("1 year") ),
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"  	=> "date",
					'dependency'	=> array(
						'element'		=> 'type',
						'value'			=> array('down', 'up')
					),
				),
				array(
					'type' 			=> 'dropdown',
					'heading' 		=> esc_html__('Position', 'codevz'),
					'param_name' 	=> 'pos',
					'edit_field_class' => 'vc_col-xs-99',
					'value'		=> array(
						esc_html__( 'Center', 'cd' ) 	=> 'tac',
						esc_html__( 'Left', 'cd' ) 		=> 'tal',
						esc_html__( 'Right', 'cd' ) 	=> 'tar',
						esc_html__( 'Center vertical', 'cd' ) 	=> 'tac cz_countdown_center_v',
						esc_html__( 'Left vertical', 'cd' ) 	=> 'tal cz_countdown_left_v',
						esc_html__( 'Right vertical', 'cd' ) 	=> 'tal cz_countdown_right_v',
						esc_html__( 'Inline view', 'cd' ) 		=> 'tac cz_countdown_inline',
					)
				),
				array(
					'type' 			=> 'cz_title',
					'param_name' 	=> 'cz_title',
					'class' 		=> '',
					'content' 		=> esc_html__( 'Translation', 'codevz' ),
				),
				array(
					"type"        	=> "textfield",
					"heading"     	=> esc_html__("Year", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"  	=> "year",
					'dependency'	=> array(
						'element'		=> 'type',
						'value'			=> array('up')
					),
				),
				array(
					"type"        	=> "textfield",
					"heading"     	=> esc_html__("Day", 'codevz'),
					'value'			=> 'Day',
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"  	=> "day"
				),
				array(
					"type"        	=> "textfield",
					"heading"     	=> esc_html__("Hour", 'codevz'),
					'value'			=> 'Hour',
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"  	=> "hour"
				),
				array(
					"type"        	=> "textfield",
					"heading"     	=> esc_html__("Minute", 'codevz'),
					'value'			=> 'Minute',
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"  	=> "minute"
				),
				array(
					"type"        	=> "textfield",
					"heading"     	=> esc_html__("Second", 'codevz'),
					'value'			=> 'Second',
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"  	=> "second"
				),
				array(
					"type"        	=> "textfield",
					"heading"     	=> esc_html__("Apostrophe s", 'codevz'),
					'value'			=> 's',
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"  	=> "plus"
				),
				array(
					"type"        	=> "textfield",
					"heading"     	=> esc_html__("Expired message", 'codevz'),
					'value'			=> 'This event has been expired',
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"  	=> "expire",
					'dependency'	=> array(
						'element'		=> 'type',
						'value'			=> array('down', 'loop')
					),
				),

				// Styling
				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'sk_overall',
					"heading"     	=> esc_html__( "Container", 'codevz'),
					'button' 		=> esc_html__( "Container", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'settings' 		=> array( 'background', 'padding', 'border', 'box-shadow' ),
					'group' 		=> esc_html__( 'Styling', 'codevz' ),
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_overall_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_overall_mobile' ),
				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'sk_cols',
					"heading"     	=> esc_html__( "Columns", 'codevz'),
					'button' 		=> esc_html__( "Columns", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'settings' 		=> array( 'width', 'text-align', 'background', 'padding', 'margin', 'border', 'box-shadow' ),
					'group' 		=> esc_html__( 'Styling', 'codevz' ),
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_cols_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_cols_mobile' ),

				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'sk_nums',
					"heading"     	=> esc_html__( "Numbers", 'codevz'),
					'button' 		=> esc_html__( "Numbers", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'settings' 		=> array( 'color', 'font-family', 'font-size', 'background', 'padding', 'margin', 'border' ),
					'group' 		=> esc_html__( 'Styling', 'codevz' ),
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_nums_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_nums_mobile' ),

				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'sk_title',
					"heading"     	=> esc_html__( "Titles", 'codevz'),
					'button' 		=> esc_html__( "Titles", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'settings' 		=> array( 'color', 'font-family', 'font-size', 'background', 'padding', 'margin', 'border' ),
					'group' 		=> esc_html__( 'Styling', 'codevz' ),
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_title_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_title_mobile' ),

				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'sk_expired',
					"heading"     	=> esc_html__( "Expired message", 'codevz'),
					'button' 		=> esc_html__( "Expired message", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'settings' 		=> array( 'width', 'color', 'font-family', 'font-size', 'background', 'padding' ),
					'group' 		=> esc_html__( 'Styling', 'codevz' ),
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_expired_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_expired_mobile' ),

				// Advanced
				array(
					'type' 			=> 'checkbox',
					'heading' 		=> esc_html__( 'Hide on Tablet ?', 'codevz' ),
					'param_name' 	=> 'hide_on_t',
					'edit_field_class' => 'vc_col-xs-6',
					'group' 		=> esc_html__( 'Advanced', 'codevz' )
				), 
				array(
					'type' 			=> 'checkbox',
					'heading' 		=> esc_html__( 'Hide on Mobile ?', 'codevz' ),
					'param_name' 	=> 'hide_on_m',
					'edit_field_class' => 'vc_col-xs-6',
					'group' 		=> esc_html__( 'Advanced', 'codevz' )
				),
				array(
					'type' 			=> 'cz_title',
					'param_name' 	=> 'cz_title',
					'class' 		=> '',
					'content' 		=> esc_html__( 'Parallax', 'codevz' ),
					'group' 		=> esc_html__( 'Advanced', 'codevz' )
				),
				array(
					"type"        	=> "dropdown",
					"heading"     	=> esc_html__( "Parallax", 'codevz' ),
					"param_name"  	=> "parallax_h",
					'edit_field_class' => 'vc_col-xs-99',
					'value'		=> array(
						esc_html__( 'Select', 'codevz' )					=> '',
						
						esc_html__( 'Vertical', 'codevz' )					=> 'v',
						esc_html__( 'Vertical + Mouse parallax', 'codevz' )		=> 'vmouse',
						esc_html__( 'Horizontal', 'codevz' )				=> 'true',
						esc_html__( 'Horizontal + Mouse parallax', 'codevz' )	=> 'truemouse',
						esc_html__( 'Mouse parallax', 'codevz' )				=> 'mouse',
					),
					"group"  		=> esc_html__( 'Advanced', 'codevz' )
				),
				array(
					"type"        	=> "cz_slider",
					"heading"     	=> esc_html__( "Parallax speed", 'codevz' ),
					"description"   => esc_html__( "Parallax is according to page scrolling", 'codevz' ),
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"  	=> "parallax",
					"value"  		=> "0",
					'options' 		=> array( 'unit' => '', 'step' => 1, 'min' => -50, 'max' => 50 ),
					'dependency'	=> array(
						'element'		=> 'parallax_h',
						'value'			=> array( 'v', 'vmouse', 'true', 'truemouse' )
					),
					"group"  		=> esc_html__( 'Advanced', 'codevz' )
				),
				array(
					"type"        	=> "cz_slider",
					"heading"     	=> esc_html__("Mouse speed", 'codevz'),
					"description"   => esc_html__( "Mouse parallax is according to mouse move", 'codevz' ),
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"  	=> "mparallax",
					"value"  		=> "0",
					'options' 		=> array( 'unit' => '', 'step' => 1, 'min' => -30, 'max' => 30 ),
					'dependency'	=> array(
						'element'		=> 'parallax_h',
						'value'			=> array( 'vmouse', 'truemouse', 'mouse' )
					),
					"group"  		=> esc_html__( 'Advanced', 'codevz' )
				),
				array(
					'type' 			=> 'cz_title',
					'param_name' 	=> 'cz_title',
					'class' 		=> '',
					'content' 		=> esc_html__( 'Animation & Class', 'codevz' ),
					'group' 		=> esc_html__( 'Advanced', 'codevz' )
				),
				vc_map_add_css_animation( false ),
				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'sk_brfx',
					"heading"     	=> esc_html__( "Block Reveal", 'codevz'),
					'button' 		=> esc_html__( "Block Reveal", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99 hidden',
					'group' 	=> esc_html__( 'Advanced', 'codevz' ),
					'settings' 		=> array( 'background' )
				),
				array(
					"type"        	=> "cz_slider",
					"heading"     	=> esc_html__("Animation Delay", 'codevz'),
					"description" 	=> 'e.g. 500ms',
					"param_name"  	=> "anim_delay",
					'options' 		=> array( 'unit' => 'ms', 'step' => 100, 'min' => 0, 'max' => 5000 ),
					'edit_field_class' => 'vc_col-xs-6',
					'group' 		=> esc_html__( 'Advanced', 'codevz' )
				),
				array(
					"type"        	=> "textfield",
					"heading"     	=> esc_html__( "Extra Class", 'codevz' ),
					"param_name"  	=> "class",
					'edit_field_class' => 'vc_col-xs-6',
					'group' 		=> esc_html__( 'Advanced', 'codevz' )
				),

			)
		));
	}

	/**
	 *
	 * Shortcode output
	 * 
	 * @return string
	 * 
	 */
	public function out( $atts, $content = '' ) {
		$atts = vc_map_get_attributes( $this->name, $atts );

		// Script
		wp_enqueue_script( 'codevz-countdown' );

		// ID
		if ( ! $atts['id'] ) {
			$atts['id'] = Codevz_Plus::uniqid();
			$public = 1;
		}

		// Data
		$data = array(
			'date'	=> ( $atts['type'] === 'loop' ) ? $atts['loop'] * 60 : strtotime( $atts['date'] ) - strtotime( current_time( 'Y/m/j H:i' ) ),
			'elapse'=> ( $atts['type'] === 'up' ) ? true : false,
			'y'		=> ( $atts['type'] === 'up' ) ? $atts['year'] : '',
			'd'		=> $atts['day'],
			'h'		=> $atts['hour'],
			'm'		=> $atts['minute'],
			's'		=> $atts['second'],
			'p'		=> $atts['plus'] ? $atts['plus'] : '&nbsp;',
			'ex'	=> $atts['expire'] ? $atts['expire'] : '&nbsp;',
		);

		// Styles
		if ( isset( $public ) || Codevz_Plus::$vc_editable || Codevz_Plus::$is_admin ) {
			$css_id = '#' . $atts['id'];
			$custom = $atts['anim_delay'] ? 'animation-delay:' . $atts['anim_delay'] . ';' : '';
			
			$css_array = array(
				'sk_overall' 	=> array( $css_id, $custom ),
				'sk_brfx' 		=> $css_id . ':before',
				'sk_cols' 		=> $css_id . ' li',
				'sk_nums' 		=> $css_id . ' span',
				'sk_title' 		=> $css_id . ' p',
				'sk_expired' 	=> $css_id . ' .expired',
			);

			$css 	= Codevz_Plus::sk_style( $atts, $css_array );
			$css_t 	= Codevz_Plus::sk_style( $atts, $css_array, '_tablet' );
			$css_m 	= Codevz_Plus::sk_style( $atts, $css_array, '_mobile' );
		} else {
			Codevz_Plus::load_font( $atts['sk_nums'] );
			Codevz_Plus::load_font( $atts['sk_title'] );
		}

		// Classes
		$classes = array();
		$classes[] = $atts['id'];
		$classes[] = 'cz_countdown clr';
		$classes[] = $atts['pos'];

		// Out
		$out = "<ul id='" . $atts['id'] . "' data-countdown='" . json_encode( $data, JSON_HEX_APOS ) . "'" . Codevz_Plus::classes( $atts, $classes ) . "></ul><div" . Codevz_Plus::data_stlye( $css, $css_t, $css_m ) . "></div>";

		return Codevz_Plus::_out( $atts, $out, 'countdown' );
	}
}