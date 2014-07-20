<?php
if ( ! defined( 'PB_IMPORTBUDDY' ) || ( true !== PB_IMPORTBUDDY ) ) {
	die( '<html></html>' );
}

// Handle small size PHP upload limit knocking off authentication when uploading a backup.
if ( isset( $_SERVER['CONTENT_LENGTH'] ) && ( intval( $_SERVER['CONTENT_LENGTH'] ) > 0 ) && ( count( $_POST ) === 0 ) ) {
	pb_backupbuddy::alert( 'Error #5484548595. Unable to upload. Your PHP post_max_size setting is too small so it discarded POST data. You may have to log back in.', true );
}

$step = '1';
if ( true !== Auth::is_authenticated() ) { // Need authentication.
	$page_title = 'Authentication Required';
} else {
	$page_title = 'Step <span class="step_number">' . $step . '</span> of 6: <a href="" class="pb_backupbuddy_begintour">Tour This Page</a>Choose your backup file';
}
require_once( '_header.php' );
?>



<ol id="pb_backupbuddy_tour" style="display: none;">
	<li data-id="server_tab">Backup files currently on this server. Select one of these to restore.</li>
	<li data-id="upload_tab">Upload a backup file from your computer's web browser up to this server so you can restore it.</li>
	<li data-id="stash_tab">Retrieve a backup file stored on BackupBuddy Stash (iThemes' cloud backup storage) and pull it to this server for restoring.</li>
	<li data-id="view_meta_1">Additional details about the backup file can be viewed here, if available.</li>
	<li data-id="server_info_button">Displays information about your server such as configuration and compatibility.</li>
	<li data-id="advanced_options_button">Provides additional advanced configuration options useful for customizing restores or working around server problems.</li>
	<li data-id="next_step_button" data-button="Finish">Click here to proceed to the next step when ready.</li>
</ol>



<script type="text/javascript" src="importbuddy/js/jquery.leanModal.min.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('.leanModal').leanModal(
			{ top : 20, overlay : 0.4, closeButton: ".modal_close" }
		);
		
		/* MD5 Hash Button Clicked */
		jQuery( '.view_hash_click' ).click( function() {
			jQuery('#hash_view_loading').show();
			jQuery('#hash_view_response').hide();
			
			var backupFile = jQuery(this).attr( 'data-file' );
			jQuery.ajax({
				type: 'POST',
				url: 'importbuddy.php',
				data: {
					ajax: 'file_hash',
					file: backupFile
				},
				dataType: 'json'
			}).done( function(data) {
				jQuery('#hash_view_response').html( '<b>MD5 Checksum Hash:</b> ' + data.hash );
				jQuery('#hash_view_loading').hide();
				jQuery('#hash_view_response').show();
			}).fail( function( jqXHR, textStatus, errorThrown ){
				jQuery('#hash_view_response').html( 'Error: `' + jqXHR.responseText + '`.' );
				jQuery('#hash_view_loading').hide();
				jQuery('#hash_view_response').show();
			});
		});
	});
</script>



<?php
echo pb_backupbuddy::$classes['import']->status_box( 'Step 1 debugging information for ImportBuddy ' . pb_backupbuddy::settings( 'version' ) . ' from BackupBuddy v' . pb_backupbuddy::$options['bb_version'] . '...', true );
?>

<div class="wrap">

<?php
if ( true !== Auth::is_authenticated() ) { // Need authentication.
	if ( pb_backupbuddy::_POST( 'password' ) != '' ) {
		global $pb_login_attempts;
		pb_backupbuddy::alert( 'Invalid password. Please enter the password you provided within BackupBuddy Settings. Attempt #' . $pb_login_attempts . '.' );
		echo '<br>';
	}
	?>
	Enter your ImportBuddy password to continue.
	<br><br>
	<form method="post" action="?step=1<?php if ( pb_backupbuddy::_GET( 'skip_serverinfo' ) != '' ) { echo '&skip_serverinfo=true'; } ?>">
		<input type="password" name="password">
		<input type="submit" name="submit" value="Authenticate" class="button">
	</form>
	
	</div><!-- /wrap -->
<?php
} else {
	upload(); // Handle any uploading of a backup file.
	$backup_archives = get_archives_list();
	//print_r( $backup_archives );
	$wordpress_exists = wordpress_exists();
	?>
	
	
	
	Select a backup to restore from this server, Stash, or upload a backup below. Throughout the restore process you may hover over question marks
	<?php pb_backupbuddy::tip( 'This is an example help tip. Hover over these for additional help.' ); ?> 
	for additional help. For support see the <a href="http://ithemes.com/codex/page/BackupBuddy" target="_blank">Knowledge Base</a>
	or <a href="http://pluginbuddy.com/support/" target="_blank">Support Forum</a>.
	<br><br>
	
	
	
	<?php
	
	if ( pb_backupbuddy::_GET( 'file' ) != '' ) {
		echo '
		<div style="padding: 15px; background: #FFFFFF;">Restoring from backup <i>' . htmlentities( pb_backupbuddy::_GET( 'file' ) ) . '</i></div>
		<form action="?step=2" method="post">
			<input type="hidden" name="pass_hash" value="' . PB_PASSWORD . '" />
			<input type="hidden" name="options" value="' . htmlspecialchars( serialize( pb_backupbuddy::$options ) ) . '" />
			<input type="hidden" name="file" value="' . htmlspecialchars( pb_backupbuddy::_GET( 'file' ) ) . '">
		';
				
	} else {
		
		/********* Start warnings for existing files. *********/
		if ( wordpress_exists() === true ) {
			pb_backupbuddy::alert( 'WARNING: Existing WordPress installation found. It is strongly recommended that existing WordPress files and database be removed prior to migrating or restoring to avoid conflicts. You should not install WordPress prior to migrating.' );
		}
		if ( phpini_exists() === true ) {
			pb_backupbuddy::alert( 'WARNING: Existing php.ini file found. If your backup also contains a php.ini file it may overwrite the current one, possibly resulting in changes in cofiguration or problems. Make a backup of your existing file if your are unsure.' );
		}
		if ( htaccess_exists() === true ) {
			pb_backupbuddy::alert( 'WARNING: Existing .htaccess file found. If your backup also contains a .htaccess file it may overwrite the current one, possibly resulting in changed in configuration or problems. Make a backup of your existing file if you are unsure.' );
		}
		
		// Look for directories named after a backup file that contain WordPress.
		$backup_dirs = glob( ABSPATH . 'backup-*/wp-login.php' );
		if ( ! is_array( $backup_dirs ) ) {
			$backup_dirs = array();
		}
		if ( count( $backup_dirs ) > 0 ) {
			pb_backupbuddy::alert( 'A manually unzipped backup may have been found in the following location(s). If you manually unzipped confirm the files were not unzipped into this subdirectory else they will need to be moved up out of the subdirectory into the same directory as importbuddy.php. Possible manually unzipped backups in a subdirectory: ' . implode( ', ', $backup_dirs ) );
		}
		
		
		echo '<br><br>';
		?>
		
		
		
		<div id="pluginbuddy-tabs">
			<ul>
				<li><a href="#pluginbuddy-tabs-server" id="server_tab"><span>Server</span></a></li>
				<li><a href="#pluginbuddy-tabs-upload" id="upload_tab"><span>Upload</span></a></li>
				<li><a href="#pluginbuddy-tabs-stash" id="stash_tab"><span>Stash</span></a></li>
			</ul>
			<div id="pluginbuddy-tabs-stash">
				<div class="tabs-item">
					
					<?php require_once( '_html_1_stash.php' ); ?>
					
				</div>
			</div>
			<div class="tabs-borderwrap">
				
				<div id="pluginbuddy-tabs-upload">
					<div class="tabs-item">
						<form enctype="multipart/form-data" action="?step=1" method="POST">
							<input type="hidden" name="pass_hash" value="<?php echo PB_PASSWORD; ?>">
							<input type="hidden" name="upload" value="local">
							<input type="hidden" name="options" value="<?php echo htmlspecialchars( serialize( pb_backupbuddy::$options ) ); ?>'">
							Choose a local backup from your computer to upload to this server.<br><br>
							<p>
								<input name="file" type="file" style="width: 100%;">
							</p>
							<br>
							<input type="submit" value="Upload Backup" class="toggle button">
						</form>
						
						<br><br>
						<i>If you have trouble with uploads from this page use FTP to upload backups instead.</i>
					</div>
				</div>
				
				<div id="pluginbuddy-tabs-server">
					<div class="tabs-item">
						<?php
						if ( empty( $backup_archives ) ) { // No backups found.
							
							// Look for manually unzipped
							pb_backupbuddy::alert( '<b>No BackupBuddy Zip backup found in this directory `' . ABSPATH . '`</b> - 
								You must upload a backup file by FTP (into the same directory as this importbuddy.php file), the upload tab, or import from Stash via the Stash tab above to continue.
								<b>Do not rename the backup file from its original filename.</b> If you manually extracted/unzipped, upload the backup file,
								select it, then select <i>Advanced Troubleshooting Options</i> & click <i>Skip Zip Extraction</i>. Refresh this page once you have uploaded the backup.' );
							
						} else { // Found one or more backups.
							?>
								<form action="?step=2" method="post">
									<input type="hidden" name="pass_hash" value="<?php echo PB_PASSWORD; ?>">
									<input type="hidden" name="options" value="<?php echo htmlspecialchars( serialize( pb_backupbuddy::$options ) ); ?>'" />
							<?php
							echo '<div class="backup_select_text">Select backup from <div style="display: inline-block; max-width: 500px; overflow: scroll; vertical-align: -3px;">' . ABSPATH . '</div></div>';
							echo '<br>';
							echo '<ul style="list-style-type: none; margin: 0; padding: 0;">';
							$backup_count = count( $backup_archives );
							$i = 0;
							foreach( $backup_archives as $backup_id => $backup_archive ) {
								$i++;
								echo '<li style="padding-top: 8px; padding-bottom: 8px;';
								if ( $i < $backup_count ) {
									echo ' border-bottom: 1px solid #DFDFDF;';
								}
								echo '"><input type="radio" ';
								if ( $backup_id == 0 ) {
									echo 'checked="checked" ';
								}
								echo 'name="file" value="' . $backup_archive['file'] . '"> ' . $backup_archive['file'];
								echo '<span style="float: right;">' . pb_backupbuddy::$format->file_size( filesize( ABSPATH . $backup_archive['file'] ) ) . '</span>';
								echo '<br>';
								
								echo '<div class="description" style="margin-left: 22px; margin-top: 6px; font-style: normal; line-height: 26px;">';
								$meta = array();
								
								if ( $backup_archive['comment']['type'] == '' ) {
									if ( stristr( $backup_archive['file'], '-db-' ) !== false ) {
										echo 'Database Only Backup';
									} elseif ( stristr( $backup_archive['file'], '-full-' ) !== false ) {
										echo 'Full Backup';
									} elseif ( stristr( $backup_archive['file'], '-files-' ) !== false ) {
										echo 'Files Only Backup';
									}
								} else {
									if ( $backup_archive['comment']['type'] == 'db' ) {
										echo 'Database Only Backup';
									} elseif ( $backup_archive['comment']['type'] == 'full' ) {
										echo 'Full Backup';
									} elseif ( $backup_archive['comment']['type'] == 'files' ) {
										echo 'Files Only Backup';
									} else {
										echo $backup_archive['comment']['type'] . ' Backup';
									}
								}
								
								if ( $backup_archive['comment']['created'] != '' ) {
									echo ' from ' . pb_backupbuddy::$format->date( $backup_archive['comment']['created'] );
								}
								
								if ( $backup_archive['comment']['wp_version'] != '' ) {
									echo ' on WordPress v' . $backup_archive['comment']['wp_version'];
								}
								if ( $backup_archive['comment']['bb_version'] != '' ) {
									echo ' & BackupBuddy v' . $backup_archive['comment']['bb_version'];
								}
								
								if ( $backup_archive['comment']['siteurl'] != '' ) {
									echo '<br>Site: ' . $backup_archive['comment']['siteurl'];
								}
								
								if ( $backup_archive['comment']['profile'] != '' ) {
									echo '<br>Profile: ' . htmlentities( $backup_archive['comment']['profile'] );
								}
								
								if ( $backup_archive['comment']['note'] != '' ) {
									echo '<br>Note: ' . htmlentities( $backup_archive['comment']['note'] ) . '<br>';
								}
								
								
								
								// Show meta button if meta info available.
								if ( $backup_archive['comment']['type'] != '' ) {
									$file_hash = md5( $backup_archive['file'] );
									echo '<a href="#hash_view" class="button button-tertiary leanModal view_hash_click" style="float: left; font-size: 10px; margin-right: 5px; padding: 4px; float: right;" id="view_hash_' . $i . '" data-file="' . $backup_archive['file'] . '">View Checksum</a>';
									echo '<a href="#info_' . $file_hash . '" class="button button-tertiary leanModal" style="float: left; font-size: 10px; margin-right: 5px; padding: 4px; float: right;" id="view_meta_' . $i . '">View Meta</a>';
									?>
									<div id="hash_view" style="display: none; height: 30%;">
										<div class="modal">
											<div class="modal_header">
												<a class="modal_close">&times;</a>
												<h2>View File Hash</h2>
											</div>
											<div class="modal_content">
												<span id="hash_view_loading"><img src="importbuddy/images/loading.gif"> Calculating backup file MD5 Hash... This may take a moment...</span>
												<span id="hash_view_response"></span>
											</div>
										</div>
									</div>
									<div id="<?php echo 'info_' . $file_hash; ?>" style="display: none; height: 90%;">
										<div class="modal">
											<div class="modal_header">
												<a class="modal_close">&times;</a>
												<h2>Backup Meta Information</h2>
											</div>
											<div class="modal_content">
												<?php
												$comment_meta = array();
												foreach( $backup_archive['comment'] as $comment_line_name => $comment_line_value ) { // Loop through all meta fields in the comment array to display.
													
													if ( false !== ( $response = backupbuddy_core::pretty_meta_info( $comment_line_name, $comment_line_value ) ) ) {
														$comment_meta[] = $response;
													}
													
												}
												if ( count( $comment_meta ) > 0 ) {
													pb_backupbuddy::$ui->list_table(
														$comment_meta,
														array(
															'columns'		=>	array( 'Meta Information', 'Value' ),
															'css'			=>	'width: 100%; min-width: 200px;',
														)
													);
												} else {
													echo '<i>No meta data found in zip comment. Skipping meta information display.</i>';
												}
												?>
											</div>
										</div>
									</div>
									<?php
								} // end if type not blank.
								
								
								
								//echo implode( ' - ', $meta );
								echo '</div>';
								echo '</li>';
							}
							echo '</ul>';
						}
						?>
						
						
					</div>
				</div>
				

				
			</div>
		</div>
		<br>
	<?php } // End file not given in querystring.
	
	
	
	// If one or more backup files was found then provide a button to continue.
	if ( !empty( $backup_archives ) ) {
		echo '</div><!-- /wrap -->';
		echo '<div class="main_box_foot">';
		echo '<a id="server_info_button" href="#pb_serverinfo_modal" class="button button-tertiary leanModal" style="float: left; font-size: 13px; margin-right: 5px;">Server Information</a>';
		echo '<a id="advanced_options_button" href="#pb_advanced_modal" class="button button-tertiary leanModal" style="float: left; font-size: 13px;">Advanced Options</a>';
		echo '<input type="submit" name="submit" value="Next Step &rarr;" class="button" id="next_step_button">';
		echo '</div>';
	} else {
		//pb_backupbuddy::alert( 'Upload a backup file to continue.' );
		echo '<b>You must upload a backup file by FTP, the upload tab, or import from Stash to continue.</b>';
		echo '</div><!-- /wrap -->';
	}
	
	?>

	<div id="pb_advanced_modal" style="display: none;">
		<div class="modal">
			<div class="modal_header">
				<a class="modal_close">&times;</a>
				<h2>Advanced Options</h2>
				These advanced options allow customization of various ImportBuddy functionality for custom purposes or troubleshooting.
				<b>Exercise caution</b> as some advanced options may have unforeseen effects if not used properly, such as overwriting existing files
				or erasing existing database content.
			</div>
			<div class="modal_content">
				
				
				
				<br><b>ZIP Archive Extraction (Step 2)</b><br>
				<input type="checkbox" value="on" name="skip_files"> Skip zip file extraction. <?php pb_backupbuddy::tip( 'Checking this box will prevent extraction/unzipping of the backup ZIP file.  You will need to manually extract it either on your local computer then upload it or use a server-based tool such as cPanel to extract it. This feature is useful if the extraction step is unable to complete for some reason.' ); ?><br>
				<input type="checkbox" value="on" name="force_compatibility_medium" /> Force medium speed compatibility mode (ZipArchive). <br>
				<input type="checkbox" value="on" name="force_compatibility_slow" /> Force slow speed compatibility mode (PCLZip). <br>
				
				<br><b>Database Import & Migration (Steps 3-5)</b><br>
				<span style="width: 16px; display: inline-block;"></span> <i>Select the "Adanced Options" button while on Step 3 for database options.</i><br>
				
				<br><b>General</b><br>
				<input type="checkbox" value="on" name="skip_htaccess"> Skip migration of .htaccess file. <br>
				<?php //<input type="checkbox" name="force_high_security"> Force high security on a normal security backup<br> ?>
				<input type="checkbox" value="on" name="show_php_warnings" /> Show detailed PHP warnings. <br>
				
				<br>
				Import Logging: <select name="log_level">
					<option value="0">None</option>
					<option value="1">Errors Only</option>
					<option value="2">Errors & Warnings</option>
					<option value="3" selected>Everything (default)</option>
				</select> <?php pb_backupbuddy::tip( 'Errors and other debugging information will be written to importbuddy.txt in the same directory as importbuddy.php.  This is useful for debugging any problems encountered during import.  Support may request this file to aid in tracking down any problems or bugs.' ); ?>
				
				
				
			</div>
		</div>
	</div>
	
	
	
	<div id="pb_serverinfo_modal" style="display: none; height: 90%;">
		<div class="modal">
			<div class="modal_header">
				<a class="modal_close">&times;</a>
				<h2>Server Information</h2>
			</div>
			<div class="modal_content">
				
				
				
				<?php
				global $detected_max_execution_time;
				$server_info_file = ABSPATH . 'importbuddy/controllers/pages/server_tools.php';
				if ( file_exists( $server_info_file ) ) {
					require_once( $server_info_file );
				} else {
					echo '{Error: Missing server tools file `' . $server_info_file . '`.}';
				}
				?>
				
				
				
			</div>
		</div>
	</div>
	
	
	
<?php
	echo '</form>';
}
require_once( '_footer.php' );
?>
