<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.

/**
 *
 * Accordion
 * 
 * @author Codevz
 * @copyright Codevz
 * @link http://codevz.com/
 * 
 */
class CDVZ_accordion extends Codevz_Plus {

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
		add_shortcode( 'cz_acc_child', array( $this, 'acc_child' ) );

		Codevz_Plus::vc_map( array(
			'category'		=> CDVZ_VC_CAT,
			'base'			=> $this->name,
			'name'			=> esc_html__( 'Accordion, Toggle', 'codevz' ), 
			'description'	=> esc_html__( 'Show/Hide large content', 'codevz' ),
			'icon'			=> 'czi',
			"weight"        => CDVZ_VC_WEIGHT,
			'is_container' 	=> true,
			'js_view' 		=> 'VcColumnView',
			'as_parent'		=> array( 'only' => 'cz_acc_child' ), 
			'params'		=> array(
				array(
					'type' 			=> 'cz_sc_id',
					'param_name' 	=> 'id',
					'save_always' 	=> true
				),
				array(
					"type"        	=> "checkbox",
					"heading"     	=> esc_html__("Toggle mode?", 'codevz'),
					'edit_field_class' => 'vc_col-xs-3',
					"param_name"  	=> "toggle"
				),
				array(
					"type"        	=> "checkbox",
					"heading"     	=> esc_html__("1st open?", 'codevz'),
					'edit_field_class' => 'vc_col-xs-3',
					"param_name"  	=> "first_open"
				),
				array(
					"type"        	=> "checkbox",
					"heading"     	=> esc_html__("Icons before title?", 'codevz'),
					'edit_field_class' => 'vc_col-xs-3',
					"param_name"  	=> "icon_before"
				),
				array(
					'type' 			=> 'checkbox',
					'heading' 		=> esc_html__( 'Inline subtitle?', 'codevz' ),
					'edit_field_class' => 'vc_col-xs-3',
					'param_name' 	=> 'subtitle_inline'
				),

				array(
					'type' 			=> 'cz_title',
					'param_name' 	=> 'cz_title',
					'class' 		=> '',
					'content' 		=> esc_html__( 'Default and activated icons', 'codevz' ),
				),
				array(
					"type"        	=> "cz_icon",
					"heading"     	=> esc_html__("Default icon", 'codevz'),
					"description"   => esc_html__("When accordion is in close mode", 'codevz'),
					"param_name"  	=> "open_icon",
					'edit_field_class' => 'vc_col-xs-99',
					'value'			=> Codevz_Plus::$is_rtl ? 'fa fa-angle-left' : 'fa fa-angle-right'
				),
				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'sk_open_icon',
					"heading"     	=> esc_html__( "Icon styling", 'codevz'),
					'button' 		=> esc_html__( "Default icon", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'settings' 		=> array( 'color', 'font-size', 'background', 'padding' ),
					'dependency'	=> array(
						'element'		=> 'open_icon',
						'not_empty'		=> true
					),
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_open_icon_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_open_icon_mobile' ),
				array(
					"type"        	=> "cz_icon",
					"heading"     	=> esc_html__("Activated icon", 'codevz'),
					"description"   => esc_html__("When accordion is in open mode", 'codevz'),
					"param_name"  	=> "close_icon",
					'edit_field_class' => 'vc_col-xs-99',
					'value'			=> 'fa fa-angle-down'
				),
				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'sk_close_icon',
					"heading"     	=> esc_html__( "Icon styling", 'codevz'),
					'button' 		=> esc_html__( "Activated Icon", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'settings' 		=> array( 'color', 'font-size', 'background', 'padding' ),
					'dependency'	=> array(
						'element'		=> 'close_icon',
						'not_empty'		=> true
					),
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_close_icon_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_close_icon_mobile' ),

				// Styling
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
					'param_name' 	=> 'sk_title',
					"heading"     	=> esc_html__( "Titles", 'codevz'),
					'button' 		=> esc_html__( "Titles", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'settings' 		=> array( 'color', 'font-size', 'background', 'border' )
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_title_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_title_mobile' ),

				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'sk_active',
					"heading"     	=> esc_html__( "Active title", 'codevz'),
					'button' 		=> esc_html__( "Active title", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'settings' 		=> array( 'color', 'font-size', 'background', 'border' )
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_active_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_active_mobile' ),

				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'sk_subtitle',
					"heading"     	=> esc_html__( "Subtitle", 'codevz'),
					'button' 		=> esc_html__( "Subtitle", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'settings' 		=> array( 'color', 'font-size', 'background' )
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_subtitle_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_subtitle_mobile' ),

				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'sk_content',
					"heading"     	=> esc_html__( "Content", 'codevz'),
					'button' 		=> esc_html__( "Content", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'settings' 		=> array( 'color', 'font-size', 'background', 'border' )
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_content_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_content_mobile' ),

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

		Codevz_Plus::vc_map( array(
			'category'		=> CDVZ_VC_CAT,
			'base'			=> 'cz_acc_child',
			'name'			=> esc_html__( 'Accordion item', 'codevz' ), 
			'description'	=> esc_html__( 'Collapsible content, Accordion & Toggle', 'codevz' ),
			'icon'			=> 'czi',
			'is_container' 	=> true,
			'js_view'		=> 'VcColumnView',
			'content_element'=> true,
			'as_child'		=> array( 'only' => $this->name ), 
			'params'		=> array(

				array(
					"type"        	=> "textarea",
					"heading"     	=> esc_html__("Title", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"  	=> "title",
					"value"  		=> "Accordion title",
					'admin_label' 	=> true
				),
				array(
					"type"        	=> "textarea",
					"heading"     	=> esc_html__("Subtitle", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"  	=> "subtitle"
				),
				array(
					"type"        	=> "cz_icon",
					"heading"     	=> esc_html__("Icon before title", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"  	=> "icon"
				),
				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'sk_icon',
					"heading"     	=> esc_html__( "Icon styling", 'codevz'),
					'button' 		=> esc_html__( "Icon", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'settings' 		=> array( 'color', 'font-size', 'background', 'border' ),
					'dependency'	=> array(
						'element'		=> 'icon',
						'not_empty'		=> true
					),
				),
			)

		));
	}

	/**
	 *
	 * Shortcode container output
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
				'sk_overall' 	=> array( $css_id, $custom ),
				'sk_brfx' 		=> $css_id . ':before',
				'sk_title' 		=> $css_id . ' .cz_acc_child',
				'sk_subtitle' 	=> $css_id . ' .cz_acc_child small',
				'sk_active' 	=> $css_id . ' .cz_isOpen .cz_acc_child',
				'sk_content' 	=> $css_id . ' .cz_acc_child_content',
				'sk_open_icon' 	=> $css_id . ' .cz_acc_open_icon',
				'sk_close_icon' => $css_id . ' .cz_acc_close_icon',
			);

			$css 	= Codevz_Plus::sk_style( $atts, $css_array );
			$css_t 	= Codevz_Plus::sk_style( $atts, $css_array, '_tablet' );
			$css_m 	= Codevz_Plus::sk_style( $atts, $css_array, '_mobile' );

		} else {
			Codevz_Plus::load_font( $atts['sk_title'] );
			Codevz_Plus::load_font( $atts['sk_subtitle'] );
		}

		// Arrows
		$arrows = array( 'open'	=> $atts['open_icon'], 'close'	=> $atts['close_icon'] );

		// Classes
		$classes = array();
		$classes[] = $atts['id'];
		$classes[] = 'cz_acc clr';
		$classes[] = $atts['subtitle_inline'] ? 'cz_acc_subtitle_inline' : '';
		$classes[] = $atts['toggle'] ? 'cz_acc_toggle' : '';
		$classes[] = $atts['icon_before'] ? 'cz_acc_icon_before' : '';
		$classes[] = $atts['first_open'] ? 'cz_acc_first_open' : '';

		// Out
		$out = '<div id="' . $atts['id'] . '" data-arrows=\'' . json_encode( $arrows ) . '\'' . Codevz_Plus::classes( $atts, $classes ) . Codevz_Plus::data_stlye( $css, $css_t, $css_m ) . '><div>' . do_shortcode( $content ) . '</div></div>';

		return Codevz_Plus::_out( $atts, $out, 'accordion' );
	}

	/**
	 *
	 * Shortcode inner ( children ) output
	 * 
	 * @return string
	 * 
	 */
	public function acc_child( $atts, $content = '' ) {
		$atts = vc_map_get_attributes( 'cz_acc_child', $atts );

		// Icon, subtitle
		$css = $atts['sk_icon'] ? ' style="' . $atts['sk_icon'] . '"' : '';
		$icon = $atts['icon'] ? '<i class="' . $atts['icon'] . ' mr10"' . $css . '></i>' : '';
		$subtitle = $atts['subtitle'] ? '<small>' . $atts['subtitle'] . '</small>' : '';

		// Out
		$out = '<div><span class="cz_acc_child">' . $icon . $atts['title'] . $subtitle . '</span><div class="cz_acc_child_content clr">' . do_shortcode( $content ) . '</div></div>';
		
		return Codevz_Plus::_out( $atts, $out, 'accordion' );
	}

}