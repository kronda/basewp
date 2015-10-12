<?php
/**
* wpv-pagination-embedded.php
*
* @package Views
*
* @since unknown
*/

// Set the default values to display in the View editor.
add_filter( 'wpv_view_settings', 'wpv_pagination_defaults', 10 );

function wpv_pagination_defaults( $view_settings ) {
    $defaults = array(
        'posts_per_page' => 10,
        'pagination' => array(
        //    'disable',
            'mode' => 'paged',
            'preload_images' => 1,
            'cache_pages' => 1,
            'preload_pages' => 1,
            'spinner' => 'default',
            'spinner_image' => WPV_URL_EMBEDDED . '/res/img/ajax-loader.gif',
            'spinner_image_uploaded' => '',
            'callback_next' => '',
            'page_selector_control_type' => 'drop_down',
        ),
        'ajax_pagination' => array(
        //    'disable',
            'style' => 'fade',
        ),
        'rollover' => array(
            'posts_per_page' => 1,
            'speed' => 5,
            'effect' => 'fade',
            'preload_images' => 1,
            'include_page_selector' => 0,
            'include_prev_next_page_controls' => 0,
        ),
    );
    $view_settings = wpv_parse_args_recursive( $view_settings, $defaults );
    // Move the 0-indexed items out of the recursive parsing: it breaks!
    if ( ! isset( $view_settings['pagination'][0] ) ) {
		$view_settings['pagination'][0] = 'disable';
    }
    if ( ! isset( $view_settings['ajax_pagination'][0] ) ) {
		$view_settings['ajax_pagination'][0] = 'disable';
    }

    if ( $view_settings['pagination']['spinner'] == 'uploaded' ) {
        $view_settings['pagination']['spinner_image'] = $view_settings['pagination']['spinner_image_uploaded'];
    }

    return $view_settings;
}

// @todo DEPRECATED
add_filter( 'wpv_view_settings_save', 'wpv_pager_defaults_save', 10 );
function wpv_pager_defaults_save( $view_settings ) {
    // we need to set 0 for the checkboxes that aren't checked and are missing for the $_POST.
    $defaults = array(
        'pagination' => array(
            'preload_images' => 0,
            'cache_pages' => 0,
            'preload_pages' => 0,
        ),
        'rollover' => array(
            'preload_images' => 0,
        ),
    );
    $view_settings = wpv_parse_args_recursive( $view_settings, $defaults );
    return $view_settings;
}

function wpv_get_view_pagination_data( $view_settings ) {
	$pagination_data = array();
	// AJAX
	$pagination_data['ajax'] = $view_settings['ajax_pagination'][0] == 'enable' ? 'true' : 'false';
	// AJAX effect
	$pagination_data['effect'] = isset( $view_settings['ajax_pagination']['style'] ) ? $view_settings['ajax_pagination']['style'] : 'fade';
	// Rollover
	$pagination_data['stop_rollover'] = 'false';
	// Adjust for rollover
	if ( $view_settings['pagination']['mode'] == 'rollover' ) {
		$pagination_data['ajax'] = 'true';
		$pagination_data['effect'] = isset( $view_settings['rollover']['effect'] ) ? $view_settings['rollover']['effect'] : $pagination_data['effect'];
		$pagination_data['stop_rollover'] = 'true';
	}
	// Cache & preload
	$pagination_data['cache_pages'] = $view_settings['pagination']['cache_pages'];
	$pagination_data['preload_pages'] = $view_settings['pagination']['preload_pages'];
	// Spinner & spinner image
	$pagination_data['spinner'] = ( isset( $view_settings['pagination']['spinner'] ) ) ? $view_settings['pagination']['spinner'] : 'no';
	$pagination_data['spinner_image'] = ( isset( $view_settings['pagination']['spinner_image'] ) ) ? $view_settings['pagination']['spinner_image'] : '';
	// $spinner_image might contain SSL traces, adjust if needed
	if ( ! is_ssl() ) {
		$pagination_data['spinner_image'] = str_replace( 'https://', 'http://', $pagination_data['spinner_image'] );
	}
	// Callback next
	$pagination_data['callback_next'] = ( isset( $view_settings['pagination']['callback_next'] ) ) ? $view_settings['pagination']['callback_next'] : '';
	
	return $pagination_data;
}

/**
* Views-Shortcode: wpv-pagination
*
* Description: Display the pagination controls that are within the shortcode.
* The pagination controls will only be displayed if there are multiple
* pages to display
*
* Parameters:
* This has no parameters.
*
* Example usage:
*
* Link:
*
* Note:
*
*/

add_shortcode( 'wpv-pagination', 'wpv_pagination_shortcode' );

function wpv_pagination_shortcode( $atts, $value ) {
    extract(
		shortcode_atts(
			array(), 
			$atts
		)
    );
    global $WP_Views;
    if ( $WP_Views->get_max_pages() > 1.0 ) {
        return wpv_do_shortcode( $value );
    } else {
        return '';
    }
}

/**
* Views-Shortcode: wpv-pager-num-page
*
* Description: Display the maximum number of pages found by the Views Query.
*
* Parameters:
* This has no parameters.
*
* Example usage:
*
* Link:
*
* Note:
*
*/

add_shortcode('wpv-pager-num-page', 'wpv_pager_num_page_shortcode');

function wpv_pager_num_page_shortcode( $atts ) {
    extract(
        shortcode_atts(
			array(), 
			$atts
		)
    );
    global $WP_Views;
    if ( $WP_Views->get_max_pages() > 1.0 ) {
        return sprintf( '%1.0f', $WP_Views->get_max_pages() );
    } else {
        return '';
    }
}

/**
* Views-Shortcode: wpv-pager-prev-page
*
* Description: Display a "Previous" link to move to the previous page.
*
* Parameters:
* This has no parameters.
*
* Example usage:
* [wpv-pager-prev-page]Previous[/wpv-pager-prev-page]
*
* Link:
*
* Note:
*
*/

add_shortcode( 'wpv-pager-prev-page', 'wpv_pager_prev_page_shortcode' );

function wpv_pager_prev_page_shortcode( $atts, $value ) {
    extract(
        shortcode_atts(
			array(
				'style' => '',
				'class' => ''
            ),
			$atts
		)
    );
	
    global $WP_Views;
    $page = $WP_Views->get_current_page_number();
    $view_settings = $WP_Views->get_view_settings();
	
    $display = false;
    if ( 
		$WP_Views->get_max_pages() > 1.0 
		&& (
			$view_settings['pagination']['mode'] == 'rollover' 
			|| $page > 1
		)
	) {
		$display = true;
    }

    if ( $display ) {
        $page--;
        $value = wpv_do_shortcode( $value );
		$pagination_data = wpv_get_view_pagination_data( $view_settings );
        if ( $view_settings['pagination']['mode'] == 'rollover' ) {
            if ( $pagination_data['effect'] == 'slideleft' ) {
                $pagination_data['effect'] = 'slideright';
            } else if ( $pagination_data['effect'] == 'slidedown' ) {
                $pagination_data['effect'] = 'slideup';
            }
        }

		if ( ! empty( $style ) ) {
            $style = ' style="'. esc_attr( $style )  .'"';
        }
        if ( ! empty( $class) ) {
            $class = ' ' . esc_attr( $class );
        }
		
        if ( $page <= 0 ) {
            $page = $WP_Views->get_max_pages();
        } else if ( $page > $WP_Views->get_max_pages() ) {
            $page = 1;
        }
		
		$return = '<a href="#"'
			. ' data-viewnumber="' . esc_attr( $WP_Views->get_view_count() ) . '"'
			. ' data-page="' . esc_attr( $page ) . '"'
			. ' data-ajax="' . esc_attr( $pagination_data['ajax'] ) . '"'
			. ' data-effect="' . esc_attr( $pagination_data['effect'] ) . '"'
			. ' data-maxpages="' . esc_attr( $WP_Views->get_max_pages() ) . '"'
			. ' data-cachepages="' . esc_attr( $pagination_data['cache_pages'] ) . '"'
			. ' data-preloadimages="' . esc_attr( $pagination_data['preload_pages'] ) . '"'
			. ' data-spinner="' . esc_attr( $pagination_data['spinner'] ) . '"'
			. ' data-spinnerimage="' . esc_attr( $pagination_data['spinner_image'] ) . '"'
			. ' data-callbacknext="' . esc_attr( $pagination_data['callback_next'] ) . '"'
			. ' data-stoprollover="' . esc_attr( $pagination_data['stop_rollover'] ) . '"'
			. ' class="wpv-filter-previous-link js-wpv-pagination-previous-link'. $class .'"'. $style .'>' 
			. $value 
			. '</a>';
			
		return $return;
    } else {
        return '';
    }
}

/**
* Views-Shortcode: wpv-pager-next-page
*
* Description: Display a "Next" link to move to the next page.
*
* Parameters:
* This has no parameters.
*
* Example usage:
* [wpv-pager-next-page]Next[/wpv-pager-next-page]
*
* Link:
*
* Note:
*
*/

add_shortcode( 'wpv-pager-next-page', 'wpv_pager_next_page_shortcode' );

function wpv_pager_next_page_shortcode( $atts, $value ) {
    extract(
        shortcode_atts(
			array(
                'style' => '',
                'class' => ''
            ), 
			$atts
		)
    );

    global $WP_Views;
    $page = $WP_Views->get_current_page_number();
    $view_settings = $WP_Views->get_view_settings();
	
    $display = false;
    if ( 
		$WP_Views->get_max_pages() > 1.0 
		&& (
			$view_settings['pagination']['mode'] == 'rollover'
			|| $page < $WP_Views->get_max_pages()
		)
	) {
        $display = true;
    }

    if ( $display ) {
        $page++;
        $value = wpv_do_shortcode( $value );
		$pagination_data = wpv_get_view_pagination_data( $view_settings );
		if ( $view_settings['pagination']['mode'] == 'rollover' ) {
            if ( $pagination_data['effect'] == 'slideright' ) {
                $pagination_data['effect'] = 'slideleft';
            } else if ( $pagination_data['effect'] == 'slideup' ) {
                $pagination_data['effect'] = 'slidedown';
            }
        }
		
		if ( ! empty( $style ) ) {
            $style = ' style="'. esc_attr( $style ) .'"';
        }
        if ( ! empty( $class ) ) {
            $class = ' ' . esc_attr( $class );
        } 
        // adjust pages when reaching beyond the last or first
        if ( $page <= 0 ) {
            $page = $WP_Views->get_max_pages();
        } else if ( $page > $WP_Views->get_max_pages() ) {
            $page = 1;
        }
        
		$return = '<a href="#"'
			. ' data-viewnumber="' . esc_attr( $WP_Views->get_view_count() ) . '"'
			. ' data-page="' . esc_attr( $page ) . '"'
			. ' data-ajax="' . esc_attr( $pagination_data['ajax'] ) . '"'
			. ' data-effect="' . esc_attr( $pagination_data['effect'] ) . '"'
			. ' data-maxpages="' . esc_attr( $WP_Views->get_max_pages() ) . '"'
			. ' data-cachepages="' . esc_attr( $pagination_data['cache_pages'] ) . '"'
			. ' data-preloadimages="' . esc_attr( $pagination_data['preload_pages'] ) . '"'
			. ' data-spinner="' . esc_attr( $pagination_data['spinner'] ) . '"'
			. ' data-spinnerimage="' . esc_attr( $pagination_data['spinner_image'] ) . '"'
			. ' data-callbacknext="' . esc_attr( $pagination_data['callback_next'] ) . '"'
			. ' data-stoprollover="' . esc_attr( $pagination_data['stop_rollover'] ) . '"'
			. ' class="wpv-filter-next-link js-wpv-pagination-next-link'. $class .'"'. $style .'>' 
			. $value 
			. '</a>';
			
		return $return;
    } else {
        return '';
    }
}

/**
* Views-Shortcode: wpv-pager-current-page
*
* Description: Display the current page number. It can be displayed as a single number
* or as a drop-down list or series of dots to select another page.
*
* Parameters:
* 'style' => leave empty to display a number.
* 'style' => 'drop_down' to display a selector to select another page.
* 'style' => 'link' to display a series of links to each page
*
* Example usage:
*
* Link:
*
* Note:
*
*/

add_shortcode( 'wpv-pager-current-page', 'wpv_pager_current_page_shortcode' );

function wpv_pager_current_page_shortcode( $atts ) {
    extract(
        shortcode_atts(
			array(), 
			$atts
		)
    );

    global $WP_Views;
	$view_id = $WP_Views->get_current_view();
    
    if ( $WP_Views->get_max_pages() <= 1.0 ) {
        return '';
    }

    $page = $WP_Views->get_current_page_number();

    if ( isset( $atts['style'] ) ) {
        
        $view_settings = $WP_Views->get_view_settings();
        $cache_pages = $view_settings['pagination']['cache_pages'];
        $preload_pages = $view_settings['pagination']['preload_pages'];
        $spinner = $view_settings['pagination']['spinner'];
        $spinner_image = $view_settings['pagination']['spinner_image'];
		// $spinner_image might contain SSL traces, adjust if needed
		if ( ! is_ssl() ) {
			$spinner_image = str_replace( 'https://', 'http://', $spinner_image );
		}
        $callback_next = $view_settings['pagination']['callback_next'];
        
        if ( $view_settings['pagination']['mode'] == 'paged' ) {
            $ajax = $view_settings['ajax_pagination'][0] == 'enable' ? 'true' : 'false';
            $effect = isset( $view_settings['ajax_pagination']['style'] ) ? $view_settings['ajax_pagination']['style'] : 'fade';
        }
        
        if ( $view_settings['pagination']['mode'] == 'rollover' ) {
            $ajax = 'true';
            $effect = $view_settings['rollover']['effect'];
            // convert rollover to slide effect if the user clicks on a page.
            
            if ( $effect == 'slideleft' || $effect == 'slideright' ) {
                $effect = 'slideh';
            }
            if ( $effect == 'slideup' || $effect == 'slidedown' ) {
                $effect = 'slidev';
            }
        }

        switch( $atts['style'] ) {
            case 'drop_down':
                $out = '';
                $out .= '<select id="wpv-page-selector-' . $WP_Views->get_view_count() . '" class="js-wpv-page-selector" data-viewnumber="' . $WP_Views->get_view_count() . '" data-ajax="' . $ajax . '" data-effect="' . $effect . '" data-maxpages="' . $WP_Views->get_max_pages() . '" data-cachepages="' . $cache_pages . '" data-preloadimages="' . $preload_pages . '" data-spinner="' . $spinner . '" data-spinnerimage="' . $spinner_image . '" data-callbacknext="' . $callback_next . '" data-stoprollover="true">' . "\n";
        
                $max_page = intval( $WP_Views->get_max_pages() );
                for ($i = 1; $i < $max_page + 1; $i++) {
                    $is_selected = $i == $page ? ' selected="selected"' : '';
                    $page_number = apply_filters( 'wpv_pagination_page_number', $i, $atts['style'], $view_id ) ;
                    $out .= '<option value="' . $i . '" ' . $is_selected . '>' . $page_number . "</option>\n";
                }
                $out .= "</select>\n";
        
                return $out;
                    
            case 'link':
                $page_count = intval( $WP_Views->get_max_pages() );
                // output a series of dots linking to each page.
                $classname = '';
                $out = '<div class="wpv_pagination_links">';
				$classname = 'wpv_pagination_dots';
				$classname = apply_filters( 'wpv_pagination_container_classname', $classname, $atts['style'], $view_id );
				$out .= '<ul class="' . $classname . '">';
                
                for ( $i = 1; $i < $page_count + 1; $i++ ) {
                    $page_title = sprintf( __( 'Page %s', 'wpv-views' ), $i );
                    $page_title = esc_attr( apply_filters( 'wpv_pagination_page_title', $page_title, $i, $atts['style'], $view_id ) );
                    $page_number = apply_filters( 'wpv_pagination_page_number', $i, $atts['style'], $view_id );
                    $link = '<a title="' . $page_title . '" href="#" class="wpv-filter-pagination-link js-wpv-pagination-link" data-viewnumber="' . $WP_Views->get_view_count() . '" data-page="' . $i . '" data-ajax="' . $ajax . '" data-effect="' . $effect . '" data-maxpages="' . $page_count . '" data-cachepages="' . $cache_pages . '" data-preloadimages="' . $preload_pages . '" data-spinner="' . $spinner . '" data-spinnerimage="' . $spinner_image . '" data-callbacknext="' . $callback_next . '" data-stoprollover="true">' . $page_number . '</a>';
                    $link_id = 'wpv-page-link-' . $WP_Views->get_view_count() . '-' . $i;
                    $item = '';
					if ( $i == $page ) {
                        $item .= '<li id="' . $link_id . '" class="' . $classname . '_item wpv_page_current">' . $link . '</li>';
                    } else {
                        $item .= '<li id="' . $link_id . '" class="' . $classname . '_item">' . $link . '</li>';
                    }
					$item = apply_filters( 'wpv_pagination_page_item', $item, $i, $page, $page_count, $atts['style'], $view_id );
					$out .= $item;
                }
                $out .= '</ul>';
                $out .= '</div>';
                //$out .= '<br />'; NOTE: this extra br tag was removed in Views 1.5
                return $out;

        }
    } else {
        // show the page number.
        return sprintf( '%d', $page );
    }
}

// @todo DEPRECATED, check and clean

function wpv_pagination_js() {
    static $js_rendered = false;
    if ($js_rendered == false) {

        $ajax_url = home_url();
        if (substr($ajax_url, strlen($ajax_url) - 1, 1) != '/') {
            $ajax_url .= '/';
        }

        $permalink_structure = get_option('permalink_structure');

        if ($permalink_structure != '') {
            $ajax_url .= 'wpv-ajax-pagination/';
        } else {
            $ajax_url = plugins_url('wpv-ajax-pagination-default.php', __FILE__);
        }
        
        if ( isset( $_SERVER ['SERVER_SOFTWARE'] ) && ( strpos( strtolower ( $_SERVER ['SERVER_SOFTWARE'] ), 'iis' ) !== false ) ) { // Workaround for IIS servers - See: https://icanlocalize.basecamphq.com/projects/7393061-toolset/todo_items/166116657/comments
			$ajax_url = plugins_url('wpv-ajax-pagination-default.php', __FILE__);
		}
		
		// NOTE fix possible SSL problems below
		/*
		Only check is_ssl() because the pagination URL must have the same origin as the frontend page requesting it
		if ( ( defined( 'FORCE_SSL_ADMIN' ) && FORCE_SSL_ADMIN ) || is_ssl() ) {
			$ajax_url = str_replace( 'http://', 'https://', $ajax_url );
		}
		*/
		if ( is_ssl() ) {
			$ajax_url = str_replace( 'http://', 'https://', $ajax_url );
		}
		
        // NOTE maybe network_admin_url() for multisite should be used here
        ?>
        <script type="text/javascript">
        
            var wpv_admin_ajax_url = "<?php echo admin_url('admin-ajax.php', null); ?>";
            var wpv_ajax_pagination_url = "<?php echo $ajax_url; ?>";

                        
        </script>
        <?php
        $js_rendered = true;
    }
}

function wpv_pagination_rollover_shortcode() {
    global $WP_Views;
    $view_settings = $WP_Views->get_view_settings();
    $view_settings['rollover']['count'] = $WP_Views->get_max_pages();
    wpv_pagination_rollover_add_slide( $WP_Views->get_view_count(), $view_settings );
    add_action( 'wp_footer', 'wpv_pagination_rollover_js', 30 ); // Set priority higher than 20, when all the footer scripts are loaded
}

function wpv_pagination_rollover_add_slide($id, $settings = array()) {
    static $rollovers = array();
    if ( $id == 'get' ) {
        return $rollovers;
    }
    $rollovers[$id] = $settings;
}

function wpv_pagination_rollover_js() {
    $rollovers = wpv_pagination_rollover_add_slide( 'get' );
    if ( ! empty( $rollovers ) ) {
        global $WP_Views;
        $out = '';
        wpv_pagination_js();
        ?>
        <script type="text/javascript">
            jQuery( document ).ready( function() {
        <?php
        foreach ( $rollovers as $id => $rollover ) {
			if ( 
				! isset( $rollover['pagination']['spinner'] ) 
				|| empty( $rollover['pagination']['spinner'] )
			) {
				$spinner = 'default';
			} else {
				$spinner = esc_js( $rollover['pagination']['spinner'] );
			}
			$spinner_image= '';
			if ( 'default' == $spinner ) {
				$spinner_image = isset( $rollover['pagination']['spinner_image'] ) ? esc_url( $rollover['pagination']['spinner_image'] ) : '';
			} else if ( 'uploaded' == $spinner ) {
				$spinner_image = isset( $rollover['pagination']['spinner_image_uploaded'] ) ? esc_url( $rollover['pagination']['spinner_image_uploaded'] ) : '';
			}
			// $spinner_image might contain SSL traces, adjust if needed
			if ( ! is_ssl() ) {
				$spinner_image = str_replace( 'https://', 'http://', $spinner_image );
			}
			// Make sure we have all the needed data
			if ( 
				! isset( $rollover['rollover']['effect'] ) 
				|| empty ( $rollover['rollover']['effect'] )
			) {
				$rollover['rollover']['effect'] = 'fade';
			}
			if ( 
				! isset( $rollover['rollover']['speed'] ) 
				|| empty( $rollover['rollover']['speed'] )
			) {
				$rollover['rollover']['speed'] = 5;
			}
			if ( ! isset( $rollover['rollover']['count'] ) ) {
				$rollover['rollover']['count'] = 0;
			}
			if ( 
				! isset( $rollover['pagination']['cache_pages'] ) 
				|| empty( $rollover['pagination']['cache_pages'] )
			) {
				$rollover['pagination']['cache_pages'] = 1;
			}
			if ( 
				! isset( $rollover['pagination']['preload_pages'] ) 
				|| empty( $rollover['pagination']['preload_pages'] )
			) {
				$rollover['pagination']['preload_pages'] = 1;
			}
			if ( ! isset( $rollover['pagination']['callback_next'] ) ) {
				$rollover['pagination']['callback_next'] = '';
			}
			$out .= 'jQuery("#wpv-view-layout-' . $id . '").wpvRollover({id: "' . $id . '"'
                    . ', effect: "' . esc_js( $rollover['rollover']['effect'] ) . '"'
                    . ', speed: ' . esc_js( $rollover['rollover']['speed'] )
                    . ', page: 1'
					. ', count: ' . esc_js( $rollover['rollover']['count'] )
                    . ', cache_pages: ' . esc_js( $rollover['pagination']['cache_pages'] )
                    . ', preload_pages: ' . esc_js( $rollover['pagination']['preload_pages'] )
                    . ', spinner: "' . $spinner . '"'
                    . ', spinner_image: "' . $spinner_image . '"'
                    . ', callback_next: "' . esc_js( $rollover['pagination']['callback_next'] ) . '"'
                    . '});' . "\r\n";
        }
        echo $out;
        ?>
                });
        </script>
        <?php
    }
}

// add a filter so we can set the correct language in WPML during pagination
add_filter( 'icl_current_language', 'wpv_ajax_pagination_lang' );

function wpv_ajax_pagination_lang( $lang ) {
    if (
		isset( $_POST['action'] ) 
		&& esc_attr( $_POST['action'] ) == 'wpv_get_page' 
		&& isset( $_POST['lang'] )
	) {
        $lang = esc_attr( $_POST['lang'] );
    }
    return $lang;
}

// Gets the new page for a view.

function wpv_ajax_get_page( $post_data ) {
    global $WP_Views, $post, $authordata, $id;
    
    // Fix a problem with WPML using cookie language when DOING_AJAX is set.
    $cookie_lang = null;
    if (
		isset( $_COOKIE['_icl_current_language'] ) 
		&& isset( $post_data['lang'] )
	) {
        $cookie_lang = $_COOKIE['_icl_current_language'];
        $_COOKIE['_icl_current_language'] = $post_data['lang'];
    }
    
    // Switch WPML to the correct language.
    if ( isset( $post_data['lang'] ) ) {
        global $sitepress;
        if ( method_exists( $sitepress, 'switch_lang' ) ) {
            $sitepress->switch_lang( $post_data['lang'] );
        }
    }


    $_GET['wpv_paged'] = intval( esc_attr( $post_data['page'] ) );
    $_GET['wpv_view_count'] = esc_attr( $post_data['view_number'] );
    if (
		isset( $post_data['wpv_column_sort_id'] ) 
		&& esc_attr( $post_data['wpv_column_sort_id'] ) != 'undefined' 
		&& esc_attr( $post_data['wpv_column_sort_id'] ) != '' 
	) {
        $_GET['wpv_column_sort_id'] = esc_attr( $post_data['wpv_column_sort_id'] );
    }
    if (
		isset( $post_data['wpv_column_sort_dir'] ) 
		&& esc_attr( $post_data['wpv_column_sort_dir'] ) != 'undefined' 
		&& esc_attr( $post_data['wpv_column_sort_dir'] ) != ''
	) {
        $_GET['wpv_column_sort_dir'] = esc_attr( $post_data['wpv_column_sort_dir'] );
    }
    
	// $post_data['get_params'] holds arbitrary URL parameters from the page triggering the pagination
	// We have a hacky solution to keep array URL parameters 
	// by using the flag ##URLARRAYVALHACK## as the glue of the imploded array
    if ( isset( $post_data['get_params'] ) ) {
        foreach( $post_data['get_params'] as $key => $param ) {
            if ( ! isset( $_GET[$key] ) ) {
                $param_san = esc_attr( $param );
				// @hack alert!! We can not avoid this :-(
				if ( strpos( $param_san, '##URLARRAYVALHACK##' ) !== false ) {
					$_GET[$key] = explode( '##URLARRAYVALHACK##', $param_san );
				} else {
					$_GET[$key] = $param_san;
				}
            }
        }
    }
    
	// In other $post_data items, we are keeping the [] brackets for array flagging
    if ( isset( $post_data['dps_pr'] ) ) {
		foreach ( $post_data['dps_pr'] as $dps_pr_item ) {
			if ( is_array( $dps_pr_item ) && isset( $dps_pr_item['name'] ) && isset( $dps_pr_item['value'] ) ) {
				if ( strlen( $dps_pr_item['name'] ) < 2 ) {
					if ( !isset( $_GET[$dps_pr_item['name']] ) ) {
						$_GET[$dps_pr_item['name']] = esc_attr( $dps_pr_item['value'] );
					}
				} else {
					if ( strpos( $dps_pr_item['name'], '[]' ) === strlen( $dps_pr_item['name'] ) - 2 ) {
						$name = str_replace( '[]', '', $dps_pr_item['name'] );
						if ( !isset( $_GET[$name] ) ) {
							$_GET[$name] = array( esc_attr( $dps_pr_item['value'] ) );
						} else if ( is_array( $_GET[$name] ) ) {
							$_GET[$name][] = esc_attr( $dps_pr_item['value'] );
						}
					} else {
						if ( !isset( $_GET[$dps_pr_item['name']] ) ) {
							$_GET[$dps_pr_item['name']] = esc_attr( $dps_pr_item['value'] );
						}
					}
				}
			}
		}
    }
    
    if ( isset( $post_data['dps_general'] ) ) {
		$corrected_item = array();
		foreach ( $post_data['dps_general'] as $dps_pr_item ) {
			if ( is_array( $dps_pr_item ) && isset( $dps_pr_item['name'] ) && isset( $dps_pr_item['value'] ) ) {
				if ( strlen( $dps_pr_item['name'] ) < 2 ) {
					$_GET[$dps_pr_item['name']] = esc_attr( $dps_pr_item['value'] );
				} else {
					if ( strpos( $dps_pr_item['name'], '[]' ) === strlen( $dps_pr_item['name'] ) - 2 ) {
						$name = str_replace( '[]', '', $dps_pr_item['name'] );
						if ( !in_array( $name, $corrected_item ) ) {
							$corrected_item[] = $name;
							if ( isset( $_GET[$name] ) ) {
								unset( $_GET[$name] );
							}
						}
						if ( !isset( $_GET[$name] ) ) {
							$_GET[$name] = array( esc_attr( $dps_pr_item['value'] ) );
						} else if ( is_array( $_GET[$name] ) ) {
							$_GET[$name][] = esc_attr( $dps_pr_item['value'] );
						}
					} else {
						$_GET[$dps_pr_item['name']] = esc_attr( $dps_pr_item['value'] );
					}
				}
			}
		}
    }

	$view_data = json_decode( base64_decode( $post_data['view_hash'] ), true );
	
	// Adjust wpv_post_id, wpv_aux_parent_term_id, wpv_aux_parent_user_id
	// Needed for filters based on the current page or on nested Views

    if ( 
		isset( $post_data['post_id'] ) 
		&& is_numeric( $post_data['post_id'] )
	) {
		$_GET['wpv_post_id'] = esc_attr( $post_data['post_id'] );
        $post_id = esc_attr( $post_data['post_id'] );
        $post = get_post( $post_id );
        $authordata = new WP_User( $post->post_author );
        $id = $post->ID;
    }
	
	if ( 
		isset( $post_data['wpv_aux_parent_term_id'] ) 
		&& is_numeric( $post_data['wpv_aux_parent_term_id'] )
	) {
		$_GET['wpv_aux_parent_term_id'] = esc_attr( $post_data['wpv_aux_parent_term_id'] );
        $WP_Views->parent_taxonomy = esc_attr( $post_data['wpv_aux_parent_term_id'] );
    }
	
	if ( 
		isset( $post_data['wpv_aux_parent_user_id'] ) 
		&& is_numeric( $post_data['wpv_aux_parent_user_id'] )
	) {
		$_GET['wpv_aux_parent_user_id'] = esc_attr( $post_data['wpv_aux_parent_user_id'] );
        $WP_Views->parent_user = esc_attr( $post_data['wpv_aux_parent_user_id'] );
    }

    if ( esc_attr( $post_data['wpv_view_widget_id'] ) == 0 ) {
        // set the view count so we return the right view number after rendering.
        $view_id = $WP_Views->get_view_id( $view_data );
        $WP_Views->set_view_count( intval( esc_attr( $post_data['view_number'] ) ), $view_id );
        echo $WP_Views->short_tag_wpv_view( $view_data );
    } else {
        // set the view count so we return the right view number after rendering.
        $WP_Views->set_view_count( intval( esc_attr( $post_data['view_number'] ) ), esc_attr( $post_data['wpv_view_widget_id'] ) );
        $widget = new WPV_Widget();
        $args = array(
			'before_widget' => '',
            'before_title' => '',
            'after_title' => '',
            'after_widget' => ''
		);
        $widget->widget(
			$args, 
			array(
				'title' => '',
                'view' => esc_attr( $post_data['wpv_view_widget_id'] )
			)
		);
        echo $WP_Views->get_max_pages();
    }

    if ( $cookie_lang ) {
        // reset language cookie.
        $_COOKIE['_icl_current_language'] = $cookie_lang;
    }
}

/**
* wpv_pagination_router
*
* Renders the requested page for the requested View
*
* Check if the current loaded URL contains 'wpv-ajax-pagination' and if so load the View page requested
*
* @since unknown
*
* @note using a priority of 1 in the template_redirect action so we fire this early and no other can call this a 404
*/

add_action( 'template_redirect', 'wpv_pagination_router', 1 );

function wpv_pagination_router() {
    $bits = explode( "/", esc_attr( $_SERVER['REQUEST_URI'] ) );
    for ( $i = 0; $i < count( $bits ) - 1; $i++ ) {
        if ( $bits[$i] == 'wpv-ajax-pagination' ) {
            // get the post data. It's hex encoded json
            $post_data = $bits[$i + 1];
            $post_data = pack( 'H*', $post_data );
            
            $post_data = json_decode( $post_data, true );
            $charset = get_bloginfo( 'charset' );
			
			global $wp_query;
			if ( $wp_query->is_404 ) {
                $wp_query->is_404 = false;
            }
            
            header( 'HTTP/1.1 200 OK' );
            header( 'Content-Type: text/html;charset=' . $charset );
            echo '<html><body>';
            
            wpv_ajax_get_page( $post_data );
            
            echo '</body></html>';
            
            exit;
        }
    }
}
