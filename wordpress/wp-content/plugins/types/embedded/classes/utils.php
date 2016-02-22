<?php

/**
 * Class of helper functions that don't fit anywhere else.
 */
final class WPCF_Utils {


	/**
	 * Shortcut method for retrieving public built-in taxonomies.
	 *
	 * @param string $output_mode 'objects'|'names'
	 *
	 * @return object[] Array of taxonomy objects or names.
	 */
	static function get_builtin_taxonomies( $output_mode = 'objects' ) {
		// todo add simple caching
		return get_taxonomies( array( 'public' => true, '_builtin' => true ), $output_mode );
	}


	/**
	 * Get a definitive set of all taxonomies recognized by Types.
	 *
	 * Respects if some builtin taxonomy is overridden by Types.
	 *
	 * @return array
	 */
	static function get_all_taxonomies() {
		// todo add simple caching
		$taxonomies = array();

		// Read custom taxonomies first.
		$custom_taxonomies = get_option( WPCF_OPTION_NAME_CUSTOM_TAXONOMIES, array() );
		if ( is_array( $custom_taxonomies ) ) {
			foreach ( $custom_taxonomies as $slug => $data ) {
				$taxonomies[ $slug ] = $data;
			}
		}

		// Get built-in taxonomies and add them to the set, but avoid overwriting custom taxonomies
		$builtin_taxonomies = self::object_to_array_deep( self::get_builtin_taxonomies() );
		foreach ( $builtin_taxonomies as $slug => $data ) {
			// check if built-in taxonomies are already saved as custom taxonomies
			if ( isset( $taxonomies[ $slug ] ) ) {
				continue;
			}

			if ( ! isset( $data['slug'] ) ) {
				$data['slug'] = $slug;
			}

			$taxonomies[ $slug ] = $data;
		}

		return $taxonomies;
	}


	/**
	 * Transform an object and all it's fields recursively into an associative array. If any object's field is
	 * an array, individual elements of the array will be transformed as well.
	 *
	 * @param object|array $object The object or array of objects to transform.
	 * @return array
	 */
	static function object_to_array_deep( $object ) {
		if ( is_array( $object ) || is_object( $object ) ) {
			$result = array();
			foreach ( $object as $key => $value ) {
				$result[ $key ] = self::object_to_array_deep( $value );
			}

			return $result;
		}

		return $object;
	}


	/**
	 * Try to convert a taxonomy slug to a label.
	 *
	 * @param string $slug Taxonomy slug.
	 * @param string $label_name One of the available labels of the taxonomy.
	 *
	 * @link https://codex.wordpress.org/Function_Reference/get_taxonomies Taxonomy object description.
	 *
	 * @return string Selected taxonomy label or slug if the label was not found.
	 */
	static function taxonomy_slug_to_label( $slug, $label_name = 'name' ) {
		$all_taxonomies = self::get_all_taxonomies();

		$taxonomy_display_name = wpcf_getnest( $all_taxonomies, array( $slug, 'labels', $label_name ), $slug );

		return $taxonomy_display_name;
	}


	/**
	 * Check if searched string is a substring of the value.
	 *
	 * @param string $search_string
	 * @param string $value
	 * @return bool
	 */
	static function is_string_match( $search_string, $value ) {
		return ( false !== strpos( mb_strtolower( $value ), mb_strtolower( trim( $search_string ) ) ) );
	}

}