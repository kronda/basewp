<?php
if ( ! defined( 'PB_IMPORTBUDDY' ) || ( true !== PB_IMPORTBUDDY ) ) {
	die( '<html></html>' );
}

$page_title = '';
$page_title = 'Step <span class="step_number">' . $step . '</span> of 6: <a href="" class="pb_backupbuddy_begintour">Tour This Page</a>URL & Database Settings';
require_once( '_header.php' );
echo '<div class="wrap">';
?>


<ol id="pb_backupbuddy_tour" style="display: none;">
	<li data-id="site_url">This is the URL of your WordPress site. If you are importing to a new domain or subdirectory this should reflect that.</li>
	<li data-id="new_cpanel_db_link">If you need help manually creating a database in cPanel (if applicable) click this link to open a new tab with an instructional video.</li>
	<li data-class="createdb_modal_link">Click here to create a new cPanel database (if applicable) for you by providing your cPanel login and details.</li>
	<li data-id="server_info_button">Enter your mySQL server details in the fields below. Some settings will be provided by your hosting provider.</li>
	<li data-id="advanced_options_button">Click for additional advanced configuration options useful for customizing restores or working around server problems.</li>
	<li data-id="test_db_button">Click to test your database settings before you may proceed to the next step.</li>
	<li data-id="next_step_button" data-button="Finish">Once your database settings are successfully verified you may click here to proceed to the next step.</li>
</ol>


<?php
pb_backupbuddy::flush(); // Prevents hanging page from blocking any of the step from loading.
$database_defaults = get_database_defaults();
$database_previous = get_previous_database_settings();
$default_url = get_default_url();
$custom_home_tip = 'OPTIONAL. This is also known as the site address. This is the home address
	where your main site resides. This may differ from your WordPress URL. For example: http://foo.com';
?>


<style type="text/css">
	.db_test_container {
		clear: both;
		display: none;
		background-color: #FAFAFA;
		
		border-radius: 4px;
		-moz-border-radius: 4px;
		-webkit-border-radius: 4px;
		border: 1px solid #DFDFDF;
		
		margin-right:10px;
		padding:8px;
	}
</style>

<script type="text/javascript" src="importbuddy/js/jquery.leanModal.min.js"></script>


<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#pb_backupbuddy_malwarescanloading').slideToggle();
		
		jQuery( '.db_setting' ).change( function() {
			jQuery('.pb_database_next_submit').addClass( 'button_disabled' );
		});
		
		jQuery('.pb_database_next_test').click( function() {
			jQuery('#ithemes_loading').slideDown();
			jQuery('#ithemes_loading').html( '<img src="importbuddy/images/loading.gif"> Loading ...' );
			
			if ( jQuery( '#wipe_database_option' ).is( ':checked' ) ) {
				wipe_database_option = '1';
			} else {
				wipe_database_option = '0';
			}
			
			if ( jQuery( '#wipe_database_all_option' ).is( ':checked' ) ) {
				wipe_database_all_option = '1';
			} else {
				wipe_database_all_option = '0';
			}
			
			if ( jQuery( '#skip_database_import' ).is( ':checked' ) ) {
				skip_database_import = '1';
			} else {
				skip_database_import = '0';
			}
			
			if ( jQuery( '#skip_database_migration' ).is( ':checked' ) ) {
				skip_database_migration = '1';
			} else {
				skip_database_migration = '0';
			}
			
			if ( jQuery( '#ignore_sql_errors' ).is( ':checked' ) ) {
				ignore_sql_errors = '1';
			} else {
				ignore_sql_errors = '0';
			}
			
			
			jQuery.post('importbuddy.php', {
					ajax: "mysql_test",
					pass_hash: jQuery('#pass_hash').val(),
					server: jQuery('#mysql_server').val(),
					name: jQuery('#mysql_name').val(),
					user: jQuery('#mysql_user').val(),
					pass: jQuery('#mysql_password').val(),
					wipe_database: wipe_database_option,
					wipe_database_all: wipe_database_all_option,
					skip_database_import: skip_database_import,
					skip_database_migration: skip_database_migration,
					prefix: jQuery('#mysql_prefix').val(),
					ignore_sql_errors: ignore_sql_errors,
					options: jQuery('#pb_options').val()
				 }, function(data) {
				 	data = jQuery.trim( data );
					jQuery('#ithemes_loading').html( data );
					if ( data.toLowerCase().indexOf( 'overall result success' ) >= 0 ) {
						jQuery('.pb_database_next_submit').removeClass( 'button_disabled' );
					} else {
						jQuery('.pb_database_next_submit').addClass( 'button_disabled' );
					}
			} );
			return false;
		});
		
		jQuery('.pb_database_next_submit').click( function() {
			if ( jQuery(this).hasClass( 'button_disabled' ) ) {
				alert( 'You must successfully verify database settings by clicking the button to the left after any changes to continue.' );
				return false;
			}
		});
		
		
		jQuery('.createdb_modal_link').click( function() {
			url = jQuery('#site_url').val();
			var hostname = jQuery('<a>').prop('href', url).prop('hostname');
			//alert( hostname );
			jQuery( '#cpanel_url' ).val( hostname );
			//jQuery( '.cpanel_url_full' ).html( 'http://' + hostname + ':2082/' );
			
		});
		jQuery( '#cpanel_url' ).change( function() {
			//jQuery( '.cpanel_url_full' ).html( 'http://' + jQuery(this).val() + ':2082/' );
		});
		jQuery('.leanModal').leanModal(
			{ top : 20, overlay : 0.4, closeButton: ".modal_close" }
		);
		
		jQuery( '.cpanel_user' ).change(function(){
			jQuery( '.cpanel_user_mirror' ).html( jQuery( '#cpanel_user' ).val() + '_' );
		});
		
		jQuery( '.cpanel_createdb_create' ).click( function() {
			//alert( 'do ajax here' );
			//return false;
			
			// Validate db stuff is all alphanumeric.
			if (
				( false == /^[a-zA-Z0-9]+$/.test( jQuery('#cpanel_dbname').val() ) )
				||
				( false == /^[a-zA-Z0-9]+$/.test( jQuery('#cpanel_dbuser').val() ) )
				) {
				alert( 'Database values (except password) must contain alphanumeric characters only and no spaces. Correct this and try again.' );
				return false;
			}
			
			// Validate input lengths.
			if ( jQuery('#cpanel_dbpass').val().length < 5 ) {
				alert( 'Database passwords must be 5 or more characters in length.' );
				return false;
			}
			
			jQuery( '.cpanel_createdb_loading' ).show();
			
			jQuery.post('importbuddy.php?ajax=cpanel_createdb',
				jQuery( '#cpanel_createdb_form' ).serialize(), function(data) {
					jQuery( '.cpanel_createdb_loading' ).hide();
					
					data = jQuery.trim( data );
					jQuery('#ithemes_loading').html( data );
					jQuery( '.cpanel_createdb_loading' ).hide();
					
					//alert( 'slice: ' + data.slice( -7 ) );
					if ( data.slice( 0,7 ) == 'Success' ) {
						
						jQuery( '#mysql_name' ).val( jQuery( '#cpanel_user' ).val() + '_' + jQuery('#cpanel_dbname').val() );
						jQuery( '#mysql_user' ).val( jQuery( '#cpanel_user' ).val() + '_' + jQuery('#cpanel_dbuser').val() );
						jQuery( '#mysql_password' ).val( jQuery('#cpanel_dbpass').val() );
						
						alert( data + "\n\n" + 'Your database settings will now be set.' );
						jQuery('.modal_close').trigger('click');
						
					} else {
						alert( data );
						//jQuery('.pb_database_next_submit').addClass( 'button_disabled' );
					}
					
				}
			);
			
			return false;
		});
		
	});
</script>



<form action="?step=4" method="post" class="db_setting">
	<input type="hidden" name="options" id="pb_options" value="<?php echo htmlspecialchars( serialize( pb_backupbuddy::$options ) ); ?>">
	<input type="hidden" name="pass_hash" id="pass_hash" value="<?php echo htmlspecialchars( pb_backupbuddy::_POST( 'pass_hash' ) ); ?>">
	
	<?php
	if ( 'files' == pb_backupbuddy::$options['dat_file']['backup_type'] ) {
		pb_backupbuddy::alert( 'This is a Files Only restoration, no database will be imported or migrated so the next two steps will be skipped. Additionally, your wp-config.php configuration will remain intact without modification, including database settings.' );
		echo '<br><br>';
	}
	?>
	
	<div <?php if ( 'files' == pb_backupbuddy::$options['dat_file']['backup_type'] ) { echo 'style="display: none;"'; } ?>><!-- begin wrapping url and db settings -->
		
		
		<h3>URL Settings</h3>
		<div style="margin-left: 20px;">
			
			<label>
				WordPress Address
				<?php pb_backupbuddy::tip( 'This is the address where you want the final WordPress site you are
					restoring / migrating to reside. Ex: http://foo.com/wp', '', true ); ?>
				<br>
				<span class="light">(Site URL)</span>
				<br><br>&nbsp;
			</label>
			<input type="text" name="siteurl" id="site_url" value="<?php echo $default_url; ?>" size="40" /><br>
			&nbsp;<span class="light" style="display: inline-block; width: 475px;">previously: <?php echo pb_backupbuddy::$options['dat_file']['siteurl']; ?></span>
			
			<?php if ( isset( pb_backupbuddy::$options['dat_file']['is_multisite'] ) && ( ( pb_backupbuddy::$options['dat_file']['is_multisite'] === true ) || ( pb_backupbuddy::$options['dat_file']['is_multisite'] == 'true' ) ) ) { // multisite ?>
				<br><br>Note: This URL above will also be the new Multisite Network URL.
				<br><br>
				<label>
					MultiSite Domain
					<?php pb_backupbuddy::tip( 'This is the MultiSite main domain. Ex: foo.com. WARNING: Changing this may result in URL problems. Use caution.', '', true ); ?><br>
					<br><br>&nbsp;
				</label>
				<input type="text" name="domain" value="<?php echo get_default_domain(); ?>" size="40" /><br>
				&nbsp;<span class="light" style="display: inline-block; width: 400px;">previously: <?php echo pb_backupbuddy::$options['dat_file']['domain']; ?></span>
				<br><br>
			<?php } else { ?>
			
			<label style="width: 420px; margin-left: 200px;">
				<input type="checkbox" name="custom_home" class="option_toggle" value="on" id="custom_home">
				Use optional custom site address (Home URL)?
				<?php pb_backupbuddy::tip( $custom_home_tip, '', true ); ?>
			</label>
			<br style="clear: both;">
			
			<div class="custom_home_toggle" style="display: none; width: 100%;">
				<label>
					Site Address
					<?php pb_backupbuddy::tip( $custom_home_tip, '', true ); ?>
					<br>
					<span class="light">(Home URL)</span>
					<br><br>&nbsp;
				</label>
				<input type="text" name="home" value="<?php echo $default_url; ?>" size="40" />			<br>
				&nbsp;<span class="light" style="display: inline-block; width: 475px;">previously: <?php echo pb_backupbuddy::$options['dat_file']['homeurl']; ?></span>
			</div>
			
			<?php } // end non-multisite ?>
			
		</div>
		
		<br style="clear: both;">
		<hr>
		
		<h3>Database Settings<?php
			pb_backupbuddy::tip( 'These settings control where your backed up database will be restored to.
			If you are restoring to the same server, the settings below will import the database
			to your existing WordPress database location, overwriting your existing WordPress database
			already on the server.  If you are moving to a new host you will need to create a database
			to import into. The database settings MUST be unique for each WordPress installation.  If
			you use the same settings for multiple WordPress installations then all blog content and
			settings will be shared, causing conflicts!', '', true );
		?></h3>
		<div style="margin-left: 20px;">
			
			
			<div>
				
				<table width="100%"><tr>
					<td>
						<a target="_new" id="new_cpanel_db_link" href="http://ithemes.com/tutorial-create-database-in-cpanel/">
							Use your host's control panel to create a database (if it doesn't exist yet) then enter its settings below
						</a>
					</td>
					<td style="width: 50px;" align="center">
						<b>OR</b>
					</td>
					<td align="right" style="white-space: nowrap;">
						<a href="#pb_createdb_modal" class="button leanModal createdb_modal_link" style="float: right; font-size: 13px;">Have cPanel? Click to create a database</a>
					</td>
					

				</tr></table>
				
			</div>
			<br><br>
			
			<?php if ( 'files' == pb_backupbuddy::$options['dat_file']['backup_type'] ) {
				pb_backupbuddy::alert( 'Note: This is a Files Only backup. The database import step will be skipped. You may also wish to skip migrating & updating URLs and paths within the database. This may be disabled by selecting the Advanced Options button below.' );
				echo '<br>';
			}
			?>
			
			<label>
				MySQL Server
				<?php pb_backupbuddy::tip( 'This is the address to the mySQL server where your database will be stored.
						99% of the time this is localhost.  The location of your mySQL server will be provided
						to you by your host if it differs.', '', true ); ?>
			</label>
			<input class="db_setting" type="text" name="db_server" id="mysql_server" value="<?php echo $database_defaults['server']; ?>" style="width: 175px;" />
			<?php if ( $database_previous['server'] != '' ) { echo '<span class="light">previously: ' . $database_previous['server'] . '</span>'; } ?>
			<br>
			
			<label>
				Database Name
				<?php pb_backupbuddy::tip( 'This is the name of the database you want to import your blog into. The database
					user must have permissions to be able to access this database.  If you are migrating this blog
					to a new host you will need to create this database (ie using CPanel or phpmyadmin) and create
					a mysql database user with permissions.', '', true ); ?>
			</label>
			<input class="db_setting" type="text" name="db_name" id="mysql_name" value="<?php echo $database_defaults['database']; ?>" style="width: 175px;" />
			<?php if ( $database_previous['database'] != '' ) { echo '<span class="light">previously: ' . $database_previous['database'] . '</span>'; } ?>
			<br>
			
			<label>
				Database User
				<?php pb_backupbuddy::tip( 'This is the database user account that has permission to access the database name
					in the input above.  This user must be given permission to this database for the import to work.', '', true ); ?>
			</label>
			<input class="db_setting" type="text" name="db_user" id="mysql_user" value="<?php echo $database_defaults['user']; ?>" style="width: 175px;" />
			<?php if ( $database_previous['user'] != '' ) { echo '<span class="light">previously: ' . $database_previous['user'] . '</span>'; } ?>
			<br>
			
			<label>
				Database Pass
				<?php pb_backupbuddy::tip( 'This is the password for the database user.', '', true ); ?>
			</label>
			<input class="db_setting" type="text" name="db_password" id="mysql_password" value="<?php echo $database_defaults['password']; ?>" style="width: 175px;" />
			<?php if ( $database_previous['password'] != '' ) { echo '<span class="light">previously: ' . $database_previous['password'] . '</span>'; } ?>
			<br>
			
			<label>
				Database Prefix
				<?php pb_backupbuddy::tip( 'This is the prefix given to all tables in the database.  If you are cloning the site
					on the same server AND the same database name then you will want to change this or else the imported
					database will overwrite the existing tables.', '', true ); ?>
			</label>
			<input class="db_setting" type="text" name="db_prefix" id="mysql_prefix" id="mysql_prefix" value="<?php echo $database_defaults['prefix']; ?>" style="width: 175px;" />
			<?php if ( $database_previous['prefix'] != '' ) { echo '<span class="light">previously: ' . $database_previous['prefix'] . '</span>'; } ?>
			<br>
			
		</div><!-- end wrapping url and db settings -->
		
		
		<label>&nbsp;</label>
		
		<div style="margin-top: 12px;">
			<div class="db_test_container" id="ithemes_loading">
				<img src="importbuddy/images/loading.gif">Loading ...</div>
			</div>
		
		<?php if ( ( pb_backupbuddy::$options['force_high_security'] != false ) || ( isset( pb_backupbuddy::$options['dat_file']['high_security'] ) && ( pb_backupbuddy::$options['dat_file']['high_security'] === true ) ) ) { ?>
			<label>&nbsp;</label><br>
			<h3>Create Administrator Account <?php pb_backupbuddy::tip( 'Your backup was created either with High Security Mode enabled or from a WordPress Multisite installation. For security your must provide a WordPress username and password to grant administrator privileges to.', '', true ); ?></h3>
			<label>
				New admin username
			</label>
			<input type="text" name="admin_user" id="admin_user" value="" style="width: 175px;" />
			<span class="light">(if user exists, it will be overwritten)</span>
			<br>
			<label>
				Password
			</label>
			<input type="text" name="admin_pass" id="admin_pass" value="" style="width: 175px;" />
			<br>
		<?php } // end high security. ?>
		
		
	</div>
	
	<input type="hidden" name="file" value="<?php echo htmlentities( pb_backupbuddy::$options['file'] ); ?>" />
	</div><!-- /wrap -->
	<div class="main_box_foot">
		<a href="#pb_advanced_modal" class="button button-tertiary leanModal" id="advanced_options_button" style="float: left; font-size: 13px;">Advanced Options</a>
		
		<?php if ( 'files' != pb_backupbuddy::$options['dat_file']['backup_type'] ) { ?>
		<input type="submit" name="submit" id="test_db_button" value="Test Database Settings" class="button pb_database_next_test">
		<?php } ?>
		
		&nbsp;&nbsp;&nbsp;
		<input type="submit" name="submit" id="next_step_button" value="Next Step &rarr;" class="button <?php if ( 'files' != pb_backupbuddy::$options['dat_file']['backup_type'] ) { echo 'button_disabled'; } ?> pb_database_next_submit">
	</div>




	<div id="pb_advanced_modal" style="display: none;">
		<div class="modal">
			<div class="modal_header">
				<a class="modal_close">&times;</a>
				<h2>Advanced Options</h2>
				These advanced options allow customization of various ImportBuddy functionality for custom purposes or troubleshooting.
				<b>Exercise caution</b> as some advanced options may have unforeseen effects if not used properly, such as overwriting existing files
				or erasing existing database content.
			</div>
			<div class="modal_content">
				
				
				<?php
				// TODO: Clean up this entire options system.
				
				// Set variables needed by script for this page.
				global $detected_max_execution_time;
				$detected_max_execution_time = str_ireplace( 's', '', ini_get( 'max_execution_time' ) );
				if ( is_numeric( $detected_max_execution_time ) ) {
					$detected_max_execution_time = $detected_max_execution_time;
				} else {
					$detected_max_execution_time = '30';
				}
				if ( $detected_max_execution_time == '0' ) {
					$detected_max_execution_time = '30';
				}
				
				if ( 'files' == pb_backupbuddy::$options['dat_file']['backup_type'] ) {
					echo 'Advanced database options unavailable as this is a files only backup. Import of the database, migration of paths & URLs, as well as updates to the wp-config.php database settings will be skipped.<br><br>';
					pb_backupbuddy::$options['skip_database_import'] = true;
					pb_backupbuddy::$options['skip_database_migration'] = true;
					pb_backupbuddy::$options['skip_database_migration'] = true;
				}
				?>
				
				<h4 style="margin-top: 0px;">Database Import (Step 4)</h4>
				
				<input class="db_setting" type="checkbox" value="on" name="skip_database_import" id="skip_database_import" onclick="
					if ( jQuery(this).is( ':checked' ) ) { // On checking this box, we need to hide options; unchecking show options.
						jQuery( '#database_import_options' ).slideUp();
					} else {
						jQuery( '#database_import_options' ).slideDown();
					}
					jQuery( '#database_import_options > input' ).removeAttr('checked'); // Uncheck all options within.
				"
				
				
				
				<?php if ( true == pb_backupbuddy::$options['skip_database_import'] ) { echo 'checked="checked"'; } ?>
				<?php if ( 'files' == pb_backupbuddy::$options['dat_file']['backup_type'] ) { echo ' disabled="true"'; } ?>
				> Skip import of database. <br>
				<div id="database_import_options" <?php if ( pb_backupbuddy::$options['skip_database_import'] == true ) { echo 'style="display: none;"'; } ?>>
					
					<input class="db_setting" type="checkbox" name="wipe_database" id="wipe_database_option" onclick="
						if ( jQuery(this).is( ':checked' ) ) {
							if ( !confirm( 'WARNING - Use with Caution - WARNING \n\nThis will clear any existing WordPress installation or other content in this database that matches the new site database prefix you specify. This could result in loss of posts, comments, pages, settings, and other software data loss. Verify you are using the exact database settings you want to be using. This cannot be undone. \n\nAre you sure you want to wipe all existing data matching the specified table prefix?' ) ) {
								return false;
							} else {
								jQuery( '#wipe_database_all_option' ).removeAttr('checked'); // Uncheck wipe all option they checked this they probably want that unchecked.
							}
						}
					"
					<?php if ( pb_backupbuddy::$options['wipe_database'] == true ) { echo 'checked="checked"'; } ?>
					> Delete database tables that match new prefix on import. <span class="pb_label pb_label-warning">Caution</span> <?php pb_backupbuddy::tip( 'WARNING: Checking this box will have this script clear ALL existing data from your database that match the new database prefix prior to import, possibly including non-WordPress data. This is useful if you are restoring over an existing site or repairing a failed migration. Use caution when using this option and double check the destination prefix. This cannot be undone.' ); ?><br>
					
					<input class="db_setting" type="checkbox" value="on" name="wipe_database_all" id="wipe_database_all_option" onclick="
						if ( jQuery(this).is( ':checked' ) ) {
							if ( !confirm( 'WARNING - Use with Caution - WARNING \n\nThis will clear ALL content in this database, including other WordPress installations and non-WordPress data. This could result in loss of WordPress data and any other software data stored in the defined database. Verify you are using the exact database settings you want to be using. This cannot be undone. \n\nAre you sure you want to wipe all data in this database?' ) ) {
								return false;
							} else {
								jQuery( '#wipe_database_option' ).removeAttr('checked'); // Uncheck wipe prefix option as this supercedes it.
							}
						}
					"
					<?php if ( pb_backupbuddy::$options['wipe_database_all'] == true ) { echo 'checked="checked"'; } ?>
					> Delete <b>ALL</b> database tables, erasing <b>ALL</b> database content. <span class="pb_label pb_label-warning">Caution</span> <?php pb_backupbuddy::tip( 'WARNING: Checking this box will have this script clear ALL existing data from your database, period, including non-WordPress data found. Use with extreme caution, verifying you are using the exact correct database settings. This cannot be undone.' ); ?><br>
					
					<input type="checkbox" value="on" name="mysqlbuddy_compatibility"
					<?php if ( pb_backupbuddy::$options['mysqlbuddy_compatibility'] == true ) { echo 'checked="checked"'; } ?>
					> Force database import compatibility (pre-v3.0) mode. <br>
					<input class="db_setting" type="checkbox" value="on" id="ignore_sql_errors" name="ignore_sql_errors"
					<?php if ( pb_backupbuddy::$options['ignore_sql_errors'] == true ) { echo 'checked="checked"'; } ?>
					> Ignore existing WordPress tables and import (merge tables) anyways. <?php pb_backupbuddy::tip( 'When checked ImportBuddy will allow importing database tables that have the same name as existing tables. This results in a merge of the existing data with the imported database being merged. Note that this is does NOT update existing data and only ADDS new database table rows. All other SQL conflict errors will be suppressed as well. Use this feature with caution.' ); ?><br>
					Maximum time allowed per import chunk: <input type="text" name="max_execution_time" value="<?php echo $detected_max_execution_time; ?>" size="5"> seconds. <?php pb_backupbuddy::tip( 'The maximum amount of time ImportBuddy should allow a database import chunk to run. ImportBuddy by default limits each chunk to your Maximum PHP runtime. If your database import step is timing out then lowering this value will instruct the script to limit each `chunk` to allow it to finish within this time period. Raising this value above your servers limits will not increase or override server settings.' ); ?>
				</div>
				
				<h4>Database Migration (Step 5)</h4>
				<input type="checkbox" value="on" name="skip_database_migration" id="skip_database_migration"
				<?php if ( pb_backupbuddy::$options['skip_database_migration'] == true ) { echo 'checked="checked"'; } ?>
				<?php if ( 'files' == pb_backupbuddy::$options['dat_file']['backup_type'] ) { echo ' disabled="true"'; } ?>
				onclick="
				if ( jQuery(this).is( ':checked' ) ) { // On checking this box, we need to hide options; unchecking show options.
						jQuery( '#database_migrate_options' ).slideUp();
					} else {
						jQuery( '#database_migrate_options' ).slideDown();
					}
					jQuery( '#database_migrate_options > input' ).removeAttr('checked'); // Uncheck all options within.
				"> Skip all migration of URLs/paths in database. <br>
				<div id="database_migrate_options" <?php if ( pb_backupbuddy::$options['skip_database_migration'] == true ) { echo 'style="display: none;"'; } ?>>
					<input type="checkbox" value="on" name="skip_database_bruteforce"
					<?php if ( pb_backupbuddy::$options['skip_database_bruteforce'] == true ) { echo 'checked="checked"'; } ?>
					> Skip extended brute force migration of URLS/paths in database.<?php pb_backupbuddy::tip( 'By default BackupBuddy will extensively examine and migrate unknown database tables.  Sometimes however this can cause timeouts on larger sites.  You may skip this intensive procedure to reduce required runtime for database migration steps. Note that some URLs or paths may not be updated if skipping this step.  All major WordPress URLs and paths will still be updated however.' ); ?><br>
				</div>
				<br>
				
				
			</div>
		</div>
	</div>



</form>



<div id="pb_createdb_modal" style="display: none;">
	<div class="modal">
		<div class="modal_header">
			<a class="modal_close">&times;</a>
			<h2>Automatically Create Database via cPanel</h2>
			A new database will be created along with a new database user with permissions. cPanel with the default theme required.
		</div>
		<div class="modal_content">
			
			<form id="cpanel_createdb_form">
				
				<input type="hidden" name="pass_hash" value="<?php echo htmlspecialchars( pb_backupbuddy::_POST( 'pass_hash' ) ); ?>">
				
				<label>
					cPanel URL
					<?php pb_backupbuddy::tip( '[Ex: mydomain.com] Enter the cPanel domain to complete the URL you go to to access cPanel.  For instance if your cPanel login is at http://mydomain.com:2082/ then your domain is mydomain.com.', '', true ); ?>
				</label>
				<div style="text-align: right; margin-left: 200px; margin-bottom: 3px;">
					http://<input type="text" name="cpanel_url" id="cpanel_url" style="width: 130px;">:<input type="text" name="cpanel_port" id="cpanel_port" style="width: 62px;" value="2082">/<br>
					<span class="description cpanel_url_full"></span>
				</div>
				
				<label>
					cPanel username
					<?php pb_backupbuddy::tip( '[Ex: buddy] This is the username you use to log into your cPanel.', '', true ); ?>
				</label>
				<span style="text-align: right; width: 455px; display: inline-block;">
					<input type="text" name="cpanel_user" class="cpanel_user" id="cpanel_user" style="width: 265px;">
				</span>
				<br>
				
				<label>
					cPanel password
					<?php pb_backupbuddy::tip( '[Ex: i498hDsifH487hsS] This is the password you use to log into your cPanel.', '', true ); ?>
				</label>
				<span style="text-align: right; width: 455px; display: inline-block;">
					<input type="text" name="cpanel_pass" id="cpanel_pass" style="width: 265px;" />
				</span>
				<br>
				
				<hr style="margin: 8px;">
				
				<label>
					New database name
					<?php pb_backupbuddy::tip( '[Ex: bobsblog] The database name you want to create. Note: cPanel automatically prefixes databases with the cPanel account username and an underscore. ex if your cPanel username is "buddy": buddy_bobsblog', '', true ); ?>
				</label>
				<span style="text-align: right; width: 455px; display: inline-block;">
					<span class="cpanel_user_mirror"></span><input type="text" name="cpanel_dbname" id="cpanel_dbname" style="width: 265px;" maxlength="56">
				</span>
				<br>
				
				<label>
					New database username
					<?php pb_backupbuddy::tip( '[Ex: bob] The username you want to add to grant access to this database you want to create. Note: cPanel automatically prefixes database usernames with the cPanel account username and an underscore. ex if your cPanel username is "buddy": buddy_bob', '', true ); ?>
				</label>
				<span style="text-align: right; width: 455px; display: inline-block;">
					<span class="cpanel_user_mirror"></span><input type="text" name="cpanel_dbuser" id="cpanel_dbuser" style="width: 265px;" maxlength="7">
				</span>
				<br>
				
				<label style="width: 250px;">
					New database user password
					<?php pb_backupbuddy::tip( 'The password you would like to assign to the database user created.', '', true ); ?>
				</label>
				<span style="text-align: right; width: 405px; display: inline-block;">
					<input type="text" name="cpanel_dbpass" id="cpanel_dbpass" style="width: 265px;" value="<?php echo substr(md5(microtime()),rand(0,13),16);?>">
				</span>
				<br>
				
				<br>
				<center>
					<input type="submit" name="submit" value="Create Database" class="button button-primary cpanel_createdb_create">
					<span style="display: inline-block; width: 20px;">
						<span class="cpanel_createdb_loading" style="display: none; margin-left: 10px;"><img src="<?php echo pb_backupbuddy::plugin_url(); ?>/images/loading.gif" alt="' . __('Loading...', 'it-l10n-backupbuddy' ) . '" title="' . __('Loading...', 'it-l10n-backupbuddy' ) . '" width="16" height="16" style="vertical-align: -3px;" /></span>
					</span>
				</center>
				
			</form>
		</div>
	</div>
</div>


<?php require_once( '_footer.php' ); ?>