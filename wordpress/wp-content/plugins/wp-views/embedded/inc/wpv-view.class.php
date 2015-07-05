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


    /**
     * Dynamic attribute getter.
     *
     * The attribute value is determined in following way:
     * 1. Return value of custom getter with the name _get_{$attribute_name}(), if it exists.
     * 2. Postmeta value, if it exists.
     * 3. Default postmeta value in self::postmeta_defaults, if the attribute name is defined there.
     * 4. null
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

        // Postmeta
        $meta_value = get_post_meta( $this->object_id, $attribute_name, true );
        if( '' !== $meta_value ) {
            /* get_post_meta() returns an empty string if no postmeta with given key is present.
             * So, now we know for sure it is. */
            return $meta_value;
        }

        /* Now we know that postmeta either doesn't exist or it is an empty string. Which one is it?
         * Note: No additional query needed here, everything is cached in WP core. */
        if( metadata_exists( 'post', $this->object_id, $attribute_name ) ) {
            // It was indeed an empty string.
            return '';
        }

        // Look for a default value
        $postmeta_defaults = $this->get_postmeta_defaults();
        if( in_array( $attribute_name, $postmeta_defaults ) ) {
            return $postmeta_defaults[ $attribute_name ];
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
        if( in_array( $attribute_name, $this->get_postmeta_defaults() ) ) {
            update_post_meta( $this->object_id, $attribute_name, $value );
        }

        // The value can't be set.
        throw new InvalidArgumentException( "Invalid attribute name: $attribute_name" );
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
        $args['ID'] = $this->object_id;

        $updated = wp_update_post( $args, true );

        // Force to reload post from cache
        $this->post = null;

        return $updated;
    }


    /* ************************************************************************* *\
        Custom getters
    \* ************************************************************************* */


    /**
     * @return string The post status. @see http://codex.wordpress.org/Function_Reference/get_post_status
     */
    private function _get_post_status() {
        return $this->post()->post_status;
    }


    /**
     * @return bool True, if the post is published. False otherwise.
     */
    private function _get_is_published() {
        return ( 'publish' == $this->post_status );
    }


    /**
     * @return bool True, if the post is trashed. False otherwise.
     */
    private function _get_is_trashed() {
        return ( 'trash' == $this->post_status );
    }


    /**
     * @return string Post title.
     */
    private function _get_title() {
        return sanitize_text_field( $this->post()->post_title );
    }


    /**
     * @return int Post ID.
     */
    private function _get_id() {
        return (int) $this->object_id;
    }



}


/**
 * Base class for 'view' post type, that means Views and WPAs.
 *
 * Contains code common for both, mostly related to "view query mode", a value determining what kind of object
 * it is.
 *
 * @since 1.8
 */
abstract class WPV_View_Base extends WPV_Post_Object_Wrapper {


    /* ************************************************************************* *\
            Constants and static methods
    \* ************************************************************************* */


    /**
     * View post type slug.
     */
    const POST_TYPE = 'view';


    /**
     * Determine whether View/WPA with given ID exists.
     *
     * @param int $view_id ID of the View to check.
     *
     * @return bool True if post with given ID exists and if it's a View.
     */
    public static function is_valid( $view_id ) {
        /* Note: This should not cause a redundant database query. Post objects are cached by WP core, so this one was
         * either already loaded or it has to be loaded now and will be reused in the future. */
        return WPV_View_Base::is_wppost_view( WP_Post::get_instance( $view_id ) );
    }


    /**
     * For a given object, determine if it's a valid WP_Post object representing a View/WPA.
     *
     * @param mixed $post Value to check.
     *
     * @return bool True if $post is a valid WP_Post object representing a View/WPA, false otherwise.
     */
    public static function is_wppost_view( $post ) {
        return ( ( $post instanceof WP_Post ) && ( $post->ID > 0 ) && ( WPV_View_Base::POST_TYPE == $post->post_type ) );
    }


    /**
     * Determine if the object is used as a WordPress Archive.
     *
     * We cannot rely only on the value of "view query mode" stored in postmeta, because some filters need to be
     * applied along the way. Current implementation causes a get_post_meta() call.
     *
     * @todo can this be done better, without another query or filters?
     *
     * @param int $view_id ID of the object ('view' post type).
     *
     * @return bool True if it is a WPA, false otherwise.
     */
    public static function is_archive_view( $view_id ) {
        global $WP_Views;
        return $WP_Views->is_archive_view( $view_id );
    }


    /**
     * Create an appropriate wrapper for View or WPA post object.
     *
     * Decides by self::is_archive_view() if it's a WPA.
     *
     * @param int|WP_Post $view Post ID or post object.
     *
     * @return null|WPV_View_Embedded|WPV_WordPress_Archive_Embedded The appropriate wrapper or null on error.
     */
    public static function create( $view ) {
        if( is_integer( $view ) ) {
            $post = WP_Post::get_instance( $view );
        } else {
            $post = $view;
        }

        if( ! WPV_View_Base::is_wppost_view( $post ) ) {
            return null;
        }

        try {
            if ( WPV_View_Base::is_archive_view( $post->ID ) ) {
                return new WPV_WordPress_Archive_Embedded( $post );
            } else {
                return new WPV_View_Embedded( $post );
            }
        } catch( Exception $ex ) {
            return null;
        }
    }



    /* ************************************************************************* *\
            Methods
    \* ************************************************************************* */


    /**
     * Class constructor. Create an instance from View ID or WP_Post object representing a View.
     *
     * Please note that WP_Post object will be validated and an exception is thrown on error.
     * However, if only an ID is provided, no such validation takes place here (in order to avoid potentionally
     * unnecessary database query). So, the ID must be validated before (by WPV_View_Base::is_valid() or by other
     * means), otherwise the behaviour of this object is undefined. Also note that "view query mode" is not checked
     * here. If you are not certain about it's value, use self::create().
     *
     * @param int|WP_Post $view View ID or a WP_Post object.
     *
     * @throws InvalidArgumentException when provided argument is not a WP_Post instance representing a View or an
     * integer that *might* be a View ID.
     */
    public function __construct( $view ) {
        if( $view instanceof WP_Post ) {
            // Let's check that we indeed have a valid post and View post type
            if( WPV_View_Base::is_wppost_view( $view ) ) {
                // Store the data we got;
                $this->object_id = $view->ID;
                $this->post = clone( $view );
            } else {
                throw new InvalidArgumentException( "Invalid WP_Post object provided (not a View): " . print_r( $view, true ) );
            }
        } elseif( is_integer( $view ) && $view > 0 ) {
            // We assume (!) this is a valid View ID.
            $this->object_id = $view;
        } else {
            throw new InvalidArgumentException( "Invalid argument provided (not a View or ID): " . print_r( $view, true ) );
        }
    }


    /**
     * Get the post object representing this View.
     *
     * @return WP_Post Post object.
     *
     * @throws InvalidArgumentException if the post object cannot be retrieved or is invalid.
     */
    protected function &post() {

        if( null == $this->post ) {
            // Requesting WP_Post object, but we haven't got it yet.
            $post = WP_Post::get_instance( $this->object_id );
            if( WPV_View_Base::is_wppost_view( $post ) ) {
                $this->post = $post;
            } else {
                throw new InvalidArgumentException( 'Invalid View ID' );
            }
        }

        return $this->post;
    }


    /**
     * @var null|array Cache for View settings.
     */
    protected $views_settings_cache = null;


    /**
     * Obtain View settings. Optional caching.
     *
     * The proper way to obtain View settings is through $WP_Views->get_view_settings(), which applies some filters
     * on it. We may not need to apply them more than once.
     *
     * @param bool $use_cached If true, prefer cached version. Otherwise no caching.
     *
     * @return array View settings.
     */
    protected function get_view_settings( $use_cached = false ) {
        if( !$use_cached || ( null == $this->views_settings_cache ) ) {
            global $WP_Views;
            $this->views_settings_cache = $WP_Views->get_view_settings( $this->object_id );
        }

        return $this->views_settings_cache;
    }


    /* ************************************************************************* *\
            Custom getters
    \* ************************************************************************* */


    /**
     * @return string View description.
     */
    protected function _get_description() {
        return esc_html( $this->_wpv_description ); // postmeta
    }


    /**
     * Get cached(!) version of View settings array.
     *
     * Please use this only when you are sure you will not break anything by caching.
     *
     * @return array View settings.
     */
    protected function _get_settings() {
        return $this->get_view_settings( true );
    }


    /**
     * Get "query mode", a value determining what kind of object this is.
     *
     * Allowed values are 'normal', 'archive' and 'layouts-loop'. The value will be an empty string if the query mode
     * is not set for this object (which should never happen, though).
     *
     * @return string
     *
     * @since 1.8
     */
    protected function _get_query_mode() {
        $settings = $this->settings; // to avoid PHP notice
        return wpv_getarr( $settings, 'view-query-mode', '', array( 'normal', 'archive', 'layouts-loop' ) );
    }


    /**
     * @return string|void Label for the object depending on "view query mode". Empty string when it's invalid.
     */
    protected function _get_query_mode_display_name() {
        switch( $this->query_mode ) {
            case 'normal':
                return __( 'View', 'wpv-views' );
            case 'archive':
            case 'layouts-loop':
                return __( 'WordPress Archive', 'wpv-views' );
            default:
                // should never happen
                return '';
        }
    }

}


/**
 * Represents a single View.
 *
 * The embedded version of the wrapper. Concentrates on getters and get_ methods only.
 *
 * @since 1.8
 */
class WPV_View_Embedded extends WPV_View_Base {


    /**
     * See parent class constructor description.
     *
     * @param int|WP_Post $view View post object or ID.
     */
    public function __construct( $view ) {
        parent::__construct( $view );
    }


    /**
     * @var array Default postmeta for the View.
     *
     * Note that this should contain all postmeta keys a View can have (if they're not generic).
     *
     * @todo Add missing default values.
     * @todo Add description to default values.
     */
    protected static $postmeta_defaults = array(
            '_wpv_description' => '' );


    /**
     * @return array Default postmeta for the View.
     */
    protected function get_postmeta_defaults() {
        return WPV_View_Embedded::$postmeta_defaults;
    }



    /* ************************************************************************* *\
            Custom getters
    \* ************************************************************************* */


    /**
     * @var null|string Cache for the content_summary property.
     */
    private $content_summary_cache = null;


    /**
     * @return string "Content summary" of a View, stating what and how it will show.
     */
    protected function _get_content_summary() {
        if( empty( $this->content_summary_cache ) ) {
            $this->content_summary_cache = sanitize_text_field( apply_filters( 'wpv-view-get-content-summary', '', $this->object_id, $this->settings ) );
        }
        return $this->content_summary_cache;
    }


}



/**
 * Represents a single WordPress Archive
 *
 * @since 1.8
 */
final class WPV_WordPress_Archive_Embedded extends WPV_View_Base {

    /**
     * See parent class constructor description.
     *
     * @param int|WP_Post $wpa WPA post object or ID.
     */
    public function __construct( $wpa ) {
        parent::__construct( $wpa );
    }


    /**
     * @var array Default postmeta for the WPA.
     *
     * Note that this should contain all postmeta keys a WPA can have (if they're not generic).
     *
     * @todo Add missing default values.
     * @todo Add description to default values.
     */
    protected static $postmeta_defaults = array();


    /**
     * @return array Default postmeta for the WPA.
     */
    protected function get_postmeta_defaults() {
        return WPV_WordPress_Archive_Embedded::$postmeta_defaults;
    }


    /* ************************************************************************* *\
            Custom methods
    \* ************************************************************************* */


    /**
     * Get information about loops that have this WPA assigned as archive template.
     *
     * Take a look at $WPV_view_archive_loop->get_archive_loops() for better understanding of what is being returned.
     *
     * @param string $loop_type Relevant loop type. Possible values are the same as for $WPV_view_archive_loop->get_archive_loops().
     *
     * @return array Array of loop information.
     */
    public function get_assigned_loops( $loop_type = 'all' ) {

        global $WPV_view_archive_loop;
        $loops = $WPV_view_archive_loop->get_archive_loops( $loop_type, true );

        $selected_loops = array();
        foreach( $loops as $loop ) {
            if( $loop['wpa'] == $this->object_id ) {
                $selected_loops[] = $loop;
            }
        }

        return $selected_loops;
    }
}


/**
 * Wrapper for a Content Template.
 *
 * @since 1.8
 */
final class WPV_Content_Template_Embedded extends WPV_Post_Object_Wrapper {


    /**
     * Content template post type.
     */
    const POST_TYPE = 'view-template';


    /**
     * Constructor. Create an instance from Content Template ID or WP_Post object representing a CT.
     *
     * Please note that WP_Post object will be validated and an exception is thrown on error.
     * However, if only an ID is provided, no such validation takes place here (in order to avoid potentionally
     * unnecessary database query). So, the ID must be validated before (by WPV_Content_Template_Embedded::is_valid() or
     * by other means), otherwise the behaviour of this object is undefined.
     *
     * @param $content_template CT ID (integer) or a WP_Post object.
     *
     * @throws InvalidArgumentException when provided argument is not a WP_Post instance representing a CT or an
     * integer that *might* be a CT ID.
     */
    public function __construct( $content_template ) {
        if( $content_template instanceof WP_Post ) {
            // Let's check that we indeed have a valid post and CT post type
            if( WPV_Content_Template_Embedded::is_wppost_ct( $content_template ) ) {
                // Store the data we got;
                $this->object_id = $content_template->ID;
                $this->post = clone( $content_template );
            } else {
                throw new InvalidArgumentException( "Invalid WP_Post object provided (not a Content Template): " . print_r( $content_template, true ) );
            }
        } elseif( is_integer( $content_template ) && $content_template > 0 ) {
            // We assume (!) this is a valid View ID.
            $this->object_id = $content_template;
        } else {
            throw new InvalidArgumentException( "Invalid argument provided (not a CT or ID): " . print_r( $content_template, true ) );
        }
    }


    /**
     * For a given object, determine if it's a valid WP_Post object representing a Content Template.
     *
     * @param mixed $post Value to check.
     *
     * @return bool True if $post is a valid WP_Post object representing a CT, false otherwise.
     */
    public static function is_wppost_ct( $post ) {
        return ( ( $post instanceof WP_Post ) && ( $post->ID > 0 ) && ( WPV_Content_Template_Embedded::POST_TYPE == $post->post_type ) );
    }


    /**
     * Determine whether CT with given ID exists.
     *
     * @param int $ct_id ID of the CT to check.
     *
     * @return bool True if post with given ID exists and if it's a CT.
     */
    public static function is_valid( $ct_id ) {
        /* Note: This should not cause a redundant database query. Post objects are cached by WP core, so this one was
         * either already loaded or it has to be loaded now and will be reused in the future. */
        return WPV_Content_Template_Embedded::is_wppost_ct( WP_Post::get_instance( $ct_id ) );
    }


    /**
     * Get the post object representing this Content Template.
     *
     * @return WP_Post Post object.
     *
     * @throws InvalidArgumentException if the post object cannot be retrieved or is invalid.
     */
    protected function &post() {

        if( null == $this->post ) {
            // Requesting WP_Post object, but we haven't got it yet.
            $post = WP_Post::get_instance( $this->object_id );
            if( WPV_View_Base::is_wppost_ct( $post ) ) {
                $this->post = $post;
            } else {
                throw new InvalidArgumentException( 'Invalid Content Template ID' );
            }
        }

        return $this->post;
    }


    /**
     * @var array Default postmeta for the Content Template.
     *
     * Note that this should contain all postmeta keys a CT can have (if they're not generic).
     *
     * @todo Add all default values and their description
     */
    protected static $postmeta_defaults = array(
        '_wpv-content-template-decription' => '',
        '_view_loop_id' => 0
    );


    /**
     * Return array of default post meta.
     *
     * This is a workaround about not being able to reliably use self:: because of PHP 5.2.
     *
     * @return array Default post meta.
     */
    public function get_postmeta_defaults() {
        return WPV_Content_Template_Embedded::$postmeta_defaults;
    }


    /* ************************************************************************* *\
            Custom methods
    \* ************************************************************************* */


    /**
     * Get information about loops that have this CT assigned for archives.
     *
     * Take a look at $WPV_view_archive_loop->get_archive_loops() for better understanding of what is being returned.
     *
     * @param string $loop_type Relevant loop type: "post_type", "taxonomy" or "both".
     *
     * @return array Array of loop information.
     */
    public function get_assigned_loops( $loop_type ) {

        if( 'both' == $loop_type  ) {
            return array_merge(
                $this->get_assigned_loops( 'post_type' ),
                $this->get_assigned_loops( 'taxonomy' )
            );
        }

        global $WPV_view_archive_loop;
        $loops = $WPV_view_archive_loop->get_archive_loops( $loop_type, false, true );

        $selected_loops = array();
        foreach( $loops as $loop ) {
            if( $loop['ct'] == $this->object_id ) {
                $selected_loops[] = $loop;
            }
        }

        return $selected_loops;
    }


    /**
     * Get information about loops that have this CT assigned for single posts.
     *
     * Take a look at $WPV_view_archive_loop->get_archive_loops() for better understanding of what is being returned.
     *
     * @return array Array of loop information.
     */
    public function get_assigned_single_post_types() {
        global $WPV_view_archive_loop;

        // Get information about CT assignment and don't exclude any post types.
        $loops = $WPV_view_archive_loop->get_archive_loops( 'post_type', false, true, true );

        $selected_loops = array();
        foreach( $loops as $loop ) {
            if( $loop['single_ct'] == $this->object_id ) {
                $selected_loops[] = $loop;
            }
        }
        return $selected_loops;
    }



    /* ************************************************************************* *\
            Custom getters
    \* ************************************************************************* */


    /**
     * @return string CT description.
     */
    protected function _get_description() {
        return get_post_meta( $this->object_id, '_wpv-content-template-decription', true );
    }


    /**
     * @return int ID of a View/WPA that uses this CT as loop output template ("owns" this CT).
     * Zero if no owner exists.
     */
    protected function _get_loop_output_id() {
        $loop_output_id = $this->_view_loop_id; // postmeta

        return (int) $loop_output_id;
    }


    /**
     * @return bool True if this CT has an owner View/WPA (@see _get_loop_output_id()).
     */
    protected function _get_is_owned_by_view() {
        return ( 0 != $this->loop_output_id );
    }


}