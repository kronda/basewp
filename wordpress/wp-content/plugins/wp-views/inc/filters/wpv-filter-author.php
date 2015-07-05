<?php

/**
* Author filter
*
* @package Views
*
* @since unknown
*/

WPV_Author_Filter::on_load();

/**
* WPV_Author_Filter
*
* Views Author Filter Class
*
* @since 1.7.0
*/

class WPV_Author_Filter {

    static function on_load() {
        add_action( 'init', array( 'WPV_Author_Filter', 'init' ) );
		add_action( 'admin_init', array( 'WPV_Author_Filter', 'admin_init' ) );
    }

    static function init() {
		
    }
	
	static function admin_init() {
		// Register filter in lists and dialogs
		add_filter( 'wpv_filters_add_filter', array( 'WPV_Author_Filter', 'wpv_filters_add_filter_post_author' ), 1, 1 );
		add_action( 'wpv_add_filter_list_item', array( 'WPV_Author_Filter', 'wpv_add_filter_post_author_list_item' ), 1, 1 );
		// AJAX calbacks
		add_action( 'wp_ajax_wpv_filter_post_author_update', array( 'WPV_Author_Filter', 'wpv_filter_post_author_update_callback' ) );
			// TODO This might not be needed here, maybe for summary filter
			add_action( 'wp_ajax_wpv_filter_author_sumary_update', array( 'WPV_Author_Filter', 'wpv_filter_author_sumary_update_callback' ) );
		add_action( 'wp_ajax_wpv_filter_post_author_delete', array( 'WPV_Author_Filter', 'wpv_filter_post_author_delete_callback' ) );
		add_filter( 'wpv-view-get-summary', array( 'WPV_Author_Filter', 'wpv_post_author_summary_filter' ), 5, 3 );
		add_action( 'wp_ajax_wpv_suggest_author', array( 'WPV_Author_Filter', 'wpv_suggest_author' ) );
		add_action( 'wp_ajax_nopriv_wpv_suggest_author', array( 'WPV_Author_Filter', 'wpv_suggest_author' ) );
		// Register scripts
		add_action( 'admin_enqueue_scripts', array( 'WPV_Author_Filter','admin_enqueue_scripts' ), 20 );
	}
	
	/**
	* admin_enqueue_scripts
	*
	* Register the needed script for this filter
	*
	* @since 1.7
	*/
	
	static function admin_enqueue_scripts( $hook ) {
		wp_register_script( 'views-filter-author-js', ( WPV_URL . "/res/js/redesign/views_filter_author.js" ), array( 'suggest', 'views-filters-js'), WPV_VERSION, true );
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'views-editor' ) {
			wp_enqueue_script( 'views-filter-author-js' );
		}
	}
	
	/**
	* wpv_filters_add_filter_post_author
	*
	* Register the author filter in the popup dialog
	*
	* @param $filters
	*
	* @since unknown
	*/
	
	static function wpv_filters_add_filter_post_author( $filters ) {
		$filters['post_author'] = array(
			'name' => __( 'Post author', 'wpv-views' ),
			'present' => 'author_mode',
			'callback' => array( 'WPV_Author_Filter', 'wpv_add_new_filter_post_author_list_item' ),
			'group' => __( 'Post filters', 'wpv-views' )
		);
		return $filters;
	}
	
	/**
	* wpv_add_new_filter_post_author_list_item
	*
	* Register the author filter in the filters list
	*
	* @since unknown
	*/

	static function wpv_add_new_filter_post_author_list_item() {
		$args = array(
			'author_mode' => array( 'current_user' )
		);
		WPV_Author_Filter::wpv_add_filter_post_author_list_item( $args );
	}
	
	/**
	* wpv_add_filter_post_author_list_item
	*
	* Render author filter item in the filters list
	*
	* @param $view_settings
	*
	* @since unknown
	*/

	static function wpv_add_filter_post_author_list_item( $view_settings ) {
		if ( isset( $view_settings['author_mode'][0] ) ) {
			$li = WPV_Author_Filter::wpv_get_list_item_ui_post_author( $view_settings );
			WPV_Filter_Item::simple_filter_list_item( 'post_author', 'posts', 'post-author', __( 'Post author filter', 'wpv-views' ), $li );
		}
	}
	
	/**
	* wpv_get_list_item_ui_post_author
	*
	* Render author filter item content in the filters list
	*
	* @param $view_settings
	*
	* @since unknown
	*/

	static function wpv_get_list_item_ui_post_author( $view_settings = array() ) {
		if ( isset( $view_settings['author_mode'] ) && is_array( $view_settings['author_mode'] ) ) {
			$view_settings['author_mode'] = $view_settings['author_mode'][0];
		}
		ob_start();
		?>
		<p class='wpv-filter-post-author-edit-summary js-wpv-filter-summary js-wpv-filter-post-author-summary'>
			<?php echo wpv_get_filter_post_author_summary_txt( $view_settings ); ?>
		</p>
		<?php
		WPV_Filter_Item::simple_filter_list_item_buttons( 'post-author', 'wpv_filter_post_author_update', wp_create_nonce( 'wpv_view_filter_post_author_nonce' ), 'wpv_filter_post_author_delete', wp_create_nonce( 'wpv_view_filter_post_author_delete_nonce' ) );
		?>
		<div id="wpv-filter-post-author-edit" class="wpv-filter-edit js-wpv-filter-edit" style="padding-bottom:28px;">
			<div id="wpv-filter-post-author" class="js-wpv-filter-options js-wpv-filter-post-author-options">
				<?php WPV_Author_Filter::wpv_render_post_author_options( $view_settings ); ?>
			</div>
			<div class="js-wpv-filter-toolset-messages"></div>
			<span class="filter-doc-help">
				<?php echo sprintf(__('%sLearn about filtering by Post Author%s', 'wpv-views'),
					'<a class="wpv-help-link" href="' . WPV_FILTER_BY_AUTHOR_LINK . '" target="_blank">',
					' &raquo;</a>'
				); ?>
			</span>
		</div>
		<?php
		$res = ob_get_clean();
		return $res;
	}
	
	/**
	* wpv_filter_post_author_update_callback
	*
	* Update author filter callback
	*
	* @since unknown
	*/

	static function wpv_filter_post_author_update_callback() {
		if ( ! current_user_can( 'manage_options' ) ) {
			$data = array(
				'type' => 'capability',
				'message' => __( 'You do not have permissions for that.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		if ( 
			! isset( $_POST["wpnonce"] )
			|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_filter_post_author_nonce' ) 
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
		$view_id = intval( $_POST['id'] );
		parse_str( $_POST['filter_options'], $filter_author );
		$change = false;
		$view_array = get_post_meta( $view_id, '_wpv_settings', true );
		if ( 
			! isset( $filter_author['author_name'] ) 
			|| '' == $filter_author['author_name'] 
		) {
			$filter_author['author_name'] = '';
			$filter_author['author_id'] = 0;
		}
		$settings_to_check = array( 
			'author_mode', 'author_name', 'author_id', 
			'author_url_type', 'author_url', 'author_shortcode_type', 'author_shortcode', 'author_framework_type', 'author_framework'
		);
		foreach ( $settings_to_check as $set ) {
			if ( 
				isset( $filter_author[$set] )
				&& (
					! isset( $view_array[$set] ) 
					|| $filter_author[$set] != $view_array[$set] 
				)
			) {
				if ( is_array( $filter_author[$set] ) ) {
					$filter_author[$set] = array_map( 'sanitize_text_field', $filter_author[$set] );
				} else {
					$filter_author[$set] = sanitize_text_field( $filter_author[$set] );
				}
				$change = true;
				$view_array[$set] = $filter_author[$set];
			}
		}
		if ( $change ) {
			$result = update_post_meta( $view_id, '_wpv_settings', $view_array );
			do_action( 'wpv_action_wpv_save_item', $view_id );
		}
		$filter_author['author_mode'] = $filter_author['author_mode'][0];
		$data = array(
			'id' => $view_id,
			'message' => __( 'Post author filter saved', 'wpv-views' ),
			'summary' => wpv_get_filter_post_author_summary_txt( $filter_author )
		);
		wp_send_json_success( $data );
	}
	
	/**
	* Update author filter summary callback
	*/

	static function wpv_filter_author_sumary_update_callback() {
		$nonce = $_POST["wpnonce"];
		if ( ! wp_verify_nonce( $nonce, 'wpv_view_filter_author_nonce' ) ) {
			die( "Security check" );
		}
		parse_str( $_POST['filter_author'], $filter_author );
		$filter_author['author_mode'] = $filter_author['author_mode'][0];
		echo wpv_get_filter_post_author_summary_txt( $filter_author );
		die();
	}
	
	/**
	* wpv_filter_post_author_delete_callback
	*
	* Delete author filter callback
	*
	* @since unknown
	*/

	static function wpv_filter_post_author_delete_callback() {
		if ( ! current_user_can( 'manage_options' ) ) {
			$data = array(
				'type' => 'capability',
				'message' => __( 'You do not have permissions for that.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		if ( 
			! isset( $_POST["wpnonce"] )
			|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_filter_post_author_delete_nonce' ) 
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
		$settings_to_check = array( 
			'author_mode', 'author_name', 'author_id', 
			'author_url_type', 'author_url', 'author_shortcode_type', 'author_shortcode', 'author_framework_type', 'author_framework'
		);
		foreach ( $settings_to_check as $set ) {
			if ( isset( $view_array[$set] ) ) {
				unset( $view_array[$set] );
			}
		}
		update_post_meta( $_POST["id"], '_wpv_settings', $view_array );
		do_action( 'wpv_action_wpv_save_item', $_POST["id"] );
		$data = array(
			'id' => $_POST["id"],
			'message' => __( 'Post author filter deleted', 'wpv-views' )
		);
		wp_send_json_success( $data );
	}

	/**
	* wpv_post_author_summary_filter
	
	* Show the author filter on the View summary
	*
	* @since unknown
	*/

	static function wpv_post_author_summary_filter( $summary, $post_id, $view_settings ) {
		if( isset( $view_settings['query_type'] ) && $view_settings['query_type'][0] == 'posts' && isset( $view_settings['author_mode'] ) ) {
			$view_settings['author_mode'] = $view_settings['author_mode'][0];
			$result = wpv_get_filter_post_author_summary_txt( $view_settings, true );
			if ( $result != '' && $summary != '' ) {
				$summary .= '<br />';
			}
			$summary .= $result;
		}
		return $summary;
	}
	
	/**
	* wpv_suggest_author
	*
	* Suggest authors using an AJAX callback and built-in suggest script
	*
	* @since unknown
	*/

	static function wpv_suggest_author() {
		global $wpdb;
		$user = '%' . wpv_esc_like( $_REQUEST['q'] ) . '%';
		$results = $wpdb->get_results( 
			$wpdb->prepare( 
				"SELECT DISTINCT ID, display_name FROM {$wpdb->users} 
				INNER JOIN {$wpdb->usermeta} 
				WHERE display_name LIKE %s 
				ORDER BY display_name 
				LIMIT 0, 20", 
				$user 
			) 
		);
		foreach ( $results as $row ) {
			echo $row->display_name . ' # userID: ' . $row->ID . "\n";
		}
		die();
	}

	/**
	* wpv_render_post_author_options
	*
	* Render author filter options
	*
	* @param $view_settings
	*
	* @since unknown
	*/

	static function wpv_render_post_author_options( $view_settings = array() ) {
		$defaults = array(
			'author_mode' => 'current_user',
			'author_name' =>'',
			'author_id' => 0,
			'author_url' => 'author-filter',
			'author_url_type' => '',
			'author_shortcode' => 'author',
			'author_shortcode_type' => '',
			'author_framework' => '',
			'author_framework_type' => ''
		);
		$view_settings = wp_parse_args( $view_settings, $defaults );
		?>
		<h4><?php _e( 'How to filter', 'wpv-views' ); ?></h4>
		<ul class="wpv-filter-options-set">
			<li>
				<input type="radio" id="wpv-filter-author-current-user" name="author_mode[]" value="current_user" <?php checked( $view_settings['author_mode'], 'current_user' ); ?> autocomplete="off" />
				<label for="wpv-filter-author-current-user"><?php _e('Post author is the same as the logged in user', 'wpv-views'); ?></label>
			</li>
			<li>
				<input type="radio" id="wpv-filter-author-current-page" name="author_mode[]" value="current_page" <?php checked( $view_settings['author_mode'], 'current_page' ); ?> autocomplete="off" />
				<label for="wpv-filter-author-current-page"><?php _e('Post author is the author of the current page', 'wpv-views'); ?></label>
			</li>
			<li>
				<input type="radio" id="wpv-filter-author-parent-view" name="author_mode[]" value="parent_view" <?php checked( $view_settings['author_mode'], 'parent_view' ); ?> autocomplete="off" />
				<label for="wpv-filter-author-parent-view"><?php _e('Post author is set by the parent View', 'wpv-views'); ?></label>
			</li>
			<li>
				<input type="radio" id="wpv-filter-author-this-user" name="author_mode[]" value="this_user" <?php checked( $view_settings['author_mode'], 'this_user' ); ?> autocomplete="off" />
				<label for="wpv-filter-author-this-user"><?php _e('Post author is ', 'wpv-views'); ?></label>
				<?php 
				$author_display_name = $view_settings['author_name'];
				if ( 
					0 != $view_settings['author_id'] 
					&& '' == $author_display_name
					&& is_numeric( $view_settings['author_id'] )
				) {
					$user_info = get_userdata( intval( $view_settings['author_id'] ) );
					if ( $user_info ) {
						$author_display_name = $user_info->display_name;
					} else {
						$view_settings['author_id'] = 0;
						$author_display_name= '';
					}
				} else {
					$view_settings['author_id'] = 0;
					$author_display_name= '';
				}
				?>
				<input id="wpv_author_name" class="author_suggest js-author-suggest" type='text' name="author_name" value="<?php echo esc_attr( $author_display_name ); ?>" size="15" placeholder="<?php echo esc_attr( __( 'Start typing', 'wpv-views' ) ); ?>" />
				<input id="wpv_author" class="author_suggest_id js-author-suggest-id" type='hidden' name="author_id" value="<?php echo esc_attr( $view_settings['author_id'] ); ?>" />
			</li>
			<li>
				<input type="radio" id="wpv-filter-author-by-url" name="author_mode[]" value="by_url" <?php checked( $view_settings['author_mode'], 'by_url' ); ?> autocomplete="off" />
				<label for="wpv-filter-author-by-url"><?php _e('Author\'s ', 'wpv-views'); ?></label>
				<select id="wpv_author_url_type" name="author_url_type" autocomplete="off">
					<option value="id" <?php selected( $view_settings['author_url_type'], 'id' ); ?>><?php _e( 'ID', 'wpv-views' ); ?></option>
					<option value="username" <?php selected( $view_settings['author_url_type'], 'username' ); ?>><?php _e( 'username', 'wpv-views' ); ?></option>
				</select>
				<label for="wpv-author-url"><?php _e(' is set by the URL parameter: ', 'wpv-views'); ?></label>
				<input id="wpv-author-url" type='text' class="js-wpv-filter-author-url js-wpv-filter-validate" data-type="url" data-class="js-wpv-filter-author-url" name="author_url" value="<?php echo esc_attr( $view_settings['author_url'] ); ?>" size="10" autocomplete="off" />
			</li>
			<li>
				<input type="radio" id="wpv-filter-author-shortcode" name="author_mode[]" value="shortcode" <?php checked( $view_settings['author_mode'], 'shortcode' ); ?> autocomplete="off" />
				<label for="wpv-filter-author-shortcode"><?php _e('Author\'s ', 'wpv-views'); ?></label>
				<select id="wpv_author_shortcode_type" name="author_shortcode_type" autocomplete="off">
					<option value="id" <?php selected( $view_settings['author_shortcode_type'], 'id' ); ?>><?php _e( 'ID', 'wpv-views' ); ?></option>
					<option value="username" <?php selected( $view_settings['author_shortcode_type'], 'username' ); ?>><?php _e( 'username', 'wpv-views' ); ?></option>
				</select>
				<label for="wpv-author-shortcode"><?php _e(' is set by the View shortcode attribute: ', 'wpv-views'); ?></label>
				<input id="wpv-author-shortcode" type='text' class="js-wpv-filter-author-shortcode js-wpv-filter-validate" data-type="shortcode" data-class="js-wpv-filter-author-shortcode" name="author_shortcode" value="<?php echo esc_attr( $view_settings['author_shortcode'] ); ?>" size="10" autocomplete="off" />
			</li>
			<?php
			global $WP_Views_fapi;
			if ( $WP_Views_fapi->framework_valid ) {
				$framework_data = $WP_Views_fapi->framework_data
			?>
			<li>
				<input type="radio" id="wpv-filter-author-framework" name="author_mode[]" value="framework" <?php checked( $view_settings['author_mode'], 'framework' ); ?> autocomplete="off" />
				<label for="wpv-filter-author-framework"><?php _e('Author\'s ', 'wpv-views'); ?></label>
				<select id="wpv_author_framework_type" name="author_framework_type" autocomplete="off">
					<option value="id" <?php selected( $view_settings['author_framework_type'], 'id' ); ?>><?php _e( 'ID', 'wpv-views' ); ?></option>
					<option value="username" <?php selected( $view_settings['author_framework_type'], 'username' ); ?>><?php _e( 'username', 'wpv-views' ); ?></option>
				</select>
				<label for="wpv-author-framework"><?php echo sprintf( __( ' is set by the %s key: ', 'wpv-views' ), sanitize_text_field( $framework_data['name'] ) ); ?></label>
				<select name="author_framework" autocomplete="off">
					<option value=""><?php _e( 'Select a key', 'wpv-views' ); ?></option>
					<?php
					$fw_key_options = array();
					$fw_key_options = apply_filters( 'wpv_filter_extend_framework_options_for_post_author', $fw_key_options );
					foreach ( $fw_key_options as $index => $value ) {
						?>
						<option value="<?php echo esc_attr( $index ); ?>" <?php selected( $view_settings['author_framework'], $index ); ?>><?php echo $value; ?></option>
						<?php
					}
					?>
				</select>
			</li>
			<?php
			}
			?>
		</ul>
		<div class="filter-helper js-wpv-author-helper"></div>
		<?php
	}

}
