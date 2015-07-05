<?php

/**
* wpv_post_default_settings
*
* Sets the default settings for Views listing posts
*
* @since unknown
*/

add_filter( 'wpv_view_settings', 'wpv_post_default_settings' );

function wpv_post_default_settings($view_settings) {
	if (!isset($view_settings['post_type'])) {
		$view_settings['post_type'] = array( 'any' );
	}
	if (!isset($view_settings['post_type_dont_include_current_page'])) {
		$view_settings['post_type_dont_include_current_page'] = true;
	}
	return $view_settings;
}

add_filter('wpv_filter_query', 'wpv_filter_get_post_types_arg', 10, 2);
function wpv_filter_get_post_types_arg($query, $view_settings) {
    
    global $post;
    
    $post_type = $query['post_type'];
    // See if the post_type is exposed as a url arg.
    if (isset($view_settings['post_type_expose_arg']) && $view_settings['post_type_expose_arg']) {
        if ($_GET['wpv_post_type']) {
            $post_type = $_GET['wpv_post_type'];
        }
    }
    $query['post_type'] = $post_type;
    
    if (!isset($view_settings['post_type_dont_include_current_page']) || $view_settings['post_type_dont_include_current_page']) {

        if (is_single() || is_page()) {
        	global $wp_query;
            
            if (isset($wp_query->posts[0])) {
                $current_post = $wp_query->posts[0];
                $post_not_in_list = $current_post ? array($current_post->ID) : array();
            
				if ( isset( $query['post__not_in'] ) ) {
					$query['post__not_in'] = array_merge( (array)$query['post__not_in'], $post_not_in_list );
				} else {
					$query['post__not_in'] = $post_not_in_list;
				}
           //     $query['post__not_in'] = $post_not_in_list;
            }
        } else if ( isset( $_GET['wpv_post_id'] ) ) { //in AJAX pagination is_single() and is_page() do not work as expected
			if ( isset( $query['post__not_in'] ) ) {
				$query['post__not_in'] = array_merge( (array)$query['post__not_in'], array( $_GET['wpv_post_id'] ) );
			} else {
				$query['post__not_in'] = array( $_GET['wpv_post_id'] );
			}
        }
    }
    
    return $query;

}

/**
* wpv_filter_post_exclude_current_requires_current_page
*
* Filter hooked to wpv_filter_requires_current_page.
* When the option post_type_dont_include_current_page is checked, we need to pass the wpv_post_id value in an input when doing AJAX pagination
*
* @since 1.5.0
*/

add_filter( 'wpv_filter_requires_current_page', 'wpv_filter_post_exclude_current_requires_current_page', 10, 2 );

function wpv_filter_post_exclude_current_requires_current_page( $state, $view_settings ) {
	if ($state) {
		return $state; // Already set
	}
	if ( !isset($view_settings['post_type_dont_include_current_page'] ) || $view_settings['post_type_dont_include_current_page'] ) {
		$state = true;
	}
	return $state;
}

