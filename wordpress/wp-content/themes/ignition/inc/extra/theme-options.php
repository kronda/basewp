<?php

function thrive_settings_field_header_phone($value) {
    $checked_on = ($value == 1) ? "checked" : "";
    $checked_off = ($value == 0) ? "checked" : "";
    echo "<input class='toggle toggle-left thrive_header_phone_chk' type='radio' id='header_phone_true' name='thrive_theme_options[header_phone]' value='1' $checked_on />
    <label for='header_phone_true' class='btn'>" . __('On', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right thrive_header_phone_chk' type='radio' id='header_phone_false' name='thrive_theme_options[header_phone]' value='0' $checked_off />
    <label for='header_phone_false' class='btn'>" . __('Off', 'thrive') . "</label>";
}

function thrive_settings_field_header_phone_no($value) {
    echo "<input type='text' name='thrive_theme_options[header_phone_no]' id='thrive_theme_options_header_phone_no' value='" . esc_attr($value) . "' />";
}

function thrive_settings_field_header_phone_text($value) {    
    echo "<input value='" . esc_attr($value) . "' type='text' name='thrive_theme_options[header_phone_text]' id='thrive_theme_options_header_phone_text' />";
}

function thrive_settings_field_header_phone_text_mobile($value) {    
    echo "<input value='" . esc_attr($value) . "' type='text' name='thrive_theme_options[header_phone_text_mobile]' id='thrive_theme_options_header_phone_text_mobile' />";
}


function thrive_settings_field_header_phone_btn_color($value) {   
    $colors = _thrive_get_color_scheme_options();
    $colors = array_merge(array('default' => __("Default", 'thrive')), $colors);
    echo "<select name='thrive_theme_options[header_phone_btn_color]' id='thrive_theme_options_header_phone_btn_color'>";
    foreach ($colors as $key => $c) {
        $is_selected = ($key == $value) ? " selected" : "";
        echo "<option value='" . $key . "'" . $is_selected . ">" . $c . "</option>";
    }
    echo "</select> <a href='#' id='thrive_btn_preview_header_phone'>Preview</a>";

    echo "<div id='thrive_preview_header_phone_container'></div>";
}

function thrive_settings_field_featured_title_bg_type($value) {
    $checked_img = ($value == "image") ? "checked" : "";
    $checked_color = ($value == "color") ? "checked" : "";
    
    echo "<input class='toggle toggle-left thrive_featured_title_bg_type' type='radio' id='featured_title_bg_type_image' name='thrive_theme_options[featured_title_bg_type]' value='image' $checked_img />
    <label for='featured_title_bg_type_image' class='btn'>" . __('Image', 'thrive') . "</label>";
    echo "<input class='toggle toggle-right thrive_featured_title_bg_type' type='radio' id='featured_title_bg_type_color' name='thrive_theme_options[featured_title_bg_type]' value='color' $checked_color />
    <label for='featured_title_bg_type_color' class='btn'>" . __('Color', 'thrive') . "</label>";
}

function thrive_settings_field_featured_title_bg_solid_color($value) {
    $scheme_options = thrive_get_default_customizer_options();
    echo "<input type='text' name='thrive_theme_options[featured_title_bg_solid_color]' id='thrive_theme_options_featured_title_bg_solid_color'
        value='" . esc_attr($value) . "' data-default-color='" . $scheme_options['thrivetheme_link_color'] . "' />";
    echo "<input type='button' id='thrive_theme_options_featured_title_bg_solid_color_reset' value='" . __("Clear", 'thrive') . "' />";
}

function thrive_settings_field_featured_title_bg_img_static($value) {
    $checked_default = ($value != 'static') ? "checked" : "";
    $checked_static = ($value == 'static') ? "checked" : "";
    echo "<input class='thrive_featured_title_bg_img_static' type='radio' name='thrive_theme_options[featured_title_bg_img_static]' value='default' $checked_default /> " . __('Default', 'thrive') . "";
    echo "<input class='thrive_featured_title_bg_img_static' type='radio' name='thrive_theme_options[featured_title_bg_img_static]' value='static' $checked_static /> " . __('Static', 'thrive') . "";
}

function thrive_settings_field_featured_title_bg_img_full_height($value) {
    $checked_off = ($value != 'on') ? "checked" : ""; 
    $checked_on = ($value == 'on') ? "checked" : "";   
    echo "<input class='thrive_featured_title_bg_img_full_height' type='radio' name='thrive_theme_options[featured_title_bg_img_full_height]' value='off' $checked_off /> " . __('Off', 'thrive') . "";
    echo "<input class='thrive_featured_title_bg_img_full_height' type='radio' name='thrive_theme_options[featured_title_bg_img_full_height]' value='on' $checked_on /> " . __('On', 'thrive') . "";
}

function thrive_settings_field_featured_title_bg_img_trans($value) {
    $scheme_options = thrive_get_default_customizer_options();
    echo "<input type='text' name='thrive_theme_options[featured_title_bg_img_trans]' data-default-color='" . $scheme_options['thrivetheme_link_color'] . "' 
        id='thrive_theme_options_featured_title_bg_img_trans' value='" . esc_attr($value) . "' />";
    echo "<input type='button' id='thrive_theme_options_featured_title_bg_img_trans_reset' value='" . __("Clear", 'thrive') . "' />";
}

function thrive_settings_field_404_custom_text($value) {
    wp_editor($value, 'thrive_theme_options_404_custom_text', array('textarea_name' => 'thrive_theme_options[404_custom_text]'));
}

function thrive_settings_field_404_display_sitemap($value) {
    $checked_off = ($value != 'on') ? "checked" : ""; 
    $checked_on = ($value == 'on') ? "checked" : "";   
    echo "<input class='thrive_404_display_sitemap' type='radio' name='thrive_theme_options[404_display_sitemap]' value='off' $checked_off /> " . __('Off', 'thrive') . "";
    echo "<input class='thrive_404_display_sitemap' type='radio' name='thrive_theme_options[404_display_sitemap]' value='on' $checked_on /> " . __('On', 'thrive') . "";
}


function thrive_settings_field_related_no_text($value) {    
    echo "<input value='" . esc_attr($value) . "' type='text' name='thrive_theme_options[related_no_text]' id='thrive_theme_options_related_no_text' />";
}

function thrive_settings_field_related_number_posts($value) {
    echo "<select id='thrive_theme_options_related_number_posts' name='thrive_theme_options[related_number_posts]'>";
    for($index = 5; $index <= 20; $index++) {
        $selected_txt = ($value == $index) ? " selected" : "";
        echo "<option" . $selected_txt . ">" . $index . "</option>";        
    }
    echo "</select>";
}
    

function thrive_settings_field_related_ignore_cats($value) {
    $all_categories = get_categories();
    $categories_array = array();

    foreach ($all_categories as $cat) {
        array_push($categories_array, array('id' => $cat->cat_ID, 'name' => $cat->cat_name));
    }
    $values_array = ($value == '' ? array() : json_decode($value));
    echo "<select id='sel_thrive_theme_options_related_ignore_cats' style='width:300px;' multiple>";
    foreach ($categories_array as $cat) {
        $selected_txt = (in_array($cat['id'], $values_array)) ? " selected" : "";
        echo "<option value='" . $cat['id'] . "'" . $selected_txt . ">" . $cat['name'] . "</option>";
    }    
    echo "</selected>";
    echo "<input type='hidden' id='thrive_theme_options_related_ignore_cats' name='thrive_theme_options[related_ignore_cats]' value='" . $value . "' />";
}

function thrive_settings_field_related_ignore_tags($value) {
    $all_tags = get_tags();
    $tags_array = array();

    foreach ($all_tags as $tag) {
        array_push($tags_array, array('id' => $tag->term_id, 'name' => $tag->name));
    }
    $values_array = ($value == '' ? array() : json_decode($value));
    echo "<select id='sel_thrive_theme_options_related_ignore_tags' style='width:300px;' multiple>";
    foreach ($tags_array as $tag) {
        $selected_txt = (in_array($tag['id'], $values_array)) ? " selected" : "";
        echo "<option value='" . $tag['id'] . "'" . $selected_txt . ">" . $tag['name'] . "</option>";
    }    
    echo "</selected>";
    echo "<input type='hidden' id='thrive_theme_options_related_ignore_tags' name='thrive_theme_options[related_ignore_tags]' value='" . $value . "' />";
}
    
?>