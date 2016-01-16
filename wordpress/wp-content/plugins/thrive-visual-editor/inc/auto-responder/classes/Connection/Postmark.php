<?php

/**
 * Created by PhpStorm.
 * User: Aurelian
 * Date: 14/10/2015
 * Time: 4:59 PM
 */
class Thrive_List_Connection_Postmark extends Thrive_List_Connection_Abstract
{
    /**
     * Return the connection type
     * @return String
     */
    public static function getType()
    {
        return 'email';
    }

    /**
     * @return string the API connection title
     */
    public function getTitle()
    {
        return 'Postmark';
    }

    /**
     * output the setup form html
     *
     * @return void
     */
    public function outputSetupForm()
    {
        $this->_directFormHtml('postmark');
    }

    /**
     * should handle: read data from post / get, test connection and save the details
     *
     * on error, it should register an error message (and redirect?)
     */
    public function readCredentials()
    {

        $ajax_call = defined('DOING_AJAX') && DOING_AJAX;

        $key = !empty($_POST['connection']['key']) ? $_POST['connection']['key'] : '';
        $email = !empty($_POST['connection']['email']) ? $_POST['connection']['email'] : '';

        if (empty($key)) {
            return $ajax_call ? __('You must provide a valid Postmark key', 'thrive-cb') : $this->error(__('You must provide a valid Postmark key', 'thrive-cb'));
        }

        if (empty($email)) {
            return $ajax_call ? __('Email field must not be empty', 'thrive-cb') : $this->error(__('Email field must not be empty', 'thrive-cb'));
        }

        $this->setCredentials($_POST['connection']);

        $result = $this->testConnection();


        if ($result !== true) {

            return $ajax_call ? sprintf(__('Could not connect to Postmark using the provided key (<strong>%s</strong>)', 'thrive-cb'), $result) : $this->error(sprintf(__('Could not connect to Postmark using the provided key (<strong>%s</strong>)', 'thrive-cb'), $result));
        }

        /**
         * finally, save the connection details
         */
        $this->save();
        $this->success(__('Postmark connected successfully', 'thrive-cb'));

        if($ajax_call) {
            return true;
        }

    }

    /**
     * test if a connection can be made to the service using the stored credentials
     *
     * @return bool|string true for success or error message for failure
     */
    public function testConnection()
    {
        $postmark = $this->getApi();

        if(isset($_POST['connection']['email'])) {
            $from_email = $_POST['connection']['email'];
            $to = $_POST['connection']['email'];
        } else {
            $credentials = Thrive_List_Manager::credentials('postmark');
            if(isset($credentials)) {
                $from_email = $credentials['email'];
                $to = $credentials['email'];
            }
        }

        $subject = 'API connection test';
        $html_content = 'This is a test email from Thrive Leads Postmark API.';
        $text_content = 'This is a test email from Thrive Leads Postmark API.';
        try {
            $postmark->sendEmail($from_email, $to, $subject, $html_content, $text_content);
        } catch (Thrive_Api_Postmark_Exception $e) {
            return $e->getMessage();
        }

        $connection = get_option('tve_api_delivery_service', false);

        if($connection == false) {
            update_option( 'tve_api_delivery_service', 'postmark');
        }

        return true;

        /**
         * just try getting a list as a connection test
         */
    }

     /**
     * Send the email to the user
     * @param $post_data
     * @return bool|string
     */
    public function sendEmail($post_data)
    {
        $postmark = $this->getApi();
        $credentials = $this->getCredentials();

        $asset = get_post( $post_data['_asset_group'] );
        $files = get_post_meta($post_data['_asset_group'], 'tve_asset_group_files', true);
        $subject = get_post_meta($post_data['_asset_group'], 'tve_asset_group_subject', true);

        if($subject == "") {
            $subject = get_option('tve_leads_asset_mail_subject');
        }
        $from_email = $credentials['email'];
        $html_content = $asset->post_content;

        if($html_content == "") {
            $html_content = get_option('tve_leads_asset_mail_body');
        }

        $attached_files = '';
        foreach($files as $file) {
            $attached_files[] = '<a href="'. $file['link'] .'">'. $file['link_anchor'] .'</a><br/>';
        }

        $the_files = implode('<br/>', $attached_files);

        $html_content = str_replace('[asset_download]', $the_files, $html_content);
        $html_content = str_replace('[asset_name]', $asset->post_title, $html_content);
        $subject = str_replace('[asset_name]', $asset->post_title, $subject);

        if (isset($post_data['name']) && !empty($post_data['name'])) {
            $html_content = str_replace('[lead_name]', $post_data['name'], $html_content);
            $subject = str_replace('[lead_name]', $post_data['name'], $subject);
            $visitor_name = $post_data['name'];
        } else {
            $html_content = str_replace('[lead_name]', '', $html_content);
            $subject = str_replace('[lead_name]', '', $subject);
            $visitor_name = '';
        }

        $text_content = strip_tags($html_content);

        $result = $postmark->sendEmail(
            $from_email,
            $post_data['email'],
            $subject,
            $html_content,
            $text_content
        );

        return $result;
    }

    /**
     * instantiate the API code required for this connection
     *
     * @return mixed
     */
    protected function _apiInstance()
    {
        return new Thrive_Api_Postmark($this->param('key'));
    }

    /**
     * get all Subscriber Lists from this API service
     *
     * @return array|bool for error
     */
    protected function _getLists()
    {

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

    }
}