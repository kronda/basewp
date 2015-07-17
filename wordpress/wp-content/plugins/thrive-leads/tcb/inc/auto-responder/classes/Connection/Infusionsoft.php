<?php

/**
 * Created by PhpStorm.
 * User: radu
 * Date: 02.04.2015
 * Time: 15:33
 */
class Thrive_List_Connection_Infusionsoft extends Thrive_List_Connection_Abstract
{
    /**
     * @return string
     */
    public function getTitle()
    {
        return 'Infusionsoft';
    }

    /**
     * output the setup form html
     *
     * @return void
     */
    public function outputSetupForm()
    {
        $this->_directFormHtml('infusionsoft');
    }

    /**
     * just save the key in the database
     *
     * @return mixed|void
     */
    public function readCredentials()
    {
        $client_id = !empty($_POST['connection']['client_id']) ? $_POST['connection']['client_id'] : '';
        $key = !empty($_POST['connection']['api_key']) ? $_POST['connection']['api_key'] : '';

        if (empty($key) || empty($client_id)) {
            return $this->error('You must provide a valid Infusionsoft credentials');
        }

        $this->setCredentials($_POST['connection']);

        $result = $this->testConnection();

        if ($result !== true) {
            return $this->error('Could not connect to Infusionsoft using the provided credentials (<strong>' . $result . '</strong>)');
        }

        /**
         * finally, save the connection details
         */
        $this->save();
        $this->success('Infusionsoft connected successfully');
    }

    /**
     * test if a connection can be made to the service using the stored credentials
     *
     * @return bool|string true for success or error message for failure
     */
    public function testConnection()
    {
        /**
         * just try getting a list as a connection test
         */
        return is_array($this->_getLists());
    }

    /**
     * instantiate the API code required for this connection
     *
     * @return mixed
     */
    protected function _apiInstance()
    {
        return new Thrive_Api_Infusionsoft($this->param('client_id'), $this->param('api_key'));
    }

    /**
     * get all Subscriber Lists from this API service
     *
     * @return array
     */
    protected function _getLists()
    {
        try {
            /** @var Thrive_Api_Infusionsoft $api */
            $api = $this->getApi();

            $queryData = array(
                'GroupName' => '%',
            );
            $selectedFields = array('Id', 'GroupName');
            $response = $api->data('query', 'ContactGroup', 100, 0, $queryData, $selectedFields);

            if (empty($response)) {
                return array();
            }

            $lists = array();

            foreach ($response as $item) {
                $lists[] = array(
                    'id' => $item['Id'],
                    'name' => $item['GroupName'],
                );
            }

            return $lists;

        } catch (Exception $e) {
            return $e->getMessage();
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
        try {

            /** @var Thrive_Api_Infusionsoft $api */
            $api = $this->getApi();

            list($first_name, $last_name) = $this->_getNameParts($arguments['name']);

            $data = array(
                'FirstName' => $first_name,
                'LastName' => $last_name,
                'Email' => $arguments['email'],
                'Phone1' => $arguments['phone']
            );

            $contact_id = $api->contact('addWithDupCheck', $data, 'Email');

            return $api->contact('addToGroup', $contact_id, $list_identifier);

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

} 