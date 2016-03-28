<?php

/**
 * Base for preview renderer fields.
 *
 * Preview renderers are to be used mainly in admin, for example in post/user/term listings. Their task is to really
 * render a simple preview for the value, nothing more. In some cases the output may not even contain the complete
 * information (e.g. lots of images in a repetitive field).
 *
 * This class handles displaying repetitive fields, other preview renderers are supposed to inherit from it and
 * implement only the render_single() method.
 *
 * @since 1.9.1
 */
abstract class WPCF_Field_Renderer_Preview_Base extends WPCF_Field_Renderer_Abstract {


	protected $args;


	/**
	 * WPCF_Field_Renderer_Preview_Base constructor.
	 *
	 * @param WPCF_Field_Instance_Abstract $field
	 * @param array $args Preview renderer settings: 
	 *     - maximum_item_count => Maximum count of field values that should be displayed.  
	 *     - maximum_item_length => Maximum length of single item.
	 *     - value_separator => Separator to be used between multiple field values.
	 *     - ellipsis => Ellipsis to be added when some field values are omitted.
	 * 
	 *     Specialized renderers may interpret the settings in a different way or add their own.
	 *
	 * @since 1.9.1 
	 */
	public function __construct( $field, $args = array() ) {
		parent::__construct( $field );
		
		$this->args = wpcf_ensarr( $args );
	}

	/**
	 * Render the field value. Handle both single and repetitive fields.
	 *
	 * Rendering of a single value is defined in render_single() and multiple values are concatenated by
	 * separator provided by get_value_separator().
	 *
	 * @param bool $echo Echo the output?
	 * @return string Rendered HTML.
	 * @since 1.9.1
	 */
	public function render( $echo = false ) {

		$field_value = $this->field->get_value();

		// Handle all fields as repetitive, we allways have array of individual field values.
		$output_values = array();

		// Optionally limit the number of rendered items
		$max_item_count = $this->get_maximum_item_count();
		$loop_limit = (
			$max_item_count > 0
			? min( $max_item_count, count( $field_value ) )
			: count( $field_value )
		);

		$is_limited_by_max_count = ( $loop_limit < count( $field_value ) );
		for( $i = 0; $i < $loop_limit; ++$i ) {
			$value = array_shift( $field_value );
			$output_values[] = $this->render_single( $value );
		}

		$output = implode( $this->get_value_separator(), $output_values );
			
		$maximum_total_length = $this->get_maximum_total_length();
		$is_limited_by_max_total_length = ( 0 < $maximum_total_length && $maximum_total_length < strlen( $output ) );
		if( $is_limited_by_max_total_length ) {
			$output = substr( $output, 0, $maximum_total_length );
		}
		
		$needs_ellipsis = ( $is_limited_by_max_count || $is_limited_by_max_total_length ); 
		if( $needs_ellipsis ) {
			$output .= $this->get_ellipsis();
		}

		if( $echo ) {
			echo $output;
		}

		return $output;
	}


	/**
	 * @return string Separator to be used between multiple field values.
	 */
	protected function get_value_separator() {
		return wpcf_getarr( $this->args, 'value_separator', ', ' );
	}


	/**
	 * @param mixed $value Single field value in the intermediate format (see data mappers for details)
	 * @return string Rendered HTML
	 */
	protected abstract function render_single( $value );


	/**
	 * @return int Maximum count of field values that should be displayed. Zero means no limit.
	 * @since 1.9.1
	 */
	protected function get_maximum_item_count() {
		return absint( wpcf_getarr( $this->args, 'maximum_item_count' ) );
	}


	/**
	 * @return int Maximum length of single item. Interpretation depends on specific renderer (on a field type); it
	 *     may be completely ignored. Zero means no limit.
	 * @since 1.9.1
	 */
	protected function get_maximum_item_length() {
		return absint( wpcf_getarr( $this->args, 'maximum_item_length' ) );
	}


	/**
	 * @return int Maximum length of the final output. Zero means no limit. When some HTML is rendered, this method
	 *     needs to be overridden to allways return zero, otherwise the markup might be broken.
	 * @since 1.9.1
	 */
	protected function get_maximum_total_length() {
		return absint( wpcf_getarr( $this->args, 'maximum_total_length' ) );
	}



	/**
	 * @return string Ellipsis to be added when some field values are omitted.
	 * @since 1.9.1
	 */
	protected function get_ellipsis() {
		return wpcf_getarr( $this->args, 'ellipsis', $this->get_value_separator() . '...' );
	}
	
}