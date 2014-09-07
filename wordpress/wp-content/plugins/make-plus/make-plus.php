<?php
/**
 * Plugin Name: Make Plus
 * Plugin URI:  https://thethemefoundry.com/wordpress-themes/make/
 * Description: A powerful paid companion plugin for the Make WordPress theme.
 * Author:      The Theme Foundry
 * Version:     1.3.3
 * Author URI:  https://thethemefoundry.com
 */

if ( ! class_exists( 'TTFMP_App' ) ) :
/**
 * Collector for builder sections.
 *
 * @since 1.0.0.
 *
 * Class TTFMP_App
 */
class TTFMP_App {
	/**
	 * Current plugin version.
	 *
	 * @since 1.0.0
	 *
	 * @var   string    The semantically versioned plugin version number.
	 */
	var $version = '1.3.3';

	/**
	 * File path to the plugin dir (e.g., /var/www/mysite/wp-content/plugins/make-plus).
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    Path to the root of this plugin.
	 */
	var $root_dir = '';

	/**
	 * File path to the plugin main file (e.g., /var/www/mysite/wp-content/plugins/make-plus/make-plus.php).
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    Path to the plugin's main file.
	 */
	var $file_path = '';

	/**
	 * Path to the component directory (e.g., /var/www/mysite/wp-content/plugins/make-plus/components).
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    Path to the component directory
	 */
	var $component_base = '';

	/**
	 * The name for the components dir.
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    The components dir string.
	 */
	var $component_dir_name = 'components';

	/**
	 * Path to the shared functions directory (e.g., /var/www/mysite/wp-content/plugins/make-plus/shared).
	 *
	 * @since 1.2.0.
	 *
	 * @var   string    Path to the component directory
	 */
	var $shared_base = '';

	/**
	 * The name for the shared functions dir.
	 *
	 * @since 1.2.0.
	 *
	 * @var   string    The shared functions dir string.
	 */
	var $shared_dir_name = 'shared';

	/**
	 * The URI base for the plugin (e.g., http://domain.com/wp-content/plugins/make-plus).
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    The URI base for the plugin.
	 */
	var $url_base = '';

	/**
	 * The one instance of TTFMP_App.
	 *
	 * @since 1.0.0.
	 *
	 * @var   TTFMP_App
	 */
	private static $instance;

	/**
	 * Instantiate or return the one TTFMP_App instance.
	 *
	 * @since  1.0.0.
	 *
	 * @return TTFMP_App
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Create a new section.
	 *
	 * @since  1.0.0.
	 *
	 * @return TTFMP_App
	 */
	public function __construct() {
		// Set the main paths for the plugin
		$this->root_dir       = dirname( __FILE__ );
		$this->file_path      = $this->root_dir . '/' . basename( __FILE__ );
		$this->component_base = $this->root_dir . '/' . $this->component_dir_name;
		$this->shared_base    = $this->root_dir . '/' . $this->shared_dir_name;
		$this->url_base       = untrailingslashit( plugins_url( '/', __FILE__ ) );

		// Load in the updater
		if ( file_exists( $this->root_dir . '/updater/updater.php' ) ) {
			require_once $this->root_dir . '/updater/updater.php';
		}

		// Load compatibility helpers
		if ( file_exists( $this->root_dir . '/shared/compatibility.php' ) ) {
			require_once $this->root_dir . '/shared/compatibility.php';
		}

		// Load the components
		add_action( 'after_setup_theme', array( $this, 'load_components' ) );

		// Load shared functions
		add_action( 'after_setup_theme', array( $this, 'load_shared_functions' ) );

		// General purpose setup action
		add_action( 'init', array( $this, 'init' ) );

		// Set the updater values
		add_filter( 'ttf_updater_config', array( $this, 'updater_config' ) );
	}

	/**
	 * Bootstrapper function to load in the components.
	 *
	 * @since  1.0.0.
	 *
	 * @return void.
	 */
	public function load_components() {
		// Load shared components
		if ( is_admin() ) {
			require_once $this->root_dir . '/shared/class-reporting.php';
		}

		// Assumes that component is located at '/components/slug/slug.php'
		$components = array(
			'customizer'  => array(
				'slug'       => 'customizer',
				'conditions' => array(
					defined( 'TTFMAKE_VERSION' ) && true === version_compare( TTFMAKE_VERSION, '1.0.4', '>=' ), // Make sure the theme is >= 1.0.4
				)
			),
			'duplicator'  => array(
				'slug'       => 'duplicator',
				'conditions' => array(
					defined( 'TTFMAKE_VERSION' ) && true === version_compare( TTFMAKE_VERSION, '1.0.6', '>=' ), // Make sure the theme is >= 1.0.6
				)
			),
			'edd' => array(
				'slug'       => 'edd',
				'conditions' => array(
					defined( 'TTFMAKE_VERSION' ) && true === version_compare( TTFMAKE_VERSION, '1.0.6', '>=' ),
					defined( 'EDD_VERSION' ) && true === version_compare( EDD_VERSION, '2.0', '>=' ),
				)
			),
			'per-page'    => array(
				'slug'       => 'per-page',
				'conditions' => array(
					defined( 'TTFMAKE_VERSION' ) && true === version_compare( TTFMAKE_VERSION, '1.0.4', '>=' ), // Make sure the theme is >= 1.0.4
				)
			),
			'post-list'    => array(
				'slug'       => 'post-list',
				'conditions' => array(
					defined( 'TTFMAKE_VERSION' ) && true === version_compare( TTFMAKE_VERSION, '1.0.7', '>=' ), // Make sure the theme is >= 1.0.7
				)
			),
			'quick-start' => array(
				'slug'       => 'quick-start',
				'conditions' => array(
					defined( 'TTFMAKE_VERSION' ) && true === version_compare( TTFMAKE_VERSION, '1.0.4', '>=' ), // Make sure the theme is >= 1.0.4
					is_admin(),
				)
			),
			'style-kits' => array(
				'slug' => 'style-kits',
				'conditions' => array(
					defined( 'TTFMAKE_VERSION' ) && true === version_compare( TTFMAKE_VERSION, '1.0.6', '>=' ) , // Make sure the theme is >= 1.0.4
				)
			),
			'text-column-layout' => array(
				'slug'       => 'text-column-layout',
				'conditions' => array(
					defined( 'TTFMAKE_VERSION' ) && true === version_compare( TTFMAKE_VERSION, '1.2.0', '>=' ), // Make sure the theme is >= 1.2.0
					function_exists( 'ttfmake_get_section_data' ),
				)
			),
			'typekit'     => array(
				'slug'       => 'typekit',
				'conditions' => array(
					defined( 'TTFMAKE_VERSION' ) && true === version_compare( TTFMAKE_VERSION, '1.0.4', '>=' ), // Make sure the theme is >= 1.0.4
				)
			),
			'widget-area' => array(
				'slug'       => 'widget-area',
				'conditions' => array(
					defined( 'TTFMAKE_VERSION' ) && true === version_compare( TTFMAKE_VERSION, '1.0.4', '>=' ), // Make sure the theme is >= 1.0.4
				)
			),
			'woocommerce' => array(
				'slug'       => 'woocommerce',
				'conditions' => array(
					defined( 'TTFMAKE_VERSION' ) && true === version_compare( TTFMAKE_VERSION, '1.0.4', '>=' ), // Make sure the theme is >= 1.0.4
					in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ), // Make sure WooCommerce is installed and activated
				)
			),
		);

		foreach ( $components as $id => $component ) {
			if ( ! in_array( false, $component['conditions'] ) ) {
				$file = $this->component_base . '/' . $component['slug'] . '/' . $component['slug'] . '.php';

				if ( file_exists( $file ) ) {
					require_once $file;
				}
			}
		}
	}

	/**
	 * Load necessary shared functions.
	 *
	 * @since 1.2.0.
	 *
	 * @return void
	 */
	public function load_shared_functions() {
		// Shop settings
		if ( current_theme_supports( 'ttfmp-shop-layout-shop' ) || current_theme_supports( 'ttfmp-shop-layout-product' ) || current_theme_supports( 'ttfmp-shop-sidebar' ) || current_theme_supports( 'ttfmp-shop-color-highlight' ) ) {
			$file = $this->shared_base . '/shop-settings/shop-settings.php';
			if ( file_exists( $file ) ) {
				require_once $file;
			}
		}
	}

	/**
	 * General purpose init function.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function init() {
		load_plugin_textdomain( 'make-plus', null, $this->root_dir . '/languages/' );
	}

	/**
	 * Register values for the updater.
	 *
	 * @since  1.3.0.
	 *
	 * @param  array    $values    The present updater values.
	 * @return array               Modified updater values.
	 */
	public function updater_config( $values ) {
		return array(
			'slug'            => 'make-plus',
			'type'            => 'plugin',
			'file'            => plugin_basename( __FILE__ ),
			'current_version' => $this->version,
		);
	}
}
endif;

if ( ! function_exists( 'ttfmp_get_app' ) ) :
/**
 * Instantiate or return the one TTFMP_App instance.
 *
 * @since  1.0.0.
 *
 * @return TTFMP_App
 */
function ttfmp_get_app() {
	return TTFMP_App::instance();
}
endif;

ttfmp_get_app();
