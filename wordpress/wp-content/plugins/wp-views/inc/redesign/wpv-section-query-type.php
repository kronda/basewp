<?php

/**
* wpv_show_hide_content_selector
*
* We can enable this to hide the Content Selection section
*
* @param $sections (array) sections on the editor screen
*
* @return $sections
*
* @since unknown
*/

// add_filter('wpv_sections_query_show_hide', 'wpv_show_hide_content_selector', 1,1);

function wpv_show_hide_content_selector( $sections ) {
	$sections['content-selection'] = array(
		'name' => __( 'Content Selection', 'wpv-views' ),
	);
	return $sections;
}

/**
* add_view_content_selection
*
* Creates the Content Selection section in the edit screen
*
* @param $view_settings
* @param $view_id
*
* @since unknown
*/

add_action( 'view-editor-section-query', 'add_view_content_selection_section', 10, 2 );

function add_view_content_selection_section( $view_settings, $view_id ) {
	$hide = '';
	if (
		isset( $view_settings['sections-show-hide'] ) 
		&& isset( $view_settings['sections-show-hide']['content-selection'] ) 
		&& 'off' == $view_settings['sections-show-hide']['content-selection']
	) {
		$hide = ' hidden';
	}
	$section_help_pointer = WPV_Admin_Messages::edit_section_help_pointer( 'content_section' );
	?>
	<div class="wpv-setting-container wpv-settings-content-selection js-wpv-no-lock js-wpv-settings-content-selection<?php echo $hide; ?>">
		<div class="wpv-settings-header">
			<h3>
				<?php _e('Content Selection', 'wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip" 
					data-header="<?php echo esc_attr( $section_help_pointer['title'] ); ?>" 
					data-content="<?php echo esc_attr( $section_help_pointer['content'] ); ?>">
				</i>
			</h3>
		</div>
		<div class="wpv-setting js-wpv-setting">
			<ul>
				<?php 
				if ( ! isset( $view_settings['query_type'] ) ) {
					$view_settings['query_type'][0] = 'posts';
				}
				?>
				<li style="margin-bottom:20px">
					<?php _e('This View will display:', 'wpv-views'); ?>
					<input type="radio" style="margin-left:15px" name="_wpv_settings[query_type][]" id="wpv-settings-cs-query-type-posts" class="js-wpv-query-type" value="posts" <?php checked( $view_settings['query_type'][0], 'posts' ); ?> autocomplete="off" /><label for="wpv-settings-cs-query-type-posts"><?php _e('Post types','wpv-views') ?></label>
					<input type="radio" style="margin-left:15px" name="_wpv_settings[query_type][]" id="wpv-settings-cs-query-type-taxonomy" class="js-wpv-query-type" value="taxonomy"<?php checked( $view_settings['query_type'][0], 'taxonomy' ); ?> autocomplete="off" /><label for="wpv-settings-cs-query-type-taxonomy"><?php _e('Taxonomy','wpv-views') ?></label>
					<input type="radio" style="margin-left:15px" name="_wpv_settings[query_type][]" id="wpv-settings-cs-query-type-users" class="js-wpv-query-type" value="users"<?php checked( $view_settings['query_type'][0], 'users' ); ?> autocomplete="off" /><label for="wpv-settings-cs-query-type-users"><?php _e('Users','wpv-views') ?></label>
				</li>
				<li>
					<ul class="js-wpv-settings-query-type-posts wpv-settings-query-type-posts wpv-advanced-setting wpv-mightlong-list<?php echo ( $view_settings['query_type'][0] != 'posts' ) ? ' hidden' : ''; ?>">
						<?php
						// Store children post types in an array
						$relationships = get_option( 'wpcf_post_relationship', array() );
						$types_children = array();
						if ( is_array( $relationships ) ) {
							foreach ( $relationships as $has => $belongs ) {
								$types_children = array_merge( $types_children, array_keys( $belongs ) );
							}
						}
						$post_types = get_post_types( array( 'public' => true ), 'objects' );
						if ( ! isset( $view_settings['post_type'] ) ) {
							$view_settings['post_type'] = array();
						}
						foreach ( $view_settings['post_type'] as $type ) {
							if ( ! isset( $post_types[$type] ) ) {
								unset( $view_settings['post_type'][$type] );
							}
						}
						foreach ( $post_types as $p ) {
						?>
							<li><!-- review the use of $p->name here -->
								<?php
								$checked = in_array( $p->name, $view_settings['post_type'] ) ? ' checked="checked"' : '';
								$is_types_child = in_array( $p->name, $types_children ) ? 'yes' : 'no';
								$is_hierarchical = $p->hierarchical ? 'yes' : 'no';
								?>
								<input type="checkbox" id="wpv-settings-post-type-<?php echo esc_attr( $p->name ); ?>" name="_wpv_settings[post_type][]" data-typeschild="<?php echo esc_attr( $is_types_child ); ?>" data-hierarchical="<?php echo esc_attr( $is_hierarchical ); ?>" class="js-wpv-query-post-type" value="<?php echo esc_attr( $p->name ); ?>"<?php echo $checked; ?> autocomplete="off" />
								<label for="wpv-settings-post-type-<?php echo esc_attr( $p->name ); ?>"><?php echo $p->labels->name ?></label>
							</li>
						<?php 
						}
						?>
					</ul>
					<ul class="wpv-settings-query-type-taxonomy wpv-advanced-setting wpv-mightlong-list<?php echo ( $view_settings['query_type'][0] != 'taxonomy' ) ? ' hidden' : ''; ?>">
						<?php $taxonomies = get_taxonomies( '', 'objects' );
						$exclude_tax_slugs = array();
						$exclude_tax_slugs = apply_filters( 'wpv_admin_exclude_tax_slugs', $exclude_tax_slugs );
						if ( ! isset( $view_settings['taxonomy_type'] ) ) {
							$view_settings['taxonomy_type']= array();
						}
						foreach ( $view_settings['taxonomy_type'] as $type ) {
							if ( ! isset( $taxonomies[$type] ) ) {
								unset( $view_settings['taxonomy_type'][$type] );
								}
						}
						?>
						<?php foreach ( $taxonomies as $tax_slug => $tax ) { ?>
							<?php
							if ( in_array( $tax_slug, $exclude_tax_slugs ) ) {
								continue; // Take out taxonomies that are in our compatibility black list
							}
							if ( ! $tax->show_ui ) {
								continue; // Only show taxonomies with show_ui set to TRUE
							}
							?>
							<?php 
							if ( sizeof( $view_settings['taxonomy_type'] ) == 0 ) { // we need to check at least the first available taxonomy if no one is set
								$view_settings['taxonomy_type'][] = $tax->name;
							}
							$checked = @in_array( $tax->name, $view_settings['taxonomy_type'] ) ? ' checked="checked"' : '';
							$is_tax_hierarchical = $tax->hierarchical ? 'yes' : 'no';
							?>
							<li>
								<input type="radio" id="wpv-settings-post-taxonomy-<?php echo esc_attr( $tax->name ); ?>" name="_wpv_settings[taxonomy_type][]" data-hierarchical="<?php echo esc_attr( $is_tax_hierarchical ); ?>" class="js-wpv-query-taxonomy-type" value="<?php echo esc_attr( $tax->name ); ?>"<?php echo $checked; ?> autocomplete="off" />
								<label for="wpv-settings-post-taxonomy-<?php echo esc_attr( $tax->name ); ?>"><?php echo $tax->labels->name ?></label>
							</li>
						<?php } ?>
					</ul>
					<ul class="wpv-settings-query-type-users wpv-advanced-setting wpv-mightlong-list<?php echo ( $view_settings['query_type'][0] != 'users' ) ? ' hidden' : ''; ?>">
						<?php global $wp_roles;
						if ( ! isset( $view_settings['roles_type'] ) ) {
							$view_settings['roles_type']= array('administrator');
						}
						foreach( $wp_roles->role_names as $role => $name ) { ?>
							<?php 
							$checked = @in_array( $role, $view_settings['roles_type'] ) ? ' checked="checked"' : ''; ?>
						<li>
							<input type="radio" id="wpv-settings-post-users-<?php echo esc_attr( $role ); ?>" name="_wpv_settings[roles_type][]" class="js-wpv-query-users-type" value="<?php echo esc_attr( $role ); ?>"<?php echo $checked; ?> autocomplete="off" />
							<label for="wpv-settings-post-users-<?php echo esc_attr( $role ); ?>"><?php echo $name; ?></label>
						</li>
						<?php } ?>
						<li>
							<?php $checked = @in_array( 'any', $view_settings['roles_type'] ) ? ' checked="checked"' : ''; ?>
							<input type="radio" id="wpv-settings-post-users-any-role" name="_wpv_settings[roles_type][]" class="js-wpv-query-users-type" value="any"<?php echo $checked; ?> autocomplete="off" />
							<label for="wpv-settings-post-users-any-role"><?php _e( 'Any role', 'wpv-views' ); ?></label>
						</li>
					</ul>
				</li>
			</ul>
			<?php
			$multi_post_relations = wpv_recursive_post_hierarchy( $view_settings['post_type'] );
			$flatten_post_relations = wpv_recursive_flatten_post_relationships( $multi_post_relations );
			$relations_tree = wpv_get_all_post_relationship_options( $flatten_post_relations );
			$flatten_relations_tree = implode( ',', $relations_tree );
			?>
			<input type="hidden" class="js-flatten-types-relation-tree" value="<?php echo $flatten_relations_tree; ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_dependant_parametric_search' ); ?>" autocomplete="off" />
		</div>
		<span class="update-action-wrap auto-update js-wpv-content-section-action-wrap js-wpv-update-action-wrap">
			<span class="js-wpv-message-container"></span>
			<input type="hidden" data-success="<?php echo esc_attr( __('Updated', 'wpv-views') ); ?>" data-unsaved="<?php echo esc_attr( __('Not saved', 'wpv-views') ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_query_type_nonce' ); ?>" class="js-wpv-query-type-update" />
		</span>
	</div>
	<div class="toolset-alert toolset-alert-lock js-wpv-content-selection-mandatory-warning hidden">
		<p>
			<?php _e( 'You need to select what content to load with this View before you can continue designing the output.', 'wpv-views' ); ?>
		</p>
	</div>
<?php }

// Query type save callback function - only for Views

add_action( 'wp_ajax_wpv_update_query_type', 'wpv_update_query_type_callback' );

function wpv_update_query_type_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		$data = array(
			'type' => 'capability',
			'message' => __( 'You do not have permissions for that.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	if ( 
		! isset( $_POST["wpnonce"] )
		|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_query_type_nonce' ) 
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
	$query_type_changed = false;
	if ( ! isset( $_POST["post_type_slugs"] ) ) {
		$_POST["post_type_slugs"] = array( 'any' );
	}
	$view_array = get_post_meta( $_POST["id"],'_wpv_settings', true );
	if (
		! isset( $view_array['query_type'] ) 
		|| ! isset( $view_array['query_type'][0] ) 
		|| $view_array['query_type'][0] != $_POST["query_type"]
	) {
		$view_array['query_type'] = array( sanitize_text_field( $_POST["query_type"] ) );
		$changed = true;
		$query_type_changed = true;
	}
	// Note that the POSTed data comes with extended keys: _slugs
	// We do that to avoid POST['post_type'] entries
	$content_type = array( 'post_type', 'taxonomy_type', 'roles_type' );
	foreach ( $content_type as $cont_type ) {
		if (
			isset( $_POST[$cont_type . '_slugs'] )
			&& (
				! isset( $view_array[$cont_type] ) 
				|| $view_array[$cont_type] != $_POST[$cont_type . '_slugs']
			)
		) {
			if ( is_array( $_POST[$cont_type . '_slugs'] ) ) {
				$_POST[$cont_type . '_slugs'] = array_map( 'sanitize_text_field', $_POST[$cont_type . '_slugs'] );
			} else {
				$_POST[$cont_type . '_slugs'] = sanitize_text_field( $_POST[$cont_type . '_slugs'] );
			}
			$view_array[$cont_type] = $_POST[$cont_type . '_slugs'];
			$changed = true;
		}
	}
	if ( $changed ) {
		update_post_meta( $_POST["id"], '_wpv_settings', $view_array );
		do_action( 'wpv_action_wpv_save_item', $_POST["id"] );
	}
	// Filters list
	if ( $query_type_changed ) {
		$filters_list = '';
		ob_start();
		wpv_display_filters_list( $view_array['query_type'][0], $view_array );
		$filters_list = ob_get_contents();
		ob_end_clean();
	} else {
		$filters_list = 'no_change';
	}
	// Flatten Types post relationship
	$returned_post_types = $view_array['post_type'];
	$multi_post_relations = wpv_recursive_post_hierarchy( $returned_post_types );
	$flatten_post_relations = wpv_recursive_flatten_post_relationships( $multi_post_relations );
	if ( strlen( $flatten_post_relations ) > 0 ) {
		$relations_tree = wpv_get_all_post_relationship_options( $flatten_post_relations );
		$flatten_types_relationship_tree = implode( ',', $relations_tree );
	} else {
		$flatten_types_relationship_tree = 'NONE';
	}
	$data = array(
		'id' => $_POST["id"],
		'updated_filters_list' => $filters_list,
		'updated_flatten_types_relationship_tree' => $flatten_types_relationship_tree,
		'message' => __( 'Content Selection saved', 'wpv-views' )
	);
	wp_send_json_success( $data );
}


