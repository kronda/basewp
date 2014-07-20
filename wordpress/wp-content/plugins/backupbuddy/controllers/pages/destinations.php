<?php
wp_enqueue_script( 'thickbox' );
wp_print_scripts( 'thickbox' );
wp_print_styles( 'thickbox' );

pb_backupbuddy::disalert( 'backup_stash_advert', 'Active BackupBuddy customers already have a <b>BackupBuddy Stash</b> account with <span class="pb_label pb_label">1 GB Free Storage</span>. Just login on the <a href="?page=pb_backupbuddy_destinations">Remote Destinations</a> page.' );
?>


<style type="text/css">
#backupbuddy-meta-link-wrap a.show-settings {
	float: right;
	margin: 0 0 0 6px;
}
#screen-meta-links #backupbuddy-meta-link-wrap a {
	background: none;
}
#screen-meta-links #backupbuddy-meta-link-wrap a:after {
	content: '';
	margin-right: 5px;
}
</style>
<script type="text/javascript">
jQuery(document).ready( function() {
	jQuery('#screen-meta-links').append(
		'<div id="backupbuddy-meta-link-wrap" class="hide-if-no-js screen-meta-toggle">' +
			'<a href="" class="show-settings pb_backupbuddy_begintour"><?php _e( "Tour Page", "it-l10n-backupbuddy" ); ?></a>' +
		'</div>'
	);
});
</script>
<?php
// Tutorial
pb_backupbuddy::load_script( 'jquery.joyride-2.0.3.js' );
pb_backupbuddy::load_script( 'modernizr.mq.js' );
pb_backupbuddy::load_style( 'joyride.css' );
?>
<ol id="pb_backupbuddy_tour" style="display: none;">
	<li data-class="nav-tab-0">Remote destinations allow you to send your backups offsite to another location for safe-keeping.</li>
	<li data-class="nav-tab-1" data-button="Finish">View a list of backups recently sent to a remote destination.</li>
</ol>
<script>
jQuery(window).load(function() {
	jQuery(document).on( 'click', '.pb_backupbuddy_begintour', function(e) {
		jQuery("#pb_backupbuddy_tour").joyride({
			tipLocation: 'top',
		});
		return false;
	});
});
</script>



<script type="text/javascript">
	function pb_backupbuddy_selectdestination( destination_id, destination_title, callback_data ) {
		if ( callback_data != '' ) {
			jQuery.post( '<?php echo pb_backupbuddy::ajax_url( 'remote_send' ); ?>', { destination_id: destination_id, destination_title: destination_title, file: callback_data, trigger: 'manual' }, 
				function(data) {
					data = jQuery.trim( data );
					if ( data.charAt(0) != '1' ) {
						alert( '<?php _e("Error starting remote send", 'it-l10n-backupbuddy' ); ?>:' + "\n\n" + data );
					} else {
						alert( "<?php _e('Your file has been scheduled to be sent now. It should arrive shortly.', 'it-l10n-backupbuddy' ); ?> <?php _e( 'You will be notified by email if any problems are encountered.', 'it-l10n-backupbuddy' ); ?>" + "\n\n" + data.slice(1) );
					}
				}
			);
			
			/* Try to ping server to nudge cron along since sometimes it doesnt trigger as expected. */
			jQuery.post( '<?php echo admin_url('admin-ajax.php'); ?>',
				function(data) {
				}
			);

		} else {
			//window.location.href = '<?php echo pb_backupbuddy::page_url(); ?>&custom=remoteclient&destination_id=' + destination_id;
			window.location.href = '<?php
			if ( is_network_admin() ) {
				echo network_admin_url( 'admin.php' );
			} else {
				echo admin_url( 'admin.php' );
			}
			?>?page=pb_backupbuddy_backup&custom=remoteclient&destination_id=' + destination_id;
		}
	}
</script>


<?php
pb_backupbuddy::$ui->title( __( 'Remote Destinations', 'it-l10n-backupbuddy' ) );
echo '<div style="width: 100%;">';
_e( 'BackupBuddy supports many remote destinations which you may transfer backups to.  You may manually send backups to these locations or automatically have them sent for scheduled backups. You may view the files in a remote destination by selecting a destination below once created. In addition to viewing files, you may copy remote backups to your server, and delete files.  All subscribed BackupBuddy customers are provided <b>free</b> storage to our own BackupBuddy Stash cloud destination.', 'it-l10n-backupbuddy' );
echo '</div>';

echo '<br><br>';
pb_backupbuddy::$ui->start_tabs(
	'destinations',
	array(
		array(
			'title'		=>		'Remote Destinations',
			'slug'		=>		'destinations',
		),
		array(
			'title'		=>		'Recently Transferred Files',
			'slug'		=>		'transfers',
		),
	),
	'width: 100%;'
);


pb_backupbuddy::$ui->start_tab( 'destinations' );
echo '<iframe id="pb_backupbuddy_iframe" src="' . pb_backupbuddy::ajax_url( 'destination_picker' ) . '&action_verb=to%20manage%20files" width="100%" style="max-width: 850px;" height="1800" frameBorder="0">Error #4584594579. Browser not compatible with iframes.</iframe>';
pb_backupbuddy::$ui->end_tab();


pb_backupbuddy::$ui->start_tab( 'transfers' );
	echo '<div style="margin-left: 0px;">';
		require_once( 'server_info/remote_sends.php' );
	echo '</div>';
pb_backupbuddy::$ui->end_tab();
?>

<br style="clear: both;"><br style="clear: both;">

<?php
// Handles thickbox auto-resizing. Keep at bottom of page to avoid issues.
if ( !wp_script_is( 'media-upload' ) ) {
	wp_enqueue_script( 'media-upload' );
	wp_print_scripts( 'media-upload' );
}
?>
</div>