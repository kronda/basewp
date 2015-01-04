<?php

// DEPRECATED global used only in the old wpv_taxonomy_defaults_save function
// TO DELETE

global $taxonomy_checkboxes_defaults;
$taxonomy_checkboxes_defaults = array(
    'taxonomy_hide_empty' => true,
    'taxonomy_include_non_empty_decendants' => true,
    'taxonomy_pad_counts' => false,
);

/**
* wpv_taxonomy_default_settings
*
* Sets the default settings for Views listing taxonomies
*
* @since unknown
*/

add_filter( 'wpv_view_settings', 'wpv_taxonomy_default_settings' );

function wpv_taxonomy_default_settings( $view_settings ) {
	if ( !isset( $view_settings['taxonomy_type'] ) ) {
		$view_settings['taxonomy_type'] = array();
	}
	$taxonomy_defaults = array(
		'taxonomy_hide_empty' => true,
		'taxonomy_include_non_empty_decendants' => true,
		'taxonomy_pad_counts' => false,
	);
	foreach ( $taxonomy_defaults as $key => $value ) {
		if ( !isset( $view_settings[$key] ) ) {
			$view_settings[$key] = $value;
		}
	}
	return $view_settings;
}

function get_taxonomy_query($view_settings) {
    global $WP_Views, $wpdb, $WPVDebug;

    $taxonomies = get_taxonomies('', 'objects');
    $view_id = $WP_Views->get_current_view();

    $WPVDebug->add_log( 'info' , apply_filters('wpv-view-get-content-summary', '', $WP_Views->current_view, $view_settings) , 'short_query' );

    $tax_query_settings = array(
        'hide_empty' => $view_settings['taxonomy_hide_empty'],
        'hierarchical' => $view_settings['taxonomy_include_non_empty_decendants'],
        'pad_counts' => $view_settings['taxonomy_pad_counts'],
        'orderby' => $view_settings['taxonomy_orderby'],
        'order' => $view_settings['taxonomy_order']
    );

    $WPVDebug->add_log( 'info' , "Basic query arguments\n". print_r($tax_query_settings, true) , 'query_args' );

    /**
	* Filter wpv_filter_taxonomy_query
	*
	* This is where all the filters coming from the View settings to modify the query are (or should be) hooked
	*
	* @param $tax_query_settings the relevant elements of the View settings in an array to be used as arguments in a get_terms() call
	* @param $view_settings the View settings
	* @param $view_id the ID of the View being displayed
	*
	* @return $tax_query_settings
	*
	* @since unknown
	*/

    $tax_query_settings = apply_filters( 'wpv_filter_taxonomy_query', $tax_query_settings, $view_settings, $view_id );

	$WPVDebug->add_log( 'filters' , "wpv_filter_taxonomy_query\n". print_r($tax_query_settings, true) , 'filters', 'Filter arguments before the query using <strong>wpv_filter_taxonomy_query</strong>' );

    if (isset($_GET['wpv_column_sort_id']) && esc_attr($_GET['wpv_column_sort_id']) != '' && esc_attr($_GET['wpv_view_count']) == $WP_Views->get_view_count()) {
        $field = esc_attr($_GET['wpv_column_sort_id']);
        if ($field == 'taxonomy-link') {
            $tax_query_settings['orderby'] = 'name';
        }
        if ($field == 'taxonomy-title') {
            $tax_query_settings['orderby'] = 'name';
        }
        if ($field == 'taxonomy-post_count') {
            $tax_query_settings['orderby'] = 'count';
        }

    }

    if (isset($_GET['wpv_column_sort_dir']) && esc_attr($_GET['wpv_column_sort_dir']) != '' && esc_attr($_GET['wpv_view_count']) == $WP_Views->get_view_count()) {
        $tax_query_settings['order'] = strtoupper(esc_attr($_GET['wpv_column_sort_dir']));

    }

    if (isset($taxonomies[$view_settings['taxonomy_type'][0]])) {
        $items = get_terms($taxonomies[$view_settings['taxonomy_type'][0]]->name, $tax_query_settings);
    } else {
        // taxonomy no longer exists.
        $items = array();
    }

    // get_terms doesn't sort by count when child count is included.
    // we need to do it manually.
    if ($view_settings['taxonomy_orderby'] == 'count') {
        if ($view_settings['taxonomy_order'] == 'ASC') {
            usort($items, '_wpv_taxonomy_sort_asc');
        } else {
            usort($items, '_wpv_taxonomy_sort_dec');
        }
    }

    // Filter by parent if required.
    // Note: We could use the 'parent' siggin in the tax_query_settings but
    // this doesn't return the correct post count.

    $parent_id = null;
    if (isset( $view_settings['taxonomy_parent_mode'] ) && isset ( $view_settings['taxonomy_parent_mode'][0] ) ) {
		switch($view_settings['taxonomy_parent_mode'][0]) {
			case 'current_view':
			$parent_id = $WP_Views->get_parent_view_taxonomy();
			break;

			case 'this_parent':
			$parent_id = $view_settings['taxonomy_parent_id'];

			if ( function_exists('icl_object_id') && isset( $view_settings['taxonomy_type'][0] ) && !empty( $parent_id ) ) {
				// Adjust for WPML support
				$parent_id = icl_object_id( $parent_id, $view_settings['taxonomy_type'][0], true );
			}
			break;
		}
    }

    if ($parent_id !== null) {
        foreach($items as $index => $item) {
            if ($item->parent != $parent_id) {
                unset($items[$index]);
            }
        }
        $WPVDebug->add_log( 'filters' , "Filter by parent with ID {$parent_id}\n". print_r($items, true) , 'filters', 'Filter by parent term' );
    }

    if ( isset( $view_settings['taxonomy_terms_mode'] ) ) {
		if ($view_settings['taxonomy_terms_mode'] == 'THESE') {
			if (sizeof($view_settings['taxonomy_terms'])) {
				// filter by indiviual taxonomy terms.

				$filtered_terms = array();

				if ( function_exists('icl_object_id') && isset( $view_settings['taxonomy_type'][0] ) && !empty( $view_settings['taxonomy_terms'] ) ) {
					// Adjust for WPML support
					$trans_term_ids = array();
					foreach ( $view_settings['taxonomy_terms'] as $untrans_term_id ) {
						$trans_term_ids[] = icl_object_id( $untrans_term_id, $view_settings['taxonomy_type'][0], true );
					}
					$view_settings['taxonomy_terms'] = $trans_term_ids;
				}

				foreach($items as $item) {
					if (in_array($item->term_id, $view_settings['taxonomy_terms'])) {
					// only add the terms in the 'taxonomy_terms' array.
					$filtered_terms[] = $item;
					}
				}

				$items = $filtered_terms;

				$WPVDebug->add_log( 'filters' , "Filter by specific terms " . implode( ',  ' , $view_settings['taxonomy_terms'] ) . "\n". print_r($items, true) , 'filters', 'Filter by specific terms' );
			}
		} else {
			// get the terms from the current page.

			global $post;
			$terms = get_the_terms($post->ID, $view_settings['taxonomy_type'][0]);
			
			if ( !is_array( $terms ) ) {
				$terms = array();
			}

			$filtered_terms = array();
			$terms_info = array();

			foreach($items as $item) {
				foreach($terms as $term) {
					if ($item->term_id == $term->term_id) {
					// only add the terms in the 'taxonomy_terms' array.
					$filtered_terms[] = $item;
					$terms_info[] = $term->name . ' (id=' . $term->term_id . ')';
					}
				}
			}

			$items = $filtered_terms;

			$WPVDebug->add_log( 'filters' , "Filter by terms from the current page " . implode( ', ' , $terms_info ) . "\n" . print_r($items, true) , 'filters', 'Filter by terms from the current page' );

		}

    }

	if ( isset( $wpdb->queries ) && !empty( $wpdb->queries ) ) {
		$WPVDebug->add_log( 'mysql_query' , $wpdb->queries , 'taxonomy' );
	}

	$WPVDebug->add_log( 'info' , print_r($items, true) , 'query_results' , '' , true );

    $items = array_values($items);

    /**
	* Filter wpv_filter_taxonomy_post_query
	*
	* Filter applied to the results of the get_terms() call
	*
	* @param $items array of terms returned by the get_terms() call
	* @param $tax_query_settings the relevant elements of the View settings in an array to be used as arguments in a get_terms() call
	* @param $view_settings the View settings
	* @param $view_id the ID of the View being displayed
	*
	* @return $items
	*
	* @since unknown
	*/

    $items = apply_filters( 'wpv_filter_taxonomy_post_query', $items, $tax_query_settings, $view_settings, $view_id );

    $WPVDebug->add_log( 'filters' , "wpv_filter_taxonomy_post_query\n" . print_r($items, true) , 'filters', 'Filter the returned query using <strong>wpv_filter_taxonomy_post_query</strong>' );

    return $items;

}

function _wpv_taxonomy_sort_asc($a, $b) {
    if ($a->count == $b->count) {
        return 0;
    }

    return ($a->count < $b->count) ? -1 : 1;
}

function _wpv_taxonomy_sort_dec($a, $b) {
    if ($a->count == $b->count) {
        return 0;
    }

    return ($a->count < $b->count) ? 1 : -1;
}


/**
 * Views-Shortcode: wpv-no-taxonomy-found
 *
 * Description: The [wpv-no-taxonomy-found] shortcode will display the text inside
 * the shortcode if there are no taxonomys found by the Views query.
 *
 * Parameters:
 * This takes no parameters.
 *
 * Example usage:
 * [wpv-no-taxonomy-found]No taxonomy found[/wpv-no-taxonomy-found]
 *
 * Link:
 *
 * Note:
 * This shortcode is deprecated in favour of the new [wpv-no-items-found]
 *
 */

add_shortcode('wpv-no-taxonomy-found', 'wpv_no_taxonomy_found');
function wpv_no_taxonomy_found($atts, $value){
    extract(
        shortcode_atts( array(), $atts )
    );

    global $WP_Views;

    if ($WP_Views->get_taxonomy_found_count() == 0) {
        // display the message when no taxonomys are found.
        return wpv_do_shortcode($value);
    } else {
        return '';
    }

}

add_filter('wpv_filter_requires_current_page', 'wpv_filter_tax_requires_current_page', 10, 2);
function wpv_filter_tax_requires_current_page($state, $view_settings) {
	if ($state) {
		return $state; // Already set
	}
    if (isset($view_settings['taxonomy_terms_mode']) && $view_settings['taxonomy_terms_mode'] == 'CURRENT_PAGE') {
        $state = true;
    }

	return $state;

}
