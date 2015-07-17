<?php

/*
Plugin Name: Thrive Visual Editor
Plugin URI: http://www.thrivethemes.com
Version: 1.98
Author: <a href="http://www.thrivethemes.com">Thrive Themes</a>
Description: Live front end editor for your Wordpress content
*/

if (!defined('TVE_TCB_CORE_INCLUDED')) {
    require_once plugin_dir_path(__FILE__) . 'plugin-core.php';
}

/** plugin updates script **/
require 'plugin-updates/plugin-update-checker.php';
$MyUpdateChecker = new PluginUpdateChecker(
    'http://members.thrivethemes.com/plugin_versions/content_builder/update.json',
    __FILE__,
    'thrive-visual-editor'
);

/**
 * admin licensing menu link
 */
add_action('admin_menu', 'tve_add_settings_menu');

add_action('wp_enqueue_scripts', 'tve_frontend_enqueue_scripts');

// add the same tve_editor_filter but on this case on Landing Page templates - only applies to TCB
add_filter('tve_landing_page_content', 'tve_editor_content');

// add filter for including the TCB meta into the search functionality - this is only required on the TCB editor
add_filter('posts_clauses', 'tve_process_search_clauses', null, 2);

add_filter('get_the_content_limit', 'tve_genesis_get_post_excerpt', 10, 4);

// automatically modify lightbox title if the title of the associated landing page is modified - applies ony to TCB
add_action('save_post', 'tve_save_post_callback');

// integration with YOAST SEO
add_filter('wpseo_pre_analysis_post_content', 'tve_yoast_seo_integration');

// YOAST sitemaps - add image links
add_filter('wpseo_sitemap_urlimages', 'tve_yoast_sitemap_images', 10, 2);

if (!function_exists('tve_editor_url')) {
    /**
     * @return string the absolute url to this plugin's folder
     */
    function tve_editor_url()
    {
        return plugins_url() . '/thrive-visual-editor';
    }
}

/**
 * enqueue scripts for the frontend - also editor and preview
 */
function tve_frontend_enqueue_scripts()
{
    if (!is_editor_page_raw()) {
        /**
         * enqueue scripts and styles only for posts / pages that actually have tcb content
         */
        global $wp_query;
        if (empty($wp_query->posts)) {
            return;
        }
        $enqueue_tcb_resources = false;
        foreach ($wp_query->posts as $_post) {
            if (tve_get_post_meta($_post->ID, 'tve_updated_post')) {
                $enqueue_tcb_resources = true;
                break;
            }
        }
        $enqueue_tcb_resources = apply_filters('tcb_enqueue_resources', $enqueue_tcb_resources);
        if (!$enqueue_tcb_resources) {
            return;
        }
    }
    wp_enqueue_style("tve_default", tve_editor_css() . '/thrive_default.css');
    wp_enqueue_style("tve_colors", tve_editor_css() . '/thrive_colors.css');
    tve_enqueue_style_family();

    tve_enqueue_script("tve_frontend", tve_editor_js() . '/thrive_content_builder_frontend.min.js', array('jquery'), false, true);

    if (!is_editor_page() && is_singular()) {
        $events = tve_get_post_meta(get_the_ID(), 'tve_page_events');
        if (!empty($events)) {
            tve_page_events($events);
        }
    }

    /* params for the frontend script */
    $frontend_options = array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'is_editor_page' => true,
        'page_events' => isset($events) ? $events : array(),
        'is_single' => (string)((int)is_singular())
    );
    // hide tve more tag from front end display
    if (!is_editor_page()) {
        tve_load_custom_css();
        tve_hide_more_tag();
        tve_enqueue_custom_fonts();
        tve_enqueue_custom_scripts();
        $frontend_options['is_editor_page'] = false;
    }
    wp_localize_script('tve_frontend', 'tve_frontend_options', $frontend_options);
}

/**
 * output the admin license validation page
 */
function tve_license_validation()
{
    include('tve_settings.php');
}

/**
 * add the options link to the admin menu
 */
function tve_add_settings_menu()
{
    add_options_page('Thrive Content Builder', 'Thrive Content Builder', 'manage_options', 'tve_license_validation', 'tve_license_validation');
}

/**
 * include the TCB saved meta into query search fields
 *
 * wordpress actually allows inserting post META fields in the search query,
 * but it will always build the clauses with AND (between post content and post meta) e.g.:
 *  WHERE (posts.title LIKE '%xx%' OR posts.post_content) AND (postsmeta.meta_key = 'tve_save_post' AND postsmeta.meta_value LIKE '%xx%')
 *
 * - we cannot use this, so we hook into the final pieces of the built SQL query - we need a solution like this:
 *  WHERE ( (posts.title LIKE '%xx%' OR posts.post_content OR (postsmeta.meta_key = 'tve_save_post' AND postsmeta.meta_value LIKE '%xx%') )
 *
 * @param array $pieces
 * @param WP_Query $wpQuery
 */
function tve_process_search_clauses($pieces, $wpQuery)
{
    if (is_admin() || empty($pieces) || !$wpQuery->is_search()) {
        return $pieces;
    }
    /** @var wpdb $wpdb */
    global $wpdb;

    $query = '';
    $n = !empty($q['exact']) ? '' : '%';
    $q = $wpQuery->query_vars;
    if (!empty($q['search_terms'])) {
        foreach ($q['search_terms'] as $term) {
            if (method_exists($wpdb, 'esc_like')) { // WP4
                $term = $wpdb->esc_like($term);
            } else {
                $term = like_escape($term); // like escape is deprecated in WP4
            }

            $like = $n . $term . $n;
            $query .= "((tve_pm.meta_key = 'tve_save_post')";
            $query .= $wpdb->prepare(" AND (tve_pm.meta_value LIKE %s)) OR ", $like);
        }
    }

    if (!empty($query)) {
        // add to where clause
        $pieces['where'] = str_replace("((({$wpdb->posts}.post_title LIKE '{$n}", "( {$query} (({$wpdb->posts}.post_title LIKE '{$n}", $pieces['where']);

        $pieces['join'] = $pieces['join'] . " LEFT JOIN {$wpdb->postmeta} AS tve_pm ON ({$wpdb->posts}.ID = tve_pm.post_id)";

        if (empty($pieces['groupby'])) {
            $pieces['groupby'] = "{$wpdb->posts}.ID";
        }
    }

    return ($pieces);
}

/**
 * Handler for "get_the_content_limit" action applied by genesis themes
 *
 * Called on pages with posts list
 * If posts was created with TCB the more_element link is searched. If it is found the content before it is returned.
 * If more_element is not found the post's content added from admin is appended with TCB content then truncation is applied
 *
 * @param string $output Truncated content post by genesis
 * @param string $content the stripped and truncated genesis content
 * @param string $link the read more link
 * @param int $max_characters the maximum number of characters to truncate to
 *
 * @return string $content
 */
function tve_genesis_get_post_excerpt($output, $content, $link, $max_characters)
{
    global $post;
    $post_id = get_the_ID();

    if (!tve_check_in_loop($post_id)) {
        tve_load_custom_css($post_id);
    }

    if (!is_singular()) {
        $more_found = tve_get_post_meta(get_the_ID(), "tve_content_more_found", true);
        $content_before_more = tve_get_post_meta(get_the_ID(), "tve_content_before_more", true);
        if (!empty($content_before_more) && $more_found) {
            $more_link = apply_filters('the_content_more_link', '<a href="' . get_permalink() . '#more-' . $post->ID . '" class="more-link">Continue Reading</a>', 'Continue Reading');
            $content = "<div id='tve_editor' class='tve_shortcode_editor'>" .
                stripslashes($content_before_more) .
                $more_link .
                "</div>";
            return tve_restore_script_tags($content);
        }

        $tcb_content = tve_restore_script_tags(stripslashes(tve_get_post_meta(get_the_ID(), "tve_updated_post", true)));
        if (!$tcb_content) {
            return $output;
        }

        /**
         * inherited from genesis logic
         */
        $tcb_content = strip_tags(strip_shortcodes($tcb_content), apply_filters('get_the_content_limit_allowedtags', '<script>,<style>'));

        $tcb_content = trim(preg_replace('#<(s(cript|tyle)).*?</\1>#si', '', $tcb_content));

        // append the original genesis content
        $tcb_content .= $content;
        $tcb_content = genesis_truncate_phrase($tcb_content, $max_characters);
        $tcb_content = sprintf('<p>%s %s</p>', $tcb_content, $link);

        return $tcb_content;
    }

    return $output;
}

/**
 * render all necessary things for page-level event manager
 *
 * @param $events
 */
function tve_page_events($events)
{
    $triggers = tve_get_event_triggers('page');
    $actions = tve_get_event_actions('page');

    /* hold all the javascript callbacks required for the identified actions */
    $javascript_callbacks = isset($GLOBALS['tve_event_manager_callbacks']) ? $GLOBALS['tve_event_manager_callbacks'] : array();

    /* holds all the Global JS required by different actions and event triggers on page load */
    $registered_javascript_globals = isset($GLOBALS['tve_event_manager_global_js']) ? $GLOBALS['tve_event_manager_global_js'] : array();

    /* hold all instances of the Action classes in order to output stuff in the footer, we need to get out of the_content filter */
    $registered_actions = isset($GLOBALS['tve_event_manager_actions']) ? $GLOBALS['tve_event_manager_actions'] : array();

    /* each trigger instance might also need a bit of javascript to trigger it */
    $registered_triggers = isset($GLOBALS['tve_event_manager_triggers']) ? $GLOBALS['tve_event_manager_triggers'] : array();

    /*
     * all page level events
     */
    foreach ($events as $index => $event_config) {
        if (empty($event_config['t']) || empty($event_config['a']) || !isset($triggers[$event_config['t']]) || !isset($actions[$event_config['a']])) {
            continue;
        }
        /** @var TCB_Event_Action_Abstract $action */
        $action = $actions[$event_config['a']];
        $registered_actions [] = array(
            'class' => $action,
            'event_config' => $event_config
        );

        /** @var TCB_Event_Trigger_Abstract $trigger */
        $trigger = $triggers[$event_config['t']];
        $registered_triggers [] = array(
            'class' => $trigger,
            'event_config' => $event_config
        );

        if (!isset($javascript_callbacks[$event_config['a']])) {
            $javascript_callbacks[$event_config['a']] = $action->getJsActionCallback();
        }
        if (!isset($registered_javascript_globals['action_' . $event_config['a']])) {
            $registered_javascript_globals['action_' . $event_config['a']] = $action;
        }
        if (!isset($registered_javascript_globals['trigger_' . $event_config['t']])) {
            $registered_javascript_globals['trigger_' . $event_config['t']] = $trigger;
        }
    }

    if (empty($javascript_callbacks)) {
        return;
    }

    /* include the Event Manager css file only at this point, when we know that we'll have events on page */
    tve_enqueue_style('thrive_events', tve_editor_css() . '/events.css');

    /* we need to add all the javascript callbacks into the page */
    /* this cannot be done using wp_localize_script WP function, as each if the callback will actually be JS code */
    ///euuuughhh

    //TODO: how could we handle this in a more elegant fashion ?
    $GLOBALS['tve_event_manager_callbacks'] = $javascript_callbacks;
    $GLOBALS['tve_event_manager_global_js'] = $registered_javascript_globals;
    $GLOBALS['tve_event_manager_actions'] = $registered_actions;
    $GLOBALS['tve_event_manager_triggers'] = $registered_triggers;

    /* execute the mainPostCallback on all of the related actions, some of them might need to register stuff (e.g. lightboxes) */
    foreach ($GLOBALS['tve_event_manager_actions'] as $key => $item) {
        if (empty($item['main_post_callback_'])) {
            $GLOBALS['tve_event_manager_actions'][$key]['main_post_callback_'] = true;
            $item['class']->mainPostCallback($item['event_config']);
        }
    }

    /* remove previously assigned callback, if any */
    remove_action('wp_print_footer_scripts', 'tve_print_footer_events');
    add_action('wp_print_footer_scripts', 'tve_print_footer_events');
}

/**
 * When a page is edited from admin -> we need to use the same title for the associated lightbox, if the page in question is a landing page
 * Copy post tve meta to revision meta
 *
 * This method is also called when a revision of a post is added
 * @see wp_insert_post which is doing: "post_updated", "save_post"
 * @see defaults-filters.php for add_action("post_updated")
 *
 * @param $post_id
 */
function tve_save_post_callback($post_id)
{
    /**
     * If $post_id is an ID of a revision POST
     */
    if ($parent_id = wp_is_post_revision($post_id)) {

        $meta_keys = tve_get_used_meta_keys($parent_id);

        /**
         * copy post metas to its revision
         */
        foreach($meta_keys as $meta_key) {
            if($meta_key === 'tve_landing_page') {
                $meta_value = get_post_meta($parent_id, $meta_key, true);
            } else {
                $meta_value = tve_get_post_meta($parent_id, $meta_key);
            }
            add_metadata('post', $post_id, "tve_revision_" . $meta_key, $meta_value);
        }
    }

    $post_type = get_post_type($post_id);
    if ($post_type != 'page') {
        return;
    }
    $is_landing_page = tve_post_is_landing_page($post_id);
    $tve_globals = tve_get_post_meta($post_id, 'tve_globals');

    if (!$is_landing_page || empty($tve_globals['lightbox_id'])) {
        return;
    }
    $lightbox = get_post($tve_globals['lightbox_id']);
    if (!$lightbox) {
        return;
    }

    wp_update_post(array(
        'ID' => $tve_globals['lightbox_id'],
        'post_title' => 'Lightbox - ' . get_the_title($post_id)
    ));
}


/**
 * integration with Wordpress SEO for page analysis.
 *
 * @param string $content WP post_content
 *
 * @return string $content
 */
function tve_yoast_seo_integration($content)
{
    $post_id = get_the_ID();
    if ($post_id && !tve_is_post_type_editable(get_post_type($post_id))) {
        return $content;
    }

    /**
     * if the post is actually a Landing Page, we need to reset all previously saved content, as TCB content is the only one shown
     */
    if ($lp_template = tve_post_is_landing_page($post_id)) {
        $content = '';
    }

    $tve_saved_content = tve_get_post_meta(get_the_ID(), "tve_updated_post");

    $tve_saved_content = preg_replace('#<p(.*?)>(.*?)</p>#s', '<p>$2</p>', $tve_saved_content);
    $tve_saved_content = str_replace('<p></p>', '', $tve_saved_content);

    $content = $tve_saved_content . " " . $content;
    return $content;
}

/**
 * add TCB content images to the sitemap
 *
 * @param array $images
 * @param $post_id
 *
 * @return array
 */
function tve_yoast_sitemap_images($images, $post_id)
{
    $post_type = get_post_type($post_id);
    $p = get_post($post_id);

    if (!tve_is_post_type_editable($post_type, $post_id)) {
        return $images;
    }
    $home_url = home_url();
    $parsed_home = parse_url( $home_url );
    $host        = '';
    $scheme      = 'http';
    if ( isset( $parsed_home['host'] ) && ! empty( $parsed_home['host'] ) ) {
        $host = str_replace( 'www.', '', $parsed_home['host'] );
    }
    if ( isset( $parsed_home['scheme'] ) && ! empty( $parsed_home['scheme'] ) ) {
        $scheme = $parsed_home['scheme'];
    }

    /**
     * if the post is actually a Landing Page, we need to reset all other images and return just the ones setup in the landing page
     */
    if ($lp_template = tve_post_is_landing_page($post_id)) {
        $images = array();
    }
    $content = tve_get_post_meta($post_id, 'tve_updated_post');

    if ( preg_match_all( '`<img [^>]+>`', $content, $matches ) ) {

        foreach ( $matches[0] as $img ) {
            if ( preg_match( '`src=["\']([^"\']+)["\']`', $img, $match ) ) {
                $src = $match[1];
                if ( WPSEO_Utils::is_url_relative( $src ) === true ) {
                    if ( $src[0] !== '/' ) {
                        continue;
                    }
                    else {
                        // The URL is relative, we'll have to make it absolute
                        $src = $this->home_url . $src;
                    }
                }
                elseif ( strpos( $src, 'http' ) !== 0 ) {
                    // Protocol relative url, we add the scheme as the standard requires a protocol
                    $src = $scheme . ':' . $src;

                }

                if ( strpos( $src, $host ) === false ) {
                    continue;
                }

                if ( $src != esc_url( $src ) ) {
                    continue;
                }

                $image = array(
                    'src' => apply_filters( 'wpseo_xml_sitemap_img_src', $src, $p )
                );

                if ( preg_match( '`title=["\']([^"\']+)["\']`', $img, $title_match ) ) {
                    $image['title'] = str_replace( array( '-', '_' ), ' ', $title_match[1] );
                }
                unset( $title_match );

                if ( preg_match( '`alt=["\']([^"\']+)["\']`', $img, $alt_match ) ) {
                    $image['alt'] = str_replace( array( '-', '_' ), ' ', $alt_match[1] );
                }
                unset( $alt_match );

                $image           = apply_filters( 'wpseo_xml_sitemap_img', $image, $p );
                $images[] = $image;
            }
            unset( $match, $src );
        }
    }

    return $images;
}
