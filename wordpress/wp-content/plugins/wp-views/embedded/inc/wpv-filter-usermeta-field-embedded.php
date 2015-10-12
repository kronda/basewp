<?php

/**
* wpv_users_query_usermeta_filters
*
* Filter hooked before query and user meta fields
*
* @param $args array of arguments to be passed to WP_User_Query
* 
* @param $view_settings
*
* @return $args
*
* @since 1.6.2
*/

add_filter( 'wpv_filter_user_query', 'wpv_users_query_usermeta_filters', 70, 2 );

function wpv_users_query_usermeta_filters( $args, $view_settings ) {
	
	global $WP_Views, $no_parameter_found;
	
	$usermeta_queries = array();
	$view_id = $WP_Views->get_current_view();
	foreach ( $view_settings as $index => $value ) {
		if ( preg_match( "/usermeta-field-(.*)_type/", $index, $match ) ) {
			$field = $match[1];
			$type = $value;
			$compare = $view_settings['usermeta-field-' . $field . '_compare'];
			$value = $view_settings['usermeta-field-' . $field . '_value'];
			
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
			* Filter wpv_filter_usermeta_field_filter_type
			*
			* @param $type the type coming from the View settings filter: <CHAR>, <NUMERIC>, <BINARY>, <DATE>, <DATETIME>, <DECIMAL>, <SIGNED>, <TIME>, <UNSIGNED>
			* @param $field the key of the usermeta field being used to filter by
			* @param $view_id the ID of the View being displayed
			*
			* @since 1.8.0
			*/
			
			$type = apply_filters( 'wpv_filter_usermeta_field_filter_type', $type, $field, $view_id );
			
			$has_meta_query = wpv_resolve_meta_query( $field, $value, $type, $compare );
			if ( $has_meta_query ) {
				$usermeta_queries[] = $has_meta_query;
			}
		}
	}
	//Set usermeta relation
    if ( count( $usermeta_queries ) ) {
		$args['meta_query'] = $usermeta_queries;
        $args['meta_query']['relation'] = isset( $view_settings['usermeta_fields_relationship'] ) ? $view_settings['usermeta_fields_relationship'] : 'AND';
    }
	
	return $args;
}

/**
* wpv_filter_usermeta_field_requires_framework_values
*
* Whether the current View requires framework data for the filter by usermeta fields
*
* @param $state (boolean) the state of this need until this filter is applied
* @param $view_settings
*
* @return $state (boolean)
*
* @since 1.10
*/

add_filter( 'wpv_filter_requires_framework_values', 'wpv_filter_usermeta_field_requires_framework_values', 20, 2 );

function wpv_filter_usermeta_field_requires_framework_values( $state, $view_settings ) {
	if ( $state ) {
		return $state;
	}
	if ( $view_settings['query_type'][0] == 'users' ) {
		foreach ( $view_settings as $key => $value ) {
			if ( 
				preg_match( "/usermeta-field-(.*)_value/", $key, $res )
				&& preg_match( "/FRAME_KEY\(([^\)]+)\)/", $value, $shortcode ) 
			) {
				$state = true;
				break;
			}
		}
	}
	return $state;
}
