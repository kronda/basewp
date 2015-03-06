<?php
/**
 * @package Make Plus
 */

if ( ! class_exists( 'TTFMP_Duplicator' ) ) :
/**
 * Bootstrap all of the content/section sharing features.
 *
 * @since 1.1.0.
 */
class TTFMP_Duplicator {
	/**
	 * Name of the component.
	 *
	 * @since 1.1.0.
	 *
	 * @var   string    The name of the component.
	 */
	var $component_slug = 'duplicator';

	/**
	 * Path to the component directory (e.g., /var/www/mysite/wp-content/plugins/make-plus/components/my-component).
	 *
	 * @since 1.1.0.
	 *
	 * @var   string    Path to the component directory
	 */
	var $component_root = '';

	/**
	 * File path to the plugin main file (e.g., /var/www/mysite/wp-content/plugins/make-plus/components/my-component/my-component.php).
	 *
	 * @since 1.1.0.
	 *
	 * @var   string    Path to the plugin's main file.
	 */
	var $file_path = '';

	/**
	 * The URI base for the plugin (e.g., http://domain.com/wp-content/plugins/make-plus/my-component).
	 *
	 * @since 1.1.0.
	 *
	 * @var   string    The URI base for the plugin.
	 */
	var $url_base = '';

	/**
	 * The one instance of TTFMP_Duplicator.
	 *
	 * @since 1.1.0.
	 *
	 * @var   TTFMP_Duplicator
	 */
	private static $instance;

	/**
	 * Instantiate or return the one TTFMP_Duplicator instance.
	 *
	 * @since  1.1.0.
	 *
	 * @return TTFMP_Duplicator
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
	 * @since  1.1.0.
	 *
	 * @return TTFMP_Duplicator
	 */
	public function __construct() {
		// Set the main paths for the component
		$this->component_root = ttfmp_get_app()->component_base . '/' . $this->component_slug;
		$this->file_path      = $this->component_root . '/' . basename( __FILE__ );
		$this->url_base       = untrailingslashit( plugins_url( '/', __FILE__ ) );

		// Include needed files
		require_once $this->component_root . '/page.php';

		// The section component requires new components of Make 1.0.9
		if ( defined( 'TTFMAKE_VERSION' ) && true === version_compare( TTFMAKE_VERSION, '1.0.9', '>=' ) ) {
			require_once $this->component_root . '/section.php';
		}
	}
}
endif;

if ( ! function_exists( 'ttfmp_get_duplicator' ) ) :
/**
 * Instantiate or return the one TTFMP_Duplicator instance.
 *
 * @since  1.1.0.
 *
 * @return TTFMP_Duplicator
 */
function ttfmp_get_duplicator() {
	return TTFMP_Duplicator::instance();
}
endif;

ttfmp_get_duplicator();