<?php

define ('WPV_ITEMS_PER_PAGE', 20); // TODO move to constants.php, maybe use WPV_DEFAULT_LIST_ITEMS instead

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
	if ( $post_type == 'view' ) {
		$messages['view'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => __('View updated.', 'wpv-views'),
			2 => __('Custom field updated.'),
			3 => __('Custom field deleted.'),
			4 => __('View updated.', 'wpv-views'),
			/* translators: %s: date and time of the revision */
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

function wpv_render_filter_td( $row, $id, $name, $summary_function, $selected, $data ) { // TODO only used in old Status Filter, safe to remove

	$td = '<td><img src="' . WPV_URL . '/res/img/delete.png" onclick="on_delete_wpv_filter(\'' . $row . '\')" style="cursor: pointer" />';
	$td .= '<td class="wpv_td_filter">';
	$td .= "<div id=\"wpv-filter-" . $id . "-show\">\n";
	$td .= call_user_func($summary_function, $selected);
	$td .= "</div>\n";
	$td .= "<div id=\"wpv-filter-" . $id . "-edit\" style='background:" . WPV_EDIT_BACKGROUND . ";display:none'>\n";

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

// TODO move this AJAX calls to wpv-admin-ajax.php

/**
* Disable pagination result hint message
*/

add_action('wp_ajax_wpv_pagination_hint_result_disable', 'wpv_pagination_hint_result_disable_callback');

function wpv_pagination_hint_result_disable_callback() {
	$nonce = $_POST["wpnonce"];
	if (! wp_verify_nonce( $nonce, 'wpv_view_pagination_hint_result_dismiss_nonce' ) ) die("Security check");
	$user_ID = get_current_user_id();
	$user_help_setting = get_user_meta( $user_ID, 'wpv_view_editor_help_dismiss' );
	if ( isset( $user_help_setting[0]['pagination'] ) && $user_help_setting[0]['pagination'] == 'disable' ) {
		echo true;
	} else {
		$user_help_setting['pagination'] = 'disable';
		$result = update_user_meta( $user_ID, 'wpv_view_editor_help_dismiss', $user_help_setting );
		echo $result;
	}
	die();
}

/**
* Disable parametric search hint message TODO check if this is deprecated
*/

add_action('wp_ajax_wpv_parametric_hint_disable', 'wpv_parametric_hint_disable_callback');

function wpv_parametric_hint_disable_callback() {
	$nonce = $_POST["wpnonce"];
	if (! wp_verify_nonce( $nonce, 'wpv_view_parametric_hint_dismiss_nonce' ) ) die("Security check");
	$user_ID = get_current_user_id();
	$user_help_setting = get_user_meta( $user_ID, 'wpv_view_editor_help_dismiss' );
	if ( isset( $user_help_setting[0]['parametric_search'] ) && $user_help_setting[0]['parametric_search'] == 'disable' ) {
		echo true;
	} else {
		$user_help_setting['parametric_search'] = 'disable';
		$result = update_user_meta( $user_ID, 'wpv_view_editor_help_dismiss', $user_help_setting );
		echo $result;
	}
	die();
}

/**
* Disable inline Content Template hint message
*/

add_action('wp_ajax_wpv_content_template_hint_disable', 'wpv_content_template_hint_disable_callback');

function wpv_content_template_hint_disable_callback() {
	$nonce = $_POST["wpnonce"];
	if (! wp_verify_nonce( $nonce, 'wpv_view_content_template_hint_dismiss_nonce' ) ) die("Security check");
	$user_ID = get_current_user_id();
	$user_help_setting = get_user_meta( $user_ID, 'wpv_view_editor_help_dismiss' );
	if ( isset( $user_help_setting[0]['content_template'] ) && $user_help_setting[0]['content_template'] == 'disable' ) {
		echo true;
	} else {
		$user_help_setting['content_template'] = 'disable';
		$result = update_user_meta( $user_ID, 'wpv_view_editor_help_dismiss', $user_help_setting );
		echo $result;
	}
	die();
}

/**
* Disable Layout wizard hint message
*/

add_action('wp_ajax_wpv_layout_wizard_hint_disable', 'wpv_layout_wizard_hint_disable_callback');

function wpv_layout_wizard_hint_disable_callback() {
	$nonce = $_POST["wpnonce"];
	if (! wp_verify_nonce( $nonce, 'wpv_view_layout_wizard_hint_dismiss_nonce' ) ) die("Security check");
	$user_ID = get_current_user_id();
	$user_help_setting = get_user_meta( $user_ID, 'wpv_view_editor_help_dismiss' );
	if ( isset( $user_help_setting[0]['layout_wizard'] ) && $user_help_setting[0]['layout_wizard'] == 'disable' ) {
		echo true;
	} else {
		$user_help_setting['layout_wizard'] = 'disable';
		$result = update_user_meta( $user_ID, 'wpv_view_editor_help_dismiss', $user_help_setting );
		echo $result;
	}
	die();
}

/**
* Set default View settings and layout settings
*
* @param $settings field: view_settings or view_layout_settings
* @param $purpose purpose of the view: all, pagination, slide, parametric or full
* @return array() with desired values
*/

function wpv_view_defaults( $settings = 'view_settings', $purpose = 'full' ) {
	$defaults = array(
		// set the initial values for the View settings
		// Note: taxonomy_type is set in wpv-section-query-type.php to use the first available taxonomy
		'view_settings' => array(
			'view-query-mode'			=> 'normal',
			'view_description'			=> '',
			'view_purpose'				=> 'full',
			'query_type'				=> array('posts'),
			'taxonomy_type'				=> array('category'),
			'roles_type'				=> array('administrator'),
			'post_type_dont_include_current_page'	=> true,
			'taxonomy_hide_empty'			=> true,
			'taxonomy_include_non_empty_decendants'	=> true,
			'taxonomy_pad_counts'			=> true, // check this setting application
			'orderby'				=> 'post_date',
			'order'					=> 'DESC',
			'taxonomy_orderby'			=> 'name',
			'taxonomy_order'			=> 'DESC',
			'users_orderby'				=> 'user_login',
			'users_order'				=> 'ASC',
			'limit'					=> -1,
			'offset'				=> 0,
			'taxonomy_limit'			=> -1,
			'taxonomy_offset'			=> 0,
			'users_limit'   			=> -1,
			'users_offset'  			=> 0,
			'posts_per_page'			=> 10,
			'pagination'				=> array(
								'disable',
								'mode'				=> 'none',
								'preload_images'		=> true,
								'cache_pages'			=> true,
								'preload_pages'			=> true,
								'pre_reach'			=> 1,
								'page_selector_control_type'	=> 'drop_down',
								'spinner'			=> 'default',
								'spinner_image'			=> WPV_URL . '/res/img/ajax-loader.gif',
								'spinner_image_uploaded'	=> '',
								'callback_next'			=> ''
								), // this needs carefull review
			'ajax_pagination'			=> array(
								'disable',
								'style'				=> 'fade',
								'duration'			=> 500,
								),
			'rollover'				=> array(
								'preload_images'		=> true,
								'posts_per_page'		=> 1,
								'speed'				=> 5,
								'effect'			=> 'fade',
								'duration'			=> 500,
								),
			'filter_meta_html_state'		=> array(
								'html'				=> 'on',
								'css'				=> 'off',
								'js'				=> 'off',
								'img'				=> 'off',
								),
			'filter_meta_html'			=> "[wpv-filter-start hide=\"false\"]\n[wpv-filter-controls][/wpv-filter-controls]\n[wpv-filter-end]",
			'filter_meta_html_css'			=> '',
			'filter_meta_html_js'			=> '',
			'layout_meta_html_state'		=> array(
								'html'				=> 'on',
								'css'				=> 'off',
								'js'				=> 'off',
								'img'				=> 'off',
								),
			'layout_meta_html_css'			=> '',
			'layout_meta_html_js'			=> '',
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
		[wpml-string context=\"wpv-views\"]<strong>No items found</strong>[/wpml-string]
	[/wpv-no-items-found]
[wpv-layout-end]",
		),
	);
	switch( $purpose ) {
		case 'all':
			$defaults['view_settings']['sections-show-hide'] = array(
				'pagination'		=> 'off',
				'filter-extra'		=> 'off',
			);
			$defaults['view_settings']['view_purpose'] = 'all';
			break;
		case 'pagination':
			$defaults['view_settings']['pagination'][0] = 'enable';
			$defaults['view_settings']['pagination']['mode'] = 'paged';
			$defaults['view_settings']['sections-show-hide'] = array(
				'limit-offset'		=> 'off',
			);
			$defaults['view_settings']['view_purpose'] = 'pagination';
			break;
		case 'slider':
			$defaults['view_settings']['pagination'][0] = 'enable';
			$defaults['view_settings']['pagination']['mode'] = 'rollover';
			$defaults['view_settings']['sections-show-hide'] = array(
				'limit-offset'		=> 'off',
			);
			$defaults['view_settings']['view_purpose'] = 'slider';
			break;
		case 'parametric':
			$defaults['view_settings']['sections-show-hide'] = array(
				'pagination'		=> 'off',
			);
			$defaults['view_settings']['view_purpose'] = 'parametric';
			break;
		case 'bootstrap-grid':
			$defaults['view_settings']['sections-show-hide'] = array(
				'layout-extra'		=> 'off',
				'content'			=> 'off',
			);
			$defaults['view_settings']['view_purpose'] = 'bootstrap-grid';
			break;
		case 'full':
		default:
			$defaults['view_settings']['sections-show-hide'] = array(
			
			);
			$defaults['view_settings']['view_purpose'] = 'full';
			break;
	}
	return $defaults[$settings];
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

/**
*
* Display pagination in admin listing pages
*
* @param $context the admin page where it will be rendered: 'views', 'view-templates', 'view-archives'
* @param $wpv_found_items (int)
* @param $wpv_items_per_page (int)
* @param $mod_url (array)
* 
*/

function wpv_admin_listing_pagination( $context = 'views', $wpv_found_items, $wpv_items_per_page = WPV_ITEMS_PER_PAGE, $mod_url = array() ) {
	$page = ( isset( $_GET["paged"] ) ) ? (int) $_GET["paged"] : 1;
	$pages_count = ceil( (int) $wpv_found_items / (int) $wpv_items_per_page );
	if ( $pages_count > 1 ) {
		$items_start = ( ( ( $page - 1 ) * (int) $wpv_items_per_page ) + 1 );
		$items_end = ( ( ( $page - 1 ) * (int) $wpv_items_per_page ) + (int) $wpv_items_per_page );
		if ( $page == $pages_count ) {
			$items_end = $wpv_found_items;
		}
		$mod_url_defaults = array(
			'orderby' => '',
			'order' => '',
			'search' => '',
			'items_per_page' => '',
			'status' => ''
		);
		$mod_url = wp_parse_args($mod_url, $mod_url_defaults);
		?>
		<div class="wpv-listing-pagination tablenav">
			<div class="tablenav-pages">
				<span class="displaying-num">
					<?php _e('Displaying ', 'wpv-views'); echo $items_start; ?> - <?php echo $items_end; _e(' of ', 'wpv-views'); echo $wpv_found_items; ?>
				</span>
				<?php if ( $page > 1 ) { ?>
					<a href="<?php echo admin_url('admin.php'); ?>?page=<?php echo $context . $mod_url['orderby'] . $mod_url['order'] . $mod_url['search'] . $mod_url['items_per_page'] . $mod_url['status']; ?>&amp;paged=<?php echo $page - 1; ?>" class="wpv-filter-navigation-link">&laquo; <?php echo __('Previous page','wpv-views'); ?></a>
				<?php } ?>
				<?php
				for ( $i = 1; $i <= $pages_count; $i++ ) {
					$active = 'wpv-filter-navigation-link-inactive';
					if ( $page == $i ) $active = 'js-active active current'; ?>
					<a href="<?php echo admin_url('admin.php'); ?>?page=<?php echo $context . $mod_url['orderby'] . $mod_url['order'] . $mod_url['search'] . $mod_url['items_per_page'] . $mod_url['status']; ?>&amp;paged=<?php echo $i; ?>" class="<?php echo $active; ?>"><?php echo $i; ?></a>
				<?php } ?>
				<?php if ( $page < $pages_count ) { ?>
					<a href="<?php echo admin_url('admin.php'); ?>?page=<?php echo $context . $mod_url['orderby'] . $mod_url['order'] . $mod_url['search'] . $mod_url['items_per_page'] . $mod_url['status']; ?>&amp;paged=<?php echo $page + 1; ?>" class="wpv-filter-navigation-link"><?php echo __('Next page','wpv-views'); ?> &raquo;</a>
				<?php } ?>
				<?php _e('Items per page', 'wpv-views'); ?>
				<select class="js-items-per-page">
					<option value="10"<?php if ( $wpv_items_per_page == '10' ) echo ' selected="selected"'; ?>>10</value>
					<option value="20"<?php if ( $wpv_items_per_page == '20' ) echo ' selected="selected"'; ?>>20</value>
					<option value="50"<?php if ( $wpv_items_per_page == '50' ) echo ' selected="selected"'; ?>>50</value>
				</select>
				<a href="#" class="js-wpv-display-all-items"><?php _e('Display all items', 'wpv-views'); ?></a>
			</div><!-- .tablenav-pages -->
		</div><!-- .wpv-listing-pagination -->
	<?php } else if ( ( WPV_ITEMS_PER_PAGE != $wpv_items_per_page ) && ( $wpv_found_items > WPV_ITEMS_PER_PAGE ) ) { ?>
		<div class="wpv-listing-pagination tablenav">
			<div class="tablenav-pages">
				<a href="#" class="js-wpv-display-default-items"><?php _e('Display 20 items per page', 'wpv-views'); ?></a>
			</div><!-- .tablenav-pages -->
		</div><!-- .wpv-listing-pagination -->
	<?php }
}

// NOT needed for Views anymore DEPRECATED NOTE Layouts might find this usefull

function _wpv_get_all_views($view_query_mode) {
	global $wpdb, $WP_Views;
	
	$q = ('
        SELECT ID, post_title FROM ' . $wpdb->prefix . 'posts
        WHERE
            post_status="publish"
        AND
            post_type="view"
    ');
	
	$all_views = $wpdb->get_results( $q);
	foreach($all_views as $key => $view) {
		$settings = $WP_Views->get_view_settings($view->ID);
		if($settings['view-query-mode'] != $view_query_mode) {
			unset($all_views[$key]);
		}
	}
	
	return $all_views;

}

function _wpv_field_views_by_search($all_views, $search_term) {
/*
	if ( !empty( $search_term ) ) {
		foreach($all_views as $key => $view) {
			// check the search
			$description = get_post_meta($view->ID, '_wpv_description', true);
			if (strpos($description, $search_term) === FALSE && strpos($view->post_title, $search_term) === FALSE) {
				unset($all_views[$key]);
			}
		}
	}
*/
	foreach($all_views as $key => $view) {
		$all_views[$key] = $view->ID;
	}

	$all_views = implode(',', $all_views);
		
	return $all_views;
}

/**
* Check the existence of a kind of View NOT needed for Views anymore DEPRECATED
*
* @param $query_mode kind of View object: normal or archive
* @return boolean
*/

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
* Add View button to codemirror editor
*
* @param $editor_id ID for the relevant textarea, to be set as active editor
* @param $inline TODO document this
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
            WPV_URL . '/res/img/bw_icon16.png');

    if ( !$inline ){ echo '<div class="wpv-vicon-for-posts'. $post_hidden .'">';}
    
	if ( !$inline ){
	    add_short_codes_to_js( array('post', 'taxonomy', 'post-view', 'view-form'), $WP_Views->editor_addon );
	    $WP_Views->editor_addon->add_form_button('', $editor_id , true, true, true);
	}
	else{
		if ( empty($view) && isset($_POST['view_id']) ){
			$view = $_POST['view_id'];
			$meta = get_post_meta( $view, '_wpv_settings', true);
		}
		if ( !isset($meta['query_type'][0]) || ( isset($meta['query_type'][0]) && $meta['query_type'][0]=='posts' )){
			add_short_codes_to_js( array('post', 'taxonomy', 'post-view', 'view-form','body-view-templates-posts'), $WP_Views->editor_addon );
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
* Add CRED button to codemirror editor
*
* @param $editor_id ID for the relevant textarea, to be set as active editor
* @return $strings without the unwanted sections
*/

function wpv_add_cred_to_codemirror( $editor_id, $wrapper = '', $echo = true ){
	$return = '';
	if ( !empty( $wrapper ) ) {
		$return .= '<' . $wrapper . '>';
	}
	$return .= apply_filters('wpv_meta_html_add_form_button', '', '#'.$editor_id);
	if ( !empty( $wrapper ) ) {
		$return .= '</' . $wrapper . '>';
	}
	if ( $echo ) {
		echo $return;
	} else {
		return $return;
	}
}

//Update defaults and create new CT for slider view or bootstrap-grid view
function wpv_create_new_ct_for_view($id, $view_title, $purpose){
    global $wpdb;
	$i = 0; $add = '';
	
	while ( $i == 0 ){
		
		$ct_name = $view_title . ' - ' . $purpose . $add;
		$total = $wpdb->get_var( $wpdb->prepare(
							'SELECT count(ID) FROM ' . $wpdb->posts . ' WHERE post_title = %s AND post_type=\'view-template\'',
							$ct_name
		));
		
		$add++;
		if ( $total <= 0){
			$i=1;
		}
	}    
   
	$new_template = array(
		'post_title'    => $ct_name,
		'post_type'      => 'view-template',
		'post_content'  => '[wpv-post-link]',
		'post_status'   => 'publish',
		'post_author'   => 1
	);

	$post_id = wp_insert_post( $new_template );
	update_post_meta( $post_id, '_wpv_view_template_mode', 'raw_mode');
	update_post_meta( $post_id, '_wpv-content-template-decription', '');
	return $post_id;
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
*/

function wpv_create_view( $args ) {
	global $wpdb;
	$return = array();
	// First, set the title
	if ( !isset( $args["title"] ) || $args["title"] == '' ) {
		$args["title"] = __('Unnamed View', 'wp-views');
	}
	// Check for already existing Views with that title
	$postid = $wpdb->get_results( "SELECT ID FROM $wpdb->posts WHERE post_title = '" . $args["title"] . "' AND post_type='view'" );
	if ( $postid ) {
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
		if ( !isset( $args['settings']["purpose"] ) ) {
			$args['settings']["purpose"] = 'full';
		}
		switch ( $args['settings']["view-query-mode"] ) {
			case 'archive':
			case 'layouts-loop': // layouts-loop is similar to the archive but the posts to display will come from layouts
				$view_normal_defaults = wpv_wordpress_archives_defaults( 'view_settings' );
				$view_normal_layout_defaults = wpv_wordpress_archives_defaults( 'view_layout_settings' );
				break;
			default:
				$view_normal_defaults = wpv_view_defaults( 'view_settings', $args['settings']["purpose"] );
				$view_normal_layout_defaults = wpv_view_defaults( 'view_layout_settings', $args['settings']["purpose"] );
				break;
		}
		
		switch ($args['settings']["purpose"]) {
			case 'slider': // if the purpose is slider, create a CT for every slide, add it to the View layout and to the list of connected CT
				$temp = wpv_create_new_ct_for_view( $id, $args["title"], __('slide', 'wpv-views' ) );
				$ct_post = get_post($temp);
				$view_normal_layout_defaults['layout_meta_html'] =
				str_replace('<wpv-loop>','<wpv-loop>[wpv-post-body view_template="'.$ct_post->post_title.'"]',$view_normal_layout_defaults['layout_meta_html']);
				$view_normal_layout_defaults['included_ct_ids'] = $temp;
				update_post_meta($id, '_wpv_first_time_load', 'on');
				break;
			
			case 'bootstrap-grid': // if the purpose is from Layouts plugin, create a CT and add it to the View layout and to the list of connected CT. Create the BS grid
				
				$view_normal_defaults['sections-show-hide']['layout-extra'] = 'off';
				$view_normal_defaults['metasections-hep-show-hide']['wpv-layout-help'] = 'off';
				
				$temp = wpv_create_new_ct_for_view( $id, $args["title"], __('grid', 'wpv-views' ) );
				$ct_post = get_post($temp);
				$view_normal_layout_defaults['layout_meta_html'] = wpv_create_bootstrap_meta_html($args['cols'],
																								  $ct_post->post_name,
																								  $view_normal_layout_defaults['layout_meta_html']);
				$view_normal_layout_defaults['included_ct_ids'] = $temp;
				
				$view_normal_layout_defaults['bootstrap_grid_cols'] = $args['cols'];
				$view_normal_layout_defaults['bootstrap_grid_container'] = 'false';
				$view_normal_layout_defaults['bootstrap_grid_individual'] = '';
				$view_normal_layout_defaults['style'] = 'bootstrap-grid';
				$view_normal_layout_defaults['insert_at'] = 'insert_replace';
				$view_normal_layout_defaults['real_fields'] = array('[wpv-post-body view_template=\\"' . $ct_post->post_name. '\\"]');
				$view_normal_layout_defaults['fields'] = array('name_0' => 'wpv-post-body view_template=\\"' . $ct_post->post_name. '\\"',
															   'prefix_0' => '',
															   'row_title_0' => 'Body',
															   'suffix' => '',
															   'types_field_data_0' => '',
															   'types_field_name_0' => '');
				update_post_meta($id, '_wpv_first_time_load', 'on');
				break;
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
	global $WP_Views;
	
	$col_num = 12 / $cols;	
	$output = '';
	$row_style = '';
	$col_style = 'col-sm-';
	$body = '[wpv-post-body view_template="' . $ct_title. '"]';
	
	//Row style and cols class for bootstrap 2.0
	
	$options = $WP_Views->get_options();
	if (class_exists('WPDD_Layouts_CSSFrameworkOptions')) {
		$bootstrap_ver = WPDD_Layouts_CSSFrameworkOptions::getInstance()->get_current_framework();
		$options['wpv_bootstrap_version'] = str_replace('bootstrap-','',$bootstrap_ver);
	}else{
		//Load bootstrap version from views settings
		if ( !isset($options['wpv_bootstrap_version']) ){
			$options['wpv_bootstrap_version'] = 2;
		}
	}
	
	if ( $options['wpv_bootstrap_version'] == 2){
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