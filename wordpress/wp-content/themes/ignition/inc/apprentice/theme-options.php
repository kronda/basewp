<?php

add_action('admin_init', 'thrive_appr_theme_options_init', 12);

function thrive_appr_theme_options_init()
{
    $options = thrive_get_theme_options();
    $options_titles = _thrive_get_theme_options_fields_titles();
    //apprentince fields
    add_settings_field('appr_enable_feature', $options_titles['meta_author_name'], 'thrive_settings_field_appr_enable', 'theme_appr_enable_options', 'appr_enable_settings', $options);

    add_settings_field('appr_different_logo', $options_titles['appr_different_logo'], 'thrive_settings_field_appr_different_logo', 'theme_appr_layout_options', 'appr_layout_settings', $options);
    add_settings_field('appr_logo_type', __('Logo Type', 'thrive') . '', 'thrive_settings_field_appr_logo_type', 'theme_appr_layout_options', 'appr_layout_settings', $options);
    add_settings_field('appr_logo', __('Lesson Pages Logo', 'thrive') . '', 'thrive_settings_field_appr_logo', 'theme_appr_layout_options', 'appr_layout_settings', $options);
    add_settings_field('appr_logo_text', __('Logo Text', 'thrive') . '', 'thrive_settings_field_appr_logo_text', 'theme_appr_layout_options', 'appr_layout_settings', $options);
    add_settings_field('appr_logo_color', __('Logo Color', 'thrive') . '', 'thrive_settings_field_appr_logo_color', 'theme_appr_layout_options', 'appr_layout_settings', $options);
    add_settings_field('appr_logo_position', __('Logo Position', 'thrive') . '', 'thrive_settings_field_appr_logo_position', 'theme_appr_layout_options', 'appr_layout_settings', $options);

    add_settings_field('appr_breadcrumbs', $options_titles['appr_breadcrumbs'], 'thrive_settings_field_appr_breadcrumbs', 'theme_appr_layout_options', 'appr_layout_settings', $options);
    add_settings_field('appr_root_page', $options_titles['appr_root_page'], 'thrive_settings_field_appr_root_page', 'theme_appr_layout_options', 'appr_layout_settings', $options);
    add_settings_field('appr_sidebar', $options_titles['appr_sidebar'], 'thrive_settings_field_appr_sidebar', 'theme_appr_layout_options', 'appr_layout_settings', $options);
    add_settings_field('appr_page_comments', $options_titles['appr_page_comments'], 'thrive_settings_field_appr_page_comments', 'theme_appr_layout_options', 'appr_layout_settings', $options);
    add_settings_field('appr_prev_next_link', $options_titles['appr_prev_next_link'], 'thrive_settings_field_appr_prev_next_link', 'theme_appr_layout_options', 'appr_layout_settings', $options);
    add_settings_field('appr_media_bg_color', $options_titles['appr_media_bg_color'], 'thrive_settings_field_appr_media_bg_color', 'theme_appr_layout_options', 'appr_layout_settings', $options);

    add_settings_field('appr_favorites', $options_titles['appr_favorites'], 'thrive_settings_field_appr_favorites', 'theme_appr_feature_options', 'appr_features_settings', $options);
    add_settings_field('appr_progress_track', $options_titles['appr_progress_track'], 'thrive_settings_field_appr_progress_track', 'theme_appr_feature_options', 'appr_features_settings', $options);
    add_settings_field('appr_completed_text', $options_titles['appr_completed_text'], 'thrive_settings_field_appr_completed_text', 'theme_appr_feature_options', 'appr_features_settings', $options);
    add_settings_field('appr_download_heading', $options_titles['appr_download_heading'], 'thrive_settings_field_appr_download_heading', 'theme_appr_feature_options', 'appr_features_settings', $options);
    //add_settings_field('appr_replace_lesson',$options_titles['appr_replace_lesson'], 'thrive_settings_field_appr_replace_lesson', 'theme_appr_feature_options', 'appr_features_settings', $options);

    add_settings_field('appr_meta_author_name', $options_titles['meta_author_name'], 'thrive_settings_field_appr_meta_author_name', 'theme_appr_blog_options', 'appr_blog_settings', $options);
    add_settings_field('appr_meta_post_date', __('Lesson Date', 'thrive'), 'thrive_settings_field_appr_meta_post_date', 'theme_appr_blog_options', 'appr_blog_settings', $options);
    add_settings_field('appr_meta_post_category', __('Lesson Category', 'thrive'), 'thrive_settings_field_appr_meta_post_category', 'theme_appr_blog_options', 'appr_blog_settings', $options);
    //add_settings_field('appr_meta_comment_count', $options_titles['meta_comment_count'], 'thrive_settings_field_appr_meta_comment_count', 'theme_appr_blog_options', 'appr_blog_settings', $options);
    add_settings_field('appr_meta_post_tags', __('Lesson Tags', 'thrive'), 'thrive_settings_field_appr_meta_post_tags', 'theme_appr_blog_options', 'appr_blog_settings', $options);
    add_settings_field('appr_bottom_about_author', $options_titles['bottom_about_author'], 'thrive_settings_field_appr_bottom_about_author', 'theme_appr_blog_options', 'appr_blog_settings', $options);

    add_settings_field('appr_url_pages', $options_titles['appr_url_pages'], 'thrive_settings_field_appr_url_pages', 'theme_appr_url_options', 'appr_url_settings', $options);
    add_settings_field('appr_url_lessons', $options_titles['appr_url_lessons'], 'thrive_settings_field_appr_url_lessons', 'theme_appr_url_options', 'appr_url_settings', $options);
    add_settings_field('appr_url_categories', $options_titles['appr_url_categories'], 'thrive_settings_field_appr_url_categories', 'theme_appr_url_options', 'appr_url_settings', $options);
    add_settings_field('appr_url_tags', $options_titles['appr_url_tags'], 'thrive_settings_field_appr_url_tags', 'theme_appr_url_options', 'appr_url_settings', $options);
}

function thrive_settings_field_appr_enable($options)
{
    if (!isset($options['appr_enable'])) {
        $options['appr_enable'] = 0;
    }
}

function thrive_settings_field_appr_different_logo($options)
{
    if (!isset($options['appr_different_logo'])) {
        $options['appr_different_logo'] = 0;
    }
    $checked_on = ($options['appr_different_logo'] == '1') ? "checked" : "";
    $checked_off = ($options['appr_different_logo'] == '0') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='appr_different_logo_on' type='radio' name='thrive_theme_options[appr_different_logo]' value='1' $checked_on />
    <label for='appr_different_logo_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='appr_different_logo_off' type='radio' name='thrive_theme_options[appr_different_logo]' value='0' $checked_off />
    <label for='appr_different_logo_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_appr_logo($options)
{
    if (!isset($options['appr_logo'])) {
        $options['appr_logo'] = "";
    }
    echo "<input class='thrive_options' type='text' name='thrive_theme_options[appr_logo]' id='thrive_theme_options_appr_logo' value='" . esc_attr($options['appr_logo']) . "' /><br/>";
    echo "<input class='thrive_options pure-button upload' type='button' id='thrive_theme_options_appr_logo_btn' value=' " . __("Upload") . " ' />";
    echo "<input class='thrive_options pure-button clear-field remove' type='button' id='thrieve_theme_btn_delete_appr_logo' value=' " . __("Remove") . " ' />";
}

function thrive_settings_field_appr_logo_type($options)
{
    if (!isset($options['appr_logo_type'])) {
        $options['appr_logo_type'] = "image";
    }
    $checked_image = ($options['appr_logo_type'] != 'text') ? "checked" : "";
    $checked_text = ($options['appr_logo_type'] == 'text') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='appr_logo_type_image' type='radio' name='thrive_theme_options[appr_logo_type]' value='image' $checked_image />
    <label for='appr_logo_type_image' class='btn'>" . __('Image', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='appr_logo_type_text' type='radio' name='thrive_theme_options[appr_logo_type]' value='text' $checked_text />
    <label for='appr_logo_type_text' class='btn'>" . __('Text', 'thrive') . "</label>";
}

function thrive_settings_field_appr_logo_text($options)
{
    if (!isset($options['appr_logo_text'])) {
        $options['appr_logo_text'] = "";
    }
    echo "<input type='text' name='thrive_theme_options[appr_logo_text]' id='thrive_theme_options_appr_logo_text' value='" . esc_attr($options['appr_logo_text']) . "' />";
}

function thrive_settings_field_appr_logo_color($options)
{
    if (!isset($options['appr_logo_color'])) {
        $options['appr_logo_color'] = "default";
    }
    $colors = _thrive_get_color_scheme_options();
    $colors = array_merge(array('default' => __("Default", 'thrive')), $colors);
    echo "<select name='thrive_theme_options[appr_logo_color]' id='thrive_theme_options_appr_logo_color'>";
    foreach ($colors as $key => $c) {
        $is_selected = ($key == $options['appr_logo_color']) ? " selected" : "";
        echo "<option value='" . $key . "'" . $is_selected . ">" . $c . "</option>";
    }
    echo "</select>";
}

function thrive_settings_field_appr_logo_position($options)
{
    if (!isset($options['appr_logo_position'])) {
        $options['appr_logo_position'] = "side";
    }
    $checked_side = ($options['appr_logo_position'] != 'top') ? "checked" : "";
    $checked_top = ($options['appr_logo_position'] == 'top') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='appr_logo_position_image' type='radio' name='thrive_theme_options[appr_logo_position]' value='side' $checked_side />
    <label for='appr_logo_position_image' class='btn'>" . __('Side of menu', 'thrive') . "</label>";
    echo "<input class='toggle green-toggle' id='appr_logo_position_text' type='radio' name='thrive_theme_options[appr_logo_position]' value='top' $checked_top />
    <label for='appr_logo_position_text' class='btn'>" . __('Top of menu', 'thrive') . "</label>";
}

function thrive_settings_field_appr_breadcrumbs($options)
{
    if (!isset($options['appr_breadcrumbs'])) {
        $options['appr_breadcrumbs'] = 0;
    }
    $checked_on = ($options['appr_breadcrumbs'] == '1') ? "checked" : "";
    $checked_off = ($options['appr_breadcrumbs'] == '0') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='appr_breadcrumbs_on' type='radio' name='thrive_theme_options[appr_breadcrumbs]' value='1' $checked_on />
    <label for='appr_breadcrumbs_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='appr_breadcrumbs_off' type='radio' name='thrive_theme_options[appr_breadcrumbs]' value='0' $checked_off />
    <label for='appr_breadcrumbs_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_appr_root_page($options)
{
    if (!isset($options['appr_root_page'])) {
        $options['appr_root_page'] = 0;
    }

    $queryPostsArgs = new WP_Query(array(
        'post_type' => TT_APPR_POST_TYPE_PAGE,
        "posts_per_page" => -1
    ));
    $pages = $queryPostsArgs->get_posts();

    echo "<select name='thrive_theme_options[appr_root_page]' id='thrive_theme_options_appr_root_page'>";
    foreach ($pages as $key => $p) {
        $is_selected = ($p->ID == $options['appr_root_page']) ? " selected" : "";
        echo "<option value='" . $p->ID . "'" . $is_selected . ">" . $p->post_title . "</option>";
    }
    echo "</select>";
}

function thrive_settings_field_appr_sidebar($options)
{
    if (!isset($options['appr_sidebar'])) {
        $options['appr_sidebar'] = "left";
    }
    $checked_on = ($options['appr_sidebar'] != 'right') ? "checked" : "";
    $checked_off = ($options['appr_sidebar'] == 'right') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='appr_sidebar_on' type='radio' name='thrive_theme_options[appr_sidebar]' value='left' $checked_on />
    <label for='appr_sidebar_on' class='btn'>" . __('Left', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='appr_sidebar_off' type='radio' name='thrive_theme_options[appr_sidebar]' value='right' $checked_off />
    <label for='appr_sidebar_off' class='btn'>" . __('Right', 'thrive') . "</label>";
}

function thrive_settings_field_appr_page_comments($options)
{
    if (!isset($options['appr_page_comments'])) {
        $options['appr_page_comments'] = 1;
    }
    $checked_on = ($options['appr_page_comments'] == '1') ? "checked" : "";
    $checked_off = ($options['appr_page_comments'] == '0') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='appr_page_comments_on' type='radio' name='thrive_theme_options[appr_page_comments]' value='1' $checked_on />
    <label for='appr_page_comments_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='appr_page_comments_off' type='radio' name='thrive_theme_options[appr_page_comments]' value='0' $checked_off />
    <label for='appr_page_comments_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_appr_prev_next_link($options)
{
    if (!isset($options['appr_prev_next_link'])) {
        $options['appr_prev_next_link'] = 1;
    }
    $checked_on = ($options['appr_prev_next_link'] == '1') ? "checked" : "";
    $checked_off = ($options['appr_prev_next_link'] == '0') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='appr_prev_next_link_on' type='radio' name='thrive_theme_options[appr_prev_next_link]' value='1' $checked_on />
    <label for='appr_prev_next_link_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='appr_prev_next_link_off' type='radio' name='thrive_theme_options[appr_prev_next_link]' value='0' $checked_off />
    <label for='appr_prev_next_link_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_appr_media_bg_color($options)
{
    if (!isset($options['appr_media_bg_color'])) {
        $options['appr_media_bg_color'] = "default";
    }
    $colors = _thrive_get_color_scheme_options();
    $colors = array_merge(array('default' => __("Default", 'thrive')), $colors);
    echo "<select name='thrive_theme_options[appr_media_bg_color]' id='thrive_theme_options_appr_media_bg_color'>";
    foreach ($colors as $key => $c) {
        $is_selected = ($key == $options['appr_media_bg_color']) ? " selected" : "";
        echo "<option value='" . $key . "'" . $is_selected . ">" . $c . "</option>";
    }
    echo "</select>";
}

function thrive_settings_field_appr_favorites($options)
{
    if (!isset($options['appr_favorites'])) {
        $options['appr_favorites'] = 1;
    }
    $checked_on = ($options['appr_favorites'] == '1') ? "checked" : "";
    $checked_off = ($options['appr_favorites'] == '0') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='appr_favorites_on' type='radio' name='thrive_theme_options[appr_favorites]' value='1' $checked_on />
    <label for='appr_favorites_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='appr_favorites_off' type='radio' name='thrive_theme_options[appr_favorites]' value='0' $checked_off />
    <label for='appr_favorites_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_appr_progress_track($options)
{
    if (!isset($options['appr_progress_track'])) {
        $options['appr_progress_track'] = 1;
    }
    $checked_on = ($options['appr_progress_track'] == '1') ? "checked" : "";
    $checked_off = ($options['appr_progress_track'] == '0') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='appr_progress_track_on' type='radio' name='thrive_theme_options[appr_progress_track]' value='1' $checked_on />
    <label for='appr_progress_track_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='appr_progress_track_off' type='radio' name='thrive_theme_options[appr_progress_track]' value='0' $checked_off />
    <label for='appr_progress_track_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_appr_completed_text($options)
{
    if (!isset($options['appr_completed_text'])) {
        $options['appr_completed_text'] = "I have completed this lesson";
    }

    echo "<input type='text' name='thrive_theme_options[appr_completed_text]' id='thrive_theme_options_appr_completed_text' value='" . esc_attr($options['appr_completed_text']) . "' />";
}

function thrive_settings_field_appr_download_heading($options)
{
    if (!isset($options['appr_download_heading'])) {
        $options['appr_download_heading'] = "Resources for this lesson";
    }

    echo "<input type='text' name='thrive_theme_options[appr_download_heading]' id='thrive_theme_options_appr_download_heading' value='" . esc_attr($options['appr_download_heading']) . "' />";
}

function thrive_settings_field_appr_replace_lesson($options)
{
    if (!isset($options['appr_replace_lesson'])) {
        $options['appr_replace_lesson'] = "Lesson";
    }

    echo "<input type='text' name='thrive_theme_options[appr_replace_lesson]' id='thrive_theme_options_appr_replace_lessont' value='" . esc_attr($options['appr_replace_lesson']) . "' />";
}

function thrive_settings_field_appr_url_pages($options)
{
    if (!isset($options['appr_url_pages'])) {
        $options['appr_url_pages'] = "members";
    }

    echo "<input type='text' name='thrive_theme_options[appr_url_pages]' id='thrive_theme_options_appr_url_pages' value='" . esc_attr($options['appr_url_pages']) . "' />";
}

function thrive_settings_field_appr_url_lessons($options)
{
    if (!isset($options['appr_url_lessons'])) {
        $options['appr_url_lessons'] = "lessons";
    }

    echo "<input type='text' name='thrive_theme_options[appr_url_lessons]' id='thrive_theme_options_appr_url_lessons' value='" . esc_attr($options['appr_url_lessons']) . "' />";
}

function thrive_settings_field_appr_url_categories($options)
{
    if (!isset($options['appr_url_categories'])) {
        $options['appr_url_categories'] = "apprentice";
    }

    echo "<input type='text' name='thrive_theme_options[appr_url_categories]' id='thrive_theme_options_appr_url_categories' value='" . esc_attr($options['appr_url_categories']) . "' />";
}

function thrive_settings_field_appr_url_tags($options)
{
    if (!isset($options['appr_url_tags'])) {
        $options['appr_url_tags'] = "apprentice-tag";
    }

    echo "<input type='text' name='thrive_theme_options[appr_url_tags]' id='thrive_theme_options_appr_url_tags' value='" . esc_attr($options['appr_url_tags']) . "' />";
}

function thrive_settings_field_appr_meta_author_name($options)
{
    $options = thrive_get_theme_options();
    $checked_on = ($options['appr_meta_author_name'] == '1') ? "checked" : "";
    $checked_off = ($options['appr_meta_author_name'] == '0') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='appr_author_name_on' type='radio' name='thrive_theme_options[appr_meta_author_name]' value='1' $checked_on />
    <label for='appr_author_name_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='appr_author_name_off' type='radio' name='thrive_theme_options[appr_meta_author_name]' value='0' $checked_off />
    <label for='appr_author_name_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_appr_meta_post_date($options)
{
    $checked_on = ($options['appr_meta_post_date'] == '1') ? "checked" : "";
    $checked_off = ($options['appr_meta_post_date'] == '0') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='appr_post_date_on' type='radio' name='thrive_theme_options[appr_meta_post_date]' value='1' $checked_on />
    <label for='appr_post_date_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='appr_post_date_off' type='radio' name='thrive_theme_options[appr_meta_post_date]' value='0' $checked_off />
    <label for='appr_post_date_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_appr_meta_post_category($options)
{
    $checked_on = ($options['appr_meta_post_category'] == '1') ? "checked" : "";
    $checked_off = ($options['appr_meta_post_category'] == '0') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='appr_post_category_on' type='radio' name='thrive_theme_options[appr_meta_post_category]' value='1' $checked_on />
    <label for='appr_post_category_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='appr_post_category_off' type='radio' name='thrive_theme_options[appr_meta_post_category]' value='0' $checked_off />
    <label for='appr_post_category_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_appr_meta_post_tags($options)
{
    $checked_on = ($options['appr_meta_post_tags'] == '1') ? "checked" : "";
    $checked_off = ($options['appr_meta_post_tags'] == '0') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='appr_post_tags_on' type='radio' name='thrive_theme_options[appr_meta_post_tags]' value='1' $checked_on />
    <label for='appr_post_tags_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='appr_post_tags_off' type='radio' name='thrive_theme_options[appr_meta_post_tags]' value='0' $checked_off />
    <label for='appr_post_tags_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_appr_meta_comment_count($options)
{
    $checked_on = ($options['appr_meta_comment_count'] == '1') ? "checked" : "";
    $checked_off = ($options['appr_meta_comment_count'] == '0') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='appr_comment_count_on' type='radio' name='thrive_theme_options[appr_meta_comment_count]' value='1' $checked_on />
    <label for='appr_comment_count_on' class='btn'>On</label>";
    echo "<input class='toggle toggle-right' id='appr_comment_count_off' type='radio' name='thrive_theme_options[appr_meta_comment_count]' value='0' $checked_off />
    <label for='appr_comment_count_off' class='btn'>Off</label>";
}

function thrive_settings_field_appr_bottom_about_author($options)
{
    $options = thrive_get_theme_options();
    $checked_on = ($options['appr_bottom_about_author'] == '1') ? "checked" : "";
    $checked_off = ($options['appr_bottom_about_author'] == '0') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='appr_about_author_on' type='radio' name='thrive_theme_options[appr_bottom_about_author]' value='1' $checked_on />
    <label for='appr_about_author_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='appr_about_author_off' type='radio' name='thrive_theme_options[appr_bottom_about_author]' value='0' $checked_off />
    <label for='appr_about_author_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}


function thrive_settings_field_appr_bottom_previous_next($options)
{
    $checked_on = ($options['appr_bottom_previous_next'] == '1') ? "checked" : "";
    $checked_off = ($options['appr_bottom_previous_next'] == '0') ? "checked" : "";
    echo "<input class='toggle toggle-left' id='appr_bottom_prev_next_on' type='radio' name='thrive_theme_options[appr_bottom_previous_next]' value='1' $checked_on />
    <label for='appr_bottom_prev_next_on' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right' id='appr_bottom_prev_next_off' type='radio' name='thrive_theme_options[appr_bottom_previous_next]' value='0' $checked_off />
    <label for='appr_bottom_prev_next_off' class='btn'>" . __('Off', 'thrive') . "</label>";
}

?>
