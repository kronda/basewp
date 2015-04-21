<?php
/**
 * @package Make
 */

if ( ! function_exists( 'ttfmake_option_defaults' ) ) :
/**
 * The big array of global option defaults.
 *
 * @since  1.0.0
 *
 * @return array    The default values for all theme options.
 */
function ttfmake_option_defaults() {
	$defaults = array(
		/**
		 * General
		 */
		// Site Title & Tagline
		'hide-site-title'                          => 0,
		'hide-tagline'                             => 0,
		// Logo
		'logo-regular'                             => '',
		'logo-retina'                              => '',
		'logo-favicon'                             => '',
		'logo-apple-touch'                         => '',
		// Labels
		'navigation-mobile-label'                  => __( 'Menu', 'make' ),
		'general-sticky-label'                     => __( 'Featured', 'make' ),
		'label-read-more'                          => __( 'Read more', 'make' ),
		// Social Profiles
		'social-facebook-official'                 => '',
		'social-twitter'                           => '',
		'social-google-plus-square'                => '',
		'social-linkedin'                          => '',
		'social-instagram'                         => '',
		'social-flickr'                            => '',
		'social-youtube'                           => '',
		'social-vimeo-square'                      => '',
		'social-pinterest'                         => '',
		// Email
		'social-email'                             => '',
		// RSS
		'social-hide-rss'                          => 0,
		'social-custom-rss'                        => '',

		/**
		 * Typography
		 */
		// Global/Default
		'font-family-body'                         => 'Open Sans',
		'font-size-body'                           => 17,
		'font-weight-body'                         => 'normal',
		'font-style-body'                          => 'normal',
		'text-transform-body'                      => 'none',
		'line-height-body'                         => (float) 1.6,
		'letter-spacing-body'                      => (float) 0,
		'word-spacing-body'                        => (float) 0,
		'link-underline-body'                      => 'never',
		// Links
		'font-weight-body-link'                    => 'bold',
		// H1
		'font-family-h1'                           => 'sans-serif',
		'font-size-h1'                             => 46,
		'font-weight-h1'                           => 'normal',
		'font-style-h1'                            => 'normal',
		'text-transform-h1'                        => 'none',
		'line-height-h1'                           => (float) 1.2,
		'letter-spacing-h1'                        => (float) 0,
		'word-spacing-h1'                          => (float) 0,
		'link-underline-h1'                        => 'never',
		// H2
		'font-family-h2'                           => 'sans-serif',
		'font-size-h2'                             => 34,
		'font-weight-h2'                           => 'bold',
		'font-style-h2'                            => 'normal',
		'text-transform-h2'                        => 'none',
		'line-height-h2'                           => (float) 1.6,
		'letter-spacing-h2'                        => (float) 0,
		'word-spacing-h2'                          => (float) 0,
		'link-underline-h2'                        => 'never',
		// H3
		'font-family-h3'                           => 'sans-serif',
		'font-size-h3'                             => 24,
		'font-weight-h3'                           => 'bold',
		'font-style-h3'                            => 'normal',
		'text-transform-h3'                        => 'none',
		'line-height-h3'                           => (float) 1.6,
		'letter-spacing-h3'                        => (float) 0,
		'word-spacing-h3'                          => (float) 0,
		'link-underline-h3'                        => 'never',
		// H4
		'font-family-h4'                           => 'sans-serif',
		'font-size-h4'                             => 24,
		'font-weight-h4'                           => 'normal',
		'font-style-h4'                            => 'normal',
		'text-transform-h4'                        => 'none',
		'line-height-h4'                           => (float) 1.6,
		'letter-spacing-h4'                        => (float) 0,
		'word-spacing-h4'                          => (float) 0,
		'link-underline-h4'                        => 'never',
		// H5
		'font-family-h5'                           => 'sans-serif',
		'font-size-h5'                             => 16,
		'font-weight-h5'                           => 'bold',
		'font-style-h5'                            => 'normal',
		'text-transform-h5'                        => 'uppercase',
		'line-height-h5'                           => (float) 1.6,
		'letter-spacing-h5'                        => (float) 1,
		'word-spacing-h5'                          => (float) 0,
		'link-underline-h5'                        => 'never',
		// H6
		'font-family-h6'                           => 'sans-serif',
		'font-size-h6'                             => 14,
		'font-weight-h6'                           => 'normal',
		'font-style-h6'                            => 'normal',
		'text-transform-h6'                        => 'uppercase',
		'line-height-h6'                           => (float) 1.6,
		'letter-spacing-h6'                        => (float) 2,
		'word-spacing-h6'                          => (float) 0,
		'link-underline-h6'                        => 'never',
		// Site Title
		'font-family-site-title'                   => 'sans-serif',
		'font-size-site-title'                     => 34,
		'font-weight-site-title'                   => 'bold',
		'font-style-site-title'                    => 'normal',
		'text-transform-site-title'                => 'none',
		'line-height-site-title'                   => (float) 1.2,
		'letter-spacing-site-title'                => (float) 0,
		'word-spacing-site-title'                  => (float) 0,
		'link-underline-site-title'                => 'never',
		// Tagline
		'font-family-site-tagline'                 => 'Open Sans',
		'font-size-site-tagline'                   => 12,
		'font-weight-site-tagline'                 => 'normal',
		'font-style-site-tagline'                  => 'normal',
		'text-transform-site-tagline'              => 'uppercase',
		'line-height-site-tagline'                 => (float) 1.6,
		'letter-spacing-site-tagline'              => (float) 1,
		'word-spacing-site-tagline'                => (float) 0,
		'link-underline-site-tagline'              => 'never',
		// Menu Items
		'font-family-nav'                          => 'Open Sans',
		'font-size-nav'                            => 14,
		'font-weight-nav'                          => 'normal',
		'font-style-nav'                           => 'normal',
		'text-transform-nav'                       => 'none',
		'line-height-nav'                          => (float) 1.4,
		'letter-spacing-nav'                       => (float) 0,
		'word-spacing-nav'                         => (float) 0,
		'link-underline-nav'                       => 'never',
		// Sub-Menu Items
		'font-family-subnav'                       => 'Open Sans',
		'font-size-subnav'                         => 13,
		'font-weight-subnav'                       => 'normal',
		'font-style-subnav'                        => 'normal',
		'text-transform-subnav'                    => 'none',
		'line-height-subnav'                       => (float) 1.4,
		'letter-spacing-subnav'                    => (float) 0,
		'word-spacing-subnav'                      => (float) 0,
		'link-underline-subnav'                    => 'never',
		'font-subnav-mobile'                       => 1,
		// Current Item
		'font-weight-nav-current-item'             => 'bold',
		// Header Bar Text
		'font-family-header-bar-text'              => 'Open Sans',
		'font-size-header-bar-text'                => 13,
		'font-weight-header-bar-text'              => 'normal',
		'font-style-header-bar-text'               => 'normal',
		'text-transform-header-bar-text'           => 'none',
		'line-height-header-bar-text'              => (float) 1.6,
		'letter-spacing-header-bar-text'           => (float) 0,
		'word-spacing-header-bar-text'             => (float) 0,
		'link-underline-header-bar-text'           => 'never',
		// Header Bar Icons
		'font-size-header-bar-icon'                => 20,
		// Sidebar Widget Title
		'font-family-widget-title'                 => 'Open Sans',
		'font-size-widget-title'                   => 13,
		'font-weight-widget-title'                 => 'bold',
		'font-style-widget-title'                  => 'normal',
		'text-transform-widget-title'              => 'none',
		'line-height-widget-title'                 => (float) 1.6,
		'letter-spacing-widget-title'              => (float) 0,
		'word-spacing-widget-title'                => (float) 0,
		'link-underline-widget-title'              => 'never',
		// Sidebar Widget Body
		'font-family-widget'                       => 'Open Sans',
		'font-size-widget'                         => 13,
		'font-weight-widget'                       => 'normal',
		'font-style-widget'                        => 'normal',
		'text-transform-widget'                    => 'none',
		'line-height-widget'                       => (float) 1.6,
		'letter-spacing-widget'                    => (float) 0,
		'word-spacing-widget'                      => (float) 0,
		'link-underline-widget'                    => 'never',
		// Footer Text
		'font-family-footer-text'                  => 'Open Sans',
		'font-size-footer-text'                    => 13,
		'font-weight-footer-text'                  => 'normal',
		'font-style-footer-text'                   => 'normal',
		'text-transform-footer-text'               => 'none',
		'line-height-footer-text'                  => (float) 1.6,
		'letter-spacing-footer-text'               => (float) 0,
		'word-spacing-footer-text'                 => (float) 0,
		'link-underline-footer-text'               => 'never',
		// Footer Widget Title
		'font-family-footer-widget-title'          => 'Open Sans',
		'font-size-footer-widget-title'            => 13,
		'font-weight-footer-widget-title'          => 'bold',
		'font-style-footer-widget-title'           => 'normal',
		'text-transform-footer-widget-title'       => 'none',
		'line-height-footer-widget-title'          => (float) 1.6,
		'letter-spacing-footer-widget-title'       => (float) 0,
		'word-spacing-footer-widget-title'         => (float) 0,
		'link-underline-footer-widget-title'       => 'never',
		// Footer Widget Body
		'font-family-footer-widget'                => 'Open Sans',
		'font-size-footer-widget'                  => 13,
		'font-weight-footer-widget'                => 'normal',
		'font-style-footer-widget'                 => 'normal',
		'text-transform-footer-widget'             => 'none',
		'line-height-footer-widget'                => (float) 1.6,
		'letter-spacing-footer-widget'             => (float) 0,
		'word-spacing-footer-widget'               => (float) 0,
		'link-underline-footer-widget'             => 'never',
		// Footer Icons
		'font-size-footer-icon'                    => 20,
		// Google Web Fonts
		'font-subset'                              => 'latin',

		/**
		 * Color
		 */
		// Color Scheme
		'color-primary'                            => '#3070d1',
		'color-secondary'                          => '#eaecee',
		'color-text'                               => '#171717',
		'color-detail'                             => '#b9bcbf',
		// Links
		'color-primary-link'                       => '',
		// Background
		'background_color'                         => 'b9bcbf' , // '#' intentionally left off here
		'main-background-color'                    => '#ffffff',
		'main-background-color-opacity'            => 1,
		// Header
		'header-text-color'                        => '#171717',
		'header-background-color'                  => '#ffffff',
		'header-background-color-opacity'          => 1,
		// Site Title
		'color-site-title'                         => '',
		// Tagline
		'color-site-tagline'                       => '',
		// Menu Items
		'color-nav-text'                           => '',
		'color-nav-text-hover'                     => '',
		// Sub-Menu Items
		'color-subnav-text'                        => '',
		'color-subnav-text-hover'                  => '',
		'color-subnav-detail'                      => '',
		'color-subnav-background'                  => '',
		'color-subnav-background-opacity'          => 1,
		'color-subnav-background-hover'            => '',
		'color-subnav-background-hover-opacity'    => 1,
		// Current Item
		'color-nav-current-item-background'        => '',
		'color-nav-current-item-background-opacity'=> 1,
		// Header Bar
		'header-bar-text-color'                    => '#ffffff',
		'header-bar-link-color'                    => '',
		'header-bar-link-hover-color'              => '',
		'header-bar-border-color'                  => '#171717',
		'header-bar-background-color'              => '#171717',
		'header-bar-background-color-opacity'      => 1,
		// Sidebars
		'color-widget-title-text'                  => '',
		'color-widget-text'                        => '',
		'color-widget-border'                      => '',
		'color-widget-link'                        => '',
		'color-widget-link-hover'                  => '',
		// Footer
		'footer-text-color'                        => '#464849',
		'footer-link-color'                        => '',
		'footer-link-hover-color'                  => '',
		'footer-border-color'                      => '#b9bcbf',
		'footer-background-color'                  => '#eaecee',
		'footer-background-color-opacity'          => 1,

		/**
		 * Background Images
		 */
		// Site
		'background_image'                         => '',
		'background_repeat'                        => 'repeat',
		'background_position_x'                    => 'left',
		'background_attachment'                    => 'scroll',
		'background_size'                          => 'auto',
		// Header
		'header-background-image'                  => '',
		'header-background-repeat'                 => 'no-repeat',
		'header-background-position'               => 'center',
		'header-background-attachment'             => 'scroll',
		'header-background-size'                   => 'cover',
		// Main column
		'main-background-image'                    => '',
		'main-background-repeat'                   => 'repeat',
		'main-background-position'                 => 'left',
		'main-background-attachment'               => 'scroll',
		'main-background-size'                     => 'auto',
		// Footer
		'footer-background-image'                  => '',
		'footer-background-repeat'                 => 'no-repeat',
		'footer-background-position'               => 'center',
		'footer-background-attachment'             => 'scroll',
		'footer-background-size'                   => 'cover',

		/**
		 * Layout
		 */
		// Global
		'general-layout'                           => 'full-width',
		// Header
		'header-layout'                            => 1,
		'header-branding-position'                 => 'left',
		'header-bar-content-layout'                => 'default',
		'header-hide-padding-bottom'               => 0,
		'header-text'                              => '',
		'header-show-social'                       => 0,
		'header-show-search'                       => 1,
		// Footer
		'footer-widget-areas'                      => 3,
		'footer-layout'                            => 1,
		'footer-hide-padding-top'                  => 0,
		'footer-text'                              => '',
		'footer-show-social'                       => 1,
		// Blog (Posts Page)
		'layout-blog-hide-header'                  => 0,
		'layout-blog-hide-footer'                  => 0,
		'layout-blog-sidebar-left'                 => 0,
		'layout-blog-sidebar-right'                => 1,
		'layout-blog-featured-images'              => 'post-header',
		'layout-blog-post-date'                    => 'absolute',
		'layout-blog-post-author'                  => 'avatar',
		'layout-blog-auto-excerpt'                 => 0,
		'layout-blog-show-categories'              => 1,
		'layout-blog-show-tags'                    => 1,
		'layout-blog-featured-images-alignment'    => 'center',
		'layout-blog-post-date-location'           => 'top',
		'layout-blog-post-author-location'         => 'post-footer',
		'layout-blog-comment-count'                => 'none',
		'layout-blog-comment-count-location'       => 'before-content',
		// Archives
		'layout-archive-hide-header'               => 0,
		'layout-archive-hide-footer'               => 0,
		'layout-archive-sidebar-left'              => 0,
		'layout-archive-sidebar-right'             => 1,
		'layout-archive-featured-images'           => 'post-header',
		'layout-archive-post-date'                 => 'absolute',
		'layout-archive-post-author'               => 'avatar',
		'layout-archive-auto-excerpt'              => 0,
		'layout-archive-show-categories'           => 1,
		'layout-archive-show-tags'                 => 1,
		'layout-archive-featured-images-alignment' => 'center',
		'layout-archive-post-date-location'        => 'top',
		'layout-archive-post-author-location'      => 'post-footer',
		'layout-archive-comment-count'             => 'none',
		'layout-archive-comment-count-location'    => 'before-content',
		// Search Results
		'layout-search-hide-header'                => 0,
		'layout-search-hide-footer'                => 0,
		'layout-search-sidebar-left'               => 0,
		'layout-search-sidebar-right'              => 1,
		'layout-search-featured-images'            => 'thumbnail',
		'layout-search-post-date'                  => 'absolute',
		'layout-search-post-author'                => 'name',
		'layout-search-auto-excerpt'               => 1,
		'layout-search-show-categories'            => 1,
		'layout-search-show-tags'                  => 1,
		'layout-search-featured-images-alignment'  => 'center',
		'layout-search-post-date-location'         => 'top',
		'layout-search-post-author-location'       => 'post-footer',
		'layout-search-comment-count'              => 'none',
		'layout-search-comment-count-location'     => 'before-content',
		// Posts
		'layout-post-hide-header'                  => 0,
		'layout-post-hide-footer'                  => 0,
		'layout-post-sidebar-left'                 => 0,
		'layout-post-sidebar-right'                => 0,
		'layout-post-featured-images'              => 'post-header',
		'layout-post-post-date'                    => 'absolute',
		'layout-post-post-author'                  => 'avatar',
		'layout-post-show-categories'              => 1,
		'layout-post-show-tags'                    => 1,
		'layout-post-featured-images-alignment'    => 'center',
		'layout-post-post-date-location'           => 'top',
		'layout-post-post-author-location'         => 'post-footer',
		'layout-post-comment-count'                => 'none',
		'layout-post-comment-count-location'       => 'before-content',
		// Pages
		'layout-page-hide-header'                  => 0,
		'layout-page-hide-footer'                  => 0,
		'layout-page-sidebar-left'                 => 0,
		'layout-page-sidebar-right'                => 0,
		'layout-page-featured-images'              => 'none',
		'layout-page-post-date'                    => 'none',
		'layout-page-post-author'                  => 'none',
		'layout-page-featured-images-alignment'    => 'center',
		'layout-page-post-date-location'           => 'top',
		'layout-page-post-author-location'         => 'post-footer',
		'layout-page-comment-count'                => 'none',
		'layout-page-comment-count-location'       => 'before-content',
		'layout-page-hide-title'                   => 1,

		/**
		 * Deprecated defaults
		 */
		'font-site-title'                          => 'sans-serif',
		'font-header'                              => 'sans-serif',
		'font-body'                                => 'Open Sans',
		'font-site-title-size'                     => 34,
		'font-site-tagline-size'                   => 12,
		'font-nav-size'                            => 14,
		'font-header-size'                         => 46,
		'font-widget-size'                         => 13,
		'font-body-size'                           => 17,
		'social-facebook'                          => '',
		'main-content-link-underline'              => 0,
	);

	/**
	 * Filter the default values for the settings.
	 *
	 * @since 1.2.3.
	 *
	 * @param array    $defaults    The list of default settings.
	 */
	return apply_filters( 'make_setting_defaults', $defaults );
}
endif;

if ( ! function_exists( 'ttfmake_get_default' ) ) :
/**
 * Return a particular global option default.
 *
 * @since  1.0.0.
 *
 * @param  string    $option    The key of the option to return.
 * @return mixed                Default value if found; false if not found.
 */
function ttfmake_get_default( $option ) {
	$defaults = ttfmake_option_defaults();
	$default  = ( isset( $defaults[ $option ] ) ) ? $defaults[ $option ] : false;

	/**
	 * Filter the retrieved default value.
	 *
	 * @since 1.2.3.
	 *
	 * @param mixed     $default    The default value.
	 * @param string    $option     The name of the default value.
	 */
	return apply_filters( 'make_get_default', $default, $option );
}
endif;
