<?php

WPV_Condition::on_load();

/**
* WPV_Condition
*
* Will hold the conditional output management when it is merged with the Types-CRED syntax
*
* @since 1.9
*/

class WPV_Condition {
	
	static function on_load() {
        add_action( 'init', array( 'WPV_Condition', 'init' ) );
		add_action( 'admin_init', array( 'WPV_Condition', 'admin_init' ) );
    }

    static function init() {

    }
	
	static function admin_init() {
		
	}
	
}

add_filter( 'wpv-extra-condition-filters', 'wpv_add_time_functions_to_conditionals' );

function wpv_add_time_functions_to_conditionals( $value ) {
    return wpv_filter_parse_date( $value );
}

/**
 * Condition function to evaluate and display given block based on expressions
 * 'args' => arguments for evaluation fields
 * 
 * Supported actions and symbols:
 * 
 * Integer and floating-point numbers
 * Math operators: +, -, *, /
 * Comparison operators: &lt;, &gt;, =, &lt;=, &gt;=, !=
 * Boolean operators: AND, OR, NOT
 * Nested expressions - several levels of brackets
 * Variables defined as shortcode parameters starting with a dollar sign
 * empty() function that checks for blank or non-existing fields
 * 
 * 
 */
function wpv_condition_manage_and_evaluate( $atts, $post_to_check = null ) {
	extract(
        shortcode_atts( 
			array(
				'evaluate' => false, 
				'debug' => false, 
				'condition' => true
			), 
			$atts
		)
    );
	
	$condition = ( $condition == 'true' || $condition === TRUE ) ? true : false;

    // Do not overwrite global post
//    global $post;

    // if in admin, get the post from the URL
    if ( is_admin() ) {
        if ( empty( $post_to_check->ID ) ) {
            // Get post
            if ( isset( $_GET['post'] ) ) {
                $post_id = (int) $_GET['post'];
            } else if ( isset( $_POST['post_ID'] ) ) {
                $post_id = (int) $_POST['post_ID'];
            } else {
                $post_id = 0;
            }
            if ( $post_id ) {
                $post = get_post( $post_id );
            }
        } else {
            $post = $post_to_check;
        }
    }
    if ( empty( $post->ID ) ) {
        global $post;
    }
	$has_post = true;
    if ( empty( $post->ID ) ) {
        // Will not execute any condition that involves custom fields
        $has_post = false;
    }

    global $wplogger;
    
	if ( $has_post ) {
		do_action( 'wpv_condition', $post );
	}

	$logging_string = "####################\nwpv-if attributes\n####################\n" 
		. print_r( $atts, true ) 
		. "\n####################\nDebug information\n####################"
		. "\n--------------------\nOriginal expression: " 
		. $evaluate 
		. "\n--------------------";
		
	$evaluate = str_replace( " NEQ ", " != ", $evaluate );
	$evaluate = str_replace( " neq ", " != ", $evaluate );
	$evaluate = str_replace( " EQ ", " = ", $evaluate );
	$evaluate = str_replace( " eq ", " = ", $evaluate );
	$evaluate = str_replace( " NE ", " != ", $evaluate );
	$evaluate = str_replace( " ne ", " != ", $evaluate );
	
	$evaluate = str_replace( " LT ", " < ", $evaluate );
	$evaluate = str_replace( " lt ", " < ", $evaluate );
	$evaluate = str_replace( " LTE ", " <= ", $evaluate );
	$evaluate = str_replace( " lte ", " <= ", $evaluate );
	$evaluate = str_replace( " GT ", " > ", $evaluate );
	$evaluate = str_replace( " gt ", " > ", $evaluate );
	$evaluate = str_replace( " GTE ", " >= ", $evaluate );
	$evaluate = str_replace( " gte ", " >= ", $evaluate );

    $evaluate = apply_filters( 'wpv-extra-condition-filters', $evaluate );
	
	$logging_string .= "\nAfter expanding custom functions and date expressions: " . $evaluate;

    // Evaluate empty($field) where $field is a custom field key
	if ( $has_post ) {
		$empties = preg_match_all( "/empty\(\s*\\$(\w+)\s*\)/", $evaluate, $matches );
		if ( 
			$empties 
			&& $empties > 0 
		) {
			for ( $i = 0; $i < $empties; $i++ ) {
				$is_empty = '1=0';
				$is_empty_logging_extra = '';
				if ( isset( $atts[$matches[1][$i]] ) ) {
					$match_var = get_post_meta( $post->ID, $atts[$matches[1][$i]], true );
					if ( 
						is_null( $match_var )
						|| ( 
							is_string( $match_var ) 
							&& strlen( $match_var ) == 0 
						)
						|| ( 
							is_array( $match_var ) 
							&& empty( $match_var ) 
						) 
					) {
						$is_empty = '1=1';
						$is_empty_logging_extra = "\n\tField " . $atts[$matches[1][$i]] . " is empty";
					} else {
						$is_empty_logging_extra = "\n\tField " . $atts[$matches[1][$i]] . " is not empty";
					}
				} else {
					$is_empty_logging_extra = "\n\tERROR: Key '" . $matches[1][$i] . "' does not point to a valid attribute in the wpv-if shortcode";
				}
				$evaluate = str_replace( $matches[0][$i], $is_empty, $evaluate );
				$logging_string .= "\nAfter checking " . ( $i + 1 ) . " empty statements: " . $evaluate . $is_empty_logging_extra;
			}
		}
	}
    
    // Evaluate quoted variables that are to be used as strings
    // '$f1' will replace $f1 with the custom field value
	if ( $has_post ) {
		$strings_count = preg_match_all( '/(\'[\$\w^\']*\')/', $evaluate, $matches );
		if ( 
			$strings_count 
			&& $strings_count > 0 
		) {
			for ( $i = 0; $i < $strings_count; $i++ ) {
				$string = $matches[1][$i];
				// remove single quotes from string literals to get value only
				$string = (strpos( $string, '\'' ) === 0) ? substr( $string, 1, strlen( $string ) - 2 ) : $string;
				if ( strpos( $string, '$' ) === 0 ) {
					$quoted_variables_logging_extra = '';
					$variable_name = substr( $string, 1 ); // omit dollar sign
					if ( isset( $atts[$variable_name] ) ) {
						$string = get_post_meta( $post->ID, $atts[$variable_name], true );
						$evaluate = str_replace( $matches[1][$i], "'" . $string . "'", $evaluate );
					} else {
						$evaluate = str_replace( $matches[1][$i], "", $evaluate );
						$quoted_variables_logging_extra = "\n\tERROR: Key " . $matches[1][$i] . " does not point to a valid attribute in the wpv-if shortcode: expect parsing errors";
					}
					$logging_string .= "\nAfter replacing " . ( $i + 1 ) . " quoted variables: " . $evaluate . $quoted_variables_logging_extra;
				}
			}
		}
	}

    // Evaluate non-quoted variables, by de-quoting the quoted ones if needed
    $strings_count = preg_match_all( '/((\$\w+)|(\'[^\']*\'))\s*([\!<>\=]+)\s*((\$\w+)|(\'[^\']*\'))/',
            $evaluate, $matches );

    // get all string comparisons - with variables and/or literals
    if ( 
		$strings_count 
		&& $strings_count > 0 
	) {
        for ( $i = 0; $i < $strings_count; $i++ ) {

            // get both sides and sign
            $first_string = $matches[1][$i];
            $second_string = $matches[5][$i];
            $math_sign = $matches[4][$i];
			
			$general_variables_logging_extra = '';

            // remove single quotes from string literals to get value only
            $first_string = ( strpos( $first_string, '\'' ) === 0 ) ? substr( $first_string, 1, strlen( $first_string ) - 2 ) : $first_string;
            $second_string = ( strpos( $second_string, '\'' ) === 0 ) ? substr( $second_string, 1, strlen( $second_string ) - 2 ) : $second_string;
			$general_variables_logging_extra .= "\n\tComparing " . $first_string . " to " . $second_string;

            // replace variables with text representation
            if ( 
				strpos( $first_string, '$' ) === 0 
				&& $has_post 
			) {
                $variable_name = substr( $first_string, 1 ); // omit dollar sign
                if ( isset( $atts[$variable_name] ) ) {
                    $first_string = get_post_meta( $post->ID, $atts[$variable_name], true );
                } else {
                    $first_string = '';
					$general_variables_logging_extra .= "\n\tERROR: Key " . $variable_name . " does not point to a valid attribute in the wpv-if shortcode";
                }
            }
            if ( strpos( $second_string, '$' ) === 0 && $has_post ) {
                $variable_name = substr( $second_string, 1 );
                if ( isset( $atts[$variable_name] ) ) {
                    $second_string = get_post_meta( $post->ID, $atts[$variable_name], true );
                } else {
                    $second_string = '';
					$general_variables_logging_extra .= "\n\tERROR: Key " . $variable_name . " does not point to a valid attribute in the wpv-if shortcode";
                }
            }

            // don't do string comparison if variables are numbers 
            if ( 
				! ( 
					is_numeric( $first_string ) 
					&& is_numeric( $second_string ) 
				) 
			) {
                // compare string and return true or false
                $compared_str_result = wpv_condition_compare_strings( $first_string, $second_string, $math_sign );

                if ( $compared_str_result ) {
                    $evaluate = str_replace( $matches[0][$i], '1=1', $evaluate );
                } else {
                    $evaluate = str_replace( $matches[0][$i], '1=0', $evaluate );
                }
            } else {
                $evaluate = str_replace( $matches[1][$i], $first_string, $evaluate );
                $evaluate = str_replace( $matches[5][$i], $second_string, $evaluate );
            }
			$logging_string .= "\nAfter replacing " . ( $i + 1 ) . " general variables and comparing strings: " . $evaluate . $general_variables_logging_extra;
        }
    }

    // Evaluate comparisons when at least one of them is numeric
    $strings_count = preg_match_all( '/(\'[^\']*\')/', $evaluate, $matches );
    if ( 
		$strings_count 
		&& $strings_count > 0 
	) {
        for ( $i = 0; $i < $strings_count; $i++ ) {
            $string = $matches[1][$i];
            // remove single quotes from string literals to get value only
            $string = ( strpos( $string, '\'' ) === 0 ) ? substr( $string, 1, strlen( $string ) - 2 ) : $string;
            if ( is_numeric( $string ) ) {
                $evaluate = str_replace( $matches[1][$i], $string, $evaluate );
				$logging_string .= "\nAfter matching " . ( $i + 1 ) . " numeric strings into real numbers: " . $evaluate;
				$logging_string .= "\n\tMatched " . $matches[1][$i] . " to " . $string;
            }
        }
    }


    // Evaluate all remaining variables
	if ( $has_post ) {
		$count = preg_match_all( '/\$(\w+)/', $evaluate, $matches );

		// replace all variables with their values listed as shortcode parameters
		if ( 
			$count 
			&& $count > 0 
		) {
			$logging_string .= "\nRemaining variables: " . var_export( $matches[1], true );
			// sort array by length desc, fix str_replace incorrect replacement
			// wpv_sort_matches_by_length belongs to common/functions.php
			$matches[1] = wpv_sort_matches_by_length( $matches[1] );

			foreach ( $matches[1] as $match ) {
				if ( isset( $atts[$match] ) ) {
					$meta = get_post_meta( $post->ID, $atts[$match], true );
					if ( empty( $meta ) ) {
						$meta = "0";
					}
				} else {
					$meta = "0";
				}
				$evaluate = str_replace( '$' . $match, $meta, $evaluate );
				$logging_string .= "\nAfter replacing remaining variables: " . $evaluate;
			}
		}
	}

    $logging_string .= "\n--------------------\nEnd evaluated expression: " 
		. $evaluate 
		. "\n--------------------";

    $wplogger->log( $logging_string, WPLOG_DEBUG );
    // evaluate the prepared expression using the custom eval script
	// wpv_condition_evaluate_expression
    $result = wpv_condition_evaluate_expression( $evaluate );
    
	if ( $has_post ) {
		do_action( 'wpv_condition_end', $post );
	}
	
	$return = array();
	if ( is_bool( $result ) ) {
		$return['result'] = $result;
		$return['debug'] = $logging_string;
	} else {
		$return['result'] = ! $condition;
		$return['debug'] = $logging_string . "\n" . $result;
	}

    return $return;
}

function wpv_condition_evaluate_expression( $expression ) {
    //Replace AND, OR, ==
    $expression = strtoupper( $expression );
    $expression = str_replace( "AND", "&&", $expression );
    $expression = str_replace( "OR", "||", $expression );
    $expression = str_replace( "NOT", "!", $expression );
    $expression = str_replace( "=", "==", $expression );
    $expression = str_replace( "<==", "<=", $expression );
    $expression = str_replace( ">==", ">=", $expression );
    $expression = str_replace( "!==", "!=", $expression ); // due to the line above
	$expression = str_replace( " EQ ", " == ", $expression );
	$expression = str_replace( " NE ", " != ", $expression );
	$expression = str_replace( " NEQ ", " != ", $expression );
	$expression = str_replace( " LT ", " < ", $expression );
	$expression = str_replace( " LTE ", " <= ", $expression );
	$expression = str_replace( " GT ", " > ", $expression );
	$expression = str_replace( " GTE ", " >= ", $expression );
    // validate against allowed input characters
    $count = preg_match( '/[0-9+-\=\*\/<>&\!\|\s\(\)]+/', $expression, $matches );

    // find out if there is full match for the entire expression	
    if ( $count > 0 ) {
        if ( strlen( $matches[0] ) == strlen( $expression ) ) {
            $valid_eval = wpv_eval_check_syntax( "return $expression;" );
            if ( $valid_eval ) {
                return eval( "return $expression;" );
            } else {
                return __( "Error while parsing the evaluate expression",
                                'wpv-views' );
            }
        } else {
            return __( "Conditional expression includes illegal characters",
                            'wpv-views' );
        }
    } else {
        return __( "Correct conditional expression has not been found",
                        'wpv-views' );
    }

}

function wpv_condition_compare_strings( $first, $second, $sign ) {
    // get comparison results
    $comparison = strcmp( $first, $second );

    // verify cases 'less than' and 'less than or equal': <, <=
    if ( 
		$comparison < 0 
		&& (
			$sign == '<' 
			|| strtoupper( $sign ) == 'LT'
			|| $sign == '<='
			|| strtoupper( $sign ) == 'LTE'
		) 
	) {
        return true;
    }

    // verify cases 'greater than' and 'greater than or equal': >, >=
    if ( 
		$comparison > 0 
		&& (
			$sign == '>' 
			|| strtoupper( $sign ) == 'GT'
			|| $sign == '>='
			|| strtoupper( $sign ) == 'GTE'
		) 
	) {
        return true;
    }

    // verify equal cases: =, <=, >=
    if ( $comparison == 0 && ($sign == '=' || $sign == '<=' || $sign == '>=') ) {
        return true;
    }

    // verify != case
    if ( $comparison != 0 && $sign == '!=' ) {
        return true;
    }

    // or result is incorrect
    return false;
}

/**
 * Views-Shortcode: wpv-if
 *
 * Description: Conditional shortcode to be used to display a specific area
 * based on a custom field condition. \n
 * Supported actions and symbols:\n
 * Integer and floating-point numbers \n
 * Math operators: +, -, *, / \n
 * Comparison operators: &lt;, &gt;, =, &lt;=, &gt;=, != \n
 * Boolean operators: AND, OR, NOT \n
 * Nested expressions - several levels of parentheses \n
 * Variables defined as shortcode parameters starting with a dollar sign \n
 * empty() function that checks for empty or non-existing fields
 *
 * Parameters:
 * 'condition' => Define expected result from evaluate - either true or false
 * 'evaluate' => Evaluate expression with fields involved, sample use: "($field1 > $field2) AND !empty($field3)"
 * 'debug' => Enable debug to display error messages in the shortcode 
 * 'fieldX' => Define fields to be taken into account during evaluation 
 *
 * Example usage:
 * [wpv-if evaluate="boolean condition"]
 *    Execute code for true
 * [/wpv-if]
 * Sing a variable and comparing its value to a constant
 * [wpv-if f1="wpcf-condnum1" evaluate="$f1 = 1" debug="true"]Number1=1[/wpv-if]
 * Two numeric variables in a mathematical expression with boolean operators
 * [wpv-if f1="wpcf-condnum1" f2="wpcf-condnum2" evaluate="(2 < 3 AND (((3+$f2)/2) > 3 OR NOT($f1 > 3)))" debug="true"]Visible block[/wpv-if]
 * Compare custom field with a value
 * [wpv-if f1="wpcf-condstr1" evaluate="$f1 = 'My text'" debug="true"]Text1='My text' [/wpv-if]
 * Display condition if evaluates to false (use instead of else-if)
 * [wpv-if condition="false" evaluate="2 > 3"] 2 > 3 [/wpv-if]
 * Custom function support
 * [wpv-if evaluate="my_func() = '1'"]my_func returns 1[/wpv-if]
 *
 * Link:
 * <a href="http://wp-types.com/documentation/user-guides/conditional-html-output-in-views/?utm_source=viewsplugin&utm_campaign=views&utm_medium=views-conditional-help-link&utm_term=Conditional HTML output in Views">Conditional HTML output in Views</a>
 *
 * Note:
 *
 */

function wpv_shortcode_wpv_if( $args, $content ) {
	$evaluation_result = wpv_condition_manage_and_evaluate( $args );
    extract(
        shortcode_atts( 
			array(
				'evaluate' => false, 
				'debug' => false, 
				'condition' => true
			), 
			$args
		)
    );
    $condition = ( $condition == 'true' || $condition === TRUE ) ? true : false;
	$out = '';
    
    if (
		( 
			$evaluation_result['result'] === true 
			&& $condition 
		) 
		|| ( 
			$evaluation_result['result'] === false 
			&& ! $condition 
		)
	) {
		
		if ( strpos( $content, 'wpv-b64-' ) === 0) {
			$content = substr( $content, 7 );
			$content = base64_decode( $content );
		}

    	$out = wpv_do_shortcode( $content );
    	apply_filters('wpv_shortcode_debug','wpv-if', json_encode( $args ), '', 'Conditional output: evaluated to true', $out );
    } else {
		$out = '';
    	if ( 
			( 
				$evaluation_result['result'] === false 
				&& $condition
			) 
			|| (
				$evaluation_result['result'] === true 
				&& ! $condition
			) 
		) {
			apply_filters('wpv_shortcode_debug','wpv-if', json_encode( $args ), '', 'Conditional output: evaluated to false');
    	} else {
			apply_filters('wpv_shortcode_debug','wpv-if', json_encode( $args ), '', 'Conditional output: error', $evaluation_result['debug'] );
    	}
    }
	if ( 
		$debug 
		&& current_user_can( 'manage_options' )
	) {
		$out .= '<pre>' . $evaluation_result['debug'] . '</pre>';
	}
	return $out;
}

add_shortcode('wpv-if', 'wpv_shortcode_wpv_if');

//////////////////////////////
//////////////////////////////
/**
 * Handle wpv-if inside wpv-if
 *
 */
//////////////////////////////
//////////////////////////////

// @todo this seems to be used somewhere else, watch out before deprecate!! Maybe keep for 3rd party backwards compatibility
function wpv_resolve_wpv_if_shortcodes($content) {
	$content = wpv_parse_wpv_if_shortcodes($content);
	
	return $content;
}

/**
 * Search for the inner [wpv-if] [/wpv-if] pairs and process the inner ones first
 */

function wpv_parse_wpv_if_shortcodes($content) {
	global $shortcode_tags;

	// Back up current registered shortcodes and clear them all out
	$orig_shortcode_tags = $shortcode_tags;
	remove_all_shortcodes();

	// only do wpv-if				
	add_shortcode('wpv-if', 'wpv_shortcode_wpv_if');

	$expression = '/\\[wpv-if((?!\\[wpv-if).)*\\[\\/wpv-if\\]/isU';
	$counts = preg_match_all($expression, $content, $matches);

	while ($counts) {
		foreach($matches[0] as $match) {

			// this will only processes the [wpv-if] shortcode
			$pattern = get_shortcode_regex();
			$match_corrected = $match;
			if ( 0 !== preg_match( "/$pattern/s", $match, $match_data ) ) {
				// Base64 Encode the inside part of the expression so the WP can't strip out any data it doesn't like.
				// Be sure to prevent base64_encoding more than just the needed: only do it if there are inner shortcodes
				if ( strpos( $match_data[5], '[' ) !== false ) {
					$match_corrected = str_replace( $match_data[5], 'wpv-b64-' . base64_encode( $match_data[5] ), $match_corrected );
				}
				
				$match_attributes = wpv_shortcode_parse_condition_atts( $match_data[3] );
				if ( isset( $match_attributes['evaluate'] ) ) {
					$match_evaluate_corrected = str_replace( '<=', 'LTE', $match_attributes['evaluate'] );
					$match_evaluate_corrected = str_replace( '<>', '!=', $match_evaluate_corrected );
					$match_evaluate_corrected = str_replace( '<', 'LT', $match_evaluate_corrected );
					$match_corrected = str_replace( $match_attributes['evaluate'], $match_evaluate_corrected, $match_corrected );
				}
				
			}
			
			$shortcode = do_shortcode($match_corrected);
			$content = str_replace($match, $shortcode, $content);
			
		}
		
		$counts = preg_match_all($expression, $content, $matches);
	}

	// Put the original shortcodes back
	$shortcode_tags = $orig_shortcode_tags;
	
	return $content;
}

// Special handling to get shortcodes rendered in widgets.
function wpv_resolve_wpv_if_shortcodes_for_widgets( $content ) {
	$content = wpv_parse_wpv_if_shortcodes( $content );
	// @todo check whether this needs wpv_do_shortcode instead based on the 4.2.3 change
	return do_shortcode( $content );
}

add_filter('widget_text', 'wpv_resolve_wpv_if_shortcodes_for_widgets');

/**
* wpv_add_wpv_if_functions_support
*
* Add filter to "wpv-extra-condition-filters" located in embedded/common/functions.php
* This will add support for custom functions in wpv-if shortcodes
* Added priority of 11 to be run just after the filter "wpv_add_time_functions_to_conditionals"
*
* Syntax:
* [wpv-if evaluate="function('arg1', 'arg2', ...) = 'value'"] ... [/wpv-if]
*
* @param function
* - function name is used without quotes
* - function name can be also a public method of a class using the syntax my_class::my_method()
*   | Example: evaluate="my_class::my_method('arg1', 'arg2', ...) = 'value'"
* - function wll be passed with the current object type queried by the View ('posts' default |'taxonomy'|'users) and the object itself (defaults to the current displayed post) as its last two parameters
*
* @param function arguments
* - function can take as arguments the following types or combination of types:
*   1. string - wrapped in single quotes
*   | Example: evaluate="wpv_my_custom_function('arg1', 'arg2', ...) = 'value'"
*   2. number - not wrapped
*   | Example: evaluate="wpv_my_custom_function(1, 2, ...) = 'value'"
*   3. boolean - not wrapped
*   | Example: evaluate="wpv_my_custom_function(true, false, ...) = 'value'"
*   4. null - not wrapped
*   | Example: evaluate="wpv_my_custom_function(true, null, ...) = 'value'"
*
* @param funcion returns
* - function must return a string, a number or a boolean
* - if function returns nothing, null, an array or an object, debug will return "Conditional expression includes illegal characters"
* - if function does not exist, debug will return "Conditional expression includes illegal characters"
* - if function is called without mandatory arguments you can expect different results depending on the function flow and nice PHP notices and warnings :-)
*
* @param comparison_function
* - you can use all the comparison functions that wpv-if supports: = != < > <= <=
*   | Example: evaluate="wpv_my_custom_function('arg1', 'arg2', ...) != 'my_value'"
* - you can use all the math operators that wpv-if supports: + - * /
*   | Example: evaluate="wpv_my_custom_function('arg1', 'arg2', ...) * 2 < '10'"
*
* @param value
* - value must be wrapped in single quotes if you expect the function to return a string
* - value can be wrapped in single quotes if you expect the function to return a number, but it's not mandatory
* - value will be 1 or '1' for true and 0 or '0' for false if you expect the function to return a boolean
* - value can be '' if you need to check if the function returns an empty string
*
*/

add_filter( 'wpv-extra-condition-filters', 'wpv_add_wpv_if_functions_support', 11 );

function wpv_add_wpv_if_functions_support($evaluate) {
	$occurences = preg_match_all('/(\\w+:?:?\\w+?)\(([\\w",. \'-]*)\)/', $evaluate, $matches);
	if ( $occurences > 0 ) {
		global $WPV_settings;
		$allowed_functions = array();
		if ( 
			isset( $WPV_settings->wpv_custom_conditional_functions ) 
			&& is_array( $WPV_settings->wpv_custom_conditional_functions ) 
		) {
			$allowed_functions = $WPV_settings->wpv_custom_conditional_functions;
		}
		
		/**
		* wpv_filter_wpv_custom_conditional_functions
		*
		* Extend or modify the list of allowed functions to be usd inside wpv-if shortcodes.
		* Used to automatically register Views API functions.
		*
		* @since 1.8.0
		*/
		
		$allowed_functions = apply_filters( 'wpv_filter_wpv_custom_conditional_functions', $allowed_functions );
		for ( $i = 0; $i < $occurences; $i++ ) {
			$real_func = $matches[1][$i];
			$real_function_trimmed = trim( $real_func );
			if ( !in_array( $real_function_trimmed, $allowed_functions ) ) {
				return $evaluate;
			}
			if ( strpos( $real_func, '::' ) != false ) {
				$real_func = array_map('trim', explode('::', $real_func));
			}
			$real_values = $matches[2][$i];
			if ( is_callable($real_func ) ) {
				global $WP_Views, $post;
				$view_depth = $WP_Views->view_depth;
				$query_type = 'posts';
				$object = $post;
				$func_args = array();
				$resulting_thing = null;
				if ( !empty( $real_values ) ) {
					$func_args = array_map( 'trim', explode( ',', $real_values ) );
				}
				$func_args = array_map('wpv_if_func_booleans', $func_args);
				if ( $view_depth > 0 ) {
					$view_settings = $WP_Views->get_view_settings();
					if ( isset( $view_settings['query_type'] ) && isset( $view_settings['query_type'][0] ) ) {
						if ( $view_settings['query_type'][0] == 'taxonomy' ) {
							$query_type = 'taxonomy';
							$object = $WP_Views->taxonomy_data['term'];
						} else if ( $view_settings['query_type'][0] == 'users' ) {
							$query_type = 'users';
							$object = $WP_Views->users_data['term'];
						}
					}
				}
				array_push($func_args, $query_type);
				array_push($func_args, $object);
				$resulting_thing = call_user_func_array( $real_func, $func_args );
				if ( isset( $resulting_thing ) && !is_array( $resulting_thing ) && !is_object( $resulting_thing ) ) {
					$replace = null;
					if ( is_bool( $resulting_thing ) ) {
						if ( $resulting_thing ) {
							$replace = 1;
						} else {
							$replace = 0;
						}
					} else if ( is_numeric( $resulting_thing ) ) {
						$replace = $resulting_thing;
						if ( $replace == 1 ) {
							$replace = "'1'";
						}
					} else {
						$replace = "'" . $resulting_thing . "'";
					}
					$evaluate = str_replace($matches[0][$i], $replace, $evaluate);
				}
			}
		}
	}
	return $evaluate;
}

/**
* wpv_if_register_api_functions
*
* Extend the functions registered by the user to include the Views API conditional tags
*
* @since 1.8.0
*/

add_filter( 'wpv_filter_wpv_custom_conditional_functions', 'wpv_if_register_api_functions', 10, 1 );

function wpv_if_register_api_functions( $allowed_functions ) {
	if ( ! in_array( 'has_wpv_wp_archive', $allowed_functions ) ) {
		$allowed_functions[] = 'has_wpv_wp_archive';
	}
	if ( ! in_array( 'is_wpv_wp_archive_assigned', $allowed_functions ) ) {
		$allowed_functions[] = 'is_wpv_wp_archive_assigned';
	}
	if ( ! in_array( 'has_wpv_content_template', $allowed_functions ) ) {
		$allowed_functions[] = 'has_wpv_content_template';
	}
	if ( ! in_array( 'is_wpv_content_template_assigned', $allowed_functions ) ) {
		$allowed_functions[] = 'is_wpv_content_template_assigned';
	}
	return $allowed_functions;
}

/**
* Helper function to transform strings into booleans and to clean strings of outtr single quotes
*/

function wpv_if_func_booleans($string) {
	if ( $string == 'true' ) {
		return true;
	} else if ( $string == 'false' ) {
		return false;
	} else if ( $string == 'null' ) {
		return null;
	} else {
		$string = (strpos( $string, '\'' ) === 0) ? substr( $string, 1, strlen( $string ) - 2 ) : $string;
		return $string;
	}
}