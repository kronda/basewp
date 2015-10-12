<?php
/**
* wpv-filter-limit-embedded.php
*
* @package Views
*
* @since unknown
*/

/**
* wpv_limit_offset_default_settings
*
* Set default values for limit and offset in all Views modes
*/

add_filter( 'wpv_view_settings', 'wpv_limit_offset_default_settings', 10, 2 );

function wpv_limit_offset_default_settings( $view_settings ) {
    if ( ! isset( $view_settings['limit'] ) ) {
        $view_settings['limit'] = -1;
    }
    if ( ! isset( $view_settings['offset'] ) ) {
        $view_settings['offset'] = 0;
    }
    if ( ! isset( $view_settings['taxonomy_limit'] ) ) {
        $view_settings['taxonomy_limit'] = -1;
    }
    if ( ! isset( $view_settings['taxonomy_offset'] ) ) {
        $view_settings['taxonomy_offset'] = 0;
    }
    if ( ! isset( $view_settings['users_limit'] ) ) {
        $view_settings['users_limit'] = -1;
    }
    if ( ! isset( $view_settings['users_offset'] ) ) {
        $view_settings['users_offset'] = 0;
    }
    return $view_settings;
}

/**
* wpv_filter_limit_arg
*
* Add limit and offset settings to the query for Views listing posts
* Note the hacks for limit+offset+pagination combinations
*
* @since unknown
*
*/

add_filter( 'wpv_filter_query', 'wpv_filter_limit_arg', 10, 3 );

function wpv_filter_limit_arg( $query, $view_settings, $view_id ) {
    $limit = intval( $view_settings['limit'] );
    $offset = intval( $view_settings['offset'] );
	$override_values = wpv_override_view_limit_offset();
	if ( isset( $override_values['limit'] ) ) {
		$limit = intval( $override_values['limit'] );
	}
	if ( isset( $override_values['offset'] ) ) {
		$offset = intval( $override_values['offset'] );
	}
	// Set query arguments so wpv_filter_limit_query_post_process_fix_limit_offset_pagination can do its job
	$query['wpv_original_limit'] = $limit;
	$query['wpv_original_offset'] = $offset;
	$query['wpv_original_posts_per_page'] = $query['posts_per_page'];
	
    if ( 
		$limit != -1 
		|| $offset != 0 
	) {
		if ( 
			isset( $view_settings['pagination'] ) 
			&& isset( $view_settings['pagination'][0] ) 
			&& $view_settings['pagination'][0] != 'disable' 
		) {
			// WordPress WPBUG
			// https://core.trac.wordpress.org/ticket/18897
			// "offset" query arg produce that "paged" query arg is ignored, returning always posts for the same first paged
			// It happens because native paging in WordPress uses the MySQL offset value as starting for posts on the current page, so... well, this
			// Also, WP_Query does not have a 'limit' argument, so we are playing with fire here
			// ------------------------------------------------
			// Adjust arguments here, we will correct counters on wpv_filter_limit_query_post_process_fix_limit_offset_pagination
			if ( $limit != -1 ) {
				// We have limit + pagination
				if ( $limit > $query['posts_per_page'] ) {
					// We have limit > posts_per_page so we do need pagination
					if ( 
						isset( $query['paged'] ) 
						&& $query['paged'] > 1 
					) {
						// We are on page +1 so we need to adjust the offset to respect pagination
						if ( $offset > 0 ) {
							// We already have offset, so we need to add it to the calculations
							$query['offset'] = $offset + ( ( $query['paged'] - 1 ) * $query['posts_per_page'] );
						} else {
							$query['offset'] = ( $query['paged'] - 1 ) * $query['posts_per_page'];
						}
						// We need to adjust the posts_per_page or we will get an incorrect result on the last page,
						// where posts_per_page is the difference between the limit and the already displayed posts on previous pages
						$query['posts_per_page'] = min( $query['posts_per_page'], $limit - ( ( $query['paged'] - 1 ) * $query['posts_per_page'] ) );
					} else {
						// We are on page 1, so we only need to adjust the offset if needed
						if ( $offset > 0 ) {
							$query['offset'] = $offset;
						}
					}
				} else {
					// We have posts_per_page > limit, so we show all the posts up until limit, adjusting offset if needed
					$query['posts_per_page'] = $limit;
					if ( $offset > 0 ) {
						$query['offset'] = $offset;
					}
				}
			} else if ( $offset > 0 ) {
				// We have offset + pagination, no limit
				// We only need to adjust the offset when on page +1
				if ( 
					isset( $query['paged'] ) 
					&& $query['paged'] > 1
				) {
					$query['offset'] = $offset + ( $query['posts_per_page'] * ( $query['paged'] - 1 ) );
				} else {
					$query['offset'] = $offset;
				}
			}
        } else {
			// We do not have pagination so all reslts are returned
			// We can use the limit argument and adjust the offset one if needed
			if ( $limit != -1 ) {
				$query['posts_per_page'] = $limit;
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

/**
* wpv_filter_limit_query_post_process_fix_limit_offset_pagination
*
* WordPress WPBUG
* https://core.trac.wordpress.org/ticket/18897
* Using an "offset" query argument produce that the "paged" query argument is ignored, returning always posts for the same first paged
* It happens because native paging in WordPress uses the MySQL offset value as starting for posts on the current page, so... well, this
* Also, WP_Query does not have a "limit" argument, so we are playing with fire here
*
* @since 1.9
*/

add_filter( 'wpv_filter_query_post_process', 'wpv_filter_limit_query_post_process_fix_limit_offset_pagination', 90, 3 );

function wpv_filter_limit_query_post_process_fix_limit_offset_pagination( $query, $view_settings, $view_id ) {
	$query_data = $query->query;
	if ( 
		! isset( $query_data['wpv_original_limit'] ) 
		|| ! isset( $query_data['wpv_original_offset'] ) 
		|| ! isset( $query_data['wpv_original_posts_per_page'] )
	) {
		return $query;
	}
	if ( 
		$query_data['wpv_original_limit'] != -1 
		|| $query_data['wpv_original_offset'] != 0
		
	) {
		if ( isset( $view_settings['pagination'] ) && isset( $view_settings['pagination'][0] ) && $view_settings['pagination'][0] != 'disable' ) {
			if ( $query_data['wpv_original_limit'] != -1 ) {
				if ( $query_data['wpv_original_limit'] > $query_data['wpv_original_posts_per_page'] ) {
					if ( $query_data['wpv_original_offset'] > 0 ) {
						$query->found_posts = max( 0, ( $query->found_posts - $query_data['wpv_original_offset'] ) );
						$query->found_posts = min( $query->found_posts, $query_data['wpv_original_limit'] );
						$query->max_num_pages = ceil( $query->found_posts / $query_data['wpv_original_posts_per_page'] );
					} else {
						$query->found_posts = min( $query->found_posts, $query_data['wpv_original_limit'] );
						$query->max_num_pages = ceil( $query->found_posts / $query_data['wpv_original_posts_per_page'] );
					}
				} else {
					$query->found_posts = $query->post_count;
					$query->max_num_pages = 1;
				}
			} else if ( $query_data['wpv_original_offset'] > 0 ) {
				$query->found_posts = max( 0, ( $query->found_posts - $query_data['wpv_original_offset'] ) );
				$query->max_num_pages = ceil( $query->found_posts / $query_data['wpv_original_posts_per_page'] );
			}
		} else {
			if ( $query_data['wpv_original_limit'] != -1 ) {
				$query->found_posts = $query->post_count;
				$query->max_num_pages = 1;
			} else if ( $query_data['wpv_original_offset'] > 0 ) {
				$query->found_posts = max( 0, ( $query->found_posts - $query_data['wpv_original_offset'] ) );
				$query->max_num_pages = ceil( $query->found_posts / $query_data['wpv_original_posts_per_page'] );
			}
		}
	}
	return $query;
}

/**
* wpv_filter_limit_taxonomy_post_query_filter
*
* Applies the limit and offset settings to Views listing taxonomy terms
* Note that this hapens after the query itself
*
* @since unknown
*/

add_filter( 'wpv_filter_taxonomy_post_query', 'wpv_filter_limit_taxonomy_post_query_filter', 10, 4 );

function wpv_filter_limit_taxonomy_post_query_filter( $items, $tax_query_settings, $view_settings, $view_id ) {
    $limit = intval( $view_settings['taxonomy_limit'] );
    $offset = intval( $view_settings['taxonomy_offset'] );
	
	$override_values = wpv_override_view_limit_offset();
	if ( isset( $override_values['limit'] ) ) {
		$limit = intval( $override_values['limit'] );
	}
	if ( isset( $override_values['offset'] ) ) {
		$offset = intval( $override_values['offset'] );
	}
	
    if (
		$offset != 0 
		|| $limit != -1
	) {
        if ( $limit == -1 ) {
            $items = array_slice( $items, $offset );
        } else {
            $items = array_slice( $items, $offset, $limit );
        }
        if ( empty( $items ) ) {
            return array();
        }
    }
    return $items;
}

/**
* wpv_users_query_limit_and_offset
*
* Apply the limit and ofset settings to Views listing users
*
* @since 1.6.2
*/

add_filter( 'wpv_filter_user_query', 'wpv_users_query_limit_and_offset', 50, 3 );

function wpv_users_query_limit_and_offset( $args, $view_settings, $view_id ) {
	if ( 
		$view_settings['users_limit'] !== '-1' 
		&& $view_settings['users_limit'] !== -1 
	) {
        $args['number'] = $view_settings['users_limit'];
    }
    $args['offset'] = $view_settings['users_offset'];
	
	$override_values = wpv_override_view_limit_offset();
	if ( isset( $override_values['limit'] ) ) {
		$args['number'] = intval( $override_values['limit'] );
	}
	if ( isset( $override_values['offset'] ) ) {
		$args['offset'] = intval( $override_values['offset'] );
	}
	
    if ( $args['offset'] > 0 ) {
		if ( 
			! isset( $args['number'] ) 
			|| ( 
				isset( $args['number'] ) 
				&& $args['number'] < 1 
			) 
		) {
			$args['number'] = 2147483647;
		}
    }
	return $args;
}

/*
* wpv_override_view_limit_offset
*
* Auxiliary function that will provide limit and offset settings coming from the Views shortcode atributes, if possible
*
* @return $return (array)
*
* @since 1.6.2
*
* @updated 1.10 limit and offset attributes will always override the stored settings
*/

function wpv_override_view_limit_offset() {
	global $WP_Views;
	// Override limit and offset values with the shortcode-provided ones
	$return = array();
	//$attributes_allowed = get_view_allowed_attributes( $view_id );
	//$attributes_used_by_filters = array();
	$view_attrs = $WP_Views->get_view_shortcodes_attributes();
	// Check the attributes allowed to see if 'limit' and 'offset' are already in use
	// If a query filter already uses any of those two words, this override will not be applied
	//if ( is_array( $attributes_allowed ) ) {
	//	$attributes_used_by_filters = wp_list_pluck( $attributes_allowed, 'attribute' );
	//}
	// If that is not the case, see if we need to override
	if ( 
		isset( $view_attrs['limit'] ) 
		//&& !in_array( 'limit', $attributes_used_by_filters ) 
	) {
		$return['limit'] = intval( $view_attrs['limit'] );
	}
	if ( 
		isset( $view_attrs['offset'] ) 
		//&& !in_array( 'offset', $attributes_used_by_filters ) 
	) {
		$return['offset'] = intval( $view_attrs['offset'] );
	}
	return $return;
}
