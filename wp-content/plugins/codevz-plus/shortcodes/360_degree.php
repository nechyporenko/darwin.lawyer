<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.

/**
 *
 * 360 Degree
 * 
 * @author Codevz
 * @copyright Codevz
 * @link http://codevz.com/
 * 
 */
class CDVZ_360_degree extends Codevz_Plus {

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
			'name'			=> esc_html__( '360 Degree', 'codevz' ),
			'description'	=> esc_html__( '360 degree rotate image', 'codevz' ),
			'icon'			=> 'czi',
			"weight"         => CDVZ_VC_WEIGHT,
			'params'		=> array(
				array(
					'type' 			=> 'cz_sc_id',
					'param_name' 	=> 'id',
					'save_always' 	=> true
				),
				array(
					"type"        	=> "attach_image",
					"heading"     	=> esc_html__("Placeholder (loading image)", 'codevz'),
					'edit_field_class' => 'vc_col-xs-6',
					"param_name"  	=> "placeholder_image",
					"value"			=> "http://xtratheme.com/img/360.jpg"
				),
				array(
					"type"        	=> "attach_image",
					"heading"     	=> esc_html__("Stripe Image", 'codevz'),
					'edit_field_class' => 'vc_col-xs-6',
					"param_name"  	=> "stripe_image",
					"value"			=> "http://xtratheme.com/img/360s.jpg"
				),
				array(
					'type' 			=> 'cz_title',
					'param_name' 	=> 'cz_title',
					'class' 		=> '',
					'content' 		=> esc_html__( 'Settings', 'codevz' ),
				),
				array(
					"type"        	=> "cz_slider",
					"heading"     	=> esc_html__("Frames Count", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"  	=> "count",
					"value"			=> "8",
					'options' 		=> array( 'unit' => '', 'step' => 1, 'min' => 1, 'max' => 40 ),
				),
				array(
					'type'			=> 'dropdown',
					'heading'		=> esc_html__('Start to rotate on:', 'codevz'),
					'param_name'	=> 'action',
					'edit_field_class' => 'vc_col-xs-99',
					'value'			=> array(
						esc_html__( 'Mouse Dragging', 'codevz' ) 	=> 'drag',
						esc_html__( 'Mouse Hover', 'codevz' ) 		=> 'hover',
					)
				),
				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'sk_con',
					"heading"     	=> esc_html__( "Container Styling", 'codevz'),
					'button' 		=> esc_html__( "Container", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'settings' 		=> array( 'background', 'padding', 'border' )
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_con_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_con_mobile' ),

				// Handle
				array(
					'type' 			=> 'cz_title',
					'param_name' 	=> 'cz_title',
					'class' 		=> '',
					'content' 		=> esc_html__( 'Handle', 'codevz' ),
				),
				array(
					"type"        	=> "checkbox",
					"heading"     	=> esc_html__("Show Handle?", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"  	=> "handle"
				),
				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'sk_handle',
					"heading"     	=> esc_html__( "Handle styling ", 'codevz'),
					'button' 		=> esc_html__( "Handle", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'dependency'	=> array(
						'element'		=> 'handle',
						'not_empty'		=> true
					),
					'settings' 		=> array( 'color', 'font-size', 'background' )
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_handle_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_handle_mobile' ),
				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'sk_bar',
					"heading"     	=> esc_html__( "Bar styling", 'codevz'),
					'button' 		=> esc_html__( "Bar", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'dependency'	=> array(
						'element'		=> 'handle',
						'not_empty'		=> true
					),
					'settings' 		=> array( 'background', 'border' )
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_bar_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_bar_mobile' ),
				
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

	public function out( $atts, $content = '' ) {
		$atts = vc_map_get_attributes( $this->name, $atts );

		// ID
		if ( ! $atts['id'] ) {
			$atts['id'] = Codevz_Plus::uniqid();
			$public = 1;
		}

		// Image
		$imgsrc = Codevz_Plus::get_image( $atts['stripe_image'], 0, 1 );
		$plc_imgsrc = Codevz_Plus::get_image( $atts['placeholder_image'], 0, 1 );

		// Count
		$count = $atts['count'] ? $atts['count'] : '16';

		// Styles
		if ( isset( $public ) || Codevz_Plus::$vc_editable || Codevz_Plus::$is_admin ) {
			
			$css_id = '#' . $atts['id'];
			
			$custom = $atts['anim_delay'] ? 'animation-delay:' . $atts['anim_delay'] . ';' : '';

			$css_array = array(
				'sk_xxx'	=> array( $css_id, $custom ),
				'sk_brfx' 	=> $css_id . ':before',
				'sk_con'	=> $css_id . ' .product-viewer',
				'sk_handle'	=> $css_id . ' .handle',
				'sk_bar'	=> $css_id . ' .cz_product-viewer-handle',
			);

			$css 	= Codevz_Plus::sk_style( $atts, $css_array );
			$css_t 	= Codevz_Plus::sk_style( $atts, $css_array, '_tablet' );
			$css_m 	= Codevz_Plus::sk_style( $atts, $css_array, '_mobile' );
		}

		// Classes
		$classes = array();
		$classes[] = $atts['id'];
		$classes[] = 'cz_product-viewer-wrapper';

		// Out
		$out ='<div id="' . $atts['id'] . '" data-frame="' . $count . '" data-friction="0.33" data-action="' . $atts['action'] . '"' . Codevz_Plus::classes( $atts, $classes ) . Codevz_Plus::data_stlye( $css, $css_t, $css_m ) . '>
				<div><figure class="product-viewer">
						<img src="' . $plc_imgsrc . '" alt="Loading">
						<div class="product-sprite" data-image="' . $imgsrc . '" style="width:' . ( $count * 100 ) . '%;background-image:url(' . $imgsrc . ')"></div>
					</figure>';

		if ( $atts['handle'] == true ) {	
			$out .='<div class="cz_product-viewer-handle"><span class="fill"></span><span class="handle"><i class="fa fa-arrows-h"></i></span></div>';
		}
		$out .='</div></div>';

		wp_enqueue_script( 'codevz-modernizer' );
		wp_enqueue_script( 'codevz-360-degree' );
		return Codevz_Plus::_out( $atts, $out, 'rotate_360_degree' );
	}

}