<?php

add_action('add_meta_boxes', 'thrive_add_custom_fields');

add_action('save_post', 'thrive_save_postdata');

function thrive_add_custom_fields() {

    add_meta_box('thrive_post_options', __('Thrive Theme Options', 'thrive'), 'thrive_meta_post_options', "post");
    add_meta_box('thrive_page_options', __('Thrive Theme Options', 'thrive'), 'thrive_meta_page_options', "page");
    add_meta_box('thrive_focus_options', __('Build Your Focus Area', 'thrive'), 'thrive_meta_focusarea_options', "focus_area", "advanced", "high");
    add_meta_box('thrive_optin_options', __('Build Your Optin Form', 'thrive'), 'thrive_meta_optin_options', "thrive_optin", "advanced", "high");
    add_meta_box('thrive_focus_display_options', __('Thrive Focus Area Display Options', 'thrive'), 'thrive_meta_focusarea_display_options', "focus_area", "side");
}

function thrive_meta_post_options($post) {

    wp_nonce_field(plugin_basename(__FILE__), 'thrive_noncename');

    $post_templates = array('Default', 'Full Width', 'Landing Page', 'Narrow');

    //get the focus areas    
    $queryFocusAreas = new WP_Query("post_type=focus_area&order=ASC&posts_per_page=-1");

    $value_post_template = get_post_meta($post->ID, '_thrive_meta_post_template', true);
    $value_show_post_title = get_post_meta($post->ID, '_thrive_meta_show_post_title', true);
    if ($value_show_post_title != 0 || $value_show_post_title == "") {
        $value_show_post_title = 1;
    }
    $value_post_meta_info = get_post_meta($post->ID, '_thrive_meta_post_meta_info', true);
    $value_post_bradcrumbs = get_post_meta($post->ID, '_thrive_meta_post_breadcrumbs', true);
    $value_post_featured_image = get_post_meta($post->ID, '_thrive_meta_post_featured_image', true);
    $value_post_header_scripts = get_post_meta($post->ID, '_thrive_meta_post_header_scripts', true);
    $value_post_body_scripts = get_post_meta($post->ID, '_thrive_meta_post_body_scripts', true);
    $value_post_body_scripts_top = get_post_meta($post->ID, '_thrive_meta_post_body_scripts_top', true);
    $value_post_custom_css = get_post_meta($post->ID, '_thrive_meta_post_custom_css', true);
    $value_post_focus_area_top = get_post_meta($post->ID, '_thrive_meta_post_focus_area_top', true);
    $value_post_focus_area_bottom = get_post_meta($post->ID, '_thrive_meta_post_focus_area_bottom', true);
    if ($value_post_bradcrumbs != "on" && $value_post_bradcrumbs != "off") {
        $value_post_bradcrumbs = "default";
    }
    if ($value_post_meta_info != "on" && $value_post_meta_info != "off") {
        $value_post_meta_info = "default";
    }
    if ($value_post_featured_image != "thumbnail" && $value_post_featured_image != "wide" && $value_post_featured_image != "off") {
        $value_post_featured_image = "default";
    }
    $value_post_share_buttons = get_post_meta($post->ID, '_thrive_meta_post_share_buttons', true);
	$value_post_floating_icons = get_post_meta($post->ID, '_thrive_meta_post_floating_icons', true);
	if ($value_post_floating_icons != "on" && $value_post_floating_icons != "off") {
		$value_post_floating_icons = "default";
	}

    $thrive_meta_social_data_title = get_post_meta($post->ID, '_thrive_meta_social_data_title', true);
    $thrive_meta_social_data_description = get_post_meta($post->ID, '_thrive_meta_social_data_description', true);
    $thrive_meta_social_image = get_post_meta($post->ID, '_thrive_meta_social_image', true);
    $thrive_meta_social_twitter_username = get_post_meta($post->ID, '_thrive_meta_social_twitter_username', true);
    $value_post_related_box = get_post_meta($post->ID, '_thrive_meta_post_related_box', true);


    require ( get_template_directory() . "/inc/templates/admin-post-options.php" );
}

function thrive_meta_page_options($page) {

    wp_nonce_field(plugin_basename(__FILE__), 'thrive_noncename');

    //get the focus areas    
    $queryFocusAreas = new WP_Query("post_type=focus_area&order=ASC&posts_per_page=-1");

    $value_show_post_title = get_post_meta($page->ID, '_thrive_meta_show_post_title', true);

    if ($value_show_post_title != 0 || $value_show_post_title == "") {
        $value_show_post_title = 1;
    }
    $value_post_bradcrumbs = get_post_meta($page->ID, '_thrive_meta_post_breadcrumbs', true);
    $value_post_featured_image = get_post_meta($page->ID, '_thrive_meta_post_featured_image', true);
    $value_post_header_scripts = get_post_meta($page->ID, '_thrive_meta_post_header_scripts', true);
    $value_post_body_scripts = get_post_meta($page->ID, '_thrive_meta_post_body_scripts', true);
    $value_post_body_scripts_top = get_post_meta($page->ID, '_thrive_meta_post_body_scripts_top', true);
    $value_post_custom_css = get_post_meta($page->ID, '_thrive_meta_post_custom_css', true);
    $value_post_focus_area_top = get_post_meta($page->ID, '_thrive_meta_post_focus_area_top', true);
    $value_post_focus_area_bottom = get_post_meta($page->ID, '_thrive_meta_post_focus_area_bottom', true);
    if ($value_post_bradcrumbs != "on" && $value_post_bradcrumbs != "off") {
        $value_post_bradcrumbs = "default";
    }
    if ($value_post_featured_image != "thumbnail" && $value_post_featured_image != "wide" && $value_post_featured_image != "off") {
        $value_post_featured_image = "default";
    }
    $value_post_share_buttons = get_post_meta($page->ID, '_thrive_meta_post_share_buttons', true);
	$value_post_floating_icons = get_post_meta($page->ID, '_thrive_meta_post_floating_icons', true);
	if ($value_post_floating_icons != "on" && $value_post_floating_icons != "off") {
		$value_post_floating_icons = "default";
	}
    $thrive_meta_social_data_title = get_post_meta($page->ID, '_thrive_meta_social_data_title', true);
    $thrive_meta_social_data_description = get_post_meta($page->ID, '_thrive_meta_social_data_description', true);
    $thrive_meta_social_image = get_post_meta($page->ID, '_thrive_meta_social_image', true);
    $thrive_meta_social_twitter_username = get_post_meta($page->ID, '_thrive_meta_social_twitter_username', true);

    require ( get_template_directory() . "/inc/templates/admin-page-options.php" );
}

function thrive_meta_focusarea_options($page) {

    wp_enqueue_script("thrive-focus-options");
    wp_enqueue_style("thrive-admin-focus");
    wp_enqueue_style("thrive-admin-focustemplates");
    wp_enqueue_style('thrive-admin-responsivefocus');



    wp_nonce_field(plugin_basename(__FILE__), 'thrive_noncename');

    $focus_templates = array('1' => 'Template 1', '2' => 'Template 2', '4' => 'Template 4', '5' => 'Template 5', '6' => 'Template 6', '0' => 'Custom Template');

    $focus_colors = _thrive_get_color_scheme_options("focusareas");
    $ribbon_colors = _thrive_get_color_scheme_options("ribbon_focus_area");

    $value_focus_template = get_post_meta($page->ID, '_thrive_meta_focus_template', true);
    $value_focus_color = get_post_meta($page->ID, '_thrive_meta_focus_color', true);
    $value_ribbon_color = get_post_meta($page->ID, '_thrive_meta_ribbon_color', true);
    $value_focus_heading_text = get_post_meta($page->ID, '_thrive_meta_focus_heading_text', true);
    $value_focus_ribbon_text = get_post_meta($page->ID, '_thrive_meta_focus_ribbon_text', true);
    $value_focus_subheading_text = get_post_meta($page->ID, '_thrive_meta_focus_subheading_text', true);
    $value_focus_button_text = get_post_meta($page->ID, '_thrive_meta_focus_button_text', true);
    $value_focus_button_link = get_post_meta($page->ID, '_thrive_meta_focus_button_link', true);
    $value_focus_button_color = get_post_meta($page->ID, '_thrive_meta_focus_button_color', true);
    $value_focus_new_tab = get_post_meta($page->ID, '_thrive_meta_focus_new_tab', true);
    $value_focus_spam_text = get_post_meta($page->ID, '_thrive_meta_focus_spam_text', true);
    $value_focus_image = get_post_meta($page->ID, '_thrive_meta_focus_image', true);
    $value_focus_optin = get_post_meta($page->ID, '_thrive_meta_focus_optin', true);
    $value_focus_custom_text = get_post_meta($page->ID, '_thrive_meta_focus_custom_text', true);

    if (!$value_focus_template || $value_focus_template == "") {
        $value_focus_template = "template1";
    }

    $queryOptins = new WP_Query("post_type=thrive_optin&order=ASC&post_status=publish&posts_per_page=-1");

    //prepare the javascript params
    $wpnonce = wp_create_nonce("thrive_preview_focus_nonce");
    $focusPreviewUrl = admin_url('admin-ajax.php?action=focus_render_preview&nonce=' . $wpnonce);

    $js_params_array = array('focusPreviewUrl' => $focusPreviewUrl,
        'noonce' => $wpnonce,
        'id_post' => $page->ID);
    wp_localize_script('thrive-focus-options', 'ThriveFocusOptions', $js_params_array);

    $button_colors = _thrive_get_color_scheme_options("buttons");

    require ( get_template_directory() . "/inc/templates/admin-focus-area.php" );
}

function thrive_meta_optin_options($page) {

    wp_enqueue_script("thrive-optin-options");
    wp_nonce_field(plugin_basename(__FILE__), 'thrive_noncename');

    $value_optin_autoresponder_code = get_post_meta($page->ID, '_thrive_meta_optin_autoresponder_code', true);
    $value_optin_autoresponder_code = htmlentities($value_optin_autoresponder_code);

    //prepare the javascript params
    $wpnonce = wp_create_nonce("thrive_render_fields_nonce");
    $renderFieldsUrl = admin_url('admin-ajax.php?action=optin_render_fields&nonce=' . $wpnonce);
    $saveFieldsUrl = admin_url('admin-ajax.php?action=optin_save_field_labels&nonce=' . $wpnonce);
    $js_params_array = array(
        'renderFieldsUrl' => $renderFieldsUrl,
        'saveFieldsUrl' => $saveFieldsUrl,
        'noonce' => $wpnonce,
        'id_post' => $page->ID,
        'stuff' => 'ole');
    wp_localize_script('thrive-optin-options', 'ThriveOptinOptions', $js_params_array);

    require ( get_template_directory() . "/inc/templates/admin-optin-options.php" );
}

function thrive_meta_focusarea_display_options($page) {

    $value_focus_display_location = get_post_meta($page->ID, '_thrive_meta_focus_display_location', true);
    $value_focus_display_post_type = get_post_meta($page->ID, '_thrive_meta_focus_display_post_type', true);
    $value_focus_display_is_default = get_post_meta($page->ID, '_thrive_meta_focus_display_is_default', true);
    $value_focus_display_categories = json_decode(get_post_meta($page->ID, '_thrive_meta_focus_display_categories', true));
    $value_focus_page_blog = get_post_meta($page->ID, '_thrive_meta_focus_page_blog', true);
    $value_focus_page_archive = get_post_meta($page->ID, '_thrive_meta_focus_page_archive', true);
    if (!is_array($value_focus_display_categories)) {
        $value_focus_display_categories = array();
    }
    $all_categories = get_categories();
    $categories_array = array();

    foreach ($all_categories as $cat) {
        array_push($categories_array, array('id' => $cat->cat_ID, 'name' => $cat->cat_name));
    }

    require ( get_template_directory() . "/inc/templates/admin-focus-area-display.php" );
}

/* When the post is saved, saves our custom data */

function thrive_save_postdata($post_id) {

    if (!current_user_can('edit_post', $post_id))
        return;

    // Secondly we need to check if the user intended to change this value.
    if (!isset($_POST['thrive_noncename']) || !wp_verify_nonce($_POST['thrive_noncename'], plugin_basename(__FILE__)))
        return;

    if ('page' == $_POST['post_type']) {
        _thrive_save_page_options($_POST);
    } elseif ('focus_area' == $_POST['post_type']) {
        _thrive_save_focus_options($_POST);
    } elseif ('post' == $_POST['post_type'] || TT_APPR_POST_TYPE_LESSON == $_POST['post_type']
            || TT_APPR_POST_TYPE_PAGE == $_POST['post_type']) {
        _thrive_save_post_options($_POST);
    } elseif ('thrive_optin' == $_POST['post_type']) {
        _thrive_save_optin_options($_POST);
    }
}

function _thrive_save_post_options($post_data) {

    $post_ID = $post_data['post_ID'];
    //sanitize user input
    $thrive_meta_show_post_title = sanitize_text_field($post_data['thrive_meta_show_post_title']);
    $thrive_meta_post_template = sanitize_text_field($post_data['thrive_meta_post_template']);

    $thrive_meta_post_meta_info = sanitize_text_field($post_data['thrive_meta_post_meta_info']);
    $thrive_meta_post_breadcrumbs = sanitize_text_field($post_data['thrive_meta_post_breadcrumbs']);
    $thrive_meta_post_featured_image = sanitize_text_field($post_data['thrive_meta_post_featured_image']);
    $thrive_meta_post_header_scripts = ($post_data['thrive_meta_post_header_scripts']);
    $thrive_meta_post_body_scripts = ($post_data['thrive_meta_post_body_scripts']);
    $thrive_meta_post_body_scripts_top = ($post_data['thrive_meta_post_body_scripts_top']);
    $thrive_meta_post_custom_css = ($post_data['thrive_meta_post_custom_css']);
    $thrive_meta_post_share_buttons = ($post_data['thrive_meta_post_share_buttons']);
	$thrive_meta_post_floating_icons = ($post_data['thrive_meta_post_floating_icons']);

    $thrive_meta_social_data_title = ($post_data['thrive_meta_social_data_title']);
    $thrive_meta_social_data_description = ($post_data['thrive_meta_social_data_description']);
    $thrive_meta_social_image = ($post_data['thrive_meta_social_image']);
    $thrive_meta_social_twitter_username = ($post_data['thrive_meta_social_twitter_username']);
    $thrive_meta_post_related_box = isset($post_data['thrive_meta_post_related_box']) ? $post_data['thrive_meta_post_related_box'] : 0;

    if ($post_data['thrive_meta_post_focus_area_top'] == "default" || $post_data['thrive_meta_post_focus_area_top'] == "hide") {
        $thrive_meta_post_focust_area_top = $post_data['thrive_meta_post_focus_area_top'];
    } else if ($post_data['thrive_meta_post_focus_area_top'] == "custom") {
        $thrive_meta_post_focust_area_top = is_numeric($post_data['thrive_meta_post_focus_area_top_select']) ? $post_data['thrive_meta_post_focus_area_top_select'] : "default";
    } else {
        $thrive_meta_post_focust_area_top = "default";
    }

    if ($post_data['thrive_meta_post_focus_area_bottom'] == "default" || $post_data['thrive_meta_post_focus_area_bottom'] == "hide") {
        $thrive_meta_post_focust_area_bottom = $post_data['thrive_meta_post_focus_area_bottom'];
    } else if ($post_data['thrive_meta_post_focus_area_bottom'] == "custom") {
        $thrive_meta_post_focust_area_bottom = is_numeric($post_data['thrive_meta_post_focus_area_bottom_select']) ? $post_data['thrive_meta_post_focus_area_bottom_select'] : "default";
    } else {
        $thrive_meta_post_focust_area_bottom = "default";
    }

    add_post_meta($post_ID, '_thrive_meta_social_data_title', $thrive_meta_social_data_title, true) or
            update_post_meta($post_ID, '_thrive_meta_social_data_title', $thrive_meta_social_data_title);

    add_post_meta($post_ID, '_thrive_meta_social_data_description', $thrive_meta_social_data_description, true) or
            update_post_meta($post_ID, '_thrive_meta_social_data_description', $thrive_meta_social_data_description);

    add_post_meta($post_ID, '_thrive_meta_social_image', $thrive_meta_social_image, true) or
            update_post_meta($post_ID, '_thrive_meta_social_image', $thrive_meta_social_image);

    add_post_meta($post_ID, '_thrive_meta_social_twitter_username', $thrive_meta_social_twitter_username, true) or
            update_post_meta($post_ID, '_thrive_meta_social_twitter_username', $thrive_meta_social_twitter_username);


    add_post_meta($post_ID, '_thrive_meta_post_share_buttons', $thrive_meta_post_share_buttons, true) or
            update_post_meta($post_ID, '_thrive_meta_post_share_buttons', $thrive_meta_post_share_buttons);

	add_post_meta($post_ID, '_thrive_meta_post_floating_icons', $thrive_meta_post_floating_icons, true) or
	update_post_meta($post_ID, '_thrive_meta_post_floating_icons', $thrive_meta_post_floating_icons);

    add_post_meta($post_ID, '_thrive_meta_post_template', $thrive_meta_post_template, true) or
            update_post_meta($post_ID, '_thrive_meta_post_template', $thrive_meta_post_template);

    add_post_meta($post_ID, '_thrive_meta_show_post_title', $thrive_meta_show_post_title, true) or
            update_post_meta($post_ID, '_thrive_meta_show_post_title', $thrive_meta_show_post_title);

    add_post_meta($post_ID, '_thrive_meta_post_meta_info', $thrive_meta_post_meta_info, true) or
            update_post_meta($post_ID, '_thrive_meta_post_meta_info', $thrive_meta_post_meta_info);

    add_post_meta($post_ID, '_thrive_meta_post_breadcrumbs', $thrive_meta_post_breadcrumbs, true) or
            update_post_meta($post_ID, '_thrive_meta_post_breadcrumbs', $thrive_meta_post_breadcrumbs);

    add_post_meta($post_ID, '_thrive_meta_post_featured_image', $thrive_meta_post_featured_image, true) or
            update_post_meta($post_ID, '_thrive_meta_post_featured_image', $thrive_meta_post_featured_image);

    add_post_meta($post_ID, '_thrive_meta_post_header_scripts', $thrive_meta_post_header_scripts, true) or
            update_post_meta($post_ID, '_thrive_meta_post_header_scripts', $thrive_meta_post_header_scripts);

    add_post_meta($post_ID, '_thrive_meta_post_body_scripts', $thrive_meta_post_body_scripts, true) or
            update_post_meta($post_ID, '_thrive_meta_post_body_scripts', $thrive_meta_post_body_scripts);

    add_post_meta($post_ID, '_thrive_meta_post_body_scripts_top', $thrive_meta_post_body_scripts_top, true) or
            update_post_meta($post_ID, '_thrive_meta_post_body_scripts_top', $thrive_meta_post_body_scripts_top);

    add_post_meta($post_ID, '_thrive_meta_post_custom_css', $thrive_meta_post_custom_css, true) or
            update_post_meta($post_ID, '_thrive_meta_post_custom_css', $thrive_meta_post_custom_css);

    add_post_meta($post_ID, '_thrive_meta_post_focus_area_top', $thrive_meta_post_focust_area_top, true) or
            update_post_meta($post_ID, '_thrive_meta_post_focus_area_top', $thrive_meta_post_focust_area_top);

    add_post_meta($post_ID, '_thrive_meta_post_focus_area_bottom', $thrive_meta_post_focust_area_bottom, true) or
            update_post_meta($post_ID, '_thrive_meta_post_focus_area_bottom', $thrive_meta_post_focust_area_bottom);

    add_post_meta($post_ID, '_thrive_meta_post_related_box', $thrive_meta_post_related_box, true) or
            update_post_meta($post_ID, '_thrive_meta_post_related_box', $thrive_meta_post_related_box);
}

function _thrive_save_page_options($post_data) {
    $page_ID = $post_data['post_ID'];
    //sanitize user input
    $checkList = array('thrive_meta_show_post_title', 'thrive_meta_post_breadcrumbs', 'thrive_meta_post_featured_image', 'thrive_meta_post_header_scripts', 'thrive_meta_post_body_scripts',
        'thrive_meta_post_body_scripts_top', 'thrive_meta_post_custom_css', 'thrive_meta_post_share_buttons', 'thrive_meta_post_floating_icons', 'thrive_meta_social_data_title',
        'thrive_meta_social_data_description', 'thrive_meta_social_image', 'thrive_meta_social_twitter_username', 'thrive_meta_post_focus_area_top', 'thrive_meta_post_focus_area_bottom');
    foreach ($checkList as $checkKey) {
        $post_data[$checkKey] = isset($post_data[$checkKey]) ? $post_data[$checkKey] : "";
    }

    $thrive_meta_show_post_title = sanitize_text_field($post_data['thrive_meta_show_post_title']);
    $thrive_meta_post_breadcrumbs = sanitize_text_field($post_data['thrive_meta_post_breadcrumbs']);
    $thrive_meta_post_featured_image = sanitize_text_field($post_data['thrive_meta_post_featured_image']);
    $thrive_meta_post_header_scripts = ($post_data['thrive_meta_post_header_scripts']);
    $thrive_meta_post_body_scripts = ($post_data['thrive_meta_post_body_scripts']);
    $thrive_meta_post_body_scripts_top = ($post_data['thrive_meta_post_body_scripts_top']);
    $thrive_meta_post_custom_css = ($post_data['thrive_meta_post_custom_css']);
    $thrive_meta_post_share_buttons = ($post_data['thrive_meta_post_share_buttons']);
	$thrive_meta_post_floating_icons = ($post_data['thrive_meta_post_floating_icons']);

    $thrive_meta_social_data_title = ($post_data['thrive_meta_social_data_title']);
    $thrive_meta_social_data_description = ($post_data['thrive_meta_social_data_description']);
    $thrive_meta_social_image = ($post_data['thrive_meta_social_image']);
    $thrive_meta_social_twitter_username = ($post_data['thrive_meta_social_twitter_username']);

    if ($post_data['thrive_meta_post_focus_area_top'] == "default" || $post_data['thrive_meta_post_focus_area_top'] == "hide") {
        $thrive_meta_post_focust_area_top = $post_data['thrive_meta_post_focus_area_top'];
    } else if ($post_data['thrive_meta_post_focus_area_top'] == "custom") {
        $thrive_meta_post_focust_area_top = is_numeric($post_data['thrive_meta_post_focus_area_top_select']) ? $post_data['thrive_meta_post_focus_area_top_select'] : "default";
    } else {
        $thrive_meta_post_focust_area_top = "default";
    }

    if ($post_data['thrive_meta_post_focus_area_bottom'] == "default" || $post_data['thrive_meta_post_focus_area_bottom'] == "hide") {
        $thrive_meta_post_focust_area_bottom = $post_data['thrive_meta_post_focus_area_bottom'];
    } else if ($post_data['thrive_meta_post_focus_area_bottom'] == "custom") {
        $thrive_meta_post_focust_area_bottom = is_numeric($post_data['thrive_meta_post_focus_area_bottom_select']) ? $post_data['thrive_meta_post_focus_area_bottom_select'] : "default";
    } else {
        $thrive_meta_post_focust_area_bottom = "default";
    }

    add_post_meta($page_ID, '_thrive_meta_social_data_title', $thrive_meta_social_data_title, true) or
            update_post_meta($page_ID, '_thrive_meta_social_data_title', $thrive_meta_social_data_title);

    add_post_meta($page_ID, '_thrive_meta_social_data_description', $thrive_meta_social_data_description, true) or
            update_post_meta($page_ID, '_thrive_meta_social_data_description', $thrive_meta_social_data_description);

    add_post_meta($page_ID, '_thrive_meta_social_image', $thrive_meta_social_image, true) or
            update_post_meta($page_ID, '_thrive_meta_social_image', $thrive_meta_social_image);

    add_post_meta($page_ID, '_thrive_meta_social_twitter_username', $thrive_meta_social_twitter_username, true) or
            update_post_meta($page_ID, '_thrive_meta_social_twitter_username', $thrive_meta_social_twitter_username);

    add_post_meta($page_ID, '_thrive_meta_post_share_buttons', $thrive_meta_post_share_buttons, true) or
            update_post_meta($page_ID, '_thrive_meta_post_share_buttons', $thrive_meta_post_share_buttons);

	add_post_meta($page_ID, '_thrive_meta_post_floating_icons', $thrive_meta_post_floating_icons, true) or
	update_post_meta($page_ID, '_thrive_meta_post_floating_icons', $thrive_meta_post_floating_icons);

    add_post_meta($page_ID, '_thrive_meta_show_post_title', $thrive_meta_show_post_title, true) or
            update_post_meta($page_ID, '_thrive_meta_show_post_title', $thrive_meta_show_post_title);

    add_post_meta($page_ID, '_thrive_meta_post_breadcrumbs', $thrive_meta_post_breadcrumbs, true) or
            update_post_meta($page_ID, '_thrive_meta_post_breadcrumbs', $thrive_meta_post_breadcrumbs);

    add_post_meta($page_ID, '_thrive_meta_post_featured_image', $thrive_meta_post_featured_image, true) or
            update_post_meta($page_ID, '_thrive_meta_post_featured_image', $thrive_meta_post_featured_image);

    add_post_meta($page_ID, '_thrive_meta_post_header_scripts', $thrive_meta_post_header_scripts, true) or
            update_post_meta($page_ID, '_thrive_meta_post_header_scripts', $thrive_meta_post_header_scripts);

    add_post_meta($page_ID, '_thrive_meta_post_body_scripts', $thrive_meta_post_body_scripts, true) or
            update_post_meta($page_ID, '_thrive_meta_post_body_scripts', $thrive_meta_post_body_scripts);

    add_post_meta($page_ID, '_thrive_meta_post_body_scripts_top', $thrive_meta_post_body_scripts_top, true) or
            update_post_meta($page_ID, '_thrive_meta_post_body_scripts_top', $thrive_meta_post_body_scripts_top);

    add_post_meta($page_ID, '_thrive_meta_post_custom_css', $thrive_meta_post_custom_css, true) or
            update_post_meta($page_ID, '_thrive_meta_post_custom_css', $thrive_meta_post_custom_css);


    add_post_meta($page_ID, '_thrive_meta_post_focus_area_top', $thrive_meta_post_focust_area_top, true) or
            update_post_meta($page_ID, '_thrive_meta_post_focus_area_top', $thrive_meta_post_focust_area_top);

    add_post_meta($page_ID, '_thrive_meta_post_focus_area_bottom', $thrive_meta_post_focust_area_bottom, true) or
            update_post_meta($page_ID, '_thrive_meta_post_focus_area_bottom', $thrive_meta_post_focust_area_bottom);
}

function _thrive_save_focus_options($post_data) {

    $page_ID = $post_data['post_ID'];
    $check_fields = array(
        'thrive_meta_focus_template',
        'thrive_meta_ribbon_color',
        'thrive_meta_focus_color',
        'thrive_meta_focus_heading_text',
        'thrive_meta_focus_ribbon_text',
        'thrive_meta_focus_subheading_text',
        'thrive_meta_focus_button_text',
        'thrive_meta_focus_button_link',
        'thrive_meta_focus_button_color',
        'thrive_meta_focus_new_tab',
        'thrive_meta_focus_spam_text',
        'thrive_meta_focus_image',
        'thrive_meta_focus_optin',
        'thrive_meta_focus_custom_text',
        'thrive_meta_focus_display_between_posts',
        'thrive_meta_focus_display_location',
        'thrive_meta_focus_display_post_type',
        'thrive_meta_focus_display_is_default',
        'thrive_meta_focus_display_categories'
    );
    foreach ($check_fields as $field) {
        $post_data[$field] = isset($post_data[$field]) ? $post_data[$field] : '';
    }
    //sanitize user input
    $thrive_meta_focus_template = sanitize_text_field($post_data['thrive_meta_focus_template']);
    $thrive_meta_focus_color = sanitize_text_field($post_data['thrive_meta_focus_color']);
    $thrive_meta_ribbon_color = sanitize_text_field($post_data['thrive_meta_ribbon_color']);
    $thrive_meta_focus_heading_text = sanitize_text_field($post_data['thrive_meta_focus_heading_text']);
    $thrive_meta_focus_ribbon_text = sanitize_text_field($post_data['thrive_meta_focus_ribbon_text']);
    $thrive_meta_focus_subheading_text = ($post_data['thrive_meta_focus_subheading_text']);
    $thrive_meta_focus_button_text = sanitize_text_field($post_data['thrive_meta_focus_button_text']);
    $thrive_meta_focus_button_link = sanitize_text_field($post_data['thrive_meta_focus_button_link']);
    $thrive_meta_focus_button_color = sanitize_text_field($post_data['thrive_meta_focus_button_color']);
    $thrive_meta_focus_new_tab = sanitize_text_field($post_data['thrive_meta_focus_new_tab']);
    $thrive_meta_focus_spam_text = sanitize_text_field($post_data['thrive_meta_focus_spam_text']);
    $thrive_meta_focus_image = sanitize_text_field($post_data['thrive_meta_focus_image']);
    $thrive_meta_focus_optin = sanitize_text_field($post_data['thrive_meta_focus_optin']);
    $thrive_meta_focus_custom_text = ($post_data['thrive_meta_focus_custom_text']);
    $thrive_meta_focus_page_blog = (isset($post_data['thrive_meta_focus_page_blog'])) ? $post_data['thrive_meta_focus_page_blog'] : "";
    $thrive_meta_focus_page_archive = (isset($post_data['thrive_meta_focus_page_archive'])) ? $post_data['thrive_meta_focus_page_archive'] : "";

    $thrive_meta_focus_display_location = sanitize_text_field($post_data['thrive_meta_focus_display_location']);
    $thrive_meta_focus_display_post_type = sanitize_text_field($post_data['thrive_meta_focus_display_post_type']);
    $thrive_meta_focus_display_is_default = sanitize_text_field($post_data['thrive_meta_focus_display_is_default']);

    $thrive_meta_focus_display_categories = $post_data['thrive_meta_focus_display_categories'];


    add_post_meta($page_ID, '_thrive_meta_focus_page_blog', $thrive_meta_focus_page_blog, true) or
    update_post_meta($page_ID, '_thrive_meta_focus_page_blog', $thrive_meta_focus_page_blog);

    add_post_meta($page_ID, '_thrive_meta_focus_page_archive', $thrive_meta_focus_page_archive, true) or
    update_post_meta($page_ID, '_thrive_meta_focus_page_archive', $thrive_meta_focus_page_archive);

    add_post_meta($page_ID, '_thrive_meta_focus_display_categories', $thrive_meta_focus_display_categories, true) or
            update_post_meta($page_ID, '_thrive_meta_focus_display_categories', $thrive_meta_focus_display_categories);

    add_post_meta($page_ID, '_thrive_meta_focus_display_location', $thrive_meta_focus_display_location, true) or
            update_post_meta($page_ID, '_thrive_meta_focus_display_location', $thrive_meta_focus_display_location);

    add_post_meta($page_ID, '_thrive_meta_focus_display_post_type', $thrive_meta_focus_display_post_type, true) or
            update_post_meta($page_ID, '_thrive_meta_focus_display_post_type', $thrive_meta_focus_display_post_type);

    add_post_meta($page_ID, '_thrive_meta_focus_display_is_default', $thrive_meta_focus_display_is_default, true) or
            update_post_meta($page_ID, '_thrive_meta_focus_display_is_default', $thrive_meta_focus_display_is_default);

    add_post_meta($page_ID, '_thrive_meta_focus_template', $thrive_meta_focus_template, true) or
            update_post_meta($page_ID, '_thrive_meta_focus_template', $thrive_meta_focus_template);

    add_post_meta($page_ID, '_thrive_meta_focus_color', $thrive_meta_focus_color, true) or
            update_post_meta($page_ID, '_thrive_meta_focus_color', $thrive_meta_focus_color);

    add_post_meta($page_ID, '_thrive_meta_ribbon_color', $thrive_meta_ribbon_color, true) or
            update_post_meta($page_ID, '_thrive_meta_ribbon_color', $thrive_meta_ribbon_color);

    add_post_meta($page_ID, '_thrive_meta_focus_heading_text', $thrive_meta_focus_heading_text, true) or
            update_post_meta($page_ID, '_thrive_meta_focus_heading_text', $thrive_meta_focus_heading_text);

    add_post_meta($page_ID, '_thrive_meta_focus_ribbon_text', $thrive_meta_focus_ribbon_text, true) or
            update_post_meta($page_ID, '_thrive_meta_focus_ribbon_text', $thrive_meta_focus_ribbon_text);

    add_post_meta($page_ID, '_thrive_meta_focus_subheading_text', $thrive_meta_focus_subheading_text, true) or
            update_post_meta($page_ID, '_thrive_meta_focus_subheading_text', $thrive_meta_focus_subheading_text);

    add_post_meta($page_ID, '_thrive_meta_focus_button_text', $thrive_meta_focus_button_text, true) or
            update_post_meta($page_ID, '_thrive_meta_focus_button_text', $thrive_meta_focus_button_text);

    add_post_meta($page_ID, '_thrive_meta_focus_button_link', $thrive_meta_focus_button_link, true) or
            update_post_meta($page_ID, '_thrive_meta_focus_button_link', $thrive_meta_focus_button_link);

    add_post_meta($page_ID, '_thrive_meta_focus_button_color', $thrive_meta_focus_button_color, true) or
            update_post_meta($page_ID, '_thrive_meta_focus_button_color', $thrive_meta_focus_button_color);

    add_post_meta($page_ID, '_thrive_meta_focus_new_tab', $thrive_meta_focus_new_tab, true) or
            update_post_meta($page_ID, '_thrive_meta_focus_new_tab', $thrive_meta_focus_new_tab);

    add_post_meta($page_ID, '_thrive_meta_focus_image', $thrive_meta_focus_image, true) or
            update_post_meta($page_ID, '_thrive_meta_focus_image', $thrive_meta_focus_image);

    add_post_meta($page_ID, '_thrive_meta_focus_spam_text', $thrive_meta_focus_spam_text, true) or
            update_post_meta($page_ID, '_thrive_meta_focus_spam_text', $thrive_meta_focus_spam_text);

    add_post_meta($page_ID, '_thrive_meta_focus_optin', $thrive_meta_focus_optin, true) or
            update_post_meta($page_ID, '_thrive_meta_focus_optin', $thrive_meta_focus_optin);

    add_post_meta($page_ID, '_thrive_meta_focus_custom_text', $thrive_meta_focus_custom_text, true) or
            update_post_meta($page_ID, '_thrive_meta_focus_custom_text', $thrive_meta_focus_custom_text);
}

function _thrive_save_optin_options($post_data) {

    $page_ID = $post_data['post_ID'];
    //sanitize user input
    $thrive_meta_optin_autoresponder_code = ($post_data['thrive_meta_optin_autoresponder_code']);

    add_post_meta($page_ID, '_thrive_meta_optin_autoresponder_code', $thrive_meta_optin_autoresponder_code, true) or
            update_post_meta($page_ID, '_thrive_meta_optin_autoresponder_code', $thrive_meta_optin_autoresponder_code);
}

add_action("wp_ajax_nopriv_focus_render_preview", "thrive_focus_render_preview");
add_action("wp_ajax_focus_render_preview", "thrive_focus_render_preview");

function thrive_focus_render_preview() {

    if (!wp_verify_nonce($_REQUEST['nonce'], "thrive_preview_focus_nonce")) {
        echo 0;
        die;
    }
    if (!isset($_POST['id_post'])) {
        $current_focus = array();
    } else {
        $current_focus = get_post($_POST['id_post']);
    }

    $current_attrs = get_post_custom($current_focus->ID);
    if (!$current_attrs || !isset($current_attrs['_thrive_meta_focus_template']) || !isset($current_attrs['_thrive_meta_focus_template'][0])) {
        $current_attrs = array();
    }

    //overwrite the attributes
    $current_attrs['_thrive_meta_focus_color'][0] = $_POST['thrive_meta_focus_color'];
    $current_attrs['_thrive_meta_focus_template'][0] = $_POST['thrive_meta_focus_template'];
    $current_attrs['_thrive_meta_focus_optin'][0] = $_POST['thrive_meta_focus_optin'];
    $current_attrs['_thrive_meta_focus_heading_text'][0] = stripslashes($_POST['thrive_meta_focus_heading_text']);
    $current_attrs['_thrive_meta_focus_subheading_text'][0] = apply_filters('the_content', stripslashes($_POST['thrive_meta_focus_subheading_text']));
    $current_attrs['_thrive_meta_focus_image'][0] = $_POST['thrive_meta_focus_image'];
    $current_attrs['_thrive_meta_focus_button_link'][0] = $_POST['thrive_meta_focus_button_link'];
    $current_attrs['_thrive_meta_focus_button_text'][0] = stripslashes($_POST['thrive_meta_focus_button_text']);
    $current_attrs['_thrive_meta_focus_spam_text'][0] = stripslashes($_POST['thrive_meta_focus_spam_text']);
    $current_attrs['_thrive_meta_focus_button_color'][0] = $_POST['thrive_meta_focus_button_color'];
    $current_attrs['_thrive_meta_focus_custom_text'][0] = $_POST['thrive_meta_focus_custom_text'];
    $current_attrs['_thrive_meta_focus_new_tab'][0] = empty($_POST['thrive_meta_focus_new_tab']) ? 0 : 1;
    //echo '<pre>' . print_r($_POST, true); die;
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
    if (ob_get_contents()) {
        ob_clean();
    }

    if (!isset($current_attrs['_thrive_meta_focus_template']) || !isset($current_attrs['_thrive_meta_focus_template'][0]) || $current_attrs['_thrive_meta_focus_template'][0] == 'undefined') {
        $current_attrs['_thrive_meta_focus_template'][0] = 'custom';
    }

    $position = "top";

    $template_path = get_template_directory() . "/focusareas/" . strtolower($current_attrs['_thrive_meta_focus_template'][0]) . ".php";
    require_once $template_path;
    die;
}

add_action("wp_ajax_nopriv_optin_render_fields", "thrive_optin_render_fields");
add_action("wp_ajax_optin_render_fields", "thrive_optin_render_fields");

function thrive_optin_render_fields() {
    if (!wp_verify_nonce($_REQUEST['nonce'], "thrive_render_fields_nonce")) {
        echo 0;
        die;
    }
    if (!isset($_POST['id_post']) || !isset($_POST['autoresponder_code'])) {
        echo 0;
        die;
    }
    $current_optin = get_post($_POST['id_post']);
    if (!$current_optin) {
        echo 0;
        die;
    }

    $autoresponder_code = stripslashes($_POST['autoresponder_code']);
    $parsed_responder_code = _thrive_parse_autoresponder_code($autoresponder_code);

    if ($parsed_responder_code['parse_status'] == 0 || empty($parsed_responder_code['elements'])) {
        echo 0;
        die;
    }

    //save form action
    update_post_meta($_POST['id_post'], '_thrive_meta_optin_form_action', $parsed_responder_code['form_action']);

    //save form method
    update_post_meta($_POST['id_post'], '_thrive_meta_optin_form_method', $parsed_responder_code['form_method']);

    //save hidden inputs
    update_post_meta($_POST['id_post'], '_thrive_meta_optin_hidden_inputs', $parsed_responder_code['hidden_inputs']);

    //save not visible inputs
    update_post_meta($_POST['id_post'], '_thrive_meta_optin_not_visible_inputs', $parsed_responder_code['not_visible_inputs']);

    $optinFieldsJson = get_post_meta($_POST['id_post'], '_thrive_meta_optin_fields_array', true);
    $optinFieldsArray = json_decode($optinFieldsJson, true);

    require_once get_template_directory() . "/inc/templates/admin-optin-render-fields.php";
    die;
}

add_action("wp_ajax_nopriv_optin_save_field_labels", "thrive_optin_save_field_labels");
add_action("wp_ajax_optin_save_field_labels", "thrive_optin_save_field_labels");

function thrive_optin_save_field_labels() {
    if (!wp_verify_nonce($_REQUEST['nonce'], "thrive_render_fields_nonce")) {
        echo 0;
        die;
    }
    if (!isset($_POST['id_post']) || !isset($_POST['fieldsArray'])) {
        echo 0;
        die;
    }
    $postID = (int) $_POST['id_post'];
    $current_optin = get_post($postID);
    if (!$current_optin) {
        echo 0;
        die;
    }

    if (!is_array($_POST['fieldsArray'])) {
        $thrive_meta_optin_fields = array();
    } else {
        $thrive_meta_optin_fields = $_POST['fieldsArray'];
    }

    $thrive_meta_optin_fields_json = esc_sql(json_encode($thrive_meta_optin_fields));

    update_post_meta($postID, '_thrive_meta_optin_fields_array', $thrive_meta_optin_fields_json);

    echo 1;
    die;
}
