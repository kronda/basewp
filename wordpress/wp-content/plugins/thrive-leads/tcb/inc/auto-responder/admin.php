<?php
/**
 * Created by PhpStorm.
 * User: radu
 * Date: 02.04.2015
 * Time: 14:16
 */
add_action('admin_menu', 'tve_api_add_menu_link');
add_action('admin_enqueue_scripts', 'tve_api_admin_scripts');

add_action('admin_init', 'tve_api_handle_save');
add_action('admin_notices', 'tve_api_admin_notices');

add_action('wp_ajax_tve_api_hide_notice', 'tve_api_hide_notice');


function tve_api_add_menu_link()
{
    $icon_url = tve_editor_url() . '/editor/css/images/tcb-logo.png';
    add_menu_page("API Connections", "API Connections", "manage_options", "tve_api_connect", "tve_api_connect", $icon_url);
    add_submenu_page(null, 'Thrive API Error Log', '', 'manage_options', 'thrive_api_error_log', 'tve_api_error_log');
}

/**
 * check for any expired connections (expired access tokens), or tokens that are about to expire and display global warnings / error messages
 */
function tve_api_admin_notices()
{
    $screen = get_current_screen();
    if ($screen && $screen->parent_base == 'tve_api_connect') {
        return;
    }

    require_once dirname(__FILE__) . '/misc.php';

    $connected_apis = Thrive_List_Manager::getAvailableAPIs(true);
    $warnings = array();

    foreach ($connected_apis as $instance) {
        if ($instance->param('_nd')) {
            continue;
        }
        $warnings = array_merge($warnings, $instance->getWarnings());
    }

    $nonce = sprintf('<span class="nonce" style="display:none">%s</span>', wp_create_nonce('tve_api_dismiss'));

    $template = '<div class="%s tve-api-notice"><p>%s</p>%s</div>';

    $html = '';

    foreach ($warnings as $err) {
        $html .= sprintf($template, 'error', $err, $nonce);
    }

    echo $html;
}

/**
 * main entry point
 */
function tve_api_connect()
{
    require_once dirname(__FILE__) . '/misc.php';

    $connected_apis = Thrive_List_Manager::getAvailableAPIs(true);
    $available_apis = Thrive_List_Manager::getAvailableAPIs();
    $api_types = Thrive_List_Manager::$API_TYPES;

    $current_key = !empty($_REQUEST['api']) ? $_REQUEST['api'] : '';

    Thrive_List_Manager::flashMessages();

    include dirname(__FILE__) . '/views/admin-list.php';
}

/**
 * check to see if we currently need to save some credentials, early in the admin section (e.g. a redirect from Oauth)
 */
function tve_api_handle_save()
{
    if (empty($_REQUEST['page']) || $_REQUEST['page'] != 'tve_api_connect' || empty($_REQUEST['api'])) {
        return;
    }

    /**
     * either a POST from a regular form, or an oauth redirect
     */
    if (empty($_POST['api']) && empty($_REQUEST['oauth_token']) && empty($_REQUEST['disconnect'])) {
        return;
    }

    require_once dirname(__FILE__) . '/misc.php';
    $connection = Thrive_List_Manager::connectionInstance($_REQUEST['api']);

    if (!empty($_REQUEST['disconnect'])) {
        $connection->disconnect()->success($connection->getTitle() . ' ' . __("is now disconnected", 'thrive-cb'));
        wp_redirect(admin_url('admin.php?page=tve_api_connect'));
        exit();
    } else {
        $connection->readCredentials();
    }

    wp_redirect(admin_url('admin.php?page=tve_api_connect' . (Thrive_List_Manager::$ADMIN_HAS_ERROR ? '&api=' . $_REQUEST['api'] : '')));
    exit();
}

/**
 * enqueue all CSS / js needed for the admin connection setup
 *
 * @param string $hook
 */
function tve_api_admin_scripts($hook)
{
    /**
     * global admin JS file for notifications
     */
    tve_enqueue_script('tve-api-admin-global', tve_editor_url() . '/inc/auto-responder/views/js/admin-global.js', array('jquery'));

    if (strpos($hook, 'thrive_api_error_log') !== false) {
        tve_enqueue_script('tve-api-admin-logs-list', tve_editor_url() . '/inc/auto-responder/views/js/admin-logs-list.js', array('jquery'));
        tve_enqueue_style('tve-list-api', tve_editor_url() . '/inc/auto-responder/views/css/admin.css');
    }

    if (strpos($hook, 'page_tve_api_connect') === false) {
        return;
    }

    tve_enqueue_style('tve-list-api', tve_editor_url() . '/inc/auto-responder/views/css/admin.css');
}

/**
 * for now, just a dump of the error logs from the table
 */
function tve_api_error_log()
{
    require_once dirname(__FILE__) . '/misc.php';

    $table = new Thrive_List_LogsTable(array('ajax' => false));
    $table->prepare_items();

    include plugin_dir_path(__FILE__) . 'views/admin-error-logs.php';
}

/**
 * hide notices for a specific API connection
 */
function tve_api_hide_notice()
{
    if (!wp_verify_nonce($_POST['nonce'], 'tve_api_dismiss')) {
        exit('-1');
    }

    $key = $_POST['key'];

    require_once dirname(__FILE__) . '/misc.php';

    $connection = Thrive_List_Manager::connectionInstance($key);
    $connection->setParam('_nd', 1)->save();

    exit('1');
}
