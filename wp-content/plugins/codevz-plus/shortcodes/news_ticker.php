<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.

/**
 *
 * News Ticker
 * 
 * @author Codevz
 * @copyright Codevz
 * @link http://codevz.com/
 * 
 */
class CDVZ_news_ticker extends Codevz_Plus {

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

		add_filter( 'vc_autocomplete_cz_news_ticker_cat_callback', array( $this, 'vc_autocomplete_taxonomies_search' ), 10, 1 );
		add_filter( 'vc_autocomplete_cz_news_ticker_cat_render', array( $this, 'vc_autocomplete_taxonomies_render' ), 10, 1 );

		add_filter( 'vc_autocomplete_cz_news_ticker_tag_id_callback', array( $this, 'vc_autocomplete_taxonomies_search' ), 10, 1 );
		add_filter( 'vc_autocomplete_cz_news_ticker_tag_id_render', array( $this, 'vc_autocomplete_taxonomies_render' ), 10, 1 );

		Codevz_Plus::vc_map( array(
			'category'		=> CDVZ_VC_CAT,
			'base'			=> $this->name,
			'name'			=> esc_html__( 'News Ticker', 'codevz' ),
			'description'	=> esc_html__( 'News ticker slider', 'codevz' ),
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
					'edit_field_class' => 'vc_col-xs-99',
					'param_name' 	=> 'type',
					'value'		=> array(
						'Slide' 	=> 'slider',
						'Fade' 		=> 'fade',
						'Vertical' 	=> 'vertical',
					)
				),
				array(
					"type"        	=> "textfield",
					"heading"     	=> esc_html__("Badge", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"  	=> "badge_title",
					"value"  		=> "TRENDING",
					'admin_label' 	=> true
				),
				array(
					"type"        	=> "cz_slider",
					"heading"     	=> esc_html__("Auto play seconds", 'codevz'),
					"value"  		=> '4',
					'options' 		=> array( 'unit' => '', 'step' => 1, 'min' => 1, 'max' => 10 ),
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"  	=> "speed"
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
					'settings' 		=> array( 'background', 'padding', 'border' )
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_con_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_con_mobile' ),

				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'sk_badge',
					"heading"     	=> esc_html__( "Badge", 'codevz'),
					'button' 		=> esc_html__( "Badge", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'settings' 		=> array( 'position', 'top', 'right', 'bottom', 'left', 'color', 'font-family', 'font-size', 'background', 'padding', 'border' )
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_badge_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_badge_mobile' ),

				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'sk_links',
					"heading"     	=> esc_html__( "Links", 'codevz'),
					'button' 		=> esc_html__( "Links", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'settings' 		=> array( 'color', 'font-size' )
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_links_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_links_mobile' ),

				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'sk_meta',
					"heading"     	=> esc_html__( "Meta", 'codevz'),
					'button' 		=> esc_html__( "Meta", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'settings' 		=> array( 'color', 'font-size', 'background', 'border' )
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_meta_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_meta_mobile' ),

				array(
					'type' 			=> 'cz_sk',
					'param_name' 	=> 'sk_arrows',
					'hover_id'	 	=> 'sk_arrows_hover',
					"heading"     	=> esc_html__( "Arrows", 'codevz'),
					'button' 		=> esc_html__( "Arrows", 'codevz'),
					'edit_field_class' => 'vc_col-xs-99',
					'settings' 		=> array( 'color', 'font-size', 'background', 'border' )
				),
				array( 'type' => 'cz_hidden','param_name' => 'sk_arrows_tablet' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_arrows_mobile' ),
				array( 'type' => 'cz_hidden','param_name' => 'sk_arrows_hover' ),

				// Filter
				array(
					'type' 			=> 'cz_slider',
					'heading' 		=> esc_html__('Posts count', 'codevz'),
					'options' 		=> array( 'unit' => '', 'step' => 1, 'min' => 1, 'max' => 30 ),
					'param_name' 	=> 'posts_per_page',
					'edit_field_class' => 'vc_col-xs-99',
					'group' 		=> esc_html__( 'Filter', 'codevz' )
				),
				array(
					"type"			=> "dropdown",
					"heading"		=> esc_html__("Order", "codevz"),
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"	=> "order",
					"value"			=> array(
						__("Descending", "codevz") => 'DESC',
						__("Ascending", "codevz") => 'ASC',
					),
					'group' 		=> esc_html__( 'Filter', 'codevz' )
				), 
				array(
					"type"			=> "dropdown",
					"heading"		=> esc_html__("Orderby", "codevz"),
					'edit_field_class' => 'vc_col-xs-99',
					"param_name"	=> "orderby",
					"value"			=> array(
						__("Date", "codevz")	=> 'date',
						__("ID", "codevz")		=> 'ID',
						__("Random", "codevz") => 'rand',
						__("Author", "codevz") => 'author',
						__("Title", "codevz")	=> 'title',
						__("Name", "codevz")	=> 'name',
						__("Type", "codevz")	=> 'type',
						__("Modified", "codevz") => 'modified',
						__("Parent ID", "codevz") => 'parent',
						__("Comment Count", "codevz") => 'comment_count',
					),
					'group' 		=> esc_html__( 'Filter', 'codevz' )
				),
				array(
					'type' 			=> 'autocomplete',
					'heading' 		=> esc_html__('Category(s)', 'codevz'),
					'settings' 		=> array(
						'multiple'		=> true,
						'save_always'	=> true,
						'sortable' 		=> true,
						'groups' 		=> true,
						'unique_values' => true,
					),
					'edit_field_class' => 'vc_col-xs-99',
					'param_name' 	=> 'cat',
					'group' 		=> esc_html__( 'Filter', 'codevz' )
				),
				array(
					'type' 			=> 'autocomplete',
					'heading' 		=> esc_html__('Tag', 'codevz'),
					'settings' 		=> array(
						'multiple'		=> false,
						'save_always'	=> true,
					),
					'edit_field_class' => 'vc_col-xs-99',
					'param_name' 	=> 'tag_id',
					'group' 		=> esc_html__( 'Filter', 'codevz' )
				),
				array(
					'type' 			=> 'textfield',
					'heading' 		=> esc_html__('Search keyword', 'codevz'),
					'param_name' 	=> 's',
					'edit_field_class' => 'vc_col-xs-99',
					'group' 		=> esc_html__( 'Filter', 'codevz' )
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

			$css_array = array(
				'sk_brfx' 			=> $css_id . ':before',
				'sk_con' 			=> $css_id . ' .cz_ticker',
				'sk_badge' 			=> $css_id . ' .cz_ticker_badge',
				'sk_links' 			=> $css_id . ' a',
				'sk_meta' 			=> $css_id . ' small',
				'sk_arrows' 		=> $css_id . ' button',
				'sk_arrows_hover' 	=> $css_id . ' button:hover',
			);

			$css 	= Codevz_Plus::sk_style( $atts, $css_array );
			$css_t 	= Codevz_Plus::sk_style( $atts, $css_array, '_tablet' );
			$css_m 	= Codevz_Plus::sk_style( $atts, $css_array, '_mobile' );

			$css .= $atts['anim_delay'] ? $css_id . '{animation-delay:' . $atts['anim_delay'] . '}' : '';

		} else {
			Codevz_Plus::load_font( $atts['sk_badge'] );
			Codevz_Plus::load_font( $atts['sk_links'] );
			Codevz_Plus::load_font( $atts['sk_meta'] );
		}

		// Slick Slider
		$speed = (int) $atts['speed'];
		$slick = array(
			'slidesToShow'		=> 1, 
			'slidesToScroll'	=> 1, 
			'fade'				=> false, 
			'vertical'			=> false, 
			'infinite'			=> true, 
			'speed'				=> 1000, 
			'autoplay'			=> true, 
			'autoplaySpeed'		=> $speed . '000', 
			'dots'				=> false,
			'prevArrow'			=> '<button type="button" class="slick-prev"><i class="fa fa-angle-left"></i></button>',
			'nextArrow'			=> '<button type="button" class="slick-next"><i class="fa fa-angle-right"></i></button>',
		);

		if ( $atts['type'] === 'slider' ) {
			$slick = ' data-slick=\'' . json_encode(array_merge( $slick, array() )) . '\'';
		} else if ( $atts['type'] === 'vertical' ) {
			$slick = ' data-slick=\'' . json_encode(array_merge( $slick, array( 'verticalSwiping' => true, 'vertical' => true ) )) . '\'';
		} else {
			$slick = ' data-slick=\'' . json_encode(array_merge( $slick, array( 'fade' => true ) )) . '\'';
		}

		// Classes
		$classes = array();
		$classes[] = 'cz_ticker arrows_tr arrows_inner';
		
		// Out
		$out = '<div id="' . $atts['id'] . '" class="' . $atts['id'] . ' relative clr"' . Codevz_Plus::data_stlye( $css, $css_t, $css_m ) . '>';
		$out .= $atts['badge_title'] ? '<div class="cz_ticker_badge">' . $atts['badge_title'] . '</div>' : '';
		$out .= '<div' . Codevz_Plus::classes( $atts, $classes ) . $slick . '>';

		$atts['post_type'] = 'post';

		$q = new WP_Query( array_filter( $atts ) );
		if ( $q->have_posts() ) {
			while ( $q->have_posts() ) {
				$q->the_post();
				$out .= '<div class="cz_news_ticker_post"><a href="' . get_the_permalink() . '">' . get_the_title() . '</a> <small>' . get_the_date() . '</small></div>';
			}
		}
		wp_reset_postdata();
		$out .= '</div></div>';

		wp_enqueue_script( 'codevz-slick' );
		return Codevz_Plus::_out( $atts, $out, 'slick' );
	}

}