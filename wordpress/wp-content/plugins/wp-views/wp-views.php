<?php
/*
Plugin Name: WP Views
Plugin URI: http://wp-types.com/?utm_source=viewsplugin&utm_campaign=views&utm_medium=plugins-list-full-version&utm_term=Visit plugin site
Description: When you need to create lists of items, Views is the solution. Views will query the content from the database, iterate through it and let you display it with flair. You can also enable pagination, search, filtering and sorting by site visitors.
Author: OnTheGoSystems
Author URI: http://www.onthegosystems.com
Version: 1.10.1
*/

if ( defined( 'WPV_VERSION' ) ) return;

define('WPV_PATH', dirname(__FILE__));
define('WPV_PATH_EMBEDDED', dirname(__FILE__) . '/embedded');
define('WPV_FOLDER', basename(WPV_PATH));
// define('WPV_URL', plugins_url() . '/' . WPV_FOLDER); NOTE fix SSL possible problems below
if ( ( defined( 'FORCE_SSL_ADMIN' ) && FORCE_SSL_ADMIN ) || is_ssl() ) {
	define('WPV_URL', rtrim( str_replace( 'http://', 'https://', plugins_url() ), '/' ) . '/' . WPV_FOLDER );
}else{
	define('WPV_URL', plugins_url() . '/' . WPV_FOLDER );
}
define('WPV_URL_EMBEDDED', WPV_URL . '/embedded');
if ( is_ssl() ) {
	define('WPV_URL_EMBEDDED_FRONTEND', WPV_URL_EMBEDDED );
} else {
	define('WPV_URL_EMBEDDED_FRONTEND', str_replace( 'https://', 'http://', WPV_URL_EMBEDDED ) );
}

// load on the go resources
require WPV_PATH_EMBEDDED . '/onthego-resources/loader.php';
onthego_initialize(WPV_PATH_EMBEDDED . '/onthego-resources/', WPV_URL_EMBEDDED . '/onthego-resources/');

// Views Settings
require WPV_PATH_EMBEDDED . '/inc/wpv-settings-embedded.php';
require WPV_PATH . '/inc/wpv-settings.php';
$WPV_settings = new WPV_Settings;

// Helper classes
require_once WPV_PATH . '/inc/classes/wpv-exception-with-message.class.php';

// WPV_View and other Toolset object wrappers
require_once WPV_PATH_EMBEDDED . '/inc/classes/wpv-post-object-wrapper.class.php';
require_once WPV_PATH_EMBEDDED . '/inc/classes/wpv-view-base.class.php';
require_once WPV_PATH_EMBEDDED . '/inc/classes/wpv-view-embedded.class.php';
require_once WPV_PATH_EMBEDDED . '/inc/classes/wpv-wordpress-archive-embedded.class.php';
require_once WPV_PATH_EMBEDDED . '/inc/classes/wpv-content-template-embedded.class.php';

require_once WPV_PATH . '/inc/classes/wpv-view.class.php';
require_once WPV_PATH . '/inc/classes/wpv-wordpress-archive.class.php';
require_once WPV_PATH . '/inc/classes/wpv-content-template.class.php';

// Module Manager integration
require WPV_PATH_EMBEDDED . '/inc/wpv-module-manager.php';

if (!defined('EDITOR_ADDON_RELPATH')) {
    define('EDITOR_ADDON_RELPATH', WPV_URL . '/embedded/common/visual-editor');
}

require WPV_PATH . '/inc/constants.php';
require WPV_PATH_EMBEDDED . '/inc/constants-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-admin-messages.php';
require WPV_PATH_EMBEDDED . '/inc/functions-core-embedded.php';
require WPV_PATH . '/inc/functions-core.php';
require WPV_PATH . '/inc/wpv-deprecated.php';
require WPV_PATH . '/inc/wpv-admin-ajax.php';
require WPV_PATH . '/inc/wpv-admin-ajax-layout-wizard.php';
if ( !function_exists( 'wplogger' ) ) {
	require_once(WPV_PATH_EMBEDDED) . '/common/wplogger.php';
}
if ( !function_exists( 'wpv_debuger' ) ) { 
	require_once(WPV_PATH_EMBEDDED) . '/inc/wpv-query-debug.class.php';
}
require_once(WPV_PATH_EMBEDDED) . '/common/wp-pointer.php'; // NOTE I think we do not need pointers anymore

$wpv_wp_pointer = new WPV_wp_pointer('views');

require WPV_PATH_EMBEDDED . '/inc/wpv-shortcodes.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-shortcodes-in-shortcodes.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-shortcodes-gui.php';

//Views Conditional group
require WPV_PATH_EMBEDDED . '/inc/wpv-conditional.php';

require WPV_PATH_EMBEDDED . '/inc/wpv-formatting-embedded.php';

require WPV_PATH_EMBEDDED . '/inc/wpv-filter-meta-html-embedded.php';

require WPV_PATH_EMBEDDED . '/inc/wpv-widgets.php';
require WPV_PATH . '/inc/wpv-admin-changes.php';// Review contents, there might be DEPRECATED things
require WPV_PATH_EMBEDDED . '/inc/wpv-layout-embedded.php';

require WPV_PATH_EMBEDDED . '/inc/wpv-filter-types-embedded.php';// surely not needed here at all
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-post-types-embedded.php';// surely not needed here at all
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-taxonomy-embedded.php';// surely not needed here at all
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-order-by-embedded.php';// surely not needed here at all
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-limit-embedded.php';// surely not needed here at all

require WPV_PATH_EMBEDDED . '/inc/wpv-filter-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-pagination-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-archive-loop.php';

require WPV_PATH_EMBEDDED . '/inc/wpv-user-functions.php';

require_once( WPV_PATH_EMBEDDED . '/inc/wpv-filter-author-embedded.php');
require_once( WPV_PATH . '/inc/filters/wpv-filter-author.php');
require_once( WPV_PATH_EMBEDDED . '/inc/wpv-filter-status-embedded.php');
require_once( WPV_PATH . '/inc/filters/wpv-filter-status.php');
require_once( WPV_PATH_EMBEDDED . '/inc/wpv-filter-search-embedded.php');
require_once( WPV_PATH . '/inc/filters/wpv-filter-search.php');
require_once( WPV_PATH_EMBEDDED . '/inc/wpv-filter-category-embedded.php');
require_once( WPV_PATH . '/inc/filters/wpv-filter-category.php');
require_once( WPV_PATH_EMBEDDED . '/inc/wpv-filter-custom-field-embedded.php');
require_once( WPV_PATH . '/inc/filters/wpv-filter-custom-field.php');
require_once( WPV_PATH_EMBEDDED . '/inc/wpv-filter-parent-embedded.php');
require_once( WPV_PATH . '/inc/filters/wpv-filter-parent.php');
require_once( WPV_PATH . '/inc/filters/wpv-filter-taxonomy-term.php'); // NOTE why don't we have an embedded version of this?
require_once( WPV_PATH_EMBEDDED . '/inc/wpv-filter-post-relationship-embedded.php');
require_once( WPV_PATH . '/inc/filters/wpv-filter-post-relationship.php');
require_once( WPV_PATH_EMBEDDED . '/inc/wpv-filter-id-embedded.php');
require_once( WPV_PATH . '/inc/filters/wpv-filter-id.php');
require_once( WPV_PATH_EMBEDDED . '/inc/wpv-filter-date-embedded.php');
require_once( WPV_PATH . '/inc/filters/wpv-filter-date.php');
//Filters for users. 
require_once( WPV_PATH_EMBEDDED . '/inc/wpv-filter-users-embedded.php');
require_once( WPV_PATH . '/inc/filters/wpv-filter-users.php');
require_once( WPV_PATH_EMBEDDED . '/inc/wpv-filter-usermeta-field-embedded.php');
require_once( WPV_PATH . '/inc/filters/wpv-filter-user-field.php');


require WPV_PATH . '/inc/wpv-plugin.class.php';

require_once( WPV_PATH_EMBEDDED . '/inc/third-party/wpv-framework-api.php');

// Including files for listing pages
require_once( WPV_PATH . '/inc/wpv-listing-common.php');

//Including files with redesign for views listings and editing
require_once( WPV_PATH . '/inc/redesign/wpv-views-listing-page.php');
require_once( WPV_PATH . '/inc/wpv-add-edit.php');

//Including file with redesign for content templates listing
require_once( WPV_PATH . '/inc/redesign/wpv-content-templates-listing-page.php');

// Including file with Content template edit page
require_once( WPV_PATH . '/inc/ct-editor/ct-editor.php');


//Including file with redesign for archive views
require_once( WPV_PATH . '/inc/redesign/wpv-archive-listing-page.php');
require_once( WPV_PATH . '/inc/wpv-archive-add-edit.php');

//Including file for export/import
require WPV_PATH_EMBEDDED . '/inc/wpv-import-export-embedded.php';
require WPV_PATH . '/inc/wpv-import-export.php';
    
require WPV_PATH_EMBEDDED . '/inc/wpv-condition.php';

require WPV_PATH_EMBEDDED . '/common/WPML/wpml-string-shortcode.php';
require WPV_PATH_EMBEDDED . '/inc/WPML/wpv_wpml.php';
require WPV_PATH . '/inc/wpv-wpml.php';

$WP_Views = new WP_Views_plugin;

require WPV_PATH_EMBEDDED . '/inc/views-templates/functions-templates.php';
require WPV_PATH . '/inc/views-templates/wpv-template-plugin.class.php';
$WPV_templates = new WPV_template_plugin();

require WPV_PATH_EMBEDDED . '/inc/wpv-summary-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-readonly-embedded.php';

require WPV_PATH . '/inc/wpv-admin-update-help.php';
require WPV_PATH . '/inc/wpv-admin-notices.php';

require WPV_PATH_EMBEDDED . '/inc/wpv-api.php';

register_activation_hook(__FILE__, 'wpv_views_plugin_activate');
register_deactivation_hook(__FILE__, 'wpv_views_plugin_deactivate');

add_action('admin_init', 'wpv_views_plugin_redirect');

add_filter( 'plugin_action_links', 'wpv_views_plugin_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'wpv_views_plugin_plugin_row_meta', 10, 4 );

include_once WPV_PATH_EMBEDDED . '/common/classes/class-toolset-admin-bar-menu.php';

define( 'WPV_VERSION', '1.10.1' );

/**
* toolset_is_views_available
*
* Filter to check whether Views is installed
*
* @since 1.9
*/

add_filter( 'toolset_is_views_available', '__return_true' );

//for inline documentation plugin support

if( did_action( 'inline_doc_help_viewquery' ) == 0){
	do_action('inline_doc_help_viewquery', 'admin_screen_view_query_init');
}
if( did_action( 'inline_doc_help_viewfilter' )== 0){
	do_action('inline_doc_help_viewfilter', 'admin_screen_view_filter_init');
}
if( did_action( 'inline_doc_help_viewpagination' )== 0){
	do_action('inline_doc_help_viewpagination', 'admin_screen_view_pagination_init');
}	
if( did_action( 'inline_doc_help_viewlayout' )== 0){
	do_action('inline_doc_help_viewlayout', 'admin_screen_view_layout_init');
}	
if( did_action( 'inline_doc_help_viewlayoutmetahtml' )== 0){
	do_action('inline_doc_help_viewlayoutmetahtml', 'admin_screen_view_layoutmetahtml_init');
}	
if( did_action( 'inline_doc_help_viewtemplate' )== 0){
	do_action('inline_doc_help_viewtemplate', 'admin_screen_view_template_init');
}
