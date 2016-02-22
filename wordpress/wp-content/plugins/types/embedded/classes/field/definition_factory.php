<?php

/**
 * Abstract factory for field definitions.
 *
 * Handles creation of the objects as well as their caching.
 */
abstract class WPCF_Field_Definition_Factory {

	/**
	 * Singleton parent.
	 *
	 * @link http://stackoverflow.com/questions/3126130/extending-singletons-in-php
	 * @return WPCF_Field_Definition_Factory Instance of calling class.
	 */
	final public static function get_instance() {
		static $instances = array();
		$called_class = get_called_class();
		if( !isset( $instances[ $called_class ] ) ) {
			$instances[ $called_class ] = new $called_class();
		}
		return $instances[ $called_class ];
	}


	final private function __construct() { }


	final private function __clone() { }


	/**
	 * @return string Name of the option that should be used to obtain field definitions.
	 */
	protected abstract function get_option_name();


	/**
	 * @return string Name of the class that should be instantiated as a field definition. It MUST inherit from
	 * WPCF_Field_Definition.
	 */
	protected abstract function get_class_name();


	/**
	 * @var array Existing instances of field definitions indexed by field slugs.
	 */
	private $field_definitions = array();


	/**
	 * Load an existing field definition.
	 *
	 * For now, we're using legacy code to read fields from the options table.
	 *
	 * @param string $field_key Key used to store the field configuration in options, or field slug (which should be
	 * equal to the key).
	 * @return null|WPCF_Field_Definition Field definition or null if it can't be loaded.
	 */
	final public function load_field_definition( $field_key ) {

		if( !is_string( $field_key ) || empty( $field_key ) ) {
			return null;
		}

		// Can we use cached version?
		if( !in_array( $field_key, $this->field_definitions ) ) {

			// Get all field definitions for the option name we're using. No performance worries, it uses caching.
			$fields_from_options = $this->get_fields_from_options();
			$field_configuration = null;
			if( in_array( $field_key, array_keys( $fields_from_options ) ) ) {
				$field_configuration = $fields_from_options[ $field_key ];
			} else {

				// Theoretically, the field key may differ from the field slug (we have no invariants defined anywhere).
				// We can search the array and look for slugs.
				foreach( $fields_from_options as $field_from_options ) {
					if( wpcf_getarr( $field_from_options, 'slug' ) == $field_key ) {
						$field_configuration = $fields_from_options;
						break;
					}
				}

				if( null == $field_configuration ) {
					// No such field is defined.
					return null;
				}
			}

			// Prepare the field type information, fail if we can't.
			$field_type_slug = wpcf_getarr( $field_configuration, 'type', null );
			$field_type = WPCF_Field_Type_Definition_Factory::load( $field_type_slug );
			if( null == $field_type ) {
				return null;
			}

			// Create the object and save it to cache.
			try {
				$class_name = $this->get_class_name();
				/** @var WPCF_Field_Definition $field_definition */
				$field_definition = new $class_name( $field_type, $field_configuration );
			} catch( Exception $e ) {
				return null;
			}

			$this->field_definitions[ $field_key ] = $field_definition;
		}

		return $this->field_definitions[ $field_key ];
	}


	/**
	 * Note: Consider using the builder pattern.
	 */
	/*final protected function create_field_definition( ) {

	}*/


	/**
	 * @return array Raw field definition data from the options.
	 */
	private function get_fields_from_options() {
		return wpcf_admin_fields_get_fields( false, false, false, $this->get_option_name() );
	}


	/**
	 * Removes a single field definition from the storage of existing instances.
	 *
	 * It also completely clears the cache of the (legacy) wpcf_admin_fields_get_fields.
	 * Note that this method is public only temporarily and that this is not a mere cache clearing.
	 *
	 * @param string|null $field_slug If null, the cache will be emptied completely.
	 */
	public function clear_definition_storage( $field_slug = null ) {
		if( null == $field_slug ) {
			$this->field_definitions = array();
		} else {
			unset( $this->field_definitions[ $field_slug ] );
		}

		wpcf_admin_fields_get_fields( false, false, false, $this->get_option_name(), false, true );
	}


	/**
	 * Completely erase field definition from options and clear cache.
	 *
	 * @param string $field_slug
	 */
	private function erase_field_definition_from_options( $field_slug ) {

		$fields_from_options = $this->get_fields_from_options();
		unset( $fields_from_options[ $field_slug ] );
		wpcf_admin_fields_save_fields( $fields_from_options, true, $this->get_option_name() );

		$this->clear_definition_storage( $field_slug );
	}


	/**
	 * @return string[] Slugs of fields that have a definition in Types.
	 */
	private function get_types_field_slugs() {
		$fields_from_options = $this->get_fields_from_options();
		$field_slugs = array();
		foreach ( $fields_from_options as $field_configuration ) {
			$slug = wpcf_getarr( $field_configuration, 'slug' );
			if( !empty( $slug ) ) {
				$field_slugs[] = $slug;
			}
		}
		return array_unique( $field_slugs );
	}


	/**
	 * @param string $meta_key
	 * @return bool True if there exists any Types field definition (within the domain) that uses this key.
	 */
	private function meta_key_belongs_to_types_field( $meta_key ) {
		$field_definitions = $this->load_types_field_definitions();
		foreach( $field_definitions as $field_definition ) {
			if( $field_definition->get_meta_key() == $meta_key ) {
				return true;
			}
		}
		return false;
	}


	/**
	 * @return WPCF_Field_Definition[] All existing Types field definitions.
	 */
	private function load_types_field_definitions() {
		$field_slugs = $this->get_types_field_slugs();
		$field_definitions = array();

		foreach ( $field_slugs as $slug ) {
			$field_definition = $this->load_field_definition( $slug );
			if ( null != $field_definition ) {
				$field_definitions[] = $field_definition;
			}
		}

		return $field_definitions;
	}


	/**
	 * @return string[] All meta keys that occur in the database (within the domain).
	 */
	protected abstract function get_existing_meta_keys();


	/**
	 * @var null|WPCF_Field_Definition_Generic[] Cache.
	 */
	private $generic_field_definitions = null;


	/**
	 * @return WPCF_Field_Definition_Generic[] Definitions of all generic fields that exist in the database within
	 *     current domain.
	 */
	public function load_generic_field_definitions() {

		if( null == $this->generic_field_definitions ) {
			$existing_meta_keys = $this->get_existing_meta_keys();

			$results = array();
			foreach( $existing_meta_keys as $meta_key ) {

				if( $this->meta_key_belongs_to_types_field( $meta_key ) ) {
					continue;
				}

				$results[] = new WPCF_Field_Definition_Generic( $meta_key );
			}

			$this->generic_field_definitions = $results;

		}

		return $this->generic_field_definitions;
	}


	/**
	 * @return WPCF_Field_Definition_Abstract[] All field definitions (generic and Types-controlled).
	 */
	public function load_all_definitions() {
		return array_merge( $this->load_types_field_definitions(), $this->load_generic_field_definitions() );
	}


	/**
	 * Reorder an array of field definitions.
	 *
	 * @param WPCF_Field_Definition_Abstract[] $definitions
	 * @param string $orderby 'name'|'slug'|'is_under_types_control'|'field_type'
	 * @param string $order 'asc'|'desc'
	 *
	 * @return WPCF_Field_Definition_Abstract[] Reordered array.
	 */
	public function order_definitions( $definitions, $orderby = 'name', $order = 'asc' ) {

		$orderby_to_comparison_function = array(
			'name' => 'compare_definitions_by_name',
			'slug' => 'compare_definitions_by_slug',
			'is_under_types_control' => 'compare_definition_by_types_control',
			'field_type' => 'compare_definitions_by_field_type'
		);

		usort( $definitions, array( $this, wpcf_getarr( $orderby_to_comparison_function, $orderby, 'compare_definitions_by_name' ) ) );

		if( 'desc' == $order ) {
			$definitions = array_reverse( $definitions );
		}

		return $definitions;
	}


	/**
	 * Compare function for ordering by name in order_definitions().
	 *
	 * @param $first WPCF_Field_Definition_Abstract
	 * @param $second WPCF_Field_Definition_Abstract
	 *
	 * @return int
	 */
	public function compare_definitions_by_name( $first, $second ) {
		return strcoll( strtolower( $first->get_name() ), strtolower( $second->get_name() ) );
	}


	/**
	 * Compare function for ordering by slug in order_definitions().
	 *
	 * @param $first WPCF_Field_Definition_Abstract
	 * @param $second WPCF_Field_Definition_Abstract
	 *
	 * @return int
	 */
	public function compare_definitions_by_slug( $first, $second ) {
		return strcmp( $first->get_slug(), $second->get_slug() );
	}


	/**
	 * Compare function for ordering by the Types control status in order_definitions().
	 *
	 * @param $first WPCF_Field_Definition_Abstract
	 * @param $second WPCF_Field_Definition_Abstract
	 *
	 * @return int
	 */
	public function compare_definition_by_types_control( $first, $second ) {
		if( $first->is_under_types_control() == $second->is_under_types_control() ) {
			return 0;
		} else {
			return $first->is_under_types_control() ? 1 : -1;
		}
	}


	/**
	 * Compare function for ordering by field type in order_definitions().
	 *
	 * @param $first WPCF_Field_Definition_Abstract
	 * @param $second WPCF_Field_Definition_Abstract
	 *
	 * @return int
	 */
	public function compare_definitions_by_field_type( $first, $second ) {
		if( $first->is_under_types_control() == $second->is_under_types_control() ) {
			if( $first->is_under_types_control() ) {
				// Both are under Types control, compare their field types
				/** @var WPCF_Field_Definition $first_t */
				$first_t = $first;
				/** @var WPCF_Field_Definition $second_t */
				$second_t = $second;
				return strcmp( $first_t->get_type()->get_slug(), $second_t->get_type()->get_slug() );
			} else {
				// None are under Types control
				return 0;
			}
		} else {
			// The one that is under Types control wins.
			return $first->is_under_types_control() ? -1 : 1;
		}
	}


	/**
	 * Query field definitions.
	 *
	 * @param array $args Following arguments are recognized:
	 *     - filter: What field definitions should be retrieved: 'types'|'generic'|'all'
	 *     - orderby: 'name'|'slug'|'is_under_types_control'|'field_type'
	 *     - order: 'asc'|'desc'
	 *     - search: String for fulltext search.
	 *
	 * @return WPCF_Field_Definition_Abstract[] Field definitions that match query arguments.
	 */
	public function query_definitions( $args ) {

		$args = wp_parse_args( $args,  array('filter' => 'all') );

		// Get only certain type of field definitions (generic, Types or both)
		switch( $args['filter'] ) {
			case 'types':
				$results = $this->load_types_field_definitions();
				break;
			case 'generic':
				$results = $this->load_generic_field_definitions();
				break;
			case 'all':
				$results = $this->load_all_definitions();
				break;
			default:
				$results = array();
				break;
		}

		// Perform fulltext search if needed
		$search_string = wpcf_getarr( $args, 'search', '' );

		if( !empty( $search_string ) ) {
			$matches = array();
			foreach( $results as $definition ) {
				if( $definition->is_match( $search_string ) ) {
					$matches[] = $definition;
				}
			}
			$results = $matches;
		}

		// Sort results
		$orderby = wpcf_getarr( $args, 'orderby', 'name' );
		$order = wpcf_getarr( $args, 'order', 'asc', array( 'asc', 'desc' ) );

		$results = $this->order_definitions( $results, $orderby, $order );

		return $results;
	}


	/**
	 * Permanently delete field definition.
	 *
	 * That means:
	 * - remove it from all field groups,
	 * - delete field data from the database (sic!) and
	 * - delete the definition itself.
	 *
	 * After calling this method, the field definition object passed as parameter should never be used again.
	 *
	 * @param WPCF_Field_Definition $field_definiton
	 * @return bool
	 */
	public function delete_definition( $field_definiton ) {

		// We accept only fields that are under Types control
		if( ! $field_definiton instanceof WPCF_Field_Definition ) {
			return false;
		}

		if( ! $field_definiton->is_under_types_control() ) {
			return false;
		}

		// Remove field from all groups
		$associated_groups = $field_definiton->get_associated_groups();
		foreach( $associated_groups as $group ) {
			$group->remove_field_definition( $field_definiton );
		}

		// Delete field data
		$is_success = $field_definiton->delete_all_fields();

		// Delete the definition
		$slug_to_delete = $field_definiton->get_slug();
		$this->erase_field_definition_from_options( $slug_to_delete );

		return $is_success;

	}


}