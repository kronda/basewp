<?php

require_once dirname(__FILE__) . '/Mailchimp/Exceptions.php';

class Thrive_Api_Mailchimp
{
    public $apikey;
    public $root = 'https://api.mailchimp.com/2.0';
    public $debug = false;

    public static $error_map = array(
        "ValidationError" => "Thrive_Api_Mailchimp_ValidationError",
        "ServerError_MethodUnknown" => "Thrive_Api_Mailchimp_ServerError_MethodUnknown",
        "ServerError_InvalidParameters" => "Thrive_Api_Mailchimp_ServerError_InvalidParameters",
        "Unknown_Exception" => "Thrive_Api_Mailchimp_Unknown_Exception",
        "Request_TimedOut" => "Thrive_Api_Mailchimp_Request_TimedOut",
        "Zend_Uri_Exception" => "Thrive_Api_Mailchimp_Zend_Uri_Exception",
        "PDOException" => "Thrive_Api_Mailchimp_PDOException",
        "Avesta_Db_Exception" => "Thrive_Api_Mailchimp_Avesta_Db_Exception",
        "XML_RPC2_Exception" => "Thrive_Api_Mailchimp_XML_RPC2_Exception",
        "XML_RPC2_FaultException" => "Thrive_Api_Mailchimp_XML_RPC2_FaultException",
        "Too_Many_Connections" => "Thrive_Api_Mailchimp_Too_Many_Connections",
        "Parse_Exception" => "Thrive_Api_Mailchimp_Parse_Exception",
        "User_Unknown" => "Thrive_Api_Mailchimp_User_Unknown",
        "User_Disabled" => "Thrive_Api_Mailchimp_User_Disabled",
        "User_DoesNotExist" => "Thrive_Api_Mailchimp_User_DoesNotExist",
        "User_NotApproved" => "Thrive_Api_Mailchimp_User_NotApproved",
        "Invalid_ApiKey" => "Thrive_Api_Mailchimp_Invalid_ApiKey",
        "User_UnderMaintenance" => "Thrive_Api_Mailchimp_User_UnderMaintenance",
        "Invalid_AppKey" => "Thrive_Api_Mailchimp_Invalid_AppKey",
        "Invalid_IP" => "Thrive_Api_Mailchimp_Invalid_IP",
        "User_DoesExist" => "Thrive_Api_Mailchimp_User_DoesExist",
        "User_InvalidRole" => "Thrive_Api_Mailchimp_User_InvalidRole",
        "User_InvalidAction" => "Thrive_Api_Mailchimp_User_InvalidAction",
        "User_MissingEmail" => "Thrive_Api_Mailchimp_User_MissingEmail",
        "User_CannotSendCampaign" => "Thrive_Api_Mailchimp_User_CannotSendCampaign",
        "User_MissingModuleOutbox" => "Thrive_Api_Mailchimp_User_MissingModuleOutbox",
        "User_ModuleAlreadyPurchased" => "Thrive_Api_Mailchimp_User_ModuleAlreadyPurchased",
        "User_ModuleNotPurchased" => "Thrive_Api_Mailchimp_User_ModuleNotPurchased",
        "User_NotEnoughCredit" => "Thrive_Api_Mailchimp_User_NotEnoughCredit",
        "MC_InvalidPayment" => "Thrive_Api_Mailchimp_MC_InvalidPayment",
        "List_DoesNotExist" => "Thrive_Api_Mailchimp_List_DoesNotExist",
        "List_InvalidInterestFieldType" => "Thrive_Api_Mailchimp_List_InvalidInterestFieldType",
        "List_InvalidOption" => "Thrive_Api_Mailchimp_List_InvalidOption",
        "List_InvalidUnsubMember" => "Thrive_Api_Mailchimp_List_InvalidUnsubMember",
        "List_InvalidBounceMember" => "Thrive_Api_Mailchimp_List_InvalidBounceMember",
        "List_AlreadySubscribed" => "Thrive_Api_Mailchimp_List_AlreadySubscribed",
        "List_NotSubscribed" => "Thrive_Api_Mailchimp_List_NotSubscribed",
        "List_InvalidImport" => "Thrive_Api_Mailchimp_List_InvalidImport",
        "MC_PastedList_Duplicate" => "Thrive_Api_Mailchimp_MC_PastedList_Duplicate",
        "MC_PastedList_InvalidImport" => "Thrive_Api_Mailchimp_MC_PastedList_InvalidImport",
        "Email_AlreadySubscribed" => "Thrive_Api_Mailchimp_Email_AlreadySubscribed",
        "Email_AlreadyUnsubscribed" => "Thrive_Api_Mailchimp_Email_AlreadyUnsubscribed",
        "Email_NotExists" => "Thrive_Api_Mailchimp_Email_NotExists",
        "Email_NotSubscribed" => "Thrive_Api_Mailchimp_Email_NotSubscribed",
        "List_MergeFieldRequired" => "Thrive_Api_Mailchimp_List_MergeFieldRequired",
        "List_CannotRemoveEmailMerge" => "Thrive_Api_Mailchimp_List_CannotRemoveEmailMerge",
        "List_Merge_InvalidMergeID" => "Thrive_Api_Mailchimp_List_Merge_InvalidMergeID",
        "List_TooManyMergeFields" => "Thrive_Api_Mailchimp_List_TooManyMergeFields",
        "List_InvalidMergeField" => "Thrive_Api_Mailchimp_List_InvalidMergeField",
        "List_InvalidInterestGroup" => "Thrive_Api_Mailchimp_List_InvalidInterestGroup",
        "List_TooManyInterestGroups" => "Thrive_Api_Mailchimp_List_TooManyInterestGroups",
        "Campaign_DoesNotExist" => "Thrive_Api_Mailchimp_Campaign_DoesNotExist",
        "Campaign_StatsNotAvailable" => "Thrive_Api_Mailchimp_Campaign_StatsNotAvailable",
        "Campaign_InvalidAbsplit" => "Thrive_Api_Mailchimp_Campaign_InvalidAbsplit",
        "Campaign_InvalidContent" => "Thrive_Api_Mailchimp_Campaign_InvalidContent",
        "Campaign_InvalidOption" => "Thrive_Api_Mailchimp_Campaign_InvalidOption",
        "Campaign_InvalidStatus" => "Thrive_Api_Mailchimp_Campaign_InvalidStatus",
        "Campaign_NotSaved" => "Thrive_Api_Mailchimp_Campaign_NotSaved",
        "Campaign_InvalidSegment" => "Thrive_Api_Mailchimp_Campaign_InvalidSegment",
        "Campaign_InvalidRss" => "Thrive_Api_Mailchimp_Campaign_InvalidRss",
        "Campaign_InvalidAuto" => "Thrive_Api_Mailchimp_Campaign_InvalidAuto",
        "MC_ContentImport_InvalidArchive" => "Thrive_Api_Mailchimp_MC_ContentImport_InvalidArchive",
        "Campaign_BounceMissing" => "Thrive_Api_Mailchimp_Campaign_BounceMissing",
        "Campaign_InvalidTemplate" => "Thrive_Api_Mailchimp_Campaign_InvalidTemplate",
        "Invalid_EcommOrder" => "Thrive_Api_Mailchimp_Invalid_EcommOrder",
        "Absplit_UnknownError" => "Thrive_Api_Mailchimp_Absplit_UnknownError",
        "Absplit_UnknownSplitTest" => "Thrive_Api_Mailchimp_Absplit_UnknownSplitTest",
        "Absplit_UnknownTestType" => "Thrive_Api_Mailchimp_Absplit_UnknownTestType",
        "Absplit_UnknownWaitUnit" => "Thrive_Api_Mailchimp_Absplit_UnknownWaitUnit",
        "Absplit_UnknownWinnerType" => "Thrive_Api_Mailchimp_Absplit_UnknownWinnerType",
        "Absplit_WinnerNotSelected" => "Thrive_Api_Mailchimp_Absplit_WinnerNotSelected",
        "Invalid_Analytics" => "Thrive_Api_Mailchimp_Invalid_Analytics",
        "Invalid_DateTime" => "Thrive_Api_Mailchimp_Invalid_DateTime",
        "Invalid_Email" => "Thrive_Api_Mailchimp_Invalid_Email",
        "Invalid_SendType" => "Thrive_Api_Mailchimp_Invalid_SendType",
        "Invalid_Template" => "Thrive_Api_Mailchimp_Invalid_Template",
        "Invalid_TrackingOptions" => "Thrive_Api_Mailchimp_Invalid_TrackingOptions",
        "Invalid_Options" => "Thrive_Api_Mailchimp_Invalid_Options",
        "Invalid_Folder" => "Thrive_Api_Mailchimp_Invalid_Folder",
        "Invalid_URL" => "Thrive_Api_Mailchimp_Invalid_URL",
        "Module_Unknown" => "Thrive_Api_Mailchimp_Module_Unknown",
        "MonthlyPlan_Unknown" => "Thrive_Api_Mailchimp_MonthlyPlan_Unknown",
        "Order_TypeUnknown" => "Thrive_Api_Mailchimp_Order_TypeUnknown",
        "Invalid_PagingLimit" => "Thrive_Api_Mailchimp_Invalid_PagingLimit",
        "Invalid_PagingStart" => "Thrive_Api_Mailchimp_Invalid_PagingStart",
        "Max_Size_Reached" => "Thrive_Api_Mailchimp_Max_Size_Reached",
        "MC_SearchException" => "Thrive_Api_Mailchimp_MC_SearchException",
        "Goal_SaveFailed" => "Thrive_Api_Mailchimp_Goal_SaveFailed",
        "Conversation_DoesNotExist" => "Thrive_Api_Mailchimp_Conversation_DoesNotExist",
        "Conversation_ReplySaveFailed" => "Thrive_Api_Mailchimp_Conversation_ReplySaveFailed",
        "File_Not_Found_Exception" => "Thrive_Api_Mailchimp_File_Not_Found_Exception",
        "Folder_Not_Found_Exception" => "Thrive_Api_Mailchimp_Folder_Not_Found_Exception",
        "Folder_Exists_Exception" => "Thrive_Api_Mailchimp_Folder_Exists_Exception"
    );

    public function __construct($apikey = null, $opts = array())
    {
        if (!$apikey) {
            $apikey = getenv('MAILCHIMP_APIKEY');
        }

        if (!$apikey) {
            $apikey = $this->readConfigs();
        }

        if (!$apikey) {
            throw new Thrive_Api_Mailchimp_Error('You must provide a MailChimp API key');
        }

        $this->apikey = $apikey;
        $dc = "us1";

        if (strstr($this->apikey, "-")) {
            list($key, $dc) = explode("-", $this->apikey, 2);
            if (!$dc) {
                $dc = "us1";
            }
        }

        $this->root = str_replace('https://api', 'https://' . $dc . '.api', $this->root);
        $this->root = rtrim($this->root, '/') . '/';

        if (!isset($opts['timeout']) || !is_int($opts['timeout'])) {
            $opts['timeout'] = 600;
        }
        if (isset($opts['debug'])) {
            $this->debug = true;
        }

        $this->folders = new Thrive_Api_Mailchimp_Folders($this);
        $this->templates = new Thrive_Api_Mailchimp_Templates($this);
        $this->users = new Thrive_Api_Mailchimp_Users($this);
        $this->helper = new Thrive_Api_Mailchimp_Helper($this);
        $this->mobile = new Thrive_Api_Mailchimp_Mobile($this);
        $this->conversations = new Thrive_Api_Mailchimp_Conversations($this);
        $this->ecomm = new Thrive_Api_Mailchimp_Ecomm($this);
        $this->neapolitan = new Thrive_Api_Mailchimp_Neapolitan($this);
        $this->lists = new Thrive_Api_Mailchimp_Lists($this);
        $this->campaigns = new Thrive_Api_Mailchimp_Campaigns($this);
        $this->vip = new Thrive_Api_Mailchimp_Vip($this);
        $this->reports = new Thrive_Api_Mailchimp_Reports($this);
        $this->gallery = new Thrive_Api_Mailchimp_Gallery($this);
        $this->goal = new Thrive_Api_Mailchimp_Goal($this);
    }

    public function call($url, $params)
    {
        $params['apikey'] = $this->apikey;

        $params = json_encode($params);

        $info = thrive_api_remote_post($this->root . $url . '.json', array(
            'body' => $params,
            'timeout' => 15,
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'sslverify' => false,
        ));
        if (is_wp_error($info)) {
            /** @var WP_Error $info */
            throw new Thrive_Api_Mailchimp_HttpError("API call to $url failed: " . $info->get_error_message());
        }

        $response_body = $info['body'];

        $result = json_decode($response_body, true);

        if (floor($info['response']['code'] / 100) >= 4) {
            throw $this->castError($result);
        }

        return $result;
    }

    public function readConfigs()
    {
        $paths = array('~/.mailchimp.key', '/etc/mailchimp.key');
        foreach ($paths as $path) {
            if (file_exists($path)) {
                $apikey = trim(file_get_contents($path));
                if ($apikey) {
                    return $apikey;
                }
            }
        }
        return false;
    }

    public function castError($result)
    {
        if ($result['status'] !== 'error' || !$result['name']) {
            throw new Thrive_Api_Mailchimp_Error('We received an unexpected error: ' . json_encode($result));
        }

        $class = (isset(self::$error_map[$result['name']])) ? self::$error_map[$result['name']] : 'Thrive_Api_Mailchimp_Error';
        return new $class($result['error'], $result['code']);
    }

    public function log($msg)
    {
        if ($this->debug) {
            error_log($msg);
        }
    }
}


