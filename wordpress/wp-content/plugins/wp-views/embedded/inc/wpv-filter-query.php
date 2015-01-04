<?php

/**
 * Create the query to return the posts based on the settings
 * in the views query meta box.
 *
 */

function wpv_filter_get_posts($id) {
    global $WP_Views, $post, $wplogger,$WPVDebug, $wpdb;
	
	$view_settings_defaults = array(
		'post_type'         => 'any',
		'orderby'           => 'post-date',
		'order'             => 'DESC',
		'paged'             => '1',
		'posts_per_page'    =>  -1
	);
	extract($view_settings_defaults);
	
	$view_settings = $WP_Views->get_view_settings($id);
	$view_settings['view_id'] = $id;
	
	extract($view_settings, EXTR_OVERWRITE);

	if (isset($_GET['wpv_paged']) && isset($_GET['wpv_view_count']) && esc_attr($_GET['wpv_view_count']) == $WP_Views->get_view_count()) {
		$paged = intval(esc_attr($_GET['wpv_paged']));
	}
    $query = array(
            'posts_per_page'    => $posts_per_page,
            'paged'             => $paged,
            'post_type'         => $post_type,
            'order'             => $order,
            'suppress_filters'  => false,
			'ignore_sticky_posts' => true
    );

    if (isset($view_settings['pagination'][0]) && $view_settings['pagination'][0] == 'disable'
    // && isset($view_settings['pagination']['mode']) && $view_settings['pagination']['mode'] == 'paged'
    ) {
        // Show all the posts if pagination is disabled.
        $query['posts_per_page'] = -1;
    }
    if (isset($view_settings['pagination']['mode']) && $view_settings['pagination']['mode'] == 'rollover') {
        $query['posts_per_page'] = $view_settings['rollover']['posts_per_page'];
    }

	// Add special check for media (attachments) as their default status in not usually published
	if (sizeof($post_type) == 1 && $post_type[0] == 'attachment') {
		$query['post_status'] = 'any'; // Note this can be overriden by adding a status filter.
	}

	$WPVDebug->add_log( 'info' , apply_filters('wpv-view-get-content-summary', '', $WP_Views->current_view, $view_settings) , 'short_query' );

	$WPVDebug->add_log( 'info' , "Basic query arguments\n". print_r($query, true) , 'query_args' );

	/**
	* Filter wpv_filter_query
	*
	* This is where all the filters coming from the View settings to modify the query are hooked
	*
	* @param $query the Query arguments as in WP_Query
	* @param $view_settings the View settings
	* @param $id the ID of the View being displayed
	*
	* @return $query
	*
	* @since unknown
	*/

    $query = apply_filters( 'wpv_filter_query', $query, $view_settings, $id );

    $WPVDebug->add_log( 'filters' , "wpv_filter_query\n" . print_r($query, true) , 'filters', 'Filter arguments before the query using <strong>wpv_filter_query</strong>' );

    $post_query = new WP_Query($query);

	$WPVDebug->add_log( 'mysql_query' , $post_query->request , 'posts' , '' , true );

	$WPVDebug->add_log( 'info' , print_r($post_query, true) , 'query_results' , '' , true );

	$wplogger->log($post_query->query, WPLOG_DEBUG);
	$wplogger->log($post_query->request, WPLOG_DEBUG);

	/**
	* Filter wpv_filter_query_post_process
	*
	* This is applied to the results of the main query.
	*
	* @param $post_query the queried object returned by the WordPress WP_Query()
	* @param $view_settings the View settings
	* @param $id the ID of the View being displayed
	*
	* @return $post_query
	*
	* @since unknown
	*/

    $post_query = apply_filters( 'wpv_filter_query_post_process', $post_query, $view_settings, $id );

    $WPVDebug->add_log( 'filters' , "wpv_filter_query_post_process\n" . print_r($post_query, true) , 'filters', 'Filter the returned query using <strong>wpv_filter_query_post_process</strong>' );

    return $post_query;
}

add_filter('wpv_filter_query', 'wpv_filter_query_compatibility', 99,2);

function wpv_filter_query_compatibility($query, $view_settings) {

	// Relevanssi compatibility
	if ( isset($view_settings['search_mode'] ) && function_exists( 'relevanssi_prevent_default_request' ) ) {
		remove_filter('posts_request', 'relevanssi_prevent_default_request', 10, 2 );
	}

	return $query;
}

add_filter('wpv_filter_query_post_process', 'wpv_filter_query_post_proccess_compatibility', 99, 2);

function wpv_filter_query_post_proccess_compatibility($post_query, $view_settings ) {

	// Relevanssi compatibility
	if ( isset($view_settings['search_mode'] ) && function_exists( 'relevanssi_prevent_default_request' ) ) {
		add_filter('posts_request', 'relevanssi_prevent_default_request', 10, 2 );
	}

	return $post_query;
}

add_filter( 'wpv_filter_query', 'wpv_filter_query_post_in_and_not_in_fix', 999, 2 );

function wpv_filter_query_post_in_and_not_in_fix($query, $view_settings) {

	if ( isset( $query['post__in'] ) && isset( $query['post__not_in'] ) ) {
		$query['post__in'] = array_diff( (array) $query['post__in'], (array) $query['post__not_in'] );
		unset( $query['post__not_in'] );
	}

	return $query;
}

add_filter('wpv_filter_query_post_process', 'wpv_filter_extend_query_for_parametric', 999, 3);

function wpv_filter_extend_query_for_parametric($post_query, $view_settings, $id ) {
	remove_filter('wpv_filter_query_post_process', 'wpv_filter_extend_query_for_parametric', 999, 2);
	$dps_enabled = false;
	if ( !isset( $view_settings['dps'] ) || !is_array( $view_settings['dps'] ) ) {
		$view_settings['dps'] = array();
	}
	if ( isset( $view_settings['dps']['enable_dependency'] ) && $view_settings['dps']['enable_dependency'] == 'enable' ) {
		$dps_enabled = true;
		$controls_per_kind = wpv_count_filter_controls( $view_settings );
		$controls_count = 0;
		$no_intersection = array();
		if ( !isset( $controls_per_kind['error'] ) ) {
		//	$controls_count = array_sum( $controls_per_kind );
			$controls_count = $controls_per_kind['cf'] + $controls_per_kind['tax'] + $controls_per_kind['pr'];
			if ( $controls_per_kind['cf'] > 1 && ( !isset( $view_settings['custom_fields_relationship'] ) || $view_settings['custom_fields_relationship'] != 'AND' ) ) {
				$no_intersection[] = __( 'custom field', 'wpv-views' );
			}
			if ( $controls_per_kind['tax'] > 1 && ( !isset( $view_settings['taxonomy_relationship'] ) || $view_settings['taxonomy_relationship'] != 'AND' ) ) {
				$no_intersection[] = __( 'taxonomy', 'wpv-views' );
			}
		} else {
			$dps_enabled = false;
		}
		if ( $controls_count > 0 ) {
			if ( count( $no_intersection ) > 0 ) {
				$dps_enabled = false;
			}
		} else {
			$dps_enabled = false;
		}
	}
	
	global $WP_Views;
	if ( !$dps_enabled ) {
		// Set the force value
		$WP_Views->set_force_disable_dependant_parametric_search( true );
		return $post_query;
	}
	
	// If after all we have dps, we need to populate the $wp_object_cache
	// Check if we need to create all the cache or just for the missing ones
	global $wp_object_cache;
	$cache_exclude_queried_posts = false; // we will only exclude already queried posts if there are any queried posts and the native cache exists (so they are already cached)
	$cache_use_native = true;
	if ( is_object( $wp_object_cache ) ) {
		if ( isset( $wp_object_cache->cache ) ) {
			// existing queried posts were already cached
			if ( empty( $post_query ) ) {
				$cache_exclude_queried_posts = false; // we passed an empty $post_query so there are no queried posts at all (surely coming from a form View shortcode)
			} else {
				$cache_exclude_queried_posts = true;
			}
		} else {
			$wp_object_cache->cache = array();			
			$cache_exclude_queried_posts = false; // already queried posts were not cached where we need them
			$cache_use_native = false; // the native $wp_object_cache->cache property is not set, so we will recreate it instead of naturally caching metadata
		}
	}
	
	// In any case, we need to mimic the process that we used to generate the $query
	$view_settings_defaults = array(
	'post_type'         => 'any',
	'orderby'           => 'post-date',
	'order'             => 'DESC',
	'paged'             => '1',
	'posts_per_page'    =>  -1
	);
	extract($view_settings_defaults);
	$view_settings['view_id'] = $id;
	extract($view_settings, EXTR_OVERWRITE);
    $query = array(
            'posts_per_page'    => $posts_per_page,
            'paged'             => $paged,
            'post_type'         => $post_type,
            'order'             => $order,
            'suppress_filters'  => false,
			'ignore_sticky_posts' => true
    );
	// Add special check for media (attachments) as their default status in not usually published
	if (sizeof($post_type) == 1 && $post_type[0] == 'attachment') {
		$query['post_status'] = 'any'; // Note this can be overriden by adding a status filter.
	}
		
	$query = apply_filters( 'wpv_filter_query', $query, $view_settings, $id );
	
	// Now we have the $query as in the original one
	// We now need to overwrite the limit, offset, paged and pagination options
	// Also, we set it to just return the IDs
	$query['posts_per_page'] = -1;
	$query['ĺimit'] = -1;
	$query['paged'] = 1;
	$query['offset'] = 0;
	$query['fields'] = 'ids';
	
	if ( $cache_exclude_queried_posts ) {// do not query again already queried and cached posts
		$already = array();
		if( isset( $post_query->posts ) && !empty( $post_query->posts ) ) {
			foreach((array) $post_query->posts as $post_object) {
				$already[] = $post_object->ID;
			}
		}
		$WP_Views->returned_ids_for_parametric_search = $already;
		if ( isset ( $query['pr_filter_post__in'] ) ) {
			$query['post__in'] = $query['pr_filter_post__in'];
		} else {
			// If just for the missing ones, generate the post__not_in argument
			if ( isset( $query['post__not_in'] ) ) {
				$query['post__not_in'] = array_merge( (array) $query['post__not_in'], (array) $already );
			} else {
				$query['post__not_in'] = (array) $already;
			}
			// And adjust on the post__in argument
			if ( isset( $query['post__in'] ) ) {
				$query['post__in'] = array_diff( (array) $query['post__in'], (array) $query['post__not_in'] );
				//unset( $query['post__in'] );
			}
		}
	}
	
	// Perform the query
	$aux_cache_query = new WP_Query($query);
	
	// In case we need to recreate our own cache object, we do not need to load there all the postmeta and taxonomy data, just for the elements involved in parametric search controls
	$filter_c_mode = ( isset( $view_settings['filter_controls_mode'] ) && is_array( $view_settings['filter_controls_mode'] ) ) ? $view_settings['filter_controls_mode'] : array();
	$filter_c_name = ( isset( $view_settings['filter_controls_field_name'] ) && is_array( $view_settings['filter_controls_field_name'] ) ) ? $view_settings['filter_controls_field_name'] : array();
	$f_taxes = array();
	$f_fields = array();
	
	foreach ( $filter_c_mode as $f_index => $f_mode ) {
		if ( isset( $filter_c_name[$f_index] ) ) {
			switch ( $f_mode ) {
				case 'slug':
					$f_taxes[] = $filter_c_name[$f_index];
					break;
				case 'cf':
					$f_fields[] = $filter_c_name[$f_index];
					break;
				case 'rel':
					if ( function_exists( 'wpcf_pr_get_belongs' ) ) {
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
						foreach ( $returned_post_type_parents as $parent_to_cache ) {
							$f_fields[] = '_wpcf_belongs_' . $parent_to_cache . '_id';
						}
					}
					break;
				default:
					break;
			}
		}
	}

	// If we are using the native caching, update the cache for the posts returned by the aux query
	if ( is_array( $aux_cache_query->posts ) && !empty( $aux_cache_query->posts ) ) {
		$WP_Views->returned_ids_for_parametric_search = array_merge( $WP_Views->returned_ids_for_parametric_search, $aux_cache_query->posts );
		$WP_Views->returned_ids_for_parametric_search = array_unique( $WP_Views->returned_ids_for_parametric_search );
		if ( $cache_use_native ) {// If we are using the native caching, update the cache for the posts returned by the aux query
			update_postmeta_cache( $aux_cache_query->posts );
			update_object_term_cache( $aux_cache_query->posts, $view_settings['post_type'] );
		} else {// Else, we need to fake an $wp_object_cache->cache
			$f_data = array(
				'cf' => $f_fields,
				'tax' => $f_taxes
			);
			$cache_combined = wpv_custom_cache_metadata( $aux_cache_query->posts, $f_data );
			$wp_object_cache->cache = $cache_combined;
		}
	}

	return $post_query;
}

/**
* wpv_custom_cache_metadata
*
* Mimics the caching construction of WordPress so we can use it for counting posts
*
* @param $id_posts array of post IDs
* @param $f_data array of data to pseudo-cache
*    'tax' => array of taxonomy names to cache
*    'cf'  => array of field meta_key's to cache
*
* @return (array) compatible with $wp_object_cache->cache
*
* @since 1.6.0
*/

function wpv_custom_cache_metadata( $id_posts = array(), $f_data = array() ) {
	$f_fields = ( isset( $f_data['cf'] ) ) ? $f_data['cf'] : array();
	$f_taxes = ( isset( $f_data['tax'] ) ) ? $f_data['tax'] : array();
	// First, the post_meta
	$cache_post_meta = array();
	if ( !empty( $f_fields ) && !empty( $id_posts ) ) {
		global $wpdb;
		$id_list = join( ',', $id_posts );
		$f_fields_list = implode( "','", $f_fields );
		$meta_list = $wpdb->get_results( "SELECT post_id, meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id IN ({$id_list}) AND meta_key IN ('{$f_fields_list}') ORDER BY post_id ASC", ARRAY_A );
		if ( !empty($meta_list) ) {
			foreach ( $meta_list as $metarow) {
				$mpid = intval($metarow['post_id']);
				$mkey = $metarow['meta_key'];
				$mval = $metarow['meta_value'];
				if ( !isset($cache_post_meta[$mpid]) || !is_array($cache_post_meta[$mpid]) )
					$cache_post_meta[$mpid] = array();
				if ( !isset($cache_post_meta[$mpid][$mkey]) || !is_array($cache_post_meta[$mpid][$mkey]) )
					$cache_post_meta[$mpid][$mkey] = array();
				$cache_post_meta[$mpid][$mkey][] = $mval;
			}
		}
		// NOTE the last foreach might not be needed
		foreach ( $id_posts as $id_needed ) {
			if ( ! isset($cache_post_meta[$id_needed]) )
				$cache_post_meta[$id_needed] = array();
		}
	}
	
	//Then, the taxonomies
	$cache_post_taxes = array();
	if ( !empty( $f_taxes ) && !empty( $id_posts ) ) {
		$terms = wp_get_object_terms( $id_posts, $f_taxes, array('fields' => 'all_with_object_id') );
		$object_terms = array();
		foreach ( (array) $terms as $term )
			$object_terms[$term->object_id][$term->taxonomy][$term->term_id] = $term;
		foreach ( $id_posts as $id_needed ) {
			foreach ( $f_taxes as $taxonomy ) {
				if ( ! isset($object_terms[$id_needed][$taxonomy]) ) {
					if ( !isset($object_terms[$id_needed]) )
						$object_terms[$id_needed] = array();
					$object_terms[$id_needed][$taxonomy] = array();
				}
			}
		}
		foreach ( $object_terms as $term_id => $value ) {
			foreach ( $value as $taxonomy => $terms ) {
				if ( !isset( $cache_post_taxes[$taxonomy . '_relationships'] ) ) {
					$cache_post_taxes[$taxonomy . '_relationships'] = array();
				}
				$cache_post_taxes[$taxonomy . '_relationships'][$term_id] = $terms;
			//	wp_cache_add( $term_id, $terms, "{$taxonomy}_relationships" );
			}
		}
	}
	
	$cache_combined = array();
	$cache_combined['post_meta'] = $cache_post_meta;
	foreach ( $cache_post_taxes as $tax_key => $tax_cached_values ) {
		$cache_combined[$tax_key] = $tax_cached_values;
	}
	
	return $cache_combined;
}

function wpv_get_dependant_view_query_args( $id = null ) {
	// In any case, we need to mimic the process that we used to generate the $query
	global $WP_Views;
	$view_settings_defaults = array(
	'post_type'         => 'any',
	'orderby'           => 'post-date',
	'order'             => 'DESC',
	'paged'             => '1',
	'posts_per_page'    =>  -1
	);
	if (is_null($id)) {
		$id = $WP_Views->get_current_view();
	}
	$view_settings = $WP_Views->get_view_settings();
	extract($view_settings_defaults);
	$view_settings['view_id'] = $id;
	extract($view_settings, EXTR_OVERWRITE);
    $query = array(
            'posts_per_page'    => $posts_per_page,
            'paged'             => $paged,
            'post_type'         => $post_type,
            'order'             => $order,
            'suppress_filters'  => false,
			'ignore_sticky_posts' => true
    );
	// Add special check for media (attachments) as their default status in not usually published
	if (sizeof($post_type) == 1 && $post_type[0] == 'attachment') {
		$query['post_status'] = 'any'; // Note this can be overriden by adding a status filter.
	}
	
	// !IMPORTANT override the sorting options (not important here), sorting by a custom field breaks everything, so revert to post_date
	$view_settings['orderby'] = 'ID';
	
	$query = apply_filters( 'wpv_filter_query', $query, $view_settings, $id );
	
	// Now we have the $query as in the original one
	// We now need to overwrite the limit, offset, paged and pagination options
	//Also,we set it to just return the IDs
	$query['posts_per_page'] = -1;
	$query['ĺimit'] = -1;
	$query['paged'] = 1;
	$query['offset'] = 0;
	$query['fields'] = 'ids';
	
	return $query;
}