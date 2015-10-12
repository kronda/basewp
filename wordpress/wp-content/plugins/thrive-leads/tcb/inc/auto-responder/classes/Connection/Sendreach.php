<?php

/**
 * Created by PhpStorm.
 * User: Danut
 * Date: 7/22/2015
 * Time: 12:55 PM
 */
class Thrive_List_Connection_Sendreach extends Thrive_List_Connection_Abstract
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
     * @return string the API connection title
     */
    public function getTitle()
    {
        return 'SendReach';
    }

    /**
     * output the setup form html
     *
     * @return void
     */
    public function outputSetupForm()
    {
        $this->_directFormHtml('sendreach');
    }

    /**
     * should handle: read data from post / get, test connection and save the details
     *
     * on error, it should register an error message (and redirect?)
     *
     * @return mixed
     */
    public function readCredentials()
    {
        $key = !empty($_POST['connection']['key']) ? $_POST['connection']['key'] : '';
        $secret = !empty($_POST['connection']['secret']) ? $_POST['connection']['secret'] : '';

        if (empty($key) || empty($secret)) {
            return $this->error('You must provide valid credentials!');
        }

        $this->setCredentials($_POST['connection']);

        $result = $this->testConnection();

        if ($result !== true) {
            return $this->error(sprintf(__('Could not connect to SendReach (<strong>%s</strong>)', 'thrive-cb'), $result));
        }

        $this->save();
        $this->success(__('SendReach connection established', 'thrive-cb'));
    }

    /**
     * test if a connection can be made to the service using the stored credentials
     *
     * @return bool|string true for success or error message for failure
     */
    public function testConnection()
    {
        /** @var Thrive_Api_Sendreach $api */
        $api = $this->getApi();

        try {
            $api->getLists();
        } catch (Exception $e) {
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
        return new Thrive_Api_Sendreach($this->param('key'), $this->param('secret'));
    }

    /**
     * get all Subscriber Lists from this API service
     *
     * @return array|bool for error
     */
    protected function _getLists()
    {
        $lists = array();

        try {
            /** @var Thrive_Api_Sendreach $api */
            $api = $this->getApi();
            $api_lists = $api->getLists();
            foreach ($api_lists as $list) {
                $lists[] = array(
                    'id' => $list->id,
                    'name' => $list->list_name
                );
            }
        } catch (Exception $e) {
            $this->_error = $e->getMessage();
            return false;
        }

        return $lists;
    }

    /**
     * add a contact to a list
     *
     * @param mixed $list_identifier
     * @param array $arguments
     * @return mixed
     */
    public function addSubscriber($list_identifier, $arguments)
    {
        list($first_name, $last_name) = $this->_getNameParts($arguments['name']);

        try {
            /** @var Thrive_Api_Sendreach $api */
            $api = $this->getApi();
            $api->addSubscriber($list_identifier, $first_name, $last_name, $arguments['email']);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return true;
    }
}
