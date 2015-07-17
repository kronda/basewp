<?php

/*
Plugin Name: Thrive Leads
Plugin URI: https://thrivethemes.com
Version: 1.33
Author: <a href="https://thrivethemes.com">Thrive Themes</a>
Description: The ultimate lead capture solution for Wordpress
Text Domain: thrive-leads
*/

///** plugin updates script **/
require dirname(__FILE__) . '/plugin-updates/plugin-update-checker.php';
$MyUpdateChecker = new PluginUpdateChecker(
    'http://members.thrivethemes.com/plugin_versions/thrive_leads/update.json',
    __FILE__,
    'thrive-leads'
);

define('TVE_LEADS_TEST_DATA', false);

define('TVE_LEADS_VERSION', '1.33');

define('TVE_LEADS_DB_VERSION', '1.13');

/**
 * Required version of TCB
 */
define('TVE_REQUIRED_TCB_VERSION', 1.98);

/* the base URL for the plugin */
define('TVE_LEADS_URL', plugin_dir_url(__FILE__));
define('TVE_LEADS_DB_PREFIX', 'tve_leads_');

/* we keep these as integers */
define('TVE_LEADS_UNIQUE_IMPRESSION', 1);
define('TVE_LEADS_CONVERSION', 2);
define('TVE_LEADS_IMPRESSION', 3);

/* POST statuses */
define('TVE_LEADS_STATUS_PUBLISH', 'publish');
define('TVE_LEADS_STATUS_ARCHIVED', 'archived');
define('TVE_LEADS_STATUS_COMPLETED', 'completed');
define('TVE_LEADS_STATUS_RUNNING', 'running');

/**
 * test types
 */
define('TVE_LEADS_VARIATION_TEST_TYPE', 1);
define('TVE_LEADS_GROUP_TEST_TYPE', 2);
define('TVE_LEADS_SHORTCODE_TEST_TYPE', 3);
define('TVE_LEADS_TWO_STEP_LIGHTBOX_TEST_TYPE', 4);

/**
 * post types
 */
define('TVE_LEADS_POST_FORM_TYPE', 'tve_form_type');
define('TVE_LEADS_POST_GROUP_TYPE', 'tve_lead_group');
define('TVE_LEADS_POST_SHORTCODE_TYPE', 'tve_lead_shortcode');
define('TVE_LEADS_POST_TWO_STEP_LIGHTBOX', 'tve_lead_2s_lightbox');

/**
 * test statuses
 * Values of ENUM set for Status column in db for table wp_tve_leads_split_test
 */
define('TVE_LEADS_TEST_STATUS_RUNNING', 'running');
define('TVE_LEADS_TEST_STATUS_ARCHIVED', 'archived');

/**
 * Actions
 */
define('TVE_LEADS_ACTION_FORM_IMPRESSION', 'tve_leads_form_impression'); //hook to apply some logic for the form that is displayed
define('TVE_LEADS_ACTION_FORM_CONVERSION', 'tve_leads_form_conversion'); //allow hooking into a successful conversion

/* called when a test winner is chosen */
define('TVE_LEADS_ACTION_SET_TEST_ITEM_WINNER', 'tve_leads_action_set_test_item_winner');

/**
 * Filters
 */
define('TVE_LEADS_FILTER_DISPLAY_GROUP_FORM', 'tve_leads_filter_display_group_form'); //filter for displaying form

/**
 * bootstrap everything
 */
require_once plugin_dir_path(__FILE__) . 'start.php';

/* admin entry point */
if (is_admin()) {
    require_once plugin_dir_path(__FILE__) . 'admin/start.php';
}