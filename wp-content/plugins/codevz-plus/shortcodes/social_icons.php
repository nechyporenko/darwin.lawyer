<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.

/**
 *
 * Social Icons
 * 
 * @author Codevz
 * @copyright Codevz
 * @link http://codevz.com/
 * 
 */
class CDVZ_social_icons extends Codevz_Plus {

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
			'name'			=> esc_html__( 'Icon', 'codevz' ),
			'description'	=> esc_html__( 'Icon(s) or Social icons', 'codevz' ),
			'icon'			=> 'czi',
			"weight"         => CDVZ_VC_WEIGHT,
			'params'		=> array(
				array(
					'type' 			=> 'cz_sc_id',
					'param_name' 	=> 'id',
					'save_always' 	=> true
				),
				array(
					'type' => 'param_group',
					'heading' => esc_html__( 'Add icon(s)', 'codevz' ),
					'param_name' => 'social',
					'params' => array(
						array(
							"type"        	=> "cz_icon",
							"heading"     	=> esc_html__("Icon", 'codevz'),
							'edit_field_class' => 'vc_col-xs-99',
							"param_name"  	=> "icon",
							'value' 		=> 'fa fa-facebook'
						),
						array(
							"type"        	=> "textfield",
							"heading"     	=> esc_html__("Title", 'codevz'),
							'edit_field_class' => 'vc_col-xs-99',
							"param_name"  	=> "title",
							'admin_label'	=> true
						),
						array(
							"type"        	=> "textfield",
							"heading"     	=> esc_html__("Link", 'codevz'),
							'edit_field_class' => 'vc_col-xs-99',
							"param_name"  	=> "link"
						)
					)
				),
				array(
					'type' 			=> 'cz_title',
					'param_name' 	=> 'cz_title',
					'class' 		=> '',
					'content' 		=> esc_html__( 'Settings', 'codevz' ),
				),
				array(
					'type'			=> 'dropdown',
					'heading'		=> esc_html__('Position', 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'param_name'	=> 'position',
					'value'			=> array(
						__( 'Left', 'codevz' ) => 'tal',
						__( 'Center', 'codevz' ) => 'tac',
						__( 'Right', 'codevz' ) => 'tar',
					)
				),
				array(
					'type'			=> 'dropdown',
					'heading'		=> esc_html__('Tooltip?', 'codevz'),
					'param_name'	=> 'tooltip',
					'edit_field_class' => 'vc_col-xs-99',
					'value'			=> array(
						__( 'Select', 'codevz' ) 	=> '',
						__( 'Up', 'codevz' ) 		=> 'cz_tooltip cz_tooltip_up',
						__( 'Down', 'codevz' ) 		=> 'cz_tooltip cz_tooltip_down',
						__( 'Left', 'codevz' ) 		=> 'cz_tooltip cz_tooltip_left',
						__( 'Right', 'codevz' ) 	=> 'cz_tooltip cz_tooltip_right',
					),
				),
				array(
					'type'			=> 'dropdown',
					'heading'		=> esc_html__('Hover effect', 'codevz'),
					'param_name'	=> 'fx',
					'edit_field_class' => 'vc_col-xs-99',
					'value'			=> array(
						__( 'Select', 'codevz' ) 			=> '',
						__( 'ZoomIn', 'codevz' ) 			=> 'cz_social_fx_0',
						__( 'ZoomOut', 'codevz' ) 			=> 'cz_social_fx_1',
						__( 'Bottom to Top', 'codevz' ) 	=> 'cz_social_fx_2',
						__( 'Top to Bottom', 'codevz' ) 	=> 'cz_social_fx_3',
						__( 'Left to Right', 'codevz' ) 	=> 'cz_social_fx_4',
						__( 'Right to Left', 'codevz' ) 	=> 'cz_social_fx_5',
						__( 'Rotate', 'codevz' ) 			=> 'cz_social_fx_6',
						__( 'Infinite Shake', 'codevz' )	=> 'cz_social_fx_7',
						__( 'Infinite Wink', 'codevz' ) 	=> 'cz_social_fx_8',
						__( 'Quick Bob', 'codevz' ) 		=> 'cz_social_fx_9',
						__( 'Flip Horizontal', 'codevz' ) 	=> 'cz_social_fx_10',
						__( 'Flip Vertical', 'codevz' ) 	=> 'cz_social_fx_11',
					),
				),
				array(
					'type'			=> 'checkbox',
					'heading'		=> esc_html__('Inline title?', 'codevz'),
					'param_name'	=> 'inline_title',
					'edit_field_class' => 'vc_col-xs-99',
				),
				array(
					'type'			=> 'dropdown',
					'heading'		=> esc_html__('Social icons color', 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'param_name'	=> 'color_mode',
					'value'			=> array(
						__( 'Select', 'codevz' ) 						=> '',
						__( 'Original colors', 'codevz' ) 				=> 'cz_social_colored',
						__( 'Original colors on hover', 'codevz' ) 		=> 'cz_social_colored_hover',
						__( 'Original background', 'codevz' ) 			=> 'cz_social_colored_bg',
						__( 'Original background on hover', 'codevz' ) => 'cz_social_colored_bg_hover',
					)
				),

				array(
					'type' 			=> 'cz_title',
					'param_name' 	=> 'cz_title',
					'class' 		=> '',
					'content' 		=> esc_html__( 'Styling', 'codevz' ),
				),
				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'sk_con',
					"heading"     	=> esc_html__( "Container", 'codevz'),
					'button' 		=> esc_html__( "Container", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'settings' 		=> array( 'background', 'padding', 'border', 'box-shadow' )
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_con_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_con_mobile' ),

				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'sk_icons',
					'hover_id'	 	=> 'sk_hover',
					"heading"     	=> esc_html__( "Icons", 'codevz'),
					'button' 		=> esc_html__( "Icons", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'settings' 		=> array( 'width', 'color', 'font-size', 'background', 'border' )
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_icons_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_icons_mobile' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_hover' ),

				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'sk_title',
					"heading"     	=> esc_html__( "Inline title", 'codevz'),
					'button' 		=> esc_html__( "Inline title", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'settings' 		=> array( 'color', 'font-family', 'font-size' ),
					'dependency'	=> array(
						'element'		=> 'inline_title',
						'not_empty'		=> true
					),
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_title_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_title_mobile' ),

				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'sk_tooltip',
					"heading"     	=> esc_html__( "Tooltip", 'codevz'),
					'button' 		=> esc_html__( "Tooltip", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'settings' 		=> array( 'color', 'font-size', 'background' ),
					'dependency'	=> array(
						'element'		=> 'tooltip',
						'not_empty'		=> true
					),
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_tooltip_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_tooltip_mobile' ),

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

		// ID
		if ( ! $atts['id'] ) {
			$atts['id'] = Codevz_Plus::uniqid();
			$public = 1;
		}

		// Styles
		if ( isset( $public ) || Codevz_Plus::$vc_editable || Codevz_Plus::$is_admin ) {
			$css_id = '#' . $atts['id'];
			$custom = $atts['anim_delay'] ? 'animation-delay:' . $atts['anim_delay'] . ';' : '';

			$css_array = array(
				'sk_brfx' 	=> $css_id . ':before',
				'sk_con' 	=> array( $css_id, $custom ),
				'sk_icons' 	=> $css_id . ' a',
				'sk_hover' 	=> $css_id . ' a:hover',
				'sk_title' 	=> $css_id . ' span',
				'sk_tooltip' => $css_id . ' a:after',
			);

			$css 	= Codevz_Plus::sk_style( $atts, $css_array );
			$css_t 	= Codevz_Plus::sk_style( $atts, $css_array, '_tablet' );
			$css_m 	= Codevz_Plus::sk_style( $atts, $css_array, '_mobile' );

		} else {
			Codevz_Plus::load_font( $atts['sk_title'] );
		}

		// Title
		$inline_title = $atts['inline_title'] ? 'cz_social_inline_title' : '';

		// Social icons
		$social_icons = (array) vc_param_group_parse_atts( $atts['social'] );
		
		$social = '';
		foreach ( $social_icons as $i ) {
			if ( empty( $i['icon'] ) ) {
				continue;
			}
			$i['title'] = empty( $i['title'] ) ? '#' : $i['title'];
			$social_class = 'cz-' . str_replace( array( 'fa ', 'fa-', '-square', '-official', '-circle' ), '', $i['icon'] );
			$i['link'] = empty( $i['link'] ) ? '' : ' href="' . $i['link'] . '"';
			$social .= '<a' . $i['link'] . ' class="' . $social_class . '" target="_blank" ' . ( $atts['tooltip'] ? 'data-' : '' ) . 'title="' . $i['title'] . '"><i class="' . $i['icon'] . '">' . ( $inline_title ? '<span class="ml10">' . $i['title'] . '</span>' : '' ) . '</i></a>';
		}

		// Classes
		$classes = array();
		$classes[] = $atts['id'];
		$classes[] = 'cz_social_icons cz_social clr';
		$classes[] = $inline_title;
		$classes[] = $atts['fx'];
		$classes[] = $atts['position'];
		$classes[] = $atts['color_mode'];
		$classes[] = $atts['tooltip'];

		// Out
		$out = '<div id="' . $atts['id'] . '"' . Codevz_Plus::classes( $atts, $classes ) . Codevz_Plus::data_stlye( $css, $css_t, $css_m ) . '>' . $social . '</div>';

		return Codevz_Plus::_out( $atts, $out );
	}
}