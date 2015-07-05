<?php

/**
 * Add a filter to add the query by author to the $query
 */

add_filter( 'wpv_filter_query', 'wpv_filter_post_author', 13, 2 ); // we need to set a higher priority than the limit filter has because we use $query['post__in'] = array('0') on failure

function wpv_filter_post_author( $query, $view_settings ) {
	
	if ( isset( $view_settings['author_mode'][0] ) ) {
		$show_author_array = array();
		switch ( $view_settings['author_mode'][0] ) {
			case 'current_page':
				global $WP_Views;
				$current_page = $WP_Views->get_current_page();
				if ( $current_page ) {
					$show_author_array[] = $current_page->post_author;
				}
				break;
			case 'current_user':
				global $current_user;
				if ( is_user_logged_in() ) {
					get_currentuserinfo();
					$show_author_array[] = $current_user->ID; // set the array to only the current user ID if is logged in
				}
				break;
			case 'this_user':
				if (
					isset( $view_settings['author_id'] ) 
					&& is_numeric( $view_settings['author_id'] )
					&& $view_settings['author_id'] > 0
				) {
					$show_author_array[] = $view_settings['author_id']; // set the array to only the selected user ID
				}
				break;
			case 'parent_view':
				global $WP_Views;
				$parent_user_id = $WP_Views->get_parent_view_user();
				if ( $parent_user_id ) {
					$show_author_array[] = $parent_user_id;
				}
				break;
			case 'by_url':
				if (
					isset( $view_settings['author_url'] ) 
					&& '' != $view_settings['author_url']
					&& isset( $view_settings['author_url_type'] ) 
					&& '' != $view_settings['author_url_type']
				) {
					$author_parameter = $view_settings['author_url'];
					$author_url_type = $view_settings['author_url_type'];
					if ( isset( $_GET[$author_parameter] ) ) {
						$authors_to_load = $_GET[$author_parameter];
						if ( is_string( $authors_to_load ) ) {
							$authors_to_load = explode( ',', $authors_to_load );
						}
						if ( 1 == count( $authors_to_load ) ) {
							$authors_to_load = explode( ',', $authors_to_load[0] ); // fix on the pagination for the author filter
						}
						if ( 
							0 == count( $authors_to_load ) 
							|| '' == $authors_to_load[0] 
						) {
							// The URL parameter is empty
							$show_author_array = null;
						} else {
							// The URL parameter is not empty
							switch ( $author_url_type ) {
								case 'id':
									foreach ( $authors_to_load as $id_author_to_load ) {
										if ( is_numeric( $id_author_to_load ) ) { // if ID expected and not a number, skip it
											$show_author_array[] = $id_author_to_load; // if ID expected and is a number, add it to the array
										}
									}
									break;
								case 'username':
									foreach ( $authors_to_load as $username_author_to_load ) {
										$username_author_to_load = strip_tags( $username_author_to_load );
										$author_username_id = username_exists( $username_author_to_load );
										if ($author_username_id) {
											$show_author_array[] = $author_username_id; // if user exists, add it to the array
										}
									}
									break;
							}
						}
					} else {
						$show_author_array = null; // if the URL parameter is missing
					}
				}
				break;
			case 'shortcode':
				if (
					isset( $view_settings['author_shortcode'] ) 
					&& '' != $view_settings['author_shortcode']
					&& isset( $view_settings['author_shortcode_type'] ) 
					&& '' != $view_settings['author_shortcode_type']
				) {
					global $WP_Views;
					$author_shortcode = $view_settings['author_shortcode'];
					$author_shortcode_type = $view_settings['author_shortcode_type'];
					$view_attrs = $WP_Views->get_view_shortcodes_attributes();
					if ( isset( $view_attrs[$author_shortcode] ) ) {
						$author_candidates = explode( ',', $view_attrs[$author_shortcode] );
						switch ( $author_shortcode_type ) {
							case 'id':
								foreach ( $author_candidates as $id_candid ) {
									if ( is_numeric( $id_candid ) ) {
										$show_author_array[] = $id_candid;
									}
								}
								break;
							case 'username':
								foreach ( $author_candidates as $username_candid ) {
									$username_candid = trim( strip_tags( $username_candid ) );
									$username_candid_id = username_exists( $username_candid );
									if ( $username_candid_id ) {
										$show_author_array[] = $username_candid_id;
									}
								}						
								break;			
						}
					} else {
						$show_author_array = null;
					}
				}
				break;
			case 'framework':
				global $WP_Views_fapi;
				if ( $WP_Views_fapi->framework_valid ) {
					if (
						isset( $view_settings['author_framework'] ) 
						&& '' != $view_settings['author_framework']
						&& isset( $view_settings['author_framework_type'] ) 
						&& '' != $view_settings['author_framework_type']
					) {
						$author_framework = $view_settings['author_framework'];
						$author_framework_type = $view_settings['author_framework_type'];
						$author_candidates = $WP_Views_fapi->get_framework_value( $author_framework, array() );
						if ( ! is_array( $author_candidates ) ) {
							$author_candidates = explode( ',', $author_candidates );
						}
						$author_candidates = array_map( 'trim', $author_candidates );
						switch ( $author_framework_type ) {
							case 'id':
								foreach ( $author_candidates as $id_candid ) {
									if ( is_numeric( $id_candid ) ) {
										$show_author_array[] = $id_candid;
									}
								}
								break;
							case 'username':
								foreach ( $author_candidates as $username_candid ) {
									$username_candid = trim( strip_tags( $username_candid ) );
									// username_exists adds the sanitization
									$username_candid_id = username_exists( $username_candid );
									if ( $username_candid_id ) {
										$show_author_array[] = $username_candid_id;
									}
								}
								break;			
						}
					}
				} else {
					$show_author_array = null;
				}
				break;
		}
		
		if ( isset( $show_author_array ) ) { // only modify the query if the URL parameter is present and not empty
			if ( count( $show_author_array ) > 0 ) {
				// $query['author'] must be a string like 'id1,id2,id3'
				// because we're using &get_posts() to run the query
				// and it doesn't accept an array as author parameter
				$show_author_list = implode( ",", $show_author_array );
				if ( isset( $query['author'] ) ) {
					$query['author'] = implode( ",", array_merge( (array) $query['author'], $show_author_array ) );
				} else {
					$query['author'] = implode( ",", $show_author_array );
				}
			} else {
				// this only happens when:
				// - auth_mode = current_user and user is not logged in
				// - auth_mode = by_url and no numeric id or valid nicename is given
				// we need to return an empty query
				$query['post__in'] = array( '0' );
			}
		}
    }
    
	return $query;
}

/**
* wpv_filter_author_requires_current_page
*
* Whether the current View requires the current page data for the filter by author
*
* @param $state (boolean) the state of this need until this filter is applied
* @param $view_settings
*
* @return $state (boolean)
*
* @since 1.6.2
*/

add_filter( 'wpv_filter_requires_current_page', 'wpv_filter_author_requires_current_page', 20, 2 );

function wpv_filter_author_requires_current_page( $state, $view_settings ) {
	if ( $state ) {
		return $state; // Already set
	}
	if ( isset( $view_settings['author_mode'] ) && isset( $view_settings['author_mode'][0] ) && $view_settings['author_mode'][0] == 'current_page' ) {
		$state = true;
	}
	return $state;
}