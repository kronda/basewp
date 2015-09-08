<?php
/**
 * Created by PhpStorm.
 * User: Danut
 * Date: 4/9/2015
 * Time: 3:14 PM
 */

require_once dirname(__FILE__) . "/Ontraport/Exception.php";

class Thrive_Api_Ontraport
{
    protected $app_url = 'http://api.ontraport.com/cdata.php';

    protected $api_v2 = 'http://api.ontraport.com/';

    protected $app_id;
    protected $key;

    const FORM_OBJECT_ID = 57;
    const SEQUENCE_OBJECT_ID = 5;

    public function __construct($app_id, $key)
    {

        if (empty($app_id) || empty($key)) {
            throw new Thrive_Api_Ontraport_Exception("Invalid API credentials");
        }

        $this->app_id = $app_id;
        $this->key = $key;
    }

    /**
     * Read the sequences list and returns it
     *
     * @return array
     * @throws Thrive_Api_Ontraport_Exception
     * @return array|string
     */
    public function getSequences()
    {
        $data = $this->v2Call('1/objects', array('objectID' => self::SEQUENCE_OBJECT_ID));

        $lists = array();
        foreach ($data as $item) {
            $lists[$item['drip_id']] = $item;
        }

        return $lists;
    }

    /**
     * get forms from the API
     *
     * @return array|mixed
     * @throws Thrive_Api_Ontraport_Exception
     */
    public function getForms()
    {
        return $this->v2Call('1/objects', array('objectID' => self::FORM_OBJECT_ID));
    }

    /**
     * @param string $list_id
     * @param $fields array
     *
     * @throws Thrive_Api_Ontraport_Exception
     * @return true
     */
    public function addContact($list_id, $fields)
    {
        $fields['updateSequence'] = '*/*' . $list_id . '*/*';

        if (isset($fields['phone'])) {
            $fields['cell_phone'] = $fields['phone'];
        }

        $fields['objectID'] = 0;

        $this->v2Call('1/objects', $fields, 'POST');

        return true;
    }

    /**
     * @throws Thrive_Api_Ontraport_Exception
     */
    protected function _parseResponse($response_body)
    {
        return $response_body;
    }

    /**
     * Ontraport has a new, simpler JSON-based API
     *
     * @param string $path api path
     * @param array $param parameters
     * @param string $method http request method
     *
     * @throws Thrive_Api_Ontraport_Exception
     */
    public function v2Call($path, $params = array(), $method = 'GET')
    {
        $url = $this->api_v2 . $path;

        $args = array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'Api-Appid' => $this->app_id,
                'Api-Key' => $this->key
            ),
            'timeout' => 45
        );

        switch ($method) {
            case 'POST':
                $args['body'] = json_encode($params);
                $fn = 'thrive_api_remote_post';
                break;
            case 'GET':
            default:
                $url .= '?';
                foreach ($params as $k => $param) {
                    $url .= $k . '=' . $param . '&';
                }
                $url = rtrim($url, '?& ');
                $fn = 'thrive_api_remote_get';
                break;
        }

        $response = $fn($url, $args);
        if ($response instanceof WP_Error) {
            throw new Thrive_Api_Ontraport_Exception('Failed connecting: ' . $response->get_error_message());
        }

        $status = $response['response']['code'];
        if ($status != 200 && $status != 204) {
            throw new Thrive_Api_Ontraport_Exception('Call failed: ' . (empty($response['body']) ? 'HTTP status code ' . $status : $response['body']));
        }

        $data = @json_decode($response['body'], true);
        if (empty($data) || !isset($data['code'])) {
            throw new Thrive_Api_Ontraport_Exception('Unknown problem with the API request. Response was:' . $response['body']);
        }

        if (!empty($data['code'])) {
            throw new Thrive_Api_Ontraport_Exception('API Error: ' . isset($data['result_message']) ? $data['result_message'] : (int)$data['result_code']);
        }

        return isset($data['data']) ? $data['data'] : $data;
    }

    /**
     * Does the calls to the API
     *
     * @deprecated
     *
     * @param $request_type
     * @param array $data
     * @return array|WP_Error
     * @throws Thrive_Api_Ontraport_Exception
     */
    protected function _call($request_type, $data = array())
    {
        $postargs = "appid=" . $this->app_id . "&key=" . $this->key . "&reqType=" . $request_type;

        if (!empty($data)) {
            $postargs .= "&data=" . $data;
        }

        $response = thrive_api_remote_post($this->app_url, array(
            'body' => $postargs,
            'timeout' => 45
        ));

        if (is_wp_error($response)) {
            throw new Thrive_Api_Ontraport_Exception('Could not parse the response !');
        }

        return $response;
    }

}
