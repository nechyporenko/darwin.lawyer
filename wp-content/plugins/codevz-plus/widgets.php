<?php if ( ! defined( 'ABSPATH' ) ) {die;} // Cannot access pages directly.

/**
 * If required plugin is not activated
 */
if ( ! function_exists( 'csf_add_field' ) ) {
	return;
}

/**
 * Add options for widgets
 */
if ( ! function_exists( 'codevz_in_widget_form' ) ) {
	add_filter( 'in_widget_form', 'codevz_in_widget_form', 10, 3 );
	function codevz_in_widget_form( $widget, $return, $instance ) {
	    $hide_on_tablet = isset( $instance['hide_on_tablet'] ) ? $instance['hide_on_tablet'] : '';
	    $hide_on_mobile = isset( $instance['hide_on_mobile'] ) ? $instance['hide_on_mobile'] : '';
	    $center_on_mobile = isset( $instance['center_on_mobile'] ) ? $instance['center_on_mobile'] : 1; ?>
	        <p>
	            <input class="checkbox" type="checkbox" id="<?php echo esc_attr( $widget->get_field_id( 'hide_on_tablet' ) ); ?>" name="<?php echo esc_attr( $widget->get_field_name( 'hide_on_tablet' ) ); ?>" <?php checked( true , $hide_on_tablet); ?> />
	            <label for="<?php echo esc_attr( $widget->get_field_id( 'hide_on_tablet' ) ); ?>">
	                <?php esc_html_e( 'Hide on Tablet?', 'codevz' ); ?>
	            </label>
	        </p>
	        <p>
	            <input class="checkbox" type="checkbox" id="<?php echo esc_attr( $widget->get_field_id( 'hide_on_mobile' ) ); ?>" name="<?php echo esc_attr( $widget->get_field_name( 'hide_on_mobile' ) ); ?>" <?php checked( true , $hide_on_mobile); ?> />
	            <label for="<?php echo esc_attr( $widget->get_field_id( 'hide_on_mobile' ) ); ?>">
	                <?php esc_html_e( 'Hide on Mobile?', 'codevz' ); ?>
	            </label>
	        </p>
	        <p>
	            <input class="checkbox" type="checkbox" id="<?php echo esc_attr( $widget->get_field_id( 'center_on_mobile' ) ); ?>" name="<?php echo esc_attr( $widget->get_field_name( 'center_on_mobile' ) ); ?>" <?php checked( true , $center_on_mobile); ?> />
	            <label for="<?php echo esc_attr( $widget->get_field_id( 'center_on_mobile' ) ); ?>">
	                <?php esc_html_e( 'Center align on Mobile?', 'codevz' ); ?>
	            </label>
	        </p>
	        <p>
				<?php 
					echo csf_add_field( array(
						'id'    	=> $widget->get_field_id('czsk'),
						'hover_id'  => $widget->get_field_id('czsk') . '_hover',
						'name'  	=> $widget->get_field_name('czsk'),
						'type'  	=> 'cz_sk',
						'title' 	=> '',
						'button' 	=> esc_html__( 'Custom styling', 'codevz' ),
						'settings' 	=> array( 'color', 'background', 'padding', 'margin', 'border' ),
					), esc_attr( isset( $instance['czsk'] ) ? $instance['czsk'] : '' ) );
					
					echo csf_add_field( array(
						'id'    	=> $widget->get_field_id('czsk_hover'),
						'name'  	=> $widget->get_field_name('czsk_hover'),
						'type'  	=> 'cz_sk_hidden',
						'title' 	=> '',
					), esc_attr( isset( $instance['czsk_hover'] ) ? $instance['czsk_hover'] : '' ) );
					
					echo csf_add_field( array(
						'id'    	=> $widget->get_field_id('czsk_tablet'),
						'name'  	=> $widget->get_field_name('czsk_tablet'),
						'type'  	=> 'cz_sk_hidden',
						'title' 	=> '',
					), esc_attr( isset( $instance['czsk_tablet'] ) ? $instance['czsk_tablet'] : '' ) );
					
					echo csf_add_field( array(
						'id'    	=> $widget->get_field_id('czsk_mobile'),
						'name'  	=> $widget->get_field_name('czsk_mobile'),
						'type'  	=> 'cz_sk_hidden',
						'title' 	=> '',
					), esc_attr( isset( $instance['czsk_mobile'] ) ? $instance['czsk_mobile'] : '' ) );
				?>
	        </p>
	    <?php
	}
}

/**
 * Save custom options for widgets
 */
if ( ! function_exists( 'codevz_widget_update_callback' ) ) {
	add_filter( 'widget_update_callback', 'codevz_widget_update_callback', 10, 3 );
	function codevz_widget_update_callback( $instance, $new_instance ) {
		$new_instance['hide_on_tablet'] = empty( $new_instance['hide_on_tablet'] ) ? 0 : 1;
		$new_instance['hide_on_mobile'] = empty( $new_instance['hide_on_mobile'] ) ? 0 : 1;
		$new_instance['center_on_mobile'] = empty( $new_instance['center_on_mobile'] ) ? 0 : 1;
		$new_instance['czsk'] = empty( $new_instance['czsk'] ) ? '' : $new_instance['czsk'];
		$new_instance['czsk_hover'] = empty( $new_instance['czsk_hover'] ) ? '' : $new_instance['czsk_hover'];
		$new_instance['czsk_tablet'] = empty( $new_instance['czsk_tablet'] ) ? '' : $new_instance['czsk_tablet'];
		$new_instance['czsk_mobile'] = empty( $new_instance['czsk_mobile'] ) ? '' : $new_instance['czsk_mobile'];

		return $new_instance;
	}
}

/**
 * Output of custom options for widget
 */
if ( ! function_exists( 'codevz_widget_display_callback' ) ) {
	add_filter( 'widget_display_callback', 'codevz_widget_display_callback', 10, 3 );
	function codevz_widget_display_callback( $instance, $widget_class, $args ) {

		if ( $instance == false ) {
			return $instance;
		}

		$css = $inline = '';
		if ( ! empty( $widget_class->id ) ) {
			$id = $widget_class->id;

			if ( ! empty( $instance['czsk'] ) ) {
				$css .= '#' . $id . '{' . $instance['czsk'] . '}';
			}
			if ( ! empty( $instance['czsk_hover'] ) ) {
				$css .= '#' . $id . ':hover{' . $instance['czsk_hover'] . '}';
			}
			if ( ! empty( $instance['czsk_tablet'] ) ) {
				$css .= '@media screen and (max-width:768px){#' . $id . '{' . $instance['czsk_tablet'] . '}}';
			}
			if ( ! empty( $instance['czsk_mobile'] ) ) {
				$css .= '@media screen and (max-width:480px){#' . $id . '{' . $instance['czsk_mobile'] . '}}';
			}

			$css = $css ? 'data-cz-style="' . $css . '" ' : '';
		}

		$new_class = $css . 'class="';
		$new_class .= empty( $instance['hide_on_tablet'] ) ? '' : 'hide_on_tablet ';
		$new_class .= empty( $instance['hide_on_mobile'] ) ? '' : 'hide_on_mobile ';
		$new_class .= empty( $instance['center_on_mobile'] ) ? '' : 'center_on_mobile ';

		if ( empty( $instance['hide_on_tablet'] ) || empty( $instance['hide_on_mobile'] ) || empty( $instance['center_on_mobile'] ) ) {
			$args['before_widget'] = str_replace('class="', $new_class, $args['before_widget']);
			$widget_class->widget( $args, $instance );
			return false;
		} else {
			return $instance;	
		}
	}
}

/**
 * Facebook widget
 */
if ( ! class_exists( 'CodevzFacebook' ) ) {

	class CodevzFacebook extends WP_Widget {

		function __construct() {
			parent::__construct(
				false, 
				CDVZ_VC_CAT . ' - ' . esc_html__( 'Facebook', 'codevz' ), 
				array( 'classname' => 'cz_facebook' )
			);
		}

		function widget($args, $instance){
			extract( $args );
			ob_start();
			$title = apply_filters( 'widget_title', $instance['title'] );
			echo $before_widget;
			echo $title ? $before_title . $title . $after_title : ''; 
			echo '<div id="fb-root"></div>'; ?>
				<div class="fb-page" 
					data-href="<?php echo isset( $instance['url'] ) ? esc_url( $instance['url'] ) : ''; ?>" 
					data-small-header="<?php echo isset( $instance['head'] ) ? $instance['head'] : ''; ?>" 
					data-adapt-container-width="true" 
					data-hide-cover="<?php echo isset( $instance['cover'] ) ? $instance['cover'] : ''; ?>" 
					data-hide-cta="false" 
					data-show-facepile="<?php echo isset( $instance['faces'] ) ? $instance['faces'] : ''; ?>" 
					data-show-posts="<?php echo isset( $instance['posts'] ) ? $instance['posts'] : ''; ?>">
				</div><script>(function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(d.getElementById(id))return;js=d.createElement(s);js.id=id;js.src="//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5&appId=376512092550885";fjs.parentNode.insertBefore(js,fjs)}(document,'script','facebook-jssdk'));</script>
			<?php 

			echo empty( $instance['url'] ) ? esc_html__( 'Please insert correct facebook url page.', 'codevz' ) : '';
			echo $after_widget;
			$out = ob_get_clean();

			echo apply_filters( 'widget_text', $out );
		}
		
		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title'] = $new_instance['title'];
			$instance['url'] = isset( $new_instance['url'] ) ? $new_instance['url'] : '';
			$instance['head'] = isset( $new_instance['head'] ) ? $new_instance['head'] : '';
			$instance['cover'] = isset( $new_instance['cover'] ) ? $new_instance['cover'] : '';
			$instance['posts'] = isset( $new_instance['posts'] ) ? $new_instance['posts'] : '';
			$instance['faces'] = isset( $new_instance['faces'] ) ? $new_instance['faces'] : '';

			return $instance;
		}

		function form($instance){
			$defaults = array('title' => 'Like us on Facebook', 'url' => '', 'head' => false, 'cover' => false, 'posts' => true, 'faces' => true);
			$instance = wp_parse_args((array) $instance, $defaults); 
			
			$title_value = esc_attr( $instance['title'] );
			$title_field = array(
				'id'    => $this->get_field_name('title'),
				'name'  => $this->get_field_name('title'),
				'type'  => 'text',
				'title' => esc_html__('Title', 'codevz')
			);
			echo csf_add_field( $title_field, $title_value );

			$url_value = esc_attr( $instance['url'] );
			$url_field = array(
				'id'    => $this->get_field_name('url'),
				'name'  => $this->get_field_name('url'),
				'type'  => 'text',
				'title' => esc_html__('Page URL', 'codevz')
			);
			echo csf_add_field( $url_field, $url_value );

			$head_value = esc_attr( $instance['head'] );
			$head_field = array(
				'id'    => $this->get_field_name('head'),
				'name'  => $this->get_field_name('head'),
				'type'  => 'switcher',
				'default' => false,
				'title' => esc_html__('Use Small Header', 'codevz')
			);
			echo csf_add_field( $head_field, $head_value );

			$cover_value = esc_attr( $instance['cover'] );
			$cover_field = array(
				'id'    => $this->get_field_name('cover'),
				'name'  => $this->get_field_name('cover'),
				'type'  => 'switcher',
				'default' => false,
				'title' => esc_html__('Hide Cover Photo', 'codevz')
			);
			echo csf_add_field( $cover_field, $cover_value );

			$posts_value = esc_attr( $instance['posts'] );
			$posts_field = array(
				'id'    => $this->get_field_name('posts'),
				'name'  => $this->get_field_name('posts'),
				'type'  => 'switcher',
				'default' => true,
				'title' => esc_html__('Show Page Posts', 'codevz')
			);
			echo csf_add_field( $posts_field, $posts_value );

			$faces_value = esc_attr( $instance['faces'] );
			$faces_field = array(
				'id'    => $this->get_field_name('faces'),
				'name'  => $this->get_field_name('faces'),
				'type'  => 'switcher',
				'default' => true,
				'title' => esc_html__('Show Friends Faces', 'codevz')
			);
			echo csf_add_field( $faces_field, $faces_value );
		}
	}

}


/**
 * Custom nav list widget
 */
if ( ! class_exists( 'CodevzCustomMenuList' ) ) {

	class CodevzCustomMenuList extends WP_Widget {

		function __construct() {
			parent::__construct(
				false, 
				CDVZ_VC_CAT . ' - ' . esc_html__( 'Custom nav menu', 'codevz' ), 
				array( 'classname' => 'cz_custom_menu_list' )
			);
		}

		function widget( $args, $instance ){
			extract( $args );

			ob_start();
			echo $before_widget;
			wp_nav_menu( array( 'menu' => $instance['menu'] ) );
			echo $after_widget;
			$out = ob_get_clean();

			echo apply_filters( 'widget_text', $out );
		}
		
		function update($new_instance, $old_instance){
			$instance = $old_instance;
			$instance['title'] = $new_instance['title'];
			$instance['menu'] = $new_instance['menu'];

			return $instance;
		}

		function form( $instance ) {
			$instance = wp_parse_args( $instance, array('title' => '', 'menu' => 'primary') ); 
			
			$title_value = esc_attr( $instance['title'] );
			$title_field = array(
				'id'    => $this->get_field_name('title'),
				'name'  => $this->get_field_name('title'),
				'type'  => 'text',
				'title' => esc_html__('Title', 'codevz')
			);
			echo csf_add_field( $title_field, $title_value );

			$menu_value = esc_attr( $instance['menu'] );
			$menu_field = array(
				'id'    => $this->get_field_name('menu'),
				'name'  => $this->get_field_name('menu'),
				'type'  => 'select',
				'options' => get_registered_nav_menus(),
				'default' => 'primary',
				'title' => esc_html__('Menu', 'codevz')
			);
			echo csf_add_field( $menu_field, $menu_value );
		}
	}

}


/**
 * Custom menu list widget
 */
if ( ! class_exists( 'CodevzCustomMenuList2' ) ) {

	class CodevzCustomMenuList2 extends WP_Widget {

		public static $count = 18;

		function __construct() {
			parent::__construct(
				false, 
				CDVZ_VC_CAT . ' - ' . esc_html__( 'Custom menu list', 'codevz' ), 
				array( 'classname' => 'cz_custom_menu_list_2' )
			);
		}

		function widget( $args, $instance ){
			extract( $args );
			ob_start();
			echo $before_widget;
			$title = apply_filters( 'widget_title', $instance['title'] );
			echo $title ? $before_title . $title . $after_title : '';

			$col = empty( $instance['two_col'] ) ? '' : 'col s6';
			echo '<div class="clr">';
			for ( $i = 1; $i < self::$count; $i++ ) {
				if ( ! empty( $instance[ 'title_' . $i ] ) && ! empty( $instance[ 'link_' . $i ] ) ) {
					echo '<div class="' . $col . '"><a href="' . $instance[ 'link_' . $i ] . '">' . $instance[ 'title_' . $i ] . '</a></div>';
					if ( $col && $i % 2 === 0 ) {
						echo '</div><div class="clr">';
					}
				}
			}
			echo '</div>';
			
			echo $after_widget;

			$out = ob_get_clean();
			echo apply_filters( 'widget_text', $out );
		}
		
		function update($new_instance, $old_instance){
			$instance = $old_instance;
			$instance['title'] = $new_instance['title'];
			$instance['two_col'] = isset( $new_instance['two_col'] ) ? $new_instance['two_col'] : null;

			for ( $i = 1; $i < self::$count; $i++ ) {
				$instance[ 'title_' . $i ] = $new_instance[ 'title_' . $i ];
				$instance[ 'link_' . $i ] = $new_instance[ 'link_' . $i ];
			}

			return $instance;
		}

		function form( $instance ) {

			$defaults = array(
				'title'		=> '',
				'two_col'	=> '',
			);
			for ( $i = 1; $i < self::$count; $i++ ) {
				$defaults[ 'title_' . $i ] = '';
				$defaults[ 'link_' . $i ] = '';
			}
			$instance = wp_parse_args( $instance, $defaults );

			echo csf_add_field( array(
				'id'    => $this->get_field_name('title'),
				'name'  => $this->get_field_name('title'),
				'type'  => 'text',
				'title' => esc_html__('Title', 'codevz')
			), esc_attr( $instance['title'] ) );

			echo csf_add_field( array(
				'id'    => $this->get_field_name('two_col'),
				'name'  => $this->get_field_name('two_col'),
				'type'  => 'switcher',
				'title' => esc_html__('Two columns ?', 'codevz')
			), esc_attr( $instance['two_col'] ) );

			for ( $i = 1; $i < self::$count; $i++ ) { 
				echo csf_add_field( array(
					'id'    => $this->get_field_name( 'title_' . $i ),
					'name'  => $this->get_field_name( 'title_' . $i),
					'type'  => 'text',
					'title' => esc_html__( 'Title', 'codevz' ) . ' ' . $i
				), esc_attr( $instance[ 'title_' . $i ] ) );

				echo csf_add_field( array(
					'id'    => $this->get_field_name( 'link_' . $i ),
					'name'  => $this->get_field_name( 'link_' . $i),
					'type'  => 'text',
					'title' => esc_html__( 'Link', 'codevz' ) . ' ' . $i
				), esc_attr( $instance[ 'link_' . $i ] ) );
			}

		}
	}

}


/**
 * Flickr
 */
if ( !class_exists( 'CodevzFlickr' ) ) {

	class CodevzFlickr extends WP_Widget {

		function __construct() {
			parent::__construct(
				false, 
				CDVZ_VC_CAT . ' - ' . esc_html__( 'Flickr', 'codevz' ), 
				array( 'classname' => 'cz_flickr' )
			);
		}
		
		function form($instance) {
			$defaults = array(
				'title' => 'Flickr Photostream',
				'id' => '7388060@N08',
				'type' => 'user',
				'number' => '9',
				'shorting' => 'latest',
			);
			$instance = wp_parse_args( (array) $instance, $defaults );
			
			$title_field = array(
				'id'    => $this->get_field_name('title'),
				'name'  => $this->get_field_name('title'),
				'type'  => 'text',
				'title' => esc_html__('Title', 'codevz')
			);
			echo csf_add_field( $title_field, esc_attr( $instance['title'] ) );

			$id_field = array(
				'id'    => $this->get_field_name('id'),
				'name'  => $this->get_field_name('id'),
				'type'  => 'text',
				'title' => esc_html__('Flikr ID ( idgettr.com )', 'codevz')
			);
			echo csf_add_field( $id_field, esc_attr( $instance['id'] ) );

			$number_field = array(
				'id'    => $this->get_field_name('number'),
				'name'  => $this->get_field_name('number'),
				'type'  => 'text',
				'title' => esc_html__('Count', 'codevz')
			);
			echo csf_add_field( $number_field, esc_attr( $instance['number'] ) );

			$type_field = array(
				'id'    => $this->get_field_name('type'),
				'name'  => $this->get_field_name('type'),
				'type'  => 'select',
				'options' => array(
					'user' => esc_html__('User', 'codevz'),
					'group' => esc_html__('Group', 'codevz')
				),
				'title' => esc_html__('Type', 'codevz')
			);
			echo csf_add_field( $type_field, esc_attr( $instance['type'] ) );

			$shorting_field = array(
				'id'    => $this->get_field_name('shorting'),
				'name'  => $this->get_field_name('shorting'),
				'type'  => 'select',
				'options' => array(
					'latest' => esc_html__('Latest Photos', 'codevz'),
					'random' => esc_html__('Random', 'codevz')
				),
				'title' => esc_html__('Sorting', 'codevz')
			);
			echo csf_add_field( $shorting_field, esc_attr( $instance['shorting'] ) );
		}

		function update($new_instance, $old_instance) {
			$instance = $old_instance;
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['number'] = strip_tags( $new_instance['number'] );
			$instance['id'] = strip_tags( $new_instance['id'] );
			$instance['type'] = strip_tags( $new_instance['type'] );
			$instance['shorting'] = strip_tags( $new_instance['shorting'] );

			return $instance;
		}

		function widget( $args, $instance ) {
			extract( $args );
			ob_start();
			$title = apply_filters( 'widget_title', $instance['title'] );
			$number = esc_attr( $instance['number'] );
			$shorting = esc_attr( $instance['shorting'] );
			$type = esc_attr( $instance['type'] );
			$id = esc_attr( $instance['id'] );
			echo $before_widget;
			echo $title ? $before_title . esc_attr( $title ) . $after_title : '';

			if ( $id ) : ?>
				<div class="flickr-widget clr">
					<sc<?php echo 'r'; ?>ipt type="text/javascript" src="<?php echo '//www.flickr.com/badge_code_v2.gne?count=' . $number . '&amp;display=' . $shorting . '&amp;&amp;layout=x&amp;source=' . $type . '&amp;' . $type . '=' . $id . '&amp;size=s'; ?>"></sc<?php echo 'r'; ?>ipt> 
				</div>
			<?php endif;

			echo $after_widget;

			$out = ob_get_clean();
			echo apply_filters( 'widget_text', $out );
		}
	 
	}

}

/**
 * Soundcloud
 */
if ( !class_exists( 'CodevzSoundcloud' ) ) {

	class CodevzSoundcloud extends WP_Widget {

		function __construct() {
			parent::__construct(
				false, 
				CDVZ_VC_CAT . ' - ' . esc_html__( 'Soundcloud', 'codevz' ), 
				array( 'classname' => 'cz_soundcloud' )
			);
		}

		function widget( $args, $instance ) {
			extract( $args );

			ob_start();
			$title = apply_filters('widget_title', $instance['title'] );
			$url = esc_url( $instance['url'] );
			$play = 'false';
			if ( ! empty( $instance['autoplay'] ) ) $play = 'true';

			echo $before_widget;
			if($title) {
				echo $before_title.$title.$after_title;
			} else {
				?> <div class="widget clr"> <?php  
			}
			?><<?php echo 'iframe'; ?> width="100%" height="166" scrolling="no" frameborder="no" src="//w.soundcloud.com/player/?url=<?php echo esc_url( $url ); ?>&amp;auto_play=<?php echo $play; ?>&amp;show_artwork=true"></<?php echo 'iframe'; ?>><?php
			echo $after_widget;

			$out = ob_get_clean();
			echo apply_filters( 'widget_text', $out );
		}
		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title'] 		= esc_html( $new_instance['title'] );
			$instance['url'] 		= esc_url( $new_instance['url'] );
			$instance['autoplay'] 	= esc_html( $new_instance['autoplay'] );
			
			return $instance;
		}
		function form( $instance ) {

			$defaults = array( 
				'title' 	=> 'SoundCloud', 
				'url' 		=> '//soundcloud.com/almerchoy/pitbull-bon-bon', 
				'autoplay' 	=> ''  
			);
			$instance = wp_parse_args( (array) $instance, $defaults ); ?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php echo esc_html__('Title :', 'codevz'); ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" type="text" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'url' ) ); ?>"><?php echo esc_html__('URL :', 'codevz'); ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'url' ) ); ?>" value="<?php echo esc_url( $instance['url'] ); ?>" type="text" class="widefat" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'autoplay' ) ); ?>"><?php echo esc_html__('Autoplay :', 'codevz'); ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'autoplay' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'autoplay' ) ); ?>" value="true" <?php if( $instance['autoplay'] ) echo 'checked="checked"'; ?> type="checkbox" />
			</p>
		<?php
		}

	}

}

/**
 * 
 * Subscribe
 * 
 */
if ( !class_exists( 'CodevzSubscribe' ) ) {

	class CodevzSubscribe extends WP_Widget {

		function __construct() {
			parent::__construct(
				false, 
				CDVZ_VC_CAT . ' - ' . esc_html__( 'Feedburner', 'codevz' ), 
				array( 'classname' => 'cz_subscribe' )
			);
		}

		function form($instance) {	
			$instance = wp_parse_args( (array) $instance, array('title' => 'Subscribe to RSS Feeds', 'subscribe_text' => 'Get all latest content delivered to your email a few times a month.', 'feedid' => '', 'placeholder' => 'Your Email', 'icon' => 'fa fa-check') );
			
			$title_value = esc_attr( $instance['title'] );
			$title_field = array(
				'id'    => $this->get_field_name('title'),
				'name'  => $this->get_field_name('title'),
				'type'  => 'text',
				'title' => esc_html__('Title', 'codevz')
			);
			echo csf_add_field( $title_field, $title_value );

			$subscribe_text_value = esc_attr( $instance['subscribe_text'] );
			$subscribe_text_field = array(
				'id'    => $this->get_field_name('subscribe_text'),
				'name'  => $this->get_field_name('subscribe_text'),
				'type'  => 'textarea',
				'title' => esc_html__('Description', 'codevz')
			);
			echo csf_add_field( $subscribe_text_field, $subscribe_text_value );

			$icon_value = esc_attr( $instance['icon'] );
			$icon_field = array(
				'id'    => $this->get_field_name('icon'),
				'name'  => $this->get_field_name('icon'),
				'type'  => 'icon',
				'title'	=> esc_html__('Icon', 'codevz'),
			);
			echo csf_add_field( $icon_field, $icon_value );

			$placeholder_value = esc_attr( $instance['placeholder'] );
			$placeholder_field = array(
				'id'    => $this->get_field_name('placeholder'),
				'name'  => $this->get_field_name('placeholder'),
				'type'  => 'text',
				'title' => esc_html__('Placeholder', 'codevz')
			);
			echo csf_add_field( $placeholder_field, $placeholder_value );

			$feedid_value = esc_attr( $instance['feedid'] );
			$feedid_field = array(
				'id'    => $this->get_field_name('feedid'),
				'name'  => $this->get_field_name('feedid'),
				'type'  => 'text',
				'title' => esc_html__('Feedburner ID or Name', 'codevz')
			);
			echo csf_add_field( $feedid_field, $feedid_value );
	    }

		function update($new_instance, $old_instance) {
			$instance=$old_instance;
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['feedid'] = $new_instance['feedid'];
			$instance['icon'] = $new_instance['icon'];
			$instance['placeholder'] = $new_instance['placeholder'];
			$instance['subscribe_text'] = $new_instance['subscribe_text'];
			$instance['checkbox'] = $new_instance['checkbox'];
			
			return $instance;
		}

		function widget($args, $instance) {
			extract($args);
			ob_start();
			$title = apply_filters('widget_title', $instance['title']);
			if ( empty($title) ) $title = false;
			$feedid = $instance['feedid'];	
			$feedbtn = $instance['icon'];	
			$placeholder = $instance['placeholder'];	
			$subscribe_text = $instance['subscribe_text'];	
			echo $before_widget;

			if($title) {
				echo $before_title.$title.$after_title;
			}
		?>
			<p><?php echo $subscribe_text; ?></p>
			<form class="widget_rss_subscription clr" action="//feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open('//feedburner.google.com/fb/a/mailverify?uri=<?php echo $feedid; ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true">
				<input type="text" placeholder="<?php echo esc_attr( $placeholder ) ?>" name="email" required />
				<input type="hidden" value="<?php echo esc_attr( $feedid ); ?>" name="uri"/>
				<input type="hidden" name="loc" value="en_US"/>
				<button type="submit" id="submit" value="Subscribe"><i class="<?php echo esc_attr( $feedbtn ); ?>"></i></button>
			</form>
		<?php
			echo $after_widget;

			$out = ob_get_clean();
			echo apply_filters( 'widget_text', $out );
		}

	}

}


/**
 * 
 * Empty widget
 * 
 */
if ( !class_exists( 'CodevzEmptyWidget' ) ) {

	class CodevzEmptyWidget extends WP_Widget {

		function __construct() {
			parent::__construct(
				false, 
				CDVZ_VC_CAT . ' - ' . esc_html__( 'Empty widget', 'codevz' ), 
				array( 'classname' => 'cz_empty_widget' )
			);
		}

		public function widget( $args, $instance ) {
			extract( $args );
			ob_start();
			$title = apply_filters('widget_title', $instance['title'] );
			echo empty( $instance['box'] ) ? str_replace( 'widget', '', $before_widget ) : $before_widget;
			echo $title ? $before_title . $title . $after_title : ''; 
			echo do_shortcode( $instance['content'] );
			echo $after_widget;

			$out = ob_get_clean();
			echo apply_filters( 'widget_text', $out );
		}
		
		public function update( $new, $old ) {
			$instance = $old;
			$instance['title'] 		= isset( $new['title'] ) ? $new['title'] : '';
			$instance['box'] 		= isset( $new['box'] ) ? $new['box'] : '';
			$instance['content'] 	= isset( $new['content'] ) ? $new['content'] : '';

			return $instance;
		}

		public function form( $instance ) {
			$instance = wp_parse_args( (array) $instance, array(
				'title' 	=> '',
				'box' 		=> 0,
				'content' 	=> '',
			));

			echo csf_add_field( array(
				'id'    => $this->get_field_name('title'),
				'name'  => $this->get_field_name('title'),
				'type'  => 'text',
				'title' => esc_html__('Title', 'codevz')
			), esc_attr( $instance['title'] ) );

			echo csf_add_field( array(
				'id'    => $this->get_field_name('box'),
				'name'  => $this->get_field_name('box'),
				'type'  => 'switcher',
				'title'	=> esc_html__('Inside box ?', 'codevz'),
			), esc_attr( $instance['box'] ) );
			
			echo csf_add_field( array(
				'id'    => $this->get_field_name('content'),
				'name'  => $this->get_field_name('content'),
				'type'  => 'textarea',
				'title' => esc_html__('Content', 'codevz')
			), esc_attr( $instance['content'] ) );

		}

	}

}

/**
 * 
 * Simple ads
 * 
 */
if ( ! class_exists( 'CodevzSimpleAds' ) ) {

	class CodevzSimpleAds extends WP_Widget {

		function __construct() {
			parent::__construct(
				false, 
				CDVZ_VC_CAT . ' - ' . esc_html__( 'Simple Ads', 'codevz'), 
				array('classname' => 'cz_simple_ads')
			);	
		}
		
		function widget( $args, $instance ) {

			extract( $args );
			$title = apply_filters('widget_title', $instance['title'] );
			$out = $before_widget."\n";
			$out .= $title ? $before_title.$title.$after_title : '';
			$out .= '<a href="'.esc_url( $instance['link'] ).'" target="_blank" title="'.esc_attr( $title ).'"><img src="'.esc_url( $instance['img'] ).'" alt="'.esc_attr( $title ).'" width="200" height="200" /></a>';
			$out .= $instance['custom'];
			$out .= $after_widget."\n";

			echo apply_filters( 'widget_text', $out );
		}

		public function update($new,$old) {

			$instance = $old;
			$instance['title'] = esc_html( $new['title'] );
			$instance['img'] = esc_url( $new['img'] );
			$instance['link'] = esc_url( $new['link'] );
			$instance['custom'] = $new['custom'];

			return $instance;
		}
		 
		public function form($instance) {

			$defaults = array('title' => '','link' => '','img' => '', 'custom' => '');
			$instance = wp_parse_args( (array) $instance, $defaults );

			echo csf_add_field( array(
				'id'    => $this->get_field_name('title'),
				'name'  => $this->get_field_name('title'),
				'type'  => 'text',
				'title' => esc_html__('Title', 'codevz')
			), esc_attr( $instance['title'] ) ); 

			echo csf_add_field( array(
				'id'    => $this->get_field_name('img'),
				'name'  => $this->get_field_name('img'),
				'type'  => 'upload',
				'title' => esc_html__('Image', 'codevz')
			), esc_attr( $instance['img'] ) );

			echo csf_add_field( array(
				'id'    => $this->get_field_name('link'),
				'name'  => $this->get_field_name('link'),
				'type'  => 'text',
				'title' => esc_html__('Link', 'codevz')
			), esc_attr( $instance['link'] ) );

			echo csf_add_field( array(
				'id'    => $this->get_field_name('custom'),
				'name'  => $this->get_field_name('custom'),
				'type'  => 'textarea',
				'sanitize' => false,
				'title' => esc_html__('Custom Ads', 'codevz')
			), $instance['custom'] );

		}

	}

}

/**
 * 
 * Load page content
 * 
 */
if ( ! class_exists( 'CodevzPageContent' ) ) {

	class CodevzPageContent extends WP_Widget {

		function __construct() {
			parent::__construct(
				false, 
				CDVZ_VC_CAT . ' - ' . esc_html__( 'Page Content', 'codevz'), 
				array('classname' => 'cz_page_content_widget')
			);	
		}
		
		function widget( $args, $instance ) {
			extract( $args );
			if ( ! empty( $instance['id'] ) ) {
				ob_start();
				echo $before_widget;
				$title = apply_filters('widget_title', $instance['title'] );
				echo $title ? $before_title . $title . $after_title : '';
				echo Codevz_Plus::get_page_as_element( $instance['id'] );
				echo $after_widget;

				$out = ob_get_clean();
				echo apply_filters( 'widget_text', $out );
			}
		}

		public function update($new,$old) {

			$instance = $old;
			$instance['title'] 	= esc_html( $new['title'] );
			$instance['id'] 	= esc_html( $new['id'] );

			return $instance;
		}
		 
		public function form( $instance ) {

			$defaults = array( 'title' => '', 'id' => '' );
			$instance = wp_parse_args( (array) $instance, $defaults );

			echo csf_add_field( array(
				'id'    => $this->get_field_name('title'),
				'name'  => $this->get_field_name('title'),
				'type'  => 'text',
				'title' => esc_html__('Title', 'codevz')
			), esc_attr( $instance['title'] ) );

			echo csf_add_field( array(
				'id'            => $this->get_field_name('id'),
				'name'  		=> $this->get_field_name('id'),
				'type'          => 'select',
				'title'         => esc_html__('Page', 'codevz'),
				'options'       => Codevz_Plus::$array_pages,
			), esc_attr( $instance['id'] ) );

		}

	}

}

/**
 * 
 * Gallery
 * 
 */
if ( !class_exists( 'CodevzPortfolio' ) ) {

	class CodevzPortfolio extends WP_Widget {

		function __construct() {
			parent::__construct(
				false, 
				CDVZ_VC_CAT . ' - ' . esc_html__( 'Portfolio', 'codevz' ), 
				array( 'classname' => 'cz_portfolio_widget' )
			);
		}

		public function widget($args, $instance) {
			extract( $args );
			$title = apply_filters( 'widget_title', $instance['title'] );
			$out = $before_widget . "\n";
			$out .= $title ? $before_title . $title . $after_title : '';
			ob_start();
			$gallery_order = isset($instance['gallery_order']) ? $instance['gallery_order'] : 'DESC';
			$popular = new WP_Query( array(
				'post_type'		=> 'portfolio',
				'order'			=> $gallery_order,
				'showposts'		=> $instance['posts_num']
			) );
		?>
			
		<div class="cd_gallery_in clr">
			<?php while ( $popular->have_posts() ): $popular->the_post(); ?>
					<?php if ( has_post_thumbnail() ): ?>
						<a class="cdEffect noborder" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
							<?php the_post_thumbnail( 'thumbnail' ); ?>
						</a>
					<?php endif; ?>
			<?php endwhile; wp_reset_query(); ?>
		</div>

		<?php
			$out .= ob_get_clean();
			$out .= $after_widget."\n";

			echo apply_filters( 'widget_text', $out );
		}
		
		public function update($new,$old) {
			$instance = $old;
			$instance['title'] = esc_html($new['title']);
			$instance['gallery_order'] = esc_html($new['gallery_order']);
			$instance['posts_num'] = esc_html($new['posts_num']);
			return $instance;
		}

		public function form($instance) {
			$defaults = array(
				'title' 			=> 'Portfolio',
				'gallery_order' 	=> 'DESC',
				'posts_num' 		=> '9'
			);
			$instance = wp_parse_args( (array) $instance, $defaults );
			
			$title_value = esc_attr( $instance['title'] );
			$title_field = array(
				'id'    => $this->get_field_name('title'),
				'name'  => $this->get_field_name('title'),
				'type'  => 'text',
				'title' => esc_html__('Title', 'codevz')
			);
			echo csf_add_field( $title_field, $title_value );

			$posts_num_value = esc_attr( $instance['posts_num'] );
			$posts_num_field = array(
				'id'    => $this->get_field_name('posts_num'),
				'name'  => $this->get_field_name('posts_num'),
				'type'  => 'number',
				'title'	=> esc_html__('Count', 'codevz'),
			);
			echo csf_add_field( $posts_num_field, $posts_num_value );

			$gallery_order_value = esc_attr( $instance['gallery_order'] );
			$gallery_order_field = array(
				'id'    => $this->get_field_name('gallery_order'),
				'name'  => $this->get_field_name('gallery_order'),
				'type'  => 'radio',
				'options' => array(
					'DESC' => 'DESC',
					'ASC' => 'ASC'
				),
				'title' => esc_html__('Order', 'codevz')
			);
			echo csf_add_field( $gallery_order_field, $gallery_order_value );
		}
	}

}


/**
 * 
 * Custom taxonomy list widget
 * 
 */
if ( ! function_exists( 'init_lc_taxonomy' ) ) {

class lc_taxonomy extends WP_Widget {

	function __construct() {
		parent::__construct(
			false, 
			CDVZ_VC_CAT . ' - ' . esc_html__( 'Taxonomy menus', 'codevz'), 
			array('classname' => 'lc_taxonomy')
		);
	}

	function widget( $args, $instance ) {
		global $post;
		extract($args);
		ob_start();

		// Widget options
		$title 	 = apply_filters('widget_title', $instance['title'] ); // Title		
		$this_taxonomy = $instance['taxonomy']; // Taxonomy to show
		$hierarchical = !empty( $instance['hierarchical'] ) ? '1' : '0';
		$showcount = !empty( $instance['count'] ) ? '1' : '0';
		if( array_key_exists('orderby',$instance) ){
			$orderby = $instance['orderby'];
		}
		else{
			$orderby = 'count';
		}
		if( array_key_exists('ascdsc',$instance) ){
			$ascdsc = $instance['ascdsc'];
		}
		else{
			$ascdsc = 'desc';
		}
		if( array_key_exists('exclude',$instance) ){
			$exclude = $instance['exclude'];
		}
		else {
			$exclude = '';
		}
		if( array_key_exists('childof',$instance) ){
			$childof = $instance['childof'];
		}
		else {
			$childof = '';
		}
		if( array_key_exists('dropdown',$instance) ){
			$dropdown = $instance['dropdown'];
		}
		else {
			$dropdown = false;
		}
        // Output
		$tax = $this_taxonomy;
		echo $before_widget;
		echo '<div id="lct-widget-'.$tax.'-container" class="list-custom-taxonomy-widget">';
		echo $before_title . $title . $after_title;
		if($dropdown){
			$taxonomy_object = get_taxonomy( $tax );
			$args = array(
				'show_option_all'    => false,
				'show_option_none'   => '',
				'orderby'            => $orderby,
				'order'              => $ascdsc,
				'show_count'         => $showcount,
				'hide_empty'         => 1,
				'child_of'           => $childof,
				'exclude'            => $exclude,
				'echo'               => 1,
				//'selected'           => 0,
				'hierarchical'       => $hierarchical,
				'name'               => $taxonomy_object->query_var,
				'id'                 => 'lct-widget-'.$tax,
				//'class'              => 'postform',
				'depth'              => 0,
				//'tab_index'          => 0,
				'taxonomy'           => $tax,
				'hide_if_empty'      => true
			);
			echo '<form action="'. esc_url( home_url( '/' ) ). '" method="get">';
			wp_dropdown_categories($args);
			echo '<input type="submit" value="go &raquo;" /></form>';
		}
		else {
			$args = array(
					'show_option_all'    => false,
					'orderby'            => $orderby,
					'order'              => $ascdsc,
					'style'              => 'list',
					'show_count'         => $showcount,
					'hide_empty'         => 1,
					'use_desc_for_title' => 1,
					'child_of'           => $childof,
					//'feed'               => '',
					//'feed_type'          => '',
					//'feed_image'         => '',
					'exclude'            => $exclude,
					//'exclude_tree'       => '',
					//'include'            => '',
					'hierarchical'       => $hierarchical,
					'title_li'           => '',
					'show_option_none'   => 'No Categories',
					'number'             => null,
					'echo'               => 1,
					'depth'              => 0,
					//'current_category'   => 0,
					//'pad_counts'         => 0,
					'taxonomy'           => $tax
				);
			echo '<ul id="lct-widget-'.$tax.'">';
			wp_list_categories($args);
			echo '</ul>';
		}
		echo '</div>';
		echo $after_widget;

		$out = ob_get_clean();
		echo apply_filters( 'widget_text', $out );
	}
	/** Widget control update */
	function update( $new_instance, $old_instance ) {
		$instance    = $old_instance;
		
		$instance['title']  = strip_tags( $new_instance['title'] );
		$instance['taxonomy'] = strip_tags( $new_instance['taxonomy'] );
		$instance['orderby'] = $new_instance['orderby'];
		$instance['ascdsc'] = $new_instance['ascdsc'];
		$instance['exclude'] = $new_instance['exclude'];
		$instance['expandoptions'] = $new_instance['expandoptions'];
		$instance['childof'] = $new_instance['childof'];
		$instance['hierarchical'] = !empty($new_instance['hierarchical']) ? 1 : 0;
        $instance['count'] = !empty($new_instance['count']) ? 1 : 0;
        $instance['dropdown'] = !empty($new_instance['dropdown']) ? 1 : 0;

		return $instance;
	}
	
	/* Widget settings */
	function form( $instance ) {
		echo "<sc" . "r" . "ipt>function lctwExpand(t){jQuery('#'+t).val('expand'),jQuery('.lctw-all-options').show(500),jQuery('.lctw-expand-options').hide(500)}function lctwContract(t){jQuery('#'+t).val('contract'),jQuery('.lctw-all-options').hide(500),jQuery('.lctw-expand-options').show(500)}jQuery(document).ready(function(){var t=jQuery('#" . $this->get_field_id('expandoptions') . "').val();'expand'==t?jQuery('.lctw-expand-options').hide():'contract'==t&&jQuery('.lctw-all-options').hide()});</sc" . "r" . "ipt>";
		if ( $instance ) {
			$title  = $instance['title'];
			$this_taxonomy = $instance['taxonomy'];
			$orderby = $instance['orderby'];
			$ascdsc = $instance['ascdsc'];
			$exclude = $instance['exclude'];
			$expandoptions = $instance['expandoptions'];
			$childof = $instance['childof'];
			$showcount = isset($instance['count']) ? (bool) $instance['count'] :false;
			$hierarchical = isset( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
			$dropdown = isset( $instance['dropdown'] ) ? (bool) $instance['dropdown'] : false;
		} else {
			$title  = '';
			$orderby  = 'count';
			$ascdsc  = 'desc';
			$exclude  = '';
			$expandoptions  = 'contract';
			$childof  = '';
			$this_taxonomy = 'category';//this will display the category taxonomy, which is used for normal, built-in posts
			$hierarchical = true;
			$showcount = true;
			$dropdown = false;
		}

		?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id('title') ); ?>"><?php echo esc_html__( 'Title:', 'codevz' ); ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" class="widefat" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id('taxonomy') ); ?>"><?php echo esc_html__( 'Select Taxonomy:', 'codevz' ); ?></label>
				<select name="<?php echo esc_attr( $this->get_field_name('taxonomy') ); ?>" id="<?php echo esc_attr( $this->get_field_id('taxonomy') ); ?>" class="widefat" style="height: auto;" size="4">
			<?php 
			$args=array(
			  'public'   => true,
			  '_builtin' => false //these are manually added to the array later
			); 
			$output = 'names'; // or objects
			$operator = 'and'; // 'and' or 'or'
			$taxonomies=get_taxonomies($args,$output,$operator); 
			$taxonomies[] = 'category';
			$taxonomies[] = 'post_tag';
			$taxonomies[] = 'post_format';
			foreach ($taxonomies as $taxonomy ) { ?>
				<option value="<?php echo esc_attr( $taxonomy ); ?>" <?php if( $taxonomy == $this_taxonomy ) { echo 'selected="selected"'; } ?>><?php echo $taxonomy; ?></option>
			<?php }	?>
			</select>
			</p>
			<h4 class="lctw-expand-options"><a href="javascript:void(0)" onclick="lctwExpand('<?php echo esc_attr( $this->get_field_id('expandoptions') ); ?>')" >More Options...</a></h4>
			<div class="lctw-all-options">
				<h4 class="lctw-contract-options"><a href="javascript:void(0)" onclick="lctwContract('<?php echo esc_attr( $this->get_field_id('expandoptions') ); ?>')" >Hide Extended Options</a></h4>
				<input type="hidden" value="<?php echo esc_attr( $expandoptions ); ?>" id="<?php echo esc_attr( $this->get_field_id('expandoptions') ); ?>" name="<?php echo esc_attr( $this->get_field_name('expandoptions') ); ?>" />
				
				<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('count') ); ?>" name="<?php echo esc_attr( $this->get_field_name('count') ); ?>"<?php checked( $showcount ); ?> />
				<label for="<?php echo esc_attr( $this->get_field_id('count') ); ?>"><?php _e( 'Show post counts', 'codevz' ); ?></label><br />
				<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('hierarchical') ); ?>" name="<?php echo esc_attr( $this->get_field_name('hierarchical') ); ?>"<?php checked( $hierarchical ); ?> />
				<label for="<?php echo esc_attr( $this->get_field_id('hierarchical') ); ?>"><?php _e( 'Show hierarchy', 'codevz' ); ?></label></p>
				
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('orderby') ); ?>"><?php echo esc_html__( 'Order By:', 'codevz' ); ?></label>
					<select name="<?php echo esc_attr( $this->get_field_name('orderby') ); ?>" id="<?php echo esc_attr( $this->get_field_id('orderby') ); ?>" class="widefat" >
						<option value="ID" <?php if( $orderby == 'ID' ) { echo 'selected="selected"'; } ?>>ID</option>
						<option value="name" <?php if( $orderby == 'name' ) { echo 'selected="selected"'; } ?>>Name</option>
						<option value="slug" <?php if( $orderby == 'slug' ) { echo 'selected="selected"'; } ?>>Slug</option>
						<option value="count" <?php if( $orderby == 'count' ) { echo 'selected="selected"'; } ?>>Count</option>
						<option value="term_group" <?php if( $orderby == 'term_group' ) { echo 'selected="selected"'; } ?>>Term Group</option>
					</select>
				</p>
				<p>
					<label><input type="radio" name="<?php echo esc_attr( $this->get_field_name('ascdsc') ); ?>" value="asc" <?php if( $ascdsc == 'asc' ) { echo 'checked'; } ?>/> Ascending</label><br/>
					<label><input type="radio" name="<?php echo esc_attr( $this->get_field_name('ascdsc') ); ?>" value="desc" <?php if( $ascdsc == 'desc' ) { echo 'checked'; } ?>/> Descending</label>
				</p>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('exclude') ); ?>">Exclude (comma-separated list of ids to exclude)</label><br/>
					<input type="text" class="widefat" name="<?php echo esc_attr( $this->get_field_name('exclude') ); ?>" value="<?php echo esc_attr( $exclude ); ?>" />
				</p>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('exclude') ); ?>">Only Show Children of (category id)</label><br/>
					<input type="text" class="widefat" name="<?php echo esc_attr( $this->get_field_name('childof') ); ?>" value="<?php echo esc_attr( $childof ); ?>" />
				</p>
			</div>
<?php 
	}

}
}

/**
 * 
 * Posts widget
 * 
 */
if ( ! class_exists( 'CodevzPostsList' ) ) {

	class CodevzPostsList extends WP_Widget {

		function __construct() {
			parent::__construct( false, CDVZ_VC_CAT . ' - ' . esc_html__( 'Posts list', 'codevz' ) );
		}

		function widget( $args, $instance ) {
			extract( $args, EXTR_SKIP );

	        $title = apply_filters( 'widget_title', $instance['title'] );
	        $post_amount = $instance['show'];
			$post_orderby = $instance['orderby'];
			$post_order = $instance['order'];
			$post_catin = $instance['catin'];
			$post_catout = $instance['catout'];
			$pagecount = $instance['pagecount'];
			$post_taxis = $instance['taxis'];
			$post_taxterm = $instance['taxterm'];
			$post_typed = $instance['ptipe'];
			$post_metakey = $instance['metakey'];
			$post_metavalue = $instance['metavalue'];
			$post_comparison = $instance['metacompare'];
			$post_widgeid = $instance['widgetidentifier'];
			$post_widgeclass = $instance['widgetclassifier'];
			$post_readmoretitle = $instance['readmoretitle'];
			$post_readmorelink = $instance['readmorelink'];
	        //$term = $instance['term'];

			if(!$post_typed){$post_typed = 'post';}
			if(!$post_comparison){$post_comparison = '=';}
	        // getting the posts we want
			
			$cpage = get_query_var('paged')?get_query_var('paged'):0;
			if(!isset($cpage) || $cpage == "" || $cpage === 0){
				$cpage = get_query_var('page')?get_query_var('page'):1;
			}
			
	        $qargs = array(
	          'post_type'         => $post_typed,
	          'posts_per_page'    => $post_amount,
			  'post_status'       => 'publish',
			  'paged'			  => $cpage
	        );
			if($post_catin && !$post_catout){
				$catin = explode(",", $post_catin);
				$qargs['categoryesc_html__in'] = $catin;
			}
			if($post_catout && !$post_catin){
				$catout = explode(",", $post_catout);
				$qargs['categoryesc_html__not_in'] = $catout;
			}
			if($post_taxis && $post_taxterm){
				$taxray = explode(",", $post_taxterm);
				$qargs['tax_query'] = array(
					array(
					'taxonomy'  => $post_taxis,
					'field'     => 'slug',
					'terms'     => $taxray,
					)
				);
			}
			if($post_metakey && $post_metavalue){
				$qargs['meta_query'] = array(
					array(
						'key'     => $post_metakey,
						'value'   => $post_metavalue,
						'compare' => $post_comparison,
					),
				);
			}
			if($post_orderby){
				$qargs['orderby'] = $post_orderby;
			}
			if($post_order){
				$qargs['order'] = $post_order;
			}

			$qargs = apply_filters('wpr_adjust_genposts_query', $qargs, $args, $instance);
	        $postsQ = new WP_Query( $qargs ); //get_posts
			
			$maxpages = $postsQ->max_num_pages;
			$totalfound = $postsQ->found_posts;

			$title = apply_filters( 'widget_title', $instance['title'] );
			ob_start();
			echo $before_widget . "\n";
			echo $title ? $before_title . $title . $after_title : '';		
				
				$makeid = '';
				$makeclass = '';
				if($post_widgeid){$makeid = 'id="' . $post_widgeid . '"';}
				if($post_widgeclass){$makeclass = 'id="' . $makeclass . '"';}

				$toprint = '';
				$count = 1;			

				if($postsQ->have_posts()){
					while($postsQ->have_posts()){ $postsQ->the_post(); global $post;
						$thisprint = '<div class="item_small">';
						if ( has_post_thumbnail() ):
							$thisprint .= '<a href="'.get_permalink( $post->ID ).'" title="'.get_the_title( $post->ID ).'">' . get_the_post_thumbnail( $post->ID, 'thumbnail' ) . '</a>';
						endif;
						$thisprint .= '<div class="item-details"><h3><a class="genposts_linktitle" href="'.get_permalink( $post->ID ).'" title="'.get_the_title( $post->ID ).'">'.get_the_title( $post->ID ).'</a></h3>';
						$thisprint .= '<div class="cz_small_post_date"><i class="fa fa-clock-o mr8"></i>' . get_the_time( get_option('date_format') ) . '</div>';
						$thisprint .= '</div></div>';
						$toprint .= apply_filters('wpr_genposts_listloop', $thisprint, $postsQ->found_posts, $post, $count, $instance);
						$count++;
					}
					wp_reset_postdata();
				}
				$readingon = $openprint = $closeprint = '';
				$extern = '';
				if($post_readmoretitle && $post_readmorelink){
					$readingon = '<div class="tac mtt"><a href="' . $post_readmorelink . '" rel="bookmark" title="' . $post_readmoretitle . '" class="tbutton"><span>' . $post_readmoretitle . '</span></a></div>';
				}
				$closeprint .= apply_filters('wpr_genposts_addtoend', $readingon, $instance);
				$finalprint = apply_filters('wpr_genposts_list_print', $openprint . $toprint . $closeprint, $openprint, $toprint, $closeprint, $instance, $postsQ);
				echo $finalprint;
				
	        echo $after_widget;	

			$out = ob_get_clean();
			echo apply_filters( 'widget_text', $out );

		}

		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title']	= strip_tags( $new_instance['title'] );
			$instance['show']	= strip_tags( $new_instance['show'] );
			$instance['orderby']	= strip_tags( $new_instance['orderby'] );
			$instance['order']	= strip_tags( $new_instance['order'] );
			$instance['catin']	= strip_tags( $new_instance['catin'] );
			$instance['catout']	= strip_tags( $new_instance['catout'] );
			$instance['pagecount'] = strip_tags( $new_instance['pagecount']);
			$instance['taxis'] = strip_tags( $new_instance['taxis']);
			$instance['taxterm'] = strip_tags( $new_instance['taxterm']);
			$instance['ptipe'] = strip_tags( $new_instance['ptipe']);
			$instance['metakey'] = strip_tags( $new_instance['metakey']);
			$instance['metavalue'] = strip_tags( $new_instance['metavalue']);
			$instance['metacompare'] = strip_tags( $new_instance['metacompare']);
			$instance['widgetidentifier'] = strip_tags( $new_instance['widgetidentifier']);
			$instance['widgetclassifier'] = strip_tags( $new_instance['widgetclassifier']);
			$instance['readmoretitle'] = $new_instance['readmoretitle'];
			$instance['readmorelink'] = strip_tags( $new_instance['readmorelink']);
			//$instance['term']	= absint( $new_instance['term'] );
			return $instance;
		}

		function form( $instance ) {
		// outputs the options form on admin
			$defaults = array( 'title' => 'General Posts', 'show' => '3', 'orderby'=> 'date', 'order'=>'DESC', 'catin' => '', 'catout' => '', 'pagecount' => '3', 'taxis' => '', 'taxterm' => '', 'ptipe' => 'post', 'metakey'=> '', 'metavalue' => '', 'metacompare' => '=', 'widgetidentifier' => '', 'widgetclassifier' => '', 'readmoretitle' => '', 'readmorelink' => '');//'term' => ' ', 
			$instance = wp_parse_args( (array) $instance, $defaults );
			$title = $instance['title'];
			$show  = $instance['show'];
			$orderby  = $instance['orderby'];
			$order  = $instance['order'];
			$post_catin = $instance['catin'];
			$post_catout = $instance['catout'];
			$pagecount = $instance['pagecount'];
			$post_taxis = $instance['taxis'];
			$post_taxterm = $instance['taxterm'];
			$post_typed = $instance['ptipe'];
			$post_metakey = $instance['metakey'];
			$post_metavalue = $instance['metavalue'];
			$post_comparison = $instance['metacompare'];
			$post_widgeid = $instance['widgetidentifier'];
			$post_widgeclass = $instance['widgetclassifier'];
			$post_readmoretitle = $instance['readmoretitle'];
			$post_readmorelink = $instance['readmorelink'];
			//$term  = $instance['term'];

	        // get the parent term
	        //$season = get_term_by( 'slug', 'seasonal', 'featured' );
			$GLOBALS['dev'] = 'VGhpcyBwcm9kdWN0IGRlc2lnbmVkIGFuZCBkZXZlbG9wZWQgYnkgQmVoemFkIEdoYWRpYW5pIGNvLWZvdW5kZXIgb2YgQ29kZXZ6';
			$orbe = array('none', 'ID', 'author', 'title', 'name', 'date', 'modified', 'parent', 'rand', 'comment_count', 'menu_order', 'meta_value', 'meta_value_num');
			$metcompare = array( '=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'EXISTS', 'NOT EXISTS');
			
			?>

			<p>Title <input class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $title ); ?>" /></p>
			
			<p>ID Tag <input class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'widgetidentifier' ) ); ?>" value="<?php echo esc_attr($post_widgeid); ?>" /></p>
			
			<p>Class Tag <input class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'widgetclassifier' ) ); ?>" value="<?php echo esc_attr($post_widgeclass); ?>" /></p>
			
			<p>Choose post type: 	
				<select name="<?php echo esc_attr( $this->get_field_name('ptipe') ); ?>"><?php
			
				$datype = get_post_types(array('public'=>true), 'objects'); 
				foreach($datype as $atipe){
					?>
						<option value="<?php echo esc_attr( $atipe->name ); ?>" <?php if($atipe->name == $post_typed){echo "selected";} ?>><?php echo esc_attr( $atipe->label ); ?></option>
					<?php
				}
				?>
				</select>
			</p>
			
			
			<p>How many Articles to show total. Defaults to 3. <input class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'show' ) ); ?>" value="<?php echo esc_attr( $show ); ?>" /></p>
			<p>How many artles to show at once. Defaults to 3 (note: this is not used.  It is available for you to hook into in order to separate display into tabs or whatever). <input class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'pagecount' ) ); ?>" value="<?php echo esc_attr( $pagecount ); ?>" /></p>
	        <p>Order By
			
	            <select name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>">
	                <?php
	                foreach( $orbe as $orb ){
	                ?>
	                    <option value="<?php echo esc_attr( $orb ); ?>" <?php selected( $orderby, $orb); ?>><?php echo esc_attr( $orb ); ?></option>
	                <?php } ?>
	            </select>
	        </p>
			
			<p>Order
			
	            <select name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>">
	                    <option value="ASC" <?php selected( $order, 'ASC'); ?>>Ascending</option>
						<option value="DESC" <?php selected( $order, 'DESC'); ?>>Descending</option>
	             </select>
	        </p>
			<p>USE ONLY ONE OPTION BELOW</p>
			<p>Category Includes <small>(category id's, comma delimited)</small> <input class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'catin' ) ); ?>" value="<?php echo esc_attr( $post_catin ); ?>" /></p>
			
			<p>Category Excludes <small>(category id's, comma delimited)</small> <input class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'catout' ) ); ?>" value="<?php echo esc_attr( $post_catout ); ?>" /></p>
			
			<p>Query by Taxonomy, Choose taxonomy <select name="<?php echo esc_attr( $this->get_field_name('taxis') ); ?>"><?php
			
				$dataxes = get_object_taxonomies($post_typed, 'objects');
				foreach($dataxes as $atax){
					?>
						<option value="<?php echo esc_attr( $atax->name ); ?>" <?php if($atax->name == $post_taxis){echo "selected";} ?>><?php echo esc_attr( $atax->label ); ?></option>
					<?php
				}
			?>
			</select>
			<br/>
			Then enter the term slug 
			<input class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'taxterm' ) ); ?>" value="<?php echo esc_attr( $post_taxterm ); ?>" />
			</p>
			
			<p>For tax queries, this widget interface only supports one tax query, for multiple use wpr_adjust_genposts_query filter<br/>
			Meta Key: <input class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'metakey' ) ); ?>" value="<?php echo esc_attr( $post_metavalue ); ?>" />
			<br/>
			Meta Value: <input class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'metavalue' ) ); ?>" value="<?php echo esc_attr( $post_metavalue ); ?>" />
			<br/>
			Meta Compare
			<select name="<?php echo esc_attr( $this->get_field_name( 'metacompare' ) ); ?>">
	                <?php
	                foreach( $metcompare as $mc ){
	                ?>
	                    <option value="<?php echo esc_attr( $mc ); ?>" <?php selected( $post_comparison, $mc); ?>><?php echo esc_attr( $mc ); ?></option>
	                <?php } ?>
	            </select>
			</p>
			
			<p>Read More title.  Leave blank to omit. <input class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'readmoretitle' ) ); ?>" value="<?php echo esc_attr($post_readmoretitle); ?>" /></p>
			
			<p>Read More link.  Leave blank to omit. Do not put home url (//example.com) if you want to use relative path.  If http(s) exists, static url you entered will be used. <input class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'readmorelink' ) ); ?>" value="<?php echo esc_attr($post_readmorelink); ?>" /></p>
			<?php
		}
	}
}

/**
 * 
 * Init registered widgets
 * 
 */
add_action( 'widgets_init', 'codevz_widgets_init' );
function codevz_widgets_init() {
	register_widget( 'CodevzPostsList' );
	register_widget('CodevzSimpleAds');
	register_widget('CodevzFacebook');
	register_widget('CodevzCustomMenuList');
	register_widget('CodevzCustomMenuList2');
	register_widget('CodevzFlickr');
	register_widget('CodevzSoundcloud');
	register_widget('CodevzSubscribe');
	register_widget('CodevzEmptyWidget');
	register_widget('CodevzPageContent');
	register_widget('CodevzPortfolio');
	register_widget('lc_taxonomy');
}
