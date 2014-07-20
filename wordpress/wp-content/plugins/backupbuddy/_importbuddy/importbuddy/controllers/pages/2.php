<?php
if ( ! defined( 'PB_IMPORTBUDDY' ) || ( true !== PB_IMPORTBUDDY ) ) {
	die( '<html></html>' );
}

Auth::require_authentication(); // Die if not logged in.

$data = array(
	'step'		=>		'2',
);



pb_backupbuddy::set_greedy_script_limits( true );



parse_options();







/**
 *	parse_options()
 *
 *	Parses various submitted options and settings from step 1.
 *
 *	@return		null
 */
function parse_options() {
	
	// Set advanced debug options if user set any.
	if ( ( isset( $_POST['skip_files'] ) ) && ( $_POST['skip_files'] == 'on' ) ) {
		pb_backupbuddy::$options['skip_files'] = true;
	} else {
		pb_backupbuddy::$options['skip_files'] = false;
	}
	if ( ( isset( $_POST['skip_htaccess'] ) ) && ( $_POST['skip_htaccess'] == 'on' ) ) {
		pb_backupbuddy::$options['skip_htaccess'] = true;
	} else {
		pb_backupbuddy::$options['skip_htaccess'] = false;
	}
	if ( ( isset( $_POST['force_compatibility_medium'] ) ) && ( $_POST['force_compatibility_medium'] == 'on' ) ) {
		pb_backupbuddy::$options['force_compatibility_medium'] = true;
	} else {
		pb_backupbuddy::$options['force_compatibility_medium'] = false;
	}
	if ( ( isset( $_POST['force_compatibility_slow'] ) ) && ( $_POST['force_compatibility_slow'] == 'on' ) ) {
		pb_backupbuddy::$options['force_compatibility_slow'] = true;
	} else {
		pb_backupbuddy::$options['force_compatibility_slow'] = false;
	}
	if ( ( isset( $_POST['force_high_security'] ) ) && ( $_POST['force_high_security'] == 'on' ) ) {
		pb_backupbuddy::$options['force_high_security'] = true;
	} else {
		pb_backupbuddy::$options['force_high_security'] = false;
	}
	if ( ( isset( $_POST['show_php_warnings'] ) ) && ( $_POST['show_php_warnings'] == 'on' ) ) {
		pb_backupbuddy::$options['show_php_warnings'] = true;
	} else {
		pb_backupbuddy::$options['show_php_warnings'] = false;
	}
	if ( ( isset( $_POST['file'] ) ) && ( $_POST['file'] != '' ) ) {
		pb_backupbuddy::$options['file'] = $_POST['file'];
	} else {
		pb_backupbuddy::$options['file'] = '';
	}
	if ( ( isset( $_POST['log_level'] ) ) && ( $_POST['log_level'] != '' ) ) {
		pb_backupbuddy::$options['log_level'] = $_POST['log_level'];
	} else {
		pb_backupbuddy::$options['log_level'] = '';
	}
	
	// Set ZIP id (aka serial).
	if ( ! isset( pb_backupbuddy::$options['file'] ) ) {
		die( 'No backup zip file specified to process. Go back and make sure you selected a ZIP file to extract and restore on Step 1.' );
	}
	pb_backupbuddy::$options['zip_id'] = backupbuddy_core::get_serial_from_file( pb_backupbuddy::$options['file'] );
}



/* generate_maintenance_files()
 *
 * Generated a .maintenance file to inform WordPress not to allow access to the site.
 * This file is removed on Step 5.
 *
 */
function generate_maintenance_file() {
	if ( ! file_exists( ABSPATH . '.maintenance' ) ) {
		$maintenance_result = @file_put_contents( ABSPATH . '.maintenance', "<?php die( 'Site undergoing maintenance.' ); ?>" );
		if ( false === $maintenance_result ) {
			pb_backupbuddy::status( 'warning', '.maintenance file unable to be generated to prevent viewing partially migrated site. This is not a fatal error.' );
		} else {
			pb_backupbuddy::status( 'details', '.maintenance file generated to prevent viewing partially migrated site.' );
		}
	} else {
		pb_backupbuddy::status( 'details', '.maintenance file already exists. Skipping creation.' );
	}
} // end generate_maintenance_file().


/**
 *	extract()
 *
 *	Extract backup zip file.
 *
 *	@return		array		True if the extraction was a success OR skipping of extraction is set.
 */
function extract_files() {
	
	// Zip & Unzip library setup.
	require_once( ABSPATH . 'importbuddy/lib/zipbuddy/zipbuddy.php' );
	pb_backupbuddy::$classes['zipbuddy'] = new pluginbuddy_zipbuddy( ABSPATH, array(), 'unzip' );
	$backup_archive = ABSPATH . pb_backupbuddy::$options['file'];
	
	if ( true === pb_backupbuddy::$options['skip_files'] ) { // Option to skip all file updating / extracting.
		
		pb_backupbuddy::status( 'message', 'Skipped extracting files based on debugging options.' );
		
	} else {
		
		pb_backupbuddy::set_greedy_script_limits();
		pb_backupbuddy::status( 'message', 'Unzipping into `' . ABSPATH . '`' );
		
		$destination_directory = ABSPATH;
		
		// Set compatibility mode if defined in advanced options.
		$compatibility_mode = false; // Default to no compatibility mode.
		if ( pb_backupbuddy::$options['force_compatibility_medium'] != false ) {
			$compatibility_mode = 'ziparchive';
		} elseif ( pb_backupbuddy::$options['force_compatibility_slow'] != false ) {
			$compatibility_mode = 'pclzip';
		}
		
		// Extract zip file & verify it worked.
		if ( true !== ( $result = pb_backupbuddy::$classes['zipbuddy']->unzip( $backup_archive, $destination_directory, $compatibility_mode ) ) ) {
			pb_backupbuddy::status( 'error', 'Failed unzipping archive.' );
			pb_backupbuddy::alert( 'Failed unzipping archive.', true );
			return false;
		}
		
		pb_backupbuddy::status( 'details', 'Success extracting Zip File "' . ABSPATH . pb_backupbuddy::$options['file'] . '" into "' . ABSPATH . '".' );
		
	} // End extraction not skipped.
	
	
	// Made it here so zip returned true OR skipped unzip step.
	
	
	// Handle meta data in comment.
	pb_backupbuddy::status( 'details', 'Retrieving meta data from ZIP file (if any).' );
	$comment = pb_backupbuddy::$classes['zipbuddy']->get_comment( $backup_archive );
	$comment = backupbuddy_core::normalize_comment_data( $comment );
	$comment_text = print_r( $comment, true );
	$comment_text = str_replace( array( "\n", "\r" ), '; ', $comment_text );
	pb_backupbuddy::status( 'details', 'Backup meta data: `' . $comment_text . '`.' );
	
	// Use meta to find DAT file (if possible). BB v3.3+.
	$dat_file = '';
	if ( '' != $comment['dat_path'] ) { // Specific DAT location is known.
		pb_backupbuddy::status( 'details', 'Checking for DAT file as reported by meta data as file `' . ABSPATH . $comment['dat_path'] .'`.' );
		if ( file_exists( ABSPATH . $comment['dat_path'] ) ) {
			$dat_file = ABSPATH . $comment['dat_path'];
			pb_backupbuddy::status( 'details', 'DAT file found based on meta path.' );
		} else {
			pb_backupbuddy::status( 'warning', 'DAT file was not found as reported by meta data. This is unusual but may not be fatal. Commencing search for file...' );
		}
	}
	
	// Deduce DAT file location based on backup filename. BB < v3.3.
	if ( '' == $dat_file ) {
		pb_backupbuddy::status( 'details', 'Scanning for DAT file based on backup file name.' );
		
		$dat_file_locations = array(
			ABSPATH . 'wp-content/uploads/temp_' . pb_backupbuddy::$options['zip_id'] . '/backupbuddy_dat.php',					// OLD 1.x FORMAT. Full backup dat file location.
			ABSPATH . 'wp-content/uploads/backupbuddy_temp/' . pb_backupbuddy::$options['zip_id'] . '/backupbuddy_dat.php',		// Full backup dat file location
			ABSPATH . 'backupbuddy_dat.php',																					// DB only dat file location
		);
		
		$dat_file = '';
		foreach( $dat_file_locations as $dat_file_location ) {
			if ( file_exists( $dat_file_location ) ) {
				$dat_file = $dat_file_location;
				break;
			}
		}
		
		if ( '' == $dat_file ) { // DAT not found.
			$error_message = 'Error #9004: Key files missing. Backup data file, backupbuddy_dat.php was not found in the extracted files in any expected location. The unzip process either failed to fully complete, you renamed the backup ZIP file (rename it back to correct this), or the zip file is not a proper BackupBuddy backup.';
			pb_backupbuddy::status( 'error', $error_message );
			pb_backupbuddy::alert( $error_message, true, '9004' );
			return false;
		}
		
		pb_backupbuddy::status( 'details', 'Successfully found DAT file based on backup file name: `' . $dat_file . '`.' );
		
	}
	
	// Get DAT file contents & save into options..
	if ( false === ( pb_backupbuddy::$options['dat_file'] = backupbuddy_core::get_dat_file_array( $dat_file ) ) ) {
		die( 'Error #43784334: Fatal error getting DAT file. Import halted.' );
	}
	pb_backupbuddy::$options['temp_serial_directory'] = basename( $dat_file );
	pb_backupbuddy::save();
	
	return true;
	
} // End extract_files().



/*	rename_htaccess_temp()
 *	
 *	Renames .htaccess to .htaccess.bb_temp until last ImportBuddy step to avoid complications.
 *	
 *	@return		null
 */
function rename_htaccess_temp() {
	
	if ( !file_exists( ABSPATH . '.htaccess' ) ) {
		pb_backupbuddy::status( 'details', 'No .htaccess file found. Skipping temporary file rename.' );
	}
	
	$result = @rename( ABSPATH . '.htaccess', ABSPATH . '.htaccess.bb_temp' );
	if ( $result === true ) { // Rename succeeded.
		pb_backupbuddy::status( 'message', 'Renamed `.htaccess` file to `.htaccess.bb_temp` until final ImportBuddy step.' );
	} else { // Rename failed.
		pb_backupbuddy::status( 'warning', 'Unable to rename `.htaccess` file to `.htaccess.bb_temp`. Your file permissions may be too strict. You may wish to manually rename this file and/or check permissions before proceeding.' );
	}
	
} // End rename_htaccess_temp().







pb_backupbuddy::load_view( 'html_2', $data );
?>