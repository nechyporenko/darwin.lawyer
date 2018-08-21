<?php
	global $TS_ADVANCED_TABLESWP;
	global $wpdb;
?>
	<div class="ts-vcsc-settings-page-header">
		<h2><span class="dashicons dashicons-editor-table"></span><?php echo __("Tablenator - Advanced Tables for WordPress", "ts_visual_composer_extend"); ?> v<?php echo TS_TablesWP_GetPluginVersion(); ?> ... <?php echo __("Backup All Tables", "ts_visual_composer_extend"); ?></h2>
		<div class="clear"></div>
	</div>
	<form id="ts-advanced-table-migrate-form" class="ts-advanced-table-migrate-form" name="ts-advanced-table-migrate-form" autocomplete="off" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<div id="ts-advanced-table-backup-preloader-wrapper">
			<?php
				echo '<div id="ts-advanced-table-backup-preloader-holder">';
					echo TS_TablesWP_CreatePreloaderCSS("ts-advanced-table-backup-preloader-type", "", 11, "false");
				echo '</div>';  
			?>
		</div>
		<div class="ts-advanced-table-backup-links" style="border-bottom: 1px dashed #cccccc; display: block; width: 100%; margin: 5px 0 20px; float: left;">
			<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
				<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to return to the table listings page.", "ts_visual_composer_extend"); ?></span>
				<a href="<?php echo admin_url('admin.php?page=TS_TablesWP_Tables'); ?>" target="_parent" class="ts-advanced-tables-button-main ts-advanced-tables-button-turquoise ts-advanced-tables-button-listing">
					<?php echo __("Back to Listing", "ts_visual_composer_extend"); ?>
				</a>
			</div>
			<?php
				if (current_user_can('manage_options')) {
					echo '<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
						<span class="ts-advanced-table-tooltip-content">' . __("Click here to return to the plugins settings page.", "ts_visual_composer_extend") . '</span>
						<a href="' . admin_url('admin.php?page=TS_TablesWP_Settings') . '" target="_parent" class="ts-advanced-tables-button-main ts-advanced-tables-button-grey ts-advanced-tables-button-settings">'. __("Back to Settings", "ts_visual_composer_extend") . '</a>
					</div>';
				}
			?>
			<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
				<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to return to the tables maintenance page.", "ts_visual_composer_extend"); ?></span>
				<a href="<?php echo admin_url('admin.php?page=TS_TablesWP_Maintain'); ?>" target="_parent" class="ts-advanced-tables-button-main ts-advanced-tables-button-purple ts-advanced-tables-button-wrench">
					<?php echo __("Back To Maintenance", "ts_visual_composer_extend"); ?>
				</a>
			</div>
		</div>
		<div class="clearFixMe"></div>
		<img id="ts-advanced-table-backup-banner" style="display: block; width: 100%; max-width: 800px; height: auto; margin: 0 auto 40px auto;" src="<?php echo TS_TablesWP_GetResourceURL('images/banners/banner_export.jpg'); ?>">
		<div style="display: block; width: 100%; margin: 5px 0 20px; float: left;">
			<div style="font-weight: bold;"><?php echo __("Tables Summary:", "ts_visual_composer_extend"); ?></div>
			<div><span style="display: inline-block; min-width: 150px;"><?php echo __("Number of Tables:", "ts_visual_composer_extend"); ?></span> <?php echo count($TS_ADVANCED_TABLESWP->TS_TablesWP_Custom_Tables); ?></div>
			<div style="font-weight: bold; margin-top: 20px;"><?php echo __("Database Information:", "ts_visual_composer_extend"); ?></div>
			<div><span style="display: inline-block; min-width: 150px;"><?php echo __("Database Prefix:", "ts_visual_composer_extend"); ?></span> <?php echo $wpdb->prefix; ?></div>
			<div><span style="display: inline-block; min-width: 150px;"><?php echo __("Database Table:", "ts_visual_composer_extend"); ?></span> <?php echo TABLESWP_MYSQL; ?></div>
		</div>
		<div id="ts-advanced-table-backup-wrapper" class="ts-advanced-table-backup-wrapper" style="display: block; width: 100%; margin: 5px 0 20px; float: left;">
			<div class="ts-vcsc-notice-field ts-vcsc-critical" style="margin-top: 0px; margin-bottom: 30px; font-size: 13px; text-align: justify;">
				<?php echo __("For a full backup of all existing tables, please use the button below in order to download the backup package which will consist of a SQL database file, containing all existing tables.", "ts_visual_composer_extend"); ?>
			</div>
			<?php
				$secret 	= md5(md5(AUTH_KEY . SECURE_AUTH_KEY) . '-' . 'ts-advanced-tables');
				$link 		= admin_url('admin-ajax.php?action=ts_backup_tables&secret=' . $secret . '');
			?>	
			<div class="ts-advanced-tables-button-wrapper ts-advanced-table-tooltip-holder">
				<span class="ts-advanced-table-tooltip-content"><?php echo __("Click here to save the SQL backup file for all tables so you can (import) restore the tables (on another site).", "ts_visual_composer_extend"); ?></span>
				<a href="<?php echo $link; ?>" target="_parent" class="ts-advanced-tables-button-main ts-advanced-tables-button-orange ts-advanced-tables-button-copy" style="margin-bottom: 10px;">
					<?php echo __("Download Backup File", "ts_visual_composer_extend"); ?>
				</a>
			</div>
		</div>
	</form>