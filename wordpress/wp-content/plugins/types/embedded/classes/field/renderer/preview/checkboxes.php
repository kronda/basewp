<?php

/**
 * Preview renderer for checkboxes field.
 * 
 * @since 1.9.1 
 */
final class WPCF_Field_Renderer_Preview_Checkboxes extends WPCF_Field_Renderer_Preview_Base {

	/**
	 * Render preview for whole checkboxes field. Slightly confusingly, checkboxes fields are allways single
	 * and all options are part of one field value.
	 *
	 * @param mixed $value Single field value in the intermediate format (see data mappers for details)
	 *
	 * @return string Rendered HTML
	 */
	protected function render_single( $value ) {

		$options = $this->field->get_definition()->get_field_options();
		$output = array();

		// Let each checkbox option definition handle how it should be displayed.
		foreach( $options as $option ) {
			$display_value = ( $option->is_option_checked( $value ) ? $option->get_label() : '' );
			if( !empty( $display_value ) ) {
				$output[] = $display_value;
			}
		}

		// Apply maximum count here
		$max_item_count = $this->get_maximum_item_count();
		$is_limited_by_max_count = ( 0 < $max_item_count && $max_item_count < count( $output ) );
		if( $is_limited_by_max_count ) {
			$output = array_slice( $output, 0, $this->get_maximum_item_count() );
		}
		
		$output = implode( $this->get_value_separator(), $output );

		if( $is_limited_by_max_count ) {
			$output .= $this->get_ellipsis();
		}

		return sanitize_text_field( $output );
	}


}