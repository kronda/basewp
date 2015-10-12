<?php

/**
 * Content template editor
 *
 * This is a starting point for new (1.9) CT edit page. All files or assets relevant to main functionality
 * should be referenced here.
 *
 * The page is rendered by wpv_ct_editor_page() with help of few filters and actions (described below).
 *
 * CT edit page expects that it's URL is "admin.php?page={$WPV_CT_EDITOR_PAGE_NAME}".
 *
 * @since 1.9
 */


/*
 * Include additional Content Template Editor-related files.
 *
 * All files within inc/ct-editor should be included here.
 */

$wpv_ct_editor_required_files = array(
    'section-content.php',
    'section-module-manager.php',
    'section-settings.php',
    'section-title.php',
	'section-usage.php'
);

foreach( $wpv_ct_editor_required_files as $required_file ) {
    require_once plugin_dir_path( __FILE__ ) . $required_file;
}


/**
 * CT editor page name.
 *
 * This is the only place where this string should be hardcoded. Use the constant!
 *
 * @since 1.9
 */
define( 'WPV_CT_EDITOR_PAGE_NAME', 'ct-editor' );


add_action( 'admin_init', 'wpv_ct_editor_init' );

/**
 * Main init hook for the CT edit screen.
 *
 * @since 1.9
 */
function wpv_ct_editor_init() {
    global $pagenow;
    $page = wpv_getget( 'page' );

    // Force add the Types groups for fields and usermeta fields to the Fields and Views popup.
    if( 'admin.php' == $pagenow && WPV_CT_EDITOR_PAGE_NAME == $page ) {

        add_filter( 'wpcf_filter_force_include_types_fields_on_views_dialog', '__return_true' );

        // By now, the editor scripts should be registered, but we'll check just to be sure.
        // @todo Changes in common should make those available as dependencies, since they all are registered on init
        if ( ! wp_script_is( 'editor_addon_menu', 'registered ') ) {
            wp_register_style('editor_addon_menu', EDITOR_ADDON_RELPATH . '/res/css/pro_dropdown_2.css');
        }
        if ( ! wp_script_is( 'editor_addon_menu', 'registered ') ) {
            wp_register_style('editor_addon_menu_scroll', EDITOR_ADDON_RELPATH . '/res/css/scroll.css');
        }
        if ( ! wp_script_is( 'editor_addon_menu', 'registered ') ) {
            wp_register_script( 'icl_editor-script', EDITOR_ADDON_RELPATH . '/res/js/icl_editor_addon_plugin.js', array( 'jquery', 'quicktags', 'wplink' ) );
        }
        if ( ! wp_script_is( 'editor_addon_menu', 'registered ') ) {
            wp_register_script( 'icl_media-manager-js', EDITOR_ADDON_RELPATH . '/res/js/icl_media_manager.js', array( 'jquery', 'icl_editor-script' ) );
        }

        // Register main CT editor script
        wp_register_script(
            'views-ct-editor-js',
            WPV_URL . "/res/js/ct-editor.js",
            array(
                'jquery',
                'wp-pointer',
                'underscore',
                'views-utils-script',
                'views-codemirror-conf-script',
                'knockout3',
                'icl_editor-script',
                'icl_media-manager-js',
                'quicktags',
                'wplink',
                'toolset-utils',
                'views-ct-dialogs-js',
                'toolset-uri-js'
            ),
            WPV_VERSION,
            true
        );

        /**
         * Gather localization data for the main script.
         *
         * Individual sections that need some l10n are supposed to hook onto this filter and append their
         * localized strings for the wpv-ct-editor-js script.
         *
         * @param array $l10n_data Localization data. Each section is supposed to add an element identified by their
         *     unique slug/name and put everything inside (it can be anything that can be encoded into JSON).
         *
         * @since 1.9
         */
        $l10n_data = apply_filters( 'wpv_ct_editor_localize_script', array() );
        wp_localize_script( 'views-ct-editor-js', 'wpv_ct_editor_l10n', $l10n_data );

    }
}


// Note: Submenu entry for the Edit page is added in WP_Views_Plugin::admin_menu().


add_action( 'admin_enqueue_scripts', 'wpv_ct_editor_enqueue' );


/**
 * Register and enqueue assets for the CT edit page. Localize main script.
 *
 * This hook has lower than default priority, so we expect all the common Toolset and Views assets to be
 * already registered. No other assets should depend on this hook being executed, it's a leaf on the dependency tree.
 *
 * Main script is being registered here: wpv-ct-editor-js, located in /res/js/redesign/ct-editor.js.
 *
 * @since 1.9
 */
function wpv_ct_editor_enqueue() {
    global $pagenow;
    $page = wpv_getget( 'page' );

    // Enqueue only if we're on the right page.
    if ( 'admin.php' == $pagenow && WPV_CT_EDITOR_PAGE_NAME == $page ) {

        // We will have the "Add media" button.
        wp_enqueue_media();
        wp_enqueue_script( 'views-ct-editor-js' );

        wp_enqueue_style( 'views-admin-css' );
		wp_enqueue_style( 'views-codemirror-css' );

        // icl_editor (CodeMirror) styles
		// @todo 'views-admin-css' wil also be dependant of the common 'editor_addon_menu' and 'editor_addon_menu_scroll'
        wp_enqueue_style( 'editor_addon_menu' );
        wp_enqueue_style( 'editor_addon_menu_scroll' );

    }
}


/**
 * CT editor page handler.
 *
 * Based on the 'action' GET parameter, either create a new CT and show the edit page for it,
 * or show the edit page for an existing CT.
 *
 * For the 'create' action, following GET parameters are expected:
 * - title: Title of the new Content Template.
 * - usage: An associative array that can contains keys "single_post_types", "post_archives" and
 *   "taxonomy_archives" (others will be ignored) with arrays of post type or taxonomy slugs where this Content
 *   Template should be used. Only existing slugs are allowed (see WPV_Content_Template::_set_assigned_* methods).
 *
 * @since 1.9
 */
function wpv_ct_editor_page() {

    if( !current_user_can( 'manage_options' ) ) {
        wpv_die_toolset_alert_error( __( 'You have no permission to acces this page.', 'wpv-views' ) );
    }

    $action = wpv_getget( 'action', 'edit', array( 'edit', 'create' ) );

    switch( $action ) {

        // show edit page
        case 'edit':

            $ct_id = (int) wpv_getget( 'ct_id' );
            wpv_ct_editor_page_edit( $ct_id );
            break;

        // create a new content template and continue to edit page on success.
        case 'create':

            $title = urldecode( wpv_getget( 'title' ) );

            $usage = wpv_getget( 'usage' );
            if( !is_array( $usage ) ) {
                $usage = array();
            }

            $ct = wpv_ct_editor_page_create( $title, $usage );
            if( $ct instanceof WPV_Content_Template ) {
                wpv_ct_editor_page_edit( $ct );
            } else {
                wpv_die_toolset_alert_error( __( 'An error ocurred while creating a new Content Template.', 'wpv-views' ) );
            }

            break;
    }

}


/**
 * Handle creating a new Content Template with given parameters.
 *
 * @param string $title Title for the CT.
 * @param array $usage See wpv_ct_editor_page() description.
 * @return null|WPV_Content_Template A CT object or null if the creation has failed.
 *
 * @since 1.9
 */
function wpv_ct_editor_page_create( $title, $usage ) {

    // Create new Content Template
    $ct = WPV_Content_Template::create( $title );

    if( null == $ct ) {
        return null;
    }

    // Process the assignments to post types and taxonomies
    $single_post_types_assigned = wpv_ct_editor_assign_usage( $ct, 'single_post_types', $usage );
    $post_archives_assigned = wpv_ct_editor_assign_usage( $ct, 'post_archives', $usage );
    $taxonomy_archives_assigned = wpv_ct_editor_assign_usage( $ct, 'taxonomy_archives', $usage );

    if( !$single_post_types_assigned || !$post_archives_assigned || !$taxonomy_archives_assigned ) {
        return null;
    }

    return $ct;
}


/**
 * Safely process assignment (setting the usage) of a Content Template.
 *
 * @param WPV_Content_Template $ct
 * @param string $assignment_type One of three possible assignment types: 'single_post_types', 'post_archives'
 *     or 'taxonomy_archives'.
 * @param $usage Array of existing post type or taxonomy slugs where this CT should be assigned.
 *
 * @return bool True on success, false on failure.
 *
 * @since 1.9
 */
function wpv_ct_editor_assign_usage( $ct, $assignment_type, $usage ) {

    $selected_items = wpv_getarr( $usage, $assignment_type, null );
    if( is_array( $selected_items ) ) {
        try {
            $property_name = 'assigned_' . $assignment_type;
            $ct->$property_name = $selected_items;
            return true;
        } catch( Exception $e ) {
            return false;
        }
    } else {
        return true;
    }

}


/**
 * Render the editor page.
 *
 * Renders the individual sections, action bar with "Save all sections" button, collects Content Template properties
 * required by the sections (as a value of #js-wpv-ct) and creates a renders nonce for updating properties
 * ("wpv_ct_{$ct->id}_update_properties_by_{$uid}" stored as a value of #js-wpv-ct-update-nonce) for the main JS script.
 *
 * @param WPV_Content_Template|int Content Template object or ID.
 *
 * @since 1.9
 */
function wpv_ct_editor_page_edit( $ct ) {

    // Get the Content Template
    if( ! $ct instanceof WPV_Content_Template ) {
        $ct = WPV_Content_Template::get_instance($ct);
        if (null == $ct) {
            wpv_die_toolset_alert_error(__('You attempted to edit a Content Template that doesn&#8217;t exist. Perhaps it was deleted?', 'wpv-views'));
        }
    }

    // Do not allow editing trashed CTs
    if( 'trash' == $ct->post_status ) {
        wpv_die_toolset_alert_error( __( 'You can’t edit this Content Template because it is in the Trash. Please restore it and try again.', 'wpv-views' ) );
    }

    // Don't allow to edit CT translations
    if( !apply_filters('wpml_is_original_content', true, $ct->post()->ID, 'post_' . $ct->post()->post_type) ) {
        wpv_die_toolset_alert_error( __( 'You are trying to edit a Content Template translation. Only original laguage can be edited here. Please edit the translation through WPML Translation Management.', 'wpv-views' ) );
    }

    // Wrapper for the edit page
    echo '<div class="wrap toolset-views">';

    // Site title
    printf( '<h2>%s</h2>', __( 'Edit Content Template', 'wpv-views' ) );

    wpv_ct_editor_render_save_all_bar();


    // Gather Content Template properties and pass them as l10n to JS.

    /**
     * Gather names of Content Template properties that should be passed as a JSON to the main JS script.
     *
     * @param array $property_names Array of property names that can be retrieved from an instance of
     *     WPV_Content_Template. If CT throws an exception while getting the property, null will be passed.
     *
     * @since 1.9
     */
    $requested_property_names = array_unique( apply_filters( 'wpv_ct_editor_request_properties', array() ) );

    // Retrieve the requested properties into $ct_data.
    $ct_data = array( 'id' => $ct->id );
    foreach( $requested_property_names as $property_name ) {
        try {
            $ct_data[ $property_name ] = $ct->$property_name;
        } catch( Exception $e ) {
            $ct_data[ $property_name ] = null;
        }
    }

    // Add nonce for updating properties
    $uid = get_current_user_id();
    $ct_data['update_nonce'] = wp_create_nonce( "wpv_ct_{$ct->id}_update_properties_by_{$uid}" );
    $ct_data['trash_nonce'] = wp_create_nonce( 'wpv_view_listing_actions_nonce' );

    $ct_data['listing_page_url'] = esc_url( add_query_arg( array( 'page' => 'view-templates' ), admin_url( 'admin.php' ) ) );

    /**
     * Allow individual sections to attach custom data to ct_data.
     *
     * @param array $ct_data Associative array with CT properties (keys are property names, obviously) or other custom
     *     data attached by other page sections. Each section should choose keys that minimize the risk of conflict (e.g
     *     prepend it by "_{$section_slug}_", etc.).
     * @param WPV_Content_Template $ct Content Template to be edited.
     *
     * @since 1.9
     */
    $ct_data = apply_filters( 'wpv_ct_editor_add_custom_properties', $ct_data, $ct );

    // Pass CT data as l10n variable.
    wp_localize_script( 'views-ct-editor-js', 'wpv_ct_editor_ct_data', $ct_data );

    /**
     * Render individual sections.
     *
     * Each section is supposed to hook onto this action and at some point render it's content by
     * calling wpv_ct_editor_render_section().
     *
     * @since 1.9
     */
    do_action( 'wpv_ct_editor_sections', $ct );

    // Render HTML template for the Insert/Edit link native WP dialog.
	if ( ! class_exists( '_WP_Editors' ) ) {
		require( ABSPATH . WPINC . '/class-wp-editor.php' );
	}
	_WP_Editors::wp_link_dialog();
	
    // Wrapper end
    echo '</div>';
}


/**
 * Render CT editor section.
 *
 * All sections should use this method for rendering their final output, in order to reduce code redundancy.
 *
 * @param string $section_title Title of the section
 * @param string $class Class name to be added to div.wpv-settings-section, e.g. selector that's used by jQuery.
 * @param string $content HTML content of the section.
 * @param bool $wide_container If true, this section's container (main content) will get more space. This is
 *     meant mainly for sections with CodeMirror editors.
 * @param string $container_class Additional class(es) for the 'wpv-setting-container' div.
 * @param string $setting_class Additional class(es) for the 'wpv-setting' div.
 *
 * @since 1.9
 */
function wpv_ct_editor_render_section( $section_title, $class, $content, $wide_container = false, $container_class = '', $setting_class = '',
        $pointer_args = null )
{
    $container_class = $wide_container ? "$container_class wpv-setting-container-horizontal" : $container_class;

    $pointer = '';
    if( is_array( $pointer_args ) ) {
        $pointer_section = wpv_getarr( $pointer_args, 'section', null );
        $pointer_slug = wpv_getarr( $pointer_args, 'pointer_slug', null );
        if( null != $pointer_section && null != $pointer_slug ) {
            $pointer = sprintf(
                ' <i class="icon-question-sign js-wpv-show-pointer" data-section="%s" data-pointer-slug="%s"></i>',
                esc_attr( $pointer_section ),
                esc_attr( $pointer_slug )
            );
        }
    }

	?>

	<div class="wpv-settings-section <?php echo $class; ?> hidden">
		<div class="wpv-setting-container <?php echo $container_class; ?>">
			<div class="wpv-settings-header">
				<h3><?php echo $section_title . $pointer ?></h3>
			</div>
			<div class="wpv-setting <?php echo $setting_class; ?>">
				<?php echo $content; ?>
			</div>
		</div>
	</div>

	<?php
}


/**
 * Render the action bar with the "Save all sections at once" button.
 *
 * @since 1.9
 */
function wpv_ct_editor_render_save_all_bar() {
    ?>
    <div id="js-wpv-general-actions-bar" class="wpv-settings-save-all wpv-general-actions-bar wpv-setting-container js-wpv-general-actions-bar">
        <p class="update-button-wrap">
            <button class="button-secondary button button-large"
                    data-bind="enable: isSaveAllButtonEnabled,
                    attr: { class: isSaveAllButtonEnabled() ? 'button-primary' : 'button-secondary' },
                    click: saveAllProperties">
                <?php _e( 'Save all sections at once', 'wpv-views' ); ?>
            </button>
        </p>
        <span class="wpv-message-container js-wpv-message-container"></span>
    </div>
<?php

}


add_filter( 'wpv_ct_editor_localize_script', 'wpv_ct_editor_general_localize_script' );

/**
 * Add general CT edit page localizations for the main JS script.
 *
 * Slug "editor" is used.
 *
 * @param array $l10n_data Localization data
 * @return array Updated localization data.
 *
 * @since 1.9
 */
function wpv_ct_editor_general_localize_script( $l10n_data ) {
    $l10n_data['editor'] = array(
        'saved' => __( 'Content template saved', 'wpv-views' ),
        'unsaved' => __( 'Content template not saved', 'wpv-views' ),
        'pending_changes' => __( 'There are some unsaved changes.'. 'wpv-views' ),
        'confirm_unload' => __( 'You have entered new data on this page.', 'wpv-views' ),
        'pointer_close' => __( 'Close', 'wpv-views' )
    );
    return $l10n_data;
}


add_action( 'wp_ajax_wpv_ct_update_properties', 'wpv_ct_update_properties_callback' );

/**
 * Update one or more properties of a Content Template.
 *
 * Note: I've put this into ct-editor.php instead of wpv-admin-ajax.php, because it will most probably
 * be used *only* by Content Template edit page. No need to further bloat that file with single-purpose
 * call handlers. If the usage should change in the future, just move the code to a more appropriate place.
 *     --Jan
 *
 * Following POST parameters are expected:
 * - id: Content Template ID
 * - wpnonce: A valid wpv_ct_{$id}_update_properties_by_{$user_id} nonce.
 * - properties: An array of objects (that will be decoded from JSON to associative arrays),
 *     each of them representing a property with "name" and "value" keys.
 *
 * A WPV_Content_Template object will be instantiated and this function will try to update values of
 * it's properties as defined in the "properties" POST parameter. The "update transaction" mechansim
 * is used for this purpose (see WPV_Post_Object_Wrapper::update_transaction() for details
 * about update logic).
 *
 * It always returns JSON object with a 'success' key. If an "generic" error (like invalid
 * nonce or some invalid arguments) happens, success will be false. Otherwise, if success is true,
 * there will be a 'data' key containing:
 * - 'all_succeeded' - boolean
 * - 'results', an object with property names as keys and booleans indicating that particular
 *   property has been saved successfully (which depends on the logic in WPV_Content_Template),
 *   optionally also containing a "message" property that should be displayed to the user.
 *
 * @since 1.9
 */
function wpv_ct_update_properties_callback() {

    // Authentication and validation
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Untrusted user' );
    }
    $ct_id = (int) wpv_getpost( 'id' );
    $uid = get_current_user_id();

    $nonce_name = "wpv_ct_{$ct_id}_update_properties_by_{$uid}";
    if ( ! wp_verify_nonce( wpv_getpost( 'wpnonce' ), $nonce_name ) ) {
        wp_send_json_error( "Security check ($nonce_name)" );
    }

    $ct = WPV_Content_Template::get_instance( $ct_id );
    if( null == $ct ) {
        wp_send_json_error( 'Invalid Content Template' );
    }

    $properties = wpv_getpost( 'properties' );
    if( !is_array( $properties ) ) {
        wp_send_json_error( 'Invalid arguments (' . print_r( $properties, true ) . ')' );
    }

    // Try to save data as a transaction (all at once or nothing).
    // Refer to WPV_Post_Object_Wrapper::update_transaction() for details.
    $transaction_data = array();
    foreach( $properties as $property ) {
        // Missing property value defaults to empty array because of jQuery.ajax issues with empty arrays.
        // If it's invalid value for the property, it should be rejected during validation - no harm done here.
        $property_value = wpv_getarr( $property, 'value', array() );

        $transaction_data[ $property['name'] ] = $property_value;
    }

    // Run the update transaction.
    // Second parameter is false mostly because vm.processTitleSectionUpdateResults in JS.
    $transaction_result = $ct->update_transaction( $transaction_data, false );

    // Parse the translation result into per-property results that will be returned.
    $results = array();
    foreach( $properties as $property ) {

        $propery_name = $property['name'];
        $result = array( 'name' => $propery_name );

        if( true == $transaction_result['success'] ) {
            // Transaction success == all was updated without errors.
            $result['success'] = true;
        } else if( true == $transaction_result['partial']
            && in_array( $propery_name, $transaction_result['updated_properties'] ) ) {
            // The least desired situation (but rare) where some properties have been updated
            // and some haven't.
            $result['success'] = true;
        } else {
            // Failure, for one or the other reason. Look for an optional error message.
            $result['success'] = false;
            if( array_key_exists( $propery_name, $transaction_result['error_messages'] ) ) {
                $error = $transaction_result['error_messages'][ $propery_name ];
                $result['message'] = $error['message'];
                $result['code'] = $error['code'];
            }
        }

        $results[] = $result;
    }


    // Report success (because the AJAX call succeeded in general) and attach information
    // about each property update.
    wp_send_json_success( array( 'results' => $results ) );
}



/* ************************************************************************* *\
        Types integration
\* ************************************************************************* */


add_filter( 'wpcf_filter_force_include_types_fields_on_views_dialog', 'wpv_ct_editor_include_types_groups_on_dialog', 10, 2 );


/**
 * Enforces the Types groups for fields and usermeta fields into the Fields and Views popup.
 *
 * @since 1.9
 */
function wpv_ct_editor_include_types_groups_on_dialog( $state, $current_screen ) {
    if ( 'views_page_ct-editor' == $current_screen->id ) {
        $state = true;
    }
    return $state;
}


/* ************************************************************************* *\
        WPML integration
\* ************************************************************************* */


//add_filter( 'wpml_translation_validation_data', 'wpv_ct_wpml_translation_validation_data', 10, 2 );

/**
 *
 * Details about the filter here: https://onthegosystems.myjetbrains.com/youtrack/issue/wpmlcore-1626#comment=102-34759
 *
 * @param array $validation_results
 * @param array $data_to_validate
 * @return mixed
 */
/*function wpv_ct_wpml_translation_validation_data( $validation_results, $data_to_validate ) {

    $validation_results['messages'][] = "Test message";
    $validation_results['is_valid'] = false;

    return $validation_results;
}*/



/* ************************************************************************* *\
        Helper functions
\* ************************************************************************* */


/**
 * Render a link leading to Content Template edit page.
 *
 * @param int $ct_id ID of the Content Template.
 * @param string $label Link label (content of the a tag).
 * @param bool $echo If true (default), echoes the link HTML.
 * @return string Link HTML.
 *
 * @since 1.9
 */
function wpv_ct_editor_render_link( $ct_id, $label, $echo = true ) {

    $link = sprintf(
        '<a href="%s">%s</a>',
        wpv_ct_editor_url( $ct_id, false ),
        $label
    );

    if( $echo ) {
        echo $link;
    }

    return $link;
}


/**
 * Render an URL leading to Content Template edit page.
 *
 * @param int $ct_id ID of the Content Template.
 * @param bool $echo If true, echoes the URL. Default is false
 * @return string URL.
 *
 * @since 1.9
 */
function wpv_ct_editor_url( $ct_id, $echo = false ) {
    $url = esc_url_raw(
            add_query_arg(
            array( 'page' => WPV_CT_EDITOR_PAGE_NAME, 'ct_id' => esc_attr( $ct_id ), 'action' => 'edit' ),
            admin_url( 'admin.php' )
        )
    );

    if( $echo ) {
        echo $url;
    }
    return $url;
}


add_filter( 'icl_post_link', 'wpv_ct_post_link', 10, 4 );


/**
 * Adjust link to Content Template edit page for full Views.
 *
 * See icl_post_link for parameter description.
 *
 * @param $link
 * @param $post_type
 * @param $post_id
 * @param $link_purpose
 * @return array
 *
 * @since 1.10
 */
function wpv_ct_post_link( $link, $post_type, $post_id, $link_purpose ) {
    global $WP_Views;
    if( !$WP_Views->is_embedded() && ( WPV_Content_Template_Embedded::POST_TYPE == $post_type ) && ( 'edit' == $link_purpose ) ) {
        // Full Views, CT edit link is requested
        if( !is_array( $link ) ) {
            $link = array();
        }

        // If CT is trashed or non-existent, disable the link.
        $ct = WPV_Content_Template::get_instance( $post_id );
        if( ( null == $ct ) || $ct->is_trashed ) {
            $link['is_disabled'] = true;
        } else {
            $link['is_disabled'] = false;
            $link['url'] = wpv_ct_editor_url( $post_id, false );
        }
    }
    return $link;
}