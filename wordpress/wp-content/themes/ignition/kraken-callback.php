<?php

error_reporting(0);

$wp_path = "../../../wp-load.php";

require_once $wp_path;

if (!isset($_POST['kraked_url']) || !isset($_POST['id'])) {
    die;
}

$option_str = get_option($_POST['id']);

if (!$option_str) {
    die;
}

$options = explode("***", $option_str);
if (!isset($options[0]) || !isset($options[1])) {
    die;
}

$aid = $options[0];
$file_path = $options[1];

if (!$aid || !$file_path) {
    die;
}

$meta = wp_get_attachment_metadata($aid);

if (!empty($_POST['file_already_compressed'])) {
    $meta_info_msg = "File is already compressed";
    $meta['wp_kraken'] = $meta_info_msg;
    wp_update_attachment_metadata($aid, $meta);
    die;
}

/*
 * On success overwrite the current file
 */
$original_size = filesize($file_path);
$response = wp_remote_get($_POST['kraked_url']);
$image_string = wp_remote_retrieve_body($response);

if (is_wp_error($response) || empty($image_string)) {
    $meta_info_msg = "Compress failed";
    $meta['wp_kraken'] = $meta_info_msg;
    wp_update_attachment_metadata($aid, $meta);
    die;
}
$new_size = file_put_contents($file_path, $image_string);

$meta_info_msg = "Compressed (saved " . ($original_size - $new_size) . " bytes)";
$meta['wp_kraken'] = $meta_info_msg;
wp_update_attachment_metadata($aid, $meta);
die;
/*
 * Helper function used to log the response in a file. Not used in production.
 */
function log_client($content) {
    $file = 'log_client.txt';

    $current = file_get_contents($file);

    $current .= $content . "\n";
    $current .= "--------------------------------------- \n";

    file_put_contents($file, $current);
}