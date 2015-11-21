<?php

/*
 * Class used to handle the theme customization options
 */

class MyTheme_Customize
{
    /*
     * Register the custom settings and their controls
     */

    public static function register($wp_customize)
    {
        if (!class_exists('ThriveTheme_Select_Font_Control'))
            require_once('templates/admin-customizer-controls.php');

        $default_values = thrive_get_default_customizer_options();

        //add the new sections
        $wp_customize->add_section('thrivetheme_fonts', array(
            'title' => __('Fonts', 'thrive'),
            'priority' => 30,
        ));
        $wp_customize->add_section('thrivetheme_navigation', array(
            'title' => __('Navigation', 'thrive'),
            'priority' => 31,
        ));

        $wp_customize->add_setting('thrivetheme_link_color', array(
            'default' => $default_values['thrivetheme_link_color'],
            'type' => 'theme_mod',
            'sanitize_callback' => 'sanitize_hex_color',
            'capability' => 'edit_theme_options',
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'thrivetheme_link_color', array(
            'label' => __('Link Color', 'thrive'),
            'section' => 'colors',
            'settings' => 'thrivetheme_link_color',
        )));

        $wp_customize->add_setting('thrivetheme_highlight_color', array(
            'default' => $default_values['thrivetheme_highlight_color'],
            'type' => 'theme_mod',
            'sanitize_callback' => 'sanitize_hex_color',
            'capability' => 'edit_theme_options',
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'thrivetheme_highlight_color', array(
            'label' => __('Highlight Color', 'thrive'),
            'section' => 'colors',
            'settings' => 'thrivetheme_highlight_color',
        )));

        $wp_customize->add_setting('thrivetheme_headline_color', array(
            'default' => $default_values['thrivetheme_headline_color'],
            'type' => 'theme_mod',
            'sanitize_callback' => 'sanitize_hex_color',
            'capability' => 'edit_theme_options',
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'thrivetheme_headline_color', array(
            'label' => __('Headline text color', 'thrive'),
            'section' => 'colors',
            'settings' => 'thrivetheme_headline_color',
        )));

        $wp_customize->add_setting('thrivetheme_bodytext_color', array(
            'default' => $default_values['thrivetheme_bodytext_color'],
            'type' => 'theme_mod',
            'sanitize_callback' => 'sanitize_hex_color',
            'capability' => 'edit_theme_options',
            'theme_supports' => 'custom-background',
            //'transport' => 'postMessage'
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'thrivetheme_bodytext_color', array(
            'label' => __('Body text color', 'thrive'),
            'section' => 'colors',
            'settings' => 'thrivetheme_bodytext_color',
        )));

        $wp_customize->add_setting('thrivetheme_bg_pattern', array(
            'default' => "",
            'type' => 'theme_mod',
            'capability' => 'edit_theme_options'
        ));

        $wp_customize->add_setting('thrivetheme_bg_image', array(
            'default' => "",
            'type' => 'theme_mod',
            'capability' => 'edit_theme_options'
        ));

        $wp_customize->add_control(new ThriveTheme_Select_ImageBg_Control($wp_customize, 'thrivetheme_bg_image', array(
            'label' => __('Background image', 'thrive'),
            'section' => 'colors',
            'settings' => 'thrivetheme_bg_image',
        )));

        $wp_customize->add_control(new ThriveTheme_Select_PatternBg_Control($wp_customize, 'thrivetheme_bg_pattern', array(
            'label' => __('Background pattern', 'thrive'),
            'section' => 'colors',
            'settings' => 'thrivetheme_bg_pattern',
        )));

        $wp_customize->add_setting('thrivetheme_header_fontsize', array(
            'default' => $default_values['thrivetheme_header_fontsize'],
            'type' => 'theme_mod',
            'capability' => 'edit_theme_options',
        ));

        $wp_customize->add_control(new ThriveTheme_Select_Fontsize_Control($wp_customize, 'thrivetheme_header_fontsize', array(
            'label' => __('Headline font size', 'thrive'),
            'section' => 'thrivetheme_fonts',
            'settings' => 'thrivetheme_header_fontsize',
        )));

        $wp_customize->add_setting('thrivetheme_header_font', array(
            'default' => $default_values['thrivetheme_header_font'],
            'type' => 'theme_mod',
            'capability' => 'edit_theme_options',
        ));

        $wp_customize->add_control(new ThriveTheme_Select_Font_Control1($wp_customize, 'thrivetheme_header_font', array(
            'label' => __('Heading font', 'thrive'),
            'section' => 'thrivetheme_fonts',
            'settings' => 'thrivetheme_header_font',
        )));

        $wp_customize->add_setting('thrivetheme_headline_case', array(
            'default' => 'Regular',
            'type' => 'theme_mod',
            'capability' => 'edit_theme_options',
        ));

        $wp_customize->add_control(new ThriveTheme_Select_Case_Control($wp_customize, 'thrivetheme_headline_case', array(
            'label' => __('Headline Case', 'thrive'),
            'section' => 'thrivetheme_fonts',
            'settings' => 'thrivetheme_headline_case',
        )));

        $wp_customize->add_setting('thrivetheme_headline_weight', array(
            'default' => 'Bold',
            'type' => 'theme_mod',
            'capability' => 'edit_theme_options',
        ));

        $wp_customize->add_control(new ThriveTheme_Select_Weight_Control($wp_customize, 'thrivetheme_headline_weight', array(
            'label' => __('Headline Weight', 'thrive'),
            'section' => 'thrivetheme_fonts',
            'settings' => 'thrivetheme_headline_weight',
        )));

        $wp_customize->add_setting('thrivetheme_body_fontsize', array(
            'default' => $default_values['thrivetheme_body_fontsize'],
            'type' => 'theme_mod',
            'capability' => 'edit_theme_options',
        ));

        $wp_customize->add_control(new ThriveTheme_Select_Fontsize_Control($wp_customize, 'thrivetheme_body_fontsize', array(
            'label' => __('Body font size', 'thrive'),
            'section' => 'thrivetheme_fonts',
            'settings' => 'thrivetheme_body_fontsize',
        )));

        $wp_customize->add_setting('thrivetheme_body_font', array(
            'default' => $default_values['thrivetheme_body_font'],
            'type' => 'theme_mod',
            'capability' => 'edit_theme_options',
        ));

        $wp_customize->add_control(new ThriveTheme_Select_Font_Control2($wp_customize, 'thrivetheme_body_font', array(
            'label' => __('Body font', 'thrive'),
            'section' => 'thrivetheme_fonts',
            'settings' => 'thrivetheme_body_font',
        )));

        $wp_customize->add_setting('thrivetheme_body_lineheight', array(
            'default' => $default_values['thrivetheme_body_lineheight'],
            'type' => 'theme_mod',
            'capability' => 'edit_theme_options',
        ));

        $wp_customize->add_control(new ThriveTheme_Select_Fontlineheight_Control($wp_customize, 'thrivetheme_body_lineheight', array(
            'label' => __('Body line height', 'thrive'),
            'section' => 'thrivetheme_fonts',
            'settings' => 'thrivetheme_body_lineheight',
        )));

        //add the reset section
        $wp_customize->add_section('thrivetheme_reset', array(
            'title' => __('Reset to defaults', 'thrive'),
            'priority' => 999,
        ));
        $wp_customize->add_control(new ThriveTheme_ResetDefaults_Control($wp_customize, 'thrivetheme_reset_button', array(
            'label' => __('Reset', 'thrive'),
            'section' => 'thrivetheme_reset',
            'settings' => 'thrivetheme_headline_case'
        )));

        //add the Header section
        $wp_customize->add_section('thrivetheme_header', array(
            'title' => __('Header', 'thrive'),
            'priority' => 40,
        ));
        //menu link color
        $wp_customize->add_setting('thrivetheme_menu_link_color', array(
            'default' => $default_values['thrivetheme_link_color'],
            'type' => 'theme_mod',
            'sanitize_callback' => 'sanitize_hex_color',
            'capability' => 'edit_theme_options',
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'thrivetheme_menu_link_color', array(
            'priority' => 0,
            'label' => __('Menu link Color', 'thrive'),
            'section' => 'thrivetheme_header',
            'settings' => 'thrivetheme_menu_link_color',
        )));
        //menu hover color
        $wp_customize->add_setting('thrivetheme_menu_highlight_color', array(
            'default' => $default_values['thrivetheme_highlight_color'],
            'type' => 'theme_mod',
            'sanitize_callback' => 'sanitize_hex_color',
            'capability' => 'edit_theme_options',
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'thrivetheme_menu_highlight_color', array(
            'priority' => 0,
            'label' => __('Menu Hover Color', 'thrive'),
            'section' => 'thrivetheme_header',
            'settings' => 'thrivetheme_menu_highlight_color',
        )));

        //highlight default color
        $wp_customize->add_setting('thrivetheme_default_highlight', array(
            'type' => 'theme_mod',
            'capability' => 'edit_theme_options',
            'default' => '#0f3f8d #5772c9'
        ));
        $wp_customize->add_control(new ThriveTheme_HighlightColor_Control($wp_customize, 'thrivetheme_default_highlight', array(
            'priority' => 0,
            'label' => __('Default Highlight Menu Colors', 'thrive'),
            'section' => 'thrivetheme_header',
            'settings' => 'thrivetheme_default_highlight',
        )));

        //Highlighted Menu Background Color
        $wp_customize->add_setting('thrivetheme_highlight_background_color', array(
            'default' => '#0f3f8d',
            'type' => 'theme_mod',
            'sanitize_callback' => 'sanitize_hex_color',
            'capability' => 'edit_theme_options',
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'thrivetheme_highlight_background_color', array(
            'priority' => 0,
            'label' => __('Highlighted Menu Background Color', 'thrive'),
            'section' => 'thrivetheme_header',
            'settings' => 'thrivetheme_highlight_background_color',
        )));
        //Highlighted Menu Link Color
        $wp_customize->add_setting('thrivetheme_highlight_link_color', array(
            'default' => '#ffffff',
            'type' => 'theme_mod',
            'sanitize_callback' => 'sanitize_hex_color',
            'capability' => 'edit_theme_options',
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'thrivetheme_highlight_link_color', array(
            'priority' => 0,
            'label' => __('Highlighted Menu Link Color', 'thrive'),
            'section' => 'thrivetheme_header',
            'settings' => 'thrivetheme_highlight_link_color',
        )));

        //Highlighted Menu Hover Background Color
        $wp_customize->add_setting('thrivetheme_highlight_hover_background_color', array(
            'default' => '#5772c9',
            'type' => 'theme_mod',
            'sanitize_callback' => 'sanitize_hex_color',
            'capability' => 'edit_theme_options',
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'thrivetheme_highlight_hover_background_color', array(
            'priority' => 0,
            'label' => __('Highlighted Menu Hover Background Color', 'thrive'),
            'section' => 'thrivetheme_header',
            'settings' => 'thrivetheme_highlight_hover_background_color',
        )));
        //Highlighted Menu Hover Link Color
        $wp_customize->add_setting('thrivetheme_highlight_hover_link_color', array(
            'default' => '#FFFFFF',
            'type' => 'theme_mod',
            'sanitize_callback' => 'sanitize_hex_color',
            'capability' => 'edit_theme_options',
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'thrivetheme_highlight_hover_link_color', array(
            'priority' => 0,
            'label' => __('Highlighted Menu Hover Link Color', 'thrive'),
            'section' => 'thrivetheme_header',
            'settings' => 'thrivetheme_highlight_hover_link_color',
        )));

        //header logo
        $wp_customize->add_setting('thrivetheme_header_logo', array(
            'default' => 'show',
            'type' => 'theme_mod',
            'capability' => 'edit_theme_options',
        ));
        $wp_customize->add_control('thrivetheme_header_logo', array(
            'priority' => 1,
            'label' => __('Logo', 'thrive'),
            'section' => 'thrivetheme_header',
            'settings' => 'thrivetheme_header_logo',
            'type' => 'radio',
            'choices' => array(
                'show' => __('Show', 'thrive'),
                'hide' => __('Hide', 'thrive')
            )
        ));
        //logo width
        $wp_customize->add_setting('thrivetheme_logo_image_width', array(
            'type' => 'theme_mod',
            'default' => 200,
            'capability' => 'edit_theme_options',
        ));
        $wp_customize->add_control('thrivetheme_logo_image_width', array(
                'priority' => 2,
                'label' => __('Image Logo Width', 'thrive'),
                'section' => 'thrivetheme_header',
                'settings' => 'thrivetheme_logo_image_width'
            )
        );
        //header type
        $wp_customize->add_setting('thrivetheme_theme_background', array(
            'type' => 'theme_mod',
            'capability' => 'edit_theme_options',
        ));
        $wp_customize->add_control('thrivetheme_theme_background', array(
            'priority' => 4,
            'label' => __('Header type', 'thrive'),
            'section' => 'thrivetheme_header',
            'settings' => 'thrivetheme_theme_background',
            'type' => 'select',
            'choices' => array(
                'default-header' => 'Theme Default',
                '#customize-control-thrivetheme_background_value' => 'Custom Color',
                '#customize-control-thrivetheme_header_pattern' => 'Background Pattern',
                '#customize-control-thrivetheme_header_background_image, #customize-control-thrivetheme_header_image_type, #customize-control-thrivetheme_header_image_height' => 'Background Image',
            ),
        ));
        //header pattern
        $wp_customize->add_setting('thrivetheme_header_pattern', array(
            'type' => 'theme_mod',
            'default' => '#ffffff',
            'capability' => 'edit_theme_options',
        ));
        $wp_customize->add_control(new ThriveTheme_Header_Pattern($wp_customize, 'thrivetheme_header_pattern', array(
            'priority' => 5,
            'label' => __('Header pattern', 'thrive'),
            'section' => 'thrivetheme_header',
            'settings' => 'thrivetheme_header_pattern',
        )));
        //header color
        $wp_customize->add_setting('thrivetheme_background_value', array(
            'type' => 'theme_mod',
            'default' => '#ffffff',
            'capability' => 'edit_theme_options',
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'thrivetheme_background_value', array(
            'priority' => 5,
            'label' => __('Header color', 'thrive'),
            'section' => 'thrivetheme_header',
            'settings' => 'thrivetheme_background_value'
        )));
        //header image type
        $wp_customize->add_setting('thrivetheme_header_image_type', array(
            'type' => 'theme_mod',
            'default' => 'full',
            'capability' => 'edit_theme_options',
        ));
        $wp_customize->add_control('thrivetheme_header_image_type', array(
            'label' => __('Image type', 'thrive'),
            'section' => 'thrivetheme_header',
            'settings' => 'thrivetheme_header_image_type',
            'type' => 'radio',
            'choices' => array(
                'full' => __('Full-Width', 'thrive'),
                'centered' => __('Centered', 'thrive')
            )
        ));
        //header image
        $wp_customize->add_setting('thrivetheme_header_background_image', array(
            'type' => 'theme_mod',
            'capability' => 'edit_theme_options',
        ));
        $wp_customize->add_control(new ThriveTheme_Header_Image($wp_customize, 'thrivetheme_header_background_image', array(
            'priority' => 5,
            'label' => __('Header image', 'thrive'),
            'section' => 'thrivetheme_header',
            'settings' => 'thrivetheme_header_background_image'
        )));
        //header image height
        $wp_customize->add_setting('thrivetheme_header_image_height', array(
            'type' => 'theme_mod',
            'capability' => 'edit_theme_options',
        ));
        $wp_customize->add_control('thrivetheme_header_image_height', array(
                'priority' => 6,
                'label' => __('Header height', 'thrive'),
                'section' => 'thrivetheme_header',
                'settings' => 'thrivetheme_header_image_height'
            )
        );

        $wp_customize->remove_section('background_image');
    }

    /*
     * Output the css for each option in an <style> tag
     */

    public static function header_output()
    {
        self::generate_fonts_includes();
        ?>
        <style type="text/css"><?php
        self::generate_css('body', 'background', 'background_color', '');
        self::generate_css('.cnt article h1 a', 'color', 'thrivetheme_headline_color', '');
        self::generate_css('.bSe h1', 'color', 'thrivetheme_headline_color', '');
        self::generate_css('.bSe h2', 'color', 'thrivetheme_headline_color', '');
        self::generate_css('.bSe h3', 'color', 'thrivetheme_headline_color', '');
        self::generate_css('.bSe h4', 'color', 'thrivetheme_headline_color', '');
        self::generate_css('.bSe h5', 'color', 'thrivetheme_headline_color', '');
        self::generate_css('.bSe h6', 'color', 'thrivetheme_headline_color', '');
        self::generate_css('.cnt article p', 'color', 'thrivetheme_bodytext_color');
        self::generate_css('.cnt .bSe article', 'color', 'thrivetheme_bodytext_color');
        self::generate_css('.cnt article h1 a, .tve-woocommerce .bSe .awr .entry-title, .tve-woocommerce .bSe .awr .page-title', 'font-family', 'thrivetheme_header_font', '');
        self::generate_css('.bSe h1', 'font-family', 'thrivetheme_header_font', '');
        self::generate_css('.bSe h2,.tve-woocommerce .bSe h2', 'font-family', 'thrivetheme_header_font', '');
        self::generate_css('.bSe h3,.tve-woocommerce .bSe h3', 'font-family', 'thrivetheme_header_font', '');
        self::generate_css('.bSe h4', 'font-family', 'thrivetheme_header_font', '');
        self::generate_css('.bSe h5', 'font-family', 'thrivetheme_header_font', '');
        self::generate_css('.bSe h6', 'font-family', 'thrivetheme_header_font', '');
        self::generate_css('.bSe h1', 'text-transform', 'thrivetheme_headline_case', '');
        self::generate_css('.cnt article h1', 'font-weight', 'thrivetheme_headline_weight', '');
        self::generate_css('.bSe h1', 'font-weight', 'thrivetheme_headline_weight', '');
        self::generate_css('.bSe h1 a', 'font-weight', 'thrivetheme_headline_weight', '');
        self::generate_css('.bSe h2', 'font-weight', 'thrivetheme_headline_weight', '');
        self::generate_css('.bSe h3', 'font-weight', 'thrivetheme_headline_weight', '');
        self::generate_css('.bSe h4', 'font-weight', 'thrivetheme_headline_weight', '');
        self::generate_css('.bSe h5', 'font-weight', 'thrivetheme_headline_weight', '');
        self::generate_css('.bSe h5', 'font-weight', 'thrivetheme_headline_weight', '');
        self::generate_css('.cnt, .bp-t, .tve-woocommerce .product p, .tve-woocommerce .products p', 'font-family', 'thrivetheme_body_font');
        self::generate_css('.bSe h1, .bSe .entry-title', 'font-size', 'thrivetheme_header_fontsize', '', 'px');
        self::generate_css('.cnt', 'font-size', 'thrivetheme_body_fontsize', '', 'px');
        self::generate_css('.out', 'font-size', 'thrivetheme_body_fontsize', '', 'px');
        self::generate_css('.thrivecb', 'font-size', 'thrivetheme_body_fontsize', '', 'px');
        self::generate_css('.aut p', 'font-size', 'thrivetheme_body_fontsize', '', 'px');
        self::generate_css('.cnt p', 'line-height', 'thrivetheme_body_lineheight', '', 'em');
        self::generate_css('.lhgh', 'line-height', 'thrivetheme_body_lineheight', '', 'em');
        self::generate_css('.dhgh', 'line-height', 'thrivetheme_body_lineheight', '', 'em');
        self::generate_css('.dhgh', 'font-size', 'thrivetheme_body_fontsize', '', 'px');
        self::generate_css('.lhgh', 'font-size', 'thrivetheme_body_fontsize', '', 'px');
        self::generate_css('.thrivecb', 'line-height', 'thrivetheme_body_lineheight', '', 'em');
        /* self::generate_css('header nav ul li ul li a:hover', 'background-color', 'thrivetheme_highlight_color'); */
        /* self::generate_css('.arl, header nav ul li a:hover, header nav .selectedLiA', 'background-color', 'thrivetheme_highlight_color'); */
        /* self::generate_css('header nav ul li ul li ul li', 'background-color', 'thrivetheme_highlight_color'); */
        /* self::generate_css('header nav ul li ul li a:hover', 'background-color', 'thrivetheme_highlight_color'); */
        self::generate_css('.cnt .cmt, .cnt .acm', 'background-color', 'thrivetheme_highlight_color');
        self::generate_css('.trg', 'border-color', 'thrivetheme_highlight_color', '', ' transparent transparent');
        self::generate_css('.str', 'border-color', 'thrivetheme_highlight_color', ' transparent ', ' transparent transparent');
        /* self::generate_css('.pgn a:hover, .pgn .dots a:hover', 'background-color', 'thrivetheme_highlight_color'); */
        self::generate_css('.brd ul li', 'color', 'thrivetheme_highlight_color');
        self::generate_css('.bSe a', 'color', 'thrivetheme_link_color', '');
        self::generate_css('.bSe h1', 'text-transform', 'thrivetheme_headline_case', '');
        self::generate_css('.bSe .faq h4', 'font-family', 'thrivetheme_body_font', '');
        self::generate_css('body', 'background-image', 'thrivetheme_bg_pattern', '');
        self::generate_css('body', '', 'thrivetheme_bg_image', '');
        self::generate_css('header ul.menu > li > a', 'color', 'thrivetheme_menu_link_color', '');
        self::generate_css('header .phone .apnr, header .phone .apnr:before, header .phone .fphr', 'color', 'thrivetheme_menu_link_color', '');
        self::generate_css('header ul.menu > li > a:hover', 'color', 'thrivetheme_menu_highlight_color', '');
        self::generate_css('header .phone:hover .apnr, header .phone:hover .apnr:before, header .phone:hover .fphr', 'color', 'thrivetheme_menu_highlight_color', '');
        self::generate_css('header nav > ul > li.current_page_item > a:hover', 'color', 'thrivetheme_menu_highlight_color', '');
        self::generate_css('header nav > ul > li.current_menu_item > a:hover', 'color', 'thrivetheme_menu_highlight_color', '');
        self::generate_css('header nav > ul > li.current_menu_item > a:hover', 'color', 'thrivetheme_menu_highlight_color', '');
        self::generate_css('header nav > ul > li > a:active', 'color', 'thrivetheme_menu_highlight_color', '');
        self::generate_css('header #logo > a > img', 'max-width', 'thrivetheme_logo_image_width', '', 'px');
        self::generate_css('header ul.menu > li.h-cta > a', 'color', 'thrivetheme_highlight_link_color', '', '!important');
        self::generate_css('header ul.menu > li.h-cta >a ', 'background', 'thrivetheme_highlight_background_color', '');
        self::generate_css('header ul.menu > li.h-cta > a', 'border-color', 'thrivetheme_highlight_background_color', '');
        self::generate_css('header ul.menu > li.h-cta > a:hover', 'color', 'thrivetheme_highlight_hover_link_color', '', '!important');
        self::generate_css('header ul.menu > li.h-cta > a:hover', 'background', 'thrivetheme_highlight_hover_background_color', '');
        ?></style>
        <?php
    }

    /*
     * Include the fonts used in the theme from google
     */

    public static function generate_fonts_includes()
    {
        $header_font = get_theme_mod("thrivetheme_header_font");
        $body_font = get_theme_mod("thrivetheme_body_font");

        if ($header_font) {
            if (Thrive_Font_Import_Manager::isImportedFont($header_font)) {
                $header_font = Thrive_Font_Import_Manager::getCssFile();
            } else if (strpos($header_font, "=") === false) {  //hot fix for font manager changes
                $default_values = thrive_get_default_customizer_options();
                $header_font = $default_values['thrivetheme_header_font'];
            }
            echo "<link href='" . $header_font . "' rel='stylesheet' type='text/css'>";
        }
        if ($body_font) {
            if (Thrive_Font_Import_Manager::isImportedFont($body_font)) {
                $body_font = Thrive_Font_Import_Manager::getCssFile();
            } else if (strpos($body_font, "=") === false) {
                $default_values = thrive_get_default_customizer_options();
                $body_font = $default_values['thrivetheme_body_font'];
            }
            echo "<link href='" . $body_font . "' rel='stylesheet' type='text/css'>";
        }
    }

    /*
     * Include the javascript used in the live preview
     */

    public static function live_preview()
    {
        wp_enqueue_script(
            'mytheme-themecustomizer', get_template_directory_uri() . '/inc/js/theme-customizer.js', array('jquery', 'customize-preview'), '', true
        );
    }

    /**
     * retrieve the needed font css values from the font link
     *
     * @param string $font_link
     */
    public static function get_font_params($font_link)
    {
        if(thrive_font_manager_is_safe_font($font_link)) {
            return thrive_font_manager_get_safe_font($font_link);
        }

        if (Thrive_Font_Import_Manager::isImportedFont($font_link)) {
            return array(
                'family' => thrive_prepare_font_family($font_link),
                'variants' => array(
                    'regular',
                    'italic',
                    '600'
                ),
                'subsets' => array(
                    'latin'
                )
            );
        }

        if (strpos($font_link, '//') === 0) {
            $font_link = 'http:' . $font_link;
        }
        
        $font = array();

        $parts = parse_url($font_link);
        if (empty($parts['query'])) {
            return $font;
        }
        $args = wp_parse_args($parts['query'], array());

        if (empty($args['family'])) {
            return $font;
        }

        $parts = explode(':', $args['family']);

        $font['family'] = $parts[0];

        if (isset($parts[1])) {
            $weights = explode(',', $parts[1]);
            $font['weight'] = $weights[0];
            if (strpos($font['weight'], 'italic') !== false) {
                $font['weight'] = preg_replace('/[^\d]+/', '', $font['weight']);
                $font['style'] = 'italic';
            }
        }

        return $font;
    }

    /*
     * Generate the css syntax for an option
     */

    public static function generate_css($selector, $style, $mod_name, $prefix = '', $postfix = '', $echo = true)
    {
        $return = '';
        $mod = get_theme_mod($mod_name);
        if (empty($mod)) {
            $default_values = thrive_get_default_customizer_options();
            $mod = isset($default_values[$mod_name]) ? $default_values[$mod_name] : '';
        }
        //fix for color options if the color code doesn't have the # at the start
        if (strpos($mod_name, "color") !== false) {
            if (strpos($mod, "#") === false) {
                $mod = "#" . $mod;
            }
        }

        if ($mod_name == 'thrivetheme_header_font' || $mod_name == 'thrivetheme_body_font') {
            if (!$mod || (strpos($mod, "=") === false && !thrive_font_manager_is_safe_font($mod) && !Thrive_Font_Import_Manager::isImportedFont($mod))) {
                $default_values = thrive_get_default_customizer_options();
                if ($mod_name == "thrivetheme_header_font") {
                    $mod = $default_values['thrivetheme_header_font'];
                }
                if ($mod_name == "thrivetheme_body_font") {
                    $mod = $default_values['thrivetheme_body_font'];
                }
            }

            $font = self::get_font_params($mod);

            if (!empty($font['family'])) {
                echo sprintf(
                    '%s{font-family:%s,sans-serif;%s}',
                    $selector,
                    $font['family'],
                    $mod_name == 'thrivetheme_body_font' && !empty($font['weight']) ? 'font-weight:' . $font['weight'] . ';' : ''
                );
            }

            $is_header_bold = strtolower(get_theme_mod("thrivetheme_headline_weight")) == "bold" ? true : false;
            if ($is_header_bold && $mod_name == 'thrivetheme_header_font') {
                $font_bold_css = _thrive_get_fonts_bold_array($mod);
                if ($font_bold_css) {
                    echo $selector . " {font-weight: 700;}";
                } else {
                    echo $selector . " {font-weight: bold;}";
                }
            }

            if ($mod_name == 'thrivetheme_body_font') {
                $font_bold_css = _thrive_get_fonts_bold_array($mod);
                if ($font_bold_css) {
                    echo "article strong {font-weight: 700;}";
                } else {
                    echo "article strong {font-weight: bold;}";
                }
            }

            //for font selector, exit here
            return;
        }

        if ($mod_name == "thrivetheme_headline_case") {
            $mod = strtolower(get_theme_mod("thrivetheme_headline_case")) == "uppercase" ? "uppercase" : "Regular";
            if ($mod == "Regular") {
                return;
            }
        }

        if ($mod_name == "thrivetheme_bg_pattern") {
            if ($mod == "anopattern" || empty($mod)) {
                return;
            } else {
                $url = get_template_directory_uri() . "/images/patterns/" . $mod . ".png";
                $mod = "url('" . $url . "')";
            }
        }

        if ($mod_name == "thrivetheme_bg_image") {
            if (empty($mod)) {
                return;
            } else {
                $style = 'background-image';
                $mod = "url('" . $mod . "'); background-position: center top; background-attachment: fixed; background-repeat: no-repeat; background-size: cover;";
            }
        }

        if ($selector == "header nav ul li ul li a:hover") {
            $lighter_color = _thrive_colour_creator($mod, 10);
            $mod = $lighter_color;
        }

        if (!empty($mod)) {
            $return = sprintf('%s { %s:%s; }', $selector, $style, $prefix . $mod . $postfix
            );
            if ($echo) {
                echo $return;
            }
        }
        return $return;
    }

}

add_action('customize_register', array('MyTheme_Customize', 'register'));

add_action('wp_head', array('MyTheme_Customize', 'header_output'));

add_action('customize_preview_init', array('MyTheme_Customize', 'live_preview'));


add_action("wp_ajax_nopriv_reset_customization", "thrive_reset_customization");
add_action("wp_ajax_reset_customization", "thrive_reset_customization");

/*
 * Reset the customization options to the default
 */

function thrive_reset_customization()
{

    thrive_set_default_customizer_options();
}

function thrive_enqueue_customize_scripts()
{
    wp_enqueue_script('thrive-widgets-options', get_template_directory_uri() . '/inc/js/widgets-options.js', array('jquery', 'media-upload', 'thickbox', 'jquery-ui-autocomplete'));

    //prepare the javascript params
    $getUsersWpnonce = wp_create_nonce("thrive_helper_get_users");
    $getUsersUrl = admin_url('admin-ajax.php?action=thrive_helper_get_users&nonce=' . $getUsersWpnonce);

    $js_params_array = array('getUsersUrl' => $getUsersUrl,
        'noonce' => $getUsersWpnonce);
    wp_localize_script('thrive-widgets-options', 'ThriveWidgetsOptions', $js_params_array);
}

add_action('customize_controls_enqueue_scripts', 'thrive_enqueue_customize_scripts');
