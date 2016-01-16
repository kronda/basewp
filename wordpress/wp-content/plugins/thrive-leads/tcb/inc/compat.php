<?php
/**
 * this file handles known compatibility issues with other plugins / themes
 */

/**
 * general admin conflict notifications
 */
add_action('admin_notices', 'tve_admin_notices');

/**
 * filter for including wp affiliates scripts and styles if the shortcode is found in TCB content
 */
add_filter('affwp_force_frontend_scripts', 'tve_compat_wp_affiliate_scripts');

/**
 * display any possible conflicts with other plugins / themes as error notification in the admin panel
 */
function tve_admin_notices()
{
    $has_wp_seo_conflict = tve_has_wordpress_seo_conflict();

    if ($has_wp_seo_conflict) {
        $link = sprintf('<a href="%s">%s</a>', admin_url('admin.php?page=wpseo_advanced&tab=permalinks'), __('Wordpress SEO settings'));
        $message = sprintf(__('Thrive Content Builder and Thrive Leads cannot work with the current configuration of Wordpress SEO. Please go to %s and disable the %s"Redirect ugly URL\'s to clean permalinks"%s option', 'thrive-cb'), $link, '<strong>', '</strong>');
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

    /**
     * s3 amazon links - they don't handle shortcodes in the "WP" way
     */
    if (function_exists('s3mv')) {
        $content = s3mv($content);
    }

    /**
     * FD Footnotes plugin does not have standard shortcodes
     */
    if (function_exists('fdfootnote_convert')) {
        $content = fdfootnote_convert($content);
    }

    /**
     * A3 Lazy Load plugin
     * This plugin adds a filter on "the_content" inside of "wp" action callback -> the same as TCB does
     * Its "the_content" filter callback is executed first because of its name -> A3
     * We call its filter implementation on TCB content
     */
    if (class_exists('A3_Lazy_Load') && method_exists('A3_Lazy_Load', 'filter_content_images')) {
        global $a3_lazy_load_global_settings;
        if ($a3_lazy_load_global_settings['a3l_apply_image_to_content'] == true) {
            $content = A3_Lazy_Load::filter_content_images($content);
        }
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

/**
 * check if we are on a page / post and there is a [affiliate_area] shortcode in TCB content
 *
 * @param bool $bool current value
 *
 * @return bool
 */
function tve_compat_wp_affiliate_scripts($bool)
{
    if ($bool || !is_singular() || is_editor_page()) {
        return $bool;
    }

    $tve_saved_content = tve_get_post_meta(get_the_ID(), 'tve_updated_post');

    return has_shortcode($tve_saved_content, 'affiliate_area') || has_shortcode($tve_saved_content, 'affiliate_creatives');

}