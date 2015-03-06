<?php
/**
 * @package Make Plus
 */

if ( ! function_exists( 'ttfmake_customizer_layout_shop' ) ) :
/**
 * Configure settings and controls for the Layout: Shop section.
 *
 * @since  1.0.0.
 *
 * @param  object    $wp_customize    The global customizer object.
 * @param  string    $section         The section name.
 * @return void
 */
function ttfmake_customizer_layout_shop( $wp_customize, $section ) {
	$priority       = new TTFMAKE_Prioritizer();
	$control_prefix = 'ttfmake_';
	$setting_prefix = str_replace( $control_prefix, '', $section );

	// Section info
	$setting_id = $setting_prefix . '-section-info';
	$description = apply_filters( 'ttfmp_shop_layout_shop_description', '' );
	if ( '' !== $description ) {
		$wp_customize->add_control(
			new TTFMAKE_Customize_Misc_Control(
				$wp_customize,
				$control_prefix . $setting_id,
				array(
					'section'     => $section,
					'type'        => 'text',
					'description' => ttfmake_sanitize_text( $description ),
					'priority'    => $priority->add()
				)
			)
		);
	}

	// Header, Footer, Sidebars heading
	$setting_id = $setting_prefix . '-heading-sidebars';
	$wp_customize->add_control(
		new TTFMAKE_Customize_Misc_Control(
			$wp_customize,
			$control_prefix . $setting_id,
			array(
				'section'  => $section,
				'type'     => 'heading',
				'label'    => __( 'Header, Footer, Sidebars', 'make-plus' ),
				'priority' => $priority->add()
			)
		)
	);

	// Hide site header
	$setting_id = $setting_prefix . '-hide-header';
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
			'label'    => __( 'Hide site header', 'make-plus' ),
			'type'     => 'checkbox',
			'priority' => $priority->add()
		)
	);

	// Hide site footer
	$setting_id = $setting_prefix . '-hide-footer';
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
			'label'    => __( 'Hide site footer', 'make-plus' ),
			'type'     => 'checkbox',
			'priority' => $priority->add()
		)
	);

	// Left sidebar
	$setting_id = $setting_prefix . '-sidebar-left';
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
			'label'    => __( 'Show left sidebar', 'make-plus' ),
			'type'     => 'checkbox',
			'priority' => $priority->add()
		)
	);

	// Right sidebar
	$setting_id = $setting_prefix . '-sidebar-right';
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
			'label'    => __( 'Show right sidebar', 'make-plus' ),
			'type'     => 'checkbox',
			'priority' => $priority->add()
		)
	);
}
endif;