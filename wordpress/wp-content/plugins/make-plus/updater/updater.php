<?php

if ( ! class_exists( 'TTF_Updater' ) ) :
/**
 * Base class for kicking off updater functionality.
 *
 * @since 1.0.0.
 */
class TTF_Updater {
	/**
	 * The config values for the updater.
	 *
	 * @since 1.0.0.
	 *
	 * @var   array    The values to configure the updater.
	 */
	var $config = array();

	/**
	 * The URL to get product information.
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    The URL to get information for products.
	 */
	var $feed = 'https://thethemefoundry.com/product/feed';

	/**
	 * The one instance of TTF_Updater.
	 *
	 * @since  1.0.0.
	 *
	 * @var    TTF_Updater
	 */
	private static $instance;

	/**
	 * Instantiate or return the one TTF_Updater instance.
	 *
	 * @since  1.0.0.
	 *
	 * @return TTF_Updater
	 */
	public static function instance() {
		if ( is_null( self::$instance ) )
			self::$instance = new self();

		return self::$instance;
	}

	/**
	 * Initiate the actions.
	 *
	 * @since  1.0.0.
	 *
	 * @return TTF_Updater
	 */
	public function __construct() {
		include untrailingslashit( dirname( __FILE__ ) ) . '/api-key.php';

		// Setup the updater config
		add_action( 'after_setup_theme', array( $this, 'updater_config' ) );

		// Allow manual update checks to be performed
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'pre_set_site_transient_update_plugins' ) );
		add_filter( 'pre_set_site_transient_update_themes',  array( $this, 'pre_set_site_transient_update_themes' ) );

		// Override the requests for the plugin and theme APIs
		add_filter( 'plugins_api', array( $this, 'plugins_api' ), 10, 3 );

		// Reject update if API key is not available
		add_action( 'admin_init', array( $this, 'redirect_if_no_key' ) );

		// Pass header value to request
		add_filter( 'http_request_args', array( $this, 'http_request_args' ), 20, 2 );
	}

	/**
	 * Set the config values.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function updater_config() {
		$this->config = apply_filters( 'ttf_updater_config', array() );
	}

	/**
	 * Perform manual checks on non-wp.org plugins.
	 *
	 * @since  1.0.0.
	 *
	 * @param  object    $update    The plugin check update object.
	 * @return object               The modified plugin check update object.
	 */
	public function pre_set_site_transient_update_plugins( $update ) {
		if (
			isset( $update->response ) &&
			isset( $this->config['file'] ) &&
			isset( $this->config['slug'] ) &&
			isset( $this->config['current_version'] ) &&
			( isset( $this->config['type'] ) && 'plugin' === $this->config['type'] )
		) {
			unset( $update->response[ $this->config['file'] ] );

			// Query the feed for data about the plugin or theme
			$request = wp_remote_get( $this->feed );

			if ( 200 !== (int) wp_remote_retrieve_response_code( $request ) ) {
				return $update;
			}

			$data = (array) json_decode( wp_remote_retrieve_body( $request ) );

			// Make sure the desired plugin or theme is available
			if ( ! isset( $data[ $this->config['slug'] ] ) ) {
				return $update;
			}

			$this_data = $data[ $this->config['slug'] ];

			// Set the appropriate values in the object
			if ( isset( $update->checked ) ) {
				$update->checked[ $this->config['file'] ] = $this_data->version;
			}

			if ( 1 === version_compare( $this_data->version, $this->config['current_version'] ) ) {
				$update->response[ $this->config['file'] ]              = new stdClass();
				$update->response[ $this->config['file'] ]->new_version = $this_data->version;
				$update->response[ $this->config['file'] ]->url         = $this_data->homepage;
				$update->response[ $this->config['file'] ]->slug        = $this->config['slug'];
				$update->response[ $this->config['file'] ]->plugin      = $this->config['file'];
				$update->response[ $this->config['file'] ]->package     = $this->get_download_link();
			}
		}

		return $update;
	}

	/**
	 * Perform manual checks on non-wp.org themes.
	 *
	 * @since  1.0.0.
	 *
	 * @param  object    $update    The theme check update object.
	 * @return object               The modified theme check update object.
	 */
	public function pre_set_site_transient_update_themes( $update ) {
		if (
			isset( $update->response ) &&
			isset( $this->config['slug'] ) &&
			isset( $this->config['current_version'] ) &&
			( isset( $this->config['type'] ) && 'theme' === $this->config['type'] )
		) {
			unset( $update->response[ $this->config['slug'] ] );

			// Query the feed for data about the plugin or theme
			$request = wp_remote_get( $this->feed );

			if ( 200 !== (int) wp_remote_retrieve_response_code( $request ) ) {
				return $update;
			}

			$data = (array) json_decode( wp_remote_retrieve_body( $request ) );

			// Make sure the desired plugin or theme is available
			if ( ! isset( $data[ $this->config['slug'] ] ) ) {
				return $update;
			}

			$this_data = $data[ $this->config['slug'] ];

			// Set the appropriate values in the object
			if ( isset( $update->checked ) ) {
				$update->checked[ $this->config['slug'] ] = $this_data->version;
			}

			if ( 1 === version_compare( $this_data->version, $this->config['current_version'] ) ) {
				$update->response[ $this->config['slug'] ] = array(
					'theme'       => $this->config['slug'],
					'new_version' => $this_data->version,
					'url'         => $this_data->homepage,
					'package'     => $this->get_download_link(),
				);
			}
		}

		return $update;
	}

	/**
	 * Override the standard plugins API request when updating a certain plugin.
	 *
	 * @since  1.0.0.
	 *
	 * @param  bool                    $default    False allows the default request. Non-false value cancels the default request.
	 * @param  string                  $action     The plugins API request being made. Represents the info being requested.
	 * @param  object                  $plugin     Plugin Info API object.
	 * @return bool|WP_Error|object                False to do nothing; WP_Error if something goes wrong; object if providing other information.
	 */
	public function plugins_api( $default, $action, $plugin ) {
		if ( ( 'plugin_information' !== $action ) || ! isset( $plugin->slug ) || ( $this->config['slug'] !== $plugin->slug ) ) {
			return $default;
		}

		// Query the feed for data about the plugin or theme
		$request = wp_remote_get( $this->feed );

		if ( 200 !== (int) wp_remote_retrieve_response_code( $request ) ) {
			return $default;
		}

		$data = (array) json_decode( wp_remote_retrieve_body( $request ) );

		// Make sure the desired plugin or theme is available
		if ( ! isset( $data[ $this->config['slug'] ] ) ) {
			return $default;
		}

		// Grab the data
		$plugin = $data[ $this->config['slug'] ];

		// Convert object to array
		$plugin->sections = (array) $plugin->sections;

		// Append the download link
		$plugin->download_link = $this->get_download_link();
		return $plugin;
	}

	/**
	 * Get the download link for the plugin or theme package.
	 *
	 * @since  1.0.0.
	 *
	 * @return string    The URL for the download link.
	 */
	public function get_download_link() {
		if ( isset( $this->config['type'] ) && isset( $this->config['slug'] ) ) {
			return 'https://thethemefoundry.com/download/' . $this->config['type'] . '/item/' . $this->config['slug'] . '/';
		} else {
			return '';
		}
	}

	/**
	 * If the user has no API key, redirect to authenticate.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function redirect_if_no_key() {
		global $pagenow;

		// Plugin conditions
		$single_plugin_update   = ( 'update.php' === $pagenow && isset( $_GET['action'] ) && 'upgrade-plugin' === $_GET['action'] && isset( $this->config['file'] ) && isset( $_GET['plugin'] ) && $this->config['file'] === $_GET['plugin'] );
		$multiple_plugin_update = ( 'update-core.php' === $pagenow && isset( $_GET['action'] ) && 'do-plugin-upgrade' === $_GET['action'] && isset( $_POST['checked'] ) && isset( $this->config['file'] ) && in_array( $this->config['file'], $_POST['checked'] ) );

		// Theme conditions
		$single_theme_update   = ( 'update.php' === $pagenow && isset( $_GET['action'] ) && 'upgrade-theme' === $_GET['action'] && isset( $_GET['theme'] ) && $this->config['slug'] === $_GET['theme'] );
		$multiple_theme_update = ( 'update-core.php' === $pagenow && isset( $_GET['action'] ) && 'do-theme-upgrade' === $_GET['action'] && isset( $_POST['checked'] ) && isset( $this->config['slug'] ) && in_array( $this->config['slug'], $_POST['checked'] ) );

		// Only redirect when in the correct context
		if ( false === $single_plugin_update && false === $multiple_plugin_update && false === $single_theme_update && false === $multiple_theme_update ) {
			return;
		}

		// Don't redirect if the API key is set
		if ( false !== get_option( 'ttf-api-key', false ) ) {
			return;
		}

		// Generate the redirect based on the type
		if ( 'plugin' === $this->config['type'] ) {
			$redirect = add_query_arg(
				array(
					'action' => $_GET['action'],
					'plugin' => $this->config['file'],
					'page'   => 'updater_auth_page',
				),
				admin_url( 'tools.php' )
			);

			// Redirect to the auth page
			wp_safe_redirect( $redirect );
			exit();
		} else if ( 'theme' === $this->config['type'] ) {
			$redirect = add_query_arg(
				array(
					'action' => $_GET['action'],
					'theme'  => $this->config['slug'],
					'page'   => 'updater_auth_page',
				),
				admin_url( 'tools.php' )
			);

			// Redirect to the auth page
			wp_safe_redirect( $redirect );
			exit();
		}
	}

	/**
	 * Append API key to request as a header.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array     $args    The default args.
	 * @param  string    $url     URL for the request.
	 * @return array              Modified args.
	 */
	public function http_request_args( $args, $url ) {
		if ( $url === $this->get_download_link() ) {
			$args['headers']['X-TTF-API-Key'] = get_option( 'ttf-api-key', '' );
		}

		return $args;
	}
}
endif;

if ( ! function_exists( 'ttf_get_updater' ) ) :
/**
 * Return the TTF_Updater instance.
 *
 * @since  1.0.0.
 *
 * @return TTF_Updater
 */
function ttf_get_updater() {
	return TTF_Updater::instance();
}
endif;

ttf_get_updater();