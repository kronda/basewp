<?php

if ( class_exists( 'Toolset_Common_Bootstrap' ) ) {
    return;
};

if( !defined('TOOLSET_VERSION') ){
	define('TOOLSET_VERSION', '2.2.2');
}

if ( ! defined('TOOLSET_COMMON_VERSION' ) ) {
    define( 'TOOLSET_COMMON_VERSION', '2.2.2' );
}

if ( ! defined('TOOLSET_COMMON_PATH' ) ) {
    define( 'TOOLSET_COMMON_PATH', dirname( __FILE__ ) );
}

if ( ! defined('TOOLSET_COMMON_DIR' ) ) {
    define( 'TOOLSET_COMMON_DIR', basename( TOOLSET_COMMON_PATH ) );
}

require_once( TOOLSET_COMMON_PATH . '/bootstrap.php' );

if ( ! function_exists( 'toolset_common_boostrap' ) ) {
    function toolset_common_boostrap() {
        global $toolset_common_bootstrap;
        $toolset_common_bootstrap = Toolset_Common_Bootstrap::getInstance();
    }

	/**
	 * @TODO: there is no need to manipulate URL values for http/https if everyone uses plugins_url
     * additionally if this is somehow necessary it is enough to do $url = set_url_scheme( $url ) and the protocol
     * will be calculated by itself.
     * @TODO: only one constant is enough, no need of TOOLSET_COMMON_URL, TOOLSET_COMMON_PROTOCOL, TOOLSET_COMMON_FRONTEND_URL, TOOLSET_COMMON_FRONTEND_PROTOCOL
     * define('TOOLSET_COMMON_URL', set_url_scheme( $url ) ); covers everything
	 */
    function toolset_common_set_constants_and_start( $url ) {
		if (
			is_ssl()
			|| (
				defined( 'FORCE_SSL_ADMIN' )
				&& FORCE_SSL_ADMIN
			)
		) {
			define( 'TOOLSET_COMMON_URL', str_replace( 'http://', 'https://', $url ) );
			define( 'TOOLSET_COMMON_PROTOCOL', 'https' );
		} else {
			define( 'TOOLSET_COMMON_URL', $url );
			define( 'TOOLSET_COMMON_PROTOCOL', 'http' );
	}
		if ( is_ssl() ) {
			define( 'TOOLSET_COMMON_FRONTEND_URL', TOOLSET_COMMON_URL );
			define( 'TOOLSET_COMMON_FRONTEND_PROTOCOL', 'https' );
		} else {
			define( 'TOOLSET_COMMON_FRONTEND_URL', str_replace( 'https://', 'http://', TOOLSET_COMMON_URL ) );
			define( 'TOOLSET_COMMON_FRONTEND_PROTOCOL', 'http' );
		}
    }
    // Load early
	// We register scripts and styles that are dependences for Toolset assets
    add_action( 'after_setup_theme', 'toolset_common_boostrap' );
}


if( !function_exists('toolset_disable_wpml_admin_lang_switcher') ){
	add_filter( 'wpml_show_admin_language_switcher', 'toolset_disable_wpml_admin_lang_switcher' );
	function toolset_disable_wpml_admin_lang_switcher( $state ) {
		global $pagenow;

		$toolset_pages = array(
			'toolset-settings', 'toolset-help', 'toolset-debug-information'
		);

		$toolset_pages = apply_filters( 'toolset_filter_disable_wpml_lang_switcher_in_admin', $toolset_pages );

		if (
			$pagenow == 'admin.php'
			&& isset( $_GET['page'] )
			&& in_array( $_GET['page'], $toolset_pages )
		) {
			$state = false;
		}
		return $state;
	}

}

require_once( TOOLSET_COMMON_PATH . '/user-editors/beta.php' );