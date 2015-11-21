<?php

function _thrive_get_main_content_class($options = null) {
    if (!$options) {
        $options = thrive_get_theme_options();
    }
    $main_content_class = "fullWidth";
    if ($options['sidebar_alignement'] == "right") {
        $main_content_class = "left";
    } elseif ($options['sidebar_alignement'] == "left") {
        $main_content_class = "right";
    }
    if (is_page()) {
        $sidebar_is_active = is_active_sidebar('sidebar-2');
    } else {
        $sidebar_is_active = is_active_sidebar('sidebar-1');
    }
    if (_thrive_check_is_woocommerce_page()) {
        $sidebar_is_active = is_active_sidebar('sidebar-woo');
    }
    if (!is_page() && $options['blog_post_layout'] === 'full_width') {
        $sidebar_is_active = false;
    }
    if (!$sidebar_is_active) {
        $main_content_class = "fullWidth";
    }

    if (!is_page() && $options['blog_post_layout'] === 'narrow') {
        $main_content_class = 'bpd';
    }

    return $main_content_class;
}

function _thrive_get_author_info($author_id = 0) {
    if ($author_id == 0) {
        if (is_single() || is_page()) {
            global $post;
            $author_id = $post->post_author;
        } elseif (is_author()) {
            $author_id = get_the_author_meta('ID');
        }
    }
    $user_info = get_userdata($author_id);
    if (!$user_info) {
        return false;
    }
    $social_links = (array("twitter" => get_the_author_meta('twitter', $author_id),
        "fb" => get_the_author_meta('facebook', $author_id),
        "g_plus" => get_the_author_meta('gplus', $author_id)));

    return array('avatar' => get_avatar($user_info->user_email, 125),
        'display_name' => (!empty($user_info->first_name) || !empty($user_info->last_name)) ? $user_info->first_name . " " . $user_info->last_name : $user_info->display_name,
        'description' => $user_info->description,
        'social_links' => $social_links,
        'posts_url' => get_author_posts_url($author_id),
        'author_website' => get_the_author_meta('thrive_author_website', $author_id)
    );
}

function _thrive_get_featured_image_src($postId = null, $params = array()) {
    if (!$postId) {
        $postId = get_the_ID();
    }
    if (!isset($params['size']) || empty($params['size'])) {
        $params['size'] = "medium";
    }
    $featuredImage = null;
    if (has_post_thumbnail($postId)) {
        $featuredImage = wp_get_attachment_image_src(get_post_thumbnail_id($postId), $params['size']);
    }
    if ($featuredImage && isset($featuredImage[0])) {
        return $featuredImage[0];
    }
    if (isset($params['default']) && $params['default']) {
        return get_template_directory_uri() . "/images/default_featured.jpg";
    }
    return false;
}

function _thrive_get_footer_col_class($num_cols) {
    $f_class = "";
    switch ($num_cols) {
        case 0:
            return "";
        case 1:
            return "";
        case 2:
            return "colm twc";
        case 3:
            return "colm oth";
    }
    return $f_class;
}

function _thrive_get_footer_active_widget_areas($appr = "") {
    $num = 0;
    $active_footers = array();
    while ($num < 4) {
        $num++;
        if (is_active_sidebar('footer-' . $appr . $num)) {
            array_push($active_footers, 'footer-' . $appr . $num);
        }
    }
    return $active_footers;
}

function _thrive_render_bottom_related_posts($postId, $options = null) {
    if (!$postId || !is_single()) {
        return false;
    }
    if (!$options) {
        $options = thrive_get_options_for_post($postId);
    }
    if ($options['related_posts_box'] != 1) {
        return false;
    }
    $postType = get_post_type($postId);
    if ($postType != "post") {
        return false;
    }

    if (thrive_get_theme_options('related_posts_enabled') == 1) {
        $relatedPosts = _thrive_get_related_posts($postId, 'array', $options['related_posts_number']);
    } else {
        $relatedPosts = get_posts(array('category__in' => wp_get_post_categories($postId),
            'numberposts' => $options['related_posts_number'],
            'post__not_in' => array($postId)));
    }

    require get_template_directory() . '/partials/bottom-related-posts.php';
}

add_action('tha_head_top', 'thrive_include_meta_post_tags');

function thrive_include_meta_post_tags() {

    if (_thrive_check_is_woocommerce_page()) {
        return false;
    }

    $theme_options = thrive_get_options_for_post();

    if (!isset($theme_options['social_site_meta_enable']) || $theme_options['social_site_meta_enable'] === NULL  || $theme_options['social_site_meta_enable'] == "") {
        $theme_options['social_site_meta_enable'] = _thrive_get_social_site_meta_enable_default_value();
    }

    if ($theme_options['social_site_meta_enable'] != 1) {
        return false;
    }

    if (is_single() || is_page()) {
        $plugin_file_path = thrive_get_wp_admin_dir() . "/includes/plugin.php";        
        include_once($plugin_file_path);
        if (is_plugin_active('wordpress-seo/wp-seo.php')) {
            if ((!isset($theme_options['social_site_title']) || $theme_options['social_site_title'] == '') &&
                    (!isset($theme_options['social_site_image']) || $theme_options['social_site_image'] == '') &&
                    (!isset($theme_options['social_site_description']) || $theme_options['social_site_description'] == '') &&
                    (!isset($theme_options['social_site_twitter_username']) || $theme_options['social_site_twitter_username'] == '')) {
                return;
            } else {
                thrive_remove_yoast_meta_description();
            }
        }

        $page_type = 'article';
        if (!isset($theme_options['social_site_title']) || $theme_options['social_site_title'] == '') {
            $theme_options['social_site_title'] = get_the_title();
        }
        if (!isset($theme_options['social_site_image']) || $theme_options['social_site_image'] == '') {
            if (has_post_thumbnail(get_the_ID())) {
                $featured_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()));
                if ($featured_image && isset($featured_image[0])) {
                    $theme_options['social_site_image'] = $featured_image[0];
                }
            }
        }
        if (!isset($theme_options['social_site_description']) || $theme_options['social_site_description'] == '') {
            $post = get_post();
            $content = strip_shortcodes($post->post_content);
            $content = strip_tags($content);
            $content = preg_replace("/\s+/", " ", $content);
            $content = str_replace('&nbsp;', ' ', $content);

            $first_dot = strpos($content, '.') !== FALSE ? strpos($content, '.') : strlen($content);
            $first_question = strpos($content, '.') !== FALSE ? strpos($content, '.') : strlen($content);
            $first_exclamation = strpos($content, '.') !== FALSE ? strpos($content, '.') : strlen($content);

            $fist_sentence = min($first_dot, $first_exclamation, $first_question);
            $content = substr($content, 0, intval($fist_sentence) + 1);
            $theme_options['social_site_description'] = addslashes($content);
        }
    } else {
        $page_type = 'website';
    }
    $current_url = get_permalink();

    $meta = array(
        //uniqueID => meta
        'og:type' => array(
            //attribute -> value
            'property' => 'og:type',
            'content' => $page_type,
        ),
        'og:url' => array(
            'property' => 'og:url',
            'content' => $current_url,
        ),
        'twitter:card' => array(
            'name' => 'twitter:card',
            'content' => 'summary_large_image'
        ),
    );

    if (isset($theme_options['social_site_name']) && $theme_options['social_site_name'] != '') {
        $meta['og:site_name'] = array(
            'property' => 'og:site_name',
            'content' => str_replace('"', "'", $theme_options['social_site_name'])
        );
    }
    if (isset($theme_options['social_site_title']) && $theme_options['social_site_title'] != '') {
        $meta['og:title'] = array (
            'property' => 'og:title',
            'content' => str_replace('"', "'", $theme_options['social_site_title']),
        );
        $meta['twitter:title'] = array (
            'name' => 'twitter:title',
            'content' => str_replace('"', "'", $theme_options['social_site_title'])
        );
    }
    if (isset($theme_options['social_site_image']) && $theme_options['social_site_image'] != '') {
        $meta['og:image'] = array (
            'property' => 'og:image',
            'content' => str_replace('"', "'", $theme_options['social_site_image']),
        );
        $meta['twitter:image:src'] = array(
            'name' => 'twitter:image:src',
            'content' => str_replace('"', "'", $theme_options['social_site_image'])
        );

    }
    if (isset($theme_options['social_site_description']) && $theme_options['social_site_description'] != '') {
        $meta['og:description'] = array(
            'property' => 'og:description',
            'content' => str_replace('"', "'", $theme_options['social_site_description'])
        );
        $meta['twitter:description'] = array(
            'name' => 'twitter:description',
            'content' => str_replace('"', "'", $theme_options['social_site_description'])
        );
    }
    if (isset($theme_options['social_site_twitter_username']) && $theme_options['social_site_twitter_username'] != '') {
        $meta['twitter:creator'] = array(
            'name' => 'twitter:creator',
            'content' => '@' . str_replace('"', "'", $theme_options['social_site_twitter_username'])
        );
        $meta['twitter:site'] = array(
            'name' => 'twitter:site',
            'content' => '@' . str_replace('"', "'", $theme_options['social_site_twitter_username'])
        );
    }

    $meta = apply_filters('tha_social_meta', $meta);

    if(empty($meta)) {
        return;
    }
    echo "\n";
    //display all the meta
    foreach($meta as $uniquekey => $attributes) {
        if(empty($attributes) || !is_array($attributes)) {
            continue;
        }
        echo "<meta ";
        foreach($attributes as $attr_name => $attr_value) {
            echo $attr_name . '="' . $attr_value . '" ';
        }
        echo "/>\n";
    }
    echo "\n";
}

function thrive_remove_yoast_meta_description() {
    if (has_action('wpseo_head')) {
        if (isset($GLOBALS['wpseo_og'])) {
            remove_action('wpseo_head', array($GLOBALS['wpseo_og'], 'opengraph'), 30);
        }
        remove_action('wpseo_head', array('WPSEO_Twitter', 'get_instance'), 40);
        remove_action('wpseo_head', array('WPSEO_GooglePlus', 'get_instance'), 35);
    }
}

function thrive_get_wp_admin_dir(){
    $wp_include_dir = preg_replace('/wp-content$/', 'wp-admin', WP_CONTENT_DIR);
    return $wp_include_dir;
}

function _thrive_check_focus_area_for_pages($page, $position = "top") {
    if (!$page) {
        return false;
    }
    if ($page == "blog" && !is_home()) {
        return false;
    }

    if ($page == "blog") {
        $query = new WP_Query("post_type=focus_area&meta_key=_thrive_meta_focus_page_blog&meta_value=blog&order=ASC");
    } elseif ($page == "archive") {
        $query = new WP_Query("post_type=focus_area&meta_key=_thrive_meta_focus_page_archive&meta_value=archive&order=ASC");
    }

    $focus_areas = $query->get_posts();

    foreach ($focus_areas as $focus_area) {
        $post_custom_atr = get_post_custom($focus_area->ID);
        if (isset($post_custom_atr['_thrive_meta_focus_display_location'])
            && isset($post_custom_atr['_thrive_meta_focus_display_location'][0])
            && $post_custom_atr['_thrive_meta_focus_display_location'][0] == $position) {
            return true;
        }
    }

    return false;
}

function _thrive_is_active_sidebar($options = null) {
    if (_thrive_check_is_woocommerce_page()) {
        return is_active_sidebar('sidebar-woo');
    }
    if (!$options) {
        $options = thrive_get_theme_options();
    }
    if (is_singular()) {
        $post_template = _thrive_get_item_template(get_the_ID());
        if ($post_template == "Narrow" || $post_template == "Full Width" || $post_template == "Landing Page") {
            return false;
        }
    }
    if (is_page()) {
        $sidebar_is_active = is_active_sidebar('sidebar-2');
    } else {
        $sidebar_is_active = is_active_sidebar('sidebar-1');
    }
    if (is_singular()) {
        return $sidebar_is_active;
    }
    if ($options['blog_post_layout'] == "full_width" || $options['blog_post_layout'] == "narrow" || !$sidebar_is_active) {
        return false;
    }
    return true;
}
