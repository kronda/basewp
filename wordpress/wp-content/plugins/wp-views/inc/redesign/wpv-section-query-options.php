<?php

/*
* We can enable this to hide the Query Options section
*/

add_filter( 'wpv_sections_query_show_hide', 'wpv_show_hide_query_options', 1, 1 );

function wpv_show_hide_query_options( $sections ) {
	$sections['query-options'] = array(
		'name' => __( 'Query Options', 'wpv-views' ),
	);
	return $sections;
}

add_action( 'view-editor-section-query', 'add_view_query_options', 20, 2 );

function add_view_query_options( $view_settings, $view_id ) {
	$hide = '';
	if (
		isset( $view_settings['sections-show-hide'] ) 
		&& isset( $view_settings['sections-show-hide']['query-options'] ) 
		&& 'off' == $view_settings['sections-show-hide']['query-options']
	) {
		$hide = ' hidden';
	}
	$section_help_pointer = WPV_Admin_Messages::edit_section_help_pointer( 'query_options' );
	?>
	<div class="wpv-setting-container wpv-settings-query-options js-wpv-settings-query-options<?php echo $hide; ?>">
		<div class="wpv-settings-header">
			<h3>
				<?php _e( 'Query Options', 'wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip" 
					data-header="<?php echo esc_attr( $section_help_pointer['title'] ); ?>" 
					data-content="<?php echo esc_attr( $section_help_pointer['content'] ); ?>">
				</i>
			</h3>
		</div>
		<div class="wpv-setting js-wpv-setting">
			<ul class="wpv-query-options wpv-settings-query-type-posts"<?php echo ( $view_settings['query_type'][0] != 'posts' ) ? ' style="display: none;"' : ''; ?>>
				<li>
					<?php $checked = ( isset( $view_settings['post_type_dont_include_current_page'] ) && $view_settings['post_type_dont_include_current_page'] ) ? ' checked="checked"' : '';?>
					<input type="checkbox" id="wpv-settings-post-include-current" class="js-wpv-query-options-post-type-dont" name="_wpv_settings[post_type_dont_include_current_page]" value="1"<?php echo $checked; ?> autocomplete="off" />
					<label for="wpv-settings-post-include-current"><?php _e("Don't include current page in query result", 'wpv-views'); ?></label>
				</li>
			</ul>
			<ul class="wpv-query-options wpv-settings-query-type-taxonomy"<?php echo ( $view_settings['query_type'][0] != 'taxonomy' ) ? ' style="display: none;"' : ''; ?>>
				<li>
					<?php $checked = ( isset( $view_settings['taxonomy_hide_empty'] ) && $view_settings['taxonomy_hide_empty'] ) ? ' checked="checked"' : '';?>
					<input type="checkbox" id="wpv-settings-taxonomy-hide-empty" class="js-wpv-query-options-taxonomy-hide-empty" name="_wpv_settings[taxonomy_hide_empty]" value="1"<?php echo $checked; ?> autocomplete="off" />
					<label for="wpv-settings-taxonomy-hide-empty"><?php _e( "Don't show empty terms", 'wpv-views' ) ?></label>
				</li>
				<li>
					<?php $checked = ( isset( $view_settings['taxonomy_include_non_empty_decendants'] ) && $view_settings['taxonomy_include_non_empty_decendants'] ) ? ' checked="checked"' : '';?>
					<input type="checkbox" id="wpv-settings-taxonomy-non-empty-decendants" class="js-wpv-query-options-taxonomy-non-empty-decendants" name="_wpv_settings[taxonomy_include_non_empty_decendants]" value="1"<?php echo $checked; ?> autocomplete="off" />
					<label for="wpv-settings-taxonomy-non-empty-decendants"><?php _e( 'Include terms that have non-empty descendants', 'wpv-views' ) ?></label>
				</li>
				<li>
					<?php $checked = ( isset( $view_settings['taxonomy_pad_counts'] ) && $view_settings['taxonomy_pad_counts'] ) ? ' checked="checked"' : '';?>
					<input id="wpv-settings-taxonomy-pad-counts" type="checkbox" class="js-wpv-query-options-taxonomy-pad-counts" name="_wpv_settings[taxonomy_pad_counts]" value="1"<?php echo $checked; ?> autocomplete="off" />
					<label for="wpv-settings-taxonomy-pad-counts"><?php _e( 'Include children in the post count', 'wpv-views' ) ?></label>
				</li>
			</ul>
			<ul class="wpv-query-options wpv-settings-query-type-users"<?php echo ( $view_settings['query_type'][0] != 'users' ) ? ' style="display: none;"' : ''; ?>>
				<li>
					<?php $checked = ( isset( $view_settings['users-show-current'] ) && $view_settings['users-show-current'] ) ? ' checked="checked"' : '';?>
					<input type="checkbox" id="wpv-settings-users-show-current" class="js-wpv-query-options-users-show-current" name="_wpv_settings[users-show-current]" value="1"<?php echo $checked; ?> autocomplete="off" />
					<label for="wpv-settings-users-show-current"><?php _e( "Don't show current logged user.", 'wpv-views' ) ?></label>
				</li>
				<?php
				/*
				 * NOTE: in future if users will ask query users from entire network, we can uncomment it.
				 */
				 /*if ( is_multisite() ): ?> 
				<li>
					<?php $checked = ( !isset( $view_settings['users-show-multisite'] ) || 
						( isset( $view_settings['users-show-multisite'] ) && $view_settings['users-show-multisite'] == 'all' ) ) ? ' checked="checked"' : '';?>
					<input type="radio" id="wpv-settings-users-show-multisite" class="js-wpv-query-options-users-show-multisite" 
					name="_wpv_settings[users-show-multisite]" value="all"<?php echo $checked; ?> />
					<label for="wpv-settings-users-show-multisite"><?php _e( "Load all users from the multisite network.", 'wpv-views' ) ?></label>
				</li>
				<li>
					<?php $checked = ( isset( $view_settings['users-show-multisite'] ) && $view_settings['users-show-multisite'] == 'current' ) ? ' checked="checked"' : '';?>
					<input type="radio" id="wpv-settings-users-show-multisite2" class="js-wpv-query-options-users-show-multisite" 
					name="_wpv_settings[users-show-multisite]" value="current"<?php echo $checked; ?> />
					<label for="wpv-settings-users-show-multisite2"><?php _e( "Load only users from the child site in the network.", 'wpv-views' ) ?></label>
				</li>
				
				<?php endif; */?>
			</ul>
		</div>
		<span class="update-action-wrap auto-update js-wpv-update-action-wrap">
			<span class="js-wpv-message-container"></span>
			<input type="hidden" data-success="<?php echo esc_attr( __('Updated', 'wpv-views') ); ?>" data-unsaved="<?php echo esc_attr( __('Not saved', 'wpv-views') ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_query_options_nonce' ); ?>" class="js-wpv-query-options-update" />
		</span>
	</div>
<?php }

// Query options save callback function - only for Views

add_action( 'wp_ajax_wpv_update_query_options', 'wpv_update_query_options_callback' );

function wpv_update_query_options_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		$data = array(
			'type' => 'capability',
			'message' => __( 'You do not have permissions for that.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	if ( 
		! isset( $_POST["wpnonce"] )
		|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_query_options_nonce' ) 
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
	$query_options = array(
		'post_type_dont_include_current_page',
		'taxonomy_hide_empty',
		'taxonomy_include_non_empty_decendants',
		'taxonomy_pad_counts'
	);
	foreach ( $query_options as $query_opt ) {
		if (
			isset( $_POST[$query_opt] )
			&& (
				! isset( $view_array[$query_opt] ) 
				|| $_POST[$query_opt] != $view_array[$query_opt]
			)
		) {
			if ( is_array( $_POST[$query_opt] ) ) {
				$_POST[$query_opt] = array_map( 'sanitize_text_field', $_POST[$query_opt] );
			} else {
				$_POST[$query_opt] = sanitize_text_field( $_POST[$query_opt] );
			}
			$view_array[$query_opt] = $_POST[$query_opt];
			$changed = true;
		}
	}
	if (
		isset( $_POST["uhide"] )
		&& (
			! isset( $view_array['users-show-current'] ) 
			|| $_POST["uhide"] != $view_array['users-show-current']
		) 
	) {
		$view_array['users-show-current'] = sanitize_text_field( $_POST["uhide"] );
		$changed = true;
	}
	/*if (!isset($view_array['users-show-multisite']) || $_POST["smulti"] != $view_array['users-show-multisite']) {
		$view_array['users-show-multisite'] = $_POST["smulti"];
		$changed = true;
	}*/
	if ( $changed ) {
		update_post_meta( $_POST["id"], '_wpv_settings', $view_array );
		do_action( 'wpv_action_wpv_save_item', $_POST["id"] );
	}
	$data = array(
		'id' => $_POST["id"],
		'message' => __( 'Query Options saved', 'wpv-views' )
	);
	wp_send_json_success( $data );
}