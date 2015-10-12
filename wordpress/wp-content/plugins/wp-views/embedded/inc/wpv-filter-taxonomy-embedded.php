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
	if ( ! isset( $view_settings['taxonomy_type'] ) ) {
		$view_settings['taxonomy_type'] = array();
	}
	$taxonomy_defaults = array(
		'taxonomy_hide_empty' => true,
		'taxonomy_include_non_empty_decendants' => true,
		'taxonomy_pad_counts' => false,
	);
	foreach ( $taxonomy_defaults as $key => $value ) {
		if ( ! isset( $view_settings[$key] ) ) {
			$view_settings[$key] = $value;
		}
	}
	return $view_settings;
}

/**
* get_taxonomy_query
*
* Main function to get the results of a View that lists taxonomy terms
*
* @param $view_settings array
*
* @since unknown
*/

function get_taxonomy_query( $view_settings ) {
    global $WP_Views, $wpdb, $WPVDebug;

    $taxonomies = get_taxonomies( '', 'objects' );
    $view_id = $WP_Views->get_current_view();

    $WPVDebug->add_log( 'info', apply_filters( 'wpv-view-get-content-summary', '', $WP_Views->current_view, $view_settings ), 'short_query' );

    $tax_query_settings = array(
        'hide_empty' => $view_settings['taxonomy_hide_empty'],
        'hierarchical' => $view_settings['taxonomy_include_non_empty_decendants'],
        'pad_counts' => $view_settings['taxonomy_pad_counts']
    );

    $WPVDebug->add_log( 'info', "Basic query arguments\n". print_r( $tax_query_settings, true ) , 'query_args' );

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

	$WPVDebug->add_log( 'filters', "wpv_filter_taxonomy_query\n". print_r( $tax_query_settings, true ), 'filters', 'Filter arguments before the query using <strong>wpv_filter_taxonomy_query</strong>' );

    if ( isset( $taxonomies[$view_settings['taxonomy_type'][0]] ) ) {
        $items = get_terms( $taxonomies[$view_settings['taxonomy_type'][0]]->name, $tax_query_settings );
    } else {
        // taxonomy no longer exists.
        $items = array();
    }

    // get_terms doesn't sort by count when child count is included.
    // we need to do it manually.
    if ( $view_settings['taxonomy_orderby'] == 'count' ) {
        if ( $view_settings['taxonomy_order'] == 'ASC' ) {
            usort( $items, '_wpv_taxonomy_sort_asc' );
        } else {
            usort( $items, '_wpv_taxonomy_sort_dec' );
        }
    }

    // Filter by parent if required.
    // Note: We could use the 'parent' siggin in the tax_query_settings but
    // this doesn't return the correct post count.

    $parent_id = null;
    if (
		isset( $view_settings['taxonomy_parent_mode'] ) 
		&& isset( $view_settings['taxonomy_parent_mode'][0] ) 
	) {
		switch ( $view_settings['taxonomy_parent_mode'][0] ) {
			case 'current_view':
				$parent_id = $WP_Views->get_parent_view_taxonomy();
				break;
			case 'current_archive_loop':
				if ( 
					is_category() 
					|| is_tag() 
					|| is_tax() 
				) {
					$queried_object = get_queried_object();
					$parent_id = $queried_object->term_id;
				}
				break;
			case 'this_parent':
				$parent_id = $view_settings['taxonomy_parent_id'];
				if ( 
					isset( $view_settings['taxonomy_type'][0] ) 
					&& ! empty( $parent_id ) 
				) {
					// WordPress 4.2 compatibility - split terms
					$candidate_term_id_splitted = wpv_compat_get_split_term( $parent_id, $view_settings['taxonomy_type'][0] );
					if ( $candidate_term_id_splitted ) {
						$parent_id = $candidate_term_id_splitted;
					}
					// Adjust for WPML support
					$parent_id = apply_filters( 'translate_object_id', $parent_id, $view_settings['taxonomy_type'][0], true, null );
				}
				break;
		}
    }

    if ( $parent_id !== null ) {
        foreach( $items as $index => $item ) {
            if ( $item->parent != $parent_id ) {
                unset( $items[$index] );
            }
        }
        $WPVDebug->add_log( 'filters', "Filter by parent with ID {$parent_id}\n". print_r( $items, true ), 'filters', 'Filter by parent term' );
    }

    if ( isset( $view_settings['taxonomy_terms_mode'] ) ) {
		switch ( $view_settings['taxonomy_terms_mode'] ) {
			case 'CURRENT_PAGE':
				if ( isset( $taxonomies[$view_settings['taxonomy_type'][0]] ) ) {
					global $post;
					if ( isset( $post ) ) {
						$terms = get_the_terms( $post->ID, $view_settings['taxonomy_type'][0] );
					} else {
						$terms = array();
					}
					if ( ! is_array( $terms ) ) {
						$terms = array();
					}
					$filtered_terms = array();
					$terms_info = array();
					foreach ( $items as $item ) {
						foreach( $terms as $term ) {
							if ( $item->term_id == $term->term_id ) {
								// only add the terms in the 'taxonomy_terms' array.
								$filtered_terms[] = $item;
								$terms_info[] = $term->name . ' (id=' . $term->term_id . ')';
							}
						}
					}
					$items = $filtered_terms;
					$WPVDebug->add_log( 'filters', "Filter by terms from the current page " . implode( ', ' , $terms_info ) . "\n" . print_r( $items, true ), 'filters', 'Filter by terms from the current page' );
				} else {
					$items = array();
					$WPVDebug->add_log( 'filters', "Filter by terms from the current page but for a taxonomy that no longer exists \n" . print_r( $items, true ), 'filters', 'Filter by terms from the current page' );
				}
				break;
			case 'THESE':
				if (
					isset( $view_settings['taxonomy_terms'] )
					&& sizeof( $view_settings['taxonomy_terms'] ) 
				) {
					$filtered_terms = array();
					if ( 
						isset( $view_settings['taxonomy_type'][0] ) 
						&& ! empty( $view_settings['taxonomy_terms'] ) 
					) {
						$adjusted_term_ids = array();
						foreach ( $view_settings['taxonomy_terms'] as $candidate_term_id ) {
							// WordPress 4.2 compatibility - split terms
							$candidate_term_id_splitted = wpv_compat_get_split_term( $candidate_term_id, $view_settings['taxonomy_type'][0] );
							if ( $candidate_term_id_splitted ) {
								$candidate_term_id = $candidate_term_id_splitted;
							}
							// WPML support
							$candidate_term_id = apply_filters( 'translate_object_id', $candidate_term_id, $view_settings['taxonomy_type'][0], true, null );
							$adjusted_term_ids[] = $candidate_term_id;
						}
						$view_settings['taxonomy_terms'] = $adjusted_term_ids;
					}
					foreach ( $items as $item ) {
						if ( in_array( $item->term_id, $view_settings['taxonomy_terms'] ) ) {
							// only add the terms in the 'taxonomy_terms' array.
							$filtered_terms[] = $item;
						}
					}
					$items = $filtered_terms;
					$WPVDebug->add_log( 'filters', "Filter by specific terms " . implode( ',  ' , $view_settings['taxonomy_terms'] ) . "\n". print_r( $items, true ), 'filters', 'Filter by specific terms' );
				}
				break;
			case 'framework':
				global $WP_Views_fapi;
				if ( $WP_Views_fapi->framework_valid ) {
					if (
						isset( $view_settings['taxonomy_terms_framework'] ) 
						&& '' != $view_settings['taxonomy_terms_framework']
					) {
						$taxonomy_terms_framework = $view_settings['taxonomy_terms_framework'];
						$taxonomy_terms_candidates = $WP_Views_fapi->get_framework_value( $taxonomy_terms_framework, array() );
						if ( ! is_array( $taxonomy_terms_candidates ) ) {
							$taxonomy_terms_candidates = explode( ',', $taxonomy_terms_candidates );
						}
						$taxonomy_terms_candidates = array_map( 'esc_attr', $taxonomy_terms_candidates );
						$taxonomy_terms_candidates = array_map( 'trim', $taxonomy_terms_candidates );
						// is_numeric does sanitization
						$taxonomy_terms_candidates = array_filter( $taxonomy_terms_candidates, 'is_numeric' );
						if ( count( $taxonomy_terms_candidates ) ) {
							$filtered_terms = array();
							if ( isset( $view_settings['taxonomy_type'][0] ) ) {
								$adjusted_term_ids = array();
								foreach ( $taxonomy_terms_candidates as $candidate_term_id ) {
									// WordPress 4.2 compatibility - split terms
									$candidate_term_id_splitted = wpv_compat_get_split_term( $candidate_term_id, $view_settings['taxonomy_type'][0] );
									if ( $candidate_term_id_splitted ) {
										$candidate_term_id = $candidate_term_id_splitted;
									}
									// WPML support
									$candidate_term_id = apply_filters( 'translate_object_id', $candidate_term_id, $view_settings['taxonomy_type'][0], true, null );
									$adjusted_term_ids[] = $candidate_term_id;
								}
								$taxonomy_terms_candidates = $adjusted_term_ids;
							}
							foreach ( $items as $item ) {
								if ( in_array( $item->term_id, $taxonomy_terms_candidates ) ) {
									// only add the terms in the 'taxonomy_terms' array.
									$filtered_terms[] = $item;
								}
							}
							$items = $filtered_terms;
						}
					}
				}
				break;
		}
    }

	if ( 
		isset( $wpdb->queries ) 
		&& ! empty( $wpdb->queries ) 
	) {
		$WPVDebug->add_log( 'mysql_query', $wpdb->queries , 'taxonomy' );
	}

	$WPVDebug->add_log( 'info', print_r( $items, true ), 'query_results', '', true );

    $items = array_values( $items );

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

    $WPVDebug->add_log( 'filters', "wpv_filter_taxonomy_post_query\n" . print_r( $items, true ), 'filters', 'Filter the returned query using <strong>wpv_filter_taxonomy_post_query</strong>' );

    return $items;
}

/**
* _wpv_taxonomy_sort_asc
*
* Sort taxonomy terms by post count, ASC
*
* @since unknown
*/

function _wpv_taxonomy_sort_asc( $a, $b ) {
    if ( $a->count == $b->count ) {
        return 0;
    }
    return ( $a->count < $b->count ) ? -1 : 1;
}

/**
* _wpv_taxonomy_sort_dec
*
* Sort taxonomy terms by post count, DESC
*
* @since unknown
*/

function _wpv_taxonomy_sort_dec( $a, $b ) {
    if ( $a->count == $b->count ) {
        return 0;
    }
    return ( $a->count < $b->count ) ? 1 : -1;
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

/**
* wpv_filter_tax_requires_current_page
*
* Whether the current View requires the current page for any filter
*
* @param $state (boolean) the state of this need until this filter is applied
* @param $view_settings
*
* @return $state (boolean)
*
* @since 1.9.0
*/

add_filter( 'wpv_filter_requires_current_page', 'wpv_filter_tax_requires_current_page', 10, 2 );

function wpv_filter_tax_requires_current_page( $state, $view_settings ) {
	if ( $state ) {
		return $state; // Already set
	}
    if (
		isset( $view_settings['taxonomy_terms_mode'] ) 
		&& $view_settings['taxonomy_terms_mode'] == 'CURRENT_PAGE'
	) {
        $state = true;
    }
	return $state;

}

/**
* wpv_filter_tax_requires_framework_values
*
* Whether the current View requires framework valus for the filter by specific terms
*
* @param $state (boolean) the state of this need until this filter is applied
* @param $view_settings
*
* @return $state (boolean)
*
* @since 1.10
*/

add_filter( 'wpv_filter_requires_framework_values', 'wpv_filter_tax_requires_framework_values', 10, 2 );

function wpv_filter_tax_requires_framework_values( $state, $view_settings ) {
	if ( $state ) {
		return $state; // Already set
	}
    if (
		isset( $view_settings['taxonomy_terms_mode'] ) 
		&& $view_settings['taxonomy_terms_mode'] == 'framework'
	) {
        $state = true;
    }
	return $state;

}

/**
* wpv_filter_tax_requires_parent_term
*
* Whether the current View is nested and requires the user set by the parent View for any filter
*
* @param $state (boolean) the state of this need until this filter is applied
* @param $view_settings
*
* @return $state (boolean)
*
* @since 1.9.0
*/

add_filter( 'wpv_filter_requires_parent_term', 'wpv_filter_tax_requires_parent_term', 10, 2 );

function wpv_filter_tax_requires_parent_term( $state, $view_settings ) {
	if ( $state ) {
		return $state;
	}
	if (
		isset( $view_settings['taxonomy_parent_mode'] ) 
		&& isset( $view_settings['taxonomy_parent_mode'][0] ) 
		&& $view_settings['taxonomy_parent_mode'][0] == 'current_view'
	) {
        $state = true;
    }
	return $state;
}

/**
* wpv_filter_tax_requires_current_archive
*
* Whether the current View requires the current archive for the taxonomy parent filter
*
* @param $state (boolean) the state of this need until this filter is applied
* @param $view_settings
*
* @return $state (boolean)
*
* @since 1.10
*/

add_filter( 'wpv_filter_requires_current_archive', 'wpv_filter_tax_requires_current_archive', 10, 2 );

function wpv_filter_tax_requires_current_archive( $state, $view_settings ) {
	if ( $state ) {
		return $state; // Already set
	}
    if (
		isset( $view_settings['taxonomy_parent_mode'] ) 
		&& isset( $view_settings['taxonomy_parent_mode'][0] ) 
		&& $view_settings['taxonomy_parent_mode'][0] == 'current_archive_loop'
	) {
        $state = true;
    }
	return $state;

}