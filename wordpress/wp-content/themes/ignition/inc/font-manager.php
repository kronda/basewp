<?php

//font options for admin part
add_action("wp_ajax_display_font_manager", "thrive_display_font_options");

function thrive_display_font_options()
{
    include(get_template_directory() . '/inc/templates/admin-font-options.php');
    die;
}

add_action("wp_ajax_thrive_font_manager_delete", "thrive_font_manager_delete");

function thrive_font_manager_delete()
{
    $font_id = $_REQUEST['font_id'];
    $old_options = json_decode(get_option('thrive_font_manager_options'), true);
    $delete_key = -1;
    foreach ($old_options as $key => $font) {
        if ($font['font_id'] == $font_id) {
            $delete_key = $key;
        }
    }
    if ($delete_key != -1) {
        unset($old_options[$delete_key]);
    }
    $old_options = array_values($old_options);
    update_option('thrive_font_manager_options', json_encode($old_options));
    die;
}

add_action("wp_ajax_thrive_font_manager_add", "thrive_font_manager_add");

function thrive_font_manager_add()
{
    $options = get_option('thrive_font_manager_options');
    $option = array(
        'font_name' => $_REQUEST['font_name'],
        'font_style' => $_REQUEST['font_style'],
        'font_bold' => $_REQUEST['font_bold'],
        'font_italic' => $_REQUEST['font_italic'],
        'font_character_set' => $_REQUEST['font_character_set'],
        'font_class' => $_REQUEST['font_class'],
        'font_size' => $_REQUEST['font_size'],
        'font_height' => $_REQUEST['font_height'],
        'font_color' => $_REQUEST['font_color'],
        'custom_css' => $_REQUEST['custom_css']
    );
    if ($options == false || count(json_decode($options), true) == 0) {
        //we don't have any other options saved
        $option['font_id'] = 1;
        update_option('thrive_font_manager_options', json_encode(array($option)));
    } else {
        $old_options = json_decode(get_option('thrive_font_manager_options'), true);
        $last_option = end($old_options);
        $option['font_id'] = $last_option['font_id'] + 1;
        $old_options[] = $option;
        update_option('thrive_font_manager_options', json_encode($old_options));
    }
    die;
}

add_action("wp_ajax_thrive_font_manager_edit", "thrive_font_manager_edit");

function thrive_font_manager_edit()
{
    $old_options = json_decode(get_option('thrive_font_manager_options'), true);
    foreach ($old_options as $key => $font) {
        if ($font['font_id'] == intval($_REQUEST['font_id'])) {
            $old_options[$key]['font_name'] = $_REQUEST['font_name'];
            $old_options[$key]['font_style'] = $_REQUEST['font_style'];
            $old_options[$key]['font_bold'] = $_REQUEST['font_bold'];
            $old_options[$key]['font_italic'] = $_REQUEST['font_italic'];
            $old_options[$key]['font_character_set'] = $_REQUEST['font_character_set'];
            $old_options[$key]['font_class'] = $_REQUEST['font_class'];
            $old_options[$key]['font_size'] = $_REQUEST['font_size'];
            $old_options[$key]['font_height'] = $_REQUEST['font_height'];
            $old_options[$key]['font_color'] = $_REQUEST['font_color'];
            $old_options[$key]['custom_css'] = $_REQUEST['custom_css'];
        }
    }
    update_option('thrive_font_manager_options', json_encode($old_options));
    die;
}

add_action("wp_ajax_thrive_font_manager_duplicate", "thrive_font_manager_duplicate");

function thrive_font_manager_duplicate()
{
    $font_id = $_REQUEST['font_id'];
    $old_options = json_decode(get_option('thrive_font_manager_options'), true);
    $option = null;
    foreach ($old_options as $key => $font) {
        if ($font['font_id'] == $font_id) {
            $option = $font;
        }
    }
    if ($option) {
        $last_option = end($old_options);
        $option['font_id'] = intval($last_option['font_id']) + 1;
        $option['font_class'] = 'ttfm' . $option['font_id'];
        $old_options[] = $option;
    }
    update_option('thrive_font_manager_options', json_encode($old_options));
    die;
}

function thrive_font_manager()
{
    $font_options = is_array(json_decode(get_option('thrive_font_manager_options'), true)) ? json_decode(get_option('thrive_font_manager_options'), true) : array();
    $last_option = end($font_options);
    $new_font_id = intval($last_option['font_id']) + 1;
    include(get_template_directory() . '/inc/templates/admin-font-manager.php');
}

add_action("wp_ajax_thrive_font_manager_update_posts_fonts", "thrive_font_manager_update_posts_fonts");

function thrive_font_manager_update_posts_fonts()
{
    $posts = get_posts(array(
        'posts_per_page' => -1
    ));

    foreach ($posts as $post) {

        $post_id = $post->ID;
        $post_content = $post->post_content;
        preg_match_all("/thrive_custom_font id='\d+'/", $post_content, $font_ids);

        $post_fonts = array();
        foreach ($font_ids[0] as $font_id) {
            $parts = explode("'", $font_id);
            $id = $parts[1];
            $font = thrive_get_font_options($id);
            if (thrive_font_manager_is_safe_font($font->font_name)) {
                continue;
            }
            if(Thrive_Font_Import_Manager::isImportedFont($font->font_name)) {
                $post_fonts[] = Thrive_Font_Import_Manager::getCssFile();
                continue;
            }
            $post_fonts[] = "//fonts.googleapis.com/css?family=" . str_replace(" ", "+", $font->font_name) . ($font->font_style != 0 ? ":" . $font->font_style : "") . ($font->font_italic ? "" . $font->font_italic : "") . ($font->font_bold != 0 ? "," . $font->font_bold : "") . ($font->font_character_set != 0 ? "&subset=" . $font->font_character_set : "");
        }
        $post_fonts = array_unique($post_fonts);
        update_post_meta($post_id, 'thrive_post_fonts', sanitize_text_field(json_encode($post_fonts)));
    }
    die;
}

if (!function_exists('thrive_font_manager_get_safe_fonts')) {

    function thrive_font_manager_get_safe_fonts()
    {
        return $safe_fonts = array(
            array(
                'family' => 'Georgia, serif',
                'variants' => array('regular', 'italic', '600'),
                'subsets' => array('latin'),
            ),
            array(
                'family' => 'Palatino Linotype, Book Antiqua, Palatino, serif',
                'variants' => array('regular', 'italic', '600'),
                'subsets' => array('latin'),
            ),
            array(
                'family' => 'Times New Roman, Times, serif',
                'variants' => array('regular', 'italic', '600'),
                'subsets' => array('latin'),
            ),
            array(
                'family' => 'Arial, Helvetica, sans-serif',
                'variants' => array('regular', 'italic', '600'),
                'subsets' => array('latin'),
            ),
            array(
                'family' => 'Arial Black, Gadget, sans-serif',
                'variants' => array('regular', 'italic', '600'),
                'subsets' => array('latin'),
            ),
            array(
                'family' => 'Comic Sans MS, cursive, sans-serif',
                'variants' => array('regular', 'italic', '600'),
                'subsets' => array('latin'),
            ),
            array(
                'family' => 'Impact, Charcoal, sans-serif',
                'variants' => array('regular', 'italic', '600'),
                'subsets' => array('latin'),
            ),
            array(
                'family' => 'Lucida Sans Unicode, Lucida Grande, sans-serif',
                'variants' => array('regular', 'italic', '600'),
                'subsets' => array('latin'),
            ),
            array(
                'family' => 'Tahoma, Geneva, sans-serif',
                'variants' => array('regular', 'italic', '600'),
                'subsets' => array('latin'),
            ),
            array(
                'family' => 'Trebuchet MS, Helvetica, sans-serif',
                'variants' => array('regular', 'italic', '600'),
                'subsets' => array('latin'),
            ),
            array(
                'family' => 'Verdana, Geneva, sans-serif',
                'variants' => array('regular', 'italic', '600'),
                'subsets' => array('latin'),
            ),
            array(
                'family' => 'Courier New, Courier, monospace',
                'variants' => array('regular', 'italic', '600'),
                'subsets' => array('latin'),
            ),
            array(
                'family' => 'Lucida Console, Monaco, monospace',
                'variants' => array('regular', 'italic', '600'),
                'subsets' => array('latin'),
            ),
        );
    }
}

function thrive_font_manager_is_safe_font($font_family)
{
    foreach (thrive_font_manager_get_safe_fonts() as $font) {
        if ($font_family === $font['family']) {
            return true;
        }
    }

    return false;
}

function thrive_font_manager_get_safe_font($font_family)
{
    foreach (thrive_font_manager_get_safe_fonts() as $font) {
        if ($font['family'] === $font_family) {
            return $font;
        }
    }

    return array();
}
