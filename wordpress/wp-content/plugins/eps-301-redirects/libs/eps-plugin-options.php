<?php
/**
 *
 * Plugin Options Handler
 *
 * @author Shawn Wernig, Eggplant Studios, www.eggplantstudios.ca
 * @version 1.0.0
 * @copyright 2015 Eggplant Studios
 * @package EPS Boilerplate
 *
 *
 */

if( ! class_exists('EPS_Redirects_Plugin_Options') )
{

class EPS_Redirects_Plugin_Options {

    /**
     *
     * Will be populated with the JSON file.
     *
     * @var array
     *
     */
    public $settings = array();

    public $plugin;

    private $menu_locations = array(
        'menu', 'dashboard', 'posts', 'media', 'links', 'pages', 'comments', 'theme', 'plugins', 'users', 'management', 'options'
    );


    /**
     *
     * Initialize the Theme Options, and register some actions.
     *
     */
    public function __construct( EPS_Redirects_Plugin $Plugin ){
        $this->plugin = $Plugin;
        $this->build_settings();
        add_action( 'admin_init', array($this, 'options_defaults') );
        add_action( 'admin_init', array($this, 'register_settings') );
        add_action( 'admin_menu', array($this, 'add_options_page') );

    }

    /**
     *
     * Pull settings from the theme-options.json file.
     *
     */
    private function build_settings() {
        $this->settings = $this->read_settings( EPS_REDIRECT_PATH . 'options.json' );
    }

    private function read_settings( $uri )
    {
        if( file_exists( $uri ) )
        {
            if ( is_readable( $uri ) )
            {
                $data = $this->read_json_from_file($uri);
            }
            else
            {
                chmod($uri, 0644);
                $data = $this->read_json_from_file($uri);
                if( $data )
                {
                    $data = array(
                        'error' => array(
                            "title"         => "Oops!",
                            "description"   => "An essential file (options.json) could not be read. Please check your folder permissions.",
                            "callback"      => "error",
                            "fields"        => ''
                        )
                    );
                }
            }
        }
        else
        {
            $data = array(
                'error' => array(
                    "title"         => "Oops!",
                    "description"   => "An essential file (options.json) could not be found. Please re-install the plugin.",
                    "callback"      => "error",
                    "fields"        => ''
                )
            );
        }


        return $data;
    }

    private function read_json_from_file($uri)
    {
        try
        {
            $json = file_get_contents( $uri );
            return json_decode($json,true);
        }
        catch( Exception $e )
        {
            return false;
        }
    }

    /**
     *
     * Build the setting slug based on section.
     *
     * @param string $section
     * @return string
     */
    private function setting_slug( $section = 'general' ) {
        return $this->plugin->config('option_slug') . '_' . $section;
    }


    /**
     *
     * Registers the settings based on the JSON file we imported.
     *
     */
    public function register_settings() {

		foreach( $this->settings as $section => $args ) {
			
			register_setting(  
				$this->setting_slug( $section ), 
				$this->setting_slug( $section ), 
				array( $this, 'sanitize_inputs' )
			);
					
			add_settings_section(  
		    	$this->setting_slug( $section ), 
		    	$args['title'],
                // array( $this, 'section_'.$section.'_callback'),
                array( $this, 'section_callback'),
                $this->plugin->config('option_slug')  . '_' . $section
			);
			
			foreach( $args['fields'] as $slug => $args ) {
				$args['section'] = $section;
				add_settings_field( 
			    	$slug, 
			    	$args['label'], 
			    	array( $this, 'field_callback'),  
			    	$this->setting_slug( $section ), 
			    	$this->setting_slug( $section ),
			    	$args
				);
				
			}
		}
	}


    /**
     *
     * Sanitize inputs. TODO
     *
     * @param $args
     * @return mixed
     */
    public function sanitize_inputs( $args ) {
        return $args;
	}

    /**
     *
     * If this is the first time we're loading this, we can use the JSON file to populate some defaults.
     *
     */
    public function options_defaults() {

        foreach( $this->settings as $section => $args ) {

            $settings = get_option( $this->setting_slug( $section ) );

            if ( empty( $settings ) ) {
                $settings = array();
                foreach( $args['fields'] as $slug => $args ) {
                    $settings[$slug] = $args['default'];
                }

                add_option( $this->setting_slug( $section ), $settings, '', 'yes' );
            }

        }

	}

    
    
    /**
     *
     * Outputs the Sections intro HTML. A callback.
     *
     * TODO: Can this be made more dynamic?
     *
     * @param $args
     *
     */
    function section_callback( $args ) {        
        if( isset( $_GET['tab'] ) )
        {
            $tab = $_GET['tab'];
        }
        else
        {
            $sections = array_keys( $this->settings );
            $tab = $sections[0];
        }
		echo $this->settings[$tab]['description'];
	}

    /**
     *
     * Output the Field HTML based on the JSON and 'type' of input.
     *
     * @param $args
     *
     */
    function field_callback( $args ) {
		$option_slug = $this->setting_slug( $args['section'] );
	    $setting = get_option( $this->setting_slug( $args['section'] ) );
	    printf ("<input type='text' name='%s[%s]' value='%s' /><small>%s</small>",
		    $option_slug,
		    $args['slug'],
            ( isset( $setting[ $args['slug'] ] ) ? $setting[ $args['slug'] ] : null ),
		    $args['description']
		);
	}
	
	/**
     * 
     * ADD_PLUGIN_PAGE
     * 
     * This function initialize the plugin settings page.
     * 
     * @return string
     * @author epstudios
     *      
     */
    public function add_options_page(){
        if( in_array( $this->plugin->config('menu_location'), $this->menu_locations ) )
        {
            $func = sprintf("add_%s_page", $this->plugin->config('menu_location') );
            return $func($this->plugin->name, $this->plugin->name, $this->plugin->config('page_permission'), $this->plugin->config('page_slug'), array($this, 'do_admin_page'));
        }
        else
        {
            // TODO proper errors dude.
            printf( 'ERROR: menu location "%s" not valid.', $this->config['menu_location'] );
        }
        return false;
    }

	/**
     * 
     * DO_ADMIN_PAGE
     * 
     * This function will create the admin page.
     * 
     * @author epstudios
     *      
     */
    public function do_admin_page(){
        $current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : false;
        if( ! $current_tab )
        {
            $sections = $this->settings;
            $current_tab = key($sections);
        }
        ?>
        <div class="wrap">
            <h2><?php echo $this->plugin->name; ?> Settings</h2>
            <?php $this->get_tab_nav( $current_tab  ); ?>
            <?php $this->get_tab( $current_tab  ); ?>
        </div>
        <?php
    }

    /**
     *
     * Outputs the tab navigation based on our sections.
     *
     * @param string $current
     */
    function get_tab_nav( $current = 'general' ) {
	    echo '<div id="icon-themes" class="icon32"><br></div>';
	    echo '<h2 class="nav-tab-wrapper">';


	    foreach( $this->settings as $tab => $args ){
	        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
			printf("<a class='nav-tab%s' href='?page=%s&tab=%s'>%s</a>",
				$class,
                $this->plugin->config('option_slug'),
				$tab,
				$args['title']
			);
	    }
	    echo '</h2>';
	}

    /**
     *
     * Gets the content for the current tab.
     *
     * @param string $tab
     *
     */
    public function get_tab( $tab = 'general' ) {
        if ( $this->tab_exists( $tab ) ) {


            if( has_action( $tab.'_admin_tab'))
            {
                do_action( $tab.'_admin_tab', $this->settings[$tab] );
            }
            else
            {
            ?>
            <form method="post" action="<?php echo admin_url('options.php'); ?>">
                <?php

                settings_fields( $this->setting_slug( $tab ) );
                do_action( $this->setting_section_callback( $tab, "_before") );
                do_settings_sections( $this->plugin->config('option_slug') . '_' . $tab );
                do_action( $this->setting_section_callback($tab, '_after') );
                submit_button();

                ?>
            </form>
            <?php
            }
        }
	}

    private function setting_section_callback( $tab, $suffix = '' )
    {
        return $this->plugin->config('option_slug') . '_settings_' . $this->settings[$tab]['callback'] . $suffix;
    }


    /**
     *
     * Checks to see if a tab exists.
     *
     * @param $tab
     * @return bool
     * @throws Exception
     *
     */
    public function tab_exists( $tab ) {
		if ( ! array_key_exists( $tab, $this->settings ) ) {
			throw new Exception('Tab does not exist');
		}
		return true;
	}
	
}

}