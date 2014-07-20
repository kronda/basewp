<?php
if ( ! defined( 'PB_IMPORTBUDDY' ) || ( true !== PB_IMPORTBUDDY ) ) {
	die( '<html></html>' );
}

$page_title = 'Step <span class="step_number">' . $step . '</span> of 6: Unzipping Backup File';
require_once( '_header.php' );
echo '<div class="wrap">';

echo pb_backupbuddy::$classes['import']->status_box( 'Extracting backup ZIP file with ImportBuddy v' . pb_backupbuddy::$options['bb_version'] . '... Powered by BackupBuddy.' );
echo '<div id="pb_importbuddy_working"><img src="' . pb_backupbuddy::plugin_url() . '/images/loading_large.gif" title="Working... Please wait as this may take a moment..."></div>';
pb_backupbuddy::flush();


// Try to put a .maintenance file in place during import to help prevent against viewing site before import completes.
generate_maintenance_file();


$results = extract_files();


echo '<script type="text/javascript">jQuery("#pb_importbuddy_working").hide();</script>';

if ( true === $results ) { // Move on to next step.
	echo '<br><br><p style="text-align: center;">Files successfully extracted.</p>';
	echo '<form action="?step=3" method=post>';
	echo '<input type="hidden" name="options" value="' . htmlspecialchars( serialize( pb_backupbuddy::$options ) ) . '" />';
	echo '<input type="hidden" name="pass_hash" id="pass_hash" value="' .  htmlspecialchars( pb_backupbuddy::_POST( 'pass_hash' ) ) . '">';
	echo '</div><!-- /wrap -->';
	echo '<div class="main_box_foot"><input type="submit" name="submit" value="Next Step &rarr;" class="button" /></div>';
	echo '</form>';
} else {
	pb_backupbuddy::alert( 'File extraction process did not complete successfully. Unable to continue to next step. Manually extract the backup ZIP file and choose to "Skip File Extraction" from the advanced options on Step 1.', true, '9005' );
	echo '</div><!-- /wrap -->';
}

rename_htaccess_temp(); // Rename .htaccess to .htaccess.bb_temp until end of migration.

require_once( '_footer.php' ); ?>
