<?php

/**
* wpv-framework-api.php
*
* API definitions for third party frameworks integration
*
* @since 1.8.0
*/

/**
* wpv_api_register_framework
*
* API function to register framework integration with Views
*
* @param $framework_id (string) Framework ID
* @param $framework_data (array) Framework data
*		@param 'name' (string) (optional) The name of the framework, will default to $framework_id
*		@param 'api_mode' (string) <function|option> The kind of framework API
*		@param 'api_handler' (string) The name of the function|option that can be used to get values from option slugs
*
* @return (boolean) True if the framework was registered, false otherwise or if it was already registered
*
* @since 1.8.0
*/

function wpv_api_register_framework( $framework_id, $framework_data ) {
	global $WP_Views_fapi;
	if ( ! isset( $WP_Views_fapi ) ) {
		return false;
	}
	return $WP_Views_fapi->register_framework( $framework_id, $framework_data );
}

/**
* WP_Views_Integration_API
*
* API class for Views framework integration
*
* @since 1.8.0
*/

class WP_Views_Integration_API {
	
	public function __construct() {
        		
		$this->framework = null;
		$this->framework_data = array();
		$this->framework_valid = false;
		$this->framework_is_autodetected = false;
		$this->framework_registered_keys = array();
		$this->framework_integration_page = null;
		
		/**
		* auto_detect_list
		*
		* List of known frameworks we offer to auto-register
		*/
		
		$this->auto_detect_list = array(
			'Options_Framework'	=> array(
				'id'			=> 'options_framework',
				'name'			=> __( 'Options Framework', 'wpv-views'  ),
				'api_mode'		=> 'function',
				'api_handler'	=> 'of_get_option',
				'link'			=> 'http://wptheming.com/options-framework-plugin'
			),
			'OT_Loader'			=> array(
				'id'			=> 'option_tree',
				'name'			=> __( 'OptionTree', 'wpv-views'  ),
				'api_mode'		=> 'function',
				'api_handler'	=> 'ot_get_option',
				'link'			=> 'https://wordpress.org/plugins/option-tree/'
			),
			/*
			'ReduxFramework'	=> array(
				'id'			=> 'redux',
				'name'			=> __( 'Redux', 'wpv-views'  ),
				'api_mode'		=> 'option',
				'api_handler'	=> 'redux_demo',
				'link'			=> 'https://reduxframework.com/'
			),
			*/
			'getCherryVersion'	=> array(// TBD
				'id'			=> 'cherry',
				'name'			=> __( 'Cherry', 'wpv-views'  ),
				'api_mode'		=> 'function',
				'api_handler'	=> 'of_get_option',
				'link'			=> 'http://www.cherryframework.com/'
			),
			'upfw_init'			=> array(// TBD
				'id'			=> 'upthemes',
				'name'			=> __( 'UpThemes', 'wpv-views'  ),
				'api_mode'		=> 'function',
				'api_handler'	=> array( $this, 'compat_upthemes_handler' ),
				'link'			=> 'https://upthemes.com/upthemes-framework/'
			)
		);
		
		/**
		* example_register
		*
		* Code example to register manually
		*/
		
		$this->example_register = "<pre><code style='display:block'>add_action( 'init', 'prefix_register_framework_in_views' );\n"
				. "function prefix_register_framework_in_views() {\n"
				. "\t" . '$framework_id = \'framework_slug\';' . "\n"
				. "\t" . '$framework_data = array(' . "\n"
				. "\t\t'name'\t\t=> '" . __( 'The framework name', 'wpv-views' ) . "',\n"
				. "\t\t'api_mode'\t=> '" . __( 'function|option', 'wpv-views' ) . "',\n"
				. "\t\t'api_handler'\t=> '" . __( 'Function name|Option ID', 'wpv-views' ) . "'\n"
				. "\t" . ');' . "\n"
				. "\t" . 'if ( function_exists( \'wpv_api_register_framework\' ) ) {' . "\n"
				. "\t\t" . 'wpv_api_register_framework( $framework_id, $framework_data );' . "\n"
				. "\t" . '}' . "\n"
				. '}'
				. "</code></pre>";
		
		/**
		* example_register
		*
		* Code example to register manually
		*/
		
		$this->example_register_key = "<pre><code style='display:block'>add_action( 'wpv_action_wpv_register_integration_keys', 'prefix_register_framework_keys' );\n"
				. "function prefix_register_framework_keys( " . '$wpv_framework' . " ) {\n"
				. "\t" . '$key = \'' . __( 'option_slug', 'wpv-views' ) . "';\n"
				. "\t" . '$key_data = array(' . "\n"
				. "\t\t'framework_id'\t=> 'framework_slug'\n"
				. "\t" . ');' . "\n"
				. "\t" . '$wpv_framework->register_key( $key, $key_data );' . "\n"
				. '}'
				. "</code></pre>";
		
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'init', array( $this, 'register_saved_auto_detected' ), 99 );
		
		add_action( 'wp_loaded', array( $this, 'wp_loaded' ) );
		
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 30 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );
		
		// Bring the frameworks shortcode into the Views GUI
		add_filter( 'wpv_filter_wpv_shortcodes_gui_data', array( $this, 'register_shortcode_in_gui' ) );
		add_filter( 'editor_addon_menus_wpv-views', array( $this, 'add_shortcode_to_editor' ), 99 );
		
		add_action( 'wp_ajax_wpv_register_auto_detected_framework', array( $this, 'wpv_register_auto_detected_framework' ) );
		add_action( 'wp_ajax_wpv_update_framework_integration_keys', array( $this, 'wpv_update_framework_integration_keys' ) );
		
		// Extend Views settings to allow for registered framework options
		add_filter( 'wpv_filter_extend_limit_options', array( $this, 'extend_view_settings_as_array_options' ), 10 );
		add_filter( 'wpv_filter_extend_offset_options', array( $this, 'extend_view_settings_as_array_options' ), 10 );
		add_filter( 'wpv_filter_extend_posts_per_page_options', array( $this, 'extend_view_settings_as_array_options' ), 10 );
		
		// Extend Views filters to allow for registered framework options
		add_filter( 'wpv_filter_extend_framework_options_for_post_author', array( $this, 'extend_view_settings_as_array_options_for_filters' ), 10 );
		add_filter( 'wpv_filter_extend_framework_options_for_post_id', array( $this, 'extend_view_settings_as_array_options_for_filters' ), 10 );
		add_filter( 'wpv_filter_extend_framework_options_for_post_relationship', array( $this, 'extend_view_settings_as_array_options_for_filters' ), 10 );
		add_filter( 'wpv_filter_extend_framework_options_for_parent', array( $this, 'extend_view_settings_as_array_options_for_filters' ), 10 );
		add_filter( 'wpv_filter_extend_framework_options_for_category', array( $this, 'extend_view_settings_as_array_options_for_filters' ), 10 );
		add_filter( 'wpv_filter_extend_framework_options_for_custom_field', array( $this, 'extend_view_settings_as_array_options_for_filters' ), 10 );
		add_filter( 'wpv_filter_extend_framework_options_for_taxonomy_term', array( $this, 'extend_view_settings_as_array_options_for_filters' ), 10 );
		add_filter( 'wpv_filter_extend_framework_options_for_users', array( $this, 'extend_view_settings_as_array_options_for_filters' ), 10 );
		add_filter( 'wpv_filter_extend_framework_options_for_usermeta_field', array( $this, 'extend_view_settings_as_array_options_for_filters' ), 10 );
		
    }
	
	/**
	* init
	*
	* Executed at init, used to register scripts/styles and shortcodes
	*
	* @since 1.8.0
	*/
	
	function init() {
		wp_register_script( 'views-framework-integration-js' , WPV_URL_EMBEDDED . '/res/js/views_framework_integration.js', array( 'jquery', 'toolset-utils', 'underscore' ), WPV_VERSION );
		$framework_translations = array(
			'warning_change' => __( "Please note that changing the registered framework will restart the registered options", 'wpv-views'),
			'wpv_close' => __( 'Close', 'wpv-views') 
		);
		wp_localize_script( 'views-framework-integration-js', 'views_framework_integration_texts', $framework_translations );
		add_shortcode( 'wpv-theme-option', array( $this, 'wpv_shortcode_wpv_theme_option' ) );
	}
	
	/**
	* register_saved_auto_detected
	*
	* Auto-register selected and stored framework late on init
	*
	* @since 1.10
	*/
	
	function register_saved_auto_detected() {
		global $WPV_settings;
		$auto_detected = $this->auto_detect();
		if ( 
			! $this->framework_valid
			&& ! empty( $auto_detected )
			&& isset( $WPV_settings->wpv_saved_auto_detected_framework )
			&& ! empty( $WPV_settings->wpv_saved_auto_detected_framework ) 
			&& in_array( $WPV_settings->wpv_saved_auto_detected_framework, $auto_detected )
		) {
			$saved_auto_detected_data = $this->auto_detect_list[$WPV_settings->wpv_saved_auto_detected_framework];
			$this->register_framework( $saved_auto_detected_data['id'], $saved_auto_detected_data );
			if ( $this->framework_valid ) {
				$this->framework_is_autodetected = true;
			}
		}
	}
	
	/**
	* wp_loaded
	*
	* @since 1.10
	*/
	
	function wp_loaded() {
		
		/**
		* Fires once WordPress has loaded, allowing keys to be registered.
		*
		* @see $this->register_key()
		*
		* @since 1.10
		*/
		
		do_action( 'wpv_action_wpv_register_integration_keys', $this );
	}
	
	/**
	* admin_menu
	*
	* Add a submenu item in the Views menu for the theme framework integration
	*
	* @since 1.8.0
	*/
	
	function admin_menu() {
		$cap = 'manage_options';
		global $WP_Views;
		if ( ! $WP_Views->is_embedded() ) {
			$this->framework_integration_page = add_submenu_page( 'views', __( 'Views Integration', 'wpv-views' ), __( 'Views Integration', 'wpv-views' ), $cap, 'views-framework-integration', array( $this, 'render_framework_integration_page' ) );
			add_action( 'load-' . $this->framework_integration_page, array( $this, 'help_tab' ) );
		}
	}
	
	/**
	* enqueue_scripts
	*
	* Enqueue the script for the Views theme framework integration
	*
	* @since 1.8.0
	*/
	
	function enqueue_scripts( $hook ) {
		if ( $hook == 'views_page_views-framework-integration' ) {
			wp_enqueue_script( 'views-framework-integration-js' );
			wp_enqueue_style( 'views-admin-css' );
		}
	}
	
	/**
	* register_shortcode_in_gui
	*
	* Register the wpv-theme-option shortcode into the Views GUI
	*
	* @since 1.10
	*/
	
	function register_shortcode_in_gui( $views_shortcodes ) {
		if ( $this->framework_valid ) {
			$views_shortcodes['wpv-theme-option'] = array(
				'callback' => array( $this, 'get_shortcode_data' )
			);
		}
		return $views_shortcodes;
	}
	
	/**
	* add_shortcode_to_editor
	*
	* Add the wpv-theme-option shortcode to the editor popup
	*
	* @since 1.10
	*/
	
	function add_shortcode_to_editor( $menu = array() ) {
		$basic = __( 'Basic', 'wpv-views' );
		$keys = $this->get_combined_framework_keys();
		if ( 
			$this->framework_valid 
			&& count( $keys ) > 0 
			&& isset( $menu[$basic] )
		) {
			$theme_option_title = __( 'Theme option', 'wpv-views' );
			$nonce = wp_create_nonce( 'wpv_editor_callback' );
			$menu[$basic][$theme_option_title] = array( 
				$theme_option_title, 
				'wpv-theme-option', 
				$basic, 
				"WPViews.shortcodes_gui.wpv_insert_popup('wpv-theme-option', '" . $theme_option_title . "', {}, '" . $nonce . "', this )"
			);
		}
		return $menu;
	}
	
	/**
	* wpv_register_auto_detected_framework
	*
	* Registers the framework selected when clicking the Save button
	*
	* @since 1.10
	*/
	
	function wpv_register_auto_detected_framework() {
		if ( ! wp_verify_nonce( $_POST['wpv_framework_integration_nonce'], 'wpv_framework_integration_nonce' ) ) {
			wp_send_json_error();
        }
		$auto_detected = $this->auto_detect();
		$framework = isset( $_POST['framework'] ) ? sanitize_text_field( $_POST['framework'] ) : '';
		if (
			empty( $framework )
			|| isset( $this->auto_detect_list[$framework] )
		) {
			global $WPV_settings;
			$WPV_settings['wpv_saved_auto_detected_framework'] = $framework;
			$WPV_settings->save();
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}
	
	/**
	* wpv_update_framework_integration_keys
	*
	* AJAX callback for saving the VIews theme framework integration settings
	*
	* @since 1.8.0
	*/
	
	function wpv_update_framework_integration_keys() {
		if ( ! wp_verify_nonce( $_POST['wpv_framework_integration_nonce'], 'wpv_framework_integration_nonce' ) ) {
            wp_send_json_error();
        }
		$fw_keys = $this->get_stored_framework_keys();
		if ( 
			isset( $_POST['update_action'] ) 
			&& isset( $_POST['update_tag'] ) 
		) {
            $update_tag = sanitize_text_field( $_POST['update_tag'] );
			switch ( $_POST['update_action'] ) {
                case 'add':
                    if ( ! in_array( $update_tag, $fw_keys ) ) {
                        $fw_keys[] = $update_tag;
                    }
                    break;
                case 'delete':
                    $key = array_search( $update_tag, $fw_keys );
                    if ( $key !== false ) {
                        unset( $fw_keys[$key] );
                    }
                    break;
            }
            $this->set_stored_framework_keys( $fw_keys );
            wp_send_json_success();
        } else {
            wp_send_json_error();
        }
		wp_send_json_error();
	}
	
	/**
	* extend_view_settings_as_array_options
	*
	* Add the framework registered options to some View settings
	*
	* @since 1.8.0
	*/
	
	function extend_view_settings_as_array_options( $options = array() ) {
		if ( $this->framework_valid ) {
			$framework_keys = $this->get_combined_framework_keys();
			foreach ( $framework_keys as $fw_key ) {
				$options['FRAME_KEY(' . $fw_key . ')'] = $this->framework_data['name'] . ': ' . $fw_key;
			}
		}
		return $options;
	}
	
	/**
	* extend_view_settings_as_array_options_for_filters
	*
	* Add the framework registered options to some View query filters
	*
	* @since 1.8.0
	*/
	
	function extend_view_settings_as_array_options_for_filters( $options = array() ) {
		if ( $this->framework_valid ) {
			$framework_keys = $this->get_combined_framework_keys();
			foreach ( $framework_keys as $fw_key ) {
				$options[$fw_key] = $this->framework_data['name'] . ': ' . $fw_key;
			}
		}
		return $options;
	}
	
	/**
	* framework_missing_message_for_filters
	*
	* Display a generic error message when a query filter should be using a framework option, but no valid framework was registered
	*
	* @param $item (string|false) (optional) The specific query filter
	* @param $show_flag (boolean) (optional) Whether to add a red flag
	*
	* @return echo (string)
	*
	* @since 1.8.0
	*/
	
	function framework_missing_message_for_filters( $item = false, $show_flag = true ) {
		echo $this->get_framework_missing_message_for_filters( $item, $show_flag );
	}
	
	/**
	* get_framework_missing_message_for_filters
	*
	* Return a generic error message when a query filter should be using a framework option, but no valid framework was registered
	*
	* @param $item (string|false) (optional) The specific query filter
	* @param $show_flag (boolean) (optional) Whether to add a red flag
	*
	* @return (string)
	*
	* @since 1.8.0
	*/
	
	function get_framework_missing_message_for_filters( $item = false, $show_flag = true ) {
		$return = '';
		if ( $show_flag ) {
			$return .= '<span class="wpv-filter-title-notice wpv-filter-title-notice-error">'
			. '<i class="icon-bookmark icon-rotate-270 icon-large" title="This filters needs some action"></i>'
			. '</span>';
		}
		if ( $item ) {
			$return .= '<strong style="color:#d54e21">' . $item . '</strong> - '
			. WPV_MESSAGE_SPACE_CHAR;
		}
		$return .= __( 'This filter should use a <strong style="color:#d54e21">Framework option</strong>, but there is no Framework registered with Views.', 'wpv-views' )
			. WPV_MESSAGE_SPACE_CHAR
			. __( 'Unless you edit it, this filter will not be applied at all.', 'wpv-views' );
		return $return;
	}
	
	/**
	* wpv_shortcode_wpv_theme_option
	*
	* Shortcode to display theme framework option values
	*
	* @param $atts array
	*     'name'		=> (mandatory)	the option name
	*     'separator'	=> (optional)	the separator for array values
	*
	* @since 1.8.1
	*/
	
	function wpv_shortcode_wpv_theme_option( $atts ) {
		extract(
			shortcode_atts(
				array(
					'name' => '',
					'separator' => ', '
				),
				$atts
			)
		);
		if ( 
			empty( $name )
			|| ! $this->framework_valid
		) {
			return '';
		}
		$value = $this->get_framework_value( $name, '' );
		if ( is_array( $value ) ) {
			return implode( $separator, $value );
		} else {
			return $value;
		}
	}
	
	/**
	* get_shortcode_data
	*
	* Get the data for the wpv-theme-option shortcode to build its GUI
	*
	* @since 1.10
	*/
	
	function get_shortcode_data() {
		$url = esc_url(
			add_query_arg(
				array( 'page' => 'views-framework-integration' ),
				admin_url( 'admin.php' )
			)
		);
		$keys = $this->get_combined_framework_keys();
		$name_options = array(
			'' => __( 'Select an option name', 'wpv-views' )
		);
		foreach ( $keys as $fw_key ) {
			$name_options[$fw_key] = $fw_key;
		}
		$data = array(
			'name' => __( 'Integrated option', 'wpv-views' ),
			'label' => __( 'Integrated option', 'wpv-views' ),
			'attributes' => array(
				'display-options' => array(
					'label' => __('Display options', 'wpv-views'),
					'header' => __('Display options:', 'wpv-views'),
					'fields' => array(
						'name' => array(
							'label' => __( 'Name', 'wpv-views'),
							'type' => 'select',
							'options' => $name_options,
							'description' => __( 'The name of the theme option to display', 'wpv-views' ),
							'documentation' => '<a href="' . $url . '" target="_blank">' . __( 'Views integration with theme options', 'wpv-views' ) . '</a>',
							'required' => true,
						),
						'separator' => array(
							'label' => __( 'Separator', 'wpv-views'),
							'type' => 'text',
							'default' => ', ',
							'description' => __( 'When that theme option holds more than one value, display this separator between them', 'wpv-views' ),
						),
					),
				),
			),
		);
		return $data;
	}
	
	/**
	* register_framework
	*
	* Register framework integration with Views
	*
	* @param $framework_id (string) Framework ID
	* @param $framework_data (array) Framework data
	*		'name' => The framework name
	*		'api_mode' => <function|option>
	*		'api_handler' => Function name|Option ID
	*
	* @return (boolean) True if the framework was registered, false if it was already registered
	*
	* @since 1.8.0
	*/
	
	function register_framework( $framework_id, $framework_data ) {
		if ( ! is_null( $this->framework ) ) {
			return false;
		} else {
			$this->framework = $framework_id;
			$this->framework_data = $framework_data;
			$this->check_framework_data( $this->framework_data );
			return $this->framework_valid;
		}
	}
	
	/**
	* check_framework_data
	*
	* Validate the data when registering a framework
	* Used to decide whether it is valid or not, setting the $framework_valid property
	*
	* @param $framework_data (array)
	*/
	
	function check_framework_data( $framework_data ) {
		if ( 
			is_array( $framework_data )
			&& ! empty( $framework_data )
		) {
			if ( ! isset( $framework_data['name'] ) ) {
				$this->framework_data['name'] = $this->framework;
			}
			if ( 
				isset( $framework_data['api_mode'] ) 
				&& isset( $framework_data['api_handler'] )
			) {
				switch ( $framework_data['api_mode'] ) {
					case 'function':
						if ( is_callable( $framework_data['api_handler'] ) ) {
							$this->framework_valid = true;
						}
						break;
					case 'option':
						$framework_options = get_option( $framework_data['api_handler'], array() );
						if ( is_array( $framework_options ) ) {
							$this->framework_valid = true;
						}
						break;
				}
			}
		} else {
			$this->framework_data = array(
				'name' => __( 'Unknown framework', 'wpv-views' )
			);
		}
	}
	
	/**
	* register_key
	*
	* API method to register key in PHP
	*
	* @since 1.10
	*/
	
	function register_key( $id, $args = array() ) {
		if ( $this->framework_valid ) {
			if ( ! isset( $args['framework_id'] ) ) {
				$args['framework_id'] = $this->framework;
			}
			if ( $args['framework_id'] == $this->framework ) {
				if ( ! isset( $this->framework_registered_keys[$this->framework] ) ) {
					$this->framework_registered_keys[$this->framework] = array();
				}
				$this->framework_registered_keys[$this->framework][] = sanitize_text_field( $id );
			}
			
		}
	}
	
	/**
	* help_tab
	*
	*
	* @since 1.10
	*/
	
	function help_tab() {
		$screen = get_current_screen();
		if ( $screen->id != $this->framework_integration_page ) {
			return;
		}
		$screen->add_help_tab(
			array(
				'id'		=> 'wpv_framework_integration_help_tab_register_framework',
				'title'		=> __( 'Register a framework', 'wpv-views' ),
				'content'	=> '<p>'
					. __( '<strong>Views</strong> will auto-detect the most used frameworks and, if they are available on your site, you will be able to register one of them with a single click.', 'wpv-views' )
					. '</p>'
					. '<p>'
					. sprintf(
						__( 'However, you can also integrate a framework within Views using <a href="%s" target="_blank">our API</a>:', 'wpv-views' ),
						WPV_LINK_FRAMEWORK_INTEGRATION_DOCUMENTATION
						)
					. $this->example_register
					. '</p>'
					. '<p>'
					. __( 'Once you have integrated your framework, you need to register the options that should be available for use as Views settings.', 'wpv-views' )
					. '</p>'
			)
		);
		$screen->add_help_tab(
			array(
				'id'		=> 'wpv_framework_integration_help_tab_register_option',
				'title'		=> __( 'Register framework options', 'wpv-views' ),
				'content'	=> '<p>'
					. __( 'After integrating a framework, you need to register the options that should be available for use as View settings.', 'wpv-views' )
					. '</p>'
					. '<p>'
					. sprintf(
						__( 'You can use the provided form to add or remove registered options. In addition, you can also use <a href="%s" target="_blank">our API</a>:', 'wpv-views' ),
						WPV_LINK_FRAMEWORK_INTEGRATION_DOCUMENTATION
						)
					. $this->example_register_key
					. '</p>'
					. '<p>'
					. __( 'Once you have registered some options, you will be able to use them in different View settings, like the <em>limit</em> or the <em>offset</em>.', 'wpv-views' )
					. '</p>'
			)
		);
		$auto_detect_help_tab = '';
		foreach ( $this->auto_detect_list as $auto_detected_fw ) {
			$auto_detect_help_tab .= '<li>'
				. sprintf(
						__('%s - <code>framework_slug: %s</code> - <a href="%s" target="_blank">documentation</a>', 'wpv-views' ),
						$auto_detected_fw['name'],
						$auto_detected_fw['id'],
						$auto_detected_fw['link']
					)
				. '</li>';
		}
		$screen->add_help_tab(
			array(
				'id'		=> 'wpv_framework_integration_help_tab_auto_detect',
				'title'		=> __( 'Auto-detected frameworks', 'wpv-views' ),
				'content'	=> '<p>'
					. __( '<strong>Views</strong> can auto-detect whether one of the following frameworks is installed on your site and register it with just a single click:', 'wpv-views' )
					. '</p>'
					. '<ul>'
					. $auto_detect_help_tab
					. '</ul>'
			)
		);
	}
	
	/**
	* framework_integration_page
	*
	* Render the admin page for Views theme framework integration
	*
	* @since 1.8.0
	*/
	
	function render_framework_integration_page() {
		?>
		<div class="wrap">
			<h2><?php _e( 'Views integration', 'wpv-views' ); ?></h2>
			<?php
			if ( $this->framework ) {
				if ( $this->framework_valid ) {
					echo '<div class="wpv-setting-container wpv-add-item-settings">'
						. $this->get_registered_framework_management_structure()
						. '</div>';
					$this->display_auto_supported_selection();
				} else {
					echo '<div class="wpv-setting-container">'
						. '<div class="wpv-settings-header">'
						. '<h3>'
						. __( 'Something went wrong', 'wpv-views' )
						. '</h3>'
						. '</div>'
						. '<div class="wpv-setting">'
						. '<p>'
						. __( 'Your framework was not correctly registered. Remember that you need to register your framework as follows:', 'wpv-views' )
						. '</p>';
						echo $this->example_register;
					echo '</div>';
					echo '</div>';
					//$this->display_auto_supported_selection();
				}
			} else {
				$this->display_auto_supported_selection();
				echo '<div class="wpv-setting-container">'
					. '<div class="wpv-settings-header">'
					. '<h3>'
					. __( 'Register your framework manually', 'wpv-views' )
					. '</h3>'
					. '</div>'
					. '<div class="wpv-setting">'
					. '<p>'
					. __( 'You can register your framework manually as follows:', 'wpv-views' )
					. '</p>';
					echo $this->example_register;
				echo '</div>';
				echo '</div>';
			}
			?>
			<div class="wpv-setting-container">
				<p class="toolset-alert toolset-alert-info">
					<?php 
					echo sprintf(
						__( 'For details, check the <a href="%s" title="Documentation for Views theme framework integration">documentation page</a>', 'wpv-views' ),
						WPV_LINK_FRAMEWORK_INTEGRATION_DOCUMENTATION
					); 
					?>
				</p>
			</div>
			<?php 
			wp_nonce_field( 'wpv_framework_integration_nonce', 'wpv_framework_integration_nonce' ); 
			?>
		</div>
		<?php
	}
	
	/**
	* auto_detect
	*
	* Check for familiar frameworks and offer them for registration.
	*
	* @since 1.10
	*/
	
	function auto_detect() {
		$auto_detected = array();
		foreach ( $this->auto_detect_list as $thiz_present => $thiz_data ) {
			if ( 
				function_exists( $thiz_present ) 
				|| class_exists( $thiz_present )
			) {
				$auto_detected[] = $thiz_present;
			}
		}
		return $auto_detected;
	}
	
	/**
	* display_auto_supported_selection
	*
	* Display the section to select one supported framework.
	*
	* @note Only offer to select one when there is no registered framework OR the one registered is one of the auto-detectable
	* @note Checks the registered one
	* @note Disables the ones not detected
	*
	* @since 1.10
	*/
	
	function display_auto_supported_selection() {
		global $WPV_settings;
		$saved_auto_detected = $WPV_settings->wpv_saved_auto_detected_framework;
		$auto_detected = $this->auto_detect();
		if (
			! $this->framework_valid
			|| $this->framework_is_autodetected
		) {
		?>
		<div class="wpv-setting-container js-wpv-framework-auto-detect-selection">
			<div class="wpv-settings-header">
				<h3><?php _e( 'Autodetected frameworks', 'wpv-views' ); ?></h3>
			</div>
			<div class="wpv-setting">
				<div class="wpv-advanced-setting">
					<p><?php _e( 'We have detected the following frameworks on your site:', 'wpv-views' ); ?></p>
					<ul>
						<li>
							<input type="radio" name="wpv-framework-auto" <?php checked( $saved_auto_detected, '' ); ?> id="wpv-framework-auto" class="js-wpv-framework-auto" value="" autocomplete="off" />
							<label for="wpv-framework-auto"><?php _e( 'Do not register any framework automatically', 'wpv-views' ); ?></label>
						</li>
						<?php
						foreach ( $this->auto_detect_list as $auto_detect_key => $auto_detect_offer ) {
							?>
							<li>
								<input type="radio" name="wpv-framework-auto" <?php checked( $saved_auto_detected, $auto_detect_key ); ?> id="wpv-framework-auto-<?php echo $auto_detect_offer['id']; ?>" <?php disabled( ! in_array( $auto_detect_key , $auto_detected ) ); ?> class="js-wpv-framework-auto" value="<?php echo $auto_detect_key; ?>" autocomplete="off" />
								<label for="wpv-framework-auto-<?php echo $auto_detect_offer['id']; ?>"><?php echo $auto_detect_offer['name']; ?></label>
								- <a href="<?php echo esc_url( $auto_detect_offer['link'] ); ?>" target="_blank">
								<?php 
								echo sprintf(
									__( 'Check the details for %s', 'wpv-views' ),
									$auto_detect_offer['name']
								); 
								?>
								</a>
							</li>
							<?php
						}
						?>
					</ul>
					<p class="update-button-wrap">
						<span class="js-wpv-message-container"></span>
						<button id="js-wpv-framework-auto-save" class="button button-secondary" disabled="disabled"><?php _e( 'Save', 'wpv-views' ); ?></button>
					</p>
				</div>
			</div>
		</div>
		<?php
		}
	}
	
	/**
	* get_registered_framework_management_structure
	*
	* Display the option keys management section for the registered framework.
	*
	* @since 1.10
	*/
	
	function get_registered_framework_management_structure() {
		$return = '';
		if ( $this->framework_valid ) {
			$framework_data = $this->framework_data;
			$framework_registered_keys = $this->get_stored_framework_keys();
			ob_start();
			?>
			<div class="wpv-settings-header">
				<h3><?php echo $this->framework_data['name']; ?></h3>
			</div>
			<div class="wpv-setting">
				<div class="wpv-advanced-setting">
					<p>
						<?php
						if ( $this->framework_is_autodetected ) {
							echo sprintf( __( 'You have selected the <strong>%s</strong> options framework to be used with Views.', 'wpv-views' ), $framework_data['name'] );
						} else {
							echo sprintf( __( 'Your theme has registered the <strong>%s</strong> options framework to be used with Views.', 'wpv-views' ), $framework_data['name'] );
						}
						?>
					</p>
					<?php
					if (
						isset( $this->framework_registered_keys[$this->framework] ) 
						&& ! empty( $this->framework_registered_keys[$this->framework] )
					) {
						echo '<h4>'
							. __( 'Auto-registered options', 'wpv-views' )
							. '</h4>'
							. '<p>'
							. __( 'Those options were registered using the Views Integration API.', 'wpv-views' )
							. '</p>'
							. '<ul class="wpv-taglike-list">';
						foreach( $this->framework_registered_keys[$this->framework] as $reg_key ) {
							echo '<li>'
								. $reg_key
								. '</li>';
						}
						echo '</ul>';
					}
					?>
					<h4><?php _e( 'Declare theme options', 'wpv-views' ); ?></h4>
					<p>
						<?php
						_e( 'You can use the theme options as a source of values in several settings of Views.', 'wpv-views' );
						echo WPV_MESSAGE_SPACE_CHAR;
						_e( 'To do that, you need to declare here which theme options should be available to be used inside Views.', 'wpv-views' );
						?>
					</p>
					<p>
						<?php
						_e( 'Use the form below to register options.', 'wpv-views' );
						echo WPV_MESSAGE_SPACE_CHAR;
						_e( 'Also, note that you can delete options that no longer need to be available.', 'wpv-views' );
						?>
					</p>
					<div class="js-wpv-add-item-settings-wrapper">
						<ul class="wpv-taglike-list js-wpv-add-item-settings-list js-custom-shortcode-list">
							<?php
							foreach ( $framework_registered_keys as $fw_key ) {
									?>
									<li class="js-<?php echo $fw_key; ?>-item">
										<span class=""><?php echo $fw_key; ?></span>
										<i class="icon-remove-sign js-wpv-framework-slug-delete" data-target="<?php echo esc_attr( $fw_key ); ?>"></i>
									</li>
									<?php
								}
							?>
						</ul>
						<form class="js-wpv-add-item-settings-form js-wpv-framework-integration-form-add">
							<input type="text" placeholder="<?php _e( 'Option slug', 'wpv-views' ); ?>" class="js-wpv-add-item-settings-form-newname js-wpv-framework-integration-newname" />
							<button class="button button-secondary js-wpv-add-item-settings-form-button js-wpv-framework-slug-add" type="button" disabled><i class="icon-plus"></i> <?php _e( 'Add', 'wpv-views' ); ?></button>
							<span class="toolset-alert toolset-alert-error hidden js-wpv-cs-error"><?php _e( 'Only latin letters, numbers, underscores and dashes', 'wpv-views' ); ?></span>
							<span class="toolset-alert toolset-alert-info hidden js-wpv-cs-dup"><?php _e( 'That option was already declared', 'wpv-views' ); ?></span>
							<span class="toolset-alert toolset-alert-info hidden js-wpv-cs-ajaxfail"><?php _e( 'An error ocurred', 'wpv-views' ); ?></span>
						</form>
					</div>
				</div>
			</div>
			<?php
			$return = ob_get_clean();
		}
		return $return;
	}
	
	/**
	* get_stored_framework_keys
	*
	* Get framework keys registered in Views
	*
	* @return (array)
	*
	* @since 1.8.0
	*/
	
	function get_stored_framework_keys() {
		$return = array();
		if ( is_null( $this->framework ) ) {
			return $return;
		}
		global $WPV_settings;
		if ( 
			isset( $WPV_settings->wpv_framework_keys )
			&& is_array( $WPV_settings->wpv_framework_keys )
			&& isset( $WPV_settings->wpv_framework_keys[$this->framework] ) 
			&& is_array( $WPV_settings->wpv_framework_keys[$this->framework] ) 
		) {
			$return = $WPV_settings->wpv_framework_keys[$this->framework];
		}
		
		/**
		* wpv_filter_get_stored_framework_keys
		*
		* Filter to include or exclude specific or special registered framework keys
		*
		* @param $return 			(array) 	Existing registered framework keys
		* @param $this 	(string) 	ID of the currently registered framework
		*
		* @since 1.8.0
		*/
		
		$return = apply_filters( 'wpv_filter_get_stored_framework_keys', $return, $this );
		return $return;
	}
	
	/**
	* get_registered_framework_keys
	*
	* Get framework keys registered in PHP
	*
	* @return array
	*
	* @since 1.10
	*/
	
	function get_registered_framework_keys() {
		$return = array();
		if ( $this->framework_valid ) {
			if (
				isset( $this->framework_registered_keys[$this->framework] ) 
				&& ! empty( $this->framework_registered_keys[$this->framework] )
			) {
				$return = $this->framework_registered_keys[$this->framework];
			}
		}
		return $return;
	}
	
	/**
	* get_combined_framework_keys
	*
	* Get framework keys registered in Views and by PHP
	*
	* @return (array)
	*
	* @since 1.10
	*/
	
	function get_combined_framework_keys() {
		$return = array();
		if ( is_null( $this->framework ) ) {
			return $return;
		}
		$stored = $this->get_stored_framework_keys();
		$registered = $this->get_registered_framework_keys();
		
		$return = array_merge( $stored, $registered );
		$return = array_unique( $return );
		
		/**
		* wpv_filter_get_combined_framework_keys
		*
		* Filter to include or exclude specific or special registered framework keys
		*
		* @param $return 			(array) 	Existing registered framework keys
		* @param $this 	(string) 	ID of the currently registered framework
		*
		* @since 1.8.0
		*/
		
		$return = apply_filters( 'wpv_filter_get_combined_framework_keys', $return, $this );
		return $return;
	}
	
	/**
	* set_stored_framework_keys
	*
	* Set framework keys registered in Views
	*
	* @param $fw_keys (array) Keys to register
	*
	* @since 1.8.0
	*/
	
	function set_stored_framework_keys( $fw_keys = array() ) {
		if ( is_null( $this->framework ) ) {
			return;
		}
        
		global $WPV_settings;
		if ( 
			isset( $WPV_settings->wpv_framework_keys )
			&& is_array( $WPV_settings->wpv_framework_keys )
		) {
			$wpv_framework_settings = $WPV_settings->wpv_framework_keys;
		} else {
			$wpv_framework_settings = array();
		}
		$wpv_framework_settings[$this->framework] = $fw_keys;
		$WPV_settings->wpv_framework_keys = $wpv_framework_settings;
		$WPV_settings->save();
	}
	
	/**
	* get_framework_value
	*
	* Get the value given a registered framework key
	*
	* @param $key (string) The key
	* @param $default (mixed) The value to return when there is no valid framework registered
	*
	* @return $return (mixed)
	*
	* @since 1.8.0
	*/
	
	function get_framework_value( $key, $return ) {
		if ( $this->framework_valid ) {
			$framework_keys = $this->get_combined_framework_keys();
			if ( in_array( $key, $framework_keys ) ) {
				switch ( $this->framework_data['api_mode'] ) {
					case 'function':
						$fw_name = $this->framework_data['api_handler'];
						$return = call_user_func( $fw_name, $key );
						break;
					case 'option':
						$framework_options = get_option( $this->framework_data['api_handler'], array() );
						if ( isset( $framework_options[$key] ) ) {
							$return = $framework_options[$key];
						}
						break;
					case 'setting':
						
						break;
				}
			}
		}
		return $return;
	}
	
	/**
	* --------------------------------
	* Compatibility
	* --------------------------------
	*/
	
	/**
	* compat_upthemes_handler
	*
	* Wrapper to get values from the UpThemes framework
	*
	* @since 1.10
	*/
	
	function compat_upthemes_handler( $name ) {
		$return = '';
		if (
			function_exists( 'upfw_init' )
			&& function_exists( 'upfw_get_options' )
		) {
			$wpv_up_options = upfw_get_options();
			$wpv_up_options_array = ( array ) $wpv_up_options;
			if ( isset( $wpv_up_options_array[$name] ) ) {
				$return = $wpv_up_options_array[$name];
			}
		}
		return $return;
	}
	
}

global $WP_Views_fapi;
$WP_Views_fapi = new WP_Views_Integration_API();