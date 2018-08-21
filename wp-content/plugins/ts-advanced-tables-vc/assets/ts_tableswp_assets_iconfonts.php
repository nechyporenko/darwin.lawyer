<?php
	global $TS_ADVANCED_TABLESWP;
?>
<div id="ts-advancedtables-iconfonts" class="tab-content">
	<div class="ts-vcsc-section-main">
		<div class="ts-vcsc-section-title ts-vcsc-section-show"><i class="dashicons-index-card"></i><?php echo __("Icon Font Settings", "ts_visual_composer_extend"); ?></div>
		<div class="ts-vcsc-section-content">
			<p><?php echo __("Here you will find settings that relate to the icon fonts that can be used to add icons to a table cell using the table editor.", "ts_visual_composer_extend"); ?></p>		
			<div class="ts-vcsc-notice-field ts-vcsc-critical" style="margin-top: 10px; font-size: 13px; text-align: justify;">
				<?php echo __("Please be aware that the more icons the table editor is handling, the longer the icon picker will take to load.", "ts_visual_composer_extend"); ?>				
			</div>
			<div style="margin-top: 20px; width: 100%; color: #005DA0; font-size: 13px;">
				<?php
					if (get_option('ts_vcsc_extend_tinymceCustomArray', '') != '') {						
						echo '<div>' . __("Installed Fonts:", "ts_visual_composer_extend") . ' ' . $TS_ADVANCED_TABLESWP->TS_TablesWP_Total_Icon_Fonts . ' / ' . __("Active Fonts:", "ts_visual_composer_extend") . ' ' . $TS_ADVANCED_TABLESWP->TS_TablesWP_Active_Icon_Fonts . '</div>';
					} else {
						echo '<div>' . __("Installed Fonts:", "ts_visual_composer_extend") . ' ' . ($TS_ADVANCED_TABLESWP->TS_TablesWP_Total_Icon_Fonts - 1) . ' / ' . __("Active Fonts:", "ts_visual_composer_extend") . ' ' . $TS_ADVANCED_TABLESWP->TS_TablesWP_Active_Icon_Fonts . '</div>';
					}
					echo '<div>' . __("Installed Icons:", "ts_visual_composer_extend") . ' ' . $TS_ADVANCED_TABLESWP->TS_TablesWP_Total_Icon_Count . ' / ' . __("Active Icons:", "ts_visual_composer_extend") . ' ' . $TS_ADVANCED_TABLESWP->TS_TablesWP_Active_Icon_Count . '</div>';
				?>
			</div>
			<div id="ts_vcsc_extend_tinymceIconFontError" style="display: none;">
				<span id="ts_vcsc_extend_tinymceIconFontCheck"><?php echo __("You must select at least one allowable Icon Font!", "ts_visual_composer_extend"); ?></span>
			</div>
		</div>
	</div>
	<div class="ts-vcsc-section-main" style="display: <?php echo ((($TS_ADVANCED_TABLESWP->TS_TablesWP_VisualComposer_Active == "true") && ($TS_ADVANCED_TABLESWP->TS_TablesWP_Settings_ComposerIntegrate == true)) ? "block" : "none"); ?>">
		<div class="ts-vcsc-section-title ts-vcsc-section-hide"><i class="dashicons-vault"></i><?php echo __("Internal Fonts - WP Bakery Page Builder (formerly Visual Composer):", "ts_visual_composer_extend"); ?></div>
		<div class="ts-vcsc-section-content slideFade" style="display: none;">
			<div class="ts-vcsc-notice-field ts-vcsc-warning" style="margin-top: 10px; font-size: 13px; text-align: justify;">
				<?php echo __("If you are using an icon font that is part of WP Bakery Page Builder (formerly Visual Composer) itself, you should deactivate the matching icon font set that is part of 'Tablenator - Advanced Tables for WordPress & WP Bakery Page Builder' in order to avoid duplications and double file loads. For example, you should use either the 'Font Awesome' set from WP Bakery Page Builder (formerly Visual Composer) OR the matching set from this plugin, but not both at the same time.", "ts_visual_composer_extend"); ?>
			</div>
			<div class="ts_vcsc_extend_font_selector_container" style="margin-top: 20px;">
			<?php
				foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_VisualComposer_Font_Settings as $Icon_Font => $iconfont) {
					echo '<div class="ts_vcsc_extend_font_selector ' . $iconfont['type'] . '" data-active="' . $iconfont['active'] . '" data-icons="' . $iconfont['count'] . '" data-name="' . $iconfont['setting'] . '" data-type="' . $iconfont['type'] . '">';
						echo '<img id="ts_vcsc_extend_tinymce_composer' . $iconfont['setting'] . '_image" data-toggle="ts_vcsc_extend_tinymce_composer' . $iconfont['setting'] . '" data-load="ts_vcsc_extend_load' . $iconfont['setting'] . '" class="ts_vcsc_check_image' . ($iconfont['active'] == 'true' ? " checked" : "") .'" style="" src=' . TS_TablesWP_GetResourceURL('images/fonts/composer_' . strtolower($iconfont['setting']) . '.jpg') . '>';
						echo '<div class="ts_vcsc_extend_font_summary" style="margin-top: 10px; margin-bottom: 10px;"><a href="' . $iconfont['link'] . '" target="_blank">Created by ' . $iconfont['author'] . '</a></div>';
						echo '<div class="ts-switch-button ts-codestar-field-switcher" data-value="' . $iconfont['active'] . '" data-load="ts-load-toggle-' . $iconfont['setting'] . '" data-image="ts_vcsc_extend_tinymce_composer' . $iconfont['setting'] . '_image">';
							echo '<div id="ts-switch-toggle-' . $iconfont['setting'] . '" data-load="ts-load-toggle-' . $iconfont['setting'] . '" data-image="ts_vcsc_extend_tinymce_composer' . $iconfont['setting'] . '_image" class="ts-codestar-fieldset ts-switch-toggle">';
								echo '<label class="ts-codestar-label">';
									echo '<input style="display: none; " type="checkbox" data-load="ts_vcsc_extend_load' . $iconfont['setting'] . '" data-image="ts_vcsc_extend_tinymce_composer' . $iconfont['setting'] . '_image" data-check="ts_vcsc_extend_tinymceIconFont" name="ts_vcsc_extend_tinymce_composer' . $iconfont['setting'] . '" id="ts_vcsc_extend_tinymce_composer' . $iconfont['setting'] . '" class="validate[funcCall[checkIconFontSelect]] ts-codestar-checkbox toggle-check ts_vcsc_extend_font" data-error="' . __("Allowable Icon Fonts Selection", "ts_visual_composer_extend") . '" data-order="5" value="' . ($iconfont['active'] == 'true' ? '1' : '0') . '" ' . ($iconfont['active'] == 'true' ? ' checked="checked"' : '') . ' />';
									echo '<em data-on="Yes" data-off="No"></em>';
									echo '<span></span>';
								echo '</label>';
							echo '</div>';
						echo '</div>';							
						echo '<label style="font-weight: bold;" class="labelToggleBox" for="ts_vcsc_extend_tinymce_composer' . $iconfont['setting'] . '">' . $Icon_Font . ' (' . $iconfont['count'] . ' ' . __("Icons", "ts_visual_composer_extend") . ')</label>';
					echo '</div>';
				};
			?>					
			</div>
		</div>
	</div>
	<div class="ts-vcsc-section-main">
		<div class="ts-vcsc-section-title ts-vcsc-section-hide"><i class="dashicons-vault"></i><?php echo __("Internal Fonts - Tablenator - Advanced Tables for WordPress & WP Bakery Page Builder:", "ts_visual_composer_extend"); ?></div>
		<div class="ts-vcsc-section-content slideFade" style="display: none;">
			<div class="ts_vcsc_extend_font_selector_container" style="margin-top: 20px;">
				<?php
					foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_Icon_Font_Settings as $Icon_Font => $iconfont) {
						if (($iconfont['setting'] != "Custom") && ($iconfont['setting'] != "Dashicons")) {
							echo '<div class="ts_vcsc_extend_font_selector ' . $iconfont['type'] . '" data-active="' . $iconfont['active'] . '" data-icons="' . $iconfont['count'] . '" data-name="' . $iconfont['setting'] . '" data-type="' . $iconfont['type'] . '">';
								echo '<img id="ts_vcsc_extend_tinymce_internal' . $iconfont['setting'] . '_image" data-toggle="ts_vcsc_extend_tinymce_internal' . $iconfont['setting'] . '" data-load="ts_vcsc_extend_load' . $iconfont['setting'] . '" class="ts_vcsc_check_image' . ($iconfont['active'] == 'true' ? " checked" : "") .'" style="" src=' . TS_TablesWP_GetResourceURL('images/fonts/internal_' . strtolower($iconfont['setting']) . '.jpg') . '>';
								echo '<div class="ts_vcsc_extend_font_summary" style="margin-top: 10px; margin-bottom: 10px;"><a href="' . $iconfont['link'] . '" target="_blank">Created by ' . $iconfont['author'] . '</a></div>';
								echo '<div class="ts-switch-button ts-codestar-field-switcher" data-value="' . $iconfont['active'] . '" data-load="ts-load-toggle-' . $iconfont['setting'] . '" data-image="ts_vcsc_extend_tinymce_internal' . $iconfont['setting'] . '_image">';
									echo '<div id="ts-switch-toggle-' . $iconfont['setting'] . '" data-load="ts-load-toggle-' . $iconfont['setting'] . '" data-image="ts_vcsc_extend_tinymce_internal' . $iconfont['setting'] . '_image" class="ts-codestar-fieldset ts-switch-toggle">';
										echo '<label class="ts-codestar-label">';
											echo '<input style="display: none; " type="checkbox" data-load="ts_vcsc_extend_load' . $iconfont['setting'] . '" data-image="ts_vcsc_extend_tinymce_internal' . $iconfont['setting'] . '_image" data-check="ts_vcsc_extend_tinymceIconFont" name="ts_vcsc_extend_tinymce_internal' . $iconfont['setting'] . '" id="ts_vcsc_extend_tinymce_internal' . $iconfont['setting'] . '" class="validate[funcCall[checkIconFontSelect]] ts-codestar-checkbox toggle-check ts_vcsc_extend_font" data-error="' . __("Allowable Icon Fonts Selection", "ts_visual_composer_extend") . '" data-order="5" value="' . ($iconfont['active'] == 'true' ? '1' : '0') . '" ' . ($iconfont['active'] == 'true' ? ' checked="checked"' : '') . ' />';
											echo '<em data-on="Yes" data-off="No"></em>';
											echo '<span></span>';
										echo '</label>';
									echo '</div>';
								echo '</div>';							
								echo '<label style="font-weight: bold;" class="labelToggleBox" for="ts_vcsc_extend_tinymce_internal' . $iconfont['setting'] . '">' . $Icon_Font . ' (' . $iconfont['count'] . ' ' . __("Icons", "ts_visual_composer_extend") . ')</label>';
							echo '</div>';
						}
					};
				?>
			</div>
		</div>
	</div>
	<div class="ts-vcsc-section-main">
		<div class="ts-vcsc-section-title ts-vcsc-section-hide"><i class="dashicons-wordpress"></i><?php echo __("WordPress Fonts - Dashicons:", "ts_visual_composer_extend"); ?></div>
		<div class="ts-vcsc-section-content slideFade" style="display: none;">
			<div class="ts_vcsc_extend_font_selector_container" style="margin-top: 20px;">
				<?php
					foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_Icon_Font_Settings as $Icon_Font => $iconfont) {
						if (($iconfont['setting'] != "Custom") && ($iconfont['setting'] == "Dashicons")) {
							echo '<div class="ts_vcsc_extend_font_selector ' . $iconfont['type'] . '" data-active="' . $iconfont['active'] . '" data-icons="' . $iconfont['count'] . '" data-name="' . $iconfont['setting'] . '" data-type="' . $iconfont['type'] . '">';
								echo '<img id="ts_vcsc_extend_tinymce_internal' . $iconfont['setting'] . '_image" data-toggle="ts_vcsc_extend_tinymce_internal' . $iconfont['setting'] . '" data-load="ts_vcsc_extend_load' . $iconfont['setting'] . '" class="ts_vcsc_check_image' . ($iconfont['active'] == 'true' ? " checked" : "") .'" style="" src=' . TS_TablesWP_GetResourceURL('images/fonts/internal_' . strtolower($iconfont['setting']) . '.jpg') . '>';
								echo '<div class="ts_vcsc_extend_font_summary" style="margin-top: 10px; margin-bottom: 10px;"><a href="' . $iconfont['link'] . '" target="_blank">Created by ' . $iconfont['author'] . '</a></div>';
								echo '<div class="ts-switch-button ts-codestar-field-switcher" data-value="' . $iconfont['active'] . '" data-load="ts-load-toggle-' . $iconfont['setting'] . '" data-image="ts_vcsc_extend_tinymce_internal' . $iconfont['setting'] . '_image">';
									echo '<div id="ts-switch-toggle-' . $iconfont['setting'] . '" data-load="ts-load-toggle-' . $iconfont['setting'] . '" data-image="ts_vcsc_extend_tinymce_internal' . $iconfont['setting'] . '_image" class="ts-codestar-fieldset ts-switch-toggle">';
										echo '<label class="ts-codestar-label">';
											echo '<input style="display: none; " type="checkbox" data-load="ts_vcsc_extend_load' . $iconfont['setting'] . '" data-image="ts_vcsc_extend_tinymce_internal' . $iconfont['setting'] . '_image" data-check="ts_vcsc_extend_tinymceIconFont" name="ts_vcsc_extend_tinymce_internal' . $iconfont['setting'] . '" id="ts_vcsc_extend_tinymce_internal' . $iconfont['setting'] . '" class="validate[funcCall[checkIconFontSelect]] ts-codestar-checkbox toggle-check ts_vcsc_extend_font" data-error="' . __("Allowable Icon Fonts Selection", "ts_visual_composer_extend") . '" data-order="5" value="' . ($iconfont['active'] == 'true' ? '1' : '0') . '" ' . ($iconfont['active'] == 'true' ? ' checked="checked"' : '') . ' />';
											echo '<em data-on="Yes" data-off="No"></em>';
											echo '<span></span>';
										echo '</label>';
									echo '</div>';
								echo '</div>';							
								echo '<label style="font-weight: bold;" class="labelToggleBox" for="ts_vcsc_extend_tinymce_internal' . $iconfont['setting'] . '">' . $Icon_Font . ' (' . $iconfont['count'] . ' ' . __("Icons", "ts_visual_composer_extend") . ')</label>';
							echo '</div>';
						}
					};
				?>
			</div>
		</div>
	</div>
	<div class="ts-vcsc-section-main" style="<?php echo ((get_option('ts_vcsc_extend_tinymceCustomArray', '') != '') ? "" : "display: none;") ?>">
		<div class="ts-vcsc-section-title ts-vcsc-section-hide"><i class="dashicons-upload"></i><?php echo __("Custom Uploaded Icon Font:", "ts_visual_composer_extend"); ?></div>
		<div class="ts-vcsc-section-content slideFade" style="display: none;">
			<div class="ts_vcsc_extend_font_selector_container" style="margin-top: 20px;">
				<?php
					foreach ($TS_ADVANCED_TABLESWP->TS_TablesWP_Icon_Font_Settings as $Icon_Font => $iconfont) {
						if ($iconfont['setting'] == "Custom") {
							if (get_option('ts_vcsc_extend_tinymce_CustomArray', '') != '') {
								echo '<div class="ts_vcsc_extend_font_selector ' . $iconfont['type'] . '" data-active="' . $iconfont['active'] . '" data-icons="' . $iconfont['count'] . '" data-name="' . $iconfont['setting'] . '" data-type="' . $iconfont['type'] . '">';
									echo '<img id="ts_vcsc_extend_tinymce_internal' . $iconfont['setting'] . '_image" data-toggle="ts_vcsc_extend_tinymce_internal' . $iconfont['setting'] . '" data-load="ts_vcsc_extend_load' . $iconfont['setting'] . '" class="ts_vcsc_check_image' . ($iconfont['active'] == 'true' ? " checked" : "") .'" style="" src=' . TS_TablesWP_GetResourceURL('images/fonts/internal_' . strtolower($iconfont['setting']) . '.jpg') . '>';
									echo '<div class="ts_vcsc_extend_font_summary" style="margin-top: 10px; margin-bottom: 10px;"><a href="' . $iconfont['link'] . '" target="_blank">Created by ' . $iconfont['author'] . '</a></div>';
									echo '<div class="ts-switch-button ts-codestar-field-switcher" data-value="' . $iconfont['active'] . '" data-load="ts-load-toggle-' . $iconfont['setting'] . '" data-image="ts_vcsc_extend_tinymce_internal' . $iconfont['setting'] . '_image">';
										echo '<div id="ts-switch-toggle-' . $iconfont['setting'] . '" data-load="ts-load-toggle-' . $iconfont['setting'] . '" data-image="ts_vcsc_extend_tinymce_internal' . $iconfont['setting'] . '_image" class="ts-codestar-fieldset ts-switch-toggle">';
											echo '<label class="ts-codestar-label">';
												echo '<input style="display: none; " type="checkbox" data-load="ts_vcsc_extend_load' . $iconfont['setting'] . '" data-image="ts_vcsc_extend_tinymce_internal' . $iconfont['setting'] . '_image" data-check="ts_vcsc_extend_tinymceIconFont" name="ts_vcsc_extend_tinymce_internal' . $iconfont['setting'] . '" id="ts_vcsc_extend_tinymce_internal' . $iconfont['setting'] . '" class="validate[funcCall[checkIconFontSelect]] ts-codestar-checkbox toggle-check ts_vcsc_extend_font" data-error="' . __("Allowable Icon Fonts Selection", "ts_visual_composer_extend") . '" data-order="5" value="' . ($iconfont['active'] == 'true' ? '1' : '0') . '" ' . ($iconfont['active'] == 'true' ? ' checked="checked"' : '') . ' />';
												echo '<em data-on="Yes" data-off="No"></em>';
												echo '<span></span>';
											echo '</label>';
										echo '</div>';
									echo '</div>';							
									echo '<label style="font-weight: bold;" class="labelToggleBox" for="ts_vcsc_extend_tinymce_internal' . $iconfont['setting'] . '">' . get_option('ts_vcsc_extend_tinymce_CustomName', 'Custom User Font') . ' (' . $iconfont['count'] . ' ' . __("Icons", "ts_visual_composer_extend") . ')</label>';
								echo '</div>';
							}
						}
					};
				?>
			</div>
		</div>
	</div>
</div>
