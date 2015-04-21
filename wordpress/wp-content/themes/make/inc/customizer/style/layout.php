<?php
/**
 * @package Make
 */

if ( ! function_exists( 'ttfmake_css_layout' ) ) :
/**
 * Build the CSS rules for the custom layout options.
 *
 * @since  1.5.0.
 *
 * @return void
 */
function ttfmake_css_layout() {
	/**
	 * Header & Footer
	 */
	$header_hide_padding_bottom = absint( get_theme_mod( 'header-hide-padding-bottom', ttfmake_get_default( 'header-hide-padding-bottom' ) ) );
	$footer_hide_padding_top = absint( get_theme_mod( 'footer-hide-padding-top', ttfmake_get_default( 'footer-hide-padding-top' ) ) );
	if ( $header_hide_padding_bottom || $footer_hide_padding_top ) {
		$declarations = array();
		if ( $header_hide_padding_bottom ) {
			$declarations['padding-top'] = 0;
		}
		if ( $footer_hide_padding_top ) {
			$declarations['padding-bottom'] = 0;
		}
		ttfmake_get_css()->add( array(
			'selectors'    => array( '.site-content' ),
			'declarations' => $declarations
		) );
	}


	/**
	 * Featured image alignment
	 */
	$views = array(
		'blog',
		'archive',
		'search',
		'post',
		'page'
	);

	foreach ( $views as $view ) {
		$key       = 'layout-' . $view . '-featured-images-alignment';
		$default   = ttfmake_get_default( $key );
		$alignment = ttfmake_sanitize_choice( get_theme_mod( $key, $default ), $key );

		if ( $alignment !== $default ) {
			ttfmake_get_css()->add( array(
				'selectors'    => array( '.' . $view . ' .entry-header .entry-thumbnail' ),
				'declarations' => array(
					'text-align' => $alignment,
				),
			) );
		}
	}
}
endif;

add_action( 'make_css', 'ttfmake_css_layout' );

if ( ! function_exists( 'ttfmake_maybe_add_with_avatar_class' ) ) :
/**
 * Add a class to the bounding div if a post uses an avatar with the author byline.
 *
 * @since  1.0.11.
 *
 * @param  array     $classes    An array of post classes.
 * @param  string    $class      A comma-separated list of additional classes added to the post.
 * @param  int       $post_ID    The post ID.
 * @return array                 The modified post class array.
 */
function ttfmake_maybe_add_with_avatar_class( $classes, $class, $post_ID ) {
	$author_key    = 'layout-' . ttfmake_get_view() . '-post-author';
	$author_option = ttfmake_sanitize_choice( get_theme_mod( $author_key, ttfmake_get_default( $author_key ) ), $author_key );

	if ( 'avatar' === $author_option ) {
		$classes[] = 'has-author-avatar';
	}

	return $classes;
}
endif;

add_filter( 'post_class', 'ttfmake_maybe_add_with_avatar_class', 10, 3 );