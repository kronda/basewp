<?php
/**
 * this file handles known compatibility issues with other plugins / themes
 */

/**
 * general admin conflict notifications
 */
add_action('admin_notices', 'tve_admin_notices');

/**
 * display any possible conflicts with other plugins / themes as error notification in the admin panel
 */
function tve_admin_notices()
{
    $has_wp_seo_conflict = tve_has_wordpress_seo_conflict();

    if ($has_wp_seo_conflict) {
        $link = sprintf('<a href="%s">%s</a>', admin_url('admin.php?page=wpseo_advanced&tab=permalinks'), __('Wordpress SEO settings'));
        $message = sprintf(__('Thrive Content Builder and Thrive Leads cannot work with the current configuration of Wordpress SEO. Please go to %s and disable the %s"Redirect ugly URL\'s to clean permalinks"%s option', 'thrive-visual-editor'), $link, '<strong>', '</strong>');
        echo sprintf('<div class="error"><p>%s</p></div>', $message);
    }
}

/**
 * check if the user has a known "Coming soon" or "Membership protection" plugin installed
 * our landing pages seem to overwrite their "Coming soon" functionality
 * this would check for any coming soon plugins that use the template_redirect hook
 */
function tve_hooked_in_template_redirect()
{
    include_once ABSPATH . '/wp-admin/includes/plugin.php';

    $hooked_in_template_redirect = array(
        'wishlist-member/wpm.php',
        'ultimate-coming-soon-page/ultimate-coming-soon-page.php',
        'easy-pie-coming-soon/easy-pie-coming-soon.php',
        'coming-soon-page/coming_soon.php',
        'cc-coming-soon/cc-coming-soon.php',
        'wordpress-seo/wp-seo.php',
    );

    foreach ($hooked_in_template_redirect as $plugin) {
        if (is_plugin_active($plugin)) {
            return true;
        }
    }

    return false;
}

/**
 * Check if the user has the Wordpress SEO plugin installed and the "Redirect to clean URLs" option checked
 *
 * @return bool
 */
function tve_has_wordpress_seo_conflict()
{
    return is_plugin_active('wordpress-seo/wp-seo.php') && ($wpseo_options = get_option('wpseo_permalinks')) && !empty($wpseo_options['cleanpermalinks']);
}


/**
 * called inside the 'init' hook
 *
 * this is used to fix any plugin conflicts that might appear
 *
 * 1. YARPP - we need to disable their the_content filter when in editing mode,
 *      - they apply the_content filter automatically when querying the database for related posts
 *      - they have a filter for blacklisting a filters the_content, but that does not solve the issue - wp will never call our filter anymore
 *
 * 2. TheRetailer theme - they remove the WP media js files for some reason (??)
 */
function tve_fix_plugin_conflicts()
{
    global $yarpp;
    if (is_editor_page_raw()) {
        if ($yarpp) {
            remove_filter('the_content', array($yarpp, 'the_content'), 1200);
        }
        /**
         * Theretailer theme deregisters the mediaelement for some reason
         */
        if (function_exists('theretailer_deregister')) {
            remove_action('wp_enqueue_scripts', 'theretailer_deregister');
        }
    }
}

/**
 * apply some of currently known 3rd party filters to the TCB saved_content
 *
 * Digital Access Pass: dap_*
 *
 * @param string $content
 *
 * @return string
 */
function tve_compat_content_filters_before_shortcode($content)
{
    /**
     * Digital Access Pass %% links in the content, e.g.: %%LOGIN_FORM%%
     */
    if (function_exists('dap_login')) {
        $content = dap_login($content);
    }

    if (function_exists('dap_personalize')) {
        $content = dap_personalize($content);
    }

    if (function_exists('dap_personalize_error')) {
        $content = dap_personalize_error($content);
    }

    if (function_exists('dap_product_links')) {
        $content = dap_product_links($content);
    }

    return $content;
}


/**
 * apply some of currently known 3rd party filters to the TCB saved_content - after do_shortcode is being called
 *
 * FormMaker: Form_maker_fornt_end_main
 * @param string $content
 *
 * @return string
 */
function tve_compat_content_filters_after_shortcode($content)
{
    /**
     * FormMaker does not use WP shortcode as they should
     */
    if (function_exists('Form_maker_fornt_end_main')) {
        $content = Form_maker_fornt_end_main($content);
    }

    /**
     * in case they will ever correct the function name
     */
    if (function_exists('Form_maker_front_end_main')) {
        $content = Form_maker_front_end_main($content);
    }

    return $content;
}