<?php

/**
 * HTML code for the Import Snippets page
 *
 * @package    Code_Snippets
 * @subpackage Admin_Views
 */

if ( ! class_exists( 'Code_Snippets' ) )
	exit;

global $code_snippets;

$code_snippets->admin->get_messages( 'import' );

?>
<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e( 'Import Snippets', 'code-snippets' ); ?></h2>

	<div class="narrow">

		<p><?php _e( 'Howdy! Upload your Code Snippets export file and we&#8217;ll import the snippets to this site.', 'code-snippets' ); ?></p>

		<p><?php printf( __( 'You will need to go to the <a href="%s">Manage Snippets</a> page to activate the imported snippets.', 'code-snippets' ), $code_snippets->admin->manage_url ); ?></p>

		<p><?php _e( 'Choose a Code Snippets (.xml) file to upload, then click Upload file and import.', 'code-snippets' ); ?></p>

		<form enctype="multipart/form-data" method="post" action="" id="import-upload-form" name="code_snippets_import">
			<p>
				<input type="hidden" name="action" value="save" />
				<input type="hidden" name="max_file_size" value="8388608" />

				<label for="upload"><?php _e( 'Choose a file from your computer:', 'code-snippets' ); ?></label>
				<?php _e( '(Maximum size: 8MB)', 'code-snippets' ); ?>
				<input type="file" id="upload" name="code_snippets_import_file" size="25" accept="text/xml" />
			</p>

			<?php
				do_action( 'code_snippets/admin/import_form' );
				submit_button( __( 'Upload file and import', 'code-snippets' ) );
			?>

		</form>
	</div>
</div>
