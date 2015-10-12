<?php

/**
* wpv-filter-order-by-embedded.php
*
* @package Views
*
* @since unknown
*/

/**
* wpv_order_orderby_default_settings
*
* Sets the default order and orderby settings
*
* @since unknown
*/

add_filter( 'wpv_view_settings', 'wpv_order_orderby_default_settings' );

function wpv_order_orderby_default_settings( $view_settings ) {
	if ( ! isset( $view_settings['orderby'] ) ) {
		$view_settings['orderby'] = 'post_date';
	}
	if ( ! isset( $view_settings['order'] ) ) {
		$view_settings['order'] = 'DESC';
	}
	if ( ! isset( $view_settings['taxonomy_orderby'] ) ) {
		$view_settings['taxonomy_orderby'] = 'name';
	}
	if ( ! isset( $view_settings['taxonomy_order'] ) ) {
		$view_settings['taxonomy_order'] = 'DESC';
	}
	if ( ! isset( $view_settings['users_orderby'] ) ) {
		$view_settings['users_orderby'] = 'user_login';
	}
	if ( ! isset( $view_settings['users_order'] ) ) {
		$view_settings['users_order'] = 'DESC';
	}
	return $view_settings;
}

/**
* wpv_filter_get_order_arg
*
* Apply order and orderby settings to Views listing posts
*
* @since unknown
*
* @note Make this happens after custom fields
*/

add_filter( 'wpv_filter_query', 'wpv_filter_get_order_arg', 100, 2 );

function wpv_filter_get_order_arg( $query, $view_settings ) {
	global $WP_Views;
    $orderby = $view_settings['orderby'];
	$order = $view_settings['order'];
	// Override with attributes
	$override_allowed = array(
		'orderby'	=> array(),
		'order'		=> array( 'asc', 'ASC', 'desc', 'DESC' )
	);
	$override_values = wpv_override_view_orderby_order( $override_allowed );
	if ( isset( $override_values['orderby'] ) ) {
		$orderby = $override_values['orderby'];
	}
	if ( isset( $override_values['order'] ) ) {
		$order = strtoupper( $override_values['order'] );
	}
	
	// Override with URL parameters
	
	/*
	* -------------
	* Order
	* -------------
	*/
	
	// Legacy order URL override
    if ( 
		isset( $_GET['wpv_order'] ) 
		&& isset( $_GET['wpv_view_count'] )
		&& isset( $_GET['wpv_order'][0] )
		&& in_array( $_GET['wpv_order'][0], array( 'ASC', 'DESC' ) )
	) {
        $order = esc_attr( $_GET['wpv_order'][0] );
    }
	// Modern order URL override
	if (
		isset( $_GET['wpv_column_sort_dir'] ) 
		&& isset( $_GET['wpv_view_count'] )
		&& esc_attr( $_GET['wpv_view_count'] ) == $WP_Views->get_view_count()
		&& in_array( strtoupper( esc_attr( $_GET['wpv_column_sort_dir'] ) ), array( 'ASC', 'DESC' ) )
	) {
        $order = strtoupper( esc_attr( $_GET['wpv_column_sort_dir'] ) );
    }
	
	/*
	* -------------
	* Orderby
	* -------------
	*/
	
    if (
		isset( $_GET['wpv_column_sort_id'] ) 
		&& isset( $_GET['wpv_view_count'] )
		&& esc_attr( $_GET['wpv_column_sort_id'] ) != 'undefined' 
		&& esc_attr( $_GET['wpv_column_sort_id'] ) != '' 
		&& esc_attr( $_GET['wpv_view_count'] ) == $WP_Views->get_view_count() 
	) {
        $orderby = esc_attr( $_GET['wpv_column_sort_id'] );
    }
    
	// Adjust values for custom field sorting
    
    if ( strpos( $orderby, 'field-' ) === 0 ) {
        // Natural Views sorting by custom field
        $query['meta_key'] = substr( $orderby, 6 );
        $orderby = 'meta_value';
    } else if ( strpos( $orderby, 'post-field' ) === 0 ) {
		// Table sorting for custom field
		$query['meta_key'] = substr( $orderby, 11 );
		$orderby = 'meta_value';
	} else if ( strpos( $orderby, 'types-field' ) === 0 ) {
		// Table sorting for Types custom field
		$query['meta_key'] = strtolower( substr( $orderby, 12 ) );
		$orderby = 'meta_value';
	} else {
		$orderby = str_replace( '-', '_', $orderby );
	}
	
	if ( 
		'meta_value' == $orderby 
		&& isset( $query['meta_key'] )
	) {
		$is_types_field_data = wpv_is_types_custom_field ( $query['meta_key'] );
		if ( 
			$is_types_field_data 
			&& isset( $is_types_field_data['meta_key'] ) 
			&& isset( $is_types_field_data['type'] )
		) {
			$query['meta_key'] = $is_types_field_data['meta_key'];
			if ( in_array( $is_types_field_data['type'], array( 'numeric', 'date' ) ) ) {
				$orderby = 'meta_value_num';
			}
		}		
	}
    
    // Correct orderby options
	switch ( $orderby ) {
		case 'post_link':
			$orderby = 'post_title';
			break;
		case 'post_body':
			$orderby = 'post_content';
			break;
		case 'post_slug':
			$orderby = 'name';
			break;
		case 'post_id':
		case 'id':
			$orderby = 'ID';
			break;
		default:
			if ( strpos( $orderby, 'post_' ) === 0 ) {
				$orderby = substr( $orderby, 5 );
			}
			break;
	}
	
	$query['orderby'] = $orderby;
	$query['order'] = $order;
	
    // See if filtering by custom fields and sorting by custom field too
    if (
		isset( $query['meta_key'] ) 
		&& isset( $query['meta_query'] )
	) {
		// We only need to do something if the relation is OR
		// When the relation is AND it does not matter if we sort by one of the filtering fields, because the filter will add an existence clause anyway
		// When the relation is OR, the natural query will generate an OR clause on the sorting field existence:
		// - if it is one of the filtering fields, it will make its clause useless because just existence will make it pass
		// - if it is not one of the filtering fields it will add an OR clause on this field existence that might pass for results that do not match any of the other requirements
		// See also: https://core.trac.wordpress.org/ticket/25538
		// Since WordPress 4.1 this is indeed not needed, thanks to nested meta_query entries
		if ( 
			isset( $query['meta_query']['relation'] ) 
			&& $query['meta_query']['relation'] == 'OR' 
		) {
			global $wp_version;
			if ( version_compare( $wp_version, '4.1', '<' ) ) {
				$refinedquery = $query;
				unset( $refinedquery['orderby'] );
				unset( $refinedquery['meta_key'] );
				$refinedquery['posts_per_page'] = -1; // remove the limit in the main query to get all the relevant IDs
				$refinedquery['fields'] = 'ids';
				// first query only for filtering
				$filtered_query = new WP_Query( $refinedquery );
				$filtered_ids = array();
				if ( 
					is_array( $filtered_query->posts ) 
					&& !empty( $filtered_query->posts ) 
				) {
					$filtered_ids = $filtered_query->posts;
				}
				// remove the fields filter from the original query and add the filtered IDs
				unset( $query['meta_query'] );
				// we can replace the $query['post__in'] argument because it was applied on the auxiliar query before
				if ( count( $filtered_ids ) ) {
					$query['post__in'] = $filtered_ids;
				} else {
					$query['post__in'] = array('0');
				}
			}
        }
        
    }
	
    return $query;
}

/**
* wpv_taxonomy_query_add_sort
*
* Apply sorting settings to Views listing taxonomy terms
*
* @since 1.10
*/

add_filter( 'wpv_filter_taxonomy_query', 'wpv_taxonomy_query_add_sort', 10, 3 );

function wpv_taxonomy_query_add_sort( $tax_query_settings, $view_settings, $view_id ) {
	global $WP_Views;
	$orderby = $view_settings['taxonomy_orderby'];
	$order = $view_settings['taxonomy_order'];
	// Override with attributes
	$override_allowed = array(
		'orderby'	=> array( 'id', 'count', 'name', 'slug' ),
		'order'		=> array( 'asc', 'ASC', 'desc', 'DESC' )
	);
	$override_values = wpv_override_view_orderby_order( $override_allowed );
	if ( 
		isset( $override_values['orderby'] ) 
		&& in_array( $override_values['orderby'], $override_allowed['orderby'] )
	) {
		$orderby = $override_values['orderby'];
	}
	if ( isset( $override_values['order'] ) ) {
		$order = strtoupper( $override_values['order'] );
	}
	// Override with URL parameters
	if (
		isset( $_GET['wpv_view_count'] )
		&& esc_attr( $_GET['wpv_view_count'] ) == $WP_Views->get_view_count()
	) {
		if (
			isset( $_GET['wpv_column_sort_id'] ) 
			&& esc_attr( $_GET['wpv_column_sort_id'] ) != '' 
		) {
			$field = esc_attr( $_GET['wpv_column_sort_id'] );
			if ( $field == 'taxonomy-link' ) {
				$orderby = 'name';
			} else if ( $field == 'taxonomy-title' ) {
				$orderby = 'name';
			} else if ( $field == 'taxonomy-post_count' ) {
				$orderby = 'count';
			}
		}
		if (
			isset( $_GET['wpv_column_sort_dir'] ) 
			&& esc_attr( $_GET['wpv_column_sort_dir'] ) != '' 
			&& in_array( strtoupper( esc_attr( $_GET['wpv_column_sort_dir'] ) ), array( 'ASC', 'DESC' ) )
		) {
			$order = strtoupper( esc_attr( $_GET['wpv_column_sort_dir'] ) );
		}
	}
	$tax_query_settings['orderby'] = $orderby;
	$tax_query_settings['order'] = $order;
	return $tax_query_settings;
}

/**
* wpv_users_query_add_sort
*
* Apply sorting settings to Views listing users
*
* @since 1.6.2
*/

add_filter( 'wpv_filter_user_query', 'wpv_users_query_add_sort', 40, 2 );

function wpv_users_query_add_sort( $args, $view_settings ) {
	global $WP_Views;
	$orderby = '';
	$order = '';
	// @todo check this is most likely set!! No need to pretend it might not be
	if ( isset( $view_settings['users_orderby'] ) ) {
        $orderby = $view_settings['users_orderby'];
    }
    if ( isset( $view_settings['users_order'] ) ) {
        $order = $view_settings['users_order'];
    }
	// Override with attributes
	$override_allowed = array(
		'orderby'	=> array( 'user_email', 'user_login', 'display_name', 'user_url', 'user_registered' ),
		'order'		=> array( 'asc', 'ASC', 'desc', 'DESC' )
	);
	$override_values = wpv_override_view_orderby_order( $override_allowed );
	if ( 
		isset( $override_values['orderby'] ) 
		&& in_array( $override_values['orderby'], $override_allowed['orderby'] )
	) {
		$orderby = $override_values['orderby'];
	}
	if ( isset( $override_values['order'] ) ) {
		$order = strtoupper( $override_values['order'] );
	}
    // Override with URL parameters
	if (
		isset( $_GET['wpv_view_count'] )
		&& esc_attr( $_GET['wpv_view_count'] ) == $WP_Views->get_view_count()
	) {
		if (
			isset( $_GET['wpv_column_sort_id'] ) 
			&& esc_attr( $_GET['wpv_column_sort_id'] ) != '' 
			&& in_array( esc_attr( $_GET['wpv_column_sort_id'] ), array('user_email', 'user_login', 'display_name', 'user_url', 'user_registered') )
		) {
			$orderby = $field;
		}
		if (
			isset( $_GET['wpv_column_sort_dir'] ) 
			&& esc_attr( $_GET['wpv_column_sort_dir'] ) != '' 
			&& in_array( strtoupper( esc_attr( $_GET['wpv_column_sort_dir'] ) ), array( 'ASC', 'DESC' ) )
		) {
			$order = strtoupper( esc_attr( $_GET['wpv_column_sort_dir'] ) );
		}
	}
	if ( ! empty( $orderby ) ) {
		$args['orderby'] = $orderby;
	}
	if ( ! empty( $order ) ) {
		$args['order'] = $order;
	}
	return $args;
}

/*
* wpv_override_view_orderby_order
*
* Auxiliary function that will provide limit and offset settings coming from the Views shortcode atributes, if possible
*
* @param $allowed (array) Valid values that can be used to override
*
* @return $return (array)
*
* @since 1.10
*/

function wpv_override_view_orderby_order( $allowed = array() ) {
	$defaults = array(
		'orderby'	=> array(),
		'order'		=> array()
	);
	$allowed = wp_parse_args( $allowed, $defaults );
	global $WP_Views;
	$return = array();
	$view_attrs = $WP_Views->get_view_shortcodes_attributes();
	if ( isset( $view_attrs['orderby'] ) ) {
		if ( count( $allowed['orderby'] ) > 0 ) {
			if ( in_array( $view_attrs['orderby'], $allowed['orderby'] ) ) {
				$return['orderby'] = $view_attrs['orderby'];
			}
		} else {
			$return['orderby'] = $view_attrs['orderby'];
		}
	}
	if ( isset( $view_attrs['order'] ) ) {
		if ( count( $allowed['order'] ) > 0 ) {
			if ( in_array( $view_attrs['order'], $allowed['order'] ) ) {
				$return['order'] = $view_attrs['order'];
			}
		} else {
			$return['order'] = $view_attrs['order'];
		}
	}
	return $return;
}

// @todo temporary shortcodes

add_shortcode( 'wpv-orderby', 'wpv_shortcode_wpv_orderby' );

function wpv_shortcode_wpv_orderby( $atts ) {
	extract(
		shortcode_atts( array(
			'values'			=> '',
			'display_values'	=> '',
			'default'			=> '',
			'empty'				=> ''
		), $atts )
	);
	$return = '';
	$values = explode( ',', $values );
	$values = array_map( 'trim', $values );
	$values = array_map( 'sanitize_text_field', $values );
	$display_values = explode( ',', $display_values ) ;
	$display_values = array_map( 'trim', $display_values );
	$display_values = array_map( 'sanitize_text_field', $display_values );
	if ( 
		count( $values ) != count( $display_values ) 
		|| empty( $values )
	) {
		return $return;
	}
	$selected = isset( $_GET['wpv_column_sort_id'] ) ? esc_attr( $_GET['wpv_column_sort_id'] ) : esc_attr( $default );
	$return .= '<select name="wpv_column_sort_id" class="js-wpv-filter-trigger">';
		if ( ! empty( $empty ) ) {
			// Empty
			$return .= '<option value="">';
			$return .= $empty;
			$return .= '</option>';
		}
	foreach ( $values as $key => $val ) {
		$return .= '<option value="' . esc_attr( $val ) . '" ' . selected( $val, $selected, false ) . '>';
		$return .= $display_values[$key];
		$return .= '</option>';
	}
	$return .= '</select>';
	return $return;
}

add_shortcode( 'wpv-order', 'wpv_shortcode_wpv_order' );

function wpv_shortcode_wpv_order( $atts ) {
	extract(
		shortcode_atts( array(
			'asc'		=> 'ASC',
			'desc'		=> 'DESC',
			'default'	=> '',
			'empty'		=> ''
		), $atts )
	);
	$return = '';
	$selected = isset( $_GET['wpv_column_sort_dir'] ) ? esc_attr( $_GET['wpv_column_sort_dir'] ) : esc_attr( $default );
	$selected = strtolower( $selected );
	$return .= '<select name="wpv_column_sort_dir" class="js-wpv-filter-trigger">';
		if ( ! empty( $empty ) ) {
			// Empty
			$return .= '<option value="">';
			$return .= $empty;
			$return .= '</option>';
		}
		// ASC
		$return .= '<option value="asc" ' . selected( 'asc', $selected, false ) . '>';
		$return .= $asc;
		$return .= '</option>';
		// ASC
		$return .= '<option value="desc" ' . selected( 'desc', $selected, false ) . '>';
		$return .= $desc;
		$return .= '</option>';
	$return .= '</select>';
	return $return;
}
