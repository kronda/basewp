<?php

defined('THRIVE_CLEVER_WIDGETS_DB_UPDATE') or exit();

/**
 * @var $wpdb wpdb
 */
global $wpdb;

$old_widgets_options_table = Thrive_Clever_Widgets_Database_Manager::TCW_DATABASE_PREFIX . "widgets_options";
$new_widget_options_table = Thrive_Clever_Widgets_Database_Manager::tableName('widgets_options');

/**
 * Create new table from the old one
 */
$sql = "CREATE TABLE IF NOT EXISTS `{$new_widget_options_table}` SELECT * FROM `$old_widgets_options_table`";
$wpdb->query($sql);

$old_saved_widgets_options_table = Thrive_Clever_Widgets_Database_Manager::TCW_DATABASE_PREFIX . "saved_widgets_options";
$new_saved_widgets_options_table = Thrive_Clever_Widgets_Database_Manager::tableName("saved_widgets_options");

/**
 * Create new table from the old one
 */
$sql = "CREATE TABLE IF NOT EXISTS `{$new_saved_widgets_options_table}` SELECT * FROM `$old_saved_widgets_options_table`";
$wpdb->query($sql);
