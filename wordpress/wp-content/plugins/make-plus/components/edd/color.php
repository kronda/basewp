<?php
/**
 * @package Make Plus
 */

if ( ! function_exists( 'ttfmp_edd_add_color_css' ) ) :
/**
 * Use Make's color options to override some of EDD's CSS styles
 *
 * @since 1.0.0.
 *
 * @return void
 */
function ttfmp_edd_add_color_css() {
	// Get and escape options
	$color_secondary = maybe_hash_hex_color( get_theme_mod( 'color-secondary', ttfmake_get_default( 'color-secondary' ) ) );
	$color_highlight = maybe_hash_hex_color( get_theme_mod( 'color-highlight', ttfmake_get_default( 'color-highlight' ) ) );

	// Output the rules
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.edd-submit.button.blue',
			'#edd-purchase-button',
			'.edd-submit',
			'input[type=submit].edd-submit',
			'#edd_checkout_cart a.edd-cart-saving-button',
			'.edd-submit.button.blue.active',
			'.edd-submit.button.blue:focus',
			'.edd-submit.button.blue:hover',
			'#edd_checkout_form_wrap #edd_final_total_wrap'
		),
		'declarations' => array(
			'background-color' => $color_secondary
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'#edd_checkout_cart td',
			'#edd_checkout_cart th',
			'#edd_checkout_form_wrap fieldset'
		),
		'declarations' => array(
			'border-color' => $color_secondary
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.edd_price',
			'.edd-cart-added-alert',
		),
		'declarations' => array(
			'color' => $color_highlight
		)
	) );
}
endif;

add_action( 'ttfmake_css', 'ttfmp_edd_add_color_css' );
