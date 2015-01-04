<?php

/**
* wpv_filter_post_relationship_requires_current_page
*
* Add the filter by post relationship to the $query
*
* This function adds the filter by post relationship to the $query.
* It uses an additional auxiliar query, because post relationships are stored in custom fields, and we need to intersect those two filters.
* It usually takes a parent ID to execute the filter, but when filtering by URL parameter we must accept multiple parent IDs.
*
* @param $query
* @param $view_settings
*
* @return $query
*
* @since unknown
*
*/

add_filter('wpv_filter_query', 'wpv_filter_post_relationship', 11, 2); // run after post types filter
function wpv_filter_post_relationship($query, $view_settings) {
    
    global $WP_Views, $wpdb;
    
    if (isset($view_settings['post_relationship_mode'][0])) {
        
        $post_owner_id = 0; // the parent ID when it is just one
        $post_owner_data = array(); // we will store the data (parent ID and post_type) here to perform the auxiliar wp_query
        
        if ($view_settings['post_relationship_mode'][0] == 'current_page') {
            $post_owner_id = $WP_Views->get_top_current_page()->ID;
            if (is_archive()) { // for archive pages, the "current page" as "post where this View is inserted" is this
				$post_owner_id = $WP_Views->get_current_page()->ID;
            }
            if ($post_owner_id > 0) {
				$post_type = $wpdb->get_var( $wpdb->prepare( "SELECT post_type FROM {$wpdb->posts} WHERE ID = %d", $post_owner_id ) );
				$post_owner_data[$post_type][] = $post_owner_id;
			}
        }

        if ($view_settings['post_relationship_mode'][0] == 'parent_view') {
            $post_owner_id = $WP_Views->get_current_page()->ID;
            if ($post_owner_id > 0) {
				$post_type = $wpdb->get_var( $wpdb->prepare( "SELECT post_type FROM {$wpdb->posts} WHERE ID = %d", $post_owner_id ) );
				$post_owner_data[$post_type][] = $post_owner_id;
			}
        }
        
        if ($view_settings['post_relationship_mode'][0] == 'shortcode_attribute') {
            if (isset($view_settings['post_relationship_shortcode_attribute']) && '' != $view_settings['post_relationship_shortcode_attribute']) {
				$post_relationship_shortcode = $view_settings['post_relationship_shortcode_attribute'];
				$view_attrs = $WP_Views->get_view_shortcodes_attributes();
				if (isset($view_attrs[$post_relationship_shortcode])) {
					$post_owner_id = (int) $view_attrs[$post_relationship_shortcode];
					$post_type = $wpdb->get_var( $wpdb->prepare( "SELECT post_type FROM {$wpdb->posts} WHERE ID = %d", $post_owner_id ) );
					if (function_exists('icl_object_id')) {
						if ($post_type) {
							$post_owner_id = icl_object_id($post_owner_id, $post_type, true);
						}
					}
					if ($post_owner_id > 0) {
						$post_owner_data[$post_type][] = $post_owner_id;
					}
				}
			}
        }
        
        if ($view_settings['post_relationship_mode'][0] == 'url_parameter') {
            if (isset($view_settings['post_relationship_url_parameter']) && '' != $view_settings['post_relationship_url_parameter']) {
				$post_relationship_url_parameter = $view_settings['post_relationship_url_parameter'];
				if ( isset( $_GET[$post_relationship_url_parameter] ) && $_GET[$post_relationship_url_parameter] != array(0) ) {
				//	$post_owner_id = (int) esc_attr( $_GET[$post_relationship_url_parameter] );
					
					
					$post_owner_ids_from_url = $_GET[$post_relationship_url_parameter];
					$post_owner_ids_sanitized = array();
					if ( is_array( $post_owner_ids_from_url ) ) {
						foreach ( $post_owner_ids_from_url as $id_key => $id_value ) {
							$id_value = (int) esc_attr( trim( $id_value ) );
							if ( $id_value > 0 ) {
								$post_owner_ids_sanitized[$id_key] = $id_value;
							}
						}
					} else {
						$post_owner_ids_from_url = (int) esc_attr( $post_owner_ids_from_url );
						if ( $post_owner_ids_from_url > 0 ) {
							$post_owner_ids_sanitized[] = $post_owner_ids_from_url;
						}
					}
					if ( count( $post_owner_ids_sanitized ) ) {
						$post_types_from_url = $wpdb->get_results( "SELECT ID, post_type FROM {$wpdb->posts} WHERE ID IN ('" . implode("','", $post_owner_ids_sanitized) . "')" );
					//	print_r($post_types_from_url);
						foreach ( $post_types_from_url as $ptfu_key => $ptfu_values ) {
							$post_owner_id_item = $ptfu_values->ID;
							if ( function_exists( 'icl_object_id' ) ) {
								$post_owner_id_item = icl_object_id( $post_owner_id_item, $ptfu_values->post_type, true );
							}
							if ( $post_owner_id_item > 0 ) {
								$post_owner_data[$ptfu_values->post_type][] = $post_owner_id_item;
							}
						}
					}
					/*
					if (function_exists('icl_object_id')) {
						$post_type = $wpdb->get_var( $wpdb->prepare( "SELECT ID, post_type FROM {$wpdb->posts} WHERE ID = %d", $post_owner_id ) );
						if ($post_type) {
							$post_owner_id = icl_object_id($post_owner_id, $post_type, true);
						}
					}
					if ($post_owner_id > 0) {
						if ( !isset( $post_type ) ) {
							$post_type = $wpdb->get_var( $wpdb->prepare( "SELECT post_type FROM {$wpdb->posts} WHERE ID = %d", $post_owner_id ) );
						}
						$post_owner_data[$post_type][] = $post_owner_id;
					}
					*/
				} else {
					// TODO here we need to handle the case when the url param is not set BUT it's set for any of the parents
					// We might need recursive queries on the postmeta table
					/*
					1. get the returned post type parents
					2. get the tree applied here, will be stored in $view_settings['post_relationship_url_tree']
					3. reverse the tree so the real parent is the first one now; this parent has no value since the url param is not set
					4. get up in the tree until you find the first one element with a value
						4.1 if we get to the latest ancestor and even it does not hold any value, there is no value at all so filter by nothing: $post_owner_data = array() empty
						4.2 if we get to an ancestor with value, filter the last ancestor by the value of this one
						4.3 go down the tree following this filter until the real parent and populate the $post_owner_data[real-parent-slug]
					*/
					
					/*
					1
					*/
					$returned_post_types = $view_settings['post_type'];
					$returned_post_type_parents = array();
					if ( empty( $returned_post_types ) ) {
						$returned_post_types = array( 'any' );
					}
					foreach ( $returned_post_types as $returned_post_type_slug ) {
						$parent_parents_array = wpcf_pr_get_belongs( $returned_post_type_slug );
						if ( $parent_parents_array != false && is_array( $parent_parents_array ) ) {
							$returned_post_type_parents = array_merge( $returned_post_type_parents, array_values( array_keys( $parent_parents_array ) ) );
						}
					}
					
					/*
					2 + 3
					*/
					if ( isset( $view_settings['post_relationship_url_tree'] ) ) {
						$relationship_tree = $view_settings['post_relationship_url_tree'];
					} else {
						$relationship_tree = array();
					}
					$relationship_tree_array = array_reverse( explode( '>', $relationship_tree ) );
					
					$tree_root = end( $relationship_tree_array );
					$tree_ground = reset( $relationship_tree_array );
					
					if ( $tree_root && $tree_ground && isset( $_GET[$post_relationship_url_parameter . '-' . $tree_root] ) && !empty( $_GET[$post_relationship_url_parameter . '-' . $tree_root] ) && $_GET[$post_relationship_url_parameter . '-' . $tree_root] != array( 0 ) ) {
						// There are influencer values: let's get the last one
						$ancestor_influence = array();
						$starting_key = 0;
						array_shift( $relationship_tree_array ); // take out the first element as it is a real parent and has no value
						foreach ( $relationship_tree_array as $tree_key => $tree_ancestor ) {
							if ( $tree_ancestor != $tree_ground ) { // just check ancestors that are not the direct parent, as is has no value
						//	if ( !in_array( $tree_ancestor, $returned_post_type_parents ) ) { // just check ancestors that are not direct parents, as they have no value
								if ( isset( $_GET[$post_relationship_url_parameter . '-' . $tree_ancestor] ) && !empty( $_GET[$post_relationship_url_parameter . '-' . $tree_ancestor] ) && $_GET[$post_relationship_url_parameter . '-' . $tree_ancestor] != array( 0 ) ) {
									// This ancestor has a value. Yay!
									$post_owner_ids_from_url = $_GET[$post_relationship_url_parameter . '-' . $tree_ancestor];
									$post_owner_ids_sanitized = array();
									if ( is_array( $post_owner_ids_from_url ) ) {
										foreach ( $post_owner_ids_from_url as $id_key => $id_value ) {
											$id_value = (int) esc_attr( trim( $id_value ) );
											if ( $id_value > 0 ) {
												$post_owner_ids_sanitized[$id_key] = $id_value;
											}
										}
									} else {
										$post_owner_ids_from_url = (int) esc_attr( $post_owner_ids_from_url );
										if ( $post_owner_ids_from_url > 0 ) {
											$post_owner_ids_sanitized[] = $post_owner_ids_from_url;
										}
									}
									$ancestor_influence[$tree_ancestor] = array(
										'key' => $tree_key,
										'ids' => $post_owner_ids_sanitized
									);
									$starting_key = $tree_key;
									break;
								}
							}
						}
						if ( !empty( $ancestor_influence ) ) { // Should not be empty, but better check anyway
							$ancestor_influence = array_slice( $ancestor_influence, 0, 1 ); // It shuold have just one value, but check it anyway
							$i = 0;
							$no_results = false;
							while ( $i < $tree_key ) {
								$this_key = $tree_key - $i;
								if ( $this_key > 0 ) {
									$current_post_type = $relationship_tree_array[$this_key-1];
								} else {
									$current_post_type = $tree_ground;
								}
								$current_influencer = end( $ancestor_influence );
								$query_here = array();
								$query_here['posts_per_page'] = -1;
								$query_here['paged'] = 1;
								$query_here['offset'] = 0;
								$query_here['fields'] = 'ids';
								$query_here['cache_results'] = false;
								$query_here['update_post_meta_cache'] = false;
								$query_here['update_post_term_cache'] = false;
								$query_here['post_type'] = $current_post_type;
								$query_here['meta_query'][] = array(
									'key' => '_wpcf_belongs_' . $relationship_tree_array[$this_key] . '_id',
									'value' => $current_influencer['ids']
								);
								$aux_relationship_query = new WP_Query( $query_here );
								if ( is_array( $aux_relationship_query->posts ) && count( $aux_relationship_query->posts ) ) {
									$ancestor_influence[$current_post_type] = array(
										'key' => $this_key-1,
										'ids' => $aux_relationship_query->posts
									);
									$i++;
								} else {
									$no_results = true;
									break;
								}
								$i++;
							}
							if ( $no_results ) {
								// Along the intermediate filters, no posts were returned
								$query['post__in'] = array(-1);
							} else {
								$real_parent_filter = end( $ancestor_influence );
								$query_here = array();
								$query_here['posts_per_page'] = -1;
								$query_here['paged'] = 1;
								$query_here['offset'] = 0;
								$query_here['fields'] = 'ids';
								$query_here['cache_results'] = false;
								$query_here['update_post_meta_cache'] = false;
								$query_here['update_post_term_cache'] = false;
								$query_here['post_type'] = $tree_ground;
								$query_here['meta_query'][] = array(
									'key' => '_wpcf_belongs_' . $relationship_tree_array[0] . '_id',
									'value' => $real_parent_filter['ids']
								);
								$aux_relationship_query = new WP_Query( $query_here );
								if ( is_array( $aux_relationship_query->posts ) && count( $aux_relationship_query->posts ) ) {
									$post_owner_data[$tree_ground] = $aux_relationship_query->posts;
								} else {
									// Just on the late filter, no posts were returned
									$query['post__in'] = array(-1);
								}
							}
						}
						
					} else {
						// There are no values set, so filter by nothing
						// $post_owner_data = array() already;
					}
					
				}
			}
        }

        if ($view_settings['post_relationship_mode'][0] == 'this_page') {
            if (isset($view_settings['post_relationship_id']) && $view_settings['post_relationship_id'] > 0) {
                $post_owner_id = $view_settings['post_relationship_id'];
                if (function_exists('icl_object_id')) {
                    $post_type = $wpdb->get_var( $wpdb->prepare( "SELECT post_type FROM {$wpdb->posts} WHERE ID = %d", $post_owner_id ) );
                    if ($post_type) {
                        $post_owner_id = icl_object_id($post_owner_id, $post_type, true);
                    }
                }
                if ($post_owner_id > 0) {
					if ( !isset( $post_type ) ) {
						$post_type = $wpdb->get_var( $wpdb->prepare( "SELECT post_type FROM {$wpdb->posts} WHERE ID = %d", $post_owner_id ) );
					}
					$post_owner_data[$post_type][] = $post_owner_id;
				}
            }
        }
        
        if ( !empty( $post_owner_data ) ) {
			
			$query_here = array();
			$query_here['posts_per_page'] = -1;
			$query_here['paged'] = 1;
			$query_here['offset'] = 0;
			$query_here['post_type'] = 'any';
			$query_here['fields'] = 'ids';
			$query_here['cache_results'] = false;
			$query_here['update_post_meta_cache'] = false;
			$query_here['update_post_term_cache'] = false;
			$query_here['post_type'] = $view_settings['post_type'];
			$query_here['meta_query']['relation'] = 'AND';
			foreach ( $post_owner_data as $type => $ides ) {
				$query_here['meta_query'][] = array(
					'key' => '_wpcf_belongs_' . $type . '_id',
					'value' => $ides,
				);
			}
			$aux_relationship_query = new WP_Query( $query_here );
			
			if ( is_array( $aux_relationship_query->posts ) ) {
				if ( count( $aux_relationship_query->posts ) ) { // TODO now sure about this: array_merge merges,not calculates intersection. Future check.
					if (isset($query['post__in'])) {
						$query['post__in'] = array_merge($query['post__in'], $aux_relationship_query->posts);
					} else {
						$query['post__in'] = $aux_relationship_query->posts;
					}
					$query['pr_filter_post__in'] = $aux_relationship_query->posts;
				}
				else {
					$query['post__in'] = array(-1);
				}
			}
        }
    }
	
	return $query;
}

/**
 * Check the current post to see if it belongs to any other post types
 * defined by Types
 *
 */


add_action('wpv-before-display-post', 'wpv_before_display_post_post_relationship', 10, 2);
function wpv_before_display_post_post_relationship($post, $view_id) {

    static $related = array();
    global $WP_Views;
    
    if (function_exists('wpcf_pr_get_belongs')) {
        
        if (!isset($related[$post->post_type])) {
            $related[$post->post_type] = wpcf_pr_get_belongs($post->post_type);
        }
        if (is_array($related[$post->post_type])) {
            foreach($related[$post->post_type] as $post_type => $data) {
                $related_id = wpcf_pr_post_get_belongs($post->ID, $post_type);
                if ($related_id) {
                    $WP_Views->set_variable($post_type . '_id', $related_id);
                }
            }
        }
    }
    
}

add_filter('wpv_filter_requires_current_page', 'wpv_filter_post_relationship_requires_current_page', 10, 2);
function wpv_filter_post_relationship_requires_current_page($state, $view_settings) {
	if ($state) {
		return $state; // Already set
	}

    if (isset($view_settings['post_relationship_mode'][0])) {
        
        if ($view_settings['post_relationship_mode'][0] == 'current_page') {
            $state = true;
        }

        if ($view_settings['post_relationship_mode'][0] == 'parent_view') {
            $state = true;
        }
	}
    
    return $state;
}


