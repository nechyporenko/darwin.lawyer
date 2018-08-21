<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.

/**
 *
 * Inforgraphic Icons
 * 
 * @author Codevz
 * @copyright Codevz
 * @link http://codevz.com/
 * 
 */
class CDVZ_inforgraphic_icons extends Codevz_Plus {

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
			'deprecated' 	=> '4.6',
			'base'			=> $this->name,
			'name'			=> esc_html__( 'Inforgraphic Icons', 'codevz' ),
			'description'	=> esc_html__( 'Progress bar of icons', 'codevz' ),
			'icon'			=> 'czi',
			"weight"         => CDVZ_VC_WEIGHT,
			'params'		=> array(
				array(
					'type' 			=> 'cz_sc_id',
					'param_name' 	=> 'id',
					'save_always' 	=> true
				),
				array(
					"type"        	=> "cz_slider",
					"heading"     	=> esc_html__("Number & Unit", 'codevz'),
					"param_name"  	=> "number",
					"value"  		=> "85%",
					'edit_field_class' => 'vc_col-xs-99',
					'options' 		=> array( 'unit' => '%', 'step' => 1, 'min' => 0, 'max' => 100 ),
					'admin_label' 	=> true
				),
				array(
					"type"        	=> "cz_icon",
					"heading"     	=> esc_html__("Icon", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"  	=> "icon",
					"value"			=> "fa fa-user",
					'admin_label' 	=> true
				),
				array(
					"type"        	=> "colorpicker",
					"heading"     	=> esc_html__("Color", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"  	=> "icon_color",
					"value"  		=> "#cccccc"
				),
				array(
					"type"        	=> "cz_icon",
					"heading"     	=> esc_html__("Progressed icon", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"  	=> "icon_2",
					"value"			=> "fa fa-user"
				),
				array(
					"type"        	=> "colorpicker",
					"heading"     	=> esc_html__("Progressed color", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"  	=> "icon_2_color",
					"value"  		=> "#111111"
				),

				array(
					"type"        	=> "cz_slider",
					"heading"     	=> esc_html__("Size", 'codevz'),
					"value"   		=> "20px",
					"param_name"  	=> "font-size",
					'edit_field_class' => 'vc_col-xs-99'
				),
				array(
					"type"        	=> "cz_slider",
					"heading"     	=> esc_html__("Count of icons", 'codevz'),
					"value"   		=> "6",
					'options' 		=> array( 'unit' => 'px', 'step' => 1, 'min' => 1, 'max' => 30 ),
					"param_name"  	=> "number_of_icons",
					'edit_field_class' => 'vc_col-xs-99'
				),

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

		$number_of_icons = (int) $atts['number_of_icons'];

		$icon_color = $atts['icon_color'] ? ' style="color: ' . $atts['icon_color'] . '"' : '';
		$icon = '<i class="' . $atts['icon'] . '"' . $icon_color . '></i>';
		$icon = str_repeat( $icon, $number_of_icons );

		$icon_2_color = $atts['icon_2_color'] ? ' style="color: ' . $atts['icon_2_color'] . '"' : '';
		$icon_2 = '<i class="' . $atts['icon_2'] . '"' . $icon_2_color . '></i>';
		$icon_2 = str_repeat( $icon_2, $number_of_icons );

		$font_size = $atts['font-size'] ? ' style="font-size: ' . $atts['font-size'] . '"' : '';

		// Output
		$out = '<div' . Codevz_Plus::classes( $atts, array( 'cz_progress_bar_icon' ) ) . ' data-number="' . $atts['number'] . '"' . $font_size . '>' . $icon . '<div><div>' . $icon_2 . '</div></div></div>';

		return Codevz_Plus::_out( $atts, $out, 'progress_bar' );
	}

}