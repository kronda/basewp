<?php

/**
* WPV_Frontend_Render_Filters
*
* Pre-process Views shortcodes in several scenarios.
*
* This helper class provides a single callback to parse Views shortcodes in a fixed order:
* 	- Resolve wpv-for-each shortcodes along.
* 	- Resolve shortcodes inside shortcodes (see wpv-shortcodes-in-shortcodes.php).
* 	- Resolve wpv-if shortcodes (see wpv-condition.php).
* 	- Resolve shortcodes used as HTML attributes.
* Note that this same calbacks in the same order are applied in the the_content, in the wpv_filter_wpv_the_content_suppressed and in the wpv-pre-do-shortcode filters.
* Also note that they are executed early, priority 5, to keep compatibility with third parties doing the same at 7.
*
* @since 1.9.1
*/

WPV_Frontend_Render_Filters::on_load();

class WPV_Frontend_Render_Filters {
	
	static function on_load() {
		add_filter( 'the_content', array( 'WPV_Frontend_Render_Filters', 'the_content' ), 5 );
		add_filter( 'wpv_filter_wpv_the_content_suppressed', array( 'WPV_Frontend_Render_Filters', 'the_content' ), 5 );
		add_filter( 'wpv-pre-do-shortcode', array( 'WPV_Frontend_Render_Filters', 'wpv_pre_do_shortcode' ), 5 );
	}
	
	static function the_content( $content ) {
		
		$content = WPV_Formatting_Embedded::resolve_wpv_noautop_shortcodes( $content );
		
		$content = wpv_preprocess_foreach_shortcodes( $content );
		
		$content = wpv_resolve_internal_shortcodes( $content );
		
		$content = wpv_preprocess_wpv_conditional_shortcodes( $content );
		
		$content = wpv_resolve_wpv_if_shortcodes( $content );
		
		$content = wpv_preprocess_shortcodes_in_html_elements( $content );
		
		return $content;
	}
	
	static function wpv_pre_do_shortcode( $content ) {
		
		$content = WPV_Formatting_Embedded::resolve_wpv_noautop_shortcodes( $content );
		
		$content = wpv_preprocess_foreach_shortcodes( $content );
		
		$content = wpv_parse_content_shortcodes( $content );
		
		$content = wpv_preprocess_wpv_conditional_shortcodes( $content );
		
		$content = wpv_parse_wpv_if_shortcodes( $content );
		
		$content = wpv_preprocess_shortcodes_in_html_elements( $content );
		
		return $content;
	}
	
}

/*
* wpv_inner_shortcodes_list_regex
* return regular expression for shortcode_inside_shortcode allowed shortcodes and wpv-conditional shortcodes list
*/

function wpv_inner_shortcodes_list_regex() {
    $regex = 'wpv-post-|wpv-taxonomy-|types|wpv-current-user|wpv-user|wpv-attribute|wpv-archive-title|wpv-bloginfo|'.
        'wpv-found-count|wpv-items-count|wpv-pager|wpv-posts-found|wpv-search-term|wpv-view';
    return $regex;
}

/**
* wpv_preprocess_shortcodes_in_html_elements
*
* Processes Views shortcodes inside HTML attributes, fixing a compatibility issue with WordPress 4.2.3 and beyond.
* Heavily inspired in do_shortcodes_in_html_tags.
*
* @since 1.9.1
*/

function wpv_preprocess_shortcodes_in_html_elements( $content ) {
	global $WPV_settings;
	$views_shortcodes_regex = wpv_inner_shortcodes_list_regex();
	$inner_expressions = array();
	$inner_expressions[] = array(
								 'regex'       => "/\\[types.*?\\]\\[\\/types\\]/i",
								 'has_content' => false
								);
	$inner_expressions[] = array(
								 'regex'       => "/\\[types.*?\\](.*?)\\[\\/types\\]/i",
								 'has_content' => true
								);
	$inner_expressions[] = array(
								 'regex'       => "/\\[(". $views_shortcodes_regex .").*?\\]/i",
								 'has_content' => false
								);
	
	// support for custom inner shortcodes via settings page
	// since 1.4
	$custom_inner_shortcodes = array();
	if ( isset( $WPV_settings->wpv_custom_inner_shortcodes ) && is_array( $WPV_settings->wpv_custom_inner_shortcodes ) ) {
		$custom_inner_shortcodes = $WPV_settings->wpv_custom_inner_shortcodes;
	}
	// wpv_custom_inner_shortcodes filter
	// since 1.4
	// takes an array of shortcodes and returns an array of shortcodes
	$custom_inner_shortcodes = apply_filters( 'wpv_custom_inner_shortcodes', $custom_inner_shortcodes );
	// remove duplicates
	$custom_inner_shortcodes = array_unique( $custom_inner_shortcodes );
	// add the custom inner shortcodes, whether they are self-closing or not
	if ( sizeof( $custom_inner_shortcodes ) > 0 ) {
		foreach ( $custom_inner_shortcodes as $custom_inner_shortcode ) {
			$inner_expressions[] = array(
										 'regex'       => "/\\[" . $custom_inner_shortcode . ".*?\\](.*?)\\[\\/" . $custom_inner_shortcode . "\\]/is",
										 'has_content' => true
										);
		}
		$inner_expressions[] = array(
									 'regex' => "/\\[(" . implode( '|', $custom_inner_shortcodes ) . ").*?\\]/i",
									 'has_content' => false
									);
	}
			
			
	// Normalize entities in unfiltered HTML before adding placeholders.
	$trans = array( '&#91;' => '&#091;', '&#93;' => '&#093;' );
	$content = strtr( $content, $trans );
	
	$textarr = wpv_html_split( $content );

	foreach ( $textarr as &$element ) {
		if ( '' == $element || '<' !== $element[0] ) {
			continue;
		}

		$noopen = false === strpos( $element, '[' );
		$noclose = false === strpos( $element, ']' );
		if ( $noopen || $noclose ) {
			// This element does not contain shortcodes.
			continue;
		}

		if ( '<!--' === substr( $element, 0, 4 ) || '<![CDATA[' === substr( $element, 0, 9 ) ) {
			continue;
		}
		
		foreach ( $inner_expressions as $shortcode ) {
			$counts = preg_match_all( $shortcode[ 'regex' ], $element, $matches );
			
			if ( $counts > 0 ) {
				foreach ( $matches[0] as $index => &$match ) {
					
					// We need to exclude wpv-post-body here otherwise
					// wpautop can be applied to it too soon.
					
					if ( strpos( $match, '[wpv-post-body' ) !== 0 ) {
						$string_to_replace = $match;
						
						// execute shortcode content and replace
						
						if ( $shortcode[ 'has_content' ] ) {
							$inner_content = $matches[1][ $index ];
							if ( $inner_content ) {
								$new_inner_content = wpv_preprocess_shortcodes_in_html_elements( $inner_content );
								$match = str_replace( $inner_content, $new_inner_content, $match );
							}
						}
						$filter_state = new WPV_WP_filter_state( 'the_content' );
						$replacement = do_shortcode( $match );
						$filter_state->restore();
						$resolved_match = $replacement;
						$element = str_replace( $string_to_replace, $resolved_match, $element );
					}
				}
			}
		}
		
	}
	
	$content = implode( '', $textarr );
	
	return $content;
}

/**
* wpv_preprocess_foreach_shortcodes
*
* Processes wpv-for-each shortcodes ahead of time, adding index attributes to wpv-post-field and types inner shortcodes.
*
* @since 1.9.1
*/


function wpv_preprocess_foreach_shortcodes($content) {
	global $shortcode_tags;
	// Back up current registered shortcodes and clear them all out
	$orig_shortcode_tags = $shortcode_tags;
	remove_all_shortcodes();			
	add_shortcode( 'wpv-for-each', 'wpv_for_each_shortcode' );
	$expression = "/\\[wpv-for-each.*?\\](.*?)\\[\\/wpv-for-each\\]/is";
	$counts = preg_match_all( $expression, $content, $matches );
	while ( $counts ) {
		foreach( $matches[0] as $index => $match ) {
			// encode the data to stop WP from trying to fix it.
			$match_encoded = str_replace( $matches[ 1 ][ $index ], 'wpv-b64-' . base64_encode( $matches[ 1 ][ $index ] ), $match );
			$shortcode = do_shortcode( $match_encoded );
			$content = str_replace( $match, $shortcode, $content );
		}
		$counts = preg_match_all( $expression, $content, $matches );
	}
	$shortcode_tags = $orig_shortcode_tags;		
	
	return $content;
}

/**
* Separate HTML elements and comments from the text. Needed for wpv_preprocess_shortcodes_in_html_elements.
*
* Heavily inspired in wp_html_split
*
* @param string $input The text which has to be formatted.
* @return array The formatted text.
*
* @since 1.10
*/
function wpv_html_split( $input ) {
	static $regex;

	if ( ! isset( $regex ) ) {
		$comments =
			  '!'           // Start of comment, after the <.
			. '(?:'         // Unroll the loop: Consume everything until --> is found.
			.     '-(?!->)' // Dash not followed by end of comment.
			.     '[^\-]*+' // Consume non-dashes.
			. ')*+'         // Loop possessively.
			. '(?:-->)?';   // End of comment. If not found, match all input.

		$cdata =
			  '!\[CDATA\['  // Start of comment, after the <.
			. '[^\]]*+'     // Consume non-].
			. '(?:'         // Unroll the loop: Consume everything until ]]> is found.
			.     '](?!]>)' // One ] not followed by end of comment.
			.     '[^\]]*+' // Consume non-].
			. ')*+'         // Loop possessively.
			. '(?:]]>)?';   // End of comment. If not found, match all input.

		$regex =
			  '/('              // Capture the entire match.
			.     '<'           // Find start of element.
			.     '(?(?=!--)'   // Is this a comment?
			.         $comments // Find end of comment.
			.     '|'
			.         '(?(?=!\[CDATA\[)' // Is this a comment?
			.             $cdata // Find end of comment.
			.         '|'
			.             '[^>]*>?' // Find end of element. If not found, match all input.
			.         ')'
			.     ')'
			. ')/s';
	}

	return preg_split( $regex, $input, -1, PREG_SPLIT_DELIM_CAPTURE );
}

function wpv_shortcode_parse_condition_atts( $text ) {
	$atts = array();
	$pattern = '/([\w-]+)\s*=\s*"([^"]*)"(?:\s|$)|([\w-]+)\s*=\s*\'([^\']*)\'(?:\s|$)|([\w-]+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
	$text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
	if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {
		foreach ($match as $m) {
			if (!empty($m[1]))
				$atts[strtolower($m[1])] = stripcslashes($m[2]);
			elseif (!empty($m[3]))
				$atts[strtolower($m[3])] = stripcslashes($m[4]);
			elseif (!empty($m[5]))
				$atts[strtolower($m[5])] = stripcslashes($m[6]);
			elseif (isset($m[7]) && strlen($m[7]))
				$atts[] = stripcslashes($m[7]);
			elseif (isset($m[8]))
				$atts[] = stripcslashes($m[8]);
		}
		// Reject any unclosed HTML elements to help protect plugins.
		foreach( $atts as $key => $value ) {
			if ( 
				$key != 'evaluate' 
				&& $key != 'if'
			) {
				if ( false !== strpos( $value, '<' ) ) {
					if ( 1 !== preg_match( '/^[^<]*+(?:<[^>]*+>[^<]*+)*+$/', $value ) ) {
						$value = '';
					}
				}
				
			}
			$atts[ $key ] = $value;
		}
	} else {
		$atts = ltrim($text);
	}
	return $atts;
}
