<?php
	global $TS_ADVANCED_TABLESWP;
?>
	<div class="ts-vcsc-settings-page-header">
		<h2><span class="dashicons dashicons-grid-view"></span>Tablenator - Advanced Tables for WordPress - Table Shortcodes</h2>
		<div class="clear"></div>
	</div>
	<form id="ts-advanced-table-generator-form" class="ts-advanced-table-generator-form" name="ts-advanced-table-generator-form" autocomplete="off" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<?php
			echo '<a href="#" class="button button-primary cs-shortcode ts-advanced-table-generator-trigger" data-table-id="1" data-editor-id="ts-advanced-table-generator-input">Generate Shortcode</a>';
			echo '<textarea id="ts-advanced-table-generator-input" class="ts-advanced-table-generator-input" name="ts-advanced-table-generator-input" style="width: 100%; height: 100px;"></textarea>';
			/*$settings = array(
				'editor_height'     => 100,
				'wpautop' 			=> false, 										// use wpautop?
				'media_buttons' 	=> true, 										// show insert/upload button(s)
				'tabindex' 			=> '',
				'editor_css' 		=> '', 											// intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
				'editor_class' 		=> '', 											// add extra class(es) to the editor textarea
				'teeny' 			=> false, 										// output the minimal editor config used in Press This
				'dfw' 				=> false, 										// replace the default fullscreen with DFW (needs specific css)
				'tinymce' 			=> false, 										// load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
				'quicktags' 		=> false, 										// load Quicktags, can be used to pass settings directly to Quicktags using an array()
				'sanitize' 			=> false,
			);
			wp_editor('', 'ts-advanced-table-generator-input', $settings);*/
		?>
	</form>