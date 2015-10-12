<?php

/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 29-Jul-15
 * Time: 10:58
 */
class Thrive_List_Connection_KlickTipp extends Thrive_List_Connection_Abstract
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
        return 'KlickTipp';
    }

    /**
     * output the setup form html
     *
     * @return void
     */
    public function outputSetupForm()
    {
        $this->_directFormHtml('klicktipp');
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
        $user = isset($_POST['kt_user']) ? $_POST['kt_user'] : '';
        $password = isset($_POST['kt_password']) ? $_POST['kt_password'] : '';

        if (empty($user) || empty($password)) {
            return $this->error(__('Email and password are required', 'thrive-cb'));
        }

        $this->setCredentials(array(
            'user' => $user,
            'password' => $password
        ));

        /** @var Thrive_Api_KlickTipp $api */
        $api = $this->getApi();

        try {
            $api->login();

            $result = $this->testConnection();

            if ($result !== true) {
                return $this->error(sprintf(__('Could not connect to Klick Tipp using the provided data: %s', 'thrive-cb'), $this->_error));
            }

            /**
             * finally, save the connection details
             */
            $this->save();
            $this->success(__('Klick Tipp connected successfully!', 'thrive-cb'));

        } catch (Thrive_Api_KlickTipp_Exception $e) {
            return $this->error(sprintf(__('Could not connect to Klick Tipp using the provided data (%s)', 'thrive-cb'), $e->getMessage()));
        }
    }

    /**
     * test if a connection can be made to the service using the stored credentials
     *
     * @return bool|string true for success or error message for failure
     */
    public function testConnection()
    {
        return is_array($this->_getLists());
    }

    /**
     * instantiate the API code required for this connection
     *
     * @return mixed
     */
    protected function _apiInstance()
    {
        return new Thrive_Api_KlickTipp($this->param('user'), $this->param('password'));
    }

    /**
     * get all Subscriber Lists from this API service
     *
     * @return array|bool for error
     */
    protected function _getLists()
    {
        /** @var Thrive_Api_KlickTipp $api */
        $api = $this->getApi();

        try {
            $api->login();
        } catch (Thrive_Api_KlickTipp_Exception $e) {
            return $this->error(sprintf(__('Could not connect to Klick Tipp using the provided data (%s)', 'thrive-cb'), $e->getMessage()));
        }

        try {
            $all = $api->getLists();

            $lists = array();
            foreach ($all as $id => $name) {

                $lists[] = array(
                    'id' => $id,
                    'name' => $name
                );
            }

            return $lists;
        } catch (Thrive_Api_KlickTipp_Exception $e) {
            $this->_error = $e->getMessage();
            return false;
        }
    }

    /**
     * Subscribe an email. Requires to be logged in.
     *
     * @param mixed $list_identifier The id subscription process.
     * @param mixed $arguments (optional) Additional fields of the subscriber.
     *
     * @return An object representing the Klicktipp subscriber object.
     */
    public function addSubscriber($list_identifier, $arguments)
    {
        /** @var Thrive_Api_KlickTipp $api */
        $api = $this->getApi();

        try {
            $api->login();
        } catch (Thrive_Api_KlickTipp_Exception $e) {
            return $this->error(sprintf(__('Could not connect to Klick Tipp using the provided data (%s)', 'thrive-cb'), $e->getMessage()));
        }

        /**
         * not sure if this is ok
         */
        $arguments['tagid'] = isset($arguments['tagid']) ? $arguments['tagid'] : 0;

        list($first_name, $last_name) = $this->_getNameParts($arguments['name']);

        if (empty($first_name)) {
            $first_name = " ";
        }

        if (empty($last_name)) {
            $last_name = " ";
        }

        try {
            $api->subscribe(
                $arguments['email'],
                $list_identifier,
                $arguments['tagid'],
                array(
                    'fieldFirstName' => $first_name,
                    'fieldLastName' => $last_name
                )
            );
            $api->logout();
            return true;
        } catch (Thrive_Api_KlickTipp_Exception $e) {
            return $e->getMessage();
        }
    }
}
