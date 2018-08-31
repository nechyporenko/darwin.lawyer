<?php 
/**
 *
 * Codevz Mega Menu Walker
 * 
 * @author Codevz
 * @copyright Codevz
 * @link http://codevz.com/
 *
 */
class Codevz_Menu_Add_CSF_Fields {

	public function __construct() {
		add_action( 'init', function() {
			add_filter( 'wp_edit_nav_menu_walker', function() { return 'Codevz_Walker_Nav_Menu_Edit'; });
			add_filter( 'codevz_nav_menu_csf_fields', array( __CLASS__, 'add_fields' ), 10, 5 );
			add_action( 'save_post', array( __CLASS__, 'save' ) );
		});
	}

	/**
	 *
	 * Add new fields
	 * 
	 * @return array
	 *
	 */
	public static function add_fields( $new, $item_output, $item, $depth, $args ) {

		foreach( self::options() as $field ) {
			$lvl = isset( $field['depth'] ) ? ( $field['depth'] == $depth ) : 1;
			if ( $lvl ) {
				$meta_key = $field['name'];
				$field['id'] = $meta_key;
				$field['name'] = 'menu-item-' . $field['name'] . '[' . $item->ID . ']';
				$new .= '<div class="wp-clearfix"></div>' . csf_add_field( $field, get_post_meta( $item->ID, $meta_key, true ) );
			}
		}

		return $new;
	}

	/**
	 *
	 * Save menus
	 * 
	 * @return -
	 *
	 */
	public static function save( $id ) {
		if ( get_post_type( $id ) !== 'nav_menu_item' ) {
			return;
		}

		foreach( self::options() as $field ) {
			$name = 'menu-item-' . $field['name'];
			if ( isset( $_POST[ $name ][ $id ] ) ) {
				update_post_meta( $id, $field['name'], $_POST[ $name ][ $id ] );
			} else {
				update_post_meta( $id, $field['name'], false );
			}
		}
	}

	/**
	 *
	 * Menus options
	 * 
	 * @return array
	 *
	 */
	public static function options() {
		return array(
			array(
				'name'  => 'cz_menu_subtitle',
				'title' => esc_html__( 'Sub title', 'codevz' ),
				'type'  => 'text'
			),
			array(
				'name'  => 'cz_menu_col_title',
				'title' => esc_html__( 'As Title?', 'codevz' ),
				'type'  => 'switcher'
			),
			array(
				'name'  => 'cz_menu_activation',
				'title' => esc_html__( 'Customize?', 'codevz' ),
				'type'  => 'switcher'
			),
			array(
				'name'  	=> 'cz_menu_css_label',
				'hover_id' 	=> 'cz_menu_css_label_hover',
				'title' 	=> esc_html__( 'Label', 'codevz' ),
				'button' 	=> esc_html__( 'Styling', 'codevz' ),
				'type'  	=> 'cz_sk',
				'settings' 	=> array( 'color', 'background', 'font-size', 'font-family' ),
				'dependency' => array( 'cz_menu_activation', '==', 'true' )
			),
			array(
				'name'  	=> 'cz_menu_css_label_hover',
				'title' 	=> '',
				'button' 	=> '',
				'type'  	=> 'cz_sk_hidden',
				'settings' 	=> array( 'color', 'background', 'font-size' ),
				'dependency' => array( 'cz_menu_activation', '==', 'true' )
			),
			array(
				'name'  => 'cz_menu_icon',
				'title' => esc_html__('Icon', 'codevz'),
				'type'  => 'icon',
				'dependency' => array( 'cz_menu_activation', '==', 'true' )
			),
			array(
				'name'  	=> 'cz_menu_css_icon',
				'title' 	=> esc_html__( 'Icon', 'codevz' ),
				'button' 	=> esc_html__( 'Styling', 'codevz' ),
				'type'  	=> 'cz_sk',
				'settings' 	=> array( 'color', 'font-size', 'background', 'padding' ),
				'dependency' => array( 'cz_menu_icon|cz_menu_activation', '!=|==', '|true' )
			),
			array(
				'name'  => 'cz_menu_hide_title',
				'title' => esc_html__('Only icon?', 'codevz'),
				'type'  => 'switcher',
				'dependency' => array( 'cz_menu_icon|cz_menu_activation', '!=|==', '|true' )
			),
			array(
				'name'  => 'cz_menu_badge',
				'title' => esc_html__('Badge', 'codevz'),
				'type'  => 'text',
				'dependency' => array( 'cz_menu_activation', '==', 'true' )
			),
			array(
				'name'  	=> 'cz_menu_css_badge',
				'title' 	=> esc_html__( 'Badge', 'codevz' ),
				'button' 	=> esc_html__( 'Styling', 'codevz' ),
				'type'  	=> 'cz_sk',
				'settings' 	=> array( 'color', 'font-size', 'font-family', 'background' ),
				'dependency' => array( 'cz_menu_badge|cz_menu_activation', '!=|==', '|true' )
			),
			array(
				'name' 	=> 'cz_menu_megamenu',
				'type' 	=> 'select',
				'title' => esc_html__( 'Mega menu', 'codevz' ),
				'depth'		=> 0,
				'options' 	=> array(
					'listing' 		=> esc_html__( 'Default with children', 'codevz' ),
					'custom' 		=> esc_html__( 'Page content as mega menu', 'codevz' ),
					'custom_code' 	=> esc_html__( 'Custom shortcode as mega menu', 'codevz' ),
				),
				'default_option' => esc_html__( 'Select', 'codevz')
			),
			array(
				'name' 		=> 'cz_menu_megamenu_id',
				'type' 		=> 'select',
				'depth'		=> 0,
				'title'		=> esc_html__( 'Select', 'codevz' ),
				'options' 	=> Codevz_Plus::$array_pages,
				'dependency' => array( 'cz_menu_megamenu', 'any', 'custom' )
			),
			array(
				'name' 	=> 'cz_menu_megamenu_width',
				'type' 	=> 'select',
				'depth'		=> 0,
				'title' => esc_html__( 'Width', 'codevz' ),
				'options' 	=> array(
					'' 							 => esc_html__( 'Default', 'codevz' ),
					'cz_megamenu_center_mode' 	 => esc_html__( 'Default and Center position', 'codevz' ),
					'cz_megamenu_reverse_mode' 	 => esc_html__( 'Default and Reverse position', 'codevz' ),
					'cz_megamenu_width_full_row' => esc_html__( 'Full width according to header', 'codevz' ),
					'cz_megamenu_width_fullwide' => esc_html__( 'Full width according to window', 'codevz' ),
				),
				'dependency' => array( 'cz_menu_megamenu', 'any', 'listing,custom' )
			),
			array(
				'name'  	=> 'cz_menu_css_ul',
				'title' 	=> esc_html__( 'Mega menu', 'codevz' ),
				'depth'		=> 0,
				'button' 	=> esc_html__( 'Styling', 'codevz' ),
				'type'  	=> 'cz_sk',
				'settings' 	=> array( 'background', 'width', 'padding' ),
				'dependency' => array( 'cz_menu_megamenu', '!=', '' )
			),
			array(
				'name'  => 'cz_menu_custom',
				'title' => esc_html__('Custom code', 'codevz'),
				'depth'	=> 0,
				'type'  => 'textarea',
				'sanitize' => false,
				'help'  => esc_html__( 'If you fill this field for this column, then title and icon not works for this menu item. This field allows Shortcode and HTML code.', 'codevz' ),
				'dependency' => array( 'cz_menu_megamenu', '==', 'custom_code' )
			),
		);
	}
}
new Codevz_Menu_Add_CSF_Fields();

/**
 *
 * Extend Codevz to Walker_Nav_Menu_Edit
 * 
 * @return string
 *
 */
require_once ABSPATH . 'wp-admin/includes/nav-menu.php';
class Codevz_Walker_Nav_Menu_Edit extends Walker_Nav_Menu_Edit {

	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		parent::start_el( $item_output, $item, $depth, $args );
		$new_fields = apply_filters( 'codevz_nav_menu_csf_fields', '', $item_output, $item, $depth, $args );
		$item_output = $new_fields ? preg_replace('/(?=<div[^>]+class="[^"]*submitbox)/', $new_fields, $item_output) : '';
		$output .= $item_output;
	}

}

/**
 *
 * Add Codevz Walker into WP Walker_Nav_Menu
 * 
 * @return string
 *
 */
class Codevz_Walker_nav extends Walker_Nav_Menu {

	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		$meta = $meta2 = get_post_meta( $item->ID );
		if ( empty( $meta['cz_menu_activation'][0] ) ) {
			$meta = null;
		}
		$mnt = $value = $custom = $ul_css = '';

		$title = empty( $meta['cz_menu_hide_title'][0] ) ? $item->title : '';

		$indent = $depth ? str_repeat( "\t", $depth ) : '';
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		if ( ! empty( $meta2['cz_menu_megamenu_width'][0] ) ) {
			$classes[] = $meta2['cz_menu_megamenu_width'][0];
		}
		$classes = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$is_mega = empty( $meta2['cz_menu_megamenu'][0] ) ? '' : ' cz_parent_megamenu';

		if ( ! empty( $meta['cz_menu_icon'][0] ) ) {
			$icon = $meta['cz_menu_icon'][0];
			$icon_css = empty( $meta['cz_menu_css_icon'][0] ) ? '' : ' style="' . $meta['cz_menu_css_icon'][0] . '"';
			$title = $title ? '<i class="' . $icon . '"' . $icon_css . '></i>' . $title : '<i class="' . $icon . ' menu_icon_no_text" title="' . $title . '"' . $icon_css . '></i>';
		}

		if ( ! empty( $meta['cz_menu_badge'][0] ) ) {
			$badge_css = empty( $meta['cz_menu_css_badge'][0] ) ? '' : ' style="' . $meta['cz_menu_css_badge'][0] . '"';
			$title .= '<span class="cz_menu_badge"' . $badge_css . '>' . $meta['cz_menu_badge'][0] . '</span> ';
		}

		if ( ! empty( $meta['cz_menu_custom'][0] ) ) {
			$custom = do_shortcode( $meta['cz_menu_custom'][0] );
		}

		$ul_css = empty( $meta['cz_menu_css_ul'][0] ) ? '' : ' data-sub-menu="' . $meta['cz_menu_css_ul'][0] . '"';
		$classes .= $title ? '' : ' hide';
		$classes = ' class="' . esc_attr( $classes ) . $is_mega . $mnt . '"';
		$output .= '<li id="menu-' . $args->cz_row_id . '-' . $item->ID . '"' . $classes . $ul_css . '>';
		$attributes  = empty( $item->attr_title ) ? '' : ' title="'  . esc_attr( $item->attr_title ) .'"';
		$attributes  = ' data-title="'  . esc_attr( strip_tags( $title ) ) .'"';
		$attributes .= empty( $item->target )     ? '' : ' target="' . esc_attr( $item->target     ) .'"';
		$attributes .= empty( $item->xfn )        ? '' : ' rel="'    . esc_attr( $item->xfn        ) .'"';
		$attributes .= empty( $item->url )        ? '' : ' href="'   . esc_attr( $item->url        ) .'"';
		$attributes .= empty( $meta['cz_menu_css_label'][0] ) ? '' : ' style="' . $meta['cz_menu_css_label'][0] . '"';
		$attributes .= empty( $meta['cz_menu_css_label_hover'][0] ) ? '' : ' data-cz-style="' . $meta['cz_menu_css_label_hover'][0] . '"';
		$description = empty( $meta2['cz_menu_subtitle'][0] ) ? '' : '<span class="cz_menu_subtitle">' . $meta2['cz_menu_subtitle'][0] . '</span>';

		if ( $custom ) {
			$item_output = '<div class="cz_menu_custom">' . $custom . '</div>';
		} else if ( ! empty( $meta2['cz_menu_col_title'][0] ) ) {
			$item_output = '<h6>' . $title . '</h6>';
		} else {
			$item_output = $args->before;
			$item_output .= '<a' . $attributes . '>';
			$item_output .= $args->link_before . '<span>' . $title . '</span>' . $description . $args->link_after;
			$item_output .= '</a>';
			$item_output .= ( ! empty( $meta2['cz_menu_megamenu'][0] ) && $meta2['cz_menu_megamenu'][0] === 'custom' && ! empty( $meta2['cz_menu_megamenu_id'][0] ) ) ? '<ul class="sub-menu cz_custom_mega_menu clr">' . Codevz_Plus::get_page_as_element( $meta2['cz_menu_megamenu_id'][0] ) . '</ul>' : '';
			$item_output .= $args->after;
		}

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args, $id );
	}
}