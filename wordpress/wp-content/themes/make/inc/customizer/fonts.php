<?php
/**
 * @package Make
 */

if ( ! function_exists( 'ttfmake_get_font_property_option_keys' ) ) :
/**
 * Return all the option keys for the specified font property.
 *
 * @since  1.3.0.
 *
 * @param  string    $property    The font property to search for.
 * @return array                  Array of matching font option keys.
 */
function ttfmake_get_font_property_option_keys( $property ) {
	$all_keys = array_keys( ttfmake_option_defaults() );

	$font_keys = array();
	foreach ( $all_keys as $key ) {
		if ( preg_match( '/^' . $property . '-/', $key ) || preg_match( '/^font-' . $property . '-/', $key ) ) {
			$font_keys[] = $key;
		}
	}

	return $font_keys;
}
endif;

if ( ! function_exists( 'ttfmake_get_standard_fonts' ) ) :
/**
 * Return an array of standard websafe fonts.
 *
 * @since  1.0.0.
 *
 * @return array    Standard websafe fonts.
 */
function ttfmake_get_standard_fonts() {
	/**
	 * Allow for developers to modify the standard fonts.
	 *
	 * @since 1.2.3.
	 *
	 * @param array    $fonts    The list of standard fonts.
	 */
	return apply_filters( 'make_get_standard_fonts', array(
		'serif' => array(
			'label' => _x( 'Serif', 'font style', 'make' ),
			'stack' => 'Georgia,Times,"Times New Roman",serif'
		),
		'sans-serif' => array(
			'label' => _x( 'Sans Serif', 'font style', 'make' ),
			'stack' => '"Helvetica Neue",Helvetica,Arial,sans-serif'
		),
		'monospace' => array(
			'label' => _x( 'Monospaced', 'font style', 'make' ),
			'stack' => 'Monaco,"Lucida Sans Typewriter","Lucida Typewriter","Courier New",Courier,monospace'
		)
	) );
}
endif;

if ( ! function_exists( 'ttfmake_get_google_font_uri' ) ) :
/**
 * Build the HTTP request URL for Google Fonts.
 *
 * @since  1.0.0.
 *
 * @return string    The URL for including Google Fonts.
 */
function ttfmake_get_google_font_uri() {
	// Grab the font choices
	if ( ttfmake_customizer_supports_panels() ) {
		$all_keys = array_keys( ttfmake_option_defaults() );
		$font_keys = array();
		foreach ( $all_keys as $key ) {
			if ( false !== strpos( $key, 'font-family-' ) ) {
				$font_keys[] = $key;
			}
		}
	} else {
		$font_keys = array(
			'font-site-title',
			'font-header',
			'font-body',
		);
	}
	$fonts = array();
	foreach ( $font_keys as $key ) {
		$fonts[] = get_theme_mod( $key, ttfmake_get_default( $key ) );
	}

	// De-dupe the fonts
	$fonts         = array_unique( $fonts );
	$allowed_fonts = ttfmake_get_google_fonts();
	$family        = array();

	// Validate each font and convert to URL format
	foreach ( $fonts as $font ) {
		$font = trim( $font );

		// Verify that the font exists
		if ( array_key_exists( $font, $allowed_fonts ) ) {
			// Build the family name and variant string (e.g., "Open+Sans:regular,italic,700")
			$family[] = urlencode( $font . ':' . join( ',', ttfmake_choose_google_font_variants( $font, $allowed_fonts[ $font ]['variants'] ) ) );
		}
	}

	// Convert from array to string
	if ( empty( $family ) ) {
		return '';
	} else {
		$request = '//fonts.googleapis.com/css?family=' . implode( '|', $family );
	}

	// Load the font subset
	$subset = get_theme_mod( 'font-subset', ttfmake_get_default( 'font-subset' ) );

	if ( 'all' === $subset ) {
		$subsets_available = ttfmake_get_google_font_subsets();

		// Remove the all set
		unset( $subsets_available['all'] );

		// Build the array
		$subsets = array_keys( $subsets_available );
	} else {
		$subsets = array(
			'latin',
			$subset,
		);
	}

	// Append the subset string
	if ( ! empty( $subsets ) ) {
		$request .= urlencode( '&subset=' . join( ',', $subsets ) );
	}

	/**
	 * Filter the Google Fonts URL.
	 *
	 * @since 1.2.3.
	 *
	 * @param string    $url    The URL to retrieve the Google Fonts.
	 */
	return apply_filters( 'make_get_google_font_uri', esc_url( $request ) );
}
endif;

if ( ! function_exists( 'ttfmake_choose_google_font_variants' ) ) :
/**
 * Given a font, chose the variants to load for the theme.
 *
 * Attempts to load regular, italic, and 700. If regular is not found, the first variant in the family is chosen. italic
 * and 700 are only loaded if found. No fallbacks are loaded for those fonts.
 *
 * @since  1.0.0.
 *
 * @param  string    $font        The font to load variants for.
 * @param  array     $variants    The variants for the font.
 * @return array                  The chosen variants.
 */
function ttfmake_choose_google_font_variants( $font, $variants = array() ) {
	$chosen_variants = array();
	if ( empty( $variants ) ) {
		$fonts = ttfmake_get_google_fonts();

		if ( array_key_exists( $font, $fonts ) ) {
			$variants = $fonts[ $font ]['variants'];
		}
	}

	// If a "regular" variant is not found, get the first variant
	if ( ! in_array( 'regular', $variants ) ) {
		$chosen_variants[] = $variants[0];
	} else {
		$chosen_variants[] = 'regular';
	}

	// Only add "italic" if it exists
	if ( in_array( 'italic', $variants ) ) {
		$chosen_variants[] = 'italic';
	}

	// Only add "700" if it exists
	if ( in_array( '700', $variants ) ) {
		$chosen_variants[] = '700';
	}

	/**
	 * Allow developers to alter the font variant choice.
	 *
	 * @since 1.2.3.
	 *
	 * @param array     $variants    The list of variants for a font.
	 * @param string    $font        The font to load variants for.
	 * @param array     $variants    The variants for the font.
	 */
	return apply_filters( 'make_font_variants', array_unique( $chosen_variants ), $font, $variants );
}
endif;

if ( ! function_exists( 'ttfmake_get_google_font_subsets' ) ) :
/**
 * Retrieve the list of available Google font subsets.
 *
 * @since  1.0.0.
 *
 * @return array    The available subsets.
 */
function ttfmake_get_google_font_subsets() {
	/**
	 * Filter the list of supported Google Font subsets.
	 *
	 * @since 1.2.3.
	 *
	 * @param array    $subsets    The list of subsets.
	 */
	return apply_filters( 'make_get_google_font_subsets', array(
		'all'          => __( 'All', 'make' ),
		'cyrillic'     => __( 'Cyrillic', 'make' ),
		'cyrillic-ext' => __( 'Cyrillic Extended', 'make' ),
		'devanagari'   => __( 'Devanagari', 'make' ),
		'greek'        => __( 'Greek', 'make' ),
		'greek-ext'    => __( 'Greek Extended', 'make' ),
		'khmer'        => __( 'Khmer', 'make' ),
		'latin'        => __( 'Latin', 'make' ),
		'latin-ext'    => __( 'Latin Extended', 'make' ),
		'telugu'       => __( 'Telugu', 'make' ),
		'vietnamese'   => __( 'Vietnamese', 'make' ),
	) );
}
endif;

if ( ! function_exists( 'ttfmake_sanitize_font_subset' ) ) :
/**
 * Sanitize the Character Subset choice.
 *
 * @since  1.0.0
 *
 * @param  string    $value    The value to sanitize.
 * @return array               The sanitized value.
 */
function ttfmake_sanitize_font_subset( $value ) {
	if ( ! array_key_exists( $value, ttfmake_get_google_font_subsets() ) ) {
		$value = ttfmake_get_default( 'font-subset' );
	}

	/**
	 * Filter the sanitized subset choice.
	 *
	 * @since 1.2.3.
	 *
	 * @param string    $value    The chosen subset value.
	 */
	return apply_filters( 'make_sanitize_font_subset', $value );
}
endif;

if ( ! function_exists( 'ttfmake_all_font_choices' ) ) :
/**
 * Packages the font choices into value/label pairs for use with the customizer.
 *
 * @since  1.0.0.
 *
 * @return array    The fonts in value/label pairs.
 */
function ttfmake_all_font_choices() {
	$fonts   = ttfmake_get_all_fonts();
	$choices = array();

	// Repackage the fonts into value/label pairs
	foreach ( $fonts as $key => $font ) {
		$choices[ $key ] = $font['label'];
	}

	/**
	 * Allow for developers to modify the full list of fonts.
	 *
	 * @since 1.2.3.
	 *
	 * @param array    $choices    The list of all fonts.
	 */
	return apply_filters( 'make_all_font_choices', $choices );
}
endif;

if ( ! function_exists( 'ttfmake_all_font_choices_js' ) ) :
/**
 * Compile the font choices for better handling as a JSON object
 *
 * @since 1.3.0.
 *
 * @return array
 */
function ttfmake_all_font_choices_js() {
	$fonts   = ttfmake_get_all_fonts();
	$choices = array();

	// Repackage the fonts into value/label pairs
	foreach ( $fonts as $key => $font ) {
		$choices[] = array( 'k' => $key, 'l' => $font['label'] );
	}

	return $choices;
}
endif;

if ( ! function_exists( 'ttfmake_sanitize_font_choice' ) ) :
/**
 * Sanitize a font choice.
 *
 * @since  1.0.0.
 *
 * @param  string    $value    The font choice.
 * @return string              The sanitized font choice.
 */
function ttfmake_sanitize_font_choice( $value ) {
	if ( ! is_string( $value ) ) {
		// The array key is not a string, so the chosen option is not a real choice
		return '';
	} else if ( array_key_exists( $value, ttfmake_all_font_choices() ) ) {
		return $value;
	} else {
		return '';
	}

	/**
	 * Filter the sanitized font choice.
	 *
	 * @since 1.2.3.
	 *
	 * @param string    $value    The chosen font value.
	 */
	return apply_filters( 'make_sanitize_font_choice', $return );
}
endif;

if ( ! function_exists( 'ttfmake_get_all_fonts' ) ) :
/**
 * Compile font options from different sources.
 *
 * @since  1.0.0.
 *
 * @return array    All available fonts.
 */
function ttfmake_get_all_fonts() {
	$heading1       = array( 1 => array( 'label' => sprintf( '--- %s ---', __( 'Standard Fonts', 'make' ) ) ) );
	$standard_fonts = ttfmake_get_standard_fonts();
	$heading2       = array( 2 => array( 'label' => sprintf( '--- %s ---', __( 'Google Fonts', 'make' ) ) ) );
	$google_fonts   = ttfmake_get_google_fonts();

	/**
	 * Allow for developers to modify the full list of fonts.
	 *
	 * @since 1.2.3.
	 *
	 * @param array    $fonts    The list of all fonts.
	 */
	return apply_filters( 'make_all_fonts', array_merge( $heading1, $standard_fonts, $heading2, $google_fonts ) );
}
endif;
