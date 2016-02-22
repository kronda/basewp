<?php

/**
 * Field definition for term fields.
 *
 * @since 1.9
 */
final class WPCF_Field_Definition_Term extends WPCF_Field_Definition {


	/**
	 * Get an accessor for a specific field instance.
	 *
	 * @param WPCF_Field_Instance $field_instance Instance of the field the accessor should access.
	 * @return WPCF_Field_Accessor_Termmeta_Field
	 */
	public function get_accessor( $field_instance ) {
		return new WPCF_Field_Accessor_Termmeta_Field(
			$field_instance->get_object_id(),
			$this->get_meta_key(),
			$this->get_is_repetitive(),
			$field_instance
		);
	}


	/**
	 * @return WPCF_Field_Group[]
	 */
	public function get_associated_groups() {
		$term_field_groups = WPCF_Field_Group_Term_Factory::get_instance()->query_groups();
		$associated_groups = array();
		foreach ( $term_field_groups as $field_group ) {
			if ( $field_group->contains_field_definition( $this ) ) {
				$associated_groups[] = $field_group;
			}
		}

		return $associated_groups;
	}


	/**
	 * Delete all field values!
	 *
	 * @return bool
	 */
	public function delete_all_fields() {
		global $wpdb;

		$meta_key = $this->get_meta_key();

		$termmeta_records = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT term_id FROM $wpdb->termmeta WHERE meta_key = %s",
				$meta_key
			)
		);

		// Delete one by one because we (probably) want all the WP hooks to fire.
		foreach ( $termmeta_records as $termmeta ) {
			delete_term_meta( $termmeta->term_id, $meta_key );
		}

		return true;
	}
}