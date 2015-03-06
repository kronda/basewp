<?php
/**
 * @package Make Plus
 */

if ( ! class_exists( 'TTFMP_EDD' ) ) :
/**
 * Bootstrap the enhanced WooCommerce functionality.
 *
 * @since 1.1.0.
 */
class TTFMP_EDD {
	/**
	 * Name of the component.
	 *
	 * @since 1.1.0.
	 *
	 * @var   string    The name of the component.
	 */
	var $component_slug = 'edd';

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
	 * The one instance of TTFMP_EDD.
	 *
	 * @since 1.1.0.
	 *
	 * @var   TTFMP_EDD
	 */
	private static $instance;

	/**
	 * Instantiate or return the one TTFMP_EDD instance.
	 *
	 * @since  1.1.0.
	 *
	 * @return TTFMP_EDD
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
	 * @since  1.1.0.
	 *
	 * @return TTFMP_EDD
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
	 * @since  1.1.0.
	 *
	 * @return void
	 */
	public function init() {
		// Include needed files
		require_once $this->component_root . '/class-section-definitions.php';
		require_once $this->component_root . '/color.php';

		// Enqueue scripts and styles
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Add template path
		add_filter( 'edd_template_paths', array( $this, 'filter_template_paths' ) );

		// Filter the download item class
		add_filter( 'edd_download_class', array( $this, 'edd_download_class' ), 10, 4 );

		// Show featured images on Shop and Product views
		add_filter( 'theme_mod_layout-shop-featured-images', array( $this, 'mod_value' ) );
		add_filter( 'theme_mod_layout-product-featured-images', array( $this, 'mod_value' ) );

		// Hide author on Shop and Product views
		add_filter( 'theme_mod_layout-shop-post-author', array( $this, 'mod_value' ) );
		add_filter( 'theme_mod_layout-product-post-author', array( $this, 'mod_value' ) );

		// Define shop and product views
		add_filter( 'ttfmake_get_view', array( $this, 'get_view' ), 10, 2 );
		add_filter( 'ttfmp_perpage_view', array( $this, 'perpage_view' ), 10, 2 );

		// Add Customizer section descriptions
		add_filter( 'ttfmp_shop_layout_shop_description', array( $this, 'layout_shop_description' ) );
		add_filter( 'ttfmp_shop_layout_product_description', array( $this, 'layout_product_description' ) );

		// Add description for Highlight Color control
		add_filter( 'ttfmp_color_highlight_description', array( $this, 'color_highlight_description' ) );

		// Add support for Shop Settings
		$this->add_support();
	}

	/**
	 * Enqueue styles and scripts
	 *
	 * @since  1.1.0.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		// Styles
		wp_enqueue_style(
			'ttfmp-edd',
			trailingslashit( $this->url_base ) . 'css/edd.css',
			array( 'edd-styles' ),
			ttfmp_get_app()->version
		);
	}

	/**
	 * Add the plugin module path as the first path to search for EDD template files.
	 *
	 * @since 1.1.0.
	 *
	 * @param  array    $file_paths    The original array of file paths.
	 * @return array                   The modified array of file paths.
	 */
	public function filter_template_paths( $file_paths ) {
		$new_path = array( trailingslashit( $this->component_root ) . 'templates' );
		return array_merge( $new_path, $file_paths );
	}

	/**
	 * Add additional download item classes.
	 *
	 * @since 1.2.0.
	 *
	 * @param  string    $class    The download item classes.
	 * @param  int       $id       The download post ID.
	 * @param  array     $atts     The shortcode atts.
	 * @param  int       $i        The output counter.
	 * @return string              The modified download item classes.
	 */
	public function edd_download_class( $class, $id, $atts, $i ) {
		if ( ! isset( $atts['columns'] ) || 1 == $atts['columns'] || is_null( $i ) ) {
			return $class;
		}

		if ( 0 === (int) $i % absint( $atts['columns'] ) ) {
			$class .= ' last';
		}

		return $class;
	}

	/**
	 * Filter to identify views related to EDD.
	 *
	 * This assumes two EDD views: a "Shop" view that includes download post type archives and
	 * download category and tag archives, and a "Product" view which is just individual downloads.
	 *
	 * @since  1.2.0.
	 *
	 * @param  string    $view                The current view.
	 * @param  string    $parent_post_type    The post type of the parent of the current post.
	 * @return string
	 */
	public function get_view( $view, $parent_post_type ) {
		// Product attachments
		if ( is_attachment() && 'download' === $parent_post_type ) {
			$view = 'product';
		}
		// Shop pages
		else if ( is_post_type_archive( 'download' ) || is_tax( 'download_category' ) || is_tax( 'download_tag' ) ) {
			$view = 'shop';
		}
		// Single products
		else if ( 'download' === get_post_type() ) {
			$view = 'product';
		}

		return $view;
	}

	/**
	 * Filter to identify per-page views related to EDD.
	 *
	 * @since 1.2.0.
	 *
	 * @param $view
	 * @param $post
	 * @return string
	 */
	public function perpage_view( $view, $post ) {
		if ( 'download' === $post->post_type ) {
			$view = 'product';
		}

		return $view;
	}

	/**
	 * Add a description to the Layout: Shop section.
	 *
	 * @since 1.2.0.
	 *
	 * @param  string $text
	 * @return string
	 */
	public function layout_shop_description( $text ) {
		$description = __( 'For Easy Digital Downloads, this view consists of download archives and related category and tag archives.', 'make-plus' );

		if ( '' !== $text ) {
			$text .= ' ';
		}

		return $text . $description;
	}

	/**
	 * Add a description to the Layout: Product section.
	 *
	 * @since 1.2.0.
	 *
	 * @param  string $text
	 * @return string
	 */
	public function layout_product_description( $text ) {
		$description = __( 'For Easy Digital Downloads, this view consists of single downloads.', 'make-plus' );

		if ( '' !== $text ) {
			$text .= ' ';
		}

		return $text . $description;
	}

	/**
	 * Return a specific value depending on the current filter.
	 *
	 * @since 1.2.0.
	 *
	 * @param  mixed     $value    The current value of the setting.
	 * @return string              The modified value of the setting.
	 */
	public function mod_value( $value ) {
		$filter = current_filter();

		// Featured Image
		if ( 'theme_mod_layout-shop-featured-images' === $filter ) {
			return 'thumbnail';
		}
		if ( 'theme_mod_layout-product-featured-images' === $filter ) {
			return 'post-header';
		}

		// Post Author
		if ( 'theme_mod_layout-shop-post-author' === $filter || 'theme_mod_layout-product-post-author' === $filter ) {
			return 'none';
		}

		return $value;
	}

	/**
	 * Add support for various features in the shared Shop Settings module.
	 *
	 * @since 1.2.0.
	 *
	 * @return void
	 */
	public function add_support() {
		// Layout: Shop
		add_theme_support( 'ttfmp-shop-layout-shop' );

		// Layout: Product
		add_theme_support( 'ttfmp-shop-layout-product' );

		// Shop Sidebar
		add_theme_support( 'ttfmp-shop-sidebar', array( 'shop', 'product', 'page' ) );

		// Highlight color
		add_theme_support( 'ttfmp-shop-color-highlight' );
	}

	/**
	 * Add a description to the Highlight Color control.
	 *
	 * @since 1.5.0.
	 *
	 * @param $text
	 *
	 * @return string
	 */
	public function color_highlight_description( $text ) {
		$description = __( 'For Easy Digital Downloads, used for prices and alerts.', 'make-plus' );

		if ( '' !== $text ) {
			$text .= ' ';
		}

		return $text . $description;
	}
}
endif;

if ( ! function_exists( 'ttfmp_get_edd' ) ) :
/**
 * Instantiate or return the one TTFMP_EDD instance.
 *
 * @since  1.1.0.
 *
 * @return TTFMP_EDD
 */
function ttfmp_get_edd() {
	return TTFMP_EDD::instance();
}
endif;

ttfmp_get_edd()->init();
