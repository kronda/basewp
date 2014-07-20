<?php
if ( ! defined( 'PB_IMPORTBUDDY' ) || ( true !== PB_IMPORTBUDDY ) ) {
	die( '<html></html>' );
}

$page_title = 'Step <span class="step_number">' . $step . '</span> of 6: Final Cleanup';
require_once( '_header.php' );
echo '<div class="wrap">';

echo pb_backupbuddy::$classes['import']->status_box( 'Cleaning up after restore with ImportBuddy v' . pb_backupbuddy::$options['bb_version'] . '... Powered by BackupBuddy.' );
echo '<div id="pb_importbuddy_working"><img src="' . pb_backupbuddy::plugin_url() . '/images/loading_large.gif" title="Working... Please wait as this may take a moment..."></div>';


// Attempt to flush the page and pause so assets (CSS, images) can load before actual files get deleted by cleanup().
pb_backupbuddy::flush();
sleep( 5 ); // Pause server-side briefly to give time for their browser to load assets.
pb_backupbuddy::flush();


// Cleanup!
cleanup();


echo '<script type="text/javascript">jQuery("#pb_importbuddy_working").hide();</script>';

echo 'This step handles deleting files. In rare instances on some servers you may receive errors loading files or an unstyled page here due to files getting deleting prematurely before the page completes loading. This may safely be ignored. It is common to not be able to delete some files due to permission errors. You may manually delete them. <b>importbuddy.php</b> should always be deleted after restore for best security.<br><br>';

echo '<h3 style="text-align: center;">Your site is ready to go at<br><br>';
echo '<a href="' . pb_backupbuddy::$options['home'] . '" target="_new"><b>' . pb_backupbuddy::$options['home'] . '</b></a><br><br>';
echo 'Thank you for choosing BackupBuddy!</h3>';


echo '</div></div><br><br><br>';
?>
