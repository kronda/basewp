<?php
pb_backupbuddy::$ui->title(
	__( 'Database Rollback', 'it-l10n-backupbuddy' ) .
	' &nbsp;&nbsp; <a style="font-size: 0.6em;" href="#" onClick="jQuery(\'#pb_backupbuddy_status_wrap\').toggle();">Show / Hide Advanced Status Details</a>'
); ?>

<script>
function pb_status_undourl( undo_url ) {
	if ( '' == undo_url ) {
		jQuery( '#pb_backupbuddy_undourl' ).parent('#message').slideUp();
		return;
	}
	jQuery( '#pb_backupbuddy_undourl' ).attr( 'href', undo_url );
	jQuery( '#pb_backupbuddy_undourl' ).text( undo_url );
	jQuery( '#pb_backupbuddy_undourl' ).parent('#message').slideDown();
}
</script>

<style>
	#pb_backupbuddy_status_wrap {
		display: none;
		margin-bottom: 10px;
	}
</style>



<?php
echo '<div id="pb_backupbuddy_status_wrap">';
echo pb_backupbuddy::status_box( 'Starting rollback process . . .' );
echo '</div>';
global $wp_version;
pb_backupbuddy::status( 'details', 'BackupBuddy v' . pb_backupbuddy::settings( 'version' ) . ' using WordPress v' . $wp_version . ' on ' . PHP_OS . '.' );
?>


<script type="text/javascript">
	function pb_status_append( status_string ) {
		target_id = 'pb_backupbuddy_status'; // importbuddy_status or pb_backupbuddy_status
		if( jQuery( '#' + target_id ).length == 0 ) { // No status box yet so suppress.
			return;
		}
		jQuery( '#' + target_id ).append( "\n" + status_string );
		textareaelem = document.getElementById( target_id );
		textareaelem.scrollTop = textareaelem.scrollHeight;
	}
</script>


<div id="message" style="display: none; padding: 9px;" rel="" class="pb_backupbuddy_alert updated fade below-h2">
	<?php _e( 'If the rollback should fail for any reason you may undo its changes at any time by visiting the URL', 'it-l10n-backupbuddy' ); ?>:<br>
	<a href="" id="pb_backupbuddy_undourl" target="pb_backupbuddy_modal_iframe"></a>
</div>


<iframe id="pb_backupbuddy_modal_iframe" name="pb_backupbuddy_modal_iframe" src="<?php echo pb_backupbuddy::ajax_url( 'rollback' ); ?>&step=<?php echo pb_backupbuddy::_GET( 'step' ); ?>&archive=<?php echo pb_backupbuddy::_GET( 'rollback' ); ?>" width="100%" style="max-width: 1000px;" height="1800" frameBorder="0" padding="0" margin="0">Error #4584594579. Browser not compatible with iframes.</iframe>
