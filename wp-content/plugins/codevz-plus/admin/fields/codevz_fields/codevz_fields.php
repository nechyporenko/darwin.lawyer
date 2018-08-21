<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.

/* Admin assets */
add_action( 'admin_enqueue_scripts', 'codevz_plus_admin_enqueue_scripts', 9 );
function codevz_plus_admin_enqueue_scripts() {
  wp_enqueue_script ( 'jquery-ui-dialog' );
  wp_enqueue_script( 'cz-fields', CSF_PLUGIN_URL . '/fields/codevz_fields/codevz_fields.js', array( 'jquery' ), '', true );
  wp_enqueue_style( 'cz-fields', CSF_PLUGIN_URL . '/fields/codevz_fields/codevz_fields.css' );
  wp_enqueue_style( 'cz-icons-pack', CSF_PLUGIN_URL . '/fields/codevz_fields/icons/czicons.css' );
}
add_action( 'wp_enqueue_scripts', 'codevz_plus_wp_enqueue_scripts' );
function codevz_plus_wp_enqueue_scripts() {
  if ( Codevz_Plus::$vc_editable ) {
    wp_enqueue_style( 'cz-fields', CSF_PLUGIN_URL . '/fields/codevz_fields/codevz_fields.css' );
  }
  wp_enqueue_style( 'cz-icons-pack', CSF_PLUGIN_URL . '/fields/codevz_fields/icons/czicons.css' );
}

/* Customizer */
add_action( 'customize_preview_init', 'codevz_plus_customize_preview_init' );
function codevz_plus_customize_preview_init() {
  wp_enqueue_script( 'codevz-customize', CSF_PLUGIN_URL . '/fields/codevz_fields/codevz_customizer.js', array( 'customize-preview' ), false, true );
}

/* Field: Slider */
if( ! class_exists( 'CSF_Field_slider' ) ) {
  class CSF_Field_slider extends CSF_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '' ) {
      parent::__construct( $field, $value, $unique, $where );
    }

    public function output() {

      if ( isset( $this->field['options'] ) ) {
        $options = $this->field['options'];
      } else {
        $options = array( 'step' => 1, 'unit' => 'px', 'min' => 0, 'max' => 100 );
      }

      echo $this->element_before();
      echo '<input type="text" name="'. $this->element_name() .'" value="'. $this->element_value() .'"'. $this->element_class() . $this->element_attributes() .'/>';
      echo '<div class="csf-slider" data-options=\'' . json_encode( $options ) . '\'></div>';
      echo $this->element_after();

    }

  }
}

/* Field: 4 Sizes */
if( ! class_exists( 'CSF_Field_codevz_sizes' ) ) {
  class CSF_Field_codevz_sizes extends CSF_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '' ) {
      parent::__construct( $field, $value, $unique, $where );
    }

    public function output() {

      echo $this->element_before();
      $value_defaults = array( 'top' => '', 'right' => '', 'bottom' => '', 'left' => '' );
      $this->value  = wp_parse_args( $this->element_value(), $value_defaults );

      $default_options = array( 'step' => 1, 'unit' => 'px', 'min' => 0, 'max' => 50 );
      $options = isset( $this->field['options'] ) ? wp_parse_args( $this->field['options'], $default_options ) : $default_options;

      echo '<fieldset>';
      echo csf_add_field( array(
          'type'    => 'slider',
          'name'    => $this->element_name( '[top]' ),
          'before'  => '<i class="fa fa-angle-up"></i>',
          'options' => $options
      ), $this->value['top'], '', 'field/codevz_sizes' );

      echo csf_add_field( array(
          'type'    => 'slider',
          'name'    => $this->element_name( '[right]' ),
          'before'  => '<i class="fa fa-angle-right"></i>',
          'options' => $options
      ), $this->value['right'], '', 'field/codevz_sizes' );

      echo csf_add_field( array(
          'type'    => 'slider',
          'name'    => $this->element_name( '[bottom]' ),
          'before'  => '<i class="fa fa-angle-down"></i>',
          'options' => $options
      ), $this->value['bottom'], '', 'field/codevz_sizes' );
      
      echo csf_add_field( array(
          'type'    => 'slider',
          'name'    => $this->element_name( '[left]' ),
          'before'  => '<i class="fa fa-angle-left"></i>',
          'options' => $options
      ), $this->value['left'], '', 'field/codevz_sizes' );

      echo '<i class="fa fa-link" title="Connect all inputs"></i></fieldset>';
      echo $this->element_after();

    }
  }
}

/* Field: Box Shadow 3 size field + color */
if( ! class_exists( 'CSF_Field_codevz_box_shadow' ) ) {
  class CSF_Field_codevz_box_shadow extends CSF_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '' ) {
      parent::__construct( $field, $value, $unique, $where );
    }

    public function output() {

      echo $this->element_before();
      $value_defaults = array( 'x' => '', 'y' => '', 'blur' => '', 'color' => '', 'inset' => '' );
      $this->value  = wp_parse_args( $this->element_value(), $value_defaults );

      $options = array( 'step' => 1, 'unit' => 'px', 'min' => 0, 'max' => 100 );

      echo '<fieldset>';
      echo csf_add_field( array(
          'type'    => 'slider',
          'name'    => $this->element_name( '[x]' ),
          'before'  => '<i>X</i>',
          'options' => $options,
          'dependency' => array( '_shadow_' . $this->field['id'], '!=', 'none' ),
      ), $this->value['x'], '', 'field/codevz_box_shadow' );

      echo csf_add_field( array(
          'type'    => 'slider',
          'name'    => $this->element_name( '[y]' ),
          'before'  => '<i>Y</i>',
          'options' => $options,
          'dependency' => array( '_shadow_' . $this->field['id'], '!=', 'none' ),
      ), $this->value['y'], '', 'field/codevz_box_shadow' );

      echo csf_add_field( array(
          'type'    => 'slider',
          'name'    => $this->element_name( '[blur]' ),
          'before'  => '<i>Blur</i>',
          'options' => $options,
          'dependency' => array( '_shadow_' . $this->field['id'], '!=', 'none' ),
      ), $this->value['blur'], '', 'field/codevz_box_shadow' );

      echo csf_add_field( array(
          'type'    => 'color_picker',
          'name'    => $this->element_name( '[color]' ),
          'attributes' => array(
            'data-rgba' => '#000'
          ),
          'dependency' => array( '_shadow_' . $this->field['id'], '!=', 'none' ),
      ), $this->value['color'], '', 'field/codevz_box_shadow' );

      if ( isset( $this->field['id'] ) && Codevz_Plus::contains( $this->field['id'], 'box' ) ) {
        echo csf_add_field( array(
            'type'    => 'select',
            'name'    => $this->element_name( '[inset]' ),
            'options' => array(
              'true'   => esc_html__( 'Inset', 'codevz' ),
              'none'   => esc_html__( 'No Shadow', 'codevz' ),
            ),
            'default_option' => esc_html__( 'Outset', 'codevz'),
            'attributes' => array(
              'data-depend-id' => '_shadow_' . $this->field['id'],
            ),
        ), $this->value['inset'], '', 'field/codevz_box_shadow' );
      }

      echo '</fieldset>';
      echo $this->element_after();

    }
  }
}

/* Add new customize/complex - array field value */
function codevz_sizes_filter_customize( $i ) {
  $i[] = 'codevz_sizes';
  $i[] = 'codevz_box_shadow';
  $i[] = 'sk';

  return $i;
}
add_filter( 'csf/customize/complex', 'codevz_sizes_filter_customize' );

/* Field: Font select */
if( ! class_exists( 'CSF_Field_select_font' ) ) {
  class CSF_Field_select_font extends CSF_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '' ) {
      parent::__construct( $field, $value, $unique, $where );
    }

    public function output() {

      $value = $this->element_value();
      $hidden = ( empty( $value ) ) ? ' hidden' : '';

      echo $this->element_before();
      echo '<div class="csf-font-select">';
      echo '<input type="text" name="'. $this->element_name() .'" value="'. $value .'"'. $this->element_class( 'csf-font-value wpb_vc_param_value' ) . $this->element_attributes() .' />';
      echo '<a href="#" class="button button-primary csf-font-add">'. esc_html__( 'Choose', 'codevz' ) .'</a>';
      echo '<a href="#" class="button csf-warning-primary csf-font-remove'. $hidden .'"><i class="fa fa-remove"></i></a>';
      echo '</div>';
      echo $this->element_after();
    }

  }
}

/* Font selector get fonts by ajax */
if( ! function_exists( 'csf_get_fonts' ) ) {
  function csf_get_fonts() {

    // Websafe fonts
    $websafe = apply_filters( 'csf/field/fonts/websafe', array(
      'Arial',
      'Arial Black',
      'Comic Sans MS',
      'Impact',
      'Lucida Sans Unicode',
      'Tahoma',
      'Trebuchet MS',
      'Verdana',
      'Courier New',
      'Lucida Console',
      'Georgia, serif',
      'Palatino Linotype',
      'Times New Roman'
    ));

    // Custom fonts
    $custom_fonts = Codevz_Plus::option( 'custom_fonts' );
    if ( ! empty( $custom_fonts ) ) {
      foreach ( $custom_fonts as $a ) {
        if ( ! empty( $a['font'] ) ) {
          array_unshift( $websafe, $a['font'] );
        }
      }
    }

    foreach ( $websafe as $f ) {
      echo '<a class="websafe_font" style="font-family: ' . $f . '"><span>' . $f . '</span><div class="cz_preview"></div></a>';
    }

    // Google fonts
    $fonts = csf_get_google_fonts();
    foreach ( $fonts->items as $n => $item ) {
      $f = $item->family;

      $params = '';
      foreach ( $item->variants as $p ) {
        if ( ! Codevz_Plus::contains( $p, 'italic' ) ) {
          $v = ( $p === 'regular' ) ? '400' : $p;
          $params .= '<label class="cz_font_variants"><input type="checkbox" name="' . $p . '" value="' . $v . '">' . $p . '</label>';
        }
      }
      foreach ( $item->subsets as $p ) {
        if ( $p !== 'latin' ) {
          $params .= '<label class="cz_font_subsets"><input type="checkbox" name="' . $p . '" value="' . $p . '">' . $p . '</label>';
        }
      }

      echo '<a class="cz_font"><span>' . $f . '</span><div class="cz_preview"></div><i class="fa fa-cog"></i></a><div class="cz_font_params"><div>' . $params . '</div></div>';
    }

    die();
  }
  add_action( 'wp_ajax_csf-get-fonts', 'csf_get_fonts' );
}

/* Set icons for wp dialog */
if( ! function_exists( 'csf_set_fonts' ) ) {
  function csf_set_fonts() {
    ?>
    <div id="csf-modal-font" class="csf-modal csf-modal-font">
      <div class="csf-modal-table">
        <div class="csf-modal-table-cell">
          <div class="csf-modal-overlay"></div>
          <div class="csf-modal-inner">
            <div class="csf-modal-title">
              <?php _e( 'Google Fonts Library', 'codevz' ); ?>
              <div class="csf-modal-close csf-font-close"></div>
            </div>
            <div class="csf-modal-header csf-text-center">
              <input type="text" placeholder="<?php _e( 'Search', 'codevz' ); ?>" class="csf-font-search" />
              <input type="text" placeholder="<?php _e( 'Preview', 'codevz' ); ?>" class="csf-font-placeholder">
            </div>
            <div class="csf-modal-content"><div class="csf-font-loading"></div></div>
          </div>
        </div>
      </div>
    </div>
    <?php
  }
  add_action( 'admin_footer', 'csf_set_fonts' );
  add_action( 'customize_controls_print_footer_scripts', 'csf_set_fonts' );
}

/* Field: StyleKit */
if( ! class_exists( 'CSF_Field_cz_sk' ) ) {
  class CSF_Field_cz_sk extends CSF_Fields {
    public function __construct( $field, $value = '', $unique = '', $where = '' ) {
      parent::__construct( $field, $value, $unique, $where );
    }

    public function output() {
      echo $this->element_before();
      $val = $this->element_value();
      $val = ( is_array( $this->element_value() ) || $val === 'Array' ) ? '' : $this->element_value();
      $hover = isset( $this->field['hover_id'] ) ? ' data-hover_id="' . $this->field['hover_id'] . '"' : '';
      echo '<input type="hidden" name="'. $this->element_name() .'"' . $hover . ' value="' . $val . '" data-fields="' . implode( ' ', $this->field['settings'] ) . '"' . $this->element_attributes() . ' />';
      $is_active = $val ? ' active_stylekit' : '';
      echo '<a href="#" class="button cz_sk_btn ' . $is_active . '"><span class="cz_skico cz'. $this->field['id']  .'"></span>' . $this->field['button'] . '</a>';
      echo $this->element_after();
    }
  }
}

/* Field: StyleKit 2
if( ! class_exists( 'CSF_Field_sk' ) ) {
  class CSF_Field_sk extends CSF_Fields {
    public function __construct( $field, $value = '', $unique = '', $where = '' ) {
      parent::__construct( $field, $value, $unique, $where );
    }

    public function output() {
      echo $this->element_before();

      echo '<div class="sk_con">';
      echo '<input type="text" name="'. $this->element_name( '[style]' ) .'" value="' . ( isset( $this->value['style'] ) ? $this->value['style'] : '' ) . '" />';
      echo '<input type="text" name="'. $this->element_name( '[tablet]' ) .'" value="' . ( isset( $this->value['tablet'] ) ? $this->value['tablet'] : '' ) . '" />';
      echo '<input type="text" name="'. $this->element_name( '[mobile]' ) .'" value="' . ( isset( $this->value['mobile'] ) ? $this->value['mobile'] : '' ) . '" />';
      echo '<input type="text" name="'. $this->element_name( '[hover]' ) .'" value="' . ( isset( $this->value['hover'] ) ? $this->value['hover'] : '' ) . '" />';
      echo '</div>';
      echo $this->element_after();
    }
  }
}*/

/* Field: StyleKit hidden for responsive */
if( ! class_exists( 'CSF_Field_cz_sk_hidden' ) ) {
  class CSF_Field_cz_sk_hidden extends CSF_Fields {
    public function __construct( $field, $value = '', $unique = '', $where = '' ) {
      parent::__construct( $field, $value, $unique, $where );
    }

    public function output() {
      $val = $this->element_value();
      $val = ( is_array( $this->element_value() ) || $val === 'Array' ) ? '' : $this->element_value();
      echo '<input type="hidden" name="'. $this->element_name() .'" value="' . $val . '"' . $this->element_attributes() . ' />';
    }
  }
}

/* Style kit HTML */
if( ! function_exists( 'codevz_modal_style_kit' ) ) {
  function codevz_modal_style_kit() { ?>
    <div id="cz_modal_kit" title="Styling">
      <div>
        <form>
          <?php 

            // Start
            echo '<div class="cz_sk_row cz_sk_content_row clr">';
            echo '<h4>Content</h4>';
            echo '<div class="col s12">' . csf_add_field( array(
              'type'    => 'text',
              'id'      => 'live_id',
              'name'    => 'live_id',
              'title'   => 'ID',
            ), '' ) . '</div>';
            echo '<div class="col s12">' . csf_add_field( array(
              'id'      => 'content',
              'name'    => 'content',
              'type'    => 'text',
              'title'   => esc_html__( 'Content', 'codevz' ),
              'help'    => esc_html__( 'Any charachters or HTML symbols are allowed', 'codevz' )
            ), '' ) . '</div>';
            echo '</div>'; // Start

            // Indicator
            echo '<div class="cz_sk_row cz_sk_indicator_row clr">';
            echo '<h4>Menu Indicator</h4>';
            echo '<div class="col s12">' . csf_add_field( array(
              'id'      => '_class_indicator',
              'name'    => '_class_indicator',
              'type'    => 'icon',
              'title'   => ''
            ), '' ) . '</div>';
            echo '</div>'; // Indicator

            // Shape
            echo '<div class="cz_sk_row cz_sk_shape_row clr">';
            echo '<h4>Shape</h4>';
            echo '<div class="col s12">' . csf_add_field( array(
              'id'      => '_class_shape',
              'name'    => '_class_shape',
              'type'    => 'select',
              'title'   => esc_html__( 'Choose', 'codevz' ),
              'help'    => esc_html__( 'This option required background color', 'codevz' ),
              'options'  => array(
                ''                                => esc_html__( 'Select', 'codevz' ),
                'cz_row_shape_none'               => esc_html__( 'None', 'codevz' ),
                'cz_row_shape_full_filled_left cz_row_shape_no_right'   => esc_html__( 'Filled left', 'codevz' ),
                'cz_row_shape_full_filled_right cz_row_shape_no_left'  => esc_html__( 'Filled right', 'codevz' ),

                'cz_row_shape_1'  => esc_html__( 'Shape', 'codevz' ) . ' 1',
                'cz_row_shape_2'  => esc_html__( 'Shape', 'codevz' ) . ' 2',

                'cz_row_shape_3'  => esc_html__( 'Shape', 'codevz' ) . ' 3',
                'cz_row_shape_3 cz_row_shape_full_filled_left'  => esc_html__( 'Filled left', 'codevz' ) . ' 3',
                'cz_row_shape_3 cz_row_shape_full_filled_right' => esc_html__( 'Filled right', 'codevz' ) . ' 3',

                'cz_row_shape_4'  => esc_html__( 'Shape', 'codevz' ) . ' 4',
                'cz_row_shape_4 cz_row_shape_full_filled_left'  => esc_html__( 'Filled left', 'codevz' ) . ' 4',
                'cz_row_shape_4 cz_row_shape_full_filled_right' => esc_html__( 'Filled right', 'codevz' ) . ' 4',

                'cz_row_shape_5'  => esc_html__( 'Shape', 'codevz' ) . ' 5',
                'cz_row_shape_5 cz_row_shape_full_filled_left'  => esc_html__( 'Filled left', 'codevz' ) . ' 5',
                'cz_row_shape_5 cz_row_shape_full_filled_right' => esc_html__( 'Filled right', 'codevz' ) . ' 5',

                'cz_row_shape_6'  => esc_html__( 'Shape', 'codevz' ) . ' 6',
                'cz_row_shape_6 cz_row_shape_full_filled_left'  => esc_html__( 'Filled left', 'codevz' ) . ' 6',
                'cz_row_shape_6 cz_row_shape_full_filled_right' => esc_html__( 'Filled right', 'codevz' ) . ' 6',

                'cz_row_shape_7'  => esc_html__( 'Shape', 'codevz' ) . ' 7',
                'cz_row_shape_7 cz_row_shape_full_filled_left'  => esc_html__( 'Filled left', 'codevz' ) . ' 7',
                'cz_row_shape_7 cz_row_shape_full_filled_right' => esc_html__( 'Filled right', 'codevz' ) . ' 7',
              )
            ), '' ) . '</div>';
            echo '</div>'; // Shape

            // FX Menu
            echo '<div class="cz_sk_row cz_sk_fx_row clr">';
            echo '<h4>Menu FX</h4>';
            echo '<div class="col s12">' . csf_add_field( array(
              'id'      => '_class_menu_fx',
              'name'    => '_class_menu_fx',
              'type'    => 'select',
              'title'   => esc_html__( 'Choose', 'codevz' ),
              'help'    => esc_html__( 'You can customize shape by changing settings background, width, height, left, bottom, etc.', 'codevz' ),
              'options' => array(
                ''                          => esc_html__( 'Select', 'codevz' ),
                'cz_menu_fx_none'           => esc_html__( 'None', 'codevz' ),
                'cz_menu_fx_left_to_right'  => esc_html__( 'Left To Right', 'codevz' ),
                'cz_menu_fx_left_to_right_l'=> esc_html__( 'Left To Right Long', 'codevz' ),
                'cz_menu_fx_right_to_left'  => esc_html__( 'Right To Left', 'codevz' ),
                'cz_menu_fx_right_to_left_l'=> esc_html__( 'Right To Left Long', 'codevz' ),
                'cz_menu_fx_center_to_sides'=> esc_html__( 'Center To Sides', 'codevz' ),
                'cz_menu_fx_top_to_bottom'  => esc_html__( 'Top To Bottom', 'codevz' ),
                'cz_menu_fx_bottom_to_top'  => esc_html__( 'Bottom To Top', 'codevz' ),
                'cz_menu_fx_fade_in'    => esc_html__( 'FadeIn', 'codevz' ),
                'cz_menu_fx_zoom_in'    => esc_html__( 'ZoomIn', 'codevz' ),
                'cz_menu_fx_zoom_out'   => esc_html__( 'ZoomOut', 'codevz' ),
                'cz_menu_fx_unroll'     => esc_html__( 'Unroll Vertical', 'codevz' ),
                'cz_menu_fx_unroll_h'   => esc_html__( 'Unroll Horizontal', 'codevz' ),
              )
            ), '' ) . '</div>';
            echo '</div>'; // FX Menu

            // FX SubMenu
            echo '<div class="cz_sk_row cz_sk_fx_row clr">';
            echo '<h4>Dropdown FX</h4>';
            echo '<div class="col s12">' . csf_add_field( array(
              'id'      => '_class_submenu_fx',
              'name'    => '_class_submenu_fx',
              'type'    => 'select',
              'title'   => esc_html__( 'Choose', 'codevz' ),
              'options' => array(
                ''                        => esc_html__( 'Select', 'codevz' ),
                'cz_menu_fx_none'         => esc_html__( 'None', 'codevz' ),
                'cz_submenu_fx_moveup'    => esc_html__( 'Move up', 'codevz' ),
                'cz_submenu_fx_movedown'  => esc_html__( 'Move down', 'codevz' ),
                'cz_submenu_fx_moveleft'  => esc_html__( 'Move left', 'codevz' ),
                'cz_submenu_fx_moveright' => esc_html__( 'Move right', 'codevz' ),
                'cz_submenu_fx_zoomin'    => esc_html__( 'Zoom in', 'codevz' ),
                'cz_submenu_fx_zoomout'   => esc_html__( 'Zoom out', 'codevz' ),
                'cz_submenu_fx_rotate1'   => esc_html__( 'Rotate 1', 'codevz' ),
                'cz_submenu_fx_rotate2'   => esc_html__( 'Rotate 2', 'codevz' ),
                'cz_submenu_fx_rotate3'   => esc_html__( 'Rotate 3', 'codevz' ),
                'cz_submenu_fx_rotate4'   => esc_html__( 'Rotate 4', 'codevz' ),
                'cz_submenu_fx_skew1'     => esc_html__( 'Skew 1', 'codevz' ),
                'cz_submenu_fx_skew2'     => esc_html__( 'Skew 2', 'codevz' ),
                'cz_submenu_fx_skew3'     => esc_html__( 'Skew 3', 'codevz' ),
              )
            ), '' ) . '</div>';
            echo '</div>'; // FX SubMenu

            // Typography
            echo '<div class="cz_sk_row cz_sk_typo_row clr">';
            echo '<h4>Typography</h4>';

            echo '<div class="col s6">' . csf_add_field( array(
              'id'      => 'font-size',
              'name'    => 'font-size',
              'type'    => 'slider',
              'title'   => esc_html__( 'Size', 'codevz' ),
              'options' => array( 'unit' => 'px', 'step' => 1, 'min' => 0, 'max' => 130 )
            ), '' ) . '</div>';

            echo '<div class="col s6">' . csf_add_field( array(
              'id'      => 'color',
              'name'    => 'color',
              'type'    => 'color_picker',
              'title'   => esc_html__( 'Color', 'codevz' ),
            ), '' ) . '</div>';

            echo '<div class="clr cz_hr"></div>';

            echo '<div class="col s12">' . csf_add_field( array(
              'id'      => 'font-family',
              'name'    => 'font-family',
              'type'    => 'select_font',
              'title'   => esc_html__( 'Font Family', 'codevz' ),
            ), '' ) . '</div>';

            echo '<div class="clr cz_hr"></div>';

            echo '<div class="col s3">' . csf_add_field( array(
              'id'      => 'text-align',
              'name'    => 'text-align',
              'type'    => 'select',
              'title'   => '<i class="fa fa-align-justify" title="Text Align"></i>',
              'options' => array(
                'left'    => esc_html__( 'Left', 'codevz' ),
                'right'   => esc_html__( 'Right', 'codevz' ),
                'center'  => esc_html__( 'Center', 'codevz' ),
                'justify' => esc_html__( 'Justify', 'codevz' ),
              ),
              'default_option' => esc_html__( 'Select', 'codevz'),
            ), '' ) . '</div>';

            echo '<div class="col s3">' . csf_add_field( array(
              'id'      => 'font-weight',
              'name'    => 'font-weight',
              'type'    => 'select',
              'title'   => '<i class="fa fa-bold" title="Font Weight"></i>',
              'options' => array(
                '100' => esc_html__( '100 | Thin', 'codevz' ),
                '200' => esc_html__( '200 | Extra Light', 'codevz' ),
                '300' => esc_html__( '300 | Light', 'codevz' ),
                '400' => esc_html__( '400 | Normal', 'codevz' ),
                '500' => esc_html__( '500 | Medium', 'codevz' ),
                '600' => esc_html__( '600 | Semi Bold', 'codevz' ),
                '700' => esc_html__( '700 | Bold', 'codevz' ),
                '800' => esc_html__( '800 | Extra Bold', 'codevz' ),
                '900' => esc_html__( '900 | High Bold', 'codevz' ),
              ),
              'default_option' => __( 'Select', 'codevz'),
            ), '' ) . '</div>';

            echo '<div class="col s3">' . csf_add_field( array(
              'id'      => 'font-style',
              'name'    => 'font-style',
              'type'    => 'select',
              'title'   => '<i class="fa fa-italic" title="Font Style"></i>',
              'options' => array(
                'normal'  => esc_html__( 'Normal', 'codevz' ),
                'italic'  => esc_html__( 'Italic', 'codevz' ),
                'oblique' => esc_html__( 'Oblique', 'codevz' ),
              ),
              'default_option' => __( 'Select', 'codevz'),
            ), '' ) . '</div>';

            echo '<div class="col s3">' . csf_add_field( array(
              'id'      => 'line-height',
              'name'    => 'line-height',
              'type'    => 'slider',
              'title'   => '<i class="fa fa-text-height" title="Line Height"></i>',
              'options' => array( 'unit' => '', 'step' => 1, 'min' => 0, 'max' => 80 ),
            ), '' ) . '</div>';

            echo '<div class="col s3">' . csf_add_field( array(
              'id'      => 'letter-spacing',
              'name'    => 'letter-spacing',
              'type'    => 'slider',
              'title'   => '<i class="fa fa-text-width" title="Letter Spacing"></i>',
              'options' => array( 'unit' => 'px', 'step' => 1, 'min' => -2, 'max' => 20 ),
            ), '' ) . '</div>';

            echo '<div class="col s3">' . csf_add_field( array(
              'id'      => 'text-transform',
              'name'    => 'text-transform',
              'type'    => 'select',
              'title'   => '<i class="fa fa-font" title="Text Transform"></i>',
              'options' => array(
                'none'      => esc_html__( 'None', 'codevz' ),
                'uppercase' => esc_html__( 'Uppercase', 'codevz' ),
                'lowercase' => esc_html__( 'Lowercase', 'codevz' ),
                'capitalize' => esc_html__( 'Capitalize', 'codevz' )
              ),
              'default_option' => esc_html__( 'Select', 'codevz'),
            ), '' ) . '</div>';

            echo '<div class="col s3">' . csf_add_field( array(
              'id'      => 'text-decoration',
              'name'    => 'text-decoration',
              'type'    => 'select',
              'title'   => '<i class="fa fa-underline" title="Text Decoration"></i>',
              'options' => array(
                'none'              => esc_html__( 'None', 'codevz' ),
                'underline'         => esc_html__( 'Underline', 'codevz' ),
                'overline'          => esc_html__( 'Overline', 'codevz' ),
                'line-through'      => esc_html__( 'Line through', 'codevz' ),
              ),
              'default_option' => esc_html__( 'Select', 'codevz'),
            ), '' ) . '</div>';
            echo '</div>'; // Typography

            // SVG
            echo '<div class="cz_sk_row cz_sk_svg_row clr">';
            echo '<h4>SVG Settings</h4>';
            echo '<div class="col s12">' . csf_add_field( array(
              'id'        => '_class_svg_type',
              'name'      => '_class_svg_type',
              'type'      => 'select',
              'title'     => esc_html__( 'SVG Type', 'codevz' ),
              'options'   => array(
                'dots'      => esc_html__( 'Dots', 'codevz' ),
                'circle'    => esc_html__( 'Circle', 'codevz' ),
                'line'      => esc_html__( 'Lines', 'codevz' ),
                'x'         => esc_html__( 'X', 'codevz' ),
                'empty'     => esc_html__( 'Empty', 'codevz' ),
              ),
              'default_option' => esc_html__( 'Select', 'codevz'),
              'attributes' => array(
                'data-depend-id' => '_class_svg_type',
              ),
            ), '' ) . '</div>';

            echo '<div class="col s12">' . csf_add_field( array(
              'id'        => '_class_svg_size',
              'name'      => '_class_svg_size',
              'type'      => 'slider',
              'title'     => esc_html__( 'SVG Size', 'codevz' ),
              'options'   => array( 'unit' => '', 'step' => 1, 'min' => 1, 'max' => 10 ),
            ), '' ) . '</div>';

            echo '<div class="col s12">' . csf_add_field( array(
              'id'        => '_class_svg_color',
              'name'      => '_class_svg_color',
              'type'      => 'color_picker',
              'title'     => esc_html__( 'SVG Color', 'codevz' )
            ), '' ) . '</div>';

            echo '</div>'; // SVG

            // Background
            echo '<div class="cz_sk_row cz_sk_bg_row clr">';
            echo '<h4>Background</h4><div class="col s12">' . csf_add_field( array(
              'id'      => 'background',
              'name'    => 'background',
              'type'    => 'background',
              'title'   => ''
            ), '' ) . '</div></div>';

            // Sizes
            echo '<div class="cz_sk_row cz_sk_size_row clr">';
            echo '<h4>Sizes</h4>';
            echo '<div class="col s6">' . csf_add_field( array(
              'id'      => 'width',
              'name'    => 'width',
              'type'    => 'slider',
              'title'   => esc_html__( 'Width', 'codevz' ),
              'options' => array( 'unit' => 'px', 'step' => 1, 'min' => 0, 'max' => 1200 ),
            ), '' ) . '</div>';
            echo '<div class="col s6">' .csf_add_field( array(
              'id'      => 'height',
              'name'    => 'height',
              'type'    => 'slider',
              'title'   => esc_html__( 'Height', 'codevz' ),
              'options' => array( 'unit' => 'px', 'step' => 1, 'min' => 0, 'max' => 600 ),
            ), '' ) . '</div>';
            echo '</div>'; // Sizes

            // Spaces
            echo '<div class="cz_sk_row cz_sk_spaces_row clr">';
            echo '<h4>Spaces</h4>';
            echo '<div class="col s12">' . csf_add_field( array(
              'id'        => 'padding',
              'name'      => 'padding',
              'type'      => 'codevz_sizes',
              'title'     => esc_html__( 'Padding', 'codevz' ),
              'desc'      => esc_html__( 'Inner gap', 'codevz' ),
              'options'   => array( 'unit' => 'px', 'step' => 1, 'min' => 0, 'max' => 100 ),
              'help'      => esc_html__( 'Creating space around an element, INSIDE of any defined margins and borders. Can set using px, %, em, ...', 'codevz' )
            ), '' ) . '</div>';
            echo '<div class="clr cz_hr"></div>';
            echo '<div class="col s12">' . csf_add_field( array(
              'id'        => 'margin',
              'name'      => 'margin',
              'type'      => 'codevz_sizes',
              'title'     => esc_html__( 'Margin', 'codevz' ),
              'desc'      => esc_html__( 'Outer gap', 'codevz' ),
              'options'   => array( 'unit' => 'px', 'step' => 1, 'min' => -50, 'max' => 100 ),
              'help'      => esc_html__( 'Creating space around an element, OUTSIDE of any defined borders. Can set using px, %, em, auto, ...', 'codevz' )
            ), '' ) . '</div>';
            echo '</div>'; // Spaces

            // Border
            echo '<div class="cz_sk_row cz_sk_border_row clr">';
            echo '<h4>Border</h4>';
            echo '<div class="col s12">' . csf_add_field( array(
              'id'        => 'border-style',
              'name'      => 'border-style',
              'type'      => 'select',
              'title'     => esc_html__( 'Border', 'codevz' ),
              'options'   => array(
                'solid'     => esc_html__( 'Solid', 'codevz' ),
                'dotted'    => esc_html__( 'Dotted', 'codevz' ),
                'dashed'    => esc_html__( 'Dashed', 'codevz' ),
                'double'    => esc_html__( 'Double', 'codevz' ),
                'none'      => esc_html__( 'None', 'codevz' ),
              ),
              'default_option' => esc_html__( 'Select', 'codevz'),
              'attributes' => array(
                'data-depend-id' => 'border-style',
              ),
            ), '' );

            echo csf_add_field( array(
              'id'        => 'border-width',
              'name'      => 'border-width',
              'type'      => 'codevz_sizes',
              'options'   => array( 'unit' => 'px', 'step' => 1, 'min' => 1, 'max' => 100 ),
              'title'     => esc_html__( 'Border Width', 'codevz' ),
              'help'      => esc_html__( 'Lines around element', 'codevz' ),
              'dependency' => array( 'border-style|border-style', '!=|!=', '|none' ),
            ), '' );

            echo csf_add_field( array(
              'id'        => 'border-color',
              'name'      => 'border-color',
              'type'      => 'color_picker',
              'title'     => esc_html__( 'Border Color', 'codevz' ),
              'dependency' => array( 'border-style|border-style', '!=|!=', '|none' ),
            ), '' );

            echo csf_add_field( array(
              'id'        => 'border-right-color',
              'name'      => 'border-right-color',
              'type'      => 'color_picker',
              'title'     => esc_html__( 'Border right color', 'codevz' )
            ), '' );

            echo csf_add_field( array(
              'id'        => 'border-radius',
              'name'      => 'border-radius',
              'type'      => 'slider',
              'title'     => esc_html__( 'Radius', 'codevz' ),
              'help'      => esc_html__( 'Generate the arc for lines around element, e.g. 10px or manually set with this four positions respectively: <br />Top Right Bottom Left <br/><br/>e.g. 10px 10px 10px 10px', 'codevz' )
            ), '' ) . '</div>';
            echo '</div>'; // Border

            // Shadows
            echo '<div class="cz_sk_row cz_sk_shadow_row clr">';
            echo '<h4>Shadows</h4>';
            echo '<div class="col s12">' . csf_add_field( array(
              'id'        => 'box-shadow',
              'name'      => 'box-shadow',
              'type'      => 'codevz_box_shadow',
              'title'     => esc_html__( 'Box Shadow', 'codevz' ),
              'desc'      => esc_html__( 'All required', 'codevz' )
            ), '' ) . '</div>';
            echo '<div class="clr cz_hr"></div>';
            echo '<div class="col s12">' . csf_add_field( array(
              'id'        => 'text-shadow',
              'name'      => 'text-shadow',
              'type'      => 'codevz_box_shadow',
              'title'     => esc_html__( 'Text Shadow', 'codevz' ),
              'desc'      => esc_html__( 'All required', 'codevz' )
            ), '' ) . '</div>';
            echo '</div>'; // Shadows

            // Advanced
            echo '<div class="cz_sk_row cz_sk_advance_row clr">';
            echo '<h4>More Settings</h4>';
            echo '<div class="col s6">' . csf_add_field( array(
              'id'      => 'display',
              'name'    => 'display',
              'type'    => 'select',
              'title'   => esc_html__( 'Display', 'codevz' ),
              'options' => array(
                'none'                => esc_html__( 'None', 'codevz' ),
                'block'               => esc_html__( 'Block', 'codevz' ),
                'inline'              => esc_html__( 'Inline', 'codevz' ),
                'inline-block'        => esc_html__( 'Inline Block', 'codevz' ),
                'table'               => esc_html__( 'Table', 'codevz' ),
                'table-cell'          => esc_html__( 'Table Cell', 'codevz' ),
                'table-caption'       => esc_html__( 'Table Caption', 'codevz' ),
                'table-header-group'  => esc_html__( 'Table Header', 'codevz' ),
                'table-footer-group'  => esc_html__( 'Table Footer', 'codevz' ),
                'unset'               => esc_html__( 'Unset', 'codevz' ),
                'initial'             => esc_html__( 'Initial', 'codevz' ),
                'inherit'             => esc_html__( 'Inherit', 'codevz' ),
              ),
              'default_option' => esc_html__( 'Default', 'codevz'),
            ), '' ) . '</div>';
            echo '<div class="col s6">' . csf_add_field( array(
              'id'      => 'position',
              'name'    => 'position',
              'type'    => 'select',
              'title'   => esc_html__( 'Position', 'codevz' ),
              'options' => array(
                'static'        => esc_html__( 'Static', 'codevz' ),
                'relative'      => esc_html__( 'Relative', 'codevz' ),
                'absolute'      => esc_html__( 'Absolute', 'codevz' ),
                'initial'       => esc_html__( 'Initial', 'codevz' ),
                'unset'         => esc_html__( 'Unset', 'codevz' ),
                'initial'       => esc_html__( 'Initial', 'codevz' ),
                'inherit'       => esc_html__( 'Inherit', 'codevz' ),
              ),
              'default_option' => esc_html__( 'Default', 'codevz'),
            ), '' ) . '</div>';

            echo '<div class="clr cz_hr"></div>';

            echo '<div class="col s6">' . csf_add_field( array(
              'id'        => 'top',
              'name'      => 'top',
              'type'      => 'slider',
              'title'     => esc_html__( 'Top', 'codevz' ),
              'options'   => array( 'unit' => 'px', 'step' => 1, 'min' => -100, 'max' => 200 )
            ), '' ) . '</div>';

            echo '<div class="col s6">' . csf_add_field( array(
              'id'        => 'right',
              'name'      => 'right',
              'type'      => 'slider',
              'title'     => esc_html__( 'Right', 'codevz' ),
              'options'   => array( 'unit' => 'px', 'step' => 1, 'min' => -100, 'max' => 200 )
            ), '' ) . '</div>';

            echo '<div class="col s6">' . csf_add_field( array(
              'id'        => 'bottom',
              'name'      => 'bottom',
              'type'      => 'slider',
              'title'     => esc_html__( 'Bottom', 'codevz' ),
              'options'   => array( 'unit' => 'px', 'step' => 1, 'min' => -100, 'max' => 200 )
            ), '' ) . '</div>';

            echo '<div class="col s6">' . csf_add_field( array(
              'id'        => 'left',
              'name'      => 'left',
              'type'      => 'slider',
              'title'     => esc_html__( 'Left', 'codevz' ),
              'options'   => array( 'unit' => 'px', 'step' => 1, 'min' => -100, 'max' => 200 )
            ), '' ) . '</div>';

            echo '<div class="clr cz_hr"></div>';

            echo '<div class="col s6">' . csf_add_field( array(
              'id'      => 'overflow',
              'name'    => 'overflow',
              'type'    => 'select',
              'title'   => esc_html__( 'Overflow', 'codevz' ),
              'options' => array(
                'visible'   => esc_html__( 'Visible', 'codevz' ),
                'hidden'    => esc_html__( 'Hidden', 'codevz' ),
                'unset'     => esc_html__( 'Unset', 'codevz' ),
                'initial'   => esc_html__( 'Initial', 'codevz' ),
                'inherit'   => esc_html__( 'Inherit', 'codevz' ),
              ),
              'default_option' => esc_html__( 'Default', 'codevz'),
            ), '' ) . '</div>';

            echo '<div class="col s6">' . csf_add_field( array(
              'id'      => 'float',
              'name'    => 'float',
              'type'    => 'select',
              'title'   => esc_html__( 'Float', 'codevz' ),
              'options' => array(
                'left'      => esc_html__( 'Left', 'codevz' ),
                'right'     => esc_html__( 'Right', 'codevz' ),
                'none'      => esc_html__( 'None', 'codevz' ),
                'unset'     => esc_html__( 'Unset', 'codevz' ),
                'initial'   => esc_html__( 'Initial', 'codevz' ),
                'inherit'   => esc_html__( 'Inherit', 'codevz' ),
              ),
              'default_option' => esc_html__( 'Select', 'codevz'),
            ), '' ) . '</div>';

            echo '<div class="col s6">' . csf_add_field( array(
              'id'      => 'z-index',
              'name'    => 'z-index',
              'type'    => 'select',
              'title'   => esc_html__( 'z-index', 'codevz' ),
              'options' => array(
                '-2'  => '-2',
                '-1'  => '-1',
                '0'   => '0',
                '1'   => '1',
                '2'   => '2',
                '3'   => '3',
                '4'   => '4',
                '5'   => '5',
                '6'   => '6',
                '7'   => '7',
                '8'   => '8',
                '9'   => '9',
                '10'  => '10',
                '99'  => '99',
                '999' => '999',
                '9999' => '9999',
              ),
              'default_option' => esc_html__( 'Select', 'codevz'),
            ), '' ) . '</div>';

            echo '<div class="col s6">' . csf_add_field( array(
              'id'      => 'opacity',
              'name'    => 'opacity',
              'type'    => 'select',
              'title'   => esc_html__( 'Opacity', 'codevz' ),
              'options' => array(
                '1'      => '1',
                '0.95'   => '0.95',
                '0.9'    => '0.9',
                '0.85'   => '0.85',
                '0.8'    => '0.8',
                '0.75'   => '0.75',
                '0.7'    => '0.7',
                '0.65'   => '0.65',
                '0.6'    => '0.6',
                '0.55'   => '0.55',
                '0.5'    => '0.5',
                '0.45'   => '0.45',
                '0.4'    => '0.4',
                '0.35'   => '0.35',
                '0.3'    => '0.3',
                '0.25'   => '0.25',
                '0.2'    => '0.2',
                '0.15'   => '0.15',
                '0.1'    => '0.1',
                '0.05'   => '0.05',
                '0.0'    => '0.0',
              ),
              'default_option' => esc_html__( 'Select', 'codevz'),
            ), '' ) . '</div>';

            echo '<div class="col s6">' . csf_add_field( array(
              'id'      => 'blur',
              'name'    => 'blur',
              'type'    => 'slider',
              'options' => array( 'unit' => 'px', 'step' => 1, 'min' => 0, 'max' => 20 ),
              'title'   => esc_html__( 'Blur', 'codevz' )
            ), '' ) . '</div>';

            echo '<div class="col s6">' . csf_add_field( array(
              'id'      => 'grayscale',
              'name'    => 'grayscale',
              'type'    => 'select',
              'title'   => esc_html__( 'Grayscale', 'codevz' ),
              'options' => array(
                '100%'      => esc_html__( 'Yes', 'codevz' ),
                '0%'        => esc_html__( 'No', 'codevz' ),
              ),
              'default_option' => esc_html__( 'Select', 'codevz'),
            ), '' ) . '</div>';

            echo '<div class="col s6">' . csf_add_field( array(
              'id'        => 'transform',
              'name'      => 'transform',
              'type'      => 'slider',
              'title'     => esc_html__( 'Rotate', 'codevz' ),
              'options'   => array( 'unit' => 'deg', 'step' => 1, 'min' => 0, 'max' => 360 ),
            ), '' ) . '</div>';
            echo '</div>'; // Advanced Settings

            // Custom
            echo '<div class="cz_sk_row cz_sk_custom clr">';
            echo '<h4>Custom CSS</h4>';
            echo '<div class="col s12">' . csf_add_field( array(
              'id'      => 'custom',
              'name'    => 'custom',
              'type'    => 'textarea',
              'title'   => '',
              'attributes' => array(
                'placeholder' => 'property: value;',
                'rows' => '5',
                'cols' => '5',
                ),
              'help'    => esc_html__( 'You can add custom css for this element.', 'codevz' ) . '<br /><br />e.g.<br /><br />transform: rotate(10deg);'
            ), '' ) . '</div>';
            echo '</div>'; // Custom

            // Custom RTL
            echo '<div class="cz_sk_row cz_custom_rtl clr">';
            echo '<h4>RTL Mode</h4>';
            echo '<div class="col s12">' . csf_add_field( array(
              'id'      => 'rtl',
              'name'    => 'rtl',
              'type'    => 'textarea',
              'title'   => '',
              'attributes' => array(
                'placeholder' => 'property: value;',
                'rows' => '5',
                'cols' => '5',
              ),
            ), '' ) . '</div>';
            echo '</div>';

          ?>
        </form>
      </div>
    </div>
    <?php
  }
  add_action( 'admin_footer', 'codevz_modal_style_kit' );
  add_action( 'customize_controls_print_footer_scripts', 'codevz_modal_style_kit' );
}

/**
 * Header prest modal
add_action( 'admin_footer', 'codevz_modal_header_preset' );
add_action( 'customize_controls_print_footer_scripts', 'codevz_modal_header_preset' );
function codevz_modal_header_preset() { ?>
  <div id="csf-modal-header-preset" class="csf-modal csf-modal-header-preset">
    <div class="csf-modal-table">
      <div class="csf-modal-table-cell">
        <div class="csf-modal-overlay"></div>
        <div class="csf-modal-inner">
          <div class="csf-modal-title">
            <?php esc_html_e( 'Header Preset', 'codevz' ); ?>
            <div class="csf-modal-close csf-header-preset-close"></div>
          </div>
          <div class="csf-modal-content"><div class="csf-header-preset-loading"><?php esc_html_e( 'Loading ...', 'codevz' ); ?></div></div>
        </div>
      </div>
    </div>
  </div>
<?php }

add_action( 'wp_ajax_codevz_header_preset', 'codevz_header_preset' );
function codevz_header_preset() {
  echo '<a class="cz_header_preset" data-header="1" href="#">Header 1</a><br />';
  echo '<a class="cz_header_preset" data-header="2" href="#">Header 2</a><br />';
  echo '<a class="cz_header_preset" data-header="3" href="#">Header 3</a><br />';
  die();
}
 */