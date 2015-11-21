<?php

/*
 * Add the theme options page to the menu
 */

function thrive_page_template_add_menu_page() {
    $icon_url = get_template_directory_uri() . "/inc/images/thrive-shortcode-1.png";
    add_menu_page("Thrive Themes", "Thrive Themes", "edit_theme_options", "thrive_admin_options", "thrive_theme_options_render_page", $icon_url);
    add_submenu_page("thrive_admin_options", "Thrive Options", "Thrive Options", "edit_theme_options", "thrive_admin_options", "thrive_theme_options_render_page");
    add_submenu_page("thrive_admin_options", "Page templates", "Page templates", "edit_theme_options", "thrive_admin_page_templates", "thrive_page_templates_admin_page");
    add_submenu_page("thrive_admin_options", "Font Manager", "Font Manager", "edit_theme_options", "thrive_font_manager", "thrive_font_manager");
    add_submenu_page("thrive_admin_options", "License Validation", "License Validation", "edit_theme_options", "thrive_license_validation", "thrive_license_validation");
}

add_action('admin_menu', 'thrive_page_template_add_menu_page');

/*
 * Render the theme options page
 */

function thrive_page_templates_admin_page() {
    $page_templates = array('page_tpl_privacy', 'page_tpl_disclaimer',
        'page_tpl_lead_generation', 'page_tpl_email_confirmation',
        'page_tpl_thank_you', 'page_tpl_sales_1', 'page_tpl_video_lead_generation',
        'page_tpl_homepage1', 'page_tpl_homepage2');

    $queryOptins = new WP_Query("post_type=thrive_optin&order=ASC&post_status=publish");
    $optins = $queryOptins->get_posts();
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        require(get_template_directory() . "/inc/helpers/helper-tpls.php");
        $new_post_author = thrive_get_current_user();
        $post_data = $_POST;
        $optin_id = (isset($_POST['thrive_optin'])) ? $_POST['thrive_optin'] : 0;
        foreach ($post_data as $key => $val) {
            if (in_array($key, $page_templates)) {
                $my_post = array();
                $my_post['post_title'] = _thrive_get_page_template_title($key);

                if (isset($_POST['editable_with_tcb']) && $_POST['editable_with_tcb'] == 1 && defined("TVE_VERSION")) {
                    $my_post['post_content'] = "";
                } else {
                    $my_post['post_content'] = _thrive_generate_page_template_content($key, $optin_id);
                }

                $my_post['post_status'] = 'publish';
                $my_post['post_author'] = $new_post_author->ID;
                $my_post['post_category'] = array(0);
                $my_post['post_type'] = 'page';
                $my_post['comment_status'] = "closed";
                $post_id = wp_insert_post($my_post);

                if (isset($_POST['editable_with_tcb']) && $_POST['editable_with_tcb'] == 1 && defined("TVE_VERSION")) {
                    $tcb_content = _thrive_generate_page_template_tcb_content($key, $optin_id);
                    update_post_meta($post_id, "tve_updated_post", $tcb_content);
                    update_post_meta($post_id, "tve_save_post", $tcb_content);
                    if ($key != "page_tpl_homepage1" && $key != "page_tpl_homepage2") {
                        update_post_meta($post_id, 'tve_style_family', "Classy");
                    }
                }
                if (!$post_id) {
                    require(get_template_directory() . "/inc/templates/admin-page-templates.php");
                    exit;
                }
                //set up the other options
                switch ($key) {
                    case 'page_tpl_privacy':
                        update_post_meta($post_id, '_wp_page_template', 'narrow-page.php');
                        add_post_meta($post_id, '_thrive_meta_post_focus_area_top', "hide", true) or
                                update_post_meta($post_id, '_thrive_meta_post_focus_area_top', "hide");

                        add_post_meta($post_id, '_thrive_meta_post_focus_area_bottom', "hide", true) or
                                update_post_meta($post_id, '_thrive_meta_post_focus_area_bottom', "hide");
                        break;
                    case 'page_tpl_disclaimer':
                        update_post_meta($post_id, '_wp_page_template', 'narrow-page.php');
                        add_post_meta($post_id, '_thrive_meta_post_focus_area_top', "hide", true) or
                                update_post_meta($post_id, '_thrive_meta_post_focus_area_top', "hide");

                        add_post_meta($post_id, '_thrive_meta_post_focus_area_bottom', "hide", true) or
                                update_post_meta($post_id, '_thrive_meta_post_focus_area_bottom', "hide");
                        break;
                    case 'page_tpl_lead_generation':
                        update_post_meta($post_id, '_wp_page_template', 'landing-page.php');
                        add_post_meta($post_id, '_thrive_meta_show_post_title', 0, true) or
                                update_post_meta($post_id, '_thrive_meta_show_post_title', 0);
                        add_post_meta($post_id, '_thrive_meta_post_breadcrumbs', "off", true) or
                                update_post_meta($post_id, '_thrive_meta_post_breadcrumbs', "off");

                        break;
                    case 'page_tpl_video_lead_generation':
                        update_post_meta($post_id, '_wp_page_template', 'landing-page.php');
                        add_post_meta($post_id, '_thrive_meta_show_post_title', 0, true) or
                                update_post_meta($post_id, '_thrive_meta_show_post_title', 0);
                        add_post_meta($post_id, '_thrive_meta_post_breadcrumbs', "off", true) or
                                update_post_meta($post_id, '_thrive_meta_post_breadcrumbs', "off");
                        break;
                    case 'page_tpl_homepage1':
                        update_post_meta($post_id, '_wp_page_template', 'fullwidth-page.php');
                        add_post_meta($post_id, '_thrive_meta_show_post_title', 0, true) or
                                update_post_meta($post_id, '_thrive_meta_show_post_title', 0);
                        add_post_meta($post_id, '_thrive_meta_post_breadcrumbs', "off", true) or
                                update_post_meta($post_id, '_thrive_meta_post_breadcrumbs', "off");
                        break;
                    case 'page_tpl_homepage2':
                        update_post_meta($post_id, '_wp_page_template', 'fullwidth-page.php');
                        add_post_meta($post_id, '_thrive_meta_show_post_title', 0, true) or
                                update_post_meta($post_id, '_thrive_meta_show_post_title', 0);
                        add_post_meta($post_id, '_thrive_meta_post_breadcrumbs', "off", true) or
                                update_post_meta($post_id, '_thrive_meta_post_breadcrumbs', "off");
                        break;
                    case 'page_tpl_email_confirmation':
                        add_post_meta($post_id, '_thrive_meta_show_post_title', 0, true) or
                                update_post_meta($post_id, '_thrive_meta_show_post_title', 0);
                        update_post_meta($post_id, '_wp_page_template', 'landing-page.php');
                        add_post_meta($post_id, '_thrive_meta_post_breadcrumbs', "off", true) or
                                update_post_meta($post_id, '_thrive_meta_post_breadcrumbs', "off");
                        break;
                    case 'page_tpl_thank_you':
                        update_post_meta($post_id, '_wp_page_template', 'landing-page.php');
                        add_post_meta($post_id, '_thrive_meta_show_post_title', 0, true) or
                                update_post_meta($post_id, '_thrive_meta_show_post_title', 0);
                        add_post_meta($post_id, '_thrive_meta_post_breadcrumbs', "off", true) or
                                update_post_meta($post_id, '_thrive_meta_post_breadcrumbs', "off");
                        break;
                    case 'page_tpl_sales_1':
                        update_post_meta($post_id, '_wp_page_template', 'landing-page.php');
                        add_post_meta($post_id, '_thrive_meta_show_post_title', 0, true) or
                                update_post_meta($post_id, '_thrive_meta_show_post_title', 0);
                        add_post_meta($post_id, '_thrive_meta_post_breadcrumbs', "off", true) or
                                update_post_meta($post_id, '_thrive_meta_post_breadcrumbs', "off");
                        break;
                }
            }
        }
        wp_redirect(admin_url('edit.php?post_type=page&orderby=date&order=desc'));
        exit;
    } else {
        require(get_template_directory() . "/inc/templates/admin-page-templates.php");
    }
}

function _thrive_get_page_template_title($template) {
    switch ($template) {
        case 'page_tpl_privacy':
            return "Privacy Policy";
            break;
        case 'page_tpl_disclaimer':
            return "Disclaimer";
            break;
        case 'page_tpl_lead_generation':
            return "Lead Generation Page";
            break;
        case 'page_tpl_email_confirmation':
            return "Email Confirmation Page";
            break;
        case 'page_tpl_thank_you':
            return "Thank You Page";
            break;
        case 'page_tpl_sales_1':
            return "Sales Page Template 1";
            break;
        case 'page_tpl_video_lead_generation':
            return "Video Lead Generation Page";
            break;
        case 'page_tpl_homepage1':
            return "Homepage 1";
            break;
        case 'page_tpl_homepage2':
            return "Homepage 2";
            break;
        default:
            return "Page Template";
    }
}

function _thrive_generate_page_template_content($template, $optin_id = 0) {
    if ($template == "page_tpl_privacy") {
        $privacy_options = array(
            'website' => thrive_get_theme_options('privacy_tpl_website'),
            'company' => thrive_get_theme_options('privacy_tpl_company'),
            'contact' => thrive_get_theme_options('privacy_tpl_contact'),
            'address' => thrive_get_theme_options('privacy_tpl_address'),
        );
    }

    switch ($template) {
        case 'page_tpl_privacy':
            return _thrive_get_page_template_privacy();
            break;
        case 'page_tpl_disclaimer':
            return _thrive_get_page_template_disclaimer();
            break;
        case 'page_tpl_lead_generation':
            return _thrive_get_page_template_lead_gen($optin_id);
            break;
        case 'page_tpl_email_confirmation':
            return _thrive_get_page_template_email_confirmation();
            break;
        case 'page_tpl_thank_you':
            return _thrive_get_page_template_thank_you_dld();
            break;
        case 'page_tpl_sales_1':
            return _thrive_get_page_template_sales();
            break;
        case 'page_tpl_video_lead_generation':
            return _thrive_get_page_template_video_lead_gen($optin_id);
            break;
        case 'page_tpl_homepage1':
            return _thrive_get_page_template_homepage1($optin_id);
            break;
        case 'page_tpl_homepage2':
            return _thrive_get_page_template_homepage2($optin_id);
            break;
        default:
            return "";
    }

    return "";
}

function _thrive_generate_page_template_tcb_content($template, $optin_id = 0) {
    if ($template == "page_tpl_privacy") {
        $privacy_options = array(
            'website' => thrive_get_theme_options('privacy_tpl_website'),
            'company' => thrive_get_theme_options('privacy_tpl_company'),
            'contact' => thrive_get_theme_options('privacy_tpl_contact'),
            'address' => thrive_get_theme_options('privacy_tpl_address'),
        );
    }

    switch ($template) {
        case 'page_tpl_privacy':
            return _thrive_get_page_template_tcb_privacy();
            break;
        case 'page_tpl_disclaimer':
            return _thrive_get_page_template_tcb_disclaimer();
            break;
        case 'page_tpl_lead_generation':
            return _thrive_get_page_template_tcb_lead_gen($optin_id);
            break;
        case 'page_tpl_email_confirmation':
            return _thrive_get_page_template_tcb_email_confirmation();
            break;
        case 'page_tpl_thank_you':
            return _thrive_get_page_template_tcb_thank_you_dld();
            break;
        case 'page_tpl_sales_1':
            return _thrive_get_page_template_tcb_sales();
            break;
        case 'page_tpl_video_lead_generation':
            return _thrive_get_page_template_tcb_video_lead_gen($optin_id);
            break;
        case 'page_tpl_homepage1':
            return _thrive_get_page_template_tcb_homepage1($optin_id);
            break;
        case 'page_tpl_homepage2':
            return _thrive_get_page_template_tcb_homepage2($optin_id);
            break;
        default:
            return "";
    }

    return "";
}