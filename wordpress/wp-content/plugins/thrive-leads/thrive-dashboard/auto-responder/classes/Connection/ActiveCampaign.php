<?php

/**
 * Created by PhpStorm.
 * User: radu
 * Date: 07.05.2015
 * Time: 18:23
 */
class Thrive_List_Connection_ActiveCampaign extends Thrive_List_Connection_Abstract
{
    /**
     * @return string the API connection title
     */
    public function getTitle()
    {
        return 'ActiveCampaign';
    }

    /**
     * output the setup form html
     *
     * @return void
     */
    public function outputSetupForm()
    {
        $this->_directFormHtml('activecampaign');
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
        $url = !empty($_POST['connection']['api_url']) ? $_POST['connection']['api_url'] : '';
        $key = !empty($_POST['connection']['api_key']) ? $_POST['connection']['api_key'] : '';

        if (empty($key) || empty($url)) {
            return $this->error('Both API URL and API Key fields are required');
        }

        $this->setCredentials($_POST['connection']);

        $result = $this->testConnection();

        if ($result !== true) {
            return $this->error('Could not connect to ActiveCampaign using the provided details. Response was: <strong>' . $result . '</strong>');
        }

        /**
         * finally, save the connection details
         */
        $this->save();
        $this->success('ActiveCampaign connected successfully');
    }

    /**
     * test if a connection can be made to the service using the stored credentials
     *
     * @return bool|string true for success or error message for failure
     */
    public function testConnection()
    {
        /** @var Thrive_Api_ActiveCampaign $api */
        $api = $this->getApi();

        try {
            $raw = $api->getLists();

            return true;
        } catch (Thrive_Api_ActiveCampaign_Exception $e) {
            return $e->getMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * instantiate the API code required for this connection
     *
     * @return mixed
     */
    protected function _apiInstance()
    {
        $api_url = $this->param('api_url');
        $api_key = $this->param('api_key');

        return new Thrive_Api_ActiveCampaign($api_url, $api_key);
    }

    /**
     * get all Subscriber Lists from this API service
     *
     * @return array|bool for error
     */
    protected function _getLists()
    {
        try {
            $raw = $this->getApi()->getLists();
            $lists = array();

            foreach ($raw as $list) {
                $lists []= array(
                    'id' => $list['id'],
                    'name' => $list['name']
                );
            }

            return $lists;

        } catch (Thrive_Api_ActiveCampaign_Exception $e) {

            $this->_error = $e->getMessage();
            return false;

        } catch (Exception $e) {

            $this->_error = $e->getMessage();
            return false;
        }

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
        /** @var Thrive_Api_ActiveCampaign $api */
        $api = $this->getApi();

        try {
            list($first_name, $last_name) = $this->_getNameParts($arguments['name']);

            $api->addSubscriber($list_identifier, $arguments['email'], $first_name, $last_name, empty($arguments['phone']) ? '' : $arguments['phone']);
            return true;

        } catch (Thrive_Api_ActiveCampaign_Exception $e) {
            return $e->getMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

}