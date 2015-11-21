<?php
add_action("wp_ajax_nopriv_header_number_render_preview", "thrive_header_number_render_preview");
add_action("wp_ajax_header_number_render_preview", "thrive_header_number_render_preview");

add_action('admin_init', 'thrive_theme_options_init');
/*
 * Initialize the theme options page
 */

function thrive_theme_options_init()
{

    $section_tooltips = _thrive_get_theme_options_sections_tooltips();
    $options = thrive_get_theme_options();
    $options_titles = _thrive_get_theme_options_fields_titles();
    register_setting('thrive_options', 'thrive_theme_options', 'thrive_theme_options_validate');

    add_settings_section('general', 'General', '__return_false', 'theme_options');
    add_settings_section('global_settings', __('Global settings', 'thrive'), '__return_false', 'theme_global_options');
    add_settings_section('style_settings', __('Style settings', 'thrive'), '__return_false', 'theme_style_options');
    add_settings_section('featured_image_settings', __('Featured image', 'thrive'), '__return_false', 'theme_featured_image_options');
    add_settings_section('meta_info_settings', __('Meta info', 'thrive') . $section_tooltips['meta_info_settings'], '__return_false', 'theme_meta_info_options');
    add_settings_section('bottom_posts_settings', __('Bottom posts', 'thrive'), '__return_false', 'theme_bottom_posts_options');
    add_settings_section('other_blog_settings', __('Other settings', 'thrive'), '__return_false', 'theme_other_blog_options');
    add_settings_section('analytics_settings', __('Analytics & Scripts', 'thrive'), '__return_false', 'theme_analytics_options');
    add_settings_section('performance_settings', __('Performance', 'thrive'), '__return_false', 'theme_performance_options');
    add_settings_section('comments_settings', __('Facebook Comments', 'thrive'), '__return_false', 'theme_comments_options');
    add_settings_section('comments_blog_settings', __('Comments', 'thrive'), '__return_false', 'theme_comments_blog_options');
    add_settings_section('related_posts_settings', __('Related posts', 'thrive'), '__return_false', 'theme_related_posts_options');
    add_settings_section('related_box_settings', '', '__return_false', 'theme_related_box_options');
    add_settings_section('customizer_settings', '', '__return_false', 'theme_customizer_options');
    add_settings_section('page_templates_settings', '', '__return_false', 'theme_page_templates_options');
    add_settings_section('social_buttons_settings', __('Buttons', 'thrive'), '__return_false', 'theme_social_button_options');
    add_settings_section('social_display_settings', __('Where to display', 'thrive'), '__return_false', 'theme_social_display_options');
    add_settings_section('social_advanced_settings', __('Display & Advanced', 'thrive'), '__return_false', 'theme_social_advanced_options');
    add_settings_section('social_sharing_data', __('Social Sharing Data', 'thrive') . $section_tooltips['social_sharing_data'], '__return_false', 'theme_social_sharing_data_options');
    add_settings_section('404tpl_settings', '404', '__return_false', 'theme_404tpl_options');

    //apprentice sections
    add_settings_section('appr_enable_settings', __('Enable apprentince feature', 'thrive'), '__return_false', 'theme_appr_enable_options');
    add_settings_section('appr_layout_settings', __('Apprentince layout settings', 'thrive'), '__return_false', 'theme_appr_layout_options');
    add_settings_section('appr_blog_settings', __('Apprentince Meta', 'thrive'), '__return_false', 'theme_appr_blog_options');
    add_settings_section('appr_features_settings', __('Apprentince features', 'thrive'), '__return_false', 'theme_appr_feature_options');
    add_settings_section('appr_url_settings', __('URLs', 'thrive'), '__return_false', 'theme_appr_url_options');
    add_settings_section('appr_menu_settings', __('Apprentince menus&widgets settings', 'thrive'), '__return_false', 'theme_appr_menu_options');

    add_settings_field('logo_type', $options_titles['logo_type'], 'thrive_settings_field_logo_type', 'theme_options', 'general', $options);
    add_settings_field('logo', $options_titles['logo'], 'thrive_settings_field_logo', 'theme_options', 'general', $options);
    add_settings_field('logo_text', $options_titles['logo_text'], 'thrive_settings_field_logo_text', 'theme_options', 'general', $options);
    add_settings_field('logo_color', $options_titles['logo_color'], 'thrive_settings_field_logo_color', 'theme_options', 'general', $options);
    add_settings_field('logo_position', $options_titles['logo_position'], 'thrive_settings_field_logo_position', 'theme_options', 'general', $options);
    add_settings_field('header_phone', $options_titles['header_phone'], 'thrive_settings_field_header_phone', 'theme_options', 'general', $options['header_phone']);
    add_settings_field('header_phone_no', $options_titles['header_phone_no'], 'thrive_settings_field_header_phone_no', 'theme_options', 'general', $options['header_phone_no']);
    add_settings_field('header_phone_text', $options_titles['header_phone_text'], 'thrive_settings_field_header_phone_text', 'theme_options', 'general', $options['header_phone_text']);
    add_settings_field('header_phone_text_mobile', $options_titles['header_phone_text_mobile'], 'thrive_settings_field_header_phone_text_mobile', 'theme_options', 'general', $options['header_phone_text_mobile']);
    add_settings_field('header_phone_btn_color', $options_titles['header_phone_btn_color'], 'thrive_settings_field_header_phone_btn_color', 'theme_options', 'general', $options['header_phone_btn_color']);
    if (!empty($options['favicon'])) {
        add_settings_field('favicon', $options_titles['favicon'], 'thrive_settings_field_favicon', 'theme_options', 'general', $options);
    }
    add_settings_field('footer_copyright', $options_titles['footer_copyright'], 'thrive_settings_field_footer_copyright', 'theme_options', 'general', $options);
    add_settings_field('footer_copyright_links', $options_titles['footer_copyright_links'], 'thrive_settings_field_footer_copyright_links', 'theme_options', 'general', $options);
    add_settings_field('display_breadcrumbs', $options_titles['display_breadcrumbs'], 'thrive_settings_field_display_breadcrumbs', 'theme_global_options', 'global_settings', $options);
    add_settings_field('comments_on_pages', $options_titles['comments_on_pages'], 'thrive_settings_field_comments_on_pages', 'theme_comments_blog_options', 'comments_blog_settings', $options);
    add_settings_field('relative_time', $options_titles['relative_time'], 'thrive_settings_field_relative_time', 'theme_global_options', 'global_settings', $options);
    add_settings_field('highlight_author_comments', $options_titles['highlight_author_comments'], 'thrive_settings_field_highlight_author_comments', 'theme_comments_blog_options', 'comments_blog_settings', $options);
    add_settings_field('color_scheme', $options_titles['color_scheme'], 'thrive_settings_field_color_scheme', 'theme_style_options', 'style_settings', $options);
    add_settings_field('sidebar_alignement', $options_titles['sidebar_alignement'], 'thrive_settings_field_sidebar_alignement', 'theme_style_options', 'style_settings', $options);
    add_settings_field('blog_post_layout', $options_titles['blog_post_layout'], 'thrive_settings_field_blog_post_layout', 'theme_style_options', 'style_settings', $options);
    add_settings_field('extended_menu', $options_titles['extended_menu'], 'thrive_settings_field_extended_menu', 'theme_style_options', 'style_settings', $options);
    add_settings_field('custom_css', $options_titles['custom_css'], 'thrive_settings_field_custom_css', 'theme_style_options', 'style_settings', $options);
    add_settings_field('navigation_type', $options_titles['navigation_type'], 'thrive_settings_field_navigation_type', 'theme_style_options', 'style_settings', $options);
    add_settings_field('featured_image_style', $options_titles['featured_image_style'], 'thrive_settings_field_featured_image_style', 'theme_featured_image_options', 'featured_image_settings', $options);
    add_settings_field('featured_image_single_post', $options_titles['featured_image_single_post'], 'thrive_settings_field_featured_image_single_post', 'theme_featured_image_options', 'featured_image_settings', $options);
    add_settings_field('meta_author_name', $options_titles['meta_author_name'], 'thrive_settings_field_meta_author_name', 'theme_meta_info_options', 'meta_info_settings', $options);
    add_settings_field('meta_post_date', $options_titles['meta_post_date'], 'thrive_settings_field_meta_post_date', 'theme_meta_info_options', 'meta_info_settings', $options);
    add_settings_field('meta_post_category', $options_titles['meta_post_category'], 'thrive_settings_field_meta_post_category', 'theme_meta_info_options', 'meta_info_settings', $options);
    add_settings_field('meta_comment_count', $options_titles['meta_comment_count'], 'thrive_settings_field_meta_comment_count', 'theme_comments_blog_options', 'comments_blog_settings', $options);
    add_settings_field('meta_post_tags', $options_titles['meta_post_tags'], 'thrive_settings_field_meta_post_tags', 'theme_meta_info_options', 'meta_info_settings', $options);
    add_settings_field('bottom_about_author', $options_titles['bottom_about_author'], 'thrive_settings_field_bottom_about_author', 'theme_bottom_posts_options', 'bottom_posts_settings', $options);
    add_settings_field('bottom_previous_next', $options_titles['bottom_previous_next'], 'thrive_settings_field_bottom_previous_next', 'theme_bottom_posts_options', 'bottom_posts_settings', $options);
    add_settings_field('related_posts_box', $options_titles['related_posts_box'], 'thrive_settings_field_related_posts_box', 'theme_related_box_options', 'related_box_settings', $options);
    add_settings_field('related_posts_images', $options_titles['related_posts_images'], 'thrive_settings_field_related_posts_images', 'theme_related_box_options', 'related_box_settings', $options);
    add_settings_field('related_posts_title', $options_titles['related_posts_title'], 'thrive_settings_field_related_posts_title', 'theme_related_box_options', 'related_box_settings', $options);
    add_settings_field('related_posts_number', $options_titles['related_posts_number'], 'thrive_settings_field_related_posts_number', 'theme_related_box_options', 'related_box_settings', $options);
    add_settings_field('other_read_more_type', $options_titles['other_read_more_type'], 'thrive_settings_field_other_read_more_type', 'theme_other_blog_options', 'other_blog_settings', $options);
    add_settings_field('other_read_more_text', $options_titles['other_read_more_text'], 'thrive_settings_field_other_read_more_text', 'theme_other_blog_options', 'other_blog_settings', $options);
    add_settings_field('other_show_comment_date', $options_titles['other_show_comment_date'], 'thrive_settings_field_other_show_comment_date', 'theme_comments_blog_options', 'comments_blog_settings', $options);
    add_settings_field('other_show_excerpt', $options_titles['other_show_excerpt'], 'thrive_settings_field_other_show_excerpt', 'theme_other_blog_options', 'other_blog_settings', $options);
    add_settings_field('hide_cats_from_blog', $options_titles['hide_cats_from_blog'], 'thrive_settings_field_hide_cats_from_blog', 'theme_other_blog_options', 'other_blog_settings', $options);
    add_settings_field('analytics_header_script', $options_titles['analytics_header_script'], 'thrive_settings_field_analytics_header_script', 'theme_analytics_options', 'analytics_settings', $options);
    add_settings_field('analytics_body_script_top', $options_titles['analytics_body_script_top'], 'thrive_settings_field_analytics_body_script_top', 'theme_analytics_options', 'analytics_settings', $options);
    add_settings_field('analytics_body_script', $options_titles['analytics_body_script'], 'thrive_settings_field_analytics_body_script', 'theme_analytics_options', 'analytics_settings', $options);
    add_settings_field('image_optimization_type', $options_titles['image_optimization_type'], 'thrive_settings_field_image_optimization_type', 'theme_performance_options', 'performance_settings', $options);
    add_settings_field('comments_lazy', $options_titles['comments_lazy'], 'thrive_settings_field_comments_lazy', 'theme_performance_options', 'performance_settings', $options);
    add_settings_field('enable_fb_comments', $options_titles['enable_fb_comments'], 'thrive_settings_field_enable_fb_comments', 'theme_comments_options', 'comments_settings', $options);
    add_settings_field('fb_app_id', $options_titles['fb_app_id'], 'thrive_settings_field_fb_app_id', 'theme_comments_options', 'comments_settings', $options);
    add_settings_field('fb_no_comments', $options_titles['fb_no_comments'], 'thrive_settings_field_fb_no_comments', 'theme_comments_options', 'comments_settings', $options);
    add_settings_field('fb_color_scheme', $options_titles['fb_color_scheme'], 'thrive_settings_field_fb_color_scheme', 'theme_comments_options', 'comments_settings', $options);
    add_settings_field('privacy_tpl_website', $options_titles['privacy_tpl_website'], 'thrive_settings_field_privacy_tpl_website', 'theme_page_templates_options', 'page_templates_settings', $options);
    add_settings_field('privacy_tpl_company', $options_titles['privacy_tpl_company'], 'thrive_settings_field_privacy_tpl_company', 'theme_page_templates_options', 'page_templates_settings', $options);
    add_settings_field('privacy_tpl_contact', $options_titles['privacy_tpl_contact'], 'thrive_settings_field_privacy_tpl_contact', 'theme_page_templates_options', 'page_templates_settings', $options);
    add_settings_field('privacy_tpl_address', $options_titles['privacy_tpl_address'], 'thrive_settings_field_privacy_tpl_address', 'theme_page_templates_options', 'page_templates_settings', $options);
    add_settings_field('enable_social_buttons', $options_titles['enable_social_buttons'], 'thrive_settings_field_enable_social_buttons', 'theme_social_button_options', 'social_buttons_settings', $options);
    add_settings_field('enable_floating_icons', $options_titles['enable_floating_icons'], 'thrive_settings_field_enable_floating_icons', 'theme_social_button_options', 'social_buttons_settings', $options);
    add_settings_field('enable_twitter_button', $options_titles['enable_twitter_button'], 'thrive_settings_field_enable_twitter_button', 'theme_social_button_options', 'social_buttons_settings', $options);
    add_settings_field('social_twitter_username', $options_titles['social_twitter_username'], 'thrive_settings_field_social_twitter_username', 'theme_social_button_options', 'social_buttons_settings', $options);
    add_settings_field('enable_facebook_button', $options_titles['enable_facebook_button'], 'thrive_settings_field_enable_facebook_button', 'theme_social_button_options', 'social_buttons_settings', $options);
    add_settings_field('enable_google_button', $options_titles['enable_google_button'], 'thrive_settings_field_enable_google_button', 'theme_social_button_options', 'social_buttons_settings', $options);
    add_settings_field('enable_linkedin_button', $options_titles['enable_linkedin_button'], 'thrive_settings_field_enable_linkedin_button', 'theme_social_button_options', 'social_buttons_settings', $options);
    add_settings_field('enable_pinterest_button', $options_titles['enable_pinterest_button'], 'thrive_settings_field_enable_pinterest_button', 'theme_social_button_options', 'social_buttons_settings', $options);
    add_settings_field('social_display_location', $options_titles['social_display_location'], 'thrive_settings_field_social_display_location', 'theme_social_display_options', 'social_display_settings', $options);
    add_settings_field('social_attention_grabber', $options_titles['social_attention_grabber'], 'thrive_settings_field_social_attention_grabber', 'theme_social_advanced_options', 'social_advanced_settings', $options);
    add_settings_field('social_cta_text', $options_titles['social_cta_text'], 'thrive_settings_field_social_cta_text', 'theme_social_advanced_options', 'social_advanced_settings', $options);
    //add_settings_field('social_add_like_btn', $options_titles['social_add_like_btn'], 'thrive_settings_field_social_add_like_btn', 'theme_social_advanced_options', 'social_advanced_settings', $options);
    add_settings_field('social_site_meta_enable', $options_titles['social_site_meta_enable'], 'thrive_settings_field_social_site_meta_enable', 'theme_social_sharing_data_options', 'social_sharing_data', $options);
    add_settings_field('social_site_name', $options_titles['social_site_name'], 'thrive_settings_field_social_site_name', 'theme_social_sharing_data_options', 'social_sharing_data', $options);
    add_settings_field('social_site_title', $options_titles['social_site_title'], 'thrive_settings_field_social_site_title', 'theme_social_sharing_data_options', 'social_sharing_data', $options);
    add_settings_field('social_site_description', $options_titles['social_site_description'], 'thrive_settings_field_social_site_description', 'theme_social_sharing_data_options', 'social_sharing_data', $options);
    add_settings_field('social_site_image', $options_titles['social_site_image'], 'thrive_settings_field_social_site_image', 'theme_social_sharing_data_options', 'social_sharing_data', $options);
    add_settings_field('social_site_twitter_username', $options_titles['social_site_twitter_username'], 'thrive_settings_field_social_site_twitter_username', 'theme_social_sharing_data_options', 'social_sharing_data', $options);
    add_settings_field('404_custom_text', $options_titles['404_custom_text'], 'thrive_settings_field_404_custom_text', 'theme_404tpl_options', '404tpl_settings', isset($options['404_custom_text']) ? $options['404_custom_text'] : "");
    add_settings_field('404_display_sitemap', $options_titles['404_display_sitemap'], 'thrive_settings_field_404_display_sitemap', 'theme_404tpl_options', '404tpl_settings', isset($options['404_display_sitemap']) ? $options['404_display_sitemap'] : "");
    add_settings_field('related_no_text', $options_titles['related_no_text'], 'thrive_settings_field_related_no_text', 'theme_related_posts_options', 'related_posts_settings', isset($options['related_no_text']) ? $options['related_no_text'] : "");
    add_settings_field('related_ignore_cats', $options_titles['related_ignore_cats'], 'thrive_settings_field_related_ignore_cats', 'theme_related_posts_options', 'related_posts_settings', isset($options['related_no_text']) ? $options['related_ignore_cats'] : "");
    add_settings_field('related_ignore_tags', $options_titles['related_ignore_tags'], 'thrive_settings_field_related_ignore_tags', 'theme_related_posts_options', 'related_posts_settings', isset($options['related_no_text']) ? $options['related_ignore_tags'] : "");
    add_settings_field('related_number_posts', $options_titles['related_number_posts'], 'thrive_settings_field_related_number_posts', 'theme_related_posts_options', 'related_posts_settings', isset($options['related_no_text']) ? $options['related_number_posts'] : "");

    add_settings_section('social_settings', __('Social footer links', 'thrive'), '__return_false', 'theme_social_options');
    add_settings_section('client_settings', __('Client logos', 'thrive'), '__return_false', 'theme_client_options');

    add_settings_field('social_facebook', $options_titles['social_facebook'], 'thrive_settings_field_social_facebook', 'theme_social_options', 'social_settings', $options);
    add_settings_field('social_twitter', $options_titles['social_twitter'], 'thrive_settings_field_social_twitter', 'theme_social_options', 'social_settings', $options);
    add_settings_field('social_pinterest', $options_titles['social_pinterest'], 'thrive_settings_field_social_pinterest', 'theme_social_options', 'social_settings', $options);
    add_settings_field('social_gplus', $options_titles['social_gplus'], 'thrive_settings_field_social_gplus', 'theme_social_options', 'social_settings', $options);
    add_settings_field('social_youtube', $options_titles['social_youtube'], 'thrive_settings_field_social_youtube', 'theme_social_options', 'social_settings', $options);
    add_settings_field('social_linkedin', $options_titles['social_linkedin'], 'thrive_settings_field_social_linkedin', 'theme_social_options', 'social_settings', $options);
    add_settings_field('client_logos', $options_titles['client_logos'], 'thrive_settings_field_client_logos', 'theme_client_options', 'client_settings', $options);
}

/*
 * Set the default theme options
 */

function thrive_set_default_theme_options()
{
    $default_options = thrive_get_default_theme_options();
    $options_to_reset = array('custom_css', 'analytics_header_script', 'analytics_body_script');

    foreach ($default_options as $key => $option) {
        if (in_array($key, $options_to_reset) && get_option($key)) {
            update_option($key, $option);
        }
    }
}

/*
 * Get all the theme options or a specific one if a $key is specified
 * @param string $key Optional - the name of a specific option
 * @return mixed An array or values or a specific value
 */

function thrive_get_theme_options($key = null, $postId = 0)
{

    $options = thrive_filter_default_theme_options(get_option('thrive_theme_options'), thrive_get_default_theme_options());

    if ($key && isset($options[$key])) {
        return $options[$key];
    }

    $options['thrive_follow_widget_facebook'] = get_option('thrive_follow_widget_facebook');

    $options['display_meta'] = 0;
    if ((isset($options['meta_author_name']) && $options['meta_author_name'] == 1) || (isset($options['meta_post_date']) && $options['meta_post_date'] == 1) || (isset($options['meta_post_category']) && $options['meta_post_category'] == 1 && (get_the_category($postId))) || (isset($options['meta_post_tags']) && $options['meta_post_tags'] == 1) && (get_the_tags($postId))) {
        $options['display_meta'] = 1;
    }
    $options['meta_no_columns'] = 0;
    if ((isset($options['meta_author_name']) && $options['meta_author_name'] == 1)) {
        $options['meta_no_columns']++;
    }
    if ((isset($options['meta_post_date']) && $options['meta_post_date'] == 1)) {
        $options['meta_no_columns']++;
    }
    if ((isset($options['meta_post_category']) && $options['meta_post_category'] == 1)) {
        $options['meta_no_columns']++;
    }
    if ((isset($options['meta_post_tags']) && $options['meta_post_tags'] == 1)) {
        $options['meta_no_columns']++;
    }

    return $options;
}

/*
 * Render the theme options page
 */

function thrive_theme_options_render_page()
{

    wp_enqueue_script('thrive-theme-options');

    //prepare the javascript params
    $wpnonce = wp_create_nonce("thrive_preview_header_number_nonce");
    $headerPhonePreviewUrl = admin_url('admin-ajax.php?action=header_number_render_preview&nonce=' . $wpnonce);

    $related_nonce = wp_create_nonce("thrive_generate_related_posts");
    $generateRelatedUrl = admin_url('admin-ajax.php?action=thrive_generate_related_posts&nonce=' . $related_nonce);

    $optimize_wpnonce = wp_create_nonce("thrive_optimize_image_sizes");
    $optimizeImagesUrl = admin_url('admin-ajax.php?action=thrive_optimize_image_sizes&nonce=' . $optimize_wpnonce);

    $default_wp_sizes_nonce = wp_create_nonce("thrive_image_resize_use_default_wordpress_sizes");
    $default_wp_sizes_url = admin_url('admin-ajax.php?action=thrive_image_resize_use_default_wordpress_sizes&nonce=' . $default_wp_sizes_nonce);

    $js_params_array = array('headerPhonePreviewUrl' => $headerPhonePreviewUrl,
        'generateRelatedUrl' => $generateRelatedUrl,
        'optimizeImagesUrl' => $optimizeImagesUrl,
        'optimizeImagesStatuses' => array(
            'error' => TT_IMG_RESIZE_STATUS_ERROR,
            'not_started' => TT_IMG_RESIZE_STATUS_NOT_STARTED,
            'started' => TT_IMG_RESIZE_STATUS_STARTED,
            'finished' => TT_IMG_RESIZE_STATUS_FINISHED
        ),
        'optimizeImagesTypes' => array(
            'scale' => TT_IMG_RESIZE_TYPE_SCALE,
            'scale_and_crop' => TT_IMG_RESIZE_TYPE_SCALE_AND_CROP,
            'default' => TT_IMG_RESIZE_TYPE_DEFAULT
        ),
        'optimizeImagesLabels' => array(
            'in_progress' => __("Thumbnails resizing in progress", 'thrive'),
            'paused' => __("Click to resume the image optimization process", 'thrive'),
            'finished' => __("Your images are optimized for the current theme", 'thrive'),
            'not_started' => __("Click to start the image optimization process", 'thrive'),
            'set_wp_default' => __("Click to use the default images", 'thrive'),
            'resized_msg' => __("Image resized ", 'thrive')
        ),
        'useDefaultWpSizesUrl' => $default_wp_sizes_url,
        'noonce' => $wpnonce);

    wp_localize_script('thrive-theme-options', 'ThriveThemeOptions', $js_params_array);

    require(get_template_directory() . "/inc/templates/admin-theme-options.php");
}

/*
 * Validate the theme options
 */

function thrive_theme_options_validate($input)
{
    $current_options = thrive_get_theme_options();

    /*
     * Stop here if the input is not coming from one of our pages or if the user
     * doesn't have the permissions to modify the theme options
     * (if the favicons is not set it means that is not coming from the theme options
     * and if privacy_tpl_website is not set in the post it means that is not coming from
     * the page templates options
     */

    if (!current_user_can('edit_theme_options') || (!isset($input['logo']) && !isset($input['privacy_tpl_website']))) {
        return $current_options;
    }

    $defaults = thrive_get_default_theme_options();

    $output = $current_options;

    if (isset($input['logo'])) {
        $output['logo'] = $input['logo'];
        $logo_size = @getimagesize($input['logo']);
        if ($logo_size) {
            list($logo_width, $logo_height, $logo_type, $logo_attr) = @getimagesize($input['logo']);
            if ($logo_height) {
                $output['logo_height'] = $logo_height;
            }
            if ($logo_width) {
                $output['logo_width'] = $logo_width;
            }
        }
    }

    if (isset($input['favicon'])) {
        $output['favicon'] = $input['favicon'];
    }
    if (isset($input['footer_copyright'])) {
        $output['footer_copyright'] = $input['footer_copyright'];
    }
    if (isset($input['footer_copyright_links'])) {
        $output['footer_copyright_links'] = $input['footer_copyright_links'];
    }
    if (isset($input['display_breadcrumbs'])) {
        $output['display_breadcrumbs'] = $input['display_breadcrumbs'];
    }
    if (isset($input['comments_on_pages'])) {
        $output['comments_on_pages'] = $input['comments_on_pages'];
    }
    if (isset($input['color_scheme'])) {
        $output['color_scheme'] = $input['color_scheme'];
    }
    if (isset($input['sidebar_alignement'])) {
        $output['sidebar_alignement'] = $input['sidebar_alignement'];
    }
    if (isset($input['extended_menu'])) {
        $output['extended_menu'] = $input['extended_menu'];
    }
    if (isset($input['custom_css'])) {
        $output['custom_css'] = $input['custom_css'];
    }
    if (isset($input['header_navigation'])) {
        $output['header_navigation'] = $input['header_navigation'];
    }
    if (isset($input['featured_image_style'])) {
        $output['featured_image_style'] = $input['featured_image_style'];
    }
    if (isset($input['featured_image_single_post'])) {
        $output['featured_image_single_post'] = $input['featured_image_single_post'];
    }
    if (isset($input['meta_author_name'])) {
        $output['meta_author_name'] = $input['meta_author_name'];
    }
    if (isset($input['meta_post_date'])) {
        $output['meta_post_date'] = $input['meta_post_date'];
    }
    if (isset($input['meta_post_category'])) {
        $output['meta_post_category'] = $input['meta_post_category'];
    }
    if (isset($input['meta_post_tags'])) {
        $output['meta_post_tags'] = $input['meta_post_tags'];
    }
    if (isset($input['meta_comment_count'])) {
        $output['meta_comment_count'] = $input['meta_comment_count'];
    }
    if (isset($input['bottom_about_author'])) {
        $output['bottom_about_author'] = $input['bottom_about_author'];
    }
    if (isset($input['bottom_previous_next'])) {
        $output['bottom_previous_next'] = $input['bottom_previous_next'];
    }
    if (isset($input['related_posts_box'])) {
        $output['related_posts_box'] = $input['related_posts_box'];
    }
    if (isset($input['related_posts_title'])) {
        $output['related_posts_title'] = $input['related_posts_title'];
    }
    if (isset($input['related_posts_number'])) {
        $output['related_posts_number'] = $input['related_posts_number'];
    }
    if (isset($input['related_posts_images'])) {
        $output['related_posts_images'] = $input['related_posts_images'];
    }
    if (isset($input['other_read_more_type'])) {
        $output['other_read_more_type'] = $input['other_read_more_type'];
    }
    if (isset($input['other_read_more_text'])) {
        $output['other_read_more_text'] = $input['other_read_more_text'];
    }
    if (isset($input['hide_cats_from_blog'])) {
        $output['hide_cats_from_blog'] = $input['hide_cats_from_blog'];
    }
    if (isset($input['analytics_header_script'])) {
        $output['analytics_header_script'] = $input['analytics_header_script'];
    }
    if (isset($input['analytics_body_script'])) {
        $output['analytics_body_script'] = $input['analytics_body_script'];
    }
    if (isset($input['analytics_body_script_top'])) {
        $output['analytics_body_script_top'] = $input['analytics_body_script_top'];
    }
    if (isset($input['other_show_comment_date'])) {
        $output['other_show_comment_date'] = $input['other_show_comment_date'];
    }
    if (isset($input['image_optimization_type'])) {
        $output['image_optimization_type'] = $input['image_optimization_type'];
    }
    if (isset($input['relative_time'])) {
        $output['relative_time'] = $input['relative_time'];
    }
    if (isset($input['highlight_author_comments'])) {
        $output['highlight_author_comments'] = $input['highlight_author_comments'];
    }
    if (isset($input['social_facebook'])) {
        $output['social_facebook'] = $input['social_facebook'];
    }
    if (isset($input['social_twitter'])) {
        $output['social_twitter'] = $input['social_twitter'];
    }
    if (isset($input['social_youtube'])) {
        $output['social_youtube'] = $input['social_youtube'];
    }
    if (isset($input['social_gplus'])) {
        $output['social_gplus'] = $input['social_gplus'];
    }
    if (isset($input['social_linkedin'])) {
        $output['social_linkedin'] = $input['social_linkedin'];
    }
    if (isset($input['social_pinterest'])) {
        $output['social_pinterest'] = $input['social_pinterest'];
    }
    if (isset($input['client_logos'])) {
        $output['client_logos'] = $input['client_logos'];
    }
    if (isset($input['color_scheme']) && $input['color_scheme'] != $current_options['color_scheme']) {
        thrive_set_default_customizer_options($input['color_scheme'], true);
    }
    if (isset($input['comments_lazy'])) {
        $output['comments_lazy'] = $input['comments_lazy'];
    }
    if (isset($input['enable_fb_comments'])) {
        $output['enable_fb_comments'] = $input['enable_fb_comments'];
    }
    if (isset($input['fb_app_id'])) {
        $output['fb_app_id'] = $input['fb_app_id'];
    }
    if (isset($input['fb_no_comments'])) {
        $output['fb_no_comments'] = $input['fb_no_comments'];
    }
    if (isset($input['fb_color_scheme'])) {
        $output['fb_color_scheme'] = $input['fb_color_scheme'];
    }

    if (isset($input['privacy_tpl_website'])) {
        $output['privacy_tpl_website'] = $input['privacy_tpl_website'];
    }
    if (isset($input['privacy_tpl_address'])) {
        $output['privacy_tpl_address'] = $input['privacy_tpl_address'];
    }
    if (isset($input['privacy_tpl_company'])) {
        $output['privacy_tpl_company'] = $input['privacy_tpl_company'];
    }
    if (isset($input['privacy_tpl_contact'])) {
        $output['privacy_tpl_contact'] = $input['privacy_tpl_contact'];
    }

    if (isset($input['logo_type'])) {
        $output['logo_type'] = $input['logo_type'];
    }
    if (isset($input['logo_text'])) {
        $output['logo_text'] = $input['logo_text'];
    }
    if (isset($input['logo_color'])) {
        $output['logo_color'] = $input['logo_color'];
    }
    if (isset($input['enable_social_buttons'])) {
        $output['enable_social_buttons'] = $input['enable_social_buttons'];
    }
    if (isset($input['enable_floating_icons'])) {
        $output['enable_floating_icons'] = intval($input['enable_floating_icons']);
    }
    if (isset($input['enable_twitter_button'])) {
        $output['enable_twitter_button'] = $input['enable_twitter_button'];
    }
    if (isset($input['social_twitter_username'])) {
        $output['social_twitter_username'] = $input['social_twitter_username'];
    }
    if (isset($input['enable_facebook_button'])) {
        $output['enable_facebook_button'] = $input['enable_facebook_button'];
    }
    if (isset($input['enable_google_button'])) {
        $output['enable_google_button'] = $input['enable_google_button'];
    }
    if (isset($input['enable_linkedin_button'])) {
        $output['enable_linkedin_button'] = $input['enable_linkedin_button'];
    }
    if (isset($input['enable_pinterest_button'])) {
        $output['enable_pinterest_button'] = $input['enable_pinterest_button'];
    }
    if (isset($input['social_display_location'])) {
        $output['social_display_location'] = $input['social_display_location'];
    }
    if (isset($input['social_attention_grabber'])) {
        $output['social_attention_grabber'] = $input['social_attention_grabber'];
    }
    if (isset($input['social_cta_text'])) {
        $output['social_cta_text'] = $input['social_cta_text'];
    }
    if (isset($input['social_site_name'])) {
        $output['social_site_name'] = $input['social_site_name'];
    }
    if (isset($input['social_site_title'])) {
        $output['social_site_title'] = $input['social_site_title'];
    }
    if (isset($input['social_site_description'])) {
        $output['social_site_description'] = $input['social_site_description'];
    }
    if (isset($input['social_site_image'])) {
        $output['social_site_image'] = $input['social_site_image'];
    }
    if (isset($input['social_site_twitter_username'])) {
        $output['social_site_twitter_username'] = $input['social_site_twitter_username'];
    }
    if (isset($input['social_add_like_btn'])) {
        $output['social_add_like_btn'] = $input['social_add_like_btn'];
    }
    if (isset($input['other_show_excerpt'])) {
        $output['other_show_excerpt'] = $input['other_show_excerpt'];
    }
    if (isset($input['logo_position'])) {
        $output['logo_position'] = $input['logo_position'];
    }
    if (isset($input['navigation_type'])) {
        $output['navigation_type'] = $input['navigation_type'];
    }
    if (isset($input['social_custom_posts'])) {
        $output['social_custom_posts'] = $input['social_custom_posts'];
    }
    if (isset($input['header_phone_no'])) {
        $output['header_phone_no'] = $input['header_phone_no'];
    }
    if (isset($input['header_phone_text'])) {
        $output['header_phone_text'] = $input['header_phone_text'];
    }
    if (isset($input['header_phone'])) {
        $output['header_phone'] = $input['header_phone'];
    }
    if (isset($input['header_phone_text_mobile'])) {
        $output['header_phone_text_mobile'] = $input['header_phone_text_mobile'];
    }
    if (isset($input['header_phone_btn_color'])) {
        $output['header_phone_btn_color'] = $input['header_phone_btn_color'];
    }
    if (isset($input['404_custom_text'])) {
        $output['404_custom_text'] = $input['404_custom_text'];
    }
    if (isset($input['404_display_sitemap'])) {
        $output['404_display_sitemap'] = $input['404_display_sitemap'];
    }
    if (isset($input['related_no_text'])) {
        $output['related_no_text'] = $input['related_no_text'];
    }
    if (isset($input['related_ignore_cats'])) {
        $output['related_ignore_cats'] = $input['related_ignore_cats'];
    }
    if (isset($input['related_ignore_tags'])) {
        $output['related_ignore_tags'] = $input['related_ignore_tags'];
    }
    if (isset($input['related_number_posts'])) {
        $output['related_number_posts'] = $input['related_number_posts'];
    }
    if (isset($input['related_posts_enabled'])) {
        $output['related_posts_enabled'] = $input['related_posts_enabled'];
    }

    //apprentice stuff
    if (isset($input['appr_enable_feature'])) {
        $output['appr_enable_feature'] = $input['appr_enable_feature'];
    }
    if (isset($input['appr_different_logo'])) {
        $output['appr_different_logo'] = $input['appr_different_logo'];
    }
    if (isset($input['appr_logo'])) {
        $output['appr_logo'] = $input['appr_logo'];
    }
    if (isset($input['appr_logo_type'])) {
        $output['appr_logo_type'] = $input['appr_logo_type'];
    }
    if (isset($input['appr_logo_text'])) {
        $output['appr_logo_text'] = $input['appr_logo_text'];
    }
    if (isset($input['appr_logo_color'])) {
        $output['appr_logo_color'] = $input['appr_logo_color'];
    }
    if (isset($input['appr_logo_position'])) {
        $output['appr_logo_position'] = $input['appr_logo_position'];
    }
    if (isset($input['appr_breadcrumbs'])) {
        $output['appr_breadcrumbs'] = $input['appr_breadcrumbs'];
    }
    if (isset($input['appr_root_page'])) {
        $output['appr_root_page'] = $input['appr_root_page'];
    }
    if (isset($input['appr_root_page'])) {
        $output['appr_root_page'] = $input['appr_root_page'];
    }
    if (isset($input['appr_sidebar'])) {
        $output['appr_sidebar'] = $input['appr_sidebar'];
    }
    if (isset($input['appr_page_comments'])) {
        $output['appr_page_comments'] = $input['appr_page_comments'];
    }
    if (isset($input['appr_prev_next_link'])) {
        $output['appr_prev_next_link'] = $input['appr_prev_next_link'];
    }
    if (isset($input['appr_media_bg_color'])) {
        $output['appr_media_bg_color'] = $input['appr_media_bg_color'];
    }
    if (isset($input['appr_favorites'])) {
        $output['appr_favorites'] = $input['appr_favorites'];
    }
    if (isset($input['appr_progress_track'])) {
        $output['appr_progress_track'] = $input['appr_progress_track'];
    }
    if (isset($input['appr_completed_text'])) {
        $output['appr_completed_text'] = $input['appr_completed_text'];
    }
    if (isset($input['appr_download_heading'])) {
        $output['appr_download_heading'] = $input['appr_download_heading'];
    }
    if (isset($input['appr_replace_lesson'])) {
        $output['appr_replace_lesson'] = $input['appr_replace_lesson'];
    }
    if (isset($input['appr_url_pages'])) {
        $output['appr_url_pages'] = $input['appr_url_pages'];
    }
    if (isset($input['appr_url_lessons'])) {
        $output['appr_url_lessons'] = $input['appr_url_lessons'];
    }
    if (isset($input['appr_url_categories'])) {
        $output['appr_url_categories'] = $input['appr_url_categories'];
    }
    if (isset($input['appr_url_tags'])) {
        $output['appr_url_tags'] = $input['appr_url_tags'];
    }
    if (isset($input['appr_meta_author_name'])) {
        $output['appr_meta_author_name'] = $input['appr_meta_author_name'];
    }
    if (isset($input['appr_meta_post_date'])) {
        $output['appr_meta_post_date'] = $input['appr_meta_post_date'];
    }
    if (isset($input['appr_meta_post_category'])) {
        $output['appr_meta_post_category'] = $input['appr_meta_post_category'];
    }
    if (isset($input['appr_meta_post_tags'])) {
        $output['appr_meta_post_tags'] = $input['appr_meta_post_tags'];
    }
    if (isset($input['appr_meta_comment_count'])) {
        $output['appr_meta_comment_count'] = $input['appr_meta_comment_count'];
    }
    if (isset($input['appr_bottom_about_author'])) {
        $output['appr_bottom_about_author'] = $input['appr_bottom_about_author'];
    }
    if (isset($input['appr_bottom_previous_next'])) {
        $output['appr_bottom_previous_next'] = $input['appr_bottom_previous_next'];
    }
    if (isset($input['blog_post_layout'])) {
        $output['blog_post_layout'] = $input['blog_post_layout'];
    }
    if (isset($input['social_site_meta_enable'])) {
        $output['social_site_meta_enable'] = $input['social_site_meta_enable'];
    }
    // Trigger the flush of the rewrite rules if anything changed in the urls of the posts
    if (isset($input['appr_url_pages']) && isset($input['appr_url_lessons']) && isset($input['appr_enable_feature'])) {
        if (($current_options['appr_enable_feature'] == 0 && $input['appr_enable_feature'] == 1) ||
            ($current_options['appr_url_lessons'] != $input['appr_url_lessons']) ||
            ($current_options['appr_url_pages'] != $input['appr_url_pages']) ||
            ($current_options['appr_url_categories'] != $input['appr_url_categories']) ||
            ($current_options['appr_url_tags'] != $input['appr_url_tags'])
        ) {
            update_option("thrive-flush-rewrite-required", 1);
        }
    }

    return apply_filters('thrive_theme_options_validate', $output, $input, $defaults);
}

/*
 * Delete an image from the database and from the server
 * @param string $image_url The url of the image
 */

function delete_image($image_url)
{
    global $wpdb;

    // We need to get the image's meta ID.
    $query = "SELECT ID FROM wp_posts where guid = '" . esc_url($image_url) . "' AND post_type = 'attachment'";
    $results = $wpdb->get_results($query);

    // And delete it
    foreach ($results as $row) {
        wp_delete_attachment($row->ID);
    }
}

/*
 * Get the color scheme array
 */

function _thrive_get_color_scheme_options($section = "theme")
{
    $colors = array();
    switch ($section) {
        case 'theme':
            return array('blue' => __('Blue', 'thrive'),
                'brown' => __('Brown', 'thrive'),
                'dark' => __('Dark', 'thrive'),
                'liliac' => __('Liliac', 'thrive'),
                'navy' => __('Navy', 'thrive'),
                'purple' => __('Purple', 'thrive'),
                'red' => __('Red', 'thrive'),
                'yellow' => __('Yellow', 'thrive'),
                'pink' => __('Pink', 'thrive'));
            break;
        case 'optin':
            return array('blue' => __('Blue', 'thrive'),
                'dark' => __('Dark', 'thrive'),
                'green' => __('Green', 'thrive'),
                'light' => __('Light', 'thrive'),
                'purple' => __('Purple', 'thrive'),
                'red' => __('Red', 'thrive'),
                'teal' => __('Teal', 'thrive'),
                'orange' => __('Orange', 'thrive'));
            break;
        case 'focusareas':
            return array('blue' => __('Blue', 'thrive'),
                'dark' => __('Dark', 'thrive'),
                'light' => __('Light', 'thrive'),
                'green' => __('Green', 'thrive'),
                'red' => __('Red', 'thrive'),
                'purple' => __('Purple', 'thrive'),
                'orange' => __('Orange', 'thrive'));
            break;
        case 'ribbon_focus_area':
            return array('blue' => __('Blue', 'thrive'),
                'green' => __('Green', 'thrive'),
                'red' => __('Red', 'thrive'),
                'purple' => __('Purple', 'thrive'),
                'teal' => __('Teal', 'thrive'),
                'orange' => __('Orange', 'thrive'));
            break;
        case 'contentbox':
            return array('blue' => __('Blue', 'thrive'),
                'dark' => __('Dark', 'thrive'),
                'green' => __('Green', 'thrive'),
                'light' => __('Light', 'thrive'),
                'note' => __('Note', 'thrive'),
                'purple' => __('Purple', 'thrive'),
                'red' => __('Red', 'thrive'),
                'teal' => __('Teal', 'thrive'),
                'orange' => __('Orange', 'thrive'));
            break;
        case 'buttons':
            return array('blue' => __('Blue', 'thrive'),
                'green' => __('Green', 'thrive'),
                'red' => __('Red', 'thrive'),
                'purple' => __('Purple', 'thrive'),
                'orange' => __('Orange', 'thrive'),
                'teal' => __('Teal', 'thrive'),
                'light' => __('Light', 'thrive'),
                'dark' => __('Dark', 'thrive'));
            break;
        default:
            return array('blue' => __('Blue', 'thrive'),
                'brown' => __('Brown', 'thrive'),
                'dark' => __('Dark', 'thrive'),
                'liliac' => __('Liliac', 'thrive'),
                'navy' => __('Navy', 'thrive'),
                'purple' => __('Purple', 'thrive'),
                'red' => __('Red', 'thrive'),
                'yellow' => __('Yellow', 'thrive'),
                'pink' => __('Pink', 'thrive'));
            break;
    }
}

function thrive_settings_field_logo($options)
{
    echo "<input class='thrive_options' type='text' name='thrive_theme_options[logo]' id='thrive_theme_options_logo' value='" . esc_attr($options['logo']) . "' /><br/>";
    echo "<input class='thrive_options pure-button upload' type='button' id='thrive_theme_options_logo_btn' value=' " . __("Upload", 'thrive') . " ' />";
    echo "<input class='thrive_options pure-button clear-field remove' type='button' id='thrieve_theme_btn_delete_logo' value=' " . __("Remove", 'thrive') . " ' />";
}

function thrive_settings_field_favicon($options)
{
    echo "<input class='thrive_options' type='text' name='thrive_theme_options[favicon]' id='thrive_theme_options_favicon' value='" . esc_attr($options['favicon']) . "' /><br/>";
    echo "<input type='button' class='thrive_options pure-button upload' id='thrive_theme_options_favicon_btn' value=' " . __("Upload", 'thrive') . " ' />";
    echo "<input type='button' class='thrive_options pure-button clear-field remove' id='thrieve_theme_btn_delete_favicon' value=' " . __("Remove", 'thrive') . " ' />";
}

function thrive_settings_field_footer_copyright($options)
{
    echo "<textarea class='thrive_options' id='thrive_theme_options_footer_copyright' name='thrive_theme_options[footer_copyright]'>" . esc_attr($options['footer_copyright']) . "</textarea>";
}

function thrive_settings_field_footer_copyright_links($options)
{
    $checked_on = ($options['footer_copyright_links'] == 1) ? "checked" : "";
    $checked_off = ($options['footer_copyright_links'] == 0) ? "checked" : "";
    echo "<input class='toggle toggle-left' type='radio' id='footer_copyright_links_true' name='thrive_theme_options[footer_copyright_links]' value='1' $checked_on />
    <label for='footer_copyright_links_true' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' type='radio' id='footer_copyright_links_false' name='thrive_theme_options[footer_copyright_links]' value='0' $checked_off />
    <label for='footer_copyright_links_false' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_display_breadcrumbs($options)
{
    $checked_on = ($options['display_breadcrumbs'] == 1) ? "checked" : "";
    $checked_off = ($options['display_breadcrumbs'] == 0) ? "checked" : "";
    echo "<input class='toggle toggle-left' type='radio' id='display_breadcrumb_true' name='thrive_theme_options[display_breadcrumbs]' value='1' $checked_on />
    <label for='display_breadcrumb_true' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' type='radio' id='display_breadcrumb_false' name='thrive_theme_options[display_breadcrumbs]' value='0' $checked_off />
    <label for='display_breadcrumb_false' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_comments_on_pages($options)
{
    $checked_on = ($options['comments_on_pages'] == 1) ? "checked" : "";
    $checked_off = ($options['comments_on_pages'] == 0) ? "checked" : "";
    echo "<input class='toggle toggle-left' type='radio' id='comment_true' name='thrive_theme_options[comments_on_pages]' value='1' $checked_on />
    <label for='comment_true' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' type='radio' id='comment_false' name='thrive_theme_options[comments_on_pages]' value='0' $checked_off />
    <label for='comment_false' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_relative_time($options)
{
    if (!isset($options['relative_time'])) {
        $options['relative_time'] = 0;
    }
    $checked_on = ($options['relative_time'] == 1) ? "checked" : "";
    $checked_off = ($options['relative_time'] == 0) ? "checked" : "";
    echo "<input class='toggle toggle-left' type='radio' id='relative_time_true' name='thrive_theme_options[relative_time]' value='1' $checked_on />
    <label for='relative_time_true' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' type='radio' id='relative_time_false' name='thrive_theme_options[relative_time]' value='0' $checked_off />
    <label for='relative_time_false' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_highlight_author_comments($options)
{
    if (!isset($options['highlight_author_comments'])) {
        $options['highlight_author_comments'] = 1;
    }
    $checked_on = ($options['highlight_author_comments'] == 1) ? "checked" : "";
    $checked_off = ($options['highlight_author_comments'] == 0) ? "checked" : "";
    echo "<input class='toggle toggle-left' type='radio' id='highlight_author_comments_true' name='thrive_theme_options[highlight_author_comments]' value='1' $checked_on />
    <label for='highlight_author_comments_true' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' type='radio' id='highlight_author_comments_false' name='thrive_theme_options[highlight_author_comments]' value='0' $checked_off />
    <label for='highlight_author_comments_false' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_color_scheme($options)
{
    $colors = _thrive_get_color_scheme_options();
    echo "<select name='thrive_theme_options[color_scheme]' id='thrive_theme_options_color_scheme'>";
    foreach ($colors as $key => $c) {
        $selected = ($options['color_scheme'] == $key) ? "selected" : "";
        echo "<option value='" . $key . "' " . $selected . ">" . $c . "</option>";
    }
    echo "</select>";
}

function thrive_settings_field_sidebar_alignement($options)
{
    $checked_left = ($options['sidebar_alignement'] == 'left') ? "checked" : "";
    $checked_right = ($options['sidebar_alignement'] == 'right') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='sidebar_left' type='radio' name='thrive_theme_options[sidebar_alignement]' value='left' $checked_left />
    <label for='sidebar_left' class='btn'>" . __('Left', 'thrive') . "</label>";
    echo "<input class='toggle green-toggle' id='sidebar_right' type='radio' name='thrive_theme_options[sidebar_alignement]' value='right' $checked_right />
    <label for='sidebar_right' class='btn'>" . __('Right', 'thrive') . "</label>";
}

function thrive_settings_field_extended_menu($options)
{
    $checked_on = ($options['extended_menu'] == 'on') ? "checked" : "";
    $checked_off = ($options['extended_menu'] == 'off') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='extended_menu_on' type='radio' name='thrive_theme_options[extended_menu]' value='on' $checked_on />
    <label for='extended_menu_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='extended_menu_off' type='radio' name='thrive_theme_options[extended_menu]' value='off' $checked_off />
    <label for='extended_menu_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_custom_css($options)
{
    echo "<textarea id='thrive_theme_options_custom_css' name='thrive_theme_options[custom_css]'>" . esc_attr($options['custom_css']) . "</textarea>";
}

function thrive_settings_field_header_navigation($options)
{
    if (!isset($options['header_navigation'])) {
        $options['header_navigation'] = 65;
    }
    echo "<div class='thrive_option_header_nav_slider'></div><label class='thrive_label_header_nav_value'>" . $options['header_navigation'] . "</label>";
    echo "<input type='hidden' name='thrive_theme_options[header_navigation]' id='thrive_theme_option_hidden_nav_header' value='" . $options['header_navigation'] . "' /> ";
}

function thrive_settings_field_featured_image_style($options)
{
    $checked_1 = ($options['featured_image_style'] == 'wide') ? "checked" : "";
    $checked_2 = ($options['featured_image_style'] == 'thumbnail') ? "checked" : "";
    $checked_3 = ($options['featured_image_style'] == 'no_image') ? "checked" : "";
    echo "<input type='radio' name='thrive_theme_options[featured_image_style]' value='wide' $checked_1 /> " . __('Wide', 'thrive') . "";
    echo "<input type='radio' name='thrive_theme_options[featured_image_style]' value='thumbnail' $checked_2 /> " . __('Thumbnail', 'thrive') . "";
    echo "<input type='radio' name='thrive_theme_options[featured_image_style]' value='no_image' $checked_3 /> " . __('No Image', 'thrive') . "";
}

function thrive_settings_field_featured_image_single_post($options)
{
    $checked_on = ($options['featured_image_single_post'] == '1') ? "checked" : "";
    $checked_off = ($options['featured_image_single_post'] == '0') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='featured_image_on' type='radio' name='thrive_theme_options[featured_image_single_post]' value='1' $checked_on />
    <label for='featured_image_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='featured_image_off' type='radio' name='thrive_theme_options[featured_image_single_post]' value='0' $checked_off />
    <label for='featured_image_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_meta_author_name($options)
{
    $options = thrive_get_theme_options();
    $checked_on = ($options['meta_author_name'] == '1') ? "checked" : "";
    $checked_off = ($options['meta_author_name'] == '0') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='author_name_on' type='radio' name='thrive_theme_options[meta_author_name]' value='1' $checked_on />
    <label for='author_name_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='author_name_off' type='radio' name='thrive_theme_options[meta_author_name]' value='0' $checked_off />
    <label for='author_name_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_meta_post_date($options)
{
    $checked_on = ($options['meta_post_date'] == '1') ? "checked" : "";
    $checked_off = ($options['meta_post_date'] == '0') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='post_date_on' type='radio' name='thrive_theme_options[meta_post_date]' value='1' $checked_on />
    <label for='post_date_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='post_date_off' type='radio' name='thrive_theme_options[meta_post_date]' value='0' $checked_off />
    <label for='post_date_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_meta_post_category($options)
{
    $checked_on = ($options['meta_post_category'] == '1') ? "checked" : "";
    $checked_off = ($options['meta_post_category'] == '0') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='post_category_on' type='radio' name='thrive_theme_options[meta_post_category]' value='1' $checked_on />
    <label for='post_category_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='post_category_off' type='radio' name='thrive_theme_options[meta_post_category]' value='0' $checked_off />
    <label for='post_category_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_meta_post_tags($options)
{
    $checked_on = ($options['meta_post_tags'] == '1') ? "checked" : "";
    $checked_off = ($options['meta_post_tags'] == '0') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='post_tags_on' type='radio' name='thrive_theme_options[meta_post_tags]' value='1' $checked_on />
    <label for='post_tags_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='post_tags_off' type='radio' name='thrive_theme_options[meta_post_tags]' value='0' $checked_off />
    <label for='post_tags_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_meta_comment_count($options)
{
    $checked_on = ($options['meta_comment_count'] == '1') ? "checked" : "";
    $checked_off = ($options['meta_comment_count'] == '0') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='comment_count_on' type='radio' name='thrive_theme_options[meta_comment_count]' value='1' $checked_on />
    <label for='comment_count_on' class='btn'>On</label>";
    echo "<input class='toggle toggle-right' id='comment_count_off' type='radio' name='thrive_theme_options[meta_comment_count]' value='0' $checked_off />
    <label for='comment_count_off' class='btn'>Off</label>";
}

function thrive_settings_field_bottom_about_author($options)
{
    $options = thrive_get_theme_options();
    $checked_on = ($options['bottom_about_author'] == '1') ? "checked" : "";
    $checked_off = ($options['bottom_about_author'] == '0') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='about_author_on' type='radio' name='thrive_theme_options[bottom_about_author]' value='1' $checked_on />
    <label for='about_author_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='about_author_off' type='radio' name='thrive_theme_options[bottom_about_author]' value='0' $checked_off />
    <label for='about_author_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_bottom_previous_next($options)
{
    $checked_on = ($options['bottom_previous_next'] == '1') ? "checked" : "";
    $checked_off = ($options['bottom_previous_next'] == '0') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='bottom_prev_next_on' type='radio' name='thrive_theme_options[bottom_previous_next]' value='1' $checked_on />
    <label for='bottom_prev_next_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='bottom_prev_next_off' type='radio' name='thrive_theme_options[bottom_previous_next]' value='0' $checked_off />
    <label for='bottom_prev_next_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_related_posts_box($options)
{
    $checked_on = ($options['related_posts_box'] == '1') ? "checked" : "";
    $checked_off = ($options['related_posts_box'] == '0') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='related_posts_box_on' type='radio' name='thrive_theme_options[related_posts_box]' value='1' $checked_on />
    <label for='related_posts_box_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='related_posts_box_off' type='radio' name='thrive_theme_options[related_posts_box]' value='0' $checked_off />
    <label for='related_posts_box_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_related_posts_title($options)
{
    echo "<input type='text' name='thrive_theme_options[related_posts_title]' id='thrive_theme_options_related_posts_title' value='" . esc_attr($options['related_posts_title']) . "' />";
}

function thrive_settings_field_related_posts_number($options)
{
    echo "<input type='text' name='thrive_theme_options[related_posts_number]' id='thrive_settings_field_related_posts_number' value='" . esc_attr($options['related_posts_number']) . "' />";
}

function thrive_settings_field_related_posts_images($options)
{
    $checked_on = ($options['related_posts_images'] == '1') ? "checked" : "";
    $checked_off = ($options['related_posts_images'] == '0') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='related_posts_images_on' type='radio' name='thrive_theme_options[related_posts_images]' value='1' $checked_on />
    <label for='related_posts_images_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='related_posts_images_off' type='radio' name='thrive_theme_options[related_posts_images]' value='0' $checked_off />
    <label for='related_posts_images_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_other_read_more_type($options)
{
    $checked_button = ($options['other_read_more_type'] == 'button') ? "checked" : "";
    $checked_text = ($options['other_read_more_type'] == 'text') ? "checked" : "";
    echo "<input type='radio' name='thrive_theme_options[other_read_more_type]' value='button' $checked_button /> " . __('Button', 'thrive') . "";
    echo "<input type='radio' name='thrive_theme_options[other_read_more_type]' value='text' $checked_text /> " . __('Text', 'thrive') . "";
}

function thrive_settings_field_other_read_more_text($options)
{
    echo "<input type='text' name='thrive_theme_options[other_read_more_text]' id='thrive_theme_options_other_read_more_text' value='" . esc_attr($options['other_read_more_text']) . "' />";
}

function thrive_settings_field_social_gplus($options)
{
    $options['social_gplus'] = (isset($options['social_gplus'])) ? $options['social_gplus'] : "";
    echo "<input type='text' name='thrive_theme_options[social_gplus]' id='thrive_theme_options_social_gplus' value='" . esc_attr($options['social_gplus']) . "' />";
}

function thrive_settings_field_social_pinterest($options)
{
    $options['social_pinterest'] = (isset($options['social_pinterest'])) ? $options['social_pinterest'] : "";
    echo "<input type='text' name='thrive_theme_options[social_pinterest]' id='thrive_theme_options_social_pinterest' value='" . esc_attr($options['social_pinterest']) . "' />";
}

function thrive_settings_field_social_linkedin($options)
{
    $options['social_linkedin'] = (isset($options['social_linkedin'])) ? $options['social_linkedin'] : "";
    echo "<input type='text' name='thrive_theme_options[social_linkedin]' id='thrive_theme_options_social_linkedin' value='" . esc_attr($options['social_linkedin']) . "' />";
}

function thrive_settings_field_social_youtube($options)
{
    $options['social_youtube'] = (isset($options['social_youtube'])) ? $options['social_youtube'] : "";
    echo "<input type='text' name='thrive_theme_options[social_youtube]' id='thrive_theme_options_social_youtube' value='" . esc_attr($options['social_youtube']) . "' />";
}

function thrive_settings_field_social_twitter($options)
{
    $options['social_twitter'] = (isset($options['social_twitter'])) ? $options['social_twitter'] : "";
    echo "<input type='text' name='thrive_theme_options[social_twitter]' id='thrive_theme_options_social_twitter' value='" . esc_attr($options['social_twitter']) . "' />";
}

function thrive_settings_field_social_facebook($options)
{
    $options['social_facebook'] = (isset($options['social_facebook'])) ? $options['social_facebook'] : "";
    echo "<input type='text' name='thrive_theme_options[social_facebook]' id='thrive_theme_options_social_facebook' value='" . esc_attr($options['social_facebook']) . "' />";
}

function thrive_settings_field_hide_cats_from_blog($options)
{
    $value_hide_cats = json_decode($options['hide_cats_from_blog']);
    if (!is_array($value_hide_cats)) {
        $value_hide_cats = array();
    }
    $all_categories = get_categories();
    $categories_array = array();

    foreach ($all_categories as $cat) {
        array_push($categories_array, array('id' => $cat->cat_ID, 'name' => $cat->cat_name));
    }
    ?>
    <select id="thrive_theme_hide_cats_from_blog_sel" name="thrive_theme_hide_cats_from_blog_sel" style="width: 220px;" multiple>
        <?php foreach ($categories_array as $cat): ?>
            <option value="<?php echo $cat['id']; ?>" <?php if (in_array($cat['id'], $value_hide_cats)): ?>selected<?php endif; ?>><?php echo $cat['name']; ?></option>
        <?php endforeach; ?>
    </select>
    <input type="hidden" name="thrive_theme_options[hide_cats_from_blog]" id="thrive_theme_hide_cats_from_blog_hidden" value=""/>
<?php
}

function thrive_settings_field_analytics_header_script($options)
{
    echo "<textarea id='thrive_theme_analytics_header_script' name='thrive_theme_options[analytics_header_script]'>" . esc_attr($options['analytics_header_script']) . "</textarea>";
}

function thrive_settings_field_analytics_body_script($options)
{
    echo "<textarea id='thrive_theme_options_analytics_body_script' name='thrive_theme_options[analytics_body_script]'>" . esc_attr($options['analytics_body_script']) . "</textarea>";
}

function thrive_settings_field_analytics_body_script_top($options)
{
    echo "<textarea id='thrive_theme_options_analytics_body_script_top' name='thrive_theme_options[analytics_body_script_top]'>" . esc_attr($options['analytics_body_script_top']) . "</textarea>";
}

function thrive_settings_field_other_show_comment_date($options)
{
    $checked_on = ($options['other_show_comment_date'] == '1') ? "checked" : "";
    $checked_off = ($options['other_show_comment_date'] == '0') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='show_comment_date_on' type='radio' name='thrive_theme_options[other_show_comment_date]' value='1' $checked_on />
    <label for='show_comment_date_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='show_comment_date_off' type='radio' name='thrive_theme_options[other_show_comment_date]' value='0' $checked_off />
    <label for='show_comment_date_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_image_optimization_type($options)
{
    if (!isset($options['image_optimization_type'])) {
        $options['image_optimization_type'] = 'lossy';
    }
    $checked_1 = ($options['image_optimization_type'] == 'off') ? "checked" : "";
    $checked_2 = ($options['image_optimization_type'] == 'lossy') ? "checked" : "";
    $checked_3 = ($options['image_optimization_type'] == 'lossless') ? "checked" : "";
    echo "<input type='radio' name='thrive_theme_options[image_optimization_type]' value='off' $checked_1 /> " . __('No Compression', 'thrive') . "";
    echo "<input type='radio' name='thrive_theme_options[image_optimization_type]' value='lossy' $checked_2 /> " . __('Lossy Compression', 'thrive') . "";
    echo "<input type='radio' name='thrive_theme_options[image_optimization_type]' value='lossless' $checked_3 /> " . __('Lossless Image', 'thrive') . "";
}

function thrive_settings_field_client_logos($options)
{
    $options['client_logos'] = (isset($options['client_logos'])) ? $options['client_logos'] : "";
    echo "<textarea name='thrive_theme_options[client_logos]' id='thrive_theme_options_client_logos'>" . $options['client_logos'] . "</textarea><br/>";
    echo "<input class='thrive_options pure-button upload' type='button' id='thrieve_theme_btn_client_logos' value=' " . __("Select photos", 'thrive') . " ' />";
}

function thrive_settings_field_comments_lazy($options)
{
    if (!isset($options['comments_lazy'])) {
        $options['comments_lazy'] = 0;
    }
    $checked_on = ($options['comments_lazy'] == '1') ? "checked" : "";
    $checked_off = ($options['comments_lazy'] == '0') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='comments_lazy_on' type='radio' name='thrive_theme_options[comments_lazy]' value='1' $checked_on />
    <label for='comments_lazy_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='comments_lazy_off' type='radio' name='thrive_theme_options[comments_lazy]' value='0' $checked_off />
    <label for='comments_lazy_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_enable_fb_comments($options)
{
    if (!isset($options['enable_fb_comments'])) {
        $options['enable_fb_comments'] = "off";
    }
    ?>
    <select name="thrive_theme_options[enable_fb_comments]" id="thrive_theme_options_enable_fb_comments">
        <option value="off"><?php _e("Off", 'thrive'); ?></option>
        <option value="only_fb" <?php if ($options['enable_fb_comments'] == "only_fb"): ?>selected<?php endif; ?>><?php _e("Only facebook comments", 'thrive'); ?></option>
        <option value="both_fb_regular" <?php if ($options['enable_fb_comments'] == "both_fb_regular"): ?>selected<?php endif; ?>><?php _e("Both facebook and regular comments", 'thrive'); ?></option>
        <option value="fb_when_disabled" <?php if ($options['enable_fb_comments'] == "fb_when_disabled"): ?>selected<?php endif; ?>><?php _e("Facebook comments when regular are disabled", 'thrive'); ?></option>
    </select>
<?php
}

function thrive_settings_field_fb_app_id($options)
{
    if (!isset($options['fb_app_id'])) {
        $options['fb_app_id'] = "";
    }
    echo "<input type='text' name='thrive_theme_options[fb_app_id]' id='thrive_theme_options_fb_app_id' value='" . esc_attr($options['fb_app_id']) . "' />";
}

function thrive_settings_field_fb_no_comments($options)
{
    if (!isset($options['fb_no_comments'])) {
        $options['fb_no_comments'] = 5;
    }
    echo "<input type='text' name='thrive_theme_options[fb_no_comments]' id='thrive_theme_options_fb_no_comments' value='" . esc_attr($options['fb_no_comments']) . "' />";
}

function thrive_settings_field_fb_color_scheme($options)
{
    if (!isset($options['fb_color_scheme'])) {
        $options['fb_color_scheme'] = 'light';
    }
    $selectedDark = ($options['fb_color_scheme'] == "dark") ? " selected" : "";
    echo "<select name='thrive_theme_options[fb_color_scheme]' id='thrive_theme_options_fb_color_scheme'>";
    echo "<option value='light'>" . __("Light", 'thrive') . "</option>";
    echo "<option value='dark'" . $selectedDark . ">" . __("Dark", 'thrive') . "</option></select>";
}

function thrive_settings_field_privacy_tpl_website($options)
{
    if (!isset($options['privacy_tpl_website'])) {
        $options['privacy_tpl_website'] = "";
    }
    echo "<input type='text' name='thrive_theme_options[privacy_tpl_website]' id='thrive_theme_options_privacy_tpl_website' value='" . esc_attr($options['privacy_tpl_website']) . "' />";
}

function thrive_settings_field_privacy_tpl_contact($options)
{
    if (!isset($options['privacy_tpl_contact'])) {
        $options['privacy_tpl_contact'] = "";
    }
    echo "<input type='text' name='thrive_theme_options[privacy_tpl_contact]' id='thrive_theme_options_privacy_tpl_contact' value='" . esc_attr($options['privacy_tpl_contact']) . "' />";
}

function thrive_settings_field_privacy_tpl_address($options)
{
    if (!isset($options['privacy_tpl_address'])) {
        $options['privacy_tpl_address'] = "";
    }
    echo "<textarea id='thrive_theme_options_privacy_tpl_address' name='thrive_theme_options[privacy_tpl_address]'>" . esc_attr($options['privacy_tpl_address']) . "</textarea>";
}

function thrive_settings_field_privacy_tpl_company($options)
{
    if (!isset($options['privacy_tpl_company'])) {
        $options['privacy_tpl_company'] = "";
    }
    echo "<input type='text' name='thrive_theme_options[privacy_tpl_company]' id='thrive_theme_options_privacy_tpl_company' value='" . esc_attr($options['privacy_tpl_company']) . "' />";
}

function thrive_settings_field_logo_type($options)
{
    if (!isset($options['logo_type'])) {
        $options['logo_type'] = "image";
    }
    $checked_image = ($options['logo_type'] != 'text') ? "checked" : "";
    $checked_text = ($options['logo_type'] == 'text') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='logo_type_image' type='radio' name='thrive_theme_options[logo_type]' value='image' $checked_image />
    <label for='logo_type_image' class='btn'>" . __('Image', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='logo_type_text' type='radio' name='thrive_theme_options[logo_type]' value='text' $checked_text />
    <label for='logo_type_text' class='btn'>" . __('Text', 'thrive') . "</label>";
}

function thrive_settings_field_logo_text($options)
{
    if (!isset($options['logo_text'])) {
        $options['logo_text'] = "";
    }
    echo "<input type='text' name='thrive_theme_options[logo_text]' id='thrive_theme_options_logo_text' value='" . esc_attr($options['logo_text']) . "' />";
}

function thrive_settings_field_logo_color($options)
{
    if (!isset($options['logo_color'])) {
        $options['logo_color'] = "default";
    }
    $colors = _thrive_get_color_scheme_options();
    $colors = array_merge(array('default' => __("Default", 'thrive')), $colors);
    echo "<select name='thrive_theme_options[logo_color]' id='thrive_theme_options_logo_color'>";
    foreach ($colors as $key => $c) {
        $is_selected = ($key == $options['logo_color']) ? " selected" : "";
        echo "<option value='" . $key . "'" . $is_selected . ">" . $c . "</option>";
    }
    echo "</select>";
}

function thrive_settings_field_enable_social_buttons($options)
{
    if (!isset($options['enable_social_buttons'])) {
        $options['enable_social_buttons'] = 0;
    }
    $checked_enabled = ($options['enable_social_buttons'] == 1) ? "checked" : "";
    $checked_disabled = ($options['enable_social_buttons'] != 1) ? "checked" : "";

    echo "<input class='toggle toggle-left' id='enable_social_buttons_on' type='radio' name='thrive_theme_options[enable_social_buttons]' value='1' $checked_enabled />
    <label for='enable_social_buttons_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='enable_social_buttons_off' type='radio' name='thrive_theme_options[enable_social_buttons]' value='0' $checked_disabled />
    <label for='enable_social_buttons_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_enable_floating_icons($options)
{
    if (!isset($options['enable_floating_icons'])) {
        $options['enable_floating_icons'] = 1;
    }
    $checked_enabled = ($options['enable_floating_icons'] == 1) ? "checked" : "";
    $checked_disabled = ($options['enable_floating_icons'] == 0) ? "checked" : "";

    echo "<input class='toggle toggle-left' id='enable_floating_icons_on' type='radio' name='thrive_theme_options[enable_floating_icons]' value='1' $checked_enabled />
    <label for='enable_floating_icons_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='enable_floating_icons_off' type='radio' name='thrive_theme_options[enable_floating_icons]' value='0' $checked_disabled />
    <label for='enable_floating_icons_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_enable_twitter_button($options)
{
    if (!isset($options['enable_twitter_button'])) {
        $options['enable_twitter_button'] = 0;
    }
    $checked_enabled = ($options['enable_twitter_button'] == 1) ? "checked" : "";
    $checked_disabled = ($options['enable_twitter_button'] != 1) ? "checked" : "";

    echo "<input class='toggle toggle-left' id='enable_twitter_button_on' type='radio' name='thrive_theme_options[enable_twitter_button]' value='1' $checked_enabled />
    <label for='enable_twitter_button_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='enable_twitter_button_off' type='radio' name='thrive_theme_options[enable_twitter_button]' value='0' $checked_disabled />
    <label for='enable_twitter_button_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_social_twitter_username($options)
{
    if (!isset($options['social_twitter_username'])) {
        $options['social_twitter_username'] = "";
    }
    echo "<input type='text' name='thrive_theme_options[social_twitter_username]' id='thrive_theme_options_social_twitter_username' value='" . esc_attr($options['social_twitter_username']) . "' />";
}

function thrive_settings_field_enable_facebook_button($options)
{
    if (!isset($options['enable_facebook_button'])) {
        $options['enable_facebook_button'] = 0;
    }
    $checked_enabled = ($options['enable_facebook_button'] == 1) ? "checked" : "";
    $checked_disabled = ($options['enable_facebook_button'] != 1) ? "checked" : "";

    echo "<input class='toggle toggle-left' id='enable_facebook_button_on' type='radio' name='thrive_theme_options[enable_facebook_button]' value='1' $checked_enabled />
    <label for='enable_facebook_button_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='enable_facebook_button_off' type='radio' name='thrive_theme_options[enable_facebook_button]' value='0' $checked_disabled />
    <label for='enable_facebook_button_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_enable_google_button($options)
{
    if (!isset($options['enable_google_button'])) {
        $options['enable_google_button'] = 0;
    }
    $checked_enabled = ($options['enable_google_button'] == 1) ? "checked" : "";
    $checked_disabled = ($options['enable_google_button'] != 1) ? "checked" : "";

    echo "<input class='toggle toggle-left' id='enable_google_button_on' type='radio' name='thrive_theme_options[enable_google_button]' value='1' $checked_enabled />
    <label for='enable_google_button_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='enable_google_button_off' type='radio' name='thrive_theme_options[enable_google_button]' value='0' $checked_disabled />
    <label for='enable_google_button_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_enable_linkedin_button($options)
{
    if (!isset($options['enable_linkedin_button'])) {
        $options['enable_linkedin_button'] = 0;
    }
    $checked_enabled = ($options['enable_linkedin_button'] == 1) ? "checked" : "";
    $checked_disabled = ($options['enable_linkedin_button'] != 1) ? "checked" : "";

    echo "<input class='toggle toggle-left' id='enable_linkedin_button_on' type='radio' name='thrive_theme_options[enable_linkedin_button]' value='1' $checked_enabled />
    <label for='enable_linkedin_button_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='enable_linkedin_button_off' type='radio' name='thrive_theme_options[enable_linkedin_button]' value='0' $checked_disabled />
    <label for='enable_linkedin_button_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_enable_pinterest_button($options)
{
    if (!isset($options['enable_pinterest_button'])) {
        $options['enable_pinterest_button'] = 0;
    }
    $checked_enabled = ($options['enable_pinterest_button'] == 1) ? "checked" : "";
    $checked_disabled = ($options['enable_pinterest_button'] != 1) ? "checked" : "";

    echo "<input class='toggle toggle-left' id='enable_pinterest_button_on' type='radio' name='thrive_theme_options[enable_pinterest_button]' value='1' $checked_enabled />
    <label for='enable_pinterest_button_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='enable_pinterest_button_off' type='radio' name='thrive_theme_options[enable_pinterest_button]' value='0' $checked_disabled />
    <label for='enable_pinterest_button_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_social_display_location($options)
{
    if (!isset($options['social_display_location'])) {
        $options['social_display_location'] = "posts";
    }
    $checked_posts = (strpos($options['social_display_location'], "posts") !== false) ? " checked" : "";
    $checked_pages = (strpos($options['social_display_location'], "pages") !== false) ? " checked" : "";
    $value_hidden = "";
    if ($checked_posts != "") {
        $value_hidden .= "posts,";
    }
    if ($checked_pages != "") {
        $value_hidden .= "pages,";
    }

    echo "<input $checked_posts type='checkbox' id='thrive_theme_options_social_display_posts' />" . __("Posts", 'thrive') . "<br/>";
    echo "<input $checked_pages type='checkbox' id='thrive_theme_options_social_display_pages' />" . __("Pages", 'thrive');
    echo "<input name='thrive_theme_options[social_display_location]' type='hidden' id='thrive_theme_options_social_display_hidden' value='" . $value_hidden . "'/>";
}

function thrive_settings_field_social_attention_grabber($options)
{
    if (!isset($options['social_attention_grabber'])) {
        $options['social_attention_grabber'] = "none";
    }
    $checked_cta = ($options['social_attention_grabber'] == "cta") ? " checked" : "";
    $checked_count = ($options['social_attention_grabber'] == "count") ? " checked" : "";
    $checked_none = ($options['social_attention_grabber'] != "cta" && $options['social_attention_grabber'] != "count") ? " checked" : "";

    echo "<input type='radio' class='radio_sel_social_attention_grabber' id='thrive_theme_options_social_attention_cta' name='thrive_theme_options[social_attention_grabber]' value='cta' $checked_cta />" . __("CTA text", 'thrive');
    echo "<input type='radio' class='radio_sel_social_attention_grabber' id='thrive_theme_options_social_attention_count' name='thrive_theme_options[social_attention_grabber]' value='count' $checked_count />" . __("Share count", 'thrive');
    echo "<input type='radio' class='radio_sel_social_attention_grabber' id='thrive_theme_options_social_attention_none' name='thrive_theme_options[social_attention_grabber]' value='none' $checked_none />" . __("None", 'thrive');
}

function thrive_settings_field_social_cta_text($options)
{
    if (!isset($options['social_cta_text'])) {
        $options['social_cta_text'] = "";
    }
    echo "<input type='text' name='thrive_theme_options[social_cta_text]' id='thrive_theme_options_social_cta_text' value='" . esc_attr($options['social_cta_text']) . "' />";
}

function thrive_settings_field_social_site_name($options)
{
    if (!isset($options['social_site_name'])) {
        $options['social_site_name'] = "";
    }
    echo "<input type='text' name='thrive_theme_options[social_site_name]' value='" . esc_attr($options['social_site_name']) . "' />";
}

function thrive_settings_field_social_site_title($options)
{
    if (!isset($options['social_site_title'])) {
        $options['social_site_title'] = "";
    }
    echo "<input type='text' name='thrive_theme_options[social_site_title]' value='" . esc_attr($options['social_site_title']) . "' />";
}

function thrive_settings_field_social_site_description($options)
{
    if (!isset($options['social_site_description'])) {
        $options['social_site_description'] = "";
    }
    echo "<input type='text' name='thrive_theme_options[social_site_description]' value='" . esc_attr($options['social_site_description']) . "' />";
}

function thrive_settings_field_social_site_image($options)
{
    if (!isset($options['social_site_image'])) {
        $options['social_site_image'] = "";
    }
    echo "<input type='text' id='thrive_meta_social_image' name='thrive_theme_options[social_site_image]' value='" . esc_attr($options['social_site_image']) . "' />";
    echo '<input type="button" class="thrive_options pure-button upload" id="thrive_meta_social_button_upload" value="Upload"/>
        <input type="button" class="thrive_options pure-button clear-field remove" id="thrive_meta_social_button_remove" value="Remove"/>';
}

function thrive_settings_field_social_site_twitter_username($options)
{
    if (!isset($options['social_site_twitter_username'])) {
        $options['social_site_twitter_username'] = "";
    }
    echo "<input type='text' name='thrive_theme_options[social_site_twitter_username]' value='" . esc_attr($options['social_site_twitter_username']) . "' />";
}

function thrive_settings_field_social_add_like_btn($options)
{
    if (!isset($options['social_add_like_btn'])) {
        $options['social_add_like_btn'] = 0;
    }
    $checked_enabled = ($options['social_add_like_btn'] == 1) ? "checked" : "";
    $checked_disabled = ($options['social_add_like_btn'] != 1) ? "checked" : "";

    echo "<input class='toggle toggle-left' id='social_add_like_btn_on' type='radio' name='thrive_theme_options[social_add_like_btn]' value='1' $checked_enabled />
    <label for='social_add_like_btn_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='social_add_like_btn_off' type='radio' name='thrive_theme_options[social_add_like_btn]' value='0' $checked_disabled />
    <label for='social_add_like_btn_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_other_show_excerpt($options)
{
    if (!isset($options['other_show_excerpt'])) {
        $options['other_show_excerpt'] = 0;
    }
    $checked_content = ($options['other_show_excerpt'] != 1) ? "checked" : "";
    $checked_excerpt = ($options['other_show_excerpt'] == 1) ? "checked" : "";
    echo "<input type='radio' name='thrive_theme_options[other_show_excerpt]' value='0' $checked_content /> " . __('Post content', 'thrive') . "";
    echo "<input type='radio' name='thrive_theme_options[other_show_excerpt]' value='1' $checked_excerpt /> " . __('Post excerpt', 'thrive') . "";
}

function thrive_settings_field_logo_position($options)
{
    if (!isset($options['logo_position'])) {
        $options['logo_position'] = "side";
    }
    $checked_side = ($options['logo_position'] != 'top') ? "checked" : "";
    $checked_top = ($options['logo_position'] == 'top') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='logo_position_image' type='radio' name='thrive_theme_options[logo_position]' value='side' $checked_side />
    <label for='logo_position_image' class='btn'>" . __('Side of menu', 'thrive') . "</label>";
    echo "<input class='toggle green-toggle' id='logo_position_text' type='radio' name='thrive_theme_options[logo_position]' value='top' $checked_top />
    <label for='logo_position_text' class='btn'>" . __('Top of menu', 'thrive') . "</label>";
}

function thrive_settings_field_navigation_type($options)
{
    if (!isset($options['navigation_type'])) {
        $options['navigation_type'] = "default";
    }
    $checked_1 = ($options['navigation_type'] != "float" && $options['navigation_type'] != "scroll") ? "checked" : "";
    $checked_2 = ($options['navigation_type'] == 'float') ? "checked" : "";
    $checked_3 = ($options['navigation_type'] == 'scroll') ? "checked" : "";
    echo "<input type='radio' name='thrive_theme_options[navigation_type]' value='default' $checked_1 /> " . __('Default', 'thrive') . "";
    echo "<input type='radio' name='thrive_theme_options[navigation_type]' value='float' $checked_2 /> " . __('Floating ', 'thrive') . "";
    echo "<input type='radio' name='thrive_theme_options[navigation_type]' value='scroll' $checked_3 /> " . __('Float on scroll-up', 'thrive') . "";
}

function thrive_settings_field_social_custom_posts($options)
{
    if (!isset($options['social_custom_posts'])) {
        $options['social_custom_posts'] = "";
    }
    $exlude_types = array("post", "page", "revision", "nav_menu_item", "wpcf7_contact_form", "ngg_album", "ngg_gallery", "ngg_pictures", "displayed_gallery", "display_type",
        "gal_display_source", "lightbox_library", "focus_area", "thrive_optin");

    $post_types = get_post_types();

    foreach ($post_types as $post_type) {
        if (!in_array($post_type, $exlude_types)) {
            $checked_post = (strpos($options['social_custom_posts'], $post_type) !== false) ? " checked" : "";
            echo "<input " . $checked_post . " type='checkbox' value='" . $post_type . "' class='thrive_chk_social_custom_posts' /> " . $post_type . " &nbsp;&nbsp;";
        }
    }
    echo "<input type='hidden' value='" . $options['social_custom_posts'] . "' id='thrive_hidden_social_custom_posts' name='thrive_theme_options[social_custom_posts]' />";
}

function thrive_header_number_render_preview()
{

    if (!wp_verify_nonce($_REQUEST['nonce'], "thrive_preview_header_number_nonce")) {
        echo 0;
        die;
    }
    if (!isset($_POST['id_post'])) {
        $current_focus = array();
    } else {
        $current_focus = get_post($_POST['id_post']);
    }

    $preview_params = array('header_phone_no' => $_POST['header_phone_no'],
        'header_phone_text' => $_POST['header_phone_text'],
        'header_phone_text_mobile' => $_POST['header_phone_text_mobile'],
        'header_phone_btn_color' => $_POST['header_phone_btn_color'],
    );

    $template_path = get_template_directory() . "/inc/templates/header-phone-preview.php";
    require_once $template_path;
    die;
}

function thrive_settings_field_blog_post_layout($options)
{
    if (!isset($options['blog_post_layout'])) {
        $options['blog_post_layout'] = "default";
    }
    $checked_1 = ($options['blog_post_layout'] != 'full_width' && $options['blog_post_layout'] != "narrow") ? "checked" : "";
    $checked_2 = ($options['blog_post_layout'] == 'full_width') ? "checked" : "";
    $checked_3 = ($options['blog_post_layout'] == 'narrow') ? "checked" : "";

    echo "<input type='radio' name='thrive_theme_options[blog_post_layout]' class='thrive_theme_options_blog_post_layout_radio' value='default' $checked_1 /> " . __('Default', 'thrive') . "";
    echo "<input type='radio' name='thrive_theme_options[blog_post_layout]' class='thrive_theme_options_blog_post_layout_radio' value='full_width' $checked_2 /> " . __('Full Width', 'thrive') . "";
    echo "<input type='radio' name='thrive_theme_options[blog_post_layout]' class='thrive_theme_options_blog_post_layout_radio' value='narrow' $checked_3 /> " . __('Narrow', 'thrive') . "";
}

function thrive_settings_field_social_site_meta_enable($options)
{
    if ($options['social_site_meta_enable'] === NULL || !isset($options['social_site_meta_enable']) || $options['social_site_meta_enable'] == "") {
        $options['social_site_meta_enable'] = _thrive_get_social_site_meta_enable_default_value();
    }
    $checked_on = ($options['social_site_meta_enable'] == 1) ? "checked" : "";
    $checked_off = ($options['social_site_meta_enable'] != 1) ? "checked" : "";
    echo "<input class='toggle toggle-left' type='radio' id='social_site_meta_enable_true' name='thrive_theme_options[social_site_meta_enable]' value='1' $checked_on />
    <label for='social_site_meta_enable_true' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' type='radio' id='social_site_meta_enable_false' name='thrive_theme_options[social_site_meta_enable]' value='0' $checked_off />
    <label for='social_site_meta_enable_false' class='btn'>" . __('Off', 'thrive') . "</label>";
}