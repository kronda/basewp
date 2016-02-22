<?php

/**
 * Term Field Groups listing page.
 */
final class WPCF_Page_Listing_Termmeta extends WPCF_Page_Listing_Abstract {


	const PAGE_NAME = 'wpcf-termmeta-listing';

	const BULK_ACTION_NONCE = 'wpcf-termmeta-listing-bulk-action-nonce';

	const SCREEN_OPTION_PER_PAGE_DEFAULT_VALUE = 10;

	const SCREEN_OPTION_PER_PAGE_NAME = 'wpcf_termmeta_listing_per_page';


	public function add_submenu( $submenus ) {

		$termmeta_submenu = array(
			'menu_title' => __( 'Term Fields', 'wpcf' ),
			'menu_slug' => self::PAGE_NAME,
			'function' => array( $this, 'page_handler' ),
			'capability' => WPCF_TERM_FIELD_EDIT,
			'toolset_icon' => 'dashicons dashicons-index-card'
		);

		add_action( 'wpcf_admin_add_submenu_page_' . self::PAGE_NAME, array( $this, 'on_page_hook_published' ) );

		return $this->add_submenu_at_the_end( $submenus, $termmeta_submenu );
	}


	public function page_handler() {

		do_action( 'wpcf_admin_page_init' );

		wpcf_admin_page_add_options('uf',  __( 'User Fields', 'wpcf' ));
		//$this->add_screen_options();


		// Without this, the Activate/Deactivate link doesn't work properly (why?)
		wpcf_admin_load_collapsible();

		wpcf_admin_page_add_options('tf',  __( 'Term Fields', 'wpcf' ));

		wpcf_add_admin_header( __( 'Term Field Groups', 'wpcf' ), array( 'page' => WPCF_Page_Edit_Termmeta::PAGE_NAME ) );

		require_once WPCF_INC_ABSPATH . '/fields.php';
		// require_once WPCF_INC_ABSPATH . '/fields-list.php';

		$list_table = new WPCF_Page_Listing_Termmeta_Table();

		$list_table->prepare_items();

		if( !$list_table->has_items() ) {
			add_action( 'wpcf_groups_list_table_after', 'wpcf_admin_promotional_text' );
		}

		?>
		<form id="cf-filter" method="post">
			<!-- For plugins, we also need to ensure that the form posts back to our current page -->
			<input type="hidden" name="page" value="<?php echo self::PAGE_NAME; ?>"/>

			<?php

			$list_table->search_box( __( 'Search Term Field Groups', 'wpcf' ), 'search_id' );
			$list_table->display();

			wp_nonce_field( self::BULK_ACTION_NONCE );
			?>
		</form>
		<?php
		do_action( 'wpcf_groups_list_table_after' );

		$this->show_term_field_control_box();

		wpcf_add_admin_footer();
	}

	protected function get_page_name() {
		return self::PAGE_NAME;
	}


	/**
	 * Show box with a link to Term Field Control page.
	 */
	function show_term_field_control_box() {
		$form = array();

		$form['table-1-open'] = array(
			'#type' => 'markup',
			'#markup' => '<table class="wpcf-types-form-table widefat js-wpcf-slugize-container"><thead><tr><th>' . __( 'Term Field Control', 'wpcf' ) . '</th></tr></thead><tbody>',
			'_builtin' => true,
		);
		$form['table-row-1-open'] = array(
			'#type' => 'markup',
			'#markup' => '<tr><td>',
			'_builtin' => true,
		);

		$form['table-row-1-content-1'] = array(
			'#type' => 'markup',
			'#markup' => '<p>' . __( 'You can control Term Fields by removing them from the groups, changing type or just deleting.', 'wpcf' ),
			'_builtin' => true,
		);

		$form['table-row-1-content-2'] = array(
			'#type' => 'markup',
			'#markup' => sprintf(
				' <a class="button" href="%s">%s</a></p>',
				esc_url(
					add_query_arg(
						array( 'page' => WPCF_Page_Control_Termmeta::PAGE_NAME ),
						admin_url( 'admin.php' )
					)
				),
				__( 'Term Field Control', 'wpcf' )
			),
			'_builtin' => true,
		);

		$form['table-row-1-close'] = array(
			'#type' => 'markup',
			'#markup' => '</td></tr>',
			'_builtin' => true,
		);
		$form['table-1-close'] = array(
			'#type' => 'markup',
			'#markup' => '</tbody></table>',
			'_builtin' => true,
		);
		$form = wpcf_form( self::PAGE_NAME . '-field-control-box', $form );
		echo $form->renderForm();

	}


	public function on_page_hook_published( $page_hook ) {
		add_action( "load-$page_hook", array( $this, 'add_screen_options' ) );
	}


	public function add_screen_options() {

		$args = array(
			'label' => __( 'Term Fields', 'wpcf' ),
			'default' => self::SCREEN_OPTION_PER_PAGE_DEFAULT_VALUE,
			'option' => self::SCREEN_OPTION_PER_PAGE_NAME,
		);
		add_screen_option( 'per_page', $args );

		add_filter( 'set-screen-option', array( $this, 'set_screen_option' ), 10, 3);
	}


	function set_screen_option($status, $option, $value) {

		if ( self::SCREEN_OPTION_PER_PAGE_NAME == $option ) {
			return $value;
		}

		return $status;

	}

}