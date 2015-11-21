<?php

add_filter('wp_generate_attachment_metadata', 'thrive_update_attachment', 10, 2);

add_filter('manage_media_columns', 'thrive_media_columns');
add_action('manage_media_custom_column', 'thrive_media_custom_column', 10, 2);

add_action('admin_action_wp_kraken_manual', 'thrive_process_single_kraken_image');

function thrive_update_attachment($meta, $ID) {

    $optimize_image_type = thrive_get_theme_options('image_optimization_type');
    if ($optimize_image_type == "off") {
        return $meta;
    }
    $lossy = 0;    
    if ($optimize_image_type == "lossy") {
        $lossy = 1;
    }

    $attachment_file_path = get_attached_file($ID);
    $attachment_file_url = wp_get_attachment_url($ID);
    $process_result = thrive_process_kraken_image($attachment_file_url, $attachment_file_path, $ID, $lossy);
    $meta['wp_kraken'] = $process_result;

    if (!isset($meta['sizes']))
        return $meta;

    foreach ($meta['sizes'] as $size_key => $size_data) {

        $attachment_file_path_size = trailingslashit(dirname($attachment_file_path)) . $size_data['file'];
        $attachment_file_url_size = trailingslashit(dirname($attachment_file_url)) . $size_data['file'];
        thrive_process_kraken_image($attachment_file_url_size, $attachment_file_path_size, $ID, $lossy);
    }

    return $meta;
}

function thrive_media_columns($defaults) {
    $optimize_image_type = thrive_get_theme_options('image_optimization_type');
    
    if ($optimize_image_type == "off") {
        return $defaults;
    }
    $defaults['kraken'] = 'Optimize';
    return $defaults;
}

function thrive_media_custom_column($column_name, $id) {
    $optimize_image_type = thrive_get_theme_options('image_optimization_type');
    if ($optimize_image_type == "off") {
        return $defaults;
    }
    if ('kraken' == $column_name) {
        $data = wp_get_attachment_metadata($id);
        if (isset($data['wp_kraken']) && !empty($data['wp_kraken'])) {
            print $data['wp_kraken'];
            printf("<br><a href=\"admin.php?action=wp_kraken_manual&amp;attachment_ID=%d\">%s</a>", $id, __('Re-compress', 'thrive'));
        } else {
            if (wp_attachment_is_image($id)) {
                print __('Not processed', 'thrive');
                printf("<br><a href=\"admin.php?action=wp_kraken_manual&amp;attachment_ID=%d\">%s</a>", $id, __('Compress', 'thrive'));
            }
        }
    }
}

function thrive_process_single_kraken_image() {
    if (!current_user_can('upload_files')) {
        wp_die(__("You don't have permission to work with uploaded files.", 'thrive'));
    }

    if (!isset($_GET['attachment_ID'])) {
        wp_die(__('No attachment ID was provided.', 'thrive'));
    }

    $attachment_ID = intval($_GET['attachment_ID']);

    $original_meta = wp_get_attachment_metadata($attachment_ID);

    $new_meta = thrive_update_attachment($original_meta, $attachment_ID);
    wp_update_attachment_metadata($attachment_ID, $new_meta);

    wp_redirect(preg_replace('|[^a-z0-9-~+_.?#=&;,/:]|i', '', wp_get_referer()));
    exit();
}

function thrive_process_kraken_image($file_url, $file_path, $aid, $lossy = 1) {
    require_once 'libs/ThriveOptimize.php';
    
    $thriveOptimize = new ThriveOptimize();

    $kraken_callback_url = get_template_directory_uri() . "/kraken-callback.php";

    $params = array(
        "file_url" => $file_url,
        "callback_url" => $kraken_callback_url,
        "lossy" => $lossy
    );

    $data = $thriveOptimize->url($params);
    
    if (!isset($data['id']) && isset($data['message'])) {
        return $data['message'];
    }
    
    if (!isset($data["id"])) {
        return "Compress failed";
    }
   
    $option_content = $aid . "***" . $file_path;
    add_option($data["id"], $option_content);

   
    return "Compress in progress (refresh to see the result)";
}