<?php

/**
* Check the existence of a kind of View (normal or archive)
*
* @param $query_mode kind of View object: normal or archive
* @return array() of relevant Views if they exists or false if not
*/

function wpv_check_views_exists( $query_mode ) {
	$all_views_ids = _wpv_get_all_view_ids($query_mode);
	if ( count( $all_views_ids ) != 0 ) {
		return $all_views_ids;
	} else {
		return false;
	}
}

/**
* Get the IDs for all Views of a kind of View (normal or archive)
*
* @param $view_query_mode kind of View object: normal or archive
* @return array() of relevant Views if they exists or empty array if not
*/

function _wpv_get_all_view_ids( $view_query_mode ) {
	global $wpdb, $WP_Views;
	$q = ( 'SELECT ID FROM ' . $wpdb->prefix . 'posts WHERE post_type="view"' );
	$all_views = $wpdb->get_results( $q );
	$view_ids = array();
	foreach ( $all_views as $key => $view ) {
		$settings = $WP_Views->get_view_settings( $view->ID );
		if( $settings['view-query-mode'] != $view_query_mode ) {
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
	
	$posts = $wpdb->get_col( $wpdb->prepare( "SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} WHERE post_type='%s' AND post_status!='auto-draft'", $content_type ) );
	$count = sizeof( $posts );
	if ( $count > 0 ) {
		$posts = "'" . implode( "','", $posts ) . "'";
		$set_count = $wpdb->get_var( "SELECT COUNT(post_id) FROM {$wpdb->postmeta} WHERE
						meta_key='_views_template' AND meta_value='{$template_id}'
						AND post_id IN ({$posts})" );
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

function wpv_update_dissident_posts_from_template(  $template_id, $content_type ) {
	global $wpdb;
	
	$posts = $wpdb->get_col( "SELECT {$wpdb->posts}.ID FROM {$wpdb->posts}  WHERE post_type='{$content_type}'" );

	$count = sizeof( $posts );
	$updated_count = 0;
	if ( $count > 0 ) {
		foreach ( $posts as $post ) {
			$template_selected = get_post_meta( $post, '_views_template', true );
			if ( $template_selected != $template_id ) {
				update_post_meta( $post, '_views_template',$template_id );
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
*    $return['warning'] = There is something wrong, but keep going
*    $return['error'] = This view does not allow parametric searches
*
* @since 1.6.0
*/

function wpv_count_filter_controls( $view_settings ) {
	
	if ( !isset( $view_settings['query_type'] ) || !is_array( $view_settings['query_type'] ) || empty( $view_settings['query_type'] ) || $view_settings['query_type'][0] != 'posts' ) {
		return array( 'error' => __('This View does not list posts', 'wpv-views') );
	}
	
	if ( !isset( $view_settings['filter_meta_html'] ) || empty( $view_settings['filter_meta_html'] ) ) {
		return array( 'error' => __('Filter MetaHTML is empty', 'wpv-views') );
	}
	
	$return = array();
	$return['pr'] = 0;
	$return['cf'] = 0;
	$return['tax']= 0;
	
	$filter_controls_by_tag = substr_count( $view_settings['filter_meta_html'], '[wpv-control ' );
	$filter_controls_by_tag += substr_count( $view_settings['filter_meta_html'], '[wpv-control-set ' );
	
	if ( !isset( $view_settings['filter_controls_mode'] ) ) {
		$view_settings['filter_controls_mode'] = array();
	}
	
	if ( !is_array( $view_settings['filter_controls_mode'] ) ) {
		return array( 'error' => __('Something on the filter_controls_mode is broken', 'wpv-views') );
	}
	
	if ( count( $view_settings['filter_controls_mode'] ) == $filter_controls_by_tag ) {
		// Great! All filters were create using the GUI
		$filter_controls_by_type = array_count_values( $view_settings['filter_controls_mode'] );
		if ( isset( $filter_controls_by_type['rel'] ) && $filter_controls_by_type['rel'] > 0 ) {
			$return['pr'] = 1;
		}
		if ( isset( $filter_controls_by_type['cf'] ) && $filter_controls_by_type['cf'] > 0 ) {
			$return['cf'] = $filter_controls_by_type['cf'];
		}
		if ( isset( $filter_controls_by_type['slug'] ) && $filter_controls_by_type['slug'] > 0 ) {
			$return['tax'] = $filter_controls_by_type['slug'];
		}
		if ( $filter_controls_by_tag != $return['pr'] + $return['cf'] + $return['tax'] ) {
			// Something went wrong! There are filters using another key...
			return array( 'error' => __('Something on the filter_controls_mode has an invalid key', 'wpv-views') );
		}
	} else {
		// Mmmmm... There are filters created outside the GUI or we have a BETWEEN here
		foreach ( $view_settings as $v_key => $v_val ) {
			if ( $v_key == 'post_relationship_mode' && is_array( $v_val ) && in_array( 'url_parameter', $v_val ) ) {
				$return['pr'] = 1;
			} else if ( strpos( $v_key, 'custom-field-' ) === 0 && strpos( $v_key, '_value' ) === strlen( $v_key ) - strlen( '_value' ) && strpos( $v_val, 'URL_PARAM' ) !== false ) {
				$return['cf'] += substr_count( $v_val, 'URL_PARAM' );
			} else if ( strpos( $v_key, 'tax_' ) === 0 && strpos( $v_key, '_relationship' ) === strlen( $v_key ) - strlen( '_relationship' ) && $v_val == 'FROM URL' ) {
				$return['tax'] += 1;
			}
		}
		if ( $filter_controls_by_tag < $return['pr'] + $return['cf'] + $return['tax'] ) {
			// Something went wrong! There are filters using another key...
			$return['warning'] = __('Your View contains more URL based filters than parametric search controls in the Filter HTML textarea', 'wpv-views');
		} else if ( $filter_controls_by_tag > $return['pr'] + $return['cf'] + $return['tax'] ) {
			$return['warning'] = __('Your View contains more parametric search controls in the Filter HTML textarea than URL based filters', 'wpv-views');
		}
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