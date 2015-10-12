<?php


/**
 * Full Views version of WPV_WPML_Integration_Embedded.
 *
 * Currently without any new functionality.
 *
 * @since 1.10
 */
class WPV_WPML_Integration extends WPV_WPML_Integration_Embedded {

    protected function __construct() { parent::__construct(); }

    public static function init() {
        self::get_instance();
    }

    public static function get_instance() {
        if( null == self::$instance ) {
            self::$instance = new WPV_WPML_Integration();
        }
        return self::$instance;
    }


}