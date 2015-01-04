<?php

add_filter( 'wpv_view_settings', 'wpv_order_by_default_settings' );
add_filter( 'wpv_view_settings', 'wpv_taxonomy_order_by_default_settings' );
add_filter( 'wpv_view_settings', 'wpv_users_order_by_default_settings' );

/**
* wpv_order_by_default_settings
*
* Sets the default sorting settings for Views listing posts
*
* @since unknown
*/

function wpv_order_by_default_settings( $view_settings ) {
	if (!isset($view_settings['orderby'])) {
		$view_settings['orderby'] = 'post_date';
	}
	if (!isset($view_settings['order'])) {
		$view_settings['order'] = 'DESC';
	}
	return $view_settings;
}

/**
* wpv_taxonomy_order_by_default_settings
*
* Sets the default sorting settings for Views listing taxonomy terms
*
* @since unknown
*/

function wpv_taxonomy_order_by_default_settings( $view_settings ) {
	if ( !isset( $view_settings['taxonomy_orderby'] ) ) {
		$view_settings['taxonomy_orderby'] = 'name';
	}
	if ( !isset( $view_settings['taxonomy_order'] ) ) {
		$view_settings['taxonomy_order'] = 'DESC';
	}
	return $view_settings;
}

/**
* wpv_users_order_by_default_settings
*
* Sets the default sorting settings for Views listing users
*
* @since unknown
*/

function wpv_users_order_by_default_settings( $view_settings ) {
	if ( !isset( $view_settings['users_orderby'] ) ) {
		$view_settings['users_orderby'] = 'user_login';
	}
	if ( !isset( $view_settings['users_order'] ) ) {
		$view_settings['users_order'] = 'DESC';
	}
	return $view_settings;
}

$orderby_meta = '';
add_filter('wpv_filter_query', 'wpv_filter_get_order_arg', 100, 2); // Make this happens after custom fields
function wpv_filter_get_order_arg($query, $view_settings) {
	global $WP_Views;
    $orderby = $view_settings['orderby'];
    if (isset($_GET['wpv_column_sort_id']) && esc_attr($_GET['wpv_column_sort_id']) != 'undefined' && esc_attr($_GET['wpv_column_sort_id']) != '' && esc_attr($_GET['wpv_view_count']) == $WP_Views->get_view_count() ) {
        $orderby = esc_attr($_GET['wpv_column_sort_id']);
    }
    
    $orderby_set = false;
    
    if (strpos($orderby, 'field-') === 0) {
        // we need to order by meta data.
        $query['meta_key'] = substr($orderby, 6);
        $orderby = 'meta_value';

        $orderby_set = true;
        
        // Fix for numeric custom field , need to user meta_value_num
        if (_wpv_is_numeric_field($view_settings['orderby']) || _wpv_is_numeric_field('field-wpcf-' . $query['meta_key'])) { // This OR will ensure that numeric fields created outside Types but under Types control can sort properly
            $orderby= 'meta_value_num';
        }
    }
    $query['orderby'] = $orderby;
    
    if (isset($_GET['wpv_order'])) {
        $query['order']= esc_attr($_GET['wpv_order'][0]);
    }
    
    // check for column sorting GET parameters.
    
    if (!$orderby_set && isset($_GET['wpv_column_sort_id']) && esc_attr($_GET['wpv_column_sort_id']) != 'undefined' && esc_attr($_GET['wpv_column_sort_id']) != '' && esc_attr($_GET['wpv_view_count']) == $WP_Views->get_view_count()) {
        $field = esc_attr($_GET['wpv_column_sort_id']);
        if (strpos($field, 'post-field') === 0) {
            $query['meta_key'] = substr($field, 11);
            $query['orderby'] = 'meta_value';
            if (_wpv_is_numeric_field('field-wpcf-' . $query['meta_key'])) {// This will ensure that numeric fields created outside Types but under Types control can sort properly
                $query['orderby'] = 'meta_value_num';
            }
        } elseif (strpos($field, 'types-field') === 0) {
            $query['meta_key'] = strtolower(substr($field, 12));
            if (function_exists('wpcf_types_get_meta_prefix')) {
                $query['meta_key'] = wpcf_types_get_meta_prefix() . $query['meta_key'];
            }
            if (_wpv_is_numeric_field('field-' . $query['meta_key'])) {
                $query['orderby'] = 'meta_value_num';
            } else {
                $query['orderby'] = 'meta_value';
            }
        } else {
            $query['orderby'] = str_replace('-', '_', $field);
        }
    }
    
    if (isset($_GET['wpv_column_sort_dir']) && esc_attr($_GET['wpv_column_sort_dir']) != 'undefined' && esc_attr($_GET['wpv_column_sort_dir']) != '' && esc_attr($_GET['wpv_view_count']) == $WP_Views->get_view_count()) {
        $query['order'] = strtoupper(esc_attr($_GET['wpv_column_sort_dir']));
    }    

    if ($query['orderby'] == 'post_link') {
        $query['orderby'] = 'post_title';
    }
    if ($query['orderby'] == 'post_body') {
        $query['orderby'] = 'post_content';
    }
    if ( $query['orderby'] == 'post_slug' ) {
        $query['orderby'] = 'name';
    }
    if ( $query['orderby'] == 'post_id' ) {
        $query['orderby'] = 'ID';
    }

    if (strpos($query['orderby'], 'post_') === 0) {
        $query['orderby'] = substr($query['orderby'], 5);
    }
    
    global $orderby_meta;
    
    $orderby_meta = array();
    // See if filtering by custom fields and sorting by custom field too
    if (isset($query['meta_key']) && isset($query['meta_query'])) {
	$thirdsorting = true; // flag to know if sorting by one of the filtering custom fields or by another custom field
	// See if the orderby is the same as a custom field filter
        foreach($query['meta_query'] as $index => $meta) {
            if (isset($meta['key']) && ($meta['key'] == $query['meta_key'])) {
                // Found it.
                // We need to add a post_orderby filter directly to sort by the same field.
                $thirdsorting = false;
                $orderby_meta['order_by'] = $query['orderby'];
                $orderby_meta['meta_key'] = $query['meta_key'];
                $orderby_meta['order'] = $query['order'];
                unset($query['meta_key']);
                add_filter('posts_where', 'wpv_post_where_meta', 10, 2);
                add_filter('posts_orderby', 'wpv_post_order_by_meta', 10, 2);
                break;
            }
        }
        if ($thirdsorting) { // if filtering by custom fields and sorting by another custom field
		$refinedquery = $query;
		unset($refinedquery['orderby']);
		unset($refinedquery['meta_key']);
		$refinedquery['posts_per_page'] = -1; // remove the limit in the main query to get all the relevant IDs
		// first query only for filtering
		$filtered_query = new WP_Query( $refinedquery );
		$filtered_ids = array();
		while ( $filtered_query->have_posts() ) :
			$filtered_query->next_post();
			$filtered_ids[] = $filtered_query->post->ID;
		endwhile;
		// remove the fields filter from the original query and add the filtered IDs
		unset($query['meta_query']);
		// we can replace the $query['post__in'] argument because it was applied on the auxiliar query before
		if ( count( $filtered_ids ) ) {
			$query['post__in'] = $filtered_ids;
		} else {
			$query['post__in'] = array(-1);
		}
        }
        
    }
    return $query;
}

function wpv_post_where_meta($where, $query) {
    global $orderby_meta;
    if (isset($orderby_meta['meta_key'])) {
        $regex = '/([^\(]*)\\.meta_key\s+=\s+\'' . $orderby_meta['meta_key'] . '\'/siU';
        if(preg_match($regex, $where, $matches)) {
            $orderby_meta['join'] = $matches[1];
        }
    }

    remove_filter('posts_where', 'wpv_post_where_meta', 10, 2);

    return $where;
}

function wpv_post_order_by_meta($orderby, $query) {
    global $orderby_meta, $wpdb;
    if (isset($orderby_meta['meta_key'])) {
        
        $order_by_value = 'meta_value';
        if ($orderby_meta['order_by'] == 'meta_value_num') {
            $order_by_value .= '+0';
        }

        // We need to set a specific order.        
        $post_ids = $wpdb->get_col("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key='{$orderby_meta['meta_key']}' ORDER BY {$order_by_value} {$orderby_meta['order']}");
        $orderby = 'CASE ' . $wpdb->prefix . 'posts.ID ';
        for ($i = 0; $i < count($post_ids); $i++) {
            $orderby .= " WHEN '" . $post_ids[$i] . "' THEN " . $i;
        }
        $orderby .= ' ELSE ' . $i;
        $orderby .= ' END, id';
        
    }
    
    remove_filter('posts_orderby', 'wpv_post_order_by_meta');
    return $orderby;
}

function _wpv_is_numeric_field($field_name) {
    $opt = get_option('wpcf-fields');
    if($opt && strpos($field_name, 'field-wpcf-') === 0) {
        $field_name = substr($field_name,11);
        if (isset($opt[$field_name]['type'])) {
            $field_type = strtolower($opt[$field_name]['type']);
            if ( $field_type == 'numeric' || $field_type == 'date') {
                return true;
            }
        }
        
    }
    
    return false;
}