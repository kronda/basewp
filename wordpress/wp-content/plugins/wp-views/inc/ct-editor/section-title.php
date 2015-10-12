<?php

/**
 * Title section for Content Template edit page
 *
 * @since 1.9
 */


/* ************************************************************************* *\
        Request WPV_Content_Template properties for the JS side
\* ************************************************************************* */


add_filter( 'wpv_ct_editor_request_properties', 'wpv_ct_editor_title_section_request_properties' );


function wpv_ct_editor_title_section_request_properties( $property_names ) {
    return array_merge( $property_names, array( 'title', 'slug', 'description_raw' ) );
}


/* ************************************************************************* *\
        Localize the section in JS
\* ************************************************************************* */


add_filter( 'wpv_ct_editor_localize_script', 'wpv_ct_editor_title_section_localize_script' );


function wpv_ct_editor_title_section_localize_script( $l10n_data ) {
    $l10n_data['title_section'] = array(
        'saved' => esc_attr( __( 'Title, slug and description updated.', 'wpv-views' ) ),
        'unsaved' => esc_attr(__( 'Title, slug and description not saved.', 'wpv-views' ) ),
        'ptr_section' => array(
            'title' => __( 'Title and description', 'wpv-views' ),
            'paragraphs' => array(
                __( 'The name of the Content Template is used for you, to identify it. The name is not displayed anywhere on the site.', 'wpv-views' )
            )
        ),
        'title_and_slug_used' => __( 'Both title and slug are being already used by another Content Template. Please use other values.', 'wpv-views' ),
        'title_was_escaped' => __( 'We escaped the title before saving.', 'wpv-views' ),
        'value_already_used_exception_code' => WPV_RuntimeExceptionWithMessage::EXCEPTION_VALUE_ALREADY_USED
    );
    return $l10n_data;
}


/* ************************************************************************* *\
        Render section content
\* ************************************************************************* */


add_action( 'wpv_ct_editor_sections', 'wpv_ct_editor_title_section', 10 );


/**
 * @param WPV_Content_Template $ct
 * @since 1.9
 */
function wpv_ct_editor_title_section( $ct )
{
	ob_start();
    ?>
    <div id="titlediv">
        <div id="titlewrap">
            <label class="screen-reader-text js-title-reader" id="title-prompt-text" for="title">
                <?php _e('Enter title here', 'wp-views'); ?>
            </label>
            <input id="title" name="title" type="text" size="30" autocomplete="off"
                   data-bind="textInput: title"/>
        </div>
    </div>

    <div id="edit-slug-box">
        <label for="slug">
            <?php _e( 'Slug of this Content Template', 'wpv-views' ); ?>
        </label>
        <!--suppress HtmlFormInputWithoutLabel -->
        <input id="wpv-slug" name="slug" type="text"
               data-bind="textInput: slugAccepted"/>

        <?php
            if( $ct->can_be_trashed ) {
                ?>
                &bull;
                <button class="button-secondary" data-bind="click: trashAction, disable: isTrashing">
                    <i class="icon-trash"></i> <?php _e( 'Move to trash', 'wpv-views' ); ?>
                </button>
                <span class="spinner ajax-loader" data-bind="spinnerActive: isTrashing"></span>
                <?php
            }
        ?>

    </div>

    <p>
        <span data-bind="visible: showAddDescriptionButton">
            <button class="button-secondary" data-bind="click: showDescriptionField">
                <?php _e('Add description', 'wpv-views'); ?>
            </button>
        </span>
    </p>

    <div class="wpv-description-container" data-bind="visible: isDescriptionVisible">
        <p>
            <label for="wpv-description">
                <?php _e('Describe this Content Template', 'wpv-views'); ?>
            </label>
        </p>

        <p>
            <textarea id="wpv-description" name="description" cols="72" rows="4"
                      data-bind="textInput: descriptionAccepted"></textarea>
        </p>
    </div>

    <p class="update-button-wrap">
        <span class="js-wpv-message-container"></span>
        <span class="spinner ajax-loader" data-bind="spinnerActive: isTitleSectionUpdating"></span>
        <button data-bind="
                enable: isTitleSectionUpdateNeeded,
                attr: { class: isTitleSectionUpdateNeeded() ? 'button-primary' : 'button-secondary' },
                click: titleSectionUpdate">
            <?php _e('Update', 'wpv-views'); ?>
        </button>
    </p>
<?php

	$content = ob_get_contents();
	ob_end_clean();

    // We're adding also the "wpv-title-section" class that's important for the Action bar styling.
    // Otherwise it has no relevance here.
	wpv_ct_editor_render_section(
        __( 'Title and Description', 'wpv-views' ),
        'js-wpv-title-section wpv-title-section',
        $content,
        false,
        '',
        '',
        array( 'section' => 'title_section', 'pointer_slug' => 'ptr_section' )
    );
}