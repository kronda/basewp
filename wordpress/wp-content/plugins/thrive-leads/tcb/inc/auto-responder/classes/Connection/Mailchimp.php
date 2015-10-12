<?php

/**
 * Created by PhpStorm.
 * User: radu
 * Date: 02.04.2015
 * Time: 15:33
 */
class Thrive_List_Connection_Mailchimp extends Thrive_List_Connection_Abstract
{
    /**
     * Return the connection type
     * @return String
     */
    public static function getType()
    {
        return 'autoresponder';
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'Mailchimp';
    }

    /**
     * output the setup form html
     *
     * @return void
     */
    public function outputSetupForm()
    {
        $this->_directFormHtml('mailchimp');
    }

    /**
     * just save the key in the database
     *
     * @return mixed|void
     */
    public function readCredentials()
    {
        $key = !empty($_POST['connection']['key']) ? $_POST['connection']['key'] : '';

        if (empty($key)) {
            return $this->error(__('You must provide a valid Mailchimp key', 'thrive-cb'));
        }

        $this->setCredentials($_POST['connection']);

        $result = $this->testConnection();

        if ($result !== true) {
            return $this->error(sprintf(__('Could not connect to Mailchimp using the provided key (<strong>%s</strong>)', 'thrive-cb'), $result));
        }

        /**
         * finally, save the connection details
         */
        $this->save();
        $this->success(__('Mailchimp connected successfully', 'thrive-cb'));
    }

    /**
     * test if a connection can be made to the service using the stored credentials
     *
     * @return bool|string true for success or error message for failure
     */
    public function testConnection()
    {
        $mc = $this->getApi();
        /**
         * just try getting a list as a connection test
         */

        try {
            $mc->lists->getList();
        } catch (Thrive_Api_Mailchimp_Error $e) {
            return $e->getMessage();
        }

        return true;
    }

    /**
     * instantiate the API code required for this connection
     *
     * @return mixed
     */
    protected function _apiInstance()
    {
        return new Thrive_Api_Mailchimp($this->param('key'));
    }

    /**
     * get all Subscriber Lists from this API service
     *
     * @return array
     */
    protected function _getLists()
    {
        /** @var Thrive_Api_Mailchimp $api */
        $api = $this->getApi();

        try {
            $lists = array();

            $raw = $api->lists->getList(array(), 0, 100);
            if (empty($raw['total']) || empty($raw['data'])) {
                return array();
            }
            foreach ($raw['data'] as $item) {
                $lists [] = array(
                    'id' => $item['id'],
                    'name' => $item['name']
                );
            }
            return $lists;
        } catch (Exception $e) {
            $this->_error = $e->getMessage() . ' ' . __("Please re-check your API connection details.", 'thrive-cb');
            return false;
        }
    }

    /**
     * add a contact to a list
     *
     * @param mixed $list_identifier
     * @param array $arguments
     * @return bool|string true for success or string error message for failure
     */
    public function addSubscriber($list_identifier, $arguments)
    {
        list($first_name, $last_name) = $this->_getNameParts($arguments['name']);

        $double_optin = isset($arguments['mailchimp_optin']) && $arguments['mailchimp_optin'] == 's' ? false : true;

        /** @var Thrive_Api_Mailchimp $api */
        $api = $this->getApi();

        $merge_tags = array(
            'FNAME' => $first_name,
            'LNAME' => $last_name,
            'NAME' => $arguments['name']
        );

        if (isset($arguments['phone'])) {
            $merge_vars = $this->getCustomFields($list_identifier);
            foreach ($merge_vars as $item) {
                if ($item['field_type'] == 'phone') {
                    $merge_tags[$item['name']] = $arguments['phone'];
                    $merge_tags[$item['tag']] = $arguments['phone'];
                }
            }
        }

        try {
            $api->lists->subscribe(
                $list_identifier,
                array('email' => $arguments['email']),
                $merge_tags,
                'html',
                $double_optin,
                true
            );
            return true;
        } catch (Thrive_Api_Mailchimp_Error $e) {
            return $e->getMessage() ? $e->getMessage() : __('Unknown Mailchimp Error', 'thrive-cb');
        } catch (Exception $e) {
            return $e->getMessage() ? $e->getMessage() : __('Unknown Error', 'thrive-cb');
        }

    }

    /**
     * Allow the user to choose whether to have a single or a double optin for the form being edited
     * It will hold the latest selected value in a cookie so that the user is presented by default with the same option selected the next time he edits such a form
     *
     * @param array $params
     */
    public function renderExtraEditorSettings($params = array())
    {
        $params['optin'] = empty($params['optin']) ? (isset($_COOKIE['tve_api_mailchimp_optin']) ? $_COOKIE['tve_api_mailchimp_optin'] : 'd') : $params['optin'];
        setcookie('tve_api_mailchimp_optin', $params['optin'], strtotime('+6 months'), '/');
        $this->_directFormHtml('mailchimp/optin-type', $params);
    }

    /**
     * @param $list
     * @return mixed
     */
    public function getCustomFields($list)
    {
        /** @var Thrive_Api_Mailchimp $api */
        $api = $this->getApi();

        $merge_vars = $api->lists->mergeVars(array($list));
        if (!isset($merge_vars['data']) || !isset($merge_vars['data'][0])) {
            return array();
        }

        $list = $merge_vars['data'][0];
        if (!isset($list['merge_vars'])) {
            return array();
        }

        return $list['merge_vars'];
    }

}
