<?php

/**
* wpv_show_hide_ordering
*
* We can enable this to hide the Ordering section
*
* @param $sections (array) sections on the editor screen
*
* @return $sections
*
* @since unknown
*/

// add_filter('wpv_sections_query_show_hide', 'wpv_show_hide_ordering', 1,1)

function wpv_show_hide_ordering( $sections ) {
	$sections['ordering'] = array(
		'name' => __( 'Ordering', 'wpv-views' ),
	);
	return $sections;
}

/**
* add_view_ordering
*
* Creates the sorting section in the edit screen
*
* @param $view_settings
* @param $view_id
*
* @since unknown
*/

add_action( 'view-editor-section-query', 'add_view_ordering', 30, 2 );

function add_view_ordering( $view_settings, $view_id ) {
    global $WP_Views;
	$hide = '';
	if (
		isset( $view_settings['sections-show-hide'] ) 
		&& isset( $view_settings['sections-show-hide']['ordering'] ) 
		&& 'off' == $view_settings['sections-show-hide']['ordering']
	) {
		$hide = ' hidden';
	}
	$section_help_pointer = WPV_Admin_Messages::edit_section_help_pointer( 'ordering' );
	?>
	<div class="wpv-setting-container wpv-settings-ordering js-wpv-settings-ordering<?php echo $hide; ?>">
		<div class="wpv-settings-header">
			<h3>
				<?php _e( 'Ordering', 'wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip" 
					data-header="<?php echo esc_attr( $section_help_pointer['title'] ); ?>" 
					data-content="<?php echo esc_attr( $section_help_pointer['content'] ); ?>">
				</i>
			</h3>
		</div>
		<div class="wpv-setting js-wpv-setting">
			<p class="wpv-settings-query-type-posts js-wpv-settings-posts-order"<?php echo ( $view_settings['query_type'][0] != 'posts' ) ? ' style="display: none;"' : ''; ?>>
				<?php $view_settings = wpv_order_by_default_settings( $view_settings ); // TODO this should not be needed ?>
				<label for="wpv-settings-orderby"><?php _e( 'Order by: ', 'wpv-views' ) ?></label>
				<select id="wpv-settings-orderby" class="js-wpv-posts-orderby" name="_wpv_settings[orderby]" autocomplete="off" data-rand="<?php echo esc_attr( __('Pagination and random ordering do not work together and would produce unexpected results. Please disable pagination or random ordering.', 'wpv-views') ); ?>">
					<option value="post_date"><?php _e('Post date', 'wpv-views'); ?></option>
					<option value="post_title" <?php selected( $view_settings['orderby'], 'post_title' ); ?>><?php _e('Post title', 'wpv-views'); ?></option>
					<option value="ID" <?php selected( $view_settings['orderby'], 'ID' ); ?>><?php _e('Post ID', 'wpv-views'); ?></option>
					<option value="modified" <?php selected( $view_settings['orderby'], 'modified' ); ?>><?php _e('Last modified', 'wpv-views'); ?></option>
					<option value="menu_order" <?php selected( $view_settings['orderby'], 'menu_order' ); ?>><?php _e('Menu order', 'wpv-views'); ?></option>
					<option value="rand" <?php selected( $view_settings['orderby'], 'rand' ); ?>><?php _e('Random order', 'wpv-views'); ?></option>
					<?php
						$all_types_fields = get_option( 'wpcf-fields', array() );
						$cf_keys = $WP_Views->get_meta_keys();
						foreach ( $cf_keys as $key ) {
							$selected = ( $view_settings['orderby'] == "field-" . $key ) ? ' selected="selected"' : '';
							$option = '<option value="field-' . esc_attr( $key ) . '"' . $selected . '>';
							if ( stripos( $key, 'wpcf-' ) === 0)  {
								if ( 
									isset( $all_types_fields[substr( $key, 5 )] ) 
									&& isset( $all_types_fields[substr( $key, 5 )]['name'] ) 
								) {
									$option .= sprintf(__('Field - %s', 'wpv-views'), $all_types_fields[substr( $key, 5 )]['name']);
								} else {
									$option .= sprintf(__('Field - %s', 'wpv-views'), $key);
								}
							} else {
								$option .= sprintf(__('Field - %s', 'wpv-views'), $key);
							}
							$option .= '</option>';
							echo $option;
						}
					?>
				</select>
				<select name="_wpv_settings[order]" class="js-wpv-posts-order" autocomplete="off">
					<option value="DESC" <?php selected( $view_settings['order'], 'DESC' ); ?>><?php _e( 'Descending', 'wpv-views' ) ?></option>
					<option value="ASC" <?php selected( $view_settings['order'], 'ASC' ); ?>><?php _e( 'Ascending', 'wpv-views' ) ?></option>
				</select>
			</p>
			<p class="wpv-settings-query-type-taxonomy"<?php echo ( $view_settings['query_type'][0] != 'taxonomy' ) ? ' style="display: none;"' : ''; ?>>
				<?php $view_settings = wpv_taxonomy_order_by_default_settings( $view_settings );
				$taxonomy_order_by = array(
					'id' => __('Term ID'),
					'count' => __('Post count'),
					'name' => __('Term name'),
					'slug' => __('Term slug'),
					'term_group' => __('Term group'),
					'none' => __('No order')
				);
				?>
				<label for="wpv-settings-orderby"><?php _e( 'Order by: ', 'wpv-views' ) ?></label>
				<select id="wpv-settings-orderby" class="js-wpv-taxonomy-orderby" name="_wpv_settings[taxonomy_orderby]" autocomplete="off">
					<?php
						foreach ( $taxonomy_order_by as $id => $text ) {
						?>
							<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $view_settings['taxonomy_orderby'], $id ); ?>><?php echo $text; ?></option>
						<?php

						}
					?>
				</select>
				<select name="_wpv_settings[taxonomy_order]" class="js-wpv-taxonomy-order" autocomplete="off">
					<option value="DESC" <?php selected( $view_settings['taxonomy_order'], 'DESC' ); ?>><?php _e( 'Descending', 'wpv-views' ) ?></option>
					<option value="ASC" <?php selected( $view_settings['taxonomy_order'], 'ASC' ); ?>><?php _e( 'Ascending', 'wpv-views' ) ?></option>
				</select>
			</p>
			<p class="wpv-settings-query-type-users"<?php echo ( $view_settings['query_type'][0] != 'users' ) ? ' style="display: none;"' : ''; ?>>
				<?php 
				$users_order_by = array(
					'user_login' => __('User login'),
					'ID' => __('User ID'),
					'user_name' => __('User name'),
					'display_name' => __('User display name'),
					'user_nicename' => __('User nicename'),
					'user_email' => __('User email'),
					'user_url' => __('User URL'),
					'user_registered' => __('User registered date'),
					'post_count' => __('User post count')
				);
				if ( ! isset( $view_settings['users_orderby'] ) ) {
					$view_settings['users_orderby'] = 'user_login';
				}
				if ( ! isset( $view_settings['users_order'] ) ) {
					$view_settings['users_order'] = 'DESC';
				}
				?>
				<label for="wpv-settings-orderby"><?php _e( 'Order by: ', 'wpv-views' ) ?></label>
				<select id="wpv-settings-orderby" class="js-wpv-users-orderby" name="_wpv_settings[users_orderby]" autocomplete="off">
					<?php
						foreach ( $users_order_by as $id => $text ) {
						?>
							<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $view_settings['users_orderby'], $id ); ?>><?php echo $text; ?></option>
						<?php

						}
					?>
				</select>
				<select name="_wpv_settings[users_order]" class="js-wpv-users-order" autocomplete="off">
					<option value="DESC" <?php selected( $view_settings['users_order'], 'DESC' ); ?>><?php _e( 'Descending', 'wpv-views' ) ?></option>
					<option value="ASC" <?php selected( $view_settings['users_order'], 'ASC' ); ?>><?php _e( 'Ascending', 'wpv-views' ) ?></option>
				</select>
			</p>
			<div class="js-wpv-toolset-messages"></div>
		</div>
		<span class="update-action-wrap auto-update js-wpv-update-action-wrap">
			<span class="js-wpv-message-container"></span>
			<span type="hidden" data-success="<?php echo esc_attr( __('Updated', 'wpv-views') ); ?>" data-unsaved="<?php echo esc_attr( __('Not saved', 'wpv-views') ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_ordering_nonce' ); ?>" class="js-wpv-ordering-update" />
		</span>
	</div>
<?php }

// Sorting save callback function - only for Views

add_action( 'wp_ajax_wpv_update_sorting', 'wpv_update_sorting_callback' );

function wpv_update_sorting_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		$data = array(
			'type' => 'capability',
			'message' => __( 'You do not have permissions for that.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	if ( 
		! isset( $_POST["wpnonce"] )
		|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_ordering_nonce' ) 
	) {
		$data = array(
			'type' => 'nonce',
			'message' => __( 'Your security credentials have expired. Please reload the page to get new ones.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
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
	$changed = false;
	$view_array = get_post_meta( $_POST["id"], '_wpv_settings', true );
	$sorting_options = array(
		'orderby', 'order',
		'taxonomy_orderby', 'taxonomy_order',
		'users_orderby', 'users_order'
	);
	foreach ( $sorting_options as $sorting_opt) {
		if (
			isset( $_POST[$sorting_opt] )
			&& (
				! isset($view_array[$sorting_opt])
				|| $_POST[$sorting_opt] != $view_array[$sorting_opt]
			)
		) {
			if ( is_array( $_POST[$sorting_opt] ) ) {
				$_POST[$sorting_opt] = array_map( 'sanitize_text_field', $_POST[$sorting_opt] );
			} else {
				$_POST[$sorting_opt] = sanitize_text_field( $_POST[$sorting_opt] );
			}
			$view_array[$sorting_opt] = $_POST[$sorting_opt];
			$changed = true;
		}
	}
	if ( $changed ) {
		update_post_meta( $_POST["id"], '_wpv_settings', $view_array );
		do_action( 'wpv_action_wpv_save_item', $_POST["id"] );
	}
	$data = array(
		'id' => $_POST["id"],
		'message' => __( 'Ordering saved', 'wpv-views' )
	);
	wp_send_json_success( $data );
}