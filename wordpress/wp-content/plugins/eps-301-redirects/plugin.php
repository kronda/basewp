<?php
/**
 * Class EPS_Redirects_Plugin
 *
 * Inits the EPS_Redirects Plugin's core functionality and admin management.
 *
 *
 */



class EPS_Redirects_Plugin {

    protected $config = array(
        'version'           => EPS_REDIRECT_VERSION,
        'option_slug'       => 'eps_redirects',
        'page_slug'         => 'eps_redirects',
        'page_title'        => 'EPS Redirects',
        'menu_location'     => 'options',
        'page_permission'   => 'manage_options',
        'directory'         => 'eps-301-redirects'
    );

    protected $dependancies = array();

    protected $tables = array();

    public $name = 'EPS Redirects';


    protected $resources = array(
        'css' => array(
            'admin.css'
        ),
        'js' => array(
            'admin.js'
        )
    );


    protected $options;
    protected $messages = array();

    public function __construct()
    {
        $this->config['url'] = plugins_url() . $this->config['directory'] . '/';
        $this->config['path'] = EPS_REDIRECT_PATH . $this->config['directory'] . '/';

        if( class_exists('EPS_Redirects_Plugin_Options') )
            $this->settings = new EPS_Redirects_Plugin_Options( $this );

        register_activation_hook(	__FILE__, array($this, '_activation'));
        register_deactivation_hook(	__FILE__, array($this, '_deactivation'));

        if ( !self::is_current_version() )  self::update_self();
        add_action('init',                  array($this, 'plugin_resources'));

        // Template Hooks
        add_action( 'redirects_admin_tab', array($this, 'admin_tab_redirects'), 10, 1 );
        add_action( '404s_admin_tab', array($this, 'admin_tab_404s'), 10, 1 );
        add_action( 'error_admin_tab', array($this, 'admin_tab_error'), 10, 1 );
        add_action( 'import-export_admin_tab', array($this, 'admin_tab_import_export'), 10, 1 );
        add_action( 'eps_redirects_panels_left', array($this, 'admin_panel_cache'));
        add_action( 'eps_redirects_panels_right', array($this, 'admin_panel_donate'));

        // Actions
        add_action( 'admin_init',            array($this, 'check_plugin_actions'));

    }

    public function resolve_dependencies()
    {
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        foreach( $this->dependencies as $name => $path_to_plugin )
        {
            if ( ! is_plugin_active( $path_to_plugin ) )
            {
                echo $name . ' IS NOT INSTALLED!';
            }
        }
    }


    private function resource_path( $path, $resource )
    {
        return strtolower(
            $this->config['url']
            . $path . '/'
            . $resource );
    }

    private function resource_name( $resource )
    {
        return strtolower( $this->name . '_' . key( $resource ) );
    }

    public static function _activation()
    {
        self::_create_redirect_table(); // Maybe create the tables
        if ( !self::is_current_version() )  self::update_self();
    }
    public static function _deactivation() {}


    public function admin_url( $vars = array() )
    {
        $vars = array( 'page' => $this->config['page_slug'] ) + $vars;
        $url = 'options-general.php?' . http_build_query( $vars );
        return admin_url( $url );
    }
    /**
     *
     * update_self
     *
     * This function will check the current version and do any fixes required
     *
     * @return string - version number.
     * @author epstudios
     *
     */
    public function update_self()
    {
        $version = get_option( 'eps_redirects_version' );

        if( version_compare($version, '2.0.0', '<')) {
            // migrate old format to new format.
            add_action('admin_init', array($this, '_migrate_to_v2'), 1 );
        }
        $this->set_current_version( EPS_REDIRECT_VERSION );
        return EPS_REDIRECT_VERSION;
    }

    /**
     *
     * _migrate_to_v2
     *
     * Will migrate the old storage method to the new tables.
     *
     * @return nothing
     * @author epstudios
     *
     */
    public static function _migrate_to_v2() {
        $redirects = get_option( self::$option_slug );

        if (empty($redirects)) return false; // No redirects to migrate.

        $new_redirects = array();

        foreach ($redirects as $from => $to ) {
            $new_redirects[] = array(
                'id'        => false,
                'url_to'    => urldecode($to),
                'url_from'  => $from,
                'type'      => 'url',
                'status'    => '301'
            );
        }

        EPS_Redirects::_save_redirects( $new_redirects );
    }

    /**
     *
     * _create_tables
     *
     * Creates the database architecture
     *
     * @return nothing
     * @author epstudios
     *
     */
    public static function _create_redirect_table()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "redirects";

        $sql = "CREATE TABLE $table_name (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          url_from VARCHAR(256) DEFAULT '' NOT NULL,
          url_to VARCHAR(256) DEFAULT '' NOT NULL,
          status VARCHAR(12) DEFAULT '301' NOT NULL,
          type VARCHAR(12) DEFAULT 'url' NOT NULL,
          count mediumint(9) DEFAULT 0 NOT NULL,
          UNIQUE KEY id (id)
       );";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        return dbDelta( $sql );
    }





    /**
     *
     * plugin_resources
     *
     * Enqueues the resources, and makes sure we have what we need to proceed.
     *
     * @return nothing
     * @author epstudios
     *
     */
    public static function plugin_resources()
    {
        global $EPS_Redirects_Plugin;
        if( is_admin() && isset($_GET['page']) && $_GET['page'] == $EPS_Redirects_Plugin->config('page_slug') ) {
            wp_enqueue_script('jquery');


            wp_enqueue_script('eps_redirect_script', EPS_REDIRECT_URL .'js/scripts.js');
            wp_enqueue_style('eps_redirect_styles', EPS_REDIRECT_URL .'css/eps_redirect.css');
        }

        global $wp_rewrite;
        if( !isset($wp_rewrite->permalink_structure) || empty($wp_rewrite->permalink_structure) )
        {
            $EPS_Redirects_Plugin->add_admin_message('WARNING: EPS 301 Redirects requires that a permalink structure is set. The Default Wordpress permalink structure is not compatible. Please update the <a href="options-permalink.php" title="Permalinks">Permalink Structure</a>', "error" );
        }

        global $wpdb;
        $table_name = $wpdb->prefix . "redirects";
        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
        {
            $url = $EPS_Redirects_Plugin->admin_url( array('action' => 'eps_create_tables') );
            $EPS_Redirects_Plugin->add_admin_message('WARNING: It looks like we need to <a href="'.$url.'" title="Permalinks">Create the Database Tables First!</a>', "error" );
        }

    }

    /**
     *
     * check_plugin_actions
     *
     * This function handles various POST requests.
     *
     * @return nothing
     * @author epstudios
     *
     */
    public function check_plugin_actions(){
        if( is_admin() && isset($_GET['page']) && $_GET['page'] == $this->config('page_slug') )
        {
            // Upload a CSV
            if( isset($_POST['eps_redirect_upload']) && wp_verify_nonce( $_POST['eps_redirect_nonce_submit'], 'eps_redirect_nonce') ) {
                self::_upload();
            }
            // Export a CSV
            if( isset($_POST['eps_redirect_export']) && wp_verify_nonce( $_POST['eps_redirect_nonce_submit'], 'eps_redirect_nonce') )
            {
                self::export_csv();
            }

            // Refresh the Transient Cache
            if ( isset( $_POST['eps_redirect_refresh'] ) && wp_verify_nonce( $_POST['eps_redirect_nonce_submit'], 'eps_redirect_nonce') )
            {
                $post_types = get_post_types(array('public'=>true), 'objects');
                foreach ($post_types as $post_type )
                {
                    $options = eps_dropdown_pages( array('post_type'=>$post_type->name ) );
                    set_transient( 'post_type_cache_'.$post_type->name, $options, HOUR_IN_SECONDS );
                }
                $this->add_admin_message("SUCCCESS: Cache Refreshed.", "updated" );
            }

            // Save Redirects
            if ( isset( $_POST['eps_redirect_submit'] ) && wp_verify_nonce( $_POST['eps_redirect_nonce_submit'], 'eps_redirect_nonce') )
            {
                self::_save_redirects( EPS_Redirects::_parse_serial_array($_POST['redirect']) );
            }

            // Create tables
            if( isset($_GET['action']) && $_GET['action'] == 'eps_create_tables' )
            {
                $result = self::_create_redirect_table();
            }
        }
    }


    /**
     *
     * export_csv
     *
     * @return nothing
     * @author epstudios
     *
     */
    public static function export_csv()
    {
        $entries = EPS_Redirects::get_all();
        $filename = sprintf("%s-redirects.csv",
            date('Y-m-d')
        );
        if( $entries )
        {
            header('Content-disposition: attachment; filename='.$filename);
            header('Content-type: text/csv');

            foreach( $entries as $entry )
            {
                $csv = array(
                    $entry->status,
                    $entry->url_from,
                    $entry->url_to,
                    $entry->count
                );
                echo implode(',',$csv);
                echo "\n";
            }

            die();
        }

    }

    /**
     *
     * _upload
     *
     * This function handles the upload of CSV files, in accordance to the upload method specified.
     *
     * @return html string
     * @author epstudios
     *
     */
    private function _upload() {
        $new_redirects = array();

        $counter = array(
            'new' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0,
            'total' => 0
        );

        $mimes = array(
            'text/csv',
            'text/tsv',
            'text/plain',
            'application/csv',
            'text/comma-separated-values',
            'application/excel',
            'application/vnd.ms-excel',
            'application/vnd.msexcel',
            'text/anytext',
            'application/octet-stream',
            'application/txt'
        );
        ini_set('auto_detect_line_endings',TRUE);

        if( !in_array($_FILES['eps_redirect_upload_file']['type'], $mimes) ) {
            $this->add_admin_message(sprintf("WARNING: Not a valid CSV file - the Mime Type '%s' is wrong! No new redirects have been added.",
                $_FILES['eps_redirect_upload_file']['type']
            ), "error" );
            return false;
        }

        // open the file.
        if (($handle = fopen($_FILES['eps_redirect_upload_file']['tmp_name'], "r")) !== FALSE)
        {
            $counter['total'] = 1;
            while (($redirect = fgetcsv($handle, 0, ",")) !== FALSE)
            {
                $redirect = array_filter($redirect);

                if( empty( $redirect ) ) continue;

                $args = count($redirect);

                if( $args > 4 || $args < 2 )
                {
                    // Bad line. Too many/few arguments.
                    $this->add_admin_message(
                        sprintf("WARNING: Encountered a badly formed entry in your CSV file on line %d (we skipped it).",
                            $counter['total']
                        ),
                        "error" );
                    $counter['errors'] ++;
                    continue;
                }

                $status     = (isset($redirect[0])) ? $redirect[0] : false;
                $url_from   = (isset($redirect[1])) ? $redirect[1] : false;
                $url_to     = (isset($redirect[2])) ? $redirect[2] : false;
                $count      = (isset($redirect[3])) ? $redirect[3] : false;

                switch( strtolower( $status ) ) {
                    case '404': $status = 404; break;
                    case '302': $status = 302; break;
                    case 'off':
                    case 'no':
                    case 'inactive': $status = 'inactive'; break;
                    default: $status = 301; break;
                }

                // If the user supplied a post_id, is it valid? If so, use it!
                if( $url_to && $post_id = url_to_postid( $url_to )  )
                {
                    $url_to = $post_id;
                }

                // new redirect!
                $new_redirect = array(
                    'id'        => false, // new
                    'url_from'  => $url_from,
                    'url_to'    => $url_to,
                    'type'      => ( is_numeric( $url_to ) ) ? 'post' : 'url',
                    'status'    => $status,
                    'count'     => $count
                );

                array_push($new_redirects, $new_redirect);
                $counter['total'] ++;
            }
            fclose($handle); // close file.
        }


        if( $new_redirects )
        {
            $save_redirects = array();
            foreach( $new_redirects as $redirect )
            {
                // Decide how to handle duplicates:
                switch( strtolower( $_POST['eps_redirect_upload_method'] ) )
                {
                    case 'skip':
                        if( ! EPS_Redirects::redirect_exists( $redirect ) )
                        {
                            $save_redirects[] = $redirect;
                            $counter['new'] ++;
                        }
                        else
                        {
                            $counter['skipped'] ++;
                        }
                        break;
                    case 'update':
                        if( $entry = EPS_Redirects::redirect_exists( $redirect ) )
                        {
                            $redirect['id'] = $entry->id;
                            $counter['updated'] ++;
                            $save_redirects[] = $redirect;
                        }
                        else
                        {
                            $save_redirects[] = $redirect;
                            $counter['new'] ++;
                        }
                        break;
                    default:
                        $save_redirects[] = $redirect;
                        $counter['new'] ++;
                        break;
                }
            }

            if( ! empty( $save_redirects ) )
            {
                EPS_Redirects::_save_redirects( $save_redirects );
            }

            $this->add_admin_message(sprintf(
                "SUCCCESS: %d New Redirects, %d Updated, %d Skipped, %d Errors. (Attempted to import %d redirects).",
                $counter['new'],
                $counter['updated'],
                $counter['skipped'],
                $counter['errors'],
                $counter['total']
            ), "updated" );

        }
        else
        {
            $this->add_admin_message("WARNING: Something's up. No new redirects were added, please review your CSV file.", "error" );
        }
        ini_set('auto_detect_line_endings',FALSE);
    }



    /**
     *
     * Template Hooks
     *
     * @author epstudios
     *
     */
    public static function admin_panel_cache()
    {
        include ( EPS_REDIRECT_PATH . 'templates/admin-panel-cache.php'  );
    }
    public static function admin_panel_donate()
    {
        include ( EPS_REDIRECT_PATH . 'templates/admin-panel-donate.php'  );
    }

    public static function admin_tab_redirects( $options )
    {
        include ( EPS_REDIRECT_PATH . 'templates/admin-tab-redirects.php'  );
    }
    public static function admin_tab_404s( $options )
    {
        include ( EPS_REDIRECT_PATH . 'templates/admin-tab-404s.php'  );
    }
    public static function admin_tab_import_export( $options )
    {
        include ( EPS_REDIRECT_PATH . 'templates/admin-tab-import-export.php'  );
    }
    public static function admin_tab_error( $options )
    {
        include ( EPS_REDIRECT_PATH . 'templates/admin-tab-error.php'  );
    }


    /**
     *
     * CHECK VERSION
     *
     * This function will check the current version and do any fixes required
     *
     * @return string - version number.
     * @author epstudios
     *
     */

    public function config($name)
    {
        return ( isset($this->config[ $name ]) ) ? $this->config[ $name ] : false;
    }

    /**
     *
     *
     * Activation and Deactivation Handlers.
     *
     * @return nothing
     * @author epstudios
     */
    public function activation_error() {
        file_put_contents($this->config('path'). '/error_activation.html', ob_get_contents());
    }


    public static function is_current_version()
    {
        return version_compare( self::current_version(), EPS_REDIRECT_VERSION, '=') ? true : false; // TODO decouple
    }
    public static function current_version()
    {
        return get_option( 'eps_redirects_version' ); // TODO decouple
    }
    public static function set_current_version( $version )
    {
        update_option( 'eps_redirects_version', $version );
    }


    /**
     *
     * Notices
     *
     * These functions will output a variable containing the admin ajax url for use in javascript.
     *
     * @author epstudios
     *
     */
    protected function add_admin_message( $message, $code )
    {
        $this->messages[] = array(  $code => $message );
        add_action( 'admin_notices', array($this, 'display_admin_messages') );
    }
    public static function display_admin_messages()
    {
        global $EPS_Redirects_Plugin;
        if( is_array( $EPS_Redirects_Plugin->messages ) && ! empty( $EPS_Redirects_Plugin->messages ) )
        {
            foreach(  $EPS_Redirects_Plugin->messages as $entry )
            {
                $code = key($entry);
                $message = reset($entry);

                if( ! in_array($code, array('error','updated') ) )
                {
                    $code = 'updated';
                }
                $EPS_Redirects_Plugin->admin_notice( $message, $code);
            }
        }
    }
    public function admin_notice( $string, $type = "updated" ) {
        printf('<div class="%s"><p>%s</p></div>',
            $type,
            $string
        );
    }
}

// Init the plugin.
$EPS_Redirects_Plugin = new EPS_Redirects_Plugin();
?>