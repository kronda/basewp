<?php

/**
 * Constant to be used in other Thrive products
 * If this constant is defined then this application should not be called
 */
defined('THRIVE_DASHBOARD_INCLUDED') || define('THRIVE_DASHBOARD_INCLUDED', TRUE);

defined('THRIVE_DASHBOARD_LICENSE_API_URL') || define('THRIVE_DASHBOARD_LICENSE_API_URL', 'https://thrivethemes.com/wp-content/plugins/license_check/api/request.php');
defined('THRIVE_DASHBOARD_PRODUCTS_URL') || define('THRIVE_DASHBOARD_PRODUCTS_URL', 'https://thrivethemes.com/wp-content/plugins/license_check/api/product_list.php');
defined('THRIVE_DASHBOARD_DOWNLOAD_URL') || define('THRIVE_DASHBOARD_DOWNLOAD_URL', 'https://thrivethemes.com/members/main/?tag=');
defined('THRIVE_DASHBOARD_OFFER_IFRAME_URL') || define('THRIVE_DASHBOARD_OFFER_IFRAME_URL', 'http://thrivethemes.com/');

require_once plugin_dir_path(__FILE__) . "inc/functions.php";

require_once plugin_dir_path(__FILE__) . 'auto-responder/admin.php';

/**
 * Autoresponder APIs AJAX calls
 */
if (defined('DOING_AJAX') && DOING_AJAX) {
    require_once dirname(__FILE__) . '/auto-responder/misc.php';
    add_action('wp_ajax_tve_api_form_retry', 'tve_api_form_retry');
    add_action('wp_ajax_tve_api_delete_log', 'tve_api_delete_log');
}


/** ACTIONS */
add_action('admin_init', 'thrive_dashboard_admin_init');
add_action('admin_menu', 'thrive_dashboard_admin_menu');
add_action('admin_enqueue_scripts', 'thrive_dashboard_admin_enqueue', 1);
add_action('thrive_dashboard_thrive_icon_manager_integration', "thrive_dashboard_icon_manager");
add_action('thrive_dashboard_thrive_font_manager_integration', "thrive_dashboard_font_manager");
add_action('thrive_dashboard_thrive_auto_responder_integration', "thrive_dashboard_auto_responder");

/** FILTERS */
add_filter('thrive_dashboard_integrations_products', 'thrive_dashboard_add_integrations');

/**
 * add the admin menu link for the dashboard page
 */
function thrive_dashboard_admin_menu()
{
    add_menu_page(
        "Thrive Dashboard",
        "Thrive Dashboard",
        "manage_options",
        "thrive_dashboard_section",
        "thrive_dashboard_section",
        thrive_dashboard_url() . 'css/images/logo-icon.png'
    );

    add_submenu_page("thrive_dashboard_section", "Thrive Dashboard", "Dashboard", "manage_options", "thrive_dashboard_section", "thrive_dashboard_section");

    /**
     * @param thrive_dashboard_menu_tag
     */
    do_action("thrive_dashboard_add_menu_item", "thrive_dashboard_section");

    add_submenu_page("thrive_dashboard_section", "Thrive Integrations", "Integrations", "manage_options", "thrive_integrations_section", "thrive_integrations_section");
    add_submenu_page("thrive_dashboard_section", "Thrive Licence Manager", "License Manager", "manage_options", "thrive_license_section", "thrive_license_section");
}

/**
 * Display Dashboard Section by including dashboard.phtml template file
 *
 * @call apply_filters with tag "thrive_dashboard_installed_products"
 */
function thrive_dashboard_section()
{
    $thrive_themes = thrive_dashboard_get_all_themes();

    $products = thrive_dashboard_get_products();

    $thrive_products = thrive_dashboard_get_thrive_products('plugin');

    if (count($thrive_products)) {
        foreach ($thrive_products as $key => $other) {
            foreach ($products as $product) {
                if ($other->tag == $product['tag']) {
                    unset($thrive_products[$key]);
                    continue;
                }
            }
        }
    }

    /** @var $other_products array Used in template */
    $other_products = empty($thrive_products) ? array() : array_values($thrive_products); //reset keys

    include plugin_dir_path(__FILE__) . "templates/dashboard.phtml";
}

/**
 * Display Integration page
 */
function thrive_integrations_section()
{
    $products = thrive_dashboard_get_integrations_products();

    $integration = !empty($_GET['integration']) ? $_GET['integration'] : null;

    if ($integration) {
        do_action("thrive_dashboard_{$integration}_integration");
        return;
    }

    include plugin_dir_path(__FILE__) . "templates/integrations.phtml";
}

/**
 * Display products license status and process the requests
 */
function thrive_license_section()
{
    if (isset($_POST['product_tag'])) {
        $product = $_POST['product_tag'];
        $license_key = isset($_POST['license_key']) ? $_POST['license_key'] : '';
        $license_email = isset($_POST['license_email']) ? $_POST['license_email'] : '';

        $api_product_ids = !empty($_POST['product_ids']) && is_array($_POST['product_ids']) ? $_POST['product_ids'] : array();
        $api_product_ids = apply_filters('thrive_dashboard_license_' . $product . '_product_ids', $api_product_ids);

        $result = thrive_dashboard_license_check($license_email, $license_key, $api_product_ids);

        if (isset($result->success) && $result->success == 1) {

            //everything went well and we can activate the product
            do_action('thrive_dashboard_activate_' . $product);
            $message = array(
                'text' => apply_filters('thrive_dashboard_' . $product . '_license_valid_message', __('Thank you - You have successfully validated your license!')),
                'css_class' => 'success'
            );
        } elseif ($result->success == 0) {
            // some kind of error
            $message = array(
                'text' => $result->reason,
                'css_class' => 'danger'
            );
        } else {
            // big time error - fatal error
            $message = array(
                'text' => __("License activation error - please contact support copying this message and we'll get this sorted for you."),
                'css_class' => 'danger'
            );
        }

        if (!empty($message)) {
            thrive_dashboard_add_message($message);
        }
    }

    $selected = !empty($_GET['selected']) ? $_GET['selected'] : null;
    $no_licensed_products = thrive_dashboard_get_no_license_products();
    $licensed_products = thrive_dashboard_get_licensed_products();

    include plugin_dir_path(__FILE__) . "templates/license.phtml";
}

/**
 * Load Dashboard styles and scripts
 */
function thrive_dashboard_admin_enqueue($hook)
{
    $accepted_hooks = array(
        'toplevel_page_thrive_dashboard_section',
        'thrive-dashboard_page_thrive_integrations_section',
        'thrive-dashboard_page_thrive_license_section'
    );

    if(!in_array($hook, $accepted_hooks)) {
        return;
    }

    if (!wp_script_is('jquery', 'enqueued')) {
        wp_enqueue_script('jquery');
    }
    thrive_dashboard_enqueue_script('thrive-dashboard-main', thrive_dashboard_url() . 'js-min/main.js', array('jquery'));
    thrive_dashboard_enqueue_style('thrive-dashboard-admin', thrive_dashboard_url() . 'css/styles.css');
}

/**
 *Initialize the dashboard admin
 */
function thrive_dashboard_admin_init()
{
    /** init font manager here because of the ajax requests */
    require_once plugin_dir_path(__FILE__) . 'font-manager/font-manager.php';

    require_once plugin_dir_path(__FILE__) . 'auto-responder/admin.php';
}

/**
 * Add default integrations which will be available on all our products - Icon Manager and Font Manager
 * @param $products
 * @return array
 */
function thrive_dashboard_add_integrations($products)
{
    $icon_manager = array(
        'url' => add_query_arg(array('page' => 'thrive_integrations_section', 'integration' => 'thrive_icon_manager'), admin_url('admin.php')),
        'name' => __('Icon Manager'),
        'image_url' => thrive_dashboard_url() . 'css/images/icon-font-manager.png',
        'description' => __('Import a custom icon pack for use in your content')
    );
    $products[] = $icon_manager;

    $font_manager = array(
        'url' => add_query_arg(array('page' => 'thrive_integrations_section', 'integration' => 'thrive_font_manager'), admin_url('admin.php')),
        'name' => __("Font Manager"),
        'image_url' => thrive_dashboard_url() . 'css/images/icon-icon-manager.png',
        'description' => __('Choose from a range of over 600 Google fonts')
    );
    $products[] = $font_manager;

    $auto_responder = array(
        'url' => add_query_arg(array('page' => 'thrive_integrations_section', 'integration' => 'thrive_auto_responder'), admin_url('admin.php')),
        'name' => __("API Connections"),
        'image_url' => thrive_dashboard_url() . 'css/images/icon-icon-manager.png',
        'description' => __('API Connection description of the integration')
    );
    $products[] = $auto_responder;

    return $products;
}

/**
 * Hook implemented for Icon Manager integration requests
 * When users access the the Icon Manager section(integration)
 */
function thrive_dashboard_icon_manager()
{
    /** require this file and let it do its job */
    require_once plugin_dir_path(__FILE__) . "icon-manager/init.php";
}

/**
 * Hook implemented for Font Manager integration requests
 * When users access the the Font Manager section(integration)
 */
function thrive_dashboard_font_manager()
{
    /** enqueue some fonts before */
    thrive_dashboard_enqueue_font_manager();

    /** display font manager page */
    thrive_font_manager();
}

function thrive_dashboard_auto_responder()
{
    tve_api_connect();
}
