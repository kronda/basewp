<?php
/**
 * @package Make
 */

if ( ! function_exists( 'ttfmake_customizer_define_typography_sections' ) ) :
/**
 * Define the sections and settings for the General panel
 *
 * @since  1.3.0.
 *
 * @param  array    $sections    The master array of Customizer sections
 * @return array                 The augmented master array
 */
function ttfmake_customizer_define_typography_sections( $sections ) {
	$panel = 'ttfmake_typography';
	$typography_sections = array();

	/**
	 * Global
	 */
	$typography_sections['font'] = array(
		'panel'   => $panel,
		'title'   => __( 'Global', 'make' ),
		'options' => array_merge(
			ttfmake_customizer_typography_group_definitions( 'body', __( 'Default', 'make' ) ),
			array(
				'body-link-group' => array(
					'control' => array(
						'control_type' => 'TTFMAKE_Customize_Misc_Control',
						'label'   => __( 'Links', 'make' ),
						'type'  => 'group-title',
					),
				),
				'font-weight-body-link' => array(
					'setting' => array(
						'sanitize_callback' => 'ttfmake_sanitize_choice',
					),
					'control' => array(
						'control_type' => 'TTFMAKE_Customize_Radio_Control',
						'label'   => __( 'Font Weight', 'make' ),
						'type'  => 'radio',
						'mode'  => 'buttonset',
						'choices' => ttfmake_get_choices( 'font-weight-body-link' ),
					),
				),
			)
		),
	);

	/**
	 * Text Headers
	 */
	$typography_sections['font-headers'] = array(
		'panel'   => $panel,
		'title'   => __( 'Text Headers', 'make' ),
		'options' => array_merge(
			ttfmake_customizer_typography_group_definitions( 'h1', __( 'H1', 'make' ) ),
			ttfmake_customizer_typography_group_definitions( 'h2', __( 'H2', 'make' ) ),
			ttfmake_customizer_typography_group_definitions( 'h3', __( 'H3', 'make' ) ),
			ttfmake_customizer_typography_group_definitions( 'h4', __( 'H4', 'make' ) ),
			ttfmake_customizer_typography_group_definitions( 'h5', __( 'H5', 'make' ) ),
			ttfmake_customizer_typography_group_definitions( 'h6', __( 'H6', 'make' ) )
		),
	);

	/**
	 * Site Title & Tagline
	 */
	$typography_sections['font-site-title-tagline'] = array(
		'panel'   => $panel,
		'title'   => __( 'Site Title &amp; Tagline', 'make' ),
		'options' => array_merge(
			ttfmake_customizer_typography_group_definitions( 'site-title', __( 'Site Title', 'make' ) ),
			ttfmake_customizer_typography_group_definitions( 'site-tagline', __( 'Tagline', 'make' ) )
		),
	);

	/**
	 * Main Navigation
	 */
	$typography_sections['font-main-menu'] = array(
		'panel'   => $panel,
		'title'   => __( 'Main Menu', 'make' ),
		'options' => array_merge(
			ttfmake_customizer_typography_group_definitions( 'nav', __( 'Menu Items', 'make' ) ),
			ttfmake_customizer_typography_group_definitions( 'subnav', __( 'Sub-Menu Items', 'make' ) ),
			array(
				'font-nav-mobile-option-heading' => array(
					'control' => array(
						'control_type' => 'TTFMAKE_Customize_Misc_Control',
						'type'         => 'heading',
						'label'        => __( 'Mobile', 'make' ),
					),
				),
				'font-subnav-mobile'         => array(
					'setting' => array(
						'sanitize_callback' => 'absint',
					),
					'control' => array(
						'label' => __( 'Use Menu Item styles in mobile view', 'make' ),
						'type'  => 'checkbox',
					),
				),
				'font-nav-current-item-option-heading' => array(
					'control' => array(
						'control_type' => 'TTFMAKE_Customize_Misc_Control',
						'type'         => 'group-title',
						'label'        => __( 'Current Item', 'make' ),
					),
				),
				'font-weight-nav-current-item' => array(
					'setting' => array(
						'sanitize_callback' => 'ttfmake_sanitize_choice',
					),
					'control' => array(
						'control_type' => 'TTFMAKE_Customize_Radio_Control',
						'label'   => __( 'Font Weight', 'make' ),
						'type'  => 'radio',
						'mode'  => 'buttonset',
						'choices' => ttfmake_get_choices( 'font-weight-nav-current-item' ),
					),
				),
			)
		),
	);

	/**
	 * Header Bar
	 */
	$typography_sections['font-header-bar'] = array(
		'panel'   => $panel,
		'title'   => __( 'Header Bar', 'make' ),
		'options' => ttfmake_customizer_typography_group_definitions(
			'header-bar-text',
			__( 'Header Bar Text', 'make' ),
			__( 'Includes Header Text, Header Bar Menu items, and the search field.', 'make' )
		),
	);

	/**
	 * Sidebars
	 */
	$typography_sections['font-sidebar'] = array(
		'panel'   => $panel,
		'title'   => __( 'Sidebars', 'make' ),
		'options' => array_merge(
			ttfmake_customizer_typography_group_definitions( 'widget-title', __( 'Widget Title', 'make' ) ),
			ttfmake_customizer_typography_group_definitions( 'widget', __( 'Widget Body', 'make' ) )
		),
	);

	/**
	 * Footer
	 */
	$typography_sections['font-footer'] = array(
		'panel'   => $panel,
		'title'   => __( 'Footer', 'make' ),
		'options' => array_merge(
			ttfmake_customizer_typography_group_definitions( 'footer-widget-title', __( 'Widget Title', 'make' ) ),
			ttfmake_customizer_typography_group_definitions( 'footer-widget', __( 'Widget Body', 'make' ) ),
			ttfmake_customizer_typography_group_definitions( 'footer-text', __( 'Footer Text', 'make' ) )
		),
	);

	/**
	 * Google Font Subsets
	 */
	$typography_sections['font-google'] = array(
		'panel'   => $panel,
		'title'   => __( 'Google Font Subsets', 'make' ),
		'options' => array(
			'font-subset'      => array(
				'setting' => array(
					'sanitize_callback' => 'ttfmake_sanitize_font_subset',
				),
				'control' => array(
					'label'   => __( 'Character Subset', 'make' ),
					'type'    => 'select',
					'choices' => ttfmake_get_google_font_subsets(),
				),
			),
			'font-subset-text' => array(
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Misc_Control',
					'type'         => 'text',
					'description'  => __( 'Not all fonts provide each of these subsets.', 'make' ),
				),
			),
		),
	);

	/**
	 * Typekit
	 */
	if ( ! ttfmake_is_plus() ) {
		$typography_sections['font-typekit'] = array(
			'panel'       => $panel,
			'title'       => __( 'Typekit', 'make' ),
			'description' => __( 'Looking to add premium fonts from Typekit to your website?', 'make' ),
			'options'     => array(
				'font-typekit-update-text' => array(
					'control' => array(
						'control_type' => 'TTFMAKE_Customize_Misc_Control',
						'type'         => 'text',
						'description'  => sprintf(
							'<a href="%1$s" target="_blank">%2$s</a>',
							esc_url( ttfmake_get_plus_link( 'typekit' ) ),
							sprintf(
								__( 'Upgrade to %1$s', 'make' ),
								'Make Plus'
							)
						),
					),
				)
			)
		);
	}

	/**
	 * Filter the definitions for the controls in the Typography panel of the Customizer.
	 *
	 * @since 1.3.0.
	 *
	 * @param array    $typography_sections    The array of definitions.
	 */
	$typography_sections = apply_filters( 'make_customizer_typography_sections', $typography_sections );

	// Merge with master array
	return array_merge( $sections, $typography_sections );
}
endif;

add_filter( 'make_customizer_sections', 'ttfmake_customizer_define_typography_sections' );

/**
 * Generate an array of Customizer option definitions for a particular HTML element.
 *
 * @since 1.5.0.
 *
 * @param  string    $element
 * @param  string    $label
 * @param  string    $description
 * @return array
 */
function ttfmake_customizer_typography_group_definitions( $element, $label, $description = '' ) {
	$definitions = array(
		'typography-group-' . $element => array(
			'control' => array(
				'control_type' => 'TTFMAKE_Customize_Misc_Control',
				'label'   => $label,
				'description' => $description,
				'type'  => 'group-title',
			),
		),
		'font-family-' . $element   => array(
			'setting' => array(
				'sanitize_callback' => 'ttfmake_sanitize_font_choice',
			),
			'control' => array(
				'label'   => __( 'Font Family', 'make' ),
				'type'    => 'select',
				'choices' => ttfmake_font_choices_placeholder(),
			),
		),
		'font-size-' . $element     => array(
			'setting' => array(
				'sanitize_callback' => 'absint',
			),
			'control' => array(
				'control_type' => 'TTFMAKE_Customize_Range_Control',
				'label'   => __( 'Font Size (px)', 'make' ),
				'type'  => 'range',
				'input_attrs' => array(
					'min'  => 6,
					'max'  => 100,
					'step' => 1,
				),
			),
		),
		'font-weight-' . $element => array(
			'setting' => array(
				'sanitize_callback' => 'ttfmake_sanitize_choice',
			),
			'control' => array(
				'control_type' => 'TTFMAKE_Customize_Radio_Control',
				'label'   => __( 'Font Weight', 'make' ),
				'type'  => 'radio',
				'mode'  => 'buttonset',
				'choices' => ttfmake_get_choices( 'font-weight-' . $element ),
			),
		),
		'font-style-' . $element => array(
			'setting' => array(
				'sanitize_callback' => 'ttfmake_sanitize_choice',
			),
			'control' => array(
				'control_type' => 'TTFMAKE_Customize_Radio_Control',
				'label'   => __( 'Font Style', 'make' ),
				'type'  => 'radio',
				'mode'  => 'buttonset',
				'choices' => ttfmake_get_choices( 'font-style-' . $element ),
			),
		),
		'text-transform-' . $element => array(
			'setting' => array(
				'sanitize_callback' => 'ttfmake_sanitize_choice',
			),
			'control' => array(
				'control_type' => 'TTFMAKE_Customize_Radio_Control',
				'label'   => __( 'Text Transform', 'make' ),
				'type'  => 'radio',
				'mode'  => 'buttonset',
				'choices' => ttfmake_get_choices( 'text-transform-' . $element ),
			),
		),
		'line-height-' . $element     => array(
			'setting' => array(
				'sanitize_callback' => 'ttfmake_sanitize_float',
			),
			'control' => array(
				'control_type' => 'TTFMAKE_Customize_Range_Control',
				'label'   => __( 'Line Height (em)', 'make' ),
				'type'  => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 5,
					'step' => 0.1,
				),
			),
		),
		'letter-spacing-' . $element     => array(
			'setting' => array(
				'sanitize_callback' => 'ttfmake_sanitize_float',
			),
			'control' => array(
				'control_type' => 'TTFMAKE_Customize_Range_Control',
				'label'   => __( 'Letter Spacing (px)', 'make' ),
				'type'  => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 10,
					'step' => 0.5,
				),
			),
		),
		'word-spacing-' . $element     => array(
			'setting' => array(
				'sanitize_callback' => 'ttfmake_sanitize_float',
			),
			'control' => array(
				'control_type' => 'TTFMAKE_Customize_Range_Control',
				'label'   => __( 'Word Spacing (px)', 'make' ),
				'type'  => 'range',
				'input_attrs' => array(
					'min'  => 0,
					'max'  => 20,
					'step' => 1,
				),
			),
		),
		'link-underline-' . $element => array(
			'setting' => array(
				'sanitize_callback' => 'ttfmake_sanitize_choice',
			),
			'control' => array(
				'control_type' => 'TTFMAKE_Customize_Radio_Control',
				'label'   => __( 'Link Underline', 'make' ),
				'type'  => 'radio',
				'mode'  => 'buttonset',
				'choices' => ttfmake_get_choices( 'link-underline-' . $element ),
			),
		),
	);

	/**
	 * Filter the Customizer's font control definitions.
	 *
	 * @since 1.5.0.
	 *
	 * @param array     $definitions    Array of Customizer options and their setting and control definitions.
	 * @param string    $element        The HTML element that the font properties will apply to.
	 */
	return apply_filters( 'make_customizer_typography_group_definitions', $definitions, $element );
}

if ( ! function_exists( 'ttfmake_font_choices_placeholder' ) ) :
/**
 * Add a placeholder for the large font choices array, which will be loaded
 * in via JavaScript.
 *
 * @since 1.3.0.
 *
 * @return array
 */
function ttfmake_font_choices_placeholder() {
	return array( 'placeholder' => __( 'Loading&hellip;', 'make' ) );
}
endif;