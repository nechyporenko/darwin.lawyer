<?php if ( ! defined( 'ABSPATH' ) ) {die;}
/*
	Plugin Name: Codevz Plus
	Plugin URI: http://codevz.com/
	Description: StyleKit, post types, settings, elements, etc.
	Version: 2.0
	Author: Codevz®
	Copyright: Codevz
*/
define( 'CDVZ_VC_CAT', 'Codevz' );

/**
 *
 * Core class and functions
 *
 * @return object
 *
 */
if ( ! class_exists( 'Codevz_Plus' ) ) {
	
	class Codevz_Plus {

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
		 * Post meta box ID
		 *
		 * @var string
		 *
		 */
		public static $meta_id = 'codevz_page_meta';

		/**
		 *
		 * Theme options ID
		 *
		 * @var string
		 *
		 */
		public static $options_id = 'codevz_theme_options';

		/**
		 *
		 * Other variables
		 *
		 * @var $post 			= global current post object
		 * @var $is_rtl 		= check if site is in rtl mode
		 * @var $is_admin 		= check if is admin area
		 * @var $vc_editable 	= check if is front end page builder
		 * @var $array_pages 	= list of all pages as an array
		 * @var $is_theme 		= check if theme is from codevz
		 *
		 */
		public static $post, $is_rtl, $is_admin, $vc_editable, $array_pages, $is_theme;

		public function __construct() {

			// Define
			define( 'CDVZ_VC_WEIGHT', 0 );
			define( 'CDVZ_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			define( 'CDVZ_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
			self::$post 		= &$GLOBALS['post'];
			self::$vc_editable 	= ( isset( $_GET['vc_editable'] ) || isset( $_GET['preview_id'] ) );
			self::$is_admin 	= is_admin();
			self::$is_rtl 		= ( is_rtl() || self::option( 'rtl' ) || isset( $_GET['rtl'] ) );
			self::$is_theme 	= file_exists( get_template_directory() . '/config/config.php' );
			self::$array_pages 	= array();
			if ( self::$is_admin || is_customize_preview() ) {
				self::$array_pages = array();
				$pages = (array) get_posts( 'post_type="page"&numberposts=-1' );
				foreach ( $pages as $page ) {
					if ( isset( $page->post_title ) ) {
						self::$array_pages[ $page->post_title ] = $page->post_title;
					}
				}
			}

			// Lazyload
			if ( ! self::$vc_editable && self::option( 'lazyload' ) ) {
				add_filter( 'the_content', array( __CLASS__, 'lazyload' ), 999 );
				add_filter( 'widget_text', array( __CLASS__, 'lazyload' ), 999 );
				add_filter( 'post_thumbnail_html', array( __CLASS__, 'lazyload' ), 100000 );
				add_filter( 'get_avatar', array( __CLASS__, 'lazyload' ), 999 );
			}

			// Query args & types of assets
			add_filter( 'script_loader_src', array( __CLASS__, 'remove_query_args' ), 15, 1 );
			add_filter( 'style_loader_src', array( __CLASS__, 'remove_query_args' ), 15, 1 );
			add_filter( 'style_loader_tag', array( __CLASS__, 'remove_type_attr' ), 10, 2 );
			add_filter( 'script_loader_tag', array( __CLASS__, 'remove_type_attr' ), 10, 2 );
			
			// do_shortcode
			add_filter( 'widget_text', 'do_shortcode' );

			// Redirect maintenance page
			add_filter( 'template_redirect', array( __CLASS__, 'maintenance_mode' ) );

			// Delete Emoji
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			remove_action( 'wp_print_styles', 'print_emoji_styles' );
			remove_action( 'admin_print_styles', 'print_emoji_styles' );

			/**
			 *
			 * Load required files
			 * 
			 * @return object
			 * 
			 */
			require_once( CDVZ_PLUGIN_DIR . '/admin/csf.php' );
			if ( self::$is_theme ) {
				require_once( CDVZ_PLUGIN_DIR . '/options.php' );
				require_once( CDVZ_PLUGIN_DIR . '/widgets.php' );
				require_once( CDVZ_PLUGIN_DIR . '/megamenu.php' );
				require_once( CDVZ_PLUGIN_DIR . '/importer/importer.php' );
			}

			/**
			 *
			 * Presets content by AJAX
			 * 
			 * @return string
			 * 
			 */
			add_action( 'wp_ajax_cz_presets', function() {
				if ( ! isset( $_GET['type'] ) || ! class_exists( 'Codevz_Presets' ) ) {
					esc_html_e( 'Not found', 'codevz' );
				} else {
					$t = $_GET['type'];
					echo "<link rel='stylesheet' href='" . CDVZ_PLUGIN_URI . "assets/codevzplus.css' media='all' />";
					foreach ( Codevz_Presets::get( $t ) as $p ) {
						if ( ! is_array( $p ) || ! isset( $p['n'] ) || ( isset( $p['n'] ) && $p['n'] == '0' ) ) {
							continue;
						}

						// Format
						$p['e'] = isset( $p['e'] ) ? $p['e'] : 'jpg';

						// Add content and convert all to array
						$c = self::get_string_between( $p['s'], ']', '[/' );
						$v = shortcode_parse_atts( self::get_string_between( $p['s'], '[', ']' ) );
						if ( ! empty( $c ) ) {
							$v['content'] = $c;
						}

						// remove shortcode name
						unset( $v[0] );

						// Output
						echo "<div data-num='" . $p['n'] . "' data-shortcode='" . json_encode( $v, JSON_HEX_APOS ) . "'>";

						if ( $p['e'] === 'con' ) {
							echo "<div class='cz_pre_in'>" . do_shortcode( $p['s'] ) . "</div>";
						} else {
							echo "<img src='" . get_template_directory_uri() . "/config/presets/" . $t . "_" . $p['n'] . "." . $p['e'] . "' />";
						}
						echo "</div>";
					}
				}

				die();
			});

			/**
			 *
			 * Actions and filters
			 * 
			 * @return -
			 * 
			 */
			add_action( 'init', array( __CLASS__, 'init' ), 0 );
			add_action( 'save_post', array( __CLASS__, 'save_post' ) );

			/**
			 *
			 * Body Classes
			 * 
			 * @return string
			 * 
			 */
			add_filter( 'body_class', function( $c = array() ) {

				// Support purpose
				if ( self::isDev() ) {
					$c[] = 'codevz_support_';
				}

				// Post type class
				$cpt = self::get_post_type();
				$cpt = $cpt ? $cpt : get_post_type();
				if ( $cpt ) {
					$c[] = 'cz-cpt-' . $cpt;

					// Woocommerce
					$c[] = 'woo-col-' . self::option( 'woo_col', 4 );
					$c[] = 'woo-related-col-' . self::option( 'woo_related_col', 3 );
				}

				// RTL
				$c[] = self::$is_rtl ? 'rtl' : '';

				// Sticky
				$c[] = self::option( 'sticky' ) ? 'cz_sticky' : '';

				// Fix
				$c[] = 'clr';

				// Page ID
				if ( get_the_id() ) {
					$c[] = 'cz-page-' . get_the_id();
				}

				return $c;
			});

			/**
			 *
			 * wp_head
			 * 
			 * @return string
			 *
			 */
			add_action( 'wp_head', function() {

				if ( ! self::$vc_editable && self::option( 'seo_meta_tags' ) && ! defined( 'WPSEO_VERSION' ) ) {

					$title = $desc = $tags = '';

					if ( is_single() || is_page() ) {
						$url = get_the_permalink();
						$title = get_the_title();
						$desc = self::meta( 0, 0, 'seo_desc' );
						if ( ! $desc ) {
							$desc = apply_filters( 'the_content', self::$post->post_content );
							$desc = $desc ? wp_trim_words( do_shortcode( strip_tags( $desc ) ), 30 ) : $title;
						}
						$tags = self::meta( 0, 0, 'seo_keywords' );
						$tags = $tags ? $tags : rtrim( strip_tags( str_replace( '</a>', ',', get_the_tag_list() ) ), ',' );
						$image = get_the_post_thumbnail_url();
						echo $image ? '<meta property="og:image" content="' . $image . '" />' . "\n" : '';
					} else {
						global $wp;
						$url = home_url( $wp->request );
					}

					$title = $title ? $title : get_bloginfo( 'name' );
					$desc = $desc ? $desc : self::option( 'seo_desc', get_bloginfo( 'description' ) );
					$desc = trim( preg_replace( '/\s+/', ' ', $desc ) );
					$tags = $tags ? $tags : self::option( 'seo_keywords' );

					echo '<meta property="og:title" content="' . $title . '" />' . "\n";
					echo '<meta property="og:url" content="' . $url . '" />' . "\n";
					echo '<meta name="description" content="' . $desc . '">' . "\n";
					echo $tags ? '<meta name="keywords" content="' . $tags . '">' . "\n" : '';
					echo '<meta property="og:description" content="' . $desc . '" />' . "\n";
					echo '<meta property="og:type" content="website" />' . "\n";

				}

				echo self::option( 'head_codes' );
			});

			/**
			 *
			 * For Support Purpose
			 * 
			 * @return string
			 * 
			 */
			add_action( 'wp_footer', function() {

				// Support Purpose
				if ( self::isDev() ) {
					$theme = wp_get_theme();
					$themev = $theme->Version;
					if ( is_child_theme() ) {
						$theme = wp_get_theme( $theme->Template );
						$themev = $theme->Version;
					}
					$wp_version = get_bloginfo( 'version' );
					$memory_limit = ini_get( 'memory_limit' );
					$memory_get_usage = @number_format_i18n( memory_get_usage() );
					$memory_get_peak_usage = @number_format_i18n( memory_get_peak_usage() );
					$wp_upload_dir = wp_upload_dir();

					$array = array(
						array( 'WordPress', $wp_version ),
						array( 'Theme: ' . $theme->Name, $themev ),
						array( 'Language', get_locale() ),
						array( 'Multisite', is_multisite() ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>' ),
						array( 'WP_DEBUG', WP_DEBUG ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>' ),
						array( 'PHP Version', phpversion() ),
						array( 'Server Software', $_SERVER["SERVER_SOFTWARE"] ),
						array( 'Post Max Size', ini_get( 'post_max_size' ) ),
						array( 'Upload Max Size', ini_get( 'upload_max_filesize' ) ),
						array( 'Max Execution Time', ini_get( 'max_execution_time' ) ),
						array( 'Memory Limit', $memory_limit ),
						array( 'memory_get_usage()', $memory_get_usage . ' bytes' ),
						array( 'memory_get_peak_usage()', $memory_get_peak_usage . ' bytes' ),
						array( 'GZip', is_callable( 'gzopen' ) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>' ),
						array( 'allow_url_fopen', ini_get( 'allow_url_fopen' ) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>' ),
						array( 'Writable', is_writable( $wp_upload_dir['basedir'] ) ? '<i class="fa fa-check"></i>' : '<i class="fa fa-remove"></i>' ),
					);

					// Server
					echo '<div class="codevz_support">';
					echo '<table class="codevz_info" border="1"><tr><th>Title</th><th>Status</th></tr>';
					foreach ( $array as $key ) {
						echo '<tr>';
						echo '<td>' . $key[0] . '</td>';
						echo '<td>' . $key[1] . '</td>';
						echo '</tr>';
					}
					echo '</table>';

					// Plugins
					if ( ! function_exists( 'get_plugins' ) ) {
						require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
					}
					echo '<table class="codevz_plugins" border="1"><tr><th>Plugin</th><th>Version</th></tr>';
					foreach ( (array) get_plugins() as $plugin => $info ) {
						echo '<tr>';
						echo '<td>' . $info['Name'];
						echo is_plugin_active( $plugin ) ? '<td class="good">' . $info['Version'] . '</td>' : '<td>' . $info['Version'] . '</td>';
						echo '</tr>';
					}
					echo '</table>';

					// Options
					$options = self::option();
					if ( ! empty( $options ) ) {
						echo '<table style="margin: 50px 0 50px 4% !important" border="1"><tr><th>Option</th><th>Value</th></tr>';
						foreach ( $options as $k => $v ) {
							if ( ! empty( $v ) ) {
								echo '<tr>';
								echo '<td>' . $k . '</td>';
								echo '<td>' . ( is_array( $v ) ? serialize( $v ) : $v ) . '</td>';
								echo '</tr>';
							}
							
						}
						echo '</table>';
					}

					echo '</div>';
				}

				echo self::option( 'foot_codes' );
			});

			/**
			 *
			 * New shortcut menus to WP admin bar
			 * 
			 * @var object of WP admin bar
			 * @return object
			 * 
			 */
			add_action( 'plugins_loaded', function() {
				if ( current_user_can( 'administrator' ) && self::$is_theme ) {
					add_action( 'admin_bar_menu', function( $i ) {
						$admin = get_admin_url();
						$customize = $admin . 'customize.php?url=' . urlencode( esc_url( $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) );
						
						$i->add_node(array(
							'id' 	=> 'codevz_menu',
							'title' => esc_html__( 'Theme Options', 'codevz' ), 
							'href' 	=> $customize
						));
						$i->add_node(array(
							'parent'=> 'codevz_menu',
							'id' 	=> 'codevz_menu_demos',
							'title' => esc_html__( 'Demo Importer', 'codevz' ), 
							'href' 	=> $customize . '&autofocus[panel]=codevz_theme_options-demos',
						));
						$i->add_node(array(
							'parent'=> 'codevz_menu',
							'id' 	=> 'codevz_menu_favicon',
							'title' => esc_html__( 'Site Favicon', 'codevz' ), 
							'href' 	=> $customize . '&autofocus[panel]=title_tagline',
						));
						$i->add_node(array(
							'parent'=> 'codevz_menu',
							'id' 	=> 'codevz_menu_general',
							'title' => esc_html__( 'General', 'codevz' ), 
							'href' 	=> $customize . '&autofocus[panel]=codevz_theme_options-general',
						));
						$i->add_node(array(
							'parent'=> 'codevz_menu',
							'id' 	=> 'codevz_menu_typography',
							'title' => esc_html__( 'Typography', 'codevz' ), 
							'href' 	=> $customize . '&autofocus[panel]=codevz_theme_options-typography',
						));
						$i->add_node(array(
							'parent'=> 'codevz_menu',
							'id' 	=> 'codevz_menu_header',
							'title' => esc_html__( 'Header', 'codevz' ), 
							'href' 	=> $customize . '&autofocus[panel]=codevz_theme_options-header',
						));
						$i->add_node(array(
							'parent'=> 'codevz_menu',
							'id' 	=> 'codevz_menu_logo',
							'title' => esc_html__( 'Site Logo', 'codevz' ), 
							'href' 	=> $customize . '&autofocus[control]=codevz_theme_options[logo]',
						));
						$i->add_node(array(
							'parent'=> 'codevz_menu',
							'id' 	=> 'codevz_menu_footer',
							'title' => esc_html__( 'Footer', 'codevz' ), 
							'href' 	=> $customize . '&autofocus[panel]=codevz_theme_options-footer',
						));
						$i->add_node(array(
							'parent'=> 'codevz_menu',
							'id' 	=> 'codevz_menu_posts',
							'title' => esc_html__( 'Blog Posts', 'codevz' ), 
							'href' 	=> $customize . '&autofocus[panel]=codevz_theme_options-posts',
						));
						$i->add_node(array(
							'parent'=> 'codevz_menu',
							'id' 	=> 'codevz_menu_portfolio',
							'title' => esc_html__( 'Portfolio', 'codevz' ), 
							'href' 	=> $customize . '&autofocus[panel]=codevz_theme_options-portfolio',
						));
						$i->add_node(array(
							'parent'=> 'codevz_menu',
							'id' 	=> 'codevz_menu_product',
							'title' => esc_html__( 'WooCommerce', 'codevz' ), 
							'href' 	=> $customize . '&autofocus[panel]=codevz_theme_options-product',
						));
						$i->add_node(array(
							'parent'=> 'codevz_menu',
							'id' 	=> 'codevz_menu_custom_css',
							'title' => esc_html__( 'Additional CSS', 'codevz' ), 
							'href' 	=> $customize . '&autofocus[panel]=custom_css',
						));
						$i->add_node(array(
							'parent'=> 'codevz_menu',
							'id' 	=> 'codevz_menu_backup',
							'title' => esc_html__( 'Backup / Restore', 'codevz' ), 
							'href' 	=> $customize . '&autofocus[panel]=codevz_theme_options-backup_section',
						));
						
						$i->remove_menu( 'customize' );
					}, 99999 );
				}

				// Temporary
				if ( get_option( 'thumbnail_size_w' ) != 80 ) {
					update_option( 'thumbnail_size_w', 80 );
					update_option( 'thumbnail_size_h', 80 );
				}
			});
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
		 * Check if url contains developer request
		 * 
		 * @return boolean
		 *
		 */
		public static function isDev() {
			return ( isset( $_GET['codevz'] ) && md5( $_GET['codevz'] ) === '9dfda83f9a7d376380ec41c6e4a4b456' );
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
			}

			return $o;
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
			} else if ( function_exists( 'is_woocommerce' ) && ( is_shop() || is_woocommerce() ) ) {
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
		 * WordPress init
		 * 
		 * @return object
		 * 
		 */
		public static function init() {

			/**
			 *
			 * Menu navigation locations
			 * 
			 * @return array
			 * 
			 */
			register_nav_menus(array(
				'primary' 	=> esc_html__( 'Primary', 'codevz' ), 
				'one-page' 	=> esc_html__( 'One Page', 'codevz' ), 
				'secondary' => esc_html__( 'Secondary', 'codevz' ), 
				'footer'  	=> esc_html__( 'Footer', 'codevz' ),
				'mobile'  	=> esc_html__( 'Mobile', 'codevz' ),
				'custom-1' 	=> esc_html__( 'Custom 1', 'codevz' ), 
				'custom-2' 	=> esc_html__( 'Custom 2', 'codevz' ), 
				'custom-3' 	=> esc_html__( 'Custom 3', 'codevz' )
			));

			/**
			 *
			 * Required nicescroll for admin
			 * 
			 * @return string
			 * 
			 */
			add_action( 'admin_enqueue_scripts',  function() {
				wp_enqueue_script( 'codevz-nicescroll', CDVZ_PLUGIN_URI . 'assets/nicescroll.js', array( 'jquery' ), '', true );
			}, 1 );

			/**
			 *
			 * Show notice if visual composer is not activated
			 * 
			 * @return string
			 * 
			 */
			if ( ! function_exists( 'vc_add_shortcode_param' ) ) {
				add_action( 'admin_notices', function() { ?>
					<div class="notice notice-error">
						<p><?php esc_html_e( 'Warning: WPBakery Page Builder plugin is required for this theme.', 'codevz' ); ?></p>
					</div>
				<?php });

				return;
			}

			/**
			 *
			 * Show notice for memory limit
			 * 
			 * @return string
			 * 
			 */
			if ( (int) ini_get( 'memory_limit' ) < 128 ) {
				add_action( 'admin_notices', function() { ?>
					<div class="notice notice-error">
						<p><?php esc_html_e( 'Notice: Your server <strong>PHP Memroy Limit</strong> is not enough, Please increase it to <strong>128M</strong>', 'codevz' ); ?><br /><?php esc_html_e( 'Contact your server technical support and ask them for increasing <strong>PHP Memroy Limit</strong>.', 'codevz' ); ?></p>
					</div>
				<?php });
			}

			/**
			 *
			 * Register Post types
			 * 
			 * @return object
			 * 
			 */
			self::post_types();

			/**
			 *
			 * Enqueue and register plugin assets
			 * 
			 * @return string
			 * 
			 */
			add_action( 'wp_enqueue_scripts',  function() {
				wp_register_script( 'codevz-slick', CDVZ_PLUGIN_URI . 'assets/slick.js', array( 'jquery' ), '', true );
				wp_register_script( 'codevz-nicescroll', CDVZ_PLUGIN_URI . 'assets/nicescroll.js', array( 'jquery' ), '', true );
				wp_register_script( 'codevz-grid', CDVZ_PLUGIN_URI . 'assets/grid.js', array( 'jquery' ), '', true );
				
				wp_register_script( 'codevz-soundmanager', CDVZ_PLUGIN_URI . 'assets/soundmanager/script/soundmanager.js', array( 'jquery' ), '', true );
				wp_register_script( 'codevz-bar-ui', CDVZ_PLUGIN_URI . 'assets/soundmanager/script/bar-ui.js', array( 'jquery' ), '', true );
				wp_register_style( 'codevz-bar-ui', CDVZ_PLUGIN_URI . 'assets/soundmanager/css/bar-ui.css' );

				wp_register_script( 'codevz-tooltip', CDVZ_PLUGIN_URI . 'assets/tooltips.js', array( 'jquery' ), '', true );
				wp_register_script( 'codevz-countdown', CDVZ_PLUGIN_URI . 'assets/countdown.js', array( 'jquery' ), '', true );
				wp_register_script( 'codevz-modernizer', CDVZ_PLUGIN_URI . 'assets/modernizer.js', array( 'jquery' ), '', true );
				wp_register_script( 'codevz-360-degree', CDVZ_PLUGIN_URI . 'assets/360_degree.js', array( 'jquery' ), '', true );
				wp_register_script( 'codevz-animated-text', CDVZ_PLUGIN_URI . 'assets/animated_text.js', array( 'jquery' ), '', true );

				if ( self::option( 'nicescroll' ) ) {
					wp_enqueue_script( 'codevz-nicescroll' );
				}
			}, 1 );
			if ( ! isset( $_POST['vc_inline'] ) ) {
				add_action( 'wp_enqueue_scripts',  function() {
					wp_enqueue_script( 'codevz-plugin', CDVZ_PLUGIN_URI . 'assets/codevzplus.js', array( 'jquery' ), '', true );
					wp_enqueue_style( 'codevz-plugin', CDVZ_PLUGIN_URI . 'assets/codevzplus.css' );

					$scripts = array();
					$scripts = array(
						'cp' 	=> plugins_url( '/codevz-plus/assets/codevzplus.js' ),
						'cus' 	=> get_template_directory_uri() . '/js/custom.js',
					);

					if ( defined( 'WPCF7_VERSION' ) ) {
						$scripts['cf7'] = plugins_url( '/contact-form-7/includes/js/scripts.js' );
					}

					wp_add_inline_script( ( wp_script_is( 'codevz-custom' ) ? 'codevz-custom' : 'codevz-plugin' ), 'var cz_scripts = ' . json_encode( $scripts ) . ';', 'before' );
				}, 11 );
				add_action( 'wp_enqueue_scripts',  function() {
					if ( isset( self::$post->ID ) && ! self::$vc_editable ) {

						// Page builder styles
						$styles = get_post_meta( self::$post->ID, 'cz_sc_styles', 1 );

						// Page builder tablet styles
						$tablet = get_post_meta( self::$post->ID, 'cz_sc_styles_tablet', 1 );
						if ( $tablet ) {
							if ( self::contains( $tablet, '@media' ) ) {
								$styles .= $tablet;
							} else {
								$styles .= '@media screen and (max-width:768px){' . $tablet . '}';
							}
						}

						// Page builder mobile styles
						$mobile = get_post_meta( self::$post->ID, 'cz_sc_styles_mobile', 1 );
						if ( $mobile ) {
							if ( self::contains( $mobile, '@media' ) ) {
								$styles .= $mobile;
							} else {
								$styles .= '@media screen and (max-width:480px){' . $mobile . '}';
							}
						}

						wp_add_inline_style( 'codevz-plugin', "\n\n/* PageBuilder */" . $styles );
					}
				}, 999 );
			}

			/**
			 *
			 * Admin enqueue assets for Presets
			 * Theme colors for palettes
			 *
			 * @return string
			 * 
			 */
			add_action( 'admin_enqueue_scripts',  function( $hook ) {
				wp_add_inline_script( 'csf', 'var codevz_primary_color = "' . self::option( 'site_color', '#4e71fe' ) . '", codevz_secondary_color = "' . self::option( 'site_color_sec' ) . '";', 'before' );
			}, 99 );

			/**
			 *
			 * Custom JS/CSS for VC popup box
			 * 
			 * @return string
			 * 
			 */
			add_action( 'vc_edit_form_fields_after_render', function() {
				$body_font = self::option( '_css_body_typo' );
				$body_font = empty( $body_font[0]['font-family'] ) ? '' : $body_font[0]['font-family'];
				$body_font = explode( ':', $body_font );

				?><script type="text/javascript">
					(function($){
						$(document).ready( function(){
							$( '.wpb_edit_form_elements' ).csf_reload_script();

							$( '.vc_param_group-list' ).on( 'click', function() {
								var en = $( this );
								setTimeout(function() {
									$( '.vc_param', en ).each(function() {
										$( this ).csf_reload_script();
									});
								}, 4000 );
							});

							setTimeout(function() {
								$( '#wpb_tinymce_content_ifr' ).contents().find( 'body' ).css({
									'background': 'rgba(167, 167, 167, 0.25)',
									'font-family': '<?php echo empty( $body_font[0] ) ? 'Open Sans' : $body_font[0]; ?>'
								});
								codevz_fix_admin_fonts();
							}, 200 );
						});
					})(jQuery);
				</script>
			<?php });

			/**
			 *
			 * Enable some features for WP Editor
			 * 
			 * @param $i is array of default WP Editor features
			 * @return array
			 * 
			 */ 
			add_filter( 'mce_buttons_2', function( $i ) {
				array_shift( $i );
				array_unshift( $i, 'styleselect', 'fontselect', 'fontsizeselect', 'backcolor' );

				return $i;
			});

			/**
			 *
			 * Customize some features of WP Editor
			 * 
			 * @param $i is array of default WP Editor features values
			 * @return array
			 * 
			 */
			add_filter( 'tiny_mce_before_init', function( $i ) {
				$i['fontsize_formats'] = '6px 7px 8px 9px 10px 11px 12px 13px 14px 15px 16px 17px 18px 19px 20px 22px 24px 26px 28px 30px 32px 34px 36px 38px 40px 42px 44px 46px 48px 50px 52px 54px 56px 58px 60px 62px 64px 66px 68px 70px 72px 74px 76px 78px 80px 82px 84px 86px 88px 90px 92px 94px 96px 98px 100px 102px 104px 106px 108px 110px 120px 130px 140px 150px 160px 170px 180px 190px 200px 1em 2em 3em 4em 5em 6em 7em 8em 9em 10em 11em 12em 13em 14em 15em 16em 17em 18em 19em 20em';

				$primary_color = self::option( 'site_color', '#4e71fe' );
				$secondary_color = self::option( 'site_color_sec' );
 
 				$colors = '"000000", "Black",
                      "993300", "Burnt orange",
                      "333300", "Dark olive",
                      "003300", "Dark green",
                      "003366", "Dark azure",
                      "000080", "Navy Blue",
                      "333399", "Indigo",
                      "333333", "Very dark gray",
                      "800000", "Maroon",
                      "FF6600", "Orange",
                      "808000", "Olive",
                      "008000", "Green",
                      "008080", "Teal",
                      "0000FF", "Blue",
                      "666699", "Grayish blue",
                      "666666", "Gray",
                      "FF0000", "Red",
                      "FF9900", "Amber",
                      "99CC00", "Yellow green",
                      "339966", "Sea green",
                      "33CCCC", "Turquoise",
                      "3366FF", "Royal blue",
                      "800080", "Purple",
                      "AAAAAA", "Medium gray",
                      "FF00FF", "Magenta",
                      "FFCC00", "Gold",
                      "FFFF00", "Yellow",
                      "00FF00", "Lime",
                      "00FFFF", "Aqua",
                      "00CCFF", "Sky blue",
                      "993366", "Red violet",
                      "FFFFFF", "White",
                      "FF99CC", "Pink",
                      "FFCC99", "Peach",
                      "FFFF99", "Light yellow",
                      "CCFFCC", "Pale green",
                      "CCFFFF", "Pale cyan"';

				$colors .= ',"' . $primary_color . '", "Primary Color"';
				$colors .= $secondary_color ? ',"' . $secondary_color . '", "Secondary Color"' : '';

				// Build colour grid default+custom colors
				$i['textcolor_map'] = '[' . str_replace( '#', '', $colors ) . ']';
				$i['textcolor_rows'] = 6;

				// Fonts for WP Editor from theme options
				$i['font_formats'] = get_option( 'codevz_wp_editor_google_fonts' );

				// New style_formats
				$style_formats = array(
					array(
						'title' => esc_html__( '100 | Thin', 'codevz' ),
						'inline' => 'span',
						'styles' => array( 'font-weight' => '100' ),
						'wrapper' => false
					),
					array(
						'title' => esc_html__( '200 | Extra Light', 'codevz' ),
						'inline' => 'span',
						'styles' => array( 'font-weight' => '200' ),
						'wrapper' => false
					),
					array(
						'title' => esc_html__( '300 | Light', 'codevz' ),
						'inline' => 'span',
						'styles' => array( 'font-weight' => '300' ),
						'wrapper' => false
					),
					array(
						'title' => esc_html__( '400 | Normal', 'codevz' ),
						'inline' => 'span',
						'styles' => array( 'font-weight' => '400' ),
						'wrapper' => false
					),
					array(
						'title' => esc_html__( '500 | Medium', 'codevz' ),
						'inline' => 'span',
						'styles' => array( 'font-weight' => '500' ),
						'wrapper' => false
					),
					array(
						'title' => esc_html__( '600 | Semi Bold', 'codevz' ),
						'inline' => 'span',
						'styles' => array( 'font-weight' => '600' ),
						'wrapper' => false
					),
					array(
						'title' => esc_html__( '700 | Bold', 'codevz' ),
						'inline' => 'span',
						'styles' => array( 'font-weight' => '700' ),
						'wrapper' => false
					),
					array(
						'title' => esc_html__( '800 | Extra Bold', 'codevz' ),
						'inline' => 'span',
						'styles' => array( 'font-weight' => '800' ),
						'wrapper' => false
					),
					array(
						'title' => esc_html__( '900 | High Bold', 'codevz' ),
						'inline' => 'span',
						'styles' => array( 'font-weight' => '900' ),
						'wrapper' => false
					),
					array(
						'title' => esc_html__( 'Line height 0.6', 'codevz' ),
						'block' => 'div',
						'wrapper' => false,
						'styles' => array( 'line-height' => '0.6' )
					),
					array(
						'title' => esc_html__( 'Line height 0.8', 'codevz' ),
						'block' => 'div',
						'wrapper' => false,
						'styles' => array( 'line-height' => '0.8' )
					),
					array(
						'title' => esc_html__( 'Line height 1', 'codevz' ),
						'block' => 'div',
						'wrapper' => false,
						'styles' => array( 'line-height' => '1' )
					),
					array(
						'title' => esc_html__( 'Line height 1.1', 'codevz' ),
						'block' => 'div',
						'wrapper' => false,
						'styles' => array( 'line-height' => '1.1' )
					),
					array(
						'title' => esc_html__( 'Line height 1.2', 'codevz' ),
						'block' => 'div',
						'wrapper' => false,
						'styles' => array( 'line-height' => '1.2' )
					),
					array(
						'title' => esc_html__( 'Line height 1.3', 'codevz' ),
						'block' => 'div',
						'wrapper' => false,
						'styles' => array( 'line-height' => '1.3' )
					),
					array(
						'title' => esc_html__( 'Line height 1.4', 'codevz' ),
						'block' => 'div',
						'wrapper' => false,
						'styles' => array( 'line-height' => '1.4' )
					),
					array(
						'title' => esc_html__( 'Line height 1.5', 'codevz' ),
						'block' => 'div',
						'wrapper' => false,
						'styles' => array( 'line-height' => '1.5' )
					),
					array(
						'title' => esc_html__( 'Line height 1.6', 'codevz' ),
						'block' => 'div',
						'wrapper' => false,
						'styles' => array( 'line-height' => '1.6' )
					),
					array(
						'title' => esc_html__( 'Line height 1.7', 'codevz' ),
						'block' => 'div',
						'wrapper' => false,
						'styles' => array( 'line-height' => '1.7' )
					),
					array(
						'title' => esc_html__( 'Line height 1.8', 'codevz' ),
						'block' => 'div',
						'wrapper' => false,
						'styles' => array( 'line-height' => '1.8' )
					),
					array(
						'title' => esc_html__( 'Line height 1.9', 'codevz' ),
						'block' => 'div',
						'wrapper' => false,
						'styles' => array( 'line-height' => '1.9' )
					),
					array(
						'title' => esc_html__( 'Line height 2', 'codevz' ),
						'block' => 'div',
						'wrapper' => false,
						'styles' => array( 'line-height' => '2' )
					),
					array(
						'title' => esc_html__( 'Letter spacing -2px', 'codevz' ),
						'inline' => 'span',
						'wrapper' => false,
						'styles' => array( 'letter-spacing' => '-2px' )
					),
					array(
						'title' => esc_html__( 'Letter spacing -1px', 'codevz' ),
						'inline' => 'span',
						'wrapper' => false,
						'styles' => array( 'letter-spacing' => '-1px' )
					),
					array(
						'title' => esc_html__( 'Letter spacing 1px', 'codevz' ),
						'inline' => 'span',
						'wrapper' => false,
						'styles' => array( 'letter-spacing' => '1px' )
					),
					array(
						'title' => esc_html__( 'Letter spacing 2px', 'codevz' ),
						'inline' => 'span',
						'wrapper' => false,
						'styles' => array( 'letter-spacing' => '2px' )
					),
					array(
						'title' => esc_html__( 'Letter spacing 3px', 'codevz' ),
						'inline' => 'span',
						'wrapper' => false,
						'styles' => array( 'letter-spacing' => '3px' )
					),
					array(
						'title' => esc_html__( 'Letter spacing 4px', 'codevz' ),
						'inline' => 'span',
						'wrapper' => false,
						'styles' => array( 'letter-spacing' => '4px' )
					),
					array(
						'title' => esc_html__( 'Letter spacing 5px', 'codevz' ),
						'inline' => 'span',
						'wrapper' => false,
						'styles' => array( 'letter-spacing' => '5px' )
					),
					array(
						'title' => esc_html__( 'Letter spacing 6px', 'codevz' ),
						'inline' => 'span',
						'wrapper' => false,
						'styles' => array( 'letter-spacing' => '6px' )
					),
					array(
						'title' => esc_html__( 'Letter spacing 7px', 'codevz' ),
						'inline' => 'span',
						'wrapper' => false,
						'styles' => array( 'letter-spacing' => '7px' )
					),
					array(
						'title' => esc_html__( 'Letter spacing 8px', 'codevz' ),
						'inline' => 'span',
						'wrapper' => false,
						'styles' => array( 'letter-spacing' => '8px' )
					),
					array(
						'title' => esc_html__( 'Letter spacing 10px', 'codevz' ),
						'inline' => 'span',
						'wrapper' => false,
						'styles' => array( 'letter-spacing' => '10px' )
					),
					array(
						'title' => esc_html__( 'Letter spacing 12px', 'codevz' ),
						'inline' => 'span',
						'wrapper' => false,
						'styles' => array( 'letter-spacing' => '12px' )
					),
					array(
						'title' => esc_html__( 'Letter spacing 15px', 'codevz' ),
						'inline' => 'span',
						'wrapper' => false,
						'styles' => array( 'letter-spacing' => '15px' )
					),
					array(
						'title' => esc_html__( 'Letter spacing 20px', 'codevz' ),
						'inline' => 'span',
						'wrapper' => false,
						'styles' => array( 'letter-spacing' => '20px' )
					),
					array(
						'title' => esc_html__( 'Margin 0px', 'codevz' ),
						'inline' => 'span',
						'wrapper' => false,
						'styles' => array( 'margin' => '0px' )
					),
					array(
						'title' => esc_html__( 'Margin top 10px', 'codevz' ),
						'inline' => 'span',
						'wrapper' => false,
						'styles' => array( 'margin-top' => '10px', 'display' => 'inline-block' )
					),
					array(
						'title' => esc_html__( 'Margin top 20px', 'codevz' ),
						'inline' => 'span',
						'wrapper' => false,
						'styles' => array( 'margin-top' => '20px', 'display' => 'inline-block' )
					),
					array(
						'title' => esc_html__( 'Margin top 30px', 'codevz' ),
						'inline' => 'span',
						'wrapper' => false,
						'styles' => array( 'margin-top' => '30px', 'display' => 'inline-block' )
					),
					array(
						'title' => esc_html__( 'Margin bottom 10px', 'codevz' ),
						'inline' => 'span',
						'wrapper' => false,
						'styles' => array( 'margin-bottom' => '10px', 'display' => 'inline-block' )
					),
					array(
						'title' => esc_html__( 'Margin bottom 20px', 'codevz' ),
						'inline' => 'span',
						'wrapper' => false,
						'styles' => array( 'margin-bottom' => '20px', 'display' => 'inline-block' )
					),
					array(
						'title' => esc_html__( 'Margin bottom 30px', 'codevz' ),
						'inline' => 'span',
						'wrapper' => false,
						'styles' => array( 'margin-bottom' => '30px', 'display' => 'inline-block' )
					),
					array(
						'title'  => esc_html__( 'Highlight', 'codevz' ),
						'inline' => 'span',
						'classes' => 'cz_highlight',
						'styles' => array(
							'margin' 		=> '0 2px',
							'padding' 		=> '1px 7px 2px',
							'background' 	=> 'rgba(167, 167, 167, 0.26)',
							'border-radius' => '2px',
						),
						'wrapper' => false
					),
					array(
						'title'  => esc_html__( 'Border solid', 'codevz' ),
						'inline' => 'span',
						'classes' => 'cz_brsolid',
						'styles' => array(
							'margin' 		=> '0 2px',
							'padding' 		=> '4px 8px 5px',
							'border' 		=> '1px solid',
							'border-radius' => '2px',
						),
						'wrapper' => false
					),
					array(
						'title'  => esc_html__( 'Border dotted', 'codevz' ),
						'inline' => 'span',
						'classes' => 'cz_brdotted',
						'styles' => array(
							'margin' 		=> '0 2px',
							'padding' 		=> '4px 8px 5px',
							'border' 		=> '1px dotted',
							'border-radius' => '2px',
						),
						'wrapper' => false
					),
					array(
						'title'  => esc_html__( 'Border dashed', 'codevz' ),
						'inline' => 'span',
						'classes' => 'cz_brdashed',
						'styles' => array(
							'margin' 		=> '0 2px',
							'padding' 		=> '4px 8px 5px',
							'border' 		=> '1px dashed',
							'border-radius' => '2px',
						),
						'wrapper' => false
					),
					array(
						'title'  => esc_html__( 'Underline', 'codevz' ),
						'inline' => 'span',
						'classes' => 'cz_underline',
						'styles' => array(
							'margin' 		=> '0 2px',
							'padding' 		=> '1px 0 2px',
							'border-bottom' => '1px solid'
						),
						'wrapper' => false
					),
					array(
						'title'  => esc_html__( 'Underline Dashed', 'codevz' ),
						'inline' => 'span',
						'classes' => 'cz_underline cz_underline_dashed',
						'styles' => array(
							'margin' 		=> '0 2px',
							'padding' 		=> '1px 0 2px',
							'border-bottom' => '1px dashed'
						),
						'wrapper' => false
					),
					array(
						'title'  => esc_html__( 'Topline', 'codevz' ),
						'inline' => 'span',
						'classes' => 'cz_topline',
						'styles' => array(
							'margin' 		=> '0 2px',
							'padding' 		=> '1px 0 2px',
							'border-top' 	=> '1px solid'
						),
						'wrapper' => false
					),
					array(
						'title'  => esc_html__( 'Topline Dashed', 'codevz' ),
						'inline' => 'span',
						'classes' => 'cz_topline cz_topline_dashed',
						'styles' => array(
							'margin' 		=> '0 2px',
							'padding' 		=> '1px 0 2px',
							'border-top' 	=> '1px dashed'
						),
						'wrapper' => false
					),
					array(
						'title'  => esc_html__( 'Blockquote Center', 'codevz' ),
						'inline' => 'span',
						'classes' => 'blockquote',
						'styles' => array(
							'width' 		=> '75%',
							'margin' 		=> '0 auto',
							'display' 		=> 'table',
							'text-align' 	=> 'center',
						),
						'wrapper' => false
					),
					array(
						'title'  => esc_html__( 'Blockquote Left', 'codevz' ),
						'inline' => 'span',
						'classes' => 'blockquote',
						'styles' => array(
							'float' 		=> 'left',
							'width' 		=> '40%',
							'margin' 		=> '0 20px 20px 0',
						),
						'wrapper' => false
					),
					array(
						'title'  => esc_html__( 'Blockquote Right', 'codevz' ),
						'inline' => 'span',
						'classes' => 'blockquote',
						'styles' => array(
							'float' 		=> 'right',
							'width' 		=> '40%',
							'margin' 		=> '0 0 20px 20px',
						),
						'wrapper' => false
					),	
					array(
						'title'  => esc_html__( 'Float Right', 'codevz' ),
						'inline' => 'span',
						'styles' => array( 'float' => 'right' ),
						'wrapper' => false
					),
					array(
						'title'  => esc_html__( 'Dropcap', 'codevz' ),
						'inline' => 'span',
						'classes' => 'cz_dropcap',
						'styles' => array(
							'float' 		=> self::$is_rtl ? 'right' : 'left',
							'margin' 		=> self::$is_rtl ? '5px 0 0 12px' : '5px 12px 0 0',
							'width' 		=> '2em',
							'height' 		=> '2em',
							'line-height' 	=> '2em',
							'text-align' 	=> 'center',
						),
						'wrapper' => false
					),
					array(
						'title'  => esc_html__( 'Dropcap Border', 'codevz' ),
						'inline' => 'span',
						'classes' => 'cz_dropcap',
						'styles' => array(
							'float' 		=> self::$is_rtl ? 'right' : 'left',
							'margin' 		=> self::$is_rtl ? '5px 0 0 12px' : '5px 12px 0 0',
							'width' 		=> '2em',
							'height' 		=> '2em',
							'line-height' 	=> '2em',
							'text-align' 	=> 'center',
							'border' 		=> '2px solid',
						),
						'wrapper' => false
					),
					array(
						'title'  => esc_html__( 'Sup', 'codevz' ),
						'inline' => 'sup',
						'styles' => array(),
						'wrapper' => false
					),
					array(
						'title'  => esc_html__( 'Sub', 'codevz' ),
						'inline' => 'sub',
						'styles' => array(),
						'wrapper' => false
					),
					array(
						'title'  => esc_html__( 'Small', 'codevz' ),
						'inline' => 'small',
						'styles' => array(),
						'wrapper' => false
					),
				);
				$i['style_formats'] = json_encode( $style_formats );

				return $i;
			});

			/**
			 *
			 * New Params for VC
			 * 
			 * @return object
			 * 
			 */
			// Title Info Param
			vc_add_shortcode_param( 
				'cz_title', 
				function( $s, $v ) {
					$c = empty( $s['class'] ) ? '' : ' class="' . $s['class'] . '"';
					$u = empty( $s['url'] ) ? '' : '<a href="' . $s['url'] . '" target="_blank">';
					return $u . '<h4' . $c . '>' . $s['content'] . '</h4>' . ( $u ? '</a>' : '' ) . '<input type="hidden" name="' . $s['param_name'] . '" class="wpb_vc_param_value ' . $s['param_name'] . ' '.$s['type'].'_field" value="'.$v.'" />';
				}
			);

			// Unique ID Param
			vc_add_shortcode_param( 
				'cz_sc_id', 
				function( $s, $v ) {
					return '<input type="hidden" name="' . $s['param_name'] . '" class="wpb_vc_param_value ' . $s['param_name'] . ' '.$s['type'].'_field" value="'.$v.'" />';
				}
			);

			// Hidden field Param
			vc_add_shortcode_param( 
				'cz_hidden', 
				function( $s, $v ) {
					return '<input type="hidden" name="' . $s['param_name'] . '" class="wpb_vc_param_value ' . $s['param_name'] . ' '.$s['type'].'_field" value="'.$v.'" />';
				}
			);

			// Presets Param
			vc_add_shortcode_param( 
				'cz_presets', 
				function( $s, $v ) {
					return '<div class="cz_presets clr ' . $s['class'] . '" data-presets="' . $s['param_name'] . '"><div class="cz_presets_loader"></div></div>';
				}
			);

			// StyleKit Param
			vc_add_shortcode_param( 
				'cz_sk', 
				function( $s, $v ) {
					$hover = isset( $s['hover_id'] ) ? ' data-hover_id="' . $s['hover_id'] . '"' : '';
					$out = '<div class="cz_sk clr"><input type="hidden" name="'. $s['param_name'] . '"' . $hover . ' value="' . $v . '" class="csf-onload wpb_vc_param_value ' . esc_attr( $s['param_name'] ) .' '. esc_attr( $s['type'] ) . '" data-selector="' . ( isset( $s['selector'] ) ? $s['selector'] : '' ) . '" data-fields="' . implode( ' ', $s['settings'] ) . '" />';
					$out .= '<a href="#" class="button cz_sk_btn"><span class="cz_skico cz_skico_vc"></span>' . $s['button'] . '</a></div>';
					return $out;
				}
			);

			// MP3 Upload Param
			vc_add_shortcode_param( 
				'cz_upload', 
				function( $s, $v ) {
					$f = array(
						'id'    => esc_attr( $s['param_name'] ),
						'name'  => esc_attr( $s['param_name'] ),
						'type'  => 'upload',
						'title' => '',
						'attributes' => array(
							'class' => 'csf-onload wpb_vc_param_value '.esc_attr( $s['param_name'] ) .' '. esc_attr( $s['type'] ).''
						),
						'settings'   => array(
							'upload_type'  => 'audio/mpeg',
							'frame_title'  => 'Upload / Select',
							'insert_title' => 'Insert',
						),
					);

					if ( function_exists('csf_add_field') ) {
						return '<div class="csf-onload">' . csf_add_field( $f, $v ) . '</div>';
					} else {
						return '<div class="my_param_block">'
							.'<input name="' . esc_attr( $s['param_name'] ) . '" class="wpb_vc_param_value wpb-textinput ' .
							esc_attr( $s['param_name'] ) . ' ' .
							esc_attr( $s['type'] ) . '_field" type="text" value="' . esc_attr( $v ) . '" />' .
							'</div>';
					}
				}
			);

			// Icon Selector Param
			vc_add_shortcode_param( 
				'cz_icon', 
				function( $s, $v ) {
					$f = array(
						'id'    => esc_attr( $s['param_name'] ),
						'name'  => esc_attr( $s['param_name'] ),
						'type'  => 'icon',
						'title' => '',
						'after'	=> '<input type="hidden" name="'.$s['param_name'].'" class="wpb_vc_param_value '.$s['param_name'].' '.$s['type'].'_field" value="'.$v.'" />',
						'attributes' => array(
							'class' => 'csf-onload wpb_vc_param_value '.esc_attr( $s['param_name'] ) .' '. esc_attr( $s['type'] ).''
						),
					);

					if ( function_exists('csf_add_field') ) {
						return '<div class="csf-onload">' . csf_add_field( $f, $v ) . '</div>';
					} else {
						return '<div class="my_param_block">'
							.'<input name="' . esc_attr( $s['param_name'] ) . '" class="wpb_vc_param_value wpb-textinput ' .
							esc_attr( $s['param_name'] ) . ' ' .
							esc_attr( $s['type'] ) . '_field" type="text" value="' . esc_attr( $v ) . '" />' .
							'</div>';
					}
				}
			);

			// Image Select Param
			vc_add_shortcode_param( 
				'cz_image_select', 
				function( $s, $v ) {
					$f = array(
						'id'    => esc_attr( $s['param_name'] ),
						'name'  => esc_attr( $s['param_name'] ),
						'type'  => 'image_select',
						'options' => isset( $s['options'] ) ? $s['options'] : array(),
						'radio' => true,
						'title' => '',
						'after'	=> '<input type="hidden" name="' . $s['param_name'] . '" class="wpb_vc_param_value ' . $s['param_name'] . ' '.$s['type'].'_field" value="'.$v.'" />',
						'attributes' => array(
							'class' 			=> 'csf-onload',
							'data-depend-id' 	=> esc_attr( $s['param_name'] )
						),
					);

					if ( function_exists('csf_add_field') ) {
						return '<div class="csf-onload">' . csf_add_field( $f, $v ) . '</div>';
					} else {
						return '<div class="my_param_block">'
							.'<input name="' . esc_attr( $s['param_name'] ) . '" class="wpb_vc_param_value wpb-textinput ' .
							esc_attr( $s['param_name'] ) . ' ' .
							esc_attr( $s['type'] ) . '_field" type="text" value="' . esc_attr( $v ) . '" />' .
							'</div>';
					}
				}
			);

			// Input Slider Param
			vc_add_shortcode_param( 
				'cz_slider', 
				function( $s, $v ) {
					$f = array(
						'id'    => esc_attr( $s['param_name'] ),
						'name'  => esc_attr( $s['param_name'] ),
						'type'  => 'slider',
						'options' => isset( $s['options'] ) ? $s['options'] : array( 'unit' => 'px', 'step' => 1, 'min' => 0, 'max' => 120 ),
						'title' => '',
						'after'	=> '<input type="hidden" name="'.$s['param_name'].'" class="wpb_vc_param_value '.$s['param_name'].' '.$s['type'].'_field" value="'.$v.'" />',
						'attributes' => array(
							'class' => 'csf-onload wpb_vc_param_value '.esc_attr( $s['param_name'] ) .' '. esc_attr( $s['type'] ).''
						),
					);

					if ( function_exists('csf_add_field') ) {
						return '<div class="csf-onload">' . csf_add_field( $f, $v ) . '</div>';
					} else {
						return '<div class="my_param_block">'
							.'<input name="' . esc_attr( $s['param_name'] ) . '" class="wpb_vc_param_value wpb-textinput ' .
							esc_attr( $s['param_name'] ) . ' ' .
							esc_attr( $s['type'] ) . '_field" type="text" value="' . esc_attr( $v ) . '" />' .
							'</div>';
					}
				}
			);

			/**
			 *
			 * Filter for moving animation param into new tab Animation
			 * 
			 * @param $i is default css_animation settings
			 * @return array
			 * 
			 */
			add_filter( 'vc_map_add_css_animation', function( $i ) {
				$i['group'] = esc_html__( 'Advanced', 'codevz' );
				return $i;
			});

			/**
			 *
			 * Useful Shortcodes
			 * 
			 * @return string
			 * 
			 */
			add_shortcode( 'br', function( $a, $c = '' ) {
				return '<br class="clr" />';
			});
			add_shortcode( 'codevz_year', function( $a, $c = '' ) {
				return current_time( 'Y' );
			});
			add_shortcode( 'cz_current_year', function( $a, $c = '' ) {
				return current_time( 'Y' );
			});

			/**
			 *
			 * Load VC Templates
			 * 
			 * @return array
			 * 
			 */
			if ( ! self::option( 'vc_disable_templates' ) && class_exists( 'Codevz_Templates' ) ) {
				add_action( 'vc_load_default_templates_action', function() {
					foreach ( Codevz_Templates::get() as $t ) {
						$data               = array();
						$data['name']       = $t['f'];
						$data['custom_class'] = $t['f'];
						$data['content']    = <<<CONTENT
	{$t['c']}[vc_row][vc_column][cz_gap height="100px"][/vc_column][/vc_row]
CONTENT;
						vc_add_default_templates( $data );
					}
				});
			}

			/**
			 *
			 * VC Templates Filters
			 * 
			 * @return string
			 * 
			 */
			add_action( 'admin_footer', function() {
				?><ul class="cz_vc_filters hidden" data-tab-title="<?php esc_html_e( 'Premium Templates', 'codevz' ); ?>">
					<li data-filter="vc_ui-template" class="cz_active"><?php esc_html_e( 'All Templates', 'codevz' ); ?></li>
					<li data-filter="about"><?php esc_html_e( 'About', 'codevz' ); ?></li>
					<li data-filter="accordion"><?php esc_html_e( 'Accordion', 'codevz' ); ?></li>
					<li data-filter="banner"><?php esc_html_e( 'Banner', 'codevz' ); ?></li>
					<li data-filter="blog"><?php esc_html_e( 'Blog', 'codevz' ); ?></li>
					<li data-filter="cta"><?php esc_html_e( 'Call to Action', 'codevz' ); ?></li>
					<li data-filter="clients"><?php esc_html_e( 'Clients', 'codevz' ); ?></li>
					<li data-filter="contact"><?php esc_html_e( 'Contact', 'codevz' ); ?></li>
					<li data-filter="counter"><?php esc_html_e( 'Counter', 'codevz' ); ?></li>
					<li data-filter="countdown"><?php esc_html_e( 'Countdown', 'codevz' ); ?></li>
					<li data-filter="gallery"><?php esc_html_e( 'Gallery', 'codevz' ); ?></li>
					<li data-filter="googlemap"><?php esc_html_e( 'Google Map', 'codevz' ); ?></li>
					<li data-filter="hero"><?php esc_html_e( 'Hero Section', 'codevz' ); ?></li>
					<li data-filter="pricing"><?php esc_html_e( 'Pricing', 'codevz' ); ?></li>
					<li data-filter="quote"><?php esc_html_e( 'Quote', 'codevz' ); ?></li>
					<li data-filter="services"><?php esc_html_e( 'Services', 'codevz' ); ?></li>
					<li data-filter="skills"><?php esc_html_e( 'Skill', 'codevz' ); ?></li>
					<li data-filter="shop"><?php esc_html_e( 'Shop', 'codevz' ); ?></li>
					<li data-filter="social"><?php esc_html_e( 'Social Icons', 'codevz' ); ?></li>
					<li data-filter="tabs"><?php esc_html_e( 'Tabs', 'codevz' ); ?></li>
					<li data-filter="team"><?php esc_html_e( 'Team', 'codevz' ); ?></li>
					<li data-filter="testimonial"><?php esc_html_e( 'Testimonial', 'codevz' ); ?></li>
					<li data-filter="video"><?php esc_html_e( 'Video', 'codevz' ); ?></li>
					<li data-filter="misc"><?php esc_html_e( 'Misc', 'codevz' ); ?></li>
				</ul><?php
			});

			/**
			 *
			 * Add loop animations to vc animations list
			 * 
			 * @return string
			 * 
			 */
			add_filter( 'vc_param_animation_style_list', function( $i ) {
				return wp_parse_args( array(
					array(
						'label' => esc_html__( 'Loop Animations', 'codevz' ),
						'values' => array(
							esc_html__( 'Spinner', 'codevz' ) => array(
								'value' => 'cz_loop_spinner',
								'type' => 'in',
							),
							esc_html__( 'Pulse', 'codevz' ) => array(
								'value' => 'cz_loop_pulse',
								'type' => 'in',
							),
							esc_html__( 'Tada', 'codevz' ) => array(
								'value' => 'cz_loop_tada',
								'type' => 'in',
							),
							esc_html__( 'Flash', 'codevz' ) => array(
								'value' => 'cz_loop_flash',
								'type' => 'in',
							),
							esc_html__( 'Swing', 'codevz' ) => array(
								'value' => 'cz_loop_swing',
								'type' => 'in',
							),
							esc_html__( 'Jello', 'codevz' ) => array(
								'value' => 'cz_loop_jello',
								'type' => 'in',
							),
							esc_html__( 'Animation 1', 'codevz' ) => array(
								'value' => 'cz_infinite_anim_1',
								'type' => 'in',
							),
							esc_html__( 'Animation 2', 'codevz' ) => array(
								'value' => 'cz_infinite_anim_2',
								'type' => 'in',
							),
							esc_html__( 'Animation 3', 'codevz' ) => array(
								'value' => 'cz_infinite_anim_3',
								'type' => 'in',
							),
							esc_html__( 'Animation 4', 'codevz' ) => array(
								'value' => 'cz_infinite_anim_4',
								'type' => 'in',
							),
							esc_html__( 'Animation 5', 'codevz' ) => array(
								'value' => 'cz_infinite_anim_5',
								'type' => 'in',
							),
						),
					),
					array(
						'label' => esc_html__( 'Block Reveal', 'codevz' ),
						'values' => array(
							esc_html__( 'Right', 'codevz' ) => array(
								'value' => 'cz_brfx_right',
								'type' => 'in',
							),
							esc_html__( 'Left', 'codevz' ) => array(
								'value' => 'cz_brfx_left',
								'type' => 'in',
							),
							esc_html__( 'Up', 'codevz' ) => array(
								'value' => 'cz_brfx_up',
								'type' => 'in',
							),
							esc_html__( 'Down', 'codevz' ) => array(
								'value' => 'cz_brfx_down',
								'type' => 'in',
							),
						),
					),
				), $i );
			});

			/**
			 *
			 * Plugin url for VC Templates
			 * 
			 * @return string
			 * 
			 */
			add_action( 'admin_head', function() {
				echo '<script type="text/javascript">var CDVZ_PLUGIN_URI = "' . get_template_directory_uri() . '/config/templates";(function($){$(document).ready( function(){if(typeof codevz_fix_admin_fonts!=="undefined"){codevz_fix_admin_fonts();}});})(jQuery);</script>';
			});

			/**
			 *
			 * Plugin Languages
			 * 
			 * @return -
			 * 
			 */
			load_textdomain( 'codevz', CDVZ_PLUGIN_DIR .'/lang/'. get_locale() .'.mo' );
		}

		/**
		 *
		 * Generates unique ID
		 * 
		 * @return string
		 * 
		 */
		public static function uniqid( $p = 'cz_' ) {
			return $p . rand( 11111, 99999 );
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
		 * Shortcode output
		 * 
		 * @param $atts, content and live js functions names
		 * @return string|null
		 * 
		 */
		public static function _out( $a, $c = '', $s = '' ) {

			// Parallax
			$m = $p = '';
			$ph = empty( $a['parallax_h'] ) ? '' : $a['parallax_h'];
			$pp = empty( $a['parallax'] ) ? '' : $a['parallax'];
			if ( ! empty( $a['mparallax'] ) && self::contains( $ph, 'mouse' ) ) {
				$m = '<div class="cz_mparallax_' . $a['mparallax'] . '">';
			}
			if ( $pp ) {
				$d = ( $ph == 'true' || $ph === 'truemouse' ) ? 'h' : 'v';
				$p = '<div class="clr cz_parallax_' . $d . '_' . $pp . '">';
			}

			// Front-end JS
			if ( self::$vc_editable ) {
				$o = '';
				foreach ( (array) $s as $v ) {
					$o .= self::contains( $v, ')' ) ? 'Codevz_Plus.' . $v . ';' : ( $v ? 'Codevz_Plus.' . $v . '();' : '' );
				}
				$c .= '<script type="text/javascript">if(typeof Codevz_Plus!=="undefined"){' . $o . 'Codevz_Plus.parallax();Codevz_Plus.runScroll();Codevz_Plus.fix_wp_editor_google_fonts();}</script>';
				$p = $p ? $p : '<div class="cz_wrap">';
			}

			return $m . $p . $c . ( $p ? '</div>' : '' ) . ( $m ? '</div>' : '' );
		}

		/**
		 *
		 * Generate inline data style or style tag depend on define
		 * 
		 * @param CSS
		 * @return string|null
		 * 
		 */
		public static function data_stlye( &$d = '', &$t = '', &$m = '' ) {
			$out = '';

			// Page builder styles
			$d = empty( $d ) ? '' : $d;

			// Page builder tablet styles
			if ( ! empty( $t ) && substr( $t, 0, 1 ) !== '@' ) {
				$t = '@media screen and (max-width:768px){' . $t . '}';
			}

			// Page builder mobile styles
			if ( ! empty( $m ) && substr( $m, 0, 1 ) !== '@' ) {
				$m = '@media screen and (max-width:480px){' . $m . '}';
			}

			if ( ! self::$is_admin && ! self::$vc_editable ) {
				$out .= ( $d || $t || $m ) ? " data-cz-style='" . $d . $t . $m . "'" : '';
			} else {
				$out .= $d ? '><style class="cz_d_css">' . $d . "</style" : '';
				$out .= $t ? '><style class="cz_t_css">' . $t . "</style" : '';
				$out .= $m ? '><style class="cz_m_css">' . $m . "</style" : '';
			}

			return $out;
		}

		/**
		 *
		 * Generate titl data attributes for shortcode
		 * 
		 * @param $atts array
		 * @return string|null
		 * 
		 */
		public static function tilt( $a ) {
			if ( ! empty( $a['tilt'] ) ) {
				$o = ' data-tilt';
				$o .= ( $a['glare'] != '0' ) ? ' data-tilt-maxGlare="' . $a['glare'] . '" data-tilt-glare="true"' : '';
				$o .= ( $a['scale'] != '1' ) ? ' data-tilt-scale="' . $a['scale'] . '"' : '';
				//$o .= empty( $a['max'] ) ? '' : ' data-tilt-max="' . $a['max'] . '"';

				return $o;
			}
		}

		/**
		 *
		 * Generate class attribute for element related to $atts
		 * 
		 * @param $atts array and classes array
		 * @return string|null
		 * 
		 */
		public static function classes( $a, $o = array(), $i = 0 ) {
			$o[] = $i ? '' : esc_attr( $a['class'] );
			$o[] = empty( $a['hide_on_t'] ) ? '' : 'hide_on_tablet';
			$o[] = empty( $a['hide_on_m'] ) ? '' : 'hide_on_mobile';

			if ( ! empty( $a['css_animation'] ) && $a['css_animation'] !== 'none' ) {
				wp_enqueue_script( 'waypoints' );
				wp_enqueue_style( 'animate-css' );
				// wpb_' . $a['css_animation'] . '
				$o[] = 'wpb_animate_when_almost_visible ' . $a['css_animation'];
			}

			return ' class="' . implode( ' ', array_filter( $o ) ) . '"';
		}

		/**
		 *
		 * Generate link attributes for element related to vc_build_link array
		 * 
		 * @param vc_build_link array
		 * @return string|null
		 * 
		 */
		public static function link_attrs( $a = '' ) {
			if ( $a ) {
				$a = vc_build_link( $a );
				$url = empty( $a['url'] ) ? '' : ' href="' . esc_url( $a['url'] ) . '"';
				$rel = empty( $a['rel'] ) ? '' : ' rel="' . esc_attr( $a['rel'] ) . '"';
				$title = empty( $a['title'] ) ? '' : ' title="' . esc_attr( $a['title'] ) . '"';
				$target = empty( $a['target'] ) ? '' : ' target="' . esc_attr( $a['target'] ) . '"';

				return $url . $rel . $title . $target;
			} else {
				return ' href="#"';
			}
		}

		/**
		 *
		 * Lazyload src attributes
		 * 
		 * @return string
		 *
		 */
		public static function lazyload( $c ) {
			$is_cart = ( function_exists( 'is_cart' ) && is_cart() );

			// Skip feeds, previews, mobile, done before
			if ( self::$is_admin || is_feed() || is_preview() || $is_cart || ! empty( $_GET ) ) {
				return $c;
			}

			preg_match_all( '/<img(.*?)>/', $c, $matches, PREG_SET_ORDER, 0);
			foreach ( $matches as $key ) {
				if ( isset( $key[0] ) && ! self::contains( $key[0], 'data:image' ) && ! self::contains( $key[0], 'data-ww=' ) && ! self::contains( $key[0], 'data-bgposition=' ) ) {
					$new = preg_replace( '/ src=/', ' src="data:image/svg+xml,%3Csvg%20xmlns%3D&#39;http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg&#39;%20width=&#39;_w_&#39;%20height=&#39;_h_&#39;%20viewBox%3D&#39;0%200%20_w_%20_h_&#39;%2F%3E" data-czlz data-src=', $key[0] );

					preg_match_all( '/(?<=width="|height="|width=\'|height=\')(\d*)/', $new, $matches );
					if ( isset( $matches[0][0] ) && isset( $matches[0][1] ) ) {
						$new = str_replace( array( '_w_', '_h_' ), array( $matches[0][0], $matches[0][1] ), $new );
					}

					$c = str_replace( $key[0], $new, $c );
				}
			}

			return str_replace( 'srcset', 'data-srcset', str_replace( 'sizes=', 'data-sizes=', $c ) );
		}

		/**
		 *
		 * Remove query args for better seo, caching systems
		 * 
		 * @return string
		 *
		 */
		public static function remove_query_args( $i ) {
			return esc_url( remove_query_arg( 'ver', $i ) );
		}

		/**
		 *
		 * Remove type attr from scripts and stylesheets
		 * 
		 * @return string
		 *
		 */
		public static function remove_type_attr( $t, $h ) {
			return preg_replace( "/ type=['\"]text\/(javascript|css)['\"]/", '', $t );
		}

		/**
		 *
		 * Maintenance mode redirect
		 * 
		 * @return string
		 *
		 */
		public static function maintenance_mode( $i ) {

			$mt = self::option( 'maintenance_mode' );
			if ( $mt ) {
				$mt = get_page_by_title( $mt, 'object', 'page' );
				if ( $mt && ! is_user_logged_in() && ! is_page( $mt ) ) {
					wp_redirect( get_the_permalink( $mt ) );
					exit;
				}
			}

			return $i;
		}

		/**
		 *
		 * Generate social icons
		 * 
		 * @return string
		 *
		 */
		public static function social( $out = '' ) {

			$social = self::option( 'social' );
			if ( is_array( $social ) ) {
				$tooltip = self::option( 'social_tooltip' );
				$tooltip = $tooltip ? ' ' . $tooltip : '';
				$social_inline_title = self::option( 'social_inline_title' ) ? ' cz_social_inline_title' : '';
				$out .= '<div class="cz_social ' . esc_attr( self::option( 'social_color_mode' ) . ' ' . self::option( 'social_hover_fx' ) . $social_inline_title . $tooltip ) . '">';
				foreach ( $social as $soci ) {
					$social_link_class = 'cz-' . str_replace( array( 'fa ', 'fa-', '-square', '-official', '-circle' ), '', esc_attr( $soci['icon'] ) );
					$out .= '<a class="' . esc_attr( $social_link_class ) . '" href="' . esc_url( $soci['link'] ) . '" ' . ( $tooltip ? 'data-' : '' ) . 'title="' . esc_attr( $soci['title'] ) . '" target="_blank"><i class="' . esc_attr( $soci['icon'] ) . '"></i><span>' . esc_html( $soci['title'] ) . '</span></a>';
				}
				$out .= '</div>';
			}

			return $out;
		}

		/**
		 *
		 * Content box effects
		 * 
		 * @return array
		 * 
		 */
		public static function fx( $hover = '' ) {
			$i = array(
				esc_html__( 'Select', 'codevz' ) 		=> '',
				esc_html__( 'Zoom 1', 'codevz') 		=> 'fx_zoom_0' . $hover,
				esc_html__( 'Zoom 2', 'codevz') 		=> 'fx_zoom_1' . $hover,
				esc_html__( 'Zoom 3', 'codevz') 		=> 'fx_zoom_2' . $hover,
				esc_html__( 'Move up', 'codevz') 		=> 'fx_up' . $hover,
				esc_html__( 'Move right', 'codevz') 	=> 'fx_right' . $hover,
				esc_html__( 'Move down', 'codevz') 		=> 'fx_down' . $hover,
				esc_html__( 'Move left', 'codevz') 		=> 'fx_left' . $hover,
				esc_html__( 'Border inner', 'codevz') 	=> 'fx_inner_line' . $hover,
				esc_html__( 'Grayscale', 'codevz') 		=> 'fx_grayscale' . $hover,
				esc_html__( 'Remove Grayscale', 'codevz') => 'fx_remove_grayscale' . $hover,
				esc_html__( 'Skew left', 'codevz') 		=> 'fx_skew_left' . $hover,
				esc_html__( 'Skew right', 'codevz') 	=> 'fx_skew_right' . $hover,
				esc_html__( 'Bob loop', 'codevz') 		=> 'fx_bob' . $hover,
				esc_html__( 'Low opacity', 'codevz') 	=> 'fx_opacity' . $hover,
			);

			if ( $hover ) {
				$i = array_merge( $i, array(
					esc_html__( 'Full opacity', 'codevz') 		=> 'fx_full_opacity',
					esc_html__( '360° Z', 'codevz') 			=> 'fx_z_hover',
					esc_html__( 'Bounce', 'codevz') 			=> 'fx_bounce_hover',
					esc_html__( 'Shine', 'codevz') 				=> 'fx_shine_hover',
					esc_html__( 'Grow rotate right', 'codevz') 	=> 'fx_grow_rotate_right_hover',
					esc_html__( 'Grow rotate left', 'codevz') 	=> 'fx_grow_rotate_left_hover',
					esc_html__( 'Wobble skew', 'codevz') 		=> 'fx_wobble_skew_hover',
				) );
			}

			return $i;
		}
		
		/**
		 *
		 * Get RGB numbers of HEX color
		 * 
		 * @var Hex color code
		 * @return string
		 *
		 */
		public static function hex2rgba( $c = '', $o = '1' ) {
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

			return 'rgba(' . implode( ',', array( $r, $g, $b ) ) . ',' . $o . ')';
		}

		/**
		 *
		 * Enqueue google font
		 * 
		 * @return string|null
		 * 
		 */
		public static function load_font( $f = '' ) {
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
		 * Return full CSS with selector and fixes plus loading fonts
		 * 
		 * @return string|null
		 * 
		 */
		public static function sk_style( $atts = array(), $ids = array(), $device = '' ) {
			$out = $rtl = '';
			foreach ( (array) $ids as $id => $selector ) {
				$is_array = is_array( $selector );
				$val = empty( $atts[ $id . $device ] ) ? '' : str_replace( "``", '"', $atts[ $id . $device ] );

				if ( $val ) {
					$val = str_replace( 'CDVZ', '', $val );

					// RTL
					if ( self::contains( $val, 'RTL' ) ) {
						$rtl = self::get_string_between( $val, 'RTL', 'RTL' );
						$val = str_replace( array( $rtl, 'RTL' ), '', $val );
					}

					// Fix and load google font
					if ( self::contains( $val, 'font-family' ) ) {
						self::load_font( $val ); // Enqueue font

						// Extract font + params && Fix font for CSS
						$font = $o_font = self::get_string_between( $val, 'font-family:', ';' );
						$font = str_replace( '=', ':', $font );
						$font = str_replace( 'custom_', '', $font );
						
						if ( self::contains( $font, ':' ) ) {
							$font = explode( ':', $font );
							if ( ! empty( $font[0] ) ) {
								$val = str_replace( $o_font, "'" . $font[0] . "'", $val );
							}
						} else {
							$val = str_replace( $font, "'" . $font . "'", $val );
						}
					}

					if ( $is_array ) {
						if ( ! $device ) {
							$val .= $selector[1];
						}
						$selector = $selector[0];
					}

					// SVG background layer
					if ( self::contains( $id, 'svg_bg' ) ) {
						$type = self::contains( $val, '_class_svg_type' ) ? self::get_string_between( $val, '_class_svg_type:', ';' ) : '';
						$size = ( $type === 'circle' ) ? '3' : '1';
						$size = self::contains( $val, '_class_svg_size' ) ? self::get_string_between( $val, '_class_svg_size:', ';' ) : '1';
						$color = self::contains( $val, '_class_svg_color' ) ? self::get_string_between( $val, '_class_svg_color:', ';' ) : '#222';
						$color = self::contains( $color, 'rgba' ) ? $color : self::hex2rgba( $color );

						if ( $type === 'circle' ) {
							$base = base64_encode( "<svg xmlns='http://www.w3.org/2000/svg' width='20' height='24'><circle cx='6' cy='6' r='" . $size . "' fill='none' stroke='" . $color . "' stroke-width='1' /></svg>" );
							$val .= 'background-image: url("data:image/svg+xml;base64,' . $base . '");';
						} else if ( $type === 'dots' ) {
							$base = base64_encode( "<svg xmlns='http://www.w3.org/2000/svg' width='20' height='24'><circle cx='6' cy='6' r='" . $size . "' fill='" . $color . "' /></svg>" );
							$val .= 'background-image: url("data:image/svg+xml;base64,' . $base . '");';
						} else if ( $type === 'x' ) {
							$base = base64_encode( "<svg width='24' height='24' xmlns='http://www.w3.org/2000/svg'><path d='M4.01,15.419L15.419,4.01l0.57,0.57L4.581,15.99Z' stroke='" . $color . "' stroke-width='" . $size . "'></path><path d='M15.419,15.99L4.01,4.581l0.57-.57L15.99,15.419Z' stroke='" . $color . "' stroke-width='" . $size . "'></path></svg>" );
							$val .= 'background-image: url("data:image/svg+xml;base64,' . $base . '");';
						} else if ( $type === 'line' ) {
							$base = base64_encode( "<svg width='24' height='24' xmlns='http://www.w3.org/2000/svg'><path d='M4.01,15.419L15.419,4.01l0.57,0.57L4.581,15.99Z' stroke='" . $color . "' stroke-width='" . $size . "'></path></svg>" );
							$val .= 'background-image: url("data:image/svg+xml;base64,' . $base . '");';
						}

						// Remove unwanted in css
						if ( self::contains( $val, '_class_' ) ) {
							$val = preg_replace( '/_class_[\s\S]+?;/', '', $val );
						}
					}

					// Append CSS
					$out .= $selector . '{' . $val . '}';

					// RTL
					if ( $rtl ) {
						$sp = self::contains( $selector, array( '.cz-cpt-', '.cz-page-', '.home', 'body', '.woocommerce' ) ) ? '' : ' ';
						$out .= '.rtl' . $sp . preg_replace( '/,\s+|,/', ',.rtl' . $sp, $selector ) . '{' . $rtl . '}';
					}
					$rtl = 0;

				} else if ( $is_array && ! $device && $selector[1] ) {
					$out .= $selector[0] . '{' . $selector[1] . '}';
				}
			}

			return str_replace( ';}', '}', $out );
		}

		/**
		 *
		 * Fix: Remove extra <p> and </p> from content of elements
		 * 
		 * @return string
		 * 
		 */
		public static function fix_extra_p( $content = '' ) {
			return preg_replace( '/^<\/p>\n|<p>$/', '', $content );
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
		 * Get image by id or url
		 * 
		 * @var $i image ID or image url
		 * @var $s image size
		 * @var $url only return url of image
		 * @var $c custom class for image
		 * @return string
		 * 
		 */
		public static function get_image( $i = '', $s = 0, $url = 0, $c = '' ) {

			if ( ! self::contains( $i, '.' ) ) {
				$i = wpb_getImageBySize( array(
					'attach_id' 	=> empty( $i ) ? 1 : $i,
					'thumb_size' 	=> empty( $s ) ? 'full' : $s,
					'class' 		=> $c
				));

				$i = $i['thumbnail'];
			}

			if ( empty( $i ) ) {
				$i = '<img src="' . CDVZ_PLUGIN_URI . 'assets/admin_img/p.svg' . '" class="' . $c . '" width="1000" height="1000" alt="image" />';
			} else if ( ! self::contains( $i, 'src' ) ) {
				$i = '<img src="' . $i . '" class="' . $c . '" width="500" height="500" alt="image" />';
			}

			return $url ? self::get_string_between( $i, 'src="', '"' ) : $i;
		}

		/**
		 *
		 * VC Autocomplete callback for saved taxonomies values
		 * 
		 * @return array
		 * 
		 */
		public function vc_autocomplete_taxonomies_render( $term ) {
			$vc_taxonomies_types = vc_taxonomies_types();
			$terms = get_terms( array_keys( $vc_taxonomies_types ), array(
				'include' => array( $term['value'] ),
				'hide_empty' => false,
			) );
			$data = false;
			if ( is_array( $terms ) && 1 === count( $terms ) ) {
				$term = $terms[0];
				$data = vc_get_term_object( $term );
			}

			return $data;
		}

		/**
		 *
		 * VC Autocomplete taxonomies search process
		 * 
		 * @return string
		 * 
		 */
		public function vc_autocomplete_taxonomies_search( $search_string ) {
			$data = array();
			$vc_filter_by = vc_post_param( 'vc_filter_by', '' );
			$vc_taxonomies_types = strlen( $vc_filter_by ) > 0 ? array( $vc_filter_by ) : array_keys( vc_taxonomies_types() );
			$vc_taxonomies = get_terms( $vc_taxonomies_types, array(
				'hide_empty' => false,
				'search' => $search_string,
			) );
			if ( is_array( $vc_taxonomies ) && ! empty( $vc_taxonomies ) ) {
				foreach ( $vc_taxonomies as $t ) {
					if ( is_object( $t ) ) {
						$data[] = vc_get_term_object( $t );
					}
				}
			}

			return $data;
		}

		/**
		 *
		 * Get post data
		 * 
		 * @return string
		 * 
		 */
		public static function get_post_data( $id, $w = 'date', $s = 0, $c = '', $ic = '', $tc = '' ) {

			$cls = $w;
			$w = self::contains( $w, ' ' ) ? substr( $w, 0, strpos( $w, ' ' ) ) : $w;

			if ( $w === 'date' || $w === 'date_1' ) {

				$date = get_the_time( get_option( 'date_format' ) );
				$out = $s ? $date : '<a href="' . get_the_permalink( $id ) . '">' . $date . '</a>';

			} else if ( $w === 'cats' || $w === 'cats_1' ) {

				$cpt = get_post_type( $id );
				$tax = ( empty( $cpt ) || $cpt === 'post' ) ? 'category' : $cpt . '_cat';
				$cats = get_the_term_list( $id, $tax, '', ', ', '');
				if ( is_string( $cats ) ) {
					$out = $s ? strip_tags( $cats ) : $cats;
				}
				
			} else if ( self::contains( $w, array( 'cats_2', 'cats_3', 'cats_4', 'cats_5', 'cats_6', 'cats_7' ) ) ) {

				$out = self::get_cats( $id, $w, $s, $tc );
				
			} else if ( $w === 'tags' ) {

				$out = self::get_tags( $id, $s, $tc );
				
			} else if ( $w === 'author' ) {

				$author = get_the_author_meta( 'display_name', $id );
				$out = $s ? $author : '<a href="' . get_author_posts_url( $id ) . '">' . $author . '</a>';
				
			} else if ( $w === 'author_avatar' || $w === 'author_full_date' || $w === 'author_icon_date' ) {

				$author = get_the_author_meta( 'display_name', $id );
				$avatar = ( $ic && $w === 'author_icon_date' ) ? '<i class="cz_sub_icon fa ' . $ic . '"></i>' : get_avatar( $id, 30 );
				$link = get_author_posts_url( $id );

				if ( $s ) {
					$out = '<span class="cz_post_author_avatar">' . $avatar . '</span>';
					$out .= '<span class="cz_post_inner_meta">';
					$out .= '<span class="cz_post_author_name">' . $author . '</span>';
					if ( $w === 'author_full_date' || $w === 'author_icon_date' ) {
						$out .= '<span class="cz_post_date">' . get_the_time( get_option( 'date_format' ) ) . '</span>';
					}
					$out .= '</span>';
				} else {
					$out = '<a class="cz_post_author_avatar" href="' . $link . '">' . $avatar . '</a>';
					$out .= '<span class="cz_post_inner_meta">';
					$out .= '<a class="cz_post_author_name" href="' . $link . '">' . $author . '</a>';
					if ( $w === 'author_full_date' || $w === 'author_icon_date' ) {
						$out .= '<span class="cz_post_date">' . get_the_time( get_option( 'date_format' ) ) . '</span>';
					}
					$out .= '</span>';
				}
				
			} else if ( $w === 'price' ) {

				if ( function_exists( 'get_woocommerce_currency_symbol' ) ) {
					global $woocommerce;
					$currency = get_woocommerce_currency_symbol();
					$price = get_post_meta( $id, '_regular_price', true );
					$sale = get_post_meta( $id, '_sale_price', true );
					
					if ( $sale ) {
						$out = '<del>' . $currency . $price . '</del> ' . $currency . $sale;
					} else {
						$out = $currency . $price;
					}
				} else {
					$out = '---';
				}
				
			} else if ( $w === 'comments' ) {

				$cm = number_format_i18n( get_comments_number( $id ) );
				$out = $s ? $cm . ' ' . $c : '<a href="' . get_the_permalink( $id ) . '#comments">' . $cm . ' ' . $c . '</a>';
				
			} else if ( $w === 'custom_text' ) {
				
				$out = $s;
			
			} else if ( $w === 'custom_meta' ) {
				
				$out = (string) get_post_meta( $id, $s, true );
			
			}

			// Prefix
			$pre = ( $ic && ! self::contains( $w, 'author_' ) ) ? '<i class="cz_sub_icon mr8 fa ' . $ic . '"></i>' : '';
			$pre .= ( $c && $w !== 'comments' ) ? '<span class="cz_data_sub_prefix mr4">' . $c . '</span>' : '';

			// Out
			return isset( $out ) ? '<span class="cz_post_data cz_data_' . $cls . '">' . $pre . $out . '</span>' : '';
		}

		/**
		 *
		 * Get post categories include colors
		 * 
		 * @return string
		 * 
		 */
		public static function get_cats( $id, $style = '', $no_link = 0, $l = 10, $out = array() ) {

			$cpt = get_post_type( $id );
			$tax = ( empty( $cpt ) || $cpt === 'post' ) ? 'category' : $cpt . '_cat';

			if ( $terms = get_the_terms( $id, $tax ) ) {
				foreach ( $terms as $term ) {
					if ( isset( $term->term_id ) ) {
						$color = get_term_meta( $term->term_id, 'codevz_cat_meta', true );
						$opacity = self::contains( $style, array( '6', '7' ) ) ? '1' : '0.1';
						$color = empty( $color['color'] ) ? '' : ' style="color:' . $color['color'] . ';border-color:' . $color['color'] . ';background: ' . self::hex2rgba( $color['color'], $opacity ) . '"';
						$out[] = $no_link ? '<span' . $color . '>' . $term->name . '</span>' : '<a href="' . get_term_link( $term ) . '"' . $color . '>' . $term->name . '</a>';
					}
				}
			}

			$out = implode( '', array_slice( $out, 0, $l ) );

			return $out ? '<span class="cz_cats_2 cz_' . $style . '">' . $out . '</span>' : '';
		}

		/**
		 *
		 * Get post tags
		 * 
		 * @return string
		 *
		 */
		public static function get_tags( $id, $no_link = 0, $l = 10, $out = array() ) {
			$tax = get_object_taxonomies( get_post_type( $id ), 'objects' );

			foreach ( $tax as $tax_slug => $taks ) {
				$terms = get_the_terms( $id, $tax_slug );

				if ( ! empty( $terms ) && self::contains( $taks->label, 'Tags' ) ) {
					foreach ( $terms as $term ) {
						$out[] = $no_link ? '#' . esc_html( $term->name ) . ' ' : '<a href="' . esc_url( get_term_link( $term->slug, $tax_slug ) ) . '">#' . esc_html( $term->name ) . '</a> ';
					}
				}
			}

			$out = implode( '', array_slice( $out, 0, $l ) );

			return $out ? '<span class="cz_ptags">' . $out . '</span>' : '';
		}

		/**
		 *
		 * Limit words of string
		 * 
		 * @return string
		 *
		 */
		public static function limit_words( $s = '', $l = 11, $r = 0 ) {
			$l = $l ? (int) $l - 1 : 999;

			if ( str_word_count( $s ) > $l ) {
				if ( $r ) {
					$r = self::get_string_between( $s, '<a ', '</a>', 1 );
					$r = empty( $r[0] ) ? '' : $r[0];
					$s = $r ? str_replace( $r, '', $s ) : $s;
				}

				$s = preg_replace( '/((\w+\W*){' . $l . '}(\w+))(.*)/', '${1}', $s ) . ' ...';
			} else {
				$r = '';
			}

			if ( ! $r && self::contains( $s, '...' ) ) {
				$s = explode( "...", $s ); 
				$s = $s[0];
			}

			return $s . ( $r ? $r : '' );
		}

		/**
		 *
		 * Modify array before vc_map for defaul values
		 * 
		 * @var vc_map array
		 * @return array
		 * 
		 */
		public static function vc_map( $map ) {

			if ( function_exists( 'codevz_theme_config' ) ) {
				$default = codevz_theme_config( 'default' );
				if ( isset( $default[ $map['base'] ] ) ) {
					$default = shortcode_parse_atts( self::get_string_between( $default[ $map['base'] ], '[', ']' ) );

					foreach ( $map['params'] as $n => $p ) {
						if ( isset( $default[ $p['param_name'] ] ) ) {
							if ( $p['type'] === 'dropdown' ) {
								$map['params'][ $n ][ 'std' ] = $default[ $p['param_name'] ];
							} else {
								$map['params'][ $n ][ 'value' ] = $default[ $p['param_name'] ];
							}
							$map['params'][ $n ][ 'save_always' ] = true;
						}
					}
				}
			}

			return vc_map( $map );
		}

		/**
		 *
		 * Register new Post types
		 * 
		 * @return object
		 * 
		 */
		public static function post_types() {
			$options 	= (array) self::option();
			$post_types = (array) get_option( 'codevz_post_types' );
			$post_types[] = 'portfolio';

			foreach ( $post_types as $cpt ) {

				if ( empty( $cpt ) ) {
					continue;
				}

				$cpt = strtolower( str_replace( ' ', '_', $cpt ) );

				$opt = array(
					'slug' 			=> empty( $options[ 'slug_' . $cpt ] ) ? $cpt : $options[ 'slug_' . $cpt ], 
					'title' 		=> empty( $options[ 'title_' . $cpt ] ) ? ucwords( $cpt ) : $options[ 'title_' . $cpt ], 
					'cat_slug' 		=> empty( $options[ 'cat_' . $cpt ] ) ? $cpt . '/cat' : $options[ 'cat_' . $cpt ], 
					'cat_title' 	=> empty( $options[ 'cat_title_' . $cpt ] ) ? 'Categories' : $options[ 'cat_title_' . $cpt ], 
					'tags_slug' 	=> empty( $options[ 'tags_' . $cpt ] ) ? $cpt . '/tags' : $options[ 'tags_' . $cpt ], 
					'tags_title' 	=> empty( $options[ 'tags_title_' . $cpt ] ) ? 'Tags' : $options[ 'tags_title_' . $cpt ]
				);

				register_taxonomy( $cpt . '_cat', $cpt, 
					array(
						'hierarchical'		=> true,
						'labels'			=> array(
							'name'				=> $opt['cat_title'],
							'menu_name'			=> $opt['cat_title']
						),
						'show_ui'			=> true,
						'show_admin_column'	=> true,
						'query_var'			=> true,
						'rewrite'			=> array( 'slug' => $opt['cat_slug'], 'with_front' => false ),
					)
				);

				register_taxonomy( $cpt . '_tags', $cpt, 
					array(
						'hierarchical'		=> false,
						'labels'			=> array(
							'name'				=> $opt['tags_title'],
							'menu_name'			=> $opt['tags_title']
						),
						'show_ui'			=> true,
						'show_admin_column'	=> true,
						'query_var'			=> true,
						'rewrite'			=> array( 'slug' => $opt['tags_slug'], 'with_front' => false ),
					)
				);

				$cpt_label = str_replace( '_', ' ', $opt['title'] );
				register_post_type( $cpt, 
					array(
						'labels'		=> array(
							'name'			=> $cpt_label,
							'menu_name'		=> $cpt_label
						),
						'public'			=> true,
						//'menu_icon'		=> $icon,
						'supports'			=> array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'comments', 'author' ),
						'has_archive'		=> true,
						'show_in_rest'		=> true,
						'rewrite'			=> array( 'slug' => $opt['slug'], 'with_front' => false )
				 	)
				);
			}
		}

		/**
		 *
		 * Set shortcodes ID and extract styles then update post meta 'cz_sc_styles'
		 * 
		 * @return string
		 * 
		 */
		public static function save_post( $post_id = '' ) {
			if ( empty( $post_id ) || wp_is_post_revision( $post_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
				return;
			}

			// Get content
			$content = get_post_field( 'post_content', $post_id );

			// Extract Shortcodes
			$shortcodes = (array) self::get_string_between( $content, '[cz_', ']', 1 );
			if ( ! empty( $shortcodes ) ) {
				$styles = $tablet = $mobile = '';
				foreach ( $shortcodes as $sc ) {
					if ( ! empty( $sc ) ) {
						$do_shortcode = do_shortcode( $sc );
						$styles .= self::get_string_between( $do_shortcode, '<style class="cz_d_css">', '</style>' );
						$tablet .= self::get_string_between( $do_shortcode, '<style class="cz_t_css">', '</style>' );
						$mobile .= self::get_string_between( $do_shortcode, '<style class="cz_m_css">', '</style>' );
					}
				}

				// Update metabox for new styles
				delete_post_meta( $post_id, 'cz_sc_styles' );
				update_post_meta( $post_id, 'cz_sc_styles', $styles );
				if ( $tablet ) {
					delete_post_meta( $post_id, 'cz_sc_styles_tablet' );
					update_post_meta( $post_id, 'cz_sc_styles_tablet', $tablet );
				}
				if ( $mobile ) {
					delete_post_meta( $post_id, 'cz_sc_styles_mobile' );
					update_post_meta( $post_id, 'cz_sc_styles_mobile', $mobile );
				}
				
			}
		} // save_post

	} // Codevz_Plus

	// Run
	Codevz_Plus::instance();

	/**
	 *
	 * VC init action
	 * 
	 * @return object
	 * 
	 */
	add_action( 'vc_before_init', function() {

		// Codevz Elements
		foreach ( glob( CDVZ_PLUGIN_DIR . 'shortcodes/*.php' ) as $i ) {
			require_once( $i );
			$name = str_replace( '.php', '', basename( $i ) );
			$class = 'CDVZ_' . $name;
			$new_class = new $class( 'cz_' . $name );
			$new_class->in();
		}

		// Elements container
		if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
			class WPBakeryShortCode_cz_acc_child extends WPBakeryShortCodesContainer {}
			class WPBakeryShortCode_cz_accordion extends WPBakeryShortCodesContainer {}
			class WPBakeryShortCode_cz_carousel extends WPBakeryShortCodesContainer {}
			class WPBakeryShortCode_cz_content_box extends WPBakeryShortCodesContainer {}  
			class WPBakeryShortCode_cz_free_position_element extends WPBakeryShortCodesContainer {}
			class WPBakeryShortCode_cz_history_line extends WPBakeryShortCodesContainer {}
			class WPBakeryShortCode_cz_parallax extends WPBakeryShortCodesContainer {}
			class WPBakeryShortCode_cz_popup extends WPBakeryShortCodesContainer {}
			class WPBakeryShortCode_cz_process_line_vertical extends WPBakeryShortCodesContainer {}
			class WPBakeryShortCode_cz_show_more_less extends WPBakeryShortCodesContainer {}
			class WPBakeryShortCode_cz_speech_bubble extends WPBakeryShortCodesContainer {}
			class WPBakeryShortCode_cz_tab extends WPBakeryShortCodesContainer {}
			class WPBakeryShortCode_cz_tabs extends WPBakeryShortCodesContainer {}
			class WPBakeryShortCode_cz_timeline extends WPBakeryShortCodesContainer {}
			class WPBakeryShortCode_cz_timeline_item extends WPBakeryShortCodesContainer {}
		}

		// Presets tab for VC elements
		if ( class_exists( 'Codevz_Presets' ) ) {
			foreach ( Codevz_Presets::get() as $n => $v ) {
				vc_add_param( $n, array(
					'type' 			=> 'cz_presets',
					'param_name' 	=> $n,
					'group' 		=> esc_html__( 'Presets', 'codevz' ),
					'weight'		=> -1,
					'class'			=> ( isset( $v[0] ) && is_string( $v[0] ) ) ? 'czcol_' . $v[0] : ''
				));
			}
		}

		// Activate VC for post types
		$vc_cpts = (array) get_option( 'codevz_post_types' );
		$vc_cpts[] = 'page';
		$vc_cpts[] = 'post';
		$vc_cpts[] = 'portfolio';
		$vc_cpts[] = 'product';
		vc_set_default_editor_post_types( $vc_cpts );

	}); // vc_before_init

} // class_exists