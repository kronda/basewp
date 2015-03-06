<?php
/**
 * @package Make Plus
 */

if ( ! class_exists( 'TTFMP_Shop_Sidebar' ) ) :
/**
 * Adds a shop sidebar option.
 *
 * @since 1.2.0.
 */
class TTFMP_Shop_Sidebar {
	/**
	 * The views that support the Shop sidebar.
	 *
	 * @since 1.2.0.
	 *
	 * @var array   The views that support the Shop sidebar.
	 */
	var $views = array( 'shop', 'product' );

	/**
	 * The one instance of TTFMP_Shop_Sidebar.
	 *
	 * @since 1.2.0.
	 *
	 * @var   TTFMP_Shop_Sidebar
	 */
	private static $instance;

	/**
	 * Instantiate or return the one TTFMP_Shop_Sidebar instance.
	 *
	 * @since  1.2.0.
	 *
	 * @return TTFMP_Shop_Sidebar
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize the sidebar functionality.
	 *
	 * @since 1.2.0.
	 *
	 * @return TTFMP_Shop_Sidebar
	 */
	public function __construct() {
		// Determine which views get the Shop sidebar
		$args = get_theme_support( 'ttfmp-shop-sidebar' );
		if ( is_array( $args ) ) {
			$this->views = (array) $args[0];
		}

		// Register the sidebar
		add_action( 'widgets_init', array( $this, 'register_shop_sidebar' ) );

		// Customizer setting additions
		add_filter( 'ttfmake_setting_defaults', array( $this, 'sidebar_setting_defaults' ) );
		add_filter( 'ttfmake_setting_choices', array( $this, 'sidebar_setting_choices' ), 10, 2 );
		add_action( 'customize_register', array( $this, 'shop_sidebar' ), 20 );

		// Add filters to replace the normal sidebar with the Shop
		add_filter( 'ttfmake_sidebar_left', array( $this, 'display_shop_sidebar' ) );
		add_filter( 'ttfmake_sidebar_right', array( $this, 'display_shop_sidebar' ) );
	}

	/**
	 * Register the Shop sidebar
	 *
	 * @since 1.2.0.
	 *
	 * @return void
	 */
	public function register_shop_sidebar() {
		register_sidebar( array(
			'id'            => 'sidebar-shop',
			'name'          => __( 'Shop Sidebar', 'make-plus' ),
			'description'   => $this->sidebar_description( 'sidebar-shop' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );
	}

	/**
	 * Filter to add new Customizer defaults for the Shop Sidebar.
	 *
	 * @since 1.2.0.
	 *
	 * @param $defaults
	 * @return array
	 */
	public function sidebar_setting_defaults( $defaults ) {
		$new_defaults = array();
		foreach ( $this->views as $view ) {
			$new_defaults[ 'layout-' . $view . '-shop-sidebar' ] = ( in_array( $view, array( 'shop', 'product' ) ) ) ? 'right' : 'none';
		}

		return array_merge( $defaults, $new_defaults );
	}

	/**
	 * Filter to add new Customizer setting choices for the Shop sidebar.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array     $choices    The current choices.
	 * @param  string    $setting    The current setting.
	 * @return array
	 */
	public function sidebar_setting_choices( $choices, $setting ) {
		if ( count( $choices ) > 1 ) {
			return $choices;
		}

		// Valid setting keys
		$settings = array();
		foreach ( $this->views as $view ) {
			$settings[] = 'layout-' . $view . '-shop-sidebar';
		}

		// Choices
		if ( in_array( $setting, $settings ) ) {
			$choices = array(
				'left'  => __( 'Left Sidebar', 'make-plus' ),
				'right' => __( 'Right Sidebar', 'make-plus' ),
				'none'  => __( 'Neither', 'make-plus' ),
			);
		}

		return $choices;
	}

	/**
	 * Add the Shop Sidebar option to each supported view.
	 *
	 * @since  1.2.0.
	 *
	 * @return void
	 */
	public function shop_sidebar() {
		global $wp_customize;

		if ( ! empty( $this->views ) ) {
			foreach ( $this->views as $view ) {
				$right_sidebar  = $wp_customize->get_control( 'ttfmake_layout-' . $view . '-sidebar-right' );

				$priority       = new TTFMAKE_Prioritizer( $right_sidebar->priority + 1, 1 );
				$section        = 'ttfmake_layout-' . $view;
				$control_prefix = 'ttfmake_';
				$setting_prefix = 'layout-' . $view;

				$setting_id = $setting_prefix . '-shop-sidebar';
				$wp_customize->add_setting(
					$setting_id,
					array(
						'default'           => ttfmake_get_default( $setting_id ),
						'type'              => 'theme_mod',
						'sanitize_callback' => 'ttfmake_sanitize_choice',
					)
				);
				$wp_customize->add_control(
					$control_prefix . $setting_id,
					array(
						'settings' => $setting_id,
						'section'  => $section,
						'label'    => __( 'Shop Sidebar Location', 'make-plus' ),
						'type'     => 'select',
						'choices'  => ttfmake_get_choices( $setting_id ),
						'priority' => $priority->add()
					)
				);
			}
		}
	}

	/**
	 * Add a description to the Shop sidebar that explains where it is currently displayed.
	 *
	 * @since 1.2.0.
	 *
	 * @param  string    $sidebar_id    The id of the sidebar.
	 * @return string
	 */
	private function sidebar_description( $sidebar_id ) {
		$description = '';

		// Build the list of active locations
		$locations = '';
		if ( ! empty( $this->views ) ) {
			$total = count( $this->views );
			foreach ( $this->views as $view ) {
				$key = 'layout-' . $view . '-shop-sidebar';
				$mod = ttfmake_sanitize_choice( get_theme_mod( $key, ttfmake_get_default( $key ) ), $key );
				$choices = ttfmake_get_choices( $key );

				if ( $mod && 'none' !== $mod ) {
					// Separator between multiple locations
					if ( '' !== $locations ) {
						$locations .= _x( ';', 'list item separator', 'make-plus' ) . ' ';
					}

					// Location description
					$locations .= sprintf(
						__( 'the %1$s in the %2$s view', 'make-plus' ),
						$choices[ $mod ],
						ucwords( $view )
					);
				}
			}
		}

		// Build the description
		if ( '' === $locations ) {
			$description = __( 'This widget area is currently disabled. Enable it in the "Layout" section of the Theme Customizer.', 'make-plus' );
		} else {
			$description = sprintf(
				__( 'This widget area is currently used in place of: %s. Change this in the "Layout" section of the Theme Customizer.', 'make-plus' ),
				esc_html( $locations )
			);
		}

		return esc_html( $description );
	}

	/**
	 * Replace the normal sidebar ID with the Shop one, if applicable.
	 *
	 * @since 1.2.0.
	 *
	 * @param  string    $sidebar_id    The ID of the current sidebar.
	 * @return string
	 */
	public function display_shop_sidebar( $sidebar_id ) {
		if ( false === strpos( $sidebar_id, 'sidebar-' ) ) {
			return $sidebar_id;
		}

		$view = ttfmake_get_view();
		if ( ! in_array( $view, $this->views ) ) {
			return $sidebar_id;
		}

		$mod = get_theme_mod( 'layout-' . $view . '-shop-sidebar', ttfmake_get_default( 'layout-' . $view . '-shop-sidebar' ) );
		$location = str_replace( 'sidebar-', '', $sidebar_id );

		if ( 'none' !== $mod && $location === $mod ) {
			$sidebar_id = 'sidebar-shop';
		}

		return $sidebar_id;
	}
}
endif;

if ( ! function_exists( 'ttfmp_get_shop_sidebar' ) ) :
/**
 * Instantiate or return the one TTFMP_Shop_Sidebar instance.
 *
 * @since  1.2.0.
 *
 * @return TTFMP_Shop_Sidebar
 */
function ttfmp_get_shop_sidebar() {
	return TTFMP_Shop_Sidebar::instance();
}
endif;

ttfmp_get_shop_sidebar();