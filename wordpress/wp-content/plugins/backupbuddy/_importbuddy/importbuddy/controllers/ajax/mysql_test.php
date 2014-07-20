<?php
if ( ! defined( 'PB_IMPORTBUDDY' ) || ( true !== PB_IMPORTBUDDY ) ) {
	die( '<html></html>' );
}


Auth::require_authentication(); // Die if not logged in.


// Tests variables to populate with results.
$tests = array(
	'connect'			=> false,	// Able to connect & login to db server?
	'connect_error'		=> '',		// mysql error message in response to connect & login (if any).
	'selectdb'			=> false,	// Able to select the database?
	'selectdb_error'	=> '',		// mysql error message in response to selecting (if any).
	'createtable'		=> false,	// ability to CREATE a new table (and delete it).
	'createtable_error'	=> '',		// create table mysql error (if any).
	'wordpress_exists'	=> false,	// WordPress tables matching prefix found?
);




/***** BEGIN TESTS *****/

if ( false === @mysql_connect( pb_backupbuddy::_POST( 'server' ), pb_backupbuddy::_POST( 'user' ), pb_backupbuddy::_POST( 'pass' ) ) ) { // CONNECT failed.
	
	$tests['connect_error'] = mysql_error();
	
} else { // CONNECT success.
	
	$tests['connect'] = true;
	
	if ( false === @mysql_select_db( pb_backupbuddy::_POST( 'name' ) ) ) { // SELECT failed.
		
		$tests['selectdb_error'] = mysql_error();
		
	} else { // SELECT success.
		
		$tests['selectdb'] = true;
		
		// Test ability to create (and delete) a table to verify permissions.
		@mysql_query("DROP TABLE `" . mysql_real_escape_string( pb_backupbuddy::_POST( 'prefix' ) ) . "buddy_test`"); // drop just in case a prior attempt failed.
		$result = mysql_query( "CREATE TABLE `" . mysql_real_escape_string( pb_backupbuddy::_POST( 'prefix' ) ) . "buddy_test` (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY)" );
		if ( false !== $result ) { // create success.
			// Drop temp test table we created before we declare success.
			$result = mysql_query("DROP TABLE `" . mysql_real_escape_string( pb_backupbuddy::_POST( 'prefix' ) ) . "buddy_test`");
			if ( false !== $result ) { // drop success.
				$tests['createtable'] = true;
			} else { // drop fail.
				$tests['createtable_error'] = 'Unable to delete temporary table. ' . mysql_error();
			}
		} else { // create fail.
			$tests['createtable_error'] = 'Unable to create temporary table. ' . mysql_error();
		}
		
		// WordPress tables exist matching prefix?
		$result = mysql_query( "SHOW TABLES LIKE '" . mysql_real_escape_string( str_replace( '_', '\_', pb_backupbuddy::_POST( 'prefix' ) ) . "%" ) . "'" );
		if ( mysql_num_rows( $result ) > 0 ) { // WordPress EXISTS already. Collision.
			$tests['wordpress_exists'] = true;
		}
		unset( $result );
		
	} // end select success.
	
} // end connect success.

/***** END TESTS *****/

function test_pass() {
	echo '<span class="pb_label pb_label-success">Pass</span>';
}

function test_fail() {
	echo '<span class="pb_label pb_label-important">Fail</span>';
}

function test_warn() {
	echo '<span class="pb_label pb_label-warning">Warning</span>';
}

function fatal_test_die() {
	echo '<div style="padding: 10px;"><b>Fatal errors encountered during testing. Please resolve them to continue tests.</b></div>';
	if ( pb_backupbuddy::_POST( 'skip_database_import' ) == '1' ) {
		echo '<br><span class="pb_label pb_label-info">Important</span> Database import set to be skipped based on advanced options. Any failures ignored.<br><br>';
		echo '<!-- Success. -->';
	}
	die();
}
?>

<style type="text/css">
	
	.db_head {
		font-weight: bold;
		margin-bottom: 4px;
	}
	
	
	.test {
		border-bottom: 1px solid #DFDFDF;
		line-height: 1.3em;
	}
	.test_title {
		padding-top: 5px;
		padding-bottom: 5px;
	}
	.test_status {
		float: right;
	}
	.test_error {
		padding-left: 16px;
		padding-right: 70px;
	}
	
</style>

<div class="db_head">Testing database settings...</div>

<?php
$fatal_error = false;
?>



<div class="test">
	<div class="test_status">
		<?php
		if ( true === $tests['connect'] ) {
			test_pass();
		} else {
			test_fail();
		}
		?>
	</div>
	<div class="test_title">1. Connect & login to database server</div>
	<?php if ( false === $tests['connect'] ) {
		echo '<div class="test_error description">Error: ' . $tests['connect_error'] . '</div>';
		$fatal_error = true;
	} ?>
</div>
<?php if ( true === $fatal_error ) { fatal_test_die(); } ?>



<div class="test">
	<div class="test_status">
		<?php
		if ( true === $tests['selectdb'] ) {
			test_pass();
		} else {
			test_fail();
		}
		?>
	</div>
	<div class="test_title">2. Selecting database to verify access</div>
	<?php if ( false === $tests['selectdb'] ) {
		echo '<div class="test_error description">Error: ' . $tests['selectdb_error'] . '</div>';
		$fatal_error = true;
	} ?>
</div>
<?php if ( true === $fatal_error ) { fatal_test_die(); } ?>



<div class="test">
	<div class="test_status">
		<?php
		if ( true === $tests['createtable'] ) {
			test_pass();
		} else {
			test_fail();
		}
		?>
	</div>
	<div class="test_title">3. Creating & dropping table to verify permission</div>
	<?php if ( false === $tests['createtable'] ) {
		echo '<div class="test_error description">Error: ' . $tests['createtable_error'] . '</div>';
		$fatal_error = true;
	} ?>
</div>



<div class="test">
	<div class="test_status">
		<?php
		$warn_message = '';
		if ( false === $tests['wordpress_exists'] ) { // No existing WordPress in database with this prefix.
			test_pass();
		} else { // WordPress exists in database with same prefix.
			if ( pb_backupbuddy::_POST( 'wipe_database' ) == '1' ) {
				test_warn();
			} elseif ( pb_backupbuddy::_POST( 'wipe_database_all' ) == '1' ) {
				test_warn();
			} elseif ( pb_backupbuddy::_POST( 'skip_database_import' ) == '1' ) {
				test_warn();
			} elseif ( pb_backupbuddy::_POST( 'ignore_sql_errors' ) == '1' ) {
				test_warn();
			} else { // No wiping and NOT skipping import so this fails due to collision..
				test_fail();
				$fatal_error = true;
			}
			$warn_message .= 'A WordPress installation appears to already exist with the same prefix.<br>Select the <b>Advanced Options</b> button below to delete existing database content.';
		}
		// Notify about any wiping going on.
		if ( pb_backupbuddy::_POST( 'wipe_database' ) == '1' ) { // Option to wipe JUST MATCHING THIS PREFIX enabled.
			$warn_message .= 'Based on your settings all tables matching the specified database prefix will be erased before proceeding. Use caution. ';
		}
		if ( pb_backupbuddy::_POST( 'wipe_database_all' ) == '1' ) { // Option to wipe ALL TABLES enabled.
			$warn_message .= 'Based on your settings ALL tables in the specified database will be erased before proceeding. Use extreme caution. ';
		}
		?>
	</div>
	<div class="test_title">4. Verifying no existing WordPress tables matching prefix</div>
	<?php if ( '' != $warn_message ) {
		echo '<div class="test_error description">Warning: ' . $warn_message . '</div>';
	} ?>
</div>



<div class="test">
	<div class="test_status">
		<?php
		$prefix_pass = false;
		$prefix_warning = false;
		
		if ( preg_match('|[^a-z0-9_]|i', pb_backupbuddy::_POST( 'prefix' ) ) ) { // WordPress' regex match which is quite loose.
			test_fail();
		} else {
			if ( preg_match('/^[a-z0-9]+_$/i', pb_backupbuddy::_POST( 'prefix' ) ) ) { // Suggested format XX_
				test_pass();
				$prefix_pass = true;
			} else {
				test_warn();
				$prefix_pass = true;
				$prefix_warning = true;
			}
		}
		?>
	</div>
	<div class="test_title">5. Verifying specified prefix is in valid format</div>
	<?php
	if ( false === $prefix_pass ) {
		echo '<div class="test_error description">Error: Prefix contains characters that are not allowed. Prefixes should be in the format of letters or numbers and underscore. Ex: wp_, wp5_, mysite_, etc.</div>';
		$fatal_error = true;
	}
	if ( true === $prefix_warning ) {
		echo '<div class="test_error description">Warning: A prefix in the format of alphanumeric characters followed by an underscore is <b>highly recommended</b> to conform to WordPress & BackupBuddy conventions & expectations. Ex: wp_, wp5_, mysite_, etc.</div>';
	}
	?>
</div>


<?php
if ( ( pb_backupbuddy::_POST( 'skip_database_import' ) == '1' ) && ( pb_backupbuddy::_POST( 'skip_database_migration' ) == '1' ) ) {
	$fatal_error = false;
	echo '<br><span class="pb_label pb_label-info">Important</span> Database import & migration set to be skipped based on advanced options. Any failures ignored.<br><br>';
}
?>


<div class="test" style="border-bottom: 0;">
	<div class="test_status">
		<?php
		if ( false === $fatal_error ) {
			test_pass();
		} else {
			test_fail();
		}
		?>
	</div>
	<?php if ( false !== $fatal_error ) {
		echo '<div class="test_title"><b>Overall result failure. Correct errors and re-test.</b></div>';
	} else {
		echo '<div class="test_title"><b>Overall result success. Proceed to next step.</b></div>';
	} ?>
</div>