<?php
/**
 * Created by PhpStorm.
 * User: radu
 * Date: 29.04.2015
 * Time: 13:04
 */

/**
 * handles all AJAX calls related to rendering the lightbox contents for the lead generation element
 *
 * Class Thrive_List_Editor_Controller
 */
class Thrive_List_Editor_Controller
{
    protected $_viewPath;

    public function __construct()
    {
        $this->_viewPath = plugin_dir_path(dirname(dirname(__FILE__))) . 'views/editor/';
    }

    /**
     * get a request parameter
     *
     * @param string $key
     * @param null $default
     *
     * @return mixed
     */
    protected function _param($key, $default = null)
    {
        return isset($_POST[$key]) ? $_POST[$key] :
            (isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default);
    }

    /**
     * render a view with $data
     *
     * @param string $file
     * @param mixed $data
     *
     * @return string the rendered content
     */
    protected function _view($file, $data = array())
    {
        $file = str_replace('..', '', $file);
        if (strpos($file, '.php') === false) {
            $file .= '.php';
        }

        $path = $this->_viewPath . $file;
        if (!file_exists($path)) {
            return '';
        }

        ob_start();
        extract($data);
        include $path;
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    /**
     * process an AJAX request
     */
    public function run()
    {
        $route = $this->_param('route');

        $method = $route . 'Action';

        $response = array();
        if (method_exists($this, $method)) {
            $response = $this->{$method}();
        }

        wp_send_json($response);
    }

    /**
     * dashboard route / action
     */
    public function dashboardAction()
    {
        $response = array();

        switch ($this->_param('connection_type')) {
            case 'custom-html':
                $content = $this->_view('dashboard-custom-html');
                break;
            case 'api':
                /**
                 * if not connection is setup - return the default dashboard
                 */
                $connection_config = $this->_param('connections');
                /**
                 * try also decoding it from the hidden input element
                 */
                if (empty($connection_config)) {
                    $connection_config = Thrive_List_Manager::decodeConnectionString($this->_param('connections_str'));
                }
                if (empty($connection_config)) {
                    $content = $this->_view('dashboard');
                    break;
                }
                $renderer = new Thrive_Api_Html_Renderer();
                $helper = new Thrive_Api_CustomHtml();
                $data = array(
                    'fields_table' => $renderer->apiFieldsTable(array(
                        'show_display_options' => true,
                        'show_order' => true
                    ), $this->_param('api_fields_order', array())),
                    'connection_config' => $connection_config,
                    'show_submit_options' => apply_filters('tve_autoresponder_show_submit', true)
                );
                $content = $this->_view('dashboard-api', $data);

                $response['stripped_code'] = '';
                $response['elements'] = $this->_param('api_fields') ? $this->_param('api_fields') : $renderer->getApiFields();
                $response['element_order'] = array_keys($renderer->getOrderedFields($this->_param('api_fields_order'), array()));
                $response['form_action'] = '#';
                $response['additional_fields'] = $helper->prepareFilterHook();
                $response['form_method'] = 'post';
                $response['not_visible_inputs'] = '';
                $response['hidden_inputs'] = ''; // TODO: maybe hold here API connections ?
                $response['connections'] = $connection_config;
                /**
                 * adding API connections as one hidden input element - encrypted with a key (to see how we can improve this)
                 */
                $response['elements']['__tcb_lg_fc'] = array(
                    'type' => 'hidden',
                    'name' => '__tcb_lg_fc',
                    'value' => Thrive_List_Manager::encodeConnectionString($connection_config)
                );

                break;
            default:
                $content = $this->_view('dashboard');
                break;
        }

        $response['lb_html'] = $content;

        return $response;
    }

    /**
     * generate HTML table containing form fields settings
     */
    public function generateFieldsAction()
    {
        $handler = new Thrive_Api_CustomHtml();

        return $handler->parseHtmlCode($_POST['autoresponder_code']);
    }

    /**
     * add / edit an API connection or the form custom html code
     */
    public function formAction()
    {
        if (!($connection_type = $this->_param('connection_type')) || !in_array($connection_type, array('api', 'custom-html'))) {
            $types = array(
                'api' => __('API', "thrive-visual-editor"),
                'custom-html' => __('HTML Form code', "thrive-visual-editor")
            );

            //allow types to be filters by somewhere else
            $types = apply_filters('tve_autoresponder_connection_types', $types);

            $data['connection_types'] = $types;

            return array(
                'lb_html' => $this->_view('add', $data)
            );
        }
        if ($connection_type == 'custom-html') {
            return array(
                'lb_html' => $this->_view('add-custom-html')
            );
        }

        return array(
            'lb_html' => $this->_view('add-api', $this->_prepareApiData())
        );
    }

    /**
     * return a dropdown element filled in with integrated apis
     */
    public function apiSelectAction()
    {
        $data = $this->_prepareApiData();

        echo $this->_view('partials/api-select', $data) .
            $this->_view('partials/api-lists', $data);

        exit();
    }

    /**
     * get lists from an API and include them in an html select element
     */
    public function apiListsAction()
    {
        $api = $this->_param('api');

        if (!$api || !array_key_exists($api, Thrive_List_Manager::$AVAILABLE)) {
            exit();
        }
        $connection = Thrive_List_Manager::connectionInstance($api);

        echo $this->_view('partials/api-lists', array(
            'selected_api' => $connection,
            'connection' => $connection,
            'lists' => $connection->getLists($this->_param('force_fetch') ? false : true)
        ));

        exit();
    }

    /**
     * prepare data for the connections / lists views
     */
    protected function _prepareApiData()
    {
        /**
         * list of all connected APIs (that have been setup from admin)
         */
        $connected_apis = Thrive_List_Manager::getAvailableAPIs(true);
        /**
         * existing setup connections for this form
         */
        $connections = $this->_param('connections');
        /**
         * empty for add new, connection key for edit
         */
        $edit_api_key = $this->_param('edit');

        /**
         * if we are editing an api connection, we need to remove all other connected apis from the list
         */
        foreach ($connected_apis as $k => $api) {
            if (isset($connections[$k]) && $k != $edit_api_key) {
                unset($connected_apis[$k]);
            }
        }

        $selected_api = !empty($edit_api_key) && isset($connected_apis[$edit_api_key]) ? $connected_apis[$edit_api_key] : null;
        if (empty($selected_api) && !empty($connected_apis)) {
            $selected_api = reset($connected_apis);
        }

        /**
         * the list of lists for the current api
         */
        $lists = array();
        if (!empty($selected_api)) {
            $lists = $selected_api->getLists();
        }
        /**
         * in case of edit, the currently selected list for this connection
         */
        $selected_list = empty($edit_api_key) ? '' : $connections[$edit_api_key];

        /**
         * Any (possible) extra settings for each autoresponder
         */
        $extra_settings = $this->_param('extra', array());

        return compact('connected_apis', 'connections', 'edit_api_key', 'selected_api', 'lists', 'selected_list', 'extra_settings');
    }
} 