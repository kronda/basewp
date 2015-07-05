<?php

/*
  Modify the query to include filtering by custom_field.
  
*/

add_filter( 'wpv_filter_query', 'wpv_filter_post_custom_field', 10, 2 );

function wpv_filter_post_custom_field( $query, $view_settings ) {

	global $WP_Views, $no_parameter_found;
	
	$meta_keys = array();
	$meta_queries = array();
	$view_id = $WP_Views->get_current_view();
	foreach ( array_keys( $view_settings ) as $key ) {
		if ( 
			strpos( $key, 'custom-field-' ) === 0 
			&& strpos( $key, '_compare' ) === strlen( $key ) - strlen( '_compare' )
		) {
			if ( empty( $meta_keys ) ) {
				$meta_keys = $WP_Views->get_meta_keys();
			}
			$name = substr( $key, 0, strlen( $key ) - strlen( '_compare' ) );
			$name = substr( $name, strlen( 'custom-field-' ) );
			$meta_name = $name;
			if ( ! in_array( $meta_name, $meta_keys ) ) { // this is needed for fields with keys containing spaces - we map those spaces to underscores when creating the filter
				$meta_name = str_replace( '_', ' ', $meta_name );
			}
			
			// TODO add filter here: what happens when a meta_name contains a space AND an underscore?
			// We need a final solution, I prefer to use a %%SPACE%% placeholder and avoid the above mapping (which we should keepfor backwards compatibility)

			$value = $view_settings['custom-field-' . $name . '_value'];
			
			/**
			* Filter wpv_filter_custom_field_filter_original_value
			*
			* @param $value the value coming from the View settings filter after passing through the check for URL params, shortcode attributes and date functions comparison
			* @param $meta_name the key of the custom field being used to filter by
			* @param $view_id the ID of the View being displayed
			*
			* $value comes from the View settings. It's a string containing a single-value or a comma-separated list of single-values if the filter needs more than one value (for IN, NOT IN, BETWEEN and NOT BETWEEN comparisons)
			* Each individual single-value element in the list can use any of the following formats:
			* (string|numeric) if the single-value item is fixed
			* (string) URL_PARAM(parameter) if the filter is done via a URL param "parameter"
			* (string) VIEW_PARAM(parameter) if the filter is done via a [wpv-view] shortcode attribute "parameter"
			* (string) NOW() | TODAY() | FUTURE_DAY() | PAST_DAY() | THIS_MONTH() | FUTURE_MONTH() | PAST_MONTH() | THIS_YEAR() | FUTURE_YEAR() | PAST_YEAR() | SECONDS_FROM_NOW() | MONTHS_FROM_NOW() | YEARS_FROM_NOW() | DATE()
			*
			* @since 1.4.0
			*/
			
			$value = apply_filters( 'wpv_filter_custom_field_filter_original_value', $value, $meta_name, $view_id );
			
			/**
			* Filter wpv_resolve_variable_values
			*
			* @param $value the value coming from the View settings filter after passing through the check for URL params, shortcode attributes and date functions comparison
			* @param $resolve_attr Array containing the filters that need to be applied as resolvers
			*
			* @since 1.8.0
			*/
			
			$resolve_attr = array(
				'filters' => array( 'url_parameter', 'shortcode_attribute', 'date_timestamp', 'framework_value' )
			);
			$value = apply_filters( 'wpv_resolve_variable_values', $value, $resolve_attr );

			/**
			* Filter wpv_filter_custom_field_filter_processed_value
			*
			* @param $value the value coming from the View settings filter after passing through the check for URL params, shortcode attributes and date functions comparison
			* @param $meta_name the key of the custom field being used to filter by
			* @param $view_id the ID of the View being displayed
			*
			* @since 1.4.0
			*/
			
			$value = apply_filters( 'wpv_filter_custom_field_filter_processed_value', $value, $meta_name, $view_id );
			
			$type = $view_settings['custom-field-' . $name . '_type'];
			
			/**
			* Filter wpv_filter_custom_field_filter_type
			*
			* @param $type the type coming from the View settings filter: <CHAR>, <NUMERIC>, <BINARY>, <DATE>, <DATETIME>, <DECIMAL>, <SIGNED>, <TIME>, <UNSIGNED>
			* @param $meta_name the key of the custom field being used to filter by
			* @param $view_id the ID of the View being displayed
			*
			* @since 1.6.0
			*/
			
			$type = apply_filters( 'wpv_filter_custom_field_filter_type', $type, $meta_name, $view_id );
			
			$compare = $view_settings['custom-field-' . $name . '_compare'];
			
			$has_meta_query = wpv_resolve_meta_query( $meta_name, $value, $type, $compare );
			if ( $has_meta_query ) {
				$meta_queries[] = $has_meta_query;
			}
		}
	}
	
	//Set usermeta relation
    if ( count( $meta_queries ) ) {
		$query['meta_query'] = $meta_queries;
        $query['meta_query']['relation'] = isset( $view_settings['custom_fields_relationship'] ) ? $view_settings['custom_fields_relationship'] : 'AND';
    }

    return $query;
}

function wpv_get_custom_field_view_params($view_settings) {
    $pattern = '/VIEW_PARAM\(([^(]*?)\)/siU';

	$results = array();
	
	foreach (array_keys($view_settings) as $key) {
		if (strpos($key, 'custom-field-') === 0 && strpos($key, '_compare') === strlen($key) - strlen('_compare')) {
			$name = substr($key, 0, strlen($key) - strlen('_compare'));
			$name = substr($name, strlen('custom-field-'));
			
			$value = $view_settings['custom-field-' . $name . '_value'];
			
    
		    if(preg_match_all($pattern, $value, $matches, PREG_SET_ORDER)) {
		        foreach($matches as $match) {
					$results[] = $match[1];
				}
			}
			
		}
	}
	
	return $results;
}

/**
* wpv_resolve_meta_query
*
* Resolves if a meta_query is indeed needed, for filters by custom field and usermeta field
*
* @param $key (string) The field key
* @param $value (string) The resolved value to filter by
* @param $type (string) The filtering data type
* @param $compare (string) The filtering comparison type
*
* @return (array|boolean) The meta_query instance on success, false otherwise
*
* @since 1.8.0
*/

function wpv_resolve_meta_query( $key, $value, $type, $compare ) {
	global $no_parameter_found;
	$return = false;
	if ( $value == $no_parameter_found ) {
		return false;
	}
	if (
		$compare == 'BETWEEN' 
		|| $compare == 'NOT BETWEEN'
	) {
		// We need to make sure we have values for min and max.
		// If any of the values is missing we will transform into lower-than or greater-than filters
		// TODO: Note that we are not covering the case where min or max is an empty constant value, we might want to review that
		$values = explode( ',', $value );
		$values = array_map( 'trim', $values );
		if ( count( $values ) == 0 ) {
			return false;
		}
		if ( count( $values ) == 1 ) {
			if ( $values[0] == $no_parameter_found ) {
				return false;
			}
			if ( $compare == 'BETWEEN' ) {
				$compare =  '>=';
			} else {
				$compare =  '<=';
			}
			$value = $values[0];
		} else {
			if (
				$values[0] == $no_parameter_found 
				&& $values[1] == $no_parameter_found
			) {
				return false;
			}
			if ( $values[0] == $no_parameter_found ) {
				if ( $compare == 'BETWEEN' ) {
					$compare = '<=';
				} else {
					$compare = '>=';
				}
				$value = $values[1];
			} elseif ( $values[1] == $no_parameter_found ) {
				if ( $compare == 'BETWEEN' ) {
					$compare = '>=';
				} else {
					$compare = '<=';
				}
				$value = $values[0];
			}
		}
	}
	
	// If $value still contains a $no_parameter_found value, no filter should be applied
	// Because it means there is a non-existing or empty URL parameter
	// TODO: on shortcode attributes, an empty value as two commas will pass this test
	// Maybe this is OK, as we might want to filter by an empty value too, which is not possible on filters by URL parameter
	
	if ( strpos( $value, $no_parameter_found ) !== false ) {
		return false;
	}
	
	// Now that we are sure that the filter should be applied, even for empty values, let's do it
	
	if ( 
		$compare == 'IN' 
		|| $compare == 'NOT IN' 
	) {
		// WordPress query expects an array in this case
		$value = explode( ',', $value );
	}
	
	// Sanitization
	if ( is_array( $value ) ) {
		foreach ( $value as $v_key => $val ) {
			$value[$v_key] = stripslashes( rawurldecode( sanitize_text_field( trim( $val ) ) ) );
		}
	} else {
		$value = stripslashes( rawurldecode( sanitize_text_field( trim( $value ) ) ) );
	}
	
	if ( 
		in_array( $compare, array( '>=', '<=', '>', '<' ) )
		&& (
			empty( $value ) 
			&& ! is_numeric( $value ) 
		)
	) {
		// do nothing as we are comparing greater than / lower than to an empty value
		return false;
	} else {
		$return = array(
			'key' => $key,
			'value' => $value,
			'type' => $type,
			'compare' => $compare
		);
	}
	
	return $return;
}

