<?php
/**
 * @package Make
 */

if ( ! function_exists( 'ttfmake_customizer_navigation' ) ) :
/**
 * Configure settings and controls for the Navigation section.
 *
 * @since  1.0.0.
 *
 * @return void
 */
function ttfmake_customizer_navigation() {
	global $wp_customize;
	$theme_prefix = 'ttfmake_';
	$section_id = 'nav';
	// The Navigation section only exists if custom menus have been created.
	if ( ! isset( $wp_customize->get_section( $section_id )->title ) ) {
		$wp_customize->add_section( 'nav' );
	}
	$section = $wp_customize->get_section( $section_id );
	$priority = new TTFMAKE_Prioritizer( 10, 5 );

	// Move Navigation section to General panel
	$section->panel = $theme_prefix . 'general';

	// Set Navigation section priority
	$logo_priority = $wp_customize->get_section( $theme_prefix . 'logo' )->priority;
	$section->priority = $logo_priority + 5;

	// Adjust section title if no panel support
	if ( ! ttfmake_customizer_supports_panels() ) {
		$panels = ttfmake_customizer_get_panels();
		if ( isset( $panels['general']['title'] ) ) {
			$section->title = $panels['general']['title'] . ': ' . $section->title;
		}
	}

	// Move the Social Profile Links option to the General > Social section, if a custom menu has been created
	$social_menu = $wp_customize->get_control( 'nav_menu_locations[social]' );
	if ( $social_menu ) {
		$social_menu->section = $theme_prefix . 'social';
		$social_menu->description = __( 'After you have created your custom menu for social profile links, select it here.', 'make' );
		$custom_menu_text_priority = $wp_customize->get_control( $theme_prefix . 'social-custom-menu-text' )->priority;
		$social_menu->priority = $custom_menu_text_priority + 1;
	}

	/**
	 * Adjust Navigation section description
	 *
	 * Since the Social Profile Links item gets moved to another section, the part of the description that
	 * states the number of menu locations is misleading.
	 *
	 * Only show this description if a custom menu has been created, thus exposing the menu location options.
	 */
	$primary_menu = $wp_customize->get_control( 'nav_menu_locations[primary]' );
	if ( $primary_menu ) {
		$section->description = __( 'Select which menu appears in each location. You can edit your menu content on the Menus screen in the Appearance section.', 'make' );
	} else {
		$section->description = '';
	}
}
endif;

add_action( 'customize_register', 'ttfmake_customizer_navigation', 20 );