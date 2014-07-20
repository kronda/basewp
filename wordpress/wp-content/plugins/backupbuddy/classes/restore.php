<?php
// Dustin Bolton 2014.
class backupbuddy_restore {
	
	
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
	public function __construct( $type, $existingState = '' ) {
		pb_backupbuddy::status( 'details', 'Constructing rollback class.' );
		
		if ( ( 'rollback' != $type ) && ( 'restore' != $type ) ) {
			$this->_error( 'Invalid restore type `' . htmlentities( $type ) . '`.' );
			return false;
		}
		
		register_shutdown_function( array( &$this, 'shutdown_function' ) );
		
		if ( is_array( $existingState ) ) { // User passed along an existing state to resume.
			$this->_state = $existingState;
		} else { // Create new blank rollback process & state.
			$this->_state = array(
				'type' => $type,
				'archive' => '',					// Full archive path & filename.
				'serial' => '',						// Calculated backup serial.
				'tempPath' => '',					// Temporary path to do extractions into. Trailing path.
				'data' => array(),					// DAT file array.
				'undoURL' => '',					// URL to the undo script, eg http://your.com/backupbuddy_rollback_undo-XXXXXXXX.php
				'forceMysqlMethods' => array(),		// mysql methods to force for importing. Default to empty array to auto-detect.
				'autoAdvance' => true,				// Whether or not to auto advance (ie for web-based rollback auto-refresh to next step).
				'maxExecutionTime' => null,			// If set then override detected max execution time.
				'dbImportPoint' => 0,				// For compat mode mysql, next row to start importo n.
				'stepHistory' => array(),			// Array of arrays of the step functions run thus far. Track start and finish times.
			);
		}
		pb_backupbuddy::status( 'details', 'Restore class constructed in `' . $type . '` mode.' );
	} // End __construct().
	
	
	
	/* start()
	 *
	 * ROLLBACK, RESTORE
	 * Returns false on failure. Use getErrors() to get an array of errors encountered if any.
	 * Returns an array of information on success.
	 * Grab the rollback state data with getState().
	 *
	 * @return	bool		true on success, else false.
	 */
	public function start( $backupFile ) {
		$this->_before( __FUNCTION__ );
		
		$this->_state['archive'] = $backupFile;
		$serial = backupbuddy_core::get_serial_from_file( basename( $backupFile ) );
		$this->_state['serial'] = $serial;
		$this->_state['tempPath'] = backupbuddy_core::getTempDirectory() . $this->_state['type'] . '_' . $this->_state['serial'] . '/';
		unset( $backupFile );
		unset( $serial );
		
		// Get zip meta information.
		$customTitle = 'Backup Details';
		pb_backupbuddy::status( 'details', 'Attempting to retrieve zip meta data from comment.' );
		if ( false !== ( $metaInfo = backupbuddy_core::getZipMeta( $this->_state['archive'] ) ) ) {
			pb_backupbuddy::status( 'details', 'Found zip meta data.' );
		} else {
			pb_backupbuddy::status( 'details', 'Did not find zip meta data.' );
		}
		//$this->_state['meta'] = $metaInfo;
		
		
		pb_backupbuddy::status( 'details', 'Loading zipbuddy.' );
		require_once( pb_backupbuddy::plugin_path() . '/lib/zipbuddy/zipbuddy.php' );
		$zipbuddy = new pluginbuddy_zipbuddy( backupbuddy_core::getBackupDirectory() );
		pb_backupbuddy::status( 'details', 'Zipbuddy loaded.' );
		
		// Find DAT file.
		pb_backupbuddy::status( 'details', 'Calculating possible DAT file locations.' );
		$possibleDatLocations = array();
		if ( isset( $metaInfo['dat_path'] ) ) {
			$possibleDatLocations[] = $metaInfo['dat_path'][1]; // DAT file location encoded in meta info. Should always be valid.
		}
		$possibleDatLocations[] = 'backupbuddy_dat.php'; // DB backup.
		$possibleDatLocations[] = 'wp-content/uploads/backupbuddy_temp/' . $this->_state['serial'] . '/backupbuddy_dat.php'; // Full backup.
		pb_backupbuddy::status( 'details', 'Possible DAT file locations: `' . implode( ';', $possibleDatLocations ) . '`.' );
		$possibleDatLocations = array_unique( $possibleDatLocations );
		foreach( $possibleDatLocations as $possibleDatLocation ) {
			if ( true === $zipbuddy->file_exists( $this->_state['archive'], $possibleDatLocation, $leave_open = true ) ) {
				$detectedDatLocation = $possibleDatLocation;
				break;
			}
		} // end foreach.
		pb_backupbuddy::status( 'details', 'Confirmed DAT file location: `' . $detectedDatLocation . '`.' );
		$this->_state['datLocation'] = $detectedDatLocation;
		
		unset( $metaInfo ); // No longer need anything from the meta information.
		
		
		// Load DAT file contents.
		pb_backupbuddy::status( 'details', 'Creating temporary file directory `' . $this->_state['tempPath'] . '`.' );
		pb_backupbuddy::$filesystem->unlink_recursive( $this->_state['tempPath'] ); // Remove if already exists.
		mkdir( $this->_state['tempPath'] ); // Make empty directory.
		
		// Restore DAT file.
		pb_backupbuddy::status( 'details', 'Extracting DAT file.' );
		$files = array( $detectedDatLocation => 'backupbuddy_dat.php' );
		require( pb_backupbuddy::plugin_path() . '/classes/_restoreFiles.php' );
		$result = backupbuddy_restore_files::restore( $this->_state['archive'], $files, $this->_state['tempPath'], $zipbuddy );
		echo '<script type="text/javascript">jQuery("#pb_backupbuddy_working").hide();</script>';
		pb_backupbuddy::flush();
		if ( false === $result ) {
			$this->_error( 'Error #85484: Unable to retrieve DAT file. This is a fatal error.' );
			return false;
		}
		if ( false === ( $datData = backupbuddy_core::get_dat_file_array( $this->_state['tempPath'] . 'backupbuddy_dat.php' ) ) ) {
			$this->_error( 'Error #4839484: Unable to retrieve DAT file. The backup may have failed opening due to lack of memory, permissions issues, or other reason. Use ImportBuddy to restore or check the Advanced Log above for details.' );
			return false;
		}
		$this->_state['dat'] = $datData;
		pb_backupbuddy::status( 'details', 'DAT file extracted.' );
		
		
		if ( site_url() != $this->_state['dat']['siteurl'] ) {
			$this->_error( __( 'Error #5849843: Site URL does not match. You cannot roll back the database if the URL has changed or for backups or another site. Use importbuddy.php to restore or migrate instead.', 'it-l10n-backupbuddy' ) );
			return false;
		}
		
		global $wpdb;
		if ( $this->_state['dat']['db_prefix'] != $wpdb->prefix ) {
			$this->_error( __( 'Error #2389394: Database prefix does not match. You cannot roll back the database if the database prefix has changed or for backups or another site. Use importbuddy.php to restore or migrate instead.', 'it-l10n-backupbuddy' ) );
			return false;
		}
		
		// Store this serial in the db for future cleanup.
		if ( 'rollback' == $this->_state['type'] ) {
			pb_backupbuddy::$options['rollback_cleanups'][ $this->_state['serial'] ] = time();
			pb_backupbuddy::save();
			
			// Generate UNDO script.
			pb_backupbuddy::status( 'details', 'Generating undo script.' );
			$this->_state['undoFile'] = 'backupbuddy_rollback_undo-' . $this->_state['serial'] . '.php';
			$undoURL = rtrim( site_url(), '/\\' ) . '/' . $this->_state['undoFile'];
			if ( false === copy( dirname( __FILE__ ) . '/_rollback_undo.php', ABSPATH . $this->_state['undoFile'] ) ) {
				$this->_error( __( 'Warning: Unable to create undo script in site root. You will not be able to automated undoing the rollback if something fails so BackupBuddy will not continue.', 'it-l10n-backupbuddy' ) );
				return false;
			}
			$this->_state['undoURL'] = $undoURL;
		}
		
		pb_backupbuddy::status( 'details', 'Finished starting function.' );
		return true;
	} // End start().
	
	
	
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
			$this->_error( 'Error #85384: Unable to restore one or more database files.' );
			return false;
		}
		
		pb_backupbuddy::status( 'details', 'Finished database extraction function.' );
		return true;
	} // End extractDatabase().
	
	
	
	/* restoreDatabase()
	 *
	 * ROLLBACK, RESTORE
	 * Renames existing tables then imports the database SQL data into mysql. Turns on maintenance mode during this.
	 *
	 * @return	bool|int		true on success, false on failure, OR integer number for resume number for continuing db import.
	 */
	public function restoreDatabase() {
		$this->_before( __FUNCTION__ );
		global $wpdb;
		
		/********** Start mysqlbuddy use **********/
		
		pb_backupbuddy::status( 'details', 'Restoring database tables from SQL file(s).' );
		$sqlFile = $this->_state['tempPath'] . 'db_1.sql';
		$ignoreExisting = false;
		
		$newPrefix = 'BBnew-' . $this->_state['serial'] . '_';
		
		require_once( pb_backupbuddy::plugin_path() . '/lib/mysqlbuddy/mysqlbuddy.php' );
		pb_backupbuddy::$classes['mysqlbuddy'] = new pb_backupbuddy_mysqlbuddy( DB_HOST, DB_NAME, DB_USER, DB_PASSWORD, $newPrefix, $this->_state['forceMysqlMethods'], $this->_state['maxExecutionTime'] ); // $database_host, $database_name, $database_user, $database_pass, $old_prefix, $force_method = array()
		$import_result = pb_backupbuddy::$classes['mysqlbuddy']->import( $sqlFile, $oldPrefix = $wpdb->prefix, $this->_state['dbImportPoint'], $ignoreExisting );
		if ( false === $import_result ) {
			$this->_error( 'Error #953834: Problem importing database. See status log above for details.' );
			return false;
		} else {
			if ( is_numeric( $import_result ) ) {
				$this->_state['dbImportPoint'] = $import_result;
				pb_backupbuddy::status( 'details', 'Database import not yet finished. Resume restoreDatabase() next at `' . $this->_state['dbImportPoint'] . '`.' );
				return $import_result;
			}
		}
		
		/********** End mysqlbuddy use **********/
		
		pb_backupbuddy::status( 'details', 'Finished database restore function.' );
		return true;
	} // End restoreDatabase().
	
	
	
	/* swapDatabases()
	 *
	 * ROLLBACK
	 * Swap out the recently imported database tables with temp prefix for the live database.
	 * Sets maintenance mode during the swap, although it should be very brief.
	 *
	 */
	public function swapDatabases() {
		
		if ( 'rollback' != $this->_state['type'] ) {
			$this->_error( 'This restore type does not support this operation.' );
			return false;
		}
		
		// Turn on maintenance mode.
		if ( false === $this->_maintenanceOn() ) {
			$this->_error( 'Could not enable maintenance mode.' );
			return false;
		}
		
		global $wpdb;
		
		// Calculate temporary table prefixes.
		$newPrefix = 'BBnew-' . $this->_state['serial'] . '_'; // Temp prefix for holding the NEWly imported data.
		$oldPrefix = 'BBold-' . $this->_state['serial'] . '_'; // Temp prefix for holding the OLD (currently live) data.
		
		// Get newly imported tables with the temp prefix.
		pb_backupbuddy::status( 'details', 'Checking for newly imported rollback tables with temp prefix `' . $newPrefix . '`.' );
		$sql = "SELECT table_name FROM information_schema.tables WHERE table_name LIKE '" . str_replace( '_', '\_', $newPrefix ) . "%' AND table_schema = DATABASE()";
		$results = $wpdb->get_results( $sql, ARRAY_A );
		pb_backupbuddy::status( 'details', 'Found ' . count( $results ) . ' matching tables.' );
		if ( 0 == count( $results ) ) {
			$this->_error( 'Error getting tables or none found. SQL Query: ' . htmlentities( $sql ) );
			return false;
		}
		
		// Rename newly imported tables with temp prefix, renaming the existing live table first.
		pb_backupbuddy::status( 'details', 'Rename all existing tables with this temp prefix to prefix `' . $wpdb->prefix . '`.' );
		foreach( $results as $result ) {
			$newTableName = str_replace( $newPrefix, '', $wpdb->prefix . $result['table_name'] ); // the target new table name we are importing.
			$oldTableName = $oldPrefix . $newTableName; // the target name for the old table where we hold it in case it needs undoing.
			
			// Rename live prefix to temp old prefix for possible undo (if exists).
			pb_backupbuddy::status( 'details', 'Renaming table `' . $newTableName . '` (if exists) to `' . $oldTableName . '`.' );
			if ( false === $wpdb->query( "RENAME TABLE `" . backupbuddy_core::dbEscape( $newTableName ) . "` TO `" . backupbuddy_core::dbEscape( $oldTableName ) . "`;" ) ) {
				$this->_error( 'Error #844389a: Unable to rename table `' . $newTableName . '` to `' . $oldTableName . '`. Details: `' . $wpdb->last_error . '`.' );
				return false;
			}
			
			// Rename imported table to live prefix.
			pb_backupbuddy::status( 'details', 'Renaming table `' . $result['table_name'] . '` to `' . $newTableName . '`.' );
			if ( false === $wpdb->query( "RENAME TABLE `" . backupbuddy_core::dbEscape( $result['table_name'] ) . "` TO `" . backupbuddy_core::dbEscape( $newTableName ) . "`;" ) ) {
				$this->_error( 'Error #844389b: Unable to rename table `' . $result['table_name'] . '` to `' . $newTableName . '`. Details: `' . $wpdb->last_error . '`.' );
				return false;
			}
		}
		
		// Turn off maintenance mode.
		$this->_maintenanceOff();
		
		return true;
	}
	
	
	
	/* finalizeRollback()
	 *
	 * ROLLBACK
	 * Finalize the rollback, deleting original tables & cleaning up temp files.
	 *
	 * @return true
	 */
	public function finalizeRollback() {
		$this->_before( __FUNCTION__ );
		
		global $wpdb;
		$sql = "SELECT table_name FROM information_schema.tables WHERE table_name LIKE 'BBold-" . $this->_state['serial'] . "\_%' AND table_schema = DATABASE()";
		//echo $sql;
		$results = $wpdb->get_results( $sql, ARRAY_A );
		pb_backupbuddy::status( 'details', 'Found ' . count( $results ) . ' tables to drop.' );
		foreach( $results as $result ) {
			if ( false === $wpdb->query( "DROP TABLE `" . backupbuddy_core::dbEscape( $result['table_name'] ) . "`" ) ) {
				$this->_error( 'Unable to delete old table `' . $result['table_name'] . '`.' );
			}
		}
		
		pb_backupbuddy::status( 'details', 'Deleting undo file.' );
		@unlink( ABSPATH . $this->_state['undoFile'] );
		pb_backupbuddy::status( 'details', 'Deleting temp files.' );
		pb_backupbuddy::$filesystem->unlink_recursive( $this->_state['tempPath'] );
		
		pb_backupbuddy::status( 'details', 'Finished finalize function.' );
		return true;
	} // end finalizeRollback().
	
	
	
	/* _priorRollbackCleanup()
	 *
	 * ROLLBACK
	 * Cleans up any existing temp database tables that exist from a prior failed/incomplete rollback that need removed.
	 *
	 */
	private function _priorRollbackCleanup() {
		$this->_before( __FUNCTION__ );
		
		pb_backupbuddy::status( 'details', 'Checking for any prior failed rollback data to clean up.' );
		global $wpdb;
		
		// NEW prefix
		$sql = "SELECT table_name FROM information_schema.tables WHERE table_name LIKE 'BBnew-" . $this->_state['serial'] . "\_%' AND table_schema = DATABASE()";
		$results = $wpdb->get_results( $sql, ARRAY_A );
		pb_backupbuddy::status( 'details', 'Found ' . count( $results ) . ' tables to drop with the prefix `BBnew-' . $this->_state['serial'] . '_`.' );
		$dropCount = 0;
		foreach( $results as $result ) {
			if ( false === $wpdb->query( "DROP TABLE `" . backupbuddy_core::dbEscape( $result['table_name'] ) . "`" ) ) {
				$this->_error( 'Unable to delete table `' . $result['table_name'] . '`.' );
			} else {
				$dropCount++;
			}
		}
		pb_backupbuddy::status( 'details', 'Dropped `' . $dropCount . '` new tables.' );
		
		// OLD prefix
		$sql = "SELECT table_name FROM information_schema.tables WHERE table_name LIKE 'BBold-" . $this->_state['serial'] . "\_%' AND table_schema = DATABASE()";
		$results = $wpdb->get_results( $sql, ARRAY_A );
		pb_backupbuddy::status( 'details', 'Found ' . count( $results ) . ' tables to drop with the prefix `BBold-' . $this->_state['serial'] . '_`.' );
		$dropCount = 0;
		foreach( $results as $result ) {
			if ( false === $wpdb->query( "DROP TABLE `" . backupbuddy_core::dbEscape( $result['table_name'] ) . "`" ) ) {
				$this->_error( 'Unable to delete table `' . $result['table_name'] . '`.' );
			} else {
				$dropCount++;
			}
		}
		pb_backupbuddy::status( 'details', 'Dropped `' . $dropCount . '` old tables.' );
		
		pb_backupbuddy::status( 'details', 'Finished prior rollback cleanup.' );
	} // end.
	
	
	
	/* _maintenanceOn()
	 *
	 * Turn ON WordPress maintenance mode.
	 *
	 */
	private function _maintenanceOn() {
		// Turn on maintenance mode.
		pb_backupbuddy::status( 'details', 'Turning on maintenance mode on.' );
		if ( ! file_exists( ABSPATH . '.maintenance' ) ) {
			$maintenance_result = @file_put_contents( ABSPATH . '.maintenance', "<?php die( 'Site undergoing maintenance.' ); ?>" );
			if ( false === $maintenance_result ) {
				$this->_error( '.maintenance file unable to be generated to prevent viewing.' );
				return false;
			} else {
				pb_backupbuddy::status( 'details', '.maintenance file generated to prevent viewing partially migrated site.' );
			}
		} else {
			pb_backupbuddy::status( 'details', '.maintenance file already exists. Skipping creation.' );
		}
		return true;
	} // End _maintenanceOn().
	
	
	
	/* _maintenanceOn()
	 *
	 * Turn OFF WordPress maintenance mode.
	 *
	 */
	private function _maintenanceOff() {
		pb_backupbuddy::status( 'details', 'Turn off maintenance mode off if on.' );
		if ( file_exists( ABSPATH . '.maintenance' ) ) {
			pb_backupbuddy::status( 'details', '.maintenance file exists. Deleting...' );
			if ( false === @unlink( ABSPATH . '.maintenance' ) ) {
				$this->_error( 'Unable to delete .maintenance file.' );
			}
		} else {
			pb_backupbuddy::status( 'details', '.maintenance file does not exist.' );
		}
	} // End _maintenanceOff().
	
	
	
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
		return;
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
		pb_backupbuddy::status( 'details', 'Getting rollback state.' );
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

