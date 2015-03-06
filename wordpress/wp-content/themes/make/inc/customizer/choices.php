<?php
/**
 * @package Make
 */

if ( ! function_exists( 'ttfmake_get_choices' ) ) :
/**
 * Return the available choices for a given setting
 *
 * @since  1.0.0.
 *
 * @param  string|object    $setting    The setting to get options for.
 * @return array                        The options for the setting.
 */
function ttfmake_get_choices( $setting ) {
	if ( is_object( $setting ) ) {
		$setting = $setting->id;
	}

	$choices = array( 0 );

	switch ( $setting ) {
		case 'font-weight-body' :
		case 'font-weight-body-link' :
		case 'font-weight-h1' :
		case 'font-weight-h2' :
		case 'font-weight-h3' :
		case 'font-weight-h4' :
		case 'font-weight-h5' :
		case 'font-weight-h6' :
		case 'font-weight-site-title' :
		case 'font-weight-site-tagline' :
		case 'font-weight-nav' :
		case 'font-weight-subnav' :
		case 'font-weight-nav-current-item' :
		case 'font-weight-header-bar-text' :
		case 'font-weight-widget-title' :
		case 'font-weight-widget' :
		case 'font-weight-footer-text' :
		case 'font-weight-footer-widget-title' :
		case 'font-weight-footer-widget' :
			$choices = array(
				'normal' => __( 'Normal', 'make' ),
				'bold'   => __( 'Bold', 'make' ),
			);
			break;
		case 'font-style-body' :
		case 'font-style-h1' :
		case 'font-style-h2' :
		case 'font-style-h3' :
		case 'font-style-h4' :
		case 'font-style-h5' :
		case 'font-style-h6' :
		case 'font-style-site-title' :
		case 'font-style-site-tagline' :
		case 'font-style-nav' :
		case 'font-style-subnav' :
		case 'font-style-header-bar-text' :
		case 'font-style-widget-title' :
		case 'font-style-widget' :
		case 'font-style-footer-text' :
		case 'font-style-footer-widget-title' :
		case 'font-style-footer-widget' :
			$choices = array(
				'normal' => __( 'Normal', 'make' ),
				'italic' => __( 'Italic', 'make' ),
			);
			break;
		case 'text-transform-body' :
		case 'text-transform-h1' :
		case 'text-transform-h2' :
		case 'text-transform-h3' :
		case 'text-transform-h4' :
		case 'text-transform-h5' :
		case 'text-transform-h6' :
		case 'text-transform-site-title' :
		case 'text-transform-site-tagline' :
		case 'text-transform-nav' :
		case 'text-transform-subnav' :
		case 'text-transform-header-bar-text' :
		case 'text-transform-widget-title' :
		case 'text-transform-widget' :
		case 'text-transform-footer-text' :
		case 'text-transform-footer-widget-title' :
		case 'text-transform-footer-widget' :
			$choices = array(
				'none'      => __( 'None', 'make' ),
				'uppercase' => __( 'Uppercase', 'make' ),
				'lowercase' => __( 'Lowercase', 'make' ),
			);
			break;
		case 'link-underline-body' :
		case 'link-underline-h1' :
		case 'link-underline-h2' :
		case 'link-underline-h3' :
		case 'link-underline-h4' :
		case 'link-underline-h5' :
		case 'link-underline-h6' :
		case 'link-underline-site-title' :
		case 'link-underline-site-tagline' :
		case 'link-underline-nav' :
		case 'link-underline-subnav' :
		case 'link-underline-header-bar-text' :
		case 'link-underline-widget-title' :
		case 'link-underline-widget' :
		case 'link-underline-footer-text' :
		case 'link-underline-footer-widget-title' :
		case 'link-underline-footer-widget' :
			$choices = array(
				'always' => __( 'Always', 'make' ),
				'hover'  => __( 'On hover/focus', 'make' ),
				'never'  => __( 'Never', 'make' ),
			);
			break;
		case 'background_repeat' :
		case 'header-background-repeat' :
		case 'main-background-repeat' :
		case 'footer-background-repeat' :
			$choices = array(
				'no-repeat' => __( 'No Repeat', 'make' ),
				'repeat'    => __( 'Tile', 'make' ),
				'repeat-x'  => __( 'Tile Horizontally', 'make' ),
				'repeat-y'  => __( 'Tile Vertically', 'make' )
			);
			break;
		case 'background_position_x' :
		case 'header-background-position' :
		case 'main-background-position' :
		case 'footer-background-position' :
			$choices = array(
				'top-left'      => __( 'Top Left', 'make' ),
				'top'           => __( 'Top', 'make' ),
				'top-right'     => __( 'Top Right', 'make' ),
				'left'          => __( 'Left', 'make' ),
				'center'        => __( 'Center', 'make' ),
				'right'         => __( 'Right', 'make' ),
				'bottom-left'   => __( 'Bottom Left', 'make' ),
				'bottom'        => __( 'Bottom', 'make' ),
				'bottom-right'  => __( 'Bottom Right', 'make' ),
			);
			break;
		case 'background_attachment':
		case 'header-background-attachment':
		case 'main-background-attachment':
		case 'footer-background-attachment':
			$choices = array(
				'scroll' => __( 'Scroll', 'make' ),
				'fixed'  => __( 'Fixed', 'make' ),
			);
			break;
		case 'background_size' :
		case 'header-background-size' :
		case 'main-background-size' :
		case 'footer-background-size' :
			$choices = array(
				'auto'    => __( 'Auto', 'make' ),
				'cover'   => __( 'Cover', 'make' ),
				'contain' => __( 'Contain', 'make' )
			);
			break;
		case 'general-layout' :
			$choices = array(
				'full-width' => __( 'Full-width', 'make' ),
				'boxed'      => __( 'Boxed', 'make' )
			);
			break;
		case 'header-layout' :
			$choices = array(
				1  => __( 'Traditional', 'make' ),
				2  => __( 'Centered', 'make' ),
				3  => __( 'Navigation Below', 'make' ),
			);
			break;
		case 'header-branding-position' :
			$choices = array(
				'left'  => __( 'Left', 'make' ),
				'right' => __( 'Right', 'make' )
			);
			break;
		case 'header-bar-content-layout' :
			$choices = array(
				'default' => __( 'Default', 'make' ),
				'flipped' => __( 'Flipped', 'make' )
			);
			break;
		case 'footer-widget-areas' :
			$choices = array(
				0 => _x( '0', 'footer widget area number', 'make' ),
				1 => _x( '1', 'footer widget area number', 'make' ),
				2 => _x( '2', 'footer widget area number', 'make' ),
				3 => _x( '3', 'footer widget area number', 'make' ),
				4 => _x( '4', 'footer widget area number', 'make' )
			);
			break;
		case 'footer-layout' :
			$choices = array(
				1  => __( 'Traditional', 'make' ),
				2  => __( 'Centered', 'make' ),
			);
			break;
		case 'layout-blog-featured-images' :
		case 'layout-archive-featured-images' :
		case 'layout-search-featured-images' :
		case 'layout-post-featured-images' :
		case 'layout-page-featured-images' :
			$choices = array(
				'post-header' => __( 'Post header', 'make' ),
				'thumbnail'   => __( 'Thumbnail', 'make' ),
				'none'        => __( 'None', 'make' ),
			);
			break;
		case 'layout-blog-featured-images-alignment' :
		case 'layout-archive-featured-images-alignment' :
		case 'layout-search-featured-images-alignment' :
		case 'layout-post-featured-images-alignment' :
		case 'layout-page-featured-images-alignment' :
			$choices = array(
				'left'   => __( 'Left', 'make' ),
				'center' => __( 'Center', 'make' ),
				'right'  => __( 'Right', 'make' )
			);
			break;
		case 'layout-blog-post-date' :
		case 'layout-archive-post-date' :
		case 'layout-search-post-date' :
		case 'layout-post-post-date' :
		case 'layout-page-post-date' :
			$week_ago = date( get_option( 'date_format' ), time() - WEEK_IN_SECONDS );
			$choices = array(
				'absolute' => sprintf( __( 'Absolute (%s)', 'make' ), $week_ago ),
				'relative' => __( 'Relative (1 week ago)', 'make' ),
				'none'     => __( 'None', 'make' ),
			);
			break;
		case 'layout-blog-post-author' :
		case 'layout-archive-post-author' :
		case 'layout-search-post-author' :
		case 'layout-post-post-author' :
		case 'layout-page-post-author' :
			$choices = array(
				'avatar' => __( 'With avatar', 'make' ),
				'name'   => __( 'Without avatar', 'make' ),
				'none'   => __( 'None', 'make' ),
			);
			break;
		case 'layout-blog-comment-count' :
		case 'layout-archive-comment-count' :
		case 'layout-search-comment-count' :
		case 'layout-post-comment-count' :
		case 'layout-page-comment-count' :
			$choices = array(
				'icon' => __( 'With icon', 'make' ),
				'text' => __( 'With text', 'make' ),
				'none' => __( 'None', 'make' ),
			);
			break;
		case 'layout-blog-post-date-location' :
		case 'layout-blog-post-author-location' :
		case 'layout-blog-comment-count-location' :
		case 'layout-archive-post-date-location' :
		case 'layout-archive-post-author-location' :
		case 'layout-archive-comment-count-location' :
		case 'layout-search-post-date-location' :
		case 'layout-search-post-author-location' :
		case 'layout-search-comment-count-location' :
		case 'layout-post-post-date-location' :
		case 'layout-post-post-author-location' :
		case 'layout-post-comment-count-location' :
		case 'layout-page-post-date-location' :
		case 'layout-page-post-author-location' :
		case 'layout-page-comment-count-location' :
			$choices = array(
				'top'            => __( 'Top', 'make' ),
				'before-content' => __( 'Before content', 'make' ),
				'post-footer'    => __( 'Post footer', 'make' ),
			);
			break;
	}

	/**
	 * Filter the setting choices.
	 *
	 * @since 1.2.3.
	 *
	 * @param array     $choices    The choices for the setting.
	 * @param string    $setting    The setting name.
	 */
	return apply_filters( 'make_setting_choices', $choices, $setting );
}
endif;

if ( ! function_exists( 'ttfmake_sanitize_choice' ) ) :
/**
 * Sanitize a value from a list of allowed values.
 *
 * @since 1.0.0.
 *
 * @param  mixed    $value      The value to sanitize.
 * @param  mixed    $setting    The setting for which the sanitizing is occurring.
 * @return mixed                The sanitized value.
 */
function ttfmake_sanitize_choice( $value, $setting ) {
	if ( is_object( $setting ) ) {
		$setting = $setting->id;
	}

	$choices         = ttfmake_get_choices( $setting );
	$allowed_choices = array_keys( $choices );

	if ( ! in_array( $value, $allowed_choices ) ) {
		$value = ttfmake_get_default( $setting );
	}

	/**
	 * Filter the sanitized value.
	 *
	 * @since 1.2.3.
	 *
	 * @param mixed     $value      The sanitized value.
	 * @param string    $setting    The key for the setting.
	 */
	return apply_filters( 'make_sanitize_choice', $value, $setting );
}
endif;