<?php

/**
* wpv-user-functions.php
*
* Resolves values for Views query filters based on URL parameters, Views shortcode attributes, date functions and date calculations
*
* @package Views
*
* @since unknown
*/

if ( ! function_exists( 'wpv_filter_parse_date' ) ) {
	include_once WPV_PATH_EMBEDDED . '/common/wpv-filter-date-embedded.php';
}

$GLOBALS['no_parameter_found'] = 'WPV_NO_PARAM_FOUND';

// @todo make the existing filters use the filter method instead of calling this function
// @todo check whether this can be now DEPRECATED
function wpv_apply_user_functions( $value ) {
    $value = wpv_apply_user_function_url_param( $value );
    $value = wpv_apply_user_function_view_param( $value );
    $value = wpv_apply_user_function_date_compare( $value );
    return $value;
}

add_filter( 'wpv_resolve_variable_values', 'wpv_filter_resolve_variable_url_parameter', 10, 2 );
add_filter( 'wpv_resolve_variable_values', 'wpv_filter_resolve_variable_shortcode_attribute', 20, 2 );
add_filter( 'wpv_resolve_variable_values', 'wpv_filter_resolve_variable_date_integer', 30, 2 );
add_filter( 'wpv_resolve_variable_values', 'wpv_filter_resolve_variable_date_timestamp', 40, 2 );
add_filter( 'wpv_resolve_variable_values', 'wpv_filter_resolve_variable_framework_value', 50, 2 );

/**
* wpv_filter_resolve_variable_url_parameter
*
* Resolves URL_PARAM(xxx) matches in the $values string using $_GET attributes
*
* @param $value (string)
* @param $attr (array)
*
* @return $value (string)
*
* @since 1.8.0
*/

function wpv_filter_resolve_variable_url_parameter( $value, $attr = array() ) {
	if (
		is_array( $attr )
		&& isset( $attr['filters'] )
		&& is_array( $attr['filters'] )
		&& in_array( 'url_parameter', $attr['filters'] )
	) {
		$value = wpv_apply_user_function_url_param( $value );
	}
	return $value;
}

/**
* wpv_filter_resolve_variable_shortcode_attribute
*
* Resolves VIEW_PARAM(xxx) matches in the $values string using the View shortcode attributes
*
* @param $value (string)
* @param $attr (array)
*
* @return $value (string)
*
* @since 1.8.0
*/

function wpv_filter_resolve_variable_shortcode_attribute( $value, $attr = array() ) {
	if (
		is_array( $attr )
		&& isset( $attr['filters'] )
		&& is_array( $attr['filters'] )
		&& in_array( 'shortcode_attribute', $attr['filters'] )
	) {
		$value = wpv_apply_user_function_view_param( $value );
	}
	return $value;
}

/**
* wpv_filter_resolve_variable_date_integer
*
* Resolves CURRENT_ONE(), FUTURE_ONE(x) and PAST_ONE(x) matches in the $values string
* The $attr['date_integer_date_type'] date type sets the date element to get the integer to
* So we can return a valid integer based on the ranges this $attr['date_integer_date_type'] date type can take
*
* @param $value (string)
* @param $attr (array)
* 	$attr['date_integer_date_type'] (string) <year|month|week|day|hour|minute|second|dayofweek|dayofyear>
*
* @return $value (string)
*
* @since 1.8.0
*/

function wpv_filter_resolve_variable_date_integer( $value, $attr = array() ) {
	if (
		is_array( $attr )
		&& isset( $attr['filters'] )
		&& is_array( $attr['filters'] )
		&& in_array( 'date_integer', $attr['filters'] )
	) {
		$date_type = '';
		if ( isset( $attr['date_integer_date_type'] ) ) {
			$date_type = $attr['date_integer_date_type'];
		}
		$value = wpv_apply_user_function_date_integer( $value, $attr );
	}
	return $value;
}

/**
* wpv_filter_resolve_variable_date_timestamp
*
* Resolves ##DATE_FUNC(x)## matches in the $values string to timestamps
*
* @param $value (string)
* @param $attr (array)
*
* @return $value (string)
*
* @since 1.8.0
*/

function wpv_filter_resolve_variable_date_timestamp( $value, $attr = array() ) {
	if (
		is_array( $attr )
		&& isset( $attr['filters'] )
		&& is_array( $attr['filters'] )
		&& in_array( 'date_timestamp', $attr['filters'] )
	) {
		$value = wpv_apply_user_function_date_compare( $value );
	}
	return $value;
}

/**
* wpv_filter_resolve_variable_framework_value
*
*/

function wpv_filter_resolve_variable_framework_value( $value, $attr = array() ) {
	if (
		is_array( $attr )
		&& isset( $attr['filters'] )
		&& is_array( $attr['filters'] )
		&& in_array( 'framework_value', $attr['filters'] )
	) {
		$value = wpv_apply_user_function_framework_key( $value, $attr );
	}
	return $value;
}

function wpv_apply_user_function_framework_key( $value, $attr = array() ) {
	$pattern = '/FRAME_KEY\(([^(]*?)\)/siU';
    if ( preg_match_all( $pattern, $value, $matches, PREG_SET_ORDER ) ) {
        global $WP_Views_fapi, $no_parameter_found;
		if ( isset( $attr['default'] ) ) {
			$default = $attr['default'];
		} else {
			$default = $no_parameter_found;
		}
		foreach ( $matches as $match ) {
			$framework_value = $WP_Views_fapi->get_framework_value( $match[1], $default );
			if ( is_array( $framework_value ) ) {
				$framework_value = implode( ',', $framework_value );
			}
            $search = $match[0];
            $value = str_replace( $search, $framework_value, $value );
        }
    }
    return $value;
}

/**
* wpv_apply_user_function_date_integer
*
* Takes a string and transforms it into an integer valid as a Date_Query value.
*
* @param (string) May contain one of the following expressions to transform
*	<CURRENT_ONE()|FUTURE_ONE(x)|PAST_ONE(x)>
* @param $this_date (string) Contains the string to replace the _ONE substring from the parameter above
*	<year|month|week|day|hour|minute|second|dayofweek|dayofyear>
*
* @return (string)
*
* @since 1.8.0
*/

function wpv_apply_user_function_date_integer( $value, $attr = array() ) {
	$value = stripcslashes( $value );
	if ( 
		isset( $attr['date_integer_date_type'] ) 
		&& in_array( $attr['date_integer_date_type'], array( 'year', 'month', 'week', 'day', 'hour', 'minute', 'second', 'dayofweek', 'dayofyear' ) )
	) {
		$this_date = '_' . strtoupper( $attr['date_integer_date_type'] );
		$value = str_replace( '_ONE', $this_date, $value );
	}
	$occurences = preg_match_all( '/(\\w+)\(([^\)]*)\)/', $value, $matches );
	if ( $occurences > 0 ) {
		global $no_parameter_found;
		for ( $i = 0; $i < $occurences; $i++ ) {
			$date_func = $matches[1][$i];
			// remove comma at the end of date value in case is left there
			$date_value = isset( $matches[2] ) ? rtrim( $matches[2][$i], ',' ) : '';
			$resulting_date = false;
			$start_of_week = get_option( 'start_of_week' );
			switch ( strtoupper( $date_func ) ) {
				case "CURRENT_YEAR":
					$resulting_date = date_i18n('Y');
					break;
				case "FUTURE_YEAR":
					$resulting_date = date_i18n('Y') + $date_value;
					if ( ! checkdate( 1, 1, $resulting_date ) ) {
						$resulting_date = $no_parameter_found;
					}
					break;
				case "PAST_YEAR":
					$resulting_date = date_i18n('Y') - $date_value;
					if ( ! checkdate( 1, 1, $resulting_date ) ) {
						$resulting_date = $no_parameter_found;
					}
					break;
				// Keep an eye on the week one, as sometimes it goes from 0 to 53, sometimes from 1 to 53
				case "CURRENT_MONTH":
					$resulting_date = date_i18n('n');
					break;
				case "FUTURE_MONTH":
					$resulting_date = date_i18n( 'n', mktime( 0, 0, 0, date_i18n('m') + $date_value, 1, date_i18n('Y') ) );
					break;
				case "PAST_MONTH":
					$resulting_date = date_i18n( 'n', mktime( 0, 0, 0, date_i18n('m') - $date_value, 1, date_i18n('Y') ) );
					break;
				// Keep an eye on the week ones, as sometimes they start at 0 and sometimes they start at 1
				case 'CURRENT_WEEK':
					$resulting_date = date_i18n('W');
					if ( $resulting_date == 0 ) {
						$resulting_date = 0;
					} else {
						$resulting_date = ltrim( $resulting_date, '0' );
					}
					break;
				case "FUTURE_WEEK":
					$resulting_date = date_i18n( 'W', mktime( 0, 0, 0, date_i18n('m'), date_i18n('j') + ( 7 * $date_value ), date_i18n('Y') ) );
					if ( $resulting_date == 0 ) {
						$resulting_date = 0;
					} else {
						$resulting_date = ltrim( $resulting_date, '0' );
					}
					break;
				case "PAST_WEEK":
					$resulting_date = date_i18n( 'W', mktime( 0, 0, 0, date_i18n('m'), date_i18n('j') - ( 7 * $date_value ), date_i18n('Y') ) );
					if ( $resulting_date == 0 ) {
						$resulting_date = 0;
					} else {
						$resulting_date = ltrim( $resulting_date, '0' );
					}
					break;
				case 'CURRENT_DAY':
					$resulting_date = date_i18n('j');
					break;
				case "FUTURE_DAY":
					$resulting_date = date_i18n( 'j', mktime( 0, 0, 0, date_i18n('m'), date_i18n('j') + $date_value, date_i18n('Y') ) );
					break;
				case "PAST_DAY":
					$resulting_date = date_i18n( 'j', mktime( 0, 0, 0, date_i18n('m'), date_i18n('j') - $date_value, date_i18n('Y') ) );
					break;
				case 'CURRENT_HOUR':
					$resulting_date = date_i18n('G');
					break;
				case "FUTURE_HOUR":
					$resulting_date = date_i18n( 'G', mktime( date_i18n( 'G' ) + $date_value, 0, 0, date_i18n('m'), date_i18n('j'), date_i18n('Y') ) );
					break;
				case "PAST_HOUR":
					$resulting_date = date_i18n( 'G', mktime( date_i18n( 'G' ) - $date_value, 0, 0, date_i18n('m'), date_i18n('j'), date_i18n('Y') ) );
					break;
				case 'CURRENT_MINUTE':
					$resulting_date = date_i18n( 'i' );
					if ( $resulting_date == 0 ) {
						$resulting_date = 0;
					} else {
						$resulting_date = ltrim( $resulting_date, '0' );
					}
					break;
				case "FUTURE_MINUTE":
					$resulting_date = date_i18n( 'i', mktime( date_i18n( 'G' ), date_i18n( 'i' ) + $date_value, 0, date_i18n('m'), date_i18n('j'), date_i18n('Y') ) );
					if ( $resulting_date == 0 ) {
						$resulting_date = 0;
					} else {
						$resulting_date = ltrim( $resulting_date, '0' );
					}
					break;
				case "PAST_MINUTE":
					$resulting_date = date_i18n( 'i', mktime( date_i18n( 'G' ), date_i18n( 'i' ) - $date_value, 0, date_i18n('m'), date_i18n('j'), date_i18n('Y') ) );
					if ( $resulting_date == 0 ) {
						$resulting_date = 0;
					} else {
						$resulting_date = ltrim( $resulting_date, '0' );
					}
					break;
				case 'CURRENT_SECOND':
					$resulting_date = date_i18n( 's' );
					if ( $resulting_date == 0 ) {
						$resulting_date = 0;
					} else {
						$resulting_date = ltrim( $resulting_date, '0' );
					}
					break;
				case "FUTURE_SECOND":
					$resulting_date = date_i18n( 's', mktime( date_i18n( 'G' ), date_i18n( 'i' ), date_i18n( 's') + $date_value, date_i18n('m'), date_i18n('j'), date_i18n('Y') ) );
					if ( $resulting_date == 0 ) {
						$resulting_date = 0;
					} else {
						$resulting_date = ltrim( $resulting_date, '0' );
					}
					break;
				case "PAST_SECOND":
					$resulting_date = date_i18n( 's', mktime( date_i18n( 'G' ), date_i18n( 'i' ), date_i18n( 's' ) - $date_value, date_i18n('m'), date_i18n('j'), date_i18n('Y') ) );
					if ( $resulting_date == 0 ) {
						$resulting_date = 0;
					} else {
						$resulting_date = ltrim( $resulting_date, '0' );
					}
					break;
				/**
				* XXX_DAYOFWEEK returns an integer from 1 to 7 depending on the start_of_week setting:
				* start_of_week = 1 ( Monday )		=>	returns 1 = Monday, ..., 7 = Sunday
				* start_of_week !=1	( not Monday )	=>	returns 1 = Sunday, ..., 7 = Saturday
				*/
				case 'CURRENT_DAYOFWEEK':
					$resulting_date = date_i18n( 'w' );
					if ( $start_of_week == 1 ) {
						if ( $resulting_date == 0 ) {
							$resulting_date = 7;
						}
					} else {
						$resulting_date = $resulting_date + 1;
					}
					break;
				case "FUTURE_DAYOFWEEK":
					$resulting_date = date_i18n( 'w', mktime( 0, 0, 0, date_i18n('m'), date_i18n('j') + $date_value, date_i18n('Y') ) );
					if ( $start_of_week == 1 ) {
						if ( $resulting_date == 0 ) {
							$resulting_date = 7;
						}
					} else {
						$resulting_date = $resulting_date + 1;
					}
					break;
				case "PAST_DAYOFWEEK":
					$resulting_date = date_i18n( 'w', mktime( 0, 0, 0, date_i18n('m'), date_i18n('j') - $date_value, date_i18n('Y') ) );
					if ( $start_of_week == 1 ) {
						if ( $resulting_date == 0 ) {
							$resulting_date = 7;
						}
					} else {
						$resulting_date = $resulting_date + 1;
					}
					break;
				case 'CURRENT_DAYOFYEAR':
					$resulting_date = date_i18n('z') + 1;
					break;
				case "FUTURE_DAYOFYEAR":
					$resulting_date = date_i18n( 'z', mktime( 0, 0, 0, date_i18n('m'), date_i18n('j') + $date_value, date_i18n('Y') ) ) + 1;
					break;
				case "PAST_DAYOFYEAR":
					$resulting_date = date_i18n( 'z', mktime( 0, 0, 0, date_i18n('m'), date_i18n('j') - $date_value, date_i18n('Y') ) ) + 1;
					break;
			}
			if ( $resulting_date !== false ) {
				$value = str_replace( $matches[0][$i], $resulting_date, $value );
			}
		}
	}
	return $value;
}

/**
* wpv_apply_user_function_url_param
*
* Takes a string and resolves the URL_PARAM(xxx) matches using $_GET parameters
*
* @param (string) May contain the following expression to transform
*	<URL_PARAM(xxx)>
*
* @return (string)
*
* @since unknown
*/

function wpv_apply_user_function_url_param( $value ) {
    $pattern = '/URL_PARAM\(([^(]*?)\)/siU';
    if ( preg_match_all( $pattern, $value, $matches, PREG_SET_ORDER ) ) {
        global $no_parameter_found;
		foreach( $matches as $match ) {
            if ( isset( $_GET[$match[1]] ) ) {
				$url_param = $_GET[$match[1]];
				if ( is_array( $url_param ) ) {
					// TODO: an empty value inside that array should be ignored too
					// Really? For checkboxes and multi-select, you can not pass multiple values if one is empty
					// As when clicking the empty value, all others are unselected
					// But just in case, this is good to have
					// Anyhow it makes it impossible to filter by several values if one is empty
					foreach ( $url_param as $key => $val ) {
						if ( $val == '' ) {
							$url_param[$key] = $no_parameter_found;
						}
					}
					$url_param = implode(',', $url_param);
				}
				if ( $url_param == '' ) {
					// an empty parameter should be ignored.
					// eg. my-site.com/price_search/?beds=2&price=
	                $url_param = $no_parameter_found;
				}
            } else {
                $url_param = $no_parameter_found;
				 if ( isset( $_GET[$match[1] . '_fakezero'] ) ) {
					$url_param_fakezero = $_GET[$match[1] . '_fakezero'];
					if ( $url_param_fakezero == 'yes' ) {
						$url_param = 0;
					}
				 }
            }
            $search = $match[0];
            $value = str_replace( $search, $url_param, $value );
        }
    }
    return $value;
}

/**
* wpv_apply_user_function_view_param
*
* Takes a string and resolves the VIEW_PARAM(xxx) matches using the current View shortcode attributes
*
* @param (string) May contain the following expression to transform
*	<VIEW_PARAM(xxx)>
*
* @return (string)
*
* @since unknown
*/

function wpv_apply_user_function_view_param( $value ) {
    $pattern = '/VIEW_PARAM\(([^(]*?)\)/siU';
    if( preg_match_all( $pattern, $value, $matches, PREG_SET_ORDER ) ) {
        global $WP_Views, $no_parameter_found;
		foreach($matches as $match) {
            $view_attr = $WP_Views->get_view_shortcodes_attributes();
            if ( isset( $view_attr[$match[1]] ) ) {
				if ( $view_attr[$match[1]] != '' ) {
					$view_param = $view_attr[$match[1]];
				} else {
					// an empty parameter should be ignored.
					// eg. [wpv-view name="my-view" beds="2" price=""]
					// TODO we might want to review this
	                $view_param = $no_parameter_found;
				}
            } else {
                $view_param = $no_parameter_found;
            }
            $search = $match[0];
            $value = str_replace( $search, $view_param, $value );
        }
    }
    return $value;
}

/**
* wpv_apply_user_function_date_compare
*
* Takes a string and resolves the ##DATE_FUNC(xxx)## matches to timestamps
*
* @param (string) May contain the following expression to transform
* 	NOW()
* 	TODAY()    (time at 00:00 today)
* 	FUTURE_DAY(1)
* 	PAST_DAY(1)
* 	THIS_MONTH()   (time at 00:00 on first day of this month)
* 	FUTURE_MONTH(1)
* 	PAST_MONTH(1)
* 	THIS_YEAR()   (time at 00:00 on first day of this year)
* 	FUTURE_YEAR(1)
* 	PAST_YEAR(1)
* 	SECONDS_FROM_NOW(1)
* 	MONTHS_FROM_NOW(1)
* 	YEARS_FROM_NOW(1)
* 	DATE(dd,mm,yyyy)
* 	DATE(dd,mm,yyyy)    as per Views
* 	DATE('dd/mm/yyyy', 'd/m/Y')
* 	DATE('mm/dd/yyyy', 'm/d/Y')
*
* @return (string)
*
* @since unknown
*/

function wpv_apply_user_function_date_compare( $value ) {
	$parsed = wpv_filter_parse_date( $value );
	if ( $parsed ) {
		$value = $parsed;
	}
	
	return $value;
}

/**
* wpv_is_valid_non_empty_value_to_filter
*
* Checks whether a string is a valid non-empty value to be passed to a filter
* Basicaly, leaves out empty non-zero values and $no_parameter_found
* Suitable to be used on arrays through array_filter
*
* @param $value (string)
*
* @return (boolean)
*
* @since 1.8.0
*/

function wpv_is_valid_non_empty_value_to_filter( $value ) {
	global $no_parameter_found;
	return ( ( ! empty( $value ) || $value === '0' || $value === 0 ) && $value != $no_parameter_found );
}

/**
* wpv_resolve_variable_view_settings
*
* @since 1.8.0
*/

add_filter( 'wpv_filter_override_view_settings', 'wpv_resolve_variable_view_settings', 1 );

function wpv_resolve_variable_view_settings( $view_settings = array() ) {
	if ( ! is_array( $view_settings ) ) {
		return $view_settings;
	}
	foreach ( $view_settings as $vs_key => $vs_value ) {
		switch ( $vs_key ) {
			case 'limit':
			case 'taxonomy_limit':
			case 'users_limit':
				$candidate = apply_filters( 'wpv_resolve_variable_values', $view_settings[$vs_key], array( 'filters' => array( 'framework_value' ), 'default' => -1 ) );
				if ( is_numeric( $candidate ) ) {
					$view_settings[$vs_key] = intval( $candidate );
				} else {
					$view_settings[$vs_key] = -1;
				}
				break;
			case 'offset':
			case 'taxonomy_offset':
			case 'users_offset':
				$candidate = apply_filters( 'wpv_resolve_variable_values', $view_settings[$vs_key], array( 'filters' => array( 'framework_value' ), 'default' => 0 ) );
				if ( is_numeric( $candidate ) ) {
					$view_settings[$vs_key] = intval( $candidate );
				} else {
					$view_settings[$vs_key] = 0;
				}
				break;
			case 'posts_per_page':
				$candidate = apply_filters( 'wpv_resolve_variable_values', $view_settings[$vs_key], array( 'filters' => array( 'framework_value' ), 'default' => 10 ) );
				if ( is_numeric( $candidate ) ) {
					$view_settings[$vs_key] = intval( $candidate );
				} else {
					$view_settings[$vs_key] = 10;
				}
				break;
			case 'rollover':
				if ( isset( $view_settings[$vs_key]['posts_per_page'] ) ) {
					$candidate = apply_filters( 'wpv_resolve_variable_values', $view_settings[$vs_key]['posts_per_page'], array( 'filters' => array( 'framework_value' ), 'default' => 10 ) );
					if ( is_numeric( $candidate ) ) {
						$view_settings[$vs_key]['posts_per_page'] = intval( $candidate );
					} else {
						$view_settings[$vs_key]['posts_per_page'] = 10;
					}
				}
				break;
			default:
				break;
		}
	}
	return $view_settings;
}

/**
* wpv_filter_variable_settings_require_framework_values
*
* Whether the current View requires framework data for the filter by variable settings
*
* @param $state (boolean) the state of this need until this filter is applied
* @param $view_settings
*
* @return $state (boolean)
*
* @since 1.10
*/

add_filter( 'wpv_filter_requires_framework_values', 'wpv_filter_variable_settings_require_framework_values', 20, 2 );

function wpv_filter_variable_settings_require_framework_values( $state, $view_settings ) {
	if ( $state ) {
		return $state;
	}
	$pattern = '/FRAME_KEY\(([^(]*?)\)/siU';
	foreach ( $view_settings as $vs_key => $vs_value ) {
		switch ( $vs_key ) {
			case 'limit':
			case 'taxonomy_limit':
			case 'users_limit':
			case 'offset':
			case 'taxonomy_offset':
			case 'users_offset':
			case 'posts_per_page':
				if ( preg_match_all( $pattern, $vs_value, $matches, PREG_SET_ORDER ) ) {
					$state = true;
				}
				break;
			case 'rollover':
				if ( 
					is_array( $vs_value )
					&& isset( $vs_value['posts_per_page'] ) 
				) {
					if ( preg_match_all( $pattern, $vs_value['posts_per_page'], $matches, PREG_SET_ORDER ) ) {
						$state = true;
					}
				}
				break;
			default:
				break;
		}
	}
	return $state;
}