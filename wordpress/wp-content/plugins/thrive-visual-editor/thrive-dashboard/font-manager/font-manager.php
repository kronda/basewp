<?php

//font options for admin part
add_action("wp_ajax_display_font_manager", "thrive_display_font_options");
add_action("wp_ajax_thrive_font_manager_delete", "thrive_font_manager_delete");
add_action("wp_ajax_thrive_font_manager_add", "thrive_font_manager_add");
add_action("wp_ajax_thrive_font_manager_edit", "thrive_font_manager_edit");
add_action("wp_ajax_thrive_font_manager_duplicate", "thrive_font_manager_duplicate");
add_action("wp_ajax_thrive_font_manager_update_posts_fonts", "thrive_font_manager_update_posts_fonts");

if (!function_exists('thrive_display_font_options')) {
    function thrive_display_font_options()
    {
        include(plugin_dir_path(__FILE__) . '/admin-font-options.php');
        die;
    }
}

if (!function_exists('thrive_font_manager_delete')) {
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
        update_option('thrive_font_manager_options', json_encode($old_options));
        die;
    }
}

if (!function_exists('thrive_font_manager_add')) {
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
}

if (!function_exists('thrive_font_manager_edit')) {
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
}

if (!function_exists('thrive_font_manager_duplicate')) {
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
}

if (!function_exists('thrive_font_manager')) {
    /**
     * Includes the Font Manager entry point file
     */
    function thrive_font_manager()
    {
        $font_options = is_array(json_decode(get_option('thrive_font_manager_options'), true)) ? json_decode(get_option('thrive_font_manager_options'), true) : array();
        $last_option = end($font_options);
        $new_font_id = intval($last_option['font_id']) + 1;

        include dirname(__FILE__) . '/admin-font-manager.php';
    }
}

if (!function_exists('thrive_font_manager_update_posts_fonts')) {
    function thrive_font_manager_update_posts_fonts()
    {
        $posts = get_posts();
        foreach ($posts as $post) {

            $post_id = $post->ID;
            $post_content = $post->post_content;
            preg_match_all("/thrive_custom_font id='\d+'/", $post_content, $font_ids);

            $post_fonts = array();
            foreach ($font_ids[0] as $font_id) {
                $parts = explode("'", $font_id);
                $id = $parts[1];
                $font = thrive_get_font_options($id);
                $post_fonts[] = "//fonts.googleapis.com/css?family=" . str_replace(" ", "+", $font->font_name) . ($font->font_style != 0 ? ":" . $font->font_style : "") . ($font->font_italic ? "" . $font->font_italic : "") . ($font->font_bold != 0 ? "," . $font->font_bold : "") . ($font->font_character_set != 0 ? "&subset=" . $font->font_character_set : "");
            }
            update_post_meta($post_id, 'thrive_post_fonts', sanitize_text_field(json_encode($post_fonts)));
        }
        die();
    }
}

function thrive_dashboard_enqueue_font_manager()
{
    wp_enqueue_style('thickbox');

    wp_enqueue_script('thickbox');

    wp_enqueue_style('thrive-theme-options', thrive_dashboard_url() . 'css/theme-options.css');

    wp_enqueue_style('thrive-admin-colors', thrive_dashboard_url() . 'css/thrive-admin-colors.css');

    wp_enqueue_style('thrive-base-css', thrive_dashboard_url() . 'css/pure-base.css');
    wp_enqueue_style('thrive-pure-css', thrive_dashboard_url() . 'css/pure.css');

    wp_enqueue_script('wp-color-picker');
    wp_enqueue_style('wp-color-picker');
}

if (!function_exists('_thrive_get_font_family_array')) {

    function _thrive_get_font_family_array($font_name = null)
    {
        if ($font_name === false) {
            return false;
        }
        $font_name = str_replace(" ", "", trim($font_name));
        $fonts = array('AbrilFatface' => "font-family: 'Abril Fatface', cursive;",
            'Amatic SC' => "font-family: 'Amatic SC', cursive;",
            'Archivo Black' => "font-family: 'Archivo Black', sans-serif;",
            'Arbutus Slab' => "font-family: 'Arbutus Slab', serif;",
            'Archivo Narrow' => "font-family: 'Archivo Narrow', sans-serif;",
            'Arial' => "font-family: 'Arial';",
            'Arimo' => "font-family: 'Arimo', sans-serif;",
            'Arvo' => "font-family: 'Arvo', serif;",
            'Boogaloo' => "font-family: 'Boogaloo', cursive;",
            'Calligraffitti' => "font-family: 'Calligraffitti', cursive;",
            'CantataOne' => "font-family: 'Cantata One', serif;",
            'Cardo' => "font-family: 'Cardo', serif;",
            'Cutive' => "font-family: 'Cutive', serif;",
            'DaysOne' => "font-family: 'Days One', sans-serif;",
            'Dosis' => "font-family: 'Dosis', sans-serif;",
            'Droid Sans' => "font-family: 'Droid Sans', sans-serif;",
            'Droid Serif' => "font-family: 'Droid Serif', sans-serif;",
            'FjallaOne' => "font-family: 'Fjalla One', sans-serif;",
            'FrancoisOne' => "font-family: 'Francois One', sans-serif;",
            'Georgia' => "font-family: 'Georgia';",
            'GravitasOne' => "font-family: 'Gravitas One', cursive;",
            'Helvetica' => "font-family: 'Helvetica';",
            'JustAnotherHand' => "font-family: 'Just Another Hand', cursive;",
            'Josefin Sans' => "font-family: 'Josefin Sans', sans-serif;",
            'Josefin Slab' => "font-family: 'Josefin Slab', serif;",
            'Lobster' => "font-family: 'Lobster', cursive;",
            'Lato' => "font-family: 'Lato', sans-serif;",
            'Montserrat' => "font-family: 'Montserrat', sans-serif;",
            'NotoSans' => "font-family: 'Noto Sans', sans-serif;",
            'OleoScript' => "font-family: 'Oleo Script', cursive;",
            'Old Standard TT' => "font-family: 'Old Standard TT', serif;",
            'Open Sans' => "font-family: 'Open Sans', sans-serif;",
            'Oswald' => "font-family: 'Oswald', sans-serif;",
            'OpenSansCondensed' => "font-family: 'Open Sans Condensed', sans-serif;",
            'Oxygen' => "font-family: 'Oxygen', sans-serif;",
            'Pacifico' => "font-family: 'Pacifico', cursive;",
            'Playfair Display' => "font-family: 'Playfair Display', serif;",
            'Poiret One' => "font-family: 'Poiret One', cursive;",
            'PT Sans' => "font-family: 'PT Sans', sans-serif;",
            'PT Serif' => "font-family: 'PT Serif', sans-serif;",
            'Raleway' => "font-family: 'Raleway', sans-serif;",
            'Roboto' => "font-family: 'Roboto', sans-serif;",
            'Roboto Condensed' => "font-family: 'Roboto Condensed', sans-serif;",
            'Roboto Slab' => "font-family: 'Roboto Slab', serif;",
            'ShadowsIntoLightTwo' => "font-family: 'Shadows Into Light Two', cursive;",
            'Source Sans Pro' => "font-family: 'Source Sans Pro', sans-serif;",
            'Sorts Mill Gaudy' => "font-family: 'Sorts Mill Gaudy', cursive;",
            'SpecialElite' => "font-family: 'Special Elite', cursive;",
            'Tahoma' => "font-family: 'Tahoma';",
            'TimesNewRoman' => "font-family: 'Times New Roman';",
            'Ubuntu' => "font-family: 'Ubuntu', sans-serif;",
            'Ultra' => "font-family: 'Ultra', serif;",
            'VarelaRound' => "font-family: 'Varela Round', sans-serif;",
            'Verdana' => "font-family: 'Verdana';",
            'Vollkorn' => "font-family: 'Vollkorn', serif;",);

        if ($font_name) {
            if (isset($fonts[$font_name])) {
                return $fonts[$font_name];
            } else {
                return false;
            }
        }
        return $fonts;
    }
}
