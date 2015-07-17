<?php

/**
 * this should handle file inclusions, requires etc
 */
require_once plugin_dir_path(__FILE__) . 'inc/db.php';
require_once plugin_dir_path(__FILE__) . 'inc/hooks.php';
require_once plugin_dir_path(__FILE__) . 'inc/helpers.php';
require_once plugin_dir_path(__FILE__) . 'inc/data.php';
require_once plugin_dir_path(__FILE__) . 'inc/triggers.php';
require_once plugin_dir_path(__FILE__) . 'inc/animations.php';
require_once plugin_dir_path(__FILE__) . 'inc/classes/thrive_leads_widget.php';
require_once plugin_dir_path(__FILE__) . 'inc/classes/Thrive_Leads_Request_Handler.php';
require_once plugin_dir_path(__FILE__) . 'inc/classes/Thrive_Leads_Template_Manager.php';

require_once plugin_dir_path(__FILE__) . 'tcb-bridge/tcb_action_hooks.php';

/**
 * at this point, we need to either hook into an existing Content Builder plugin, or use the copy we store in the tcb folder
 */
include_once(ABSPATH . 'wp-admin/includes/plugin.php');
if (!file_exists(dirname(dirname(__FILE__)) . '/thrive-visual-editor/thrive-visual-editor.php') || !is_plugin_active('thrive-visual-editor/thrive-visual-editor.php')) {
    include_once plugin_dir_path(__FILE__) . 'tcb-bridge/init.php';
    define('EXTERNAL_TCB', 0);
} else {
    define('EXTERNAL_TCB', 1);
}

/* check database version and run any necessary update scripts */
require_once plugin_dir_path(__FILE__) . 'database/Thrive_Leads_Database_Manager.php';
Thrive_Leads_Database_Manager::check();

add_action('init', 'tve_leads_init');
add_action('widgets_init', 'tve_leads_widget_init');

/**
 * these need to be added here, because we will make some WP admin ajax calls
 */
/**
 * ajax call that tracks the conversion for a form
 */
add_action('wp_ajax_tve_leads_ajax_conversion', 'tve_leads_ajax_conversion');
/**
 * also for non-authenticated users
 */
add_action('wp_ajax_nopriv_tve_leads_ajax_conversion', 'tve_leads_ajax_conversion');

/**
 * ajax call that tracks the impression for all rendered forms
 */
add_action('wp_ajax_tve_leads_ajax_impression', 'tve_leads_ajax_impression');
/**
 * also for non-authenticated users
 */
add_action('wp_ajax_nopriv_tve_leads_ajax_impression', 'tve_leads_ajax_impression');

if (tve_leads_get_option('ajax_load')) {
    /**
     * load the forms via AJAX requests on DOM ready
     */
    add_action('wp_ajax_tve_leads_ajax_load_forms', 'tve_leads_ajax_load_forms');
    add_action('wp_ajax_nopriv_tve_leads_ajax_load_forms', 'tve_leads_ajax_load_forms');
}

/**
 * logic to be applied on form impression (display) - TL will save the display as a new event in the log table
 */
add_action(TVE_LEADS_ACTION_FORM_IMPRESSION, 'tve_leads_register_impression', 10, 4);

/**
 * logic to be applied on form conversion (successfull submit) - TL will save the conversion as a new event in the log table
 */
add_action(TVE_LEADS_ACTION_FORM_CONVERSION, 'tve_leads_register_conversion', 10, 5);

/**
 * called when a winner is decided in a test (either manually, by admin or automatically, after a conversion)
 *
 * called with 2 parameters:
 *
 *      $winner_test_item - the winner of the test (important data: form_type_id, main_group_id, variation_key, unique_impressions, impressions, conversions)
 *      $test - the actual test containing test items and various data
 *
 */
add_action(TVE_LEADS_ACTION_SET_TEST_ITEM_WINNER, 'tve_leads_set_test_item_winner', 10, 2);

if (defined('DOING_AJAX') && DOING_AJAX) {

    /**
     * hook into the TCB form submission action - this is triggered for forms that are connected to an API
     */
    add_action('tcb_api_form_submit', 'tve_leads_api_form_submit');
}

if (!is_admin()) {

    /**
     * change the page title when editing a TL form
     */
    add_filter('wp_title', 'tve_leads_editor_page_title', 10, 2);

    /**
     * sign end of content with and element to be used in JS
     */
    add_filter('the_content', 'tve_leads_filter_end_content');

    /**
     * action for enqueueing scripts and CSS, but only on display pages (on pages where we have forms to be displayed)
     */
    add_action('wp_enqueue_scripts', 'tve_leads_enqueue_form_scripts');

    /**
     * print the footer JS required for tracking conversions, triggering displays etc
     */
    add_action('wp_print_footer_scripts', 'tve_leads_print_footer_scripts');

    /**
     * The only group that would be displayed for current request
     */
    global $tve_lead_group;
    $tve_lead_group = null;

    /**
     * it seems WP has an issue with redirection causing infinite loop if the administrator has setup the url to have uppercase characters
     */
    add_filter('redirect_canonical', 'tve_leads_canonical_url_lowercase', 10, 2);

    /**
     * this is called before template_redirect hook
     */
    add_action('wp', 'tve_leads_query_group');

}