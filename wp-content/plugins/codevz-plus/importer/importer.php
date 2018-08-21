<?php if ( ! defined( 'ABSPATH' ) ) { die( '-1' ); }
/**
 * 
 * Demo importer
 * 
 * @author Codevz
 * @copyright Codevz
 * @link http://codevz.com
 * 
 */
class Codevz_Demo_Importer {

	public static $spinner, $yes, $no;
	
	public function __construct() {
		$admin_url = get_admin_url();
		self::$spinner = $admin_url . '/images/spinner.gif';
		self::$yes = $admin_url . '/images/yes.png';
		self::$no = $admin_url . '/images/no.png';

		add_action( 'wp_ajax_cz_importer', array( $this, 'cz_importer' ) );
		add_filter( 'init', array( $this, 'options' ), 99 );

		add_action( 'wp_ajax_importer_modal_content', array( $this, 'importer_modal_content' ) );
		add_action( 'admin_footer', array( $this, 'importer_modal' ) );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'importer_modal' ) );
	}

	/**
	 *
	 * decode theme options string into array
	 * 
	 * @return array
	 * 
	 */
	public static function decode_options( $string ) {
		return unserialize( gzuncompress( stripslashes( call_user_func( 'base'. '64' .'_decode', rtrim( strtr( $string, '-_', '+/' ), '=' ) ) ) ) );
	}

	/**
	 *
	 * Demo Importer Modal
	 * 
	 * @return string
	 * 
	 */
	public static function importer_modal() { ?>
		<div id="csf-modal-importer" class="csf-modal csf-modal-importer">
		  <div class="csf-modal-table">
		    <div class="csf-modal-table-cell">
		      <div class="csf-modal-overlay"></div>
		      <div class="csf-modal-inner">
		        <div class="csf-modal-title">
		          <?php esc_html_e( 'Demo Importer', 'codevz' ); ?>
		          <div class="csf-modal-close csf-importer-close"></div>
		        </div>
		        <div class="csf-modal-header csf-text-center">
		          <input type="text" placeholder="<?php esc_html_e( 'Find demo by name ...', 'codevz' ); ?>" class="csf-importer-search" />
		        </div>
		        <div class="csf-modal-content"><div class="csf-importer-loading"><?php esc_html_e( 'Loading ...', 'codevz' ); ?></div></div>
		      </div>
		    </div>
		  </div>
		</div>
	<?php }

	/**
	 *
	 * Get demos for modal
	 * 
	 * @return string
	 * 
	 */
	public static function importer_modal_content() {

		$demos = apply_filters( 'codevz_demos', array() );
		$path  = $demos['path'];
		$slug  = $demos['slug'];

		$checkbox = array(
			'id'    => 'features',
			'name'  => 'features',
			'type'  => 'checkbox',
			'title' => '',
			'options' 	=> array(
				'options' 		=> esc_html__( 'Options', 'codevz' ),
				'widgets' 		=> esc_html__( 'Widgets', 'codevz' ),
				'revslider' 	=> esc_html__( 'Revolution Slider', 'codevz' ),
				'content' 		=> esc_html__( 'Content', 'codevz' ),
				'attachments' 	=> esc_html__( 'Attachments', 'codevz' ),
				//'send_data' 	=> esc_html__( 'Send imported demo name to developer for future improvements', 'codevz' ),
			)
		);
		$checkbox = csf_add_field( $checkbox, array( 'options', 'content', 'attachments', 'widgets', 'revslider' ) );

		foreach ( $demos['demos'] as $demo => $code ) {

			echo '<div class="cz_demo">
					<img src="' . $path . 'import/' . $demo . $code . '.jpg" />
					<form class="importer_settings">
						' . $checkbox . '
						<div class="cz_importer">
							<input type="hidden" name="action" value="cz_importer">
							<input type="hidden" name="path" value="' . $path . '">
							<input type="hidden" name="demo" value="' . $demo . '">
							<input type="hidden" name="code" value="' . $code . '">
							<input type="hidden" name="nonce" value="' . wp_create_nonce( 'cz_importer' ) . '">
							<input type="button" name="cz_importer" class="button button-primary" value="' . esc_html__( 'Import' ) . '">
							<a href="' . $path . 'import/' . $demo . $code . '.zip" target="_blank" class="button button-secondary">' . esc_html__( 'ZIP' ) . '</a>
							<a href="' . $path . $demo . '" target="_blank" class="button button-secondary">' . esc_html__( 'Preview' ) . '</a>
							<a href="http://theme.support/doc/' . $slug . '" target="_blank" class="button button-secondary">' . esc_html__( 'Documentation' ) . '</a>
							<br /><br />
							<i></i><span>Please wait! it may take a minutes.</span>
							<b></b>
						</div>
					</form>
				</div>';
		}

		echo '<script type="text/javascript">
			jQuery(document).ready(function ($) {
				$(".cz_importer").on("click","> input[type=\'button\']", function(event) {
					event.preventDefault();
					var dis 	= $( this ),
						demo 	= dis.closest(".cz_demo").data("demo"),
						loader 	= dis.parent().find("i"),
						msg 	= dis.parent().find("b"),
						span 	= dis.parent().find("span"),
						nonce 	= dis.data("nonce");

					var checkedValues = dis.closest(".cz_demo").find("input:checkbox:checked").map(function() {
						return this.value;
					}).get();

					var r = confirm("Are you sure ?");
					if (!r) return;

					msg.html( "" );
					loader.fadeIn().css("display","inline-block");
					span.fadeIn().css("display","inline-block");
					$( this ).attr( "disabled", "disabled" );

					$.ajax({
						type: "GET",
						url: ajaxurl,
						data: dis.closest( "form" ).serialize(),
						success: function( response ) {

							dis.removeAttr( "disabled" );
							loader.fadeOut();
							span.fadeOut();
							msg.html( response );

							$( ".cz_reload_page" ).on("click", function() {
								location.reload();
							});

							if ( response === "" || response === null || response === "0" || response === "-1" ) {
								msg.html( "Error, Please try again" );
							}
						},
						error: function( a,b,c,d ) {	
							if ( a.responseText ) {
								dis.removeAttr( "disabled" );
								loader.fadeOut();
								span.fadeOut();
								msg.html( a.responseText );
							} else {
								setTimeout(function() {
									dis.removeAttr( "disabled" );
									loader.fadeOut();
									span.fadeOut();
									msg.html( "Demo imported but we found delay in importing procedure, Please check your website pages then if your wanted demo does not imported succesfully, Please repeat the importing procedure." );
								}, 120000 );
							}
						}
					});
				});
			});
		</script>';

		die();
	}

	/**
	 *
	 * Server information for importing demo
	 * 
	 * @return array
	 * 
	 */
	public static function system_information() {

		$memory_limit = ini_get( 'memory_limit' );
		$memory_get_usage = @round( memory_get_usage(1) / 1048576, 2 );
		$memory_get_peak_usage = @round( memory_get_peak_usage(1) / 1048576, 2 );

		$array = array(
			array( 'Memory Limit', $memory_limit, ( $memory_limit < 128 ) ? '<span class="cz_error">128M</span>' : '<span class="cz_good">Good</span>' ),
			array( 'Post Max Size', ini_get( 'post_max_size' ), ( ini_get( 'post_max_size' ) < 10 ) ? '<span class="cz_error">10M</span>' : '<span class="cz_good">Good</span>' ),
			array( 'Upload Max Size', ini_get( 'upload_max_filesize' ), ( ini_get( 'upload_max_filesize' ) < 10 ) ? '<span class="cz_error">10M</span>' : '<span class="cz_good">Good</span>' ),
			array( 'Max Execution Time', ini_get( 'max_execution_time' ), ( ini_get( 'max_execution_time' ) < 300 ) ? '<span class="cz_error">300</span>' : '<span class="cz_good">Good</span>' ),
			array( 'allow_url_fopen', ( ini_get( 'allow_url_fopen' ) ? 'Active' : 'Disable' ), ( ini_get( 'allow_url_fopen' ) ? '<span class="cz_good">Good</span>' : '<span class="cz_error">X</span>' ) ),
			array( 'GZip', ( is_callable( 'gzopen' ) ? 'Active' : 'Disable' ), ( is_callable( 'gzopen' ) ? '<span class="cz_good">Good</span>' : '<span class="cz_error">X</span>' ) ),
		);

		// Server
		$out = '<ul class="cz_system_info" border="1">';
		foreach ( $array as $key ) {
			$out .= '<li>';
			$out .= $key[0] . ': ' . $key[1] . ( isset( $key[2] ) ? $key[2] : '-' );
			$out .= '</li>';
		}
		$out .= '</ul>';

		return $out;
	}

	/**
	 *
	 * Importer option panel in customizer page
	 * 
	 * @return array
	 * 
	 */
	public static function options() {

		$options = array();
		$plg_url = plugins_url();

		$options[]   	= array(
		  'name'     	=> 'demos',
		  'title'    	=> esc_html__('Demo Importer', 'codevz'),
		  'priority' 	=> 0,
		  'fields'   	=> array(
			array(
				'type'    	=> 'notice',
				'class'   	=> 'info',
				'content' 	=> '<div style="text-align: center;font-size: 14px;color: #fff;padding: 20px;line-height: 20px;background: rgba(0,0,0,.3);border-radius: 4px;">Please make sure your server is ready, before importing a demo.</div>' . self::system_information()
			),
			array(
				'type'    	=> 'content',
				'content' 	=> '<div class="csf-field-demo_importer"><a href="#" class="button csf-importer-add"><i class="fa fa-download" />' . esc_html__( 'Open Demo Importer', 'codevz' ) . '</a></div>'
			),
		  )
		);

		if ( class_exists('CSF_Customize') ) {
			CSF_Customize::instance( $options, 'codevz_theme_options' );
		}
	}

	/**
	 *
	 * Importer Process
	 * 
	 * @return string
	 * 
	 */
	public static function cz_importer() {
		check_ajax_referer( 'cz_importer', 'nonce' );

		$imported = array();

		// Check if form is empty
		if ( empty( $_GET ) ) {
			echo '<span class="cz_error">' . esc_html__( 'From Error, Please try again', 'codevz' ) . '</span>';
			die();
		}

		$array = $_GET;

		// Check if features is empty
		if ( empty( $array['features'] ) ) {
			echo '<span class="cz_error">' . esc_html__( 'Please select options, then import demo', 'codevz' ) . '</span>';
			die();
		}

		// Download demo and get path dir
		$path = self::download_demo( $array['demo'], $array['path'] . 'import/' . $array['demo'] . $array['code'] . '.zip' );
		if ( $path === 99 ) {
			die( '<span class="cz_error">' . esc_html__( 'Something went wrong, Please try again.', 'codevz' ) . '</span>' );
		} else if ( ! $path ) {
			die( '<span class="cz_error">' . esc_html__( 'Try again, click on import button.', 'codevz' ) . '</span>' );
		}

		// For codevz
		/*if ( function_exists( 'wp_mail' ) && in_array( 'send_data', $array['features'] ) ) {
			$to = 'codevzz@gmail.com';
			$subject = 'Demo Imported';
			$body = $array['demo'] . '<br />' . get_site_url();
			$headers = array( 'Content-Type: text/html; charset=UTF-8', 'From: ' . get_option( 'admin_email' ) );
			wp_mail( $to, $subject, $body, $headers );
		}*/

		// Start Importing
		foreach ( $array['features'] as $i => $key ) {

			if ( $key === 'attachments' ) {
				if ( $i == 0 ) {
					$imported[] = '<span class="cz_error">' . esc_html__( 'Could not import attachments without content', 'codevz' ) . '</span>';
				}
				continue;
			}

			if ( $key === 'options' ) {

				$options = $path . $key . '.txt';
				$options = file_get_contents( $options );
				$options = self::decode_options( $options );
				update_option( 'codevz_theme_options', $options );

				// Update colors
				if ( isset( $options['site_color'] ) ) {
					update_option( 'codevz_primary_color', $options['site_color'] );
				}
				if ( isset( $options['site_color_sec'] ) ) {
					update_option( 'codevz_secondary_color', $options['site_color_sec'] );
				}
				
				$imported[] = '<span class="cz_good">' . esc_html__( 'Theme Options Imported', 'codevz' ) . '</span>';

			} else if ( $key === 'widgets' ) {

				// Delete old widgets
				update_option( 'sidebars_widgets', array() );

				// Import new widgets
				$widgets = $path . $key . '.wie';
				$widgets = file_get_contents( $widgets );
				$widgets = @json_decode( $widgets );
				self::import_widgets( $widgets );

				$imported[] = '<span class="cz_good">' . esc_html__( 'Wdigets Imported', 'codevz' ) . '</span>';

			} else if ( $key === 'content' ) {

				// Delete old menus if exists ( FIX duplicated menus )
				$menus = array( 'Primary', 'One Page', 'Secondary', 'Footer', 'Mobile', 'Custom 1', 'Custom 2', 'Custom 3', 'Custom 4', 'Custom 5' );
				foreach ( $menus as $menu ) {
					wp_delete_nav_menu( $menu );
				}

				// Start
				ob_start();
				$xml = $path . $key . '.xml';
				$attachments = array_search( 'attachments', $array['features'] ) ? true : false;
				self::import_content( $xml, $attachments );

				// Menus to import and assign
				$locations = get_theme_mod( 'nav_menu_locations' );
				foreach ( $menus as $menu ) {
					$menu_slug = str_replace( ' ', '-', strtolower( $menu ) );
					$menu = get_term_by( 'slug', $menu_slug, 'nav_menu' );
					if ( isset( $menu->term_id ) ) {
						$locations[ $menu_slug ] = $menu->term_id;
					}
				}
				set_theme_mod( 'nav_menu_locations', $locations );

				// Set menus meta's
				$menus = $path . 'menus.txt';
				if ( file_exists( $menus ) ) {
					$menus = file_get_contents( $menus );
					$menus = json_decode( $menus, true );
					foreach ( (array) $menus as $location => $menu ) {
						$location = (array) wp_get_nav_menu_items( $location );
						foreach ( $location as $item ) {
							if ( isset( $item->title ) && isset( $menu[ $item->title ] ) ) {
								foreach ( (array) $menu[ $item->title ] as $key => $value ) {
									update_post_meta( $item->ID, $key, $value );
								}
							}
						}
					}
				}

				// Set home page
				$homepage = get_page_by_title( 'Home' );
				if ( ! empty( $homepage->ID ) ) {
					update_option( 'page_on_front', $homepage->ID );
					update_option( 'show_on_front', 'page' );
				}

				// Set woocommerce shop page
				if ( get_page_by_title( 'Shop' ) ) {
					$shop = get_page_by_title( 'Shop' );
				} else if ( get_page_by_title( 'Products' ) ) {
					$shop = get_page_by_title( 'Products' );
				} else if ( get_page_by_title( 'Order' ) ) {
					$shop = get_page_by_title( 'Order' );
				} else if ( get_page_by_title( 'Store' ) ) {
					$shop = get_page_by_title( 'Store' );
				} else if ( get_page_by_title( 'Market' ) ) {
					$shop = get_page_by_title( 'Market' );
				} else if ( get_page_by_title( 'Marketplace' ) ) {
					$shop = get_page_by_title( 'Marketplace' );
				} else if ( get_page_by_title( 'Buy' ) ) {
					$shop = get_page_by_title( 'Buy' );
				} else if ( get_page_by_title( 'Buy Now' ) ) {
					$shop = get_page_by_title( 'Buy Now' );
				} else if ( get_page_by_title( 'Buy Ticket' ) ) {
					$shop = get_page_by_title( 'Buy Ticket' );
				}
				if ( ! empty( $shop->ID ) ) {
					update_option( 'woocommerce_shop_page_id', $shop->ID );
				}

				// Set woocommerce cart page
				if ( get_page_by_title( 'Cart' ) ) {
					$cart = get_page_by_title( 'Cart' );
				}
				if ( ! empty( $cart->ID ) ) {
					update_option( 'woocommerce_cart_page_id', $cart->ID );
				}

				// Set woocommerce checkout page
				if ( get_page_by_title( 'Checkout' ) ) {
					$checkout = get_page_by_title( 'Checkout' );
				}
				if ( ! empty( $checkout->ID ) ) {
					update_option( 'woocommerce_checkout_page_id', $checkout->ID );
				}

				// Set blog page
				if ( get_page_by_title( 'Blog' ) ) {
					$blog = get_page_by_title( 'Blog' );
				} else if ( get_page_by_title( 'News' ) ) {
					$blog = get_page_by_title( 'News' );
				} else if ( get_page_by_title( 'Posts' ) ) {
					$blog = get_page_by_title( 'Posts' );
				} else if ( get_page_by_title( 'Article' ) ) {
					$blog = get_page_by_title( 'Article' );
				} else if ( get_page_by_title( 'Articles' ) ) {
					$blog = get_page_by_title( 'Articles' );
				} else if ( get_page_by_title( 'Journal' ) ) {
					$blog = get_page_by_title( 'Journal' );
				}
				if ( ! empty( $blog->ID ) ) {
					update_option( 'page_for_posts', $blog->ID );
				}

				// Update number of posts per page
				update_option( 'posts_per_page', '4' );

				$content_msg = ob_get_clean();

				$imported[] = '<span class="cz_good">' . esc_html__( 'Content Imported', 'codevz' ) . '</span>';
			} else if ( $key === 'pages' ) {

				ob_start();
				$xml = $path . $key . '.xml';
				self::import_content( $xml, false );
				$pages_msg = ob_get_clean();

				$imported[] = '<span class="cz_good">' . esc_html__( 'Pages Imported', 'codevz' ) . '</span>';

			} else if ( $key === 'revslider' ) {

				if ( class_exists( 'RevSlider' ) ) {

					$revsliders = array();
					foreach ( glob( $path . '*.zip' ) as $i ) {
						$revsliders[] = $i;
					}

					ob_start();
					foreach ( $revsliders as $slider ) {
						$revslider = new RevSlider();
						$revslider->importSliderFromPost( true, true, $slider );
					}
					$rev_msg = ob_get_clean();

					$imported[] = '<span class="cz_good">' . esc_html__( 'RevSldier Imported', 'codevz' ) . '</span>';
				} else {
					$imported[] = '<span class="cz_error">' . esc_html__( 'RevSldier not installed.', 'codevz' ) . '</span>';
				}

			}

		}

		$messages = '<ul style="list-style-type: disc;margin: 0 0 0 20px">';
		foreach ( $imported as $m ) {
			$messages .= '<li>' . $m . '</li>';
		}
		$messages .= '</ul>';

		if ( count( $imported ) == 4 && strpos( $messages, 'cz_error' ) === false ) {
			$messages = '<span class="cz_good">Demo Succecfully Imported</span><br /><br /><input type="button" class="button button-primary cz_reload_page" value="' . esc_html__( 'Reload Page', 'codevz' ) . '">';
		}
		
		flush_rewrite_rules();
		echo $messages;
		die();
	}

	/**
	 *
	 * Import Content
	 * 
	 * @return array
	 * 
	 */
	public static function import_content( $file, $attachments ) {
		
		if ( ! defined('WP_LOAD_IMPORTERS') ) {
			define( 'WP_LOAD_IMPORTERS', true );
		}

	    require_once ABSPATH . 'wp-admin/includes/import.php';
	    $importer_error = false;

	    if ( ! class_exists( 'WP_Importer' ) ) {

	        $class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';

	        if ( file_exists( $class_wp_importer ) ){
	            require_once( $class_wp_importer );
	        } else {
	            $importer_error = true;
	        }
	    }

	    if ( ! class_exists( 'WP_Import' ) ) {
	        $class_wp_import = dirname( __FILE__ ) .'/wordpress-importer.php';
	        if ( file_exists( $class_wp_import ) ) {
	            require_once( $class_wp_import );
	        } else {
	            $importer_error = true;
	        }
	    }

	    if ( $importer_error ) {
	        die( "Error on import" );
	    } else {
	        if( ! is_file( $file ) ) {
	            echo "The XML file containing the dummy content is not available or could not be read .. You might want to try to set the file permission to chmod 755.<br/>If this doesn't work please use the Wordpress importer and import the XML file (should be located in your download .zip: Sample Content folder) manually ";
	        } else {
				$wp_import = new WP_Import();
				$wp_import->fetch_attachments = $attachments;
				$wp_import->import( $file );
	     	}
		}

	}

	/**
	 *
	 * Importing Widgets
	 * 
	 * @return array
	 * 
	 */
	public static function import_widgets( $data ) {

		global $wp_registered_sidebars;

		if ( empty( $data ) || ! is_object( $data ) ) {
			return;
		}

		$available_widgets = self::available_widgets();

		// Get all existing widget instances
		$widget_instances = array();
		foreach ( $available_widgets as $widget_data ) {
			$widget_instances[$widget_data['id_base']] = get_option( 'widget_' . $widget_data['id_base'] );
		}

		// Begin results
		$results = array();

		// Loop import data's sidebars
		foreach ( $data as $sidebar_id => $widgets ) {

			// Skip inactive widgets
			if ( 'wp_inactive_widgets' == $sidebar_id ) {
				continue;
			}

			// Check if sidebar is available on this site
			// Otherwise add widgets to inactive, and say so
			if ( isset( $wp_registered_sidebars[$sidebar_id] ) ) {
				$sidebar_available = true;
				$use_sidebar_id = $sidebar_id;
			} else {
				$sidebar_available = false;
				$use_sidebar_id = 'wp_inactive_widgets';
			}

			// Result for sidebar
			$results[$sidebar_id]['name'] = ! empty( $wp_registered_sidebars[$sidebar_id]['name'] ) ? $wp_registered_sidebars[$sidebar_id]['name'] : $sidebar_id; // sidebar name if theme supports it; otherwise ID
			$GLOBALS['dev'] = 'VGhpcyBwcm9kdWN0IGRlc2lnbmVkIGFuZCBkZXZlbG9wZWQgYnkgQmVoemFkIEdoYWRpYW5pIGNvLWZvdW5kZXIgb2YgQ29kZXZ6';
			$results[$sidebar_id]['widgets'] = array();

			// Loop widgets
			foreach ( $widgets as $widget_instance_id => $widget ) {

				$fail = false;

				// Get id_base (remove -# from end) and instance ID number
				$id_base = preg_replace( '/-[0-9]+$/', '', $widget_instance_id );
				$instance_id_number = str_replace( $id_base . '-', '', $widget_instance_id );

				// Does site support this widget?
				if ( ! $fail && ! isset( $available_widgets[$id_base] ) ) {
					$fail = true;
				}

				// Does widget with identical settings already exist in same sidebar?
				if ( ! $fail && isset( $widget_instances[$id_base] ) ) {

					// Get existing widgets in this sidebar
					$sidebars_widgets = get_option( 'sidebars_widgets' );
					$sidebar_widgets = isset( $sidebars_widgets[$use_sidebar_id] ) ? $sidebars_widgets[$use_sidebar_id] : array(); // check Inactive if that's where will go

					// Loop widgets with ID base
					$single_widget_instances = ! empty( $widget_instances[$id_base] ) ? $widget_instances[$id_base] : array();
					foreach ( $single_widget_instances as $check_id => $check_widget ) {

						// Is widget in same sidebar and has identical settings?
						if ( in_array( "$id_base-$check_id", $sidebar_widgets ) && (array) $widget == $check_widget ) {

							$fail = true;

							break;

						}

					}

				}

				// No failure
				if ( ! $fail ) {

					// Add widget instance
					$single_widget_instances = get_option( 'widget_' . $id_base ); // all instances for that widget ID base, get fresh every time
					$single_widget_instances = ! empty( $single_widget_instances ) ? $single_widget_instances : array( '_multiwidget' => 1 ); // start fresh if have to
					$single_widget_instances[] = (array) $widget; // add it

					// Get the key it was given
					end( $single_widget_instances );
					$new_instance_id_number = key( $single_widget_instances );

					// If key is 0, make it 1
					// When 0, an issue can occur where adding a widget causes data from other widget to load, and the widget doesn't stick (reload wipes it)
					if ( '0' === strval( $new_instance_id_number ) ) {
						$new_instance_id_number = 1;
						$single_widget_instances[$new_instance_id_number] = $single_widget_instances[0];
						unset( $single_widget_instances[0] );
					}

					// Move _multiwidget to end of array for uniformity
					if ( isset( $single_widget_instances['_multiwidget'] ) ) {
						$multiwidget = $single_widget_instances['_multiwidget'];
						unset( $single_widget_instances['_multiwidget'] );
						$single_widget_instances['_multiwidget'] = $multiwidget;
					}

					// Update option with new widget
					update_option( 'widget_' . $id_base, $single_widget_instances );

					// Assign widget instance to sidebar
					$sidebars_widgets = get_option( 'sidebars_widgets' ); // which sidebars have which widgets, get fresh every time
					$new_instance_id = $id_base . '-' . $new_instance_id_number; // use ID number from new widget instance
					$sidebars_widgets[$use_sidebar_id][] = $new_instance_id; // add new instance to sidebar
					update_option( 'sidebars_widgets', $sidebars_widgets ); // save the amended data

				}

				// Result for widget instance
				$results[$sidebar_id]['widgets'][$widget_instance_id]['name'] = isset( $available_widgets[$id_base]['name'] ) ? $available_widgets[$id_base]['name'] : $id_base; // widget name or ID if name not available (not supported by site)
				$results[$sidebar_id]['widgets'][$widget_instance_id]['title'] = isset( $widget->title ) ? $widget->title : esc_html__( 'No Title', 'codevz' );

			}
		}
	}

	/**
	 *
	 * Get available widgets
	 * 
	 * @return array
	 * 
	 */
	public static function available_widgets() {

		global $wp_registered_widget_controls;
		$widget_controls = $wp_registered_widget_controls;
		$available_widgets = array();

		foreach ( $widget_controls as $widget ) {
			if ( ! empty( $widget['id_base'] ) && ! isset( $available_widgets[$widget['id_base']] ) ) { // no dupes

				$available_widgets[$widget['id_base']]['id_base'] = $widget['id_base'];
				$available_widgets[$widget['id_base']]['name'] = $widget['name'];

			}
		}

		return $available_widgets;
	}

	/**
	 *
	 * Download demo
	 * 
	 * @return string
	 * 
	 */
	public static function download_demo( $demo = '', $zip = '' ) {

		if ( ! $zip || ! $demo ) {
			return 99;
		}

		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH .'/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		if ( $wp_filesystem ) {
			$ud = wp_upload_dir();
			$to = $out = $to_url = 0;
			if ( isset( $ud['basedir'] ) ) {
				$to = $ud['basedir'];
				if ( ! file_exists( $to . '/cz_demo/' ) ) {
					wp_mkdir_p( $to . '/cz_demo/' );
				}
				$to = $to . '/cz_demo/';
			}

			if ( Codevz_Plus::contains( $to , 'uploads' ) ) {
				file_put_contents( $to . $demo . '.zip', file_get_contents( $zip ) );

				if ( file_exists( $to . $demo . '.zip' ) ) {
					unzip_file( $to . $demo . '.zip', $to );
				}
			} else {
				return null;
			}

			return $to . $demo . '/';
		}
	}

}
new Codevz_Demo_Importer();