<?php

define('TVE_LEADS_ADMIN_URL', plugin_dir_url(__FILE__));

add_action('admin_init', 'tve_leads_admin_init');
add_action('admin_menu', 'tve_leads_admin_menu');
add_action('admin_enqueue_scripts', 'tve_leads_admin_enqueue', 1);
add_action('admin_print_scripts', 'tve_leads_remove_junk_scripts', 11);

add_filter('tve_leads_settings_post_types_blacklist', 'tve_leads_settings_post_types_blacklist');

if (defined('DOING_AJAX') && DOING_AJAX) {
    add_action('tcb_ajax_load', 'tve_leads_admin_ajax_load');
    add_filter('tve_autoresponder_connection_types', 'tve_leads_ajax_filter_connection_types');
    add_filter('tve_autoresponder_show_submit', 'tve_leads_filter_autoresponder_submit_option');
    add_action('wp_ajax_tve_leads_ajax_tag_search', 'tve_leads_ajax_tag_search');
}

require_once plugin_dir_path(__FILE__) . 'inc/helpers.php';

/**
 * called on admin_init hook
 */
function tve_leads_admin_init()
{
    add_action('wp_ajax_thrive_leads_backend_ajax', 'thrive_leads_backend_ajax');

    if (!tve_leads_check_tcb_version()) {
        add_action('admin_notices', 'tve_leads_admin_notice_wrong_tcb_version');
    }
}

/**
 * the TCB version is not compatible with the current TL version
 */
function tve_leads_admin_notice_wrong_tcb_version()
{
    $screen = get_current_screen();
    if ($screen && $screen->parent_base == 'thrive_leads_dashboard') {
        return;
    }
    $html = '<div class="error"><p>%s</p></div>';
    $text = sprintf(__('Current version of Thrive Leads is not compatible with the current version of Thrive Content Builder. Please update both plugins to the latest versions.', 'thrive-leads'));

    if ($screen && $screen->base != 'plugins') {
        $text .= ' <a href="' . admin_url('plugins.php') . '">' . __('Manage plugins', 'thrive-leads') . '</a>';
    }

    echo sprintf($html, $text);
}

/**
 * main entry point for the incoming ajax requests (initiated from backbone)
 *
 * passes the request to the Thrive_Leads_Ajax_Controller for processing
 */
function thrive_leads_backend_ajax()
{
    require_once plugin_dir_path(__FILE__) . '/controllers/Thrive_Leads_Ajax_Controller.php';

    $response = Thrive_Leads_Ajax_Controller::instance()->handle();

    echo json_encode($response);
    exit();
}

/**
 * enqueue all required scripts and css
 *
 * @param string $hook
 */
function tve_leads_admin_enqueue($hook)
{
    /* first, the license check */
    if (!tve_leads_license_activated()) {
        return;
    }

    /* second, the minimum required TCB version */
    if (!tve_leads_check_tcb_version()) {
        return;
    }

    /* load scripts only on our dashboard page, the entry point for the backbone app */
    if ($hook != 'toplevel_page_thrive_leads_dashboard' && $hook != 'thrive-leads_page_thrive_leads_reporting') {
        return;
    }

    /**
     * specific admin styles
     */
    tve_leads_enqueue_style(
        'thrive-leads-admin',
        TVE_LEADS_ADMIN_URL . 'css/styles.css'
    );

    /**
     * overwrite thickbox styles
     */
    tve_leads_enqueue_style(
        'thrive-leads-admin-thickbox',
        TVE_LEADS_ADMIN_URL . 'css/thickbox.css'
    );

    /**
     * tabs styles for Lead Group Display settings
     */
    tve_leads_enqueue_style(
        'thrive-leads-admin-tabs',
        TVE_LEADS_ADMIN_URL . 'css/tabs.css'
    );
    global $tve_leads_chart_colors;

    $data = array(
        'url' => array(
            'wp' => rtrim(get_site_url(), '/') . '/',
            'wp_content' => rtrim(WP_CONTENT_URL, '/') . '/',
            'admin' => rtrim(get_admin_url(), '/') . '/',
            'includes' => includes_url(),
            'reporting' => menu_page_url('thrive_leads_reporting', false)
        ),
        'variation_test_type' => TVE_LEADS_VARIATION_TEST_TYPE,
        'group_test_type' => TVE_LEADS_GROUP_TEST_TYPE,
        'shortcode_test_type' => TVE_LEADS_SHORTCODE_TEST_TYPE,
        'two_step_lightbox_test_type' => TVE_LEADS_TWO_STEP_LIGHTBOX_TEST_TYPE,
        'test_status' => array(
            'running' => TVE_LEADS_TEST_STATUS_RUNNING,
            'archived' => TVE_LEADS_TEST_STATUS_ARCHIVED
        ),
        'tve_leads_post_form_type' => TVE_LEADS_POST_FORM_TYPE,
        'tve_leads_post_shortcode_type' => TVE_LEADS_POST_SHORTCODE_TYPE,
        'tve_leads_post_two_step_lightbox_type' => TVE_LEADS_POST_TWO_STEP_LIGHTBOX,
        'templates' => tve_load_backbone_templates(plugin_dir_path(__FILE__) . 'views' . DIRECTORY_SEPARATOR . 'template'),
        'default_form_types' => tve_leads_prepare_default_form_types(),
        'translations' => require plugin_dir_path(__FILE__) . '/inc/translations.php',
        'CHART_RED' => '#F60000',
        'CHART_GREEN' => '#006600',
        'CHART_GREY' => '#C0C0C0',
        'CHART_COLORS' => $tve_leads_chart_colors
    );

    /**
     * some plugins that insert scripts incorrectly on our pages
     */
    wp_dequeue_style('youzign_style');
    wp_deregister_style('youzign_style');

    wp_enqueue_script('jquery');
    add_thickbox();
    wp_enqueue_script('backbone');

    tve_leads_enqueue_script('thrive-leads-init', plugins_url('thrive-leads/admin/js-min/_init.js'), array('jquery', 'backbone'));

    wp_localize_script('thrive-leads-init', 'ThriveLeadsConst', $data);

    tve_leads_enqueue_script('tve-leads-views', plugins_url('thrive-leads/admin/js-min/views.js'), array('jquery', 'backbone', 'thrive-leads-init'));
    tve_leads_enqueue_script('tve-leads-models', plugins_url('thrive-leads/admin/js-min/models.js'), array('jquery', 'backbone', 'thrive-leads-init'));

    //copy text in clipboard
    wp_enqueue_script('jquery-zclip', plugins_url('thrive-leads/js/jquery.zclip.1.1.1/jquery.zclip.js'), array('jquery', 'thrive-leads-init'));

    //autocomplete
    wp_enqueue_script('jquery-ui-autocomplete');

    /* wystia script for popover videos */
    wp_enqueue_script('tl-wistia-popover', '//fast.wistia.com/assets/external/popover-v1.js', array(), '', true);

    if ($hook == 'toplevel_page_thrive_leads_dashboard') {
        wp_enqueue_script('jquery-ui-slider', false, array('jquery'));
        wp_enqueue_script('jquery-ui-sortable', false, array('jquery'));
        wp_enqueue_script('tve-leads-highcharts', plugins_url('thrive-leads/admin/js-min/reporting/highcharts.js'), array('jquery', 'thrive-leads-init'));
        wp_enqueue_script('tve-leads-highcharts-more', plugins_url('thrive-leads/admin/js-min/reporting/highcharts-more.js'), array('jquery', 'thrive-leads-init'));
        tve_leads_enqueue_script('tve-leads-charts', plugins_url('thrive-leads/admin/js-min/reporting/charts.js'), array('jquery', 'backbone', 'thrive-leads-init'));
        tve_leads_enqueue_script('tve-leads-routes', plugins_url('thrive-leads/admin/js-min/routes.js'), array(
            'jquery',
            'backbone',
            'jquery-ui-sortable',
            'thrive-leads-init',
            'tve-leads-views',
            'tve-leads-models',
            'thickbox'));
        tve_leads_enqueue_script('tve-leads-dashboard', plugins_url('thrive-leads/admin/js-min/dashboard.js'), array(
            'jquery',
            'backbone',
            'tve-leads-routes',
            'tve-leads-models',
            'tve-leads-views',
        ));
    }

    if ($hook == 'thrive-leads_page_thrive_leads_reporting') {
        tve_leads_enqueue_script('tve-leads-reporting-routes', plugins_url('thrive-leads/admin/js-min/reporting/routes.js'), array('jquery', 'backbone', 'thrive-leads-init', 'tve-leads-reporting-views', 'tve-leads-reporting-models'));
        tve_leads_enqueue_script('tve-leads-reporting-views', plugins_url('thrive-leads/admin/js-min/reporting/views.js'), array('jquery', 'backbone', 'thrive-leads-init', 'tve-leads-views'));
        tve_leads_enqueue_script('tve-leads-reporting-models', plugins_url('thrive-leads/admin/js-min/reporting/models.js'), array('jquery', 'backbone', 'thrive-leads-init', 'tve-leads-models'));
        tve_leads_enqueue_script('tve-leads-highcharts', plugins_url('thrive-leads/admin/js-min/reporting/highcharts.js'), array('jquery', 'thrive-leads-init'));
        tve_leads_enqueue_script('tve-leads-charts', plugins_url('thrive-leads/admin/js-min/reporting/charts.js'), array('jquery', 'backbone', 'thrive-leads-init'));
        tve_leads_enqueue_script('jquery-ui-datepicker');
        tve_leads_enqueue_script('tve-leads-reporting', plugins_url('thrive-leads/admin/js-min/reporting/reporting.js'), array(
            'jquery',
            'backbone',
            'tve-leads-reporting-routes',
            'tve-leads-reporting-models',
            'tve-leads-reporting-views',
        ));
    }
}

/**
 * some plugins add their scripts via admin_print_scripts - this is not cool
 *
 */
function tve_leads_remove_junk_scripts()
{
    /* the following lines address the "Show the URL" plugin - what a piece of software ... ! */
    wp_dequeue_script('smpso_zclip');
    wp_deregister_script('smpso_zclip');
    wp_dequeue_script('smpso_custom');
    wp_deregister_script('smpso_custom');
}

/**
 * add the admin menu link for the dashboard page
 */
function tve_leads_admin_menu()
{
    add_menu_page(
        "Thrive Leads",
        "Thrive Leads",
        "manage_options",
        "thrive_leads_dashboard",
        "thrive_leads_dashboard",
        TVE_LEADS_ADMIN_URL . 'img/logo-icon.png'
    );

    add_submenu_page("thrive_leads_dashboard", "Thrive Leads Dashboard", "Dashboard", "manage_options", "thrive_leads_dashboard", "thrive_leads_dashboard");
    add_submenu_page("thrive_leads_dashboard", "Thrive Leads Reporting", "Reporting", "manage_options", "thrive_leads_reporting", "thrive_leads_reporting");

    /**
     * page to redirect the user to when he needs to activate his license
     */
    add_options_page('Thrive Leads', 'Thrive Leads', 'manage_options', 'tve_leads_license_activation', 'tve_leads_license_activation');
}

/**
 * output Thrive Leads dashboard - the main plugin page
 */
function thrive_leads_dashboard()
{
    if (!tve_leads_license_activated()) {
        return tve_leads_license_warning();
    }

    if (!tve_leads_check_tcb_version()) {
        return tve_leads_tcb_version_warning();
    }

    global $tvedb;

    $dashboard_data = array(
        'global_settings' => array(
            'ajax_load' => tve_leads_get_option('ajax_load'),
        ),
        'groups' => tve_leads_get_groups(),
        'shortcodes' => tve_leads_get_shortcodes(array(
            'active_test' => true,
            'tracking_data' => true,
            'get_variations' => true
        )),
        'two_step_lightbox' => tve_leads_get_two_step_lightboxes(array(
            'active_test' => true,
            'tracking_data' => true,
            'get_variations' => true
        )),
        'summary' => array(
            'impressions' => tve_leads_get_tracking_data(TVE_LEADS_UNIQUE_IMPRESSION, array('date' => 'today', 'is_unique' => 1)),
            'conversions' => tve_leads_get_tracking_data(TVE_LEADS_CONVERSION, array('date' => 'today')),
        ),
        'has_non_unique_impressions' => $tvedb->count_non_unique_impressions()
    );
    include dirname(__FILE__) . '/views/dashboard.php';
}

/**
 * output Thrive Leads reporting - the main plugin page
 */
function thrive_leads_reporting()
{
    if (!tve_leads_license_activated()) {
        return tve_leads_license_warning();
    }

    if (!tve_leads_check_tcb_version()) {
        return tve_leads_tcb_version_warning();
    }

    $reporting_data = array(
        'lead_groups' => tve_leads_get_groups(
            array(
                'full_data' => false,
                'tracking_data' => false,
                'completed_tests' => false,
                'active_tests' => false,
            )
        ),
        'shortcodes' => tve_leads_get_shortcodes(
            array('active_test' => false)
        ),
        'two_step_lightbox' => tve_leads_get_two_step_lightboxes(
            array('active_test' => false)
        )
    );
    include dirname(__FILE__) . '/views/reporting.php';
}

/**
 * output a page where users can activate their license
 */
function tve_leads_license_activation()
{
    include dirname(dirname(__FILE__)) . '/inc/license_activation.php';
}

/**
 * AJAX- load a custom file through the WP api
 *
 * @param string $ajax_load
 */
function tve_leads_admin_ajax_load($ajax_load)
{
    $base = plugin_dir_path(dirname(__FILE__));
    switch ($ajax_load) {
        case 'thrive_leads_templates':
            include $base . 'editor-lightbox/lb_templates.php';
            break;
    }

    exit();
}

/**
 * This filter should strip out the HTML connection type for set up a lead generation element
 * This filter should be applied only if the form type is short-code with content locking option true
 *
 * @param $connection_types
 * @return mixed
 * @author Dan
 */
function tve_leads_ajax_filter_connection_types($connection_types)
{
    $shortcode = tve_leads_get_shortcode($_POST['post_id']);

    if (!$shortcode) {
        return $connection_types;
    }

    if ($shortcode->content_locking) {
        $types['api'] = __('API', 'thrive-leads');

        return $types;
    }

    return $connection_types;
}

/**
 * check if the form being edited is a content locking shortcode and if so, hide the "After the form is submitted" options
 *
 * @param bool $show_submit
 * @return bool
 */
function tve_leads_filter_autoresponder_submit_option($show_submit)
{
    $shortcode = tve_leads_get_shortcode($_POST['post_id']);

    if (!$shortcode || !$shortcode->content_locking) {
        return true;
    }

    return false;
}