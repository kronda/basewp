<?php
/**
 * @package Make Plus
 */

if ( ! class_exists( 'TTFMP_Shop_Settings' ) ) :
/**
 * Class TTFMP_Shop_Settings
 *
 * @since 1.2.0.
 */
class TTFMP_Shop_Settings {
	/**
	 * Name of the component.
	 *
	 * @since 1.2.0.
	 *
	 * @var   string    The name of the component.
	 */
	var $component_slug = 'shop-settings';

	/**
	 * Path to the component directory (e.g., /var/www/mysite/wp-content/plugins/make-plus/components/my-component).
	 *
	 * @since 1.2.0.
	 *
	 * @var   string    Path to the component directory
	 */
	var $component_root = '';

	/**
	 * File path to the plugin main file (e.g., /var/www/mysite/wp-content/plugins/make-plus/components/my-component/my-component.php).
	 *
	 * @since 1.2.0.
	 *
	 * @var   string    Path to the plugin's main file.
	 */
	var $file_path = '';

	/**
	 * The URI base for the plugin (e.g., http://domain.com/wp-content/plugins/make-plus/my-component).
	 *
	 * @since 1.2.0.
	 *
	 * @var   string    The URI base for the plugin.
	 */
	var $url_base = '';

	/**
	 * The one instance of TTFMP_Shop_Settings.
	 *
	 * @since 1.2.0.
	 *
	 * @var   TTFMP_Shop_Settings
	 */
	private static $instance;

	/**
	 * Instantiate or return the one TTFMP_Shop_Settings instance.
	 *
	 * @since  1.2.0.
	 *
	 * @return TTFMP_Shop_Settings
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
	 * @since  1.2.0.
	 *
	 * @return TTFMP_Shop_Settings
	 */
	public function __construct() {
		// Set the main paths for the component
		$this->component_root = ttfmp_get_app()->shared_base . '/' . $this->component_slug;
		$this->file_path      = $this->component_root . '/' . basename( __FILE__ );
		$this->url_base       = untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Initialize the components of the module
	 *
	 * @since  1.2.0.
	 *
	 * @return void
	 */
	public function init() {
		// Check for theme support
		add_action( 'after_setup_theme', array( $this, 'check_theme_support' ), 99 );

		// Detect and update old settings
		if ( true === $this->has_old_settings() ) {
			$this->update_settings();
		}
	}

	/**
	 * Check for theme support after Make Plus modules have had a chance to load.
	 *
	 * @since 1.5.0.
	 *
	 * @return void
	 */
	public function check_theme_support() {
		// Layout: Shop & Layout: Product
		if ( current_theme_supports( 'ttfmp-shop-layout-shop' ) || current_theme_supports( 'ttfmp-shop-layout-product' ) ) {
			if ( ttfmake_customizer_supports_panels() && function_exists( 'ttfmake_customizer_add_panels' ) ) {
				add_filter( 'make_customizer_sections', array( $this, 'customizer_sections' ), 20 );
			} else {
				add_filter( 'ttfmake_customizer_sections', array( $this, 'legacy_customizer_sections' ) );
			}

			if ( current_theme_supports( 'ttfmp-shop-layout-shop' ) ) {
				add_filter( 'ttfmake_setting_defaults', array( $this, 'layout_shop_setting_defaults' ) );
			}
			if ( current_theme_supports( 'ttfmp-shop-layout-product' ) ) {
				add_filter( 'ttfmake_setting_defaults', array( $this, 'layout_product_setting_defaults' ) );
			}
		}

		// Shop Sidebar
		if ( false !== current_theme_supports( 'ttfmp-shop-sidebar' ) ) {
			require_once $this->component_root . '/class-shop-sidebar.php';
		}

		// Highlight Color
		if ( current_theme_supports( 'ttfmp-shop-color-highlight' ) ) {
			add_action( 'customize_register', array( $this, 'color_highlight' ), 20 );
			add_filter( 'ttfmake_setting_defaults', array( $this, 'color_highlight_setting_default' ) );
		}
	}

	/**
	 * Filter to add the Layout: Shop section and/or the Layout: Product section to the Customizer.
	 *
	 * @since  1.3.3.
	 *
	 * @param  array    $sections    The array of sections to add to the Customizer.
	 * @return array                 The modified array of sections.
	 */
	public function customizer_sections( $sections ) {
		$panel = 'ttfmake_content-layout';
		$theme_prefix = 'ttfmake_';

		/**
		 * Shop
		 */
		$prefix = 'layout-shop-';
		$sections['layout-shop'] = array(
			'panel' => $panel,
			'title' => __( 'Shop', 'make-plus' ),
			'description' => ttfmake_sanitize_text( apply_filters( 'ttfmp_shop_layout_shop_description', '' ) ),
			'options' => ( function_exists( 'ttfmake_customizer_layout_region_group_definitions' ) ) ?
				ttfmake_customizer_layout_region_group_definitions( 'shop' ) :
				array(
				$prefix . 'sidebars-heading' => array(
					'control' => array(
						'control_type'		=> 'TTFMAKE_Customize_Misc_Control',
						'type'				=> 'heading',
						'label'				=> __( 'Header, Footer, Sidebars', 'make' ),
					),
				),
				$prefix . 'hide-header' => array(
					'setting' => array(
						'sanitize_callback'	=> 'absint',
					),
					'control' => array(
						'label'				=> __( 'Hide site header', 'make' ),
						'type'				=> 'checkbox',
					),
				),
				$prefix . 'hide-footer' => array(
					'setting' => array(
						'sanitize_callback'	=> 'absint',
					),
					'control' => array(
						'label'				=> __( 'Hide site footer', 'make' ),
						'type'				=> 'checkbox',
					),
				),
				$prefix . 'sidebar-left' => array(
					'setting' => array(
						'sanitize_callback'	=> 'absint',
					),
					'control' => array(
						'label'				=> __( 'Show left sidebar', 'make' ),
						'type'				=> 'checkbox',
					),
				),
				$prefix . 'sidebar-right' => array(
					'setting' => array(
						'sanitize_callback'	=> 'absint',
					),
					'control' => array(
						'label'				=> __( 'Show right sidebar', 'make' ),
						'type'				=> 'checkbox',
					),
				),
			),
		);

		/**
		 * Product
		 */
		$prefix = 'layout-product-';
		$sections['layout-product'] = array(
			'panel' => $panel,
			'title' => __( 'Product', 'make-plus' ),
			'description' => ttfmake_sanitize_text( apply_filters( 'ttfmp_shop_layout_product_description', '' ) ),
			'options' => ( function_exists( 'ttfmake_customizer_layout_region_group_definitions' ) ) ?
				ttfmake_customizer_layout_region_group_definitions( 'product' ) :
				array(
				$prefix . 'sidebars-heading' => array(
					'control' => array(
						'control_type'		=> 'TTFMAKE_Customize_Misc_Control',
						'type'				=> 'heading',
						'label'				=> __( 'Header, Footer, Sidebars', 'make' ),
					),
				),
				$prefix . 'hide-header' => array(
					'setting' => array(
						'sanitize_callback'	=> 'absint',
					),
					'control' => array(
						'label'				=> __( 'Hide site header', 'make' ),
						'type'				=> 'checkbox',
					),
				),
				$prefix . 'hide-footer' => array(
					'setting' => array(
						'sanitize_callback'	=> 'absint',
					),
					'control' => array(
						'label'				=> __( 'Hide site footer', 'make' ),
						'type'				=> 'checkbox',
					),
				),
				$prefix . 'sidebar-left' => array(
					'setting' => array(
						'sanitize_callback'	=> 'absint',
					),
					'control' => array(
						'label'				=> __( 'Show left sidebar', 'make' ),
						'type'				=> 'checkbox',
					),
				),
				$prefix . 'sidebar-right' => array(
					'setting' => array(
						'sanitize_callback'	=> 'absint',
					),
					'control' => array(
						'label'				=> __( 'Show right sidebar', 'make' ),
						'type'				=> 'checkbox',
					),
				),
			),
		);

		return $sections;
	}

	/**
	 * Filter to add the Layout: Shop section and/or the Layout: Product section to the Customizer.
	 *
	 * This function takes the main array of Customizer sections and attempts to insert
	 * new ones right after the layout-page section.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array    $sections    The array of sections to add to the Customizer.
	 * @return array                 The modified array of sections.
	 */
	public function legacy_customizer_sections( $sections ) {
		$new_sections = array();

		if ( current_theme_supports( 'ttfmp-shop-layout-shop' ) ) {
			$new_sections['layout-shop'] = array( 'title' => __( 'Layout: Shop', 'make-plus' ), 'path' => $this->component_root );
		}
		if ( current_theme_supports( 'ttfmp-shop-layout-product' ) ) {
			$new_sections['layout-product'] = array( 'title' => __( 'Layout: Product', 'make-plus' ), 'path' => $this->component_root );
		}

		// Get the position of the layout-page section in the array
		$keys = array_keys( $sections );
		$positions = array_flip( $keys );
		$layout_page = absint( $positions[ 'layout-page' ] );

		// Slice the array
		$front = array_slice( $sections, 0, $layout_page + 1 );
		$back  = array_slice( $sections, $layout_page + 1 );

		// Combine and return
		return array_merge( $front, $new_sections, $back );
	}

	/**
	 * Filter to add default values for the Layout: Shop section in the Customizer.
	 *
	 * @since  1.2.0.
	 *
	 * @param  array    $defaults    The array of Customizer option defaults.
	 * @return array
	 */
	public function layout_shop_setting_defaults( $defaults ) {
		$new_defaults = array(
			'layout-shop-hide-header'      => 0,
			'layout-shop-hide-footer'      => 0,
			'layout-shop-sidebar-left'     => 0,
			'layout-shop-sidebar-right'    => 1,
		);

		return array_merge( $defaults, $new_defaults );
	}

	/**
	 * Filter to add default values for the Layout: Product section in the Customizer.
	 *
	 * @since  1.2.0.
	 *
	 * @param  array    $defaults    The array of Customizer option defaults.
	 * @return array
	 */
	public function layout_product_setting_defaults( $defaults ) {
		$new_defaults = array(
			'layout-product-hide-header'     => 0,
			'layout-product-hide-footer'     => 0,
			'layout-product-sidebar-left'    => 0,
			'layout-product-sidebar-right'   => 1,
		);

		return array_merge( $defaults, $new_defaults );
	}

	/**
	 * Add a Highlight Color control to the Colors section of the Customizer
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function color_highlight( $wp_customize ) {
		$color_detail = $wp_customize->get_control( 'ttfmake_color-detail' );

		$priority       = new TTFMAKE_Prioritizer( $color_detail->priority + 1, 1 );
		$section        = $color_detail->section;
		$control_prefix = 'ttfmake_';
		$setting_prefix = 'color';

		/**
		 * Filter the description of the Highlight Color control.
		 *
		 * @since 1.5.0.
		 *
		 * @param string    $description    The control description.
		 */
		$description = apply_filters( 'ttfmp_color_highlight_description', '' );

		// Highlight Color
		$setting_id = $setting_prefix . '-highlight';
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => ttfmake_get_default( $setting_id ),
				'type'              => 'theme_mod',
				'sanitize_callback' => 'maybe_hash_hex_color',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$control_prefix . $setting_id,
				array(
					'settings' => $setting_id,
					'section'  => $section,
					'label'    => __( 'Highlight Color', 'make-plus' ),
					'description' => $description,
					'priority' => $priority->add()
				)
			)
		);
	}

	/**
	 * Filter to add default value for the Highlight Color setting in the Customizer.
	 *
	 * @since  1.2.0.
	 *
	 * @param  array    $defaults    The array of Customizer option defaults.
	 * @return array
	 */
	public function color_highlight_setting_default( $defaults ) {
		$new_defaults = array(
			'color-highlight'  => '#289a00',
		);

		return array_merge( $defaults, $new_defaults );
	}

	/**
	 * Check for flag that indicates that settings have been updated to the latest version.
	 *
	 * @since 1.2.0.
	 *
	 * @return bool    True if settings have not been updated for this version of the plugin.
	 */
	public function has_old_settings() {
		$updated = get_transient( 'ttfmp_shop_settings_updated' );
		if ( $updated !== ttfmp_get_app()->version ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Check for old settings and migrate them to the new ones if necessary.
	 *
	 * Set a transient to indicate the settings have been checked.
	 *
	 * @since 1.2.0.
	 *
	 * @return void
	 */
	public function update_settings() {
		// Migrate the WooCommerce highlight color to the shared Shop Settings highlight color
		if ( false !== $old_setting = get_theme_mod( 'color-woocommerce-highlight' ) ) {
			set_theme_mod( 'color-highlight', $old_setting );
			remove_theme_mod( 'color-woocommerce-highlight' );
		}

		// Migrate the WooCommerce shop sidebar to the shared Shop Settings shop sidebar
		$sidebars = get_option( 'sidebars_widgets' );
		if ( isset( $sidebars['sidebar-shop-woocommerce'] ) ) {
			$sidebars['sidebar-shop'] = $sidebars['sidebar-shop-woocommerce'];
			unset( $sidebars['sidebar-shop-woocommerce'] );
			update_option( 'sidebars_widgets', $sidebars );
		}

		set_transient( 'ttfmp_shop_settings_updated', ttfmp_get_app()->version );
	}
}
endif;

if ( ! function_exists( 'ttfmp_get_shop_settings' ) ) :
/**
 * Instantiate or return the one TTFMP_WooCommerce instance.
 *
 * @since  1.2.0.
 *
 * @return TTFMP_WooCommerce
 */
function ttfmp_get_shop_settings() {
	return TTFMP_Shop_Settings::instance();
}
endif;

ttfmp_get_shop_settings()->init();
