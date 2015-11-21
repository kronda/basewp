<?php

add_shortcode('thrive_lessons_list', 'thrive_shortcode_appr_lessons_list');
add_shortcode('thrive_lessons_gallery', 'thrive_shortcode_appr_lessons_gallery');
add_shortcode('thrive_welcome_back', 'thrive_shortcode_appr_welcome_back');

function thrive_shortcode_appr_lessons_list($attr, $content)
{

    $attr = shortcode_atts(array('title' => '',
        'thumbnails' => 'off',
        'no_posts' => 5,
        'filter' => 'recent',
        'category' => 0,
        'user' => -1), $attr);

    if ($attr['filter'] == "popular") {
        $query_params = array(
            'order' => 'DESC',
            'orderby' => 'comment_count',
            'posts_per_page' => -1
        );
        if ($attr['category'] > 0) {
            $query_params['tax_query'] = array(
                'taxonomy' => 'apprentice',
                'field' => 'term_id',
                'terms' => $attr['category']
            );
        }
        if ($attr['user'] != -1) {
            $query_params['author'] = $attr['user'];
        }

        $query_params['post_type'] = TT_APPR_POST_TYPE_LESSON;
        $r = new WP_Query($query_params);
    } else {
        $query_params = array(
            'orderby' => 'date',
            'posts_per_page' => -1
        );
        if ($attr['category'] > 0) {
            $query_params['tax_query'] = array(
                'taxonomy' => 'apprentice',
                'field' => 'term_id',
                'terms' => $attr['category']
            );
        }
        if ($attr['user'] != -1) {
            $query_params['author'] = $attr['user'];
        }

        $query_params['post_type'] = TT_APPR_POST_TYPE_LESSON;

        $r = new WP_Query($query_params);
    }

    if ($attr['category'] > 0) {
        $posts = _thrive_get_ordered_lessons($r->get_posts(), $attr['category']);
    } else {
        $posts = $r->get_posts();
    }
    $posts = array_slice($posts, 0, $attr['no_posts']);

    $output = "<div class='clear'></div><div class='scbp'>";
    if (!empty($attr['title'])) {
        $output .= "<h3>" . $attr['title'] . "</h3>";
    }

    foreach ($posts as $p) {
        if ($attr['thumbnails'] == "on") {
            $featured_img_data = thrive_get_post_featured_image($p->ID, "tt_post_icon");
            $featured_img = $featured_img_data['image_src'];

            $output .= "<div class='pps clearfix'><div class='left tim'>";
            if ($featured_img && isset($featured_img[0])) {
                $output .= "<a href='" . get_permalink($p->ID) . "' style='background-image: url(\"" . $featured_img . "\");'></a></div>";
            } else { //some default image
                $output .= "<a href='" . get_permalink($p->ID) . "' style='background-image: url(\"" . get_template_directory_uri() . "/images/default_post_list.png\")'></a></div>";
            }
            $output .= "<div class='left txt'>";
            $output .= "<a href='" . get_permalink($p->ID) . "'>" . $p->post_title . "</a>";
            $output .= "<span class='thrive_date'>" . get_the_time('Y-m-d', $p->ID) . "</span></div><div class='clear'></div></div>";
        } else {
            $output .= "<div class='pps clearfix'><div class='left txt noImageTab'>";
            $output .= "<a href='" . get_permalink($p->ID) . "'>" . $p->post_title . "</a>";
            $output .= "<span class='thrive_date'>" . get_the_time('Y-m-d', $p->ID) . "</span></div><div class='clear'></div></div>";
        }
    }
    $output .= "</div>";
    return $output;
}

function thrive_shortcode_appr_lessons_gallery($attr, $content)
{
    $attr = shortcode_atts(array('title' => '',
        'no_posts' => 5,
        'filter' => 'recent',
        'category' => 0,
        'user' => -1), $attr);

    if ($attr['filter'] == "popular") {
        $query_params = array(
            'order' => 'DESC',
            'orderby' => 'comment_count',
            'posts_per_page' => -1
        );
        if ($attr['user'] != -1) {
            $query_params['author'] = $attr['user'];
        }
        if ($attr['category'] > 0) {
            $query_params['tax_query'] = array(
                'taxonomy' => 'apprentice',
                'field' => 'term_id',
                'terms' => $attr['category']
            );
        }
        $query_params['post_type'] = TT_APPR_POST_TYPE_LESSON;
        $r = new WP_Query($query_params);
    } else {
        $query_params = array(
            'orderby' => 'date',
            'posts_per_page' => -1
        );
        if ($attr['user'] != -1) {
            $query_params['author'] = $attr['user'];
        }
        if ($attr['category'] > 0) {
            $query_params['tax_query'] = array(
                'taxonomy' => 'apprentice',
                'field' => 'term_id',
                'terms' => $attr['category']
            );
        }
        $query_params['post_type'] = TT_APPR_POST_TYPE_LESSON;
        $r = new WP_Query($query_params);
    }
    if ($attr['category'] > 0) {
        $posts = _thrive_get_ordered_lessons($r->get_posts(), $attr['category']);
    } else {
        $posts = $r->get_posts();
    }
    $posts = array_slice($posts, 0, $attr['no_posts']);

    $output = "<div class='scbg clearfix'>";
    if (!empty($attr['title'])) {
        $output .= "<h3>" . $attr['title'] . "</h3>";
    }

    foreach ($posts as $p) {
        $featured_img_data = thrive_get_post_featured_image($p->ID, "tt_grid_layout");
        $featured_img = $featured_img_data['image_src'];

        $output .= "<div class='scc left'>";
        $output .= "<a class='' href='" . get_permalink($p->ID) . "'>";
        if ($featured_img) {
            $output .= "<div class='rimc' style='background-image: url(\"" . $featured_img . "\");'></div>";
        } else { //some default image
            $output .= "<div class='rimc def' style='background-image: url(\"" . get_template_directory_uri() . "/images/default.png\");'></div>";
        }
        $output .= "</a>";
        $output .= "<div class='bgin'>";
        $output .= "<h4><a class='' href='" . get_permalink($p->ID) . "'>" . $p->post_title . "</a></h4>";
        $output .= "<div class='mre'><a class='' href='" . get_permalink($p->ID) . "'><span class='awe'>&#xf18e;</span></a></div>";
        $output .= "</div></div>";
    }
    $output .= "</div>";
    return $output;
}

function thrive_shortcode_appr_welcome_back($attr, $content)
{
    $attr = shortcode_atts(array('welcome_message' => __("Welcome back, {NamePlaceholder}! Click here to continue where you left off!", 'thrive'),
        'start_message' => __("Hello, {NamePlaceholder}! Click here to get started!", 'thrive'),
        'color' => 'blue'), $attr);

    if ($attr['color'] == "dark") {
        $colour_scheme = "shn";
    } else {
        $colour_scheme = "shnd";
    }

    if (!is_user_logged_in()) {
        return;
    }

    if (thrive_get_theme_options("appr_progress_track") != 1) {
        return;
    }

    global $current_user;
    get_currentuserinfo();

    $display_name = (empty($current_user->display_name)) ? $current_user->user_login : $current_user->display_name;

    $thrive_progress_array = get_user_meta($current_user->ID, THRIVE_APPR_PROGRESS_META_KEY, true);

    $lesson_link = false;
    if (!empty($thrive_progress_array) && array($thrive_progress_array)) {
        $thrive_progress_array = array_reverse($thrive_progress_array, true);
        foreach ($thrive_progress_array as $key => $val) {
            if ($val == THRIVE_APPR_PROGRESS_STARTED) {
                $lesson_link = get_post_permalink($key);
                break;
            }
        }
    }

    $message = str_replace("{NamePlaceholder}", $display_name, $attr['welcome_message']);

    //if no lesson found, display the get started message
    if ($lesson_link === false) {
        $lessonsOrder = json_decode(get_option("thrive_appr_lessons_order"), true);
        if (!is_array($lessonsOrder)) {
            return;
        }

        foreach ($lessonsOrder as $catId => $lessonsIds) {
            foreach ($lessonsIds as $key => $pId) {
                if (is_numeric($key)) {
                    if ('publish' === get_post_status($pId)) {
                        $lesson_link = get_permalink($pId);
                        break;
                    }
                }
            }
            if ($lesson_link) {
                break;
            }
        }

        if ($lesson_link) {
            $message = str_replace("{NamePlaceholder}", $display_name, $attr['start_message']);
        }

    }

    if (!$lesson_link) {
        return false;
    }


    $output = '<a href="' . $lesson_link . '" class="center mbi mb ' . $attr['color'] . '">
                    <div class="mbr">
                        <span class="mbt">' . $message . '</span>
                    </div>
                    <div class="clear"></div>
                </a>';

    return $output;
}

?>
