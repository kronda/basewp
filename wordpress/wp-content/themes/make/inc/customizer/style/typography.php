<?php
/**
 * @package Make
 */

if ( ! function_exists( 'ttfmake_css_fonts' ) ) :
/**
 * Build the CSS rules for the custom fonts
 *
 * @since  1.0.0
 *
 * @return void
 */
function ttfmake_css_fonts() {
	// Use legacy function instead if no panel support
	if ( ! ttfmake_customizer_supports_panels() ) {
		ttfmake_css_legacy_fonts();
		return;
	}

	// Get relative sizes
	$percent = ttfmake_font_get_relative_sizes();

	/**
	 * Body
	 */
	$element = 'body';
	$selectors = array( 'body', '.font-body' );
	$declarations = ttfmake_parse_font_properties( $element );
	if ( ! empty( $declarations ) ) {
		ttfmake_get_css()->add( array( 'selectors' => $selectors, 'declarations' => $declarations, ) );
	}
	$link_rule = ttfmake_parse_link_underline( $element, array( 'a' ) );
	if ( ! empty( $link_rule ) ) {
		ttfmake_get_css()->add( $link_rule );
	}
	// Links
	$link_font_weight = ttfmake_sanitize_choice( get_theme_mod( 'font-weight-body-link', ttfmake_get_default( 'font-weight-body-link' ) ), 'font-weight-body-link' );
	if ( $link_font_weight !== ttfmake_get_default( 'font-weight-body-link' ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array( 'a' ),
			'declarations' => array(
				'font-weight' => $link_font_weight,
			)
		) );
	}
	// Comments
	if ( isset( $declarations['font-size-px'] ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array( '#comments' ),
			'declarations' => array(
				'font-size-px'  => ttfmake_get_relative_font_size( $declarations['font-size-px'], $percent[ 'comments' ] ) . 'px',
				'font-size-rem' => ttfmake_convert_px_to_rem( ttfmake_get_relative_font_size( $declarations['font-size-px'], $percent[ 'comments' ] ) ) . 'rem'
			)
		) );
		// Comment date
		ttfmake_get_css()->add( array(
			'selectors'    => array( '.comment-date' ),
			'declarations' => array(
				'font-size-px'  => ttfmake_get_relative_font_size( $declarations['font-size-px'], $percent[ 'comment-date' ] ) . 'px',
				'font-size-rem' => ttfmake_convert_px_to_rem( ttfmake_get_relative_font_size( $declarations['font-size-px'], $percent[ 'comment-date' ] ) ) . 'rem'
			)
		) );
	}

	/**
	 * H1
	 */
	$element = 'h1';
	$selectors = array( 'h1', 'h1 a', '.font-header' );
	$declarations = ttfmake_parse_font_properties( $element );
	if ( ! empty( $declarations ) ) {
		ttfmake_get_css()->add( array( 'selectors' => $selectors, 'declarations' => $declarations, ) );
	}
	$link_rule = ttfmake_parse_link_underline( $element, array( 'h1 a' ) );
	if ( ! empty( $link_rule ) ) {
		ttfmake_get_css()->add( $link_rule );
	}

	/**
	 * H2
	 */
	$element = 'h2';
	$selectors = array( 'h2', 'h2 a' );
	$declarations = ttfmake_parse_font_properties( $element );
	if ( ! empty( $declarations ) ) {
		ttfmake_get_css()->add( array( 'selectors' => $selectors, 'declarations' => $declarations, ) );
	}
	$link_rule = ttfmake_parse_link_underline( $element, array( 'h2 a' ) );
	if ( ! empty( $link_rule ) ) {
		ttfmake_get_css()->add( $link_rule );
	}
	// Post title with two sidebars
	if ( isset( $declarations['font-size-px'] ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array( '.has-left-sidebar.has-right-sidebar .entry-title' ),
			'declarations' => array(
				'font-size-px'  => ttfmake_get_relative_font_size( $declarations['font-size-px'], $percent[ 'post-title' ] ) . 'px',
				'font-size-rem' => ttfmake_convert_px_to_rem( ttfmake_get_relative_font_size( $declarations['font-size-px'], $percent[ 'post-title' ] ) ) . 'rem'
			),
			'media'        => 'screen and (min-width: 800px)'
		) );
	}

	/**
	 * H3
	 */
	$element = 'h3';
	$selectors = array( 'h3', 'h3 a', '.builder-text-content .widget-title' );
	$declarations = ttfmake_parse_font_properties( $element );
	if ( ! empty( $declarations ) ) {
		ttfmake_get_css()->add( array( 'selectors' => $selectors, 'declarations' => $declarations, ) );
	}
	$link_rule = ttfmake_parse_link_underline( $element, array( 'h3 a' ) );
	if ( ! empty( $link_rule ) ) {
		ttfmake_get_css()->add( $link_rule );
	}

	/**
	 * H4
	 */
	$element = 'h4';
	$selectors = array( 'h4', 'h4 a' );
	$declarations = ttfmake_parse_font_properties( $element );
	if ( ! empty( $declarations ) ) {
		ttfmake_get_css()->add( array( 'selectors' => $selectors, 'declarations' => $declarations, ) );
	}
	$link_rule = ttfmake_parse_link_underline( $element, array( 'h4 a' ) );
	if ( ! empty( $link_rule ) ) {
		ttfmake_get_css()->add( $link_rule );
	}

	/**
	 * H5
	 */
	$element = 'h5';
	$selectors = array( 'h5', 'h5 a' );
	$declarations = ttfmake_parse_font_properties( $element );
	if ( ! empty( $declarations ) ) {
		ttfmake_get_css()->add( array( 'selectors' => $selectors, 'declarations' => $declarations, ) );
	}
	$link_rule = ttfmake_parse_link_underline( $element, array( 'h5 a' ) );
	if ( ! empty( $link_rule ) ) {
		ttfmake_get_css()->add( $link_rule );
	}

	/**
	 * H6
	 */
	$element = 'h6';
	$selectors = array( 'h6', 'h6 a' );
	$declarations = ttfmake_parse_font_properties( $element );
	if ( ! empty( $declarations ) ) {
		ttfmake_get_css()->add( array( 'selectors' => $selectors, 'declarations' => $declarations, ) );
	}
	$link_rule = ttfmake_parse_link_underline( $element, array( 'h6 a' ) );
	if ( ! empty( $link_rule ) ) {
		ttfmake_get_css()->add( $link_rule );
	}

	/**
	 * Site Title
	 */
	$element = 'site-title';
	$selectors = array( '.site-title', '.site-title a', '.font-site-title' );
	$declarations = ttfmake_parse_font_properties( $element );
	if ( ! empty( $declarations ) ) {
		ttfmake_get_css()->add( array( 'selectors' => $selectors, 'declarations' => $declarations, ) );
	}
	$link_rule = ttfmake_parse_link_underline( $element, array( '.site-title a' ) );
	if ( ! empty( $link_rule ) ) {
		ttfmake_get_css()->add( $link_rule );
	}

	/**
	 * Site Tagline
	 */
	$element = 'site-tagline';
	$selectors = array( '.site-description', '.site-description a', '.font-site-tagline' );
	$declarations = ttfmake_parse_font_properties( $element );
	if ( ! empty( $declarations ) ) {
		ttfmake_get_css()->add( array( 'selectors' => $selectors, 'declarations' => $declarations, ) );
	}
	$link_rule = ttfmake_parse_link_underline( $element, array( '.site-description a' ) );
	if ( ! empty( $link_rule ) ) {
		ttfmake_get_css()->add( $link_rule );
	}

	/**
	 * Menu Item
	 */
	$menu_items_customized = false;
	$element = 'nav';
	$selectors = array( '.site-navigation .menu li a', '.font-nav' );
	$declarations = ttfmake_parse_font_properties( $element );
	if ( ! empty( $declarations ) ) {
		ttfmake_get_css()->add( array( 'selectors' => $selectors, 'declarations' => $declarations, ) );
		$menu_items_customized = true;
	}
	$link_rule = ttfmake_parse_link_underline( $element, array( '.site-navigation .menu li a' ) );
	if ( ! empty( $link_rule ) ) {
		ttfmake_get_css()->add( $link_rule );
	}
	// Arrow size and grandchild arrow size and position
	if ( isset( $declarations['font-size-px'] ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.site-navigation .menu .page_item_has_children a:after',
				'.site-navigation .menu .menu-item-has-children a:after'
			),
			'declarations' => array(
				'top' => '-' . ttfmake_get_relative_font_size( $declarations['font-size-px'], 10 ) . 'px',
				'font-size-px' => ttfmake_get_relative_font_size( $declarations['font-size-px'], 72 ) . 'px',
				'font-size-rem' => ttfmake_convert_px_to_rem( ttfmake_get_relative_font_size( $declarations['font-size-px'], 72 ) ) . 'rem'
			),
			'media'        => 'screen and (min-width: 800px)'
		) );
	}

	/**
	 * Sub-Menu Item
	 */
	$submenu_items_customized = false;
	$element = 'subnav';
	$selectors = array( '.site-navigation .menu .sub-menu li a', '.site-navigation .menu .children li a' );
	$declarations = ttfmake_parse_font_properties( $element, $menu_items_customized );
	$simplify_mobile = (bool) get_theme_mod( 'font-' . $element . '-mobile', ttfmake_get_default( 'font-' . $element . '-mobile' ) );
	$media = 'all';
	if ( true === $simplify_mobile ) {
		$media = 'screen and (min-width: 800px)';
	}
	if ( ! empty( $declarations ) ) {
		ttfmake_get_css()->add( array( 'selectors' => $selectors, 'declarations' => $declarations, 'media' => $media ) );
		$submenu_items_customized = true;
	}
	$link_rule = ttfmake_parse_link_underline( $element, array( '.site-navigation .menu .sub-menu li a', '.site-navigation .menu .children li a' ) );
	if ( ! empty( $link_rule ) ) {
		$link_rule['media'] = $media;
		ttfmake_get_css()->add( $link_rule );
	}
	// Grandchild arrow size
	if ( isset( $declarations['font-size-px'] ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.site-navigation .menu .children .page_item_has_children a:after',
				'.site-navigation .menu .sub-menu .menu-item-has-children a:after'
			),
			'declarations' => array(
				//'top' => ( $declarations['font-size-px'] * 0.7 ) - 5 . 'px',
				'font-size-px' => ttfmake_get_relative_font_size( $declarations['font-size-px'], 72 ) . 'px',
				'font-size-rem' => ttfmake_convert_px_to_rem( ttfmake_get_relative_font_size( $declarations['font-size-px'], 72 ) ) . 'rem'
			),
			'media'        => 'screen and (min-width: 800px)'
		) );
	}

	/**
	 * Current Item
	 */
	$current_item_weight = ttfmake_sanitize_choice( get_theme_mod( 'font-weight-nav-current-item', ttfmake_get_default( 'font-weight-nav-current-item' ) ), 'font-weight-nav-current-item' );
	if ( $current_item_weight !== ttfmake_get_default( 'font-weight-nav-current-item' ) || true === $menu_items_customized || true === $submenu_items_customized ) {
		ttfmake_get_css()->add( array(
			'selectors' => array(
				'.site-navigation .menu li.current_page_item > a',
				'.site-navigation .menu .children li.current_page_item > a',
				'.site-navigation .menu li.current-menu-item > a',
				'.site-navigation .menu .sub-menu li.current-menu-item > a',
			),
			'declarations' => array(
				'font-weight' => $current_item_weight
			),
		) );
		ttfmake_get_css()->add( array(
			'selectors' => array(
				'.site-navigation .menu li.current_page_item > a',
				'.site-navigation .menu .children li.current_page_item > a',
				'.site-navigation .menu li.current_page_ancestor > a',
				'.site-navigation .menu li.current-menu-item > a',
				'.site-navigation .menu .sub-menu li.current-menu-item > a',
				'.site-navigation .menu li.current-menu-ancestor > a',
			),
			'declarations' => array(
				'font-weight' => $current_item_weight
			),
			'media' => 'screen and (min-width: 800px)',
		) );
	}

	/**
	 * Header Bar Text
	 */
	$element = 'header-bar-text';
	$selectors = array( '.header-bar', '.header-text', '.header-bar .search-form input', '.header-bar .menu a' );
	$declarations = ttfmake_parse_font_properties( $element );
	if ( ! empty( $declarations ) ) {
		ttfmake_get_css()->add( array( 'selectors' => $selectors, 'declarations' => $declarations, ) );
	}
	$link_rule = ttfmake_parse_link_underline( $element, array( '.header-bar a', '.header-text a', '.header-bar .menu a' ) );
	if ( ! empty( $link_rule ) ) {
		ttfmake_get_css()->add( $link_rule );
	}
	// Header Bar Icons
	$header_icon_size = absint( get_theme_mod( 'font-size-header-bar-icon', ttfmake_get_default( 'font-size-header-bar-icon' ) ) );
	if ( $header_icon_size !== ttfmake_get_default( 'font-size-header-bar-icon' ) ) {
		ttfmake_get_css()->add( array(
			'selectors' => array( '.header-social-links li a' ),
			'declarations' => array(
				'font-size-px' => $header_icon_size . 'px',
				'font-size-rem' => ttfmake_convert_px_to_rem( $header_icon_size ) . 'rem'
			),
		) );
		ttfmake_get_css()->add( array(
			'selectors' => array( '.header-social-links li a' ),
			'declarations' => array(
				'font-size-px' => ttfmake_get_relative_font_size( $header_icon_size, $percent[ 'header-bar-icon' ] ) . 'px',
				'font-size-rem' => ttfmake_convert_px_to_rem( ttfmake_get_relative_font_size( $header_icon_size, $percent[ 'header-bar-icon' ] ) ) . 'rem'
			),
			'media' => 'screen and (min-width: 1100px)'
		) );
	}

	/**
	 * Sidebar Widget Title
	 */
	$element = 'widget-title';
	$selectors = array( '.sidebar .widget-title', '.sidebar .widgettitle', '.sidebar .widget-title a', '.sidebar .widgettitle a', '.font-widget-title' );
	$declarations = ttfmake_parse_font_properties( $element );
	if ( ! empty( $declarations ) ) {
		ttfmake_get_css()->add( array( 'selectors' => $selectors, 'declarations' => $declarations, ) );
	}
	$link_rule = ttfmake_parse_link_underline( $element, array( '.sidebar .widget-title a', '.sidebar .widgettitle a' ) );
	if ( ! empty( $link_rule ) ) {
		ttfmake_get_css()->add( $link_rule );
	}

	/**
	 * Sidebar Widget Body
	 */
	$element = 'widget';
	$selectors = array( '.sidebar .widget', '.font-widget' );
	$declarations = ttfmake_parse_font_properties( $element );
	if ( ! empty( $declarations ) ) {
		ttfmake_get_css()->add( array( 'selectors' => $selectors, 'declarations' => $declarations, ) );
	}
	$link_rule = ttfmake_parse_link_underline( $element, array( '.sidebar .widget a' ) );
	if ( ! empty( $link_rule ) ) {
		ttfmake_get_css()->add( $link_rule );
	}

	/**
	 * Footer Widget Title
	 */
	$element = 'footer-widget-title';
	$selectors = array( '.footer-widget-container .widget-title', '.footer-widget-container .widgettitle', '.footer-widget-container .widget-title a', '.footer-widget-container .widgettitle a' );
	$declarations = ttfmake_parse_font_properties( $element );
	if ( ! empty( $declarations ) ) {
		ttfmake_get_css()->add( array( 'selectors' => $selectors, 'declarations' => $declarations, ) );
	}
	$link_rule = ttfmake_parse_link_underline( $element, array( '.footer-widget-container .widget-title a', '.footer-widget-container .widgettitle a' ) );
	if ( ! empty( $link_rule ) ) {
		ttfmake_get_css()->add( $link_rule );
	}

	/**
	 * Footer Widget Body
	 */
	$element = 'footer-widget';
	$selectors = array( '.footer-widget-container .widget' );
	$declarations = ttfmake_parse_font_properties( $element );
	if ( ! empty( $declarations ) ) {
		ttfmake_get_css()->add( array( 'selectors' => $selectors, 'declarations' => $declarations, ) );
	}
	$link_rule = ttfmake_parse_link_underline( $element, array( '.footer-widget-container .widget a' ) );
	if ( ! empty( $link_rule ) ) {
		ttfmake_get_css()->add( $link_rule );
	}

	/**
	 * Footer Text
	 */
	$element = 'footer-text';
	$selectors = array( '.footer-text' );
	$declarations = ttfmake_parse_font_properties( $element );
	if ( ! empty( $declarations ) ) {
		ttfmake_get_css()->add( array( 'selectors' => $selectors, 'declarations' => $declarations, ) );
	}
	$link_rule = ttfmake_parse_link_underline( $element, array( '.footer-text a' ) );
	if ( ! empty( $link_rule ) ) {
		ttfmake_get_css()->add( $link_rule );
	}
	// Footer Icons
	$footer_icon_size = absint( get_theme_mod( 'font-size-footer-icon', ttfmake_get_default( 'font-size-footer-icon' ) ) );
	if ( $footer_icon_size !== ttfmake_get_default( 'font-size-footer-icon' ) ) {
		ttfmake_get_css()->add( array(
			'selectors' => array( '.footer-social-links' ),
			'declarations' => array(
				'font-size-px' => $footer_icon_size . 'px',
				'font-size-rem' => ttfmake_convert_px_to_rem( $footer_icon_size ) . 'rem'
			),
		) );
		ttfmake_get_css()->add( array(
			'selectors' => array( '.footer-social-links' ),
			'declarations' => array(
				'font-size-px' => ttfmake_get_relative_font_size( $footer_icon_size, $percent[ 'footer-icon' ] ) . 'px',
				'font-size-rem' => ttfmake_convert_px_to_rem( ttfmake_get_relative_font_size( $footer_icon_size, $percent[ 'footer-icon' ] ) ) . 'rem'
			),
			'media' => 'screen and (min-width: 1100px)'
		) );
	}
}
endif;

add_action( 'make_css', 'ttfmake_css_fonts' );

if ( ! function_exists( 'ttfmake_get_font_stack' ) ) :
/**
 * Validate the font choice and get a font stack for it.
 *
 * @since  1.0.0.
 *
 * @param  string    $font    The 1st font in the stack.
 * @return string             The full font stack.
 */
function ttfmake_get_font_stack( $font ) {
	$all_fonts = ttfmake_get_all_fonts();

	// Sanitize font choice
	$font = ttfmake_sanitize_font_choice( $font );

	// Standard font
	if ( isset( $all_fonts[ $font ]['stack'] ) && ! empty( $all_fonts[ $font ]['stack'] ) ) {
		$stack = $all_fonts[ $font ]['stack'];
	} elseif ( in_array( $font, ttfmake_all_font_choices() ) ) {
		$stack = '"' . $font . '","Helvetica Neue",Helvetica,Arial,sans-serif';
	} else {
		$stack = '"Helvetica Neue",Helvetica,Arial,sans-serif';
	}

	/**
	 * Allow developers to filter the full font stack.
	 *
	 * @since 1.2.3.
	 *
	 * @param string    $stack    The font stack.
	 * @param string    $font     The font.
	 */
	return apply_filters( 'make_font_stack', $stack, $font );
}
endif;

if ( ! function_exists( 'ttfmake_font_get_relative_sizes' ) ) :
/**
 * Return an array of percentages to use when calculating certain font sizes.
 *
 * @since  1.3.0.
 *
 * @return array    The percentage value relative to another specific size
 */
function ttfmake_font_get_relative_sizes() {
	/**
	 * Filter the array of relative font sizes.
	 *
	 * Each array item defines a percentage by which to scale a font size compared
	 * to some other font size. Most of these were deprecated in version 1.3.0.
	 *
	 * @since 1.0.0.
	 *
	 * @param array    $sizes    The array of relative sizes.
	 */
	return apply_filters( 'make_font_relative_size', array(
		// Relative to navigation font size
		'sub-menu'        => 93,  // Deprecated in 1.3.0.
		// Relative to Header Bar icon size
		'header-bar-icon' => 85,
		'footer-icon'     => 85,
		// Relative to widget font size
		'widget-title'    => 100, // Deprecated in 1.5.0.
		// Relative to header font size
		'h1'              => 100, // Deprecated in 1.3.0.
		'h2'              => 74,  // Deprecated in 1.3.0.
		'h3'              => 52,  // Deprecated in 1.3.0.
		'h4'              => 52,  // Deprecated in 1.3.0.
		'h5'              => 35,  // Deprecated in 1.3.0.
		'h6'              => 30,  // Deprecated in 1.3.0.
		'post-title'      => 74,
		// Relative to body font size
		'comments'        => 88,
		'comment-date'    => 82,
	) );
}
endif;

if ( ! function_exists( 'ttfmake_parse_font_properties' ) ) :
/**
 * Cycle through the font options for the given element and collect an array
 * of option values that are non-default.
 *
 * @since  1.3.0.
 *
 * @param  string    $element    The element to parse the options for.
 * @param  bool      $force      True to include properties that have default values.
 * @return array                 An array of non-default CSS declarations.
 */
function ttfmake_parse_font_properties( $element, $force = false ) {
	/**
	 * Filter the array of customizable font properties and their sanitization callbacks.
	 *
	 * css_property => sanitize_callback
	 *
	 * @since 1.3.0.
	 *
	 * @param array    $properties    The array of font properties and callbacks.
	 */
	$properties = apply_filters( 'make_css_font_properties', array(
		'font-family'	 => 'ttfmake_sanitize_font_choice',
		'font-size'		 => 'absint',
		'font-weight'    => 'ttfmake_sanitize_choice',
		'font-style'     => 'ttfmake_sanitize_choice',
		'text-transform' => 'ttfmake_sanitize_choice',
		'line-height'    => 'ttfmake_sanitize_float',
		'letter-spacing' => 'ttfmake_sanitize_float',
		'word-spacing'   => 'absint',
	), $element );

	$declarations = array();
	foreach ( $properties as $property => $callback ) {
		$setting_id = $property . '-' . $element;
		$value = get_theme_mod( $setting_id, ttfmake_get_default( $setting_id ) );
		$sanitized_value = call_user_func_array( $callback, array( $value, $setting_id ) );
		if ( true === $force || ( false !== $value && $value !== ttfmake_get_default( $setting_id ) ) ) {
			if ( 'font-family' === $property ) {
				$declarations[ $property ] = ttfmake_get_font_stack( $sanitized_value );
			} else if ( 'font-size' === $property ) {
				$declarations[ $property . '-px' ] = $sanitized_value . 'px';
				$declarations[ $property . '-rem' ] = ttfmake_convert_px_to_rem( $sanitized_value ) . 'rem';
			} else if ( in_array( $property, array( 'letter-spacing', 'word-spacing' ) ) ) {
				$declarations[ $property ] = $sanitized_value . 'px';
			} else {
				$declarations[ $property ] = $sanitized_value;
			}
		}
	}

	return $declarations;
}
endif;

/**
 * Generate a CSS rule definition array for an element's link underline property.
 *
 * @since 1.5.0.
 *
 * @param  string    $element      The element to look up in the theme options.
 * @param  array     $selectors    The base selectors to use for the rule.
 * @return array                   A CSS rule definition array.
 */
function ttfmake_parse_link_underline( $element, $selectors ) {
	$setting_id = 'link-underline-' . $element;
	$value = get_theme_mod( $setting_id, ttfmake_get_default( $setting_id ) );
	if ( false !== $value && $value !== ttfmake_get_default( $setting_id ) ) {
		$sanitized_value = ttfmake_sanitize_choice( $value, $setting_id );

		// Declarations
		$declarations = array( 'text-decoration' => 'underline' );
		if ( 'never' === $sanitized_value ) {
			$declarations['text-decoration'] = 'none';
		}

		// Selectors
		$parsed_selectors = $selectors;
		if ( 'hover' === $sanitized_value ) {
			foreach ( $selectors as $key => $selector ) {
				$parsed_selectors[ $key ] = $selector . ':hover';
				$parsed_selectors[] = $selector . ':focus';
			}
		}

		// Return CSS rule array
		return array(
			'selectors' => $parsed_selectors,
			'declarations' => $declarations,
		);
	}

	return array();
}

if ( ! function_exists( 'ttfmake_get_relative_font_size' ) ) :
/**
 * Convert a font size to a relative size based on a starting value and percentage.
 *
 * @since  1.0.0.
 *
 * @param  mixed    $value         The value to base the final value on.
 * @param  mixed    $percentage    The percentage of change.
 * @return float                   The converted value.
 */
function ttfmake_get_relative_font_size( $value, $percentage ) {
	return round( (float) $value * ( $percentage / 100 ) );
}
endif;

if ( ! function_exists( 'ttfmake_convert_px_to_rem' ) ) :
/**
 * Given a px value, return a rem value.
 *
 * @since  1.0.0.
 *
 * @param  mixed    $px      The value to convert.
 * @param  mixed    $base    The font-size base for the rem conversion (deprecated).
 * @return float             The converted value.
 */
function ttfmake_convert_px_to_rem( $px, $base = 0 ) {
	return (float) $px / 10;
}
endif;

if ( ! function_exists( 'ttfmake_css_legacy_fonts' ) ) :
/**
 * Build the CSS rules for the custom fonts
 *
 * @since  1.0.0.
 *
 * @return void
 */
function ttfmake_css_legacy_fonts() {
	/**
	 * Font Families
	 */
	// Get and escape options
	$font_site_title       = get_theme_mod( 'font-site-title', ttfmake_get_default( 'font-site-title' ) );
	$font_site_title_stack = ttfmake_get_font_stack( $font_site_title );
	$font_header           = get_theme_mod( 'font-header', ttfmake_get_default( 'font-header' ) );
	$font_header_stack     = ttfmake_get_font_stack( $font_header );
	$font_body             = get_theme_mod( 'font-body', ttfmake_get_default( 'font-body' ) );
	$font_body_stack       = ttfmake_get_font_stack( $font_body );

	// Site Title Font
	if ( $font_site_title !== ttfmake_get_default( 'font-site-title' ) && '' !== $font_site_title_stack ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array( '.site-title', '.font-site-title' ),
			'declarations' => array(
				'font-family' => $font_site_title_stack
			)
		) );
	}

	// Header Font
	if ( $font_header !== ttfmake_get_default( 'font-header' ) && '' !== $font_header_stack ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', '.font-header' ),
			'declarations' => array(
				'font-family' => $font_header_stack
			)
		) );
	}

	// Body Font
	if ( $font_body !== ttfmake_get_default( 'font-body' ) && '' !== $font_body_stack ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array( 'body', '.font-body' ),
			'declarations' => array(
				'font-family' => $font_body_stack
			)
		) );
	}

	/**
	 * Font Sizes
	 */
	// Get and escape options
	$font_site_title_size = absint( get_theme_mod( 'font-site-title-size', ttfmake_get_default( 'font-site-title-size' ) ) );
	$font_site_tagline_size = absint( get_theme_mod( 'font-site-tagline-size', ttfmake_get_default( 'font-site-tagline-size' ) ) );
	$font_nav_size        = absint( get_theme_mod( 'font-nav-size', ttfmake_get_default( 'font-nav-size' ) ) );
	$font_header_size     = absint( get_theme_mod( 'font-header-size', ttfmake_get_default( 'font-header-size' ) ) );
	$font_widget_size     = absint( get_theme_mod( 'font-widget-size', ttfmake_get_default( 'font-widget-size' ) ) );
	$font_body_size       = absint( get_theme_mod( 'font-body-size', ttfmake_get_default( 'font-body-size' ) ) );

	// Relative font sizes
	$percent = ttfmake_font_get_relative_sizes();

	// Site Title Font Size
	if ( $font_site_title_size !== ttfmake_get_default( 'font-site-title-size' ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array( '.site-title', '.font-site-title' ),
			'declarations' => array(
				'font-size-px'  => $font_site_title_size . 'px',
				'font-size-rem' => ttfmake_convert_px_to_rem( $font_site_title_size ) . 'rem'
			)
		) );
	}

	// Site Tagline Font Size
	if ( $font_site_tagline_size !== ttfmake_get_default( 'font-site-tagline-size' ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array( '.site-description', '.font-site-tagline' ),
			'declarations' => array(
				'font-size-px'  => $font_site_tagline_size . 'px',
				'font-size-rem' => ttfmake_convert_px_to_rem( $font_site_tagline_size ) . 'rem'
			)
		) );
	}

	// Navigation Font Size
	if ( $font_nav_size !== ttfmake_get_default( 'font-nav-size' ) ) {
		// Top level
		ttfmake_get_css()->add( array(
			'selectors'    => array( '.site-navigation .menu li a', '.font-nav' ),
			'declarations' => array(
				'font-size-px'  => $font_nav_size . 'px',
				'font-size-rem' => ttfmake_convert_px_to_rem( $font_nav_size ) . 'rem'
			)
		) );

		// Sub menu items
		ttfmake_get_css()->add( array(
			'selectors'    => array( '.site-navigation .menu .sub-menu li a', '.site-navigation .menu .children li a' ),
			'declarations' => array(
				'font-size-px'  => ttfmake_get_relative_font_size( $font_nav_size, $percent['sub-menu'] ) . 'px',
				'font-size-rem' => ttfmake_convert_px_to_rem( ttfmake_get_relative_font_size( $font_nav_size, $percent['sub-menu'] ) ) . 'rem'
			),
			'media'        => 'screen and (min-width: 800px)'
		) );

		// Grandchild arrow position
		ttfmake_get_css()->add( array(
			'selectors'    => array( '.site-navigation .menu .sub-menu .menu-item-has-children a:after', '.site-navigation .menu .children .menu-item-has-children a:after' ),
			'declarations' => array(
				'top' => ( $font_nav_size * 1.4 / 2 ) - 5 . 'px'
			),
			'media'        => 'screen and (min-width: 800px)'
		) );
	}

	// Header Font Sizes
	if ( $font_header_size !== ttfmake_get_default( 'font-header-size' ) ) {
		// h1
		ttfmake_get_css()->add( array(
			'selectors'    => array( 'h1', '.font-header' ),
			'declarations' => array(
				'font-size-px'  => $font_header_size . 'px',
				'font-size-rem' => ttfmake_convert_px_to_rem( $font_header_size ) . 'rem'
			)
		) );

		// h2
		ttfmake_get_css()->add( array(
			'selectors'    => array( 'h2' ),
			'declarations' => array(
				'font-size-px'  => ttfmake_get_relative_font_size( $font_header_size, $percent[ 'h2' ] ) . 'px',
				'font-size-rem' => ttfmake_convert_px_to_rem( ttfmake_get_relative_font_size( $font_header_size, $percent[ 'h2' ] ) ) . 'rem'
			)
		) );

		// h3
		ttfmake_get_css()->add( array(
			'selectors'    => array( 'h3', '.builder-text-content .widget-title' ),
			'declarations' => array(
				'font-size-px'  => ttfmake_get_relative_font_size( $font_header_size, $percent[ 'h3' ] ) . 'px',
				'font-size-rem' => ttfmake_convert_px_to_rem( ttfmake_get_relative_font_size( $font_header_size, $percent[ 'h3' ] ) ) . 'rem'
			)
		) );

		// h4
		ttfmake_get_css()->add( array(
			'selectors'    => array( 'h4' ),
			'declarations' => array(
				'font-size-px'  => ttfmake_get_relative_font_size( $font_header_size, $percent[ 'h4' ] ) . 'px',
				'font-size-rem' => ttfmake_convert_px_to_rem( ttfmake_get_relative_font_size( $font_header_size, $percent[ 'h4' ] ) ) . 'rem'
			)
		) );

		// h5
		ttfmake_get_css()->add( array(
			'selectors'    => array( 'h5' ),
			'declarations' => array(
				'font-size-px'  => ttfmake_get_relative_font_size( $font_header_size, $percent[ 'h5' ] ) . 'px',
				'font-size-rem' => ttfmake_convert_px_to_rem( ttfmake_get_relative_font_size( $font_header_size, $percent[ 'h5' ] ) ) . 'rem'
			)
		) );

		// h6
		ttfmake_get_css()->add( array(
			'selectors'    => array( 'h6' ),
			'declarations' => array(
				'font-size-px'  => ttfmake_get_relative_font_size( $font_header_size, $percent[ 'h6' ] ) . 'px',
				'font-size-rem' => ttfmake_convert_px_to_rem( ttfmake_get_relative_font_size( $font_header_size, $percent[ 'h6' ] ) ) . 'rem'
			)
		) );

		// Post title with two sidebars
		ttfmake_get_css()->add( array(
			'selectors'    => array( '.has-left-sidebar.has-right-sidebar .entry-title' ),
			'declarations' => array(
				'font-size-px'  => ttfmake_get_relative_font_size( $font_header_size, $percent[ 'post-title' ] ) . 'px',
				'font-size-rem' => ttfmake_convert_px_to_rem( ttfmake_get_relative_font_size( $font_header_size, $percent[ 'post-title' ] ) ) . 'rem'
			),
			'media'        => 'screen and (min-width: 800px)'
		) );
	}

	// Widget Font Size
	if ( $font_widget_size !== ttfmake_get_default( 'font-widget-size' ) ) {
		// Widget body
		ttfmake_get_css()->add( array(
			'selectors'    => array( '.widget', '.font-widget' ),
			'declarations' => array(
				'font-size-px'  => $font_widget_size . 'px',
				'font-size-rem' => ttfmake_convert_px_to_rem( $font_widget_size ) . 'rem'
			)
		) );

		// Widget title
		ttfmake_get_css()->add( array(
			'selectors'    => array( '.widget-title' ),
			'declarations' => array(
				'font-size-px'  => ttfmake_get_relative_font_size( $font_widget_size, $percent[ 'widget-title' ] ) . 'px',
				'font-size-rem' => ttfmake_convert_px_to_rem( ttfmake_get_relative_font_size( $font_widget_size, $percent[ 'widget-title' ] ) ) . 'rem'
			)
		) );
	}

	// Body Font Size
	if ( $font_body_size !== ttfmake_get_default( 'font-body-size' ) ) {
		// body
		ttfmake_get_css()->add( array(
			'selectors'    => array( 'body', '.font-body', '.builder-text-content .widget' ),
			'declarations' => array(
				'font-size-px'  => $font_body_size . 'px',
				'font-size-rem' => ttfmake_convert_px_to_rem( $font_body_size ) . 'rem'
			)
		) );

		// Comments
		ttfmake_get_css()->add( array(
			'selectors'    => array( '#comments' ),
			'declarations' => array(
				'font-size-px'  => ttfmake_get_relative_font_size( $font_body_size, $percent[ 'comments' ] ) . 'px',
				'font-size-rem' => ttfmake_convert_px_to_rem( ttfmake_get_relative_font_size( $font_body_size, $percent[ 'comments' ] ) ) . 'rem'
			)
		) );

		// Comment date
		ttfmake_get_css()->add( array(
			'selectors'    => array( '.comment-date' ),
			'declarations' => array(
				'font-size-px'  => ttfmake_get_relative_font_size( $font_body_size, $percent[ 'comment-date' ] ) . 'px',
				'font-size-rem' => ttfmake_convert_px_to_rem( ttfmake_get_relative_font_size( $font_body_size, $percent[ 'comment-date' ] ) ) . 'rem'
			)
		) );
	}
}
endif;