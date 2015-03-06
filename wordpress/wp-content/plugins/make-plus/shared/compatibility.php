<?php
/**
 * @package Make Plus
 */

if ( ! function_exists( 'ttfmake_customizer_supports_panels' ) ) :
/**
* Detect support for Customizer panels
*
* This feature was introduced in WP 4.0.
*
* @since 1.3.0.
*
* @return bool
*/
function ttfmake_customizer_supports_panels() {
	return ( class_exists( 'WP_Customize_Manager' ) && method_exists( 'WP_Customize_Manager', 'add_panel' ) ) || function_exists( 'wp_validate_boolean' );
}
endif;