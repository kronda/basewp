<?php
/**
 * @package Make
 */

/**
 * Prevent switching to Make.
 *
 * @since 1.6.1.
 *
 * @link https://github.com/WordPress/WordPress/blob/4.1.1/wp-content/themes/twentyfifteen/inc/back-compat.php#L14-L39
 *
 * @return void
 */
function ttfmake_prevent_activation() {
	switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );
	unset( $_GET['activated'] );
	add_action( 'admin_notices', 'ttfmake_activation_notice' );
}

if ( version_compare( $GLOBALS['wp_version'], TTFMAKE_MIN_WP_VERSION, '<' ) ) {
	add_action( 'after_switch_theme', 'ttfmake_prevent_activation' );
}

/**
 * Show activation notice.
 *
 * @since 1.6.1.
 *
 * @return void
 */
function ttfmake_activation_notice() {
	$message = sprintf(
		__( 'Make requires at least WordPress version %1$s. You are running version %2$s. Please upgrade and try again.', 'make' ),
		esc_html( TTFMAKE_MIN_WP_VERSION ),
		esc_html( $GLOBALS['wp_version'] )
	);
	printf( '<div class="error"><p>%s</p></div>', $message );
}