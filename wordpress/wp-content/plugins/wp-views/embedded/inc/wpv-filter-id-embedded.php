<?php

/**
 * Add a filter to add the query by author to the $query
 */

add_filter( 'wpv_filter_query', 'wpv_filter_post_id', 13, 2 ); // we need to set a higher priority than the limit filter has because we use $query['post__in'] = array('0') on failure

function wpv_filter_post_id( $query, $view_settings ) {
	// @todo are IDs adjusted in WPML? Maybe yes, because it filters the post__in and post__not_in arguments
	if ( isset( $view_settings['id_mode'][0] ) ) {
		$include = true;
		$show_id_array = array();		
		if ( 
			isset( $view_settings['id_in_or_out'] ) 
			&& 'out' == $view_settings['id_in_or_out'] 
		) {
			$include = false;
		}
		switch ( $view_settings['id_mode'][0] ) {
			case 'by_ids':
				if (
					isset( $view_settings['post_id_ids_list'] ) 
					&& '' != $view_settings['post_id_ids_list']
				) {
					$id_ids_list = explode( ',', $view_settings['post_id_ids_list'] );
					foreach ( $id_ids_list as $id_candidate ) {
						$show_id_array[] = (int) trim( $id_candidate );
					}
				}
				else {
					$show_id_array = null;
				}
				break;
			case 'by_url':
				if (
					isset( $view_settings['post_ids_url'] ) 
					&& '' != $view_settings['post_ids_url']
				) {
					$id_parameter = $view_settings['post_ids_url'];	
					if ( isset( $_GET[$id_parameter] ) ) {
						$ids_to_load = $_GET[$id_parameter];
						if ( is_array( $ids_to_load ) ) {
							if ( 
								0 == count( $ids_to_load ) 
								|| '' == $ids_to_load[0] 
							) {
								$show_id_array = null;
							} else {
								foreach ( $ids_to_load as $id_candidate ) {
									$show_id_array[] = (int) trim( $id_candidate );
								}
							}
						} else {
							if ( '' == $ids_to_load ) {
								$show_id_array = null;
							} else {
								$show_id_array[] = (int) trim( $ids_to_load );
							}
						}
					} else {
						$show_id_array = null;
					}
				}
				break;
			case 'shortcode':
				global $WP_Views;
				if (
					isset( $view_settings['post_ids_shortcode'] ) 
					&& '' != $view_settings['post_ids_shortcode']
				) {
					$id_shortcode = $view_settings['post_ids_shortcode'];	
					$view_attrs = $WP_Views->get_view_shortcodes_attributes();
					if ( isset( $view_attrs[$id_shortcode] ) ) {
						$ids_to_load = explode( ',', $view_attrs[$id_shortcode] );
						if ( count( $ids_to_load ) > 0 ) {
							foreach ( $ids_to_load as $id_candidate ) {
								$show_id_array[] = (int) trim( $id_candidate );
							}
						}
					} else {
						$show_id_array = null;
					}
				}
				break;
			case 'framework':
				global $WP_Views_fapi;
				if ( $WP_Views_fapi->framework_valid ) {
					if (
						isset( $view_settings['post_ids_framework'] ) 
						&& '' != $view_settings['post_ids_framework']
					) {
						$post_ids_framework = $view_settings['post_ids_framework'];
						$post_ids_candidates = $WP_Views_fapi->get_framework_value( $post_ids_framework, array() );
						if ( ! is_array( $post_ids_candidates ) ) {
							$post_ids_candidates = explode( ',', $post_ids_candidates );
						}
						if ( count( $post_ids_candidates ) > 0 ) {
							foreach ( $post_ids_candidates as $id_candidate ) {
								if ( is_numeric( $id_candidate ) ) {
									$show_id_array[] = (int) trim( $id_candidate );
								}
							}
						}
					}
				} else {
					$show_id_array = null;
				}
				break;
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