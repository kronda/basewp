<?php

/**
 * Static class for shortcut functions related to field types, groups, definitions and instances.
 * @since 1.9
 */
final class WPCF_Field_Utils {

	private function __construct() { }


	/**
	 * Create a term field instance.
	 *
	 * @param string $field_slug Slug of existing field definition.
	 * @param int $term_id ID of the term where the field belongs.
	 *
	 * @return null|WPCF_Field_Instance Field instance or null if an error occurs.
	 * @since 1.9
	 */
	public static function create_term_field_instance( $field_slug, $term_id ) {
		try {
			return new WPCF_Field_Instance( WPCF_Field_Term_Definition_Factory::load( $field_slug ), $term_id );
		} catch( Exception $e ) {
			return null;
		}
	}


	/**
	 * Obtain toolset-forms "field configuration", which is an array of settings for specific field instance.
	 *
	 * @param WPCF_Field_Instance $field
	 * @since 1.9
	 */
	public static function get_toolset_forms_field_config( $field ) {
		return wptoolset_form_filter_types_field(
			$field->get_definition()->get_definition_array(),
			$field->get_object_id()
		);
	}


	/**
	 * Gather an unique array of field definitions from given groups.
	 *
	 * The groups are expected to belong to the same domain (term/post/user), otherwise problems may occur when
	 * field slugs conflict.
	 *
	 * @param WPCF_Field_Group[] $field_groups
	 * @return WPCF_Field_Definition[]
	 * @since 1.9
	 */
	public static function get_field_definitions_from_groups( $field_groups ) {
		$field_definitions = array();
		foreach( $field_groups as $group ) {
			$group_field_definitions = $group->get_field_definitions();

			foreach( $group_field_definitions as $field_definition ) {
				$field_definitions[ $field_definition->get_slug() ] = $field_definition;
			}
		}
		return $field_definitions;
	}

}