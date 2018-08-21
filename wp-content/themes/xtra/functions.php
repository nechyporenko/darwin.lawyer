<?php 
/**
 *
 * Theme core functions and definitions
 * 
 * If you need to override theme functions, Check theme documentation
 * that is included in the theme package you've downlaoded from themeforest.
 * 
 * @since 1.0
 * 
 */
if ( ! class_exists( 'Codevz' ) ) {
	
	class Codevz {

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
		 * Post meta ID
		 *
		 * @access private
		 * @var string
		 *
		 */
		private static $meta_id = 'codevz_page_meta';

		/**
		 *
		 * Theme options ID
		 *
		 * @access private
		 * @var string
		 *
		 */
		private static $options_id = 'codevz_theme_options';

		/**
		 *
		 * Other definitions
		 *
		 * @var $elm_id = increase ID for header elements
		 * @var $post 	= global current post object
		 * @var $is_woo = check if woocommerceis activated
		 * @var $dir 	= get theme directory path
		 *
		 */
		public static $elm_id = 0, $post, $is_woo, $dir, $is_rtl;

		public function __construct() {

			self::$post 	= &$GLOBALS['post'];
			self::$is_woo 	= function_exists( 'is_woocommerce' );
			self::$dir 		= trailingslashit( get_template_directory() );
			self::$is_rtl 	= ( is_rtl() || self::option( 'rtl' ) || isset( $_GET['rtl'] ) );

			// Required files
			require_once( self::$dir . 'config/tgmpa.php' );
			$path = '';
			if ( function_exists( 'get_blog_details' ) ) {
				$path = get_blog_details();
				$path = self::$dir . 'config/config-' . substr( $path->home, strrpos( $path->home, '/' ) + 1 ) . '.php';
			}
			if ( file_exists( $path ) ) {
				require_once( $path );
			} else {
				require_once( self::$dir . 'config/config.php' );
			}

			// Demo importer
			add_filter( 'codevz_demos', function() {
				return array(
					'slug'		=> codevz_theme_config( 'textdomain' ),
					'path' 		=> codevz_theme_config( 'server' ),
					'demos' 	=> codevz_theme_config( 'demos' )
				);
			});

			// Actions
			add_action( 'tgmpa_register', array( __CLASS__, 'plugins' ) );
			add_action( 'after_setup_theme', array( __CLASS__, 'theme_setup' ) );
			add_action( 'after_switch_theme', array( __CLASS__, 'import_settings' ) );
			add_action( 'widgets_init', array( __CLASS__, 'register_sidebars' ) );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_assets' ) );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_dynamic_css' ), 99 );
			add_action( 'nav_menu_css_class', array( __CLASS__, 'menu_current_class' ), 10, 2 );
			add_action( 'wp_ajax_codevz_ajax_search', array( __CLASS__, 'codevz_ajax_search' ) );
			add_action( 'wp_ajax_nopriv_codevz_ajax_search', array( __CLASS__, 'codevz_ajax_search' ) );
			add_action( 'wp_ajax_codevz_selective_refresh', array( __CLASS__, 'codevz_row_inner' ) );
			add_action( 'wp_ajax_nopriv_codevz_selective_refresh', array( __CLASS__, 'codevz_row_inner' ) );
			add_action( 'pre_get_posts', array( __CLASS__, 'action_pre_get_posts' ), 99 );
			add_action( 'wp_head', array( __CLASS__, 'wp_head' ) );

			// Filters
			add_filter( 'excerpt_length', array( __CLASS__, 'excerpt_length' ), 999 );
			add_filter( 'excerpt_more', array( __CLASS__, 'excerpt_more' ) );
			add_filter( 'get_the_excerpt', array( __CLASS__, 'get_the_excerpt' ), 21 );
			
			// WooCommerce
			if ( self::$is_woo ) {
				add_filter( 'woocommerce_add_to_cart_fragments', array( __CLASS__, 'woo_cart' ) );
				add_filter( 'loop_shop_columns', array( __CLASS__, 'woo_col' ), 999 );
				add_filter( 'woocommerce_product_query', array( __CLASS__, 'woo_ppp' ), 999 );
				add_filter( 'woocommerce_output_related_products_args', array( __CLASS__, 'woo_related' ), 999 );
			}
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
		 * Import default theme options
		 * 
		 * @return array
		 *
		 */
		public static function import_settings() {
			$vk = codevz_theme_config( 'version_key' );
			$id = self::$options_id;

			if ( $vk && ! get_option( $id . '_' . $vk ) ) {
				update_option( $id, codevz_default_options() );
				update_option( $id . '_' . $vk, 1 );
			} else if ( empty( get_option( $id ) ) ) {
				update_option( $id, codevz_default_options() );
			}
		}

		/**
		 *
		 * TGM plugins activation
		 * 
		 * @return array|string|null
		 *
		 */
		public static function plugins() {
			$plugins = codevz_theme_config( 'plugins' );

			tgmpa( $plugins, array(
				'id'          		=> 'tgmpa',
				'default_path'      => '', 
				'menu'          	=> 'tgmpa-install-plugins',
				'parent_slug'       => 'themes.php',
				'capability'      	=> 'edit_theme_options',
				'has_notices'       => true,
				'dismissable'       => true,
				'dismiss_msg'       => '',
				'is_automatic'      => false, 
				'message'         	=> ''
			));
		}

		/**
		 *
		 * Get meta box for any page
		 * 
		 * @return array|string|null
		 *
		 */
		public static function meta( $id = 0, $key = 0, $sub = 0 ) {
			$id = $id ? $id : ( isset( self::$post->ID ) ? self::$post->ID : 0 );
			$key = $key ? $key : self::$meta_id;
			$meta = (array) get_post_meta( $id, $key, true );

			if ( $sub ) {
				return isset( $meta[ $sub ] ) ? $meta[ $sub ] : 0;
			} else {
				return $id ? $meta : '';
			}
		}

		/**
		 *
		 * Get option from customize page
		 * 
		 * @return array|string|null
		 *
		 */
		public static function option( $key = '', $default = '' ) {
			$all = (array) get_option( self::$options_id );

			// Overide options
			if ( isset( $_GET['o'] ) ) {
				foreach ( $_GET['o'] as $o => $v ) {
					if ( ! is_array( $v ) ) {
						$all[ $o ] = esc_attr( str_replace( '.', '!', $v ) );
					}
				}
			} else if ( isset( $_GET['ajax'] ) ) {
				$all[ 'ajax' ] = 1;
			} else if ( isset( $_GET['rtl'] ) ) {
				$all[ 'rtl' ] = 1;
			}

			return $key ? ( empty( $all[ $key ] ) ? $default : $all[ $key ] ) : $all;
		}

		/**
		 *
		 * After setup theme
		 * 
		 * @return object
		 *
		 */
		public static function theme_setup() {

			// Menu location, others registered by plugin
			register_nav_menus( array( 'primary' => 'Primary' ) );

			// Theme Supports
			add_theme_support( 'title-tag' );
			add_theme_support( 'html5' );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'woocommerce' );
			add_theme_support( 'wc-product-gallery-slider' );
			add_theme_support( 'bbpress' );

			// Images
			add_image_size( 'codevz_360_320', 360, 320, true ); 	// Medium
			add_image_size( 'codevz_600_600', 600, 600, true ); 	// Square
			add_image_size( 'codevz_1200_200', 1200, 200, true ); 	// CPT Full
			add_image_size( 'codevz_1200_500', 1200, 500, true ); 	// CPT Full
			add_image_size( 'codevz_600_1000', 600, 1000, true ); 	// Vertical
			add_image_size( 'codevz_600_9999', 600, 9999 ); 		// Masonry

			// Content Width
			if ( ! isset( $content_width ) ) {
				$content_width = (int) self::option( 'site_width', 1170 );
			}

			// Languages
			$textdomain = codevz_theme_config( 'textdomain' );
			load_theme_textdomain( $textdomain, self::$dir . 'lang' );
		}

		/**
		 *
		 * Front-end assets
		 * 
		 * @return string
		 *
		 */
		public static function load_assets() {
			if ( ! isset( $_POST['vc_inline'] ) ) {
				$uri = get_template_directory_uri();

				// JS
				wp_enqueue_script( 'codevz-custom', $uri . '/js/custom.js', array( 'jquery' ), '', true );
				if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
					wp_enqueue_script( 'comment-reply' );
				}
				if ( self::option( 'ajax' ) ) {
					wp_enqueue_style( 'js_composer_front' );
					wp_enqueue_script( 'codevz-ajax', $uri . '/js/ajax.js', array( 'jquery' ), '', true );
				}

				// Custom JS
				$js = self::option( 'js' );
				if ( $js ) {
					wp_add_inline_script( 'codevz-custom', 'jQuery(document).ready(function($) {' . esc_js( $js ) . '});' );
				}

				// Styles
				wp_enqueue_style( 'codevz-style', get_stylesheet_uri() );
				wp_enqueue_style( 'font-awesome', $uri . '/icons/font-awesome.min.css', array(), '4.7', 'all' );
			}
		}

		/**
		 *
		 * Load dynamic style as a file or inline
		 * 
		 * @return string
		 *
		 */
		public static function load_dynamic_css() {
			if ( ! isset( $_POST['vc_inline'] ) ) {
				// Custom styles
				$handle = wp_style_is( 'codevz-plugin' ) ? 'codevz-plugin' : 'codevz-style';
				$extra_css = '';

				// Woocommerce
				if ( self::$is_woo ) {
					$extra_css .= "/* Woo */" . '.woocommerce ul.products li.product, .woocommerce-page ul.products li.product{text-align: center}.woo-col-2.woocommerce ul.products li.product, .woo-col-2.woocommerce-page ul.products li.product, .woo-related-col-2.woocommerce ul.products .related li.product, .woo-related-col-2.woocommerce-page ul.products .related li.product {width: 48.05%}.woo-col-3.woocommerce ul.products li.product, .woo-col-3.woocommerce-page ul.products li.product, .woo-related-col-3.woocommerce ul.products .related li.product, .woo-related-col-3.woocommerce-page ul.products .related li.product {width: calc(100% / 3 - 2.6%)}.woo-col-5.woocommerce ul.products li.product, .woo-col-5.woocommerce-page ul.products li.product {width: calc(100% / 5 - 3.2%)}.woo-col-6.woocommerce ul.products li.product, .woo-col-6.woocommerce-page ul.products li.product {width: calc(100% / 6 - 3.2%)}.rtl .woocommerce-error,.rtl .woocommerce-info,.rtl .woocommerce-message{padding:15px 70px !important;margin:0 0 30px !important}.quantity{position:relative}input[type=number]::-webkit-inner-spin-button,input[type=number]::-webkit-outer-spin-button{-webkit-appearance:none;margin:0}input[type=number]{-moz-appearance:textfield}.quantity input{width:45px;height:42px;line-height:1.65;float:left;display:block;padding:0;margin:0;padding-left:20px;border:1px solid rgba(167, 167, 167, 0.3)}.quantity input:focus{outline:0}.quantity-nav{float:left;position:relative;height:41px;margin:0 0 0 -11px}.rtl .quantity-nav{float:left;margin:0 0 0 25px}.quantity-button{position:relative;cursor:pointer;border-left:1px solid rgba(167, 167, 167, 0.3);width:25px;text-align:center;color:inherit;font-size:14px;line-height:1.5;transform:translateX(-100%)}.quantity-button.quantity-up{position:absolute;height:50%;top:0;border-bottom:1px solid rgba(167, 167, 167, 0.3)}.quantity-button.quantity-down{position:absolute;bottom:-1px;height:50%}.woocommerce .quantity .qty {margin:0 10px 0 0;padding: 10px 16px !important;width: 80px;text-align:left}.rtl .woocommerce .quantity .qty{margin:0 0 0 10px}.woocommerce-Tabs-panel h2 {display: none !important}.woocommerce-checkout #payment ul.payment_methods li img{display:inline-block}.woocommerce nav.woocommerce-pagination ul li{border: 0 !important;overflow:visible}.woocommerce a.remove{border-radius:2px}.cross-sells{display: none}.post-type-archive-product h1.page-title,.woocommerce #comments.content{display:none}.woocommerce ul.products li.product .star-rating{margin: 10px auto 0}.outofstock .button{display: none !important}#order_review_heading{margin:30px 0 20px}.woocommerce .woocommerce-ordering,.woocommerce .woocommerce-result-count{box-sizing:border-box;margin:0 0 2em}.woocommerce span.onsale,.woocommerce ul.products li.product .onsale{z-index:9;background:#fff;border-radius:100%;display:inline-block;padding:0;position:absolute;top:20px;left:20px;right:auto;margin:0;color:initial;line-height:4em;width:4em;height:4em;font-size:16px;font-weight:600;min-height:initial;box-shadow:0 0 30px rgba(17,17,17,.06)}.woocommerce.single span.onsale, .woocommerce.single ul.products li.product .onsale{left:15px;right:auto}.woocommerce nav.woocommerce-pagination ul li a, .woocommerce nav.woocommerce-pagination ul li span{line-height: 3em}.woocommerce ul.products li.product .button{margin: 20px auto 0;display:table}.woocommerce ul.products li.product .button:before{font-family:FontAwesome;content:"\f07a";position:static;transform:initial;display:inline;background:none !important;margin-right:10px}.woocommerce ul.products li.product .button.loading:after{margin-top:3px}.woocommerce a.added_to_cart{position:absolute;bottom:-28px;left:50%;margin:0;font-size:12px;transform:translateX(-50%);letter-spacing:2px}.woocommerce ul.products li.product .woocommerce-loop-category__title, .woocommerce ul.products li.product .woocommerce-loop-product__title, .woocommerce ul.products li.product h3 {font-size:22px}.woocommerce ul.products li.product:hover .button{opacity:1}.woocommerce ul.products li.product .price{background:#fff;border-radius:30px;display:inline-block;padding:4px 16px;position:absolute;top:20px;right:20px;color:#262626;font-weight:bold}.woocommerce ul.products li.product .price del{font-size:.7em;display:inline-block}.woocommerce .product_meta{font-size:13px}.woocommerce div.product form.cart,.woocommerce div.product p.cart{margin:2em 0}.woocommerce ul.products li.product h3{font-size:16px;width:85%}.woocommerce div.product .woocommerce-tabs .panel{padding:30px;border:1px solid rgba(167,167,167,.2);border-radius:0 2px 2px}.woocommerce div.product .woocommerce-tabs ul.tabs{padding:0 0 0 5px;margin:0 0 -1px}.woocommerce div.product .woocommerce-tabs ul.tabs li{opacity:.6;border:1px solid rgba(167,167,167,.2);background: rgba(167, 167, 167, 0.1);border-radius:2px 2px 0 0;border-bottom:0}.woocommerce div.product .woocommerce-tabs ul.tabs li.active{opacity:1}.woocommerce div.product .woocommerce-tabs ul.tabs:before,.woocommerce nav.woocommerce-pagination ul{border:0}.woocommerce div.product .woocommerce-tabs ul.tabs li.active:after,.woocommerce div.product .woocommerce-tabs ul.tabs li.active:before{box-shadow:none;display:none}.woocommerce table.shop_table td{padding:16px 20px}.woocommerce table.shop_table th{padding: 20px}#add_payment_method #payment,.woocommerce-cart #payment,.woocommerce-checkout #payment{background:0 0;padding:10px}#add_payment_method #payment ul.payment_methods,.woocommerce-cart #payment ul.payment_methods,.woocommerce-checkout #payment ul.payment_methods{border-bottom:1px solid rgba(167,167,167,.2)}.woocommerce-error,.woocommerce-info,.woocommerce-message{line-height:40px;background-color:rgba(167,167,167,.1);border:0;padding:22px 60px !important;margin:0 0 30px !important}td.product-subtotal,td.product-total,tr.cart-subtotal td{font-size:14px}tr.order-total td{font-size:18px;font-weight:700}.woocommerce ul.products li.product .price ins{text-decoration:none}.woocommerce nav.woocommerce-pagination ul li a:focus,.woocommerce nav.woocommerce-pagination ul li a:hover,.woocommerce nav.woocommerce-pagination ul li span.current{color:#fff !important}.woocommerce nav.woocommerce-pagination ul li span.current{border:0}.woocommerce nav.woocommerce-pagination ul li a, .woocommerce nav.woocommerce-pagination ul li span {font-size: 14px !important}#add_payment_method .cart-collaterals .cart_totals table td,#add_payment_method .cart-collaterals .cart_totals table th,.woocommerce-cart .cart-collaterals .cart_totals table td,.woocommerce-cart .cart-collaterals .cart_totals table th,.woocommerce-checkout .cart-collaterals .cart_totals table td,.woocommerce-checkout .cart-collaterals .cart_totals table th{vertical-align:middle}#add_payment_method #payment,.woocommerce form.checkout_coupon,.woocommerce form.login,.woocommerce form.register,.woocommerce-cart #payment,.woocommerce-checkout #payment{border:1px solid rgba(167,167,167,.2);border-radius:0}.woocommerce #coupon_code{padding:12px;width:auto}.woocommerce p #coupon_code{width:100%!important}.woocommerce input.button:disabled,.woocommerce input.button:disabled[disabled]{color:#fff}.woocommerce input.button{padding:12px 30px}#add_payment_method #payment div.payment_box,.woocommerce-cart #payment div.payment_box,.woocommerce-checkout #payment div.payment_box{background-color:rgba(167,167,167,.1)}#add_payment_method #payment div.payment_box:before,.woocommerce-cart #payment div.payment_box:before,.woocommerce-checkout #payment div.payment_box:before{top:-14px;border-bottom-color:rgba(167,167,167,.1)}.woocommerce-thankyou-order-received{font-size:20px;background:#eafff1;color:#17ac4d;padding:20px;border-radius:2px}.woocommerce .product_title{font-size:30px}.woocommerce-product-rating{font-size:12px}.woocommerce ul.order_details li {line-height: 3;margin-right: 3em}.calculated_shipping h2 {font-size: 24px;margin: 0 0 20px;opacity: .4}.related.products li{margin-bottom:0!important}#payment label{display:inline}.about_paypal{margin:0 10px}.showcoupon{font-weight:900}.woocommerce nav.woocommerce-pagination ul li a:focus, .woocommerce nav.woocommerce-pagination ul li a:hover, .woocommerce nav.woocommerce-pagination ul li span.current {background: #353535;color: #8a7e88}.woocommerce-MyAccount-navigation ul {list-style-type: none;margin: 0;padding:0}.edit-account fieldset{margin-bottom:30px}.woocommerce-MyAccount-navigation ul {list-style-type: none;margin: 0}.woocommerce-MyAccount-navigation a,.woocommerce-account ul.digital-downloads li .count {padding: 10px 20px;display: block;background: rgba(167, 167, 167, 0.1);margin: 0 20px 6px 0;border-radius: 2px}.woocommerce-MyAccount-navigation a:hover, .woocommerce-MyAccount-navigation .is-active a {background: rgba(167, 167, 167, 0.2);color: #fff}.edit-account .input.woocommerce-Button.button {margin: 20px 0 0}.woocommerce ul.product_list_widget li img {float: left;margin: 0 20px 0 0;width: 80px}.woocommerce .widget_price_filter .price_slider_wrapper .ui-widget-content{background-color: #e9e9e9}.woocommerce .widget_price_filter .ui-slider .ui-slider-range,.woocommerce .widget_price_filter .ui-slider .ui-slider-handle{background-color:#a7a7a7}.product_meta > span{display:block;margin:0 0 5px}#comments .commentlist li .avatar{padding: 0 !important;border-radius: 100% !important;width: 40px !important;box-shadow: 1px 10px 10px rgba(167, 167, 167, 0.3) !important;border:0 !important;top:25px !important;left:20px !important}.rtl #comments .commentlist li .avatar{left:auto;right:20px}.woocommerce #reviews #comments ol.commentlist li .comment-text{padding: 30px !important;margin: 0 50px}.woocommerce table.shop_table td, .woocommerce-cart .cart-collaterals .cart_totals tr th {border-top: 1px solid rgba(167, 167, 167, 0.2) !important}.product_meta a{font-weight:bold;background:rgba(167, 167, 167, 0.12);padding:0px 8px;border-radius:2px;margin:4px 0;display:inline-block}#add_payment_method table.cart img, .woocommerce-cart table.cart img,.woocommerce-checkout table.cart img{width:80px !important}.cart_totals h2,.woocommerce-additional-fields > h3,.woocommerce-billing-fields > h3,#order_review_heading{font-size:24px;padding:0 0 0 2px}.woocommerce-review-link{display:none}.woocommerce ul.products li.product .woocommerce-loop-product__link{display:block}label.woocommerce-form__label.woocommerce-form__label-for-checkbox.inline{margin: 0 20px}from.woocommerce-product-search input{float: left;width: 61%;margin-right: 5%}from.woocommerce-product-search button{width: 34%;padding: 12px 0}.woocommerce div.product .woocommerce-tabs ul.tabs li.active a{color: #111}.rtl ul.products li.product .button:before{margin-left:10px !important;margin-right:0 !important}.comment-form-rating p:nth-child(3){display:none !important}.woocommerce ul.products li.product a img{max-width: 100% !important;max-height: 100% !important}.woocommerce #respond input#submit.added::after, .woocommerce a.button.added::after, .woocommerce button.button.added::after, .woocommerce input.button.added::after{vertical-align: middle}.rtl .woocommerce div.product form.cart div.quantity{margin:0 -25px 0 20px}.rtl .woocommerce-product-gallery{direction:ltr}.pswp__ui{width:100%;height:100%}.pswp__button--arrow--left, .pswp__button--arrow--right{position:absolute !important}.woocommerce div.product div.images .flex-control-thumbs li{width: calc(100% / 4 - 10px);margin:10px 0 0 10px;padding:0}.woocommerce div.product div.images .flex-control-thumbs{margin-left:-10px}.woocommerce-error::before, .woocommerce-info::before,.woocommerce-message::before{top: 21px}.woocommerce-account .addresses .title .edit{float:right;margin:5px 20px;opacity: .5}.rtl.woocommerce .woocommerce-result-count, .rtl.woocommerce-page .woocommerce-result-count{float: right}.rtl.woocommerce .woocommerce-ordering, .rtl.woocommerce-page .woocommerce-ordering{float: left}.ajax_add_to_cart.loading{padding-right:35px !important}.rtl .ajax_add_to_cart.loading{padding-left:35px !important}';
				}

				// DWQA
				if ( function_exists( 'dwqa' ) ) {
					$extra_css .= "/* DWQA */" . '.dwqa-questions-list {border: 1px solid #ebebeb;margin: 30px 0 0;border-radius: 10px}.dwqa-questions-list .dwqa-question-item {border: 0;border-bottom: 1px solid #ebebeb}.dwqa-questions-list .dwqa-question-item .dwqa-question-stats{margin-top:-24px;right:14px}.dwqa-questions-list .dwqa-question-item .dwqa-question-stats span {margin-top: -4px;min-width: 40px;height: 50px;border: 1px solid #ebebeb;border-radius: 10px;padding: 5px 10px 0;color: #868686}.dwqa-question-filter a {opacity: .6;margin: 0 20px 0 0}.dwqa-question-filter a.active{opacity:1;font-weight:bold}.wp-core-ui .button-group.button-small .button, .wp-core-ui .button.button-small {display: none;width: auto}.wp-switch-editor {cursor: pointer;opacity: .6;margin: 10px 10px 0 0 !important;padding: 0 2px !important;background: none !important;color: #111 !important}.html-active.wp-core-ui .button{display:inline-block !important;margin: 2px}.tmce-active .switch-tmce, .html-active .switch-html{opacity: 1;font-weight: bold}.mce-panel{border: 0 solid rgba(165, 165, 165, 0.2) !important;background-color: #f9f9f9 !important}.dwqa-container p:first-child {margin-bottom: 40px !important}label[for="question_title"]{font-size:20px;font-weight: 500}#wp-question-content-wrap {position: relative}.wp-editor-tabs {position: absolute;right: 0;z-index: 2;top: 0}.dwqa-captcha:before {content:"";clear: both;width: 100%;display: block}.dwqa-captcha {display: block;width: 100%}.dwqa-questions-list .dwqa-question-meta span{opacity: .7}.dwqa-status{opacity: 1 !important}.dwqa-question-title{font-size:18px;font-weight: 500}.dwqa-questions-list .dwqa-question-item .avatar{vertical-align: middle}.dwqa-question-footer .dwqa-question-status{font-size: 0}.dwqa-answers-title{margin:40px 0 10px;font-weight:500;font-size:18px}.dwqa-container p,.quicktags-toolbar{text-align:left}.single-dwqa-question .dwqa-container p{margin:0 0 30px !important;line-height:40px;font-size:18px}.single-dwqa-question .dwqa-container p:last-child{margin-bottom:0 !important}.single-dwqa-question .dwqa-question-item a,.dwqa-answer-item .dwqa-answer-meta a{text-transform: capitalize}.single .dwqa-question-footer {margin-bottom:-10px;margin-top:30px;opacity: .8}.dwqa-answer-form-title{padding:20px 0 14px;font-size:18px;font-weight:500;border:0}.single-dwqa-question .dwqa-question-item, .single-dwqa-question .dwqa-answer-item {background: rgba(167, 167, 167, 0.03);padding: 30px !important;border: 1px solid #ebebeb;border-radius: 10px;margin:0 0 20px 68px}.dwqa-answers-title, .dwqa_delete_question, .dwqa_delete_answer{display: none}.single-dwqa-question .dwqa-question-vote,.single-dwqa-question .dwqa-answer-vote {top: 90px;left:-62px}.single-dwqa-question .dwqa-question-item .avatar,.single-dwqa-question .dwqa-answer-item .avatar {left:-70px;top: 20px;max-width:48px !important}.dwqa-questions-list .dwqa-question-item .avatar{max-width:48px !important}.single-dwqa-question .dwqa-pick-best-answer{left:-64px}.dwqa-question-item .dwqa-status-closed {background: #222 !important;box-shadow:none !important}.dwqa-question-item .dwqa-status-answered, .dwqa-staff {background: #4e71fe !important;box-shadow:none !important}.dwqa-question-item .dwqa-status-open {background: #e67e22 !important;box-shadow:none !important}.dwqa-question-item .dwqa-status-resolved {background: #00cc47 !important;box-shadow:none !important}.dwqa-question-item .dwqa-status-closed:after,.dwqa-question-item .dwqa-status-answered:after,.dwqa-question-item .dwqa-status-open:after,.dwqa-question-item .dwqa-status-resolved:after{color: #fff !important}.dwqa-question-content, .dwqa-answer-content{padding: 0 40px 0 0}.single-dwqa-question .dwqa-question-item,.dwqa-answer-item{min-height:initial !important}.dwqa-ask-question a{padding: 6px 30px !important;font-size: 14px !important}.dwqa-pagination a, .dwqa-pagination span {padding: 1px 12px;border: 1px solid #ebebeb;margin: 30px 2px 0;border-radius: 4px}span.dwqa-page-numbers.dwqa-current{opacity: .6}.dwqa-search .dwqa-autocomplete{box-shadow: 1px 8px 32px rgba(17, 17, 17, 0.12);border-radius:20px;overflow:hidden}.dwqa-search .dwqa-autocomplete li{border-bottom: 1px solid rgba(221, 221, 221, 0.4)}';
				}

				// Dark
				if ( self::option( 'dark' ) ) {
					$extra_css .= "/* Dark */" . 'body{background-color:#222;color:#fff}.layout_1,.layout_2{background:#191919}a,.woocommerce-error, .woocommerce-info, .woocommerce-message{color:#fff}input,textarea,select,.nice-select{color: #000}.sf-menu li li a,.sf-menu .cz > h6{color: #000}.cz_quote_arrow blockquote{background:#272727}.search_style_icon_dropdown .outer_search, .cz_cart_items {background: #000;color: #c0c0c0 !important}.woocommerce div.product .woocommerce-tabs ul.tabs li.active a {color: #111}#bbpress-forums li{background:none!important}#bbpress-forums li.bbp-header,#bbpress-forums li.bbp-header,#bbpress-forums li.bbp-footer{background:#141414!important;color:#FFF;padding:10px 20px!important}.bbp-header a{color:#fff}.subscription-toggle,.favorite-toggle{padding: 1px 20px !important;}span#subscription-toggle{color: #000}#bbpress-forums #bbp-single-user-details #bbp-user-navigation li.current a{background:#1D1E20!important;color:#FFF;opacity:1}#bbpress-forums li.bbp-body ul.forum,#bbpress-forums li.bbp-body ul.topic{padding:10px 20px!important}.bbp-search-form{margin:0 0 12px!important}.bbp-form .submit{margin:0 auto 20px}div.bbp-breadcrumb,div.bbp-topic-tags{line-height:36px}.bbp-breadcrumb-sep{padding:0 6px}#bbpress-forums li.bbp-header ul{font-size:14px}.bbp-forum-title,#bbpress-forums .bbp-topic-title .bbp-topic-permalink{font-size:16px;font-weight:700}#bbpress-forums .bbp-topic-started-by{display:inline-block}#bbpress-forums p.bbp-topic-meta a{margin:0 4px 0 0;display:inline-block}#bbpress-forums p.bbp-topic-meta img.avatar,#bbpress-forums ul.bbp-reply-revision-log img.avatar,#bbpress-forums ul.bbp-topic-revision-log img.avatar,#bbpress-forums div.bbp-template-notice img.avatar,#bbpress-forums .widget_display_topics img.avatar,#bbpress-forums .widget_display_replies img.avatar{margin-bottom:-2px;border:0}span.bbp-admin-links{color:#4F4F4F}span.bbp-admin-links a{color:#7C7C7C}.bbp-topic-revision-log-item *{display:inline-block}#bbpress-forums .bbp-topic-content ul.bbp-topic-revision-log,#bbpress-forums .bbp-reply-content ul.bbp-topic-revision-log,#bbpress-forums .bbp-reply-content ul.bbp-reply-revision-log{border-top:1px dotted #474747;padding:10px 0 0;color:#888282}.bbp-topics,.bbp-replies,.topic{position:relative}#subscription-toggle,#favorite-toggle{float:right;line-height:34px;color:#DFDFDF;display:block;border:1px solid #DFDFDF;padding:0;margin:0;font-size:12px;border:0!important}.bbp-user-subscriptions #subscription-toggle,.bbp-user-favorites #favorite-toggle{position:absolute;top:0;right:0;line-height:20px}.bbp-reply-author br{display:none}#bbpress-forums li{text-align:left}li.bbp-forum-freshness,li.bbp-topic-freshness{width:23%}.bbp-topics-front ul.super-sticky,.bbp-topics ul.super-sticky,.bbp-topics ul.sticky,.bbp-forum-content ul.sticky{background-color:#2C2C2C!important;border-radius:0!important;font-size:1.1em}#bbpress-forums div.odd,#bbpress-forums ul.odd{background-color:#0D0D0D!important}div.bbp-template-notice a{display:inline-block}div.bbp-template-notice a:first-child,div.bbp-template-notice a:last-child{display:inline-block}#bbp_topic_title,#bbp_topic_tags{width:400px}#bbp_stick_topic_select,#bbp_topic_status_select,#display_name{width:200px}#bbpress-forums #bbp-your-profile fieldset span.description{color:#FFF;border:#353535 1px solid;background-color:#222!important;margin:16px 0}#bbpress-forums fieldset.bbp-form{margin-bottom:40px}.bbp-form .quicktags-toolbar{border:1px solid #EBEBEB}.bbp-form .bbp-the-content,#bbpress-forums #description{border-width:1px!important;height:200px!important}#bbpress-forums #bbp-single-user-details{width:100%;float:none;border-bottom:1px solid #080808;box-shadow:0 1px 0 rgba(34,34,34,0.8);margin:0 0 20px;padding:0 0 20px}#bbpress-forums #bbp-user-wrapper h2.entry-title{margin:-2px 0 20px;display:inline-block;border-bottom:1px solid #FF0078}#bbpress-forums #bbp-single-user-details #bbp-user-navigation a{padding:2px 8px}#bbpress-forums #bbp-single-user-details #bbp-user-navigation{display:inline-block}#bbpress-forums #bbp-user-body,.bbp-user-section p{margin:0}.bbp-user-section{margin:0 0 30px}#bbpress-forums #bbp-single-user-details #bbp-user-avatar{margin:0 20px 0 0;width:auto;display:inline-block}#bbpress-forums div.bbp-the-content-wrapper input{width:auto!important}input#bbp_topic_subscription{width:auto;display:inline-block;vertical-align:-webkit-baseline-middle}.widget_display_replies a,.widget_display_topics a{display:inline-block}.widget_display_replies li,.widget_display_forums li,.widget_display_views li,.widget_display_topics li{display:block;border-bottom:1px solid #282828;line-height:32px;position:relative}.widget_display_replies li div,.widget_display_topics li div{font-size:11px}.widget_display_stats dt{display:block;border-bottom:1px solid #282828;line-height:32px;position:relative}.widget_display_stats dd{float:right;margin:-40px 0 0;color:#5F5F5F}#bbpress-forums div.bbp-topic-content code,#bbpress-forums div.bbp-reply-content code,#bbpress-forums div.bbp-topic-content pre,#bbpress-forums div.bbp-reply-content pre{background-color:#FFF;padding:12px 20px;max-width:96%;margin-top:0}#bbpress-forums div.bbp-forum-author img.avatar,#bbpress-forums div.bbp-topic-author img.avatar,#bbpress-forums div.bbp-reply-author img.avatar{border-radius:100%}#bbpress-forums li.bbp-header,#bbpress-forums li.bbp-footer,#bbpress-forums li.bbp-body ul.forum,#bbpress-forums li.bbp-body ul.topic,div.bbp-forum-header,div.bbp-topic-header,div.bbp-reply-header{border-top:1px solid #252525!important}#bbpress-forums ul.bbp-lead-topic,#bbpress-forums ul.bbp-topics,#bbpress-forums ul.bbp-forums,#bbpress-forums ul.bbp-replies,#bbpress-forums ul.bbp-search-results,#bbpress-forums fieldset.bbp-form,#subscription-toggle,#favorite-toggle{border:1px solid #252525!important}#bbpress-forums div.bbp-forum-header,#bbpress-forums div.bbp-topic-header,#bbpress-forums div.bbp-reply-header{background-color:#1A1A1A!important}#bbpress-forums div.even,#bbpress-forums ul.even{background-color:#161616!important}.bbp-view-title{display:block}div.fixed_contact,i.backtotop,i.fixed_contact,.ajax_search_results{background:#151515}.nice-select{background-color:#fff;color:#000}.nice-select .list{background:#fff}.woocommerce div.product .woocommerce-tabs ul.tabs li.active a,.woocommerce div.product .woocommerce-tabs ul.tabs li a{color: #fff}.woocommerce #reviews #comments ol.commentlist li .comment-text{border-color:rgba(167, 167, 167, 0.2) !important}.woocommerce div.product .woocommerce-tabs ul.tabs li.active{background:rgba(167, 167, 167, 0.2)}.reviews_tab{margin:0 !important;background-color:rgba(167, 167, 167, 0.1) !important}.woocommerce div.product .woocommerce-tabs ul.tabs li::before,.woocommerce div.product .woocommerce-tabs ul.tabs li::after{display:none!important}#comments .commentlist li .avatar{box-shadow: 1px 10px 10px rgba(167, 167, 167, 0.1) !important}.cz_line{background:#fff}';
				}

				// Theme styles
				if ( is_customize_preview() ) {
					wp_add_inline_style( $handle, $extra_css );
				} else {
					$ts = self::option( 'css_out' );

					// Fix for old users
					if ( ! $ts && class_exists( 'Codevz_Options' ) ) {
						$ts = Codevz_Options::css_out();
					}

					// Add styles
					wp_add_inline_style( $handle, $extra_css . $ts );

					// Fonts
					$fonts = self::option( 'fonts_out', array() );
					foreach ( $fonts as $font ) {
						self::enqueue_font( $font );
					}
				}
			}
		}


		/**
		 *
		 * Register theme sidebars
		 * 
		 * @return object
		 *
		 */
		public static function register_sidebars() {
			$sidebars = array( 'primary', 'secondary', 'footer-1', 'footer-2', 'footer-3', 'footer-4', 'footer-5', 'footer-6', 'offcanvas_area' );
			foreach ( (array) self::option( 'sidebars' ) as $i ) {
				if ( ! empty( $i['id'] ) ) {
					$sidebars[] = sanitize_title_with_dashes( strtolower( $i['id'] ) );
				}
			}

			// Woocommerce
			if ( self::$is_woo ) {
				$sidebars[] = 'product-primary';
				$sidebars[] = 'product-secondary';
			}

			if ( function_exists( 'dwqa' ) ) {
				$sidebars[] = 'dwqa-question-primary';
				$sidebars[] = 'dwqa-question-secondary';
			}

			if ( function_exists( 'is_bbpress' ) ) {
				$sidebars[] = 'bbpress-primary';
				$sidebars[] = 'bbpress-secondary';
			}
			
			if ( function_exists( 'is_buddypress' ) ) {
				$sidebars[] = 'buddypress-primary';
				$sidebars[] = 'buddypress-secondary';
			}
			
			if ( function_exists( 'EDD' ) ) {
				$sidebars[] = 'download-primary';
				$sidebars[] = 'download-secondary';
			}

			// Post types
			$cpt = (array) get_option( 'codevz_post_types' );
			$cpt[] = self::option( 'slug_portfolio', 'portfolio' );

			// Custom post type UI
			if ( function_exists( 'cptui_get_post_type_slugs' ) ) {
				$cptui = cptui_get_post_type_slugs();
				if ( is_array( $cptui ) ) {
					$cpt = wp_parse_args( $cptui, $cpt );
				}
			}

			// All CPT
			foreach ( $cpt as $p ) {
				if ( $p ) {
					$sidebars[] = $p . '-primary';
					$sidebars[] = $p . '-secondary';
				}
			}

			foreach ( $sidebars as $id ) {
				$class = self::contains( $id, 'footer' ) ? 'footer_widget' : 'widget';
				register_sidebar( array( 
					'name'			=> esc_html( ucwords( str_replace( '-', ' ', $id ) ) ),
					'id'			=> $id,
					'before_widget'	=> '<div id="%1$s" class="' . esc_attr( $class ) . ' clr %2$s">',
					'after_widget'	=> '</div>',
					'before_title'	=> '<h4>',
					'after_title'	=> '</h4>'
				) );
			}
		}

		/**
		 *
		 * WP head
		 * 
		 * @return string
		 *
		 */
		public static function wp_head() {
			if ( is_singular() && pings_open() ) {
				printf( '<link rel="pingback" href="%s">' . "\n", get_bloginfo( 'pingback_url' ) );
			}
		}

		/**
		 *
		 * WP Menu current class
		 * 
		 * @return string
		 *
		 */
		public static function menu_current_class( $classes, $item ) {
			$classes[] = 'cz';

			// Fix anchor links
			if ( isset( $item->url ) && self::contains( $item->url, '/#' ) ) {
				return $classes;
			}

			// Find parent menu
			$in_array = in_array( 'current_page_parent', $classes );

			// Current menu
			if ( in_array( 'current-menu-ancestor', $classes ) || in_array( 'current-menu-item', $classes ) || ( $in_array && ( is_singular( 'post' ) || is_tag() || is_category() || is_author() ) ) ) {
				$classes[] = 'current_menu';
			}

			// Current parent menu
			if ( have_posts() ) { 
				$c = get_post_type_object( get_post_type( self::$post->ID ) );
				if ( ! empty( $c ) ) {
					$ms = strtolower( trim( $item->url ) );
					$ms = str_replace( home_url( '/' ), '', $ms );
					if ( ( self::contains( $ms, $c->rewrite['slug'] ) && $in_array ) || self::contains( $ms, $c->has_archive ) || self::contains( $ms, '/' . strtolower( $c->label ) ) ) {
						$classes[] = 'current_menu';
					}
				}
			}

			return $classes;
		}

		/**
		 *
		 * Set settings for post types
		 * 
		 * @return array
		 *
		 */
		public static function action_pre_get_posts( $q ) {
			if ( is_admin() || empty( $q ) ) {
				return $q;
			}

			$q->query[ 'post_type' ] = isset( $q->query[ 'post_type' ] ) ? $q->query[ 'post_type' ] : 'post';

			// Set new settings for post types
			$cpt = (array) get_option( 'codevz_post_types' );
			$cpt[] = 'portfolio';
			foreach ( $cpt as $name ) {
				$ppp = self::option( 'posts_per_page_' . $name );
				$is_cpt = ( is_post_type_archive( $name ) && $q->query[ 'post_type' ] === $name );
				$is_tax = ( is_tax( $name . '_cat' ) && isset( $q->query[ $name . '_cat' ] ) );

				if ( $ppp && ! is_admin() && ( $is_cpt || $is_tax ) ) {
					$q->set( 'posts_per_page', $ppp );
				}
			}

			// Search
			$search = self::option( 'search_cpt' );
			if ( $q->is_main_query() && $q->is_search() && $search ) {
				$q->set( 'post_type', explode( ',', str_replace( ' ', '', $search ) ) );
			}

			return $q;
		}

		/**
		 *
		 * Get current post type name
		 * 
		 * @return string
		 *
		 */
		public static function get_post_type( $id = '' ) {

			if ( is_search() || is_tag() || is_404() ) {
				$cpt = '';
			} else if ( function_exists( 'is_bbpress' ) && is_bbpress() ) {
				$cpt = 'bbpress';
			} else if ( self::$is_woo && ( is_shop() || is_woocommerce() ) ) {
				$cpt = 'product';
			} else if ( function_exists( 'is_buddypress' ) && is_buddypress() ) {
				$cpt = 'buddypress';
			} else if ( get_post_type( $id ) || is_singular() ) {
				$cpt = get_post_type( $id );
			} else if ( is_tax() ) {
				$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
				if ( get_taxonomy( $term->taxonomy ) ) {
					$cpt = get_taxonomy( $term->taxonomy )->object_type[0];
				}
			} else if ( is_post_type_archive() ) {
				$cpt = get_post_type_object( get_query_var( 'post_type' ) )->name;
			} else {
				$cpt = 'post';
			}

			return $cpt;
		}

		/**
		 *
		 * Get shortcode from page ID + Generate styles
		 * 
		 * @var post ID
		 * @return string
		 *
		 */
		public static function get_page_as_element( $id = '', $query = 0 ) {

			// Escape
			$id = esc_html( $id );
			$query = esc_html( $query );

			// Check
			if ( ! $id ) {
				return;
			}

			// Check if its number
			if ( ! is_numeric( $id ) ) {
				$page = get_page_by_title( $id, 'object', 'page' );
				if ( isset( $page->ID ) && ! is_page( $page->ID ) ) {
					$id = $page->ID;
				} else {
					return;
				}
			}

			// If post not exist
			if ( ! get_post_status( $id ) ) {
				return;
			}

			// Get post content by ID
			$o = get_post_field( 'post_content', $id );
			if ( $query ) {
				$o = str_replace( 'query=""', 'query="1"', $o );
			}
			
			// Get post meta
			$s = get_post_meta( $id, '_wpb_shortcodes_custom_css', 1 ) . get_post_meta( $id, 'cz_sc_styles', 1 );

			// Page builder tablet styles
			$tablet = get_post_meta( $id, 'cz_sc_styles_tablet', 1 );
			if ( $tablet ) {
				if ( substr( $tablet, 0, 1 ) === '@' ) {
					$s .= $tablet;
				} else {
					$s .= '@media screen and (max-width:768px){' . $tablet . '}';
				}
			}

			// Page builder mobile styles
			$mobile = get_post_meta( $id, 'cz_sc_styles_mobile', 1 );
			if ( $mobile ) {
				if ( substr( $mobile, 0, 1 ) === '@' ) {
					$s .= $mobile;
				} else {
					$s .= '@media screen and (max-width:480px){' . $mobile . '}';
				}
			}

			// Output
			if ( ! is_page( $id ) ) {
				$o = "<div data-cz-style='" . esc_attr( $s ) . "'>" . do_shortcode( $o ) . "</div>";
			} else {
				return;
			}

			return $o;
		}

		/**
		 *
		 * Get required data attributes for body
		 * 
		 * @return string
		 *
		 */
		public static function intro_attrs() {
			$i = ' data-ajax="' . admin_url( 'admin-ajax.php' ) . '"';

			// Theme Colors for live and switcher
			$i .= ' data-primary-color="' . esc_attr( self::option( 'site_color', '#4e71fe' ) ) . '"';
			$i .= ' data-primary-old-color="' . esc_attr( get_option( 'codevz_primary_color', self::option( 'site_color', '#4e71fe' ) ) ) . '"';
			$i .= ' data-secondary-color="' . esc_attr( self::option( 'site_color_sec', 0 ) ) . '"';
			$i .= ' data-secondary-old-color="' . esc_attr( get_option( 'codevz_secondary_color', 0 ) ) . '"';

			// NiceScroll
			if ( self::option( 'nicescroll' ) ) {
				$nice = (array) self::option( 'nicescroll_opt' );
				if ( isset( $nice[0] ) ) {
					$nice = $nice[0];
				}
				$nice['zindex'] = '999';
				$nice['cursorborder'] = '0px';
				$i .= ' data-nice=\'' . json_encode( $nice, JSON_HEX_QUOT ) . '\'';
			}

			return $i;
		}

		/**
		 *
		 * Filter WordPress excerpt length
		 * 
		 * @return string
		 *
		 */
		public static function excerpt_length() {
			$cpt = self::get_post_type();

			if ( $cpt && $cpt !== 'post' ) {
				return self::option( 'post_excerpt_' . $cpt, 20 );
			}

			return self::option( 'post_excerpt', 20 );
		}

		/**
		 *
		 * Excerpt read more button
		 * 
		 * @return string
		 *
		 */
		public static function excerpt_more( $more ) {
			return '';
		}
		public static function get_the_excerpt( $excerpt ) {
			$cpt = self::get_post_type();

			if ( $cpt && $cpt !== 'post' ) {
				$title = esc_html( self::option( 'readmore_' . $cpt ) );
				$icon = esc_attr( self::option( 'readmore_icon_' . $cpt ) );
			} else {
				$title = esc_html( self::option( 'readmore' ) );
				$icon = esc_attr( self::option( 'readmore_icon' ) );
			}
			
			$icon = $icon ? '<i class="' . $icon . '"></i>' : '';

			$button = ( $title || $icon ) ? '<a class="cz_readmore' . ( $title ? '' : ' cz_readmore_no_title' ) . ( $icon ? '' : ' cz_readmore_no_icon' ) . '" href="' . esc_url( get_the_permalink( self::$post->ID ) ) . '">' . $icon . '<span>' . $title . '</span></a>' : '';

			return $excerpt ? $excerpt . ' ... ' . $button : '';
		}

		/**
		 *
		 * Get next|prev posts for single post page
		 * 
		 * @return string
		 *
		 */
		public static function next_prev_item() {
			$cpt = self::get_post_type();
			$tax = ( $cpt === 'post' ) ? 'category' : $cpt . '_cat';
			$prevPost = get_previous_post( true, '', $tax ) ? get_previous_post( true, '', $tax ) : get_previous_post();
			$nextPost = get_next_post( true, '', $tax ) ? get_next_post( true, '', $tax ) : get_next_post();

			ob_start();
			if ( $prevPost || $nextPost ) { ?>
				<ul class="next_prev clr">
					<?php if( $prevPost ) { ?>
						<li class="previous">
							<?php $prevthumbnail = get_the_post_thumbnail( $prevPost->ID, 'thumbnail' ); ?>
							<?php previous_post_link( '%link', '<i class="fa fa-angle-left"></i><h4><small>' . esc_html( self::option( 'prev_post' ) ) . '</small>%title</h4>' ); ?>
						</li>
					<?php } if( $nextPost ) { ?>
						<li class="next">
							<?php $nextthumbnail = get_the_post_thumbnail( $nextPost->ID, 'thumbnail' ); ?>
							<?php next_post_link( '%link', '<h4><small>' . esc_html( self::option( 'next_post' ) ) . '</small>%title</h4><i class="fa fa-angle-right"></i>' ); ?>
						</li>
					<?php } ?>
				</ul>
			<?php 
			}

			return ob_get_clean();
		}

		/**
		 *
		 * Enqueue google font
		 * 
		 * @return string|null
		 * 
		 */
		public static function enqueue_font( $f = '' ) {
			if ( ! $f || self::contains( $f, 'custom_' ) ) {
				return;
			} else {
				$f = self::contains( $f, ';' ) ? self::get_string_between( $f, 'font-family:', ';' ) : $f;
				$f = str_replace( '=', ':', $f );
			}

			$defaults = array(
				'Arial' 			=> 'Arial',
				'Arial Black' 		=> 'Arial Black',
				'Comic Sans MS' 	=> 'Comic Sans MS',
				'Impact' 			=> 'Impact',
				'Lucida Sans Unicode' => 'Lucida Sans Unicode',
				'Tahoma' 			=> 'Tahoma',
				'Trebuchet MS' 		=> 'Trebuchet MS',
				'Verdana' 			=> 'Verdana',
				'Courier New' 		=> 'Courier New',
				'Lucida Console' 	=> 'Lucida Console',
				'Georgia, serif' 	=> 'Georgia, serif',
				'Palatino Linotype' => 'Palatino Linotype',
				'Times New Roman' 	=> 'Times New Roman'
			);

			// Custom fonts
			$custom_fonts = (array) self::option( 'custom_fonts' );
			foreach ( $custom_fonts as $a ) {
				if ( ! empty( $a['font'] ) ) {
					$defaults[ $a['font'] ] = $a['font'];
				}
			}

			$f = self::contains( $f, ':' ) ? $f : $f . ':100,200,300,400,500,600,700,800,900';
			$f = explode( ':', $f );
			$p = empty( $f[1] ) ? '' : ':' . $f[1];
			
			if ( ! empty( $f[0] ) && ! isset( $defaults[ $f[0] ] ) ) {
				wp_enqueue_style( 'google-font-' . sanitize_title_with_dashes( $f[0] ), '//fonts.googleapis.com/css?family=' . str_replace( ' ', '+', $f[0] ) . $p );
			}
		}

		/**
		 *
		 * SK Style + load font
		 * 
		 * @return string
		 *
		 */
		public static function sk_inline_style( $sk = '' ) {
			$sk = str_replace( 'CDVZ', '', $sk );
			if ( self::contains( $sk, 'font-family' ) ) {
				self::enqueue_font( $sk );

				// Extract font + params && Fix font for CSS
				$font = $o_font = self::get_string_between( $sk, 'font-family:', ';' );
				$font = str_replace( '=', ':', $font );
				
				if ( self::contains( $font, ':' ) ) {
					$font = explode( ':', $font );
					if ( ! empty( $font[0] ) ) {
						$sk = str_replace( $o_font, "'" . $font[0] . "'", $sk );
					}
				} else {
					$sk = str_replace( $font, "'" . $font . "'", $sk );
				}
			}

			if ( self::$is_rtl ) {
				return str_replace( 'RTL', '', $sk );
			} else if ( self::contains( $sk, 'RTL' ) ) {
				return strstr( $sk, 'RTL', true );
			} else {
				return $sk;
			}
		}

		/**
		 *
		 * Get element for row builder 
		 * 
		 * @return string
		 *
		 */
		public static function get_row_element( $i, $m = array() ) {
			if ( ! isset( $i['element'] ) ) {
				return;
			}

			// Element margin
			$margin = '';
			if ( ! empty( $i['margin'] ) ) {
				foreach ( $i['margin'] as $key => $val ) {
					$margin .= $val ? 'margin-' . esc_attr( $key ) . ': ' . esc_attr( $val ) . ';' : '';
				}
			}

			// Classes of element
			$elm_class = empty( $i['vertical'] ) ? '' : ' cz_vertical_elm';
			$elm_class .= empty( $i['elm_on_sticky'] ) ? '' : ' ' . $i['elm_on_sticky'];
			$elm_class .= empty( $i['hide_on_mobile'] ) ? '' : ' hide_on_mobile';
			$elm_class .= empty( $i['hide_on_tablet'] ) ? '' : ' hide_on_tablet';
			$elm_class .= empty( $i['elm_center'] ) ? '' : ' cz_elm_center';

			// Start element
			$elm = $i['element'];
			$elm_unique = esc_attr( $elm . '_' . $m['id'] );
			$data_settings = is_customize_preview() ? " data-settings='" . json_encode( $i, JSON_HEX_APOS ) . "'" : '';
			echo '<div class="cz_elm ' . esc_attr( $elm_unique . $m['depth'] . ' inner_' . $elm_unique . $m['inner_depth'] . $elm_class ) . '" style="' . esc_attr( $margin ) . '"' . wp_kses_post( $data_settings ) . '>';

			// Check element
			if ( $elm === 'logo' || $elm === 'logo_2' ) {

				$logo = self::option( $elm );

				if ( $logo ) {
					echo '<div class="logo_is_img ' . esc_attr( $elm ) . '"><a href="' . esc_url( home_url( '/' ) ) . '"><img src="' . esc_url( $logo ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" width="200" height="200"' . ( empty( $i['logo_width'] ) ? '' : ' style="width: ' . esc_attr( $i['logo_width'] ) . '"' ) . '></a>';
				} else {
					echo '<div class="logo_is_text ' . esc_attr( $elm ) . '"><a href="' . esc_url( home_url( '/' ) ) . '"><h1>' . esc_html( get_bloginfo( 'name' ) ) . '</h1></a>';
				}

				$logo_tooltip = self::option( 'logo_hover_tooltip' );
				if ( $logo_tooltip && $m['id'] !== 'header_4' && $m['id'] !== 'header_5' ) {
					echo '<div class="logo_hover_tooltip">' . self::get_page_as_element( esc_html( $logo_tooltip ) ) . '</div>';
				}
				
				echo '</div>';

			} else if ( $elm === 'menu' ) {

				$type = empty( $i['menu_type'] ) ? 'cz_menu_default' : $i['menu_type'];
				if ( $type === 'offcanvas_menu_left' ) {
					$type = 'offcanvas_menu inview_left';
				} else if ( $type === 'offcanvas_menu_right' ) {
					$type = 'offcanvas_menu inview_right';
				}

				$menu_title = isset( $i['menu_title'] ) ? $i['menu_title'] : '';
				$menu_icon = empty( $i['menu_icon'] ) ? 'fa fa-bars' : $i['menu_icon'];
				$icon_style = empty( $i['sk_menu_icon'] ) ? '' : self::sk_inline_style( $i['sk_menu_icon'] );
				$menu_icon_class = $menu_title ? ' icon_plus_text' : '';

				// Add icon and mobile menu
				if ( $type && $type !== 'offcanvas_menu' && $type !== 'cz_menu_default' ) {
					echo '<i class="' . esc_attr( $menu_icon . ' icon_' . $type . $menu_icon_class ) . '" style="' . esc_attr( $icon_style ) . '">' . esc_html( $menu_title ) . '</i>';
				}
				echo '<i class="' . esc_attr( $menu_icon . ' hide icon_mobile_' . $type . $menu_icon_class ) . '" style="' . esc_attr( $icon_style ) . '">' . esc_html( $menu_title ) . '</i>';

				// Default
				if ( empty( $i['menu_location'] ) ) {
					$i['menu_location'] = 'primary';
				}

				// Check for meta box and set one page instead primary
				if ( $i['menu_location'] === 'primary' && self::meta( 0, 0, 'one_page' ) ) {
					$i['menu_location'] = 'one-page';
				}

				// Menu
				$nav = array(
					'theme_location' 	=> esc_attr( $i['menu_location'] ),
					'cz_row_id' 		=> esc_attr( $m['id'] ),
					'container' 		=> false,
					'fallback_cb' 		=> '',
					'items_wrap' 		=> '<ul id="' . esc_attr( $elm_unique ) . '" class="sf-menu clr ' . esc_attr( $type ) . '" data-indicator="' . esc_attr( self::get_string_between( self::option( '_css_menu_indicator_a_' . $m['id'] ), '_class_indicator:', ';' ) ) . '" data-indicator2="' . esc_attr( self::get_string_between( self::option( '_css_menu_ul_indicator_a_' . $m['id'] ), '_class_indicator:', ';' ) ) . '">%3$s</ul>'
				);
				if ( class_exists( 'Codevz_Walker_nav' ) ) {
					$nav['walker'] = new Codevz_Walker_nav();
				}
				wp_nav_menu( $nav );

			} else if ( $elm === 'social' && class_exists( 'Codevz_Plus' ) ) {

				echo Codevz_Plus::social();

			} else if ( $elm === 'image' && isset( $i['image'] ) ) {

				$link = empty( $i['image_link'] ) ? '' : $i['image_link'];
				$width = empty( $i['image_width'] ) ? 'auto' : $i['image_width'];
				if ( $link ) {
					echo '<a class="elm_h_image" href="' . esc_url( $link ) . '"><img src="' . esc_url( $i['image'] ) . '" alt="image" width="' . esc_attr( $width ) . '" height="200" /></a>';
				} else {
					echo '<img src="' . esc_url( $i['image'] ) . '" alt="#" width="' . esc_attr( $width ) . '" height="200" />';
				}

			} else if ( $elm === 'icon' ) {

				$link = isset( $i['it_link'] ) ? $i['it_link'] : '';

				$text_style = empty( $i['sk_it'] ) ? '' : self::sk_inline_style( $i['sk_it'] );
				$icon_style = empty( $i['sk_it_icon'] ) ? '' : self::sk_inline_style( $i['sk_it_icon'] );

				if ( $link ) {
					echo '<a class="elm_icon_text" href="' . esc_url( $link ) . '">';
				} else {
					echo '<div class="elm_icon_text">';
				}

				if ( ! empty( $i['it_icon'] ) ) {
					echo '<i class="' . esc_attr( $i['it_icon'] ) . '" style="' . esc_attr( $icon_style ) . '"></i>';
				}

				if ( ! empty( $i['it_text'] ) ) {
					echo '<span class="' . esc_attr( empty( $i['it_icon'] ) ? '' : 'ml10' ) . '" style="' . esc_attr( $text_style ) . '">' . do_shortcode( wp_kses_post( str_replace( '%year%', current_time( 'Y' ), $i['it_text'] ) ) ) . '</span>';
				} else {
					echo '<span></span>';
				}
				
				if ( $link ) {
					echo '</a>';
				} else {
					echo '</div>';
				}

			} else if ( $elm === 'search' ) {

				$icon_style = empty( $i['sk_search_icon'] ) ? '' : self::sk_inline_style( $i['sk_search_icon'] );
				$icon_in_style = empty( $i['sk_search_icon_in'] ) ? '' : self::sk_inline_style( $i['sk_search_icon_in'] );
				$input_style = empty( $i['sk_search_input'] ) ? '' : self::sk_inline_style( $i['sk_search_input'] );
				$outer_style = empty( $i['sk_search_con'] ) ? '' : self::sk_inline_style( $i['sk_search_con'] );
				$ajax_style = empty( $i['sk_search_ajax'] ) ? '' : self::sk_inline_style( $i['sk_search_ajax'] );
				$icon = empty( $i['search_icon'] ) ? 'fa fa-search' : $i['search_icon'];
				$ajax = empty( $i['ajax_search'] ) ? '' : ' cz_ajax_search';

				$form_style = empty( $i['search_form_width'] ) ? '' : 'width: ' . esc_attr( $i['search_form_width'] );

				$i['search_type'] = empty( $i['search_type'] ) ? 'form' : $i['search_type'];
				$i['search_placeholder'] = empty( $i['search_placeholder'] ) ? '' : $i['search_placeholder'];

				echo '<div class="search_with_icon search_style_' . esc_attr( $i['search_type'] . $ajax ) . '">';
				echo self::contains( esc_attr( $i['search_type'] ), 'form' ) ? '' : '<i class="' . esc_attr( $icon ) . '" style="' . esc_attr( $icon_style ) . '"></i>';

				echo '<div class="outer_search" style="' . esc_attr( $outer_style ) . '"><div class="search" style="' . esc_attr( $form_style ) . '">'; ?>
					<form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" autocomplete="off">
						<?php 
							if ( $i['search_type'] === 'icon_full' ) {
								echo '<span' . ( empty( $i['sk_search_title'] ) ? '' : ' style="' . esc_attr( self::sk_inline_style( $i['sk_search_title'] ) ) . '"' ) . '>' . esc_html( $i['search_placeholder'] ) . '</span>';
								$i['search_placeholder'] = '';
							}
						?>
						<input name="nonce" type="hidden" value="<?php echo wp_create_nonce('ajax_search_nonce'); ?>" />
						<input name="cpt" type="hidden" value="<?php echo empty( $i['search_cpt'] ) ? '' : esc_attr( $i['search_cpt'] ); ?>" />
						<input name="posts_per_page" type="hidden" value="<?php echo empty( $i['search_count'] ) ? '' : esc_attr( $i['search_count'] ); ?>" />
						<input name="no_thumbnail" type="hidden" value="<?php echo empty( $i['search_no_thumbnail'] ) ? '' : esc_attr( $i['search_no_thumbnail'] ); ?>" />
						<input name="view_all_translate" type="hidden" value="<?php echo empty( $i['search_view_all_translate'] ) ? '' : esc_attr( $i['search_view_all_translate'] ); ?>" />
						<input class="ajax_search_input" name="s" type="text" placeholder="<?php echo esc_attr( $i['search_placeholder'] ); ?>" style="<?php echo esc_attr( $input_style ); ?>">
						<button type="submit"><i class="<?php echo wp_kses_post( $icon ); ?>" style="<?php echo esc_attr( $icon_in_style ); ?>"></i></button>
					</form>
					<div class="ajax_search_results" style="<?php echo esc_attr( $ajax_style ); ?>"></div>
				</div><?php
				echo '</div></div>';

			} else if ( $elm === 'widgets' ) {

				$elm_uniqid = 'cz_ofc_' . rand( 11111, 99999 );
				$con_style = empty( $i['sk_offcanvas'] ) ? '' : self::sk_inline_style( $i['sk_offcanvas'] );
				$icon_style = empty( $i['sk_offcanvas_icon'] ) ? '' : 'i.' . $elm_uniqid . '{' . self::sk_inline_style( $i['sk_offcanvas_icon'] ) . '}';
				$icon_style .= empty( $i['sk_offcanvas_icon_hover'] ) ? '' : 'i.' . $elm_uniqid . ':hover{' . self::sk_inline_style( $i['sk_offcanvas_icon_hover'] ) . '}';
				$icon = empty( $i['offcanvas_icon'] ) ? 'fa fa-bars' : $i['offcanvas_icon'];

				echo '<div class="offcanvas_container"><i class="' . esc_attr( $icon . ' ' . $elm_uniqid ) . '" data-cz-style="' . esc_attr( $icon_style ) . '"></i><div class="offcanvas_area offcanvas_original ' . ( empty( $i['inview_position_widget'] ) ? 'inview_left' : esc_attr( $i['inview_position_widget'] ) ) . '" style="' . esc_attr( $con_style ) . '">';
				if ( is_active_sidebar( 'offcanvas_area' ) ) {
					dynamic_sidebar( 'offcanvas_area' );  
				}
				echo '</div></div>';

			} else if ( $elm === 'hf_elm' ) {

				$con_style = empty( $i['sk_hf_elm'] ) ? '' : self::sk_inline_style( $i['sk_hf_elm'] );
				$icon_style = empty( $i['sk_hf_elm_icon'] ) ? '' : self::sk_inline_style( $i['sk_hf_elm_icon'] );
				$icon = empty( $i['hf_elm_icon'] ) ? 'fa fa-bars' : $i['hf_elm_icon'];

				echo '<i class="hf_elm_icon ' . esc_attr( $icon ) . '" style="' . esc_attr( $icon_style ) . '"></i><div class="hf_elm_area" style="' . esc_attr( $con_style ) . '"><div class="row clr">' . ( empty( $i['hf_elm_page'] ) ? '' : self::get_page_as_element( esc_html( $i['hf_elm_page'] ) ) ) . '</div></div>';

			} else if ( $elm === 'shop_cart' ) {

				if ( self::$is_woo ) {
					$icon_style = empty( $i['sk_shop_icon'] ) ? '' : self::sk_inline_style( $i['sk_shop_icon'] );
					$icon = empty( $i['shopcart_icon'] ) ? 'fa fa-shopping-basket' : $i['shopcart_icon'];

					$woo_style = empty( $i['sk_shop_count'] ) ? '' : '.cz_cart_count, .cart_1 .cz_cart_count{' . esc_attr( self::sk_inline_style( $i['sk_shop_count'] ) ) . '}';
					$woo_style .= empty( $i['sk_shop_content'] ) ? '' : '.cz_cart_items{' . esc_attr( self::sk_inline_style( $i['sk_shop_content'] ) ) . '}';

					echo '<div class="elms_shop_cart ' . ( empty( $i['shopcart_type'] ) ? '' : esc_attr( $i['shopcart_type'] ) ) . '" data-cz-style="' . esc_attr( $woo_style ) . '">';
					echo '<a class="shop_icon noborder" href="' . esc_url( wc_get_cart_url() ) . '">';
					echo '<i class="' . esc_attr( $icon ) . '" style="' . esc_attr( $icon_style ) . '"></i>';
					echo '</a><div class="cz_cart">' . ( is_customize_preview() ? '<span class="cz_cart_count">2</span><div class="cz_cart_items"><div><div class="cart_list"><div class="item_small"><a href="#"></a><div class="cart_list_product_title"><h3><a href="#">XXX</a></h3><div class="cart_list_product_quantity">1 x <span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>32.00</span></div><a href="#" class="remove" data-product_id="1066"><i class="fa fa-trash"></i></a></div></div><div class="item_small"><a href="#"></a><div class="cart_list_product_title"><h3><a href="#">XXX</a></h3><div class="cart_list_product_quantity">1 x <span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>32.00</span></div><a href="#" class="remove" data-product_id="1066"><i class="fa fa-trash"></i></a></div></div></div><div class="cz_cart_buttons clr"><a href="#">XXX, <span><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>64.00</span></span></a><a href="#">XXX</a></div></div></div>' : '' ) . '</div>';
					echo '</div>';
				} else {
					echo 'WooCommerce';
				}

			} else if ( $elm === 'line' && isset( $i['line_type'] ) ) {

				$line = empty( $i['sk_line'] ) ? '' : self::sk_inline_style( $i['sk_line'] );
				echo '<div class="' . esc_attr( $i['line_type'] ) . '" style="' . esc_attr( $line ) . '">&nbsp;</div>';

			} else if ( $elm === 'button' ) {

				$elm_uniqid = 'cz_btn_' . rand( 11111, 99999 );
				$btn_css = empty( $i['sk_btn'] ) ? '' : self::sk_inline_style( $i['sk_btn'] );
				$btn_hover = empty( $i['sk_btn_hover'] ) ? '' : '.' . esc_attr( $elm_uniqid ) . ':hover{' . str_replace( ';', ' !important;', self::sk_inline_style( $i['sk_btn_hover'] ) ) . '}';
				echo '<a class="cz_header_button ' . esc_attr( $elm_uniqid ) . '" href="' . ( empty( $i['btn_link'] ) ? '' : esc_url( $i['btn_link'] ) ) . '" style="' . esc_attr( $btn_css ) . '" data-cz-style="' . esc_attr( $btn_hover ) . '">' . esc_html( empty( $i['btn_title'] ) ? 'Button' : $i['btn_title'] ) . '</a>';

			// Custom shortcode or HTML codes
			} else if ( $elm === 'custom' && isset( $i['custom'] ) ) {

				echo do_shortcode( esc_html( $i['custom'] ) );

			// WPML Switcher
			} else if ( $elm === 'wpml' ) {

				if ( function_exists('icl_get_languages') ) {
					$wpml = icl_get_languages( 'skip_missing=N&orderby=KEY&order=DIR&link_empty_to=str' );
					if ( is_array( $wpml ) ) {
						$bg = empty( $i['wpml_background'] ) ? '' : 'background: ' . esc_attr( $i['wpml_background'] ) . '';
						echo '<div class="cz_language_switcher"><div style="' . esc_attr( $bg ) . '">';
						foreach ( $wpml as $lang => $vals ) {
							if ( ! empty( $vals ) ) {

								$class = $vals['active'] ? 'cz_current_language' : '';
								if ( empty( $i['wpml_title'] ) ) {
									$title = $vals['translated_name'];
								} else if ( $i['wpml_title'] !== 'no_title' ) {
									$title = ucwords( $vals[ $i['wpml_title'] ] );
								} else {
									$title = '';
								}

								if ( $class && ! empty( $i['wpml_color'] ) ) {
									$color = 'color: ' . esc_attr( $i['wpml_current_color'] );
								} else if ( ! $class && ! empty( $i['wpml_color'] ) ) {
									$color = 'color: ' . esc_attr( $i['wpml_color'] );
								}

								if ( !empty( $i['wpml_flag'] ) ) {
									echo '<a class="' . esc_attr( $class ) . '" href="' . esc_url( $vals['url'] ) . '" style="' . esc_attr( $color ) . '"><img src="' . esc_url( $vals['country_flag_url'] ) . '" alt="#" width="200" height="200" class="' . esc_attr( $title ? 'mr8' : '' ) . '" />' . esc_html( $title ) . '</a>';
								} else {
									echo '<a class="' . esc_attr( $class ) . '" href="' . esc_url( $vals['url'] ) . '" style="' . esc_attr( $color ) . '">' . esc_html( $title ) . '</a>';
								}

							}
						}
						echo '</div></div>';
					}
				} else {
					echo 'WPML';
				}

			// Custom page as element
			} else if ( $elm === 'custom_element' && ! empty( $i['header_elements'] ) ) {

				echo self::get_page_as_element( esc_html( $i['header_elements'] ) );
				//echo preg_replace( '#\[[^\]]+\]#', '', self::get_page_as_element( esc_html( $i['header_elements'] ) ) );
 
			// Current user avatar
			} else if ( $elm === 'avatar' ) {

				$sk_avatar = empty( $i['sk_avatar'] ) ? '' : $i['sk_avatar'];
				$link = empty( $i['avatar_link'] ) ? '' : $i['avatar_link'];
				$size = empty( $i['avatar_size'] ) ? '' : $i['avatar_size'];

				echo '<a class="cz_user_gravatar" href="' . esc_url( $link ) . '" style="' . esc_attr( $sk_avatar ) . '">';
				if ( is_user_logged_in() ) {
					global $current_user;
					echo wp_kses_post( get_avatar( esc_html( $current_user->user_email ), esc_attr( $size ) ) );
				} else {
					echo wp_kses_post( get_avatar( 'xxx@xxx.xxx', esc_attr( $size ) ) );
				}
				echo '</a>';
			}

			// Close element
			echo '</div>';
		}

		/**
		 *
		 * Get WooCommerce cart in header
		 * 
		 * @return string
		 *
		 */
		public static function woo_cart( $fragments ) {
			$wc = WC();
			$count = $wc->cart->cart_contents_count;
			$total = $wc->cart->get_cart_total();
			
			ob_start(); ?>
				<div class="cz_cart">
					<?php if ( $count > 0 ) { ?>
					<span class="cz_cart_count"><?php echo esc_html( $count ); ?> <span> - <?php echo esc_html( $total ); ?></span></span>
					<?php } ?>
					<div class="cz_cart_items"><div>
				        <?php if ( $wc->cart->cart_contents_count == 0 ) { ?>
					    	<div class="cart_list">
					    		<div class="item_small"><?php echo esc_html( self::option( 'woo_no_products', 'No products in the cart' ) ); ?></div>
					    	</div>
					    <?php $fragments['.cz_cart'] = ob_get_clean(); return $fragments; } else { ?>
				        	<div class="cart_list">
				        		<?php foreach( $wc->cart->cart_contents as $cart_item_key => $cart_item ) { $id = $cart_item['product_id']; ?>
						            <div class="item_small">
						                <a href="<?php echo esc_url( get_permalink( $id ) ); ?>">
						                	<?php $thumbnail_id = $cart_item['variation_id'] ? $cart_item['variation_id'] : $id; ?>
						                	<?php echo wp_kses_post( get_the_post_thumbnail( $thumbnail_id, 'thumbnail' ) ); ?>
						                </a>
						                <div class="cart_list_product_title">
						                    <h3><a href="<?php echo esc_url( get_permalink( $id ) ); ?>"><?php echo esc_html( get_the_title( $id ) ); ?></a></h3>
						                    <div class="cart_list_product_quantity"><?php echo wp_kses_post( $cart_item['quantity'] ); ?> x <?php echo wp_kses_post( $wc->cart->get_product_subtotal( $cart_item['data'], 1 ) ); ?> </div>
						                    <a href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>" class="remove" data-product_id="<?php echo esc_attr( $id ); ?>"><i class="fa fa-trash"></i></a>
						                </div>
						            </div>
				        		<?php } ?>
				        	</div>
					        
					        <div class="cz_cart_buttons clr">
								<a href="<?php echo esc_url( get_permalink(get_option('woocommerce_cart_page_id')) ); ?>"><?php echo esc_html( self::option( 'woo_cart', 'Cart' ) ); ?>, <span><?php echo wp_kses_post( $wc->cart->get_cart_total() ); ?></span></a>
								<a href="<?php echo esc_url( get_permalink(get_option('woocommerce_checkout_page_id')) ); ?>"><?php echo esc_html( self::option( 'woo_checkout', 'Checkout' ) ); ?></a>
					        </div>
				        <?php } ?>
					</div></div>
				</div>
			<?php 

			$fragments['.cz_cart'] = ob_get_clean();

			return $fragments;
		}

		/**
		 *
		 * WooCommerce products columns
		 * 
		 * @return string
		 *
		 */
		public static function woo_col() {
			return (int) self::option( 'woo_col', 4 );
		}

		/**
		 *
		 * WooCommerce products per page
		 * 
		 * @return string
		 *
		 */
		public static function woo_ppp( $q ) {
			$q->set( 'posts_per_page', (int) self::option( 'woo_items_per_page', 8 ) );
		}

		/**
		 *
		 * WooCommerce products per page
		 * 
		 * @return string
		 *
		 */
		public static function woo_related( $i ) {
			$i['posts_per_page'] = $i['columns'] = (int) self::option( 'woo_related', 3 );

			return $i;
		}

		/**
		 *
		 * Generate inner row elements positions
		 * 
		 * @return string
		 *
		 */
		public static function codevz_row_inner( $id = 0, $pos = 0, $out = '' ) {
			if ( isset( $_POST['id'] ) && isset( $_POST['pos'] ) ) {
				$ajax = 1;
				$id = $_POST['id'];
				$pos = $_POST['pos'];
			}

			$elms = self::option( $id . $pos );
			if ( $elms ) {
				$shape = self::get_string_between( self::option( '_css_' . $id . $pos ), '_class_shape:', ';' );
				$shape = $shape ? ' ' . $shape : '';
				$center = self::contains( $pos, 'center' );

				echo '<div class="elms' . esc_attr( $pos . ' ' . $id . $pos . ' ' . $shape ) . '">';
				if ( $center ) {
					echo '<div>';
				}
				$inner_id = 0;
				foreach ( (array) $elms as $v ) {
					if ( empty( $v['element'] ) ) {
						continue;
					}
					$more = array();
					$more['id'] = $id;
					$more['depth'] = $pos . '_' . self::$elm_id++;
					$more['inner_depth'] = $pos . '_' . $inner_id++;

					self::get_row_element( $v, $more );
				}
				if ( $center ) {
					echo '</div>';
				}
				echo '</div>';
			}

			if ( isset( $ajax ) ) {
				die();
			}
		}

		/**
		 *
		 * Generate header|footer|side row elements
		 * 
		 * @return string
		 *
		 */
		public static function row( $args ) {

			ob_start();
			foreach ( $args['nums'] as $num ) {
				$id = esc_attr( $args['id'] );

				// Check if sticky header is not custom
				if ( $num === '5' && ! self::option( 'sticky_header' ) ) {
					continue;
				}

				// Columns
				$left = self::option( $id . $num . $args['left'] );
				$right = self::option( $id . $num . $args['right'] );
				$center = self::option( $id . $num . $args['center'] );

				// Row Shape
				$shape = self::get_string_between( self::option( '_css_row_' . $id . $num ), '_class_shape:', ';' );
				$shape = $shape ? ' ' . $shape : '';

				// Menu FX
				$menufx = self::get_string_between( self::option( '_css_menu_a_hover_before_' . $id . $num ), '_class_menu_fx:', ';' );
				$menufx = $menufx ? ' ' . $menufx : '';

				// Menu FX
				$submenufx = self::get_string_between( self::option( '_css_menu_ul_' . $id . $num ), '_class_submenu_fx:', ';' );
				$submenufx = $submenufx ? ' ' . $submenufx : '';

				// Check sticky header
				$sticky = self::option( 'sticky_header' );
				$sticky = ( self::contains( $sticky, $num ) && $id !== 'footer_' ) ? ' header_is_sticky' : '';
				if ( is_page() ) {
					$smart = Codevz::meta( self::$post->ID, 0, 'one_page' );
				}
				$sticky .= ( empty( $smart ) && $sticky && self::option( 'smart_sticky' ) ) ? ' smart_sticky' : '';
				$sticky .= ( self::option( 'mobile_sticky' ) && $id . $num === 'header_4' ) ? ' ' . self::option( 'mobile_sticky' ) : '';

				// Before mobile header
				if ( $num === '4' && self::option( 'b_mobile_header' ) ) {
					echo '<div class="row clr cz_before_mobile_header">' . self::get_page_as_element( self::option( 'b_mobile_header' ) ) . '</div>';
				}

				// Start
				if ( $left || $center || $right ) {
					echo '<div class="' . esc_attr( $id . $num . ( $center ? ' have_center' : '' ) . $shape . $sticky . $menufx . $submenufx ) . '">';
					if ( $args['row'] ) {
						echo '<div class="row elms_row"><div class="clr">';
					}

					self::codevz_row_inner( $id . $num, $args['left'] );
					self::codevz_row_inner( $id . $num, $args['center'] );
					self::codevz_row_inner( $id . $num, $args['right'] );

					if ( $args['row'] ) {
						echo '</div></div>';
					}
					echo '</div>';
				}

				// After mobile header
				if ( $num === '4' && self::option( 'a_mobile_header' ) ) {
					echo '<div class="row clr cz_after_mobile_header">' . self::get_page_as_element( self::option( 'a_mobile_header' ) ) . '</div>';
				}
			}
			echo ob_get_clean();
		}

		/**
		 *
		 * Generate page
		 * 
		 * @return string
		 *
		 */
		public static function generate_page( $page = '' ) {
			get_header();

			// Settings
			$cpt = self::get_post_type();
			$is_search = is_search();
			if ( $is_search ) {
				$option_cpt = '_search';
			} else if ( is_home() || is_category() || is_tag() || $cpt === 'post' ) {
				$option_cpt = '_post';
			} else {
				$option_cpt = ( $cpt === 'post' || $cpt === 'page' || empty( $cpt ) ) ? '' : '_' . $cpt;
			}
			$title = self::option( 'page_title' . $option_cpt );
			$title = ( ! $title || $title === '1' ) ? self::option( 'page_title' ) : $title;
			$layout = self::option( 'layout' . $option_cpt );

			if ( ! $cpt || $cpt === 'post' || $cpt === 'page' ) {
				$primary = 'primary';
				$secondary = 'secondary';
			} else {
				$cpt_slug = get_post_type_object( $cpt );
				$cpt_slug = isset( $cpt_slug->name ) ? $cpt_slug->name : $cpt;
				$primary = $cpt_slug . '-primary';
				$secondary = $cpt_slug . '-secondary';
			}

			$layout = ( ! $layout || $layout === '1' ) ? self::option( 'layout' ) : $layout;
			$blank = ( $layout === 'bpnp' || $layout === 'ws' ) ? 1 : 0;
			$is_404 = ( is_404() || $page === '404' );
			$current_id = $is_404 ? self::option( '404' ) : ( isset( self::$post->ID ) ? self::$post->ID : 0 );
			$show_featured_image = 1;
			if ( is_singular() || $cpt === 'page' || $is_404 ) {
				$meta = self::meta( $current_id );
				if ( isset( $meta['layout'] ) && $meta['layout'] !== '1' ) {
					$layout = $meta['layout'];
					$blank = ( $meta['layout'] === 'none' || $meta['layout'] === 'bpnp' ) ? 1 : 0;
				}
				$show_featured_image = empty( $meta['hide_featured_image'] );
			}
			$queried_object = get_queried_object();

			// Start page content
			$bpnp = ( $layout === 'bpnp' ) ? ' cz_bpnp' : '';
			$bpnp .= empty( $meta['page_content_margin'] ) ? '' : ' ' . $meta['page_content_margin'];
			echo '<div id="page_content" class="page_content' . esc_attr( $bpnp ) . '"><div class="row clr">';

			// Before content
			if ( $is_404 ) {
				echo '<section class="s12 clr">';
			} else if ( $layout === 'both-side' ) {
				echo '<aside class="col s3 sidebar_primary"><div class="sidebar_inner">';
				if ( is_active_sidebar( $primary ) ) {
					dynamic_sidebar( $primary );  
				}
				echo '</div></aside><section class="col s6">';
			} else if ( $layout === 'both-side2' ) {
				echo '<aside class="col s3 sidebar_primary"><div class="sidebar_inner">';
				if ( is_active_sidebar( $primary ) ) {
					dynamic_sidebar( $primary );
				}
				echo '</div></aside><section class="col s7">';
			} else if ( $layout === 'both-right' ) {
				echo '<section class="col s6">';
			} else if ( $layout === 'both-right2' ) {
				echo '<section class="col s7">';
			} else if ( $layout === 'right' ) {
				echo '<section class="col s8">';
			} else if ( $layout === 'right-s' ) {
				echo '<section class="col s9">';
			} else if ( $layout === 'center' ) {
				echo '<aside class="col s2">&nbsp</aside>';
				echo '<section class="col s8">';
			} else if ( $layout === 'both-left' ) {
				echo '<section class="col s6 col_not_first righter">';
			} else if ( $layout === 'both-left2' ) {
				echo '<section class="col s7 col_not_first righter">';
			} else if ( $layout === 'left' ) {
				echo '<section class="col s8 col_not_first righter">';
			} else if ( $layout === 'left-s' ) {
				echo '<section class="col s9 col_not_first righter">';
			} else {
				echo '<section class="s12 clr">';
			}

			$single_classes = is_single() ? ' ' . implode( ' ', get_post_class( 'single_con' ) ) : '';
			echo '<div class="' . esc_attr( ( $blank ? 'cz_is_blank' : 'content' ) . $single_classes ) . ' clr">';

			if ( $is_404 ) {
				if ( $current_id ) {
					echo self::get_page_as_element( $current_id );
				} else {
					echo '<h2 style="text-align:center;font-size:160px">404<small style="font-size: 32px">' . self::option( '404_msg', 'How did you get here?! It’s cool. We’ll help you out.' ) . '</small></h2>';
					echo '<form class="search_404" method="get" action="' . esc_url(home_url('/')) . '">
	                    <input id="inputhead" name="s" type="text" value="">
	                    <button type="submit"><i class="fa fa-search"></i></button>
	                </form>';
					echo '<a class="button" href="' . esc_url( home_url( '/' ) ) . '" style="margin: 80px auto 0;display:table">' . self::option( '404_btn', 'Back to Homepage' ) . '</a>';
				}
			} else if ( $page === 'page' || $page === 'single' ) {
				if ( have_posts() ) {
					$single_meta_cpt = ( $cpt === 'page' || empty( $cpt ) ) ? 'post' : $cpt;
					$single_meta = (array) self::option( 'meta_data_' . $single_meta_cpt );
					$single_meta = array_flip( $single_meta );
					$author_url = get_author_posts_url( get_the_author_meta( 'ID' ) );

					the_post();
					if ( ! $blank && ( $title === '1' || $title === '2' || $title === '8' ) ) {
						$small = '';
						if ( is_single() && empty( $queried_object->taxonomy ) && isset( $single_meta['mbot'] ) ) {
							$small .= '<span class="cz_top_meta">';
							$small .= '<span class="cz_top_meta_i mr10"><i class="fa fa-edit mr4"></i>Posted by <a class="cz_post_author_name" href="' . esc_url( $author_url ) . '">' . ucwords( get_the_author() ) . '</a></span>';
							$small .= '<span class="cz_top_meta_i"><i class="fa fa-clock-o mr4"></i>on <span class="cz_post_date">' . esc_html( get_the_time( get_option( 'date_format' ) ) ) . '</span></span>';
							$small .= '</span>';
						}
						echo '<h3 class="section_title">' . get_the_title() . wp_kses_post( $small ) . '</h3>';
					}

					if ( $page === 'single' && has_post_thumbnail() && isset( $single_meta['image'] ) && $show_featured_image ) {
						echo '<div class="cz_single_fi">';
						the_post_thumbnail( 'full' );
						echo '</div><br />';
					}

					echo '<div class="cz_post_content">';
					the_content();
					echo '</div>';

					echo '<div class="clr"></div>';

					wp_link_pages( array(
						'before'=>'<div class="pagination mt20 clr">', 
						'after'=>'</div>', 
						'link_after'=>'</b>', 
						'link_before'=>'<b>'
					));

					if ( $page === 'single' && empty( $queried_object->taxonomy ) ) {

						if ( isset( $single_meta['date'] ) || isset( $single_meta['author'] ) ) {
							echo '<span class="cz_post_meta mt50">';
							echo isset( $single_meta['author'] ) ? '<a class="cz_post_author_avatar" href="' . esc_url( $author_url ) . '">' . get_avatar( get_the_author_meta( 'ID' ), 40 ) . '</a>' : '';
							echo '<span class="cz_post_inner_meta">';
							echo isset( $single_meta['author'] ) ? '<a class="cz_post_author_name" href="' . esc_url( $author_url ) . '">' . ucwords( get_the_author() ) . '</a>' : '';
							echo isset( $single_meta['date'] ) ? '<span class="cz_post_date">' . esc_html( get_the_time( get_option( 'date_format' ) ) ) . '</span>' : '';
							echo '</span></span>';
						}

						echo '<div class="clr mt40"></div>';

						if ( isset( $single_meta['cats'] ) ) {
							echo '<p class="cz_post_cat mr20">';
							echo self::post_category();
							echo '</p>';
						}

						if ( isset( $single_meta['tags'] ) ) {
							echo self::the_tags();
						}
						
						echo '<div class="clr"></div>';

						if ( isset( $single_meta['next_prev'] ) && self::next_prev_item() ) {
							echo '</div><div class="content cz_next_prev_posts clr">' . self::next_prev_item();
						}

						if ( isset( $single_meta['author_box'] ) && self::author_box() ) {
							echo '</div><div class="content cz_author_box clr">';
							echo '<h4>' . esc_html( ucfirst( get_the_author_meta('display_name') ) ) . '<small class="righter cz_view_author_posts"><a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">Author posts <i class="fa fa-angle-double-right ml4"></i></a></small></h4>';
							echo self::author_box();
						}

						$related_ppp = self::option( 'related_' . $single_meta_cpt . '_ppp' );
						if ( $related_ppp && $related_ppp != '0' && $cpt !== 'page' && $cpt !== 'product' && $cpt !== 'dwqa-question' ) {
							echo self::related(array(
								'posts_per_page' 	=> esc_attr( $related_ppp ),
								'related_columns' 	=> esc_attr( self::option( 'related_' . $single_meta_cpt . '_col', 's4' ) ),
								'section_title' 	=> esc_html( self::option( 'related_posts_' . $single_meta_cpt, 'Related Posts ...' ) )
							));
						}
					}
				}
			} else if ( $page === 'woocommerce' ) {
				woocommerce_content();
			} else if ( have_posts() ) {

				// Archive title
				if ( ! is_home() && ( $title === '2' || $title === '8' ) ) {
					self::page_title();
				}

				// Author box
				if ( is_author() && self::author_box() ) {
					echo '<h3>' . esc_html( ucfirst( get_the_author_meta('display_name') ) ) . '<small class="righter cz_view_author_posts"><a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">View all posts <i class="fa fa-angle-double-right ml4"></i></a></small></h3>';
					echo self::author_box();
					echo '</div><div class="content clr">';
				}

				// Template
				$template = self::option( 'template_style' );

				if ( $cpt && $cpt !== 'post' && $cpt !== 'page' ) {
					$template = self::option( 'template_style_' . $cpt, $template );
					$x_height = self::option( '2x_height_image_' . $cpt );
					$excerpt = self::option( 'post_excerpt_' . $cpt , 20 );
				} else {
					$cpt = 'post';
					$x_height = self::option( '2x_height_image' );
					$excerpt = self::option( 'post_excerpt', 20 );
				}

				$custom_template = self::option( 'template_' . $cpt );

				if ( $template === 'x' && $custom_template ) {
					echo self::get_page_as_element( esc_html( $custom_template ), 1 );
				} else {

					$gallery_mode = '';
					if ( $template === '9' || $template === '10' || $template === '11' ) {
						$gallery_mode = ' cz_posts_gallery_mode';
					}

					$post_class = '';
					$svg = self::option( 'default_featured_image', 0 ) ? 'cz_post_svg' : '';

					// Sizes
					$image_size = 'codevz_360_320';
					$svg_w = '360';
					$svg_h = '320';
					if ( $template == '2' ) {
						$post_class .= ' cz_default_loop_right';
					} else if ( $template == '3' ) {
						$post_class .= ' cz_default_loop_full';
						$image_size = 'codevz_1200_500';
						$svg_w = '1200';
						$svg_h = '500';
					} else if ( $template == '4' || $template == '9' ) {
						$post_class .= ' cz_default_loop_grid col s6';
					} else if ( $template == '5' || $template == '10' ) {
						$post_class .= ' cz_default_loop_grid col s4';
					} else if ( $template == '7' || $template == '11' ) {
						$post_class .= ' cz_default_loop_grid col s3';
					} else if ( $template == '8' ) {
						$post_class .= ' cz_default_loop_full';
						$image_size = 'codevz_1200_200';
						$svg_w = '1200';
						$svg_h = '200';
					}

					// Square size
					if ( $template === '4' || $template === '12' ) {
						$image_size = 'codevz_600_600';
						$svg_w = $svg_h = '600';
					}

					// Square size
					if ( $template === '9' || $template === '10' || $template === '11' ) {
						$post_class .= ' cz_default_loop_square';
						$image_size = 'codevz_600_600';
						$svg_w = $svg_h = '600';
					}

					// Vertical size
					if ( $x_height && $template !== '3' ) {
						$image_size = 'codevz_600_1000';
						$svg_w = '600';
						$svg_h = '1000';

						if ( $template === '8' ) {
							$image_size = 'codevz_1200_500';
							$svg_w = '1200';
							$svg_h = '500';
						}
					}

					// Clearfix
					$clr = 999;
					if ( $template === '4' || $template === '9' ) {
						$clr = 2;
					} else if ( $template === '5' || $template === '10' ) {
						$clr = 3;
					} else if ( $template === '7' || $template === '11' ) {
						$clr = 4;
					}

					// Post hover icon
					if ( self::contains( self::option( 'hover_icon_' . $cpt ), array( 'image', 'imhoh', 'iasi' ) ) ) {
						$post_hover_icon = '<i class="cz_post_icon"><img src="' . self::option( 'hover_icon_image_' . $cpt ) . '" /></i>';
					} else if ( self::option( 'hover_icon_' . $cpt ) === 'none' ) {
						$post_hover_icon = '';
					} else {
						$post_hover_icon = '<i class="cz_post_icon ' . self::option( 'hover_icon_icon_' . $cpt, 'fa czico-109-link-symbol-1' ) . '"></i>';
					}
					if ( self::option( 'hover_icon_' . $cpt ) === 'ihoh' || self::option( 'hover_icon_' . $cpt ) === 'imhoh' ) {
						$gallery_mode .= ' cz_post_hover_icon_hoh';
					} else if ( self::option( 'hover_icon_' . $cpt ) === 'asi' || self::option( 'hover_icon_' . $cpt ) === 'iasi' ) {
						$gallery_mode .= ' cz_post_hover_icon_asi';
					}

					echo '<div class="cz_posts_container cz_posts_template_' . $template . $gallery_mode . '"><div class="clr mb30">';

					// Chess style
					$chess = 0;
					if ( self::contains( $template, array( '12', '13', '14' ) ) ) {
						$chess = 1;
					}

					$i = 1;
					while ( have_posts() ) {
						the_post();
						$link = get_the_permalink();
						$title = get_the_title();
						$author_url = get_author_posts_url( get_the_author_meta( 'ID' ) );
						$even_odd = '';
						if ( $template === '6' ) {
							$even_odd = ( $i % 2 == 0 ) ? ' cz_post_even cz_default_loop_right' : ' cz_post_odd';
						}

						echo '<article class="' . esc_attr( implode( ' ', get_post_class( 'cz_default_loop clr' . $post_class . $even_odd ) ) ) . '"><div class="clr">';
						
						if ( has_post_thumbnail() ) {
							echo '<a class="cz_post_image" href="' . esc_url( $link ) . '">';
							the_post_thumbnail( $image_size );
							echo wp_kses_post( $post_hover_icon ) . '</a>';
						} else if ( $svg ) {
							echo '<a class="cz_post_image ' . $svg . '" href="' . esc_url( $link ) . '">';
							echo '<img src="data:image/svg+xml,%3Csvg%20xmlns%3D&#39;http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg&#39;%20width=&#39;' . $svg_w . '&#39;%20height=&#39;' . $svg_h . '&#39;%20viewBox%3D&#39;0%200%20' . $svg_w . '%20' . $svg_h . '&#39;%2F%3E" alt="Placeholder" />';
							echo wp_kses_post( $post_hover_icon ) . '</a>';
						}
						
						if ( $chess ) {
							echo '<div class="cz_post_chess_content">';
							echo '<a class="cz_post_title" href="' . esc_url( $link ) . '"><h3>' . $title . '</h3></a>';
							echo self::excerpt_more( 1 );
							echo '</div>';
						} else {
							echo '<a class="cz_post_title" href="' . esc_url( $link ) . '"><h3>' . $title . '</h3></a>';
							$author_url = get_author_posts_url( get_the_author_meta( 'ID' ) );
							echo '<span class="cz_post_meta mt10 mb10">';
							echo '<a class="cz_post_author_avatar" href="' . esc_url( $author_url ) . '">' . get_avatar( get_the_author_meta( 'ID' ), 40 ) . '</a>';
							echo '<span class="cz_post_inner_meta">';
							echo '<a class="cz_post_author_name" href="' . esc_url( $author_url ) . '">' . esc_html( ucwords( get_the_author() ) ) . '</a>';
							echo '<span class="cz_post_date">' . esc_html( get_the_time( get_option( 'date_format' ) ) ) . '</span>';
							echo '</span></span>';
							$ex = ( ( $excerpt !== '-1' ) ? get_the_excerpt() : do_shortcode( get_the_content() ) );
							echo ( $ex && $ex !== ' ... ' ) ? '<div class="cz_post_excerpt">' . $ex . '</div>' : '';
							echo '</div>';
						}

						echo '</article>';

						// Clearfix
						if ( $i % $clr === 0 ) {
							echo '</div><div class="clr mb30">';
						}

						$i++;
					}

					echo '</div></div>'; // row

					// Pagination
					echo '<div class="clr tac">';
					the_posts_pagination(array(
						'prev_text'          => self::$is_rtl ? '<i class="fa fa-angle-double-right mr4"></i>' : '<i class="fa fa-angle-double-left mr4"></i>',
						'next_text'          => self::$is_rtl ? '<i class="fa fa-angle-double-left ml4"></i>' : '<i class="fa fa-angle-double-right ml4"></i>',
						'before_page_number' => ''
					));
					echo '</div>';
				}

			} else {
				echo '<h3>' . esc_html( self::option( 'not_found', 'Not found!' ) ) . '</h3>';
			}

			echo '</div>'; // content

			// Comments
			if ( is_single() || is_page() ) {
				if ( ! $is_404 && comments_open() ) {
					echo '<div id="comments" class="content clr">';
					comments_template();
					echo '</div>';
				} else if ( isset( $queried_object->post_type ) && $queried_object->post_type == 'post' ) {
					echo '<p class="cz_nocomment mb10" style="opacity:.6"><i>' . esc_html( self::option( 'cm_disabled', 'Comments are disabled.' ) ) . '</i></p>';
				}
			}

			echo '</section>';

			// After content
			if ( $is_404 ) {
				echo '<section class="s12 clr">';
			} else if ( $layout === 'right' ) {
				echo '<aside class="col s4 sidebar_primary"><div class="sidebar_inner">';
				if ( is_active_sidebar( $primary ) ) {
					dynamic_sidebar( $primary );  
				}
				echo '</div></aside>';
			} else if ( $layout === 'right-s' ) {
				echo '<aside class="col s3 sidebar_primary"><div class="sidebar_inner">';
				if ( is_active_sidebar( $primary ) ) {
					dynamic_sidebar( $primary );  
				}
				echo '</div></aside>';
			} else if ( $layout === 'left' ) {
				echo '<aside class="col s4 col_first sidebar_primary"><div class="sidebar_inner">';
				if ( is_active_sidebar( $primary ) ) {
					dynamic_sidebar( $primary );  
				}
				echo '</div></aside>';
			} else if ( $layout === 'left-s' ) {
				echo '<aside class="col s3 col_first sidebar_primary"><div class="sidebar_inner">';
				if ( is_active_sidebar( $primary ) ) {
					dynamic_sidebar( $primary );  
				}
				echo '</div></aside>';
			} else if ( $layout === 'center' ) {
				echo '<aside class="col s2">&nbsp</aside>';
			} else if ( $layout === 'both-side' ) {
				echo '<aside class="col s3 righter sidebar_secondary"><div class="sidebar_inner">';
				if ( is_active_sidebar( $secondary ) ) {
					dynamic_sidebar( $secondary );  
				}
				echo '</div></aside>';
			} else if ( $layout === 'both-side2' ) {
				echo '<aside class="col s2 righter sidebar_secondary"><div class="sidebar_inner">';
				if ( is_active_sidebar( $secondary ) ) {
					dynamic_sidebar( $secondary );  
				}
				echo '</div></aside>';
			} else if ( $layout === 'both-right' ) {
				echo '<aside class="col s3 sidebar_secondary"><div class="sidebar_inner">';
				if ( is_active_sidebar( $secondary ) ) {
					dynamic_sidebar( $secondary );  
				}
				echo '</div></aside><aside class="col s3 sidebar_primary"><div class="sidebar_inner">';
				if ( is_active_sidebar( $primary ) ) {
					dynamic_sidebar( $primary );  
				}
				echo '</div></aside>';
			} else if ( $layout === 'both-right2' ) {
				echo '<aside class="col s2 sidebar_secondary"><div class="sidebar_inner">';
				if ( is_active_sidebar( $secondary ) ) {
					dynamic_sidebar( $secondary );  
				}
				echo '</div></aside><aside class="col s3 sidebar_primary"><div class="sidebar_inner">';
				if ( is_active_sidebar( $primary ) ) {
					dynamic_sidebar( $primary );  
				}
				echo '</div></aside>';
			} else if ( $layout === 'both-left' ) {
				echo '<aside class="col s3 col_first sidebar_primary"><div class="sidebar_inner">';
				if ( is_active_sidebar( $primary ) ) {
					dynamic_sidebar( $primary );  
				}
				echo '</div></aside><aside class="col s3 sidebar_secondary"><div class="sidebar_inner">';
				if ( is_active_sidebar( $secondary ) ) {
					dynamic_sidebar( $secondary );  
				}
				echo '</div></aside>';
			} else if ( $layout === 'both-left2' ) {
				echo '<aside class="col s3 col_first sidebar_primary"><div class="sidebar_inner">';
				if ( is_active_sidebar( $primary ) ) {
					dynamic_sidebar( $primary );  
				}
				echo '</div></aside><aside class="col s2 sidebar_secondary"><div class="sidebar_inner">';
				if ( is_active_sidebar( $secondary ) ) {
					dynamic_sidebar( $secondary );  
				}
				echo '</div></aside>';
			}

			echo '</div></div>'; // row, page_content
			get_footer();
		}

		/**
		 *
		 * Get post type's categories
		 * 
		 * @return string
		 *
		 */
		public static function post_category( $l = 1, $s = 0 ) {

			$out = array();
			$cpt = self::get_post_type();
			$tax = ( $cpt === 'post' ) ? 'category' : $cpt . '_cat';

			$terms = (array) get_the_terms( self::$post->ID, $tax );
			foreach ( $terms as $term ) {
				if ( isset( $term->term_id ) ) {
					$out[] = $l ? '<a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a>' : esc_html( $term->name );
				}
			}

			$out = implode( $s ? ', ' : '', $out );
			$pre = $l ? '<a href="#"><i class="fa fa-folder-open"></i></a>' : '<i class="fa fa-folder-open mr10"></i>';

			return $out ? $pre . $out : '';
		}

		/**
		 *
		 * Get post type's tags
		 * 
		 * @return string
		 *
		 */
		public static function the_tags() {
			$out = '';
			$tax = get_object_taxonomies( self::$post->post_type, 'objects' );

			foreach ( $tax as $tax_slug => $taks ) {
				$terms = get_the_terms( self::$post->ID, $tax_slug );

			    if ( ! empty( $terms ) && self::contains( $taks->label, 'Tags' ) ) {
			        $out .= '<p class="tagcloud"><a href="#"><i class="fa fa-tags"></i></a>';
			        foreach ( $terms as $term ) {
			            $out .= '<a href="' . esc_url( get_term_link( $term->slug, $tax_slug ) ) . '">' . esc_html( $term->name ) . '</a>';
			        }
			        $out .= "</p>";
			    }
			}

			return $out;
		}

		/**
		 *
		 * Get related posts for single post page
		 * 
		 * @return string
		 *
		 */
		public static function related( $args = array() ) {

			$id = self::$post->ID;
			$cpt = get_post_type( $id );
			$meta = self::meta();

			$args = wp_parse_args( $args, array(
				'extra_class'	=> '',
				'by'			=> 'cats',
				'post_type'		=> $cpt,
				'post__not_in'	=> array( $id ),
				'posts_per_page'=> 3,
				'related_columns'=> 's4'
			) );

			if ( $args['by'] === 'cats' ) {
				if ( $cpt === 'post' ) {
					$args['category__in'] = wp_get_post_categories( $id, array( 'fields'=>'ids' ) );
				} else {
					$taxonomy = $cpt . '_cat';
					$get_cats = get_the_terms( $id, $taxonomy );
					$get_cats = $get_cats ? $get_cats : '';
					if ( $get_cats ) {
						$tax = array('relation' => 'OR');
						foreach ( $get_cats as $key ) {
							if ( is_object( $key ) ) {
								$tax[] = array(
									'taxonomy' => $taxonomy,
									'terms' => $key->term_id
								);
							}
						}
						$args['tax_query'] = $tax;
					}
				}
			} else if ( $args['by'] === 'tags' ) {
				$args['tag__in'] = wp_get_post_tags( $id, array( 'fields'=>'ids' ) );
			} else if ( $args['by'] === 'rand' ) {
				$args['orderby'] = 'rand';
			}

			/* Tax query */
			$taxes = array( '_cats', '_tags' );
			$tax_query = array();
			foreach ( $taxes as $tax ) {
				if ( isset( $args[ $tax ] ) ) {
					$tax_array = explode( ',', $args[ $tax ] );
					if ( $tax === '_cats' ) {
						$tax = 'category';
					} else if ( $tax === '_tags' ) {
						$tax = 'post_tags';
					}
					foreach ( $tax_array as $cat ) {
						if ( ! empty( $cat ) ) {
							$tax_query[] = array( 'taxonomy' => $tax, 'field' => 'slug', 'terms' => $cat );
						}
					}
				}
			}
			$args['tax_query'] = empty( $tax_query ) ? null : wp_parse_args( $tax_query, array( 'relation' => 'OR' ) );

			$query = new WP_Query( $args );

			ob_start();
			echo '<div class="clr">';
			if ( $query->have_posts() ): 
				$i = 1;
				$col = ( $args['related_columns'] === 's6' ) ? 2 : ( ( $args['related_columns'] === 's4' ) ? 3 : 4 );
				while ( $query->have_posts() ) : $query->the_post();
				$cats = ( ! $cpt || $cpt === '' || $cpt === 'post' ) ? 'category' : $cpt . '_cat';	
			?>
				<article id="post-<?php the_ID(); ?>" class="cz_related_post col <?php echo esc_attr( $args['related_columns'] ); ?>"><div>
					<?php if ( has_post_thumbnail() ) { ?><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php the_post_thumbnail( 'codevz_360_320' ); ?></a><?php } ?>
					<a class="cz_post_title mt10 block" href="<?php echo esc_url( get_the_permalink() ); ?>">
						<h3><?php the_title(); ?></h3>
					</a>
					<?php echo wp_kses_post( get_the_term_list( get_the_id(), $cats, '<small class="cz_related_post_date mt10"><i class="fa fa-folder-open mr10"></i>', ', ', '</small>' ) ); ?>
				</div></article>
			<?php 
				if ( $i % $col === 0 ) {
					echo '</div><div class="clr">';
				}

				$i++;
				endwhile;
			endif;
			echo '</div>';
			wp_reset_postdata();

			$related = ob_get_clean();

			if ( $related && $related !== '<div class="clr"></div>' ) {
				return '</div><div class="content cz_related_posts clr"><h4>' . esc_html( $args['section_title'] ) . '</h4>' . $related;
			}
		}

		/**
		 *
		 * Get string between two string
		 * 
		 * @return string
		 * 
		 */
		public static function get_string_between( $c = '', $s, $e, $m = 0 ) {
			if ( $c ) {
				if ( $m ) {
					preg_match_all( '~' . preg_quote( $s, '~' ) . '(.*?)' . preg_quote( $e, '~' ) . '~s', $c, $matches );
					return $matches[0];
				}

				$r = explode( $s, $c );
				if ( isset( $r[1] ) ) {
					$r = explode( $e, $r[1] );
					return $r[0];
				}
			}

			return;
		}

		/**
		 *
		 * Check if string contains specific value(s)
		 * 
		 * @return string
		 *
		 */
		public static function contains( $v = '', $a = array() ) {
			if ( $v ) {
				foreach ( (array) $a as $k ) {
					if ( $k && strpos( $v, $k ) !== false ) {
						return 1;
						break;
					}
				}
			}
			
			return null;
		}
		
		/**
		 *
		 * Get current page title
		 * 
		 * @return string
		 *
		 */
		public static function page_title( $tag = 'h3', $class = '' ) {

			if ( is_404() ) {
				$i = '404';
			} else if ( is_search() ) {
				$i = self::option( 'search_title_prefix', 'Search result for:' ) . ' ' . get_search_query();
			} else if ( is_archive() ) {
				$i = get_the_archive_title();
				if ( self::contains( $i, ':' ) ) {
					$i = substr( $i, strpos( $i, ': ' ) + 1 );
				}
			} else if ( is_single() ) {
				$i = single_post_title( '', false );
				$i = $i ? $i : get_the_title();
			} else if ( is_home() ) {
				$i = get_option( 'page_for_posts' ) ? get_the_title( get_option( 'page_for_posts' ) ) : 'Blog';
			} else {
				$i = get_the_title();
			}

			echo '<' . esc_attr( $tag ) . ' class="section_title ' . esc_attr( $class ) . '">' . wp_kses_post( $i ) . '</' . esc_attr( $tag ) . '>';
			if ( is_category() && category_description() ) {
				echo category_description();
			}

			if ( is_tag() && tag_description() ) {
				echo tag_description();
			}

			if ( is_tax() && term_description( get_query_var('term_id'), get_query_var( 'taxonomy' ) ) ) {
				echo term_description( get_query_var('term_id'), get_query_var( 'taxonomy' ) );
			}
		}

		/**
		 *
		 * Get author box
		 * 
		 * @return string
		 *
		 */
		public static function author_box() {
			return get_the_author_meta( 'description' ) ? '<div class="cz_author_box clr"><div class="lefter mr20 mt10">' . get_avatar( get_the_author_meta( 'user_email' ), '100' ) . '</div><p>' . get_the_author_meta('description') . '</p></div>' : '';
		}

		/**
		 *
		 * Ajax search process
		 * 
		 * @return string
		 *
		 */
		public static function codevz_ajax_search() {
			if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( $_GET['nonce'], 'ajax_search_nonce' ) ) {
				die( '<b class="ajax_search_error">Try again ...</b>' );
			}

			$l = empty( $_GET['posts_per_page'] ) ? 4 : (int) $_GET['posts_per_page'];
			$s = sanitize_text_field( $_GET['s'] );
			$c = empty( $_GET['cpt'] ) ? array( 'any' ) : explode( ',', str_replace( ' ', '', $_GET['cpt'] ) );
			
			$q = new WP_Query( array(
				'post_type' 	 => $c,
				's'              => $s,
				'posts_per_page' => $l,
				'orderby'		 => 'type',
				'fields'         => 'ids'
			));

			$nt = empty( $_GET['no_thumbnail'] ) ? 0 : 1;

			ob_start();
			if ( $q->have_posts() ) {
				while ( $q->have_posts() ) {
					$q->the_post();
					$cpt = self::get_post_type();
					if ( $cpt === 'page' || $cpt === 'dwqa-answer' ) {
						continue;
					}

					echo '<div id="post-' . esc_attr( get_the_id() ) . '" class="item_small">';
					if ( has_post_thumbnail() && ! $nt ) {
						echo '<a class="theme_img_hover" href="' . esc_url( get_the_permalink() ) . '"><img src="' . esc_url( get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ) ) . '" width="80" height="80" /></a>';
					}
					echo apply_filters( 'cz_ajax_search_instead_img', '' );
					echo '<div class="item-details">';
					echo '<h3><a href="' . esc_url( get_the_permalink() ) . '" rel="bookmark">' . get_the_title() . '</a></h3>';
					echo apply_filters( 'cz_ajax_search_meta', '<span class="cz_search_item_cpt mr4"><i class="fa fa-folder-o mr4"></i>' . ucwords( ( $cpt === 'dwqa-question' ) ? 'Questions' : $cpt ) . '</span><span><i class="fa fa-clock-o mr4"></i>' . esc_html( get_the_date() ) . '</span>' );
					echo '</div></div>';
				}
			} else {
				echo '<b class="ajax_search_error">' . esc_html( self::option( 'not_found', 'Not found!' ) ) . '</b>';
			}

			if ( $q->post_count >= $l ) {
				unset( $_GET['action'] );
				unset( $_GET['nonce'] );
				$va = empty( $_GET['view_all_translate'] ) ? 'View all results' : $_GET['view_all_translate'];
				echo '<a class="va_results" href="' . esc_url( home_url( '/' ) ) . '?s=' . esc_attr( $s ) . '">' . $va . '</div>';
			}

			echo ob_get_clean();
			wp_reset_postdata();
			die();
		}

		/**
		 *
		 * Get breadcrumbs
		 * 
		 * @return string
		 *
		 */
		public static function breadcrumbs( $is_right = '' ) {
			$out = array();
			$bc = (array) self::breadcrumbs_array();
			$count = count( $bc );
			$i = 1;
			foreach ( $bc as $ancestor ) {
				if ( $i === $count ) {
					global $wp;
					$out[] = '<b itemscope itemtype="http://data-vocabulary.org/Breadcrumb" class="inactive_l"><a class="cz_br_current" href="' . esc_url( home_url( $wp->request ) ) . '" onclick="return false;" itemprop="url"><span itemprop="title">' . wp_kses_post( $ancestor['title'] ) . '</span></a></b>';
				} else {
					$out[] = '<b itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="' . esc_url( $ancestor['link'] ) . '" itemprop="url"><span itemprop="title">' . wp_kses_post( $ancestor['title'] ) . '</span></a></b>';
				}
				$i++;
			}

			echo '<div class="breadcrumbs clr' . esc_attr( $is_right ) . '">';
			echo wp_kses_post( implode( ' <i class="' . esc_attr( self::option( 'breadcrumbs_separator', 'fa fa-long-arrow-right' ) ) . ' cz_breadcrumbs_separator"></i> ', $out ) );
			echo '</div>';
		}

		public static function breadcrumbs_array() {
			global $post;

			$bc = array();
			$bc[] = array( 'title' => '<i class="fa fa-home cz_breadcrumbs_home"></i>', 'link' => esc_url( home_url( '/' ) ) );
			$bc = self::add_posts_page_array( $bc );
			if ( is_404() ) {
				$bc[] = array( 'title' => '404', 'link' => false );
			} else if ( is_search() ) {
				$bc[] = array( 'title' => get_search_query(), 'link' => false );
			} else if ( is_tax() ) {
				$taxonomy = get_query_var( 'taxonomy' );
				$term = get_term_by( 'slug', get_query_var( 'term' ), $taxonomy );
				if ( get_taxonomy( $term->taxonomy ) ) {
					$ptn = get_taxonomy( $term->taxonomy )->object_type[0];
					$bc[] = array( 'title' => ucwords($ptn), 'link' => get_post_type_archive_link( $ptn ) );
				}
				$bc[] = array( 'title' => sprintf( '%s', $term->name ), 'link' => get_term_link( $term->term_id, $term->slug ) );
			} else if ( is_attachment() ) {
				if ( $post->post_parent ) {
					$parent_post = get_post( $post->post_parent );
					if ( $parent_post ) {
						$singular_bread_crumb_arr = self::singular_breadcrumbs_array( $parent_post );
						$bc = array_merge( $bc, $singular_bread_crumb_arr );
					}
				}
				if ( isset( $parent_post->post_title ) ) {
					$bc[] = array( 'title' => $parent_post->post_title, 'link' => get_permalink( $parent_post->ID ) );
				}
				$bc[] = array( 'title' => sprintf( '%s', $post->post_title ), 'link' => get_permalink( $post->ID ) );
			} else if ( ( is_singular() || is_single() ) && ! is_front_page() ) {
				$singular_bread_crumb_arr = self::singular_breadcrumbs_array( $post );
				$bc = array_merge( $bc, $singular_bread_crumb_arr );
				$bc[] = array( 'title' => $post->post_title, 'link' => get_permalink( $post->ID ) );
			} else if ( is_category() ) {
				global $cat;

				$category = get_category( $cat );
				if ( $category->parent != 0 ) {
					$ancestors = array_reverse( get_ancestors( $category->term_id, 'category' ) );
					foreach ( $ancestors as $ancestor_id ) {
						$ancestor = get_category( $ancestor_id );
						$bc[] = array( 'title' => $ancestor->name, 'link' => get_category_link( $ancestor->term_id ) );
					}
				}
				$bc[] = array( 'title' => sprintf( '%s', $category->name ), 'link' => get_category_link( $cat ) );
			} else if ( is_tag() ) {
				global $tag_id;
				$tag = get_tag( $tag_id );
				$bc[] = array( 'title' => sprintf( '%s', $tag->name ), 'link' => get_tag_link( $tag_id ) );
			} else if ( is_author() ) {
				$author = get_query_var( 'author' );
				$bc[] = array( 'title' => sprintf( '%s', get_the_author_meta( 'display_name', get_query_var( 'author' ) ) ), 'link' => get_author_posts_url( $author ) );
			} else if ( is_day() ) {
				$m = get_query_var( 'm' );
				if ( $m ) {
					$year = substr( $m, 0, 4 );
					$month = substr( $m, 4, 2 );
					$day = substr( $m, 6, 2 );
				} else {
					$year = get_query_var( 'year' );
					$month = get_query_var( 'monthnum' );
					$day = get_query_var( 'day' );
				}
				$month_title = self::get_month_title( $month );
				$bc[] = array( 'title' => sprintf( '%s', $year ), 'link' => get_year_link( $year ) );
				$bc[] = array( 'title' => sprintf( '%s', $month_title ), 'link' => get_month_link( $year, $month ) );
				$bc[] = array( 'title' => sprintf( '%s', $day ), 'link' => get_day_link( $year, $month, $day ) );
			} else if ( is_month() ) {
				$m = get_query_var( 'm' );
				if ( $m ) {
					$year = substr( $m, 0, 4 );
					$month = substr( $m, 4, 2 );
				} else {
					$year = get_query_var( 'year' );
					$month = get_query_var( 'monthnum' );
				}
				$month_title = self::get_month_title( $month );
				$bc[] = array( 'title' => sprintf( '%s', $year ), 'link' => get_year_link( $year ) );
				$bc[] = array( 'title' => sprintf( '%s', $month_title ), 'link' => get_month_link( $year, $month ) );
			} else if ( is_year() ) {
				$m = get_query_var( 'm' );
				if ( $m ) {
					$year = substr( $m, 0, 4 );
				} else {
					$year = get_query_var( 'year' );
				}
				$bc[] = array( 'title' => sprintf( '%s', $year ), 'link' => get_year_link( $year ) );
			} else if ( is_post_type_archive() ) {
				$post_type = get_post_type_object( get_query_var( 'post_type' ) );
				$bc[] = array( 'title' => sprintf( '%s', $post_type->label ), 'link' => get_post_type_archive_link( $post_type->name ) );
			}

			return $bc;
		}

		public static function singular_breadcrumbs_array( $post ) {
			$bc = array();
			$post_type = get_post_type_object( $post->post_type );

			if ( $post_type && $post_type->has_archive ) {
				$bc[] = array( 'title' => sprintf( '%s', $post_type->label ), 'link' => get_post_type_archive_link( $post_type->name ) );
			}

			if ( is_post_type_hierarchical( $post_type->name ) ) {
				$ancestors = array_reverse( get_post_ancestors( $post ) );
				if ( count( $ancestors ) ) {
					$ancestor_posts = get_posts( 'post_type=' . $post_type->name . '&include=' . implode( ',', $ancestors ) );
					foreach( (array) $ancestors as $ancestor ) {
						foreach ( (array) $ancestor_posts as $ancestor_post ) {
							if ( $ancestor === $ancestor_post->ID ) {
								$bc[] = array( 'title' => $ancestor_post->post_title, 'link' => get_permalink( $ancestor_post->ID ) );
							}
						}
					}
				}
			} else {
				$post_type_taxonomies = get_object_taxonomies( $post_type->name, false );
				if ( is_array( $post_type_taxonomies ) && count( $post_type_taxonomies ) ) {
					foreach( $post_type_taxonomies as $tax_slug => $taxonomy ) {
						if ( $taxonomy->hierarchical && $tax_slug !== 'post_tag' && $tax_slug !== 'artists_cat' ) {
							$terms = get_the_terms( self::$post->ID, $tax_slug );
							if ( $terms ) {
								$term = array_shift( $terms );
								if ( $term->parent != 0  ) {
									$ancestors = array_reverse( get_ancestors( $term->term_id, $tax_slug ) );
									foreach ( $ancestors as $ancestor_id ) {
										$ancestor = get_term( $ancestor_id, $tax_slug );
										$bc[] = array( 'title' => $ancestor->name, 'link' => get_term_link( $ancestor, $tax_slug ) );
									}
								}
								$bc[] = array( 'title' => $term->name, 'link' => get_term_link( $term, $tax_slug ) );
								break;
							}
						}
					}
				}
			}

			return $bc;
		}

		public static function add_posts_page_array( $bc ) {
			if ( is_page() || is_front_page() || is_author() || is_date() ) {
				return $bc;
			} else if ( is_category() ) {
				$tax = get_taxonomy( 'category' );
				if ( count( $tax->object_type ) != 1 || $tax->object_type[0] != 'post' ) {
					return $bc;
				}
			} else if ( is_tag() ) {
				$tax = get_taxonomy( 'post_tag' );
				if ( count( $tax->object_type ) != 1 || $tax->object_type[0] != 'post' ) {
					if ( isset( $_GET['post_type'] ) ) {
						$bc[] = array( 'title' => get_post_type_object( $_GET['post_type'] )->labels->name, 'link' => get_post_type_archive_link( $_GET['post_type'] ) );
					}
					return $bc;
				}
			} else if ( is_tax() ) {
				$tax = get_taxonomy( get_query_var( 'taxonomy' ) );
				if ( count( $tax->object_type ) != 1 || $tax->object_type[0] != 'post' ) {
					return $bc;
				}
			} else if ( is_home() && ! get_query_var( 'pagename' ) ) {
				return $bc;
			} else {
				$post_type = get_query_var( 'post_type' ) ? get_query_var( 'post_type' ) : 'post';
				if ( $post_type != 'post' ) {
					return $bc;
				}
			}
			if ( get_option( 'show_on_front' ) === 'page' && get_option( 'page_for_posts' ) && ! is_404() ) {
				$posts_page = get_post( get_option( 'page_for_posts' ) );
				$bc[] = array( 'title' => $posts_page->post_title, 'link' => get_permalink( $posts_page->ID ) );
			}

			return $bc;
		}

		public static function get_month_title( $monthnum = 0 ) {
			global $wp_locale;
			$monthnum = (int) $monthnum;
			$date_format = get_option( 'date_format' );
			if ( in_array( $date_format, array( 'DATE_COOKIE', 'DATE_RFC822', 'DATE_RFC850', 'DATE_RFC1036', 'DATE_RFC1123', 'DATE_RFC2822', 'DATE_RSS' ) ) ) {
				$month_format = 'M';
			} else if ( in_array( $date_format, array( 'DATE_ATOM', 'DATE_ISO8601', 'DATE_RFC3339', 'DATE_W3C' ) ) ) {
				$month_format = 'm';
			} else {
				preg_match( '/(^|[^\\\\]+)(F|m|M|n)/', str_replace( '\\\\', '', get_option( 'date_format' ) ), $m );
				$month_format = empty( $m[2] ) ? 'F' : $m[2];
			}

			if ( $month_format === 'F' ) {
				return $wp_locale->get_month( $monthnum );
			} else if ( $month_format === 'M' ) {
				return $wp_locale->get_month_abbrev( $wp_locale->get_month( $monthnum ) );
			} else {
				return $monthnum;
			}
		}

	}

	Codevz::instance();
}
// Add custom font to font settings
function wpex_add_custom_fonts() {
	return array( 'FuturaBookC' ); // You can add more then 1 font to the array!
	return array( 'FuturaDemiC' ); // You can add more then 1 font to the array!
	return array( 'FuturaLightC' ); // You can add more then 1 font to the array!
	return array( 'FuturaMediumC' ); // You can add more then 1 font to the array!
}