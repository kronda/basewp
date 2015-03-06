<?php
/**
 * @package Make Plus
 */

if ( ! class_exists( 'TTFMP_WooCommerce' ) ) :
/**
 * Bootstrap the enhanced WooCommerce functionality.
 *
 * @since 1.0.0.
 */
class TTFMP_WooCommerce {
	/**
	 * Name of the component.
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    The name of the component.
	 */
	var $component_slug = 'woocommerce';

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
	 * The version of the WooCommerce plugin.
	 *
	 * @since 1.5.0.
	 *
	 * @var    int    The version of the WooCommerce plugin.
	 */
	var $wc_version = 0;

	/**
	 * WooCommerce Colors plugin indicator flag.
	 *
	 * @since 1.5.0.
	 *
	 * @var    bool    True if WooCommerce Colors plugin is active.
	 */
	var $colors_plugin = false;

	/**
	 * The one instance of TTFMP_WooCommerce.
	 *
	 * @since 1.0.0.
	 *
	 * @var   TTFMP_WooCommerce
	 */
	private static $instance;

	/**
	 * Instantiate or return the one TTFMP_WooCommerce instance.
	 *
	 * @since  1.0.0.
	 *
	 * @return TTFMP_WooCommerce
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
	 * @return TTFMP_WooCommerce
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
		// Passive mode
		if ( true === ttfmp_get_app()->passive ) {
			// Include needed files
			require_once $this->component_root . '/class-shortcode.php';
		}
		// Active mode
		else {
			// Detect WooCommerce plugin version
			if ( defined( 'WC_VERSION' ) ) {
				$this->wc_version = WC_VERSION;
			}

			// Detect WooCommerce Colors plugin
			$this->colors_plugin = in_array( 'woocommerce-colors/woocommerce-colors.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );

			// Include needed files
			require_once $this->component_root . '/class-section-definitions.php';
			require_once $this->component_root . '/class-shortcode.php';

			// Enqueue scripts and styles
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// Define shop and product views
			add_filter( 'ttfmake_get_view', array( $this, 'get_view' ), 10, 2 );
			add_filter( 'ttfmp_perpage_view', array( $this, 'perpage_view' ), 10, 2 );

			// Add Customizer section descriptions
			add_filter( 'ttfmp_shop_layout_shop_description', array( $this, 'layout_shop_description' ) );
			add_filter( 'ttfmp_shop_layout_product_description', array( $this, 'layout_product_description' ) );

			// Handle color with the WC Color plugin
			if ( true === $this->colors_plugin ) {
				add_action( 'customize_register', array( $this, 'wc_colors_customizer_mod' ), 30 );
			}
			// Handle color for WC < 2.3
			else if ( version_compare( $this->wc_version, '2.3', '<' ) ) {
				require_once $this->component_root . '/legacy/class-legacy-color.php';
			}

			// Add support for Shop Settings
			$this->add_support();

			// Admin notices
			add_action( 'admin_init', array( $this, 'admin_notice' ) );
		}
	}

	/**
	 * Enqueue styles and scripts
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		// Styles
		if ( version_compare( $this->wc_version, '2.3', '>=' ) ) {
			wp_enqueue_style(
				'ttfmp-woocommerce',
				trailingslashit( $this->url_base ) . 'css/woocommerce.css',
				array( 'woocommerce-general', 'woocommerce-smallscreen', 'woocommerce-layout' ),
				ttfmp_get_app()->version
			);
		} else {
			wp_enqueue_style(
				'ttfmp-woocommerce-legacy',
				trailingslashit( $this->url_base ) . 'legacy/woocommerce.css',
				array( 'woocommerce-general', 'woocommerce-smallscreen', 'woocommerce-layout' ),
				ttfmp_get_app()->version
			);
		}
	}

	/**
	 * Modify the WooCommerce color section added by the WooCommerce Colors plugin.
	 *
	 * @since 1.5.0.
	 *
	 * @param $wp_customize
	 */
	public function wc_colors_customizer_mod( $wp_customize ) {
		$panel_id = 'ttfmake_color-scheme';
		$panel = $wp_customize->get_panel( $panel_id );
		$section_id = 'woocommerce_colors';
		$section = $wp_customize->get_section( $section_id );

		// Move the WooCommerce section to the Colors panel
		$section->panel = 'ttfmake_color-scheme';
		$section->priority = (int) $panel->priority + 95;
	}

	/**
	 * Filter to identify views related to WooCommerce
	 *
	 * This assumes two WooCommerce views: a "Shop" view that includes product archives
	 * and other shop utility pages such as Checkout, and a "Product" view which is just
	 * individual products.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $view                The current view.
	 * @param  string    $parent_post_type    The post type of the parent of the current post.
	 * @return string
	 */
	public function get_view( $view, $parent_post_type ) {
		// Product attachments
		if ( is_attachment() && 'product' === $parent_post_type ) {
			$view = 'product';
		}
		// Shop pages
		else if ( is_shop() || is_product_category() || is_product_tag() || is_cart() || is_checkout() ) {
			$view = 'shop';
		}
		// Single products
		else if ( is_product() ) {
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
		if ( $post->ID === get_option( 'woocommerce_cart_page_id' ) || $post->ID === get_option( 'woocommerce_checkout_page_id' ) ) {
			$view = 'shop';
		} else if ( 'product' === $post->post_type ) {
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
		$description = __( 'For WooCommerce, this view consists of product archives and other shop utility pages such as Checkout.', 'make-plus' );

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
		$description = __( 'For WooCommerce, this view consists of single products.', 'make-plus' );

		if ( '' !== $text ) {
			$text .= ' ';
		}

		return $text . $description;
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
		if ( version_compare( $this->wc_version, '2.3', '<' ) ) {
			add_theme_support( 'ttfmp-shop-color-highlight' );
		}
	}

	/**
	 * Add relevant admin notices.
	 *
	 * @since 1.5.0.
	 *
	 * @return void
	 */
	public function admin_notice() {
		if ( ! function_exists( 'ttfmake_register_admin_notice' ) ) {
			return;
		}

		if ( version_compare( $this->wc_version, '2.3', '>=' ) && false === $this->colors_plugin ) {
			ttfmake_register_admin_notice(
				'woocommerce-23-no-color-plugin',
				sprintf(
					__( 'Make\'s color scheme no longer applies to WooCommerce shop elements. Please install the %s plugin to customize your shop\'s colors.', 'make-plus' ),
					sprintf(
						'<a href="%1$s">%2$s</a>',
						esc_url( 'https://wordpress.org/plugins/woocommerce-colors/' ),
						__( 'WooCommerce Colors', 'make-plus' )
					)
				),
				array(
					'cap'    => 'update_plugins',
					'screen' => array( 'index.php', 'plugins.php' ),
					'type'   => 'warning',
				)
			);
		}
	}
}
endif;

if ( ! function_exists( 'ttfmp_get_woocommerce' ) ) :
/**
 * Instantiate or return the one TTFMP_WooCommerce instance.
 *
 * @since  1.0.0.
 *
 * @return TTFMP_WooCommerce
 */
function ttfmp_get_woocommerce() {
	return TTFMP_WooCommerce::instance();
}
endif;

ttfmp_get_woocommerce()->init();
