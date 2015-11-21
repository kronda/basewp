<?php
/**
 * Created by PhpStorm.
 * User: Danut
 * Date: 9/25/2015
 * Time: 9:49 AM
 */

require_once dirname(__FILE__) . "/classes/Thrive_Font_Import_Manager.php";

add_action('admin_menu', 'thrive_admin_menu_font_import_manager');
function thrive_admin_menu_font_import_manager()
{
    add_submenu_page("thrive_admin_options", "Font Import Manager", "Font Import Manager", "edit_theme_options", "thrive_font_import_manager", 'thrive_font_import_manager_main_page');
}

function thrive_font_import_manager_main_page()
{
    $font_import_manager = Thrive_Font_Import_Manager::getInstance(get_template_directory_uri() . "/inc");
    $font_import_manager->mainPage();
}
