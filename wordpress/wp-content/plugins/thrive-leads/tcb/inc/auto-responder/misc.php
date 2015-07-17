<?php
/**
 * Contains:
 * - autoloaders for the main library files
 * - wrappers over WordPress wp_remote_* functions
 * - helper functions
 */

/**
 * Wrapper over the WP wp_remote_get function
 *
 * @see wp_remote_get
 *
 * @param string $url Site URL to retrieve.
 * @param array $args Optional. Request arguments. Default empty array.
 * @return WP_Error|array The response or WP_Error on failure.
 */
function thrive_api_remote_get($url, $args = array())
{
    $args['sslverify'] = false;
    return wp_remote_get($url, $args);
}

/**
 * Wrapper over the WP wp_remote_post function - all API POST requests pass through this function
 *
 * @see wp_remote_post
 *
 * @param string $url Site URL to retrieve.
 * @param array $args Optional. Request arguments. Default empty array.
 * @return WP_Error|array The response or WP_Error on failure.
 */
function thrive_api_remote_post($url, $args = array())
{
    $args['sslverify'] = false;
    return wp_remote_post($url, $args);
}

/**
 *
 * Wrapper over the WP wp_remote_request function - API external calls pass through this function
 * sets some default parameters
 *
 * @see wp_remote_request
 *
 * @param string $url Site URL to retrieve.
 * @param array $args Optional. Request arguments. Default empty array.
 * @return WP_Error|array The response or WP_Error on failure.
 */
function thrive_api_remote_request($url, $args = array())
{
    $args['sslverify'] = false;
    return wp_remote_request($url, $args);
}

/**
 * handle the actual autoload (require_once)
 *
 * @param string $basepath
 * @param string $className
 *
 * @return bool|void
 */
function _thrive_api__autoload($basepath, $className)
{
    $parts = explode('_', $className);
    if (empty($parts)) {
        return false;
    }

    $filename = array_pop($parts);

    $path = $basepath;
    foreach ($parts as $part) {
        $path .= $part . '/';
    }

    $path .= $filename . '.php';

    if (!file_exists($path)) {
        return false;
    }

    require_once $path;
}

/**
 * autoload any class from the lib/vendor folder
 * @param string $className
 *
 * @return bool|void
 */
function thrive_api_vendor_loader($className)
{
    $namespace = 'Thrive_Api_'; // = thrive
    if (strpos($className, $namespace) !== 0) {
        return false;
    }

    $basedir = rtrim(dirname(__FILE__), '/\\') . '/lib/vendor/';

    return _thrive_api__autoload($basedir, str_replace($namespace, '', $className));
}

/**
 *
 * autoload "internal" auto-responder component classes (located in inc/auto-responder/classes folder)
 * @param string $className
 *
 * @return bool
 */
function thrive_api_classes_loader($className)
{
    $namespace = 'Thrive_List_';
    if (strpos($className, $namespace) !== 0) {
        return false;
    }

    $basedir = rtrim(dirname(__FILE__), '/\\') . '/classes/';

    return _thrive_api__autoload($basedir, str_replace($namespace, '', $className));
}

spl_autoload_register('thrive_api_vendor_loader');
spl_autoload_register('thrive_api_classes_loader');

/**
 * handles all api-related AJAX calls made when editing a Lead Generation element
 */
function tve_api_editor_actions()
{
    $controller = new Thrive_List_Editor_Controller();
    $controller->run();
}

/**
 * AJAX call handler to delete API's logs
 */
function tve_api_delete_log()
{
    $log_id = !empty($_POST['log_id']) ? intval($_POST['log_id']) : null;

    if (empty($log_id)) {
        exit(json_encode(array(
            'status' => 'error',
            'message' => __("Log ID is not valid !", "thrive-visual-editor")
        )));
    }

    global $wpdb;

    $delete_result = $wpdb->delete($wpdb->prefix . 'tcb_api_error_log', array('id' => $log_id), array('%d'));
    if ($delete_result === false) {
        exit(json_encode(array(
            'status' => 'error',
            'message' => sprintf(__("An error occurred: %s", "thrive-visual-editor"), $wpdb->last_error)
        )));
    } else if($delete_result === 0) {
        exit(json_encode(array(
            'status' => 'error',
            'message' => sprintf(__("The log with ID: %s could not be found !", "thrive-visual-editor"), $log_id)
        )));
    }

    exit(json_encode(array(
        'status' => 'success',
        'message' => sprintf(__("API Log with ID: %s has been deleted with success !", "thrive-visual-editor"), $log_id)
    )));
}

/**
 * AJAX call handler for API logs that are being retried
 * If the subscription is made with success the log is deleted from db
 */
function tve_api_form_retry()
{

    $connection_name = !empty($_POST['connection_name']) ? $_POST['connection_name'] : null;
    $list_id = !empty($_POST['list_id']) ? $_POST['list_id'] : null;
    $email = !empty($_POST['email']) ? $_POST['email'] : null;
    $name = !empty($_POST['name']) ? $_POST['name'] : '';
    $phone = !empty($_POST['phone']) ? $_POST['phone'] : '';
    $log_id = !empty($_POST['log_id']) ? intval($_POST['log_id']) : null;

    if (empty($connection_name)) {
        exit(json_encode(array(
            'status' => 'error',
            'message' => __('Connection is not specified !', "thrive-visual-editor")
        )));
    }

    if (empty($list_id)) {
        exit(json_encode(array(
            'status' => 'error',
            'message' => __('Where should I subscribe this user? List is not specified !', "thrive-visual-editor")
        )));
    }

    if (empty($email)) {
        exit(json_encode(array(
            'status' => 'error',
            'message' => __('Email is not specified !', "thrive-visual-editor")
        )));
    }

    $data = array(
        'email' => $email,
        'name' => $name,
        'phone' => $phone
    );

    $response = tve_api_add_subscriber($connection_name, $list_id, $data, false);

    if($response !== true) {
        exit(json_encode(array(
            'status' => 'error',
            'message' => $response
        )));
    }

    if (!empty($log_id)) {

        global $wpdb;

        $delete_result = $wpdb->delete($wpdb->prefix . 'tcb_api_error_log', array('id' => $log_id), array('%d'));

        if ($delete_result === false) {
            exit(json_encode(array(
                'status' => 'error',
                'message' => __("Subscription was made with success but we could not delete the log from database !", "thrive-visual-editor")
            )));
        }
    }

    exit(json_encode(array(
        'status' => 'success',
        'message' => __('Subscription was made with success !', 'thrive-visual-editor')
    )));
}

/**
 * AJAX call on a Lead Generation form that's connected to an api
 */
function tve_api_form_submit()
{
    $data = $_POST;

    if (empty($data['email'])) {
        exit(json_encode(array(
            'error' => __('The email address is required', 'thrive-visual-editor'),
        )));
    }

    if (!is_email($data['email'])) {
        exit(json_encode(array(
            'error' => __('The email address is invalid', 'thrive-visual-editor'),
        )));
    }

    $post = $data;
    unset($post['action'], $post['__tcb_lg_fc'], $post['_back_url']);

    /**
     * action filter -  allows hooking into the form submission event
     *
     * @param array $post the full _POST data
     *
     */
    do_action('tcb_api_form_submit', $post);

    if (empty($data['__tcb_lg_fc']) || !($connections = Thrive_List_Manager::decodeConnectionString($data['__tcb_lg_fc']))) {
        exit(json_encode(array(
            'error' => __('No connection for this form', 'thrive-visual-editor'),
        )));
    }

    //these are not needed anymore
    unset($data['__tcb_lg_fc'], $data['_back_url'], $data['action']);

    $result = array();
    $data['name'] = !empty($data['name']) ? $data['name'] : '';
    $data['phone'] = !empty($data['phone']) ? $data['phone'] : '';

    /**
     * filter - allows modifying the data before submitting it to the API
     *
     * @param array $data
     */
    $data = apply_filters('tcb_api_subscribe_data', $data);

    $available = Thrive_List_Manager::getAvailableAPIs(true);
    foreach ($available as $key => $connection) {
        if (!isset($connections[$key])) {
            continue;
        }
        // Not sure how we can perform validations / mark errors here
        $result[$key] = tve_api_add_subscriber($connection, $connections[$key], $data);
    }

    /**
     * $result will contain boolean 'true' or string error messages for each connected api
     * these error messages will literally have no meaning for the user - we'll just store them in a db table and show them in admin somewhere
     */
    echo json_encode($result);
    die;
}

/**
 * make an api call to a subscribe a user
 *
 * @param string|Thrive_List_Connection_Abstract $connection
 * @param mixed $list_identifier the list identifier
 * @param array $data submitted data
 * @param bool $log_error whether or not to log errors in a DB table
 */
function tve_api_add_subscriber($connection, $list_identifier, $data, $log_error = true)
{

    if (is_string($connection)) {
        $connection = Thrive_List_Manager::connectionInstance($connection);
    }

    /**
     * filter - allows modifying the sent data to each individual API instance
     *
     * @param array $data data to be sent to the API instance
     * @param Thrive_List_Connection_Abstract $connection the connection instance
     * @param mixed $list_identifier identifier for the list which will receive the new email
     */
    $data = apply_filters('tcb_api_subscribe_data_instance', $data, $connection, $list_identifier);

    /** @var Thrive_List_Connection_Abstract $connection */
    $result = $connection->addSubscriber($list_identifier, $data);

    if (!$log_error || true === $result) {
        return $result;
    }

    global $wpdb;

    /**
     * at this point, we need to log the error in a DB table, so that the user can see all these error later on and (maybe) re-subscribe the user
     */
    $log_data = array(
        'date' => date('Y-m-d H:i:s'),
        'error_message' => $result,
        'api_data' => serialize($data),
        'connection' => $connection->getKey(),
        'list_id' => maybe_serialize($list_identifier)
    );

    $wpdb->insert($wpdb->prefix . 'tcb_api_error_log', $log_data);

    return $result;

}