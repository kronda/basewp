<?php

/**
 * Created by PhpStorm.
 * User: radu
 * Date: 07.05.2015
 * Time: 17:05
 */
class Thrive_Api_ActiveCampaign
{
    /**
     * @var string API URL - taken from the activecampaign API tab
     */
    protected $_apiUrl;

    /**
     * @var string API KEY - taken from the activecampaign API tab
     */
    protected $_apiKey;

    /**
     * @var string
     */
    protected $_apiFormat = 'json';

    public function __construct($apiUrl, $apiKey)
    {
        if (empty($apiKey) || empty($apiUrl)) {
            throw new Thrive_Api_ActiveCampaign_Exception('Both API Url and API Key are required');
        }

        $this->_apiKey = $apiKey;
        $this->_apiUrl = rtrim($apiUrl, '/') . '/';
    }

    /**
     * Retrieve all the subscriber lists, including all information associated with each.
     *
     * @see http://www.activecampaign.com/api/example.php?call=list_list
     *
     * @return array
     *
     * @throws Thrive_Api_ActiveCampaign_Exception
     */
    public function getLists()
    {
        $result = $this->call('list_list', array('ids' => 'all', 'full' => 0));

        $lists = array();
        foreach ($result as $index => $data) {
            if (is_numeric($index)) {
                $lists [] = $data;
            }
        }

        return $lists;
    }

    /**
     * Retrieve all the subscriber forms, including all information associated with each.
     *
     * @see http://www.activecampaign.com/api/example.php?call=form_getforms
     *
     * @return array
     *
     * @throws Thrive_Api_ActiveCampaign_Exception
     */
    public function getForms()
    {
        $result = $this->call('form_getforms');

        $forms = array();
        foreach ($result as $index => $data) {
            if (is_numeric($index)) {
                $forms [] = $data;
            }
        }

        return $forms;
    }

    /**
     * subscribe contact to a list
     *
     * @see http://www.activecampaign.com/api/example.php?call=contact_add
     *
     * TODO: maybe grow the list parameters to include all
     *
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     * @param string $phone
     *
     * @throws Thrive_Api_ActiveCampaign_Exception
     */
    public function addSubscriber($list_id, $email, $firstName = '', $lastName = '', $phone = '', $form_id = 0, $organizationName = '', $tags = array(), $ip = null)
    {
        $body = array(
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone,
            'p[' . $list_id . ']' => $list_id,
            'instantresponders[' . $list_id . ']' => 1,
            'status[' . $list_id . ']' => 1
        );
        if (!empty($form_id)) {
            $body['form'] = $form_id;
        }

        if (!empty($organizationName)) {
            $body['orgname'] = $organizationName;
        }
        if (!empty($tags)) {
            if (is_array($tags)) {
                $tags = implode(',', $tags);
            }
            $body['tags'] = $tags;
        }

        if (!empty($ip)) {
            $body['ip4'] = $ip;
        }

        return $this->call('contact_sync', array(), $body, 'POST');
    }

    /**
     * perform a webservice call
     *
     * @see http://www.activecampaign.com/api/overview.php
     *
     * ActiveCampaign requires some of the params be sent by query string and others via POST body
     * by default, api_key, api_action and api_output are sent via query string
     *
     * @param string $apiAction by default, this is sent via query string
     * @param array $bodyParams body request parameters
     * @param string $method GET or POST
     */
    public function call($apiAction, $queryStringParams = array(), $bodyParams = array(), $method = 'GET')
    {
        $queryStringParams['api_key'] = $this->_apiKey;
        $queryStringParams['api_action'] = $apiAction;
        $queryStringParams['api_output'] = $this->_apiFormat;

        $url = $this->_apiUrl . 'admin/api.php?';
        foreach ($queryStringParams as $key => $value) {
            $url .= $key . '=' . $value . '&';
        }

        $url = rtrim($url, '&');

        $args = array();

        switch ($method) {
            case 'POST':
                $args['body'] = $bodyParams;
                $function = 'thrive_api_remote_post';
                break;
            case 'GET':
            default:
                $function = 'thrive_api_remote_get';
                break;
        }

        $response = $function($url, $args);
        if ($response instanceof WP_Error) {
            throw new Thrive_Api_ActiveCampaign_Exception('Failed connecting: ' . $response->get_error_message());
        }

        $body = wp_remote_retrieve_body($response);

        $data = $this->_parseResponse($body);

        if (empty($data)) {
            if (strpos($data, 'g-recaptcha') !== FALSE) {
                throw new Thrive_Api_ActiveCampaign_Exception('Unknown problem with the API request. Please recheck your account.');
            }

            throw new Thrive_Api_ActiveCampaign_Exception('Unknown problem with the API request. Response was:' . $body);
        }

        if (isset($data['result_code']) && empty($data['result_code'])) {
            throw new Thrive_Api_ActiveCampaign_Exception('API Error: ' . isset($data['result_message']) ? $data['result_message'] : (int)$data['result_code']);
        }

        return $data;
    }

    /**
     *
     * parse the response based on $this->_apiFormat field
     * @throws Thrive_Api_ActiveCampaign_Exception
     * @param string $response
     *
     * @return array
     */
    protected function _parseResponse($response)
    {
        $response = trim($response);
        switch ($this->_apiFormat) {
            case 'json':
                $data = @json_decode($response, true);
                break;
            case 'serialize':
                $data = @unserialize($response);
                break;
            case 'xml':
            default:
                throw new Thrive_Api_ActiveCampaign_Exception('api_format not implemented: ' . $this->_apiFormat);
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getApiUrl()
    {
        return $this->_apiUrl;
    }

    /**
     * @param string $apiUrl
     *
     * @return Thrive_Api_ActiveCampaign
     */
    public function setApiUrl($apiUrl)
    {
        $this->_apiUrl = $apiUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->_apiKey;
    }

    /**
     * @param string $apiKey
     *
     * @return Thrive_Api_ActiveCampaign
     */
    public function setApiKey($apiKey)
    {
        $this->_apiKey = $apiKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiFormat()
    {
        return $this->_apiFormat;
    }

    /**
     * @param string $apiFormat
     *
     * @return Thrive_Api_ActiveCampaign
     */
    public function setApiFormat($apiFormat)
    {
        $this->_apiFormat = $apiFormat;
        return $this;
    }


}