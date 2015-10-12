<?php
/**
 * Views plugin object wrappers
 *
 * A collection of classes for encapsulating objects of the Views plugin - Views, WordPress Archives and Content
 * Templates. These classes should provide easy access to commonly used properties or performed operations.
 *
 * The inheritance structure is following:
 *
 * - WPV_Post_Object_Wrapper
 *    .- WPV_View_Base
 *    .    - WPV_View_Embedded
 *    .    - WPV_WordPress_Archive_Embedded
 *    .- WPV_Content_Template_Embedded
 *
 * The *_Embedded classes are meant to be extended in full Views.
 *
 * @todo When we drop PHP 5.2 support, replace "self::", "WPV_View_Base::" etc. with "static::".
 *     Also get_postmeta_defaults() should be no longer necessary.
 *     For details refer to @link https://stackoverflow.com/questions/13613594/overriding-class-constants-vs-properties
 *
 * @since 1.8
 */


/**
 * Wraps a WP_Post object.
 *
 * Provides basic functionality to wrap a WP_Post object and access it's properties and metadata easily.
 *
 * @since 1.8
 */
abstract class WPV_Post_Object_Wrapper {

    /**
     * @var int ID of the object. After constructor finishes, this should be allways set.
     */
    protected $object_id = null;


    /**
     * @var WP_Post|null Post object or null if it was not yet fetched from the database. This should not be
     * accessed directly, but through $this->post().
     */
    protected $post = null;


    /**
     * Get the encapsulated post object.
     *
     * @return WP_Post
     *
     * @throws InvalidArgumentException When the post can't be retrieved.
     */
    abstract protected function &post();


    /**
     * Return array of default post meta.
     *
     * This is a workaround about not being able to reliably use self:: because of PHP 5.2.
     *
     * @return array Default post meta.
     */
    abstract protected function get_postmeta_defaults();


    /* ************************************************************************* *\
        Static methods
    \* ************************************************************************* */


    /**
     * Check if a name is already in use for given post type.
     *
     * Returns true if the name is used either as a post title or a post slug.
     *
     * @param string $name Name to check.
     * @param string $post_type Post type slug.
     * @param int $except_id Post ID that should be excluded from checking.
     * @param array &$collision_data (since 1.10) If there is a name collision, this will be set to an array:
     *     - id: ID of the other post
     *     - colliding_field: Where has the collision with $name happened: 'post_title', 'post_name' or 'both'
     *     - post_title: Title of the other post
     *
     * @return bool True if name is already used elsewhere, false otherwise.
     */
    public static function is_name_used_base( $name, $post_type, $except_id = 0, &$collision_data = null ) {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT post_title, post_name, ID
        	FROM {$wpdb->posts}
        	WHERE ( post_title = %s OR post_name = %s )
        		AND post_type = %s
        		AND ID != %d
        	LIMIT 1",
            $name,
            $name,
            $post_type,
            $except_id
        );

        $existing_post = $wpdb->get_row( $query );

        $collision_exists = ( null != $existing_post );
        if( $collision_exists ) {
            $title_collides = ( $name == $existing_post->post_title );
            $slug_collides = ( $name == $existing_post->post_name );
            if( $title_collides && $slug_collides ) {
                $colliding_field = 'both';
            } else if( $title_collides ) {
                $colliding_field = 'post_title';
            } else if( $slug_collides ) {
                $colliding_field = 'post_name';
            }
            /** @noinspection PhpUndefinedVariableInspection */
            $collision_data = array(
                'id' => $existing_post->ID,
                'colliding_field' => $colliding_field,
                'post_title' => $existing_post->post_title
            );
        }

        return $collision_exists;
    }


    /* ************************************************************************* *\
        Getter and setter
    \* ************************************************************************* */


    /**
     * Dynamic attribute getter.
     *
     * Returns the value of custom getter with the name _get_{$attribute_name}(), if it exists. Otherwise
     * null is returned.
     *
     * @param string $attribute_name Name of the attribute.
     *
     * @return mixed Value of the attribute or null if it doesn't exist.
     */
    public function __get( $attribute_name ) {

        // Custom getter
        $method_name = '_get_' . $attribute_name;
        if( method_exists( $this, $method_name ) )  {
            return $this->$method_name();
        }

        // Everything has failed
        return null;
    }


    /**
     * Dynamic attribute setter.
     *
     * The attribute is set in following way:
     * 1. By a custom setter with the name in the format _set_{$attribute_name}, if such exists.
     * 2. As a postmeta value, if the key is defined in self::$postmeta_defaults.
     *
     * If the attribute cannot be set, an InvalidArgumentException is thrown. Note that this is well-defined behaviour.
     *
     * @param string $attribute_name Name of the attribute.
     * @param mixed $value Value to be set.
     *
     * @throws InvalidArgumentException if the attribute value cannot be set.
     */
    public function __set( $attribute_name, $value ) {

        // Custom setter
        $method_name = '_set_' . $attribute_name;
        if( method_exists( $this, $method_name ) )  {
            $this->$method_name( $value );
            return;
        }

        // If the key is defined in postmeta defaults, set the value as postmeta
        /*if( in_array( $attribute_name, $this->get_postmeta_defaults() ) ) {
            update_post_meta( $this->object_id, $attribute_name, $value );
            $this->maybe_after_update_action();
        }*/

        // The value can't be set.
        throw new InvalidArgumentException( "Invalid attribute name: $attribute_name" );
    }


    /* ************************************************************************* *\
        Post updating, postmeta access
    \* ************************************************************************* */


    /**
     * @var bool If true, after update action will not be executed until
     * resume_after_update_actions().
     *
     * @since 1.9
     */
    private $is_after_update_action_deferred = false;


    /**
     * @var bool Indicate whether after update action should be called on
     * resume_after_update_actions().
     *
     * @since 1.9
     */
    private $is_after_update_action_needed = false;


    /**
     * Stop executing after update action automatically after each
     * property change.
     *
     * By calling this method you are becoming responsible for either
     * invoking the after update action manually or returning to the
     * automatic mode via resume_after_update_actions().
     *
     * @since 1.9
     */
    public function defer_after_update_actions() {
        $this->is_after_update_action_deferred = true;
    }


    /**
     * Run the after update action manually.
     *
     * @since 1.9
     */
    public function after_update_action() {

        do_action( 'wpv_action_wpv_save_item', $this->id );

        $this->is_after_update_action_needed = false;
    }


    /**
     * Run the after update action or indicate it needs to be executed,
     * depending on the current mode (automatic or deferred).
     *
     * @since 1.9
     */
    public function maybe_after_update_action() {
        if( $this->is_after_update_action_deferred ) {
            $this->is_after_update_action_needed = true;
        } else {
            $this->after_update_action();
        }
    }


    /**
     * Return to the automatic mode of running after update action and run
     * the action if it is needed.
     *
     * @since 1.9
     */
    public function resume_after_update_actions() {
        $this->is_after_update_action_deferred = false;
        if( $this->is_after_update_action_needed ) {
            $this->after_update_action();
        }
    }


    /**
     * Update View's post record in the database.
     *
     * It works as wp_update_post() with only few differences:
     *
     * - The ID argument is not mandatory.
     * - If an ID is provided, it must match ID of this View.
     * - After updating, the privately stored WP_Post object is discarded.
     *
     * @param array $args Array of arguments for wp_update_post();
     *
     * @return int|WP_Error ID of the updated post or a WP_Error object.
     */
    public function update_post( $args ) {

        if( !is_array( $args ) ) {
            throw new InvalidArgumentException( 'args is not an array.' );
        }

        if( in_array( 'ID', $args ) && ( $args['ID'] != $this->object_id ) ) {
            throw new InvalidArgumentException( 'Invalid ID given as an argument' );
        }

        // Make sure that wp_update_post gets the ID it needs.
        $args['ID'] = $this->id;

        $updated = wp_update_post( $args, true );

        // Force to reload post from cache
        $this->post = null;

        $this->maybe_after_update_action();

        return $updated;
    }


    /**
     * Get postmeta from this post.
     *
     * Method for getting single-value postmeta. Return value is determined in following way:
     *
     * 1. Postmeta value, if it exists.
     * 2. Default postmeta value in self::postmeta_defaults, if the attribute name is defined there.
     * 3. null
     *
     * @param string $key Meta key.
     * @return mixed
     *
     * @since 1.9
     */
    public function get_postmeta( $key ) {

        // Postmeta
        $meta_value = get_post_meta( $this->object_id, $key, true );

        if( '' !== $meta_value ) {
            /* get_post_meta() returns an empty string if no postmeta with given key is present.
             * So, now we know for sure it is. */
            return $meta_value;
        }

        /* Now we know that postmeta either doesn't exist or it is an empty string. Which one is it?
         * Note: No additional query needed here, everything is cached in WP core. */
        if( metadata_exists( 'post', $this->object_id, $key ) ) {
            // It was indeed an empty string.
            return '';
        }

        // Look for a default value
        $postmeta_defaults = $this->get_postmeta_defaults();
        if( array_key_exists( $key, $postmeta_defaults ) ) {
            return $postmeta_defaults[ $key ];
        }

        // Everything else has failed
        return null;
    }


    /**
     * Update postmeta of this post.
     *
     * Shortcut method for setting single-value postmeta.
     *
     * @param string $key Meta key.
     * @param string $value New meta value.
     * @return bool Same as update_post_meta(), except that it returns true instead of meta_id.
     */
    public function update_postmeta( $key, $value ) {
        $ret = update_post_meta( $this->object_id, $key, $value );

        $this->maybe_after_update_action();

        if( is_numeric( $ret ) ) {
            // $ret is new meta_id, but we will just indicate success. There should be no
            // reason to need meta_id.
            return true;
        } else {
            return $ret;
        }
    }


    /* ************************************************************************* *\
        Update transactions
    \* ************************************************************************* */


    /**
     * This method provides a very generic mechanism for updating multiple properties
     * in a transaction - that means, either all of them get updated or none does.
     *
     * Of course, support for individual properties is necessary: The class has to
     * contain validation methods _validate_{$property_name} that will accept
     * the candidate value and *throw an exception* if the validation fails. It's
     * return value is ignored here.
     *
     * If the property has no validation method, it is assumed that any value will
     * be accepted.
     *
     * To be more exact, the general rule is that if a value passes through validation
     * (or if validation method doesn't exist), it should also pass through the setter
     * without any exceptions being thrown.
     *
     * Failure to follow this rule might cause "partial success" of this method, which
     * is exactly what it tries to avoid.
     *
     * @param $data Array with update data, property values indexed by their names.
     *     array( 'property_one' => $any_value, 'property_two' => ... )
     * @param bool $break_on_error Define whether to stop validation on first error or continue
     *     validating other properties even after it's clear that the transaction will fail.
     *     Default is true.
     *
     * @return array(
     *      Results of the action.
     *      @type bool $success True if *all* properties have been successfully updated.
     *      @type array $error_messages Error messages to display to the user (if there are any),
     *          indexed by names of properties that caused them. Each element is an array(
     *              @type string $message Text of the message.
     *              @type int $code Message code (zero means not set).
     *          ).
     *      @type bool $partial If this is true, it means that some properties have been
     *          updated and some not. This can be true only if $success is false.
     *      @type array $updated_properties This is set only if $partial is true, and in such case
     *          it contains names of properties that *have* been successfully updated.
     *      @type string $first_error_message For the convenience, if there are any error messages,
     *          this will contain the text of the first one.
     *    )
     *
     * @since 1.9
     */
    public function update_transaction( $data, $break_on_error = true ) {

        if( !is_array( $data ) ) {
            // ...nothing to do here!
            return array( 'success' => false, 'partial' => false, 'error_messages' => array() );
        }

        // Try to validate all properties without updating first.
        $can_update_everything = true;
        $error_messages = array();
        $first_error_message = null;
        foreach( $data as $property_name => $value ) {

            // Fail if property doesn't have a setter
            $setter_method_name = '_set_' . $property_name;
            if( !method_exists( $this, $setter_method_name ) ) {
                $can_update_everything = false;
                continue;
            }

            $validate_method_name = '_validate_' . $property_name;
            if( method_exists( $this, $validate_method_name ) )  {
                // we have a validation method for this property
                try {

                    $this->$validate_method_name( $value );

                } catch(WPV_RuntimeExceptionWithMessage $e) {

                    // Validation has failed with a message.
                    $can_update_everything = false;
                    $error_messages[ $property_name ] = array(
                        'message' => $e->getUserMessage(),
                        'code' => $e->getCode()
                    );

                    $first_error_message = (null == $first_error_message) ? $e->getUserMessage() : $first_error_message;

                    if( $break_on_error ) {
                        // We can stop trying now.
                        break;
                    } else {
                        continue;
                    }

                } catch(Exception $e) {

                    // Validation has failed without a message. So we will keep trying
                    // to validate other properties and perhaps some other will give us
                    // something to display.
                    $can_update_everything = false;
                    continue;
                }
            } else {
                // no validation method available, we assume everything is ok
            }

        }

        // Abort the transaction if anything was not validated.
        if( !$can_update_everything ) {
            $result = array( 'success' => false, 'partial' => false, 'error_messages' => $error_messages );
            if( null != $first_error_message ) {
                $result['first_error_message'] = $first_error_message;
            }
            return $result;
        }

        // Commit the transaction
        $was_after_update_action_deferred = $this->is_after_update_action_deferred;
        $this->defer_after_update_actions();

        $did_update_everything = true;
        $updated_properties = array();
        foreach( $data as $property_name => $value ) {
            $setter_method_name = '_set_' . $property_name;
            // The setter still might throw something. It shouldn't, but we will not count on that.
            try {
                $this->$setter_method_name( $value );
                // Update performed
                $updated_properties[] = $property_name;
            } catch(WPV_RuntimeExceptionWithMessage $e) {
                $did_update_everything = false;
                $error_messages[ $property_name ] = array(
                    'message' => $e->getUserMessage(),
                    'code' => $e->getCode()
                );
                $first_error_message = (null == $first_error_message) ? $e->getUserMessage() : $first_error_message;
                continue;
            } catch(Exception $e) {
                $did_update_everything = false;
                continue;
            }
        }

        if( !$was_after_update_action_deferred ) {
            $this->resume_after_update_actions();
        }

        // todo handle the situation when first property update fails

        $ret = array(
            'success' => $did_update_everything,
            'partial' => !$did_update_everything,
            'error_messages' => $error_messages
        );
        if( !$did_update_everything ) {
            $ret['updated_properties'] = $updated_properties;
        }
        if( null != $first_error_message ) {
            $ret['first_error_message'] = $first_error_message;
        }

        return $ret;
    }


    /* ************************************************************************* *\
        Custom getters and setters
    \* ************************************************************************* */


    /**
     * @return string The post status. @see http://codex.wordpress.org/Function_Reference/get_post_status
     */
    protected function _get_post_status() {
        return $this->post()->post_status;
    }


    /**
     * True, if the post is published. False otherwise.
     * @return bool
     */
    protected function _get_is_published() {
        return ( 'publish' == $this->post_status );
    }


    /**
     * True, if the post is trashed. False otherwise.
     * @return bool
     */
    protected function _get_is_trashed() {
        return ( 'trash' == $this->post_status );
    }


    /**
     * Get post title.
     * @return string
     */
    protected function _get_title() {
        return sanitize_text_field( $this->post()->post_title );
    }


    /**
     * Post slug.
     * @return string
     */
    protected function _get_slug() {
        return sanitize_text_field( $this->post()->post_name );
    }


    /**
     * Counterpart of the _set_slug_safe() in WPV_Content_Template.
     *
     * @return string
     * @since 1.9
     */
    protected function _get_slug_safe() {
        return $this->slug;
    }


    /**
     * Post ID.
     * @return int
     */
    protected function _get_id() {
        return (int) $this->object_id;
    }


    /**
     * Post content.
     * @return string
     */
    protected function _get_content() {
        return $this->post()->post_content;
    }
}