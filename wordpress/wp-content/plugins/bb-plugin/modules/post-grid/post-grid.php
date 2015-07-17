<?php

/**
 * @class FLPostGridModule
 */
class FLPostGridModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct()
	{
		parent::__construct(array(
			'name'          => __('Posts', 'fl-builder'),
			'description'   => __('Display a grid of your WordPress posts.', 'fl-builder'),
			'category'      => __('Advanced Modules', 'fl-builder'),
			'editor_export' => false,
			'enabled'       => true
		));
	}

	/**
	 * @method enqueue_scripts
	 */
	public function enqueue_scripts()
	{
		if(FLBuilderModel::is_builder_active() || $this->settings->layout == 'grid') {
			$this->add_js('jquery-masonry');
		}
		if(FLBuilderModel::is_builder_active() || $this->settings->layout == 'gallery') {
			$this->add_js('fl-gallery-grid');
		}
		if(FLBuilderModel::is_builder_active() || $this->settings->pagination == 'scroll') {
			$this->add_js('jquery-infinitescroll');
		}
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLPostGridModule', array(
	'layout'        => array(
		'title'         => __('Layout', 'fl-builder'),
		'sections'      => array(
			'general'       => array(
				'title'         => '',
				'fields'        => array(
					'layout'        => array(
						'type'          => 'select',
						'label'         => __('Layout Style', 'fl-builder'),
						'default'       => 'grid',
						'options'       => array(
							'grid'          => __('Grid', 'fl-builder'),
							'gallery'       => __('Gallery', 'fl-builder'),
							'feed'          => __('Feed', 'fl-builder'),
						),
						'toggle'        => array(
							'grid'          => array(
								'sections'      => array('grid', 'image', 'content'),
								'fields'        => array('show_author')
							),
							'feed'          => array(
								'sections'      => array('image', 'content'),
								'fields'        => array('image_position', 'show_author', 'show_comments', 'content_type')
							)
						)
					),
					'pagination'     => array(
						'type'          => 'select',
						'label'         => __('Pagination Style', 'fl-builder'),
						'default'       => 'numbers',
						'options'       => array(
							'numbers'       => __('Numbers', 'fl-builder'),
							'scroll'        => __('Scroll', 'fl-builder'),
							'none'          => _x( 'None', 'Pagination style.', 'fl-builder' ),
						)
					),
					'posts_per_page' => array(
						'type'          => 'text',
						'label'         => __('Posts Per Page', 'fl-builder'),
						'default'       => '10',
						'size'          => '4'
					),
				)
			),
			'grid'          => array(
				'title'         => __('Grid', 'fl-builder'),
				'fields'        => array(
					'post_width'    => array(
						'type'          => 'text',
						'label'         => __('Post Width', 'fl-builder'),
						'default'       => '300',
						'maxlength'     => '3',
						'size'          => '4',
						'description'   => 'px'
					),
					'post_spacing'  => array(
						'type'          => 'text',
						'label'         => __('Post Spacing', 'fl-builder'),
						'default'       => '60',
						'maxlength'     => '3',
						'size'          => '4',
						'description'   => 'px'
					),
				)
			),
			'image'        => array(
				'title'         => __( 'Featured Image', 'fl-builder' ),
				'fields'        => array(
					'show_image'    => array(
						'type'          => 'select',
						'label'         => __('Image', 'fl-builder'),
						'default'       => '1',
						'options'       => array(
							'1'             => __('Show', 'fl-builder'),
							'0'             => __('Hide', 'fl-builder')
						),
						'toggle'        => array(
							'1'             => array(
								'fields'        => array('image_size')
							)
						)
					),
					'image_position' => array(
						'type'          => 'select',
						'label'         => __('Position', 'fl-builder'),
						'default'       => 'above',
						'options'       => array(
							'above'         => __('Above Text', 'fl-builder'),
							'beside'        => __('Beside Text', 'fl-builder')
						)
					),
					'image_size'    => array(
						'type'          => 'photo-sizes',
						'label'         => __('Size', 'fl-builder'),
						'default'       => 'medium'
					),
				)
			),
			'info'          => array(
				'title'         => __( 'Post Info', 'fl-builder' ),
				'fields'        => array(
					'show_author'   => array(
						'type'          => 'select',
						'label'         => __('Author', 'fl-builder'),
						'default'       => '1',
						'options'       => array(
							'1'             => __('Show', 'fl-builder'),
							'0'             => __('Hide', 'fl-builder')
						)
					),
					'show_date'     => array(
						'type'          => 'select',
						'label'         => __('Date', 'fl-builder'),
						'default'       => '1',
						'options'       => array(
							'1'             => __('Show', 'fl-builder'),
							'0'             => __('Hide', 'fl-builder')
						),
						'toggle'        => array(
							'1'             => array(
								'fields'        => array('date_format')
							)
						)
					),
					'date_format'   => array(
						'type'          => 'select',
						'label'         => __('Date Format', 'fl-builder'),
						'default'       => 'M j, Y',
						'options'       => array(
							// Note for developer: I would personally add this as a text field with a default value of get_option( 'date_format' )
							'M j, Y'        => date('M j, Y'),
							'F j, Y'        => date('F j, Y'),
							'm/d/Y'         => date('m/d/Y'),
							'm-d-Y'         => date('m-d-Y'),
							'd M Y'         => date('d M Y'),
							'd F Y'         => date('d F Y'),
							'Y-m-d'         => date('Y-m-d'),
							'Y/m/d'         => date('Y/m/d'),
						)
					),
					'show_comments' => array(
						'type'          => 'select',
						'label'         => __('Comments', 'fl-builder'),
						'default'       => '1',
						'options'       => array(
							'1'             => __('Show', 'fl-builder'),
							'0'             => __('Hide', 'fl-builder')
						)
					),
				)
			),
			'content'       => array(
				'title'         => __( 'Content', 'fl-builder' ),
				'fields'        => array(
					'show_content'  => array(
						'type'          => 'select',
						'label'         => __('Content', 'fl-builder'),
						'default'       => '1',
						'options'       => array(
							'1'             => __('Show', 'fl-builder'),
							'0'             => __('Hide', 'fl-builder')
						)
					),
					'content_type'  => array(
						'type'          => 'select',
						'label'         => __('Content Type', 'fl-builder'),
						'default'       => 'excerpt',
						'options'       => array(
							'excerpt'        => __('Excerpt', 'fl-builder'),
							'full'           => __('Full Text', 'fl-builder')
						)
					),
					'show_more_link' => array(
						'type'          => 'select',
						'label'         => __('More Link', 'fl-builder'),
						'default'       => '0',
						'options'       => array(
							'1'             => __('Show', 'fl-builder'),
							'0'             => __('Hide', 'fl-builder')
						)
					),
					'more_link_text' => array(
						'type'          => 'text',
						'label'         => __('More Link Text', 'fl-builder'),
						'default'       => __('Read More', 'fl-builder'),
					),
				)
			)
		)
	),
	'content'   => array(
		'title'         => __('Content', 'fl-builder'),
		'file'          => FL_BUILDER_DIR . 'includes/loop-settings.php',
	)
));