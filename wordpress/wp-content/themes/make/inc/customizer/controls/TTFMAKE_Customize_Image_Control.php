<?php
/**
 * @package Make
 */

if ( class_exists( 'WP_Customize_Image_Control' ) && ! class_exists( 'TTFMAKE_Customize_Image_Control' ) ) :
/**
 * Class TTFMAKE_Customize_Image_Control
 *
 * Extend WP_Customize_Image_Control allowing access to uploads made within the same context.
 *
 * @since 1.0.0.
 */
class TTFMAKE_Customize_Image_Control extends WP_Customize_Image_Control {
	/**
	 * Override the stock tab_uploaded function.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function tab_uploaded() {
		$images = get_posts( array(
			'post_type'  => 'attachment',
			'meta_key'   => '_wp_attachment_context',
			'meta_value' => $this->context,
			'orderby'    => 'none',
			'nopaging'   => true,
		) );

		?><div class="uploaded-target"></div><?php

		if ( empty( $images ) ) {
			return;
		}

		foreach ( (array) $images as $image ) {
			$thumbnail_url = wp_get_attachment_image_src( $image->ID, 'medium' );
			$this->print_tab_image( esc_url_raw( $image->guid ), esc_url_raw( $thumbnail_url[0] ) );
		}
	}
}
endif;