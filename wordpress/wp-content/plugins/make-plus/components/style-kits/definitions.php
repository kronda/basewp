<?php
/**
 * @package Make Plus
 */

if ( ! function_exists( 'ttfmp_style_kit_definitions' ) ) :
/**
 * Define the settings for each Style Kit.
 *
 * @since 1.1.0.
 *
 * @return array    The array of settings for each kit.
 */
function ttfmp_style_kit_definitions() {
	$definitions = array(
		'light' => array(
			'label' => __( 'Light', 'make-plus' ),
			'priority' => 10,
			'definitions' => array(
				/**
				 * Typography
				 */
				'font-family-body'                  => 'PT Sans',
				'font-size-body'                    => 17,
				'line-height-body'                  => 1.5,
				'font-family-h1'                    => 'PT Sans',
				'font-size-h1'                      => 50,
				'font-weight-h1'                    => 'normal',
				'font-family-h2'                    => 'PT Sans',
				'font-size-h2'                      => 34,
				'font-weight-h2'                    => 'normal',
				'font-family-h3'                    => 'PT Sans',
				'font-size-h3'                      => 24,
				'font-weight-h3'                    => 'normal',
				'font-family-h4'                    => 'PT Sans',
				'font-size-h4'                      => 24,
				'font-weight-h4'                    => 'normal',
				'font-family-h5'                    => 'PT Sans',
				'font-size-h5'                      => 16,
				'font-weight-h5'                    => 'normal',
				'font-family-h6'                    => 'PT Sans',
				'font-size-h6'                      => 14,
				'font-weight-h6'                    => 'normal',
				'font-family-site-title'            => 'PT Sans',
				'font-size-site-title'              => 34,
				'font-family-site-tagline'          => 'PT Sans',
				'font-size-site-tagline'            => 12,
				'font-family-nav'					=> 'PT Sans',
				'font-size-nav'                     => 14,
				'font-family-subnav'				=> 'PT Sans',
				'font-size-subnav'                  => 13,
				'font-family-widget'                => 'PT Sans',
				'font-size-widget'                  => 13,
				/**
				 * Color
				 */
				'color-primary'                     => '#0fc637',
				'color-secondary'                   => '#eaecee',
				'color-text'                        => '#171717',
				'color-detail'                      => '#b9bcbf',
				'background_color'                  => '#f3f3f3',
				'main-background-color'             => '#ffffff',
				'header-text-color'                 => '#171717',
				'header-background-color'           => '#ffffff',
				'color-site-title'                  => '#171717',
				'header-bar-background-color'       => '#ffffff',
				'header-bar-text-color'             => '#171717',
				'header-bar-border-color'           => '#b9bcbf',
				'footer-text-color'                 => '#464849',
				'footer-border-color'               => '#b9bcbf',
				'footer-background-color'           => '#ffffff',
				/**
				 * Background Images
				 */
				'background_image'                  => '',
				'header-background-image'           => '',
				'main-background-image'             => '',
				'footer-background-image'           => '',
				/**
				 * Layout
				 */
				'general-layout'                    => 'boxed',
				'header-layout'                     => 1,
				'footer-layout'                     => 1,
			)
		),
		'dark' => array(
			'label' => __( 'Dark', 'make-plus' ),
			'priority' => 20,
			'definitions' => array(
				/**
				 * Typography
				 */
				'font-family-body'                  => 'Lato',
				'font-size-body'                    => 17,
				'font-family-h1'                    => 'Lato',
				'font-size-h1'                      => 50,
				'font-family-h2'                    => 'Lato',
				'font-size-h2'                      => 34,
				'font-family-h3'                    => 'Lato',
				'font-size-h3'                      => 24,
				'font-family-h4'                    => 'Lato',
				'font-size-h4'                      => 24,
				'font-family-h5'                    => 'Lato',
				'font-size-h5'                      => 16,
				'font-family-h6'                    => 'Lato',
				'font-size-h6'                      => 14,
				'font-family-site-title'            => 'Lato',
				'font-size-site-title'              => 34,
				'font-family-site-tagline'          => 'Lato',
				'font-size-site-tagline'            => 12,
				'font-family-nav'					=> 'Lato',
				'font-size-nav'                     => 14,
				'font-family-subnav'				=> 'Lato',
				'font-size-subnav'                  => 13,
				'font-family-widget'                => 'Lato',
				'font-size-widget'                  => 14,
				/**
				 * Color
				 */
				'color-primary'                     => '#ea451c',
				'color-secondary'                   => '#a0a0a0',
				'color-text'                        => '#ffffff',
				'color-detail'                      => '#b9bcbf',
				'background_color'                  => '#323232',
				'main-background-color'             => '#323232',
				'header-text-color'                 => '#ffffff',
				'header-background-color'           => '#323232',
				'color-site-title'                  => '#ffffff',
				'header-bar-background-color'       => '#1e1e1e',
				'header-bar-text-color'             => '#ffffff',
				'header-bar-border-color'           => '#1e1e1e',
				'footer-text-color'                 => '#ffffff',
				'footer-border-color'               => '#353535',
				'footer-background-color'           => '#1e1e1e',
				/**
				 * Background Images
				 */
				'background_image'                  => '',
				'header-background-image'           => '',
				'main-background-image'             => '',
				'footer-background-image'           => '',
				/**
				 * Layout
				 */
				'general-layout'                    => 'full-width',
				'header-layout'                     => 1,
				'footer-layout'                     => 1,
			)
		),
		'modern' => array(
			'label' => __( 'Modern', 'make-plus' ),
			'priority' => 30,
			'definitions' => array(
				/**
				 * Typography
				 */
				'font-family-body'                  => 'Open Sans',
				'font-size-body'                    => 17,
				'font-family-h1'                    => 'Raleway',
				'font-size-h1'                      => 50,
				'font-family-h2'                    => 'Raleway',
				'font-size-h2'                      => 34,
				'font-family-h3'                    => 'Raleway',
				'font-size-h3'                      => 24,
				'font-family-h4'                    => 'Raleway',
				'font-size-h4'                      => 24,
				'font-family-h5'                    => 'Raleway',
				'font-size-h5'                      => 16,
				'font-family-h6'                    => 'Raleway',
				'font-size-h6'                      => 14,
				'font-family-site-title'            => 'Raleway',
				'font-size-site-title'              => 34,
				'font-family-site-tagline'          => 'Open Sans',
				'font-size-site-tagline'            => 12,
				'font-family-nav'					=> 'Open Sans',
				'font-size-nav'                     => 14,
				'font-family-subnav'				=> 'Open Sans',
				'font-size-subnav'                  => 13,
				'font-family-widget'                => 'Open Sans',
				'font-size-widget'                  => 14,
				/**
				 * Color
				 */
				'color-primary'                     => '#e83365',
				'color-secondary'                   => '#d4d6d7',
				'color-text'                        => '#171717',
				'color-detail'                      => '#b9bcbf',
				'background_color'                  => '#b9bcbf',
				'main-background-color'             => '#ffffff',
				'header-text-color'                 => '#171717',
				'header-background-color'           => '#ffffff',
				'color-site-title'                  => '#171717',
				'header-bar-background-color'       => '#d4d6d7',
				'header-bar-text-color'             => '#171717',
				'header-bar-border-color'           => '#b0b1b2',
				'footer-text-color'                 => '#464849',
				'footer-border-color'               => '#b0b1b2',
				'footer-background-color'           => '#d4d6d7',
				/**
				 * Background Images
				 */
				'background_image'                  => '',
				'header-background-image'           => '',
				'main-background-image'             => '',
				'footer-background-image'           => '',
				/**
				 * Layout
				 */
				'general-layout'                    => 'full-width',
				'header-layout'                     => 1,
				'footer-layout'                     => 1,
			)
		),
		'creative' => array(
			'label' => __( 'Creative', 'make-plus' ),
			'priority' => 40,
			'definitions' => array(
				/**
				 * Typography
				 */
				'font-family-body'                  => 'Open Sans',
				'font-size-body'                    => 17,
				'font-family-h1'                    => 'Open Sans',
				'font-size-h1'                      => 50,
				'font-family-h2'                    => 'Open Sans',
				'font-size-h2'                      => 34,
				'font-family-h3'                    => 'Open Sans',
				'font-size-h3'                      => 24,
				'font-family-h4'                    => 'Open Sans',
				'font-size-h4'                      => 24,
				'font-family-h5'                    => 'Open Sans',
				'font-size-h5'                      => 16,
				'font-family-h6'                    => 'Open Sans',
				'font-size-h6'                      => 14,
				'font-family-site-title'            => 'Cookie',
				'font-size-site-title'              => 34,
				'font-family-site-tagline'          => 'Open Sans',
				'font-size-site-tagline'            => 12,
				'font-family-nav'					=> 'Open Sans',
				'font-size-nav'                     => 14,
				'font-family-subnav'				=> 'Open Sans',
				'font-size-subnav'                  => 13,
				'font-family-widget'                => 'Open Sans',
				'font-size-widget'                  => 14,
				/**
				 * Color
				 */
				'color-primary'                     => '#fe6541',
				'color-secondary'                   => '#bac6cd',
				'color-text'                        => '#667077',
				'color-detail'                      => '#bac6cd',
				'background_color'                  => '#b9bcbf',
				'main-background-color'             => '#fafbf8',
				'header-text-color'                 => '#667077',
				'header-background-color'           => '#fafbf8',
				'color-site-title'                  => '#fe6541',
				'header-bar-background-color'       => '#bac6cd',
				'header-bar-text-color'             => '#171717',
				'header-bar-border-color'           => '#fafbf8',
				'footer-text-color'                 => '#667077',
				'footer-border-color'               => '#bac6cd',
				'footer-background-color'           => '#fafbf8',
				/**
				 * Background Images
				 */
				'background_image'                  => '',
				'header-background-image'           => '',
				'main-background-image'             => '',
				'footer-background-image'           => '',
				/**
				 * Layout
				 */
				'general-layout'                    => 'full-width',
				'header-layout'                     => 1,
				'footer-layout'                     => 1,
			)
		),
		'vintage' => array(
			'label' => __( 'Vintage', 'make-plus' ),
			'priority' => 50,
			'definitions' => array(
				/**
				 * Typography
				 */
				'font-family-body'                  => 'IM Fell English',
				'font-size-body'                    => 17,
				'font-family-h1'                    => 'Playfair Display',
				'font-size-h1'                      => 50,
				'font-family-h2'                    => 'Playfair Display',
				'font-size-h2'                      => 34,
				'font-family-h3'                    => 'Playfair Display',
				'font-size-h3'                      => 24,
				'letter-spacing-h3'                 => 1,
				'font-family-h4'                    => 'Playfair Display',
				'font-size-h4'                      => 24,
				'font-family-h5'                    => 'Playfair Display',
				'font-size-h5'                      => 16,
				'font-family-h6'                    => 'Playfair Display',
				'font-size-h6'                      => 14,
				'font-family-site-title'            => 'Playfair Display',
				'font-size-site-title'              => 78,
				'text-transform-site-title'         => 'uppercase',
				'letter-spacing-site-title'         => 8,
				'font-family-site-tagline'          => 'IM Fell English',
				'font-size-site-tagline'            => 12,
				'font-family-nav'					=> 'IM Fell English',
				'font-size-nav'                     => 15,
				'font-weight-nav'                   => 'bold',
				'font-style-nav'                    => 'italic',
				'font-family-subnav'				=> 'IM Fell English',
				'font-size-subnav'                  => 13,
				'font-family-widget'                => 'IM Fell English',
				'font-size-widget'                  => 14,
				/**
				 * Color
				 */
				'color-primary'                     => '#d0601d',
				'color-secondary'                   => '#dedabe',
				'color-text'                        => '#22231c',
				'color-detail'                      => '#dedabe',
				'background_color'                  => '#393a32',
				'main-background-color'             => '#fbf9ec',
				'header-text-color'                 => '#22231c',
				'header-background-color'           => '#fbf9ec',
				'color-site-title'                  => '#22231c',
				'header-bar-background-color'       => '#22231c',
				'header-bar-text-color'             => '#ffffff',
				'header-bar-border-color'           => '#22231c',
				'footer-text-color'                 => '#22231c',
				'footer-border-color'               => '#dedabe',
				'footer-background-color'           => '#fbf9ec',
				/**
				 * Background Images
				 */
				'background_image'                  => '',
				'header-background-image'           => '',
				'main-background-image'             => '',
				'footer-background-image'           => '',
				/**
				 * Layout
				 */
				'general-layout'                    => 'boxed',
				'header-layout'                     => 2,
				'footer-layout'                     => 2,
			)
		),
		'hello' => array(
			'label' => __( 'Hello', 'make-plus' ),
			'priority' => 5,
			'definitions' => array(
				/**
				 * Typography
				 */
				'font-family-body'                         => 'PT Mono',
				'font-size-body'                           => 14,
				'line-height-body'                         => 1.7,
				'font-family-h1'                           => 'Karla',
				'font-size-h1'                             => 46,
				'font-family-h2'                           => 'Karla',
				'font-size-h2'                             => 34,
				'font-family-h3'                           => 'Karla',
				'font-size-h3'                             => 24,
				'font-family-h4'                           => 'Karla',
				'font-size-h4'                             => 24,
				'font-family-h5'                           => 'Karla',
				'font-size-h5'                             => 16,
				'font-family-h6'                           => 'Karla',
				'font-size-h6'                             => 14,
				'font-family-site-title'                   => 'Kalam',
				'font-size-site-title'                     => 70,
				'font-family-site-tagline'                 => 'PT Mono',
				'font-size-site-tagline'                   => 14,
				'font-family-nav'                          => 'Karla',
				'font-size-nav'                            => 13,
				'font-weight-nav'                          => 'bold',
				'text-transform-nav'                       => 'uppercase',
				'letter-spacing-nav'                       => 1,
				'font-family-subnav'                       => 'PT Mono',
				'font-size-subnav'                         => 13,
				'font-family-widget'                       => 'PT Mono',
				'font-size-widget'                         => 12,
				/**
				 * Color
				 */
				'color-primary'                            => '#003bff',
				'color-secondary'                          => '#eaecee',
				'color-text'                               => '#000000',
				'color-detail'                             => '#b9bcbf',
				'background_color'                         => 'b9bcbf',
				'main-background-color'                    => '#f7f7f7',
				'header-background-color'                  => '#f7f7f7',
				'header-text-color'                        => '#000000',
				'color-site-title'                         => '#000000',
				'header-bar-background-color'              => '#171717',
				'header-bar-text-color'                    => '#ffffff',
				'header-bar-border-color'                  => '#171717',
				'footer-background-color'                  => '#ffffff',
				'footer-text-color'                        => '#000000',
				'footer-border-color'                      => '#000000',
				/**
				 * Background Images
				 */
				'background_image'                         => '',
				'header-background-image'                  => '',
				'main-background-image'                    => '',
				'footer-background-image'                  => '',
				/**
				 * Layout
				 */
				'general-layout'                           => 'full-width',
				'header-layout'                            => 2,
				'footer-layout'                            => 2,
				'font-size-footer-icon'                    => 24,
				'layout-blog-featured-images'              => 'post-header',
				'layout-blog-post-date'                    => 'relative',
				'layout-blog-post-author'                  => 'avatar',
				'layout-blog-auto-excerpt'                 => 0,
				'layout-blog-show-categories'              => 1,
				'layout-blog-show-tags'                    => 1,
				'layout-blog-featured-images-alignment'    => 'left',
				'layout-blog-post-date-location'           => 'before-content',
				'layout-blog-post-author-location'         => 'before-content',
				'layout-blog-comment-count'                => 'icon',
				'layout-blog-comment-count-location'       => 'post-footer',
				'layout-archive-featured-images'           => 'post-header',
				'layout-archive-post-date'                 => 'relative',
				'layout-archive-post-author'               => 'avatar',
				'layout-archive-auto-excerpt'              => 0,
				'layout-archive-show-categories'           => 1,
				'layout-archive-show-tags'                 => 1,
				'layout-archive-featured-images-alignment' => 'left',
				'layout-archive-post-date-location'        => 'before-content',
				'layout-archive-post-author-location'      => 'before-content',
				'layout-archive-comment-count'             => 'icon',
				'layout-archive-comment-count-location'    => 'post-footer',
				'layout-search-featured-images'            => 'thumbnail',
				'layout-search-post-date'                  => 'relative',
				'layout-search-post-author'                => 'none',
				'layout-search-auto-excerpt'               => 1,
				'layout-search-show-categories'            => 1,
				'layout-search-show-tags'                  => 1,
				'layout-search-post-date-location'         => 'before-content',
				'layout-search-comment-count'              => 'none',
				'layout-post-featured-images'              => 'post-header',
				'layout-post-post-date'                    => 'relative',
				'layout-post-post-author'                  => 'avatar',
				'layout-post-show-categories'              => 1,
				'layout-post-show-tags'                    => 1,
				'layout-post-featured-images-alignment'    => 'left',
				'layout-post-post-date-location'           => 'before-content',
				'layout-post-post-author-location'         => 'before-content',
				'layout-post-comment-count'                => 'none',
				'layout-page-featured-images'              => 'none',
				'layout-page-post-date'                    => 'none',
				'layout-page-post-author'                  => 'none',
				'layout-page-comment-count'                => 'none',
			),
		),
	);

	return apply_filters( 'ttfmp_style_kit_definitions', $definitions );
}
endif;
