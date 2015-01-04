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

add_filter('wpv_filter_query', 'wpv_filter_limit_arg', 10, 2);

function wpv_filter_limit_arg($query, $view_settings) {
    $limit = intval($view_settings['limit']);
    $offset = intval($view_settings['offset']);
    if ( ( $offset != 0 || $limit != -1 ) ) {
		if ( isset( $view_settings['pagination'] ) && isset( $view_settings['pagination'][0] ) && $view_settings['pagination'][0] != 'disable' ) {
			remove_filter('wpv_filter_query', 'wpv_filter_limit_arg', 10, 2);
			add_filter('wpv_filter_query_post_process',
					'wpv_filter_limit_query_post_process_filter', 10, 2);
			unset($query['paged']);
			$query['posts_per_page'] = -1;
			global $WPVDebug;
			$WPVDebug->add_log( 'info' , __('This View uses an auxiliar query: it has pagination and limit&offset settings, and WordPress can not perform a single Query to handle both.', 'wpv-views') , 'additional_info' , '' , true );
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

function wpv_filter_limit_query_post_process_filter($query, $view_settings) {
    remove_filter('wpv_filter_query_post_process',
            'wpv_filter_limit_query_post_process_filter', 10, 2);
    if (!empty($query->posts)) {
        $limit = intval($view_settings['limit']);
        $offset = intval($view_settings['offset']);
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

        add_filter('wpv_filter_query', 'wpv_filter_limit_arg', 10, 2);

        return $query;
    }
    
    add_filter('wpv_filter_query', 'wpv_filter_limit_arg', 10, 2);
    
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
        'wpv_filter_limit_taxonomy_post_query_filter', 10, 3);

function wpv_filter_limit_taxonomy_post_query_filter($items,
        $tax_query_settings, $view_settings) {
    $limit = intval($view_settings['taxonomy_limit']);
    $offset = intval($view_settings['taxonomy_offset']);
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