<?php

/* Settings Panel */
FLCustomizer::add_panel('fl-settings', array(
	'title'    => _x( 'Settings', 'Customizer panel title.', 'fl-automator' ),
	'sections' => array(

		/* Social Links Section */
		'fl-social-links' => array(
			'title'   => _x( 'Social Links', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Social Icons Color */
				'fl-social-icons-color' => array(
					'setting'   => array(
						'default'   => 'mono'
					),
					'control'   => array(
						'class'     => 'WP_Customize_Control',
						'label'     => __('Social Icons Color', 'fl-automator'),
						'type'      => 'select',
						'choices'   => array(
							'branded'   => __('Branded', 'fl-automator'),
							'mono'      => __('Monochrome', 'fl-automator')
						)
					)
				),

				/* Social Links (no need to translate brand names) */
				'fl-social-facebook' => array(
					'control'   => array(
						'class' => 'WP_Customize_Control',
						'label' => 'Facebook',
					)
				),
				'fl-social-twitter' => array(
					'control'   => array(
						'class' => 'WP_Customize_Control',
						'label' => 'Twitter',
					)
				),
				'fl-social-google' => array(
					'control'   => array(
						'class' => 'WP_Customize_Control',
						'label' => 'Google',
					)
				),
				'fl-social-linkedin' => array(
					'control'   => array(
						'class' => 'WP_Customize_Control',
						'label' => 'LinkedIn',
					)
				),
				'fl-social-yelp' => array(
					'control'   => array(
						'class' => 'WP_Customize_Control',
						'label' => 'Yelp',
					)
				),
				'fl-social-pinterest' => array(
					'control'   => array(
						'class' => 'WP_Customize_Control',
						'label' => 'Pinterest',
					)
				),
				'fl-social-tumblr' => array(
					'control'   => array(
						'class' => 'WP_Customize_Control',
						'label' => 'Tumblr',
					)
				),
				'fl-social-vimeo' => array(
					'control'   => array(
						'class' => 'WP_Customize_Control',
						'label' => 'Vimeo',
					)
				),
				'fl-social-youtube' => array(
					'control'   => array(
						'class' => 'WP_Customize_Control',
						'label' => 'YouTube',
					)
				),
				'fl-social-flickr' => array(
					'control'   => array(
						'class' => 'WP_Customize_Control',
						'label' => 'Flickr',
					)
				),
				'fl-social-instagram' => array(
					'control'   => array(
						'class' => 'WP_Customize_Control',
						'label' => 'Instagram',
					)
				),
				'fl-social-dribbble' => array(
					'control'   => array(
						'class' => 'WP_Customize_Control',
						'label' => 'Dribbble',
					)
				),
				'fl-social-500px' => array(
					'control'   => array(
						'class' => 'WP_Customize_Control',
						'label' => '500px',
					)
				),
				'fl-social-blogger' => array(
					'control'   => array(
						'class' => 'WP_Customize_Control',
						'label' => 'Blogger',
					)
				),
				'fl-social-github' => array(
					'control'   => array(
						'class' => 'WP_Customize_Control',
						'label' => 'GitHub',
					)
				),
				'fl-social-rss' => array(
					'control'   => array(
						'class' => 'WP_Customize_Control',
						'label' => __( 'RSS', 'fl-automator' )
					)
				),
				'fl-social-email' => array(
					'control'   => array(
						'class' => 'WP_Customize_Control',
						'label' => __( 'Email', 'fl-automator' )
					)
				)
			)
		),

		/* Favicons Section */
		'fl-favicons' => array(
			'disable' => function_exists( 'has_site_icon' ),
			'title'   => _x( 'Favicons', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Favicon */
				'fl-favicon' => array(
					'setting'   => array(
						'default'   => ''
					),
					'control'   => array(
						'class'     => 'WP_Customize_Image_Control',
						'label'     => __('Favicon', 'fl-automator')
					)
				),

				/* Apple Touch Icon */
				'fl-apple-touch-icon' => array(
					'setting'   => array(
						'default'   => ''
					),
					'control'   => array(
						'class'     => 'WP_Customize_Image_Control',
						'label'     => __('Apple Touch Icon', 'fl-automator')
					)
				)
			)
		),
	)
));