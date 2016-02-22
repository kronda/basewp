<?php

/**
 * Term field group.
 *
 * @since 1.9
 */
final class WPCF_Field_Group_Term extends WPCF_Field_Group {


	const POST_TYPE = 'wp-types-term-group';


	/**
	 * Key for postmeta that holds slugs of taxonomies associated with this group. This is a "plural" postmeta,
	 * each record contains one slug.
	 */
	const POSTMETA_ASSOCIATED_TAXONOMY = '_wp_types_associated_taxonomy';


	/**
	 * WPCF_Field_Group_Term constructor.
	 *
	 * @param WP_Post $field_group_post Post object representing a term field group.
	 * @throws InvalidArgumentException
	 */
	public function __construct( $field_group_post ) {
		parent::__construct( $field_group_post );
		if( self::POST_TYPE != $field_group_post->post_type ) {
			throw new InvalidArgumentException( 'incorrect post type' );
		}
	}


	/**
	 * @return WPCF_Field_Definition_Factory Field definition factory of the correct type.
	 */
	protected function get_field_definition_factory() {
		return WPCF_Field_Term_Definition_Factory::get_instance();
	}


	/**
	 * Get taxonomies that are associated with this field group.
	 *
	 * @return string[] Taxonomy slugs. Empty array means that this group should be displayed with all taxonomies.
	 */
	public function get_associated_taxonomies() {
		$postmeta = get_post_meta( $this->get_id(), self::POSTMETA_ASSOCIATED_TAXONOMY, false );
		return wpcf_ensarr( $postmeta );
	}


	/**
	 * Quickly determine whether given taxonomy is associated with this group.
	 *
	 * @param string $taxonomy_slug
	 * @return bool
	 */
	public function has_associated_taxonomy( $taxonomy_slug ) {
		$taxonomies = $this->get_associated_taxonomies();
		return ( empty( $taxonomies ) || in_array( $taxonomy_slug, $taxonomies ) );
	}


	/**
	 * Remove association with a single taxonomy from the database.
	 *
	 * @param string $taxonomy_slug Slug of the taxonomy.
	 */
	private function remove_associated_taxonomy( $taxonomy_slug ) {
		if( empty( $taxonomy_slug ) ) {
			return;
		}
		delete_post_meta( $this->get_id(), self::POSTMETA_ASSOCIATED_TAXONOMY, $taxonomy_slug );
		$this->execute_group_updated_action();
	}


	/**
	 * Add an association with a single taxonomy to the database.
	 *
	 * @param string $taxonomy_slug Slug of the taxonomy. If empty or not sanitized, the function does nothing.
	 */
	private function add_associated_taxonomy( $taxonomy_slug ) {
		if( empty( $taxonomy_slug ) || $taxonomy_slug != sanitize_title( $taxonomy_slug )) {
			return;
		}
		add_post_meta( $this->get_id(), self::POSTMETA_ASSOCIATED_TAXONOMY, $taxonomy_slug );
		$this->execute_group_updated_action();
	}


	/**
	 * Update the set of taxonomies associated with this field group.
	 *
	 * @param string[] $taxonomy_slugs Array of (sanitized) taxonomy slugs.
	 */
	public function update_associated_taxonomies( $taxonomy_slugs ) {
		$current_taxonomies = $this->get_associated_taxonomies();

		// Remove taxonomies that are associated but shouldn't be.
		$to_remove = array_diff( $current_taxonomies, $taxonomy_slugs );
		foreach( $to_remove as $taxonomy_slug ) {
			$this->remove_associated_taxonomy( $taxonomy_slug );
		}

		// Add taxonomies that aren't associated but should be.
		$to_add = array_diff( $taxonomy_slugs, $current_taxonomies );
		foreach( $to_add as $taxonomy_slug ) {
			$this->add_associated_taxonomy( $taxonomy_slug );
		}
	}

}