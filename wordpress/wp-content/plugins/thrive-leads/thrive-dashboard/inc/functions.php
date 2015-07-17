<?php

if (!function_exists('thrive_dashboard_url')) {
    /**
     * This function returns the URL to thrive dashboard
     * @return string URL with / appended
     */
    function thrive_dashboard_url()
    {
        return plugins_url() . '/thrive-visual-editor//thrive-dashboard/';
    }
}

if (!function_exists('thrive_dashboard_path')) {
    /**
     * This function returns the PATH to thrive dashboard
     * @return string URL with / appended
     */
    function thrive_dashboard_path()
    {
        return plugin_dir_path(dirname(__FILE__));
    }
}

/**
 * wrapper over the wp_enqueue_script functions
 * it will add the version
 *
 * @param $handle
 * @param string $src
 * @param array $deps
 * @param bool $ver
 * @param bool $in_footer
 */
function thrive_dashboard_enqueue_script($handle, $src = '', $deps = array(), $ver = false, $in_footer = false)
{
    if ($ver === false) {
        $ver = "1.0";
    }
    wp_enqueue_script($handle, $src, $deps, $ver, $in_footer);
}


/**
 * wrapper over the wp enqueue_style function
 * it will add the version
 *
 * @param $handle
 * @param $src
 * @param array $deps
 * @param bool $ver
 * @param $media
 */
function thrive_dashboard_enqueue_style($handle, $src, $deps = array(), $ver = false, $media = 'all')
{
    if ($ver === false) {
        $ver = "1.0";
    }
    wp_enqueue_style($handle, $src, $deps, $ver, $media);
}

/**
 * Returns products to be displayed on Thrive Dashboard
 *
 * Item properties:
 * @param name string required
 * @param image_url string required
 * @param links array required
 * @param tag string unique
 * @param description string
 *
 * Link properties:
 * @param caption string
 * @param url string
 * @param css_class string
 *
 * @call apply_filters for tag "thrive_dashboard_installed_products"
 * @return array
 */
function thrive_dashboard_get_products()
{
    $products = array();
    $products = apply_filters('thrive_dashboard_installed_products', $products);

    return $products;
}

/**
 * Returns all non licensed Thrive products
 * Each item of the returned array will have the fallowing indexes:
 * @param tag string unique
 * @param image_url string Required; image to be shown in License Manager page
 * @param api_url string license validation API url
 * @param ids array ids to be sent to license validation API
 *
 * @call apply_filters for tag "thrive_dashboard_no_license_products"
 * @return array|mixed|void
 */
function thrive_dashboard_get_no_license_products()
{

    $products = array();

    $products = apply_filters('thrive_dashboard_no_license_products', $products);

    return $products;
}

/**
 * Returns all licensed Thrive products
 * Each item of the returned array will have the fallowing indexes:
 * @param name string
 *
 * @call apply_filters for tag "thrive_dashboard_licensed_products"
 * @return array|mixed|void
 */
function thrive_dashboard_get_licensed_products()
{
    $products = array();

    $products = apply_filters('thrive_dashboard_licensed_products', $products);

    return $products;
}

/**
 * Returns all the integrations items
 * Each item of the returned array will have the following intexes:
 * @param name string
 * @param description string
 * @param image_url
 * @param url
 *
 * @call apply_filters for tag "thrive_dashboard_integrations_products"
 * @return array|mixed|void
 */
function thrive_dashboard_get_integrations_products()
{
    $products = array();

    $products = apply_filters('thrive_dashboard_integrations_products', $products);

    return $products;
}


/**
 * Push the $message in session to be displayed later
 * $message is an array with following indexes:
 * @param text string
 * @param css_class string
 *
 * @param $message
 */
function thrive_dashboard_add_message($message)
{
    $messages = get_option('td_messages', array());

    if (is_string($message)) {
        $message = array(
            'text' => $message,
            'css_class' => 'info'
        );
    }

    $messages[] = $message;
    update_option('td_messages', $messages);
}

/**
 * Displays messages from session and clear them after
 */
function thrive_dashboard_display_messages()
{
    $messages = get_option('td_messages', array());

    foreach ($messages as $message) {
        if (empty($message['text'])) {
            continue;
        }
        echo '<div class="tvedash-alert tvedash-alert-' . (isset($message['css_class']) ? $message['css_class'] : 'info') . '">' . $message['text'] . '</div>';
    }

    update_option('td_messages', array());
}

/**
 * Returns a list of all Thrive Products
 * This list will be displayed in dashboard section but the already activated products will be stripped off in later logic
 * @param $type String Type of product to sort. If it's left empty, all products will be returned
 * @param $sort Boolean Sort result array alphabetically
 *
 * @return array of StdClass products
 */
function thrive_dashboard_get_thrive_products($type = '', $sort = true)
{
    $products = array();

    $response = wp_remote_get(THRIVE_DASHBOARD_PRODUCTS_URL, array('sslverify' => false));
    if (is_wp_error($response)) {
        return $products;
    }

    $products = json_decode($response['body']);
    if ($type != '') {
        foreach ($products as $key => $product) {
            if ($product->type != $type) {
                unset($products[$key]);
            }
        }
    }

    if ($sort == true) {
        usort($products, function ($a, $b) {
            return strcasecmp($a->name, $b->name);
        });
    }

    return $products;
}

/**
 * Returns a list of all Thrive Themes
 * This list will be displayed in dashboard section and the ones already owned will be checked
 *
 * @return array of StdClass themes
 */
function thrive_dashboard_get_all_themes()
{
    $themes = thrive_dashboard_get_thrive_products('theme');
    $current_theme = wp_get_theme();

    foreach ($themes as $key => $theme) {

        if ($current_theme->name == $theme->name) {
            unset($themes[$key]);
            continue;
        }

        $local_theme = wp_get_theme($theme->tag);
        if ($local_theme->exists()) {
            $theme->installed = true;
        } else {
            $theme->installed = false;
        }
    }

    return $themes;
}

/**
 * Checks the global variable that's set in all thrive themes to check if the user is using a Thrive Theme
 **/
function thrive_dashboard_is_thrive_theme()
{
    global $is_thrive_theme;
    if (isset($is_thrive_theme) && $is_thrive_theme == true) {
        return true;
    } else {
        return false;
    }
}

add_action('wp_ajax_thrive_change_current_theme', 'thrive_change_current_theme');
add_action('wp_ajax_nopriv_thrive_change_current_theme', 'thrive_change_current_theme');

/**
 * Change current theme to another one specified by tag.
 */
function thrive_change_current_theme()
{
    $theme_tag = isset($_POST['theme']) ? $_POST['theme'] : '';
    $new_theme = wp_get_theme($theme_tag);

    if ($new_theme->exists()) {
        switch_theme($theme_tag);
        die('success');
    } else {
        die('error');
    }

}

/**
 * Verify if a license is activated or not through the API
 * @param $licensed_email
 * @param string $license_key
 * @param array $product_ids
 * @return array|mixed|stdClass
 */
function thrive_dashboard_license_check($licensed_email, $license_key = '', $product_ids = array())
{
    $api_url = THRIVE_DASHBOARD_LICENSE_API_URL;
    $api_url .= "?license=" . $license_key;
    $api_url .= "&email=" . $licensed_email;
    $api_url .= "&product_id=" . implode(',', $product_ids);
    $licenseValid = wp_remote_get($api_url, array('sslverify' => false, 'timeout' => 120));

    if (is_wp_error($licenseValid)) {
        /** @var WP_Error $licenseValid */
        /** Couldn't connect to the API URL - possible because wp_remote_get failed for whatever reason.  Maybe CURL not activated on server, for instance */
        $response = new stdClass();
        $response->success = 0;
        $response->reason = sprintf(__("An error occurred while connecting to the license server. Error: %s. Please login to thrivethemes.com, report this error message on the forums and we'll get this sorted for you"), $licenseValid->get_error_message());

        return $response;
    }

    $response = @json_decode($licenseValid['body']);

    if (empty($response)) {
        $response = new stdClass();
        $response->success = 0;
        $response->reason = sprintf(__("An error occurred while receiving the license status. The response was: %s. Please login to thrivethemes.com, report this error message on the forums and we'll get this sorted for you."), $licenseValid['body']);

        return $response;
    }

    return $response;
}
