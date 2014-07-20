<?php // Settings to display in a form for a user to configure.

$default_name = NULL;
if ( 'add' == $mode ) {
	$default_name = 'My Local';
}
$settings_form->add_setting( array(
	'type'		=>		'text',
	'name'		=>		'title',
	'title'		=>		__( 'Destination name', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( 'Name of the new destination to create. This is for your convenience only.', 'it-l10n-backupbuddy' ),
	'rules'		=>		'required|string[1-45]',
	'default'	=>		$default_name,
) );



if ( pb_backupbuddy::_GET('add') != '' ) { // set default only when adding.
	$default_path = ABSPATH;
} else {
	$default_path = '';
}
$settings_form->add_setting( array(
	'type'		=>		'text',
	'name'		=>		'path',
	'title'		=>		__( 'Local file path', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( 'Provide the full path to the location\'s directory. This must map to the web location for the destination URL.', 'it-l10n-backupbuddy' ),
	'default'	=>		$default_path,
	'css'		=>		'width: 100%;',
	'rules'		=>		'required|string[1-500]',
) );


if ( pb_backupbuddy::_GET('add') != '' ) { // set default only when adding.
	$default_url = rtrim( site_url(), '/\\' ) . '/';
} else {
	$default_url = '';
}
$settings_form->add_setting( array(
	'type'		=>		'text',
	'name'		=>		'url',
	'title'		=>		__( 'Migration URL', 'it-l10n-backupbuddy' ) . '<br><span class="description">Optional, for migrations</span>',
	'tip'		=>		__( 'Enter the URL corresponding to the local destination path. This URL must lead to the location where files uploaded to this remote destination would end up. If the destination is in a subdirectory make sure to match it in the corresponding URL.', 'it-l10n-backupbuddy' ),
	'css'		=>		'width: 100%;',
	'default'	=>		$default_url,
	'classes'	=>		'migration_url',
	'rules'		=>		'string[0-500]',
) );

$settings_form->add_setting( array(
	'type'		=>		'text',
	'name'		=>		'archive_limit',
	'title'		=>		__( 'Archive limit', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( '[Example: 5] - Enter 0 for no limit. This is the maximum number of archives to be stored in this specific destination. If this limit is met the oldest backups will be deleted.', 'it-l10n-backupbuddy' ),
	'rules'		=>		'required|int[0-9999999]',
	'css'		=>		'width: 50px;',
	'after'		=>		' backups',
) );
