<?php

/**
 * HTML code for the Add New/Edit Snippet page
 *
 * @package    Code_Snippets
 * @subpackage Admin_Views
 */

if ( ! class_exists( 'Code_Snippets' ) )
	exit;

global $code_snippets;

$table   = $code_snippets->get_table_name();
$screen  = get_current_screen();

$edit_id = ( isset( $_REQUEST['edit'] ) ? absint( $_REQUEST['edit'] ) : 0 );
$snippet = $code_snippets->get_snippet( $edit_id );

$code_snippets->admin->get_messages( 'single' );

?>

<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php
		if ( $edit_id ) {
			esc_html_e( 'Edit Snippet', 'code-snippets' );

			if ( $code_snippets->user_can( 'install' ) )
				printf( ' <a href="%1$s" class="add-new-h2">%2$s</a>',
					$code_snippets->admin->single_url,
					esc_html_x( 'Add New', 'snippet', 'code-snippets' )
				);
		} else {
			esc_html_e( 'Add New Snippet', 'code-snippets' );
		}
	?></h2>

	<form method="post" action="" style="margin-top: 10px;">
		<?php

			/* Output the hidden fields */

			if ( 0 !== $snippet->id )
				printf ( '<input type="hidden" name="snippet_id" value="%d" />', $snippet->id );

			printf ( '<input type="hidden" name="snippet_active" value="%d" />', $snippet->active );
		?>
		<div id="titlediv">
			<div id="titlewrap">
				<label for="title" style="display: none;"><?php _e( 'Name (short title)', 'code-snippets' ); ?></label>
				<input id="title" type="text" autocomplete="off" name="snippet_name" value="<?php echo $snippet->name; ?>" placeholder="<?php _e( 'Name (short title)', 'code-snippets' ); ?>" />
			</div>
		</div>

		<label for="snippet_code">
			<h3><?php _e( 'Code', 'code-snippets' ); ?></h3>
		</label>

		<textarea id="snippet_code" name="snippet_code" rows="20" spellcheck="false" style="font-family: monospace; width: 100%;"><?php echo esc_textarea( $snippet->code ); ?></textarea>

		<?php

			/* Allow addon plugins (and us!) to add fields and content to this page */
			do_action( 'code_snippets/admin/single', $snippet );

			/* Add a nonce for security */
			wp_nonce_field( 'save_snippet' );

		?>

		<p class="submit">
			<?php

				/* Save Snippet button */

				submit_button( null, 'primary', 'save_snippet', false );

				/* Save Snippet & Activate/Deactivate button */

				if ( ! $snippet->active ) {
					submit_button(
						__( 'Save Changes &amp; Activate', 'code-snippets' ),
						'secondary', 'save_snippet_activate', false
					);

				} else {
					submit_button(
						__( 'Save Changes &amp; Deactivate', 'code-snippets' ),
						'secondary', 'save_snippet_deactivate', false
					);
				}

				if ( 0 !== $snippet->id ) {

					/* Export button */

					submit_button( __( 'Export', 'code-snippets' ), 'secondary', 'export_snippet', false );

					/* Delete button */

					$confirm_delete_js = esc_js(
						sprintf (
							'return confirm("%s");',
							__( "You are about to permanently delete this snippet.\n'Cancel' to stop, 'OK' to delete.", 'code-snippets' )
						)
					);

					submit_button(
						__( 'Delete', 'code-snippets' ),
						'secondary', 'delete_snippet', false,
						sprintf ( 'onclick="%s"', $confirm_delete_js )
					);
				}

			?>
		</p>

	</form>
</div>
