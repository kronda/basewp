<?php

/**
* wpv_check_views_exists
*
* Checks the existence of Views given a query mode.
*
* @param (string|array) $query_mode Kind of View object: 'normal' or 'archive' (or 'layouts-loop'). 
* @param $args = array(
* 	@param $args['status'] (array|false) the View status to be counted, if needed
* 	@param $args['orderby'] (string|false) the sorting order, if needed
*
* @return (array|false) array of View IDs or false
*
* @since unknown
*/

function wpv_check_views_exists( $query_mode, $args = array() ) {
	$defaults = array(
		'post_status' => false,
		'orderby' => false
	);
	$args = wp_parse_args( $args, $defaults );
	$all_views_ids = _wpv_get_all_view_ids( $query_mode, $args );
	if ( count( $all_views_ids ) != 0 ) {
		return $all_views_ids;
	} else {
		return false;
	}
}


/**
 * _wpv_get_all_view_ids
 *
 * Get the IDs for all Views of a kind of View (normal or archive)
 *
 * @param string|array $view_query_mode Kind of View object: 'normal' or 'archive' (or 'layouts-loop'). 
 * @param $args = array(
 * 	@param $args['status'] (array|false) the View status to be counted, if needed
 * 	@param $args['orderby'] (string|false) the sorting order, if needed
 * )
 * 
 * @return array() of relevant Views if they exists or empty array if not
 *
 * @since unknown
 */
function _wpv_get_all_view_ids( $view_query_mode, $args = array() ) {
	global $wpdb, $WP_Views;
	$view_status_string = "";
	$order_by_string = "";
	$post_type = 'view';
	$defaults = array(
		'post_status' => false,
		'orderby' => false
	);
	$values_to_prepare = array();
	$args = wp_parse_args( $args, $defaults );
	$values_to_prepare[] = $post_type;
	if ( $args['post_status'] ) {
		$view_stati = is_array( $args['post_status'] ) ? $args['post_status'] : array( $args['post_status'] );
		$view_stati_count = count( $view_stati );
		$view_stati_placeholders = array_fill( 0, $view_stati_count, '%s' );
		$view_status_string = " AND post_status IN (" . implode( ",", $view_stati_placeholders ) . ")";
		foreach ( $view_stati as $view_st ) {
			$values_to_prepare[] = $view_st;
		}
	}
	if ( $args['orderby'] ) {
		$orderby = $args['orderby'];
		$order_by_string = " ORDER BY %s";
		$values_to_prepare[] = $orderby;
	}
	$all_views = $wpdb->get_results( 
		$wpdb->prepare( 
			"SELECT ID FROM {$wpdb->posts} 
			WHERE post_type = %s 
			{$view_status_string} 
			{$order_by_string}",
			$values_to_prepare 
		) 
	);
	$view_ids = array();
	$view_query_mode = is_array( $view_query_mode ) ? $view_query_mode : array( $view_query_mode );
	foreach ( $all_views as $key => $view ) {
		$settings = $WP_Views->get_view_settings( $view->ID );
		if ( ! in_array( $settings['view-query-mode'], $view_query_mode ) ) {
			unset( $all_views[$key] );
		} else {
			$view_ids[] = $view->ID;
		}
	}
	return $view_ids;
}


/**
* wpv_count_dissident_posts_from_template
*
* Counts the amount of posts of a given type that do not use a given Template and creates the HTML structure to notify about it
* Used on the Views popups for the Content Templates listing page on single usage and for the Template edit screen
*
* @param $template_id the ID of the Content Template we want to check against
* @param $content_type the post type to check
* @param $message_header (optional) to override the default message on the HTML structure header "Do you want to apply to all?"
*
* @return nothing
*
* @since 1.5.1
*/

function wpv_count_dissident_posts_from_template( $template_id, $content_type, $message_header = null ) {
	global $wpdb;
	
	if ( is_null( $message_header ) ) {
		$message_header = __('Do you want to apply to all?','wpv-views');
	}
	
	$posts = $wpdb->get_col( 
		$wpdb->prepare( 
			"SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} 
			WHERE post_type = %s 
			AND post_status != 'auto-draft'", 
			$content_type 
		) 
	);
	$count = sizeof( $posts );
	if ( $count > 0 ) {
		$posts = "'" . implode( "','", $posts ) . "'";
		$set_count = $wpdb->get_var( 
			$wpdb->prepare(
				"SELECT COUNT(post_id) FROM {$wpdb->postmeta} 
				WHERE meta_key = '_views_template' 
				AND meta_value = %s
				AND post_id IN ({$posts})",
				$template_id
			)
		);
		if ( ( $count - $set_count ) > 0 ) {
			$ptype = get_post_type_object( $content_type );
			$type_label = $ptype->labels->singular_name;
			$message = sprintf( __( '%d %s uses a different Content Template.', 'wpv-views' ), ( $count - $set_count ) , $type_label );
			if ( ( $count - $set_count ) > 1 ){
				$type_label = $ptype->labels->name;
				$message = sprintf( __( '%d %s use a different Content Template.', 'wpv-views' ), ( $count - $set_count ) , $type_label );
			}
		?>

			<div class="wpv-dialog">
				<div class="wpv-dialog-header">
					<h2><?php echo $message_header; ?></h2>
					<i class="icon-remove js-dialog-close"></i>
				</div>
				<div class="wpv-dialog-content">
				<?php echo $message; ?>
				</div>
				<div class="wpv-dialog-footer">
					<button class="button js-dialog-close"><?php _e('Cancel','wpv-views') ?></button>
					<button class="button button-primary js-wpv-content-template-update-posts-process"
					data-type="<?php echo $content_type;?>"
					data-id="<?php echo $template_id;?>">
					<?php echo sprintf( __( 'Update %s now', 'wpv-views' ), $type_label ) ?></button>
				</div>
			</div>
		<?php
		}
	}
}

/**
* wpv_update_dissident_posts_from_template
*
* Updates all the of posts of a given type to use a given Template and creates the HTML structure to notify about it
* Used on the Views popups for the Content Templates listing page on single usage and for the Template edit screen
*
* @param $template_id the ID of the Content Template we want to check against
* @param $content_type the post type to check
*
* @return nothing
*
* @since 1.5.1
*/

function wpv_update_dissident_posts_from_template( $template_id, $content_type ) {
	global $wpdb;
	
	$posts = $wpdb->get_col( 
		$wpdb->prepare(
			"SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} 
			WHERE post_type = %s",
			$content_type
		)
	);

	$count = sizeof( $posts );
	$updated_count = 0;
	if ( $count > 0 ) {
		foreach ( $posts as $post ) {
			$template_selected = get_post_meta( $post, '_views_template', true );
			if ( $template_selected != $template_id ) {
				update_post_meta( $post, '_views_template', $template_id );
				$updated_count += 1;
			}
		}
	}
	echo '<div class="wpv-dialog wpv-dialog-change js-wpv-dialog-change">
				<div class="wpv-dialog-header">
					<h2>' . __('Success!', 'wpv-views') . '</h2>
				</div>
				<div class="wpv-dialog-content">
					<p>'. sprintf(__('All %ss were updated', 'wpv-views'), $content_type) .'</p>
				</div>
				<div class="wpv-dialog-footer">
					<button class="button-secondary js-dialog-close">'. esc_js( __('Close','wpv-views') ) .'</button>
				</div>
			</div>';
}

/**
* wpv_count_filter_controls
*
* Counts the number of different parametric searches by kind (tax, cf, pr)
*
* @param $view_settings
*
* @return (array) $return
*    $return['pr'] = 0;
*    $return['cf'] = 0;
*    $return['tax'] = 0;
*    $return['search'] = 0;
*    $return['warning'] = There is something wrong, but keep going
*    $return['error'] = This view does not allow parametric searches
*
* @since 1.6.0
*/

function wpv_count_filter_controls( $view_settings ) {
	
	$return = array();
	
	if (
		! isset( $view_settings['filter_meta_html'] )
		|| empty( $view_settings['filter_meta_html'] )
	) {
		$return['error'] = __('Filter MetaHTML is empty', 'wpv-views');
	}
	
	$return['pr'] = 0;
	$return['cf'] = 0;
	$return['tax']= 0;
	$return['search'] = 0;
	
	$filter_controls_by_tag = substr_count( $view_settings['filter_meta_html'], '[wpv-control ' );
	$filter_controls_by_tag += substr_count( $view_settings['filter_meta_html'], '[wpv-control-set ' );
	$filter_controls_by_tag += substr_count( $view_settings['filter_meta_html'], '[wpv-filter-search-box' );
	
	if ( ! isset( $view_settings['filter_controls_mode'] ) ) {
		$view_settings['filter_controls_mode'] = array();
	}
	
	if ( ! is_array( $view_settings['filter_controls_mode'] ) ) {
		$return['error'] = __('Something on the filter_controls_mode is broken', 'wpv-views');
	}
	$return['missing'] = array();
	foreach ( $view_settings as $v_key => $v_val ) {
		if ( $v_key == 'post_relationship_mode' && is_array( $v_val ) && in_array( 'url_parameter', $v_val ) ) {
			$return['pr'] = 1;
			if ( substr_count( $view_settings['filter_meta_html'], '[wpv-control-set ' ) !== 1 ) {
				$return['missing'][] = array(
					'type' => 'rel',
					'name' => __( 'post relationship', 'wpv-views' )
				);
			}
		} else if ( strpos( $v_key, 'custom-field-' ) === 0 && strpos( $v_key, '_value' ) === strlen( $v_key ) - strlen( '_value' ) && strpos( $v_val, 'URL_PARAM' ) !== false ) {
			$return['cf'] += substr_count( $v_val, 'URL_PARAM' );
			$v_array = explode( ',', $v_val );
			foreach ( $v_array as $v_candidate) {
				$v_candidate = trim( $v_candidate );
				if ( substr_count( $v_candidate, 'URL_PARAM' ) > 0 ) {
					$v_url = substr( $v_candidate, 10, -1 );
					if ( substr_count( stripslashes( $view_settings['filter_meta_html'] ), 'url_param="' . $v_url . '"' ) !== 1 ) {
						$return['missing'][] = array(
							'type' => 'cf',
							'name' => substr( $v_key, 13, -6 )
						);
					}
				}
			}
		} else if ( strpos( $v_key, 'tax_' ) === 0 && strpos( $v_key, '_relationship' ) === strlen( $v_key ) - strlen( '_relationship' ) && $v_val == 'FROM URL' ) {
			$return['tax'] += 1;
			$tax_name = substr( $v_key, 4, -13 );
			if ( substr_count( stripslashes( $view_settings['filter_meta_html'] ), 'taxonomy="' . $tax_name . '"' ) !== 1 ) {
				$return['missing'][] = array(
					'type' => 'tax',
					'name' => $tax_name
				);
			}
		} else if ( $v_key == 'search_mode' ) {
			$return['search'] = 1;
			if ( 
				substr_count( $view_settings['filter_meta_html'], '[wpv-filter-search-box' ) !== 1 
				&& substr_count( $view_settings['filter_meta_html'], 'url_param="wpv_post_search"' ) !== 1 
			) {
				$return['missing'][] = array(
					'type' => 'search',
					'name' => __( 'post search', 'wpv-views' )
				);
			}
		}
	}
	if ( $filter_controls_by_tag < $return['pr'] + $return['cf'] + $return['tax'] + $return['search'] ) {
		// Something went wrong! There are filters using another key...
		$return['warning'] = __('Your View contains more URL based filters than parametric search controls in the Filter HTML textarea', 'wpv-views');
	} else if ( $filter_controls_by_tag > $return['pr'] + $return['cf'] + $return['tax'] + $return['search'] ) {
		$return['warning'] = __('Your View contains more parametric search controls in the Filter HTML textarea than URL based filters', 'wpv-views');
	}
	return $return;
}

/**
* wpv_types_get_field_type
*
* Get the Types type of a given custom field
*
* @param $field_name (string) the field meta_key
*
* @return (string) the field type if any or an empty string if not
*/

function wpv_types_get_field_type( $field_name ) {
    $field_type = '';
	if ( !empty( $field_name ) ) {
		$opt = get_option( 'wpcf-fields', array() );
		if( $opt && !empty( $opt ) ) {
			if ( strpos( $field_name, 'wpcf-' ) === 0 ) {
				$field_name = substr( $field_name, 5 );
			}
			if ( isset( $opt[$field_name] ) && is_array( $opt[$field_name] ) && isset( $opt[$field_name]['type'] ) ) {
				$field_type = strtolower( $opt[$field_name]['type'] );
			}
		}
	}
    return $field_type;
}

/**
* wpv_types_get_field_name
*
* Get the Types name of a given custom field
*
* @param $field_name (string) the field meta_key
*
* @return (string) the Types field name if any or the same $field_name if not
*
* @since 1.8.0
*/

function wpv_types_get_field_name( $field_name ) {
    $field_nicename = $field_name;
	if ( ! empty( $field_name ) ) {
		$opt = get_option( 'wpcf-fields', array() );
		if( 
			$opt 
			&& ! empty( $opt ) 
		) {
			if ( strpos( $field_name, 'wpcf-' ) === 0 ) {
				$field_name = substr( $field_name, 5 );
			}
			if ( 
				isset( $opt[$field_name] ) 
				&& is_array( $opt[$field_name] ) 
				&& isset( $opt[$field_name]['name'] ) 
			) {
				$field_nicename = $opt[$field_name]['name'];
			}
		}
	}
    return $field_nicename;
}

/** 
* wpv_esc_like
* 
* In WordPress 4.0, like_escape() was deprecated, due to incorrect documentation and improper sanitization leading to a history of misuse
* To maintain compatibility with versions of WP before 4.0, we duplicate the logic of the replacement, wpdb::esc_like()
* 
* @since 1.6.2 
* 
* @see wpdb::esc_like() for more details on proper use. 
* 
* @param string $text The raw text to be escaped. 
* @return string Text in the form of a LIKE phrase. Not SQL safe. Run through wpdb::prepare() before use. 
*/ 
function wpv_esc_like( $text ) { 
   global $wpdb; 
   if ( method_exists( $wpdb, 'esc_like' ) ) { 
		return $wpdb->esc_like( $text ); 
   } else { 
		return like_escape( esc_sql( $text ) ); 
   } 
}

/**
* wpv_compat_get_split_term
*
* In WordPress 4.2, wp_get_split_term() was introduced to get the new term_id for a term that had been splitted 
* because its term_id was shared across several taxonomies
*
* @since 1.8.0
*
* @param int $term_id The term_id to check
* @param string $taxonomy The taxonomy to get the term_id from
-*
* @return bool|int The new term_id if it has changed and the function is available, false otherwise
*/

function wpv_compat_get_split_term( $term_id, $taxonomy ) {
	if ( function_exists( 'wp_get_split_term' ) ) {
		return wp_get_split_term( $term_id, $taxonomy );
	} else {
		return false;
	}
}

/**
 * Render a "toolset-alert-error" message and die.
 *
 * Renders a properly wrapped error message. Created to reduce code redundancy.
 *
 * @param string $message Text of the message to be rendered.
 *
 * @since 1.7
 */
function wpv_die_toolset_alert_error( $message ) {
	wp_die(	sprintf( '<div class="wpv-setting-container"><p class="toolset-alert toolset-alert-error">%s</p></div>', $message ) );
}

/**
* wpv_dismiss_dialog
*
* Sets a dialog as dismissed for the current user
*
* @param $dialog (string) the ID for the dialog
*
* @since 1.7
*/

function wpv_dismiss_dialog( $dialog = '' ) {
	$dialog = sanitize_key( $dialog );
	if ( empty( $dialog ) ) {
		return;
	}
	$user_id = get_current_user_id();
	$dismissed_dialogs = get_user_meta( $user_id, '_wpv_dismissed_dialogs', true );
	if ( ! is_array( $dismissed_dialogs ) || empty( $dismissed_dialogs ) ) {
		$dismissed_dialogs = array();
	}
	$dismissed_dialogs[ $dialog ] = 'yes';
	update_user_meta( $user_id, '_wpv_dismissed_dialogs', $dismissed_dialogs );
	// Remove the old usermeta field that we used when displaying dialogs after parametric and pagination insertion
	delete_user_meta( $user_id, 'wpv_view_editor_help_dismiss' );
}

/**
* wpv_dismiss_pointer
*
* Sets a pointer as dismissed for the current user
*
* @param $pointer (string) the ID for the pointer
*
* @since 1.7
*/

function wpv_dismiss_pointer( $pointer = '' ) {
	$pointer = sanitize_key( $pointer );
	if ( empty( $pointer ) ) {
		return;
	}
	$user_id = get_current_user_id();
	$dismissed_pointers = get_user_meta( $user_id, '_wpv_dismissed_pointers', true );
	if ( ! is_array( $dismissed_pointers ) || empty( $dismissed_pointers ) ) {
		$dismissed_pointers = array();
	}
	$dismissed_pointers[ $pointer ] = 'yes';
	update_user_meta( $user_id, '_wpv_dismissed_pointers', $dismissed_pointers );
	// Remove the old usermeta field that we used when displaying dialogs after parametric and pagination insertion
	delete_user_meta( $user_id, 'wpv_view_editor_help_dismiss' );
}

/**
 * wpv_get_the_archive_title
 * 
 * Duplicate of WordPress 4.1+ get_the_archive_title()
 * 
 * Link: https://developer.wordpress.org/reference/functions/get_the_archive_title/
 * 
 * We provide our own function so as to decrease dependencies
 * 
 * @since 1.8
 * 
 * @return type string 
 */
function wpv_get_the_archive_title() {
    if ( is_category() ) {
        $title = sprintf( __( 'Category: %s' ), single_cat_title( '', false ) );
    } elseif ( is_tag() ) {
        $title = sprintf( __( 'Tag: %s' ), single_tag_title( '', false ) );
    } elseif ( is_author() ) {
        $title = sprintf( __( 'Author: %s' ), '<span class="vcard">' . get_the_author() . '</span>' );
    } elseif ( is_year() ) {
        $title = sprintf( __( 'Year: %s' ), get_the_date( _x( 'Y', 'yearly archives date format' ) ) );
    } elseif ( is_month() ) {
        $title = sprintf( __( 'Month: %s' ), get_the_date( _x( 'F Y', 'monthly archives date format' ) ) );
    } elseif ( is_day() ) {
        $title = sprintf( __( 'Day: %s' ), get_the_date( _x( 'F j, Y', 'daily archives date format' ) ) );
    } elseif ( is_tax( 'post_format' ) ) {
        if ( is_tax( 'post_format', 'post-format-aside' ) ) {
            $title = _x( 'Asides', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
            $title = _x( 'Galleries', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
            $title = _x( 'Images', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
            $title = _x( 'Videos', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
            $title = _x( 'Quotes', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
            $title = _x( 'Links', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
            $title = _x( 'Statuses', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
            $title = _x( 'Audio', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
            $title = _x( 'Chats', 'post format archive title' );
        }
    } elseif ( is_post_type_archive() ) {
        $title = sprintf( __( 'Archives: %s' ), post_type_archive_title( '', false ) );
    } elseif ( is_tax() ) {
        $tax = get_taxonomy( get_queried_object()->taxonomy );
        /* translators: 1: Taxonomy singular name, 2: Current taxonomy term */
        $title = sprintf( __( '%1$s: %2$s' ), $tax->labels->singular_name, single_term_title( '', false ) );
    } else {
        $title = __( 'Archives' );
    }
 
    return $title;
}


/**
 * Safely retrieve a key from $_POST variable.
 *
 * This is a wrapper for wpv_get_from_array(). See that for more information.
 *
 * @param string $key See wpv_getarr().
 * @param mixed $default See wpv_getarr().
 * @param null|array $valid See wpv_getarr().
 *
 * @return mixed See wpv_getarr().
 *
 * @since 1.8
 */
function wpv_getpost( $key, $default = '', $valid = null ) {
    return wpv_getarr( $_POST, $key, $default, $valid );
}


/**
 * Safely retrieve a key from $_GET variable.
 *
 * This is a wrapper for wpv_get_from_array(). See that for more information.
 *
 * @param string $key See wpv_getarr().
 * @param mixed $default See wpv_getarr().
 * @param null|array $valid See wpv_getarr().
 *
 * @return mixed See wpv_getarr().
 *
 * @since 1.8
 */
function wpv_getget( $key, $default = '', $valid = null ) {
    return wpv_getarr( $_GET, $key, $default, $valid );
}


/**
 * Safely retrieve a key from given array (meant for $_POST, $_GET, etc).
 *
 * Checks if the key is set in the source array. If not, default value is returned. Optionally validates against array
 * of allowed values and returns default value if the validation fails.
 *
 * @param array $source The source array.
 * @param string $key The key to be retrieved from the source array.
 * @param mixed $default Default value to be returned if key is not set or the value is invalid. Optional.
 *     Default is empty string.
 * @param null|array $valid If an array is provided, the value will be validated against it's elements.
 *
 * @return mixed The value of the given key or $default.
 *
 * @since 1.8
 */
function wpv_getarr( &$source, $key, $default = '', $valid = null ) {
    if( isset( $source[ $key ] ) ) {
        $val = $source[ $key ];
        if( is_array( $valid ) && !in_array( $val, $valid ) ) {
            return $default;
        }
        return $val;
    } else {
        return $default;
    }
}


/**
 * Retrieve a modified URL with query string, omitting empty query arguments.
 *
 * Behaves exactly like add_query_arg(), except that it omits arguments with
 * value of empty string.
 *
 * @since 1.7
 *
 * @link http://codex.wordpress.org/Function_Reference/add_query_arg
 *
 * @param array $args Associative array of argument names and their values.
 * @param string $url Existing URL.
 *
 * @return New URL query string.
 */
function wpv_maybe_add_query_arg( $args, $url ) {
    foreach( $args as $key => $val ) {
        if( '' === $val ) {
            unset( $args[ $key ] );
        }
    }
    return add_query_arg( $args, $url );
}
