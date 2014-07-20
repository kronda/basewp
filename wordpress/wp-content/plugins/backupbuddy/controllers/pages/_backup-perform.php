<?php
// Assets
wp_enqueue_script( 'thickbox' );
wp_print_scripts( 'thickbox' );
wp_print_styles( 'thickbox' );
pb_backupbuddy::load_style( 'backupProcess.css' );

require_once( pb_backupbuddy::plugin_path() . '/classes/backup.php' );
pb_backupbuddy::$classes['backup'] = new pb_backupbuddy_backup();
$serial_override = pb_backupbuddy::random_string( 10 ); // Set serial ahead of time so can be used by AJAX before backup procedure actually begins.

pb_backupbuddy::$ui->title( 'Create Backup' );
if ( 'true' == pb_backupbuddy::_GET( 'quickstart_wizard' ) ) {
	pb_backupbuddy::alert( 'Your Quick Setup Settings have been saved. Now performing your first backup...' );
}
?>

<script type="text/javascript">
	window.onerror=function(){
		alert( 'Error #82389: A javascript occurred which may break functionality on this page. Check your browser error console for details. This is most often caused by another plugin or theme containing broken javascript. Try temporarily disabling all other plugins.' );
	}
	
	var statusBox; // #backupbuddy_messages
	
	var stale_archive_time_trigger = 5; // If this time ellapses without archive size increasing warn user that something may have gone wrong.
	var stale_archive_time_trigger_increment = 1; // Number of times the popup has been shown.
	var backupbuddy_errors_encountered = 0; // number of errors sent via log.
	var last_archive_size = 0; // makes in scope later.
	var keep_polling = 1;
	var pb_blink_status = 1;
	var last_archive_change = 0; // Time where archive size last changed.
	var last_archive_size = ''; // Last archive size string.
	var backup_init_complete_poll_retry_count = 8; // How many polls to wait for backup init to complete
	var seconds_before_verifying_cron_schedule = 15; // How many seconds must elapse while in the cronPass action before polling WP to check and see if the schedule exists.
	
	// Vars used by events.
	var backupbuddy_currentFunction = '';
	var backupbuddy_currentAction = '';
	var backupbuddy_currentActionStart = 0;
	var backupbuddy_currentActionLastWarn = 0;
	var suggestions = [];
	
	jQuery(document).ready(function() {
		
		<?php
		// For MODERN mode we will wait until the DOM fully loads before beginning polling the server status.
		if ( pb_backupbuddy::$options['backup_mode'] != '1' ) { // NOT classic mode. Run once doc ready.
			echo "setTimeout( 'backupbuddy_poll()', 500 );";
		}
		?>
		
		jQuery( '.pb_backupbuddy_hoveraction_send' ).click( function(e) {
			tb_show( 'BackupBuddy', '<?php echo pb_backupbuddy::ajax_url( 'destination_picker' ); ?>&callback_data=' + jQuery(this).attr('rel') + '&sending=1&TB_iframe=1&width=640&height=455', null );
			return false;
		});
		
		jQuery( '#pb_backupbuddy_stop > a' ).click( function() {
			jQuery(this).html( 'Cancelling ...' );
			backupbuddy_log( '' );
			backupbuddy_log( "***** BACKUP CANCELLED MANUALLY BY USER - Forcing backup to skip to cleanup step as soon as possible. *****" );
			backupbuddy_log( '' );
			var cancel_button = jQuery(this);
			jQuery.post( '<?php echo pb_backupbuddy::ajax_url( 'stop_backup' ); ?>', { serial: '<?php echo $serial_override; ?>' }, 
				function(data) {
					data = jQuery.trim( data );
					if ( data.charAt(0) != '1' ) {
						alert( '<?php _e("Error stopping backup.", 'it-l10n-backupbuddy' ); ?> Details:' + "\n\n" + data );
					} else {
						//alert( "<?php _e('This backup has been stopped. Any external spawned processes currently active may continue until timeout.', 'it-l10n-backupbuddy' ); ?> <?php _e( 'You will be notified by email if any problems are encountered.', 'it-l10n-backupbuddy' ); ?>" + "\n\n" + data.slice(1) );
						cancel_button.html( 'Backup Cancelled' );
						cancel_button.attr( 'disabled', 'disabled' );
					}
				}
			);
			return false;
		});
		
	}); // end on jquery ready.
	
	
	
	<?php
	if ( pb_backupbuddy::$options['backup_mode'] == '1' ) { // CLASSIC mode. Run right away so we can show output before page finishes loading (backup fully finishes).
		echo "setTimeout( 'backupbuddy_poll()', 1000 );";
	}
	?>
	
	
	
	
	function backupbuddy_showSuggestions( suggestionList ) {
		//suggestionList.forEach( function(suggestion){
		for (var k in suggestionList){
			backupbuddy_log( '*** POSSIBLE ISSUE ***' );
			backupbuddy_log( '* ABOUT: ' + suggestionList[k].description );
			backupbuddy_log( '* POSSIBLE FIX: ' + suggestionList[k].quickFix );
			backupbuddy_log( '* MORE INFORMATION: ' + suggestionList[k].solution );
			backupbuddy_log( '***' );
		}
	}
	
	
	
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
	
	
	
	/***** LOOK & FEEL *****/
	
	
	
	setInterval( 'blink_ledz()' , 400 );
	function backupbuddy_redstatus() {
		jQuery( '#ui-id-2' ).css( 'background', '#FF8989' );
		jQuery( '#ui-id-2' ).css( 'color', '#000000' );
	}
	function blink_ledz( this_status ) {
		if ( pb_blink_status == 1 ) {
			jQuery( '.pb_backupbuddy_blinkz' ).addClass( 'pb_backupbuddy_empty' );
			jQuery( '.pb_backupbuddy_blinkz' ).removeClass( 'pb_backupbuddy_glow' );
			pb_blink_status = 0;
		} else {
			jQuery( '.pb_backupbuddy_blinkz' ).addClass( 'pb_backupbuddy_glow' );
			jQuery( '.pb_backupbuddy_blinkz' ).removeClass( 'pb_backupbuddy_empty' );
			pb_blink_status = 1;
		}
	}
	
	
	
	/***** HELPER FUNCTIONS *****/
	
	
	
	function unix_timestamp() {
		return Math.round( ( new Date() ).getTime() / 1000 );
	}
	
	
	function backupbuddy_poll_altcron() {
		if ( keep_polling != 1 ) {
			return;
		}
		
		jQuery.get(
			'<?php echo admin_url('admin.php').'?page=pluginbuddy_backupbuddy&pb_backupbuddy_alt_cron=true'; ?>',
			function(data) {
			}
		);
	}
	
	
	
	/***** BACKUP STATUS *****/
	
	
	// Used in BackupBuddy _backup-perform.php and ImportBuddy _header.php
	function backupbuddy_log( json ) {
		
		if( 'undefined' === typeof statusBox ) { // No status box yet so may need to create it.
			statusBox = jQuery( '#backupbuddy_messages' );
			if( statusBox.length == 0 ) { // No status box yet so suppress.
				return;
			}
		}
		
		message = '';
		
		if ( 'string' == ( typeof json ) ) {
			message = "-----------\t\t-------\t-------\t" + json;
		} else {
			message = json.date + '.' + json.u + " \t" + json.run + "sec \t" + json.mem + "MB\t" + json.data;
		}
		
		statusBox.append( "\r\n" + message );
		statusBox.scrollTop( statusBox[0].scrollHeight - statusBox.height() );
	}
	
	
	// left hour pad with zeros
	function backupbuddy_hourpad(n) { return ("0" + n).slice(-2); }
	
	
	function backupbuddy_poll() {
		if ( keep_polling != 1 ) {
			return;
		}
		
		// Check to make sure archive size is increasing. Warn if it seems to hang.
		if ( ( last_archive_change != 0 ) && ( ( ( unix_timestamp() - last_archive_change ) > stale_archive_time_trigger ) ) ) {
			backupbuddy_redstatus();
			if ( stale_archive_time_trigger >= 35 ) {
				alert( "Warning: The backup archive file size has not increased in " + stale_archive_time_trigger + " seconds. The backup may have failed. If it does not increase in the next few minutes it most likely timed out.\nSubsequent warnings will be displayed in the Status Log which contains more details." );
				jQuery( '#backupbuddy_messages' ).append( "\n* Warning: The backup archive file size has not increased in " + stale_archive_time_trigger + " seconds. The backup may have failed. If it does not increase in the next few minutes it most likely timed out." );
			} else {
				var stale_time = Math.round( stale_archive_time_trigger / 60 );
				if ( stale_time != 0 ) {
					jQuery( '#backupbuddy_messages' ).append( "\n* Warning: The backup archive file size has not increased in " + stale_time + " minutes. The backup may have failed. If it does not increase in the next few minutes it most likely timed out." );
				}
			}
			textareaelem = document.getElementById( 'backupbuddy_messages' );
			textareaelem.scrollTop = textareaelem.scrollHeight;
			
			stale_archive_time_trigger = 60 * 5 * stale_archive_time_trigger_increment;
			stale_archive_time_trigger_increment++;
		}
		
		specialAction = '';
		jQuery('.pb_backupbuddy_loading').show();
		backupbuddy_log( 'Ping? Waiting for server . . .' );
		if ( 'cronPass' == backupbuddy_currentAction ) { // In cronPass action...
			if ( ( unix_timestamp() - backupbuddy_currentActionStart ) > seconds_before_verifying_cron_schedule ) {
				backupbuddy_log( 'It has been ' + ( unix_timestamp() - backupbuddy_currentActionStart ) + ' seconds since the next step was scheduled. Checking cron schedule.' );
				specialAction = 'checkSchedule';
			}
		}
		jQuery.ajax({
			
			url:	'<?php echo pb_backupbuddy::ajax_url( 'backup_status' ); ?>',
			type:	'post',
			data:	{ serial: '<?php echo $serial_override; ?>', initwaitretrycount: backup_init_complete_poll_retry_count, action: 'pb_backupbuddy_backup_status', specialAction: specialAction },
			context: document.body,
			
			success: function( data ) {
				
				jQuery('.pb_backupbuddy_loading').hide();
				
				data = data.split( "\n" );
				for( var i = 0; i < data.length; i++ ) {
					
					
					try {
						var json = jQuery.parseJSON( data[i] );
						
						// Used in BackupBuddy _backup-perform.php and ImportBuddy _header.php
						
						json.date = new Date();
						json.date = new Date(  ( json.time * 1000 ) + json.date.getTimezoneOffset() * 60000 );
						var seconds = json.date.getSeconds();
						if ( seconds < 10 ) {
							seconds = '0' + seconds;
						}
						json.date = backupbuddy_hourpad( json.date.getHours() ) + ':' + json.date.getMinutes() + ':' + seconds;
						
						triggerEvent = 'backupbuddy_' + json.event;
						
						// Log non-text events.
						if ( ( 'details' !== json.event ) && ( 'message' !== json.event ) && ( 'error' !== json.event ) ) {
							console.log( 'Non-text event `' + triggerEvent + '`.' );
						} else {
							//console.log( json.data );
						}
						
						jQuery('#backupbuddy_messages').trigger( triggerEvent, [json] );
						continue;
					} catch(e) {
						console.log( 'NOTjson' + data[i] );
					}
					continue;
					
				} // end for.
				
				// Set the next server poll if applicable to happen in 2 seconds.
				setTimeout( 'backupbuddy_poll()' , 2000 );
				<?php // Handles alternate WP cron forcing.
				if ( defined('ALTERNATE_WP_CRON') && ALTERNATE_WP_CRON ) {
					echo '	setTimeout( \'backupbuddy_poll_altcron()\', 2000 );';
				}
				?>
				
			}, // end success.
			
			complete: function( jqXHR, status ) {
				if ( ( status != 'success' ) && ( status != 'notmodified' ) ) {
					jQuery('.pb_backupbuddy_loading').hide();
				}
			} // end complete.
			
		}); // end ajax.
		
		
		
		// Check runtime of current action...
		if ( '' !== backupbuddy_currentAction ) {
			actionRunTime = unix_timestamp() - backupbuddy_currentActionStart;
			sinceLastWarn = ( unix_timestamp() - backupbuddy_currentActionLastWarn );
			
			
			if ( 'cronPass' == backupbuddy_currentAction ) {
				if ( ( actionRunTime > 20 ) && ( sinceLastWarn > 45 ) ) { // sinceLastWarn is large number (timestamp) until set first time. so triggers off actionRunTime solely first.
					backupbuddy_currentActionLastWarn = unix_timestamp();
					thisSuggestion = {
						description: 'BackupBuddy uses WordPress\' scheduling system (cron) for running each backup step. Sometimes something interferes with this scheduling preventing the next step from running.',
						quickFix: 'If there are delays but the backup proceeds anyways then you can ignore this. If not, you will need to narrow down the problem first.',
						solution: 'Narrow down the problem: Run BackupBuddy in classic mode which bypasses the cron. Navigate to Settings: Advanced Settings / Troubleshooting tab: Change "Default global backup method" to Classic Mode (v1.x). If either of these fixes it, another plugin is most likely the cause is a malfunctioning plugin or a server problem. Disable all other plugins to see if this solves the problem. If it does then it is a problem plugin. Enable one by one until the problem returns to determine the culprit.'
					};
					suggestions['cronPass'] = thisSuggestion;
					backupbuddy_showSuggestions( [thisSuggestion] );
				}
			}
			
			
			if ( 'importbuddyCreation' == backupbuddy_currentAction ) {
				if ( ( actionRunTime > 10 ) && ( sinceLastWarn > 30 ) ) { // sinceLastWarn is large number (timestamp) until set first time. so triggers off actionRunTime solely first.
					backupbuddy_currentActionLastWarn = unix_timestamp();
					thisSuggestion = {
						description: 'BackupBuddy by default includes a copy of the restore tool, importbuddy.php, inside the backup ZIP file for retrieval if needed in the future.',
						quickFix: 'Turn off inclusion of ImportBuddy. Navigate to Settings: Advanced Settings / Troubleshooting tab: Uncheck "Include ImportBuddy in full backup archive".',
						solution: 'Increase available PHP memory.'
					};
					suggestions['importbuddyCreation'] = thisSuggestion;
					backupbuddy_showSuggestions( [thisSuggestion] );
				}
			}
			
			
			if ( 'zipCommentMeta' == backupbuddy_currentAction ) {
				if ( ( actionRunTime > 10 ) && ( sinceLastWarn > 30 ) ) { // sinceLastWarn is large number (timestamp) until set first time. so triggers off actionRunTime solely first.
					backupbuddy_currentActionLastWarn = unix_timestamp();
					thisSuggestion = {
						description: 'Some servers have trouble adding in a zip comment to files after they are created. Disabling this option skips this step. This meta data is not required so disabling it is not a problem.',
						quickFix: 'Turn off zip saving meta data in comments. Navigate to Settings: Advanced Settings / Troubleshooting tab: Uncheck "Save meta data in comment" to disable saving it.',
						solution: 'Increasing overall resources may help if you wish to keep this enabled.'
					};
					suggestions['zipCommentMeta'] = thisSuggestion;
					backupbuddy_showSuggestions( [thisSuggestion] );
				}
			}
			
			
		} // end if an action is running.
		
		
		
	} // end backupbuddy_poll().
	
	
	
	function pb_status_append( status_string ) {
		target_id = 'backupbuddy_messages'; // importbuddy_status or pb_backupbuddy_status
		if( jQuery( '#' + target_id ).length == 0 ) { // No status box yet so suppress.
			return;
		}
		jQuery( '#' + target_id ).append( "\n" + status_string );
		textareaelem = document.getElementById( target_id );
		textareaelem.scrollTop = textareaelem.scrollHeight;
	}
	
	
	
	// Trigger an error to be logged, displayed, etc.
	// Returns updated message with trouble URL, etc.
	// Used in BackupBuddy _backup-perform.php and ImportBuddy _header.php
	function backupbuddyError( message ) {
		
		// Get start of any error numbers.
		troubleURL = '';
		error_number_begin = message.toLowerCase().indexOf( 'error #' );
		
		if ( error_number_begin >= 0 ) {
			error_number_begin += 7; // Shift over index to after 'error #'.
			error_number_end = message.toLowerCase().indexOf( ':', error_number_begin );
			if ( error_number_end < 0 ) { // End still not found.
				error_number_end = message.toLowerCase().indexOf( '.', error_number_begin );
			}
			if ( error_number_end < 0 ) { // End still not found.
				error_number_end = message.toLowerCase().indexOf( ' ', error_number_begin );
			}
			error_number = message.slice( error_number_begin, error_number_end );
			troubleURL = 'http://ithemes.com/codex/page/BackupBuddy:_Error_Codes#' + error_number;
		}
		
		if ( '' !== troubleURL ) {
			// Display error in overview tab.
			jQuery( '.backup-steps' ).append( '<span class="backup-step-status backup-step-status-error"></span><div class="backup-step backup-step-secondary"><span class="backup-step-title">' + message + ' <a href="' + troubleURL + '">Click to <b>view error details</b> in the Knowledge Base</a></span></div>' );
			
			// Add error URL link to message.
			message += ' Error information & troubleshooting: ' + troubleURL + ' ';
		}
		
		// Display error box to make it clear errors were encountered.
		backupbuddy_errors_encountered++;
		jQuery( '#backupbuddy_errors_notice_count' ).text( backupbuddy_errors_encountered );
		jQuery( '#backupbuddy_errors_notice' ).slideDown();
		
		// If the word error is nowhere in the error message then add in error prefix.
		if ( message.toLowerCase().indexOf( 'error' ) < 0 ) {
			message = 'ERROR: ' + message;
		}
		
		backupbuddy_redstatus();
		
		return message; // Return updated error message with trouble URL.
	} // end backupbuddyError().
	
	
	// Used in BackupBuddy _backup-perform.php and ImportBuddy _header.php
	function backupbuddyWarning( message ) {
		return 'Warning: ' + message;
	} // end backupbuddyWarning().
</script>



<div style="width: 800px;" class="pb_progress">
	<span class="pb_backupbuddy_loading" style="display: none; margin-top: 13px; margin-left: 10px; float: right;"><img src="<?php echo pb_backupbuddy::plugin_url();; ?>/images/loading.gif" <?php echo 'alt="', __('Loading...', 'it-l10n-backupbuddy' ),'" title="',__('Loading...', 'it-l10n-backupbuddy' ),'"';?> width="16" height="16" style="vertical-align: -3px;" /></span>
	<div class="pb_backupbuddy_progress">
		<span class="pb_backupbuddy_step pb_backupbuddy_settings pb_backupbuddy_activate" id="pb_backupbuddy_slot1_step"><?php _e('Settings Export', 'it-l10n-backupbuddy' );?></span>
		<span class="pb_backupbuddy_empty pb_backupbuddy_blinkz pb_backupbuddy_activate" id="pb_backupbuddy_slot1_led"></span>
		
		<span class="pb_backupbuddy_step pb_backupbuddy_database" id="pb_backupbuddy_slot2_step"><?php _e('Database Export', 'it-l10n-backupbuddy' );?></span>
		<span class="pb_backupbuddy_empty" id="pb_backupbuddy_slot2_led"></span>
		
		<span class="pb_backupbuddy_step pb_backupbuddy_files" id="pb_backupbuddy_slot3_step"><?php _e('Files Export', 'it-l10n-backupbuddy' );?></span>
		<span class="pb_backupbuddy_empty" id="pb_backupbuddy_slot3_led"></span>
		
		<span class="pb_backupbuddy_step pb_backupbuddy_end pb_backupbuddy_empty" id="pb_backupbuddy_slot4_led"></span>
	</div>
</div>


<div style="clear: both;"></div>


<div style="width: 793px;">
	<br>
	<div id="pb_backupbuddy_archive_download" style="display: none; text-align: center;">
		<a id="pb_backupbuddy_archive_url" href="#" class="button-primary"><?php _e('Download backup', 'it-l10n-backupbuddy' ); ?> (<span class="backupbuddy_archive_size">0 MB</span>)</a>
		&nbsp;&nbsp;&nbsp;
		<a id="pb_backupbuddy_archive_send" href="#" class="button-primary pb_backupbuddy_hoveraction_send"><?php _e('Send backup to destination', 'it-l10n-backupbuddy' ); ?></a>
		&nbsp;&nbsp;&nbsp;
		<a href="<?php echo pb_backupbuddy::page_url(); ?>" class="button secondary-button">&larr; <?php _e( 'back to backups', 'it-l10n-backupbuddy' );?></a>
	</div>
	<br>
</div>


<div>
	<span style="float: right; margin-top: 18px;">
		<b><?php _e('Archive size', 'it-l10n-backupbuddy' );?></b>:&nbsp; <span class="backupbuddy_archive_size">0 MB</span>
	</span>
	<?php
	
	
	$active_tab = 0;
	pb_backupbuddy::$ui->start_tabs(
		'settings',
		array(
			
			/*
			array(
				'title'		=>		__( 'Overview', 'it-l10n-backupbuddy' ),
				'slug'		=>		'general',
				'css'		=>		'margin-top: -11px;',
			),
			*/
			
			array(
				'title'		=>		__( 'Status Log', 'it-l10n-backupbuddy' ),
				'slug'		=>		'advanced',
				'css'		=>		'margin-top: -11px;',
			),
		),
		'width: 100%;',
		true,
		$active_tab
	);
	
	
	/*
	pb_backupbuddy::$ui->start_tab( 'general' );
	?>
	<div class="backup-steps">
	</div>
	<div style="width: 793; height: 33px; text-align: center; margin: 5px;">
		<span class="pb_backupbuddy_loading" style="display: none; margin-left: auto; margin-right: auto;"><img src="<?php echo pb_backupbuddy::plugin_url();; ?>/images/loading_large.gif" <?php echo 'alt="', __('Loading...', 'it-l10n-backupbuddy' ),'" title="',__('Loading...', 'it-l10n-backupbuddy' ),'"';?> width="33" height="33"></span>
	</div>
	<?php
	pb_backupbuddy::$ui->end_tab();
	*/
	
	
	pb_backupbuddy::$ui->start_tab( 'advanced' );
	echo '<textarea wrap="off" id="backupbuddy_messages" style="width: 100%; font-family: Andale Mono, monospace; tab-size: 3; -moz-tab-size: 3; -o-tab-size: 3;">Time				Elapsed	Memory	Message</textarea>';
	pb_backupbuddy::$ui->end_tab();
	
	
	?>
</div>


<div style="width: 793px;">
	<div id="backupbuddy_errors_notice"><span id="backupbuddy_errors_notice_count"></span> or more errors encountered. See the Status Log for details.<br><span id="backupbuddy_errors_notice_subtext"><b>Not all errors are fatal.</b> Look up error codes & troubleshooting details in the <a href="http://ithemes.com/codex/page/BackupBuddy#Troubleshooting" target="_new"><b>Knowledge Base</b></a>.<br><b><i>Provide a copy of the Status Log if seeking support.</i></b></span></div>
	
	<br>
	<div id="pb_backupbuddy_stop">
		<a class="button secondary-button">Cancel Backup</a>
	</div>
	<br><br>
	
	<div class="description" style="text-align: center;">
		<?php
		if ( pb_backupbuddy::$options['backup_mode'] == '1' ) { // Classic mode (all in one page load).
			_e('Running in CLASSIC mode. Leaving this page before the backup completes will lead to a failed backup.', 'it-l10n-backupbuddy' );
		}
		?>
	</div>
</div>



<?php
$requested_profile = pb_backupbuddy::_GET( 'backupbuddy_backup' );
if ( 'db' == $requested_profile ) { // db profile is always index 1.
	$requested_profile = '1';
} elseif ( 'full' == $requested_profile ) { // full profile is always index 2.
	$requested_profile = '2';
}

$export_plugins = array(); // Default of no exported plugins. Used by MS export.
if ( pb_backupbuddy::_GET( 'backupbuddy_backup' ) == 'export' ) { // EXPORT.
	$export_plugins = pb_backupbuddy::_POST( 'items' );
	$profile_array = pb_backupbuddy::$options['0']; // Run exports on default profile.
	$profile_array['type'] = 'export'; // Pass array with export type set.
} else { // NOT MULTISITE EXPORT.
	if ( is_numeric( $requested_profile ) ) {
		if ( isset( pb_backupbuddy::$options['profiles'][ $requested_profile ] ) ) {
			$profile_array = pb_backupbuddy::$options['profiles'][ $requested_profile ];
		} else {
			die( 'Error #84537483: Invalid profile ID `' . htmlentities( $requested_profile ) . '`. Profile with this number was not found. Try deactivating then reactivating the plugin. If this fails please reset the plugin Settings back to Defaults from the Settings page.' );
		}
	} else {
		die( 'Error #85489548955. Invalid profile ID not numeric: `' . htmlentities( $requested_profile ) . '`.' );
	}
}


// Sending to remote destination after manual backup completes?
$post_backup_steps = array();
if ( ( pb_backupbuddy::_GET( 'after_destination' ) != '' ) && ( is_numeric( pb_backupbuddy::_GET( 'after_destination' ) ) ) ) {
	$destination_id = (int) pb_backupbuddy::_GET( 'after_destination' );
	if ( pb_backupbuddy::_GET( 'delete_after' ) == 'true' ) {
		$delete_after = true;
	} else {
		$delete_after = false;
	}
	$post_backup_steps = array(
		array(
			'function'		=>		'send_remote_destination',
			'args'			=>		array( $destination_id, $delete_after ),
			'start_time'	=>		0,
			'finish_time'	=>		0,
			'attempts'		=>		0,
		)
	);
	pb_backupbuddy::status( 'details', 'Manual backup set to send to remote destination `' . $destination_id . '`.  Delete after: `' . $delete_after . '`. Added to post backup function steps.' );
}



pb_backupbuddy::load_script( 'backupEvents.js' );



// Run the backup!
pb_backupbuddy::flush(); // Flush any buffer to screen just before the backup begins.
if ( pb_backupbuddy::$classes['backup']->start_backup_process(
		$profile_array,											// Profile array.
		'manual',												// Backup trigger. manual, scheduled
		array(),												// pre-backup array of steps.
		$post_backup_steps,										// post-backup array of steps.
		'',														// friendly title of schedule that ran this (if applicable).
		$serial_override,										// if passed then this serial is used for the backup insteasd of generating one.
		$export_plugins											// Multisite export only: array of plugins to export.
	) !== true ) {
	pb_backupbuddy::alert( __('Fatal Error #4344443: Backup failure', 'it-l10n-backupbuddy' ), true );
}
?>


</div>
