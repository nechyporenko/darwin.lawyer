<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.

/**
 *
 * Stylish UI List
 * 
 * @author Codevz
 * @copyright Codevz
 * @link http://codevz.com/
 * 
 */
class CDVZ_stylish_list extends Codevz_Plus {

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
			'name'			=> esc_html__( 'Stylish List', 'codevz' ),
			'description'	=> esc_html__( 'Custom list with icon', 'codevz' ),
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
					'heading' => esc_html__( 'Lists', 'codevz' ),
					'param_name' => 'items',
					'params' => array(
						array(
							'type' 			=> 'textfield',
							'heading' 		=> esc_html__( 'Title', 'codevz' ),
							'param_name' 	=> 'title',
							'value'			=> 'This is list item',
							'edit_field_class' => 'vc_col-xs-99',
							'admin_label'	=> true
						),
						array(
							'type' 			=> 'textfield',
							'heading' 		=> esc_html__( 'Subtitle', 'codevz' ),
							'edit_field_class' => 'vc_col-xs-99',
							'param_name' 	=> 'subtitle',
						),
						array(
							"type"        	=> "cz_icon",
							"heading"     	=> esc_html__("Icon", 'codevz'),
							'edit_field_class' => 'vc_col-xs-99',
							"param_name"  	=> "icon",
							//'value'			=> 'fa fa-check'
						),
						array(
							'type' 			=> 'vc_link',
							'heading' 		=> esc_html__( 'Link', 'codevz' ),
							'edit_field_class' => 'vc_col-xs-99',
							'param_name' 	=> 'link',
						),
					),
				),
				array(
					"type"        	=> "dropdown",
					"heading"     	=> esc_html__("List type", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"  	=> "list_style",
					'value'		=> array(
						'Default' 			=> 'none;margin: 0',
						'circle' 			=> 'circle',
						'decimal' 			=> 'decimal',
						'decimal-leading-zero' => 'decimal-leading-zero',
						'disc' 				=> 'disc',
						'persian' 			=> 'persian',
						'lower-roman' 		=> 'lower-roman',
						'upper-alpha' 		=> 'upper-alpha',
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
					'param_name' 	=> 'sk_overall',
					"heading"     	=> esc_html__( "Container", 'codevz'),
					'button' 		=> esc_html__( "Container", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'settings' 		=> array( 'background', 'padding', 'border' )
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_overall_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_overall_mobile' ),

				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'sk_lists',
					'hover_id'	 	=> 'sk_lists_hover',
					"heading"     	=> esc_html__( "List items", 'codevz'),
					'button' 		=> esc_html__( "List items", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'settings' 		=> array( 'color', 'width', 'float', 'display', 'font-size' )
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_lists_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_lists_mobile' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_lists_hover' ),

				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'sk_subtitle',
					"heading"     	=> esc_html__( "Subtitle", 'codevz'),
					'button' 		=> esc_html__( "Subtitle", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'settings' 		=> array( 'color', 'font-size' )
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_subtitle_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_subtitle_mobile' ),

				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'sk_icons',
					'hover_id'	 	=> 'sk_icons_hover',
					"heading"     	=> esc_html__( "Icons", 'codevz'),
					'button' 		=> esc_html__( "Icons", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'settings' 		=> array( 'color', 'font-size', 'background', 'border' )
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_icons_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_icons_mobile' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_icons_hover' ),

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

			$custom = $atts['list_style'] ? 'list-style-type: ' . $atts['list_style'] . ';' : '';
			$custom .= $atts['anim_delay'] ? 'animation-delay:' . $atts['anim_delay'] . ';' : '';
			$css = $custom ? $css_id . '{' . $custom . '}' : '';

			$css_array = array(
				'sk_overall' 		=> $css_id,
				'sk_brfx' 			=> $css_id . ':before',
				'sk_lists' 			=> $css_id . ' li',
				'sk_lists_hover' 	=> $css_id . ' li:hover',
				'sk_subtitle' 		=> $css_id . ' small',
				'sk_icons' 			=> $css_id . ' i',
				'sk_icons_hover' 	=> $css_id . ' li:hover i'
			);

			$css 	.= Codevz_Plus::sk_style( $atts, $css_array );
			$css_t 	= Codevz_Plus::sk_style( $atts, $css_array, '_tablet' );
			$css_m 	= Codevz_Plus::sk_style( $atts, $css_array, '_mobile' );

			if ( Codevz_Plus::contains( $atts['sk_lists'], 'text-align:center' ) ) {
				$css .= $css_id . ' li > div {display: inline-block}';
			}

		} else {
			Codevz_Plus::load_font( $atts['sk_lists'] );
			Codevz_Plus::load_font( $atts['sk_subtitle'] );
		}

		// Classes
		$classes = array();
		$classes[] = $atts['id'];
		$classes[] = 'cz_stlylish_list clr';
		$classes[] = ( $atts['list_style'] && $atts['list_style'] !== 'none;margin: 0' ) ? 'cz_sl_list_item' : '';

		// Out
		$out = '<ul id="' . $atts['id'] . '"' . Codevz_Plus::classes( $atts, $classes ) . Codevz_Plus::data_stlye( $css, $css_t, $css_m ) . '>';
		$items = (array) vc_param_group_parse_atts( $atts['items'] );
		foreach ( $items as $i ) {
			$ico = empty( $i['icon'] ) ? '' : '<div class="cz_sl_icon"><i class="' . $i['icon'] . ' mr8"></i></div>';
			$sub = empty( $i['subtitle'] ) ? '' : '<small>' . $i['subtitle'] . '</small>';
			$link = empty( $i['link'] ) ? '' : '<a' . Codevz_Plus::link_attrs( $i['link'] ) . '>';
			$out .= empty( $i['title'] ) ? '' : '<li class="clr">' . $link . $ico . '<div><span>' . $i['title'] . $sub . '</span></div>' . ( $link ? '</a>' : '' ) . '</li>';
		}
		$out .= '</ul>';

		return Codevz_Plus::_out( $atts, $out );
	}
}