<?php
	global $TS_ADVANCED_TABLESWP;
?>
<div id="ts-advancedtables-changelog" class="tab-content">
	<div class="ts-vcsc-section-main">
		<div class="ts-vcsc-section-title ts-vcsc-section-show"><i class="dashicons-media-text"></i><?php echo __("Changelog", "ts_visual_composer_extend"); ?></div>
		<div class="ts-vcsc-section-content">
			<div class="ts-vcsc-notice-field ts-vcsc-success" style="margin-top: 10px; font-size: 13px; text-align: justify;">
				<?php echo __("The plugin is constantly evolving and adding new features. The listing below is a summary of all changes and additions so far.", "ts_visual_composer_extend"); ?>				
			</div>	
			<?php
				$url_gets		= ini_get('allow_url_fopen');
				$url_site 		= get_site_url();
				$url_file		= TS_TablesWP_GetResourceURL('changelog.txt');
				if (strpos($url_file, $url_site) !== false) {
					$url_final	= $url_file;
				} else {
					$url_final	= $url_site . $url_file;
				}
				if ($url_gets == 1) {
					$changelog 		= file_get_contents($url_final, true);
					echo nl2br(str_replace('<br/>', PHP_EOL, $changelog));
				} else {
					echo 'Your site setup does not allow the usage of "allow_url_fopen" and so the changelog file could not be loaded. You can find the full and official changelog
					<a href="http://helpdesk.krautcoding.com/forums/forum/wordpress-plugins/advanced-tables-for-visual-composer/" target="_blank">here</a>.';
				}
			?>
		</div>
	</div>
</div>