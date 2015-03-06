<?php
/**
 * @package Make
 */

if ( ! function_exists( 'ttfmake_customizer_background' ) ) :
/**
 * Configure settings and controls for the Background section.
 *
 * @since  1.0.0.
 *
 * @return void
 */
function ttfmake_customizer_background() {
	global $wp_customize;
	$theme_prefix = 'ttfmake_';
	$section_id = 'background_image';
	$section = $wp_customize->get_section( $section_id );
	$priority = new TTFMAKE_Prioritizer( 10, 5 );

	// Move and rename Background Color control to Global section of Color panel
	$wp_customize->get_control( 'background_color' )->section = $theme_prefix . 'color';
	$wp_customize->get_control( 'background_color' )->label = __( 'Site Background Color', 'make' );
	$wp_customize->get_control( 'background_color' )->priority = (int) $wp_customize->get_control( $theme_prefix . 'color-group-global-background' )->priority + 5;

	// Move Background Image section to Background Images panel
	$section->panel = $theme_prefix . 'background-images';

	// Set section title
	$section->title = __( 'Site', 'make' );

	// Set section priority
	$header_priority = $wp_customize->get_section( $theme_prefix . 'header-background' )->priority;
	$section->priority = $header_priority - 5;

	// Adjust section title if no panel support
	if ( ! ttfmake_customizer_supports_panels() ) {
		$panels = ttfmake_customizer_get_panels();
		if ( isset( $panels['background-images']['title'] ) ) {
			$section->title = $panels['background-images']['title'] . ': ' . $section->title;
		}
	}

	// Reconfigure image and repeat controls
	$wp_customize->get_control( 'background_image' )->label = __( 'Background Image', 'make' );
	$wp_customize->get_control( 'background_image' )->priority = $priority->add();
	$wp_customize->get_control( 'background_repeat' )->label = __( 'Repeat', 'make' );
	$wp_customize->get_control( 'background_repeat' )->priority = $priority->add();

	// Remove position and attachment controls
	$wp_customize->remove_control( 'background_position_x' );
	$wp_customize->remove_control( 'background_attachment' );

	// Add replacement and new controls
	$options = array(
		'background_position_x' => array(
			'setting' => array(
				'sanitize_callback' => 'ttfmake_sanitize_choice',
			),
			'control' => array(
				'control_type' => 'TTFMAKE_Customize_Background_Position_Control',
				'label'   => __( 'Position', 'make' ),
				'type'    => 'radio',
				'choices' => ttfmake_get_choices( 'background_position_x' ),
			),
		),
		'background_attachment'     => array(
			'setting' => array(
				'sanitize_callback' => 'ttfmake_sanitize_choice',
			),
			'control' => array(
				'control_type' => 'TTFMAKE_Customize_Radio_Control',
				'label'   => __( 'Attachment', 'make' ),
				'type'    => 'radio',
				'mode'    => 'buttonset',
				'choices' => ttfmake_get_choices( 'background_attachment' ),
			),
		),
		'background_size' => array(
			'setting' => array(
				'sanitize_callback' => 'ttfmake_sanitize_choice',
			),
			'control' => array(
				'control_type' => 'TTFMAKE_Customize_Radio_Control',
				'label'   => __( 'Size', 'make' ),
				'type'    => 'radio',
				'mode'    => 'buttonset',
				'choices' => ttfmake_get_choices( 'background_size' ),
			),
		),
	);
	$new_priority = ttfmake_customizer_add_section_options( $section_id, $options, $priority->add() );
	$priority->set( $new_priority );
}
endif;

add_action( 'customize_register', 'ttfmake_customizer_background', 20 );