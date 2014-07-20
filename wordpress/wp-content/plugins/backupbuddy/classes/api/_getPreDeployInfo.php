<?php
$upload_max_filesize = str_ireplace( 'M', '', @ini_get( 'upload_max_filesize' ) );
if ( ( ! is_numeric( $upload_max_filesize ) ) || ( 0 == $upload_max_filesize ) ) {
	$upload_max_filesize = 1;
}

$max_execution_time = str_ireplace( 's', '', @ini_get( 'max_execution_time' ) );
if ( ( ! is_numeric( $max_execution_time ) ) || ( 0 == $max_execution_time ) ) {
	$max_execution_time = 30;
}

$memory_limit = str_ireplace( 'M', '', @ini_get( 'memory_limit' ) );
if ( ( ! is_numeric( $memory_limit ) ) || ( 0 == $memory_limit ) ) {
	$memory_limit = 32;
}










$root = get_template_directory();

$generate_sha1 = true;
//echo 'mem:' . memory_get_usage(true) . '<br>';
$files = (array) pb_backupbuddy::$filesystem->deepglob( $root );
//echo 'mem:' . memory_get_usage(true) . '<br>';

$root_len = strlen( $root );
$hashedFiles = array();
foreach( $files as $file_id => &$file ) {
	$stat = stat( $file );
	if ( FALSE === $stat ) { pb_backupbuddy::status( 'error', 'Unable to read file `' . $file . '` stat.' ); }
	$new_file = substr( $file, $root_len );
	
	$sha1 = '';
	if ( ( true === $generate_sha1 ) && ( $stat['size'] < 1073741824 ) ) { // < 100mb
		$sha1 = sha1_file( $file );
	}
	
	$hashedFiles[$new_file] = array(
		//'scanned'	=>	time(),
		'size'		=> $stat['size'],
		'modified'	=> $stat['mtime'],
		'sha1'		=> $sha1,
	);
	unset( $files[$file_id] ); // Better to free memory or leave out for performance?
	
}
unset( $files );
//echo 'mem:' . memory_get_usage(true) . '<br>';
//echo 'filecount: ' . count( $new_files ) . '<br>';
//print_r( $new_files );





global $wp_version;
return array(
	'backupbuddyVersion'		=> pb_backupbuddy::settings( 'version' ),
	'wordpressVersion'			=> $wp_version,
	'localTime'					=> time(),
	'php'						=> array(
									'upload_max_filesize' => $upload_max_filesize,
									'max_execution_time' => $max_execution_time,
									'memory_limit' => $memory_limit,
									),
	'abspath'					=> ABSPATH,
	'siteurl'					=> site_url(),
	'homeurl'					=> home_url(),
	'activePlugins'				=> backupbuddy_api::getActivePlugins(),
	'activeTheme'				=> get_template(),
	'themeSignatures'			=> $hashedFiles,
	'notifications'				=> array(), // Array of string notification messages.
);