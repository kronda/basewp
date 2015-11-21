<?php

//register the new menu location
register_nav_menu('apprentice', __('Apprentice Menu', 'thrive'));

add_action('init', 'thrive_appr_create_post_types');
add_action('admin_enqueue_scripts', 'thrive_appr_enqueue_admin', PHP_INT_MAX);
add_action('widgets_init', 'thrive_init_appr_widgets');
add_filter('pre_get_posts', 'thrive_appr_include_lessons');

add_action("wp_ajax_thrive_appr_add_new_cat", "thrive_appr_add_new_cat");
add_action("wp_ajax_thrive_appr_delete_cat", "thrive_appr_delete_cat");
add_action("wp_ajax_thrive_appr_save_cat_tree", "thrive_appr_save_cat_tree");
add_action("wp_ajax_thrive_appr_add_to_favorites", "thrive_appr_add_to_favorites");
add_action("wp_ajax_nopriv_thrive_appr_add_to_favorites", "thrive_appr_add_to_favorites");
add_action("wp_ajax_thrive_appr_set_progress", "thrive_appr_set_progress");
add_action("wp_ajax_nopriv_thrive_appr_set_progress", "thrive_appr_set_progress");
add_action("wp_ajax_thrive_appr_save_lessons_level", "thrive_appr_save_lessons_level");

add_action('admin_menu', 'thrive_appr_add_menu_page');

require(get_template_directory() . "/inc/apprentice/helpers.php");
require(get_template_directory() . "/inc/apprentice/meta-options.php");
require(get_template_directory() . "/inc/apprentice/theme-options.php");
require(get_template_directory() . "/inc/apprentice/shortcodes/shortcodes.php");
require(get_template_directory() . "/inc/apprentice/widgets/widget-appr-nav.php");
require(get_template_directory() . "/inc/apprentice/widgets/widget-appr-recent.php");
require(get_template_directory() . "/inc/apprentice/widgets/widget-appr-favorites.php");
require(get_template_directory() . "/inc/apprentice/widgets/widget-appr-popular.php");

function thrive_appr_add_menu_page()
{
    add_menu_page("Manage Content", "Manage Content", "edit_theme_options", "thrive_admin_page_appr_contents", "thrive_admin_page_appr_contents", "", 59);
}

function thrive_appr_get_theme_options()
{
    $theme_options = thrive_get_theme_options();

    if (isset($theme_options['appr_different_logo']) && $theme_options['appr_different_logo'] == 1 && isset($theme_options['appr_logo']) && !empty($theme_options['appr_logo'])) {
        $theme_options['logo'] = $theme_options['appr_logo'];
    }

    if (isset($theme_options['appr_different_logo']) && $theme_options['appr_different_logo'] == 1 && isset($theme_options['appr_logo_type']) && !empty($theme_options['appr_logo_type'])) {
        $theme_options['logo_type'] = $theme_options['appr_logo_type'];
    }

    if (isset($theme_options['appr_different_logo']) && $theme_options['appr_different_logo'] == 1 && isset($theme_options['appr_logo_color']) && !empty($theme_options['appr_logo_color'])) {
        $theme_options['logo_color'] = $theme_options['appr_logo_color'];
    }

    if (isset($theme_options['appr_different_logo']) && $theme_options['appr_different_logo'] == 1 && isset($theme_options['appr_logo_text']) && !empty($theme_options['appr_logo_text'])) {
        $theme_options['logo_text'] = $theme_options['appr_logo_text'];
    }

    if (isset($theme_options['appr_different_logo']) && $theme_options['appr_different_logo'] == 1 && isset($theme_options['appr_logo_position']) && !empty($theme_options['appr_logo_position'])) {
        $theme_options['logo_position'] = $theme_options['appr_logo_position'];
    }

    if (isset($theme_options['appr_breadcrumbs'])) {
        $theme_options['display_breadcrumbs'] = $theme_options['appr_breadcrumbs'];
    }

    if (isset($theme_options['appr_sidebar'])) {
        $theme_options['sidebar_alignement'] = $theme_options['appr_sidebar'];
    }

    if (isset($theme_options['appr_page_comments'])) {
        $theme_options['comments_on_pages'] = $theme_options['appr_page_comments'];
    }

    if (isset($theme_options['appr_prev_next_link'])) {
        $theme_options['bottom_previous_next'] = $theme_options['appr_prev_next_link'];
    }

    if (isset($theme_options['appr_prev_next_link'])) {
        $theme_options['bottom_previous_next'] = $theme_options['appr_prev_next_link'];
    }
    if (isset($theme_options['appr_meta_author_name'])) {
        $theme_options['meta_author_name'] = $theme_options['appr_meta_author_name'];
    }
    if (isset($theme_options['appr_meta_post_date'])) {
        $theme_options['meta_post_date'] = $theme_options['appr_meta_post_date'];
    }
    if (isset($theme_options['appr_meta_post_category'])) {
        $theme_options['meta_post_category'] = $theme_options['appr_meta_post_category'];
    }
    if (isset($theme_options['appr_meta_post_tags'])) {
        $theme_options['meta_post_tags'] = $theme_options['appr_meta_post_tags'];
    }
    if (isset($theme_options['appr_bottom_about_author'])) {
        $theme_options['bottom_about_author'] = $theme_options['appr_bottom_about_author'];
    }

    return $theme_options;
}

function thrive_appr_enqueue_scripts()
{

    wp_register_script('thrive-appr-main-script', get_template_directory_uri() . '/appr/js/thrive-apprentice.js', array('jquery'), "", true);

    wp_enqueue_script('thrive-appr-main-script');

    $params_array = array('ajax_url' => admin_url('admin-ajax.php'),
        'current_post_id' => get_the_ID(),
        'lang' => array('remove_from_fav' => __("Remove from Favorites", 'thrive'),
            'add_to_fav' => __("Mark as Favorite", 'thrive')),
        'progress_status' => array('new' => THRIVE_APPR_PROGRESS_NEW,
            'started' => THRIVE_APPR_PROGRESS_STARTED,
            'completed' => THRIVE_APPR_PROGRESS_COMPLETED)
    );
    wp_localize_script('thrive-appr-main-script', 'ThriveApprentice', $params_array);

    $options = thrive_get_theme_options();
    $options['color_scheme'] = in_array($options['color_scheme'], array('green', 'red', 'purple', 'orange', 'blue')) ? $options['color_scheme'] : 'green';
    $main_css_path = get_template_directory_uri() . '/appr/css/apprentice_' . $options['color_scheme'] . '.css';
    wp_register_style('thrive-apprentice-style', $main_css_path, array("thrive-reset"), '20120208', 'all');
    wp_enqueue_style('thrive-apprentice-style');
}

add_action('wp_enqueue_scripts', 'thrive_appr_enqueue_scripts');

function thrive_appr_enqueue_admin()
{
    $screen = get_current_screen();

    if ($screen->id == "toplevel_page_thrive_admin_page_appr_contents") {

        if (wp_script_is("jquery-ui-sortable")) {
            wp_dequeue_script('jquery-ui-sortable');
        }

        $default_admin_scripts = array("common", "admin-bar", "utils", "svg-painter", "wp-auth-check", "thickbox", "jquery-ui-dialog");

        global $wp_scripts;

        foreach ($wp_scripts->queue as $handle) {
            if (!in_array($handle, $default_admin_scripts)) {
                wp_dequeue_script($handle);
            }
        }

        wp_register_style('thrive-admin-appr-contents', get_template_directory_uri() . '/inc/apprentice/css/admin-appr-contents.css');
        wp_enqueue_style('thrive-admin-appr-contents');

        wp_enqueue_script('jquery-ui-core', false, array('jquery'));

        wp_enqueue_script('jquery-ui-draggable');
        wp_enqueue_script('jquery-ui-droppable');

        //hot fix for membermouse bug
        wp_enqueue_script('jquery-ui-dialog');

        wp_register_script('thrive-jquery-sortable', get_template_directory_uri() . '/inc/apprentice/libs/jquery-sortable.js',
            array('jquery', 'jquery-ui-core', 'jquery-ui-draggable', 'jquery-ui-droppable'));
        wp_enqueue_script('thrive-jquery-sortable');

    }

    if ($screen->id == TT_APPR_POST_TYPE_LESSON || $screen->id == TT_APPR_POST_TYPE_PAGE) {
        wp_register_script('thrive-appr-posts', get_template_directory_uri() . '/inc/apprentice/js/admin-appr-posts.js', array('jquery'));

        $wpnonce = wp_create_nonce("thrive_render_fields_nonce");
        $js_params_array = array('addNewCatUrl' => admin_url('admin-ajax.php?action=thrive_appr_add_new_cat&nonce=' . $wpnonce),
            'noonce' => $wpnonce);

        wp_enqueue_script('thrive-appr-posts', get_template_directory_uri() . '/inc/apprentice/js/admin-appr-posts.js');
        wp_localize_script('thrive-appr-posts', 'ThriveApprLesson', $js_params_array);

    }

    if ($screen->id == "toplevel_page_thrive_admin_options") {
        wp_register_script('thrive-appr-options', get_template_directory_uri() . '/inc/apprentice/js/admin-theme-options.js', array('jquery'));
        wp_enqueue_script('thrive-appr-options');
    }
}

function thrive_appr_create_post_types()
{
    $url_categories = thrive_get_theme_options("appr_url_categories");
    $url_tags = thrive_get_theme_options("appr_url_tags");
    if (empty($url_categories)) {
        $url_categories = "apprentice";
    }
    if (empty($url_tags)) {
        $url_tags = "apprentice-tag";
    }

    //register a new taxonomy for the apprentice structure
    register_taxonomy('apprentice', array(TT_APPR_POST_TYPE_LESSON), array(
        'hierarchical' => true,
        'labels' => array('name' => __("Apprentice Category", 'thrive'),
            'singular_name' => __('Category', 'thrive'),
            'search_items' => __('Search Categories', 'thrive'),
            'all_items' => __('All Categories', 'thrive'),
            'parent_item' => __('Parent Category', 'thrive'),
            'parent_item_colon' => __('Parent Category:', 'thrive'),
            'edit_item' => __('Edit Category', 'thrive'),
            'update_item' => __('Update Category', 'thrive'),
            'add_new_item' => __('Add New Category', 'thrive'),
            'new_item_name' => __('New Category Name', 'thrive'),
            'menu_name' => __('Categories', 'thrive'),
        ),
        'rewrite' => array('slug' => $url_categories),
        'show_in_nav_menus' => true,
        'show_ui' => true
    ));

    register_taxonomy('apprentice-tag', array(TT_APPR_POST_TYPE_LESSON), array(
        'hierarchical' => false,
        'labels' => array('name' => __("Apprentice Tags", 'thrive'),
            'singular_name' => __('Tag', 'thrive'),
            'search_items' => __('Search Tags', 'thrive'),
            'all_items' => __('All Tags', 'thrive'),
            'parent_item' => __('Parent Tag', 'thrive'),
            'parent_item_colon' => __('Parent Tag:', 'thrive'),
            'edit_item' => __('Edit Tag', 'thrive'),
            'update_item' => __('Update Tag', 'thrive'),
            'add_new_item' => __('Add New Tag', 'thrive'),
            'new_item_name' => __('New Tag Name', 'thrive'),
            'menu_name' => __('Tags', 'thrive'),
        ),
        'rewrite' => array('slug' => $url_tags),
        'show_in_nav_menus' => true
    ));

    $url_lessons = thrive_get_theme_options("appr_url_lessons");
    $url_pages = thrive_get_theme_options("appr_url_pages");
    if (empty($url_lessons)) {
        $url_lessons = "lessons";
    }
    if (empty($url_pages)) {
        $url_pages = "members";
    }

    register_post_type(TT_APPR_POST_TYPE_LESSON, array(
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'slug' => 'lessons',
            'with_front' => true,
            'labels' => array(
                'name' => __('Apprentice Lessons'),
                'singular_name' => __('Apprentice Lesson'),
                'add_new' => __('Add New', 'lesson', 'thrive'),
                'add_new_item' => __('Add New Lesson', 'thrive'),
                'new_item' => __('New Lesson', 'thrive'),
                'edit_item' => __('Edit Lesson', 'thrive'),
                'view_item' => __('View Lesson', 'thrive'),
                'all_items' => __('All Lessons', 'thrive'),
            ),
            'has_archive' => true,
            'supports' => array('title', 'editor', 'thumbnail', 'comments', 'author'),
            'rewrite' => array('slug' => $url_lessons, 'with_front' => false),
        )
    );
    register_post_type(TT_APPR_POST_TYPE_PAGE, array(
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'slug' => 'members',
            'labels' => array(
                'name' => __('Apprentice Pages'),
                'singular_name' => __('Apprentice Page'),
                'add_new' => __('Add New', 'thrive'),
                'add_new_item' => __('Add New Apprentice Page', 'thrive'),
                'new_item' => __('New Apprentice Page', 'thrive'),
                'edit_item' => __('Edit Apprentice Page', 'thrive'),
                'view_item' => __('View Apprentice Page', 'thrive'),
                'all_items' => __('All Apprentice Pages', 'thrive'),
            ),
            'has_archive' => true,
            'supports' => array('title', 'editor', 'thumbnail', 'comments', 'author'),
            'rewrite' => array('slug' => $url_pages, 'with_front' => false),
        )
    );

    register_taxonomy_for_object_type('apprentice', TT_APPR_POST_TYPE_LESSON);
    register_taxonomy_for_object_type('apprentice-tag', TT_APPR_POST_TYPE_LESSON);

    if (get_option("thrive-flush-rewrite-required") == 1) {
        flush_rewrite_rules();
        update_option("thrive-flush-rewrite-required", 0);
    }
}

function thrive_init_appr_widgets()
{
    register_widget('Thrive_Appr_Nav');
    register_widget('Thrive_Appr_Recent');
    register_widget('Thrive_Appr_Favorites');
    register_widget('Thrive_Appr_Popular');

    register_sidebar(array(
        'name' => __('Apprentice Sidebar', 'thrive'),
        'id' => 'sidebar-appr',
        'before_widget' => '<section id="%1$s"><div class="scn">',
        'after_widget' => '</div></section>',
        'before_title' => '<p class="ttl">',
        'after_title' => '</p>',
    ));

    register_sidebar(array(
        'name' => __('Apprentice Footer Column 1', 'thrive'),
        'id' => 'footer-appr-1',
        'before_widget' => '<section id="%1$s">',
        'after_widget' => '</div></section>',
        'before_title' => '<p class="ttl">',
        'after_title' => '</p><div class="scn">',
    ));

    register_sidebar(array(
        'name' => __('Apprentice Footer Column 2', 'thrive'),
        'id' => 'footer-appr-2',
        'before_widget' => '<section id="%1$s">',
        'after_widget' => '</div></section>',
        'before_title' => '<p class="ttl">',
        'after_title' => '</p><div class="scn">',
    ));

    register_sidebar(array(
        'name' => __('Apprentice Footer Column 3', 'thrive'),
        'id' => 'footer-appr-3',
        'before_widget' => '<section id="%1$s">',
        'after_widget' => '</div></section>',
        'before_title' => '<p class="ttl">',
        'after_title' => '</p><div class="scn">',
    ));
}

function thrive_admin_page_appr_contents()
{
    $wpnonce = wp_create_nonce("thrive_render_fields_nonce");
    $addNewCatUrl = admin_url('admin-ajax.php?action=thrive_appr_add_new_cat&nonce=' . $wpnonce);
    $deleteCatUrl = admin_url('admin-ajax.php?action=thrive_appr_delete_cat&nonce=' . $wpnonce);
    $saveCatTreeUrl = admin_url('admin-ajax.php?action=thrive_appr_save_cat_tree&nonce=' . $wpnonce);
    $saveLessonsLevelUrl = admin_url('admin-ajax.php?action=thrive_appr_save_lessons_level&nonce=' . $wpnonce);

    $js_params_array = array('addNewCatUrl' => $addNewCatUrl,
        'deleteCatUrl' => $deleteCatUrl,
        'saveCatTreeUrl' => $saveCatTreeUrl,
        'saveLessonsLevelUrl' => $saveLessonsLevelUrl,
        'lessonsLevel' => thrive_appr_get_lessons_level(),
        'noonce' => $wpnonce);

    wp_enqueue_script('thrive-appr-contents', get_template_directory_uri() . '/inc/apprentice/js/admin-appr-contents.js');

    wp_localize_script('thrive-appr-contents', 'ThriveApprOptions', $js_params_array);

    $courses = _thrive_get_ordered_cat_array_by_parent(0);

    $queryPostsArgs = array(
        'posts_per_page' => -1,
        'post_type' => TT_APPR_POST_TYPE_LESSON,
        'tax_query' => array(
            array(
                'taxonomy' => 'apprentice',
                'field' => 'term_id',
                'terms' => 0
            )
        )
    );

    $lessonsLevel = thrive_appr_get_lessons_level();

    $lessonsOrder = json_decode(get_option("thrive_appr_lessons_order"), true);
    //echo "<pre>" . print_r($lessonsOrder, true); die;

    $queryLessonCatsPosts = new WP_Query(array('post_type' => TT_APPR_POST_TYPE_LESSON, 'post_status' => 'publish', 'posts_per_page' => -1));
    $lessons = $queryLessonCatsPosts->get_posts();
    $excludeIds = array();

    //start PilotPress required scripts
    wp_register_style('jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery.ui.all.css');
    wp_enqueue_style('jquery-ui');
    wp_enqueue_script('jquery-ui-tabs');
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script('iris');
    //end PilotPress required scripts

    require(get_template_directory() . "/inc/apprentice/templates/admin-appr-contents.php");
}

function thrive_appr_breadcrumbs()
{

    global $post;

    if (is_home() || is_404()) {
        return;
    }

    $arrowImg = "<span> >> </span>";

    $root_page_id = thrive_get_theme_options("appr_root_page");

    if ($root_page_id != 0 && $homepage_url = get_page_link($root_page_id)) {
        $homepage_id = get_option('page_on_front');
        $homepage_url = get_page_link($root_page_id);
        echo "<li><a class='home' href='" . $homepage_url . "'> " . __("Lessons Home", 'thrive') . $arrowImg . "</a></li>";
    } else {
        echo "<li><a class='home' href='" . get_option('home') . "'> " . __("Home", 'thrive') . $arrowImg . "</a></li>";
    }

    $post_type = get_post_type(get_the_ID());

    if (is_singular() && $post_type == TT_APPR_POST_TYPE_LESSON) {


        $terms = wp_get_post_terms($post->ID, "apprentice");

        if (!empty($terms)) {

            $lessons_level = thrive_appr_get_lessons_level();

            if ($lessons_level == 1) {
                if (isset($terms[0])) {
                    echo "<li><a href=" . get_term_link($terms[0]->term_id, "apprentice") . ">" . $terms[0]->name . "</a></li>";
                }
            } elseif ($lessons_level == 2) {
                if (isset($terms[0])) {
                    $term2 = get_term_by('id', $terms[0]->parent, "apprentice");
                    if ($term2) {
                        echo "<li><a href=" . get_term_link($term2->term_id, "apprentice") . ">" . $term2->name . $arrowImg . "</a></li>";
                    }
                    echo "<li><a href=" . get_term_link($terms[0]->term_id, "apprentice") . ">" . $terms[0]->name . "</a></li>";
                }
            } elseif ($lessons_level == 3) {
                if (isset($terms[0])) {
                    $term2 = get_term_by('id', $terms[0]->parent, "apprentice");
                    if ($term2) {
                        $term3 = get_term_by('id', $term2->parent, "apprentice");
                        if ($term3) {
                            echo "<li><a href=" . get_term_link($term3->term_id, "apprentice") . ">" . $term3->name . $arrowImg . "</a></li>";
                        }
                        echo "<li><a href=" . get_term_link($term2->term_id, "apprentice") . ">" . $term2->name . $arrowImg . "</a></li>";
                    }
                    echo "<li><a href=" . get_term_link($terms[0]->term_id, "apprentice") . ">" . $terms[0]->name . "</a></li>";
                }
            }

        }
        if (empty($terms)) {
            echo "<li>";
        } else {
            echo "<li>" . $arrowImg;
        }
        echo get_the_title();
        echo "</li>";

    } elseif (is_tax("apprentice")) {
        $queried_object = get_queried_object();
        if ($queried_object && isset($queried_object->taxonomy)) {
            $term_id = (int)$queried_object->term_id;
            $taxonomy = get_term($term_id, "apprentice");

            if ($taxonomy->parent > 0 && $tax_parent = get_term($taxonomy->parent, "apprentice")) {
                echo "<li><a href=" . get_term_link($tax_parent->term_id, "apprentice") . ">" . $tax_parent->name . "</a></li>";
            }

            echo '<li>' . $taxonomy->name . '</li>';
        }

    } elseif (is_category() || is_single()) {
        $cats = get_the_category($post->ID);
        if (!empty($cats)) {
            if (isset($cats[0])) {
                echo "<li><a href=" . get_category_link($cats[0]->term_id) . ">" . $cats[0]->cat_name . "</a></li>";
            }
        }
        if (is_single()) {
            if (!isset($cats[0])) {
                $arrowImg = "";
            }
            echo "<li>" . $arrowImg;
            echo get_the_title();
            echo "</li>";
        }
    } elseif (is_page()) {
        if ($post->post_parent) {
            $anc = get_post_ancestors($post->ID);
            $anc_link = get_page_link($post->post_parent);

            foreach ($anc as $ancestor) {
                $output = "<li><a href=" . $anc_link . ">" . get_the_title($ancestor) . " " . $arrowImg . "</a></li>";
            }
            echo $output . "<li>";
            the_title();
            echo "</li>";
        } else {
            echo '<li>';
            echo the_title();
            echo "</li>";
        }
    } elseif (is_tag()) {
        echo '<li>' . single_tag_title('', false) . '</li>';
    } elseif (is_day()) {
        echo '<li>' . __("Archive", 'thrive') . ": ";
        the_time('F jS, Y');
        echo '</li>';
    } elseif (is_month()) {
        echo '<li>' . __("Archive", 'thrive') . ": ";
        the_time('F, Y');
        echo '</li>';
    } elseif (is_year()) {
        echo '<li>' . __("Archive", 'thrive') . ": ";
        the_time('Y');
        echo '</li>';
    } elseif (is_author()) {
        echo '<li>' . __("Author's archive", 'thrive') . ": ";
        echo '</li>';
    } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {
        echo '<li>' . __("Archive", 'thrive') . ": </li>";
        echo '';
    } elseif (is_search()) {
        echo '<li>' . __("Search results", 'thrive') . ": </li>";
    } elseif (is_archive()) {
        echo '<li>' . __("Archive", 'thrive') . ": </li>";
    }
    return;
}

function thrive_appr_add_new_cat()
{
    if (!wp_verify_nonce($_REQUEST['nonce'], "thrive_render_fields_nonce")) {
        echo 0;
        die;
    }
    if (!isset($_POST['cat_name']) || !isset($_POST['cat_slug']) || empty($_POST['cat_name'])) {
        echo 0;
        die;
    }

    $cat_name = sanitize_text_field($_POST['cat_name']);
    $cat_slug = sanitize_text_field($_POST['cat_slug']);
    $cat_description = sanitize_text_field($_POST['cat_description']);
    $parent_id = (int)$_POST['parent_id'];

    $result = wp_insert_term($cat_name, 'apprentice', array(
        'description' => $cat_description,
        'slug' => $cat_slug,
        'parent' => $parent_id
    ));

    if ($result && isset($result['term_id'])) {
        echo $result['term_id'];
    } else {
        echo 0;
    }

    die;
}

function thrive_appr_delete_cat()
{
    if (!wp_verify_nonce($_REQUEST['nonce'], "thrive_render_fields_nonce")) {
        echo 0;
        die;
    }
    if (!isset($_POST['cat_id'])) {
        echo 0;
        die;
    }
    $term_id = (int)$_POST['cat_id'];
    wp_delete_term($term_id, "apprentice");
    echo 1;
    die;
}

function thrive_appr_save_cat_tree_item($item, $item_id, $parent_id = 0)
{
    if ($item['type'] == 'lesson') {
        global $lessonsOrder;
        $item_order = (isset($item['order']) && ($item['order'] - 1) >= 0) ? $item['order'] - 1 : 0;
        if (!isset($lessonsOrder[$parent_id])) {
            $lessonsOrder[$parent_id] = array($item_order => $item_id);
        } else {
            $lessonsOrder[$parent_id][$item_order] = $item_id;
        }
        wp_set_post_terms($item_id, $parent_id, 'apprentice');
    } else {
        global $lessonsOrder;

        $item_order = (isset($item['order'])) ? $item['order'] : 0;

        if (!isset($lessonsOrder[$item_id])) {
            $lessonsOrder[$item_id] = array('order' => $item_order);
        }

        wp_update_term($item_id, 'apprentice', array('parent' => $parent_id));
        if (!empty($item['children'])) {
            foreach ($item['children'] as $child_id => $child_item) {
                thrive_appr_save_cat_tree_item($child_item, $child_id, $item_id);
            }
        }
    }
}

//array to hold the lessons order
$lessonsOrder = array();

function thrive_appr_save_cat_tree()
{
    if (!wp_verify_nonce($_REQUEST['nonce'], "thrive_render_fields_nonce")) {
        echo 0;
        die;
    }
    if (!isset($_POST['cat_tree'])) {
        echo 0;
        die;
    }

    if (empty($_POST['cat_tree'])) {
        echo 0;
        die;
    }

    foreach ($_POST['cat_tree'] as $id => $_item) {
        //first level => categories
        thrive_appr_save_cat_tree_item($_item, $id, 0);
    }
    if (!empty($_POST['unassigned_lessons'])) {
        foreach ($_POST['unassigned_lessons'] as $lesson_id) {
            wp_delete_object_term_relationships($lesson_id, 'apprentice');
        }
    }
    //save the lessons order object
    global $lessonsOrder;
    foreach ($lessonsOrder as $key => $orderArray) {
        ksort($lessonsOrder[$key]);
    }
    update_option("thrive_appr_lessons_order", json_encode($lessonsOrder));

    echo 1;
    die;
}

function thrive_appr_save_lessons_level()
{
    if (!isset($_POST['lessons_level'])) {
        echo 0;
        die;
    }
    $lessonsLevel = (int)$_POST['lessons_level'];
    $previousLessonsLevel = thrive_appr_get_lessons_level();

    if ($lessonsLevel != $previousLessonsLevel) {
        update_option("thrive_appr_lessons_order", json_encode(array()));
        $all_cats = get_terms("apprentice", 'hide_empty=0');

        foreach ($all_cats as $cat) {
            if ($cat->term_id == 3) {
                wp_update_term($cat->term_id, "apprentice", array("parent" => 0));
            }
            wp_update_term($cat->term_id, "apprentice", array("parent" => 0));
        }

        $queryLessons = new WP_Query(array('post_type' => TT_APPR_POST_TYPE_LESSON, 'posts_per_page' => 100));
        $allLessons = $queryLessons->get_posts();

        foreach ($allLessons as $lesson) {
            wp_delete_object_term_relationships($lesson->ID, 'apprentice');
        }
    }

    update_option("thrive_appr_lessons_level", $lessonsLevel);
    echo 1;
    die;
}

function thrive_appr_get_lessons_level()
{
    $lessonsLevel = get_option("thrive_appr_lessons_level");
    if (!$lessonsLevel || empty($lessonsLevel)) {
        return 1; //default lessons level
    }
    return $lessonsLevel;
}

/*
 * Overwrites the rendering of the default template file for 
 * the apprentice sections
 */

function thrive_appr_template_redirect()
{

    $template_page_name = NULL;
    if (!is_singular()) {
        if (is_tax("apprentice")) {
            $template_page_name = 'appr/taxonomy-apprentice.php';
            include TEMPLATEPATH . '/' . $template_page_name;
            exit;
        } else {
            return false;
        }
    }

    $post_type = get_post_type();

    switch ($post_type) {
        case TT_APPR_POST_TYPE_LESSON:
            $template_page_name = 'appr/appr-lesson.php';
            break;
        case TT_APPR_POST_TYPE_PAGE:
            $template_page_name = 'appr/appr-page.php';
            break;
        default:
            return false; //exit if not apprentice page
    }

    $post_template = _thrive_get_item_template(get_the_ID());

    switch ($post_template) {
        case "Full Width" :
            $template_page_name = 'appr/appr-full-width.php';
            break;
        case "Narrow" :
            $template_page_name = 'appr/appr-narrow.php';
            break;
        case "Landing Page" :
            $template_page_name = 'appr/appr-landing-page.php';
            break;
    }


    if ($template_page_name !== NULL) {
        include TEMPLATEPATH . '/' . $template_page_name;
        exit;
    }
}

//add_action('template_redirect', 'thrive_appr_template_redirect');

function thrive_appr_add_to_favorites()
{
    if (!is_user_logged_in()) {
        echo 0;
        die;
    }
    $post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
    global $current_user;
    get_currentuserinfo();
    if (_thrive_appr_check_favorite($post_id, $current_user->ID)) {
        _thrive_appr_remove_favorite($post_id, $current_user->ID);
        echo 1;
    } else {
        _thrive_appr_add_favorite($post_id, $current_user->ID);
        echo 2;
    }
    die;
}

function thrive_appr_set_progress()
{
    if (!is_user_logged_in()) {
        echo 0;
        die;
    }
    if (!isset($_POST['status']) || ($_POST['status'] != THRIVE_APPR_PROGRESS_NEW && $_POST['status'] != THRIVE_APPR_PROGRESS_STARTED && $_POST['status'] != THRIVE_APPR_PROGRESS_COMPLETED)) {
        echo 0;
        die;
    }
    $post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
    global $current_user;
    get_currentuserinfo();

    _thrive_appr_set_progress($post_id, $current_user->ID, $_POST['status']);

    echo $post_id;
    die;
}

/*
 * Adds the lessons in the loop
 */

function thrive_appr_include_lessons($query)
{
    if (is_author())
        $query->set('post_type', array('post', TT_APPR_POST_TYPE_LESSON));

    remove_action('pre_get_posts', 'thrive_appr_include_lessons');

    return $query;
}

/*
 * Get the template files for apprentice contents
 */
function thrive_appr_get_apprentice_single_templates($single_template) {
    global $post;

    if ($post->post_type == TT_APPR_POST_TYPE_LESSON || $post->post_type == TT_APPR_POST_TYPE_PAGE) {

        $single_template = TEMPLATEPATH . "/appr/appr-lesson.php";

        if ($post->post_type == TT_APPR_POST_TYPE_PAGE) {
            $single_template = TEMPLATEPATH . "/appr/appr-page.php";
        }

        $post_template = _thrive_get_item_template(get_the_ID());

        switch ($post_template) {
            case "Full Width" :
                $single_template = TEMPLATEPATH . '/appr/appr-full-width.php';
                break;
            case "Narrow" :
                $single_template = TEMPLATEPATH . '/appr/appr-narrow.php';
                break;
            case "Landing Page" :
                $single_template = TEMPLATEPATH . '/appr/appr-landing-page.php';
                break;
        }

    }
    return $single_template;
}
add_filter( 'single_template', 'thrive_appr_get_apprentice_single_templates' );

function thrive_appr_get_apprentice_tax_templates($tax_template) {
    if (is_tax("apprentice")) {
        $tax_template = TEMPLATEPATH . '/appr/taxonomy-apprentice.php';
    }
    return $tax_template;
}
add_filter( 'taxonomy_template', 'thrive_appr_get_apprentice_tax_templates' );


