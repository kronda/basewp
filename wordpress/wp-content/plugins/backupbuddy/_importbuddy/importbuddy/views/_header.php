<?php
if ( ! defined( 'PB_IMPORTBUDDY' ) || ( true !== PB_IMPORTBUDDY ) ) {
	die( '<html></html>' );
}
?>
<!DOCTYPE html>
<!--[if IE 8]>
<html xmlns="http://www.w3.org/1999/xhtml" class="ie8"  dir="ltr" lang="en-US">
<![endif]-->
<!--[if !(IE 8) ]><!-->
<html xmlns="http://www.w3.org/1999/xhtml"  dir="ltr" lang="en-US">
<!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<title>ImportBuddy v<?php echo pb_backupbuddy::$options['bb_version']; ?> Restore / Migration Tool - Powered by BackupBuddy</title>
		<meta name="robots" content="noindex">
		
		<?php
		pb_backupbuddy::load_style( 'style.css' );
		
		pb_backupbuddy::load_script( 'jquery.js' );
		pb_backupbuddy::load_script( 'ui.core.js' );
		pb_backupbuddy::load_script( 'ui.widget.js' );
		pb_backupbuddy::load_script( 'ui.tabs.js' );
		pb_backupbuddy::load_script( 'tooltip.js' );
		pb_backupbuddy::load_script( 'importbuddy.js' );
		
		// Tutorial
		pb_backupbuddy::load_script( 'jquery.joyride-2.0.3.js' );
		pb_backupbuddy::load_script( 'modernizr.mq.js' );
		pb_backupbuddy::load_style( 'joyride.css' );
		?>
		
		<link rel="icon" type="image/png" href="importbuddy/images/favicon.png">
		<script type="text/javascript">
			
			jQuery(window).load(function() {
				// Tour system.
				jQuery( '.pb_backupbuddy_begintour' ).click( function() {
					jQuery("#pb_backupbuddy_tour").joyride({
						tipLocation: 'top',
					});
					return false;
				});
			});
			
			function pb_status_append( status_string ) {
				target_id = 'importbuddy_status'; // importbuddy_status or pb_backupbuddy_status
				if( jQuery( '#' + target_id ).length == 0 ) { // No status box yet so suppress.
					return;
				}
				jQuery( '#' + target_id ).append( "\n" + status_string );
				textareaelem = document.getElementById( target_id );
				textareaelem.scrollTop = textareaelem.scrollHeight;
			}
			
		</script>
	</head>
		<?php
		if ( pb_backupbuddy::$options['display_mode'] == 'normal' ) {
			echo '<body>';
			echo '<div class="topNav">';
				
				if ( true === Auth::is_authenticated() ) { // Only display these links if logged in.
					echo '<a ';
					if ( pb_backupbuddy::_GET( 'step' ) != '' ) { echo 'class="activePage" '; }
					echo 'href="?step=1">Restore / Migrate</a>';
					
					
					echo '<a ';
					if ( pb_backupbuddy::_GET( 'page' ) == 'dbreplace' ) { echo 'class="activePage" '; }
					echo 'href="?page=dbreplace">Database Text Replace</a>';
					
				}
				
				$simpleVersion = pb_backupbuddy::$options['bb_version'];
				if ( strpos( pb_backupbuddy::$options['bb_version'], ' ' ) > 0 ) {
					$simpleVersion = substr( pb_backupbuddy::$options['bb_version'], 0, strpos( pb_backupbuddy::$options['bb_version'], ' ' ) );
				}
				echo '<a href="http://ithemes.com/purchase/backupbuddy/" target="_new" title="Visit BackupBuddy Website in New Window" style="float: right;"><img src="importbuddy/images/icon_menu_32x32.png" width="16" height="16">&nbsp;&nbsp;ImportBuddy v' . $simpleVersion . ' for BackupBuddy</a>';
				echo '<br style="clear: both;">';
			echo '</div>';
			//echo '<center><img src="importbuddy/images/bb-logo.png" title="BackupBuddy Restoration & Migration Tool" style="margin-top: 10px;"></center><br>';
		} else { // Magic migration mode inside WordPress (in an iframe).
			echo '<body onLoad="window.parent.scroll(0,0);">'; // Auto scroll to top of parent while in iframe.
		}
		
		//<a href="http://ithemes.com/codex/page/BackupBuddy" style="text-decoration: none;">Need help? See the <b>Knowledge Base</b> for tutorials & more.</a><br>
		?>
		
		<center>
		<?php
		if ( isset( $step ) && ( $step > 1 ) ) { // Only show advanced option settings after Step 1 to hide on logging screen.
			if ( pb_backupbuddy::$options['skip_files'] != false ) {
				echo '<img src="' . pb_backupbuddy::plugin_url() . '/images/bullet_error.png" style="vertical-align: -3px;">';
				echo 'Advanced Option to skip files is set to true. Files will not be extracted.<br>';
			}
			if ( pb_backupbuddy::$options['wipe_database'] != false ) {
				echo '<img src="' . pb_backupbuddy::plugin_url() . '/images/bullet_error.png" style="vertical-align: -3px;">';
				echo 'Advanced Option to delete database tables with same prefix enabled. Used caution.<br>';
			}
			if ( pb_backupbuddy::$options['wipe_database_all'] != false ) {
				echo '<img src="' . pb_backupbuddy::plugin_url() . '/images/bullet_error.png" style="vertical-align: -3px;">';
				echo 'Advanced Option to delete ALL database tables & content enabled. Use caution.<br>';
			}
			if ( pb_backupbuddy::$options['skip_database_import'] != false ) {
				echo '<img src="' . pb_backupbuddy::plugin_url() . '/images/bullet_error.png" style="vertical-align: -3px;">';
				echo 'Advanced Option to skip database import enabled.<br>';
			}
			if ( pb_backupbuddy::$options['skip_database_migration'] != false ) {
				echo '<img src="' . pb_backupbuddy::plugin_url() . '/images/bullet_error.png" style="vertical-align: -3px;">';
				echo 'Advanced Option to skip database migration (import only) enabled.<br>';
			}
			if ( pb_backupbuddy::$options['skip_database_bruteforce'] != false ) {
				echo '<img src="' . pb_backupbuddy::plugin_url() . '/images/bullet_error.png" style="vertical-align: -3px;">';
				echo 'Advanced Option to skip database brute force migration (basic migration only) enabled..<br>';
			}
			if ( pb_backupbuddy::$options['mysqlbuddy_compatibility'] != false ) {
				echo '<img src="' . pb_backupbuddy::plugin_url() . '/images/bullet_error.png" style="vertical-align: -3px;">';
				echo 'Advanced Option to import database in compatibility mode (pre-v3.0) set to true. This may be slower.<br>';
			}
			if ( pb_backupbuddy::$options['skip_htaccess'] != false ) {
				echo '<img src="' . pb_backupbuddy::plugin_url() . '/images/bullet_error.png" style="vertical-align: -3px;">';
				echo 'Advanced Option to skip migrating .htaccess file is enabled.<br>';
			}
			if ( pb_backupbuddy::$options['force_compatibility_medium'] != false ) {
				echo '<img src="' . pb_backupbuddy::plugin_url() . '/images/bullet_error.png" style="vertical-align: -3px;">';
				echo 'Advanced Option to force medium compatibility mode.<br>';
			}
			if ( pb_backupbuddy::$options['force_compatibility_slow'] != false ) {
				echo '<img src="' . pb_backupbuddy::plugin_url() . '/images/bullet_error.png" style="vertical-align: -3px;">';
				echo 'Debug option to force slow compatibility mode. This may result in slower, less reliable operation.<br>';
			}
			if ( pb_backupbuddy::$options['force_high_security'] != false ) {
				echo '<img src="' . pb_backupbuddy::plugin_url() . '/images/bullet_error.png" style="vertical-align: -3px;">';
				echo 'Debug option to force high security mode. You may be prompted for more information than normal.<br>';
			}
			if ( pb_backupbuddy::$options['show_php_warnings'] != false ) {
				echo '<img src="' . pb_backupbuddy::plugin_url() . '/images/bullet_error.png" style="vertical-align: -3px;">';
				echo 'Debug option to strictly report all errors & warnings from PHP is set to true. This may cause operation problems.<br>';
			}
			if ( pb_backupbuddy::$options['ignore_sql_errors'] != false ) {
				echo '<img src="' . pb_backupbuddy::plugin_url() . '/images/bullet_error.png" style="vertical-align: -3px;">';
				echo 'Debug option to ignore existing database table and other SQL errors enabled. Data may be appending to existing tables. Use with caution.<br>';
			}
		} // end on step 1.
		echo '</center>';
		?>
		
		<div style="display: none;" id="pb_importbuddy_blankalert"><?php pb_backupbuddy::alert( '#TITLE# #MESSAGE#', true, '9021' ); ?></div>
		
		<div style="max-width: 800px; margin-left: auto; margin-right: auto;">
			<div class="main_box">
				<div class="main_box_head">
					<?php echo $page_title; ?>
				</div>
