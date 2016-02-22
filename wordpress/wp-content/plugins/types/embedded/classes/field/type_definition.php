<?php

/**
 * Field type definition.
 *
 * This represents a single field type like "email", "audio", "checkbox" and so on. This class must be instantiated
 * exclusively through WPCF_Field_Type_Definition_Factory.
 */
final class WPCF_Field_Type_Definition {


	/**
	 * @var string Slug of the registered field type.
	 */
	private $field_type_slug;


	/**
	 * @var string Name of the field type that can be displayed to the user.
	 */
	private $display_name;


	/**
	 * @var string Field description entered by the user.
	 */
	private $description;


	/**
	 * @var array Arguments defining the field type. Can contain some legacy values.
	 */
	private $args;


	/**
	 * WPCF_Field_Type_Definition constructor.
	 *
	 * @param string $field_type_slug Field type slug.
	 * @param array $args Additional array of arguments which should contain at least 'display_name' (or 'title')
	 * and 'description' elements, but omitting them is not critical.
	 */
	public function __construct( $field_type_slug, $args ) {

		if( sanitize_title( $field_type_slug ) != $field_type_slug ) {
			throw new InvalidArgumentException( 'Invalid field type slug.' );
		}

		if( ! is_array( $args ) ) {
			throw new InvalidArgumentException( 'Wrong arguments provided.' );
		}

		$this->field_type_slug = $field_type_slug;

		// Try to fall back to legacy "title", and if even that fails, use id instead.
		$this->display_name = sanitize_text_field( wpcf_getarr( $args, 'display_name', wpcf_getarr( $args, 'title', $field_type_slug ) ) );

		$this->description = wpcf_getarr( $args, 'description', '' );
		$this->args = $args;
	}


	public function get_slug() { return $this->field_type_slug; }

	public function get_display_name() { return $this->display_name; }

	public function get_description() { return $this->description; }


	/**
	 * Direct access to the field type configuration.
	 *
	 * It is strongly encouraged to write custom (and safe) getters for anything you need to get from it.
	 *
	 * @return array
	 */
	public function get_args() { return $this->args; }

}