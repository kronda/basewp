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
            return $this->error(__('Both API URL and API Key fields are required', 'thrive-cb'));
        }

        $this->setCredentials($_POST['connection']);

        $result = $this->testConnection();

        if ($result !== true) {
            return $this->error(sprintf(__('Could not connect to ActiveCampaign using the provided details. Response was: <strong>%s</strong>', 'thrive-cb'), $result));
        }

        /**
         * finally, save the connection details
         */
        $this->save();
        $this->success(__('ActiveCampaign connected successfully', 'thrive-cb'));
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
                $lists [] = array(
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
     * get all Subscriber Forms from this API service
     *
     * @return array|bool for error
     */
    protected function _getForms()
    {
        try {
            $raw = $this->getApi()->getForms();
            $forms = array();

            $lists = $this->getLists();
            foreach($lists as $list) {
                $forms[$list['id']][0] = array(
                    'id' => 0,
                    'name' => __('none', 'thrive-cb')
                );
            }

            foreach ($raw as $form) {
                foreach ($form['lists'] as $list_id) {
                    if (empty($forms[$list_id])) {
                        $forms[$list_id] = array();
                    }
                    /**
                     * for some reason, I've seen an instance where forms were duplicated (2 or more of the same form were displayed in the list)
                     */
                    $forms[$list_id][$form['id']] = array(
                        'id' => $form['id'],
                        'name' => $form['name']
                    );
                }
            }

            return $forms;

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

            $api->addSubscriber(
                $list_identifier,
                $arguments['email'],
                $first_name,
                $last_name,
                empty($arguments['phone']) ? '' : $arguments['phone'],
                empty($arguments['activecampaign_form']) ? 0 : $arguments['activecampaign_form'],
                '',
                trim($arguments['activecampaign_tags'], ','));
            return true;

        } catch (Thrive_Api_ActiveCampaign_Exception $e) {
            return $e->getMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * output any (possible) extra editor settings for this API
     *
     * @param array $params allow various different calls to this method
     */
    public function renderExtraEditorSettings($params = array())
    {
        $params['forms'] = $this->_getForms();
        if (!is_array($params['forms'])) {
            $params['forms'] = array();
        }

        $this->_directFormHtml('activecampaign/forms-list', $params);
    }

}