<?php
class pb_backupbuddy_import {
	
	
	
	/**
	 *	migrate_htaccess()
	 *
	 *	Migrates .htaccess.bb_temp file if it exists.
	 *
	 *	@return		boolean		False only if file is unwritable. True if write success; true if file does not even exist.
	 *
	 */
	function migrate_htaccess() {
		if ( pb_backupbuddy::$options['skip_htaccess'] == true ) {
			pb_backupbuddy::status( 'message', 'Skipping .htaccess migration based on settings.' );
			return true;
		}
		
		$htaccessFile = ABSPATH . '.htaccess.bb_temp';
		
		// If no .htaccess.bb_temp file exists then create a basic default one then migrate that as needed. @since 2.2.32.
		if ( ! file_exists( $htaccessFile ) ) {
			pb_backupbuddy::status( 'message', 'No `' . basename( $htaccessFile ) . '` file found. Creating basic default .htaccess.bb_temp file (to be later renamed to .htaccess).' );
			
			// Default .htaccess file.
			$htaccess_contents = 
"# BEGIN WordPress\n
<IfModule mod_rewrite.c>\n
RewriteEngine On\n
RewriteBase /\n
RewriteRule ^index\\.php$ - [L]\n
RewriteCond %{REQUEST_FILENAME} !-f\n
RewriteCond %{REQUEST_FILENAME} !-d\n
RewriteRule . /index.php [L]\n
</IfModule>\n
# END WordPress\n";
			file_put_contents( $htaccessFile, $htaccess_contents );
			unset( $htaccess_contents );
		}
		
		pb_backupbuddy::status( 'message', 'Migrating `' . basename( $htaccessFile ) . '` file...' );
		
		$oldurl = strtolower( pb_backupbuddy::$options['dat_file']['siteurl'] );
		$oldurl = str_replace( '/', '\\', $oldurl );
		$oldurl = str_replace( 'http:\\', '', $oldurl );
		$oldurl = trim( $oldurl, '\\' );
		$oldurl = explode( '\\', $oldurl );
		$oldurl[0] = '';
		
		$old_path = implode( '/', $oldurl );
		
		$newurl = strtolower( pb_backupbuddy::$options['siteurl'] );
		$newurl = str_replace( '/', '\\', $newurl );
		$newurl = str_replace( 'http:\\', '', $newurl );
		$newurl = trim( $newurl, '\\' );
		$newurl = explode( '\\', $newurl );
		$newurl[0] = '';
		
		pb_backupbuddy::status( 'message', 'Checking `' . basename( $htaccessFile ) . '` file.' );
		
		// If the URL (domain and/or URL subdirectory ) has changed, then need to update .htaccess.bb_temp file.
		if ( $newurl !== $oldurl ) {
			pb_backupbuddy::status( 'message', 'URL directory has changed. Updating from `' . implode( '/', $oldurl ) . '` to `' . implode( '/', $newurl ) . '`.' );
			
			$rewrite_lines = array();
			$got_rewrite = false;
			$rewrite_path = implode( '/', $newurl );
			$file_array = file( $htaccessFile );
			
			
			// Loop through .htaccess lines, updating as needed.
			foreach ( (array)$file_array as $line_number => $line) {
				if ( $got_rewrite == true ) { // In a WordPress section.
					if ( strstr( $line, 'END WordPress' ) ) { // End of a WordPress block so stop replacing.
						$got_rewrite = false;
						$rewrite_lines[] =  $line; // Captures end of WordPress block.
					} else {
						if ( strstr( $line, 'RewriteBase' ) ) { // RewriteBase
							$rewrite_lines[] = 'RewriteBase ' . $rewrite_path . '/' . "\n";
						} elseif ( strstr( $line, 'RewriteRule' ) ) { // RewriteRule
							if ( strstr( $line, '^index\.php$' ) ) { // Handle new strange rewriterule. Leave as is.
								$rewrite_lines[] = $line;
								pb_backupbuddy::status( 'details', '.htaccess ^index\.php$ detected. Leaving as is.' );
							} elseif ( ! strstr( $line, 'RewriteRule . ') ) {
								// Handle what is probably a user generated rule - better detection needed.
								$new_line = str_replace( $old_path, $rewrite_path, $line );
								$rewrite_lines[] = $new_line;
								if ( $new_line != $line ) {
									pb_backupbuddy::status( 'message', '.htaccess line changed from `' . trim( $line ) . '` to `' . trim( $new_line ) . '`.' );
								}
							} else { // Normal spot.
								$rewrite_lines[] = 'RewriteRule . ' . $rewrite_path . '/index.php' . "\n";
							}
						} else {
							$rewrite_lines[] =  $line; // Captures everything inside WordPress block we arent modifying.
							if ( false !== strstr( $line, 'RewriteRule . ') ) { // RewriteRule, warn user potentially if path may need changed.
								if ( $old_path !== $rewrite_path ) {
									pb_backupbuddy::status( 'warning', 'User-defined RewriteRule found and WordPress path has changed so this rule MAY need manually updated by you to function properly.  Line: "' . $line . '".' );
								}
							}
						}
					}
					
					
				} else { // Outside a WordPress section.
					if ( strstr( $line, 'BEGIN WordPress' ) ) {
						$got_rewrite = true; // Beginning of a WordPress block so start replacing.
					}
					$rewrite_lines[] =  $line; // Captures everything outside of WordPress block.
				}
			} // end foreach.
			
			// Check that we can write to this file (if it already exists).
			if ( file_exists( $htaccessFile ) && ( ! is_writable( $htaccessFile ) ) ) {
				pb_backupbuddy::status( 'warning', 'Warning #28573: Temp `' . basename( $htaccessFile ) . '` file shows to be unwritable. Attempting to override permissions temporarily.' );
				$oldPerms = ( fileperms( $htaccessFile ) & 0777 );
				@chmod( $htaccessFile, 0644 ); // Try to make writable.
				// Check if still not writable...
				if ( ! is_writable( $htaccessFile ) ) {
					pb_backupbuddy::status( 'error', 'Error #9020: Unable to write to `' . basename( $htaccessFile ) . '` file. Verify permissions.' );
					pb_backupbuddy::alert( 'Warning: Unable to write to temporary .htaccess file. Verify this file has proper write permissions. You may receive 404 Not Found errors on your site if this is not corrected. To fix after migration completes: Log in to your WordPress admin and select Settings: Permalinks from the left menu then save. To manually update, copy/paste the following into your .htaccess file: <textarea>' . implode( $rewrite_lines ) . '</textarea>', '9020' );
					return false;
				}
			}
			
			$handling = fopen( $htaccessFile, 'w');
			fwrite( $handling, implode( $rewrite_lines ) );
			fclose( $handling );
			unset( $handling );
			
			// Restore prior permissions if applicable.
			if ( isset( $oldPerms ) ) {
				@chmod( $htaccessFile, $oldPerms );
			}
			
			pb_backupbuddy::status( 'message', 'Migrated `' . basename( $htaccessFile ) . '` file. It will be renamed back to `.htaccess` on the final step.' );
		} else {
			pb_backupbuddy::status( 'message', 'No changes needed for `' . basename( $htaccessFile ) . '` file.' );
		}
		
		return true;
	} // End migrate_htaccess().
	
	
	
	/**
	 *	wipe_database()
	 *
	 *	Clear out the existing database to prepare for importing new data.
	 *
	 *	@return			boolean		Currently always true.
	 */
	function wipe_database( $prefix ) {
		if ( $prefix == '' ) {
			pb_backupbuddy::status( 'warning', 'No database prefix specified to wipe.' );
			return false;
		}
		pb_backupbuddy::status( 'message', 'Beginning wipe of database tables matching prefix `' . $prefix . '`...' );
		
		// Connect to database.
		$this->connect_database();
		
		global $wpdb;
		$rows = $wpdb->get_results( "SELECT table_name FROM information_schema.tables WHERE table_name LIKE '" . backupbuddy_core::dbEscape( str_replace( '_', '\_', $prefix ) ) . "%' AND table_schema = DATABASE()", ARRAY_A );
		$table_wipe_count = count( $rows );
		foreach( $rows as $row ) {
			pb_backupbuddy::status( 'details', 'Dropping table `' . $row['table_name'] . '`.' );
			$wpdb->query( 'DROP TABLE `' . $row['table_name'] . '`' );
		}
		unset( $rows );
		pb_backupbuddy::status( 'message', 'Wiped database of ' . $table_wipe_count . ' tables.' );
		
		return true;
	} // End wipe_database().
	
	
	
	/**
	 *	wipe_database()
	 *
	 *	Clear out the existing database to prepare for importing new data.
	 *
	 *	@return			boolean		Currently always true.
	 */
	function wipe_database_all( $confirm = false ) {
		if ( $confirm !== true ) {
			die( 'Error #5466566: Parameter 1 to wipe_database_all() must be boolean true to proceed.' );
		}
		
		pb_backupbuddy::status( 'message', 'Beginning wipe of ALL database tables...' );
		
		// Connect to database.
		$this->connect_database();
		
		global $wpdb;
		$rows = $wpdb->get_results( "SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE()", ARRAY_A );
		$table_wipe_count = count( $rows );
		foreach( $rows as $row ) {
			pb_backupbuddy::status( 'details', 'Dropping table `' . $row['table_name'] . '`.' );
			$wpdb->query( 'DROP TABLE `' . $row['table_name'] . '`' );
		}
		unset( $rows );
		pb_backupbuddy::status( 'message', 'Wiped database of ' . $table_wipe_count . ' tables.' );
		
		return true;
	} // End wipe_database_all().
	
	
	
	/*	preg_escape_back()
	 *	
	 *	Escape backreferences from string for use with regex. Used by migrate_wp_config().
	 *	@see migrate_wp_config()
	 *	
	 *	@param		string		$string		String to escape.
	 *	@return		string					Escaped string.
	 */
	function preg_escape_back($string) {
		// Replace $ with \$ and \ with \\
		$string = preg_replace('#(?<!\\\\)(\\$|\\\\)#', '\\\\$1', $string);
		return $string;
	} // End preg_escape_back().
	
	
	
	/**
	 *	migrate_wp_config()
	 *
	 *	Migrates and updates the wp-config.php file contents as needed.
	 *
	 *	@return			true|string			True on success. On false returns the new wp-config file content.
	 */
	function migrate_wp_config() {
		pb_backupbuddy::status( 'message', 'Starting migration of wp-config.php file...' );
		
		pb_backupbuddy::flush();
		
		$configFile = ABSPATH . 'wp-config.php';
		pb_backupbuddy::status( 'details', 'Config file: `' . $configFile . '`.' );

		if ( file_exists( $configFile ) ) {
			// Useful REGEX site: http://gskinner.com/RegExr/
			
			$updated_home_url = false;
			$wp_config = array();
			$lines = file( $configFile );
			
			$patterns = array();
			$replacements = array();
			
			/*
			Update WP_SITEURL, WP_HOME if they exist.
			Update database DB_NAME, DB_USER, DB_PASSWORD, and DB_HOST.
			RegExp: /define\([\s]*('|")WP_SITEURL('|"),[\s]*('|")(.)*('|")[\s]*\);/gi
			pattern: define\([\s]*('|")WP_SITEURL('|"),[\s]*('|")(.)*('|")[\s]*\);
			*/
			$pattern[0] = '/define\([\s]*(\'|")WP_SITEURL(\'|"),[\s]*(\'|")(.)*(\'|")[\s]*\);/i';
			$replace[0] = "define( 'WP_SITEURL', '" . trim( pb_backupbuddy::$options['siteurl'], '/' ) . "' );";
			pb_backupbuddy::status( 'details', 'wp-config.php: Setting WP_SITEURL (if applicable) to `' . trim( pb_backupbuddy::$options['siteurl'], '/' ) . '`.' );
			$pattern[1] = '/define\([\s]*(\'|")WP_HOME(\'|"),[\s]*(\'|")(.)*(\'|")[\s]*\);/i';
			$replace[1] = "define( 'WP_HOME', '" . trim( pb_backupbuddy::$options['home'], '/' ) . "' );";
			pb_backupbuddy::status( 'details', 'wp-config.php: Setting WP_HOME (if applicable) to `' . trim( pb_backupbuddy::$options['home'], '/' ) . '`.' );
			
			$pattern[2] = '/define\([\s]*(\'|")DB_NAME(\'|"),[\s]*(\'|")(.)*(\'|")[\s]*\);/i';
			$replace[2] = "define( 'DB_NAME', '" . pb_backupbuddy::$options['db_name'] . "' );";
			$pattern[3] = '/define\([\s]*(\'|")DB_USER(\'|"),[\s]*(\'|")(.)*(\'|")[\s]*\);/i';
			$replace[3] = "define( 'DB_USER', '" . pb_backupbuddy::$options['db_user'] . "' );";
			$pattern[4] = '/define\([\s]*(\'|")DB_PASSWORD(\'|"),[\s]*(\'|")(.)*(\'|")[\s]*\);/i';
			$replace[4] = "define( 'DB_PASSWORD', '" . $this->preg_escape_back( pb_backupbuddy::$options['db_password'] ) . "' );";
			$pattern[5] = '/define\([\s]*(\'|")DB_HOST(\'|"),[\s]*(\'|")(.)*(\'|")[\s]*\);/i';
			$replace[5] = "define( 'DB_HOST', '" . pb_backupbuddy::$options['db_server'] . "' );";
			
			// If multisite, update domain.
			if ( pb_backupbuddy::$options['domain'] != '' ) {
				$pattern[6] = '/define\([\s]*(\'|")DOMAIN_CURRENT_SITE(\'|"),[\s]*(\'|")(.)*(\'|")[\s]*\);/i';
				$replace[6] = "define( 'DOMAIN_CURRENT_SITE', '" . pb_backupbuddy::$options['domain'] . "' );";
				pb_backupbuddy::status( 'details', 'wp-config.php: Setting DOMAIN_CURRENT_SITE (if applicable) to `' . pb_backupbuddy::$options['domain'] . '`.' );
			} else {
				pb_backupbuddy::status( 'details', 'wp-config.php did not update DOMAIN_CURRENT_SITE as it was blank.' );
			}
			/*
			Update table prefix.
			RegExp: /\$table_prefix[\s]*=[\s]*('|")(.)*('|");/gi
			pattern: \$table_prefix[\s]*=[\s]*('|")(.)*('|");
			*/
			$pattern[7] = '/\$table_prefix[\s]*=[\s]*(\'|")(.)*(\'|");/i';
			$replace[7] = '$table_prefix = \'' . pb_backupbuddy::$options['db_prefix'] . '\';';
			
			
			// Perform the actual replacement.
			$lines = preg_replace( $pattern, $replace, $lines );
			
			// Check that we can write to this file.
			if ( ! is_writable( $configFile ) ) {
				pb_backupbuddy::status( 'warning', 'Warning #28572: wp-config.php shows to be unwritable. Attempting to override permissions temporarily.' );
				$oldPerms = ( fileperms( $configFile ) & 0777 );
				@chmod( $configFile, 0644 ); // Try to make writable.
			}
			
			// Write changes to config file.
			if ( false === ( file_put_contents( $configFile, $lines ) ) ) {
				pb_backupbuddy::alert( 'ERROR #84928: Unable to save changes to wp-config.php. Verify this file has proper write permissions. You may need to manually edit it.', true, '9020' );
				return implode( "\n", $lines );
			}
			
			// Restore prior permissions if applicable.
			if ( isset( $oldPerms ) ) {
				@chmod( $configFile, $oldPerms );
			}

			unset( $lines );
		} else {
			pb_backupbuddy::status( 'warning', 'Warning: wp-config.php file not found.' );
			//pb_backupbuddy::alert( 'Note: wp-config.php file not found. This is normal for a database only backup.' );
		}
		
		pb_backupbuddy::status( 'message', 'Migration of wp-config.php complete.' );
		
		return true;
	} // End migrate_wp_config().
	
	
	
	// TODO: switch to using pb_backupbuddy::status_box() instead.
	/**
	 *	status_box()
	 *
	 *	Displays a textarea for placing status text into.
	 *
	 *	@param			$default_text	string		First line of text to display.
	 *	@param			boolean			$hidden		Whether or not to apply display: none; CSS.
	 *	@return							string		HTML for textarea.
	 */
	function status_box( $default_text = '', $hidden = false ) {
		define( 'PB_STATUS', true ); // Tells framework status() function to output future logging info into status box via javascript.
		$return = '<textarea readonly="readonly" id="importbuddy_status" wrap="off"';
		if ( $hidden === true ) {
			$return .= ' style="display: none; "';
		}
		$return .= '>' . $default_text . '</textarea>';
		
		return $return;
	}
	
	
	
	
		/**
	 *	connect()
	 *
	 *	Initializes a connection to the mysql database.
	 *
	 *	@return		boolean		True on success; else false. Success testing is very loose.
	 */
	function connect_database() {
		
		pb_backupbuddy::flush();
		
		global $wpdb;
		$wpdb = new wpdb( pb_backupbuddy::$options['db_user'], pb_backupbuddy::$options['db_password'], pb_backupbuddy::$options['db_name'], pb_backupbuddy::$options['db_server'] );
		
		pb_backupbuddy::flush();
		
		return true;
	}
	
	
	
	/**
	 *	migrate_database()
	 *
	 *	Migrates the already imported database's content for updates ABSPATH and URL.
	 *
	 *	@return		boolean		True=success, False=failed.
	 *
	 */
	function migrate_database() {
		require( 'importbuddy/classes/_migrate_database.php' );
		return $return;
	}
	
	
	
} // End class.
?>
