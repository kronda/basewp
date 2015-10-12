<?php

/**
 * Created by PhpStorm.
 * User: Danut
 * Date: 9/15/2015
 * Time: 12:45 PM
 */
class Thrive_List_Connection_Drip extends Thrive_List_Connection_Abstract
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
        return 'Drip';
    }

    /**
     * instantiate the API code required for this connection
     *
     * @return mixed
     */
    protected function _apiInstance()
    {
        return new Thrive_Api_Drip($this->param('token'));
    }

    /**
     * output the setup form html
     *
     * @return void
     */
    public function outputSetupForm()
    {
        $this->_directFormHtml('drip');
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
        $token = !empty($_POST['connection']['token']) ? $_POST['connection']['token'] : '';
        $client_id = !empty($_POST['connection']['client_id']) ? $_POST['connection']['client_id'] : '';

        if (empty($token) || empty($client_id)) {
            return $this->error(__('You must provide a valid Drip token and Client ID', 'thrive-cb'));
        }

        $this->setCredentials($_POST['connection']);

        $result = $this->testConnection();

        if ($result !== true) {
            return $this->error(sprintf(__('Could not connect to Drip using the provided Token and Client ID (<strong>%s</strong>)', 'thrive-cb'), $result));
        }

        /**
         * finally, save the connection details
         */
        $this->save();
        $this->success(__('Drip connected successfully', 'thrive-cb'));
    }

    /**
     * test if a connection can be made to the service using the stored credentials
     *
     * @return bool|string true for success or error message for failure
     */
    public function testConnection()
    {
        try {

            /** @var Thrive_Api_Drip $api */
            $api = $this->getApi();

            $accounts = $api->get_accounts();

            if (empty($accounts) || !is_array($accounts)) {
                return __("Drip connection could not be validated!", 'thrive-cb');
            }

            foreach ($accounts['accounts'] as $account) {
                if ($account['id'] === $this->param('client_id')) {
                    return true;
                }
            }

            return false;

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * get all Subscriber Lists from this API service
     *
     * @return array|bool for error
     */
    protected function _getLists()
    {
        try {
            /** @var Thrive_Api_Drip $api */
            $api = $this->getApi();

            $campaigns = $api->get_campaigns(array(
                'account_id' => $this->param('client_id'),
                'status' => 'all',
            ));

            if (empty($campaigns) || !is_array($campaigns)) {
                $this->_error = __('There is not Campaign in your Drip account to be fetched !');
                return false;
            }

            $lists = array();

            foreach ($campaigns['campaigns'] as $campaign) {
                $lists[] = array(
                    'id' => $campaign['id'],
                    'name' => $campaign['name']
                );
            }

            return $lists;

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
        list($first_name, $last_name) = $this->_getNameParts($arguments['name']);
        $phone = !empty($arguments['phone']) ? $arguments['phone'] : '';

        try {
            /** @var Thrive_Api_Drip $api */
            $api = $this->getApi();

            $user = array(
                'account_id' => $this->param('client_id'),
                'campaign_id' => $list_identifier,
                'email' => $arguments['email'],
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'custom_fields' => array(
                    'thrive_first_name' => $first_name,
                    'thrive_last_name' => $last_name,
                    'thrive_phone' => $phone
                )
            );

            $lead = $api->create_or_update_subscriber($user);
            if (empty($user)) {
                return __("User could not be subscribed");
            }

            $client = array_shift($lead['subscribers']);

            $api->subscribe_subscriber(array(
                'account_id' => $this->param('client_id'),
                'campaign_id' => $list_identifier,
                'email' => $client['email'],
            ));

            $api->record_event(array(
                'account_id' => $this->param('client_id'),
                'action' => 'Submitted a Thrive Leads form',
                'email' => $arguments['email']
            ));

            return true;

        } catch (Thrive_Api_Drip_Exception_Unsubscribed $e) {
            $api->delete_subscriber($user);
            return $this->addSubscriber($list_identifier, $arguments);

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
