<?php

/**
* Search filter & taxonomy search filter
*
* @package Views
*
* @since unknown
*/

WPV_Search_Filter::on_load();

/**
* WPV_Search_Filter
*
* Views Search Filter Class
*
* @since 1.7.0
*/

class WPV_Search_Filter {

    static function on_load() {
        add_action( 'init', array( 'WPV_Search_Filter', 'init' ) );
		add_action( 'admin_init', array( 'WPV_Search_Filter', 'admin_init' ) );
    }

    static function init() {
		
    }
	
	static function admin_init() {
		// Register filters in lists and dialogs
		add_filter( 'wpv_filters_add_filter', array( 'WPV_Search_Filter', 'wpv_filters_add_filter_post_search' ), 1, 1 );
		add_action( 'wpv_add_filter_list_item', array( 'WPV_Search_Filter', 'wpv_add_filter_post_search_list_item' ), 1, 1 );
		add_filter( 'wpv_taxonomy_filters_add_filter', array( 'WPV_Search_Filter', 'wpv_filters_add_filter_taxonomy_search' ), 1, 1 );
		add_action( 'wpv_add_taxonomy_filter_list_item', array( 'WPV_Search_Filter', 'wpv_add_filter_taxonomy_search_list_item' ), 1, 1 );
		// AJAX calbacks
		add_action( 'wp_ajax_wpv_filter_post_search_update', array( 'WPV_Search_Filter', 'wpv_filter_post_search_update_callback' ) );
			// TODO This might not be needed here, maybe for summary filter
			add_action( 'wp_ajax_wpv_filter_post_search_sumary_update', array( 'WPV_Search_Filter', 'wpv_filter_post_search_sumary_update_callback' ) );
		add_action( 'wp_ajax_wpv_filter_post_search_delete', array( 'WPV_Search_Filter', 'wpv_filter_post_search_delete_callback' ) );
		add_filter( 'wpv-view-get-summary', array( 'WPV_Search_Filter', 'wpv_post_search_summary_filter' ), 5, 3 );
		add_action( 'wp_ajax_wpv_filter_taxonomy_search_update', array( 'WPV_Search_Filter', 'wpv_filter_taxonomy_search_update_callback' ) );
			// TODO This might not be needed here, maybe for summary filter
			add_action( 'wp_ajax_wpv_filter_taxonomy_search_sumary_update', array( 'WPV_Search_Filter', 'wpv_filter_taxonomy_search_sumary_update_callback' ) );
		add_action( 'wp_ajax_wpv_filter_taxonomy_search_delete', array( 'WPV_Search_Filter', 'wpv_filter_taxonomy_search_delete_callback' ) );
		// Register scripts
		add_action( 'admin_enqueue_scripts', array( 'WPV_Search_Filter','admin_enqueue_scripts' ), 20 );
	}
	
	/**
	* admin_enqueue_scripts
	*
	* Register the needed script for this filter
	*
	* @since 1.7
	*/
	
	static function admin_enqueue_scripts( $hook ) {
		wp_register_script( 'views-filter-search-js', ( WPV_URL . "/res/js/redesign/views_filter_search.js" ), array( 'views-filters-js'), WPV_VERSION, true );
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'views-editor' ) {
			wp_enqueue_script( 'views-filter-search-js' );
		}
	}
	
	//-----------------------
	// Search filter
	//-----------------------
	
	/**
	* wpv_filters_add_filter_post_search
	*
	* Register the search filter in the popup dialog
	*
	* @param $filters
	*
	* @since unknown
	*/

	static function wpv_filters_add_filter_post_search( $filters ) {
		$filters['post_search'] = array(
			'name' => __( 'Post search', 'wpv-views' ),
			'present' => 'search_mode',
			'callback' => array( 'WPV_Search_Filter', 'wpv_add_new_filter_post_search_list_item' ),
			'group' => __( 'Post filters', 'wpv-views' )
		);
		return $filters;
	}
	
	/**
	* wpv_add_new_filter_post_search_list_item
	*
	* Register the search filter in the filters list
	*
	* @since unknown
	*/

	static function wpv_add_new_filter_post_search_list_item() {
		$args = array(
			'search_mode' => array( 'specific' )
		);
		WPV_Search_Filter::wpv_add_filter_post_search_list_item( $args );
	}
	
	/**
	* wpv_add_filter_post_search_list_item
	*
	* Render search filter item in the filters list
	*
	* @param $view_settings
	*
	* @since unknown
	*/

	static function wpv_add_filter_post_search_list_item( $view_settings ) {
		if ( isset( $view_settings['search_mode'] ) ) {
			$li = WPV_Search_Filter::wpv_get_list_item_ui_post_search( $view_settings );
			WPV_Filter_Item::simple_filter_list_item( 'post_search', 'posts', 'post-search', __( 'Post search filter', 'wpv-views' ), $li );
		}
	}

	/**
	* wpv_get_list_item_ui_post_search
	*
	* Render search filter item content in the filters list
	*
	* @param $view_settings
	*
	* @since unknown
	*/

	static function wpv_get_list_item_ui_post_search( $view_settings = array() ) {
		if ( isset( $view_settings['search_mode'] ) && is_array( $view_settings['search_mode'] ) ) {
			$view_settings['search_mode'] = $view_settings['search_mode'][0];
		}
		if ( ! isset( $view_settings['post_search_value'] ) ) {
			$view_settings['post_search_value'] = '';
		}
		ob_start();
		?>
		<p class='wpv-filter-search-summary js-wpv-filter-summary js-wpv-filter-post-search-summary'>
			<?php echo wpv_get_filter_post_search_summary_txt( $view_settings ); ?>
		</p>
		<?php
		WPV_Filter_Item::simple_filter_list_item_buttons( 'post-search', 'wpv_filter_post_search_update', wp_create_nonce( 'wpv_view_filter_post_search_nonce' ), 'wpv_filter_post_search_delete', wp_create_nonce( 'wpv_view_filter_post_search_delete_nonce' ) );
		?>
		<div id="wpv-filter-post-search-edit" class="wpv-filter-edit js-wpv-filter-edit" style="padding-bottom:28px;">
			<div id="wpv-filter-post-search" class="js-wpv-filter-options js-wpv-filter-post-search-options">
				<?php WPV_Search_Filter::wpv_render_post_search_options( $view_settings ); ?>
			</div>
			<div class="js-wpv-filter-toolset-messages"></div>
			<span class="filter-doc-help">
				<?php echo sprintf(__('%sLearn about filtering for a specific text string%s', 'wpv-views'),
					'<a class="wpv-help-link" href="' . WPV_FILTER_BY_SPECIFIC_TEXT_LINK . '" target="_blank">',
					' &raquo;</a>'
				); ?>
			</span>
		</div>
		<?php
		$res = ob_get_clean();
		return $res;
	}

	/**
	* wpv_filter_post_search_update_callback
	*
	* Update search filter callback
	*
	* @since unknown
	*/

	static function wpv_filter_post_search_update_callback() {
		if ( ! current_user_can( 'manage_options' ) ) {
			$data = array(
				'type' => 'capability',
				'message' => __( 'You do not have permissions for that.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		if ( 
			! isset( $_POST["wpnonce"] )
			|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_filter_post_search_nonce' ) 
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
		$view_id = $_POST['id'];
		parse_str( $_POST['filter_options'], $filter_search );
		$view_array = get_post_meta( $view_id, '_wpv_settings', true );
		if ( ! isset( $filter_search['post_search_value'] ) ) {
			$filter_search['post_search_value'] = '';
		}
		$settings_to_check = array(
			'search_mode',
			'post_search_value',
			'post_search_content'
		);
		foreach ( $settings_to_check as $set ) {
			if ( 
				isset( $filter_search[$set] ) 
				&& (
					! isset( $view_array[$set] ) 
					|| $filter_search[$set] != $filter_search[$set] 
				)
			) {
				if ( is_array( $filter_search[$set] ) ) {
					$filter_search[$set] = array_map( 'sanitize_text_field', $filter_search[$set] );
				} else {
					$filter_search[$set] = sanitize_text_field( $filter_search[$set] );
				}
				$change = true;
				$view_array[$set] = $filter_search[$set];
			}
		}
		if ( $change ) {
			update_post_meta( $view_id, '_wpv_settings', $view_array );
			do_action( 'wpv_action_wpv_save_item', $view_id );
		}
		$filter_search['search_mode'] = $filter_search['search_mode'][0];
		$data = array(
			'id' => $view_id,
			'message' => __( 'Post search filter saved', 'wpv-views' ),
			'summary' => wpv_get_filter_post_search_summary_txt( $filter_search )
		);
		wp_send_json_success( $data );
	}
	
	/**
	* Update search filter summary callback
	*/

	static function wpv_filter_post_search_sumary_update_callback() {
		$nonce = $_POST["wpnonce"];
		if ( ! wp_verify_nonce( $nonce, 'wpv_view_filter_post_search_nonce' ) ) {// Not sure about this nonce...
			die( "Security check" );
		}
		parse_str( $_POST['filter_options'], $filter_search );
		$filter_search['search_mode'] = $filter_search['search_mode'][0];
		if ( ! isset( $filter_search['post_search_value'] ) ) {
			$filter_search['post_search_value'] = '';
		}
		echo wpv_get_filter_post_search_summary_txt( $filter_search );
		die();
	}
	
	/**
	* wpv_filter_post_search_delete_callback
	*
	* Delete search filter callback
	*
	* @since unknown
	*/

	static function wpv_filter_post_search_delete_callback() {
		if ( ! current_user_can( 'manage_options' ) ) {
			$data = array(
				'type' => 'capability',
				'message' => __( 'You do not have permissions for that.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		if ( 
			! isset( $_POST["wpnonce"] )
			|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_filter_post_search_delete_nonce' ) 
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
		if ( isset( $view_array['search_mode'] ) ) {
			unset( $view_array['search_mode'] );
		}
		if ( isset( $view_array['post_search_value'] ) ) {
			unset( $view_array['post_search_value'] );
		}
		if ( isset( $view_array['post_search_content'] ) ) {
			unset( $view_array['post_search_content'] );
		}
		update_post_meta( $_POST["id"], '_wpv_settings', $view_array );
		do_action( 'wpv_action_wpv_save_item', $_POST["id"] );
		$data = array(
			'id' => $_POST["id"],
			'message' => __( 'Post search filter deleted', 'wpv-views' )
		);
		wp_send_json_success( $data );;
	}
	
	/**
	* wpv_post_search_summary_filter
	
	* Show the search filter on the View summary
	*
	* @since unknown
	*
	* @todo we may need something like that for taxonomy search
	*/

	static function wpv_post_search_summary_filter( $summary, $post_id, $view_settings ) {
		if ( isset( $view_settings['query_type']) && $view_settings['query_type'][0] == 'posts' && isset( $view_settings['search_mode'] ) ) {
			$view_settings['search_mode'] = $view_settings['search_mode'][0];
			$result = wpv_get_filter_post_search_summary_txt( $view_settings, true );
			if ( $result != '' && $summary != '' ) {
				$summary .= '<br />';
			}
			$summary .= $result;
		}
		return $summary;
	}

	//-----------------------
	// Taxonomy search filter
	//-----------------------

	/**
	* wpv_filters_add_filter_taxonomy_search
	*
	* Register the search filter in the popup dialog
	*
	* @param $filters
	*
	* @since unknown
	*/
	
	static function wpv_filters_add_filter_taxonomy_search( $filters ) {
		$filters['taxonomy_search'] = array(
			'name' => __( 'Taxonomy search', 'wpv-views' ),
			'present' => 'taxonomy_search_mode',
			'callback' => array( 'WPV_Search_Filter', 'wpv_add_new_filter_taxonomy_search_list_item' ),
			'group' => __( 'Taxonomy filters', 'wpv-views' )
		);
		return $filters;
	}
	
	/**
	* wpv_add_new_filter_taxonomy_search_list_item
	*
	* Register the taxonomy search filter in the filters list
	*
	* @since unknown
	*/

	static function wpv_add_new_filter_taxonomy_search_list_item() {
		$args = array(
			'taxonomy_search_mode' => array('specific')
		);
		WPV_Search_Filter::wpv_add_filter_taxonomy_search_list_item( $args );
	}
	
	/**
	* wpv_add_filter_taxonomy_search_list_item
	*
	* Render taxonomy search filter item in the filters list
	*
	* @param $view_settings
	*
	* @since unknown
	*/

	static function wpv_add_filter_taxonomy_search_list_item( $view_settings ) {
		if ( isset( $view_settings['taxonomy_search_mode'] ) ) {
			$li = WPV_Search_Filter::wpv_get_list_item_ui_taxonomy_search( '', $view_settings );
			WPV_Filter_Item::simple_filter_list_item( 'taxonomy_search', 'taxonomies', 'taxonomy-search', __( 'Taxonomy search filter', 'wpv-views' ), $li );
		}
	}
	
	/**
	* wpv_get_list_item_ui_taxonomy_search
	*
	* Render taxonomy search filter item content in the filters list
	*
	* @param $view_settings
	*
	* @since unknown
	*/

	static function wpv_get_list_item_ui_taxonomy_search( $selected, $view_settings = array() ) {
		if ( isset( $view_settings['taxonomy_search_mode'] ) && is_array( $view_settings['taxonomy_search_mode'] ) ) {
			$view_settings['taxonomy_search_mode'] = $view_settings['taxonomy_search_mode'][0];
		}
		if ( !isset( $view_settings['taxonomy_search_value'] ) ) {
			$view_settings['taxonomy_search_value'] = '';
		}
		ob_start();
		?>
		<p class='wpv-filter-taxonomy-search-summary js-wpv-filter-summary js-wpv-filter-taxonomy-search-summary'>
			<?php echo wpv_get_filter_taxonomy_search_summary_txt( $view_settings ); ?>
		</p>
		<?php
		WPV_Filter_Item::simple_filter_list_item_buttons( 'taxonomy-search', 'wpv_filter_taxonomy_search_update', wp_create_nonce( 'wpv_view_filter_taxonomy_search_nonce' ), 'wpv_filter_taxonomy_search_delete', wp_create_nonce( 'wpv_view_filter_taxonomy_search_delete_nonce' ) );
		?>
		<div id="wpv-filter-taxonomy-search-edit" class="wpv-filter-edit js-wpv-filter-edit">
			<div id="wpv-filter-taxonomy-search" class="js-wpv-filter-options js-wpv-filter-taxonomy-search-options">
				<?php WPV_Search_Filter::wpv_render_taxonomy_search_options( $view_settings ); ?>
			</div>
			<div class="js-wpv-filter-toolset-messages"></div>
		</div>
		<?php
		$res = ob_get_clean();
		return $res;
	}

	/**
	* wpv_filter_taxonomy_search_update_callback
	*
	* Update taxonomy search filter callback
	*
	* @since unknown
	*/

	static function wpv_filter_taxonomy_search_update_callback() {
		if ( ! current_user_can( 'manage_options' ) ) {
			$data = array(
				'type' => 'capability',
				'message' => __( 'You do not have permissions for that.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		if ( 
			! isset( $_POST["wpnonce"] )
			|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_filter_taxonomy_search_nonce' ) 
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
		$view_id = $_POST['id'];
		parse_str( $_POST['filter_options'], $filter_search );
		$view_array = get_post_meta( $view_id, '_wpv_settings', true );
		if ( ! isset( $filter_search['taxonomy_search_value'] ) ) {
			$filter_search['taxonomy_search_value'] = '';
		}
		$settings_to_check = array(
			'taxonomy_search_mode',
			'taxonomy_search_value'
		);
		foreach ( $settings_to_check as $set ) {
			if ( 
				isset( $filter_search[$set] ) 
				&& (
					! isset( $view_array[$set] ) 
					|| $filter_search[$set] != $filter_search[$set] 
				)
			) {
				if ( is_array( $filter_search[$set] ) ) {
					$filter_search[$set] = array_map( 'sanitize_text_field', $filter_search[$set] );
				} else {
					$filter_search[$set] = sanitize_text_field( $filter_search[$set] );
				}
				$change = true;
				$view_array[$set] = $filter_search[$set];
			}
		}
		if ( $change ) {
			$result = update_post_meta( $view_id, '_wpv_settings', $view_array );
			do_action( 'wpv_action_wpv_save_item', $view_id );
		}
		$filter_search['taxonomy_search_mode'] = $filter_search['taxonomy_search_mode'][0];
		$data = array(
			'id' => $view_id,
			'message' => __( 'Taxonomy filter saved', 'wpv-views' ),
			'summary' => wpv_get_filter_taxonomy_search_summary_txt( $filter_search )
		);
		wp_send_json_success( $data );
	}
	
	/**
	* Update taxonomy search filter summary callback
	*/

	static function wpv_filter_taxonomy_search_sumary_update_callback() {
		$nonce = $_POST["wpnonce"];
		if ( ! wp_verify_nonce( $nonce, 'wpv_view_filter_taxonomy_search_nonce' ) ) {
			die( "Security check" );
		}
		parse_str( $_POST['filter_options'], $filter_search );
		$filter_search['taxonomy_search_mode'] = $filter_search['taxonomy_search_mode'][0];
		if ( ! isset( $filter_search['taxonomy_search_value'] ) ) {
			$filter_search['taxonomy_search_value'] = '';
		}
		echo wpv_get_filter_taxonomy_search_summary_txt( $filter_search );
		die();
	}
	
	/**
	* wpv_filter_taxonomy_search_delete_callback
	*
	* Delete taxonomy search filter callback
	*
	* @since unknown
	*/

	static function wpv_filter_taxonomy_search_delete_callback() {
		if ( ! current_user_can( 'manage_options' ) ) {
			$data = array(
				'type' => 'capability',
				'message' => __( 'You do not have permissions for that.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		if ( 
			! isset( $_POST["wpnonce"] )
			|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_filter_taxonomy_search_delete_nonce' ) 
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
		if ( isset( $view_array['taxonomy_search_mode'] ) ) {
			unset( $view_array['taxonomy_search_mode'] );
		}
		if ( isset( $view_array['taxonomy_search_value'] ) ) {
			unset( $view_array['taxonomy_search_value'] );
		}
		update_post_meta( $_POST["id"], '_wpv_settings', $view_array );
		do_action( 'wpv_action_wpv_save_item', $_POST["id"] );
		$data = array(
			'id' => $_POST["id"],
			'message' => __( 'Taxonomy search filter deleted', 'wpv-views' )
		);
		wp_send_json_success( $data );
	}

	/**
	* wpv_render_post_search_options
	*
	* Render search filter options
	*
	* @param $view_settings
	*
	* @since unknown
	*/

	static function wpv_render_post_search_options( $view_settings = array() ) {
		$defaults = array(
			'search_mode' => 'specific',
			'post_search_value' => '',
			'post_search_content' => 'full_content'
		);
		$view_settings = wp_parse_args( $view_settings, $defaults );
		?>
		<h4><?php _e( 'How to search', 'wpv-views' ); ?></h4>
		<ul class="wpv-filter-options-set">
			<li>
				<input type="radio" id="wpv-search-mode-specific" class="js-wpv-post-search-mode" name="search_mode[]" value="specific" <?php checked( $view_settings['search_mode'], 'specific' ); ?> />
				<label for="wpv-search-mode-specific"><?php _e( 'Search for a specific text:', 'wpv-views' ); ?></label>
				<!-- MAYBE DEPRECATED -->
				<input type="hidden" name="filter_by_search" value="1"/>
				<!-- it was used to prevent duplications, not used anymore I think -->
				<input type='text' name="post_search_value" value="<?php echo esc_attr( $view_settings['post_search_value'] ); ?>" />
			</li>
			<li>
				<?php $checked = ( $view_settings['search_mode'] == 'manual' || $view_settings['search_mode'] == 'visitor' ) ? 'checked="checked"' : ''; ?>
				<input type="radio" id="wpv-search-mode-manual" class="js-wpv-post-search-mode" name="search_mode[]" value="manual" <?php echo $checked; ?> />
				<label for="wpv-search-mode-manual"><?php _e( 'I’ll add the search box to the HTML manually', 'wpv-views' ); ?></label>
			</li>
		</ul>
		<h4><?php _e('Where to search', 'wpv-views'); ?></h4>
		<ul class="wpv-filter-options-set">
			<li>
				<input type="radio" id="wpv-search-content-full" name="post_search_content" value="full_content"<?php if ( $view_settings['post_search_content'] == 'full_content' ) echo ' checked="checked"';?>>
				<label for="wpv-search-content-full"><?php echo __( 'Posts content and title', 'wpv-views' ); ?></label>
			</li>
			<li>
				<input type="radio" id="wpv-search-content-title" name="post_search_content" value="just_title"<?php if ( $view_settings['post_search_content'] == 'just_title' ) echo ' checked="checked"';?>>
				<label for="wpv-search-content-title"><?php echo __( 'Just posts title', 'wpv-views' ); ?></label>
			</li>
		</ul>
		<?php
	}

	/**
	* wpv_render_taxonomy_search_options
	*
	* Render taxonomy search filter options
	*
	* @param $args
	*
	* @since unknown
	*/

	static function wpv_render_taxonomy_search_options( $view_settings = array() ) {
		$defaults = array(
			'taxonomy_search_mode' => 'specific',
			'taxonomy_search_value' => ''
		);
		$view_settings = wp_parse_args( $view_settings, $defaults );
		?>
			<h4><?php _e( 'How to search', 'wpv-views' ); ?></h4>
			<ul class="wpv-filter-options-set">
				<li>
					<input type="radio" id="wpv-taxonomy-search-mode-specific" name="taxonomy_search_mode[]" value="specific" <?php checked( $view_settings['taxonomy_search_mode'], 'specific' ); ?> />
					<label for="wpv-taxonomy-search-mode-specific"><?php _e( 'Search for a specific text:', 'wpv-views' ); ?></label>
					<input type='text' name="taxonomy_search_value" value="<?php echo esc_attr( $view_settings['taxonomy_search_value'] ); ?>" />
				</li>
				<li>
					<?php $checked = ( $view_settings['taxonomy_search_mode'] == 'manual' || $view_settings['taxonomy_search_mode'] == 'visitor' ) ? 'checked="checked"' : ''; ?>
					<input type="radio" id="wpv-taxonomy-search-mode-manual" name="taxonomy_search_mode[]" value="manual" <?php echo $checked; ?> />
					<label for="wpv-taxonomy-search-mode-manual"><?php _e( 'I’ll add the search box to the HTML manually', 'wpv-views' ); ?></label>
				</li>
			</ul>
		<?php
	}

}

/**
* wpv_search_get_url_params
*
* This is for Gen's function to get available URL params
*
* @param $view_settings
*
* @since unknown
*/

function wpv_search_get_url_params( $view_settings ) {
	if ( isset( $view_settings['search_mode'][0]) && $view_settings['search_mode'][0] == 'visitor' ) {
		return array(
			array(
				'name' => __( 'Search' , 'wpv-views' ),
				'param' => 'wpv_post_search',
				'mode' => 'search'
			)
		);
	} else {
		return array();
	}
}

/**
*
* NOTE this seems to be used only in the old Filter Controls table initialization
*/

function wpv_filter_search_js() {
	?>
	
    <script type="text/javascript">
		var wpv_search_text = '<?php echo esc_js(__('Search', 'wpv-views')); ?>';
	</script>
	
	<?php
}