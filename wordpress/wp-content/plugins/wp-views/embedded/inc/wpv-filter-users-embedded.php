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
	if ( !isset( $view_settings['roles_type'] ) ) {
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

function get_users_query($view_settings) {
    global $WP_Views, $current_user, $wplogger, $no_parameter_found, $WPVDebug, $wpdb;

	$view_id = $WP_Views->get_current_view();
    $items = array();
    $args = array();
    $include = array();
    $exclude = array();

    $WPVDebug->add_log( 'info' , apply_filters('wpv-view-get-content-summary', '', $WP_Views->current_view, $view_settings) , 'short_query' );

    if ( isset( $view_settings['roles_type'][0] ) ){
        $args['role'] = $view_settings['roles_type'][0];
    }
    if ( isset( $view_settings['users-show-current'] ) && $view_settings['users-show-current'] == 1 ){
        $exclude[] = $current_user->ID;
    }
    if ( isset( $view_settings['users_orderby'] ) ){
        $args['orderby'] = $view_settings['users_orderby'];
    }
    if ( isset( $view_settings['users_order'] ) ){
        $args['order'] = $view_settings['users_order'];
    }

    // Users orderby and order based on URL params - for table sorting

    if (isset($_GET['wpv_column_sort_id']) && esc_attr($_GET['wpv_column_sort_id']) != '' && esc_attr($_GET['wpv_view_count']) == $WP_Views->get_view_count()) {
        $field = esc_attr($_GET['wpv_column_sort_id']);
        if ( in_array( $field, array('user_email', 'user_login', 'display_name', 'user_url', 'user_registered') ) ) {
		$args['orderby'] = $field;

		if (isset($_GET['wpv_column_sort_dir']) && esc_attr($_GET['wpv_column_sort_dir']) != '') {
			$args['order'] = strtoupper(esc_attr($_GET['wpv_column_sort_dir']));
		}

        }

    }

     //Limit & Offset
    if ( $view_settings['users_limit'] !== '-1' && $view_settings['users_limit'] !== -1 ){
        $args['number'] = $view_settings['users_limit'];
    }
    $args['offset'] = $view_settings['users_offset'];
    if ( $args['offset'] > 0 ) {
		if ( !isset( $args['number'] ) || ( isset( $args['number'] ) && $args['number'] < 1 ) ) {
			$args['number'] = 2147483647;
		}
    }

    // Users filter
    if ( isset($view_settings['users_mode']) && !empty($view_settings['users_mode'][0])  ){

        //Include/Exclude list of users
        if ( $view_settings['users_mode'][0] == 'this_user' ){
            if ( $view_settings['users_query_in'] == 'exclude' ){
                if ( !empty($view_settings['users_id']) ){
                    $user_list = array_map('trim', explode(',', $view_settings['users_id']));
                    $exclude = array_merge( $exclude, $user_list );
                }
            }
            if ( $view_settings['users_query_in'] == 'include' ){
                if ( !empty($view_settings['users_id']) ){
                    $user_list = array_map('trim', explode(',', $view_settings['users_id']));
                    $args['include'] = $user_list;
                }
            }
        }

        //Show user by url
        if ( $view_settings['users_mode'][0] == 'by_url' ){
            $user_list = array();
            $user_url = isset( $view_settings['users_url'] ) ? $view_settings['users_url'] : '';
            if ( '' != $user_url && isset( $_GET[$user_url] ) ) {
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
							if ( !is_null( $user_candidate_id ) && is_numeric( $user_candidate_id ) ) {
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
						if ( !is_null( $user_candidate_id ) && is_numeric( $user_candidate_id ) ) {
							$user_list[] = $user_candidate_id;
						}
                    }
                }

                if ( $view_settings['users_query_in'] == 'exclude' ){
                    $exclude = array_merge( $exclude, $user_list );
                } else {
					if ( empty( $user_list ) ) $user_list = array('0');
                    $args['include'] = $user_list;
                }
            }
        }

        //Show user by shortcode
        if ( $view_settings['users_mode'][0] == 'shortcode' ){
			$users_shortcode = '';
			$users_shortcode_type = '';
			if ( isset( $view_settings['users_shortcode'] ) && '' != $view_settings['users_shortcode'] ) {
				$users_shortcode = $view_settings['users_shortcode'];
			}
			if ( isset( $view_settings['users_shortcode_type'] ) && '' != $view_settings['users_shortcode_type'] ) {
				$users_shortcode_type = $view_settings['users_shortcode_type'];
			}
			if ( '' != $users_shortcode && '' != $users_shortcode_type ) {
				$view_attrs = $WP_Views->get_view_shortcodes_attributes();
				$user_list = array();
				if ( isset( $view_attrs[$users_shortcode] ) ){
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
								if ( !is_null( $user_id_candidate ) && is_numeric( $user_id_candidate ) ) {
									$user_list[] = $user_id_candidate;
								}
							}
							break;
					}
					if ( $view_settings['users_query_in'] == 'exclude' ){
						$exclude = array_merge( $exclude, $user_list );
					} else {
						if ( empty( $user_list ) ) $user_list = array('0');
						$args['include'] = $user_list;
					}
				}
			}
		}
	} // End users filter


    //Usermeta filter
    $total_meta = 0;
    foreach ($view_settings as $index => $value) {
       if ( preg_match("/usermeta-field-(.*)_type/",$index, $match) ){
           $field = $match[1];
           $type = $value;
           $compare = $view_settings['usermeta-field-'.$field.'_compare'];
           $value = $view_settings['usermeta-field-'.$field.'_value'];
           $value = wpv_apply_user_functions($value);
           if ( $value != $no_parameter_found ) {

		if ( $field == 'user_email' || $field == 'user_login' || $field == 'user_url' || $field == 'display_name' ){
    		$args['search'] = ''.$value.'';// remove * wildcards
    		$args['search_columns'] = array($field);
		}else{
    		$total_meta++;
    		$args['meta_query'][] = array( 'key' => $field, 'value' => $value, 'compare' => $compare, 'type' => $type );
		}
	   }

       }
    }
    if ( $total_meta >1 ){
        $args['meta_query']['relation'] = $view_settings['usermeta_fields_relationship'];
    }

    if ( !empty( $exclude ) ) {
	$args['exclude'] = $exclude;
    }

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
// $args['fields'] = 'all_with_meta';
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
* wpv_include_exclude_users
*
* Filter hooked late in wpv_filter_user_query to fix the use of exclude and include at the same time
*
* @param $args array of arguments to be passed to WP_User_Query
*
* @return $args
*
* @since 1.5.1
*/

add_filter( 'wpv_filter_user_query', 'wpv_include_exclude_users', 99 );

function wpv_include_exclude_users( $args ) {
	if ( isset( $args['include'] ) && is_array( $args['include'] ) && isset( $args['exclude'] ) && is_array( $args['exclude'] ) ) {
		$args_diff = array_diff( $args['include'], $args['exclude'] );
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
		$id_list = join( ',', $user_ids );
		$all_usermeta = $wpdb->get_results( "SELECT * FROM {$wpdb->usermeta} WHERE user_id IN ($id_list)", OBJECT );
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
	
	return $items;
	
}