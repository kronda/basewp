<?php

if ( ! function_exists( 'ttfmp_woocommerce_add_color_css' ) ) :
/**
 * Use Make's color options to override some of WooCommerce's CSS styles
 *
 * @since 1.0.0.
 *
 * @return void
 */
function ttfmp_woocommerce_add_color_css() {
	// Get and escape options
	$color_primary         = maybe_hash_hex_color( get_theme_mod( 'color-primary', ttfmake_get_default( 'color-primary' ) ) );
	$color_secondary       = maybe_hash_hex_color( get_theme_mod( 'color-secondary', ttfmake_get_default( 'color-secondary' ) ) );
	$color_text            = maybe_hash_hex_color( get_theme_mod( 'color-text', ttfmake_get_default( 'color-text' ) ) );
	$color_detail          = maybe_hash_hex_color( get_theme_mod( 'color-detail', ttfmake_get_default( 'color-detail' ) ) );
	$color_highlight       = maybe_hash_hex_color( get_theme_mod( 'color-highlight', ttfmake_get_default( 'color-highlight' ) ) );
	$main_background_color = maybe_hash_hex_color( get_theme_mod( 'main-background-color', ttfmake_get_default( 'main-background-color' ) ) );

	// Output the rules
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.woocommerce #content div.product .woocommerce-tabs ul.tabs li',
			'.woocommerce div.product .woocommerce-tabs ul.tabs li',
			'.woocommerce-page #content div.product .woocommerce-tabs ul.tabs li',
			'.woocommerce-page div.product .woocommerce-tabs ul.tabs li'
		),
		'declarations' => array(
			'background-color' => $color_secondary
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.product_meta',
			'.product_meta a'
		),
		'declarations' => array(
			'color' => $color_detail
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'product_meta a:hover',
		),
		'declarations' => array(
			'color' => $color_primary
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.woocommerce #content .quantity .minus',
			'.woocommerce #content .quantity .plus',
			'.woocommerce .quantity .minus',
			'.woocommerce .quantity .plus',
			'.woocommerce-page #content .quantity .minus',
			'.woocommerce-page #content .quantity .plus',
			'.woocommerce-page .quantity .minus',
			'.woocommerce-page .quantity .plus',
			'.woocommerce #content .quantity input.qty',
			'.woocommerce .quantity input.qty',
			'.woocommerce-page #content .quantity input.qty',
			'.woocommerce-page .quantity input.qty',
		),
		'declarations' => array(
			'background-color' => $color_secondary
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.woocommerce #content .quantity .minus',
			'.woocommerce #content .quantity .plus',
			'.woocommerce .quantity .minus',
			'.woocommerce .quantity .plus',
			'.woocommerce-page #content .quantity .minus',
			'.woocommerce-page #content .quantity .plus',
			'.woocommerce-page .quantity .minus',
			'.woocommerce-page .quantity .plus',
			'.woocommerce #content .quantity input.qty',
			'.woocommerce .quantity input.qty',
			'.woocommerce-page #content .quantity input.qty',
			'.woocommerce-page .quantity input.qty',
		),
		'declarations' => array(
			'background-color' => $color_secondary
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.woocommerce #content .quantity .minus:hover',
			'.woocommerce #content .quantity .plus:hover',
			'.woocommerce .quantity .minus:hover',
			'.woocommerce .quantity .plus:hover',
			'.woocommerce-page #content .quantity .minus:hover',
			'.woocommerce-page #content .quantity .plus:hover',
			'.woocommerce-page .quantity .minus:hover',
			'.woocommerce-page .quantity .plus:hover',
		),
		'declarations' => array(
			'background-color' => $main_background_color
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.woocommerce #content input.button.alt',
			'.woocommerce #respond input#submit.alt',
			'.woocommerce a.button.alt',
			'.woocommerce button.button.alt',
			'.woocommerce input.button.alt',
			'.woocommerce-page #content input.button.alt',
			'.woocommerce-page #respond input#submit.alt',
			'.woocommerce-page a.button.alt',
			'.woocommerce-page button.button.alt',
			'.woocommerce-page input.button.alt',
			'.woocommerce #content input.button.alt:hover',
			'.woocommerce #respond input#submit.alt:hover',
			'.woocommerce a.button.alt:hover',
			'.woocommerce button.button.alt:hover',
			'.woocommerce input.button.alt:hover',
			'.woocommerce-page #content input.button.alt:hover',
			'.woocommerce-page #respond input#submit.alt:hover',
			'.woocommerce-page a.button.alt:hover',
			'.woocommerce-page button.button.alt:hover',
			'.woocommerce-page input.button.alt:hover',
		),
		'declarations' => array(
			'background-color' => $color_primary
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.woocommerce #content input.button',
			'.woocommerce #respond input#submit',
			'.woocommerce a.button',
			'.woocommerce button.button',
			'.woocommerce input.button',
			'.woocommerce-page #content input.button',
			'.woocommerce-page #respond input#submit',
			'.woocommerce-page a.button',
			'.woocommerce-page button.button',
			'.woocommerce-page input.button',
			'.woocommerce #content input.button:hover',
			'.woocommerce #respond input#submit:hover',
			'.woocommerce a.button:hover',
			'.woocommerce button.button:hover',
			'.woocommerce input.button:hover',
			'.woocommerce-page #content input.button:hover',
			'.woocommerce-page #respond input#submit:hover',
			'.woocommerce-page a.button:hover',
			'.woocommerce-page button.button:hover',
			'.woocommerce-page input.button:hover',
		),
		'declarations' => array(
			'background-color' => $color_secondary
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.woocommerce span.onsale',
			'.woocommerce-page span.onsale',
		),
		'declarations' => array(
			'background-color' => $color_highlight
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.woocommerce .woocommerce-error',
			'.woocommerce .woocommerce-info',
			'.woocommerce .woocommerce-message',
			'.woocommerce-page .woocommerce-error',
			'.woocommerce-page .woocommerce-info',
			'.woocommerce-page .woocommerce-message',
		),
		'declarations' => array(
			'background-color' => $color_secondary
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.shipping-calculator-button',
		),
		'declarations' => array(
			'color' => $color_highlight
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.woocommerce #payment',
			'.woocommerce-page #payment',
		),
		'declarations' => array(
			'background-color' => $color_secondary
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.woocommerce #payment div.payment_box',
			'.woocommerce-page #payment div.payment_box',
		),
		'declarations' => array(
			'border-color' => $color_detail . ' !important',
			'background-color' => $color_detail
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.builder-section-content .products h3',
		),
		'declarations' => array(
			'color' => $color_text
		)
	) );
}
endif;

add_action( 'ttfmake_css', 'ttfmp_woocommerce_add_color_css' );
