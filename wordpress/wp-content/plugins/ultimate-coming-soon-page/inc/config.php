<?php
/**
 * Config
 *
 * @package WordPress
 * @subpackage Ultimate_Coming_Soon_Page
 * @since 0.1
 */

if ( ! class_exists( 'SeedProd_Ultimate_Coming_Soon_Page' ) ) {
    class SeedProd_Ultimate_Coming_Soon_Page extends SeedProd_Framework {

		private $coming_soon_rendered = false;

        /**
         *  Extend the base construct and add plugin specific hooks
         */
        function __construct(){
            $seedprod_comingsoon_options = get_option('seedprod_comingsoon_options');
            parent::__construct();
            add_action( 'wp_ajax_seedprod_comingsoon_refesh_list', array(&$this,'refresh_list'));
            if((isset($seedprod_comingsoon_options['comingsoon_enabled']) && in_array('1',$seedprod_comingsoon_options['comingsoon_enabled'])) || (isset($_GET['cs_preview']) && $_GET['cs_preview'] == 'true')){
                if(function_exists('bp_is_active')){
                    add_action('template_redirect', array(&$this,'render_comingsoon_page'),9);
                }else{
                    add_action('template_redirect', array(&$this,'render_comingsoon_page'));
                }
                add_action( 'admin_bar_menu',array( &$this, 'admin_bar_menu' ), 1000 );
            }
            add_action( 'wp_ajax_seedprod_mailinglist_callback', array(&$this,'ajax_mailinglist_callback') );
            add_action( 'wp_ajax_nopriv_seedprod_mailinglist_callback', array(&$this,'ajax_mailinglist_callback') );
            add_action( 'wp_ajax_seedprod_email_export_delete', array(&$this,'email_export_delete') );
            add_action( 'wp_enqueue_scripts', array(&$this,'add_frontent_scripts') );
            add_action( 'sc_head','wp_enqueue_scripts',1);
            add_filter( 'plugin_action_links', array(&$this,'plugin_action_links'), 10, 2);
            if($seedprod_comingsoon_options['comingsoon_mailinglist'] == 'database'){
                $this->email_database_setup();
            }
        }

        /**
        * Display admin bar when active
        */

        function admin_bar_menu(){
            global $wp_admin_bar;

            /* Add the main siteadmin menu item */
                $wp_admin_bar->add_menu( array(
                    'id'     => 'debug-bar',
                    'href' => admin_url().'options-general.php?page=seedprod_coming_soon',
                    'parent' => 'top-secondary',
                    'title'  => apply_filters( 'debug_bar_title', __('Coming Soon Mode Active', 'ultimate-coming-soon-page') ),
                    'meta'   => array( 'class' => 'ucsp-mode-active' ),
                ) );
        }

        /**
         * Display the coming soon page
         */
        function render_comingsoon_page() {
                $seedprod_comingsoon_options = get_option('seedprod_comingsoon_options');


                // Return if a login page
                if(empty($seedprod_comingsoon_options['disable_default_exclude_terms'])){
                    if(preg_match("/login|admin|dashboard|account/i",$_SERVER['REQUEST_URI']) > 0){
                        return false;
                    }
                }


                // Show feed if feedburner is in use
                if(!empty($seedprod_comingsoon_options['comingsoon_feedburner_address']) && is_feed()){
                    return false;
                }


                if ( !is_user_logged_in() || (isset($_GET['cs_preview']) && $_GET['cs_preview'] == 'true')) {
                    $this->coming_soon_rendered = true;
					$file = plugin_dir_path(__FILE__).'template/template-coming-soon.php';
                    include($file);
                }

        }

        /**
         * Load frontend scripts
         */
        function add_frontent_scripts() {
				if($this->coming_soon_rendered){
	                //wp_enqueue_script( 'modernizr', plugins_url('inc/template/modernizr.js',dirname(__FILE__)), array(),'1.7' );
	                wp_enqueue_script( 'seedprod_coming_soon_script', plugins_url('inc/template/script.js',dirname(__FILE__)), array( 'jquery' ),$this->plugin_version, true );
	                $data = array(
	                    'msgdefault' => __( 'Enter Your Email' , 'ultimate-coming-soon-page'),
	                    'msg500' => __( 'Error :( Please try again.' , 'ultimate-coming-soon-page'),
	                    'msg400' => __( 'Please enter a valid email.' , 'ultimate-coming-soon-page'),
	                    'msg200' => __( "You'll be notified soon!" , 'ultimate-coming-soon-page'),

	                );
	                wp_localize_script( 'seedprod_coming_soon_script', 'seedprod_err_msg', $data );
            	}
        }

        /**
         * Create Database to Store Emails
         */
        function email_database_setup() {
            global $wpdb;
            $tablename = $wpdb->prefix . "seedprod_emails";
            if( $wpdb->get_var("SHOW TABLES LIKE '$tablename'") != $tablename ){
                $sql = "CREATE TABLE `$tablename` (
                    `id` int(10) unsigned NOT NULL auto_increment,
                    `email` varchar(255) NOT NULL,
                    `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
                );";

                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

                dbDelta($sql);
            }

        }


        /**
         *  Callback for mailing list to be displayed in the admin area.
         */
        function refresh_list(){
            if(check_ajax_referer('seedprod_comingsoon_refesh_list')){
                $api_key = $_GET['apikey'];
                delete_transient('seedprod_comingsoon_mailinglist');
                $mailchimp_lists = $this->get_mailchimp_lists($api_key);
                echo json_encode($mailchimp_lists);
                exit();
            }
        }

        /**
         *  Get List from MailChimp
         */
        function get_mailchimp_lists($apikey){
            $mailchimp_lists = unserialize(get_transient('seedprod_comingsoon_mailinglist'));
            if($mailchimp_lists === false){
                require_once 'lib/MCAPI.class.php';
                $seedprod_comingsoon_options = get_option('seedprod_comingsoon_options');
                if(!isset($apikey)){
                    $apikey = $seedprod_comingsoon_options['comingsoon_mailchimp_api_key'];
                }
                $api = new MCAPI($apikey);

                $retval = $api->lists();
                if ($api->errorCode){
                	$mailchimp_lists['false'] = __("Unable to load lists, check your API Key!", 'ultimate-coming-soon-page');
                } else {

                	foreach ($retval['data'] as $list){
                	    $mailchimp_lists[$list['id']] = 'MailChimp - '.$list['name'];
                	}
                	set_transient('seedprod_comingsoon_mailinglist',serialize( $mailchimp_lists ),86400);
                }
            }
            return $mailchimp_lists;
        }

        /**
         *  Display mailing list field in admin
         */
        function callback_mailinglist_field() {
            $options = get_option('seedprod_comingsoon_options');
            $id = 'comingsoon_mailinglist';
            $setting_id = 'seedprod_comingsoon_options';
            //$option_values = $this->get_mailchimp_lists(null);
            $option_values['none'] = 'Do not display an Email SignUp';
            $option_values['feedburner'] = 'FeedBurner';
            //$option_values['database'] = 'Database';
            $ajax_url = html_entity_decode(wp_nonce_url('admin-ajax.php?action=seedprod_comingsoon_refesh_list','seedprod_comingsoon_refesh_list'));
            echo "<select id='$id' class='' name='{$setting_id}[$id]'>";
    	    foreach($option_values as $k=>$v){
    	        echo "<option value='$k' ".($options[$id] == $k ? 'selected' : '').">$v</option>";
    	    }
    	    echo "</select><!--<button id='comingsoon_mailinglist_refresh' type='button' class='button-secondary'>Refresh</button>-->
            <br><small class='description'>More Options like MailChimp, Aweber, Gravity Forms and others in the <a href='http://www.seedprod.com/ultimate-coming-soon-page-vs-coming-soon-pro/?utm_source=plugin&utm_medium=banner&utm_campaign=coming-soon-pro-in-plugin-banner' target='_blank'>Pro Version</a> :)</small>
            <script type='text/javascript'>
            jQuery(document).ready(function($) {
                $('#comingsoon_mailinglist_refresh').click(function() {
                    apikey = $('#comingsoon_mailchimp_api_key').val();
                    $.post('{$ajax_url}&apikey='+apikey, function(data) {
                      lists = $.parseJSON(data);
                      if(lists){
                          $('#comingsoon_mailinglist').html('');
                      }
                      $.each(lists,function(k,v){
                          $('#comingsoon_mailinglist').prepend('<option value=\"'+k+'\">'+v+'</option>');
                      });
                      $('#comingsoon_mailinglist_refresh').html('Lists Refreshed');
                    });
                });
            });
            </script>
            ";
        }

        /**
         * Subscribe User to Mailing List or return an error.
         */
        function ajax_mailinglist_callback() {
            //if ( empty($_POST) || !wp_verify_nonce($_GET['noitfy_nonce'],'seedprod_comingsoon_callback') )
            if(empty($_GET['email']))
            {
               header('HTTP/1.1 403 Forbidden',true,403);
               exit;
            }
            else
            {
                $seedprod_comingsoon_options = get_option('seedprod_comingsoon_options');
                $email = $_GET['email'];
                $errcode = 0;
                // If not email exit and return 400
                if(is_email($email) != $email){
                    die('400');
                }

                // If databse option update db
                if($seedprod_comingsoon_options['comingsoon_mailinglist'] == 'database'){
                    global $wpdb;
                    $tablename = $wpdb->prefix . "seedprod_emails";
                    $values = array(
                        'email' => $email
                    );
                    $format_values = array(
                        '%s'
                    );
                    $sql = "SELECT `email` FROM $tablename WHERE email = %s";
                    $safe_sql = $wpdb->prepare($sql,$email);
                    $select_result =$wpdb->get_var($safe_sql);
                    if($select_result != $email){
                        $insert_result = $wpdb->insert(
                            $tablename,
                            $values,
                            $format_values
                        );
                    }

                    if($insert_result != false){
                        die('200');
                    }
                    exit;
                }

                // if mailchimp option
                require_once 'lib/MCAPI.class.php';
                $seedprod_comingsoon_options = get_option('seedprod_comingsoon_options');
                $apikey = $seedprod_comingsoon_options['comingsoon_mailchimp_api_key'];
                $api = new MCAPI($apikey);
                $listId = $seedprod_comingsoon_options['comingsoon_mailinglist'];

                $retval = $api->listSubscribe( $listId, $email, $merge_vars=NULL,$email_type='html', $double_optin=true);
                if($retval == false){
                    die('400');
                }
                if ($api->errorCode){
                	die('500');
                } else {
                    die('200');
                }
                exit;
            }
        }

        /**
         * Incentive Section explanation Text
         */
        function section_incentive() {
        	echo '<p class="seedprod_section_explanation">'.__('Offer your visitors incentives such as coupons codes, free ebook, free software, etc. in exchange for their email.
        	Just fill out either or both of the fileds below and the information will be displayed after you have succesfully captured their email.
        	', 'ultimate-coming-soon-page').'</p>';
        }

        /**
        * Email Export
        */
        function email_export_delete(){
            if(check_ajax_referer('seedprod_email_export_delete')){
                if($_GET['method'] == 'export'){
                    global $wpdb;
                	$csv_output .= "Email,Created";
                	$csv_output .= "\n";
                    $tablename = $wpdb->prefix . "seedprod_emails";
                    $sql = "SELECT email,created FROM " . $tablename;
                    $results = $wpdb->get_results($wpdb->prepare($sql));

                     foreach ($results as $result) {
                     	$csv_output .= $result->email ."," . $result->created ."\n";
                     }

                     $filename = $file."emails_".date("Y-m-d_H-i",time());
                     header("Content-type: text/plain");
                     header("Content-disposition: attachment; filename=".$filename.".csv");
                     print $csv_output;
                     exit;
                }elseif($_GET['method'] == 'delete'){
                    global $wpdb;
                	$tablename = $wpdb->prefix . "seedprod_emails";
                   	$sql = "TRUNCATE " . $tablename;
                	$result = $wpdb->query($sql);
                	if($result){
                	    echo '200';
                	}
                	exit;
                }
            }else{
                header('HTTP/1.1 403 Forbidden',true,403);
                exit;
            }
        }

        /**
         * Callback Email Export
         */
        function callback_database_field(){
            $ajax_url = html_entity_decode(wp_nonce_url('admin-ajax.php?action=seedprod_email_export_delete','seedprod_email_export_delete'));
            $data = array( 'delete_confirm' => __( 'Are you sure you want to DELETE all emails?' , 'ultimate-coming-soon-page') );
            wp_localize_script( 'seedprod_coming_soon_script', 'seedprod_object', $data );
            echo "<button id='comingsoon_export_emails' type='button' class='button-secondary'>Export</button><button id='comingsoon_delete_emails' type='button' class='button-secondary'>Delete</button>
            <br><small class='description'></small>
            <script type='text/javascript'>
            jQuery(document).ready(function($) {
                $('#comingsoon_export_emails').click(function() {
                    window.location.href = '{$ajax_url}&method=export';
                });
                $('#comingsoon_delete_emails').click(function() {
                    if(confirm(seedprod_object.delete_confirm)){
                        $.get('{$ajax_url}&method=delete', function(data) {
                           $('#comingsoon_delete_emails').html('Emails Deleted').attr('disabled','disabled');
                        });
                    }
                });
            });
            </script>
            ";
        }

        function plugin_action_links($links, $file) {
            $plugin_file = 'ultimate-coming-soon-page/ultimate-coming-soon-page.php';
            if ($file == $plugin_file) {
                $settings_link = '<a href="options-general.php?page=seedprod_coming_soon">Settings</a>';
                array_push($links, $settings_link);
            }
            return $links;
        }



        // End of Class
    }
}

/**
 * Config
 */
$seedprod_comingsoon = new SeedProd_Ultimate_Coming_Soon_Page();
$seedprod_comingsoon->plugin_base_url = plugins_url('',dirname(__FILE__));
$seedprod_comingsoon->plugin_version = '0.1';
$seedprod_comingsoon->plugin_type = 'free';
$seedprod_comingsoon->plugin_short_url = 'http://bit.ly/pPUKHe';
$seedprod_comingsoon->plugin_name = __('Coming Soon', 'ultimate-coming-soon-page');
$seedprod_comingsoon->menu[] = array("type" => "add_options_page",
                         "page_name" => __("Coming Soon", 'ultimate-coming-soon-page'),
                         "menu_name" => __("Coming Soon", 'ultimate-coming-soon-page'),
                         "capability" => "manage_options",
                         "menu_slug" => "seedprod_coming_soon",
                         "callback" => array($seedprod_comingsoon,'option_page'),
                         "icon_url" => plugins_url('framework/seedprod-icon-16x16.png',dirname(__FILE__)),
                        );

/**
 *  Do not replace validate_function. Create unique id and copy menu slug
 * from menu config. Create 'validate_function' if using custom validation.
 */
$seedprod_comingsoon->options[] = array( "type" => "setting",
                "id" => "seedprod_comingsoon_options",
				"menu_slug" => "seedprod_coming_soon"
				);

/**
 * Create unique id,label, create 'desc_callback' if you need custom description, attach
 * to a menu_slug from menu config.
 */
$seedprod_comingsoon->options[] = array( "type" => "section",
                "id" => "seedprod_section_coming_soon",
				"label" => __("Settings", 'ultimate-coming-soon-page'),
				"menu_slug" => "seedprod_coming_soon");


/**
 * Choose type, id, label, attache to a section and setting id.
 * Create 'callback' function if you are creating a custom field.
 * Optional desc, default value, class, option_values, pattern
 * Types image,textbox,select,textarea,radio,checkbox,color,custom
 */
$seedprod_comingsoon->options[] = array( "type" => "checkbox",
                "id" => "comingsoon_enabled",
				"label" => __("Enable", 'ultimate-coming-soon-page'),
				"desc" => sprintf(__("Enable if you want to display a coming soon page to visitors. Users who are logged in will not see the coming soon page, this means you.  <a href='%s/?cs_preview=true'>Preview</a>", 'ultimate-coming-soon-page'),home_url()),
                "option_values" => array('1'=>__('Yes', 'ultimate-coming-soon-page')),
				"section_id" => "seedprod_section_coming_soon",
				"setting_id" => "seedprod_comingsoon_options",
				);
$seedprod_comingsoon->options[] = array( "type" => "image",
                "id" => "comingsoon_image",
				"label" => __("Image", 'ultimate-coming-soon-page'),
				"desc" => __("Upload a logo or teaser image (or) enter the url to your image. <a href='http://demo.seedprod.com/coming-soon-pro/?utm_source=ultimate-coming-soon-page-plugin&utm_medium=link&utm_campaign=Free%20Backgrounds' target='_blank'>Looking for FREE backgrounds?</a>", 'ultimate-coming-soon-page'),
				"section_id" => "seedprod_section_coming_soon",
				"setting_id" => "seedprod_comingsoon_options",
				);
$seedprod_comingsoon->options[] = array( "type" => "textbox",
                "id" => "comingsoon_headline",
				"label" => __("Headline", 'ultimate-coming-soon-page'),
				"desc" => __("Write a headline for your coming soon page. Tip: Avoid using 'Coming Soon'.", 'ultimate-coming-soon-page'),
				"section_id" => "seedprod_section_coming_soon",
				"setting_id" => "seedprod_comingsoon_options",
				);
$seedprod_comingsoon->options[] = array( "type" => "wpeditor",
                "id" => "comingsoon_description",
				"label" => __("Description", 'ultimate-coming-soon-page'),
				"desc" => __("Tell the visitor what to expect from your site.", 'ultimate-coming-soon-page'),
				"class" => "large-text",
				"section_id" => "seedprod_section_coming_soon",
				"setting_id" => "seedprod_comingsoon_options",
				);

$seedprod_comingsoon->options[] = array( "type" => "custom",
                "id" => "comingsoon_mailinglist",
                "label" => __("Mailing List", 'ultimate-coming-soon-page'),
                "callback" => array($seedprod_comingsoon,'callback_mailinglist_field'),
				"section_id" => "seedprod_section_coming_soon",
				"setting_id" => "seedprod_comingsoon_options",
				);

$seedprod_comingsoon->options[] = array( "type" => "textbox",
                "id" => "comingsoon_feedburner_address",
                "label" => __("FeedBurn Address", 'ultimate-coming-soon-page'),
                "desc" => __("<strong>Note:</strong> Enter the part after http://feeds2.feedburner.com/ <a href='http://wordpress.org/extend/plugins/ultimate-coming-soon-page/faq/'' target='_blank'> Learn how</a> to use FeedBurner to collect emails.", 'ultimate-coming-soon-page'),
                "section_id" => "seedprod_section_coming_soon",
                "setting_id" => "seedprod_comingsoon_options",
                );

$seedprod_comingsoon->options[] = array( "type" => "textarea",
                "id" => "comingsoon_headerscripts",
				"label" => __("Header Scripts", 'ultimate-coming-soon-page'),
				"desc" => __("Enter any scripts that need to be outputted in the html head.", 'ultimate-coming-soon-page'),
				"class" => "large-text",
				"section_id" => "seedprod_section_coming_soon",
				"setting_id" => "seedprod_comingsoon_options",
				);

$seedprod_comingsoon->options[] = array( "type" => "textarea",
                "id" => "comingsoon_customhtml",
				"label" => __("Custom HTML", 'ultimate-coming-soon-page'),
				"desc" => __("Enter any custom html or javascript that you want outputted. You can also enter you Google Analytics code.", 'ultimate-coming-soon-page'),
				"class" => "large-text",
				"section_id" => "seedprod_section_coming_soon",
				"setting_id" => "seedprod_comingsoon_options",
				);

$seedprod_comingsoon->options[] = array( "type" => "checkbox",
                "id" => "disable_default_exclude_terms",
                "label" => __("Disable Default Excluded URLs", 'ultimate-coming-soon-page'),
                "desc" => sprintf(__("By default urls with the terms: login, admin, dashboard and account are excluded. Check to disbale. ", 'ultimate-coming-soon-page'),home_url()),
                "option_values" => array('1'=>__('Yes', 'ultimate-coming-soon-page')),
                "section_id" => "seedprod_section_coming_soon",
                "setting_id" => "seedprod_comingsoon_options",
                );


$seedprod_comingsoon->options[] = array( "type" => "section",
                "id" => "seedprod_section_style",
				"label" => __("Style", 'ultimate-coming-soon-page'),
				"menu_slug" => "seedprod_coming_soon");

$seedprod_comingsoon->options[] = array( "type" => "color",
                "id" => "comingsoon_custom_bg_color",
				"label" => __("Background Color", 'ultimate-coming-soon-page'),
				"section_id" => "seedprod_section_style",
				"setting_id" => "seedprod_comingsoon_options",
				"default_value" => "#ffffff",
				);

$seedprod_comingsoon->options[] = array( "type" => "radio",
                "id" => "comingsoon_background_noise_effect",
                "label" => __("Background Noise Effect", 'ultimate-coming-soon-page'),
                "option_values" => array('on'=>__('On', 'ultimate-coming-soon-page'),'off'=>__('Off', 'ultimate-coming-soon-page')),
                "desc" => __("Adds a noise effect when over the selected color.", 'ultimate-coming-soon-page'),
                "default_value" => "on",
                "section_id" => "seedprod_section_style",
                "setting_id" => "seedprod_comingsoon_options",
                );

$seedprod_comingsoon->options[] = array( "type" => "image",
                "id" => "comingsoon_custom_bg_image",
				"label" => __("Background Image", 'ultimate-coming-soon-page'),
				"section_id" => "seedprod_section_style",
				"setting_id" => "seedprod_comingsoon_options",
				"desc" => __('Upload an optional background image (or) enter the url to your image. This will override the color above if set.', 'ultimate-coming-soon-page'),
				);
$seedprod_comingsoon->options[] = array( "type" => "checkbox",
                "id" => "comingsoon_background_strech",
                "label" => __("Stretch Background Image", 'ultimate-coming-soon-page'),
                "desc" => sprintf(__("This will stretch your background image to match any browser size.", 'ultimate-coming-soon-page'),home_url()),
                "option_values" => array('1'=>__('Yes', 'ultimate-coming-soon-page')),
                "section_id" => "seedprod_section_style",
                "setting_id" => "seedprod_comingsoon_options",
                );



$seedprod_comingsoon->options[] = array( "type" => "radio",
                "id" => "comingsoon_font_color",
				"label" => __("Font Color", 'ultimate-coming-soon-page'),
				"option_values" => array('black'=>__('Black', 'ultimate-coming-soon-page'),'gray'=>__('Gray', 'ultimate-coming-soon-page'),'white'=>__('White', 'ultimate-coming-soon-page')),
				"desc" => __("", 'ultimate-coming-soon-page'),
				"default_value" => "black",
				"section_id" => "seedprod_section_style",
				"setting_id" => "seedprod_comingsoon_options",
				);

$seedprod_comingsoon->options[] = array( "type" => "radio",
                "id" => "comingsoon_text_shadow_effect",
                "label" => __("Text Shadow Effect", 'ultimate-coming-soon-page'),
                "option_values" => array('on'=>__('On', 'ultimate-coming-soon-page'),'off'=>__('Off', 'ultimate-coming-soon-page')),
                "desc" => __("", 'ultimate-coming-soon-page'),
                "default_value" => "on",
                "section_id" => "seedprod_section_style",
                "setting_id" => "seedprod_comingsoon_options",
                );

$seedprod_comingsoon->options[] = array( "type" => "select",
                "id" => "comingsoon_headline_font",
				"label" => __("Headline Font", 'ultimate-coming-soon-page'),
				"option_values" => $seedprod_comingsoon->font_field_list(),
				"section_id" => "seedprod_section_style",
				"setting_id" => "seedprod_comingsoon_options",
				"desc" => __('View <a href="http://www.ampsoft.net/webdesign-l/WindowsMacFonts.html">System Fonts</a> - View <a href="http://www.google.com/webfonts">Google Fonts</a>', 'ultimate-coming-soon-page'),
				);
$seedprod_comingsoon->options[] = array( "type" => "select",
                "id" => "comingsoon_body_font",
				"label" => __("Body Font", 'ultimate-coming-soon-page'),
				"option_values" => $seedprod_comingsoon->font_field_list(),
				"default_value" => "_impact",
				"section_id" => "seedprod_section_style",
				"setting_id" => "seedprod_comingsoon_options",
				"desc" => __('View <a href="http://www.ampsoft.net/webdesign-l/WindowsMacFonts.html">System Fonts</a> - View <a href="http://www.google.com/webfonts">Google Fonts</a>', 'ultimate-coming-soon-page'),
				);

$seedprod_comingsoon->options[] = array( "type" => "textarea",
                "id" => "comingsoon_custom_css",
				"label" => __("Custom CSS", 'ultimate-coming-soon-page'),
				"desc" => "",
				"class" => "large-text",
				"section_id" => "seedprod_section_style",
				"setting_id" => "seedprod_comingsoon_options",
				"desc" => __('Need to tweaks the styles? Add your custom CSS here.', 'ultimate-coming-soon-page'),
				);

$seedprod_comingsoon->options[] = array( "type" => "radio",
                "id" => "comingsoon_footer_credit",
				"label" => __("Powered By SeedProd", 'ultimate-coming-soon-page'),
				"option_values" => array('0'=>__('Nope - Got No Love', 'ultimate-coming-soon-page'),'1'=>__('Yep - I Love You Man', 'ultimate-coming-soon-page')),
				"desc" => __("Can we show a <strong>cool stylish</strong> footer credit at the bottom the page.", 'ultimate-coming-soon-page'),
				"default_value" => "0",
				"section_id" => "seedprod_section_style",
				"setting_id" => "seedprod_comingsoon_options",
				);



?>
