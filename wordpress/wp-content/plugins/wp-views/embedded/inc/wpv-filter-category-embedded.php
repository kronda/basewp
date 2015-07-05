<?php

/**
 * Modify the query to include filtering by category.
 *
 */

add_filter( 'wpv_filter_query', 'wpv_filter_post_category', 10, 2 );

function wpv_filter_post_category( $query, $view_settings ) {

	global $WP_Views;
	if ( ! isset( $view_settings['taxonomy_relationship'] ) ) {
		$view_settings['taxonomy_relationship'] = 'AND';
	}
	$taxonomies = get_taxonomies( '', 'objects' );
	foreach ( $taxonomies as $category_slug => $category ) {
		$relationship_name = ( $category->name == 'category' ) ? 'tax_category_relationship' : 'tax_' . $category->name . '_relationship';
		if ( isset( $view_settings[$relationship_name] ) ) {
			
			$save_name = ( $category->name == 'category' ) ? 'post_category' : 'tax_input_' . $category->name;
			
			$attribute_operator = ( isset( $view_settings['taxonomy-' . $category->name . '-attribute-operator'] ) ) ? $view_settings['taxonomy-' . $category->name . '-attribute-operator'] : 'IN';
			if ( $attribute_operator == 'IN' ) {
				$include_child = true;	
			} else {
				$include_child = false;	
			}
			/*
			 * Filter: wpv_filter_tax_filter_include_children
			 * 
			 * @param: $include_child - current status
			 * @paran: $category->name - Category nicename
			 * @param: $WP_Views->current_view - View ID
			 * 
			*/
			//$include_child = apply_filters( 'wpv_filter_tax_filter_include_children', $include_child, $category->name, $WP_Views->current_view );
			
			switch ( $view_settings['tax_' . $category->name . '_relationship'] ) {
				case 'FROM PAGE':
					$current_page = $WP_Views->get_current_page();
					if ( $current_page ) {
						$terms = array();
						$term_obj = get_the_terms( $current_page->ID, $category->name );
						if ( 
							$term_obj 
							&& ! is_wp_error( $term_obj ) 
						) {
							$terms = array_values( wp_list_pluck( $term_obj, 'term_id' ) );
						}
						if ( count( $terms ) ) {
							$include_child = apply_filters( 'wpv_filter_tax_filter_include_children', $include_child, $category->name, $WP_Views->current_view );
							$query['tax_query'][] = array(
								'taxonomy' => $category->name,
								'field' => 'id',
								'terms' => _wpv_get_adjusted_terms($terms, $category->name),
								'operator' => "IN",
								"include_children" => $include_child
							);
						} else { // if the current page has no term in the given taxonomy, return nothing
							$include_child = true;
							$include_child = apply_filters( 'wpv_filter_tax_filter_include_children', $include_child, $category->name, $WP_Views->current_view );
							$query['tax_query'][] = array(
								'taxonomy' => $category->name,
								'field' => 'id',
								'terms' => 0,
								'operator' => "IN",
								"include_children" => $include_child
							);
						}
					}
					break;
				case 'FROM ATTRIBUTE':
					$attribute = $view_settings['taxonomy-' . $category->name . '-attribute-url'];
					if ( isset( $view_settings['taxonomy-' . $category->name . '-attribute-url-format'] ) ) {
						$attribute_format = $view_settings['taxonomy-' . $category->name . '-attribute-url-format'][0];
					} else {
						$attribute_format = 'name';
					}
					$view_attrs = $WP_Views->get_view_shortcodes_attributes();
					if ( isset( $view_attrs[$attribute] ) ) {
						$terms = explode( ',', $view_attrs[$attribute] );
						$term_ids = array();
						foreach ( $terms as $t ) {
							// get_term_by does sanitization
							$term = get_term_by( $attribute_format, trim( $t ), $category->name );
							if ( $term ) {
								array_push( $term_ids, $term->term_id );
							}
						}
						if ( count( $term_ids ) > 0 ) {
							$include_child = apply_filters( 'wpv_filter_tax_filter_include_children', $include_child, $category->name, $WP_Views->current_view );
							$query['tax_query'][] = array(
								'taxonomy' => $category->name,
								'field' => 'id',
								'terms' => _wpv_get_adjusted_terms( $term_ids, $category->name ),
								'operator' => $attribute_operator,
								"include_children" => $include_child
							);
						} else if ( count( $terms ) > 0 ) { // if the shortcode attribute exists and is not empty, and no term matches the value, return nothing
							$include_child = true;
							$include_child = apply_filters( 'wpv_filter_tax_filter_include_children', $include_child, $category->name, $WP_Views->current_view );
							$query['tax_query'][] = array(
								'taxonomy' => $category->name,
								'field' => 'id',
								'terms' => 0,
								'operator' => "IN",
								"include_children" => $include_child
							);
						}
					}
					break;
				case 'FROM URL':
					$url_parameter = $view_settings['taxonomy-' . $category->name . '-attribute-url'];
					if ( isset( $view_settings['taxonomy-' . $category->name . '-attribute-url-format'] ) ) {
						$url_format = $view_settings['taxonomy-' . $category->name . '-attribute-url-format'][0];
					} else {
						$url_format = 'name';
					}
					if ( isset( $_GET[$url_parameter] ) ) {
						if ( is_array( $_GET[$url_parameter] ) ) {
							$terms = $_GET[$url_parameter];
						} else {
							$terms = explode( ',', $_GET[$url_parameter] );
						}
						$term_ids = array();
						foreach ( $terms as $t ) {
							// get_term_by does sanitization
							$term = get_term_by( $url_format, trim( $t ), $category->name );
							if ( $term ) {
								array_push( $term_ids, $term->term_id );
							}
						}
						if ( count( $term_ids ) > 0 ) {
							$include_child = apply_filters( 'wpv_filter_tax_filter_include_children', $include_child, $category->name, $WP_Views->current_view );
							$query['tax_query'][] = array(
								'taxonomy' => $category->name,
								'field' => 'id',
								'terms' => _wpv_get_adjusted_terms( $term_ids, $category->name ),
								'operator' => $attribute_operator,
								"include_children" => $include_child
							);
						} else if ( ! empty( $_GET[$url_parameter] ) ) {
							$include_child = true;
							$include_child = apply_filters( 'wpv_filter_tax_filter_include_children', $include_child, $category->name, $WP_Views->current_view );
							$query['tax_query'][] = array(
								'taxonomy' => $category->name,
								'field' => 'id',
								'terms' => 0,
								'operator' => "IN",
								"include_children" => $include_child
							);
						}
					}
					break;
				case 'FROM PARENT VIEW':
					$parent_term_id = $WP_Views->get_parent_view_taxonomy();
					if ( $parent_term_id ) {
						$include_child = true;
						$include_child = apply_filters( 'wpv_filter_tax_filter_include_children', $include_child, $category->name, $WP_Views->current_view );
						$query['tax_query'][] = array(
							'taxonomy' => $category->name,
							'field' => 'id',
							'terms' => _wpv_get_adjusted_terms( array( $parent_term_id ), $category->name ),
							'operator' => "IN",
							"include_children" => $include_child
						);
					} else {
						$include_child = true;
						$include_child = apply_filters( 'wpv_filter_tax_filter_include_children', $include_child, $category->name, $WP_Views->current_view );
						$query['tax_query'][] = array(
							'taxonomy' => $category->name,
							'field' => 'id',
							'terms' => 0,
							'operator' => "IN",
							"include_children" => $include_child
						);
					}
					break;
				case 'IN':
				case 'NOT IN':
				case 'AND':
					if ( $view_settings['tax_' . $category->name . '_relationship'] == 'IN' ) {
						$include_child = true;	
					} else {
						$include_child = false;	
					}
					$include_child = apply_filters( 'wpv_filter_tax_filter_include_children', $include_child, $category->name, $WP_Views->current_view );
					if ( isset( $view_settings[$save_name] ) ) {
						$term_ids = $view_settings[$save_name];
						$query['tax_query'][] = array(
							'taxonomy' => $category->name,
							'field' => 'id',
							'terms' => _wpv_get_adjusted_terms( $term_ids, $category->name ),
							'operator' => $view_settings['tax_' . $category->name . '_relationship'],
							"include_children" => $include_child
						);
					}
					break;
				case 'framework':
					global $WP_Views_fapi;
					if ( 
						$WP_Views_fapi->framework_valid
						&& isset( $view_settings['taxonomy-' . $category->name . '-framework'] )
						&& '' != $view_settings['taxonomy-' . $category->name . '-framework']
					) {
						$include_child = true;
						$include_child = apply_filters( 'wpv_filter_tax_filter_include_children', $include_child, $category->name, $WP_Views->current_view );
						$framework_key = $view_settings['taxonomy-' . $category->name . '-framework'];
						$taxonomy_terms_candidates = $WP_Views_fapi->get_framework_value( $framework_key, array() );
						if ( ! is_array( $taxonomy_terms_candidates ) ) {
							$taxonomy_terms_candidates = explode( ',', $taxonomy_terms_candidates );
						}
						$taxonomy_terms_candidates = array_map( 'esc_attr', $taxonomy_terms_candidates );
						$taxonomy_terms_candidates = array_map( 'trim', $taxonomy_terms_candidates );
						// is_numeric does sanitization
						$taxonomy_terms_candidates = array_filter( $taxonomy_terms_candidates, 'is_numeric' );
						if ( count( $taxonomy_terms_candidates ) ) {
							$query['tax_query'][] = array(
								'taxonomy' => $category->name,
								'field' => 'id',
								'terms' => _wpv_get_adjusted_terms( $taxonomy_terms_candidates, $category->name ),
								'operator' => 'IN',
								"include_children" => $include_child
							);
						}
					}
					break;
				
			}
		}
    }
	
	if ( isset( $query['tax_query'] ) ) {
		$query['tax_query']['relation'] = $view_settings['taxonomy_relationship'];
	}
    
    return $query;
}

function wpv_get_taxonomy_view_params($view_settings) {
	$results = array();
	
	$taxonomies = get_taxonomies('', 'objects');
	foreach ($taxonomies as $category_slug => $category) {
		$relationship_name = ( $category->name == 'category' ) ? 'tax_category_relationship' : 'tax_' . $category->name . '_relationship';
		
		if (isset($view_settings[$relationship_name])) {
			
			$save_name = ( $category->name == 'category' ) ? 'post_category' : 'tax_input_' . $category->name;			
			
			if ($view_settings['tax_' . $category->name . '_relationship'] == "FROM ATTRIBUTE") {
				$attribute = $view_settings['taxonomy-' . $category->name . '-attribute-url'];
				$results[] = $attribute;
			}
		}
    }
    
	return $results;
}


function _wpv_get_adjusted_terms( $term_ids, $category_name ) {
	if ( ! empty( $term_ids ) ) {
		$adjusted_term_ids = array();
		foreach ( $term_ids as $candidate_term_id ) {
			// WordPress 4.2 compatibility - split terms
			$candidate_term_id_splitted = wpv_compat_get_split_term( $candidate_term_id, $category_name );
			if ( $candidate_term_id_splitted ) {
				$candidate_term_id = $candidate_term_id_splitted;
			}
			// WPML support
			$candidate_term_id = apply_filters( 'translate_object_id', $candidate_term_id, $category_name, true, null );
			$adjusted_term_ids[] = $candidate_term_id;
		}
		$term_ids = $adjusted_term_ids;
	}
	return $term_ids;	
}

add_filter('wpv_filter_requires_current_page', 'wpv_filter_cat_requires_current_page', 10, 2);
function wpv_filter_cat_requires_current_page($state, $view_settings) {
	if ($state) {
		return $state; // Already set
	}

	$taxonomies = get_taxonomies('', 'objects');
	foreach ($taxonomies as $category_slug => $category) {
		$relationship_name = ( $category->name == 'category' ) ? 'tax_category_relationship' : 'tax_' . $category->name . '_relationship';
		
		if (isset($view_settings[$relationship_name])) {
			if ($view_settings['tax_' . $category->name . '_relationship'] == "FROM PAGE") {
				$state = true;
				break;
			}
		}
	}
	
	return $state;
	
}
