<?php
// $mode is defined prior to this file load as either destination or migration.

if ( $mode == 'migration' ) {
	$picker_url = pb_backupbuddy::ajax_url( 'migration_picker' );
} else {
	$picker_url = pb_backupbuddy::ajax_url( 'destination_picker' );
}
//$picker_url .= '&sending=' . pb_backupbuddy::_GET( 'sending' );

if ( pb_backupbuddy::_GET( 'action_verb' ) != '' ) {
	$action_verb = ' ' . htmlentities( pb_backupbuddy::_GET( 'action_verb' ) );
} else { // default
	$action_verb = '';
}

pb_backupbuddy::load_style( 'admin' );
pb_backupbuddy::load_style( 'destination_picker.css' );
pb_backupbuddy::load_script( 'jquery' );
pb_backupbuddy::load_script( 'jquery-ui-core' );
pb_backupbuddy::load_script( 'jquery-ui-widget' );

// Load accordion JS. Pre WP v3.3 we need to load our own JS file. Was: pb_backupbuddy::load_script( 'jquery-ui-accordion' );
global $wp_version;
pb_backupbuddy::load_script( version_compare( $wp_version, '3.3', '<' ) ? 'jquery.ui.accordion.min.js' : 'jquery-ui-accordion' );

// Destinations may hide the add and test buttons by altering these variables.
global $pb_hide_save;
global $pb_hide_test;
$pb_hide_save = false;
$pb_hide_test = false;

// Load destinations class.
require_once( pb_backupbuddy::plugin_path() . '/destinations/bootstrap.php' );

pb_backupbuddy::load_script( 'filetree.js' );
pb_backupbuddy::load_style( 'filetree.css' );
?>


<script>
	jQuery(document).ready(function() {
		
		// Open settings for destination.
		jQuery( '.dest_select_open' ).click( function() {
			//jQuery('.settings').stop(true, true).slideUp(200); // Limits to only one open at a time.
			jQuery(this).next('.settings').stop(true, true).slideToggle(200);
		} );
		
		
		
		// Select a destionation to return to parent page.
		jQuery('.dest_select_select').click(function(e) {
		
			<?php
			if ( $mode == 'migration' ) {
				?>
				destination_url = jQuery(this).nextAll('.settings').find('.migration_url').val();
				if ( destination_url == '' ) {
					alert( 'Please enter a destination URL in the settings for the destination, test it, then save before selecting this destination.' );
					jQuery(this).nextAll('.settings').find('.migration_url').css( 'background', '#ffffe0' );
					jQuery(this).nextAll('.settings').first().stop(true, true).slideDown(200);
					return false;
				}
				<?php
			}
			?>
			
			dest_id = jQuery(this).parent('.bb-dest-option').attr( 'rel' );
			<?php
			if ( pb_backupbuddy::_GET( 'quickstart' ) != '' ) {
				?>
				var win = window.dialogArguments || opener || parent || top;
				win.pb_backupbuddy_quickstart_destinationselected( dest_id );
				win.tb_remove();
				return false;
				<?php
			}
			?>
			
			if ( jQuery( '#pb_backupbuddy_remote_delete' ).is( ':checked' ) ) {
				delete_after = true;
			} else {
				delete_after = false;
			}
			
			var win = window.dialogArguments || opener || parent || top;
			win.pb_backupbuddy_selectdestination( dest_id, jQuery(this).children('.bb-dest-name').html(), '<?php if ( !empty( $_GET['callback_data'] ) ) { echo $_GET['callback_data']; } ?>', delete_after );
			win.tb_remove();
			return false;
		});
		
		
		// Existing destination accordion.
		jQuery( '#pb_backupbuddy_destpicker' ).accordion( { header: 'h3', active: false, collapsible: true, autoHeight: false } );
		
		
		// Config button in existing destination accordion.
		jQuery( '.pb_backupbuddy_destpicker_config' ).click( function() {
			jQuery( '#pb_backupbuddy_destpicker' ).accordion( 'activate', parseInt( jQuery(this).attr( 'rel' ) ) );
		} );
		
		
		// Click to display create new destinations.
		jQuery( '#pb_backupbuddy_destpicker_slidecreate' ).click( function() {
			jQuery( '#pb_backupbuddy_destpicker_slidecreatebox' ).slideToggle();
			return false;
		} );
		
		
		// Test a remote destination.
		jQuery( '.pb_backupbuddy_destpicker_test' ).click( function() {
			
			jQuery(this).children( '.pb_backupbuddy_destpicker_testload' ).show();
			jQuery.post( '<?php echo pb_backupbuddy::ajax_url( 'remote_test' ); ?>', jQuery(this).parent( 'form' ).serialize(), 
				function(data) {
					jQuery( '.pb_backupbuddy_destpicker_testload' ).hide();
					data = jQuery.trim( data );
					alert( data );
				}
			);
			
			return false;
		} );
		
		
		// Save a remote destination settings.
		jQuery( '.pb_backupbuddy_destpicker_save' ).click( function() {
			var pb_remote_id = jQuery(this).parents( '.bb-dest-option' ).attr( 'rel' );
			//var pb_accordion_id = jQuery(this).parents( '.pb_backupbuddy_destpicker_id' ).attr( 'alt' );
			var new_title = jQuery(this).parent( 'form' ).find( '#pb_backupbuddy_title' ).val();
			
			jQuery(this).next( '.pb_backupbuddy_destpicker_saveload' ).show();
			jQuery.post( '<?php echo pb_backupbuddy::ajax_url( 'remote_save' ); ?>&pb_backupbuddy_destinationid=' + pb_remote_id, jQuery(this).parent( 'form' ).serialize(), 
				function(data) {
					data = jQuery.trim( data );
					
					if ( data == 'Destination Added.' ) {
						<?php
						if ( pb_backupbuddy::_GET( 'quickstart' ) != '' ) {
						?>
						var win = window.dialogArguments || opener || parent || top;
						win.pb_backupbuddy_quickstart_destinationselected();
						win.tb_remove();
						return false;
						<?php
						}
						?>
						//alert( data + "\n\nNow returning to destination list..." );
						window.location.href = '<?php echo $picker_url . '&callback_data=' . pb_backupbuddy::_GET( 'callback_data' ); ?>&sending=<?php echo pb_backupbuddy::_GET( 'sending' ); ?>&alert_notice=' + encodeURIComponent( 'New destination titled "' + jQuery( '#pb_backupbuddy_title' ).val() + '" successfully added.' );
					} else if ( data == 'Settings saved.' ) {
						jQuery( '.pb_backupbuddy_destpicker_saveload' ).hide();
						
						jQuery( '#pb_backupbuddy_destpicker_title_' + pb_remote_id ).html( '<img src="<?php echo pb_backupbuddy::plugin_url(); ?>/images/updated.png" title="Settings recently updated."> ' + new_title );
						//alert( data );
						
						// Collapse accordion
						//jQuery( '#pb_backupbuddy_destpicker' ).accordion( 'activate', parseInt( pb_accordion_id ) );
						jQuery( '.settings' ).slideUp(200);
					} else {
						jQuery( '.pb_backupbuddy_destpicker_saveload' ).hide();
						alert( "Error: \n\n" + data );
					}
					
				}
			);
			
			return false;
		} );
		
		
		// Delete a remote destination settings.
		jQuery( '.pb_backupbuddy_destpicker_delete' ).click( function() {
			
			if ( !confirm( 'Are you sure you want to delete this destination?' ) ) {
				return false;
			}
			
			//var pb_remote_id = jQuery(this).parents( '.pb_backupbuddy_destpicker_id' ).attr( 'rel' );
			var pb_remote_id = jQuery(this).parents( '.bb-dest-option' ).attr( 'rel' );
			//alert( 'id: ' + pb_remote_id );
			
			//var pb_accordion_id = jQuery(this).parents( '.pb_backupbuddy_destpicker_id' ).attr( 'alt' );
			var new_title = jQuery(this).parent( 'form' ).find( '#pb_backupbuddy_title' ).val();
			
			jQuery(this).children( '.pb_backupbuddy_destpicker_deleteload' ).show();
			jQuery.post( '<?php echo pb_backupbuddy::ajax_url( 'remote_delete' ); ?>&pb_backupbuddy_destinationid=' + pb_remote_id, jQuery(this).parent( 'form' ).serialize(), 
				function(data) {
					jQuery( '.pb_backupbuddy_destpicker_deleteload' ).slideUp();
					data = jQuery.trim( data );
					
					if ( data == 'Destination deleted.' ) {
						
						jQuery( '#pb_backupbuddy_destpicker_title_' + pb_remote_id ).html( 'Deleted: ' + new_title );
						
						// Slide up and hide the deleted destinations.
						jQuery( '#pb_backupbuddy_destpicker_dest_' + pb_remote_id ).slideUp(200);
						
						// Count number remaining. If last item is deleted then we need to hide the destination table to avoid a bar in the UI.
						var num_visible_destinations = jQuery('.pb_backupbuddy_destination_list > .bb-dest-option:visible').length;
						//alert( 'visible: ' + num_visible_destinations );
						if ( num_visible_destinations <= 1 ) { // Last one still reports 1 as the animation is not done yet from sliding.
							jQuery('.pb_backupbuddy_destination_list').slideUp(200);
							jQuery('.pb_backupbuddy_selectexistingtext').slideUp(200);
						}
						
					} else { // Show message if not success.
						alert( data );
					}
					
				}
			);
			
			return false;
		} );
		
		
		// Select a destionation to return to parent page.
		jQuery('.pb_backupbuddy_destpicker_select').click(function(e) {
			var win = window.dialogArguments || opener || parent || top;
			win.pb_backupbuddy_selectdestination( jQuery(this).attr( 'rel' ), jQuery(this).attr( 'alt' ), '<?php if ( !empty( $_GET['callback_data'] ) ) { echo $_GET['callback_data']; } ?>' );
			win.tb_remove();
			return false;
		});
		
		
		jQuery( '.dest_select_select' ).hover(
			function(){
				jQuery(this).find( '.bb-dest-view-files' ).show();
			},
			function(){
				jQuery(this).find( '.bb-dest-view-files' ).hide();
			}
		);
		
	});
</script>

<style type="text/css">
	.bb-dest-option .settings:before {
		background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/dest_arrow.jpg') top right no-repeat;
		display: block;
		content: '';
		height: 9px;
		width: 17px;
		margin: 0 0 0 94.5%; //556px;
	}
	
	
	
	
	
	
	
	
	

	#pb_backupbuddy_destpicker {
		margin: 10px;
		-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;
		border:1px solid #C9C9C9;
		background-color:#EEEEEE;
		padding: 6px;
	}
	.pb_backupbuddy_destpicker_rowtable {
		width: 100%;
		border-collapse: collapse;
		border-top: 1px solid #C9C9C9;
	}
	.pb_backupbuddy_destpicker_rowtable tr:hover {
		//background: #E8E8E8;
		cursor: pointer;
		
		background: #dbdbdb; /* Old browsers */
background: -moz-radial-gradient(center, ellipse cover, #dbdbdb 0%, #eeeeee 79%); /* FF3.6+ */
background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%,#dbdbdb), color-stop(79%,#eeeeee)); /* Chrome,Safari4+ */
background: -webkit-radial-gradient(center, ellipse cover, #dbdbdb 0%,#eeeeee 79%); /* Chrome10+,Safari5.1+ */
background: -o-radial-gradient(center, ellipse cover, #dbdbdb 0%,#eeeeee 79%); /* Opera 12+ */
background: -ms-radial-gradient(center, ellipse cover, #dbdbdb 0%,#eeeeee 79%); /* IE10+ */
background: radial-gradient(ellipse at center, #dbdbdb 0%,#eeeeee 79%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#dbdbdb', endColorstr='#eeeeee',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */



	}
	.pb_backupbuddy_destpicker_rowtable td {
		padding: 8px;
		padding-top: 12px;
		padding-bottom: 12px;
	}
	#pb_backupbuddy_destpicker h3:focus {
		outline: 0;
	}
	.pb_backupbuddy_destpicker_type {
		width: 80px;
	}
	.pb_backupbuddy_destpicker_config {
		width: 40px;
		text-align: right;
	}
	.pb_backupbuddy_destpicker_test {
		text-align: center;
		display: inline-block;
		margin-right: 15px;
	}
	.pb_backupbuddy_destpicker_testload {
		display: none;
		vertical-align: -2px;
		margin-left: 10px;
		width: 12px;
		height: 12px;
	}
	.pb_backupbuddy_destpicker_save {
		//width: 90px;
		text-align: center;
		display: inline-block;
		margin-right: 15px;
	}
	.pb_backupbuddy_destpicker_saveload,.pb_backupbuddy_destpicker_deleteload {
		display: none;
		vertical-align: -4px;
		margin-left: 5px;
		width: 16px;
		height: 16px;
	}
	
	
	.pb_backupbuddy_destpicker_newdest {
		background-color:#EEEEEE;
		width: 90%;
		padding: 10px;
		margin-left: auto;
		margin-right: auto;
		margin-bottom: 10px;
		-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;
	}
	.pb_backupbuddy_destpicker_newdest_select {
		float: right;
		padding-top: 10px;
	}
	
	.form-table tbody tr th {
		//font-size: 12px;
	}
	
	
	.button-primary:hover {
		color: #FFFFFF;
	}
	
	.bb-dest-view-files {
		display: none;
		float: right;
		margin-right: 10px;
		margin-top: 5px;
		font-style: italic;
	}
</style>



<?php
if ( $mode == 'migration' ) {
	pb_backupbuddy::alert( '
		<b>' . __( 'Tip', 'it-l10n-backupbuddy' ) . ':</b>
		' . __( 'If you encounter difficulty try the ImportBuddy tool. Verify the destination URL by entering the "Migration URL", and clicking "Test Settings" before proceeding.', 'it-l10n-backupbuddy' ) .
		' ' . __( 'Only Local & FTP destinations may be used for automated migrations.', 'it-l10n-backupbuddy' ) . '
	' );
	echo '<br>';
}


global $pb_hide_save;
if ( pb_backupbuddy::_GET( 'add' ) != '' ) {
	
	$destination_type = pb_backupbuddy::_GET( 'add' );
	
	echo '<h2>Add New Destination</h2>';
	
	echo '<div class="pb_backupbuddy_destpicker_id bb-dest-option" rel="NEW">';
	$settings = pb_backupbuddy_destinations::configure( array( 'type' => $destination_type ), 'add' );
	
	if ( $settings === false ) {
		echo 'Error #556656a. Unable to display configuration.';
	} else {
		if ( $pb_hide_test !== true ) {
			$test_button = '<a href="#" class="button secondary-button pb_backupbuddy_destpicker_test" href="#" title="Test destination settings." style="margin-top: 3px;">Test Settings<img class="pb_backupbuddy_destpicker_testload" src="' . pb_backupbuddy::plugin_url() . '/images/loading.gif" title="Testing... This may take several seconds..."></a>&nbsp;&nbsp;';
		} else {
			$test_button = '';
		}
		if ( $pb_hide_save !== true ) {
			$save_button = '<img class="pb_backupbuddy_destpicker_saveload" src="' . pb_backupbuddy::plugin_url() . '/images/loading.gif" title="Saving... This may take a few seconds...">';
			echo $settings->display_settings( '+ Add Destination', $test_button, $save_button, 'pb_backupbuddy_destpicker_save' ); // title, before, after, class
		}
		
	}
	echo '</div>';
	echo '<br><br><br><a class="button secondary-button" href="' . $picker_url . '&callback_data=' . pb_backupbuddy::_GET( 'callback_data' ) . '&quickstart=' . pb_backupbuddy::_GET( 'quickstart' ) . '&filter=' . pb_backupbuddy::_GET( 'filter' ) . '">&larr; back to destinations</a>';
	
	return;
}

/*
pb_backupbuddy::load_script( 'admin.js', true ); // pbframework version due to second param.
pb_backupbuddy::load_script( 'admin.js' );
pb_backupbuddy::load_style( 'admin.css', true ); // pbframework version due to second param.
pb_backupbuddy::load_script( 'tooltip.js', true ); // pbframework version due to second param.
*/



// Determine how many destinations we will be listing.
if ( $mode == 'migration' ) {
	$destination_list_count = 0;
	foreach( pb_backupbuddy::$options['remote_destinations'] as $destination ) {
		if ( ( $destination['type'] != 'local' ) && ( $destination['type'] != 'ftp' ) ) { // if not local or ftp when in migration mode then skip.
			continue;
		} else {
			$destination_list_count++;
		}
	}
} else {
	$destination_list_count = count( pb_backupbuddy::$options['remote_destinations'] );
}


$i = 0;
if ( ( pb_backupbuddy::_GET( 'show_add' ) != 'true' ) && ( $destination_list_count > 0 ) ) {
	
	if ( pb_backupbuddy::_GET( 'alert_notice' ) != '' ) {
		pb_backupbuddy::alert( htmlentities( stripslashes( pb_backupbuddy::_GET( 'alert_notice' ) ) ) );
	}
	
	?>
	<div class="destination">
		<h3 class="pb_backupbuddy_selectexistingtext">Select existing destination<?php echo $action_verb; ?>:</h3>
		<div class="bb-dest clearfix pb_backupbuddy_destination_list">
	
	
		<?php
		
		// Offer post-send delete for manual sends.
		if ( ( pb_backupbuddy::_GET( 'sending' ) == '1' ) && ( count( pb_backupbuddy::$options['remote_destinations'] ) > 0 ) ) {
			echo '<div style="margin-bottom: 9px;"><label><input type="checkbox" name="delete_after" id="pb_backupbuddy_remote_delete" value="1"> <b>Delete local backup</b> after successful send?</label></div>';
		}
		
		foreach( pb_backupbuddy::$options['remote_destinations'] as $destination_id => $destination ) {
			
			if ( $mode == 'migration' ) {
				if ( ( $destination['type'] != 'local' ) && ( $destination['type'] != 'ftp' ) && ( $destination['type'] != 'sftp' ) ) { // if not local or ftp when in migration mode then skip.
					continue;
				}
			}
			
			// Filter only showing certain destination type.
			if ( '' != pb_backupbuddy::_GET( 'filter' ) ) {
				if ( $destination['type'] != pb_backupbuddy::_GET( 'filter' ) ) {
					continue; // Move along to next destination.
				}
			}
			
			// Destinations may hide the add and test buttons by altering these variables.
			$pb_hide_save = false;
			$pb_hide_test = false;
			
			$destination_info = pb_backupbuddy_destinations::get_info( $destination['type'] );
			?>
	
			<div class="bb-dest-option" id="pb_backupbuddy_destpicker_dest_<?php echo $destination_id; ?>" rel="<?php echo $destination_id; ?>">
				<a href="#select" class="info added dest_select_select" title="Click here<?php echo $action_verb; ?>.">
					<span class="icon <?php echo $destination['type']; ?>" style="background: transparent url('<?php echo pb_backupbuddy::plugin_url(); ?>/destinations/<?php echo $destination['type']; ?>/icon.png') top left no-repeat;"></span>
					<span class="type"><?php echo $destination_info['name']; ?></span>
					<span class="bb-dest-name" id="pb_backupbuddy_destpicker_title_<?php echo $destination_id; ?>"><?php echo $destination['title']; ?></span>
					<?php if ( 'email' != $destination['type'] ) {
						if ( pb_backupbuddy::_GET( 'sending' ) == '1' ) {
							echo '<span class="bb-dest-view-files">Send to this destination</span>';
						} elseif ( $mode == 'migration' ) {
							echo '<span class="bb-dest-view-files">Migrate to this destination</span>';
						} else {
							echo '<span class="bb-dest-view-files">View remote files</span>';
						}
					} ?>
				</a>
				<a href="#settings" class="optionicon open dest_select_open" style="background-image: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/dest_gear.png');" title="Click here to configure this destination's settings."></a>
				<div class="settings">
					<div class="settings-inside">
						
						<?php // DESTINATION CONFIG FORM
						$settings = pb_backupbuddy_destinations::configure( $destination, 'edit' );
						if ( $settings === false ) {
							echo 'Error #556656b. Unable to display configuration. This destination\'s settings may be corrupt. Removing this destination. Please refresh the page.';
							unset( pb_backupbuddy::$options['remote_destinations'][ $destination_id ] );
							pb_backupbuddy::save();
						} else {
							if ( $pb_hide_test !== true ) {
								$test_button = '<a href="#" class="button secondary-button pb_backupbuddy_destpicker_test" href="#" title="Test destination settings." style="margin-top: 3px;">Test Settings<img class="pb_backupbuddy_destpicker_testload" src="' . pb_backupbuddy::plugin_url() . '/images/loading.gif" title="Testing... This may take several seconds..."></a>&nbsp;&nbsp;';
							} else {
								$test_button = '';
							}
							if ( $pb_hide_save !== true ) {
								$save_and_delete_button = '<img class="pb_backupbuddy_destpicker_saveload" src="' . pb_backupbuddy::plugin_url() . '/images/loading.gif" title="Saving... This may take a few seconds...">';
							} else {
								$save_and_delete_button = '';
							}
							$save_and_delete_button .= '<a style="float: right;" href="#" class="button secondary-button pb_backupbuddy_destpicker_delete" href="#" title="Delete this Destination" style="margin-top: 3px;">Delete Destination<img class="pb_backupbuddy_destpicker_deleteload" src="' . pb_backupbuddy::plugin_url() . '/images/loading.gif" title="Deleting... This may take a few seconds..."></a>';
							echo $settings->display_settings( 'Save Settings', $test_button, $save_and_delete_button, 'pb_backupbuddy_destpicker_save' ); // title, before, after, class
						}
						//echo '<pre>' . print_r( $destination, true ) . '</pre>';
						?>
	
					</div>
				</div>
			</div>
			
			<?php
			$i++;
		} // end foreach.
		?>
	
		</div>
		
		<?php
		if ( 'true' != pb_backupbuddy::_GET( 'quickstart' ) ) {
			if ( $mode == 'migration' ) {
				echo '<h3>' . __( 'Or add new migration destination', 'it-l10n-backupbuddy' ) . ':</h3>';
			} else {
				echo '<h3>' . __( 'Or add new destination', 'it-l10n-backupbuddy' ) . ':</h3>';
			}
		?>
		
		<div class="bb-dest clearfix">
			<div class="bb-dest-option">
				<a href="<?php
						if ( $mode == 'migration' ) {
							echo pb_backupbuddy::ajax_url( 'migration_picker' );
						} else {
							echo pb_backupbuddy::ajax_url( 'destination_picker' );
						}
					?>&show_add=true&sending=<?php echo pb_backupbuddy::_GET( 'sending' ); ?>" class="info add-new open">
					<span class="icon plus" style="background-image: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/dest_plus.png');"></span>
					<span class="type">Add new</span>
					<?php
					if ( $mode == 'migration' ) {
						echo '<span class="bb-dest-name">FTP, sFTP, or Local</span>';
					} else {
						echo '<span class="bb-dest-name">Stash, FTP, S3, etc.</span>';
					}
					?>
					<span class="button button-primary" style="float: right; margin: 0;">+ Add New</span>
				</a>
				<div class="settings add-new">
					<div class="settings-inside">
						
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
	
	</div>
	
	
	
	<?php
} else { // Add Mode
	?>
	<div id="pb_backupbuddy_destpicker_slidecreatebox" style="<?php if ( $i > 0 ) { echo 'display: none;'; } ?>">
		<h3>Select a destination to add below:</h3>
		<br>
		
		<?php
		// Display all remote destinations the user can add.
		foreach( pb_backupbuddy_destinations::get_destinations_list() as $destination_name => $destination ) {
			
			if ( $mode == 'migration' ) {
				if ( ( $destination_name != 'local' ) && ( $destination_name != 'ftp' ) && ( $destination_name != 'sftp' ) ) { // if not local or ftp when in migration mode then skip.
					continue;
				}
			}
			
			?>
			<div class="pb_backupbuddy_destpicker_newdest" style="background: #FFFFFF; border-bottom: 2px dotted #DFDFDF; padding-bottom: 25px;">
				<div class="pb_backupbuddy_destpicker_newdest_select">
					<a href="<?php echo $picker_url; ?>&add=<?php echo $destination_name; ?>&callback_data=<?php echo pb_backupbuddy::_GET( 'callback_data' ); ?>&sending=<?php echo pb_backupbuddy::_GET( 'sending' ); ?>" class="button button-primary" id="pb_backupbuddy_addnewdest_launch">+ Add New</a>
				</div>
				<h1><?php
				
				if ( $destination_name == 'stash' ) {
					echo '<span class="backupbuddy-icon-drive" style="font-size: 1.2em; vertical-align: -4px;"> Stash';
				} else {
					echo $destination['name'];
				}
				?></h1>
				<?php
					echo $destination['description'];
					if ( $destination_name == 'stash' ) {
						echo '<br><br><div style="text-align: center;"><span class="pb_label pb_label-info" style="font-size: 12px; margin-left: 10px; position: relative; top: -3px;"><i>1 GB free for active BackupBuddy customers!</i></span></div>';
					}
				?>
			</div>
			<?php
			
		}
		?>
	
	</div>
	
	<?php
	
	if ( ( $mode != 'migration' ) && ( $destination_list_count > 0 ) ) { // Only show if the add page is available (non-migration and there are already-added destinations).
		echo'<br><br>
		<a href="' . pb_backupbuddy::ajax_url( 'destination_picker' ) . '&show_add=false&quickstart=' . pb_backupbuddy::_GET( 'quickstart' ) . '&filter=' . pb_backupbuddy::_GET( 'filter' ) . '" class="button button-secondary">
			&larr; back to destinations
		</a>';
	}
	echo '<br><br>';
	
}
?>

<style type="text/css">
	/* Core Styles - USED BY DIRECTORY EXCLUDER */
	.jqueryFileTree LI.directory { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/directory.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.expanded { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/folder_open.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.file { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/file.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.wait { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/spinner.gif') 6px 6px no-repeat; }
	/* File Extensions*/
	.jqueryFileTree LI.ext_3gp { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/film.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_afp { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/code.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_afpa { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/code.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_asp { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/code.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_aspx { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/code.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_avi { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/film.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_bat { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/application.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_bmp { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/picture.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_c { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/code.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_cfm { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/code.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_cgi { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/code.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_com { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/application.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_cpp { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/code.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_css { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/css.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_doc { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/doc.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_exe { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/application.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_gif { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/picture.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_fla { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/flash.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_h { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/code.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_htm { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/html.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_html { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/html.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_jar { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/java.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_jpg { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/picture.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_jpeg { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/picture.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_js { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/script.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_lasso { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/code.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_log { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/txt.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_m4p { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/music.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_mov { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/film.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_mp3 { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/music.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_mp4 { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/film.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_mpg { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/film.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_mpeg { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/film.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_ogg { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/music.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_pcx { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/picture.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_pdf { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/pdf.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_php { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/php.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_png { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/picture.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_ppt { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/ppt.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_psd { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/psd.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_pl { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/script.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_py { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/script.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_rb { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/ruby.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_rbx { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/ruby.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_rhtml { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/ruby.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_rpm { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/linux.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_ruby { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/ruby.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_sql { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/db.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_swf { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/flash.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_tif { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/picture.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_tiff { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/picture.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_txt { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/txt.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_vb { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/code.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_wav { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/music.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_wmv { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/film.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_xls { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/xls.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_xml { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/code.png') 6px 6px no-repeat; }
	.jqueryFileTree LI.ext_zip { background: url('<?php echo pb_backupbuddy::plugin_url(); ?>/images/filetree/zip.png') 6px 6px no-repeat; }
</style>