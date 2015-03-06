<?php
/**
 * @package Make Plus
 */

if ( ! class_exists( 'TTFMP_Style_Kits' ) ) :
/**
 * Add additional Customizer options.
 *
 * @since 1.0.0.
 */
class TTFMP_Style_Kits {
	/**
	 * Name of the component.
	 *
	 * @since 1.1.0.
	 *
	 * @var   string    The name of the component.
	 */
	var $component_slug = 'style-kits';

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
	 * The one instance of TTFMP_Style_Kits.
	 *
	 * @since 1.1.0.
	 *
	 * @var   TTFMP_Style_Kits
	 */
	private static $instance;

	/**
	 * Instantiate or return the one TTFMP_Style_Kits instance.
	 *
	 * @since  1.1.0.
	 *
	 * @return TTFMP_Style_Kits
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
	 * @return TTFMP_Style_Kits
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
		require_once $this->component_root . '/definitions.php';

		// Enqueue
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'customize_controls_print_styles', array( $this, 'print_styles' ) );
		add_action( 'wp_head', array( $this, 'head_script' ) );

		// Customizer filters
		if ( ttfmake_customizer_supports_panels() && function_exists( 'ttfmake_customizer_add_panels' ) ) {
			add_filter( 'make_customizer_sections', array( $this, 'customizer_sections' ) );
		} else {
			add_filter( 'ttfmake_customizer_sections', array( $this, 'legacy_customizer_sections' ) );
		}

		// Definition filters
		add_filter( 'ttfmp_style_kit_definitions', array( $this, 'parse_definitions' ), 98 );
		add_filter( 'ttfmp_style_kit_definitions', array( $this, 'add_default_kit' ), 99 );
	}

	/**
	 * Filter to add a new Customizer section
	 *
	 * This function takes the main array of Customizer sections and adds a new one
	 * right before the first panel.
	 *
	 * @since  1.3.3.
	 *
	 * @param  array    $sections    The array of sections to add to the Customizer.
	 * @return array                 The modified array of sections.
	 */
	public function customizer_sections( $sections ) {
		global $wp_customize;
		$theme_prefix = 'ttfmake_';

		// Get priority of General panel
		$general_priority = $wp_customize->get_panel( $theme_prefix . 'general' )->priority;

		$sections['stylekit'] = array(
			'title' => __( 'Style Kits', 'make-plus' ),
			'description' => __( 'Use a style kit to quickly apply designer-picked style choices (fonts, layout, colors) to your website.', 'make-plus' ),
			'priority' => $general_priority - 10,
			'options' => array(
				'stylekit-heading' => array(
					'control' => array(
						'control_type'		=> 'TTFMAKE_Customize_Misc_Control',
						'label'				=> __( 'Kits', 'make-plus' ),
						'type'				=> 'heading',
					),
				),
				'stylekit-dropdown' => array(
					'control' => array(
						'control_type'		=> 'TTFMAKE_Customize_Misc_Control',
						'type'				=> 'text',
						'description'		=> sprintf(
							'<select>%s</select>',
							ttfmp_get_style_kits()->get_kit_options()
						),
					),
				),
				'stylekit-buttons' => array(
					'control' => array(
						'control_type'		=> 'TTFMAKE_Customize_Misc_Control',
						'type'				=> 'text',
						'description'		=> '<a href="#" class="button reset-design">' . __( 'Reset', 'make-plus' ) . '</a><a href="#" class="button load-design">' . __( 'Load Kit', 'make-plus' ) . '</a>',
					),
				),
			),
		);

		return $sections;
	}

	/**
	 * Filter to add a new Customizer section
	 *
	 * This function takes the main array of Customizer sections and attempts to insert
	 * a new one right before the Fonts section.
	 *
	 * @since  1.1.0.
	 *
	 * @param  array    $sections    The array of sections to add to the Customizer.
	 * @return array                 The modified array of sections.
	 */
	public function legacy_customizer_sections( $sections ) {
		$new_sections = array(
			'stylekit'    => array( 'title' => __( 'Style Kits', 'make-plus' ), 'path' => $this->component_root ),
		);

		// Get the position of the layout-page section in the array
		$keys = array_keys( $sections );
		$positions = array_flip( $keys );
		$font = absint( $positions[ 'font' ] );

		// Slice the array
		$front = array_slice( $sections, 0, $font );
		$back  = array_slice( $sections, $font );

		// Combine and return
		return array_merge( $front, $new_sections, $back );
	}

	/**
	 * Enqueue scripts for handling Style Kits choices
	 *
	 * @since 1.1.0.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		// Enqueue Style Kits script
		wp_enqueue_script(
			'ttfmp-style-kits',
			trailingslashit( $this->url_base ) . 'js/customizer-style-kits.js',
			array( 'jquery', 'customize-controls' ),
			ttfmp_get_app()->version,
			true
		);

		// Localize Style Kits script
		$defaults = array( 'defaults' => $this->get_defaults() );
		$definitions = ttfmp_style_kit_definitions();
		$data = array_merge( $defaults, $definitions );
		wp_localize_script(
			'ttfmp-style-kits',
			'ttfmpStyleKitData',
			$data
		);
	}

	/**
	 * Add inline styles for the Style Presets controls
	 *
	 * @since 1.1.0.
	 *
	 * @return void
	 */
	public function print_styles() { ?>
		<style type="text/css">
			#customize-control-ttfmake_stylekit-buttons .button {
				font-style: normal;
			}
			#customize-control-ttfmake_stylekit-buttons .load-design {
				margin-left: 5px;
			}
			#customize-control-ttfmake_stylekit-buttons .spinner {
				display: inline-block;
				margin-top: 4px;
				vertical-align: middle;
			}
		</style>
	<?php }

	/**
	 * Fire a JS function from the parent frame when the preview document finishes loading.
	 *
	 * This will only be added to the document head when loaded in the Preview Pane.
	 *
	 * @since 1.1.0.
	 *
	 * @return void
	 */
	public function head_script() {
		global $wp_customize;
		if ( ! isset( $wp_customize ) || ! $wp_customize->is_preview() ) {
			return;
		}
		?>
		<script type="application/javascript">
			(function($) {
				$(document).on('ready', function() {
					if ('function' === typeof parent.ttfmpDetectPreview) {
						parent.ttfmpDetectPreview();
					}
				});
			})(jQuery);
		</script>
	<?php }

	/**
	 * Parse the definitions array and return the options markup for a select dropdown.
	 *
	 * @since 1.1.0.
	 *
	 * @return string    The options markup.
	 */
	public function get_kit_options() {
		$output = '<option selected="selected" disabled="disabled">--- ' . __( "Choose a kit", 'make-plus' ) . ' ---</option>';

		$definitions = ttfmp_style_kit_definitions();
		$options = array();
		foreach ( $definitions as $key => $kit ) {
			$label = ( isset( $kit['label'] ) ) ? $kit['label'] : ucwords( preg_replace( '/[\-_]/', ' ', $key ) );
			$priority = ( isset( $kit['priority'] ) ) ? absint( $kit['priority'] ) : 0;

			if ( ! isset( $options[$priority] ) || ! is_array( $options[$priority] ) ) {
				$options[$priority] = array();
			}

			$options[$priority][] = '<option value="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</option>';
		}

		ksort( $options );
		foreach ( $options as $priority => $array ) {
			$options[$priority] = implode( '', $array );
		}

		return implode( '', $options );
	}

	/**
	 * Return an array of keys for the options that Style Kits are allowed to change.
	 *
	 * @since 1.4.7
	 *
	 * @return mixed    The array of allowed keys.
	 */
	private function get_allowed_option_keys() {
		// List of options that Style Kits won't touch.
		$blacklist = array(
			'hide-site-title',
			'hide-tagline',
			'logo-regular',
			'logo-retina',
			'logo-favicon',
			'logo-apple-touch',
			'navigation-mobile-label',
			'general-sticky-label',
			'label-read-more',
			'social-facebook-official',
			'social-twitter',
			'social-google-plus-square',
			'social-linkedin',
			'social-instagram',
			'social-flickr',
			'social-youtube',
			'social-vimeo-square',
			'social-pinterest',
			'social-email',
			'social-hide-rss',
			'social-custom-rss',
			'background_repeat',
			'background_position_x',
			'background_attachment',
			'background_size',
			'header-background-repeat',
			'header-background-position',
			'header-background-attachment',
			'header-background-size',
			'main-background-repeat',
			'main-background-position',
			'main-background-attachment',
			'main-background-size',
			'footer-background-repeat',
			'footer-background-position',
			'footer-background-attachment',
			'footer-background-size',
			'header-branding-position',
			'header-bar-content-layout',
			'header-text',
			'header-show-social',
			'header-show-search',
			'footer-widget-areas',
			'footer-text',
			'footer-show-social',
			'layout-blog-hide-header',
			'layout-blog-hide-footer',
			'layout-blog-sidebar-left',
			'layout-blog-sidebar-right',
			'layout-archive-hide-header',
			'layout-archive-hide-footer',
			'layout-archive-sidebar-left',
			'layout-archive-sidebar-right',
			'layout-search-hide-header',
			'layout-search-hide-footer',
			'layout-search-sidebar-left',
			'layout-search-sidebar-right',
			'layout-post-hide-header',
			'layout-post-hide-footer',
			'layout-post-sidebar-left',
			'layout-post-sidebar-right',
			'layout-page-hide-header',
			'layout-page-hide-footer',
			'layout-page-sidebar-left',
			'layout-page-sidebar-right',
			'layout-page-hide-title',
		);

		// Get the master options list.
		$keys = array_keys( ttfmake_option_defaults() );

		// Remove blacklisted options.
		foreach ( $blacklist as $option ) {
			if ( isset( $keys[ $option ] ) ) {
				unset( $keys[ $option ] );
			}
		}

		/**
		 * Filter to modify the array of option keys that Style Kits is allowed to modify.
		 *
		 * @since 1.5.0.
		 *
		 * @param array    $keys    Array of option keys that Style Kits is allowed to modify.
		 */
		return apply_filters( 'ttfmp_style_kit_allowed_option_keys', $keys );
	}

	/**
	 * Return an array of allowed keys matched with their default values.
	 *
	 * @since 1.4.7.
	 *
	 * @return array    The array of defaults.
	 */
	private function get_defaults() {
		$all_defaults = ttfmake_option_defaults();
		$allowed_keys = $this->get_allowed_option_keys();

		$defaults = array();
		foreach ( $allowed_keys as $key ) {
			if ( isset( $all_defaults[$key] ) ) {
				$defaults[$key] = $all_defaults[$key];
			}
		}

		return $defaults;
	}

	/**
	 * Filter the style kit definitions to add a Default kit.
	 *
	 * @since 1.4.7
	 *
	 * @param  array    $definitions    The original array of kit definitions.
	 * @return mixed                    The modified array of kit definitions.
	 */
	public function add_default_kit( $definitions ) {
		$defaults = $this->get_defaults();

		$definitions['default'] = array(
			'label' => __( 'Default', 'make-plus' ),
			'priority' => 1,
			'definitions' => $defaults,
		);

		return $definitions;
	}

	/**
	 * Filter to parse the style kit definitions to fill gaps with default values and remove
	 * non-matching keys.
	 *
	 * @since 1.4.7.
	 *
	 * @param  array    $definitions    The original array of kit definitions.
	 * @return mixed                    The modified array of kit definitions.
	 */
	public function parse_definitions( $definitions ) {
		$defaults = $this->get_defaults();

		foreach ( $definitions as $kit => $data ) {
			// Use shortcode_atts so that non-matching option keys are removed.
			$parsed_definitions = shortcode_atts( $defaults, $data['definitions'] );
			$definitions[$kit]['definitions'] = $parsed_definitions;
		}

		return $definitions;
	}
}
endif;

if ( ! function_exists( 'ttfmp_get_style_kits' ) ) :
/**
 * Instantiate or return the one TTFMP_Style_Kits instance.
 *
 * @since  1.1.0.
 *
 * @return TTFMP_Style_Kits
 */
function ttfmp_get_style_kits() {
	return TTFMP_Style_Kits::instance();
}
endif;

ttfmp_get_style_kits()->init();
