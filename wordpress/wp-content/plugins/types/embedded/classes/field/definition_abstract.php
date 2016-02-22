<?php

/**
 * Abstract of a field definition (common interface and code for generic and Types field definitions).
 */
abstract class WPCF_Field_Definition_Abstract {

	/**
	 * @return string Field definition slug.
	 */
	public abstract function get_slug();


	/**
	 * @return string Field definition display name.
	 */
	public abstract function get_name();


	/**
	 * @return string Description provided by the user.
	 */
	public abstract function get_description();


	/**
	 * @return string Meta key used to store values of these fields.
	 */
	public abstract function get_meta_key();

	/**
	 * Determine whether the field is currently under Types control.
	 *
	 * @return mixed
	 */
	public abstract function is_under_types_control();


	/**
	 * @return WPCF_Field_Group[]
	 */
	public abstract function get_associated_groups();


	/**
	 * Does the field definition match a certain string?
	 *
	 * Searches it's name and slug.
	 *
	 * @param string $search_string
	 * @return bool
	 */
	public function is_match( $search_string ) {
		return (
			WPCF_Utils::is_string_match( $search_string, $this->get_name() )
			|| WPCF_Utils::is_string_match( $search_string, $this->get_slug() )
		);
	}


}