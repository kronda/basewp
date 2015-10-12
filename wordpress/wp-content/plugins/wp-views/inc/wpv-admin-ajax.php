<?php
/**
 * General file for all AJAX calls
 *
 * All AJAX calls used in the backend must be set here.
 */



/* ************************************************************************* *\
        Views & WPA edit sceen
\* ************************************************************************* */

/*
 * Screen options save callback function.
 *
 * @todo There may be some deprecated options, e.g. the option for layout-extra in sections-show-hide. These should be
 *     deleted in a future upgrade procedure. See following links for more information:
 *     - https://icanlocalize.basecamphq.com/projects/7393061-toolset/todo_items/193583572/comments#comment_303063628
 *     - https://icanlocalize.basecamphq.com/projects/7393061-toolset/todo_items/193583488/comments
 *
 * @since unknown
 */


/**
 * wpv_save_screen_options_callback
 *
 * Save Views and WPA screen options.
 *
 * @since unknown
 */
 
add_action('wp_ajax_wpv_save_screen_options', 'wpv_save_screen_options_callback');

function wpv_save_screen_options_callback() {
	wpv_ajax_authenticate( 'wpv_view_show_hide_nonce', array( 'parameter_source' => 'post', 'type_of_death' => 'data' ) );
	
	if (
		! isset( $_POST["id"] )
		|| ! is_numeric( $_POST["id"] )
		|| intval( $_POST['id'] ) < 1 
	) {
		$data = array(
			'type' => 'id',
			'message' => __( 'Wrong or missing ID.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	$view_array = get_post_meta( $_POST["id"], '_wpv_settings', true );
	if ( isset( $_POST['settings'] ) ) {
		parse_str( $_POST['settings'], $settings );
		foreach ( $settings as $section => $state ) {
			$section = sanitize_text_field( $section );
			$state = sanitize_text_field( $state );
			$view_array['sections-show-hide'][$section] = $state;
		}
	}
	if ( isset( $_POST['helpboxes'] ) ) {
		parse_str( $_POST['helpboxes'], $help_settings );
		foreach ( $help_settings as $section => $state ) {
			$section = sanitize_text_field( $section );
			$state = sanitize_text_field( $state );
			$view_array['metasections-hep-show-hide'][$section] = $state;
		}
	}
	if ( isset( $_POST['purpose'] ) ) {
		$view_array['view_purpose'] = sanitize_text_field( $_POST['purpose'] );
	}
	update_post_meta( $_POST["id"], '_wpv_settings', $view_array );
	do_action( 'wpv_action_wpv_save_item', $_POST["id"] );
	$data = array(
		'id' => $_POST["id"],
		'message' => __( 'Screen options saved', 'wpv-views' )
	);
	wp_send_json_success( $data );
}


add_action( 'wp_ajax_wpv_update_title_description', 'wpv_update_title_description_callback' );

/**
 * Save Views and WPA title and description section.
 *
 * Expects following $_POST variables:
 * - wpnonce
 * - id
 * - title
 * - slug
 * - description
 * - is_title_escaped
 *
 * @since unknown
 */
function wpv_update_title_description_callback() {

    wpv_ajax_authenticate( 'wpv_view_title_description_nonce', array( 'type_of_death' => 'data' ) );

    $view_id = intval( wpv_getpost( 'id', 0 ) );

    // This is full Views, so we will allways get WPV_View, WPV_WordPress_Archive or null.
    $view = WPV_View_Base::get_instance( $view_id );

    // Fail if the View/WPA doesn't exist.
    if ( null == $view ) {
		wp_send_json_error( array(
            'type' => 'id',
            'message' => __( 'Wrong or missing ID.', 'wpv-views' )
        ) );
	}

    // Try to update all three properties at once.
    $transaction_result = $view->update_transaction( array(
        'title' => wpv_getpost( 'title' ),
        'slug' => wpv_getpost( 'slug' ),
        'description' => wpv_getpost( 'description' )
    ) );

    // On failure, return the first available error message (there should be only one anyway).
    if( !$transaction_result['success'] ) {
        $error_message = wpv_getarr( $transaction_result, 'first_error_message', __( 'An unexpected error happened.', 'wpv-views' ) );
        wp_send_json_error( array( 'type' => 'update', 'message' => $error_message ) );
    }

    // Success.

    // Use special success message if title was changed by escaping in JS.
    $is_title_escaped = intval( wpv_getpost( 'is_title_escaped', 0 ) );
    if( $is_title_escaped ) {
        $success_message = __( 'We escaped the title before saving.', 'wpv-views' );
    } else {
        $success_message = __( 'Title and Description saved', 'wpv-views' );
    }

	wp_send_json_success( array( 'id' => $view_id, 'message' => $success_message ) );
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
	wpv_ajax_authenticate( 'wp_nonce_create_view', array( 'parameter_source' => 'post', 'type_of_death' => 'data' ) );
	
	if ( 
		! isset( $_POST["title"] ) 
		|| $_POST["title"] == '' 
	) {
		$_POST["title"] = __('Unnamed View', 'wp-views');
	}
    if ( 
		! isset( $_POST["kind"] ) 
		|| $_POST["kind"] == '' 
	) {
		$_POST["kind"] = 'normal';
	}
    if (
		! isset( $_POST["purpose"] ) 
		|| $_POST["purpose"] == '' 
	) {
		$_POST["purpose"] = 'full';
	}

    $args = array(
		'title' => $_POST["title"],
		'settings' => array(
			'view-query-mode' => $_POST["kind"],
			'view_purpose' => $_POST["purpose"]
		)
    );

    $response = wpv_create_view( $args );
    $result = array();

    if ( isset( $response['success'] ) ) {
		$data = array(
			'new_view_id' => $response['success']
		);
		wp_send_json_success( $data );
    } else if ( isset( $response['error'] ) ) {
		$data = array(
			'message' => $response['error']
		);
		wp_send_json_error( $data );
    } else {
		$data = array(
			'message' => __('The View could not be created', 'wpv-views')
		);
		wp_send_json_error( $data );
    }
}

/**
 * View Scan usage callback action.
 *
 * Expects following POST arguments:
 * - wpnonce: A valid work_views_listing nonce.
 * - id: ID of the View.
 *
 * Prints JSON-encoded array of items on success. Each item has a 'post_title' and 'link'.
 * Otherwise prints an error message (not a valid JSON).
 *
 * @todo change the nonce
 *
 * @since unknown
 */
add_action( 'wp_ajax_wpv_scan_view_usage', 'wpv_scan_view_usage_callback' );

function wpv_scan_view_usage_callback() {
	wpv_ajax_authenticate( 'work_views_listing', array( 'parameter_source' => 'post', 'type_of_death' => 'data' ) );

    $post_id = wpv_getpost( 'id', 0 );
	if ( 0 == $post_id 	) {
		$data = array(
			'message' => __( 'Wrong data', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}

	global $wpdb, $sitepress;
	
	$values_to_prepare = array();
	$trans_join = '';
    $trans_where = '';
    if ( 
		isset( $sitepress ) 
		&& function_exists( 'icl_object_id' )
	) {
		$current_lang_code = $sitepress->get_current_language();
		$trans_join = " JOIN {$wpdb->prefix}icl_translations t ";
		$trans_where = " AND ID = t.element_id AND t.language_code = %s ";
		$values_to_prepare[] = $current_lang_code;
    }
	
    $view = get_post( $post_id );
	$needle = '[wpv-view name="' . $view->post_title . '"';
	$needle = '%' . wpv_esc_like( $needle ) . '%';
	$needle_name = '[wpv-view name="' . $view->post_name . '"';
	$needle_name = '%' . wpv_esc_like( $needle_name ) . '%';
	
	$values_to_prepare[] = $needle;
	$values_to_prepare[] = $needle_name;
	$values_to_prepare[] = $needle;
	$values_to_prepare[] = $needle_name;

    $q = "SELECT DISTINCT * FROM {$wpdb->posts} {$trans_join} 
		WHERE post_status = 'publish' 
		{$trans_where}
		AND post_type NOT IN ('revision')
		AND (
			ID IN ( 
				SELECT DISTINCT ID FROM {$wpdb->posts}
				WHERE ( post_content LIKE %s OR post_content LIKE %s ) 
				AND post_type NOT IN ('revision')
				AND post_status = 'publish' 
			)
			OR ID IN (
				SELECT DISTINCT post_id FROM {$wpdb->postmeta}
				WHERE ( meta_value LIKE %s OR meta_value LIKE %s ) 
			)
		)";

    $res = $wpdb->get_results( 
		$wpdb->prepare(
			$q,
			$values_to_prepare
		),
		OBJECT 
	);
    
	$items = array();
	if ( ! empty( $res ) ) {
        foreach ( $res as $row ) {
            $type = get_post_type_object( $row->post_type );
            $type = $type->labels->singular_name;

            if ( $row->post_type == 'view' ) {
                $edit_link = get_admin_url() . "admin.php?page=views-editor&view_id=" . $row->ID;
            } else if( WPV_Content_Template_Embedded::POST_TYPE == $row->post_type ) {
                $edit_link = wpv_ct_editor_url( $row->ID );
            } else {
                $edit_link = get_admin_url() . "post.php?post=" . $row->ID . "&action=edit";
			}
			
			$items[] = array(
				'id'	=> $row->ID,
				'link'	=> $edit_link,
				'title'	=> "<strong>" . $type . "</strong>: " . $row->post_title
			);
        }
    }
	$data = array(
		'used_on' => $items
	);
	wp_send_json_success( $data );
}



add_action( 'wp_ajax_wpv_duplicate_this_view', 'wpv_duplicate_this_view_callback' );

/**
 * View duplicate callback function.
 *
 * Expects following POST arguments:
 * - wpnonce: A valid wpv_duplicate_view_nonce.
 * - id: View ID.
 * - name: Name of the new View.
 *
 * Refer to WPV_View::duplicate() for more information about the duplication itself.
 *
 * @since unknown
 */
function wpv_duplicate_this_view_callback() {
	wpv_ajax_authenticate( 'wpv_duplicate_view_nonce', array( 'parameter_source' => 'post', 'type_of_death' => 'data' ) );
	
    $post_id = (int) wpv_getpost( 'id', 0 );
    $post_name= sanitize_text_field( wpv_getpost( 'name', '' ) );
	if ( ( 0 == $post_id ) || empty( $post_name ) ) {
		$data = array(
			'message' => __('Wrong data', 'wpv-views')
		);
		wp_send_json_error( $data );
	}

    if ( WPV_View_Base::is_name_used( $post_name ) ) {
        $data = array(
			'message' => __( 'A View with that name already exists. Please use another name', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}

    // Get the original View.
    $original_view = WPV_View::get_instance( $post_id );
    if( null == $original_view ) {
		$data = array(
			'message' => __('Wrong data', 'wpv-views')
		);
		wp_send_json_error( $data );
    }
    
    $duplicate_view_id = $original_view->duplicate( $post_name );
    if ( $duplicate_view_id ) {
        // original post id (shouldn't we rather return new id?)
        wp_send_json_success();
    } else {
        $data = array(
			'message' => __( 'Unexpected error', 'wpv-views' )
		);
		wp_send_json_error( $data );
    }

}

/**
 * Render a popup to confirm bulk Views trashing or deleting.
 *
 * This is called by deleteViewsConfirmation() in views_listing_page.js. The Popup is identified by class
 * "js-bulk-delete-views-dialog". It also contains a table with Views to be deleted and buttons to scan for their usage.
 *
 * Delete button (js-bulk-remove-view-permanent) contains data attributes "view-ids" with comma-separated list of
 * View IDs and "nonce" with a wpv_bulk_remove_view_permanent_nonce.
 *
 * Following POST variables are expected:
 * - wpnonce: A valid wpv_view_listing_actions_nonce.
 * - ids: An array of View IDs to be trashed/deleted
 * - view_action: Action to perform: 'delete' or 'trash'.
 *
 * @since 1.7
 */ 
add_action( 'wp_ajax_wpv_view_bulk_trashdel_render_popup', 'wpv_view_bulk_trashdel_render_popup_callback' );

function wpv_view_bulk_trashdel_render_popup_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		$data = array(
			'type' => 'capability',
			'message' => __( 'You do not have permissions for that.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	if ( 
		! isset( $_POST["wpnonce"] )
		|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_listing_actions_nonce' ) 
	) {
		$data = array(
			'type' => 'nonce',
			'message' => __( 'Your security credentials have expired. Please reload the page to get new ones.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}

	$post_ids = wpv_getpost( 'ids', array() );
	if ( is_string( $_POST['ids'] ) ) {
		$post_ids = array( $_POST['ids'] );
	}
	// We only get IDs and titles
	global $wpdb;
	$post_ids = array_map( 'esc_attr', $post_ids );
	$post_ids = array_map( 'trim', $post_ids );
	// is_numeric does sanitization
	$post_ids = array_filter( $post_ids, 'is_numeric' );
	$post_ids = array_map( 'intval', $post_ids );
	if( ! empty( $post_ids ) ) {
		$post_id_list = implode( ',', $post_ids );
		$views = $wpdb->get_results(
			"SELECT ID as id, post_title 
			FROM {$wpdb->posts} 
			WHERE post_type = 'view' 
			AND id IN ( $post_id_list )" 
		);
	} else {
		$views = array(); // This should never happen.
	}
	$view_count = count( $views );
	// Different values based on the action we're confirming (they're all here).
	$view_action = wpv_getpost( 'view_action', 'delete', array( 'delete', 'trash' ) );
	$action_word = ( 'delete' == $view_action ) ? __( 'delete', 'wpv-views' ) : __( 'trash', 'wpv-views' );
	ob_start();
	?>
		<div class="wpv-dialog wpv-shortcode-gui-content-wrapper">
			<h3>
				<?php
					printf(
							_n(
								'Are you sure you want to %s this View?',
								'Are you sure you want %s these Views?',
								$view_count,
								'wpv-views' ),
							$action_word );
				?>
			</h3>
			<p>
				<?php
					echo _n(
							'Please use the Scan button first to be sure that it is not used anywhere.',
							'Please use Scan buttons first to be sure that they are not used anywhere.',
							$view_count,
							'wpv-views' );
				?>
			</p>
			<table class="wpv-view-table" style="width: 100%;">
				<?php
					foreach( $views as $view ) {
						?>
						<tr>
							<td><strong><?php echo esc_html( $view->post_title ); ?></strong></td>
							<td class="wpv-admin-listing-col-scan">
								<button class="button js-scan-button" data-view-id="<?php echo esc_attr( $view->id ); ?>">
									<?php _e( 'Scan', 'wp-views' ); ?>
								</button>
								<span class="js-nothing-message hidden"><?php _e( 'Nothing found', 'wpv-views' ); ?></span>
							</td>
						</tr>
						<?php
					}
				?>
			</table>
		</div>
	<?php
	$result = ob_get_clean();
	$data = array(
		'dialog_content' => $result
	);
	wp_send_json_success( $data );
}

/* ************************************************************************** *\
		WP Archive listing screen
\* ************************************************************************** */


// Add up, down or first WP Archive - popup structure

add_action('wp_ajax_wpv_create_wp_archive_popup', 'wpv_create_wp_archive_popup_callback');

function wpv_create_wp_archive_popup_callback() {
	wpv_ajax_authenticate( 'work_views_listing', array( 'parameter_source' => 'get', 'type_of_death' => 'data' ) );
	global $WPV_view_archive_loop;
	ob_start();
	$WPV_view_archive_loop->_create_view_archive_popup();
	$response = ob_get_clean();
	$data = array(
		'dialog_content' => $response
	);
	wp_send_json_success( $data );
}

// Change usage for WP Archive in name arrange - popup structure

add_action('wp_ajax_wpv_change_wp_archive_usage_popup', 'wpv_change_wp_archive_usage_popup_callback');

function wpv_change_wp_archive_usage_popup_callback() {
	wpv_ajax_authenticate( 'work_views_listing', array( 'parameter_source' => 'get', 'type_of_death' => 'data' ) );
	if (
		! isset( $_GET["id"] )
		|| ! is_numeric( $_GET["id"] )
		|| intval( $_GET['id'] ) < 1 
	) {
		$data = array(
			'message' => __( 'Untrusted data', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	global $WPV_view_archive_loop;
	$id = intval( $_GET["id"] );
	ob_start();
	$WPV_view_archive_loop->_create_view_archive_popup( $id );
	$response = ob_get_clean();
	$data = array(
		'dialog_content' => $response
	);
	wp_send_json_success( $data );
}

// Change usage for Archive in name arrange callback function

add_action('wp_ajax_wpv_wp_archive_change_usage', 'wpv_wp_archive_change_usage_callback');

function wpv_wp_archive_change_usage_callback() {
	wpv_ajax_authenticate( 'work_views_listing', array( 'parameter_source' => 'post', 'type_of_death' => 'data' ) );
	
	if ( ! isset( $_POST["form"] ) ) {
		$data = array(
			'message' => __( 'Untrusted data', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
    global $WPV_view_archive_loop;
    parse_str( $_POST['form'], $form_data );
	$archive_id = $form_data["wpv-archive-view-id"];
	$WPV_view_archive_loop->update_view_archive_settings( $archive_id, $form_data );
	do_action( 'wpv_action_wpv_save_item', $archive_id );
	wp_send_json_success();
}

// Add up, down or first WP Archive callback function
// Uses the same callback as in the usage arrange mode

add_action('wp_ajax_wpv_wp_archive_create_new', 'wp_ajax_wpv_wp_archive_create_new_callback');

function wp_ajax_wpv_wp_archive_create_new_callback() {
	wpv_ajax_authenticate( 'work_views_listing', array( 'parameter_source' => 'post', 'type_of_death' => 'data' ) );
	if ( ! isset( $_POST["form"] ) ) {
		$data = array(
			'message' => __( 'Untrusted data', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	global $wpdb, $WPV_view_archive_loop;
	parse_str( $_POST['form'], $form_data );
	// Create archive
	$existing = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} 
			WHERE ( post_title = %s OR post_name = %s ) 
			AND post_type = 'view' 
			LIMIT 1",
			$form_data["wpv-new-archive-name"],
			$form_data["wpv-new-archive-name"]
		)
	);
	if ( $existing ) {
		$data = array(
			'message' => __( 'Untrusted data', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	$new_archive = array(
		'post_title'    => $form_data["wpv-new-archive-name"],
		'post_type'      => 'view',
		'post_content'  => "[wpv-layout-meta-html]",
		'post_status'   => 'publish',
		'post_author'   => get_current_user_id(),
		'comment_status' => 'closed'
	);
	$post_id = wp_insert_post( $new_archive );

	$archive_defaults = wpv_wordpress_archives_defaults( 'view_settings' );
	$archive_layout_defaults = wpv_wordpress_archives_defaults( 'view_layout_settings' );
	update_post_meta( $post_id, '_wpv_settings', $archive_defaults );
	update_post_meta( $post_id, '_wpv_layout_settings', $archive_layout_defaults );
	$WPV_view_archive_loop->update_view_archive_settings( $post_id, $form_data );
	$data = array(
		'id' => $post_id
	);
	wp_send_json_success( $data );
}

// Create WP Archive in usage arrange callback function
// @todo we need to use the API to create this, or at least *create* that API if needed

add_action( 'wp_ajax_wpv_create_wpa_for_archive_loop', 'wp_ajax_wpv_create_wpa_for_archive_loop_callback' );

function wp_ajax_wpv_create_wpa_for_archive_loop_callback() {
	wpv_ajax_authenticate( 'work_views_listing', array( 'parameter_source' => 'post', 'type_of_death' => 'data' ) );
	
	global $wpdb, $WPV_view_archive_loop;
	$existing = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} 
			WHERE ( post_title = %s OR post_name = %s ) 
			AND post_type = 'view' 
			LIMIT 1",
			$_POST["title"],
			$_POST["title"]
		)
	);
	if ( $existing ) {
		$data = array(
			'message'	=> __( 'A WordPress Archive with that name already exists. Please use another name.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	$new_archive = array(
		'post_title'    => sanitize_text_field( $_POST["title"] ),
		'post_type'     => 'view',
		'post_content'  => "[wpv-layout-meta-html]",
		'post_status'   => 'publish',
		'post_author'   => get_current_user_id(),
		'comment_status' => 'closed'
	);
	$post_id = wp_insert_post( $new_archive );
	$archive_defaults = wpv_wordpress_archives_defaults( 'view_settings' );
	$archive_layout_defaults = wpv_wordpress_archives_defaults( 'view_layout_settings' );
	update_post_meta( $post_id, '_wpv_settings', $archive_defaults );
	update_post_meta( $post_id, '_wpv_layout_settings', $archive_layout_defaults );
	
	$form_data = array(
		sanitize_text_field( $_POST['loop'] ) => $post_id
	);
	$WPV_view_archive_loop->update_view_archive_settings( $post_id, $form_data );
	$data = array(
		'id' => $post_id
	);
	wp_send_json_success( $data );
}


/**
 * Render a popup to change WordPress Archive usage.
 *
 * Used in WordPress Archive listing, arranged by usage.
 *
 * Expects following GET arguments:
 * - wpnonce: A valid wpv_wp_archive_arrange_usage nonce.
 * - id: Slug of the loop whose WPA should be changed.
 *
 * @since unknown.
 */ 
add_action('wp_ajax_wpv_change_wpa_for_archive_loop_popup', 'wpv_change_wpa_for_archive_loop_popup_callback');

function wpv_change_wpa_for_archive_loop_popup_callback() {
	wpv_ajax_authenticate( 'wpv_wp_archive_arrange_usage', array( 'parameter_source' => 'get', 'type_of_death' => 'data' ) );
	
	if ( ! isset( $_GET["id"] ) ) {
		$data = array(
			'message'	=> __( 'Untrusted data', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	global $WPV_view_archive_loop, $WPV_settings;
	// Slug of the loop.
	$loop_key = sanitize_text_field( $_GET["id"] );
    $loops = $WPV_view_archive_loop->_get_post_type_loops();
	/* We will slightly misuse this function, but it gives us exactly what we need:
	 * a list of published WPAs. */
	$views_pre_query_data = wpv_prepare_view_listing_query(
		array( 'archive', 'layouts-loop' ),
		'publish',
		array( 'posts.post_title' => 'post_title' ), // also give us post title
		true, // return rows from the table
		array( "posts.post_status = 'publish'" ) 
	); // limit mysql query only to published posts
	$views = $views_pre_query_data['rows'];
	// ID of currently assigned view or 0.
	$currently_assigned_view_id = isset( $WPV_settings[ $loop_key ] ) ? $WPV_settings[ $loop_key ] : 0; 
	ob_start();
	?>
	<div class="wpv-dialog wpv-shortcode-gui-content-wrapper js-wpv-dialog-change">
		<h3><?php _e( 'Select a WordPress Archive', 'wpv-views' ); ?></h3>
		<?php wp_nonce_field( 'wpv_view_edit_nonce', 'wpv_view_edit_nonce' ); ?>
		<input type="hidden" value="<?php echo esc_attr( $loop_key ); ?>" id="js-wpv-change-wpa-for-archive-loop-key" name="wpv-archive-loop-key" />
		<ul id="js-wpv-change-wpa-for-archive-loop-list" class="wpv-mightlong-list">
			<li>
				<label>
					<input type="radio" name="wpv-view-loop-archive" value="0" <?php checked( 0 == $currently_assigned_view_id ); ?> />
					<?php _e( 'Don\'t use a WordPress Archive for this loop', 'wpv-views' ); ?>
				</label>
			</li>
			<?php
				foreach ( $views as $view ) {
					?>
					<li>
						<label>
							<input type="radio" <?php checked( $view->id == $currently_assigned_view_id ); ?>
									name="wpv-view-loop-archive" value="<?php echo esc_attr( $view->id ); ?>" />
							<?php echo $view->post_title; ?>
						</label>
					</li>
					<?php
				}
			?>
		</ul>
	</div>
	<?php
	$response = ob_get_clean();
	$data = array(
		'dialog_content' => $response
	);
	wp_send_json_success( $data );
}

// Change WP Archive usage in usage arrange callback function

add_action('wp_ajax_wpv_change_wpa_for_archive_loop', 'wpv_change_wpa_for_archive_loop_callback');

function wpv_change_wpa_for_archive_loop_callback() {
	wpv_ajax_authenticate( 'wpv_wp_archive_arrange_usage', array( 'parameter_source' => 'post', 'type_of_death' => 'data' ) );
	
	global $WPV_settings;
	$loop = sanitize_text_field( $_POST["loop"] );
	$selected = sanitize_text_field( $_POST["selected"] );
	$WPV_settings[$loop] = $selected;
	do_action( 'wpv_action_wpv_save_item', $selected );
	foreach ( $WPV_settings as $key => $value ) {
        if ( $value == 0 ) {
            unset( $WPV_settings[$key] );
        }
    }
	$WPV_settings->save();
	wp_send_json_success();
}

// Delete one WPA permanently callback function
// @note it also deletes the loop Template if needed

add_action('wp_ajax_wpv_delete_wpa_permanent', 'wpv_delete_wpa_permanent_callback');

function wpv_delete_wpa_permanent_callback() {
	wpv_ajax_authenticate( 'wpv_remove_view_permanent_nonce', array( 'parameter_source' => 'post', 'type_of_death' => 'data' ) );

	if (
		! isset( $_POST["id"] )
		|| ! is_numeric( $_POST["id"] )
		|| intval( $_POST['id'] ) < 1 
	) {
		$data = array(
			'message' => __( 'Wrong data.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	$loop_content_template = get_post_meta( $_POST["id"], '_view_loop_template', true );
	wp_delete_post( $_POST["id"] );
	if ( ! empty( $loop_content_template ) ) {
		wp_delete_post( $loop_content_template, true );
	}
    // Clean options - when deleting WPA
    global $WPV_settings;	
	$WPV_settings->refresh_view_settings_data();
    $WPV_settings->save();
	wp_send_json_success();
}

// Change status of View and WPA callback function TODO use a more generic function name

add_action('wp_ajax_wpv_view_change_status', 'wpv_view_change_status_callback');

function wpv_view_change_status_callback(){
	if ( ! current_user_can( 'manage_options' ) ) {
		die( "Untrusted user" );
	}
	if ( ! (
		wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_listing_actions_nonce' )  // from the Views listing screen OR
		|| wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_change_status' ) // from the View edit screen
	) ) {
		die( "Security check" );
	}
	if (
		! isset( $_POST["id"] )
		|| ! is_numeric( $_POST["id"] )
		|| intval( $_POST['id'] ) < 1 
	) {
		die( "Untrusted data" );
	}
	if ( ! isset( $_POST['newstatus'] ) ) {
		$_POST['newstatus'] = 'publish';
	}
	$my_post = array(
		'ID'           => $_POST["id"],
		'post_status' => $_POST['newstatus']
	);
	$return = wp_update_post( $my_post );
	if ( isset( $_POST['cleararchives'] ) ) {
		global $WPV_settings;
		if ( ! $WPV_settings->is_empty() ) {
			foreach ( $WPV_settings as $option_name => $option_value ) {
				if ( strpos( $option_name, 'view_' ) === 0  && $option_value == $_POST["id"] ) {
					$WPV_settings[$option_name] = 0;
				}
			}
			$WPV_settings->save();
		}
	}
	do_action( 'wpv_action_wpv_save_item', $_POST["id"] );
	echo $return;
	die();
}


/**
 * Change status of multiple Views, WordPress Archives or Content Templates. Callback function.
 *
 * Following POST parameters are expected:
 * 
 * - wpnonce: A valid wpv_view_listing_actions_nonce.
 * - newstatus: New status for posts that should be updated. Default is 'publish'.
 * - ids: An array of post IDs that should be updated. Single (string or numeric) value is also accepted.
 * - cleararchives: If set to 1, assignment of givent posts (WPAs) in archive loops will be cleared.
 * 
 * Outputs '0' on failure (when one or more posts couldn't be updated) and '1' on success.
 *
 * @since 1.7
 */ 
add_action( 'wp_ajax_wpv_view_bulk_change_status', 'wpv_view_bulk_change_status_callback' );

function wpv_view_bulk_change_status_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		die( "Untrusted user" );
	}
	if ( ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_listing_actions_nonce' ) ) {
		die( "Security check" );
	}
	$new_status = isset( $_POST['newstatus'] ) ? $_POST['newstatus'] : 'publish';
	if ( ! isset( $_POST['ids'] ) ) {
		$post_ids = array();
	} else if ( is_string( $_POST['ids'] ) ) {
		$post_ids = array( $_POST['ids'] );
	} else {
		$post_ids = $_POST['ids'];
	}
	// Update post statuses
	$is_failure = false;
	foreach ( $post_ids as $post_id ) {
		$my_post = array(
				'ID' => $post_id,
				'post_status' => $new_status );
		$res = wp_update_post( $my_post );
		$is_failure = $is_failure || ( $res == 0 );
		do_action( 'wpv_action_wpv_save_item', $post_id );
	}
	// Clear archive loop assignment, if requested
	if ( isset( $_POST['cleararchives'] ) && ( 1 == $_POST['cleararchives'] ) ) {
		global $WPV_settings;
		if ( ! $WPV_settings->is_empty() ) {
			foreach ( $WPV_settings as $option_name => $option_value ) {
				if ( ( strpos( $option_name, 'view_' ) === 0 ) && in_array( $option_value, $post_ids ) ) {
					$WPV_settings[ $option_name ] = 0;
				}
			}
			$WPV_settings->save();
		}
	}
	echo $is_failure ? 0 : 1;
	die();
}


/**
 * Permanently delete multiple Views or WordPress Archives. Callback function.
 *
 * Needs a wpv_bulk_remove_view_permanent_nonce to be present.
 * Also deletes associated loop Templates if any
 * 
 * Outputs 0 on failure (when one or more posts couldn't be deleted) and 1 on success.
 *
 * $since 1.7
 */ 
add_action( 'wp_ajax_wpv_bulk_delete_views_permanent', 'wpv_bulk_delete_views_permanent_callback' );

function wpv_bulk_delete_views_permanent_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		die( "Untrusted user" );
	}
	if ( ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_bulk_remove_view_permanent_nonce' ) ) {
		die( "Security check" );
	}
	global $wpdb;
	if ( ! isset( $_POST['ids'] ) ) {
		$post_ids = array();
	} else if ( is_string( $_POST['ids'] ) ) {
		$post_ids = array( $_POST['ids'] );
	} else {
		$post_ids = $_POST['ids'];
	}
	$post_ids = array_map( 'esc_attr', $post_ids );
	$post_ids = array_map( 'trim', $post_ids );
	// is_numeric does sanitization
	$post_ids = array_filter( $post_ids, 'is_numeric' );
	$post_ids = array_map( 'intval', $post_ids );
	$is_failure = false;
	// Delete loop Templates if any
	if ( count( $post_ids ) > 0 ) {
		$remove_loop_templates = " AND post_id IN (" . implode( "," , $post_ids ) . ") ";
		$remove_loop_templates_ids = $wpdb->get_col( 
			"SELECT DISTINCT meta_value FROM {$wpdb->postmeta} 
			WHERE meta_key = '_view_loop_template' 
			AND meta_value != '0' 
			{$remove_loop_templates}" 
		);
		foreach ( $remove_loop_templates_ids as $remove_template ) {
			wp_delete_post( $remove_template, true );
		}
	}
	foreach ( $post_ids as $post_id ) {
		$res = wp_delete_post( $post_id );
		$is_failure = $is_failure || ( $res == false );
	}
	// Clean options - when deleting WPA
	global $WPV_settings;
	$WPV_settings->refresh_view_settings_data();
    $WPV_settings->save();
	echo $is_failure ? 0 : 1;
	die();
}


/**
 * Find out which WordPress Archives are used in some loops.
 *
 * For given WPA IDs output those who are used in archive loops. If there are any, also
 * generate HTML for the colorbox popup - confirmation to trash them.
 *
 * Following GET parameters are expected:
 * - wpnonce: Valid wpv_view_listing_actions_nonce.
 * - ids: An array of WPA IDs that should be checked.
 *
 * Output is a JSON representation of an array with following elements:
 * - used_wpa_ids: An array of IDs of WPAs in use.
 * - colorbox_html: If used_wpa_ids is non-empty, this contains HTML for the colorbox popup.
 *     When user confirms it, *all* of the WPAs will be trashed (not only those from used_wpa_ids).
 *     Otherwise it is an empty string.
 *
 * @since 1.7
 */ 
add_action( 'wp_ajax_wpv_archive_check_usage', 'wpv_archive_check_usage_callback' );

function wpv_archive_check_usage_callback() {
	wpv_ajax_authenticate( 'wpv_view_listing_actions_nonce', array( 'parameter_source' => 'get', 'type_of_death' => 'data' ) );
	
	if( ! isset( $_GET['ids'] ) ) {
		$post_ids = array();
	} else if( is_string( $_GET['ids'] ) ) {
		$post_ids = array( $_GET['ids'] );
	} else {
		$post_ids = $_GET['ids'];
	}
	$post_ids = array_map( 'esc_attr', $post_ids );
	$post_ids = array_map( 'trim', $post_ids );
	// is_numeric does sanitization
	$post_ids = array_filter( $post_ids, 'is_numeric' );
	$post_ids = array_map( 'intval', $post_ids );
	$nonce = wp_create_nonce( 'wpv_view_listing_actions_nonce' );
	global $WPV_settings, $WPV_view_archive_loop;
	$loops = $WPV_view_archive_loop->_get_post_type_loops();
	$taxonomies = get_taxonomies( '', 'objects' );
	$exclude_tax_slugs = array();
	$exclude_tax_slugs = apply_filters( 'wpv_admin_exclude_tax_slugs', $exclude_tax_slugs );
	// This will hold IDs of used archives.
	$archive_ids_in_use = array();
	// Check for usage in loops
	foreach ( $loops as $loop => $loop_name ) {
		if ( isset( $WPV_settings[ 'view_' . $loop ] )
			&& in_array( $WPV_settings[ 'view_' . $loop ], $post_ids ) )
		{
			$used_archive_id = $WPV_settings[ 'view_' . $loop ];
			// Use post id for both key and value to ensure it will be present only once as value.
			$archive_ids_in_use[ $used_archive_id ] = $used_archive_id;
		}
	}
	// Check for usage in taxonomies
	foreach ( $taxonomies as $category_slug => $category ) {
		if ( in_array( $category_slug, $exclude_tax_slugs ) ) {
			continue;
		}
		// Only show taxonomies with show_ui set to TRUE
		if ( !$category->show_ui ) {
			continue;
		}
		$name = $category->name;
		if ( isset ( $WPV_settings[ 'view_taxonomy_loop_' . $name ] )
			&& in_array( $WPV_settings[ 'view_taxonomy_loop_' . $name ], $post_ids ) )
		{
			$used_archive_id = $WPV_settings[ 'view_taxonomy_loop_' . $name ];
			$archive_ids_in_use[ $used_archive_id ] = $used_archive_id;
		}
	}
	$archive_ids_in_use = array_map( 'esc_attr', $archive_ids_in_use );
	$archive_ids_in_use = array_map( 'trim', $archive_ids_in_use );
	// is_numeric does sanitization
	$archive_ids_in_use = array_filter( $archive_ids_in_use, 'is_numeric' );
	$archive_ids_in_use = array_map( 'intval', $archive_ids_in_use );
	// If there are some used archives, generate html for the colorbox confirmation popup
	if ( ! empty( $archive_ids_in_use ) ) {
		// We only get IDs and titles
		global $wpdb;
		$used_archive_id_list = implode( ',', $archive_ids_in_use );
		$used_archives = $wpdb->get_results(
			"SELECT ID as id, post_title FROM {$wpdb->posts} 
			WHERE post_type = 'view' 
			AND id IN ( $used_archive_id_list )"
		);
		$used_archive_count = count( $archive_ids_in_use );
		ob_start();
		?>
		<div class="wpv-dialog wpv-shortcode-gui-content-wrapper js-bulk-trash-archives-dialog">
			<h3>
				<?php
					echo _n(
							'Are you sure you want to trash this WordPress Archive?',
							'Are you sure you want to trash these WordPress Archives?',
							$used_archive_count,
							'wpv-views' );
				?>
			</h3>
			<p>
				<?php
					echo _n(
							'It is assigned to one or more archive or taxonomy loops.',
							'Some of them are assigned to archive or taxonomy loops.',
							$used_archive_count,
							'wpv-views' )
						. WPV_MESSAGE_SPACE_CHAR
						. _n(
							'Trashing it will also unassign it.',
							'Trashing them will also unassign them.',
							$used_archive_count,
							'wpv-views' );
				?>
			</p>
			<ul style="list-style-type: disc; padding-left: 40px;">
				<?php
					foreach( $used_archives as $archive ) {
						?>
							<li><strong><?php echo $archive->post_title; ?></strong></li>
						<?php
					}
				?>
			</ul>
		</div> <!-- .js-bulk-trash-archives-dialog -->
		<?php
		$dialog_content = ob_get_contents();
		ob_end_clean();
	} else {
		$dialog_content = '';
	}
	$data = array(
		'dialog_content'	=> $dialog_content,
		'used_wpa_ids'		=> $archive_ids_in_use
	);
	wp_send_json_success( $data );
}


/* ************************************************************************* *\
        Content Templates
\* ************************************************************************* */


add_action( 'wp_ajax_wpv_apply_ct_to_cpt_posts_popup', 'wpv_apply_ct_to_cpt_posts_popup_callback');


/**
 * Callback function for the AJAX action wp_ajax_wpv_ct_update_posts used to count dissident posts that are not using
 * the Template assigned to its type. This is called on the Content Templates listing screen for single usage
 * and on the Template edit screen.
 *
 * @since 1.3.0
 */
function wpv_apply_ct_to_cpt_posts_popup_callback() {
	wpv_ajax_authenticate( 'work_view_template', array( 'parameter_source' => 'get', 'type_of_death' => 'data' ) );

    if ( 
		isset ( $_GET['type'] ) 
		&& isset( $_GET['id'] ) 
	) {
        $type = sanitize_text_field( $_GET['type'] );
        $id = intval( $_GET['id'] );
    } else {
		$data = array(
			'message' => __( 'Wrong data.', 'wpv-views' )
		);
		wp_send_json_error( $data );
    }
	
	try {
        $ct = new WPV_Content_Template_Embedded( $id );
    } catch( Exception $e ) {
        // well, we were not handling non-existent CTs before and I am not sure how to do that now...
        $data = array(
			'message' => __( 'Wrong data.', 'wpv-views' )
		);
		wp_send_json_error( $data );
    }
	
    $dissident_post_count = $ct->get_dissident_posts( $type, 'count' );
    if ( $dissident_post_count > 0 ) {
        $ptype = get_post_type_object( $type );

        if ( $dissident_post_count > 1 ){
            $type_label = $ptype->labels->name;
            $message = '<p>' . sprintf( __( '<strong>%d %s</strong> use a different Content Template.', 'wpv-views' ), $dissident_post_count , $type_label ) . '</p>';
        } else {
            $type_label = $ptype->labels->singular_name;
            $message = '<p>' . sprintf( __( '<strong>%d %s</strong> uses a different Content Template.', 'wpv-views' ), $dissident_post_count, $type_label ) . '</p>';
        }
		$message .= '<p>' 
			. __( 'Maybe you have manually set a different one on them.', 'wpv-views' ) 
			. WPV_MESSAGE_SPACE_CHAR
			. sprintf(
					__( 'Click <em>Update</em> to force this Content Template into all single %s', 'wpv-views' ),
					$type_label
				)
			. '</p>';
		$data = array(
			'dialog_content' => '<div class="wpv-dialog wpv-shortcode-gui-content-wrapper">' . $message . '</div>'
		);
		wp_send_json_success( $data );
        ?>
    <?php
    } else {
		$data = array(
			'message' => __( 'Wrong data.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
}

// Unlink a Content Template for orphaned single posts types when there is no general Template associated with that type

add_action('wp_ajax_wpv_clear_cpt_from_ct', 'wpv_clear_cpt_from_ct_callback');

function wpv_clear_cpt_from_ct_callback() {
	wpv_ajax_authenticate( 'wpv_clear_cpt_from_ct_nonce', array( 'parameter_source' => 'post', 'type_of_death' => 'data' ) );
	if ( ! isset( $_POST['slug'] ) ) {
		$data = array(
			'message' => __( 'Slug not set in the AJAX call.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	} else {
		global $wpdb;
		$type = $_POST['slug'];
		$posts = $wpdb->get_col( 
			$wpdb->prepare(
				"SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} 
				WHERE post_type = %s",
				$type
			)
		);
		$count = sizeof( $posts );
		if ( $count > 0 ) {
		foreach ( $posts as $post ) {
			update_post_meta( $post, '_views_template', 0 );
			}
		}
		wp_send_json_success();
	}
}

/*
 * Add new Content Template - popup structure
 * Added by Gen TODO check this nonce
 */

add_action('wp_ajax_wpv_ct_create_new_render_popup', 'wpv_ct_create_new_render_popup_callback');

/**
 * Render HTML for the Add new Content Template dialog.
 *
 * @since unknown
 */
function wpv_ct_create_new_render_popup_callback(){

    wpv_ajax_authenticate( 'work_view_template', array( 'parameter_source' => 'get', 'type_of_death' => 'data' ) );

    $ct_title = wpv_getget( 'ct_title', '' );
    $ct_id_selected = intval( wpv_getget( 'ct_selected', 0 ) );
    $ct_selected = WPV_Content_Template::get_instance( $ct_id_selected );
	
	ob_start();
	?>
    <div class="wpv-dialog wpv-shortcode-gui-content-wrapper js-wpv-dialog-add-new-content-template wpv-dialog-add-new-content-template">
		<label for="wpv-new-content-template-name">
			<strong><?php _e('Name this Content Template','wpv-views') ?></strong>
		</label>
		<input type="text" value="<?php echo esc_attr( $ct_title ); ?>" id="wpv-new-content-template-name" class="js-wpv-new-content-template-name wpv-new-content-template-name" placeholder="<?php echo esc_attr( __('Content Template name','wpv-views') ) ?>" name="wpv-new-content-template-name">
		<h3><?php _e('What content will this template be for?','wpv-views') ?></h3>
		<p>
			<?php
			_e( 'A Content Template can replace the content of the post with whatever you put into it, including Views shortcodes. ', 'wpv-views' );
			?>
		</p>
		<p>
			<input id="wpv-content-template-no-use" type="checkbox" class="js-dont-assign" <?php checked( $ct_id_selected, 0 ); ?> name="wpv-new-content-template-post-type[]" value="0" />
			<label for="wpv-content-template-no-use"><?php _e("Don't assign to any post type",'wpv-views') ?></label>
		</p>

		<div>
			<?php
				wpv_render_ct_assignment_sections( $ct_selected );
			?>
		</div>
		<div class="js-wpv-error-container"></div>
    </div> <!-- wpv-dialog -->
    <?php
	$response = ob_get_clean();
	$data = array(
		'dialog_content' => $response
	);
	wp_send_json_success( $data );
}

/**
 * Save new CT callback function.
 *
 * Used only on CT listing page now.
 *
 * Added by Gen
 * TODO check this nonce
 *
 * @since unknown
 */
add_action('wp_ajax_wpv_ct_create_new_save', 'wpv_ct_create_new_save_callback');

function wpv_ct_create_new_save_callback()
{
    if ( ! current_user_can( 'manage_options' ) ) {
        die( "Untrusted user" );
    }
    if (
        ! isset( $_POST["wpnonce"] )
        || ! wp_verify_nonce( $_POST["wpnonce"], 'work_view_template' )
    ) {
        die( "Undefined Nonce" );
    }
    $title = '';
    if ( isset( $_POST['title'] ) ) {
        $title = sanitize_text_field( $_POST['title'] );
    }
    if ( empty( $title ) ) {
        print json_encode( array( 'error', __( 'You can not create a Content Template with an empty name.', 'wpv-views' ) ) );
        die();
    }
    if ( ! isset( $_POST['type'] ) ) {
        $_POST['type'] = array( 0 );
    }
    $type = $_POST['type'];
    $create_template = wpv_create_content_template( $title, '', false, '' );
    if ( isset( $create_template['error'] ) ) {
        print json_encode( array( 'error', __( 'A Content Template with that name already exists. Please use another name.', 'wpv-views' ) ) );
        die();
    }
    if ( isset( $create_template['success'] ) ) {
        if ( $type[0] != '0' ) {
            global $WPV_settings;
            foreach ( $type as $type_to_save ) {
                $type_to_save = sanitize_text_field( $type_to_save );
                $WPV_settings[ $type_to_save ] = $create_template['success'];
            }
            $WPV_settings->save();
        }
        print json_encode( array( $create_template['success'] ) );
    } else {
        print json_encode( array( 'error', __( 'An unexpected error happened.', 'wpv-views' ) ) );
    }
    die();
}



/**
 * Delete CT callback function. 
 *
 * DEPRECATED since 1.8. Use wpv_ct_bulk_delete instead!
 *
 * Not used in Views, but I'm not sure about other plugins.
 *
 * @see wpv_ct_bulk_delete_callback()
 */ 
add_action('wp_ajax_wpv_delete_ct', 'wpv_delete_ct_callback');

function wpv_delete_ct_callback(){
	if ( ! current_user_can( 'manage_options' ) ) {
		die( "Untrusted user" );
	}
    if ( 
		! isset( $_POST["wpnonce"] ) 
		|| ! wp_verify_nonce($_POST["wpnonce"], 'work_view_template') 
	) {
		die( "Undefined Nonce" );
	}

    $tid = $_POST['id'];
    global $WPV_settings;
    foreach ( $WPV_settings as $key => $value ) {
        if ( $value == $tid ) {
            $WPV_settings[$key] = 0;
        }
    }
    
    $WPV_settings->save();
    
    wp_delete_post( $tid );
    echo $tid;
    die();
}


//Duplicate CT callback function

add_action('wp_ajax_wpv_duplicate_ct', 'wpv_duplicate_ct_callback');

function wpv_duplicate_ct_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		$data = array(
			'type' => 'capability',
			'message' => __( 'You do not have permissions for that.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	if ( 
		! isset( $_POST["wpnonce"] )
		|| ! wp_verify_nonce( $_POST["wpnonce"], 'work_view_template' )
	) {
		$data = array(
			'type' => 'nonce',
			'message' => __( 'Your security credentials have expired. Please reload the page to get new ones.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	
	$title = '';
	if ( isset( $_POST['title'] ) ) {
	   $title = sanitize_text_field( $_POST['title'] );
	}
	if ( empty( $title ) ) {
		$data = array(
			'type' => 'title',
			'message' => __( 'You can not create a Content Template with an empty name.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}

    // Load the original CT.
    $original_ct_id = $_POST["id"];
    try {
        $original_ct = new WPV_Content_Template( $original_ct_id );
    } catch( Exception $e ) {
		$data = array(
			'type' => 'error',
			'message' => __( 'An unexpected error happened.', 'wpv-views' )
		);
		wp_send_json_error( $data );
    }

    // Check for uniqueness of the new title.
    if( WPV_Content_Template_Embedded::is_name_used( $title ) ) {
		$data = array(
			'type' => 'title',
			'message' => __( 'A Content Template with that name already exists. Please use another name.', 'wpv-views' )
		);
		wp_send_json_error( $data );
    }

    // Clone and report the result.
    $cloned_ct = $original_ct->clone_this( $title, false );

    if ( null == $cloned_ct ) {
		$data = array(
			'type' => 'error',
			'message' => __( 'An unexpected error happened.', 'wpv-views' )
		);
		wp_send_json_error( $data );
    } else {
        wp_send_json_success();
    }
}

// Change CT usage - popup structure TODO review this nonces

add_action('wp_ajax_wpv_change_ct_usage_popup', 'wpv_change_ct_usage_popup');

/**
 * Render HTML for the dialog to change CT assignment.
 *
 * Expects following GET parameters:
 * - wpnonce: A valid work_view_template nonce.
 * - id: Valid ID of a Content Template
 *
 * @since unknown
 */
function wpv_change_ct_usage_popup() {

    wpv_ajax_authenticate( 'work_view_template', array( 'parameter_source' => 'get', 'type_of_death' => 'data' ) );

    $id = intval( wpv_getget( 'id', 0 ) );
    $ct = WPV_Content_Template::get_instance( $id );
    if( null == $ct ) {
		$data = array(
			'type' => 'error',
			'message' => __( 'Invalid CT ID.', 'wpv-views' )
		);
		wp_send_json_error( $data );
    }
	ob_start();
    ?>
    <div class="wpv-dialog wpv-shortcode-gui-content-wrapper js-wpv-dialog-add-new-content-template wpv-dialog-add-new-content-template">
        <h3><?php _e('What content will this template be for?','wpv-views') ?></h3>
		<form method="" id="wpv-add-new-content-template-form">
             <?php wpv_render_ct_assignment_sections( $ct ); ?>
        </form>
	</div>
    <?php
	$response = ob_get_clean();
	$data = array(
		'dialog_content' => $response
	);
	wp_send_json_success( $data );
}


/**
 * Render part of the dialog for assigning CT to different loops/post types.
 *
 * This will render the "toggle" section headers and section content with checkboxes. If a CT is provided,
 * checkbox value and section toggle states will be adjusted to it's current settings.
 *
 * This code is being used in "Add new Content Template" and "Change template usage" dialogs.
 *
 * @param WPV_Content_Template|null $ct Existing CT for default values or null.
 *
 * @since 1.10
 */
function wpv_render_ct_assignment_sections( $ct = null ) {

    global $WPV_view_archive_loop;
    $single_post_type_loops = $WPV_view_archive_loop->get_archive_loops( 'post_type', false, true, true );
    $post_type_loops = $WPV_view_archive_loop->get_archive_loops( 'post_type', false, true );
    $taxonomy_archive_loops = $WPV_view_archive_loop->get_archive_loops( 'taxonomy', false, true );

    $asterisk = '<span style="color:red;">*</span>';
    $asterisk_explanation = __( '<span style="color:red">*</span> A different Content Template is already assigned to this item', 'wpv-views' );

    $has_ct = ( null != $ct );

    //
    // Section for single post type assignment
    //

    ?>
    <p>
        <span class="js-wpv-content-template-open wpv-content-template-open" title="<?php echo esc_attr( __( "Click to toggle", 'wpv-views' ) ); ?>">
            <?php echo __( 'Single pages', 'wpv-views' ); ?>:
            <i class="icon-caret-down"></i>
        </span>
    </p>
    <?php

    $open_section = false;
    $show_asterisk_explanation = false;

    // The output buffer is used because first we need to determine the value of $open_section
    // and then we can render the content.
    ob_start();

    if ( count( $single_post_type_loops ) > 0 ) {
        echo '<ul>';
        foreach ( $single_post_type_loops as $post_type_loop ) {
            $post_type = $post_type_loop['post_type_name'];
            $setting_name = 'views_template_for_' . esc_attr( $post_type );
            $assigned_ct_id = $post_type_loop['single_ct'];
            $type_current = $type_used = false;

            if ( $assigned_ct_id != 0 ) {
                $type_used = true;
                $show_asterisk_explanation = true;
            }
            if ( $has_ct && $ct->id == $assigned_ct_id ) {
                $type_current = true;
                $type_used = false;
                $open_section = true;
            }

            printf(
                '<li>
                    <input id="%s" type="checkbox" name="wpv-new-content-template-post-type[]" %s data-title="%s" value="%s" />
                    <label for="%s">%s%s</label>
                </li>',
                $setting_name, checked( $type_current, true, false ), esc_attr( $post_type_loop['display_name'] ),  $setting_name,
                $setting_name, $post_type_loop['display_name'], ( $type_used ? $asterisk : '' )
            );

        }

        echo '</ul>';

        if ( $show_asterisk_explanation ) {
            printf( '<span class="wpv-asterisk-explanation">%s</span>', $asterisk_explanation );
        }

    } else {
        _e( 'There are no single post types to assign Content Templates to', 'wpv-views' );
    }

    $s_content = ob_get_clean();

    ?>
    <div class="js-wpv-content-template-dropdown-list wpv-content-template-dropdown-list<?php echo $open_section ? '' : ' hidden'; ?>">
        <?php echo $s_content; ?>
    </div>
    <p>
        <span class="js-wpv-content-template-open wpv-content-template-open" title="<?php echo esc_attr( __( "Click to toggle", 'wpv-views' ) ); ?>">
            <?php echo __( 'Post type archives', 'wpv-views' ); ?>:
            <i class="icon-caret-down"></i>
        </span>
    </p>
    <?php

    //
    // Section for post type loops
    //

    $open_section = false;
    $show_asterisk_explanation = false;
    ob_start();

    if ( count( $post_type_loops ) > 0 ) {

        echo '<ul>';
        foreach ( $post_type_loops as $post_type_loop ) {
            $post_type = $post_type_loop['post_type_name'];
            $setting_name = 'views_template_archive_for_' . esc_attr( $post_type );
            $assigned_ct_id = $post_type_loop['ct'];
            $type_current = $type_used = false;
            if ( $assigned_ct_id != 0 ) {
                $type_used = true;
                $show_asterisk_explanation = true;
            }
            if ( $has_ct && $ct->id == $assigned_ct_id ) {
                $type_current = true;
                $type_used = false;
                $open_section = true;
            }

            printf(
                '<li>
                    <input id="%s" type="checkbox" name="wpv-new-content-template-post-type[]" %s data-title="%s" value="%s" />
                    <label for="%s">%s%s</label>
                </li>',
                $setting_name, checked( $type_current, true, false ), esc_attr( $post_type_loop['display_name'] ), $setting_name,
                $setting_name, $post_type_loop['display_name'], ( $type_used ? $asterisk : '' )
            );

        }

        echo '</ul>';

        if ( $show_asterisk_explanation ) {
            printf( '<span class="wpv-asterisk-explanation">%s</span>', $asterisk_explanation );
        }

    } else {
        _e( 'There are no post type archives to assign Content Templates to', 'wpv-views' );
    }

    $pta_content = ob_get_clean();

    ?>
    <div class="js-wpv-content-template-dropdown-list wpv-content-template-dropdown-list<?php echo $open_section ? '' : ' hidden'; ?>">
        <?php echo $pta_content; ?>
    </div>
    <p>
        <span class="js-wpv-content-template-open wpv-content-template-open" title="<?php echo esc_attr( __( "Click to toggle", 'wpv-views' ) ); ?>">
            <?php echo __( 'Taxonomy archives', 'wpv-views' ); ?>:
            <i class="icon-caret-down"></i>
        </span>
    </p>
    <?php

    //
    // Section for taxonomy archive loops
    //

    $open_section = false;
    $show_asterisk_explanation = false;
    ob_start();
    if ( count( $taxonomy_archive_loops ) > 0 ) {
        echo '<ul>';

        foreach ( $taxonomy_archive_loops as $taxonomy_archive_loop ) {

            $type = $taxonomy_archive_loop['slug'];
            $label = $taxonomy_archive_loop['display_name'];
            $setting_name = 'views_template_loop_' . $type;
            $assigned_ct_id = $taxonomy_archive_loop['ct'];
            $type_current = $type_used = false;

            if ( 0 != $assigned_ct_id ) {
                $type_used = true;
                $show_asterisk_explanation = true;
            }
            if ( $has_ct && $ct->id == $assigned_ct_id ) {
                $type_current = true;
                $type_used = false;
                $open_section = true;
            }

            printf(
                '<li>
                    <input id="%s" type="checkbox" name="wpv-new-content-template-post-type[]" %s data-title="%s" value="%s" />
                    <label for="%s">%s%s</label>
                </li>',
                $setting_name, checked( $type_current, true, false ), esc_attr( $label ), $setting_name,
                $setting_name, $label, ( $type_used ? $asterisk : '' )
            );

        }

        echo '</ul>';

        if ( $show_asterisk_explanation ) {
            printf( '<span class="wpv-asterisk-explanation">%s</span>', $asterisk_explanation );
        }

    } else {
        _e( 'There are no taxonomy archives to assign Content Templates to', 'wpv-views' );
    }

    $tax_content = ob_get_clean();

    ?>
    <div class="js-wpv-content-template-dropdown-list wpv-content-template-dropdown-list<?php echo $open_section ? '' : ' hidden'; ?>">
        <?php echo $tax_content; ?>
    </div>
    <?php
}

add_action('wp_ajax_wpv_change_ct_usage', 'wpv_change_ct_usage_callback');

function wpv_change_ct_usage_callback() {
	wpv_ajax_authenticate( 'work_view_template', array( 'parameter_source' => 'post', 'type_of_death' => 'data' ) );

    global $WPV_settings;
    $id = $_POST["view_template_id"];
    if ( isset( $_POST['type'] ) ) {
        $type = $_POST['type'];
    } else {
        $type = array();
    }
    
    foreach ( $WPV_settings as $key => $value ) {
        if ( $value == $id ) {
            $WPV_settings[$key] = 0;
        }
    }
    
    for ( $i = 0; $i < count( $type ); $i++ ) {
        $WPV_settings[$type[$i]] = $id;
    }
    
    $WPV_settings->save();
	do_action( 'wpv_action_wpv_save_item', $id );
    wp_send_json_success();
}

// Change CT action - popup structure 

add_action('wp_ajax_wpv_change_ct_assigned_to_something_dialog', 'wpv_change_ct_assigned_to_something_dialog_callback');

function wpv_change_ct_assigned_to_something_dialog_callback(){
    wpv_ajax_authenticate( 'work_view_template', array( 'parameter_source' => 'get', 'type_of_death' => 'data' ) );
	
    global $wpdb, $WPV_settings;
	$values_to_prepare = array();
	$exclude_loop_templates = '';
	$exclude_loop_templates_ids = wpv_get_loop_content_template_ids();
	if ( count( $exclude_loop_templates_ids ) > 0 ) {
		$exclude_loop_templates_ids_sanitized = array_map( 'esc_attr', $exclude_loop_templates_ids );
		$exclude_loop_templates_ids_sanitized = array_map( 'trim', $exclude_loop_templates_ids_sanitized );
		// is_numeric + intval does sanitization
		$exclude_loop_templates_ids_sanitized = array_filter( $exclude_loop_templates_ids_sanitized, 'is_numeric' );
		$exclude_loop_templates_ids_sanitized = array_map( 'intval', $exclude_loop_templates_ids_sanitized );
		if ( count( $exclude_loop_templates_ids_sanitized ) > 0 ) {
			$exclude_loop_templates = " AND p.ID NOT IN ('" . implode( "','" , $exclude_loop_templates_ids_sanitized ) . "') ";
		}
	}
	$values_to_prepare[] = 'view-template';
	$view_tempates_available = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT p.ID, p.post_title 
			FROM {$wpdb->posts} p
			WHERE p.post_status = 'publish' 
			AND p.post_type = %s 
			{$exclude_loop_templates}
			ORDER BY p.post_title",
			$values_to_prepare
		)
	);
	
    $post_type = sanitize_text_field( $_GET['pt'] );
	$selected = ( isset( $WPV_settings[$post_type] ) ) ? $WPV_settings[$post_type] : 0;
	ob_start();
    ?>
    <div class="wpv-dialog wpv-shortcode-gui-content-wrapper js-wpv-dialog-add-new-content-template wpv-dialog-add-new-content-template">
		<h3><?php _e( 'Select one of the following Content Templates.', 'wpv-views' ); ?></h3>
		<ul class="wpv-mightlong-list">
			<li>
				<input type="radio" id="wpv-content-template-name-0" class="js-wpv-content-template-name" name="wpv-content-template-name" <?php checked( 0, $selected ); ?> value="0" />
				<label for="wpv-content-template-name-0"><?php _e( 'Don’t use any Content Template', 'wpv-views' ); ?></label>
			</li>
			<?php
			if ( count( $view_tempates_available ) > 0 ) {
				foreach ( $view_tempates_available as $row ) {
					?>
						<li>
							<input type="radio" id="wpv-content-template-name-<?php echo esc_attr( $row->ID );?>" class="js-wpv-content-template-name" name="wpv-content-template-name" <?php checked( $row->ID, $selected ); ?> value="<?php echo esc_attr( $row->ID );?>" />
							<label for="wpv-content-template-name-<?php echo esc_attr( $row->ID );?>"><?php echo $row->post_title; ?></label>
						</li>
					<?php
				}
			}
			?>
		</ul>
    </div>
    <?php
	$response = ob_get_clean();
	$data = array(
		'dialog_content' => $response
	);
	wp_send_json_success( $data );
}

// Change CT action callback function TODO check nonces

add_action('wp_ajax_wpv_change_ct_assigned_to_something', 'wpv_change_ct_assigned_to_something_callback');

function wpv_change_ct_assigned_to_something_callback() {
	wpv_ajax_authenticate( 'work_view_template', array( 'parameter_source' => 'post', 'type_of_death' => 'data' ) );
    
    global $WPV_settings;
    $pt = sanitize_text_field( $_POST["pt"] );    
    if ( isset( $_POST['value'] ) ) {
        $value = intval( $_POST['value'] );
		do_action( 'wpv_action_wpv_save_item', $value );
    } else {
        $value = 0;
    }
    $WPV_settings[$pt] = $value;
    $WPV_settings->save();
	
    wp_send_json_success();
}

// Response when updating all posts to use a given CT - popup structure TODO localize!!!! and check nonce
// TODO seems that this is called in a colorbox callback, but BUT is executes the delete... TODO review this all

add_action('wp_ajax_wpv_apply_ct_to_cpt_posts', 'wpv_apply_ct_to_cpt_posts_callback');

function wpv_apply_ct_to_cpt_posts_callback() {
	wpv_ajax_authenticate( 'work_view_template', array( 'parameter_source' => 'post', 'type_of_death' => 'data' ) );
   
    $id = intval( $_POST['id'] );
    $type = sanitize_text_field( $_POST['type'] );
    wpv_update_dissident_posts_from_template( $id, $type, false );
	
    wp_send_json_success();
}

add_action('wp_ajax_wpv_content_template_move_to_trash', 'wpv_content_template_move_to_trash_callback');

/** Move CT to trash or show message.
 *
 * Prints a JSON array. If CT is not in use, it is trashed and first element of the array is "move" and second one
 * contains ID of the CT. Otherwise first element is "show" and second contains HTML for the colorbox dialog that should
 * be shown.
 *
 * @deprecated Not used in Views since 1.10. Use wpv_bulk_content_templates_move_to_trash instead.
 */
function wpv_content_template_move_to_trash_callback() {
	wpv_ajax_authenticate( 'wpv_view_listing_actions_nonce', array( 'parameter_source' => 'post', 'type_of_death' => 'data' ) );

	if ( isset( $_POST['id'] ) ) {
		$ct_id = intval( $_POST['id'] );
	} else {
		$data = array(
			'type' => 'error',
			'message' => __( 'Invalid CT ID.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	global $wpdb;
	$posts_count = $wpdb->get_var( 
		$wpdb->prepare( 
			"SELECT count(posts.ID) FROM {$wpdb->posts} as posts,{$wpdb->postmeta} as postmeta 
			WHERE postmeta.meta_key = '_views_template' 
			AND postmeta.meta_value = %s 
			AND postmeta.post_id = posts.ID",
			$ct_id 
		) 
	);
	if ( $posts_count == 0 ) {

		$my_post = array(
			'ID'          => $ct_id,
			'post_status' => 'trash'
		);
		// TODO $return is never used; should it be?
		$return = wp_update_post( $my_post );
		do_action( 'wpv_action_wpv_save_item', $ct_id );
		wpv_replace_views_template_options( $ct_id, 0 );
		
		$data = array(
			'action' => 'reload'
		);
		wp_send_json_success( $data );
	} else {
		$template_list = $wpdb->get_results( 
			$wpdb->prepare( 
				"SELECT ID, post_title FROM {$wpdb->posts} 
				WHERE post_status = 'publish' 
				AND post_type = 'view-template' 
				AND ID != %s",
				$ct_id 
			)
		);
		ob_start();
		?>
		<div class="wpv-dialog wpv-shortcode-gui-content-wrapper">
			<p>
				<?php echo sprintf( _n('1 item', '%s items', $posts_count, 'wpv-views'), $posts_count ) . __( ' use this content template. What do you want to do?', 'wpv-views' ); ?>
			</p>
			<ul>
                <?php if ( count($template_list) > 0 ){?>
                <li><label>
                    <input type="radio" name="wpv-content-template-replace-to" class="js-wpv-existing-posts-ct-replace-to js-wpv-existing-posts-ct-replace-to-selected-ct" value="0" id="wpv-content-template-replace-to" />
                    <?php _e( 'Choose a different content template for them: ', 'wpv-views' )?>
                    </label>
                    <select name="wpv-ct-list-for-replace" class="js-wpv-ct-list-for-replace" id="wpv-ct-list-for-replace">
                    	<option value=''><?php _e( 'Select Content Template', 'wpv-views' )?> </option>
                    <?php
						foreach( $template_list as $temp_post ) :
							echo '<option value="'. esc_attr( $temp_post->ID ).'">'. $temp_post->post_title .'</option>';
                        endforeach;
					?></select>

                </li>
                <?php }?>
                <li><label>
                    <input type="radio" name="wpv-content-template-replace-to" class="js-wpv-existing-posts-ct-replace-to" value="1" />
                    <?php _e( 'Don\'t use any content template for these items', 'wpv-views' )?>
                    </label>
                </li>
             </ul>
        </div>
        <?php
        $response = ob_get_clean();		
		$data = array(
			'action'			=> 'dialog',
			'dialog_content'	=> $response
		);
		wp_send_json_success( $data );
	}
}


/**
 * Initiate a bulk trash action.
 *
 * For given content templates, find out how many posts use them. If no template is used in any post,
 * trash them right away. If one or more templates is being used, render HTML for a colorbox dialog
 * (wpv-dialog-bulk-replace-content-template) that will ask user to decide how to replace those templates.
 * See js events on '.js-ct-bulk-replace-usage' for further information.
 *
 * Expected POST parameters:
 * - ids: A non-empty array of content template IDs.
 * - wpnonce: A valid wpv_view_listing_actions_nonce.
 * @todo use dedicated nonce containing user and CT id; note that this is used also on CT edit page
 *
 * Response is an JSON object containing following properties:
 * - all_ids: Array of all content templates that were/should be trashed.
 * - action: 'trashed' if CTs have been trashed, 'confirm' if a popup should be shown before that.
 * - popup_content: If action is 'confirm', this will contain the HTML of the popup.
 * 
 * @since 1.7
 */ 
add_action( 'wp_ajax_wpv_bulk_content_templates_move_to_trash', 'wpv_bulk_content_templates_move_to_trash_callback' );

function wpv_bulk_content_templates_move_to_trash_callback() {
	wpv_ajax_authenticate( 'wpv_view_listing_actions_nonce', array( 'parameter_source' => 'post', 'type_of_death' => 'data' ) );

	if ( ! isset( $_POST['ids'] ) ) {
		// We don't allow empty input
		$data = array(
			'type' => 'error',
			'message' => __( 'Invalid CT ID.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	} else if ( is_string( $_POST['ids'] ) ) {
		$ct_ids = array( $_POST['ids'] );
	} else {
		$ct_ids = $_POST['ids'];
	}
	
	$ct_ids = array_map( 'esc_attr', $ct_ids );
	$ct_ids = array_map( 'trim', $ct_ids );
	// is_numeric does sanitization
	$ct_ids = array_filter( $ct_ids, 'is_numeric' );
	$ct_ids = array_map( 'intval', $ct_ids );

	$result = array(
        'all_ids' => $ct_ids,
        'success' => true
    );

	// Determine which templates are currently in use.
	global $wpdb;

	// This will hold information about used templates indexed by their IDs
	$used_templates = array();
	foreach( $ct_ids as $template_id ) {
		// TODO this probably counts drafts, autosaves, etc. Is that desired?
		$using_posts_count = $wpdb->get_var( 
			$wpdb->prepare(
				"SELECT DISTINCT COUNT( posts.ID )
				FROM {$wpdb->posts} AS posts, {$wpdb->postmeta} AS postmeta
				WHERE postmeta.meta_key = '_views_template'
				AND postmeta.meta_value = %s
				AND postmeta.post_id = posts.ID",
				$template_id 
			) 
		);
		if( $using_posts_count > 0 ) {
			$template_title = $wpdb->get_var( 
				$wpdb->prepare(
					"SELECT post_title FROM {$wpdb->posts} 
					WHERE ID = %d 
					LIMIT 1",
					$template_id 
				) 
			);
			$used_templates[ $template_id ] = array(
					'title' => $template_title,
					'usage_count' => $using_posts_count );
		}
	}

	if ( empty( $used_templates ) ) {
		// No template is used, we can trash them all.

		global $WPV_settings;

		foreach( $ct_ids as $template_id ) {

			// Trash the template
			$my_post = array(
					'ID' => $template_id,
					'post_status' => 'trash' );
			wp_update_post( $my_post );
			do_action( 'wpv_action_wpv_save_item', $template_id );

			// Remove references to trashed template from Views options
			wpv_replace_views_template_options( $template_id, 0, $WPV_settings );
		}

        $WPV_settings->save();
		
		$data['action'] = 'reload';
		wp_send_json_success( $data );
		
	} else {
        // One or more templates are in use, we need to show a confirmation.

		// Get list of templates that can be used as a replacement for the trashed ones.
		$templates_to_trash = implode( ',', $ct_ids );
		$template_list = $wpdb->get_results( 
			"SELECT ID, post_title
			FROM {$wpdb->posts}
			WHERE post_status = 'publish'
			AND post_type = 'view-template'
			AND ID NOT IN ( " . $templates_to_trash . " )"  
		);

		// Render popup content.
		ob_start();

		?>
		<div class="wpv-dialog wpv-shortcode-gui-content-wrapper js-wpv-dialog-bulk-replace-content-template wpv-dialog-bulk-replace-content-template">
			<p><?php _e( 'These content templates are in use. What do you want to do?', 'wpv-views' ); ?></p>
			<?php
			// Show a div with options for each used template.
			foreach( $used_templates as $template_id => $template_info ) {
				$template_title = $template_info['title'];
				$template_usage_count = $template_info['usage_count'];

				?>
				<div>
					<?php
						printf(
								'<p><strong>%s</strong> (%s %s)</p>',
								$template_title,
								__( 'used by', 'wpv-view' ),
								sprintf( _n( '1 item', '%s items', $template_usage_count, 'wpv-views' ), $template_usage_count ) );
					?>
					<ul>
						<?php
							/* Show an option to replace this template with another one, if there are some left.
							 * Radio buttons are grouped by name: "wpv-content-template-replace-{$template_id}-to".
							 * Select field for replacement template is identified as "wpv-ct-list-for-replace-{$template_id}"
							 *
							 * Submit button contains attributes 'data-ct-ids' with all Content Template IDs that should be trashed
							 * and 'data-replace-ids' with those that should be replaced. */ 
							if( !empty( $template_list ) ) {
								?>
								<li>
									<label>
										<?php
											printf(
													'<input type="radio" name="wpv-content-template-replace-%d-to"
														class="js-wpv-bulk-existing-posts-ct-replace-to js-wpv-bulk-existing-posts-ct-replace-to-selected-ct"
														value="different_template" id="wpv-content-template-replace-to" />',
													$template_id );
											_e( 'Choose a different content template for them: ', 'wpv-views' );
										?>
									</label>
									<?php
										printf( '<select class="js-wpv-bulk-ct-list-for-replace" id="wpv-ct-list-for-replace-%d" data-template-id="%d">', esc_attr( $template_id ), esc_attr( $template_id ) );
										printf( '<option value="">%s</option>', __( 'Select Content Template', 'wpv-views' ) );
										foreach( $template_list as $temp_post ) {
											printf( '<option value="%s">%s</option>', $temp_post->ID, $temp_post->post_title );
										}
										printf( '</select>' );
									?>
								</li>
								<?php
							}
						?>
						<li>
							<label>
								<?php
									printf( '<input type="radio" name="wpv-content-template-replace-%d-to"
												class="js-wpv-bulk-existing-posts-ct-replace-to" value="no_template" />',
											$template_id );
									_e( 'Don\'t use any content template for these items', 'wpv-views' );
								?>
							</label>
						</li>
					</ul>
				</div>
				<?php
				printf(
					'<input type="hidden" class="js-ct-bulk-replace-usage-data" data-ct-ids="%s" data-replace-ids="%s" />',
					urlencode( implode( ',', $ct_ids ) ),
					urlencode( implode( ',', array_keys( $used_templates ) ) )
				);
			}
			?>
		</div>
		<?php

		$dialog_content = ob_get_clean();;
		
		$data['action'] = 'dialog';
		$data['dialog_content'] = $dialog_content;
		wp_send_json_success( $data );
	}
	
}




// Change CT usage before move to trash
add_action('wp_ajax_wpv_ct_trash_with_replace', 'wpv_ct_trash_with_replace_callback');

function wpv_ct_trash_with_replace_callback() {
	wpv_ajax_authenticate( 'wpv_view_listing_actions_nonce', array( 'parameter_source' => 'post', 'type_of_death' => 'data' ) );
	
	if ( isset( $_POST['id'] ) ) {
		$ct_id = intval( $_POST['id'] );
	} else {
		$data = array(
			'type' => 'error',
			'message' => __( 'Invalid CT ID.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	
	global $wpdb;
	$replace = ( $_POST['replace_to'] == 0 ) ? intval( $_POST['replace_ct'] ) : 0;
	$wpdb->query( 
		$wpdb->prepare( 
			"UPDATE {$wpdb->postmeta} 
			SET meta_value = %s 
			WHERE meta_key = '_views_template' 
			AND meta_value = %s", 
			$replace,
			$ct_id
		) 
	);

	wpv_replace_views_template_options( $ct_id, $replace );

	$my_post = array(
		'ID'           => $ct_id,
		'post_status' => 'trash'
	);
	wp_update_post( $my_post );
	do_action( 'wpv_action_wpv_save_item', $ct_id );
	
	wp_send_json_success();
}



/**
 * Replace content templates that are being used by some posts and trash all given content templates (which may be a
 * superset of those being used).
 *
 * Expected POST parameters:
 * - ids: an array of IDs of all templates that should be trashed
 * - toreplace: dtto, templates that should be replaced
 * - replacements: dtto, replacement templates (same lenght and order as toreplace)
 * - wpnonce: A valid wpv_view_listing_actions_nonce.
 *
 * Content templates from 'toreplace' used in posts (and post types) will be replaced by 'replacements'. Zero values
 * in 'replacements' are interpreted as "no template". Then, all templates from 'ids' will be trashed.
 *
 * Outputs '1' on success.
 *
 * @since 1.7
 */ 
add_action( 'wp_ajax_wpv_ct_bulk_trash_with_replace', 'wpv_ct_bulk_trash_with_replace_callback' );

function wpv_ct_bulk_trash_with_replace_callback() {
	wpv_ajax_authenticate( 'wpv_view_listing_actions_nonce', array( 'parameter_source' => 'post', 'type_of_death' => 'data' ) );
	
	if ( ! isset( $_POST['ids'] ) ) {
		// Don't allow empty input
		$data = array(
			'type' => 'error',
			'message' => __( 'Invalid CT ID.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	} else if ( is_string( $_POST['ids'] ) ) {
		$ct_ids = array( $_POST['ids'] );
	} else {
		$ct_ids = $_POST['ids'];
	}
	
	$ct_ids = array_map( 'esc_attr', $ct_ids );
	$ct_ids = array_map( 'trim', $ct_ids );
	// is_numeric does sanitization
	$ct_ids = array_filter( $ct_ids, 'is_numeric' );
	$ct_ids = array_map( 'intval', $ct_ids );

	if ( 
		isset( $_POST['replacements'] ) 
		&& isset( $_POST['toreplace'] )
		&& is_array( $_POST['replacements'] ) 
		&& is_array( $_POST['toreplace'] ) 
	) {
		/* This will hold template IDs as keys and IDs of their replacements as values. Value 0 indicates
		 * 'don't use any content template'. */
		$replacements = array();

		$replacement_count = count( $_POST['replacements'] );
		for ( $i = 0; $i < $replacement_count; ++$i ) {
			$replacements[ $_POST['toreplace'][ $i ] ] = $_POST['replacements'][ $i ];
		}
	} else {
		$data = array(
			'type' => 'error',
			'message' => __( 'Invalid data', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	
	global $wpdb, $WPV_settings;

	// Replace content templates as requested
	foreach( $replacements as $original_template_id => $replacement_template_id ) {
		$changed_rows = $wpdb->query( 
			$wpdb->prepare(
				"UPDATE {$wpdb->postmeta}
				SET meta_value = %s
				WHERE meta_key = '_views_template'
				AND meta_value = %s",
				$replacement_template_id,
				$original_template_id 
			) 
		);

		wpv_replace_views_template_options( $original_template_id, $replacement_template_id, $WPV_settings );
	}

	// Now trash all requested templates
	foreach( $ct_ids as $template_id ) {
		$my_post = array(
				'ID' => $template_id,
				'post_status' => 'trash' );
		wp_update_post( $my_post );
		do_action( 'wpv_action_wpv_save_item', $template_id );

		// Remove references to trashed template from Views options
		wpv_replace_views_template_options( $template_id, 0, $WPV_settings );
	}

	$WPV_settings->save();
	
	wp_send_json_success();
}



/**
 * Count posts where given Content Templates are used.
 *
 * For given array of Content Template IDs it calculates in how many posts is each template used.
 * Outputs a JSON representation of an array where keys are CT IDs and values are post counts.
 * This array also allways contains an element "0" with the sum of all post counts.
 *
 * Expected GET parameters:
 * - wpnonce: A valid wpv_view_listing_actions_nonce.
 * - ids: An array of CT IDs
 *
 * @since 1.7
 */ 
add_action( 'wp_ajax_wpv_ct_bulk_count_usage', 'wpv_ct_bulk_count_usage_callback' );

function wpv_ct_bulk_count_usage_callback() {
	wpv_ajax_authenticate( 'wpv_view_listing_actions_nonce', array( 'parameter_source' => 'get', 'type_of_death' => 'data' ) );

	if( !isset( $_POST['ids'] ) ) {
		$ct_ids = array();
	} else if( is_string( $_POST['ids'] ) ) {
		$ct_ids = array( $_POST['ids'] );
	} else {
		$ct_ids = $_POST['ids'];
	}

	global $wpdb;
	$data = array();

	$usage_results = array();
	$total_usage = 0;
	
	foreach( $ct_ids as $ct_id ) {
		$assigned_count = $wpdb->get_var( 
			$wpdb->prepare(
				"SELECT COUNT(post_id)
				FROM {$wpdb->postmeta} JOIN {$wpdb->posts} p
				WHERE meta_key='_views_template'
				AND meta_value = %d
				AND post_id = p.ID
				AND p.post_status NOT IN ('auto-draft')
				AND p.post_type != 'revision'",
				$ct_id 
			) 
		);
		$usage_results[] = array(
			'id'	=> $ct_id,
			'count'	=> $assigned_count
		);
		$total_usage += $assigned_count;
	}

	$data['total_usage'] = $total_usage;
	$data['usage_results'] = $usage_results;
	
	wp_send_json_success( $data );

}


/**
 * Bulk delete Content Templates.
 *
 * Expects following POST parameters:
 * - wpnonce: A valid wpv_view_listing_actions_nonce.
 * - ids: An array of CT IDs to be deleted.
 *
 * Deletes templates and removes all occurences of their IDs from Views options.
 *
 * Outputs '1' on success.
 *
 * @since 1.7
 */ 
add_action( 'wp_ajax_wpv_ct_bulk_delete', 'wpv_ct_bulk_delete_callback' );

function wpv_ct_bulk_delete_callback() {
	wpv_ajax_authenticate( 'wpv_view_listing_actions_nonce', array( 'parameter_source' => 'post', 'type_of_death' => 'data' ) );

	if( !isset( $_POST['ids'] ) ) {
		$ct_ids = array();
	} else if( is_string( $_POST['ids'] ) ) {
		$ct_ids = array( $_POST['ids'] );
	} else {
		$ct_ids = $_POST['ids'];
	}

    global $WPV_settings;

    foreach( $ct_ids as $ct_id ) {
		wpv_replace_views_template_options( $ct_id, 0, $WPV_settings );
		wp_delete_post( $ct_id );
	}
	
	$WPV_settings->save();

	wp_send_json_success();
}



add_action( 'wp_ajax_wpv_ct_bind_posts', 'wpv_ct_bind_posts_callback' );

/**
 * Bind specific posts to a Content Template.
 *
 * Following POST parameters are expected:
 * - id: Content Template ID
 * - wpnonce: A valid wpv_ct_{$id}_bind_posts_by_{$user_id} nonce.
 * - posts_to_bind: An array of post IDs that should be bound.
 *
 * Returns a default WP json response (error/success), possibly with a debug message
 * on error.
 *
 * @since 1.9
 */
function wpv_ct_bind_posts_callback() {
    // Authentication and validation
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Untrusted user' );
    }
    $ct_id = (int) wpv_getpost( 'id' );
    $uid = get_current_user_id();

    $nonce_name = "wpv_ct_{$ct_id}_bind_posts_by_{$uid}";
    if ( ! wp_verify_nonce( wpv_getpost( 'wpnonce' ), $nonce_name ) ) {
        wp_send_json_error( "Security check ($nonce_name)" );
    }

    $ct = WPV_Content_Template::get_instance( $ct_id );
    if( null == $ct ) {
        wp_send_json_error( 'Invalid Content Template' );
    }

    $posts_to_bind = wpv_getpost( 'posts_to_bind' );
    if( !is_array( $posts_to_bind ) ) {
        wp_send_json_error( 'Invalid arguments (' . print_r( $posts_to_bind, true ) . ')' );
    }

    // Post binding
    $updated = $ct->bind_posts( $posts_to_bind );

    if( false === $updated ) {
        wp_send_json_error( 'bind_posts failed' );
    }

    wp_send_json_success( array( 'updated' => $updated ) );
}



/* ************************************************************************* *\
        Miscellaneous
\* ************************************************************************* */


/**
 * wpv_dismiss_pointer_callback
 *
 * Dismiss pointers created with Views, when needed
 *
 * @since 1.7
 *
 * @todo this needs a nonce, the earlier the better
 */
add_action( 'wp_ajax_wpv_dismiss_pointer', 'wpv_dismiss_pointer_callback' );

function wpv_dismiss_pointer_callback() {
	if ( ! isset( $_POST['name'] ) ) {
		echo 'wpv_failure';
		die();
	}
	$pointer = sanitize_key( $_POST['name'] );
	if ( empty( $pointer ) ) {
		echo 'wpv_failure';
		die();
	}
	wpv_dismiss_pointer( $pointer );
	echo 'wpv_success';
	die();
}



/* ************************************************************************* *\
        Helper functions
\* ************************************************************************* */


/**
 * Perform basic authentication check.
 *
 * Check user capability and nonce. Dies with an error message (wp_json_error() by default) if the authentization
 * is not successful.
 *
 * @param string $nonce_name Name of the nonce that should be verified.
 * @param array $args Arguments (
 *     @type string $nonce_parameter Name of the parameter containing nonce value.
 *         Optional, defaults to "wpnonce".
 *     @type string $parameter_source Determines where the function should look for the nonce parameter.
 *         Allowed values are 'get' and 'post'. Optional, defaults to 'post'.
 *     @type string $capability_needed Capability that user has to have in order to pass the check.
 *         Optional, default is "manage_options".
 *     @type string $type_of_death How to indicate failure:
 *         - 'die': The error message will be echoed as a simple string. Default behaviour.
 *         - 'false': Do not die, just return false.
 *         - 'message': Call wp_json_error() and pass message as data.
 *         - 'data': Call wp_json_error with array( 'type' => 'capability'|'nonce', 'message' => $error_message )
 *         Optional, default is 'die'.
 *     )
 *
 * @return bool|void
 *
 * @since 1.9
 */
function wpv_ajax_authenticate( $nonce_name, $args = array() )
{
    // Read arguments
    $type_of_death = wpv_getarr( $args, 'type_of_death', 'die', array( 'die', 'false', 'message', 'data' ) );
    $nonce_parameter = wpv_getarr( $args, 'nonce_parameter', 'wpnonce' );
    $capability_needed = wpv_getarr( $args, 'capability_needed', 'manage_options' );
    $parameter_source_name = wpv_getarr( $args, 'parameter_source', 'post', array( 'get', 'post' ) );
    $parameter_source = ( $parameter_source_name == 'get' ) ? $_GET : $_POST;

    $is_error = false;
    $error_message = null;
    $error_type = null;

    // Check permissions
    if ( ! current_user_can( $capability_needed ) ) {
        $error_message = __( 'You do not have permissions for that.', 'wpv-views' );
        $error_type = 'capability';
        $is_error = true;
    }

    // Check nonce
    if ( !$is_error && !wp_verify_nonce( wpv_getarr( $parameter_source, $nonce_parameter, '' ), $nonce_name ) ) {
        $error_message = __( 'Your security credentials have expired. Please reload the page to get new ones.', 'wpv-views' );
        $error_type = 'nonce';
        $is_error = true;
    }

    if( $is_error ) {
        switch( $type_of_death ) {

            case 'message':
                wp_send_json_error( $error_message );
                break;

            case 'data':
                wp_send_json_error( array( 'type' => $error_type, 'message' => $error_message ) );
                break;

            case 'false':
                return false;

            case 'die':
            default:
                die( $error_message );
                break;
        }
    }

    return true;
}