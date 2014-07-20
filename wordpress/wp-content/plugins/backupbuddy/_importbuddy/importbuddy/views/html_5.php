<?php
if ( ! defined( 'PB_IMPORTBUDDY' ) || ( true !== PB_IMPORTBUDDY ) ) {
	die( '<html></html>' );
}

$page_title = 'Step <span class="step_number">' . $step . '</span> of 6: Database Migration (Updating URLs, paths, etc)';
require_once( '_header.php' );
?>

<script type="text/javascript" src="importbuddy/js/jquery.simple-expand.min.js"></script>
<script type="text/javascript" src="importbuddy/js/jquery.leanModal.min.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function() {
		
		jQuery('.expander').simpleexpand();
		
		jQuery('.leanModal').leanModal(
			{ top : 20, overlay : 0.4, closeButton: ".modal_close" }
		);
	});
</script>


<?php
echo '<div class="wrap">';


rename_htaccess_temp_back(); // Rename .htaccess.bb_temp back to .htaccess.


echo pb_backupbuddy::$classes['import']->status_box( 'Migrating database content with ImportBuddy v' . pb_backupbuddy::$options['bb_version'] . '... Powered by BackupBuddy.' );
echo '<div id="pb_importbuddy_working"><img src="' . pb_backupbuddy::plugin_url() . '/images/loading_large.gif" title="Working... Please wait as this may take a moment..."></div>';
pb_backupbuddy::flush();


$result = migrate_database();


if ( ( true === pb_backupbuddy::$options['skip_database_import'] ) && ( true === pb_backupbuddy::$options['skip_database_migration'] ) ) {
	pb_backupbuddy::status( 'details', 'Skipping database verification as both Import and Migration steps were skipped so no modifications were made to it.' );
} else {
	verify_database();
}


// Remove any temporary .maintenance file created by ImportBuddy.
scrub_maintenance_file();


// Remove any temporary index.htm file created by ImportBuddy.
scrub_index_file();


echo '<script type="text/javascript">jQuery("#pb_importbuddy_working").hide();</script>';

if ( true === $result ) {
	$wpconfig_result = migrate_wp_config();
	if ( $wpconfig_result !== true ) {
		pb_backupbuddy::alert( 'Error: Unable to update wp-config.php file. Verify write permissions for the wp-config.php file then refresh this page. You may manually update your wp-config.php file by changing it to the following:<textarea readonly="readonly" style="width: 80%;">' . $wpconfig_result . '</textarea>' );
	}
	pb_backupbuddy::status( 'message', 'Import complete!' );
	
	echo '<form action="?step=6" method=post>';
	echo '<input type="hidden" name="pass_hash" id="pass_hash" value="' . htmlspecialchars( pb_backupbuddy::_POST( 'pass_hash' ) ) . '">';
	echo '<input type="hidden" name="options" value="' . htmlspecialchars( serialize( pb_backupbuddy::$options ) ) . '" />';
	
	// Scan for 'trouble' such as a remaining .maintenance file, index.htm, index.html, missing wp-config.php, missing .htaccess, etc etc.
	$trouble = trouble_scan();
	if ( count( $trouble ) > 0 ) {
		echo '<br>';
		$trouble_text = '';
		foreach( $trouble as $this_trouble ) {
			$trouble_text .= '<li>' . $this_trouble . '</li>';
		}
		$trouble_text = '<ul>' . $trouble_text . '</ul>';
		pb_backupbuddy::alert( '<b>Warning:</b> One or more potential issues detected that <i>may</i> require your attention.' . $trouble_text );
	}
	?>
	
	
	<h3>Verify imported site functionality before proceeding:<br><br><a href="<?php echo pb_backupbuddy::$options['home']; ?>" target="_new"><?php echo pb_backupbuddy::$options['home']; ?></a></h3><br>
	
	
	<h3>Problems? Click to view possible solutions:</h3>
	<div class="expander-box">
		<a class="expander" href="#">Clicking on a posts results in a 404 Not Found</a>
		<div class="content">
			This is typically caused by a problem with your .htaccess file.  Log into your wp-admin, navigate to Settings: Permalinks in the WordPress menu and click the "Save" button to update permalink settings to your .htaccess file. This typically resolves this problem.
		</div>
	</div>
	<div class="expander-box">
		<a class="expander" href="#">Logging in redirects back to the old site</a>
		<div class="content">
			This is usually caused by entering the source site URL as the destination URL on Step 3.  Re-restoring using the correct URL should fix this problem.
		</div>
	</div>
	<div class="expander-box">
		<a class="expander" href="#">Source site has changed to the destination URL</a>
		<div class="content">
			This is caused if you restored over your source site database by entering the source site database settings on Step 3. You may re-restore using correct settings.  You may correct the modified URL on the source site by using the Server Tools page's Mass Text Replace tool within the Database tab.
		</div>
	</div>
	
	
	<br>
	<h3>Final cleanup step next:</h3>
	<table><tr><td>
		<label for="delete_backup" style="width: auto; font-size: 12px;"><input type="checkbox" name="delete_backup" id="delete_backup" value="1" checked> Delete backup zip archive</label>
		<br>
		<label for="delete_temp" style="width: auto; font-size: 12px;"><input type="checkbox" name="delete_temp" id="delete_temp" value="1" checked> Delete temporary import files</label>
	</td><td>
		<label for="delete_importbuddy" style="width: auto; font-size: 12px;"><input type="checkbox" name="delete_importbuddy" id="delete_importbuddy" value="1" checked> Delete ImportBuddy tool files</label>
		<br>
		<label for="delete_importbuddylog" style="width: auto; font-size: 12px;"><input type="checkbox" name="delete_importbuddylog" id="delete_importbuddylog" value="1" checked> Delete importbuddy.txt log file</label>
	</td></tr></table>
	
	<?php
	echo '</div><!-- /wrap -->';
	echo '<div class="main_box_foot">';
	echo '<a href="#pb_log_modal" class="button button-tertiary leanModal" style="float: left; font-size: 13px;">View Import Log</a>';
	echo '<input type="submit" name="submit" class="button" value="Clean up & remove temporary files &rarr;" />';
	echo '</div>';
	echo '</form>';
} else {
	pb_backupbuddy::alert( 'Error: Unable to migrate database content. Something went wrong with the database migration portion of the restore process.', true );
	echo '</div><!-- /wrap -->';
}
?>




<div id="pb_log_modal" style="display: none;">
	<div class="modal">
		<div class="modal_header">
			<a class="modal_close">&times;</a>
			<h2>Import Log</h2>
			Much of the Import process is logged for debugging or troubleshooting purposes.
			This log may be helpful when seeking technical support or assistance.
		</div>
		<div class="modal_content">
			
			<textarea style="width: 95%; height: 300px;" wrap="off"><?php echo file_get_contents( ABSPATH . 'importbuddy/status-' . pb_backupbuddy::$options['log_serial'] . '.txt' ); ?></textarea>
			
		</div>
	</div>
</div>




<?php
require_once( '_footer.php' );
?>
