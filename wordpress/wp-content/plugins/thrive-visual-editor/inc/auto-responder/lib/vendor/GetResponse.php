<?php

/**
 *
 * modifications to the original class were added to provide a better integration with WP API
 *
 * Thrive_Api_GetResponsePHP is a PHP5 implementation of the Thrive_Api_GetResponse API
 * @internal This wrapper is incomplete and subject to change.
 * @authors Ben Tadiar <ben@bentadiar.co.uk>, Robert Staddon <robert@abundantdesigns.com>
 * @copyright Copyright (c) 2010 Assembly Studios
 * @link http://www.assemblystudios.co.uk
 * @package Thrive_Api_GetResponsePHP
 * @version 0.1.1
 */

/**
 * Thrive_Api_GetResponse Class
 * @package Thrive_Api_GetResponsePHP
 */
class Thrive_Api_GetResponse
{
    /**
     * Thrive_Api_GetResponse API key
     * http://www.getresponse.com/my_api_key.html
     * @var string
     */
    public $apiKey = 'PASS_API_KEY_WHEN_INSTANTIATING_CLASS';

    /**
     * Thrive_Api_GetResponse API URL
     * @var string
     * @access private
     */
    private $apiURL = 'http://api2.getresponse.com';

    /**
     * Text comparison operators used to filter results
     * @var array
     * @access private
     */
    private $textOperators = array('EQUALS', 'NOT_EQUALS', 'CONTAINS', 'NOT_CONTAINS', 'MATCHES');

    /**
     * Check cURL extension is loaded and that an API key has been passed
     * @param string $apiKey Thrive_Api_GetResponse API key
     * @throws Thrive_Api_GetResponse_Exception
     */
    public function __construct($apiKey = null)
    {
        if (is_null($apiKey)) {
            throw new Thrive_Api_GetResponse_Exception('API key must be supplied');
        }
        $this->apiKey = $apiKey;
    }

    /**
     * Test connection to the API, returns "pong" on success
     * @return string
     */
    public function ping()
    {
        $request = $this->prepRequest('ping');
        $response = $this->execute($request);

        return $response->ping;
    }

    /**
     * Get basic user account information
     * @return object
     */
    public function getAccountInfo()
    {
        $request = $this->prepRequest('get_account_info');
        $response = $this->execute($request);
        return $response;
    }

    /**
     * Get list of email addresses assigned to account
     * @return object
     */
    public function getAccountFromFields()
    {
        $request = $this->prepRequest('get_account_from_fields');
        $response = $this->execute($request);
        return $response;
    }

    /**
     * Get single email address assigned to an account using the account From Field ID
     * @param string $id
     * @return object
     */
    public function getAccountFromFieldByID($id)
    {
        $request = $this->prepRequest('get_account_from_field', array('account_from_field' => $id));
        $response = $this->execute($request);
        return $response;
    }

    /**
     * Get single email address assigned to an account using an email address
     * @param string $email
     * @return object
     */
    public function getAccountFromFieldsByEmail($email)
    {
        $request = $this->prepRequest('get_account_from_fields');
        $response = $this->execute($request);
        foreach ($response as $key => $account) if ($account->email != $email) unset($response->$key);
        return $response;
    }

    /**
     * Get a list of active campaigns, optionally filtered
     * @param string $operator Comparison operator
     * @param string $comparison Text/expression to compare against
     * @return object
     */
    public function getCampaigns($operator = 'CONTAINS', $comparison = '%')
    {
        $params = null;
        if (in_array($operator, $this->textOperators)) $params = array('name' => array($operator => $comparison));
        $request = $this->prepRequest('get_campaigns', $params);
        $response = $this->execute($request);
        return $response;
    }

    /**
     * Return a campaign by ID
     * @param string $id Campaign ID
     * @return object
     */
    public function getCampaignByID($id)
    {
        $request = $this->prepRequest('get_campaign', array('campaign' => $id));
        $response = $this->execute($request);
        return $response;
    }

    /**
     * Return a campaign ID by name
     * @param string $name Campaign Name
     * @return string Campaign ID
     */
    public function getCampaignByName($name)
    {
        $request = $this->prepRequest('get_campaigns', array('name' => array('EQUALS' => $name)));
        $response = $this->execute($request);
        return key($response);
    }

    /**
     * Return a list of messages, optionally filtered by multiple conditions
     * @todo Implement all conditions, this is unfinished
     * @param array|null $campaigns Optional argument to narrow results by campaign ID
     * @param string|null $type Optional argument to narrow results by "newsletter", "autoresponder", or "draft"
     * @param string $operator
     * @param string $comparison
     * @return object
     */
    public function getMessages($campaigns = null, $type = null, $operator = 'CONTAINS', $comparison = '%')
    {
        $params = null;
        if (is_array($campaigns)) $params['campaigns'] = $campaigns;
        if (is_string($type)) $params['type'] = $type;
        $request = $this->prepRequest('get_messages', $params);
        $response = $this->execute($request);
        return $response;
    }

    /**
     * Return a message by ID
     * @param string $id Message ID
     * @return object
     */
    public function getMessageByID($id)
    {
        $request = $this->prepRequest('get_message', array('message' => $id));
        $response = $this->execute($request);
        return $response;
    }

    /**
     * Return an autoresponder message from a campaign by Cycle Day
     * @param string $campaign Campaign ID
     * @param string $cycle_day Cycle Day
     * @return object
     */
    public function getMessageByCycleDay($campaign, $cycle_day)
    {
        $params['campaigns'] = array($campaign);
        $params['type'] = "autoresponder";
        $request = $this->prepRequest('get_messages', $params);
        $response = $this->execute($request);
        foreach ($response as $key => $message) if ($message->day_of_cycle != $cycle_day) unset($response->$key);
        return $response;
    }

    /**
     * Return message contents by ID
     * @param string $id Message ID
     * @return object
     */
    public function getMessageContents($id)
    {
        $request = $this->prepRequest('get_message_contents', array('message' => $id));
        $response = $this->execute($request);
        return $response;
    }

    /**
     * Return message statistics
     * @param string $message Message ID
     * @param string $grouping grouping
     * @return object|null
     */
    public function getMessageStats($message, $grouping = "yearly")
    {
        $params['message'] = $message;
        $params['grouping'] = $grouping;
        $request = $this->prepRequest('get_message_stats', $params);
        $response = $this->execute($request);
        return $response;
    }

    /**
     * Return autoresponder message contents by Cycle Day
     * @param string $campaign Campaign ID
     * @param string $cycle_day Cycle Day
     * @return object|null
     */
    public function getMessageContentsByCycleDay($campaign, $cycle_day)
    {
        $params['campaigns'] = array($campaign);
        $params['type'] = "autoresponder";
        $request = $this->prepRequest('get_messages', $params);
        $response = $this->execute($request);
        foreach ($response as $key => $message) if ($message->day_of_cycle == $cycle_day) return $this->getMessageContents($key);
        return null;
    }

    /**
     * Add autoresponder to a campaign at a specific day of cycle
     * @param string $campaign Campaign ID
     * @param string $subject Subject of message
     * @param array $contents Allowed keys are "plain" and "html", at least one is mandatory
     * @param int $cycle_day
     * @param string $from_field From Field ID obtained through getAccountFromFields()
     * @param array $flags Enables extra functionality for a message: "clicktrack", "subscription_reminder", "openrate", "google_analytics"
     * @return object
     */
    public function addAutoresponder($campaign, $subject, $cycle_day, $html = null, $plain = null, $from_field = null, $flags = null)
    {
        $params = array('campaign' => $campaign, 'subject' => $subject, 'day_of_cycle' => $cycle_day);
        if (is_string($html)) $params['contents']['html'] = $html;
        if (is_string($plain)) $params['contents']['plain'] = $plain;
        if (is_string($from_field)) $params['from_field'] = $from_field;
        if (is_array($flags)) $params['flags'] = $flags;
        $request = $this->prepRequest('add_autoresponder', $params);
        $response = $this->execute($request);
        return $response;
    }

    /**
     * Delete an autoresponder
     * @param string $id
     * @return object
     */
    public function deleteAutoresponder($id)
    {
        $request = $this->prepRequest('delete_autoresponder', array('message' => $id));
        $response = $this->execute($request);
        return $response;
    }

    /**
     * Return a list of contacts, optionally filtered by multiple conditions
     * @todo Implement all conditions, this is unfinished
     * @param array|null $campaigns Optional argument to narrow results by campaign ID
     * @param string $operator Optional argument to change operator (default is 'CONTAINS')
     *        See https://github.com/GetResponse/DevZone/tree/master/API#operators for additional operator options
     * @param string $comparison
     * @param array $fields (an associative array, the keys of which should enable/disable comparing name or email)
     * @return object
     */
    public function getContacts($campaigns = null, $operator = 'CONTAINS', $comparison = '%', $fields = array('name' => true, 'email' => false))
    {
        $params = null;
        if (is_array($campaigns)) $params['campaigns'] = $campaigns;
        if ($fields['name']) $params['name'] = $this->prepTextOp($operator, $comparison);
        if ($fields['email']) $params['email'] = $this->prepTextOp($operator, $comparison);
        $request = $this->prepRequest('get_contacts', $params);
        $response = $this->execute($request);
        return $response;
    }

    /**
     * Return a list of contacts by email address (optionally narrowed by campaign)
     * @param string $email Email Address of Contact (or a string contained in the email address)
     * @param array|null $campaigns Optional argument to narrow results by campaign ID
     * @param string $operator Optional argument to change operator (default is 'CONTAINS')
     *        See https://github.com/GetResponse/DevZone/tree/master/API#operators for additional operator options
     * @return object
     */
    public function getContactsByEmail($email, $campaigns = null, $operator = 'CONTAINS')
    {
        $params = null;
        $params['email'] = $this->prepTextOp($operator, $email);
        if (is_array($campaigns)) $params['campaigns'] = $campaigns;
        $request = $this->prepRequest('get_contacts', $params);
        $response = $this->execute($request);
        return $response;
    }

    /**
     * Return a list of contacts filtered by custom contact information
     * $customs is an associative arrays, the keys of which should correspond to the
     * custom field names of the customers you wish to retrieve.
     * @param array|null $campaigns Optional argument to narrow results by campaign ID
     * @param string $operator
     * @param array $customs
     * @param string $comparison
     * @return object
     */
    public function getContactsByCustoms($campaigns = null, $customs, $operator = 'EQUALS')
    {
        $params = null;
        if (is_array($campaigns)) $params['campaigns'] = $campaigns;
        if (!is_array($customs)) trigger_error('Second argument must be an array', E_USER_ERROR);
        foreach ($customs as $key => $val) $params['customs'][] = array('name' => $key, 'content' => $this->prepTextOp($operator, $val));
        $request = $this->prepRequest('get_contacts', $params);
        $response = $this->execute($request);
        return $response;
    }

    /**
     * Return a contact by ID
     * @param string $id User ID
     * @return object
     */
    public function getContactByID($id)
    {
        $request = $this->prepRequest('get_contact', array('contact' => $id));
        $response = $this->execute($request);
        return $response;
    }


    /**
     * Set a contact name
     * @param string $id User ID
     * @return object
     */
    public function setContactName($id, $name)
    {
        $request = $this->prepRequest('set_contact_name', array('contact' => $id, 'name' => $name));
        $response = $this->execute($request);
        return $response;
    }

    /**
     * Set a contact cycle
     * @param string $id User ID
     * @param int $cycle_day Cycle Day
     * @return object
     */
    public function setContactCycle($id, $cycle_day)
    {
        $request = $this->prepRequest('set_contact_cycle', array('contact' => $id, 'cycle_day' => $cycle_day));
        $response = $this->execute($request);
        return $response;
    }

    /**
     * Set a contact campaign
     * @param string $id User ID
     * @param string $campaign Campaign ID
     * @return object
     */
    public function setContactCampaign($id, $campaign)
    {
        $request = $this->prepRequest('move_contact', array('contact' => $id, 'campaign' => $campaign));
        $response = $this->execute($request);
        return $response;
    }

    /**
     * Return a contacts custom information
     * @param string $id User ID
     * @return object
     */
    public function getContactCustoms($id)
    {
        $request = $this->prepRequest('get_contact_customs', array('contact' => $id));
        $response = $this->execute($request);
        return $response;
    }


    /**
     * Set custom contact information
     * $customs is an associative array, the keys of which should correspond to the
     * custom field name you wish to add/modify/remove.
     * Actions: added if not present, updated if present, removed if value is null
     * @todo Implement multivalue customs.
     * @param string $id User ID
     * @param array $customs
     * @return object
     */
    public function setContactCustoms($id, $customs)
    {
        if (!is_array($customs)) trigger_error('Second argument must be an array', E_USER_ERROR);
        foreach ($customs as $key => $val) $params[] = array('name' => $key, 'content' => $val);
        $request = $this->prepRequest('set_contact_customs', array('contact' => $id, 'customs' => $params));
        $response = $this->execute($request);
        return $response;
    }

    /**
     * Return a contacts GeoIP
     * @param string $id User ID
     * @return object
     */
    public function getContactGeoIP($id)
    {
        $request = $this->prepRequest('get_contact_geoip', array('contact' => $id));
        $response = $this->execute($request);
        return $response;
    }

    /**
     * List dates when the messages were opened by contacts
     * @param string $id User ID
     * @return object
     */
    public function getContactOpens($id)
    {
        $request = $this->prepRequest('get_contact_opens', array('contact' => $id));
        $response = $this->execute($request);
        return $response;
    }

    /**
     * List dates when the links in messages were clicked by contacts
     * @param string $id User ID
     * @return object
     */
    public function getContactClicks($id)
    {
        $request = $this->prepRequest('get_contact_clicks', array('contact' => $id));
        $response = $this->execute($request);
        return $response;
    }

    /**
     * Add contact to the specified list (Requires email verification by contact)
     * The return value of this function will be "queued", and on subsequent
     * submission of the same email address will be "duplicated".
     * @param string $campaign Campaign ID
     * @param string $name Name of contact
     * @param string $email Email address of contact
     * @param string $action Standard, insert or update
     * @param int $cycle_day
     * @param array $customs
     * @return object
     */
    public function addContact($campaign, $name, $email, $action = 'standard', $cycle_day = 0, $customs = array())
    {
        $params = array('campaign' => $campaign, 'action' => $action, 'name' => $name,
            'email' => $email, 'cycle_day' => $cycle_day, 'ip' => $_SERVER['REMOTE_ADDR']);
        if (!empty($customs)) {
            foreach ($customs as $key => $val) $c[] = array('name' => $key, 'content' => $val);
            $params['customs'] = $c;
        }
        $request = $this->prepRequest('add_contact', $params);
        $response = $this->execute($request);
        return $response;
    }

    /**
     * Delete a contact
     * @param string $id
     * @return object
     */
    public function deleteContact($id)
    {
        $request = $this->prepRequest('delete_contact', array('contact' => $id));
        $response = $this->execute($request);
        return $response;
    }

    /**
     * Get blacklist masks on account level
     * Account is determined by API key
     * @return object
     */
    public function getAccountBlacklist()
    {
        $request = $this->prepRequest('get_account_blacklist');
        $response = $this->execute($request);
        return $response;
    }

    /**
     * Adds blacklist mask on account level
     * @param string $mask
     * @return object
     */
    public function addAccountBlacklist($mask)
    {
        $request = $this->prepRequest('add_account_blacklist', array('mask' => $mask));
        $response = $this->execute($request);
        return $response;
    }

    /**
     * Delete blacklist mask on account level
     * @param string $mask
     * @return object
     */
    public function deleteAccountBlacklist($mask)
    {
        $request = $this->prepRequest('delete_account_blacklist', array('mask' => $mask));
        $response = $this->execute($request);
        return $response;
    }

    /**
     * Return a key => value array for text comparison
     * @param string $operator
     * @param mixed $comparison
     * @return array
     * @access private
     */
    private function prepTextOp($operator, $comparison)
    {
        if (!in_array($operator, $this->textOperators)) trigger_error('Invalid text operator', E_USER_ERROR);
        if ($operator === 'CONTAINS') $comparison = '%' . $comparison . '%';
        return array($operator => $comparison);
    }

    /**
     * Return array as a JSON encoded string
     * @param string $method API method to call
     * @param array $params Array of parameters
     * @return string JSON encoded string
     * @access private
     */
    private function prepRequest($method, $params = null, $id = null)
    {
        $array = array($this->apiKey);
        if (!is_null($params)) $array[1] = $params;
        $request = json_encode(array('method' => $method, 'params' => $array, 'id' => $id));
        return $request;
    }

    /**
     * Executes an API call
     * @param string $request JSON encoded array
     * @return object
     * @access private
     *
     * @throws Thrive_Api_GetResponse_Exception
     */
    private function execute($request)
    {
        $result = thrive_api_remote_post($this->apiURL, array(
            'headers' => array(
                'Content-type' => 'application/json',
            ),
            'body' => $request
        ));
        if ($result instanceof WP_Error) {
            throw new Thrive_Api_GetResponse_Exception($result->get_error_message());
        }
        $httpCode = $result['response']['code'];
        if (!(($httpCode == '200') || ($httpCode == '204'))) {
            throw new Thrive_Api_GetResponse_Exception('API call failed. Server returned status code ' . $httpCode);
        }
        $response = json_decode($result['body']);
        if ($response->error) {
            throw new Thrive_Api_GetResponse_Exception('Api call failed: ' . $response->error->message);
        }

        if (!$response->error) {
            return $response->result;
        }
    }
}