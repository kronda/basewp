<?php
/**
 * Created by PhpStorm.
 * User: Danut
 * Date: 9/25/2015
 * Time: 9:49 AM
 */

add_submenu_page("thrive_font_manager", "Font Import Manager", "Font Import Manager", "edit_theme_options", "thrive_font_import_manager", 'thrive_font_import_manager_main_page');

function thrive_font_import_manager_main_page()
{
    $font_import_manager = Thrive_Font_Import_Manager::getInstance(tve_editor_url() . "/admin", 'thrive-cb');
    $font_import_manager->mainPage();
}
