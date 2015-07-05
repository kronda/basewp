<?php
/*
 * Limit and offset filter embedded.
 */

add_filter('wpv_view_settings', 'wpv_limit_default_settings', 10, 2);

function wpv_limit_default_settings($view_settings) {

    if (!isset($view_settings['limit'])) {
        $view_settings['limit'] = -1;
    }
    if (!isset($view_settings['offset'])) {
        $view_settings['offset'] = 0;
    }
    if (!isset($view_settings['taxonomy_limit'])) {
        $view_settings['taxonomy_limit'] = -1;
    }
    if (!isset($view_settings['taxonomy_offset'])) {
        $view_settings['taxonomy_offset'] = 0;
    }
    if (!isset($view_settings['users_limit'])) {
        $view_settings['users_limit'] = -1;
    }
    if (!isset($view_settings['users_offset'])) {
        $view_settings['users_offset'] = 0;
    }

    return $view_settings;
}

add_filter('wpv_filter_query', 'wpv_filter_limit_arg', 10, 3);

function wpv_filter_limit_arg($query, $view_settings, $view_id) {
    $limit = intval($view_settings['limit']);
    $offset = intval($view_settings['offset']);
	
	$override_values = wpv_override_view_limit_offset( $view_id );
	if ( isset( $override_values['limit'] ) ) {
		$limit = intval( $override_values['limit'] );
	}
	if ( isset( $override_values['offset'] ) ) {
		$offset = intval( $override_values['offset'] );
	}
	
    if ( ( $offset != 0 || $limit != -1 ) ) {
		if ( isset( $view_settings['pagination'] ) && isset( $view_settings['pagination'][0] ) && $view_settings['pagination'][0] != 'disable' ) {
			remove_filter('wpv_filter_query', 'wpv_filter_limit_arg', 10, 3);
			add_filter('wpv_filter_query_post_process',
					'wpv_filter_limit_query_post_process_filter', 10, 3);
			unset($query['paged']);
			$query['posts_per_page'] = -1;
			global $WPVDebug;
			$WPVDebug->add_log( 'info' , __('This View uses an auxiliary query: it has pagination and limit&offset settings, and WordPress can not perform a single Query to handle both.', 'wpv-views') , 'additional_info' , '' , true );
        } else {
			if ( $limit != -1 ) {
				$query['posts_per_page'] = $limit;
				add_filter('wpv_filter_query_post_process', 'wpv_filter_limit_query_post_process_adjust_found_posts_max_num_pages', 90, 2);
			}
			if ( $offset > 0 ) {
				$query['offset'] = $offset;
				if ( $limit == -1 ) {
					// http://dev.mysql.com/doc/refman/5.0/en/select.html
					// Offset will not work with posts_per_page = -1
					$query['posts_per_page'] = 2147483647;
				}
			}
        }
    }
    return $query;
}

function wpv_filter_limit_query_post_process_filter($query, $view_settings, $view_id) {
    remove_filter('wpv_filter_query_post_process',
            'wpv_filter_limit_query_post_process_filter', 10, 3);
    if (!empty($query->posts)) {
		$limit = intval($view_settings['limit']);
        $offset = intval($view_settings['offset']);
		
		$override_values = wpv_override_view_limit_offset( $view_id );
		if ( isset( $override_values['limit'] ) ) {
			$limit = intval( $override_values['limit'] );
		}
		if ( isset( $override_values['offset'] ) ) {
			$offset = intval( $override_values['offset'] );
		}
		
        if ($limit == -1) {
            $posts = array_slice($query->posts, $offset);
        } else {
            $posts = array_slice($query->posts, $offset, $limit);
        }
        add_filter('wpv_filter_query', 'wpv_filter_limit_arg_post_in', 12, 2); // needs to be after post relationships filter
        global $wpv_limit_post_in;
        if (!empty($posts)) {
            $wpv_limit_post_in = array();
            foreach ($posts as $key => $post) {
                $wpv_limit_post_in[] = $post->ID;
            }
        } else {
            $wpv_limit_post_in = array(0);
        }
        $query = wpv_filter_get_posts($view_settings['view_id']);
        remove_filter('wpv_filter_query', 'wpv_filter_limit_arg_post_in', 12, 2);

        add_filter('wpv_filter_query', 'wpv_filter_limit_arg', 10, 3);

        return $query;
    }
    
    add_filter('wpv_filter_query', 'wpv_filter_limit_arg', 10, 3);
    
    return $query;
}

function wpv_filter_limit_arg_post_in($query, $view_settings) {
    global $wpv_limit_post_in;
    $query['post__in'] = $wpv_limit_post_in;
    return $query;
}

function wpv_filter_limit_query_post_process_adjust_found_posts_max_num_pages( $query, $view_settings ) {
	remove_filter('wpv_filter_query_post_process', 'wpv_filter_limit_query_post_process_adjust_found_posts_max_num_pages', 90, 2);
	$query->found_posts = $query->post_count;
	$query->max_num_pages = 1;
	return $query;
}

// Taxonomies
add_filter('wpv_filter_taxonomy_post_query',
        'wpv_filter_limit_taxonomy_post_query_filter', 10, 4);

function wpv_filter_limit_taxonomy_post_query_filter($items,
        $tax_query_settings, $view_settings, $view_id) {
    $limit = intval($view_settings['taxonomy_limit']);
    $offset = intval($view_settings['taxonomy_offset']);
	
	$override_values = wpv_override_view_limit_offset( $view_id );
	if ( isset( $override_values['limit'] ) ) {
		$limit = intval( $override_values['limit'] );
	}
	if ( isset( $override_values['offset'] ) ) {
		$offset = intval( $override_values['offset'] );
	}
	
    if ($offset != 0 || $limit != -1) {
        if ($limit == -1) {
            $items = array_slice($items, $offset);
        } else {
            $items = array_slice($items, $offset, $limit);
        }
        if (empty($items)) {
            return array();
        }
    }
    return $items;
}

/*
* wpv_override_view_limit_offset
*
* Auxiliary function that will provide limit and offset settings coming from the Views shortcode atributes, if possible
*
* @param $view_id (integer)
*
* @return $return (array)
*
* @since 1.6.2
*/

function wpv_override_view_limit_offset( $view_id ) {
	global $WP_Views;
	// Override limit and offset values with the shortcode-provided ones
	$return = array();
	$attributes_allowed = get_view_allowed_attributes( $view_id );
	$attributes_used_by_filters = array();
	$view_attrs = $WP_Views->get_view_shortcodes_attributes();
	// Check the attributes allowed to see if 'limit' and 'offset' are already in use
	// If a query filter already uses any of those two words, this override will not be applied
	if ( is_array( $attributes_allowed ) ) {
		$attributes_used_by_filters = wp_list_pluck( $attributes_allowed, 'attribute' );
	}
	// If that is not the case, see if we need to override
	if ( isset( $view_attrs['limit'] ) && !in_array( 'limit', $attributes_used_by_filters ) ) {
		$return['limit'] = intval( $view_attrs['limit'] );
	}
	if ( isset( $view_attrs['offset'] ) && !in_array( 'offset', $attributes_used_by_filters ) ) {
		$return['offset'] = intval( $view_attrs['offset'] );
	}
	// Return
	return $return;
}
