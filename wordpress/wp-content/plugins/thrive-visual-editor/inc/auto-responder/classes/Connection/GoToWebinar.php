<?php
/**
 * Created by PhpStorm.
 * User: radu
 * Date: 06.05.2015
 * Time: 17:29
 */

class Thrive_List_Connection_GoToWebinar extends Thrive_List_Connection_Abstract
{
    const APPLICATION_CONSUMER_KEY = 'KQ4oUgxUrzFiAbmwaCDoyqrHgRKACzQG';
//    const APPLICATION_CONSUMER_KEY = 'Mtm8i2IdR2mOkAY3uVoW5f4TdGaBxpkY';

    /**
     * check if the expires_at field is in the past
     * GoToWebinar auth access tokens expire after about one year
     *
     * @return bool
     */
    public function isExpired()
    {
        if (!$this->isConnected()) {
            return false;
        }

        $expires_at = $this->param('expires_at');

        return time() > $expires_at;
    }

    /**
     * get the expiry date and time user-friendly formatted
     */
    public function getExpiryDate()
    {
        return date('l, F j, Y H:i:s', $this->param('expires_at'));
    }

    /**
     * @return string the API connection title
     */
    public function getTitle()
    {
        return 'GoToWebinar';
    }

    /**
     * these are called webinars, not lists
     * @return string
     */
    public function getListSubtitle()
    {
        return __('Choose from the following upcoming webinars', 'thrive-cb');
    }


    /**
     * output the setup form html
     *
     * @return void
     */
    public function outputSetupForm()
    {
        $this->_directFormHtml('gotowebinar');
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
        $email = $_POST['gtw_email'];
        $password = $_POST['gtw_password'];

        if (empty($email) || empty($password)) {
            return $this->error(__('Email and password are required', 'thrive-cb'));
        }

        /** @var Thrive_Api_GoToWebinar $api */
        $api = $this->getApi();

        try {
            $api->directLogin($email, $password);

            $this->setCredentials($api->getCredentials());
            /**
             * finally, save the connection details
             */
            $this->save();
            $this->success('GoToWebinar connected successfully');

        } catch (Thrive_Api_GoToWebinar_Exception $e) {
            return $this->error(sprintf(__('Could not connect to GoToWebinar using the provided data (%s)', 'thrive-cb'), $e->getMessage()));
        }

    }

    /**
     * test if a connection can be made to the service using the stored credentials
     *
     * @return bool|string true for success or error message for failure
     */
    public function testConnection()
    {
        // this is not applicable here
    }

    /**
     * instantiate the API code required for this connection
     *
     * @return mixed
     */
    protected function _apiInstance()
    {
        $access_token = $organizer_key = null;
        if ($this->isConnected() && !$this->isExpired()) {
            $access_token = $this->param('access_token');
            $organizer_key = $this->param('organizer_key');
        }

        return new Thrive_Api_GoToWebinar(self::APPLICATION_CONSUMER_KEY, $access_token, $organizer_key);
    }

    /**
     * get all Subscriber Lists from this API service
     *
     * @return array|bool for error
     */
    protected function _getLists()
    {
        /** @var Thrive_Api_GoToWebinar $api */
        $api = $this->getApi();
        $lists = array();

        try {
            $all = $api->getUpcomingWebinars();

            foreach ($all as $item) {

                preg_match('#register/(\d+)$#', $item['registrationUrl'], $m);

                $lists [] = array(
                    'id' => isset($m[1]) ? $m[1] : number_format((float)$item['webinarKey'], 0, "", ""),
                    'name' => $item['subject']
                );
            }

            return $lists;
        } catch (Thrive_Api_GoToWebinar_Exception $e) {
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
        /** @var Thrive_Api_GoToWebinar $api */
        $api = $this->getApi();

        list($first_name, $last_name) = $this->_getNameParts($arguments['name']);

        if(empty($first_name)) {
            $first_name = " ";
        }

        if(empty($last_name)) {
            $last_name = " ";
        }

        try {
            $api->registerToWebinar($list_identifier, $first_name, $last_name, $arguments['email']);
            return true;
        } catch (Thrive_Api_GoToWebinar_Exception $e) {
            return $e->getMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *
     * @return int the number of days in which this token will expire
     */
    public function expiresIn()
    {
        $expires_at = $this->param('expires_at');
        $diff = (int)(($expires_at - time()) / (3600 * 24));

        return $diff;
    }

    /**
     * check if the connection is about to expire in less than 30 days or it's already expired
     */
    public function getWarnings()
    {
        if (!$this->isConnected()) {
            return array();
        }

        $fix = '<a href="' . admin_url('admin.php?page=tve_api_connect&api=' . $this->getKey()) . '#tve-list-setup-' . $this->getKey() . '">' . __('Renew the token', 'thrive-cb') . '</a>';
        $dismiss = '<a href="javascript:void(0)" class="tve-api-dismiss" data-key="' . $this->getKey() . '">' . __('don\'t show this message again', 'thrive-cb') . '</a>';

        if ($this->isExpired()) {

            return array(
                sprintf(__('Thrive API Connections: The access token for %s has expired on %s.', 'thrive-cb'), '<strong>' . $this->getTitle() . '</strong>', '<strong>' .
                    $this->getExpiryDate() . '</strong>') . ' ' . $fix . ' or ' . $dismiss
            );
        }

        $diff = $this->expiresIn();

        if ($diff > 30) {
            return array();
        }

        $message = $diff == 0 ?
            __('Thrive API Connections: The access token for %s will expire today.', 'thrive-cb') :
            ($diff == 1 ?
                __('Thrive API Connections: The access token for %s will expire tomorrow.', 'thrive-cb') :
                __('Thrive API Connections: The access token for %s will expire in %s days.', 'thrive-cb'));

        return array(
            sprintf($message, '<strong>' . $this->getTitle() . '</strong>', '<strong>' . $diff . '</strong>') . ' ' . $fix . ' or ' . $dismiss . '.'
        );
    }

}