<?php

add_action( 'view-editor-section-hidden', 'wpv_loop_wizard_add_data_to_js', 10, 4 );

function wpv_loop_wizard_add_data_to_js( $view_settings, $view_layout_settings, $view_id, $user_id ) {
	$loop_wizard_saved_settings = wpv_layout_wizard_load_saved_settings( $view_id );
	ob_start();
    require_once( WPV_PATH . '/inc/redesign/templates/wpv-layout-edit-wizard.tpl.php' );
	$dialog = ob_get_clean();
	?>
	<!--
	<div class="popup-window-container">
		<?php
		// require_once( WPV_PATH . '/inc/redesign/templates/wpv-layout-edit-wizard.tpl.php' );
		?>
	</div>
	-->
	<script type="text/javascript">
		var WPViews = WPViews || {};
		WPViews.layout_wizard_saved_settings = <?php echo json_encode( $loop_wizard_saved_settings ); ?>;
		WPViews.layout_wizard_saved_dialog = <?php echo json_encode( $dialog ); ?>;
	</script>
	<?php
}

function wpv_loop_wizard_get_available_field_menus( $view_id ) {
	global $WP_Views;
	$view_settings = $WP_Views->get_view_settings( $view_id );
	
	static $menus_for_what = array();
	
	$menus_for = 'posts';
	if ( 
		isset( $view_settings['query_type'][0] ) 
		&& $view_settings['query_type'][0] == 'taxonomy' 
	) {
		$menus_for = 'taxonomy';
	} else if (
		isset( $view_settings['query_type'][0] ) 
		&& $view_settings['query_type'][0] == 'users' 
	) {
		$menus_for = 'users';
	}
	
	if ( isset( $menus_for_what[$menus_for] ) ) {
		return $menus_for_what[$menus_for];
	}
	
	$menus = array();
	$menus_entries = array();
	
	switch ( $menus_for ) {
		case 'taxonomy':
		case 'users':
			$menus_entries = array(
				'post-view', 
				'taxonomy-view', 
				'user-view'
			);
			break;
		default:
			$menus_entries = array(
				'post',
				'post-fields-grouped',
				'post-view', 
				'user', 
				'taxonomy-view', 
				'user-view',
				'body-view-templates'
			);
			break;
	}
	
	$menus_entries[] = 'loop-wizard-for-' . $menus_for;

	$WP_Views->editor_addon = new WPV_Editor_addon(
		'wpv-views',
		__('Insert Views Shortcodes', 'wpv-views'),
		WPV_URL . '/res/js/views_editor_plugin.js',
		WPV_URL_EMBEDDED . '/res/img/views-icon-black_16X16.png'
	);

	add_short_codes_to_js( 
		$menus_entries, 
		$WP_Views->editor_addon 
	);

	$fields_list = $WP_Views->editor_addon->get_fields_list();
	
	if ( $fields_list ) {
		foreach ( $fields_list as $item ) {
			if ( ! isset( $menus[$item[2]] ) ) {
				$menus[$item[2]] = array();
			}
			$menus[$item[2]][$item[0]] = $item;
		}
	}

	if ( $menus_for == 'posts' ) {
		do_action( 'wpv_action_wpv_add_types_postmeta_usermeta_to_editor_menus' );
		// Backwards compatibility - this is available as of now, before a Types update
		// @todo remove this once Types gets an update
		do_action( 'views_ct_inline_editor' );
	} else if ( $menus_for == 'taxonomy' ) {
		remove_filter( 'editor_addon_menus_wpv-views', 'wpv_post_taxonomies_editor_addon_menus_wpv_views_filter', 11 );
		add_filter( 'editor_addon_menus_wpv-views', 'wpv_layout_taxonomy_V', 30 );
	} else if ( $menus_for == 'users' ) {
		remove_filter( 'editor_addon_menus_wpv-views', 'wpv_post_taxonomies_editor_addon_menus_wpv_views_filter', 11 );
		add_filter( 'editor_addon_menus_wpv-views', 'wpv_layout_users_V', 30 );
	}
	
	// Apply filters
	$menus = apply_filters( 'editor_addon_menus_wpv-views', $menus );
	// Sort menus
	if ( is_array( $menus ) ) {
		$menus = $WP_Views->editor_addon->sort_menus( $menus );
	}
	
	if ( $menus_for == 'taxonomy' ) {
		remove_filter( 'editor_addon_menus_wpv-views', 'wpv_layout_taxonomy_V', 30 );
		add_filter( 'editor_addon_menus_wpv-views', 'wpv_post_taxonomies_editor_addon_menus_wpv_views_filter', 11 );
	} else if ( $menus_for == 'users' ) {
		remove_filter( 'editor_addon_menus_wpv-views', 'wpv_layout_users_V', 30 );
		add_filter( 'editor_addon_menus_wpv-views', 'wpv_post_taxonomies_editor_addon_menus_wpv_views_filter', 11 );
	}
	
	$menus_for_what[$menus_for] = $menus;
	
	return $menus_for_what[$menus_for];
}

add_action( 'wp_ajax_wpv_loop_wizard_load_saved_fields', 'wpv_loop_wizard_load_saved_fields' );

function wpv_loop_wizard_load_saved_fields() {
	wpv_ajax_authenticate( 'wpv_loop_wizard_nonce', array( 'parameter_source' => 'post', 'type_of_death' => 'data' ) );
	
	if (
		! isset( $_POST["view_id"] )
		|| ! is_numeric( $_POST["view_id"] )
		|| intval( $_POST['view_id'] ) < 1 
	) {
		$data = array(
			'type' => 'id',
			'message' => __( 'Wrong or missing View ID.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	
	$view_id = intval( $_POST['view_id'] );
	
	if ( ! isset( $_POST['selected_fields'] ) ) {
		$data = array(
			'type' => 'data',
			'message' => __( 'Wrong or missing data for selected fields.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	} else if ( 
		! is_array( $_POST['selected_fields'] ) 
		|| count( $_POST['selected_fields'] ) == 0
	) {
		$data = array(
			'type' => 'data',
			'message' => __( 'Wrong or missing data for selected fields.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	} else {
		$pattern = get_shortcode_regex();
		$menus = wpv_loop_wizard_get_available_field_menus( $view_id );
		
		global $WP_Views;
		$view_settings = $WP_Views->get_view_settings( $view_id );
		$menus_for = 'posts';
		if ( 
			isset( $view_settings['query_type'][0] ) 
			&& $view_settings['query_type'][0] == 'taxonomy' 
		) {
			$menus_for = 'taxonomy';
		} else if (
			isset( $view_settings['query_type'][0] ) 
			&& $view_settings['query_type'][0] == 'users' 
		) {
			$menus_for = 'users';
		}
		
		$views_shortcodes_with_api_obj = apply_filters( 'wpv_filter_wpv_shortcodes_gui_data', array() );
		$views_shortcodes_with_api = array_keys( $views_shortcodes_with_api_obj );
		$user_fields_with_head = array(
			'user_email', 'display_name', 'user_login', 'user_url', 'user_registered'
		);
		$selected_fields = $_POST['selected_fields'];
		$result_html = '';
		foreach ( $selected_fields as $key => $shortcode_selected ) {

			$shortcode_selected_unslashed = stripslashes( $shortcode_selected );
			$shortcode_selected_handle = '';
			$shortcode_selected_attributes = '';
			if ( 0 !== preg_match( "/$pattern/s", $shortcode_selected_unslashed, $shortcode_selected_data ) ) {
				$shortcode_selected_handle = $shortcode_selected_data[2];
				$shortcode_selected_attributes = shortcode_parse_atts( $shortcode_selected_data[3] );
			}
			$shortcode_selected_is_ct = false;
			$shortcode_selected_ct_selected = '';
			$shortcode_selected_is_types_field = false;
			$shortcode_selected_is_types_userfield = false;
			$shortcode_selected_types_name = '';
			$shortcode_selected_has_views_attributes_gui = false;
			$selected_views_attributes_gui = '';
			if (
				in_array( $shortcode_selected_handle, $views_shortcodes_with_api ) 
				&& ! in_array( $shortcode_selected_handle, array( 'wpv-post-body', 'wpv-post-field' ) ) 
			) {
				$shortcode_selected_has_views_attributes_gui = true;
			}
			$shortcode_selected_found = false;
	
			// Check whether the selected item is a CT one
			if ( 'wpv-post-body' == $shortcode_selected_handle ) {
				$shortcode_selected_is_ct = true;
				if ( 
					isset( $shortcode_selected_attributes['view_template'] ) 
					&& 'None' != $shortcode_selected_attributes['view_template'] 
					&& ! empty( $shortcode_selected_attributes['view_template']  )
				) {
					$shortcode_selected_ct_selected = $shortcode_selected_attributes['view_template'];
					global $wpdb;
					$selected_c_template_alt = $wpdb->get_var(
						$wpdb->prepare(
							"SELECT post_title FROM {$wpdb->posts} 
							WHERE post_name = %s
							LIMIT 1",
							$shortcode_selected_ct_selected
						)
					);
					if ( $selected_c_template_alt ) {
						$shortcode_selected_ct_selected = $selected_c_template_alt;
					}
				} else {
					$shortcode_selected_ct_selected = 'None';
				}
			}
	
			// Check whether this is a Types field, because we would need its name
			if ( 'types' == $shortcode_selected_handle ) {
				if ( isset( $shortcode_selected_attributes['field'] ) ) {
					$shortcode_selected_is_types_field = true;
					$shortcode_selected_types_name = $shortcode_selected_attributes['field'];
				} else if ( isset( $shortcode_selected_attributes['usermeta'] ) ) {
					$shortcode_selected_is_types_userfield = true;
					$shortcode_selected_types_name = $shortcode_selected_attributes['usermeta'];
				}
			}
			
			// Start the output for this field
			ob_start();
			?>
			<div id="layout-wizard-style_<?php echo $key; ?>" class="wpv-loop-wizard-item-container js-wpv-loop-wizard-item-container">
				<i class="icon-move js-layout-wizard-move-field"></i>
				<select name="layout-wizard-style" class="wpv-layout-wizard-item js-wpv-select2 js-wpv-layout-wizard-item js-layout-wizard-item">
				<?php
				foreach ( $menus as $group_title => $group_items ) {
					?>
					<optgroup label="<?php echo $group_title; ?>">
					<?php foreach ( $group_items as $current_item_title => $current_item ) {
						if ( 'css' == $current_item_title ) {
							// For some reason Types field bleed this css entry everywhere! Related to the editor_addon_menus_ filter
							continue;
						}
						$current_shortcode_is_selected = false;
						$current_shortcode_name = $current_item[0];
						list($current_shortcode_handle) = explode( ' ', $current_item[1] );
						$current_shortcode_handle_corrected = $current_shortcode_handle; // needed to separate wpv-post-body from CT entries
						$current_shortcode_to_insert = $current_item[1];
						$current_shortcode_head = ''; // for table layouts
						$current_shortcode_is_types = false;
						$current_shortcode_types_name = '';
						
						if (
							'wpv-post-body' == $current_shortcode_handle 
							&& __( 'Basic', 'wpv-views' ) == $group_title
						) {
							$current_shortcode_to_insert = 'wpv-post-body view_template="None"';
						}
					
						// First, check if the selected value matches a shortcode handler directly
						if ( 
							esc_sql( $shortcode_selected ) == '[' . $current_shortcode_to_insert . ']' 
							|| substr( $shortcode_selected_unslashed , 1, -1 ) == stripslashes( $current_shortcode_to_insert )
						) {
							$current_shortcode_is_selected = true;
						}

						// Check wpv-post-taxonomy shortcodes: the current item shortcode handle contains wpv-post-taxonomy and matches the selected one, de-bracket-ed
						if ( 
							! $current_shortcode_is_selected 
							&& 'wpv-post-taxonomy' == $current_shortcode_handle 
							&& 'wpv-post-taxonomy' == $shortcode_selected_handle
							&& strpos( $current_shortcode_to_insert, 'type="' . $shortcode_selected_attributes['type'] . '"' ) !== false
						) {
							$current_shortcode_is_selected = true;
							$current_shortcode_to_insert = trim( $shortcode_selected_unslashed, '[]');
						}
					
						// Check wpv-view shortcodes: the current item shortcode handle contains wpv-views and matches the selected one, de-bracket-ed
						if ( 
							! $current_shortcode_is_selected 
							&& 'wpv-view' == $current_shortcode_handle 
							&& 'wpv-view' == $shortcode_selected_handle
							&& trim( $shortcode_selected_unslashed, '[]') == $current_shortcode_to_insert 
						) {
							$current_shortcode_is_selected = true;
						}
					
						if (
							! $current_shortcode_is_selected 
							&& $shortcode_selected_is_ct
							&& 'wpv-post-body' == $current_shortcode_handle 
						) {
							if (
								'None' == $shortcode_selected_ct_selected
								&& __( 'Basic', 'wpv-views' ) == $group_title
							) {
								$current_shortcode_is_selected = true;
							} else if (
								strpos( $current_shortcode_to_insert, 'view_template="' . $shortcode_selected_ct_selected . '"' ) !== false
								&& __('Content Template', 'wpv-views') == $group_title
							) {
								$current_shortcode_is_selected = true;
							}
						}
					
						if (
							! $current_shortcode_is_selected 
							&& 'types' == $current_shortcode_handle 
						) {
							if ( 
								$shortcode_selected_is_types_field 
								&& preg_match( '/field="(.*?)"/', $current_shortcode_to_insert, $field_in_loop ) !== 0 
								&& $field_in_loop[1] == $shortcode_selected_types_name
							) {
								$current_shortcode_is_selected = true;
							} else if ( 
								$shortcode_selected_is_types_userfield 
								&& preg_match( '/usermeta="(.*?)"/', $current_shortcode_to_insert, $field_in_loop ) !== 0 
								&& $field_in_loop[1] == $shortcode_selected_types_name
							) {
								$current_shortcode_is_selected = true;
							}
						}
					
						if (
							! $current_shortcode_is_selected 
							&& ! in_array( $shortcode_selected_handle, array( 'wpv-view', 'wpv-post-body', 'wpv-post-field', 'wpv-post-taxonomy', 'types', 'wpv-user' ) ) // watch out generic shortcodes with multiple uses
							&& $current_shortcode_handle == $shortcode_selected_handle
						) {
							$current_shortcode_is_selected = true;
							$current_shortcode_to_insert = trim( $shortcode_selected_unslashed, '[]');
						}
					
						// Manage fields added on Types but now Types is not here - what for...
						// @todo
					
						// Head value for table layouts
						if ( $current_shortcode_handle === "wpv-post-taxonomy" ) {
							$current_shortcode_head = 'wpv-post-taxonomy';
							$current_shortcode_head = '';
						} else if ( $current_shortcode_handle === "wpv-post-field" ) {
							$current_shortcode_handle_corrected = $current_shortcode_handle . '_corrected';
							$current_shortcode_head = 'post-field-' . $current_shortcode_name;
							// if it is a new WooCommerce Views field
							if ( preg_match('/name="views_woo(.*?)"/', $current_shortcode_to_insert, $woo_match) ) {
								$current_shortcode_head = 'post-field-views_woo' . $woo_match[1];
							}
						} else if ( substr( $current_shortcode_to_insert, 0, 8 ) === "wpv-post" ) {
							if (
								'wpv-post-body' == $current_shortcode_handle
								&& __( 'Basic', 'wpv-views' ) != $group_title
							) {
								$current_shortcode_handle_corrected = $current_shortcode_handle . '_corrected';
							}
							$current_shortcode_head = substr( $current_shortcode_handle, 4 );
							if ( 
								$current_shortcode_handle === "wpv-post-status" 
								|| $current_shortcode_handle === "wpv-post-class" 
								|| $current_shortcode_handle === "wpv-post-body" 
								|| $current_shortcode_handle === "wpv-post-featured-image" 
							) {
								$current_shortcode_head = '';
							}
						} else if ( $current_shortcode_handle === "wpv-view" ) {
							$current_shortcode_head = 'post-view';
							$current_shortcode_head = '';
						} else if ( $current_shortcode_handle === "types" ) {
							if ( preg_match( '/field="(.*?)"/', $current_shortcode_to_insert, $field_in_loop ) !== 0 ) {
								$current_shortcode_head = 'types-field-' . $field_in_loop[1];
								$current_shortcode_is_types = true;
								$current_shortcode_types_name = $field_in_loop[1];
							} else if ( preg_match( '/usermeta="(.*?)"/', $current_shortcode_to_insert, $field_in_loop ) !== 0 ) {
								$current_shortcode_head = '';
								$current_shortcode_is_types = true;
								$current_shortcode_types_name = $field_in_loop[1];
							}
						} else if ( substr( $current_shortcode_to_insert, 0, 12 ) === "wpv-taxonomy" ) { // heading table solumns for wpv-taxonomy-* shortcodes
							if ( in_array( $current_shortcode_handle, array( 'wpv-taxonomy-link', 'wpv-taxonomy-title' ) ) ) {
								$current_shortcode_head = substr( $current_shortcode_handle, 4 );
							}
							if ( $current_shortcode_handle == 'wpv-taxonomy-post-count' ) {
								$current_shortcode_head = 'taxonomy-post_count';
							}
						} else if ( $current_shortcode_handle === "wpv-user" ) { // heaading table columns for wpv-user shortcodes
							preg_match( '/field="(.*?)"/', $current_shortcode_to_insert, $view_user_field );
							if ( 
								isset( $view_user_field[1] ) 
								&& in_array( $view_user_field[1], $user_fields_with_head ) 
							) {
								$current_shortcode_head = $view_user_field[1];
							} else {
								$current_shortcode_head = '';
							}
						}
					
						if ( $current_shortcode_is_selected ) {
							$shortcode_selected_found = true;
						}
						?>
						<option value="<?php echo base64_encode('['.$current_shortcode_to_insert.']'); ?>" 
							data-shortcodehandle="<?php echo esc_attr( $current_shortcode_handle_corrected ); ?>" 
							data-fieldname="<?php echo esc_attr( $current_shortcode_name ); ?>"
							data-headename="<?php echo esc_attr( $current_shortcode_head ); ?>" 
							data-rowtitle="<?php echo esc_attr( $current_shortcode_name ); ?>" 
							data-istype="<?php echo $current_shortcode_is_types ? '1' : '0'; ?>" 
							data-typename="<?php echo esc_attr( $current_shortcode_types_name ); ?>" 
							data-hasattributesgui="<?php echo ( in_array( $current_shortcode_handle_corrected, $views_shortcodes_with_api ) && ! in_array( $current_shortcode_handle_corrected, array( 'wpv-post-body', 'wpv-post-field' ) ) ) ? '1' : '0'; ?>" 
							data-onclick="<?php echo isset( $current_item[3] ) ? $current_item[3] : ''; ?>" 
							<?php 
							if ( $current_shortcode_is_selected ) {
								echo ' selected="selected"';
								$selected_views_attributes_gui = ( $shortcode_selected_has_views_attributes_gui && isset( $current_item[3] ) ) ? $current_item[3] : '';
							} 
							?>>
							<?php echo $current_shortcode_name; ?>
						</option>
					<?php } ?>
					</optgroup>
				<?php } ?>
				</select>
				<p class="wpv-helper-text js-layout-wizard-body-template-text <?php if ( ! ( 'wpv-post-body' == $shortcode_selected_handle && 'None' == $shortcode_selected_ct_selected ) ) { ?>hidden<?php } ?>" style="margin-left: 33px;">
					<?php echo __('Using the Content Template', 'wpv-views'); ?>
					<select name="layout-wizard-body-template" class="layout-wizard-body-template js-wpv-layout-wizard-body-template">
						<option value="<?php echo base64_encode( '[wpv-post-body view_template="None"]' ); ?>" data-rowtitle="<?php echo esc_attr( __( 'None', 'wpv-views' ) ); ?>" <?php if ('None' == $shortcode_selected_ct_selected) echo 'selected="selected"' ?> ><?php _e( 'None', 'wpv-views' ); ?></option>
						<?php
						if ( isset( $menus[__('Content Template', 'wpv-views')] ) ) {
							foreach ( $menus[__('Content Template', 'wpv-views')] as $ct_item ) { ?>
								<option value="<?php echo base64_encode('['.$ct_item[1].']'); ?>" data-rowtitle="<?php echo $ct_item[0]; ?>" <?php if (trim($ct_item[0])==$shortcode_selected_ct_selected) echo 'selected="selected"' ?> > <?php echo $ct_item[0]; ?></option>
							<?php 
							}
						}
						?>
					</select>
				</p>
				<button class="button-secondary js-custom-types-fields" 
				<?php 
				if ( 
					! ( 
						$shortcode_selected_is_types_field 
						|| $shortcode_selected_is_types_userfield 
					) 
				) { 
					?> style="display: none" <?php 
				} else {
					?>  rel="<?php echo $shortcode_selected_types_name; ?>" <?php 
				}
				if ( $menus_for == 'users' ) {
					echo ' data-type="views-usermeta"';
				}
				?>>
					<i class="icon-edit"></i> <?php _e('Edit', 'wpv-views'); ?>
				</button>
			
				<button class="button-secondary js-wpv-loop-wizard-shortcode-ui"
				<?php
				if ( ! $shortcode_selected_has_views_attributes_gui ) {
					echo ' style="display: none" ';
				} else {
					echo ' rel="' . $shortcode_selected_handle . '" ';
					echo ' onclick="' . $selected_views_attributes_gui . '" ';
				}
				$nonce = wp_create_nonce('wpv_editor_callback');
				echo ' data-nonce="' . $nonce . '" ';
				?>>
					<i class="icon-edit"></i> <?php _e('Edit', 'wpv-views'); ?>
				</button>
			
				<button class="button-secondary js-layout-wizard-remove-field" style="position: absolute; top: 5px; right: 5px;"><i class="icon-remove"></i></button>
			</div>
			<?php
			$result_html_candidate = ob_get_clean();
			if ( $shortcode_selected_found ) {
				$result_html .= $result_html_candidate;
			}
		}
		$data = array(
			'html' => $result_html
		);
		wp_send_json_success( $data );
	}
}

/*
* File for Layout Wizard AJAX calls
*/

/*
* Layout Wizard
*/

add_action( 'wp_ajax_wpv_layout_wizard', 'wpv_layout_wizard_callback' );

function wpv_layout_wizard_callback() {
	if (
		! isset( $_POST["view_id"] )
		|| ! is_numeric( $_POST["view_id"] )
		|| intval( $_POST['view_id'] ) < 1 
	) {
		$data = array(
			'type' => 'id',
			'message' => __( 'Wrong or missing ID.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
    ob_start();
    require_once( WPV_PATH . '/inc/redesign/templates/wpv-layout-edit-wizard.tpl.php' );
	$dialog = ob_get_clean();
	$data = array(
		'dialog' => $dialog,
		'settings' => wpv_layout_wizard_load_saved_settings( $_POST["view_id"] )
	);
	wp_send_json_success( $data );
}

/**
* wpv_layout_wizard_load_saved_settings
*
* Returns the layout settings for a given View, adjusting the Bootstrap ones based on the Views and Layouts settings
*
* @since unknown
*/

function wpv_layout_wizard_load_saved_settings( $view_id ) {
	$view_layout_settings = get_post_meta( $view_id, '_wpv_layout_settings', true);
	if ( class_exists( 'WPDD_Layouts_CSSFrameworkOptions' ) ) {
		$bootstrap_ver = WPDD_Layouts_CSSFrameworkOptions::getInstance()->get_current_framework();
		$view_layout_settings['wpv_bootstrap_version'] = str_replace( 'bootstrap-', '', $bootstrap_ver );
	} else {
		global $WPV_settings;
		$view_layout_settings['wpv_bootstrap_version'] = 1;
		//Load bootstrap version from views settings
		if ( isset( $WPV_settings['wpv_bootstrap_version'] ) ) {
			$view_layout_settings['wpv_bootstrap_version'] = $WPV_settings['wpv_bootstrap_version'];
		}
	}
	if ( 
		isset( $view_layout_settings['fields'] )
		&& is_array( $view_layout_settings['fields'] )
	) {
		$view_layout_settings['fields'] = array_values( $view_layout_settings['fields'] );
	}
	return $view_layout_settings;    
}

/**
* wpv_create_layout_content_template
*
* Create a Loop Template on demand based on Loop Wizard settings
*
* @since unknown
*/

add_action('wp_ajax_wpv_create_layout_content_template', 'wpv_create_layout_content_template');

function wpv_create_layout_content_template() {
	if ( ! current_user_can( 'manage_options' ) ) {
		$data = array(
			'type' => 'capability',
			'message' => __( 'You do not have permissions for that.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	if ( 
		! isset( $_POST["wpnonce"] )
		|| ! wp_verify_nonce( $_POST["wpnonce"], 'layout_wizard_nonce' ) 
	) {
		$data = array(
			'type' => 'nonce',
			'message' => __( 'Your security credentials have expired. Please reload the page to get new ones.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	if (
		! isset( $_POST["view_id"] )
		|| ! is_numeric( $_POST["view_id"] )
		|| intval( $_POST['view_id'] ) < 1 
	) {
		$data = array(
			'type' => 'id',
			'message' => __( 'Wrong or missing ID.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
    $template = wpv_create_content_template( 'Loop item in '. $_POST['view_name'] );
	$view_id = $_POST['view_id'];
    if ( isset( $template['success'] ) ) {
        update_post_meta( $view_id, '_view_loop_template', $template['success'] );
        update_post_meta( $template['success'], '_view_loop_id', $view_id );
        $ct_post_id = $template['success'];    
		$data = array(
			'id' => $view_id,
			'message' => __( 'Content Template for this Loop Output created', 'wpv-views' ),
			'template_id' => $ct_post_id,
			'template_title' => $template['title']			
		);
        $post = get_post( $ct_post_id );
        $meta = get_post_meta( $view_id, '_wpv_layout_settings', true );
        $reg_templates = array();
        if ( isset( $meta['included_ct_ids'] ) ) {
            $reg_templates = explode( ',', $meta['included_ct_ids'] );
        }
        if ( ! in_array( $ct_post_id, $reg_templates ) ) {            
            array_unshift( $reg_templates, $ct_post_id );
            $meta['included_ct_ids'] = implode( ',', $reg_templates );
            update_post_meta( $view_id, '_wpv_layout_settings', $meta );
            ob_start();
            wpv_list_view_ct_item( $post, $ct_post_id, $view_id, true );
            $data['template_html'] = ob_get_clean();
        }
		do_action( 'wpv_action_wpv_save_item', $view_id );
		wp_send_json_success( $data );
    } else {
        $data = array(
			'type' => 'error',
			'message' => __( 'Could not create a Content Template for this Loop Output. Please reload the page and try again.', 'wpv-views' )
		);
		wp_send_json_error( $data );
    }
}


/**
 * Generate layout settings for a View.
 *
 * This is basically just a wrapper for the WPV_View_Base::generate_loop_output() method that handles AJAX stuff.
 * 
 * Expects following POST arguments:
 * - wpnonce: A valid layout_wizard_nonce.
 * - view_id: ID of a View. Used to retrieve current View "_wpv_layout_settings". If ID is invalid or the View doesn't
 *       have these settings, an empty array is used instead.
 * - style: One of the valid Loop Output styles. @see WPV_View_Base::generate_loop_output().
 * - fields: Array of arrays of field attributes (= the fields whose shortcodes should be inserted into loop output).
 *       For historical reason, each field is represented by a non-associative array whose elements have this meaning:
 *       0 - prefix, text before [shortcode]
 *       1 - [shortcode]
 *       2 - suffix, text after [shortcode]
 *       3 - field name
 *       4 - header name
 *       5 - row title <TH>
 *       Note: 0,2 maybe not used since v1.3
 * - args: An array of arguments for WPV_View_Base::generate_loop_output(), encoded as a JSON string.
 *
 * Outputs a JSON-encoded array with following elements:
 * - success: Boolean. If false, the AJAX call has failed and this is the only element present (or making sense).
 * - loop_output_settings: An array with loop output settings (old values merged with new ones). Keys stored in database
 *       and not updated by wpv_generate_view_loop_output() will be preserved.
 * - ct_content: Content of the Content Template to be used in Loop Output, if such exists, or an empty string.
 * 
 * @see WPV_View_Base::generate_loop_output() for detailed information.
 *
 * @since 1.8
 */ 
add_action( 'wp_ajax_wpv_generate_view_loop_output', 'wpv_generate_view_loop_output_callback' );

function wpv_generate_view_loop_output_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		$data = array(
			'type' => 'capability',
			'message' => __( 'You do not have permissions for that.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	if ( 
		! isset( $_POST["wpnonce"] )
		|| ! wp_verify_nonce( $_POST["wpnonce"], 'layout_wizard_nonce' ) 
	) {
		$data = array(
			'type' => 'nonce',
			'message' => __( 'Your security credentials have expired. Please reload the page to get new ones.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	if (
		! isset( $_POST["view_id"] )
		|| ! is_numeric( $_POST["view_id"] )
		|| intval( $_POST['view_id'] ) < 1 
	) {
		$data = array(
			'type' => 'id',
			'message' => __( 'Wrong or missing ID.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}

	$view_id = $_POST['view_id'];
	$style = sanitize_text_field( $_POST['style'] );
	
	// @todo better validation
	$fields = json_decode( stripslashes( $_POST['fields'] ), true );
	$args = json_decode( stripslashes( $_POST['args'] ), true );

    // Translate field data from non-associative arrays into something that WPV_View_Base::generate_loop_output() understands.
    $fields_normalized = array();
    foreach( $fields as $field ) {
	    $fields_normalized[] = array(
				'prefix' => $field[0],
				'shortcode' => $field[1],
				'suffix' => $field[2],
				'field_name' => $field[3],
				'header_name' => $field[4],
				'row_title' => $field[5] );
	}
	
	$loop_output = WPV_View_Base::generate_loop_output( $style, $fields_normalized, $args );

	// Forward the fail when loop output couldn't have been generated. 
	if ( null == $loop_output ) {
		$data = array(
			'type' => 'error',
			'message' => __( 'Could not generate the Loop Output. Please reload and try again.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
		
	// Merge new settings to existing ones (overwrite keys from $layout_settings but keep the rest).
	$loop_output_settings = $loop_output['loop_output_settings'];
	$prev_settings = get_post_meta( $view_id, '_wpv_layout_settings', true );
	if( ! is_array( $prev_settings ) ) {
		// Handle missing _wpv_layout_settings for given View.
		$prev_settings = array();
	}
	$loop_output_settings = array_merge( $prev_settings, $loop_output_settings );
	
	if ( 
		isset( $loop_output_settings['fields'] )
		&& is_array( $loop_output_settings['fields'] )
	) {
		$loop_output_settings['fields'] = array_values( $loop_output_settings['fields'] );
	}

	// Return the results.
	$data = array(
		'loop_output_settings' => $loop_output_settings,
		'ct_content' => $loop_output['ct_content'] 
	);
	wp_send_json_success( $data );
}

/**
* wpv_loop_wizard_add_field
*
* ADd a new field to the Loop Wizard
* Nte that this is also used to generate the GUI for all the already existing fields
*
* @since unknown
*/

add_action( 'wp_ajax_wpv_loop_wizard_add_field', 'wpv_loop_wizard_add_field' );

function wpv_loop_wizard_add_field() {
	wpv_ajax_authenticate( 'wpv_loop_wizard_nonce', array( 'parameter_source' => 'post', 'type_of_death' => 'data' ) );
	
	if (
		! isset( $_POST["view_id"] )
		|| ! is_numeric( $_POST["view_id"] )
		|| intval( $_POST['view_id'] ) < 1 
	) {
		$data = array(
			'type' => 'id',
			'message' => __( 'Wrong or missing View ID.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}

	$menus = wpv_loop_wizard_get_available_field_menus( $_POST["view_id"] );
		
	$views_shortcodes_with_api_obj = apply_filters( 'wpv_filter_wpv_shortcodes_gui_data', array() );
	$views_shortcodes_with_api = array_keys( $views_shortcodes_with_api_obj );
	$user_fields_with_head = array(
		'user_email', 'display_name', 'user_login', 'user_url', 'user_registered'
	);
	
    ob_start();
	?>
	<div id="layout-wizard-style___wpv_layout_count_placeholder__" class="wpv-loop-wizard-item-container js-wpv-loop-wizard-item-container">
		<i class="icon-move js-layout-wizard-move-field"></i>
		<select name="layout-wizard-style" class="wpv-layout-wizard-item js-wpv-select2 js-wpv-layout-wizard-item js-layout-wizard-item">
		<?php
		foreach ( $menus as $group_title => $group_items ) {
			?>
			<optgroup label="<?php echo $group_title; ?>">
			<?php foreach ( $group_items as $current_item_title => $current_item ) {
				if ( 'css' == $current_item_title ) {
					// For some reason Types field bleed this css entry everywhere! Related to the editor_addon_menus_ filter
					continue;
				}
				$current_shortcode_name = $current_item[0];
				list($current_shortcode_handle) = explode( ' ', $current_item[1] );
				$current_shortcode_handle_corrected = $current_shortcode_handle; // needed to separate wpv-post-body from CT entries
				$current_shortcode_to_insert = $current_item[1];
				$current_shortcode_head = ''; // for table layouts
				$current_shortcode_is_types = false;
				$current_shortcode_types_name = '';
				
				if (
					'wpv-post-body' == $current_shortcode_handle 
					&& __( 'Basic', 'wpv-views' ) == $group_title
				) {
					$current_shortcode_to_insert = 'wpv-post-body view_template="None"';
				}
			
				// Head value for table layouts
				if ( $current_shortcode_handle === "wpv-post-taxonomy" ) {
					$current_shortcode_head = 'wpv-post-taxonomy';
					$current_shortcode_head = '';
				} else if ( $current_shortcode_handle === "wpv-post-field" ) {
					$current_shortcode_handle_corrected = $current_shortcode_handle . '_corrected';
					$current_shortcode_head = 'post-field-' . $current_shortcode_name;
					// if it is a new WooCommerce Views field
					if ( preg_match('/name="views_woo(.*?)"/', $current_shortcode_to_insert, $woo_match) ) {
						$current_shortcode_head = 'post-field-views_woo' . $woo_match[1];
					}
				} else if ( substr( $current_shortcode_to_insert, 0, 8 ) === "wpv-post" ) {
					if (
						'wpv-post-body' == $current_shortcode_handle
						&& __( 'Basic', 'wpv-views' ) != $group_title
					) {
						$current_shortcode_handle_corrected = $current_shortcode_handle . '_corrected';
					}
					$current_shortcode_head = substr( $current_shortcode_handle, 4 );
					if ( 
						$current_shortcode_handle === "wpv-post-status" 
						|| $current_shortcode_handle === "wpv-post-class" 
						|| $current_shortcode_handle === "wpv-post-body" 
						|| $current_shortcode_handle === "wpv-post-featured-image" 
					) {
						$current_shortcode_head = '';
					}
				} else if ( $current_shortcode_handle === "wpv-view" ) {
					$current_shortcode_head = 'post-view';
					$current_shortcode_head = '';
				} else if ( $current_shortcode_handle === "types" ) {
					if ( preg_match( '/field="(.*?)"/', $current_shortcode_to_insert, $field_in_loop ) !== 0 ) {
						$current_shortcode_head = 'types-field-' . $field_in_loop[1];
						$current_shortcode_is_types = true;
						$current_shortcode_types_name = $field_in_loop[1];
					} else if ( preg_match( '/usermeta="(.*?)"/', $current_shortcode_to_insert, $field_in_loop ) !== 0 ) {
						$current_shortcode_head = '';
						$current_shortcode_is_types = true;
						$current_shortcode_types_name = $field_in_loop[1];
					}
				} else if ( substr( $current_shortcode_to_insert, 0, 12 ) === "wpv-taxonomy" ) { // heading table solumns for wpv-taxonomy-* shortcodes
					if ( in_array( $current_shortcode_handle, array( 'wpv-taxonomy-link', 'wpv-taxonomy-title' ) ) ) {
						$current_shortcode_head = substr( $current_shortcode_handle, 4 );
					}
					if ( $current_shortcode_handle == 'wpv-taxonomy-post-count' ) {
						$current_shortcode_head = 'taxonomy-post_count';
					}
				} else if ( $current_shortcode_handle === "wpv-user" ) { // heaading table columns for wpv-user shortcodes
					preg_match( '/field="(.*?)"/', $current_shortcode_to_insert, $view_user_field );
					if ( 
						isset( $view_user_field[1] ) 
						&& in_array( $view_user_field[1], $user_fields_with_head ) 
					) {
						$current_shortcode_head = $view_user_field[1];
					} else {
						$current_shortcode_head = '';
					}
				}
				?>
				<option value="<?php echo base64_encode('['.$current_shortcode_to_insert.']'); ?>" 
					data-shortcodehandle="<?php echo esc_attr( $current_shortcode_handle_corrected ); ?>" 
					data-fieldname="<?php echo esc_attr( $current_shortcode_name ); ?>"
					data-headename="<?php echo esc_attr( $current_shortcode_head ); ?>" 
					data-rowtitle="<?php echo esc_attr( $current_shortcode_name ); ?>" 
					data-istype="<?php echo $current_shortcode_is_types ? '1' : '0'; ?>" 
					data-typename="<?php echo esc_attr( $current_shortcode_types_name ); ?>" 
					data-hasattributesgui="<?php echo ( in_array( $current_shortcode_handle_corrected, $views_shortcodes_with_api ) && ! in_array( $current_shortcode_handle_corrected, array( 'wpv-post-body', 'wpv-post-field' ) ) ) ? '1' : '0'; ?>" 
					data-onclick="<?php echo isset( $current_item[3] ) ? $current_item[3] : ''; ?>" 
					>
					<?php echo $current_shortcode_name; ?>
				</option>
			<?php } ?>
			</optgroup>
		<?php } ?>
		</select>
		<p class="wpv-helper-text js-layout-wizard-body-template-text hidden" style="margin-left: 33px;">
			<?php echo __('Using the Content Template', 'wpv-views'); ?>
			<select name="layout-wizard-body-template" class="layout-wizard-body-template js-wpv-layout-wizard-body-template">
				<option value="<?php echo base64_encode( '[wpv-post-body view_template="None"]' ); ?>" data-rowtitle="<?php echo esc_attr( __( 'None', 'wpv-views' ) ); ?>"><?php _e( 'None', 'wpv-views' ); ?></option>
				<?php
				if ( isset( $menus[__('Content Template', 'wpv-views')] ) ) {
					foreach ( $menus[__('Content Template', 'wpv-views')] as $ct_item ) { ?>
						<option value="<?php echo base64_encode('['.$ct_item[1].']'); ?>" data-rowtitle="<?php echo $ct_item[0]; ?>"><?php echo $ct_item[0]; ?></option>
					<?php 
					}
				}
				?>
			</select>
		</p>
		<button class="button-secondary js-custom-types-fields" style="display: none">
			<i class="icon-edit"></i> <?php _e('Edit', 'wpv-views'); ?>
		</button>
	
		<button class="button-secondary js-wpv-loop-wizard-shortcode-ui" style="display: none" data-nonce="<?php echo wp_create_nonce('wpv_editor_callback'); ?>">
			<i class="icon-edit"></i> <?php _e('Edit', 'wpv-views'); ?>
		</button>
	
		<button class="button-secondary js-layout-wizard-remove-field" style="position: absolute; top: 5px; right: 5px;"><i class="icon-remove"></i></button>
	</div>
	<?php
	$result_html = ob_get_clean();
	$data = array(
		'html' => $result_html
	);
	wp_send_json_success( $data );
}

/**
* wpv_update_loop_wizard_data_callback
*
* Update just the Loop Wizard data
* This is needed when there were only fields-related changes coming and pushing a Loop Output using a loop Template - so no Layout Output update is needed
*
* @since 1.9
*/

add_action( 'wp_ajax_wpv_update_loop_wizard_data', 'wpv_update_loop_wizard_data_callback' );

function wpv_update_loop_wizard_data_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		$data = array(
			'type' => 'capability',
			'message' => __( 'You do not have permissions for that.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	if ( 
		! isset( $_POST["wpnonce"] )
		|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_layout_extra_nonce' ) 
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
	// Get View settings and layout settings
    $view_layout_array = get_post_meta( $_POST["id"], '_wpv_layout_settings', true );
    // Save the wizard settings
    if ( isset( $_POST['include_wizard_data'] ) ) {
        $view_layout_array['style'] = sanitize_text_field( $_POST['style'] );
        $view_layout_array['table_cols'] = sanitize_text_field( $_POST['table_cols'] );
		$view_layout_array['bootstrap_grid_cols'] = sanitize_text_field( $_POST['bootstrap_grid_cols'] );
		$view_layout_array['bootstrap_grid_container'] = sanitize_text_field( $_POST['bootstrap_grid_container'] );
		$view_layout_array['bootstrap_grid_row_class'] = sanitize_text_field( $_POST['bootstrap_grid_row_class'] );
		$view_layout_array['bootstrap_grid_individual'] = sanitize_text_field( $_POST['bootstrap_grid_individual'] );
        $view_layout_array['include_field_names'] = sanitize_text_field( $_POST['include_field_names'] );
        $view_layout_array['fields'] = $_POST['fields'];// @todo sanitize this
        $view_layout_array['real_fields'] = $_POST['real_fields'];// @todo sanitize this 
    }
	update_post_meta( $_POST["id"], '_wpv_layout_settings', $view_layout_array );
	do_action( 'wpv_action_wpv_save_item', $_POST["id"] );
	$data = array(
		'id' => $_POST["id"],
		'message' => __( 'Loop Output saved', 'wpv-views' )
	);
	wp_send_json_success( $data );
}