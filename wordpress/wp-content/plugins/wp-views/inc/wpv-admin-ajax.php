<?php

/*
* General file for all AJAX calls
* All AJAX calls used in the backend must be set here
*/

/*
* Views & WPA edit sceen
*/

// Screen options save callback function

add_action('wp_ajax_wpv_save_screen_options', 'wpv_save_screen_options_callback');

function wpv_save_screen_options_callback() {
	$nonce = $_POST["wpnonce"];
	if (! wp_verify_nonce($nonce, 'wpv_view_show_hide_nonce') ) die("Security check");
	$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
	if ( isset( $_POST['settings'] ) ) {
		parse_str($_POST['settings'], $settings);
		foreach ($settings as $section => $state) {
			$view_array['sections-show-hide'][$section] = $state;
		}
	}
	if ( isset( $_POST['helpboxes'] ) ) {
		parse_str($_POST['helpboxes'], $help_settings);
		foreach ($help_settings as $section => $state) {
			$view_array['metasections-hep-show-hide'][$section] = $state;
		}
	}
	if ( isset( $_POST['purpose'] ) ) {
		$view_array['view_purpose'] = $_POST['purpose'];
	}
	update_post_meta($_POST["id"], '_wpv_settings', $view_array);
	echo $_POST["id"];
	die();
}

// Title and description save callback function

add_action('wp_ajax_wpv_update_title_description', 'wpv_update_title_description_callback');

function wpv_update_title_description_callback() {
	global $wpdb;
	$nonce = $_POST["wpnonce"];
	if (! wp_verify_nonce($nonce, 'wpv_view_title_description_nonce') ) die("Security check");
	$view_desc = get_post_meta($_POST["id"], '_wpv_description', true);
	$view_title = get_the_title($_POST["id"]);
	$view_slug = basename( get_permalink( $_POST["id"] ) );
	$result = true;
	$return = $_POST["id"];
	$edit = 'WordPress Archive';
	if ( isset($_POST['edit']) ){
		$edit = $_POST['edit'];
	}
	if ( !isset( $_POST["title"] ) || empty( $_POST["title"] ) ) {
		print json_encode( array('error', __( 'You can not leave the title empty.', 'wpv-views' ) ) );
		die();
	}
	if ( !isset( $_POST["slug"] ) || empty( $_POST["slug"] ) ) {
		print json_encode( array('error', __( 'You can not leave the slug empty.', 'wpv-views' ) ) );
		die();
	}
	if ( $_POST["slug"] != sanitize_title( $_POST['slug'] ) ) {
		print json_encode( array('error', __( 'The slug can only contain lowercase letters, numbers or dashes.', 'wpv-views' ) ) );
		die();
	}
	$title_check = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type='view' AND ID!=%d", $_POST["title"], $return ) );
	if ( !empty($title_check)  ){
		print json_encode( array('error', sprintf( __( 'A %s with that name already exists. Please use another name.', 'wpv-views' ), $edit )) );
		die();
	}
	$name_check = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_name = %s AND post_type='view' AND ID!=%d", $_POST["slug"], $return ) );
	if ( !empty($name_check)  ){
		print json_encode( array('error', sprintf( __( 'A %s with that slug already exists. Please use another slug.', 'wpv-views' ), $edit )) );
		die();
	}
	$value = filter_input_array(INPUT_POST, array('description' => array('filter' => FILTER_SANITIZE_STRING, 'flags' => !FILTER_FLAG_STRIP_LOW)));
	if (!isset($view_desc) || $value['description'] != $view_desc) {
		$view_desc = $value['description'];
		$result = update_post_meta($_POST["id"], '_wpv_description', $view_desc);
	}
	if ($_POST["title"] != $view_title || $_POST["slug"] != $view_slug) {
		$view = array();
		$view['ID'] = $_POST["id"];
		$view['post_title'] = $_POST["title"];
		$view['post_name'] = $_POST["slug"];
		$return = wp_update_post( $view );
	}

	echo $result ? $return : false;
	die();
}

// Loop selection save callback function - only for WPA

add_action('wp_ajax_wpv_update_loop_selection', 'wpv_update_loop_selection_callback');

function wpv_update_loop_selection_callback() {
	$nonce = $_POST["wpnonce"];
	if (! wp_verify_nonce($nonce, 'wpv_view_loop_selection_nonce') ) die("Security check");
	global $WPV_view_archive_loop;
	parse_str($_POST['form'], $form_data);
	$WPV_view_archive_loop->update_view_archive_settings($_POST["id"], $form_data);
	$loop_form = '';
	ob_start();
	render_view_loop_selection_form( $_POST['id'] );
	$loop_form = ob_get_contents();
	ob_end_clean();
	$return_result['wpv_settings_archive_loops'] = $loop_form;
	$return_result['success'] = $_POST['id'];
	echo json_encode( $return_result );
	die();
}

// Query type save callback function - only for Views

add_action('wp_ajax_wpv_update_query_type', 'wpv_update_query_type_callback');

function wpv_update_query_type_callback() {
	$nonce = $_POST["wpnonce"];
	if (! wp_verify_nonce($nonce, 'wpv_view_query_type_nonce') ) die("Security check");
	$changed = false;
	$return_result = array();
	if (!isset($_POST["post_types"])) $_POST["post_types"] = array('any');
	$view_array = get_post_meta($_POST["id"],'_wpv_settings', true);
	if (isset($view_array['query_type']) && isset($view_array['query_type'][0]) && $view_array['query_type'][0] == $_POST["query_type"]) {
	} else {
		$view_array['query_type'] = array($_POST["query_type"]);
		$changed = true;
	}
	if (!isset($view_array['post_type']) || $view_array['post_type'] != $_POST["post_types"]) {
		$view_array['post_type'] = $_POST["post_types"];
		$changed = true;
	}
	if (!isset($view_array['taxonomy_type']) || $view_array['taxonomy_type'] != $_POST["taxonomies"]) {
		$view_array['taxonomy_type'] = $_POST["taxonomies"];
		$changed = true;
	}
	if (!isset($view_array['roles_type']) || $view_array['roles_type'] != $_POST["users"]) {
		$view_array['roles_type'] = $_POST["users"];
		$changed = true;
	}
	if ($changed) {
		$result = update_post_meta($_POST["id"], '_wpv_settings', $view_array);
	//	echo $result ? $_POST["id"] : false;
		$return_result['success'] = $result ? $_POST["id"] : false;
	} else {
	//	echo $_POST["id"];
		$return_result['success'] = $_POST['id'];
	}
	// Filters list
	$filters_list = '';
	ob_start();
	wpv_display_filters_list( $view_array['query_type'][0], $view_array );
	$filters_list = ob_get_contents();
	ob_end_clean();
	$return_result['wpv_filter_update_filters_list'] = $filters_list;
	// Flatten Types post relationship
	$returned_post_types = $view_array['post_type'];
	$multi_post_relations = wpv_recursive_post_hierarchy( $returned_post_types );
	$flatten_post_relations = wpv_recursive_flatten_post_relationships( $multi_post_relations );
	if ( strlen( $flatten_post_relations ) > 0 ) {
		$relations_tree = wpv_get_all_post_relationship_options( $flatten_post_relations );
		$return_result['wpv_update_flatten_types_relationship_tree'] = implode( ',', $relations_tree );
	} else {
		$return_result['wpv_update_flatten_types_relationship_tree'] = 'NONE';
	}
	// Now, the dependent parametric search structure
	$dps_structure = '';
	ob_start();
	wpv_dps_settings_structure( $view_array, $_POST["id"] );
	$dps_structure = ob_get_contents();
	ob_end_clean();
	$return_result['wpv_dps_settings_structure'] = $dps_structure;
//	echo $return;
	
	echo json_encode( $return_result );
	die();
}

// Query options save callback function - only for Views

add_action('wp_ajax_wpv_update_query_options', 'wpv_update_query_options_callback');

function wpv_update_query_options_callback() {
	$nonce = $_POST["wpnonce"];
	if (! wp_verify_nonce($nonce, 'wpv_view_query_options_nonce') ) die("Security check");
	$changed = false;
	$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
	if (!isset($view_array['post_type_dont_include_current_page']) || $_POST["dont"] != $view_array['post_type_dont_include_current_page']) {
		$view_array['post_type_dont_include_current_page'] = $_POST["dont"];
		$changed = true;
	}
	if (!isset($view_array['taxonomy_hide_empty']) || $_POST["hide"] != $view_array['taxonomy_hide_empty']) {
		$view_array['taxonomy_hide_empty'] = $_POST["hide"];
		$changed = true;
	}
	if (!isset($view_array['taxonomy_include_non_empty_decendants']) || $_POST["empty"] != $view_array['taxonomy_include_non_empty_decendants']) {
		$view_array['taxonomy_include_non_empty_decendants'] = $_POST["empty"];
		$changed = true;
	}
	if (!isset($view_array['taxonomy_pad_counts']) || $_POST["pad"] != $view_array['taxonomy_pad_counts']) {
		$view_array['taxonomy_pad_counts'] = $_POST["pad"];
		$changed = true;
	}
	if (!isset($view_array['users-show-current']) || $_POST["uhide"] != $view_array['users-show-current']) {
		$view_array['users-show-current'] = $_POST["uhide"];
		$changed = true;
	}
	/*if (!isset($view_array['users-show-multisite']) || $_POST["smulti"] != $view_array['users-show-multisite']) {
		$view_array['users-show-multisite'] = $_POST["smulti"];
		$changed = true;
	}*/
	if ($changed) {
		$result = update_post_meta($_POST["id"], '_wpv_settings', $view_array);
		echo $result ? $_POST["id"] : false;
	} else {
		echo $_POST["id"];
	}
	die();
}

// Sorting save callback function - only for Views

add_action('wp_ajax_wpv_update_sorting', 'wpv_update_sorting_callback');

function wpv_update_sorting_callback() {
	$nonce = $_POST["wpnonce"];
	if (! wp_verify_nonce($nonce, 'wpv_view_ordering_nonce') ) die("Security check");
	$changed = false;
	$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
	if (!isset($view_array['orderby']) || $_POST["orderby"] != $view_array['orderby']) {
		$view_array['orderby'] = $_POST["orderby"];
		$changed = true;
	}
	if (!isset($view_array['order']) || $_POST["order"] != $view_array['order']) {
		$view_array['order'] = $_POST["order"];
		$changed = true;
	}
	if (!isset($view_array['taxonomy_orderby']) || $_POST["taxonomy_orderby"] != $view_array['taxonomy_orderby']) {
		$view_array['taxonomy_orderby'] = $_POST["taxonomy_orderby"];
		$changed = true;
	}
	if (!isset($view_array['taxonomy_order']) || $_POST["taxonomy_order"] != $view_array['taxonomy_order']) {
		$view_array['taxonomy_order'] = $_POST["taxonomy_order"];
		$changed = true;
	}
	if (!isset($view_array['users_orderby']) || $_POST["users_orderby"] != $view_array['users_orderby']) {
		$view_array['users_orderby'] = $_POST["users_orderby"];
		$changed = true;
	}
	if (!isset($view_array['users_order']) || $_POST["users_order"] != $view_array['users_order']) {
		$view_array['users_order'] = $_POST["users_order"];
		$changed = true;
	}
	if ($changed) {
		$result = update_post_meta($_POST["id"], '_wpv_settings', $view_array);
		echo $result ? $_POST["id"] : false;
	} else {
		echo $_POST["id"];
	}
	die();
}

// Limit and offset save callback function - only for Views

add_action('wp_ajax_wpv_update_limit_offset', 'wpv_update_limit_offset_callback');

function wpv_update_limit_offset_callback() {
	$nonce = $_POST["wpnonce"];
	if (! wp_verify_nonce($nonce, 'wpv_view_limit_offset_nonce') ) die("Security check");
	$changed = false;
	$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
	if (!isset($view_array['limit']) || $_POST["limit"] != $view_array['limit']) {
		$view_array['limit'] = $_POST["limit"];
		$changed = true;
	}
	if (!isset($view_array['offset']) || $_POST["offset"] != $view_array['offset']) {
		$view_array['offset'] = $_POST["offset"];
		$changed = true;
	}
	if (!isset($view_array['taxonomy_limit']) || $_POST["taxonomy_limit"] != $view_array['taxonomy_limit']) {
		$view_array['taxonomy_limit'] = $_POST["taxonomy_limit"];
		$changed = true;
	}
	if (!isset($view_array['taxonomy_offset']) || $_POST["taxonomy_offset"] != $view_array['taxonomy_offset']) {
		$view_array['taxonomy_offset'] = $_POST["taxonomy_offset"];
		$changed = true;
	}
    if (!isset($view_array['users_limit']) || $_POST["users_limit"] != $view_array['users_limit']) {
        $view_array['users_limit'] = $_POST["users_limit"];
        $changed = true;
    }
    if (!isset($view_array['users_offset']) || $_POST["users_offset"] != $view_array['users_offset']) {
        $view_array['users_offset'] = $_POST["users_offset"];
        $changed = true;
    }
	if ($changed) {
		$result = update_post_meta($_POST["id"], '_wpv_settings', $view_array);
		echo $result ? $_POST["id"] : false;
	} else {
		echo $_POST["id"];
	}
	die();
}

// Pagination save callback function - only for Views

add_action('wp_ajax_wpv_update_pagination', 'wpv_update_pagination_callback');

function wpv_update_pagination_callback() {
	$nonce = $_POST["wpnonce"];
	if (! wp_verify_nonce($nonce, 'wpv_view_pagination_nonce') ) die("Security check");
	$changed = false;
	parse_str($_POST['settings'], $settings);
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
	$settings = wpv_parse_args_recursive($settings, $defaults);
	$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
	if ( $view_array['posts_per_page'] != $settings['posts_per_page'] ) {
		$view_array['posts_per_page'] = $settings['posts_per_page'];
		$changed = true;
	}
	if ( $view_array['pagination'] != $settings['pagination'] ) {
		$view_array['pagination'] = $settings['pagination'];
		$changed = true;
	}
	if ( $view_array['ajax_pagination'] != $settings['ajax_pagination'] ) {
		$view_array['ajax_pagination'] = $settings['ajax_pagination'];
		$changed = true;
	}
	if ( $view_array['rollover'] != $settings['rollover'] ) {
		$view_array['rollover'] = $settings['rollover'];
		$changed = true;
	}
	if ($changed) {
		$result = update_post_meta($_POST["id"], '_wpv_settings', $view_array);
		echo $result ? $_POST["id"] : false;
	} else {
		echo $_POST["id"];
	}
	die();
}

// Filter Extra save callback function - only for Views

add_action('wp_ajax_wpv_update_filter_extra', 'wpv_update_filter_extra_callback');

function wpv_update_filter_extra_callback() {
	$nonce = $_POST["wpnonce"];
	if (! wp_verify_nonce($nonce, 'wpv_view_filter_extra_nonce') ) die("Security check");
	$changed = false;
	$return_result = array();
	$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
	if (!isset($view_array['filter_meta_html']) || $_POST["query_val"] != $view_array['filter_meta_html']) {
		$view_array['filter_meta_html'] = $_POST["query_val"];
		wpv_add_controls_labels_to_translation( $_POST["query_val"], $_POST["id"] );
		$changed = true;
	}
	wpv_register_wpml_strings( $_POST["query_val"] );
	if (!isset($view_array['filter_meta_html_css']) || $_POST["query_css_val"] != $view_array['filter_meta_html_css']) {
		$view_array['filter_meta_html_css'] = $_POST["query_css_val"];
		$changed = true;
	}
	if (!isset($view_array['filter_meta_html_js']) || $_POST["query_js_val"] != $view_array['filter_meta_html_js']) {
		$view_array['filter_meta_html_js'] = $_POST["query_js_val"];
		$changed = true;
	}
	if ($changed) {
		$result = update_post_meta($_POST["id"], '_wpv_settings', $view_array);
		$return_result['success'] = $result ? $_POST["id"] : false;
	} else {
		$return_result['success'] = $_POST["id"];
	}
	// Now, the dependent parametric search structure
	$dps_structure = '';
	ob_start();
	wpv_dps_settings_structure( $view_array, $_POST["id"] );
	$dps_structure = ob_get_contents();
	ob_end_clean();
	$return_result['wpv_dps_settings_structure'] = $dps_structure;
	echo json_encode( $return_result );
	die();
}

// Layout Extra save callback function

add_action('wp_ajax_wpv_update_layout_extra', 'wpv_update_layout_extra_callback');

function wpv_update_layout_extra_callback() {
	$nonce = $_POST["wpnonce"];
	if (! wp_verify_nonce($nonce, 'wpv_view_layout_extra_nonce') ) die("Security check");
    
    // Save the wizard settings if they are there.
    if (isset($_POST['style'])) {
        $settings = get_post_meta($_POST["id"], '_wpv_layout_settings', true);
        $settings['style'] = $_POST['style'];
        $settings['insert_at'] = $_POST['insert_at'];
        $settings['table_cols'] = $_POST['table_cols'];
		//$settings['bootstrap_grid_style'] = $_POST['bootstrap_grid_style'];
		$settings['bootstrap_grid_cols'] = $_POST['bootstrap_grid_cols'];
		//$settings['bootstrap_grid_cols_width'] = $_POST['bootstrap_grid_cols_width'];
		$settings['bootstrap_grid_container'] = $_POST['bootstrap_grid_container'];
		$settings['bootstrap_grid_individual'] = $_POST['bootstrap_grid_individual'];
        $settings['include_field_names'] = $_POST['include_field_names'];
    
        $settings['fields'] = $_POST['fields'];        
        $settings['real_fields'] = $_POST['real_fields'];        
        
        update_post_meta($_POST["id"], '_wpv_layout_settings', $settings);
    }
    
	$changed = false;
	$changed_bis = false;
    //update_post_meta($_POST["id"], '_wpv_layout_settings', $settings);
    
    $view_layout_array = get_post_meta($_POST["id"], '_wpv_layout_settings', true);

    $previous_layout = $view_layout_array;
    
	$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);

	if (!isset($view_layout_array['layout_meta_html']) || $_POST["layout_val"] != $view_layout_array['layout_meta_html']) {
   		$view_layout_array['layout_meta_html'] = $_POST["layout_val"];
		$changed = true;
	}
	wpv_register_wpml_strings( $_POST["layout_val"] );
	if (!isset($view_array['layout_meta_html_css']) || $_POST["layout_css_val"] != $view_array['layout_meta_html_css']) {
		$view_array['layout_meta_html_css'] = $_POST["layout_css_val"];
		$changed_bis = true;
	}
	if (!isset($view_array['layout_meta_html_js']) || $_POST["layout_js_val"] != $view_array['layout_meta_html_js']) {
		$view_array['layout_meta_html_js'] = $_POST["layout_js_val"];
		$changed_bis = true;
	}
	if ($changed || $changed_bis) {
            
        // We need to pass the previous value for some reason.
        // Otherwise update_post_meta returns 0 because it thinks nothing has changed.
		$result = update_post_meta($_POST["id"], '_wpv_layout_settings', $view_layout_array, $previous_layout);
                
		$result_bis = update_post_meta($_POST["id"], '_wpv_settings', $view_array);
                
		echo ($result || $result_bis) ? $_POST["id"] : false;
	} else {
		echo $_POST["id"];
	}
	die();
}

// Layout Extra JS save callback function

add_action('wp_ajax_wpv_update_layout_extra_js', 'wpv_update_layout_extra_js_callback');

function wpv_update_layout_extra_js_callback() {
	$nonce = $_POST["wpnonce"];
	if (! wp_verify_nonce($nonce, 'wpv_view_layout_settings_extra_js_nonce') ) die("Security check");
	$view_array = get_post_meta($_POST["id"], '_wpv_layout_settings', true);
	if (isset($view_array['additional_js']) && $_POST["value"] == $view_array['additional_js']) {
		echo $_POST["id"];
		die();
	}
	$view_array['additional_js'] = $_POST["value"];
	$result = update_post_meta($_POST["id"], '_wpv_layout_settings', $view_array);
        echo $result ? $_POST["id"] : false;
        die();
}

// Content save callback function

add_action('wp_ajax_wpv_update_content', 'wpv_update_content_callback');

function wpv_update_content_callback() {
	$nonce = $_POST["wpnonce"];
	if (! wp_verify_nonce($nonce, 'wpv_view_content_nonce') ) die("Security check");
	$content_post = get_post($_POST["id"]);
	$content = $content_post->post_content;
	wpv_register_wpml_strings( $_POST["content"] );
	if ($_POST["content"] == $content) {
		echo $_POST["id"];
		die();
	}
	$this_post = array();
	$this_post['ID'] = $_POST["id"];
	$this_post['post_content'] = $_POST["content"];
	$result = wp_update_post( $this_post );
    echo $result ? $_POST["id"] : false;
    die();
}

/*
* Views listing screen
*/

/**
* wpv_create_view_callback
*
* View create callback function
*
* AJAX callback for the wpv_create_view action
*
* @param $_POST['wpnonce'] (string) 'wp_nonce_create_view'
* @param $_POST["title"] (string) (optional) Title for the View
* @param $_POST['kind'] (string) (optional) <normal> <archive>
* @param $_POST['purpose'] (string) (optional) <all> <pagination> <slider> <parametric> <full>
*
* @return (ID|JSON) New View ID on success or JSONed array('error'=>'error', 'error_message'=>'The error message') on fail
*
* @uses wpv_create_view
*
* @since 1.3.0
*/

add_action( 'wp_ajax_wpv_create_view', 'wpv_create_view_callback' );

function wpv_create_view_callback() {

	if ( ! wp_verify_nonce( $_POST["wpnonce"], 'wp_nonce_create_view' ) ) die("Security check");
	
	if ( !isset( $_POST["title"] ) || $_POST["title"] == '' ) $_POST["title"] = __('Unnamed View', 'wp-views');
    if ( !isset( $_POST["kind"] ) || $_POST["kind"] == '' ) $_POST["kind"] = 'normal';
    if ( !isset( $_POST["purpose"] ) || $_POST["purpose"] == '' ) $_POST["purpose"] = 'full';
    
    $args = array(
		'title' => $_POST["title"],
		'settings' => array(
			'view-query-mode' => $_POST["kind"],
			'purpose' => $_POST["purpose"]
		)
    );
    
    $response = wpv_create_view( $args );
    $result = array();
    
    if ( isset( $response['success'] ) ) {
		echo $response['success'];
    } else if ( isset( $response['error'] ) ) {
		$result['error'] = 'error';
		$result['error_message'] = $response['error'];
		echo json_encode( $result );
    } else {
		$result['error'] = 'error';
		$result['error_message'] = __('The View could not be created', 'wpv-views');
		echo json_encode( $result );
    }

	die();
}

// View Scan usage callback action

add_action('wp_ajax_wpv_scan_view', 'wpv_scan_view_callback');

function wpv_scan_view_callback() {
    global $wpdb, $sitepress;

    $nonce = $_POST["wpnonce"];
    if (! wp_verify_nonce($nonce, 'work_views_listing') ) die("Security check"); // TODO change this nonce

    $view = get_post($_POST["id"]);

    $list = ''; // TODO where the hell is this list used anymore?
    $list .= '<ul class="posts-list">';
    $needle = '%[wpv-view%name="%' . esc_sql($view->post_title). '%"]%';
    $needle = esc_sql( $needle );
    $needle_name = '%[wpv-view%name="%' . esc_sql($view->post_name). '%"]%';
    $needle_name = esc_sql( $needle_name );
    
    $trans_join = '';
    $trans_where = '';
    $trans_meta_where = '';
    
    if (function_exists('icl_object_id')) {
	$current_lang_code = $sitepress->get_current_language();
	$trans_join = " JOIN {$wpdb->prefix}icl_translations t ";
	$trans_where = " AND ID = t.element_id AND t.language_code =  '{$current_lang_code}' ";
	$trans_meta_where = " AND post_id = t.element_id AND t.language_code =  '{$current_lang_code}' ";
    }

    $q = "SELECT DISTINCT * FROM {$wpdb->posts} WHERE
     ID in (SELECT DISTINCT ID FROM {$wpdb->posts} {$trans_join} WHERE ( post_content LIKE '{$needle}' OR post_content LIKE '{$needle_name}' ) AND post_type NOT IN ('revision') AND post_status='publish' {$trans_where})
     OR
     ID in (SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE ( meta_value LIKE '{$needle}' OR meta_value LIKE '{$needle_name}' ) AND post_status='publish')";
    $res = $wpdb->get_results($q, OBJECT);


   if (!empty($res)) {
        $items = array();
        foreach ($res as $row) {
            $item = array();

            $type = get_post_type_object($row->post_type);

            $type = $type->labels->singular_name;

            $item['post_title'] = "<b>".$type.": </b>".$row->post_title;

            if ($row->post_type=='view')
                $edit_link = get_admin_url()."admin.php?page=views-editor&view_id=".$row->ID;
            else
                $edit_link = get_admin_url()."post.php?post=".$row->ID."&action=edit";

            $item['link'] = $edit_link;

            $items[] = $item;
        }
        echo json_encode($items);
    }


    die();
}

// View duplicate callback function

add_action('wp_ajax_wpv_duplicate_this_view', 'wpv_duplicate_this_view_callback');

function wpv_duplicate_this_view_callback() {
	if (! wp_verify_nonce($_POST["wpnonce"], 'wpv_duplicate_view_nonce') ) die("Security check");
	global $wpdb;
	$postid = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type='view'", $_POST["name"] ) );
	if ( !empty($postid)  ){
		echo 'error';
		die;
	}

	$old_post_id = $_POST["id"];
	$original_post = get_post($old_post_id, ARRAY_A);

	$original_post['post_title'] = $_POST["name"];

	unset( $original_post['ID'] );
	unset( $original_post['post_name'] );
	unset( $original_post['post_date'] );
	unset( $original_post['post_date_gmt'] );

	$new_post_id = wp_insert_post($original_post);

	$view_array = get_post_meta($old_post_id, '_wpv_settings', true);
	$view_layout_array = get_post_meta($old_post_id, '_wpv_layout_settings', true);
	$view_desc = get_post_meta( $old_post_id, '_wpv_description', true );
	update_post_meta($new_post_id, '_wpv_settings', $view_array);
	update_post_meta($new_post_id, '_wpv_layout_settings', $view_layout_array);
	update_post_meta( $new_post_id, '_wpv_description', $view_desc );

	echo $_POST["id"];
	die();
}

/*
* WP Archive listing screen
*/

// Add up, down or first WP Archive - popup structure

add_action('wp_ajax_wpv_create_wp_archive_button', 'wpv_create_wp_archive_button_callback');

function wpv_create_wp_archive_button_callback() {

	if (! wp_verify_nonce($_POST["wpnonce"], 'work_views_listing') ) die("Security check");

        global $WPV_view_archive_loop;
        echo $WPV_view_archive_loop->_create_view_archive_popup();
        die();
}

// Add up, down or first WP Archive callback function
// Uses the same callback as in the usage arrange mode

add_action('wp_ajax_wpv_create_archive_view', 'wp_ajax_wpv_create_usage_archive_view_callback');

// Change usage for WP Archive in name arrange - popup structure

add_action('wp_ajax_wpv_archive_change_usage_popup', 'wpv_archive_change_usage_popup_callback');

function wpv_archive_change_usage_popup_callback() {
    if (! wp_verify_nonce($_POST["wpnonce"], 'work_views_listing') ) die("Security check");

        global $WPV_view_archive_loop;

        $id = $_POST["id"];

        echo $WPV_view_archive_loop->_create_view_archive_popup($id);
        die();
}

// Change usage for Archive in name arrange callback function

add_action('wp_ajax_wpv_archive_change_usage', 'wpv_archive_change_usage_callback');

function wpv_archive_change_usage_callback() {
	global $wpdb;
	$nonce = $_POST["wpnonce"];
	if (! wp_verify_nonce($nonce, 'work_views_listing') ) die("Security check");

        global $WPV_view_archive_loop;
        parse_str($_POST['form'], $form_data);

	$archive_id = $form_data["wpv-archive-view-id"];

	$WPV_view_archive_loop->update_view_archive_settings($archive_id, $form_data);
	echo 'ok';
	die();
}

// Create WP Archive in usage arrange - popup structure

add_action('wp_ajax_wpv_create_usage_archive_view_popup', 'wpv_create_usage_archive_view_popup_callback');

function wpv_create_usage_archive_view_popup_callback(){
	$nonce = $_POST["wpnonce"];
	if (! wp_verify_nonce($nonce, 'wpv_wp_archive_arrange_usage') ) die("Security check");
    global $WPV_view_archive_loop, $WP_Views;
    $options = $WP_Views->get_options();
    $loops = $WPV_view_archive_loop->_get_post_type_loops();
        ?>
        <div class="wpv-dialog wpv-dialog-change js-wpv-dialog-change">
                <div class="wpv-dialog-header">
                    <h2><?php _e('Name of WordPress Archive for','wpv-views'); ?> <strong><?php echo $_POST['for_whom']; ?></strong></h2>
                    <i class="icon-remove js-dialog-close"></i>
                </div>
		<form id="wpv-add-wp-archive-for-loop-form">
                <div class="wpv-dialog-content">		
		<div class="hidden">
                    <?php
                        foreach($loops as $loop => $loop_name) {
                            foreach ($options as $opt_id=> $opt_name) {
				                if ('view_'.$loop == $opt_id && $opt_name !== 0) {
                                    unset($loops[$loop]);
                                    break;
                                }
                            }
                        }
                    ?>

                    <?php if (!empty($loops)) {  ?>
                        <?php foreach($loops as $loop => $loop_name) { ?>
                            <?php $checked = ( $loop_name == $_POST['for_whom'] ) ? ' checked="checked"' : ''; ?>
                                <input type="checkbox" <?php echo $checked; ?> name="wpv-view-loop-<?php echo $loop; ?>" />
                        <?php }; ?>
                    <?php } ?>

                    <?php
                    $taxonomies = get_taxonomies('', 'objects');
                    $exclude_tax_slugs = array();
			$exclude_tax_slugs = apply_filters( 'wpv_admin_exclude_tax_slugs', $exclude_tax_slugs );
                        foreach ($taxonomies as $category_slug => $category) {
                           if ( in_array( $category_slug, $exclude_tax_slugs ) ) {
                                    unset($taxonomies[$category_slug]);
                                    continue;
                            };
                            foreach ($options as $opt_id=> $opt_name) {
				if ('view_taxonomy_loop_' . $category_slug == $opt_id && $opt_name !== 0) {
                                    unset($taxonomies[$category_slug]);
                                    break;
                                };
                            };
                        };
                    ?>

                    <?php if (!empty($taxonomies)): ?>
                        <?php foreach ($taxonomies as $category_slug => $category): ?>
                            <?php
                                $name = $category->name;
                                $checked = ( $category->labels->name == $_POST['for_whom'] ) ? ' checked="checked"' : '';
                            ?>
                            <input type="checkbox" <?php echo $checked; ?> name="wpv-view-taxonomy-loop-<?php echo $name; ?>" />
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </div>
		
                    <p>
                        <input type="text" value="" class="js-wpv-new-archive-name wpv-new-archive-name" placeholder="<?php echo htmlentities( __('WordPress Archive name','wpv-views'), ENT_QUOTES ) ?>" name="wpv-new-archive-name">
                    </p>
                    <div class="js-wp-archive-create-error"></div>
                </div>
		</form>
                <div class="wpv-dialog-footer">
                    <button class="button-secondary js-dialog-close" type="button" name="wpv-archive-view-cancel"><?php _e('Cancel', 'wpv-views'); ?></button>
                    <button class="button-secondary js-wpv-add-wp-archive-for-loop" disabled="disabled" name="wpv-archive-view-ok" data-error="<?php echo htmlentities( __('A WordPress Archive with that name already exists. Please use another name.', 'wpv-views'), ENT_QUOTES ); ?>" data-url="<?php echo admin_url( 'admin.php?page=view-archives-editor&amp;view_id='); ?>">
                        <?php _e('Add new WordPress Archive', 'wpv-views'); ?>
                    </button>
                </div>
        </div>
    <?php die();
}

// Create WP Archive in usage arrange callback function

add_action('wp_ajax_wpv_create_usage_archive_view', 'wp_ajax_wpv_create_usage_archive_view_callback');

function wp_ajax_wpv_create_usage_archive_view_callback() {
	$nonce = $_POST["wpnonce"];
	if (! wp_verify_nonce($nonce, 'work_views_listing') ) die("Security check");

        global $wpdb, $WPV_view_archive_loop;
        parse_str($_POST['form'], $form_data);

	// Create archive
	$postid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_title = '" . $form_data["wpv-new-archive-name"] . "' AND post_type='view'" );
	if ( !empty($postid)  ){
	echo 'error';
	die();
	}
	$new_archive = array(
	'post_title'    => $form_data["wpv-new-archive-name"],
	'post_type'      => 'view',
	'post_content'  => "[wpv-layout-meta-html]",
	'post_status'   => 'publish',
	'post_author'   => get_current_user_id(),
	'comment_status' => 'closed'
	);
	$post_id = wp_insert_post($new_archive);

	$archive_defaults = wpv_wordpress_archives_defaults('view_settings');
	$archive_layout_defaults = wpv_wordpress_archives_defaults('view_layout_settings');
	update_post_meta($post_id, '_wpv_settings', $archive_defaults);
	update_post_meta($post_id, '_wpv_layout_settings', $archive_layout_defaults);

	$WPV_view_archive_loop->update_view_archive_settings($post_id, $form_data);

	echo $post_id;
	die();
}

// Change WP Archive usage in usage arrange - popup structure
// TODO review this SQL syntax...

add_action('wp_ajax_wpv_show_views_for_loop', 'wpv_show_views_for_loop_callback');

function wpv_show_views_for_loop_callback() {
        global $WPV_view_archive_loop, $wpdb, $WP_Views;
	if (! wp_verify_nonce($_POST["wpnonce"], 'wpv_wp_archive_arrange_usage') ) die("Security check");

        $options = $WP_Views->get_options();

        $loops = $WPV_view_archive_loop->_get_post_type_loops();

?>
        <div class="wpv-dialog wpv-dialog-change js-wpv-dialog-change">
            <form id="wpv-archive-view-form-for-loop">
                <div class="wpv-dialog-header">
                    <h2><?php _e('Select WordPress Archive For Loop','wpv-views'); ?></h2>
                    <i class="icon-remove js-dialog-close"></i>
                </div>
                <div class="wpv-dialog-content">
                    <?php wp_nonce_field('wpv_view_edit_nonce', 'wpv_view_edit_nonce'); ?>

                    <input type="hidden" value="<?php echo $_POST["id"]; ?>" name="wpv-archive-loop-key" />

                    <?php
                        $q = ('
                            SELECT DISTINCT wpp.* FROM `'.$wpdb->prefix.'posts` wpp
                            WHERE
                                ID in (SELECT post_id FROM `'.$wpdb->prefix.'postmeta` WHERE `meta_value` like \'%view-query-mode%"archive"%\' AND `meta_key`="_wpv_settings")
                            AND
                                wpp.post_status="publish"
                            AND
                                wpp.post_type="view"
                            ORDER BY wpp.post_date DESC
                        ');

                        $res = $wpdb->get_results($q, OBJECT);

                        ?>
                        <h3><?php _e('Archive views', 'wpv-views'); ?></h3>
                        <ul>
                            <li>
                                <label>
                                    <input type="radio" name="wpv-view-loop-archive" value="0" /> <?php _e('Don\'t use a WordPress Archive for this loop', 'wpv-views'); ?>
                                </label>
                            </li>
                        <?php
                        foreach ($res as $view) {
                            $checked = '';
                            if (isset($options[$_POST["id"]]) && $view->ID == $options[$_POST["id"]]) {
                                $checked = ' checked ';
                            }
                            ?>
                            <li>
                                <label>
                                    <input type="radio" <?php echo $checked; ?> name="wpv-view-loop-archive" value="<?php echo $view->ID; ?>" /> <?php echo $view->post_title; ?>
                                </label>
                            </li>
                            <?php
                        }
                        ?>
                        </ul>

                </div>
                <div class="wpv-dialog-footer">
                    <button class="button-secondary js-dialog-close" type="button" name="wpv-archive-view-cancel"><?php _e('Cancel', 'wpv-views'); ?></button>
                    <button class="button-primary js-update-archive-for-loop" type="button" name="wpv-archive-view-ok">
                        <?php _e('Accept', 'wpv-views'); ?>
                    </button>
                </div>
            </form>
        </div>
<?php
        die();
}

// Change WP Archive usage in usage arrange callback function

add_action('wp_ajax_wpv_update_archive_for_view', 'wpv_update_archive_for_view_callback');

function wpv_update_archive_for_view_callback() {
	global $WP_Views;
//	global $WPV_view_archive_loop;
	if (! wp_verify_nonce($_POST["wpnonce"], 'wpv_wp_archive_arrange_usage') ) die("Security check");

	$options = $WP_Views->get_options();

	$options[$_POST["loop"]] = $_POST["selected"];
	foreach($options as $key => $value) {
		if ($value == 0) unset($options[$key]);
	}

	$WP_Views->save_options( $options );

	echo 'ok';
	die();
}

// Delete Views and WPA permanently callback function TODO add different nonces for Views and for WPA

add_action('wp_ajax_wpv_delete_view_permanent', 'wpv_delete_view_permanent_callback');

function wpv_delete_view_permanent_callback() {
        global $WPV_view_archive_loop, $WP_Views;

	$nonce = $_POST["wpnonce"];
	if (! wp_verify_nonce($nonce, 'wpv_remove_view_permanent_nonce') ) die("Security check");

	wp_delete_post($_POST["id"]);
	
	// Clean options - when deleting WPA
        $options = $WP_Views->get_options();
        WP_Views_archive_loops::clear_options_data($options);
        $WP_Views->save_options($options);

        echo $_POST["id"];
        die();
}

// Change status of View and WPA callback function TODO use a more generic function name

add_action('wp_ajax_wpv_view_change_status', 'wpv_view_change_status_callback');

function wpv_view_change_status_callback(){
	$nonce = $_POST["wpnonce"];
	if ( ! (
		wp_verify_nonce($nonce, 'wpv_view_listing_actions_nonce') || // from the Views listing screen OR
		wp_verify_nonce($nonce, 'wpv_view_change_status') // from the View edit screen
	) ) die("Security check");

	if ( !isset( $_POST['newstatus'] ) ) $_POST['newstatus'] = 'publish';
	$my_post = array(
		'ID'           => $_POST["id"],
		'post_status' => $_POST['newstatus']
	);

	$return = wp_update_post( $my_post );

        echo $return;
        die();
}

/*
* Content Templates
*/

/**
* wpv_ct_update_posts_callback
*
* Callback function for the AJAX action wp_ajax_wpv_ct_update_posts used to count dissident posts that are not using the Template assigned to its type
* This is called on the Content Templates listing screen for single usage and on the Template edit screen
*
* Added by Gen TODO check this nonce
*
* @since 1.3.0
*
* @uses wpv_count_dissident_posts_from_template
*/
 
add_action('wp_ajax_wpv_ct_update_posts', 'wpv_ct_update_posts_callback');

function wpv_ct_update_posts_callback(){
    if ( !isset($_GET["wpnonce"]) || ! wp_verify_nonce($_GET["wpnonce"], 'work_view_template') ) die("Undefined Nonce.");
    global $WP_Views;
    $options = $WP_Views->get_options();
    if ( isset ($_GET['type']) && isset($_GET['tid']) ){
        $type = $_GET['type'];
        $tid = $options['views_template_for_' . $type];
    }
    else {
      return;
    }
    wpv_count_dissident_posts_from_template( $tid, $type );
	die();
}

// Unlink a Content Template for orphaned single posts types when there is no general Template asociated with that type

add_action('wp_ajax_wpv_single_unlink_template', 'wpv_single_unlink_template_callback');

function wpv_single_unlink_template_callback() {
	if ( !isset( $_POST["wpnonce"] ) || ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_single_unlink_template_nonce') ) die("Undefined Nonce.");
	if ( !isset( $_POST['slug'] ) ) {
		echo __('Slug not set in the AJAX call', 'wpv-wiews');
	} else {
		global $wpdb;
		$type = $_POST['slug'];
		$posts = $wpdb->get_col( "SELECT {$wpdb->posts}.ID FROM {$wpdb->posts}  WHERE post_type='{$type}'" );
		$count = sizeof( $posts );
		if ( $count > 0 ) {
		foreach ( $posts as $post ) {
			update_post_meta( $post, '_views_template', 0 );
			}
		}
		echo 'ok';
	}
	die();
}

/*
 * Add new Content Template - popup structure
 * Added by Gen TODO check this nonce
 */
 
add_action('wp_ajax_wpv_ct_create_new', 'wpv_ct_create_new_callback');

function wpv_ct_create_new_callback(){
   if ( !isset($_GET["wpnonce"]) || ! wp_verify_nonce($_GET["wpnonce"], 'work_view_template') ) die("Undefined Nonce.");
   global $wpdb, $WP_Views;
   $options = $WP_Views->get_options();
   $post_types_array = wpv_get_pt_tax_array();
   $ct_title = $ct_selected = '';
   if ( isset($_GET['ct_title']) ){
       $ct_title = $_GET['ct_title'];
   }
   if ( isset($_GET['ct_selected']) ){
       $ct_selected = $_GET['ct_selected'];
   }
	$asterisk = '<span style="color:red">*</span>';
	$asterisk_explanation = __( '<span style="color:red">*</span> A different Content Template is already assigned to this item', 'wpv-views' );
   ?>
    <div class="wpv-dialog js-wpv-dialog-add-new-content-template wpv-dialog-add-new-content-template">
        <form method="" id="wpv-add-new-content-template-form">
	        <div class="wpv-dialog-header">
	            <h2><?php _e('Add new Content Template','wpv-views'); ?></h2>
	            <i class="icon-remove js-dialog-close"></i>
	        </div>
	        <div class="wpv-dialog-content">
	            <p><strong><?php _e('What content will this template be for?','wpv-views') ?></strong></p>

                <p>
					<input id="wpv-content-template-no-use" type="checkbox" class="js-dont-assign"<?php echo $ct_selected == '' ? ' checked="checked"' : ''; ?> name="wpv-new-content-template-post-type[]" value="0" />
                	<label for="wpv-content-template-no-use"><?php _e("Don't assign to any post type",'wpv-views') ?></label>
                </p>
				
				<div>
					<p>
						<span class="js-wpv-content-template-open wpv-content-template-open" title="<?php echo htmlentities( __( "Click to toggle", 'wpv-views' ), ENT_QUOTES ); ?>">
							<?php echo __( 'Single pages', 'wpv-views' ); ?>:
							<i class="icon-caret-down"></i>
						</span>
					</p>
					<?php
					$single_posts = $post_types_array['single_post'];//key is views_template_for_
					$open_section = false;
					$show_asterisk_explanation = false;
					ob_start();
					if ( count( $single_posts ) > 0 ) {
						?>
						<ul>
						<?php
						foreach ( $single_posts as $s_post ) {// $s_post is an array with each element being (name, label)
							$type = $s_post[0];
							$label = $s_post[1];
							$type_current = $type_used = false;
							if ( isset( $options['views_template_for_' . $type] ) && $options['views_template_for_' . $type] != 0 ) {
								$type_used = true;
								$show_asterisk_explanation = true;
							}
							if ( 'views_template_for_' . $type == $ct_selected ) {
								$type_current = true;
								$type_used = false;
								$open_section = true;
							}
							?>
							<li>
								<input id="<?php echo 'views_template_for_' . $type; ?>" type="checkbox" name="wpv-new-content-template-post-type[]"<?php echo $type_current ? ' checked="checked"' : '';?> data-title="<?php echo esc_attr( $label ); ?>" value="<?php echo 'views_template_for_' . $type; ?>" />
								<label for="<?php echo 'views_template_for_' . $type; ?>"><?php echo $label; echo $type_used ? $asterisk : ''; ?></label>
							</li>
						<?php
						}
						?>
						</ul>
						<?php if ( $show_asterisk_explanation ) { ?>
						<span class="wpv-asterisk-explanation">
							<?php echo $asterisk_explanation; ?>
						</span>
						<?php } ?>
						<?php
					} else {
						_e( 'There are no single post types to assign Content Templates to', 'wpv-views' );
					}
					$s_content = ob_get_clean();
					?>
					<div class="js-wpv-content-template-dropdown-list wpv-content-template-dropdown-list<?php echo $open_section ? '' : ' hidden'; ?>">
						<?php echo $s_content; ?>
					</div>
					<p>
						<span class="js-wpv-content-template-open wpv-content-template-open" title="<?php echo htmlentities( __( "Click to toggle", 'wpv-views' ), ENT_QUOTES ); ?>">
							<?php echo __( 'Post type archives', 'wpv-views' ); ?>:
							<i class="icon-caret-down"></i>
						</span>
					</p>
					<?php
					$archive_posts = $post_types_array['archive_post'];//key is views_template_archive_for_
					$open_section = false;
					$show_asterisk_explanation = false;
					ob_start();
					if ( count( $archive_posts ) > 0 ) {
						?>
						<ul>
						<?php
						foreach ( $archive_posts as $s_post ) {// $s_post is an array with each element being (name, label)
							$type = $s_post[0];
							$label = $s_post[1];
							$type_current = $type_used = false;
							if ( isset( $options['views_template_archive_for_' . $type] ) && $options['views_template_archive_for_' . $type] != 0 ) {
								$type_used = true;
								$show_asterisk_explanation = true;
							}
							if ( 'views_template_archive_for_' . $type == $ct_selected ) {
								$type_current = true;
								$type_used = false;
								$open_section = true;
							}
							?>
							<li>
								<input id="<?php echo 'views_template_archive_for_' . $type; ?>" type="checkbox" name="wpv-new-content-template-post-type[]"<?php echo $type_current ? ' checked="checked"' : ''; ?> data-title="<?php echo esc_attr( $label ); ?>" value="<?php echo 'views_template_archive_for_' . $type; ?>" />
								<label for="<?php echo 'views_template_archive_for_' . $type; ?>"><?php echo $label; echo $type_used ? $asterisk : ''; ?></label>
							</li>
							<?php
						}
						?>
						</ul>
						<?php if ( $show_asterisk_explanation ) { ?>
						<span class="wpv-asterisk-explanation">
							<?php echo $asterisk_explanation; ?>
						</span>
						<?php } ?>
						<?php
					} else {
						_e( 'There are no post type archives to assign Content Templates to', 'wpv-views' );
					}
					$pta_content = ob_get_clean();
					?>
					<div class="js-wpv-content-template-dropdown-list wpv-content-template-dropdown-list<?php echo $open_section ? '' : ' hidden'; ?>">
						<?php echo $pta_content; ?>
					</div>
					<p>
						<span class="js-wpv-content-template-open wpv-content-template-open" title="<?php echo htmlentities( __( "Click to toggle", 'wpv-views' ), ENT_QUOTES ); ?>">
							<?php echo __( 'Taxonomy archives', 'wpv-views' ); ?>:
							<i class="icon-caret-down"></i>
						</span>
					</p>
					<?php
					$archive_taxes = $post_types_array['taxonomy_post'];//key is views_template_loop_
					$open_section = false;
					$show_asterisk_explanation = false;
					ob_start();
					if ( count( $archive_taxes ) > 0 ) {
						?>
						<ul>
						<?php
						foreach ( $archive_taxes as $s_post ) {// $s_post is an array with each element being (name, label)
							$type = $s_post[0];
							$label = $s_post[1];
							$type_current = $type_used = false;
							if ( isset( $options['views_template_loop_' . $type] ) && $options['views_template_loop_' . $type] != 0 ) {
								$type_used = true;
								$show_asterisk_explanation = true;
							}
							if ( 'views_template_loop_' . $type == $ct_selected ) {
								$type_current = true;
								$type_used = false;
								$open_section = true;
							}
							?>
							<li>
								<input id="<?php echo 'views_template_loop_' . $type; ?>" type="checkbox" name="wpv-new-content-template-post-type[]"<?php echo $type_current? ' checked="checked"' : '';?> data-title="<?php echo esc_attr( $label ); ?>" value="<?php echo 'views_template_loop_' . $type; ?>" />
								<label for="<?php echo 'views_template_loop_' . $type; ?>"><?php echo $label; echo $type_used ? $asterisk : ''; ?></label>
							</li>
							<?php
						}
						?>
						</ul>
						<?php if ( $show_asterisk_explanation ) { ?>
						<span class="wpv-asterisk-explanation">
							<?php echo $asterisk_explanation; ?>
						</span>
						<?php } ?>
						<?php
					} else {
						_e( 'There are no taxonomy archives to assign Content Templates to', 'wpv-views' );
					}
					$tax_content = ob_get_clean();
					?>
					<div class="js-wpv-content-template-dropdown-list wpv-content-template-dropdown-list<?php echo $open_section ? '' : ' hidden'; ?>">
						<?php echo $tax_content; ?>
					</div>
				</div>
                <p>
                	<strong><?php _e('Name this Content Template','wpv-views') ?></strong>
                </p>
	            <p>
	                <input type="text" value="<?php echo htmlentities( $ct_title, ENT_QUOTES ); ?>" class="js-wpv-new-content-template-name wpv-new-content-template-name" placeholder="<?php echo htmlentities( __('Content template name','wpv-views'), ENT_QUOTES ) ?>" name="wpv-new-content-template-name">
	            </p>
                <div class="js-error-container">
                </div>
	        </div> <!-- .wpv-dialog-content -->
	        <div class="wpv-dialog-footer">
	            <button class="button js-dialog-close"><?php _e('Cancel','wpv-views') ?></button>
	            <button class="button button-primary js-create-new-template"><?php _e('Create a template','wpv-views') ?></button>
	        </div>
        </form>

    </div> <!-- wpv-dialog -->
    <?php
     die();
}

/*
 * Save new CT callback function
 * Added by Gen TODO check this nonce
 */
 
add_action('wp_ajax_wpv_ct_create_new_save', 'wpv_ct_create_new_save_callback');

function wpv_ct_create_new_save_callback(){
    if ( !isset($_POST["wpnonce"]) || ! wp_verify_nonce($_POST["wpnonce"], 'work_view_template') ) die("Undefined Nonce.");

    global $wpdb, $WP_Views;
    $options = $WP_Views->get_options();


        $name = $title = '';
        if ( isset($_POST['name']) ){
            $name = wp_strip_all_tags($_POST['name']);
        }
        if ( isset($_POST['title']) ){
           $title = $_POST['title'];
        }
        if ( !isset( $_POST['type'] ) ) {
			$_POST['type'] = array(0);
        }
        $type = $_POST['type'];
        $old_post = get_page_by_title( $name , OBJECT, 'view-template');
        if ( is_object($old_post) ){
           print json_encode( array('error', __( 'A Content Template with that name already exists. Please use another name.', 'wpv-views' )) );
           die();
        }

        $new_template = array(
          'post_title'    => $name,
          'post_type'      => 'view-template',
          'post_content'  => '',
          'post_status'   => 'publish',
          'post_author'   => 1, // TODO check why author here
//          'post_name' => sanitize_title( $name )
        );

        $post_id = wp_insert_post( $new_template );
        update_post_meta( $post_id, '_wpv-content-template-decription', '');
        if ( $type[0] != '0' ){
             for ($i=0;$i<count($type);$i++){
                 $options[$type[$i]] = $post_id;
             }
             $WP_Views->save_options( $options );
        }
        print json_encode( array($post_id) );

   die();
}

// Check if another CT with the same name already exists TODO check where this is used TODO check this nonce

add_action('wp_ajax_wpv_ct_check_name_exists', 'wpv_ct_check_name_exists_callback');

function wpv_ct_check_name_exists_callback(){
    if ( !isset($_POST["wpnonce"]) || ! wp_verify_nonce($_POST["wpnonce"], 'set_view_template') ) die("Undefined Nonce.");

    global $wpdb, $post;

        $name = $title = '';
        if ( isset($_POST['title']) ){
            $name = $_POST['title'];
        }
        $id = $_POST['id'];
        $postid = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type='view-template' AND ID != %d", $name, $id ) );
        if ( !empty($postid)  ){
           print json_encode( array('error', __( 'A Content Template with that name already exists. Please use another name.', 'wpv-views' )) );
           die();
        }
        print json_encode( array('Ok', '') );
   die();
}

// Delete CT callback function

add_action('wp_ajax_wpv_delete_ct', 'wpv_delete_ct_callback');

function wpv_delete_ct_callback(){

    if ( !isset($_POST["wpnonce"]) || ! wp_verify_nonce($_POST["wpnonce"], 'work_view_template') ) die("Undefined Nonce.");

    global $wpdb, $WP_Views;
    $options = $WP_Views->get_options();
    $tid = $_POST['id'];
    foreach ($options as $key => $value) {
         if ($value == $tid){
            $options[$key] = 0;
         }
    }
    $WP_Views->save_options( $options );
    wp_delete_post($tid);
	echo $tid;
    die();
}

//Duplicate CT callback function

add_action('wp_ajax_wpv_duplicate_ct', 'wpv_duplicate_ct_callback');

function wpv_duplicate_ct_callback(){
    global $wpdb;
    if ( !isset($_POST["wpnonce"]) || ! wp_verify_nonce($_POST["wpnonce"], 'work_view_template') ) die("Undefined Nonce.");
        
        $postid = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type='view-template'", $_POST["name"] ) );
        if ( !empty($postid)  ){
               print json_encode( array('error', __( 'A Content Template with that name already exists. Please use another name.', 'wpv-views' ) ) );
               die();
        }
        
        $old_post_id = $_POST["id"];
        $original_post = get_post($old_post_id, ARRAY_A);
        unset($original_post['ID']);
        $new_template = array(
          'post_title'    => $_POST["name"],
          'post_type'      => 'view-template',
          'post_content'  => $original_post['post_content'],
          'post_status'   => 'publish',
          'post_author'   => 1 // TODO check why author here
        );
        $new_post_id = wp_insert_post($new_template);
        update_post_meta( $new_post_id, '_wpv-content-template-decription', '');
        $old_post_meta = get_post_meta($old_post_id);
        foreach ($old_post_meta as $key => $value) {
            if ($key == '_edit_lock'){
                continue;
            }
            update_post_meta($new_post_id, $key, $value[0]);
        }
        print json_encode( array('ok') );

    die();
}

// Connect a CT to a View Layout MetaHTML textarea - popup structure TODO check nonce

add_action('wp_ajax_wpv_assign_ct_to_view', 'wpv_assign_ct_to_view_callback');

function wpv_assign_ct_to_view_callback(){
    if ( !isset($_POST["wpnonce"]) || ! wp_verify_nonce($_POST["wpnonce"], 'wpv-ct-inline-edit') ) die("Undefined Nonce.");

        $view_id = $_POST['view_id'];
        $meta = get_post_meta( $view_id, '_wpv_layout_settings', true);
        $templates = array();
        if ( isset($meta['included_ct_ids']) && $meta['included_ct_ids'] != '' ){
            $templates = explode( ',', $meta['included_ct_ids']);
        }
       ?>

       <div class="wpv-dialog js-wpv-dialog-add-new-content-template">
                <form method="" id="wpv-add-new-content-template-form">
                <div class="wpv-dialog-header">
                    <h2><?php _e('Add new Content Template','wpv-views') ?></h2>
                    <i class="icon-remove js-dialog-close"></i>
                </div>
                <div class="wpv-dialog-content">
                    <p class="<?php echo count($templates) < 1 ? 'hidden' : '' ?>">
                        <input type="radio" name="wpv-ct-type" value="2" id="js-wpv-ct-type-existing-asigned">
                        <label for="js-wpv-ct-type-existing-asigned"><?php _e('A Content Template that is already connected to this View','wpv-views') ?>: </label>
                        <select id="js-wpv-ct-add-id-assigned">
                            <option value="0"><?php _e('Choose','wpv-views') ?>&hellip;</option>
                            <?php
                             $not_in = '';
                             $not_in_array = array();
                             $num_templates = count($templates);
                             for ($i=0; $i<$num_templates; $i++){
                                 if ( is_numeric( $templates[$i] ) ) {
									$template_post = get_post($templates[$i]);
									if ( is_object($template_post) ){
										$not_in_array[] =  $template_post->ID;
										echo '<option value="'.$template_post->ID.'">'. $template_post->post_title .'</option>';
									}
                                 }
                             }
                             $not_in = implode( ',', $not_in_array );
                            ?>
                        </select>
                    </p>
                    <?php $query =  get_posts(array( 'post_type' => 'view-template', 'exclude'=> $not_in, 'orderby' => 'title', 'order' => 'ASC', 'posts_per_page' => '-1' ));
                    ?>
                    <p class="<?php echo count($query) < 1 ? 'hidden' : '' ?>">
                        <input type="radio" name="wpv-ct-type" value="0" id="js-wpv-ct-type-existing">
                        <label for="js-wpv-ct-type-existing"><?php _e('Connect an existing Content template to this View','wpv-views') ?>: </label>
                        <select id="js-wpv-ct-add-id">
                            <option value="0"><?php _e('Choose','wpv-views') ?>&hellip;</option>
                            <?php

                            foreach( $query as $temp_post ) :
                                echo '<option value="'.$temp_post->ID.'">'. $temp_post->post_title .'</option>';
                            endforeach;
                            ?>
                        </select>
                    </p>
                    <p>
                        <input type="radio" name="wpv-ct-type" value="1" id="js-wpv-ct-type-new">
                        <label for="js-wpv-ct-type-new"><?php _e('Create a new Content Template for this View','wpv-views') ?>: </label>
                        <input type="text" id="js-wpv-ct-type-new-name" placeholder="Name">
                    </p>
                    <div class="js-add-new-ct-error-container"></div>
                    <p id="js-wpv-add-to-editor-line">
                        <input type="checkbox" class="js-wpv-add-to-editor-check" name="wpv-ct-add-to-editor" value="1" id="js-wpv-ct-add-to-editor-btn" checked="checked">
                        <label for="js-wpv-ct-add-to-editor-btn"><?php _e('Insert shortcode to editor','wpv-views') ?></label>
                    </p>
                </div>
                <div class="wpv-dialog-footer">
                    <button class="button js-dialog-close"><?php _e('Cancel','wpv-views') ?></button>
                    <button class="button button-primary js-create-new-template"><?php _e('Add template','wpv-views') ?></button>
                </div>
                </form>
         </div>

       <?php

    die();
}

// Delete the CT connection with a View - popup structure

add_action('wp_ajax_wpv_remove_content_template_from_view', 'wpv_remove_content_template_from_view_callback');

function wpv_remove_content_template_from_view_callback() {

    if ( !isset($_POST["wpnonce"]) || ! wp_verify_nonce($_POST["wpnonce"], 'wpv-ct-inline-edit') ) die("Undefined Nonce.");
    $view_id = $_POST['view_id'];
    $id = $_POST['id'];
    ?>

   <div class="wpv-dialog js-wpv-dialog-remove-content-template-from-view">
            <form method="" id="wpv-remove-content-template-from-view-form">
            <div class="wpv-dialog-header">
                <h2><?php _e('Remove the Content Template from the view','wpv-views') ?></h2>
                <i class="icon-remove js-dialog-close"></i>
            </div>
            <div class="wpv-dialog-content">
                <p>
                    <?php _e("This will remove the link between your view and the Content Template.  The Content Template will not be deleted.") ?>
                </p>
            </div>
            <div class="wpv-dialog-footer">
            	<p class="dont-show-again">
            		<input type="checkbox" id="dont-show-again" />
            		<label for="dont-show-again"><?php _e("Don't show this message again",'wpv-views') ?></label>
            	</p>
                <button class="button js-dialog-close"><?php _e('Cancel','wpv-views') ?></button>
                <button class="button button-primary js-remove-template-from-view" data-id="<?php echo $id; ?>" data-viewid="<?php echo $view_id; ?>"><?php _e('Remove','wpv-views') ?></button>
            </div>
            </form>
     </div>

   <?php

    die();
}

// Delete the CT connection with a View callback function TODO check this nonces

add_action('wp_ajax_wpv_remove_content_template_from_view_process', 'wpv_remove_content_template_from_view_process_callback');

function wpv_remove_content_template_from_view_process_callback() {

    if ( !isset($_POST["wpnonce"]) || ! wp_verify_nonce($_POST["wpnonce"], 'wpv-ct-inline-edit') ) die("Undefined Nonce.");
    $view_id = $_POST['view_id'];
    $id = $_POST['id'];

    $meta = get_post_meta( $view_id, '_wpv_layout_settings', true);
    $templates = $exists = '';
    if ( isset( $meta['included_ct_ids'] ) ) {
		$reg_templates = explode( ',', $meta['included_ct_ids'] );
		if ( in_array( $id, $reg_templates ) ) {
			$id_key = array_search( $id, $reg_templates );
			unset( $reg_templates[$id_key] );
		}
		$templates = implode( ',', $reg_templates );
    }
    if ( $exists == ''){
        $meta['included_ct_ids'] = $templates;
        print update_post_meta($view_id, '_wpv_layout_settings', $meta );
    }

    die();
}

// Change CT usage - popup structure TODO review this nonces

add_action('wp_ajax_ct_change_types', 'ct_change_types_callback');

function ct_change_types_callback(){
   if ( !isset( $_GET["wpnonce"] ) || ! wp_verify_nonce($_GET["wpnonce"], 'work_view_template') ) die( "Undefined nonce" );
   global $wpdb, $WP_Views;
   $options = $WP_Views->get_options();
   $post_types_array = wpv_get_pt_tax_array();
   $id = $_GET['id'];
   $asterisk = '<span style="color:red;">*</span>';
   $asterisk_explanation = __( '<span style="color:red">*</span> A different Content Template is already assigned to this item', 'wpv-views' );
	?>
    <div class="wpv-dialog js-wpv-dialog-add-new-content-template wpv-dialog-add-new-content-template">
        <form method="" id="wpv-add-new-content-template-form">
        <div class="wpv-dialog-header">
            <h2><?php _e('Change Types','wpv-views') ?></h2>
            <i class="icon-remove js-dialog-close"></i>
        </div>
        <div class="wpv-dialog-content">
            <p><?php _e('What content will this template be for?','wpv-views') ?></p>
            <div>
                <p>
					<span class="js-wpv-content-template-open wpv-content-template-open" title="<?php echo htmlentities( __( "Click to toggle", 'wpv-views' ), ENT_QUOTES ); ?>">
						<?php echo __( 'Single pages', 'wpv-views' ); ?>:
						<i class="icon-caret-down"></i>
					</span>
				</p>
                <?php
                $single_posts = $post_types_array['single_post'];//key is views_template_for_
                $open_section = false;
                $show_asterisk_explanation = false;
                ob_start();
                if ( count( $single_posts ) > 0 ) {
					?>
					<ul>
					<?php
					foreach ( $single_posts as $s_post ) {// $s_post is an array with each element being (name, label)
						$type = $s_post[0];
						$label = $s_post[1];
						$type_current = $type_used = false;
						if ( isset( $options['views_template_for_' . $type] ) && $options['views_template_for_' . $type] != 0 ) {
							$type_used = true;
							$show_asterisk_explanation = true;
						}
						if ( isset( $options['views_template_for_' . $type] ) && $options['views_template_for_' . $type] == $id ) {
							$type_current = true;
							$type_used = false;
							$open_section = true;
						}
						?>
						<li>
							<input id="<?php echo 'views_template_for_' . $type; ?>" type="checkbox" name="wpv-new-content-template-post-type[]"<?php echo $type_current? ' checked="checked"' : '';?> data-title="<?php echo esc_attr( $label ); ?>" value="<?php echo 'views_template_for_' . $type; ?>" />
							<label for="<?php echo 'views_template_for_' . $type; ?>"><?php echo $label; echo $type_used ? $asterisk : ''; ?></label>
						</li>
					<?php
					}
					?>
					</ul>
					<?php if ( $show_asterisk_explanation ) { ?>
					<span class="wpv-asterisk-explanation">
						<?php echo $asterisk_explanation; ?>
					</span>
					<?php } ?>
					<?php
                } else {
					_e( 'There are no single post types to assign Content Templates to', 'wpv-views' );
                }
                $s_content = ob_get_clean();
                ?>
                <div class="js-wpv-content-template-dropdown-list wpv-content-template-dropdown-list<?php echo $open_section ? '' : ' hidden'; ?>">
					<?php echo $s_content; ?>
                </div>
                <p>
					<span class="js-wpv-content-template-open wpv-content-template-open" title="<?php echo htmlentities( __( "Click to toggle", 'wpv-views' ), ENT_QUOTES ); ?>">
						<?php echo __( 'Post type archives', 'wpv-views' ); ?>:
						<i class="icon-caret-down"></i>
					</span>
				</p>
                <?php
                $archive_posts = $post_types_array['archive_post'];//key is views_template_archive_for_
                $open_section = false;
                $show_asterisk_explanation = false;
                ob_start();
                if ( count( $archive_posts ) > 0 ) {
					?>
					<ul>
					<?php
					foreach ( $archive_posts as $s_post ) {// $s_post is an array with each element being (name, label)
						$type = $s_post[0];
						$label = $s_post[1];
						$type_current = $type_used = false;
						if ( isset( $options['views_template_archive_for_' . $type] ) && $options['views_template_archive_for_' . $type] != 0 ) {
							$type_used = true;
							$show_asterisk_explanation = true;
						}
						if ( isset( $options['views_template_archive_for_' . $type] ) && $options['views_template_archive_for_' . $type] == $id ) {
							$type_current = true;
							$type_used = false;
							$open_section = true;
						}
						?>
						<li>
							<input id="<?php echo 'views_template_archive_for_' . $type; ?>" type="checkbox" name="wpv-new-content-template-post-type[]"<?php echo $type_current ? ' checked="checked"' : ''; ?> data-title="<?php echo esc_attr( $label ); ?>" value="<?php echo 'views_template_archive_for_' . $type; ?>" />
                            <label for="<?php echo 'views_template_archive_for_' . $type; ?>"><?php echo $label; echo $type_used ? $asterisk : ''; ?></label>
                        </li>
						<?php
					}
					?>
					</ul>
					<?php if ( $show_asterisk_explanation ) { ?>
					<span class="wpv-asterisk-explanation">
						<?php echo $asterisk_explanation; ?>
					</span>
					<?php } ?>
					<?php
                } else {
					_e( 'There are no post type archives to assign Content Templates to', 'wpv-views' );
                }
                $pta_content = ob_get_clean();
                ?>
				<div class="js-wpv-content-template-dropdown-list wpv-content-template-dropdown-list<?php echo $open_section ? '' : ' hidden'; ?>">
					<?php echo $pta_content; ?>
				</div>
				<p>
					<span class="js-wpv-content-template-open wpv-content-template-open" title="<?php echo htmlentities( __( "Click to toggle", 'wpv-views' ), ENT_QUOTES ); ?>">
						<?php echo __( 'Taxonomy archives', 'wpv-views' ); ?>:
						<i class="icon-caret-down"></i>
					</span>
				</p>
                <?php
                $archive_taxes = $post_types_array['taxonomy_post'];//key is views_template_loop_
                $open_section = false;
                $show_asterisk_explanation = false;
                ob_start();
                if ( count( $archive_taxes ) > 0 ) {
					?>
					<ul>
					<?php
					foreach ( $archive_taxes as $s_post ) {// $s_post is an array with each element being (name, label)
						$type = $s_post[0];
						$label = $s_post[1];
						$type_current = $type_used = false;
						if ( isset( $options['views_template_loop_' . $type] ) && $options['views_template_loop_' . $type] != 0 ) {
							$type_used = true;
							$show_asterisk_explanation = true;
						}
						if ( isset( $options['views_template_loop_' . $type] ) && $options['views_template_loop_' . $type] == $id ) {
							$type_current = true;
							$type_used = false;
							$open_section = true;
						}
						?>
						<li>
							<input id="<?php echo 'views_template_loop_' . $type; ?>" type="checkbox" name="wpv-new-content-template-post-type[]"<?php echo $type_current? ' checked="checked"' : '';?> data-title="<?php echo esc_attr( $label ); ?>" value="<?php echo 'views_template_loop_' . $type; ?>" />
                            <label for="<?php echo 'views_template_loop_' . $type; ?>"><?php echo $label; echo $type_used ? $asterisk : ''; ?></label>
                        </li>
                        <?php
					}
					?>
					</ul>
					<?php if ( $show_asterisk_explanation ) { ?>
					<span class="wpv-asterisk-explanation">
						<?php echo $asterisk_explanation; ?>
					</span>
					<?php } ?>
					<?php
				} else {
					_e( 'There are no taxonomy archives to assign Content Templates to', 'wpv-views' );
				}
				$tax_content = ob_get_clean();
				?>
				<div class="js-wpv-content-template-dropdown-list wpv-content-template-dropdown-list<?php echo $open_section ? '' : ' hidden'; ?>">
					<?php echo $tax_content; ?>
				</div>
            </div>
        </div>
        <div class="wpv-dialog-footer">
            <button class="button js-dialog-close"><?php _e('Cancel','wpv-views') ?></button>
            <button class="button button-primary js-ct-change-type-process" data-id="<?php echo $id; ?>"><?php _e('Change','wpv-views') ?></button>
        </div>
        </form>
		</div>
    <?php
     die();
}

//Change CT usage callback function TODO check this nonce

add_action('wp_ajax_ct_change_types_process', 'ct_change_types_process_callback');

function ct_change_types_process_callback(){

    if ( !isset($_POST["wpnonce"]) || ! wp_verify_nonce($_POST["wpnonce"], 'work_view_template') ) die("Undefined Nonce.");

    global $wpdb, $WP_Views;
    $options = $WP_Views->get_options();
        $id = $_POST["view_template_id"];
        if ( isset($_POST['type']) ){
            $type = $_POST['type'];
        }else{
            $type = array();
        }
        foreach ($options as $key => $value) {
             if ($value == $id){
                $options[$key] = 0;
             }
        }
        for ($i=0;$i<count($type);$i++){
                 $options[$type[$i]] = $id;
        }
        $WP_Views->save_options( $options );

        echo 'ok';

    die();
}

// Change CT action - popup structure TODO check nonce, check header texts

add_action('wp_ajax_ct_change_types_pt', 'ct_change_types_pt_callback');

function ct_change_types_pt_callback(){
    if ( !isset($_GET["wpnonce"]) || ! wp_verify_nonce($_GET["wpnonce"], 'work_view_template') ) die("Undefined Nonce.");
    global $wpdb, $WP_Views;
    $query = new WP_Query('post_type=view-template&posts_per_page=-1');
    $sort = $_GET['sort'];
    $post_type = $_GET['pt'];
    $no_type = __('Don’t use any Content Template for this Post Type','wpv-views');
    $head_text = __('Change Post Type','wpv-views');
    if ( isset($_GET['msg']) && $_GET['msg'] == '2'){
        $no_type = __('Don’t use any Content Template for this Taxonomy','wpv-views');
        $head_text = __('Change Taxonomy','wpv-views');
    }
    $options = $WP_Views->get_options();
    ?>
    <div class="wpv-dialog js-wpv-dialog-add-new-content-template wpv-dialog-add-new-content-template">
        <form method="" id="wpv-add-new-content-template-form">
        <div class="wpv-dialog-header">
            <h2><?php echo $head_text ?></h2>
            <i class="icon-remove js-dialog-close"></i>
        </div>
        <div class="wpv-dialog-content">
            <div><?php // echo '<pre>';print_r($query);echo '</pre>'; ?>
                <ul>
                <li><label>
                    <input type="radio" name="wpv-new-post-type-content-template" value="0" />
                     <?php echo $no_type; ?>
                     </label>
                </li>
                <?php
                while ($query->have_posts()) :

                    $query->the_post();
                    $id = get_the_id();
                    $current = '';
                    if ( isset($options[$post_type]) && $id == $options[$post_type] ){
                        $current = ' checked="checked"';
                    }
                   ?>
                     <li>
                            <label>
                                <input type="radio" name="wpv-new-post-type-content-template" <?php echo $current;?> value="<?php echo $id;?>" />
                                <?php the_title();?>
                            </label>
                     </li>
                    <?php

                endwhile; ?>
                    </ul>
           </div>

        </div>
        <div class="wpv-dialog-footer">
            <button class="button js-dialog-close"><?php _e('Cancel','wpv-views') ?></button>
            <button class="button button-primary js-ct-change-types-pt-process" data-pt="<?php echo $post_type?>" data-sort="<?php echo $sort?>"><?php _e('Change','wpv-views') ?></button>
        </div>
        </div>
    <?php
    die();
}

// Change CT action callback function TODO check nonces

add_action('wp_ajax_ct_change_types_pt_process', 'ct_change_types_pt_process_callback');

function ct_change_types_pt_process_callback(){

    if ( !isset($_POST["wpnonce"]) || ! wp_verify_nonce($_POST["wpnonce"], 'work_view_template') ) die("Undefined Nonce.");
    global $wpdb, $WP_Views;
        $options = $WP_Views->get_options();
        $pt = $_POST["pt"];
        $sort = $_POST['sort'];
        if ( isset($_POST['value']) ){
            $value = $_POST['value'];
        }
        else{
            $value = 0;
        }
        $options[$pt] = $value;

        $WP_Views->save_options( $options );
        $out = wpv_admin_menu_content_template_listing_by_type_row( $sort );
        echo $out;

    die();
}

//Assign new Content template to view TODO I thought this was done in a function above...

add_action('wp_ajax_wpv_add_view_template', 'wpv_add_view_template_callback');

function wpv_add_view_template_callback() {
    global $wpdb;
    //add new content template
    if ( !isset($_POST["wpnonce"]) || !wp_verify_nonce($_POST["wpnonce"], 'wpv-ct-inline-edit') ) die("Undefined Nonce.");
    if ( isset($_POST['template_name']) ){
        $postid = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type='view-template'", $_POST['template_name'] ) );
        if ( !empty($postid)  ){
               echo 'error_name'; die();
        }
        $new_template = array(
          'post_title'    => $_POST['template_name'],
          'post_type'      => 'view-template',
          'post_content'  => '',
          'post_status'   => 'publish',
          'post_author'   => 1,// TODO check why author here
       //   'post_name' => sanitize_title( $_POST['template_name'] )
        );
        $ct_post_id = wp_insert_post( $new_template );
        update_post_meta( $ct_post_id, '_wpv_view_template_mode', 'raw_mode');
        update_post_meta( $ct_post_id, '_wpv-content-template-decription', '');
    }
    else{
       $ct_post_id = $_POST['template_id'];
    }

    $post = get_post($ct_post_id);

    if ( !is_object($post) ){
        echo 'error'; die();
    }
    $meta = get_post_meta( $_POST['view_id'], '_wpv_layout_settings', true);
    $templates = $exists = '';
    $reg_templates = array();
    if ( isset( $meta['included_ct_ids'] ) ){
        $reg_templates = explode( ',', $meta['included_ct_ids'] );
		if ( in_array( $ct_post_id, $reg_templates ) ) {
			$exists = 1;
		}
    }
    if ( $exists == '') {
		$reg_templates[] = $ct_post_id;
		$templates = implode( ',', $reg_templates );
        $meta['included_ct_ids'] = $templates;
        update_post_meta($_POST['view_id'], '_wpv_layout_settings', $meta );
        echo wpv_list_view_ct_item($post, $ct_post_id, $_POST['view_id']);
    }
    else {
        echo '1';
    }

    die();
}

// Update CT (inline - inside View editor page) TODO check nonces

add_action('wp_ajax_wpv_ct_update_inline', 'wpv_ct_update_inline_callback');

function wpv_ct_update_inline_callback() {
    //add new content template
    if ( !isset($_POST["wpnonce"]) || !wp_verify_nonce($_POST["wpnonce"], 'wpv-ct-inline-edit') ) die("Undefined Nonce.");
    $my_post = array();
    $my_post['ID'] = $_POST['ct_id'];
    $my_post['post_content'] = $_POST['ct_value'];
	if ( isset( $_POST['ct_title'] ) ) {
		$my_post['post_title'] = $_POST['ct_title'];
	}
    print wp_update_post( $my_post );
    die();
}

// Response when updating all posts to use a given CT - popup structure TODO localize!!!! and check nonce
// TODO seems that this is called in a colorbox callback, but BUT is executes the delete... TODO review this all

add_action('wp_ajax_set_view_template_listing', 'set_view_template_listing_callback');

function set_view_template_listing_callback() {
    if ( !isset($_POST["wpnonce"]) || ! wp_verify_nonce($_POST["wpnonce"], 'work_view_template') ) die("Undefined Nonce.");
    $view_template_id = $_POST['view_template_id'];
    $type = $_POST['type'];
    wpv_update_dissident_posts_from_template( $view_template_id, $type);
    die();
}

// Load CT editor (inline - inside View editor page) TODO check nonce and, god's sake, error handling

add_action('wp_ajax_wpv_ct_loader_inline', 'wpv_ct_loader_inline_callback');

function wpv_ct_loader_inline_callback() {
    //add new content template
    if ( !isset($_POST["wpnonce"]) || !wp_verify_nonce($_POST["wpnonce"], 'wpv-ct-inline-edit') ) die("Undefined Nonce.");
    do_action('views_ct_inline_editor');
    $post = get_post($_POST['id']);
    define("CT_INLINE", "1");
    $out = '';
    if ( is_object($post) && isset($post->ID) ){
        ob_start();
        $ct_id = $post->ID;


    ?>

       	<div class="code-editor-toolbar js-code-editor-toolbar">
	       <ul class="js-wpv-v-icon js-wpv-v-icon-<?php echo $ct_id; ?>">
	            <li class="wpv-vicon-codemirror-button">
					<?php wpv_add_v_icon_to_codemirror( 'wpv-ct-inline-editor-'.$ct_id, true ); ?>
	            </li>
	            <?php wpv_add_cred_to_codemirror( 'wpv-ct-inline-editor-'.$ct_id, 'li' ); ?>
				<li>
					<button class="button-secondary js-code-editor-toolbar-button js-wpv-media-manager" data-id="<?php echo $ct_id; ?>" data-content="<?php echo 'wpv-ct-inline-editor-'.$ct_id; ?>">
						<i class="icon-picture"></i>
						<span class="button-label"><?php _e('Media','wpv-views'); ?></span>
					</button>
				</li>
	       </ul>
      	</div>
		<textarea name="name" rows="10" id="wpv-ct-inline-editor-<?php echo $ct_id; ?>"><?php echo $post->post_content;?></textarea></p>
		<p class="update-button-wrap">
		   <button class="button js-wpv-ct-update-inline js-wpv-ct-update-inline-<?php echo $ct_id; ?>" data-unsaved="<?php echo htmlentities( __('Not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-id="<?php echo $ct_id; ?>"><?php _e('Update','wpv-views'); ?></button>
		</p>

    <?php
    $out = ob_get_contents();
    ob_end_clean();
    print $out;
    }else{
       print 'error';
    }

    die();
}

// Close help box for CT (inline - inside View edit screen) TODO check nonce

add_action('wp_ajax_close_ct_help_box', 'close_ct_help_box_callback');

function close_ct_help_box_callback() {
    //add new content template
    if ( !isset($_POST["wpnonce"]) || !wp_verify_nonce($_POST["wpnonce"], 'set_view_template') ) die("Undefined Nonce.");
    $close = 1;
    if ( isset($_POST['close_this']) ){
        $close = $_POST['close_this'];
    }
    update_option('wpv_content_template_show_help',$close);
    die();
}

