<?php

/**
* Post relationship filter
*
* @package Views
*
* @since unknown
*/

WPV_Post_Relationship_Filter::on_load();

/**
* WPV_Post_Relationship_Filter
*
* Views Post Relationship Filter Class
*
* @since 1.7.0
*/

class WPV_Post_Relationship_Filter {

    static function on_load() {
        add_action( 'init', array( 'WPV_Post_Relationship_Filter', 'init' ) );
		add_action( 'admin_init', array( 'WPV_Post_Relationship_Filter', 'admin_init' ) );
    }

    static function init() {
		
    }
	
	static function admin_init() {
		// Register filter in lists and dialogs
		add_filter( 'wpv_filters_add_filter', array( 'WPV_Post_Relationship_Filter', 'wpv_filters_add_filter_relationship' ), 1, 2 );
		add_action( 'wpv_add_filter_list_item', array( 'WPV_Post_Relationship_Filter', 'wpv_add_filter_post_relationship_list_item' ), 1, 1 );
		// AJAX calbacks
		add_action( 'wp_ajax_wpv_filter_post_relationship_update', array( 'WPV_Post_Relationship_Filter', 'wpv_filter_post_relationship_update_callback' ) );
			// TODO This might not be needed here, maybe for summary filter
			add_action( 'wp_ajax_wpv_filter_post_relationship_sumary_update', array( 'WPV_Post_Relationship_Filter', 'wpv_filter_post_relationship_sumary_update_callback' ) );
		add_action( 'wp_ajax_wpv_filter_post_relationship_delete', array( 'WPV_Post_Relationship_Filter', 'wpv_filter_post_relationship_delete_callback' ) );
		add_filter( 'wpv-view-get-summary', array( 'WPV_Post_Relationship_Filter', 'wpv_post_relationship_summary_filter' ), 5, 3 );
		add_action( 'wp_ajax_wpv_get_post_relationship_post_select', array( 'WPV_Post_Relationship_Filter', 'wpv_get_post_relationship_post_select_callback' ) );
		// Register scripts
		add_action( 'admin_enqueue_scripts', array( 'WPV_Post_Relationship_Filter','admin_enqueue_scripts' ), 20 );
	}
	
	/**
	 * admin_enqueue_scripts
	 *
	 * Register the needed script for this filter
	 *
	 * @since 1.7
	 */
	static function admin_enqueue_scripts( $hook ) {
		wp_register_script( 'views-filter-post-relationship-js', ( WPV_URL . "/res/js/redesign/views_filter_post_relationship.js" ), array( 'suggest', 'views-filters-js'), WPV_VERSION, true );
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'views-editor' ) {
			wp_enqueue_script( 'views-filter-post-relationship-js' );
			$pr_strings = array(
				'post_type_missing' => __( 'There is no post type selected in the Content Selection section', 'wpv-views' ),
				'post_type_orphan' => __( 'This will filter out posts of the following types, because they are not children of any other post type:', 'wpv-views' ),
			);
			wp_localize_script( 'views-filter-post-relationship-js', 'wpv_pr_strings', $pr_strings );
		}
	}
	
	/**
	 * wpv_filters_add_filter_relationship
	 *
	 * Register the post relationship filter in the popup dialog
	 *
	 * @param $filters
	 *
	 * @since unknown
	 */
	static function wpv_filters_add_filter_relationship( $filters, $post_type ) {
		if ( function_exists( 'wpcf_pr_get_belongs' ) ) {
			$filters['post_relationship'] = array(
				'name' => __( 'Post relationship - Post is a child of', 'wpv-views' ),
				'present' => 'post_relationship_mode',
				'callback' => array( 'WPV_Post_Relationship_Filter', 'wpv_add_new_filter_post_relationship_list_item' ),
				'args' => $post_type,
				'group' => __( 'Post filters', 'wpv-views' )
			);
		}
		return $filters;
	}
	
	/**
	 * wpv_add_new_filter_post_relationship_list_item
	 *
	 * Register the post relationship filter in the filters list
	 *
	 * @param $post_type array
	 *
	 * @since unknown
	 */
	static function wpv_add_new_filter_post_relationship_list_item( $post_type ) {
		if ( function_exists( 'wpcf_pr_get_belongs' ) ) {
			$args = array(
				'post_relationship_mode' => array( 'current_page' ),
				'post_type'=> $post_type
			);
			WPV_Post_Relationship_Filter::wpv_add_filter_post_relationship_list_item( $args );
		}
	}
	
	/**
	 * wpv_add_filter_post_relationship_list_item
	 *
	 * Render post relationship filter item in the filters list
	 *
	 * @param $view_settings
	 *
	 * @since unknown
	 */
	static function wpv_add_filter_post_relationship_list_item( $view_settings ) {
		if ( function_exists( 'wpcf_pr_get_belongs' ) ) {
			if ( isset( $view_settings['post_relationship_mode'][0] ) ) {
				$li = WPV_Post_Relationship_Filter::wpv_get_list_item_ui_post_post_relationship( $view_settings );
				WPV_Filter_Item::simple_filter_list_item( 'post_relationship', 'posts', 'post-relationship', __( 'Post relationship filter', 'wpv-views' ), $li );
			}
		}
	}
	
	/**
	* wpv_get_list_item_ui_post_post_relationship
	*
	* Render post relationship filter item content in the filters list
	*
	* @param $view_settings
	*
	* @since unknown
	*/

	static function wpv_get_list_item_ui_post_post_relationship( $view_settings = array() ) {
		if ( isset( $view_settings['post_relationship_mode'] ) && is_array( $view_settings['post_relationship_mode'] ) ) {
			$view_settings['post_relationship_mode'] = $view_settings['post_relationship_mode'][0];
		}
		if (
			isset( $view_settings['post_relationship_id'] ) 
			&& ! empty( $view_settings['post_relationship_id'] )
		) {
			// Adjust for WPML support
			$view_settings['post_relationship_id'] = apply_filters( 'translate_object_id', $view_settings['post_relationship_id'], 'any', true, null );
		}
		if ( ! isset( $view_settings['post_type'] ) ) {
			$view_settings['post_type'] = array();
		}
		ob_start()
		?>
		<p class='wpv-filter-post-relationship-edit-summary js-wpv-filter-summary js-wpv-filter-post-relationship-summary'>
			<?php echo wpv_get_filter_post_relationship_summary_txt( $view_settings ); ?>
		</p>
		<?php
		WPV_Filter_Item::simple_filter_list_item_buttons( 'post-relationship', 'wpv_filter_post_relationship_update', wp_create_nonce( 'wpv_view_filter_post_relationship_nonce' ), 'wpv_filter_post_relationship_delete', wp_create_nonce( 'wpv_view_filter_post_relationship_delete_nonce' ) );
		?>
		<span class="wpv-filter-title-notice js-wpv-filter-post-relationship-notice hidden">
			<i class="icon-bookmark icon-rotate-270 icon-large" title="<?php echo esc_attr( __( 'This filters needs some action', 'wpv-views' ) ); ?>"></i>
		</span>
		<div id="wpv-filter-post-relationship-edit" class="wpv-filter-edit js-wpv-filter-edit" style="padding-bottom:28px;">
			<div id="wpv-filter-post-relationship" class="js-wpv-filter-options js-wpv-filter-post-relationship-options">
				<?php WPV_Post_Relationship_Filter::wpv_render_post_relationship( $view_settings ); ?>
			</div>
			<div class="js-wpv-filter-toolset-messages"></div>
			<span class="filter-doc-help">
				<a class="wpv-help-link" target="_blank" href="http://wp-types.com/documentation/user-guides/querying-and-displaying-child-posts/?utm_source=viewsplugin&utm_campaign=views&utm_medium=edit-view-relationships-filter&utm_term=Querying and Displaying Child Posts">
					<?php _e('Querying and Displaying Child Posts', 'wpv-views'); ?>
				 &raquo;</a>
			</span>
		</div>
		<?php
		$res = ob_get_clean();
		return $res;
	}
	
	/**
	* wpv_filter_post_relationship_update_callback
	*
	* Update post relationship filter callback
	*
	* @since unknown
	*/

	static function wpv_filter_post_relationship_update_callback() {
		if ( ! current_user_can( 'manage_options' ) ) {
			$data = array(
				'type' => 'capability',
				'message' => __( 'You do not have permissions for that.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		if ( 
			! isset( $_POST["wpnonce"] )
			|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_filter_post_relationship_nonce' ) 
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
		if ( empty( $_POST['filter_options'] ) ) {
			$data = array(
				'type' => 'data_missing',
				'message' => __( 'Wrong or missing data.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		$change = false;
		$view_id = intval( $_POST['id'] );
		parse_str( $_POST['filter_options'], $filter_relationship );
		$view_array = get_post_meta( $view_id, '_wpv_settings', true );
		if ( ! isset( $filter_relationship['post_relationship_id'] ) ) {
			$filter_relationship['post_relationship_id'] = 0;
		}
		$settings_to_check = array(
			'post_relationship_mode',
			'post_relationship_id',
			'post_relationship_shortcode_attribute',
			'post_relationship_url_parameter',
			'post_relationship_framework'
		);
		foreach ( $settings_to_check as $set ) {
			if ( 
				isset( $filter_relationship[$set] ) 
				&& (
					! isset( $view_array[$set] ) 
					|| $filter_relationship[$set] != $view_array[$set] 
				)
			) {
				if ( is_array( $filter_relationship[$set] ) ) {
					$filter_relationship[$set] = array_map( 'sanitize_text_field', $filter_relationship[$set] );
				} else {
					$filter_relationship[$set] = sanitize_text_field( $filter_relationship[$set] );
				}
				$change = true;
				$view_array[$set] = $filter_relationship[$set];
			}
		}
		if ( $change ) {
			$result = update_post_meta( $view_id, '_wpv_settings', $view_array );
			do_action( 'wpv_action_wpv_save_item', $view_id );
		}
		$data = array(
			'id' => $view_id,
			'message' => __( 'Specific users filter saved', 'wpv-views' ),
			'summary' => wpv_get_filter_post_relationship_summary_txt( $filter_relationship )
		);
		wp_send_json_success( $data );
	}
	
	/**
	* Update post relationship filter summary callback
	*/
	
	static function wpv_filter_post_relationship_sumary_update_callback() {
		$nonce = $_POST["wpnonce"];
		if ( ! wp_verify_nonce( $nonce, 'wpv_view_filter_post_relationship_nonce' ) ) {
			die( "Security check" );
		}
		if ( ! isset( $_POST['post_relationship_id'] ) ) {
			$_POST['post_relationship_id'] = 0;
		}
		echo wpv_get_filter_post_relationship_summary_txt(
			array(
				'post_relationship_mode'	=> $_POST['post_relationship_mode'],
				'post_relationship_id'		=> $_POST['post_relationship_id']
			)
		);
		die();
	}
	
	/**
	* wpv_filter_post_relationship_delete_callback
	*
	* Delete post relationship filter callback
	*
	* @since unknown
	*/

	static function wpv_filter_post_relationship_delete_callback() {
		if ( ! current_user_can( 'manage_options' ) ) {
			$data = array(
				'type' => 'capability',
				'message' => __( 'You do not have permissions for that.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		if ( 
			! isset( $_POST["wpnonce"] )
			|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_filter_post_relationship_delete_nonce' ) 
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
		$view_array = get_post_meta( $_POST["id"], '_wpv_settings', true );
		$to_delete = array(
			'post_relationship_mode',
			'post_relationship_id',
			'post_relationship_shortcode_attribute',
			'post_relationship_url_parameter',
			'post_relationship_url_tree',
			'post_relationship_framework'
		);
		foreach ( $to_delete as $index ) {
			if ( isset( $view_array[$index] ) ) {
				unset( $view_array[$index] );
			}
		}
		$len = isset( $view_array['filter_controls_field_name'] ) ? count( $view_array['filter_controls_field_name'] ) : 0;
		$splice = false;
		for ( $i = 0; $i < $len; $i++ ) {
			if ( strpos( $view_array['filter_controls_field_name'][$i], 'relationship' ) !== false ) {
				$splice = $i;
			}
		}
		if ( $splice !== false ) {
			foreach ( Editor_addon_parametric::$prm_db_fields as $dbf ) {
				array_splice( $view_array[$dbf], $splice, 1 );
			}
		}
		update_post_meta( $_POST["id"], '_wpv_settings', $view_array );
		do_action( 'wpv_action_wpv_save_item', $_POST["id"] );
		$data = array(
			'id' => $_POST["id"],
			'message' => __( 'Post relationship filter deleted', 'wpv-views' )
		);
		wp_send_json_success( $data );
	}
	
	/**
	* wpv_post_relationship_summary_filter
	
	* Show the post relationship filter on the View summary
	*
	* @since unknown
	*/

	static function wpv_post_relationship_summary_filter( $summary, $post_id, $view_settings ) {
		if ( isset( $view_settings['query_type'] ) && $view_settings['query_type'][0] == 'posts' && isset( $view_settings['post_relationship_mode'] ) ) {
			$view_settings['post_relationship_mode'] = $view_settings['post_relationship_mode'][0];
			$result = wpv_get_filter_post_relationship_summary_txt( $view_settings, true );
			if ( $result != '' && $summary != '' ) {
				$summary .= '<br />';
			}
			$summary .= $result;
		}
		return $summary;
	}
	
	/**
	* wpv_render_post_relationship
	*
	* Render post relationship filter options
	*
	* @param $view_settings
	*
	* @since unknown
	*/

	static function wpv_render_post_relationship( $view_settings = array() ) {
		global $wpdb;
		$defaults = array(
			'post_relationship_mode' => 'current_page',
			'post_relationship_id' => 0,
			'post_relationship_shortcode_attribute' => 'wpvprchildof',
			'post_relationship_url_parameter' => 'wpv-pr-child-of',
			'post_relationship_framework' => ''
		);
		$view_settings = wp_parse_args( $view_settings, $defaults );
		?>
		<h4><?php _e( 'Select posts that are children of...', 'wpv-views' ); ?></h4>
		<ul class="wpv-filter-options-set">
			<li>
				<input type="radio" id="post-relationship-mode-current-page" class="js-post-relationship-mode" name="post_relationship_mode[]" value="current_page" <?php checked( $view_settings['post_relationship_mode'], 'current_page' ); ?> autocomplete="off" />
				<label for="post-relationship-mode-current-page"><?php _e('Post where this View is inserted', 'wpv-views'); ?></label>
			</li>
			<li>
				<input type="radio" id="post-relationship-mode-parent-view" class="js-post-relationship-mode" name="post_relationship_mode[]" value="parent_view" <?php checked( $view_settings['post_relationship_mode'], 'parent_view' ); ?> autocomplete="off" />
				<label for="post-relationship-mode-parent-view"><?php _e('Post set by parent View', 'wpv-views'); ?></label>
			</li>
			<li>
				<input type="radio" id="post-relationship-mode-this-page" class="js-post-relationship-mode" name="post_relationship_mode[]" value="this_page" <?php checked( $view_settings['post_relationship_mode'], 'this_page' ); ?> autocomplete="off" />
				<label for="post-relationship-mode-this-page"><?php _e('Specific:', 'wpv-views'); ?></label>
				<select id="wpv_post_relationship_post_type" name="post_relationship_type" class="js-post-relationship-post-type" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_post_relationship_post_type_nonce' ); ?>" autocomplete="off">
				<?php
				$post_types = get_post_types( array( 'public' => true ), 'objects' );
				if ( 
					$view_settings['post_relationship_id'] == 0 
					|| $view_settings['post_relationship_id'] == '' 
				) {
					$selected_type = 'page';
				} else {
					$selected_type = $wpdb->get_var( 
						$wpdb->prepare(
							"SELECT post_type FROM {$wpdb->posts} 
							WHERE ID = %d 
							LIMIT 1",
							$view_settings['post_relationship_id']
						)
					);
					if ( ! $selected_type ) {
						$selected_type = 'page';
					}
				}
				foreach ( $post_types as $post_type ) {
					?>
					<option value="<?php echo esc_attr( $post_type->name ); ?>" <?php selected( $selected_type, $post_type->name ); ?>><?php echo $post_type->labels->singular_name; ?></option>
					<?php 
				}
				?>
				</select>
				<?php wpv_show_posts_dropdown( $selected_type, 'post_relationship_id', $view_settings['post_relationship_id'] ); ?>
			</li>
			<li>
				<input type="radio" id="post-relationship-mode-shortcode" class="js-post-relationship-mode" name="post_relationship_mode[]" value="shortcode_attribute" <?php checked( $view_settings['post_relationship_mode'], 'shortcode_attribute' ); ?> autocomplete="off" />
				<label for="post-relationship-mode-shortcode"><?php _e('Post with ID set by the shortcode attribute', 'wpv-views'); ?></label>
				<input class="js-post-relationship-shortcode-attribute js-wpv-filter-validate" name="post_relationship_shortcode_attribute" data-type="shortcode" type="text" value="<?php echo esc_attr( $view_settings['post_relationship_shortcode_attribute'] ); ?>" autocomplete="off" />
			</li>
			<li>
				<input type="radio" id="post-relationship-mode-url" class="js-post-relationship-mode" name="post_relationship_mode[]" value="url_parameter" <?php checked( $view_settings['post_relationship_mode'], 'url_parameter' ); ?> autocomplete="off" />
				<label for="post-relationship-mode-url"><?php _e('Post with ID set by the URL parameter', 'wpv-views'); ?></label>
				<input class="js-post-relationship-url-parameter js-wpv-filter-validate" name="post_relationship_url_parameter" data-type="url" type="text" value="<?php echo esc_attr( $view_settings['post_relationship_url_parameter'] ); ?>" autocomplete="off" />
			</li>
			<?php
			global $WP_Views_fapi;
			if ( $WP_Views_fapi->framework_valid ) {
				$framework_data = $WP_Views_fapi->framework_data
			?>
			<li>
				<input type="radio" id="post-relationship-mode-framework" class="js-post-relationship-mode" name="post_relationship_mode[]" value="framework" <?php checked( $view_settings['post_relationship_mode'], 'framework' ); ?> autocomplete="off" />
				<label for="post-relationship-mode-framework"><?php echo sprintf( __( 'Post with ID set by the %s key: ', 'wpv-views'), sanitize_text_field( $framework_data['name'] ) ); ?></label>
				<select name="post_relationship_framework" autocomplete="off">
					<option value=""><?php _e( 'Select a key', 'wpv-views' ); ?></option>
					<?php
					$fw_key_options = array();
					$fw_key_options = apply_filters( 'wpv_filter_extend_framework_options_for_post_relationship', $fw_key_options );
					foreach ( $fw_key_options as $index => $value ) {
						?>
						<option value="<?php echo esc_attr( $index ); ?>" <?php selected( $view_settings['post_relationship_framework'], $index ); ?>><?php echo $value; ?></option>
						<?php
					}
					?>
				</select>
			</li>
			<?php
			}
			?>
		</ul>
		<?php
	}
	
	/**
	* wpv_get_post_relationship_post_select_callback
	*
	* Render a select dropdown given a post type
	*
	* @since unknown
	*/
	
	static function wpv_get_post_relationship_post_select_callback() {
		$nonce = $_POST["wpnonce"];
		if ( ! wp_verify_nonce( $nonce, 'wpv_view_filter_post_relationship_post_type_nonce' ) ) {
			die( "Security check" );
		}
		wpv_show_posts_dropdown( $_POST['post_type'], 'post_relationship_id' );
		die();
	}

	//----------------------------------------------------------------

	/**
	* DEPRECATED maybe used by MM?
	*/
	/*
	static function wpv_ajax_wpv_get_post_relationship_info() { // TODO check if this is deprecated
		if (wp_verify_nonce($_POST['wpv_nonce'], 'wpv_get_posts_select_nonce')) {
			if (function_exists('wpcf_pr_get_belongs') && isset($_POST['post_types'])) {
				$post_types = get_post_types('', 'objects');

				$output_done = false;
				foreach ($_POST['post_types'] as $post_type) {

					$related = wpcf_pr_get_belongs($post_type);
					if ($related === false) {
						echo sprintf(__('Post type <strong>%s</strong> doesn\'t belong to any other post type', 'wpv-views'), $post_types[$post_type]->labels->singular_name, $related);
						echo '<br />';
						$output_done = true;
					}
					if (is_array($related) && count($related)) {
						$keys = array_keys($related);
						$related = array();

						foreach($keys as$key) {
							$related[] = $post_types[$key]->labels->singular_name;
						}

					}
					if (is_array($related) && count($related) == 1) {
						$related = implode(', ', $related);
						echo sprintf(__('Post type <strong>%s</strong> is a child of <strong>%s</strong> post type', 'wpv-views'), $post_types[$post_type]->labels->singular_name, $related);
						echo '<br />';
						$output_done = true;
					}
					if (is_array($related) && count($related) > 1) {
						$last = array_pop($related);
						$related = implode(', ', $related);
						$related .= __(' and ') . $last;
						echo sprintf(__('Post type <strong>%s</strong> is a child of <strong>%s</strong> post types', 'wpv-views'), $post_types[$post_type]->labels->singular_name, $related);
						echo '<br />';
						$output_done = true;
					}
				}
				if ($output_done) {
					echo '<br />';
				}
			}

		}
		die();
	}
	*/

}
