<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.

/**
 *
 * Image
 * 
 * @author Codevz
 * @copyright Codevz
 * @link http://codevz.com/
 * 
 */
class CDVZ_image extends Codevz_Plus {

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
			'name'			=> esc_html__( 'Image', 'codevz' ), 
			'description'	=> esc_html__( 'Customizable image', 'codevz' ),
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
					"heading"     	=> esc_html__("Image", 'codevz'),
					"param_name"  	=> "image",
					"edit_field_class" => 'vc_col-xs-99',
					'admin_label' 	=> true
				),
				array(
					"type"        	=> "textfield",
					"heading"     	=> esc_html__("Image size", 'codevz'),
					"description"   => esc_html__('Enter image size (e.g: "thumbnail", "medium", "large", "full"), Alternatively enter size in pixels (e.g: 200x100 (Width x Height)).', 'codevz'),
					"value"  		=> "full",
					"param_name"  	=> "size",
					"edit_field_class" => 'vc_col-xs-99',
					'admin_label' 	=> true
				),
				array(
					"type" => "dropdown",
					"heading" => esc_html__("Image opacity","codevz"),
					"param_name" => "image_opacity",
					"edit_field_class" => 'vc_col-xs-99',
					"value" => array('100%'=>'','90%'=>'op_9','80%'=>'op_8','70%'=>'op_7','60%'=>'op_6','50%'=>'op_5','40%'=>'op_4','30%'=>'op_3','20%'=>'op_2','10%'=>'op_1','0%'=>'op_0'),
				),
				array(
					'type'			=> 'dropdown',
					'heading'		=> esc_html__('Image position', 'codevz'),
					'description'	=> esc_html__('Position will affect according to image width', 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'param_name'	=> 'css_position',
					'value'			=> array(
						'inline' 	=> 'relative',
						'block' 	=> 'relative;display: block;text-align:center',
						'Left' 		=> 'relative;float:left',
						'Center' 	=> 'relative;display: table;margin:0 auto',
						'Right' 	=> 'relative;float:right',
					)
				),
				array(
					"type"        	=> "cz_slider",
					"heading"     	=> esc_html__("Custom width", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"  	=> "css_width",
					'options' 		=> array( 'unit' => 'px', 'step' => 1, 'min' => 50, 'max' => 500 ),
					"description"   => esc_html__('e.g. 200px or 60% according to parent width', 'codevz'),
				),
				array(
					"type" => "dropdown",
					"heading" => esc_html__("Link","codevz"),
					"param_name" => "link_type",
					'edit_field_class' => 'vc_col-xs-99',
					"value" => array(
						esc_html__('None', 'codevz') 	=> 'none',
						esc_html__('Link to large image (Lightbox)', 'codevz') => 'lightbox',
						esc_html__('Custom', 'codevz') 	=> 'custom',
					)
				),
				array(
					"type"        	=> "vc_link",
					"heading"     	=> esc_html__("Link", 'codevz'),
					"param_name"  	=> "link",
					'edit_field_class' => 'vc_col-xs-99',
					'dependency'	=> array(
						'element'		=> 'link_type',
						'value'			=> array( 'custom')
					)
				),

				array(
					"type"        	=> "attach_image",
					"heading"     	=> esc_html__("Hover Image", 'codevz'),
					"param_name"  	=> "hover_image",
					"edit_field_class" => 'vc_col-xs-99',
					'group' 		=> esc_html__( 'Hover', 'codevz' )
				),
				array(
					"type" => "dropdown",
					"heading" => esc_html__("Hover opacity","codevz"),
					"param_name" => "hover_opacity",
					"edit_field_class" => 'vc_col-xs-99',
					"value" => array('100%'=>'op_100','90%'=>'op_9','80%'=>'op_8','70%'=>'op_7','60%'=>'op_6','50%'=>'op_5','40%'=>'op_4','30%'=>'op_3','20%'=>'op_2','10%'=>'op_1','0%'=>'op_0'),
					'group' 		=> esc_html__( 'Hover', 'codevz' ),
					'dependency'	=> array(
						'element'		=> 'hover_image',
						'not_empty'		=> true
					)
				),
				array(
					'type' 			=> 'dropdown',
					'heading' 		=> esc_html__( 'Hover effect', 'codevz' ),
					'param_name' 	=> 'fx_hover',
					"edit_field_class" => 'vc_col-xs-99',
					'value'			=> array(
						__( 'Simple Fade', 'codevz') 		=> '',
						__( 'Flip Horizontal', 'codevz') 	=> 'cz_image_flip_h',
						__( 'Flip Vertical', 'codevz') 		=> 'cz_image_flip_v',
						__( 'Fade To Top', 'codevz') 		=> 'cz_image_fade_to_top',
						__( 'Fade To Bottom', 'codevz') 	=> 'cz_image_fade_to_bottom',
						__( 'Fade To Left', 'codevz') 		=> 'cz_image_fade_to_left',
						__( 'Fade To Right', 'codevz') 		=> 'cz_image_fade_to_right',
						__( 'Zoom In', 'codevz') 			=> 'cz_image_zoom_in',
						__( 'Zoom Out', 'codevz') 			=> 'cz_image_zoom_out',
						__( 'Blurred', 'codevz') 			=> 'cz_image_blurred',
					),
					'group' 		=> esc_html__( 'Hover', 'codevz' ),
					'dependency'	=> array(
						'element'		=> 'hover_image',
						'not_empty'		=> true
					)
				),

				array(
					'type' 			=> 'cz_title',
					'param_name' 	=> 'cz_title',
					'class' 		=> '',
					'content' 		=> esc_html__( 'Tooltip', 'codevz' ),
					'group' 		=> esc_html__( 'Hover', 'codevz' )
				),
				array(
					"type"        	=> "textfield",
					"heading"     	=> esc_html__("Tooltip", 'codevz'),
					"edit_field_class" => 'vc_col-xs-99',
					"param_name"  	=> "tooltip",
					'group' 		=> esc_html__( 'Hover', 'codevz' )
				),
				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'svg_tooltip',
					"heading"     	=> esc_html__( "Tooltip styling", 'codevz'),
					'button' 		=> esc_html__( "Tooltip", 'codevz'),
					"edit_field_class" => 'vc_col-xs-99',
					'group' 		=> esc_html__( 'Hover', 'codevz' ),
					'settings' 		=> array( 'color', 'font-size', 'background' )
				),
				array( 'type' => 'cz_hidden','param_name' => 'svg_bg_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'svg_bg_mobile' ),

				array(
					'type' 			=> 'cz_title',
					'param_name' 	=> 'cz_title',
					'class' 		=> '',
					'content' 		=> esc_html__( 'Styling', 'codevz' )
				),
				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'sk_css',
					'hover_id'	 	=> 'sk_css_hover',
					"heading"     	=> esc_html__( "Image styling", 'codevz'),
					'button' 		=> esc_html__( "Image", 'codevz'),
					"edit_field_class" => 'vc_col-xs-99',
					'settings' 		=> array( 'background', 'padding', 'border', 'box-shadow' )
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_css_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_css_mobile' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_css_hover' ),
				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'svg_bg',
					"heading"     	=> esc_html__( "Background layer", 'codevz'),
					'button' 		=> esc_html__( "Background layer", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'settings' 		=> array( 'svg', 'top', 'left', 'width', 'height' )
				),
				array( 'type' => 'cz_hidden','param_name' => 'svg_bg_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'svg_bg_mobile' ),

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
					'content' 		=> esc_html__( 'Tilt effect on hover', 'codevz' ),
					'group' 		=> esc_html__( 'Advanced', 'codevz' )
				),
				array(
					"type"        	=> "dropdown",
					"heading"     	=> esc_html__("Tilt effect", 'codevz'),
					"param_name"  	=> "tilt",
					'edit_field_class' => 'vc_col-xs-99',
					'value'		=> array(
						'Off'	=> '',
						'On'	=> 'on',
					),
					"group"  		=> esc_html__( 'Advanced', 'codevz' )
				),
				 array(
					"type" => "dropdown",
					"heading" => esc_html__("Glare","codevz"),
					"param_name" => "glare",
					"edit_field_class" => 'vc_col-xs-99',
					"value" => array( '0','0.2','0.4','0.6','0.8','1' ),
					'dependency'	=> array(
						'element'		=> 'tilt',
						'value'			=> array( 'on')
					),
					"group"  		=> esc_html__( 'Advanced', 'codevz' )
				),
				array(
					"type" => "dropdown",
					"heading" => esc_html__("Scale","codevz"),
					"param_name" => "scale",
					"edit_field_class" => 'vc_col-xs-99',
					"value" 	=> array('0.9','0.8','1','1.1','1.2'),
					"std" 		=> '1',
					'dependency'	=> array(
						'element'		=> 'tilt',
						'value'			=> array( 'on')
					),
					"group"  		=> esc_html__( 'Advanced', 'codevz' )
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

		// Fix hover
		//if ( ! $atts['hover_image'] ) {
			//$atts['hover_image'] = $atts['image'];
		//}

		// Images
		$image = Codevz_Plus::get_image( $atts['image'], $atts['size'] );
		$hover_image = $atts['hover_image'] ? Codevz_Plus::get_image( $atts['hover_image'], $atts['size'] ) : '' ;

		// Link
		$a_before = $a_after = '';
		if ( $atts['link_type'] === 'lightbox' ) {
			$link = Codevz_Plus::get_image( $atts['image'], 0, 1 );
			$a_before = '<a href="' . $link . '" >';
			$a_after = '</a>';
		} else if ( $atts['link_type'] === 'custom' ) {
	 		$a_before .= '<a'. Codevz_Plus::link_attrs( $atts['link'] ) . '>';
	 		$a_after = '</a>';
		}

		// Tooltip
		$tooltip = $atts['tooltip'] ? ' data-tooltip="' . $atts['tooltip'] . '"' : '';

		// Styles
		if ( isset( $public ) || Codevz_Plus::$vc_editable || Codevz_Plus::$is_admin ) {
			$css_id = '#' . $atts['id'];

			$css_array = array(
				'sk_brfx' 		=> $css_id . ':before',
				'sk_css' 		=> $css_id . ' .cz_image_in',
				'sk_css_hover' 	=> $css_id . ':hover .cz_image_in',
				'svg_bg' 		=> $css_id . ' .cz_svg_bg:before',
				'svg_tooltip' 	=> $css_id . '[data-tooltip]:before'
			);

			$css 	= Codevz_Plus::sk_style( $atts, $css_array );
			$css_t 	= Codevz_Plus::sk_style( $atts, $css_array, '_tablet' );
			$css_m 	= Codevz_Plus::sk_style( $atts, $css_array, '_mobile' );

			$custom = $atts['css_width'] ? 'width:' . $atts['css_width'] . ';' : '';
			$custom .= $atts['css_position'] ? 'position:' . $atts['css_position'] . ';' : '';
			$css .= $custom ? $css_id . ' > div{' . $custom . '}' : '';
		}

		// Hover
		$hover_image_tag = $hover_image ? '<div class="cz_hover_image cz_'.$atts['hover_opacity'].'">' .$a_before . $hover_image . $a_after .'</div>' : '';

		// Out
		$out = '<div id="' . $atts['id'] . '"' . Codevz_Plus::classes( $atts, array( $atts['id'], 'cz_image', $atts['fx_hover'] ) ) . $tooltip . Codevz_Plus::data_stlye( $css, $css_t, $css_m ) . '><div class="' . ( $atts['svg_bg'] ? 'cz_svg_bg' : '' ) . '"><div class="cz_image_in"' . Codevz_Plus::tilt( $atts ) . '><div class="cz_main_image cz_'.$atts['image_opacity'].'">' . $a_before . $image . $a_after . '</div>' . $hover_image_tag . '</div></div></div>';

		return Codevz_Plus::_out( $atts, $out, 'tilt' );
	}
}