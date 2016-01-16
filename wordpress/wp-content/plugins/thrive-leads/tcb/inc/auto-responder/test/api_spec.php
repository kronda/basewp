<?php

require_once '../../../../../../wp-load.php';
require_once '../misc.php';

/**
 *  Postmark Email test
 */
$postmark_key = "b95d97f2-fb54-470b-9672-ad84ee6d0bdc";
$postmark = new Thrive_Api_Postmark($postmark_key);

$sendResult = $postmark->sendEmail(
    "aurelian.pop@bitstone.eu",
    "aurelian.pop@bitstone.eu",
    "Hello from Postmark!",
    "This is just a friendly hello from your friends at Postmark."
);

var_dump($sendResult);

/** Mandrill Email Test */
//$mandrill_key = "vUItKclMpLrIJWuZceOZug";
//$mandrill = new Thrive_Api_Mandrill($mandrill_key);
//$message = array(
//    'html' => '<p>Example HTML content</p>',
//    'text' => 'Example text content',
//    'subject' => 'example subject',
//    'from_email' => 'message.from_email@example.com',
//    'from_name' => 'Example Name',
//    'to' => array(
//        array(
//            'email' => 'storker6@yahoo.com',
//            'name' => 'Recipient Name',
//            'type' => 'to'
//        )
//    ),
//    'headers' => array('Reply-To' => 'message.reply@example.com'),
//    'important' => false,
//    'track_opens' => null,
//    'track_clicks' => null,
//    'auto_text' => null,
//    'auto_html' => null,
//    'inline_css' => null,
//    'url_strip_qs' => null,
//    'preserve_recipients' => null,
//    'view_content_link' => null,
//    'bcc_address' => 'message.bcc_address@example.com',
//    'tracking_domain' => null,
//    'signing_domain' => null,
//    'return_path_domain' => null,
//    'merge' => true,
//    'merge_language' => 'mailchimp',
//    'global_merge_vars' => array(
//        array(
//            'name' => 'merge1',
//            'content' => 'merge1 content'
//        )
//    ),
//    'merge_vars' => array(
//        array(
//            'rcpt' => 'recipient.email@example.com',
//            'vars' => array(
//                array(
//                    'name' => 'merge2',
//                    'content' => 'merge2 content'
//                )
//            )
//        )
//    ),
//    'tags' => array('password-resets'),
//    'subaccount' => 'customer-123',
//    'google_analytics_domains' => array('example.com'),
//    'google_analytics_campaign' => 'message.from_email@example.com',
//    'metadata' => array('website' => 'www.example.com'),
//    'recipient_metadata' => array(
//        array(
//            'rcpt' => 'recipient.email@example.com',
//            'values' => array('user_id' => 123456)
//        )
//    ),
//    'attachments' => array(
//        array(
//            'type' => 'text/plain',
//            'name' => 'myfile.txt',
//            'content' => 'ZXhhbXBsZSBmaWxl'
//        )
//    ),
//    'images' => array(
//        array(
//            'type' => 'image/png',
//            'name' => 'IMAGECID',
//            'content' => 'ZXhhbXBsZSBmaWxl'
//        )
//    )
//);
//$async = false;
//$ip_pool = 'Main Pool';
//$send_at = 'example send_at';
//$result = $mandrill->messages->send($message, $async, $ip_pool);
//
//var_dump($result);



    /**
$mailchimp_key = 'c902447483f66c6cc5b78ffb51f46e79-us4';



$mc = new Thrive_Api_Mailchimp($mailchimp_key, array(
    'debug' => true
));
//var_dump($mc->lists->getList());
var_dump($mc->lists->subscribe('98e94d7dcb', array('email' => 'radu.groza@live.com')));
 *
*/

//$mc = Thrive_List_Manager::connectionInstance('mailchimp');
///** @var Thrive_Api_Mailchimp $api */
//$api = $mc->getApi();
//var_dump($api->lists->mergeVars(array('979e4a2cc0')));
//die;
//var_dump($mc->addSubscriber('979e4a2cc0', array(
//    'name' => 'Radu Groza',
//    'email' => 'radu.groza@bitstone.eu',
//    'phone' => '290834795452'
//)));
//var_Dump($mc->getLists());

//
//
//$consumerKey = 'AkkjPM2epMfahWNUW92Mk2tl';
//$consumerSecret = 'V9bzMop78pXTlPEAo30hxZF7dXYE6T6Ww2LAH95m';
//
//$api = new Thrive_Api_AWeber($consumerKey, $consumerSecret);
//
//$tokens = $api->getRequestToken('http://192.168.1.164/wordpress.release/wp-content/plugins/thrive-leads/inc/auto-responder/test/api_spec.php');
//var_Dump($tokens);
//
//$get_response_key = '2ffba7921374bfe0b70153cbea9d82cc';
//
//$gr = new Thrive_Api_GetResponse($get_response_key);
//var_dump($gr->getCampaigns());

//$aweber = Thrive_List_Manager::connectionInstance('aweber');
//var_dump($aweber->addSubscriber(3868311, array(
//    'name' => 'Radu Groza',
//    'email' => 'radu.groza@live.com',
//    'phone' => '2340985723045'
//)));
//var_dump($aweber->getLists(false));

// iContact
// register app: http://www.icontact.com/developerportal/documentation/register-your-app/
//$iContact = Thrive_Api_IContact::getInstance()->setConfig(array(
//    'appId'       => 'mJpmL1yEVYs9ykGNkx6Wm77zQ21QYjxf',
//    'apiPassword' => 'mJpmL1yEVYs9ykGNkx6Wm77zQ21QYjxf',
//    'apiUsername' => 'Test radu',
//    'companyId' => '1557770',
//    'profileId' => '18802'
//));
//
//
//
//try {
//    var_dump($iContact->getAccountId());
//    var_dump($iContact->getContacts());
//    var_dump($iContact->getLists());
//} catch (Exception $e) {
//    var_dump($iContact->getErrors());
//}
//

//Ontraport

$client = new Thrive_Api_Ontraport('2_21425_1I4AjAUgj', '9L11s2auliQZOGQ');
//echo '<pre>';
//print_r($client->v2Call('1/objects', array('objectID' => 0)));
//die;
//var_dump($client->getSequences());
//var_dump($client->getForms());
//$client->addContact(58, array(
//    'firstname' => 'Radu',
//    'lastname' => 'Groza',
//    'email' => 'radu.groza@bitstone.eu',
//    'phone' => '12333',
//));
//echo '<pre>';
//print_r($client->v2Call('1/objects', array('objectID' => 0, 'performAll' => 'true', 'sort' => 'date', 'sortDir' => 'desc')));
//die;


//$appid = "2_21777_APCLaLhJc";
//$key = "gZc65VqY1Mb9ZOT";
//$reqType= "fetch_sequences";
//$postargs = "appid=".$appid."&key=".$key."&reqType=".$reqType."&data=JSON";
//$request = "http://api.ontraport.com/cdata.php";
//$session = curl_init($request);
//curl_setopt ($session, CURLOPT_POST, true);
//curl_setopt ($session, CURLOPT_POSTFIELDS, $postargs);
//curl_setopt($session, CURLOPT_HEADER, false);
//curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
//$response = curl_exec($session);
//
//header("Content-Type: text/xml");
//echo($response);
//curl_close($session);

// GoToWebinar

//$apiKey = 'KQ4oUgxUrzFiAbmwaCDoyqrHgRKACzQG';
//$client = new Thrive_Api_GoToWebinar($apiKey, 'PSE8cZMgmn74worrWM30G5JHlrBC', '200000000000015219');
////$client->directLogin('shane@imimpact.com', 'U73bba32');
////var_dump($client->getCredentials()); die;
////var_Dump($client->getUpcomingWebinars());
//var_dump($client->registerToWebinar('6034462127666876418', 'Radu', 'Groza', 'radu.groza@bitstone.eu'));
//die;

//$client = new Thrive_Api_ActiveCampaign('https://radutest.api-us1.com', 'a39d6bce4cc1581219c34dd2abae0daca504426a933b66ec92b877708d266efc7cf5a49f');
//$client->getLists();
//var_dump($client->addSubscriber(1, 'radu.groza@bitstone.eu', 'Radu', 'Groza', '07235785345'));
//
///** @var Thrive_List_Connection_iContact $iContact */
//$iContact = Thrive_List_Manager::connectionInstance('icontact');
//var_dump($iContact->addSubscriber(8551, array(
//    'name' => 'radu groza',
//    'email' => 'radu.groza@bitstone.eu',
//    'phone' => '129837689'
//)));
//
//var_dump($iContact->getApi()->getLists());