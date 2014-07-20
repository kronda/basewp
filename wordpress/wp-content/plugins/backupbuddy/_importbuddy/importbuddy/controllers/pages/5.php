<?php
if ( ! defined( 'PB_IMPORTBUDDY' ) || ( true !== PB_IMPORTBUDDY ) ) {
	die( '<html></html>' );
}

Auth::require_authentication(); // Die if not logged in.

$data = array(
	'step'		=>		'5',
);


pb_backupbuddy::set_greedy_script_limits( true );




/**
 *	migrate_database()
 *
 *	Connects database and performs migration of DB content. Handles skipping.
 *
 *	@return		null
 */
function migrate_database() {
	pb_backupbuddy::$classes['import']->connect_database();
	
	if ( false === pb_backupbuddy::$options['skip_database_migration'] ) {
		return pb_backupbuddy::$classes['import']->migrate_database();
	} else {
		pb_backupbuddy::status( 'message', 'Skipping database migration based on settings.' );
		return true;
	}
}


/**
 *	migrate_wp_config()
 *
 *	Passthrough for suture use; trying to funnel all essential migration steps through the API files.
 *
 *	@return		true on success, new wp config file content on failure.
 */
function migrate_wp_config() {
	if ( isset( pb_backupbuddy::$options['dat_file']['wp-config_in_parent'] ) ) {
		if ( pb_backupbuddy::$options['dat_file']['wp-config_in_parent'] === true ) { // wp-config.php used to be in parent. Must copy from temp dir to root.
			pb_backupbuddy::status( 'details', 'DAT file indicates wp-config.php was previously in the parent directory. Copying into site root.' );
			
			$config_source = ABSPATH . 'wp-content/uploads/backupbuddy_temp/' . pb_backupbuddy::$options['zip_id'] . '/wp-config.php';
			$result = copy( $config_source, ABSPATH . 'wp-config.php' );
			if ( $result === true ) {
				pb_backupbuddy::status( 'message', 'wp-config.php file was restored to the root of the site `' . ABSPATH . 'wp-config.php`. It was previously in the parent directory of the source site. You may move it manually to the parent directory.' );
			} else {
				pb_backupbuddy::status( 'error', 'Unable to move wp-config.php file from temporary location `' . $config_source . '` to root.' );
			}
			
		} else { // wp-config.php was in normal location on source site. Nothing to do.
			pb_backupbuddy::status( 'details', 'DAT file indicates wp-config.php was previously in the normal location.' );
		}
	} else { // Pre 3.0 backup
		pb_backupbuddy::status( 'details', 'Backup pre-v3.0 so wp-config.php must be in normal location.' );
	}
	
	if ( 'files' == pb_backupbuddy::$options['dat_file']['backup_type'] ) {
		pb_backupbuddy::status( 'details', 'Skipping update of Database Settings and URLs in wp-config.php as this is a Files Only Backup.' );
		$migrateResult = true;
	} else {
		pb_backupbuddy::status( 'details', 'Updating Database Settings and URLs in wp-config.php as this is not a Files Only Backup.' );
		$migrateResult = pb_backupbuddy::$classes['import']->migrate_wp_config();
	}
	return $migrateResult;
}


/*	verify_database()
 *	
 *	Verify various contents of the database after all migration is complete.
 *	
 *	@param		
 *	@return		
 */
function verify_database() {
	
	pb_backupbuddy::$classes['import']->connect_database();
	$db_prefix = pb_backupbuddy::$options['db_prefix'];
	
	// Check site URL.
	$result = mysql_query( "SELECT option_value FROM `{$db_prefix}options` WHERE option_name='siteurl' LIMIT 1" );
	if ( $result === false ) {
		pb_backupbuddy::status( 'error', 'Unable to retrieve siteurl from database. A portion of the database may not have imported (or with the wrong prefix).' );
	} else {
		while( $row = mysql_fetch_row( $result ) ) {
			pb_backupbuddy::status( 'details', 'Final site URL: `' . $row[0] . '`.' );
		}
		mysql_free_result( $result ); // Free memory.
	}
	
	// Check home URL.
	$result = mysql_query( "SELECT option_value FROM `{$db_prefix}options` WHERE option_name='home' LIMIT 1" );
	if ( $result === false ) {
		pb_backupbuddy::status( 'error', 'Unable to retrieve home [url] from database. A portion of the database may not have imported (or with the wrong prefix).' );
	} else {
		while( $row = mysql_fetch_row( $result ) ) {
			pb_backupbuddy::status( 'details', 'Final home URL: `' . $row[0] . '`.' );
		}
	}
	@mysql_free_result( $result ); // Free memory.
	
	// Verify media upload path.
	$result = mysql_query( "SELECT option_value FROM `{$db_prefix}options` WHERE option_name='upload_path' LIMIT 1" );
	if ( $result === false ) {
		pb_backupbuddy::status( 'error', 'Unable to retrieve upload_path from database table ' . "`{$db_prefix}options`" . '. A portion of the database may not have imported (or with the wrong prefix).' );
		$media_upload_path = '{ERR_34834984-UNKNOWN}';
	} else {
		while( $row = mysql_fetch_row( $result ) ) {
			$media_upload_path = $row[0];
		}
	}
	@mysql_free_result( $result ); // Free memory.
	
	pb_backupbuddy::status( 'details', 'Media upload path in database options table: `' . $media_upload_path . '`.' );
	if ( substr( $media_upload_path, 0, 1 ) == '/' ) { // Absolute path.
		if ( !file_exists( $media_upload_path ) ) { // Media path does not exist.
			$media_upload_message = 'Your media upload path is assigned a directory which does not appear to exist on this server. Please verify it is correct in your WordPress settings. Current path: `' . $media_upload_path . '`.';
			pb_backupbuddy::alert( $media_upload_message );
			pb_backupbuddy::status( 'warning', $media_upload_message );
		} else { // Media path does exist.
			pb_backupbuddy::status( 'details', 'Your media upload path is assigned an absolute path which appears to be correct.' );
		}
	} else { // Relative path.
		pb_backupbuddy::status( 'details', 'Your media upload path is assigned a relative path; validity not tested.' );
	}
	
} // End verify_database().



/* scrub_maintenance_file()
 *
 * Deletes .maintenance file if it appears to have the contents that ImportBuddy created it with.
 * Non-importbuddy created .maintenance files are left in place to be warned about later as the user may want it there.
 *
 */
function scrub_maintenance_file() {
	if ( file_exists( ABSPATH . '.maintenance' ) ) {
		pb_backupbuddy::status( 'details', '.maintenance file exists. Checking to see if ImportBuddy generated it.' );
		$maintenance_contents = @file_get_contents( ABSPATH . '.maintenance' );
		if ( false === $maintenance_contents ) { // Cannot read.
			pb_backupbuddy::status( 'error', '.maintenance file unreadable. You may need to manually delete it to view your site.' );
		} else { // Read file succeeded.
			if ( trim( $maintenance_contents ) == "<?php die( 'Site undergoing maintenance.' ); ?>" ) { // Our file. Delete it!
				$maintenance_unlink = @unlink( ABSPATH . '.maintenance' );
				if ( true === $maintenance_unlink ) {
					pb_backupbuddy::status( 'details', 'Temporary .maintenance file created by ImportBuddy successfully deleted.' );
				} else {
					pb_backupbuddy::status( 'error', 'Unable to delete temporary .maintenance file.  This is likely due to permissions. You may need to manually delete it to view your site.' );
				}
			} else { // Not our file. Leave alone. We will warn about this later though.
				pb_backupbuddy::status( 'details', '.maintenance file not generated by ImportBuddy. Leaving as is. You may need to delete it to view your site.' );
			}
		}
	} else { // No .maintenance file.
		pb_backupbuddy::status( 'details', '.maintenance file not found. Skipping deletion.' );
	}
} // End scrub_maintenance_file().



/* scrub_index_file()
 *
 * Deletes index.htm file if it appears to have the contents that ImportBuddy created it with.
 * Non-importbuddy created index.htm files are left in place to be warned about later as the user may want it there.
 *
 */
function scrub_index_file() {
	if ( file_exists( ABSPATH . 'index.htm' ) ) {
		pb_backupbuddy::status( 'details', 'index.htm file exists. Checking to see if ImportBuddy generated it.' );
		$index_contents = @file_get_contents( ABSPATH . 'index.htm' );
		if ( false === $index_contents ) { // Cannot read.
			pb_backupbuddy::status( 'error', 'index.htm file unreadable. You may need to manually delete it to view your site.' );
		} else { // Read file succeeded.
			if ( trim( $index_contents ) == '<html></html>' ) { // Our file. Delete it!
				$index_unlink = @unlink( ABSPATH . 'index.htm' );
				if ( true === $index_unlink ) {
					pb_backupbuddy::status( 'details', 'Temporary index.htm file created by ImportBuddy successfully deleted.' );
				} else {
					pb_backupbuddy::status( 'error', 'Unable to delete temporary index.htm file.  This is likely due to permissions. You may need to manually delete it to view your site.' );
				}
			} else { // Not our file. Leave alone. We will warn about this later though.
				pb_backupbuddy::status( 'details', 'index.htm file not generated by ImportBuddy. Leaving as is. You may need to delete it to view your site.' );
			}
		}
	} else { // No index.htm file.
		pb_backupbuddy::status( 'details', 'index.htm file not found. Skipping deletion.' );
	}
} // End scrub_index_file().



/*	rename_htaccess_temp_back()
 *	
 *	Renames .htaccess to .htaccess.bb_temp until last ImportBuddy step to avoid complications.
 *	
 *	@return		null
 */
function rename_htaccess_temp_back() {
	
	if ( !file_exists( ABSPATH . '.htaccess.bb_temp' ) ) {
		pb_backupbuddy::status( 'details', 'No `.htaccess.bb_temp` file found. Skipping temporary file rename.' );
	}
	
	$result = @rename( ABSPATH . '.htaccess.bb_temp', ABSPATH . '.htaccess' );
	if ( $result === true ) { // Rename succeeded.
		pb_backupbuddy::status( 'message', 'Renamed `.htaccess.bb_temp` file to `.htaccess` until final ImportBuddy step.' );
	} else { // Rename failed.
		pb_backupbuddy::status( 'error', 'Unable to rename `.htaccess.bb_temp` file to `.htaccess`. Your file permissions may be too strict. You may wish to manually rename this file and/or check permissions before proceeding.' );
	}
	
} // End rename_htaccess_temp_back().



/* trouble_scan()
 *
 * Scans for potential problems and provided informative warnings.
 *
 * @return array Array of text warnings to display to user.
 *
 */
function trouble_scan() {
	$trouble = array();
	
	// .maintenance
	if ( file_exists( ABSPATH . '.maintenance' ) ) {
		$trouble[] = '.maintenance file found in WordPress root. The site may not be accessible unless this file is deleted.';
	}
	
	// index.htm
	if ( file_exists( ABSPATH . 'index.htm' ) ) {
		$trouble[] = 'index.htm file found in WordPress root. This may prevent WordPress from loading on some servers & may need to be deleted.';
	}
	
	// index.html
	if ( file_exists( ABSPATH . 'index.html' ) ) {
		$trouble[] = 'index.html file found in WordPress root. This may prevent WordPress from loading on some servers & may need to be deleted.';
	}
	
	// wp-config.php
	if ( ! file_exists( ABSPATH . 'wp-config.php' ) ) {
		$trouble[] = 'Warning only: wp-config.php file not found WordPress root. <i>If this is a database-only restore you should restore a full backup first.</i>';
	} else { // wp-config.php exists so check for unchanged URLs not updated due to provenance unknown.
		
		if ( 'files' == pb_backupbuddy::$options['dat_file']['backup_type'] ) {
			pb_backupbuddy::status( 'details', 'Skipping URL scan for wp-config.php as this is a Files Only restore.' );
		} else {
			pb_backupbuddy::status( 'details', 'Checking wp-config.php file for unchanged URLs.' );
			$config_contents = @file_get_contents( ABSPATH . 'wp-config.php' );
			if ( false === $config_contents ) { // Unable to open.
				pb_backupbuddy::status( 'error', 'Unable to open wp-config.php for checking though it exists. Verify permissions.' );
			} else { // Able to open.
				
				preg_match_all( '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $config_contents, $matches );
				$matches = $matches[0];
				foreach( $matches as $match ) {
					if ( false !== stristr( $match, 'api.wordpress.org' ) ) {
						continue;
					}
					if ( false !== stristr( $match, 'codex.wordpress.org' ) ) {
						continue;
					}
					$trouble[] = 'A URL found in one or more locations in wp-config.php was not migrated as it was either not recognized or in an unrecognized location in the file: "' . htmlentities( $match ) . '".';
				}
				
				if ( false !== stristr( $config_contents, 'COOKIE_DOMAIN' ) ) { // Found cookie domain.
					$trouble[] = 'Cookie domain set in wp-config.php file and has not been updated. You may need to manualy update this.';
				}
			}
		}
	}
	
	// .htaccess
	if ( ! file_exists( ABSPATH . '.htaccess' ) ) {
		$trouble[] = 'Warning only: .htaccess file not found in WordPress root. This is used for permalinks on servers which support it. If needed or URLs result in a 404 you may regenerate this file by logging into the wp-admin & navigating to Settings: Permalinks and clicking "Save".';
	}
	
	// php.ini
	if ( file_exists( ABSPATH . 'php.ini' ) ) {
		$trouble[] = 'A php.ini file was restored in the import process in the site root. This may cause problems with site functionality if imported to a different server as configuration options often differ between servers, possibly resulting in degraded performance or unexpected behavior.';
	}
	
	if ( count( $trouble ) > 0 ) {
		pb_backupbuddy::status( 'warning', 'Potential problems that may need your attention: ' . implode( '; ', $trouble ) );
	} else {
		pb_backupbuddy::status( 'details', 'No potential problems detected.' );
	}
	
	return $trouble;
	
} // End trouble_scan().



if ( $mode == 'html' ) {
	pb_backupbuddy::load_view( 'html_5', $data );
} else { // API mode.
	$result = migrate_database();
	if ( $result === true ) {
		migrate_wp_config();
		if ( ( true === pb_backupbuddy::$options['skip_database_import'] ) && ( true === pb_backupbuddy::$options['skip_database_migration'] ) ) {
			pb_backupbuddy::status( 'details', 'Skipping database verification as both Import and Migration steps were skipped so no modifications were made to it.' );
		} else {
			verify_database();
		}
	}
}
?>
