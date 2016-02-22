<?php

/**
 * Abstract representation of a field group (without defined type).
 *
 * Descendants of this class allways must be instantiated through appropriate factory class inheriting
 * from WPCF_Field_Group_Factory.
 *
 * Any method that permanently changes any group data needs to call execute_group_updated_action() afterwards.
 */
abstract class WPCF_Field_Group {

	/**
	 * Stores the slugs of the fields that belong to this group, as a comma separated list.
	 */
	const POSTMETA_FIELD_SLUGS_LIST = '_wp_types_group_fields';


	const FIELD_SLUG_DELIMITER = ',';


	/**
	 * @var WP_Post Post object that represents the field group.
	 */
	private $post;


	/**
	 * WPCF_Field_Group constructor.
	 *
	 * Note that it is protected - inheriting classes have to implement an override that should, additionally, check
	 * if the post has correct type.
	 *
	 * @param WP_Post $field_group_post Post object that represents the field group.
	 * @throws InvalidArgumentException
	 */
	protected function __construct( $field_group_post ) {
		if( ! $field_group_post instanceof WP_Post ) {
			throw new InvalidArgumentException( 'First argument is not a post.' );
		}
		$this->post = $field_group_post;
	}


	/**
	 * @return WPCF_Field_Definition_Factory Field definition factory of the correct type.
	 */
	protected abstract function get_field_definition_factory();


	/**
	 * @return WP_Post Post object representing the field group.
	 */
	protected function get_post() {
		return $this->post;
	}


	/**
	 * @return int ID of the field group.
	 */
	public function get_id() {
		return $this->get_post()->ID;
	}


	/**
	 * @return string Unique name (post slug) of the field group.
	 */
	public function get_slug() {
		return sanitize_title( $this->get_post()->post_name );
	}


	/**
	 * @return string Field group description. Sanitized as a text field.
	 */
	public function get_description() {
		return sanitize_text_field( $this->get_post()->post_content );
	}


	/**
	 * Note that for displaying group name to the user you should use get_display_name() instead.
	 *
	 * @return string Field group title.
	 */
	public function get_name() {
		return sanitize_text_field( $this->get_post()->post_title );
	}


	/**
	 * Get group name as it should be displayed to the user.
	 *
	 * Handles string translation if applicable.
	 */
	public function get_display_name() {
		return wpcf_translate(
			sprintf( 'group %d name', $this->get_id() ),
			$this->get_name()
		);
	}


	/**
	 * @return bool True if the field group is active, false if deactivated.
	 */
	public function is_active() {
		return ( $this->get_post()->post_status == 'publish' ? true : false );
	}


	/**
	 * @return string Status of the underlying post. Limited to 'publish'|'draft' (default).
	 */
	public function get_post_status() {
		return ( $this->is_active() ? 'publish' : 'draft' );
	}


	/**
	 * @return int ID of the user who edited the field group last.
	 */
	public function get_author() {
		return (int) $this->get_post()->post_author;
	}


	/*
	 * $group['meta_box_context'] = 'normal';
    $group['meta_box_priority'] = 'high';
    $group['filters_association'] = get_post_meta( $post->ID, '_wp_types_group_filters_association', true );
	 */


	/**
	 * Change name of the field group.
	 *
	 * Do not confuse with the title. *All* changes of the name must happen through this method, otherwise
	 * unexpected behaviour of the WPCF_Field_Group_Factory can occur.
	 *
	 * @param string $value New value of the post name. Note that it may be further modified by WordPress before saving.
	 */
	public function set_name( $value ) {
		$updated_post_id = wp_update_post(
			array( 'ID' => $this->get_id(), 'post_name' => sanitize_title( $value ) )
		);
		if( 0 !== $updated_post_id ) {
			// Refresh the post object and notify about renaming field group.
			$this->post = WP_Post::get_instance( $updated_post_id );
			do_action( 'wpcf_field_group_renamed', $value, $this );
			$this->execute_group_updated_action();
		}
	}


	/**
	 * @return string The underlying post type of the post representing the field group.
	 */
	public function get_post_type() {
		return $this->post->post_type;
	}


	/**
	 * @var null|array Array of field slugs that belong to this field group, or null if it was not loaded from database yet.
	 */
	private $field_slugs = null;


	/**
	 * Check if a field slug is valid and can be stored in the database.
	 *
	 * @param string $field_slug
	 * @return bool True if the slug is valid.
	 */
	public function is_valid_field_slug( $field_slug ) {
		return !empty( $field_slug ) && is_string( $field_slug ) && ( sanitize_title( $field_slug ) == $field_slug );
	}


	/**
	 * Check if an array of field slugs is valid.
	 *
	 * @param array|mixed $field_slugs
	 * @return bool True if an array of valid field slugs was provided.
	 */
	protected function validate_field_slugs( $field_slugs ) {
		if( !is_array( $field_slugs ) ) {
			return false;
		}

		// Every element needs to be valid
		if( count( $field_slugs ) != count ( array_filter( $field_slugs, array( $this, 'is_valid_field_slug' ) ) ) ) {
			return false;
		}

		return true;
	}


	/**
	 * @return array Slugs of fields that belong to this field group, or empty array when database doesn't contain
	 * valid data.
	 *
	 * Consider using get_field_definitions() instead.
	 */
	public function get_field_slugs() {
		if( null == $this->field_slugs ) {
			$field_slug_list = get_post_meta( $this->get_id(), self::POSTMETA_FIELD_SLUGS_LIST, true );
			$field_slugs = explode( self::FIELD_SLUG_DELIMITER, $field_slug_list );

			// Remove empty slugs
			foreach( $field_slugs as $key => $slug ) {
				if( empty( $slug ) ) {
					unset( $field_slugs[ $key ] );
				}
			}

			$this->field_slugs = $this->validate_field_slugs( $field_slugs ) ? $field_slugs : array();
		}
		return $this->field_slugs;
	}


	/**
	 * Get all existing definitions of fields that belong to this group.
	 *
	 * @return WPCF_Field_Definition[]
	 */
	public function get_field_definitions() {
		$slugs = $this->get_field_slugs();
		$field_definitions = array();
		$factory = $this->get_field_definition_factory();
		foreach( $slugs as $slug ) {
			$field_definition = $factory->load_field_definition( $slug );
			if( null != $field_definition ) {
				$field_definitions[] = $field_definition;
			}
		}
		return $field_definitions;
	}


	/**
	 * Update the set of field slugs that belong to this field group.
	 *
	 * @param array $field_slugs Array of valid field slugs.
	 * @return bool True if the database was updated successfully, false otherwise.
	 */
	protected function set_field_slugs( $field_slugs ) {
		if( !$this->validate_field_slugs( $field_slugs ) ) {
			return false;
		}

		$this->field_slugs = $field_slugs;

		$field_slug_list = implode( self::FIELD_SLUG_DELIMITER, $field_slugs );

		$updated = update_post_meta( $this->get_id(), self::POSTMETA_FIELD_SLUGS_LIST, $field_slug_list );

		$this->execute_group_updated_action();

		return ( $updated != false );
	}


	/**
	 * Remove field definition from this group.
	 *
	 * @param WPCF_Field_Definition $field_definition
	 * @return bool
	 */
	public function remove_field_definition( $field_definition ) {

		$field_slugs = $this->get_field_slugs();

		$slug_to_remove = $field_definition->get_slug();
		$key = array_search( $slug_to_remove, $field_slugs );
		if( $key !== false ) {
			unset( $field_slugs[ $key ] );
		}

		$is_success = $this->set_field_slugs( $field_slugs );

		return $is_success;
	}


	/**
	 * Check if a string is contained within the field group definition.
	 *
	 * Searches in ID, slug, title and description. Case insensitive.
	 *
	 * @param string $search_string String to look for.
	 * @return bool True if found.
	 */
	public function is_match( $search_string ) {
		return (
			WPCF_Utils::is_string_match( $search_string, $this->get_id() )
			|| WPCF_Utils::is_string_match( $search_string, $this->get_slug() )
			|| WPCF_Utils::is_string_match( $search_string, $this->get_name() )
			|| WPCF_Utils::is_string_match( $search_string, $this->get_display_name() )
			|| WPCF_Utils::is_string_match( $search_string, $this->get_description() )
		);
	}


	public function contains_field_definition( $field_definition ) {
		if( $field_definition instanceof WPCF_Field_Definition ) {
			return in_array( $field_definition, $this->get_field_definitions() );
		} elseif( is_string( $field_definition ) ) {
			return in_array( $field_definition, $this->get_field_slugs() );
		} else {
			return false;
		}
	}


	/**
	 * Execute the wpcf_group_updated action.
	 *
	 * Needs to be called after each (permanent) change to a group.
	 */
	public function execute_group_updated_action() {

		/**
		 * Executed after a group has been updated in the database.
		 *
		 * @param int $id Group ID.
		 * @param WPCF_Field_Group $group The group object.
		 * @since 1.9
		 */
		do_action( 'wpcf_group_updated', $this->get_id(), $this );
	}

}