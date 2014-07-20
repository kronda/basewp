<?php
// LOADED FROM: core.php, periodic_cleanup().
// INCOMING VARIABLES: $backup_age_limit = 172800, $die_on_fail = true


$max_importbuddy_age = 60*60*1; // 1hr - Max age, in seconds, importbuddy files can be there before cleaning up (delay useful if just imported and testing out site).
$max_status_log_age = 48; // Max age in hours.
$max_site_log_size = pb_backupbuddy::$options['max_site_log_size'] * 1024 * 1024; // in bytes.

pb_backupbuddy::status( 'message', 'Starting cleanup procedure for BackupBuddy v' . pb_backupbuddy::settings( 'version' ) . '.' );

if ( !isset( pb_backupbuddy::$options ) ) {
	pb_backupbuddy::load();
}


// Clean up any old rollback undo files hanging around.
$files = (array)glob( ABSPATH . 'backupbuddy_rollback*' );
foreach( $files as $file ) {
	$file_stats = stat( $file );
	if ( ( time() - $file_stats['mtime'] ) > $max_status_log_age ) {
		@unlink( $file );
	}
}


// Alert user if no new backups FINISHED within X number of days if enabled. Max 1 email notification per 24 hours period.
if ( pb_backupbuddy::$options['no_new_backups_error_days'] > 0 ) {
	if ( pb_backupbuddy::$options['last_backup_finish'] > 0 ) {
		$time_since_last = time() - pb_backupbuddy::$options['last_backup_finish'];
		$days_since_last = $time_since_last / 60 / 60 / 24;
		if ( $days_since_last > pb_backupbuddy::$options['no_new_backups_error_days'] ) {
			
			$last_sent = get_transient( 'pb_backupbuddy_no_new_backup_error' );
			if ( false === $last_sent ) {
				$last_sent = time();
				set_transient( 'pb_backupbuddy_no_new_backup_error', $last_sent, (60*60*24) );
			}
			if ( ( time() - $last_sent ) > (60*60*24) ) { // 24hrs+ elapsed since last email sent.
				$message = 'Alert! BackupBuddy is configured to notify you if no new backups have completed in `' . pb_backupbuddy::$options['no_new_backups_error_days'] . '` days. It has been `' . $days_since_last . '` days since your last completed backups.';
				pb_backupbuddy::status( 'warning', $message );
				backupbuddy_core::mail_error( $message );
			}
			
		}
	}
}


// TODO: Check for orphaned .gz files in root from PCLZip.
// Cleanup backup itegrity portion of array (status logging info inside function).


/***** BEGIN CLEANUP LOGS *****/

// Purge old logs.
pb_backupbuddy::status( 'details', 'Cleaning up old logs.' );
$log_directory = backupbuddy_core::getLogDirectory();
// Purge individual backup status logs unmodified in certain number of hours.
$files = glob( $log_directory . 'status-*.txt' );
if ( is_array( $files ) && !empty( $files ) ) { // For robustness. Without open_basedir the glob() function returns an empty array for no match. With open_basedir in effect the glob() function returns a boolean false for no match.
	foreach( $files as $file ) {
		$file_stats = stat( $file );
		if ( ( time() - $file_stats['mtime'] ) > $max_status_log_age ) {
			@unlink( $file );
		}
	}
}

// Purge site-wide log if over certain size.
$files = glob( $log_directory . 'log-*.txt' );
if ( is_array( $files ) && !empty( $files ) ) { // For robustness. Without open_basedir the glob() function returns an empty array for no match. With open_basedir in effect the glob() function returns a boolean false for no match.
	foreach( $files as $file ) {
		$file_stats = stat( $file );
		if ( $file_stats['size'] > ( $max_site_log_size ) ) {
			backupbuddy_core::mail_error( 'NOTICE ONLY (not an error): A BackupBuddy log file has exceeded the size threshold of ' . $max_site_log_size . ' KB and has been deleted to maintain performance. This is only a notice. Deleted log file: ' . $file . '.' );
			@unlink( $file );
		}
	}
}

/***** END CLEANUP LOGS *****/



// Cleanup any temporary local destinations.
pb_backupbuddy::status( 'details', 'Cleaning up any temporary local destinations.' );
foreach( pb_backupbuddy::$options['remote_destinations'] as $destination_id => $destination ) {
	if ( ( $destination['type'] == 'local' ) && ( isset( $destination['temporary'] ) && ( $destination['temporary'] === true ) ) ) { // If local and temporary.
		if ( ( time() - $destination['created'] ) > $backup_age_limit ) { // Older than 12 hours; clear out!
			pb_backupbuddy::status( 'details', 'Cleaned up stale local destination `' . $destination_id . '`.' );
			unset( pb_backupbuddy::$options['remote_destinations'][$destination_id] );
			pb_backupbuddy::save();
		}
	}
}


// Cleanup excess remote sending stats.
pb_backupbuddy::status( 'details', 'Cleaning up remote send stats.' );
backupbuddy_core::trim_remote_send_stats( $backup_age_limit );


// Verify directory existance and anti-directory browsing is in place everywhere.
backupbuddy_core::verify_directories();


require_once( pb_backupbuddy::plugin_path() . '/classes/fileoptions.php' );

// Purge fileoptions files without matching backup file in existance that are older than 30 days.
pb_backupbuddy::status( 'details', 'Cleaning up old backup fileoptions option files.' );
$fileoptions_directory = backupbuddy_core::getLogDirectory() . 'fileoptions/';
$files = glob( $fileoptions_directory . '*.txt' );
if ( ! is_array( $files ) ) {
	$files = array();
}
foreach( $files as $file ) {
	$backup_options = new pb_backupbuddy_fileoptions( $file, $read_only = true );
	if ( true !== ( $result = $backup_options->is_ok() ) ) {
		pb_backupbuddy::status( 'error', 'Error retrieving fileoptions file `' . $file . '`. Err 335353266.' );
	} else {
		if ( isset( $backup_options->options['archive_file'] ) ) {
			if ( ! file_exists( $backup_options->options['archive_file'] ) ) { // No corresponding backup ZIP file.
				$modified = filemtime( $file );
				if ( ( time() - $modified ) > backupbuddy_core::MAX_SECONDS_TO_KEEP_ORPHANED_FILEOPTIONS_FILES ) { // Too many days old so delete.
					if ( false === unlink( $file ) ) {
						pb_backupbuddy::status( 'error', 'Unable to delete orphaned fileoptions file `' . $file . '`.' );
					}
					if ( file_exists( $file . '.lock' ) ) {
						@unlink( $file . '.lock' );
					}
				} else {
					// Do not delete orphaned fileoptions because it is not old enough. Recent backups page needs these to list the backup.
				}
			} else { // Backup ZIP file exists.
				
			}
		}
	}
} // end foreach.


// Handle high security mode archives directory .htaccess system. If high security backup directory mode: Make sure backup archives are NOT downloadable by default publicly. This is only lifted for ~8 seconds during a backup download for security. Overwrites any existing .htaccess in this location.
if ( pb_backupbuddy::$options['lock_archives_directory'] == '0' ) { // Normal security mode. Put normal .htaccess.
	pb_backupbuddy::status( 'details', 'Removing .htaccess high security mode for backups directory. Normal mode .htaccess to be added next.' );
	// Remove high security .htaccess.
	if ( file_exists( backupbuddy_core::getBackupDirectory() . '.htaccess' ) ) {
		$unlink_status = @unlink( backupbuddy_core::getBackupDirectory() . '.htaccess' );
		if ( $unlink_status === false ) {
			pb_backupbuddy::alert( 'Error #844594. Unable to temporarily remove .htaccess security protection on archives directory to allow downloading. Please verify permissions of the BackupBuddy archives directory or manually download via FTP.' );
		}
	}
	
	// Place normal .htaccess.
	pb_backupbuddy::anti_directory_browsing( backupbuddy_core::getBackupDirectory(), $die_on_fail );

} else { // High security mode. Make sure high security .htaccess in place.
	pb_backupbuddy::status( 'details', 'Adding .htaccess high security mode for backups directory.' );
	$htaccess_creation_status = @file_put_contents( backupbuddy_core::getBackupDirectory() . '.htaccess', 'deny from all' );
	if ( $htaccess_creation_status === false ) {
		pb_backupbuddy::alert( 'Error #344894545. Security Warning! Unable to create security file (.htaccess) in backups archive directory. This file prevents unauthorized downloading of backups should someone be able to guess the backup location and filenames. This is unlikely but for best security should be in place. Please verify permissions on the backups directory.' );
	}
	
}


// Remove any copy of importbuddy.php in root.
pb_backupbuddy::status( 'details', 'Cleaning up importbuddy.php script in site root if it exists & is not very recent.' );
if ( file_exists( ABSPATH . 'importbuddy.php' ) ) {
	$modified = filemtime( ABSPATH . 'importbuddy.php' );
	if ( ( FALSE === $modified ) || ( time() > ( $modified + $max_importbuddy_age ) ) ) { // If time modified unknown OR was modified long enough ago.
		pb_backupbuddy::status( 'details', 'Unlinked importbuddy.php in root of site.' );
		unlink( ABSPATH . 'importbuddy.php' );
	} else {
		pb_backupbuddy::status( 'details', 'SKIPPED unlinking importbuddy.php in root of site as it is fresh and may still be in use.' );
	}
}


// Remove any copy of importbuddy directory in root.
pb_backupbuddy::status( 'details', 'Cleaning up importbuddy directory in site root if it exists & is not very recent.' );
if ( file_exists( ABSPATH . 'importbuddy/' ) ) {
	$modified = filemtime( ABSPATH . 'importbuddy/' );
	if ( ( FALSE === $modified ) || ( time() > ( $modified + $max_importbuddy_age ) ) ) { // If time modified unknown OR was modified long enough ago.
		pb_backupbuddy::status( 'details', 'Unlinked importbuddy directory recursively in root of site.' );
		pb_backupbuddy::$filesystem->unlink_recursive( ABSPATH . 'importbuddy/' );
	} else {
		pb_backupbuddy::status( 'details', 'SKIPPED unlinked importbuddy directory recursively in root of site as it is fresh and may still be in use.' );
	}
}



// Remove any old temporary directories in wp-content/uploads/backupbuddy_temp/. Logs any directories it cannot delete.
pb_backupbuddy::status( 'details', 'Cleaning up any old temporary zip directories in: wp-content/uploads/backupbuddy_temp/' );
$temp_directory = backupbuddy_core::getTempDirectory();
$files = glob( $temp_directory . '*' );
if ( is_array( $files ) && !empty( $files ) ) { // For robustness. Without open_basedir the glob() function returns an empty array for no match. With open_basedir in effect the glob() function returns a boolean false for no match.
	foreach( $files as $file ) {
		if ( ( strpos( $file, 'index.' ) !== false ) || ( strpos( $file, '.htaccess' ) !== false ) ) { // Index file or htaccess dont get deleted so go to next file.
			continue;
		}
		$file_stats = stat( $file );
		if ( ( 0 == $backup_age_limit ) || ( ( time() - $file_stats['mtime'] ) > $backup_age_limit ) ) { // If older than 12 hours, delete the log.
			if ( @pb_backupbuddy::$filesystem->unlink_recursive( $file ) === false ) {
				pb_backupbuddy::status( 'error', 'Unable to clean up (delete) temporary directory/file: `' . $file . '`. You should manually delete it or check permissions.' );
			}
		}
	}
}


// Cleanup any temp files from a failed restore within WordPress. (extract file feature).
$temp_dir = get_temp_dir();
pb_backupbuddy::status( 'details', 'Cleaning up temporary files from individual file / directory restores in directory `' . $temp_dir . '`...' );
$possibly_temp_restore_dirs = glob( $temp_dir . 'backupbuddy-*' );
if ( ! is_array( $possibly_temp_restore_dirs ) ) { $possibly_temp_restore_dirs = array(); }
foreach( $possibly_temp_restore_dirs as $possibly_temp_restore_dir ) {
	if ( false === pb_backupbuddy::$filesystem->unlink_recursive( $possibly_temp_restore_dir ) ) { // Delete.
		pb_backupbuddy::status( 'details', 'Unable to delete temporary holding directory `' . $possibly_temp_restore_dir . '`.' );
	} else {
		pb_backupbuddy::status( 'details', 'Cleaned up temporary files.' );
	}
}
pb_backupbuddy::status( 'details', 'Individual file / directory restore cleanup complete.' );


// Remove any old temporary zip directories: wp-content/uploads/backupbuddy_backups/temp_zip_XXXX/. Logs any directories it cannot delete.
pb_backupbuddy::status( 'details', 'Cleaning up any old temporary zip directories in backup directory temp location `' . backupbuddy_core::getBackupDirectory() . 'temp_zip_XXXX/`.' );
$temp_directory = backupbuddy_core::getBackupDirectory() . 'temp_zip_*';
$files = glob( $temp_directory . '*' );
if ( is_array( $files ) && !empty( $files ) ) { // For robustness. Without open_basedir the glob() function returns an empty array for no match. With open_basedir in effect the glob() function returns a boolean false for no match.
	foreach( $files as $file ) {
		if ( ( strpos( $file, 'index.' ) !== false ) || ( strpos( $file, '.htaccess' ) !== false ) ) { // Index file or htaccess dont get deleted so go to next file.
			continue;
		}
		$file_stats = stat( $file );
		if ( ( time() - $file_stats['mtime'] ) > $backup_age_limit ) { // If older than 12 hours, delete the log.
			if ( @pb_backupbuddy::$filesystem->unlink_recursive( $file ) === false ) {
				$message = 'BackupBuddy was unable to clean up (delete) temporary directory/file: `' . $file . '`. You should manually delete it and/or verify proper file permissions to allow BackupBuddy to clean up for you.';
				pb_backupbuddy::status( 'error', $message );
				backupbuddy_core::mail_error( $message );
			}
		}
	}
}


// Cleanup remote S3 multipart chunking.
foreach( pb_backupbuddy::$options['remote_destinations'] as $destination ) {
	if ( $destination['type'] != 's3' ) { continue; }
	if ( isset( $destination['max_chunk_size'] ) && ( $destination['max_chunk_size'] == '0' ) ) { continue; }
	
	pb_backupbuddy::status( 'details', 'Found S3 Multipart Chunking Destinations to cleanup.' );
	require_once( pb_backupbuddy::plugin_path() . '/destinations/bootstrap.php' );
	$cleanup_result = pb_backupbuddy_destinations::multipart_cleanup( $destination );
	/*
	if ( true === $cleanup_result ) {
		pb_backupbuddy::status( 'details', 'S3 Multipart Chunking Cleanup Success.' );
	} else {
		pb_backupbuddy::status( 'error', 'S3 Multipart Chunking Cleanup FAILURE. Manually cleanup stalled multipart send via S3 or try again later.' );
	}
	*/
}


// Clean up and temp rollback database tables.
foreach( pb_backupbuddy::$options['rollback_cleanups'] as $cleanup_serial => $start_time ) {
	if ( ( time() - $start_time ) > $max_status_log_age ) {
		if ( false !== ( $results = mysql_query( "SELECT table_name FROM information_schema.tables WHERE table_name LIKE 'BBnew-" . $cleanup_serial . "\_%' AND table_schema = DATABASE()" ) ) ) {
				while( $result = mysql_fetch_row( $results ) ) {
					if ( false === mysql_query( "DROP TABLE `" . mysql_real_escape_string( $result[0] ) . "`") ) {
						pb_backupbuddy::status( 'details', 'Error #8493984: Unable to drop temp rollback table `' . $result['table_name'] . '`.' );
					}
				}
		}
		unset( pb_backupbuddy::$options['rollback_cleanups'][$cleanup_serial] );
		pb_backupbuddy::save();
	}
}


@clearstatcache(); // Clears file info stat cache.

pb_backupbuddy::status( 'message', 'Finished cleanup procedure.' );