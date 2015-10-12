<?php
/**
 * Filter the_content tag 
 * Added support for internal shortcode execution
 * This handles Types shortcodes within other shortcodes
 * eg.  [app [types field="my_field"]]
 */

function wpv_resolve_internal_shortcodes($content) {
	$content = wpv_parse_content_shortcodes($content);
	
	return $content;
}

/**
 * Parse shortcodes in the page content
 * @param string page content to be evaluated for internal shortcodes
 */
function wpv_parse_content_shortcodes($content) {
	global $WPV_settings;
    
    $views_shortcodes_regex = wpv_inner_shortcodes_list_regex();
    
	$inner_expressions = array();
	$inner_expressions[] = "/\\[types.*?\\].*?\\[\\/types\\]/i";    
	$inner_expressions[] = "/\\[(". $views_shortcodes_regex .").*?\\]/i";
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
			$inner_expressions[] = "/\\[" . $custom_inner_shortcode . ".*?\\].*?\\[\\/" . $custom_inner_shortcode . "\\]/i";
		}
		$inner_expressions[] = "/\\[(" . implode( '|', $custom_inner_shortcodes ) . ").*?\\]/i";
	}
	// search for shortcodes
	$matches = array();
	$counts = _find_outer_brackets($content, $matches);
	
	// iterate 0-level shortcode elements
	if($counts > 0) {
		foreach($matches as $match) {
			
			foreach ($inner_expressions as $inner_expression) {
				$inner_counts = preg_match_all($inner_expression, $match, $inner_matches);
				
				// replace all 1-level inner shortcode matches
				if($inner_counts > 0) {
					foreach($inner_matches[0] as &$inner_match) {
						// execute shortcode content and replace
						$resolved_match = wpv_preprocess_shortcodes_in_html_elements($inner_match);
						$filter_state = new WPV_WP_filter_state( 'the_content' );
						$resolved_match = do_shortcode( $resolved_match );
						$filter_state->restore();
						$content = str_replace($inner_match, $resolved_match, $content);
						$match = str_replace($inner_match, $resolved_match, $match);
					}
				}
			}
		}
	}
	
	return $content;
}

function _find_outer_brackets($content, &$matches) {
	$count = 0;
	
	$first = strpos($content, '[');
	if ($first !== FALSE) {
		$length = strlen($content);
		$brace_count = 0;
		$brace_start = -1;
		for ($i = $first; $i < $length; $i++) {
			if ($content[$i] == '[') {
				if($brace_count == 0) {
					$brace_start = $i + 1;
				}
				$brace_count++;
			}
			if ($content[$i] == ']') {
				if ($brace_count > 0) {
					$brace_count--;
					if ($brace_count == 0) {
						$matches[] = substr($content, $brace_start, $i - $brace_start);
						$count++;
					}
				}
			}
		}
	}
	
	return $count;
}


// Special handling to get shortcodes rendered in widgets.
function wpv_resolve_internal_shortcodes_for_widgets($content) {
	$content = wpv_parse_content_shortcodes($content);
	
	return do_shortcode($content);
}

add_filter('widget_text', 'wpv_resolve_internal_shortcodes_for_widgets', 9, 1);