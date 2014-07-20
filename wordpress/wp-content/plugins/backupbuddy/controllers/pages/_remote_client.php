<?php
	if ( isset( pb_backupbuddy::$options['remote_destinations'][$_GET['destination_id']] ) ) {
		$destination = &pb_backupbuddy::$options['remote_destinations'][$_GET['destination_id']];
	} else {
		echo __('Error #438934894349. Invalid destination ID.', 'it-l10n-backupbuddy' );
		return;
	}
	
	
	require_once( pb_backupbuddy::plugin_path() . '/destinations/bootstrap.php' );
	pb_backupbuddy_destinations::manage( $destination );
	
	
	echo '<br><br><br>';
	echo '<a class="button" href="';
	if ( is_network_admin() ) {
		echo network_admin_url( 'admin.php' );
	} else {
		echo admin_url( 'admin.php' );
	}
	echo '?page=pb_backupbuddy_destinations">&larr; back to destinations</a><br><br>';
?>
