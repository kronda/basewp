<?php

/**
 * Google address preview renderer.
 *
 * Displays excerpt of the address with a link to Google Maps.
 *
 * @since 1.9.1
 */
final class WPCF_Field_Renderer_Preview_Address extends WPCF_Field_Renderer_Preview_Base {

	/**
	 * @param mixed $value Single field value in the intermediate format (see data mappers for details)
	 *
	 * @return string Rendered HTML
	 */
	protected function render_single( $value ) {

		if( !is_string( $value ) ) {
			return '';
		}
		
		$label = $value;
		
		// Keep maximum length per item
		$max_length = $this->get_maximum_item_length();
		if( 0 < $max_length && $max_length < strlen( $label ) ) {
			$label = substr( $label, 0, $max_length - 3 ) . '...';
		}
		
		$link = sprintf(
			'<a target="_blank" href="http://maps.google.com/?q=%s">%s</a>',
			esc_attr( $value ),
			sanitize_text_field( $label )	
		);
		
		return $link;
	}
	
	
	protected function get_maximum_total_length() {
		return 0;
	}

}