<?php
/*
Plugin Name: WP Views
Plugin URI: http://wp-types.com/
Description: When you need to create lists of items, Views is the solution. Views will query the content from the database, iterate through it and let you display it with flair. You can also enable pagination, search, filtering and sorting by site visitors.
Author: ICanLocalize
Author URI: http://wpml.org
Version: 1.6.1
*/

if(defined('WPV_VERSION')) return;

define('WPV_VERSION', '1.6.1');
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

// Module Manager integration
require WPV_PATH_EMBEDDED . '/inc/wpv-module-manager.php';

if (!defined('EDITOR_ADDON_RELPATH')) {
    define('EDITOR_ADDON_RELPATH', WPV_URL . '/embedded/common/visual-editor');
}

require WPV_PATH . '/inc/constants.php';
require WPV_PATH . '/inc/wpv-admin-messages.php';
require WPV_PATH_EMBEDDED . '/inc/functions-core-embedded.php';
require WPV_PATH . '/inc/functions-core.php';
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
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-meta-html-embedded.php';

require WPV_PATH_EMBEDDED . '/inc/wpv-widgets.php';
require WPV_PATH . '/inc/wpv-layout.php'; // DEPRECATED test NOTE maybe Victor is using something from here
require WPV_PATH . '/inc/wpv-filter-controls.php'; // DEPRECATED test
require WPV_PATH . '/inc/wpv-admin-changes.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-layout-embedded.php';
require WPV_PATH . '/inc/wpv-filter.php'; // DEPRECATED test
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
//Filters for users. 
require_once( WPV_PATH_EMBEDDED . '/inc/wpv-filter-users-embedded.php');
require_once( WPV_PATH . '/inc/filters/wpv-filter-users.php');
require_once( WPV_PATH_EMBEDDED . '/inc/wpv-filter-usermeta-field-embedded.php');
require_once( WPV_PATH . '/inc/filters/wpv-filter-user-field.php');


require WPV_PATH . '/inc/wpv-plugin.class.php';

//Including files with redesign for views listings and editing
require_once( WPV_PATH . '/inc/redesign/wpv-views-listing-page.php');
require_once( WPV_PATH . '/inc/wpv-add-edit.php');

//Including file with redesign for content templates listing
require_once( WPV_PATH . '/inc/redesign/wpv-content-templates-listing-page.php'); 

//Including file with redesign for archive views
require_once( WPV_PATH . '/inc/redesign/wpv-archive-listing-page.php');
require_once( WPV_PATH . '/inc/wpv-archive-add-edit.php');

//if (is_admin()) {
    require WPV_PATH_EMBEDDED . '/inc/wpv-import-export-embedded.php';
    require WPV_PATH . '/inc/wpv-import-export.php';
//}
    
require WPV_PATH_EMBEDDED . '/inc/wpv-condition.php';

require WPV_PATH_EMBEDDED . '/common/WPML/wpml-string-shortcode.php';
require WPV_PATH_EMBEDDED . '/inc/WPML/wpv_wpml.php';

$WP_Views = new WP_Views_plugin;

require WPV_PATH . '/inc/views-templates/functions-templates.php';
require WPV_PATH . '/inc/views-templates/wpv-template-plugin.class.php';
$WPV_templates = new WPV_template_plugin();

register_activation_hook(__FILE__, 'wpv_views_plugin_activate');
register_deactivation_hook(__FILE__, 'wpv_views_plugin_deactivate');

add_action('admin_init', 'wpv_views_plugin_redirect');

add_filter('plugin_action_links', 'wpv_views_plugin_action_links', 10, 2);

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

// compatibility notices TODO use AJAX for handling this

add_action('admin_notices', 'wpv_admin_notice');

function wpv_admin_notice() { // TODO update admin notices, check Types and CRED version numbers
	global $current_user, $pagenow;
	if ( current_user_can( 'activate_plugins' ) && ( defined( 'WPCF_VERSION' ) &&  version_compare( WPCF_VERSION, '1.4.0.2' )  < 0 ) ) {
		$user_id = $current_user->ID;
		/* Check that the user hasn't already clicked to ignore the message */
		if ( ! get_user_meta($user_id, 'wpv_1304_types_notice') ) {
			parse_str($_SERVER['QUERY_STRING'], $params);
			echo '<div class="updated"><p>'; 
			printf(__('Views 1.3.1 is compatible with Types version 1.4.0.2 or greater. Please update Types. | <a href="%1$s">Dismiss</a>', 'wpv-views'), '?' . http_build_query(array_merge($params, array('wpv_131_types_ignore'=>'0'))));
			echo "</p></div>";
		}
	}

	if ( current_user_can( 'activate_plugins' ) && ( defined( 'CRED_FE_VERSION' ) &&  version_compare( CRED_FE_VERSION, '1.2.3' )  < 0 ) ) {
		$user_id = $current_user->ID;
		/* Check that the user hasn't already clicked to ignore the message */
		if ( ! get_user_meta($user_id, 'wpv_1304_cred_notice') ) {
			parse_str($_SERVER['QUERY_STRING'], $params);
			echo '<div class="updated"><p>'; 
			printf(__('Views 1.3.1 is compatible with CRED version 1.2.3 or greater. Please update CRED. | <a href="%1$s">Dismiss</a>', 'wpv-views'), '?' . http_build_query(array_merge($params, array('wpv_131_cred_ignore'=>'0'))));
			echo "</p></div>";
		}
	}
	
}

add_action('admin_init', 'wpv_admin_notice_ignore');

function wpv_admin_notice_ignore() {
	global $current_user;
	$user_id = $current_user->ID;
	/* If user clicks to ignore the notice, add that to their user meta */
	if ( isset($_GET['wpv_131_types_ignore']) && '0' == $_GET['wpv_131_types_ignore'] ) {
		 add_user_meta($user_id, 'wpv_1304_types_notice', 'true', true);
	}

	if ( isset($_GET['wpv_131_cred_ignore']) && '0' == $_GET['wpv_131_cred_ignore'] ) {
		 add_user_meta($user_id, 'wpv_1304_cred_notice', 'true', true);
	}
}