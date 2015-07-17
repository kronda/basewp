<?php

/**
 * Helper class for Customizer logic.
 *
 * @since 1.2.0
 */
final class FLCustomizer {

	/**
	 * An array of data used to render Customizer panels.
	 *
	 * @since 1.2.0
	 * @access private
	 * @var array $_panels
	 */
	static private $_panels = array();

	/**
	 * An array of data for each settings preset.
	 *
	 * @since 1.2.0
	 * @access private
	 * @var array $_presets
	 */
	static private $_presets = array();

	/**
	 * Cache for the get_theme_mods call.
	 *
	 * @since 1.2.0
	 * @access private
	 * @var array $_mods
	 */
	static private $_mods = false;

	/**
	 * A flag for whether or not we're in a Customizer
	 * preview or not.
	 *
	 * @since 1.2.0
	 * @access private
	 * @var bool $_in_customizer_preview
	 */
	static private $_in_customizer_preview = false;

	/**
	 * The prefix for the option that is stored in the 
	 * database for the cached CSS file key.
	 *
	 * @since 1.2.0
	 * @access private
	 * @var string $_css_key
	 */
	static private $_css_key = 'fl_theme_css_key';

	/**
	 * Adds Customizer panel data to the $_panels array.
	 *
	 * @since 1.2.0
	 * @param string $key The key for the panel to add. Must be unique.
	 * @param array $data The panel data.
	 * @return void
	 */
	static public function add_panel( $key, $data )
	{
		self::$_panels[ $key ] = $data;
	}

	/**
	 * Adds settings preset data to the $_presets array.
	 *
	 * @since 1.2.0
	 * @param string $key The key for the preset to add. Must be unique.
	 * @param array $data An array of settings for the preset.
	 * @return void
	 */
	static public function add_preset( $key, $data )
	{
		self::$_presets[ $key ] = $data;
	}

	/**
	 * Removes a preset from the presets array.
	 *
	 * @since 1.3.0
	 * @param string $key The key of the preset to remove.
	 * @return void
	 */
	static public function remove_preset( $key )
	{
		unset( self::$_presets[ $key ] );
	}

	/**
	 * Called by the customize_preview_init action to initialize 
	 * a Customizer preview.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	static public function preview_init()
	{
		self::$_in_customizer_preview = true;

		self::refresh_css();

		wp_enqueue_script( 'fl-stylesheet', FL_THEME_URL . '/js/stylesheet.js', array(), '', true );
		wp_enqueue_script( 'fl-customizer-preview', FL_THEME_URL . '/js/customizer-preview.js', array(), '', true );
	}

	/**
	 * Called by the customize_controls_enqueue_scripts action to enqueue 
	 * styles and scripts for the Customizer.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	static public function controls_enqueue_scripts()
	{
		wp_enqueue_style( 'fl-customizer', FL_THEME_URL . '/css/customizer.css', array(), FL_THEME_VERSION );
		wp_enqueue_script( 'fl-customizer-toggles', FL_THEME_URL . '/js/customizer-toggles.js', array(), FL_THEME_VERSION, true );
		wp_enqueue_script( 'fl-customizer', FL_THEME_URL . '/js/customizer.js', array(), FL_THEME_VERSION, true );
	}

	/**
	 * Called by the customize_controls_print_footer_scripts action to 
	 * print scripts in the footer for the Customizer.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	static public function controls_print_footer_scripts()
	{
		// Opening script tag
		echo '<script>';

		// Fonts
		FLFonts::js();

		// Defaults
		echo 'var FLCustomizerPresetDefaults = ' . json_encode( self::_get_default_preset_mods() ) . ';';

		// Presets
		echo 'var FLCustomizerPresets = ' . json_encode( self::$_presets ) . ';';

		// Closing script tag
		echo '</script>';
	}

	/**
	 * Called by the customize_register action to register presets,
	 * panels, sections, settings and controls.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	static public function register( $customizer )
	{
		require_once FL_THEME_DIR . '/classes/class-fl-customizer-control.php';

		self::_register_presets( $customizer );
		self::_register_panels( $customizer );
		self::_register_export_import_section( $customizer );
		self::_move_builtin_sections( $customizer );
	}

	/**
	 * Called by the customize_save_after action to refresh
	 * the cached CSS when Customizer settings are saved.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	static public function save( $customizer )
	{
		self::refresh_css();
	}

	/**
	 * Returns an array of all theme mods.
	 *
	 * @since 1.2.0
	 * @return array
	 */
	static public function get_mods()
	{
		// We don't have mods yet, get them from the database.
		if ( ! self::$_mods ) {

			// Get preset preview mods.
			if ( self::is_preset_preview() ) {
				$mods = self::_get_preset_preview_mods();
			}
			// Get saved mods.
			else {

				// Get the settings.
				$mods = get_theme_mods();

				// Merge default mods.
				$mods = self::_merge_mods( 'default', $mods );
			}

			// No mods! Get defaults.
			if ( ! $mods ) {
				$mods = self::_get_default_mods();
			}
		}
		// We have cached the mods already.
		else {
			$mods = self::$_mods;
		}

		// Hack to insure the mod values are the same as the customzier
		// values since get_theme_mods doesn't return the correct values
		// while in the customizer. See https://core.trac.wordpress.org/ticket/24844
		if ( self::is_customizer_preview() ) {
			foreach ( $mods as $key => $val ) {
				$mods[ $key ] = apply_filters( 'theme_mod_' . $key, $mods[ $key ] );
			}
		}

		return $mods;
	}

	/**
	 * Returns a URL for the cached CSS file.
	 *
	 * @since 1.2.0
	 * @return string
	 */
	static public function css_url()
	{
		// Get the cache dir and css key.
		$cache_dir = self::get_cache_dir();
		$css_slug  = self::_css_slug();
		$css_key   = get_option( self::$_css_key . '-' . $css_slug );
		$css_path  = $cache_dir['path'] . $css_slug . '-' . $css_key . '.css';
		$css_url   = $cache_dir['url'] . $css_slug . '-' . $css_key . '.css';

		// No css key, recompile the css.
		if ( ! $css_key ) {
			self::_compile_css();
			return self::css_url();
		}

		// Check to see if the file exists.
		if ( ! file_exists( $css_path ) ) {
			self::_compile_css();
			return self::css_url();
		}

		// Return the url.
		return $css_url;
	}

	/**
	 * Clears and rebuilds the cached CSS file.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	static public function refresh_css()
	{
		self::_clear_css_cache();
		self::_compile_css();
	}

	/**
	 * Checks to see if this is a preset preview or not.
	 *
	 * @since 1.2.0
	 * @return bool
	 */
	static public function is_preset_preview()
	{
		if ( ! isset( $_GET['fl-preview'] ) ) {
			return false;
		}
		if ( ! isset( self::$_presets[ $_GET['fl-preview'] ] ) ) {
			return false;
		}
		else if ( current_user_can('manage_options') || self::_is_demo_server() ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks to see if this is a Customizer preview or not.
	 *
	 * @since 1.2.0
	 * @return bool
	 */
	static public function is_customizer_preview()
	{
		return self::$_in_customizer_preview;
	}

	/**
	 * Sanitize callback for Customizer number settings.
	 *
	 * @since 1.2.0
	 * @return int
	 */
	static public function sanitize_number( $val )
	{
		return is_numeric( $val ) ? $val : 0;
	}

	/**
	 * Returns an array with the path and url for the cache directory.
	 *
	 * @since 1.2.0
	 * @return array
	 */
	static public function get_cache_dir()
	{
		$dir_name   = basename( FL_THEME_DIR );
		$wp_info    = wp_upload_dir();

		// SSL workaround.
		if ( FLTheme::is_ssl() ) {
			$wp_info['baseurl'] = str_ireplace( 'http://', 'https://', $wp_info['baseurl'] );
		}

		// Build the paths.
		$dir_info   = array(
			'path'      => $wp_info['basedir'] . '/' . $dir_name . '/',
			'url'       => $wp_info['baseurl'] . '/' . $dir_name . '/'
		);

		// Create the cache dir if it doesn't exist.
		if ( ! file_exists( $dir_info['path'] ) ) {
			mkdir( $dir_info['path'] );
		}

		return $dir_info;
	}

	/**
	 * Registers the presets section, control and setting.
	 *
	 * @since 1.2.0
	 * @access private
	 * @param object $customizer An instance of WP_Customize_Manager.
	 * @return void
	 */
	static private function _register_presets( $customizer )
	{
		// Presets section
		$customizer->add_section( 'fl-presets', array(
			'title'    => _x( 'Presets', 'Customizer section title. Theme design/style presets.', 'fl-automator' ),
			'priority' => 0
		) );

		// Presets setting
		$customizer->add_setting( 'fl-preset', array(
			'default' => 'default'
		));

		// Presets choices
		$choices = array();

		foreach ( self::$_presets as $key => $val ) {
			$choices[ $key ] = $val['name'];
		}

		// Presets control
		$customizer->add_control( new WP_Customize_Control( $customizer, 'fl-preset', array(
			'section'       => 'fl-presets',
			'settings'      => 'fl-preset',
			'description'   => __( 'Start by selecting a preset for your theme.', 'fl-automator' ),
			'type'          => 'select',
			'choices'       => $choices
		)));
	}

	/**
	 * Registers the panels using data in the $_panels array.
	 *
	 * @since 1.2.0
	 * @access private
	 * @param object $customizer An instance of WP_Customize_Manager.
	 * @return void
	 */
	static private function _register_panels( $customizer )
	{
		$panel_priority     = 1;
		$section_priority   = 1;
		$option_priority    = 1;

		// Loop panels
		foreach ( self::$_panels as $panel_key => $panel_data ) {

			// Add panel
			if ( self::_has_panel_support() ) {
				$customizer->add_panel( $panel_key, array(
					'title'    => $panel_data['title'],
					'priority' => $panel_priority
				));
			}

			// Increment panel priority
			$panel_priority++;

			// Loop panel sections
			if ( isset( $panel_data['sections'] ) ) {

				foreach ( $panel_data['sections'] as $section_key => $section_data ) {

					// Add section
					$customizer->add_section( $section_key, array(
						'panel'    => $panel_key,
						'title'    => $section_data['title'],
						'priority' => $section_priority
					));

					// Increment section priority
					$section_priority++;

					// Loop section options
					if ( isset( $section_data['options'] ) ) {

						foreach ( $section_data['options'] as $option_key => $option_data ) {

							// Add setting
							if ( ! isset( $option_data['setting'] ) ) {
								$option_data['setting'] = array( 'default' => '' );
							}

							$customizer->add_setting( $option_key, $option_data['setting'] );

							// Add control
							$option_data['control']['section']  = $section_key;
							$option_data['control']['settings'] = $option_key;
							$option_data['control']['priority'] = $option_priority;
							$customizer->add_control(
								new $option_data['control']['class']( $customizer, $option_key, $option_data['control'] )
							);

							// Increment option priority
							$option_priority++;
						}

						// Reset option priority
						$option_priority = 0;
					}
				}

				// Reset section priority on if we have panel support.
				if ( self::_has_panel_support() ) {
					$section_priority = 0;
				}
			}
		}
	}

	/**
	 * Registers the export/import section, control and setting.
	 *
	 * @since 1.2.0
	 * @access private
	 * @param object $customizer An instance of WP_Customize_Manager.
	 * @return void
	 */
	static private function _register_export_import_section( $customizer )
	{
		if ( ! class_exists( 'CEI_Core' ) && current_user_can( 'install_plugins' ) ) {

			$customizer->add_section( 'fl-export-import', array(
				'title'    => _x( 'Export/Import', 'Customizer section title.', 'fl-automator' ),
				'priority' => 10000000
			) );

			$customizer->add_setting( 'fl-export-import', array(
				'default' => '',
				'type'    => 'none'
			));

			$customizer->add_control( new FLCustomizerControl(
				$customizer,
				'fl-export-import',
				array(
					'section'       => 'fl-export-import',
					'type'          => 'export-import',
					'priority'      => 1
				)
			));
		}
	}

	/**
	 * Checks to see if Customizer panels are supported.
	 *
	 * @since 1.2.0
	 * @access private
	 * @return bool
	 */
	static private function _has_panel_support()
	{
		return method_exists( 'WP_Customize_Manager' , 'add_panel' );
	}

	/**
	 * Moves the builtin sections to the Settings panel.
	 *
	 * @since 1.2.0
	 * @access private
	 * @param object $customizer An instance of WP_Customize_Manager.
	 * @return void
	 */
	static private function _move_builtin_sections( $customizer )
	{
		$title_tagline      = $customizer->get_section( 'title_tagline' );
		$nav                = $customizer->get_section( 'nav' );
		$static_front_page  = $customizer->get_section( 'static_front_page' );

		// Set new panels or set a low priority.
		if ( self::_has_panel_support() ) {
			$title_tagline->panel       = 'fl-settings';
			$nav->panel                 = 'fl-settings';
			$static_front_page->panel   = 'fl-settings';
		}
		else {
			$title_tagline->priority      = 10000;
			$nav->priority                = 10001;
			$static_front_page->priority  = 10002;
		}
	}

	/**
	 * Get an array of defaults for all Customizer settings.
	 *
	 * @since 1.2.0
	 * @access private
	 * @return array
	 */
	static private function _get_default_mods()
	{
		$mods = array();

		// Loop through the panels.
		foreach ( self::$_panels as $panel ) {

			if ( ! isset( $panel['sections'] ) ) {
				continue;
			}

			// Loop through the panel sections.
			foreach ( $panel['sections'] as $section ) {

				if ( ! isset( $section['options'] ) ) {
					continue;
				}

				// Loop through the section options.
				foreach ( $section['options'] as $option_id => $option ) {
					$mods[ $option_id ] = isset( $option['setting']['default'] ) ? $option['setting']['default'] : '';
				}
			}
		}

		return $mods;
	}

	/**
	 * Get an array of defaults for settings that have a preset.
	 *
	 * @since 1.2.0
	 * @access private
	 * @return array
	 */
	static private function _get_default_preset_mods()
	{
		$keys       = array();
		$defaults   = self::_get_default_mods();
		$mods       = array();

		foreach ( self::$_presets as $preset => $data ) {

			foreach ( $data['settings'] as $key => $val ) {

				if ( ! in_array( $key, $keys ) ) {
					$keys[] = $key;
				}
			}
		}

		foreach ( $keys as $key ) {
			if ( isset( $defaults[ $key ] ) ) {
				$mods[ $key ] = $defaults[ $key ];
			}
		}

		return $mods;
	}

	/**
	 * Get an array of mods for either a Customizer preview
	 * or a preset preview.
	 *
	 * @since 1.2.0
	 * @access private
	 * @return array|bool
	 */
	static private function _get_preset_preview_mods()
	{
		if ( self::is_preset_preview() ) {

			$preset_slug                       = $_GET['fl-preview'];
			$preset                            = self::$_presets[ $preset_slug ];
			$preset['settings']['fl-preset']   = $_GET['fl-preview'];

			if ( current_user_can('manage_options' ) ) {
				return self::_merge_mods( 'saved', $preset['settings'] );
			}
			else if ( self::_is_demo_server() ) {
				return self::_merge_mods( 'default', $preset['settings'] );
			}

			return false;
		}
	}

	/**
	 * Checks if this is the Beaver Builder demo server or not.
	 *
	 * @since 1.2.0
	 * @access private
	 * @return bool
	 */
	static private function _is_demo_server()
	{
		return stristr( $_SERVER['HTTP_HOST'], 'demos.wpbeaverbuilder.com' );
	}

	/**
	 * Merges the provided mods array with the type of mods
	 * specified in the $merge_with param.
	 *
	 * @since 1.2.0
	 * @access private
	 * @param string $merge_with Possible values are default and saved.
	 * @param array $mods The mods array to merge with.
	 * @return array|bool
	 */
	static private function _merge_mods( $merge_with = 'default', $mods = null )
	{
		if ( ! $mods ) {
			return false;
		}
		else if ( $merge_with == 'default' ) {
			$new_mods = self::_get_default_mods();
		}
		else if ( $merge_with == 'saved' ) {
			$new_mods = get_theme_mods();
			$new_mods = self::_merge_mods( 'default', $new_mods );
		}

		foreach ( $mods as $mod_id => $mod ) {
			$new_mods[ $mod_id ] = $mod;
		}

		return $new_mods;
	}

	/**
	 * Deletes all cached CSS files.
	 *
	 * @since 1.2.0
	 * @access private
	 * @return void
	 */
	static private function _clear_css_cache()
	{
		$dir_name   = basename( FL_THEME_DIR );
		$cache_dir  = self::get_cache_dir();
		$css_slug   = self::_css_slug();

		if ( ! empty( $cache_dir['path'] ) && stristr( $cache_dir['path'], $dir_name ) ) {

			$css = glob( $cache_dir['path'] . $css_slug . '-*' );

			foreach ( $css as $file ) {
				if ( is_file( $file ) ) {
					unlink( $file );
				}
			}
		}
	}

	/**
	 * Returns the prefix slug for the CSS cache file.
	 *
	 * @since 1.2.0
	 * @access private
	 * @return string
	 */
	static private function _css_slug()
	{
		if ( self::is_preset_preview() ) {
			$slug = 'preview-' . $_GET['fl-preview'];
		}
		else if ( self::is_customizer_preview() ) {
			$slug = 'customizer';
		}
		else {
			$slug = 'skin';
		}

		return $slug;
	}

	/**
	 * Compiles the cached CSS file.
	 *
	 * @since 1.2.0
	 * @access private
	 * @return void
	 */
	static private function _compile_css()
	{
		$theme_info   = wp_get_theme();
		$mods         = self::get_mods();
		$preset       = isset( $mods['fl-preset'] ) ? $mods['fl-preset'] : 'default';
		$cache_dir    = self::get_cache_dir();
		$new_css_key  = uniqid();
		$css_slug     = self::_css_slug();
		$css          = '';

		// Theme stylesheet
		$css .= file_get_contents( FL_THEME_DIR . '/less/theme.less' );

		// WooCommerce
		$css .= file_get_contents( FL_THEME_DIR . '/less/woocommerce.less' );

		// Skin
		if ( isset( self::$_presets[ $preset ]['skin'] ) ) {
		
			$skin = self::$_presets[ $preset ]['skin'];
			
			if ( stristr( $skin, '.css' ) || stristr( $skin, '.less' ) ) {
				$skin_file = $skin;
			}
			else {
				$skin_file = FL_THEME_DIR . '/less/skin-' . $skin . '.less';
			}
			
			if ( file_exists( $skin_file ) ) {
				$css .= file_get_contents( $skin_file );
			}
		}

		// Replace {FL_THEME_URL} placeholder.
		$css = str_replace( '{FL_THEME_URL}', FL_THEME_URL, $css );

		// Compile LESS
		$css = self::_compile_less( $css );

		// Compress
		$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
		$css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );

		// Save the new css.
		file_put_contents( $cache_dir['path'] . $css_slug . '-' . $new_css_key . '.css', $css );

		// Save the new css key.
		update_option( self::$_css_key . '-' . $css_slug, $new_css_key );
	}

	/**
	 * Compiles the provided LESS CSS.
	 *
	 * @since 1.2.0
	 * @access private
	 * @param string $css The LESS CSS to compile.
	 * @return string
	 */
	static private function _compile_less( $css )
	{
		if ( ! class_exists( 'lessc' ) ) {
			require_once FL_THEME_DIR . '/classes/class-lessc.php';
		}

		$less = new lessc;
		$mods = self::get_mods();

		// Fix issue with IE filters
		$css = preg_replace_callback( '(filter\s?:\s?(.*);)', 'FLCustomizer::_preg_replace_less', $css );

		// Mixins
		$mixins = file_get_contents( FL_THEME_DIR . '/less/mixins.less' );

		// Vars
		$less_vars = self::_get_less_vars();

		// Compile and return
		return $less->compile( $mixins . $less_vars . $css );
	}

	/**
	 * Builds a string with LESS variables using Customizer settings.
	 *
	 * @since 1.2.0
	 * @access private
	 * @return string
	 */
	static private function _get_less_vars()
	{
		$mods                                   = self::get_mods();
		$defaults   							= self::_get_default_mods();
		$vars                                   = array();
		$vars_string                            = '';

		// Layout
		$boxed                                  = 'boxed' == $mods['fl-layout-width'];
		$shadow_size                            = $mods['fl-layout-shadow-size'];
		$shadow_color                           = $mods['fl-layout-shadow-color'];
		$vars['body-padding']                   = $boxed ? $mods['fl-layout-spacing'] . 'px 0' : '0';
		$vars['page-shadow']                    = $boxed ? '0 0 ' . $shadow_size . 'px ' . $shadow_color : 'none';

		// Body Background Image
		$vars['body-bg-image']                  = empty( $mods['fl-body-bg-image'] ) ? 'none' : 'url(' . $mods['fl-body-bg-image'] . ')';
		$vars['body-bg-repeat']                 = $mods['fl-body-bg-repeat'];
		$vars['body-bg-position']               = $mods['fl-body-bg-position'];
		$vars['body-bg-attachment']             = $mods['fl-body-bg-attachment'];
		$vars['body-bg-size']                   = $mods['fl-body-bg-size'];

		// Body Colors
		$vars['body-bg-color']                  = FLColor::hex( array( $mods['fl-body-bg-color'], $defaults['fl-body-bg-color'] ) );
		$vars['body-bg-color-2']             	= FLColor::similar( array( 1, 4, 13 ), $vars['body-bg-color'] );
		$vars['body-bg-color-3']             	= FLColor::similar( array( 3, 9, 18 ), $vars['body-bg-color'] );
		$vars['body-border-color']              = FLColor::similar( array( 10, 9, 19 ), $vars['body-bg-color'] );
		$vars['body-border-color-2']            = FLColor::similar( array( 20, 20, 30 ), $vars['body-bg-color'] );
		$vars['body-fg-color']                  = FLColor::foreground( $vars['body-bg-color'] );

		// Accent Color
		$vars['accent-color']                   = FLColor::hex( array( $mods['fl-accent'], $defaults['fl-accent'] ) );
		$vars['accent-hover-color']             = FLColor::hex( array( $mods['fl-accent-hover'], $mods['fl-accent'] ) );
		$vars['accent-fg-color']                = FLColor::foreground( $vars['accent-color'] );
		$vars['accent-hover-fg-color']          = FLColor::foreground( $vars['accent-hover-color'] );

		// Text Colors
		$vars['heading-color']                  = FLColor::hex( $mods['fl-heading-text-color'] );
		$vars['text-color']                     = FLColor::hex( $mods['fl-body-text-color'] );

		// Fonts
		$vars['text-font']                      = self::_get_font_family_string( $mods['fl-body-font-family'] );
		$vars['text-size']                      = $mods['fl-body-font-size'] . 'px';
		$vars['heading-font']                   = self::_get_font_family_string( $mods['fl-heading-font-family'] );
		$vars['heading-weight']                 = $mods['fl-heading-font-weight'];
		$vars['heading-transform']              = $mods['fl-heading-font-format'];
		$vars['h1-size']                        = $mods['fl-h1-font-size'] . 'px';
		$vars['h2-size']                        = $mods['fl-h2-font-size'] . 'px';
		$vars['h3-size']                        = $mods['fl-h3-font-size'] . 'px';
		$vars['h4-size']                        = $mods['fl-h4-font-size'] . 'px';
		$vars['h5-size']                        = $mods['fl-h5-font-size'] . 'px';
		$vars['h6-size']                        = $mods['fl-h6-font-size'] . 'px';
		$vars['logo-font']                      = self::_get_font_family_string( $mods['fl-logo-font-family'] );
		$vars['logo-weight']                    = $mods['fl-logo-font-weight'];
		$vars['logo-size']                      = $mods['fl-logo-font-size'] . 'px';
		
		// Top Bar Background Image
		$vars['topbar-bg-image']                = empty( $mods['fl-topbar-bg-image'] ) ? 'none' : 'url(' . $mods['fl-topbar-bg-image'] . ')';
		$vars['topbar-bg-repeat']               = $mods['fl-topbar-bg-repeat'];
		$vars['topbar-bg-position']             = $mods['fl-topbar-bg-position'];
		$vars['topbar-bg-attachment']           = $mods['fl-topbar-bg-attachment'];
		$vars['topbar-bg-size']                 = $mods['fl-topbar-bg-size'];

		// Top Bar Colors
		$vars['topbar-bg-color']				= FLColor::hex_or_transparent( $mods['fl-topbar-bg-color'] );
		$vars['topbar-bg-grad']                	= $mods['fl-topbar-bg-gradient'] ? 10 : 0;
		$vars['topbar-border-color']        	= FLColor::similar( array( 10, 13, 19 ), array( $vars['topbar-bg-color'], $vars['body-bg-color'] ) );
		$vars['topbar-fg-color']               	= FLColor::hex( array( $mods['fl-topbar-text-color'], $vars['text-color'] ) );
		$vars['topbar-fg-link-color']          	= FLColor::hex( array( $mods['fl-topbar-link-color'], $vars['topbar-fg-color'] ) );
		$vars['topbar-fg-hover-color']         	= FLColor::hex( array( $mods['fl-topbar-hover-color'], $vars['topbar-fg-color'] ) );
		$vars['topbar-dropdown-bg-color']      	= FLColor::hex( array( $mods['fl-topbar-bg-color'], $vars['body-bg-color'] ) );

		// Header Background Image
		$vars['header-bg-image']                = empty( $mods['fl-header-bg-image'] ) ? 'none' : 'url(' . $mods['fl-header-bg-image'] . ')';
		$vars['header-bg-repeat']               = $mods['fl-header-bg-repeat'];
		$vars['header-bg-position']             = $mods['fl-header-bg-position'];
		$vars['header-bg-attachment']           = $mods['fl-header-bg-attachment'];
		$vars['header-bg-size']                 = $mods['fl-header-bg-size'];

		// Header Colors
		$vars['header-bg-color']				= FLColor::hex_or_transparent( $mods['fl-header-bg-color'] );
		$vars['header-bg-grad']                 = $mods['fl-header-bg-gradient'] ? 10 : 0;
		$vars['header-border-color']        	= FLColor::similar( array( 10, 13, 19 ), array( $vars['header-bg-color'], $vars['body-bg-color'] ) );
		$vars['header-fg-color']                = FLColor::hex( array( $mods['fl-header-text-color'], $vars['text-color'] ) );
		$vars['header-fg-link-color']           = FLColor::hex( array( $mods['fl-header-link-color'], $vars['header-fg-color'] ) );
		$vars['header-fg-hover-color']          = FLColor::hex( array( $mods['fl-header-hover-color'], $vars['header-fg-color'] ) );
		$vars['header-padding']                 = $mods['fl-header-padding'] . 'px';
		
		// Fixed Header Background Color
		$vars['fixed-header-bg-color']          = FLColor::hex( array( $vars['header-bg-color'], $vars['body-bg-color'] ) );
		
		// Nav Fonts
		$vars['nav-font-family']                = self::_get_font_family_string( $mods['fl-nav-font-family'] );
		$vars['nav-font-weight']                = $mods['fl-nav-font-weight'];
		$vars['nav-font-format']                = $mods['fl-nav-font-format'];
		$vars['nav-font-size']                  = $mods['fl-nav-font-size'] . 'px';
		
		// Nav Background Image
		$vars['nav-bg-image']                	= empty( $mods['fl-nav-bg-image'] ) ? 'none' : 'url(' . $mods['fl-nav-bg-image'] . ')';
		$vars['nav-bg-repeat']               	= $mods['fl-nav-bg-repeat'];
		$vars['nav-bg-position']             	= $mods['fl-nav-bg-position'];
		$vars['nav-bg-attachment']           	= $mods['fl-nav-bg-attachment'];
		$vars['nav-bg-size']                 	= $mods['fl-nav-bg-size'];
		
		// Nav Layout
		$vars['nav-item-spacing']               = $mods['fl-nav-item-spacing'] . 'px';

		// Right Nav Colors
		if ( 'right' == $mods['fl-header-layout'] ) {
			$vars['nav-bg-color']               = $vars['header-bg-color'];
			$vars['nav-bg-grad']                = 0;
			$vars['nav-border-color']           = $vars['header-border-color'];
			$vars['nav-fg-color']               = $vars['header-fg-color'];
			$vars['nav-fg-link-color']          = $vars['header-fg-link-color'];
			$vars['nav-fg-hover-color']         = $vars['header-fg-hover-color'];
		}
		// Bottom and Centered Nav Colors
		else {
			$vars['nav-bg-color']				= FLColor::hex_or_transparent( $mods['fl-nav-bg-color'] );
			$vars['nav-bg-grad']                = $mods['fl-nav-bg-gradient'] ? 5 : 0;
			$vars['nav-border-color']        	= FLColor::similar( array( 10, 13, 19 ), array( $vars['nav-bg-color'], $vars['body-bg-color'] ) );
			$vars['nav-fg-color']               = FLColor::hex( array( $mods['fl-nav-link-color'], $vars['text-color'] ) );
			$vars['nav-fg-link-color']          = FLColor::hex( array( $mods['fl-nav-link-color'], $vars['text-color'] ) );
			$vars['nav-fg-hover-color']         = FLColor::hex( array( $mods['fl-nav-hover-color'], $vars['nav-fg-color'] ) );
		}
		
		// Nav Dropdown Colors
		$vars['nav-dropdown-bg-color']			= FLColor::hex( array( $vars['nav-bg-color'], $vars['body-bg-color'] ) );
		
		// Mobile Nav Colors
		$vars['mobile-nav-btn-color']       	= FLColor::similar( array( 10, 13, 19 ), array( $vars['header-bg-color'], $vars['body-bg-color'] ) );
		$vars['mobile-nav-fg-color']        	= $vars['header-fg-color'];
		$vars['mobile-nav-fg-link-color']   	= $vars['header-fg-link-color'];
		$vars['mobile-nav-fg-hover-color']  	= $vars['header-fg-hover-color'];
		
		// Content Background Image
		$vars['content-bg-image']               = empty( $mods['fl-content-bg-image'] ) ? 'none' : 'url(' . $mods['fl-content-bg-image'] . ')';
		$vars['content-bg-repeat']              = $mods['fl-content-bg-repeat'];
		$vars['content-bg-position']            = $mods['fl-content-bg-position'];
		$vars['content-bg-attachment']          = $mods['fl-content-bg-attachment'];
		$vars['content-bg-size']                = $mods['fl-content-bg-size'];

		// Content Colors
		$vars['content-bg-color']               = FLColor::hex( $mods['fl-content-bg-color'] );
		
		if ( ! FLColor::is_hex( $vars['content-bg-color'] ) ) {
			$vars['content-bg-color-2']             = $vars['body-bg-color-2'];
			$vars['content-bg-color-3']             = $vars['body-bg-color-3'];
			$vars['border-color']                   = $vars['body-border-color'];
			$vars['border-color-2']                 = $vars['body-border-color-2'];
			$vars['content-fg-color']               = $vars['body-fg-color'];
		}
		else {
			$vars['content-bg-color-2']             = FLColor::similar( array( 1, 4, 13 ), $vars['content-bg-color'] );
			$vars['content-bg-color-3']             = FLColor::similar( array( 3, 9, 18 ), $vars['content-bg-color'] );
			$vars['border-color']                   = FLColor::similar( array( 10, 9, 19 ), $vars['content-bg-color'] );
			$vars['border-color-2']                 = FLColor::similar( array( 20, 20, 30 ), $vars['content-bg-color'] );
			$vars['content-fg-color']               = FLColor::foreground( $vars['content-bg-color'] );
		}
		
		// Inputs Colors
		$vars['input-bg-color']               	= FLColor::hex( array( $vars['content-bg-color-2'], $vars['body-bg-color-2'], '#fcfcfc' ) );
		$vars['input-bg-focus-color']           = FLColor::hex( array( $vars['content-bg-color'], $vars['body-bg-color'], '#ffffff' ) );
		$vars['input-border-color']             = FLColor::hex( array( $vars['border-color'], $vars['body-border-color'], '#e6e6e6' ) );
		$vars['input-border-focus-color']       = FLColor::hex( array( $vars['border-color-2'], $vars['body-border-color-2'], '#cccccc' ) );
		
		// Footer Widget Background Image
		$vars['footer-widgets-bg-image']        = empty( $mods['fl-footer-widgets-bg-image'] ) ? 'none' : 'url(' . $mods['fl-footer-widgets-bg-image'] . ')';
		$vars['footer-widgets-bg-repeat']       = $mods['fl-footer-widgets-bg-repeat'];
		$vars['footer-widgets-bg-position']     = $mods['fl-footer-widgets-bg-position'];
		$vars['footer-widgets-bg-attachment']   = $mods['fl-footer-widgets-bg-attachment'];
		$vars['footer-widgets-bg-size']         = $mods['fl-footer-widgets-bg-size'];

		// Footer Widget Colors
		$vars['footer-widgets-bg-color']		= FLColor::hex_or_transparent( $mods['fl-footer-widgets-bg-color'] );
		$vars['footer-widgets-bg-grad']         = $mods['fl-footer-widgets-bg-gradient'] ? 15 : 0;
		$vars['footer-widgets-border-color']    = FLColor::similar( array( 10, 13, 19 ), array( $vars['footer-widgets-bg-color'], $vars['body-bg-color'] ) );
		$vars['footer-widgets-fg-color']        = FLColor::hex( array( $mods['fl-footer-widgets-text-color'], $vars['text-color'] ) );
		$vars['footer-widgets-fg-link-color']   = FLColor::hex( array( $mods['fl-footer-widgets-link-color'], $vars['footer-widgets-fg-color'] ) );
		$vars['footer-widgets-fg-hover-color']  = FLColor::hex( array( $mods['fl-footer-widgets-hover-color'], $vars['footer-widgets-fg-color'] ) );
		
		// Footer Background Image
		$vars['footer-bg-image']        		= empty( $mods['fl-footer-bg-image'] ) ? 'none' : 'url(' . $mods['fl-footer-bg-image'] . ')';
		$vars['footer-bg-repeat']       		= $mods['fl-footer-bg-repeat'];
		$vars['footer-bg-position']     		= $mods['fl-footer-bg-position'];
		$vars['footer-bg-attachment']   		= $mods['fl-footer-bg-attachment'];
		$vars['footer-bg-size']         		= $mods['fl-footer-bg-size'];

		// Footer Colors
		$vars['footer-bg-color']				= FLColor::hex_or_transparent( $mods['fl-footer-bg-color'] );
		$vars['footer-bg-grad']         		= $mods['fl-footer-bg-gradient'] ? 8 : 0;
		$vars['footer-border-color']    		= FLColor::similar( array( 10, 13, 19 ), array( $vars['footer-bg-color'], $vars['body-bg-color'] ) );
		$vars['footer-fg-color']        		= FLColor::hex( array( $mods['fl-footer-text-color'], $vars['text-color'] ) );
		$vars['footer-fg-link-color']           = FLColor::hex( array( $mods['fl-footer-link-color'], $vars['footer-fg-color'] ) );
		$vars['footer-fg-hover-color']          = FLColor::hex( array( $mods['fl-footer-hover-color'], $vars['footer-fg-color'] ) );
		
		// WooCommerce
		if ( FLTheme::is_plugin_active( 'woocommerce' ) ) {
			$vars['woo-cats-add-button']        = $mods['fl-woo-cart-button'] == 'hidden' ? 'none' : 'inline-block';
		}

		// Build the vars string
		foreach ( $vars as $key => $value ) {
			$vars_string .= '@' . $key . ':' . $value . ';';
		}

		// Return the vars string
		return $vars_string;
	}

	/**
	 * Builds a font family string using the provided font key.
	 *
	 * @since 1.2.0
	 * @access private
	 * @param string $font The font key.
	 * @return string
	 */
	static private function _get_font_family_string( $font )
	{
		$string = '';

		if ( isset( FLFontFamilies::$system[ $font ] ) ) {
			$string = $font . ', ' . FLFontFamilies::$system[ $font ]['fallback'];
		}
		else {
			$string = '"' . $font . '", sans-serif';
		}

		return $string;
	}

	/**
	 * Regx replace callback for LESS to fix issues with IE filters.
	 *
	 * @since 1.2.0
	 * @access private
	 * @param array $matches
	 * @return string
	 */
	static private function _preg_replace_less( $matches )
	{
		if ( ! empty( $matches[1] ) ) {
			return 'filter: ~"' . $matches[1] . '";';
		}

		return $matches[0];
	}
}