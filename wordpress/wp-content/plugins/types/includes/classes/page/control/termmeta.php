<?php

/**
 * Term Fields Control page handler.
 */
final class WPCF_Page_Control_Termmeta extends WPCF_Page_Listing_Abstract {

	const PAGE_NAME = 'wpcf-termmeta-control';

	const BULK_ACTION_NONCE = 'wpcf-termmeta-control-bulk-action-nonce';

	const SCREEN_OPTION_PER_PAGE_NAME = 'wpcf_termmeta_control_per_page';

	const SCREEN_OPTION_PER_PAGE_DEFAULT_VALUE = 10;


	private function get_title() {
		return __( 'Term Field Control', 'wpcf' );
	}


	public function add_submenu( $submenus ) {
		return $submenus;
	}


	/**
	 * Call this only if you are actually showing the page.
	 */
	public function initialize() {

		$page_hook = wpcf_admin_add_submenu_page(
			array(
				'menu_title' => $this->get_title(),
				'function' => array( $this, 'page_handler' ),
				'capability_filter' => 'wpcf_tfc_view'
			),
			wpcf_getget( 'page' )
		);

		// I hate having to do this. Refactor!
		require_once WPCF_INC_ABSPATH . '/fields.php';

		wpcf_fields_contol_common_resources();

		add_action( "load-$page_hook", array( $this, 'add_screen_options' ) );
	}


	/**
	 * Render the page.
	 *
	 * @return void
	 */
	public function page_handler() {

		do_action( 'wpcf_admin_page_init' );
		wpcf_admin_load_collapsible();
		//$this->add_screen_options();

		wpcf_add_admin_header( $this->get_title(), array( 'page' => self::PAGE_NAME ) );

		$list_table = new WPCF_Page_Control_Termmeta_Table();

		$list_table->prepare_items();

		if( !$list_table->has_items() ) {
			add_action( 'wpcf_groups_list_table_after', 'wpcf_admin_promotional_text' );
		}

		?>

		<!-- form id used in custom-fields-control-form.js -->
		<form id="wpcf-custom-fields-control-form" method="post">

			<!-- For plugins, we also need to ensure that the form posts back to our current page -->
			<input type="hidden" name="page" value="<?php echo self::PAGE_NAME; ?>"/>

			<!-- Pass the information about the kind of fields and groups to JS -->
			<input type="hidden" id="wpcf_admin_field_type" value="wpcf-termmeta" />

			<!-- This will be used by some of the bulk action dialogs to pass back some information -->
			<input type="hidden" name='wpcf-id' id='wpcf_admin_custom_fields_control_type' value='' />

			<?php
			$list_table->search_box( __( 'Search term fields', 'wpcf' ), 'search_id' );
			$list_table->display();

			wp_nonce_field( self::BULK_ACTION_NONCE );
			?>
		</form>
		<?php

		wpcf_add_admin_footer();

	}


	protected function get_page_name() {
		return self::PAGE_NAME;
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