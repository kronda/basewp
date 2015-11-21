<?php

define('THRIVE_APPR_FAV_META_KEY', "thrive_appr_favorites");
define('THRIVE_APPR_FAV_POST_META_KEY', "thrive_appr_favorites_no");
define('THRIVE_APPR_PROGRESS_META_KEY', "thrive_appr_progress");
define('THRIVE_APPR_PROGRESS_NEW', "new");
define('THRIVE_APPR_PROGRESS_STARTED', "started");
define('THRIVE_APPR_PROGRESS_COMPLETED', "completed");

function _thrive_appr_set_progress($post_id, $user_id = 0, $status = THRIVE_APPR_PROGRESS_NEW)
{
    if (!$user_id) {
        if (is_user_logged_in()) {
            global $current_user;
            get_currentuserinfo();
            $user_id = $current_user->ID;
        }
    }
    if (!$post_id || !$user_id) {
        return false;
    }

    $thrive_progress = get_user_meta($user_id, THRIVE_APPR_PROGRESS_META_KEY, true);
    if (!is_array($thrive_progress)) {
        $thrive_progress = array();
    }
    $thrive_progress[$post_id] = $status;

    update_user_meta($user_id, THRIVE_APPR_PROGRESS_META_KEY, $thrive_progress);
}

function _thrive_appr_get_progress($post_id, $user_id = 0)
{
    if (!$user_id) {
        if (is_user_logged_in()) {
            global $current_user;
            get_currentuserinfo();
            $user_id = $current_user->ID;
        }
    }
    if (!$post_id || !$user_id) {
        return false;
    }
    $thrive_progress = get_user_meta($user_id, THRIVE_APPR_PROGRESS_META_KEY, true);
    if (!is_array($thrive_progress)) {
        return THRIVE_APPR_PROGRESS_NEW;
    }
    if (isset($thrive_progress[$post_id])) {
        return $thrive_progress[$post_id];
    }
    return THRIVE_APPR_PROGRESS_NEW;
}

function _thrive_appr_add_favorite($post_id, $user_id)
{
    if (!$post_id || !$user_id) {
        return false;
    }

    $thrive_favorites = get_user_meta($user_id, THRIVE_APPR_FAV_META_KEY, true);
    if (!is_array($thrive_favorites)) {
        $thrive_favorites = array();
    }
    $thrive_favorites[] = $post_id;

    update_user_meta($user_id, THRIVE_APPR_FAV_META_KEY, $thrive_favorites);
    //update the post favorite counter
    $current_post_fav_no = (int)get_post_meta($post_id, THRIVE_APPR_FAV_POST_META_KEY, true);
    if ($current_post_fav_no && !empty($current_post_fav_no)) {
        $current_post_fav_no++;
    } else {
        $current_post_fav_no = 0;
    }
    update_post_meta($post_id, THRIVE_APPR_FAV_POST_META_KEY, $current_post_fav_no);
}

function _thrive_appr_remove_favorite($post_id, $user_id)
{
    if (!$post_id || !$user_id) {
        return false;
    }

    $thrive_favorites = get_user_meta($user_id, THRIVE_APPR_FAV_META_KEY, true);
    $thrive_favorites = array_diff($thrive_favorites, array($post_id));
    $thrive_favorites = array_values($thrive_favorites);

    update_user_meta($user_id, THRIVE_APPR_FAV_META_KEY, $thrive_favorites);

    //update the post favorite counter
    $current_post_fav_no = (int)get_post_meta($post_id, THRIVE_APPR_FAV_POST_META_KEY, true);
    if ($current_post_fav_no && !empty($current_post_fav_no)) {
        if ($current_post_fav_no <= 1) {
            $current_post_fav_no = 0;
        } else {
            $current_post_fav_no--;
        }
    } else {
        $current_post_fav_no = 0;
    }
    update_post_meta($post_id, THRIVE_APPR_FAV_POST_META_KEY, $current_post_fav_no);
}

function _thrive_appr_check_favorite($post_id, $user_id = 0)
{
    if (!$user_id) {
        if (is_user_logged_in()) {
            global $current_user;
            get_currentuserinfo();
            $user_id = $current_user->ID;
        }
    }
    if (!$post_id || !$user_id) {
        return false;
    }
    $thrive_favorites = get_user_meta($user_id, THRIVE_APPR_FAV_META_KEY, true);
    if (!is_array($thrive_favorites)) {
        return false;
    }
    if (in_array($post_id, $thrive_favorites)) {
        return true;
    }
    return false;
}

/*
 * Function to retreive the category tree for the apprentice courses/modules/lessons
 * Not implemented recursively as we won't go deeper than 3 levels  * 
 */

function _thrive_appr_get_category_object($parent = 0)
{

    $courses = get_terms("apprentice", 'hide_empty=0&parent=' . $parent);

    $catLevel = 0;
    if ($parent > 0) {
        $catLevel = _thrive_appr_get_cat_level($parent) + 1;
    }

    $queryPostsArgs = array(
        'post_type' => TT_APPR_POST_TYPE_LESSON,
        'posts_per_page' => -1,
        'suppress_filters' => true,
        'tax_query' => array(
            array(
                'taxonomy' => 'apprentice',
                'field' => 'term_id',
                'terms' => 0
            )
        )
    );

    $orderArray = json_decode(get_option("thrive_appr_lessons_order"), true);

    $tree_depth = 0;
    $temp_courses = array();
    $temp_lessonsLevelCats = array();
    $temp_firstParentCats = array();

    $lessonsLevel = thrive_appr_get_lessons_level() - $catLevel;

    foreach ($courses as $course) {
        $course = (array)$course;
        $course['order'] = (isset($orderArray[$course['term_id']]) && isset($orderArray[$course['term_id']]['order'])) ?
            $orderArray[$course['term_id']]['order'] : 0;
        if ($lessonsLevel == 1) {
            $queryPostsArgs['tax_query'][0]['terms'] = $course['term_id'];
            $queryCoursesPosts = new WP_Query($queryPostsArgs);
            $course['posts'] = _thrive_get_ordered_lessons($queryCoursesPosts->get_posts(), $course['term_id']);
            $temp_lessonsLevelCats[] = $course;
        }
        if ($lessonsLevel == 2) {
            $temp_firstParentCats[] = $course;
        }
        $temp_modules = array();
        if ($lessonsLevel > 1) {
            $modules = get_terms("apprentice", 'hide_empty=0&parent=' . $course['term_id']);
            foreach ($modules as $module) {
                $module = (array)$module;

                $module['order'] = (isset($orderArray[$module['term_id']]) && isset($orderArray[$module['term_id']]['order'])) ?
                    $orderArray[$module['term_id']]['order'] : 0;
                if ($lessonsLevel == 2) {
                    $queryPostsArgs['tax_query'][0]['terms'] = $module['term_id'];
                    $queryModulesPosts = new WP_Query($queryPostsArgs);
                    $module['posts'] = _thrive_get_ordered_lessons($queryModulesPosts->get_posts(), $module['term_id']);
                    $temp_lessonsLevelCats[] = $module;
                }
                if ($lessonsLevel == 3) {
                    $temp_firstParentCats[] = $module;
                }
                $temp_lessonCats = array();
                if ($lessonsLevel > 2) {
                    $lessonCats = get_terms("apprentice", 'hide_empty=0&parent=' . $module['term_id']);
                    foreach ($lessonCats as $cat) {
                        $cat = (array)$cat;
                        $cat['order'] = (isset($orderArray[$cat['term_id']]) && isset($orderArray[$cat['term_id']]['order'])) ?
                            $orderArray[$cat['term_id']]['order'] : 0;

                        $queryPostsArgs['tax_query'][0]['terms'] = $cat['term_id'];
                        $queryLessonCatsPosts = new WP_Query($queryPostsArgs);
                        $cat['posts'] = _thrive_get_ordered_lessons($queryLessonCatsPosts->get_posts(), $cat['term_id']);
                        array_push($temp_lessonCats, $cat);
                        $temp_lessonsLevelCats[] = $cat;
                    }
                }
                usort($temp_lessonCats, "_thrive_sort_by_order");
                $module['lessonCats'] = $temp_lessonCats;
                array_push($temp_modules, $module);
            }
        }
        usort($temp_modules, "_thrive_sort_by_order");
        $course['modules'] = $temp_modules;
        array_push($temp_courses, $course);
    }
    usort($temp_courses, "_thrive_sort_by_order");
    $result = array('depth' => $lessonsLevel,
        'courses' => $temp_courses,
        'lessonsLevelCats' => $temp_lessonsLevelCats,
        'firstParentCats' => $temp_firstParentCats
    );
    return $result;
}

function _thrive_appr_get_lessons($id_parent = 0)
{
    $queryPostsArgs = array(
        'posts_per_page' => -1,
        'post_type' => TT_APPR_POST_TYPE_LESSON,
        'tax_query' => array(
            array(
                'taxonomy' => 'apprentice',
                'field' => 'term_id',
                'terms' => $id_parent
            )
        )
    );

    $queryLessonCatsPosts = new WP_Query($queryPostsArgs);
    return _thrive_get_ordered_lessons($queryLessonCatsPosts->get_posts(), $id_parent);
}

function _thrive_appr_get_cat_level($catId)
{

    $currentTerm = get_term($catId, "apprentice");

    if ($currentTerm->parent == 0) {
        return 0;
    }
    $parentTerm = get_term($currentTerm->parent, "apprentice");
    if ($parentTerm->parent == 0) {
        return 1;
    }
    $parentTerm = get_term($parentTerm->parent, "apprentice");
    if ($parentTerm->parent == 0) {
        return 2;
    } //shouldn't be higher than 2
    return 0;
}

function _thrive_app_get_lesson_icon($type = "text")
{
    switch ($type) {
        case 'audio':
            return "";
            break;
        case 'video':
            return "";
            break;
        case 'gallery':
            return "";
            break;
        default:
            return "";
    }
    return "";
}

function _thrive_appr_get_list_class($cat, $lessonsLevel)
{

    if ($cat == "course") {
        if ($lessonsLevel == 1) {
            return "cat-list-3";
        }
        if ($lessonsLevel == 2) {
            return "cat-list-2";
        }
        if ($lessonsLevel == 3) {
            return "cat-list-1";
        }
    }

    if ($cat == "module") {
        if ($lessonsLevel == 2) {
            return "cat-list-3";
        }
        if ($lessonsLevel == 3) {
            return "cat-list-2";
        }
    }
    return "";
}

function _thrive_get_ordered_lessons($posts, $catId)
{
    $lessonsOrder = json_decode(get_option("thrive_appr_lessons_order"), true);

    if (!$lessonsOrder || empty($lessonsOrder)) {
        return $posts;
    }
    if (!isset($lessonsOrder[$catId])) {
        return $posts;
    }
    $orderedPosts = array();
    foreach ($lessonsOrder[$catId] as $pId) {
        foreach ($posts as $p) {
            if ($p->ID == $pId) {
                $orderedPosts[] = $p;
            }
        }
    }
    return $orderedPosts;
}

function _thrive_get_next_prev_lesson_link($lessonId, $next = true)
{
    $lessonsOrder = json_decode(get_option("thrive_appr_lessons_order"), true);
    if (!is_array($lessonsOrder)) {
        $lessonsOrder = array();
    }

    $all_lessons = array();
    foreach ($lessonsOrder as $catId => $lessonsIds) {
        foreach ($lessonsIds as $key => $pId) {
            if (is_numeric($key)) {
                $all_lessons[] = $pId;
            }
        }
    }


    foreach ($all_lessons as $key => $pId) {
        if ($pId == $lessonId) {
            if ($next === true) {
                //get the first published lesson
                for ($index = $key + 1; $index < count($all_lessons); $index++) {
                    if (isset($all_lessons[$index]) && $all_lessons[$index] != $lessonId) {
                        if ('publish' === get_post_status($all_lessons[$index])) {
                            return get_permalink($all_lessons[$index]);
                        }
                    }
                }
                return false;
            }
            if ($next === false) {
                //get the first published lesson
                for ($index = $key - 1; $index >= 0; $index--) {
                    if ('publish' === get_post_status($all_lessons[$index])) {
                        return get_permalink($all_lessons[$index]);
                    }
                }
                return false;
            }
        }
    }

    return false;

}

function _thrive_get_ordered_cat_array_by_parent($parent_id = 0)
{
    $parent_id = (int)$parent_id;

    $cats = get_terms("apprentice", 'hide_empty=0&parent=' . $parent_id);

    $orderArray = json_decode(get_option("thrive_appr_lessons_order"), true);

    $tempArray = array();

    foreach ($cats as $cat) {
        $cat = (array)$cat;
        $cat['order'] = (isset($orderArray[$cat['term_id']]) && isset($orderArray[$cat['term_id']]['order'])) ?
            $orderArray[$cat['term_id']]['order'] : 0;
        $tempArray[] = $cat;
    }

    usort($tempArray, "_thrive_sort_by_order");

    return $tempArray;
}

function _thrive_sort_by_order($a, $b)
{
    if (!isset($a['order'])) {
        $a['order'] = 0;
    }
    if (!isset($b['order'])) {
        $b['order'] = 0;
    }
    return $a['order'] - $b['order'];
}

/*
 * Method that retreives the audio or media options for a lesson
 * @param $lesson_id
 */
function _thrive_appr_get_media_options_for_lesson($lesson_id = null, $options = null) {
    if (!$lesson_id) {
        $lesson_id = get_the_ID();
    }
    if (!$lesson_id) {
        return;
    }
    if (!$options) {
        $options = thrive_get_options_for_post($lesson_id, array('apprentice' => 1));
    }

    $media_options = array();
    $lesson_type = get_post_meta($lesson_id, '_thrive_meta_appr_lesson_type', true);

    if ($lesson_type == "audio") {
        $media_options = array(
            'audio_type' => get_post_meta($lesson_id, '_thrive_meta_appr_audio_type', true),
            'soundcloud_embed_code' => get_post_meta($lesson_id, '_thrive_meta_appr_audio_soundcloud_embed_code', true),
            'audio_file' => get_post_meta($lesson_id, '_thrive_meta_appr_audio_file', true),
        );
    }

    if ($lesson_type == "video") {

        $thrive_meta_appr_video_type = get_post_meta($lesson_id, '_thrive_meta_appr_video_type', true);
        $thrive_meta_appr_video_youtube_url = get_post_meta($lesson_id, '_thrive_meta_appr_video_youtube_url', true);
        $thrive_meta_appr_video_vimeo_url = get_post_meta($lesson_id, '_thrive_meta_appr_video_vimeo_url', true);
        $thrive_meta_appr_video_custom_url = get_post_meta($lesson_id, '_thrive_meta_appr_video_custom_url', true);
        $thrive_meta_appr_video_custom_code = get_post_meta($lesson_id, '_thrive_meta_appr_video_custom_embed', true);

        $youtube_attrs = array(
            'hide_logo' => get_post_meta($lesson_id, '_thrive_meta_appr_video_youtube_hide_logo', true),
            'hide_controls' => get_post_meta($lesson_id, '_thrive_meta_appr_video_youtube_hide_controls', true),
            'hide_related' => get_post_meta($lesson_id, '_thrive_meta_appr_video_youtube_hide_related', true),
            'hide_title' => get_post_meta($lesson_id, '_thrive_meta_appr_video_youtube_hide_title', true),
            'autoplay' => get_post_meta($lesson_id, '_thrive_meta_appr_video_youtube_autoplay', true),
            'hide_fullscreen' => get_post_meta($lesson_id, '_thrive_meta_appr_video_youtube_hide_fullscreen', true),
            'video_width' => 1080
        );

        if ($thrive_meta_appr_video_type == "youtube") {
            $video_code = _thrive_get_youtube_embed_code($thrive_meta_appr_video_youtube_url, $youtube_attrs);
        } elseif ($thrive_meta_appr_video_type == "vimeo") {
            $video_code = _thrive_get_vimeo_embed_code($thrive_meta_appr_video_vimeo_url);
        } elseif($thrive_meta_appr_video_type == "custom_embed") {
            $video_code = do_shortcode($thrive_meta_appr_video_custom_code);
        } else {
            if (strpos($thrive_meta_appr_video_custom_url, "<") !== false) { //if embeded code or url
                $video_code = do_shortcode($thrive_meta_appr_video_custom_url);
            } else {
                $video_code = do_shortcode("[video src='" . $thrive_meta_appr_video_custom_url . "']");
            }
        }

        $media_options = array(
            'video_type' => $thrive_meta_appr_video_type,
            'video_code' => $video_code
        );
    }


    return $media_options;
}

function _thrive_appr_get_lessons_root_url() {
    $root_page_id = thrive_get_theme_options("appr_root_page");
    $homepage_url = get_page_link($root_page_id);
    if ($homepage_url && !empty($homepage_url)) {
        return $homepage_url;
    } else {
        return home_url('/');
    }
}

?>
