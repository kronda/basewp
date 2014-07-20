<?php
/*	pb_backupbuddy_backup class
 *	
 *	Handles the actual backup procedures.
 *	
 *	USED BY:
 *
 *	1) Full & DB backups
 *	2) Multisite backups & exports
 *
 */
class pb_backupbuddy_backup {
	
	private $_errors = array();								// TODO:  No longer used? Remove?
	private $_status_logging_started = false;				// Marked true once anything has been status logged during this process. Used by status().
	
	// PHP date() timestamp format for the backup archive filename. DATE is default.
	const ARCHIVE_NAME_FORMAT_DATE = 'Y_m_d';				// Format when archive_name_format = date.
	const ARCHIVE_NAME_FORMAT_DATETIME = 'Y_m_d-h_ia';		// Format when archive_name_format = datetime.
	
	
	
	/*	__construct()
	 *	
	 *	Default contructor. Initialized core and zipbuddy classes.
	 *	
	 *	@return		null
	 */
	function __construct() {
		
		// Load core if it has not been instantiated yet.
		if ( ! class_exists( 'backupbuddy_core' ) ) {
			require_once( pb_backupbuddy::plugin_path() . '/classes/core.php' );
		}
		
		// Load zipbuddy if it has not been instantiated yet.
		if ( !isset( pb_backupbuddy::$classes['zipbuddy'] ) ) {
			require_once( pb_backupbuddy::plugin_path() . '/lib/zipbuddy/zipbuddy.php' );
			pb_backupbuddy::$classes['zipbuddy'] = new pluginbuddy_zipbuddy( backupbuddy_core::getBackupDirectory() );
		}
		
		// Register PHP shutdown function to help catch and log fatal PHP errors during backup.
		register_shutdown_function( array( &$this, 'shutdown_function' ) );
		
	} // End __construct().
	
	
	
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
		
		
		// Calculate log directory.
		$log_directory = backupbuddy_core::getLogDirectory();
		$main_file = $log_directory . 'log-' . pb_backupbuddy::$options['log_serial'] . '.txt';
		
		
		// Determine if writing to a serial log.
		if ( pb_backupbuddy::$_status_serial != '' ) {
			$serial = pb_backupbuddy::$_status_serial;
			$serial_file = $log_directory . 'status-' . $serial . '_' . pb_backupbuddy::$options['log_serial'] . '.txt';
			$write_serial = true;
		} else {
			$write_serial = false;
		}
		
		
		// Format error message.
		$e_string = "---\n" . __( 'Fatal PHP error encountered:', 'it-l10n-backupbuddy' ) . "\n";
		foreach( (array)$e as $e_line_title => $e_line ) {
			$e_string .= $e_line_title . ' => ' . $e_line . "\n";
		}
		$e_string .= "---\n";
		
		
		// Write to log.
		@file_put_contents( $main_file, $e_string, FILE_APPEND );
		if ( $write_serial === true ) {
			@file_put_contents( $serial_file, $e_string, FILE_APPEND );
		}
		
		
	} // End shutdown_function.
	
	
	
	/*	start_backup_process()
	 *	
	 *	Initializes the entire backup process.
	 *	
	 *	@param		array		$profile			Backup profile array. Previously (pre-4.0): Valid values: db, full, export.
	 *	@param		string		$trigger			What triggered this backup. Valid values: scheduled, manual.
	 *	@param		array		$pre_backup			Array of functions to prepend to the backup steps array.
	 *	@param		array		$post_backup		Array of functions to append to the backup steps array. Ie. sending to remote destination.
	 *	@param		string		$schedule_title		Title name of schedule. Used for tracking what triggered this in logging. For debugging.
	 *	@param		string		$serial_override	If provided then this serial will be used instead of an auto-generated one.
	 *	@param		array		$export_plugins		For use in export backup type. List of plugins to export.
	 *	@return		boolean							True on success; false otherwise.
	 */
	function start_backup_process( $profile, $trigger = 'manual', $pre_backup = array(), $post_backup = array(), $schedule_title = '', $serial_override = '', $export_plugins = array() ) {
		
		// Load profile defaults.
		$profile = array_merge( pb_backupbuddy::settings( 'profile_defaults' ), $profile );
		foreach( $profile as $profile_item_name => &$profile_item ) { // replace non-overridden defaults with actual default value.
			if ( '-1' == $profile_item ) { // Set to use default so go grab default.
				if ( isset( pb_backupbuddy::$options['profiles'][0][ $profile_item_name ] ) ) {
					$profile_item = pb_backupbuddy::$options['profiles'][0][ $profile_item_name ]; // Grab value from defaults profile and replace with it.
				}
			}
		}
		
		// Handle backup mode.
		$backup_mode = pb_backupbuddy::$options['backup_mode']; // Load global default mode.
		if ( '1' == $profile['backup_mode'] ) { // Profile forces classic.
			$backup_mode = '1';
		} elseif ( '2' == $profile['backup_mode'] ) { // Profiles forces modern.
			$backup_mode = '2';
		}
		$profile['backup_mode'] = $backup_mode;
		unset( $backup_mode );
		
		// If classic mode then we need to redirect output to displaying inline via JS instead of AJAX-based.
		if ( '1' == $profile['backup_mode'] ) {
			//global $pb_backupbuddy_js_status;
			//$pb_backupbuddy_js_status = true;
		}
		
		if ( $serial_override != '' ) {
			$serial = $serial_override;
		} else {
			$serial = pb_backupbuddy::random_string( 10 );
		}
		pb_backupbuddy::set_status_serial( $serial ); // Default logging serial.
		
		global $wp_version;
		pb_backupbuddy::status( 'details', 'BackupBuddy v' . pb_backupbuddy::settings( 'version' ) . ' using WordPress v' . $wp_version . ' on ' . PHP_OS . '.' );
		//pb_backupbuddy::status( 'details', __('Peak memory usage', 'it-l10n-backupbuddy' ) . ': ' . round( memory_get_peak_usage() / 1048576, 3 ) . ' MB' );
		
		if ( $this->pre_backup( $serial, $profile, $trigger, $pre_backup, $post_backup, $schedule_title, $export_plugins ) === false ) {
			pb_backupbuddy::status( 'details', 'pre_backup() function failed.' );
			return false;
		}
		
		if ( ( $trigger == 'scheduled' ) && ( pb_backupbuddy::$options['email_notify_scheduled_start'] != '' ) ) {
			pb_backupbuddy::status( 'details', __('Sending scheduled backup start email notification if applicable.', 'it-l10n-backupbuddy' ) );
			backupbuddy_core::mail_notify_scheduled( $serial, 'start', __('Scheduled backup', 'it-l10n-backupbuddy' ) . ' (' . $this->_backup['schedule_title'] . ') has begun.' );
		}
		
		if ( $profile['backup_mode'] == '2' ) { // Modern mode with crons.
			
			pb_backupbuddy::status( 'message', 'Running in modern backup mode based on settings. Mode value: `' . $profile['backup_mode'] . '`. Trigger: `' . $trigger . '`.' );
			
			unset( $this->_backup_options ); // File unlocking is handled on deconstruction.  Make sure unlocked before firing off another cron spawn.
			
			// If using alternate cron on a manually triggered backup then skip running the cron on this pageload to avoid header already sent warnings.
			if ( ( $trigger != 'manual' ) || ( defined('ALTERNATE_WP_CRON') && ALTERNATE_WP_CRON ) ) {
				$this->cron_next_step( false );
			} else {
				//$this->cron_next_step( true );
				$this->cron_next_step( false ); // as of Aug 9, 2013 no longer spawn the cron. Caused very odd issue of double code runs.
			}
			
		} else { // Classic mode; everything runs in this single PHP page load.
			
			pb_backupbuddy::status( 'message', 'Running in classic backup mode based on settings.' );
			$this->process_backup( $this->_backup['serial'], $trigger );
			
		}
		
		return true;
		
	} // End start_backup_process().
	
	
	
	/*	pre_backup()
	 *	
	 *	Set up the backup data structure containing steps, set up temp directories, etc.
	 *	
	 *	@param		array		$profile			Backup profile array data. Prev (pre-4.0):	Backup type. Valid values: db, full, export.
	 *	@param		string		$trigger			What triggered this backup. Valid values: scheduled, manual.
	 *	@param		array		$pre_backup			Array of functions to prepend to the backup steps array.
	 *	@param		array		$post_backup		Array of functions to append to the backup steps array. Ie. sending to remote destination.
	 *	@param		string		$schedule_title		Title name of schedule. Used for tracking what triggered this in logging. For debugging.
	 *	@param		array		$export_plugins		For use in export backup type. List of plugins to export.
	 *	@return		boolean							True on success; false otherwise.
	 */
	function pre_backup( $serial, $profile, $trigger, $pre_backup = array(), $post_backup = array(), $schedule_title = '', $export_plugins = array() ) {
		
		pb_backupbuddy::status( 'startFunction', json_encode( array( 'function' => 'pre_backup', 'title' => 'Getting ready to backup' ) ) );
		
		$type = $profile['type'];
		
		// Log some status information.
		pb_backupbuddy::status( 'details', __( 'Performing pre-backup procedures.', 'it-l10n-backupbuddy' ) );
		if ( $type == 'full' ) {
			pb_backupbuddy::status( 'message', __( 'Full backup mode.', 'it-l10n-backupbuddy' ) );
		} elseif ( $type == 'db' ) {
			pb_backupbuddy::status( 'message', __( 'Database only backup mode.', 'it-l10n-backupbuddy' ) );
		} elseif ( $type == 'files' ) {
			pb_backupbuddy::status( 'message', __( 'Files only backup mode.', 'it-l10n-backupbuddy' ) );
			//$profile['skip_database_dump'] = '1';
		} elseif ( $type == 'export' ) {
			pb_backupbuddy::status( 'message', __( 'Multisite Site Export mode.', 'it-l10n-backupbuddy' ) );
		} else {
			pb_backupbuddy::status( 'error', __( 'Error #8587383: Unknown backup mode.', 'it-l10n-backupbuddy' ) ) . 'Supplied backup type: `' . htmlentities( $type ) . '`.';
		}
		
		if ( '1' == pb_backupbuddy::$options['prevent_flush'] ) {
			pb_backupbuddy::status( 'details', 'Flushing will be skipped based on advanced settings.' );
		} else {
			pb_backupbuddy::status( 'details', 'Flushing will not be skipped (default).' );
		}
		
		// Schedule daily housekeeping.
		if ( false === wp_next_scheduled( pb_backupbuddy::cron_tag( 'housekeeping' ) ) ) { // if schedule does not exist...
			backupbuddy_core::schedule_event( time() + ( 60*60 * 2 ), 'daily', pb_backupbuddy::cron_tag( 'housekeeping' ), array() ); // Add schedule.
		}
		
		
		// Verify directories.
		pb_backupbuddy::status( 'details', 'Verifying directories ...' );
		if ( false === backupbuddy_core::verify_directories() ) {
			pb_backupbuddy::status( 'error', 'Error #18573. Error verifying directories. See details above. Backup halted.' );
			pb_backupbuddy::status( 'haltScript', '' ); // Halt JS on page.
			die();
		} else {
			pb_backupbuddy::status( 'details', 'Directories verified.' );
		}
		
		
		// Delete all backup archives if this troubleshooting option is enabled.
		if ( pb_backupbuddy::$options['delete_archives_pre_backup'] == '1' ) {
			pb_backupbuddy::status( 'message', 'Deleting all existing backups prior to backup as configured on the settings page.' );
			$file_list = glob( backupbuddy_core::getBackupDirectory() . 'backup*.zip' );
			if ( is_array( $file_list ) && !empty( $file_list ) ) {
				foreach( $file_list as $file ) {
					if ( @unlink( $file ) === true ) {
						pb_backupbuddy::status( 'details', 'Deleted backup archive `' . basename( $file ) . '` based on settings to delete all backups.' );
					} else {
						pb_backupbuddy::status( 'details', 'Unable to delete backup archive `' . basename( $file ) . '` based on settings to delete all backups. Verify permissions.' );
					}
				}
			}
		}
				
		// Generate unique serial ID.
		pb_backupbuddy::status( 'details', 'Backup serial generated: `' . $serial . '`.' );
		
		pb_backupbuddy::status( 'details', 'About to load fileoptions data in create mode.' );
		require_once( pb_backupbuddy::plugin_path() . '/classes/fileoptions.php' );
		$this->_backup_options = new pb_backupbuddy_fileoptions( backupbuddy_core::getLogDirectory() . 'fileoptions/' . $serial . '.txt', $read_only = false, $ignore_lock = false, $create_file = true );
		if ( true !== ( $result = $this->_backup_options->is_ok() ) ) {
			pb_backupbuddy::status( 'error', __('Fatal Error #9034 A. Unable to access fileoptions data.', 'it-l10n-backupbuddy' ) . ' Error: ' . $result );
			pb_backupbuddy::status( 'haltScript', '' ); // Halt JS on page.
			return false;
		}
		pb_backupbuddy::status( 'details', 'Fileoptions data loaded.' );
		$this->_backup = &$this->_backup_options->options; // Set reference.
		
		
		// Cleanup internal stats.
		pb_backupbuddy::status( 'details', 'Updating statistics for last backup start.' );
		pb_backupbuddy::$options['last_backup_start'] = time(); // Reset time since last backup.
		pb_backupbuddy::$options['last_backup_serial'] = $serial;
		pb_backupbuddy::save();
		
		
		// Output active plugins list for debugging...
		$activePlugins = get_option( 'active_plugins' );
		pb_backupbuddy::status( 'details', 'Active WordPress plugins: `' . implode( '; ', $activePlugins ) . '`.' );
		pb_backupbuddy::status( 'startSubFunction', json_encode( array( 'function' => 'wp_plugins_found', 'title' => 'Found ' . count( $activePlugins ) . ' active WordPress plugins.' ) ) );
		unset( $activePlugins );
		
		// Prepare some values for setting up the backup data.
		$siteurl_stripped = backupbuddy_core::backup_prefix();
		
		// Calculate customizable section of archive filename (date vs date+time).
		if ( pb_backupbuddy::$options['archive_name_format'] == 'datetime' ) { // "datetime" = Date + time.
			$backupfile_datetime = date( self::ARCHIVE_NAME_FORMAT_DATETIME, pb_backupbuddy::$format->localize_time( time() ) );
		} else { // "date" = date only (the default).
			$backupfile_datetime = date( self::ARCHIVE_NAME_FORMAT_DATE, pb_backupbuddy::$format->localize_time( time() ) );
		}
		
		
		// Compression to bool.
		/*
		if ( $profile['compression'] == '1' ) {
			$profile['compression'] = true;
		} else {
			$profile['compression'] = false;
		}
		*/
		if ( pb_backupbuddy::$options['compression'] == '1' ) {
			$compression = true;
		} else {
			$compression = false;
		}
		
		$archiveFile = backupbuddy_core::getBackupDirectory() . 'backup-' . $siteurl_stripped . '-' . $backupfile_datetime . '-' . $type . '-' . $serial . '.zip';
		//$archiveURL = pb_backupbuddy::ajax_url( 'download_archive' ) . '&backupbuddy_backup=' . basename( $archiveFile );
		$archiveURL = '';
		$abspath = str_replace( '\\', '/', ABSPATH ); // Change slashes to handle Windows as we store backup_directory with Linux-style slashes even on Windows.
		$backup_dir = str_replace( '\\', '/', backupbuddy_core::getBackupDirectory() );
		if ( FALSE !== stristr( $backup_dir, $abspath ) ) { // Make sure file to download is in a publicly accessible location (beneath WP web root technically).
			$sitepath = str_replace( $abspath, '', $backup_dir );
			$archiveURL = rtrim( site_url(), '/\\' ) . '/' . trim( $sitepath, '/\\' ) . '/' . basename( $archiveFile );
		}
		
		
		// Set up the backup data.
		$this->_backup = array(
			'data_version'			=>		0,												// Data structure version.
			'backupbuddy_version'	=>		pb_backupbuddy::settings( 'version' ),			// BB version used for this backup.
			'serial'				=>		$serial,										// Unique identifier.
			'init_complete'			=>		false,											// Whether pre_backup() completed or not. Other step status is already tracked and stored in data structure but pre_backup 'step' was not until now. Jan 6, 2013.
			'backup_mode'			=>		$profile['backup_mode'],						// Tells whether modern or classic mode.
			'type'					=>		$type,											// db, full, or export.
			'profile'				=>		$profile,										// Backup profile data.
			'default_profile'		=>		pb_backupbuddy::$options['profiles'][0],		// Default profile.
			'start_time'			=>		time(),											// When backup started. Now.
			'finish_time'			=>		0,
			'updated_time'			=>		time(),											// When backup last updated. Subsequent steps update this.
			'status'				=>		array(),										// TODO: what goes in this?
			'archive_size'			=>		0,
			'schedule_title'		=>		$schedule_title,								// Title of the schedule that made this backup happen (if applicable).
			'backup_directory'		=>		backupbuddy_core::getBackupDirectory(),			// Directory backups stored in.
			'archive_file'			=>		$archiveFile,									// Unique backup ZIP filename.
			'archive_url'			=>		$archiveURL,									// Target download URL.
			'trigger'				=>		$trigger,										// How backup was triggered: manual or scheduled.
			'zip_method_strategy'	=>		pb_backupbuddy::$options['zip_method_strategy'],// Enumerated zip method strategy
			'compression'			=>		$compression, 									//$profile['compression'], // Boolean - future enumerated?
			'ignore_zip_warnings'	=>		pb_backupbuddy::$options['ignore_zip_warnings'],// Boolean - future bitmask?
			'ignore_zip_symlinks'	=>		pb_backupbuddy::$options['ignore_zip_symlinks'],// Boolean - future bitmask?
			'steps'					=>		array(),										// Backup steps to perform. Set next in this code.
			'integrity'				=>		array(),										// Used later for tests and stats post backup.
			'temp_directory'		=>		'',												// Temp directory to store SQL and DAT file. Differs for exports. Defined in a moment...
			'backup_root'			=>		'',												// Where to start zipping from. Usually root of site. Defined in a moment...
			'export_plugins'		=>		array(),										// Plugins to export during MS export of a subsite.
			'additional_table_includes'	=>	array(),
			'additional_table_excludes'	=>	array(),
			'directory_exclusions'		=>	backupbuddy_core::get_directory_exclusions( $profile, false, $serial ), // Do not trim trailing slash
			'table_sizes'			=>		array(),										// Array of tables to backup AND their sizes.
			'breakout_tables'		=>		array(),										// Array of tables that will be broken out to separate steps.
		);
		
		// Warn if excluding key paths.
		$fileExcludes = backupbuddy_core::alert_core_file_excludes( $this->_backup['directory_exclusions'] );
		foreach( $fileExcludes as $fileExcludeId => $fileExclude ) {
			pb_backupbuddy::status( 'warning', $fileExclude );
		}
		pb_backupbuddy::status( 'startSubFunction', json_encode( array( 'function' => 'file_excludes', 'title' => 'Found ' . count( $fileExcludes ) . ' file or directory exclusions.' ) ) );
		
		// Figure out paths.
		if ( ( $this->_backup['type'] == 'full' ) || ( $this->_backup['type'] == 'files' ) ) {
			$this->_backup['temp_directory'] = ABSPATH . 'wp-content/uploads/backupbuddy_temp/' . $serial . '/';
			$this->_backup['backup_root'] = ABSPATH; // ABSPATH contains trailing slash.
		} elseif ( $this->_backup['type'] == 'db' ) {
			$this->_backup['temp_directory'] = ABSPATH . 'wp-content/uploads/backupbuddy_temp/' . $serial . '/';
			$this->_backup['backup_root'] = $this->_backup['temp_directory'];
		} elseif ( $this->_backup['type'] == 'export' ) {
			// WordPress unzips into wordpress subdirectory by default so must include that in path.
			$this->_backup['temp_directory'] = ABSPATH . 'wp-content/uploads/backupbuddy_temp/' . $serial . '/wordpress/wp-content/uploads/backupbuddy_temp/' . $serial . '/'; // We store temp data for export within the temporary WordPress installation within the temp directory. A bit confusing; sorry about that.
			$this->_backup['backup_root'] = ABSPATH . 'wp-content/uploads/backupbuddy_temp/' . $serial . '/wordpress/';
		} else {
			pb_backupbuddy::status( 'error', __('Backup FAILED. Unknown backup type.', 'it-l10n-backupbuddy' ) );
			pb_backupbuddy::status( 'haltScript', '' ); // Halt JS on page.
		}
		
		
		// Plugins to export (only for MS exports).
		if ( count( $export_plugins ) > 0 ) {
			$this->_backup['export_plugins'] = $export_plugins;
		}
		
		
		// Calculate additional database table inclusion/exclusion.
		$additional_includes = explode( "\n", $profile['mysqldump_additional_includes'] );
		array_walk( $additional_includes, create_function('&$val', '$val = trim($val);')); 
		$this->_backup['additional_table_includes'] = array_unique( $additional_includes ); // removes duplicates.
		$additional_excludes = explode( "\n", $profile['mysqldump_additional_excludes'] );
		array_walk( $additional_excludes, create_function('&$val', '$val = trim($val);'));
		$this->_backup['additional_table_excludes'] = array_unique( $additional_excludes ); // removes duplicates.
		unset( $additional_includes );
		unset( $additional_excludes );
		
		/********* Begin setting up steps array. *********/
		
		if ( $type == 'export' ) {
			pb_backupbuddy::status( 'details', 'Setting up export-specific steps.' );
			
			$this->_backup['steps'][] = array(
				'function'		=>	'ms_download_extract_wordpress',
				'args'			=>	array(),
				'start_time'	=>	0,
				'finish_time'	=>	0,
				'attempts'		=>	0,
			);
			$this->_backup['steps'][] = array(
				'function'		=>	'ms_create_wp_config',
				'args'			=>	array(),
				'start_time'	=>	0,
				'finish_time'	=>	0,
				'attempts'		=>	0,
			);
			$this->_backup['steps'][] = array(
				'function'		=>	'ms_copy_plugins',
				'args'			=>	array(),
				'start_time'	=>	0,
				'finish_time'	=>	0,
				'attempts'		=>	0,
			);
			$this->_backup['steps'][] = array(
				'function'		=>	'ms_copy_themes',
				'args'			=>	array(),
				'start_time'	=>	0,
				'finish_time'	=>	0,
				'attempts'		=>	0,
			);
			$this->_backup['steps'][] = array(
				'function'		=>	'ms_copy_media',
				'args'			=>	array(),
				'start_time'	=>	0,
				'finish_time'	=>	0,
				'attempts'		=>	0,
			);
			$this->_backup['steps'][] = array(
				'function'		=>	'ms_copy_users_table', // Create temp user and usermeta tables.
				'args'			=>	array(),
				'start_time'	=>	0,
				'finish_time'	=>	0,
				'attempts'		=>	0,
			);
		}
		
		if ( ( '1' != $profile['skip_database_dump'] ) && ( $profile['type'] != 'files' ) ) { // Backup database if not skipping AND not a files only backup.
			
			global $wpdb;
			// Default tables to backup.
			if ( $profile['backup_nonwp_tables'] == '1' ) { // Backup all tables.
				$base_dump_mode = 'all';
			} elseif ( $profile['backup_nonwp_tables'] == '2' ) { // Backup no tables by default. Relies on listed additional tables.
				$base_dump_mode = 'none';
			} else { // Only backup matching prefix.
				$base_dump_mode = 'prefix';
			}
			
			
			$additional_tables = $this->_backup['additional_table_includes'];
			if ( $type == 'export' ) {
				global $wpdb;
				array_push( $additional_tables, $wpdb->prefix . "users" );
				array_push( $additional_tables, $wpdb->prefix . "usermeta" );
			}
			
			// Warn if excluding key WP tables.
			$tableExcludes = backupbuddy_core::alert_core_table_excludes( $this->_backup['additional_table_excludes'] );
			foreach( $tableExcludes as $tableExcludeId => $tableExclude ) {
				pb_backupbuddy::status( 'warning', $tableExclude );
			}
			
			// Calculate tables to dump based on the provided information. $tables will be an array of tables.
			$tables = $this->_calculate_tables( $base_dump_mode, $additional_tables, $this->_backup['additional_table_excludes'] );
			pb_backupbuddy::status( 'startSubFunction', json_encode( array( 'function' => 'calculate_tabeles', 'title' => 'Found ' . count( $tables ) . ' tables to backup based on settings.' ) ) );
			
			// If calculations show NO database tables should be backed up then change mode to skip database dump.
			if ( 0 == count( $tables ) ) {
				pb_backupbuddy::status( 'warning', 'WARNING #857272: No database tables will be backed up based on current settings. This will not be a complete backup. Adjust settings if this is not intended and use with caution. Skipping database dump step.' );
				$profile['skip_database_dump'] = '1';
				$this->_backup['profile']['skip_database_dump'] = '1';
			} else { // One or more tables set to backup.
				
				// Obtain tables sizes. Surround each table name by a single quote and implode with commas for SQL query to get sizes.
				$tables_formatted = $tables;
				foreach( $tables_formatted as &$table_formatted ) {
					$table_formatted = "'{$table_formatted}'";
				}
				$tables_formatted = implode( ',', $tables_formatted );
				$sql = "SHOW TABLE STATUS WHERE Name IN({$tables_formatted});";
				$rows = $wpdb->get_results( $sql, ARRAY_A );
				if ( false === $rows ) {
					pb_backupbuddy::alert( 'Error #85473474: Unable to retrieve table status. Query: `' . $sql . '`.', true );
					return false;
				}
				$totalDatabaseSize = 0;
				foreach( $rows as $row ) {
					$this->_backup['table_sizes'][ $row['Name'] ] = ( $row['Data_length'] + $row['Index_length'] );
					$totalDatabaseSize += $this->_backup['table_sizes'][ $row['Name'] ];
				}
				unset( $rows );
				unset( $tables_formatted );
				
				pb_backupbuddy::status( 'details', 'Total database size: `' . pb_backupbuddy::$format->file_size( $totalDatabaseSize ) . '`.' );
				
				// Tables we will try to break out into standalone steps if possible.
				$breakout_tables_defaults = array(
					$wpdb->prefix . 'posts',
					$wpdb->prefix . 'postmeta',
				);
				
				// Step through tables we want to break out and figure out which ones were indeed set to be backed up and break them out.
				if ( pb_backupbuddy::$options['breakout_tables'] == '0' ) { // Breaking out DISABLED.
					pb_backupbuddy::status( 'details', 'Breaking out tables DISABLED based on settings.' );
				} else { // Breaking out ENABLED.
					pb_backupbuddy::status( 'details', 'Breaking out tables ENABLED based on settings. Tables to be broken out into individual steps: `' . implode( ', ', $breakout_tables_defaults ) . '`.' );
					foreach( (array)$breakout_tables_defaults as $breakout_tables_default ) {
						if ( in_array( $breakout_tables_default, $tables ) ) {
							$this->_backup['breakout_tables'][] = $breakout_tables_default;
							$tables = array_diff( $tables, array( $breakout_tables_default ) ); // Remove from main table backup list.
						}
					}
				}
				unset( $breakout_tables_defaults ); // No longer needed.
				
				$this->_backup['steps'][] = array(
					'function'		=>	'backup_create_database_dump',
					'args'			=>	array( $tables ),
					'start_time'	=>	0,
					'finish_time'	=>	0,
					'attempts'		=>	0,
				);
				
				// Set up backup steps for additional broken out tables.
				foreach( (array)$this->_backup['breakout_tables'] as $breakout_table ) {
					$this->_backup['steps'][] = array(
						'function'		=>	'backup_create_database_dump',
						'args'			=>	array( array( $breakout_table ) ),
						'start_time'	=>	0,
						'finish_time'	=>	0,
						'attempts'		=>	0,
					);
				}
				
			} // end there being tables to backup.
			
		} else {
			pb_backupbuddy::status( 'message', __( 'Skipping database dump based on settings / profile type.', 'it-l10n-backupbuddy' ) . ' Backup type: `' . $type . '`.' );
		}
		
		$this->_backup['steps'][] = array(
			'function'		=>	'backup_zip_files',
			'args'			=>	array(),
			'start_time'	=>	0,
			'finish_time'	=>	0,
			'attempts'		=>	0,
		);
		
		if ( $type == 'export' ) {
			$this->_backup['steps'][] = array( // Multisite export specific cleanup.
				'function'		=>	'ms_cleanup', // Removes temp user and usermeta tables.
				'args'			=>	array(),
				'start_time'	=>	0,
				'finish_time'	=>	0,
				'attempts'		=>	0,
			);
		}
		
		if ( $profile['integrity_check'] == '1' ) {
			pb_backupbuddy::status( 'details', __( 'Integrity check will be performed based on settings for this profile.', 'it-l10n-backupbuddy' ) );
			$this->_backup['steps'][] = array(
				'function'		=>	'integrity_check',
				'args'			=>	array(),
				'start_time'	=>	0,
				'finish_time'	=>	0,
				'attempts'		=>	0,
			);
		} else {
			pb_backupbuddy::status( 'details', __( 'Skipping integrity check step based on settings for this profile.', 'it-l10n-backupbuddy' ) );
		}
		
		$this->_backup['steps'][] = array(
			'function'		=>	'post_backup',
			'args'			=>	array(),
			'start_time'	=>	0,
			'finish_time'	=>	0,
			'attempts'		=>	0,
		);
		
		// Prepend and append pre backup and post backup steps.				
		$this->_backup['steps'] = array_merge( $pre_backup, $this->_backup['steps'], $post_backup );
		
		/********* End setting up steps array. *********/
		
		
		// Save what we have so far so that any errors below will end up displayed to user.
		$this->_backup_options->save();
		
		
		/********* Begin directory creation and security. *********/
		
		pb_backupbuddy::anti_directory_browsing( $this->_backup['backup_directory'] );
		
		// Prepare temporary directory for holding SQL and data file.
		if ( backupbuddy_core::getTempDirectory() == '' ) {
			pb_backupbuddy::status( 'error', 'Error #54534344. Temp directory blank. Please deactivate then reactivate plugin to reset.' );
			return false;
		}
		
		if ( !file_exists( $this->_backup['temp_directory'] ) ) {
			if ( pb_backupbuddy::$filesystem->mkdir( $this->_backup['temp_directory'] ) === false ) {
				pb_backupbuddy::status( 'error', 'Error #9002b. Unable to create temporary storage directory (' . $this->_backup['temp_directory'] . ')' );
				return false;
			}
		}
		if ( !is_writable( $this->_backup['temp_directory'] ) ) {
			pb_backupbuddy::status( 'error', 'Error #9015. Temp data directory is not writable. Check your permissions. (' . $this->_backup['temp_directory'] . ')' );
			return false;
		}
		pb_backupbuddy::anti_directory_browsing( ABSPATH . 'wp-content/uploads/backupbuddy_temp/' );
		
		// Prepare temporary directory for holding ZIP file while it is being generated.
		$this->_backup['temporary_zip_directory'] = backupbuddy_core::getBackupDirectory() . 'temp_zip_' . $this->_backup['serial'] . '/';
		if ( !file_exists( $this->_backup['temporary_zip_directory'] ) ) {
			if ( pb_backupbuddy::$filesystem->mkdir( $this->_backup['temporary_zip_directory'] ) === false ) {
				pb_backupbuddy::status( 'details', 'Error #9002c. Unable to create temporary ZIP storage directory (' . $this->_backup['temporary_zip_directory'] . ')' );
				return false;
			}
		}
		if ( !is_writable( $this->_backup['temporary_zip_directory'] ) ) {
			pb_backupbuddy::status( 'error', 'Error #9015. Temp data directory is not writable. Check your permissions. (' . $this->_backup['temporary_zip_directory'] . ')' );
			return false;
		}
		
		/********* End directory creation and security *********/
		
		
		// Generate backup DAT (data) file containing details about the backup.
		if ( $this->backup_create_dat_file( $trigger ) !== true ) {
			pb_backupbuddy::status( 'details', __('Problem creating DAT file.', 'it-l10n-backupbuddy' ) );
			return false;
		}
		
		// Generating ImportBuddy file to include in the backup for FULL BACKUPS ONLY currently. Cannot put in DB because it would be in root and be excluded or conflict on extraction.
		if ( $type == 'full' ) {
			if ( pb_backupbuddy::$options['include_importbuddy'] == '1' ) {
				pb_backupbuddy::status( 'details', 'Generating ImportBuddy tool to include in backup archive: `' . $this->_backup['temp_directory'] . 'importbuddy.php`.' );
				pb_backupbuddy::status( 'startAction', 'importbuddyCreation' );
				backupbuddy_core::importbuddy( $this->_backup['temp_directory'] . 'importbuddy.php' );
				pb_backupbuddy::status( 'finishAction', 'importbuddyCreation' );
				pb_backupbuddy::status( 'details', 'ImportBuddy generation complete.' );
			} else { // dont include importbuddy.
				pb_backupbuddy::status( 'details', 'ImportBuddy tool inclusion in ZIP backup archive skipped based on settings.' );
			}
		}
		
		// Save all of this.
		$this->_backup['init_complete'] = true; // pre_backup() completed.
		$this->_backup_options->save();
		
		
		pb_backupbuddy::status( 'details', __('Finished pre-backup procedures.', 'it-l10n-backupbuddy' ) );
		pb_backupbuddy::status( 'milestone', 'finish_settings' );
		
		pb_backupbuddy::status( 'finishFunction', json_encode( array( 'function' => 'pre_backup' ) ) );
		return true;
		
	} // End pre_backup().
	
	
	
	/*	process_backup()
	 *	
	 *	Process and run the next backup step.
	 *	
	 *	@param		string		$serial		Unique backup identifier.
	 *	@param		string		$trigger	What triggered this processing: manual or scheduled.
	 *	@return		boolean					True on success, false otherwise.
	 */
	function process_backup( $serial, $trigger = 'manual' ) {
		pb_backupbuddy::status( 'details', 'Running process_backup() for serial `' . $serial . '`.' );
		
		// Assign reference to backup data structure for this backup.
		if ( ! isset( $this->_backup_options ) ) {
			pb_backupbuddy::status( 'details', 'About to load fileoptions data.' );
			$attempt_transient_prefix = 'pb_backupbuddy_lock_attempts-';
			require_once( pb_backupbuddy::plugin_path() . '/classes/fileoptions.php' );
			$this->_backup_options = new pb_backupbuddy_fileoptions( backupbuddy_core::getLogDirectory() . 'fileoptions/' . $serial . '.txt' );
			if ( true !== ( $result = $this->_backup_options->is_ok() ) ) { // Unable to access fileoptions.
				
				$attempt_delay_base = 10; // Base number of seconds to delay. Each subsequent attempt increases this delay by a multiple of the attempt number.
				$max_attempts = 8; // Max number of attempts to try to delay around a file lock. Delay increases each time.
				
				$this->_backup['serial'] = $serial; // Needs to be populated for use by cron schedule step.
				pb_backupbuddy::status( 'warning', __('Warning #9034 B. Unable to access fileoptions data.', 'it-l10n-backupbuddy' ) . ' Warning: ' . $result, $serial );
				
				// Track lock attempts in transient system. This is not vital & since locks may be having issues track this elsewhere.
				$lock_attempts = get_transient( $attempt_transient_prefix . $serial );
				if ( false === $lock_attempts ) {
					$lock_attempts = 0;
				}
				$lock_attempts++;
				set_transient( $attempt_transient_prefix . $serial, $lock_attempts, (60*60*24) ); // Increment lock attempts. Hold attempt count for 24 hours to help make sure we don't lose attempt count if very low site activity, etc.
				
				if ( $lock_attempts > $max_attempts ) {
					pb_backupbuddy::status( 'error', 'Backup halted. Maximum number of attempts made attempting to access locked fileoptions file. This may be caused by something causing backup steps to run out of order or file permission issues on the temporary directory holding the file `' . $fileoptions_file  . '`. Verify correct permissions.', $serial );
					pb_backupbuddy::status( 'haltScript', '', $serial ); // Halt JS on page.
					delete_transient( $attempt_transient_prefix . $serial );
					return false;
				}
				
				$wait_time = $attempt_delay_base * $lock_attempts;
				pb_backupbuddy::status( 'message', 'A scheduled step attempted to run before the previous step completed. The previous step may have failed or two steps may be attempting to run simultaneously.', $serial );
				pb_backupbuddy::status( 'message', 'Waiting `' . $wait_time . '` seconds before continuing. Attempt #' . $lock_attempts . ' of ' . $max_attempts . ' max allowed before giving up.', $serial );
				$this->cron_next_step( false, $wait_time );
				return false;
				
			} else { // Accessed fileoptions. Clear/reset any attempt count.
				delete_transient( $attempt_transient_prefix . $serial );
			}
			pb_backupbuddy::status( 'details', 'Fileoptions data loaded.' );
			$this->_backup = &$this->_backup_options->options;
		}
		
		if ( $this->_backup_options->options['profile']['backup_mode'] != '1' ) { // Only check for cronPass action if in modern mode.
			pb_backupbuddy::status( 'finishAction', 'cronPass' );
		}
		
		// Handle cancelled backups (stop button).
		if ( true == get_transient( 'pb_backupbuddy_stop_backup-' . $serial ) ) { // Backup flagged for stoppage. Proceed directly to cleanup.
			
			pb_backupbuddy::status( 'message', 'Backup STOPPED. Post backup cleanup step has been scheduled to clean up any temporary files.' );
			foreach( $this->_backup['steps'] as $step_id => $step ) {
				if ( $step['function'] != 'post_backup' ) {
					if ( $step['start_time'] == 0 ) {
						$this->_backup['steps'][$step_id]['start_time'] = -1; // Flag for skipping.
					}
				} else { // Post backup step.
					$this->_backup['steps'][$step_id]['args'] = array( true, true ); // Run post_backup in fail mode & delete backup file.
				}
			}
			//pb_backupbuddy::save();
			$this->_backup_options->save();
			pb_backupbuddy::status( 'haltScript', '' ); // Halt JS on page.
			
		}
		
		
		$found_next_step = false;
		
		
		// Loop through steps finding first step that has not run.
		foreach( (array)$this->_backup['steps'] as $step_index => $step ) {
			//pb_backupbuddy::status( 'details', 'step: ' . $step['function'] . 'start: ' . $step['start_time'] );
			if ( ( $step['start_time'] != -1 ) && ( $step['start_time'] != 0 ) && ( $step['finish_time'] == 0 ) ) { // A step is not marked for skippage, has begun but has not finished. This should not happen but the WP cron is funky. Wait a while before continuing.
				
				// Re-load, respecting locks to help avoid race conditions.
				$this->_backup_options->load( $ignore_lock = false, $create_file = false, $retryCount = 0 );
				if ( true !== ( $this->_backup_options->is_ok() ) ) { // Unable to access fileoptions.
					pb_backupbuddy::status( 'warning', 'Unable to update out of order step attempt count due to file lock. It may be being written to by the other step at this moment.' );
				} else {
					pb_backupbuddy::status( 'details', 'Saving update to step attempt count.' );
					$this->_backup['steps'][$step_index]['attempts']++; // Increment this as an attempt.
					$this->_backup_options->save();
				}
				
				if ( ( $step['attempts'] < 6 ) ) {
					$wait_time = 60 * $step['attempts']; // Each attempt adds a minute of wait time.
					pb_backupbuddy::status( 'warning', 'A scheduled step attempted to run before the previous step completed. Waiting `' . $wait_time . '` seconds before continuing for it to catch up. Attempt number `' . $step['attempts'] . '`.' );
					$this->cron_next_step( false, $wait_time );
					return false;
				} else { // Too many attempts to run this step.
					pb_backupbuddy::status( 'error', 'A scheduled step attempted to run before the previous step completed. After several attempts (`' . $step['attempts'] . '`) of failure BackupBuddy has given up. Halting backup.' );
					return false;
				}
				
				break;
				
			} elseif ( $step['start_time'] == 0 ) { // Step that is not marked for skippage and has not started yet.
				$found_next_step = true;
				$this->_backup['steps'][$step_index]['start_time'] = time(); // Set this step time to now.
				$this->_backup['steps'][$step_index]['attempts']++; // Increment this as an attempt.
				$this->_backup_options->save();
				
				pb_backupbuddy::status( 'details', 'Found next step to run: `' . $step['function'] . '`.' );
				
				break; // Break out of foreach loop to continue.
			} elseif ( $step['start_time'] == -1 ) { // Step flagged for skipping. Do not run.
				pb_backupbuddy::status( 'details', 'Step `' . $step['function'] . '` flagged for skipping. Skipping.' );
			} else { // Last case: Finished. Skip.
				// Do nothing for completed steps.
				//pb_backupbuddy::status( 'details', 'Step `' . $step['function'] . '` doing nothing with start `' . $step['start_time'] . '`.' );
			}
			
		} // End foreach().
		
		
		if ( $found_next_step === false ) { // No more steps to perform; return.
			pb_backupbuddy::status( 'details', 'No more steps found.' );
			return false;
		}
		//pb_backupbuddy::save();
		
		
		pb_backupbuddy::status( 'details', __('Peak memory usage', 'it-l10n-backupbuddy' ) . ': ' . round( memory_get_peak_usage() / 1048576, 3 ) . ' MB' );		
		
		/********* Begin Running Step Function **********/
		if ( method_exists( $this, $step['function'] ) ) {
			$args = '';
			foreach( $step['args'] as $arg ) {
				if ( is_array( $arg ) ) {
					$args .= '{' . implode( ',', $arg ) . '},';
				} else {
					$args .= implode( ',', $step['args'] ) . ',';
				}
			}
			
			pb_backupbuddy::status( 'details', '-----' );
			pb_backupbuddy::status( 'details', 'Starting step function `' . $step['function'] . '` with args `' . $args . '`. Attempt #' . ( $step['attempts'] + 1 ) . '.' ); // attempts 0-indexed.
			
			$functionTitle = $step['function'];
			$subFunctionTitle = '';
			$functionTitle = backupbuddy_core::prettyFunctionTitle( $step['function'], $step['args'] );
			pb_backupbuddy::status( 'startFunction', json_encode( array( 'function' => $step['function'], 'title' => $functionTitle ) ) );
			if ( '' != $subFunctionTitle ) {
				pb_backupbuddy::status( 'startSubFunction', json_encode( array( 'function' => $step['function'] . '_subfunctiontitle', 'title' => $subFunctionTitle ) ) );
			}
				
			$response = call_user_func_array( array( &$this, $step['function'] ), $step['args'] );
		} else {
			pb_backupbuddy::status( 'error', __( 'Error #82783745: Invalid function `' . $step['function'] . '`' ) );
			$response = false;
		}
		/********* End Running Step Function **********/
		//unset( $step );
		
		if ( $response === false ) { // Function finished but reported failure.
			
			// Failure caused by backup cancellation.
			if ( true == get_transient( 'pb_backupbuddy_stop_backup-' . $serial ) ) {
				pb_backupbuddy::status( 'haltScript', '' ); // Halt JS on page.
				return false;
			}
			
			pb_backupbuddy::status( 'error', 'Failed function `' . $this->_backup['steps'][$step_index]['function'] . '`. Backup terminated.' );
			pb_backupbuddy::status( 'errorFunction', $this->_backup['steps'][$step_index]['function'] );
			pb_backupbuddy::status( 'details', __('Peak memory usage', 'it-l10n-backupbuddy' ) . ': ' . round( memory_get_peak_usage() / 1048576, 3 ) . ' MB' );
			pb_backupbuddy::status( 'haltScript', '' ); // Halt JS on page.
			
			$args = print_r( $this->_backup['steps'][$step_index]['args'], true );
			$attachment = NULL;
			$attachment_note = 'Enable full logging for troubleshooting (a log will be sent with future error emails while enabled).';
			
			if ( pb_backupbuddy::$options['log_level'] == '3' ) { // Full logging enabled.
				// Log file will be attached.
				$log_file = backupbuddy_core::getLogDirectory() . 'status-' . $serial . '_' . pb_backupbuddy::$options['log_serial'] . '.txt';
				if ( file_exists( $log_file ) ) {
					$attachment = $log_file;
					$attachment_note = 'A log file is attached which may provide further details.';
				} else {
					$attachment = NULL;
				}
			}
			
			// Send error notification email.
			backupbuddy_core::mail_error(
				'One or more backup steps reported a failure. ' . $attachment_note . ' Backup failure running function `' . $this->_backup['steps'][$step_index]['function'] . '` with the arguments `' . $args . '` with backup serial `' . $serial . '`. Please run a manual backup of the same type to verify backups are working properly or view the backup status log.',
				NULL,
				$attachment
			);
			
			return false;
			
		} else { // Function finished successfully.
			
			$this->_backup['steps'][$step_index]['finish_time'] = time();
			$this->_backup['updated_time'] = time();
			$this->_backup_options->save();
			
			pb_backupbuddy::status( 'details', sprintf( __('Finished function `%s`. Peak memory usage', 'it-l10n-backupbuddy' ) . ': ' . round( memory_get_peak_usage() / 1048576, 3 ) . ' MB', $this->_backup['steps'][$step_index]['function'] ) );
			pb_backupbuddy::status( 'finishFunction', json_encode( array( 'function' => $this->_backup['steps'][$step_index]['function'] ) ) );
			pb_backupbuddy::status( 'details', '-----' );
			
			$found_another_step = false;
			foreach( $this->_backup['steps'] as $next_step ) { // Loop through each step and see if any have not started yet.
				if ( $next_step['start_time'] == 0 ) { // Another unstarted step exists. Schedule it.
					$found_another_step = true;
					if ( $this->_backup['profile']['backup_mode'] == '2' ) { // Modern mode with crons.
						$this->cron_next_step();
					} else { // classic mode
						$this->process_backup( $this->_backup['serial'], $trigger );
					}
					
					break;
				}
			} // End foreach().
			
			if ( $found_another_step == false ) {
				pb_backupbuddy::status( 'details', __( 'No more backup steps remain. Finishing...', 'it-l10n-backupbuddy' ) );
				$this->_backup['finish_time'] = time();
				$this->_backup_options->save();
				pb_backupbuddy::status( 'startFunction', json_encode( array( 'function' => 'backup_success', 'title' => __( 'Backup completed successfully.', 'it-l10n-backupbuddy' ) ) ) );
				pb_backupbuddy::status( 'finishFunction', json_encode( array( 'function' => 'backup_success' ) ) );
			} else {
				pb_backupbuddy::status( 'details', 'Completed step function `' . $step['function'] . '`.' );
				//pb_backupbuddy::status( 'details', 'The next should run in a moment. If it does not please check for plugin conflicts and that the next step is scheduled in the cron on the Server Information page.' );
			}
			
			return true;
		}
		
		
	} // End process_backup().
	
	
	
	/*	cron_next_step()
	 *	
	 *	Schedule the next step into the cron. Defaults to scheduling to happen _NOW_. Automatically opens a loopback to trigger cron in another process by default.
	 *	
	 *	@param		boolean		$spawn_cron			Whether or not to to spawn a loopback to run the cron. If using an offset this most likely should be false. Default: true
	 *	@param		int			$future_offset		Seconds in the future for this process to run. Most likely set $spawn_cron false if using an offset. Default: 0
	 *	@return		null
	 */
	function cron_next_step( $spawn_cron = true, $future_offset = 0 ) {
		
		pb_backupbuddy::status( 'details', 'Scheduling Cron for `' . $this->_backup['serial'] . '`.' );
		
		// Need to make sure the database connection is active. Sometimes it goes away during long bouts doing other things -- sigh.
		// This is not essential so use include and not require (suppress any warning)
		pb_backupbuddy::status( 'details', 'Loading DB kicker in case database has gone away.' );
		@include_once( pb_backupbuddy::plugin_path() . '/lib/wpdbutils/wpdbutils.php' );
		if ( class_exists( 'pluginbuddy_wpdbutils' ) ) {
			global $wpdb;
			$dbhelper = new pluginbuddy_wpdbutils( $wpdb );
			if ( ! $dbhelper->kick() ) {
				pb_backupbuddy::status( 'error', __('Database Server has gone away, unable to schedule next backup step. The backup cannot continue. This is most often caused by mysql running out of memory or timing out far too early. Please contact your host.', 'it-l10n-backupbuddy' ) );
				pb_backupbuddy::status( 'haltScript', '' ); // Halt JS on page.
				return false;
			} else {
				pb_backupbuddy::status( 'details', 'Database seems to still be connected.' );
			}
		} else {
			pb_backupbuddy::status( 'details', __('Database Server connection status unverified.', 'it-l10n-backupbuddy' ) );
		}
		
		// Schedule event.
		$cron_time = ( time() + $future_offset );
		$cron_tag = pb_backupbuddy::cron_tag( 'process_backup' );
		$cron_args = array( $this->_backup['serial'] );
		pb_backupbuddy::status( 'details', 'Scheduling next step to run at `' . $cron_time . '` (localized time: ' . pb_backupbuddy::$format->date( pb_backupbuddy::$format->localize_time( $cron_time ) ) . ') with cron tag `' . $cron_tag . '` and serial arguments `' . implode( ',', $cron_args ) . '`.' );
		$schedule_result = backupbuddy_core::schedule_single_event( $cron_time, $cron_tag, $cron_args );
		if ( $schedule_result === false ) {
			pb_backupbuddy::status( 'error', 'Unable to schedule next cron step. Verify that another plugin is not preventing / conflicting.' );
		} else {
			pb_backupbuddy::status( 'details', 'Next step scheduled.' );
			pb_backupbuddy::status( 'startAction', 'cronPass' );
			pb_backupbuddy::status( 'cronParams', base64_encode( json_encode( array( 'time' => $cron_time, 'tag' => $cron_tag, 'args' => $cron_args ) ) ) );
		}
		
		// Spawn cron.
		if ( $spawn_cron === true ) {
			spawn_cron( time() + 150 ); // Adds > 60 seconds to get around once per minute cron running limit.
		}
		update_option( '_transient_doing_cron', 0 ); // Prevent cron-blocking for next item.
		
		pb_backupbuddy::status( 'details', 'About to run next step. If the backup does not proceed within 15 seconds then something is interfering with the WordPress CRON system such as: server loopback issues, caching plugins, or scheduling plugins. Try disabling other plugins to see if it resolves issue.  Check the Server Information page cron section to see if the next BackupBuddy step is scheduled to run. Enable "Classic" backup mode on the "Settings" page to rule out non-cron issues.' );
		return;
		
	} // End cron_next_step().
	
	
	
	/*	backup_create_dat_file()
	 *	
	 *	Generates backupbuddy_dat.php within the temporary directory containing the
	 *	random serial in its name. This file contains a serialized array that has been
	 *	XOR encrypted for security.  The XOR key is backupbuddy_SERIAL where SERIAL
	 *	is the randomized set of characters in the ZIP filename. This file contains
	 *	various information about the source site.
	 *	
	 *	@param		string			$trigger			What triggered this backup. Valid values: scheduled, manual.
	 *	@return		boolean			true on success making dat file; else false
	 */
	function backup_create_dat_file( $trigger ) {
		
		pb_backupbuddy::status( 'details', __( 'Creating DAT (data) file snapshotting site & backup information.', 'it-l10n-backupbuddy' ) );
		
		global $wpdb, $current_blog;
		
		$is_multisite = $is_multisite_export = false; //$from_multisite is from a site within a network
		$upload_url_rewrite = $upload_url = '';
		if ( ( is_multisite() && ( $trigger == 'scheduled' ) ) || (is_multisite() && is_network_admin() ) ) { // MS Network Export IF ( in a network and triggered by a schedule ) OR ( in a network and logged in as network admin)
			$is_multisite = true;
		} elseif ( is_multisite() ) { // MS Export (individual site)
			$is_multisite_export = true;
			$uploads = wp_upload_dir();
			$upload_url_rewrite = site_url( str_replace( ABSPATH, '', $uploads[ 'basedir' ] ) ); // URL we rewrite uploads to. REAL direct url.
			$upload_url = $uploads[ 'baseurl' ]; // Pretty virtual path to uploads directory.
		}
		
		// Handle wp-config.php file in a parent directory.
		if ( $this->_backup['type'] == 'full' ) {
			$wp_config_parent = false;
			if ( file_exists( ABSPATH . 'wp-config.php' ) ) { // wp-config in normal place.
				pb_backupbuddy::status( 'details', 'wp-config.php found in normal location.' );
			} else { // wp-config not in normal place.
				pb_backupbuddy::status( 'message', 'wp-config.php not found in normal location; checking parent directory.' );
				if ( file_exists( dirname( ABSPATH ) . '/wp-config.php' ) ) { // Config in parent.
					$wp_config_parent = true;
					pb_backupbuddy::status( 'message', 'wp-config.php found in parent directory. Copying wp-config.php to temporary location for backing up.' );
					$this->_backup['wp-config_in_parent'] = true;
					
					copy( dirname( ABSPATH ) . '/wp-config.php', $this->_backup['temp_directory'] . 'wp-config.php' );
				} else {
					pb_backupbuddy::status( 'error', 'wp-config.php not found in normal location NOR parent directory. This will result in an incomplete backup which will be marked as bad.' );
				}
			}
		} else {
			$wp_config_parent = false;
		}
		
		global $wp_version;
		
		$totalPosts = 0;
		foreach( wp_count_posts( 'post' ) as $counttype => $count ) {
			$totalPosts += $count;
		}
		$totalPages = 0;
		foreach( wp_count_posts( 'page' ) as $counttype => $count ) {
			$totalPages += $count;
		}
		$totalComments = 0;
		foreach( wp_count_comments() as $counttype => $count ) {
			$totalComments += $count;
		}
		$totalUsers = count_users();
		$totalUsers = $totalUsers['total_users'];
		
		$dat_content = array(
			
			// Backup Info.
			'backupbuddy_version'		=> pb_backupbuddy::settings( 'version' ),
			'wordpress_version'			=>		$wp_version,											// WordPress version.
			'backup_time'				=> $this->_backup['start_time'],
			'backup_type'				=> $this->_backup['type'],
			'profile'					=> $this->_backup['profile'],
			'default_profile'			=>		pb_backupbuddy::$options['profiles'][0],				// Default profile.
			'serial'					=> $this->_backup['serial'],
			'trigger'					=> $trigger,													// What triggered this backup. Valid values: scheduled, manual.
			'wp-config_in_parent'		=> $wp_config_parent,											// Whether or not the wp-config.php file is in one parent directory up. If in parent directory it will be copied into the temp serial directory along with the .sql and DAT file. On restore we will NOT place in a parent directory due to potential permission issues, etc. It will be moved into the normal location. Value set to true later in this function if applicable.
			
			// WordPress Info.
			'abspath'					=> ABSPATH,
			'siteurl'					=> site_url(),
			'homeurl'					=> home_url(),
			'blogname'					=> get_option( 'blogname' ),
			'blogdescription'			=> get_option( 'blogdescription' ),
			'active_plugins'			=> implode( ', ', get_option( 'active_plugins' ) ),
			'posts'						=>		$totalPosts,											// Total WP posts, publishes, draft, private, trash, etc.
			'pages'						=>		$totalPages,											// Total WP pages, publishes, draft, private, trash, etc.
			'comments'					=>		$totalComments,											// Total WP comments, approved, spam, etc
			'users'						=>		$totalUsers,											// Total users on site.st
			
			// Database Info. Remaining sensitive info added in after printing out DAT (for security).
			'db_prefix'					=> $wpdb->prefix,
			'db_server'					=> DB_HOST,
			'db_name'					=> DB_NAME,
			'db_user'					=> '', // Set several lines down after printing out DAT.
			'db_password'				=> '', // Set several lines down after printing out DAT.
			'db_exclusions'				=> implode( ',', explode( "\n", $this->_backup['profile']['mysqldump_additional_excludes'] ) ),
			'db_inclusions'				=> implode( ',', explode( "\n", $this->_backup['profile']['mysqldump_additional_includes'] ) ),
			'breakout_tables'			=> $this->_backup['breakout_tables'],							// Tables broken out into individual backup steps.
			'tables_sizes'				=> $this->_backup['table_sizes'],								// Tables backed up and their sizes.
			
			// Multisite Info.
			'is_multisite' 				=> $is_multisite,												// Full Network backup?
			'is_multisite_export' 		=> $is_multisite_export,										// Subsite backup (export)?
			'domain'					=> is_object( $current_blog ) ? $current_blog->domain : '',		// Ex: bob.com
			'path'						=> is_object( $current_blog ) ? $current_blog->path : '',		// Ex: /wordpress/
			'upload_url' 				=> $upload_url,  												// Pretty URL.
			'upload_url_rewrite' 		=> $upload_url_rewrite,											// Real existing URL that the pretty URL will be rewritten to.
			
			// ImportBuddy Options.
			// 'import_display_previous_values'	=>	pb_backupbuddy::$options['import_display_previous_values'],	// Whether or not to display the previous values from the source on import. Useful if customer does not want to blatantly display previous values to anyone restoring the backup.
			
		); // End setting $dat_content.
		
		
		// If currently using SSL or forcing admin SSL then we will check the hardcoded defined URL to make sure it matches.
		if ( is_ssl() OR ( defined( 'FORCE_SSL_ADMIN' ) && FORCE_SSL_ADMIN == true ) ) {
			$dat_content['siteurl'] = get_option('siteurl');
			pb_backupbuddy::status( 'details', __('Compensating for SSL in siteurl.', 'it-l10n-backupbuddy' ) );
		}
		
		// Output for troubleshooting.
		pb_backupbuddy::status( 'details', 'DAT file contents (sans database user/pass): ' . str_replace( "\n", '; ', print_r( $dat_content, true ) ) );
		
		// Remaining DB settings.
		$dat_content['db_user'] = DB_USER;
		$dat_content['db_password'] = DB_PASSWORD;
		
		
		// Serialize .dat file array.
		$dat_content = base64_encode( serialize( $dat_content ) );
		
		// Write data to the dat file.
		$dat_file = $this->_backup['temp_directory'] . 'backupbuddy_dat.php';
		if ( false === ( $file_handle = fopen( $dat_file, 'w' ) ) ) {
			pb_backupbuddy::status( 'details', sprintf( __('Error #9017: Temp data file is not creatable/writable. Check your permissions. (%s)', 'it-l10n-backupbuddy' ), $dat_file  ) );
			pb_backupbuddy::status( 'error', 'Temp data file is not creatable/writable. Check your permissions. (' . $dat_file . ')', '9017' );
			return false;
		}
		fwrite( $file_handle, "<?php die('Access Denied.'); ?>\n" . $dat_content );
		fclose( $file_handle );
		
		pb_backupbuddy::status( 'details', __('Finished creating DAT (data) file.', 'it-l10n-backupbuddy' ) );
		
		return true;
		
	} // End backup_create_dat_file().
	
	
	
	/*	backup_create_database_dump()
	 *	
	 *	Prepares configuration and passes to the mysqlbuddy library to handle backing up the database.
	 *	Automatically handles falling back to compatibility modes.
	 *	
	 *	@return		boolean				True on success; false otherwise.
	 */
	function backup_create_database_dump( $tables ) {
		
		pb_backupbuddy::status( 'milestone', 'start_database' );
		pb_backupbuddy::status( 'message', __('Starting database backup process.', 'it-l10n-backupbuddy' ) );
		
		if ( pb_backupbuddy::$options['force_mysqldump_compatibility'] == '1' ) {
			pb_backupbuddy::status( 'message', 'Forcing database dump compatibility mode based on settings. Use PHP-based dump mode only.' );
			$force_methods = array( 'php' ); // Force php mode only.
		} else {
			pb_backupbuddy::status( 'message', 'Using auto-detected database dump method(s) based on settings.' );
			$force_methods = array(); // Default, auto-detect.
		}
		
		
		// Load mysqlbuddy and perform dump.
		require_once( pb_backupbuddy::plugin_path() . '/lib/mysqlbuddy/mysqlbuddy.php' );
		global $wpdb;
		pb_backupbuddy::$classes['mysqlbuddy'] = new pb_backupbuddy_mysqlbuddy( DB_HOST, DB_NAME, DB_USER, DB_PASSWORD, $wpdb->prefix, $force_methods ); // $database_host, $database_name, $database_user, $database_pass, $old_prefix, $force_method = array()
		$result = pb_backupbuddy::$classes['mysqlbuddy']->dump( $this->_backup['temp_directory'], $tables );
		
		
		// Check and make sure mysql server is still around. If it's missing at this point we may not be able to trust that it succeeded properly.
		/*
		// REMOVED 3-3-2014. PHP dump now checks connection status after each table and command line is not related to this. Settings are all stored in fileoptions so this is no longer relevant.
		
		global $wpdb;
		if ( @mysql_ping( $wpdb->dbh ) === false ) { // No longer connected to database if false.
			pb_backupbuddy::status( 'error', __( 'ERROR #9027b: The mySQL server went away at some point during the database dump step. This is almost always caused by mySQL running out of memory or the mysql server timing out far too early. Contact your host. The database dump integrity can no longer be guaranteed so the backup has been halted.' ) );
			if ( $result === true ) {
				pb_backupbuddy::status( 'details', 'The database dump reported SUCCESS prior to this problem.' );
			} else {
				pb_backupbuddy::status( 'details', 'The database dump reported FAILURE prior to this problem.' );
			}
			pb_backupbuddy::status( 'haltScript', '' ); // Halt JS on page.
			return false;
		}
		*/
		
		
		return $result;

	} // End backup_create_database_dump().
	
	
	
	/*	backup_zip_files()
	 *	
	 *	Create ZIP file containing everything.
	 *	
	 *	@return		boolean			True on success; false otherwise.
	 */
	function backup_zip_files() {
		
		pb_backupbuddy::status( 'milestone', 'start_files' );
		pb_backupbuddy::status( 'details', 'Backup root: `' . $this->_backup['backup_root'] . '`.' );
		
		// Set compression on / off.
		//pb_backupbuddy::$classes['zipbuddy']->set_compression( $this->_backup['compression'] );
		
		
		// Calculate some statistics to store in meta later. These need to be calculated before zipping in case the DB goes away later to prevent a possible failure.
		$totalPosts = 0;
		foreach( wp_count_posts( 'post' ) as $type => $count ) {
			$totalPosts += $count;
		}
		$totalPages = 0;
		foreach( wp_count_posts( 'page' ) as $type => $count ) {
			$totalPages += $count;
		}
		$totalComments = 0;
		foreach( wp_count_comments() as $type => $count ) {
			$totalComments += $count;
		}
		$totalUsers = count_users();
		$totalUsers = $totalUsers['total_users'];
		
		global $wpdb;
		$db_prefix = $wpdb->prefix;
		
		
		// Create zip file!
		$zip_response = pb_backupbuddy::$classes['zipbuddy']->add_directory_to_zip(
			$this->_backup['archive_file'],									// string	Zip file to create.
			$this->_backup['backup_root'],									// string	Directory to zip up (root).
			$this->_backup['directory_exclusions'],							// array	Files/directories to exclude. (array of strings).
			$this->_backup['temporary_zip_directory']						// string	Temp directory location to store zip file in.
		);
		
		
		// Zip results.
		if ( $zip_response === true ) { // Zip success.
			pb_backupbuddy::status( 'message', __('Backup ZIP file successfully created.', 'it-l10n-backupbuddy' ) );
			if ( chmod( $this->_backup['archive_file'], 0644) ) {
				pb_backupbuddy::status( 'details', __('Chmod of ZIP file to 0644 succeeded.', 'it-l10n-backupbuddy' ) );
			} else {
				pb_backupbuddy::status( 'details', __('Chmod of ZIP file to 0644 failed.', 'it-l10n-backupbuddy' ) );
			}
			
			// Save meta information in comment.
			if ( '0' == pb_backupbuddy::$options['save_comment_meta'] ) {
				pb_backupbuddy::status( 'details', 'Skipping saving meta data to zip comment based on settings.' );
			} else {
				pb_backupbuddy::status( 'details', 'Saving meta data to zip comment.' );
				
				
				
				global $wp_version;
				$meta = array(
						'serial'		=>	$this->_backup['serial'],
						'siteurl'		=>	site_url(),
						'type'			=>	$this->_backup['type'],
						'profile'		=>	$this->_backup['profile']['title'],
						'created'		=>	$this->_backup['start_time'],
						'db_prefix'		=>	$db_prefix,
						'bb_version'	=>	pb_backupbuddy::settings( 'version' ),
						'wp_version'	=>	$wp_version,
						'dat_path'		=>	str_replace( $this->_backup['backup_root'], '', $this->_backup['temp_directory'] . 'backupbuddy_dat.php' ),
						'posts'			=>	$totalPosts,
						'pages'			=>	$totalPages,
						'comments'		=>	$totalComments,
						'users'			=>	$totalUsers,
						'note'			=>	'',
					);
				$comment = backupbuddy_core::normalize_comment_data( $meta );
				pb_backupbuddy::status( 'startAction', 'zipCommentMeta' );
				$comment_result = pb_backupbuddy::$classes['zipbuddy']->set_comment( $this->_backup['archive_file'], $comment );
				pb_backupbuddy::status( 'finishAction', 'zipCommentMeta' );
				if ( $comment_result !== true ) {
					pb_backupbuddy::status( 'warning', 'Unable to save meta data to zip comment. This is not a fatal warning & will not impact the backup itself.' );
				} else {
					pb_backupbuddy::status( 'details', 'Saved meta data to zip comment.' );
				}
			}
			
		} else { // Zip failure.
			
			// Delete temporary data directory.
			if ( file_exists( $this->_backup['temp_directory'] ) ) {
				pb_backupbuddy::status( 'details', __('Removing temp data directory.', 'it-l10n-backupbuddy' ) );
				pb_backupbuddy::$filesystem->unlink_recursive( $this->_backup['temp_directory'] );
			}
			
			// Delete temporary ZIP directory.
			if ( file_exists( $this->_backup['temporary_zip_directory'] ) ) {
				pb_backupbuddy::status( 'details', __('Removing temp zip directory.', 'it-l10n-backupbuddy' ) );
				pb_backupbuddy::$filesystem->unlink_recursive( $this->_backup['temporary_zip_directory'] );
			}
			
			pb_backupbuddy::status( 'error', __('Error #3382: Backup FAILED. Unable to successfully generate ZIP archive.', 'it-l10n-backupbuddy' ) );
			pb_backupbuddy::status( 'error', __('Error #3382 help: http://ithemes.com/codex/page/BackupBuddy:_Error_Codes#3382', 'it-l10n-backupbuddy' ) );
			pb_backupbuddy::status( 'haltScript', '' ); // Halt JS on page.
			return false;
			
		} // end zip failure.
		
		
		// Need to make sure the database connection is active. Sometimes it goes away during long bouts doing other things -- sigh.
		// This is not essential so use include and not require (suppress any warning)
		pb_backupbuddy::status( 'details', 'Loading DB kicker in case database has gone away.' );
		@include_once( pb_backupbuddy::plugin_path() . '/lib/wpdbutils/wpdbutils.php' );
		if ( class_exists( 'pluginbuddy_wpdbutils' ) ) {
			// This is the database object we want to use
			global $wpdb;
			
			// Get our helper object and let it use us to output status messages
			$dbhelper = new pluginbuddy_wpdbutils( $wpdb );
			
			// If we cannot kick the database into life then signal the error and return false which will stop the backup
			// Otherwise all is ok and we can just fall through and let the function return true
			if ( !$dbhelper->kick() ) {
				pb_backupbuddy::status( 'error', __('Backup FAILED. Backup file produced but Database Server has gone away, unable to schedule next backup step', 'it-l10n-backupbuddy' ) );
				return false;
			} else {
				pb_backupbuddy::status( 'details', 'Database seems to still be connected.' );
			}
		} else {
			// Utils not available so cannot verify database connection status - just notify
			pb_backupbuddy::status( 'details', __('Database Server connection status unverified.', 'it-l10n-backupbuddy' ) );
		}
		
		return true;
		
	} // End backup_zip_files().
	
	
	
	/*	trim_old_archives()
	 *	
	 *	Get rid of excess archives based on user-defined parameters.
	 *	
	 *	@param		
	 *	@return		
	 */
	function trim_old_archives() {
		
		pb_backupbuddy::status( 'details', __('Trimming old archives (if needed).', 'it-l10n-backupbuddy' ) );
		
		$summed_size = 0;
		
		$file_list = glob( backupbuddy_core::getBackupDirectory() . 'backup*.zip' );
		if ( is_array( $file_list ) && !empty( $file_list ) ) {
			foreach( (array) $file_list as $file ) {
				$file_stats = stat( $file );
				$modified_time = $file_stats['mtime'];
				$filename = str_replace( backupbuddy_core::getBackupDirectory(), '', $file ); // Just the file name.
				$files[$modified_time] = array(
													'filename'				=>		$filename,
													'size'					=>		$file_stats['size'],
													'modified'				=>		$modified_time,
												);
				$summed_size += ( $file_stats['size'] / 1048576 ); // MB
			}
		}
		unset( $file_list );
		if ( empty( $files ) ) { // return if no archives (nothing else to do).
			pb_backupbuddy::status( 'details', __( 'No old archive trimming needed.', 'it-l10n-backupbuddy' ) );
			return true;
		} else {
			krsort( $files );
		}
		
		$trim_count = 0;
		
		
		// Limit by age if set.
		if ( (int) pb_backupbuddy::$options['archive_limit_age'] > 0 ) {
			foreach( $files as $file_modified => $file ) {
				if ( ! is_numeric( $file['modified'] ) ) { // Could not get age so skipping.
					continue;
				}
				$backup_age = (int) ( time() - $file['modified'] ) / 60 / 60 / 24;
				if ( $backup_age > pb_backupbuddy::$options['archive_limit_age'] ) { // Too old; delete!
					pb_backupbuddy::status( 'details', __('Deleting old archive `' . $file['filename'] . '` as it exceeds the maximum age limit `' . pb_backupbuddy::$options['archive_limit_age'] . '` allowed at `' . $backup_age . '` days.' ) );
					unlink( backupbuddy_core::getBackupDirectory() . $file['filename'] );
					unset( $files[$file_modified] );
					$trim_count++;
				} else {
					//pb_backupbuddy::status( 'details', 'Not deleted: ' . $backup_age );
				}
			}
		} // end age limit.
		
		
		// Limit by number of archives if set. Deletes oldest archives over this limit.
		if ( ( pb_backupbuddy::$options['archive_limit'] > 0 ) && ( count( $files ) ) > pb_backupbuddy::$options['archive_limit'] ) {
			// Need to trim.
			$i = 0;
			foreach( $files as $file_modified => $file ) {
				$i++;
				if ( $i > pb_backupbuddy::$options['archive_limit'] ) {
					pb_backupbuddy::status( 'details', sprintf( __('Deleting old archive `%s` as it causes archives to exceed total number allowed.', 'it-l10n-backupbuddy' ), $file['filename'] ) );
					unlink( backupbuddy_core::getBackupDirectory() . $file['filename'] );
					unset( $files[$file_modified] );
					$trim_count++;
				}
			}
		} // end number of archives limit.
		
		
		// Limit by size of archives, oldest first if set.
		$files = array_reverse( $files, true ); // Reversed so we delete oldest files first as long as size limit still is surpassed; true = preserve keys.
		if ( ( pb_backupbuddy::$options['archive_limit_size'] > 0 ) && ( $summed_size > pb_backupbuddy::$options['archive_limit_size'] ) ) {
			// Need to trim.
			foreach( $files as $file_modified => $file ) {
				if ( $summed_size > pb_backupbuddy::$options['archive_limit_size'] ) {
					$summed_size = $summed_size - ( $file['size'] / 1048576 );
					pb_backupbuddy::status( 'details', sprintf( __('Deleting old archive `%s` due as it causes archives to exceed total size allowed.', 'it-l10n-backupbuddy' ),  $file['filename'] ) );
					if ( $file['filename'] != basename( $this->_backup['archive_file'] ) ) { // Delete excess archives as long as it is not the just-made backup.
						unlink( backupbuddy_core::getBackupDirectory() . $file['filename'] );
						unset( $files[$file_modified] );
						$trim_count++;
					} else {
						$message = __( 'ERROR #9028: Based on your backup archive limits (size limit) the backup that was just created would be deleted. Skipped deleting this backup. Please update your archive limits.' );
						pb_backupbuddy::status( 'message', $message );
						backupbuddy_core::mail_error( $message );
					}
				}
			}
		} // end combined file size limit.
		
		
		pb_backupbuddy::status( 'details', 'Trimmed ' . $trim_count . ' old archives based on settings archive limits.' );
		return true;
		
	} // End trim_old_archives().
	
	
	
	/* integrity_check()
	 *
	 * Perform integrity check on backup file to confirm backup.
	 *
	 */
	function integrity_check() {
		
		pb_backupbuddy::status( 'milestone', 'start_integrity' );
		pb_backupbuddy::status( 'message', __( 'Scanning and verifying backup file integrity.', 'it-l10n-backupbuddy' ) );
		if ( ( $this->_backup['profile']['type'] != 'files' ) && ( $this->_backup['profile']['skip_database_dump'] == '1' ) ) {
			pb_backupbuddy::status( 'warning', 'WARNING: Database .SQL file does NOT exist because the database dump has been set to be SKIPPED based on settings. Use with caution!' );
		}
		
		$options = array(
			'skip_database_dump' => $this->_backup['profile']['skip_database_dump'],
		);
		
		$result = backupbuddy_core::backup_integrity_check( $this->_backup['archive_file'], $this->_backup_options, $options, $skipLogRedirect = true );
		if ( false === $result['is_ok'] ) {
			$message = __( 'Backup failed to pass integrity check. The backup may have failed OR the backup may be valid but the integrity check could not verify it. This could be due to permissions, large file size, running out of memory, or other error. Verify you have not excluded one or more required files, paths, or database tables; check for warnings above in the status log.  You may wish to manually verify the backup file is functional or re-scan.', 'it-l10n-backupbuddy' );
			pb_backupbuddy::status( 'error', $message );
			
			pb_backupbuddy::status( 'details', 'Running cleanup procedure now in current step as backup procedure is halting.' );
			$this->post_backup( true ); // Post backup cleanup in fail mode.
			//pb_backupbuddy::status( 'haltScript', '' ); // Halt JS on page.
			
			pb_backupbuddy::status( 'details', __( 'Sending integrity check failure email.', 'it-l10n-backupbuddy' ) );
			backupbuddy_core::mail_error( $message );
			
			return false;
		}
		
		pb_backupbuddy::status( 'milestone', 'finish_integrity' );
		return true;
		
	} // End integrity_check().
	
	
	
	/*	post_backup()
	 *	
	 *	Post-backup procedured. Clean up, send notifications, etc.
	 *	
	 *	@return		null
	 */
	function post_backup( $fail_mode = false, $cancel_backup = false ) {
		pb_backupbuddy::status( 'message', __('Cleaning up after backup.', 'it-l10n-backupbuddy' ) );
		
		// Delete temporary data directory.
		if ( file_exists( $this->_backup['temp_directory'] ) ) {
			pb_backupbuddy::status( 'details', __('Removing temp data directory.', 'it-l10n-backupbuddy' ) );
			pb_backupbuddy::$filesystem->unlink_recursive( $this->_backup['temp_directory'] );
		}
		// Delete temporary ZIP directory.
		if ( file_exists( backupbuddy_core::getBackupDirectory() . 'temp_zip_' . $this->_backup['serial'] . '/' ) ) {
			pb_backupbuddy::status( 'details', __('Removing temp zip directory.', 'it-l10n-backupbuddy' ) );
			pb_backupbuddy::$filesystem->unlink_recursive( backupbuddy_core::getBackupDirectory() . 'temp_zip_' . $this->_backup['serial'] . '/' );
		}
		
		
		if ( true === $fail_mode ) {
			pb_backupbuddy::status( 'warning', 'Backup archive limiting has been skipped since there was an error to avoid deleting potentially good backups to make room for a potentially bad backup.' );
		} else {
			$this->trim_old_archives(); // Clean up any old excess archives pushing us over defined limits in settings.
		}
		
		
		if ( true === $cancel_backup ) {
			pb_backupbuddy::status( 'details', 'Backup stopped so deleting backup ZIP file.' );
			$unlink_result = @unlink( $this->_backup['archive_file'] );
			if ( true === $unlink_result ) {
				pb_backupbuddy::status( 'details', 'Deleted stopped backup file.' );
			} else {
				pb_backupbuddy::status( 'error', 'Unable to delete stopped backup file. You should delete it manually as it may be damaged from stopping mid-backup. File to delete: `' . $this->_backup['archive_file'] . '`.' );
			}
			
			$this->_backup['finish_time'] = -1;
			//pb_backupbuddy::save();
			$this->_backup_options->save();
			
		} else { // Not cancelled.
			$this->_backup['archive_size'] = @filesize( $this->_backup['archive_file'] );
			pb_backupbuddy::status( 'details', __('Final ZIP file size', 'it-l10n-backupbuddy' ) . ': ' . pb_backupbuddy::$format->file_size( $this->_backup['archive_size'] ) );
			pb_backupbuddy::status( 'archiveSize', pb_backupbuddy::$format->file_size( $this->_backup['archive_size'] ) );
			
			if ( $fail_mode === false ) { // Not cancelled and did not fail so mark finish time.
				
				//error_log( print_r( $this->_backup_options->options, true ) );
				
				$archiveFile = basename( $this->_backup_options->options['archive_file'] );
				
				// Calculate backup download URL, if any.
				//$downloadURL = pb_backupbuddy::ajax_url( 'download_archive' ) . '&backupbuddy_backup=' . $archiveFile;
				$downloadURL = '';
				$abspath = str_replace( '\\', '/', ABSPATH ); // Change slashes to handle Windows as we store backup_directory with Linux-style slashes even on Windows.
				$backup_dir = str_replace( '\\', '/', backupbuddy_core::getBackupDirectory() );
				if ( FALSE !== stristr( $backup_dir, $abspath ) ) { // Make sure file to download is in a publicly accessible location (beneath WP web root technically).
					//pb_backupbuddy::status( 'details', 'mydir: `' . $backup_dir . '`, abs: `' . $abspath . '`.');
					$sitepath = str_replace( $abspath, '', $backup_dir );
					$downloadURL = rtrim( site_url(), '/\\' ) . '/' . trim( $sitepath, '/\\' ) . '/' . $archiveFile;
				}
				
				
				$integrityIsOK = '-1';
				if ( isset( $this->_backup_options->options['integrity']['is_ok'] ) ) {
					$integrityIsOK = $this->_backup_options->options['integrity']['is_ok'];
				}
				
				$destinations = array();
				foreach( $this->_backup_options->options['steps'] as $step ) {
					if ( 'send_remote_destination' == $step['function'] ) {
						$destinations[] = array(
											'id' => $step['args'][0],
											'title' => pb_backupbuddy::$options['remote_destinations'][ $step['args'][0] ]['title'],
											'type' => pb_backupbuddy::$options['remote_destinations'][ $step['args'][0] ]['type'],
										);
					}
				}
				
				pb_backupbuddy::status( 'details', 'Updating statistics for last backup completed and number of edits since last backup.' );
				$finishTime = time();
				pb_backupbuddy::$options['last_backup_finish'] = $finishTime;
				pb_backupbuddy::$options['last_backup_stats'] = array(
																	//'serial'			=> $this->_backup['serial'],
																	'archiveFile'		=> $archiveFile,
																	'archiveURL'		=> $downloadURL,
																	'archiveSize'		=> $this->_backup['archive_size'],
																	'start'				=> pb_backupbuddy::$options['last_backup_start'],
																	'finish'			=> $finishTime,
																	'type'				=> $this->_backup_options->options['profile']['type'],
																	'profileTitle'		=> htmlentities( $this->_backup_options->options['profile']['title'] ),
																	'scheduleTitle'		=> $this->_backup_options->options['schedule_title'], // Empty string is no schedule.
																	'integrityStatus'	=> $integrityIsOK, // 1, 0, -1 (unknown)
																	'destinations'		=> $destinations, // Index is destination ID. Empty array if none.
																);
				//error_log( print_r( pb_backupbuddy::$options['last_backup_stats'], true ) );
				pb_backupbuddy::$options['edits_since_last'] = 0; // Reset edit stats for notifying user of how many posts/pages edited since last backup happened.
				pb_backupbuddy::save();
			}
			
		}
		
		
		if ( $this->_backup['trigger'] == 'manual' ) {
			// No more manual notifications. Removed Feb 2012 before 3.0.
		} elseif ( $this->_backup['trigger'] == 'scheduled' ) {
			if ( ( false === $fail_mode ) && ( false === $cancel_backup ) ) {
				pb_backupbuddy::status( 'details', __( 'Sending scheduled backup complete email notification.', 'it-l10n-backupbuddy' ) );
				$message = 'completed successfully in ' . pb_backupbuddy::$format->time_duration( time() - $this->_backup['start_time'] ) . ".\n";
				backupbuddy_core::mail_notify_scheduled( $this->_backup['serial'], 'complete', __( 'Scheduled backup', 'it-l10n-backupbuddy' ) . ' "' . $this->_backup['schedule_title'] . '" ' . $message );
			}
		} else {
			pb_backupbuddy::status( 'error', 'Error #4343434. Unknown backup trigger.' );
		}
		
		
		pb_backupbuddy::status( 'message', __( 'Finished cleaning up.', 'it-l10n-backupbuddy' ) );
		
		if ( true === $cancel_backup ) {
			pb_backupbuddy::status( 'details', 'Backup cancellation complete.' );
			return false;
		} else {
			if ( true === $fail_mode ) {
				pb_backupbuddy::status( 'details', __( 'As this backup did not pass the integrity check you should verify it manually or re-scan. Integrity checks can fail on good backups due to permissions, large file size exceeding memory limits, etc. You may manually disable integrity check on the Settings page but you will no longer be notified of potentially bad backups.', 'it-l10n-backupbuddy' ) );
			} else {
				
				//$stats = stat( $this->_backup['archive_file'] );
				//$sizeFormatted = pb_backupbuddy::$format->file_size( $stats['size'] );
				pb_backupbuddy::status(
					'archiveInfo',
					json_encode( array(
						'file' => basename( $this->_backup['archive_file'] ),
						'url' => pb_backupbuddy::ajax_url( 'download_archive' ) . '&backupbuddy_backup=' . basename( $this->_backup['archive_file'] ),
						//'sizeBytes' => $stats,
						//'sizeFormatted' => $sizeFormatted,
					) )
				);
				
			}
		}
		
		return true;
		
	} // End post_backup().
	
	
	
	/*	send_remote_destination()
	 *	
	 *	Send the current backup to a remote destination such as S3, Dropbox, FTP, etc.
	 *	Scheduled remote sends end up coming through here before passing to core.
	 *	
	 *	@param		int		$destination_id		Destination ID (remote destination array index) to send to.
	 *	@param		boolean	$delete_after		Whether or not to delete backup file after THIS successful remote transfer.
	 *	@return		boolean						Returns result of pb_backupbuddy::send_remote_destination(). True (success) or false (failure).
	 */
	function send_remote_destination( $destination_id, $delete_after = false ) {
		
		pb_backupbuddy::status( 'details', 'Sending file to remote destination ID: `' . $destination_id . '`. Delete after: `' . $delete_after . '`.' );
		pb_backupbuddy::status( 'details', 'IMPORTANT: If the transfer is set to be chunked then only the first chunk status will be displayed during this process. Subsequent chunks will happen after this has finished.' );
		$response = backupbuddy_core::send_remote_destination( $destination_id, $this->_backup['archive_file'], '', false, $delete_after );
		
		if ( false === $response ) { // Send failure.
			$error_message = 'BackupBuddy failed sending a backup to the remote destination "' . pb_backupbuddy::$options['remote_destinations'][ $destination_id ]['title'] . '" (id: ' . $destination_id . '). Please verify and test destination settings and permissions. Check the error log for further details.';
			pb_backupbuddy::status( 'error', 'Failure sending to remote destination. Details: ' . $error_message );
			backupbuddy_core::mail_error( $error_message );
		}
		
	} // End send_remote_destination().
	
	
	
	// DEPRECATED Mar 5, 2013. - Dustin
	/*	post_remote_delete()
	 *	
	 *	Deletes backup archive. Used to delete the backup after sending to a remote destination for scheduled backups.
	 *	
	 *	@return		boolean		True on deletion success; else false.
	 */
	function post_remote_delete() {
		
		
		// DEPRECATED FUNCTION. DO NOT USE.
		
		
		pb_backupbuddy::status( 'error', 'CALL TO DEPRECATED FUNCTION post_remote_delete().' );
		pb_backupbuddy::status( 'details', 'Deleting local copy of file sent remote.' );
		if ( file_exists( $this->_backup['archive_file'] ) ) {
			unlink( $this->_backup['archive_file'] );
		}
		
		if ( file_exists( $this->_backup['archive_file'] ) ) {
			pb_backupbuddy::status( 'details', __('Error. Unable to delete local archive as requested.', 'it-l10n-backupbuddy' ) );
			return false; // Didnt delete.
		} else {
			pb_backupbuddy::status( 'details', __('Deleted local archive as requested.', 'it-l10n-backupbuddy' ) );
			return true; // Deleted.
		}
	} // End post_remote_delete().
	
	
	
	
	
	
	
	
	
	
	/********* BEGIN MULTISITE (Exporting subsite; creates a standalone backup) *********/
	
	
	
	/*	ms_download_extract_wordpress()
	 *	
	 *	Used by Multisite Exporting.
	 *	Downloads and extracts the latest WordPress for making a standalone backup of a subsite.
	 *	Authored by Ron H. Modified by Dustin B.
	 *	
	 *	@return		boolean		True on success, else false.
	 */
	public function ms_download_extract_wordpress() {
		
		// Step 1 - Download a copy of WordPress.
		if ( !function_exists( 'download_url' ) ) {
			pb_backupbuddy::status( 'details', 'download_url() function not found. Loading `/wp-admin/includes/file.php`.' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}
		pb_backupbuddy::status( 'message', 'Downloading latest WordPress ZIP file.' );
		$wp_file = download_url( 'http://wordpress.org/latest.zip' );
		if ( is_wp_error( $wp_file ) ) { // Grabbing WordPress ZIP failed.
			pb_backupbuddy::status( 'error', 'Error getting latest WordPress ZIP file: `' . $wp_file->get_error_message() . '`.' );
			return false;
		} else { // Grabbing WordPress ZIP succeeded.
			//error_log ('nowperror' );
			pb_backupbuddy::status( 'details', 'Latest WordPress ZIP file successfully downloaded.' );
		}
		
		
		// Step 2 - Extract WP into a separate directory.
		if ( !isset( pb_backupbuddy::$classes['zipbuddy'] ) ) {
			pb_backupbuddy::$classes['zipbuddy'] = new pluginbuddy_zipbuddy( $this->_options['backup_directory'] );
		}
		pb_backupbuddy::status( 'details', 'About to unzip file.' );
		ob_start();
		pb_backupbuddy::$classes['zipbuddy']->unzip( $wp_file, dirname( $this->_backup['backup_root'] ) );
		pb_backupbuddy::status( 'details', 'Unzip complete.' );
		pb_backupbuddy::status( 'details', 'Debugging information: `' . ob_get_clean() . '`' );
		
		@unlink( $wp_file );
		if ( file_exists( $wp_file ) ) { // Check to see if unlink() worked.
			pb_backupbuddy::status( 'warning', 'Unable to delete temporary WordPress file `' . $wp_file . '`. You may want to delete this after the backup / export completed.' );
		}
		
		return true;
		
	} // End ms_download_wordpress().
	
	
	
	/*	ms_create_wp_config()
	 *	
	 *	Used by Multisite Exporting.
	 *	Creates a standalone wp-config.php file for making a standalone backup from a subsite.
	 *	Authored by Ron H. Modified by Dustin B.
	 *	
	 *	@return		boolean			Currently only returns true.
	 */
	public function ms_create_wp_config() {
		
		pb_backupbuddy::status( 'message', 'Creating new wp-config.php file for temporary WordPress installation.' );
		
		global $current_blog;
		$blog_id = absint( $current_blog->blog_id );
		
		//Step 3 - Create new WP-Config File
		$to_file = "<?php\n";
		$to_file .= sprintf( "define( 'DB_NAME', '%s' );\n", '' );
		$to_file .= sprintf( "define( 'DB_USER', '%s' );\n", '' );
		$to_file .= sprintf( "define( 'DB_PASSWORD', '%s' );\n", '' );
		$to_file .= sprintf( "define( 'DB_HOST', '%s' );\n", '' );
		$charset = defined( 'DB_CHARSET' ) ? DB_CHARSET : '';
		$collate = defined( 'DB_COLLATE' ) ? DB_COLLATE : '';
		$to_file .= sprintf( "define( 'DB_CHARSET', '%s' );\n", $charset );
		$to_file .= sprintf( "define( 'DB_COLLATE', '%s' );\n", $collate );
		
		//Attempt to remotely retrieve salts
		$salts = wp_remote_get( 'https://api.wordpress.org/secret-key/1.1/salt/' );
		if ( !is_wp_error( $salts ) ) { // Success.
			$to_file .= wp_remote_retrieve_body( $salts ) . "\n";
		} else { // Failed.
			pb_backupbuddy::status( 'warning', 'Error getting salts from WordPress.org for wp-config.php. You may need to manually edit your wp-config on restore. Error: `' . $salts->get_error_message() . '`.' );
		}
		$to_file .= sprintf( "define( 'WPLANG', '%s' );\n", WPLANG );
		$to_file .= sprintf( '$table_prefix = \'%s\';' . "\n", 'bbms' . $blog_id . '_' );
		
		$to_file .= "if ( !defined('ABSPATH') ) { \n\tdefine('ABSPATH', dirname(__FILE__) . '/'); }";
		$to_file .= "/** Sets up WordPress vars and included files. */\n
		require_once(ABSPATH . 'wp-settings.php');";
		$to_file .= "\n?>";
		
		//Create the file, save, and close
		$file_handle = fopen( $this->_backup['backup_root'] . 'wp-config.php', 'w' );
		fwrite( $file_handle, $to_file );
		fclose( $file_handle );
		
		pb_backupbuddy::status( 'message', 'Temporary WordPress wp-config.php file created.' );
		
		return true;
	} // End ms_create_wp_config().
	
	
	
	/*	ms_copy_plugins()
	 *	
	 *	Used by Multisite Exporting.
	 *	Copies over the selected plugins for inclusion into the backup for creating a standalone backup from a subsite.
	 *	Authored by Ron H. Modified by Dustin B.
	 *	
	 *	@return		boolean			True on success, else false.
	 */
	public function ms_copy_plugins() {
	
		pb_backupbuddy::status( 'message', 'Copying selected plugins into temporary WordPress installation.' );
		
		// Step 4 - Copy over plugins.
		// Move over plugins.
		$plugin_items = $this->_backup['export_plugins'];
		
		// Get plugins for site.
		$site_plugins = get_option( 'active_plugins' );
		if ( !empty( $site_plugins ) ) {
			$plugin_items['site'] = $site_plugins;
		}
		
		//Populate $items_to_copy for all plugins to copy over
		if ( is_array( $plugin_items ) ) {
			$items_to_copy = array();
			//Get content directories by using this plugin as a base
			$content_dir = $dropin_plugins_dir = dirname( dirname( dirname( rtrim( plugin_dir_path(__FILE__), '/' ) ) ) );
			$mu_plugins_dir = $content_dir . '/mu-plugins';
			$plugins_dir = $content_dir . '/plugins';
			
			//Get the special plugins (mu, dropins, network activated)
			foreach ( $plugin_items as $type => $plugins ) {
				foreach ( $plugins as $plugin ) {
					if ( $type == 'mu' ) {
						$items_to_copy[ $plugin ] = $mu_plugins_dir . '/' . $plugin;
					} elseif ( $type == 'dropin' ) {
						$items_to_copy[ $plugin ] = $dropin_plugins_dir . '/' . $plugin;
					} elseif ( $type == 'network' || $type == 'site' ) {
						//Determine if we're a folder-based plugin, or a file-based plugin (such as hello.php)
						$plugin_path = dirname( $plugins_dir . '/' . $plugin );
						if ( basename( $plugin_path ) == 'plugins' ) {
							$plugin_path = $plugins_dir . '/' . $plugin;
						}
						$items_to_copy[ basename( $plugin_path ) ] = $plugin_path;		
					}
				} //end foreach $plugins.
			} //end foreach special plugins.
			
			
			//Copy the files over
			$wp_dir = '';
			if ( count( $items_to_copy ) > 0 ) {
				$wp_dir = $this->_backup['backup_root'];
				$wp_plugin_dir = $wp_dir . 'wp-content/plugins/';
				foreach ( $items_to_copy as $file => $original_destination ) {
					if ( file_exists( $original_destination ) && file_exists( $wp_plugin_dir ) ) {
						//$this->copy( $original_destination, $wp_plugin_dir . $file ); 
						$result = pb_backupbuddy::$filesystem->recursive_copy( $original_destination, $wp_plugin_dir . $file );
						
						if ( $result === false ) {
							pb_backupbuddy::status( 'error', 'Unable to copy plugin from `' . $original_destination . '` to `' . $wp_plugin_dir . $file . '`. Verify permissions.' );
							return false;
						} else {
							pb_backupbuddy::status( 'details', 'Copied plugin from `' . $original_destination . '` to `' . $wp_plugin_dir . $file . '`.' );
						}
					}
				}
			}
			
			// Finished
			
			pb_backupbuddy::status( 'message', 'Copied selected plugins into temporary WordPress installation.' );
			return true;

		} else {
			// Nothing has technically failed at this point - There just aren't any plugins to copy over.
			
			pb_backupbuddy::status( 'message', 'No plugins were selected for backup. Skipping plugin copying.' );
			return true;
		}
		
		return true; // Shouldnt get here.
		
	} // End ms_copy_plugins().
	
	
	
	/*	ms_copy_themes()
	 *	
	 *	Used by Multisite Exporting.
	 *	Copies over the selected themes for inclusion into the backup for creating a standalone backup from a subsite.
	 *	Authored by Ron H. Modified by Dustin B.
	 *	
	 *	@return		boolean			True on success, else false.
	 */
	public function ms_copy_themes() {
	
		
		pb_backupbuddy::status( 'message', 'Copying theme(s) into temporary WordPress installation.' );
		
		if ( !function_exists( 'wp_get_theme' ) ) {
			pb_backupbuddy::status( 'details', 'wp_get_theme() function not found. Loading `/wp-admin/includes/theme.php`.' );
			require_once( ABSPATH . 'wp-admin/includes/theme.php' );
			pb_backupbuddy::status( 'details', 'Loaded `/wp-admin/includes/theme.php`.' );
		}
		
		// Use new wp_get_theme() if available.
		if ( function_exists( 'wp_get_theme' ) ) { // WordPress v3.4 or newer.
			pb_backupbuddy::status( 'details', 'wp_get_theme() available. Using it.' );
			$current_theme = wp_get_theme();
		} else { // WordPress pre-v3.4
			pb_backupbuddy::status( 'details', 'wp_get_theme() still unavailable (pre WordPress v3.4?). Attempting to use older current_theme_info() fallback.' );
			$current_theme = current_theme_info();
		}
		
				
		//Step 5 - Copy over themes
		$template_dir = $current_theme->template_dir;
		$stylesheet_dir = $current_theme->stylesheet_dir;
		
		pb_backupbuddy::status( 'details', 'Got current theme information.' );
		
		//If $template_dir and $stylesheet_dir don't match, that means we have a child theme and need to copy over the parent also
		$items_to_copy = array();
		$items_to_copy[ basename( $template_dir ) ] = $template_dir;
		if ( $template_dir != $stylesheet_dir ) {
			$items_to_copy[ basename( $stylesheet_dir ) ] = $stylesheet_dir;
		}
		
		pb_backupbuddy::status( 'details', 'About to begin copying theme files...' );
		
		//Copy the files over
		if ( count( $items_to_copy ) > 0 ) {
			$wp_dir = $this->_backup['backup_root'];
			$wp_theme_dir = $wp_dir . 'wp-content/themes/';
			foreach ( $items_to_copy as $file => $original_destination ) {
				if ( file_exists( $original_destination ) && file_exists( $wp_theme_dir ) ) {
					
					$result = pb_backupbuddy::$filesystem->recursive_copy( $original_destination, $wp_theme_dir . $file ); 
					
					if ( $result === false ) {
						pb_backupbuddy::status( 'error', 'Unable to copy theme from `' . $original_destination . '` to `' . $wp_theme_dir . $file . '`. Verify permissions.' );
						return false;
					} else {
						pb_backupbuddy::status( 'details', 'Copied theme from `' . $original_destination . '` to `' . $wp_theme_dir . $file . '`.' );
					}
				} // end if file exists.
			} // end foreach $items_to_copy.
		} // end if.
		
		pb_backupbuddy::status( 'message', 'Copied theme into temporary WordPress installation.' );
		return true;
		
	} // End ms_copy_themes().
	
	
	
	/*	ms_copy_media()
	 *	
	 *	Used by Multisite Exporting.
	 *	Copies over media (wp-content/uploads) for this site for inclusion into the backup for creating a standalone backup from a subsite.
	 *	Authored by Ron H. Modified by Dustin B.
	 *	
	 *	@return		boolean			True on success, else false.
	 */
	public function ms_copy_media() {
		
		pb_backupbuddy::status( 'message', 'Copying media into temporary WordPress installation.' );
		
		//Step 6 - Copy over media/upload files
		$upload_dir = wp_upload_dir();
		$original_upload_base_dir = $upload_dir[ 'basedir' ];
		$destination_upload_base_dir = $this->_backup['backup_root'] . 'wp-content/uploads';
		//$result = pb_backupbuddy::$filesystem->custom_copy( $original_upload_base_dir, $destination_upload_base_dir, array( 'ignore_files' => array( $this->_backup['serial'] ) ) );
		
		// Grab directory upload contents so we can exclude backupbuddy directories.
		$upload_contents = glob( $original_upload_base_dir . '/*' );
		if ( !is_array( $upload_contents ) ) {
			$upload_contents = array();
		}
				
		foreach( $upload_contents as $upload_content ) {
			if ( strpos( $upload_content, 'backupbuddy_' ) === false ) { // Dont copy over any backupbuddy-prefixed uploads directories.
				$result = pb_backupbuddy::$filesystem->recursive_copy( $upload_content, $destination_upload_base_dir . '/' . basename( $upload_content ) );
			}
		}
		
		if ( $result === false ) {
			pb_backupbuddy::status( 'error', 'Unable to copy media from `' . $original_upload_base_dir . '` to `' . $destination_upload_base_dir . '`. Verify permissions.' );
			return false;
		} else {
			pb_backupbuddy::status( 'details', 'Copied media from `' . $original_upload_base_dir . '` to `' . $destination_upload_base_dir . '`.' );
			return true;
		}
		
	} // End ms_copy_media().
	
	
	
	/*	ms_copy_users_table()
	 *	
	 *  Step 7
	 *	Used by Multisite Exporting.
	 *	Copies over users to a temp table for this site for inclusion into the backup for creating a standalone backup from a subsite.
	 *	Authored by Ron H. Modified by Dustin B.
	 *	
	 *	@return		boolean			Currently only returns true.
	 */
	public function ms_copy_users_table() {
		
		pb_backupbuddy::status( 'message', 'Copying temporary users table for users in this blog.' );

		global $wpdb, $current_blog;
		
		$new_user_tablename = $wpdb->prefix . 'users';
		$new_usermeta_tablename = $wpdb->prefix . 'usermeta';
		
		if ( $new_user_tablename == $wpdb->users ) {
			pb_backupbuddy::status( 'message', 'Temporary users table would match existing users table. Skipping creation of this temporary users & usermeta tables.' );
			return true;
		}
		
		// Copy over users table to temporary table.
		pb_backupbuddy::status( 'message', 'Created new table `' . $new_user_tablename . '` like `' . $wpdb->users . '`.' );
		$wpdb->query( "CREATE TABLE `{$new_user_tablename}` LIKE `{$wpdb->users}`" );
		$wpdb->query( "INSERT `{$new_user_tablename}` SELECT * FROM `{$wpdb->users}" );
		
		// Copy over usermeta table to temporary table.
		pb_backupbuddy::status( 'message', 'Created new table `' . $new_usermeta_tablename . '` like `' . $wpdb->usermeta . '`.' );
		$wpdb->query( "CREATE TABLE `{$new_usermeta_tablename}` LIKE `{$wpdb->usermeta}`" );
		$wpdb->query( "INSERT `{$new_usermeta_tablename}` SELECT * FROM `{$wpdb->usermeta}" );
		
		// Get list of users associated with this site.
		$users_to_capture = array();
		$user_args = array(
			'blog_id' => $current_blog->blog_id
		);
		$users = get_users( $user_args );
		if ( $users ) {
			foreach ( $users as $user ) {
				array_push( $users_to_capture, $user->ID );
			}
		}
		$users_to_capture = implode( ',', $users_to_capture );
		pb_backupbuddy::status( 'details', 'Users to capture: ' . print_r( $users_to_capture, true ) );
		
		// Remove users from temporary table that arent associated with this site.
		$wpdb->query( "DELETE from `{$new_user_tablename}` WHERE ID NOT IN( {$users_to_capture} )" );
		$wpdb->query( "DELETE from `{$new_usermeta_tablename}` WHERE user_id NOT IN( {$users_to_capture} )" );
		

		
		pb_backupbuddy::status( 'message', 'Copied temporary users table for users in this blog.' );
		return true;
		
	} // End ms_copy_users_table().
	
	public function ms_cleanup() {
		pb_backupbuddy::status( 'details', 'Beginning Multisite-export specific cleanup.' );
		
		global $wpdb;
		$new_user_tablename = $wpdb->prefix . 'users';
		$new_usermeta_tablename = $wpdb->prefix . 'usermeta';
		
		if ( ( $new_user_tablename == $wpdb->users ) || ( $new_usermeta_tablename == $wpdb->usermeta ) ) {
			pb_backupbuddy::status( 'error', 'Unable to clean up temporary user tables as they match main tables. Skipping to prevent data loss.' );
			return;
		}
		
		pb_backupbuddy::status( 'details', 'Dropping temporary table `' . $new_user_tablename . '`.' );
		$wpdb->query( "DROP TABLE `{$new_user_tablename}`" );
		pb_backupbuddy::status( 'details', 'Dropping temporary table `' . $new_usermeta_tablename . '`.' );
		$wpdb->query( "DROP TABLE `{$new_usermeta_tablename}`" );
		
		pb_backupbuddy::status( 'details', 'Done Multisite-export specific cleanup.' );
	}
	
	/********* END MULTISITE *********/
	
	
	
	
	
	/*	_calculate_tables()
	 *	
	 *	Takes a base level to calculate tables from.  Then adds additional tables.  Then removes any exclusions. Returns array of final table listing to backup.
	 *	
	 *	@see dump().
	 *	
	 *	@param		string		$base_dump_mode			Determines which database tables to dump by default. Valid values:  all, none, prefix
	 *	@param		array		$additional_includes	Array of additional table(s) to INCLUDE in dump. Added in addition to those found by the $base_dump_mode
	 *	@param		array		$additional_excludes	Array of additional table(s) to EXCLUDE from dump. Removed from those found by the $base_dump_mode + $additional_includes.
	 *	@return		array								Array of tables to backup.
	 */
	private function _calculate_tables( $base_dump_mode, $additional_includes = array(), $additional_excludes = array() ) {
		
		global $wpdb;
		$wpdb->show_errors(); // Turn on error display.
		
		$tables = array();
		pb_backupbuddy::status( 'details', 'Calculating mysql database tables to backup.' );
		pb_backupbuddy::status( 'details', 'Base database dump mode (before inclusions/exclusions): `' . $base_dump_mode . '`.' );
		
		// Calculate base tables.
		if ( $base_dump_mode == 'all' ) { // All tables in database to start with.
			$sql = 'SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE()';
			pb_backupbuddy::status( 'startAction', 'schemaTables' );
			$results = $wpdb->get_results( $sql, ARRAY_A );
			pb_backupbuddy::status( 'finishAction', 'schemaTables' );
			if ( ( null === $results ) || ( false === $results ) ) {
				pb_backupbuddy::status( 'error', 'Error #8493894a: Unable to calculate database tables with query: `' . $sql . '`. Check database permissions or contact host.' );
			}
			foreach( (array)$results as $result ) {
				array_push( $tables, $result['table_name'] );
			}
			unset( $results );
		
		} elseif ( $base_dump_mode == 'none' ) { // None to start with.
			
			// Do nothing.
			
		} elseif ( $base_dump_mode == 'prefix' ) { // Tables matching prefix.
			
			pb_backupbuddy::status( 'details', 'Determining database tables with prefix `' . $wpdb->prefix . '`.' );
			$prefix_sql = str_replace( '_', '\_', $wpdb->prefix );
			$sql = "SELECT table_name FROM information_schema.tables WHERE table_name LIKE '{$prefix_sql}%' AND table_schema = DATABASE()";
			pb_backupbuddy::status( 'startAction', 'schemaTables' );
			$results = $wpdb->get_results( $sql, ARRAY_A );
			pb_backupbuddy::status( 'finishAction', 'schemaTables' );
			if ( ( null === $results ) || ( false === $results ) ) {
				pb_backupbuddy::status( 'error', 'Error #8493894b: Unable to calculate database tables with query: `' . $sql . '`. Check database permissions or contact host.' );
			}
			foreach( (array)$results as $result ) {
				array_push( $tables, $result['table_name'] );
			}
			unset( $results );
			
		} else { // unknown dump mode.
			
			pb_backupbuddy::status( 'error', 'Error #454545: Unknown database dump mode.' ); // Should never see this.
			
		}
		pb_backupbuddy::status( 'details', 'Base database tables based on settings (' . count( $tables ) . ' tables): `' . implode( ',', $tables ) . '`' );
		
		// Add additional tables.
		$tables = array_merge( $tables, $additional_includes );
		$tables = array_filter( $tables ); // Trim any phantom tables that the above line may have introduced.
		pb_backupbuddy::status( 'details', 'Database tables after addition (' . count( $tables ) . ' tables): `' . implode( ',', $tables ) . '`' );
		
		// Remove excluded tables.
		$tables = array_diff( $tables, $additional_excludes );
		pb_backupbuddy::status( 'details', 'Database tables after exclusion (' . count( $tables ) . ' tables): `' . implode( ',', $tables ) . '`' );
		
		return array_values( $tables ); // Clean up indexing & return.
		
	} // End _calculate_tables().
	
	
	
} // End class.
?>
