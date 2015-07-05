<?php

/**
* Update messages for regular edit screens
*
* @param $messages
* @return $messages
*/

add_filter('post_updated_messages', 'wpv_post_updated_messages_filter', 9999);

function wpv_post_updated_messages_filter( $messages ) {
	global $post;

	$post_type = get_post_type();
	// DEPRECATED
	// We have now our own edit pages, so this is not fired anymore
	// Commented out in 1.7
	/*
	if ( $post_type == 'view' ) {
		$messages['view'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => __('View updated.', 'wpv-views'),
			2 => __('Custom field updated.'),
			3 => __('Custom field deleted.'),
			4 => __('View updated.', 'wpv-views'),
			5 => isset($_GET['revision']) ? sprintf( __('View restored to revision from %s', 'wpv-views'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => __('View published.', 'wpv-views'),
			7 => __('View saved.', 'wpv-views'),
			8 => __('View submitted.', 'wpv-views'),
			9 => sprintf( __('View scheduled for: <strong>%1$s</strong>.', 'wpv-views'),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ) ),
			10 => __('View draft updated', 'wpv-views'),
			);
	}
	*/
	if ( $post_type == 'view-template' ) {
		$messages['view-template'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => __('Content template updated.', 'wpv-views'),
			2 => __('Custom field updated.'),
			3 => __('Custom field deleted.'),
			4 => __('Content template updated.', 'wpv-views'),
			/* translators: %s: date and time of the revision */
			5 => isset($_GET['revision']) ? sprintf( __('Content template restored to revision from %s', 'wpv-views'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => __('Content template published.', 'wpv-views'),
			7 => __('Content template saved.', 'wpv-views'),
			8 => __('Content template submitted.', 'wpv-views'),
			9 => sprintf( __('Content template scheduled for: <strong>%1$s</strong>.', 'wpv-views'),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ) ),
			10 => __('Content template draft updated', 'wpv-views'),
			);
	}
	return $messages;
}

/**
*
* Function wpv_redirect_admin_listings
*
* Prevents users from accessing the natural listing pages that WordPress creates for Views and Content Templates
* and redirects them to the new listing pages
*
*/

add_action('admin_init', 'wpv_redirect_admin_listings');

function wpv_redirect_admin_listings(){
	global $pagenow;
	/* Check current admin page. */
	if ( $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'view' ) {
		wp_redirect(admin_url('/admin.php?page=views', 'http'), 301);
		exit;
	} elseif ( $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'view-template' ) {
		wp_redirect(admin_url('/admin.php?page=view-templates', 'http'), 301);
		exit;
	}
}

function wpv_render_checkboxes( $values, $selected, $name ) { // TODO only used in old Status Filter, safe to remove
	$checkboxes = '<ul>';
	foreach ( $values as $value ) {

		if ( in_array( $value, $selected ) ) {
			$checked = ' checked="checked"';
		} else {
			$checked = '';
		}
		$checkboxes .= '<li><label><input type="checkbox" name="_wpv_settings[' . $name . '][]" value="' . $value . '"' . $checked . ' />&nbsp;' . $value . '</label></li>';

	}
	$checkboxes .= '</ul>';

	return $checkboxes;
}
/*
* DEPECATED
* Commented out in 1.7

function wpv_render_filter_td( $row, $id, $name, $summary_function, $selected, $data ) { // TODO only used in old Status Filter, safe to remove

	$td = '<td><img src="' . WPV_URL . '/res/img/delete.png" onclick="on_delete_wpv_filter(\'' . $row . '\')" style="cursor: pointer" />';
	$td .= '<td class="wpv_td_filter">';
	$td .= "<div id=\"wpv-filter-" . $id . "-show\">\n";
	$td .= call_user_func($summary_function, $selected);
	$td .= "</div>\n";
	$td .= "<div id=\"wpv-filter-" . $id . "-edit\" style='display:none'>\n";

	$td .= '<fieldset>';
	$td .= '<legend><strong>' . $name . ':</strong></legend>';
	$td .= '<div>' . $data . '</div>';
	$td .= '</fieldset>';
	ob_start();
	?>
		<input class="button-primary" type="button" value="<?php echo __('OK', 'wpv-views'); ?>" name="<?php echo __('OK', 'wpv-views'); ?>" onclick="wpv_show_filter_<?php echo $id; ?>_edit_ok()"/>
		<input class="button-secondary" type="button" value="<?php echo __('Cancel', 'wpv-views'); ?>" name="<?php echo __('Cancel', 'wpv-views'); ?>" onclick="wpv_show_filter_<?php echo $id; ?>_edit_cancel()"/>
	<?php
	$td .= ob_get_clean();
	$td .= '</div></td>';

	return $td;
}
*/

/**
 * Generate default View settings or layout settings.
 *
 * This is now merely a wrapper around wpv_view_default_settings() and wpv_view_default_layout_settings().
 * Feel free to use those functions directly instead.
 *
 * @param string $settings Field: 'view_settings' or 'view_layout_settings'.
 * @param string $purpose Purpose of the view: 'all', 'pagination', 'slide', 'parametric' or 'full'.
 *
 * @return array Array with desired values or an empty array if invalid parameters are provided.
 *
 * @since unknown
 */
function wpv_view_defaults( $settings = 'view_settings', $purpose = 'full' ) {

	switch( $settings ) {

		case 'view_settings':
			return wpv_view_default_settings( $purpose );

		case 'view_layout_settings':
			return wpv_view_default_layout_settings( $purpose );

		default:
			return array();
	}

}


/**
 * Generate default View settings.
 *
 * Depending on a View purpose, generate default settings for a View.
 *
 * @param $purpose Purpose of the view: 'all', 'pagination', 'slide', 'parametric' or 'full'. For invalid values
 *     'full' is assumed.
 *
 * @return array Array with desired values.
 *
 * @since 1.7
 */
function wpv_view_default_settings( $purpose = 'full' ) {

	/* Set the initial values for the View settings.
	 * Note: taxonomy_type is set in wpv-section-query-type.php to use the first available taxonomy. */
	$defaults = array(
			'view-query-mode' => 'normal',
			'view_description' => '',
			'view_purpose' => 'full',
			'query_type' => array( 'posts' ),
			'taxonomy_type' => array( 'category' ),
			'roles_type' => array( 'administrator' ),
			'post_type_dont_include_current_page' => true,
			'taxonomy_hide_empty' => true,
			'taxonomy_include_non_empty_decendants'	=> true,
			'taxonomy_pad_counts' => true, // check this setting application
			'orderby' => 'post_date',
			'order'	=> 'DESC',
			'taxonomy_orderby' => 'name',
			'taxonomy_order' => 'DESC',
			'users_orderby' => 'user_login',
			'users_order' => 'ASC',
			'limit'	=> -1,
			'offset' => 0,
			'taxonomy_limit' => -1,
			'taxonomy_offset' => 0,
			'users_limit' => -1,
			'users_offset' => 0,
			'posts_per_page' => 10,
			// TODO this needs carefull review
			'pagination' => array(
					'disable',
					'mode' => 'none',
					'preload_images' => true,
					'cache_pages' => true,
					'preload_pages'	=> true,
					'pre_reach'	=> 1,
					'page_selector_control_type' => 'drop_down',
					'spinner' => 'default',
					'spinner_image'	=> WPV_URL . '/res/img/ajax-loader.gif',
					'spinner_image_uploaded' => '',
					'callback_next'	=> '' ),
			'ajax_pagination' => array(
					'disable',
					'style'	=> 'fade',
					'duration' => 500 ),
			'rollover' => array(
					'preload_images' => true,
					'posts_per_page' => 1,
					'speed'	=> 5,
					'effect' => 'fade',
					'duration' => 500 ),
			'filter_meta_html_state' => array(
					'html' => 'on',
					'css' => 'off',
					'js' => 'off',
					'img' => 'off' ),
			'filter_meta_html' => "[wpv-filter-start hide=\"false\"]\n[wpv-filter-controls][/wpv-filter-controls]\n[wpv-filter-end]",
			'filter_meta_html_css' => '',
			'filter_meta_html_js' => '',
			'layout_meta_html_state' => array(
					'html' => 'on',
					'css' => 'off',
					'js' => 'off',
					'img' => 'off' ),
			'layout_meta_html_css' => '',
			'layout_meta_html_js' => '' );

	// purpose-specific modifications
	$defaults['view_purpose'] = $purpose;

	switch( $purpose ) {

		case 'all':
			$defaults['sections-show-hide'] = array(
				'pagination' => 'off',
				'filter-extra-parametric' => 'off',
				'filter-extra' => 'off'	);
			break;

		case 'pagination':
			$defaults['pagination'][0] = 'enable'; // disable --> enable
			$defaults['pagination']['mode'] = 'paged';
			$defaults['sections-show-hide'] = array( 'limit-offset' => 'off' );
			break;

		case 'slider':
			$defaults['pagination'][0] = 'enable'; // disable --> enable
			$defaults['pagination']['mode'] = 'rollover';
			$defaults['sections-show-hide'] = array( 'limit-offset' => 'off' );
			break;

		case 'parametric':
			$defaults['sections-show-hide'] = array(
					'query-options'	=> 'off',
					'limit-offset' => 'off',
					'pagination' => 'off',
					'content-filter' => 'off' );
			break;

		case 'full':
		default:
			$defaults['sections-show-hide'] = array( );
			// This has to stay here, because we're also catching invalid $purpose values.
			$defaults['view_purpose'] = 'full';
			break;
	}
	return $defaults;
}


/**
 * Generate default View layout settings.
 *
 * Depending on a View purpose, generate default settings for a View.
 *
 * @param $purpose Purpose of the view: 'all', 'pagination', 'slide', 'parametric' or 'full'. For invalid values
 *     'full' is assumed.
 *
 * @return array Array with desired values.
 *
 * @since 1.7
 */
function wpv_view_default_layout_settings( $purpose ) {

	// almost all of this settings are only needed to create the layout on the fly, so they are not needed here
	$defaults = array(
			'additional_js' => '',
			'layout_meta_html' =>
					"[wpv-layout-start]\n"
					. "	[wpv-items-found]\n"
					. "	<!-- wpv-loop-start -->\n"
					. "		<wpv-loop>\n"
					. "		</wpv-loop>\n"
					. "	<!-- wpv-loop-end -->\n"
					. "	[/wpv-items-found]\n"
					. "	[wpv-no-items-found]\n"
					. "		[wpml-string context=\"wpv-views\"]<strong>No items found</strong>[/wpml-string]\n"
					. "	[/wpv-no-items-found]\n"
					. "[wpv-layout-end]\n" );

	// Purpose-specific modifications
	switch( $purpose ) {

		case 'all':
		case 'pagination':
			// nothing to do here... yet
			break;

		case 'slider':
			// Generate full loop output settings
			$result = wpv_generate_view_loop_output(
					'unformatted',
					array(),
					array() );
			$defaults = $result['loop_output_settings'];
			break;

		case 'parametric':
		case 'full':
		default:
			// nothing to do here... yet
			break;
	}
	return $defaults;
}


/**
* Set default WordPress Archives settings and layout settings
*
* @param $settings field: view_settings or view_layout_settings
* @return array() with desired values
*/

function wpv_wordpress_archives_defaults( $settings = 'view_settings' ) {
	$defaults = array(
		'view_settings' => array(
			'view-query-mode'			=> 'archive',
			'sections-show-hide'			=> array(
									'content'		=> 'off',
								)
		),
		'view_layout_settings' => array( // almost all of this settings are only needed to create the layout on the fly, so they are not needed here
			'additional_js'				=> '',
			'layout_meta_html'			=> "[wpv-layout-start]
	[wpv-items-found]
	<!-- wpv-loop-start -->
		<wpv-loop>
		</wpv-loop>
	<!-- wpv-loop-end -->
	[/wpv-items-found]
	[wpv-no-items-found]
		[wpml-string context=\"wpv-views\"]<strong>No posts found</strong>[/wpml-string]
	[/wpv-no-items-found]
[wpv-layout-end]",
		),
	);
	return $defaults[$settings];
}



// NOT needed for Views anymore DEPRECATED NOTE Layouts might find this usefull
// DEPRECATED I would delete this, check other plugins usage
function _wpv_get_all_views($view_query_mode) {
	global $wpdb, $WP_Views;
	$all_views = $wpdb->get_results( 
		"SELECT ID, post_title FROM {$wpdb->posts} 
		WHERE post_status = 'publish' 
		AND post_type = 'view'"
	);
	foreach( $all_views as $key => $view ) {
		$settings = $WP_Views->get_view_settings( $view->ID );
		if( $settings['view-query-mode'] != $view_query_mode ) {
			unset( $all_views[$key] );
		}
	}
	return $all_views;
}

/**
* DEPRECATED
*
* Commented out in 1.7
function _wpv_field_views_by_search($all_views, $search_term) {

//	if ( !empty( $search_term ) ) {
//		foreach($all_views as $key => $view) {
//			// check the search
//			$description = get_post_meta($view->ID, '_wpv_description', true);
//			if (strpos($description, $search_term) === FALSE && strpos($view->post_title, $search_term) === FALSE) {
//				unset($all_views[$key]);
//			}
//		}
//	}

	foreach($all_views as $key => $view) {
		$all_views[$key] = $view->ID;
	}

	$all_views = implode(',', $all_views);

	return $all_views;
}
*/

/**
* Check the existence of a kind of View NOT needed for Views anymore DEPRECATED
*
* @param $query_mode kind of View object: normal or archive
* @return boolean
*/
// DEPRECATED I would delete this, check other plugins usage
function wpv_check_items_exists( $query_mode ) {
	$all_views = _wpv_get_all_views($query_mode);

    return count( $all_views ) != 0;
}

/**
* Cleans the WordPress Media popup to be used in Views and WordPress Archives
*
* @param $strings elements to be included
* @return $strings without the unwanted sections
*/

add_filter( 'media_view_strings', 'custom_media_uploader' );

function custom_media_uploader( $strings ) {
	if ( isset( $_GET['page'] ) && ( 'view-archives-editor' == $_GET['page'] || 'views-editor' == $_GET['page'] ) ) {
		unset( $strings['createGalleryTitle'] ); //Create Gallery
	}
	return $strings;
}

/**
* Add View button and dialog
*
* @param $editor_id ID for the relevant textarea, to be set as active editor
* @param $inline TODO document this
*
* @return $strings without the unwanted sections
*/

function wpv_add_v_icon_to_codemirror( $editor_id, $inline = false ) {

    global $WP_Views;
    $view = '';
    if ( isset($_GET['view_id']) ){
        $view = $_GET['view_id'];
    }
    $is_taxonomy = false;
	$is_users = false;
    $post_hidden = '';
    $tax_hidden = ' hidden';
	$users_hidden = ' hidden';

    $meta = get_post_meta( $view, '_wpv_settings', true);
    if ( isset($meta['query_type']) && $meta['query_type'][0] == 'taxonomy'){
           $is_taxonomy = true;
           $post_hidden = ' hidden';
           $tax_hidden = '';
		   $users_hidden = ' hidden';
    }
	if ( isset($meta['query_type']) && $meta['query_type'][0] == 'users'){
           $is_users = true;
           $post_hidden = ' hidden';
           $tax_hidden = ' hidden';
		   $users_hidden = '';
    }


    $WP_Views->editor_addon = new Editor_addon('wpv-views',
            __('Insert Views Shortcodes', 'wpv-views'),
            WPV_URL . '/res/js/views_editor_plugin.js',
            WPV_URL_EMBEDDED . '/res/img/views-icon-black_16X16.png');

    if ( !$inline ){ echo '<div class="wpv-vicon-for-posts'. $post_hidden .'">';}

	if ( !$inline ){
	    add_short_codes_to_js( array('post', 'taxonomy', 'post-view'), $WP_Views->editor_addon );
	    $WP_Views->editor_addon->add_form_button('', $editor_id , true, true, true);
	}
	else{
		if ( empty($view) && isset($_POST['view_id']) ){
			$view = $_POST['view_id'];
			$meta = get_post_meta( $view, '_wpv_settings', true);
		}
		if ( !isset($meta['query_type'][0]) || ( isset($meta['query_type'][0]) && $meta['query_type'][0]=='posts' )){
			add_short_codes_to_js( array('post', 'taxonomy', 'post-view','body-view-templates-posts'), $WP_Views->editor_addon );
	    	$WP_Views->editor_addon->add_form_button('', $editor_id , true, true, true);
		}elseif( isset($meta['query_type'][0]) && $meta['query_type'][0]=='users' ){
			$WP_Views->editor_addon->add_users_form_button('', $editor_id, true);
		}
		elseif( isset($meta['query_type'][0]) && $meta['query_type'][0]=='taxonomy' ){
			remove_filter('editor_addon_menus_wpv-views', 'wpv_post_taxonomies_editor_addon_menus_wpv_views_filter', 11);
        	add_filter('editor_addon_menus_wpv-views', 'wpv_layout_taxonomy_V');
       		$WP_Views->editor_addon->add_form_button('', $editor_id, true, true, true);
        	remove_filter('editor_addon_menus_wpv-views', 'wpv_layout_taxonomy_V');
        	add_filter('editor_addon_menus_wpv-views', 'wpv_post_taxonomies_editor_addon_menus_wpv_views_filter', 11);
		}

	}

    if ( !$inline ){echo '</div>';  }

    if ( !$inline ){
        echo '<div class="wpv-vicon-for-taxonomy'. $tax_hidden .'">';
        remove_filter('editor_addon_menus_wpv-views', 'wpv_post_taxonomies_editor_addon_menus_wpv_views_filter', 11);
        add_filter('editor_addon_menus_wpv-views', 'wpv_layout_taxonomy_V');

        $WP_Views->editor_addon->add_form_button('', $editor_id, true, true, true);

        remove_filter('editor_addon_menus_wpv-views', 'wpv_layout_taxonomy_V');
        add_filter('editor_addon_menus_wpv-views', 'wpv_post_taxonomies_editor_addon_menus_wpv_views_filter', 11);
        echo '</div>';
    }
	if ( !$inline ){
        echo '<div class="wpv-vicon-for-users'. $users_hidden .'">';

        //add_filter('editor_addon_menus_wpv-views', 'wpv_layout_users_V');

        $WP_Views->editor_addon->add_users_form_button('', $editor_id, true);

        //remove_filter('editor_addon_menus_wpv-views', 'wpv_layout_users_V');
        //add_filter('editor_addon_menus_wpv-views', 'wpv_post_taxonomies_editor_addon_menus_wpv_views_filter', 11);
        echo '</div>';
    }
}

/**
 * Add usermeta V icon menu
 *
 *
 **/
function wpv_layout_users_V($menu) { // MAYBE DEPRECATED

    // remove post items and add taxonomy items.

    global $wpv_shortcodes;
    //print_r( $wpv_shortcodes );exit;
    $basic = __('Basic', 'wpv-views');
    $menu = array($basic => array());
    //print_r($menu);exit;
    /*$taxonomy = array('username',
                      'aim');

    foreach ($taxonomy as $key) {
        $menu[$basic][$wpv_shortcodes[$key][1]] = array($wpv_shortcodes[$key][1],
                                                                        $wpv_shortcodes[$key][0],
                                                                        $basic,
                                                                        '');
    }    */
    return $menu;

}

/**
* wpv_create_content_template
*
* Creates a new Content Template given a title and an optional suffix
*
* @param $title (string)
* @param $suffix (string)
* @param $force (boolean) whether to force the creation of the Template by incremental numbers added to the title in case it is already in use
* @param $content (string)
*
* @return (array) $return
*     'success' => (int) The ID of the CT created
*     'error' => (string) Error message
*     'title' => (string) The title of the CT created or the one that made this fail
*
* @since 1.7
*/

function wpv_create_content_template( $title, $suffix = '', $force = true, $content = '' ) {
    global $wpdb;
	$return = array();
	$real_suffix = '';
	if ( ! empty( $suffix ) ) {
		$real_suffix = ' - ' . $suffix;
	}
	if ( $force ) {
		$counter = 0;
		while ( $counter < 20 ) {
			$add = ' ' . $counter;
			if ( $counter == 0 ) {
				$add = '';
			}
			$template_title = $title . $real_suffix . $add;
			$existing = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT count(ID) FROM {$wpdb->posts} 
					WHERE ( post_title = %s OR post_name = %s ) 
					AND post_type = 'view-template' 
					LIMIT 1",
					$template_title,
					$template_title
				)
			);
			if ( $existing <= 0 ) {
				break;
			} else {
				$counter++;
			}
		}
	} else {
		$template_title = $title . $real_suffix;
		$existing = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT count(ID) FROM {$wpdb->posts} 
				WHERE ( post_title = %s OR post_name = %s ) 
				AND post_type = 'view-template' 
				LIMIT 1",
				$template_title,
				$template_title
			)
		);
		if ( $existing > 0 ) {
			$return['error'] = __( 'A Content Template with that title already exists. Please use another title.', 'wpv-views' );
			$return['title'] = $template_title;
			return $return;
		}
	}

	$template = array(
		'post_title'    => $template_title,
		'post_type'     => 'view-template',
		'post_content'  => $content,
		'post_status'   => 'publish'
	);

	$template_id = wp_insert_post( $template );
	update_post_meta( $template_id, '_wpv_view_template_mode', 'raw_mode' );
	update_post_meta( $template_id, '_wpv-content-template-decription', '' );
	$return['success'] = $template_id;
	$return['title'] = $template_title;
	return $return;
}

/**
* wpv_clone_content_template
*
* API to clone a Content Template
*
* @param (int) $origin_ct_id The original CT ID
* @param (array) $args Some modifiers
*     'title' => (string) If passed, use this title instead of the original CT
*     'force => (boolean) Whether to force the creation of the clone or bail on title duplication
*
* @return (array) $clone
*     'success' => (int) The ID of the CT created
*     'error' => (string) Error message
*     'title' => (string) The title of the CT created or the one that made this fail
*
* @since 1.7
*/

function wpv_clone_content_template( $origin_ct_id, $args = array() ) {
	$args_default = array(
		'title' => false,
		'force' => false
	);
	$args = wp_parse_args( $args, $args_default );
	$original_post = get_post( $origin_ct_id, ARRAY_A );
	$cloned_title = $original_post['post_title'];
	if ( $args['title'] ) {
		$cloned_title = $args['title'];
	}
	$clone = wpv_create_content_template( $cloned_title, '', $args['force'], $original_post['post_content'] );
	if ( isset( $clone['success'] ) ) {
		$origin_ct_meta = get_post_meta( $origin_ct_id );
		foreach ( $origin_ct_meta as $key => $value ) {
			if ( 
				$key != '_edit_lock' 
				&& $key != '_view_loop_id'
			) {
				update_post_meta( $clone['success'], $key, $value[0] );
			}
		}
	}
	return $clone;
}

/**
* wpv_create_view
*
* API function to create a new View
*
* @param $args (array) set of arguments for the new View
*    'title' (string) (semi-mandatory) Title for the View
*    'settings' (array) (optional) Array compatible with the View settings to override the defaults
*    'layout_settings' (array) (optional) Array compatible with the View layout settings to override the defaults
*
* @return (array) response of the operation, one of the following
*    $return['success] = View ID
*    $return['error'] = 'Error message'
*
* @since 1.6.0
*
* @note overriding default Views settings and layout settings must provide complete data when the element is an array, because it overrides them all.
*    For example, $args['settings']['pagination'] can not override just the "postsper page" options: it must provide a complete pagination implementation.
*    This might change and be corrected in the future, keeping backwards compatibility.
*
* @todo once we create a default layout for a View, we need to make sure that:
* - the _view_loop_template postmeat is created and updated - DONE
* - the fields added to that loop Template are stored in the layout settings - PENDING
* - check how Layouts can apply this all to their Views, to create a Bootstrap loop by default - PENDING
*/

function wpv_create_view( $args ) {
	global $wpdb;
	$return = array();
	// First, set the title
	if ( !isset( $args["title"] ) || $args["title"] == '' ) {
		$args["title"] = __('Unnamed View', 'wp-views');
	}
	// Check for already existing Views with that title
	$existing = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} 
			WHERE ( post_title = %s OR post_name = %s ) 
			AND post_type = 'view' 
			LIMIT 1",
			$args["title"],
			$args["title"]
		)
	);
	if ( $existing ) {
		$return['error'] = __( 'A View with that name already exists. Please use another name.', 'wpv-views' );
		return $return;
	}
	// Compose the $post to be created
	$post = array(
		'post_type'	=> 'view',
		'post_title'	=> $args["title"],
		'post_status'	=> 'publish',
		'post_content'	=> "[wpv-filter-meta-html]\n[wpv-layout-meta-html]"
	);
	$id = wp_insert_post( $post );
	if ( 0 != $id ) {
		if ( !isset( $args['settings'] ) || !is_array( $args['settings'] ) ) {
			$args['settings'] = array();
		}
		if ( !isset( $args['layout_settings'] ) || !is_array( $args['layout_settings'] ) ) {
			$args['layout_settings'] = array();
		}
		if ( !isset( $args['settings']["view-query-mode"] ) ) {
			$args['settings']["view-query-mode"] = 'normal';  // TODO check if view-query-mode is needed anymore, see below
		}
		if ( !isset( $args['settings']["view_purpose"] ) ) {
			$args['settings']["view_purpose"] = 'full';
		}

		$create_loop_template = false;
		$create_loop_template_suffix = '';
		$create_loop_template_content = '';
		$create_loop_template_layout = '';
		$add_archive_pagination = false;

		switch ( $args['settings']["view-query-mode"] ) {
			case 'archive':
				$view_normal_defaults = wpv_wordpress_archives_defaults( 'view_settings' );
				$view_normal_layout_defaults = wpv_wordpress_archives_defaults( 'view_layout_settings' );
				break;
			case 'layouts-loop':
				$view_normal_defaults = wpv_wordpress_archives_defaults( 'view_settings' );
				$view_normal_layout_defaults = wpv_wordpress_archives_defaults( 'view_layout_settings' );
				$create_loop_template = true;
				$create_loop_template_suffix = __('loop item', 'wpv-views' );
				$create_loop_template_content = "<h1>[wpv-post-title]</h1>\n[wpv-post-body view_template=\"None\"]\n[wpv-post-featured-image]\n"
					. sprintf(__('Posted by %s on %s', 'wpv-views'), '[wpv-post-author]', '[wpv-post-date]');
				$add_archive_pagination = true;
				break;
			default:
				$view_normal_defaults = wpv_view_defaults( 'view_settings', $args['settings']["view_purpose"] );
				$view_normal_layout_defaults = wpv_view_defaults( 'view_layout_settings', $args['settings']["view_purpose"] );
				if ( $args['settings']["view_purpose"] == 'slider' ) {
					$create_loop_template = true;
					$create_loop_template_suffix = __('slide', 'wpv-views' );
					$create_loop_template_content = '[wpv-post-link]';
				} else if ( $args['settings']["view_purpose"] == 'bootstrap-grid' ) {
					// Deprecated in Views 1.7, keep for backwards compatibility
					$args['settings']["view_purpose"] = 'full';
				}
				break;
		}

		if ( $create_loop_template ) {
			// @todo review
			// This creates the Template, but it does not adjust the Layout Wizard settings to use it, in case someone touches it
			$template = wpv_create_content_template( $args["title"], $create_loop_template_suffix, true, $create_loop_template_content );
			if ( isset ( $template['success'] ) ) {
				$template_id = $template['success'];
				if ( isset( $template['title'] ) ) {
					$template_title = $template['title'];
				} else {
					$template_object = get_post( $template_id );
					$template_title = $template_object->post_title;
				}
				// @todo here we should create the layout acordingly to the $create_loop_template_layout value
				$view_normal_layout_defaults['layout_meta_html'] = str_replace(
					"<wpv-loop>",
					"<wpv-loop>\n\t\t\t[wpv-post-body view_template=\"" . $template_title . "\"]",
					$view_normal_layout_defaults['layout_meta_html']
				);
				$view_normal_layout_defaults['included_ct_ids'] = $template_id;
				update_post_meta( $id, '_view_loop_template', $template_id );
				update_post_meta( $template_id, '_view_loop_id', $id );
				// @todo
				// I really hate this solution
				update_post_meta( $id, '_wpv_first_time_load', 'on' );
			}
		}
		
		if ( $add_archive_pagination ) {
			$view_normal_layout_defaults['layout_meta_html'] = str_replace(
				"[/wpv-items-found]",
				"[wpv-archive-pager-prev-page]\n\t\t[wpml-string context=\"wpv-views\"]Older posts[/wpml-string]\n\t[/wpv-archive-pager-prev-page]\n\t[wpv-archive-pager-next-page]\n\t\t[wpml-string context=\"wpv-views\"]Newer posts[/wpml-string]\n\t[/wpv-archive-pager-next-page]\n\t[/wpv-items-found]",
				$view_normal_layout_defaults['layout_meta_html']
			);
		}

		// Override the settings with our own
		foreach ( $args['settings'] as $key => $value ) {
			$view_normal_defaults[$key] = $args['settings'][$key];
		}
		// Override the layout settings with our own
		foreach ( $args['layout_settings'] as $key => $value ) {
			$view_normal_layout_defaults[$key] = $args['layout_settings'][$key];
		}
		// Set the whole View settings
		update_post_meta($id, '_wpv_settings', $view_normal_defaults);
		update_post_meta($id, '_wpv_layout_settings', $view_normal_layout_defaults);
		$return['success'] = $id;
	} else {
		$return['error'] = __( 'The View could not be created.', 'wpv-views' );
		return $return;
	}
	return $return;
}

/* NOTE: This function is also called from Layouts plugin */
function wpv_create_bootstrap_meta_html ($cols, $ct_title, $meta_html) {
	global $WPV_settings;

	$col_num = 12 / $cols;
	$output = '';
	$row_style = '';
	$col_style = 'col-sm-';
	$body = '[wpv-post-body view_template="' . $ct_title. '"]';

	//Row style and cols class for bootstrap 2.0

	if (class_exists('WPDD_Layouts_CSSFrameworkOptions')) {
		$bootstrap_ver = WPDD_Layouts_CSSFrameworkOptions::getInstance()->get_current_framework();
		$WPV_settings->wpv_bootstrap_version = str_replace('bootstrap-','',$bootstrap_ver);
	}else{
		//Load bootstrap version from views settings
		if ( ! isset( $WPV_settings->wpv_bootstrap_version ) ) {
            $WPV_settings->wpv_bootstrap_version = 2;
        }
    }

	if ( $WPV_settings->wpv_bootstrap_version == 2 ) {
        $row_style = ' row-fluid';
		$col_style = 'span';
	}

	$output .= "   <wpv-loop wrap=\"" . $cols . "\" pad=\"true\">\n";
	$ifone = '';
	//
	if ( $cols == 1){
		$ifone = '</div>';
	}
	$output .= "         [wpv-item index=1]\n";
	$output .= "            <div class=\"row" . $row_style . "\"><div class=\"" . $col_style . $col_num . "\">" . $body . "</div>" . $ifone . "\n";
	$output .= "         [wpv-item index=other]\n";
	$output .= "            <div class=\"" . $col_style . $col_num . "\">" . $body . "</div>\n";

	if ( $cols > 1){
		$output .= "         [wpv-item index=" . $cols . "]\n";
		$output .= "            <div class=\"" . $col_style . $col_num . "\">" . $body . "</div></div>\n";
	}

	$output .= "         [wpv-item index=pad]\n";
	$output .= "            <div class=\"" . $col_style . $col_num . "\"></div>\n";
	$output .= "         [wpv-item index=pad-last]\n";
	$output .= "            </div>\n";
	$output .= "    </wpv-loop>\n";

	return preg_replace('#\<wpv-loop(.*?)\>(.*)</wpv-loop>#is', $output, $meta_html);
}


/**
 * Helper function for producing "current" CSS class for Tab design for admin screens when a condition is met.
 *
 * Inspired by WordPress checked() and selected() functions. You can either provide two values, which will be then
 * compared to each other, or one boolean value determining whether the class "current" should be produced.
 *
 * @param mixed|bool $first_value First value to compare with the second value or a boolean if second value is null.
 * @param mixed|null $second_value Second value to compare or null if first value should be used as a boolean.
 *     Default is null.
 * @param bool $echo If true, the result will be also echoed.
 *
 * @return string The result: 'class="current"' or an empty string.
 *
 * @since 1.8
 */ 
function wpv_current_class( $first_value, $second_value = null, $echo = true ) {
	if( $second_value == null ) {
		$condition = (bool) $first_value;
	} else {
		$condition = ( $first_value == $second_value );
	}
	
	$result = $condition ? 'class="current"' : '';

	if( $echo ) {
		echo $result;
	}

	return $result;
}


/**
 * Replace occurences of a View/Content Template/WordPress Archive ID by another ID in Views' settings.
 *
 * Specifically, all options starting with 'views_template_' are processed.
 *
 * @param int $replace_what The ID to be replaced.
 * @param int $replace_by New value.
 * @param mixed $settings If null, Views options are obtained from global $WP_Views and also saved there afterwards.
 *     Otherwise, an array with Views options is expected and after processing it is not saved, but returned instead.
 *
 * @return Modified array of Views options if $settings was provided, nothing otherwise.
 *
 * @since 1.7
 */
function wpv_replace_views_template_options( $replace_what, $replace_by, $settings = null ) {
	if( null == $settings ) {
        global $WPV_settings;
        $settings = $WPV_settings;
		$save_options = true;
	} else {
		$save_options = false;
	}

	foreach ( $settings as $option_name => $option_value ) {
		if ( ( strpos( $option_name, 'views_template_' ) === 0 )
			&& $option_value == $replace_what )
		{
			$settings[ $option_name ] = $replace_by;
		}
	}

	if( $save_options ) {
        $settings->save();
	} else {
		return $settings;
	}
}


/**
 * Generate default loop output settings (former layout settings) for a View, based on chosen loop output style
 *
 * @param string $style Loop output style name, which must be one of the following values:
 *     - table
 *     - bootstrap-grid
 *     - table_of_fields
 *     - ordered_list
 *     - un_ordered_list
 *     - unformatted
 *
 * @param array $fields (
 *         Array of definitions of fields that will be present in the loop output. If an element is not present, empty
 *         string is used instead.
 *
 *         @type string $prefix Prefix, text before shortcode.
 *         @type string $shortcode The shortcode ('[shortcode]').
 *         @type string $suffix Text after shortcode.
 *         @type string $field_name Field name.
 *         @type string $header_name Header name.
 *         @type string $row_title Row title <TH>.
 *     )
 *
 * @param array $args(
 *         Additional arguments.
 *
 *         @type bool $include_field_names If the loop output style is table_of_fields, determines whether the rendered
 *             loop output will contain table header with field names. Optional. Default is true.
 *         @type int $tab_column_count Number of columns for the bootstrap-grid style. Optional. Default is 1.
 *         @type int $bootstrap_column_count Number of columns for the table style. Optional. Default is 1.
 *         @type int $bootstrap_version Version of Bootstrap. Mandatory for bootstrap-grid style, irrelephant
 *             otherwise. Must be 2 or 3.
 *         @type bool $add_container Argument for bootstrap-grid style. If true, enclose rendered html in a
 *             container div. Optional. Default is false.
 *         @type bool $add_row_class Argument for bootstrap-grid style. If true, a "row" class will be added to
 *             elements representing rows. For Bootstrap 3 it is added anyway. Optional. Default is false.
 *         @type bool $render_individual_columns Argument for bootstrap-grid style. If true, a wpv-item shortcode
 *             will be rendered for each singular column. Optional. Default is false.
 *         @type bool $render_only_wpv_loop If true, only the code that should be within "<!-- wpv-loop start -->" and
 *             "<!-- wpv-loop end -->" tags is rendered. Optional. Default is false.
 *         @type bool $use_loop_template Determines whether a Content Template will be used for field shortcodes.
 *             If true, the content of the CT will be returned in the 'ct_content' element and the loop output will
 *             contain shortcodes referencing it. In such case the argument loop_template_title is mandatory. Optional.
 *             Default is false.
 *         @type string $loop_template_title Title of the Content Template that should contain field shortcodes. Only
 *             relevant if use_loop_template is true, and in such case it is mandatory.
 *     )
 *
 * @return  null|array Null on error. Otherwise an array containing following elements:
 *     array(
 *         @type array loop_output_settings Loop Output settings for a View, as they should be stored in the database:
 *             array(
 *                 @type string $style
 *                 @type string $layout_meta_html
 *                 @type int $table_cols
 *                 @type int $bootstrap_grid_cols
 *                 @type string $bootstrap_grid_container '1' or ''
 *                 @type string $bootstrap_grid_row_class '1' or ''
 *                 @type string $bootstrap_grid_individual '1' or ''
 *                 @type string $include_field_names '1' or ''
 *                 @type array $fields
 *                 @type array $real_fields
 *             )
 *         @type string ct_content Content of the Content Template (see use_loop_template argument for more info) or
 *             an empty string.
 *     )
 *
 * @since 1.7
 */
function wpv_generate_view_loop_output( $style, $fields, $args ) {

	// Default values for arguments
	$args = array_merge(
			array(
					'include_field_names' => true,
					'tab_column_count' => 1,
					'bootstrap_column_count' => 1,
					'bootstrap_version' => 'undefined',
					'add_container' => false,
					'add_row_class' => false,
					'render_individual_columns' => false,
					'use_loop_template' => false,
					'loop_template_title' => '',
					'render_only_wpv_loop' => false ),
			$args );
					
	// Avoid extract() and validate.
	$include_field_names = ( true == $args['include_field_names'] ) ? true : false;
	$tab_column_count = (int) $args['tab_column_count'];
	$bootstrap_column_count = (int) $args['bootstrap_column_count'];
	$bootstrap_version = in_array( $args['bootstrap_version'], array( 2, 3, 'undefined' ) ) ? $args['bootstrap_version'] : 'undefined';
	$add_container = ( true == $args['add_container'] ) ? true : false;
	$add_row_class = ( true == $args['add_row_class'] ) ? true : false;
	$render_individual_columns = ( true == $args['render_individual_columns'] ) ? true : false;
	$use_loop_template = ( true == $args['use_loop_template'] ) ? true : false;
	$loop_template_title = $args['loop_template_title']; // can be anything
	$render_only_wpv_loop = ( true == $args['render_only_wpv_loop'] ) ? true : false;

	// Disallow empty title if we're creating new CT
	if( ( true == $use_loop_template ) && empty( $loop_template_title ) ) {
		//echo "use_loop_template";
		return null;
	}

	// Results
	$loop_output_settings = array(
			'style' => $style,  // this will be valid value, or we'll return null later
			'additional_js'	=> '' );

	// Ensure all field keys are present for all fields.
	$fields_normalized = array();
	$field_defaults = array(
			'prefix' => '',
			'shortcode' => '',
			'suffix' => '',
			'field_name' => '',
			'header_name' => '',
			'row_title' => '' );
	foreach( $fields as $field ) {
		$fields_normalized[] = wp_parse_args( $field, $field_defaults );
	}
	$fields = $fields_normalized;

	// Render layout HTML
	switch( $style ) {
		case 'table':
			$loop_output = wpv_render_table_layout( $fields, $args );
			break;
		case 'bootstrap-grid':
			$loop_output = wpv_render_bootstrap_grid_layout( $fields, $args );
			break;
		case 'table_of_fields':
			$loop_output = wpv_render_table_of_fields_layout( $fields, $args );
			break;
		case 'ordered_list':
			$loop_output = wpv_render_list_layout( $fields, $args, 'ol' );
			break;
		case 'un_ordered_list':
			$loop_output = wpv_render_list_layout( $fields, $args, 'ul' );
			break;
		case 'unformatted':
			$loop_output = wpv_render_unformatted_layout( $fields, $args );
			break;
		default:
			// Invalid loop output style
			//echo "invalid LOS";
			return null;
	}
	// If rendering has failed, we fail too.
	if( null == $loop_output ) {
		//echo "nothing rendered";
		return null;
	}
	
	$layout_meta_html = $loop_output['loop_template'];

	if( ! $render_only_wpv_loop ) {
		// Render the whole layout_meta_html
		$layout_meta_html = sprintf(
				"[wpv-layout-start]\n"
				. "\t[wpv-items-found]\n"
				. "\t<!-- wpv-loop-start -->\n"
				. "%s"
				. "\t<!-- wpv-loop-end -->\n"
				. "\t[/wpv-items-found]\n"
				. "\t[wpv-no-items-found]\n"
				. "\t[wpml-string context=\"wpv-views\"]<strong>No items found</strong>[/wpml-string]\n"
				. "\t[/wpv-no-items-found]\n"
				. "[wpv-layout-end]\n",
				$layout_meta_html );
	}

	$loop_output_settings['layout_meta_html'] = $layout_meta_html;

	// Pass other layout settings in the same way as it was in wpv_update_layout_extra_callback().

	// Only one value makes sense, but both are always stored...
	$loop_output_settings['table_cols'] = $tab_column_count;
	$loop_output_settings['bootstrap_grid_cols']  = $bootstrap_column_count;

	// These are '1' for true or '' for false (not sure if e.g. 0 can be passed instead, better leave it as it was).
	$loop_output_settings['bootstrap_grid_container'] = $add_container ? '1' : '';
	$loop_output_settings['bootstrap_grid_row_class'] = $add_row_class ? '1' : '';
	$loop_output_settings['bootstrap_grid_individual'] = $render_individual_columns ? '1' : '';
	$loop_output_settings['include_field_names'] = $include_field_names ? '1' : '';

	/* The 'fields' element is originally constructed in wpv_layout_wizard_convert_settings() with a comment
	 * saying just "Compatibility". 
	 * 
	 * TODO it would be nice to explain why is this needed (compatibility with what?). */
	$fields_compatible = array();
    $field_index = 0;
    foreach ( $fields as $field ) {
        $fields_compatible[ 'prefix_' . $field_index ] = '';

        $shortcode = stripslashes( $field['shortcode'] );

        if ( preg_match( '/\[types.*?field=\"(.*?)\"/', $shortcode, $matched ) ) {
            $fields_compatible[ 'name_' . $field_index ] = 'types-field';
            $fields_compatible[ 'types_field_name_' . $field_index ] = $matched[1];
            $fields_compatible[ 'types_field_data_' . $field_index ] = $shortcode;
        } else {
            $fields_compatible[ 'name_' . $field_index ] = trim( $shortcode, '[]');
            $fields_compatible[ 'types_field_name_' . $field_index ] = '';
            $fields_compatible[ 'types_field_data_' . $field_index ] = '';
        }

        $fields_compatible[ 'row_title_' . $field_index ] = $field['field_name'];
        $fields_compatible[ 'suffix_' . $field_index ] = '';

        ++$field_index;
    }
	$loop_output_settings['fields'] = $fields_compatible;

    // 'real_fields' will be an array of field shortcodes
    $field_shortcodes = array();
    foreach( $fields as $field ) {
		$field_shortcodes[] = stripslashes( $field['shortcode'] );
	}
    $loop_output_settings['real_fields'] = $field_shortcodes;

	// we'll be returning layout settings and content of a CT (optionally)
	$result = array(
			'loop_output_settings' => $loop_output_settings,
			'ct_content' => $loop_output['ct_content'] );
	
	return $result;
}


/**
 * Helper rendering function. Renders shortcodes for fields with all required prefixes and suffixes.
 *
 * Each field is rendered on a new line.
 *
 * @param array $fields The array of definitions of fields. See wpv_generate_view_loop_output() for details.
 * @param string $row_prefix Additional prefix for the field shortcode.
 * @param string $row_suffix Additional suffix for the field shortcode.
 *
 * @return string The shortcodes for all given fields.
 *
 * @since 1.8
 */ 
function wpv_render_field_codes( $fields, $row_prefix = '', $row_suffix = '' ) {
	$field_codes = array();
	foreach( $fields as $field ) {
		$field_codes[] = $row_prefix . $field['prefix'] . $field['shortcode'] . $field['suffix'] . $row_suffix;
	}
	return implode( "\n", $field_codes );
}


/**
 * Render unformatted View layout.
 *
 * @see wpv_generate_view_loop_output()
 *
 * @param array $fields Array of fields to be used inside this layout.
 * @param array $args Additional arguments. 
 *
 * @return array(
 *     @type string $loop_template Loop Output code.
 *     @type string $ct_content Content of the Content Template or an empty string if it's not being used.
 * )
 *
 * @since 1.8
 */
function wpv_render_unformatted_layout( $fields, $args ) {
	
	$indent = $args['use_loop_template'] ? "" : "\t\t";
	
	$field_codes = wpv_render_field_codes( $fields, $indent );
	
	if( $args['use_loop_template'] ) {
		$ct_content = $field_codes;
		$loop_template_body = "\t\t[wpv-post-body view_template=\"{$args['loop_template_title']}\"]";
	} else {
		$ct_content = '';
		$loop_template_body = $field_codes;
	}
	
	$loop_template = "\t<wpv-loop>\n" . $loop_template_body . "\n\t</wpv-loop>\n\t";
	
	return array(
			'loop_template' => $loop_template,
			'ct_content' => $ct_content );
}


/**
 * Render List View layout.
 *
 * @see wpv_generate_view_loop_output()
 *
 * @param array $fields Array of fields to be used inside this layout.
 * @param array $args Additional arguments.
 * @param string $list_type Type of the list. Can be 'ul' for unordered list or 'ol' for ordered list. Defaults to 'ul'.
 *
 * @return array(
 *     @type string $loop_template Loop Output code.
 *     @type string $ct_content Content of the Content Template or an empty string if it's not being used.
 * )
 *
 * @since 1.7
 */
function wpv_render_list_layout( $fields, $args, $list_type = 'ul' ) {
	
	$indent = $args['use_loop_template'] ? "" : "\t\t\t\t";
	$field_codes = wpv_render_field_codes( $fields, $indent );
	$list_type = ( 'ol' == $list_type ) ? 'ol' : 'ul';
	
	if( $args['use_loop_template'] ) {
		$ct_content = $field_codes;
		$loop_template_body = "\t\t\t<li>[wpv-post-body view_template=\"{$args['loop_template_title']}\"]</li>";
	} else {
		$ct_content = '';
		$loop_template_body = "\t\t\t<li>\n$field_codes\n\t\t\t</li>";
	}
	
	$loop_template = 
			"\t<$list_type>\n"
			. "\t\t<wpv-loop>\n"
			. $loop_template_body . "\n"
			. "\t\t</wpv-loop>\n"
			. "\t</$list_type>\n\t";
        
	return array(
			'loop_template' => $loop_template,
			'ct_content' => $ct_content );
}


/**
 * Render Table View layout.
 *
 * @see wpv_generate_view_loop_output()
 *
 * @param array $fields Array of fields to be used inside this layout.
 * @param array $args Additional arguments. 
 *
 * @return array(
 *     @type string $loop_template Loop Output code.
 *     @type string $ct_content Content of the Content Template or an empty string if it's not being used.
 * )
 *
 * @since 1.7
 */
function wpv_render_table_of_fields_layout( $fields, $args = array() ) {

	// Optionally render table header with field names.
	$thead = '';
	if ( $args['include_field_names'] ) {
		$thead = "\t\t<thead>\n\t\t\t<tr>\n";
		foreach( $fields as $field ) {
			$thead .= "\t\t\t\t<th>[wpv-heading name=\"{$field['header_name']}\"]{$field['row_title']}[/wpv-heading]</th>\n";
		}
		$thead .= "\t\t\t</tr>\n\t\t</thead>\n";
	}

	// Table body
	$indent = $args['use_loop_template'] ? "" : "\t\t\t\t";
	$field_codes = wpv_render_field_codes( $fields, $indent . '<td>', '</td>' );

	if( $args['use_loop_template'] ) {
		$ct_content = $field_codes;
		$loop_template_body = "\t\t\t\t[wpv-post-body view_template=\"{$args['loop_template_title']}\"]";
	} else {
		$ct_content = '';
		$loop_template_body = $field_codes;
	}

	// Put it all together.
	$loop_template = 
		"\t<table width=\"100%\">\n"
		. $thead
		. "\t\t<tbody>\n"
		. "\t\t<wpv-loop>\n"
		. "\t\t\t<tr>\n"
		. $loop_template_body . "\n"
		. "\t\t\t</tr>\n"
		. "\t\t</wpv-loop>\n\t\t</tbody>\n\t</table>\n\t";
        
	return array(
			'loop_template' => $loop_template,
			'ct_content' => $ct_content );
}


/**
 * Render Table-based grid View layout.
 *
 * @see wpv_generate_view_loop_output()
 *
 * @param array $fields Array of fields to be used inside this layout.
 * @param array $args Additional arguments.
 *
 * @return array(
 *     @type string $loop_template Loop Output code.
 *     @type string $ct_content Content of the Content Template or an empty string if it's not being used.
 * )
 *
 * @since 1.7
 */
function wpv_render_table_layout( $fields, $args ) {

	$indent = $args['use_loop_template'] ? "" : "\t\t\t\t";
	$field_codes = wpv_render_field_codes( $fields, $indent );

	if( $args['use_loop_template'] ) {
		$ct_content = $field_codes;
		$loop_template_body = "\t\t\t\t[wpv-post-body view_template=\"{$args['loop_template_title']}\"]";
	} else {
		$ct_content = '';
		$loop_template_body = $field_codes;
	}
	
	$cols = $args['tab_column_count'];
	
	$loop_template = 
			"\t<table width=\"100%\">\n\t<wpv-loop wrap=\"$cols\" pad=\"true\">\n"
			. "\t\t[wpv-item index=1]\n"
			. "\t\t<tr>\n\t\t\t<td>\n$loop_template_body\n\t\t\t</td>\n"
			. "\t\t[wpv-item index=other]\n"
			. "\t\t\t<td>\n$loop_template_body\n\t\t\t</td>\n"
			. "\t\t[wpv-item index=$cols]\n"
			. "\t\t\t<td>\n$loop_template_body\n\t\t\t</td>\n\t\t</tr>\n"
			. "\t\t[wpv-item index=pad]\n"
			. "\t\t\t<td></td>\n"
			. "\t\t[wpv-item index=pad-last]\n"
			. "\t\t\t<td></td>\n\t\t</tr>\n"
			. "\t</wpv-loop>\n\t</table>\n\t";
        	 
	return array(
			'loop_template' => $loop_template,
			'ct_content' => $ct_content );
}


/**
 * Render Bootstrap grid View layout.
 *
 * @see wpv_generate_view_loop_output()
 *
 * @param array $fields Array of fields to be used inside this layout.
 * @param array $args Additional arguments. 
 *
 * @return null|array Null on error (missing bootstrap version), otherwise the array:
 *     array (
 *         @type string $loop_template Loop Output code.
 *         @type string $ct_content Content of the Content Template or an empty string if it's not being used.
 *     )
 *
 * @since 1.7
 */
function wpv_render_bootstrap_grid_layout( $fields, $args ) {

	// bootstrap_column_count, bootstrap_version, add_container, add_row_class, render_individual_columns
	extract( $args );
	$column_count = $bootstrap_column_count;

	// Fail if we don't have valid bootstrap version
	if( !in_array( $bootstrap_version, array( 2, 3 ) ) ) {
		return null;
	}

	$indent = $args['use_loop_template'] ? "" : "\t\t\t\t";
	$field_codes = wpv_render_field_codes( $fields, $indent );
	
	// Prevent division by zero
	if( $column_count < 1 ) {
		return null;
	}

	$column_offset = 12 / $column_count;

	$output = '';

	// Row style and cols class for bootstrap 2
	$row_style = ( $bootstrap_version == 2 ) ? ' row-fluid' : '';
	$col_style = ( $bootstrap_version == 2 ) ? 'span' : 'col-sm-';
	$col_class = $col_style . $column_offset;

	// Add row class (optional for bootstrap 2)
	$row_class = ( $add_row_class || ( 3 == $bootstrap_version ) ) ? 'row' : '';

	if( $args['use_loop_template'] ) {
		$ct_content = $field_codes; 
		$loop_item = "<div class=\"$col_class\">[wpv-post-body view_template=\"{$args['loop_template_title']}\"]</div>";
	} else {
		$ct_content = '';
		$loop_item = "<div class=\"$col_class\">\n$field_codes\n\t\t\t</div>";
	}

	if( $add_container ) {
		$output .= "\t<div class=\"container\">\n";
	}
	
	$output .= "\t<wpv-loop wrap=\"{$column_count}\" pad=\"true\">\n";
	
	// If the first column is also a last column, close the div tag.
	$ifone = ( 1 == $column_count ) ? "\n\t\t</div>" : '';

	if( $render_individual_columns ) {
		// Render items for each column.
		$output .=
				"\t\t[wpv-item index=1]\n" 
				. "\t\t<div class=\"{$row_class} {$row_style}\">\n"
				. "\t\t\t$loop_item$ifone\n";
		for( $i = 2; $i < $column_count; ++$i ) {
			$output .=
					"\t\t[wpv-item index=$i]\n" .
					"\t\t\t$loop_item\n";
		}
	} else {
		// Render compact HTML
		$output .=
				"\t\t[wpv-item index=1]\n" 
				. "\t\t<div class=\"{$row_class} {$row_style}\">\n"
				. "\t\t\t$loop_item$ifone\n"
				. "\t\t[wpv-item index=other]\n"
				. "\t\t\t$loop_item\n";
	}

	// Render item for last column.
	if ( $column_count > 1) {
		$output .=
				"\t\t[wpv-item index=$column_count]\n" 
				. "\t\t\t$loop_item\n"
				. "\t\t</div>\n";
	}

	// Padding items
	$output .=
			"\t\t[wpv-item index=pad]\n"
			. "\t\t\t<div class=\"{$col_class}\"></div>\n" 
			. "\t\t[wpv-item index=pad-last]\n" 
			. "\t\t\t<div class=\"{$col_class}\"></div>\n"
			. "\t\t</div>\n" 
			. "\t</wpv-loop>\n\t";

	if ( $add_container ) {
		$output .= "</div>\n\t";
	}

	return array(
			'loop_template' => $output,
			'ct_content' => $ct_content );
}
