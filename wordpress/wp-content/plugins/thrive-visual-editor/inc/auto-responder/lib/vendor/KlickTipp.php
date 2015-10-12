<?php

/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 29-Jul-15
 * Time: 10:58
 */
class Thrive_Api_KlickTipp
{
    private $sessionId;
    private $sessionName;
    private $error;
    private $user;
    private $url;
    private $password;


    /**
     * Thrive_Api_KlickTipp constructor.
     */
    public function __construct($username, $password, $service = 'http://api.klick-tipp.com')
    {
        $this->user = $username;
        $this->password = $password;
        $this->url = $service;
    }


    /**
     * Get all subscription processes (lists) of the logged in user. Requires to be logged in.
     * @throws Thrive_Api_KlickTipp_Exception
     *
     * @return array A associative array <list id> => <list name>
     */
    public function getLists()
    {
        try {
            $this->login();
        } catch (Thrive_Api_KlickTipp_Exception $e) {
            echo('Could not connect to Klick Tipp using the provided data (' . $e->getMessage() . ')');
        }

        $response = $this->_http_request('/list');

        if (empty($response->error) && is_array($response->data)) {
            return $response->data;
        } else {
            $message = 'Could not retrieve lists: ' . $response->data;
            throw new Thrive_Api_KlickTipp_Exception($message);
        }
    }

    /**
     * Subscribe an email. Requires to be logged in.
     *
     * @param mixed $email The email address of the subscriber.
     * @param mixed $listid (optional) The id subscription process.
     * @param mixed $tagid (optional) The id of the manual tag the subscriber will be tagged with.
     * @param mixed $fields (optional) Additional fields of the subscriber.
     * @throws Thrive_Api_KlickTipp_Exception Exception
     *
     * @return mixed An object representing the Klicktipp subscriber object.
     */
    public function subscribe($email, $listid = 0, $tagid = 0, $fields = array())
    {
        if (empty($email)) {
            throw new Thrive_Api_KlickTipp_Exception('Illegal Arguments');
        }

        // subscribe
        $data = array(
            'email' => $email,
        );
        if (!empty($listid)) {
            $data['listid'] = $listid;
        }
        if (!empty($tagid)) {
            $data['tagid'] = $tagid;
        }
        if (!empty($fields)) {
            $data['fields'] = $fields;
        }
        $response = $this->_http_request('/subscriber', 'POST', $data);

        if (empty($response->error)) {
            return !isset($response->data) ? NULL : $response->data;
        } else {
            $message = 'Subscription failed: ' . $response->error;
            throw new Thrive_Api_KlickTipp_Exception($message);
        }
    }

    /**
     * Login
     *
     * @param mixed $username The login name of the user to login.
     * @param mixed $password The password of the user.
     * @throws Thrive_Api_KlickTipp_Exception Exception
     *
     * @return TRUE on success
     */
    public function login()
    {
        if (empty($this->user) || empty($this->password)) {
            throw new Thrive_Api_KlickTipp_Exception('Illegal Arguments');
        }

        // Login
        $data = array(
            'username' => $this->user,
            'password' => $this->password,
        );
        $response = $this->_http_request('/account/login', 'POST', $data, FALSE);

        if (empty($response->error) && isset($response->data) && isset($response->data->sessid)) {
            $this->sessionId = $response->data->sessid;
            $this->sessionName = $response->data->session_name;
            return TRUE;
        } else {
            $message = 'Login failed: ' . (empty($response->data) ? '' : $response->data);
            throw new Thrive_Api_KlickTipp_Exception($message);
        }
    }

    /**
     * Logs out the user currently logged in.
     * @throws Thrive_Api_KlickTipp_Exception Exception
     *
     * @return TRUE on success
     */
    public function logout()
    {
        $response = $this->_http_request('/account/logout', 'POST');

        if (empty($response->error)) {
            return TRUE;
        } else {
            $message = 'Logout failed: ' . $response->error;
            throw new Thrive_Api_KlickTipp_Exception($message);
        }
    }


    /**
     * Helper function.
     * Establishes the system connection to the website.
     * @param $path
     * @param string $method
     * @param null $data
     * @param bool|TRUE $usesession
     * @throws Thrive_Api_KlickTipp_Exception exception
     *
     * @return stdClass
     */
    private function _http_request($path, $method = 'GET', $data = NULL, $usesession = TRUE)
    {

        $args = array();

        // Set session cookie if applicable
        if ($usesession && !empty($this->sessionName)) {
            $args['cookies'] = array(new WP_Http_Cookie(array('name' => $this->sessionName, 'value' => $this->sessionId)));
        }

        //get serialized response
        $args['headers'] = array("Accept" => "application/vnd.php.serialized");

        $url = $this->url . $path;

        //build parameters depending on the send method type
        if ($method == 'GET') {
            $url .= '?' . build_query($data);
            $request = thrive_api_remote_get($url, $args);
        } elseif ($method == 'POST') {
            $args['body'] = $data;
            $request = thrive_api_remote_post($url, $args);
        } else {
            $request = null;
        }

        $result = new stdClass();

        if (is_wp_error($request)) {
            $result->error = $request->get_error_message();
        }

        $result->data = maybe_unserialize(wp_remote_retrieve_body($request));

        return $result;
    }
}