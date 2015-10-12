<?php
/**
* wpv-filter-parent-embedded.php
*
* @package Views
*
* @since unknown
*/

/**
* wpv_filter_post_parent
*
* Apply the filter by post parent to the View query
*
* @since unknown
*/

add_filter( 'wpv_filter_query', 'wpv_filter_post_parent', 10, 2 );

function wpv_filter_post_parent( $query, $view_settings ) {
    if ( isset( $view_settings['parent_mode'][0] ) ) {
		switch ( $view_settings['parent_mode'][0] ) {
			case 'current_page':
				global $WP_Views;
				$current_page = $WP_Views->get_current_page();
				if ( $current_page ) {
					$query['post_parent'] = $current_page->ID;
				}
				break;
			case 'this_page':
				if (
					isset( $view_settings['parent_id'] )
					&& is_numeric( $view_settings['parent_id'] )
					&& $view_settings['parent_id'] > 0 
				) {
					$query['post_parent'] = $view_settings['parent_id'];
					// Adjust for WPML support
					// 'any' will make WPML manage the 'post_type' this parent belongs to
					$query['post_parent'] = apply_filters( 'translate_object_id', $query['post_parent'], 'any', true, null );
				} else {
					// filter for items with no parents
					$query['post_parent'] = 0;
				}
				break;
			case 'no_parent':
				$query['post_parent'] = 0;
				break;
			case 'shortcode_attribute':
				global $WP_Views;
				if (
					isset( $view_settings['parent_shortcode_attribute'] ) 
					&& '' != $view_settings['parent_shortcode_attribute']
				) {
					$parent_shortcode = $view_settings['parent_shortcode_attribute'];
					$view_attrs = $WP_Views->get_view_shortcodes_attributes();
					if ( 
						isset( $view_attrs[$parent_shortcode] ) 
						&& intval( $view_attrs[$parent_shortcode] ) > 0
					) {
						$query['post_parent'] = intval( $view_attrs[$parent_shortcode] );
						// Adjust for WPML support
						// 'any' will make WPML manage the 'post_type' this parent belongs to
						$query['post_parent'] = apply_filters( 'translate_object_id', $query['post_parent'], 'any', true, null );
					}
				}
				break;
			case 'url_parameter':
				if (
					isset( $view_settings['parent_url_parameter'] ) 
					&& '' != $view_settings['parent_url_parameter']
				) {
					$parent_url_parameter = $view_settings['parent_url_parameter'];
					if ( isset( $_GET[$parent_url_parameter] ) 
						&& $_GET[$parent_url_parameter] != array( 0 ) 
						&& $_GET[$parent_url_parameter] != 0 
					) {
						$post_owner_ids_from_url = $_GET[$parent_url_parameter];
						$post_owner_ids_sanitized = array();
						//$post_owner_ids_final = array();
						if ( is_array( $post_owner_ids_from_url ) ) {
							foreach ( $post_owner_ids_from_url as $id_value ) {
								$id_value = (int) esc_attr( trim( $id_value ) );
								if ( $id_value > 0 ) {
									// Adjust for WPML support
									// 'any' will make WPML manage the 'post_type' this parent belongs to
									$id_value = apply_filters( 'translate_object_id', $id_value, 'any', true, null );
									$post_owner_ids_sanitized[] = $id_value;
								}
							}
						} else {
							$post_owner_ids_from_url = (int) esc_attr( trim( $post_owner_ids_from_url ) );
							if ( $post_owner_ids_from_url > 0 ) {
								// Adjust for WPML support
								// 'any' will make WPML manage the 'post_type' this parent belongs to
								$post_owner_ids_from_url = apply_filters( 'translate_object_id', $post_owner_ids_from_url, 'any', true, null );
								$post_owner_ids_sanitized[] = $post_owner_ids_from_url;
							}
						}
						if ( count( $post_owner_ids_sanitized ) ) {
							$query['post_parent__in'] = $post_owner_ids_sanitized;
						}
					}
				}
				break;
			case 'framework':
				global $WP_Views_fapi;
				if ( $WP_Views_fapi->framework_valid ) {
					if (
						isset( $view_settings['parent_framework'] ) 
						&& '' != $view_settings['parent_framework']
					) {
						$parent_framework = $view_settings['parent_framework'];
						$parent_candidates = $WP_Views_fapi->get_framework_value( $parent_framework, array() );
						$parent_candidates_final = array();
						if ( ! is_array( $parent_candidates ) ) {
							$parent_candidates = explode( ',', $parent_candidates );
						}
						$parent_candidates = array_map( 'esc_attr', $parent_candidates );
						$parent_candidates = array_map( 'trim', $parent_candidates );
						// is_numeric + intval does sanitization
						$parent_candidates = array_filter( $parent_candidates, 'is_numeric' );
						$parent_candidates = array_map( 'intval', $parent_candidates );
						if ( count( $parent_candidates ) ) {
							foreach ( $parent_candidates as $parent_cand ) {
								// Adjust for WPML support
								// 'any' will make WPML manage the 'post_type' this parent belongs to
								$parent_cand = apply_filters( 'translate_object_id', $parent_cand, 'any', true, null );
								$parent_candidates_final[] = $parent_cand;
							}
						}
						if ( count( $parent_candidates_final ) ) {
							$query['post_parent__in'] = $parent_candidates_final;
						}
					}
				}
				break;
		}
    }
    
    return $query;
}

/**
* wpv_filter_parent_requires_current_page
*
* Check if the current filter by post parent needs info about the current page
*
* @since unknown
*/

add_filter( 'wpv_filter_requires_current_page', 'wpv_filter_parent_requires_current_page', 10, 2 );

function wpv_filter_parent_requires_current_page( $state, $view_settings ) {
	if ( $state ) {
		return $state;
	}
    if ( isset( $view_settings['parent_mode'][0] ) ) {
        if ( $view_settings['parent_mode'][0] == 'current_page' ) {
            $state = true;
        }
    }
    return $state;
}

/**
* wpv_filter_parent_requires_framework_values
*
* Check if the current filter by post parent needs info about the framework values
*
* @since 1.10
*/

add_filter( 'wpv_filter_requires_framework_values', 'wpv_filter_parent_requires_framework_values', 10, 2 );

function wpv_filter_parent_requires_framework_values( $state, $view_settings ) {
	if ( $state ) {
		return $state;
	}
    if ( isset( $view_settings['parent_mode'][0] ) ) {
        if ( $view_settings['parent_mode'][0] == 'framework' ) {
            $state = true;
        }
    }
    return $state;
}

/**
* wpv_filter_register_post_parent_filter_shortcode_attributes
*
* Register the filter by post IDs on the method to get View shortcode attributes
*
* @since 1.10
*/

add_filter( 'wpv_filter_register_shortcode_attributes_for_posts', 'wpv_filter_register_post_parent_filter_shortcode_attributes', 10, 2 );

function wpv_filter_register_post_parent_filter_shortcode_attributes( $attributes, $view_settings ) {
	if (
		isset( $view_settings['parent_mode'] ) 
		&& isset( $view_settings['parent_mode'][0] ) 
		&& $view_settings['parent_mode'][0] == 'shortcode_attribute' 
	) {
		$attributes[] = array (
			'query_type'	=> $view_settings['query_type'][0],
			'filter_type'	=> 'post_parent',
			'filter_label'	=> __( 'Post parent', 'wpv-views' ),
			'value'			=> 'post_parent',
			'attribute'		=> $view_settings['parent_shortcode_attribute'],
			'expected'		=> 'numberlist',
			'placeholder'	=> '10',
			'description'	=> __( 'Please type a post ID to get its native children', 'wpv-views' )
		);
	}
	return $attributes;
}