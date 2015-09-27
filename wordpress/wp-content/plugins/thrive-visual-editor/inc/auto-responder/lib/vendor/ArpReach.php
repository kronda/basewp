<?php

/**
 * Created by PhpStorm.
 * User: Danut
 * Date: 9/10/2015
 * Time: 5:03 PM
 */

require_once dirname(__FILE__) . "/ArpReach/Exception.php";

class Thrive_Api_ArpReach
{

    protected $url;
    protected $api_key;

    /**
     * Thrive_Api_ArpReach constructor.
     *
     * @param $url string
     * @param $api_key string
     */
    public function __construct($url, $api_key)
    {
        $this->url = $url;
        $this->api_key = $api_key;
    }

    public function testConnection()
    {
        return $this->call_api('ping');
    }

    public function subscribe($list, $user)
    {
        $params = array(
            'email_address' => $user['email'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'phone_number_1' => $user['phone'],
            'lists' => json_encode(array(
                array(
                    'list' => $list,
                    'status' => 1,
                    'next_message' => 2
                )
            ))
        );

        return $this->call_api('add_contact', $params);
    }

    protected function call_api($method, $params = array())
    {
        $url = trim($this->url, "/") . "/a.php/api/" . trim($method, "/");

        $params['api_key'] = $this->api_key;

        $args = array(
            'headers' => array(),
            'timeout' => 45,
            'body' => ($params)
        );

        switch ($method) {
            default:
                $fn = 'thrive_api_remote_post';
                break;
        }

        $request_response = $fn($url, $args);

        if (is_wp_error($request_response)) {
            throw new Thrive_Api_ArpReach_Exception($request_response->get_error_message());
        }

        $api_response = json_decode($request_response['body']);

        if (empty($api_response)) {
            throw new Thrive_Api_ArpReach_Exception('Invalid API call');
        }

        if (!empty($api_response->status) && $api_response->status === 'error') {
            throw new Thrive_Api_ArpReach_Exception($api_response->detail);
        }

        return $api_response;
    }
}
