<?php
/*
Plugin Name: Thrive Clever Widgets
Plugin URI: https://thrivethemes.com
Version: 1.07
Author: <a href="https://thrivethemes.com">Thrive Themes</a>
Description: Take control over exactly where your widgets are displayed on your site
*/

if (!defined('WPINC')) {
    die;
}

define('THRIVE_CLEVER_WIDGETS_VERSION', '1.07');

require_once plugin_dir_path(__FILE__) . 'includes/class-thrive-clever-widgets-manager.php';

/**
 * Bootstrap the plugin
 */
$tcw_manager = new Thrive_Clever_Widgets_Manager();
$tcw_manager->run();
