<?php
// backupbuddy_core class always available to functions here.

class pb_backupbuddy_ajax extends pb_backupbuddy_ajaxcore {
	
	
	// IMPORTANT: MUST provide 3rd param, backup serial ID, when using pb_backupbuddy::status() within this function for it to show for this backup.
	public function backup_status() {
		$serial = trim( pb_backupbuddy::_POST( 'serial' ) );
		$serial = str_replace( '/\\', '', $serial );
		$init_wait_retry_count = (int)trim( pb_backupbuddy::_POST( 'initwaitretrycount' ) );
		
		// Forward all logging to this serial file.
		pb_backupbuddy::set_status_serial( $serial );
		
		if ( true == get_transient( 'pb_backupbuddy_stop_backup-' . $serial ) ) {
			pb_backupbuddy::status( 'message', 'Backup STOPPED. Post backup cleanup step has been scheduled to clean up any temporary files.', $serial );
			
			require_once( pb_backupbuddy::plugin_path() . '/classes/fileoptions.php' );
			$fileoptions_file = backupbuddy_core::getLogDirectory() . 'fileoptions/' . $serial . '.txt';
			$backup_options = new pb_backupbuddy_fileoptions( $fileoptions_file, false, $ignore_lock = true );
			
			if ( true !== ( $result = $backup_options->is_ok() ) ) {
				pb_backupbuddy::status( 'error', 'Unable to access fileoptions file `' . $fileoptions_file . '`.', $serial );
			}
			
			// Wipe backup file.
			if ( isset( $backup_options->options['archive_file'] ) && file_exists( $backup_options->options['archive_file'] ) ) { // Final zip file.
				$unlink_result = @unlink( $backup_options->options['archive_file'] );
				if ( true === $unlink_result ) {
					pb_backupbuddy::status( 'details', 'Deleted stopped backup ZIP file.', $serial );
				} else {
					pb_backupbuddy::status( 'error', 'Unable to delete stopped backup file. You should delete it manually as it may be damaged from stopping mid-backup. File to delete: `' . $backup_options->options['archive_file'] . '`.', $serial );
				}
			} else {
				pb_backupbuddy::status( 'details', 'Archive file not found. Not deleting.', $serial );
			}
			
			// NOTE: fileoptions file will be wiped by periodic cleanup. We need to keep this for now...
			
			delete_transient( 'pb_backupbuddy_stop_backup-' . $serial );
			pb_backupbuddy::status( 'details', 'Backup stopped. Any remaining processes or files will time out and be cleaned up by scheduled housekeeping functionality.', $serial );
			pb_backupbuddy::status( 'haltScript', '', $serial ); // Halt JS on page.
		}
		
		// Make sure the serial exists.
		if ( $serial != '' ) {
			require_once( pb_backupbuddy::plugin_path() . '/classes/fileoptions.php' );
			$fileoptions_file = backupbuddy_core::getLogDirectory() . 'fileoptions/' . $serial . '.txt';
			$backup_options = new pb_backupbuddy_fileoptions( $fileoptions_file, $read_only = true, $ignore_lock = true );
			$backup = &$backup_options->options;
			if ( true !== ( $result = $backup_options->is_ok() ) ) {
				if ( 0 >= $init_wait_retry_count ) {
					// Waited too long for init to complete, must be something wrong
					pb_backupbuddy::status( 'error', 'Error #8329754.  Error retrieving fileoptions file `' . $fileoptions_file . '`. Error details `' . $result . '`.', $serial );
					pb_backupbuddy::status( 'haltScript', '', $serial );
					die();
				} else {
					pb_backupbuddy::status( 'details', 'Waiting for the fileoptions initialization for serial `' . $serial . '` to complete: ' . $init_wait_retry_count, $serial );				
					pb_backupbuddy::status( 'wait_init', '', $serial );
				}
				
			}
		}
		if ( ( $serial == '' ) || ( !is_array( $backup ) ) ) {
			pb_backupbuddy::status( 'error', 'Error #9031. Invalid backup serial (' . htmlentities( $serial ) . '). Please check directory permissions for your wp-content/uploads/ directory recursively, your PHP error_log for any errors, and that you have enough free disk space. If seeking support please provide this full status log and PHP error log. Fatal error. Verify this fileoptions file exists `' . $fileoptions_file . '`', $serial );
			pb_backupbuddy::status( 'haltScript', '', $serial );
			die();
		} else {
			
			// Verify init completed.
			if ( false === $backup['init_complete'] ) {
				if ( 0 >= $init_wait_retry_count ) {
					// Waited too long for init to complete, must be something wrong
					pb_backupbuddy::status( 'error', 'Error #9033: The pre-backup initialization for serial `' . $serial . '` was unable save pre-backup initialization options (init_complete===false) possibly because the pre-backup initialization step did not complete. If the log indicates the pre-backup procedure did indeed complete then something prevented BackupBuddy from updating the database such as an misconfigured caching plugin. Check for any errors above or in logs. Verify permissions & that there is enough server memory. See the BackupBuddy "Server Information" page to help assess your server.', $serial );
					pb_backupbuddy::status( 'haltScript', '', $serial );
				} else {
					pb_backupbuddy::status( 'details', 'Waiting for the pre-backup initialization for serial `' . $serial . '` to complete: ' . $init_wait_retry_count, $serial );				
					pb_backupbuddy::status( 'wait_init', '', $serial );
				}
			}
			
			//***** Process any specialAction methods.
			if ( 'checkSchedule' == pb_backupbuddy::_POST( 'specialAction' ) ) {
				
				if ( FALSE === ( $next_scheduled = wp_next_scheduled( 'pb_backupbuddy_process_backup', array( $serial ) ) ) ) {
					//pb_backupbuddy::status( 'details', print_r( pb_backupbuddy::_POST(), true ), $serial );
					pb_backupbuddy::status( 'warning', 'WordPress reports the next step is not currently scheduled. It is either in the process of running or went missing.', $serial, null, $echoNotWrite = true );
				} else {
					pb_backupbuddy::status( 'details', 'Checked cron schedule. Next run: `' . $next_scheduled . '`. ' . ( $next_scheduled - time() ) . ' seconds from now.', $serial, null, $echoNotWrite = true );
				}
				
			}
			//***** End processing any specialAction methods.
			
			//***** Begin outputting status of the current step.
			foreach( $backup['steps'] as $step ) {
				if ( ( $step['start_time'] != -1 ) && ( $step['start_time'] != 0 ) && ( $step['finish_time'] == 0 ) ) { // A step isnt mark to skip, has begun but has not finished. This should not happen but the WP cron is funky. Wait a while before continuing.
					
					// For database dump step output the SQL file current size.
					if ( $step['function'] == 'backup_create_database_dump' ) {
						$sql_file = $backup['temp_directory'] . 'db_1.sql';
						if ( file_exists( $sql_file ) ) {
							$sql_filesize = pb_backupbuddy::$format->file_size( filesize( $sql_file ) );
						} else { // No SQL file yet.
							$sql_filesize = '[SQL file not found yet]';
						}
						pb_backupbuddy::status( 'details', 'Current SQL database dump file size: ' . $sql_filesize . '.', $serial );
					}
					
					pb_backupbuddy::status( 'details', 'Waiting for function `' . $step['function'] . '` to complete. Started ' . ( time() - $step['start_time'] ) . ' seconds ago.', $serial );
					if ( ( time() - $step['start_time'] ) > 300 ) {
						pb_backupbuddy::status( 'warning', 'The function `' . $step['function'] . '` is taking an abnormally long time to complete (' . ( time() - $step['start_time'] ) . ' seconds). The backup may have failed. If it does not increase in the next few minutes it most likely timed out. See the Status Log for details.', $serial );
					}
				} elseif ( $step['start_time'] == 0 ) { // Step that has not started yet.
					// Do nothing.
				} elseif ( $step['start_time'] == -1 ) { // Step marked for skipping (backup stop button hit).
					// Do nothing.
				} else { // Last case: Finished. Skip.
					// Do nothing.
				}
			}
			//***** End outputting status of the current step.
			
			
			//***** Begin output of temp zip file size.
			$temporary_zip_directory = backupbuddy_core::getBackupDirectory() . 'temp_zip_' . $serial . '/';
			if ( file_exists( $temporary_zip_directory ) ) { // Temp zip file.
				$directory = opendir( $temporary_zip_directory );
				while( $file = readdir( $directory ) ) {
					if ( ( $file != '.' ) && ( $file != '..' ) && ( $file != 'exclusions.txt' ) && ( !preg_match( '/.*\.txt/', $file ) ) && ( !preg_match( '/pclzip.*\.gz/', $file) ) ) {
						$stats = stat( $temporary_zip_directory . $file );
						pb_backupbuddy::status( 'details', __('Temporary ZIP file size', 'it-l10n-backupbuddy' ) .': ' . pb_backupbuddy::$format->file_size( $stats['size'] ), $serial );
						pb_backupbuddy::status( 'archiveSize', pb_backupbuddy::$format->file_size( $stats['size'] ), $serial );
					}
				}
				closedir( $directory );
				unset( $directory );
			}
			//***** End output of temp zip file size.
			
			
			// Output different stuff to the browser depending on whether backup is finished or not.
			if ( $backup['finish_time'] > 0 ) { // BACKUP FINISHED.
				
				// OUTPUT COMPLETED ZIP FINAL SIZE.
				if( file_exists( $backup['archive_file'] ) ) { // Final zip file.
					$stats = stat( $backup['archive_file'] );
					pb_backupbuddy::status( 'details', '--- ' . __( 'New PHP process.' ), $serial );
					pb_backupbuddy::status( 'details', __('Completed backup final ZIP file size', 'it-l10n-backupbuddy' ) . ': ' . pb_backupbuddy::$format->file_size( $stats['size'] ), $serial );
					pb_backupbuddy::status( 'archiveSize', pb_backupbuddy::$format->file_size( $stats['size'] ), $serial );
					$backup_finished = true;
				} else {
					$purposeful_deletion = false;
					foreach( $backup['steps'] as $step ) {
						if ( $step['function'] == 'send_remote_destination' ) {
							if ( $step['args'][1] == true ) {
								pb_backupbuddy::status( 'details', 'Option to delete local backup after successful send enabled so local file deleted.' );
								$purposeful_deletion = true;
								break;
							}
						}
					}
					if ( $purposeful_deletion !== true ) {
						pb_backupbuddy::status( 'error', __( 'Backup reports success but unable to access final ZIP file. Verify permissions and ownership. If the error persists insure that server is properly configured with suphp and proper ownership & permissions.', 'it-l10n-backupbuddy' ), $serial );
					}
				}
				pb_backupbuddy::status( 'message', __('Backup successfully completed in', 'it-l10n-backupbuddy' ) . ' ' . pb_backupbuddy::$format->time_duration( $backup['finish_time'] - $backup['start_time'] ) . '.', $serial );
				pb_backupbuddy::status( 'milestone', 'finish_backup', $serial );
			} else { // NOT FINISHED
				//$return_status .= '!' . pb_backupbuddy::$format->localize_time( time() ) . "|~|0|~|0|~|ping\n";
			}
			
			
			//***** Begin getting status log information.
			$status_lines = pb_backupbuddy::get_status( $serial, true, false, true ); // Clear file, dont unlink file, supress status retrieval msg.
			echo implode( '', $status_lines );
			
			// Queue up a pong for the next response.
			pb_backupbuddy::status( 'message', __( 'Pong! Server replied.', 'it-l10n-backupbuddy' ), $serial );
		}
		
		
		die();
	} // End backup_status().
	
	
	
	/* importbuddy()
	 *
	 * Compile ImportBuddy and stream download to browser.
	 *
	 */
	public function importbuddy() {
		
		$pass_hash = '';
		$password = stripslashes( pb_backupbuddy::_GET( 'p' ) );
		
		if ( $password != '' ) {
			$pass_hash = md5( $password );
			if ( pb_backupbuddy::$options['importbuddy_pass_hash'] == '' ) { // if no default pass is set then we set this as default.
				pb_backupbuddy::$options['importbuddy_pass_hash'] = $pass_hash;
				pb_backupbuddy::$options['importbuddy_pass_length'] = strlen( $password ); // length of pass pre-hash.
				pb_backupbuddy::save();
			}
		}
		
		backupbuddy_core::importbuddy( '', $pass_hash ); // Outputs importbuddy to browser for download.
		
		die();
		
	} // End importbuddy().
	
	
	
	/* repairbuddy()
	 *
	 * Compile RepairBuddy and stream download to browser.
	 *
	 */
	public function repairbuddy() {
		
		backupbuddy_core::repairbuddy(); // Outputs repairbuddy to browser for download.
		
		die();
	} // End repairbuddy().
	
	
	
	/* hash()
	 *
	 * Generate a hash/CRC for a file at user request.
	 *
	 */
	public function hash() {
		pb_backupbuddy::load();
		
		pb_backupbuddy::$ui->ajax_header();
		
		require_once( 'ajax/_hash.php' );
		
		pb_backupbuddy::$ui->ajax_footer();
		die();
		
	} // End destination_picker().
	
	
	
	/* destination_picker()
	 *
	 * iframe remote destination selector page.
	 *
	 */
	public function destination_picker() {
		pb_backupbuddy::load();
		
		pb_backupbuddy::$ui->ajax_header();
		
		$mode = 'destination';
		require_once( 'ajax/_destination_picker.php' );
		
		pb_backupbuddy::$ui->ajax_footer();
		die();
		
	} // End destination_picker().
	
	
	
	/* migration_picker()
	 *
	 * Same as destination picker but in migration mode (only limited destinations are available).
	 *
	 */
	public function migration_picker() {
		pb_backupbuddy::load();
		
		pb_backupbuddy::$ui->ajax_header();
		
		$mode = 'migration';
		require_once( 'ajax/_destination_picker.php' );
		
		pb_backupbuddy::$ui->ajax_footer();
		die();
		
	} // End migration_picker().
	
	
	
	/*	remote_send()
	 *	
	 *	Send backup archive to a remote destination manually. Optionally sends importbuddy.php with files.
	 *	Sends are scheduled to run in a cron and are passed to the cron.php remote_send() method.
	 *	
	 *	@return		null
	 */
	public function remote_send() {
		
		$success_output = false; // Set to true onece a leading 1 has been sent to the javascript to indicate success.
		$destination_id = pb_backupbuddy::_POST( 'destination_id' );
		if ( pb_backupbuddy::_POST( 'file' ) != 'importbuddy.php' ) {
			$backup_file = backupbuddy_core::getBackupDirectory() . pb_backupbuddy::_POST( 'file' );
			if ( ! file_exists( $backup_file ) ) { // Error if file to send did not exist!
				$error_message = 'Unable to find file `' . $backup_file . '` to send. File does not appear to exist. You can try again in a moment or turn on full error logging and try again to log for support.';
				pb_backupbuddy::status( 'error', $error_message );
				pb_backupbuddy::alert( $error_message, true );
				die();
			}
		} else {
			$backup_file = '';
		}
		
		// Send ImportBuddy along-side?
		if ( pb_backupbuddy::_POST( 'send_importbuddy' ) == '1' ) {
			$send_importbuddy = true;
			pb_backupbuddy::status( 'details', 'Cron send to be scheduled with importbuddy sending.' );
		} else {
			$send_importbuddy = false;
			pb_backupbuddy::status( 'details', 'Cron send to be scheduled WITHOUT importbuddy sending.' );
		}
		
		// Delete local copy after send completes?
		if ( pb_backupbuddy::_POST( 'delete_after' ) == 'true' ) {
			$delete_after = true;
			pb_backupbuddy::status( 'details', 'Remote send set to delete after successful send.' );
		} else {
			$delete_after = false;
			pb_backupbuddy::status( 'details', 'Remote send NOT set to delete after successful send.' );
		}
		
		// For Stash we will check the quota prior to initiating send.
		if ( pb_backupbuddy::$options['remote_destinations'][$destination_id]['type'] == 'stash' ) {
			// Pass off to destination handler.
			require_once( pb_backupbuddy::plugin_path() . '/destinations/bootstrap.php' );
			$send_result = pb_backupbuddy_destinations::get_info( 'stash' ); // Used to kick the Stash destination into life.
			$stash_quota = pb_backupbuddy_destination_stash::get_quota( pb_backupbuddy::$options['remote_destinations'][$destination_id], true );
			
			if ( isset( $stash_quota['error'] ) ) {
				echo  ' Error accessing Stash account. Send aborted. Details: `' . implode( ' - ', $stash_quota['error'] ) . '`.';
				die();
			}
			
			if ( $backup_file != '' ) {
				$backup_file_size = filesize( $backup_file );
			} else {
				$backup_file_size = 50000;
			}
			if ( ( $backup_file_size + $stash_quota['quota_used'] ) > $stash_quota['quota_total'] ) {
				echo "You do not have enough Stash storage space to send this file. Please upgrade your Stash storage or delete files to make space.\n\n";
				
				echo 'Attempting to send file of size ' . pb_backupbuddy::$format->file_size( $backup_file_size ) . ' but you only have ' . $stash_quota['quota_available_nice'] . ' available. ';
				echo 'Currently using ' . $stash_quota['quota_used_nice'] . ' of ' . $stash_quota['quota_total_nice'] . ' (' . $stash_quota['quota_used_percent'] . '%).';
				die();
			} else {
				if ( isset( $stash_quota['quota_warning'] ) && ( $stash_quota['quota_warning'] != '' ) ) {
					echo '1Warning: ' . $stash_quota['quota_warning'] . "\n\n";
					$success_output = true;
				}
			}
			
		} // end if Stash.
		
		pb_backupbuddy::status( 'details', 'Scheduling cron to send to this remote destination...' );
		$schedule_result = backupbuddy_core::schedule_single_event( time(), pb_backupbuddy::cron_tag( 'remote_send' ), array( $destination_id, $backup_file, pb_backupbuddy::_POST( 'trigger' ), $send_importbuddy, $delete_after ) );
		if ( $schedule_result === FALSE ) {
			$error = 'Error scheduling file transfer. Please check your BackupBuddy error log for details. A plugin may have prevented scheduling or the database rejected it.';
			pb_backupbuddy::status( 'error', $error );
			echo $error;
		} else {
			pb_backupbuddy::status( 'details', 'Cron to send to remote destination scheduled.' );
		}
		spawn_cron( time() + 150 ); // Adds > 60 seconds to get around once per minute cron running limit.
		update_option( '_transient_doing_cron', 0 ); // Prevent cron-blocking for next item.
		
		// SEE cron.php remote_send() for sending function that we pass to via the cron above.
		
		if ( $success_output === false ) {
			echo 1;
		}
		die();
	} // End remote_send().
	
	
	
	/*	migrate_status()
	 *	
	 *	Gives the current migration status. Echos.
	 *	
	 *	@return		null
	 */
	function migrate_status() {
		
		$step = pb_backupbuddy::_POST( 'step' );
		$backup_file = pb_backupbuddy::_POST( 'backup_file' );
		$url = trim( pb_backupbuddy::_POST( 'url' ) );
		
		switch( $step ) {
			case 'step1': // Make sure backup file has been transferred properly.
				// Find last migration.
				$last_migration_key = get_transient( 'pb_backupbuddy_migrationkey' );
				
				if ( false === $last_migration_key ) {
					die( json_encode( array(
						'status_code' 		=>		'failure',
						'status_message'	=>		'Status: Error #54849545. Unable to determine which backup is migrating. Please try again.',
						'next_step'			=>		'0',
					) ) );
				}
				
				pb_backupbuddy::status( 'details', 'About to load fileoptions data.' );
				require_once( pb_backupbuddy::plugin_path() . '/classes/fileoptions.php' );
				$fileoptions_obj = new pb_backupbuddy_fileoptions( backupbuddy_core::getLogDirectory() . 'fileoptions/send-' . $last_migration_key . '.txt', $read_only = true, $ignore_lock = true, $create_file = false );
				if ( true !== ( $result = $fileoptions_obj->is_ok() ) ) {
					pb_backupbuddy::status( 'error', __('Fatal Error #9034.2342348. Unable to access fileoptions data.', 'it-l10n-backupbuddy' ) . ' Error: ' . $result );
					return false;
				}
				pb_backupbuddy::status( 'details', 'Fileoptions data loaded.' );
				$fileoptions = &$fileoptions_obj->options;
				
				$migrate_send_status = $fileoptions['status'];
				
				if ( $migrate_send_status == 'timeout' ) {
					$status_message = 'Status: Waiting for backup to finish uploading to server...';
					$next_step = '1';
				} elseif ( $migrate_send_status == 'failure' ) {
					$status_message = 'Status: Sending backup to server failed.';
					$next_step = '0';
				} elseif ( $migrate_send_status == 'success' ) {
					$status_message = 'Status: Success sending backup file.';
					$next_step = '2';
				}
				die( json_encode( array(
					'status_code' 		=>		$migrate_send_status,
					'status_message'	=>		$status_message,
					'next_step'			=>		$next_step,
				) ) );
				
				break;
				
			case 'step2': // Hit importbuddy file to make sure URL is correct, it exists, and extracts itself fine.
				
				$url = rtrim( $url, '/' ); // Remove trailing slash if its there.
				if ( strpos( $url, 'importbuddy.php' ) === false ) { // If no importbuddy.php at end of URL add it.
					$url .= '/importbuddy.php';
				}
				
				if ( ( false === strstr( $url, 'http://' ) ) && ( false === strstr( $url, 'https://' ) ) ) { // http or https is missing; prepend it.
					$url = 'http://' . $url;
				}
				
				$response = wp_remote_get( $url . '?api=ping', array(
						'method' => 'GET',
						'timeout' => 45,
						'redirection' => 5,
						'httpversion' => '1.0',
						'blocking' => true,
						'headers' => array(),
						'body' => null,
						'cookies' => array()
					)
				);
				
				
				if( is_wp_error( $response ) ) {
					die( json_encode( array(
						'status_code' 		=>		'failure',
						'status_message'	=>		'Status: HTTP error checking for importbuddy.php at `' . $url . '`. Error: `' . $response->get_error_message() . '`.',
						'next_step'			=>		'0',
					) ) );
				}
				
				
				if ( trim( $response['body'] ) == 'pong' ) { // Importbuddy found.
					die( json_encode( array(
						'import_url'		=>		$url . '?display_mode=embed&file=' . pb_backupbuddy::_POST( 'backup_file' ) . '&v=' . pb_backupbuddy::$options['importbuddy_pass_hash'],
						'status_code' 		=>		'success',
						'status_message'	=>		'Sucess verifying URL is valid importbuddy.php location. Continue migration below.',
						'next_step'			=>		'0',
					) ) );
				} else { // No importbuddy here.
					die( json_encode( array(
						'status_code' 		=>		'failure',
						'status_message'	=>		'<b>Error</b>: The importbuddy.php file uploaded was not found at <a href="' . $url . '">' . $url . '</a>. Please verify the URL properly matches & corresponds to the upload directory entered for this destination\'s settings.<br><br><b>Tip:</b> This error is only caused by URL not properly matching, permissions on the destination server blocking the script, or other destination server error. You may manually verify that the importbuddy.php scripts exists in the expected location on the destination server and that the script URL <a href="' . $url . '">' . $url . '</a> properly loads the ImportBuddy tool. You may manually upload importbuddy.php and the backup ZIP file to the destination server & navigating to its URL in your browser for an almost-as-quick alternative.',
						'next_step'			=>		'0',
					) ) );
				}
				
				break;
				
			default:
				echo 'Invalid migrate_status() step: `' . pb_backupbuddy::_POST( 'step' ) . '`.';
				break;
		} // End switch on action.
		
		die();
		
	} // End migrate_status().
	
	
	
	/*	icicle()
	 *	
	 *	Builds and returns graphical directory size listing. Echos.
	 *	
	 *	@return		null
	 */
	public function icicle() {
		pb_backupbuddy::set_greedy_script_limits(); // Building the directory tree can take a bit.
		
		$response = backupbuddy_core::build_icicle( ABSPATH, ABSPATH, '', -1 );
		
		echo $response[0];
		die();
	} // End icicle().
	
	
	
	/* remote_delete()
	 *
	 * description
	 *
	 */
	public function remote_delete() {
		
		pb_backupbuddy::verify_nonce(); // Security check.
		
		// Destination ID.
		$destination_id = pb_backupbuddy::_GET( 'pb_backupbuddy_destinationid' );
		
		// Delete the destination.
		require_once( pb_backupbuddy::plugin_path() . '/destinations/bootstrap.php' );
		$delete_response = pb_backupbuddy_destinations::delete_destination( $destination_id, true );
		
		// Response.
		if ( $delete_response !== true ) { // Some kind of error so just echo it.
			echo 'Error #544558: `' . $delete_response . '`.';
		} else { // Success.
			echo 'Destination deleted.';
		}
		
		die();
		
	} // End remote_delete().
	
	
	
	/*	remote_test()
	 *	
	 *	Remote destination testing. Echos.
	 *	
	 *	@return		null
	 */
	function remote_test() {
		
		if ( defined( 'PB_DEMO_MODE' ) ) {
			die( 'Access denied in demo mode.' );
		}
		
		global $pb_backupbuddy_destination_errors;
		$pb_backupbuddy_destination_errors = array();
		
		
		require_once( pb_backupbuddy::plugin_path() . '/destinations/bootstrap.php' );
		
		$form_settings = array();
		foreach( pb_backupbuddy::_POST() as $post_id => $post ) {
			if ( substr( $post_id, 0, 15 ) == 'pb_backupbuddy_' ) {
				$id = substr( $post_id, 15 );
				if ( $id != '' ) {
					$form_settings[$id] = $post;
				}
			}
		}
		
		$test_result = pb_backupbuddy_destinations::test( $form_settings );
		
		if ( $test_result === true ) {
			echo 'Test successful.';
		} else {
			echo "Test failed.\n\n";
			echo $test_result;
			foreach( $pb_backupbuddy_destination_errors as $pb_backupbuddy_destination_error ) {
				echo $pb_backupbuddy_destination_error . "\n";
			}
		}
		
		die();
		
	} // End remote_test().
	
	
	
	/*	remote_save()
	 *	
	 *	Remote destination saving.
	 *	
	 *	@return		null
	 */
	public function remote_save() {
		
		pb_backupbuddy::verify_nonce();
		
		
		require_once( pb_backupbuddy::plugin_path() . '/destinations/bootstrap.php' );
		$settings_form = pb_backupbuddy_destinations::configure( array( 'type' => pb_backupbuddy::_POST( 'pb_backupbuddy_type' ) ), 'save' );
		
		$save_result = $settings_form->process();
		
		
		$destination_id = trim( pb_backupbuddy::_GET( 'pb_backupbuddy_destinationid' ) );
		
		
		if ( count( $save_result['errors'] ) == 0 ) { // NO ERRORS SO SAVE.
			
			if ( $destination_id == 'NEW' ) { // ADD NEW.
			
				// Copy over dropbox token.
				$save_result['data']['token'] = pb_backupbuddy::$options['dropboxtemptoken'];
				
				pb_backupbuddy::$options['remote_destinations'][] = $save_result['data'];
				
				pb_backupbuddy::save();
				echo 'Destination Added.';
			} elseif ( !isset( pb_backupbuddy::$options['remote_destinations'][$destination_id] ) ) { // EDITING NONEXISTANT.
				echo 'Error #54859. Invalid destination ID.';
			} else { // EDITING EXISTING -- Save!
				
				// Copy over dropbox token.
				//$token_copy_holder = pb_backupbuddy::$options['remote_destinations'][$destination_id]['token'];
				
				pb_backupbuddy::$options['remote_destinations'][$destination_id] = array_merge( pb_backupbuddy::$options['remote_destinations'][$destination_id], $save_result['data'] );
				//echo '<pre>' . print_r( pb_backupbuddy::$options['remote_destinations'][$destination_id], true ) . '</pre>';
				
				pb_backupbuddy::save();
				echo 'Settings saved.';
			}
			
		} else {
			echo "Error saving settings.\n\n";
			echo implode( "\n", $save_result['errors'] );
		}
		die();
		
	} // End remote_save().
	
	
	
	/*	refresh_site_size()
	 *	
	 *	Server info page site size refresh. Echos out the new site size (pretty version).
	 *	
	 *	@return		null
	 */
	public function refresh_site_size() {
		
		$site_size = backupbuddy_core::get_site_size(); // array( site_size, site_size_sans_exclusions ).
		
		echo pb_backupbuddy::$format->file_size( $site_size[0] );
		
		die();
	} // End refresh_site_size().
	
	
	
	/*	refresh_site_size_excluded()
	 *	
	 *	Server info page site size (sans exclusions) refresh. Echos out the new site size (pretty version).
	 *	
	 *	@return		null
	 */
	public function refresh_site_size_excluded() {
		
		$site_size = backupbuddy_core::get_site_size(); // array( site_size, site_size_sans_exclusions ).
		
		echo pb_backupbuddy::$format->file_size( $site_size[1] );
		
		die();
	} // End refresh_site_size().
	
	
	
	/*	refresh_site_objects()
	 *	
	 *	Server info page site objects file count refresh. Echos out the new site file count (pretty version).
	 *	
	 *	@return		null
	 */
	public function refresh_site_objects() {
		
		$site_size = backupbuddy_core::get_site_size(); // array( site_size, site_size_sans_exclusions ).
		
		echo $site_size[2];
		
		die();
	} // End refresh_site_size().
	
	
	
	/*	refresh_site_objects_excluded()
	 *	
	 *	Server info page site objects file count (sans exclusions) refresh. Echos out the new site file count (exclusions applied) (pretty version).
	 *	
	 *	@return		null
	 */
	public function refresh_site_objects_excluded() {
		
		$site_size = backupbuddy_core::get_site_size(); // array( site_size, site_size_sans_exclusions ).
		
		echo $site_size[3];
		
		die();
	} // End refresh_site_size().
	
	
	
	/*	refresh_database_size()
	 *	
	 *	Server info page database size refresh. Echos out the new site size (pretty version).
	 *	
	 *	@return		null
	 */
	public function refresh_database_size() {
		
		$database_size = backupbuddy_core::get_database_size(); // array( database_size, database_size_sans_exclusions ).
		
		echo pb_backupbuddy::$format->file_size( $database_size[1] );
		
		die();
	} // End refresh_site_size().
	
	
	
	/*	refresh_database_size_excluded()
	 *	
	 *	Server info page database size (sans exclusions) refresh. Echos out the new site size (pretty version).
	 *	
	 *	@return		null
	 */
	public function refresh_database_size_excluded() {
		
		$database_size = backupbuddy_core::get_database_size(); // array( database_size, database_size_sans_exclusions ).
		
		echo pb_backupbuddy::$format->file_size( $database_size[1] );
		
		die();
	} // End refresh_site_size().
	
	
	
	/*	exclude_tree()
	 *	
	 *	Directory exclusion tree for settings page.
	 *	
	 *	@return		null
	 */
	function exclude_tree() {
		$root = ABSPATH . urldecode( pb_backupbuddy::_POST( 'dir' ) );
		
		if( file_exists( $root ) ) {
			$files = scandir( $root );
			
			natcasesort( $files );
			
			// Sort with directories first.
			$sorted_files = array(); // Temporary holder for sorting files.
			$sorted_directories = array(); // Temporary holder for sorting directories.
			foreach( $files as $file ) {
				if ( ( $file == '.' ) || ( $file == '..' ) ) {
					continue;
				}
				if( is_file( str_replace( '//', '/', $root . $file ) ) ) {
					array_push( $sorted_files, $file );
				} else {
					array_unshift( $sorted_directories, $file );
				}
			}
			$files = array_merge( array_reverse( $sorted_directories ), $sorted_files );
			unset( $sorted_files );
			unset( $sorted_directories );
			unset( $file );
			
			
			if( count( $files ) > 0 ) { // Files found.
				echo '<ul class="jqueryFileTree" style="display: none;">';
				foreach( $files as $file ) {
					if( file_exists( str_replace( '//', '/', $root . $file ) ) ) {
						if ( is_dir( str_replace( '//', '/', $root . $file ) ) ) { // Directory.
							echo '<li class="directory collapsed">';
							$return = '';
							$return .= '<div class="pb_backupbuddy_treeselect_control">';
							$return .= '<img src="' . pb_backupbuddy::plugin_url() . '/images/redminus.png" style="vertical-align: -3px;" title="Add to exclusions..." class="pb_backupbuddy_filetree_exclude">';
							$return .= '</div>';
							echo '<a href="#" rel="' . htmlentities( str_replace( ABSPATH, '', $root ) . $file) . '/" title="Toggle expand...">' . htmlentities($file) . $return . '</a>';
							echo '</li>';
						} else { // File.
							echo '<li class="file collapsed">';
							$return = '';
							$return .= '<div class="pb_backupbuddy_treeselect_control">';
							$return .= '<img src="' . pb_backupbuddy::plugin_url() . '/images/redminus.png" style="vertical-align: -3px;" title="Add to exclusions..." class="pb_backupbuddy_filetree_exclude">';
							$return .= '</div>';
							echo '<a href="#" rel="' . htmlentities( str_replace( ABSPATH, '', $root ) . $file) . '">' . htmlentities($file) . $return . '</a>';
							echo '</li>';
						}
					}
				}
				echo '</ul>';
			} else {
				echo '<ul class="jqueryFileTree" style="display: none;">';
				echo '<li><a href="#" rel="' . htmlentities( pb_backupbuddy::_POST( 'dir' ) . 'NONE' ) . '"><i>Empty Directory ...</i></a></li>';
				echo '</ul>';
			}
		} else {
			echo 'Error #1127555. Unable to read site root.';
		}
		
		die();
	} // End exclude_tree().
	
	
	
	/*	file_tree()
	 *	
	 *	File tree for viewing zip contents.
	 *	
	 *	@return		null
	 */
	function file_tree() {
		
		// How long to cache the specific backup file tree information for (seconds)
		$max_cache_time = 86400;
		
		// This is the root directory we want the listing for
		$root = trim( urldecode( pb_backupbuddy::_POST( 'dir' ) ) );
		$root_len = strlen( $root );
		
		// This will identify the backup zip file we want to list
		$serial = pb_backupbuddy::_GET( 'serial' );
		
		// The fileoptions file that contains the file tree information		
		require_once( pb_backupbuddy::plugin_path() . '/classes/fileoptions.php' );
		$fileoptions_file = backupbuddy_core::getLogDirectory() . 'fileoptions/' . $serial . '-filetree.txt';
		
		// Purge cache if too old.
		if ( file_exists( $fileoptions_file ) && ( ( time() - filemtime( $fileoptions_file ) ) > $max_cache_time ) ) {
			if ( false === unlink( $fileoptions_file ) ) {
				pb_backupbuddy::alert( 'Error #456765545. Unable to wipe cached fileoptions file `' . $fileoptions_file . '`.' );
			}
		}
		
		$fileoptions = new pb_backupbuddy_fileoptions( $fileoptions_file );
		
		// Either we are getting cached file tree information or we need to create afresh		
		if ( true !== ( $result = $fileoptions->is_ok() ) ) {
			// Get file listing.
			require_once( pb_backupbuddy::plugin_path() . '/lib/zipbuddy/zipbuddy.php' );
			pb_backupbuddy::$classes['zipbuddy'] = new pluginbuddy_zipbuddy( ABSPATH, array(), 'unzip' );
			$files = pb_backupbuddy::$classes['zipbuddy']->get_file_list( backupbuddy_core::getBackupDirectory() . str_replace( '\\/', '', pb_backupbuddy::_GET( 'zip_viewer' ) ) );
			$fileoptions->options = $files;
			$fileoptions->save();
		} else {
			$files = &$fileoptions->options;
		}
		
		// Just make sure we have a sensible files listing
		if ( ! is_array( $files ) ) {
			die( 'Error #548484.  Unable to retrieve file listing from backup file `' . htmlentities( pb_backupbuddy::_GET( 'zip_viewer' ) ) . '`.' );
		}
		
		// To record subdirs of this root
		$subdirs = array();
		
		// Strip out any files/subdirs that are not actually directly under the given root
		foreach( $files as $key => $file ) {
			
			// If shorter than root length then certainly is not within this (root) directory.
			// It's a quick test that is more effective the longer the root (the deeper you go
			// into the tree)
			if ( strlen( $file[ 0 ] ) < $root_len ) {
			
				unset( $files[ $key ] );
				continue;
				
			}
			
			// The root must be prefix of this file	otherwise it's not under the root
			// e.g., with root=this/dir/path/
			// these will fail: file=this/dir/file; file=this/dir/otherpath/; file=that/dir/path/file
			// and these would succeed: file=this/dir/path/; file=this/dir/path/file; file=this/dir/path/otherpath/
			if ( substr( $file[ 0 ], 0, $root_len ) != $root ) {
			
				unset( $files[ $key ] );
				continue;
				
			}
			
			// If the file _is_ the root then we don't want to list it
			// Don't want to do this on _every_ file as very specific so do it here after we have
			// weeded out files for more common reasons
			if ( 0 == strcmp( $file[ 0 ], $root ) ) {
			
				unset( $files[ $key ] );
				continue;				
			
			}
			
			// Interesting file, get the path with the root prefix removed
			// Note: root may be empty in which case the result will be the original filename
			$unrooted_file = substr( $file[ 0 ], $root_len );
			
			// We must ensure that we list the subdir/ even if subdir/ does not appear
			// as a distinct entry in the list but only subdir/file or subdir/subsubdir/ or
			// subdir/subsubdir/file. Find if we have any directory separator(s) in the filename
			// and if so remember where the first is
			if ( false !== ( $pos = strpos( $unrooted_file, '/' ) ) ) {
				
				// Get the subdir/ prefix part, discarding everything after the first /
				$subdir = substr( $unrooted_file, 0, ( $pos + 1 ) );
				
				// Have we already seen it
				if ( !in_array( $subdir, $subdirs ) ) {
				
					// Not already seen so record we have seen it and modify this entry to be
					// specific for the subdir/
					$subdirs[] = $subdir;
					
					// Replace the original (rooted) file name
					$files[ $key ][ 0 ] = $subdir;
					
				} else {
				
					// We already know about the subdir/ so remove this entry
					unset( $files[ $key ] );
					continue;
				
				}
			
			} else {
			
				// This is just like file within the root
				// Replace the original (rooted) file name
				$files[ $key ][ 0 ] = $unrooted_file;
			
			}
			
		}
		
		// Simple sort function to bubble dirs up to the top of list and
		// have dirs and files in simple alpha order
		function pb_backupbuddy_sort_file_list( $a, $b ) {
		
			// If both are dirs or files then result is 0
			// If $a is dir and $b is file then result is -1
			// If $a is file and $b is dir then result is 1
			if ( 0 == ( $res = substr_count( $b[0], '/' ) - substr_count( $a[0], '/' ) ) ) {
			
				// Both same type so sort alpha
				$res = strcmp( rtrim( $a[0], '/' ), rtrim( $b[0], '/') );
			
			}
			
			return $res;
			
		}
		
		// Try and sort the files to put dirs first and all in alpha
		// Remember original in case the sort fails
		$saved_files = $files;
		if ( false === usort( $files, 'pb_backupbuddy_sort_file_list' ) ) {
		
			// Hmm, the sort failed, just revert to original
			$files = $saved_files;
		}
		
		// Now we can start to build the listing to display
		if( count( $files ) > 0 ) {
			echo '<ul class="jqueryFileTree" style="display: none;">';
			
			// Files which are considered text-based and therefore contents viewable to the user.
			$view_ext = array(
				'php',
				'htaccess',
				'htm',
				'html',
				'txt',
				'css',
				'ini',
				'sql',
			);
			
			foreach( $files as $file ) {
				if ( substr( $file[0], -1 ) == '/' ) { // Directory.
					echo '<li class="directory collapsed">';
					$return = '';
					echo '<input type="checkbox">';
					echo '<a class="hoverable" href="#" rel="' . htmlentities( $root . $file[0] ) . '" title="Toggle expand...">' . htmlentities( rtrim( $file[0], '/' ) ) . $return . '</a>';
					echo '</li>';
				} else { // File.
					
					$actions = array();
					$ext = pathinfo( htmlentities( $file[0] ), PATHINFO_EXTENSION );
					
					$viewable = false;
					if ( in_array( $ext, $view_ext ) ) {
						$viewable = true;
					}
					
					echo '<li class="file collapsed ext_' . $ext;
					if ( true === $viewable ) {
						echo ' viewable';
					}
					echo '"><input type="checkbox">';
					if ( true === $viewable ) {
						echo '<a onclick="modal_live(\'restore_file_view\',jQuery(this));" class="hoverable" rel="' . htmlentities( $root . $file[0] ) . '">';
					} else {
						echo '<a href="#" rel="' . htmlentities( $root . $file[0] ) . '">';
					}
					echo htmlentities( $file[0] );
					
					if ( true === $viewable ) {
						echo '<span class="viewlink_place"><img src="' . pb_backupbuddy::plugin_url() . '/images/eyecon.png"></span>';
						echo '<span class="viewlink"><img src="' . pb_backupbuddy::plugin_url() . '/images/eyecon.png"> View</span>';
					}
					
					echo '<span class="pb_backupbuddy_fileinfo">';
					echo '	<span class="pb_backupbuddy_col1">' . pb_backupbuddy::$format->file_size( $file[1] ) . '</span>';
					echo '	<span class="pb_backupbuddy_col2">' . pb_backupbuddy::$format->date( pb_backupbuddy::$format->localize_time( $file[3] ) ) . ' <span class="description">(' . pb_backupbuddy::$format->time_ago( $file[3] ) . ' ago)</span></span>';
					echo '</span>';
					
					echo '</a></li>';
				}
			}
			echo '</ul>';
		} else {
			echo '<ul class="jqueryFileTree" style="display: none;">';
			echo '<li><a href="#" rel="' . htmlentities( pb_backupbuddy::_POST( 'dir' ) . 'NONE' ) . '"><i>Empty Directory ...</i></a></li>';
			echo '</ul>';
		}
		
		die();
	} // End file_tree().
	
	
	
	/*	download_archive()
	 *	
	 *	Handle allowing download of archive.
	 *	
	 *	@param		
	 *	@return		
	 */
	public function download_archive() {
		
		if ( is_multisite() && !current_user_can( 'manage_network' ) ) { // If a Network and NOT the superadmin must make sure they can only download the specific subsite backups for security purposes.
			
			// Only allow downloads of their own backups.
			if ( !strstr( pb_backupbuddy::_GET( 'backupbuddy_backup' ), backupbuddy_core::backup_prefix() ) ) {
				die( 'Access Denied. You may only download backups specific to your Multisite Subsite. Only Network Admins may download backups for another subsite in the network.' );
			}
		}
		
		// Make sure file exists we are trying to get.
		if ( !file_exists( backupbuddy_core::getBackupDirectory() . pb_backupbuddy::_GET( 'backupbuddy_backup' ) ) ) { // Does not exist.
			die( 'Error #548957857584784332. The requested backup file does not exist. It may have already been deleted.' );
		}
		
		$abspath = str_replace( '\\', '/', ABSPATH ); // Change slashes to handle Windows as we store backup_directory with Linux-style slashes even on Windows.
		$backup_dir = str_replace( '\\', '/', backupbuddy_core::getBackupDirectory() );
		
		// Make sure file to download is in a publicly accessible location (beneath WP web root technically).
		if ( FALSE === stristr( $backup_dir, $abspath ) ) {
			die( 'Error #5432532. You cannot download backups stored outside of the WordPress web root. Please use FTP or other means.' );
		}
		
		// Made it this far so download dir is within this WP install.
		$sitepath = str_replace( $abspath, '', $backup_dir );
		$download_url = rtrim( site_url(), '/\\' ) . '/' . trim( $sitepath, '/\\' ) . '/' . pb_backupbuddy::_GET( 'backupbuddy_backup' );
		
		if ( pb_backupbuddy::$options['lock_archives_directory'] == '1' ) { // High security mode.
			
			if ( file_exists( backupbuddy_core::getBackupDirectory() . '.htaccess' ) ) {
				$unlink_status = @unlink( backupbuddy_core::getBackupDirectory() . '.htaccess' );
				if ( $unlink_status === false ) {
					die( 'Error #844594. Unable to temporarily remove .htaccess security protection on archives directory to allow downloading. Please verify permissions of the BackupBuddy archives directory or manually download via FTP.' );
				}
			}
			
			header( 'Location: ' . $download_url );
			ob_clean();
			flush();
			sleep( 8 ); // Wait 8 seconds before creating security file.
			
			$htaccess_creation_status = @file_put_contents( backupbuddy_core::getBackupDirectory() . '.htaccess', 'deny from all' );
			if ( $htaccess_creation_status === false ) {
				die( 'Error #344894545. Security Warning! Unable to create security file (.htaccess) in backups archive directory. This file prevents unauthorized downloading of backups should someone be able to guess the backup location and filenames. This is unlikely but for best security should be in place. Please verify permissions on the backups directory.' );
			}
			
		} else { // Normal mode.
			header( 'Location: ' . $download_url );
		}
		
		
		
		die();
	} // End download_archive().
	
	
	
	/* phpinfo()
	 *
	 * Server info page phpinfo button.
	 *
	 */
	public function phpinfo() {
		phpinfo();
		die();
	} // End phpinfo().
	
	
	
	/*	set_backup_note()
	 *	
	 *	Used for setting a note to a backup archive.
	 *	
	 *	@return		null
	 */
	public function set_backup_note() {
		if ( !isset( pb_backupbuddy::$classes['zipbuddy'] ) ) {
			require_once( pb_backupbuddy::plugin_path() . '/lib/zipbuddy/zipbuddy.php' );
			pb_backupbuddy::$classes['zipbuddy'] = new pluginbuddy_zipbuddy( backupbuddy_core::getBackupDirectory() );
		}
		
		$backup_file = backupbuddy_core::getBackupDirectory() . pb_backupbuddy::_POST( 'backup_file' );
		$note = pb_backupbuddy::_POST( 'note' );
		$note = preg_replace( "/[[:space:]]+/", ' ', $note );
		$note = preg_replace( "/[^[:print:]]/", '', $note );
		$note = substr( $note, 0, 200 );
		
		
		// Returns true on success, else the error message.
		$old_comment = pb_backupbuddy::$classes['zipbuddy']->get_comment( $backup_file );
		$comment = backupbuddy_core::normalize_comment_data( $old_comment );
		$comment['note'] = $note;
		
		//$new_comment = base64_encode( serialize( $comment ) );
		
		$comment_result = pb_backupbuddy::$classes['zipbuddy']->set_comment( $backup_file, $comment );
		
		if ( $comment_result !== true ) {
			echo $comment_result;
		} else {
			echo '1';
		}
		
		// Even if we cannot save the note into the archive file, store it in internal settings.
		$serial = backupbuddy_core::get_serial_from_file( $backup_file );
		
		
		require_once( pb_backupbuddy::plugin_path() . '/classes/fileoptions.php' );
		$backup_options = new pb_backupbuddy_fileoptions( backupbuddy_core::getLogDirectory() . 'fileoptions/' . $serial . '.txt' );
		if ( true === ( $result = $backup_options->is_ok() ) ) {
			$backup_options->options['integrity']['comment'] = $note;
			$backup_options->save();
		}
		
		
		die();
	} // End set_backup_note().
	
	
	
	/* integrity_status()
	 *
	 * description
	 *
	 */
	public function integrity_status() {
		$serial = pb_backupbuddy::_GET( 'serial' );
		$serial = str_replace( '/\\', '', $serial );
		pb_backupbuddy::load();
		pb_backupbuddy::$ui->ajax_header();
		
		require_once( pb_backupbuddy::plugin_path() . '/classes/fileoptions.php' );
		$backup_options = new pb_backupbuddy_fileoptions( backupbuddy_core::getLogDirectory() . 'fileoptions/' . $serial . '.txt', $read_only = true );
		if ( true !== ( $result = $backup_options->is_ok() ) ) {
			pb_backupbuddy::alert( __('Unable to access fileoptions data file.', 'it-l10n-backupbuddy' ) . ' Error: ' . $result );
			die();
		}
		
		$integrity = $backup_options->options['integrity'];
		
		//echo '<p><b>' . __( 'Backup File', 'it-l10n-backupbuddy' ) . '</b>: ' . $integrity['file'] . '</p>';
		
		$start_time = 'Unknown';
		$finish_time = 'Unknown';
		if ( isset( $backup_options->options['start_time'] ) ) {
			$start_time = pb_backupbuddy::$format->date( pb_backupbuddy::$format->localize_time( $backup_options->options['start_time'] ) ) . ' <span class="description">(' . pb_backupbuddy::$format->time_ago( $backup_options->options['finish_time'] ) . ' ago)</span>';
			if ( $backup_options->options['finish_time'] > 0 ) {
				$finish_time = pb_backupbuddy::$format->date( pb_backupbuddy::$format->localize_time( $backup_options->options['finish_time'] ) ) . ' <span class="description">(' . pb_backupbuddy::$format->time_ago( $backup_options->options['finish_time'] ) . ' ago)</span>';
			} else { // unfinished.
				$finish_time = '<i>Unfinished</i>';
			}
		}
		
		
		//***** BEGIN TESTS AND RESULTS.
		if ( isset( $integrity['status_details'] ) ) { // $integrity['status_details'] is NOT array (old, pre-3.1.9).
			echo '<h3>Integrity Technical Details</h3>';
			echo '<textarea style="width: 100%; height: 175px;" wrap="off">';
			foreach( $integrity as $item_name => $item_value ) {
				$item_value = str_replace( '<br />', '<br>', $item_value );
				$item_value = str_replace( '<br><br>', '<br>', $item_value );
				$item_value = str_replace( '<br>', "\n     ", $item_value );
				echo $item_name . ' => ' . $item_value . "\n";
			}
			echo '</textarea><br><br><b>Note:</b> It is normal to see several "file not found" entries as BackupBuddy checks for expected files in multiple locations, expecting to only find each file once in one of those locations.';
		} else { // $integrity['status_details'] is array.
			
			echo '<br>';
			
			if ( isset( $integrity['status_details'] ) ) { // PRE-v4.0 Tests.
				function pb_pretty_results( $value ) {
					if ( $value === true ) {
						return '<span class="pb_label pb_label-success">Pass</span>';
					} else {
						return '<span class="pb_label pb_label-important">Fail</span>';
					}
				}
				
				// The tests & their status..
				$tests = array();
				$tests[] = array( 'BackupBackup data file exists', pb_pretty_results( $integrity['status_details']['found_dat'] ) );
				$tests[] = array( 'Database SQL file exists', pb_pretty_results( $integrity['status_details']['found_sql'] ) );
				if ( $integrity['detected_type'] == 'full' ) { // Full backup.
					$tests[] = array( 'WordPress wp-config.php exists (full/files backups only)', pb_pretty_results( $integrity['status_details']['found_wpconfig'] ) );
				} elseif ( $integrity['detected_type'] == 'files' ) { // Files only backup.
					$tests[] = array( 'WordPress wp-config.php exists (full/files backups only)', pb_pretty_results( $integrity['status_details']['found_wpconfig'] ) );
				} else { // DB only.
					$tests[] = array( 'WordPress wp-config.php exists (full/files backups only)', '<span class="pb_label pb_label-success">N/A</span>' );
				}
			} else { // 4.0+ Tests.
				$tests = array();
				foreach( (array)$integrity['tests'] as $test ) {
					if ( true === $test['pass'] ) {
						$status_text = '<span class="pb_label pb_label-success">Pass</span>';
					} else {
						$status_text = '<span class="pb_label pb_label-important">Fail</span>';
					}
					$tests[] = array( $test['test'], $status_text );
				}
			}
			
			$columns = array(
				__( 'Integrity Test', 'it-l10n-backupbuddy' ),
				__( 'Status', 'it-l10n-backupbuddy' ),
			);
			
			pb_backupbuddy::$ui->list_table(
				$tests,
				array(
					'columns'		=>	$columns,
					'css'			=>	'width: 100%; min-width: 200px;',
				)
			);
		
		} // end $integrity['status_details'] is an array.
		echo '<br><br>';
		//***** END TESTS AND RESULTS.
		
		
		// Output meta info table (if any).
		if ( false === ( $metaInfo = backupbuddy_core::getZipMeta( backupbuddy_core::getBackupDirectory() . $integrity['file'] ) ) ) { // $backup_options->options['archive_file']
			echo '<i>No meta data found in zip comment. Skipping meta information display.</i>';
		} else {
			pb_backupbuddy::$ui->list_table(
				$metaInfo,
				array(
					'columns'		=>	array( 'Backup Details', 'Value' ),
					'css'			=>	'width: 100%; min-width: 200px;',
				)
			);
		}
		echo '<br><br>';
		
		
		//***** BEGIN STEPS.
		$steps = array();
		$steps[] = array( 'Start Time', $start_time, '' );
		if ( isset( $backup_options->options['steps'] ) ) {
			foreach( $backup_options->options['steps'] as $step ) {
				if ( isset( $step['finish_time'] ) && ( $step['finish_time'] != 0 ) ) {
					
					// Step name.
					if ( $step['function'] == 'backup_create_database_dump' ) {
						if ( count( $step['args'][0] ) == 1 ) {
							$step_name = 'Database dump (breakout: ' . $step['args'][0][0] . ')';
						} else {
							$step_name = 'Database dump';
						}
					} elseif ( $step['function'] == 'backup_zip_files' ) {
						if ( isset( $backup_options->options['steps']['backup_zip_files'] ) ) {
							$zip_time = $backup_options->options['steps']['backup_zip_files'];
						} else {
							$zip_time = 0;
						}
						
						// Calculate write speed in MB/sec for this backup.
						if ( $zip_time == '0' ) { // Took approx 0 seconds to backup so report this speed.
							$write_speed = '> ' . pb_backupbuddy::$format->file_size( $backup_options->options['integrity']['size'] );
						} else {
							if ( $zip_time == 0 ) {
								$write_speed = '';
							} else {
								$write_speed = pb_backupbuddy::$format->file_size( $backup_options->options['integrity']['size'] / $zip_time ) . '/sec';
							}
						}
						$step_name = 'Zip archive creation (Write speed: ' . $write_speed . ')';
					} elseif ( $step['function'] == 'post_backup' ) {
						$step_name = 'Post-backup cleanup';
					} elseif( $step['function'] == 'integrity_check' ) {
						$step_name = 'Integrity Check';
					} else {
						$step_name = $step['function'];
					}
					
					// Step time taken.
					$step_time = (string)( $step['finish_time'] - $step['start_time'] ) . ' seconds';
					
					// Compile details for this step into array.
					$steps[] = array(
						$step_name,
						$step_time,
						$step['attempts'],
					);
					
				}
			} // End foreach.
		} else { // End if serial in array is set.
			$step_times[] = 'unknown';
		} // End if serial in array is NOT set.
		
		
		// Total overall time from initiation to end.
		if ( isset( $backup_options->options['finish_time'] ) && isset( $backup_options->options['start_time'] ) && ( $backup_options->options['finish_time'] != 0 ) && ( $backup_options->options['start_time'] != 0 ) ) {
			$total_time = ( $backup_options->options['finish_time'] - $backup_options->options['start_time'] ) . ' seconds';
		} else {
			$total_time = '<i>Unknown</i>';
		}
		$steps[] = array( 'Finish Time', $finish_time, '' );
		$steps[] = array(
			'<b>Total Overall Time</b>',
			$total_time,
			'',
		);
		
		$columns = array(
			__( 'Backup Steps', 'it-l10n-backupbuddy' ),
			__( 'Time', 'it-l10n-backupbuddy' ),
			__( 'Attempts', 'it-l10n-backupbuddy' ),
		);
		
		if ( count( $steps ) == 0 ) {
			_e( 'No step statistics were found for this backup.', 'it-l10n-backupbuddy' );
		} else {
			pb_backupbuddy::$ui->list_table(
				$steps,
				array(
					'columns'		=>	$columns,
					'css'			=>	'width: 100%; min-width: 200px;',
				)
			);
		}
		echo '<br><br>';
		//***** END STEPS.
		
		
		
		if ( isset( $backup_options->options['trigger'] ) ) {
			$trigger = $backup_options->options['trigger'];
		} else {
			$trigger = 'Unknown trigger';
		}
		$scanned = pb_backupbuddy::$format->date( pb_backupbuddy::$format->localize_time( $integrity['scan_time'] ) );
		echo ucfirst( $trigger ) . " backup {$integrity['file']} last scanned {$scanned}.";
		echo '<br><br><br>';
		
		echo '<a class="button secondary-button" onclick="jQuery(\'#pb_backupbuddy_advanced_debug\').slideToggle();">Display Advanced Debugging</a>';
		echo '<div id="pb_backupbuddy_advanced_debug" style="display: none;">';
		echo '<textarea style="width: 100%; height: 400px;" wrap="on">';
		echo print_r( $backup_options->options, true );
		echo '</textarea><br><br>';
		echo '</div><br><br>';
		
		
		pb_backupbuddy::$ui->ajax_footer();
		die();
		
	} // End integrity_status().
	
	
	
	/*	db_check()
	 *	
	 *	Check database integrity on a specific table. Used on server info page.
	 *	
	 *	@return		null
	 */
	public function db_check() {
		
		$table = base64_decode( pb_backupbuddy::_GET( 'table' ) );
		$check_level = 'MEDIUM';
		
		global $wpdb;
		
		pb_backupbuddy::$ui->ajax_header();
		echo '<h2>Database Table Check</h2>';
		echo 'Checking table `' . $table . '` using ' . $check_level . ' scan...<br><br>';
		$rows = $wpdb->get_results( "CHECK TABLE `" . backupbuddy_core::dbEscape( $table ) . "` " . $check_level, ARRAY_A );
		echo '<b>Results:</b><br><br>';
		echo '<table class="widefat">';
		foreach( $rows as $row ) {
			echo '<tr>';
			echo '<td>' . $row['Msg_type'] . '</td>';
			echo '<td>' . $row['Msg_text'] . '</td>';
			echo '</tr>';
		}
		unset( $rows );
		echo '</table>';
		pb_backupbuddy::$ui->ajax_footer();
		
		die();
		
	} // End db_check().
	
	
	
	/*	db_repair()
	 *	
	 *	Repair specific table. Used on server info page.
	 *	
	 *	@return		null
	 */
	public function db_repair() {
		
		$table = base64_decode( pb_backupbuddy::_GET( 'table' ) );
		
		global $wpdb;
		
		pb_backupbuddy::$ui->ajax_header();
		echo '<h2>Database Table Repair</h2>';
		echo 'Repairing table `' . $table . '`...<br><br>';
		$rows = $wpdb->get_results( "REPAIR TABLE `" . backupbuddy_core::dbEscape( $table ) . "`", ARRAY_A );
		echo '<b>Results:</b><br><br>';
		echo '<table class="widefat">';
		foreach( $rows as $row ) {
			echo '<tr>';
			echo '<td>' . $row['Msg_type'] . '</td>';
			echo '<td>' . $row['Msg_text'] . '</td>';
			echo '</tr>';
		}
		unset( $rows );
		echo '</table>';
		pb_backupbuddy::$ui->ajax_footer();
		
		die();
		
	} // End db_repair().
	
	
	
	/*	php_max_runtime_test()
	 *	
	 *	Tests the ACTUAL PHP maximum runtime of the server by echoing and logging to the status log the seconds elapsed.
	 *	
	 *	@param		int		$stop_time_limit		Time after which the test will stop if it is still running.
	 *	@return		null
	 */
	public function php_max_runtime_test() {
		
		$stop_time_limit = 240;
		pb_backupbuddy::set_greedy_script_limits(); // Crank it up for the test!
		
		$m = "# Starting BackupBuddy PHP Max Execution Time Tester. Determines what your ACTUAL limit is (usually shorter than the server reports so now you can find out the truth!). Stopping test if it gets to `{$stop_time_limit}` seconds. When your browser stops loading this page then the script has most likely timed out at your actual PHP limit.";
		pb_backupbuddy::status( 'details', $m );
		echo $m . "<br>\n";
		
		$t = 0; // Time = 0;
		while( $t < $stop_time_limit ) {
			
			pb_backupbuddy::status( 'details', 'Max PHP Execution Time Test status: ' . $t );
			echo $t . "<br>\n";
			//sleep( 1 );
			$now = time(); while ( time() < ( $now + 1 ) ) { true; }
			flush();
			$t++;
			
		}
		
		$m = '# Ending BackupBuddy PHP Max Execution Time The test was stopped as the test time limit of ' . $stop_time_limit . ' seconds.';
		pb_backupbuddy::status( 'details', $m );
		echo $m . "<br>\n";
		die();
	} // End php_max_runtime_test().
	
	
	
	public function disalert() {
		$unique_id = pb_backupbuddy::_POST( 'unique_id' );
		
		pb_backupbuddy::$options['disalerts'][$unique_id] = time();
		pb_backupbuddy::save();
		
		die('1');
		
	} // End disalert().
	
	
	
	public function importexport_settings() {
		pb_backupbuddy::load();
		pb_backupbuddy::$ui->ajax_header();
		
		if ( pb_backupbuddy::_POST( 'import_settings' ) != '' ) {
			$import = trim( stripslashes( pb_backupbuddy::_POST( 'import_data' ) ) );
			$import = base64_decode( $import );
			if ( $import === false ) { // decode failed.
				pb_backupbuddy::alert( 'Unable to decode settings data. Import aborted. Insure that you fully copied the settings and did not change any of the text.' );
			} else { // decode success.
				if ( ( $import = maybe_unserialize( $import ) ) === false ) { // unserialize fail.
					pb_backupbuddy::alert( 'Unable to unserialize settings data. Import aborted. Insure that you fully copied the settings and did not change any of the text.' );
				} else { // unserialize success.
					if ( !isset( $import['data_version'] ) ) { // missing expected content.
						pb_backupbuddy::alert( 'Unserialized settings data but it did not contain expected data. Import aborted. Insure that you fully copied the settings and did not change any of the text.' );
					} else { // contains expected content.
						pb_backupbuddy::$options = $import;
						require_once( pb_backupbuddy::plugin_path() . '/controllers/activation.php' ); // Run data migration to upgrade if needed.
						pb_backupbuddy::save();
						pb_backupbuddy::alert( 'Provided settings successfully imported. Prior settings overwritten.' );
					}
				}
			}
		}
		
		echo '<h2>Export BackupBuddy Settings</h2>';
		echo 'Copy the encoded plugin settings below and paste it into the destination BackupBuddy Settings Import page.<br><br>';
		echo '<textarea style="width: 100%; height: 100px;" wrap="on">';
		echo base64_encode( serialize( pb_backupbuddy::$options ) );
		echo '</textarea>';
		
		echo '<br><br><br>';
		
		echo '<h2>Import BackupBuddy Settings</h2>';
		echo 'Paste encoded plugin settings below to import & replace current settings.  If importing settings from an older version and errors are encountered please deactivate and reactivate the plugin.<br><br>';
		echo '<form method="post" action="' . pb_backupbuddy::ajax_url( 'importexport_settings' ) . '">';
		echo '<textarea style="width: 100%; height: 100px;" wrap="on" name="import_data"></textarea>';
		echo '<br><br><input type="submit" name="import_settings" value="Import Settings" class="button button-primary">';
		echo '</form>';
		
		pb_backupbuddy::$ui->ajax_footer();
		die();
	} // End importexport_settings().
	
	
	
	/* refresh_zip_methods()
	 *
	 * Server Info page refreshing available zip methods. Useful since these are normally cached.
	 *
	 */
	public function refresh_zip_methods() {
	
		// Make sure the legacy method transient is gone
		delete_transient( 'pb_backupbuddy_avail_zip_methods_classic' );
		
		if ( !isset( pb_backupbuddy::$classes['zipbuddy'] ) ) {

			// We don't have an instance of zipbuddy so make sure we can create one
			require_once( pb_backupbuddy::plugin_path() . '/lib/zipbuddy/zipbuddy.php' );
			
			// Find out the transient name(s) and delete them
			$transients = pluginbuddy_zipbuddy::get_transient_names_static();
			foreach ( $transients as $transient ) {
			
				delete_transient( $transient );
				
			}
			
			// Instantiating a class object will renew the deleted method transient
			pb_backupbuddy::$classes['zipbuddy'] = new pluginbuddy_zipbuddy( ABSPATH );
			
		} else {
		
			// We have an instance of zipbuddy so we can use it
			// Find out the transient name(s) and delete them
			$transients = pluginbuddy_zipbuddy::get_transient_names_static();
			foreach ( $transients as $transient ) {
			
				delete_transient( $transient );
				
			}
			
			// Just call the refresh function
			pb_backupbuddy::$classes['zipbuddy']->refresh_zip_methods();
			
		}
		
		// Now simply provide the list of methods
		echo implode( ', ', pb_backupbuddy::$classes['zipbuddy']->_zip_methods );
		
		die();
	} // End refresh_zip_methods().
	
	
	
	/* site_size_listing()
	 *
	 * Display site site listing on Server Info page.
	 *
	 */
	public function site_size_listing() {
		
		
		$profile_id = 0;
		if ( is_numeric( pb_backupbuddy::_GET( 'profile' ) ) ) {
			if ( isset( pb_backupbuddy::$options['profiles'][ pb_backupbuddy::_GET( 'profile' ) ] ) ) {
				$profile_id = pb_backupbuddy::_GET( 'profile' );
				pb_backupbuddy::$options['profiles'][ pb_backupbuddy::_GET( 'profile' ) ] = array_merge( pb_backupbuddy::settings( 'profile_defaults' ), pb_backupbuddy::$options['profiles'][ pb_backupbuddy::_GET( 'profile' ) ] ); // Set defaults if not set.
			} else {
				pb_backupbuddy::alert( 'Error #45849458b: Invalid profile ID number `' . htmlentities( pb_backupbuddy::_GET( 'profile' ) ) . '`. Displaying with default profile.', true );
			}
		}
		
		echo '<!-- profile: ' . $profile_id . ' -->';
		
		$exclusions = backupbuddy_core::get_directory_exclusions( pb_backupbuddy::$options['profiles'][ $profile_id ] );
		
		$result = pb_backupbuddy::$filesystem->dir_size_map( ABSPATH, ABSPATH, $exclusions, $dir_array );
		if ( 0 == $result ) {
			pb_backupbuddy::alert( 'Error #5656653. Unable to access directory map listing for directory `' . ABSPATH . '`.' );
			die();
		}
		$total_size = pb_backupbuddy::$options['stats']['site_size'] = $result[0];
		$total_size_excluded = pb_backupbuddy::$options['stats']['site_size_excluded'] = $result[1];
		pb_backupbuddy::$options['stats']['site_size_updated'] = time();
		pb_backupbuddy::save();
		
		arsort( $dir_array );
		
		if ( pb_backupbuddy::_GET( 'text' ) == 'true' ) {
			pb_backupbuddy::$ui->ajax_header();
			echo '<h3>' . __( 'Site Size Listing & Exclusions', 'it-l10n-backupbuddy' ) . '</h3>';
			echo '<textarea style="width:100%; height: 300px; font-family: monospace;" wrap="off">';
			echo __('Size + Children', 'it-l10n-backupbuddy' ) . "\t";
			echo __('- Exclusions', 'it-l10n-backupbuddy' ) . "\t";
			echo __('Directory', 'it-l10n-backupbuddy' ) . "\n";
		} else {
			?>
			<style>
				.backupbuddy_sizemap_table th {
					white-space: nowrap;
				}
				.backupbuddy_sizemap_table td {
					word-break: break-all;
				}
			</style>
			<table class="widefat backupbuddy_sizemap_table">
				<thead>
					<tr class="thead">
						<?php
							echo '<th>', __('Directory', 'it-l10n-backupbuddy' ), '</th>',
								 '<th>', __('Size with Children', 'it-l10n-backupbuddy' ), '</th>',
								 '<th>', __('Size with Exclusions', 'it-l10n-backupbuddy' ), '<br><span class="description">Global defaults profile</span></th>';
						?>
					</tr>
				</thead>
				<tfoot>
					<tr class="thead">
						<?php
							echo '<th>', __('Directory', 'it-l10n-backupbuddy' ), '</th>',
								 '<th>', __('Size with Children', 'it-l10n-backupbuddy' ), '</th>',
								 '<th>', __('Size with Exclusions', 'it-l10n-backupbuddy' ), '<br><span class="description">Global defaults profile</span></th>';
						?>
					</tr>
				</tfoot>
				<tbody>
			<?php
		}
		if ( pb_backupbuddy::_GET( 'text' ) == 'true' ) {
				echo str_pad( pb_backupbuddy::$format->file_size( $total_size ), 10, ' ', STR_PAD_RIGHT ) . "\t" . str_pad( pb_backupbuddy::$format->file_size( $total_size_excluded ), 10, ' ', STR_PAD_RIGHT ) . "\t" . __( 'TOTALS', 'it-l10n-backupbuddy' ) . "\n";
		} else {
			echo '<tr><td align="right"><b>' . __( 'TOTALS', 'it-l10n-backupbuddy' ) . ':</b></td><td><b>' . pb_backupbuddy::$format->file_size( $total_size ) . '</b></td><td><b>' . pb_backupbuddy::$format->file_size( $total_size_excluded ) . '</b></td></tr>';
		}
		$item_count = 0;
		foreach ( $dir_array as $id => $item ) { // Each $item is in format array( TOTAL_SIZE, TOTAL_SIZE_TAKING_EXCLUSIONS_INTO_ACCOUNT );
			$item_count++;
			if ( $item_count > 100 ) {
				flush();
				$item_count = 0;
			}
			if ( $item[1] === false ) {
				if ( pb_backupbuddy::_GET( 'text' ) == 'true' ) {
					$excluded_size = 'EXCLUDED';
					echo '**';
				} else {
					$excluded_size = '<span class="pb_label pb_label-important">Excluded</span>';
					echo '<tr style="background: #fcc9c9;">';
				}
			} else {
				$excluded_size = pb_backupbuddy::$format->file_size( $item[1] );
				if ( pb_backupbuddy::_GET( 'text' ) != 'true' ) {
					echo '<tr>';
				}
			}
			if ( pb_backupbuddy::_GET( 'text' ) == 'true' ) {
				echo str_pad( pb_backupbuddy::$format->file_size( $item[0] ), 10, ' ', STR_PAD_RIGHT ) . "\t" . str_pad( $excluded_size, 10, ' ', STR_PAD_RIGHT ) . "\t" . $id . "\n";
			} else {
				echo '<td>' . $id . '</td><td>' . pb_backupbuddy::$format->file_size( $item[0] ) . '</td><td>' . $excluded_size . '</td></tr>';
			}
		}
		if ( pb_backupbuddy::_GET( 'text' ) == 'true' ) {
				echo str_pad( pb_backupbuddy::$format->file_size( $total_size ), 10, ' ', STR_PAD_RIGHT ) . "\t" . str_pad( pb_backupbuddy::$format->file_size( $total_size_excluded ), 10, ' ', STR_PAD_RIGHT ) . "\t" . __( 'TOTALS', 'it-l10n-backupbuddy' ) . "\n";
		} else {
			echo '<tr><td align="right"><b>' . __( 'TOTALS', 'it-l10n-backupbuddy' ) . ':</b></td><td><b>' . pb_backupbuddy::$format->file_size( $total_size ) . '</b></td><td><b>' . pb_backupbuddy::$format->file_size( $total_size_excluded ) . '</b></td></tr>';
		}
		if ( pb_backupbuddy::_GET( 'text' ) == 'true' ) {
			echo "\n\nEXCLUSIONS (" . count( $exclusions ) . "):" . "\n" . implode( "\n", $exclusions );
			echo '</textarea>';
			pb_backupbuddy::$ui->ajax_footer();
		} else {
			echo '</tbody>';
			echo '</table>';
			
			echo '<br>';
			echo 'Exclusions (' . count( $exclusions ) . ')';
			pb_backupbuddy::tip( 'List of directories that will be excluded in an actual backup. This includes user-defined directories and BackupBuddy directories such as the archive directory and temporary directories.' );
			echo '<div id="pb_backupbuddy_serverinfo_exclusions" style="background-color: #EEEEEE; padding: 4px; float: right; white-space: nowrap; height: 90px; width: 70%; min-width: 400px; overflow: auto;"><i>' . implode( "<br>", $exclusions ) . '</i></div>';
			echo '<br style="clear: both;">';
			echo '<br><br><center>';
			echo '<a href="' . pb_backupbuddy::ajax_url( 'site_size_listing' ) . '&text=true&#038;TB_iframe=1&#038;width=640&#038;height=600" class="thickbox button secondary-button">' . __( 'Display Directory Size Listing in Text Format', 'it-l10n-backupbuddy' ) . '</a>';
			echo '</center>';
		}
		die();
		
	} // End site_size_listing().
	
	
	
	function stop_backup() {
		
		$serial = pb_backupbuddy::_POST( 'serial' );
		set_transient( 'pb_backupbuddy_stop_backup-' . $serial, true, ( 60*60*24 ) );
		
		die( '1' );
		
	} // End stop_backup().
	
	
	
	function quickstart_stash_test() {
		die( 'Not yet implemented.' );
	} // End quickstart_stash_test().
	
	
	
	/* quickstart()
	 *
	 * Display quickstart form in a modal.
	 *
	 */
	function quickstart() {
		pb_backupbuddy::$ui->ajax_header();
		pb_backupbuddy::load_style( 'thickboxed.css' );
		require_once( pb_backupbuddy::plugin_path() . '/views/_quicksetup.php' );
		pb_backupbuddy::$ui->ajax_footer();
		die();
	} // End quickstart().
	
	
	
	/* quickstart_form()
	 *
	 * Saving Quickstart form.
	 *
	 */
	function quickstart_form() {
		
		$errors = array();
		$form = pb_backupbuddy::_POST();
		//print_r( $form );
		
		if ( ( '' != $form['email'] ) && ( false !== stristr( $form['email'], '@' ) ) ) {
			pb_backupbuddy::$options['email_notify_error'] = strip_tags( $form['email'] );
		} else {
			$errors[] = 'Invalid email address.';
		}
		
		if ( ( '' != $form['password'] ) && ( $form['password'] == $form['password_confirm'] ) ) {
			pb_backupbuddy::$options['importbuddy_pass_hash'] = md5( $form['password'] );
			pb_backupbuddy::$options['importbuddy_pass_length'] = strlen( $form['password'] );
		} elseif ( '' == $form['password'] ) {
			$errors[] = 'Please enter a password for restoring / migrating.';
		} else {
			$errors[] = 'Passwords do not match.';
		}
		
		if ( '' != $form['schedule'] ) {
			$destination_id = '';
			if ( '' != $form['destination_id'] ) { // Dest id explicitly set.
				$destination_id = $form['destination_id'];
			} else { // No explicit destination ID; deduce it.
				if ( '' != $form['destination'] ) {
					foreach( pb_backupbuddy::$options['remote_destinations'] as $destination_index => $destination ) { // Loop through ending with the last created destination of this type.
						if ( $destination['type'] == $form['destination'] ) {
							$destination_id = $destination_index;
						}
					}
				}
			}
			
			function pb_backupbuddy_schedule_exist_by_title( $title ) {
				foreach( pb_backupbuddy::$options['schedules'] as $schedule ) {
					if ( $schedule['title'] == $title ) {
						return true;
					}
				}
				return false;
			}
			
			// STARTER
			if ( 'starter' == $form['schedule'] ) {
				
				$title = 'Weekly Database (Quick Setup - Starter)';
				if ( false === pb_backupbuddy_schedule_exist_by_title( $title ) ) {
					$add_response = backupbuddy_core::add_backup_schedule(
						$title,
						$profile = '1',
						$interval = 'weekly',
						$first_run = ( time() + ( get_option( 'gmt_offset' ) * 3600 ) + 86400 ),
						$remote_destinations = array( $destination_id )
					);
					if ( true !== $add_response ) { $errors[] = $add_response; }
				}
				
				$title = 'Monthly Full (Quick Setup - Starter)';
				if ( false === pb_backupbuddy_schedule_exist_by_title( $title ) ) {
					$add_response = backupbuddy_core::add_backup_schedule(
						$title,
						$profile = '2',
						$interval = 'monthly',
						$first_run = ( time() + ( get_option( 'gmt_offset' ) * 3600 ) + 86400 + 18000 ),
						$remote_destinations = array( $destination_id )
					);
					if ( true !== $add_response ) { $errors[] = $add_response; }
				}
				
			}
			
			// BLOGGER
			if ( 'blogger' == $form['schedule'] ) {
				
				$title = 'Daily Database (Quick Setup - Blogger)';
				if ( false === pb_backupbuddy_schedule_exist_by_title( $title ) ) {
					$add_response = backupbuddy_core::add_backup_schedule(
						$title,
						$profile = '1',
						$interval = 'daily',
						$first_run = ( time() + ( get_option( 'gmt_offset' ) * 3600 ) + 86400 ),
						$remote_destinations = array( $destination_id )
					);
					if ( true !== $add_response ) { $errors[] = $add_response; }
				}
				
				$title = 'Weekly Full (Quick Setup - Blogger)';
				if ( false === pb_backupbuddy_schedule_exist_by_title( $title ) ) {
					$add_response = backupbuddy_core::add_backup_schedule(
						$title,
						$profile = '2',
						$interval = 'weekly',
						$first_run = ( time() + ( get_option( 'gmt_offset' ) * 3600 ) + 86400 + 18000 ),
						$remote_destinations = array( $destination_id )
					);
					if ( true !== $add_response ) { $errors[] = $add_response; }
				}
				
			}
			
			
		} // end set schedule.
		
		
		if ( 0 == count( $errors ) ) {
			pb_backupbuddy::save();
			die( 'Success.' );
		} else {
			die( implode( "\n", $errors ) );
		}
		
	} // End quickstart_form().
	
	
	
	/* profile_settings()
	 *
	 * View a specified profile's settings.
	 *
	 */
	function profile_settings() {
		
		pb_backupbuddy::$ui->ajax_header();
		require_once( pb_backupbuddy::plugin_path() . '/views/settings/_includeexclude.php' );
		pb_backupbuddy::$ui->ajax_footer();
		die();
		
	} // End profile_settings().
	
	
	
	/* restore_file_view()
	 *
	 * View contents of a file (text) that is inside a zip archive.
	 *
	 */
	function restore_file_view() {
		
		pb_backupbuddy::$ui->ajax_header( true, false ); // js, no padding
		
		$archive_file = pb_backupbuddy::_GET( 'archive' ); // archive to extract from.
		$file = pb_backupbuddy::_GET( 'file' ); // file to extract.
		$serial = backupbuddy_core::get_serial_from_file( $archive_file ); // serial of archive.
		$temp_file = uniqid(); // temp filename to extract into.
		
		require_once( pb_backupbuddy::plugin_path() . '/lib/zipbuddy/zipbuddy.php' );
		$zipbuddy = new pluginbuddy_zipbuddy( backupbuddy_core::getBackupDirectory() );
		
		// Calculate temp directory & lock it down.
		$temp_dir = get_temp_dir();
		$destination = $temp_dir . 'backupbuddy-' . $serial;
		if ( ( ( ! file_exists( $destination ) ) && ( false === mkdir( $destination ) ) ) ) {
			$error = 'Error #458485945: Unable to create temporary location.';
			pb_backupbuddy::status( 'error', $error );
			die( $error );
		}
		
		// If temp directory is within webroot then lock it down.
		$temp_dir = str_replace( '\\', '/', $temp_dir ); // Normalize for Windows.
		$temp_dir = rtrim( $temp_dir, '/\\' ) . '/'; // Enforce single trailing slash.
		if ( FALSE !== stristr( $temp_dir, ABSPATH ) ) { // Temp dir is within webroot.
			pb_backupbuddy::anti_directory_browsing( $destination );
		}
		unset( $temp_dir );
		
		$message = 'Extracting "' . $file . '" from archive "' . $archive_file . '" into temporary file "' . $destination . '". ';
		echo '<!-- ';
		pb_backupbuddy::status( 'details', $message );
		echo $message;
		
		
		$extractions = array( $file => $temp_file );
		$extract_result = $zipbuddy->extract( backupbuddy_core::getBackupDirectory() . $archive_file, $destination, $extractions );
		if ( false === $extract_result ) { // failed.
			echo ' -->';
			$error = 'Error #584984458. Unable to extract.';
			pb_backupbuddy::status( 'error', $error );
			die( $error );
		} else { // success.
			_e( 'Success.', 'it-l10n-backupbuddy' );
			echo ' -->';
			?>
			<textarea readonly="readonly" wrap="off" style="width: 100%; min-height: 175px; height: 100%; margin: 0;"><?php echo file_get_contents( $destination . '/' . $temp_file ); ?></textarea>
			<?php
			//unlink( $destination . '/' . $temp_file );
		}
		
		// Try to cleanup.
		if ( file_exists( $destination ) ) {
			if ( false === pb_backupbuddy::$filesystem->unlink_recursive( $destination ) ) {
				pb_backupbuddy::status( 'details', 'Unable to delete temporary holding directory `' . $destination . '`.' );
			} else {
				pb_backupbuddy::status( 'details', 'Cleaned up temporary files.' );
			}
		}
		
		pb_backupbuddy::$ui->ajax_footer();
		die();
		
	} // End restore_file_view().
	
	
	
	/* restore_file_restore()
	 *
	 * AJAX page for thickbox for restoring a file from inside an archive..
	 *
	 */
	public function restore_file_restore() {
		
		$files = pb_backupbuddy::_GET( 'files' ); // file to extract.
		$files_array = explode( ',', $files );
		$files = array();
		foreach( $files_array as $file ) {
			if ( substr( $file, -1 ) == '/' ) { // If directory then add wildcard.
				$file = $file . '*';
			}
			$files[$file] = $file;
		}
		unset( $files_array );
		
		pb_backupbuddy::$ui->ajax_header( true, false ); // js, no padding
		?>
		
		<script type="text/javascript">
			function pb_status_append( status_string ) {
				target_id = 'pb_backupbuddy_status'; // importbuddy_status or pb_backupbuddy_status
				if( jQuery( '#' + target_id ).length == 0 ) { // No status box yet so suppress.
					return;
				}
				jQuery( '#' + target_id ).append( "\n" + status_string );
				textareaelem = document.getElementById( target_id );
				textareaelem.scrollTop = textareaelem.scrollHeight;
			}
		</script>
		<?php
		$success = false;

		global $pb_backupbuddy_js_status;
		$pb_backupbuddy_js_status = true;
		echo pb_backupbuddy::status_box( 'Restoring . . .' );
		echo '<div id="pb_backupbuddy_working" style="width: 100px;"><br><center><img src="' . pb_backupbuddy::plugin_url() . '/images/working.gif" title="Working... Please wait as this may take a moment..."></center></div>';

		pb_backupbuddy::set_status_serial( 'restore' );
		global $wp_version;
		pb_backupbuddy::status( 'details', 'BackupBuddy v' . pb_backupbuddy::settings( 'version' ) . ' using WordPress v' . $wp_version . ' on ' . PHP_OS . '.' );
		
		
		$archive_file = pb_backupbuddy::_GET( 'archive' ); // archive to extract from.
		require( pb_backupbuddy::plugin_path() . '/classes/_restoreFiles.php' );
		$result = backupbuddy_restore_files::restore( backupbuddy_core::getBackupDirectory() . $archive_file, $files, $finalPath = ABSPATH );
		
		echo '<script type="text/javascript">jQuery("#pb_backupbuddy_working").hide();</script>';
		pb_backupbuddy::flush();
		if ( false === $result ) {
			
		} else {
		}
		pb_backupbuddy::$ui->ajax_footer();
		
		pb_backupbuddy::$ui->ajax_footer();
		die();
		
	} // End restore_file_restore().
	
	
	
	/* email_error_test()
	 *
	 * Test sending emails on the Settings page. Tries sending email and dies with "1" on success, else error message string echo'd out and dies.
	 *
	 */
	function email_error_test() {
		
		$email = pb_backupbuddy::_POST( 'email' );
		if ( $email == '' ) {
			die( 'You must supply an Error email address to send test message to.' );
		}
		backupbuddy_core::mail_error( 'THIS IS ONLY A TEST. This is a test of the Error Notification email.', $email );
		die('1');
		
	} // End email_error_test().
	
	
	
	/* remotesend_details()
	 *
	 * View log for a remote destination file transfer. Outputs HTML and information and die()'s.
	 *
	 */
	function remotesend_details() {
		
		$send_id = pb_backupbuddy::_GET( 'send_id' );
		$send_id = str_replace( '/\\', '', $send_id );
		
		pb_backupbuddy::$ui->ajax_header();
		
		$log_file = backupbuddy_core::getLogDirectory() . 'status-remote_send-' . $send_id . '_' . pb_backupbuddy::$options['log_serial'] . '.txt';
		if ( ! file_exists( $log_file ) ) {
			die( 'Error #4438958945985: Unable to read log file `' . $log_file . '`.' );
		}
		
		// Display log.
		echo '<h3>Remote Send Log</h3>';
		echo '<textarea style="width: 100%; height: 80%;" wrap="off">';
		$lines = file_get_contents( $log_file );
		if ( false === $lines ) {
			echo 'Error #849834: Unable to read log file `' . $log_file . '`.';
		} else {
			$lines = explode( "\n", $lines );
			foreach( (array)$lines as $line ) {
				$line = json_decode( $line, true );
				//print_r( $line );
				if ( is_array( $line ) ) {
					$u = '';
					if ( isset( $line['u'] ) ) { // As off v4.2.15.6. TODO: Remove this in a couple of versions once old logs without this will have cycled out.
						$u = '.' . $line['u'];
					}
					echo pb_backupbuddy::$format->date( $line['time'], 'G:i:s' ) . $u . "\t\t";
					echo $line['run'] . "sec\t";
					echo $line['mem'] . "MB\t";
					echo $line['event'] . "\t";
					echo $line['data'] . "\n";
				}
			}
		}
		echo '</textarea>';
		
		pb_backupbuddy::$ui->ajax_footer();
		die();
		
	} // End remotesend_details().
	
	
	
	/* remotesend_abort()
	 *
	 * Abort an in-progress demote destination file transfer. Dies with outputting "1" on success.
	 *
	 */
	function remotesend_abort() {
		
		$send_id = pb_backupbuddy::_GET( 'send_id' );
		$send_id = str_replace( '/\\', '', $send_id );
		
		pb_backupbuddy::status( 'details', 'About to load fileoptions data.' );
		require_once( pb_backupbuddy::plugin_path() . '/classes/fileoptions.php' );
		$fileoptions_obj = new pb_backupbuddy_fileoptions( backupbuddy_core::getLogDirectory() . 'fileoptions/send-' . $send_id . '.txt', $read_only = false, $ignore_lock = true, $create_file = false );
		if ( true !== ( $result = $fileoptions_obj->is_ok() ) ) {
			pb_backupbuddy::status( 'error', __('Fatal Error #9034.324544. Unable to access fileoptions data.', 'it-l10n-backupbuddy' ) . ' Error: ' . $result );
			return false;
		}
		pb_backupbuddy::status( 'details', 'Fileoptions data loaded.' );
		$fileoptions = &$fileoptions_obj->options;
		
		$fileoptions['status'] = 'aborted';
		$fileoptions_obj->save();
		
		die( '1' );
		
	} // End remotesend_abort().
	
	
	
	/* destination_ftp_pathpicker()
	 *
	 * description
	 *
	 */
	function destination_ftp_pathpicker() {
		
		function pb_backupbuddy_ftp_listDetailed($resource, $directory = '.') { 
			if (is_array($children = @ftp_rawlist($resource, $directory))) { 
			    $items = array(); 

			    foreach ($children as $child) { 
			        $chunks = preg_split("/\s+/", $child); 
			        list($item['rights'], $item['number'], $item['user'], $item['group'], $item['size'], $item['month'], $item['day'], $item['time']) = $chunks; 
			        $item['type'] = $chunks[0]{0} === 'd' ? 'directory' : 'file'; 
			        array_splice($chunks, 0, 8); 
			        $items[implode(" ", $chunks)] = $item; 
			    } 

			    return $items; 
			} 
			return false;
		} // end listDetailed subfunction.
		
		
		$settings = array(
			'address'		=> pb_backupbuddy::_GET( 'pb_backupbuddy_address' ),
			'username'		=> pb_backupbuddy::_GET( 'pb_backupbuddy_username' ),
			'password'		=> pb_backupbuddy::_GET( 'pb_backupbuddy_password' ),
			'ftps'			=> pb_backupbuddy::_GET( 'pb_backupbuddy_ftps' ),
			'active_mode'	=> pb_backupbuddy::_GET( 'pb_backupbuddy_active_mode' ),
		);
		
		if ( ( $settings['address'] == '' ) || ( $settings['username'] == '' ) || ( $settings['password'] == '' ) ) {
			die( __('Missing required FTP server inputs.', 'it-l10n-backupbuddy' ) );
		}
		
		// Settings
		if ( $settings['active_mode'] == '0' ) {
			$active_mode = false;
		} else {
			$active_mode = true;
		}
		$server = $settings['address'];
		$port = '21';
		if ( strstr( $server, ':' ) ) {
			$server_params = explode( ':', $server );
			
			$server = $server_params[0];
			$port = $server_params[1];
		}
		
		// Connect.
		if ( $settings['ftps'] == '0' ) {
			$conn_id = @ftp_connect( $server, $port, 10 ); // timeout of 10 seconds.
			if ( $conn_id === false ) {
				$error = __( 'Unable to connect to FTP address `' . $server . '` on port `' . $port . '`.', 'it-l10n-backupbuddy' );
				$error .= "\n" . __( 'Verify the server address and port (default 21). Verify your host allows outgoing FTP connections.', 'it-l10n-backupbuddy' );
				die( $error );
			}
		} else {
			if ( function_exists( 'ftp_ssl_connect' ) ) {
				$conn_id = @ftp_ssl_connect( $server, $port );
				if ( $conn_id === false ) {
					die( __('Destination server does not support FTPS?', 'it-l10n-backupbuddy' ) );
				}
			} else {
				die( __('Your web server doesnt support FTPS.', 'it-l10n-backupbuddy' ) );
			}
		}
		
		// Authenticate.
		$login_result = @ftp_login( $conn_id, $settings['username'], $settings['password'] );
		if ( ( !$conn_id ) || ( !$login_result ) ) {
			pb_backupbuddy::status( 'details', 'FTP test: Invalid user/pass.' );
			$response = __('Unable to login to FTP server. Bad user/pass.', 'it-l10n-backupbuddy' );
			if ( $settings['ftps'] != '0' ) {
				$response .= "\n\nNote: You have FTPs enabled. You may get this error if your host does not support encryption at this address/port.";
			}
			die( $response );
		}
		
		pb_backupbuddy::status( 'details', 'FTP test: Success logging in.' );
		
		// Handle active/pasive mode.
		if ( $active_mode === true ) { // do nothing, active is default.
			pb_backupbuddy::status( 'details', 'Active FTP mode based on settings.' );
		} elseif ( $active_mode === false ) { // Turn passive mode on.
			pb_backupbuddy::status( 'details', 'Passive FTP mode based on settings.' );
			ftp_pasv( $conn_id, true );
		} else {
			pb_backupbuddy::status( 'error', 'Unknown FTP active/passive mode: `' . $active_mode . '`.' );
		}
		
		// Calculate root.
		$ftpRoot = urldecode( pb_backupbuddy::_POST( 'dir' ) );
		if ( '' == $ftpRoot ) { // No root passed so figure out root from FTP server itself.
			$ftpRoot = ftp_pwd( $conn_id );
		}
		
		
		$ftpList = pb_backupbuddy_ftp_listDetailed( $conn_id, $ftpRoot );
		
		
		echo '<ul class="jqueryFileTree pb_backupbuddy_ftpdestination_pathpickerboxtree">';
		if ( count( $ftpList ) > 2 ) {
			foreach( $ftpList as $fileName => $file ) {
				if ( ( '.' == $fileName ) || ( '..' == $fileName ) ) {
					continue;
				}
				if ( 'directory' == $file['type'] ) { // Directory.
					echo '<li class="directory collapsed">';
					$return = '';
					$return .= '<div class="pb_backupbuddy_treeselect_control">';
					$return .= '<img src="' . pb_backupbuddy::plugin_url() . '/images/greenplus.png" style="vertical-align: -3px;" title="Select this path..." class="pb_backupbuddy_filetree_select">';
					$return .= '</div>';
					echo '<a href="#" rel="' . htmlentities( $ftpRoot . $fileName ) . '/" title="Toggle expand...">' . htmlentities($fileName) . $return . '</a>';
					echo '</li>';
				} else { // File.
					echo '<li class="file collapsed">';
					echo '<a href="#" rel="' . htmlentities( $ftpRoot . $fileName ) . '">' . htmlentities($fileName) . '</a>';
					echo '</li>';
				}
			}
		} else {
			echo '<ul class="jqueryFileTree">';
			echo '<li><a href="#" rel="' . htmlentities( pb_backupbuddy::_POST( 'dir' ) . 'NONE' ) . '"><i>Empty Directory ...</i></a></li>';
			echo '</ul>';
		}
		echo '</ul>';
		
		die();
		
	} // End destination_ftp_pathpicker().
	
	
	
	/* rollback()
	 *
	 * Displayed in page by iframe via _rollback.php
	 * Expects GET variables:
	 * 		step		Numeric step number to run.
	 * 		archive		Zip archive filename (basename only).
	 *
	 */
	public function rollback() {
		pb_backupbuddy::$ui->ajax_header();
		pb_backupbuddy::load_script( 'jquery' );
		echo '<div id="pb_backupbuddy_working" style="width: 100px; margin-bottom: 30px;"><br><center><img src="' . pb_backupbuddy::plugin_url() . '/images/working.gif" title="Working... Please wait as this may take a moment..."></center></div>';
		?>
		
		
		<script>
		function pb_status_append( status_string ) {
			var win = window.dialogArguments || opener || parent || top;
			win.pb_status_append( status_string );
		}
		function pb_status_undourl( undo_url ) {
			var win = window.dialogArguments || opener || parent || top;
			win.pb_status_undourl( undo_url );
		}
		
		var win = window.dialogArguments || opener || parent || top;
		win.window.scrollTo(0,0);
		</script>
		
		
		<?php
		global $pb_backupbuddy_js_status;
		$pb_backupbuddy_js_status = true;
		pb_backupbuddy::set_status_serial( 'restore' );
		
		
		$step = strip_tags( pb_backupbuddy::_GET( 'step' ) );
		if ( ( '' == $step ) || ( ! is_numeric( $step ) ) ) {
			$step = 0;
		}
		$backupFile = strip_tags( pb_backupbuddy::_GET( 'archive' ) );
		if ( '' == $backupFile ) {
			pb_backupbuddy::alert( 'The backup file to restore from must be specified.' );
			die();
		}
		$stepFile = pb_backupbuddy::plugin_path() . '/controllers/pages/rollback/_step' . $step . '.php';
		if ( ! file_exists( $stepFile ) ) {
			pb_backupbuddy::alert( 'Error #849743. Invalid roll back step `' . htmlentities( pb_backupbuddy::_GET( 'step' ) ) . '` (' . $step . ').' );
			die();
		}
		require( $stepFile );
		
		echo '<br><br><br>';
		echo '<script type="text/javascript">jQuery("#pb_backupbuddy_working").hide();</script>';
		pb_backupbuddy::$ui->ajax_footer();
		pb_backupbuddy::flush();
		
		die();
	} // End rollback().
	
	
	
	/* deploy()
	 *
	 * Displayed in page by iframe via deploy.php
	 * Expects GET variables:
	 * 		step		Step file to run.
	 *
	 */
	public function deploy() {
		pb_backupbuddy::$ui->ajax_header();
		pb_backupbuddy::load_script( 'jquery' );
		echo '<div id="pb_backupbuddy_working" style="width: 100px; margin-bottom: 30px;"><br><center><img src="' . pb_backupbuddy::plugin_url() . '/images/working.gif" title="Working... Please wait as this may take a moment..."></center></div>';
		?>
		
		
		<script>
		function pb_status_append( status_string ) {
			var win = window.dialogArguments || opener || parent || top;
			win.pb_status_append( status_string );
		}
		function pb_status_undourl( undo_url ) {
			var win = window.dialogArguments || opener || parent || top;
			win.pb_status_undourl( undo_url );
		}
		
		var win = window.dialogArguments || opener || parent || top;
		win.window.scrollTo(0,0);
		</script>
		
		
		<?php
		global $pb_backupbuddy_js_status;
		$pb_backupbuddy_js_status = true;
		pb_backupbuddy::set_status_serial( 'deploy' );
		
		
		$step = strip_tags( pb_backupbuddy::_GET( 'step' ) );
		if ( ! ctype_alnum( $step ) ) {
			die( 'Error #8549845: Invalid step `' . htmlentities( $step ) . '`.' );
		}
		$stepFile = pb_backupbuddy::plugin_path() . '/controllers/pages/deploy/_' . $step . '.php';
		
		if ( ! file_exists( $stepFile ) ) {
			pb_backupbuddy::alert( 'Error #3298238. Invalid deploy step `' . htmlentities( pb_backupbuddy::_GET( 'step' ) ) . '` (' . $step . ').' );
			die();
		}
		require( $stepFile );
		
		echo '<br><br><br>';
		echo '<script type="text/javascript">jQuery("#pb_backupbuddy_working").hide();</script>';
		pb_backupbuddy::$ui->ajax_footer();
		pb_backupbuddy::flush();
		
		die();
	} // End rollback().
	
	
	
	/* getMainLog()
	 *
	 * Dump out contents of the main log file.
	 *
	 */
	function getMainLog() {
		$log_file = backupbuddy_core::getLogDirectory() . 'log-' . pb_backupbuddy::$options['log_serial'] . '.txt';
		if ( file_exists( $log_file ) ) {
			readfile( $log_file );
		} else {
			echo __('Nothing has been logged.', 'it-l10n-backupbuddy' );
		}
		die();
	} // End getMainLog().
	
	
	// Access credentials _MUST_ always be checked before allowing any access whatsoever.
	function api() {
		die('0');
		
		// TODO: Internal security lockout.
		if ( ( ! isset( pb_backupbuddy::$options['api_key_test'] ) ) || ( pb_backupbuddy::$options['api_key_test'] == '' ) ) {
			die('0');
		}
		if ( pb_backupbuddy::$options['api_key_test'] != 'dsnfilasbfisybfdjybfjalybsfaklsbfa' ) {
			die('0');
		}
		
		$run = pb_backupbuddy::_POST( 'run' );
		// TODO: TESTING temp allow GET method:
		if ( '' == $run ) {
			$run = pb_backupbuddy::_GET( 'run' );
		}
		
		if ( '' == $run ) {
			die( json_encode( array( 'success' => false, 'error' => 'Error #489384: Missing run command.' ) ) );
		} else {
			if ( false === ( $return = call_user_func(  'backupbuddy_api::' . $run ) ) ) {
				die( json_encode( array( 'success' => false, 'error' => 'Error #328983: Command failed.' ) ) );
			} else {
				die( json_encode( array( 'success' => true, 'version' => pb_backupbuddy::settings( 'version' ), 'data' => $return ) ) );
			}
		}
		
		die();
	} // end api().
	
} // end class.
?>
