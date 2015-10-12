<?php

/**
 * Represents a single Content Template.
 *
 * Full version with setters & co.
 *
 * @since 1.9
 */
final class WPV_Content_Template extends WPV_Content_Template_Embedded {


    /* ************************************************************************* *\
        Constants
    \* ************************************************************************* */


    /**
     * @var array CT postmeta keys that should not be copied when cloning a CT.
     *
     * Note: When adding new postmeta here, don't forget to update wpml-config.xml!
     *
     * @since 1.9
     */
    protected static $postmeta_keys_not_to_clone = array(
        WPV_Content_Template_Embedded::POSTMETA_EDIT_LOCK,
        WPV_Content_Template_Embedded::POSTMETA_LOOP_OUTPUT_ID
    );


    /* ************************************************************************* *\
        Constructor
    \* ************************************************************************* */


    /**
     * See parent class constructor description.
     *
     * @param int|WP_Post $ct CT post object or ID.
     */
    public function __construct( $ct ) {
        parent::__construct( $ct );
    }


    /* ************************************************************************* *\
        Static methods
    \* ************************************************************************* */


    /**
     * Create an instance of WPV_Content_Template from Content Template ID or a WP_Post object.
     *
     * See WPV_View_Embedded constructor for details.
     *
     * @param int|WP_Post $ct CT ID or a WP_Post object.
     *
     * @return null|WPV_Content_Template
     */
    public static function get_instance( $ct ) {
        try {
            $ct = new WPV_Content_Template( $ct );
            return $ct;
        } catch( Exception $e ) {
            return null;
        }
    }


    /**
     * Create a new Content Template with default settings.
     *
     * @param string $title Content Template title.
     * @param bool $adjust_duplicate_title If true, change the title if it's not unique among Content Templates. You can (and
     *     should) check for the value that was actually saved to database through the returned $ct->title. If this is
     *     false and the title is not unique (determined by is_name_used()), the operation will fail.
     *
     * @return null|WPV_Content_Template CT object or null if creating has failed.
     *
     * @since 1.9
     */
    public static function create( $title, $adjust_duplicate_title = true ) {

        $sanitized_title = sanitize_text_field( $title );

        // Handle empty title
        if( empty( $sanitized_title ) ) {
            if( $adjust_duplicate_title ) {
                $sanitized_title = sanitize_text_field( __( 'Content Template', 'wpv-views' ) );
            } else {
                // empty title, but we're not allowed to adjust it -> fail
                return null;
            }
        }

        // Ensure title uniqueness (or fail)
        $is_title_unique = ! WPV_Content_Template_Embedded::is_name_used( $sanitized_title );

        if( !$is_title_unique ) {
            if( $adjust_duplicate_title ) {
                $sanitized_title = WPV_Content_Template::get_unique_title( $sanitized_title );
            } else {
                // Non-unique title & we're not allowed to re-use it -> fail
                return null;
            }
        }

        // Insert the post in database.
        $post_id = wp_insert_post(
            array(
                'post_title' => $sanitized_title,
                'post_status' => 'publish',
                'post_type' => WPV_Content_Template_Embedded::POST_TYPE,
                'post_content' => ''
            ),
            false
        );

        // Create the CT object or fail
        $ct = WPV_Content_Template::get_instance( $post_id );

        if( null == $ct ) {
            return null;
        }

        $ct->defer_after_update_actions();

        // Save default postmeta values
        foreach( WPV_Content_Template_Embedded::$postmeta_defaults as $meta_key => $meta_value ) {
            $ct->update_postmeta( $meta_key, $meta_value );
        }

        // After update action will be called exactly once.
        $ct->maybe_after_update_action();
        $ct->resume_after_update_actions();

        return $ct;
    }


    /**
     * Generate an unique title for a Content Template based on a candidate value.
     *
     * @param string $title_candidate Non-blank (e.g. not only whitespace) title candidate.
     * @param int $except_id CT id that should be excluded from the uniqueness check.
     * @return null|string An unique title or null if the input was invalid.
     *
     * @since 1.9
     *
     * @todo consider moving this to WPV_Post_Object_Wrapper
     */
    public static function get_unique_title( $title_candidate, $except_id = 0 ) {

        $title_candidate = trim( $title_candidate );
        if( empty( $title_candidate ) ) {
            return null;
        }

        // If the title is already unique, we're done.
        if( ! WPV_Content_Template_Embedded::is_name_used( $title_candidate, $except_id ) ) {
            return $title_candidate;
        }

        // If current title has a number at it's end, we'll use it and start incrementing it. If not,
        // we will just add a number as a suffix.
        $title_parts = explode( ' ', trim( $title_candidate ) );
        // there will allways be at least one part
        $last_title_part = $title_parts[ count( $title_parts ) - 1 ];

        if( is_numeric( $last_title_part ) ) {
            $numeric_suffix =  $last_title_part + 1;
            $title_base = implode( ' ', array_slice( $title_parts, 0, -1 ) );
        } else {
            $numeric_suffix = 2;
            $title_base = $title_candidate;
        }

        // Keep incrementing the suffix until an unique title is found.
        do {
            $title_candidate = "$title_base $numeric_suffix";
            ++$numeric_suffix;
        } while( WPV_Content_Template_Embedded::is_name_used( $title_candidate, $except_id ) );

        return $title_candidate;
    }


    /* ************************************************************************* *\
        Custom methods
    \* ************************************************************************* */


    /*function bind_dissident_posts( $post_type, $post_ids = null ) {
        // TODO re-implement this better and here
        wpv_update_dissident_posts_from_template( $this->id, $post_type, false );
    }*/


    /**
     * Bind posts to this Content Template.
     *
     * This CT will be set as a "single post template" for given posts.
     *
     * @param $post_ids Array of post IDs.
     *
     * @return bool|int Number of updated posts or false if the action has failed.
     *
     * @since 1.9
     */
    public function bind_posts( $post_ids ) {
        if( !is_array( $post_ids ) ) {
            return false;
        }

        // Update the appropriate postmeta key for each post.
        $updated_count = 0;
        foreach ( $post_ids as $post_id ) {
            $current_content_template = get_post_meta( $post_id, WPV_Content_Template_Embedded::POST_TEMPLATE_BINDING_POSTMETA_KEY, true );
            if ( $current_content_template != $this->id ) {
                update_post_meta( $post_id, WPV_Content_Template_Embedded::POST_TEMPLATE_BINDING_POSTMETA_KEY, $this->id );
                ++$updated_count;
            }
        }

        return $updated_count;
    }


    /**
     * Clone a Content Template.
     *
     * @param string $title Title of the new CT.
     * @param bool $adjust_duplicate_title If true, the title might get changed in order to ensure it's uniqueness.
     *     Otherwise, if $title is not unique, the cloning will fail.
     * @return null|WPV_Content_Template The cloned CT or null on failure.
     *
     * @since 1.9
     */
    public function clone_this( $title, $adjust_duplicate_title = true ) {

        // Create new CT
        $cloned_ct = WPV_Content_Template::create( $title, $adjust_duplicate_title );
        if( null == $cloned_ct ) {
            return null;
        }


        // Copy postmeta
        $cloned_ct->defer_after_update_actions();
        $postmeta_defaults = $this->get_postmeta_defaults();
        foreach( $postmeta_defaults as $meta_key => $ignored_value ) {
            if( !in_array( $meta_key, WPV_Content_Template::$postmeta_keys_not_to_clone ) ) {
                $cloned_ct->update_postmeta( $meta_key, $this->get_postmeta( $meta_key ) );
            }
        }
        $cloned_ct->resume_after_update_actions();

        // Copy content
        $cloned_ct->content_raw = $this->content_raw;

        return $cloned_ct;
    }


    /* ************************************************************************* *\
        Setters (& validators)
    \* ************************************************************************* */


    /**
     * Validate the post title.
     *
     * The value must already be sanitized (without HTML tags etc.) and unique among CT post
     * titles and slugs. It also can't be empty. Surrounding whitespaces will not cause
     * an exception, but they will be trimmed.
     *
     * @param string $value New post title.
     * @return string Sanitized value, safe to be used.
     * @throws WPV_RuntimeExceptionWithMessage
     * @since 1.9
     */
    protected function _validate_title( $value ) {

        $sanitized_value = sanitize_text_field( $value );

        // Check if the original value contains something that shouldn't be there.
        // We tolerate whitespace at the beginning and end, ergo the trim (but we will
        // work with the trimmed value from now on).
        if( trim( $value ) != $sanitized_value ) {
            throw new WPV_RuntimeExceptionWithMessage(
                '_validate_title failed: invalid characters',
                __( 'The title can not contain any tabs, line breaks or HTML code.', 'wpv-views' )
            );
        }

        if( empty( $sanitized_value ) ) {
            throw new WPV_RuntimeExceptionWithMessage(
                '_validate_title failed: empty value',
                __( 'You can not leave the title empty.', 'wpv-views' )
            );
        }

        $collision_data = array();
        if( WPV_Content_Template_Embedded::is_name_used( $sanitized_value, $this->id, $collision_data ) ) {
            switch( $collision_data['colliding_field'] ) {
                case 'post_name':
                    $exception_message = sprintf(
                        __( 'Another Content Template (%s) already uses this title value as it\'s slug. Please use another title.', 'wpv-views' ),
                        sanitize_text_field( $collision_data['post_title'] )
                    );
                    break;
                case 'post_title':
                    $exception_message = __( 'Another Content Template with that title already exists. Please use another title.', 'wpv-views' );
                    break;
                case 'both':
                    $exception_message = __( 'Another Content Template already uses this title value as it\'s title and slug. Please use another title.', 'wpv-views' );
                    break;
                default:
                    // Should never happen
                    $exception_message = __( 'Another item with that slug or title already exists. Please use another title.', 'wpv-views' );
                    break;
            }
            //$exception_message = print_r( $collision_data, true );
            throw new WPV_RuntimeExceptionWithMessage(
                '_validate_title failed: name is already being used for another CT',
                $exception_message,
                WPV_RuntimeExceptionWithMessage::EXCEPTION_VALUE_ALREADY_USED
            );
        }

        return $sanitized_value;
    }


    /**
     * Post title setter.
     *
     * See _validate_title().
     *
     * @param string $value New post title.
     * @throws Exception, WPV_RuntimeExceptionWithMessage
     * @since 1.9
     */
    protected function _set_title( $value ) {

        $sanitized_value = $this->_validate_title( $value );

        $result = $this->update_post( array( 'post_title' => $sanitized_value ) );

        if( $result instanceof WP_Error ) {
            throw new Exception( '_set_title failed: WP_Error' );
        }
    }


    /**
     * Post slug validation.
     *
     * Accepts a non-empty value containing only lowercase letters, numbers or dashes.
     *
     * @param string $value New post slug.
     * @return string Sanitized value safe to be used.
     * @throws WPV_RuntimeExceptionWithMessage
     * @since 1.9
     */
    protected function _validate_slug( $value ) {

        $sanitized_value = sanitize_title( $value );
        if( $value != $sanitized_value ) {
            throw new WPV_RuntimeExceptionWithMessage(
                '_validate_slug failed: invalid characters',
                __( 'The slug can only contain lowercase latin letters, numbers or dashes.', 'wpv-views' )
            );
        }

        if( empty( $sanitized_value ) ) {
            throw new WPV_RuntimeExceptionWithMessage(
                '_validate_slug failed: empty value',
                __( 'You can not leave the slug empty.', 'wpv-views' )
            );
        }

        $collision_data = array();
        if( WPV_Content_Template_Embedded::is_name_used( $sanitized_value, $this->id, $collision_data ) ) {
            switch( $collision_data['colliding_field'] ) {
                case 'post_name':
                    $exception_message = sprintf(
                        __( 'Another Content Template (%s) with that slug already exists. Please use another slug.', 'wpv-views' ),
                        sanitize_text_field( $collision_data['post_title'] )
                    );
                    break;
                case 'post_title':
                    $exception_message = __( 'Another Content Template already uses this slug value as it\'s title. Please use another slug.', 'wpv-views' );
                    break;
                case 'both':
                    $exception_message = __( 'Another Content Template already uses this slug value as it\'s slug and title. Please use another slug.', 'wpv-views' );
                    break;
                default:
                    $exception_message = __( 'Another item with that slug or title already exists. Please use another slug.', 'wpv-views' );
                    break;
            }
            throw new WPV_RuntimeExceptionWithMessage(
                '_validate_slug failed: name is already being used for another CT',
                $exception_message,
                WPV_RuntimeExceptionWithMessage::EXCEPTION_VALUE_ALREADY_USED
            );
        }

        return $sanitized_value;
    }


    /**
     * Post slug (a.k.a. post_name) setter.
     *
     * See _validate_slug().
     *
     * @param string $value New post slug.
     * @throws Exception, WPV_RuntimeExceptionWithMessage
     * @since 1.9
     */
    protected function _set_slug( $value ) {

        $sanitized_value = $this->_validate_slug( $value );

        $result = $this->update_post( array( 'post_name' => $sanitized_value ) );

        if( $result instanceof WP_Error ) {
            throw new Exception( '_set_title failed: WP_Error' );
        }
    }


    /**
     * Safe post slug setter.
     *
     * Sets a post slug in a safe way - it sanitizes the candidate value and if it's empty, it uses post title.
     * Then it ensures the uniqueness of a slug with wp_unique_post_slug.
     *
     * @param string $value Slug candidate.
     *
     * @since 1.9
     */
    protected function _set_slug_safe( $value ) {
        $slug_candidate = sanitize_title( $value );

        if( empty( $slug_candidate ) ) {
            $slug_candidate = sanitize_title( $this->title );
        }

        $this->slug = wp_unique_post_slug( $slug_candidate, $this->id, 'publish', WPV_Content_Template_Embedded::POST_TYPE, 0 );
    }


    /**
     * Content Template description setter.
     *
     * @param string $value New description. It will be sanitized before saving.
     *
     * @since 1.9
     */
    protected function _set_description( $value ) {
	    $sanitized_value = sanitize_text_field( $value );
        $this->update_postmeta( WPV_Content_Template_Embedded::POSTMETA_DESCRIPTION_KEY, $sanitized_value );
    }


    /**
     * Identical to _set_description().
     *
     * Implemented only as a complement to _get_description_raw() which differs from _get_description().
     *
     * @param string $value New description.
     *
     * @since 1.9
     */
	protected function _set_description_raw( $value ) {
		$this->description = $value;
	}


    /**
     * Content template output mode setter.
     *
     * Allowed values are:
     * - 'WP_mode' for default WordPress mode with auto-inserted paragraphs
     * - 'raw_mode' for "manual paragraphs" mode
     *
     * @param string $value New output mode.
     *
     * @since 1.9
     */
	protected function _set_output_mode( $value ) {

		static $allowed_values = array( 'WP_mode', 'raw_mode' );
		if( !in_array( $value, $allowed_values ) ) {
			throw new WPV_RuntimeExceptionWithMessage(
				'_set_output_mode failed: invalid value',
				__( 'Invalid output mode.', 'wpv-views' )
			);
		}

		$this->update_postmeta( WPV_Content_Template_Embedded::POSTMETA_OUTPUT_MODE, $value );
	}


    /**
     * Update Views settings of assigned CTs for single post types.
     *
     * For a given array of post types, ensure that those post types (and only those) have
     * this CT assigned as a template for single posts.
     *
     * WARNING, setting this overwrites global Views settings! It's not a really property of this
     * content template.
     *
     * Used by the Content Template edit page.
     *
     * @param $assigned_post_types Array of (existing) post type slugs.
     *
     * @since 1.9
     *
     * @throws InvalidArgumentException on invalid input.
     */
    protected function _set_assigned_single_post_types( $assigned_post_types ) {

        if( null == $assigned_post_types ) {
            $assigned_post_types = array();
        }

        if( !is_array( $assigned_post_types ) ) {
            throw new InvalidArgumentException( 'Array expected' );
        }

        // This will throw an exception if we're trying to change assignment for a non-existent post type.
        $post_types = get_post_types( array( 'public' => true ), 'names' );
        $this->check_allowed_loops( $assigned_post_types, $post_types );

        $this->update_content_template_assignment( $assigned_post_types, $post_types, WPV_Settings_Embedded::SINGLE_POST_TYPES_CT_ASSIGNMENT_PREFIX );
    }


    /**
     * Update Views settings of assigned CTs for custom post type archives
     *
     * For a given array of post types, ensure that those post types (and only those) have
     * this CT assigned as a template for post archives.
     *
     * WARNING, setting this overwrites global Views settings! It's not a really property of this
     * content template.
     *
     * Used by the Content Template edit page.
     *
     * @param $assigned_post_archives Array of (existing) post type slugs. Only custom post types with
     *     archives are accepted.
     *
     * @since 1.9
     *
     * @throws InvalidArgumentException on invalid input.
     */
    protected function _set_assigned_post_archives( $assigned_post_archives ) {

        if( null == $assigned_post_archives ) {
            $assigned_post_archives = array();
        }

        if( !is_array( $assigned_post_archives ) ) {
            throw new InvalidArgumentException( 'Array expected' );
        }

        $post_types = get_post_types( array( 'public' => true, '_builtin' => false, 'has_archive' => true ), 'names' );
        $this->check_allowed_loops( $assigned_post_archives, $post_types );

        $this->update_content_template_assignment( $assigned_post_archives, $post_types, WPV_Settings_Embedded::CPT_ARCHIVES_CT_ASSIGNMENT_PREFIX );
    }


    /**
     * Update Views settings of assigned CTs for taxonomy archives.
     *
     * For a given array of taxonomy slugs, ensure that those taxonomies (and only those) have
     * this CT assigned as a template for taxonomy archives.
     *
     * WARNING, setting this overwrites global Views settings! It's not a really property of this
     * content template.
     *
     * Used by the Content Template edit page.
     *
     * @param $assigned_taxonomy_archives Array of (existing) taxonomy slugs.
     *
     * @since 1.9
     *
     * @throws InvalidArgumentException on invalid input.
     */
    protected function _set_assigned_taxonomy_archives( $assigned_taxonomy_archives ) {

        if( null == $assigned_taxonomy_archives ) {
            $assigned_taxonomy_archives = array();
        }

        if( !is_array( $assigned_taxonomy_archives ) ) {
            throw new InvalidArgumentException( 'Array expected' );
        }

        // Get allowed taxonomy slugs
        global $WPV_view_archive_loop;
        $taxonomy_archive_loops = $WPV_view_archive_loop->get_archive_loops( 'taxonomy' );
        $taxonomy_slugs = array();
        foreach( $taxonomy_archive_loops as $taxonomy_loop ) {
            $taxonomy_slugs[] = $taxonomy_loop['slug'];
        }

        // Throw an exception if we're trying to change assignment for non-existent taxonomy.
        $this->check_allowed_loops( $assigned_taxonomy_archives, $taxonomy_slugs );

        $this->update_content_template_assignment( $assigned_taxonomy_archives, $taxonomy_slugs, WPV_Settings_Embedded::TAXONOMY_ARCHIVES_CT_ASSIGNMENT_PREFIX );
    }


    /**
     * Set template content.
     *
     * If the content has changed, update the post and afterwards also register WPML strings, update field values postmeta
     * and execute the wpv_action_wpv_save_item action.
     *
     * This method, as opposed to _set_content_raw(), also checks if all [types] shortcodes have both opening and closing tags.
     * Warning: That may not be desired behaviour in some cases (like CT cloning or similar operations).
     *
     * @param string $post_content New template content.
     *
     * @throws RuntimeException if CT couldn't have been updated or WPV_RuntimeExceptionWithMessage when the input has
     *     syntax errors (only basic check is performed).
     *
     * @since 1.9
     */
    protected function _set_content( $post_content ) {

        $original_post_content = $this->content;

        if ( $original_post_content != $post_content ) {

            // Check if all [types] shortcodes have both opening and closing tags. If not, throw an exception.
            // Details about why is this important: http://wp-types.com/faq/why-do-types-shortcodes-have-to-be-closed/
            // @todo we might want to add more thorough syntax checks
            $open_tags = substr_count( $post_content, '[types' );
            $close_tags = substr_count( $post_content, '[/types' );
            if ( $close_tags < $open_tags ) {
                throw new WPV_RuntimeExceptionWithMessage(
                    '_set_content failed: single-ended types shortcodes',
                    sprintf(
                        __( 'This template includes single-ended shortcodes. Please close all shortcodes to avoid processing errors. %sRead more%s', 'wpv-views' ),
                        '<a href="http://wp-types.com/faq/why-do-types-shortcodes-have-to-be-closed/?utm_source=viewsplugin&utm_campaign=views&utm_medium=edit-content-template-not-closed-shortcodes-message&utm_term=Read more" target="_blank">',
                        ' &raquo;</a>'
                    )
                );
            }

            $this->content_raw = $post_content;
        }
    }


    /**
     * Set template content without checking syntax.
     *
     * If the content has changed, update the post and afterwards also register WPML strings, update field values postmeta
     * and execute the wpv_action_wpv_save_item action.
     *
     * @param string $post_content New template content.
     *
     * @throws RuntimeException if CT couldn't have been updated.
     *
     * @since 1.9
     */
    protected function _set_content_raw( $post_content ) {

        $original_post_content = $this->content;

        if ( $original_post_content != $post_content ) {

            // Update the post and throw on error.
            $update_result = $this->update_post( array( 'post_content' => $post_content ) );

            if( $update_result instanceof WP_Error ) {
                throw new RuntimeException( '_set_content failed: couldn\'t update_post.' );
            }

            wpv_register_wpml_strings( $post_content );

        }
    }


    /**
     * Template Extra CSS setter.
     *
     * @param string $value New CSS
     * @since 1.9
     */
    protected function _set_template_extra_css( $value ) {
        $this->update_postmeta( WPV_Content_Template_Embedded::POSTMETA_TEMPLATE_EXTRA_CSS, $value );
    }


    /**
     * Template Extra JS code setter.
     *
     * @param string $value New JS code
     * @since 1.9
     */
    protected function _set_template_extra_js( $value ) {
        $this->update_postmeta( WPV_Content_Template_Embedded::POSTMETA_TEMPLATE_EXTRA_JS, $value );
    }


    /**
     * Set Content Template post status.
     *
     * Only 'publish' and 'trash' are allowed.
     *
     * @param string $value New post status.
     * @throws InvalidArgumentException on invalid input.
     *
     * @since 1.9
     */
    protected function _set_post_status( $value ) {
        $allowed_post_statuses = array( 'publish', 'trash' );
        if( !in_array( $value, $allowed_post_statuses ) ) {
            throw new InvalidArgumentException(
                '_set_post_status: invalid post status, only ' . implode( ', ', $allowed_post_statuses ) . ' are allowed for Content Templates.'
            );
        }

        $this->update_post( array( 'post_status' => $value ) );
    }


    /**
     * Set content template "owner" View/WPA.
     *
     * @param int $value ID of a View/WPA that uses this CT as loop output template ("owns" this CT).
     * Zero if no owner exists.
     *
     * @since 1.9
     */
    protected function _set_loop_output_id( $value ) {
        $this->update_postmeta( WPV_Content_Template_Embedded::POSTMETA_LOOP_OUTPUT_ID, (int) $value );
    }


    /* ************************************************************************* *\
        Helper functions
    \* ************************************************************************* */


    /**
     * Check if all given loop names are allowed. Throw an exception if they're not.
     *
     * @param $given_loops Array of given loop slugs.
     * @param $allowed_loops Array of allowed loop slugs.
     *
     * @throws InvalidArgumentException if there is one or more unallowed loops.
     *
     * @since 1.9
     */
    private function check_allowed_loops( $given_loops, $allowed_loops ) {
        $unknown_loops = array_diff( $given_loops, $allowed_loops );
        if( !empty( $unknown_loops ) ) {
            throw new InvalidArgumentException( 'Unknown loops: ' . implode( ', ', $unknown_loops ) );
        }
    }


    /**
     * Update global Views settings about CT assigned to certain post types or taxonomies.
     *
     * Ensure that given post types (and only those) have this CT assigned in a role given by the setting prefix.
     *
     * @param $assigned_post_types Array of ALL post type or taxonomy slugs that should have this CT assigned.
     * @param $all_post_types Array of all post types or taxonomies that should be checked.
     * @param string $setting_prefix Setting prefix determining the role in which CT will (or will not) be assigned.
     *     Values that make sense are:
     *     - WPV_Settings_Embedded::SINGLE_POST_TYPES_CT_ASSIGNMENT_PREFIX
     *     - WPV_Settings_Embedded::CPT_ARCHIVES_CT_ASSIGNMENT_PREFIX
     *     - WPV_Settings_Embedded::TAXONOMY_ARCHIVES_CT_ASSIGNMENT_PREFIX
     *
     * @since 1.9
     */
    private function update_content_template_assignment( $assigned_post_types, $all_post_types, $setting_prefix ) {
        global $WPV_settings;

        foreach( $all_post_types as $post_type ) {
            $setting_name = $setting_prefix . $post_type;
            $is_post_type_assigned = in_array( $post_type, $assigned_post_types );

            if( ( $WPV_settings[ $setting_name ] == $this->id ) && ! $is_post_type_assigned ) {
                // This post type is assigned to this CT and it shouldn't be.
                $WPV_settings[ $setting_name ] = 0;
            } else if( $is_post_type_assigned ) {
                // This post type should be assigned to this CT.
                $WPV_settings[ $setting_name ] = $this->id;
            }
        }

        $WPV_settings->save();
    }

}