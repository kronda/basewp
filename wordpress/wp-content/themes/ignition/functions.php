<?php
add_filter('auto_update_theme', '__return_true');
add_filter('previous_comments_link_attributes', 'thrive_get_previous_comments_link_attributes');
add_filter('next_comments_link_attributes', 'thrive_get_next_comments_link_attributes');
/*
 * Set up a global variable in order to know that this is a thrive theme
 */
global $is_thrive_theme;
$is_thrive_theme = true;
/*
 * Include the init file that handles the main configurations and the backend 
 * methods
 */
include(get_template_directory() . '/inc/configs/init.php');

include(get_template_directory() . "/inc/configs/theme-options.php");

include(get_template_directory() . '/inc/tha-theme-hooks.php');

include(get_template_directory() . '/inc/thrive-image-optimization.php');

include(get_template_directory() . '/inc/templates/custom-menu-walker.php');

include(get_template_directory() . '/inc/helpers/views.php');

/*
 * Render the breadcrumbs
 */
if (!function_exists('thrive_breadcrumbs')) :
    function thrive_breadcrumbs()
    {

        global $post;

        if (is_404()) {
            return;
        }

        $arrowImg = " &nbsp;/&nbsp; ";

        if (get_option('show_on_front') == 'page') {
            $posts_page_id = get_option('page_for_posts');
            $posts_page_url = get_page_link($posts_page_id);
            $homepage_id = get_option('page_on_front');
            $homepage_url = empty($homepage_id) ? get_option('home') : get_page_link($homepage_id);
            echo "<li typeof='v:Breadcrumb'><a rel='v:url' property='v:title' class='home' href='" . $homepage_url . "'> " . __("Home", 'thrive') . $arrowImg . "</a></li>";
            if (!is_page() && !empty($posts_page_id)) {
                if (!is_home()) {
                    echo "<li typeof='v:Breadcrumb'><a rel='v:url' property='v:title' class='home' href='" . $posts_page_url . "'> " . __("Blog", 'thrive') . $arrowImg . "</a></li>";
                } else {
                    echo "<li typeof='v:Breadcrumb'><a class='no-link' rel='v:url' property='v:title' href='" . $posts_page_url . "'>" . __("Blog", 'thrive') . "</a></li>";
                }
            }
        } else {
            echo "<li typeof='v:Breadcrumb'><a rel='v:url' property='v:title' class='home' href='" . get_option('home') . "'> " . __("Home", 'thrive') . $arrowImg . "</a></li>";
        }


        if (is_category() && isset($post->ID)) {
            $cats = get_the_category($post->ID);
            if (!empty($cats)) {
                if (isset($cats[0])) {
                    echo "<li typeof='v:Breadcrumb'><a rel='v:url' property='v:title' href='" . get_category_link($cats[0]->term_id) . "'>" . $cats[0]->cat_name . "</a></li>";
                }
            }
        } elseif (is_single()) {
            if (!isset($cats[0])) {
                $arrowImg = "";
            }
            echo "<li typeof='v:Breadcrumb'><a class='no-link' rel='v:url' property='v:title' href='" . get_post_permalink($post->id) . "'>" . $arrowImg;
            echo get_the_title();
            echo "</a></li>";

        } elseif (is_page()) {
            if ($post->post_parent) {
                $anc = array_reverse(get_post_ancestors($post->ID));

                $output = "";
                foreach ($anc as $ancestor) {
                    $anc_link = get_page_link($ancestor);
                    $output .= "<li typeof='v:Breadcrumb'><a rel='v:url' property='v:title' href='" . $anc_link . "'>" . get_the_title($ancestor) . " " . $arrowImg . "</a></li>";
                }
                echo $output . "<li typeof='v:Breadcrumb'><a class='no-link' rel='v:url' property='v:title' href='" . get_permalink() . "'>";
                the_title();
                echo "</a></li>";
            } else {
                echo "<li typeof='v:Breadcrumb'><a class='no-link' rel='v:url' property='v:title' href='" . get_permalink() . "'>";
                echo the_title();
                echo "</a></li>";
            }
        } elseif (is_tag()) {
            echo "<li typeof='v:Breadcrumb'>" . single_tag_title('', false) . '</li>';
        } elseif (is_day()) {
            echo __("Archive", 'thrive') . ": ";
            the_time('F jS, Y');
            echo '</li>';
        } elseif (is_month()) {
            echo __("Archive", 'thrive') . ": ";
            the_time('F, Y');
            echo '</li>';
        } elseif (is_year()) {
            echo __("Archive", 'thrive') . ": ";
            the_time('Y');
            echo '</li>';
        } elseif (is_author()) {
            echo __("Author's archive", 'thrive') . ": ";
            echo '</li>';
        } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {
            echo __("Archive", 'thrive') . ": ";
            echo '';
        } elseif (is_search()) {
            echo __("Search results", 'thrive') . ": ";
        } elseif (is_archive()) {
            echo __("Archive", 'thrive') . ": ";
        }
        return;
    }
endif; //thrive_breadcrumbs
/*
 * Render the pagination links
 */

function thrive_pagination()
{
    global $wp_query;

    $total_pages = $wp_query->max_num_pages;

    if ($total_pages > 1) {

        $current_page = max(1, get_query_var('paged'));

        if (!is_search()) {
            echo paginate_links(array(
                'base' => trim(get_pagenum_link(1), "/") . '/%_%',
                'current' => $current_page,
                'total' => $total_pages,
            ));
        } else {
            echo paginate_links(array(
                'base' => get_pagenum_link(1) . '%_%',
                'format' => ((get_option('permalink_structure') && !$wp_query->is_search) || (is_home() && get_option('show_on_front') !== 'page' && !get_option('page_on_front'))) ? '?paged=%#%' : '&paged=%#%', // %#% will be replaced with page number
                'current' => $current_page,
                'total' => $total_pages,
            ));
        }
    }
}

/*
 * Check if the curernt post (or page) has a focus area that needs to be rendered
 * @return Boolean
 */

function thrive_check_top_focus_area()
{

    if (is_home() || is_404()) {
        return false;
    }

    global $post;

    if (!$post || !isset($post->post_type)) {
        return false;
    }

    if ($post->post_type == TT_APPR_POST_TYPE_LESSON || $post->post_type == TT_APPR_POST_TYPE_PAGE) {
        $post->post_type = "post";
    }

    if ($post->post_type == "post") {
        return _thrive_check_top_focus_area_post($post);
    } else {
        return _thrive_check_top_focus_area_page($post);
    }
}

/*
 * Check if the curernt post (or page) has a focus area that needs to be rendered
 * @return Boolean
 */

function thrive_check_bottom_focus_area()
{

    if (is_home() || is_404()) {
        return false;
    }

    global $post;
    if (!$post || !isset($post->post_type)) {
        return false;
    }
    if ($post->post_type == "post") {
        return _thrive_check_top_focus_area_post($post, "bottom");
    } else {
        return _thrive_check_top_focus_area_page($post, "bottom");
    }
}

/*
 * Helper function used to check if the curernt post has a focus area that needs to be rendered
 * @param Post object
 * @return Boolean
 */

function _thrive_check_top_focus_area_post($post, $position = "top")
{
    $custom_fields = get_post_custom($post->ID);

    if ($position == "top") {
        if (isset($custom_fields['_thrive_meta_post_focus_area_top'][0]) && is_numeric($custom_fields['_thrive_meta_post_focus_area_top'][0]) && get_post($custom_fields['_thrive_meta_post_focus_area_top'][0]) && $post->post_type == "post") {
            return true;
        }

        if (isset($custom_fields['_thrive_meta_post_focus_area_top'][0]) && $custom_fields['_thrive_meta_post_focus_area_top'][0] == "hide" && $post->post_type == "post") {
            return false;
        }
    } else {
        if (isset($custom_fields['_thrive_meta_post_focus_area_bottom'][0]) && is_numeric($custom_fields['_thrive_meta_post_focus_area_bottom'][0]) && get_post($custom_fields['_thrive_meta_post_focus_area_bottom'][0]) && $post->post_type == "post") {
            return true;
        }

        if (isset($custom_fields['_thrive_meta_post_focus_area_bottom'][0]) && $custom_fields['_thrive_meta_post_focus_area_bottom'][0] == "hide" && $post->post_type == "post") {
            return false;
        }
    }

    $post_categories = wp_get_post_categories($post->ID);

    $query1 = new WP_Query("post_type=focus_area&meta_key=_thrive_meta_focus_display_post_type&meta_value=post&order=ASC&posts_per_page=-1");
    foreach ($query1->get_posts() as $p) {
        //check for the top display option
        $post_custom_atr = get_post_custom($p->ID);
        $focus_cats = json_decode($post_custom_atr['_thrive_meta_focus_display_categories'][0]);
        if (!is_array($focus_cats)) {
            $focus_cats = array();
        }

        if (isset($post_custom_atr['_thrive_meta_focus_display_location']) && isset($post_custom_atr['_thrive_meta_focus_display_location'][0]) && $post_custom_atr['_thrive_meta_focus_display_location'][0] == $position && ($post_custom_atr['_thrive_meta_focus_display_is_default'][0] == 1 || count(array_intersect($post_categories, $focus_cats)) > 0)) {
            return true;
        }
    }
    return false;
}

/*
 * Helper function used to check if the curernt page has a focus area that needs to be rendered
 * @param Post object
 * @return Boolean
 */

function _thrive_check_top_focus_area_page($post, $position = "top")
{
    $custom_fields = get_post_custom($post->ID);
    if ($position == "top") {
        if (isset($custom_fields['_thrive_meta_post_focus_area_top'][0]) && is_numeric($custom_fields['_thrive_meta_post_focus_area_top'][0]) && get_post($custom_fields['_thrive_meta_post_focus_area_top'][0]) && $post->post_type == "page") {
            return true;
        }

        if (isset($custom_fields['_thrive_meta_post_focus_area_top'][0]) && $custom_fields['_thrive_meta_post_focus_area_top'][0] == "hide" && $post->post_type == "page") {
            return false;
        }
    } else {
        if (isset($custom_fields['_thrive_meta_post_focus_area_bottom'][0]) && is_numeric($custom_fields['_thrive_meta_post_focus_area_bottom'][0]) && get_post($custom_fields['_thrive_meta_post_focus_area_bottom'][0]) && $post->post_type == "page") {
            return true;
        }

        if (isset($custom_fields['_thrive_meta_post_focus_area_bottom'][0]) && $custom_fields['_thrive_meta_post_focus_area_bottom'][0] == "hide" && $post->post_type == "page") {
            return false;
        }
    }
    $query2 = new WP_Query("post_type=focus_area&meta_key=_thrive_meta_focus_display_post_type&meta_value=page&order=ASC&posts_per_page=-1");

    $post_categories = wp_get_post_categories($post->ID);

    foreach ($query2->get_posts() as $p) {
        $post_custom_atr = get_post_custom($p->ID);
        if (isset($post_custom_atr['_thrive_meta_focus_display_categories']) && $post_custom_atr['_thrive_meta_focus_display_categories'][0]) {
            $focus_cats = json_decode($post_custom_atr['_thrive_meta_focus_display_categories'][0]);
        } else {
            $focus_cats = array();
        }
        if (!is_array($focus_cats)) {
            $focus_cats = array();
        }

        if (isset($post_custom_atr['_thrive_meta_focus_display_location']) && isset($post_custom_atr['_thrive_meta_focus_display_location'][0]) && $post_custom_atr['_thrive_meta_focus_display_location'][0] == $position) {
            return true;
        }
    }

    return false;
}

/*
 * Renders the top focus area
 */

function thrive_render_top_focus_area($position = "top", $place = null)
{
    global $post;
    $current_post = $post;
    $page_focus = null;
    $post_focus = null;
    $current_focus = null;
    $current_focus_attrs = null;

    $custom_fields = get_post_custom($post->ID);

    if ($position == "top") {
        if (isset($custom_fields['_thrive_meta_post_focus_area_top'][0]) && is_numeric($custom_fields['_thrive_meta_post_focus_area_top'][0]) && get_post($custom_fields['_thrive_meta_post_focus_area_top'][0]) && $post->post_type == "post") {
            $post_focus = get_post($custom_fields['_thrive_meta_post_focus_area_top'][0]);
        }

        if (isset($custom_fields['_thrive_meta_post_focus_area_top']) && is_numeric($custom_fields['_thrive_meta_post_focus_area_top'][0]) && get_post($custom_fields['_thrive_meta_post_focus_area_top'][0]) && $post->post_type == "page") {
            $page_focus = get_post($custom_fields['_thrive_meta_post_focus_area_top'][0]);
        }
    } else {
        if (isset($custom_fields['_thrive_meta_post_focus_area_bottom'][0]) && is_numeric($custom_fields['_thrive_meta_post_focus_area_bottom'][0]) && get_post($custom_fields['_thrive_meta_post_focus_area_bottom'][0]) && $post->post_type == "post") {
            $post_focus = get_post($custom_fields['_thrive_meta_post_focus_area_bottom'][0]);
        }

        if (isset($custom_fields['_thrive_meta_post_focus_area_bottom']) && is_numeric($custom_fields['_thrive_meta_post_focus_area_bottom'][0]) && get_post($custom_fields['_thrive_meta_post_focus_area_bottom'][0]) && $post->post_type == "page") {
            $page_focus = get_post($custom_fields['_thrive_meta_post_focus_area_bottom'][0]);
        }
    }

    if (!$post_focus) {
        $post_categories = wp_get_post_categories($post->ID);
        $query1 = new WP_Query("post_type=focus_area&meta_key=_thrive_meta_focus_display_post_type&meta_value=post&order=ASC&posts_per_page=-1");
        foreach ($query1->get_posts() as $p) {
            $post_custom_atr = get_post_custom($p->ID);
            $focus_cats = json_decode($post_custom_atr['_thrive_meta_focus_display_categories'][0]);
            if (!is_array($focus_cats)) {
                $focus_cats = array();
            }

            if (isset($post_custom_atr['_thrive_meta_focus_display_location']) && isset($post_custom_atr['_thrive_meta_focus_display_location'][0]) && $post_custom_atr['_thrive_meta_focus_display_location'][0] == $position && ($post_custom_atr['_thrive_meta_focus_display_is_default'][0] == 1 || count(array_intersect($post_categories, $focus_cats)) > 0)) {
                $post_focus = $p;
            }
        }
    }
    if (!$page_focus) {
        $post_categories = wp_get_post_categories($post->ID);
        //get the focus area for the posts and for the pages, if any is set
        $query2 = new WP_Query("post_type=focus_area&meta_key=_thrive_meta_focus_display_post_type&meta_value=page&order=ASC&posts_per_page=-1");
        foreach ($query2->get_posts() as $p) {
            $post_custom_atr = get_post_custom($p->ID);
            if (isset($post_custom_atr['_thrive_meta_focus_display_categories']) && $post_custom_atr['_thrive_meta_focus_display_categories'][0]) {
                $focus_cats = json_decode($post_custom_atr['_thrive_meta_focus_display_categories'][0]);
            } else {
                $focus_cats = array();
            }
            if (!is_array($focus_cats)) {
                $focus_cats = array();
            }
            if (isset($post_custom_atr['_thrive_meta_focus_display_location']) && isset($post_custom_atr['_thrive_meta_focus_display_location'][0]) && $post_custom_atr['_thrive_meta_focus_display_location'][0] == $position) {
                $page_focus = $p;
            }
        }
    }

    if ($current_post->post_type == "post") {
        if ($post_focus) {
            $current_focus = $post_focus;
        }
    }

    if ($post->post_type == "page") {
        if ($page_focus) {
            $current_focus = $page_focus;
        }
    }

    if ($place == "blog" || $place == "archive") {

        if ($place == "blog") {
            $query4 = new WP_Query("post_type=focus_area&meta_key=_thrive_meta_focus_page_blog&meta_value=blog&order=ASC&posts_per_page=-1");
        } elseif ($place == "archive") {
            $query4 = new WP_Query("post_type=focus_area&meta_key=_thrive_meta_focus_page_archive&meta_value=archive&order=ASC&posts_per_page=-1");
        }

        $focus_areas = $query4->get_posts();

        foreach ($focus_areas as $focus_area) {
            $post_custom_atr = get_post_custom($focus_area->ID);

            if (isset($post_custom_atr['_thrive_meta_focus_display_location'])
                && isset($post_custom_atr['_thrive_meta_focus_display_location'][0])
                && $post_custom_atr['_thrive_meta_focus_display_location'][0] == $position
            ) {
                $current_focus = $focus_area;
            }
        }
    }

    if (!$current_focus) {
        return;
    }
    $current_attrs = get_post_custom($current_focus->ID);

    if (!$current_attrs || !isset($current_attrs['_thrive_meta_focus_template']) || !isset($current_attrs['_thrive_meta_focus_template'][0])) {
        return;
    }

    if (isset($current_attrs['_thrive_meta_focus_optin']) && isset($current_attrs['_thrive_meta_focus_optin'][0])) {

        $optin_id = (int)$current_attrs['_thrive_meta_focus_optin'][0];

        //form action
        $optinFormAction = get_post_meta($optin_id, '_thrive_meta_optin_form_action', true);

        //form method
        $optinFormMethod = get_post_meta($optin_id, '_thrive_meta_optin_form_method', true);
        $optinFormMethod = strtolower($optinFormMethod);
        $optinFormMethod = $optinFormMethod === 'post' || $optinFormMethod === 'get' ? $optinFormMethod : 'post';

        //form hidden inputs
        $optinHiddenInputs = get_post_meta($optin_id, '_thrive_meta_optin_hidden_inputs', true);

        //form fields
        $optinFieldsJson = get_post_meta($optin_id, '_thrive_meta_optin_fields_array', true);
        $optinFieldsArray = json_decode($optinFieldsJson, true);

        //form not visible inputs
        $optinNotVisibleInputs = get_post_meta($optin_id, '_thrive_meta_optin_not_visible_inputs', true);
    } else {
        $optinFieldsArray = array();
        $optinFormAction = "";
        $optinHiddenInputs = "";
    }

    $value_focus_template = strtolower($current_attrs['_thrive_meta_focus_template'][0]);

    if ($value_focus_template == "template3") {
        $value_focus_template = "template2";
    }

    $template_path = get_template_directory() . "/focusareas/" . $value_focus_template . ".php";
    if ($position != "top" && $value_focus_template == "template6") {
        return;
    }
    require $template_path;
}

/*
 * Renders the bottom focus area
 */

function thrive_render_bottom_focus_area()
{
    global $post;
    $current_post = $post;
    $page_focus = null;
    $post_focus = null;
    $current_focus = null;

    $custom_fields = get_post_custom($post->ID);

    if (isset($custom_fields['_thrive_meta_post_focus_area_bottom'][0]) && is_numeric($custom_fields['_thrive_meta_post_focus_area_bottom'][0]) && get_post($custom_fields['_thrive_meta_post_focus_area_bottom'][0]) && $post->post_type == "post") {
        $post_focus = get_post($custom_fields['_thrive_meta_post_focus_area_bottom'][0]);
    }

    if (isset($custom_fields['_thrive_meta_post_focus_area_bottom'][0]) && is_numeric($custom_fields['_thrive_meta_post_focus_area_bottom'][0]) && get_post($custom_fields['_thrive_meta_post_focus_area_bottom'][0]) && $post->post_type == "page") {
        $page_focus = get_post($custom_fields['_thrive_meta_post_focus_area_bottom'][0]);
    }


    //get the focus area for the posts and for the pages, if any is set
    $query1 = new WP_Query("post_type=focus_area&meta_key=_thrive_meta_focus_display_post_type&meta_value=post&order=ASC&posts_per_page=-1");
    foreach ($query1->get_posts() as $p) {
        $post_custom_atr = get_post_custom($p->ID);
        if (isset($post_custom_atr['_thrive_meta_focus_display_location']) && isset($post_custom_atr['_thrive_meta_focus_display_location'][0]) && $post_custom_atr['_thrive_meta_focus_display_location'][0] == "bottom") {
            $post_focus = $p;
        }
    }

    //get the focus area for the posts and for the pages, if any is set
    $query2 = new WP_Query("post_type=focus_area&meta_key=_thrive_meta_focus_display_post_type&meta_value=page&order=ASC&posts_per_page=-1");
    foreach ($query2->get_posts() as $p) {
        $post_custom_atr = get_post_custom($p->ID);
        if (isset($post_custom_atr['_thrive_meta_focus_display_location']) && isset($post_custom_atr['_thrive_meta_focus_display_location'][0]) && $post_custom_atr['_thrive_meta_focus_display_location'][0] == "bottom") {
            $page_focus = $p;
        }
    }

    if ($current_post->post_type == "post") {
        if ($post_focus) {
            $current_focus = $post_focus;
        }
    }

    if ($post->post_type == "page") {
        if ($page_focus) {
            $current_focus = $page_focus;
        }
    }

    if (!$current_focus) {
        return;
    }
    $current_attrs = get_post_custom($current_focus->ID);

    if (!$current_attrs || !isset($current_attrs['_thrive_meta_focus_template']) || !isset($current_attrs['_thrive_meta_focus_template'][0])) {
        return;
    }

    $template_path = get_template_directory() . "/focusareas/" . strtolower($current_attrs['_thrive_meta_focus_template'][0]) . "_bottom.php";

    require_once $template_path;
}

/*
 * Changes the page menu markup in order to render it accordingly to the theme's markup
 * @param string $page_markup The menu markup
 * @return string The new markup
 */

function thrive_custom_page_menu($page_markup)
{
    preg_match('/^<div class=\"([a-z0-9-_]+)\">/i', $page_markup, $matches);
    $divclass = $matches[1];
    $toreplace = array('<div class="' . $divclass . '">', '</div>');
    $new_markup = str_replace($toreplace, '', $page_markup);
    $new_markup = preg_replace('/^<ul>/i', '<ul id="' . $divclass . '">', $new_markup);
    $new_markup = '<nav class="right">' . $new_markup . '</nav>';
    return $new_markup;
}

add_filter('wp_page_menu', 'thrive_custom_page_menu');

// read more link
add_filter('the_content_more_link', 'thrive_more_link', 10, 2);
function thrive_more_link($more_link, $more_link_text)
{
    $options = thrive_get_theme_options();
    $read_more_class = ($options['other_read_more_type'] == "button") ? "btn dark medium" : "readmore_link";
    $read_more_text = ($options['other_read_more_text'] != "") ? $options['other_read_more_text'] : "Read more";
    $read_more_text = ($options['other_read_more_type'] == "button") ? "<span>" . $options['other_read_more_text'] . "</span>" : $options['other_read_more_text'];
    $thrive_more_link = '<a class="' . $read_more_class . '" href="' . get_permalink() . '">' . $read_more_text . '</a>';

    if ($options['other_read_more_type'] == "button") {
        $output = "<div class='mre'><a href='" . get_permalink() . "'><span>" . $read_more_text . "</span>" . ($options["other_show_excerpt"] == 0 ? "<span class='awe'>&#xf18e;</span>" : "") . "<div class='clear'></div></a></div>";
    } else {
        $output = "<a href='" . get_permalink() . "' class='rmt'>" . $read_more_text . "</a>";
    }
    return $output;
}

/**
 * Because TCB does apply filters on "the_content_more_link"
 * and because of the filter "the_content_more_link" added in this theme
 * This function removes the more_text appended to the excerpt
 *
 * @see  thrive_more_link()
 */
add_filter("the_excerpt", "thrive_the_excerpt");
function thrive_the_excerpt($excerpt)
{
    $thrive_read_more_text = trim(thrive_get_theme_options('other_read_more_text'));
    $last_occurrence = strrpos($excerpt, $thrive_read_more_text);
    if ($last_occurrence !== false) {
        $excerpt = substr_replace($excerpt, "", $last_occurrence, strlen($thrive_read_more_text));
    }
    return $excerpt;
}

// Adding actions to show and edit the field
add_action('show_user_profile', 'thrive_social_fields_display', 10);
add_action('edit_user_profile', 'thrive_social_fields_display', 10);

function thrive_social_fields_display($user)
{
    ?>
    <h3><?php _e('Thrive Author Box Social Settings', 'thrive'); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="facebook"><?php _e("Facebook Page URL", 'thrive'); ?></label></th>
            <td>
                <input type="text" name="facebook" id="facebook"
                       value="<?php echo esc_attr(get_the_author_meta('facebook', $user->ID)); ?>"
                       class="regular-text"/><br/>
            </td>
        </tr>
        <tr>
            <th><label for="twitter"><?php _e("Twitter Username", 'thrive'); ?></label></th>
            <td>
                <input type="text" name="twitter" id="twitter"
                       value="<?php echo esc_attr(get_the_author_meta('twitter', $user->ID)); ?>"
                       class="regular-text"/><br/>
            </td>
        </tr>
        <tr>
            <th><label for="gplus"><?php _e("Google+ Profile URL", 'thrive'); ?></label></th>
            <td>
                <input type="text" name="gplus" id="gplus"
                       value="<?php echo esc_attr(get_the_author_meta('gplus', $user->ID)); ?>"
                       class="regular-text"/><br/><br/>
                <input type="checkbox"
                       name="gauthor" <?php echo (get_the_author_meta('gauthor', $user->ID)) ? "checked" : ""; ?> />
                <label for="gauthor"><?php _e("Activate Google Authorship", 'thrive'); ?></label>
                <br/>
                <span class="description"><?php __("This adds a rel=author tag into your blog post headers, which allows
                        Google to recognize you as the author. Only tick this option if you aren't already using a
                        different Google authorship integration.", 'thrive'); ?></span>
                <br/>
            </td>
        </tr>
    </table>
    <?php
}

add_action('personal_options_update', 'thrive_save_user_fields');
add_action('edit_user_profile_update', 'thrive_save_user_fields');

function thrive_save_user_fields($user_id)
{

    if (!current_user_can('edit_user', $user_id))
        return false;

    $_POST['gauthor'] = (isset($_POST['gauthor'])) ? $_POST['gauthor'] : "";
    $_POST['gplus'] = (isset($_POST['gplus'])) ? $_POST['gplus'] : "";
    $_POST['twitter'] = (isset($_POST['twitter'])) ? $_POST['twitter'] : "";
    $_POST['facebook'] = (isset($_POST['facebook'])) ? $_POST['facebook'] : "";

    update_usermeta($user_id, 'gauthor', $_POST['gauthor']);
    update_usermeta($user_id, 'gplus', $_POST['gplus']);
    update_usermeta($user_id, 'twitter', $_POST['twitter']);
    update_usermeta($user_id, 'facebook', $_POST['facebook']);
}

// google authorship link
add_action('wp_head', 'thrive_gauthorship');

function thrive_gauthorship()
{
    if (is_single() || is_page()) {
        global $post;
        $user_id = $post->post_author;
        $g_page = get_the_author_meta('gplus', $user_id);
        $g_activated = get_the_author_meta('gauthor', $user_id);
        if ($g_page && $g_activated):
            echo '<link rel="author" href="' . $g_page . '"/>';
        endif;
    }
}

function thrive_exclude_category($query)
{
    $hide_cat_option = thrive_get_theme_options('hide_cats_from_blog');

    if (!is_string($hide_cat_option)) {
        $hide_cat_option = "";
    }

    $hide_categories = is_array(json_decode($hide_cat_option)) ? json_decode($hide_cat_option) : array();
    $temp_query_string_part = "";
    foreach ($hide_categories as $temp_cat_id) {
        $temp_query_string_part .= "-" . $temp_cat_id . " ";
    }

    if ($query->is_home()) {
        $query->set('cat', $temp_query_string_part);
    }
    return $query;
}

add_filter('pre_get_posts', 'thrive_exclude_category');

// prevent wrapping of paragraph tags around shortcodes
add_filter('the_content', 'thrive_remove_autop_shortcodes');

function thrive_remove_autop_shortcodes($content)
{
    $array = array(
        '<p>[' => '[',
        ']</p>' => ']',
        ']<br />' => ']'
    );

    $content = strtr($content, $array);
    return $content;
}

// attach classes that are helpful for CSS to primary menu and remove all pages apart from top level pages from the footer menu
function thrive_menu_set_dropdown($sorted_menu_items, $args)
{
    if (isset($args->theme_location) && $args->theme_location == "primary") {
        $last_top = 0;
        $post_id_key = array();

        foreach ($sorted_menu_items as $key => $obj) {
            // if not parent element (class not to be applied to parent element
            if (0 != $obj->menu_item_parent) {
                $sorted_menu_items[$last_top]->classes['dropdown'] = 'toplvl dropdown';
                // need to map key to post id
                $post_id_key[$obj->db_id] = $key;

                // if menu item has parent
                if ($obj->menu_item_parent) {
                    if (isset($post_id_key[$obj->menu_item_parent])) {
                        // give parent class identifier
                        $sub_menu_parent_key = $post_id_key[$obj->menu_item_parent];
                        $sorted_menu_items[$sub_menu_parent_key]->classes['dropdown'] = 'arl';
                    }
                }
            } else {
                // top level menu item
                $sorted_menu_items[$key]->classes['dropdown'] = 'toplvl';
                $last_top = $key;
            }
        }
        return $sorted_menu_items;
    }

    return $sorted_menu_items;
}

//add_filter('wp_nav_menu_objects', 'thrive_menu_set_dropdown', 10, 2);

require(get_template_directory() . "/inc/clone-post.php");
require(get_template_directory() . "/inc/theme-update.php");


/*
 * Add a new default avatar image
 */
add_filter('avatar_defaults', 'thrive_default_avatar_image');

function thrive_default_avatar_image($avatar_defaults)
{
    $myavatar = get_template_directory_uri() . '/images/default_avatar.png';
    $avatar_defaults[$myavatar] = "ThriveDefaultAvatar";
    return $avatar_defaults;
}

/*
 * Remove the query string for the static scripts and stylesheets used
 * by this theme
 */

function thrive_remove_script_version($src)
{
    $thrive_files = array("script.js", "jquery", "reset.css", "main_blue.css",
        "main_green.css", "main_orange.css", "main_purple.css", "main_red.css");

    if (thrive_strposa($src, $thrive_files)) {
        $parts = explode('?', $src);
        return $parts[0];
    }
    return $src;
}

add_filter('script_loader_src', 'thrive_remove_script_version', 15, 1);
add_filter('style_loader_src', 'thrive_remove_script_version', 15, 1);

/*
 * Hook into the ThriveContentBuilder filter and add the predefined colors for the page sections shortcode
 */
add_filter("tcb_page_section_colours", "thrive_add_tcb_page_sections_colors");

function thrive_add_tcb_page_sections_colors($colours)
{
    if (!is_array($colours)) {
        $colours = array();
    }

    $img_url = get_template_directory_uri() . "/images";

    $colours['pattern1'] = array('color' => '#FFFFFF', 'shadow' => '', 'image' => '', 'pattern' => '', 'textstyle' => 'dark');
    $colours['pattern2'] = array('color' => '#F2F2F2', 'shadow' => '', 'image' => '', 'pattern' => '', 'textstyle' => 'dark');
    $colours['pattern3'] = array('color' => '#34495E', 'shadow' => '', 'image' => '', 'pattern' => '', 'textstyle' => 'light');

    return $colours;
}

add_action('wp_ajax_thrive_lazy_load_comments', 'thrive_lazy_load_comments');
add_action('wp_ajax_nopriv_thrive_lazy_load_comments', 'thrive_lazy_load_comments');

function thrive_lazy_load_comments()
{
    $post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
    $comment_page = isset($_POST['comment_page']) ? (int)$_POST['comment_page'] : 1;
    $commnts_per_page = 10;

    $args = array(
        'post_id' => $post_id,
        'order' => strtoupper(get_option('comment_order', 'asc')),
        'number' => $commnts_per_page,
        'offset' => ($comment_page - 1) * $commnts_per_page,
    );
    $comments = get_comments($args);
    wp_list_comments(
        array(
            'callback' => 'thrive_comments',
            'reverse_top_level' => false
        ),
        $comments);
    wp_die();
}

/*
 * Render the comments template and the comments form
 * Used as a callback by wp_list_comments() for displaying the comments.
 */
if (!function_exists('thrive_comments')) :
    function thrive_comments($comment, $args, $depth)
    {
        $GLOBALS['comment'] = $comment;
        switch ($comment->comment_type) :
            case 'pingback' :
                break;
            case 'trackback' :
                break;
            default :
                // Proceed with normal comments.
                global $post;
                if (!$post) {
                    $post = get_post($comment->comment_post_ID);
                }
                $show_comment_date = thrive_get_theme_options('other_show_comment_date');
                $relative_time = thrive_get_theme_options('relative_time');
                $highlight_author_comments = thrive_get_theme_options('highlight_author_comments');
                $comment_author = get_user_by('email', get_comment_author_email());
                $comment_author_url = get_comment_author_url();
                $display_name = null;
                $comment_container_class = "cmc";
                $comment_author_id = 0;
                if ($comment_author) {
                    $fname = get_the_author_meta('first_name', $comment_author->ID);
                    $lname = get_the_author_meta('last_name', $comment_author->ID);
                    $author_name = get_the_author_meta('display_name', $comment_author->ID);
                    $display_name = empty($author_name) ? $fname . " " . $lname : $author_name;
                    if ($post->post_author == $comment_author->ID && $highlight_author_comments == 1) {
                        $comment_container_class .= " byAut";
                    }
                    $comment_author_id = $comment_author->ID;
                }
                if (!$display_name || $display_name == "") {
                    $display_name = get_comment_author();
                }
                $client_ip = _thrive_get_client_ip();
                $comment_author_ip = get_comment_author_IP();
                $user_ID = get_current_user_id();
                ?>
                <?php if (_thrive_check_comment_approved($comment->comment_approved, $client_ip, $comment_author_ip, $user_ID, $comment_author_id)): ?>
                <div class="cmb" id="comment-<?php echo get_comment_ID(); ?>">
                    <div class="<?php echo $comment_container_class; ?>">
                        <div class="left">
                            <?php echo get_avatar(get_comment_author_email(), 69); ?>
                        </div>

                        <div class="right">
                            <span class="left nam">
                                <?php if ($comment_author_url && $comment_author_url != ""): ?>
                                    <a href="<?php echo $comment_author_url; ?>" target="_blank" rel="nofollow">
                                        <?php echo $display_name; ?>
                                    </a>
                                <?php else: ?>
                                    <span class="uNM"><?php echo $display_name; ?></span>
                                <?php endif; ?>
                                <?php if ($show_comment_date == 1): ?>
                                    -
                                    <span class="uDt">
                                        <?php if ($relative_time == 1): ?>
                                            <?php echo thrive_human_time(get_comment_date('U')); ?>
                                        <?php else: ?>
                                            <?php echo get_comment_date(); ?>
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>
                            </span>
                            <?php if (comments_open() && !post_password_required()) : ?>
                                <a class="rpl right reply" href="#" id="link-reply-<?php echo get_comment_ID(); ?>"
                                   cid="<?php echo get_comment_ID(); ?>">
                                    <span class="awe">&#xf064;</span><?php _e("Reply", 'thrive'); ?>
                                </a>
                            <?php endif; ?>
                            <div class="clear"></div>
                            <?php if ('0' == $comment->comment_approved): ?>
                                <p class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.', 'thrive'); ?></p>
                            <?php else: ?>
                                <?php comment_text(); ?>
                            <?php endif; ?>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="lrp" id="respond-container-<?php echo get_comment_ID(); ?>" style="display: none;">
                        <p class="left">
                            <?php _e("Leave a comment:", 'thrive'); ?>
                        </p>
                        <a href="#" class="crp right cancel_reply" cid="<?php echo get_comment_ID(); ?>">
                            <span class="awe">&#xf00d;</span>
                            <?php _e("Cancel Reply", 'thrive'); ?>
                        </a>

                        <div class="clear"></div>
                        <form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post">
                            <?php if (!is_user_logged_in()): ?>
                                <input type="text" author="author" id="author"
                                       placeholder="<?php _e("Name", 'thrive'); ?>*" name="author">
                                <input type="text" author="email" id="email"
                                       placeholder="<?php _e("Email", 'thrive'); ?>*" name="email">
                                <input type="text" class="lst" author="website" id="website"
                                       placeholder="<?php _e("Website", 'thrive'); ?>" name="website">
                            <?php endif; ?>
                            <textarea class="textarea" name="comment" id="comment"></textarea>
                            <input id="comment_post_ID" type="hidden" value="<?php echo get_the_ID(); ?>"
                                   name="comment_post_ID">
                            <input id="comment_parent" type="hidden" value="<?php echo get_comment_ID(); ?>"
                                   name="comment_parent">

                            <div class="">
                                <input type="submit" value="<?php _e("SUBMIT", 'thrive'); ?>">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="clear"></div>
                <!--<div class="lrp" id="respond-container-<?php echo get_comment_ID(); ?>">
                    </div>-->
            <?php endif; ?>
                <?php
                break;
        endswitch;
    }
endif; //thrive_comments

/*
 * Add custom items to the menu from the admin part. 
 */
add_filter('wp_setup_nav_menu_item', 'thrive_custom_admin_nav_item');

function thrive_custom_admin_nav_item($menu_item)
{
    $menu_item->extended_activate = get_post_meta($menu_item->ID, '_menu_item_extended_activate', true);
    $menu_item->highlight_menu = get_post_meta($menu_item->ID, '_menu_item_highlight_menu_item', true);
    $menu_item->extended_columns = get_post_meta($menu_item->ID, '_menu_item_extended_columns', true);
    $menu_item->extended_heading = get_post_meta($menu_item->ID, '_menu_item_extended_heading', true);
    $menu_item->extended_disable_link = get_post_meta($menu_item->ID, '_menu_item_extended_disable_link', true);
    $menu_item->extended_text_chk = get_post_meta($menu_item->ID, '_menu_item_extended_text_chk', true);
    $menu_item->extended_free_text = get_post_meta($menu_item->ID, '_menu_item_extended_free_text', true);
    return $menu_item;
}

add_action('wp_update_nav_menu_item', 'thrive_custom_admin_nav_update', 10, 3);

function thrive_custom_admin_nav_update($menu_id, $menu_item_db_id, $args)
{
    if (isset($_REQUEST['menu-item-extended-activate'][$menu_item_db_id]) && is_array($_REQUEST['menu-item-extended-activate'])) {
        $custom_value = $_REQUEST['menu-item-extended-activate'][$menu_item_db_id];
        update_post_meta($menu_item_db_id, '_menu_item_extended_activate', $custom_value);
    } else {
        update_post_meta($menu_item_db_id, '_menu_item_extended_activate', 'off');
    }

    if (isset($_REQUEST['menu-item-highlight-menu-item'][$menu_item_db_id]) && is_array($_REQUEST['menu-item-highlight-menu-item'])) {
        $custom_value = $_REQUEST['menu-item-highlight-menu-item'][$menu_item_db_id];
        update_post_meta($menu_item_db_id, '_menu_item_highlight_menu_item', $custom_value);
    } else {
        update_post_meta($menu_item_db_id, '_menu_item_highlight_menu_item', 'off');
    }

    if (isset($_REQUEST['menu-item-extended-columns'][$menu_item_db_id]) && is_array($_REQUEST['menu-item-extended-columns'])) {
        $custom_value = $_REQUEST['menu-item-extended-columns'][$menu_item_db_id];
        update_post_meta($menu_item_db_id, '_menu_item_extended_columns', $custom_value);
    }

    if (isset($_REQUEST['menu-item-extended-heading'][$menu_item_db_id]) && is_array($_REQUEST['menu-item-extended-heading'])) {
        $custom_value = $_REQUEST['menu-item-extended-heading'][$menu_item_db_id];
        update_post_meta($menu_item_db_id, '_menu_item_extended_heading', $custom_value);
    } else {
        if (get_post_meta($menu_item_db_id, '_menu_item_extended_heading', true) == '') {
            update_post_meta($menu_item_db_id, '_menu_item_extended_heading', 'on');
        } else {
            update_post_meta($menu_item_db_id, '_menu_item_extended_heading', 'off');
        }
    }

    if (isset($_REQUEST['menu-item-extended-disable-link'][$menu_item_db_id]) && is_array($_REQUEST['menu-item-extended-disable-link'])) {
        $custom_value = $_REQUEST['menu-item-extended-disable-link'][$menu_item_db_id];
        update_post_meta($menu_item_db_id, '_menu_item_extended_disable_link', $custom_value);
    } else {
        update_post_meta($menu_item_db_id, '_menu_item_extended_disable_link', 'off');
    }

    if (isset($_REQUEST['menu-item-extended-text-chk'][$menu_item_db_id]) && is_array($_REQUEST['menu-item-extended-text-chk'])) {
        $custom_value = $_REQUEST['menu-item-extended-text-chk'][$menu_item_db_id];
        update_post_meta($menu_item_db_id, '_menu_item_extended_text_chk', $custom_value);
    } else {
        update_post_meta($menu_item_db_id, '_menu_item_extended_text_chk', 'off');
    }

    if (isset($_REQUEST['menu-item-extended-free-text'][$menu_item_db_id]) && is_array($_REQUEST['menu-item-extended-free-text'])) {
        $custom_value = $_REQUEST['menu-item-extended-free-text'][$menu_item_db_id];
        update_post_meta($menu_item_db_id, '_menu_item_extended_free_text', $custom_value);
    }
}

add_filter('wp_edit_nav_menu_walker', 'thrive_function_admin_custom_menu_walker', 10, 2);
/*
 * TODO - be more specific on what filters should be removed
 */
//remove_all_filters('wp_get_nav_menu_items');


function custom_nav_edit_walker($walker, $menu_id)
{
    return 'Walker_Nav_Menu_Edit_Custom';
}

/*
 * add custom font css
 */
add_action("tha_head_top", "thrive_load_font_css");

function thrive_load_font_css($font)
{

    $fonts = (array)json_decode(get_option('thrive_font_manager_options'));
    if (!$fonts || !is_array($fonts)) {
        return;
    }
    echo '<style type="text/css">';

    foreach ($fonts as $font) {
        echo ' .' . $font->font_class . '{';
        echo "font-family: " . thrive_prepare_font_family($font->font_name) . ";";
        echo 'font-size:' . $font->font_size . ';';
        echo 'line-height:' . $font->font_height . ';';
        echo 'color:' . $font->font_color . ';';
        echo '} ';
        echo $font->custom_css;
    }

    echo '</style>';
}

/**
 * Prepare font family name to be added to css rule
 *
 * @param $font_family
 * @return string
 */
function thrive_prepare_font_family($font_family)
{
    $chunks = explode(",", $font_family);
    $length = count($chunks);
    $font = "";
    foreach ($chunks as $key => $value) {
        $font .= "'" . trim($value) . "'";
        $font .= ($key + 1) < $length ? ", " : "";
    }
    return $font;
}

function thrive_save_post_font($post_id)
{

    $post_content = get_post_field('post_content', $post_id);
    preg_match_all("/thrive_custom_font id='\d+'/", $post_content, $font_ids);

    $post_fonts = array();
    foreach ($font_ids[0] as $font_id) {
        $parts = explode("'", $font_id);
        $id = $parts[1];
        $font = thrive_get_font_options($id);
        if(thrive_font_manager_is_safe_font($font->font_name)) {
            continue;
        }
        if(Thrive_Font_Import_Manager::isImportedFont($font->font_name)) {
            $post_fonts[] = Thrive_Font_Import_Manager::getCssFile();
            continue;
        }
        $post_fonts[] = "//fonts.googleapis.com/css?family=" . str_replace(" ", "+", $font->font_name) . ($font->font_style != 0 ? ":" . $font->font_style : "") . ($font->font_bold != 0 ? "," . $font->font_bold : "") . ($font->font_italic != 0 ? $font->font_italic : "") . ($font->font_character_set != 0 ? "&subset=" . $font->font_character_set : "");
    }
    $post_fonts = array_unique($post_fonts);
    update_post_meta($post_id, 'thrive_post_fonts', sanitize_text_field(json_encode($post_fonts)));
}

add_action('save_post', 'thrive_save_post_font');

function thrive_enqueue_head_fonts()
{

    if (is_singular()) {
        $post_id = get_the_ID();
    } else {
        $post_id = array();
        if (have_posts()) {
            while (have_posts()) {
                the_post();
                $post_id[] = get_the_ID();
            }
        }
    }
    if (is_array($post_id)) {
        foreach ($post_id as $id) {
            $fonts = json_decode(get_post_meta($id, 'thrive_post_fonts', true));
            if ($fonts != null) {
                foreach ($fonts as $key => $font) {
                    wp_enqueue_style('tcf_' . md5($font), $font);
                }
            }
        }
    } else {
        $fonts = json_decode(get_post_meta($post_id, 'thrive_post_fonts', true));
        if ($fonts != null) {
            foreach ($fonts as $key => $font) {
                wp_enqueue_style('tcf_' . md5($font), $font);
            }
        }
    }
}

add_filter('is_protected_meta', 'thrive_hide_custom_fields', 10, 2);

function thrive_hide_custom_fields($protected, $meta_key)
{
    $keys = array(
        'thrive_post_fonts', 'thrive_share_count'
    );

    if (in_array($meta_key, $keys)) {
        return true;
    }

    return $protected;
}

add_filter('wp_link_pages_args', 'thrive_add_next_and_number');

function thrive_add_next_and_number($args)
{
    if ($args['next_or_number'] == 'next_and_number') {
        global $page, $numpages, $multipage, $more, $pagenow;
        $next = '';
        $prev = '';
        if ($multipage) {
            if ((intval($page) - 1) && $more) {
                $prev = '<span class="page-numbers">' . _wp_link_page(intval($page) - 1) . __('Previous', 'thrive') . '</a></span> ';
            }
            $output = $args['before'] . $prev;

            for ($i = 1; $i <= $numpages; $i++) {
                if ($i != $page || ((!$more) && ($page == 1))) {
                    $output .= '<span class="page-numbers">' . _wp_link_page($i) . $i . '</a></span> ';
                } else {
                    $output .= '<a class="page-numbers"><strong>' . $i . '</strong></a> ';
                }
            }

            if ((intval($page) + 1) <= $numpages && $more) {
                $next = '<span class="page-numbers">' . _wp_link_page(intval($page) + 1) . __('Next', 'thrive') . '</a></span> ';
            }
            $output .= $next . $args['after'];
            echo $output;
        }
    } else {
        return $args;
    }
    return array(
        'before' => '',
        'after' => '',
        'link_before' => '',
        'link_after' => '',
        'next_or_number' => 'number',
        'separator' => '',
        'nextpagelink' => '',
        'previouspagelink' => '',
        'pagelink' => '',
        'echo' => 1
    );
}

/*
 * Custom title output
 */
if (!function_exists('thrive_wp_title')) :
    function thrive_wp_title($title)
    {
        if (is_front_page()) {
            return get_bloginfo('name') . ' | ' . get_bloginfo('description');
        } elseif (is_feed()) {
            return ' | RSS Feed';
        } else {
            return trim($title, '| ') . ' | ' . get_bloginfo('name');
        }

    }

    add_filter('wp_title', 'thrive_wp_title');
endif;


/**
 * add custom classes for woocommerce - the right way
 */
add_filter('body_class', 'thrive_body_class');

/**
 * check if woocommerce specific pages and append required classes
 *
 * @param array $classes
 */
function thrive_body_class($classes)
{
    if (class_exists('WooCommerce')) {
        $classes [] = 'tve-woo-minicart';
    }

    if (_thrive_check_is_woocommerce_page()) {
        $classes [] = 'tve-woocommerce';
    }

    return $classes;
}

/**
 *  Display navigation to next/previous comments when applicable.
 */
function thrive_theme_comment_nav()
{
    // Are there comments to navigate through?
    if (get_comment_pages_count() > 1 && get_option('page_comments')) {
        echo "<div class='awr ctr pgn'>";

        if ($prev_link = get_previous_comments_link(__('Older Comments', 'thrive'))) {
            printf('%s', $prev_link);
        }

        if ($next_link = get_next_comments_link(__('Newer Comments', 'thrive'))) {
            printf('%s', $next_link);
        }

        echo "</div><!-- .nav-links -->";
    }
}

/**
 * return the previous comments page link's class
 * @return string
 */
function thrive_get_previous_comments_link_attributes()
{
    return 'class="prev page-numbers"';
}

/**
 * return the next comments page link's class
 * @return string
 */
function thrive_get_next_comments_link_attributes()
{
    return 'class="next page-numbers"';
}
