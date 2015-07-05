<?php
/**
 * @package Make
 */

/**
 * Add notice if Make Plus is installed as a theme.
 *
 * @since  1.1.2.
 *
 * @param  string         $source           File source location.
 * @param  string         $remote_source    Remove file source location.
 * @param  WP_Upgrader    $upgrader         WP_Upgrader instance.
 * @return WP_Error                         Error or source on success.
 */
function ttfmake_check_package( $source, $remote_source, $upgrader ) {
	global $wp_filesystem;

	if ( ! isset( $_GET['action'] ) || 'upload-theme' !== $_GET['action'] ) {
		return $source;
	}

	if ( is_wp_error( $source ) ) {
		return $source;
	}

	// Check the folder contains a valid theme
	$working_directory = str_replace( $wp_filesystem->wp_content_dir(), trailingslashit( WP_CONTENT_DIR ), $source );
	if ( ! is_dir( $working_directory ) ) { // Sanity check, if the above fails, lets not prevent installation.
		return $source;
	}

	// A proper archive should have a style.css file in the single subdirectory
	if ( ! file_exists( $working_directory . 'style.css' ) && strpos( $source, 'make-plus-' ) >= 0 ) {
		return new WP_Error( 'incompatible_archive_theme_no_style', $upgrader->strings[ 'incompatible_archive' ], __( 'The uploaded package appears to be a plugin. PLEASE INSTALL AS A PLUGIN.', 'make' ) );
	}

	return $source;
}

add_filter( 'upgrader_source_selection', 'ttfmake_check_package', 9, 3 );

if ( ! function_exists( 'ttfmake_filter_backcompat' ) ) :
/**
 * Adds back compat for filters with changed names.
 *
 * In Make 1.2.3, filters were all changed from "ttfmake_" to "make_". In order to maintain back compatibility, the old
 * version of the filter needs to still be called. This function collects all of those changed filters and mirrors the
 * new filter so that the old filter name will still work.
 *
 * @since  1.2.3.
 *
 * @return void
 */
function ttfmake_filter_backcompat() {
	// All filters that need a name change
	$old_filters = array(
		'template_content_archive'     => 2,
		'fitvids_custom_selectors'     => 1,
		'template_content_page'        => 2,
		'template_content_search'      => 2,
		'footer_1'                     => 1,
		'footer_2'                     => 1,
		'footer_3'                     => 1,
		'footer_4'                     => 1,
		'sidebar_left'                 => 1,
		'sidebar_right'                => 1,
		'template_content_single'      => 2,
		'get_view'                     => 2,
		'has_sidebar'                  => 3,
		'read_more_text'               => 1,
		'supported_social_icons'       => 1,
		'exif_shutter_speed'           => 2,
		'exif_aperture'                => 2,
		'style_formats'                => 1,
		'prepare_data_section'         => 3,
		'insert_post_data_sections'    => 1,
		'section_classes'              => 2,
		'the_builder_content'          => 1,
		'builder_section_footer_links' => 1,
		'section_defaults'             => 1,
		'section_choices'              => 3,
		'gallery_class'                => 2,
		'builder_banner_class'         => 2,
		'customizer_sections'          => 1,
		'setting_defaults'             => 1,
		'font_relative_size'           => 1,
		'font_stack'                   => 2,
		'font_variants'                => 3,
		'all_fonts'                    => 1,
		'get_google_fonts'             => 1,
		'custom_logo_information'      => 1,
		'custom_logo_max_width'        => 1,
		'setting_choices'              => 2,
		'social_links'                 => 1,
		'show_footer_credit'           => 1,
		'is_plus'                      => 1,
	);

	foreach ( $old_filters as $filter => $args ) {
		add_filter( 'make_' . $filter, 'ttfmake_backcompat_filter', 10, $args );
	}
}
endif;

add_action( 'after_setup_theme', 'ttfmake_filter_backcompat', 1 );

if ( ! function_exists( 'ttfmake_backcompat_filter' ) ) :
/**
 * Prepends "ttf" to a filter name and calls that new filter variant.
 *
 * @since  1.2.3.
 *
 * @return mixed    The result of the filter.
 */
function ttfmake_backcompat_filter() {
	$filter = 'ttf' . current_filter();
	$args   = func_get_args();
	return apply_filters_ref_array( $filter, $args );
}
endif;

if ( ! function_exists( 'ttfmake_action_backcompat' ) ) :
/**
 * Adds back compat for actions with changed names.
 *
 * In Make 1.2.3, actions were all changed from "ttfmake_" to "make_". In order to maintain back compatibility, the old
 * version of the action needs to still be called. This function collects all of those changed actions and mirrors the
 * new filter so that the old filter name will still work.
 *
 * @since  1.2.3.
 *
 * @return void
 */
function ttfmake_action_backcompat() {
	// All filters that need a name change
	$old_actions = array(
		'section_text_before_columns_select' => 1,
		'section_text_after_columns_select'  => 1,
		'section_text_after_title'           => 1,
		'section_text_before_column'         => 2,
		'section_text_after_column'          => 2,
		'section_text_after_columns'         => 1,
		'css'                                => 1,
	);

	foreach ( $old_actions as $action => $args ) {
		add_action( 'make_' . $action, 'ttfmake_backcompat_action', 10, $args );
	}
}
endif;

add_action( 'after_setup_theme', 'ttfmake_action_backcompat', 1 );

if ( ! function_exists( 'ttfmake_backcompat_action' ) ) :
/**
 * Prepends "ttf" to a filter name and calls that new filter variant.
 *
 * @since  1.2.3.
 *
 * @return mixed    The result of the filter.
 */
function ttfmake_backcompat_action() {
	$action = 'ttf' . current_filter();
	$args   = func_get_args();
	do_action_ref_array( $action, $args );
}
endif;

if ( ! function_exists( 'ttfmake_customizer_get_key_conversions' ) ) :
/**
 * Return an array of option key migration sets.
 *
 * @since  1.3.0.
 *
 * @return array    The list of key migration sets.
 */
function ttfmake_customizer_get_key_conversions() {
	// $new_key => $old_key
	$conversions = array(
		'font-family-site-title'      => 'font-site-title',
		'font-family-h1'              => 'font-header',
		'font-family-h2'              => 'font-header',
		'font-family-h3'              => 'font-header',
		'font-family-h4'              => 'font-header',
		'font-family-h5'              => 'font-header',
		'font-family-h6'              => 'font-header',
		'font-family-body'            => 'font-body',
		'font-size-site-title'        => 'font-site-title-size',
		'font-size-site-tagline'      => 'font-site-tagline-size',
		'font-size-nav'               => 'font-nav-size',
		'font-size-h1'                => 'font-header-size',
		'font-size-h2'                => 'font-header-size',
		'font-size-h3'                => 'font-header-size',
		'font-size-h4'                => 'font-header-size',
		'font-size-h5'                => 'font-header-size',
		'font-size-h6'                => 'font-header-size',
		'font-size-widget'            => 'font-widget-size',
		'font-size-body'              => 'font-body-size',
		'social-facebook-official'    => 'social-facebook',
		'main-content-link-underline' => 'link-underline-body',
	);

	/**
	 * Filter the array of Customizer option key conversions.
	 *
	 * The keys for some Customizer options have changed between versions. This array
	 * defines each change as $new_key => $old key.
	 *
	 * @since 1.3.0.
	 *
	 * @param array    $conversions    The array of key conversions.
	 */
	return apply_filters( 'make_customizer_key_conversions', $conversions );
}
endif;

if ( ! function_exists( 'ttfmake_customizer_convert_theme_mods' ) ) :
/**
 * Convert old theme mod values to their newer equivalents.
 *
 * @since  1.3.0.
 *
 * @return void
 */
function ttfmake_customizer_set_up_theme_mod_conversions() {
	// Set up the necessary filters
	foreach ( ttfmake_customizer_get_key_conversions() as $key => $value ) {
		add_filter( 'theme_mod_' . $key, 'ttfmake_customizer_convert_theme_mods_filter', 11 );
	}
}
endif;

add_action( 'after_setup_theme', 'ttfmake_customizer_set_up_theme_mod_conversions', 11 );

if ( ! function_exists( 'ttfmake_customizer_convert_theme_mods_filter' ) ) :
/**
 * Convert a new theme mod value from an old one.
 *
 * @since  1.3.0.
 *
 * @param  mixed    $value    The current value.
 * @return mixed              The modified value.
 */
function ttfmake_customizer_convert_theme_mods_filter( $value ) {
	$new_mod_name = str_replace( 'theme_mod_', '', current_filter() );
	$conversions  = ttfmake_customizer_get_key_conversions();
	$mods         = get_theme_mods();

	/**
	 * When previewing a page, the logic for this filter needs to change. Because the isset check in the conditional
	 * below will always fail if the new mod key is not set (i.e., the value isn't in the db yet), the default value,
	 * instead of the preview value will always show. Instead, when previewing, the value needs to be gotten from
	 * the `get_theme_mod()` call without this filter applied. This will give the new preview value. If it is not found,
	 * then the normal routine will be used.
	 */
	if ( ttfmake_is_preview() ) {
		remove_filter( current_filter(), 'ttfmake_customizer_convert_theme_mods_filter', 11 );
		$previewed_value = get_theme_mod( $new_mod_name, 'default-value' );
		add_filter( current_filter(), 'ttfmake_customizer_convert_theme_mods_filter', 11 );

		if ( 'default-value' !== $previewed_value ) {
			return $previewed_value;
		}
	}

	/**
	 * We only want to convert the value if the new mod is not in the mods array. This means that the value is not set
	 * and an attempt to get the value from an old key is warranted.
	 */
	if ( ! isset( $mods[ $new_mod_name ] ) ) {
		// Verify that this key should be converted
		if ( isset( $conversions[ $new_mod_name ] ) ) {
			$old_mod_name  = $conversions[ $new_mod_name ];
			$old_mod_value = get_theme_mod( $old_mod_name, 'default-value' );

			// The old value is indeed set
			if ( 'default-value' !== $old_mod_value ) {
				$value = $old_mod_value;

				// Now that we have the right old value, convert it if needed
				$value = ttfmake_customizer_convert_theme_mods_values( $old_mod_name, $new_mod_name, $value );
			}
		}
	}

	return $value;
}
endif;

if ( ! function_exists( 'ttfmake_customizer_convert_theme_mods_values' ) ) :
/**
 * This function converts values from old mods to values for new mods.
 *
 * @since  1.3.0.
 *
 * @param  string    $old_key    The old mod key.
 * @param  string    $new_key    The new mod key.
 * @param  mixed     $value      The value of the mod.
 * @return mixed                 The convert mod value.
 */
function ttfmake_customizer_convert_theme_mods_values( $old_key, $new_key, $value ) {
	if ( 'font-header-size' === $old_key ) {
		$percent = ttfmake_font_get_relative_sizes();
		$h       = preg_replace( '/font-size-(h\d)/', '$1', $new_key );
		$value   = ttfmake_get_relative_font_size( $value, $percent[$h] );
	} else if ( 'main-content-link-underline' === $old_key ) {
		if ( 1 == $value ) {
			$value = 'always';
		}
	}

	return $value;
}
endif;

/**
 * Upgrade notices related to Make.
 *
 * @since 1.4.9.
 *
 * @return void
 */
function ttfmake_upgrade_notices() {
	global $wp_version;

	if ( version_compare( $wp_version, TTFMAKE_MIN_WP_VERSION, '<' ) ) {
		ttfmake_register_admin_notice(
			'make-wp-lt-min-version',
			sprintf(
				__( 'Make requires version %1$s of WordPress or higher. Please %2$s to ensure full compatibility.', 'make' ),
				TTFMAKE_MIN_WP_VERSION,
				sprintf(
					'<a href="%1$s">%2$s</a>',
					admin_url( 'update-core.php' ),
					__( 'update WordPress', 'make' )
				)
			),
			array(
				'cap'     => 'update_core',
				'dismiss' => false,
				'screen'  => array( 'dashboard', 'themes.php', 'update-core.php' ),
				'type'    => 'error',
			)
		);
	}
}

add_action( 'admin_init', 'ttfmake_upgrade_notices' );

/**
 * Upgrade notices related to Make Plus.
 *
 * @since 1.4.9.
 *
 * @return void
 */
function ttfmake_plus_upgrade_notices() {
	if ( ttfmake_is_plus() && function_exists( 'ttfmp_get_app' ) ) {
		$make_plus_version = ttfmp_get_app()->version;

		if ( version_compare( $make_plus_version, '1.4.7', '<=' ) ) {
			ttfmake_register_admin_notice(
				'make-plus-lte-147',
				sprintf(
					__( 'A new version of Make Plus is available. If you encounter problems updating through %1$s, please %2$s to update manually.', 'make' ),
					sprintf(
						'<a href="%1$s">%2$s</a>',
						admin_url( 'update-core.php' ),
						__( 'the WordPress interface', 'make' )
					),
					sprintf(
						'<a href="%1$s" target="_blank">%2$s</a>',
						esc_url( 'https://thethemefoundry.com/tutorials/updating-your-existing-theme/' ),
						__( 'follow these steps', 'make' )
					)
				),
				array(
					'cap'    => 'update_plugins',
					'screen' => array( 'dashboard', 'update-core.php', 'plugins.php' ),
					'type'   => 'warning',
				)
			);
		}
	}
}

add_action( 'admin_init', 'ttfmake_plus_upgrade_notices' );

if ( ! function_exists( 'ttfmake_display_favicons' ) ) :
/**
 * Write the favicons to the head to implement the options.
 *
 * This function is deprecated. The functionality was moved to ttfmake_head_late().
 *
 * @since  1.0.0.
 * @deprecated 1.5.0.
 *
 * @return void
 */
function ttfmake_display_favicons() {
	_deprecated_function( __FUNCTION__, '1.5.0' );
}
endif;

if ( ! function_exists( 'ttfmake_body_layout_classes' ) ) :
/**
 * Add theme option body classes.
 *
 * This function is deprecated. The functionality was moved to ttfmake_body_classes().
 *
 * @since  1.0.0.
 * @deprecated 1.5.0.
 *
 * @param  array    $classes    Existing classes.
 * @return array                Modified classes.
 */
function ttfmake_body_layout_classes( $classes ) {
	_deprecated_function( __FUNCTION__, '1.5.0' );
	return $classes;
}
endif;

if ( ! function_exists( 'ttfmake_customizer_define_header_sections' ) ) :
/**
 * Define the sections and settings for the Header panel.
 *
 * @since  1.3.0.
 * @deprecated 1.5.0.
 *
 * @param  array    $sections    The master array of Customizer sections
 * @return array                 The augmented master array
 */
function ttfmake_customizer_define_header_sections( $sections ) {
	_deprecated_function( __FUNCTION__, '1.5.0' );
	return $sections;
}
endif;

if ( ! function_exists( 'ttfmake_customizer_define_footer_sections' ) ) :
/**
 * Define the sections and settings for the Footer panel
 *
 * @since  1.3.0.
 *
 * @param  array    $sections    The master array of Customizer sections
 * @return array                 The augmented master array
 */
function ttfmake_customizer_define_footer_sections( $sections ) {
	_deprecated_function( __FUNCTION__, '1.5.0' );
	return $sections;
}
endif;

if ( ! function_exists( 'ttfmake_css_add_rules' ) ) :
/**
 * Process user options to generate CSS needed to implement the choices.
 *
 * This function has been broken up into several files/functions in the inc/customizer/style directory.
 *
 * @since  1.0.0.
 * @deprecated 1.5.0.
 *
 * @return void
 */
function ttfmake_css_add_rules() {
	_deprecated_function( __FUNCTION__, '1.5.0' );
}
endif;

if ( ! function_exists( 'ttfmake_customizer_supports_panels' ) ) :
/**
 * Detect support for Customizer panels.
 *
 * This feature was introduced in WP 4.0. The WP_Customize_Manager class is not loaded
 * outside of the Customizer, so this also looks for wp_validate_boolean(), another
 * function added in WP 4.0.
 *
 * This function has been deprecated, as Make no longer supports WordPress versions that don't support panels.
 *
 * @since  1.3.0.
 *
 * @return bool    Whether or not panels are supported.
 */
function ttfmake_customizer_supports_panels() {
	_deprecated_function( __FUNCTION__, '1.6.0' );
	return ( class_exists( 'WP_Customize_Manager' ) && method_exists( 'WP_Customize_Manager', 'add_panel' ) ) || function_exists( 'wp_validate_boolean' );
}
endif;

if ( ! function_exists( 'ttfmake_customizer_add_legacy_sections' ) ) :
/**
 * Add the old sections and controls to the customizer for WP installations with no panel support.
 *
 * This function has been deprecated, as Make no longer supports WordPress versions that don't support panels.
 *
 * @since  1.3.0.
 *
 * @param  WP_Customize_Manager    $wp_customize    Theme Customizer object.
 * @return void
 */
function ttfmake_customizer_add_legacy_sections( $wp_customize ) {
	_deprecated_function( __FUNCTION__, '1.6.0' );
}
endif;

if ( ! function_exists( 'ttfmake_css_legacy_fonts' ) ) :
/**
 * Build the CSS rules for the custom fonts.
 *
 * This function has been deprecated, as Make no longer supports WordPress versions that don't support panels.
 *
 * @since  1.0.0.
 *
 * @return void
 */
function ttfmake_css_legacy_fonts() {
	_deprecated_function( __FUNCTION__, '1.6.0' );
}
endif;