<?php

class Thrive_Clever_Widgets_Manager_Admin
{
    protected $version;

    public function __construct($version)
    {
        $this->version = $version;
    }

    public function checkForPluginUpdates()
    {
        require plugin_dir_path(dirname(__FILE__)) . '/plugin-updates/plugin-update-checker.php';

        new PluginUpdateChecker(
            'http://members.thrivethemes.com/plugin_versions/thrive_clever_widgets/update.json',
            plugin_dir_path(dirname(__FILE__)) . 'thrive-clever-widgets.php',
            'thrive-clever-widgets'
        );
    }

    /**
     * Enqueue assets only for the widgets.php request
     * @param $hook
     */
    public function enqueue_scripts($hook)
    {
        if ($hook != 'widgets.php') {
            return;
        }

        add_thickbox();

        /**
         * specific admin styles
         */
        wp_enqueue_style('thrive-clever-widgets-admin', plugin_dir_url(__FILE__) . 'css/styles.css', array(), $this->version, false);

        /**
         * overwrite thickbox styles
         */
        wp_enqueue_style('thrive-clever-widgets-admin-thickbox', plugin_dir_url(__FILE__) . 'css/thickbox.css', array(), $this->version, false);

        /**
         * tabs styles
         */
        wp_enqueue_style('thrive-clever-widgets-admin-tabs', plugin_dir_url(__FILE__) . 'css/tabs.css', array(), $this->version, false);

        /**
         * backbone models
         */
        wp_enqueue_script('thrive-clever-widgets-option-model', plugin_dir_url(__FILE__) . 'js-min/models/option.js', array('jquery', 'underscore', 'backbone'), $this->version, true);
        wp_enqueue_script('thrive-clever-widgets-tab-model', plugin_dir_url(__FILE__) . 'js-min/models/tab.js', array('jquery', 'underscore', 'backbone'), $this->version, true);
        wp_enqueue_script('thrive-clever-widgets-hanger-model', plugin_dir_url(__FILE__) . 'js-min/models/hanger.js', array('jquery', 'underscore', 'backbone'), $this->version, true);
        wp_enqueue_script('thrive-clever-widgets-filter-model', plugin_dir_url(__FILE__) . 'js-min/models/filter.js', array('jquery', 'underscore', 'backbone'), $this->version, true);
        wp_enqueue_script('thrive-clever-widgets-template-model', plugin_dir_url(__FILE__) . 'js-min/models/template.js', array('jquery', 'underscore', 'backbone'), $this->version, true);

        /**
         * backbone collections
         */
        wp_enqueue_script('thrive-clever-widgets-options-collection', plugin_dir_url(__FILE__) . 'js-min/collections/options.js', array('jquery', 'underscore', 'backbone'), $this->version, true);
        wp_enqueue_script('thrive-clever-widgets-tabs-collection', plugin_dir_url(__FILE__) . 'js-min/collections/tabs.js', array('jquery', 'underscore', 'backbone'), $this->version, true);
        wp_enqueue_script('thrive-clever-widgets-hangers-collection', plugin_dir_url(__FILE__) . 'js-min/collections/hangers.js', array('jquery', 'underscore', 'backbone'), $this->version, true);
        wp_enqueue_script('thrive-clever-widgets-filters-collection', plugin_dir_url(__FILE__) . 'js-min/collections/filters.js', array('jquery', 'underscore', 'backbone'), $this->version, true);
        wp_enqueue_script('thrive-clever-widgets-templates-collection', plugin_dir_url(__FILE__) . 'js-min/collections/templates.js', array('jquery', 'underscore', 'backbone'), $this->version, true);

        /**
         * backbone views
         */
        wp_enqueue_script('thrive-clever-widgets-hanger-view', plugin_dir_url(__FILE__) . 'js-min/views/hanger-view.js', array('jquery', 'underscore', 'backbone'), $this->version, true);
        wp_enqueue_script('thrive-clever-widgets-option-view', plugin_dir_url(__FILE__) . 'js-min/views/option-view.js', array('jquery', 'underscore', 'backbone'), $this->version, true);
        wp_enqueue_script('thrive-clever-widgets-tab-content-view', plugin_dir_url(__FILE__) . 'js-min/views/tab-content-view.js', array('jquery', 'underscore', 'backbone'), $this->version, true);
        wp_enqueue_script('thrive-clever-widgets-filters-view', plugin_dir_url(__FILE__) . 'js-min/views/filters-view.js', array('jquery', 'underscore', 'backbone'), $this->version, true);
        wp_enqueue_script('thrive-clever-widgets-tab-label-view', plugin_dir_url(__FILE__) . 'js-min/views/tab-label-view.js', array('jquery', 'underscore', 'backbone'), $this->version, true);
        wp_enqueue_script('thrive-clever-widgets-thickbox-view', plugin_dir_url(__FILE__) . 'js-min/views/thickbox-view.js', array('jquery', 'underscore', 'backbone'), $this->version, true);
        wp_enqueue_script('thrive-clever-widgets-admin-view', plugin_dir_url(__FILE__) . 'js-min/views/admin-view.js', array('jquery', 'underscore', 'backbone'), $this->version, true);
        wp_enqueue_script('thrive-clever-widgets-admin-app', plugin_dir_url(__FILE__) . 'js-min/admin-app.js', array('jquery', 'underscore', 'backbone'), $this->version, true);

        /**
         * tools
         */
        wp_dequeue_script('mpjquerytools');

        $data = array(
            'url' => array(
                'includes' => includes_url()
            )
        );

        wp_localize_script('thrive-clever-widgets-admin-app', 'tcw_const', $data);
    }

    /**
     * Defines an action that renders the settings button for each widget
     */
    public function sidebar_admin_setup()
    {
        add_action('in_widget_form', array($this, 'render_button'));
    }

    /**
     * Display the button for each selected widget
     * @param $widget
     */
    public function render_button($widget)
    {
        if (is_object($widget) && $widget instanceof Thrive_Leads_Widget) {
            return;
        }
        ob_start();
        include plugin_dir_path(__FILE__) . 'partials/thrive-clever-widgets-button.php';
        $template = ob_get_contents();
        ob_end_clean();
        echo $template;
    }

    /**
     * Build all the content for the thickbox content
     */
    public function display_widget_popup()
    {
        if (!$this->isLicenseActivated()) {
            include plugin_dir_path(__FILE__) . 'partials/thrive-clever-widgets-licensing-form.php';
            die;
        }
        $this->load_dependencies();

        $widget = $_GET['widget'];

        $hangers[] = new Thrive_Clever_Widgets_Hanger('show_widget_options', $widget);
        $hangers[] = new Thrive_Clever_Widgets_Hanger('hide_widget_options', $widget);

        try {
            /**
             * @var $hanger Thrive_Clever_Widgets_Hanger
             */
            foreach ($hangers as $hanger) {
                $hanger->initTabs(array(
                    'other_screens' => __('Basic Settings', 'thrive-cw'),
                    'taxonomy_terms' => __("Categories etc.", 'thrive-cw'),
                    'posts' => __('Posts', 'thrive-cw'),
                    'pages' => __('Pages', 'thrive-cw'),
                    'page_templates' => __('Page Templates', 'thrive-cw'),
                    'post_types' => __('Post Types', 'thrive-cw'),
                    'taxonomy_archives' => __('Archive Pages', 'thrive-cw'),
                    'others' => __('Other', 'thrive-cw'),
                ));
            }

            $savedOptions = new Thrive_Clever_Widgets_Widget_Options($widget);
            $savedOptions->initOptions();

            //used in popup partial
            $savedTemplates = $this->getSavedTemplates();


        } catch (Exception $e) {
            var_dump($e->getMessage());
            die;
        }

        include plugin_dir_path(__FILE__) . 'partials/thrive-clever-widgets-popup.php';
        die;
    }

    public function getSavedTemplates()
    {
        $savedTemplates = new Thrive_Clever_Widgets_Saved_Options();
        $templates = $savedTemplates->getAll();
        foreach ($templates as $template) {
            $template->show_widget_options = $this->processTpl(json_decode(stripcslashes($template->show_widget_options), true));
            $template->hide_widget_options = $this->processTpl(json_decode(stripcslashes($template->hide_widget_options), true));
        }
        return $templates;
    }

    protected function processTpl($savedOptions)
    {
        $return = array();
        foreach ($savedOptions['tabs'] as $index => $tab) {
            $return[$tab['identifier']] = array(
                'options' => $tab['options'],
                'index' => $index
            );
        }
        return $return;
    }

    public function save_options()
    {
        if (empty($_POST['options']) || empty($_POST['widget'])) {
            die;
        }

        require_once plugin_dir_path(dirname(__FILE__)) . 'database/class-thrive-clever-widgets-database-manager.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/classes/Thrive_Clever_Widgets_Widget_Options.php';

        $widget = new Thrive_Clever_Widgets_Widget_Options($_POST['widget'], $_POST['options'][0], $_POST['options'][1]);
        $widget->save();
    }

    public function save_template()
    {
        if (empty($_POST['options']) || empty($_POST['name'])) {
            die;
        }

        require_once plugin_dir_path(dirname(__FILE__)) . 'database/class-thrive-clever-widgets-database-manager.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/classes/Thrive_Clever_Widgets_Saved_Options.php';

        $widget = new Thrive_Clever_Widgets_Saved_Options($_POST['name'], $_POST['options'][0], $_POST['options'][1]);
        $saved = $widget->save();

        $return = array(
            'success' => $saved === true,
            'message' => $saved === true ? __('Template saved with success !', 'thrive-cw') : $saved,
            'templates' => $saved === true ? $this->getSavedTemplates() : array()
        );

        exit(json_encode($return));
    }

    public function add_settings_menu()
    {
        add_options_page('Thrive Clever Widgets', 'Thrive Clever Widgets', 'manage_options', 'tcw_license_validation', array($this, 'license_validation_page'));
    }

    public function license_validation_page()
    {
        if (!$this->isLicenseActivated()) {
            include plugin_dir_path(__FILE__) . 'partials/thrive-clever-widgets-licensing-form.php';
            return;
        }

        include plugin_dir_path(__FILE__) . 'partials/thrive-clever-widgets-license.php';
    }

    public function license_check($licensed_email, $license_key = '')
    {
        $api_url = "https://thrivethemes.com/wp-content/plugins/license_check/api/request.php";
        $api_url .= "?license=" . $license_key;
        $api_url .= "&email=" . $licensed_email;
        $api_url .= "&product_id=4,5,6,7,37,38,39,40,41,42,43,23,24";
        $licenseValid = wp_remote_get($api_url, array(
            'sslverify' => false,
            'timeout' => 120
        ));

        if (is_wp_error($licenseValid)) {
            /** @var WP_Error $licenseValid */
            /** Couldn't connect to the API URL - possible because wp_remote_get failed for whatever reason.  Maybe CURL not activated on server, for instance */
            $response = new stdClass();
            $response->success = 0;
            $response->reason = sprintf(__("An error occurred while connecting to the license server. Error: %s. Please login to thrivethemes.com, report this error message on the forums and we'll get this sorted for you", 'thrive-cw'), $licenseValid->get_error_message());

            return $response;
        }

        $response = @json_decode($licenseValid['body']);

        if (empty($response)) {
            $response = new stdClass();
            $response->success = 0;
            $response->reason = sprintf(__("An error occurred while receiving the license status. The response was: %s. Please login to thrivethemes.com, report this error message on the forums and we'll get this sorted for you.", 'thrive-cw'), $licenseValid['body']);

            return $response;
        }

        return $response;
    }

    public function load_plugin_textdomain()
    {
        $domain = 'thrive-cw';
        $locale = apply_filters('plugin_locale', get_locale(), $domain);

        load_textdomain($domain, WP_LANG_DIR . '/thrive/' . $domain . "-" . $locale . ".mo");
        load_plugin_textdomain($domain, FALSE, dirname(dirname(plugin_basename(__FILE__))) . '/languages/');
    }

    /**
     ****===*** PRIVATE FUNCTIONS ***===***
     */

    /**
     * Load all the dependencies that are needed for this manager
     */
    private function load_dependencies()
    {
        require_once plugin_dir_path(__FILE__) . 'classes/Thrive_Clever_Widgets_Filter.php';
        require_once plugin_dir_path(__FILE__) . 'classes/Thrive_Clever_Widgets_Action.php';
        require_once plugin_dir_path(__FILE__) . 'classes/Thrive_Clever_Widgets_Option.php';
        require_once plugin_dir_path(__FILE__) . 'classes/Thrive_Clever_Widgets_Hanger.php';
        require_once plugin_dir_path(__FILE__) . 'classes/Thrive_Clever_Widgets_Tab_Interface.php';
        require_once plugin_dir_path(__FILE__) . 'classes/Thrive_Clever_Widgets_Tab.php';
        require_once plugin_dir_path(__FILE__) . 'classes/Thrive_Clever_Widgets_Tab_Factory.php';
        require_once plugin_dir_path(__FILE__) . 'classes/Thrive_Clever_Widgets_Posts_Tab.php';
        require_once plugin_dir_path(__FILE__) . 'classes/Thrive_Clever_Widgets_Pages_Tab.php';
        require_once plugin_dir_path(__FILE__) . 'classes/Thrive_Clever_Widgets_Page_Templates_Tab.php';
        require_once plugin_dir_path(__FILE__) . 'classes/Thrive_Clever_Widgets_Post_Types_Tab.php';
        require_once plugin_dir_path(__FILE__) . 'classes/Thrive_Clever_Widgets_Taxonomy_Archives_Tab.php';
        require_once plugin_dir_path(__FILE__) . 'classes/Thrive_Clever_Widgets_Taxonomy_Terms_Tab.php';
        require_once plugin_dir_path(__FILE__) . 'classes/Thrive_Clever_Widgets_Other_Screens_Tab.php';
        require_once plugin_dir_path(__FILE__) . 'classes/Thrive_Clever_Widgets_Direct_Urls_Tab.php';
        require_once plugin_dir_path(__FILE__) . 'classes/Thrive_Clever_Widgets_Visitors_Status_Tab.php';
        require_once plugin_dir_path(__FILE__) . 'classes/Thrive_Clever_Widgets_Others_Tab.php';
        require_once plugin_dir_path(dirname(__FILE__)) . '/database/class-thrive-clever-widgets-database-manager.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/classes/Thrive_Clever_Widgets_Widget_Options.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/classes/Thrive_Clever_Widgets_Saved_Options.php';
    }

    private function isLicenseActivated()
    {
        return get_option('tcw_license_status') === 'ACTIVE';
    }
}
