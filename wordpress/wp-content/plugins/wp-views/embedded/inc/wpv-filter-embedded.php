<?php


/* Handle the short codes for creating a user query form
  
  [wpv-filter-start]
  [wpv-filter-end]
  [wpv-filter-submit]
  
*/

/**
 * Views-Shortcode: wpv-filter-start
 *
 * Description: The [wpv-filter-start] shortcode specifies the start point
 * for any controls that the views filter generates. Example controls are
 * pagination controls and search boxes. This shortcode is usually added
 * automatically to the Views Meta HTML.
 *
 * Parameters:
 * This takes no parameters.
 *
 * Example usage:
 *
 * Link:
 *
 * Note:
 *
 */
add_shortcode('wpv-filter-start', 'wpv_filter_shortcode_start');
function wpv_filter_shortcode_start($atts){
	
	global $WP_Views;
	$view_id = $WP_Views->get_current_view();
	$view_settings = $WP_Views->get_view_settings();
	$view_layout_settings = $WP_Views->get_view_layout_settings();
	$view_attrs = $WP_Views->get_view_shortcodes_attributes();
	$is_required = false;
	$dps_enabled = false;
	$counters_enabled = false;
	$out = '';
    if ( _wpv_filter_is_form_required() ) {
		
		$is_required = true;
		
        extract(
            shortcode_atts( array(), $atts )
        );
        
        $hide = '';
        if (
			( isset( $atts['hide'] ) && $atts['hide'] == 'true' )
			|| ( isset( $view_attrs['view_display'] ) && $view_attrs['view_display'] == 'layout' )
		) {
            $hide = ' style="display:none;"';
        }
        
        $form_class = array( 'js-wpv-form-full' );
        // Dependant stuf
        if ( !isset( $view_settings['dps'] ) || !is_array( $view_settings['dps'] ) ) {
			$view_settings['dps'] = array();
		}
		if ( isset( $view_settings['dps']['enable_dependency'] ) && $view_settings['dps']['enable_dependency'] == 'enable' ) {
			$dps_enabled = true;
			$controls_per_kind = wpv_count_filter_controls( $view_settings );
			$controls_count = 0;
			$no_intersection = array();
			if ( !isset( $controls_per_kind['error'] ) ) {
				$controls_count = $controls_per_kind['cf'] + $controls_per_kind['tax'] + $controls_per_kind['pr'] + $controls_per_kind['search'];
				if ( $controls_per_kind['cf'] > 1 && ( !isset( $view_settings['custom_fields_relationship'] ) || $view_settings['custom_fields_relationship'] != 'AND' ) ) {
					$no_intersection[] = __( 'custom field', 'wpv-views' );
				}
				if ( $controls_per_kind['tax'] > 1 && ( !isset( $view_settings['taxonomy_relationship'] ) || $view_settings['taxonomy_relationship'] != 'AND' ) ) {
					$no_intersection[] = __( 'taxonomy', 'wpv-views' );
				}
			} else {
				$dps_enabled = false;
			}
			if ( $controls_count > 0 ) {
				if ( count( $no_intersection ) > 0 ) {
					$dps_enabled = false;
				}
			} else {
				$dps_enabled = false;
			}
		}
		if ( !isset( $view_settings['filter_meta_html'] ) ) {
			$view_settings['filter_meta_html'] = '';
		}
		if ( strpos( $view_settings['filter_meta_html'], '%%COUNT%%' ) !== false ) {
			$counters_enabled = true;
		}
		if ( $dps_enabled || $counters_enabled ) {
			// TODO review this, makes little sense
			if ( $dps_enabled ) {
				$form_class[] = 'js-wpv-dps-enabled';
			}
		} else {
			// Set the force value
			$WP_Views->set_force_disable_dependant_parametric_search( true );
		}
        if ( !isset( $view_settings['dps']['ajax_results'] ) ) {
			$view_settings['dps']['ajax_results'] = 'disable';
		}
		if ( !isset( $view_settings['dps']['ajax_results_submit'] ) ) {
			$view_settings['dps']['ajax_results_submit'] = 'reload';
		}
		$ajax = $view_settings['dps']['ajax_results'];
		$ajax_submit = $view_settings['dps']['ajax_results_submit'];
		if ( $ajax == 'enable' ) {
			$form_class[] = 'js-wpv-ajax-results-enabled';
		} else if ( $ajax == 'disable' && $ajax_submit == 'ajaxed' ) {
			$form_class[] = 'js-wpv-ajax-results-submit-enabled';
		}
		

        $page = 1;
		
        $effect = 'fade';
		$ajax_pre_before = '';
		if ( isset( $view_settings['dps']['ajax_results_pre_before'] ) ) {
			$ajax_pre_before = esc_attr( $view_settings['dps']['ajax_results_pre_before'] );
		}
        $ajax_before = '';
        if ( isset( $view_settings['dps']['ajax_results_before'] ) ) {
			$ajax_before = esc_attr( $view_settings['dps']['ajax_results_before'] );
        }
        $ajax_after = '';
        if ( isset( $view_settings['dps']['ajax_results_after'] ) ) {
			$ajax_after = esc_attr( $view_settings['dps']['ajax_results_after'] );
        }
        
        $url = get_permalink();
        $out = '<form' . $hide . ' autocomplete="off" name="wpv-filter-' . $WP_Views->get_view_count() . '" action="' . $url . '" method="get" class="wpv-filter-form js-wpv-filter-form js-wpv-filter-form-' . $WP_Views->get_view_count() . ' ' . implode( ' ', $form_class ) . '" data-viewnumber="' . $WP_Views->get_view_count() . '" data-viewid="' . $view_id . '">';
        
        $out .= '<input type="hidden" class="js-wpv-dps-filter-data js-wpv-filter-data-for-this-form" data-action="' . $url . '" data-page="' . $page . '" data-ajax="' . $ajax . '" data-effect="' . $effect . '" data-maxpages="' . $WP_Views->get_max_pages() . '" data-ajaxprebefore="' . $ajax_pre_before . '" data-ajaxbefore="' . $ajax_before . '" data-ajaxafter="' . $ajax_after . '" />';
        
		// Set a hidden input for the View attributes, so we can pass them if needed
		if ( isset( $view_attrs['name'] ) ) {
			unset( $view_attrs['name'] );
		}
		if ( !empty( $view_attrs ) ) {
			$att_data = '';
			foreach ( $view_attrs as $att_key => $att_val ) {
				$att_data .= ' data-' . $att_key . '="' . esc_attr( $att_val ) . '"';
			}
			$out .= '<input type="hidden" class="js-wpv-view-attributes"' . $att_data . ' />';
		}
		
        // add hidden inputs for any url parameters.
        // We need these for when the form is submitted.
        $url_query = parse_url($url, PHP_URL_QUERY);
        if ($url_query != '') {
            $query_parts = explode('&', $url_query);
            foreach($query_parts as $param) {
                $item = explode('=', $param);
                if (strpos($item[0], 'wpv_') !== 0) {
                    $out .= '<input id="wpv_param_' . $item[0] . '" type="hidden" name="' . $item[0] . '" value="' . $item[1] . '" />';
                }
            }
        }
        
        // Add hidden inputs for column sorting id and direction:
        // these start populated with the View settings values and will be changed when a column title is clicked.
        if (isset($view_layout_settings['style']) && ($view_layout_settings['style'] == 'table_of_fields' or $view_layout_settings['style'] == 'table')) {
            if ($view_settings['query_type'][0] == 'posts') {
                $sort_id = $view_settings['orderby'];
                $sort_dir = strtolower($view_settings['order']);
            }
            if ($view_settings['query_type'][0] == 'taxonomy') {
                $sort_id = $view_settings['taxonomy_orderby'];
                $sort_dir = strtolower($view_settings['taxonomy_order']);
            }
            if ($view_settings['query_type'][0] == 'users') {
                $sort_id = $view_settings['users_orderby'];
                $sort_dir = strtolower($view_settings['users_order']);
            }

            if (
				isset( $_GET['wpv_column_sort_id'] ) 
				&& esc_attr( $_GET['wpv_column_sort_id'] ) != '' 
				&& isset( $_GET['wpv_view_count'] ) 
				&& esc_attr( $_GET['wpv_view_count'] ) == $WP_Views->get_view_count() 
			) {
                $sort_id = esc_attr( $_GET['wpv_column_sort_id'] );
            }
            if (
				isset( $_GET['wpv_column_sort_dir'] ) 
				&& esc_attr( $_GET['wpv_column_sort_dir'] ) != '' 
				&& isset( $_GET['wpv_view_count'] ) 
				&& esc_attr( $_GET['wpv_view_count'] ) == $WP_Views->get_view_count()
			) {
                $sort_dir = esc_attr( $_GET['wpv_column_sort_dir'] );
            }
            
            $out .= '<input id="wpv_column_sort_id" type="hidden" name="wpv_column_sort_id" value="' . $sort_id . '" />';
            $out .= '<input id="wpv_column_sort_dir" type="hidden" name="wpv_column_sort_dir" value="' . $sort_dir . '" />';
        }
        
        /**
        * Add other hidden fields for:
        *
        * max number of pages for this View
        * preload reach
        * widget ID when aplicable
        * View count for multiple Views per pages
        * View hash
        * current post ID when needed
        */
        
		/**
		* @todo this might not need a name at all...
		*/
        $out .= '<input id="wpv_paged_max-' . $WP_Views->get_view_count() . '" type="hidden" name="wpv_paged_max" value="' . intval($WP_Views->get_max_pages()) . '" />';
        
        if ( isset( $view_settings['pagination']['pre_reach'] ) ) { $pre_reach = intval($view_settings['pagination']['pre_reach']); } else { $pre_reach = 1; }
		/**
		* @todo this might not need a name at all...
		*/
        $out .= '<input id="wpv_paged_preload_reach-' . $WP_Views->get_view_count() . '" type="hidden" name="wpv_paged_preload_reach" value="' . $pre_reach . '" />';
        
        $out .= '<input id="wpv_widget_view-' . $WP_Views->get_view_count() . '" type="hidden" name="wpv_widget_view_id" value="' . intval($WP_Views->get_widget_view_id()) . '" />';
        $out .= '<input id="wpv_view_count-' . $WP_Views->get_view_count() . '" type="hidden" name="wpv_view_count" value="' . $WP_Views->get_view_count() . '" />';

        $view_data = $WP_Views->get_view_shortcodes_attributes();
        //$view_data['view_id'] = $WP_Views->get_current_view();
        $out .= '<input id="wpv_view_hash-' . $WP_Views->get_view_count() . '" type="hidden" name="wpv_view_hash" value="' . base64_encode(json_encode($view_data)) . '" />';
    
        $requires_current_page = false;
		/**
		* wpv_filter_requires_current_page
		*
		* Whether the current View requires the current page for any filter
		*
		* @param $requires_current_page boolean
		* @param $view_settings
		*
		* @since unknown
		*/
        $requires_current_page = apply_filters('wpv_filter_requires_current_page', $requires_current_page, $view_settings);
        if ( $requires_current_page ) {
            $current_post = $WP_Views->get_top_current_page();
            if (
				$current_post 
				&& isset( $current_post->ID ) 
			) {
                $out .= '<input id="wpv_post_id-' . esc_attr( $WP_Views->get_view_count() ) . '" type="hidden" name="wpv_post_id" value="' . esc_attr( $current_post->ID ) . '" class="js-wpv-keep-on-clear" />';
            }
        }
		
		$requires_parent_term = false;
		/**
		* wpv_filter_requires_parent_term
		*
		* Whether the current View is nested and requires the parent term for any filter
		*
		* @param $requires_parent_term boolean
		* @param $view_settings
		*
		* @since unknown
		*/
		$requires_parent_term = apply_filters( 'wpv_filter_requires_parent_term', $requires_parent_term, $view_settings );
		if ( $requires_parent_term ) {
            $parent_term_id = $WP_Views->get_parent_view_taxonomy();
            if ( $parent_term_id ) {
                $out .= '<input id="wpv_aux_parent_term_id-' . esc_attr( $WP_Views->get_view_count() ) . '" type="hidden" name="wpv_aux_parent_term_id" value="' . esc_attr( $parent_term_id ) . '" class="js-wpv-keep-on-clear" />';
            }
        }
		
		$requires_parent_user = false;
		/**
		* wpv_filter_requires_parent_user
		*
		* Whether the current View is nested and requires the parent user for any filter
		*
		* @param $requires_parent_user boolean
		* @param $view_settings
		*
		* @since unknown
		*/
		$requires_parent_user = apply_filters( 'wpv_filter_requires_parent_user', $requires_parent_user, $view_settings );
		if ( $requires_parent_user ) {
            $parent_user_id = $WP_Views->get_parent_view_user();
            if ( $parent_user_id ) {
                $out .= '<input id="wpv_aux_parent_user_id-' . esc_attr( $WP_Views->get_view_count() ) . '" type="hidden" name="wpv_aux_parent_user_id" value="' . esc_attr( $parent_user_id ) . '" class="js-wpv-keep-on-clear" />';
            }
        }
		
        add_action('wp_footer', 'wpv_pagination_js');
        
        // Rollover
        if (isset($view_settings['pagination']['mode']) && $view_settings['pagination']['mode'] == 'rollover') {
            wpv_pagination_rollover_shortcode();
        }
        
    }
    
	/**
	* Filter wpv_filter_start_filter_form
	*
	* @param $out the default form opening tag followed by the required hidden input tags needed for pagination and table sorting
	* @param $view_settings the current View settings
	* @param $view_id the ID of the View being displayed
	* @param $is_required [true|false] whether this View requires a form to be displayed (has a parametric search OR uses table sorting OR uses pagination)
	*
	* This can be useful to create additional inputs for the current form without needing to add them to the Filter HTML textarea
	* Also, can help users having formatting issues
	*
	* @return $out
	*
	* Since 1.5.1
	*
	*/
	
	$out = apply_filters( 'wpv_filter_start_filter_form', $out, $view_settings, $view_id, $is_required );
    
    return $out;
}

/**
 * Views-Shortcode: wpv-filter-end
 *
 * Description: The [wpv-filter-end] shortcode is the end point
 * for any controls that the views filter generates.
 *
 * Parameters:
 * This takes no parameters.
 *
 * Example usage:
 *
 * Link:
 *
 * Note:
 *
 */
  
add_shortcode('wpv-filter-end', 'wpv_filter_shortcode_end');
function wpv_filter_shortcode_end($atts){
	
	global $WP_Views;
	$view_id = $WP_Views->get_current_view();
	$view_settings = $WP_Views->get_view_settings();
	$is_required = false;
	$out = '';
	
    if (_wpv_filter_is_form_required()) {
		$is_required = true;
        extract(
            shortcode_atts( array(), $atts )
        );
        $out = '</form>';
        
	}
	
	/**
	* Filter wpv_filter_end_filter_form
	*
	* @param $out the default form closing tag
	* @param $view_settings the current View settings
	* @param $view_id the ID of the View being displayed
	* @param $is_required [true|false] whether this View requires a form to be displayed (has a parametric search OR uses table sorting OR uses pagination)
	*
	* This can be useful to create additional inputs for the current form without needing to add them to the Filter HTML textarea
	*
	* @return $out
	*
	* Since 1.5.1
	*
	*/
	
	$out = apply_filters( 'wpv_filter_end_filter_form', $out, $view_settings, $view_id, $is_required );
    $WP_Views->set_force_disable_dependant_parametric_search( false );
    return $out;
}
    
function _wpv_filter_is_form_required() {
    global $WP_Views;

    if ($WP_Views->rendering_views_form()) {
        return true;
    }
    
    $view_layout_settings = $WP_Views->get_view_layout_settings();

    if (isset($view_layout_settings['style']) && $view_layout_settings['style'] == 'table_of_fields') {
        // required for table sorting
        return true;
    }

    $view_settings = $WP_Views->get_view_settings();
    if ($view_settings['pagination'][0] == 'enable' || $view_settings['pagination']['mode'] == 'rollover') {
        return true;
    }

    $meta_html = isset( $view_settings['filter_meta_html'] ) ? $view_settings['filter_meta_html'] : '';
	if(preg_match('#\\[wpv-control.*?\\]#is', $meta_html, $matches)) {
	    if ($matches[0] != '') {
	        return true;
	    }
	}


    if (isset($view_settings['post_search_value']) || isset($view_settings['taxonomy_search_value'])) {
        return true;
    }
    
    return false;
}

/**
 * Views-Shortcode: wpv-filter-submit
 *
 * Description: The [wpv-filter-submit] shortcode adds a submit button to
 * the form that the views filter generates. An example is the "Submit" button
 * for a search box
 *
 * Parameters:
 * 'hide' => 'true'|'false'
 * 'name' => The text to be used on the button.
 * 'class' => The classname to be applied to the button - space-separated list
 * 'type' => The HTML tag to use, input|button
 *
 * Example usage:
 *
 * Link:
 *
 * Note:
 *
 */
  
add_shortcode( 'wpv-filter-submit', 'wpv_filter_shortcode_submit' );
function wpv_filter_shortcode_submit( $atts ){
    if ( _wpv_filter_is_form_required() ) {
        extract(
            shortcode_atts( array(
				'name' => '',
				'class' => '',
				'hide' => '',
				'type' => 'input',
                'style' => ''
				),
				$atts 
			)
        );
        global $WP_Views;
        $view_settings = $WP_Views->get_view_settings();
		$classnames = array();
		$class_to_add = '';
        $hide_to_add = '';
		$out = '';
		
        if ( $hide == 'true' ) {
            $hide_to_add = ' display:none" ';
        }
        if ( ! empty( $class ) ) {
            $classnames = explode( ' ', esc_attr( $class ) );
        }
        if ( ! empty( $style ) ) {
            $hide_to_add .= esc_attr( $style );
        }        
        if ( ! empty( $hide_to_add ) ){
            $hide_to_add = ' style="'. $hide_to_add .'"';
        }
        
        if ( ! isset( $view_settings['dps'] ) ) {
			$view_settings['dps'] = array();
		}
		if ( ! isset( $view_settings['dps']['ajax_results'] ) ) {
			$view_settings['dps']['ajax_results'] = 'disable';
		}
		if ( ! isset( $view_settings['dps']['ajax_results_submit'] ) ) {
			$view_settings['dps']['ajax_results_submit'] = 'reload';
		}
		$ajax = $view_settings['dps']['ajax_results'];
		$ajax_submit = $view_settings['dps']['ajax_results_submit'];
		if ( $ajax == 'enable' || ( $ajax == 'disable' && $ajax_submit == 'ajaxed' ) ) {
			$classnames[] = 'js-wpv-submit-trigger';
		}
		
		if ( count( $classnames ) > 0 ) {
			$class_to_add = ' class="' . implode( ' ', $classnames ) . '"';
		}        
        
		$aux_array = $WP_Views->view_used_ids;
		$view_name = get_post_field( 'post_name', end($aux_array));
        $name = wpv_translate( 'submit_name', $name, false, 'View ' . $view_name );
		
		if ( $type == 'button' ) {
			$out .= '<button type="submit"' . $hide_to_add . $class_to_add . ' >' . $name . '</button>';
		} else {
			$out .= '<input type="submit" value="' . esc_attr( $name ) . '" name="wpv_filter_submit"' . $hide_to_add . $class_to_add . ' />';
		}
        
        return $out;
    } else {
        return '';
    }
}

/**
 * Views-Shortcode: wpv-post-count
 *
 * Description: The [wpv-post-count] shortcode displays the number of posts
 * that will be displayed on the page. When using pagination, this value will
 * be limited by the page size and the number of remaining results.
 *
 * Parameters:
 * This takes no parameters.
 *
 * Example usage:
 * Showing [wpv-post-count] posts of [wpv-found-count] posts found
 *
 * Link:
 *
 * Note:
 * This shortcode is deprecated in favor of [wpv-items-count]
 *
 */
  
add_shortcode('wpv-post-count', 'wpv_post_count');
function wpv_post_count($atts){
    extract(
        shortcode_atts( array(), $atts )
    );

    global $WP_Views;
    
    $query = $WP_Views->get_query();
    
    if ($query) {
        return $query->post_count;
    } else {
        return '';
    }
}


/**
 * Views-Shortcode: wpv-items-count
 *
 * Description: The [wpv-items-count] shortcode displays the number of items (posts/taxonomy terms/users)
 * that will be displayed on the page. When using pagination, this value will
 * be limited by the page size and the number of remaining results.
 *
 * Parameters:
 * This takes no parameters.
 *
 * Example usage:
 * Showing [wpv-items-count] posts of [wpv-found-count] posts found
 *
 * Link:
 *
 * Note:
 *
 */
  
add_shortcode('wpv-items-count', 'wpv_items_count');
function wpv_items_count($atts){
     extract(
        shortcode_atts( array(), $atts )
    );

    global $WP_Views;

	if ( isset( $WP_Views->current_view ) ) {
		$view_settings = $WP_Views->get_view_settings( $WP_Views->current_view );
    }
	$out = '';
	
	if ( !isset($view_settings['query_type'][0]) || ( isset($view_settings['query_type'][0]) && $view_settings['query_type'][0]=='posts' )){
    	$query = $WP_Views->get_query();
		if ( isset($query->post_count) ){
			$out = $query->post_count;
		}
	}elseif( isset($view_settings['query_type'][0]) && $view_settings['query_type'][0]=='users' ){
		if ( isset($WP_Views->users_data['item_count_this_page']) ){
			$out = $WP_Views->users_data['item_count_this_page']; 
		}
	}
	elseif( isset($view_settings['query_type'][0]) && $view_settings['query_type'][0]=='taxonomy' ){
		if ( isset($WP_Views->taxonomy_data['item_count_this_page']) ){
			$out = $WP_Views->taxonomy_data['item_count_this_page']; 
		}
	}
    
	return $out;
}


    
/**
 * Views-Shortcode: wpv-found-count
 *
 * Description: The [wpv-found-count] shortcode displays the total number of
 * items (posts/taxonomy terms/users) that have been found by the Views query. This value is calculated
 * before pagination, so even if you are using pagination, it will return
 * the total number of posts matching the query.
 *
 * Parameters:
 * This takes no parameters.
 *
 * Example usage:
 * Showing [wpv-post-count] posts of [wpv-found-count] posts found
 *
 * Link:
 *
 * Note:
 *
 */
  
 
add_shortcode('wpv-found-count', 'wpv_found_count');
function wpv_found_count($atts){
    extract(
        shortcode_atts( array(), $atts )
    );

    global $WP_Views;

	if ( isset( $WP_Views->current_view ) ) {
		$view_settings = $WP_Views->get_view_settings( $WP_Views->current_view );
    }
	$out = '';
	
	if ( !isset($view_settings['query_type'][0]) || ( isset($view_settings['query_type'][0]) && $view_settings['query_type'][0]=='posts' )){
    	$query = $WP_Views->get_query();
		if ( isset($query->found_posts) ){
			$out = $query->found_posts;
		}
	}elseif( isset($view_settings['query_type'][0]) && $view_settings['query_type'][0]=='users' ){
		if ( isset($WP_Views->users_data['item_count']) ){
			$out = $WP_Views->users_data['item_count']; 
		}
	}
	elseif( isset($view_settings['query_type'][0]) && $view_settings['query_type'][0]=='taxonomy' ){
		if ( isset($WP_Views->taxonomy_data['item_count']) ){
			$out = $WP_Views->taxonomy_data['item_count']; 
		}
	}
    
	return $out;
}

/**
 * Views-Shortcode: wpv-posts-found
 *
 * Description: The wpv-posts-found shortcode will display the text inside
 * the shortcode if there are posts found by the Views query.
 *
 * Parameters:
 * This takes no parameters.
 *
 * Example usage:
 * [wpv-posts-found]Some posts were found[/wpv-posts-found]
 *
 * Link:
 *
 * Note:
 * This shortcode is deprecated in favour of the new [wpv-items-found]
 *
 */
  
add_shortcode('wpv-posts-found', 'wpv_posts_found');
function wpv_posts_found($atts, $value){
    extract(
        shortcode_atts( array(), $atts )
    );

    global $WP_Views;
    
    $query = $WP_Views->get_query();

    if ($query && ($query->found_posts != 0 || $query->post_count != 0)) {
        // display the message when posts are found.
        return wpv_do_shortcode($value);
    } else {
        return '';
    }
    
}
    
/**
 * Views-Shortcode: wpv-no-posts-found
 *
 * Description: The wpv-no-posts-found shortcode will display the text inside
 * the shortcode if there are no posts found by the Views query.
 *
 * Parameters:
 * This takes no parameters.
 *
 * Example usage:
 * [wpv-no-posts-found]No posts found[/wpv-no-posts-found]
 *
 * Link:
 *
 * Note:
 * This shortcode is deprecated in favour of the new [wpv-no-items-found]
 *
 */
  
add_shortcode('wpv-no-posts-found', 'wpv_no_posts_found');
function wpv_no_posts_found($atts, $value){
    extract(
        shortcode_atts( array(), $atts )
    );

    global $WP_Views;
    
    $query = $WP_Views->get_query();

    if ($query && $query->found_posts == 0 && $query->post_count == 0) {
        // display the message when no posts are found.
        return wpv_do_shortcode($value);
    } else {
        return '';
    }
    
}

/**
 * Views-Shortcode: wpv-items-found
 *
 * Description: The wpv-items-found shortcode will display the text inside
 * the shortcode if there are items found by the Views query.
 *
 * Parameters:
 * This takes no parameters.
 *
 * Example usage:
 * [wpv-items-found]Some posts/taxonomy terms/users were found[/wpv-items-found]
 *
 * Link:
 *
 * Note:
 *
 */
  
add_shortcode('wpv-items-found', 'wpv_items_found');
function wpv_items_found($atts, $value){
    extract(
        shortcode_atts( array(), $atts )
    );

    global $WP_Views;
    
    $view_settings = $WP_Views->get_view_settings();

    if ( isset( $view_settings['query_type'] ) && isset( $view_settings['query_type'][0] ) && 
        ( $view_settings['query_type'][0] == 'taxonomy' || $view_settings['query_type'][0] == 'users') ) {

    if ( $view_settings['query_type'][0] == 'users' ){
	   $number = $WP_Views->get_users_found_count();
    }else{
       $number = $WP_Views->get_taxonomy_found_count();
    }

	if ($number && $number != 0) {
		// display the message when posts are found.
		return wpv_do_shortcode($value);
	} else {
		return '';
	}
	
    } else {
    
	$query = $WP_Views->get_query();

	if ($query && ($query->found_posts != 0 || $query->post_count != 0)) {
		// display the message when posts are found.
		return wpv_do_shortcode($value);
	} else {
		return '';
	}
    }
    
}

/**
 * Views-Shortcode: wpv-no-items-found
 *
 * Description: The wpv-no-items-found shortcode will display the text inside
 * the shortcode if there are no items found by the Views query.
 *
 * Parameters:
 * This takes no parameters.
 *
 * Example usage:
 * [wpv-no-items-found]No items found[/wpv-no-items-found]
 *
 * Link:
 *
 * Note:
 *
 */
  
add_shortcode('wpv-no-items-found', 'wpv_no_items_found');
function wpv_no_items_found($atts, $value){
    extract(
        shortcode_atts( array(), $atts )
    );

    global $WP_Views;
    
    $view_settings = $WP_Views->get_view_settings();
    
    if ( isset( $view_settings['query_type'] ) && isset( $view_settings['query_type'][0] ) && $view_settings['query_type'][0] == 'taxonomy' ) {
    
	$number = $WP_Views->get_taxonomy_found_count();
	
	if ( isset($number) && $number === 0) {
		// display the message when posts are found.
		return wpv_do_shortcode($value);
	} else {
		return '';
	}
	
    } else if ( $view_settings['query_type'][0] == 'users' ){
    
        $number = $WP_Views->get_users_found_count();
        
        if ( isset($number) && $number === 0) {
		// display the message when posts are found.
		return wpv_do_shortcode($value);
	} else {
		return '';
	}
	
    } else {
    
	$query = $WP_Views->get_query();

	if ($query && $query->found_posts == 0 && $query->post_count == 0) {
		// display the message when no posts are found.
		return wpv_do_shortcode($value);
	} else {
		return '';
	}
    }
}
    
/*
         
    This shows the user interface to the end user on page
    that contains the view.
    
*/

function wpv_filter_show_user_interface($name, $values, $selected, $style) {
    $out = '';
    $out .= "<div>\n";
    
    if ($style == 'drop_down') {
        $out .= '<select name="'. $name . '[]">' . "\n";
    }
    
    foreach($values as $v) {
        switch ($style) {
            case "checkboxes":
                if (is_array($selected)) {
                    $checked = @in_array($v, $selected) ? ' checked="checked"' : '';
                } else {
                    $checked = $v == $selected ? ' checked="checked"' : '';
                }
                $out .= '<label><input type="checkbox" name="' . $name. '[]" value="' . $v . '" ' . $checked . ' />&nbsp;' . $v . "</label>\n";
                break;

            case "radios":
                if (is_array($selected)) {
                    $checked = @in_array($v, $selected) ? ' checked="checked"' : '';
                } else {
                    $checked = $v == $selected ? ' checked="checked"' : '';
                }
                $out .= '<label><input type="radio" name="' . $name. '[]" value="' . $v . '" ' . $checked . ' />&nbsp;' . $v . "</label>\n";
                break;

            case "drop_down":
                if (is_array($selected)) {
                    $is_selected = @in_array($v, $selected) ? ' selected="selected"' : '';
                } else {
                    $is_selected = $v == $selected ? ' selected="selected"' : '';
                }
                $out .= '<option value="' . $v . '" ' . $is_selected . '>' . $v . "</option>\n";
                break;
        }
    }

    if ($style == 'drop_down') {
        $out .= "</select>\n";
    }
    
    $out .= "</div>\n";
    
    return $out;
}


/**
 * 
 * Views-Shortcode: wpv-control
 *
 * Description: Add filters for View
 *
 * Parameters:
 * type: Type of retrieved field layout 'radio', 'checkbox', 'select', 'multi-select', 'textfield', 'checkboxes', 'datepicker'
 * url_param: The URL parameter passed as an argument
 * values: Optional, a list of supplied values
 * display_values: Optional, a list of values to display for the corresponding values
 * auto_fill: Optional, when set to a "field-slug" the control will be populated with custom field values from the database.
 * auto_fill_default: Optional, use to set the default, unselected, value of the control. eg Ignore or Don't care
 * auto_fill_sort: Optional. 'asc', 'desc', 'ascnum', 'descnum', 'none'. Defaults to ascending.
 * field: Optional, a custom field to retrieve values from
 * title: Optional, use for the checkbox title
 * taxonomy: Optional, use when a taxonomy control should be displayed.
 * taxonomy_orderby. Optional. 'name', 'id', 'count', 'slug', 'term_group', 'none'. Defaults to 'name'
 * taxonomy_order: Optional 'ASC', 'DESC'. Defaults to ascending.
 * default_label: Optional, use when a taxonomy control should be displayed using select input type.
 * date_format: Optional, use for a datepicker control
 *
 * Example usage:
 *
 * Link:
 * More details about this shortcode here: <a href="http://wp-types.com/documentation/wpv-control-fields-in-front-end-filters/?utm_source=viewsplugin&utm_campaign=views&utm_medium=filter-help-link&utm_term=http://wp-types.com/documentation/wpv-control-fields-in-front-end-filters/" title="wpv-control – Displaying fields in front-end filters">http://wp-types.com/documentation/wpv-control-fields-in-front-end-filters/</a>
 *
 * Note:
 *
 */
function wpv_shortcode_wpv_control($atts) {
	
	// First control checks
	if ( !isset( $atts['url_param'] ) ) {
		return __('The url_param is missing from the wpv-control shortcode argument.', 'wpv-views');
	}
	if ( ( !isset( $atts['type'] ) || $atts == '' ) && !isset( $atts['field'] ) ) {
		return __('The "type" or "field" needs to be set in the wpv-control shortcode argument.', 'wpv-views');
	}
	
	//Start the shortcode management
	global $WP_Views, $no_parameter_found;
	$aux_array = $WP_Views->view_used_ids;
	$view_name = get_post_field( 'post_name', end( $aux_array ) );
	
	extract(
		shortcode_atts(array(
				'type' => '', // select, multi-select, checbox, checkboxes, radio/radios, date/datepicker, textfield
				'values' => array(), // (optional) comma-separated list of user-provided values
                'display_values' => array(), // (optional) comma-separated list of user-provided display values
				'field' => '', // name of the custom field
				'url_param' => '', // URL parameter to be used
                'title' => '', // title to be used on a checkbox field type
                'taxonomy' => '', // name of the taxonomy for taxonomies filter controls
                'taxonomy_orderby' => 'name', // order of the terms for taxonomies filter controls
                'taxonomy_order' => 'ASC', // orderby of the terms for taxonomies filter controls
                'format' => false, // format of the display value, use %%NAME%% or %%COUNT%% as placeholders
                'default_label' => '', // default label for taxonomies filter controls when using select input type
                'hide_empty' => 'false', // option to hide empty terms for taxonomies filter controls
                'auto_fill' => '', // options to auto fill values for custom fields filter controls - provide the field name
                'auto_fill_default' => '', // default value when using auto_fill for custom fields filter controls
                'auto_fill_sort' => '', // order when using auto_fill for custom fields filter controls
                'date_format' => '', // date format for date controls
				'default_date' => '',  // default date for date controls
				'force_zero' => 'false',
                'style' => '', // inline styles for input
                'class' => '', // input classes
                'label_style' => '', // inline styles for input label
                'label_class' => '' // classes for input label
			), $atts)
	);
	
    $style = esc_attr( $style );
    $class = esc_attr( $class );
    $label_style = esc_attr( $label_style );
    $label_class = esc_attr( $label_class );  
    
	// First, parametric search control for taxonomy
	if ( $taxonomy != '' ) {
		// Translate the default label if any
		if ( !empty( $default_label ) ) {
			$default_label = wpv_translate( $url_param . '_default_label', $default_label, false, 'View ' . $view_name );
			$atts['default_label'] = $default_label;
		}
		// Render the taxonomy control
		return wpv_render_taxonomy_control( $atts );
    }
	
	// Before doing anything else, rule out textfields
	if ( $type == 'textfield' ) {
		// Textfield field
		$default_value = '';
		if ( isset( $_GET[ $url_param ] ) ) {
			$default_value = stripslashes( urldecode( sanitize_text_field( $_GET[ $url_param ] ) ) );
		}
		
		// Render the form content
		$element = wpv_form_control( array(
				'field' => array(
						'#type' => 'textfield',
						'#id' => 'wpv_control_textfield_' . $url_param,
						'#name' => $url_param,
						'#attributes' => array( 'style' => $style, 'class' => 'js-wpv-filter-trigger-delayed ' . $class  ),
						'#inline' => true,
						'#value' => $default_value ) ) );
		return $element;
	}
    
	// Check if the View has dependency enabled
    $view_settings = $WP_Views->get_view_settings();
	$dependant = false;
	$counters = ( $format && strpos( $format, '%%COUNT%%' ) !== false ) ? true : false;
	$empty_action = array();
	if ( isset( $view_settings['dps'] )
		&& is_array( $view_settings['dps'] )
		&& isset( $view_settings['dps']['enable_dependency'] )
		&& $view_settings['dps']['enable_dependency'] == 'enable' )
	{
		$dependant = true;
		$force_disable_dependant = $WP_Views->get_force_disable_dependant_parametric_search();
		if ( $force_disable_dependant ) {
			$dependant = false;
		}
	}
	
	// Some basic values
	if ( empty( $field ) ) {
		if ( empty( $auto_fill ) ) {
			// In this case, the shortcode is not about a custom field filter but a generic one without taxonomy, field or auto_fill attributes
			// It can be used to generate custom form inputs, given that the user provides values (and maybe display_values)
			// So we need to disable dependency
			$dependant = false;
			$counters = false;
		} else {
			$field_real_name = _wpv_get_field_real_slug( $auto_fill );
		}
	} else {
		$field_real_name = _wpv_get_field_real_slug( $field );
	}
	
	$display_values_trans = false; // flag to whether the display_values need to be translated
	$out = '';
	
	// If dependency is ON, build the basic data and cache
	if ( $dependant || $counters ) {
		$empty_default = 'hide';
		$empty_alt = 'disable';
		$empty_options = array( 'select', 'radios', 'checkboxes' ); // multi-select is a special case because of dashes and underscores
		foreach ( $empty_options as $empty_opt ) {
			if ( isset( $view_settings['dps'][ 'empty_' . $empty_opt ] )
				&& $view_settings['dps'][ 'empty_' . $empty_opt ] == $empty_alt )
			{
				$empty_action[ $empty_opt ] = $empty_alt;
			} else {
				$empty_action[ $empty_opt ] = $empty_default;
			}
		}
		if ( isset( $view_settings['dps']['empty_multi_select'] )
			&& $view_settings['dps']['empty_multi_select'] == $empty_alt )
		{
			$empty_action['multi-select'] = $empty_alt;
		} else {
			$empty_action['multi-select'] = $empty_default;
		}
		$wpv_data_cache = array();
		$original_value = $view_settings[ 'custom-field-' . $field_real_name . '_value' ];
		$processed_value = wpv_apply_user_functions( $original_value );
		$compare_function = $view_settings[ 'custom-field-' . $field_real_name . '_compare' ];
		$current_value_key = false;
		// @todo check IN, NOT IN and != compare functions
		$comparator = 'equal';
		$filter_full_list = false;
		if ( $compare_function == 'BETWEEN' ) {
			$original_value_array = array_map( 'trim', explode( ',', $original_value ) );
			$processed_value_array = array_map( 'trim', explode( ',', $processed_value ) );
			$current_value_key = array_search( 'URL_PARAM(' . $url_param . ')', $original_value_array );
			if ( $current_value_key !== false ) {
				$processed_value = isset( $processed_value_array[ $current_value_key ] ) ? $processed_value_array[ $current_value_key ] : $no_parameter_found;
				if ( $current_value_key < 1 ) {
					$comparator = 'lower-equal-than';
				} else if ( $current_value_key > 0 ) {
					$comparator = 'greater-equal-than';
				}
			}
		} else if ( $compare_function == '>' ) {
			$comparator = 'lower-than';
		} else if ( $compare_function == '>=' ) {
			$comparator = 'lower-equal-than';
		} else if ( $compare_function == '<' ) {
			$comparator = 'greater-than';
		} else if ( $compare_function == '<=' ) {
			$comparator = 'greater-equal-than';
		}
		// Construct $wpv_data_cache['post_meta']
		if ( $processed_value == $no_parameter_found ) {
			global $wp_object_cache;
			$wpv_data_cache = isset( $wp_object_cache->cache ) ? $wp_object_cache->cache : array();
			$aux_query_count = null;
		} else {
			// When there is a selected value, create a pseudo-cache based on all the other filters
			// Note that checkboxes filters can generate nested meta_query entries
			$query = wpv_get_dependant_view_query_args();
			$aux_cache_query = null;
			$filter_full_list = true;
			if ( isset( $query['meta_query'] ) && is_array( $query['meta_query'] ) ) {
				foreach ( $query['meta_query'] as $qt_index => $qt_val ) {
					if ( is_array( $qt_val ) ) {
						foreach ( $qt_val as $qt_val_key => $qt_val_val ) {
							if ( 
								$qt_val_key == 'key' 
								&& $qt_val_val == $field_real_name
							) {
								if ( $compare_function == 'BETWEEN' ) {
									if ( 
										$qt_val['compare'] == 'BETWEEN' 
										&& $current_value_key !== false 
									) {
										$qt_val['value'] = isset( $qt_val['value'] ) ? $qt_val['value'] : '';
										$passed_values = is_array( $qt_val['value'] ) ? $qt_val['value'] : array_map( 'trim', explode( ',', $qt_val['value'] ) );
										if ( $current_value_key < 1 && isset( $passed_values[1] ) ) {
											$query['meta_query'][ $qt_index ]['compare'] = '<=';
											$query['meta_query'][ $qt_index ]['value']= $passed_values[1];
										} else if ( $current_value_key > 0 && isset( $passed_values[0] ) ) {
											$query['meta_query'][ $qt_index ]['compare'] = '>=';
											$query['meta_query'][ $qt_index ]['value']= $passed_values[0];
										}
									} else {
										unset( $query['meta_query'][ $qt_index ] );
									}
									// if $compare_function is BETWEEN and we have a meta_query not using BETWEEN, we have a partial query here, so keep it
								} else {
									unset( $query['meta_query'][$qt_index] );
								}
							} else if ( 
								is_array( $qt_val_val ) 
								&& isset( $qt_val_val['key'] ) 
								&& $qt_val_val['key'] == $field_real_name
							) {
								if ( $compare_function == 'BETWEEN' ) {
									if ( 
										$qt_val_val['compare'] == 'BETWEEN' 
										&& $current_value_key !== false 
									) {
										$qt_val_val['value'] = isset( $qt_val_val['value'] ) ? $qt_val_val['value'] : '';
										$passed_values = is_array( $qt_val_val['value'] ) ? $qt_val_val['value'] : array_map( 'trim', explode( ',', $qt_val_val['value'] ) );
										if ( $current_value_key < 1 && isset( $passed_values[1] ) ) {
											$query['meta_query'][ $qt_index ][ $qt_val_key ]['compare'] = '<=';
											$query['meta_query'][ $qt_index ][ $qt_val_key ]['value']= $passed_values[1];
										} else if ( $current_value_key > 0 && isset( $passed_values[0] ) ) {
											$query['meta_query'][ $qt_index ][ $qt_val_key ]['compare'] = '>=';
											$query['meta_query'][ $qt_index ][ $qt_val_key ]['value']= $passed_values[0];
										}
									} else {
										unset( $query['meta_query'][ $qt_index ][ $qt_val_key ] );
									}
									// if $compare_function is BETWEEN and we have a meta_query not using BETWEEN, we have a partial query here, so keep it
								} else {
									unset( $query['meta_query'][$qt_index][ $qt_val_key ] );
								}
							}
						}
					}
				}
			}
			$aux_cache_query = new WP_Query($query);
			if ( is_array( $aux_cache_query->posts ) && !empty( $aux_cache_query->posts ) ) {
				$aux_query_count = count( $aux_cache_query->posts );
				$f_fields = array( $field_real_name );
				$wpv_data_cache = wpv_custom_cache_metadata( $aux_cache_query->posts, array( 'cf' => $f_fields ) );
			}
		}
		if ( !isset( $wpv_data_cache['post_meta'] ) ) {
			$wpv_data_cache['post_meta'] = array();
		}
		
		// OK, for checkboxes custom fields the stored value is NOT the one we use for filtering
		// So instead of filtering $wpv_data_cache['post_meta'] we will loop it to see if the $field_real_name key exists
		// AND check the serialized value to see if it contains the given real value (warning, not the label!)
		// AND break as soon as true because we need no counters
		// Expensive, but not sure if more than wp_list_filter though
	}
	
	// Management of multiselect
	$multi = '';
	if ( $type == 'multi-select') {
		$type = 'select';
		$multi = 'multiple';
	}
	
	//  $filter_check_type = _wpv_is_field_of_type( $auto_fill, 'checkboxes' ) ? 'checkboxes' : 'other';
	$filter_check_type = wpv_types_get_field_type( $field );
	
	if ( $auto_fill != '' ) {
		/**
		* If using auto_fill, populate the values and display_values arrays
		*/
        
        /**
        * First we are going to populate those variables
        */
        $fields = array(); // this will hold the Types fields from the Options
        $db_values = array(); // this will hold the field values from Types options or from the database
        $display_text = array(); // this will hold the field values pretty display text, if it is a Types field with options
        $auto_fill_default_trans = false; // flag to whether the auto_fill_default has translated, based on whether it is one of the existing values
        
        if ( !function_exists( 'wpcf_admin_fields_get_fields' ) ) {
			if( defined( 'WPCF_EMBEDDED_ABSPATH' ) ) {
				include WPCF_EMBEDDED_ABSPATH . '/includes/fields.php';
			}
		}
		if ( function_exists( 'wpcf_admin_fields_get_fields' ) ) {
			$fields = wpcf_admin_fields_get_fields();
		}
		// $field_name = substr($auto_fill, 5); // TODO DONE check this for fields created outside of Types and brought under Types control
		if ( strpos( $auto_fill, 'wpcf-' ) === 0 ) {
			$field_name = substr( $auto_fill, 5 );
        } else {
			$field_name = $auto_fill;
        }

        // If it is a Types field with options
        if ( isset( $fields[ $field_name ] ) && isset( $fields[ $field_name ]['data']['options'] ) ) { 
			// If it is a checkboxes Types field
			if ( _wpv_is_field_of_type( $auto_fill, 'checkboxes' ) ) { 
				$options = $fields[ $field_name ]['data']['options'];
				foreach( $options as $field_key => $option ) {
					// Fill the db_values and display_text (translated if needed) arrays
					$db_values[] = $option['title'];
					$display_text[ $option['title'] ] = wpv_translate( 'field '. $fields[ $field_name ]['id'] .' option '. $field_key .' title', $option['title'], false, 'plugin Types' );
				}
			} else {
				// If it is a Types field different from checkboxes but with options
				$options = $fields[ $field_name ]['data']['options'];
				if ( isset( $options['default'] ) ) {
					// remove the default option from the array
					unset( $options['default'] );
				}
				if ( isset( $fields[ $field_name ]['data']['display'] ) ) {
					$display_option =  $fields[ $field_name ]['data']['display'];
				}
				foreach ( $options as $field_key => $option ) {
					if ( isset( $option['value'] ) ) {
						$db_values[] = $option['value'];
					}
					if ( isset( $display_option )
						&& 'value' == $display_option
						&& isset( $option['display_value'] ) )
					{
						$display_text[ $option['value'] ] = wpv_translate( 'field '. $fields[ $field_name ]['id'] .' option '. $field_key .' title', $option['display_value'], false, 'plugin Types' );
					} else {
						$display_text[ $option['value'] ] = wpv_translate( 'field '. $fields[ $field_name ]['id'] .' option '. $field_key .' title', $option['title'], false, 'plugin Types' );
					}
					if ( $auto_fill_default != '' ) {
						// translate the auto_fill_default option if needed, just when it's one of the existing options
						$auto_fill_default = str_replace( '\,', ',', $auto_fill_default );
						if ( $auto_fill_default == $option['title'] ) {
							$auto_fill_default = wpv_translate( 'field '. $fields[ $field_name ]['id'] .' option '. $field_key .' title', $option['title'], false, 'plugin Types' );
							// set this flat to true: we already have translated auto_fill_default
							$auto_fill_default_trans = true; 
						}
						$auto_fill_default = str_replace( ',', '\,', $auto_fill_default );
					}
				}
			}

			// Now sort the values based on auto_fill_sort
			switch ( strtolower( $auto_fill_sort ) ) {
				case 'desc':
					sort( $db_values );
					$db_values = array_reverse( $db_values );
					break;
				case 'descnum':
					sort( $db_values, SORT_NUMERIC );
					$db_values = array_reverse( $db_values );
					break;
				case 'none':
					break;
				case 'ascnum':
					sort( $db_values, SORT_NUMERIC );
					break;
				default:
					sort( $db_values );
					break;
			}
			
        } else {
			// If it is not a Types field OR is a Types field without options

			global $wpdb;
			$values_to_prepare = array();
			$values_to_prepare[] = $auto_fill;
			$wpdb_where = '';
			if ( isset( $view_settings['post_type'] )
				&& is_array( $view_settings['post_type'] )
				&& ! empty( $view_settings['post_type'] )
				&& ! in_array( 'any', $view_settings['post_type'] ) 
			) {
				$post_type_count = count( $view_settings['post_type'] );
				$post_type_placeholders = array_fill( 0, $post_type_count, '%s' );
				$wpdb_where .= " AND p.post_type IN (" . implode( ",", $post_type_placeholders ) . ") ";
				foreach ( $view_settings['post_type'] as $pt ) {
					$values_to_prepare[] = $pt;
				}
			}
			if ( 
				isset( $view_settings['post_status'] ) 
				&& is_array( $view_settings['post_status'] ) 
				&& ! empty( $view_settings['post_status'] )
			) {
				if ( ! in_array( 'any', $view_settings['post_status'] ) ) {
					$post_status_count = count( $view_settings['post_status'] );
					$post_status_placeholders = array_fill( 0, $post_status_count, '%s' );
					$wpdb_where .= " AND p.post_status IN (" . implode( ",", $post_status_placeholders ) . ") ";
					foreach ( $view_settings['post_status'] as $ps ) {
						$values_to_prepare[] = $ps;
					}
				}
			} else {
				$status = array( 'publish' );
				if ( current_user_can( 'read_private_posts' ) ) {
					$status[] = 'private';
				}
				$wpdb_where .= " AND p.post_status IN ( '" . implode( "','", $status ) . "' ) ";
			}
			$wpdb_orderby = '';
			switch ( strtolower( $auto_fill_sort ) ) {
				case 'desc':
					$wpdb_orderby = "ORDER BY pm.meta_value DESC";
					break;
				case 'descnum':
					$wpdb_orderby = "ORDER BY pm.meta_value + 0 DESC";
					break;
				case 'ascnum':
					$wpdb_orderby = "ORDER BY pm.meta_value + 0 ASC";
					break;
				default:
					$wpdb_orderby = "ORDER BY pm.meta_value ASC";
					break;
			}
			$db_values = $wpdb->get_col( 
				$wpdb->prepare(
					"SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id 
					WHERE pm.meta_key = %s AND pm.meta_value IS NOT NULL AND pm.meta_value != '' 
					{$wpdb_where} 
					{$wpdb_orderby}",
					$values_to_prepare 
				) 
			);
        }
        
        /**
        * Now we are going to fill the $values and $display_values comma-separated strings based on $db_values and, in case, $display_text
        * NOTE if $auto_fill_default_trans is FALSE then the auto_fill_default is NOT one of the existing option titles so we will translate it
        */
        if ( $auto_fill_default != '' ) {
			// If auto_fill_default is not empty, adjust and translate when needed
			if ( !$auto_fill_default_trans ) {
				// translate the auto_fill_default option when it's not one of the existing options
				$auto_fill_default = str_replace( '\,', ',', $auto_fill_default );
				$auto_fill_default = wpv_translate( $url_param . '_auto_fill_default', stripslashes( $auto_fill_default ), false, 'View ' . $view_name );
				$auto_fill_default = str_replace( ',', '\,', $auto_fill_default );
			}
            $values = '';
            $display_values = str_replace( '\,', '%comma%', $auto_fill_default );
            // flag to whether there is an auto_fill_default value that we ad at the beginning of the $display_value string
            $first = false; 
        } else {
            $values = '';
            $display_values = '';
            $first = true;
        }
        foreach( $db_values as $value ) {
            if ( $value !== false ) {
                if ( !$first ) {
                    $values .= ',';
                    $display_values .= ',';
                }
                // HACK to handle commas in values
                $values .= str_replace( ',', '%comma%', $value ); 
                if ( isset( $display_text[$value] ) ) {
					// HACK to handle commas in display_values
					$display_values .= str_replace( ',', '%comma%', $display_text[ $value ] ); 
				} else {
					// HACK to handle commas in display_values
					$display_values .= str_replace( ',', '%comma%', $value ); 
				}
                $first = false;
            }
        }
    // If not using auto_fill, check if there are manually added display_values
    } else if ( !empty( $display_values ) ) { 
		// mark that the display_values need to be translated
		$display_values_trans = true; 
    }
    
	/*
	* Now we have a comma-separated list of $values and $display_values, hopefully ;-D
	* In fact, we count with a $values comma-separated list
	* We will fill the $values_arr array and transform $display_values into an array
	*/
	
	if( !empty( $values ) ) {
		// When values attributes are manually defined, the inner commas are formatted as \, and we need to apply the same HACK as for the automatically set values
		$values_fix = str_replace( '\,', '%comma%', $values );
		// Now, get the $values_arr array of values
		$values_arr = explode( ',', $values_fix );
		// And undo the comma HACK
		$values_arr = str_replace( '%comma%', ',', $values_arr );
        if ( !empty( $display_values ) ) {
			// If there are display_values,again sync the comma HACK
			$display_values = str_replace( '\,', '%comma%', $display_values );
			// Get an array of $display_values
			$display_values = explode( ',', $display_values );
			// And undo the comma HACK
			$display_values = str_replace( '%comma%', ',', $display_values );
			if ( $display_values_trans ) {
				// If we need to translate the $display_values
				$translated_values = array();
				foreach ( $display_values as $index => $valuetrans ) {
					$translated_values[ $index ] = wpv_translate( $url_param . '_display_values_' . ( $index + 1 ), stripslashes( $valuetrans ), false, 'View ' . $view_name );
				}
				$display_values = $translated_values;
			}
        }

		// Parse date expressions in values.
		$values_count = count( $values_arr );
		for( $i = 0; $i < $values_count; ++$i ) {
			$values_arr[ $i ] = wpv_filter_parse_date( $values_arr[ $i ] );
		}
        
		/**
		* Now that we have the $values_arr and $display_values we focus on the kind of output
		* Based on $type we will popuate an $options variable and use the wpv_form_control() function
		*/
		
        if( !in_array( $type, array( 'radio', 'radios', 'select', 'checkboxes' ) ) ) {
            // For wpv-control shortcodes using auto_fill or values/display_values we only allow those kind of types
            $type = 'select';
        }
        if ( $type == 'radio' ) {
            // In fact, radios == radio
            $type = 'radios';
        }
        $options = array();
		// Now, depending on $type
        switch ( $type ) {
        
            case 'checkboxes':
                // If we need to render CHECKBOXES
                $defaults = array();
                $original_get = null;
                if ( isset( $auto_fill_default ) ) {
					// First, check if the defaul value already exists and set the appropriate arrays and values
					$num_auto_fill_default_display = array_count_values( $display_values );
					$auto_fill_default_trans = str_replace( '\,', ',', $auto_fill_default );
					if (
							// if the auto_fill_default is one of the display_values
							( isset( $num_auto_fill_default_display[ $auto_fill_default_trans ] )
							&& $num_auto_fill_default_display[ $auto_fill_default_trans ] > 1 )  
						||
							// OR if the auto_fill_default is one of the values
							in_array( $auto_fill_default_trans, $values_arr ) ) 
					{ 
						// Take out the first element of the $values_arr and the $display_values, which holds and empty string and the auto_fill_default value
						$values_arr_def = array_shift( $values_arr );
						$display_values_def = array_shift( $display_values );
					}
					// Then, set the preliminary $defaults value based on auto_fill_default
					$defaults = str_replace( '\,', '%comma%', $auto_fill_default );
					$defaults = explode( ',', $defaults );
					$defaults = str_replace( '%comma%', ',', $defaults );
					$defaults = array_map( 'trim', $defaults );
                }
                if ( isset( $_GET[ $url_param ] ) ) {
                    // Override $defaults if a set of values is coming from the URL parameter
                    $original_get = $_GET[ $url_param ];
                    $defaults = $_GET[ $url_param ];
                    if ( is_string( $defaults ) ) {
						$defaults = explode( ',',$defaults );
					}
                    unset( $_GET[ $url_param ] );
                }
                $count_values_array = count( $values_arr );
                for( $i = 0; $i < $count_values_array; $i++ ) {
                    // Loop through the $values_arr
                    $value = $values_arr[ $i ];
                    $value = trim( $value );
                    // Check for a display value
                    if ( isset( $display_values[ $i ] ) ) {
                        $display_value = $display_values[ $i ];
                    } else {
                        $display_value = $value;
                    }
                    // Compose the $options for this value
                    $options[ $value ]['#name'] = $url_param . '[]';
                    $options[ $value ]['#title'] = $display_value;
                    $options[ $value ]['#value'] = $value;
                    // set default using option titles too
                    $options[ $value ]['#default_value'] = in_array( $value, $defaults ) || in_array( $options[ $value ]['#title'], $defaults); 
                    $options[ $value ]['#attributes']['class'] = 'js-wpv-filter-trigger ' . $class;
                    $options[ $value ]['#attributes']['style'] = $style;
                    $options[ $value ]['#labelclass'] = $label_class;
                    $options[ $value ]['#labelstyle'] = $label_style;
                    // Dependant stuff
                    if ( $dependant || $counters ) {
						if ( $format ) {
							$display_value_formatted_name = str_replace( '%%NAME%%', $options[ $value ]['#title'], $format );
							$options[ $value ]['#title'] = $display_value_formatted_name;
						}
						$meta_criteria_to_filter = array( $field_real_name => array( $value ) );
						$this_query = $WP_Views->get_query();
						if ( empty( $value ) && !is_numeric( $value ) && is_object( $this_query ) ) {
							if ( isset( $aux_query_count ) ) {
								$this_checker = $aux_query_count;
							} else {
								$this_checker = $this_query->found_posts;
							}
						} else {
							$data = array();
							$data['list'] = $wpv_data_cache['post_meta'];
							$data['args'] = $meta_criteria_to_filter;
							$data['kind'] = $filter_check_type;
							$data['comparator'] = $comparator;
							if ( $counters ) {
								$data['count_matches'] = true;
							}
							$data['filter_full_list'] = $filter_full_list;
							$this_checker = wpv_list_filter_checker( $data );
						}
						if ( $counters ) {
							$display_value_formatted_name = str_replace( '%%COUNT%%', $this_checker, $options[ $value ]['#title'] );
							$options[ $value ]['#title'] = $display_value_formatted_name;                            
						}
						if ( !$this_checker && ( !empty( $value ) || is_numeric( $value ) ) && !$options[ $value ]['#default_value'] && $dependant ) {
							$options[ $value ]['#attributes']['#disabled'] = 'true';
							$options[ $value ]['#labelclass'] .= ' wpv-parametric-disabled ';
							if ( isset( $empty_action['checkboxes'] ) && $empty_action['checkboxes'] == 'hide' ) {
								unset( $options[ $value ] );
							}
						}
					}
//                    $options[$value]['#inline'] = true;
//                    $options[$value]['#after'] = '&nbsp;&nbsp;';
                }
                // Render the form control element
               	$element = wpv_form_control( array(
						'field' => array(
				                '#type' => $type,
				                '#id' => 'wpv_control_' . $type . '_' . $url_param,
				                '#name' => $url_param . '[]',
				                '#attributes' => array( 'style' => '' ),
				                '#inline' => true,
				                '#options' => $options,
								'#before' => '<div class="wpcf-checkboxes-grou">', //we need to wrap them for js purposes
								'#after' => '</div>' ) ) );
                
                if ( $original_get ) {
                    $_GET[ $url_param ] = $original_get;
                }
                break;
                
            default:
                // If we need to check any other field with values and a type that is not checkboxes (radios or select)
                $options_array = array();

                // This one will hold options in a display_vaue => value format so we can use it to compose the default_value later
                $options = array(); 

                $count_values_array = count( $values_arr );
                for( $i = 0; $i < $count_values_array; $i++ ) {
                    // Loop through the $values_arr
                    $value = $values_arr[ $i ];
                    $value = trim( $value );
                    // Check for a display value
                    if ( isset( $display_values[ $i ] ) ) {
                        $display_value = $display_values[ $i ];
                    } else {
                        $display_value = $value;
                    }
                    // Compose the $options for this value
                    $options[ $display_value ] = $value;
                    $options_array[ $display_value ] = array(
							'#title' => $display_value,
							'#value' => $value,
							'#inline' => true,
							'#after' => '<br />' );
                    $options_array[ $display_value ]['#attributes']['class'] = 'js-wpv-filter-trigger';
                    
                    if ( $type == 'radios' ) {
                        $options_array[ $display_value ]['#attributes']['class'] .= ' ' . $class;
                        $options_array[ $display_value ]['#attributes']['style'] = $style;
                        $options_array[ $display_value ]['#labelclass'] = $label_class;
                        $options_array[ $display_value ]['#labelstyle'] = $label_style;
                    }
                    // Dependant stuff
					if ( $dependant || $counters ) {
						if ( $format ) {
							$display_value_formatted_name = str_replace( '%%NAME%%', $options_array[ $display_value ]['#title'], $format );
							$options_array[ $display_value ]['#title'] = $display_value_formatted_name;
						}
						$this_query = $WP_Views->get_query();
						if ( empty( $value ) && !is_numeric( $value ) && is_object( $this_query ) ) {
							if ( isset( $aux_query_count ) ) {
								$this_checker = $aux_query_count;
							} else {
								$this_checker = $this_query->found_posts;
							}
						} else {
							$meta_criteria_to_filter = array( $field_real_name => array( $value ) );
							$data = array();
							$data['list'] = $wpv_data_cache['post_meta'];
							$data['args'] = $meta_criteria_to_filter;
							$data['kind'] = $filter_check_type;
							$data['comparator'] = $comparator;
							if ( $counters ) {
								$data['count_matches'] = true;
							}
							$data['filter_full_list'] = $filter_full_list;
							$this_checker = wpv_list_filter_checker( $data );
						}
						if ( $counters ) {
							$display_value_formatted_counter = str_replace( '%%COUNT%%', $this_checker, $options_array[ $display_value ]['#title'] );
							$options_array[ $display_value ]['#title'] = $display_value_formatted_counter;
						}
						
						if ( !$this_checker && ( !empty( $value ) || is_numeric( $value ) ) && $dependant ) {
							// TODO DONE need to merge this with the default_value below, to avoid hiddin or disabling selected items
							$options_array[ $display_value ]['#disable'] = 'true';
							$options_array[ $display_value ]['#labelclass'] = 'wpv-parametric-disabled';
							if ( $type == 'select' && $multi == 'multiple' ) {
								if ( isset( $empty_action['multi-select'] ) && $empty_action['multi-select'] == 'hide' ) {
									unset( $options_array[ $display_value ] );
								}
							} else if ( isset( $empty_action[ $type ] ) && $empty_action[ $type ] == 'hide' ) {
								unset( $options_array[ $display_value ] );
							}
						}
					}
                }
                
                if ( count( $values_arr ) != count( $options ) ) {
					// if the $values_arr has one more item than $options, there is a repeating value: the default one added to the beginning
					$default_value = reset( $options );
				} else {
					if ( 
						$type == 'radios' 
						|| $multi == 'multiple'
					) {
						$default_value = '';
					} else {
						// so the default value in this case is the first element in $values_arr
						$default_value = isset( $values_arr[0] ) ? $values_arr[0] : '';
					}
				}
				if ( $type == 'radios' ) {
					if ( isset( $_GET[ $url_param ] ) && in_array( $_GET[ $url_param ], $options ) ) {
						$default_value = $_GET[ $url_param ];
					}
					$name_aux = $url_param;
				} else {
					// Basically, if $type == 'select'
					if ( isset( $_GET[ $url_param ] ) ) {
						if ( is_array( $_GET[ $url_param ] ) ) {
							if ( count( array_intersect($_GET[ $url_param ], $options) ) > 0 ) {
								$default_value = $_GET[ $url_param ];
							}
						} else {
							if ( in_array( $_GET[ $url_param ], $options ) ) {
								$default_value = $_GET[ $url_param ];
							}
						}
					}
					$name_aux = $url_param . '[]';
				}

				// Now we need to recreate the $options_array element if it is a default one and is disabled or removed
				if ( is_array( $default_value ) ) {
					foreach ( $default_value as $dv ) {
						$aux_display_values = array_keys( $options, $dv, true );
						foreach ( $aux_display_values as $aux_dv ) {
							// TODO where is $aux_dv defined??
							if ( isset( $options_array[ $aux_dv ] ) ) {
								if ( isset( $options_array[ $aux_dv ]['#disable'] ) ) {
									unset( $options_array[ $aux_dv ]['#disable'] );
								}
								$options_array[ $aux_dv ]['#labelclass'] = '';
							} else {
								$options_array[ $aux_dv ] = array(
										'#title' => $aux_dv,
										'#value' => $dv,
										'#inline' => true,
										'#after' => '<br />' );
								$options_array[ $aux_dv ]['#attributes']['class'] = 'js-wpv-filter-trigger ';
							}
						}
					}
				} else {
					$aux_display_values = array_keys( $options, $default_value, true );
					foreach ( $aux_display_values as $aux_dv ) {
						if ( isset( $options_array[ $aux_dv ] ) ) {
							if ( isset( $options_array[$aux_dv]['#disable'] ) ) {
								unset( $options_array[$aux_dv]['#disable'] );
							}
							$options_array[ $aux_dv ]['#labelclass'] = '';
						} else {
							$options_array[ $aux_dv ] = array(
									'#title' => $aux_dv,
									'#value' => $default_value,
									'#inline' => true,
									'#after' => '<br />' );
							$options_array[ $aux_dv ]['#attributes']['class'] = 'js-wpv-filter-trigger';
						}
					}
				}
				
				$element = wpv_form_control( array(
						'field' => array(
								'#type' => $type,
								'#id' => 'wpv_control_' . $type . '_' . $url_param,
								'#name' => $name_aux,
								'#attributes' => array('style' => $style, 'class' => 'js-wpv-filter-trigger' . $class ),
								'#inline' => true,
								'#options' => $options_array, // NOTE this was originally $options but as it's not an array I can not set a "disabled" option
								'#default_value' => $default_value,
								'#multiple' => $multi // NOTE I'd say that radios do not need multiple but it should do no harm
								) )	);
				break;
        }
		return $element;
		
	} else if ( !empty( $field ) ) {
		/**
		* When field attribute is defined but we do not have auto_fill nor manually entered values
		* In this case, we display the control input based on $type or the field type itself if needed (mainly for Types auto style, but we can expect other combinations)
		*/

		// Check if Types is active because we are using wpcf_admin_fields_get_field()
		if ( !function_exists( 'wpcf_admin_fields_get_field' ) ) {
			if ( defined( 'WPCF_EMBEDDED_ABSPATH' ) ) {
				include WPCF_EMBEDDED_ABSPATH . '/includes/fields.php';
			} else {
				return __( 'Types plugin is required.', 'wpv-views' );
			}
		}
		if ( !function_exists( 'wpv_form_control' ) ) {
			include '../common/functions.php';
		}
		//This is important cause wpcf_admin_fields_get_field works with id: $field - 'wpcf-' and search with 'wpcf-'.$field
		/*if( strpos($field, 'wpcf-') !== false ) {
			$tmp = explode('wpcf-', $field);
			$field = $tmp[1];
		}*/
		// Get field options and translate name if needed
		$field_options = wpcf_admin_fields_get_field( $field );
		if ( empty( $field_options ) ) {
			return __( 'Empty field values or incorrect field defined. ', 'wpv-views' );
		}
        $field_options['name'] = wpv_translate( 'field ' . $field_options['id'] . ' name', $field_options['name'], false, 'plugin Types' );
		// Get field type, override if $type exists and default it to textfield if needed
		$field_type = $field_options['type'];
		if ( !empty( $type ) ) {
			// Watch out: this is where we can override the field type itself
			$field_type = $type;
		}
        if ( !in_array( $field_type, array( 'radio', 'checkbox', 'checkboxes', 'select', 'textfield', 'date', 'datepicker' ) ) ) {
            $field_type = 'textfield';
        }
		// Display time!!
		if ( $field_type == 'radio' ) {
			// Radio field
			$field_radio_options = isset( $field_options['data']['options'] ) ? $field_options['data']['options'] : array();
			$options = array();
			foreach ( $field_radio_options as $key => $opts ) {
				if ( is_array( $opts ) ) {
					
					if ( isset( $field_options['data']['display'] )
						&& 'value' == $field_options['data']['display']
						&& isset( $opts['display_value'] ) )
					{
						// if we have an actual display value and is set to be used, use it
						$display_value = $opts['display_value'];
						$value = $opts['value'];
					} else {
						// else, use the field value title and watch out because checkboxes fields need their titles as values
						$display_value = wpv_translate( 'field '. $field_options['id'] .' option '. $key .' title', $opts['title'], false, 'plugin Types' );
						if ( _wpv_is_field_of_type( 'wpcf-' . $field, 'checkboxes' ) ) {
							$value = $opts['title'];
						} else {
							$value = $opts['value'];
						}
					}
					$options[ $display_value ] = array(
						'#title' => $display_value,
						'#value' => $value,
						'#inline' => true,
						'#after' => '<br />'
                    );
                    $options[ $display_value ]['#attributes']['class'] = 'js-wpv-filter-trigger ' . $class;
                    $options[ $display_value ]['#attributes']['style'] = $style;
					// Dependant stuff
					if ( $dependant || $counters ) {
						if ( $format ) {
							$display_value_formatted_name = str_replace( '%%NAME%%', $options[ $display_value ]['#title'], $format );
							$options[ $display_value ]['#title'] = $display_value_formatted_name;
						}
						$this_query = $WP_Views->get_query();
						if ( empty( $value ) && !is_numeric( $value ) && is_object( $this_query ) ) {
							if ( isset( $aux_query_count ) ) {
								$this_checker = $aux_query_count;
							} else {
								$this_checker = $this_query->found_posts;
							}
						} else {
							$meta_criteria_to_filter = array( $field_real_name => array( $value ) );
							$data = array();
							$data['list'] = $wpv_data_cache['post_meta'];
							$data['args'] = $meta_criteria_to_filter;
							$data['kind'] = $filter_check_type;
							$data['comparator'] = $comparator;
							if ( $counters ) {
								$data['count_matches'] = true;
							}
							$data['filter_full_list'] = $filter_full_list;
							$this_checker = wpv_list_filter_checker( $data );
						}
						if ( $counters ) {
							$display_value_formatted_counter = str_replace( '%%COUNT%%', $this_checker, $options[ $display_value ]['#title'] );
							$options[ $display_value ]['#title'] = $display_value_formatted_counter;
						}
						if ( !$this_checker
							&& ( !empty( $value ) || is_numeric( $value ) )
							&& ( !isset( $_GET[$url_param] ) || $_GET[$url_param] !== $value )
							&& $dependant )
						{
							$options[ $display_value ]['#disable'] = 'true';
							$options[ $display_value ]['#labelclass'] = 'wpv-parametric-disabled';
							if ( isset( $empty_action['radios'] ) && $empty_action['radios'] == 'hide' ) {
								unset( $options[ $display_value ] );
							}
						}
					}
				}
			}
			// Render the form content
			$element = wpv_form_control( array(
					'field' => array(
							'#type' => 'radios',
							'#id' => 'wpv_control_radio_' . $field,
							'#name' => $url_param,
							'#attributes' => array( 'style' => $style, 'class' => $class ),
							'#inline' => true,
							'#options' => $options,
							'#default_value' => isset( $_GET[ $url_param ] ) ? $_GET[ $url_param ] : '' ) ) );
							
            return $element;
		} else if ( $field_type == 'checkbox' ) {
            // Checkbox field
            // Populate the $checkbox_name with the wpv-control title attribute OR the field name itself
            if ( isset( $atts['title'] ) ) {
                $checkbox_name =  wpv_translate( $url_param . '_title', $title, false, 'View ' . $view_name );
            } else {
				// NOTE mmmmmm we seem to have translated this $field_options['name'] right above...
                $checkbox_name = wpv_translate( 'field ' . $field_options['name'] . ' name', $field_options['name'], false, 'plugin Types' );
            }
            
            $value = $field_options['data']['set_value'];
            $coming_value = '';
			if ( isset( $_GET[ $url_param ] ) && !empty( $_GET[ $url_param ] ) ) {
				$value = esc_attr( $_GET[ $url_param ] );
				$coming_value = esc_attr( $_GET[ $url_param ] );
			} else if ( isset( $_GET[ $url_param ] ) && is_numeric( $_GET[ $url_param ] ) ) {
				// this only happens when the value to store when checked is actually zero - nonsense
				$value = 0;
				$coming_value = 0;
			} else if ( empty( $_GET[ $url_param ] ) ) {
				unset( $_GET[ $url_param ] );
			}
			$attributes = array( 'style' => '', 'class' => 'js-wpv-filter-trigger' );
            $labelclass = '';
            $show_checkbox = true;
            // Dependant stuff
			if ( $dependant || $counters ) {
				if ( $format ) {
					$display_value_formatted_name = str_replace( '%%NAME%%', $checkbox_name, $format );
					$checkbox_name = $display_value_formatted_name;
				}
				$meta_criteria_to_filter = array( $field_real_name => array( $value ) );
				$data = array();
				$data['list'] = $wpv_data_cache['post_meta'];
				$data['args'] = $meta_criteria_to_filter;
				$data['kind'] = $filter_check_type;
				$data['comparator'] = $comparator;
				if ( $counters ) {
					$data['count_matches'] = true;
				}
				$data['filter_full_list'] = $filter_full_list;
				$this_checker = wpv_list_filter_checker( $data );
				if ( $counters ) {
					$display_value_formatted_count = str_replace( '%%COUNT%%', $this_checker, $checkbox_name );
					$checkbox_name = $display_value_formatted_count;
				}
				if ( !$this_checker && empty( $coming_value ) && $dependant ) {
					$attributes['#disabled'] = 'true';
					$labelclass = 'wpv-parametric-disabled';
					if ( isset( $empty_action['checkboxes'] ) && $empty_action['checkboxes'] == 'hide' ) {
						$show_checkbox = false;
					}
				}
			}
            if ( $show_checkbox ) {
				// Render the form content
                $attributes['class'] .= ' ' . $class;
                $attributes['style'] = $style;
                
				$element = wpv_form_control( array(
						'field' => array(
								'#type' => 'checkbox',
								'#id' => 'wpv_control_checkbox_' . $field,
								'#name' => $url_param,
								'#attributes' => $attributes,
								'#inline' => true,
								'#title' => $checkbox_name,
								'#labelclass' => $labelclass . ' ' . $label_class,
                                '#labelstyle' => $label_style,
								'#value' => $field_options['data']['set_value'],
								'#default_value' => 0 ) ) );
				if ( isset( $field_options['data']['save_empty'] ) && $field_options['data']['save_empty'] == 'yes' && $force_zero == 'true' ) {
					$attributes['class'] = '';
					$attributes['checked'] = 'checked';
					$element .= wpv_form_control( array(
							'field' => array(
									'#type' => 'hidden',
									'#id' => 'wpv_control_checkbox_' . $field . '_fakezero',
									'#name' => $url_param . '_fakezero',
									'#attributes' => $attributes,
									'#inline' => true,
									'#value' => 'yes',
									'#default_value' => 0 ) ) );
				}
			} else {
				$element = '';
			}
            return $element;
            
		} else if ( $field_type == 'checkboxes' ) {

            // Checkboxes field
            $defaults = array();
            $original_get = null;
            if ( isset( $_GET[ $url_param ] ) ) {
                $original_get = $_GET[ $url_param ];
                $defaults = $_GET[ $url_param ];
                if ( is_string( $defaults ) ) {
					$defaults = explode( ',',$defaults );
				}
                unset( $_GET[ $url_param ] );
            }
            $field_checkboxes_options = isset( $field_options['data']['options'] ) ? $field_options['data']['options'] : array();
            if ( isset( $field_checkboxes_options['default'] ) ) {
				// Remove the default option from the array because it breaks the loop below
				unset( $field_checkboxes_options['default'] );
			}
            foreach( $field_checkboxes_options as $key => $value ) {
                $display_value = wpv_translate( 'field '. $field_options['id'] .' option '. $key .' title', trim( $value['title'] ), false, 'plugin Types' );
                if ( _wpv_is_field_of_type( 'wpcf-' . $field, 'checkboxes' ) ) {
					$value = trim( $value['title'] );
				} else {
					$value = trim( $value['value'] );
                }
                
                $options[ $value ]['#name'] = $url_param . '[]';
                $options[ $value ]['#title'] = $display_value;
                $options[ $value ]['#value'] = $value;
                $options[ $value ]['#default_value'] = in_array( $value, $defaults );
                //$options[$value]['#inline'] = true;
                //$options[$value]['#after'] = '&nbsp;&nbsp;';
                $options[ $value ]['#attributes']['class'] = 'js-wpv-filter-trigger ' . $class;
                $options[ $value ]['#attributes']['style'] = $style;
                $options[ $value ]['#labelclass'] = $label_class;
                $options[ $value ]['#labelstyle'] = $label_style;
                // Dependant stuff
				if ( $dependant || $counters ) {
					if ( $format ) {
						$display_value_formatted_name = str_replace( '%%NAME%%', $options[ $value ]['#title'], $format );
						$options[ $value ]['#title'] = $display_value_formatted_name;
					}
					$meta_criteria_to_filter = array( $field_real_name => array( $value ) ); // TODO DONE IMPORTANT check what is coming here as value, maybe $opts['title'] sometimes
					$this_query = $WP_Views->get_query();
					if ( empty( $value ) && !is_numeric( $value ) && is_object( $this_query ) ) {
						if ( isset( $aux_query_count ) ) {
							$this_checker = $aux_query_count;
						} else {
							$this_checker = $this_query->found_posts;
						}
					} else {
						$data = array();
						$data['list'] = $wpv_data_cache['post_meta'];
						$data['args'] = $meta_criteria_to_filter;
						$data['kind'] = $filter_check_type;
						$data['comparator'] = $comparator;
						if ( $counters ) {
							$data['count_matches'] = true;
						}
						$data['filter_full_list'] = $filter_full_list;
						$this_checker = wpv_list_filter_checker( $data );
					}
					if ( $counters ) {
						$display_value_formatted = str_replace( '%%COUNT%%', $this_checker, $options[ $value ]['#title'] );
						$options[ $value ]['#title'] = $display_value_formatted;
					}
					if ( !$this_checker && ( !empty( $value ) || is_numeric( $value ) ) && !$options[ $value ]['#default_value'] && $dependant ) {
						$options[ $value ]['#attributes']['#disabled'] = 'true';
						$options[ $value ]['#labelclass'] .= ' wpv-parametric-disabled';
						if ( isset( $empty_action['checkboxes'] ) && $empty_action['checkboxes'] == 'hide' ) {
							unset( $options[ $value ] );
						}
					}
				}
            }
            // Render the form content
            $element = wpv_form_control( array(
					'field' => array(
                            '#type' => 'checkboxes',
                            '#id' => 'wpv_control_checkbox_' . $field,
                            '#name' => $url_param . '[]',
                            '#attributes' => array( 'style' => '' ),
                            '#inline' => true,
                            '#options' => $options ) ) );
            if ( $original_get ) {
                $_GET[ $url_param ] = $original_get;
            }
            return $element;
		} else if ( $field_type == 'select' ) {
			// Select field
			$field_select_options = isset( $field_options['data']['options'] ) ? $field_options['data']['options'] : array();;
			$options = array();
			$opt_aux = array();
			foreach ( $field_select_options as $key => $opts ) {
				if ( is_array( $opts ) ) {
					
					$display_value = wpv_translate( 'field '. $field_options['id'] .' option '. $key .' title', $opts['title'], false, 'plugin Types' );
					if ( _wpv_is_field_of_type( 'wpcf-' . $field, 'checkboxes' ) ) {
						$value = $opts['title'];
					} else {
						$value = $opts['value'];
					}
					
					$options[ $display_value ] = array(
						'#title' => $display_value,
						'#value' => $value,
						'#inline' => true,
						'#after' => '<br />'
                    );
                    $opt_aux[ $display_value ] = $value;
					$options[ $display_value ]['#attributes']['class'] = 'js-wpv-filter-trigger';
					// Dependant stuff
					if ( $dependant || $counters ) {
						if ( $format ) {
							$display_value_formatted_name = str_replace( '%%NAME%%', $options[ $display_value ]['#title'], $format );
							$options[ $display_value ]['#title'] = $display_value_formatted_name;
						}
						$this_query = $WP_Views->get_query();
						if ( empty( $value ) && !is_numeric( $value ) && is_object( $this_query ) ) {
							if ( isset( $aux_query_count ) ) {
								$this_checker = $aux_query_count;
							} else {
								$this_checker = $this_query->found_posts;
							}
						} else {
							$meta_criteria_to_filter = array( $field_real_name => array( $value ) );
							$data = array();
							$data['list'] = $wpv_data_cache['post_meta'];
							$data['args'] = $meta_criteria_to_filter;
							$data['kind'] = $filter_check_type;
							$data['comparator'] = $comparator;
							if ( $counters ) {
								$data['count_matches'] = true;
							}
							$data['filter_full_list'] = $filter_full_list;
							$this_checker = wpv_list_filter_checker( $data );
						}
						if ( $counters ) {
							$display_value_formatted_counter = str_replace( '%%COUNT%%', $this_checker, $options[ $display_value ]['#title'] );
							$options[ $display_value ]['#title'] = $display_value_formatted_counter;
						}
						if ( !$this_checker && ( !empty( $value ) || is_numeric( $value ) ) && $dependant ) {
							// TODO DONE we need to adjust this with the $default_value below
							$options[ $display_value ]['#disable'] = 'true';
							$options[ $display_value ]['#labelclass'] = 'wpv-parametric-disabled';
							if ( $multi == 'multiple' ) {
								if ( isset( $empty_action['multi-select'] ) && $empty_action['multi-select'] == 'hide' ) {
									unset( $options[ $display_value ] );
								}
							} else if ( isset( $empty_action['select'] ) && $empty_action['select'] == 'hide' ) {
								unset( $options[ $display_value ] );
							}
						}
					}
				}
			}
			$default_value = false;
			if ( isset( $_GET[ $url_param ] ) ) {
				if ( is_array( $_GET[ $url_param ] ) ) {
					if ( count( array_intersect($_GET[ $url_param ], $opt_aux) ) > 0 ) {
						$default_value = $_GET[ $url_param ];
					}
				} else {
					if ( in_array( $_GET[ $url_param ], $opt_aux ) ) {
						$default_value = $_GET[ $url_param ];
					}
				}
			}
			
			// Now we need to recreate the $options element if it is a default one and is disabled or removed
			if ( $default_value !== false && is_array( $default_value ) ) {
				foreach ( $default_value as $dv ) {
					$aux_display_values = array_keys( $opt_aux, $dv, true );
					foreach ( $aux_display_values as $aux_dv ) {
						if ( isset( $options[ $aux_dv ] ) ) {
							if ( isset( $options[ $aux_dv ]['#disable'] ) ) {
								unset( $options[ $aux_dv ]['#disable'] );
							}
							$options[ $aux_dv ]['#labelclass'] = '';
						} else {
							$options[ $aux_dv ] = array(
								'#title' => $aux_dv,
								'#value' => $dv,
								'#inline' => true,
								'#after' => '<br />'
							);
							$options[ $aux_dv ]['#attributes']['class'] = 'js-wpv-filter-trigger';
						}
					}
				}
			} else if ( $default_value !== false ) {
				$aux_display_values = array_keys( $opt_aux, $default_value, true );
				foreach ( $aux_display_values as $aux_dv ) {
					if ( isset( $options[ $aux_dv ] ) ) {
						if ( isset( $options[ $aux_dv ]['#disable'] ) ) {
							unset( $options[ $aux_dv ]['#disable'] );
						}
						$options[ $aux_dv ]['#labelclass'] = '';
					} else {
						$options[ $aux_dv ] = array(
							'#title' => $aux_dv,
							'#value' => $default_value,
							'#inline' => true,
							'#after' => '<br />'
						);
						$options[ $aux_dv ]['#attributes']['class'] = 'js-wpv-filter-trigger';
					}
				}
			}
			
			
			// Render the form content
			$element = wpv_form_control( array(
					'field' => array(
	                        '#type' => 'select',
	                        '#id' => 'wpv_control_select_' . $url_param,
	                        '#name' => $url_param . '[]',
	                        '#attributes' => array( 'style' => $style, 'class' => $class ),
	                        '#inline' => true,
							'#options' => $options,
							'#default_value' => $default_value,
							'#multiple' => $multi ) ) );
	        return $element;
	        
		} else if ( $field_type == 'textfield' ) {
			// Textfield field
			$default_value = '';
			if ( isset( $_GET[ $url_param ] ) ) {
				$default_value = stripslashes( urldecode( sanitize_text_field( $_GET[ $url_param ] ) ) );
			}
			
			// Render the form content
			$element = wpv_form_control( array(
					'field' => array(
	                        '#type' => 'textfield',
	                        '#id' => 'wpv_control_textfield_' . $url_param,
	                        '#name' => $url_param,
	                        '#attributes' => array( 'style' => $style, 'class' => 'js-wpv-filter-trigger-delayed ' . $class  ),
	                        '#inline' => true,
							'#value' => $default_value ) ) );
	        return $element;
	        
		} else if ( $field_type == 'date' || $field_type == 'datepicker' ) {
			// Date or datepicker field
			$out = wpv_render_datepicker( $url_param, $date_format, $default_date );
            return $out;
        }
        // In case we have a field attribute but it does not match any vaid type, return nothing
		return '';
		
	} else {
        // When there is a type attribute without field or auto_fill or values attributes it's likely for a checkbox or a datepicker
        // But I'm not sure what is this used for, because it really does not filter by any field
        $default_value = '';
        if ( isset( $_GET[ $url_param ] ) ) {
            $default_value = $_GET[ $url_param ];
        }
        switch ( $type ) {
            case 'checkbox':
                // In this case, there is no way to implement dependant parametric search, because we have no field to check against
                $element = array(
						'field' => array(
                                '#type' => $type,
                                '#id' => 'wpv_control_' . $type . '_' . $url_param,
                                '#name' => $url_param,
                                '#attributes' => array( 'style' => $style, 'class' => 'js-wpv-filter-trigger ' . $class ),
                                '#inline' => true,
                                '#value' => $default_value ) );
                $element['field']['#title'] = wpv_translate( $url_param . '_title', $title, false, 'View ' . $view_name );
                $element = wpv_form_control( $element );
                break;
            case 'datepicker':
                $element = wpv_render_datepicker( $url_param, $date_format, $default_date );
                break;
            default:
                $element = array(
						'field' => array(
                                '#type' => $type,
                                '#id' => 'wpv_control_' . $type . '_' . $url_param,
                                '#name' => $url_param,
                                '#attributes' => array( 'style' => $style, 'class' => $class ),
                                '#inline' => true,
                                '#value' => $default_value ) );
                $element = wpv_form_control( $element );
                break;
        }
        return $element;
    }
}

/**
* Auxiliar functions for the wpv-control shortcode
*/

/**
* _wpv_is_field_of_type
*
* Checks if a field is a Types field of a given type
*
* @note $field_name must start with the wpcf- prefix so for fields created outside Types and then under Types control you need to add it on origin
*
* @param $field_name (string) the field name
* @param $type (string) the Types type to check against
*
* @return (bool)
*
* @since unknown
*
*/

function _wpv_is_field_of_type($field_name, $type) {
    $opt = get_option('wpcf-fields');
    if( $opt ) {
        if ( strpos( $field_name, 'wpcf-' ) === 0 ) {
			$field_name = substr( $field_name, 5 );
        }
        if ( isset( $opt[$field_name] ) && is_array( $opt[$field_name] ) && isset( $opt[$field_name]['type'] ) ) {
            $field_type = strtolower( $opt[$field_name]['type'] );
            if ( $field_type == $type ) {
                return true;
            }
        }
    }
    return false;
}

/**
* _wpv_get_field_real_slug
*
* For a Types field,takes the field name and returns the field real meta_key
*
* @param $field_name (string) the field name
*
* @return (string)
*
* @since unknown
*
*/

function _wpv_get_field_real_slug($field_name) {
	$real_slug = $field_name;
	$opt = get_option('wpcf-fields');
    if($opt) {
        if ( isset( $opt[$field_name] ) && isset( $opt[$field_name]['meta_key'] ) ) {
            $real_slug = $opt[$field_name]['meta_key'];
        }
        
    }
	return $real_slug;
}

/**
* wpv_render_datepicker
*
* Renders the datepicker for date based frontend filters
*
* @param $url_param (string) the URL parameter used on the frontend filter
* @param $date_format (string) the date format to use when displaying the selected date besides the datepicker
* @param $default_date (string) the default date to be used when there is no value passed by the URL parameter - special case NONE
*
* @return (string) containing the needed inputs
*
* @since unknown
*
* @note $default_date default value was changed in 1.7 from NOW() to NONE; empty will mean NONE too
*
* @todo add an attribute for themes http://rtsinani.github.io/jquery-datepicker-skins/ 
* OR better an option in the Views settings, because we can not have two different styles for two datepickers
*/

function wpv_render_datepicker( $url_param, $date_format, $default_date = '' ) {
    
    static $support_loaded = false;
	$display_date = $datepicker_date = '';
	// TODO we need to enqueue this style in the right way. We are loading it twice! And it can not be unregistered...
    if ( !$support_loaded && 1 === 0 ) {
        ?>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery('head').append('<link rel="stylesheet" href="<?php echo WPV_URL_EMBEDDED . '/common/toolset-forms/css/wpt-jquery-ui/datepicker.css';?>" type="text/css" />');
                });
            </script>
        <?php
        $support_loaded = true;
    }
    
    if ( $date_format == '' ) {
        $date_format = get_option( 'date_format' );
    }
	
	$clear_button_style = '';
	$date = '';
    
    if( isset( $_GET[$url_param] ) ) {
        if ( $_GET[$url_param] == '' || $_GET[$url_param] == '0' ) {
			$date = '';
		} else {
			$date = $_GET[$url_param];
		}
    } else {
		if ( 
			$default_date == '' 
			|| $default_date == 'NONE' 
		) {
			$date = '';
		} else {
			$date = wpv_filter_parse_date( $default_date );
		}
    }
	
	if ( is_numeric( $date ) ) {
		if (
			$date < -12219292800 
			|| $date > 32535215940
		) {
			$date = '';
		}
	} else {
		$date = '';
	}
	
	if ( $date != '' ) {
    	$display_date = adodb_date( $date_format, $date );
	} else {
		$clear_button_style = ' style="display:none"';
	}

    
    $out = '';
    $out .= '<span class="wpv_date_input js-wpv-date-param-' . $url_param . ' js-wpv-date-display" data-param="' . $url_param . '">' . $display_date . '</span> ';
    $out .= '<input type="hidden" class="js-wpv-date-param-' . $url_param . '-value js-wpv-filter-trigger" name="' . $url_param . '" value="' . $date . '" />';
    $out .= '<input type="hidden" class="js-wpv-date-param-' . $url_param . '-format" name="' . $url_param . '-format" value="' . $date_format . '" />';

	if ( $date != '' ) {
    	$datepicker_date = adodb_date( 'dmY', $date );
	}
    $out .= '<input type="hidden" data-param="' . $url_param . '" class="wpv-date-front-end js-wpv-frontend-datepicker js-wpv-date-front-end-' . $url_param . '" value="' . $datepicker_date . '"/>';
	
	$delete_date_image = WPV_URL_EMBEDDED_FRONTEND . '/res/img/delete.png';
	$delete_date_image = apply_filters( 'wpv_filter_wpv_delete_date_image', $delete_date_image );
	$delete_date_image = apply_filters( 'wptoolset_filter_wptoolset_delete_date_image', $delete_date_image );
	
	$out .= '<img src="' . $delete_date_image . '" title="' . esc_attr( __( 'Clear date', 'wpv-views' ) ) . '" alt="' . esc_attr( __( 'Clear date', 'wpv-views' ) ) . '" class="wpv-date-front-end-clear js-wpv-date-front-end-clear js-wpv-date-front-end-clear-' . $url_param . '" data-param="' . $url_param . '"' . $clear_button_style . ' />';

    return $out;
}

/**
* Custom Walkers used in taxonomy parametric searches
*/

/**
* Walker_Category_select
*
* Walker to return select or multi-select options when walking taxonomies
*
* @param $selected_id (int|array) selected term or array of selected terms
* @param $slug_mode (bool) true uses term slugs, false uses term names
* @param $format (string|false) structure of the option label, use %%NAME%% or %%COUNT%% as placeholders
* @param $taxonomy (string) relevant taxonomy
*
* @since unknown
*/

class Walker_Category_select extends Walker {
	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); //TODO: decouple this

    function __construct($selected_id, $slug_mode = false, $format = false, $taxonomy = 'category', $type = 'select' ){
		global $WP_Views;
		$this->selected = $selected_id;
        $this->slug_mode = $slug_mode;
        $this->format = $format;
		$this->counters = ( $this->format && strpos( $this->format, '%%COUNT%%' ) !== false ) ? true : false;
        $view_settings = $WP_Views->get_view_settings();
        $this->dependant = false;
        $this->empty_action = 'none';
        if ( isset( $view_settings['dps'] ) && is_array( $view_settings['dps'] ) && isset( $view_settings['dps']['enable_dependency'] ) && $view_settings['dps']['enable_dependency'] == 'enable' ) {
			$this->dependant = true;
			$force_disable_dependant = $WP_Views->get_force_disable_dependant_parametric_search();
			if ( $force_disable_dependant ) {
				$this->dependant = false;
			} else {
				$this->empty_action = 'hide';
				if ( $type == 'select' && isset( $view_settings['dps']['empty_select'] ) && $view_settings['dps']['empty_select'] == 'disable' ) {
					$this->empty_action = 'disable';
				} else if ( $type == 'multi-select' && isset( $view_settings['dps']['empty_multi_select'] ) && $view_settings['dps']['empty_multi_select'] == 'disable' ) {
					$this->empty_action = 'disable';
				}
			}
		}
		$this->show = true;
		$this->posts_to_taxes = array();
		if ( $this->dependant || $this->counters ) {
			$operator = isset( $view_settings['taxonomy-' . $taxonomy . '-attribute-operator'] ) ? $view_settings['taxonomy-' . $taxonomy . '-attribute-operator'] : 'IN';
			// Construct $this->posts_to_taxes
			if ( empty( $this->selected ) || $this->selected === 0 || ( is_array( $this->selected ) && in_array( (string) 0, $this->selected ) )  || ( $type == 'multi-select' && $operator == 'AND' ) ) {
				// This is when there is no non-default selected
				global $wp_object_cache;
				$wpv_data_cache = isset( $wp_object_cache->cache ) ? $wp_object_cache->cache : array();
				if ( isset( $wpv_data_cache[$taxonomy . '_relationships'] ) && is_array( $wpv_data_cache[$taxonomy . '_relationships'] ) ) {
					foreach ( $wpv_data_cache[$taxonomy . '_relationships'] as $pid => $tax_array ) {
						if ( is_multisite() ) {
							$blog_id = get_current_blog_id();
							$pid = str_replace( $blog_id . ':', '', $pid );
						}
						if ( in_array( $pid, $WP_Views->returned_ids_for_parametric_search ) && is_array( $tax_array ) && count( $tax_array ) > 0 ) {
							//$this_post_taxes = array_combine( array_values( array_keys( $tax_array ) ) , array_keys( $tax_array ) );
							$this_post_taxes = wp_list_pluck( $tax_array, 'term_id', 'term_id' );
							$this->posts_to_taxes[$pid] = $this_post_taxes;
						}
					}
				}
			} else {
				// When there is a selected value, create a pseudo-cache based on all the other filters
				$query = wpv_get_dependant_view_query_args();
				$aux_cache_query = null;
				if ( isset( $query['tax_query'] ) && is_array( $query['tax_query'] ) ) {
					foreach ( $query['tax_query'] as $qt_index => $qt_val ) {
						if ( is_array( $qt_val ) && isset( $qt_val['taxonomy'] ) && $qt_val['taxonomy'] == $taxonomy ) {
							unset( $query['tax_query'][$qt_index] );
						}
					}
				}
				$aux_cache_query = new WP_Query($query);
				if ( is_array( $aux_cache_query->posts ) && !empty( $aux_cache_query->posts ) ) {
					$f_taxes = array( $taxonomy );
					$wpv_data_cache = wpv_custom_cache_metadata( $aux_cache_query->posts, array( 'tax' => $f_taxes ) );
					if ( isset( $wpv_data_cache[$taxonomy . '_relationships'] ) && is_array( $wpv_data_cache[$taxonomy . '_relationships'] ) ) {
						foreach ( $wpv_data_cache[$taxonomy . '_relationships'] as $pid => $tax_array ) {
							if ( is_array( $tax_array ) && count( $tax_array ) > 0 ) {
								//$this_post_taxes = array_combine( array_values( array_keys( $tax_array ) ) , array_keys( $tax_array ) );
								$this_post_taxes = wp_list_pluck( $tax_array, 'term_id', 'term_id' );
								$this->posts_to_taxes[$pid] = $this_post_taxes;
							}
						}
					}
				}
			}
		}
	}
	
	function start_lvl(&$output, $depth = 0, $args = array()) {
	}

	function end_lvl(&$output, $depth = 0, $args = array()) {
	}

	function start_el(&$output, $category, $depth = 0, $args = array(), $current_object_id = 0) {
		extract($args);
		$selected = '';
		
		$indent = str_repeat('-', $depth);
		if ($indent != '') {
			$indent = '&nbsp;' . str_repeat('&nbsp;', $depth) . $indent;
		}
		
		$tax_option = $category->name;
		if ($this->format) {
			$tax_option = str_replace( '%%NAME%%', $category->name, $this->format );
		}
		
		$this->show = true;
		if ( $this->dependant || $this->counters ) {
			$wpv_tax_criteria_matching_posts = array();
			$wpv_tax_criteria_to_filter = array($category->term_id => $category->term_id);
			$wpv_tax_criteria_matching_posts = wp_list_filter($this->posts_to_taxes, $wpv_tax_criteria_to_filter);
			if ( count( $wpv_tax_criteria_matching_posts ) == 0 && $this->dependant ) {
				$this->show = false;
			}
			if ( $this->counters ) {
				$tax_option = str_replace( '%%COUNT%%', count( $wpv_tax_criteria_matching_posts ), $tax_option );
			}
		}
		
		$real_value = $category->name;
		if ( $this->slug_mode ) {
			$real_value = $category->slug;
		}
		
		if ( is_array( $this->selected ) ) {
			foreach( $this->selected as $sel ) {
				$selected .= $sel == $real_value ? ' selected="selected"' : '';
			}
		} else {
			$selected .= $this->selected == $real_value ? ' selected="selected"' : '';
		}
		
		if ( $this->show || !empty( $selected ) ) {
			$output .= '<option value="' . $real_value . '"' . $selected . '>' . $indent . $tax_option . "</option>\n";
		} else if ( $this->empty_action != 'hide' ) {
			$output .= '<option value="' . $real_value . '"' . $selected . ' disabled="disabled">' . $indent . $tax_option . "</option>\n";
		}
	}

	function end_el(&$output, $category, $depth = 0, $args = array()) {
	}
}

/**
* Walker_Category_radios
*
* Walker to return radios when walking taxonomies
*
* @param $selected_id (int|array) selected term (or array of selected terms although this does not allow for multiple selected items, just in case)
* @param $slug_mode (bool) true uses term slugs, false uses term names
* @param $format (string|false) structure of the option label
*    use %%NAME%% or %%COUNT%% as placeholders
* @param $taxonomy (string) relevant taxonomy
*
* @since unknown
*/

class Walker_Category_radios extends Walker {
	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); //TODO: decouple this

    function __construct($selected_id, $slug_mode = false, $format = false, $taxonomy = 'category', $style = '', $class = '', $label_style = '', $label_class = ''){
		global $WP_Views;
		$this->selected = $selected_id;
        $this->slug_mode = $slug_mode;
        $this->format = $format;
        $this->style = $style;
        $this->input_class = $class;
        $this->label_style = $label_style;
        $this->label_class = $label_class;
		$this->counters = ( $this->format && strpos( $this->format, '%%COUNT%%' ) !== false ) ? true : false;
        $view_settings = $WP_Views->get_view_settings();
        $this->dependant = false;
        $this->empty_action = 'none';
        if ( isset( $view_settings['dps'] ) && is_array( $view_settings['dps'] ) && isset( $view_settings['dps']['enable_dependency'] ) && $view_settings['dps']['enable_dependency'] == 'enable' ) {
			$this->dependant = true;
			$force_disable_dependant = $WP_Views->get_force_disable_dependant_parametric_search();
			if ( $force_disable_dependant ) {
				$this->dependant = false;
			} else {
				$this->empty_action = 'hide';
				if ( isset( $view_settings['dps']['empty_radios'] ) && $view_settings['dps']['empty_radios'] == 'disable' ) {
					$this->empty_action = 'disable';
				}
			}
		}
		$this->show = true;
		$this->posts_to_taxes = array();
		if ( $this->dependant || $this->counters  ) {
			// Construct $this->posts_to_taxes
			if ( empty( $this->selected ) || $this->selected === 0 || ( is_array( $this->selected ) && in_array( (string) 0, $this->selected ) ) ) {
				// This is when there is no non-default selected
				global $wp_object_cache;
				$wpv_data_cache = isset( $wp_object_cache->cache ) ? $wp_object_cache->cache : array();
				if ( isset( $wpv_data_cache[$taxonomy . '_relationships'] ) && is_array( $wpv_data_cache[$taxonomy . '_relationships'] ) ) {
					foreach ( $wpv_data_cache[$taxonomy . '_relationships'] as $pid => $tax_array ) {
						if ( is_multisite() ) {
							$blog_id = get_current_blog_id();
							$pid = str_replace( $blog_id . ':', '', $pid );
						}
						if ( in_array( $pid, $WP_Views->returned_ids_for_parametric_search ) && is_array( $tax_array ) && count( $tax_array ) > 0 ) {
							//$this_post_taxes = array_combine( array_values( array_keys( $tax_array ) ) , array_keys( $tax_array ) );
							$this_post_taxes = wp_list_pluck( $tax_array, 'term_id', 'term_id' );
							$this->posts_to_taxes[$pid] = $this_post_taxes;
						}
					}
				}
			} else {
				// When there is a selected value, create a pseudo-cache based on all the other filters
				$query = wpv_get_dependant_view_query_args();
				$aux_cache_query = null;
				if ( isset( $query['tax_query'] ) && is_array( $query['tax_query'] ) ) {
					foreach ( $query['tax_query'] as $qt_index => $qt_val ) {
						if ( is_array( $qt_val ) && isset( $qt_val['taxonomy'] ) && $qt_val['taxonomy'] == $taxonomy ) {
							unset( $query['tax_query'][$qt_index] );
						}
					}
				}
				$aux_cache_query = new WP_Query($query);
				if ( is_array( $aux_cache_query->posts ) && !empty( $aux_cache_query->posts ) ) {
					$f_taxes = array( $taxonomy );
					$wpv_data_cache = wpv_custom_cache_metadata( $aux_cache_query->posts, array( 'tax' => $f_taxes ) );
					if ( isset( $wpv_data_cache[$taxonomy . '_relationships'] ) && is_array( $wpv_data_cache[$taxonomy . '_relationships'] ) ) {
						foreach ( $wpv_data_cache[$taxonomy . '_relationships'] as $pid => $tax_array ) {
							if ( is_array( $tax_array ) && count( $tax_array ) > 0 ) {
								//$this_post_taxes = array_combine( array_values( array_keys( $tax_array ) ) , array_keys( $tax_array ) );
								$this_post_taxes = wp_list_pluck( $tax_array, 'term_id', 'term_id' );
								$this->posts_to_taxes[$pid] = $this_post_taxes;
							}
						}
					}
				}
			}
		}
	}
	
	function start_lvl(&$output, $depth = 0, $args = array()) {
	}

	function end_lvl(&$output, $depth = 0, $args = array()) {
	}

	function start_el(&$output, $category, $depth = 0, $args = array(), $current_object_id = 0) {
		extract($args);
		$selected = '';
		
		if ( empty($taxonomy) )
            $taxonomy = 'category';
 
        if ( $taxonomy == 'category' ) {
            $name = 'post_category';
        } else {
            $name = $taxonomy;
		}
		
		$indent = str_repeat('-', $depth);
		if ( $indent != '' ) {
			$indent = '&nbsp;' . str_repeat('&nbsp;', $depth) . $indent;
		}
		
		$tax_option = $category->name;
		if ( $this->format ) {
			$tax_option = str_replace( '%%NAME%%', $category->name, $this->format );
		}
		
		$this->show = true;
		if ( $this->dependant || $this->counters ) {
			$wpv_tax_criteria_matching_posts = array();
			$wpv_tax_criteria_to_filter = array($category->term_id => $category->term_id);
			$wpv_tax_criteria_matching_posts = wp_list_filter($this->posts_to_taxes, $wpv_tax_criteria_to_filter);
			if ( count( $wpv_tax_criteria_matching_posts ) == 0 && $this->dependant ) {
				$this->show = false;
			}
			if ( $this->counters ) {
				$tax_option = str_replace( '%%COUNT%%', count( $wpv_tax_criteria_matching_posts ), $tax_option );
			}
		}
		
		$tmp = is_array( $this->selected ) ? $this->selected[0] : $this->selected;
		$real_value = $category->name;
		if ( $this->slug_mode ) {
			$real_value = $category->slug;
		}
		$selected .= ( $tmp == $real_value ) ? ' checked' : '';
		
		if ( $this->show || !empty( $selected ) ) {
			$output .= '<input id="' . $name . '-'. $category->slug . '"'.( !empty($this->style) ? ' style="' . $this->style . '"' : '' ).' class="js-wpv-filter-trigger'. ( !empty($this->input_class) ? ' '. $this->input_class : '' ) .'" name="'.$name.'" type="radio" value="' . $real_value . '"' . $selected . '/>
            <label for="' . $name . '-'. $category->slug . '"'. ( !empty($this->label_style) ? ' style="' . $this->label_style . '"' : '' ) .' class="radios-taxonomies-title'. ( !empty($this->label_class) ? ' '. $this->label_class : '' ) .'">' . $indent . $tax_option . '</label>';
		} else if ( $this->empty_action != 'hide' ) {
			$output .= '<input id="' . $name . '-'. $category->slug . '"'.( !empty($this->style) ? ' style="' . $this->style . '"' : '' ).' class="js-wpv-filter-trigger'. ( !empty($this->input_class) ? ' '. $this->input_class : '' ) .'" name="'.$name.'" type="radio" value="' . $real_value . '"' . $selected . ' disabled="disabled" />
            <label for="' . $name . '-'. $category->slug . '"'. ( !empty($this->label_style) ? ' style="' . $this->label_style . '"' : '' ) .' class="radios-taxonomies-title wpv-parametric-disabled'. ( !empty($this->label_class) ? ' '. $this->label_class : '' ) .'">' . $indent . $tax_option . '</label>';
		}
	}

	function end_el(&$output, $category, $depth = 0, $args = array()) {
		if ( $this->show ) {
			$output .= "\n";
		}
	}
}

/**
* Walker_Category_id_select
*
* Walker to return radios when walking taxonomies
*
* @param $selected_id (int|array) selected term (or array of selected terms although this does not allow for multiple selected items, just in case)
*
* @note check where is this used (I think on the backend) and why it has only one @param
*
* @since unknown
*/

class Walker_Category_id_select extends Walker {
	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); //TODO: decouple this

    function __construct($selected_id){
		$this->selected = $selected_id;
	}
	
	function start_lvl(&$output, $depth = 0, $args = array()) {
	}

	function end_lvl(&$output, $depth = 0, $args = array()) {
	}

	function start_el(&$output, $category, $depth = 0, $args = array(), $current_object_id = 0) {
		extract($args);
		
		$indent = str_repeat('-', $depth);
		if ($indent != '') {
			$indent = '&nbsp;' . str_repeat('&nbsp;', $depth) . $indent;
		}
		
        $selected = $this->selected == $category->term_id ? ' selected="selected"' : '';
    		$output .= '<option value="' . $category->term_id. '"' . $selected . '>' . $indent . $category->name . "</option>\n";
	}

	function end_el(&$output, $category, $depth = 0, $args = array()) {
	}
}

if ( !class_exists( 'WPV_Walker_Category_Checklist' ) ) {
    
	/**
	* WPV_Walker_Category_Checklist
	*
	* Walker to return checkboxes when walking taxonomies
	*
	* @param $slug_mode (bool) true uses term slugs, false uses term names
	* @param $format (string|false) structure of the option label
	*    use %%NAME%% or %%COUNT%% as placeholders
	* @param $taxonomy (string) relevant taxonomy
	*
	* @since unknown
	*/

	class WPV_Walker_Category_Checklist extends Walker {
		var $tree_type = 'category';
		var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); //TODO: decouple this
							
		function __construct($slug_mode = false, $format = false, $taxonomy = 'category', $selected_cats = array(), $style = '', $class = '', $label_style = '', $label_class = '' ) {
			global $WP_Views;
			$this->slug_mode = $slug_mode;
			$this->format = $format;
			$this->counters = ( $this->format && strpos( $this->format, '%%COUNT%%' ) !== false ) ? true : false;
			$view_settings = $WP_Views->get_view_settings();
			$this->dependant = false;
			$this->empty_action = 'none';
            $this->style = $style;
            $this->input_class = $class;
            $this->label_style = $label_style;
            $this->label_class = $label_class;
			if ( isset( $view_settings['dps'] ) && is_array( $view_settings['dps'] ) && isset( $view_settings['dps']['enable_dependency'] ) && $view_settings['dps']['enable_dependency'] == 'enable' ) {
				$this->dependant = true;
				$force_disable_dependant = $WP_Views->get_force_disable_dependant_parametric_search();
				if ( $force_disable_dependant ) {
					$this->dependant = false;
				} else {
					$this->empty_action = 'hide';
					if ( isset( $view_settings['dps']['empty_checkboxes'] ) && $view_settings['dps']['empty_checkboxes'] == 'disable' ) {
						$this->empty_action = 'disable';
					}
				}
			}
			$this->show = true;
			$this->posts_to_taxes = array();
			if ( $this->dependant || $this->counters ) {
				// Construct $this->posts_to_taxes
				$operator = isset( $view_settings['taxonomy-' . $taxonomy . '-attribute-operator'] ) ? $view_settings['taxonomy-' . $taxonomy . '-attribute-operator'] : 'IN';
				if ( empty( $selected_cats ) || $operator == 'AND' ) {
					// This is when there is no non-default selected
					global $wp_object_cache;
					$wpv_data_cache = isset( $wp_object_cache->cache ) ? $wp_object_cache->cache : array();
					if ( isset( $wpv_data_cache[$taxonomy . '_relationships'] ) && is_array( $wpv_data_cache[$taxonomy . '_relationships'] ) ) {
						foreach ( $wpv_data_cache[$taxonomy . '_relationships'] as $pid => $tax_array ) {
							if ( is_multisite() ) {
								$blog_id = get_current_blog_id();
								$pid = str_replace( $blog_id . ':', '', $pid );
							}
							if ( in_array( $pid, $WP_Views->returned_ids_for_parametric_search ) && is_array( $tax_array ) && count( $tax_array ) > 0 ) {
								$this_post_taxes = wp_list_pluck( $tax_array, 'term_id', 'term_id' );
								$this->posts_to_taxes[$pid] = $this_post_taxes;
							}
						}
					}
				} else {
					// When there is a selected value, create a pseudo-cache based on all the other filters
					$query = wpv_get_dependant_view_query_args();
					$aux_cache_query = null;
					if ( isset( $query['tax_query'] ) && is_array( $query['tax_query'] ) ) {
						foreach ( $query['tax_query'] as $qt_index => $qt_val ) {
							if ( is_array( $qt_val ) && isset( $qt_val['taxonomy'] ) && $qt_val['taxonomy'] == $taxonomy ) {
								unset( $query['tax_query'][$qt_index] );
							}
						}
					}
					$aux_cache_query = new WP_Query($query);
					if ( is_array( $aux_cache_query->posts ) && !empty( $aux_cache_query->posts ) ) {
						$f_taxes = array( $taxonomy );
						$wpv_data_cache = wpv_custom_cache_metadata( $aux_cache_query->posts, array( 'tax' => $f_taxes ) );
						if ( isset( $wpv_data_cache[$taxonomy . '_relationships'] ) && is_array( $wpv_data_cache[$taxonomy . '_relationships'] ) ) {
							foreach ( $wpv_data_cache[$taxonomy . '_relationships'] as $pid => $tax_array ) {
								if ( is_array( $tax_array ) && count( $tax_array ) > 0 ) {
									$this_post_taxes = wp_list_pluck( $tax_array, 'term_id', 'term_id' );
									$this->posts_to_taxes[$pid] = $this_post_taxes;
								}
							}
						}
					}
				}
			}
		}
		
		function start_lvl(&$output, $depth = 0, $args = array() ) {
			$indent = str_repeat("\t", $depth);
			$output .= "$indent<ul class='children'>\n";
					//$output .= "$indent\n";
		}
		
		function end_lvl(&$output, $depth = 0, $args = array() ) {
			$indent = str_repeat("\t", $depth);
			$output .= "$indent</ul>\n";
					//$output .= "$indent\n";
		}
		
		function start_el(&$output, $category, $depth = 0, $args = array(), $current_object_id = 0 ) {
			extract($args);
			if ( empty($taxonomy) )
				$taxonomy = 'category';
		
			if ( $taxonomy == 'category' )
				$name = 'post_category';
			else
				$name = $taxonomy;
			
			$tax_option = esc_html( apply_filters('the_category', $category->name ));
			if ($this->format) {
				$tax_option = str_replace( '%%NAME%%', $category->name, $this->format );
			}
			
			$this->show = true;
			if ( $this->dependant || $this->counters ) {
				$wpv_tax_criteria_matching_posts = array();
				$wpv_tax_criteria_to_filter = array($category->term_id => $category->term_id);
				// $criteria_real = array_combine( array_values( $selected_cats ) , array_values( $selected_cats ) );
				$wpv_tax_criteria_matching_posts = wp_list_filter($this->posts_to_taxes, $wpv_tax_criteria_to_filter);
				if ( count( $wpv_tax_criteria_matching_posts ) == 0 && $this->dependant ) {
					$this->show = false;
				}
				if ( $this->counters ) {
					$tax_option = str_replace( '%%COUNT%%', count( $wpv_tax_criteria_matching_posts ), $tax_option );
				}
			}
			
			if ( $this->slug_mode ) {
				$real_value = $category->slug;
			} else {
				$real_value = $category->name;
			}
			
			$class = in_array( $category->term_id, $popular_cats ) ? ' class="popular-category"' : '';
			
			// NOTE: were outputing the "slug" and not the "term-id".
			// WP outputs the "term-id"
			if ( $this->show || in_array( $real_value, $selected_cats ) ) {
				$output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" . '<label class="selectit'. ( !empty($this->label_class) ? ' '. $this->label_class : '' ) .'"'. ( !empty($this->label_style) ? ' style="' . $this->label_style . '"' : '' ) .'><input'. ( !empty($this->style) ? ' style="' . $this->style . '"' : '' ) .' value="' . $real_value . '" type="checkbox" class="js-wpv-filter-trigger'. ( !empty($this->input_class) ? ' '. $this->input_class : '' ) .'" name="'.$name.'[]" id="in-'.$taxonomy.'-' . $category->term_id . '"' . checked( in_array( $real_value, $selected_cats ), true, false ) . disabled( empty( $args['disabled'] ), false, false ) . ' /> ' . $tax_option . '</label>';
			} else if ( $this->empty_action != 'hide' ) {
				$disabled = '';
				$disabled_class = '';
				if ( !in_array( $real_value, $selected_cats ) ) {
					$disabled = ' disabled="disabled"';
					$disabled_class = ' wpv-parametric-disabled';
					$args['disabled'] = 'disabled';
				}
				$output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" . '<label class="selectit' . $disabled_class . ( !empty($this->label_class) ? ' '. $this->label_class : '' ) .'"'. ( !empty($this->label_style) ? ' style="' . $this->label_style . '"' : '' ) .'><input'. ( !empty($this->style) ? ' style="' . $this->style . '"' : '' ) .' value="' . $real_value . '" type="checkbox" class="js-wpv-filter-trigger'. ( !empty($this->input_class) ? ' '. $this->input_class : '' ) .'" name="'.$name.'[]" id="in-'.$taxonomy.'-' . $category->term_id . '"' . checked( in_array( $real_value, $selected_cats ), true, false ) . disabled( empty( $args['disabled'] ), false, false ) . ' /> ' . $tax_option . '</label>';
			}
		}
		
		function end_el(&$output, $category, $depth = 0, $args = array()) {
			if ( $this->show ) {
				$output .= "</li>\n";
			}
		}
	}
}

function wpv_render_taxonomy_control( $atts ) {

    // We need to know what attribute url format are we using
    // to make the control filter use values of names or slugs for values.
    // If using names, $url_format=false and if using slugs, $url_format=true
	//printf('$taxonomy %s, $type %s, $url_param %s, $default_label %s, $taxonomy_orderby %s, $taxonomy_order %s, $format %s', $taxonomy, $type, $url_param, $default_label, $taxonomy_orderby, $taxonomy_order, $format);
    global $WP_Views;
    
    extract(
		shortcode_atts(array(
				'taxonomy' => '',
				'type' => '',
				'url_param' => '',
				'default_label' => '',
				'taxonomy_orderby' => '',
				'taxonomy_order' => '',
				'format' => '',
				'hide_empty' => '',
                'style' => '', // input inline styles
                'class' => '', // input classes
                'label_style' => '', // inline styles for input label
                'label_class' => '' // classes for input label
			), $atts)
	);
    
    if ( !taxonomy_exists( $taxonomy ) ) {
		return;
    }
    
	$url_format = true;
    
    $view_settings = $WP_Views->get_view_settings();
    
    if (isset($view_settings['taxonomy-'. $taxonomy .'-attribute-url-format']) && 'slug' != $view_settings['taxonomy-'.$taxonomy . '-attribute-url-format'][0]) $url_format = false;
    
    $terms = array();
    $get_value = ( $hide_empty == 'true' ) ? '' : 'all';
    $default_selected = '';
    
    if (isset($_GET[$url_param])) {
        if (is_array($_GET[$url_param])) {
            $terms = $_GET[$url_param];
        } else {
            // support csv terms
            $terms = explode(',', $_GET[$url_param]);
        }
    }    
    
    ob_start();
    ?>
		<?php
            if ( $type == 'select' || $type == 'multi-select' ) {
                $name = $taxonomy;
                if ( $name == 'category' ) {
                    $name = 'post_category';
                }
                
				if ( $type == 'select' ) {
					echo '<select name="' . $name . '"'. ( !empty($style) ? ' style="' . $style . '"' : '' ) .' class="js-wpv-filter-trigger'. ( !empty($class) ? ' '. $class : '' ) .'">';// maybe it is $name and not $taxonomy, and maybe slug-**; we need to add the influencers here
					if ( empty( $terms ) || in_array( (string) 0, $terms ) ) {
						$default_selected = " selected='selected'";
					}
					// TODO we do not add counters nor any format here, as we do for custom fields. WE might need to review this.
					echo '<option' . $default_selected . ' value="0">' . $default_label . '</option>'; // set the label for the default option
				} else if ( $type == 'multi-select' ) {
					echo '<select name="' . $name . '[]" multiple="multiple"'. ( !empty($style) ? ' style="' . $style . '"' : '' ) .' class="js-wpv-filter-trigger'. ( !empty($class) ? ' '. $class : '' ) .'" size="10">';
				}
				$temp_slug = '0';
				if ( count( $terms ) ) {
					$temp_slug = $terms;
				}
				$my_walker = new Walker_Category_select($temp_slug, $url_format, $format, $taxonomy, $type);
				wpv_terms_checklist(0, array('taxonomy' => $taxonomy, 'selected_cats' => $terms, 'walker' => $my_walker, 'taxonomy_orderby' => $taxonomy_orderby, 'taxonomy_order' => $taxonomy_order, 'get_value' => $get_value));
                echo '</select>';
            } 
			elseif ($type == 'radios' || $type == 'radio' ) {

			    $name = $taxonomy;
			    if ($name == 'category') {
			        $name = 'post_category';
			    }
				
			    $temp_slug = '0';
			    if ( count( $terms ) ) {
			        $temp_slug = $terms;
			    }
			    if ( isset( $default_label ) ) {
					if ( empty( $terms ) || in_array( (string) 0, $terms ) ) {
						$default_selected = " checked='checked'";
					}
					echo '<input id="' . $name . '-"'. ( !empty($style) ? ' style="' . $style . '"' : '' ) .' class="js-wpv-filter-trigger'. ( !empty($class) ? ' '. $class : '' ) .'" name="'.$name.'" type="radio" value="0"' . $default_selected . '/>
                    <label for="' . $name . '-"'. ( !empty($label_style) ? ' style="' . $label_style . '"' : '' ) .' class="radios-taxonomies-title'. ( !empty($label_class) ? ' '. $label_class : '' ) .'">' . $default_label . '</label>';
				}
				$my_walker = new Walker_Category_radios($temp_slug, $url_format, $format, $taxonomy, $style, $class, $label_style, $label_class);
			    wpv_terms_checklist(0, array('taxonomy' => $taxonomy, 'selected_cats' => $terms, 'walker' => $my_walker, 'taxonomy_orderby' => $taxonomy_orderby, 'taxonomy_order' => $taxonomy_order, 'get_value' => $get_value));
			} else {
				echo '<ul class="categorychecklist form-no-clear">';
			    wpv_terms_checklist(0, array('taxonomy' => $taxonomy, 'selected_cats' => $terms, 'url_format' => $url_format, 'format' => $format, 'taxonomy_orderby' => $taxonomy_orderby, 'taxonomy_order' => $taxonomy_order, 'get_value' => $get_value, 'style' => $style, 'class' => $class, 'label_style' => $label_style, 'label_class' => $label_class ));
				echo '</ul>';
			}
            
        ?>
		
    <?php
    
    $taxonomy_check_list = ob_get_clean();
    
    if ($taxonomy == 'category') {
        $taxonomy_check_list = str_replace('name="post_category', 'name="' . $url_param, $taxonomy_check_list);
    } else {
        $taxonomy_check_list = str_replace('name="' . $taxonomy, 'name="' . $url_param, $taxonomy_check_list);
    }
    
    return $taxonomy_check_list;
    
}

/**
* Taxonomy independent version of wp_category_checklist
*
* @since 3.0.0
*
* @param int $post_id
* @param array $args
*/
if ( !function_exists( 'wpv_terms_checklist' ) ) {
	function wpv_terms_checklist( $post_id = 0, $args = array() ) {
		$defaults = array(
			'descendants_and_self' => 0,
			'selected_cats' => false,
			'popular_cats' => false,
			'walker' => null,
			'url_format' => false,
			'format' => false,
			'taxonomy' => 'category',
			'taxonomy_orderby' => 'name',
			'taxonomy_order' => 'ASC',
			'checked_ontop' => false,
			'get_value' => 'all',
			'classname' => '',
            'style' => '',
            'class' => '',
            'label_class' => '',
            'label_style' => ''
		);
		extract( wp_parse_args( $args, $defaults ), EXTR_SKIP );
		
		if ( empty( $walker ) || !is_a( $walker, 'Walker' ) )
			$walker = new WPV_Walker_Category_Checklist( $url_format, $format, $taxonomy, $selected_cats, $style, $class, $label_style, $label_class );
		
		if ( !in_array( $taxonomy_orderby, array( 'id', 'count', 'name', 'slug', 'term_group', 'none' ) ) ) $taxonomy_orderby = 'name';
		if ( !in_array( $taxonomy_order, array( 'ASC', 'DESC' ) ) ) $taxonomy_order = 'ASC';
		
		$descendants_and_self = (int) $descendants_and_self;
		
		$args = array( 'taxonomy' => $taxonomy );
		
		$tax = get_taxonomy( $taxonomy );
		$args['disabled'] = false;
		
		if ( is_array( $selected_cats ) )
			$args['selected_cats'] = $selected_cats;
		elseif ( $post_id )
			$args['selected_cats'] = wp_get_object_terms( $post_id, $taxonomy, array_merge( $args, array( 'fields' => 'ids' ) ) );
		else
			$args['selected_cats'] = array();
		
		if ( is_array( $popular_cats ) )
			$args['popular_cats'] = $popular_cats;
		else
			$args['popular_cats'] = get_terms( $taxonomy, array( 'fields' => 'ids', 'orderby' => 'count', 'order' => 'DESC', 'number' => 10, 'hierarchical' => false ) );
		
		if ( $descendants_and_self ) {
			$categories = (array) get_terms( $taxonomy, array( 'child_of' => $descendants_and_self, 'hierarchical' => 0, 'hide_empty' => 1 ) );
			$self = get_term( $descendants_and_self, $taxonomy );
			array_unshift( $categories, $self );
		} else {
			$categories = (array) get_terms( $taxonomy, array('get' => $get_value, 'orderby' => $taxonomy_orderby, 'order' => $taxonomy_order ) );
		}
		
		if ( $checked_ontop ) {
			// Post process $categories rather than adding an exclude to the get_terms() query to keep the query the same across all posts (for any query cache)
			$checked_categories = array();
			$keys = array_keys( $categories );
		
			foreach( $keys as $k ) {
				if ( in_array( $categories[$k]->term_id, $args['selected_cats'] ) ) {
					$checked_categories[] = $categories[$k];
					unset( $categories[$k] );
				}
			}
		
			// Put checked cats on top
			echo call_user_func_array( array( &$walker, 'walk' ), array( $checked_categories, 0, $args ) );
		}
		// Then the rest of them
		echo call_user_func_array( array( &$walker, 'walk' ), array( $categories, 0, $args ) );
	}
}

add_shortcode('wpv-control', 'wpv_shortcode_wpv_control');

/**
* wpv_recursive_post_hierarchy
*
* Create a multidimensional array of Types post relationships
*
* @param $post_types (array) array of post type slugs to get the relationships from
* @param $level (int) depth of recursion, we are hardcoding limiting it to 5
*
* @return (array)
*
* @note this function is recursive
* @note the returned array has the format
*    Array(
*        [father] => Array(
*            [grandpa-one] => Array(
*                [bisa] => Array()
*            )
*            [grandma-one] => Array()
*        )
*        [mother] => Array()
*    )
*
* @since 1.6.0
*/

function wpv_recursive_post_hierarchy( $post_types = array(), $level = 0 ) {
	$parents_array = array();
	if ( ! is_array( $post_types ) ) {
		// Sometimes, when saving the Content Selection section with no post type selected, this is not an array
		// That can happen when switching to list taxonomy terms or users without selecting a post type first
		return $parents_array;
	}
	if ( function_exists( 'wpcf_pr_get_belongs' ) && $level < 5 ) {
		foreach ( $post_types as $post_type_slug ) {
			$this_parents = wpcf_pr_get_belongs( $post_type_slug );
			if ( $this_parents != false && is_array( $this_parents ) ) {
				$new_parents = array_values( array_keys( $this_parents ) );
				foreach ( $new_parents as $new_parent ) {
					$new_level = wpv_recursive_post_hierarchy( array( $new_parent ), $level + 1 );
					$parents_array[$new_parent] = $new_level;
				}
			}
		}
	}
	return $parents_array;
}

/**
* wpv_recursive_flatten_post_relationships
*
* Flatten a multidimensional array of Types post relationships
*
* After creating a multidimensional array of Types post relationships with wpv_recursive_post_hierarchy we use this function to flatten it
* and return the same structure as a string using specific signs for parent and adjacent relations
*
* @param $relations_array (array) multidimensional array of Types post relationships, following the format:
*    Array(
*        [father] => Array(
*            [grandpa-one] => Array(
*                [bisa] => Array()
*            )
*            [grandma-one] => Array()
*        )
*        [mother] => Array()
*    )
* @param $low_level (string) auxiliar string containing the lower levels in recursion, defaults to ''
* @param $parent_sep (string) separator for parent relationships, defaults to ">"
* @param $adjacent_sep (string) separator for adjacent relationships, defaults to ","
*
* @return (string)
*
* @note this function is recursive
* @note the returning string has the format
*    bisa>grandpa-one>father,grandma-one>father,mother
*
* @since 1.6.0
*/

function wpv_recursive_flatten_post_relationships( $relations_array, $low_level = '', $parent_sep = '>', $adjacent_sep = ',' ) {
	$my_aux = array();
	foreach ( $relations_array as $my_key => $my_value ) {
		if ( empty( $low_level ) ) {
			$my_aux[$my_key] = $my_key;
		} else {
			$my_aux[$my_key] = $my_key . $parent_sep . $low_level;
		}
		if ( !empty( $my_value ) ) {
			$my_aux[$my_key] = wpv_recursive_flatten_post_relationships( $my_value, $my_aux[$my_key], $parent_sep, $adjacent_sep );
		}
	}
	$flatten = implode( $adjacent_sep, $my_aux );
	return $flatten;
}

/**
* wpv_get_all_post_relationship_options
*
* Calculates all the possible ancestor trees given a recursive flatten post relationship string
*
* @param $relations_string (string) flatten post relationship string
* @param $parent_sep (string) separator for parent relationships, defaults to ">"
* @param $adjacent_sep (string) separator for adjacent relationships, defaults to ","
*
* @return (array)
*
* @note $relations_string must have the format
*    bisa>grandpa-one>father,grandma-one>father,mother
*
* @since 1.6.0
*/

function wpv_get_all_post_relationship_options( $relations_string, $parent_sep = '>', $adjacent_sep = ',' ) {
	$relations_trees = array();
	$aux_array = explode( $adjacent_sep, $relations_string );
	foreach ( $aux_array as $aux_tree ) {
		$relations_trees[] = $aux_tree;
		$aux_tree_pieces = explode( $parent_sep, $aux_tree );
		while ( sizeof( $aux_tree_pieces ) > 1 ) {
			array_shift( $aux_tree_pieces );
			$relations_trees[] = implode( $parent_sep, $aux_tree_pieces );
		}
	}
	return array_unique( $relations_trees );
}

// TODO
/**
Test dependence in three ref sites:

http://ref.wp-types.com/classifieds/
http://ref.wp-types.com/bootcommerce/
http://ref.wp-types.com/estates/
*/

/**
* wpv_shortcode_wpv_control_set
*
* Render the filter control for posts relationships.
*
* @param $atts (array) array of the wpv-control shortcode attributes
*    @string $url_param URL parameter for this filter control
*    @string $ancestors relationship tree to be rendered
* @param $value (string) contains a series of [wpv-control-item] shortcodes in a HTML structure
*
* @return (string) output for this wpv-control-set shortcode.
*
* @since 1.6.0
*
* @uses wpcf_pr_get_belongs
*/

add_shortcode('wpv-control-set', 'wpv_shortcode_wpv_control_set');

function wpv_shortcode_wpv_control_set( $atts, $value ) {
	// First control checks
	if ( !function_exists( 'wpcf_pr_get_belongs' ) ) {
		return __( 'You need the Types plugin to render this parametric search control', 'wpv-views' );
	}
	if ( !isset( $atts['url_param'] ) ) {
		return __('The url_param argument is missing from the wpv-control-set shortcode.', 'wpv-views');
	}
	if ( !isset( $atts['ancestors'] ) ) {
		return __('The ancestors argument is missing from the wpv-control-set shortcode.', 'wpv-views');
	}
	extract(
		shortcode_atts(array(
				'url_param' => '', // URL parameter to be used
				'ancestors' => '', // comma-separated list of parent post types for post relationship filter controls, or tree
				'format' => '' // format to use on the direct parent of the listed posts
			), $atts)
	);
	
	global $WP_Views;
	$view_settings = $WP_Views->get_view_settings();
	$returned_post_types = $view_settings['post_type'];
	$returned_post_type_parents = array();
	if ( empty( $returned_post_types ) ) {
		$returned_post_types = array( 'any' );
	}
	foreach ( $returned_post_types as $returned_post_type_slug ) {
		$parent_parents_array = wpcf_pr_get_belongs( $returned_post_type_slug );
		if ( $parent_parents_array != false && is_array( $parent_parents_array ) ) {
			$returned_post_type_parents = array_merge( $returned_post_type_parents, array_values( array_keys( $parent_parents_array ) ) );
		}
	}
	
	$replace = '[wpv-control-item url_param="' . $url_param . '" ancestor_tree="' . $ancestors . '" returned_pt="' . implode( ',', $returned_post_types ) . '" returned_pt_parents="' . implode( ',' , $returned_post_type_parents ) . '"';
	if ( !empty( $format ) ) {
		$replace .= ' format="' . $format . '"';
	}
	$value = str_replace( '[wpv-control-item', $replace, $value );
	$return = wpv_do_shortcode( $value );
	return $return;
}

/**
* wpv_shortcode_wpv_control_item
*
* Render the filter control items for posts relationships.
*
* @param $atts (array) array of the wpv-control shortcode attributes
*    @string $type kind of control to be displayed: <select> <multi-select> <checkboxes> <radios>
*    @string $ancestor_type particular ancestor that this shortcode refers to
*    @string $default_label (optional) default label for select dropdowns
*    @string $url_param (inherited) URL parameter for this filter control
*    @string $ancestor_tree (inherited) relationship tree coming from the [wpv-control-set] wrapper
*    @string $returned_pt_parents (inherited) comma separated list of direct parents of the post types listed in this View
*    @string $orderby (optional) Field name by which options will be sorted:
*         <id> <title> <date> <date_modified> <comment_count>. Defaults to <title>. Invalid value is ignored.
*    @string $order (optional) Sorting order for options: <asc> for ascending or <desc> for descending.
*         Defaults to <asc>. Invalid value is ignored.
*
* @return (string) output for this wpv-control-item shortcode.
*
* @since 1.6.0
*
* @uses wpcf_pr_get_belongs
* @uses wpv_form_control
*/

add_shortcode('wpv-control-item', 'wpv_shortcode_wpv_control_item');

function wpv_shortcode_wpv_control_item( $atts, $value ) {
	global $sitepress;
	// First control checks
	if ( !function_exists( 'wpcf_pr_get_belongs' ) ) {
		return __( 'You need the Types plugin to render this parametric search control', 'wpv-views' );
	}
	if ( !isset( $atts['url_param'] ) || empty( $atts['url_param'] ) ) {
		return __('The url_param argument is missing from the wpv-control-set shortcode.', 'wpv-views');
	}
	if ( !isset( $atts['type'] ) || empty( $atts['type'] ) ) {
		return __('The type argument needs to be set in the wpv-control-item shortcode.', 'wpv-views');
	}
	if ( !isset( $atts['ancestor_type'] ) || empty( $atts['ancestor_type'] ) ) {
		return __('The ancestor_type argument is missing from the wpv-control-item shortcode.', 'wpv-views');
	}
	if ( !isset( $atts['ancestor_tree'] ) || empty( $atts['ancestor_tree'] ) ) {
		return __('The ancestors argument is missing from the wpv-control-set shortcode.', 'wpv-views');
	}
	if ( !isset( $atts['returned_pt_parents'] ) || empty( $atts['returned_pt_parents'] ) ) {
		return __('The post types listed in this View do not have ancestors.', 'wpv-views');
	}
	extract(
		shortcode_atts( array(
				'type' => '', // select, multi-select, checbox, checkboxes, radio/radios, date/datepicker, textfield
				'url_param' => '', // URL parameter to be used
				'ancestor_type' => '',
				'ancestor_tree' => '',
				'default_label' => '',
				'returned_pt' => '',
				'returned_pt_parents' => '',
				'format' => false,
				'orderby' => 'title', // can be any key of $allowed_orderby_values
				'order' => 'ASC', // ASC or DESC
                'style' => '', // inline styles for input
                'class' => '', // input classes
                'label_style' => '', // inline styles for input label
                'label_class' => '' // classes for input label
			), $atts)
	);
    
    $style = esc_attr( $style );
    $class = esc_attr( $class );
    $label_style = esc_attr( $label_style );
    $label_class = esc_attr( $label_class );   
    
	$ancestor_tree_array = explode( '>', $ancestor_tree ); // NOTE this makes it useful for just one-branch scenarios, might extend this
	if ( !in_array( $ancestor_type, $ancestor_tree_array ) ) {
		return __( 'The ancestor_type argument refers to a post type that is not included in the ancestors tree.', 'wpv-views' );
	}
	global $wpdb, $WP_Views;
	$return = '';
	$this_type_parent_classes = array();
	$returned_post_types = explode( ',', $returned_pt );
	$returned_post_type_parents = explode( ',', $returned_pt_parents );
	
	$filter_full_list = false;
	
	$this_tree_ground = end( $ancestor_tree_array );
	$this_tree_roof = reset( $ancestor_tree_array );
	if ( !in_array( $this_tree_ground, $returned_post_type_parents ) ) {
		return __( 'The ancestors argument does not end with a valid parent for the returned post types on this View.', 'wpv-views' );
	}
	
	if ( !empty( $default_label ) ) {
		$aux_array = $WP_Views->view_used_ids;
		$view_name = get_post_field( 'post_name', end($aux_array));
		$default_label = wpv_translate( $ancestor_type . '_default_label', $default_label, false, 'View ' . $view_name );
	}

	// Validate order and orderby arguments for SQL query (ignore invalid values).

	// Allowed values and their translation into names of wp_posts columns.
	$allowed_orderby_values = array(
			'id' => 'ID',
			'title' => 'post_title',
			'date' => 'post_date',
			'date_modified' => 'post_modified',
			'comment_count' => 'comment_count' );
	if( ! isset( $allowed_orderby_values[ $orderby ] ) ) {
		$orderby = 'title';
	}
	// Now $orderby contains a valid column name at all times.
	$orderby = $allowed_orderby_values[ $orderby ];
	// Default to ASC on invalid $order value.
	$order = ( 'DESC' == strtoupper( $order ) ) ? 'DESC' : 'ASC';
	
	$view_settings = $WP_Views->get_view_settings();
	$dependant = false;
	$counters = false;
	$empty_action = array();
	if ( isset( $view_settings['dps'] ) && is_array( $view_settings['dps'] ) && isset( $view_settings['dps']['enable_dependency'] ) && $view_settings['dps']['enable_dependency'] == 'enable' ) {
		$dependant = true;
		$force_disable_dependant = $WP_Views->get_force_disable_dependant_parametric_search();
		if ( $force_disable_dependant ) {
			$dependant = false;
		}
	}
	if ( $format && strpos( $format, '%%COUNT%%' ) !== false ) {
		$counters = true;
	}
	
	if ( ( $dependant || $counters ) && $this_tree_ground == $ancestor_type ) {
		$wpv_data_cache = array();
		// Construct $wpv_data_cache
		if ( !isset( $_GET[$url_param] ) || empty( $_GET[$url_param] ) || $_GET[$url_param] === 0 || ( is_array( $_GET[$url_param] ) && in_array( (string) 0, $_GET[$url_param] ) ) ) {
			// This is when there is no value selected
			global $wp_object_cache;
			$wpv_data_cache = isset( $wp_object_cache->cache ) ? $wp_object_cache->cache : array();
		} else {
			// When there is a selected value, create a pseudo-cache based on all the other filters
			$query = wpv_get_dependant_view_query_args();
			$aux_cache_query = null;
			$filter_full_list = true;
			if ( isset( $query['post__in'] ) && is_array( $query['post__in'] ) && isset( $query['pr_filter_post__in'] ) && is_array( $query['pr_filter_post__in'] ) ) {
				$diff = array_diff( $query['post__in'], $query['pr_filter_post__in'] );
				if ( empty( $diff ) ) {// TODO maybe we can skip the query here
					unset( $query['post__in'] );
				} else {
					$query['post__in'] = $diff;
				}
			}
			$aux_cache_query = new WP_Query($query);
			if ( is_array( $aux_cache_query->posts ) && !empty( $aux_cache_query->posts ) ) {
				$f_fields = array( '_wpcf_belongs_' . $ancestor_type . '_id' );
				$wpv_data_cache = wpv_custom_cache_metadata( $aux_cache_query->posts, array( 'cf' => $f_fields ) );
			}
		}
		if ( !isset( $wpv_data_cache['post_meta'] ) ) {
			$wpv_data_cache['post_meta'] = array();
		}
		$empty_default = 'hide';
		$empty_alt = 'disable';
		$empty_options = array( 'select', 'radios', 'checkboxes' ); // multi-select is a special case because of dashes and underscores
		foreach ( $empty_options as $empty_opt ) {
			if ( isset( $view_settings['dps']['empty_' . $empty_opt] ) && $view_settings['dps']['empty_' . $empty_opt] == $empty_alt ) {
				$empty_action[$empty_opt] = $empty_alt;
			} else {
				$empty_action[$empty_opt] = $empty_default;
			}
		}
		if ( isset( $view_settings['dps']['empty_multi_select'] ) && $view_settings['dps']['empty_multi_select'] == $empty_alt ) {
			$empty_action['multi-select'] = $empty_alt;
		} else {
			$empty_action['multi-select'] = $empty_default;
		}
	}
	
	if ( $this_tree_ground == $ancestor_type ) {
	//	$this_type_parent_classes[] = 'js-wpv-post-relationship-real-parent';
		$this_type_parent_classes[] = 'js-wpv-filter-trigger';
	} else {
		$this_type_parent_classes[] = 'js-wpv-post-relationship-update';
	}
	
	if ( $this_tree_roof == $ancestor_type ) {
		$values_to_prepare = array();
		// Adjust query for WPML support
		$wpml_join = $wpml_where = "";
		if (
			isset( $sitepress ) 
			&& function_exists( 'icl_object_id' )
		) {
			$current_pt_translatable = $sitepress->is_translated_post_type( $ancestor_type );
			if ( $current_pt_translatable ) {
				$wpml_current_language = $sitepress->get_current_language();
				$wpml_join = " JOIN {$wpdb->prefix}icl_translations t ";
				$wpml_where = " AND p.ID = t.element_id AND t.language_code = %s ";
				$values_to_prepare[] = $wpml_current_language;
			}
		}
		$values_to_prepare[] = $ancestor_type;
		$pa_results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT p.ID, p.post_title
				FROM {$wpdb->posts} p {$wpml_join}
				WHERE p.post_status = 'publish' 
				{$wpml_where} 
				AND p.post_type = %s 
				ORDER BY p.{$orderby} {$order}",
				$values_to_prepare
			)
		);
	} else {
		$aux_position_array = array_keys( $ancestor_tree_array, $ancestor_type );
		if ( count( $aux_position_array ) > 1 ) {
			return __( 'There seems to be some kind of infinite loop happening.', 'wpv-views' );
		}
		$this_type_parents = array_slice( $ancestor_tree_array, 0, $aux_position_array[0] );
		foreach ( $this_type_parents as $ttp_item )  {
			$this_type_parent_classes[] = 'js-wpv-' . $ttp_item . '-watch';
		}
		
		$this_parent_parents = array();
		$this_parent_parents = wpcf_pr_get_belongs( $ancestor_type );
		if ( $this_parent_parents != false && is_array( $this_parent_parents ) ) {
			$this_parent_parents_array = array_merge( array_values( array_keys( $this_parent_parents ) ) );
		}
		
		$real_influencer_array = array_intersect( $this_parent_parents_array, $this_type_parents );
		$query_here = array();
		$query_here['posts_per_page'] = -1;
		$query_here['paged'] = 1;
		$query_here['offset'] = 0;
		$query_here['fields'] = 'ids';
		$query_here['post_type'] = $ancestor_type;
		foreach ( $real_influencer_array as $real_influencer ) {
			if ( isset( $_GET[$url_param . '-' . $real_influencer] ) && !empty( $_GET[$url_param . '-' . $real_influencer] ) && $_GET[$url_param . '-' . $real_influencer] != array( 0 ) ) {
				$query_here['meta_query'][] = array(
					'key' => '_wpcf_belongs_' . $real_influencer . '_id',
					'value' => $_GET[$url_param . '-' . $real_influencer]
				);
			}
		}
		if ( isset( $query_here['meta_query'] ) ) {
			$query_here['meta_query']['relation'] = 'AND';
			$aux_relationship_query = new WP_Query( $query_here );
			if ( 
				is_array( $aux_relationship_query->posts ) 
				&& count( $aux_relationship_query->posts ) 
			) {
				// If there are posts with those requirements, get their ID and post_title
				// We do not really need sanitization here, as $aux_relationship_query->posts only contains IDs come from the database, but still
				$values_to_prepare = array();
				$aux_rel_count = count( $aux_relationship_query->posts );
				$aux_rel_placeholders = array_fill( 0, $aux_rel_count, '%d' );
				foreach ( $aux_relationship_query->posts as $aux_rel_id ) {
					$values_to_prepare[] = $aux_rel_id;
				}
				$pa_results = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT ID, post_title
						FROM {$wpdb->posts}
						WHERE post_status = 'publish' AND ID IN (" . implode( ",", $aux_rel_placeholders ) . ")
						ORDER BY {$orderby} {$order}",
						$values_to_prepare
					)
				);
			} else {
				//If there are no posts with those requeriments, render no posts
				$pa_results = array();
			}
		} else {
			$pa_results = array();
		}
	}
	//$pa_results = $wpdb->get_results( "SELECT ID, post_title FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type = '{$ancestor_type}'" );
	// Render different controls based on the $type attribute
	switch ( $type ) {
		case 'select':
		case 'multi-select':
			// Add the default value to the top of the options if $type is select, with a 0 value
			$options = array();
			if ( $type == 'select' ) {
				if ( empty( $default_label ) ) {
					$options[''] = 0;
				} else {
					$options[$default_label] = 0;
				}
			}
			// Create the basic $element that will hold the wpv_form_control attributes
			$element = array( 'field' => array(
							'#type' => 'select',
							'#attributes' => array( 'style' => $style, 'class' => $class . ' ', 'data-currentposttype' => $ancestor_type ),
							'#inline' => true
					)
			);
			// Build the name, id and default values depending whether we are dealing with a real parent or not
			$element['field']['#default_value'] = array( 0 );
			if ( $ancestor_type == $this_tree_ground ) {
				$element['field']['#name'] = $url_param . '[]';
				$element['field']['#id'] = 'wpv_control_' . $type . '_' . $url_param;
				if ( isset( $_GET[$url_param] ) ) {
					$element['field']['#default_value'] = $_GET[$url_param];
				}
			} else {
				$element['field']['#name'] = $url_param . '-' . $ancestor_type . '[]';
				$element['field']['#id'] = 'wpv_control_' . $type . '_' . $url_param . '_' . $ancestor_type;
				if ( isset( $_GET[$url_param . '-' . $ancestor_type] ) ) {
					$element['field']['#default_value'] = $_GET[$url_param . '-' . $ancestor_type];
				}
			}
			// Security check: this must always be an array!
			if ( ! is_array( $element['field']['#default_value'] ) ) {
				$element['field']['#default_value'] = array( $element['field']['#default_value'] );
			}
			// Loop through the posts and add them as options like post_title => ID
			foreach ( $pa_results as $pa_item ) {
				
				$options[$pa_item->post_title] = array(
					'#title' => $pa_item->post_title,
					'#value' => $pa_item->ID,
					'#inline' => true,
					'#after' => '<br />'
				);
				// Dependant stuf
				if ( $this_tree_ground == $ancestor_type ) {
					if ( $format ) {
						$display_value_formatted_name = str_replace( '%%NAME%%', $options[$pa_item->post_title]['#title'], $format );
						$options[$pa_item->post_title]['#title'] = $display_value_formatted_name;
					}
					if ( $dependant || $counters  ) {
						$val = $pa_item->ID;
						$this_query = $WP_Views->get_query();
						if ( isset( $_GET[$url_param] ) && is_array( $_GET[$url_param] ) && in_array( $val, $_GET[$url_param] ) && is_object( $this_query ) && !$counters ) {
							$this_checker = $this_query->found_posts;
						} else {
							$meta_criteria_to_filter = array( '_wpcf_belongs_' . $ancestor_type . '_id' => array( $val ) );
							$data = array();
							$data['list'] = $wpv_data_cache['post_meta'];
							$data['args'] = $meta_criteria_to_filter;
							$data['kind'] = '';
							$data['comparator'] = 'equal';
							$data['filter_full_list'] = $filter_full_list;
							if ( $counters ) {
								$data['count_matches'] = true;
							}
							$this_checker = wpv_list_filter_checker( $data );
						}
						if ( $counters ) {
							$display_value_formatted_counter = $options[$pa_item->post_title]['#title'];
							$display_value_formatted_counter = str_replace( '%%COUNT%%', $this_checker, $display_value_formatted_counter );
							$options[$pa_item->post_title]['#title'] = $display_value_formatted_counter;
						}
						if ( !$this_checker && !in_array( $pa_item->ID, $element['field']['#default_value'] ) && $dependant ) {
							$options[$pa_item->post_title]['#disable'] = 'true';
							$options[$pa_item->post_title]['#labelclass'] = 'wpv-parametric-disabled';
							if ( isset( $empty_action[$type] ) && $empty_action[$type] == 'hide' ) {
								unset( $options[$pa_item->post_title] );
							}
						}
					}
				}
			}
			$element['field']['#options'] = $options;
			// If there are no options and is multi-select, break NOTE this break is for hide, maybe disable, we will see
			if ( $type == 'multi-select' && count( $options ) == 0 ) {
			//	break;
			}
			// Add classnames js-wpv-{slug}-watch for each post type slug in any tree that is ancestor of the current one, to be able to act on their changes
			if ( count( $this_type_parent_classes ) ) {
				$element['field']['#attributes']['class'] .= implode( ' ', $this_type_parent_classes );
			}
			// If there is only one option for select or none for multi-select, disable this form control NOTE review this
			if ( count( $options ) == 1 && $type == 'select' ) {
				$element['field']['#attributes']['disabled'] = 'disabled';
			}
			// If $type is multi-select, use it
			if ( $type == 'multi-select' ) {
				$element['field']['#multiple'] = 'multiple';
			}
			// Create the form control and add it to the $returned_value
			$return .= wpv_form_control( $element );
			break;
		case 'checkboxes':
			// If there are no options, break
			if ( count( $pa_results ) == 0 ) {
				$options = array();
			}
			// Create the basic $element that will hold the wpv_form_control attributes
			$element = array( 'field' => array(
						'#type' => $type,
						'#attributes' => array( 'style' => $style, 'class' => $class ),
						'#inline' => true,
						'#before' => '<div class="wpcf-checkboxes-grou">', //we need to wrap them for js purposes
						'#after' => '</div>'
			) );
			// Build the name, id and default values depending whether we are dealing with a real parent or not
			$element['field']['#default_value'] = array( -1 );
			if ( $ancestor_type == $this_tree_ground ) {
				$checkbox_url_param = $url_param . '[]';
				if ( isset( $_GET[$url_param] ) ) {
					$element['field']['#default_value'] = $_GET[$url_param];
				}
				$element['field']['#name'] = $url_param . '[]';
				$element['field']['#id'] = 'wpv_control_' . $type . '_' . $url_param;
			} else {
				$checkbox_url_param = $url_param . '-' . $ancestor_type . '[]';
				if ( isset( $_GET[$url_param . '-' . $ancestor_type] ) ) {
					$element['field']['#default_value'] = $_GET[$url_param . '-' . $ancestor_type];
				}
				$element['field']['#name'] = $url_param . '-' . $ancestor_type . '[]';
				$element['field']['#id'] = 'wpv_control_' . $type . '_' . $url_param . '_' . $ancestor_type;
			}
			// Security check: this must always be an array!
			if ( ! is_array( $element['field']['#default_value'] ) ) {
				$element['field']['#default_value'] = array( $element['field']['#default_value'] );
			}
			// Add classnames js-wpv-{slug}-watch for each post type slug in any tree that is ancestor of the current one, to be able to act on their changes
			$checkboxes_classes = '';
			if ( count( $this_type_parent_classes ) ) {
				$checkboxes_classes = implode( ' ', $this_type_parent_classes );
			}
			// Loop through the posts and add them as options
			foreach ( $pa_results as $pa_item ) {
				$options[$pa_item->ID]['#name'] = $checkbox_url_param;
				$options[$pa_item->ID]['#title'] = $pa_item->post_title;
				$options[$pa_item->ID]['#value'] = $pa_item->ID;
				$options[$pa_item->ID]['#default_value'] = in_array( $pa_item->ID, $element['field']['#default_value'] ); // set default using option titles too
				$options[$pa_item->ID]['#inline'] = true;
				$options[$pa_item->ID]['#after'] = '&nbsp;&nbsp;';
				$options[$pa_item->ID]['#attributes']['data-currentposttype'] = $ancestor_type;
				$options[$pa_item->ID]['#attributes']['data-triggerer'] = 'rel-relationship';
                $options[$pa_item->ID]['#attributes']['style'] = $style;
                $options[$pa_item->ID]['#attributes']['class'] = $class;
				if ( !empty( $checkboxes_classes ) ) {
					$options[$pa_item->ID]['#attributes']['class'] .= ' ' . $checkboxes_classes;
				}
                $options[$pa_item->ID]['#labelclass'] = $label_class;
                $options[$pa_item->ID]['#labelstyle'] = $label_style;
				// Dependant stuff
				if ( $this_tree_ground == $ancestor_type ) {
					if ( $format ) {
						$display_value_formatted_name = str_replace( '%%NAME%%', $options[$pa_item->ID]['#title'], $format );
						$options[$pa_item->ID]['#title'] = $display_value_formatted_name;
					}
					if ( $dependant || $counters  ) {
						$val = $pa_item->ID;
						$this_query = $WP_Views->get_query();
						if ( isset( $_GET[$url_param] ) && is_array( $_GET[$url_param] ) && in_array( $val, $_GET[$url_param] ) && is_object( $this_query ) && !$counters ) {
							$this_checker = $this_query->found_posts;
						} else {
							$meta_criteria_to_filter = array( '_wpcf_belongs_' . $ancestor_type . '_id' => array( $val ) );
							$data = array();
							$data['list'] = $wpv_data_cache['post_meta'];
							$data['args'] = $meta_criteria_to_filter;
							$data['kind'] = '';
							$data['comparator'] = 'equal';
							if ( $counters ) {
								$data['count_matches'] = true;
							}
							$data['filter_full_list'] = $filter_full_list;
							$this_checker = wpv_list_filter_checker( $data );
						}
						if ( $counters ) {
							$display_value_formatted_counter = $options[$pa_item->ID]['#title'];
							$display_value_formatted_counter = str_replace( '%%COUNT%%', $this_checker, $display_value_formatted_counter );
							$options[$pa_item->ID]['#title'] = $display_value_formatted_counter;
						}
						if ( !$this_checker && !in_array( $pa_item->ID, $element['field']['#default_value'] ) && $dependant ) {
							$options[$pa_item->ID]['#attributes']['#disabled'] = 'true';
							$options[$pa_item->ID]['#labelclass'] .= ' wpv-parametric-disabled';
							if ( isset( $empty_action['checkboxes'] ) && $empty_action['checkboxes'] == 'hide' ) {
								unset( $options[$pa_item->ID] );
							}
						}
					}
				}
			}
			$element['field']['#options'] = $options;
			// Calculate the control
			$return .= wpv_form_control( $element );
			break;
		case 'radio':
		case 'radios':
			// Create the basic $element that will hold the wpv_form_control attributes
			$element = array( 'field' => array(
							'#type' => 'radios',
							'#attributes' => array( 'style' => $style, 'class' => $class, 'data-currentposttype' => $ancestor_type, 'data-triggerer' => 'rel-relationship' ),
							'#inline' => true
						)
			);
			// If there are no options, break
			if ( count( $pa_results ) == 0 ) {
				$options = array();
			}
			if ( !empty( $default_label ) ) {
				$options[$default_label] = array(
					'#title' => $default_label,
					'#value' => 0,
					'#inline' => true,
					'#after' => '<br />'
				);
				if ( 
					$this_tree_ground == $ancestor_type 
					&& $dependant 
					&& count( $pa_results ) == 0 
					&& (
						! isset( $_GET[$url_param] ) 
						|| $_GET[$url_param] != '' 
					)
				) {
					$options[$default_label]['#disable'] = 'true';
					$options[$default_label]['#labelclass'] = ' wpv-parametric-disabled';
				}
			}
			// Build the name, id and default values depending whether we are dealing with a real parent or not
			$element['field']['#default_value'] = 0;
			if ( $ancestor_type == $this_tree_ground ) {
				$element['field']['#name'] = $url_param;
				$element['field']['#id'] = 'wpv_control_' . $type . '_' . $url_param;
				if ( 
					isset( $_GET[$url_param] ) 
					&& $_GET[$url_param] != 0 
				) {
					$element['field']['#default_value'] = $_GET[$url_param];
				}
			} else {
				$element['field']['#name'] = $url_param . '-' . $ancestor_type;
				$element['field']['#id'] = 'wpv_control_' . $type . '_' . $url_param . '_' . $ancestor_type;
				if ( 
					isset( $_GET[$url_param . '-' . $ancestor_type] ) 
					&& $_GET[$url_param . '-' . $ancestor_type] != 0 
				) {
					$element['field']['#default_value'] = $_GET[$url_param . '-' . $ancestor_type];
				}
			}
			// Security check: this must always be a string!
			if ( is_array( $element['field']['#default_value'] ) ) {
				$element['field']['#default_value'] = reset( $element['field']['#default_value'] );
			}
			// Loop through the posts and add them as options like post_title => ID
			foreach ( $pa_results as $pa_item ) {
				
				$options[$pa_item->post_title] = array(
					'#title' => $pa_item->post_title,
					'#value' => $pa_item->ID,
					'#inline' => true,
					'#after' => '<br />'
				);
                $options[$pa_item->post_title]['#labelclass'] = $label_class;
                $options[$pa_item->post_title]['#labelstyle'] = $label_style;
				// Dependant stuf
				if ( $this_tree_ground == $ancestor_type ) {
					if ( $format ) {
						$display_value_formatted_name = str_replace( '%%NAME%%', $options[$pa_item->post_title]['#title'], $format );
						$options[$pa_item->post_title]['#title'] = $display_value_formatted_name;
					}
					if ( $dependant || $counters ) {
						$val = $pa_item->ID;
						$this_query = $WP_Views->get_query();
						if ( isset( $_GET[$url_param] ) && !empty( $_GET[$url_param] ) && $val == esc_attr( $_GET[$url_param] ) && is_object( $this_query ) && !$counters ) {
							$this_checker = $this_query->found_posts;
						} else {
							$meta_criteria_to_filter = array( '_wpcf_belongs_' . $ancestor_type . '_id' => array( $val ) );
							$data = array();
							$data['list'] = $wpv_data_cache['post_meta'];
							$data['args'] = $meta_criteria_to_filter;
							$data['kind'] = '';
							$data['comparator'] = 'equal';
							if ( $counters ) {
								$data['count_matches'] = true;
							}
							$data['filter_full_list'] = $filter_full_list;
							$this_checker = wpv_list_filter_checker( $data );
						}
						if ( $counters ) {
							$display_value_formatted_counter = $options[$pa_item->post_title]['#title'];
							$display_value_formatted_counter = str_replace( '%%COUNT%%', $this_checker, $display_value_formatted_counter );
							$options[$pa_item->post_title]['#title'] = $display_value_formatted_counter;
						}
						if ( !$this_checker && $pa_item->ID != $element['field']['#default_value'] && $dependant ) {
							$options[$pa_item->post_title]['#disable'] = 'true';
							$options[$pa_item->post_title]['#labelclass'] .= ' wpv-parametric-disabled';
							if ( isset( $empty_action['radios'] ) && $empty_action['radios'] == 'hide' ) {
								unset( $options[$pa_item->post_title] );
							}
						}
					}
				}
			}
			$element['field']['#options'] = $options;
			// Add classnames js-wpv-{slug}-watch for each post type slug in any tree that is ancestor of the current one, to be able to act on their changes
			if ( count( $this_type_parent_classes ) ) {
				$element['field']['#attributes']['class'] .= ' '. implode( ' ', $this_type_parent_classes );
			}
			// If there is only one option, disable this form control
			//This is not really needed,asin this case we are breaking above TODO review this
			if ( count( $options ) == 0 ) {
				$element['field']['#attributes']['disabled'] = 'disabled';
			}
			// Calculate the control
			$return .= wpv_form_control( $element );
			break;
		default:
			break;
	}
	return $return;
}

//not in use anymore - leave it for retro-compatibility
add_shortcode('wpv-filter-controls', 'wpv_shortcode_wpv_filter_controls');

function wpv_shortcode_wpv_filter_controls($atts, $value) {
    
    /**
     *
     * This is a do nothing shortcode. It's just a place holder for putting the
     * wpv-control shortcodes and allows for easier editing inside the meta HTML
     *
     * This shortcode now has a function: when hide="true"
     * it does not display the wpv-control shortcodes
     * This is usefull if you need to show pagination controls but not filter controls
     * For View Forms, this hide parameter is overriden and controls are always shown
     */
    
    $value = str_replace("<!-- ADD USER CONTROLS HERE -->", '', $value);
    
	if (isset($atts['hide']) && $atts['hide'] == 'true') {
		return '';
        } else {
		return wpv_do_shortcode($value);
        }
    
}

add_action( 'wp_ajax_wpv_update_parametric_search', 'wpv_update_parametric_search' );
add_action( 'wp_ajax_nopriv_wpv_update_parametric_search', 'wpv_update_parametric_search' );

function wpv_update_parametric_search() {
	$view_id = $_POST['viewid'];
	$getthis = $_POST['getthis'];
	$args = array(
		'id' => $view_id
	);
	$get_override = array();
	if ( isset( $_POST['valz'] ) && is_array( $_POST['valz'] ) ) {
		foreach ( $_POST['valz'] as $getter ) {
			if ( isset( $getter['name'] ) && isset( $getter['value'] ) ) {
				if ( strlen( $getter['name'] ) > 2 && substr( $getter['name'], -2 ) == '[]' ) {
					$real_name = substr( $getter['name'], 0, -2 );
					if ( isset( $get_override[$real_name] ) && is_array( $get_override[$real_name] ) ) {
						$get_override[$real_name][] = $getter['value'];
					} else {
						$get_override[$real_name] = array( $getter['value'] );
					}
				} else if ( $getter['name'] == 'wpv_post_id' ) {
					global $WP_Views, $post, $authordata, $id;
					$post_id = esc_attr( $getter['value'] ); // we need to set this for the post_type_dont_include_current_page setting to work
					$post = get_post($post_id);
					$WP_Views->top_current_page = $post;
					$authordata = new WP_User($post->post_author);
					$id = $post->ID;
					$WP_Views->current_page = array( $post );
				} else if ( $getter['name'] == 'wpv_aux_parent_term_id' ) {
					$WP_Views->parent_taxonomy = esc_attr( $getter['value'] );
				} else if ( $getter['name'] == 'wpv_aux_parent_user_id' ) {
					$WP_Views->parent_user = esc_attr( $getter['value'] );
				} else if ( in_array( $getter['name'], array( 'wpv_column_sort_id', 'wpv_column_sort_dir' ) ) ) {
					// @todo temporary workaround
					if ( 
						$getter['value'] != ''
					) {
						$get_override[$getter['name']] = $getter['value'];
					}
				} else {
					$get_override[$getter['name']] = $getter['value'];
				}
			}
		}
	}
	if ( isset( $_POST['targetid'] ) ) {
		$args['target_id'] = $_POST['targetid'];
	}
	if ( isset( $_POST['attributes'] ) && is_array( $_POST['attributes'] ) ) {
		$args = array_merge( $args, $_POST['attributes'] );
	}
	
	// Switch WPML to the correct language.
    if (isset($get_override['lang'])) {
        global $sitepress;
        if (method_exists($sitepress, 'switch_lang')) {
            $sitepress->switch_lang($get_override['lang']);
        }
    }
	
	$response = array();
	
	if ( $getthis == 'form' ) {
		if ( isset( $args['target_id'] ) ) {
			$response['form'] = render_view( $args, $get_override );
			unset( $args['target_id'] );
		}
		$response['full'] = '';
	} else if ( $getthis == 'full' ) {
		$response['form'] = '';
		if ( isset( $args['target_id'] ) ) {
			unset( $args['target_id'] );
		}
		$response['full'] = render_view( $args, $get_override );
	} else if ( $getthis == 'both' ) {
		if ( isset( $args['target_id'] ) ) {
			$response['form'] = render_view( $args, $get_override );
			unset( $args['target_id'] );
		}
		$response['full'] = render_view( $args, $get_override );
	} else {
		$response['form'] = '';
		$response['full'] = '';
	}
	/*
	$response['full'] = render_view( $args, $get_override );
	if ( isset( $args['target_id'] ) ) {
		$response['form'] = render_view( $args, $get_override );
		unset( $args['target_id'] );
	}
	$response['full'] = render_view( $args, $get_override );
	*/
	echo json_encode( $response );
	die();
}


/**
* wpv_list_filter_checker
*
* Walks an array of postmeta looking for a given value.
*
* @param array $data {
* 		@type array $list The array of postmeta to walk indexed by post IDs.
*  		@type array $args The data to check against indexed by field names.
*  		@type string $kind The Types postmeta type, mainly whether it is checkboxes or not.
*  		@type string $comparator What kind of value is called valid, useful for between, greater than and lower than filters.
*  		@type bool $filter_full_list Whether we need to filter the whole list or there will be an array of post IDs to pre-filter.
*  		@type bool $count_matches Whether we bail at first finding or we need to return a number of matches.
* }
* 
* @return (boolean|integer) Whether the args condition is met or how many times it is met.
*
* @since 1.6.1
*/

function wpv_list_filter_checker( $data ) {
	// Extract the values in $args
	$list = isset( $data['list'] ) ? $data['list'] : array();
	$args = isset( $data['args'] ) ? $data['args'] : array();
	$kind = isset( $data['kind'] ) ? $data['kind'] : '';
	$comparator = isset( $data['comparator'] ) ? $data['comparator'] : 'equal';
	$filter_full_list = isset( $data['filter_full_list'] ) ? $data['filter_full_list'] : true;
	$count_matches = isset( $data['count_matches'] ) ? $data['count_matches'] : false;

	// Now, let's play
	$return = ( $count_matches ) ? 0 : false;
	if ( ! is_array( $list ) ) {
		return $return;
	}
	if ( empty( $args ) ) {
		return $return;
	}
	$types_opt = get_option( 'wpcf-fields' );	

	global $WP_Views;

	// Iterate through all posts' meta
	foreach ( $list as $key => $obj ) {
		if ( !$filter_full_list ) {
			if ( is_multisite() ) {
				$blog_id = get_current_blog_id();
				$key = str_replace( $blog_id . ':', '', $key );
			}
			if ( !in_array( $key, $WP_Views->returned_ids_for_parametric_search ) ) {
				continue;
			}
		}
		$to_match = (array) $obj;

		// Iterate through all fields that should be compared.
		foreach ( $args as $m_key => $m_value ) {			
		
			if ( array_key_exists( $m_key, $to_match ) ) {
				
				if ( $kind == 'checkboxes' && is_array( $to_match[ $m_key ] ) && is_array( $m_value ) && $types_opt ) {
					/* Special case.
					 * We're dealing with checkboxes, field exists and we are comparing two arrays of values.
					 * Actually, in this case we are given option *titles* in $m_value, so we have to get titles
					 * from field options. Also note that the "comparator" argument makes no sense here and is ignored.
					 */

					// $m_key: name of the field
					// $m_value: array of value (option title) we want to find in postmeta. There is never than one element.

					if ( strpos( $m_key, 'wpcf-' ) === 0 ) {
						$field_name = substr( $m_key, 5 );
					} else {
						$field_name = $m_key;
					}

					// Get field options from Types options (if they exist).
					$field_opt = ( isset( $types_opt[ $field_name ] )
								&& is_array( $types_opt[ $field_name ] )
								&& isset( $types_opt[ $field_name ]['data'] )
								&& is_array( $types_opt[ $field_name ]['data'] )
								&& isset( $types_opt[ $field_name ]['data']['options'] ) )
							? $types_opt[ $field_name ]['data']['options']
							: array();

					// Iterate through values in the postmeta field that should be compared.
					foreach ( $to_match[ $m_key ] as $opt ) {
					
						// This will either be false or an array holding slugs of options "checked" in the postmeta.
						$opt_data = false;
						$opt_array = maybe_unserialize( $opt );
						if ( $opt_array && is_array( $opt_array ) ) {
							// Only get keys for values that are arrays
							// Because sometimes checkbox(es) store zero when unchecked
							$opt_data = array();
							foreach ( $opt_array as $opt_array_key => $opt_array_value ) {
								if ( is_array( $opt_array_value ) ) {
									$opt_data[] = $opt_array_key;
								}
							}
						}
						
						// Build array of titles of options "checked" in postmeta.
						$opt_checked_titles = array();
						if( $opt_data && ! empty( $opt_data ) && is_array( $field_opt ) ) {
							foreach( $opt_data as $option_slug ) {
								// We don't assume anything here
								if( isset( $field_opt[ $option_slug ], $field_opt[ $option_slug ]['title'] ) ) {
									$opt_checked_titles[] = $field_opt[ $option_slug ]['title'];
								}
							}
						}

						// Is there a match?
						if( in_array( $m_value[0], $opt_checked_titles ) ) {
							if ( $count_matches ) {
								$return = $return + 1;
							} else {
								return true;
							}
						}
					}
					
				} else {
					if ( is_array( $m_value ) ) {
						$real_value = $m_value[0];
					} else {
						$real_value = $m_value;
					}
					if ( is_array( $to_match[ $m_key ] ) ) {
						foreach ( $to_match[ $m_key ] as $test_value ) {
							if ( !empty( $test_value ) || is_numeric( $test_value ) ) {
								if ( $comparator == 'greater-than' && $real_value > $test_value ) {
									if ( $count_matches ) {
										$return = $return + 1;
									} else {
										return true;
									}
								} else if ( $comparator == 'greater-equal-than' && $real_value >= $test_value ) {
									if ( $count_matches ) {
										$return = $return + 1;
									} else {
										return true;
									}
								} else if ( $comparator == 'lower-than' && $real_value < $test_value ) {
									if ( $count_matches ) {
										$return = $return + 1;
									} else {
										return true;
									}
								} else if ( $comparator == 'lower-equal-than' && $real_value <= $test_value ) {
									if ( $count_matches ) {
										$return = $return + 1;
									} else {
										return true;
									}
								} else if ( $comparator == 'equal' && $real_value == $test_value ) {
									if ( $count_matches ) {
										$return = $return + 1;
									} else {
										return true;
									}
								}
							}
						}
					} else {
						if ( $comparator == 'greater-than' && $real_value > $to_match[ $m_key ] ) {
							if ( $count_matches ) {
								$return = $return + 1;
							} else {
								return true;
							}
						} else if ( $comparator == 'greater-equal-than' && $real_value >= $to_match[ $m_key ] ) {
							if ( $count_matches ) {
								$return = $return + 1;
							} else {
								return true;
							}
						} else if ( $comparator == 'lower-than' && $real_value < $to_match[ $m_key ] ) {
							if ( $count_matches ) {
								$return = $return + 1;
							} else {
								return true;
							}
						} else if ( $comparator == 'lower-equal-than' && $real_value <= $to_match[ $m_key ] ) {
							if ( $count_matches ) {
								$return = $return + 1;
							} else {
								return true;
							}
						} else if ( $comparator == 'equal' && $real_value == $to_match[ $m_key ] ) {
							if ( $count_matches ) {
								$return = $return + 1;
							} else {
								return true;
							}
						}
					}
				}
			}
		}
	}
	return $return;
}


/**
* wpv_shortcode_wpv_filter_spinner
*
* Shortcode to display a spinner on parametric search with AJAXed results on the fly
*
* @param $atts (array) optios for this shortcode
*    'container' => HTML tag to be used
*    'class' => additional classnames to be used
*    'position' => <before> <after> <none> where to add the spinner relative to the $value
*	 'spinner' => URL of the spinner to be used
* @param $value (string) text to be wrapped inside the container
*/

add_shortcode('wpv-filter-spinner', 'wpv_shortcode_wpv_filter_spinner');

function wpv_shortcode_wpv_filter_spinner( $atts, $value ) {
	extract(
		shortcode_atts(array(
				'container' => 'span',
				'class' => '',
				'position' => 'before',
				'spinner' => '',
                'style' => ''
			), $atts)
	);
	
	if (
		empty( $spinner ) 
		&& ! empty( $position )
		&& $position != 'none'
	) {
		// Keep the spinner coming from the View settings for backward compatibility
		global $WP_Views;
		$view_settings = $WP_Views->get_view_settings();
		if (
			isset( $view_settings['dps'] ) 
			&& isset( $view_settings['dps']['spinner'] ) 
			&& $view_settings['dps']['spinner'] != 'none'
		) {
			if ( $view_settings['dps']['spinner'] == 'custom' ) {
				if ( isset( $view_settings['dps']['spinner_image_uploaded'] ) ) {
					$spinner = $view_settings['dps']['spinner_image_uploaded'];
				}
			} else if ( $view_settings['dps']['spinner'] == 'inhouse' ) {
				if ( isset( $view_settings['dps']['spinner_image'] ) ) {
					$spinner = $view_settings['dps']['spinner_image'];
				}
			}
		}
	}
	
	// $spinner_image might contain SSL traces, adjust if needed
	if ( !is_ssl() ) {
		$spinner = str_replace( 'https://', 'http://', $spinner );
	}
	
    if ( ! empty( $style ) ) {
        $style = '; '. esc_attr( $style );
    }
    
	$return = '<' . $container . ' style="display:none'. $style .'" class="js-wpv-dps-spinner';
	if ( !empty( $class ) ) {
		$return .= ' ' . $class;
	}
	$return .= '">';
	if ( ! empty( $position ) && ! empty( $spinner ) && $position == 'before' ) {
		$return .= '<img src="' . $spinner . '" />';
	}
	$return .= wpv_do_shortcode( $value );
	if ( ! empty( $position ) && ! empty( $spinner ) && $position == 'after' ) {
		$return .= '<img src="' . $spinner . '" />';
	}
	$return .= '</' . $container . '>';
	return $return;
}

/**
* wpv_shortcode_wpv_filter_reset
*
* Shortcode to display a reset button on parametric search
*
* @param $atts (array) optios for this shortcode
*    'name' => __('Reset', 'wpv-views')
*    'class' => additional classnames to be used
*    'type' => HTML tag to use, input|button
*/

add_shortcode('wpv-filter-reset', 'wpv_shortcode_wpv_filter_reset');

function wpv_shortcode_wpv_filter_reset( $atts ) {
	if ( _wpv_filter_is_form_required() ) {
		extract(
		shortcode_atts(array(
				'class' => '',
				'reset_label' => __('Reset', 'wpv-views'),
				'type' => 'input',
                'style' => ''
			), $atts)
		);
        $class_to_add = '';
        $classnames = array();
        if ( ! empty( $class ) ) {
            $classnames = explode( ' ', esc_attr( $class ) );
        }
        if ( ! empty( $style ) ) {
            $style = ' style="'. esc_attr( $style ) .'"';
        }
		$classnames[] = 'js-wpv-reset-trigger';
		if ( count( $classnames ) > 0 ) {
			$class_to_add = ' class="' . implode( ' ', $classnames ) . '"';
		}
        global $WP_Views;
		$aux_array = $WP_Views->view_used_ids;
		$view_name = get_post_field( 'post_name', end($aux_array));
        $reset_label = wpv_translate( 'button_reset_label', $reset_label, false, 'View ' . $view_name );
		if ( $type == 'button' ) {
			$out = '<button' . $class_to_add . $style . ' >' . $reset_label . '</button>';
		} else {
			$out = '<input type="button" value="' . esc_attr( $reset_label ) . '" name="wpv_filter_reset"' . $class_to_add . $style . ' />';
		}
        return $out;
    } else {
        return '';
    }
}