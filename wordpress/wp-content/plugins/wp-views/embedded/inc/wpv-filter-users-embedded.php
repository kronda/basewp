<?php

/**
* wpv_users_default_settings
*
* Sets the default settings for Views listing users
*
* @since 1.6.0
*/

add_filter( 'wpv_view_settings', 'wpv_users_default_settings' );

function wpv_users_default_settings( $view_settings ) {
	if ( ! isset( $view_settings['roles_type'] ) ) {
		$view_settings['roles_type'] = array();
	}
	return $view_settings;
}

/**
* get_users_query
*
* Performs the WP_User_Query when a View lists users
*
* @param $view_settings the View settings
*
* @return $items an array of User objects
*
* @note username_exists() does its own sanitization and data validation in WP_User::get_data_by()
*
* @since 1.4.0
*/

function get_users_query( $view_settings ) {
    global $WP_Views, $wplogger, $WPVDebug, $wpdb;

	$view_id = $WP_Views->get_current_view();
    $items = array();
    $args = array();

    $WPVDebug->add_log( 'info' , apply_filters('wpv-view-get-content-summary', '', $WP_Views->current_view, $view_settings) , 'short_query' );

    $wplogger->log($args, WPLOG_DEBUG);

    $WPVDebug->add_log( 'info' , "Basic query arguments\n". print_r($args, true) , 'query_args' );

    /**
	* Filter wpv_filter_user_query
	*
	* This is where all the filters coming from the View settings to modify the query are (or should be) hooked
	*
	* @param $args the relevant elements of the View settings in an array to be used as arguments in a WP_User_Query() call
	* @param $view_settings the View settings
	* @param $view_id the ID of the View being displayed
	*
	* @return $args
	*
	* @since 1.4.0
	*/

    $args = apply_filters( 'wpv_filter_user_query', $args, $view_settings, $view_id );

    $WPVDebug->add_log( 'filters' , "wpv_filter_user_query\n" . print_r($args, true) , 'filters', 'Filter arguments before the query using <strong>wpv_filter_user_query</strong>' );

    $user_query = new WP_User_Query( $args );

	if ( !empty($wpdb->queries) ){
		$WPVDebug->add_log( 'mysql_query' , $wpdb->queries , 'users' );
	}

	$WPVDebug->add_log( 'info' , print_r($user_query, true) , 'query_results' , '' , true );

    if ( ! empty( $user_query->results ) ) {
        $items = $user_query->results;
    }

    /**
	* Filter wpv_filter_user_post_query
	*
	* Filter applied to the results of the WP_User_Query() call
	*
	* @param $items array of terms returned by the WP_User_Query() call
	* @param $args the relevant elements of the View settings in an array to be used as arguments in a WP_User_Query() call
	* @param $view_settings the View settings
	* @param $view_id the ID of the View being displayed
	*
	* @return $items
	*
	* @since 1.4.0
	*/

    $items = apply_filters( 'wpv_filter_user_post_query', $items, $args, $view_settings, $view_id );

	$WPVDebug->add_log( 'filters' , "wpv_filter_user_post_query\n" . print_r($items, true) , 'filters', 'Filter the returned query using <strong>wpv_filter_user_post_query</strong>' );

    return $items;
}

/**
* wpv_users_query_include_role
*
* Apply the setting about user roles
*
* @since 1.6.2
*/

add_filter( 'wpv_filter_user_query', 'wpv_users_query_include_role', 20, 2 );

function wpv_users_query_include_role( $args, $view_settings ) {
	$args['role'] = 'administrator';
	if ( isset( $view_settings['roles_type'][0] ) ){
        $args['role'] = $view_settings['roles_type'][0];
    }
	if ( $args['role'] == 'any' ) {
		unset( $args['role'] );
	}
	return $args;
}

/**
* wpv_users_query_include_current_user
*
* Apply setting about excluding current user
*
* @since 1.6.2
*/

add_filter( 'wpv_filter_user_query', 'wpv_users_query_include_current_user', 30, 2 );

function wpv_users_query_include_current_user( $args, $view_settings ) {
	global $current_user;
	if ( 
		isset( $view_settings['users-show-current'] ) 
		&& $view_settings['users-show-current'] == 1 
	) {
		if ( 
			! isset( $args['exclude'] ) 
			|| ! is_array( $args['exclude'] ) 
		) {
			$args['exclude'] = array($current_user->ID);	
		} elseif ( 
			isset( $args['exclude'] ) 
			&& ! in_array( $current_user->ID, $args['exclude'] ) 
		) {
			$args['exclude'] = 	array_merge( $args['exclude'], array($current_user->ID) );
		}
	}
	return $args;
}

/**
* wpv_users_query_user_filters
*
* Apply filter by specific users
*
* @since 1.6.2
*/

add_filter( 'wpv_filter_user_query', 'wpv_users_query_user_filters', 60, 2 );

function wpv_users_query_user_filters( $args, $view_settings ) {
	global $WP_Views;
	$exclude = array();
	// Users filter
    if ( 
		isset( $view_settings['users_mode'] ) 
		&& ! empty( $view_settings['users_mode'][0] ) 
	) {
		switch ( $view_settings['users_mode'][0] ) {
			case 'this_user':
				if ( $view_settings['users_query_in'] == 'exclude' ) {
					if ( ! empty($view_settings['users_id'] ) ){
						$user_list = array_map( 'trim', explode( ',', $view_settings['users_id'] ) );
						$exclude = array_merge( $exclude, $user_list );
					}
				}
				if ( $view_settings['users_query_in'] == 'include' ) {
					if ( ! empty( $view_settings['users_id'] ) ) {
						$user_list = array_map( 'trim', explode( ',', $view_settings['users_id'] ) );
						$args['include'] = $user_list;
					}
				}
				break;
			case 'by_url':
				if (
					isset( $view_settings['users_url'] )
					&& '' != $view_settings['users_url']
					&& isset( $view_settings['users_url_type'] )
					&& '' != $view_settings['users_url_type']
				) {
					$user_url = $view_settings['users_url'];
					$user_url_type = $view_settings['users_url_type'];
					if ( isset( $_GET[$user_url] ) ) {
						$user_list = array();
						if ( is_array( $_GET[$user_url] ) ) {
							if ( $view_settings['users_url_type'] == 'id' ) {
								foreach ( $_GET[$user_url] as $user_candidate ) {
									if ( is_numeric( $user_candidate ) ) {
										$user_list[] = $user_candidate;
									}
								}
							} else {
								foreach ( $_GET[$user_url] as $user_candidate ) {
									$user_candidate_id = username_exists( $user_candidate );
									if ( 
										! is_null( $user_candidate_id ) 
										&& is_numeric( $user_candidate_id ) 
									) {
										$user_list[] = $user_candidate_id;
									}
								}
							}
						} else {
							if ( $view_settings['users_url_type'] == 'id' ){
								if ( is_numeric( $_GET[$user_url] ) ) {
									$user_list = array( $_GET[$user_url] );
								}
							} else {
								$user_candidate_id = username_exists( $_GET[$user_url] );
								if ( 
									! is_null( $user_candidate_id ) 
									&& is_numeric( $user_candidate_id ) 
								) {
									$user_list[] = $user_candidate_id;
								}
							}
						}
						
						if ( $view_settings['users_query_in'] == 'exclude' ) {
							$exclude = array_merge( $exclude, $user_list );
						} else {
							if ( empty( $user_list ) ) {
								$user_list = array('0');
							}
							$args['include'] = $user_list;
						}
					}
				}
				break;
			case 'shortcode':
				if ( 
					isset( $view_settings['users_shortcode'] ) 
					&& '' != $view_settings['users_shortcode'] 
					&& isset( $view_settings['users_shortcode_type'] ) 
					&& '' != $view_settings['users_shortcode_type'] 
				) {
					$users_shortcode = $view_settings['users_shortcode'];
					$users_shortcode_type = $view_settings['users_shortcode_type'];
					$view_attrs = $WP_Views->get_view_shortcodes_attributes();
					$user_list = array();
					if ( isset( $view_attrs[$users_shortcode] ) ) {
						$users = $view_attrs[$users_shortcode];
						$users = array_map( 'trim', explode( ',', $users ) );
						switch ( $users_shortcode_type ) {
							case 'id':
								foreach ( $users as $user_candidate ) {
									if ( is_numeric( $user_candidate ) ) {
										$user_list[] = $user_candidate;
									}
								}
								break;
							default:
								foreach ( $users as $user_candidate ) {
									$user_id_candidate = username_exists( $user_candidate );
									if ( 
										! is_null( $user_id_candidate ) 
										&& is_numeric( $user_id_candidate ) 
									) {
										$user_list[] = $user_id_candidate;
									}
								}
								break;
						}
						if ( $view_settings['users_query_in'] == 'exclude' ) {
							$exclude = array_merge( $exclude, $user_list );
						} else {
							if ( empty( $user_list ) ) {
								$user_list = array('0');
							}
							$args['include'] = $user_list;
						}
					}
				}
				break;
			case 'framework':
				global $WP_Views_fapi;
				if ( 
					$WP_Views_fapi->framework_valid
					&& isset( $view_settings['users_framework'] ) 
					&& '' != $view_settings['users_framework'] 
					&& isset( $view_settings['users_framework_type'] ) 
					&& '' != $view_settings['users_framework_type'] 
				) {
					$users_framework = $view_settings['users_framework'];
					$users_framework_type = $view_settings['users_framework_type'];
					$user_list = array();
					$users_candidates = $WP_Views_fapi->get_framework_value( $users_framework, array() );
					if ( ! is_array( $users_candidates ) ) {
						$users_candidates = explode( ',', $users_candidates );
					}
					$users_candidates = array_map( 'trim', $users_candidates );
					switch ( $users_framework_type ) {
						case 'id':
							foreach ( $users_candidates as $id_candid ) {
								if ( is_numeric( $id_candid ) ) {
									$user_list[] = $id_candid;
								}
							}
							break;
						case 'username':
							foreach ( $users_candidates as $username_candid ) {
								$username_candid = trim( strip_tags( $username_candid ) );
								// username_exists adds the sanitization
								$username_candid_id = username_exists( $username_candid );
								if ( $username_candid_id ) {
									$user_list[] = $username_candid_id;
								}
							}
							break;			
					}
					if ( $view_settings['users_query_in'] == 'exclude' ) {
						$exclude = array_merge( $exclude, $user_list );
					} else {
						if ( empty( $user_list ) ) {
							$user_list = array('0');
						}
						$args['include'] = $user_list;
					}
				}
				break;
		}
	}
	
	if ( 
		! isset( $args['exclude'] ) 
		|| ! is_array( $args['exclude'] ) ) {
		$args['exclude'] = $exclude;	
	} elseif ( isset( $args['exclude'] ) ) {
		$args['exclude'] = 	array_merge( $args['exclude'], $exclude );
	}
	
	return $args;
}

/**
* wpv_filter_register_specific_users_shortcode_attributes
*
* Register the filter by specific users on the method to get View shortcode attributes
*
* @since 1.10
*/

add_filter( 'wpv_filter_register_shortcode_attributes_for_users', 'wpv_filter_register_specific_users_shortcode_attributes', 10, 2 );

function wpv_filter_register_specific_users_shortcode_attributes( $attributes, $view_settings ) {
	if (
		isset( $view_settings['users_mode'] ) 
		&& isset( $view_settings['users_mode'][0] ) 
		&& $view_settings['users_mode'][0] == 'shortcode' 
	) {
		$action = __( 'include', 'wpv-views' );
		if ( $view_settings['users_query_in'] == 'exclude' ) {
			$action = __( 'exclude', 'wpv-views' );
		}
		$attributes[] = array(
			'query_type'	=> $view_settings['query_type'][0],
			'filter_type'	=> 'user_specific',
			'filter_label'	=> __( 'Specific users', 'wpv-views' ),
			'value'			=> $view_settings['users_shortcode_type'],
			'attribute'		=> $view_settings['users_shortcode'],
			'expected'		=> ( $view_settings['users_shortcode_type'] == 'id' ) ? 'numberlist' : 'string',
			'placeholder'	=> ( $view_settings['users_shortcode_type'] == 'id' ) ? '2, 4, 7, 8' : 'admin, john, mary',
			'description'	=> ( $view_settings['users_shortcode_type'] == 'id' ) 
				? sprintf( __( 'Please type a comma separated list of user IDs to %s', 'wpv-views' ), $action ) 
				: sprintf( __( 'Please type a comma separated list of usernames to %s', 'wpv-views' ), $action )
		);
	}
	return $attributes;
}

/**
* wpv_include_exclude_users
*
* Fix the WP_User_Query args when they contain both include and exclude parameters
*
* @since 1.5.1
*/

add_filter( 'wpv_filter_user_query', 'wpv_include_exclude_users', 99 );

function wpv_include_exclude_users( $args ) {
	if ( 
		isset( $args['include'] ) 
		&& is_array( $args['include'] ) 
		&& isset( $args['exclude'] ) 
		&& is_array( $args['exclude'] ) 
	) {
		$args_diff = array_diff( $args['include'], $args['exclude'] );
		if ( empty( $args_diff ) ) {
			$args_diff = array( '0' );
		}
		$args['include'] = $args_diff;
	}
	return $args;
}

/**
* wpv_cache_complete_usermeta_for_types
*
* Caches all the usermeta for the users returned by a WP_User_Query performed by a View listing users
*
* @param $items an array of User objects
*
* @return $items
*
* @since 1.5.1
*/

add_filter( 'wpv_filter_user_post_query', 'wpv_cache_complete_usermeta_for_types' );

function wpv_cache_complete_usermeta_for_types( $items ) {
	global $wpdb;
	
	if ( empty( $items ) )
		return $items;
	
	// Only add the Types usermeta cache if Types is active
	if ( defined( 'WPCF_VERSION' ) ) {
	
		$user_ids = array();
		$cache_group_ids = 'types_cache_user_ids';
		$cache_group = 'types_cache';
		
		foreach ( $items as $user ) {
			$cache_key_looped_post = md5( 'user::_is_cached' . $user->ID );
			$cached_object = wp_cache_get( $cache_key_looped_post, $cache_group_ids );
			if ( false === $cached_object ) {
				$user_ids[] = $user->ID;
				wp_cache_add( $cache_key_looped_post, $user->ID, $cache_group_ids );
			}
		}
		if ( ! empty( $user_ids ) ) {
			$id_list = implode( ',', $user_ids );
			// We do not need to prepare this query as $id_list only contains numeric natural IDs
			$all_usermeta = $wpdb->get_results( 
				"SELECT * FROM {$wpdb->usermeta} 
				WHERE user_id IN ({$id_list})", 
				OBJECT 
			);
			if ( !empty( $all_usermeta ) ) {
				$cache_key_keys = array();
				foreach ( $all_usermeta as $metarow ) {
					$mpid = intval($metarow->user_id);
					$mkey = $metarow->meta_key;
					$cache_key_keys[$mpid . $mkey][] = $metarow;
				}
				foreach ( $cache_key_keys as $single_meta_keys => $single_meta_values ) {
					$cache_key_looped_new = md5( 'usermeta::_get_meta' . $single_meta_keys );
					wp_cache_add( $cache_key_looped_new, $single_meta_values, $cache_group );// WordPress cache
				}
			}
		}
	}
	
	return $items;
}

/**
* wpv_filter_user_requires_framework_values
*
* Whether the current View requires framework valus for the filter by specific users
*
* @param $state (boolean) the state of this need until this filter is applied
* @param $view_settings
*
* @return $state (boolean)
*
* @since 1.10
*/

add_filter( 'wpv_filter_requires_framework_values', 'wpv_filter_user_requires_framework_values', 10, 2 );

function wpv_filter_user_requires_framework_values( $state, $view_settings ) {
	if ( $state ) {
		return $state; // Already set
	}
    if (
		isset( $view_settings['users_mode'] ) 
		&& isset( $view_settings['users_mode'][0] )
		&& $view_settings['users_mode'][0] == 'framework'
	) {
        $state = true;
    }
	return $state;

}