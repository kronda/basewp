<?php

/*
 * Get the default values for the theme customization settings
 * @param string $color_scheme Optional - the color scheme - if not selected the current one will be used
 * @return array The default values array
 */

function thrive_get_default_customizer_options($color_scheme = null) {

    if (!$color_scheme) {
        $color_scheme = thrive_get_theme_options('color_scheme');
    }

    $default_values = array();
    switch ($color_scheme) {
        case 'blue':
            $default_values = array(
                'thrivetheme_link_color' => '#28cdfb',
                'thrivetheme_highlight_color' => '#28cdfb',
                'thrivetheme_menu_highlight_color' => '#28cdfb',
                'thrivetheme_headline_color' => '#555555',
                'thrivetheme_bodytext_color' => '#555555',
                'thrivetheme_header_font' => '//fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin-ext,latin',
                'thrivetheme_body_font' => "//fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin-ext,latin",
                'thrivetheme_header_fontsize' => '33',
                'thrivetheme_body_fontsize' => '13',
                'thrivetheme_body_lineheight' => '2',
                'thrivetheme_default_highlight' =>'#0f3f8d #5772c9',
                'thrivetheme_highlight_background_color'=>'#0f3f8d',
                'thrivetheme_highlight_link_color'=>'#FFFFFF',
                'thrivetheme_highlight_hover_background_color'=>'#5772c9',
                'thrivetheme_highlight_hover_link_color'=>'#FFFFFF'
            );
            break;
        case 'brown':
            $default_values = array(
                'thrivetheme_link_color' => '#736357',
                'thrivetheme_highlight_color' => '#736357',
                'thrivetheme_menu_highlight_color' => '#736357',
                'thrivetheme_headline_color' => '#555555',
                'thrivetheme_bodytext_color' => '#555555',
                'thrivetheme_header_font' => '//fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin-ext,latin',
                'thrivetheme_body_font' => "//fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin-ext,latin",
                'thrivetheme_header_fontsize' => '33',
                'thrivetheme_body_fontsize' => '13',
                'thrivetheme_body_lineheight' => '2',
                'thrivetheme_default_highlight' =>'#736357 #908073',
                'thrivetheme_highlight_background_color'=>'#736357',
                'thrivetheme_highlight_link_color'=>'#FFFFFF',
                'thrivetheme_highlight_hover_background_color'=>'#908073',
                'thrivetheme_highlight_hover_link_color'=>'#FFFFFF'
            );
            break;
        case 'dark':
            $default_values = array(
                'thrivetheme_link_color' => '#34495e',
                'thrivetheme_highlight_color' => '#34495e',
                'thrivetheme_menu_highlight_color' => '#34495e',
                'thrivetheme_headline_color' => '#555555',
                'thrivetheme_bodytext_color' => '#555555',
                'thrivetheme_header_font' => '//fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin-ext,latin',
                'thrivetheme_body_font' => "//fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin-ext,latin",
                'thrivetheme_header_fontsize' => '33',
                'thrivetheme_body_fontsize' => '13',
                'thrivetheme_body_lineheight' => '2',
                'thrivetheme_default_highlight' =>'#5b5b5b #7c7c7c',
                'thrivetheme_highlight_background_color'=>'#5b5b5b',
                'thrivetheme_highlight_link_color'=>'#FFFFFF',
                'thrivetheme_highlight_hover_background_color'=>'#7c7c7c',
                'thrivetheme_highlight_hover_link_color'=>'#FFFFFF'
            );
            break;
        case 'liliac':
            $default_values = array(
                'thrivetheme_link_color' => '#7b0046',
                'thrivetheme_highlight_color' => '#7b0046',
                'thrivetheme_menu_highlight_color' => '#7b0046',
                'thrivetheme_headline_color' => '#555555',
                'thrivetheme_bodytext_color' => '#555555',
                'thrivetheme_header_font' => '//fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin-ext,latin',
                'thrivetheme_body_font' => "//fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin-ext,latin",
                'thrivetheme_header_fontsize' => '33',
                'thrivetheme_body_fontsize' => '13',
                'thrivetheme_body_lineheight' => '2',
                'thrivetheme_default_highlight' =>'#7a0048 #ad3f75',
                'thrivetheme_highlight_background_color'=>'#7a0048',
                'thrivetheme_highlight_link_color'=>'#FFFFFF',
                'thrivetheme_highlight_hover_background_color'=>'#ad3f75',
                'thrivetheme_highlight_hover_link_color'=>'#FFFFFF'
            );
            break;
        case 'navy':
            $default_values = array(
                'thrivetheme_link_color' => '#005b7f',
                'thrivetheme_highlight_color' => '#005b7f',
                'thrivetheme_menu_highlight_color' => '#005b7f',
                'thrivetheme_headline_color' => '#555555',
                'thrivetheme_bodytext_color' => '#555555',
                'thrivetheme_header_font' => '//fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin-ext,latin',
                'thrivetheme_body_font' => "//fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin-ext,latin",
                'thrivetheme_header_fontsize' => '33',
                'thrivetheme_body_fontsize' => '13',
                'thrivetheme_body_lineheight' => '2',
                'thrivetheme_default_highlight' =>'#005b7f #1485b2',
                'thrivetheme_highlight_background_color'=>'#005b7f',
                'thrivetheme_highlight_link_color'=>'#FFFFFF',
                'thrivetheme_highlight_hover_background_color'=>'#1485b2',
                'thrivetheme_highlight_hover_link_color'=>'#FFFFFF'
            );
            break;
        case 'pink':
            $default_values = array(
                'thrivetheme_link_color' => ' #fcb4e6',
                'thrivetheme_highlight_color' => '#fcb4e6',
                'thrivetheme_menu_highlight_color' => '#fcb4e6',
                'thrivetheme_headline_color' => '#555555',
                'thrivetheme_bodytext_color' => '#555555',
                'thrivetheme_header_font' => '//fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin-ext,latin',
                'thrivetheme_body_font' => "//fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin-ext,latin",
                'thrivetheme_header_fontsize' => '33',
                'thrivetheme_body_fontsize' => '13',
                'thrivetheme_body_lineheight' => '2',
                'thrivetheme_default_highlight' =>'#fc71d1 #fcb4e6',
                'thrivetheme_highlight_background_color'=>'#fc71d1',
                'thrivetheme_highlight_link_color'=>'#FFFFFF',
                'thrivetheme_highlight_hover_background_color'=>'#fcb4e6',
                'thrivetheme_highlight_hover_link_color'=>'#FFFFFF'
            );
            break;
        case 'purple':
            $default_values = array(
                'thrivetheme_link_color' => '#8562a3',
                'thrivetheme_highlight_color' => '#8562a3',
                'thrivetheme_menu_highlight_color' => '#8562a3',
                'thrivetheme_headline_color' => '#555555',
                'thrivetheme_bodytext_color' => '#555555',
                'thrivetheme_header_font' => '//fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin-ext,latin',
                'thrivetheme_body_font' => "//fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin-ext,latin",
                'thrivetheme_header_fontsize' => '33',
                'thrivetheme_body_fontsize' => '13',
                'thrivetheme_body_lineheight' => '2',
                'thrivetheme_default_highlight' =>'#855ea2 #ad85cb',
                'thrivetheme_highlight_background_color'=>'#855ea2',
                'thrivetheme_highlight_link_color'=>'#FFFFFF',
                'thrivetheme_highlight_hover_background_color'=>'',
                'thrivetheme_highlight_hover_link_color'=>'#FFFFFF'
            );
            break;
        case 'red':
            $default_values = array(
                'thrivetheme_link_color' => '#ff4351',
                'thrivetheme_highlight_color' => '#ff4351',
                'thrivetheme_menu_highlight_color' => '#ff4351',
                'thrivetheme_headline_color' => '#555555',
                'thrivetheme_bodytext_color' => '#555555',
                'thrivetheme_header_font' => '//fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin-ext,latin',
                'thrivetheme_body_font' => "//fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin-ext,latin",
                'thrivetheme_header_fontsize' => '33',
                'thrivetheme_body_fontsize' => '13',
                'thrivetheme_body_lineheight' => '2',
                'thrivetheme_default_highlight' =>'#ff4351 #fd737d',
                'thrivetheme_highlight_background_color'=>'#ff4351',
                'thrivetheme_highlight_link_color'=>'#FFFFFF',
                'thrivetheme_highlight_hover_background_color'=>'#fd737d',
                'thrivetheme_highlight_hover_link_color'=>'#FFFFFF'
            );
            break;
        case 'yellow':
            $default_values = array(
                'thrivetheme_link_color' => '#feae1b',
                'thrivetheme_highlight_color' => '#feae1b',
                'thrivetheme_menu_highlight_color' => '#feae1b',
                'thrivetheme_headline_color' => '#555555',
                'thrivetheme_bodytext_color' => '#555555',
                'thrivetheme_header_font' => '//fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin-ext,latin',
                'thrivetheme_body_font' => "//fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin-ext,latin",
                'thrivetheme_header_fontsize' => '33',
                'thrivetheme_body_fontsize' => '13',
                'thrivetheme_body_lineheight' => '2',
                'thrivetheme_default_highlight' =>'#fbb128 #ffd953',
                'thrivetheme_highlight_background_color'=>'#fbb128',
                'thrivetheme_highlight_link_color'=>'#FFFFFF',
                'thrivetheme_highlight_hover_background_color'=>'#ffd953',
                'thrivetheme_highlight_hover_link_color'=>'#FFFFFF'
            );
            break;
        default:
            $default_values = array(
                'thrivetheme_link_color' => '#34495e',
                'thrivetheme_highlight_color' => '#34495e',
                'thrivetheme_menu_highlight_color' => '#34495e',
                'thrivetheme_headline_color' => '#555555',
                'thrivetheme_bodytext_color' => '#555555',
                'thrivetheme_header_font' => '//fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin-ext,latin',
                'thrivetheme_body_font' => "//fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin-ext,latin",
                'thrivetheme_header_fontsize' => '33',
                'thrivetheme_body_fontsize' => '13',
                'thrivetheme_body_lineheight' => '2',
                'thrivetheme_default_highlight' =>'#0f3f8d #5772c9',
                'thrivetheme_highlight_background_color'=>'#0f3f8d',
                'thrivetheme_highlight_link_color'=>'#FFFFFF',
                'thrivetheme_highlight_hover_background_color'=>'#5772c9',
                'thrivetheme_highlight_hover_link_color'=>'#FFFFFF'
            );
            break;
    }

    $default_values['thrivetheme_logo_image_width'] = 200;
    $default_values['thrivetheme_menu_link_color'] = "#4E4E4E";
    $default_values['thrivetheme_theme_background'] = "default-header";

    return $default_values;
}

/*
 * Get the default theme options
 * @return array The default values array
 */

function thrive_get_default_theme_options() {

    $footerCopyrightText = "Copyright text " . date("Y") . " by " . get_bloginfo('name') . ". ";
    //$footerCopyrightLinks = 'Designed by <a href="http://www.thrivethemes.com" target="_blank" >Thrive Themes</a>|Powered by <a href="http://www.wordpress.org" target="_blank">WordPress</a>';
    $default_theme_options = array(
        'logo' => get_template_directory_uri() . '/inc/images/TT-logo-small.png',
        'logo_width' => '200',
        'logo_height' => '100',
        'favicon' => '',
        'footer_copyright' => $footerCopyrightText,
        'footer_copyright_links' => 1,
        'display_breadcrumbs' => 1,
        'comments_on_pages' => 0,
        'color_scheme' => 'dark',
        'sidebar_alignement' => 'right',
        'extended_menu' => 'on',
        'fonts' => 'pair1',
        'custom_css' => "",
        'featured_image_style' => 'wide',
        'featured_image_single_post' => 1,
        'meta_author_name' => 1,
        'meta_post_date' => 1,
        'meta_post_category' => 1,
        'meta_post_tags' => 0,
        'meta_comment_count' => 1,
        'bottom_about_author' => 1,
        'bottom_previous_next' => 0,
        'related_posts_box' => 0,
        'related_posts_number' => 8,
        'related_posts_title' => __('Related Posts', "thrive"),
        'related_posts_images' => 1,
        'other_read_more_type' => 'text',
        'other_read_more_text' => __("Continue reading", 'thrive'),
        'hide_cats_from_blog' => '',
        'analytics_header_script' => "",
        'analytics_body_script' => "",
        'analytics_body_script_top' => "",
        'other_show_comment_date' => 1,
        'image_optimization_type' => "off",
        'relative_time' => 0,
        'highlight_author_comments' => 1,
        'header_navigation' => 65,
        'social_facebook' => "",
        'social_twitter' => "",
        'social_gplus' => "",
        'social_pinterest' => "",
        'social_youtube' => "",
        'social_linkedin' => "",
        'client_logos' => "",
        'comments_lazy' => 0,
        'enable_fb_comments' => "off",
        'fb_app_id' => "",
        'fb_no_comments' => 5,
        'fb_color_scheme' => 'light',
        'privacy_tpl_website' => get_bloginfo('url'),
        'privacy_tpl_address' => "",
        'privacy_tpl_company' => "",
        'privacy_tpl_contact' => "",
        'logo_type' => "image",
        'logo_text' => "",
        'logo_color' => "default",
        'enable_social_buttons' => 0,
        'enable_floating_icons' => 1,
        'enable_twitter_button' => 0,
        'social_twitter_username' => "",
        'enable_facebook_button' => 0,
        'enable_google_button' => 0,
        'enable_linkedin_button' => 0,
        'enable_pinterest_button' => 0,
        'social_display_location' => 'posts',
        'social_attention_grabber' => 'none',
        'social_cta_text' => '',
        'social_add_like_btn' => 0,
        'other_show_excerpt' => 0,
        'logo_position' => 'side',
        'navigation_type' => 'default',
        'social_custom_posts' => "",
        'header_phone' => 0,
        'header_phone_no' => "",
        'header_phone_text' => "",
        'header_phone_text_mobile' => "",
        'header_phone_btn_color' => "default",
        '404_custom_text' => '',
        '404_display_sitemap' => 'off',
        'related_no_text' => __("No related posts for this content", 'thrive'),
        'related_ignore_cats' => '',
        'related_ignore_tags' => '',
        'related_number_posts' => 10,
        'related_posts_enabled' => 0,
        'social_site_name' => '',
        'social_site_title' => '',
        'social_site_description' => '',
        'social_site_image' => '',
        'social_site_twitter_username' => '',
        'appr_enable_feature' => 0,
        'appr_different_logo' => 0,
        'appr_logo' => "",
        'appr_logo_type' => "image",
        'appr_logo_text' => "",
        'appr_logo_color' => "default",
        'appr_logo_position' => 'top',
        'appr_breadcrumbs' => 0,
        'appr_root_page' => 0,
        'appr_sidebar' => "left",
        'appr_page_comments' => 1,
        'appr_prev_next_link' => 1,
        'appr_media_bg_color' => "default",
        'appr_favorites' => 1,
        'appr_progress_track' => 1,
        'appr_completed_text' => "I have completed this lesson",
        'appr_download_heading' => "Resources for this lesson",
        'appr_replace_lesson' => "Lesson",
        'appr_url_pages' => "members",
        'appr_url_lessons' => "lessons",
        'appr_url_categories' => "apprentice",
        'appr_url_tags' => "apprentice-tag",
        'appr_meta_author_name' => 1,
        'appr_meta_post_date' => 1,
        'appr_meta_post_category' => 1,
        'appr_meta_post_tags' => 0,
        'appr_meta_comment_count' => 1,
        'appr_bottom_about_author' => 1,
        'appr_bottom_previous_next' => 0,
        'blog_post_layout' => "default",
        'social_site_meta_enable' => _thrive_get_social_site_meta_enable_default_value(),
    );



    $afterFilter = apply_filters('thrive_default_theme_options', $default_theme_options);

    return $afterFilter;
}

/*
 * Overwrites the general theme options with the options for a specific post
 * @param int $postId Post Id
 * @return array
 */

function thrive_get_options_for_post($postId = null, $features = null) {
    if ($features && is_array($features) && isset($features['apprentice']) && $features['apprentice'] == 1) {
        $general_options = thrive_appr_get_theme_options();
    } else {
        $general_options = thrive_get_theme_options();
    }

    if (!$postId) {
        if (is_singular()) {
            $postId = get_the_ID();
        } else {
            return $general_options;
        }
    }

    $post_options = get_post_custom($postId);

    $display_options = $general_options;

    //if we're not on a page or an apprentice page set the comments on pages option to 1
    $post_type = get_post_type();
    if ($post_type != "page" && $post_type != TT_APPR_POST_TYPE_PAGE) {
        $display_options['comments_on_pages'] = 1;
    }

    /* if((show_title variable set and set to 1) or (not set at all))? show title : hide title */
    $display_options['show_post_title'] = ((isset($post_options['_thrive_meta_show_post_title'][0]) && $post_options['_thrive_meta_show_post_title'][0] == 1) || (!isset($post_options['_thrive_meta_show_post_title'][0]))) ? 1 : 0;

    if (isset($post_options['_thrive_meta_post_meta_info'][0]) && $post_options['_thrive_meta_post_meta_info'][0] == "on") {
        $display_options['meta_comment_count'] = $display_options['meta_author_name'] = $display_options['meta_post_date'] = $display_options['meta_post_category'] = $display_options['meta_post_tags'] = 1;
    }
    if (isset($post_options['_thrive_meta_post_meta_info'][0]) && $post_options['_thrive_meta_post_meta_info'][0] == "off") {
        $display_options['meta_comment_count'] = $display_options['meta_author_name'] = $display_options['meta_post_date'] = $display_options['meta_post_category'] = $display_options['meta_post_tags'] = 0;
    }

    if (isset($post_options['_thrive_meta_post_breadcrumbs'][0]) && $post_options['_thrive_meta_post_breadcrumbs'][0] == "on") {
        $display_options['display_breadcrumbs'] = 1;
    }
    if (isset($post_options['_thrive_meta_post_breadcrumbs'][0]) && $post_options['_thrive_meta_post_breadcrumbs'][0] == "off") {
        $display_options['display_breadcrumbs'] = 0;
    }

    // featured images - global vs local settings
    // if viewing a page or post and featured image options set at post/page level, then override global setting.
    if (is_singular() && isset($post_options['_thrive_meta_post_featured_image'][0]) && ($post_options['_thrive_meta_post_featured_image'][0] == "thumbnail" || $post_options['_thrive_meta_post_featured_image'][0] == "wide" || $post_options['_thrive_meta_post_featured_image'][0] == "off")) {
        $display_options['featured_image_style'] = $post_options['_thrive_meta_post_featured_image'][0];
    }

    if (is_singular() && isset($post_options['_thrive_meta_post_featured_image'][0]) && ($post_options['_thrive_meta_post_featured_image'][0] != "thumbnail" && $post_options['_thrive_meta_post_featured_image'][0] != "wide" && $post_options['_thrive_meta_post_featured_image'][0] != "off")) {
        if ($display_options['featured_image_single_post'] == 0) {
            $display_options['featured_image_style'] = "off";
        }
    }
    // if viewing archive page and post level setting is accurate then override global.  Else keep global setting.
    if (!is_singular() && isset($post_options['_thrive_meta_post_featured_image'][0]) && $post_options['_thrive_meta_post_featured_image'][0] != "off" && $post_options['_thrive_meta_post_featured_image'][0] != "") {
        $display_options['featured_image_style'] = $post_options['_thrive_meta_post_featured_image'][0];
    }
    //


    if (is_single() || is_page()) {
        if (isset($post_options['_thrive_meta_social_data_title'][0]) && !empty($post_options['_thrive_meta_social_data_title'][0])) {
            $display_options['social_site_title'] = $post_options['_thrive_meta_social_data_title'][0];
        }
        if (isset($post_options['_thrive_meta_social_data_description'][0]) && !empty($post_options['_thrive_meta_social_data_description'][0])) {
            $display_options['social_site_description'] = $post_options['_thrive_meta_social_data_description'][0];
        }
        if (isset($post_options['_thrive_meta_social_image'][0]) && !empty($post_options['_thrive_meta_social_image'][0])) {
            $display_options['social_site_image'] = $post_options['_thrive_meta_social_image'][0];
        }
        if (isset($post_options['_thrive_meta_social_twitter_username'][0]) && !empty($post_options['_thrive_meta_social_twitter_username'][0])) {
            $display_options['social_site_twitter_username'] = $post_options['_thrive_meta_social_twitter_username'][0];
        }
    }


    if (isset($post_options['_thrive_meta_post_header_scripts'][0]) && $post_options['_thrive_meta_post_header_scripts'][0] != "" && (is_single() || is_page())) {
        $display_options['analytics_header_script'] = $post_options['_thrive_meta_post_header_scripts'][0];
    }

    if (isset($post_options['_thrive_meta_post_body_scripts'][0]) && $post_options['_thrive_meta_post_body_scripts'][0] != "" && (is_single() || is_page())) {
        $display_options['analytics_body_script'] = $post_options['_thrive_meta_post_body_scripts'][0];
    }

    if (isset($post_options['_thrive_meta_post_body_scripts_top'][0]) && $post_options['_thrive_meta_post_body_scripts_top'][0] != "" && (is_single() || is_page())) {
        $display_options['analytics_body_script_top'] = $post_options['_thrive_meta_post_body_scripts_top'][0];
    }

    if (isset($post_options['_thrive_meta_post_custom_css'][0]) && $post_options['_thrive_meta_post_custom_css'][0] != "" && (is_single() || is_page())) {
        $display_options['custom_css'] = $post_options['_thrive_meta_post_custom_css'][0];
    }

    if ((isset($display_options['meta_post_tags']) && $display_options['meta_post_tags'] == 1)) {
        $posttags = get_the_tags($postId);
        if (count($posttags) == 0 || !$posttags) {
            $display_options['meta_post_tags'] = 0;
        }
    }
    if ((isset($display_options['meta_post_category']) && $display_options['meta_post_category'] == 1)) {
        $categories = get_the_category($postId);
        if (count($categories) == 0 || !$categories) {
            $display_options['meta_post_category'] = 0;
        }
    }

    $display_options['display_meta'] = 0;
    if ((isset($display_options['meta_author_name']) && $display_options['meta_author_name'] == 1) || (isset($display_options['meta_post_date']) && $display_options['meta_post_date'] == 1) || (isset($display_options['meta_post_category']) && $display_options['meta_post_category'] == 1 && (get_the_category($postId))) || (isset($display_options['meta_post_tags']) && $display_options['meta_post_tags'] == 1) && (get_the_tags($postId))) {
        $display_options['display_meta'] = 1;
    }

    $display_options['meta_no_columns'] = 0;
    if ((isset($display_options['meta_author_name']) && $display_options['meta_author_name'] == 1)) {
        $display_options['meta_no_columns'] ++;
    }
    if ((isset($display_options['meta_post_date']) && $display_options['meta_post_date'] == 1)) {
        $display_options['meta_no_columns'] ++;
    }
    if ((isset($display_options['meta_post_category']) && $display_options['meta_post_category'] == 1)) {
        $display_options['meta_no_columns'] ++;
    }
    if ((isset($display_options['meta_post_tags']) && $display_options['meta_post_tags'] == 1)) {
        $display_options['meta_no_columns'] ++;
    }

	if (isset($post_options['_thrive_meta_post_floating_icons'][0]) && $post_options['_thrive_meta_post_floating_icons'][0] != "default" && (is_single() || is_page())) {
		$display_options['enable_floating_icons'] = $post_options['_thrive_meta_post_floating_icons'][0] == "on" ? 1 : 0;
	}

    if (isset($post_options['_thrive_meta_post_share_buttons'][0]) && $post_options['_thrive_meta_post_share_buttons'][0] == "off") {
        $display_options['enable_social_buttons'] = 0;
    } elseif (is_page() && !empty($display_options['social_display_location']) && strpos($display_options['social_display_location'], "page") === false) {
        $display_options['enable_social_buttons'] = 0;
    } elseif (!is_page() && !empty($display_options['social_display_location']) && strpos($display_options['social_display_location'], "post") === false) {
        $display_options['enable_social_buttons'] = 0;
    }
    if (isset($post_options['_thrive_meta_post_related_box'][0]) && $post_options['_thrive_meta_post_related_box'][0] == "off" && (is_single() || is_page())) {
        $display_options['related_posts_box'] = 0;
    }

    $current_post_type = get_post_type();
    if (isset($current_post_type) && !empty($current_post_type)) {
        if ($current_post_type != "post" && $current_post_type != "page") {
            if (strpos($display_options['social_custom_posts'], $current_post_type) === false) {
                $display_options['enable_social_buttons'] = 0;
            } else {
                $display_options['enable_social_buttons'] = 1;
            }
        }
    } else {
        $display_options['enable_social_buttons'] = 0;
    }

    //disable the floating option for the sharing buttons on the blank page template
    if (is_page()) {
        $page_template = get_post_meta($postId, '_wp_page_template', true);
        if ($page_template == "blank-page.php") {
            $display_options['enable_floating_icons'] = 0;
        }
    }

    return $display_options;
}

/*
 * Set the default values for the theme customization settings
 * @param string $color_scheme Optional - the color scheme - if not selected the current one will be used
 * @param boolean $resetOnlyColors
 */

function thrive_set_default_customizer_options($color_scheme = null, $resetOnlyColors = false) {
    if ($color_scheme === null) {
        $color_scheme = thrive_get_theme_options('color_scheme');
    }
    $default_options = thrive_get_default_customizer_options($color_scheme);

    foreach ($default_options as $key => $val) {
        if ($resetOnlyColors) {
            if (strpos($key, "color") !== false) {
                set_theme_mod($key, $val);
            }
        } else {
            set_theme_mod($key, $val);
        }
    }

    //set other default values
    if (!$resetOnlyColors) {
        set_theme_mod("thrivetheme_bg_pattern", "anopattern");
        set_theme_mod("thrivetheme_headline_case", "Regular");
        set_theme_mod("thrivetheme_headline_weight", "Bold");
    }
    set_theme_mod("background_color", "#fbfbfb");
}

?>
