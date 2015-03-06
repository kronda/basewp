<?php
/**
 * @package Make
 */

/**
 * Build the CSS rules for the color scheme.
 *
 * @since 1.5.0.
 *
 * @return void
 */
function ttfmake_css_color() {
	/**
	 * Global
	 */
	// Primary color
	$color_primary = maybe_hash_hex_color( get_theme_mod( 'color-primary', ttfmake_get_default( 'color-primary' ) ) );
	if ( $color_primary !== ttfmake_get_default( 'color-primary' ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.color-primary-text',
				'a',
				'.entry-author-byline a.vcard',
				'.entry-footer a:hover',
				'.comment-form .required',
				'ul.ttfmake-list-dot li:before',
				'ol.ttfmake-list-dot li:before',
				'.entry-comment-count a:hover',
				'.comment-count-icon a:hover',
			),
			'declarations' => array(
				'color' => $color_primary
			)
		) );
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.color-primary-background',
				'.ttfmake-button.color-primary-background',
			),
			'declarations' => array(
				'background-color' => $color_primary
			)
		) );
		ttfmake_get_css()->add( array(
			'selectors'    => array( '.color-primary-border' ),
			'declarations' => array(
				'border-color' => $color_primary
			)
		) );
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.site-navigation ul.menu ul a:hover',
				'.site-navigation ul.menu ul a:focus',
				'.site-navigation .menu ul ul a:hover',
				'.site-navigation .menu ul ul a:focus',
			),
			'declarations' => array(
				'background-color' => $color_primary
			),
			'media'        => 'screen and (min-width: 800px)'
		) );
	}

	// Secondary color
	$color_secondary = maybe_hash_hex_color( get_theme_mod( 'color-secondary', ttfmake_get_default( 'color-secondary' ) ) );
	if ( $color_secondary !== ttfmake_get_default( 'color-secondary' ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.color-secondary-text',
				'.builder-section-banner .cycle-pager',
				'.ttfmake-shortcode-slider .cycle-pager',
				'.builder-section-banner .cycle-prev:before',
				'.builder-section-banner .cycle-next:before',
				'.ttfmake-shortcode-slider .cycle-prev:before',
				'.ttfmake-shortcode-slider .cycle-next:before',
				'.ttfmake-shortcode-slider .cycle-caption',
			),
			'declarations' => array(
				'color' => $color_secondary
			)
		) );
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.color-secondary-background',
				'blockquote.ttfmake-testimonial',
				'tt',
				'kbd',
				'pre',
				'code',
				'samp',
				'var',
				'textarea',
				'input[type="date"]',
				'input[type="datetime"]',
				'input[type="datetime-local"]',
				'input[type="email"]',
				'input[type="month"]',
				'input[type="number"]',
				'input[type="password"]',
				'input[type="search"]',
				'input[type="tel"]',
				'input[type="text"]',
				'input[type="time"]',
				'input[type="url"]',
				'input[type="week"]',
				'.ttfmake-button.color-secondary-background',
				'button.color-secondary-background',
				'input[type="button"].color-secondary-background',
				'input[type="reset"].color-secondary-background',
				'input[type="submit"].color-secondary-background',
				'.sticky-post-label',
				'.widget_tag_cloud a',
			),
			'declarations' => array(
				'background-color' => $color_secondary
			)
		) );
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.site-navigation .menu .sub-menu',
				'.site-navigation .menu .children',
			),
			'declarations' => array(
				'background-color' => $color_secondary
			),
			'media'        => 'screen and (min-width: 800px)'
		) );
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.color-secondary-border',
				'table',
				'table th',
				'table td',
				'.header-layout-3 .site-navigation .menu',
			),
			'declarations' => array(
				'border-color' => $color_secondary
			)
		) );
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'hr',
				'hr.ttfmake-line-dashed',
				'hr.ttfmake-line-double',
				'blockquote.ttfmake-testimonial:after',
			),
			'declarations' => array(
				'border-top-color' => $color_secondary
			)
		) );
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.comment-body',
				'.post',
				'.widget li',
			),
			'declarations' => array(
				'border-bottom-color' => $color_secondary
			)
		) );
	}

	// Text color
	$color_text = maybe_hash_hex_color( get_theme_mod( 'color-text', ttfmake_get_default( 'color-text' ) ) );
	if ( $color_text !== ttfmake_get_default( 'color-text' ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.color-text',
				'body',
				'.entry-date a',
				'button',
				'input',
				'select',
				'textarea',
				'[class*="navigation"] .nav-previous a',
				'[class*="navigation"] .nav-previous span',
				'[class*="navigation"] .nav-next a',
				'[class*="navigation"] .nav-next span',
			),
			'declarations' => array(
				'color' => $color_text
			)
		) );
		// These placeholder selectors have to be isolated in individual rules.
		// See http://css-tricks.com/snippets/css/style-placeholder-text/#comment-96771
		ttfmake_get_css()->add( array(
			'selectors'    => array( '::-webkit-input-placeholder' ),
			'declarations' => array(
				'color' => $color_text
			)
		) );
		ttfmake_get_css()->add( array(
			'selectors'    => array( ':-moz-placeholder' ),
			'declarations' => array(
				'color' => $color_text
			)
		) );
		ttfmake_get_css()->add( array(
			'selectors'    => array( '::-moz-placeholder' ),
			'declarations' => array(
				'color' => $color_text
			)
		) );
		ttfmake_get_css()->add( array(
			'selectors'    => array( ':-ms-input-placeholder' ),
			'declarations' => array(
				'color' => $color_text
			)
		) );
	}

	// Detail color
	$color_detail = maybe_hash_hex_color( get_theme_mod( 'color-detail', ttfmake_get_default( 'color-detail' ) ) );
	if ( $color_detail !== ttfmake_get_default( 'color-detail' ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.color-detail-text',
				'.builder-section-banner .cycle-pager .cycle-pager-active',
				'.ttfmake-shortcode-slider .cycle-pager .cycle-pager-active',
				'.post-categories li:after',
				'.post-tags li:after',
				'.comment-count-icon:before',
				'.entry-comment-count a',
				'.comment-count-icon a',
			),
			'declarations' => array(
				'color' => $color_detail
			)
		) );
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.site-navigation .page_item_has_children a:after',
				'.site-navigation .menu-item-has-children a:after'
			),
			'declarations' => array(
				'color' => $color_detail
			),
			'media'        => 'screen and (min-width: 800px)'
		) );
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.site-navigation .menu .sub-menu a',
				'.site-navigation .menu .children a',
			),
			'declarations' => array(
				'border-bottom-color' => $color_detail
			),
			'media'        => 'screen and (min-width: 800px)'
		) );
		ttfmake_get_css()->add( array(
			'selectors'    => array( '.color-detail-background' ),
			'declarations' => array(
				'background-color' => $color_detail
			)
		) );
		ttfmake_get_css()->add( array(
			'selectors'    => array( '.color-detail-border' ),
			'declarations' => array(
				'border-color' => $color_detail
			)
		) );
	}

	// Link Hover/Focus Color
	$color_primary_link = maybe_hash_hex_color( get_theme_mod( 'color-primary-link', ttfmake_get_default( 'color-primary-link' ) ) );
	if ( $color_primary_link && $color_primary_link !== ttfmake_get_default( 'color-primary-link' ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'a:hover',
				'a:focus',
				'.entry-author-byline a.vcard:hover',
				'.entry-author-byline a.vcard:focus',
			),
			'declarations' => array(
				'color' => $color_primary_link
			)
		) );
	}

	// Main background color
	$main_background_color = maybe_hash_hex_color( get_theme_mod( 'main-background-color', ttfmake_get_default( 'main-background-color' ) ) );
	$main_background_color_opacity = ttfmake_sanitize_float( get_theme_mod( 'main-background-color-opacity', ttfmake_get_default( 'main-background-color-opacity' ) ) );
	if ( $main_background_color !== ttfmake_get_default( 'main-background-color' ) || $main_background_color_opacity !== (float) ttfmake_get_default( 'main-background-color-opacity' ) ) {
		// Convert to RGBa
		$color_value = ttfmake_hex_to_rgb( $main_background_color ) . ', ' . $main_background_color_opacity;

		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.site-content',
				'body.mce-content-body',
			),
			'declarations' => array(
				'background-color' => 'rgba(' . $color_value . ')'
			)
		) );
	}

	/**
	 * Header
	 */
	// Header text color
	$header_text_color = maybe_hash_hex_color( get_theme_mod( 'header-text-color', ttfmake_get_default( 'header-text-color' ) ) );
	if ( $header_text_color !== ttfmake_get_default( 'header-text-color' ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.site-header',
				'.site-title',
				'.site-title a',
				'.site-navigation .menu li a',
			),
			'declarations' => array(
				'color' => $header_text_color
			)
		) );
	}

	// Header background color
	$header_background_color = maybe_hash_hex_color( get_theme_mod( 'header-background-color', ttfmake_get_default( 'header-background-color' ) ) );
	$header_background_color_opacity = ttfmake_sanitize_float( get_theme_mod( 'header-background-color-opacity', ttfmake_get_default( 'header-background-color-opacity' ) ) );
	if ( $header_background_color !== ttfmake_get_default( 'header-background-color' ) || $header_background_color_opacity !== (float) ttfmake_get_default( 'header-background-color-opacity' ) ) {
		// Convert to RGBa
		$color_value = ttfmake_hex_to_rgb( $header_background_color ) . ', ' . $header_background_color_opacity;

		ttfmake_get_css()->add( array(
			'selectors'    => array( '.site-header-main' ),
			'declarations' => array(
				'background-color' => 'rgba(' . $color_value . ')'
			)
		) );
	}

	/**
	 * Site Title & Tagline
	 */
	// Site title
	$color_site_title = maybe_hash_hex_color( get_theme_mod( 'color-site-title', ttfmake_get_default( 'color-site-title' ) ) );
	if ( $color_site_title && ( $color_site_title !== ttfmake_get_default( 'color-site-title' ) || $header_text_color !== $color_site_title ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.site-header .site-title',
				'.site-header .site-title a',
			),
			'declarations' => array(
				'color' => $color_site_title
			)
		) );
	}
	// Tagline
	$color_tagline = maybe_hash_hex_color( get_theme_mod( 'color-site-tagline', ttfmake_get_default( 'color-site-tagline' ) ) );
	if ( $color_tagline && ( $color_tagline !== ttfmake_get_default( 'color-site-tagline' ) || $header_text_color !== $color_tagline ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array( '.site-header .site-description' ),
			'declarations' => array(
				'color' => $color_tagline
			)
		) );
	}

	/**
	 * Main Menu
	 */
	// Menu Item Text
	$color_nav_text = maybe_hash_hex_color( get_theme_mod( 'color-nav-text', ttfmake_get_default( 'color-nav-text' ) ) );
	if ( $color_nav_text && $color_nav_text !== ttfmake_get_default( 'color-nav-text' ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array( '.site-navigation .menu li a' ),
			'declarations' => array(
				'color' => $color_nav_text
			)
		) );
	}

	// Menu Item Text hover
	$color_nav_text_hover = maybe_hash_hex_color( get_theme_mod( 'color-nav-text-hover', ttfmake_get_default( 'color-nav-text-hover' ) ) );
	if ( $color_nav_text_hover && $color_nav_text_hover !== ttfmake_get_default( 'color-nav-text-hover' ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.site-navigation .menu li a:hover',
				'.site-navigation .menu li a:focus',
			),
			'declarations' => array(
				'color' => $color_nav_text_hover
			)
		) );
	}

	// Sub-Menu Item Text
	$color_subnav_text = maybe_hash_hex_color( get_theme_mod( 'color-subnav-text', ttfmake_get_default( 'color-subnav-text' ) ) );
	if ( $color_subnav_text && $color_subnav_text !== ttfmake_get_default( 'color-subnav-text' ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.site-navigation ul.menu ul a',
				'.site-navigation ul.menu ul a',
				'.site-navigation .menu ul ul a',
				'.site-navigation .menu ul ul a',
			),
			'declarations' => array(
				'color' => $color_subnav_text
			),
			'media' => 'screen and (min-width: 800px)'
		) );
	}

	// Sub-Menu Item Text hover
	$color_subnav_text_hover = maybe_hash_hex_color( get_theme_mod( 'color-subnav-text-hover', ttfmake_get_default( 'color-subnav-text-hover' ) ) );
	if ( $color_subnav_text_hover && $color_subnav_text_hover !== ttfmake_get_default( 'color-subnav-text-hover' ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.site-navigation ul.menu ul a:hover',
				'.site-navigation ul.menu ul a:focus',
				'.site-navigation .menu ul ul a:hover',
				'.site-navigation .menu ul ul a:focus',
			),
			'declarations' => array(
				'color' => $color_subnav_text_hover
			),
			'media' => 'screen and (min-width: 800px)'
		) );
	}

	// Sub-Menu Item Detail
	$color_subnav_detail = maybe_hash_hex_color( get_theme_mod( 'color-subnav-detail', ttfmake_get_default( 'color-subnav-detail' ) ) );
	if ( $color_subnav_detail && $color_subnav_detail !== ttfmake_get_default( 'color-subnav-detail' ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.site-navigation .page_item_has_children a:after',
				'.site-navigation .menu-item-has-children a:after',
			),
			'declarations' => array(
				'color' => $color_subnav_detail
			),
			'media' => 'screen and (min-width: 800px)'
		) );
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.site-navigation .menu .sub-menu a',
				'.site-navigation .menu .children a',
			),
			'declarations' => array(
				'border-bottom-color' => $color_subnav_detail
			),
			'media' => 'screen and (min-width: 800px)'
		) );
	}

	// Sub-Menu Item Background
	$color_subnav_background = maybe_hash_hex_color( get_theme_mod( 'color-subnav-background', ttfmake_get_default( 'color-subnav-background' ) ) );
	$color_subnav_background_opacity = ttfmake_sanitize_float( get_theme_mod( 'color-subnav-background-opacity', ttfmake_get_default( 'color-subnav-background-opacity' ) ) );
	if ( $color_subnav_background && ( $color_subnav_background !== ttfmake_get_default( 'color-subnav-background' ) || $color_subnav_background_opacity !== ttfmake_get_default( 'color-subnav-background-opacity' ) ) ) {
		// Convert to RGBa
		$color_value = ttfmake_hex_to_rgb( $color_subnav_background ) . ', ' . $color_subnav_background_opacity;

		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.site-navigation .menu .sub-menu',
				'.site-navigation .menu .children',
			),
			'declarations' => array(
				'background-color' => 'rgba(' . $color_value . ')'
			),
			'media' => 'screen and (min-width: 800px)'
		) );
	}

	// Sub-Menu Item Background hover
	$color_subnav_background_hover = maybe_hash_hex_color( get_theme_mod( 'color-subnav-background-hover', ttfmake_get_default( 'color-subnav-background-hover' ) ) );
	$color_subnav_background_hover_opacity = ttfmake_sanitize_float( get_theme_mod( 'color-subnav-background-hover-opacity', ttfmake_get_default( 'color-subnav-background-hover-opacity' ) ) );
	if ( $color_subnav_background_hover && ( $color_subnav_background_hover !== ttfmake_get_default( 'color-subnav-background-hover' ) || $color_subnav_background_hover_opacity !== ttfmake_get_default( 'color-subnav-background-hover-opacity' ) ) ) {
		// Convert to RGBa
		$color_value = ttfmake_hex_to_rgb( $color_subnav_background_hover ) . ', ' . $color_subnav_background_hover_opacity;

		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.site-navigation ul.menu ul a:hover',
				'.site-navigation ul.menu ul a:focus',
				'.site-navigation .menu ul ul a:hover',
				'.site-navigation .menu ul ul a:focus',
			),
			'declarations' => array(
				'background-color' => 'rgba(' . $color_value . ')'
			),
			'media' => 'screen and (min-width: 800px)'
		) );
	}

	// Current Item Background
	$color_nav_current_item_background = maybe_hash_hex_color( get_theme_mod( 'color-nav-current-item-background', ttfmake_get_default( 'color-nav-current-item-background' ) ) );
	$color_nav_current_item_background_opacity = ttfmake_sanitize_float( get_theme_mod( 'color-nav-current-item-background-opacity', ttfmake_get_default( 'color-nav-current-item-background-opacity' ) ) );
	if ( $color_nav_current_item_background && ( $color_nav_current_item_background !== ttfmake_get_default( 'color-nav-current-item-background' ) || $color_nav_current_item_background_opacity !== ttfmake_get_default( 'color-nav-current-item-background-opacity' ) ) ) {
		// Convert to RGBa
		$color_value = ttfmake_hex_to_rgb( $color_nav_current_item_background ) . ', ' . $color_nav_current_item_background_opacity;

		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.site-navigation .menu li.current_page_item',
				'.site-navigation .menu .children li.current_page_item',
				'.site-navigation .menu li.current_page_ancestor',
				'.site-navigation .menu li.current-menu-item',
				'.site-navigation .menu .sub-menu li.current-menu-item',
				'.site-navigation .menu li.current-menu-ancestor',
			),
			'declarations' => array(
				'background-color' => 'rgba(' . $color_value . ')'
			),
			'media' => 'screen and (min-width: 800px)'
		) );
	}

	/**
	 * Header Bar
	 */
	// Header Bar text color
	$header_bar_text_color = maybe_hash_hex_color( get_theme_mod( 'header-bar-text-color', ttfmake_get_default( 'header-bar-text-color' ) ) );
	if ( $header_bar_text_color !== ttfmake_get_default( 'header-bar-text-color' ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.header-bar',
				'.header-bar a',
				'.header-bar .menu li a',
			),
			'declarations' => array(
				'color' => $header_bar_text_color
			)
		) );
	}

	// Header Bar link color
	$header_bar_link_color = maybe_hash_hex_color( get_theme_mod( 'header-bar-link-color', ttfmake_get_default( 'header-bar-link-color' ) ) );
	if ( $header_bar_link_color && $header_bar_link_color !== ttfmake_get_default( 'header-bar-link-color' ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.header-bar a',
				'.header-bar .menu li a',
				'.header-bar .social-links a',
			),
			'declarations' => array(
				'color' => $header_bar_link_color
			)
		) );
	}

	// Header Bar link hover color
	$header_bar_link_hover_color = maybe_hash_hex_color( get_theme_mod( 'header-bar-link-hover-color', ttfmake_get_default( 'header-bar-link-hover-color' ) ) );
	if ( $header_bar_link_hover_color && $header_bar_link_hover_color !== ttfmake_get_default( 'header-bar-link-hover-color' ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.header-bar a:hover',
				'.header-bar a:focus',
				'.header-bar .menu li a:hover',
				'.header-bar .menu li a:focus',
			),
			'declarations' => array(
				'color' => $header_bar_link_hover_color
			)
		) );
	}

	// Header Bar border color
	$header_bar_border_color = maybe_hash_hex_color( get_theme_mod( 'header-bar-border-color', ttfmake_get_default( 'header-bar-border-color' ) ) );
	if ( $header_bar_border_color !== ttfmake_get_default( 'header-bar-border-color' ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.header-bar',
				'.header-bar .search-form input',
				'.header-social-links li:first-of-type',
				'.header-social-links li a',
			),
			'declarations' => array(
				'border-color' => $header_bar_border_color
			)
		) );
	}

	// Header Bar background color
	$header_bar_background_color = maybe_hash_hex_color( get_theme_mod( 'header-bar-background-color', ttfmake_get_default( 'header-bar-background-color' ) ) );
	$header_bar_background_color_opacity = ttfmake_sanitize_float( get_theme_mod( 'header-bar-background-color-opacity', ttfmake_get_default( 'header-bar-background-color-opacity' ) ) );
	if ( $header_bar_background_color !== ttfmake_get_default( 'header-bar-background-color' ) || $header_bar_background_color_opacity !== (float) ttfmake_get_default( 'header-bar-background-color-opacity' ) ) {
		// Convert to RGBa
		$color_value = ttfmake_hex_to_rgb( $header_bar_background_color ) . ', ' . $header_bar_background_color_opacity;

		ttfmake_get_css()->add( array(
			'selectors'    => array( '.header-bar' ),
			'declarations' => array(
				'background-color' => 'rgba(' . $color_value . ')'
			)
		) );
	}

	/**
	 * Sidebars
	 */
	// Sidebar widget title
	$color_widget_title = maybe_hash_hex_color( get_theme_mod( 'color-widget-title-text', ttfmake_get_default( 'color-widget-title-text' ) ) );
	if ( $color_widget_title && $color_widget_title !== ttfmake_get_default( 'color-widget-title-text' ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.sidebar .widget-title',
				'.sidebar .widgettitle',
				'.sidebar .widget-title a',
				'.sidebar .widgettitle a',
			),
			'declarations' => array(
				'color' => $color_widget_title
			),
		) );
	}

	// Sidebar widget body
	$color_widget_body = maybe_hash_hex_color( get_theme_mod( 'color-widget-text', ttfmake_get_default( 'color-widget-text' ) ) );
	if ( $color_widget_body && $color_widget_body !== ttfmake_get_default( 'color-widget-text' ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array( '.sidebar .widget' ),
			'declarations' => array(
				'color' => $color_widget_body
			),
		) );
	}

	// Sidebar link
	$color_widget_link = maybe_hash_hex_color( get_theme_mod( 'color-widget-link', ttfmake_get_default( 'color-widget-link' ) ) );
	if ( $color_widget_link && $color_widget_link !== ttfmake_get_default( 'color-widget-link' ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array( '.sidebar a' ),
			'declarations' => array(
				'color' => $color_widget_link
			),
		) );
	}

	// Sidebar link hover
	$color_widget_link_hover = maybe_hash_hex_color( get_theme_mod( 'color-widget-link-hover', ttfmake_get_default( 'color-widget-link-hover' ) ) );
	if ( $color_widget_link_hover && $color_widget_link_hover !== ttfmake_get_default( 'color-widget-link-hover' ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.sidebar a:hover',
				'.sidebar a:focus',
			),
			'declarations' => array(
				'color' => $color_widget_link_hover
			),
		) );
	}

	// Sidebar widget border
	$color_widget_border = maybe_hash_hex_color( get_theme_mod( 'color-widget-border', ttfmake_get_default( 'color-widget-border' ) ) );
	if ( $color_widget_border && $color_widget_border !== ttfmake_get_default( 'color-widget-border' ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.sidebar table',
				'.sidebar table th',
				'.sidebar table td',
				'.sidebar .widget li',
			),
			'declarations' => array(
				'border-color' => $color_widget_border
			),
		) );
	}

	/**
	 * Footer section
	 */
	// Footer text color
	$footer_text_color = maybe_hash_hex_color( get_theme_mod( 'footer-text-color', ttfmake_get_default( 'footer-text-color' ) ) );
	if ( $footer_text_color !== ttfmake_get_default( 'footer-text-color' ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.site-footer',
				'.site-footer .social-links a',
			),
			'declarations' => array(
				'color' => $footer_text_color
			)
		) );
	}

	// Footer link color
	$footer_link_color = maybe_hash_hex_color( get_theme_mod( 'footer-link-color', ttfmake_get_default( 'footer-link-color' ) ) );
	if ( $footer_link_color !== ttfmake_get_default( 'footer-link-color' ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array( '.site-footer a' ),
			'declarations' => array(
				'color' => $footer_link_color
			)
		) );
	}

	// Footer link hover color
	$footer_link_hover_color = maybe_hash_hex_color( get_theme_mod( 'footer-link-hover-color', ttfmake_get_default( 'footer-link-hover-color' ) ) );
	if ( $footer_link_hover_color !== ttfmake_get_default( 'footer-link-hover-color' ) || $footer_link_color !== ttfmake_get_default( 'footer-link-color' ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array(
				'.site-footer a:hover',
				'.site-footer a:focus',
			),
			'declarations' => array(
				'color' => $footer_link_hover_color
			)
		) );
	}

	// Footer border color
	$footer_border_color = maybe_hash_hex_color( get_theme_mod( 'footer-border-color', ttfmake_get_default( 'footer-border-color' ) ) );
	if ( $footer_border_color !== ttfmake_get_default( 'footer-border-color' ) ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array( '.site-footer *:not(select)' ),
			'declarations' => array(
				'border-color' => $footer_border_color . ' !important'
			)
		) );
	}

	// Footer background color
	$footer_background_color = maybe_hash_hex_color( get_theme_mod( 'footer-background-color', ttfmake_get_default( 'footer-background-color' ) ) );
	$footer_background_color_opacity = ttfmake_sanitize_float( get_theme_mod( 'footer-background-color-opacity', ttfmake_get_default( 'footer-background-color-opacity' ) ) );
	if ( $footer_background_color !== ttfmake_get_default( 'footer-background-color' ) || $footer_background_color_opacity !== (float) ttfmake_get_default( 'footer-background-color-opacity' ) ) {
		// Convert to RGBa
		$color_value = ttfmake_hex_to_rgb( $footer_background_color ) . ', ' . $footer_background_color_opacity;

		ttfmake_get_css()->add( array(
			'selectors'    => array( '.site-footer' ),
			'declarations' => array(
				'background-color' => 'rgba(' . $color_value . ')'
			)
		) );
	}
}

add_action( 'make_css', 'ttfmake_css_color' );

/**
 * Convert a hex string into a comma separated RGB string.
 *
 * @link http://bavotasan.com/2011/convert-hex-color-to-rgb-using-php/
 *
 * @since 1.5.0.
 *
 * @param  $value
 * @return bool|string
 */
function ttfmake_hex_to_rgb( $value ) {
	$hex = sanitize_hex_color_no_hash( $value );

	if ( 6 === strlen( $hex ) ) {
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );
	} else if ( 3 === strlen( $hex ) ) {
		$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
		$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
		$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
	} else {
		return false;
	}

	return "$r, $g, $b";
}