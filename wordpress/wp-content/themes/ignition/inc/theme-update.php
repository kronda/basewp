<?php

/* * ****************Change this****************** */
$thrive_update_api_url = 'https://thrivethemes.com/dl-37718237045349/';

/* * *********************Parent Theme************* */
if (function_exists('wp_get_theme')) {
    $thrive_theme_data = wp_get_theme(get_option('template'));
    $thrive_theme_version = $thrive_theme_data->Version;
} else {
    $thrive_theme_data = get_theme_data(TEMPLATEPATH . '/style.css');
    $thrive_theme_version = $thrive_theme_data['Version'];
}
$thrive_theme_package_name = strtolower($thrive_theme_data['Name']);
$thrive_theme_base = get_option('template');
/* * *********************************************** */

//Uncomment below to find the theme slug that will need to be setup on the api server
add_filter('pre_set_site_transient_update_themes', 'thrive_check_for_update');

function thrive_check_for_update($checked_data)
{

    global $wp_version, $thrive_theme_version, $thrive_theme_base, $thrive_update_api_url, $thrive_theme_package_name;

    $request = array(
        'slug' => $thrive_theme_package_name,
        'version' => $thrive_theme_version
    );
    // Start checking for an update
    $send_for_check = thrive_prepare_request('theme_update', $request, array('sslverify' => false));

    $raw_response = wp_remote_post($thrive_update_api_url, $send_for_check);

    //TODO - check the response body better
    if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200)) {
        if (!empty($raw_response['body']) && strpos($raw_response['body'], "Warning") === false) {
            $response = unserialize($raw_response['body']);
        } else {
            $response = false;
        }
    }


    // Feed the update data into WP updater
    if (!empty($response))
        $checked_data->response[$thrive_theme_base] = $response;

    return $checked_data;
}

// Take over the Theme info screen on WP multisite
add_filter('themes_api', 'thrive_theme_api_call', 10, 3);

function thrive_theme_api_call($def, $action, $args)
{
    global $thrive_theme_base, $thrive_update_api_url, $thrive_theme_version, $thrive_update_api_url;

    if ($args->slug != $thrive_theme_base)
        return false;

    // Get the current version

    $args->version = $thrive_theme_version;
    $request_string = thrive_prepare_request($action, $args);
    $request = wp_remote_post($thrive_update_api_url, $request_string);

    if (is_wp_error($request)) {
        $res = new WP_Error('themes_api_failed', __('An Unexpected HTTP Error occurred during the API request', 'thrive') . '</p> <p><a href="?" onclick="document.location.reload(); return false;"> ' . __('Try again', 'thrive') . '', $request->get_error_message());
    } else {
        $res = unserialize($request['body']);

        if ($res === false)
            $res = new WP_Error('themes_api_failed', __('An unknown error occurred', 'thrive'), $request['body']);
    }

    return $res;
}

/**
 * Prepare request for api call
 *
 * @param $action
 * @param $args
 * @param $extra_settings
 * @return array
 */
function thrive_prepare_request($action = '', $args = array(), $extra_settings = array())
{
    global $wp_version;

    $request = array_merge(
        $extra_settings,
        array(
            'body' => array(
                'action' => $action,
                'request' => serialize($args),
                'api-key' => md5(home_url())
            ),
            'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url()
        )
    );

    return $request;
}

if (is_admin())
    $current = get_transient('update_themes');
?>