<?php
/**
 * @package Make
 */

if ( ! function_exists( 'ttfmake_customizer_define_colorscheme_sections' ) ) :
/**
 * Define the sections and settings for the General panel
 *
 * @since  1.3.0.
 *
 * @param  array    $sections    The master array of Customizer sections
 * @return array                 The augmented master array
 */
function ttfmake_customizer_define_colorscheme_sections( $sections ) {
	$panel = 'ttfmake_color-scheme';
	$colorscheme_sections = array();

	/**
	 * General
	 */
	$colorscheme_sections['color'] = array(
		'panel'   => $panel,
		'title'   => __( 'Global', 'make' ),
		'options' => array(
			'color-group-color-scheme' => array(
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Misc_Control',
					'label'   => __( 'Color Scheme', 'make' ),
					'type'  => 'group-title',
				),
			),
			'color-primary'   => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Primary Color', 'make' ),
					'description'  => sprintf(
						__( 'Used for: %s', 'make' ),
						__( 'links', 'make' )
					),
				),
			),
			'color-secondary' => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Secondary Color', 'make' ),
					'description'  => sprintf(
						__( 'Used for: %s', 'make' ),
						__( 'form inputs, table borders, ruled lines, slider buttons', 'make' )
					),
				),
			),
			'color-text'      => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Text Color', 'make' ),
					'description'  => sprintf(
						__( 'Used for: %s', 'make' ),
						__( 'most text', 'make' )
					),
				),
			),
			'color-detail'    => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Detail Color', 'make' ),
					'description'  => sprintf(
						__( 'Used for: %s', 'make' ),
						__( 'UI icons', 'make' )
					),
				),
			),
			'color-group-global-link' => array(
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Misc_Control',
					'label'   => __( 'Links', 'make' ),
					'type'  => 'group-title',
				),
			),
			'color-primary-link'    => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Link Hover/Focus Color', 'make' ),
					'description'  => __( 'The default link color is controlled by the "Primary Color" option above.' ),
				),
			),
			'color-group-global-background' => array(
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Misc_Control',
					'label'   => __( 'Background', 'make' ),
					'type'  => 'group-title',
				),
			),
			// Site Background Color gets inserted here.
			'main-background-color-heading' => array(
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Misc_Control',
					'label'   => __( 'Main Column Background Color', 'make' ),
					'type'  => 'heading',
				),
			),
			'main-background-color' => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
				),
			),
			'main-background-color-opacity'     => array(
				'setting' => array(
					'sanitize_callback' => 'ttfmake_sanitize_float',
				),
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Range_Control',
					'label'   => __( 'Opacity', 'make' ),
					'type'  => 'range',
					'input_attrs' => array(
						'min'  => 0,
						'max'  => 1.01, // Needs to be slightly over 1 to handle rounding error.
						'step' => 0.05,
					),
				),
			),
		),
	);

	/**
	 * Background
	 *
	 * @deprecated 1.5.0.
	 */
	$colorscheme_sections['color-background'] = array(
		'panel'   => $panel,
		'title'   => __( 'Background', 'make' ),
		'options' => array(),
	);

	/**
	 * Site Header
	 */
	$colorscheme_sections['color-header'] = array(
		'panel'   => $panel,
		'title'   => __( 'Site Header', 'make' ),
		'options' => array(
			'header-text-color'           => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Text Color', 'make' ),
				),
			),
			'header-background-color-heading' => array(
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Misc_Control',
					'label'   => __( 'Background Color', 'make' ),
					'type'  => 'heading',
				),
			),
			'header-background-color'     => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
				),
			),
			'header-background-color-opacity'     => array(
				'setting' => array(
					'sanitize_callback' => 'ttfmake_sanitize_float',
				),
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Range_Control',
					'label'   => __( 'Opacity', 'make' ),
					'type'  => 'range',
					'input_attrs' => array(
						'min'  => 0,
						'max'  => 1.01, // Needs to be slightly over 1 to handle rounding error.
						'step' => 0.05,
					),
				),
			),
		),
	);

	/**
	 * Site Title & Tagline
	 */
	$colorscheme_sections['color-site-title-tagline'] = array(
		'panel'   => $panel,
		'title'   => __( 'Site Title &amp; Tagline', 'make' ),
		'description' => sprintf(
			__( 'These options override the %s option in the %s section.', 'make' ),
			__( 'Text Color', 'make' ),
			__( 'Site Header', 'make' )
		),
		'options' => array(
			'color-site-title'            => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Site Title Color', 'make' ),
				),
			),
			'color-site-tagline'            => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Tagline Color', 'make' ),
				),
			),
		),
	);

	/**
	 * Main Menu
	 */
	$colorscheme_sections['color-main-menu'] = array(
		'panel'   => $panel,
		'title'   => __( 'Main Menu', 'make' ),
		'options' => array(
			'color-group-nav-item' => array(
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Misc_Control',
					'label'   => __( 'Menu Items', 'make' ),
					'type'  => 'group-title',
				),
			),
			'color-nav-text'            => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Text Color', 'make' ),
					'description' => sprintf(
						__( 'This option overrides the %s option in the %s section.', 'make' ),
						__( 'Text Color', 'make' ),
						__( 'Site Header', 'make' )
					),
				),
			),
			'color-nav-text-hover'            => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Hover/Focus Text Color', 'make' ),
				),
			),
			'color-group-subnav-item' => array(
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Misc_Control',
					'label'   => __( 'Sub-Menu Items', 'make' ),
					'type'  => 'group-title',
				),
			),
			'color-subnav-text'            => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Text Color', 'make' ),
					'description' => sprintf(
						__( 'This option overrides the %s option in the %s section.', 'make' ),
						__( 'Text Color', 'make' ),
						__( 'Site Header', 'make' )
					),
				),
			),
			'color-subnav-text-hover'            => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Hover/Focus Text Color', 'make' ),
				),
			),
			'color-subnav-detail'            => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Detail Color', 'make' ),
					'description' => sprintf(
						__( 'This option overrides the %s option in the %s section.', 'make' ),
						__( 'Detail Color', 'make' ),
						__( 'Global', 'make' )
					),
				),
			),
			'color-subnav-background'            => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Background Color', 'make' ),
					'description' => sprintf(
						__( 'This option overrides the %s option in the %s section.', 'make' ),
						__( 'Secondary Color', 'make' ),
						__( 'Global', 'make' )
					),
				),
			),
			'color-subnav-background-opacity'     => array(
				'setting' => array(
					'sanitize_callback' => 'ttfmake_sanitize_float',
				),
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Range_Control',
					'label'   => __( 'Opacity', 'make' ),
					'type'  => 'range',
					'input_attrs' => array(
						'min'  => 0,
						'max'  => 1.01, // Needs to be slightly over 1 to handle rounding error.
						'step' => 0.05,
					),
				),
			),
			'color-subnav-background-hover'            => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Hover/Focus Background Color', 'make' ),
					'description' => sprintf(
						__( 'This option overrides the %s option in the %s section.', 'make' ),
						__( 'Primary Color', 'make' ),
						__( 'Global', 'make' )
					),
				),
			),
			'color-subnav-background-hover-opacity'     => array(
				'setting' => array(
					'sanitize_callback' => 'ttfmake_sanitize_float',
				),
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Range_Control',
					'label'   => __( 'Opacity', 'make' ),
					'type'  => 'range',
					'input_attrs' => array(
						'min'  => 0,
						'max'  => 1.01, // Needs to be slightly over 1 to handle rounding error.
						'step' => 0.05,
					),
				),
			),
			'color-group-current-item' => array(
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Misc_Control',
					'label'   => __( 'Current Item', 'make' ),
					'type'  => 'group-title',
				),
			),
			'color-nav-current-item-background'            => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Background Color', 'make' ),
				),
			),
			'color-nav-current-item-background-opacity'     => array(
				'setting' => array(
					'sanitize_callback' => 'ttfmake_sanitize_float',
				),
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Range_Control',
					'label'   => __( 'Opacity', 'make' ),
					'type'  => 'range',
					'input_attrs' => array(
						'min'  => 0,
						'max'  => 1.01, // Needs to be slightly over 1 to handle rounding error.
						'step' => 0.05,
					),
				),
			),
		),
	);

	/**
	 * Header Bar
	 */
	$colorscheme_sections['color-header-bar'] = array(
		'panel'   => $panel,
		'title'   => __( 'Header Bar', 'make' ),
		'options' => array(
			'header-bar-text-color'       => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Text Color', 'make' ),
				),
			),
			'header-bar-link-color'       => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Link Color', 'make' ),
				),
			),
			'header-bar-link-hover-color'       => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Link Hover/Focus Color', 'make' ),
					'description' => sprintf(
						__( 'This option overrides the %s option in the %s section.', 'make' ),
						__( 'Link Hover/Focus Color', 'make' ),
						__( 'Global', 'make' )
					),
				),
			),
			'header-bar-border-color'     => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Border Color', 'make' ),
				),
			),
			'header-bar-background-color-heading' => array(
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Misc_Control',
					'label'   => __( 'Background Color', 'make' ),
					'type'  => 'heading',
				),
			),
			'header-bar-background-color' => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
				),
			),
			'header-bar-background-color-opacity'     => array(
				'setting' => array(
					'sanitize_callback' => 'ttfmake_sanitize_float',
				),
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Range_Control',
					'label'   => __( 'Opacity', 'make' ),
					'type'  => 'range',
					'input_attrs' => array(
						'min'  => 0,
						'max'  => 1.01, // Needs to be slightly over 1 to handle rounding error.
						'step' => 0.05,
					),
				),
			),
		),
	);

	/**
	 * Sidebars
	 */
	$colorscheme_sections['color-sidebar'] = array(
		'panel'   => $panel,
		'title'   => __( 'Sidebars', 'make' ),
		'options' => array(
			'color-group-sidebar-widget' => array(
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Misc_Control',
					'label'   => __( 'Widgets', 'make' ),
					'type'  => 'group-title',
				),
			),
			'color-widget-title-text'            => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Widget Title Color', 'make' ),
					'description' => sprintf(
						__( 'This option overrides the %s option in the %s section.', 'make' ),
						__( 'Text Color', 'make' ),
						__( 'Global', 'make' )
					),
				),
			),
			'color-widget-text'            => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Widget Body Color', 'make' ),
					'description' => sprintf(
						__( 'This option overrides the %s option in the %s section.', 'make' ),
						__( 'Text Color', 'make' ),
						__( 'Global', 'make' )
					),
				),
			),
			'color-widget-border'            => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Border Color', 'make' ),
					'description' => sprintf(
						__( 'This option overrides the %s option in the %s section.', 'make' ),
						__( 'Secondary Color', 'make' ),
						__( 'Global', 'make' )
					),
				),
			),
			'color-group-sidebar-link' => array(
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Misc_Control',
					'label'   => __( 'Links', 'make' ),
					'type'  => 'group-title',
				),
			),
			'color-widget-link'            => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Link Color', 'make' ),
					'description' => sprintf(
						__( 'This option overrides the %s option in the %s section.', 'make' ),
						__( 'Primary Color', 'make' ),
						__( 'Global', 'make' )
					),
				),
			),
			'color-widget-link-hover'            => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Link Hover/Focus Color', 'make' ),
				),
			),
		),
	);

	/**
	 * Footer
	 */
	$colorscheme_sections['color-footer'] = array(
		'panel'   => $panel,
		'title'   => __( 'Footer', 'make' ),
		'options' => array(
			'footer-text-color'       => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Footer Text Color', 'make' ),
				),
			),
			'footer-link-color'       => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Link Color', 'make' ),
					'description' => sprintf(
						__( 'This option overrides the %s option in the %s section.', 'make' ),
						__( 'Primary Color', 'make' ),
						__( 'Global', 'make' )
					),
				),
			),
			'footer-link-hover-color'       => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Link Hover/Focus Color', 'make' ),
					'description' => sprintf(
						__( 'This option overrides the %s option in the %s section.', 'make' ),
						__( 'Link Hover/Focus Color', 'make' ),
						__( 'Global', 'make' )
					),
				),
			),
			'footer-border-color'     => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
					'label'        => __( 'Border Color', 'make' ),
				),
			),
			'footer-background-color-heading' => array(
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Misc_Control',
					'label'   => __( 'Background Color', 'make' ),
					'type'  => 'heading',
				),
			),
			'footer-background-color' => array(
				'setting' => array(
					'sanitize_callback' => 'maybe_hash_hex_color',
				),
				'control' => array(
					'control_type' => 'WP_Customize_Color_Control',
				),
			),
			'footer-background-color-opacity'     => array(
				'setting' => array(
					'sanitize_callback' => 'ttfmake_sanitize_float',
				),
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Range_Control',
					'label'   => __( 'Opacity', 'make' ),
					'type'  => 'range',
					'input_attrs' => array(
						'min'  => 0,
						'max'  => 1.01, // Needs to be slightly over 1 to handle rounding error.
						'step' => 0.05,
					),
				),
			),
		),
	);

	/**
	 * Filter the definitions for the controls in the Color Scheme panel of the Customizer.
	 *
	 * @since 1.3.0.
	 *
	 * @param array    $colorscheme_sections    The array of definitions.
	 */
	$colorscheme_sections = apply_filters( 'make_customizer_colorscheme_sections', $colorscheme_sections );

	// Merge with master array
	return array_merge( $sections, $colorscheme_sections );
}
endif;

add_filter( 'make_customizer_sections', 'ttfmake_customizer_define_colorscheme_sections' );