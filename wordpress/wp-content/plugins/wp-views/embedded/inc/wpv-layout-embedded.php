<?php

/*
 
    Shortcode for sorting by the column heading in
    table layout mode.
    
*/

add_shortcode('wpv-heading', 'wpv_header_shortcode');
function wpv_header_shortcode($atts, $value){
    extract(
        shortcode_atts( array('name' => ''), $atts )
    );

    if (isset($atts['name']) && strpos($atts['name'], 'types-field-')) {
        $atts['name'] = strtolower($atts['name']);
    }
    
    global $WP_Views;
    $view_settings = $WP_Views->get_view_settings();
    
    //'wpv_column_sort_id'
    $order_class = 'wpv-header-no-sort';
    
    if ($view_settings['view-query-mode'] == 'normal' && $atts['name'] != 'post-body' && $atts['name'] != 'wpv-post-taxonomy' && !empty( $atts['name'] ) 
	// remove table column sorting for certain fields un Views listing users
	&& ( $view_settings['query_type'][0] != 'users' || in_array( $atts['name'],array('user_email', 'user_login', 'display_name', 'user_url', 'user_registered') ) )
    ) {
	
	$head_name = $atts['name'];
	if ( strpos( $head_name, 'types-field') === 0 ) {
		$field_name = 'wpcf-' . strtolower( substr( $head_name, 12 ) );
		if ( !function_exists( '_wpv_is_field_of_type' ) ) include_once( WPV_PATH_EMBEDDED . '/inc/wpv-filter-embedded.php');
		if ( _wpv_is_field_of_type( $field_name, 'checkboxes' ) || _wpv_is_field_of_type( $field_name, 'skype' ) ) {
			return wpv_do_shortcode( $value );
		}
	}

        if (isset($_GET['wpv_column_sort_id']) && esc_attr($_GET['wpv_column_sort_id']) == $atts['name'] && isset($_GET['wpv_view_count']) && $WP_Views->get_view_count() == esc_attr($_GET['wpv_view_count']) ) {
            
            if (isset($_GET['wpv_column_sort_dir']) && esc_attr($_GET['wpv_column_sort_dir']) != '') {
                if (esc_attr($_GET['wpv_column_sort_dir']) == 'asc') {
                    $order_class = 'wpv-header-asc';
                } else {
                    $order_class = 'wpv-header-desc';
                }
            } else {
                // use the default order
                $order_selected = $view_settings['order'];
                if ($order_selected == 'ASC') {
                    $order_class = 'wpv-header-asc';
                } else {
                    $order_class = 'wpv-header-desc';
                }
            }
        }
        if ($order_class == 'wpv-header-asc') {
            $dir = "desc";
        } else {
            $dir = "asc";
        }
        $link = '<a href="#" class="' . $order_class . ' js-wpv-column-header-click" data-viewnumber="' . $WP_Views->get_view_count() . '" data-name="' . $atts['name'] . '" data-direction="' . $dir . '">' . wpv_do_shortcode( $value ) . '<span class="wpv-sorting-indicator"></span></a>';
        return $link;
    } else {
        return wpv_do_shortcode( $value );
    }
}

add_shortcode('wpv-layout-start', 'wpv_layout_start_shortcode');
function wpv_layout_start_shortcode($atts){
    
    global $WP_Views;
    
    // TODO Check Additional JS
    $view_settings = $WP_Views->get_view_layout_settings();
    if (!empty($view_settings['additional_js'])) {
        $scripts = explode(',', $view_settings['additional_js']);
        $count = 1;
        foreach ($scripts as $script) {
            if (strpos($script, '[theme]') == 0) {
                $script = str_replace('[theme]', get_stylesheet_directory_uri(), $script);
            }
            add_action('wp_footer', create_function('$a=1, $script=\'' . $script. '\'', 'echo "<script type=\"text/javascript\" src=\"$script?ver=" . rand(1, 1000) . "\"></script>";'), 21); // Set priority higher than 20, when all the footer scripts are loaded
            $count++;
        }
    }
    $view_settings = $WP_Views->get_view_settings();
    $class = array('js-wpv-view-layout');
    $style = array();
    $speed = '';
    if (($view_settings['pagination'][0] == 'enable' && $view_settings['ajax_pagination'][0] == 'enable') || $view_settings['pagination']['mode'] == 'rollover') {
        $class[] = 'wpv-pagination';
        $class[] = 'js-wpv-pagination-responsive';
        if (!isset($view_settings['pagination']['preload_images'])) {
            $view_settings['pagination']['preload_images'] = false;
        }
        if (!isset($view_settings['rollover']['preload_images'])) {
            $view_settings['rollover']['preload_images'] = false;
        }
        if (($view_settings['pagination']['mode'] == 'paged' && $view_settings['pagination']['preload_images'])
            || ($view_settings['pagination']['mode'] == 'rollover' && $view_settings['rollover']['preload_images'])) {
            $class[] = 'wpv-pagination-preload-images';
            $style[] = 'visibility:hidden;';
        }
        if (($view_settings['pagination']['mode'] == 'paged' && $view_settings['pagination']['preload_pages'])
            || ($view_settings['pagination']['mode'] == 'rollover' && $view_settings['pagination']['preload_pages'])) {
            $class[] = 'wpv-pagination-preload-pages';
        }
        if ($view_settings['pagination']['mode'] == 'paged' && isset($view_settings['ajax_pagination']['duration'])) {
		$speed = $view_settings['ajax_pagination']['duration'];
        }
        if ($view_settings['pagination']['mode'] == 'rollover' && isset($view_settings['rollover']['duration'])) {
		$speed = $view_settings['rollover']['duration'];
        }
        
        $add = '';
        if (!empty($class)) {
            $add .= ' class="' . implode(' ', $class) . '"';
        }
        if (!empty($style)) {
            $add .= ' style="' . implode(' ', $style) . '"';
        }
        if (!empty($speed)) {
			$add .= ' data-duration="' . $speed .  '"';
        }
        
        return "<div id=\"wpv-view-layout-" . $WP_Views->get_view_count() . "\"$add>\n";
    } else if ( isset( $view_settings['dps'] ) && is_array( $view_settings['dps'] ) && isset( $view_settings['dps']['ajax_results'] ) && $view_settings['dps']['ajax_results'] == 'enable' ) {
		return "<div id=\"wpv-view-layout-" . $WP_Views->get_view_count() . "\" class=\"js-wpv-view-layout\">\n";
    } else {
        return '';
    }
}

add_shortcode('wpv-layout-end', 'wpv_layout_end_shortcode');
function wpv_layout_end_shortcode($atts){
    global $WP_Views;
    
    $view_settings = $WP_Views->get_view_settings();
    if (($view_settings['pagination'][0] == 'enable' && $view_settings['ajax_pagination'][0] == 'enable') || $view_settings['pagination']['mode'] == 'rollover') {
        return '</div>';
    } else if ( isset( $view_settings['dps'] ) && is_array( $view_settings['dps'] ) && isset( $view_settings['dps']['ajax_results'] ) && $view_settings['dps']['ajax_results'] == 'enable' ) {
		return '</div>';
    } else {
        return '';
    }
}

add_shortcode('wpv-layout-row', 'wpv_layout_row');
function wpv_layout_row( $atts, $value ){
	extract(
		shortcode_atts( array(
			'framework' => 'bootstrap',
			'cols' => 12,
			'col_options' => '',
		), $atts )
	);
	if ( 'bootstrap' == $framework ) {
		$elements = substr_count( $value, '[wpv-layout-cell-span]' );
		$counter = 1;
		$pattern = array();

		// if we have col_options
		preg_match_all('/\{([^}]*)\}/', $col_options, $pieces);
		foreach($pieces[1] as $match) {
			$piece = explode(',', $match);
			if ( ( count( $piece ) == $elements ) && ( array_sum( $piece ) == $cols ) ) {
				$pattern = $piece;
			}
		}
		while(preg_match('#\\[wpv-layout-cell-span]#', $value, $matches)) {
			$pos = strpos( $value, $matches[0] );
			$len = strlen( $matches[0] );
			if ( 0 < count( $pattern ) ) {
				$value = substr_replace( $value, 'span' . $pattern[$counter - 1], $pos, $len );
				$counter++;
			} elseif ( $counter < $elements ) {
				$counter++;
				$value = substr_replace( $value, 'span' . floor( $cols/$elements ), $pos, $len );
			} else {
				$value = substr_replace( $value, 'span' . ( $cols - ( ( $elements -1 ) * ( floor( $cols/$elements ) ) ) ), $pos, $len );
			}
		}
	}
	
	return wpv_do_shortcode( $value );
        
}

add_shortcode('wpv-layout-meta-html', 'wpv_layout_meta_html');
function wpv_layout_meta_html($atts) {
    extract(
        shortcode_atts( array(), $atts )
    );

    global $WP_Views;
    $view_layout_settings = $WP_Views->get_view_layout_settings();
    
    if (isset($view_layout_settings['layout_meta_html'])) {
        
        $content = wpml_content_fix_links_to_translated_content($view_layout_settings['layout_meta_html']);
        
        return wpv_do_shortcode($content);
    } else {
        return '';
    }
}