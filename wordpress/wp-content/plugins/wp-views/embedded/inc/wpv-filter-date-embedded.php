<?php

/**
* Post date filter embedded
*
* @package Views
*
* @since 1.8.0
*/

add_filter( 'wpv_filter_query', 'wpv_filter_post_date', 50, 2 );

/**
* wpv_filter_post_date
*
* Builds the date_query argument for WP_Query based on $view_settings values
*
* @param $query (array)
* @param $view_Settings (array)
*
* @return $query (array)
*
* @since 1.8.0
*/

function wpv_filter_post_date( $query, $view_settings ) {
	if ( 
		isset( $view_settings['date_filter'] ) 
		&& is_array( $view_settings['date_filter'] ) 
	) {
		// @todo add a filter here for date conditions
		if (
			isset( $view_settings['date_filter']['date_conditions'] ) 
			&& is_array( $view_settings['date_filter']['date_conditions'] ) 
		) {
			$date_query = array();
			$date_operator = array(
				'single' => array( '=', '!=', '<', '<=', '>', '>=' ),
				'group' => array( 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' ),
			);
			$date_options = array( 'year', 'month', 'week', 'day', 'dayofyear', 'dayofweek', 'hour', 'minute', 'second' );
			$date_columns = array( 'post_date', 'post_date_gmt', 'post_modified', 'post_modified_gmt' );
			$date_relation = 'AND';
			if (
				isset( $view_settings['date_filter']['date_relation'] ) 
				&& in_array( $view_settings['date_filter']['date_relation'], array( 'OR', 'AND' ) ) 
			) {
				$date_relation = $view_settings['date_filter']['date_relation'];
			}
			$start_of_week = get_option( 'start_of_week' );
			foreach ( $view_settings['date_filter']['date_conditions'] as $date_condition ) {
				if ( 
					is_array( $date_condition ) 
					&& isset( $date_condition['date_operator'] )
				) {
					$date_query_instance = array();
					if ( in_array( $date_condition['date_operator'], $date_operator['single'] ) ) {
						$are_date_opt_valid = true;
						foreach ( $date_options as $date_opt ) {
							if (
								isset( $date_condition[$date_opt] )
							) {
								// Translate URL_PARAM, VIEW_PARAM and date functions into values
								$value = $date_condition[$date_opt];
								$resolve_attr = array(
									'filters' => array( 'date_integer', 'url_parameter', 'shortcode_attribute' ),
									'date_integer_date_type' => $date_opt
								);
								$value = apply_filters( 'wpv_resolve_variable_values', $value, $resolve_attr );
								$value = explode( ',', $value );
								$value = array_map( 'trim', $value );
								$value = array_filter( $value, 'wpv_is_valid_non_empty_value_to_filter' );
								if ( ! empty( $value ) ) {
									$value_real = reset( $value );
									if ( wpv_integer_date_validate( $value_real, $date_opt ) !== false ) {
										if ( 
											$date_opt == 'dayofweek' 
											&& $start_of_week == 1
										) {
											/*
											* Based on the $start_of_week setting, $value_real is 1 ( Monday ) to 7 ( Sunday )
											* We must translate it to values that the date_query['dayofweek'] attribute understands
											* That is, 1 ( Sunday ) to 7 ( Saturday )
											*/
											$value_real = ( $value_real % 7 ) + 1;
										}
										$date_query_instance[$date_opt] = $value_real;
									} else {
										$are_date_opt_valid = false;
									}
								}
							}
						}
						if ( 
							! empty( $date_query_instance ) 
							&& $are_date_opt_valid
						) {
							$date_query_instance['compare'] = $date_condition['date_operator'];
							if (
								isset( $date_condition['date_column'] )
								&& in_array( $date_condition['date_column'], $date_columns )
							) {
								$date_query_instance['column'] = $date_condition['date_column'];
							}
							$date_query[] = $date_query_instance;
						}
					} else if ( 
						in_array( $date_condition['date_operator'], $date_operator['group'] ) 
						&& isset( $date_condition['date_multiple_selected'] )
						&& in_array( $date_condition['date_multiple_selected'], $date_options )
						&& isset( $date_condition[$date_condition['date_multiple_selected']] )
						&& ! empty( $date_condition[$date_condition['date_multiple_selected']] )
					) {
						$value = $date_condition[$date_condition['date_multiple_selected']];
						// Translate URL_PARAM, VIEW_PARAM and date functions into values
						$resolve_attr = array(
							'filters' => array( 'date_integer', 'url_parameter', 'shortcode_attribute' ),
							'date_integer_date_type' => $date_condition['date_multiple_selected']
						);
						$value = apply_filters( 'wpv_resolve_variable_values', $value, $resolve_attr );
						$value = explode( ',', $value );
						$value = array_map( 'trim', $value );
						$value = array_filter( $value, 'wpv_is_valid_non_empty_value_to_filter' );
						$value = wpv_array_date_validate( $value, $date_condition['date_multiple_selected'] );
						if ( ! empty( $value ) ) {
							$indexed_values = array_values( $value );
							if ( 
								$date_condition['date_multiple_selected'] == 'dayofweek' 
								&& $start_of_week == 1
							) {
								foreach ( $indexed_values as $indexed_k => $indexed_v ) {
									/*
									* Based on the $start_of_week setting, $indexed_v is 1 ( Monday ) to 7 ( Sunday )
									* We must translate it to values that the date_query['dayofweek'] attribute understands
									* That is, 1 ( Sunday ) to 7 ( Saturday )
									*/
									$indexed_values[$indexed_k] = ( $indexed_v % 7 ) + 1;
								}
							}
							$date_query_instance[$date_condition['date_multiple_selected']] = $indexed_values;
							$date_query_instance['compare'] = $date_condition['date_operator'];
							if (
								isset( $date_condition['date_column'] )
								&& in_array( $date_condition['date_column'], $date_columns )
							) {
								$date_query_instance['column'] = $date_condition['date_column'];
							}
							$date_query[] = $date_query_instance;
						}
					}
				}
			}
			if ( ! empty( $date_query ) ) {
				$date_query['relation'] = $date_relation;
				$query['date_query'] = $date_query;
			}
		}
	}
	return $query;
}

/**
* wpv_array_date_validate
*
* Validate each element in an array of values, given a date field type
*
* @param $value (array)
* @param $validate (string)
* 	<year|month|week|day|hour|minute|second|dayofweek|dayofyear>
*
* @return $value (array)
*
* @since 1.8.0
*/

function wpv_array_date_validate( $value, $validate = '' ) {
	if ( 
		! empty( $validate ) 
		&& is_array( $value )
		&& ! empty( $value )
	) {
		switch ( $validate ) {
			case 'year':
			case 'month':
			case 'week':
			case 'day':
			case 'hour':
			case 'minute':
			case 'second':
			case 'dayofweek':
			case 'dayofyear':
				foreach ( $value as $val_key => $val_candidate ) {
					if ( wpv_integer_date_validate( $val_candidate, $validate ) === false ) {
						$value[$val_key] = null;
					}
				}
				break;
		}
		$existing_values = count( $value );
		$value = array_filter( $value, 'wpv_is_valid_non_empty_value_to_filter' );
		$validated_values = count( $value );
		if ( $existing_values != $validated_values ) {
			$value = array();
		}
	}
	return $value;
}

/**
* wpv_integer_date_validate
*
* Validate a value, given a date field type
*
* @param $value (string|integer)
* @param $validate (string)
* 	<year|month|week|day|hour|minute|second|dayofweek|dayofyear>
*
* @return $return (boolean)
*
* @since 1.8.0
*/

function wpv_integer_date_validate( $value, $validate = '' ) {
	$return = false;
	if ( 
		! empty( $validate ) 
		&& ( 
			! empty( $value )
			|| is_numeric( $value )
		)
	) {
		switch ( $validate ) {
			case 'year':
				if ( 
					checkdate( 1, 1, $value ) 
					&& 1000 < intval( $value )
					&& intval( $value ) < 9999
				) {
					$return = true;
				}
				break;
			case 'month':
				if ( checkdate( $value, 1, 2012 ) ) {
					$return = true;
				}
				break;
			case 'week':
				if ( 
					0 <= intval( $value )
					&& intval( $value ) <= 53
				) {
					$return = true;
				}
				break;
			case 'day':
				if ( checkdate( 1, $value, 2012 ) ) {
					$return = true;
				}
				break;
			case 'hour':
				if ( 
					0 <= intval( $value )
					&& intval( $value ) <= 23
				) {
					$return = true;
				}
				break;
			case 'minute':
			case 'second':
				if ( 
					0 <= intval( $value )
					&& intval( $value ) <= 59
				) {
					$return = true;
				}
				break;
			case 'dayofweek':
				if ( 
					1 <= intval( $value )
					&& intval( $value ) <= 7
				) {
					$return = true;
				}
				break;
			case 'dayofyear':
				if ( 
					1 <= intval( $value )
					&& intval( $value ) <= 366
				) {
					$return = true;
				}
				break;
		}
	}
	return $return;
}