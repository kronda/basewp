<?php

defined('THRIVE_CLEVER_WIDGETS_DB_UPDATE') or exit();

/**
 * @var $wpdb wpdb
 */
global $wpdb;

$widgets_options_table = Thrive_Clever_Widgets_Database_Manager::TCW_DATABASE_PREFIX . "widgets_options";

$sql = "CREATE TABLE " . $widgets_options_table . " (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `widget` VARCHAR(255) NOT NULL,
    `description` VARCHAR(255),
    `show_widget_options` TEXT NOT NULL,
    `hide_widget_options` TEXT NOT NULL,
    PRIMARY KEY (`id`)
)";
$wpdb->query($sql);

$saved_widgets_options_table = Thrive_Clever_Widgets_Database_Manager::TCW_DATABASE_PREFIX . "saved_widgets_options";

$sql = "CREATE TABLE " . $saved_widgets_options_table . " (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `show_widget_options` TEXT NOT NULL,
    `hide_widget_options` TEXT NOT NULL,
    PRIMARY KEY (`id`)
)";
$wpdb->query($sql);
