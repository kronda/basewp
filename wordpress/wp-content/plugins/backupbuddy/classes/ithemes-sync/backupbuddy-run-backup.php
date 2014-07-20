<?php
class Ithemes_Sync_Verb_Backupbuddy_Run_Backup extends Ithemes_Sync_Verb {
	public static $name = 'backupbuddy-run-backup';
	public static $description = 'Run a backup profile.';
	
	private $default_arguments = array(
		'profile'	=> '', // Valid values: db, full, [numeric profile ID]
	);
	
	/*
	 * Return:
	 *		array(
	 *			'success'	=>	'0' | '1'
	 *			'status'	=>	'Status message.'
	 *		)
	 *
	 */
	public function run( $arguments ) {
		$arguments = Ithemes_Sync_Functions::merge_defaults( $arguments, $this->default_arguments );
		
		$profile = $arguments['profile'];
		if ( 'db' == $profile ) { // db profile is always index 1.
			$profile = '1';
		} elseif ( 'full' == $profile ) { // full profile is always index 2.
			$profile = '2';
		}
		
		if ( is_numeric( $profile ) ) {
			if ( isset( pb_backupbuddy::$options['profiles'][ $profile ] ) ) {
				$profileArray = pb_backupbuddy::$options['profiles'][ $profile ];
			} else {
				return array(
					'api' => '0',
					'status' => 'error',
					'message' => 'Error #2332904: Invalid profile ID `' . htmlentities( $profile ) . '`. Profile with this number was not found. Try deactivating then reactivating the plugin. If this fails please reset the plugin Settings back to Defaults from the Settings page.',
				);
			}
		} else {
			return array(
				'api' => '0',
				'status' => 'error',
				'message' => 'Error #85489548955. Invalid profile ID not numeric: `' . htmlentities( $profile ) . '`.',
			);
		}
		
		require_once( pb_backupbuddy::plugin_path() . '/classes/backup.php' );
		pb_backupbuddy::$classes['backup'] = new pb_backupbuddy_backup();
		// Set serial ahead of time so can be used by AJAX before backup procedure actually begins.
		$serial = pb_backupbuddy::random_string( 10 );
		
		$profileArray['backup_mode'] = '2'; // Force modern mode when running under sync.
		
		// Run the backup!
		if ( pb_backupbuddy::$classes['backup']->start_backup_process(
				$profileArray,											// Profile array.
				'manual',												// Backup trigger. manual, scheduled
				array(),												// pre-backup array of steps.
				array(),												// post-backup array of steps.
				'it-sync',												// friendly title of schedule that ran this (if applicable).
				$serial,												// if passed then this serial is used for the backup insteasd of generating one.
				array()													// Multisite export only: array of plugins to export.
			) !== true ) {
			return array(
				'api' => '0',
				'status' => 'error',
				'message' => 'Error #435832: Backup failed. See BackupBuddy log for details.',
			);
		}
		
		return array(
			'api' => '0',
			'status' => 'ok',
			'message' => 'Backup initiated successfully.',
			'serial' => $serial,
		);
		
	} // End run().
	
	
} // End class.
