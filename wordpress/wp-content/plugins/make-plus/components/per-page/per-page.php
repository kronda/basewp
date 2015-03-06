<?php
/**
 * @package Make Plus
 */

if ( ! class_exists( 'TTFMP_PerPage' ) ) :
/**
 * Bootstrap the per-page options functionality.
 *
 * @since 1.0.0.
 */
class TTFMP_PerPage {
	/**
	 * Name of the component.
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    The name of the component.
	 */
	var $component_slug = 'per-page';

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
	 * Namespacing prefix.
	 *
	 * @since  1.0.0.
	 *
	 * @var    string    Prefix for name strings.
	 */
	var $prefix = 'ttfmp_per-page_';

	/**
	 * The one instance of TTFMP_PerPage.
	 *
	 * @since 1.0.0.
	 *
	 * @var   TTFMP_PerPage
	 */
	private static $instance;

	/**
	 * Instantiate or return the one TTFMP_PerPage instance.
	 *
	 * @since  1.0.0.
	 *
	 * @return TTFMP_PerPage
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Bootstrap the module
	 *
	 * @since  1.0.0.
	 *
	 * @return TTFMP_PerPage
	 */
	public function __construct() {
		// Set the main paths for the component
		$this->component_root = ttfmp_get_app()->component_base . '/' . $this->component_slug;
		$this->file_path      = $this->component_root . '/' . basename( __FILE__ );
		$this->url_base       = untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Initialize the components of the module
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function init() {
		// Load components
		require_once $this->component_root . '/class-metabox.php';
		require_once $this->component_root . '/class-options.php';

		// Override applicable global settings
		if ( ! is_admin() ) {
			add_action( 'wp', array( $this, 'add_mod_filters' ) );
		}
	}

	/**
	 * Utility to determine the view type of a post.
	 *
	 * Based on the different views used in the Layout sections of the Customizer.
	 *
	 * @since  1.0.0.
	 *
	 * @param  object    $post    The post to determine the view type of
	 * @return string
	 */
	public function get_view( $post ) {
		if ( $post->ID === get_option( 'woocommerce_cart_page_id' ) || $post->ID === get_option( 'woocommerce_checkout_page_id' ) ) {
			$view = 'shop';
		} else if ( 'product' === $post->post_type ) {
			$view = 'product';
		} else if ( 'page' === $post->post_type ) {
			$view = 'page';
		} else {
			$view = 'post';
		}

		return apply_filters( 'ttfmp_perpage_view', $view, $post );
	}

	/**
	 * Add the setting overrides for the current view.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function add_mod_filters() {
		global $post;
		$view = ttfmake_get_view();

		// Only do this for certain views.
		if ( is_object( $post ) && in_array( $view, array( 'post', 'page', 'product', 'shop' ) ) ) {
			if ( $settings = get_post_meta( $post->ID, $this->prefix . 'settings', true ) ) {
				foreach ( (array) $settings as $key => $value ) {
					$mod_key = 'layout-' . $view . '-' . $key;
					add_filter( 'theme_mod_' . $mod_key, array( $this, 'filter_mod' ) );
				}
			}
		}
	}

	/**
	 * Filter a theme mod to override its value.
	 *
	 * @since  1.0.0.
	 *
	 * @param  mixed    $value    The original value of the theme mod
	 * @return mixed              The modified value of the theme mod
	 */
	public function filter_mod( $value ) {
		global $post;
		$view = ttfmake_get_view();

		// Reverse-engineer the setting key from the filter
		$filter = current_filter();
		$mod_key = str_replace( 'theme_mod_', '', $filter );
		$key = preg_replace( '/layout\-[^\-]+\-/', '', $mod_key );

		// Override the value if it exists in the post meta
		if ( $settings = get_post_meta( $post->ID, $this->prefix . 'settings', true ) ) {
			$value = ttfmp_get_perpage_options()->sanitize_post_meta( $key, $settings[$key], $view );
		}

		return $value;
	}
}
endif;

if ( ! function_exists( 'ttfmp_get_perpage' ) ) :
/**
 * Instantiate or return the one TTFMP_PerPage instance.
 *
 * @since  1.0.0.
 *
 * @return TTFMP_PerPage
 */
function ttfmp_get_perpage() {
	return TTFMP_PerPage::instance();
}
endif;

ttfmp_get_perpage()->init();
