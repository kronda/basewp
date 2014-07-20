<?php
if ( ! defined( 'PB_IMPORTBUDDY' ) || ( true !== PB_IMPORTBUDDY ) ) {
	die( '<html></html>' );
}

Auth::require_authentication(); // Die if not logged in.

$data = array(
	'step'		=>		'4',
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
	if ( isset( $_POST['siteurl'] ) ) {
		pb_backupbuddy::$options['siteurl'] = $_POST['siteurl'];
		
		if ( isset( $_POST['custom_home'] ) ) {
			pb_backupbuddy::$options['home'] = $_POST['home'];
		} else {
			pb_backupbuddy::$options['home'] = $_POST['siteurl'];
		}
		
	} // end isset post siteurl.
	
	
	if ( ( isset( $_POST['skip_database_import'] ) ) && ( $_POST['skip_database_import'] == 'on' ) ) { pb_backupbuddy::$options['skip_database_import'] = true; } else { pb_backupbuddy::$options['skip_database_import'] = false; }
	if ( ( isset( $_POST['skip_database_migration'] ) ) && ( $_POST['skip_database_migration'] == 'on' ) ) { pb_backupbuddy::$options['skip_database_migration'] = true; } else { pb_backupbuddy::$options['skip_database_migration'] = false; }
	if ( ( isset( $_POST['skip_database_bruteforce'] ) ) && ( $_POST['skip_database_bruteforce'] == 'on' ) ) { pb_backupbuddy::$options['skip_database_bruteforce'] = true; } else { pb_backupbuddy::$options['skip_database_bruteforce'] = false; }
	if ( ( isset( $_POST['mysqlbuddy_compatibility'] ) ) && ( $_POST['mysqlbuddy_compatibility'] == 'on' ) ) { pb_backupbuddy::$options['mysqlbuddy_compatibility'] = true; } else { pb_backupbuddy::$options['mysqlbuddy_compatibility'] = false; }
	if ( ( isset( $_POST['wipe_database'] ) ) && ( $_POST['wipe_database'] == 'on' ) ) { pb_backupbuddy::$options['wipe_database'] = true; } else { pb_backupbuddy::$options['wipe_database'] = false; }
	if ( ( isset( $_POST['wipe_database_all'] ) ) && ( $_POST['wipe_database_all'] == 'on' ) ) { pb_backupbuddy::$options['wipe_database_all'] = true; } else { pb_backupbuddy::$options['wipe_database_all'] = false; }
	if ( ( isset( $_POST['ignore_sql_errors'] ) ) && ( $_POST['ignore_sql_errors'] == 'on' ) ) { pb_backupbuddy::$options['ignore_sql_errors'] = true; } else { pb_backupbuddy::$options['ignore_sql_errors'] = false; }
	if ( ( isset( $_POST['max_execution_time'] ) ) && ( is_numeric( $_POST['max_execution_time'] ) ) ) {
		pb_backupbuddy::$options['max_execution_time'] = $_POST['max_execution_time'];
	} else {
		pb_backupbuddy::$options['max_execution_time'] = 30;
	}
	
	// Override for files only backup type.
	if ( 'files' == pb_backupbuddy::$options['dat_file']['backup_type'] ) {
		pb_backupbuddy::$options['skip_database_import'] = true;
		pb_backupbuddy::$options['skip_database_migration'] = true;
		pb_backupbuddy::$options['skip_database_migration'] = true;
	}
	
	
	// Multisite domain.
	if ( isset( $_POST['domain'] ) ) { pb_backupbuddy::$options['domain'] = $_POST['domain']; }
	
	if ( isset( $_POST['db_server'] ) ) { pb_backupbuddy::$options['db_server'] = $_POST['db_server']; }
	if ( isset( $_POST['db_user'] ) ) { pb_backupbuddy::$options['db_user'] = $_POST['db_user']; }
	if ( isset( $_POST['db_password'] ) ) { pb_backupbuddy::$options['db_password'] = $_POST['db_password']; }
	if ( isset( $_POST['db_name'] ) ) { pb_backupbuddy::$options['db_name'] = $_POST['db_name']; }
	if ( isset( $_POST['db_prefix'] ) ) {
		if ( !preg_match('/^[a-z0-9_]+$/i', $_POST['db_prefix'] ) ) {
			pb_backupbuddy::alert( 'ERROR: Invalid characters in database prefix. Please use your browser\'s back button to correct the error. Prefixes should only contain letters (xyz), numbers (123), and underscores (_).', true );
			die();
		}
		
		pb_backupbuddy::$options['db_prefix'] = $_POST['db_prefix'];
	}
	
	
	
	pb_backupbuddy::save();
}


/**
 *	import_database()
 *
 *	Parses various submitted options and settings from step 1.
 *
 *	@return		array		array( import_success, import_complete ).
 *							import_success: false if unable to import into database, true if import is working thus far/completed.
 *							import_complete: If incomplete, an integer of the next query to begin on. If complete, true. False if import_success = false.
 */
function import_database() {
	pb_backupbuddy::set_greedy_script_limits();
	
	
	// Migrate htaccess.
	pb_backupbuddy::$classes['import']->migrate_htaccess();
	
	
	if ( true === pb_backupbuddy::$options['skip_database_import'] ) {
		pb_backupbuddy::status( 'message', 'Skipping database restore based on settings.' );
		return array( true, true );
	}
	
	
	pb_backupbuddy::status( 'message', 'Verifying database connection and settings...' );
	if ( pb_backupbuddy::$classes['import']->connect_database() === false ) {
		pb_backupbuddy::alert( 'ERROR: Unable to select your specified database. Verify the database name and that you have set up proper permissions for your specified username to access it. Details: ' . mysql_error(), true, '9007' );
		return( array( false, false ) );
	} else {
		
		pb_backupbuddy::status( 'message', 'Database connection and settings verified. Connected to database `' . pb_backupbuddy::$options['db_name'] . '`.' );
		// Import database unless disabled.
		$db_continue = false;
		
		// Wipe database with matching prefix if option was selected.
		if ( pb_backupbuddy::$options['wipe_database'] == true ) {
			if ( isset( $_POST['db_continue'] ) && ( is_numeric( $_POST['db_continue'] ) ) ) {
				// do nothing
			} else { // dont wipe on substeps of db import.
				pb_backupbuddy::status( 'message', 'Wiping existing database tables with the same prefix based on settings...' );
				$failed = !pb_backupbuddy::$classes['import']->wipe_database( pb_backupbuddy::$options['db_prefix'] );
				if ( false !== $failed ) {
					pb_backupbuddy::message( 'error', 'Unable to wipe database as configured in the settings.' );
					pb_backupbuddy::alert( 'Unable to wipe database as configured in the settings.', true );
				}
			}
		}
		
		// Wipe database of ALL TABLES if option was selected.
		if ( pb_backupbuddy::$options['wipe_database_all'] == true ) {
			if ( isset( $_POST['db_continue'] ) && ( is_numeric( $_POST['db_continue'] ) ) ) {
				// do nothing
			} else { // dont wipe on substeps of db import.
				pb_backupbuddy::status( 'message', 'Wiping ALL existing database tables based on settings (use with caution)...' );
				$failed = !pb_backupbuddy::$classes['import']->wipe_database_all( true );
				if ( false !== $failed ) {
					pb_backupbuddy::message( 'error', 'Unable to wipe database as configured in the settings.' );
					pb_backupbuddy::alert( 'Unable to wipe database as configured in the settings.', true );
				}
			}
		}
		
		
		// Sanitize db continuation value if needed.
		if ( isset( $_POST['db_continue'] ) && ( is_numeric( $_POST['db_continue'] ) ) ) {
			// do nothing
		} else {
			$_POST['db_continue'] = 0;
		}
		
		
		// Look through and try to find .SQL file to import.
		$possible_sql_files = array( // Possible locations of .SQL file.
			pb_backupbuddy::$options['temp_serial_directory'] . '/db_1.sql',										// Determined from detecting DAT file. Should always be the location really... As of v4.1.
			ABSPATH . 'wp-content/uploads/temp_'.pb_backupbuddy::$options['zip_id'].'/db.sql',						// Full backup < v2.0.
			ABSPATH . 'db.sql',																						// Database backup < v2.0.
			ABSPATH . 'wp-content/uploads/backupbuddy_temp/' . pb_backupbuddy::$options['zip_id'] . '/db_1.sql',	// Full backup >= v2.0.
			ABSPATH . 'db_1.sql',																					// Database backup >= v2.0.
		);
		if ( !defined( 'PB_STANDALONE' ) ) { // When in WordPress add import paths also.
			$possible_sql_files[] = ABSPATH . 'wp-content/uploads/backupbuddy_temp/import_' . pb_backupbuddy::$options['zip_id'] . '/wp-content/uploads/backupbuddy_temp/' . pb_backupbuddy::$options['zip_id'] . '/db_1.sql';		// Multisite import >= 2.0.
			$possible_sql_files[] = pb_backupbuddy::$options['database_directory'] . 'db_1.sql';																																	// Multisite import >= v2.0.
		}
		$sql_file = '';
		pb_backupbuddy::status( 'details', 'Determining SQL file location...' );
		foreach( $possible_sql_files as $possible_sql_file ) { // Check each file location to see which hits.
			pb_backupbuddy::status( 'details', 'Looking for SQL file at `' . $possible_sql_file . '`.' );
			if ( file_exists( $possible_sql_file ) ) {
				$sql_file = $possible_sql_file; // Set SQL file location.
				pb_backupbuddy::status( 'details', 'Found SQL file as `' . $sql_file . '`.' );
				break; // Search is over. Use ths found file.
			}
		} // End foreach().
		unset( $possible_sql_files );
		if ( $sql_file == '' ) {
			pb_backupbuddy::status( 'error', 'Unable to find db_1.sql or other expected database file in the extracted files in the expected location. Make sure you did not rename your backup ZIP file. You may manually restore your SQL file if you can find it via phpmyadmin or similar tool then on Step 1 of ImportBuddy select the advanced option to skip database import. This will allow you to proceed.' );
			return false;
		}
		
		// Whether or not to ignore existing tables errors.
		if ( pb_backupbuddy::$options['ignore_sql_errors'] != false ) {
			$ignore_existing = true;
		} else {
			$ignore_existing = false;
		}
		
		
		/********** Start mysqlbuddy use **********/
		
		$force_mysqlbuddy_methods = array(); // Default, not forcing of methods.
		if ( pb_backupbuddy::$options['mysqlbuddy_compatibility'] != false ) { // mysqldump compatibility mode.
			$force_mysqlbuddy_methods = array( 'php' );
		}
		
		require_once( pb_backupbuddy::plugin_path() . '/lib/mysqlbuddy/mysqlbuddy.php' );
		pb_backupbuddy::$classes['mysqlbuddy'] = new pb_backupbuddy_mysqlbuddy( pb_backupbuddy::$options['db_server'], pb_backupbuddy::$options['db_name'], pb_backupbuddy::$options['db_user'], pb_backupbuddy::$options['db_password'], pb_backupbuddy::$options['db_prefix'], $force_mysqlbuddy_methods ); // $database_host, $database_name, $database_user, $database_pass, $old_prefix, $force_method = array()
		
		$import_result = pb_backupbuddy::$classes['mysqlbuddy']->import( $sql_file, pb_backupbuddy::$options['dat_file']['db_prefix'], $_POST['db_continue'], $ignore_existing );
		
		/********** End mysqlbuddy use **********/
		
		
		if ( true === $import_result ) { // Fully finished successfully.
			return( array( true, true ) );
		} elseif ( false === $import_result ) { // Full failure.
			return( array( false, false ) );
		} else { // Needs to chunk up DB import and continue...
			//$db_continue = true;
			// Continue on query $import_result...
			pb_backupbuddy::status( 'message', 'Next step will begin import on query ' . $import_result . '.' );
			return array( true, $import_result );
		}
	}
	
	
} // End import_database().



pb_backupbuddy::load_view( 'html_4', $data );
?>