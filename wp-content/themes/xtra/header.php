<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' );?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0"/>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="intro" <?php echo Codevz::intro_attrs(); ?>></div>
<?php 
	// Header settings
	$cpt = Codevz::get_post_type();
	$option_cpt = ( $cpt === 'post' || $cpt === 'page' || empty( $cpt ) ) ? '' : '_' . $cpt;
	$fixed_side = Codevz::option( 'fixed_side' ) ? ' is_fixed_side' : '';
	$cover = Codevz::option( 'page_cover' . $option_cpt );
	$option_cpt = ( ! $cover || $cover === '1' ) ? '' :  $option_cpt;
	$layout = Codevz::option( 'boxed', '' );
	// Reload cover
	$cover = Codevz::option( 'page_cover' . $option_cpt );
	$cover_rev = Codevz::option( 'page_cover_rev' . $option_cpt );
	$cover_custom = Codevz::option( 'page_cover_custom' . $option_cpt );
	$cover_custom_page =  Codevz::option( 'page_cover_page' . $option_cpt );
	$cover_than_header = Codevz::option( 'cover_than_header' . $option_cpt );
	$cover_parallax = Codevz::option( 'title_parallax' . $option_cpt );
	$title = Codevz::option( 'page_title' . $option_cpt );
	$page_title_center = Codevz::option( 'page_title_center' . $option_cpt ) ? ' page_title_center' : '';
	if ( $title === '2' || $title === '6' || $title === '9' ) {
		$page_title_center = '';
	}
	$is_ajax = ( Codevz::option( 'ajax' ) || isset( $_GET['ajax'] ) ) ? ' codevz_ajax' : '';
	
	$_404 = Codevz::option( '404' );
	$is_404 = is_404();
	$header = $footer = 1;

	// Single page settings
	if ( is_singular() || ( $is_404 && $_404 ) ) {
		$_id = ( $is_404 && $_404 ) ? $_404 : 0;
		$meta = Codevz::meta( $_id );
		if ( isset( $meta['cover_than_header'] ) ) {
			if ( $meta['page_cover'] === 'none' ) {
				$cover = 'none';
			} else if ( $meta['page_cover'] !== '1' ) {
				$cover = $meta['page_cover'];
				$cover_rev = $meta['page_cover_rev'];
				$cover_custom = $meta['page_cover_custom'];
				$cover_custom_page =  $meta['page_cover_page'];
			}

			// Layout
			if ( isset( $meta['boxed'] ) && $meta['boxed'] !== 'd' ) {
				$layout = $meta['boxed'];
			}
			
			// Others
			$header = !$meta['hide_header'];
			$footer = !$meta['hide_footer'];
		}
		if ( ! empty( $meta['cover_than_header'] ) ) {
			$cover_than_header = ( $meta['cover_than_header'] === 'd' ) ? $cover_than_header : $meta['cover_than_header'];
		}
	}

	// Preloader
	if ( Codevz::option( 'pageloader' ) ) {
		echo '<div class="pageloader ' . esc_attr( Codevz::option( 'pageloader_fx' ) ) . '" data-out="' . esc_attr( Codevz::option( 'out_loading' ) ) . '" data-time="' . esc_attr( Codevz::option( 'pageloader_time' ) ) . '"><img src="' . esc_attr( Codevz::option( 'pageloader_img', 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzgiIGhlaWdodD0iMzgiIHZpZXdCb3g9IjAgMCAzOCAzOCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBzdHJva2U9IiNhN2E3YTciPg0KICAgIDxnIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+DQogICAgICAgIDxnIHRyYW5zZm9ybT0idHJhbnNsYXRlKDEgMSkiIHN0cm9rZS13aWR0aD0iMiI+DQogICAgICAgICAgICA8Y2lyY2xlIHN0cm9rZS1vcGFjaXR5PSIuMyIgY3g9IjE4IiBjeT0iMTgiIHI9IjE4Ii8+DQogICAgICAgICAgICA8cGF0aCBkPSJNMzYgMThjMC05Ljk0LTguMDYtMTgtMTgtMTgiPg0KICAgICAgICAgICAgICAgIDxhbmltYXRlVHJhbnNmb3JtDQogICAgICAgICAgICAgICAgICAgIGF0dHJpYnV0ZU5hbWU9InRyYW5zZm9ybSINCiAgICAgICAgICAgICAgICAgICAgdHlwZT0icm90YXRlIg0KICAgICAgICAgICAgICAgICAgICBmcm9tPSIwIDE4IDE4Ig0KICAgICAgICAgICAgICAgICAgICB0bz0iMzYwIDE4IDE4Ig0KICAgICAgICAgICAgICAgICAgICBkdXI9IjFzIg0KICAgICAgICAgICAgICAgICAgICByZXBlYXRDb3VudD0iaW5kZWZpbml0ZSIvPg0KICAgICAgICAgICAgPC9wYXRoPg0KICAgICAgICA8L2c+DQogICAgPC9nPg0KPC9zdmc+' ) ) . '" alt="loading" width="150" height="150" /></div>';
	}

	// Hidden top bar
	$hidden_top_bar = Codevz::option( 'hidden_top_bar' );
	if ( $hidden_top_bar ) {
		echo '<div class="hidden_top_bar"><div class="row clr">' . Codevz::get_page_as_element( esc_html( $hidden_top_bar ) ) . '</div><i class="fa fa-angle-down"></i></div>';
	}

	// Start page layout
	echo '<div id="layout" class="clr layout_' . esc_attr( $layout . ( $fixed_side ? ' is_fixed_side' : '' ) ) . $is_ajax . '">';

	// Fixed Side
	$fixed_side = Codevz::option( 'fixed_side' );
	$il_width = '';
	if ( $fixed_side ) {
		echo '<aside class="fixed_side fixed_side_' . esc_attr( $fixed_side ) . '">';
		Codevz::row(array(
			'id'		=> 'fixed_side_',
			'nums'		=> array('1'),
			'row'		=> 0,
			'left'		=> '_top',
			'right'		=> '_middle',
			'center'	=> '_bottom'
		));
		echo '</aside>';
		$il_width = Codevz::get_string_between( Codevz::option( '_css_fixed_side_style' ), 'width:', ';' );
		$il_width = $il_width ? ' style="width: calc(100% - ' . $il_width . ')"' : '';
	}

	// Inner layout
	echo '<div class="inner_layout"' . $il_width . '><div class="cz_overlay"></div>';

	// Fixed ads beside layout
	$fixed_ads = Codevz::option('fixed_ads');
	if ( $fixed_ads ) {
		foreach ( $fixed_ads as $ad_num => $ad ) {
			echo '<a href="' . esc_url( $ad['link'] ) . '" class="cz_fixed_ad cz_fixed_ad_' . esc_attr( ++$ad_num ) . ' hide" title="' . esc_attr( $ad['title'] ) . '" target="_blank"><img src="' . esc_url( $ad['image'] ) . '" alt="' . esc_attr( $ad['title'] ) . '" width="200" height="200" /></a>';
		}
	}

	// Cover & Title
	$cover_type = $cover;
	if ( $cover && $cover !== 'none' ) {
		ob_start();
		echo '<div class="page_cover' . esc_attr( $page_title_center . ' ' . $cover_than_header ) . '">';
		if ( $cover === 'title' ) {
			echo '<div class="page_title" data-title-parallax="' . esc_attr( $cover_parallax ) . '">';

				$title_content = $breadcrumbs_content = '';
				$breadcrumbs_right = ( $title === '6' || $title === '9' );
				if ( $breadcrumbs_right ) {
					echo '<div class="right_br_full_container clr"><div class="row clr">';
				}

				if ( $title !== '2' && $title !== '7' && $title !== '8' && $title !== '9' ) {
					ob_start();
					Codevz::page_title( 'h1' );
					$title_content = ob_get_clean();
				}

				if ( $title !== '2' && $title !== '3' ) {
					ob_start();
					Codevz::breadcrumbs();
					$breadcrumbs_content = $breadcrumbs_right ? '<div class="righter">' . ob_get_clean() . '</div>' : '<div class="breadcrumbs_container clr"><div class="row clr">' . ob_get_clean() . '</div></div>';
				}

				if ( $title === '5' ) {
					echo wp_kses_post( $breadcrumbs_content . '<div class="row clr">' . $title_content . '</div>' );
				} else {
					if ( $title_content ) {
						echo '<div class="' . esc_attr( $breadcrumbs_right ? 'lefter' : 'row clr' ) . '">' . wp_kses_post( $title_content ) . '</div>';
					}
					echo wp_kses_post( $breadcrumbs_content );
				}

				if ( $breadcrumbs_right ) {
					echo '</div></div>';
				}
				
			echo '</div>';
		} else if ( $cover === 'rev' && $cover_rev ) {
			echo do_shortcode( '[rev_slider alias="' . esc_attr( $cover_rev ) . '"]' );
		} else if ( $cover === 'custom' ) {
			echo '<div class="page_cover_custom">' . do_shortcode( esc_html( $cover_custom ) ) . '</div>';
		} else if ( $cover === 'page' ) {
			echo Codevz::get_page_as_element( esc_html( $cover_custom_page ) );
		}
		echo '</div>'; // page_cover
		$cover = ob_get_clean();
	} else {
		$cover = '<div class="page_cover"></div>';
	}

	if ( $cover_than_header === 'header_after_cover' ) {
		echo do_shortcode( $cover );
	}

	// Start Header
	if ( $header ) {
		$sticky = Codevz::option( 'sticky_header' );
		$sticky = $sticky ? ' cz_sticky_h' . $sticky : '';
		echo '<header class="page_header clr' . $sticky . '">';
		Codevz::row(array(
			'id'		=> 'header_',
			'nums'		=> array('1', '2', '3', '4', '5'),
			'row'		=> 1,
			'left'		=> '_left',
			'right'		=> '_right',
			'center'	=> '_center'
		));
		echo '</header>';
	}

	if ( $cover_than_header != 'header_after_cover' ) {
		echo do_shortcode( $cover );
	}

	// Ajax loader
	if ( $is_ajax ) {
		$img = Codevz::option( 'ajax_loader' ) ? '<img src="' . Codevz::option( 'ajax_loader' ) . '" />' : '';
		echo '<div class="cz_ajax_loader' . ( $img ? ' cz_ajax_img' : '' ) . '">' . $img . '</div>';
	}
?>