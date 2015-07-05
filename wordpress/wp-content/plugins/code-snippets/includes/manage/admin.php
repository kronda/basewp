<?php

/**
 * HTML code for the Manage Snippets page
 *
 * @package Code_Snippets
 * @subpackage Manage
 */

/* Bail if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

global $code_snippets_list_table;
?>

<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php
	esc_html_e( 'Snippets', 'code-snippets' );

	printf( '<a href="%2$s" class="add-new-h2">%1$s</a>',
		esc_html_x( 'Add New', 'snippet', 'code-snippets' ),
		code_snippets_get_menu_url( 'add' )
	);

	$code_snippets_list_table->search_notice();
	?></h2>

	<?php $code_snippets_list_table->views(); ?>

	<form method="get" action="">
		<?php
			$code_snippets_list_table->required_form_fields( 'search_box' );
			$code_snippets_list_table->search_box( __( 'Search Installed Snippets', 'code-snippets' ), 'search_id' );
		?>
	</form>
	<form method="post" action="">
		<?php
			$code_snippets_list_table->required_form_fields();
			$code_snippets_list_table->display();
		?>
	</form>

	<?php do_action( 'code_snippets/admin/manage' ); ?>

</div>
