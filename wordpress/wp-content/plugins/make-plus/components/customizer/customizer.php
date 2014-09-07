<?php

if ( ! class_exists( 'TTFMP_Customizer' ) ) :
/**
 * Add additional Customizer options.
 *
 * @since 1.0.0.
 */
class TTFMP_Customizer {
	/**
	 * Name of the component.
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    The name of the component.
	 */
	var $component_slug = 'customizer';

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
	 * The one instance of TTFMP_Customizer.
	 *
	 * @since 1.0.0.
	 *
	 * @var   TTFMP_Customizer
	 */
	private static $instance;

	/**
	 * Instantiate or return the one TTFMP_Customizer instance.
	 *
	 * @since  1.0.0.
	 *
	 * @return TTFMP_Customizer
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
	 * @return TTFMP_Customizer
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
		// Customizer defaults
		add_filter( 'ttfmake_setting_defaults', array( $this, 'setting_defaults' ) );

		// Add new settings and controls
		if ( ttfmake_customizer_supports_panels() && function_exists( 'ttfmake_customizer_add_panels' ) ) {
			add_filter( 'make_customizer_footer_sections', array( $this, 'footer_white_label' ) );
		} else {
			add_action( 'customize_register', array( $this, 'legacy_footer_white_label' ), 20 );
		}

		// Enable White Label setting
		add_filter( 'ttfmake_show_footer_credit', array( $this, 'show_footer_credit' ) );
	}

	/**
	 * Filter to add new Customizer setting defaults
	 *
	 * @since  1.0.0.
	 *
	 * @param  array    $defaults    The array of Customizer option defaults.
	 * @return array
	 */
	public function setting_defaults( $defaults ) {
		$new_defaults = array(
			// Footer
			'footer-hide-credit' => 0,
		);

		return array_merge( $defaults, $new_defaults );
	}

	/**
	 * Add White Label settings and controls to the Footer section
	 *
	 * @since  1.0.0.
	 *
	 * @param  array    $footer_sections    Array of definitions for the Footer panel
	 * @return array                        Augmented array of definitions for the Footer panel
	 */
	public function footer_white_label( $footer_sections ) {
		$theme_prefix = 'ttfmake_';
		$panel = 'ttfmake_footer';

		$footer_sections['footer-white-label'] = array(
			'panel' => $panel,
			'title' => __( 'White Label', 'make' ),
			'options' => array(
				'footer-hide-credit' => array(
					'setting' => array(
						'sanitize_callback'	=> 'absint',
					),
					'control' => array(
						'label'				=> __( 'Hide theme credit', 'make-plus' ),
						'type'				=> 'checkbox',
					),
				),
			),
		);

		return $footer_sections;
	}

	/**
	 * Add White Label settings and controls to the Footer section
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function legacy_footer_white_label() {
		global $wp_customize;

		$social_icons = $wp_customize->get_control( 'ttfmake_footer-show-social' );

		$priority       = new TTFMAKE_Prioritizer( $social_icons->priority + 1, 1 );
		$section        = 'ttfmake_footer';
		$control_prefix = 'ttfmp_';
		$setting_prefix = 'footer';

		// White Label line
		$setting_id = $setting_prefix . '-whitelabel-line';
		$wp_customize->add_control(
			new TTFMAKE_Customize_Misc_Control(
				$wp_customize,
				$control_prefix . $setting_id,
				array(
					'section'     => $section,
					'type'        => 'line',
					'priority'    => $priority->add()
				)
			)
		);

		// White Label heading
		$setting_id = $setting_prefix . '-whitelabel-heading';
		$wp_customize->add_control(
			new TTFMAKE_Customize_Misc_Control(
				$wp_customize,
				$control_prefix . $setting_id,
				array(
					'section'  => $section,
					'type'     => 'heading',
					'label'    => __( 'White Label', 'make-plus' ),
					'priority' => $priority->add()
				)
			)
		);

		// Hide credit
		$setting_id = $setting_prefix . '-hide-credit';
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => ttfmake_get_default( $setting_id ),
				'type'              => 'theme_mod',
				'sanitize_callback' => 'absint',
			)
		);
		$wp_customize->add_control(
			$control_prefix . $setting_id,
			array(
				'settings' => $setting_id,
				'section'  => $section,
				'label'    => __( 'Hide theme credit', 'make-plus' ),
				'type'     => 'checkbox',
				'priority' => $priority->add()
			)
		);
	}

	/**
	 * Return the value of the White Label setting.
	 *
	 * @since  1.0.0.
	 *
	 * @param  bool    $bool    The original boolean
	 * @return bool             The modified boolean
	 */
	public function show_footer_credit( $bool ) {
		return ! (bool) get_theme_mod( 'footer-hide-credit', ttfmake_get_default( 'footer-hide-credit' ) );
	}
}
endif;

if ( ! function_exists( 'ttfmp_get_customizer' ) ) :
/**
 * Instantiate or return the one TTFMP_Customizer instance.
 *
 * @since  1.0.0.
 *
 * @return TTFMP_Customizer
 */
function ttfmp_get_customizer() {
	return TTFMP_Customizer::instance();
}
endif;

ttfmp_get_customizer()->init();
