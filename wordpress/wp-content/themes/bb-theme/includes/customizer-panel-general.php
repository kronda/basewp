<?php

/* General Panel */
FLCustomizer::add_panel('fl-general', array(
	'title'    => _x( 'General', 'Customizer panel title.', 'fl-automator' ),
	'sections' => array(

		/* Layout Section */
		'fl-layout' => array(
			'title'   => _x( 'Layout', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Layout Width */
				'fl-layout-width' => array(
					'setting'   => array(
						'default'   => 'full-width'
					),
					'control'   => array(
						'class'         => 'WP_Customize_Control',
						'label'         => __('Layout Width', 'fl-automator'),
						'type'          => 'select',
						'choices'       => array(
							'boxed'             => __('Boxed', 'fl-automator'),
							'full-width'        => __('Full Width', 'fl-automator')
						)
					)
				),

				/* Spacing */
				'fl-layout-spacing' => array(
					'setting'   => array(
						'default'           => '0',
						'sanitize_callback' => 'FLCustomizer::sanitize_number'
					),
					'control'   => array(
						'class'     => 'WP_Customize_Control',
						'label'     => __('Spacing (px)', 'fl-automator'),
						'type'      => 'text'
					)
				),

				/* Drop Shadow Size */
				'fl-layout-shadow-size' => array(
					'setting'   => array(
						'default'   => '0',
						'sanitize_callback' => 'FLCustomizer::sanitize_number'
					),
					'control'   => array(
						'class'     => 'WP_Customize_Control',
						'label'     => __('Drop Shadow Size (px)', 'fl-automator'),
						'type'      => 'text'
					)
				),

				/* Drop Shadow Color */
				'fl-layout-shadow-color' => array(
					'setting'   => array(
						'default'   => '#d9d9d9'
					),
					'control'   => array(
						'class'     => 'WP_Customize_Color_Control',
						'label'     => __('Drop Shadow Color', 'fl-automator')
					)
				),
			)
		),

		/* Body Background Section */
		'fl-body-bg' => array(
			'title'   => _x( 'Background', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Background Color */
				'fl-body-bg-color' => array(
					'setting'   => array(
						'default'   => '#f2f2f2'
					),
					'control'   => array(
						'class'     => 'WP_Customize_Color_Control',
						'label'     => __('Background Color', 'fl-automator')
					)
				),

				/* Background Image */
				'fl-body-bg-image' => array(
					'setting'   => array(
						'default'   => '',
						'transport' => 'postMessage'
					),
					'control'   => array(
						'class'     => 'WP_Customize_Image_Control',
						'label'     => __('Background Image', 'fl-automator')
					)
				),

				/* Background Repeat */
				'fl-body-bg-repeat' => array(
					'setting'   => array(
						'default'   => 'no-repeat',
						'transport' => 'postMessage'
					),
					'control'   => array(
						'class'     => 'WP_Customize_Control',
						'label'     => __('Background Repeat', 'fl-automator'),
						'type'      => 'select',
						'choices'   => array(
							'no-repeat'  => __('None', 'fl-automator'),
							'repeat'     => __('Tile', 'fl-automator'),
							'repeat-x'   => __('Horizontal', 'fl-automator'),
							'repeat-y'   => __('Vertical', 'fl-automator')
						)
					)
				),

				/* Background Position */
				'fl-body-bg-position' => array(
					'setting'   => array(
						'default'   => 'center top',
						'transport' => 'postMessage'
					),
					'control'   => array(
						'class'     => 'WP_Customize_Control',
						'label'     => __('Background Position', 'fl-automator'),
						'type'      => 'select',
						'choices'   => array(
							'left top'      => __( 'Left Top', 'fl-automator' ),
							'left center'   => __( 'Left Center', 'fl-automator' ),
							'left bottom'   => __( 'Left Bottom', 'fl-automator' ),
							'right top'     => __( 'Right Top', 'fl-automator' ),
							'right center'  => __( 'Right Center', 'fl-automator' ),
							'right bottom'  => __( 'Right Bottom', 'fl-automator' ),
							'center top'    => __( 'Center Top', 'fl-automator' ),
							'center center' => __( 'Center', 'fl-automator' ),
							'center bottom' => __( 'Center Bottom', 'fl-automator' )
						)
					)
				),

				/* Background Attachment */
				'fl-body-bg-attachment' => array(
					'setting'   => array(
						'default'   => 'scroll',
						'transport' => 'postMessage'
					),
					'control'   => array(
						'class'     => 'WP_Customize_Control',
						'label'     => __('Background Attachment', 'fl-automator'),
						'type'      => 'select',
						'choices'   => array(
							'scroll'    => __('Scroll', 'fl-automator'),
							'fixed'     => __('Fixed', 'fl-automator')
						)
					)
				),

				/* Background Size */
				'fl-body-bg-size' => array(
					'setting'   => array(
						'default'   => 'auto',
						'transport' => 'postMessage'
					),
					'control'   => array(
						'class'     => 'WP_Customize_Control',
						'label'     => __('Background Scale', 'fl-automator'),
						'type'      => 'select',
						'choices'   => array(
							'auto'      => __('None', 'fl-automator'),
							'contain'   => __('Fit', 'fl-automator'),
							'cover'     => __('Fill', 'fl-automator')
						)
					)
				)
			)
		),

		/* Accent Color Section */
		'fl-accent-color' => array(
			'title'   => _x( 'Accent Color', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Accent Color */
				'fl-accent' => array(
					'setting'   => array(
						'default'   => '#428bca'
					),
					'control'   => array(
						'class'     => 'WP_Customize_Color_Control',
						'label'     => __('Accent Color', 'fl-automator'),
						'description'   => __('The accent color will be used to color elements such as links and buttons as well as various elements in your theme.', 'fl-automator')
					)
				),
				
				/* Accent Hover Color */
				'fl-accent-hover' => array(
					'setting'   => array(
						'default'   => '#428bca'
					),
					'control'   => array(
						'class'     => 'WP_Customize_Color_Control',
						'label'     => __('Accent Hover Color', 'fl-automator')
					)
				)
			)
		),

		/* Heading Font Section */
		'fl-heading-font' => array(
			'title'   => _x( 'Headings', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Heading Text Color */
				'fl-heading-text-color' => array(
					'setting'   => array(
						'default'   => '#333333',
						'transport' => 'postMessage'
					),
					'control'   => array(
						'class'     => 'WP_Customize_Color_Control',
						'label'     => __('Heading Color', 'fl-automator')
					)
				),

				/* Heading Font Family */
				'fl-heading-font-family' => array(
					'setting'   => array(
						'default'   => 'Helvetica',
						'transport' => 'postMessage'
					),
					'control'   => array(
						'class'     => 'FLCustomizerControl',
						'label'     => __('Heading Font Family', 'fl-automator'),
						'type'      => 'font',
						'connect'   => 'fl-heading-font-weight'
					)
				),

				/* Heading Font Weight */
				'fl-heading-font-weight' => array(
					'setting'   => array(
						'default'   => '400'
					),
					'control'   => array(
						'class'     => 'FLCustomizerControl',
						'label'     => __('Heading Font Weight', 'fl-automator'),
						'type'      => 'font-weight',
						'connect'   => 'fl-heading-font-family'
					)
				),

				/* Heading Font Format */
				'fl-heading-font-format' => array(
					'setting'   => array(
						'default'   => 'none',
						'transport' => 'postMessage'
					),
					'control'   => array(
						'class'     => 'WP_Customize_Control',
						'label'     => __('Heading Font Format', 'fl-automator'),
						'type'      => 'select',
						'choices'   => array(
							'none'       => __('Regular', 'fl-automator'),
							'capitalize' => __('Capitalize', 'fl-automator'),
							'uppercase'  => __('Uppercase', 'fl-automator'),
							'lowercase'  => __('Lowercase', 'fl-automator')
						)
					)
				),

				/* Line */
				'fl-heading-font-line1' => array(
					'control'   => array(
						'class'     => 'FLCustomizerControl',
						'type'      => 'line'
					)
				),

				/* H1 Font Size */
				'fl-h1-font-size' => array(
					'setting'   => array(
						'default'   => '36',
						'transport' => 'postMessage',
						'sanitize_callback' => 'FLCustomizer::sanitize_number'
					),
					'control'   => array(
						'class' => 'WP_Customize_Control',
						'label' => sprintf( _x( '%s Font Size (px)', '%s stands for HTML heading tag.', 'fl-automator' ), 'H1' ),
						'type'  => 'text'
					)
				),

				/* H2 Font Size */
				'fl-h2-font-size' => array(
					'setting'   => array(
						'default'   => '30',
						'transport' => 'postMessage',
						'sanitize_callback' => 'FLCustomizer::sanitize_number'
					),
					'control'   => array(
						'class' => 'WP_Customize_Control',
						'label' => sprintf( _x( '%s Font Size (px)', '%s stands for HTML heading tag.', 'fl-automator' ), 'H2' ),
						'type'  => 'text'
					)
				),

				/* H3 Font Size */
				'fl-h3-font-size' => array(
					'setting'   => array(
						'default'   => '24',
						'transport' => 'postMessage',
						'sanitize_callback' => 'FLCustomizer::sanitize_number'
					),
					'control'   => array(
						'class' => 'WP_Customize_Control',
						'label' => sprintf( _x( '%s Font Size (px)', '%s stands for HTML heading tag.', 'fl-automator' ), 'H3' ),
						'type'  => 'text'
					)
				),

				/* H4 Font Size */
				'fl-h4-font-size' => array(
					'setting'   => array(
						'default'   => '18',
						'transport' => 'postMessage',
						'sanitize_callback' => 'FLCustomizer::sanitize_number'
					),
					'control'   => array(
						'class' => 'WP_Customize_Control',
						'label' => sprintf( _x( '%s Font Size (px)', '%s stands for HTML heading tag.', 'fl-automator' ), 'H4' ),
						'type'  => 'text'
					)
				),

				/* H5 Font Size */
				'fl-h5-font-size' => array(
					'setting'   => array(
						'default'   => '14',
						'transport' => 'postMessage',
						'sanitize_callback' => 'FLCustomizer::sanitize_number'
					),
					'control'   => array(
						'class' => 'WP_Customize_Control',
						'label' => sprintf( _x( '%s Font Size (px)', '%s stands for HTML heading tag.', 'fl-automator' ), 'H5' ),
						'type'  => 'text'
					)
				),

				/* H6 Font Size */
				'fl-h6-font-size' => array(
					'setting'   => array(
						'default'   => '12',
						'transport' => 'postMessage',
						'sanitize_callback' => 'FLCustomizer::sanitize_number'
					),
					'control'   => array(
						'class' => 'WP_Customize_Control',
						'label' => sprintf( _x( '%s Font Size (px)', '%s stands for HTML heading tag.', 'fl-automator' ), 'H6' ),
						'type'  => 'text'
					)
				)
			)
		),

		/* Body Font Section */
		'fl-body-font' => array(
			'title'   => _x( 'Text', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Body Text Color */
				'fl-body-text-color' => array(
					'setting'   => array(
						'default'   => '#808080'
					),
					'control'   => array(
						'class'     => 'WP_Customize_Color_Control',
						'label'     => __('Text Color', 'fl-automator')
					)
				),

				/* Body Font Family */
				'fl-body-font-family' => array(
					'setting'   => array(
						'default'   => 'Helvetica'
					),
					'control'   => array(
						'class'     => 'FLCustomizerControl',
						'label'     => __('Text Font Family', 'fl-automator'),
						'type'      => 'font'
					)
				),

				/* Body Font Size */
				'fl-body-font-size' => array(
					'setting'   => array(
						'default'   => '14',
						'sanitize_callback' => 'FLCustomizer::sanitize_number'
					),
					'control'   => array(
						'class'     => 'WP_Customize_Control',
						'label'     => __('Text Font Size (px)', 'fl-automator'),
						'type'      => 'text'
					)
				)
			)
		),
	)
));