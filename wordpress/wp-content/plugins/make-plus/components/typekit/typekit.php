<?php
/**
 * @package Make Plus
 */

if ( ! class_exists( 'TTFMP_Typekit' ) ) :
/**
 * Bootstrap the Typekit functionality.
 *
 * @since 1.0.0.
 */
class TTFMP_Typekit {
	/**
	 * Name of the component.
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    The name of the component.
	 */
	var $component_slug = 'typekit';

	/**
	 * Path to the component directory (e.g., /var/www/mysite/wp-content/plugins/make-plus/components/my-component).
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    Path to the component directory
	 */
	var $component_root = '';

	/**
	 * File path to the plugin main file (e.g., /var/www/mysite/wp-content/plugins/make-plus/components/my-component/my-component.php).
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    Path to the plugin's main file.
	 */
	var $file_path = '';

	/**
	 * The URI base for the plugin (e.g., http://domain.com/wp-content/plugins/make-plus/my-component).
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    The URI base for the plugin.
	 */
	var $url_base = '';

	/**
	 * The one instance of TTFMP_Typekit.
	 *
	 * @since 1.0.0.
	 *
	 * @var   TTFMP_Typekit
	 */
	private static $instance;

	/**
	 * Instantiate or return the one TTFMP_Typekit instance.
	 *
	 * @since  1.0.0.
	 *
	 * @return TTFMP_Typekit
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
	 * @return TTFMP_Typekit
	 */
	public function __construct() {
		// Set the main paths for the component
		$this->component_root = ttfmp_get_app()->component_base . '/' . $this->component_slug;
		$this->file_path      = $this->component_root . '/' . basename( __FILE__ );
		$this->url_base       = untrailingslashit( plugins_url( '/', __FILE__ ) );

		// Include needed files
		require_once $this->component_root . '/customizer.php';
	}
}
endif;

if ( ! function_exists( 'ttfmp_get_typekit' ) ) :
/**
 * Instantiate or return the one TTFMP_Typekit instance.
 *
 * @since  1.0.0.
 *
 * @return TTFMP_Typekit
 */
function ttfmp_get_typekit() {
	return TTFMP_Typekit::instance();
}
endif;

ttfmp_get_typekit();