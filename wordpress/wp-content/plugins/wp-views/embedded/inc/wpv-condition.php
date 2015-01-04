<?php

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
 * <a href="http://wp-types.com/documentation/user-guides/conditional-html-output-in-views/">Conditional HTML output in Views</a>
 *
 * Note:
 *
 */

function wpv_shortcode_wpv_if($args, $content) {
    $result = wpv_condition($args);
    
    extract(
        shortcode_atts( array('evaluate' => FALSE, 'debug' => FALSE, 'condition' => TRUE), $args)
    );
    $condition = ($condition == 'true' || $condition === TRUE) ? true : false;
    
 	// show the view area if condition corresponds to the evaluate returned result 1=1 or 0=0
    if(($result === true && $condition) || ($result === false && !$condition)) {
    	$out = wpv_do_shortcode($content);
    	apply_filters('wpv_shortcode_debug','wpv-if', json_encode($args), '', 'Conditional output: evaluated to true', $out);
    	return $out;
    }
    else {
    	// output empty string or the error message if debug is true
    	// empty for different condition and evaluate result
    	if(($result === false && $condition) || ($result === true && !$condition) ) {
			apply_filters('wpv_shortcode_debug','wpv-if', json_encode($args), '', 'Conditional output: evaluated to false');
    		return '';
    	}
    	else {
    		if($debug) {
    			return $result;
    		} else {
				return '';
    		}
    		apply_filters('wpv_shortcode_debug','wpv-if', json_encode($args), '', 'Conditional output: error', $result);
    	}
    }
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

function wpv_resolve_wpv_if_shortcodes($content) {
	$content = wpv_parse_wpv_if_shortcodes($content);
	
	return $content;
}

// adding filter with priority before do_shortcode and other WP standard filters
add_filter('the_content', 'wpv_resolve_wpv_if_shortcodes', 9);

/**
 * Search for the inner [wpv-if] [/wpv-if] pairs and process the inner ones first
 * TODO: see if we can have wpv-if inside wpv-for-each working
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
			$shortcode = do_shortcode($match);
			$content = str_replace($match, $shortcode, $content);
			
		}
		
		$counts = preg_match_all($expression, $content, $matches);
	}

	// Put the original shortcodes back
	$shortcode_tags = $orig_shortcode_tags;
	
	return $content;
}

// register filter for the wpv_do_shortcode Views rendering
add_filter('wpv-pre-do-shortcode', 'wpv_parse_wpv_if_shortcodes');


// Special handling to get shortcodes rendered in widgets.
function wpv_resolve_wpv_if_shortcodes_for_widgets($content) {
	$content = wpv_parse_wpv_if_shortcodes($content);
	
	return do_shortcode($content);
}

add_filter('widget_text', 'wpv_resolve_wpv_if_shortcodes_for_widgets');

/**
* wpv_add_wpv_if_functions_support
*
* Add filter to "wpv-extra-condition-filters" located in embedded/common/functions.php
* This will add support for custom functions in wpv-if shortcodes
* Added priority of 11 to be run just after the filter "wpv_add_time_functions"
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
	if($occurences > 0) {
		for($i = 0; $i < $occurences; $i++) {
			$real_func = $matches[1][$i];
			if ( strpos( $real_func, '::' ) != false ) {
				$real_func = array_map('trim', explode('::', $real_func));
			}
			$real_values = $matches[2][$i];
			if ( is_callable($real_func ) ) {
				global $WP_Views;
				$view_settings = $WP_Views->get_view_settings();
				$query_type = null;
				$object = null;
				$func_args = array();
				$resulting_thing = null;
				if ( !empty( $real_values ) ) $func_args = array_map('trim', explode(',', $real_values));
				$func_args = array_map('wpv_if_func_booleans', $func_args);
				if ( isset( $view_settings['query_type'] ) && isset( $view_settings['query_type'][0] ) ) {
					if ($view_settings['query_type'][0] == 'posts') {
						$query_type = 'posts';
						global $post;
						$object = $post;
					}
					else if ($view_settings['query_type'][0] == 'taxonomy') {
						$query_type = 'taxonomy';
						$object = $WP_Views->taxonomy_data['term'];
					}
				}//print_r($func_args);
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