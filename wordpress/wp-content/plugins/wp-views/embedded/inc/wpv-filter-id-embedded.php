<?php

/**
 * Add a filter to add the query by author to the $query
 */

add_filter('wpv_filter_query', 'wpv_filter_post_id', 13, 2); // we need to set a higher priority than the limit filter has because we use $query['post__in'] = array('0') on failure
function wpv_filter_post_id($query, $view_settings) {
// TEST are IDs adjusted in WPML? Maybe yes, because it filters the post__in and post__not_in arguments
	global $WP_Views;

	if (isset($view_settings['id_mode'][0])) {
		global $wpdb;
		$id_parameter = '';
		$show_id_array = array();
		$id_shortcode = '';
		$id_ids_list = '';
		$include = true;
		
		if ( isset( $view_settings['id_in_or_out'] ) && 'out' == $view_settings['id_in_or_out'] ) {
			$include = false;
		}
        
		if ($view_settings['id_mode'][0] == 'by_ids') {
			if (isset($view_settings['post_id_ids_list']) && '' != $view_settings['post_id_ids_list']) {
				$id_ids_list = $view_settings['post_id_ids_list'];
			}
			if ( '' != $id_ids_list){
				$id_ids_list = explode(',', $id_ids_list);
				
				for ( $i = 0; $i < count($id_ids_list); $i++){
					$show_id_array[] = (int) trim( $id_ids_list[$i] );	
				}
			}
			else {
					$show_id_array = null; // if the list of IDs is empty
			}
		//	$include = false;
		}
		
		if ($view_settings['id_mode'][0] == 'by_url') {
			if (isset($view_settings['post_ids_url']) && '' != $view_settings['post_ids_url']) {
				$id_parameter = $view_settings['post_ids_url'];	
			}
			if ('' != $id_parameter) {
				if (isset($_GET[$id_parameter])) {  // if the URL parameter is present
					$ids_to_load = $_GET[$id_parameter]; // get the array of possible authors from the URL parameter
					if ( is_array( $ids_to_load ) ){
						for ( $i = 0; $i < count($ids_to_load ); $i++){
							$show_id_array[] = (int) trim( $ids_to_load[$i] );	
						}
					}
					else{
						$show_id_array[] = $ids_to_load;	
					}
				} else {
					$show_id_array = null; // if the URL parameter is missing
				}
			}
		}
		
		if ($view_settings['id_mode'][0] == 'shortcode') {
			if (isset($view_settings['post_ids_shortcode']) && '' != $view_settings['post_ids_shortcode']) {
				$id_shortcode = $view_settings['post_ids_shortcode'];	
			}
			if ('' != $id_shortcode) {
				$view_attrs = $WP_Views->get_view_shortcodes_attributes();
				if (isset($view_attrs[$id_shortcode])) { // if the defined shortcode attribute is present
					$ids_to_load = explode(',', $view_attrs[$id_shortcode]); // allow for multiple ids
					if ( count( $ids_to_load ) > 0 ){
						for ( $i = 0; $i < count( $ids_to_load ); $i++){
							$show_id_array[] = (int) trim( $ids_to_load[$i] );	
						}
					}
				} else {
					$show_id_array = null; // if the shortcode attribute is missing
				}
			}
		}
		
        
		// TODO check if needed the ignore_sticky_posts argument here, seems not because we are adding it by default
		// See: http://codex.wordpress.org/Class_Reference/WP_Query#Post_.26_Page_Parameters
		if ( isset( $show_id_array ) ) { // only modify the query if the URL parameter is present and not empty
			if ( count( $show_id_array ) > 0 ) {
				if ( $include ) {
					if ( isset( $query['post__in'] ) ) {
						$query['post__in'] = array_merge( (array)$query['post__in'], $show_id_array );
					} else {
						$query['post__in'] = $show_id_array;
					}
				} else {
					if ( isset( $query['post__not_in'] ) ) {
						$query['post__not_in'] = array_merge( (array)$query['post__not_in'], $show_id_array );
					} else {
						$query['post__not_in'] = $show_id_array;
					}
				}
			} else {
				if ( $include ) {
					if ( ! isset( $query['post__in'] ) ) $query['post__in'] = array('0');
				} else {
					if ( ! isset( $query['post__not_in'] ) ) $query['post__not_in'] = array('0');
				}
			}
		}
        
		
    }
    
	
	
	
	return $query;
}