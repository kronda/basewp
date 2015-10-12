<?php

if ( defined( 'WPV_VERSION' ) ) {
    // the plugin version is present.
    return; 
}

// EMBEDDED VERSION

define( 'WPV_VERSION', '1.10.1' );

/*
 * Note: This modification was not authorized, but might be in the future. I'll dare to just leave it here. --Jan
 *
 * WPV_PATH and WPV_PATH_EMBEDDED can be overriden, if WPV_PATH_OVERRIDE is also defined.
 *
 * If those constants are defined at this point, Views will not try to redefine them. This may be helpful in some weird
 * scenarios (like using symlinks on dev site) and will not affect anyone who doesn't make an effort to use this
 * feature.
 */
/*if( !defined( 'WPV_PATH_OVERRIDE' ) || !defined( 'WPV_PATH' ) ) {
    define( 'WPV_PATH', dirname( __FILE__ ) );
}

if( !defined( 'WPV_PATH_OVERRIDE' ) || !defined( 'WPV_PATH_EMBEDDED' ) ) {
    define( 'WPV_PATH_EMBEDDED', dirname( __FILE__ ) );
}*/

define('WPV_PATH', dirname(__FILE__));
define('WPV_PATH_EMBEDDED', dirname(__FILE__));

if (!defined('WPV_FOLDER')) {
	define('WPV_FOLDER', basename(WPV_PATH));
}

// Module Manager integration
require WPV_PATH_EMBEDDED . '/inc/wpv-module-manager.php';

if(strpos(str_replace('\\', '/', WPV_PATH_EMBEDDED), str_replace('\\', '/', WP_PLUGIN_DIR)) !== false){
	$wpv_url = plugins_url('embedded-views' , dirname(__FILE__));
	if ( defined( 'WPV_EMBEDDED_ALONE' ) ) {
		$wpv_url = plugins_url() . '/' . WPV_FOLDER;
	}
	if ( ( defined( 'FORCE_SSL_ADMIN' ) && FORCE_SSL_ADMIN ) || is_ssl() ) {
		$wpv_url = str_replace( 'http://', 'https://', $wpv_url );
	}
	define('WPV_URL', $wpv_url);
	define('WPV_URL_EMBEDDED', $wpv_url);
} else {
	define('WPV_URL', get_stylesheet_directory_uri() . '/' . WPV_FOLDER);
	define('WPV_URL_EMBEDDED', WPV_URL);
}
if ( is_ssl() ) {
	define('WPV_URL_EMBEDDED_FRONTEND', WPV_URL_EMBEDDED );
} else {
	define('WPV_URL_EMBEDDED_FRONTEND', str_replace( 'https://', 'http://', WPV_URL_EMBEDDED ) );
}

if( defined('WPV_URL_EMBEDDED') ){
    // load on the go resources
	require WPV_PATH_EMBEDDED . '/onthego-resources/loader.php';
	onthego_initialize(WPV_PATH_EMBEDDED . '/onthego-resources/', WPV_URL_EMBEDDED . '/onthego-resources/');
}

if (!defined('EDITOR_ADDON_RELPATH')) {
    define('EDITOR_ADDON_RELPATH', WPV_URL . '/common/visual-editor');
}

if ( !function_exists( 'wplogger' ) ) {
	require_once(WPV_PATH_EMBEDDED) . '/common/wplogger.php';
}

if ( !function_exists( 'wpv_debuger' ) ) { 
	require_once(WPV_PATH_EMBEDDED) . '/inc/wpv-query-debug.class.php';
}

require WPV_PATH_EMBEDDED . '/inc/constants-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-admin-messages.php';
require WPV_PATH_EMBEDDED . '/inc/functions-core-embedded.php';

require_once WPV_PATH_EMBEDDED . '/common/wp-pointer.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-shortcodes.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-shortcodes-in-shortcodes.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-shortcodes-gui.php';

//Views Conditional group
require WPV_PATH_EMBEDDED . '/inc/wpv-conditional.php';

require WPV_PATH_EMBEDDED . '/inc/wpv-formatting-embedded.php';

require WPV_PATH_EMBEDDED . '/inc/wpv-layout-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-meta-html-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-pagination-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-archive-loop.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-category-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-custom-field-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-order-by-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-post-types-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-search-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-status-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-author-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-users-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-usermeta-field-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-id-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-date-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-parent-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-types-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-post-relationship-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-taxonomy-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-limit-embedded.php';

require_once( WPV_PATH_EMBEDDED . '/inc/third-party/wpv-framework-api.php');

require WPV_PATH_EMBEDDED . '/inc/wpv-user-functions.php';

require WPV_PATH_EMBEDDED . '/inc/wpv-widgets.php';

require WPV_PATH_EMBEDDED . '/inc/wpv.class.php';

require WPV_PATH_EMBEDDED . '/inc/wpv-condition.php';

require WPV_PATH_EMBEDDED . '/common/WPML/wpml-string-shortcode.php';
require WPV_PATH_EMBEDDED . '/inc/WPML/wpv_wpml.php';


if (is_admin()) {
    require WPV_PATH_EMBEDDED . '/inc/wpv-import-export-embedded.php';
}

global $WP_Views;
$WP_Views = new WP_Views();

require WPV_PATH . '/inc/views-templates/functions-templates.php';
require WPV_PATH . '/inc/views-templates/wpv-template.class.php';

global $WPV_templates;
$WPV_templates = new WPV_template();

// Views Settings (Read-Only)
require_once WPV_PATH_EMBEDDED . '/inc/wpv-settings-embedded.php';

global $WPV_settings;
$WPV_settings = new WPV_Settings_Embedded;

// Views object wrappers
require_once WPV_PATH_EMBEDDED . '/inc/classes/wpv-post-object-wrapper.class.php';
require_once WPV_PATH_EMBEDDED . '/inc/classes/wpv-view-base.class.php';
require_once WPV_PATH_EMBEDDED . '/inc/classes/wpv-view-embedded.class.php';
require_once WPV_PATH_EMBEDDED . '/inc/classes/wpv-wordpress-archive-embedded.class.php';
require_once WPV_PATH_EMBEDDED . '/inc/classes/wpv-content-template-embedded.class.php';

require WPV_PATH_EMBEDDED . '/inc/wpv-summary-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-readonly-embedded.php';

require WPV_PATH_EMBEDDED . '/inc/wpv-api.php';

if (!is_admin()) {

	add_action('init', 'wpv_add_jquery');

    function wpv_add_jquery() {
		wp_enqueue_script('jquery');
	}
}

/**
* toolset_is_views_embedded_available
*
* Filter to check whether Views Embedded is installed
*
* @since 1.9
*/

add_filter( 'toolset_is_views_embedded_available', '__return_true' );


/* ************************************************************************* *\
        Add Toolset promotion pop-up.
\* ************************************************************************* */

include_once( WPV_PATH_EMBEDDED . '/common/classes/class.toolset.promo.php' );

new Toolset_Promotion;

/**
 * Register screen IDs where Toolset promotion pop-up should be available.
 *
 * Currently we use it on embedded listing pages.
 *
 * @since 1.8
 */
add_filter( 'toolset_promotion_screen_ids', 'wpv_register_toolset_promotion_screen_ids' );

function wpv_register_toolset_promotion_screen_ids( $screen_ids ) {
    if( is_array( $screen_ids ) ) {
        $screen_ids = array_merge(
            $screen_ids,
            array( 'toplevel_page_embedded-views', 'views_page_embedded-views-templates', 'views_page_embedded-views-archives' ) );
    }
    return $screen_ids;
}