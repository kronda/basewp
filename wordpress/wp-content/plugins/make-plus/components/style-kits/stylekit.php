<?php
/**
 * @package Make Plus
 */

if ( ! function_exists( 'ttfmake_customizer_stylekit' ) ) :
/**
 * Configure settings and controls for the Design Packs section.
 *
 * @since  1.1.0.
 *
 * @param  object    $wp_customize    The global customizer object.
 * @param  string    $section         The section name.
 * @return void
 */
function ttfmake_customizer_stylekit( $wp_customize, $section ) {
	$priority       = new TTFMAKE_Prioritizer();
	$control_prefix = 'ttfmake_';
	$setting_prefix = str_replace( $control_prefix, '', $section );

	// Style Kits info
	$setting_id = $setting_prefix . '-info';
	$wp_customize->add_control(
		new TTFMAKE_Customize_Misc_Control(
			$wp_customize,
			$control_prefix . $setting_id,
			array(
				'section'     => $section,
				'type'        => 'text',
				'description' => __( 'Use a style kit to quickly apply designer-picked style choices (fonts, layout, colors) to your website.', 'make-plus' ),
				'priority'    => $priority->add()
			)
		)
	);

	// Style Kits heading
	$setting_id = $setting_prefix . '-heading';
	$wp_customize->add_control(
		new TTFMAKE_Customize_Misc_Control(
			$wp_customize,
			$control_prefix . $setting_id,
			array(
				'section'     => $section,
				'type'        => 'heading',
				'label' => __( 'Kits', 'make-plus' ),
				'priority'    => $priority->add()
			)
		)
	);

	// Style Kits dropdown
	$setting_id = $setting_prefix . '-dropdown';
	$wp_customize->add_control(
		new TTFMAKE_Customize_Misc_Control(
			$wp_customize,
			$control_prefix . $setting_id,
			array(
				'section'     => $section,
				'type'        => 'text',
				'description' => sprintf(
					'<select>%s</select>',
					ttfmp_get_style_kits()->get_kit_options()
				),
				'priority'    => $priority->add()
			)
		)
	);

	// Style Kits buttons
	$setting_id = $setting_prefix . '-buttons';
	$wp_customize->add_control(
		new TTFMAKE_Customize_Misc_Control(
			$wp_customize,
			$control_prefix . $setting_id,
			array(
				'section'     => $section,
				'type'        => 'text',
				'description' => '<a href="#" class="button reset-design">' . __( 'Reset', 'make-plus' ) . '</a><a href="#" class="button load-design">' . __( 'Load Kit', 'make-plus' ) . '</a>',
				'priority'    => $priority->add()
			)
		)
	);
}
endif;