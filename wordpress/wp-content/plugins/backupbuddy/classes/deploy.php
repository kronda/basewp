<?php
// Dustin Bolton 2014.
class backupbuddy_deploy {
	
	private $_state = array();		// Holds current state data. Retrieve with getState() and pass onto next run in the constructor.
	private $_errors = array();		// Hold error strings to retrieve with getErrors().
	
	
	
	/* __construct()
	 *
	 * ROLLBACK, RESTORE
	 *
	 * @param	string	$type			Restore type: rollback (roll back from inside WordPress), restore (importbuddy)
	 * @param	array 	$existinData	State data from a previous instantiation. Previously returned from getState().
	 *
	 */
	public function __construct( $apiURL, $existingState = '' ) {
		pb_backupbuddy::status( 'details', 'Constructing deploy class.' );
		register_shutdown_function( array( &$this, 'shutdown_function' ) );
		
		if ( is_array( $existingState ) ) { // User passed along an existing state to resume.
			$this->_state = $existingState;
		} else { // Create new blank process & state.
			$this->_state = array(
				'apiURL' => $apiURL,
				'startTime' => time(),
			);
		}
		pb_backupbuddy::status( 'details', 'Deploy class constructed.' );
	} // End __construct().
	
	
	
	public function remoteAPI( $endpoint ) {
		$response = wp_remote_post( $this->_state['apiURL'], array(
			'method' => 'POST',
			'timeout' => 10,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'body' => array( 'backupbuddyVersion' => pb_backupbuddy::settings( 'version' ), 'run' => $endpoint ),
			'cookies' => array()
		    )
		);
		if ( is_wp_error( $response ) ) {
			return $this->_error( $response->get_error_message() );
		} else {
			if ( null === ( $return = json_decode( $response['body'], true ) ) ) {
				return $this->_error( 'Error #45434: Unable to decode json response `' . $response['body'] . '`.' );
			} else {
				if ( true !== $return['success'] ) {
					return $this->_error( 'Error #3289379: API did not report success.' );
				} else {
					return $return['data'];
				}
			}
		}
	}
	
	
	
	/* start()
	 *
	 * @return	bool		true on success, else false.
	 */
	public function start( $sourceInfo ) {
		$this->_before( __FUNCTION__ );
		
		$pingTimePre = microtime(true);
		$this->_state['remoteInfo'] = $this->remoteAPI( 'getPreDeployInfo' );
		$pingTimePost = microtime(true);
		$this->_state['remoteInfo']['pingTime'] = $pingTimePost - $pingTimePre;
		
		// Calculate plugins that do not match.
		$this->_state['sendPlugins'] = $this->calculatePluginDiff( $sourceInfo['activePlugins'], $this->_state['remoteInfo']['activePlugins'] );
		
		// Calculate themes that do not match.
		$this->_state['sendThemeFiles'] = $this->calculateThemeDiff( $sourceInfo['themeSignatures'], $this->_state['remoteInfo']['themeSignatures'] );
		
		//unset( $this->_state['remoteInfo']['themeSignatures'] );
		return true;
	} // End start().
	
	
	public function calculateThemeDiff( $sourceThemeSignatures, $destinationThemeSignatures ) {
		$updateThemeFiles = array(); // Theme files to send.
		// Loop through local theme files to see if they differ from anything on remote.
		foreach( $sourceThemeSignatures as $file => $signature ) {
			if ( ! isset( $destinationThemeSignatures[ $file ] ) ) { // File does not exist on destination.
				$updateThemeFiles[] = $file;
			} else { // File exists on remote. See if content is the same.
				if ( $signature['sha1'] != $destinationThemeSignatures[ $file ]['sha1'] ) { // Hash mismatch. Needs updating.
					$updateThemeFiles[] = $file;
				}
			}
		}
		return $updateThemeFiles;
	}
	
	public function calculatePluginDiff( $sourcePlugins, $destinationPlugins ) {
		$updatePlugins = array();
		foreach( $sourcePlugins as $sourceSlug => $sourcePlugin ) {
			if ( ! isset( $destinationPlugins[ $sourceSlug ] ) ) { // Plugin does not exist on destination.
				$updatePlugins[] = $sourceSlug;
			} else { // File exists on remote. See if content is the same.
				if ( $sourcePlugins[ $sourceSlug ]['version'] != $destinationPlugins[ $sourceSlug ]['version'] ) { // Version mismatch. Needs updating.
					$updatePlugins[] = $sourceSlug;
				}
			}
		}
		return $updatePlugins;
	}
	
	public function hashFileMap( $root ) {
		$generate_sha1 = true;
		
		echo 'mem:' . memory_get_usage(true) . '<br>';
		$files = (array) pb_backupbuddy::$filesystem->deepglob( $root );
		
		echo 'mem:' . memory_get_usage(true) . '<br>';
		$root_len = strlen( $root );
		$new_files = array();
		foreach( $files as $file_id => &$file ) {
			$stat = stat( $file );
			
			if ( FALSE === $stat ) {
				pb_backupbuddy::status( 'error', 'Unable to read file `' . $file . '` stat.' );
			}
			$new_file = substr( $file, $root_len );
			
			$sha1 = '';
			if ( ( true === $generate_sha1 ) && ( $stat['size'] < 1073741824 ) ) { // < 100mb
				$sha1 = sha1_file( $file );
			}
			
			$new_files[$new_file] = array(
				'scanned'	=>	time(),
				'size'		=> $stat['size'],
				'modified'	=> $stat['mtime'],
				'sha1'		=> $sha1,
				
				
				// TODO: don't render sha1 here? do it in a subsequent step(s) with cron to allow for more time? update fileoptions file every x number of tiles and a count attempts without proceeding to assume failure? max_overall attempts?
				
				
			);
			unset( $files[$file_id] ); // Better to free memory or leave out for performance?
			
		}
		unset( $files );
		echo 'mem:' . memory_get_usage(true) . '<br>';
		echo 'filecount: ' . count( $new_files ) . '<br>';
		print_r( $new_files );
	} // end crcMap().
	
	
	
	/* extractDatabase()
	 *
	 * ROLLBACK, RESTORE
	 * Extracts database file(s) into temp dir.
	 *
	 * @param	bool		true on success, else false.
	 */
	public function extractDatabase() {
		$this->_before( __FUNCTION__ );
		
		$this->_priorRollbackCleanup();
		
		pb_backupbuddy::status( 'details', 'Loading zipbuddy.' );
		require_once( pb_backupbuddy::plugin_path() . '/lib/zipbuddy/zipbuddy.php' );
		$zipbuddy = new pluginbuddy_zipbuddy( backupbuddy_core::getBackupDirectory() );
		pb_backupbuddy::status( 'details', 'Zipbuddy loaded.' );
		
		// Find SQL file location in archive.
		pb_backupbuddy::status( 'details', 'Calculating possible SQL file locations.' );
		$possibleSQLLocations = array();
		$possibleSQLLocations[] = trim( rtrim( str_replace( 'backupbuddy_dat.php', '', $this->_state['datLocation'] ), '\\/' ) . '/db_1.sql', '\\/' ); // SQL file most likely is in the same spot the dat file was.
		$possibleSQLLocations[] = 'db_1.sql'; // DB backup.
		$possibleSQLLocations[] = 'wp-content/uploads/backupbuddy_temp/' . $this->_state['serial'] . '/db_1.sql'; // Full backup.
		pb_backupbuddy::status( 'details', 'Possible SQL file locations: `' . implode( ';', $possibleSQLLocations ) . '`.' );
		$possibleSQLLocations = array_unique( $possibleSQLLocations );
		foreach( $possibleSQLLocations as $possibleSQLLocation ) {
			if ( true === $zipbuddy->file_exists( $this->_state['archive'], $possibleSQLLocation, $leave_open = true ) ) {
				$detectedSQLLocation = $possibleSQLLocation;
				break;
			}
		} // end foreach.
		pb_backupbuddy::status( 'details', 'Confirmed SQL file location: `' . $detectedSQLLocation . '`.' );
		
		// Get SQL file.
		$files = array( $detectedSQLLocation => 'db_1.sql' );
		pb_backupbuddy::$filesystem->unlink_recursive( $this->_state['tempPath'] ); // Remove if already exists.
		mkdir( $this->_state['tempPath'] ); // Make empty directory.
		require( pb_backupbuddy::plugin_path() . '/classes/_restoreFiles.php' );
		
		// Extract SQL file.
		pb_backupbuddy::status( 'details', 'Extracting SQL file(s).' );
		if ( false === backupbuddy_restore_files::restore( $this->_state['archive'], $files, $this->_state['tempPath'], $zipbuddy ) ) {
			return $this->_error( 'Error #85384: Unable to restore one or more database files.' );
		}
		
		pb_backupbuddy::status( 'details', 'Finished database extraction function.' );
		return true;
	} // End extractDatabase().
	
	
	
	/* _error()
	 *
	 * Logs error messages for retrieval with getErrors().
	 *
	 * @param	string		$message	Error message to log.
	 * @return	null
	 */
	private function _error( $message ) {
		$this->_errors[] = $message;
		pb_backupbuddy::status( 'error', $message );
		return false;
	}
	
	
	
	/* getErrors()
	 *
	 * Get any errors which may have occurred.
	 *
	 * @return	array 		Returns an array of string error messages.
	 */
	public function getErrors() {
		return $this->_errors;
	} // End getErrors();
	
	
	
	/* getState()
	 *
	 * Get state array data for passing to the constructor for subsequent calls.
	 *
	 * @return	array 		Returns an array of state data.
	 */
	public function getState() {
		pb_backupbuddy::status( 'details', 'Getting deploy state.' );
		return $this->_state;
	} // End getState().
	
	
	
	/* setState()
	 *
	 * Replace current state array with provided one.
	 *
	 */
	public function setState( $stateData ) {
		$this->_state = $stateData;
	} // End setState().
	
	
	
	/* _before()
	 *
	 * Runs before every function to keep track of ran functions in the state data for debugging.
	 *
	 * @return	null
	 */
	private function _before( $functionName ) {
		$this->_state['stepHistory'][] = array( 'function' => $functionName, 'start' => time() );
		pb_backupbuddy::status( 'details', 'Starting function `' . $functionName . '`.' );
		return;
	} // End _before().
	
	
	
	/*	shutdown_function()
	 *	
	 *	Used for catching fatal PHP errors during backup to write to log for debugging.
	 *	
	 *	@return		null
	 */
	public function shutdown_function() {
		
		// Get error message.
		// Error types: http://php.net/manual/en/errorfunc.constants.php
		$e = error_get_last();
		if ( $e === NULL ) { // No error of any kind.
			return;
		} else { // Some type of error.
			if ( !is_array( $e ) || ( $e['type'] != E_ERROR ) && ( $e['type'] != E_USER_ERROR ) ) { // Return if not a fatal error.
				return;
			}
		}
		
		$e_string = '';
		foreach( (array)$e as $e_line_title => $e_line ) {
			$e_string .= $e_line_title . ' => ' . $e_line . "\n";
		}
		
		pb_backupbuddy::status( 'error', 'FATAL PHP ERROR: ' . $e_string );
		
	} // End shutdown_function.
	
	
	
} // end class.

