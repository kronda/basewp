<?php

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
     * View settings key for Filter meta HTML code.
     * @since 1.10
     */
    const VIEW_SETTINS_FILTER_META_HTML = 'filter_meta_html';


    /**
     * View settings key for Filter extra CSS code.
     * @since 1.10
     */
    const VIEW_SETTINS_FILTER_CSS = 'filter_meta_html_css';


    /**
     * View settings key for Filter extra JS code.
     */
    const VIEW_SETTINS_FILTER_JS = 'filter_meta_html_js';




    /**
     * Default postmeta values specific for Views.
     *
     * Note that this should contain all postmeta keys that only a View can have, but it doesn't (yet).
     *
     * @todo Add missing default values.
     * @todo Add description to default values.
     * @var array
     */
    protected static $postmeta_defaults = array(

        // this will be merged with WPV_View_Base
        WPV_View_Base::POSTMETA_VIEW_SETTINGS => array(
            WPV_View_Embedded::VIEW_SETTINS_FILTER_META_HTML => '',
            WPV_View_Embedded::VIEW_SETTINS_FILTER_CSS => '',
            WPV_View_Embedded::VIEW_SETTINS_FILTER_JS => '',
        )
    );


    /**
     * Get default postmeta for a View.
     *
     * Combine self::$postmeta_defaults with defaults common for Views and WPAs.
     *
     * @return array
     */
    protected function get_postmeta_defaults() {
        $parent_postmeta = parent::get_postmeta_defaults();
        $this_postmeta = WPV_View_Embedded::$postmeta_defaults;
        return wpv_array_merge_recursive_distinct( $parent_postmeta, $this_postmeta );
    }



    /* ************************************************************************* *\
            Custom getters
    \* ************************************************************************* */


    /**
     * @var null|string Cache for the content_summary property.
     */
    private $content_summary_cache = null;


    /**
     * Get "Content summary" of a View, stating what and how it will show.
     *
     * As this is rather expensive operation, caching is used.
     *
     * @return string
     */
    protected function _get_content_summary() {
        if( empty( $this->content_summary_cache ) ) {
            $this->content_summary_cache = sanitize_text_field(
                apply_filters( 'wpv-view-get-content-summary', '', $this->object_id, $this->settings )
            );
        }
        return $this->content_summary_cache;
    }


    /**
     * Get Filter meta HTML code.
     *
     * This is a View setting.
     *
     * @return string
     * @since 1.10
     */
    protected function _get_filter_meta_html() {
        return $this->get_view_setting( WPV_View_Embedded::VIEW_SETTINS_FILTER_META_HTML );
    }


    /**
     * Get Filter extra CSS code.
     *
     * This is a View setting.
     *
     * @return string
     * @since 1.10
     */
    protected function _get_filter_css() {
        return $this->get_view_setting( WPV_View_Embedded::VIEW_SETTINS_FILTER_CSS );
    }


    /**
     * Get Filter extra JS code.
     *
     * This is a View setting.
     *
     * @return string
     * @since 1.10
     */
    protected function _get_filter_js() {
        return $this->get_view_setting( WPV_View_Embedded::VIEW_SETTINS_FILTER_JS );
    }


    /* ************************************************************************* *\
            View-specific View settings
    \* ************************************************************************* */

    /**
     * View purpose - setting for adjusting which sections will be displayed on View edit page.
     *
     * Allowed values:
     * - all
     * - pagination
     * - slider
     * - parametric
     * - full
     *
     * Anything else should be understood as 'full'.
     *
     * @since 1.10
     */
    const VIEW_SETTINGS_PURPOSE = 'view_purpose';


    /**
     * Get purpose of the View.
     *
     * This is a View setting.
     *
     * @return string
     * @since 1.10
     */
    protected function _get_purpose() {
        $purpose = $this->get_view_setting( WPV_View_Embedded::VIEW_SETTINGS_PURPOSE );
        if( !in_array( $purpose, array( 'all', 'pagination', 'parametric', 'slider', 'full' ) ) ) {
            $purpose = 'full';
        }
        return $purpose;
    }

}