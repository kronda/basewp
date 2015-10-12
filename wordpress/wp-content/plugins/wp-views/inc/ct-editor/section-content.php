<?php

/**
 * Content section for Content Template edit page
 *
 * @since 1.9
 */


/* ************************************************************************* *\
        Request WPV_Content_Template properties for the JS side
\* ************************************************************************* */


add_filter( 'wpv_ct_editor_request_properties', 'wpv_ct_editor_content_section_request_properties' );


function wpv_ct_editor_content_section_request_properties( $property_names ) {
    return array_merge( $property_names, array( 'content', 'template_extra_css', 'template_extra_js' ) );
}


/* ************************************************************************* *\
        Request CRED assets for this page
\* ************************************************************************* */


/**
 * Determine if CRED version is high enough to support CRED button on the CT edit page.
 *
 * @return bool True if we should render the button.
 * @since 1.9
 */
function wpv_ct_editor_is_cred_button_supported() {
    $last_unsupporting_cred_version = '1.3.6.1';

    if( !defined( 'CRED_FE_VERSION' ) ) {
        // seems like no CRED at all
        return false;
    }

    // true if current CRED version is HIGHER than 1.3.6.1.
    return ( version_compare( CRED_FE_VERSION, $last_unsupporting_cred_version ) == 1 );
}



add_filter( 'cred_get_custom_pages_to_load_assets', 'wpv_ct_editor_content_section_request_cred_assets' );

/**
 * Tell CRED that it should load it's assets to display the CRED Forms button
 * on CT edit page, too.
 *
 * @param array $set_on_pages Page names where CRED assets should be loaded.
 * @return array The modified array of page names.
 * @since 1.9
 */
function wpv_ct_editor_content_section_request_cred_assets( $set_on_pages ) {
    if( wpv_ct_editor_is_cred_button_supported() ) {
        if (!is_array($set_on_pages)) {
            $set_on_pages = array();
        }
        $set_on_pages[] = WPV_CT_EDITOR_PAGE_NAME;
    }
    return $set_on_pages;
}


/* ************************************************************************* *\
        Localize the section in JS
\* ************************************************************************* */


add_filter( 'wpv_ct_editor_localize_script', 'wpv_ct_editor_content_section_localize_script' );


function wpv_ct_editor_content_section_localize_script( $l10n_data ) {
    $l10n_data['content_section'] = array(
        'saved' => esc_attr( __( 'Template updated.', 'wpv-views' ) ),
        'unsaved' => esc_attr(__( 'Template not saved.', 'wpv-views' ) ),
        'ptr_section' => array(
            'title' => __( 'Template', 'wpv-views' ),
            'paragraphs' => array(
                __( 'Add fields to the template to display the content. Use HTML tags for styling.', 'wpv-views' )
            )
        )
    );
    return $l10n_data;
}


/* ************************************************************************* *\
        Render section content
\* ************************************************************************* */


add_action( 'wpv_ct_editor_sections', 'wpv_ct_editor_content_section', 20 );


function wpv_ct_editor_content_section( $ct ) {
    ob_start();

    ?>
    <div class="js-code-editor code-editor content-editor" data-name="complete-output-editor">
        <div class="code-editor-toolbar js-code-editor-toolbar">
            <ul>
                <?php
					$menus_to_add = array(
						'post',						// wpv-post shortcodes plus non-Types fields under their own section
						'post-extended',			// generic shortcodes extended in the Basic section
						'post-fields-placeholder',	// non-Types fields on demand
						'user',						// basic user data
						'body-view-templates',		// Content Templates
						'post-view',				// Views listing posts
						'taxonomy-view',			// all available Views listing terms
						'user-view'					// all available Views listing users
					);
                    do_action( 'wpv_views_fields_button', 'wpv_content', $menus_to_add );

                    // Needed so CRED displays a button instead of a fake anchor tag
                    if( wpv_ct_editor_is_cred_button_supported() ) {
                        define("CT_INLINE", "1");
                        do_action('wpv_cred_forms_button', 'wpv_content');
                    }
                    
                    wpv_ct_editor_content_add_media_button( $ct->id, 'wpv_content' );
                ?>
            </ul>
        </div>
        <!--suppress HtmlFormInputWithoutLabel -->
        <textarea cols="30" rows="10" id="wpv_content" name="wpv_content"
                  data-bind="textInput: postContentAccepted"></textarea>

        <!--
            CSS editor
        -->
        <div class="wpv-editor-metadata-toggle" data-bind="click: toggleCssEditor">
            <span class="wpv-toggle-toggler-icon">
                <i data-bind="attr: { class: isCssEditorExpanded() ? 'icon-caret-up icon-large' : 'icon-caret-down icon-large' }"></i>
            </span>
            <i class="icon-pushpin" data-bind="widthToggleVisible: isCssPinVisible"></i>
            <strong><?php _e( 'CSS editor', 'wpv-views' ); ?></strong>
        </div>
        <div class="wpv-ct-assets-inline-editor"
                data-bind="editorVisible: isCssEditorExpanded"
                data-target-editor="css">
            <!--suppress HtmlFormInputWithoutLabel -->
            <textarea name="name" id="wpv_template_extra_css"
                      data-bind="textInput: templateCssAccepted"></textarea>
        </div>

        <!--
            JS editor
        -->
        <div class="wpv-editor-metadata-toggle" data-bind="click: toggleJsEditor">
            <span class="wpv-toggle-toggler-icon">
                <i data-bind="attr: { class: isJsEditorExpanded() ? 'icon-caret-up icon-large' : 'icon-caret-down icon-large' }"></i>
            </span>
            <i class="icon-pushpin" data-bind="widthToggleVisible: isJsPinVisible"></i>
            <strong><?php _e( 'JS editor', 'wpv-views' ); ?></strong>
        </div>
        <div class="wpv-ct-assets-inline-editor"
                data-bind="editorVisible: isJsEditorExpanded"
                data-target-editor="js">
            <!--suppress HtmlFormInputWithoutLabel -->
            <textarea name="name" id="wpv_template_extra_js"
                      data-bind="textInput: templateJsAccepted"></textarea>
        </div>

        <?php wpv_formatting_help_content_template(); ?>
    </div>

    <p class="update-button-wrap">
        <span class="update-action-wrap">
            <span class="js-wpv-message-container"></span>
            <span class="spinner ajax-loader" data-bind="spinnerActive: isContentSectionUpdating"></span>
        </span>
        <button data-bind="
                enable: isContentSectionUpdateNeeded,
                attr: { class: isContentSectionUpdateNeeded() ? 'button-primary' : 'button-secondary' },
                click: contentSectionUpdate">
            <?php _e( 'Update', 'wpv-views' ); ?>
        </button>
    </p>

    <?php

    $content = ob_get_contents();
    ob_end_clean();

    wpv_ct_editor_render_section(
        __( 'Template', 'wpv-views' ),
        'js-wpv-content-section',
        $content,
        true,
        '',
        '',
        array( 'section' => 'content_section', 'pointer_slug' => 'ptr_section' ) );
}


/**
 * Render media button for a CodeMirror editor.
 *
 * @param int $ct_id Content Template ID
 * @param string $context Editor context (id of the underlying textarea)
 *
 * @since 1.9
 */
function wpv_ct_editor_content_add_media_button( $ct_id, $context ) {
    ?>
    <li>
        <button class="button-secondary js-code-editor-toolbar-button js-wpv-media-manager"
                data-id="<?php echo $ct_id; ?>" data-content="<?php echo $context; ?>">
            <i class="icon-picture"></i>
            <span class="button-label"><?php _e( 'Media', 'wpv-views' ); ?></span>
        </button>
    </li>
    <?php
}