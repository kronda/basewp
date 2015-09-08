<?php

/**
 * Class Thrive_Clever_Widgets_Widget_Options
 * JSON options saved by user in database
 * Mapper model over database table
 */
class Thrive_Clever_Widgets_Widget_Options
{
    private $table_name = 'widgets_options';
    private $widget;
    private $description;
    public $show_widget_options;
    public $hide_widget_options;
    private $db;

    public function __construct($widget, $show_widget_options = '', $hide_widget_options = '')
    {
        /**
         * @var $wpdb wpdb
         */
        global $wpdb;
        $this->db = $wpdb;
        $this->table_name = Thrive_Clever_Widgets_Database_Manager::tableName($this->table_name);
        $this->widget = $widget;
        $this->show_widget_options = $show_widget_options;
        $this->hide_widget_options = $hide_widget_options;
    }

    protected function _processPreSave($jsonOptions)
    {
        $options = @json_decode(stripcslashes($jsonOptions), true);
        if (empty($options) || empty($options['tabs'])) {
            return json_encode(array('identifier' => $jsonOptions['identifier']));
        }

        foreach ($options['tabs'] as $index => $tab) {
            $saved_options = array();
            foreach ($tab['options'] as $i => $item) {
                if (!empty($item['isChecked']) || $item['type'] == 'direct_url') {
                    $saved_options [] = $item['id'];
                }
            }
            unset($options['tabs'][$index]['actions']);
            unset($options['tabs'][$index]['filters']);
            $options['tabs'][$index]['options'] = $saved_options;
        }

        return json_encode($options);
    }

    public function save()
    {
        if ($this->delete() === false) {
            return $this->db->last_error;
        }
        $this->db->suppress_errors();

        $show_options = $this->_processPreSave($this->show_widget_options);
        $hide_options = $this->_processPreSave($this->hide_widget_options);

        return $this->db->insert($this->table_name, array(
            'widget' => $this->widget,
            'description' => $this->description,
            'show_widget_options' => $show_options,
            'hide_widget_options' => $hide_options
        )) !== false ? true : $this->db->last_error;
    }

    public function delete()
    {
        //old code for WP 4.1.1
        //$this->db->delete($this->table_name, array('`widget`' => $this->widget));

        //new code for WP 4.1.2
        $result = $this->db->query(
            $this->db->prepare("DELETE FROM `{$this->table_name}` WHERE `widget` = %s", $this->widget)
        );

        return $result;
    }

    /**
     * Read options from database
     * @return $this
     */
    public function initOptions()
    {
        $sql = "SELECT * FROM {$this->table_name} WHERE `widget` = '{$this->widget}'";
        $row = $this->db->get_row($sql);
        if ($row) {
            $this->show_widget_options = $row->show_widget_options;
            $this->hide_widget_options = $row->hide_widget_options;
            $this->description = $row->description;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getShowWidgetOptions()
    {
        return $this->show_widget_options;
    }

    /**
     * @return string
     */
    public function getHideWidgetOptions()
    {
        return $this->hide_widget_options;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    // get current URL
    public function get_current_URL()
    {
        $current_url = 'http';
        $server_https = @$_SERVER["HTTPS"];
        $server_name = $_SERVER["SERVER_NAME"];
        $server_port = $_SERVER["SERVER_PORT"];
        $request_uri = $_SERVER["REQUEST_URI"];
        if ($server_https == "on") $current_url .= "s";
        $current_url .= "://";
        if ($server_port != "80") $current_url .= $server_name . ":" . $server_port . $request_uri;
        else $current_url .= $server_name . $request_uri;
        return $current_url;
    }

    /**
     * Check if any option is checked
     * @return bool
     */
    public function checkForAnyOptionChecked()
    {
        $showingOptions = @json_decode(stripcslashes($this->getShowWidgetOptions()));

        if (empty($showingOptions)) {
            return false;
        }

        //if there are any options saved in any tag return true
        foreach ($showingOptions->tabs as $tab) {
            if (!empty($tab->options)) {
                return true;
            }
        }

        $hidingOptions = @json_decode(stripcslashes($this->getHideWidgetOptions()));

        if (empty($hidingOptions)) {
            return false;
        }

        //if there are any options saved in any tag return true
        foreach ($hidingOptions->tabs as $tab) {
            if (!empty($tab->options)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function displayWidget()
    {
        $display = true;

        /**
         * if none of the options is selected keep displaying the widget
         * and let the Wordpress apply its logic on it
         */
        if (!$this->checkForAnyOptionChecked()) {
            return true;
        }

        $front_page_set = get_option('show_on_front') == 'page';

        if (is_front_page() && $front_page_set) {

            /* @var $otherScreensTab Thrive_Clever_Widgets_Other_Screens_Tab */
            $otherScreensTab = Thrive_Clever_Widgets_Tab_Factory::build('other_screens');
            $otherScreensTab->setSavedOptions($this);

            /* @var $directUrlsTab Thrive_Clever_Widgets_Direct_Urls_Tab */
            $directUrlsTab = Thrive_Clever_Widgets_Tab_Factory::build('direct_urls');
            $directUrlsTab->setSavedOptions($this);

            /* @var $visitorsStatusTab Thrive_Clever_Widgets_Visitors_Status_Tab */
            $visitorsStatusTab = Thrive_Clever_Widgets_Tab_Factory::build('visitors_status');
            $visitorsStatusTab->setSavedOptions($this);
            $visitorsStatus = is_user_logged_in() ? 'logged_in' : 'logged_out';

            $inclusion = $otherScreensTab->isScreenAllowed('front_page')
                || $directUrlsTab->isUrlAllowed($this->get_current_URL())
                || $visitorsStatusTab->isStatusAllowed($visitorsStatus);

            if ($inclusion === false) {
                return false;
            }

            $exclusion = $otherScreensTab->isScreenDenied('front_page')
                || $directUrlsTab->isUrlDenied($this->get_current_URL())
                || $visitorsStatusTab->isStatusDenied($visitorsStatus);

            if ($exclusion === true) {
                $display = false;
            }

            //endif is_front_page

        } else if (is_home() || (is_front_page() && !$front_page_set)) {

            /* @var $otherScreensTab Thrive_Clever_Widgets_Other_Screens_Tab */
            $otherScreensTab = Thrive_Clever_Widgets_Tab_Factory::build('other_screens');
            $otherScreensTab->setSavedOptions($this);

            /* @var $directUrlsTab Thrive_Clever_Widgets_Direct_Urls_Tab */
            $directUrlsTab = Thrive_Clever_Widgets_Tab_Factory::build('direct_urls');
            $directUrlsTab->setSavedOptions($this);

            /* @var $visitorsStatusTab Thrive_Clever_Widgets_Visitors_Status_Tab */
            $visitorsStatusTab = Thrive_Clever_Widgets_Tab_Factory::build('visitors_status');
            $visitorsStatusTab->setSavedOptions($this);
            $visitorsStatus = is_user_logged_in() ? 'logged_in' : 'logged_out';

            $inclusion = $otherScreensTab->isScreenAllowed('blog_index')
                || $directUrlsTab->isUrlAllowed($this->get_current_URL())
                || $visitorsStatusTab->isStatusAllowed($visitorsStatus);

            if ($inclusion === false) {
                return false;
            }

            $exclusion = $otherScreensTab->isScreenDenied('blog_index')
                || $directUrlsTab->isUrlDenied($this->get_current_URL())
                || $visitorsStatusTab->isStatusDenied($visitorsStatus);

            if ($exclusion === true) {
                $display = false;
            }

        } else if (is_page()) {

            /* @var $post WP_Post */
            global $post;

            /** @var Thrive_Clever_Widgets_Other_Screens_Tab $otherScreensTab */
            $otherScreensTab = Thrive_Clever_Widgets_Tab_Factory::build('other_screens');
            $otherScreensTab->setSavedOptions($this);

            /* @var $pagesTab Thrive_Clever_Widgets_Pages_Tab */
            $pagesTab = Thrive_Clever_Widgets_Tab_Factory::build('pages');
            $pagesTab->setSavedOptions($this);

            /* @var $pageTemplatesTab Thrive_Clever_Widgets_Page_Templates_Tab */
            $pageTemplatesTab = Thrive_Clever_Widgets_Tab_Factory::build('page_templates');
            $pageTemplatesTab->setSavedOptions($this);

            /* @var $postTypesTab Thrive_Clever_Widgets_Post_Types_Tab */
            $postTypesTab = Thrive_Clever_Widgets_Tab_Factory::build('post_types');
            $postTypesTab->setSavedOptions($this);

            /* @var $directUrlsTab Thrive_Clever_Widgets_Direct_Urls_Tab */
            $directUrlsTab = Thrive_Clever_Widgets_Tab_Factory::build('direct_urls');
            $directUrlsTab->setSavedOptions($this);

            /* @var $visitorsStatusTab Thrive_Clever_Widgets_Visitors_Status_Tab */
            $visitorsStatusTab = Thrive_Clever_Widgets_Tab_Factory::build('visitors_status');
            $visitorsStatusTab->setSavedOptions($this);
            $visitorsStatus = is_user_logged_in() ? 'logged_in' : 'logged_out';


            $inclusion = $otherScreensTab->allTypesAllowed(get_post_type())
                || $pagesTab->isPageAllowed($post)
                || $postTypesTab->isTypeAllowed(get_post_type())
                || $directUrlsTab->isUrlAllowed($this->get_current_URL())
                || $pageTemplatesTab->isTemplateAllowed(basename(get_page_template()))
                || $visitorsStatusTab->isStatusAllowed($visitorsStatus);


            if ($inclusion === false) {
                return false;
            }

            $exclusion = $otherScreensTab->allTypesDenied(get_post_type())
                || $pagesTab->isPageDenied($post)
                || $postTypesTab->isDeniedType(get_post_type())
                || $directUrlsTab->isUrlDenied($this->get_current_URL())
                || $pageTemplatesTab->isTemplateDenied(basename(get_page_template()))
                || $visitorsStatusTab->isStatusDenied($visitorsStatus);

            if ($exclusion === true) {
                $display = false;
            }

            //endif is_page

        } else if (is_single()) {

            /* @var $post WP_Post */
            global $post;

            /** @var Thrive_Clever_Widgets_Other_Screens_Tab $otherScreensTab */
            $otherScreensTab = Thrive_Clever_Widgets_Tab_Factory::build('other_screens');
            $otherScreensTab->setSavedOptions($this);

            /* @var $postsTab Thrive_Clever_Widgets_Posts_Tab */
            $postsTab = Thrive_Clever_Widgets_Tab_Factory::build('posts');
            $postsTab->setSavedOptions($this);

            /* @var $postTypesTab Thrive_Clever_Widgets_Post_Types_Tab */
            $postTypesTab = Thrive_Clever_Widgets_Tab_Factory::build('post_types');
            $postTypesTab->setSavedOptions($this);

            /* @var $directUrlsTab Thrive_Clever_Widgets_Direct_Urls_Tab */
            $directUrlsTab = Thrive_Clever_Widgets_Tab_Factory::build('direct_urls');
            $directUrlsTab->setSavedOptions($this);

            /* @var $visitorsStatusTab Thrive_Clever_Widgets_Visitors_Status_Tab */
            $visitorsStatusTab = Thrive_Clever_Widgets_Tab_Factory::build('visitors_status');
            $visitorsStatusTab->setSavedOptions($this);
            $visitorsStatus = is_user_logged_in() ? 'logged_in' : 'logged_out';

            /* @var $taxonomyTermsTab Thrive_Clever_Widgets_Taxonomy_Terms_Tab */
            $taxonomyTermsTab = Thrive_Clever_Widgets_Tab_Factory::build('taxonomy_terms');
            $taxonomyTermsTab->setSavedOptions($this);

            $inclusion = $otherScreensTab->allTypesAllowed(get_post_type()) || $postsTab->isPostAllowed($post)
                || $postTypesTab->isTypeAllowed(get_post_type())
                || $directUrlsTab->isUrlAllowed($this->get_current_URL())
                || $visitorsStatusTab->isStatusAllowed($visitorsStatus)
                || $taxonomyTermsTab->isPostAllowed($post);

            if ($inclusion === false) {
                return false;
            }

            $exclusion = $otherScreensTab->allTypesDenied(get_post_type()) || $postsTab->isPostDenied($post)
                || $postTypesTab->isDeniedType(get_post_type())
                || $directUrlsTab->isUrlDenied($this->get_current_URL())
                || $visitorsStatusTab->isStatusDenied($visitorsStatus)
                || $taxonomyTermsTab->isPostDenied($post);

            if ($exclusion === true) {
                $display = false;
            }

            //endif is_single

        } else if (is_archive()) {

            $taxonomy = get_queried_object();

            /* @var $taxonomyArchivesTab Thrive_Clever_Widgets_Taxonomy_Archives_Tab */
            $taxonomyArchivesTab = Thrive_Clever_Widgets_Tab_Factory::build('taxonomy_archives');
            $taxonomyArchivesTab->setSavedOptions($this);

            /* @var $directUrlsTab Thrive_Clever_Widgets_Direct_Urls_Tab */
            $directUrlsTab = Thrive_Clever_Widgets_Tab_Factory::build('direct_urls');
            $directUrlsTab->setSavedOptions($this);

            /* @var $visitorsStatusTab Thrive_Clever_Widgets_Visitors_Status_Tab */
            $visitorsStatusTab = Thrive_Clever_Widgets_Tab_Factory::build('visitors_status');
            $visitorsStatusTab->setSavedOptions($this);
            $visitorsStatus = is_user_logged_in() ? 'logged_in' : 'logged_out';

            $inclusion = $taxonomyArchivesTab->isTaxonomyAllowed($taxonomy)
                || $directUrlsTab->isUrlAllowed($this->get_current_URL())
                || $visitorsStatusTab->isStatusAllowed($visitorsStatus);

            if ($inclusion === false) {
                return false;
            }

            $exclusion = $taxonomyArchivesTab->isTaxonomyDenied($taxonomy)
                || $directUrlsTab->isUrlDenied($this->get_current_URL())
                || $visitorsStatusTab->isStatusDenied($visitorsStatus);

            if ($exclusion === true) {
                $display = false;
            }

            //endif is_archive

        } else if (is_404()) {

            /* @var $otherScreensTab Thrive_Clever_Widgets_Other_Screens_Tab */
            $otherScreensTab = Thrive_Clever_Widgets_Tab_Factory::build('other_screens');
            $otherScreensTab->setSavedOptions($this);

            /* @var $directUrlsTab Thrive_Clever_Widgets_Direct_Urls_Tab */
            $directUrlsTab = Thrive_Clever_Widgets_Tab_Factory::build('direct_urls');
            $directUrlsTab->setSavedOptions($this);

            /* @var $visitorsStatusTab Thrive_Clever_Widgets_Visitors_Status_Tab */
            $visitorsStatusTab = Thrive_Clever_Widgets_Tab_Factory::build('visitors_status');
            $visitorsStatusTab->setSavedOptions($this);
            $visitorsStatus = is_user_logged_in() ? 'logged_in' : 'logged_out';

            $inclusion = $otherScreensTab->isScreenAllowed('404_error_page')
                || $directUrlsTab->isUrlAllowed($this->get_current_URL())
                || $visitorsStatusTab->isStatusAllowed($visitorsStatus);

            if ($inclusion === false) {
                return false;
            }

            $exclusion = $otherScreensTab->isScreenDenied('404_error_page')
                || $directUrlsTab->isUrlDenied($this->get_current_URL())
                || $visitorsStatusTab->isStatusDenied($visitorsStatus);

            if ($exclusion === true) {
                $display = false;
            }

            //endif is_404

        } else if (is_search()) {

            /* @var $otherScreensTab Thrive_Clever_Widgets_Other_Screens_Tab */
            $otherScreensTab = Thrive_Clever_Widgets_Tab_Factory::build('other_screens');
            $otherScreensTab->setSavedOptions($this);

            /* @var $directUrlsTab Thrive_Clever_Widgets_Direct_Urls_Tab */
            $directUrlsTab = Thrive_Clever_Widgets_Tab_Factory::build('direct_urls');
            $directUrlsTab->setSavedOptions($this);

            /* @var $visitorsStatusTab Thrive_Clever_Widgets_Visitors_Status_Tab */
            $visitorsStatusTab = Thrive_Clever_Widgets_Tab_Factory::build('visitors_status');
            $visitorsStatusTab->setSavedOptions($this);
            $visitorsStatus = is_user_logged_in() ? 'logged_in' : 'logged_out';

            $inclusion = $otherScreensTab->isScreenAllowed('search_page')
                || $directUrlsTab->isUrlAllowed($this->get_current_URL())
                || $visitorsStatusTab->isStatusAllowed($visitorsStatus);

            if ($inclusion === false) {
                return false;
            }

            $exclusion = $otherScreensTab->isScreenDenied('search_page')
                || $directUrlsTab->isUrlDenied($this->get_current_URL())
                || $visitorsStatusTab->isStatusDenied($visitorsStatus);

            if ($exclusion === true) {
                $display = false;
            }

            //endif is_search
        }

        return $display;
    }

    public function getTabSavedOptions($tabIndex, $hanger)
    {
        $options = json_decode(stripcslashes($this->$hanger));

        if (empty($options) || empty($options->tabs[$tabIndex]) || empty($options->tabs[$tabIndex]->options)) {
            return array();
        }

        return $options->tabs[$tabIndex]->options;
    }
}
