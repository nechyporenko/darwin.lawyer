<?php 
	$footer = ( is_singular() || is_404() ) ? !Codevz::meta( Codevz::option( '404' ), 0, 'hide_footer' ) : 1;

	if ( $footer ) {
		echo '<footer class="page_footer' . esc_attr( Codevz::option( 'fixed_footer' ) ? ' cz_fixed_footer' : '' ) . '">';

		// Row before footer
		Codevz::row(array(
			'id'		=> 'footer_',
			'nums'		=> array('1'),
			'row'		=> 1,
			'left'		=> '_left',
			'right'		=> '_right',
			'center'	=> '_center'
		));

		// Footer widgets
		$footer_layout = Codevz::option( 'footer_layout' );
		if ( $footer_layout ) {
			$layout = explode( ',', $footer_layout );
			$count = count( $layout );
			$is_widget = 0;
			foreach ( $layout as $num => $col ) {
				$num++;
				if ( is_active_sidebar( 'footer-' . $num ) ) {
					$is_widget = 1;
				}
			}
			foreach ( $layout as $num => $col ) {
				$num++;
				if ( $is_widget && $num === 1 ) {
					echo '<div class="cz_middle_footer"><div class="row clr">';
				}
				if ( is_active_sidebar( 'footer-' . $num ) ) {
					echo '<div class="col ' . esc_attr( $col ) . ' sidebar_footer-' . esc_attr( $num ) . ' clr">';
					dynamic_sidebar( 'footer-' . $num );  
					echo '</div>';
				} else {
					echo '<div class="col ' . esc_attr( $col ) . ' sidebar_footer-' . esc_attr( $num ) . ' clr">&nbsp;</div>';
				}
				if ( $is_widget && $num === $count ) {
					echo '</div></div>';
				}
			}
		}

		// Row after footer
		Codevz::row(array(
			'id'		=> 'footer_',
			'nums'		=> array('2'),
			'row'		=> 1,
			'left'		=> '_left',
			'right'		=> '_right',
			'center'	=> '_center'
		));

		echo '</footer>';
	}

	echo '</div></div>'; // layout

	// Back to top & contact
	echo Codevz::option( 'backtotop' ) ? '<i class="' . esc_attr( Codevz::option( 'backtotop' ) ) . ' backtotop"></i>' : '';
	$cf7 = Codevz::option( 'cf7_beside_backtotop' );
	if ( $cf7 ) {
		echo '<i class="' . esc_attr( Codevz::option( 'cf7_beside_backtotop_icon', 'fa fa-envelope-o' ) ) . ' fixed_contact"></i>';
		echo '<div class="fixed_contact">' . Codevz::get_page_as_element( esc_html( $cf7 ) ) . '</div>';
	}

	// Popup
	echo Codevz::get_page_as_element( esc_html( Codevz::option( 'popup' ) ) );

	// Ajax music player
	if ( Codevz::option( 'ajax' ) && Codevz::option( 'ajax_mp' ) ) {
		$ajax_tracks = urlencode( json_encode( Codevz::option( 'ajax_mp_tracks', array() ) ) );
		echo do_shortcode( '[cz_music_player id="cz_ajax_mp" fixed="true" dark_text="' . Codevz::option( 'ajax_mp_dark_text' ) . '" flat="' . Codevz::option( 'ajax_mp_flat' ) . '" autoplay="' . Codevz::option( 'ajax_mp_autoplay' ) . '" tracks="' . $ajax_tracks . '"]' );
	}
?>
<div class="cz_fixed_top_border"></div>
<div class="cz_fixed_bottom_border"></div>

<?php wp_footer(); ?>
</body>
</html>