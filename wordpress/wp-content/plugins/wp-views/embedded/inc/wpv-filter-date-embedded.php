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
							$date_query_instance_compare = $date_condition['date_operator'];
							$date_query_instance_column = 'post_date';
							if (
								isset( $date_condition['date_column'] )
								&& in_array( $date_condition['date_column'], $date_columns )
							) {
								$date_query_instance_column = $date_condition['date_column'];
							}
							$date_query_instance_resolved = wpv_resolve_single_date_query( $date_query_instance, $date_query_instance_compare, $date_query_instance_column );
							$date_query[] = $date_query_instance_resolved;
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
						$has_group_date_query = wpv_resolve_group_date_query( $value, $date_condition );
						if ( $has_group_date_query ) {
							$date_query[] = $has_group_date_query;
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

/**
* wpv_resolve_single_date_query
*
* Resolve single date query entries, based on the date conditions:
* 	- Equal comparisons are returned as is
* 	- Singular date conditions are returned as is
* 	- Different from comparisons are turned into sub-conditions with an OR relation: ! ( A AND B ) == ( ! A ) OR ( ! B )
* 	- Greater (or equal to) and Lower (or equal to) depend on the year condition:
* 		- If there is a year condition:
* 			- A speciffic date is calculated and applied as a 'before' or 'after' statement 
* 			- 'week', 'dayofweek' and 'dayofyear', if any, are transformed into individual 'before' or 'after' conditions and added as an AND statement to the above one
* 		- If there is no year condition:
* 			- Several subqueries are added depending on the existing conditions for 'month', 'day', 'hour', 'minute' and 'second': <= ( A AND B AND C ) = ( < A OR ( = A AND < B ) OR ( = A AND = B AND <= C ) )
* 			- 'week', 'dayofweek' and 'dayofyear', if any, are transformed into individual conditions and added as an AND statement to the above one
*
* @param $instance (array) List of date parameters and their values
* @param $compare
* @param $column
*
* @return (array) Date query arguments
*
* @since 1.10
*/

function wpv_resolve_single_date_query( $instance, $compare, $column ) {
	if ( 
		count( $instance ) == 1 
		|| $compare == '='
	) {
		// $instance contains all the date conditions already, just needs 'compare' and 'column' values
		$instance['compare'] = $compare;
		$instance['column'] = $column;
		return $instance;
	}
	$resolved_instance = array();
	switch ( $compare ) {
		case '!=':
			foreach ( $instance as $condition => $value ) {
				$resolved_instance[] = array(
					'compare'	=> $compare,
					'column'	=> $column,
					$condition	=> $value
				);
			}
			// If we have just one date condition, return it; otherwise, return the combination (that will use an AND relation)
			if ( count( $resolved_instance ) == 1 ) {
				$resolved_instance = $resolved_instance[0];
			} else {
				$resolved_instance['relation'] = 'OR';
			}
			break;
		case '<':
		case '<=':
		case '>':
		case '>=':
			if ( isset( $instance['year'] ) ) {
				$before_or_after = in_array( $compare, array( '<', '<=' ) ) ? 'before' : 'after';
				$is_inclusive = in_array( $compare, array( '<=', '>=' ) );
				// Basic instance: a 'year' condition is set
				// We need to populate 'month', 'day', 'hour', 'minute' and 'second' with values until the first one is missing
				// Then we calculate a date and compose a 'before' or 'after' statement, taking care of the inclusive argument
				$resolved_basic_instance = array();
				$date_array = array(
					'year'	=> $instance['year'],
				);
				$use_max = in_array( $compare, array( '<=', '>' ) );
				$continue_statement = true;
				// Set defaults for other date elements
				if ( isset( $instance['month'] ) ) {
					$date_array['month'] = $instance['month'];
				} else {
					$continue_statement = false;
				}
				if ( 
					$continue_statement
					&& isset( $instance['day'] ) 
				) {
					$date_array['day'] = $instance['day'];
				} else {
					$continue_statement = false;
				}
				if ( 
					$continue_statement
					&& isset( $instance['hour'] ) 
				) {
					$date_array['hour'] = $instance['hour'];
				} else {
					$continue_statement = false;
				}
				if ( 
					$continue_statement
					&& isset( $instance['minute'] ) 
				) {
					$date_array['minute'] = $instance['minute'];
				} else {
					$continue_statement = false;
				}
				if ( 
					$continue_statement
					&& isset( $instance['second'] ) 
				) {
					$date_array['second'] = $instance['second'];
				} else {
					$continue_statement = false;
				}
				
				$resolved_basic_instance[$before_or_after] = $date_array;

				if ( in_array( $compare, array( '<=', '>=' ) ) ) {
					$resolved_basic_instance['inclusive'] = true;
				}
				$resolved_basic_instance['column'] = $column;
				$resolved_instance[] = $resolved_basic_instance;
				
				// 'wee', 'dayofyear' and 'dayofweek' instances: a 'year' condition is set
				// So we need to calculate specific dates based on that data
				// Then we compose a 'before' or 'after' statement
				if ( isset( $instance['week'] ) ) {
					$zeroed_week = sprintf( "%02u", $instance['week'] );
					$calc_date = date( "F jS, Y", strtotime( $instance['year'] . "W" . $zeroed_week . "1" ) );
					if ( ! in_array( $compare, array( '<', '>=' ) ) ) {
						$calc_date = date( "F jS, Y", strtotime( $instance['year'] . "W" . $zeroed_week . "7" ) );
					}
					$resolved_instance[] = array(
						$before_or_after	=> $calc_date,
						'inclusive'			=> $is_inclusive,
						'column'			=> $column
					);
				}
				if ( isset( $instance['dayofyear'] ) ) {
					$calc_date = date( "F jS, Y", strtotime( $instance['year'] . "-01-01 +" . ( $instance['dayofyear'] - 1 ) . " days" ) );
					$resolved_instance[] = array(
						$before_or_after	=> $calc_date,
						'inclusive'			=> $is_inclusive,
						'column'			=> $column
					);
				}
				if ( isset( $instance['dayofweek'] ) ) {
					$resolved_instance[] = array(
						'compare'	=> $compare,
						'column'	=> $column,
						'dayofweek'	=> $instance['dayofweek']
					);
				}
				// If we have just one date condition, return it; otherwise, return the combination (that will use an AND relation)
				if ( count( $resolved_instance ) == 1 ) {
					$resolved_instance = $resolved_instance[0];
				}
			} else {
				// No year condition is set
				// 'week', 'dayofyear' and 'dayofweek' produce individual conditions combined with AND relations
				// If no further conditions are set, just return what we have
				$resolved_individual_instance = array();
				foreach ( $instance as $condition => $value ) {
					if ( in_array( $condition, array( 'week', 'dayofweek', 'dayofyear' ) ) ) {
						$resolved_individual_instance[] = array(
							'compare'	=> $compare,
							'column'	=> $column,
							$condition	=> $value
						);
						unset( $instance[$condition] );
					}
				}

				$resolved_mixed_instance = array();
				$partial_count = count( $instance );
				if ( $partial_count > 0 ) {
					// No year condition, but further conditions are set: let's handle the 'month', 'day', 'hour', 'minute' and 'second' conditions
					// For each remaining condition in that order, apply strict conditions combined with mixed statements combining equal plus strict conditions
					// Mind that the last condition should get the original condition in that mixed statement
					$processed_count = 0;
					$compare_corrected = in_array( $compare, array( '<', '<=' ) ) ? '<' : '>';
					$takeof_array = array(
						'compare'	=> '=',
						'column'	=> $column
					);
					
					// Check 'month', 'day', 'hour', 'minute' and 'second' conditions
					// If this is the first condition we find, prepare a sub-condition with comparison depending on whether there are even more conditions pending processing
					// If this is not the first condition, prepare a sub-condition combining previous ones with an equal comparison and this one with comparison depending on whether there are even more conditions pending processing
					// If the next condition is not met, use the native comparison; use the corrected otherwise
					// If the previous condition was not there, break
					$sequel_checks = array( 'month', 'day', 'hour', 'minute', 'second' );
					foreach ( $sequel_checks as $sek_index => $seq_check ) {
						if ( isset( $instance[$seq_check] ) ) {
							$processed_count = $processed_count + 1;
							if ( $processed_count == 1 ) {
								$resolved_mixed_instance[] = array(
									'compare'	=> ( ! isset( $instance[ $sequel_checks[ $sek_index + 1 ] ] ) ) ? $compare : $compare_corrected,
									'column'	=> $column,
									$seq_check	=> $instance[$seq_check]
								);
							} else {
								$resolved_mixed_instance[] = array(
									array(
										'compare'	=> ( ! isset( $instance[ $sequel_checks[ $sek_index + 1 ] ] ) ) ? $compare : $compare_corrected,
										'column'	=> $column,
										$seq_check	=> $instance[$seq_check]
									),
									$takeof_array,
									'relation'		=> 'AND'
								);
							}
							$takeof_array[$seq_check] = $instance[$seq_check];
						} else if ( 
							$sek_index > 0
							&& isset( $instance[ $sequel_checks[ $sek_index - 1 ] ] )
						) {
							break;
						}
					}
				}
				
				// Compose the resolved date query
				if (
					count( $resolved_individual_instance ) > 0 
					&& count( $resolved_mixed_instance ) > 0
				) {
					if ( count( $resolved_individual_instance ) == 1 ) {
						$resolved_individual_instance = $resolved_individual_instance[0];
					}
					if ( count( $resolved_mixed_instance ) == 1 ) {
						$resolved_mixed_instance = $resolved_mixed_instance[0];
					} else {
						$resolved_mixed_instance['relation'] = 'OR';
					}
					$resolved_instance = array(
						$resolved_individual_instance,
						$resolved_mixed_instance,
						'relation'	=> 'AND'
					);
				} else if ( count( $resolved_individual_instance ) > 0  ) {
					if ( count( $resolved_individual_instance ) == 1 ) {
						$resolved_instance = $resolved_individual_instance[0];
					} else {
						$resolved_instance = $resolved_individual_instance;
					}
				} else if ( count( $resolved_mixed_instance ) > 0 ) {
					if ( count( $resolved_mixed_instance ) == 1 ) {
						$resolved_instance = $resolved_mixed_instance[0];
					} else {
						$resolved_mixed_instance['relation'] = 'OR';
						$resolved_instance = $resolved_mixed_instance;
					}
				}
			}
			break;
	}
	
	return $resolved_instance;
}

/**
* wpv_resolve_group_date_query
*
* Generate the date_query entry for IN, NOT IN, BETWEEN and NOT BETWEEN comparisons
*
* In case of BETWEEN and NOT BETWEEN, we also need to manage the cases where one of the values is $no_parameter_found
* If so, we will transform them into the right greater-or-equal-than or lower-or-equal-than statements.
*
* @param array $value
* @param array $date_condition
*
* @return (array|boolean) The date_query instance on success, false otherwise
*
* @since 1.9.0
*/

function wpv_resolve_group_date_query( $value, $date_condition ) {
	global $no_parameter_found;
	$start_of_week = get_option( 'start_of_week' );
	$date_columns = array( 'post_date', 'post_date_gmt', 'post_modified', 'post_modified_gmt' );
	$date_condition_operator = $date_condition['date_operator'];
	$adjust_start_of_week = false;
	if ( 
		$date_condition['date_multiple_selected'] == 'dayofweek' 
		&& $start_of_week == 1
	) {
		/*
		* Based on the $start_of_week setting, $indexed_v is 1 ( Monday ) to 7 ( Sunday )
		* We must translate it to values that the date_query['dayofweek'] attribute understands
		* That is, 1 ( Sunday ) to 7 ( Saturday )
		*/
		$adjust_start_of_week = true;
	}
	$date_query_instance = array();
	if (
		isset( $date_condition['date_column'] )
		&& in_array( $date_condition['date_column'], $date_columns )
	) {
		$date_query_instance['column'] = $date_condition['date_column'];
	}
	if (
		in_array( $no_parameter_found, $value ) 
		&& (
			'BETWEEN' == $date_condition_operator 
			|| 'NOT BETWEEN' == $date_condition_operator 
		)
	) {
		// Clean from empty values, the ones we care about are $no_parameter_found instead
		$value = array_filter( $value );
		$value_count = count( $value );
		if ( 0 == $value_count ) {
			return false;
		} else if ( 1 == $value_count ) {
			if ( $value[0] == $no_parameter_found ) {
				return false;
			} else {
				if ( $date_condition_operator == 'BETWEEN' ) {
					$date_condition_operator = '>=';
				} else {
					$date_condition_operator = '<=';
				}
				$single_value = $value[0];
				$is_valid_value = wpv_integer_date_validate( $single_value, $date_condition['date_multiple_selected'] );
				if ( $is_valid_value !== false ) {
					if ( $adjust_start_of_week ) {
						$single_value = ( $single_value % 7 ) + 1;
					}
					$date_query_instance[$date_condition['date_multiple_selected']] = $single_value;
					$date_query_instance['compare'] = $date_condition_operator;
					return $date_query_instance;
				}
			}
		} else {
			if (
				$value[0] == $no_parameter_found 
				&& $value[1] == $no_parameter_found
			) {
				return false;
			}
			if ( $value[0] == $no_parameter_found ) {
				if ( $date_condition_operator == 'BETWEEN' ) {
					$date_condition_operator = '<=';
				} else {
					$date_condition_operator = '>=';
				}
				$single_value = $value[1];
				$is_valid_value = wpv_integer_date_validate( $single_value, $date_condition['date_multiple_selected'] );
				if ( $is_valid_value !== false ) {
					if ( $adjust_start_of_week ) {
						$single_value = ( $single_value % 7 ) + 1;
					}
					$date_query_instance[$date_condition['date_multiple_selected']] = $single_value;
					$date_query_instance['compare'] = $date_condition_operator;
					return $date_query_instance;
				}
			} elseif ( $value[1] == $no_parameter_found ) {
				if ( $date_condition_operator == 'BETWEEN' ) {
					$date_condition_operator = '>=';
				} else {
					$date_condition_operator = '<=';
				}
				$single_value = $value[0];
				$is_valid_value = wpv_integer_date_validate( $single_value, $date_condition['date_multiple_selected'] );
				if ( $is_valid_value !== false ) {
					if ( $adjust_start_of_week ) {
						$single_value = ( $single_value % 7 ) + 1;
					}
					$date_query_instance[$date_condition['date_multiple_selected']] = $single_value;
					$date_query_instance['compare'] = $date_condition_operator;
					return $date_query_instance;
				}
			} else {
				$value = array_filter( $value, 'wpv_is_valid_non_empty_value_to_filter' );
				$value = wpv_array_date_validate( $value, $date_condition['date_multiple_selected'] );
				if ( ! empty( $value ) ) {
					$indexed_values = array_values( $value );
					if ( $adjust_start_of_week ) {
						foreach ( $indexed_values as $indexed_k => $indexed_v ) {
							$indexed_values[$indexed_k] = ( $indexed_v % 7 ) + 1;
						}
					}
					$date_query_instance[$date_condition['date_multiple_selected']] = $indexed_values;
					$date_query_instance['compare'] = $date_condition_operator;
					return $date_query_instance;
				}
			}
		}
	} else {
		$value = array_filter( $value, 'wpv_is_valid_non_empty_value_to_filter' );
		$value = wpv_array_date_validate( $value, $date_condition['date_multiple_selected'] );
		if ( ! empty( $value ) ) {
			$indexed_values = array_values( $value );
			if ( $adjust_start_of_week ) {
				foreach ( $indexed_values as $indexed_k => $indexed_v ) {
					$indexed_values[$indexed_k] = ( $indexed_v % 7 ) + 1;
				}
			}
			$date_query_instance[$date_condition['date_multiple_selected']] = $indexed_values;
			$date_query_instance['compare'] = $date_condition_operator;
			return $date_query_instance;
		}
	}
	return false;
}

/**
* wpv_filter_register_post_author_shortcode_attributes
*
* Register the filter by post author on the method to get View shortcode attributes
*
* @since 1.10
*/

add_filter( 'wpv_filter_register_shortcode_attributes_for_posts', 'wpv_filter_register_post_date_shortcode_attributes', 10, 2 );

function wpv_filter_register_post_date_shortcode_attributes( $attributes, $view_settings ) {
	if ( 
		isset( $view_settings['date_filter'] ) 
		&& is_array( $view_settings['date_filter'] ) 
	) {
		$date_operator = array(
			'single' => array( '=', '!=', '<', '<=', '>', '>=' ),
			'group' => array( 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' ),
		);
		$date_options = array( 'year', 'month', 'week', 'day', 'dayofyear', 'dayofweek', 'hour', 'minute', 'second' );
		$date_options_data = array(
			'year'		=> array(
				'label'			=> __( 'year', 'wpv-views' ), 
				'placeholder'	=> '2015',
				'description'	=> __( 'Please enter a valid four-digits year, like 2015', 'wpv-views' )
				),
			'month'		=> array(
				'label'			=> __( 'month', 'wpv-views' ), 
				'placeholder'	=> '6',
				'description'	=> __( 'Please enter a valid month number (1-12)', 'wpv-views' )
				),
			'week'		=> array(
				'label'			=> __( 'week', 'wpv-views' ), 
				'placeholder'	=> '23',
				'description'	=> __( 'Please enter a valid week number (1-53)', 'wpv-views' )
				),
			'day'		=> array(
				'label'			=> __( 'day', 'wpv-views' ), 
				'placeholder'	=> '15',
				'description'	=> __( 'Please enter a valid day number (1-31)', 'wpv-views' )
				),
			'dayofyear'	=> array(
				'label'			=> __( 'day of the year', 'wpv-views' ), 
				'placeholder'	=> '280',
				'description'	=> __( 'Please enter a valid day of the year (1-366)', 'wpv-views' )
				),
			'dayofweek'	=> array(
				'label'			=> __( 'day of the week', 'wpv-views' ), 
				'placeholder'	=> '5',
				'description'	=> __( 'Please enter a valid day of the week (1-7)', 'wpv-views' )
				),
			'hour'		=> array(
				'label'			=> __( 'hour', 'wpv-views' ), 
				'placeholder'	=> '6',
				'description'	=> __( 'Please enter a valid hour (0-23)', 'wpv-views' )
				),
			'minute'	=> array(
				'label'			=> __( 'minute', 'wpv-views' ), 
				'placeholder'	=> '35',
				'description'	=> __( 'Please enter a valid minute (0-59)', 'wpv-views' )
				),
			'second'	=> array(
				'label'			=> __( 'second', 'wpv-views' ),
				'placeholder'	=> '45',
				'description'	=> __( 'Please enter a valid second (0-59)', 'wpv-views' )
				)
		);
		foreach ( $view_settings['date_filter']['date_conditions'] as $date_condition ) {
			if ( 
				is_array( $date_condition ) 
				&& isset( $date_condition['date_operator'] )
			) {
				if ( in_array( $date_condition['date_operator'], $date_operator['single'] ) ) {
					foreach ( $date_options as $date_opt ) {
						if (
							isset( $date_condition[$date_opt] )
						) {
							// Translate URL_PARAM, VIEW_PARAM and date functions into values
							$value = $date_condition[$date_opt];
							$value = explode( ',', $value );
							$value = array_map( 'trim', $value );
							if ( ! empty( $value ) ) {
								$value_real = reset( $value );
								if ( preg_match( "/VIEW_PARAM\(([^\)]+)\)/", $value_real, $shortcode ) ) {
									$attributes[] = array(
										'query_type'	=> 'posts',
										'filter_type'	=> 'post_date',
										'filter_label'	=> sprintf( __( 'Post date - %s (<em>%s</em>)', 'wpv-views' ), $date_options_data[$date_opt]['label'], $shortcode[1] ),
										'value'			=> 'number' . $date_opt,
										'attribute'		=> $shortcode[1],
										'expected'		=> $date_opt,
										'placeholder'	=> $date_options_data[$date_opt]['placeholder'],
										'description'	=> $date_options_data[$date_opt]['description']
									);
								}
							}
						}
					}
					
				} else if ( 
					in_array( $date_condition['date_operator'], $date_operator['group'] ) 
					&& isset( $date_condition['date_multiple_selected'] )
					&& in_array( $date_condition['date_multiple_selected'], $date_options )
					&& isset( $date_condition[$date_condition['date_multiple_selected']] )
					&& ! empty( $date_condition[$date_condition['date_multiple_selected']] )
				) {
					$date_cond_selected = $date_condition['date_multiple_selected'];
					$value = $date_condition[$date_cond_selected];
					$value = explode( ',', $value );
					$value = array_map( 'trim', $value );
					if ( ! empty( $value ) ) {
						foreach ( $value as $value_real ) {
							if ( preg_match( "/VIEW_PARAM\(([^\)]+)\)/", $value_real, $shortcode ) ) {
								$attributes[] = array(
									'query_type'	=> 'posts',
									'filter_type'	=> 'post_date',
									'filter_label'	=> sprintf( __( 'Post date - %s (<em>%s</em>)', 'wpv-views' ), $date_options_data[$date_cond_selected]['label'], $shortcode[1] ),
									'value'			=> 'number' . $date_cond_selected,
									'attribute'		=> $shortcode[1],
									'expected'		=> $date_cond_selected,
									'placeholder'	=> $date_options_data[$date_cond_selected]['placeholder'],
									'description'	=> $date_options_data[$date_cond_selected]['description']
								);
							}
						}
					}
				}
			}
		}
	}
	return $attributes;
}