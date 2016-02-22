<?php

/**
 * Edit Term Field Group page handler.
 *
 * This is a wrapper around an implementation taken from the legacy code. All of it needs complete refactoring.
 *
 * @since 1.9
 */
final class WPCF_Page_Edit_Termmeta extends WPCF_Page_Abstract {

	const PAGE_NAME = 'wpcf-termmeta-edit';

	/**
	 * Name of the form rendered by wpcf_form.
	 */
	const FORM_NAME = 'wpcf_form_termmeta_fields';


	/**
	 * @return WPCF_Page_Edit_Termmeta
	 */
	public static function get_instance() {
		return parent::get_instance();
	}


	/**
	 * Call this only if you are actually showing the page.
	 */
	public function initialize() {

		$hook = wpcf_admin_add_submenu_page(
			array(
				'menu_title' => $this->get_menu_title(),
				'function' => array( $this, 'page_handler' ),
				'capability' => WPCF_TERM_FIELD_EDIT
			),
			wpcf_getget( 'page' )
		);

		$load_page_action = 'load-' . $hook;

		// Prepare form, which includes saving data and optionally redirecting to the edit page with group ID
		// as GET parameter. That's why it must be executed earlier than as a menu page callback.
		add_action( $load_page_action, array( $this, 'prepare_form_maybe_redirect' ) );

		// This one handles enqueuing script and styles. Originally it is meant for post fields and it also probably
		// does some things that are not needed now.
		add_action( $load_page_action, 'wpcf_admin_enqueue_group_edit_page_assets' );

		wpcf_admin_plugin_help( $hook, self::PAGE_NAME );
	}


	public function initialize_ajax_handler() {
		new WPCF_Page_Edit_Termmeta_Form();
	}


	public function get_menu_title() {
		return __( 'Edit Term Field Group', 'wpcf' );
	}


	public function get_page_title( $purpose = 'edit' ) {
		switch ( $purpose ) {
			case 'add':
				return __( 'Add New Term Field Group', 'wpcf' );
			case 'view':
				return __( 'View Term Field Group', 'wpcf' );
			default:
				return __( 'Edit Term Field Group', 'wpcf' );
		}
	}


	public function page_handler() {

		// Following code taken from the legacy parts. Needs refactoring.

		// By now we expect that prepare_form_maybe_redirect() was already called. If not, something went terribly wrong.
		if( null == $this->wpcf_admin ) {
			return;
		}

		// Well this doesn't look right.
		$post_type = current_filter();

		// Start rendering the page.

		// Header and title
		$page_purpose = $this->wpcf_admin->get_page_purpose();
		$add_new_button = ( 'edit' == $page_purpose ) ? array( 'page' => self::PAGE_NAME ) : false;
		wpcf_add_admin_header( $this->get_page_title( $page_purpose ), $add_new_button );

		// Display WPML admin notices if there are any.
		wpcf_wpml_warning();

		// Transform the form data into an Enlimbo form
		$form = wpcf_form( self::FORM_NAME, $this->form );

		// Dark magic happens here.
		echo '<form method="post" action="" class="wpcf-fields-form wpcf-form-validate js-types-show-modal">';
		wpcf_admin_screen( $post_type, $form->renderForm() );
		echo '</form>';

		wpcf_add_admin_footer();

	}

	/**
	 * @var null|array
	 */
	private $form = null;

	/**
	 * @var WPCF_Page_Edit_Termmeta_Form
	 */
	private $wpcf_admin = null;


	/**
	 * Prepare the form data.
	 *
	 * That includes saving, which may also include redirecting to the edit page with newly created group's ID
	 * in a GET parameter.
	 */
	public function prepare_form_maybe_redirect() {

		// Following code taken from the legacy parts. Needs refactoring.

		require_once WPCF_INC_ABSPATH . '/fields.php';
		require_once WPCF_INC_ABSPATH . '/fields-form.php';
		require_once WPCF_INC_ABSPATH . '/classes/class.types.admin.edit.custom.fields.group.php';

		$wpcf_admin = new WPCF_Page_Edit_Termmeta_Form();
		$wpcf_admin->init_admin();

		$this->form = $wpcf_admin->form();
		$this->wpcf_admin = $wpcf_admin;
	}

}