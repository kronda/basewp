<?php
if ( ! defined( 'PB_IMPORTBUDDY' ) || ( true !== PB_IMPORTBUDDY ) ) {
	die( '<html></html>' );
}
?>

</div>
</div>



<?php if ( pb_backupbuddy::$options['display_mode'] == 'normal' ) { ?>
	<div class="footer"><br><br>
		<center>
			<?php
			if ( pb_backupbuddy::$options['bb_version'] != '') {
				echo '<br><span class="footer_text">ImportBuddy v' . pb_backupbuddy::$options['bb_version'] . ' for <a href="http://ithemes.com/">BackupBuddy</a></span>';
			}
			?>
		</center>
	</div>
<?php } ?>



</body>
</html>
