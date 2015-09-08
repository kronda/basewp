<?php
/**
 * Created by PhpStorm.
 * User: Danut
 * Date: 8/18/2015
 * Time: 3:18 PM
 *
 * For some reasons the CW tables do not have primary keys set.
 * This script prepares the ID column and set the PRIMARY KEY and also the AUTOCOMPLETE
 *
 */

defined('THRIVE_CLEVER_WIDGETS_DB_UPDATE') or exit();

/**
 * @var $wpdb wpdb
 */
global $wpdb;

$primary = $wpdb->get_results("SHOW INDEXES FROM " . Thrive_Clever_Widgets_Database_Manager::tableName('widgets_options') . " WHERE Key_name = 'PRIMARY'");
if (empty($primary)) {
    //prepare the ID columns
    $sql = "SELECT * FROM `" . Thrive_Clever_Widgets_Database_Manager::tableName('widgets_options') . "`";
    $results = $wpdb->get_results($sql);
    $index = 1;
    if (!empty($results)) {
        foreach ($results as $option) {
            $wpdb->update(Thrive_Clever_Widgets_Database_Manager::tableName('widgets_options'), array('id' => $index++), array('widget' => $option->widget));
        }
    }
    //set the primary key
    $sql = "ALTER TABLE `" . Thrive_Clever_Widgets_Database_Manager::tableName('widgets_options') . "` ADD PRIMARY KEY (id)";
    $wpdb->query($sql);
    //set the autocomplete number
    $sql = "ALTER TABLE `" . Thrive_Clever_Widgets_Database_Manager::tableName('widgets_options') . "` MODIFY COLUMN `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT = {$index}";
    $wpdb->query($sql);
}

//=====================================================//
//=====================================================//
//=====================================================//

$primary = $wpdb->get_results("SHOW INDEXES FROM " . Thrive_Clever_Widgets_Database_Manager::tableName('saved_widgets_options') . " WHERE Key_name = 'PRIMARY'");
if (empty($primary)) {
    //prepare the ID columns
    $sql = "SELECT * FROM `" . Thrive_Clever_Widgets_Database_Manager::tableName('saved_widgets_options') . "`";
    $results = $wpdb->get_results($sql);
    $index = 1;
    if (!empty($results)) {
        foreach ($results as $option) {
            $wpdb->update(Thrive_Clever_Widgets_Database_Manager::tableName('saved_widgets_options'), array('id' => $index++), array('name' => $option->name));
        }
    }
    //set the primary key
    $sql = "ALTER TABLE `" . Thrive_Clever_Widgets_Database_Manager::tableName('saved_widgets_options') . "` ADD PRIMARY KEY (id)";
    $wpdb->query($sql);
    //set the autocomplete number
    $sql = "ALTER TABLE `" . Thrive_Clever_Widgets_Database_Manager::tableName('saved_widgets_options') . "` MODIFY COLUMN `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT = {$index}";
    $wpdb->query($sql);
}
