<?php

/**
 * Created by PhpStorm.
 * User: Danut
 * Date: 7/30/2015
 * Time: 11:47 AM
 */
class Thrive_List_Connection_Sendy extends Thrive_List_Connection_Abstract
{
    /**
     * @return string the API connection title
     */
    public function getTitle()
    {
        return 'Sendy';
    }

    /**
     * output the setup form html
     *
     * @return void
     */
    public function outputSetupForm()
    {
        $this->_directFormHtml('sendy');
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
        $url = !empty($_POST['connection']['url']) ? $_POST['connection']['url'] : '';

        $lists = !empty($_POST['connection']['lists']) ? $_POST['connection']['lists'] : array();
        $lists = array_filter($lists);

        if (empty($url) || empty($lists)) {
            return $this->error('Invalid URL or Lists IDs');
        }

        $_POST['connection']['lists'] = $lists;

        $this->setCredentials($_POST['connection']);

        $result = $this->testConnection();

        if ($result !== true) {
            return $this->error(__('Could not connect to Sendy', 'thrive-cb'));
        }

        $this->save();
        $this->success('Sendy connected successfully');
    }

    /**
     * test if a connection can be made to the service using the stored credentials
     *
     * @return bool|string true for success or error message for failure
     */
    public function testConnection()
    {
        /** @var Thrive_Api_Sendy $api */
        $api = $this->getApi();

        return $api->testUrl();
    }

    /**
     * instantiate the API code required for this connection
     *
     * @return mixed
     */
    protected function _apiInstance()
    {
        return new Thrive_Api_Sendy($this->param('url'));
    }

    /**
     * get all Subscriber Lists from this API service
     *
     * @return array|bool for error
     */
    protected function _getLists()
    {
        $lists = array();

        foreach ($this->param('lists') as $id) {
            $lists[] = array(
                'id' => $id,
                'name' => "#" . $id
            );
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
        /** @var Thrive_Api_Sendy $api */
        $api = new Thrive_Api_Sendy($this->param('url'));

        try {
            $api->subscribe($arguments['email'], $list_identifier, $arguments['name']);
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return false;
    }

}
