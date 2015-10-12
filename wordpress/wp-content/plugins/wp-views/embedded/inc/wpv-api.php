<?php

/**
* wpv-api.php
*
* Contains all public APIs to be used by third-party developers
*
* @package Views
*
* @since 1.8.0
*/

/*
* ----------------------------------------------------------
* Get functions - get results
* ----------------------------------------------------------
*/

/**
* get_view_query_results
*
* Returns the result of a query filtered by a View.
*
* @param int $view_id ID of the relevant View
* @param object $post_in (optional) Sets the global $post
* @param object $current_user_in (optional) Sets the global $current_user
* @param array $args (optional) Attributes to pass to the View, like shortcode attributes when using [wpv-view]
*
* @return Array of $post objects if the View lists posts, $term objects if the View lists taxonomies or $user objects if the View lists users
*
* @usage  <?php echo get_view_query_results( 80 ); ?>
*
* @since unknown
*
*/
function get_view_query_results( $view_id, $post_in = null, $current_user_in = null, $args = array() ) {
	$view_post = get_post( $view_id );
	if (
		! $view_post 
		|| $view_post->post_status != 'publish' 
		|| $view_post->post_type != 'view'
	) {
		return array();
	}
	global $WP_Views, $post, $current_user, $authordata;
	// Save current globals to restore them later
	$post_old = $post;
	$current_user_old = $current_user;
	$authordata_old = $authordata;
	$items = array();
	if ( $post_in ) {
		$post = $post_in;
	}
	if ( $current_user_in ) {
		$current_user = $current_user_in;
	}
	$view_settings = $WP_Views->get_view_settings( $view_id );
	array_push( $WP_Views->view_shortcode_attributes, $args );
	$query_type = ( isset( $view_settings['query_type'][0] ) ) ? $view_settings['query_type'][0] : 'posts';
	switch ( $query_type ) {
		case 'posts':
			// get the posts using the query settings for this view.
			$archive_query = null;
			if ( 
				isset( $view_settings['view-query-mode'] ) 
				&& $view_settings['view-query-mode'] == 'archive' 
			) {
				// check for an archive loop
				global $WPV_view_archive_loop;
				if ( isset( $WPV_view_archive_loop ) ) {
					$archive_query = $WPV_view_archive_loop->get_archive_loop_query();
				}

			} else if ( 
				isset( $view_settings['view-query-mode'] ) 
				&& $view_settings['view-query-mode'] == 'layouts-loop' 
			) {
				global $wp_query;
				$archive_query = ( isset( $wp_query ) && ( $wp_query instanceof WP_Query ) ) ? clone $wp_query : null;
			}
			if ( $archive_query ) {
				$ret_query = $archive_query;
			} else {
				$ret_query = wpv_filter_get_posts( $view_id );
			}
			$items = $ret_query->posts;
			break;
		case 'taxonomy':
			$items = $WP_Views->taxonomy_query( $view_settings );
			break;
		case 'users':
			$items = $WP_Views->users_query( $view_settings );
			break;
	}
	array_pop( $WP_Views->view_shortcode_attributes );
	// Restore current globals
	$post = $post_old;
	$current_user = $current_user_old;
	$authordata = $authordata_old;
	return $items;
}

/*
* ----------------------------------------------------------
* Render functions
* ----------------------------------------------------------
*/

/**
* render_view
*
* Renders a View and returns the result.
*
* @param array $args {
*	 You can pass one of these keys:
* 	 $name The View post_name.
*	 $title The View post_title.
*	 $id The View post ID.
*	 $target_id The target page ID if you want to render just the View form.
* }
* @param array $post_override An array to be used to override $_GET values.
*
* @usage  <?php echo render_view( array( 'title' => 'Top pages' ) ); ?>
*
* @since unknown
*/

function render_view( $args, $get_override = array() ) {
	global $wpdb, $WP_Views;
	$id = 0;
    // Get View ID
	if ( isset( $args['id'] ) ) {
		$id = $args['id'];
	} elseif ( isset( $args['name'] ) ) {
		$id = $wpdb->get_var( 
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} 
				WHERE post_type = 'view' 
				AND post_name = %s 
				LIMIT 1",
				$args['name'] 
			) 
		);
	} elseif ( isset( $args['title'] ) ) {
		$id = $wpdb->get_var( 
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} 
				WHERE post_type = 'view' 
				AND post_title = %s 
				LIMIT 1",
				$args['title'] 
			) 
		);
	}
    $status = get_post_status( $id );
    // Views must be published in order to produce any output
	if ( 
		intval( $id ) > 0 
		&& $status == 'publish' 
	) {
		if ( ! empty( $get_override ) ) {
			$post_old = $_GET;
			foreach ( $get_override as $key => $value ) {
				$_GET[ $key ] = $value;
			}
		}
		$args['id'] = $id;
		array_push( $WP_Views->view_shortcode_attributes, $args );
		if ( isset( $args['target_id'] ) ) {
			$out = $WP_Views->short_tag_wpv_view_form( array( 'id' => $id, 'target_id' => $args['target_id'] ) );
		} else {
			$out = $WP_Views->render_view_ex( $id, md5( serialize( $args ) ) );
		}
		$WP_Views->view_used_ids[] = $id;
		array_pop( $WP_Views->view_shortcode_attributes );
		if ( !empty( $get_override ) ) {
			$_GET = $post_old;
		}
		return $out;
	} else {
		return '';
	}
}

/**
* render_view_template
*
* Returns the content of a Content Template applied to a Post
*
* @param integer	$view_template_id	ID of the relevant Content Template
* @param object		$post_in			Post to apply the Content Template to
* @param object		$current_user_in	Sets the global $current_user
* @param array		$args				Extra arguments to be used
*
* @usage  <?php echo render_view_template(80, $mypost)); ?>
*
* @note we need to set the global $authordata to the right user
*
* @since unknown
*/

function render_view_template( $view_template_id, $post_in = null, $current_user_in = null, $args = array() ) {
	$ct_post = get_post( $view_template_id );
	if (
		! $ct_post 
		|| $ct_post->post_status != 'publish' 
		|| $ct_post->post_type != 'view-template'
	) {
		return '';
	}
	
	global $WPV_templates, $post, $current_user, $authordata;
	// Save current globals to restore them later
	$post_old = $post;
	$current_user_old = $current_user;
	$authordata_old = $authordata;
	if ( $post_in ) {
		$post = $post_in;
		$authordata = new WP_User( $post->post_author );
	}
	if ( $current_user_in ) {
		$current_user = $current_user_in;
	}
	// Adjust for WPML support
	$view_template_id = apply_filters( 'translate_object_id', $view_template_id, 'view-template', true, null );
	$content = $WPV_templates->get_template_content( $view_template_id );
	// If this function returns null, $view_template_id does not exist or is not a Content Template or its status is different from 'publish'
	if ( is_null( $content ) ) {
		$content = '';
	} else {
		$WPV_templates->view_template_used_ids[] = $view_template_id;
		$output_mode = get_post_meta( $view_template_id, '_wpv_view_template_mode', true );
		if ( $output_mode == 'raw_mode' ) {
			$WPV_templates->remove_wpautop();
		}
		$content = wpml_content_fix_links_to_translated_content( $content );
		if (
			
			/**
			* wpv_filter_wpv_render_view_template_force_suppress_filters
			*
			* Force the use of the restricted wpv_filter_wpv_the_content_suppressed filter instead of the the_content one.
			*
			* @param bool 						Defaults to false
			* @param object	$ct_post 			The Content Template post object
			* @param object	$post_in			Post object to overwrote the global $post
			* @param object $current_user_in	User object that overwrote the global $current_user
			* @param array	$args				Extra arguments passed to the function
			*
			* Since 1.10
			*/

			apply_filters( 'wpv_filter_wpv_render_view_template_force_suppress_filters', false, $ct_post, $post_in, $current_user_in, $args )
			|| (
				isset( $args['suppress_filters'] )
				&& $args['suppress_filters']
			)
		) {
			$content = apply_filters( 'wpv_filter_wpv_the_content_suppressed', $content );
		} else {
			$content = apply_filters( 'the_content', $content );
		}
	}
	// Restore current globals
	$post = $post_old;
	$current_user = $current_user_old;
	$authordata = $authordata_old;
	return $content;
}

/*
* ----------------------------------------------------------
* Template tags
* ----------------------------------------------------------
*/

/**
* has_wpv_wp_archive
*
* Official API for checking whether an archive loop has a WPA assigned.
*
* Alias for wpv_has_wordpress_archive
*
* @since 1.8.0
*/

function has_wpv_wp_archive( $kind = 'other', $slug = 'home-blog' ) {
	return wpv_has_wordpress_archive( $kind, $slug );
}

/**
* wpv_has_wordpress_archive
*
* Checks if a given archive page has a WPA assigned to it. Defaults to check the home/blog archive loop.
*
* @param string $kind [post|taxonomy|other] The kind of archive to be checked
* @param string $slug The slug of the archive to be checked:
*	- if $kind is "post" then the slug of the post type
*	- if $kind is "taxonomy" then the slug of the taxonomy
*	- if $kind is "other" it can be [home-blog|search|author|year|month|day]
*
* @return (int) The ID of the assigned WPA or 0 if there is no one
*
* @since 1.6.0
*/

function wpv_has_wordpress_archive( $kind = 'other', $slug = 'home-blog' ) {
	global $WPV_settings;
	$return = 0;
	$identifier = '';
	switch ( $kind ) {
		case 'post':
			$identifier = 'view_cpt_' . $slug;
			break;
		case 'taxonomy':
			$identifier = 'view_taxonomy_loop_' . $slug;
			break;
		case 'other':
			$identifier = 'view_' . $slug . '-page';
			break;
	}
	if ( 
		! empty( $identifier ) 
		&& isset( $WPV_settings[$identifier] ) 
	) {
		$return = $WPV_settings[$identifier];
	}
	return $return;
}

/**
* is_wpv_wp_archive_assigned
*
* Check if the current page is an archive page and has a WPA assigned to it.
*
* @return bool
*
* @since 1.8.0 
*/

function is_wpv_wp_archive_assigned() {
	if (
		! is_archive() 
		&& ! is_home()
		&& ! is_search()
	) {
		return false;
	}
	global $WPV_settings;
	if ( is_home() ) {
		if ( 
			isset( $WPV_settings['view_home-blog-page'] ) 
			&& $WPV_settings['view_home-blog-page'] > 0 
		) {
			return true;
		} else {
			return false;
		}
	} else if ( is_search() ) {
		if (
			isset( $WPV_settings['view_search-page'] ) 
			&& $WPV_settings['view_search-page'] > 0 
		) {
			return true;
		} else {
			return false;
		}
	} else if ( is_author() ) {
		if (
			isset( $WPV_settings['view_author-page'] ) 
			&& $WPV_settings['view_author-page'] > 0 
		) {
			return true;
		} else {
			return false;
		}
	} else if ( is_year() ) {
		if (
			isset( $WPV_settings['view_year-page'] ) 
			&& $WPV_settings['view_year-page'] > 0 
		) {
			return true;
		} else {
			return false;
		}
	} else if ( is_month() ) {
		if (
			isset( $WPV_settings['view_month-page'] ) 
			&& $WPV_settings['view_month-page'] > 0 
		) {
			return true;
		} else {
			return false;
		}
	} else if ( is_day() ) {
		if (
			isset( $WPV_settings['view_day-page'] ) 
			&& $WPV_settings['view_day-page'] > 0 
		) {
			return true;
		} else {
			return false;
		}
	} else if ( 
		is_tax() 
		|| is_category() 
		|| is_tag() 
	) {
		global $wp_query;
		$queried_term = $wp_query->get_queried_object();
		if ( 
			$queried_term 
			&& isset( $queried_term->taxonomy )
			&& isset( $WPV_settings['view_taxonomy_loop_' . $queried_term->taxonomy] ) 
			&& $WPV_settings['view_taxonomy_loop_' . $queried_term->taxonomy] > 0 
		) {
			return true;
		} else {
			return false;
		}
	} else if ( is_post_type_archive() ) {
		global $wp_query;
		$queried_post_type = $wp_query->get('post_type');
		if ( is_array( $queried_post_type ) ) {
			$queried_post_type = reset( $queried_post_type );
		}
		if ( 
			isset( $WPV_settings['view_cpt_' . $queried_post_type] ) 
			&& $WPV_settings['view_cpt_' . $queried_post_type] > 0 
		) {
			return true;
		} else {
			return false;
		}
	}
	return false;
}

/**
* has_wpv_content_template
*
* Check if a given post has a CT assigned to it
*
* @param int $post_id The ID of the post to check
*
* @return (int) The ID of the assigned CT or 0 if there is no one
*
* @since 1.8.0
*/

function has_wpv_content_template( $post_id = null ) {
	$return = 0;
	$post_id = intval( $post_id );
	$template_selected = get_post_meta( $post_id, '_views_template', true );
	if (
		! empty( $template_selected ) 
		&& intval( $template_selected ) > 0
	) {
		return $template_selected;
	}
	return $return;
}

/**
* is_wpv_content_template_assigned
*
* Check if the current page is a singular one and has a CT assigned to it.
*
* @return bool
*
* @since 1.8.0 
*/

function is_wpv_content_template_assigned() {
	if ( is_singular() ) {
		global $post;
		$post = get_post( $post );
		if ( 
			is_null( $post ) 
			|| ! ( $post instanceof WP_Post )
		) {
			return false;
		}
		$template_selected = get_post_meta( $post->ID, '_views_template', true );
		if (
			! empty( $template_selected ) 
			&& intval( $template_selected ) > 0
		) {
			return true;
		}
	}
	return false;
}
