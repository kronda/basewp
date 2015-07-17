<?php

/*

WARNING! DO NOT EDIT THEME FILES IF YOU PLAN ON UPDATING!

Theme files will be overwritten and your changes will be lost 
when updating. Instead, add custom code in the admin under 
Appearance > Theme Settings > Code or create a child theme.

*/

// Defines
define( 'FL_THEME_VERSION', '1.3.1' );
define( 'FL_THEME_DIR', get_template_directory() );
define( 'FL_THEME_URL', get_template_directory_uri() );

// Classes
require_once 'classes/class-fl-color.php';
require_once 'classes/class-fl-customizer.php';
require_once 'classes/class-fl-fonts.php';
require_once 'classes/class-fl-theme.php';
require_once 'classes/class-fl-theme-update.php';

// Theme Actions
add_action( 'after_setup_theme',     'FLTheme::setup' );
add_action( 'init',                  'FLTheme::init_woocommerce' );
add_action( 'wp_enqueue_scripts',    'FLTheme::enqueue_scripts', 999 );
add_action( 'widgets_init',          'FLTheme::widgets_init' );

// Theme Filters
add_filter( 'body_class',            'FLTheme::body_class' );
add_filter( 'excerpt_more',          'FLTheme::excerpt_more' );

// Theme Updates
add_action( 'init',                  'FLThemeUpdate::init' );

// Admin Actions
add_action( 'admin_head',            'FLTheme::favicon' );

// Customizer
add_action( 'customize_preview_init',                    'FLCustomizer::preview_init' );
add_action( 'customize_controls_enqueue_scripts',        'FLCustomizer::controls_enqueue_scripts' );
add_action( 'customize_controls_print_footer_scripts',   'FLCustomizer::controls_print_footer_scripts' );
add_action( 'customize_register',                        'FLCustomizer::register' );
add_action( 'customize_save_after',                      'FLCustomizer::save' );
