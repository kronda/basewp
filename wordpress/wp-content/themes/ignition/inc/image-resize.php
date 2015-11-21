<?php

/*
 * Get the featured image source for a post
 * @param int $post_id
 * @param int $size
 * @param String $default_featured - display default featured image
 * @return string|bool Featured image source. False if it doesn't exist
 */
function thrive_get_post_featured_image_src($post_id = 0, $size = "medium", $default_featured = false)
{

    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $size = _thrive_filter_default_image_sizes($size);

    if (has_post_thumbnail($post_id)) {

        $attachment_id = get_post_thumbnail_id($post_id);
        $attachment_meta = wp_get_attachment_metadata($attachment_id);


        if (!isset($attachment_meta['sizes'][$size]) || !is_array($attachment_meta['sizes'][$size])) {
            //if the size if not available return the original as it is the closest to the required dimension
            return wp_get_attachment_url($attachment_id);
        }


        $featured_image_src = wp_get_attachment_image_src($attachment_id, $size);

        if (isset($featured_image_src[0])) {
            return $featured_image_src[0];
        }
    } else {
        $featured_image_src = $default_featured ? get_template_directory_uri() . "/images/default_featured.jpg" : "";
    }

    return $featured_image_src;
}

/*
 * Get the featured image data for a post
 * @param int $post_id
 * @param int $size
 * @param String $default_featured - display default featured image
 * @return string|bool Featured image source. False if it doesn't exist
 */
function thrive_get_post_featured_image($post_id = 0, $size = "medium", $default_featured = false)
{
    $image_data = array("image_src" => "", "image_alt" => "", "image_title" => "");

    if (!$post_id) {
        $post_id = get_the_ID();
    }
    $size = _thrive_filter_default_image_sizes($size);

    if (has_post_thumbnail($post_id)) {
        $attachment_id = get_post_thumbnail_id($post_id);
        $attachment_meta = wp_get_attachment_metadata($attachment_id);

        $image_data["image_alt"] = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
        $image_data["image_title"] = $attachment = get_post($attachment_id)->post_title;

        if (!isset($attachment_meta['sizes'][$size]) || !is_array($attachment_meta['sizes'][$size])) {
            //if the size if not available return the original as it is the closest to the required dimension
            $image_data["image_src"] = wp_get_attachment_url($attachment_id);
            return $image_data;
        }

        $featured_image_src = wp_get_attachment_image_src($attachment_id, $size);
        if (isset($featured_image_src[0])) {
            $image_data["image_src"] = $featured_image_src[0];
            return $image_data;
        }
    } else {
        $image_data["image_src"] = $default_featured ? get_template_directory_uri() . "/images/default_featured.jpg" : "";
    }
    return $image_data;
}

add_action("wp_ajax_thrive_optimize_image_sizes", "thrive_optimize_image_sizes");
add_action("wp_ajax_nopriv_thrive_optimize_image_sizes", "thrive_optimize_image_sizes");

function thrive_optimize_image_sizes()
{

    /*
     * Set the resize type
     */
    if (isset($_POST['resize_type']) && ($_POST['resize_type'] == TT_IMG_RESIZE_TYPE_SCALE_AND_CROP || $_POST['resize_type'] == TT_IMG_RESIZE_TYPE_SCALE)) {
        $tt_resize_type = $_POST['resize_type'];
    } else { //if the type is set to default, update the status and exit
        $tt_resize_type = TT_IMG_RESIZE_TYPE_DEFAULT;
    }

    if (!wp_verify_nonce($_REQUEST['nonce'], "thrive_optimize_image_sizes")) {
        $response = array(
            'status' => -1,
            'message' => __("Security error! Refresh the page and try again.", 'thrive'),
            'resize_type' => $tt_resize_type,
            'location' => 111
        );
        wp_send_json($response);
    }

    //cancel the resize process
    if (isset($_POST['cancel_process']) && $_POST['cancel_process'] == 1) {
        update_option("thrive_resized_images_json", "");
        _thrive_set_image_resize_optimization_status(TT_IMG_RESIZE_STATUS_NOT_STARTED);
        wp_send_json(1);
    }

    //update the image resize type
    update_option("thrive_image_resize_type", $tt_resize_type);

    /*
     * If default, exit here
     */
    if ($tt_resize_type == TT_IMG_RESIZE_TYPE_DEFAULT) {
        $response = array(
            'status' => 0,
            'message' => __("Use the default wordpress sizes set successfully", 'thrive'),
            'resize_type' => $tt_resize_type,
            'location' => 111
        );
        wp_send_json($response);
    }

    $optimize_status = _thrive_get_image_resize_optimization_status();

    /*
     * If the resize is already finished and the previous resize type matches the current request, exit here
     * Otherwise, it means that we should reset the processed images array in order to rerun the resize process
     */
    if ($optimize_status == TT_IMG_RESIZE_STATUS_FINISHED && get_option("thrive_last_image_resize_type") == $tt_resize_type) {
        $response = array(
            'status' => $optimize_status,
            'message' => __("Optimization finalized successfully", 'thrive'),
            'resize_type' => $tt_resize_type,
            'location' => 222
        );
        wp_send_json($response);
    } elseif (get_option("thrive_last_image_resize_type") != $tt_resize_type && get_option("thrive_last_image_resize_type") != TT_IMG_RESIZE_TYPE_DEFAULT) {
        update_option("thrive_resized_images_json", "");
        update_option("thrive_last_image_resize_type", TT_IMG_RESIZE_TYPE_DEFAULT);
    }

    if ($optimize_status != TT_IMG_RESIZE_STATUS_STARTED) {
        _thrive_set_image_resize_optimization_status(TT_IMG_RESIZE_STATUS_STARTED);
    }

    $image_ids = _thrive_get_all_image_ids_from_media_library();

    foreach ($image_ids as $attachment_id) {
        if (_thrive_check_if_resize_needed($attachment_id)) {
            $resize_result = _thrive_create_resized_versions_for_image($attachment_id);
            $attachment_meta = wp_get_attachment_metadata($attachment_id);

            $filename = ($attachment_meta && isset($attachment_meta['file'])) ? $attachment_meta['file'] : "";

            $response = array(
                'status' => TT_IMG_RESIZE_STATUS_STARTED,
                'filename' => $filename,
                'message' => __("Optimization in progress. Previous optimized file: " . $filename),
                'resize_result' => $resize_result,
                'resize_type' => $tt_resize_type,
                'location' => 333
            );

            wp_send_json($response);
        }
    }

    //if no image needed optimization, update the image optimization status to finalized
    _thrive_set_image_resize_optimization_status(TT_IMG_RESIZE_STATUS_FINISHED);
    //and also update the last successful image optimization type option
    update_option("thrive_last_image_resize_type", $tt_resize_type);

    $response = array(
        'status' => TT_IMG_RESIZE_STATUS_FINISHED,
        'message' => __("Optimization finalized successfully."),
        'resize_type' => $tt_resize_type,
        'location' => 444
    );

    wp_send_json($response);
}

/*
 * Checks if one of the theme required sizes is not available for a specific image
 * @param integer $attachment_id
 * @return boolean
 */
function _thrive_check_if_resize_needed($attachment_id)
{
    $attachment_meta = wp_get_attachment_metadata($attachment_id);
    $file_path = get_attached_file($attachment_id);

    if (!file_exists($file_path)) {
        return false;
    }

    if (_thrive_check_if_image_processed($attachment_id) !== false) {
        return false;
    }

    return true;

}

/*
 * Return an array of all the images from the media library
 */
function _thrive_get_all_image_ids_from_media_library()
{
    $args = array(
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'post_status' => 'inherit',
        'posts_per_page' => -1,

    );

    $query_images = new WP_Query($args);
    $images = array();

    foreach ($query_images->posts as $image) {
        $images[] = $image->ID;
    }

    return $images;
}

/*
 * Generates the image at the required sizes
 * @param $attachment_id
 */
function _thrive_create_resized_versions_for_image($attachment_id)
{
    if (!defined('thrive_image_sizes_registered')) {
        thrive_register_new_image_sizes();
    }

    //we set the status to -1 so if an error occurs we will skip the file next time
    _thrive_set_image_resized($attachment_id, -1);

    $file_path = get_attached_file($attachment_id);
    $new_attachment_meta = wp_generate_attachment_metadata($attachment_id, $file_path);
    $result = true;

    if (is_wp_error($new_attachment_meta)) {
        _thrive_set_image_resized($attachment_id, -1);
        $result = false;
    }

    wp_update_attachment_metadata($attachment_id, $new_attachment_meta);

    _thrive_set_image_resized($attachment_id, 1);

    return $result;

}

/*
 * Returns the status of the image resize process
 * @return int
 */
function _thrive_get_image_resize_optimization_status()
{

    $status = get_option("thrive_image_resize_optimization_status");

    if ($status != TT_IMG_RESIZE_STATUS_NOT_STARTED && $status != TT_IMG_RESIZE_STATUS_STARTED && $status != TT_IMG_RESIZE_STATUS_FINISHED) {
        _thrive_set_image_resize_optimization_status(TT_IMG_RESIZE_STATUS_NOT_STARTED);
    }

    return $status;

}

/*
 * Sets the status of the image optimization option
 * @return Int
 */
function _thrive_set_image_resize_optimization_status($status)
{
    if ($status != TT_IMG_RESIZE_STATUS_NOT_STARTED && $status != TT_IMG_RESIZE_STATUS_STARTED && $status != TT_IMG_RESIZE_STATUS_FINISHED) {
        $status = TT_IMG_RESIZE_STATUS_NOT_STARTED;
    }

    update_option("thrive_image_resize_optimization_status", $status);
}

/*
 * Checks if an image was already processed
 * @param int $attachment_id
 * @return boolean
 */
function _thrive_check_if_image_processed($attachment_id)
{
    $tt_resized_images = json_decode(get_option("thrive_resized_images_json"), true);

    if (empty($tt_resized_images) || !is_array($tt_resized_images)) {
        return false;
    }

    if (isset($tt_resized_images[$attachment_id])) {
        return $tt_resized_images[$attachment_id];
    }

    return false;
}

/*
 * Sets an image as processed
 * @param int $attachment_id
 * @param int $process_result - the resize result, 1 for success, -1 for failure
 */
function _thrive_set_image_resized($attachment_id, $process_result = 1)
{

    $tt_resized_images = json_decode(get_option("thrive_resized_images_json"), true);

    if (empty($tt_resized_images) || !is_array($tt_resized_images)) {
        $tt_resized_images = array();
    }

    $tt_resized_images[$attachment_id] = $process_result;

    update_option("thrive_resized_images_json", json_encode($tt_resized_images));
}

/*
 * Filter the default wordpress resized images to match one of ours
 */
function _thrive_filter_default_image_sizes($size)
{

    $tt_img_resize_type = _thrive_get_image_resize_type();

    if ($tt_img_resize_type == TT_IMG_RESIZE_TYPE_DEFAULT) {

        switch ($size) {
            case "tt_featured_thumbnail":
            case "tt_post_icon":
            case "tt_latest_images":
                return "thumbnail";
            case "tt_grid_layout":
            case "tt_related_posts":
            case "extended_menu":
            case "tt_related_widget":
            case "tt_extended_menu":
            case "tt_post_gallery":
                return "medium";
            case "tt_featured_wide_narrow":
            case "tt_featured_wide_sidebar":
            case "tt_featured_wide_full":
                return "large";
        }

    } else {

        switch ($size) {
            case "thumbnail":
                return "tt_featured_thumbnail";
            case "medium":
                return "tt_grid_layout";
            case "wide":
                return "tt_featured_wide_full";
        }

    }
    return $size;
}

add_action("wp_ajax_thrive_image_resize_dismiss_notification", "thrive_image_resize_dismiss_notification");
add_action("wp_ajax_nopriv_thrive_image_resize_dismiss_notification", "thrive_image_resize_dismiss_notification");

function thrive_image_resize_dismiss_notification()
{

    if (!wp_verify_nonce($_REQUEST['nonce'], "thrive_image_resize_dismiss_notification")) {
        die(0);
    }
    update_option("thrive_optimize_image_dismiss_notification", 1);
    wp_send_json(1);
}


/*
 * Check if we should display the admin notification about the image resize
 * @return boolean
 */
function _thrive_check_display_admin_notification()
{
    $status = _thrive_get_image_resize_optimization_status();
    $dismiss_notif_option = get_option("thrive_optimize_image_dismiss_notification");

    if ($status == TT_IMG_RESIZE_STATUS_NOT_STARTED && $dismiss_notif_option != 1) {
        return true;
    }

    return false;
}


if (_thrive_check_display_admin_notification()) {
    add_action('admin_notices', 'thrive_image_resize_optimization_notification');
}

/*
 * Display the notification message
 */
function thrive_image_resize_optimization_notification()
{
    $theme_options_url = menu_page_url("thrive_admin_options", false) . "#performance-options";

    $dismiss_wpnonce = wp_create_nonce("thrive_image_resize_dismiss_notification");
    $dismiss_notif_url = admin_url('admin-ajax.php?action=thrive_image_resize_dismiss_notification&nonce=' . $dismiss_wpnonce);

    $message = "<div class='update-nag'>Your image thumbnails are currently not optimized for this theme. <a href='" . $theme_options_url . "'>Click here </a> to resize the thumbnails. ";

    $message .= "<a id='tt-link-dismiss-image-optimize' href=''>Dismiss this message</a></div>";

    echo $message;

    /*
     * Script to handle the dismiss message action
     */
    ?>

    <script type="text/javascript">
        jQuery("#tt-link-dismiss-image-optimize").click(function (event) {
            event.preventDefault();
            var _tt_dismiss_image_notif_url = "<?php echo $dismiss_notif_url;?>";
            var _tt_dismiss_image_notif_params = {
                nonce: "<?php echo $dismiss_wpnonce;?>"
            };

            jQuery.post(_tt_dismiss_image_notif_url, _tt_dismiss_image_notif_params, function (result) {
                if (result == 1) {
                    jQuery("#tt-link-dismiss-image-optimize").parent().hide();
                }
            });
            return false;
        });
    </script>

<?php
}

/*
 * Register the image sizes used in the theme
 */

add_action('after_setup_theme', 'thrive_register_new_image_sizes');

function thrive_register_new_image_sizes()
{
    if (defined('thrive_image_sizes_registered')) {
        return;
    }
    $tt_image_sizes = _thrive_get_theme_image_sizes_array();

    foreach ($tt_image_sizes as $name => $size) {
        add_image_size($name, $size[0], $size[1], 1);
    }
    define('thrive_image_sizes_registered', true);
}

add_filter('image_resize_dimensions', 'thrive_custom_image_resize_dimensions', 10, 6);
function thrive_custom_image_resize_dimensions($payload, $orig_w, $orig_h, $dest_w, $dest_h, $crop)
{

    $tt_img_resize_type = _thrive_get_image_resize_type();
    //if the crop param is set to true for the current registered size and if the resize option is set to scale and crop
    //leave the crop the same as wordpress default (force the crop from the center)
    if ($crop && $tt_img_resize_type == TT_IMG_RESIZE_TYPE_SCALE_AND_CROP) {
        // crop the largest possible portion of the original image that we can size to $dest_w x $dest_h
        $aspect_ratio = $orig_w / $orig_h;
        $new_w = min($dest_w, $orig_w);
        $new_h = min($dest_h, $orig_h);

        if (!$new_w) {
            $new_w = (int)round($new_h * $aspect_ratio);
        }

        if (!$new_h) {
            $new_h = (int)round($new_w / $aspect_ratio);
        }

        $size_ratio = max($new_w / $orig_w, $new_h / $orig_h);

        $crop_w = round($new_w / $size_ratio);
        $crop_h = round($new_h / $size_ratio);

        if (!is_array($crop) || count($crop) !== 2) {
            $crop = array('center', 'center');
        }

        list($x, $y) = $crop;

        if ('left' === $x) {
            $s_x = 0;
        } elseif ('right' === $x) {
            $s_x = $orig_w - $crop_w;
        } else {
            $s_x = floor(($orig_w - $crop_w) / 2);
        }

        if ('top' === $y) {
            $s_y = 0;
        } elseif ('bottom' === $y) {
            $s_y = $orig_h - $crop_h;
        } else {
            $s_y = floor(($orig_h - $crop_h) / 2);
        }
    } else {
        // don't crop, just resize using $dest_w x $dest_h as a maximum bounding box
        $crop_w = $orig_w;
        $crop_h = $orig_h;

        $s_x = 0;
        $s_y = 0;

        list($new_w, $new_h) = wp_constrain_dimensions($orig_w, $orig_h, $dest_w, $dest_h);
    }

    // if the resulting image would be the same size or larger we don't want to resize it
    if ($new_w >= $orig_w && $new_h >= $orig_h)
        return false;

    // the return array matches the parameters to imagecopyresampled()
    // int dst_x, int dst_y, int src_x, int src_y, int dst_w, int dst_h, int src_w, int src_h
    return array(0, 0, (int)$s_x, (int)$s_y, (int)$new_w, (int)$new_h, (int)$crop_w, (int)$crop_h);

}

/*
 * Returns the image resize type option, if no one is set return "use default wordpress sizes" as default
 */
function _thrive_get_image_resize_type()
{
    $tt_resize_type = get_option("thrive_image_resize_type");
    if (empty($tt_resize_type) ||
              ($tt_resize_type != TT_IMG_RESIZE_TYPE_SCALE
            && $tt_resize_type != TT_IMG_RESIZE_TYPE_SCALE_AND_CROP
            && $tt_resize_type != TT_IMG_RESIZE_TYPE_DEFAULT)) {
        $tt_resize_type = TT_IMG_RESIZE_TYPE_DEFAULT;
    }
    return $tt_resize_type;
}


function _thrive_get_last_image_resize_type()
{
    $tt_resize_type = get_option("thrive_last_image_resize_type");
    if (empty($tt_resize_type) ||
              ($tt_resize_type != TT_IMG_RESIZE_TYPE_SCALE
            && $tt_resize_type != TT_IMG_RESIZE_TYPE_SCALE_AND_CROP
            && $tt_resize_type != TT_IMG_RESIZE_TYPE_DEFAULT)) {
        $tt_resize_type = TT_IMG_RESIZE_TYPE_DEFAULT;
    }
    return $tt_resize_type;
}

