<?php if ( ! defined( 'ABSPATH' ) ) { die; }
/**
 *
 * Options, Metabox, Taxonomy
 * 
 * @author Codevz
 * @link http://codevz.com/
 *
 */
/*
array(
	'id' 			=> 'sk_widgets_title',
	'type' 			=> 'sk',
	'setting_args' 	=> array( 'transport' => 'postMessage' ),
	'setting_sk' 	=> array(
		'title' 		=> esc_html__( 'Widgets title', 'codevz' ),
		'mobile' 		=> 1,
		'selector' 		=> '.widget > h4',
		'hover' 		=> '.widget > h4:hover',
		'fields' 		=> array( 'color,background,font-size,text-align,border' ),
	),
),
*/
if ( ! class_exists( 'Codevz_Options' ) ) {
	class Codevz_Options {

		/**
		 *
		 * Instance
		 *
		 * @access private
		 * @var class
		 *
		 */
		private static $instance = null;

		/**
		 *
		 * StyleKit button advanced mode
		 *
		 * @var string
		 *
		 */
		private static $sk_advanced;

		public function __construct() {

			// Advanced SK switcher
			self::$sk_advanced = '<div class="cz_advanced_tab"><span class="cz_s cz_active">' . esc_html__( 'Simple', 'codevz' ) . '</span><span class="cz_a">' . esc_html__( 'Advanced', 'codevz' ) . '</span></div>';

			// Options & Metabox
			add_action( 'init', function() {
				if ( class_exists( 'CSF' ) ) {
					CSF_Customize::instance( self::options(), Codevz_Plus::$options_id );
					CSF_Metabox::instance( self::metabox() );

					// Taxonomy Meta
					$tax_meta = array();
					foreach ( self::post_types( array( 'post' ) ) as $cpt ) {
						$tax_meta[] = array(
							'id'       => 'codevz_cat_meta',
							'taxonomy' => ( $cpt === 'post' ) ? 'category' : $cpt . '_cat',
							'fields'   => array(
								array(
								  'id'    => 'color',
								  'type'  => 'color_picker',
								  'title' => esc_html__( 'Color Scheme', 'codevz' )
								)
							)
						);
					}
					CSF_Taxonomy::instance( $tax_meta );
				}
			}, 999 );

			// Save customize settings
			add_action( 'customize_save_after', array( __CLASS__, 'codevz_customize_save_after' ) );

			// Enqueue inline styles
			if ( ! isset( $_POST['vc_inline'] ) ) {
				add_action( 'wp_enqueue_scripts', function() {

					// Single page CSS
					if ( is_singular() && isset( Codevz_Plus::$post->ID ) ) {
						$meta = get_post_meta( Codevz_Plus::$post->ID, 'codevz_single_page_css', 1 );
						if ( $meta ) {
							wp_add_inline_style( 'codevz-plugin', str_replace( 'Array', '', $meta ) );
						}
					}

					// Options json for customize preview
					if ( is_customize_preview() ) {
						wp_add_inline_style( 'codevz-plugin', self::css_out( 1 ) );
						self::codevz_wp_footer_options_json();
					}
				}, 999 );
			}

			// Update single page CSS
			add_action( 'save_post', array( __CLASS__, 'codevz_save_post' ) );
		}

		/**
		 *
		 * Instance
		 *
		 */
		public static function instance() {
			if ( self::$instance === null ) {
				self::$instance = new self();
			}
			
			return self::$instance;
		}

		/**
		 *
		 * Get list of post types created via customizer
		 * 
		 * @return array
		 *
		 */
		public static function post_types( $a = array() ) {
			$a = array_merge( $a, (array) get_option( 'codevz_post_types' ) );
			$a[] = 'portfolio';

			// Custom post type UI
			if ( function_exists( 'cptui_get_post_type_slugs' ) ) {
				$cptui = cptui_get_post_type_slugs();
				if ( is_array( $cptui ) ) {
					$a = wp_parse_args( $cptui, $a );
				}
			}

			return $a;
		}

		/**
		 *
		 * Update single page CSS as metabox 'codevz_single_page_css'
		 * 
		 * @return string
		 * 
		 */
		public static function codevz_save_post( $post_id = '' ) {
			if ( empty( $post_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
				return;
			}

			$meta = self::css_out( 0, Codevz_Plus::meta( $post_id ) );
			delete_post_meta( $post_id, 'codevz_single_page_css' );
			if ( $meta ) {
				update_post_meta( $post_id, 'codevz_single_page_css', $meta );
			}
		}

		/**
		 *
		 * Get post type in admin area
		 * 
		 * @return string
		 *
		 */
		public static function get_post_type_admin() {
			global $post, $typenow, $current_screen;

			if ( $post && $post->post_type ) {
				return $post->post_type;
			} else if ( $typenow ) {
				return $typenow;
			} else if ( $current_screen && $current_screen->post_type ) {
				return $current_screen->post_type;
			} else if ( isset( $_REQUEST['post_type'] ) ) {
				return sanitize_key( $_REQUEST['post_type'] );
			} else if ( isset( $_REQUEST['post'] ) ) {
				return get_post_type( $_REQUEST['post'] );
			}

			return null;
		}

		/**
		 *
		 * Generate styles when customizer saves
		 * 
		 * @return array
		 *
		 */
		public static function css_out( $is_customize_preview = 0, $single_page = 0 ) {
			$out = $dynamic = $dynamic_tablet = $dynamic_mobile = '';
			$fonts = array();

			// Options
			$opt = $single_page ? (array) $single_page : (array) get_option( Codevz_Plus::$options_id );

			// Generating styles
			foreach ( $opt as $id => $val ) {
				if ( $val && Codevz_Plus::contains( $id, '_css_' ) ) {
					if ( is_array( $val ) || Codevz_Plus::contains( $val, '[' ) ) {
						continue;
					}

					// Temp fix for live customizer fonts generation
					if ( $is_customize_preview ) {
						if ( Codevz_Plus::contains( $val, 'font-family' ) ) {
							$fonts[]['font'] = $val;
						}
						continue;
					}

					// CSS Selector
					$selector = Codevz_Plus::contains( $id, '_css_page_body_bg' ) ? 'html,body' : self::get_selector( $id );
					if ( $single_page ) {
						$page_id = '.cz-page-' . ( isset( $_POST['post_id'] ) ? $_POST['post_id'] : Codevz_Plus::$post->ID );
						$selector = ( $selector === 'html,body' ) ? 'body' . $page_id : $page_id . ' ' . $selector;
						if ( Codevz_Plus::contains( $selector, ',' ) ) {
							$selector = str_replace( ',', ',' . $page_id . ' ', $selector );
						}
					}

					// Fix custom css
					$val = str_replace( 'CDVZ', '', $val );

					// RTL
					if ( Codevz_Plus::contains( $val, 'RTL' ) ) {
						$rtl = Codevz_Plus::get_string_between( $val, 'RTL', 'RTL' );
						$val = str_replace( array( $rtl, 'RTL' ), '', $val );
					}

					// Set font family
					if ( Codevz_Plus::contains( $val, 'font-family' ) ) {
						$fonts[]['font'] = $val;

						// Extract font + params && Fix font for CSS
						$font = $o_font = Codevz_Plus::get_string_between( $val, 'font-family:', ';' );
						$font = str_replace( '=', ':', $font );
						
						if ( Codevz_Plus::contains( $font, ':' ) ) {
							$font = explode( ':', $font );
							if ( ! empty( $font[0] ) ) {
								$val = str_replace( $o_font, "'" . $font[0] . "'", $val );
							}
						} else {
							$val = str_replace( $font, "'" . $font . "'", $val );
						}
					}

					// Remove unwanted in css
					if ( Codevz_Plus::contains( $val, '_class_' ) ) {
						$val = preg_replace( '/_class_[\s\S]+?;/', '', $val );
					}

					// Fix sticky styles priority
					if ( $id === '_css_container_header_5' || $id === '_css_row_header_5' ) {
						$val = str_replace( '!important', '', $val );
						$val = str_replace( ';', ' !important;', $val );
					}

					// Append to out
					if ( ! empty( $val ) && ! empty( $selector ) ) {
						if ( Codevz_Plus::contains( $id, '_tablet' ) ) {
							$dynamic_tablet .= $selector . '{' . $val . '}';
						} else if ( Codevz_Plus::contains( $id, '_mobile' ) ) {
							$dynamic_mobile .= $selector . '{' . $val . '}';
						} else {
							$dynamic .= $selector . '{' . $val . '}';
						}
					}

					// RTL
					if ( ! empty( $rtl ) ) {
						$sp = Codevz_Plus::contains( $selector, array( '.cz-cpt-', '.cz-page-', '.home', 'body', '.woocommerce' ) ) ? '' : ' ';
						$dynamic .= '.rtl' . $sp . preg_replace( '/,\s+|,/', ',.rtl' . $sp, $selector ) . '{' . $rtl . '}';
					}
					$rtl = 0;
				}
			}

			// Final out
			if ( ! $is_customize_preview ) {
				$dynamic = $dynamic ? "\n\n/* Dynamic " . ( $single_page ? 'Single' : '' ) . " */" . $dynamic : '';
				if ( $single_page && Codevz_Plus::option( 'responsive' ) ) {
					$dynamic .= $dynamic_tablet ? '@media screen and (max-width:768px){' . $dynamic_tablet . '}' : '';
					$dynamic .= $dynamic_mobile ? '@media screen and (max-width:480px){' . $dynamic_mobile . '}' : '';
				}
			}

			$dynamic = str_replace( ';}', '}', $dynamic );

			// Single pages
			if ( $single_page ) {
				return $dynamic;
			}

			// Site Width & Boxed
			$site_width = empty( $opt['site_width'] ) ? 0 : $opt['site_width'];
			if ( $site_width ) {
				if ( empty( $opt['boxed'] ) ) {
					$out .= '.row{width: ' . $site_width . '}';
				} else if ( $opt['boxed'] == '2' ) {
					$out .= '.layout_2,.layout_2 .cz_fixed_footer,.layout_2 .header_is_sticky{width: ' . $site_width . '}.layout_2 .row{width: calc(' . $site_width . ' - 10%)}';
				} else {
					$out .= '.layout_1,.layout_1 .cz_fixed_footer,.layout_1 .header_is_sticky{width: ' . $site_width . '}.layout_1 .row{width: calc(' . $site_width . ' - 10%)}';
				}
			}

			// Responsive
			if ( ! empty( $opt['responsive'] ) ) {
				$bxw = empty( $opt['boxed'] ) ? '1170px' : '1300px';
				$rs1 = empty( $opt['site_width'] ) ? $bxw : ( Codevz_Plus::contains( $opt['site_width'], '%' ) ? '4000px' : $opt['site_width'] );
				$rsc = isset( $opt['breakpoint_2_custom_css'] ) ? $opt['breakpoint_2_custom_css'] : '';
				$rsc3 = isset( $opt['breakpoint_3_custom_css'] ) ? $opt['breakpoint_3_custom_css'] : '';

				$lt = $pt = $mm = '';
				$header_css = '.header_1,.header_2,.header_3,.header_5,.fixed_side{display: none !important}.header_4,.Corpse_Sticky.cz_sticky_corpse_for_header_4{display: block !important}.header_onthe_cover:not(.header_onthe_cover_dt):not(.header_onthe_cover_all){margin-top: 0 !important}';

				if ( empty( $opt['mobile_header'] ) || ( isset( $opt['mobile_header'] ) && $opt['mobile_header'] === 'pt' ) ) {
					$pt = $header_css;
				} else if ( $opt['mobile_header'] === 'lt' ) {
					$lt = $header_css;
				} else {
					$mm = $header_css;
				}

				$dynamic .= "\n\n/* Responsive */" . '@media screen and (max-width:' . $rs1 . '){#layout{width:100%!important}#layout.layout_1,#layout.layout_2{width:95%!important}.row{width:90% !important;padding:0}blockquote{padding:20px}.slick-slide{margin:0!important}footer .elms_center,footer .elms_left,footer .elms_right,footer .have_center .elms_left, footer .have_center .elms_center, footer .have_center .elms_right{float:none;display:table;text-align:center;margin: 0 auto;flex:unset}}
	@media screen and (max-width:1025px){' . $lt . '.header_1,.header_2,.header_3{width: 100%}#layout.layout_1,#layout.layout_2{width:94%!important}#layout.layout_1 .row,#layout.layout_2 .row{width:90% !important}}
	@media screen and (max-width:768px){' . $pt . 'body,#layout{padding: 0 !important;margin: 0 !important}body{overflow-x:hidden}.inner_layout,#layout.layout_1,#layout.layout_2,.col,.cz_five_columns > .wpb_column,.cz_five_columns > .vc_vc_column{width:100% !important;margin:0 !important;border-radius:0}.hidden_top_bar,.fixed_contact,.cz_process_road_a,.cz_process_road_b{display:none!important}.cz_parent_megamenu>.sub-menu{margin:0!important}.is_fixed_side{padding:0!important}.cz_tabs_is_v .cz_tabs_nav,.cz_tabs_is_v .cz_tabs_content{width: 100% !important;margin-bottom: 20px}.wpb_column {margin-bottom: 20px}.cz_fixed_footer {position: static !important}' . $rsc . '.Corpse_Sticky,.hide_on_tablet{display:none !important}header i.hide,.show_on_tablet{display:block}.cz_grid_item:not(.slick-slide){width:50% !important}.cz_grid_item img{width:auto !important;margin: 0 auto}.cz_mobile_text_center, .cz_mobile_text_center *{text-align:center !important;float:none !important;margin-right:auto;margin-left:auto}.cz_mobile_btn_center{float:none !important;margin-left: auto !important;margin-right: auto !important;display: table !important;text-align: center !important}.vc_row[data-vc-stretch-content] .vc_column-inner[class^=\'vc_custom_\'],.vc_row[data-vc-stretch-content] .vc_column-inner[class*=\' vc_custom_\'] {padding:20px !important;}.wpb_column {margin-bottom: 0 !important;}.vc_row.no_padding .vc_column_container > .vc_column-inner, .vc_row.nopadding .vc_column_container > .vc_column-inner{padding:0 !important;}.cz_posts_container article > div{height: auto !important}.cz_split_box_left > div, .cz_split_box_right > div {width:100%;float:none}.woo-col-3.woocommerce ul.products li.product, .woo-col-3.woocommerce-page ul.products li.product, .woo-related-col-3.woocommerce ul.products .related li.product, .woo-related-col-3.woocommerce-page ul.products .related li.product {width: calc(100% / 2 - 2.6%)}.search_style_icon_full .search{width:86%;top:80px}.vc_row-o-equal-height .cz_box_front_inner, .vc_row-o-equal-height .cz_eqh, .vc_row-o-equal-height .cz_eqh > div, .vc_row-o-equal-height .cz_eqh > div > div, .vc_row-o-equal-height .cz_eqh > div > div > div, .cz_posts_equal > .clr{display:block !important}.cz_a_c.cz_timeline_container:before {left: 0}.cz_timeline-i i {left: 0;transform: translateX(-50%)}.cz_a_c .cz_timeline-content {margin-left: 50px;width: 70%;float: left}.cz_a_c .cz_timeline-content .cz_date{position: static;text-align: left}.cz_posts_template_13 article,.cz_posts_template_14 article{width:100%}.center_on_mobile,.center_on_mobile *{text-align:center !important;float:none !important}.center_on_mobile .cz_wh_left, .center_on_mobile .cz_wh_right {display:block}.center_on_mobile .item_small > a{display:inline-block;margin:2px 0}.center_on_mobile img,.center_on_mobile .cz_image > div{display:table !important;margin-left: auto !important;margin-right: auto !important}.tac_in_mobile{text-align:center !important;float:none !important;display:table;margin-left:auto !important;margin-right:auto !important}' . $dynamic_tablet . '}
	@media screen and (max-width:480px){' . $mm . '.cz_grid_item img{width:auto !important}.hide_on_mobile,.show_only_tablet,.fixed_contact,.cz_cart_items{display:none}header i.hide,.show_on_mobile{display:block}.offcanvas_area{width:80%}.cz_tab_a,.cz_tabs_content,.cz_tabs_is_v .cz_tabs_nav{box-sizing:border-box;display: block;width: 100% !important;margin-bottom: 20px}.woocommerce ul.products li.product, .woocommerce-page ul.products li.product, .woocommerce-page[class*=columns-] ul.products li.product, .woocommerce[class*=columns-] ul.products li.product,.wpcf7-form p,.cz_default_loop,.cz_post_image,.cz_post_chess_content{width: 100% !important}.cz_post_chess_content{position:static;transform:none}.cz_post_image,.cz_default_grid{width: 100%;margin-bottom:30px !important}.wpcf7-form p {width: 100% !important;margin: 0 0 10px !important}[class^="cz_parallax_"],[class*=" cz_parallax_"]{transform:none !important}th, td {padding: 1px}dt {width: auto}dd {margin: 0}pre{width: 90%}.woocommerce .woocommerce-result-count, .woocommerce-page .woocommerce-result-count,.woocommerce .woocommerce-ordering, .woocommerce-page .woocommerce-ordering{float:none;text-align:center;width:100%}.woocommerce #coupon_code, .coupon input.button {width:100% !important;margin:0 0 10px !important}span.wpcf7-not-valid-tip{left:auto}.wpcf7-not-valid-tip:after{right:auto;left:-41px}.cz_video_popup div{width:fit-content}.cz_grid_item:not(.slick-slide){width:100% !important;margin: 0 !important;height:auto !important}.cz_grid_item > div{margin:0 0 10px !important}.cz_grid{width:100% !important;margin:0 !important}.next_prev li {float:none !important;width:100% !important;border: 0 !important;margin-bottom:30px !important}.services.left .service_custom,.services.right .service_custom,.services.left .service_img,.services.right .service_img{float:none;margin:0 auto 20px auto !important;display:table}.services div.service_text,.services.right div.service_text{padding:0 !important;text-align:center !important}.header_onthe_cover_dt{margin-top:0 !important}.alignleft,.alignright{float:none;margin:0 auto 30px}.woocommerce li.product{margin-bottom:30px !important}.woocommerce #reviews #comments ol.commentlist li .comment-text{margin:0 !important}#comments .commentlist li .avatar{left:-20px !important}.services .service_custom i{left: 50%;transform: translateX(-50%)}#commentform > p{display:block;width:100%}blockquote,.blockquote{width:100% !important;box-sizing:border-box;text-align:center;display:table !important;margin:0 auto 30px !important;float:none !important}.cz_related_post{margin-bottom: 30px !important}.right_br_full_container .lefter, .right_br_full_container .righter,.right_br_full_container .breadcrumbs{width:100%;text-align:center}a img.alignleft,a img.alignright{margin:0 auto 30px;display:block;float:none}.cz_popup_in{max-height:85%!important;max-width:90%!important;min-width:0;animation:none;box-sizing:border-box;left:5%;transform:translate(0,-50%)}.rtl .sf-menu > .cz{width:100%}.cz_2_btn a {box-sizing: border-box}.cz_has_year{margin-left:0 !important}.cz_history_1 > span:first-child{position:static !important;margin-bottom:10px !important;display:inline-block}.search-form .search-submit{margin: 0}.page_item_has_children .children, ul.cz_circle_list {margin: 8px 0 8px 10px}ul, .widget_nav_menu .sub-menu, .widget_categories .children, .page_item_has_children .children, ul.cz_circle_list{margin-left: 10px}.dwqa-questions-list .dwqa-question-item{padding: 20px 20px 20px 90px}.dwqa-question-content, .dwqa-answer-content{padding:0}.cz_hexagon{position: relative;margin: 0 auto 30px}.cz_gallery_badge{right:-10px}.woocommerce table.shop_table_responsive tr td,.woocommerce-page table.shop_table_responsive tr td{display:flow-root !important}.quantity{float:right}.wpb_animate_when_almost_visible{animation-delay:initial !important}' . $rsc3 . $dynamic_mobile . '}';
			}
			
			// Fixed Border for Body
			if ( ! empty( $opt['_css_body'] ) && Codevz_Plus::contains( $opt['_css_body'], 'border-width' ) && Codevz_Plus::contains( $opt['_css_body'], 'border-color' ) ) {
				$out .= '.cz_fixed_top_border, .cz_fixed_bottom_border {border-top: ' . Codevz_Plus::get_string_between( $opt['_css_body'], 'border-width:', ';' ) . ' solid ' . Codevz_Plus::get_string_between( $opt['_css_body'], 'border-color:', ';' ) . '}';
			}

			// Site Colors
			if ( ! empty( $opt['site_color'] ) ) {
				$site_color = $opt['site_color'];
				$out .= "\n\n/* Theme color */" . 'a:hover, .sf-menu > .cz.current_menu > a, .sf-menu > .cz > .current_menu > a, .sf-menu > .current-menu-parent > a {color: ' . $site_color . '} 
	form button, .button,.sf-menu > .cz > a:before,.sf-menu > .cz > a:before,.widget_product_search #searchsubmit, .post-password-form input[type="submit"], .wpcf7-submit, .submit_user, 
	#commentform #submit, .commentlist li.bypostauthor > .comment-body:after,.commentlist li.comment-author-admin > .comment-body:after, 
	.woocommerce input.button.alt.woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button,.woocommerce .woocommerce-error .button, 
	.woocommerce .woocommerce-info .button, .woocommerce .woocommerce-message .button, .woocommerce-page .woocommerce-error .button, .woocommerce-page .woocommerce-info .button, 
	.woocommerce-page .woocommerce-message .button,#add_payment_method table.cart input, .woocommerce-cart table.cart input:not(.input-text), .woocommerce-checkout table.cart input,
	.woocommerce input.button:disabled, .woocommerce input.button:disabled[disabled],#add_payment_method table.cart input, #add_payment_method .wc-proceed-to-checkout a.checkout-button, 
	.woocommerce-cart .wc-proceed-to-checkout a.checkout-button, .woocommerce-checkout .wc-proceed-to-checkout a.checkout-button,.woocommerce #payment #place_order, .woocommerce-page #payment #place_order,.woocommerce input.button.alt,
	.woocommerce #respond input#submit.alt:hover, .pagination .current, .pagination > b, .pagination a:hover, .page-numbers .current, .page-numbers a:hover, .pagination .next:hover, 
	.pagination .prev:hover, input[type=submit], .sticky:before, .commentlist li.comment-author-admin .fn, .woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, 
	.woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover, .woocommerce-MyAccount-navigation a:hover, .woocommerce-MyAccount-navigation .is-active a,
	input[type=submit],input[type=button],.cz_header_button,.cz_default_portfolio a, .dwqa-questions-footer .dwqa-ask-question a,
	.cz_readmore, .woocommerce nav.woocommerce-pagination ul li a:focus, .woocommerce nav.woocommerce-pagination ul li a:hover, 
	.woocommerce nav.woocommerce-pagination ul li span.current, .cz_btn, 
	.woocommerce .widget_price_filter .ui-slider .ui-slider-range, 
	.woocommerce .widget_price_filter .ui-slider .ui-slider-handle {background-color: ' . $site_color . '}
	.cs_load_more_doing, div.wpcf7 .wpcf7-form .ajax-loader, .cz_ajax_loader {border-right-color: ' . $site_color . '}
	input:focus,textarea:focus,select:focus {border-color: ' . $site_color . '}
	::selection {background-color: ' . $site_color . ';color: #fff}
	::-moz-selection {background-color: ' . $site_color . ';color: #fff}';
			} // Primary Color

			// Dev CSS
			$out .= empty( $opt['dev_css'] ) ? '' : "\n\n/* Dev */" . $opt['dev_css'];

			// Custom CSS
			$out .= empty( $opt['css'] ) ? '' : "\n\n/* Custom */" . $opt['css'];

			// Enqueue Google Fonts
			if ( ! isset( $opt['_css_body_typo'] ) || ! Codevz_Plus::contains( $opt['_css_body_typo'], 'font-family' ) ) {
				$fonts[]['font'] = Codevz_Plus::$is_rtl ? 'font-family:Cairo;' : 'font-family:Open Sans;';
			}
			$fonts = wp_parse_args( (array) Codevz_Plus::option( 'wp_editor_fonts' ), $fonts );
			$final_fonts = array();
			foreach ( $fonts as $font ) {
				if ( isset( $font['font'] ) ) {
					$final_fonts[] = $font['font'];
					Codevz_Plus::load_font( $font['font'] );
				}
			}

			// Generated fonts
			$options = get_option( Codevz_Plus::$options_id );
			$options['fonts_out'] = $final_fonts;
			update_option( Codevz_Plus::$options_id, $options );

			// Output
			return $out . $dynamic;
		}

		/**
		 *
		 * Get RGB numbers of HEX color
		 * 
		 * @var Hex color code
		 * @return string
		 *
		 */
		public static function hex2rgb( $c = '', $s = 0 ) {
			if ( empty( $c[0] ) ) {
				return '';
			}
			
			$c = substr( $c, 1 );
			if ( strlen( $c ) == 6 ) {
				list( $r, $g, $b ) = array( $c[0] . $c[1], $c[2] . $c[3], $c[4] . $c[5] );
			} elseif ( strlen( $c ) == 3 ) {
				list( $r, $g, $b ) = array( $c[0] . $c[0], $c[1] . $c[1], $c[2] . $c[2] );
			} else {
				return false;
			}
			$r = hexdec( $r );
			$g = hexdec( $g );
			$b = hexdec( $b );

			return implode( $s ? ', ' : ',', array( $r, $g, $b ) );
		}

		/**
		 *
		 * Update database, options for site colors changes
		 * 
		 * @var Old string and New string
		 * @return -
		 *
		 */
		public static function updateDatabase( $o = '', $n = '' ) {
			if ( $o ) {
				$old_rgb = self::hex2rgb( $o );
				$new_rgb = self::hex2rgb( $n );
				$old_rgb_s = self::hex2rgb( $o, 1 );
				$new_rgb_s = self::hex2rgb( $n, 1 );

				// Replace db
				global $wpdb;
				//$wpdb->prepare();
				$wpdb->query( "UPDATE " . $wpdb->prefix . "posts SET post_content = replace(replace(replace(post_content, '" . $old_rgb_s . "','" . $new_rgb_s . "' ), '" . $old_rgb . "','" . $new_rgb . "' ), '" . $o . "','" . $n . "')" );
				$wpdb->query( "UPDATE " . $wpdb->prefix . "postmeta SET meta_value = replace(replace(replace(meta_value, '" . $old_rgb_s . "','" . $new_rgb_s . "' ), '" . $old_rgb . "','" . $new_rgb . "' ), '" . $o . "','" . $n . "')" );
				
				// Replace options
				$all = json_encode( Codevz_Plus::option() );
				$all = str_replace( array( $o, $old_rgb, $old_rgb_s ), array( $n, $new_rgb, $new_rgb_s ), $all );
				update_option( Codevz_Plus::$options_id, json_decode( $all, true ) );
			}
		}

		/**
		 *
		 * Action after customizer saved
		 * 
		 * @return -
		 *
		 */
		public static function codevz_customize_save_after() {

			/*
			// Header Preset
			require_once self::$dir . 'includes/headers_preset.php';
			$header_preset = Codevz_Plus::option( 'header_preset' );
			if ( $header_preset && function_exists( 'codevz_headers_preset' ) ) {
					$options = Codevz_Plus::option();
					foreach ( (array) codevz_headers_preset( 'reset' ) as $key => $val ) {
						unset( $options[ $key ] );
					}
				unset( $options['header_preset'] );
				update_option( Codevz_Plus::$options_id, wp_parse_args( codevz_headers_preset( $header_preset ), $options ) );
			}*/

			// Update new post types
			$new_cpt = Codevz_Plus::option( 'add_post_type' );
			if ( is_array( $new_cpt ) && json_encode( $new_cpt ) !== json_encode( get_option( 'codevz_post_types_org' ) ) ) {
				$post_types = array();
				foreach ( $new_cpt as $cpt ) {
					if ( isset( $cpt['name'] ) ) {
						$post_types[] = strtolower( $cpt['name'] );
					}
				}
				update_option( 'codevz_css_selectors', '' );
				update_option( 'codevz_post_types', $post_types );
				update_option( 'codevz_post_types_org', $new_cpt );
			} else if ( empty( $new_cpt ) ) {
				delete_option( 'codevz_post_types' );
			}

			// Update Google Fonts for WP editor
			$fonts = Codevz_Plus::option( 'wp_editor_fonts' );
			if ( json_encode( $fonts ) !== json_encode( get_option( 'codevz_wp_editor_google_fonts_org' ) ) ) {
				$wp_editor_fonts = '';
				$fonts = wp_parse_args( $fonts, array(
					array( 'font' => 'inherit' ),
					array( 'font' => 'Arial' ),
					array( 'font' => 'Arial Black' ),
					array( 'font' => 'Comic Sans MS' ),
					array( 'font' => 'Impact' ),
					array( 'font' => 'Lucida Sans Unicode' ),
					array( 'font' => 'Tahoma' ),
					array( 'font' => 'Trebuchet MS' ),
					array( 'font' => 'Verdana' ),
					array( 'font' => 'Courier New' ),
					array( 'font' => 'Lucida Console' ),
					array( 'font' => 'Georgia, serif' ),
					array( 'font' => 'Palatino Linotype' ),
					array( 'font' => 'Times New Roman' )
				));

				// Custom fonts
				$custom_fonts = Codevz_Plus::option( 'custom_fonts' );
				if ( ! empty( $custom_fonts ) ) {
					$fonts = wp_parse_args( $custom_fonts, $fonts );
				}

				foreach ( $fonts as $font ) {
					if ( ! empty( $font['font'] ) ) {
						$font = $font['font'];
						if ( Codevz_Plus::contains( $font, ':' ) ) {
							$value = explode( ':', $font );
							$font = empty( $value[0] ) ? $font : $value[0];
							$wp_editor_fonts .= $font . '=' . $font . ';';
						} else {
							$title = ( $font === 'inherit' ) ? esc_html__( 'Inherit', 'codevz' ) : $font;
							$wp_editor_fonts .= $title . '=' . $font . ';';
						}
					}
				}
				update_option( 'codevz_wp_editor_google_fonts', $wp_editor_fonts );
				update_option( 'codevz_wp_editor_google_fonts_org', $fonts );
			}

			// Update primary theme color
			$primary = Codevz_Plus::option( 'site_color' );
			if ( $primary && $primary !== get_option( 'codevz_primary_color' ) ) {
				self::updateDatabase( get_option( 'codevz_primary_color' ), $primary );
				update_option( 'codevz_primary_color', $primary );
			}

			// Update secondary theme color
			$secondary = Codevz_Plus::option( 'site_color_sec' );
			if ( $secondary && $secondary !== get_option( 'codevz_secondary_color' ) ) {
				self::updateDatabase( get_option( 'codevz_secondary_color' ), $secondary );
				update_option( 'codevz_secondary_color', $secondary );
			}

			// Update CSS options
			$options = get_option( Codevz_Plus::$options_id );
			$options['css_out'] = self::css_out();
			update_option( Codevz_Plus::$options_id, $options );
		}

		/**
		 *
		 * Meta box for pages, posts, port types
		 * 
		 * @return array
		 *
		 */
		public static function metabox() {

			// Add one-page menu option for pages only
			add_filter( 'codevz_metabox', function( $a ) {

				if ( self::get_post_type_admin() === 'page' ) {
					$a[0]['fields'][] = array(
						'id'  		=> 'one_page',
						'type'  	=> 'switcher',
						'title' 	=> esc_html__( 'One page menu?', 'codevz' ),
						'desc' 		=> esc_html__( 'One page menu instead primary menu for this page. You can set One page location from Appearance > Menus', 'codevz' ),
					);
				} else {
					$a[0]['fields'][] = array(
						'id'  		=> 'hide_featured_image',
						'type' 		=> 'switcher',
						'title' 	=> esc_html__( 'Hide featured image?', 'codevz' ),
						'desc' 		=> esc_html__( 'Only on this page hide post featured image', 'codevz' ),
					);
				}

				return $a;
			}, 999 );

			// SEO options
			$seo = Codevz_Plus::option( 'seo_meta_tags' ) ? array(
				array(
					'id' 		=> 'seo_desc',
					'type' 		=> 'text',
					'title' 	=> esc_html__( 'SEO description', 'codevz' ),
					'desc' 		=> esc_html__( "Short description about this page, If you leave this field empty, Then post content or post title will be use for SEO description.", 'codevz' ),
				),
				array(
					'id' 		=> 'seo_keywords',
					'type' 		=> 'text',
					'title' 	=> esc_html__( 'SEO keywords', 'codevz' ),
					'desc'		=> esc_html__( 'Keywords about this page, Separate with comma, e.g. Business,Company,WordPress', 'codevz' ),
				),
			) : array(
					array(
						'type'    => 'content',
						'content' => esc_html__( 'Please first enable SEO options from Theme Options > General > Advanced', 'codevz' )
					),
			);
			$seo = array(
				  'name'   => 'page_seo_settings',
				  'title'  => esc_html__( 'SEO settings', 'codevz' ),
				  'icon'   => 'fa fa-search',
				  'fields' => $seo
			);

			// Return meta box
			return array(array(
				'id'           => Codevz_Plus::$meta_id,
				'title'        => esc_html__( 'Page settings', 'codevz' ),
				'post_type'    => self::post_types( array( 'post', 'page' ) ),
				'context'      => 'normal',
				'priority'     => 'default',
				'show_restore' => true,
				'sections'     => apply_filters( 'codevz_metabox', array(

					array(
					  'name'   => 'page_general_settings',
					  'title'  => esc_html__( 'General settings', 'codevz' ),
					  'icon'   => 'fa fa-cog',
					  'fields' => array(
						array(
							'id' 		=> 'boxed',
							'type' 		=> 'image_select',
							'title' 	=> esc_html__( 'Layout', 'codevz' ),
							'options' 	=> array(
								'd'         => CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-0.png',
								''          => CDVZ_PLUGIN_URI . 'assets/admin_img/layout-1.png',
								'1' 		=> CDVZ_PLUGIN_URI . 'assets/admin_img/layout-2.png',
								'2'      	=> CDVZ_PLUGIN_URI . 'assets/admin_img/layout-3.png',
							),
							'default' 	=> 'd',
							'attributes' => array(
								'data-depend-id' => 'boxed'
							),
							'desc' 	=> esc_html__( "The default layout is set from Theme Options > General > Layout", 'codevz' ),
						),
						array(
						  'id'  		=> 'layout',
						  'type'  		=> 'image_select',
						  'title' 		=> esc_html__( 'Sidebar position', 'codevz' ),
							'desc'  	=> esc_html__( 'The default sidebar position is set from Theme Options > General > Sidebar position', 'codevz' ),
							'options' 	=> array(
								'1'           	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-0.png',
								'none' 			=> CDVZ_PLUGIN_URI . 'assets/admin_img/off.png',
								'bpnp'      	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-2.png',
								'center' 		=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-13.png',
								'right'       	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-3.png',
								'right-s'     	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-4.png',
								'left'        	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-5.png',
								'left-s'      	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-6.png',
								'both-side' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-7.png',
								'both-side2' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-8.png',
								'both-right'	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-9.png',
								'both-right2'	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-10.png',
								'both-left' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-11.png',
								'both-left2' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-12.png',
							),
							'default'     => ( self::get_post_type_admin() === 'page' ) ? 'none' : '1',
						),
			  array(
				'id'      => 'page_content_margin',
				'type'    => 'select',
				'title'   => esc_html__( 'Page content gap', 'codevz' ),
				'desc'    => esc_html__( 'The gap between header, content and footer', 'codevz' ),
				'options' => array(
					''  		=> esc_html__( 'Default', 'codevz' ),
					'mt0'  		=> esc_html__( 'No gap between header and content', 'codevz' ),
					'mb0'  		=> esc_html__( 'No gap between content and footer', 'codevz' ),
					'mt0 mb0'  	=> esc_html__( 'No gap between header, content and footer', 'codevz' ),
				)
			  ),
			  array(
				'id'        	=> '_css_page_body_bg',
				'type'      	=> 'cz_sk',
				'title'     	=> esc_html__( 'Page background', 'codevz' ),
				'button'    	=> esc_html__( 'StyleKit', 'codevz' ),
				'settings'    	=> array( 'background' ),
				'selector'    	=> '',
				'desc'   	=> esc_html__( 'Color or image', 'codevz' ),
			  ),
			  array('id' => '_css_page_body_bg_tablet','type' => 'cz_sk_hidden','selector' => ''),
			  array('id' => '_css_page_body_bg_mobile','type' => 'cz_sk_hidden','selector' => ''),
			  array(
				'id'        	=> '_css_layout_1',
				'type'      	=> 'cz_sk',
				'title'     	=> esc_html__( 'Boxed layout background', 'codevz' ),
				'button'    	=> esc_html__( 'StyleKit', 'codevz' ),
				'settings'    	=> array( 'background' ),
				'selector'    	=> '',
				'desc'   		=> esc_html__( 'Works only on boxed layout', 'codevz' ),
				'dependency' => array( 'boxed', 'any', '1,2' ),
			  ),
			  array('id' => '_css_layout_1_tablet','type' => 'cz_sk_hidden','selector' => ''),
			  array('id' => '_css_layout_1_mobile','type' => 'cz_sk_hidden','selector' => ''),

				array(
					'id'  		=> 'hide_header',
					'type'  	=> 'switcher',
					'title' 	=> esc_html__( 'Hide header?', 'codevz' ),
					'desc'   	=> esc_html__( 'Hide header only on this page', 'codevz' ),
				),
				array(
					'id'  		=> 'hide_footer',
					'type' 		=> 'switcher',
					'title' 	=> esc_html__( 'Hide footer?', 'codevz' ),
					'desc'   	=> esc_html__( 'Hide footer only on this page', 'codevz' ),
				),

			)
		  ), // page_general_settings

		  array(
			'name'   => 'page_header',
			'title'  => esc_html__( 'Header Settings', 'codevz' ),
			'icon'   => 'fa fa-paint-brush',
			'fields' => array(
			  array(
				'id'        => 'cover_than_header',
				'type'      => 'select',
				'title'     => esc_html__( 'Header position', 'codevz' ),
				'desc'      => esc_html__( 'If you want to make your header overlay on slider or title or page content, then change this option on your needs. The default option is set from Theme Options > Header > Title & Breadcrumbs', 'codevz' ),
				'options'   => array(
				  'd'                   					=> esc_html__( 'Default', 'codevz' ),
				  'header_top'          					=> esc_html__( 'Header before title section', 'codevz' ),
				  'header_after_cover'  					=> esc_html__( 'Header after title section', 'codevz' ),
				  'header_onthe_cover'  					=> esc_html__( 'Header overlay only on desktop', 'codevz' ),
				  'header_onthe_cover header_onthe_cover_dt' => esc_html__( 'Header overlay only on desktop & tablet', 'codevz' ),
				  'header_onthe_cover header_onthe_cover_all' => esc_html__( 'Header overlay on all devices', 'codevz' ),
				),
				'default'   => 'd',
			  ),
			  array(
				'id'    => 'page_cover',
				'type'    => 'select',
				'title'   => esc_html__( 'Title type', 'codevz' ),
				'options'   => array(
				  '1'     		=> esc_html__( 'Default', 'codevz' ),
				  'none'    	=> esc_html__( 'None', 'codevz' ),
				  'title'   	=> esc_html__( 'Title & Breadcrumbs', 'codevz' ),
				  'rev'     	=> esc_html__( 'Revolution Slider', 'codevz' ),
				  'custom'  	=> esc_html__( 'Custom Shortcode', 'codevz' ),
				  'page'    	=> esc_html__( 'Custom Page', 'codevz' )
				),
				'default'   => '1',
				'desc'     	=> esc_html__( 'If you want to learn more about how title section works, set this to default then go to Theme Options > Header > Title & Breadcrumbs and change settings.', 'codevz' ),
				'help'     	=> esc_html__( 'Title and breadcrumbs only can be set from Theme Options > Header > Title & Breadcrumbs', 'codevz' ),
			  ),
			  array(
				'id'    		=> 'page_cover_page',
				'type'    		=> 'select',
				'title'   		=> esc_html__( 'Select Page', 'codevz' ),
				'desc'   		=> esc_html__( 'You can create custom page from Dashboard > Pages and assing it here, This will show instead title section for this page.', 'codevz' ),
				'options'   	=> Codevz_Plus::$array_pages,
				'dependency' 	=> array( 'page_cover', '==', 'page' ),
				'default_option' => esc_html__( 'Select', 'codevz'),
			  ),
			  array(
				'id'    		=> 'page_cover_custom',
				'type'    		=> 'textarea',
				'title'   		=> esc_html__( 'Custom Shortcode', 'codevz' ),
				'desc' 			=> esc_html__( 'Shortcode or custom HTML codes allowed, This will show instead title section.', 'codevz' ),
				'dependency' 	=> array( 'page_cover', '==', 'custom' )
			  ),
			  array(
				'id'    		=> 'page_cover_rev',
				'type'    		=> 'select',
				'title'   		=> esc_html__( 'Select Reolution Slider', 'codevz' ),
				'desc' 			=> esc_html__( 'You can create slider from Dashboard > Revolution Slider then assing it here.', 'codevz' ),
				'options'   	=> self::revSlider(),
				'dependency' 	=> array( 'page_cover', '==', 'rev' ),
				'default_option' => esc_html__( 'Select', 'codevz'),
			  ),
			  array(
				'id'        => '_css_page_title',
				'type'      => 'cz_sk',
				'title'    => esc_html__( 'Container background', 'codevz' ),
				'desc'     => esc_html__( 'Background image, color or padding of title section or header overlay', 'codevz' ),
				'button'    => esc_html__( 'Stylekit', 'codevz' ),
				'settings'  => array( 'background', 'padding', 'border' ),
				'selector'  => ''
			  ),
			  array('id' => '_css_page_title_tablet','type' => 'cz_sk_hidden','selector' => ''),
			  array('id' => '_css_page_title_mobile','type' => 'cz_sk_hidden','selector' => ''),
			  array(
				'id'          => '_css_page_title_color',
				'type'        => 'cz_sk',
				'title'      => esc_html__( 'Page title', 'codevz' ),
				'desc' 			=> esc_html__( 'Color and font size', 'codevz' ),
				'button'    => esc_html__( 'Stylekit', 'codevz' ),
				'settings'    => array( 'color', 'font-size', 'padding' ),
				'selector'    => ''
			  ),
			  array('id' => '_css_page_title_color_tablet','type' => 'cz_sk_hidden','selector' => ''),
			  array('id' => '_css_page_title_color_mobile','type' => 'cz_sk_hidden','selector' => ''),
			  array(
				'id'      => '_css_page_title_breadcrumbs_color',
				'type'      => 'cz_sk',
				'title'    => esc_html__( 'Breadcrumbs color', 'codevz' ),
				'desc' 			=> esc_html__( 'Color and font size', 'codevz' ),
				'button'    => esc_html__( 'Stylekit', 'codevz' ),
				'settings'    => array( 'font-size', 'color' ),
				'selector'    => ''
			  ),
			  array('id' => '_css_page_title_breadcrumbs_color_tablet','type' => 'cz_sk_hidden','selector' => ''),
			  array('id' => '_css_page_title_breadcrumbs_color_mobile','type' => 'cz_sk_hidden','selector' => ''),

			  array(
				'id'      	=> '_css_container_header_1',
				'type'      => 'cz_sk',
				'title'    	=> esc_html__( 'Header top bar', 'codevz' ),
				'button'    => esc_html__( 'StyleKit', 'codevz' ),
				'settings' 	=> array( 'background', 'padding', 'border' ),
				'selector' 	=> ''
			  ),
			  array('id' => '_css_container_header_1_tablet','type' => 'cz_sk_hidden','selector' => ''),
			  array('id' => '_css_container_header_1_mobile','type' => 'cz_sk_hidden','selector' => ''),

			  array(
				'id'      => '_css_container_header_2',
				'type'      => 'cz_sk',
				'title'    => esc_html__( 'Header', 'codevz' ),
				'button'    => esc_html__( 'StyleKit', 'codevz' ),
				'settings'    => array( 'background', 'padding', 'border' ),
				'selector'    => ''
			  ),
			  array('id' => '_css_container_header_2_tablet','type' => 'cz_sk_hidden','selector' => ''),
			  array('id' => '_css_container_header_2_mobile','type' => 'cz_sk_hidden','selector' => ''),

			  array(
				'id'      => '_css_container_header_3',
				'type'      => 'cz_sk',
				'title'    => esc_html__( 'Header bottom bar', 'codevz' ),
				'button'    => esc_html__( 'StyleKit', 'codevz' ),
				'settings'    => array( 'background', 'padding', 'border' ),
				'selector'    => ''
			  ),
			  array('id' => '_css_container_header_3_tablet','type' => 'cz_sk_hidden','selector' => ''),
			  array('id' => '_css_container_header_3_mobile','type' => 'cz_sk_hidden','selector' => ''),

			  array(
				'id'        => '_css_header_container',
				'type'      => 'cz_sk',
				'title'     => esc_html__( 'Overall Header', 'codevz' ),
				'help'      => esc_html__( 'This StyleKit contains all 3 header rows together, For example is suitable for header background.', 'codevz' ),
				'button'    => esc_html__( 'StyleKit', 'codevz' ),
				'settings'  => array( 'background', 'padding', 'border' ),
				'selector'  => ''
			  ),
			  array('id' => '_css_header_container_tablet','type' => 'cz_sk_hidden','selector' => ''),
			  array('id' => '_css_header_container_mobile','type' => 'cz_sk_hidden','selector' => ''),

			  array(
				'id'        => '_css_fixed_side_style',
				'type'      => 'cz_sk',
				'title'     => esc_html__( 'Fixed Side', 'codevz' ),
				'help'      => esc_html__( 'You can enable Fixed Side from Theme Options > Header > Fixed Side', 'codevz' ),
				'button'    => esc_html__( 'StyleKit', 'codevz' ),
				'settings'  => array( 'background', 'width', 'border' ),
				'selector'  => ''
			  ),
			  array('id' => '_css_fixed_side_style_tablet','type' => 'cz_sk_hidden','selector' => ''),
			  array('id' => '_css_fixed_side_style_mobile','type' => 'cz_sk_hidden','selector' => ''),

			)
		  ), // page_header_settings
					$seo
				))
			));
		}

		/**
		 *
		 * Breadcrumbs and title options
		 * 
		 * @var post type name, CSS selector
		 * @return array
		 *
		 */
		public static function title_options( $i = '', $c = '' ) {

			// Icon option for default title settings
			$br_separator = $i ? array(
				'type'    		=> 'notice',
				'class'   		=> 'info',
				'content' 		=> '',
				'dependency' 	=> array( 'xxx', '==', 'true' )
			) : array(
				'id'    		=> 'breadcrumbs_separator',
				'type'  		=> 'icon',
				'title' 		=> esc_html__( 'Breadcrumbs delimiter', 'codevz' ),
				'dependency' 	=> array( 'page_cover|page_title', '==|any', 'title|4,5,6,7,8,9' ),
				'setting_args' 	=> array( 'transport' => 'postMessage' )
			);

			return array(
				array(
					'id' 			=> 'cover_than_header' . $i,
					'type' 			=> 'select',
					'title' 		=> esc_html__( 'Header position', 'codevz' ),
					'help'      	=> esc_html__( 'If you want to make your header overlay on slider or title or page content, then change this option on your needs. The default option is set from Theme Options > Header > Title & Breadcrumbs', 'codevz' ),
					'options' 		=> array(
						'' 		 			 						=> esc_html__( 'Default', 'codevz' ),
						'header_top'          						=> esc_html__( 'Header before title section', 'codevz' ),
						'header_after_cover'  						=> esc_html__( 'Header after title section', 'codevz' ),
						'header_onthe_cover'  						=> esc_html__( 'Header overlay only on desktop', 'codevz' ),
						'header_onthe_cover header_onthe_cover_dt' 	=> esc_html__( 'Header overlay only on desktop & tablet', 'codevz' ),
						'header_onthe_cover header_onthe_cover_all' => esc_html__( 'Header overlay on all devices', 'codevz' ),
					)
				),
				array(
					'id' 	=> 'page_cover' . $i,
					'type' 	=> 'select',
					'title' => esc_html__( 'Title Type', 'codevz' ),
					'options' => array(
						( $i ? '1' : '' ) => $i ? esc_html__( 'Default', 'codevz' ) : esc_html__( 'Select', 'codevz' ),
						'none' 		=> esc_html__( 'None', 'codevz' ),
						'title' 	=> esc_html__( 'Title & Breadcrumbs', 'codevz' ),
						'rev' 		=> esc_html__( 'Revolution Slider', 'codevz' ),
						'custom' 	=> esc_html__( 'Custom Shortcode', 'codevz' ),
						'page' 		=> esc_html__( 'Custom Page', 'codevz' )
					),
					'help'  	=> esc_html__( 'The default option for all pages that have no title settings', 'codevz' ),
					'default' 	=> $i ? '1' : 'none'
				),
				array(
					'id'            => 'page_cover_page' . $i,
					'type'          => 'select',
					'title'         => esc_html__( 'Select Page', 'codevz' ),
					'help'   		=> esc_html__( 'You can create custom page from Dashboard > Pages and assing it here, This will show instead title section.', 'codevz' ),
					'options'       => Codevz_Plus::$array_pages,
					'default_option'=> esc_html__( 'Select', 'codevz' ),
					'dependency' 	=> array( 'page_cover' . $i, '==', 'page' )
				),
				array(
					'id' 		=> 'page_cover_custom' . $i,
					'type' 		=> 'textarea',
					'title' 	=> esc_html__( 'Custom Shortcode', 'codevz' ),
					'help' 		=> esc_html__( 'Shortcode or custom HTML allowed', 'codevz' ),
					'dependency' => array( 'page_cover' . $i, '==', 'custom' )
				),
				array(
					'id' 			=> 'page_cover_rev' . $i,
					'type' 			=> 'select',
					'title' 		=> esc_html__( 'Select Reolution Slider', 'codevz' ),
					'help' 			=> esc_html__( 'You can create slider from Dashboard > Revolution Slider then assing it here.', 'codevz' ),
					'options' 		=> self::revSlider(),
					'dependency' 	=> array( 'page_cover' . $i, '==', 'rev' ),
					'default_option' => esc_html__( 'Select', 'codevz'),
				),

				array(
					'id' 			=> 'page_title' . $i,
					'type' 			=> 'select',
					'title' 		=> esc_html__( 'Title & Breadcrumbs', 'codevz' ),
					'options' 		=> array(
						'1' 	=> $i ? esc_html__( 'Default', 'codevz' ) : esc_html__( 'Select', 'codevz' ),
						'3' 	=> esc_html__( 'Page title', 'codevz' ),
						'2' 	=> esc_html__( 'Page title above content', 'codevz' ),
						'4' 	=> esc_html__( 'Title &gt; Breadcrumbs', 'codevz' ),
						'5' 	=> esc_html__( 'Breadcrumbs &gt; Title', 'codevz' ),
						'6' 	=> esc_html__( 'Title left & Breadcrumbs right', 'codevz' ),
						'7' 	=> esc_html__( 'Breadcrumbs', 'codevz' ),
						'9' 	=> esc_html__( 'Breadcrumbs right', 'codevz' ),
						'8' 	=> esc_html__( 'Breadcrumbs & Title above content', 'codevz' ),
					),
					'dependency' 	=> array( 'page_cover' . $i, '==', 'title' ),
					'default' 		=> '1'
				),
				array(
					'id'      		=> 'page_title_center' . $i,
					'type'      	=> 'switcher',
					'title'   		=> esc_html__( 'Title & breadcrumbs center?', 'codevz' ),
					'dependency'  	=> array( 'page_cover' . $i . '|page_title' . $i, 'any|any', 'title|3,4,5,7,8,9' )
				),
				$br_separator,
				array(
					'id' 			=> '_css_page_title' . $i,
					'type' 			=> 'cz_sk',
					'button' 		=> esc_html__( 'Container background', 'codevz' ),
					'setting_args' 	=> array( 'transport' => 'postMessage' ),
					'settings' 		=> array( 'background', 'padding', 'border' ),
					'selector' 		=> $c . '.page_title,' . $c . '.header_onthe_cover .page_title',
					'dependency' 	=> array( 'page_cover' . $i . '|page_title' . $i, '==|any', 'title|2,3,4,5,6,7,8,9' )
				),
				array(
					'id' 			=> '_css_page_title' . $i . '_tablet',
					'type' 			=> 'cz_sk_hidden',
					'setting_args' 	=> array( 'transport' => 'postMessage' ),
					'selector' 		=> $c . '.page_title,' . $c . '.header_onthe_cover .page_title'
				),
				array(
					'id' 			=> '_css_page_title' . $i . '_mobile',
					'type' 			=> 'cz_sk_hidden',
					'setting_args' 	=> array( 'transport' => 'postMessage' ),
					'selector' 		=> $c . '.page_title,' . $c . '.header_onthe_cover .page_title'
				),
				array(
					'id' 			=> '_css_page_title_inner_row' . $i,
					'type' 			=> 'cz_sk',
					'button' 		=> esc_html__( 'Inner Row', 'codevz' ),
					'setting_args' 	=> array( 'transport' => 'postMessage' ),
					'settings' 		=> array( 'background', 'width', 'padding' ),
					'selector' 		=> $c . '.page_title .row',
					'dependency' 	=> array( 'page_cover' . $i . '|page_title' . $i, '==|any', 'title|3,4,5,6,7,8,9' )
				),
				array(
					'id' 			=> '_css_page_title_inner_row' . $i . '_tablet',
					'type' 			=> 'cz_sk_hidden',
					'setting_args' 	=> array( 'transport' => 'postMessage' ),
					'selector' 		=> $c . '.page_title .row',
				),
				array(
					'id' 			=> '_css_page_title_inner_row' . $i . '_mobile',
					'type' 			=> 'cz_sk_hidden',
					'setting_args' 	=> array( 'transport' => 'postMessage' ),
					'selector' 		=> $c . '.page_title .row',
				),
				array(
					'id' 			=> '_css_page_title_color' . $i,
					'type' 			=> 'cz_sk',
					'button' 		=> esc_html__( 'Page Title', 'codevz' ),
					'setting_args' 	=> array( 'transport' => 'postMessage' ),
					'settings' 		=> array( 'color', 'font-size', 'padding' ),
					'selector' 		=> $c . '.page_title .section_title',
					'dependency' 	=> array( 'page_cover' . $i . '|page_title' . $i, '==|any', 'title|3,4,5,6' )
				),
				array(
					'id' 			=> '_css_page_title_color' . $i . '_tablet',
					'type' 			=> 'cz_sk_hidden',
					'setting_args' 	=> array( 'transport' => 'postMessage' ),
					'selector' 		=> $c . '.page_title .section_title',
				),
				array(
					'id' 			=> '_css_page_title_color' . $i . '_mobile',
					'type' 			=> 'cz_sk_hidden',
					'setting_args' 	=> array( 'transport' => 'postMessage' ),
					'selector' 		=> $c . '.page_title .section_title',
				),
				array(
					'id' 			=> '_css_inner_title' . $i,
					'type' 			=> 'cz_sk',
					'button' 		=> esc_html__( 'Title ( in Content )', 'codevz' ),
					'setting_args' 	=> array( 'transport' => 'postMessage' ),
					'settings' 		=> array( 'color', 'font-size', 'padding' ),
					'selector' 		=> $c . ' .content > h3:first-child,' . $c . ' .content .section_title',
					'dependency' 	=> array( 'page_cover' . $i . '|page_title' . $i, '==|any', 'title|2,8' )
				),
				array(
					'id' 			=> '_css_inner_title' . $i . '_tablet',
					'type' 			=> 'cz_sk_hidden',
					'setting_args' 	=> array( 'transport' => 'postMessage' ),
					'selector' 		=> $c . ' .content > h3:first-child,' . $c . ' .content .section_title'
				),
				array(
					'id' 			=> '_css_inner_title' . $i . '_mobile',
					'type' 			=> 'cz_sk_hidden',
					'setting_args' 	=> array( 'transport' => 'postMessage' ),
					'selector' 		=> $c . ' .content > h3:first-child,' . $c . ' .content .section_title'
				),
				array(
					'id' 			=> '_css_page_title_breadcrumbs_color' . $i,
					'type' 			=> 'cz_sk',
					'button' 		=> esc_html__( 'Breadcrumbs color', 'codevz' ),
					'setting_args' 	=> array( 'transport' => 'postMessage' ),
					'settings' 		=> array( 'color', 'font-size' ),
					'selector' 		=> $c . '.page_title a,' . $c . '.page_title a:hover,' . $c . '.page_title i',
					'dependency' 	=> array( 'page_cover' . $i . '|page_title' . $i, '==|any', 'title|4,5,6,7,8,9' )
				),
				array(
					'id' 			=> '_css_page_title_breadcrumbs_color' . $i . '_tablet',
					'type' 			=> 'cz_sk_hidden',
					'setting_args' 	=> array( 'transport' => 'postMessage' ),
					'selector' 		=> $c . '.page_title a,' . $c . '.page_title a:hover,' . $c . '.page_title i',
				),
				array(
					'id' 			=> '_css_page_title_breadcrumbs_color' . $i . '_mobile',
					'type' 			=> 'cz_sk_hidden',
					'setting_args' 	=> array( 'transport' => 'postMessage' ),
					'selector' 		=> $c . '.page_title a,' . $c . '.page_title a:hover,' . $c . '.page_title i',
				),
				array(
					'id' 			=> '_css_breadcrumbs_inner_container' . $i,
					'type' 			=> 'cz_sk',
					'button' 		=> esc_html__( 'Breadcrumbs Inner', 'codevz' ),
					'setting_args' 	=> array( 'transport' => 'postMessage' ),
					'settings' 		=> array( 'background', 'width', 'padding' ),
					'selector' 		=> $c . '.breadcrumbs',
					'dependency' 	=> array( 'page_cover' . $i . '|page_title' . $i, '==|any', 'title|4,5,6,7,8,9' )
				),
				array(
					'id' 			=> '_css_breadcrumbs_inner_container' . $i . '_tablet',
					'type' 			=> 'cz_sk_hidden',
					'setting_args' 	=> array( 'transport' => 'postMessage' ),
					'selector' 		=> $c . '.breadcrumbs',
				),
				array(
					'id' 			=> '_css_breadcrumbs_inner_container' . $i . '_mobile',
					'type' 			=> 'cz_sk_hidden',
					'setting_args' 	=> array( 'transport' => 'postMessage' ),
					'selector' 		=> $c . '.breadcrumbs',
				),
				array(
					'id' 			=> '_css_right_br_full_container' . $i,
					'type' 			=> 'cz_sk',
					'button' 		=> esc_html__( 'Overall row container', 'codevz' ),
					'setting_args' 	=> array( 'transport' => 'postMessage' ),
					'settings' 		=> array( 'background', 'padding' ),
					'selector' 		=> $c . '.right_br_full_container',
					'dependency' 	=> array( 'page_cover' . $i . '|page_title' . $i, '==|==', 'title|6' )
				),
				array(
					'id' 			=> '_css_right_br_full_container' . $i . '_tablet',
					'type' 			=> 'cz_sk_hidden',
					'setting_args' 	=> array( 'transport' => 'postMessage' ),
					'selector' 		=> $c . '.right_br_full_container',
				),
				array(
					'id' 			=> '_css_right_br_full_container' . $i . '_mobile',
					'type' 			=> 'cz_sk_hidden',
					'setting_args' 	=> array( 'transport' => 'postMessage' ),
					'selector' 		=> $c . '.right_br_full_container',
				),
			);
		}

		/**
		 *
		 * Customize page options
		 * 
		 * @return array
		 *
		 */
		public static function options() {

			$options = array();

			$options[]   = array(
				'name' 		=> 'general',
				'title' 	=> esc_html__( 'General', 'codevz' ),
				'sections' => array(

					array(
						'name'   => 'layout',
						'title'  => esc_html__( 'Layout', 'codevz' ),
						'fields' => array(
							array(
								'id' 		=> 'boxed',
								'type' 		=> 'image_select',
								'title' 	=> esc_html__( 'Layout', 'codevz' ),
								'help'  	=> esc_html__( 'The default option for all pages', 'codevz' ),
								'options' 	=> array(
									''          => CDVZ_PLUGIN_URI . 'assets/admin_img/layout-1.png',
									'1' 		=> CDVZ_PLUGIN_URI . 'assets/admin_img/layout-2.png',
									'2'      	=> CDVZ_PLUGIN_URI . 'assets/admin_img/layout-3.png',
								),
								'setting_args' 	  => array( 'transport' => 'postMessage' )
							),
							array(
								'id'        => 'site_width',
								'type'      => 'slider',
								'title'     => esc_html__( 'Site width', 'codevz' ),
								'help'   	=> 'e.g. 1200px',
								'options' 	=> array( 'unit' => 'px', 'step' => 10, 'min' => 960, 'max' => 1400 ),
								'setting_args' 	  => array( 'transport' => 'postMessage' )
							),
							array(
								'id' 		=> 'layout',
								'type' 		=> 'image_select',
								'title' 	=> esc_html__( 'Sidebar position', 'codevz' ),
								'help'  	=> esc_html__( 'The default option for all pages', 'codevz' ),
								'options' 	=> array(
									'ws' 			=> CDVZ_PLUGIN_URI . 'assets/admin_img/off.png',
									'bpnp'      	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-2.png',
									'center' 		=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-13.png',
									'right'       	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-3.png',
									'right-s'     	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-4.png',
									'left'        	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-5.png',
									'left-s'      	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-6.png',
									'both-side' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-7.png',
									'both-side2' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-8.png',
									'both-right'	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-9.png',
									'both-right2'	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-10.png',
									'both-left' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-11.png',
									'both-left2' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-12.png',
								),
								'default'  	=> 'right',
							),
							array(
								'id' 		=> 'sticky',
								'type' 		=> 'switcher',
								'title' 	=> esc_html__( 'Sticky sidebar?', 'codevz' ),
								'help' 		=> esc_html__( 'Sticky sidebar on page scrolling', 'codevz' )
							),
							array(
								'id' 		=> 'responsive',
								'type' 		=> 'switcher',
								'title' 	=> esc_html__( 'Responsive?', 'codevz' ),
								'default' 	=> true,
								'help' 		=> esc_html__( 'Recommended, Better view on small devices', 'codevz' )
							),
							array(
								'id' 			=> 'rtl',
								'type' 			=> 'switcher',
								'title' 		=> esc_html__( 'RTL mode?', 'codevz' ),
								'help' 			=> esc_html__( 'For right to left languages', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' )
							),
						)
					),

					array(
						'name'   => 'styling',
						'title'  => esc_html__( 'Theme Colors', 'codevz' ),
						'fields' => array(
							array(
								'id'        => 'site_color',
								'type'      => 'color_picker',
								'title'     => esc_html__( 'Accent color', 'codevz' ),
								'desc'      => esc_html__( 'Warning: All old Primary Colors in the options and pages content will change.', 'codevz' ),
								'setting_args' => array( 'transport' => 'postMessage' )
							),
							array(
								'id'        => 'site_color_sec',
								'type'      => 'color_picker',
								'title'     => esc_html__( 'Secondary color', 'codevz' ),
								'desc'      => esc_html__( 'Warning: All old Secondary Colors in the options and pages content will change.', 'codevz' ),
								'setting_args' => array( 'transport' => 'postMessage' )
							),
							array(
								'id' 	=> 'dark',
								'type' 	=> 'switcher',
								'title' => esc_html__( 'Dark mode?', 'codevz' ),
								'help'  => esc_html__( 'Please note: Some sections have dynamic colors and it may you see them still in light mode, So you need to find and edit each settings manually.', 'codevz' )
							),
							array(
								'type'    => 'notice',
								'class'   => 'info',
								'content' => esc_html__( 'Styles', 'codevz' ) . self::$sk_advanced
							),
							array(
								'id' 			=> '_css_body',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Body background', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'background' ),
								'selector' 		=> 'html,body',
							),
							array(
								'id' 			=> '_css_body_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> 'html,body'
							),
							array(
								'id' 			=> '_css_body_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> 'html,body'
							),
							array(
								'id' 			=> '_css_layout_1',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Boxed background', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'background' ),
								'selector' 		=> '#layout'
							),
							array(
								'id' => '_css_layout_1_tablet', 'type' => 'cz_sk_hidden', 'setting_args' => array( 'transport' => 'postMessage' ),
								'selector' 		=> '#layout'
							),
							array(
								'id' => '_css_layout_1_mobile', 'type' => 'cz_sk_hidden', 'setting_args' => array( 'transport' => 'postMessage' ),
								'selector' 		=> '#layout'
							),
							array(
								'id' 			=> '_css_buttons',
								'hover_id' 		=> '_css_buttons_hover',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Buttons', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'background', 'font-size', 'border' ),
								'selector' 		=> 'form button,.wpcf7-submit,.dwqa-questions-footer .dwqa-ask-question a,input[type=submit],input[type=button],.button,.cz_header_button,.woocommerce a.button,.woocommerce input.button,.woocommerce #respond input#submit.alt,.woocommerce a.button.alt,.woocommerce button.button.alt,.woocommerce input.button.alt'
							),
							array(
								'id' 			=> '_css_buttons_tablet', 'type' => 'cz_sk_hidden', 'setting_args' => array( 'transport' => 'postMessage' ),
								'selector' 		=> 'form button,.wpcf7-submit,.dwqa-questions-footer .dwqa-ask-question a,input[type=submit],input[type=button],.button,.cz_header_button,.woocommerce a.button,.woocommerce input.button,.woocommerce #respond input#submit.alt,.woocommerce a.button.alt,.woocommerce button.button.alt,.woocommerce input.button.alt'
							),
							array(
								'id' 			=> '_css_buttons_mobile', 'type' => 'cz_sk_hidden', 'setting_args' => array( 'transport' => 'postMessage' ),
								'selector' 		=> 'form button,.wpcf7-submit,.dwqa-questions-footer .dwqa-ask-question a,input[type=submit],input[type=button],.button,.cz_header_button,.woocommerce a.button,.woocommerce input.button,.woocommerce #respond input#submit.alt,.woocommerce a.button.alt,.woocommerce button.button.alt,.woocommerce input.button.alt'
							),
							array(
								'id' 			=> '_css_buttons_hover', 'type' => 'cz_sk_hidden', 'setting_args' => array( 'transport' => 'postMessage' ),
								'selector' 		=> 'form button:hover,.wpcf7-submit:hover,.dwqa-questions-footer .dwqa-ask-question a:hover,input[type=submit]:hover,input[type=button]:hover,.button:hover,.cz_header_button:hover,.woocommerce a.button:hover,.woocommerce input.button:hover,.woocommerce #respond input#submit.alt:hover,.woocommerce a.button.alt:hover,.woocommerce button.button.alt:hover,.woocommerce input.button.alt:hover'
							),
							array(
								'id' 			=> '_css_content_block',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Content area', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'background', 'border' ),
								'selector' 		=> '.content'
							),
							array(
								'id' 			=> '_css_content_block_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.content'
							),
							array(
								'id' 			=> '_css_content_block_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.content'
							),
							array(
								'id' 			=> '_css_content_block_headline',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Content area headline', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'background', 'font-size', 'border' ),
								'selector' 		=> '.content > h3:first-child, .content .section_title'
							),
							array(
								'id' 			=> '_css_content_block_headline_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.content > h3:first-child, .content .section_title'
							),
							array(
								'id' 			=> '_css_content_block_headline_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.content > h3:first-child, .content .section_title'
							),
							array(
								'id' 			=> '_css_content_block_links',
								'hover_id'		=> '_css_content_block_links_hover',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Content area links', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color' ),
								'selector' 		=> '.content a'
							),
							array(
								'id' 			=> '_css_content_block_links_hover', 'type' => 'cz_sk_hidden', 'setting_args' => array( 'transport' => 'postMessage' ),
								'selector' 		=> '.content a:hover'
							),
							array(
								'id' 			=> '_css_sidebar_primary',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Primary sidebar area', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'background', 'border' ),
								'selector' 		=> '.sidebar_primary .sidebar_inner'
							),
							array(
								'id' 			=> '_css_sidebar_primary_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.sidebar_primary .sidebar_inner'
							),
							array(
								'id' 			=> '_css_sidebar_primary_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.sidebar_primary .sidebar_inner'
							),
							array(
								'id' 			=> '_css_sidebar_secondary',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Secondary sidebar area', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'background', 'border' ),
								'selector' 		=> '.sidebar_secondary .sidebar_inner'
							),
							array(
								'id' 			=> '_css_sidebar_secondary_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.sidebar_secondary .sidebar_inner'
							),
							array(
								'id' 			=> '_css_sidebar_secondary_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.sidebar_secondary .sidebar_inner'
							),
							array(
								'id' 			=> '_css_widgets',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Widgets', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'font-size', 'background', 'border' ),
								'selector' 		=> '.widget'
							),
							array(
								'id' 			=> '_css_widgets_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.widget'
							),
							array(
								'id' 			=> '_css_widgets_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.widget'
							),
							array(
								'id' 			=> '_css_widgets_headline',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Widgets headline', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'background', 'font-size', 'text-align', 'border' ),
								'selector' 		=> '.widget > h4'
							),
							array(
								'id' 			=> '_css_widgets_headline_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.widget > h4'
							),
							array(
								'id' 			=> '_css_widgets_headline_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.widget > h4'
							),
							array(
								'id' 			=> '_css_widgets_links',
								'hover_id' 		=> '_css_widgets_links_hover',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Widgets links', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color' ),
								'selector' 		=> '.widget a'
							),
							array(
								'id' 			=> '_css_widgets_links_hover',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.widget a:hover'
							),
							array(
								'id' 			=> '_css_all_img_tags',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Images', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'background', 'padding', 'border' ),
								'selector'    => 'img, .cz_image img'
							),
							array(
								'id' 			=> '_css_input_textarea',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Input, Textarea', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'font-size', 'background', 'border' ),
								'selector' 		=> 'input,textarea,select,.qty'
							),
							array(
								'id' 			=> '_css_input_textarea_focus',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Input on focus', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'background', 'border' ),
								'selector' 		=> 'input:focus,textarea:focus,select:focus'
							),
							array(
								'id' 			=> '_css_select',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Select', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'font-size', 'background', 'border' ),
								'selector' 		=> 'select,.nice-select'
							),
							array(
								'id' 			=> '_css_select_dropdown',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Select Dropdown', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'font-size', 'background', 'border' ),
								'selector' 		=> '.nice-select .list'
							),
							array(
								'id' 			=> '_css_lightbox_bg',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Lightbox Backgroud', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'background', 'border-right-color' ),
								'selector' 		=> '.lg-backdrop'
							),
						)
					),

					array(
						'name'   => 'loading',
						'title'  => esc_html__( 'Loading', 'codevz' ),
						'fields' => array(
							array(
								'id'			=> 'pageloader',
								'type'			=> 'switcher',
								'title'			=> esc_html__( 'Loading', 'codevz' ),
								'help'			=> esc_html__( 'Show loading screen for visitors', 'codevz' ),
							),
							array(
								'id'			=> 'out_loading',
								'type'			=> 'switcher',
								'title'			=> esc_html__('Show loading on links?', 'codevz'),
								'help'			=> esc_html__('By click on any links, loading will display', 'codevz'),
								'dependency'  	=> array( 'pageloader', '==', true ),
								'setting_args' 	=> array( 'transport' => 'postMessage' )
							),
							array(
								'id' 			=> '_css_preloader',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Loading Background', 'codevz' ),
								'help' 			=> esc_html__( 'Color or image', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'background' ),
								'selector' 		=> '.pageloader',
								'dependency' 	=> array( 'pageloader', '==', true )
							),
							array(
								'id' 			=> '_css_preloader_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.pageloader',
							),
							array(
								'id' 			=> '_css_preloader_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.pageloader',
							),
							array(
								'id'			=> 'pageloader_img',
								'type'			=> 'upload',
								'title'			=> esc_html__('Loading image', 'codevz'),
								'help'			=> esc_html__('Recommended GIF animation image', 'codevz'),
								'preview'       => 1,
								'dependency'  	=> array( 'pageloader', '==', true ),
								'setting_args' 	=> array( 'transport' => 'postMessage' )
							),
							array(
								'id'        => 'pageloader_fx',
								'type'      => 'select',
								'title'     => esc_html__( 'Loading image animation', 'codevz' ),
								'options' 	=> array(
									'' 				=> esc_html__( 'Select', 'codevz' ),
									'cz_load_fx1' 	=> esc_html__( 'Animation', 'codevz' ) . ' 1',
									'cz_load_fx2' 	=> esc_html__( 'Animation', 'codevz' ) . ' 2',
								),
								'dependency'    => array( 'pageloader', '==', true ),
							),
							array(
								'id'			=> 'pageloader_time',
								'type'			=> 'slider',
								'title'			=> esc_html__('Timeout (Recommended)', 'codevz'),
								'help'			=> esc_html__('Hide loading screen after this miliseconds time', 'codevz'),
								'options' 		=> array( 'unit' => '', 'step' => 500, 'min' => 500, 'max' => 10000 ),
								'dependency' 	=> array( 'pageloader', '==', true ),
								'setting_args' 	=> array( 'transport' => 'postMessage' )
							),
						),
					),

					array(
						'name'    => 'page_404',
						'title'   => esc_html__( 'Page 404', 'codevz' ),
						'fields'  => array(
							array(
								'id'            => '404',
								'type'          => 'select',
								'title'         => esc_html__( 'Default or select custom page', 'codevz' ),
								'help'          => esc_html__( 'You can create a new page from Dashboard > Page and assing it here for page 404', 'codevz' ),
								'options'       => Codevz_Plus::$array_pages,
								'default_option'=> esc_html__( 'Select', 'codevz' ),
								'setting_args'  => array('transport' => 'postMessage')
							),
							array(
								'id'            => '404_msg',
								'type'          => 'text',
								'title'         => esc_html__( 'Message 404', 'codevz' ),
								'default'       => 'How did you get here?! It’s cool. We’ll help you out.',
								'setting_args'  => array('transport' => 'postMessage')
							),
							array(
								'id'            => '404_btn',
								'type'          => 'text',
								'title'         => esc_html__( 'Button 404', 'codevz' ),
								'default'       => 'Back to Homepage',
								'setting_args'  => array('transport' => 'postMessage')
							),
						)
					),

					array(
						'name'   => 'ajax',
						'title'  => esc_html__( 'AJAX Mode', 'codevz' ),
						'fields' => array(
							array(
								'id' 			=> 'ajax',
								'type' 			=> 'switcher',
								'title' 		=> esc_html__( 'Ajax', 'codevz' ),
								'help'			=> esc_html__( 'Ajax mode will loads pages without reloading browser and uses fewer server resources', 'codevz'),
							),
							array(
								'id' 			=> '_css_ajax_loader',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Spinner styling', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'background', 'border-right-color' ),
								'selector' 		=> '.cz_ajax_loader',
								'dependency' 	=> array( 'ajax', '==', true )
							),
							array(
								'id' 			=> '_css_ajax_loader_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz_ajax_loader',
							),
							array(
								'id' 			=> '_css_ajax_loader_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz_ajax_loader',
							),
							array(
								'id'			=> 'ajax_loader',
								'type'			=> 'upload',
								'title'			=> esc_html__('Spinner image', 'codevz'),
								'help'			=> esc_html__('Recommended GIF animation image', 'codevz'),
								'preview'       => 1,
								'dependency'  	=> array( 'ajax', '==', true )
							),
							array(
								'id'            => 'ajax_mp',
								'type'          => 'switcher',
								'title'         => esc_html__( 'Music player?', 'codevz' ),
								'dependency' 	=> array( 'ajax', '==', true ),
							),
							array(
								'id'              => 'ajax_mp_tracks',
								'type'            => 'group',
								'title' 		  => esc_html__('Add track(s)', 'codevz'),
								'button_title'    => esc_html__('Add new', 'codevz'),
								'dependency' 	  => array( 'ajax_mp|ajax', '==|==', 'true|true' ),
								'fields'          => array(
									array(
										'id'          => 'title',
										'type'        => 'text',
										'title'       => esc_html__('Title', 'codevz')
									),
									array(
										'id'          => 'badge',
										'type'        => 'text',
										'title'       => esc_html__('Badge', 'codevz')
									),
									array(
										'id'          => 'mp3',
										'type'        => 'upload',
										'title'       => esc_html__('MP3 or Stream URL', 'codevz'),
										'settings'   => array(
											'upload_type'  => 'audio/mpeg',
											'frame_title'  => 'Upload / Select',
											'insert_title' => 'Insert',
										),
									),
								)
							),
							array(
								'id'            => 'ajax_mp_autoplay',
								'type'          => 'switcher',
								'title'         => esc_html__( 'Auto play?', 'codevz' ),
								'dependency' 	=> array( 'ajax_mp|ajax', '==|==', 'true|true' ),
							),
							array(
								'id'            => 'ajax_mp_flat',
								'type'          => 'switcher',
								'title'         => esc_html__( 'Flat mode?', 'codevz' ),
								'dependency' 	=> array( 'ajax_mp|ajax', '==|==', 'true|true' ),
							),
							array(
								'id'            => 'ajax_mp_dark_text',
								'type'          => 'switcher',
								'title'         => esc_html__( 'Dark text?', 'codevz' ),
								'dependency' 	=> array( 'ajax_mp|ajax', '==|==', 'true|true' ),
							),
							array(
								'id' 			=> '_css_ajax_mp',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Player styling', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'background', 'border', 'width', 'float' ),
								'selector' 		=> '#cz_ajax_mp .bd.sm2-main-controls, #cz_ajax_mp .bd.sm2-playlist-drawer',
								'dependency' 	=> array( 'ajax_mp|ajax', '==|==', 'true|true' ),
							),
							array(
								'id' 			=> '_css_ajax_mp_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '#cz_ajax_mp .bd.sm2-main-controls, #cz_ajax_mp .bd.sm2-playlist-drawer',
							),
							array(
								'id' 			=> '_css_ajax_mp_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '#cz_ajax_mp .bd.sm2-main-controls, #cz_ajax_mp .bd.sm2-playlist-drawer',
							),
						)
					),

					array(
						'name'   => 'nicescroll',
						'title'  => esc_html__( 'Nicescroll', 'codevz' ),
						'fields' => array(
							array(
								'id' 		=> 'nicescroll',
								'type' 		=> 'switcher',
								'title' 	=> esc_html__( 'NiceScroll', 'codevz' ),
								'default' 	=> false
							),
							array(
								'id'              => 'nicescroll_opt',
								'type'            => 'group',
								'limit'           => 1,
								'title' 		  => esc_html__('Configuration', 'codevz'),
								'button_title'    => esc_html__('Parameters', 'codevz'),
								'fields'          => array(
									array(
										'id'        => 'railalign',
										'type'      => 'select',
										'title'     => esc_html__( 'Position?', 'codevz' ),
										'options' 	=> array(
											'right' 		=> esc_html__( 'Right', 'codevz' ),
											'left' 			=> esc_html__( 'Left', 'codevz' )
										),
										'default' 	=> 'right',
									),
									array(
										'id'        => 'cursorcolor',
										'type'      => 'color_picker',
										'title'     => esc_html__( 'Scrollbar color', 'codevz' )
									),
									array(
										'id'        => 'background',
										'type'      => 'color_picker',
										'title'     => esc_html__( 'Rail background', 'codevz' )
									),
									array(
										'id'        => 'cursoropacitymin',
										'type'      => 'select',
										'title'     => esc_html__( 'Scrollbar opacity inactive', 'codevz' ),
										'options' 	=> array(
											'1' 			=> '1',
											'0.9' 			=> '0.9',
											'0.8' 			=> '0.8',
											'0.7' 			=> '0.7',
											'0.6' 			=> '0.6',
											'0.5' 			=> '0.5',
											'0.4' 			=> '0.4',
											'0.3' 			=> '0.3',
											'0.2' 			=> '0.2',
											'0.1' 			=> '0.1',
											'00' 			=> '0',
										),
									),
									array(
										'id'        => 'cursoropacitymax',
										'type'      => 'select',
										'title'     => esc_html__( 'Scrollbar opacity active', 'codevz' ),
										'options' 	=> array(
											'1' 			=> '1',
											'0.9' 			=> '0.9',
											'0.8' 			=> '0.8',
											'0.7' 			=> '0.7',
											'0.6' 			=> '0.6',
											'0.5' 			=> '0.5',
											'0.4' 			=> '0.4',
											'0.3' 			=> '0.3',
											'0.2' 			=> '0.2',
											'0.1' 			=> '0.1',
											'00' 			=> '0',
										),
									),
									array(
										'id'        => 'cursorwidth',
										'type'      => 'slider',
										'title'     => esc_html__( 'Scrollbar width', 'codevz' ),
										'default'	=> '8px',
										'options' 	=> array( 'unit' => 'px', 'step' => 1, 'min' => 1, 'max' => 50 )
									),
									array(
										'id'        => 'cursorborderradius',
										'type'      => 'slider',
										'title'     => esc_html__( 'Scrollbar border radius', 'codevz' ),
										'default'	=> '20px',
										'options' 	=> array( 'unit' => 'px', 'step' => 1, 'min' => 1, 'max' => 50 )
									),
									array(
										'id'        => 'scrollspeed',
										'type'      => 'slider',
										'title'     => esc_html__( 'Scrolling speed', 'codevz' ),
										'options' 	=> array( 'unit' => '', 'step' => 10, 'min' => 10, 'max' => 120 )
									),
									array(
										'id'        => 'scrollspeed',
										'type'      => 'slider',
										'title'     => esc_html__( 'Scrolling speed mouse wheel', 'codevz' ),
										'options' 	=> array( 'unit' => '', 'step' => 10, 'min' => 10, 'max' => 120 )
									),

								),
								'dependency' => array( 'nicescroll', '==', 'true' )
							),

						)
					),

					array(
						'name'   => 'custom_codes',
						'title'  => esc_html__( 'Custom codes', 'codevz' ),
						'fields' => array(
							array(
								'id'		=> 'dev_css',
								'type'		=> 'textarea',
								'title'		=> 'DEV CSS',
								'dependency'  => array( 'dev', '==', 'xxx' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' )
							),
							array(
								'id'		=> 'css',
								'type'		=> 'textarea',
								'title'		=> esc_html__('Custom CSS', 'codevz'),
								'help'		=> esc_html__('Insert codes without style tag', 'codevz'),
								'attributes' => array(
									'placeholder' => ".selector {font-size: 20px}",
				  					'style'       => "direction: ltr",
								),
								'setting_args' 	=> array( 'transport' => 'postMessage' )
							),
							array(
								'id'		=> 'js',
								'type'		=> 'textarea',
								'title'		=> esc_html__('Custom JS', 'codevz'),
								'help'		=> esc_html__('Insert codes without script tag or HTML', 'codevz'),
								'attributes' => array(
									'placeholder' => "jQuery('.selector').addClass('class');",
				  					'style'       => "direction: ltr",
								)
							),
							array(
								'id'		=> 'head_codes',
								'type'		=> 'textarea',
								'title'		=> esc_html__('Before closing &lt;/head&gt;', 'codevz'),
								'help'		=> esc_html__('If you have google analytics code, insert it here.', 'codevz'),
								'attributes' => array(
								  'style'       => "direction: ltr",
								),
							),
							array(
								'id'		=> 'foot_codes',
								'type'		=> 'textarea',
								'title'		=> esc_html__('Before closing &lt;/body&gt;', 'codevz'),
								'attributes' => array(
								  'style'       => "direction: ltr",
								),
							),
						),
					),

					array(
						'name'   => 'general_more',
						'title'  => esc_html__( 'Advanced settings', 'codevz' ),
						'fields' => array(
							array(
								'id'            => 'maintenance_mode',
								'type'          => 'select',
								'title'         => esc_html__( 'Maintenance Mode?', 'codevz' ),
								'help'          => esc_html__( 'You can create a coming soon or maintenance mode page then assign it here. All your website visitors will redirect to this page.', 'codevz' ),
								'options'       => Codevz_Plus::$array_pages,
								'default_option'=> esc_html__( 'Select', 'codevz' ),
								'setting_args'  => array( 'transport' => 'postMessage' )
							),
							array(
								'id'            => 'lazyload',
								'type'          => 'switcher',
								'title'         => esc_html__('Lazyload images?', 'codevz'),
								'help'          => esc_html__('Speed up your site by loading images on page scrolling', 'codevz'),
								'setting_args'  => array('transport' => 'postMessage')
							),
							array(
								'id' 			  => 'seo_meta_tags',
								'type' 			  => 'switcher',
								'title' 		  => esc_html__( 'SEO meta tags?', 'codevz' ),
								'help' 			  => esc_html__( 'If you are not using any SEO plugin, So turn this option ON, This will automatically add meta tags to all pages according to page title, content and kewords.', 'codevz' ),
								'setting_args' 	  => array( 'transport' => 'postMessage' )
							),
							array(
								'id' 			  => 'seo_desc',
								'type' 			  => 'text',
								'title' 		  => esc_html__( 'SEO description', 'codevz' ),
								'help' 			  => esc_html__( 'Short description about your site', 'codevz' ),
								'setting_args' 	  => array( 'transport' => 'postMessage' ),
								'dependency' 	  => array( 'seo_meta_tags', '==', 'true' )
							),
							array(
								'id' 			  => 'seo_keywords',
								'type' 			  => 'text',
								'title' 		  => esc_html__( 'SEO keywords', 'codevz' ),
								'help' 			  => esc_html__( 'Separate with comma, e.g. Business,Company,WordPress', 'codevz' ),
								'setting_args' 	  => array( 'transport' => 'postMessage' ),
								'dependency' 	  => array( 'seo_meta_tags', '==', 'true' )
							),
							array(
								'id' 			  => 'vc_disable_templates',
								'type' 			  => 'switcher',
								'title' 		  => esc_html__( 'Disable templates?', 'codevz' ),
								'help' 			  => esc_html__( 'If you don\'t need premium templates in page builder, check this option. This will improve your page builder load speed.', 'codevz' ),
								'setting_args' 	  => array( 'transport' => 'postMessage' )
							),
							array(
								'id'            => 'popup',
								'type'          => 'select',
								'title'         => esc_html__( 'Popup modal box?', 'codevz' ),
								'help' 			=> esc_html__( 'Select page that contains popup modal box element.', 'codevz' ),
								'options'       => Codevz_Plus::$array_pages,
								'default_option'=> esc_html__( 'Select', 'codevz' ),
							),
							array(
								'id'              => 'add_post_type',
								'type'            => 'group',
								'title' 		  => esc_html__('Add CPT (DEPRECATED)', 'codevz'),
								'desc' 			  => esc_html__( 'DO NOT use this option for adding post tpye, Instead install and use Custom Post Type UI plugin.', 'codevz' ),
								'button_title'    => esc_html__('Add', 'codevz'),
								'fields'          => array(
									array(
										'id'          => 'name',
										'type'        => 'text',
										'title'       => esc_html__('Unique Name', 'codevz'),
										'desc' 		  => 'e.g. projects or movies',
										'setting_args'=> array( 'transport' => 'postMessage' ),
									),
								),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
							),
						),

					),
				),
			);

			$options[]   = array(
				'name' 		=> 'typography',
				'title' 	=> esc_html__( 'Typography', 'codevz' ),
				'fields' => array(

					array(
						'id' 			=> '_css_body_typo',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'Body font', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'color', 'font-family', 'font-size', 'line-height' ),
						'selector' 		=> 'body'
					),
					array(
						'id' 			=> '_css_body_typo_tablet',
						'type' 			=> 'cz_sk_hidden',
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'selector' 		=> 'body'
					),
					array(
						'id' 			=> '_css_body_typo_mobile',
						'type' 			=> 'cz_sk_hidden',
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'selector' 		=> 'body'
					),
					array(
						'id' 			=> '_css_menu_nav_typo',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'Menu font', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'font-family' ),
						'selector' 		=> '.sf-menu a'
					),
					array(
						'id' 			=> '_css_all_headlines',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'Headlines font', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'color', 'font-family', 'line-height' ),
						'selector' 		=> 'h1,h2,h3,h4,h5,h6'
					),
					array(
						'id' 			=> '_css_all_headlines_tablet',
						'type' 			=> 'cz_sk_hidden',
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'selector' 		=> 'h1,h2,h3,h4,h5,h6'
					),
					array(
						'id' 			=> '_css_all_headlines_mobile',
						'type' 			=> 'cz_sk_hidden',
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'selector' 		=> 'h1,h2,h3,h4,h5,h6'
					),
					array(
						'id' 			=> '_css_h1',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'H1', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'color', 'font-family', 'font-size', 'line-height' ),
						'selector' 		=> 'h1'
					),
					array(
						'id' 			=> '_css_h1_tablet',
						'type' 			=> 'cz_sk_hidden',
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'selector' 		=> 'h1'
					),
					array(
						'id' 			=> '_css_h1_mobile',
						'type' 			=> 'cz_sk_hidden',
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'selector' 		=> 'h1'
					),
					array(
						'id' 			=> '_css_h2',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'H2', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'color', 'font-family', 'font-size', 'line-height' ),
						'selector' 		=> 'h2'
					),
					array(
						'id' 			=> '_css_h2_tablet',
						'type' 			=> 'cz_sk_hidden',
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'selector' 		=> 'h2'
					),
					array(
						'id' 			=> '_css_h2_mobile',
						'type' 			=> 'cz_sk_hidden',
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'selector' 		=> 'h2'
					),
					array(
						'id' 			=> '_css_h3',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'H3', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'color', 'font-family', 'font-size', 'line-height' ),
						'selector' 		=> 'h3'
					),
					array(
						'id' 			=> '_css_h3_tablet',
						'type' 			=> 'cz_sk_hidden',
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'selector' 		=> 'h3'
					),
					array(
						'id' 			=> '_css_h3_mobile',
						'type' 			=> 'cz_sk_hidden',
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'selector' 		=> 'h3'
					),
					array(
						'id' 			=> '_css_h4',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'H4', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'color', 'font-family', 'font-size', 'line-height' ),
						'selector' 		=> 'h4'
					),
					array(
						'id' 			=> '_css_h4_tablet',
						'type' 			=> 'cz_sk_hidden',
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'selector' 		=> 'h4'
					),
					array(
						'id' 			=> '_css_h4_mobile',
						'type' 			=> 'cz_sk_hidden',
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'selector' 		=> 'h4'
					),
					array(
						'id' 			=> '_css_h5',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'H5', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'color', 'font-family', 'font-size', 'line-height' ),
						'selector' 		=> 'h5'
					),
					array(
						'id' 			=> '_css_h5_tablet',
						'type' 			=> 'cz_sk_hidden',
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'selector' 		=> 'h5'
					),
					array(
						'id' 			=> '_css_h5_mobile',
						'type' 			=> 'cz_sk_hidden',
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'selector' 		=> 'h5'
					),
					array(
						'id' 			=> '_css_h6',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'H6', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'color', 'font-family', 'font-size', 'line-height' ),
						'selector' 		=> 'h6'
					),
					array(
						'id' 			=> '_css_h6_tablet',
						'type' 			=> 'cz_sk_hidden',
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'selector' 		=> 'h6'
					),
					array(
						'id' 			=> '_css_h6_mobile',
						'type' 			=> 'cz_sk_hidden',
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'selector' 		=> 'h6'
					),
					array(
						'id' 			=> '_css_p',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'Paragraphs', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'color', 'font-size', 'line-height', 'margin' ),
						'selector' 		=> 'p'
					),
					array(
						'id' 			=> '_css_p_tablet',
						'type' 			=> 'cz_sk_hidden',
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'selector' 		=> 'p'
					),
					array(
						'id' 			=> '_css_p_mobile',
						'type' 			=> 'cz_sk_hidden',
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'selector' 		=> 'p'
					),
					array(
						'id' 			=> '_css_a',
						'hover_id' 		=> '_css_a_hover',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'Links', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'color', 'font-weight', 'font-style', 'text-decoration' ),
						'selector' 		=> 'a'
					),
					array(
						'id' 			=> '_css_a_tablet',
						'type' 			=> 'cz_sk_hidden',
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'selector' 		=> 'a'
					),
					array(
						'id' 			=> '_css_a_mobile',
						'type' 			=> 'cz_sk_hidden',
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'selector' 		=> 'a'
					),
					array(
						'id' 			=> '_css_a_hover',
						'type' 			=> 'cz_sk_hidden',
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'selector' 		=> 'a:hover'
					),

					array(
						'id'              => 'wp_editor_fonts',
						'type'            => 'group', 
						'title' 		  => esc_html__( 'Add google fonts for WP Editor', 'codevz' ),
						'help' 			  => esc_html__( 'You can add custom google fonts and use them inside WP Editor in posts or page builder elements', 'codevz' ),
						'desc' 			  => esc_html__( 'Maximum add 2 fonts', 'codevz' ),
						'button_title'    => esc_html__( 'Add', 'codevz' ),
						'fields'          => array(
							array(
								'id' 		     => 'font',
								'type' 		     => 'select_font',
								'title' 	     => esc_html__('Font family', 'codevz')
							),
						),
						'setting_args' 	  => array( 'transport' => 'postMessage' )
					),
					array(
						'id'              => 'custom_fonts',
						'type'            => 'group', 
						'title' 		  => esc_html__( 'Add custom font name', 'codevz' ),
						'help' 			  => esc_html__( 'You can add your own custom font name and access it from fonts library and WP Editor, You should upload font files and add font CSS via child theme or other way by yourself', 'codevz' ),
						'desc' 			  => esc_html__( 'Save and refresh is required', 'codevz' ),
						'button_title'    => esc_html__( 'Add', 'codevz' ),
						'fields'          => array(
							array(
								'id' 		     => 'font',
								'type' 		     => 'text',
								'title' 	     => esc_html__('Font name', 'codevz')
							),
						),
						'setting_args' 	  => array( 'transport' => 'postMessage' )
					),
				),
			);

	  /*
	  // Export headers array
	  $new_header = array();
	  foreach ( self::reset_header() as $k => $v ) {
		$v = Codevz_Plus::option( $k );
		if ( $v ) {
		  if ( is_array( $v ) ) {
			foreach ( $v as $kk => $vv ) {
				foreach ( $vv as $kkk => $vvv ) {
				  if ( is_array( $vvv ) ) {
					$vv[$kkk] = array_filter( $vvv );
				  }
				}
			  $v[$kk] = array_filter( $vv );
			}
		  }

		  $new_header[ $k ] = $v;
		}
	  }
	  ob_start();
	  var_export( $new_header );
	  $new_header = ob_get_clean();
	  */

	  //$options['header'] = array(
			$options[] = array(
				'name' 		=> 'header',
				'title' 	=> esc_html__( 'Header', 'codevz' ),
				'sections' => array(

		  /*array(
			'name'   => 'header_preset',
			'title'  => esc_html__( 'Header Preset', 'codevz' ),
			'fields' => array(
			  array(
				'type'    => 'content',
				'content' => '<textarea disabled="disabled" rows="10" style="width:100%">'. $new_header .'</textarea>'
			  ),
			  array(
				'type'      => 'content',
				'content'   => '<div class="csf-field-header-preset"><a href="#" class="button csf-header-preset-add">' . esc_html__( 'Open Header Preset', 'codevz' ) . '</a></div>'
			  ),
			  array(
				'id'            => 'header_preset',
				'type'          => 'text',
				'title'         => '',
				'setting_args'  => array( 'transport' => 'postMessage' )
			  ),
			)
		  ),*/
		  array(
			'name'   => 'header_logo',
			'title'  => esc_html__( 'Logo', 'codevz' ),
			'fields' => array(
							array(
								'id' 			=> 'logo',
								'type' 			=> 'upload',
								'title' 		=> esc_html__( 'Logo', 'codevz' ),
								'preview'       => 1,
								'setting_args' 	=> array('transport' => 'postMessage')
							),
							array(
								'id' 			=> 'logo_2',
								'type' 			=> 'upload',
								'title' 		=> esc_html__( 'Logo 2 (alternative)', 'codevz' ),
								'help' 			=> esc_html__( 'Useful for sticky header or footer', 'codevz' ),
								'preview'       => 1,
								'setting_args' 	=> array('transport' => 'postMessage')
							),
							array(
								'id'            => 'logo_hover_tooltip',
								'type'          => 'select',
								'title'         => esc_html__( 'Logo tooltip', 'codevz' ),
								'options'       => Codevz_Plus::$array_pages,
								'default_option'=> esc_html__( 'Select', 'codevz' ),
							),
							array(
								'type'    => 'notice',
								'class'   => 'info',
								'content' => esc_html__( 'Styles', 'codevz' )
							),
							array(
								'id'            => '_css_logo_css',
								'type'          => 'cz_sk',
								'button'        => esc_html__( 'Logo', 'codevz' ),
								'setting_args'  => array( 'transport' => 'postMessage' ),
								'settings'      => array( 'color', 'background', 'font-family', 'font-size', 'border' ),
								'selector'      => '.logo > a, .logo > h1, .logo h2',
							),
							array(
								'id' 			=> '_css_logo_css_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.logo > a, .logo > h1, .logo h2',
							),
							array(
								'id' 			=> '_css_logo_css_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.logo > a, .logo > h1, .logo h2',
							),
							array(
								'id' 			=> '_css_logo_2_css',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Logo 2', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings'      => array( 'color', 'background', 'font-family', 'font-size', 'border' ),
								'selector' 		=> '.logo_2 > a, .logo_2 > h1'
							),
							array(
								'id' 			=> '_css_logo_2_css_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.logo_2 > a, .logo_2 > h1'
							),
							array(
								'id' 			=> '_css_logo_2_css_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.logo_2 > a, .logo_2 > h1'
							),
							array(
								'id' 			=> '_css_logo_hover_tooltip',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Logo tooltip', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'background', 'width', 'border' ),
								'selector' 		=> '.logo_hover_tooltip',
								'dependency' 	=> array( 'logo_hover_tooltip', '!=', '' )
							),
							array(
								'id' 			=> '_css_logo_hover_tooltip_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.logo_hover_tooltip',
							),
							array(
								'id' 			=> '_css_logo_hover_tooltip_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.logo_hover_tooltip',
							),
						)
					),

					array(
						'name'   => 'header_social',
						'title'  => esc_html__( 'Social Icons', 'codevz' ),
						'fields' => array(
							array(
								'id'              => 'social',
								'type'            => 'group',
								'title'           => esc_html__( 'Social Icons', 'codevz' ),
								'button_title'    => esc_html__( 'Add', 'codevz' ),
								'accordion_title' => esc_html__( 'Add', 'codevz' ),
								'fields'          => array(
									array(
										'id'    	=> 'title',
										'type'  	=> 'text',
										'title' 	=> esc_html__( 'Title', 'codevz' )
									),
									array(
										'id'    	=> 'icon',
										'type'  	=> 'icon',
										'title' 	=> esc_html__( 'Icon', 'codevz' ),
										'default' 	=> 'fa fa-facebook'
									),
									array(
										'id'    	=> 'link',
										'type'  	=> 'text',
										'title' 	=> esc_html__( 'Link', 'codevz' )
									),
								),
								'setting_args' 	     => array( 'transport' => 'postMessage' ),
								'selective_refresh'  => array(
									'selector' 			=> '.elms_row .cz_social',
									'settings' 			=> Codevz_Plus::$options_id . '[social]',
									'render_callback'  	=> function() {
										return Codevz_Plus::social();
									},
									'container_inclusive' => true
								),
							),
							array(
								'id'            => 'social_hover_fx',
								'type'          => 'select',
								'title'         => esc_html__( 'Hover effect?', 'codevz' ),
								'options'       => array(
									'cz_social_fx_0' => esc_html__( 'ZoomIn', 'codevz' ),
									'cz_social_fx_1' => esc_html__( 'ZoomOut', 'codevz' ),
									'cz_social_fx_2' => esc_html__( 'Bottom to Top', 'codevz' ),
									'cz_social_fx_3' => esc_html__( 'Top to Bottom', 'codevz' ),
									'cz_social_fx_4' => esc_html__( 'Left to Right', 'codevz' ),
									'cz_social_fx_5' => esc_html__( 'Right to Left', 'codevz' ),
									'cz_social_fx_6' => esc_html__( 'Rotate', 'codevz' ),
									'cz_social_fx_7' => esc_html__( 'Infinite Shake', 'codevz' ),
									'cz_social_fx_8' => esc_html__( 'Infinite Wink', 'codevz' ),
									'cz_social_fx_9' => esc_html__( 'Quick Bob', 'codevz' ),
									'cz_social_fx_10'=> esc_html__( 'Flip Horizontal', 'codevz' ),
									'cz_social_fx_11'=> esc_html__( 'Flip Vertical', 'codevz' ),
								),
								'default_option' => esc_html__( 'Select', 'codevz'),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selective_refresh' => array(
									'selector' 			=> '.elms_row .cz_social',
									'settings' 			=> Codevz_Plus::$options_id . '[social_hover_fx]',
									'render_callback' 	=> function() {
										return Codevz_Plus::social();
									},
									'container_inclusive' => true
								),
							),
							array(
								'id'            => 'social_color_mode',
								'type'          => 'select',
								'title'         => esc_html__( 'Color mode?', 'codevz' ),
								'options'       => array(
									'cz_social_colored' 		=> esc_html__( 'Original colors', 'codevz' ),
									'cz_social_colored_hover' 	=> esc_html__( 'Original colors on :Hover', 'codevz' ),
									'cz_social_colored_bg' 		=> esc_html__( 'Original background', 'codevz' ),
									'cz_social_colored_bg_hover' => esc_html__( 'Original background on :Hover', 'codevz' ),
								),
								'default_option' => esc_html__( 'Select', 'codevz'),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selective_refresh' => array(
									'selector' 			=> '.elms_row .cz_social',
									'settings' 			=> Codevz_Plus::$options_id . '[social_color_mode]',
									'render_callback' 	=> function() {
										return Codevz_Plus::social();
									},
									'container_inclusive' => true
								),
							),
			  array(
				'id'            => 'social_inline_title',
				'type'          => 'switcher',
				'title'         => esc_html__( 'Inline titles?', 'codevz' ),
				'setting_args'  => array( 'transport' => 'postMessage' ),
				'selective_refresh' => array(
				  'selector'      => '.elms_row .cz_social',
				  'settings'      => Codevz_Plus::$options_id . '[social_inline_title]',
				  'render_callback'   => function() {
					return Codevz_Plus::social();
				  },
									'container_inclusive' => true
				),
			  ),
			  array(
				'id'            => 'social_tooltip',
				'type'          => 'select',
				'title'         => esc_html__( 'Tooltip?', 'codevz' ),
				'help'          => esc_html__( 'Required title for each social icons', 'codevz' ),
				'options'       => array(
				  'cz_tooltip cz_tooltip_up'    => esc_html__( 'Up', 'codevz' ),
				  'cz_tooltip cz_tooltip_down'  => esc_html__( 'Down', 'codevz' ),
				  'cz_tooltip cz_tooltip_right' => esc_html__( 'Right', 'codevz' ),
				  'cz_tooltip cz_tooltip_left'  => esc_html__( 'Left', 'codevz' ),
				),
				'default_option' => esc_html__( 'Select', 'codevz'),
				'setting_args'  => array( 'transport' => 'postMessage' ),
				'selective_refresh' => array(
				  'selector'      => '.elms_row .cz_social',
				  'settings'      => Codevz_Plus::$options_id . '[social_tooltip]',
				  'render_callback'   => function() {
					return Codevz_Plus::social();
				  },
									'container_inclusive' => true
				),
			  ),

							array(
								'id' 			=> '_css_social',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Container', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'background', 'padding', 'margin', 'border', 'box-shadow' ),
								'selector' 		=> '.elms_row .cz_social, .fixed_side .cz_social'
							),
							array(
								'id' 			=> '_css_social_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.elms_row .cz_social, .fixed_side .cz_social'
							),
							array(
								'id' 			=> '_css_social_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.elms_row .cz_social, .fixed_side .cz_social'
							),
							array(
								'id' 			=> '_css_social_a',
								'hover_id' 		=> '_css_social_a_hover',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Social icons', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'background', 'font-size', 'padding', 'margin', 'border', 'box-shadow' ),
								'selector' 		=> '.elms_row .cz_social a, .fixed_side .cz_social a'
							),
							array(
								'id' 			=> '_css_social_a_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.elms_row .cz_social a, .fixed_side .cz_social a'
							),
							array(
								'id' 			=> '_css_social_a_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.elms_row .cz_social a, .fixed_side .cz_social a'
							),
						  array(
							'id'      => '_css_social_a_hover',
							'type'      => 'cz_sk_hidden',
							'setting_args'  => array( 'transport' => 'postMessage' ),
							'selector'    => '.elms_row .cz_social a:hover, .fixed_side .cz_social a:hover'
						  ),
						  array(
							'id'      => '_css_social_inline_titles',
							'type'      => 'cz_sk',
							'button'    => esc_html__( 'Inline title', 'codevz' ),
							'setting_args'  => array( 'transport' => 'postMessage' ),
							'settings'    => array( 'color', 'background', 'font-size', 'font-weight', 'letter-spacing', 'line-height', 'padding', 'margin', 'border' ),
							'selector'    => '.elms_row .cz_social a span, .fixed_side .cz_social a span',
							'dependency'  => array( 'social_inline_title', '!=', '' )
						  ),
						  array(
							'id'      => '_css_social_inline_titles_tablet',
							'type'      => 'cz_sk_hidden',
							'setting_args'  => array( 'transport' => 'postMessage' ),
							'selector'    => '.elms_row .cz_social a span, .fixed_side .cz_social a span'
						  ),
						  array(
							'id'      => '_css_social_inline_titles_mobile',
							'type'      => 'cz_sk_hidden',
							'setting_args'  => array( 'transport' => 'postMessage' ),
							'selector'    => '.elms_row .cz_social a span, .fixed_side .cz_social a span'
						  ),
						  array(
							'id'      => '_css_social_tooltip',
							'type'      => 'cz_sk',
							'button'    => esc_html__( 'Tooltip', 'codevz' ),
							'setting_args'  => array( 'transport' => 'postMessage' ),
							'settings'    => array( 'color', 'background', 'font-size', 'font-weight', 'letter-spacing', 'line-height', 'padding', 'margin', 'border' ),
							'selector'    => '.elms_row .cz_social a:after, .fixed_side .cz_social a:after',
							'dependency'  => array( 'social_tooltip', '!=', '' )
						  ),

						),
					),
					array(
						'name'   => 'header_1',
						'title'  => esc_html__( 'Header top bar', 'codevz' ),
						'fields' => self::row_options( 'header_1' )
					),
					array(
						'name'   => 'header_2',
						'title'  => esc_html__( 'Header', 'codevz' ),
						'fields' => self::row_options( 'header_2' )
					),
					array(
						'name'   => 'header_3',
						'title'  => esc_html__( 'Header bottom bar', 'codevz' ),
						'fields' => self::row_options( 'header_3' )
					),
					array(
						'name'   => 'header_5',
						'title'  => esc_html__( 'Sticky Header', 'codevz' ),
						'fields' => self::row_options( 'header_5' )
					),
					array(
						'name'   => 'mobile_header',
						'title'  => esc_html__( 'Mobile Header', 'codevz' ),
						'fields' => self::row_options( 'header_4' )
					),
					array(
						'name'   => 'fixed_side_1',
						'title'  => esc_html__( 'Fixed Side', 'codevz' ),
						'fields' => self::row_options( 'fixed_side_1', array('top','middle','bottom') )
					),
					array(
						'name'   => 'title_br',
						'title'  => esc_html__( 'Title & Breadcrumbs', 'codevz' ),
						'fields' => self::title_options()
					),
					array(
						'name'   => 'header_more',
						'title'  => esc_html__( 'More', 'codevz' ),
						'fields' => array(
							array(
								'id' 			=> '_css_header_container',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Overall header background', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'background', 'border' ),
								'selector' 		=> '.page_header'
							),
							array(
								'id' 			=> '_css_header_container_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.page_header'
							),
							array(
								'id' 			=> '_css_header_container_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.page_header'
							),
							array(
								'id'            => 'hidden_top_bar',
								'type'          => 'select',
								'title'         => esc_html__( 'Extra header panel', 'codevz' ),
								'options'       => Codevz_Plus::$array_pages,
								'selective_refresh' => array(
									'selector' => '.hidden_top_bar > i',
									'container_inclusive' => true
								),
								'default_option'=> esc_html__( 'Select', 'codevz' ),
							),
							array(
								'id' 			=> '_css_hidden_top_bar',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Header panel colors', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'background', 'padding' ),
								'selector' 		=> '.hidden_top_bar',
								'dependency' 	=> array( 'hidden_top_bar', '!=', '' )
							),
							array(
								'id' 			=> '_css_hidden_top_bar_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.hidden_top_bar',
							),
							array(
								'id' 			=> '_css_hidden_top_bar_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.hidden_top_bar',
							),
							array(
								'id' 			=> '_css_hidden_top_bar_handle',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Handle color', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'background' ),
								'selector' 		=> '.hidden_top_bar > i',
								'dependency' 	=> array( 'hidden_top_bar', '!=', '' )
							),
							array(
								'id' 			=> '_css_hidden_top_bar_handle_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.hidden_top_bar > i',
							),
							array(
								'id' 			=> '_css_hidden_top_bar_handle_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.hidden_top_bar > i',
							),
						),
					),

				),
			);

			$options[]   = array(
				'name' 		=> 'footer',
				'title' 	=> esc_html__( 'Footer', 'codevz' ),
				'sections' => array(

					array(
						'name'   => 'footer_1',
						'title'  => esc_html__( 'Footer top bar', 'codevz' ),
						'fields' => self::row_options( 'footer_1' )
					),
					array(
						'name'   => 'footer_widgets',
						'title'  => esc_html__( 'Footer Widgets', 'codevz' ),
						'fields' => array(
							array(
								'id' 	=> 'footer_layout',
								'type' 	=> 'select',
								'title' => esc_html__( 'Footer columns', 'codevz' ),
								'options' => array(
									'' 					=> esc_html__( 'Select', 'codevz' ),
									's12'				=> '1/1',
									's6,s6'				=> '1/2 1/2',
									's4,s8'				=> '1/3 2/3',
									's8,s4'				=> '2/3 1/3',
									's3,s9'				=> '1/4 3/4',
									's9,s3'				=> '3/4 1/4',
									's4,s4,s4'			=> '1/3 1/3 1/3',
									's3,s6,s3'			=> '1/4 2/4 1/4',
									's3,s3,s6'			=> '1/4 1/4 2/4',
									's6,s3,s3'			=> '2/4 1/4 1/4',
									's2,s2,s8'			=> '1/6 1/6 4/6',
									's2,s8,s2'			=> '1/6 4/6 1/6',
									's8,s2,s2'			=> '4/6 1/6 1/6',
									's3,s3,s3,s3'		=> '1/4 1/4 1/4 1/4',
									's6,s2,s2,s2'		=> '3/6 1/6 1/6 1/6',
									's2,s2,s2,s6'		=> '1/6 1/6 1/6 3/6',
									's2,s2,s2,s2,s4'	=> '1/6 1/6 1/6 1/6 2/6',
									's4,s2,s2,s2,s2'	=> '2/6 1/6 1/6 1/6 1/6',
									's2,s2,s4,s2,s2'	=> '1/6 1/6 2/6 1/6 1/6',
									's2,s2,s2,s2,s2,s2'	=> '1/6 1/6 1/6 1/6 1/6 1/6',
								),
							),
							array(
								'id' 			=> '_css_footer',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Container', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'background', 'padding', 'border' ),
								'selector' 		=> '.cz_middle_footer',
								'dependency' 	=> array( 'footer_layout', '!=', '' )
							),
							array(
								'id' 			=> '_css_footer_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz_middle_footer',
							),
							array(
								'id' 			=> '_css_footer_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz_middle_footer',
							),
							array(
								'id' 			=> '_css_footer_row',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Row inner', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'width', 'background', 'border' ),
								'selector' 		=> '.cz_middle_footer > .row',
								'dependency' 	=> array( 'footer_layout', '!=', '' )
							),
							array(
								'id' 			=> '_css_footer_row_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz_middle_footer > .row',
							),
							array(
								'id' 			=> '_css_footer_row_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz_middle_footer > .row',
							),
							array(
								'id' 			=> '_css_footer_widget',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Widgets', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'font-size', 'background', 'padding', 'border' ),
								'selector' 		=> '.footer_widget',
								'dependency' 	=> array( 'footer_layout', '!=', '' )
							),
							array(
								'id' 			=> '_css_footer_widget_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.footer_widget',
							),
							array(
								'id' 			=> '_css_footer_widget_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.footer_widget',
							),
							array(
								'id' 			=> '_css_footer_widget_headlines',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Widgets headlines', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'background', 'font-size', 'line-height', 'padding', 'border' ),
								'selector' 		=> '.footer_widget > h4',
								'dependency' 	=> array( 'footer_layout', '!=', '' )
							),
							array(
								'id' 			=> '_css_footer_widget_headlines_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.footer_widget > h4',
							),
							array(
								'id' 			=> '_css_footer_widget_headlines_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.footer_widget > h4',
							),
							array(
								'id' 			=> '_css_footer_a',
								'hover_id' 		=> '_css_footer_a_hover',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Widgets links', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'font-style' ),
								'selector' 		=> '.cz_middle_footer a',
								'dependency' 	=> array( 'footer_layout', '!=', '' )
							),
							array(
								'id' 			=> '_css_footer_a_hover',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz_middle_footer a:hover',
							),
							array(
								'type'    => 'notice',
								'class'   => 'info',
								'content' => esc_html__( 'You can manage your footer widgets from Appearance > Widgets', 'codevz' )
							),
						),
					),
					array(
						'name'   => 'footer_2',
						'title'  => esc_html__( 'Footer bottom bar', 'codevz' ),
						'fields' => self::row_options( 'footer_2' )
					),
					array(
						'name'   => 'footer_more',
						'title'  => esc_html__( 'More', 'codevz' ),
						'fields' => array(
							array(
								'id' 			=> '_css_overal_footer',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Overall footer background', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'background', 'padding', 'border' ),
								'selector' 		=> '.page_footer'
							),
							array(
								'id' 			=> '_css_overal_footer_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.page_footer'
							),
							array(
								'id' 			=> '_css_overal_footer_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.page_footer'
							),
							array(
								'id' 			=> 'fixed_footer',
								'type' 			=> 'switcher',
								'title' 		=> esc_html__( 'Fixed footer on scrolling?', 'codevz' ),
								'help'			=> esc_html__( 'Body Background color is required for fixed footer. Go to General > Theme color > Body', 'codevz' ),
							),
							array(
								'id'    		=> 'backtotop',
								'type'  		=> 'icon',
								'title' 		=> esc_html__( 'Back to top button', 'codevz' ),
								'default'		=> 'fa fa-angle-up',
								'setting_args' 	=> array( 'transport' => 'postMessage' )
							),
							array(
								'id' 			=> '_css_backtotop',
								'hover_id' 		=> '_css_backtotop_hover',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Back to top styling', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'background', 'font-size', 'border' ),
								'selector' 		=> 'i.backtotop'
							),
							array(
								'id' 			=> '_css_backtotop_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> 'i.backtotop'
							),
							array(
								'id' 			=> '_css_backtotop_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> 'i.backtotop'
							),
							array(
								'id' 			=> '_css_backtotop_hover',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> 'i.backtotop:hover'
							),
							array(
								'id' 			=> 'cf7_beside_backtotop',
								'type' 			=> 'select',
								'title' 		=> esc_html__( 'Quick contact form', 'codevz' ),
								'help' 			=> esc_html__( 'Select page that contains contact form element.', 'codevz' ),
								'options'       => Codevz_Plus::$array_pages,
								'default_option'=> esc_html__( 'Select', 'codevz' ),
							),
							array(
								'id' 			=> '_css_cf7_beside_backtotop_container',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Contact form container', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'background', 'border' ),
								'selector' 		=> 'div.fixed_contact',
								'dependency' 	=> array( 'cf7_beside_backtotop', '!=', '' ),
							),
							array(
								'id' 			=> '_css_cf7_beside_backtotop_container_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> 'div.fixed_contact',
							),
							array(
								'id' 			=> '_css_cf7_beside_backtotop_container_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> 'div.fixed_contact',
							),
							array(
								'id'    		=> 'cf7_beside_backtotop_icon',
								'type'  		=> 'icon',
								'title' 		=> esc_html__( 'Contact icon', 'codevz' ),
								'default'		=> 'fa fa-envelope-o',
								'dependency' => array( 'cf7_beside_backtotop', '!=', '' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
							),
							array(
								'id' 			=> '_css_cf7_beside_backtotop',
								'hover_id' 		=> '_css_cf7_beside_backtotop_hover',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Contact icon', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'background', 'font-size', 'border' ),
								'selector' 		=> 'i.fixed_contact',
								'dependency' 	=> array( 'cf7_beside_backtotop', '!=', '' ),
							),
							array(
								'id' 			=> '_css_cf7_beside_backtotop_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> 'i.fixed_contact',
							),
							array(
								'id' 			=> '_css_cf7_beside_backtotop_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> 'i.fixed_contact',
							),
							array(
								'id' 			=> '_css_cf7_beside_backtotop_hover',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> 'i.fixed_contact:hover',
							),
						),
					),
				),
			);

			$options[]   = array(
				'name' 		=> 'posts',
				'title' 	=> esc_html__( 'Blog', 'codevz' ),
				'sections' => array(

					array(
						'name'   => 'blog_settings',
						'title'  => esc_html__( 'Blog Settings', 'codevz' ),
						'fields' => array(
							array(
								'id' 	=> 'layout_post',
								'type' 	=> 'image_select',
								'title' => esc_html__( 'Sidebar position', 'codevz' ),
								'desc'  => esc_html__( 'The default is from General > Layout', 'codevz' ),
								'help'  => esc_html__( 'Blog archive and blog posts', 'codevz' ),
								'options' 	=> array(
									'1'           	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-0.png',
									'ws' 			=> CDVZ_PLUGIN_URI . 'assets/admin_img/off.png',
									'bpnp'      	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-2.png',
									'center' 		=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-13.png',
									'right'       	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-3.png',
									'right-s'     	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-4.png',
									'left'        	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-5.png',
									'left-s'      	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-6.png',
									'both-side' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-7.png',
									'both-side2' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-8.png',
									'both-right'	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-9.png',
									'both-right2'	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-10.png',
									'both-left' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-11.png',
									'both-left2' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-12.png',
								),
								'default' => '1'
							),
							array(
								'id' 		=> 'template_style',
								'type' 		=> 'image_select',
								'title' 	=> esc_html__( 'Template', 'codevz' ),
								'help'  	=> esc_html__( 'Blog archive page, category page, tags page, etc.', 'codevz' ),
								'options' 	=> array(
									'1'			=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-1.png',
									'2'			=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-2.png',
									'6'			=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-1-2.png',
									'3'			=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-3.png',
									'4'			=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-4.png',
									'5'			=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-5.png',
									'7'			=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-7.png',
									'8'			=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-8.png',
									'9'			=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-9.png',
									'10'		=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-10.png',
									'11'		=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-11.png',
									'12'		=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-12.png',
									'13'		=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-13.png',
									'14'		=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-14.png',
									'x' 		=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-x.png',
								),
								'attributes' => array(
									'data-depend-id' => 'template_style'
								)
							),
						  array(
							'id'    => 'template_post',
							'type'    => 'select',
							'title'   => esc_html__( 'Custom page', 'codevz' ),
							'options'   => Codevz_Plus::$array_pages,
							'default_option'=> esc_html__( 'Select', 'codevz' ),
							'dependency'  => array( 'template_style', '==', 'x' ),
						  ),
							array(
								'id'    	=> 'default_featured_image',
								'type'  	=> 'switcher',
								'title' 	=> esc_html__( 'Placeholder?', 'codevz' ),
								'help' 		=> esc_html__( 'Will show placeholder image for posts without featured image', 'codevz' ),
								'dependency'  => array( 'template_style', '!=', 'x' )
							),
							array(
								'id'    	=> '2x_height_image',
								'type'  	=> 'switcher',
								'title' 	=> esc_html__( '2x height image?', 'codevz' ),
								'dependency'  => array( 'template_style|template_style', '!=|!=', 'x|3' )
							),
							array(
								'id'    	=> 'post_excerpt',
								'type'  	=> 'slider',
								'title'   => esc_html__( 'Excerpt lenght', 'codevz' ),
								'help' 	  => esc_html__( '-1 means full content without readmore button', 'codevz' ),
								'options'	=> array( 'unit' => '', 'step' => 1, 'min' => 0, 'max' => 50 ),
								'default' 	=> '20',
								'dependency'  => array( 'template_style|template_style|template_style|template_style', '!=|!=|!=|!=', 'x|12|13|14' )
							),
							array(
								'id' 		=> 'hover_icon_post',
								'type' 		=> 'select',
								'title' 	=> esc_html__( 'Hover icon', 'codevz' ),
								'help' 		=> esc_html__( 'Post image is required for display icon', 'codevz' ),
								'options' 	=> array(
									'' 			=> esc_html__( 'Icon on hover', 'codevz' ),
									'ihoh' 		=> esc_html__( 'Icon hide on hover', 'codevz' ),
									'asi' 		=> esc_html__( 'Icon visible', 'codevz' ),
									'image' 	=> esc_html__( 'Image on hover', 'codevz' ),
									'imhoh' 	=> esc_html__( 'Image hide on hover', 'codevz' ),
									'iasi' 		=> esc_html__( 'Image visible', 'codevz' ),
									'none' 		=> esc_html__( 'None', 'codevz' ),
								),
								'attributes' => array(
									'data-depend-id' => 'hover_icon_post'
								)
							),
							array(
								'id'          => 'hover_icon_icon_post',
								'type'        => 'icon',
								'title'       => esc_html__('Hover icon', 'codevz'),
								'default'	  => 'fa czico-109-link-symbol-1',
								'dependency'  	=> array( 'hover_icon_post', 'any', ',ihoh,asi' )
							),
							array(
								'id' 			=> 'hover_icon_image_post',
								'type' 			=> 'upload',
								'title' 		=> esc_html__( 'Hover image', 'codevz' ),
								'help' 			=> esc_html__( 'Upload small image', 'codevz' ),
								'preview'       => 1,
								'dependency'  	=> array( 'hover_icon_post', 'any', 'image,imhoh,iasi' )
							),
							array(
								'id'          => 'readmore',
								'type'        => 'text',
								'title'       => esc_html__( 'Read more button', 'codevz' ),
								'default'	    => 'Read More',
								'setting_args' => array( 'transport' => 'postMessage' ),
								'dependency'  => array( 'post_excerpt', '!=', '-1' )
							),
							array(
								'id'          => 'not_found',
								'type'        => 'text',
								'title'       => esc_html__( 'Posts not found message', 'codevz' ),
								'default'	  => 'Not found!',
								'setting_args'=> array( 'transport' => 'postMessage' )
							),
							array(
								'id'          => 'cm_disabled',
								'type'        => 'text',
								'title'       => esc_html__( 'Comments disabled message', 'codevz' ),
								'default'	  => 'Comments are disabled.',
								'setting_args'=> array( 'transport' => 'postMessage' )
							),
						),
					),

					array(
						'name'   => 'blog_styles',
						'title'  => esc_html__( 'Blog Styles', 'codevz' ),
						'fields' => array(
							array(
								'type'    => 'notice',
								'class'   => 'info',
								'content' => esc_html__( 'Styles', 'codevz' ) . self::$sk_advanced
							),
							array(
								'id' 			=> '_css_sticky_post',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Sticky Post', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'background', 'padding', 'border' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop.sticky > div',
							),
							array(
								'id' 			=> '_css_sticky_post_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop.sticky > div',
							),
							array(
								'id' 			=> '_css_sticky_post_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop.sticky > div',
							),
							array(
								'id' 			=> '_css_overall_post',
								'hover_id' 		=> '_css_overall_post_hover',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Posts', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'background', 'padding', 'border' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop > div',
							),
							array(
								'id' 			=> '_css_overall_post_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop > div',
							),
							array(
								'id' 			=> '_css_overall_post_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop > div',
							),
							array(
								'id' 			=> '_css_overall_post_hover',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop:hover > div',
							),
							array(
								'id' 			=> '_css_post_hover_icon',
								'hover_id' 		=> '_css_post_hover_icon_hover',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Hover icon', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'font-size', 'background', 'padding', 'border' ),
								'selector' 		=> '.cz-cpt-post article .cz_post_icon',
							),
							array(
								'id' 			=> '_css_post_hover_icon_hover',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz-cpt-post article:hover .cz_post_icon',
							),
							array(
								'id' 			=> '_css_post_image',
								'hover_id' 		=> '_css_post_image_hover',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Posts image', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'opacity', 'background', 'padding', 'border' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop .cz_post_image, .cz-cpt-post .cz_post_svg',
							),
							array(
								'id' 			=> '_css_post_image_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop .cz_post_image, .cz-cpt-post .cz_post_svg',
							),
							array(
								'id' 			=> '_css_post_image_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop .cz_post_image, .cz-cpt-post .cz_post_svg',
							),
							array(
								'id' 			=> '_css_post_image_hover',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop .cz_post_image:hover,.cz-cpt-post  .cz_post_svg',
							),
							array(
								'id' 			=> '_css_post_title',
								'hover_id' 		=> '_css_post_title_hover',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Posts title', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'background', 'font-size', 'line-height', 'padding', 'border' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop .cz_post_title h3',
							),
							array(
								'id' 			=> '_css_post_title_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop .cz_post_title h3',
							),
							array(
								'id' 			=> '_css_post_title_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop .cz_post_title h3',
							),
							array(
								'id' 			=> '_css_post_title_hover',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop .cz_post_title h3',
							),
							array(
								'id' 			=> '_css_post_meta_overall',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Posts meta', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'float', 'background', 'padding', 'border' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop .cz_post_meta',
							),
							array(
								'id' 			=> '_css_post_meta_overall_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop .cz_post_meta',
							),
							array(
								'id' 			=> '_css_post_meta_overall_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop .cz_post_meta',
							),
							array(
								'id' 			=> '_css_post_avatar',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Posts avatar', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'background', 'padding', 'width', 'height', 'border' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop .cz_post_author_avatar img',
							),
							array(
								'id' 			=> '_css_post_avatar_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop .cz_post_author_avatar img',
							),
							array(
								'id' 			=> '_css_post_avatar_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop .cz_post_author_avatar img',
							),
							array(
								'id' 			=> '_css_post_author',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Posts author', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'font-size', 'font-weight' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop .cz_post_author_name',
							),
							array(
								'id' 			=> '_css_post_author_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop .cz_post_author_name',
							),
							array(
								'id' 			=> '_css_post_author_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop .cz_post_author_name',
							),
							array(
								'id' 			=> '_css_post_date',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Posts date', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'font-size', 'font-style' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop .cz_post_date',
							),
							array(
								'id' 			=> '_css_post_date_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop .cz_post_date',
							),
							array(
								'id' 			=> '_css_post_date_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop .cz_post_date',
							),
							array(
								'id' 			=> '_css_post_excerpt',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Posts excerpt', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'text-align', 'color', 'font-size', 'line-height' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop .cz_post_excerpt',
							),
							array(
								'id' 			=> '_css_post_excerpt_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop .cz_post_excerpt',
							),
							array(
								'id' 			=> '_css_post_excerpt_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz-cpt-post .cz_default_loop .cz_post_excerpt',
							),

							array(
								'type'    => 'notice',
								'class'   => 'info',
								'content' => esc_html__( 'Read more button', 'codevz' )
							),
							array(
								'id' 			=> '_css_readmore',
								'hover_id' 		=> '_css_readmore_hover',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Read more', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'float', 'color', 'background', 'font-size', 'border' ),
								'selector' 		=> '.cz-cpt-post .cz_readmore'
							),
							array(
								'id' 			=> '_css_readmore_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz-cpt-post .cz_readmore',
							),
							array(
								'id' 			=> '_css_readmore_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz-cpt-post .cz_readmore',
							),
							array(
								'id' 			=> '_css_readmore_hover',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz-cpt-post .cz_readmore:hover',
							),
							array(
								'id'          => 'readmore_icon',
								'type'        => 'icon',
								'title'       => esc_html__('Read more icon', 'codevz'),
								'default'	  => 'fa fa-angle-right'
							),
							array(
								'id' 			=> '_css_readmore_i',
								'hover_id' 		=> '_css_readmore_i_hover',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Read more icon', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'font-size' ),
								'selector' 		=> '.cz-cpt-post .cz_readmore i',
							),
							array(
								'id' 			=> '_css_readmore_i_hover',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.cz-cpt-post .cz_readmore:hover i',
							),
							array(
								'type'    => 'notice',
								'class'   => 'info',
								'content' => esc_html__( 'Paginations', 'codevz' )
							),
							array(
								'id' 			=> '_css_pagination_li',
								'hover_id' 		=> '_css_pagination_li_hover',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Pagination', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'background', 'font-size', 'border' ),
								'selector' 		=> '.pagination a, .pagination > b, .pagination span, .page-numbers a, .page-numbers span, .woocommerce nav.woocommerce-pagination ul li a, .woocommerce nav.woocommerce-pagination ul li span'
							),
							array(
								'id' 			=> '_css_pagination_li_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.pagination a, .pagination > b, .pagination span, .page-numbers a, .page-numbers span, .woocommerce nav.woocommerce-pagination ul li a, .woocommerce nav.woocommerce-pagination ul li span'
							),
							array(
								'id' 			=> '_css_pagination_li_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.pagination a, .pagination > b, .pagination span, .page-numbers a, .page-numbers span, .woocommerce nav.woocommerce-pagination ul li a, .woocommerce nav.woocommerce-pagination ul li span'
							),
							array(
								'id' 			=> '_css_pagination_li_hover',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.pagination .current, .pagination > b, .pagination a:hover, .page-numbers .current, .page-numbers a:hover, .pagination .next:hover, .pagination .prev:hover, .woocommerce nav.woocommerce-pagination ul li a:focus, .woocommerce nav.woocommerce-pagination ul li a:hover, .woocommerce nav.woocommerce-pagination ul li span.current'
							),

						),
					),

					array(
						'name'   => 'single_settings',
						'title'  => esc_html__( 'Single Settings', 'codevz' ),
						'fields' => array(
							array(
								'id' 	=> 'meta_data_post',
								'type' 	=> 'checkbox',
								'title' => esc_html__( 'Single posts page', 'codevz' ),
								'options' => array(
									'image'		=> esc_html__( 'Featured image', 'codevz' ),
									'author'	=> esc_html__( 'Author avatar & name', 'codevz' ),
									'date'		=> esc_html__( 'Date', 'codevz' ),
									'mbot'		=> esc_html__( 'Meta below title', 'codevz' ),
									'cats'		=> esc_html__( 'Categories', 'codevz' ),
									'tags'		=> esc_html__( 'Tags', 'codevz' ),
									'author_box'=> esc_html__( 'Author box', 'codevz' ),
									'next_prev' => esc_html__( 'Next prev posts', 'codevz' ),
								),
								'default' => array( 'image','date','author','cats','tags','author_box', 'next_prev' )
							),
							array(
								'id' 			=> 'prev_post',
								'type' 			=> 'text',
								'title' 		=> esc_html__( 'Prev post sur title', 'codevz' ),
								'default' 		=> 'Previous',
								'setting_args' 	=> array('transport' => 'postMessage')
							),
							array(
								'id' 			=> 'next_post',
								'type' 			=> 'text',
								'title' 		=> esc_html__( 'Next post sur title', 'codevz' ),
								'default' 		=> 'Next',
								'setting_args' 	=> array('transport' => 'postMessage')
							),
							array(
								'id'    	=> 'related_post_ppp',
								'type'  	=> 'slider',
								'title' 	=> esc_html__( 'Related posts', 'codevz' ),
								'options'	=> array( 'unit' => '', 'step' => 1, 'min' => -1, 'max' => 100 ),
								'default' 	=> '3'
							),
							array(
								'id'          	=> 'related_posts_post',
								'type'        	=> 'text',
								'title'       	=> esc_html__('Related title', 'codevz'),
								'default'		=> 'Related Posts ...',
								'setting_args' 	=> array('transport' => 'postMessage'),
								'dependency'  	=> array( 'related_post_ppp', '!=', '0' ),
							),
							array(
								'id' 		=> 'related_post_col',
								'type' 		=> 'image_select',
								'title' 	=> esc_html__( 'Related columns', 'codevz' ),
								'options' 	=> array(
									's6' 		=> CDVZ_PLUGIN_URI . 'assets/admin_img/cols-2.png',
									's4' 		=> CDVZ_PLUGIN_URI . 'assets/admin_img/cols-3.png',
									's3' 		=> CDVZ_PLUGIN_URI . 'assets/admin_img/cols-4.png',
								),
								'default' 	=> 's4',
								'dependency'  => array( 'related_post_ppp', '!=', '0' ),
							),
							array(
								'id'    		=> 'no_comment',
								'type'  		=> 'text',
								'title' 		=> esc_html__( 'No comment title', 'codevz' ),
								'default' 		=> 'No comment',
								'setting_args' 	=> array( 'transport' => 'postMessage' )
							),
							array(
								'id'    		=> 'comment',
								'type'  		=> 'text',
								'title' 		=> esc_html__( 'Comment title', 'codevz' ),
								'default' 		=> 'Comment',
								'setting_args' 	=> array( 'transport' => 'postMessage' )
							),
							array(
								'id'    		=> 'comments',
								'type'  		=> 'text',
								'title' 		=> esc_html__( 'Comments title', 'codevz' ),
								'default' 		=> 'Comments',
								'setting_args' 	=> array( 'transport' => 'postMessage' )
							),
						),
					),

					array(
						'name'   => 'single_styles',
						'title'  => esc_html__( 'Single Styles', 'codevz' ),
						'fields' => array(
							array(
								'type'    => 'notice',
								'class'   => 'info',
								'content' => esc_html__( 'Styles', 'codevz' ) . self::$sk_advanced
							),
							array(
								'id' 			=> '_css_single_con',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Content container', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'background', 'padding', 'border' ),
								'selector' 		=> '.single-post .single_con',
							),
							array(
								'id' 			=> '_css_single_con_tablet','type' => 'cz_sk_hidden','setting_args' => array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .single_con',
							),
							array(
								'id' 			=> '_css_single_con_mobile','type' => 'cz_sk_hidden','setting_args' => array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .single_con',
							),
							array(
								'id' 			=> '_css_single_title',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Title', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'font-size', 'line-height' ),
								'selector' 		=> '.single-post h3.section_title',
							),
							array(
								'id' 			=> '_css_single_title_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post h3.section_title',
							),
							array(
								'id' 			=> '_css_single_title_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post h3.section_title',
							),
							array(
								'id' 			=> '_css_single_fi',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Featured image', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'background', 'padding', 'margin', 'border' ),
								'selector' 		=> '.single-post .cz_single_fi img',
							),
							array(
								'id' 			=> '_css_single_fi_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .cz_single_fi img',
							),
							array(
								'id' 			=> '_css_single_fi_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .cz_single_fi img',
							),
							array(
								'id' 			=> '_css_single_post_avatar',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Post author avatar', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'background', 'padding', 'border' ),
								'selector' 		=> '.single-post .cz_post_author_avatar img',
							),
							array(
								'id' 			=> '_css_single_post_avatar_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .cz_post_author_avatar img',
							),
							array(
								'id' 			=> '_css_single_post_avatar_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .cz_post_author_avatar img',
							),
							array(
								'id' 			=> '_css_single_post_author',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Post author', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'font-size' ),
								'selector' 		=> '.single-post .cz_post_author_name',
							),
							array(
								'id' 			=> '_css_single_post_author_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .cz_post_author_name',
							),
							array(
								'id' 			=> '_css_single_post_author_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .cz_post_author_name',
							),
							array(
								'id' 			=> '_css_single_post_date',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Post date', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'font-size' ),
								'selector' 		=> '.single-post .cz_post_date',
							),
							array(
								'id' 			=> '_css_single_post_date_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .cz_post_date',
							),
							array(
								'id' 			=> '_css_single_post_date_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .cz_post_date',
							),
							array(
								'id' 			=> '_css_single_mbot',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Meta', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'background', 'padding', 'border' ),
								'selector' 		=> '.single-post .cz_top_meta_i',
							),
							array(
								'id' 			=> '_css_single_mbot_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .cz_top_meta_i',
							),
							array(
								'id' 			=> '_css_single_mbot_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .cz_top_meta_i',
							),
							array(
								'id' 			=> '_css_single_mbot_i',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Meta title', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color' ),
								'selector' 		=> '.single-post .cz_top_meta_i a, .single-post .cz_top_meta_i .cz_post_date',
							),
							array(
								'id' 			=> '_css_single_mbot_i_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .cz_top_meta_i a, .single-post .cz_top_meta_i .cz_post_date',
							),
							array(
								'id' 			=> '_css_single_mbot_i_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .cz_top_meta_i a, .single-post .cz_top_meta_i .cz_post_date',
							),
							array(
								'id' 			=> '_css_tags_categories',
								'hover_id' 		=> '_css_tags_categories_hover',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Post tags, categories', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'background', 'font-size', 'border' ),
								'selector' 		=> '.tagcloud a, .cz_post_cat a'
							),
							array(
								'id' 			=> '_css_tags_categories_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.tagcloud a, .cz_post_cat a'
							),
							array(
								'id' 			=> '_css_tags_categories_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.tagcloud a, .cz_post_cat a'
							),
							array(
								'id' 			=> '_css_tags_categories_hover',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.tagcloud a:hover, .cz_post_cat a:hover'
							),
							array(
								'id' 			=> '_css_tags_categories_icon',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Icon of tags, categories', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'background', 'font-size', 'border' ),
								'selector' 		=> '.single .cz_is_blank .tagcloud a:first-child, .single .content .tagcloud a:first-child, .single .cz_is_blank .cz_post_cat a:first-child, .single .content .cz_post_cat a:first-child'
							),
							array(
								'id' 			=> '_css_tags_categories_icon_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single .cz_is_blank .tagcloud a:first-child, .single .content .tagcloud a:first-child, .single .cz_is_blank .cz_post_cat a:first-child, .single .content .cz_post_cat a:first-child'
							),
							array(
								'id' 			=> '_css_tags_categories_icon_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single .cz_is_blank .tagcloud a:first-child, .single .content .tagcloud a:first-child, .single .cz_is_blank .cz_post_cat a:first-child, .single .content .cz_post_cat a:first-child'
							),
							array(
								'id' 			=> '_css_next_prev_con',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Next/Prev posts container', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'background', 'padding', 'border' ),
								'selector' 		=> '.single-post .next_prev'
							),
							array(
								'id' 			=> '_css_next_prev_con_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .next_prev'
							),
							array(
								'id' 			=> '_css_next_prev_con_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .next_prev'
							),
							array(
								'id' 			=> '_css_next_prev_icons',
								'hover_id' 		=> '_css_next_prev_icons_hover',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Next/Prev posts icons', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'background', 'font-size', 'padding', 'border' ),
								'selector' 		=> '.single-post .next_prev i'
							),
							array(
								'id' 			=> '_css_next_prev_icons_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .next_prev i'
							),
							array(
								'id' 			=> '_css_next_prev_icons_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .next_prev i'
							),
							array(
								'id' 			=> '_css_next_prev_icons_hover',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .next_prev li:hover i'
							),
							array(
								'id' 			=> '_css_next_prev_titles',
								'hover_id' 		=> '_css_next_prev_titles_hover',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Next/Prev post titles', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'font-size', 'line-height' ),
								'selector' 		=> '.single-post .next_prev h4'
							),
							array(
								'id' 			=> '_css_next_prev_titles_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .next_prev h4'
							),
							array(
								'id' 			=> '_css_next_prev_titles_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .next_prev h4'
							),
							array(
								'id' 			=> '_css_next_prev_titles_hover',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .next_prev li:hover h4'
							),
							array(
								'id' 			=> '_css_next_prev_surtitle',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Next/Prev sur title', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'background', 'font-size', 'padding', 'border' ),
								'selector' 		=> '.single-post .next_prev h4 small'
							),
							array(
								'id' 			=> '_css_next_prev_surtitle_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .next_prev h4 small'
							),
							array(
								'id' 			=> '_css_next_prev_surtitle_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .next_prev h4 small'
							),

							array(
								'type'    => 'notice',
								'class'   => 'info',
								'content' => esc_html__( 'Single More', 'codevz' )
							),
							array(
								'id' 			=> '_css_related_posts_con',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Related posts container', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'background', 'padding', 'border' ),
								'selector' 		=> '.single-post .cz_related_posts'
							),
							array(
								'id' 			=> '_css_related_posts_con_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .cz_related_posts'
							),
							array(
								'id' 			=> '_css_related_posts_con_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .cz_related_posts'
							),
							array(
								'id' 			=> '_css_related_posts_sec_title',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Related section title', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'background', 'font-size', 'padding', 'border' ),
								'selector' 		=> '.single-post .cz_related_posts > h4'
							),
							array(
								'id' 			=> '_css_related_posts_sec_title_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .cz_related_posts > h4'
							),
							array(
								'id' 			=> '_css_related_posts_sec_title_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .cz_related_posts > h4'
							),
							array(
								'id' 			=> '_css_related_posts',
								'hover_id' 		=> '_css_related_posts_hover',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Related posts', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'background', 'padding', 'border' ),
								'selector' 		=> '.single-post .cz_related_post > div'
							),
							array(
								'id' => '_css_related_posts_tablet',
								'type' => 'cz_sk_hidden',
								'setting_args' => array( 'transport' => 'postMessage' ),
								'selector' => '.single-post .cz_related_post > div'
							),
							array(
								'id' => '_css_related_posts_mobile',
								'type' => 'cz_sk_hidden',
								'setting_args' => array( 'transport' => 'postMessage' ),
								'selector' => '.single-post .cz_related_post > div'
							),
							array(
								'id' 			=> '_css_related_posts_hover',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .cz_related_post:hover > div'
							),
							array(
								'id'      	=> '_css_related_posts_img',
								'hover_id' 	=> '_css_related_posts_img_hover',
								'type'      => 'cz_sk',
								'button'    => esc_html__( 'Related posts image', 'codevz' ),
								'setting_args'  => array( 'transport' => 'postMessage' ),
								'settings'    => array( 'background', 'padding', 'border' ),
								'selector'    => '.single-post .cz_related_post > div img'
							),
							array(
								'id' => '_css_related_posts_img_tablet',
								'type' => 'cz_sk_hidden',
								'setting_args' => array( 'transport' => 'postMessage' ),
								'selector'    => '.single-post .cz_related_post > div img'
							),
							array(
								'id' => '_css_related_posts_img_mobile',
								'type' => 'cz_sk_hidden',
								'setting_args' => array( 'transport' => 'postMessage' ),
								'selector'    => '.single-post .cz_related_post > div img'
							),
							array(
								'id' => '_css_related_posts_img_hover',
								'type' => 'cz_sk_hidden',
								'setting_args' => array( 'transport' => 'postMessage' ),
								'selector'    => '.single-post .cz_related_post:hover > div img'
							),
							array(
								'id' 			=> '_css_related_posts_title',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Related posts title', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'font-size', 'line-height' ),
								'selector' 		=> '.single-post .cz_related_post h3'
							),
							array(
								'id' 			=> '_css_related_posts_title_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .cz_related_post h3'
							),
							array(
								'id' 			=> '_css_related_posts_title_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .cz_related_post h3'
							),
							array(
								'id' 			=> '_css_related_posts_meta',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Related posts meta', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'font-size' ),
								'selector' 		=> '.single-post .cz_related_post_date'
							),
							array(
								'id' 			=> '_css_related_posts_meta_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .cz_related_post_date'
							),
							array(
								'id' 			=> '_css_related_posts_meta_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .cz_related_post_date'
							),
							array(
								'id' 			=> '_css_related_posts_meta_links',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Related meta links', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'font-size' ),
								'selector' 		=> '.single-post .cz_related_post_date a'
							),
							array(
								'id' 			=> '_css_related_posts_meta_links_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .cz_related_post_date a'
							),
							array(
								'id' 			=> '_css_related_posts_meta_links_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .cz_related_post_date a'
							),
							array(
								'id' 			=> '_css_single_comments_title',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Comments title', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'color', 'font-size', 'border' ),
								'selector' 		=> '.single-post #comments > h3'
							),
							array(
								'id' 			=> '_css_single_comments_title_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post #comments > h3'
							),
							array(
								'id' 			=> '_css_single_comments_title_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post #comments > h3'
							),
							array(
								'id' 			=> '_css_single_comments_li',
								'type' 			=> 'cz_sk',
								'button' 		=> esc_html__( 'Comments', 'codevz' ),
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'settings' 		=> array( 'background', 'padding', 'border' ),
								'selector' 		=> '.single-post .commentlist li article'
							),
							array(
								'id' 			=> '_css_single_comments_li_tablet',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .commentlist li article'
							),
							array(
								'id' 			=> '_css_single_comments_li_mobile',
								'type' 			=> 'cz_sk_hidden',
								'setting_args' 	=> array( 'transport' => 'postMessage' ),
								'selector' 		=> '.single-post .commentlist li article'
							),
						),
					),

				  array(
					'name'   => 'search_settings',
					'title'  => esc_html__( 'Search Page', 'codevz' ),
					'fields' => array(
						array(
							'id'      => 'search_title_prefix',
							'type'    => 'text',
							'title'   => esc_html__( 'Search title prefix', 'codevz' ),
							'default' => 'Search result for:',
						),
						array(
							'id' 		=> 'search_cpt',
							'type' 		=> 'text',
							'title'		=> esc_html__( 'Search post type(s)', 'codevz' ),
							'help'		=> 'e.g. post,portfolio,product'
						),
						array(
							'id'  => 'layout_search',
							'type'  => 'image_select',
							'title' 		=> esc_html__( 'Sidebar position', 'codevz' ),
							'desc'  		=> esc_html__( 'The default is from General > Layout', 'codevz' ),
							'options'       => array(
								'1'           	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-0.png',
								'ws' 			=> CDVZ_PLUGIN_URI . 'assets/admin_img/off.png',
								'bpnp'      	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-2.png',
								'center' 		=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-13.png',
								'right'       	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-3.png',
								'right-s'     	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-4.png',
								'left'        	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-5.png',
								'left-s'      	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-6.png',
								'both-side' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-7.png',
								'both-side2' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-8.png',
								'both-right'	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-9.png',
								'both-right2'	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-10.png',
								'both-left' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-11.png',
								'both-left2' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-12.png',
							),
							'default' => 'right'
						),
					),
				  ),

				),
			);

			// Generate options for each post types
			foreach ( self::post_types() as $cpt ) {
				if ( empty( $cpt ) ) {
					continue;
				}
				$name = get_post_type_object( $cpt );
				$name = isset( $name->label ) ? $name->label : ucwords( str_replace( '_', ' ', $cpt ) );
				$options[] = array(
					'name'   	=> 'post_type_' . $cpt,
					'title'  	=> $name,
					'sections' 	=> array(
						array(
							'name'   => $cpt . '_slug',
							'title'  => esc_html__( 'Slug and Title', 'codevz' ),
							'fields' => array(
								array(
									'type'    => 'notice',
									'class'   => 'info',
									'content' => esc_html__( 'Warning: If you change post type slug(s), you must save option and via Dashboard > Settings > Permalinks save your permalinks.', 'codevz' )
								),
								array(
									'id' 	=> 'slug_' . $cpt,
									'type' 	=> 'text',
									'title' => esc_html__( 'Slug', 'codevz' ),
									'attributes' => array( 'placeholder'	=> $cpt ),
									'setting_args' => array('transport' => 'postMessage')
								),
								array(
									'id' 	=> 'title_' . $cpt,
									'type' 	=> 'text',
									'title' => esc_html__( 'Archive title', 'codevz' ),
									'attributes' => array( 'placeholder'	=> $name ),
									'setting_args' => array('transport' => 'postMessage')
								),
								array(
									'id' 	=> 'cat_' . $cpt,
									'type' 	=> 'text',
									'title' => esc_html__( 'Category slug', 'codevz' ),
									'attributes' => array( 'placeholder'	=> $cpt . '/cat' ),
									'setting_args' => array('transport' => 'postMessage')
								),
								array(
									'id' 	=> 'cat_title_' . $cpt,
									'type' 	=> 'text',
									'title' => esc_html__( 'Category title', 'codevz' ),
									'attributes' => array( 'placeholder'	=> 'Categories' ),
									'setting_args' => array('transport' => 'postMessage')
								),
								array(
									'id' 	=> 'tags_' . $cpt,
									'type' 	=> 'text',
									'title' => esc_html__( 'Tags slug', 'codevz' ),
									'attributes' => array( 'placeholder'	=> $cpt . '/tags' ),
									'setting_args' => array('transport' => 'postMessage')
								),
								array(
									'id' 	=> 'tags_title_' . $cpt,
									'type' 	=> 'text',
									'title' => esc_html__( 'Tags title', 'codevz' ),
									'attributes' => array( 'placeholder'	=> 'Tags' ),
									'setting_args' => array('transport' => 'postMessage')
								),
							)
						),

						array(
							'name'   => $cpt . '_settings',
							'title'  => $name . ' ' . esc_html__( 'Settings', 'codevz' ),
							'fields' => wp_parse_args( 
								self::title_options( '_' . $cpt, '.cz-cpt-' . $cpt . ' ' ),
								array(
									array(
										'id' 	=> 'layout_' . $cpt,
										'type' 	=> 'image_select',
										'title' => esc_html__( 'Sidebar position', 'codevz' ),
										'desc'  => esc_html__( 'The default is from General > Layout', 'codevz' ),
										'help'  => $name . ' ' . esc_html__( 'archive and posts', 'codevz' ),
										'options' 	=> array(
											'1'           	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-0.png',
											'ws' 			=> CDVZ_PLUGIN_URI . 'assets/admin_img/off.png',
											'bpnp'      	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-2.png',
											'center' 		=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-13.png',
											'right'       	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-3.png',
											'right-s'     	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-4.png',
											'left'        	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-5.png',
											'left-s'      	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-6.png',
											'both-side' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-7.png',
											'both-side2' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-8.png',
											'both-right'	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-9.png',
											'both-right2'	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-10.png',
											'both-left' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-11.png',
											'both-left2' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-12.png',
										),
										'default' => '1'
									),
									array(
										'id' 		=> 'template_style_' . $cpt,
										'type' 		=> 'image_select',
										'title' 	=> esc_html__( 'Template', 'codevz' ),
										'help'  	=> $name . ' ' . esc_html__( 'archive page, category page, tags page, etc.', 'codevz' ),
										'options' 	=> array(
											'1'			=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-1.png',
											'2'			=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-2.png',
											'6'			=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-1-2.png',
											'3'			=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-3.png',
											'4'			=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-4.png',
											'5'			=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-5.png',
											'7'			=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-7.png',
											'8'			=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-8.png',
											'9'			=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-9.png',
											'10'		=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-10.png',
											'11'		=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-11.png',
											'12'		=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-12.png',
											'13'		=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-13.png',
											'14'		=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-14.png',
						  					'x' 		=> CDVZ_PLUGIN_URI . 'assets/admin_img/posts-x.png',
										),
										'attributes' => array(
											'data-depend-id' => 'template_style_' . $cpt
										),
										'default' => '10'
									),
									array(
										'id'    => 'template_' . $cpt,
										'type'    => 'select',
										'title'   => esc_html__( 'Custom page', 'codevz' ),
										'options'   => Codevz_Plus::$array_pages,
										'default_option'=> esc_html__( 'Select', 'codevz' ),
										'dependency'  => array( 'template_style_' . $cpt, '==', 'x' ),
									),
									array(
										'id'    	=> '2x_height_image_' . $cpt,
										'type'  	=> 'switcher',
										'title' 	=> esc_html__( '2x height image?', 'codevz' ),
										'dependency'  => array( 'template_style_' . $cpt . '|template_style_' . $cpt, '!=|!=', 'x|3' )
									),
									array(
										'id'    	=> 'posts_per_page_' . $cpt,
										'type'  	=> 'slider',
										'title' 	=> esc_html__( 'Posts per page', 'codevz' ),
										'options'	=> array( 'unit' => '', 'step' => 1, 'min' => -1, 'max' => 100 ),
										'dependency'  => array( 'template_style_' . $cpt, '!=', 'x' )
									),
									array(
										'id'    	=> 'post_excerpt_' . $cpt,
										'type'  	=> 'slider',
										'title'   => esc_html__( 'Excerpt lenght', 'codevz' ),
										'help' 	  => esc_html__( '-1 means full content without readmore button', 'codevz' ),
										'options'	=> array( 'unit' => '', 'step' => 1, 'min' => 0, 'max' => 50 ),
										'default' 	=> '20',
										'dependency'  => array( 'template_style_' . $cpt . '|template_style_' . $cpt . '|template_style_' . $cpt . '|template_style_' . $cpt, '!=|!=|!=|!=', 'x|12|13|14' )
									),
									array(
										'id' 		=> 'hover_icon_' . $cpt,
										'type' 		=> 'select',
										'title' 	=> esc_html__( 'Hover icon', 'codevz' ),
										'options' 	=> array(
											'' 			=> esc_html__( 'Icon on hover', 'codevz' ),
											'ihoh' 		=> esc_html__( 'Icon hide on hover', 'codevz' ),
											'asi' 		=> esc_html__( 'Icon visible', 'codevz' ),
											'image' 	=> esc_html__( 'Image on hover', 'codevz' ),
											'imhoh' 	=> esc_html__( 'Image hide on hover', 'codevz' ),
											'iasi' 		=> esc_html__( 'Image visible', 'codevz' ),
											'none' 		=> esc_html__( 'None', 'codevz' ),
										),
										'attributes' => array(
											'data-depend-id' => 'hover_icon_' . $cpt
										)
									),
									array(
										'id'          => 'hover_icon_icon_' . $cpt,
										'type'        => 'icon',
										'title'       => esc_html__('Hover icon', 'codevz'),
										'default'	  => 'fa czico-109-link-symbol-1',
										'dependency'  => array( 'hover_icon_' . $cpt, 'any', ',ihoh,asi' )
									),
									array(
										'id' 			=> 'hover_icon_image_' . $cpt,
										'type' 			=> 'upload',
										'title' 		=> esc_html__( 'Hover image', 'codevz' ),
										'help' 			=> esc_html__( 'Upload small image', 'codevz' ),
										'preview'       => 1,
										'dependency'  => array( 'hover_icon_' . $cpt, '==', 'image' ),
										'dependency'  => array( 'hover_icon_' . $cpt, 'any', 'imhoh,image,iasi' )
									),
									array(
										'id'          => 'readmore_' . $cpt,
										'type'        => 'text',
										'title'       => esc_html__( 'Read more button', 'codevz' ),
										'default'	    => 'Read More',
										'setting_args' => array( 'transport' => 'postMessage' ),
										'dependency'  => array( 'post_excerpt_' . $cpt, '!=', '-1' )
									),
								)
							)
						),

						array(
							'name'   => $cpt . '_styles',
							'title'  => $name . ' ' . esc_html__( 'Styles', 'codevz' ),
							'fields' => array(
								array(
									'type'    => 'notice',
									'class'   => 'info',
									'content' => esc_html__( 'Styles', 'codevz' ) . self::$sk_advanced
								),
								array(
									'id' 			=> '_css_sticky_' . $cpt,
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Sticky Post', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'background', 'padding', 'border' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop.sticky > div',
								),
								array(
									'id' 			=> '_css_sticky_' . $cpt . '_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop.sticky > div',
								),
								array(
									'id' 			=> '_css_sticky_' . $cpt . '_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop.sticky > div',
								),
								array(
									'id' 			=> '_css_overall_' . $cpt . '',
									'hover_id' 		=> '_css_overall_' . $cpt . '_hover',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Posts', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'background', 'padding', 'border' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop > div',
								),
								array(
									'id' 			=> '_css_overall_' . $cpt . '_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop > div',
								),
								array(
									'id' 			=> '_css_overall_' . $cpt . '_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop > div',
								),
								array(
									'id' 			=> '_css_overall_' . $cpt . '_hover',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop:hover > div',
								),
								array(
									'id' 			=> '_css_' . $cpt . '_hover_icon',
									'hover_id' 		=> '_css_' . $cpt . '_hover_icon_hover',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Hover icon', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'font-size', 'background', 'padding', 'border' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' article .cz_post_icon',
								),
								array(
									'id' 			=> '_css_' . $cpt . '_hover_icon_hover',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' article:hover .cz_post_icon',
								),
								array(
									'id' 			=> '_css_' . $cpt . '_image',
									'hover_id' 		=> '_css_' . $cpt . '_image_hover',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Posts image', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'opacity', 'background', 'padding', 'border' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop .cz_post_image, .cz-cpt-' . $cpt . ' .cz_post_svg',
								),
								array(
									'id' 			=> '_css_' . $cpt . '_image_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop .cz_post_image, .cz-cpt-' . $cpt . ' .cz_post_svg',
								),
								array(
									'id' 			=> '_css_' . $cpt . '_image_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop .cz_post_image, .cz-cpt-' . $cpt . ' .cz_post_svg',
								),
								array(
									'id' 			=> '_css_' . $cpt . '_image_hover',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop .cz_post_image:hover,.cz-cpt-' . $cpt . '  .cz_post_svg',
								),
								array(
									'id' 			=> '_css_' . $cpt . '_title',
									'hover_id' 		=> '_css_' . $cpt . '_title_hover',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Posts title', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'background', 'font-size', 'line-height', 'padding', 'border' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop .cz_post_title h3',
								),
								array(
									'id' 			=> '_css_' . $cpt . '_title_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop .cz_post_title h3',
								),
								array(
									'id' 			=> '_css_' . $cpt . '_title_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop .cz_post_title h3',
								),
								array(
									'id' 			=> '_css_' . $cpt . '_title_hover',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop .cz_post_title h3',
								),
								array(
									'id' 			=> '_css_' . $cpt . '_meta_overall',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Posts meta', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'float', 'background', 'padding', 'border' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop .cz_post_meta',
								),
								array(
									'id' 			=> '_css_' . $cpt . '_meta_overall_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop .cz_post_meta',
								),
								array(
									'id' 			=> '_css_' . $cpt . '_meta_overall_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop .cz_post_meta',
								),
								array(
									'id' 			=> '_css_' . $cpt . '_avatar',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Posts avatar', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'background', 'padding', 'width', 'height', 'border' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop .cz_post_author_avatar img',
								),
								array(
									'id' 			=> '_css_' . $cpt . '_avatar_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop .cz_post_author_avatar img',
								),
								array(
									'id' 			=> '_css_' . $cpt . '_avatar_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop .cz_post_author_avatar img',
								),
								array(
									'id' 			=> '_css_' . $cpt . '_author',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Posts author', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'font-size', 'font-weight' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop .cz_post_author_name',
								),
								array(
									'id' 			=> '_css_' . $cpt . '_author_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop .cz_post_author_name',
								),
								array(
									'id' 			=> '_css_' . $cpt . '_author_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop .cz_post_author_name',
								),
								array(
									'id' 			=> '_css_' . $cpt . '_date',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Posts date', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'font-size', 'font-style' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop .cz_post_date',
								),
								array(
									'id' 			=> '_css_' . $cpt . '_date_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop .cz_post_date',
								),
								array(
									'id' 			=> '_css_' . $cpt . '_date_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop .cz_post_date',
								),
								array(
									'id' 			=> '_css_' . $cpt . '_excerpt',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Posts excerpt', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'text-align', 'color', 'font-size', 'line-height' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop .cz_post_excerpt',
								),
								array(
									'id' 			=> '_css_' . $cpt . '_excerpt_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop .cz_post_excerpt',
								),
								array(
									'id' 			=> '_css_' . $cpt . '_excerpt_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_default_loop .cz_post_excerpt',
								),

								array(
									'type'    => 'notice',
									'class'   => 'info',
									'content' => esc_html__( 'Read more button', 'codevz' )
								),
								array(
									'id' 			=> '_css_readmore_' . $cpt,
									'hover_id' 		=> '_css_readmore_' . $cpt . '_hover',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Read more', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'float', 'color', 'background', 'font-size', 'border' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_readmore'
								),
								array(
									'id' 			=> '_css_readmore_' . $cpt . '_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_readmore',
								),
								array(
									'id' 			=> '_css_readmore_' . $cpt . '_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_readmore',
								),
								array(
									'id' 			=> '_css_readmore_' . $cpt . '_hover',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_readmore:hover',
								),
								array(
									'id'          => 'readmore_icon_' . $cpt,
									'type'        => 'icon',
									'title'       => esc_html__('Read more icon', 'codevz'),
									'default'	  => 'fa fa-angle-right'
								),
								array(
									'id' 			=> '_css_readmore_i_' . $cpt,
									'hover_id' 		=> '_css_readmore_i_' . $cpt . '_hover',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Read more icon', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'font-size' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_readmore i',
								),
								array(
									'id' 			=> '_css_readmore_i_' . $cpt . '_hover',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.cz-cpt-' . $cpt . ' .cz_readmore:hover i',
								),

							),
						),

						array(
							'name'   => $cpt . '_single_settings',
							'title'  => esc_html__( 'Single Settings', 'codevz' ),
							'fields' => array(
								array(
									'id' 	=> 'meta_data_' . $cpt,
									'type' 	=> 'checkbox',
									'title' => esc_html__( 'Single posts page', 'codevz' ),
									'options' => array(
										'image'		=> esc_html__( 'Featured image', 'codevz' ),
										'author'	=> esc_html__( 'Author avatar & name', 'codevz' ),
										'date'		=> esc_html__( 'Date', 'codevz' ),
										'mbot'		=> esc_html__( 'Meta below title', 'codevz' ),
										'cats'		=> esc_html__( 'Categories', 'codevz' ),
										'tags'		=> esc_html__( 'Tags', 'codevz' ),
										'author_box'=> esc_html__( 'Author box', 'codevz' ),
										'next_prev' => esc_html__( 'Next prev posts', 'codevz' ),
									),
									'default' => array( 'image','date','author','cats','tags','author_box', 'next_prev' )
								),
								array(
									'id' 			=> 'prev_' . $cpt,
									'type' 			=> 'text',
									'title' 		=> esc_html__( 'Prev post sur title', 'codevz' ),
									'default' 		=> 'Previous',
									'setting_args' 	=> array('transport' => 'postMessage')
								),
								array(
									'id' 			=> 'next_' . $cpt,
									'type' 			=> 'text',
									'title' 		=> esc_html__( 'Next post sur title', 'codevz' ),
									'default' 		=> 'Next',
									'setting_args' 	=> array('transport' => 'postMessage')
								),
								array(
									'id'    		=> 'related_' . $cpt . '_ppp',
									'type'  		=> 'slider',
									'title' 		=> esc_html__( 'Related posts', 'codevz' ),
									'options'		=> array( 'unit' => '', 'step' => 1, 'min' => -1, 'max' => 100 ),
									'default' 		=> '3'
								),
								array(
									'id'          	=> 'related_posts_' . $cpt,
									'type'        	=> 'text',
									'title'       	=> esc_html__('Related title', 'codevz'),
									'default'		=> 'You may also like ...',
									'setting_args' 	=> array('transport' => 'postMessage'),
									'dependency'  	=> array( 'related_' . $cpt . '_ppp', '!=', '0' ),
								),
								array(
									'id' 		=> 'related_' . $cpt . '_col',
									'type' 		=> 'image_select',
									'title' 	=> esc_html__( 'Related columns', 'codevz' ),
									'options' 	=> array(
										's6' 		=> CDVZ_PLUGIN_URI . 'assets/admin_img/cols-2.png',
										's4' 		=> CDVZ_PLUGIN_URI . 'assets/admin_img/cols-3.png',
										's3' 		=> CDVZ_PLUGIN_URI . 'assets/admin_img/cols-4.png',
									),
									'default' 	=> 's4',
									'dependency'  => array( 'related_' . $cpt . '_ppp', '!=', '0' ),
								),
								array(
									'id'    		=> 'no_comment_' . $cpt,
									'type'  		=> 'text',
									'title' 		=> esc_html__( 'No comment title', 'codevz' ),
									'default' 		=> 'No comment',
									'setting_args' 	=> array( 'transport' => 'postMessage' )
								),
								array(
									'id'    		=> 'comment_' . $cpt,
									'type'  		=> 'text',
									'title' 		=> esc_html__( 'Comment title', 'codevz' ),
									'default' 		=> 'Comment',
									'setting_args' 	=> array( 'transport' => 'postMessage' )
								),
								array(
									'id'    		=> 'comments_' . $cpt,
									'type'  		=> 'text',
									'title' 		=> esc_html__( 'Comments title', 'codevz' ),
									'default' 		=> 'Comments',
									'setting_args' 	=> array( 'transport' => 'postMessage' )
								),
							),
						),

						array(
							'name'   => $cpt . '_single_styles',
							'title'  => esc_html__( 'Single Styles', 'codevz' ),
							'fields' => array(
								array(
									'type'    => 'notice',
									'class'   => 'info',
									'content' => esc_html__( 'Styles', 'codevz' ) . self::$sk_advanced
								),
								array(
									'id' 			=> '_css_single_con_' . $cpt,
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Content container', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'background', 'padding', 'border' ),
									'selector' 		=> '.single-' . $cpt . ' .single_con',
								),
								array(
									'id' 			=> '_css_single_con_' . $cpt . '_tablet','type' => 'cz_sk_hidden','setting_args' => array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .single_con',
								),
								array(
									'id' 			=> '_css_single_con_' . $cpt . '_mobile','type' => 'cz_sk_hidden','setting_args' => array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .single_con',
								),
								array(
									'id' 			=> '_css_single_title_' . $cpt,
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Title', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'font-size', 'line-height' ),
									'selector' 		=> '.single-' . $cpt . ' h3.section_title',
								),
								array(
									'id' 			=> '_css_single_title_' . $cpt . '_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' h3.section_title',
								),
								array(
									'id' 			=> '_css_single_title_' . $cpt . '_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' h3.section_title',
								),
								array(
									'id' 			=> '_css_single_fi_' . $cpt,
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Featured image', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'background', 'padding', 'margin', 'border' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_single_fi img',
								),
								array(
									'id' 			=> '_css_single_fi_' . $cpt . '_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_single_fi img',
								),
								array(
									'id' 			=> '_css_single_fi_' . $cpt . '_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_single_fi img',
								),
								array(
									'id' 			=> '_css_single_avatar_' . $cpt,
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Post author avatar', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'background', 'padding', 'border' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_post_author_avatar img',
								),
								array(
									'id' 			=> '_css_single_' . $cpt . '_avatar_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_post_author_avatar img',
								),
								array(
									'id' 			=> '_css_single_' . $cpt . '_avatar_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_post_author_avatar img',
								),
								array(
									'id' 			=> '_css_single_' . $cpt . '_author',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Post author', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'font-size' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_post_author_name',
								),
								array(
									'id' 			=> '_css_single_' . $cpt . '_author_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_post_author_name',
								),
								array(
									'id' 			=> '_css_single_' . $cpt . '_author_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_post_author_name',
								),
								array(
									'id' 			=> '_css_single_' . $cpt . '_date',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Post date', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'font-size' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_post_date',
								),
								array(
									'id' 			=> '_css_single_' . $cpt . '_date_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_post_date',
								),
								array(
									'id' 			=> '_css_single_' . $cpt . '_date_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_post_date',
								),
								array(
									'id' 			=> '_css_single_' . $cpt . '_mbot',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Meta', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'background', 'padding', 'border' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_top_meta_i',
								),
								array(
									'id' 			=> '_css_single_' . $cpt . '_mbot_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_top_meta_i',
								),
								array(
									'id' 			=> '_css_single_' . $cpt . '_mbot_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_top_meta_i',
								),
								array(
									'id' 			=> '_css_single_' . $cpt . '_mbot_i',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Meta title', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_top_meta_i a, .single-' . $cpt . ' .cz_top_meta_i .cz_post_date',
								),
								array(
									'id' 			=> '_css_single_' . $cpt . '_mbot_i_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_top_meta_i a, .single-' . $cpt . ' .cz_top_meta_i .cz_post_date',
								),
								array(
									'id' 			=> '_css_single_' . $cpt . '_mbot_i_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_top_meta_i a, .single-' . $cpt . ' .cz_top_meta_i .cz_post_date',
								),
								array(
									'id' 			=> '_css_next_prev_' . $cpt . '_con',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Next/Prev posts container', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'background', 'padding', 'border' ),
									'selector' 		=> '.single-' . $cpt . ' .next_prev'
								),
								array(
									'id' 			=> '_css_next_prev_' . $cpt . '_con_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .next_prev'
								),
								array(
									'id' 			=> '_css_next_prev_' . $cpt . '_con_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .next_prev'
								),
								array(
									'id' 			=> '_css_next_prev_' . $cpt . '_icons',
									'hover_id' 		=> '_css_next_prev_' . $cpt . '_icons_hover',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Next/Prev posts icons', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'background', 'font-size', 'padding', 'border' ),
									'selector' 		=> '.single-' . $cpt . ' .next_prev i'
								),
								array(
									'id' 			=> '_css_next_prev_' . $cpt . '_icons_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .next_prev i'
								),
								array(
									'id' 			=> '_css_next_prev_' . $cpt . '_icons_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .next_prev i'
								),
								array(
									'id' 			=> '_css_next_prev_' . $cpt . '_icons_hover',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .next_prev li:hover i'
								),
								array(
									'id' 			=> '_css_next_prev_' . $cpt . '_titles',
									'hover_id' 		=> '_css_next_prev_' . $cpt . '_titles_hover',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Next/Prev post titles', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'font-size', 'line-height' ),
									'selector' 		=> '.single-' . $cpt . ' .next_prev h4'
								),
								array(
									'id' 			=> '_css_next_prev_' . $cpt . '_titles_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .next_prev h4'
								),
								array(
									'id' 			=> '_css_next_prev_' . $cpt . '_titles_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .next_prev h4'
								),
								array(
									'id' 			=> '_css_next_prev_' . $cpt . '_titles_hover',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .next_prev li:hover h4'
								),
								array(
									'id' 			=> '_css_next_prev_' . $cpt . '_surtitle',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Next/Prev sur title', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'background', 'font-size', 'padding', 'border' ),
									'selector' 		=> '.single-' . $cpt . ' .next_prev h4 small'
								),
								array(
									'id' 			=> '_css_next_prev_' . $cpt . '_surtitle_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .next_prev h4 small'
								),
								array(
									'id' 			=> '_css_next_prev_' . $cpt . '_surtitle_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .next_prev h4 small'
								),

								array(
									'type'    => 'notice',
									'class'   => 'info',
									'content' => esc_html__( 'Single More', 'codevz' )
								),
								array(
									'id' 			=> '_css_related_posts_' . $cpt . '_con',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Related posts container', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'background', 'padding', 'border' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_related_posts'
								),
								array(
									'id' 			=> '_css_related_posts_' . $cpt . '_con_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_related_posts'
								),
								array(
									'id' 			=> '_css_related_posts_' . $cpt . '_con_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_related_posts'
								),
								array(
									'id' 			=> '_css_related_posts_' . $cpt . '_sec_title',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Related section title', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'background', 'font-size', 'padding', 'border' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_related_posts > h4'
								),
								array(
									'id' 			=> '_css_related_posts_' . $cpt . '_sec_title_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_related_posts > h4'
								),
								array(
									'id' 			=> '_css_related_posts_' . $cpt . '_sec_title_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_related_posts > h4'
								),
								array(
									'id' 			=> '_css_related_posts_' . $cpt,
									'hover_id' 		=> '_css_related_posts_' . $cpt . '_hover',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Related posts', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'background', 'padding', 'border' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_related_post > div'
								),
								array(
									'id' => '_css_related_posts_' . $cpt . '_tablet',
									'type' => 'cz_sk_hidden',
									'setting_args' => array( 'transport' => 'postMessage' ),
									'selector' => '.single-' . $cpt . ' .cz_related_post > div'
								),
								array(
									'id' => '_css_related_posts_' . $cpt . '_mobile',
									'type' => 'cz_sk_hidden',
									'setting_args' => array( 'transport' => 'postMessage' ),
									'selector' => '.single-' . $cpt . ' .cz_related_post > div'
								),
								array(
									'id' 			=> '_css_related_posts_' . $cpt . '_hover',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_related_post:hover > div'
								),
								array(
									'id'      	=> '_css_related_posts_' . $cpt . '_img',
									'hover_id' 	=> '_css_related_posts_img_' . $cpt . '_hover',
									'type'      => 'cz_sk',
									'button'    => esc_html__( 'Related images', 'codevz' ),
									'setting_args'  => array( 'transport' => 'postMessage' ),
									'settings'    => array( 'background', 'padding', 'border' ),
									'selector'    => '.single-' . $cpt . ' .cz_related_post > div img'
								),
								array(
									'id' => '_css_related_posts_img_' . $cpt . '_hover',
									'type' => 'cz_sk_hidden',
									'setting_args' => array( 'transport' => 'postMessage' ),
									'selector'    => '.single-' . $cpt . ' .cz_related_post:hover > div img'
								),
								array(
									'id' => '_css_related_posts_' . $cpt . '_img_tablet',
									'type' => 'cz_sk_hidden',
									'setting_args' => array( 'transport' => 'postMessage' ),
									'selector'    => '.single-' . $cpt . ' .cz_related_post > div img'
								),
								array(
									'id' => '_css_related_posts_' . $cpt . '_img_mobile',
									'type' => 'cz_sk_hidden',
									'setting_args' => array( 'transport' => 'postMessage' ),
									'selector'    => '.single-' . $cpt . ' .cz_related_post > div img'
								),
								array(
									'id' 			=> '_css_related_posts_' . $cpt . '_title',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Related posts title', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'font-size', 'line-height' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_related_post h3'
								),
								array(
									'id' 			=> '_css_related_posts_' . $cpt . '_title_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_related_post h3'
								),
								array(
									'id' 			=> '_css_related_posts_' . $cpt . '_title_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_related_post h3'
								),
								array(
									'id' 			=> '_css_related_posts_' . $cpt . '_meta',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Related posts meta', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'font-size' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_related_post_date'
								),
								array(
									'id' 			=> '_css_related_posts_' . $cpt . '_meta_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_related_post_date'
								),
								array(
									'id' 			=> '_css_related_posts_' . $cpt . '_meta_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_related_post_date'
								),
								array(
									'id' 			=> '_css_related_posts_' . $cpt . '_meta_links',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Related meta links', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'font-size' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_related_post_date a'
								),
								array(
									'id' 			=> '_css_related_posts_' . $cpt . '_meta_links_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_related_post_date a'
								),
								array(
									'id' 			=> '_css_related_posts_' . $cpt . '_meta_links_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .cz_related_post_date a'
								),
								array(
									'id' 			=> '_css_single_comments_' . $cpt . '_title',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Comments title', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'font-size', 'border' ),
									'selector' 		=> '.single-' . $cpt . ' #comments > h3'
								),
								array(
									'id' 			=> '_css_single_comments_' . $cpt . '_title_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' #comments > h3'
								),
								array(
									'id' 			=> '_css_single_comments_' . $cpt . '_title_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' #comments > h3'
								),
								array(
									'id' 			=> '_css_single_comments_' . $cpt . '_li',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Comments', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'background', 'padding', 'border' ),
									'selector' 		=> '.single-' . $cpt . ' .commentlist li article'
								),
								array(
									'id' 			=> '_css_single_comments_' . $cpt . '_li_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .commentlist li article'
								),
								array(
									'id' 			=> '_css_single_comments_' . $cpt . '_li_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.single-' . $cpt . ' .commentlist li article'
								),
							),
						),


					)
				);
			}

			// bbpress options
			if ( function_exists( 'is_bbpress' ) ) {
				$options[] = array(
					'name'   => 'post_type_bbpress',
					'title'  => esc_html__( 'BBPress', 'codevz' ),
					'fields' => wp_parse_args( 
						self::title_options( '_bbpress', '.cz-cpt-bbpress ' ),
						array(
							array(
								'id' 	=> 'layout_bbpress',
								'type' 	=> 'image_select',
								'title' => esc_html__( 'Sidebar position', 'codevz' ),
								'desc'  => esc_html__( 'Default is from General > Layout', 'codevz' ),
								'help'  => esc_html__( 'For all bbpress pages', 'codevz' ),
								'options' 	=> array(
									'1'           	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-0.png',
									'ws' 			=> CDVZ_PLUGIN_URI . 'assets/admin_img/off.png',
									'bpnp'      	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-2.png',
									'center' 		=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-13.png',
									'right'       	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-3.png',
									'right-s'     	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-4.png',
									'left'        	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-5.png',
									'left-s'      	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-6.png',
									'both-side' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-7.png',
									'both-side2' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-8.png',
									'both-right'	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-9.png',
									'both-right2'	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-10.png',
									'both-left' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-11.png',
									'both-left2' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-12.png',
								),
								'default' => '1'
							),
						)
					)
				);
			}

			// DWQA options
			if ( function_exists( 'dwqa' ) ) {
				$options[] = array(
					'name'   => 'post_type_dwqa-question',
					'title'  => esc_html__( 'DWQA', 'codevz' ),
					'fields' => wp_parse_args( 
						self::title_options( '_dwqa-question', '.cz-cpt-dwqa-question ' ),
						array(
							array(
								'id' 	=> 'layout_dwqa-question',
								'type' 	=> 'image_select',
								'title' => esc_html__( 'Sidebar position', 'codevz' ),
								'desc'  => esc_html__( 'Default is from General > Layout', 'codevz' ),
								'help'  => esc_html__( 'For all questions pages', 'codevz' ),
								'options' 	=> array(
									'1'           	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-0.png',
									'ws' 			=> CDVZ_PLUGIN_URI . 'assets/admin_img/off.png',
									'bpnp'      	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-2.png',
									'center' 		=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-13.png',
									'right'       	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-3.png',
									'right-s'     	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-4.png',
									'left'        	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-5.png',
									'left-s'      	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-6.png',
									'both-side' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-7.png',
									'both-side2' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-8.png',
									'both-right'	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-9.png',
									'both-right2'	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-10.png',
									'both-left' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-11.png',
									'both-left2' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-12.png',
								),
								'default' => '1'
							),
						)
					)
				);
			}

			// WooCommerce options
			if ( function_exists('is_woocommerce') ) {
				$options[] = array(
					'name' 		=> 'post_type_product',
					'title' 	=> esc_html__( 'WooCommerce', 'codevz' ),
					'sections'  => array(

						array(
							'name'   => 'woo_settings',
							'title'  => esc_html__( 'Woocommerce Settings', 'codevz' ),
							'fields' => wp_parse_args(
								self::title_options( '_product', '.cz-cpt-product ' ),
								array(
									array(
										'id' 		=> 'layout_product',
										'type' 		=> 'image_select',
										'title' 	=> esc_html__( 'Sidebar position', 'codevz' ),
										'desc'  	=> esc_html__( 'Default is from General > Layout', 'codevz' ),
										'help'  	=> esc_html__( 'For all shop and products pages', 'codevz' ),
										'options' 	=> array(
											'1'           	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-0.png',
											'ws' 			=> CDVZ_PLUGIN_URI . 'assets/admin_img/off.png',
											'bpnp'      	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-2.png',
											'center' 		=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-13.png',
											'right'       	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-3.png',
											'right-s'     	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-4.png',
											'left'        	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-5.png',
											'left-s'      	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-6.png',
											'both-side' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-7.png',
											'both-side2' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-8.png',
											'both-right'	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-9.png',
											'both-right2'	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-10.png',
											'both-left' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-11.png',
											'both-left2' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-12.png',
										),
										'help'  	=> esc_html__( 'Default layout for all WooCommerce pages', 'codevz' ),
										'default' => '1'
									),
									array(
										'id' 	=> 'woo_col',
										'type' 	=> 'image_select',
										'title' => esc_html__( 'Shop columns', 'codevz' ),
										'options' 	=> array(
											'2' => CDVZ_PLUGIN_URI . 'assets/admin_img/cols-2.png',
											'3' => CDVZ_PLUGIN_URI . 'assets/admin_img/cols-3.png',
											'4' => CDVZ_PLUGIN_URI . 'assets/admin_img/cols-4.png',
											'5' => CDVZ_PLUGIN_URI . 'assets/admin_img/cols-5.png',
											'6' => CDVZ_PLUGIN_URI . 'assets/admin_img/cols-6.png'
										),
										'default' 	=> '4'
									),
									array(
										'id'    => 'woo_items_per_page',
										'type'  => 'slider',
										'title' => esc_html__( 'Products per page', 'codevz' ),
										'options'	=> array( 'unit' => '', 'step' => 1, 'min' => -1, 'max' => 100 ),
									),
									array(
										'id' 	=> 'woo_related_col',
										'type' 	=> 'image_select',
										'title' => esc_html__( 'Related products', 'codevz' ),
										'options' 	=> array(
											'2' => CDVZ_PLUGIN_URI . 'assets/admin_img/cols-2.png',
											'3' => CDVZ_PLUGIN_URI . 'assets/admin_img/cols-3.png',
											'4' => CDVZ_PLUGIN_URI . 'assets/admin_img/cols-4.png'
										),
										'default' 	=> '3'
									),
									array(
										'id'    		=> 'woo_cart',
										'type'  		=> 'text',
										'title' 		=> esc_html__( 'Cart translation', 'codevz' ),
										'default' 		=> 'Cart',
										'setting_args' 	=> array( 'transport' => 'postMessage' ),
									),
									array(
										'id'    		=> 'woo_checkout',
										'type'  		=> 'text',
										'title' 		=> esc_html__( 'Cart checkout translation', 'codevz' ),
										'default' 		=> 'Checkout',
										'setting_args' 	=> array( 'transport' => 'postMessage' ),
									),
									array(
										'id'    		=> 'woo_no_products',
										'type'  		=> 'text',
										'title' 		=> esc_html__( 'Cart no prodcuts translation', 'codevz' ),
										'default' 		=> 'No products in the cart',
										'setting_args' 	=> array( 'transport' => 'postMessage' ),
									),
								)
							)
						),

						array(
							'name'   => 'woo_styles',
							'title'  => esc_html__( 'Woocommerce Styles', 'codevz' ),
							'fields' => array(
								array(
									'type'    => 'notice',
									'class'   => 'info',
									'content' => esc_html__( 'Styles', 'codevz' ) . self::$sk_advanced
								),
								array(
									'id' 			=> '_css_woo_products_overall',
									'hover_id' 		=> '_css_woo_products_overall_hover',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Products', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'background', 'padding', 'border' ),
									'selector' 		=> '.woocommerce ul.products li.product .woocommerce-loop-product__link'
								),
								array(
									'id' 			=> '_css_woo_products_overall_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce ul.products li.product .woocommerce-loop-product__link'
								),
								array(
									'id' 			=> '_css_woo_products_overall_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce ul.products li.product .woocommerce-loop-product__link'
								),
								array(
									'id' 			=> '_css_woo_products_overall_hover',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce ul.products li.product:hover .woocommerce-loop-product__link'
								),
								array(
									'id' 			=> '_css_woo_products_thumbnails',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Images', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'background', 'padding', 'border', 'border-radius' ),
									'selector' 		=> '.woocommerce ul.products li.product a img'
								),
								array(
									'id' 			=> '_css_woo_products_thumbnails_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce ul.products li.product a img'
								),
								array(
									'id' 			=> '_css_woo_products_thumbnails_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce ul.products li.product a img'
								),
								array(
									'id' 			=> '_css_woo_products_title',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Titles', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'font-family', 'font-size', 'text-align', 'float' ),
									'selector' 		=> '.woocommerce ul.products li.product .woocommerce-loop-category__title, .woocommerce ul.products li.product .woocommerce-loop-product__title, .woocommerce ul.products li.product h3,.woocommerce.woo-template-2 ul.products li.product .woocommerce-loop-category__title, .woocommerce.woo-template-2 ul.products li.product .woocommerce-loop-product__title, .woocommerce.woo-template-2 ul.products li.product h3'
								),
								array(
									'id' 			=> '_css_woo_products_title_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce ul.products li.product .woocommerce-loop-category__title, .woocommerce ul.products li.product .woocommerce-loop-product__title, .woocommerce ul.products li.product h3,.woocommerce.woo-template-2 ul.products li.product .woocommerce-loop-category__title, .woocommerce.woo-template-2 ul.products li.product .woocommerce-loop-product__title, .woocommerce.woo-template-2 ul.products li.product h3'
								),
								array(
									'id' 			=> '_css_woo_products_title_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce ul.products li.product .woocommerce-loop-category__title, .woocommerce ul.products li.product .woocommerce-loop-product__title, .woocommerce ul.products li.product h3,.woocommerce.woo-template-2 ul.products li.product .woocommerce-loop-category__title, .woocommerce.woo-template-2 ul.products li.product .woocommerce-loop-product__title, .woocommerce.woo-template-2 ul.products li.product h3'
								),
								array(
									'id' 			=> '_css_woo_products_stars',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Rating stars', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'font-size' ),
									'selector' 		=> '.woocommerce ul.products li.product .star-rating'
								),
								array(
									'id' 			=> '_css_woo_products_stars_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce ul.products li.product .star-rating'
								),
								array(
									'id' 			=> '_css_woo_products_stars_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce ul.products li.product .star-rating'
								),
								array(
									'id' 			=> '_css_woo_products_onsale',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'On sale badge', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'line-height', 'width', 'height', 'color', 'background', 'font-family', 'font-size', 'top', 'left', 'border' ),
									'selector' 		=> '.woocommerce span.onsale, .woocommerce ul.products li.product .onsale'
								),
								array(
									'id' 			=> '_css_woo_products_onsale_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce span.onsale, .woocommerce ul.products li.product .onsale'
								),
								array(
									'id' 			=> '_css_woo_products_onsale_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce span.onsale, .woocommerce ul.products li.product .onsale'
								),
								array(
									'id' 			=> '_css_woo_products_price',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Price', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'background', 'font-family', 'font-size', 'top', 'right' ),
									'selector' 		=> '.woocommerce ul.products li.product .price'
								),
								array(
									'id' 			=> '_css_woo_products_price_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce ul.products li.product .price'
								),
								array(
									'id' 			=> '_css_woo_products_price_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce ul.products li.product .price'
								),
								array(
									'id' 			=> '_css_woo_products_add_to_cart',
									'hover_id' 		=> '_css_woo_products_add_to_cart_hover',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Add to cart button', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'font-family', 'font-size', 'opacity', 'float', 'background', 'border' ),
									'selector' 		=> '.woocommerce .button.product_type_simple.add_to_cart_button.ajax_add_to_cart, .woocommerce .button.product_type_variable.add_to_cart_button'
								),
								array(
									'id' 			=> '_css_woo_products_add_to_cart_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce .button.product_type_simple.add_to_cart_button.ajax_add_to_cart, .woocommerce .button.product_type_variable.add_to_cart_button'
								),
								array(
									'id' 			=> '_css_woo_products_add_to_cart_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce .button.product_type_simple.add_to_cart_button.ajax_add_to_cart, .woocommerce .button.product_type_variable.add_to_cart_button'
								),
								array(
									'id' 			=> '_css_woo_products_add_to_cart_hover',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce .button.product_type_simple.add_to_cart_button.ajax_add_to_cart:hover, .woocommerce .button.product_type_variable.add_to_cart_button:hover'
								),
								array(
									'id' 			=> '_css_woo_products_added_to_cart',
									'hover_id' 		=> '_css_woo_products_added_to_cart_hover',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'View cart link', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'font-size', 'font-style' ),
									'selector' 		=> '.woocommerce a.added_to_cart'
								),
								array(
									'id' 			=> '_css_woo_products_added_to_cart_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce a.added_to_cart'
								),
								array(
									'id' 			=> '_css_woo_products_added_to_cart_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce a.added_to_cart'
								),
								array(
									'id' 			=> '_css_woo_products_added_to_cart_hover',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce a.added_to_cart:hover'
								),
								array(
									'id' 			=> '_css_woo_products_result_count',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Shop result count', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'background', 'font-size', 'padding', 'border' ),
									'selector' 		=> '.woocommerce .woocommerce-result-count'
								),
								array(
									'id' 			=> '_css_woo_products_result_count_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce .woocommerce-result-count'
								),
								array(
									'id' 			=> '_css_woo_products_result_count_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce .woocommerce-result-count'
								),

								array(
									'type'    => 'notice',
									'class'   => 'info',
									'content' => esc_html__( 'Product Single Page', 'codevz' )
								),
								array(
									'id' 			=> '_css_woo_product_thumbnail',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Image', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'background', 'padding', 'border' ),
									'selector' 		=> '.woocommerce div.product div.images img'
								),
								array(
									'id' 			=> '_css_woo_product_thumbnail_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce div.product div.images img'
								),
								array(
									'id' 			=> '_css_woo_product_thumbnail_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce div.product div.images img'
								),
								array(
									'id' 			=> '_css_woo_product_title',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Title', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'text-align', 'color', 'font-family', 'font-size' ),
									'selector' 		=> '.woocommerce div.product .product_title'
								),
								array(
									'id' 			=> '_css_woo_product_title_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce div.product .product_title'
								),
								array(
									'id' 			=> '_css_woo_product_title_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce div.product .product_title'
								),
								array(
									'id' 			=> '_css_woo_product_stars',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Rating stars', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'font-size', 'padding' ),
									'selector' 		=> '.woocommerce .woocommerce-product-rating .star-rating'
								),
								array(
									'id' 			=> '_css_woo_product_stars_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce .woocommerce-product-rating .star-rating'
								),
								array(
									'id' 			=> '_css_woo_product_stars_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce .woocommerce-product-rating .star-rating'
								),
								array(
									'id' 			=> '_css_woo_product_price',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Price', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'background', 'font-family', 'font-size' ),
									'selector' 		=> '.woocommerce div.product .summary p.price, .woocommerce div.product .summary span.price'
								),
								array(
									'id' 			=> '_css_woo_product_price_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce div.product .summary p.price, .woocommerce div.product .summary span.price'
								),
								array(
									'id' 			=> '_css_woo_product_price_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce div.product .summary p.price, .woocommerce div.product .summary span.price'
								),
								array(
									'id' 			=> '_css_woo_product_oos',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Out of stock', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'font-size' ),
									'selector' 		=> '.woocommerce div.product .out-of-stock'
								),
								array(
									'id' 			=> '_css_woo_product_oos_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce div.product .out-of-stock'
								),
								array(
									'id' 			=> '_css_woo_product_oos_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce div.product .out-of-stock'
								),
								array(
									'id' 			=> '_css_woo_product_meta',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Product meta', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'font-size' ),
									'selector' 		=> '.woocommerce .product_meta'
								),
								array(
									'id' 			=> '_css_woo_product_meta_tablet',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce .product_meta'
								),
								array(
									'id' 			=> '_css_woo_product_meta_mobile',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce .product_meta'
								),
								array(
									'id' 			=> '_css_woo_product_meta_link',
									'hover_id' 		=> '_css_woo_product_meta_link_hover',
									'type' 			=> 'cz_sk',
									'button' 		=> esc_html__( 'Meta links', 'codevz' ),
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'settings' 		=> array( 'color', 'background', 'font-size', 'border' ),
									'selector' 		=> '.woocommerce .product_meta a'
								),
								array(
									'id' 			=> '_css_woo_product_meta_link_hover',
									'type' 			=> 'cz_sk_hidden',
									'setting_args' 	=> array( 'transport' => 'postMessage' ),
									'selector' 		=> '.woocommerce .product_meta a:hover'
								),
							)
						)

					)
				);
			}

			// BuddyPress options
			if ( function_exists( 'is_buddypress' ) ) {
				$options[] = array(
					'name'   => 'post_type_buddypress',
					'title'  => esc_html__( 'Buddy Press', 'codevz' ),
					'fields' => wp_parse_args( 
						self::title_options( '_buddypress', '.cz-cpt-buddypress ' ),
						array(
							array(
								'id' 	=> 'layout_buddypress',
								'type' 	=> 'image_select',
								'title' => esc_html__( 'Sidebar position', 'codevz' ),
								'options' 	=> array(
									'1'           	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-0.png',
									'ws' 			=> CDVZ_PLUGIN_URI . 'assets/admin_img/off.png',
									'bpnp'      	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-2.png',
									'center' 		=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-13.png',
									'right'       	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-3.png',
									'right-s'     	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-4.png',
									'left'        	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-5.png',
									'left-s'      	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-6.png',
									'both-side' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-7.png',
									'both-side2' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-8.png',
									'both-right'	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-9.png',
									'both-right2'	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-10.png',
									'both-left' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-11.png',
									'both-left2' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-12.png',
								),
								'default' => '1'
							),
						)
					)
				);
			}

			// EDD options
			if ( function_exists( 'EDD' ) ) {
				$options[] = array(
					'name'   => 'post_type_download',
					'title'  => esc_html__( 'Easy Digital Downloads', 'codevz' ),
					'fields' => wp_parse_args( 
						self::title_options( '_download', '.cz-cpt-download ' ),
						array(
							array(
								'id' 			=> 'layout_download',
								'type' 			=> 'image_select',
								'title' 		=> esc_html__( 'Sidebar position', 'codevz' ),
								'options' 	=> array(
									'1'           	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-0.png',
									'ws' 			=> CDVZ_PLUGIN_URI . 'assets/admin_img/off.png',
									'bpnp'      	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-2.png',
									'center' 		=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-13.png',
									'right'       	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-3.png',
									'right-s'     	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-4.png',
									'left'        	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-5.png',
									'left-s'      	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-6.png',
									'both-side' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-7.png',
									'both-side2' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-8.png',
									'both-right'	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-9.png',
									'both-right2'	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-10.png',
									'both-left' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-11.png',
									'both-left2' 	=> CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-12.png',
								),
								'default' => '1'
							),
						)
					)
				);
			}

			// Customize options for current widgets
			$options[] = array(
				'name'   => 'cz_customize_widgets',
				'title'  => esc_html__( 'Customize widgets', 'codevz' ),
				'fields' => self::sidebars_widgets()
			);

			$options[] = array(
				'name'   => 'backup_section',
				'title'  => esc_html__( 'Backup / Reset', 'codevz' ),
				'priority' => 900,
				'fields' => array(
					array(
						'type'    => 'notice',
						'class'   => 'info',
						'content' => esc_html__( 'You can save your current options. Download a Backup or Import options.', 'codevz' )
					),
					array(
						'type' => 'backup'
					),
				)
			);

	  /*
	  $ids = array();
	  foreach ( $options['header']['sections'] as $key ) {
		foreach ( $key['fields'] as $k ) {
		  if ( ! empty( $k['id'] ) 
			&& $k['id'] !== 'logo' 
			&& $k['id'] !== '_css_logo_css' 
			&& $k['id'] !== '_css_logo_css_tablet' 
			&& $k['id'] !== '_css_logo_css_mobile' 
			&& $k['id'] !== 'logo_2' 
			&& $k['id'] !== '_css_logo_2_css' 
			&& $k['id'] !== '_css_logo_2_css_tablet' 
			&& $k['id'] !== '_css_logo_2_css_mobile' 
			&& $k['id'] !== 'logo_hover_tooltip' 
			&& $k['id'] !== '_css_logo_hover_tooltip' 
			&& $k['id'] !== '_css_logo_hover_tooltip_tablet' 
			&& $k['id'] !== '_css_logo_hover_tooltip_mobile' 
			&& $k['id'] !== 'social' 
		  ) {
			$ids[ $k['id'] ] = '';
		  }
		}
	  }
	  var_export( $ids );
	  */

			return $options;
		}

		/**
		 *
		 * Get CSS selector via option ID
		 * 
		 * @return string
		 *
		 */
		public static function get_selector( $i = '', $s = array() ) {

			$new_date = filemtime( __FILE__ );
			$save_date = get_option( 'codevz_options_file_modified_date' );

			if ( $save_date != $new_date || ! get_option( 'codevz_css_selectors' ) ) {
				foreach( self::options() as $option ) {
					if ( ! empty( $option['sections'] ) ) {
						foreach ( $option['sections'] as $section ) {
							if ( ! empty( $section['fields'] ) ) {
								foreach( $section['fields'] as $field ) {
									if ( ! empty( $field['id'] ) && ! empty( $field['selector'] ) ) {
										$s[ $field['id'] ] = $field['selector'];
									}
								}
							}
						}
					} else {
						if ( ! empty( $option['fields'] ) ) {
							foreach( $option['fields'] as $field ) {
								if ( ! empty( $field['id'] ) && ! empty( $field['selector'] ) ) {
									$s[ $field['id'] ] =  $field['selector'];
								}
							}
						}
					}
				}

				update_option( 'codevz_css_selectors', $s );
				update_option( 'codevz_options_file_modified_date', $new_date );
			} else {
				$s = get_option( 'codevz_css_selectors', array() );
			}

			// Append dynamic widgets and sidebars
			$sidebars_widgets = (array) get_option( 'sidebars_widgets' );
			foreach ( $sidebars_widgets as $sidebar => $widgets ) {
				if ( $sidebar && $sidebar !== 'wp_inactive_widgets' && is_array( $widgets ) ) {
					foreach ( $widgets as $widget ) {
						$s[ '_css_' . $sidebar . '_' . $widget ] =  '.sidebar_' . $sidebar . ' #' . $widget;
					}
				}
			}

			return ( $i === 'all' ) ? $s : ( isset( $s[ $i ] ) ? $s[ $i ] : '' );
		}

		/**
		 *
		 * Get all sidebars and add widgets settings into customize page
		 * 
		 * @return array
		 *
		 */
		public static function sidebars_widgets() {
			$o = array(
				array(
					'type'    => 'notice',
					'class'   => 'info',
					'content' => esc_html__( 'Deprecated', 'codevz' ) . '<br />' . esc_html__( 'Do not use this options, StyleKits for widgets are deprecated and new options has been moved into each widget as an separate option. Go to Appearance > Widgets you can customize each widget styling.', 'codevz' )
				),
			);
			$a = (array) get_option( 'sidebars_widgets' );

			foreach ( $a as $sidebar => $widgets ) {
				if ( $sidebar && $sidebar !== 'wp_inactive_widgets' && is_array( $widgets ) ) {
					foreach ( $widgets as $widget ) {
						$o[] = array(
							'id' 			=> '_css_' . $sidebar . '_' . $widget,
							'hover_id' 		=> '_css_' . $sidebar . '_' . $widget . '_hover',
							'type' 			=> 'cz_sk',
							'button' 		=> ucfirst( $sidebar ) . ' : ' . $widget,
							'setting_args' 	=> array( 'transport' => 'postMessage' ),
							'settings' 		=> array( 'color', 'background', 'padding', 'margin', 'border' ),
							'selector' 		=> '.sidebar_' . $sidebar . ' #' . $widget
						);
						$o[] = array(
							'id' 			=> '_css_' . $sidebar . '_' . $widget . '_tablet',
							'type' 			=> 'cz_sk_hidden',
							'setting_args' 	=> array( 'transport' => 'postMessage' ),
							'selector' 		=> '.sidebar_' . $sidebar . ' #' . $widget
						);
						$o[] = array(
							'id' 			=> '_css_' . $sidebar . '_' . $widget . '_mobile',
							'type' 			=> 'cz_sk_hidden',
							'setting_args' 	=> array( 'transport' => 'postMessage' ),
							'selector' 		=> '.sidebar_' . $sidebar . ' #' . $widget
						);
					}
				}
			}

			return $o;
		}

		/**
		 *
		 * General help texts for options
		 * 
		 * @return array
		 *
		 */
		public static function help( $i ) {

			$o = array(
				'4'				=> 'e.g. 10px 10px 10px 10px',
				'px'			=> 'e.g. 30px',
				'padding'		=> esc_html__( 'Space around an element, INSIDE of any defined margins and borders. Can set using px, %, em, ...', 'codevz' ),
				'margin'		=> esc_html__( 'Space around an element, OUTSIDE of any defined borders. Can set using px, %, em, auto, ...', 'codevz' ),
				'border'		=> esc_html__( 'Lines around element, e.g. 2px or manually set with this four positions respectively: <br />Top Right Bottom Left <br/><br/>e.g. 2px 2px 2px 2px', 'codevz' ),
				'radius'		=> esc_html__( 'Generate the arc for lines around element, e.g. 10px or manually set with this four positions respectively: <br />Top Right Bottom Left <br/><br/>e.g. 10px 10px 10px 10px', 'codevz' ),
				'default'		=> esc_html__( 'Default option', 'codevz' ),
			);

			return isset( $o[ $i ] ) ? $o[ $i ] : '';
		}

		/**
		 *
		 * Header builder elements
		 * 
		 * @return array
		 *
		 */
		public static function elements( $id, $title, $dependency = array(), $pos = '' ) {

			$is_fixed_side = Codevz_Plus::contains( $id, 'side' );
			$is_1_2_3 = Codevz_Plus::contains( $id, array( 'header_1', 'header_2', 'header_3' ) );
			$is_footer = Codevz_Plus::contains( $id, 'footer' );

			return array(
				'id'              => $id,
				'type'            => 'group',
				'title'           => $title,
				'button_title'    => esc_html__( 'Add', 'codevz' ) . ' ' . ucwords( $pos ),
				'accordion_title' => esc_html__( 'Add', 'codevz' ) . ' ' . ucwords( $pos ),
				'dependency'	  => $dependency,
				'setting_args' 	  => array( 'transport' => 'postMessage' ),
				'fields'          => array(

					array(
						'id' 	=> 'element',
						'type' 	=> 'select',
						'title' => esc_html__( 'Element', 'codevz' ),
						'options' => array(
							'logo' 		=> esc_html__( 'Logo', 'codevz' ),
							'logo_2' 	=> esc_html__( 'Logo Alternative', 'codevz' ),
							'menu' 		=> esc_html__( 'Menu', 'codevz' ),
							'social' 	=> esc_html__( 'Social icons', 'codevz' ),
							'icon' 		=> esc_html__( 'Icon and text', 'codevz' ),
							'search' 	=> esc_html__( 'Search', 'codevz' ),
							'line' 		=> esc_html__( 'Line', 'codevz' ),
							'button' 	=> esc_html__( 'Button', 'codevz' ),
							'image' 	=> esc_html__( 'Image', 'codevz' ),
							'shop_cart' => esc_html__( 'Shopping cart', 'codevz' ),
							'wpml' 		=> esc_html__( 'WPML selector', 'codevz' ),
							'widgets' 	=> esc_html__( 'Offcanvas sidebar', 'codevz' ),
							'hf_elm' 	=> esc_html__( 'Dropdown content', 'codevz' ),
							'avatar' 	=> esc_html__( 'Logged-in user GrAvatar', 'codevz' ),
							'custom' 	=> esc_html__( 'Custom shortcode', 'codevz' ),
							'custom_element' => esc_html__( 'Custom page', 'codevz' ),
						),
						'default_option' => esc_html__( 'Select', 'codevz'),
					),

					// Element ID for live customize
					array(
						'id'   		 => 'element_id',
						'title'   	 => 'ID',
						'type'       => 'text',
						'default'    => $id,
						'dependency' => array( 'xxx', '==', 'xxx' ),
					),

					// Custom
					array(
						'id' 			=> 'header_elements',
						'type' 			=> 'select',
						'title'			=> esc_html__( 'Select page as element', 'codevz' ),
						'options' 		=> Codevz_Plus::$array_pages,
						'default_option'=> esc_html__( 'Select', 'codevz' ),
						'dependency' 	=> array( 'element', '==', 'custom_element' ),
					),

					// Custom
					array(
						'id'    		=> 'custom',
						'type'  		=> 'textarea',
						'title' 		=> esc_html__( 'Custom shortcode', 'codevz' ),
						'default' 		=> 'Insert shortcode or HTML',
						'dependency' 	=> array( 'element', '==', 'custom' ),
					),

					// Element margin
					array(
						'id'        => 'margin',
						'type'      => 'codevz_sizes',
						'title'     => esc_html__( 'Margin', 'codevz' ),
						'options'	=> array( 'unit' => 'px', 'step' => 1, 'min' => -20, 'max' => 100 ),
						'default'	=> array(
							'top' 		=> '20px',
							'right' 	=> '',
							'bottom' 	=> '20px',
							'left' 		=> '',
						),
						'help'		=> self::help('margin'),
						'dependency' => array( 'element', '!=', '' )
					),

					// Logo
					array(
						'id'    => 'logo_width',
						'type'  => 'slider',
						'title' => esc_html__( 'Width', 'codevz' ),
						'options'	=> array( 'unit' => 'px', 'step' => 1, 'min' => 0, 'max' => 500 ),
						'dependency' => array( 'element', 'any', 'logo,logo_2' ),
					),

					// Menu
					array(
						'id' 		=> 'menu_location',
						'type' 		=> 'select',
						'title' 	=> esc_html__( 'Menu location', 'codevz' ),
						'help' 		=> esc_html__( 'To add or edit menus go to Appearance > Menus', 'codevz' ),
						'options' 	=> array(
							'' 			=> esc_html__( 'Select', 'codevz' ), 
							'primary' 	=> esc_html__( 'Primary', 'codevz' ), 
							'secondary' => esc_html__( 'Secondary', 'codevz' ), 
							'one-page'  => esc_html__( 'One Page', 'codevz' ), 
							'footer'  	=> esc_html__( 'Footer', 'codevz' ),
							'mobile'  	=> esc_html__( 'Mobile', 'codevz' ),
							'custom-1' 	=> esc_html__( 'Custom 1', 'codevz' ), 
							'custom-2' 	=> esc_html__( 'Custom 2', 'codevz' ), 
							'custom-3' 	=> esc_html__( 'Custom 3', 'codevz' ),
							'custom-4' 	=> esc_html__( 'Custom 4', 'codevz' ),
							'custom-5' 	=> esc_html__( 'Custom 5', 'codevz' )
						),
						'dependency' => array( 'element', '==', 'menu' ),
					),
					array(
						'id'    => 'menu_type',
						'type'  => 'select',
						'title' => esc_html__( 'Menu type', 'codevz' ),
						'options' 	=> array(
							'' 							   => esc_html__( 'Default', 'codevz' ),
							'offcanvas_menu_left' 		   => esc_html__( 'Offcanvas left', 'codevz' ),
							'offcanvas_menu_right' 		   => esc_html__( 'Offcanvas right', 'codevz' ),
							'fullscreen_menu' 			   => esc_html__( 'Full screen', 'codevz' ),
							'dropdown_menu' 			   => esc_html__( 'Dropdown', 'codevz' ),
							'open_horizontal inview_left'  => esc_html__( 'Sliding menu left', 'codevz' ),
							'open_horizontal inview_right' => esc_html__( 'Sliding menu right', 'codevz' ),
							'left_side_dots side_dots' 	   => esc_html__( 'Vertical dots left', 'codevz' ),
							'right_side_dots side_dots'    => esc_html__( 'Vertical dots right', 'codevz' ),
						),
						'dependency' => array( 'element', '==', 'menu' ),
					),
					array(
						'type'    		=> 'content',
						'content' 		=> '<a class="button cz_menu_sk" href="#customize-control-codevz_theme_options-_css_menu_container_' . str_replace( '_' . $pos, '', $id ) . '">' . esc_html__( 'Menu Styling', 'codevz' ) . '</a>',
						'dependency' 	=> array( 'element', '==', 'menu' ),
					),
					array(
						'id'    		=> 'menu_icon',
						'type'  		=> 'icon',
						'title' 		=> esc_html__( 'Icon', 'codevz' ),
						'dependency' 	=> array( 'element|menu_type', '==|any', 'menu|offcanvas_menu_left,offcanvas_menu_right,fullscreen_menu,dropdown_menu,open_horizontal inview_left,open_horizontal inview_right' ),
					),
					array(
						'id'    		=> 'menu_title',
						'type'  		=> 'text',
						'title' 		=> esc_html__( 'Title', 'codevz' ),
						'dependency' 	=> array( 'element|menu_type', '==|any', 'menu|offcanvas_menu_left,offcanvas_menu_right,fullscreen_menu,dropdown_menu,open_horizontal inview_left,open_horizontal inview_right' ),
					),
					array(
						'id' 			=> 'sk_menu_icon',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'Icon styling', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'font-size', 'color', 'background', 'border' ),
						'dependency' 	=> array( 'element|menu_type', '==|any', 'menu|offcanvas_menu_left,offcanvas_menu_right,fullscreen_menu,dropdown_menu,open_horizontal inview_left,open_horizontal inview_right' ),
					),

					// Social
					array(
						'type'    		=> 'content',
						'content' 		=> esc_html__( 'To add or edit social icons go to Theme Options > Header > Social icons', 'codevz' ),
						'dependency' 	=> array( 'element', '==', 'social' ),
					),

					// Image
					array(
						'id'    => 'image',
						'type'  => 'upload',
						'title' => esc_html__( 'Image', 'codevz' ),
						'preview'       => 1,
						'dependency' => array( 'element', '==', 'image' ),
						'attributes' => array(
							'style'		=> 'display: block'
						)
					),
					array(
						'id'    => 'image_width',
						'type'  => 'slider',
						'title' => esc_html__( 'Width', 'codevz' ),
						'options'	=> array( 'unit' => 'px', 'step' => 1, 'min' => 0, 'max' => 800 ),
						'dependency' => array( 'element', '==', 'image' ),
					),
					array(
						'id'    => 'image_link',
						'type'  => 'text',
						'title' => esc_html__( 'Link', 'codevz' ),
						'dependency' => array( 'element', '==', 'image' ),
					),

					// Icon & Text
					array(
						'id'    		=> 'it_text',
						'type'  		=> 'textarea',
						'title' 		=> esc_html__( 'Text', 'codevz' ),
						'help'  		=> esc_html__( 'Instead current year use this [cz_current_year]', 'codevz' ),
						'default'  		=> esc_html__( 'I am a text element and you can edit me.', 'codevz' ),
						'dependency' 	=> array( 'element', '==', 'icon' ),
					),
					array(
						'id' 			=> 'it_link',
						'type' 			=> 'text',
						'title' 		=> esc_html__( 'Link', 'codevz' ),
						'dependency' 	=> array( 'element', '==', 'icon' ),
					),
					array(
						'id' 			=> 'sk_it',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'Text styling', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'font-size', 'color' ),
						'dependency' 	=> array( 'element|it_text', '==|!=', 'icon|' )
					),
					array(
						'id'    => 'it_icon',
						'type'  => 'icon',
						'title' => esc_html__( 'Icon', 'codevz' ),
						'dependency' => array( 'element', '==', 'icon' ),
					),
					array(
						'id' 			=> 'sk_it_icon',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'Icon styling', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'font-size', 'color', 'background', 'border' ),
						'dependency' 	=> array( 'element|it_icon', '==|!=', 'icon|' )
					),

					// Search
					array(
						'id' 	=> 'search_type',
						'type' 	=> 'select',
						'title' => esc_html__( 'Search type', 'codevz' ),
						'options' 	=> array(
							'icon_dropdown' => esc_html__( 'Dropdown', 'codevz' ),
							'form' 			=> esc_html__( 'Form', 'codevz' ),
							'form_2' 		=> esc_html__( 'Form', 'codevz' ) . ' 2',
							'icon_full' 	=> esc_html__( 'Full screen', 'codevz' ),
							'icon_fullrow' 	=> esc_html__( 'Full row', 'codevz' ),
						),
						'dependency' => array( 'element', '==', 'search' ),
					),
					array(
						'id'    => 'search_placeholder',
						'type'  => 'text',
						'title' => esc_html__( 'Placeholder/Title', 'codevz' ),
						'dependency' => array( 'element', '==', 'search' ),
					),
					array(
						'id' 			=> 'sk_search_title',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'Title styling', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'font-size', 'color' ),
						'dependency' 	=> array( 'element|search_type', '==|==', 'search|icon_full' )
					),
					array(
						'id'    => 'search_form_width',
						'type'  => 'slider',
						'title' => esc_html__( 'Form width', 'codevz' ),
						'options' => array( 'unit' => 'px', 'step' => 1, 'min' => 100, 'max' => 500 ),
						'dependency' => array( 'element|search_type', '==|any', 'search|form,form_2' ),
					),
					array(
						'id' 			=> 'sk_search_con',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'Search container', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'background', 'padding', 'border' ),
						'dependency' 	=> array( 'element', '==', 'search' ),
					),
					array(
						'id' 			=> 'sk_search_input',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'Search input', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'font-size', 'color', 'background', 'border' ),
						'dependency' 	=> array( 'element', '==', 'search' )
					),
					array(
						'id'    => 'search_icon',
						'type'  => 'icon',
						'title' => esc_html__( 'Icon', 'codevz' ),
						'dependency' => array( 'element|search_type', '==|any', 'search|icon_dropdown,icon_full,icon_fullrow' ),
					),
					array(
						'id' 			=> 'sk_search_icon',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'Search icon', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'font-size', 'color', 'background', 'border' ),
						'dependency' 	=> array( 'element|search_type', '==|any', 'search|icon_dropdown,icon_full,icon_fullrow' ),
					),
					array(
						'id' 			=> 'sk_search_icon_in',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'Search icon in input', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'font-size', 'color', 'background', 'border' ),
						'dependency' 	=> array( 'element', '==', 'search' ),
					),
					array(
						'id' 		=> 'ajax_search',
						'type' 		=> 'switcher',
						'title'		=> esc_html__( 'Ajax Search?', 'codevz' ),
						'dependency' => array( 'element', '==', 'search' ),
					),
					array(
						'id' 		=> 'search_cpt',
						'type' 		=> 'text',
						'title'		=> esc_html__( 'Post type(s)', 'codevz' ),
						'help'		=> 'e.g. post,portfolio,product',
						'dependency' => array( 'ajax_search|element', '!=|==', '|search' ),
					),
					array(
						'id' 		=> 'search_count',
						'type' 		=> 'slider',
						'title'		=> esc_html__( 'Search count', 'codevz' ),
						'options' 	=> array( 'unit' => '', 'step' => 1, 'min' => 1, 'max' => 12 ),
						'dependency' => array( 'ajax_search|element', '!=|==', '|search' ),
					),
					array(
						'id' 		=> 'search_no_thumbnail',
						'type' 		=> 'switcher',
						'title'		=> esc_html__( 'No thumbnails?', 'codevz' ),
						'dependency' => array( 'ajax_search|element', '!=|==', '|search' ),
					),
					array(
						'id' 		=> 'search_view_all_translate',
						'type' 		=> 'text',
						'title'		=> esc_html__( 'View all translate', 'codevz' ),
						'dependency' => array( 'ajax_search|element', '!=|==', '|search' ),
					),
					array(
						'id' 			=> 'sk_search_ajax',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'Posts container', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'background', 'padding', 'border' ),
						'dependency' => array( 'ajax_search|element', '!=|==', '|search' ),
					),

					// Offcanvas
					array(
						'id' 		=> 'inview_position_widget',
						'type' 		=> 'select',
						'title' 	=> esc_html__( 'Direction?', 'codevz' ),
						'help' 		=> esc_html__( 'For adding or changing widgets in offcanvas area, go to Appearance > Widgets > Offcanvas', 'codevz' ),
						'options' 	=> array(
							'inview_left' 	=> esc_html__( 'Left', 'codevz' ),
							'inview_right' => esc_html__( 'Right', 'codevz' ),
						),
						'dependency' => array( 'element', '==', 'widgets' ),
					),
					array(
						'id' 			=> 'sk_offcanvas',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'Offcanvas styling', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'background', 'padding', 'border' ),
						'dependency' 	=> array( 'element', '==', 'widgets' )
					),
					array(
						'id'    => 'offcanvas_icon',
						'type'  => 'icon',
						'title' => esc_html__( 'Icon', 'codevz' ),
						'dependency' => array( 'element', '==', 'widgets' ),
					),
					array(
						'id' 			=> 'sk_offcanvas_icon',
						'hover_id' 		=> 'sk_offcanvas_icon_hover',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'Icon styling', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'color', 'font-size', 'background', 'border' ),
						'dependency' 	=> array( 'element', '==', 'widgets' )
					),
					array('id' => 'sk_offcanvas_icon_hover','type' => 'cz_sk_hidden'),

					// Hidden fullwidth content area
					array(
						'id' 			=> 'hf_elm_page',
						'type' 			=> 'select',
						'title'			=> esc_html__( 'Select page content', 'codevz' ),
						'help' 			=> esc_html__( 'You can create a new page from Dashboard > Page and assing it here', 'codevz' ),
						'options' 		=> Codevz_Plus::$array_pages,
						'default_option'=> esc_html__( 'Select', 'codevz' ),
						'dependency' 	=> array( 'element', '==', 'hf_elm' ),
					),
					array(
						'id' 			=> 'sk_hf_elm',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'Container styling', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'background', 'padding', 'border' ),
						'dependency' 	=> array( 'element', '==', 'hf_elm' )
					),
					array(
						'id'    => 'hf_elm_icon',
						'type'  => 'icon',
						'title' => esc_html__( 'Icon', 'codevz' ),
						'dependency' => array( 'element', '==', 'hf_elm' ),
					),
					array(
						'id' 			=> 'sk_hf_elm_icon',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'Icon styling', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'color', 'font-size', 'background', 'padding', 'border' ),
						'dependency' 	=> array( 'element', '==', 'hf_elm' )
					),

					// Shop
					array(
						'id' 		=> 'shopcart_type',
						'type' 		=> 'select',
						'title' 	=> esc_html__( 'Type', 'codevz' ),
						'help' 		=> esc_html__( 'If you can not see any difference, You need to save options and check it live site.', 'codevz' ),
						'options' 	=> array(
							'cart_1' 	=> '1',
							'cart_2' 	=> '2',
						),
						'dependency' => array( 'element', '==', 'shop_cart' ),
					),
					array(
						'id' 			=> 'sk_shop_count',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'Count styling', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'top', 'right', 'color', 'font-size', 'background', 'border' ),
						'dependency' 	=> array( 'element', '==', 'shop_cart' )
					),

					array(
						'id'    => 'shopcart_icon',
						'type'  => 'icon',
						'title' => esc_html__( 'Icon', 'codevz' ),
						'dependency' => array( 'element', '==', 'shop_cart' ),
					),
					array(
						'id' 			=> 'sk_shop_icon',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'Icon styling', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'color', 'font-size', 'background', 'border' ),
						'dependency' 	=> array( 'element', '==', 'shop_cart' )
					),
					array(
						'id' 			=> 'sk_shop_content',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'Shop content styling', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'background', 'padding', 'border' ),
						'dependency' 	=> array( 'element', '==', 'shop_cart' )
					),

					// Line
					array(
						'id' 	=> 'line_type',
						'type' 	=> 'select',
						'title' => esc_html__( 'Line type', 'codevz' ),
						'help'  => esc_html__( 'Background color for line is important that you can change it from line styling button.', 'codevz' ),
						'options' 	=> array(
			  				'header_line_2'   	=> esc_html__( 'Default', 'codevz' ),
							'header_line_1' 	=> esc_html__( 'Full height', 'codevz' ),
							'header_line_3' 	=> esc_html__( 'Slash', 'codevz' ),
							'header_line_4' 	=> esc_html__( 'Horizontal', 'codevz' ),
						),
						'dependency' => array( 'element', '==', 'line' ),
					),
					array(
						'id' 			=> 'sk_line',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'Line styling', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'background' ),
						'dependency' 	=> array( 'element', '==', 'line' )
					),

					// Button options
					array(
						'id'    	=> 'btn_title',
						'type'  	=> 'text',
						'title' 	=> esc_html__( 'Title', 'codevz' ),
						'default' 	=> esc_html__( 'Button title', 'codevz' ),
						'dependency' => array( 'element', '==', 'button' ),
					),
					array(
						'id'    => 'btn_link',
						'type'  => 'text',
						'title' => esc_html__( 'Link', 'codevz' ),
						'dependency' => array( 'element', '==', 'button' ),
					),
					array(
						'id' 			=> 'sk_btn',
						'hover_id' 		=> 'sk_btn_hover',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'Button styling', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'color', 'font-size', 'font-family', 'font-weight', 'background', 'border' ),
						'dependency' 	=> array( 'element', '==', 'button' )
					),
					array('id' => 'sk_btn_hover','type' => 'cz_sk_hidden'),

					// WPML
					array(
						'id' 	=> 'wpml_title',
						'type' 	=> 'select',
						'title' => esc_html__( 'Title', 'codevz' ),
						'options' 	=> array(
							'translated_name' 	=> esc_html__( 'Translated Name', 'codevz' ),
							'language_code' 	=> esc_html__( 'Language code', 'codevz' ),
							'native_name' 		=> esc_html__( 'Native name', 'codevz' ),
							'translated_name' 	=> esc_html__( 'Translated name', 'codevz' ),
							'no_title' 			=> esc_html__( 'No title', 'codevz' ),
						),
						'dependency' => array( 'element', '==', 'wpml' ),
					),
					array(
						'id'    => 'wpml_flag',
						'type'  => 'switcher',
						'title' => esc_html__( 'Flag?', 'codevz' ),
						'dependency' => array( 'element|wpml_title', '==|!=', 'wpml|country_flag_url' ),
					),
					array(
						'id'    => 'wpml_current_color',
						'type'  => 'color_picker',
						'title' => esc_html__( 'Current language color', 'codevz' ),
						'dependency' => array( 'element', '==', 'wpml' ),
					),
					array(
						'id'    => 'wpml_background',
						'type'  => 'color_picker',
						'title' => esc_html__( 'Background', 'codevz' ),
						'dependency' => array( 'element', '==', 'wpml' ),
					),
					array(
						'id'    => 'wpml_color',
						'type'  => 'color_picker',
						'title' => esc_html__( 'Inner color', 'codevz' ),
						'dependency' => array( 'element', '==', 'wpml' ),
					),

					// Avatar
					array(
						'id'    => 'avatar_size',
						'type'  => 'slider',
						'title' => esc_html__( 'Size', 'codevz' ),
						'dependency' => array( 'element', '==', 'avatar' ),
						'default' => '40px'
					),
					array(
						'id' 			=> 'sk_avatar',
						'type' 			=> 'cz_sk',
						'button' 		=> esc_html__( 'Avatar styling', 'codevz' ),
						'setting_args' 	=> array( 'transport' => 'postMessage' ),
						'settings' 		=> array( 'background', 'padding', 'border' ),
						'dependency' 	=> array( 'element', '==', 'avatar' )
					),
					array(
						'id'    => 'avatar_link',
						'type'  => 'text',
						'title' => esc_html__( 'Link', 'codevz' ),
						'dependency' => array( 'element', '==', 'avatar' ),
					),

					// Others
					array(
						'id' 		=> 'vertical',
						'type' 		=> 'switcher',
						'title'		=> esc_html__( 'Vertical?', 'codevz' ),
						'dependency' => $is_fixed_side ? array( 'element', 'any', 'social,icon' ) : array( 'element', '==', 'xxx' )
					),
					array(
						'id' 	=> 'elm_on_sticky',
						'type' 	=> 'select',
						'title' => esc_html__( 'Visibility on sticky?', 'codevz' ),
						'help'  => esc_html__( 'You can enable sticky mode from Theme Options > Header > Sticky Header', 'codevz' ),
						'options' 	=> array(
							'' 					=> esc_html__( 'Default', 'codevz' ),
							'show_on_sticky' 	=> esc_html__( 'Show on Sticky ?', 'codevz' ),
							'hide_on_sticky' 	=> esc_html__( 'Hide on Sticky ?', 'codevz' ),
						),
						'dependency' => $is_1_2_3 ? array( 'element', '!=', '' ) : array( 'element', '==', 'xxx' )
					),
					array(
						'id' 		=> 'elm_center',
						'type' 		=> 'switcher',
						'title'		=> esc_html__( 'Center?', 'codevz' ),
						'dependency' => $is_fixed_side ? array( 'element', '!=', '' ) : array( 'element', '==', 'xxx' )
					),
					array(
						'id' 		=> 'hide_on_mobile',
						'type' 		=> 'switcher',
						'title'		=> esc_html__( 'Hide on mobile?', 'codevz' ),
						'dependency' => $is_footer ? array( 'element', '!=', '' ) : array( 'element', '==', 'xxx' )
					),
					array(
						'id' 		=> 'hide_on_tablet',
						'type' 		=> 'switcher',
						'title'		=> esc_html__( 'Hide on tablet?', 'codevz' ),
						'dependency' => $is_footer ? array( 'element', '!=', '' ) : array( 'element', '==', 'xxx' )
					),

				)
			);
		}

		/**
		 *
		 * Header row builder options
		 * 
		 * @return array
		 *
		 */
		public static function row_options( $id, $positions = array('left', 'center', 'right') ) {

			$elm = '.' . $id;
			$out = array();

			// If is sticky so show dropdown option and create dependency
			if ( $id === 'header_5' ) {
				$elm = '.onSticky';
				$dependency = array( 'sticky_header', '==', '5' );
				
				$out[] = array(
					'id' 		=> 'sticky_header',
					'type' 		=> 'select',
					'title' 	=> esc_html__( 'Sticky type', 'codevz' ),
					'options' 	=> array(
						''			=> esc_html__( 'Off', 'codevz' ),
						'1'			=> esc_html__( 'Header top bar', 'codevz' ),
						'2'			=> esc_html__( 'Header', 'codevz' ),
						'3'     	=> esc_html__( 'Header bottom bar', 'codevz' ),
						'12'    	=> esc_html__( 'Header top bar + Header', 'codevz' ),
						'23'    	=> esc_html__( 'Header + Header bottom bar', 'codevz' ),
						'13'    	=> esc_html__( 'Header top bar + Header bottom bar', 'codevz' ),
						'123'	  	=> esc_html__( 'All Headers Sticky', 'codevz' ),
						'5'			=> esc_html__( 'Custom Sticky', 'codevz' ),
					)
				);
				$out[] = array(
					'id' 		=> 'smart_sticky',
					'type' 		=> 'switcher',
					'title' 	=> esc_html__( 'Smart sticky?', 'codevz' ),
					'dependency' => array( 'sticky_header', '!=', '' )
				);
			} else {
				$dependency = array();
			}

			// Fixed position before elements
			if ( $id === 'fixed_side_1' ) {
				$out[] = array(
					'id' 		=> 'fixed_side',
					'type' 		=> 'image_select',
					'title' 	=> esc_html__( 'Position?', 'codevz' ),
					'options' 	=> array(
						''		=> CDVZ_PLUGIN_URI . 'assets/admin_img/off.png',
						'left'	=> Codevz_Plus::$is_rtl ? CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-3.png' : CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-5.png',
						'right'	=> Codevz_Plus::$is_rtl ? CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-5.png' : CDVZ_PLUGIN_URI . 'assets/admin_img/sidebar-3.png',
					),
					'attributes' => array(
						'data-depend-id' => 'fixed_side'
					)
				);
				$dependency = array( 'fixed_side', 'any', 'left,right' );
			}

			// Tablet/Mobile header
			if ( $id === 'header_4' ) {
				$out[] = array(
				  'id'    => 'mobile_header',
				  'type'    => 'select',
				  'title'   => esc_html__( 'Start showing at?', 'codevz' ),
				  'options'   => array(
					''      => esc_html__( 'Default', 'codevz' ),
					'lt'    => esc_html__( 'Landscape Tablet', 'codevz' ),
					'pt'    => esc_html__( 'Portrait Tablet', 'codevz' ),
					'mm'    => esc_html__( 'Mobile', 'codevz' ),
				  )
				);
				$out[] = array(
					'id' 		=> 'mobile_sticky',
					'type' 		=> 'select',
					'title' 	=> esc_html__( 'Sticky mode?', 'codevz' ),
					'options' 	=> array(
						''								=> esc_html__( 'Select', 'codevz' ),
						'header_is_sticky'				=> esc_html__( 'Sticky', 'codevz' ),
						'header_is_sticky smart_sticky'	=> esc_html__( 'Smart Sticky', 'codevz' ),
					)
				);
			}

			// Left center right elements and style
			foreach ( $positions as $num => $pos ) {
				$num++;
				$out[] = self::elements( $id . '_' . $pos, '', $dependency, $pos );
			}

	  // Before after mobile header
	  if ( $id === 'header_4' ) {
		$out[] = array(
		  'id'            => 'b_mobile_header',
		  'type'          => 'select',
		  'title'         => esc_html__( 'Assing page before header', 'codevz' ),
		  'options'       => Codevz_Plus::$array_pages,
			'default_option'=> esc_html__( 'Select', 'codevz' ),
		);
		$out[] = array(
		  'id'            => 'a_mobile_header',
		  'type'          => 'select',
		  'title'         => esc_html__( 'Assing page after header', 'codevz' ),
		  'options'       => Codevz_Plus::$array_pages,
			'default_option'=> esc_html__( 'Select', 'codevz' ),
		);
	  }

			// If its fixed header so show dropdown option
			$out[] = array(
				'type'    => 'notice',
				'class'   => 'info',
				'content' => esc_html__( 'Styles', 'codevz' ) . self::$sk_advanced,
				'dependency' => $dependency
			);
			if ( $id === 'fixed_side_1' ) {
				$out[] = array(
					'id' 			=> '_css_fixed_side_style',
					'type' 			=> 'cz_sk',
					'button' 		=> esc_html__( 'Container', 'codevz' ),
					'setting_args' 	=> array( 'transport' => 'postMessage' ),
					'settings' 		=> array( 'background', 'width', 'border' ),
					'selector' 		=> '.fixed_side, .fixed_side .theiaStickySidebar'
				);
			} else {
				$f_dependency = ( $id === 'header_5' ) ? array( 'sticky_header', '!=', '' ) : array();
				$out[] = array(
					'id' 			=> '_css_container_' . $id,
					'type' 			=> 'cz_sk',
					'button' 		=> esc_html__( 'Container', 'codevz' ),
					'setting_args' 	=> array( 'transport' => 'postMessage' ),
					'settings' 		=> array( 'background', 'padding', 'border' ),
					'selector' 		=> $elm,
					'dependency' 	=> $f_dependency
				);
				$out[] = array(
					'id' 			=> '_css_row_' . $id,
					'type' 			=> 'cz_sk',
					'button' 		=> esc_html__( 'Row inner', 'codevz' ),
					'setting_args' 	=> array( 'transport' => 'postMessage' ),
					'settings' 		=> array( 'background', '_class_shape', 'width', 'padding', 'border' ),
					'selector' 		=> $elm . ' .row',
					'dependency' 	=> $f_dependency
				);
			}

			// Left center right elements and style
			foreach ( $positions as $num => $pos ) {
				$num++;
				$out[] = array(
					'id' 			=> '_css_' . $id . '_' . $pos,
					'type' 			=> 'cz_sk',
					'button' 		=> ucfirst( $pos ),
					'setting_args' 	=> array( 'transport' => 'postMessage' ),
					'settings' 		=> array( 'background', '_class_shape', 'padding', 'border' ),
					'selector' 		=> $elm . ' .elms_' . $pos,
					'dependency' 	=> $dependency
				);
			}

			// Menus style for each row
			$menu_unique_id = '#menu_' . $id;
			$out[] = array(
				'type' 			=> 'notice',
				'class' 		=> 'info',
				'content' 		=> esc_html__( 'Menu styles of this row', 'codevz' ),
				'dependency' 	=> $dependency
			);
			$out[] = array(
				'id' 			=> '_css_menu_container_' . $id,
				'type' 			=> 'cz_sk',
				'button' 		=> esc_html__( 'Menu container', 'codevz' ),
				'setting_args' 	=> array( 'transport' => 'postMessage' ),
				'settings' 		=> array( 'background', 'padding', 'border' ),
				'selector' 		=> $menu_unique_id,
				'dependency' 	=> $dependency
			);
			$out[] = array(
				'id' 			=> '_css_menu_li_' . $id,
				'type' 			=> 'cz_sk',
				'button' 		=> esc_html__( 'Menus parent', 'codevz' ),
				'setting_args' 	=> array( 'transport' => 'postMessage' ),
				'settings' 		=> array( 'float', 'text-align', 'padding', 'margin', 'border' ),
				'selector' 		=> $menu_unique_id . ' > .cz',
				'dependency' 	=> $dependency
			);
			$out[] = array(
				'id' 			=> '_css_menu_a_' . $id,
				'hover_id' 		=> '_css_menu_a_hover_' . $id,
				'type' 			=> 'cz_sk',
				'button' 		=> esc_html__( 'Menus', 'codevz' ),
				'setting_args' 	=> array( 'transport' => 'postMessage' ),
				'settings' 		=> array( 'color', 'background', 'font-family', 'font-size', 'padding', 'margin', 'border' ),
				'selector' 		=> $menu_unique_id . ' > .cz > a',
				'dependency' 	=> $dependency
			);
			$out[] = array(
				'id' 			=> '_css_menu_a_hover_' . $id,
				'type' 			=> 'cz_sk_hidden',
				'button' 		=> '',
				'setting_args' 	=> array( 'transport' => 'postMessage' ),
				'selector' 		=> $menu_unique_id . ' > .cz > a:hover,' . $menu_unique_id . ' > .cz:hover > a,' . $menu_unique_id . ' > .current_menu > a,' . $menu_unique_id . ' > .current-menu-parent > a',
				'dependency' 	=> $dependency
			);

			$out[] = array(
				'id' 			=> '_css_menu_a_hover_before_' . $id,
				'type' 			=> 'cz_sk',
				'button' 		=> esc_html__( 'Active shape', 'codevz' ),
				'setting_args' 	=> array( 'transport' => 'postMessage' ),
				'settings' 		=> array( '_class_menu_fx', 'background', 'height', 'width', 'left', 'bottom', 'border' ),
				'selector' 		=> $menu_unique_id . ' > .cz > a:before',
				'dependency' 	=> $dependency
			);
			$out[] = array(
				'id' 			=> '_css_menu_subtitle_' . $id,
				'hover_id' 		=> '_css_menu_subtitle_' . $id . '_hover',
				'type' 			=> 'cz_sk',
				'button' 		=> esc_html__( 'Subtitle', 'codevz' ),
				'setting_args' 	=> array( 'transport' => 'postMessage' ),
				'settings' 		=> array( 'color', 'background', 'font-size', 'padding', 'margin' ),
				'selector' 		=> $menu_unique_id . ' > .cz > a > .cz_menu_subtitle',
				'dependency' 	=> $dependency
			);
			$out[] = array(
				'id' 			=> '_css_menu_subtitle_' . $id . '_hover',
				'type' 			=> 'cz_sk_hidden',
				'button' 		=> '',
				'setting_args' 	=> array( 'transport' => 'postMessage' ),
				'selector' 		=> $menu_unique_id . ' > .cz > a:hover > .cz_menu_subtitle,' . $menu_unique_id . ' > .cz:hover > a > .cz_menu_subtitle,' . $menu_unique_id . ' > .current_menu > a > .cz_menu_subtitle,' . $menu_unique_id . ' > .current-menu-parent > a > .cz_menu_subtitle',
				'dependency' 	=> $dependency
			);
			$out[] = array(
				'id' 			=> '_css_menu_indicator_a_' . $id,
				'type' 			=> 'cz_sk',
				'button' 		=> esc_html__( 'Indicator', 'codevz' ),
				'setting_args' 	=> array( 'transport' => 'postMessage' ),
				'settings' 		=> array( 'color', 'font-size', '_class_indicator' ),
				'selector' 		=> $menu_unique_id . ' > .cz > a .cz_indicator',
				'dependency' 	=> $dependency
			);
			$out[] = array(
				'id' 			=> '_css_menus_separator_' . $id,
				'type' 			=> 'cz_sk',
				'button' 		=> esc_html__( 'Delimiter', 'codevz' ),
				'setting_args' 	=> array( 'transport' => 'postMessage' ),
				'settings' 		=> array( 'content', 'rotate', 'color', 'font-size', 'margin' ),
				'selector' 		=> $menu_unique_id . ' > .cz:after',
				'dependency' 	=> $dependency
			);
			$out[] = array(
				'id' 			=> '_css_menu_ul_' . $id,
				'type' 			=> 'cz_sk',
				'button' 		=> esc_html__( 'Dropdown', 'codevz' ),
				'setting_args' 	=> array( 'transport' => 'postMessage' ),
				'settings' 		=> array( '_class_submenu_fx', 'width', 'background', 'padding', 'margin', 'border' ),
				'selector' 		=> $menu_unique_id . ' .cz .sub-menu:not(.cz_megamenu_inner_ul),' . $menu_unique_id . ' .cz_megamenu_inner_ul .cz_megamenu_inner_ul',
				'dependency' 	=> $dependency
			);
			$out[] = array(
				'id' 			=> '_css_menu_ul_a_' . $id,
				'hover_id' 		=> '_css_menu_ul_a_hover_' . $id,
				'type' 			=> 'cz_sk',
				'button' 		=> esc_html__( 'Dropdown menus', 'codevz' ),
				'setting_args' 	=> array( 'transport' => 'postMessage' ),
				'settings' 		=> array( 'color', 'background', 'font-family', 'text-align', 'font-size', 'padding', 'margin', 'border' ),
				'selector' 		=> $menu_unique_id . ' .cz .cz a',
				'dependency' 	=> $dependency
			);
			$out[] = array(
				'id' 			=> '_css_menu_ul_a_hover_' . $id,
				'type' 			=> 'cz_sk_hidden',
				'button' 		=> '',
				'setting_args' 	=> array( 'transport' => 'postMessage' ),
				'selector' 		=> $menu_unique_id . ' .cz .cz a:hover,' . $menu_unique_id . ' .cz .cz:hover > a,' . $menu_unique_id . ' .cz .current_menu > a,' . $menu_unique_id . ' .cz .current_menu > .current_menu',
				'dependency' 	=> $dependency
			);
			$out[] = array(
				'id' 			=> '_css_menu_ul_indicator_a_' . $id,
				'type' 			=> 'cz_sk',
				'button' 		=> esc_html__( 'Dropdown menus indicator', 'codevz' ),
				'setting_args' 	=> array( 'transport' => 'postMessage' ),
				'settings' 		=> array( 'color', 'font-size', '_class_indicator' ),
				'selector' 		=> $menu_unique_id . ' .cz .cz a .cz_indicator',
				'dependency' 	=> $dependency
			);
			$out[] = array(
				'id' 			=> '_css_menu_ul_ul_' . $id,
				'type' 			=> 'cz_sk',
				'button' 		=> esc_html__( '3rd level dropdown', 'codevz' ),
				'setting_args' 	=> array( 'transport' => 'postMessage' ),
				'settings' 		=> array( 'margin' ),
				'selector' 		=> $menu_unique_id . ' .sub-menu .sub-menu:not(.cz_megamenu_inner_ul)',
				'dependency' 	=> $dependency
			);
			$out[] = array(
				'id' 			=> '_css_menu_inner_megamenu_' . $id,
				'type' 			=> 'cz_sk',
				'button' 		=> esc_html__( 'Megamenu lists', 'codevz' ),
				'setting_args' 	=> array( 'transport' => 'postMessage' ),
				'settings' 		=> array( 'margin', 'padding', 'background', 'border', 'box-shadow' ),
				'selector' 		=> $menu_unique_id . ' .cz_parent_megamenu > [class^="cz_megamenu_"] > .cz, .cz_parent_megamenu > [class*=" cz_megamenu_"] > .cz',
				'dependency' 	=> $dependency
			);
			$out[] = array(
				'id' 			=> '_css_menu_ul_a_h6_' . $id,
				'type' 			=> 'cz_sk',
				'button' 		=> esc_html__( 'Megamenu columns title', 'codevz' ),
				'setting_args' 	=> array( 'transport' => 'postMessage' ),
				'settings' 		=> array( 'color', 'background', 'font-family', 'text-align', 'font-size', 'padding', 'margin', 'border' ),
				'selector' 		=> $menu_unique_id . ' .cz .cz h6',
				'dependency' 	=> $dependency
			);

			return $out;
		}

		/**
		 *
		 * Generate json of options for customize footer and live changes
		 * 
		 * @return string
		 *
		 */
		public static function codevz_wp_footer_options_json() {
			$out = array();

			foreach ( Codevz_Plus::option() as $id => $val ) {
				if ( ! empty( $val ) && Codevz_Plus::contains( $id, '_css_' ) ) {
					$out[ $id ] = $val;
				}
			}

			wp_add_inline_script( 'codevz-customize', 'var codevz_selectors = ' . json_encode( (array) self::get_selector( 'all' ) ) . ', codevz_customize_json = ' . json_encode( $out ) . ';', 'before' );
		}

		/**
		 *
		 * Get sidebars
		 * 
		 * @return string
		 *
		 */
		public static function sidebars() {
			$options = array( '' => esc_html__( 'Default', 'codevz' ) );
			$sidebars = (array) get_option( 'sidebars_widgets' );
			foreach ( $sidebars as $i => $w ) {
				if ( isset( $i ) && ( $i !== 'array_version' && $i !== 'jr-insta-shortcodes' && $i !== 'wp_inactive_widgets' ) ) {
					$options[ $i ] = ucwords( $i );
				}
			}

			return $options;
		}

		/**
		 *
		 * Get list of Revolution Sliders
		 * 
		 * @return string
		 *
		 */
		public static function revSlider( $o = array() ) {
			if ( class_exists( 'RevSliderAdmin' ) ) {
				$s  = new RevSlider();
				$o = array();
				foreach ( (array) $s->getAllSliderAliases() as $id => $s ) {
					if ( ! empty( $s ) ) {
						$o[ $s ] = $s;
					}
				}
				if ( empty( $o ) ) {
					$o = array( esc_html__('Not found, Please create new from Revolution Slider menu', 'codevz') );
				}
			} else {
				$o = array( esc_html__('Sorry! Revolution Slider is not installed or activated', 'codevz') );
			}

			return $o;
		}

		/**
		 *
		 * Reset all header settings
		 * 
		 * @return -
		 *
		 */
		public static function reset_header( $c = 0 ) {
		  $required = array(
			'social_hover_fx' => '',
			'social_color_mode' => '',
			'social_inline_title' => '',
			'social_tooltip' => '',
			'_css_social' => '',
			'_css_social_tablet' => '',
			'_css_social_mobile' => '',
			'_css_social_a' => '',
			'_css_social_a_tablet' => '',
			'_css_social_a_mobile' => '',
			'_css_social_a_hover' => '',
			'_css_social_a_hover_tablet' => '',
			'_css_social_a_hover_mobile' => '',
			'_css_social_inline_titles' => '',
			'_css_social_inline_titles_tablet' => '',
			'_css_social_inline_titles_mobile' => '',
			'_css_social_tooltip' => '',
			'_css_social_tooltip_tablet' => '',
			'_css_social_tooltip_mobile' => '',
			'row_type_header_1' => '',
			'page_as_row_header_1' => '',
			'header_1_left' => '',
			'header_1_center' => '',
			'header_1_right' => '',
			'_css_container_header_1' => '',
			'_css_container_header_1_tablet' => '',
			'_css_container_header_1_mobile' => '',
			'_css_row_header_1' => '',
			'_css_row_header_1_tablet' => '',
			'_css_row_header_1_mobile' => '',
			'_css_header_1_left' => '',
			'_css_header_1_left_tablet' => '',
			'_css_header_1_left_mobile' => '',
			'_css_header_1_center' => '',
			'_css_header_1_center_tablet' => '',
			'_css_header_1_center_mobile' => '',
			'_css_header_1_right' => '',
			'_css_header_1_right_tablet' => '',
			'_css_header_1_right_mobile' => '',
			'_css_menu_container_header_1' => '',
			'_css_menu_container_header_1_tablet' => '',
			'_css_menu_container_header_1_mobile' => '',
			'_css_menu_li_header_1' => '',
			'_css_menu_li_header_1_tablet' => '',
			'_css_menu_li_header_1_mobile' => '',
			'_css_menu_a_header_1' => '',
			'_css_menu_a_header_1_tablet' => '',
			'_css_menu_a_header_1_mobile' => '',
			'_css_menu_a_hover_header_1' => '',
			'_css_menu_a_hover_header_1_tablet' => '',
			'_css_menu_a_hover_header_1_mobile' => '',
			'_css_menu_a_hover_before_header_1' => '',
			'_css_menu_a_hover_before_header_1_tablet' => '',
			'_css_menu_a_hover_before_header_1_mobile' => '',
			'_css_menu_indicator_a_header_1' => '',
			'_css_menu_a_indicator_header_1_tablet' => '',
			'_css_menu_a_indicator_header_1_mobile' => '',
			'_css_menus_separator_header_1' => '',
			'_css_menus_separator_header_1_tablet' => '',
			'_css_menus_separator_header_1_mobile' => '',
			'_css_menu_ul_header_1' => '',
			'_css_menu_ul_header_1_tablet' => '',
			'_css_menu_ul_header_1_mobile' => '',
			'_css_menu_ul_a_header_1' => '',
			'_css_menu_ul_a_header_1_tablet' => '',
			'_css_menu_ul_a_header_1_mobile' => '',
			'_css_menu_ul_a_hover_header_1' => '',
			'_css_menu_ul_a_hover_header_1_tablet' => '',
			'_css_menu_ul_a_hover_header_1_mobile' => '',
			'_css_menu_ul_indicator_a_header_1' => '',
			'_css_menu_ul_indicator_a_header_1_tablet' => '',
			'_css_menu_ul_indicator_a_header_1_mobile' => '',
			'_css_menu_ul_ul_header_1' => '',
			'_css_menu_ul_ul_header_1_tablet' => '',
			'_css_menu_ul_ul_header_1_mobile' => '',
			'_css_menu_inner_megamenu_header_1' => '',
			'_css_menu_inner_megamenu_header_1_tablet' => '',
			'_css_menu_inner_megamenu_header_1_mobile' => '',
			'row_type_header_2' => '',
			'page_as_row_header_2' => '',
			'header_2_left' => '',
			'header_2_center' => '',
			'header_2_right' => '',
			'_css_container_header_2' => '',
			'_css_container_header_2_tablet' => '',
			'_css_container_header_2_mobile' => '',
			'_css_row_header_2' => '',
			'_css_row_header_2_tablet' => '',
			'_css_row_header_2_mobile' => '',
			'_css_header_2_left' => '',
			'_css_header_2_left_tablet' => '',
			'_css_header_2_left_mobile' => '',
			'_css_header_2_center' => '',
			'_css_header_2_center_tablet' => '',
			'_css_header_2_center_mobile' => '',
			'_css_header_2_right' => '',
			'_css_header_2_right_tablet' => '',
			'_css_header_2_right_mobile' => '',
			'_css_menu_container_header_2' => '',
			'_css_menu_container_header_2_tablet' => '',
			'_css_menu_container_header_2_mobile' => '',
			'_css_menu_li_header_2' => '',
			'_css_menu_li_header_2_tablet' => '',
			'_css_menu_li_header_2_mobile' => '',
			'_css_menu_a_header_2' => '',
			'_css_menu_a_header_2_tablet' => '',
			'_css_menu_a_header_2_mobile' => '',
			'_css_menu_a_hover_header_2' => '',
			'_css_menu_a_hover_header_2_tablet' => '',
			'_css_menu_a_hover_header_2_mobile' => '',
			'_css_menu_a_hover_before_header_2' => '',
			'_css_menu_a_hover_before_header_2_tablet' => '',
			'_css_menu_a_hover_before_header_2_mobile' => '',
			'_css_menu_indicator_a_header_2' => '',
			'_css_menu_a_indicator_header_2_tablet' => '',
			'_css_menu_a_indicator_header_2_mobile' => '',
			'_css_menus_separator_header_2' => '',
			'_css_menus_separator_header_2_tablet' => '',
			'_css_menus_separator_header_2_mobile' => '',
			'_css_menu_ul_header_2' => '',
			'_css_menu_ul_header_2_tablet' => '',
			'_css_menu_ul_header_2_mobile' => '',
			'_css_menu_ul_a_header_2' => '',
			'_css_menu_ul_a_header_2_tablet' => '',
			'_css_menu_ul_a_header_2_mobile' => '',
			'_css_menu_ul_a_hover_header_2' => '',
			'_css_menu_ul_a_hover_header_2_tablet' => '',
			'_css_menu_ul_a_hover_header_2_mobile' => '',
			'_css_menu_ul_indicator_a_header_2' => '',
			'_css_menu_ul_indicator_a_header_2_tablet' => '',
			'_css_menu_ul_indicator_a_header_2_mobile' => '',
			'_css_menu_ul_ul_header_2' => '',
			'_css_menu_ul_ul_header_2_tablet' => '',
			'_css_menu_ul_ul_header_2_mobile' => '',
			'_css_menu_inner_megamenu_header_2' => '',
			'_css_menu_inner_megamenu_header_2_tablet' => '',
			'_css_menu_inner_megamenu_header_2_mobile' => '',
			'row_type_header_3' => '',
			'page_as_row_header_3' => '',
			'header_3_left' => '',
			'header_3_center' => '',
			'header_3_right' => '',
			'_css_container_header_3' => '',
			'_css_container_header_3_tablet' => '',
			'_css_container_header_3_mobile' => '',
			'_css_row_header_3' => '',
			'_css_row_header_3_tablet' => '',
			'_css_row_header_3_mobile' => '',
			'_css_header_3_left' => '',
			'_css_header_3_left_tablet' => '',
			'_css_header_3_left_mobile' => '',
			'_css_header_3_center' => '',
			'_css_header_3_center_tablet' => '',
			'_css_header_3_center_mobile' => '',
			'_css_header_3_right' => '',
			'_css_header_3_right_tablet' => '',
			'_css_header_3_right_mobile' => '',
			'_css_menu_container_header_3' => '',
			'_css_menu_container_header_3_tablet' => '',
			'_css_menu_container_header_3_mobile' => '',
			'_css_menu_li_header_3' => '',
			'_css_menu_li_header_3_tablet' => '',
			'_css_menu_li_header_3_mobile' => '',
			'_css_menu_a_header_3' => '',
			'_css_menu_a_header_3_tablet' => '',
			'_css_menu_a_header_3_mobile' => '',
			'_css_menu_a_hover_header_3' => '',
			'_css_menu_a_hover_header_3_tablet' => '',
			'_css_menu_a_hover_header_3_mobile' => '',
			'_css_menu_a_hover_before_header_3' => '',
			'_css_menu_a_hover_before_header_3_tablet' => '',
			'_css_menu_a_hover_before_header_3_mobile' => '',
			'_css_menu_indicator_a_header_3' => '',
			'_css_menu_a_indicator_header_3_tablet' => '',
			'_css_menu_a_indicator_header_3_mobile' => '',
			'_css_menus_separator_header_3' => '',
			'_css_menus_separator_header_3_tablet' => '',
			'_css_menus_separator_header_3_mobile' => '',
			'_css_menu_ul_header_3' => '',
			'_css_menu_ul_header_3_tablet' => '',
			'_css_menu_ul_header_3_mobile' => '',
			'_css_menu_ul_a_header_3' => '',
			'_css_menu_ul_a_header_3_tablet' => '',
			'_css_menu_ul_a_header_3_mobile' => '',
			'_css_menu_ul_a_hover_header_3' => '',
			'_css_menu_ul_a_hover_header_3_tablet' => '',
			'_css_menu_ul_a_hover_header_3_mobile' => '',
			'_css_menu_ul_indicator_a_header_3' => '',
			'_css_menu_ul_indicator_a_header_3_tablet' => '',
			'_css_menu_ul_indicator_a_header_3_mobile' => '',
			'_css_menu_ul_ul_header_3' => '',
			'_css_menu_ul_ul_header_3_tablet' => '',
			'_css_menu_ul_ul_header_3_mobile' => '',
			'_css_menu_inner_megamenu_header_3' => '',
			'_css_menu_inner_megamenu_header_3_tablet' => '',
			'_css_menu_inner_megamenu_header_3_mobile' => '',
			'sticky_header' => '',
			'smart_sticky' => '',
			'header_5_left' => '',
			'header_5_center' => '',
			'header_5_right' => '',
			'_css_container_header_5' => '',
			'_css_container_header_5_tablet' => '',
			'_css_container_header_5_mobile' => '',
			'_css_row_header_5' => '',
			'_css_row_header_5_tablet' => '',
			'_css_row_header_5_mobile' => '',
			'_css_header_5_left' => '',
			'_css_header_5_left_tablet' => '',
			'_css_header_5_left_mobile' => '',
			'_css_header_5_center' => '',
			'_css_header_5_center_tablet' => '',
			'_css_header_5_center_mobile' => '',
			'_css_header_5_right' => '',
			'_css_header_5_right_tablet' => '',
			'_css_header_5_right_mobile' => '',
			'_css_menu_container_header_5' => '',
			'_css_menu_container_header_5_tablet' => '',
			'_css_menu_container_header_5_mobile' => '',
			'_css_menu_li_header_5' => '',
			'_css_menu_li_header_5_tablet' => '',
			'_css_menu_li_header_5_mobile' => '',
			'_css_menu_a_header_5' => '',
			'_css_menu_a_header_5_tablet' => '',
			'_css_menu_a_header_5_mobile' => '',
			'_css_menu_a_hover_header_5' => '',
			'_css_menu_a_hover_header_5_tablet' => '',
			'_css_menu_a_hover_header_5_mobile' => '',
			'_css_menu_a_hover_before_header_5' => '',
			'_css_menu_a_hover_before_header_5_tablet' => '',
			'_css_menu_a_hover_before_header_5_mobile' => '',
			'_css_menu_indicator_a_header_5' => '',
			'_css_menu_a_indicator_header_5_tablet' => '',
			'_css_menu_a_indicator_header_5_mobile' => '',
			'_css_menus_separator_header_5' => '',
			'_css_menus_separator_header_5_tablet' => '',
			'_css_menus_separator_header_5_mobile' => '',
			'_css_menu_ul_header_5' => '',
			'_css_menu_ul_header_5_tablet' => '',
			'_css_menu_ul_header_5_mobile' => '',
			'_css_menu_ul_a_header_5' => '',
			'_css_menu_ul_a_header_5_tablet' => '',
			'_css_menu_ul_a_header_5_mobile' => '',
			'_css_menu_ul_a_hover_header_5' => '',
			'_css_menu_ul_a_hover_header_5_tablet' => '',
			'_css_menu_ul_a_hover_header_5_mobile' => '',
			'_css_menu_ul_indicator_a_header_5' => '',
			'_css_menu_ul_indicator_a_header_5_tablet' => '',
			'_css_menu_ul_indicator_a_header_5_mobile' => '',
			'_css_menu_ul_ul_header_5' => '',
			'_css_menu_ul_ul_header_5_tablet' => '',
			'_css_menu_ul_ul_header_5_mobile' => '',
			'_css_menu_inner_megamenu_header_5' => '',
			'_css_menu_inner_megamenu_header_5_tablet' => '',
			'_css_menu_inner_megamenu_header_5_mobile' => '',
			'row_type_header_4' => '',
			'page_as_row_header_4' => '',
			'mobile_header' => '',
			'mobile_sticky' => '',
			'header_4_left' => '',
			'header_4_center' => '',
			'header_4_right' => '',
			'_css_container_header_4' => '',
			'_css_container_header_4_tablet' => '',
			'_css_container_header_4_mobile' => '',
			'_css_row_header_4' => '',
			'_css_row_header_4_tablet' => '',
			'_css_row_header_4_mobile' => '',
			'_css_header_4_left' => '',
			'_css_header_4_left_tablet' => '',
			'_css_header_4_left_mobile' => '',
			'_css_header_4_center' => '',
			'_css_header_4_center_tablet' => '',
			'_css_header_4_center_mobile' => '',
			'_css_header_4_right' => '',
			'_css_header_4_right_tablet' => '',
			'_css_header_4_right_mobile' => '',
			'_css_menu_container_header_4' => '',
			'_css_menu_container_header_4_tablet' => '',
			'_css_menu_container_header_4_mobile' => '',
			'_css_menu_li_header_4' => '',
			'_css_menu_li_header_4_tablet' => '',
			'_css_menu_li_header_4_mobile' => '',
			'_css_menu_a_header_4' => '',
			'_css_menu_a_header_4_tablet' => '',
			'_css_menu_a_header_4_mobile' => '',
			'_css_menu_a_hover_header_4' => '',
			'_css_menu_a_hover_header_4_tablet' => '',
			'_css_menu_a_hover_header_4_mobile' => '',
			'_css_menu_a_hover_before_header_4' => '',
			'_css_menu_a_hover_before_header_4_tablet' => '',
			'_css_menu_a_hover_before_header_4_mobile' => '',
			'_css_menu_indicator_a_header_4' => '',
			'_css_menu_a_indicator_header_4_tablet' => '',
			'_css_menu_a_indicator_header_4_mobile' => '',
			'_css_menus_separator_header_4' => '',
			'_css_menus_separator_header_4_tablet' => '',
			'_css_menus_separator_header_4_mobile' => '',
			'_css_menu_ul_header_4' => '',
			'_css_menu_ul_header_4_tablet' => '',
			'_css_menu_ul_header_4_mobile' => '',
			'_css_menu_ul_a_header_4' => '',
			'_css_menu_ul_a_header_4_tablet' => '',
			'_css_menu_ul_a_header_4_mobile' => '',
			'_css_menu_ul_a_hover_header_4' => '',
			'_css_menu_ul_a_hover_header_4_tablet' => '',
			'_css_menu_ul_a_hover_header_4_mobile' => '',
			'_css_menu_ul_indicator_a_header_4' => '',
			'_css_menu_ul_indicator_a_header_4_tablet' => '',
			'_css_menu_ul_indicator_a_header_4_mobile' => '',
			'_css_menu_ul_ul_header_4' => '',
			'_css_menu_ul_ul_header_4_tablet' => '',
			'_css_menu_ul_ul_header_4_mobile' => '',
			'_css_menu_inner_megamenu_header_4' => '',
			'_css_menu_inner_megamenu_header_4_tablet' => '',
			'_css_menu_inner_megamenu_header_4_mobile' => '',
			'fixed_side' => '',
			'row_type_fixed_side_1' => '',
			'page_as_row_fixed_side_1' => '',
			'fixed_side_1_top' => '',
			'fixed_side_1_middle' => '',
			'fixed_side_1_bottom' => '',
			'_css_fixed_side_style' => '',
			'_css_fixed_side_style_tablet' => '',
			'_css_fixed_side_style_mobile' => '',
			'_css_fixed_side_1_top' => '',
			'_css_fixed_side_1_top_tablet' => '',
			'_css_fixed_side_1_top_mobile' => '',
			'_css_fixed_side_1_middle' => '',
			'_css_fixed_side_1_middle_tablet' => '',
			'_css_fixed_side_1_middle_mobile' => '',
			'_css_fixed_side_1_bottom' => '',
			'_css_fixed_side_1_bottom_tablet' => '',
			'_css_fixed_side_1_bottom_mobile' => '',
			'_css_menu_container_fixed_side_1' => '',
			'_css_menu_container_fixed_side_1_tablet' => '',
			'_css_menu_container_fixed_side_1_mobile' => '',
			'_css_menu_li_fixed_side_1' => '',
			'_css_menu_li_fixed_side_1_tablet' => '',
			'_css_menu_li_fixed_side_1_mobile' => '',
			'_css_menu_a_fixed_side_1' => '',
			'_css_menu_a_fixed_side_1_tablet' => '',
			'_css_menu_a_fixed_side_1_mobile' => '',
			'_css_menu_a_hover_fixed_side_1' => '',
			'_css_menu_a_hover_fixed_side_1_tablet' => '',
			'_css_menu_a_hover_fixed_side_1_mobile' => '',
			'_css_menu_a_hover_before_fixed_side_1' => '',
			'_css_menu_a_hover_before_fixed_side_1_tablet' => '',
			'_css_menu_a_hover_before_fixed_side_1_mobile' => '',
			'_css_menu_indicator_a_fixed_side_1' => '',
			'_css_menu_a_indicator_fixed_side_1_tablet' => '',
			'_css_menu_a_indicator_fixed_side_1_mobile' => '',
			'_css_menus_separator_fixed_side_1' => '',
			'_css_menus_separator_fixed_side_1_tablet' => '',
			'_css_menus_separator_fixed_side_1_mobile' => '',
			'_css_menu_ul_fixed_side_1' => '',
			'_css_menu_ul_fixed_side_1_tablet' => '',
			'_css_menu_ul_fixed_side_1_mobile' => '',
			'_css_menu_ul_a_fixed_side_1' => '',
			'_css_menu_ul_a_fixed_side_1_tablet' => '',
			'_css_menu_ul_a_fixed_side_1_mobile' => '',
			'_css_menu_ul_a_hover_fixed_side_1' => '',
			'_css_menu_ul_a_hover_fixed_side_1_tablet' => '',
			'_css_menu_ul_a_hover_fixed_side_1_mobile' => '',
			'_css_menu_ul_indicator_a_fixed_side_1' => '',
			'_css_menu_ul_indicator_a_fixed_side_1_tablet' => '',
			'_css_menu_ul_indicator_a_fixed_side_1_mobile' => '',
			'_css_menu_ul_ul_fixed_side_1' => '',
			'_css_menu_ul_ul_fixed_side_1_tablet' => '',
			'_css_menu_ul_ul_fixed_side_1_mobile' => '',
			'_css_menu_inner_megamenu_fixed_side_1' => '',
			'_css_menu_inner_megamenu_fixed_side_1_tablet' => '',
			'_css_menu_inner_megamenu_fixed_side_1_mobile' => '',
			'_css_header_container' => '',
			'_css_header_container_tablet' => '',
			'_css_header_container_mobile' => '',
			'hidden_top_bar' => '',
			'_css_hidden_top_bar' => '',
			'_css_hidden_top_bar_tablet' => '',
			'_css_hidden_top_bar_mobile' => '',
			'_css_hidden_top_bar_handle' => '',
			'_css_hidden_top_bar_handle_tablet' => '',
			'_css_hidden_top_bar_handle_mobile' => '',
		  );

		  if ( $c ) {
			$updated = wp_parse_args( $required, Codevz_Plus::option() );
			update_option( Codevz_Plus::$options_id, $updated );
		  }

		  return $required;
		}

	}

	Codevz_Options::instance();
}
