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
* WP_Views_framework_api
*
* API class for Views framework integration
*
* @since 1.8.0
*/

class WP_Views_framework_api {
	
	public function __construct() {
        		
		$this->framework = null;
		$this->framework_data = array();
		$this->framework_valid = false;
		
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 30 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );
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
	* Executed at init, used to register scripts/styles
	*
	* @since 1.8.0
	*/
	
	function init() {
		wp_register_script( 'views-framework-integration-js' , WPV_URL_EMBEDDED . '/res/js/views_framework_integration.js', array( 'jquery' ), WPV_VERSION );
	}
	
	/**
	* admin_menu
	*
	* Add a submenu item in the Views menu for the theme framework integration
	*
	* @since 1.8.0
	*/
	
	function admin_menu() {
		if ( ! is_null( $this->framework ) ) {
			$cap = 'manage_options';
			global $WP_Views;
			if ( ! $WP_Views->is_embedded() ) {
				add_submenu_page( 'views', __( 'Framework', 'wpv-views' ), __( 'Framework', 'wpv-views' ), $cap, 'views-framework-integration', array( $this, 'framework_integration_page' ) );
			}
		}
	}
	
	/**
	* framework_integration_page
	*
	* Render the admin page for Views theme framework integration
	*
	* @since 1.8.0
	*/
	
	function framework_integration_page() {
		?>
		<div class="wrap">
			<h2><?php _e( 'Views integration', 'wpv-views' ); ?></h2>
			<div class="wpv-setting-container wpv-add-item-settings">
			<?php
			if ( $this->framework_valid ) {
				$framework_data = $this->framework_data;
				$framework_registered_keys = $this->get_framework_keys();
				?>
				<div class="wpv-settings-header">
					<h3><?php echo $this->framework_data['name']; ?></h3>
				</div>
				<div class="wpv-setting">
					<h4><?php _e( 'Declare theme options', 'wpv-views' ); ?></h4>
					<div class="wpv-advanced-setting">
						<p>
							<?php
							echo sprintf( __( 'Your theme has registered the <strong>%s</strong> options framework to be used with Views.', 'wpv-views' ), $framework_data['name'] );
							?>
						</p>
						<p>
							<?php
							_e( 'This means that you can use the theme options as a source of values in several settings of Views.', 'wpv-views' );
							echo WPV_MESSAGE_SPACE_CHAR;
							_e( 'Before you can do that, you need to declare here which theme options should be available to be used inside Views.', 'wpv-views' );
							?>
						</p>
						<p>
							<?php
							_e( 'Simply use the form below to declare options.', 'wpv-views' );
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
								<span class="toolset-alert toolset-alert-error hidden js-wpv-cs-error"><?php _e( 'Only letters, numbers, underscores and dashes', 'wpv-views' ); ?></span>
								<span class="toolset-alert toolset-alert-info hidden js-wpv-cs-dup"><?php _e( 'That option was already declared', 'wpv-views' ); ?></span>
								<span class="toolset-alert toolset-alert-info hidden js-wpv-cs-ajaxfail"><?php _e( 'An error ocurred', 'wpv-views' ); ?></span>
							</form>
						</div>
						<?php wp_nonce_field( 'wpv_framework_integration_nonce', 'wpv_framework_integration_nonce' ); ?>
					</div>
				</div>
				<?php
			} else {
				?>
				<div class="error">
				<p><?php _e( 'Your framework was not correctly registered. Remember that you need to register your framework as follows:', 'wpv-views' ); ?></p>
				<pre><code style="display:block"><?php echo 'add_action( \'init\', \'prefix_register_framework_in_views\' )' . "\n"
				. 'function prefix_register_framework_in_views() {' . "\n"
				. "\t" . '$framework_id = \'framework_slug\';' . "\n"
				. "\t" . '$framework_data = array(' . "\n"
				. "\t\t" . '\'name\' => \'' . __( 'The framework name', 'wpv-views' ) . '\',' . "\n"
				. "\t\t" . '\'api_mode\' => \'' . __( 'function|option', 'wpv-views' ) . '\',' . "\n"
				. "\t\t" . '\'api_handler\' => \'' . __( 'Function name|Option ID', 'wpv-views' ) . '\'' . "\n"
				. "\t" . ')' . "\n"
				. "\t" . 'if ( function_exists( \'wpv_api_register_framework\' ) ) {' . "\n"
				. "\t\t" . 'wpv_api_register_framework( $framework_id, $framework_data );' . "\n"
				. "\t" . '}' . "\n"
				. '}';
				?></code></pre>
				<p><?php echo sprintf(
					__( 'For details, check the <a href="%s" title="Documentation for Views theme framework integration">documentation page</a>', 'wpv-views' ),
					'http://www.google.es'
				); ?></p>
				</div>
				<?php
			}
			?>
			</div>
		</div>
		<?php
	}
	
	/**
	* enqueue_scripts
	*
	* Enqueue the script for the Views theme framework integration
	*
	* @since 1.8.0
	*/
	
	function enqueue_scripts( $hook ) {
		if ( ! is_null( $this->framework ) ) {
			if ( $hook == 'views_page_views-framework-integration' ) {
				wp_enqueue_script( 'views-framework-integration-js' );
				wp_enqueue_style( 'views-admin-css' );
			}
		}
	}
	
	/**
	* wpv_update_framework_integration_keys
	*
	* AJAX callback for saving the VIews theme framework integration settings
	*
	* @since 1.8.9
	*/
	
	function wpv_update_framework_integration_keys() {
		if ( ! wp_verify_nonce( $_POST['wpv_framework_integration_nonce'], 'wpv_framework_integration_nonce' ) ) {
            die( "Security check" );
        }
		$fw_keys = $this->get_framework_keys();
		if ( 
			isset( $_POST['csaction'] ) 
			&& isset( $_POST['cstarget'] ) 
		) {
            switch ( $_POST['csaction'] ) {
                case 'add':
                    if ( !in_array( $_POST['cstarget'], $fw_keys ) ) {
                        $fw_keys[] = $_POST['cstarget'];
                    }
                    break;
                case 'delete':
                    $key = array_search( $_POST['cstarget'], $fw_keys );
                    if ( $key !== false ) {
                        unset( $fw_keys[$key] );
                    }
                    break;
            }
            $this->set_framework_keys( $fw_keys );
            echo 'ok';
        } else {
            echo 'error';
        }
		die();
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
						if ( function_exists( $framework_data['api_handler'] ) ) {
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
	* extend_view_settings_as_array_options
	*
	* Add the framework registered options to some View settings
	*
	* @since 1.8.0
	*/
	
	function extend_view_settings_as_array_options( $options = array() ) {
		if ( $this->framework_valid ) {
			$framework_keys = $this->get_framework_keys();
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
			$framework_keys = $this->get_framework_keys();
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
	* get_framework_keys
	*
	* Get framework keys registered in Views
	*
	* @return (array)
	*
	* @since 1.8.0
	*/
	
	function get_framework_keys() {
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
		* wpv_filter_get_framework_keys
		*
		* Filter to include or exclude specific or special registered framework keys
		*
		* @param $return (array) Existing registered framerowk keys
		* @param $this->framework (string) ID of the currently registered framework
		*
		* @since 1.8.0
		*/
		
		$return = apply_filters( 'wpv_filter_get_framework_keys', $return, $this->framework );
		return $return;
	}
	
	/**
	* set_framework_keys
	*
	* Set framework keys registered in Views
	*
	* @param $fw_keys (array) Keys to register
	*
	* @since 1.8.0
	*/
	
	function set_framework_keys( $fw_keys = array() ) {
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
			$framework_keys = $this->get_framework_keys();
			if ( in_array( $key, $framework_keys ) ) {
				switch ( $this->framework_data['api_mode'] ) {
					case 'function':
						$fw_name = $this->framework_data['api_handler'];
						$return = call_user_func( $fw_name, $key );
						break;
					case 'option':
						$framework_options = get_option( $framework_data['api_handler'], array() );
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
	
}

global $WP_Views_fapi;
$WP_Views_fapi = new WP_Views_framework_api();