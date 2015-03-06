<?php
/**
 * @package Make
 */

/**
 * Define the sections and settings for the Background Images panel
 *
 * @since 1.5.0.
 *
 * @param  array    $sections    The master array of Customizer sections
 * @return array                 The augmented master array
 */
function ttfmake_customizer_define_background_images_sections( $sections ) {
	$theme_prefix = 'ttfmake_';
	$panel = 'ttfmake_background-images';
	$background_sections = array();

	/**
	 * Header
	 */
	$background_sections['header-background'] = array(
		'panel'   => $panel,
		'title'   => __( 'Header', 'make' ),
		'options' => ttfmake_customizer_background_image_group_definitions( 'header' ),
	);

	/**
	 * Main Column
	 */
	$background_sections['main-background'] = array(
		'panel'   => $panel,
		'title'   => __( 'Main Column', 'make' ),
		'options' => ttfmake_customizer_background_image_group_definitions( 'main' ),
	);

	/**
	 * Footer
	 */
	$background_sections['footer-background'] = array(
		'panel'   => $panel,
		'title'   => __( 'Footer', 'make' ),
		'options' => ttfmake_customizer_background_image_group_definitions( 'footer' ),
	);

	/**
	 * Filter the definitions for the controls in the Background Images panel of the Customizer.
	 *
	 * @since 1.3.0.
	 *
	 * @param array    $header_sections    The array of definitions.
	 */
	$background_sections = apply_filters( 'make_customizer_background_sections', $background_sections );

	// Merge with master array
	return array_merge( $sections, $background_sections );
}

add_filter( 'make_customizer_sections', 'ttfmake_customizer_define_background_images_sections' );

/**
 * Generate an array of Customizer option definitions for a particular HTML element.
 *
 * @since 1.5.0.
 *
 * @param  $region
 * @return array
 */
function ttfmake_customizer_background_image_group_definitions( $region ) {
	$definitions = array(
		$region . '-background-image'    => array(
			'setting' => array(
				'sanitize_callback' => 'esc_url_raw',
			),
			'control' => array(
				'control_type' => 'TTFMAKE_Customize_Image_Control',
				'label'        => __( 'Background Image', 'make' ),
				'context'      => 'ttfmake_' . $region . '-background-image',
			),
		),
		$region . '-background-repeat'   => array(
			'setting' => array(
				'sanitize_callback' => 'ttfmake_sanitize_choice',
			),
			'control' => array(
				'label'   => __( 'Repeat', 'make' ),
				'type'    => 'radio',
				'choices' => ttfmake_get_choices( $region . '-background-repeat' ),
			),
		),
		$region . '-background-position' => array(
			'setting' => array(
				'sanitize_callback' => 'ttfmake_sanitize_choice',
			),
			'control' => array(
				'control_type' => 'TTFMAKE_Customize_Background_Position_Control',
				'label'   => __( 'Position', 'make' ),
				'type'    => 'radio',
				'choices' => ttfmake_get_choices( $region . '-background-position' ),
			),
		),
		$region . '-background-attachment'     => array(
			'setting' => array(
				'sanitize_callback' => 'ttfmake_sanitize_choice',
			),
			'control' => array(
				'control_type' => 'TTFMAKE_Customize_Radio_Control',
				'label'   => __( 'Attachment', 'make' ),
				'type'    => 'radio',
				'mode'    => 'buttonset',
				'choices' => ttfmake_get_choices( $region . '-background-attachment' ),
			),
		),
		$region . '-background-size'     => array(
			'setting' => array(
				'sanitize_callback' => 'ttfmake_sanitize_choice',
			),
			'control' => array(
				'control_type' => 'TTFMAKE_Customize_Radio_Control',
				'label'   => __( 'Size', 'make' ),
				'type'    => 'radio',
				'mode'    => 'buttonset',
				'choices' => ttfmake_get_choices( $region . '-background-size' ),
			),
		),
	);

	/**
	 * Filter the Customizer's background image control definitions.
	 *
	 * @since 1.5.0.
	 *
	 * @param array     $definitions    Array of Customizer options and their setting and control definitions.
	 * @param string    $region         The site region that the background image properties will apply to.
	 */
	return apply_filters( 'make_customizer_background_image_group_definitions', $definitions, $region );
}