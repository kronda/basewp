<?php

add_action('add_meta_boxes', 'thrive_appr_add_custom_fields');
add_action('save_post', 'thrive_save_appr_postdata');

function thrive_appr_add_custom_fields() {
    add_meta_box('thrive_page_options', __('Thrive Theme Options', 'thrive'), 'thrive_meta_post_options', TT_APPR_POST_TYPE_PAGE);
    add_meta_box('thrive_page_options', __('Thrive Theme Options', 'thrive'), 'thrive_meta_post_options', TT_APPR_POST_TYPE_LESSON);
    add_meta_box('thrive_focus_options', __('Apprentice Options', 'thrive'), 'thrive_meta_apprentice_lesson_options', TT_APPR_POST_TYPE_LESSON, "advanced", "high");
    add_meta_box('thrive_appr_select_category', __('Apprentice Category', 'thrive'), 'thrive_appr_meta_select_category', TT_APPR_POST_TYPE_LESSON, "side");
}

function thrive_save_appr_postdata($post_id) {

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (!isset($_POST['post_type'])) {
        return;
    }

    if (TT_APPR_POST_TYPE_LESSON == $_POST['post_type']) {
        _thrive_save_page_options($_POST);
        _thrive_save_apprentice_lesson_options($_POST);
    } elseif (TT_APPR_POST_TYPE_PAGE == $_POST['post_type']) {
        _thrive_save_focus_options($_POST);
    }
}

function thrive_appr_meta_select_category($post) {
    $catObject = _thrive_appr_get_category_object();

    $lessonsLevel = thrive_appr_get_lessons_level();
    $lessonsLevelCats = $catObject['lessonsLevelCats'];
    $firstParentCats = $catObject['firstParentCats'];
    $categories = wp_get_post_terms($post->ID, "apprentice");
    $currentCat = (isset($categories[0])) ? $categories[0] : null;

    require ( get_template_directory() . "/inc/apprentice/templates/post-select-category.php" );
}

function thrive_meta_apprentice_lesson_options($post) {

    $lesson_types = array('text' => __("Text", 'thrive'),
        'video' => __("Video", 'thrive'),
        'audio' => __("Audio", 'thrive'),
    );

    $icon_types = array('document' => __("Document", 'thrive'),
        'video' => __("Video", 'thrive'),
        'audio' => __("Audio", 'thrive'),
        'link' => __("Link", 'thrive'),
        'download' => __("Download", 'thrive')
    );

    $value_appr_lesson_type = get_post_meta($post->ID, '_thrive_meta_appr_lesson_type', true);
    $downloadLinksJson = get_post_meta($post->ID, '_thrive_meta_appr_download_links', true);
    $downloadLinksArray = json_decode($downloadLinksJson, true);
    if (!is_array($downloadLinksArray)) {
        $downloadLinksArray = array();
    }

    $thrive_meta_appr_video_type = get_post_meta($post->ID, '_thrive_meta_appr_video_type', true);
    $thrive_meta_appr_video_youtube_url = get_post_meta($post->ID, '_thrive_meta_appr_video_youtube_url', true);
    $thrive_meta_appr_video_youtube_hide_related = get_post_meta($post->ID, '_thrive_meta_appr_video_youtube_hide_related', true);
    $thrive_meta_appr_video_youtube_hide_logo = get_post_meta($post->ID, '_thrive_meta_appr_video_youtube_hide_logo', true);
    $thrive_meta_appr_video_youtube_hide_controls = get_post_meta($post->ID, '_thrive_meta_appr_video_youtube_hide_controls', true);
    $thrive_meta_appr_video_youtube_hide_title = get_post_meta($post->ID, '_thrive_meta_appr_video_youtube_hide_title', true);
    $thrive_meta_appr_video_youtube_autoplay = get_post_meta($post->ID, '_thrive_meta_appr_video_youtube_autoplay', true);
    $thrive_meta_appr_video_youtube_hide_fullscreen = get_post_meta($post->ID, '_thrive_meta_appr_video_youtube_hide_fullscreen', true);
    $thrive_meta_appr_video_vimeo_url = get_post_meta($post->ID, '_thrive_meta_appr_video_vimeo_url', true);
    $thrive_meta_appr_video_custom_url = get_post_meta($post->ID, '_thrive_meta_appr_video_custom_url', true);
    $thrive_meta_appr_video_custom_embed = get_post_meta($post->ID, '_thrive_meta_appr_video_custom_embed', true);
    $thrive_meta_appr_quote_text = get_post_meta($post->ID, '_thrive_meta_appr_quote_text', true);
    $thrive_meta_appr_quote_author = get_post_meta($post->ID, '_thrive_meta_appr_quote_author', true);
    $thrive_meta_appr_audio_type = get_post_meta($post->ID, '_thrive_meta_appr_audio_type', true);
    $thrive_meta_appr_audio_file = get_post_meta($post->ID, '_thrive_meta_appr_audio_file', true);
    $thrive_meta_appr_audio_soundcloud_url = get_post_meta($post->ID, '_thrive_meta_appr_audio_soundcloud_url', true);
    $thrive_meta_appr_audio_soundcloud_autoplay = get_post_meta($post->ID, '_thrive_meta_appr_audio_soundcloud_autoplay', true);
    $thrive_meta_appr_gallery_images = get_post_meta($post->ID, '_thrive_meta_appr_gallery_images', true);

    require ( get_template_directory() . "/inc/apprentice/templates/admin-appr-lesson-options.php" );
}

function _thrive_save_apprentice_lesson_options($post_data) {
    global $wpdb;
    $post_id = $post_data['post_ID'];

    //update the apprentice category
    $apprCategory = (isset($post_data['thrive_appr_lesson_cat'])) ? (int) $post_data['thrive_appr_lesson_cat'] : 0;
    if ($apprCategory > 0) {        
        //if the category is changed, update the lessons order option
        $previousCategories = wp_get_post_terms($post_id, "apprentice");
        $previousCat = (isset($previousCategories[0])) ? $previousCategories[0] : null;
        $previousCatId = ($previousCat) ? $previousCat->term_id : 0;
                
        if ($apprCategory != $previousCatId) {

            $lessonsOrder = json_decode(get_option("thrive_appr_lessons_order"), true);
            
            if (!$lessonsOrder || empty($lessonsOrder)) {
                $lessonsOrder = array($apprCategory => array($post_id));
            }
            
            if (!isset($lessonsOrder[$apprCategory])) {
                $lessonsOrder[$apprCategory] = array();
            }
            
            foreach ($lessonsOrder as $catIdKey => $pIds) {
                if ($catIdKey == $previousCatId) {
                    if (($tempKey = array_search($post_id, $pIds)) !== false) {
                        unset($pIds[$tempKey]);
                    }
                }
                if ($catIdKey == $apprCategory && !in_array($post_id, $lessonsOrder[$catIdKey])) {                                                    
                    array_push($lessonsOrder[$catIdKey], (int) $post_id);
                }
            }
            
            
            update_option("thrive_appr_lessons_order", json_encode($lessonsOrder));
        }
        
        wp_set_post_terms($post_id, $apprCategory, "apprentice");
    }

    $checkbox_fields = array('thrive_meta_appr_video_youtube_hide_related',
        'thrive_meta_appr_video_youtube_hide_logo',
        'thrive_meta_appr_video_youtube_hide_controls',
        'thrive_meta_appr_video_youtube_hide_title',
        'thrive_meta_appr_video_youtube_autoplay',
        'thrive_meta_appr_video_youtube_hide_fullscreen',
        'thrive_meta_appr_audio_soundcloud_autoplay');
    foreach($checkbox_fields as $field) {
        if(!isset($post_data[$field])) {
            $post_data[$field] = 0;
        }
    }

    $checkList = array('thrive_meta_appr_lesson_type', 'thrive_meta_appr_download_links', 'thrive_meta_appr_video_type', 'thrive_meta_appr_video_youtube_url', 'thrive_meta_appr_video_vimeo_url',
        'thrive_meta_appr_video_custom_url', 'thrive_meta_appr_video_custom_embed', 'thrive_meta_appr_quote_text', 'thrive_meta_appr_quote_author', 'thrive_meta_appr_audio_type',
        'thrive_meta_appr_audio_file', 'thrive_meta_appr_audio_soundcloud_url', 'thrive_meta_appr_audio_soundcloud_autoplay', 'thrive_meta_appr_gallery_images');
    foreach ($checkList as $checkKey) {
        $post_data[$checkKey] = isset($post_data[$checkKey]) ? $post_data[$checkKey] : "";
    }

    $thrive_meta_appr_lesson_type = sanitize_text_field($post_data['thrive_meta_appr_lesson_type']);
    $thrive_meta_appr_download_links = $post_data['thrive_meta_appr_download_links'];
    $thrive_meta_appr_download_links_json = $thrive_meta_appr_download_links;

    $thrive_meta_appr_video_type = ($post_data['thrive_meta_appr_video_type']);
    $thrive_meta_appr_video_youtube_url = ($post_data['thrive_meta_appr_video_youtube_url']);
    $thrive_meta_appr_video_youtube_hide_related = ($post_data['thrive_meta_appr_video_youtube_hide_related']);
    $thrive_meta_appr_video_youtube_hide_logo = ($post_data['thrive_meta_appr_video_youtube_hide_logo']);
    $thrive_meta_appr_video_youtube_hide_controls = ($post_data['thrive_meta_appr_video_youtube_hide_controls']);
    $thrive_meta_appr_video_youtube_hide_title = ($post_data['thrive_meta_appr_video_youtube_hide_title']);
    $thrive_meta_appr_video_youtube_autoplay = ($post_data['thrive_meta_appr_video_youtube_autoplay']);
    $thrive_meta_appr_video_youtube_hide_fullscreen = ($post_data['thrive_meta_appr_video_youtube_hide_fullscreen']);
    $thrive_meta_appr_video_vimeo_url = ($post_data['thrive_meta_appr_video_vimeo_url']);
    $thrive_meta_appr_video_custom_url = ($post_data['thrive_meta_appr_video_custom_url']);
    $thrive_meta_appr_video_custom_embed = ($post_data['thrive_meta_appr_video_custom_embed']);
    $thrive_meta_appr_quote_text = ($post_data['thrive_meta_appr_quote_text']);
    $thrive_meta_appr_quote_author = ($post_data['thrive_meta_appr_quote_author']);
    $thrive_meta_appr_audio_type = ($post_data['thrive_meta_appr_audio_type']);
    $thrive_meta_appr_audio_file = ($post_data['thrive_meta_appr_audio_file']);
    $thrive_meta_appr_audio_soundcloud_url = ($post_data['thrive_meta_appr_audio_soundcloud_url']);
    $thrive_meta_appr_audio_soundcloud_autoplay = ($post_data['thrive_meta_appr_audio_soundcloud_autoplay']);
    $thrive_meta_appr_gallery_images = ($post_data['thrive_meta_appr_gallery_images']);

    add_post_meta($post_id, '_thrive_meta_appr_lesson_type', $thrive_meta_appr_lesson_type, true) or
            update_post_meta($post_id, '_thrive_meta_appr_lesson_type', $thrive_meta_appr_lesson_type);

    add_post_meta($post_id, '_thrive_meta_appr_download_links', $thrive_meta_appr_download_links_json, true) or
            update_post_meta($post_id, '_thrive_meta_appr_download_links', $thrive_meta_appr_download_links_json);

    add_post_meta($post_id, '_thrive_meta_appr_video_type', $thrive_meta_appr_video_type, true) or
            update_post_meta($post_id, '_thrive_meta_appr_video_type', $thrive_meta_appr_video_type);
    add_post_meta($post_id, '_thrive_meta_appr_video_youtube_url', $thrive_meta_appr_video_youtube_url, true) or
            update_post_meta($post_id, '_thrive_meta_appr_video_youtube_url', $thrive_meta_appr_video_youtube_url);
    add_post_meta($post_id, '_thrive_meta_appr_video_youtube_hide_related', $thrive_meta_appr_video_youtube_hide_related, true) or
            update_post_meta($post_id, '_thrive_meta_appr_video_youtube_hide_related', $thrive_meta_appr_video_youtube_hide_related);
    add_post_meta($post_id, '_thrive_meta_appr_video_youtube_hide_logo', $thrive_meta_appr_video_youtube_hide_logo, true) or
            update_post_meta($post_id, '_thrive_meta_appr_video_youtube_hide_logo', $thrive_meta_appr_video_youtube_hide_logo);
    add_post_meta($post_id, '_thrive_meta_appr_video_youtube_hide_controls', $thrive_meta_appr_video_youtube_hide_controls, true) or
            update_post_meta($post_id, '_thrive_meta_appr_video_youtube_hide_controls', $thrive_meta_appr_video_youtube_hide_controls);
    add_post_meta($post_id, '_thrive_meta_appr_video_youtube_hide_title', $thrive_meta_appr_video_youtube_hide_title, true) or
            update_post_meta($post_id, '_thrive_meta_appr_video_youtube_hide_title', $thrive_meta_appr_video_youtube_hide_title);
    add_post_meta($post_id, '_thrive_meta_appr_video_youtube_autoplay', $thrive_meta_appr_video_youtube_autoplay, true) or
            update_post_meta($post_id, '_thrive_meta_appr_video_youtube_autoplay', $thrive_meta_appr_video_youtube_autoplay);
    add_post_meta($post_id, '_thrive_meta_appr_video_youtube_hide_fullscreen', $thrive_meta_appr_video_youtube_hide_fullscreen, true) or
            update_post_meta($post_id, '_thrive_meta_appr_video_youtube_hide_fullscreen', $thrive_meta_appr_video_youtube_hide_fullscreen);
    add_post_meta($post_id, '_thrive_meta_appr_video_vimeo_url', $thrive_meta_appr_video_vimeo_url, true) or
            update_post_meta($post_id, '_thrive_meta_appr_video_vimeo_url', $thrive_meta_appr_video_vimeo_url);
    add_post_meta($post_id, '_thrive_meta_appr_video_custom_url', $thrive_meta_appr_video_custom_url, true) or
            update_post_meta($post_id, '_thrive_meta_appr_video_custom_url', $thrive_meta_appr_video_custom_url);
    add_post_meta($post_id, '_thrive_meta_appr_video_custom_embed', $thrive_meta_appr_video_custom_embed, true) or
            update_post_meta($post_id, '_thrive_meta_appr_video_custom_embed', $thrive_meta_appr_video_custom_embed);
    add_post_meta($post_id, '_thrive_meta_appr_quote_text', $thrive_meta_appr_quote_text, true) or
            update_post_meta($post_id, '_thrive_meta_appr_quote_text', $thrive_meta_appr_quote_text);
    add_post_meta($post_id, '_thrive_meta_appr_quote_author', $thrive_meta_appr_quote_author, true) or
            update_post_meta($post_id, '_thrive_meta_appr_quote_author', $thrive_meta_appr_quote_author);
    add_post_meta($post_id, '_thrive_meta_appr_audio_type', $thrive_meta_appr_audio_type, true) or
            update_post_meta($post_id, '_thrive_meta_appr_audio_type', $thrive_meta_appr_audio_type);
    add_post_meta($post_id, '_thrive_meta_appr_audio_file', $thrive_meta_appr_audio_file, true) or
            update_post_meta($post_id, '_thrive_meta_appr_audio_file', $thrive_meta_appr_audio_file);
    add_post_meta($post_id, '_thrive_meta_appr_audio_soundcloud_url', $thrive_meta_appr_audio_soundcloud_url, true) or
            update_post_meta($post_id, '_thrive_meta_appr_audio_soundcloud_url', $thrive_meta_appr_audio_soundcloud_url);
    add_post_meta($post_id, '_thrive_meta_appr_audio_soundcloud_autoplay', $thrive_meta_appr_audio_soundcloud_autoplay, true) or
            update_post_meta($post_id, '_thrive_meta_appr_audio_soundcloud_autoplay', $thrive_meta_appr_audio_soundcloud_autoplay);
    add_post_meta($post_id, '_thrive_meta_appr_gallery_images', $thrive_meta_appr_gallery_images, true) or
            update_post_meta($post_id, '_thrive_meta_appr_gallery_images', $thrive_meta_appr_gallery_images);

    //get and save the soundcloud embed code
    if (!empty($thrive_meta_appr_audio_soundcloud_url)) {
        $soundcloudParams = array('url' => $thrive_meta_appr_audio_soundcloud_url,
            'auto_play' => ($thrive_meta_appr_audio_soundcloud_autoplay == 1) ? "true" : "false",
            'format' => 'json');
        if (!class_exists('ThriveSoundcloud')) {
            include get_template_directory() . '/inc/apprentice/libs/ThriveSoundcloud.php';
        }
        $thriveSoundcloud = new ThriveSoundcloud();
        $response = $thriveSoundcloud->url($soundcloudParams);

        if ($response && isset($response->html)) {
            add_post_meta($post_id, '_thrive_meta_appr_audio_soundcloud_embed_code', $response->html, true) or
                    update_post_meta($post_id, '_thrive_meta_appr_audio_soundcloud_embed_code', $response->html);
        }
    }
}

?>
