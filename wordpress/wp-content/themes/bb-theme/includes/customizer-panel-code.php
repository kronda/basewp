<?php

/* Code Panel */
FLCustomizer::add_panel('fl-code', array(
	'title'    => _x( 'Code', 'Customizer panel title.', 'fl-automator' ),
	'sections' => array(

		/* CSS Section */
		'fl-css-code-section' => array(
			'title'   => _x( 'CSS Code', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* CSS */
				'fl-css-code' => array(
					'setting'   => array(
						'default'   => ''
					),
					'control'   => array(
						'description'   => __('CSS entered in the box below will be rendered within &lt;style&gt; tags.', 'fl-automator'),
						'class'         => 'FLCustomizerControl',
						'type'          => 'code'
					)
				)
			)
		),

		/* JavaScript Section */
		'fl-js-code-section' => array(
			'title'   => _x( 'JavaScript Code', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* JavaScript */
				'fl-js-code' => array(
					'setting'   => array(
						'default'   => ''
					),
					'control'   => array(
						'description'   => __('JavaScript entered in the box below will be rendered within &lt;script&gt; tags.', 'fl-automator'),
						'class'         => 'FLCustomizerControl',
						'type'          => 'code'
					)
				)
			)
		),

		/* Head Section */
		'fl-head-code-section' => array(
			'title'   => _x( 'Head Code', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Head */
				'fl-head-code' => array(
					'setting'   => array(
						'default'   => ''
					),
					'control'   => array(
						'description'   => __('Code entered in the box below will be rendered within the page &lt;head&gt; tag.', 'fl-automator'),
						'class'         => 'FLCustomizerControl',
						'type'          => 'code'
					)
				)
			)
		),

		/* Header Section */
		'fl-header-code-section' => array(
			'title'   => _x( 'Header Code', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Footer */
				'fl-header-code' => array(
					'setting'   => array(
						'default'   => ''
					),
					'control'   => array(
						'description'   => __('Code entered in the box below will be rendered directly after the opening &lt;body&gt; tag.', 'fl-automator'),
						'class'         => 'FLCustomizerControl',
						'type'          => 'code'
					)
				)
			)
		),

		/* Footer Section */
		'fl-footer-code-section' => array(
			'title'   => _x( 'Footer Code', 'Customizer section title.', 'fl-automator' ),
			'options' => array(

				/* Footer */
				'fl-footer-code' => array(
					'setting'   => array(
						'default'   => ''
					),
					'control'   => array(
						'description'   => __('Code entered in the box below will be rendered directly before the closing &lt;body&gt; tag.', 'fl-automator'),
						'class'         => 'FLCustomizerControl',
						'type'          => 'code'
					)
				)
			)
		)
	)
));