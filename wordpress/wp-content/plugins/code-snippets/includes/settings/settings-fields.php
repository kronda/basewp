<?php

/**
 * This file handles outputting the settings fields
 * @package Code_Snippets
 */

/**
 * Render a checkbox field for a setting
 * @param array $atts The setting field's attributes
 */
function code_snippets_checkbox_field( $atts ) {
	$saved_value = code_snippets_get_setting( $atts['section'], $atts['id'] );
	$input_name = sprintf( 'code_snippets_settings[%s][%s]', $atts['section'], $atts['id'] );

	$output = sprintf (
		'<input type="checkbox" name="%s"%s>',
		$input_name,
		checked( $saved_value, true, false )
	);

	if ( isset( $atts['label'] ) ) {
		printf ( '<label for="%s">%s %s</label>', $input_name, $output, $atts['label'] );
	} else {
		echo $output;
	}
}

/**
 * Render a number select field for an editor setting
 * @param array $atts The setting field's attributes
 */
function code_snippets_number_field( $atts ) {

	printf (
		'<input type="number" name="code_snippets_settings[%s][%s]" value="%s">',
		$atts['section'],
		$atts['id'],
		code_snippets_get_setting( $atts['section'], $atts['id'] )
	);
}
