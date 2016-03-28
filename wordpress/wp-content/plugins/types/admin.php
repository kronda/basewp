<?php
/**
 *
 * Admin functions
 *
 *
 */
require_once WPCF_ABSPATH.'/marketing.php';
require_once WPCF_ABSPATH.'/includes/classes/class.wpcf.roles.php';
WPCF_Roles::getInstance();
/*
 * This needs to be called after main 'init' hook.
 * Main init hook calls required Types code for frontend.
 * Admin init hook only in admin area.
 *
 * TODO Revise it to change to 'admin_init'
 */
add_action( 'admin_init', 'wpcf_admin_init_hook', 11 );

add_action( 'init', 'wpcf_init_admin_pages' );

add_action( 'admin_menu', 'wpcf_admin_menu_hook' );
add_action( 'wpcf_admin_page_init', 'wpcf_enqueue_scripts' );

// OMG, why so early? At this point we don't even have embedded Types (with functions.php).
if ( defined( 'DOING_AJAX' ) ) {
    require_once WPCF_INC_ABSPATH . '/ajax.php';
    if ( isset($_REQUEST['action']) ) {
        switch( $_REQUEST['action']){
            /**
             * post edit screen
             */
        case 'wpcf_edit_post_get_child_fields_screen':
        case 'wpcf_edit_post_get_icons_list':
        case 'wpcf_edit_post_save_child_fields':
            require_once WPCF_INC_ABSPATH.'/classes/class.types.admin.edit.post.type.php';
            new Types_Admin_Edit_Post_Type();
            break;
            /**
             * custom fields group edit screen
             */
        case 'wpcf_ajax_filter':
        case 'wpcf_edit_field_choose':
        case 'wpcf_edit_field_insert':
        case 'wpcf_edit_field_select':
        case 'wpcf_edit_field_add_existed': {

	        require_once WPCF_INC_ABSPATH.'/classes/class.types.admin.edit.custom.fields.group.php';

	        // Be careful here. For some AJAX actions we rely on the fact that the page parameter is not set and/or
	        // that post and user fields can use the same handler (which is originally meant for post fields only).

	        // We don't have functions.php at this point, can't use wpcf_getpost().
	        $current_page = isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : Types_Admin_Edit_Custom_Fields_Group::PAGE_NAME;
	        if( in_array( $current_page, array( Types_Admin_Edit_Custom_Fields_Group::PAGE_NAME, 'wpcf-edit-usermeta' ) ) ) {
		        new Types_Admin_Edit_Custom_Fields_Group();
	        }

	        // For other pages, we will initialize during the 'init' hook when the autoloader is already available.
	        // At this point we don't even have access to names of the pages.
	        // See wpcf_init_admin_pages().
	        break;
        }
        case 'wpcf_edit_field_condition_get':
        case 'wpcf_edit_field_condition_get_row':
        case 'wpcf_edit_field_condition_save':
        case 'wpcf_edit_custom_field_group_get':
            require_once WPCF_INC_ABSPATH.'/classes/class.types.fields.conditional.php';
            new Types_Fields_Conditional();
            break;
        case 'wpcf_edit_post_get_fields_box':
            require_once WPCF_INC_ABSPATH.'/classes/class.types.admin.fields.php';
            new Types_Admin_Fields();
            break;
            /**
             * custom fields control screen
             */
        case 'wpcf_custom_fields_control_change_type':
        case 'wpcf_custom_fields_control_get_groups':
        case 'wpcf_usermeta_control_get_groups':
            require_once WPCF_INC_ABSPATH.'/classes/class.types.admin.control.fields.php';
            new Types_Admin_Control_Fields();
            break;
        }
    }
}
include_once WPCF_ABSPATH.'/includes/classes/class.wpcf.marketing.messages.php';
new WPCF_Types_Marketing_Messages();

/**
 * last edit flag
 */
if ( !defined('TOOLSET_EDIT_LAST' )){
    define( 'TOOLSET_EDIT_LAST', '_toolset_edit_last');
}

/**
 * last author
 */
if ( !defined('WPCF_AUTHOR' )){
    define( 'WPCF_AUTHOR', '_wpcf_author_id');
}

/**
 * admin_init hook.
 */
function wpcf_admin_init_hook()
{
    wp_register_style('wpcf-css-embedded', WPCF_EMBEDDED_RES_RELPATH . '/css/basic.css', array(), WPCF_VERSION );

    wp_enqueue_style( 'wpcf-promo-tabs', WPCF_EMBEDDED_RES_RELPATH . '/css/tabs.css', array(), WPCF_VERSION );
    wp_enqueue_style('toolset-dashicons');

}


/**
 * Initialize admin pages.
 *
 * @todo This, also, needs a review very badly.
 * @since 1.9
 */
function wpcf_init_admin_pages() {

	if( is_admin() ) {
		WPCF_Page_Listing_Termmeta::get_instance();
	}

	if( defined( 'DOING_AJAX' ) ) {
		$action = wpcf_getpost( 'action' );
		$current_page = wpcf_getpost( 'page' );

		switch( $action ) {

			case 'wpcf_edit_field_select':
			case 'wpcf_ajax_filter': {
				if( WPCF_Page_Edit_Termmeta::PAGE_NAME == $current_page ) {
					WPCF_Page_Edit_Termmeta::get_instance()->initialize_ajax_handler();
				}
				break;
			}
		}
	}


}


/**
 * Get information about admin menu subpages.
 *
 * It is also being used as a source for dashboard items.
 *
 * @return array See the wpcf_admin_menu_get_subpages filter description.
 */
function wpcf_admin_menu_get_subpages()
{
    $subpages = array();

    // Dashboard
    $subpages['wpcf-dashboard'] = array(
        'menu_title' => __( 'Dashboard', 'wpcf' ),
        'function'   => 'wpcf_admin_menu_summary_dashboard',
        'capability_filter' => 'wpcf_cpt_view',
        'capability' => WPCF_CUSTOM_POST_TYPE_VIEW,
        'context' => 'menu_only',
    );

    // Post Types
    $subpages['wpcf-cpt'] = array(
        'menu_title' => __( 'Post Types', 'wpcf' ),
        'function'   => 'wpcf_admin_menu_summary_cpt',
        'capability_filter' => 'wpcf_cpt_view',
        'capability' => WPCF_CUSTOM_POST_TYPE_VIEW,
        'toolset_icon' => 'dashicons dashicons-admin-post',
    );

    // Taxonomies
    $subpages['wpcf-ctt'] = array(
        'menu_title' => __( 'Taxonomies', 'wpcf' ),
        'function'   => 'wpcf_admin_menu_summary_ctt',
        'capability_filter' => 'wpcf_ctt_view',
        'capability' => WPCF_CUSTOM_TAXONOMY_VIEW,
        'toolset_icon' => 'dashicons dashicons-tag',
    );

    // Custom fields
    $subpages['wpcf-cf'] = array(
        'menu_title' => __( 'Post Fields', 'wpcf' ),
        'function'   => 'wpcf_admin_menu_summary',
        'capability_filter' => 'wpcf_cf_view',
        'capability' => WPCF_CUSTOM_FIELD_VIEW,
        'toolset_icon' => 'dashicons dashicons-forms',
    );

    // User Meta
    $subpages['wpcf-um'] = array(
        'menu_title' => __( 'User Fields', 'wpcf' ),
        'function'   => 'wpcf_usermeta_summary',
        'capability_filter' => 'wpcf_uf_view',
        'capability' => WPCF_USER_META_FIELD_VIEW,
        'toolset_icon' => 'dashicons dashicons-id-alt',
    );

    // Settings
    $subpages['wpcf-custom-settings'] = array(
        'menu_title' => __( 'Settings', 'wpcf' ),
        'function'   => 'wpcf_admin_menu_settings',
        'toolset_icon' => 'dashicons dashicons-admin-settings',
    );


	/**
	 * Allow for adding more admin menu subpages.
	 *
	 * Each subpage definition is an associative with following elements:
	 *
	 * string $menu_title: Title to be displayed in the menu.
	 * string $function: Callback function name to render the page.
	 * string $capability_filter: Name of the filter that will be applied to the capability,
	 *     see wpcf_admin_add_submenu_page() for details.
	 * string $capability: Capability required to access the page.
	 * string $context: Where to display this menu item. 'context_only'|'dashboard_only'|missing
	 *
	 * Key of the subpage definition is the page name.
	 */
    $subpages = apply_filters( 'wpcf_admin_menu_get_subpages', $subpages );

    return $subpages;
}

/**
 * admin_menu hook.
 */
function wpcf_admin_menu_hook()
{
    $wpcf_capability = apply_filters( 'wpcf_capability', WPCF_CUSTOM_POST_TYPE_VIEW);

    add_menu_page(
        __( 'Types', 'wpcf' ),
        __( 'Types', 'wpcf' ),
        $wpcf_capability,
        'wpcf',
        'wpcf_admin_menu_summary'
    );

    $subpages = wpcf_admin_menu_get_subpages();

    foreach( $subpages as $menu_slug => $menu ) {
        if ( isset($menu['context']) && 'dashboard_only' == $menu['context'] ) {
            continue;
        }
        wpcf_admin_add_submenu_page($menu, $menu_slug);
    }

    if ( isset( $_GET['page'] ) ) {
	    $current_page = $_GET['page'];
	    switch ( $current_page ) {
		    /**
		     * User Fields Control
		     */
		    case 'wpcf-user-fields-control':
			    wpcf_admin_add_submenu_page(
				    array(
					    'menu_title' => __( 'User Field Control', 'wpcf' ),
					    'function' => 'wpcf_admin_menu_user_fields_control',
					    'capability_filter' => 'wpcf_ufc_view',
				    ),
				    'wpcf-user-fields-control'
			    );
			    break;

		    /**
		     *  Post Fields Control
		     */
		    case 'wpcf-custom-fields-control':
			    wpcf_admin_add_submenu_page(
				    array(
					    'menu_title' => __( 'Post Field Control', 'wpcf' ),
					    'function' => 'wpcf_admin_menu_custom_fields_control',
					    'capability_filter' => 'wpcf_cfc_view',
				    ),
				    'wpcf-custom-fields-control'
			    );
			    break;
		    /**
		     * Import/Export
		     */
		    case 'wpcf-import-export':
			    wpcf_admin_add_submenu_page(
				    array(
					    'menu_title' => __( 'Import/Export', 'wpcf' ),
					    'function' => 'wpcf_admin_menu_import_export',
				    ),
				    'wpcf-import-export'
			    );
			    break;

		    /**
		     * debug
		     */
		    case 'wpcf-debug-information':
			    wpcf_admin_add_submenu_page(
				    array(
					    'menu_title' => __( 'Debug Information', 'wpcf' ),
					    'function' => 'wpcf_admin_menu_debug_information',
				    ),
				    'wpcf-debug-information'
			    );
			    break;
		    /**
		     * custom field grup
		     */
		    case 'wpcf-edit':
			    $title = isset( $_GET['group_id'] ) ? __( 'Edit Group', 'wpcf' ) : __( 'Add New Post Field Group', 'wpcf' );
			    $hook  = wpcf_admin_add_submenu_page(
				    array(
					    'menu_title' => $title,
					    'function' => 'wpcf_admin_menu_edit_fields',
					    'capability' => WPCF_CUSTOM_FIELD_VIEW
				    ),
				    $current_page
			    );
			    add_action( 'load-' . $hook, 'wpcf_admin_menu_edit_fields_hook' );
			    wpcf_admin_plugin_help( $hook, 'wpcf-edit' );
			    break;

		    case 'wpcf-view-custom-field':
			    $hook = wpcf_admin_add_submenu_page(
				    array(
					    'menu_title' => __( 'View Post Field Group', 'wpcf' ),
					    'function' => 'wpcf_admin_menu_edit_fields',
					    'capability' => WPCF_CUSTOM_FIELD_VIEW
				    ),
				    $current_page
			    );
			    wpcf_admin_plugin_help( $hook, 'wpcf-edit' );
			    break;
		    /**
		     * custom post
		     */
		    case 'wpcf-edit-type':
			    $title = __( 'Add New Post Type', 'wpcf' );
			    if ( isset( $_GET['wpcf-post-type'] ) ) {
				    $title = __( 'Edit Post Type', 'wpcf' );
			    }
			    $hook = wpcf_admin_add_submenu_page(
				    array(
					    'menu_title' => $title,
					    'function' => 'wpcf_admin_menu_edit_type',
					    'capability' => WPCF_CUSTOM_FIELD_EDIT
				    ),
				    $current_page
			    );
			    add_action( 'load-' . $hook, 'wpcf_admin_menu_edit_type_hook' );
			    wpcf_admin_plugin_help( $hook, 'wpcf-edit-type' );
			    break;

		    case 'wpcf-view-type':
			    $hook = wpcf_admin_add_submenu_page(
				    array(
					    'menu_title' => __( 'View Post Type', 'wpcf' ),
					    'function' => 'wpcf_admin_menu_edit_type',
					    'capability' => WPCF_CUSTOM_FIELD_VIEW
				    ),
				    $current_page
			    );
			    add_action( 'load-' . $hook, 'wpcf_admin_menu_edit_type_hook' );
			    wpcf_admin_plugin_help( $hook, 'wpcf-edit-type' );
			    break;

		    case 'wpcf-edit-tax':
			    $title = isset( $_GET['wpcf-tax'] ) ? __( 'Edit Taxonomy', 'wpcf' ) : __( 'Add New Taxonomy', 'wpcf' );
			    $hook  = wpcf_admin_add_submenu_page(
				    array(
					    'menu_title' => $title,
					    'function' => 'wpcf_admin_menu_edit_tax',
					    'capability' => WPCF_CUSTOM_TAXONOMY_EDIT
				    ),
				    $current_page
			    );
			    add_action( 'load-' . $hook, 'wpcf_admin_menu_edit_tax_hook' );
			    wpcf_admin_plugin_help( $hook, 'wpcf-edit-tax' );
			    break;

		    case 'wpcf-view-tax':
			    $hook = wpcf_admin_add_submenu_page(
				    array(
					    'menu_title' => __( 'View Taxonomy', 'wpcf' ),
					    'function' => 'wpcf_admin_menu_edit_tax',
					    'capability' => WPCF_CUSTOM_TAXONOMY_VIEW
				    ),
				    $current_page
			    );
			    add_action( 'load-' . $hook, 'wpcf_admin_menu_edit_tax_hook' );
			    wpcf_admin_plugin_help( $hook, 'wpcf-edit-tax' );
			    break;

		    /**
		     * user meta fields
		     */
		    case 'wpcf-edit-usermeta':
			    $title = isset( $_GET['group_id'] ) ? __( 'Edit User Field Group', 'wpcf' ) : __( 'Add New User Field Group', 'wpcf' );
			    $hook  = wpcf_admin_add_submenu_page(
				    array(
					    'menu_title' => $title,
					    'function' => 'wpcf_admin_menu_edit_user_fields',
					    'capability' => WPCF_USER_META_FIELD_EDIT,
				    ),
				    $current_page
			    );
			    wpcf_admin_plugin_help( $hook, 'wpcf-edit-usermeta' );
			    break;

		    case 'wpcf-view-usermeta':
			    $hook = wpcf_admin_add_submenu_page(
				    array(
					    'menu_title' => __( 'View User Field Group', 'wpcf' ),
					    'function' => 'wpcf_admin_menu_edit_user_fields',
					    'capability' => WPCF_USER_META_FIELD_VIEW,
				    ),
				    $current_page
			    );
			    wpcf_admin_plugin_help( $hook, 'wpcf-edit-usermeta' );
			    break;

		    case WPCF_Page_Edit_Termmeta::PAGE_NAME:

				// Initialize the page.
			    /** @var WPCF_Page_Edit_Termmeta $termmeta_page */
				$termmeta_page = WPCF_Page_Edit_Termmeta::get_instance();
				$termmeta_page->initialize();

		        break;

		    case WPCF_Page_Control_Termmeta::PAGE_NAME:

				// Initialize by creating an instance.
				/** @var WPCF_Page_Control_Termmeta $term_field_control_page */
			    $term_field_control_page = WPCF_Page_Control_Termmeta::get_instance();
				$term_field_control_page->initialize();
			    break;

	    }

    }

    // Check if migration from other plugin is needed
    if (
        (class_exists( 'Acf') && !class_exists('acf_pro'))
        || defined( 'CPT_VERSION' ) 
    ) {
        $hook = add_submenu_page( 'wpcf', __( 'Migration', 'wpcf' ),
            __( 'Migration', 'wpcf' ), 'manage_options', 'wpcf-migration',
            'wpcf_admin_menu_migration' );
        add_action( 'load-' . $hook, 'wpcf_admin_menu_migration_hook' );
        wpcf_admin_plugin_help( $hook, 'wpcf-migration' );
    }

    do_action( 'wpcf_menu_plus' );

    // remove the repeating Types submenu
    remove_submenu_page( 'wpcf', 'wpcf' );
}

/**
 * Menu page hook.
 */
function wpcf_admin_menu_debug_information()
{
    require_once WPCF_EMBEDDED_ABSPATH.'/toolset/toolset-common/debug/debug-information.php';
}

/**
 * Types Dashboard
 */
function wpcf_admin_menu_summary_dashboard_hook()
{
    do_action( 'wpcf_admin_page_init' );
    require_once WPCF_INC_ABSPATH.'/classes/class.types.dashboard.php';
    $wpcf_admin = new Types_Dashboard();
    $form = $wpcf_admin->form();
    wpcf_form( 'wpcf_form_dashboard', $form );
}

function wpcf_admin_menu_summary_dashboard()
{
    $post_type = current_filter();
    wpcf_add_admin_header( __( 'Dashboard', 'wpcf' ));
    $form = wpcf_form( 'wpcf_form_dashboard' );
    $form_output = $form->renderForm();
    wpcf_admin_screen($post_type, $form_output);
}

/**
 * Menu page hook.
 */
function wpcf_usermeta_summary_hook()
{
    do_action( 'wpcf_admin_page_init' );
    wpcf_admin_load_collapsible();
    wpcf_admin_page_add_options('uf',  __( 'User Fields', 'wpcf' ));
}

/**
 * Menu page hook.
 */
function wpcf_admin_menu_summary_hook()
{
    do_action( 'wpcf_admin_page_init' );
    wpcf_admin_load_collapsible();
    wpcf_admin_page_add_options('cf',  __( 'Post Fields', 'wpcf' ));
}

/**
 * Menu page display.
 */
function wpcf_admin_menu_summary()
{
    wpcf_add_admin_header( __( 'Post Field Groups', 'wpcf' ), array('page'=>'wpcf-edit'));
    require_once WPCF_INC_ABSPATH . '/fields.php';
    require_once WPCF_INC_ABSPATH . '/fields-list.php';
    $to_display = wpcf_admin_fields_get_fields();
    if ( !empty( $to_display ) ) {
        add_action( 'wpcf_groups_list_table_after', 'wpcf_admin_promotional_text' );
    }
    wpcf_admin_fields_list();
    wpcf_add_admin_footer();
}


function wpcf_admin_enqueue_group_edit_page_assets() {
	do_action( 'wpcf_admin_page_init' );

	/*
	 * Enqueue scripts
	 */
	// Group filter
	wp_enqueue_script( 'wpcf-filter-js',
		WPCF_EMBEDDED_RES_RELPATH
		. '/js/custom-fields-form-filter.js', array('jquery'), WPCF_VERSION );
	// Form
	wp_enqueue_script( 'wpcf-form-validation',
		WPCF_EMBEDDED_RES_RELPATH . '/js/'
		. 'jquery-form-validation/jquery.validate.min.js', array('jquery'),
		WPCF_VERSION );
	wp_enqueue_script( 'wpcf-form-validation-additional',
		WPCF_EMBEDDED_RES_RELPATH . '/js/'
		. 'jquery-form-validation/additional-methods.min.js',
		array('jquery'), WPCF_VERSION );
	// Scroll
	wp_enqueue_script( 'wpcf-scrollbar',
		WPCF_EMBEDDED_RELPATH . '/toolset/toolset-common/visual-editor/res/js/scrollbar.js',
		array('jquery') );
	wp_enqueue_script( 'wpcf-mousewheel',
		WPCF_EMBEDDED_RELPATH . '/toolset/toolset-common/visual-editor/res/js/mousewheel.js',
		array('wpcf-scrollbar') );
	// MAIN
	wp_enqueue_script(
		'wpcf-fields-form',
		WPCF_EMBEDDED_RES_RELPATH.'/js/fields-form.js',
		array( 'wpcf-js' ),
		WPCF_VERSION
	);
	wp_enqueue_script(
		'wpcf-admin-fields-form',
		WPCF_RES_RELPATH.'/js/fields-form.js',
		array(),
		WPCF_VERSION
	);

	/*
	 * Enqueue styles
	 */
	wp_enqueue_style( 'wpcf-scroll',
		WPCF_EMBEDDED_RELPATH . '/toolset/toolset-common/visual-editor/res/css/scroll.css' );

	//Css editor
	wp_enqueue_script( 'wpcf-form-codemirror' ,
		WPCF_RELPATH . '/resources/js/codemirror234/lib/codemirror.js', array('wpcf-js'));
	wp_enqueue_script( 'wpcf-form-codemirror-css-editor' ,
		WPCF_RELPATH . '/resources/js/codemirror234/mode/css/css.js', array('wpcf-js'));
	wp_enqueue_script( 'wpcf-form-codemirror-html-editor' ,
		WPCF_RELPATH . '/resources/js/codemirror234/mode/xml/xml.js', array('wpcf-js'));
	wp_enqueue_script( 'wpcf-form-codemirror-html-editor2' ,
		WPCF_RELPATH . '/resources/js/codemirror234/mode/htmlmixed/htmlmixed.js', array('wpcf-js'));
	wp_enqueue_script( 'wpcf-form-codemirror-editor-resize' ,
		WPCF_RELPATH . '/resources/js/jquery_ui/jquery.ui.resizable.min.js', array('wpcf-js'));

	wp_enqueue_style( 'wpcf-css-editor',
		WPCF_RELPATH . '/resources/js/codemirror234/lib/codemirror.css' );
	//wp_enqueue_style( 'wpcf-css-editor-resize',
	//        WPCF_RELPATH . '/resources/js/jquery_ui/jquery.ui.theme.min.css' );
	wp_enqueue_style( 'wpcf-usermeta',
		WPCF_EMBEDDED_RES_RELPATH . '/css/usermeta.css' );

	wp_enqueue_style( 'font-awesome' );

	add_action( 'admin_footer', 'wpcf_admin_fields_form_js_validation' );

}


/**
 * Menu page hook.
 */
function wpcf_admin_menu_edit_fields_hook()
{
	wpcf_admin_enqueue_group_edit_page_assets();

    require_once WPCF_INC_ABSPATH . '/fields.php';
    require_once WPCF_INC_ABSPATH . '/fields-form.php';
//    $form = wpcf_admin_fields_form();
    //require_once WPCF_INC_ABSPATH.'/classes/class.types.admin.edit.custom.fields.group.php';
    $wpcf_admin = new Types_Admin_Edit_Custom_Fields_Group();
    $wpcf_admin->init_admin();
    $form = $wpcf_admin->form();
    wpcf_form( 'wpcf_form_fields', $form );
}

/**
 * Menu page display.
 */
function wpcf_admin_menu_edit_fields()
{
    $add_new = false;
    $post_type = current_filter();
    $title = __('View Post Field Group', 'wpcf');
    if ( isset( $_GET['group_id'] ) ) {
        if ( WPCF_Roles::user_can_edit('custom-field', array('id' => $_GET['group_id']))) {
            $title = __( 'Edit Post Field Group', 'wpcf' );
            $add_new = array(
                'page' => 'wpcf-edit',
            );
        }
    } else if ( WPCF_Roles::user_can_create('custom-field')) {
        $title = __( 'Add New Post Field Group', 'wpcf' );
    }
    wpcf_add_admin_header( $title, $add_new );
    wpcf_wpml_warning();
    $form = wpcf_form( 'wpcf_form_fields' );
    echo '<form method="post" action="" class="wpcf-fields-form wpcf-form-validate js-types-show-modal">';
    wpcf_admin_screen($post_type, $form->renderForm());
    echo '</form>';
    wpcf_add_admin_footer();
}

function wpcf_admin_page_add_options( $name, $label)
{
    $option = 'per_page';
    $args = array(
        'label' => $label,
        'default' => 10,
        'option' => sprintf('wpcf_%s_%s', $name, $option),
    );
    add_screen_option( $option, $args );
}

function wpcf_admin_menu_summary_cpt_ctt_hook()
{
    do_action( 'wpcf_admin_page_init' );
    wp_enqueue_style( 'wpcf-promo-tabs', WPCF_RES_RELPATH . '/css/tabs.css', array(), WPCF_VERSION );
    wpcf_admin_load_collapsible();
    require_once WPCF_INC_ABSPATH . '/custom-types.php';
    require_once WPCF_INC_ABSPATH . '/custom-taxonomies.php';
    require_once WPCF_INC_ABSPATH . '/custom-types-taxonomies-list.php';
}

/**
 * Menu page hook.
 */
function wpcf_admin_menu_summary_cpt_hook()
{
    wpcf_admin_menu_summary_cpt_ctt_hook();
    wpcf_admin_page_add_options('cpt',  __( 'Post Types', 'wpcf' ));
}

/**
 * Menu page display.
 */
function wpcf_admin_menu_summary_cpt()
{
    wpcf_add_admin_header(
        __( 'Post Types', 'wpcf' ),
        array('page'=>'wpcf-edit-type'),
        __('Add New', 'wpcf')
    );
    $to_display_posts = get_option( WPCF_OPTION_NAME_CUSTOM_TYPES, array() );
    $to_display_tax = get_option( WPCF_OPTION_NAME_CUSTOM_TAXONOMIES, array() );
    if ( !empty( $to_display_posts ) || !empty( $to_display_tax ) ) {
        add_action( 'wpcf_types_tax_list_table_after', 'wpcf_admin_promotional_text' );
    }
    wpcf_admin_custom_post_types_list();
    wpcf_add_admin_footer();
}

/**
 * Menu page hook.
 */
function wpcf_admin_menu_summary_ctt_hook()
{
    wpcf_admin_menu_summary_cpt_ctt_hook();
    wpcf_admin_page_add_options('ctt',  __( 'Taxonomies', 'wpcf' ));
}

/**
 * Menu page display.
 */
function wpcf_admin_menu_summary_ctt()
{
    wpcf_add_admin_header( __( 'Taxonomies', 'wpcf' ), array('page' => 'wpcf-edit-tax') );
    wpcf_admin_custom_taxonomies_list();
    do_action('wpcf_types_tax_list_table_after');
    wpcf_add_admin_footer();
}

/**
 * Menu page hook.
 */
function wpcf_admin_menu_edit_type_hook()
{
    require_once WPCF_INC_ABSPATH . '/fields.php';
    do_action( 'wpcf_admin_page_init' );
    require_once WPCF_EMBEDDED_INC_ABSPATH . '/custom-types.php';
    require_once WPCF_INC_ABSPATH . '/custom-types-form.php';
    require_once WPCF_INC_ABSPATH . '/post-relationship.php';
    wp_enqueue_script( 'wpcf-custom-types-form',
            WPCF_RES_RELPATH . '/js/'
            . 'custom-types-form.js', array('jquery', 'jquery-ui-dialog', 'jquery-masonry'), WPCF_VERSION );
    wp_enqueue_script( 'wpcf-form-validation',
            WPCF_RES_RELPATH . '/js/'
            . 'jquery-form-validation/jquery.validate.min.js', array('jquery'),
            WPCF_VERSION );
    wp_enqueue_script( 'wpcf-form-validation-additional',
            WPCF_RES_RELPATH . '/js/'
            . 'jquery-form-validation/additional-methods.min.js',
            array('jquery'), WPCF_VERSION );
    wp_enqueue_style('wp-jquery-ui-dialog');
    add_action( 'admin_footer', 'wpcf_admin_types_form_js_validation' );
    wpcf_post_relationship_init();
    /**
     * add form
     */
    //    $form = wpcf_admin_custom_types_form();
    require_once WPCF_INC_ABSPATH.'/classes/class.types.admin.edit.post.type.php';
    $wpcf_admin = new Types_Admin_Edit_Post_Type();
    $wpcf_admin->init_admin();
    $form = $wpcf_admin->form();
    wpcf_form( 'wpcf_form_types', $form );
}

/**
 * Menu page display.
 */
function wpcf_admin_menu_edit_type()
{
    $post_type = current_filter();
    $title = __('View Post Type', 'wpcf');
    if ( WPCF_Roles::user_can_edit('custom-post-type', array()) ) {
        if ( isset( $_GET['wpcf-post-type'] ) ) {
            $title = __( 'Edit Post Type', 'wpcf' );
            /**
             * add new CPT link
             */
            $title .= sprintf(
                '<a href="%s" class="add-new-h2">%s</a>',
                esc_url(add_query_arg( 'page', 'wpcf-edit-type', admin_url('admin.php'))),
                __('Add New', 'wpcf')
            );
        } else {
            $title = __( 'Add New Post Type', 'wpcf' );
        }
    }
    wpcf_add_admin_header( $title );
    wpcf_wpml_warning();
    $form = wpcf_form( 'wpcf_form_types' );
    echo '<form method="post" action="" class="wpcf-types-form wpcf-form-validate js-types-do-not-show-modal">';
    wpcf_admin_screen($post_type, $form->renderForm());
    echo '</form>';
    wpcf_add_admin_footer();
}

/**
 * Menu page hook.
 */
function wpcf_admin_menu_edit_tax_hook()
{
    do_action( 'wpcf_admin_page_init' );
    wp_enqueue_script( 'wpcf-form-validation',
            WPCF_RES_RELPATH . '/js/'
            . 'jquery-form-validation/jquery.validate.min.js', array('jquery'),
            WPCF_VERSION );
    wp_enqueue_script( 'wpcf-form-validation-additional',
            WPCF_RES_RELPATH . '/js/'
            . 'jquery-form-validation/additional-methods.min.js',
            array('jquery'), WPCF_VERSION );
    wp_enqueue_script( 'wpcf-taxonomy-form',
        WPCF_RES_RELPATH . '/js/'
        . 'taxonomy-form.js', array( 'jquery' ), WPCF_VERSION );
    add_action( 'admin_footer', 'wpcf_admin_tax_form_js_validation' );
    require_once WPCF_EMBEDDED_INC_ABSPATH . '/custom-taxonomies.php';
    require_once WPCF_INC_ABSPATH . '/custom-taxonomies-form.php';
//    $form = wpcf_admin_custom_taxonomies_form();
    require_once WPCF_INC_ABSPATH.'/classes/class.types.admin.edit.taxonomy.php';
    $wpcf_admin = new Types_Admin_Edit_Taxonomy();
    $wpcf_admin->init_admin();
    $form = $wpcf_admin->form();
    wpcf_form( 'wpcf_form_tax', $form );
}

/**
 * Menu page display.
 */
function wpcf_admin_menu_edit_tax()
{
    $post_type = current_filter();
    $title = __( 'View Taxonomy', 'wpcf' );
    $add_new = false;
    if ( WPCF_Roles::user_can_create('custom-taxonomy') ) {
        $title = __( 'Add New Taxonomy', 'wpcf' );
        if ( isset( $_GET['wpcf-tax'] ) ) {
            $title = __( 'Edit Taxonomy', 'wpcf' );
            $add_new = array('page' => 'wpcf-edit-tax' );
        }
    }
    wpcf_add_admin_header( $title, $add_new);
    wpcf_wpml_warning();
    $form = wpcf_form( 'wpcf_form_tax' );
    echo '<form method="post" action="" class="wpcf-tax-form wpcf-form-validate js-types-show-modal">';
    wpcf_admin_screen($post_type, $form->renderForm());
    echo '</form>';
    wpcf_add_admin_footer();
}

/**
 * Menu page hook.
 */
function wpcf_admin_menu_import_export_hook()
{
    do_action( 'wpcf_admin_page_init' );
    require_once WPCF_INC_ABSPATH . '/fields.php';
    require_once WPCF_INC_ABSPATH . '/import-export.php';
    if ( extension_loaded( 'simplexml' ) && isset( $_POST['export'] )
            && wp_verify_nonce( $_POST['_wpnonce'], 'wpcf_import' ) ) {
        wpcf_admin_export_data();
        die();
    }
}

/**
 * Menu page display.
 */
function wpcf_admin_menu_import_export()
{
    wpcf_add_admin_header( __( 'Import/Export', 'wpcf' ) );
    echo '<form method="post" action="" class="wpcf-import-export-form '
    . 'wpcf-form-validate" enctype="multipart/form-data">';
    echo wpcf_form_simple( wpcf_admin_import_export_form() );
    echo '</form>';
    wpcf_add_admin_footer();
}

/**
 * Menu page hook.
 */
function wpcf_admin_menu_custom_fields_control_hook()
{
    require_once WPCF_INC_ABSPATH . '/fields-control.php';
    wpcf_admin_menu_custom_fields_control_hook_helper();
}


/**
 * Menu page display.
 */
function wpcf_admin_menu_custom_fields_control()
{
    global $wpcf_control_table;
    wpcf_add_admin_header( __( 'Post Field Control', 'wpcf' ) );
    echo '<form method="post" action="" id="wpcf-custom-fields-control-form" class="wpcf-custom-fields-control-form wpcf-form-validate" enctype="multipart/form-data">';
    echo wpcf_admin_custom_fields_control_form( $wpcf_control_table );
    wp_nonce_field( 'custom_fields_control_bulk' );
    echo '</form>';
    wpcf_add_admin_footer();
}

/**
 * Menu page hook.
 */
function wpcf_admin_menu_migration_hook()
{
    do_action( 'wpcf_admin_page_init' );
    require_once WPCF_INC_ABSPATH . '/fields.php';
    require_once WPCF_INC_ABSPATH . '/custom-types.php';
    require_once WPCF_INC_ABSPATH . '/custom-taxonomies.php';
    require_once WPCF_INC_ABSPATH . '/migration.php';
    $form = wpcf_admin_migration_form();
    wpcf_form( 'wpcf_form_migration', $form );
}

/**
 * Menu page display.
 */
function wpcf_admin_menu_migration()
{
    wpcf_add_admin_header( __( 'Migration', 'wpcf' ) );
    echo '<form method="post" action="" id="wpcf-migration-form" class="wpcf-migration-form '
    . 'wpcf-form-validate" enctype="multipart/form-data">';
    $form = wpcf_form( 'wpcf_form_migration' );
    echo $form->renderForm();
    echo '</form>';
    wpcf_add_admin_footer();
}

/**
 * Menu page hook.
 */
function wpcf_admin_menu_settings_hook()
{
    do_action( 'wpcf_admin_page_init' );
    require_once WPCF_INC_ABSPATH . '/settings.php';
    $form = wpcf_admin_general_settings_form();
    wpcf_form( 'wpcf_form_general_settings', $form );
}

/**
 * Menu page display.
 */
function wpcf_admin_menu_settings()
{
    ob_start();
    wpcf_add_admin_header( __( 'Settings', 'wpcf' ) );
?>
<form method="post" action="" id="wpcf-general-settings-form" class="wpcf-settings-form wpcf-form-validate">
<?php

    $form = wpcf_form( 'wpcf_form_general_settings' );
    echo $form->renderForm();

?>
    </form>
<?php
    wpcf_add_admin_footer();

    echo ob_get_clean();
}

/**
 * Adds typical header on admin pages.
 *
 * @param string $title
 * @param string $icon_id Custom icon
 * @return string
 */
function wpcf_add_admin_header($title, $add_new = false, $add_new_title = false)
{
    echo '<div class="wrap">';
    echo '<h2>', $title;
    if ( !$add_new_title ) {
        $add_new_title = __('Add New', 'wpcf');
    }
    if ( is_array($add_new) && isset($add_new['page']) ) {
        $add_button = false;
        /**
         * check user can?
         */
        switch($add_new['page']) {
	        case 'wpcf-edit-type':
		        $add_button = WPCF_Roles::user_can_create( 'custom-post-type' );
		        break;
	        case 'wpcf-edit-tax':
		        $add_button = WPCF_Roles::user_can_create( 'custom-taxonomy' );
		        break;
	        case 'wpcf-edit':
		        $add_button = WPCF_Roles::user_can_create( 'custom-field' );
		        break;
	        case 'wpcf-edit-usermeta':
		        $add_button = WPCF_Roles::user_can_create( 'user-meta-field' );
		        break;
	        case WPCF_Page_Edit_Termmeta::PAGE_NAME:
		        $add_button = WPCF_Roles::user_can_create( 'term-field' );
		        break;
        }
        if ( $add_button ) {
            printf(
                ' <a href="%s" class="add-new-h2">%s</a>',
                esc_url(add_query_arg( $add_new, admin_url('admin.php'))),
                $add_new_title
            );
        }
    }
    echo '</h2>';
    $current_page = sanitize_text_field( $_GET['page'] );
    do_action( 'wpcf_admin_header' );
    do_action( 'wpcf_admin_header_' . $current_page );
}

/**
 * Adds footer on admin pages.
 *
 * <b>Strongly recomended</b> if wpcf_add_admin_header() is called before.
 * Otherwise invalid HTML formatting will occur.
 */
function wpcf_add_admin_footer()
{
    $current_page = sanitize_text_field( $_GET['page'] );
	do_action( 'wpcf_admin_footer_' . $current_page );
    do_action( 'wpcf_admin_footer' );
    echo '</div>';
}

/**
 * Returns HTML formatted 'widefat' table.
 *
 * @param type $ID
 * @param type $header
 * @param type $rows
 * @param type $empty_message
 */
function wpcf_admin_widefat_table( $ID, $header, $rows = array(), $empty_message = 'No results' )
{
    if ( 'No results' == $empty_message ) {
        $empty_message = __('No results', 'wpcf');
    }
    $head = '';
    $footer = '';
    foreach ( $header as $key => $value ) {
        $head .= '<th id="wpcf-table-' . $key . '">' . $value . '</th>' . "\r\n";
        $footer .= '<th>' . $value . '</th>' . "\r\n";
    }
    echo '<table id="' . $ID . '" class="widefat" cellspacing="0">
            <thead>
                <tr>
                  ' . $head . '
                </tr>
            </thead>
            <tfoot>
                <tr>
                  ' . $footer . '
                </tr>
            </tfoot>
            <tbody>
              ';
    $row = '';
    if ( empty( $rows ) ) {
        echo '<tr><td colspan="' . count( $header ) . '">' . $empty_message
        . '</td></tr>';
    } else {
        $i = 0;
        foreach ( $rows as $row ) {
            $classes = array();
            if ( $i++%2 ) {
                $classes[] =  'alternate';
            }
            if ( isset($row['status']) && 'inactive' == $row['status'] ) {
                $classes[] = sprintf('status-%s', $row['status']);
            };
            printf('<tr class="%s">', implode(' ', $classes ));
            foreach ( $row as $column_name => $column_value ) {
                if ( preg_match( '/^(status|raw_name)$/', $column_name )) {
                    continue;
                }
                echo '<td class="wpcf-table-column-' . $column_name . '">';
                echo $column_value;
                echo '</td>' . "\r\n";
            }
            echo '</tr>' . "\r\n";
        }
    }
    echo '
            </tbody>
          </table>' . "\r\n";
}

/**
 * Admin tabs.
 *
 * @param type $tabs
 * @param type $page
 * @param type $default
 * @param type $current
 * @return string
 */
function wpcf_admin_tabs($tabs, $page, $default = '', $current = '')
{
    if ( empty( $current ) && isset( $_GET['tab'] ) ) {
        $current = sanitize_text_field( $_GET['tab'] );
    } else {
        $current = $default;
    }
    $output = '<h2 class="nav-tab-wrapper">';
    foreach ( $tabs as $tab => $name ) {
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        $output .= "<a class='nav-tab$class' href='?page=$page&tab=$tab'>$name</a>";
    }
    $output .= '</h2>';
    return $output;
}

/**
 * Saves open fieldsets.
 *
 * @param type $action
 * @param type $fieldset
 */
function wpcf_admin_form_fieldset_save_toggle($action, $fieldset)
{
    $data = get_user_meta( get_current_user_id(), 'wpcf-form-fieldsets-toggle',
            true );
    if ( $action == 'open' ) {
        $data[$fieldset] = 1;
    } elseif ( $action == 'close' ) {
        unset( $data[$fieldset] );
    }
    update_user_meta( get_current_user_id(), 'wpcf-form-fieldsets-toggle', $data );
}

/**
 * Check if fieldset is saved as open.
 *
 * @param type $fieldset
 */
function wpcf_admin_form_fieldset_is_collapsed($fieldset)
{
    $data = get_user_meta( get_current_user_id(), 'wpcf-form-fieldsets-toggle',
            true );
    if ( empty( $data ) ) {
        return true;
    }
    return array_key_exists( $fieldset, $data ) ? false : true;
}

/**
 * Adds help on admin pages.
 *
 * @param type $contextual_help
 * @param type $screen_id
 * @param type $screen
 * @return type
 */
function wpcf_admin_plugin_help($hook, $page)
{
    global $wp_version;
    $call = false;
    $contextual_help = '';
    $page = $page;
    if ( isset( $page ) && isset( $_GET['page'] ) && $_GET['page'] == $page ) {
        switch ( $page ) {
            case 'wpcf-cf':
                $call = 'custom_fields';
                break;

            case 'wpcf-cpt':
                $call = 'post_types_list';
                break;

            case 'wpcf-ctt':
                $call = 'custom_taxonomies_list';
                break;

            case 'wpcf-import-export':
                $call = 'import_export';
                break;

            case 'wpcf-edit':
                $call = 'edit_group';
                break;

            case 'wpcf-edit-type':
                $call = 'edit_type';
                break;

            case 'wpcf-edit-tax':
                $call = 'edit_tax';
                break;

            case 'wpcf':
                $call = 'wpcf';
                break;

            case 'wpcf-um':
                $call = 'user_fields_list';
                break;

            case 'wpcf-edit-usermeta':
                $call = 'user_fields_edit';
                break;

            case 'wpcf-termmeta-listing':
                $call = 'term_fields_list';
                break;

	        case WPCF_Page_Edit_Termmeta::PAGE_NAME:
		        $call = 'edit_termmeta';
        }
    }
    if ( $call ) {
        require_once WPCF_ABSPATH . '/help.php';
        // WP 3.3 changes
        if ( version_compare( $wp_version, '3.2.1', '>' ) ) {
            wpcf_admin_help_add_tabs($call, $hook, $contextual_help);
        } else {
            $contextual_help = wpcf_admin_help( $call, $contextual_help );
            add_contextual_help( $hook, $contextual_help );
        }
    }
}

/**
 * Promo texts
 *
 * @todo Move!
 */
function wpcf_admin_promotional_text()
{
    $promo_tabs = get_option( '_wpcf_promo_tabs', false );
    // random selection every one hour
    if ( $promo_tabs ) {
        $time = time();
        $time_check = intval( $promo_tabs['time'] ) + 60 * 60;
        if ( $time > $time_check ) {
            $selected = mt_rand( 0, 3 );
            $promo_tabs['selected'] = $selected;
            $promo_tabs['time'] = $time;
            update_option( '_wpcf_promo_tabs', $promo_tabs );
        } else {
            $selected = $promo_tabs['selected'];
        }
    } else {
        $promo_tabs = array();
        $selected = mt_rand( 0, 3 );
        $promo_tabs['selected'] = $selected;
        $promo_tabs['time'] = time();
        update_option( '_wpcf_promo_tabs', $promo_tabs );
    }
}

/**
 * Collapsible scripts.
 */
function wpcf_admin_load_collapsible()
{
    wp_enqueue_script( 'wpcf-collapsible',
            WPCF_RES_RELPATH . '/js/collapsible.js', array('jquery'),
            WPCF_VERSION );
    wp_enqueue_style( 'wpcf-collapsible',
            WPCF_RES_RELPATH . '/css/collapsible.css', array(), WPCF_VERSION );
    $option = get_option( 'wpcf_toggle', array() );
    if ( !empty( $option ) ) {
        $setting = 'new Array("' . implode( '", "', array_keys( $option ) ) . '")';
        wpcf_admin_add_js_settings( 'wpcf_collapsed', $setting );
    }
}

/**
 * Various delete/deactivate content actions.
 *
 * @param type $type
 * @param type $arg
 * @param type $action
 */
function wpcf_admin_deactivate_content($type, $arg, $action = 'delete')
{
    switch ( $type ) {
        case 'post_type':
            // Clean tax relations
            if ( $action == 'delete' ) {
                $custom = get_option( WPCF_OPTION_NAME_CUSTOM_TAXONOMIES, array() );
                foreach ( $custom as $post_type => $data ) {
                    if ( empty( $data['supports'] ) ) {
                        continue;
                    }
                    if ( array_key_exists( $arg, $data['supports'] ) ) {
                        unset( $custom[$post_type]['supports'][$arg] );
                        $custom[$post_type][TOOLSET_EDIT_LAST] = time();
                    }
                }
                update_option( WPCF_OPTION_NAME_CUSTOM_TAXONOMIES, $custom );
            }
            break;

        case 'taxonomy':
            // Clean post relations
            if ( $action == 'delete' ) {
                $custom = get_option( WPCF_OPTION_NAME_CUSTOM_TYPES, array() );
                foreach ( $custom as $post_type => $data ) {
                    if ( empty( $data['taxonomies'] ) ) {
                        continue;
                    }
                    if ( array_key_exists( $arg, $data['taxonomies'] ) ) {
                        unset( $custom[$post_type]['taxonomies'][$arg] );
                        $custom[$post_type][TOOLSET_EDIT_LAST] = time();
                    }
                }
                update_option( WPCF_OPTION_NAME_CUSTOM_TYPES, $custom );
            }
            break;

        default:
            break;
    }
}

/**
 * Loads teasers.
 *
 * @param type $teasers
 */
function wpcf_admin_load_teasers($teasers)
{
    foreach ( $teasers as $teaser ) {
        $file = WPCF_ABSPATH . '/plus/' . $teaser;
        if ( file_exists( $file ) ) {
            require_once $file;
        }
    }
}

/**
 * Get temporary directory
 *
 * @return
 */

function wpcf_get_temporary_directory()
{
    $dir = sys_get_temp_dir();
    if ( !empty( $dir ) && is_dir( $dir ) && is_writable( $dir ) ) {
        return $dir;
    }
    $dir = wp_upload_dir();
    $dir = $dir['basedir'];
    return $dir;
}

/**
 * add types configuration to debug
 */

function wpcf_get_extra_debug_info($extra_debug)
{
    $extra_debug['types'] = wpcf_get_settings();
    return $extra_debug;
}

add_filter( 'icl_get_extra_debug_info', 'wpcf_get_extra_debug_info' );

function wpcf_admin_add_submenu_page($menu, $menu_slug = null, $menu_parent = 'wpcf')
{
    if ( !is_admin() ) {
        return;
    }
    $menu_slug = array_key_exists('menu_slug', $menu)? $menu['menu_slug']:$menu_slug;

    $capability = array_key_exists('capability', $menu)? $menu['capability']:'manage_options';;
    $wpcf_capability = apply_filters( 'wpcf_capability', $capability, $menu, $menu_slug );
    $wpcf_capability = apply_filters( 'wpcf_capability'.$menu_slug, $capability, $menu, $menu_slug );

    /**
     * allow change capability  by filter
     * full list https://goo.gl/OJYTvl
     */
    if ( isset($menu['capability_filter'] ) ) {
        $wpcf_capability = apply_filters( $menu['capability_filter'], $wpcf_capability, $menu, $menu_slug );
    }

    /**
     * add submenu
     */
    $hook = add_submenu_page(
        $menu_parent,
        isset($menu['page_title'])? $menu['page_title']:$menu['menu_title'],
        $menu['menu_title'],
        $wpcf_capability,
        $menu_slug,
        array_key_exists('function', $menu)? $menu['function']:null
    );

	// For given menu slug, publish the final hook name in case we need it somewhere.
	do_action( "wpcf_admin_add_submenu_page_$menu_slug", $hook );

    if ( !empty($menu_slug) ) {
        wpcf_admin_plugin_help( $hook, $menu_slug );
    }
    /**
     * add action
     */
    if ( !array_key_exists('load_hook', $menu) && array_key_exists('function', $menu) && is_string( $menu['function' ] ) ) {
        $menu['load_hook'] = sprintf( '%s_hook', $menu['function'] );
    }
    if ( !empty($menu['load_hook']) && function_exists( $menu['load_hook'] ) ) {
        $action = sprintf(
            'load-%s',
            array_key_exists('hook', $menu)? $menu['hook']:$hook
        );
        add_action( $action, $menu['load_hook'] );
    }
    /**
     * add submenu to submenu
     */
    if ( array_key_exists('submenu', $menu) ) {
        foreach( $menu['submenu'] as $submenu_slug => $submenu ) {
            wpcf_admin_add_submenu_page($submenu, $submenu_slug, $hook);
        }
    }
    return $hook;
}

/**
 * sort helper for tables
 */
function wpcf_usort_reorder($a,$b)
{
    $orderby = (!empty($_REQUEST['orderby'])) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'title'; //If no sort, default to title
    $order = (!empty($_REQUEST['order'])) ? sanitize_text_field( $_REQUEST['order'] ) : 'asc'; //If no order, default to asc
    if ( ! in_array( $order, array( 'asc', 'desc' ) ) ) {
        $order = 'asc';
    }
    if ('title' == $orderby || !isset($a[$orderby])) {
        $orderby = 'slug';
    }
    /**
     * sort by slug if sort field is the same
     */
    if ( $a[$orderby] == $b[$orderby] ) {
        $orderby = 'slug';
    }
    $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
    return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
}

add_filter('set-screen-option', 'wpcf_table_set_option', 10, 3);
function wpcf_table_set_option($status, $option, $value)
{
      return $value;
}

function wpcf_admin_screen( $post_type, $form_output = '')
{
?>
<div id="poststuff">
    <div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">
<?php echo $form_output; ?>
        <div id="postbox-container-1" class="postbox-container">
            <?php do_meta_boxes($post_type, 'side', null); ?>
        </div>
        <div id="postbox-container-2" class="postbox-container">
<?php
    do_meta_boxes($post_type, 'normal', null);
    do_meta_boxes($post_type, 'advanced', null);
?>
        </div>
    </div>
</div>
<?php
}

/**
 * Add Usermeta Fields manager page.
 *
 * @author Gen gen.i@icanlocalize.com
 * @since Types 1.3
 */
function wpcf_admin_menu_user_fields_control_hook()
{
    require_once WPCF_INC_ABSPATH . '/usermeta-control.php';
    wpcf_admin_menu_user_fields_control_hook_helper();
}

/**
 * Menu page display.
 */
function wpcf_admin_menu_user_fields_control() {
    global $wpcf_control_table;
    wpcf_add_admin_header( __( 'User Field Control', 'wpcf' ) );
    echo '<form method="post" action="" id="wpcf-custom-fields-control-form" class="wpcf-custom-fields-control-form '
    . 'wpcf-form-validate" enctype="multipart/form-data">';
    echo wpcf_admin_custom_fields_control_form( $wpcf_control_table );
    wp_nonce_field( 'user_fields_control_bulk' );
    echo '</form>';
    wpcf_add_admin_footer();
}


/* Delete this with release of 2.0 as it's than fixed in toolset-common */
function types_670() { ?>
	<script type="text/javascript">
		function typesCheckTrigger(trigger, formID)
		{
			var $ = jQuery;
			$trigger = $('[data-wpt-name="' + trigger + '"]', formID)

			if ($('body').hasClass('wp-admin')) {
				trigger = trigger.replace(/wpcf\-/, 'wpcf[') + ']';
				$trigger = $('[data-wpt-name="' + trigger + '"]', formID);
			}

			if ($trigger.length < 1) {
				$trigger = $('[data-wpt-name="' + trigger + '[skypename]"]', formID);
			}

			if ($trigger.length < 1) {
				$trigger = $('[data-wpt-name="' + trigger + '[datepicker]"]', formID);
			}

			if ($trigger.length < 1) {
				$trigger = $('[data-wpt-name="' + trigger + '[]"]', formID);
			}

			if ($trigger.length > 0 && 'option' == $trigger.data('wpt-type')) {
				$trigger = $trigger.parent();
			}

			if ($trigger.length < 1) {
				if( trigger.indexOf( 'cred-' ) == -1 )
					$trigger = typesCheckTrigger('cred-' + trigger, formID);


				return false;
			}
			return $trigger;
		}

		if( typeof wptCondTriggers !== 'undefined' ) {
			_.each(wptCondTriggers, function (triggers, formID) {
				_.each(triggers, function (fields, trigger) {
					if( ! typesCheckTrigger(trigger, formID, trigger) ) {
						delete wptCondTriggers[formID][trigger];
						if( typeof wptCondFields !== 'undefined' ) {
							_.each( wptCondFields, function( fields, pageID ) {
								_.each( fields, function( field, fieldKey ) {
									_.each( field[ 'conditions' ], function( condition, conditionKey ) {
										if( condition[ 'id' ] == trigger ) {
											delete wptCondFields[ pageID ][ fieldKey ][ 'conditions' ][ conditionKey ];
										}
									} )
								} );
							} );
						}
					}
				});
			});
		}

		if( typeof wptCondCustomTriggers !== 'undefined' ) {
			_.each( wptCondCustomTriggers, function( triggers, formID ) {
				_.each( triggers, function( fields, trigger ) {
					if( !typesCheckTrigger( trigger, formID, trigger ) ) {
						delete wptCondCustomTriggers[ formID ][ trigger ];
					}
				} );
			} );
		}

	</script>
	<?php
}

add_action( 'admin_print_footer_scripts', 'types_670', 100 );