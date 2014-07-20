<?php
// Incoming vars: $backupFile, $step
if ( ! current_user_can( pb_backupbuddy::$options['role_access'] ) ) {
	die( 'Error #473623. Access Denied.' );
}



// TODO: hardcoded for dev.
$site = array(
	'url' => 'http://backupbuddy/wp-admin/admin-ajax.php?action=backupbuddy_api&k=dsnfilasbfisybfdjybfjalybsfaklsbfa',
);



require_once( pb_backupbuddy::plugin_path() . '/classes/deploy.php' );
$deploy = new backupbuddy_deploy( $site['url'] );

$localInfo = backupbuddy_api::getPreDeployInfo();
$status = $deploy->start( $localInfo );
if ( false === $status ) {
	$errors = $deploy->getErrors();
	if ( count( $errors ) > 0 ) {
		pb_backupbuddy::alert( 'Errors were encountered: ' . implode( ', ', $errors ) . ' If seeking support please click to Show Advanced Details above and provide a copy of the log.' );
	}
	return;
}



$deployData = $deploy->getState();
$deployDataJson = json_encode( $deployData );
echo '<script>console.log("deployData (len: ' . strlen( $deployDataJson ) . '):"); console.dir(' . $deployDataJson . ');</script>';





?>


<?php _e( "This will synchronize the destination site to match this site's database, media, etc. Contents of the destination site will be overwritten as needed. Verify the details below to make sure this is the correct deployment you wish to commence. You will be given the opportunity to test the changes and undo them before making them permanent. Tip: Create a Database or Full Backup before proceeding.", 'it-l10n-backupbuddy' ); ?>
<br><br>

<style>
	.tdhead {
		font-weight: bold;
	}
</style>

<table class="widefat">
	<thead>
		<tr class="thead">
			<th>&nbsp;</th><th>This Site (source)</th><th>Deploying To (destination)</th>
		</tr>
	</thead>
	<tfoot>
		<tr class="thead">
			<th>&nbsp;</th><th>This Site (source)</th><th>Deploying To (destination)</th>
		</tr>
	</tfoot>
	<tbody>
		<tr class="entry-row alternate">
			<td class="tdhead">Site URL</td>
			<td><?php echo $localInfo['siteurl']; ?> sec</td>
			<td><?php echo $deployData['remoteInfo']['siteurl']; ?> sec</td>
		</tr>
		<tr class="entry-row alternate">
			<td class="tdhead">Home URL</td>
			<td><?php echo $localInfo['homeurl']; ?> sec</td>
			<td><?php echo $deployData['remoteInfo']['homeurl']; ?> sec</td>
		</tr>
		<tr class="entry-row alternate">
			<td class="tdhead">Max Execution Time</td>
			<td><?php echo $localInfo['php']['max_execution_time']; ?> sec</td>
			<td><?php echo $deployData['remoteInfo']['php']['max_execution_time']; ?> sec</td>
		</tr>
		<tr class="entry-row alternate">
			<td class="tdhead">Max Upload File Size</td>
			<td><?php echo $localInfo['php']['upload_max_filesize']; ?> MB</td>
			<td><?php echo $deployData['remoteInfo']['php']['upload_max_filesize']; ?> MB</td>
		</tr>
		<tr class="entry-row alternate">
			<td class="tdhead">Memory Limit</td>
			<td><?php echo $localInfo['php']['memory_limit']; ?> MB</td>
			<td><?php echo $deployData['remoteInfo']['php']['memory_limit']; ?> MB</td>
		</tr>
		<tr class="entry-row alternate">
			<td class="tdhead">WordPress Version</td>
			<td><?php echo $localInfo['wordpressVersion']; ?></td>
			<td><?php echo $deployData['remoteInfo']['wordpressVersion']; ?></td>
		</tr>
		<tr class="entry-row alternate">
			<td class="tdhead">BackupBuddy Version</td>
			<td><?php echo $localInfo['backupbuddyVersion']; ?></td>
			<td><?php echo $deployData['remoteInfo']['backupbuddyVersion']; ?></td>
		</tr>
		<tr class="entry-row alternate">
			<td class="tdhead">Active Plugins</td>
			<td><?php foreach( (array)$localInfo['activePlugins'] as $localPlugin ) { echo $localPlugin['name'] . ' v' . $localPlugin['version']; } ?></td>
			<td><?php foreach( (array)$deployData['remoteInfo']['activePlugins'] as $remotePlugin ) { echo $remotePlugin['name'] . ' v' . $remotePlugin['version']; } ?> (<?php echo count( $deployData['sendPlugins'] ); ?> plugins to update)</td>
		</tr>
		<tr class="entry-row alternate">
			<td class="tdhead">Active Theme</td>
			<td><?php echo $localInfo['activeTheme']; ?></td>
			<td><?php print_r( $deployData['remoteInfo']['activeTheme'] ); ?> (<?php echo count( $deployData['sendThemeFiles'] ); ?> files to update)</td>
		</tr>
</tbody>
</table>












<br>
<h3>Database Tables to Update</h3>
<?php global $wpdb; ?>
Base tables:
<input type="radio"> This WordPress' tables (prefix <?php echo $wpdb->prefix; ?>)
<input type="radio"> <?php _e( 'All tables (including non-WordPress)', 'it-l10n-backupbuddy' ); ?>
<input type="radio"> <?php _e( 'None (use with caution)', 'it-l10n-backupbuddy' ); ?>

<script type="text/javascript">
	jQuery(document).ready(function() {
		
		/* Begin Table Selector */
		jQuery( '.pb_backupbuddy_table_addexclude' ).click(function(){
			jQuery('#pb_backupbuddy_profiles__mysqldump_additional_excludes').val( jQuery(this).parent().parent().parent().find( 'a' ).attr( 'alt' ) + "\n" + jQuery('#pb_backupbuddy_mysqldump_additional_excludes').val() );
			return false;
		});
		jQuery( '.pb_backupbuddy_table_addinclude' ).click(function(){
			jQuery('#pb_backupbuddy_profiles_mysqldump_additional_includes').val( jQuery(this).parent().parent().parent().find( 'a' ).attr( 'alt' ) + "\n" + jQuery('#pb_backupbuddy_mysqldump_additional_includes').val() );
			return false;
		});
	});
</script>
<?php
$profile_id = '';
require_once( pb_backupbuddy::plugin_path() . '/views/settings/_filetree.php' );
function pb_additional_tables( $display_size = false ) {
	
	$return = '';
	$size_string = '';
	
	global $wpdb;
	if ( true === $display_size ) {
		$results = $wpdb->get_results( "SHOW TABLE STATUS", ARRAY_A );
	} else {
		$results = $wpdb->get_results( "SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE()", ARRAY_A );
	}
	foreach( $results as $result ) {
		
		if ( true === $display_size ) {
			// Fix up row count and average row length for InnoDB engine which returns inaccurate (and changing) values for these.
			if ( 'InnoDB' === $result[ 'Engine' ] ) {
				if ( false !== ( $rowCount = $wpdb->get_var( "SELECT COUNT(1) as rowCount FROM `{$rs[ 'Name' ]}`", ARRAY_A ) ) ) {
					if ( 0 < ( $result[ 'Rows' ] = $rowCount ) ) {
						$result[ 'Avg_row_length' ] = ( $result[ 'Data_length' ] / $result[ 'Rows' ] );
					}
				}
				unset( $rowCount );
			}
			
			// Table size.
			$size_string = ' (' . pb_backupbuddy::$format->file_size( ( $result['Data_length'] + $result['Index_length'] ) ) . ') ';
			
		} // end if display size enabled.
		
		$return .= '<li class="file ext_sql collapsed">';
		$return .= '<a rel="/" alt="' . $result['table_name'] . '">' . $result['table_name'] . $size_string;
		$return .= '<div class="pb_backupbuddy_treeselect_control">';
		$return .= '<img src="' . pb_backupbuddy::plugin_url() . '/images/redminus.png" style="vertical-align: -3px;" title="Add to exclusions..." class="pb_backupbuddy_table_addexclude"> <img src="' . pb_backupbuddy::plugin_url() . '/images/greenplus.png" style="vertical-align: -3px;" title="Add to inclusions..." class="pb_backupbuddy_table_addinclude">';
		$return .= '</div>';
		$return .= '</a>';
		$return .= '</li>';
	}
	
	return '<div class="jQueryOuterTree" style="height: 160px;"><ul class="jqueryFileTree">' . $return . '</ul></div>';
	
} // end pb_additional_tables().
echo 'Hover & select <img src="' . pb_backupbuddy::plugin_url() .'/images/greenplus.png" style="vertical-align: -3px;"> to include, <img src="' . pb_backupbuddy::plugin_url() .'/images/redminus.png" style="vertical-align: -3px;"> to exclude.' . ' ' . pb_additional_tables();
?>
<br>





<h3>Database Find & Replace</h3>
The site URL (www and domain) and paths will automatically be updates. You may enter additional replacements also. Serialized data will be accounted for.<br>
Find -&gt; Replace<br>
<input type="text" value="<?php echo $localInfo['siteurl']; ?>" disabled> -&gt; <input type="text" value="<?php echo $deployData['remoteInfo']['siteurl']; ?>" disabled><br>
<input type="text" value="<?php echo $localInfo['abspath']; ?>" disabled> -&gt; <input type="text" value="<?php echo $deployData['remoteInfo']['abspath']; ?>" disabled><br>
<input type="text"> -&gt; <input type="text"> - +<br>
<br>




<br><br>
<form id="pb_backupbuddy_rollback_form" method="post" action="?action=pb_backupbuddy_rollback&step=1">
	<?php pb_backupbuddy::nonce(); ?>
	<input type="hidden" name="deployData" value="<?php echo base64_encode( serialize( $deployData ) ); ?>">
	<input type="submit" name="submitForm" class="button button-primary" value="<?php echo __('Begin Deployment') . ' &raquo;'; ?>">
	
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	
	<a class="button button-secondary" onclick="jQuery('#pb_backupbuddy_advanced').toggle();">Advanced Options</a>
	<span id="pb_backupbuddy_advanced" style="display: none; margin-left: 15px;">
		<label><input type="checkbox" name="autoAdvance" value="1" checked="checked"> Auto Advance</label>
		&nbsp;&nbsp;&nbsp;
		<label>Source chunk time limit: <input size="5" maxlength="5" type="text" name="maxExecutionTime" value="<?php echo $localInfo['php']['max_execution_time']; ?>"> sec</label>
		&nbsp;&nbsp;&nbsp;
		<label>Destination chunk time limit: <input size="5" maxlength="5" type="text" name="maxExecutionTime" value="<?php echo $deployData['remoteInfo']['php']['max_execution_time']; ?>"> sec</label>
	</span>
	
</form>


