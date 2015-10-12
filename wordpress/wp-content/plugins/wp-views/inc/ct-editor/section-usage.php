<?php

/**
 * Usage section for Content Template edit page
 *
 * @since 1.9
 */

/* ************************************************************************* *\
        Request WPV_Content_Template properties for the JS side
\* ************************************************************************* */


add_filter( 'wpv_ct_editor_request_properties', 'wpv_ct_editor_usage_section_request_properties' );


function wpv_ct_editor_usage_section_request_properties( $property_names ) {
	return array_merge( $property_names, array(
        'assigned_single_post_types',
        'assigned_post_archives',
        'assigned_taxonomy_archives',
        'dissident_posts'
    ) );
}


/* ************************************************************************* *\
        Localize the section in JS
\* ************************************************************************* */


add_filter( 'wpv_ct_editor_localize_script', 'wpv_ct_editor_usage_section_localize_script' );


function wpv_ct_editor_usage_section_localize_script( $l10n_data ) {
	$l10n_data['usage_section'] = array(
		'saved' => esc_attr( __( 'Content Template usage changed.', 'wpv-views' ) ),
		'unsaved' => esc_attr( __( 'Content Template usage not changed.', 'wpv-views' ) ),
        'ptr_section' => array(
            'title' => __( 'Choose which content types will display using this template.', 'wpv-views' ),
            'paragraphs' => array(
                __( 'Choose which content types will display using this template.', 'wpv-views' )
            )
        )
	);
	return $l10n_data;
}


/* ************************************************************************* *\
        Pass custom properties to the JS side
\* ************************************************************************* */

add_filter( 'wpv_ct_editor_add_custom_properties', 'wpv_ct_editor_usage_section_add_properties', 10, 2 );


/**
 * This will add following properties to the $ct_data that will eventually be passed
 * to the JS script:
 *
 * - usage_bind_nonce: A nonce to bind dissident posts to this CT.
 * - usage_bind_dialog_template: Localized HTML template for a Colorbox dialog for
 *     asking user's confirmation to bind dissident posts.
 *
 * @param array $ct_data Content Template data
 * @param WPV_Content_Template $ct
 * @return array Updated Content Template data
 *
 * @since 1.9
 */
function wpv_ct_editor_usage_section_add_properties( $ct_data, $ct ) {
    $uid = get_current_user_id();

    $ct_data['usage_bind_nonce'] = wp_create_nonce( "wpv_ct_{$ct->id}_bind_posts_by_{$uid}" );

    $bind_dialog_message_template = sprintf(
        __( '%s %s use a different Content Template.', 'wpv-views' ),
        '<span class="js-wpv-ct-bind-dialog-post-count"></span>' ,
        '<span class="js-wpv-ct-bind-dialog-post-type"></span>' );

    $ct_data['usage_bind_dialog_template'] = '
        <div class="wpv-dialog js-wpv-bind-dissident-posts-dialog">
            <div class="wpv-dialog-header">
                <h2>' . __( 'Do you want to apply to all?', 'wpv-views' ) . '</h2>
                <i class="icon-remove js-dialog-close"></i>
            </div>
            <div class="wpv-dialog-content">' . $bind_dialog_message_template . '</div>
            <div class="wpv-dialog-footer">
                <button class="button js-dialog-close">' . __( 'Cancel', 'wpv-views' ) . '</button>
                <button class="button button-primary js-wpv-dialog-update-button">
                    '. __( 'Update', 'wpv-views' ) . '</button>
            </div>
        </div>';

    $post_type_labels = array();

    global $WPV_view_archive_loop;
    $public_post_types = $WPV_view_archive_loop->get_archive_loops( 'post_type', false, true, true );
    foreach( $public_post_types as $post_type ) {
        $post_type_labels[ $post_type['post_type_name'] ] = array(
            'singular' => $post_type['singular_name'],
            'plural' => $post_type['display_name']
        );
    }

    $ct_data['usage_post_type_labels'] = $post_type_labels;

    return $ct_data;
}



/* ************************************************************************* *\
        Render section content
\* ************************************************************************* */


add_action( 'wpv_ct_editor_sections', 'wpv_ct_editor_usage_section', 30 );


function wpv_ct_editor_usage_section( $ct ) {
	ob_start();

    $parent_view = null;
    if( $ct->is_owned_by_view ) {
        $parent_view = WPV_View_Base::get_instance($ct->loop_output_id);
    }

    if( null != $parent_view ) {

        if( $parent_view->is_published ) {
			$edit_page = 'views-editor';
			if ( WPV_View_Base::is_archive_view( $parent_view->id ) ) {
				$edit_page = 'view-archives-editor';
			}
            $loop_template_notice = sprintf(
                __( 'This Content Template is used as the loop block for the %s <a href="%s" target="_blank">%s</a>.', 'wpv-views' ),
                $parent_view->query_mode_display_name,
                esc_attr( add_query_arg(
                    array(
                        'page' => $edit_page,
                        'view_id' => $parent_view->id
                    ),
                    admin_url( 'admin.php' )
                ) ),
                $parent_view->title
            );

        } else {

            $loop_template_notice = sprintf(
                __( 'This Content Template is used as the loop block for the trashed %s %s.', 'wpv-views' ),
                $parent_view->query_mode_display_name,
                "<strong>{$parent_view->title}</strong>"
            );
        }

        printf( '<div class="wpv-advanced-setting"><p>%s</p></div>', $loop_template_notice );

    } else {

        $asterisk_explanation =
            '<span data-bind="fadeVisibility: isAsteriskExplanationVisible(\'%s\', \'%s\')"><span style="color:red">*</span> '
            . __('A different Content Template is already assigned to this item.', 'wpv-views')
            . '</span>';


        // Render checkboxes for each type of assignment.
        $single_post_types_with_other_ct = wpv_ct_editor_usage_section_single_pages($ct, $asterisk_explanation);
        $cpt_archives_with_other_ct = wpv_ct_editor_usage_section_post_archives($ct, $asterisk_explanation);
        $taxonomy_archives_with_other_ct = wpv_ct_editor_usage_section_taxonomy_archives($ct, $asterisk_explanation);

        // Print information about other CT assignments for JS
        $other_assignments = array(
            'single_posts' => $single_post_types_with_other_ct,
            'cpt_archives' => $cpt_archives_with_other_ct,
            'taxonomy_archives' => $taxonomy_archives_with_other_ct
        );


        printf(
            '<span style="visibility: hidden" class="js-wpv-usage-other-assignments" data-value="%s"></span>',
            htmlentities(json_encode($other_assignments))
        );
        ?>

        <p class="update-button-wrap">
            <span class="update-action-wrap">
                <span class="js-wpv-message-container"></span>
                <span class="spinner ajax-loader" data-bind="spinnerActive: isUsageSectionUpdating"></span>
            </span>
            <button data-bind="
                        enable: isUsageSectionUpdateNeeded,
                        attr: { class: isUsageSectionUpdateNeeded() ? 'button-primary' : 'button-secondary' },
                        click: usageSectionUpdate">
                <?php _e('Update', 'wpv-views'); ?>
            </button>
        </p>

    <?php
    }

	$content = ob_get_contents();
	ob_end_clean();

	wpv_ct_editor_render_section(
        __( 'Usage', 'wpv-views' ),
        'js-wpv-usage-section',
        $content,
        false,
        '',
        '',
        array( 'section' => 'usage_section', 'pointer_slug' => 'ptr_section' ) );
}



/**
 * Render subsection for assignment type "single page"
 *
 * @param WPV_Content_Template $ct
 * @param string $asterisk_explanation Localized HTML template with asterisk explanation.
 *     Should contain two "%s" placeholders, one for the Knockout ViewModel property name and second
 *     for the assignment type (see js-wpv-usage-other-assignments).
 * @return array Post type slugs with different CT assigned.
 *
 * @since 1.9
 */
function wpv_ct_editor_usage_section_single_pages( $ct, $asterisk_explanation ) {
    global $WPV_view_archive_loop;

    ?>
    <h3><?php _e( 'Single pages', 'wpv-views' ); ?></h3>
    <div class="wpv-advanced-setting">
        <ul class="wpv-mightlong-list">
            <?php

            $public_post_types = $WPV_view_archive_loop->get_archive_loops( 'post_type', false, true, true );

            $dissident_posts = $ct->dissident_posts;

            $single_post_types_with_other_ct = array();

            foreach( $public_post_types as $post_type ) {

                $is_assigned_to_other_ct = ( $post_type['single_ct'] != 0 && $post_type['single_ct'] != $ct->id );

                if( $is_assigned_to_other_ct ) {
                    $single_post_types_with_other_ct[] = $post_type['post_type_name'];
                }

                if( array_key_exists( $post_type['post_type_name'], $dissident_posts ) ) {
                    $dissident_posts_for_type = $dissident_posts[$post_type['post_type_name']];
                } else {
                    $dissident_posts_for_type = array();
                }

                wpv_ct_editor_usage_section_render_item(
                    $post_type['post_type_name'],
                    $post_type['display_name'],
                    'assignedSinglePostTypesAccepted',
                    'single_posts',
                    $is_assigned_to_other_ct,
                    $dissident_posts_for_type,
                    $post_type['singular_name']
                );

            }
            ?>
        </ul>
        <?php
        printf( $asterisk_explanation, 'assignedSinglePostTypesAccepted', 'single_posts' );
        ?>
    </div>
    <?php

    return $single_post_types_with_other_ct;
}


/**
 * Render subsection for assignment type "post archive"
 *
 * @param WPV_Content_Template $ct
 * @param string $asterisk_explanation Localized HTML template with asterisk explanation.
 *     Should contain two "%s" placeholders, one for the Knockout ViewModel property name and second
 *     for the assignment type (see js-wpv-usage-other-assignments).
 * @return array Post type slugs with different CT assigned.
 *
 * @since 1.9
 */
function wpv_ct_editor_usage_section_post_archives( $ct, $asterisk_explanation ) {
    global $WPV_view_archive_loop;

    ?>
    <h3><?php _e( 'Post archives', 'wpv-views' ); ?></h3>
    <div class="wpv-advanced-setting">
        <?php
        $custom_post_types = $WPV_view_archive_loop->get_archive_loops( 'post_type', false, true, false );

        $cpt_archives_exist = !empty( $custom_post_types );

        $cpt_archives_with_other_ct = array();

        if( $cpt_archives_exist ) {
            ?>
            <ul class="wpv-mightlong-list">
                <?php
                foreach( $custom_post_types as $post_type ) {

                    $is_assigned_to_other_ct = ( $post_type['ct'] != 0 && $post_type['ct'] != $ct->id );

                    if( $is_assigned_to_other_ct ) {
                        $cpt_archives_with_other_ct[] = $post_type['post_type_name'];
                    }

                    wpv_ct_editor_usage_section_render_item(
                        $post_type['post_type_name'], $post_type['display_name'], 'assignedPostArchivesAccepted', 'cpt_archives', $is_assigned_to_other_ct
                    );

                }
                ?>
            </ul>
        <?php
        } else {
            printf( '<p>%s</p>', __( 'There are no custom post type archives', 'wpv-views' ) );
        }

        printf( $asterisk_explanation, 'assignedPostArchivesAccepted', 'cpt_archives' );
        ?>
    </div>

<?php
    return $cpt_archives_with_other_ct;
}


/**
 * Render subsection for assignment type "taxonomy archive"
 *
 * @param WPV_Content_Template $ct
 * @param string $asterisk_explanation Localized HTML template with asterisk explanation.
 *     Should contain two "%s" placeholders, one for the Knockout ViewModel property name and second
 *     for the assignment type (see js-wpv-usage-other-assignments).
 * @return array Taxonomy slugs with different CT assigned.
 *
 * @since 1.9
 */
function wpv_ct_editor_usage_section_taxonomy_archives( $ct, $asterisk_explanation ) {
    global $WPV_view_archive_loop;

    ?>
    <h3><?php _e( 'Taxonomy archives', 'wpv-views' ); ?></h3>
    <div class="wpv-advanced-setting">
        <ul class="wpv-mightlong-list">
            <?php

            $taxonomy_loops = $WPV_view_archive_loop->get_archive_loops( 'taxonomy', false, true );

            $taxonomy_archives_with_other_ct = array();

            foreach( $taxonomy_loops as $taxonomy_loop ) {

                $is_assigned_to_other_ct = ( $taxonomy_loop['ct'] != 0 && $taxonomy_loop['ct'] != $ct->id );

                if( $is_assigned_to_other_ct ) {
                    $taxonomy_archives_with_other_ct[] = $taxonomy_loop['slug'];
                }

                wpv_ct_editor_usage_section_render_item(
                    $taxonomy_loop['slug'], $taxonomy_loop['display_name'], 'assignedTaxonomyArchivesAccepted', 'taxonomy_archives', $is_assigned_to_other_ct
                );

            }
            ?>
        </ul>
        <?php
        printf( $asterisk_explanation, 'assignedTaxonomyArchivesAccepted', 'taxonomy_archives' );
        ?>
    </div>

<?php
    return $taxonomy_archives_with_other_ct;
}


/**
 * Render an item with a checkbox for the Usage settings.
 *
 * It will render a list (li) item with a label, checkbox and optionally also
 * an asterisk (if there is other CT assigned) and "Bind posts" button for dissident posts.
 * All with proper Knockout bindings.
 *
 * @param string $value Value of the checkbox.
 * @param string $display_name Label for the checkbox.
 * @param string $binding Second parameter for vm.isAsteriskVisible in JS (look there for an explanation).
 * @param string $assignment_type Assignment type (see js-wpv-usage-other-assignments).
 * @param bool $is_assigned_to_other_ct Determines whether this item has different CT assigned at the time.
 * @param null|array $dissident_posts Array of dissident post IDs or null if not applicable.
 * @param null|string $singular_display_name Single label for the post type or null if not applicable.
 *
 * @internal param WPV_Content_Template $ct
 * @since 1.9
 */
function wpv_ct_editor_usage_section_render_item( $value, $display_name, $binding, $assignment_type,
        $is_assigned_to_other_ct, $dissident_posts = null, $singular_display_name = null )
{
    ?>
    <li>
        <label>
            <?php
                printf( '<input type="checkbox" value="%s" data-bind="checked: %s"/> ', $value, $binding );

                echo $display_name;

                if( $is_assigned_to_other_ct ) {
                    printf(
                        ' <span style="color:red;" data-bind="fadeVisibility: isAsteriskVisible(\'%s\', \'%s\', \'%s\')">*</span>',
                        $assignment_type,
                        $binding,
                        $value
                    );

                } else if( is_array( $dissident_posts ) ) {
                    $post_type = esc_attr( $value );
                    $post_count = count( $dissident_posts );

                    $button_post_type_label = ( 1 == $post_count && null != $singular_display_name ) ? $singular_display_name : $display_name;

                    printf(
                        ' &nbsp;&nbsp;
                        <button class="button button-leveled button-small icon-warning-sign"
                                data-bind="click: bindDissidentPosts.bind($data, \'%s\'),
                                        enable: $root.isBindButtonEnabled(\'%s\'),
                                        fadeVisibility: $root.isBindButtonVisible(\'%s\')"
                                >
                            %s
                        </button>',
                        $post_type,
                        $post_type,
                        $post_type,
                        sprintf( __( 'Bind %u %s', 'wpv-views' ), $post_count, $button_post_type_label ),
                        $post_type
                    );

                }
            ?>
        </label>
    </li>
    <?php
}