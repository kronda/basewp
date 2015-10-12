<?php

/**
 * Views Settings Embedded
 * 
 * It implements both ArrayAccess and dynamic properties. ArrayAccess is 
 * deprecated.
 *
 * @link https://docs.google.com/a/icanlocalize.com/document/d/1PZBwYGJlggu1Zg8-D5zu6em2ytfimxnJ3lE1EjqqK9U/edit?usp=sharing
 *
 * @since 1.8
 */
class WPV_Settings_Embedded implements ArrayAccess, IteratorAggregate, Countable {


    /**
     * WP Option Name for Views
     */
    const option_name = 'wpv_options';

    /* ************************************************************************* *\
        Setting names
    \* ************************************************************************* */


    // Note: @since tags here define since when the option exist, not since when the constant is defined.

    /**
     * Setting with this prefix + post type slug ($post_type->name) holds an ID of Content Template that should be
     * used for single posts of this type, or 0 if no CT is assigned. It doesn't have to be set for all post types.
     *
     * @since unknown
     */
    const SINGLE_POST_TYPES_CT_ASSIGNMENT_PREFIX = 'views_template_for_';


    /**
     * Setting with this prefix + post type slug ($post_type->name) holds an ID of Content Template that should be
     * used for post archive for this post type, or 0 if no CT is assigned. It doesn't have to be set for all post types.
     *
     * @since unknown
     */
    const CPT_ARCHIVES_CT_ASSIGNMENT_PREFIX = 'views_template_archive_for_';


    /**
     * Setting with this prefix + taxonomy slug ($taxonomy->name) holds an ID of Content Template that should be
     * used for archives of this taxonomy, or 0 if no CT is assigned. It doesn't have to be set for all taxonomies.
     *
     * @since unknown
     */
    const TAXONOMY_ARCHIVES_CT_ASSIGNMENT_PREFIX = 'views_template_loop_';


    /**
     * Bootstrap version that is expected to be used in a theme.
     *
     * Allowed values are:
     * - '2': Bootstrap 2.0
     * - '3': Bootstrap 3.0
     * - '-1': Site is not using Bootstrap (@since 1.9)
     * - '1' or missing value (or perhaps anything else, too): Bootstrap version not set
     *
     * @since unknown
     */
    const BOOTSTRAP_VERSION = 'wpv_bootstrap_version';



    /* ************************************************************************* *\
        Methods
    \* ************************************************************************* */

    /**
     * Settings by default
     * @var array 
     */
    protected $default;

    /**
     * Settings by the customer. It always precedes.
     * @var array 
     */
    protected $custom;

    public function __construct() {
        $this->custom = get_option( self::option_name );
        if ( !$this->custom ) {
            $this->custom = array();
        }

        /**
         * wpv_                             ....    Views Plugin Settings
         * view_                            ....    View or WordPress Archive Settings
         * views_template_for_              ....    Content Template Settings for single posts
         * views_template_loop_             ....    Content Template Settings for taxonomy archives
         * views_template_archive_for_      ....    Content Template Settings for post types archives
         * wpml_                            ....    WPML-integration Settings
         */
        $this->default = array(
            'views_template_loop_blog' => '0',
            'wpml_fix_urls' => true,
            WPV_Settings_Embedded::BOOTSTRAP_VERSION => 1,
            'wpv_custom_conditional_functions' => array(),
            'wpv_custom_inner_shortcodes' => array(),
            'wpv_debug_mode' => '',
            'wpv_debug_mode_type' => 'compact',
            'wpv_map_plugin' => '',
            'wpv_show_edit_view_link' => 1,
            'wpv_show_hidden_fields' => '',
			'wpv_saved_auto_detected_framework' => ''
        );
    }

    /**
     * isset for array interface
     * @param mixed $offset setting name
     * @return bool
     */
    public function offsetExists( $offset ) {
        return isset( $this->custom[$offset] );
    }

    /**
     * getter for array interface
     * @param mixed $offset setting name
     * @return mixed setting value
     */
    public function offsetGet( $offset ) {
        if ( $offset ) {
            return $this->get( $offset );
        } else {
            return null;
        }
    }

    /**
     * ArrayAccess::offsetSet
     * @param type $offset
     * @param type $value
     */
    public function offsetSet( $offset, $value ) {
        $this->custom[$offset] = $value;
    }

    /**
     * ArrayAccess::offsetUnset
     * @param type $offset
     */
    public function offsetUnset( $offset ) {
        if ( isset( $this->custom[$offset] ) ) {
            unset( $this->custom[$offset] );
        }
    }

    /**
     * PHP dynamic setter
     * @param mixed $name $this->name
     * @return mixed
     */
    public function __get( $name ) {
        return $this->get( $name );
    }

    /**
     * PHP dynamic setter
     * @param string $name
     * @param type $value
     */
    public function __set( $name, $value ) {
        $this->custom[$name] = $value;
    }

    /**
     * PHP dynamic fields unset() method support
     * @param string $name
     */
    public function __unset( $name ) {
        if ( isset( $this->custom[$name] ) ) {
            unset( $this->custom[$name] );
        }
    }

    /**
     * PHP dynamic support for isset($this->name) 
     * @param string $name
     * @return boolean
     */
    public function __isset( $name ) {
        return isset( $this->custom[$name] );
    }

    /**
     * Obtain a value for a setting (or all settings)
     * @param string $key name of the setting to retrieve
     * @return mixed value of the key or an array with all key-value pairs
     */
    public function get( $key = null ) {
        if ( $key ) {
            // Retrieve one setting
            if ( isset( $this->custom[$key] ) ) {
                // Return user-set value, if available
                return $this->custom[$key];
            } elseif ( isset( $this->default[$key] ) ) {
                // Use default value, if available
                return $this->default[$key];
            } else {
                // There isn't any key like that
                return null;
            }
        } else {
            // Retrieve all settings
            return wp_parse_args( $this->custom, $this->default );
        }
    }

    /**
     * Set Setting(s).
     * 
     * Usage:
     *  One key-value pair
     *  set('key', 'value');
     * 
     *  Multiple key-value pairs
     *  set( array('key1' => 'value1', 'key2' => 'value2' );
     * 
     * @param mixed $param1 name of the setting or an array with name-value pairs of the settings (bulk set)
     * @param mixed $param2 value of the setting
     */
    public function set( $param1, $param2 = null ) {
        if ( is_array( $param1 ) ) {
            foreach ( $param1 as $key => $value ) {
                $this->custom[$key] = $value;
            }
        } else if ( is_object( $param1 ) && is_a( $param1, 'WPV_Settings_Embedded' ) ) {
            // DO NOTHING.
            // It's assigned already.
        } else if ( is_string( $param1 ) || is_integer( $param1 ) ) {
            $this->custom[$param1] = $param2;
        }
    }

    /**
     * Persists settings in the database
     *
     * @todo Consider some optimalization - only update options that have changed.
     */
    public function save() {
        update_option( self::option_name, $this->custom );
    }

    /**
     * Allow this class to be iterated (IteratorAggregate / ArrayIterator)
     */
    public function getIterator() {
        return new ArrayIterator( $this->custom );
    }

    public function count() {
        return count( $this->custom );
    }

    public function is_empty() {
        return empty( $this->custom );
    }

    ////////////////////////////////////////////////////////////////////////////
    //
    // Views Settings Specific Functions
    //
    ////////////////////////////////////////////////////////////////////////////

    function get_view_template_settings() {
        $post_types = get_post_types();

        $template_settings = array();

        foreach ( $post_types as $type ) {
            if ( isset( $this->custom['views_template_for_' . $type] ) && !empty( $this->custom['views_template_for_' . $type] ) ) {
                $template_settings[$type] = $this->custom['views_template_for_' . $type];
            }
        }

        return $template_settings;
    }


    /**
     * Get Content Template ID assigned to a post type as a single post template.
     *
     * @param string $post_type Post type slug.
     * @return int Content Template ID or zero if none is assigned.
     * @since 1.9
     */
    function get_ct_assigned_to_single_post_type( $post_type ) {
        $setting_name = WPV_Settings_Embedded::SINGLE_POST_TYPES_CT_ASSIGNMENT_PREFIX . $post_type;
        return (int) $this->get( $setting_name );
    }


    /**
     * Get Content Template ID assigned to a post type as a post archive template.
     *
     * @param string $post_type Post type slug.
     * @return int Content Template ID or zero if none is assigned.
     * @since 1.9
     */
    function get_ct_assigned_to_cpt_archive( $post_type ) {
        $setting_name = WPV_Settings_Embedded::CPT_ARCHIVES_CT_ASSIGNMENT_PREFIX . $post_type;
        return (int) $this->get( $setting_name );
    }


    /**
     * Get Content Template ID assigned to a taxonomy as an archive template.
     *
     * @param string $taxonomy_slug Taxonomy slug.
     * @return int Content Template ID or zero if none is assigned.
     * @since 1.9
     */
    function get_ct_assigned_to_taxonomy_archive( $taxonomy_slug ) {
        $setting_name = WPV_Settings_Embedded::TAXONOMY_ARCHIVES_CT_ASSIGNMENT_PREFIX . $taxonomy_slug;
        return (int) $this->get( $setting_name );
    }

}
