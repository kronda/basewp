<?php
/**
 * Created by PhpStorm.
 * User: radu
 * Date: 02.04.2015
 * Time: 14:10
 */

class Thrive_List_Manager
{
    public static $ADMIN_HAS_ERROR = false;

    public static $AVAILABLE = array(
        'mailchimp' => 'Thrive_List_Connection_Mailchimp',
        'aweber' => 'Thrive_List_Connection_AWeber',
        'get-response' => 'Thrive_List_Connection_GetResponse',
        'mailpoet' => 'Thrive_List_Connection_MailPoet',
        'wordpress' => 'Thrive_List_Connection_Wordpress',
        'gotowebinar' => 'Thrive_List_Connection_GoToWebinar',
        'ontraport' => 'Thrive_List_Connection_Ontraport',
        'icontact' => 'Thrive_List_Connection_iContact',
        'activecampaign' => 'Thrive_List_Connection_ActiveCampaign',
        'infusionsoft' => 'Thrive_List_Connection_Infusionsoft',
    );

    /**
     * get a list of all available APIs
     *
     * @param bool $onlyConnected if true, it will return only APIs that are already connected
     * @return Thrive_List_Connection_Abstract[]
     */
    public static function getAvailableAPIs($onlyConnected = false)
    {
        $lists = array();

        $credentials = self::credentials();
        foreach (self::$AVAILABLE as $key => $class) {
            if ($onlyConnected && empty($credentials[$key])) {
                continue;
            }
            $lists[$key] = self::connectionInstance($key, isset($credentials[$key]) ? $credentials[$key] : array());
        }

        return $lists;
    }

    /**
     * fetch the connection credentials for a specific connection (or for all at once)
     *
     * @param string $key if empty, all will be returned
     *
     * @return array
     */
    public static function credentials($key = '')
    {
        $details = get_option('thrive_mail_list_api', array());

        if (empty($key)) {
            return $details;
        }

        if (!isset($details[$key])) {
            return array();
        }

        return $details[$key];
    }

    /**
     * save the credentials for an instance
     *
     * @param Thrive_List_Connection_Abstract $instance
     */
    public static function save($instance)
    {
        $existing = self::credentials();
        $existing[$instance->getKey()] = $instance->getCredentials();

        update_option('thrive_mail_list_api', $existing);
    }

    /**
     *
     * factory method for a connection instance
     *
     * @param string $key
     * @param bool|array $savedCredentials
     *
     * @return Thrive_List_Connection_Abstract
     */
    public static function connectionInstance($key, $savedCredentials = false)
    {
        /** @var Thrive_List_Connection_Abstract $instance */
        $instance = new self::$AVAILABLE[$key]($key);

        if (false !== $savedCredentials) {
            $instance->setCredentials($savedCredentials);
        } else {
            $instance->setCredentials(self::credentials($key));
        }

        return $instance;
    }

    /**
     * saves a message to be displayed on the next request
     *
     * @param string $type
     * @param string $message
     */
    public static function message($type, $message)
    {
        if ($type == 'error') {
            self::$ADMIN_HAS_ERROR = true;
        }
        $messages = get_option('tve_api_admin_notices', array());
        $messages[$type] = $message;

        update_option('tve_api_admin_notices', $messages);
    }

    /**
     * reads out all messages (success / error from the options table)
     */
    public static function flashMessages()
    {
        $GLOBALS['thrive_list_api_message'] = get_option('tve_api_admin_notices', array());

        delete_option('tve_api_admin_notices');
    }

    /**
     * transform the array of connections into one input string
     * this is a really simple encrypt system, is there any need for a more complicated one ?
     *
     * @param array $connections a list of key => value pairs (api => list)
     *
     * @return string
     */
    public static function encodeConnectionString($connections = array())
    {
        return base64_encode(serialize($connections));
//        return htmlspecialchars(serialize($connections));
    }

    /**
     * transform the $string into an array of connections
     *
     * @param string $string
     *
     * @return array
     */
    public static function decodeConnectionString($string)
    {
//        $str = @unserialize(stripslashes(htmlspecialchars_decode($string)));
//        if (empty($str)) {
//            return array();
//        }
//        return $str;
        if (empty($string)) {
            return array();
        }
        $string = @base64_decode($string);
        if (empty($string)) {
            return array();
        }
        $data = @unserialize($string);

        return empty($data) ? array() : $data;
    }
} 