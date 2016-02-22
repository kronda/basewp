<?php

/**
 * Factory for term field definitions.
 */
final class WPCF_Field_Term_Definition_Factory extends WPCF_Field_Definition_Factory {

	/**
	 * Name of the option used to store term field definitions.
	 */
	const FIELD_DEFINITIONS_OPTION = 'wpcf-termmeta';


	protected function get_option_name() {
		return self::FIELD_DEFINITIONS_OPTION;
	}


	protected function get_class_name() {
		return 'WPCF_Field_Definition_Term';
	}


	/**
	 * Shortcut to load_field_definition().
	 *
	 * @param string $field_key
	 * @return null|WPCF_Field_Definition
	 */
	static function load( $field_key ) {
		// we cannot use self::get_instance here, because of low PHP requirements and missing get_called_class function
		// we have a fallback class for get_called_class but that scans files by debug_backtrace and return 'self'
		//   instead of WPCF_Field_Term_Definition_Factory like the original get_called_class() function does
		// ends in an error because of parents (abstract) $var = new self();
		return WPCF_Field_Term_Definition_Factory::get_instance()->load_field_definition( $field_key );
	}

	/**
	 * @return string[] All existing meta keys within the domain (= term meta).
	 */
	protected function get_existing_meta_keys() {
		global $wpdb;

		$meta_keys = $wpdb->get_col(
			"SELECT meta_key FROM {$wpdb->termmeta} GROUP BY meta_key HAVING meta_key NOT LIKE '\_%' ORDER BY meta_key"
		);

		return $meta_keys;
	}

}