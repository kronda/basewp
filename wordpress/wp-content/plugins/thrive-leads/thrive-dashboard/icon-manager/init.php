<?php

global $tve_icon_manager;
/**
 * add the Icon Manager admin menu
 */
require_once plugin_dir_path(__FILE__) . 'classes/Thrive_Icon_Manager.php';

$tve_icon_manager = Thrive_Icon_Manager::instance();
$tve_icon_manager->mainPage();