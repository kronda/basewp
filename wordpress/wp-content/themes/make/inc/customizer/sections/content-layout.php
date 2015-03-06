<?php
/**
 * @package Make
 */

if ( ! function_exists( 'ttfmake_customizer_define_contentlayout_sections' ) ) :
/**
 * Define the sections and settings for the Content & Layout panel
 *
 * @since  1.3.0.
 *
 * @param  array    $sections    The master array of Customizer sections
 * @return array                 The augmented master array
 */
function ttfmake_customizer_define_contentlayout_sections( $sections ) {
	$theme_prefix = 'ttfmake_';
	$panel = 'ttfmake_content-layout';
	$contentlayout_sections = array();

	/**
	 * Global
	 */
	$contentlayout_sections['layout-global'] = array(
		'panel'   => $panel,
		'title'   => __( 'Global', 'make' ),
		'options' => array(
			'general-layout'                => array(
				'setting' => array(
					'sanitize_callback' => 'ttfmake_sanitize_choice',
				),
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Radio_Control',
					'label'   => __( 'Site Layout', 'make' ),
					'type'    => 'radio',
					'mode'    => 'buttonset',
					'choices' => ttfmake_get_choices( 'general-layout' ),
				),
			),
		),
	);

	/**
	 * Header
	 */
	$contentlayout_sections['header'] = array(
		'panel'   => $panel,
		'title'   => __( 'Header', 'make' ),
		'options' => array(
			'header-layout-group' => array(
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Misc_Control',
					'label'   => __( 'Layout', 'make' ),
					'type'  => 'group-title',
				),
			),
			'header-layout'             => array(
				'setting' => array(
					'sanitize_callback' => 'ttfmake_sanitize_choice',
				),
				'control' => array(
					'label'   => __( 'Header Layout', 'make' ),
					'type'    => 'select',
					'choices' => ttfmake_get_choices( 'header-layout' ),
				),
			),
			'header-branding-position'  => array(
				'setting' => array(
					'sanitize_callback' => 'ttfmake_sanitize_choice',
				),
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Radio_Control',
					'label'   => __( 'Show Title/Logo On', 'make' ),
					'type'    => 'radio',
					'mode'    => 'buttonset',
					'choices' => ttfmake_get_choices( 'header-branding-position' ),
				),
			),
			'header-bar-content-layout' => array(
				'setting' => array(
					'sanitize_callback' => 'ttfmake_sanitize_choice',
				),
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Radio_Control',
					'label'   => __( 'Header Bar Layout', 'make' ),
					'type'    => 'radio',
					'mode'    => 'buttonset',
					'choices' => ttfmake_get_choices( 'header-bar-content-layout' ),
				),
			),
			'header-options-group' => array(
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Misc_Control',
					'label'   => __( 'Options', 'make' ),
					'type'  => 'group-title',
				),
			),
			'header-text'               => array(
				'setting' => array(
					'sanitize_callback' => 'ttfmake_sanitize_text',
					'transport'         => 'postMessage',
				),
				'control' => array(
					'label'       => __( 'Header Bar Text', 'make' ),
					'description' => __( 'This text only appears if a custom menu has not been assigned to the Header Bar Menu location in the Navigation section.', 'make' ),
					'type'        => 'text',
				),
			),
			'header-options-heading'    => array(
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Misc_Control',
					'type'         => 'heading',
					'label'        => __( 'Optional Header Elements', 'make' ),
				),
			),
			'header-show-search'        => array(
				'setting' => array(
					'sanitize_callback' => 'absint',
				),
				'control' => array(
					'label' => __( 'Show search field', 'make' ),
					'type'  => 'checkbox',
				),
			),
			'header-show-social'        => array(
				'setting' => array(
					'sanitize_callback' => 'absint',
				),
				'control' => array(
					'label' => __( 'Show social icons', 'make' ),
					'type'  => 'checkbox',
				),
			),
			'font-size-header-bar-icon'     => array(
				'setting' => array(
					'sanitize_callback' => 'absint',
				),
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Range_Control',
					'label'   => __( 'Icon Size (px)', 'make' ),
					'type'  => 'range',
					'input_attrs' => array(
						'min'  => 6,
						'max'  => 100,
						'step' => 1,
					),
				),
			),
		),
	);

	/**
	 * Footer
	 */
	$contentlayout_sections['footer'] = array(
		'panel' => $panel,
		'title' => __( 'Footer', 'make' ),
		'options' => array(
			'footer-widgets-group' => array(
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Misc_Control',
					'label'   => __( 'Widgets', 'make' ),
					'type'  => 'group-title',
				),
			),
			'footer-widget-areas' => array(
				'setting' => array(
					'sanitize_callback'	=> 'ttfmake_sanitize_choice',
				),
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Radio_Control',
					'label'				=> __( 'Number of Widget Areas', 'make' ),
					'type'				=> 'radio',
					'mode'              => 'buttonset',
					'choices'			=> ttfmake_get_choices( 'footer-widget-areas' ),
				),
			),
			'footer-layout-group' => array(
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Misc_Control',
					'label'   => __( 'Layout', 'make' ),
					'type'  => 'group-title',
				),
			),
			'footer-layout' => array(
				'setting' => array(
					'sanitize_callback'	=> 'ttfmake_sanitize_choice',
				),
				'control' => array(
					'label'				=> __( 'Footer Layout', 'make' ),
					'type'				=> 'select',
					'choices'			=> ttfmake_get_choices( 'footer-layout' ),
				),
			),
			'footer-options-group' => array(
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Misc_Control',
					'label'   => __( 'Options', 'make' ),
					'type'  => 'group-title',
				),
			),
			'footer-text' => array(
				'setting' => array(
					'sanitize_callback'	=> 'ttfmake_sanitize_text',
					'transport'			=> 'postMessage',
				),
				'control' => array(
					'label'				=> __( 'Footer Text', 'make' ),
					'type'				=> 'text',
				),
			),
			'footer-options-heading' => array(
				'control' => array(
					'control_type'		=> 'TTFMAKE_Customize_Misc_Control',
					'type'				=> 'heading',
					'label'				=> __( 'Optional Footer Elements', 'make' ),
				),
			),
			'footer-show-social' => array(
				'setting' => array(
					'sanitize_callback'	=> 'absint',
				),
				'control' => array(
					'label'				=> __( 'Show social icons', 'make' ),
					'type'				=> 'checkbox',
				),
			),
			'font-size-footer-icon'     => array(
				'setting' => array(
					'sanitize_callback' => 'absint',
				),
				'control' => array(
					'control_type' => 'TTFMAKE_Customize_Range_Control',
					'label'   => __( 'Icon Size (px)', 'make' ),
					'type'  => 'range',
					'input_attrs' => array(
						'min'  => 6,
						'max'  => 100,
						'step' => 1,
					),
				),
			),
		),
	);

	/**
	 * Blog
	 */
	$view = 'blog';
	$prefix = 'layout-blog-';
	$contentlayout_sections['layout-blog'] = array(
		'panel'   => $panel,
		'title'   => __( 'Blog (Posts Page)', 'make' ),
		'options' => array_merge(
			ttfmake_customizer_layout_region_group_definitions( $view ),
			ttfmake_customizer_layout_featured_image_group_definitions( $view ),
			ttfmake_customizer_layout_post_date_group_definitions( $view ),
			ttfmake_customizer_layout_post_author_group_definitions( $view ),
			ttfmake_customizer_layout_content_group_definitions( $view ),
			ttfmake_customizer_layout_post_meta_group_definitions( $view ),
			ttfmake_customizer_layout_comment_count_group_definitions( $view )
		),
	);

	/**
	 * Archives
	 */
	$view = 'archive';
	$prefix = 'layout-archive-';
	$contentlayout_sections['layout-archive'] = array(
		'panel'   => $panel,
		'title'   => __( 'Archives', 'make' ),
		'options' => array_merge(
			ttfmake_customizer_layout_region_group_definitions( $view ),
			ttfmake_customizer_layout_featured_image_group_definitions( $view ),
			ttfmake_customizer_layout_post_date_group_definitions( $view ),
			ttfmake_customizer_layout_post_author_group_definitions( $view ),
			ttfmake_customizer_layout_content_group_definitions( $view ),
			ttfmake_customizer_layout_post_meta_group_definitions( $view ),
			ttfmake_customizer_layout_comment_count_group_definitions( $view )
		),
	);

	/**
	 * Search Results
	 */
	$view = 'search';
	$prefix = 'layout-search-';
	$contentlayout_sections['layout-search'] = array(
		'panel'   => $panel,
		'title'   => __( 'Search Results', 'make' ),
		'options' => array_merge(
			ttfmake_customizer_layout_region_group_definitions( $view ),
			ttfmake_customizer_layout_featured_image_group_definitions( $view ),
			ttfmake_customizer_layout_post_date_group_definitions( $view ),
			ttfmake_customizer_layout_post_author_group_definitions( $view ),
			ttfmake_customizer_layout_content_group_definitions( $view ),
			ttfmake_customizer_layout_post_meta_group_definitions( $view ),
			ttfmake_customizer_layout_comment_count_group_definitions( $view )
		),
	);

	/**
	 * Posts
	 */
	$view = 'post';
	$prefix = 'layout-post-';
	$contentlayout_sections['layout-post'] = array(
		'panel'   => $panel,
		'title'   => __( 'Posts', 'make' ),
		'options' => array_merge(
			ttfmake_customizer_layout_region_group_definitions( $view ),
			ttfmake_customizer_layout_featured_image_group_definitions( $view ),
			ttfmake_customizer_layout_post_date_group_definitions( $view ),
			ttfmake_customizer_layout_post_author_group_definitions( $view ),
			ttfmake_customizer_layout_post_meta_group_definitions( $view ),
			ttfmake_customizer_layout_comment_count_group_definitions( $view )
		),
	);

	/**
	 * Pages
	 */
	$view = 'page';
	$prefix = 'layout-page-';
	$contentlayout_sections['layout-page'] = array(
		'panel'   => $panel,
		'title'   => __( 'Pages', 'make' ),
		'options' => array_merge(
			ttfmake_customizer_layout_region_group_definitions( $view ),
			array(
				$prefix . 'sidebars-text'             => array(
					'control' => array(
						'control_type' => 'TTFMAKE_Customize_Misc_Control',
						'type'         => 'text',
						'description'  => __( 'Sidebars are not available on pages using the Builder Template.', 'make' ),
					),
				),
			),
			ttfmake_customizer_layout_featured_image_group_definitions( $view ),
			ttfmake_customizer_layout_post_date_group_definitions( $view ),
			ttfmake_customizer_layout_post_author_group_definitions( $view ),
			ttfmake_customizer_layout_comment_count_group_definitions( $view ),
			array(
				$prefix . 'pagetitle-heading'         => array(
					'control' => array(
						'control_type' => 'TTFMAKE_Customize_Misc_Control',
						'type'         => 'group-title',
						'label'        => __( 'Page Title', 'make' ),
					),
				),
				$prefix . 'hide-title'                => array(
					'setting' => array(
						'sanitize_callback' => 'absint',
					),
					'control' => array(
						'label' => __( 'Hide title', 'make' ),
						'type'  => 'checkbox',
					),
				),
			)
		),
	);

	/**
	 * Filter the definitions for the controls in the Layout panel of the Customizer.
	 *
	 * The panel was previously labeled "Content & Layout".
	 *
	 * @since 1.3.0.
	 *
	 * @param array    $contentlayout_sections    The array of definitions.
	 */
	$contentlayout_sections = apply_filters( 'make_customizer_contentlayout_sections', $contentlayout_sections );

	/**
	 * Deprecated: Filter the definitions for the controls in the Header panel of the Customizer.
	 *
	 * This filter was introduced when the Header options were all in one panel instead of divided among
	 * Typography, Color, Layout, etc.
	 *
	 * @since 1.3.0.
	 * @deprecated 1.5.0.
	 *
	 * @param array    $header_sections    The array of definitions.
	 */
	$header_sections = apply_filters( 'make_customizer_header_sections', array() );

	/**
	 * Deprecated: Filter the definitions for the controls in the Footer panel of the Customizer.
	 *
	 * This filter was introduced when the Footer options were all in one panel instead of divided among
	 * Typography, Color, Layout, etc.
	 *
	 * @since 1.3.0.
	 * @deprecated 1.5.0.
	 *
	 * @param array    $footer_sections    The array of definitions.
	 */
	$footer_sections = apply_filters( 'make_customizer_footer_sections', array() );

	// Merge with master array
	return array_merge( $sections, $contentlayout_sections, $header_sections, $footer_sections );
}
endif;

add_filter( 'make_customizer_sections', 'ttfmake_customizer_define_contentlayout_sections' );

/**
 * Generate an array of Customizer option definitions for a particular view.
 *
 * @since 1.5.0.
 *
 * @param  string    $view
 * @return array
 */
function ttfmake_customizer_layout_region_group_definitions( $view ) {
	$prefix = 'layout-' . $view . '-';

	$definitions = array(
		$prefix . 'sidebars-heading'          => array(
			'control' => array(
				'control_type' => 'TTFMAKE_Customize_Misc_Control',
				'type'         => 'group-title',
				'label'        => __( 'Header, Footer, Sidebars', 'make' ),
			),
		),
		$prefix . 'hide-header'               => array(
			'setting' => array(
				'sanitize_callback' => 'absint',
			),
			'control' => array(
				'label' => __( 'Hide site header', 'make' ),
				'type'  => 'checkbox',
			),
		),
		$prefix . 'hide-footer'               => array(
			'setting' => array(
				'sanitize_callback' => 'absint',
			),
			'control' => array(
				'label' => __( 'Hide site footer', 'make' ),
				'type'  => 'checkbox',
			),
		),
		$prefix . 'sidebar-left'              => array(
			'setting' => array(
				'sanitize_callback' => 'absint',
			),
			'control' => array(
				'label' => __( 'Show left sidebar', 'make' ),
				'type'  => 'checkbox',
			),
		),
		$prefix . 'sidebar-right'             => array(
			'setting' => array(
				'sanitize_callback' => 'absint',
			),
			'control' => array(
				'label' => __( 'Show right sidebar', 'make' ),
				'type'  => 'checkbox',
			),
		),
	);

	return $definitions;
}

/**
 * Generate an array of Customizer option definitions for a particular view.
 *
 * @since 1.5.0.
 *
 * @param  string    $view
 * @return array
 */
function ttfmake_customizer_layout_featured_image_group_definitions( $view ) {
	$prefix = 'layout-' . $view . '-';

	$definitions = array(
		'featured-images-group-' . $view => array(
			'control' => array(
				'control_type' => 'TTFMAKE_Customize_Misc_Control',
				'label'   => __( 'Featured Images', 'make' ),
				'type'  => 'group-title',
			),
		),
		$prefix . 'featured-images'           => array(
			'setting' => array(
				'sanitize_callback' => 'ttfmake_sanitize_choice',
			),
			'control' => array(
				'label'   => __( 'Location', 'make' ),
				'type'    => 'select',
				'choices' => ttfmake_get_choices( $prefix . 'featured-images' ),
			),
		),
		$prefix . 'featured-images-alignment' => array(
			'setting' => array(
				'sanitize_callback' => 'ttfmake_sanitize_choice',
			),
			'control' => array(
				'control_type' => 'TTFMAKE_Customize_Radio_Control',
				'label'   => __( 'Alignment', 'make' ),
				'type'    => 'radio',
				'mode'    => 'buttonset',
				'choices' => ttfmake_get_choices( $prefix . 'featured-images-alignment' ),
			),
		),
	);

	return $definitions;
}

/**
 * Generate an array of Customizer option definitions for a particular view.
 *
 * @since 1.5.0.
 *
 * @param  string    $view
 * @return array
 */
function ttfmake_customizer_layout_post_date_group_definitions( $view ) {
	$prefix = 'layout-' . $view . '-';

	$definitions = array(
		'post-date-group-' . $view => array(
			'control' => array(
				'control_type' => 'TTFMAKE_Customize_Misc_Control',
				'label'   => __( 'Post Date', 'make' ),
				'type'  => 'group-title',
			),
		),
		$prefix . 'post-date'                 => array(
			'setting' => array(
				'sanitize_callback' => 'ttfmake_sanitize_choice',
			),
			'control' => array(
				'label'   => __( 'Style', 'make' ),
				'type'    => 'select',
				'choices' => ttfmake_get_choices( $prefix . 'post-date' ),
			),
		),
		$prefix . 'post-date-location'        => array(
			'setting' => array(
				'sanitize_callback' => 'ttfmake_sanitize_choice',
			),
			'control' => array(
				'control_type' => 'TTFMAKE_Customize_Radio_Control',
				'label'   => __( 'Location', 'make' ),
				'type'    => 'radio',
				'mode'    => 'buttonset',
				'choices' => ttfmake_get_choices( $prefix . 'post-date-location' ),
			),
		),
	);

	return $definitions;
}

/**
 * Generate an array of Customizer option definitions for a particular view.
 *
 * @since 1.5.0.
 *
 * @param  string    $view
 * @return array
 */
function ttfmake_customizer_layout_post_author_group_definitions( $view ) {
	$prefix = 'layout-' . $view . '-';

	$definitions = array(
		'post-author-group-' . $view => array(
			'control' => array(
				'control_type' => 'TTFMAKE_Customize_Misc_Control',
				'label'   => __( 'Post Author', 'make' ),
				'type'  => 'group-title',
			),
		),
		$prefix . 'post-author'                 => array(
			'setting' => array(
				'sanitize_callback' => 'ttfmake_sanitize_choice',
			),
			'control' => array(
				'label'   => __( 'Style', 'make' ),
				'type'    => 'select',
				'choices' => ttfmake_get_choices( $prefix . 'post-author' ),
			),
		),
		$prefix . 'post-author-location'        => array(
			'setting' => array(
				'sanitize_callback' => 'ttfmake_sanitize_choice',
			),
			'control' => array(
				'control_type' => 'TTFMAKE_Customize_Radio_Control',
				'label'   => __( 'Location', 'make' ),
				'type'    => 'radio',
				'mode'    => 'buttonset',
				'choices' => ttfmake_get_choices( $prefix . 'post-author-location' ),
			),
		),
	);

	return $definitions;
}

/**
 * Generate an array of Customizer option definitions for a particular view.
 *
 * @since 1.5.0.
 *
 * @param  string    $view
 * @return array
 */
function ttfmake_customizer_layout_comment_count_group_definitions( $view ) {
	$prefix = 'layout-' . $view . '-';

	$definitions = array(
		'comment-count-group-' . $view => array(
			'control' => array(
				'control_type' => 'TTFMAKE_Customize_Misc_Control',
				'label'   => __( 'Comment Count', 'make' ),
				'type'  => 'group-title',
			),
		),
		$prefix . 'comment-count'                 => array(
			'setting' => array(
				'sanitize_callback' => 'ttfmake_sanitize_choice',
			),
			'control' => array(
				'label'   => __( 'Style', 'make' ),
				'type'    => 'select',
				'choices' => ttfmake_get_choices( $prefix . 'comment-count' ),
			),
		),
		$prefix . 'comment-count-location'        => array(
			'setting' => array(
				'sanitize_callback' => 'ttfmake_sanitize_choice',
			),
			'control' => array(
				'control_type' => 'TTFMAKE_Customize_Radio_Control',
				'label'   => __( 'Location', 'make' ),
				'type'    => 'radio',
				'mode'    => 'buttonset',
				'choices' => ttfmake_get_choices( $prefix . 'comment-count-location' ),
			),
		),
	);

	return $definitions;
}

/**
 * Generate an array of Customizer option definitions for a particular view.
 *
 * @since 1.5.0.
 *
 * @param  string    $view
 * @return array
 */
function ttfmake_customizer_layout_post_meta_group_definitions( $view ) {
	$prefix = 'layout-' . $view . '-';

	$definitions = array(
		'post-meta-group-' . $view => array(
			'control' => array(
				'control_type' => 'TTFMAKE_Customize_Misc_Control',
				'label'   => __( 'Post Meta', 'make' ),
				'type'  => 'group-title',
			),
		),
		$prefix . 'show-categories'           => array(
			'setting' => array(
				'sanitize_callback' => 'absint',
			),
			'control' => array(
				'label' => __( 'Show categories', 'make' ),
				'type'  => 'checkbox',
			),
		),
		$prefix . 'show-tags'                 => array(
			'setting' => array(
				'sanitize_callback' => 'absint',
			),
			'control' => array(
				'label' => __( 'Show tags', 'make' ),
				'type'  => 'checkbox',
			),
		),
	);

	return $definitions;
}

/**
 * Generate an array of Customizer option definitions for a particular view.
 *
 * @since 1.5.0.
 *
 * @param  string    $view
 * @return array
 */
function ttfmake_customizer_layout_content_group_definitions( $view ) {
	$prefix = 'layout-' . $view . '-';

	$definitions = array(
		'content-group-' . $view => array(
			'control' => array(
				'control_type' => 'TTFMAKE_Customize_Misc_Control',
				'label'   => __( 'Content', 'make' ),
				'type'  => 'group-title',
			),
		),
		$prefix . 'auto-excerpt'              => array(
			'setting' => array(
				'sanitize_callback' => 'absint',
			),
			'control' => array(
				'label' => __( 'Generate excerpts automatically', 'make' ),
				'type'  => 'checkbox',
			),
		),
	);

	return $definitions;
}