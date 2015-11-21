<?php

function _thrive_get_page_template_privacy() {
    $options = array(
        'website' => thrive_get_theme_options('privacy_tpl_website'),
        'company' => thrive_get_theme_options('privacy_tpl_company'),
        'contact' => thrive_get_theme_options('privacy_tpl_contact'),
        'address' => thrive_get_theme_options('privacy_tpl_address'),
    );

    ob_start();
    include plugin_dir_path(__FILE__) . 'tpl-theme/privacy.php';
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

function _thrive_get_page_template_disclaimer() {

    $options = array(
        'website' => thrive_get_theme_options('privacy_tpl_website'),
        'company' => thrive_get_theme_options('privacy_tpl_company'),
        'contact' => thrive_get_theme_options('privacy_tpl_contact'),
        'address' => thrive_get_theme_options('privacy_tpl_address'),
    );

    ob_start();
    include plugin_dir_path(__FILE__) . 'tpl-theme/disclaimer.php';
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

function _thrive_get_page_template_lead_gen($optin_id = 0) {
    $images_dir = get_template_directory_uri() . "/images/templates";

    ob_start();
    include plugin_dir_path(__FILE__) . 'tpl-theme/lead_gen.php';
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

function _thrive_get_page_template_email_confirmation() {
    $images_dir = get_template_directory_uri() . "/images/templates";

    ob_start();
    include plugin_dir_path(__FILE__) . 'tpl-theme/email_confirmation.php';
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

function _thrive_get_page_template_video_lead_gen($optin_id = 0) {
    $images_dir = get_template_directory_uri() . "/images/templates";

    ob_start();
    include plugin_dir_path(__FILE__) . 'tpl-theme/video_lead_gen.php';
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

function _thrive_get_page_template_homepage1($optin_id = 0) {
    $images_dir = get_template_directory_uri() . "/images/templates";

    ob_start();
    include plugin_dir_path(__FILE__) . 'tpl-theme/homepage1.php';
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

function _thrive_get_page_template_homepage2($optin_id = 0) {

    $images_dir = get_template_directory_uri() . "/images/templates";

    ob_start();
    include plugin_dir_path(__FILE__) . 'tpl-theme/homepage2.php';
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

function _thrive_get_page_template_sales() {
    $images_dir = get_template_directory_uri() . "/images/templates";

    ob_start();
    include plugin_dir_path(__FILE__) . 'tpl-theme/sales.php';
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

function _thrive_get_page_template_thank_you_dld() {
    $images_dir = get_template_directory_uri() . "/images/templates";

    ob_start();
    include plugin_dir_path(__FILE__) . 'tpl-theme/thank_you_dld.php';
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

function _thrive_get_page_template_tcb_privacy() {
    $options = array(
        'website' => thrive_get_theme_options('privacy_tpl_website'),
        'company' => thrive_get_theme_options('privacy_tpl_company'),
        'contact' => thrive_get_theme_options('privacy_tpl_contact'),
        'address' => thrive_get_theme_options('privacy_tpl_address'),
    );

    ob_start();
    include plugin_dir_path(__FILE__) . 'tpl-tcb/privacy.php';
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

function _thrive_get_page_template_tcb_disclaimer() {

    $options = array(
        'website' => thrive_get_theme_options('privacy_tpl_website'),
        'company' => thrive_get_theme_options('privacy_tpl_company'),
        'contact' => thrive_get_theme_options('privacy_tpl_contact'),
        'address' => thrive_get_theme_options('privacy_tpl_address'),
    );

    ob_start();
    include plugin_dir_path(__FILE__) . 'tpl-tcb/disclaimer.php';
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

function _thrive_get_page_template_tcb_lead_gen($optin_id = 0) {
    $images_dir = get_template_directory_uri() . "/images/templates";

    ob_start();
    include plugin_dir_path(__FILE__) . 'tpl-tcb/lead_gen.php';
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

function _thrive_get_page_template_tcb_email_confirmation() {
    $images_dir = get_template_directory_uri() . "/images/templates";

    ob_start();
    include plugin_dir_path(__FILE__) . 'tpl-tcb/email_confirmation.php';
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

function _thrive_get_page_template_tcb_video_lead_gen($optin_id = 0) {
    $images_dir = get_template_directory_uri() . "/images/templates";

    ob_start();
    include plugin_dir_path(__FILE__) . 'tpl-tcb/video_lead_gen.php';
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

function _thrive_get_page_template_tcb_homepage1($optin_id = 0) {
    $images_dir = get_template_directory_uri() . "/images/templates";
    $config_optin = json_encode(array(
        'optin' => $optin_id,
        'color' => 'orange',
        'size' => 'medium',
        'text' => 'Subscribe Now',
        'layout' => 'horizontal'
    ));

    ob_start();
    include plugin_dir_path(__FILE__) . 'tpl-tcb/homepage1.php';
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

function _thrive_get_page_template_tcb_homepage2($optin_id = 0) {
    $images_dir = get_template_directory_uri() . "/images/templates";
    $config_optin = json_encode(array(
        'optin' => $optin_id,
        'color' => 'orange',
        'size' => 'medium',
        'text' => 'Subscribe Now',
        'layout' => 'horizontal'
    ));

    ob_start();
    include plugin_dir_path(__FILE__) . 'tpl-tcb/homepage2.php';
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

function _thrive_get_page_template_tcb_sales() {
    $images_dir = get_template_directory_uri() . "/images/templates";

    ob_start();
    include plugin_dir_path(__FILE__) . 'tpl-tcb/sales.php';
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

function _thrive_get_page_template_tcb_thank_you_dld() {
    $images_dir = get_template_directory_uri() . "/images/templates";

    ob_start();
    include plugin_dir_path(__FILE__) . 'tpl-tcb/thank_you_dld.php';
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

function _thrive_get_lorem_ipsum_post_content() {
    $content = "";

    return $content;
}