<?php

/**
 * Add a filter to add the query by post status to the $query
 *
 */

add_filter('wpv_filter_query', 'wpv_filter_post_status', 10, 2);
function wpv_filter_post_status($query, $view_settings) {
    
    if (isset($view_settings['post_status'])) {
        $query['post_status'] = $view_settings['post_status'];
    } else {
		$status = array( 'publish' );
		if ( in_array( 'attachment', $query['post_type']) ){
			$status[] = 'inherit';
		}
		if ( current_user_can( 'read_private_posts' ) ) {
			$status[] = 'private';
		}
		$query['post_status'] = $status;
	}
    
    return $query;
}
