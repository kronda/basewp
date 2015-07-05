<?php

/**
* Post date filter
*
* @package Views
*
* @since 1.8.0
*/

WPV_Date_Filter::on_load();

/**
* WPV_Date_Filter
*
* Views Date Filter Class
*
* @since 1.8.0
*/

class WPV_Date_Filter {

    private static $date_operator;
	private static $date_options;
	private static $date_columns;
	
	static function on_load() {
        self::$date_operator = array(
			'single' => array(
				'=' => __( 'equal to', 'wpv-views' ),
				'!=' => __( 'different from', 'wpv-views' ),
				'<' => __( 'before', 'wpv-views' ),
				'<=' => __( 'before or equal to', 'wpv-views' ),
				'>' => __( 'after', 'wpv-views' ),
				'>=' => __( 'after or equal to', 'wpv-views' )
			),
			'group' => array(
				'IN' => __( 'in any of those', 'wpv-views' ),
				'NOT IN' => __( 'not in any of those', 'wpv-views' ),
				'BETWEEN' => __( 'between those', 'wpv-views' ),
				'NOT BETWEEN' => __( 'not between those', 'wpv-views' )
			),
		);
		self::$date_options = array(
			'year' => __( 'Year', 'wpv-views' ),
			'month' => __( 'Month', 'wpv-views' ),
			'week' => __( 'Week', 'wpv-views' ),
			'day' => __( 'Day', 'wpv-views' ),
			'dayofyear' => __( 'Day of the year', 'wpv-views' ),
			'dayofweek' => __( 'Day of the week', 'wpv-views' ),
			'hour' => __( 'Hour', 'wpv-views' ),
			'minute' => __( 'Minute', 'wpv-views' ),
			'second' => __( 'Second', 'wpv-views' )
		);
		self::$date_columns = array(
			'post_date' => __( 'published date', 'wpv-views' ),
			'post_date_gmt' => __( 'published date GMT', 'wpv-views' ),
			'post_modified' => __( 'modified date', 'wpv-views' ),
			'post_modified_gmt' => __( 'modified date GMT', 'wpv-views' )
		);
		add_action( 'init', array( 'WPV_Date_Filter', 'init' ) );
		add_action( 'admin_init', array( 'WPV_Date_Filter', 'admin_init' ) );
    }

    static function init() {
		
    }
	
	static function admin_init() {
		// Register filter in lists and dialogs
		add_filter( 'wpv_filters_add_filter', array( 'WPV_Date_Filter', 'wpv_filters_add_filter_post_date' ), 1, 1 );
		add_action( 'wpv_add_filter_list_item', array( 'WPV_Date_Filter', 'wpv_add_filter_post_date_list_item' ), 1, 1 );
		// AJAX calbacks
		add_action( 'wp_ajax_wpv_filter_post_date_update', array( 'WPV_Date_Filter', 'wpv_filter_post_date_update_callback' ) );
			// TODO This might not be needed here, maybe for summary filter
			add_action( 'wp_ajax_wpv_filter_date_sumary_update', array( 'WPV_Date_Filter', 'wpv_filter_post_date_sumary_update_callback' ) );
		add_action( 'wp_ajax_wpv_filter_post_date_delete', array( 'WPV_Date_Filter', 'wpv_filter_post_date_delete_callback' ) );
		add_filter( 'wpv-view-get-summary', array( 'WPV_Date_Filter', 'wpv_post_date_summary_filter' ), 5, 3 );
		add_action( 'wp_ajax_wpv_filter_post_date_add_condition', array( 'WPV_Date_Filter', 'wpv_filter_post_date_add_condition' ) );
		// Register scripts
		add_action( 'admin_enqueue_scripts', array( 'WPV_Date_Filter','admin_enqueue_scripts' ), 20 );
	}
	
	/**
	* admin_enqueue_scripts
	*
	* Register the needed script for this filter
	*
	* @since 1.8.0
	*/
	
	static function admin_enqueue_scripts( $hook ) {
		wp_register_script( 'views-filter-date-js', ( WPV_URL . "/res/js/redesign/views_filter_date.js" ), array( 'views-filters-js'), WPV_VERSION, true );
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'views-editor' ) {
			wp_enqueue_script( 'views-filter-date-js' );
		}
	}
	
	/**
	* wpv_filters_add_filter_post_date
	*
	* Register the date filter in the popup dialog
	*
	* @param $filters
	*
	* @since 1.8.0
	*/
	
	static function wpv_filters_add_filter_post_date( $filters ) {
		$filters['post_date'] = array(
			'name' => __( 'Post date', 'wpv-views' ),
			'present' => 'date_filter',
			'callback' => array( 'WPV_Date_Filter', 'wpv_add_new_filter_post_date_list_item' ),
			'group' => __( 'Post filters', 'wpv-views' )
		);
		return $filters;
	}
	
	/**
	* wpv_add_new_filter_post_date_list_item
	*
	* Register the date filter in the filters list
	*
	* @since 1.8.0
	*/

	static function wpv_add_new_filter_post_date_list_item() {
		$args = array(
			'date_filter' => array( 
				'date_relation' => 'AND',
				'date_conditions' => array(
					'date_condition_0' => array(
						'date_operator' => '=',
						'date_column' => 'post_date',
						'date_multiple_selected' => 'year',
						'year' => '',
						'month' => '',
						'week' => '',
						'day' => '',
						'dayofyear' => '',
						'dayofweek' => '',
						'hour' => '',
						'minute' => '',
						'second' => ''
					)
				)
			)
		);
		WPV_Date_Filter::wpv_add_filter_post_date_list_item( $args );
	}
	
	/**
	* wpv_add_filter_post_date_list_item
	*
	* Render date filter item in the filters list
	*
	* @param $view_settings
	*
	* @since 1.8.0
	*/

	static function wpv_add_filter_post_date_list_item( $view_settings ) {
		if ( isset( $view_settings['date_filter'] ) ) {
			$li = WPV_Date_Filter::wpv_get_list_item_ui_post_date( $view_settings );
			WPV_Filter_Item::multiple_filter_list_item( 'post-date', 'posts', __( 'Post date filter', 'wpv-views' ), $li );
		}
	}
	
	/**
	* wpv_get_list_item_ui_post_date
	*
	* Render date filter item content in the filters list
	*
	* @param $view_settings
	*
	* @since 1.8.0
	*/

	static function wpv_get_list_item_ui_post_date( $view_settings = array() ) {
		ob_start();
		?>
		<p class='wpv-filter-post-date-edit-summary js-wpv-filter-summary js-wpv-filter-post-date-summary'>
			<?php echo wpv_get_filter_post_date_summary_txt( $view_settings ); ?>
		</p>
		<?php
		WPV_Filter_Item::filter_list_item_buttons( 'post-date', 'wpv_filter_post_date_update', wp_create_nonce( 'wpv_view_filter_post_date_nonce' ), 'wpv_filter_post_date_delete', wp_create_nonce( 'wpv_view_filter_post_date_delete_nonce' ) );
		?>
		<div id="wpv-filter-post-date-edit" class="wpv-filter-edit js-wpv-filter-edit" style="padding-bottom:28px;">
			<div id="wpv-filter-post-date" class="js-wpv-filter-options js-wpv-filter-post-date-options">
				<?php WPV_Date_Filter::wpv_render_post_date_options( $view_settings ); ?>
			</div>
			<div class="js-wpv-filter-multiple-toolset-messages"></div>
			<span class="filter-doc-help">
				<?php echo sprintf(__('%sLearn about filtering by Post Date%s', 'wpv-views'),
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
	* wpv_filter_post_date_update_callback
	*
	* Update date filter callback
	*
	* @since 1.8.0
	*/

	static function wpv_filter_post_date_update_callback() {
		if ( ! current_user_can( 'manage_options' ) ) {
			$data = array(
				'type' => 'capability',
				'message' => __( 'You do not have permissions for that.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		if ( 
			! isset( $_POST["wpnonce"] )
			|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_filter_post_date_nonce' ) 
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
		if ( 
			! isset( $_POST['date_filter'] ) 
			|| empty( $_POST['date_filter'] ) 
		) {
			$data = array(
				'type' => 'data_missing',
				'message' => __( 'Wrong or missing data.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		$view_id = intval( $_POST['id'] );
		$date_relation = isset( $_POST['date_relation'] ) ? sanitize_text_field( $_POST['date_relation'] ) : 'AND';
		$new_date_conditions = array();
		$date_conditions = $_POST['date_filter'];
		if ( is_array( $date_conditions ) ) {
			foreach ( $date_conditions as $date_key => $date_condition ) {
				$date_key = sanitize_text_field( $date_key );
				parse_str( $date_condition, $date_cond );
				$new_date_conditions['date_condition_' . $date_key] = array();
				foreach ( self::$date_options as $date_name => $date_title ) {
					if ( isset( $date_cond['wpv-' . $date_name] ) ) {
						$new_date_conditions['date_condition_' . $date_key][$date_name] = sanitize_text_field( $date_cond['wpv-' . $date_name] );
					} else {
						$new_date_conditions['date_condition_' . $date_key][$date_name] = '';
					}
				}
				// Date column
				if ( 
					isset( $date_cond['date_column'] ) 
					&& ! empty( $date_cond['date_column'] )
				) {
					$new_date_conditions['date_condition_' . $date_key]['date_column'] = sanitize_text_field( $date_cond['date_column'] );
				} else {
					$new_date_conditions['date_condition_' . $date_key]['date_column'] = 'post_date';
				}
				// Date operator
				if ( 
					isset( $date_cond['date_operator'] ) 
					&& ! empty( $date_cond['date_operator'] )
				) {
					$new_date_conditions['date_condition_' . $date_key]['date_operator'] = sanitize_text_field( $date_cond['date_operator'] );
				} else {
					$new_date_conditions['date_condition_' . $date_key]['date_operator'] = '=';
				}
				// Date multiple selected
				$override_multiple = false;
				if ( 
					isset( $date_cond['date_multiple_selected'] ) 
					&& ! empty( $date_cond['date_multiple_selected'] )
					&& isset( self::$date_options[$date_cond['date_multiple_selected']] )
				) {
					$new_date_conditions['date_condition_' . $date_key]['date_multiple_selected'] = sanitize_text_field( $date_cond['date_multiple_selected'] );
					$override_multiple = true;
				} else {
					$new_date_conditions['date_condition_' . $date_key]['date_multiple_selected'] = 'year';
				}
				// Adjust multiple when needed
				if ( 
					isset( self::$date_operator['group'][$new_date_conditions['date_condition_' . $date_key]['date_operator']] ) 
					&& $override_multiple
				) {
					if ( 
						isset( $date_cond['date_multiple_value'] ) 
						&& ! empty( $date_cond['date_multiple_value'] )
					) {
						$new_date_conditions['date_condition_' . $date_key][$new_date_conditions['date_condition_' . $date_key]['date_multiple_selected']] = sanitize_text_field( $date_cond['date_multiple_value'] );
					} else {
						$new_date_conditions['date_condition_' . $date_key][$new_date_conditions['date_condition_' . $date_key]['date_multiple_selected']] = '';
					}
				}
			}
		}
		$view_array['date_filter'] = array(
			'date_relation' => $date_relation,
			'date_conditions' => $new_date_conditions
		);
		update_post_meta( $view_id, '_wpv_settings', $view_array );
		do_action( 'wpv_action_wpv_save_item', $view_id );
		$data = array(
			'id' => $view_id,
			'message' => __( 'Post date filter saved', 'wpv-views' ),
			'summary' => wpv_get_filter_post_date_summary_txt( $view_array )
		);
		wp_send_json_success( $data );
	}
	
	/**
	* Update date filter summary callback
	*/

	static function wpv_filter_post_date_sumary_update_callback() {
		/*
		$nonce = $_POST["wpnonce"];
		if ( ! wp_verify_nonce( $nonce, 'wpv_view_filter_author_nonce' ) ) {
			die( "Security check" );
		}
		parse_str( $_POST['filter_author'], $filter_author );
		$filter_author['author_mode'] = $filter_author['author_mode'][0];
		echo wpv_get_filter_post_date_summary_txt( $filter_author );
		*/
		die();
	}
	
	/**
	* wpv_filter_post_date_delete_callback
	*
	* Delete date filter callback
	*
	* @since 1.8.0
	*/

	static function wpv_filter_post_date_delete_callback() {
		if ( ! current_user_can( 'manage_options' ) ) {
			$data = array(
				'type' => 'capability',
				'message' => __( 'You do not have permissions for that.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		if ( 
			! isset( $_POST["wpnonce"] )
			|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_filter_post_date_delete_nonce' ) 
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
		if ( isset( $view_array['date_filter'] ) ) {
			unset( $view_array['date_filter'] );
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
	* wpv_post_date_summary_filter
	
	* Show the date filter on the View summary
	*
	* @since 1.8.0
	*/

	static function wpv_post_date_summary_filter( $summary, $post_id, $view_settings ) {
		if ( 
			isset( $view_settings['query_type'] ) 
			&& $view_settings['query_type'][0] == 'posts' 
			&& isset( $view_settings['date_filter'] ) 
		) {
			$result = wpv_get_filter_post_date_summary_txt( $view_settings );
			if ( $result != '' && $summary != '' ) {
				$summary .= '<br />';
			}
			$summary .= $result;
		}
		return $summary;
	}

	/**
	* wpv_render_post_date_options
	*
	* Render date filter options
	*
	* @param $view_settings
	*
	* @since 1.8.0
	*/

	static function wpv_render_post_date_options( $view_settings = array() ) {
		$defaults = array(
			'date_filter' => array( 
				'date_relation' => 'AND',
				'date_conditions' => array(
					'date_condition_0' => array(
						'date_operator' => '=',
						'date_column' => 'post_date',
						'date_multiple_selected' => 'year',
						'year' => '',
						'month' => '',
						'week' => '',
						'day' => '',
						'dayofyear' => '',
						'dayofweek' => '',
						'hour' => '',
						'minute' => '',
						'second' => ''
					)
				)
			)
		);
		if ( 
			! isset( $view_settings['date_filter'] ) 
			|| ! is_array( $view_settings['date_filter'] )
		) {
			$view_settings['date_filter'] = $defaults['date_filter'];
		} else {
			if ( ! isset( $view_settings['date_filter']['date_relation'] ) ) {
				$view_settings['date_filter']['date_relation'] = 'AND';
			}
			if (
				! isset( $view_settings['date_filter']['date_conditions'] ) 
				|| ! is_array( $view_settings['date_filter']['date_conditions'] )
			) {
				$view_settings['date_filter']['date_conditions'] = $defaults['date_filter']['date_conditions'];
			}
		}
		//$view_settings = wp_parse_args( $view_settings, $defaults );
		foreach ( $view_settings['date_filter']['date_conditions'] as $date_condition ) {
			WPV_Date_Filter::wpv_render_post_date_condition( $date_condition );
		}
		?>
		<p>
			<button class="button button-secondary js-wpv-date-filter-add-condition"><?php _e( 'Add another date condition', 'wpv-views' ); ?></button>
		</p>
		<div class="wpv-filter-post-date-relationship wpv-filter-multiple-element js-wpv-filter-post-date-relationship<?php if ( count( $view_settings['date_filter']['date_conditions'] ) < 2 ) { echo ' hidden'; } ?>">
			<h4><?php _e('Date conditions relationship:', 'wpv-views') ?></h4>
			<div class="wpv-filter-multiple-element-options">
				<?php _e('Relationship to use when querying with multiple date conditions:', 'wpv-views'); ?>
				<select name="date_relation" id="js-wpv-filter-date-relation" autocomplete="off">
					<option value="AND" <?php selected( 'AND', $view_settings['date_filter']['date_relation'] ); ?>><?php _e( 'AND', 'wpv-views' ); ?></option>
					<option value="OR"  <?php selected( 'OR', $view_settings['date_filter']['date_relation'] ); ?>><?php _e( 'OR', 'wpv-views' ); ?></option>
				</select>
			</div>
		</div>
		<div class="filter-helper js-wpv-author-helper"></div>
		<?php
	}
	
	/**
	* wpv_render_post_date_condition
	*
	* Render date filter options
	*
	* @param $view_settings
	*
	* @since 1.8.0
	*/
	
	static function wpv_render_post_date_condition( $date_condition ) {
		if (
			is_array( $date_condition )
			&& isset ( $date_condition['date_operator'] )
		) {
		?>
		<div class="wpv-filter-multiple-element js-wpv-filter-multiple-element js-wpv-date-condition">
			<h4><?php _e( 'Date condition', 'wpv-views' ); ?></h4>
			<span class="wpv-filter-multiple-element-delete">
				<button class="button button-secondary button-small js-filter-remove js-wpv-date-condition-remove">
					<i class="icon-trash"></i>&nbsp;<?php _e( 'Delete', 'wpv-views' ); ?>
				</button>
			</span>
			<div class="wpv-filter-multiple-element-options js-wpv-date-condition-options">
				<?php 
				_e( 'Select posts with', 'wpv-views' );
				?>
				<select class="js-wpv-date-condition-column" name="date_column" autocomplete="off">
				<?php
				foreach ( self::$date_columns as $column_name => $column_title ) {
					echo '<option value="' . esc_attr( $column_name ) . '" ' . selected( $column_name, $date_condition['date_column'], false ) . '>' . $column_title . '</option>';
				}
				?>
				</select>
				<select class="js-wpv-date-condition-operator" name="date_operator" autocomplete="off">
					<?php
						foreach ( self::$date_operator['single'] as $single_compare => $single_name ) {
							?>
							<option value="<?php echo esc_attr( $single_compare ); ?>" <?php selected( $single_compare, $date_condition['date_operator'] ); ?>><?php echo $single_name; ?></option>
							<?php
						}
						foreach ( self::$date_operator['group'] as $group_compare => $group_name ) {
							?>
							<option value="<?php echo esc_attr( $group_compare ); ?>" <?php selected( $group_compare, $date_condition['date_operator'] ); ?>><?php echo $group_name; ?></option>
							<?php
						}
						?>
						<?php
					?>
				</select>
				<ul>
					<li class="js-wpv-date-condition-single<?php if ( ! isset( self::$date_operator['single'][$date_condition['date_operator']] ) ) { echo ' hidden'; } ?>">
						<table style="margin:5px 0;">
						<?php
						foreach ( self::$date_options as $date_name => $date_title ) {
							$this_options = explode( ',', $date_condition[$date_name] );
							$this_function_value = WPV_Filter_Item::get_custom_filter_function_and_value( $this_options[0] );
							?>
							<tr class="js-wpv-filter-date-condition-combo-value<?php if ( empty( $this_function_value['function'] ) ) { echo ' unused'; } ?>">
								<td>
									<?php echo $date_title; ?>
								</td>
								<td>
									<?php
									echo WPV_Date_Filter::wpv_get_date_origin_dropdown( $this_function_value['function'] );
									?>
								</td>
								<td>
									<?php
									$extra_classname = '';
									if ( $this_function_value['function'] == 'current_one' ) {
										$extra_classname = ' hidden';
									}
									?>
									<input type="text" name="wpv-date-condition-single-<?php echo esc_attr( $date_name ); ?>" class="js-wpv-element-not-serialize js-wpv-filter-date-data <?php echo esc_attr( $extra_classname ); ?> js-wpv-filter-date-<?php echo esc_attr( $date_name ); ?>" data-combotype="<?php echo esc_attr( $date_name ); ?>" value="<?php echo esc_attr( $this_function_value['value'] ); ?>" autocomplete="off" />
									<input type="hidden" name="wpv-<?php echo esc_attr( $date_name ); ?>" class="js-wpv-filter-date-data-real" value="<?php echo esc_attr( $this_options[0] ); ?>" autocomplete="off" />
								</td>
							</tr>
							<?php
						}
						?>
						</table>
					</li>
					<li class="js-wpv-date-condition-group<?php if ( ! isset( self::$date_operator['group'][$date_condition['date_operator']] ) ) { echo ' hidden'; } ?>">
						<select class="js-wpv-date-condition-group-selected" name="date_multiple_selected" style="margin:5px 0 0;" autocomplete="off">
						<?php
						foreach ( self::$date_options as $date_name => $date_title ) {
							echo '<option value="' . esc_attr( $date_name ) . '" ' . selected( $date_name, $date_condition['date_multiple_selected'], false ) . '>' . $date_title . '</option>';
						}
						?>
						</select>
						<input type="hidden" name="date_multiple_value" class="js-wpv-filter-date-data-real" value="<?php echo esc_attr( $date_condition[$date_condition['date_multiple_selected']] ); ?>" autocomplete="off" />
						<?php
						$this_options = explode( ',', $date_condition[$date_condition['date_multiple_selected']] );
						$show_buttons = in_array( $date_condition['date_operator'], array( 'IN', 'NOT IN' ) );
						foreach ( $this_options as $this_option_key => $this_option_val ) {
							$this_option_function_and_value = WPV_Filter_Item::get_custom_filter_function_and_value( $this_option_val );
							?>
							<div class="wpv-filter-date-condition-group-item js-wpv-filter-date-condition-combo-value js-wpv-filter-date-condition-group-value" style="margin:5px 0;">
							<?php
							echo WPV_Date_Filter::wpv_get_date_origin_dropdown( $this_option_function_and_value['function'] );
							$extra_classname = '';
							if ( $this_option_function_and_value['function'] == 'current_one' ) {
								$extra_classname = ' hidden';
							}
							?>
							<input type="text" name="wpv-date-condition-group-data" class="js-wpv-element-not-serialize js-wpv-filter-date-data <?php echo esc_attr( $extra_classname ); ?>" data-combotype="group" value="<?php echo esc_attr( $this_option_function_and_value['value'] ); ?>" autocomplete="off" />
							<button class="button button-secondary js-wpv-date-condition-group-value-delete <?php if ( ! $show_buttons || $this_option_key == 0 ) { echo 'hidden'; } ?>"><i class="icon-remove"></i> <?php _e( 'Remove', 'wpv-views' ); ?></button>
							</div>
							<?php
						}
						?>
						<button style="margin-top:5px;" class="button button-secondary js-wpv-date-condition-group-value-add <?php if ( ! $show_buttons ) { echo 'hidden'; } ?>"><i class="icon-plus"></i> <?php _e( 'Add another option', 'wpv-views' ); ?></button>
					</li>
				</ul>
			</div>
			<div class="js-wpv-filter-toolset-messages"></div>
		</div>
		<?php
		}
	}
	
	/**
	* wpv_get_date_origin_dropdown
	*
	* Display the date origin dropdown
	*
	* @since 1.8.0
	*/
	
	static function wpv_get_date_origin_dropdown( $selected = 'constant' ) {
		$extra_values_options = array(
			'current_one' => __( 'CURRENT_ONE', 'wpv-views' ),
			'future_one' => __( 'FUTURE_ONE', 'wpv-views' ),
			'past_one' => __( 'PAST_ONE', 'wpv-views' )
		);
		$origins = '<select class="js-wpv-element-not-serialize js-wpv-filter-date-origin" name="wpv-date-condition-origin" autocomplete="off">';
		$origins .= '<option data-group="basic" value="constant" ' . selected( $selected, 'constant', false ) . '>' . __( 'Constant', 'wpv-views' ) . '</option>';
		$origins .= '<option data-group="basic" value="attribute" ' . selected( $selected, 'attribute', false ) . '>' . __( 'Shortcode attribute', 'wpv-views' ) . '</option>';
		$origins .= '<option data-group="basic" value="url" ' . selected( $selected, 'url', false ) . '>' . __( 'URL parameter' , 'wpv-views' ) . '</option>';
		foreach ( $extra_values_options as $extra_key => $extra_value) {
			$origins .= '<option data-group="generic" value="' . esc_attr( $extra_key ) . '" ' . selected( $selected, $extra_key, false ) . '>' . $extra_value . '</option>';
		}
		$origins .= '</select>';
		return $origins;
	}
	
	/**
	* wpv_filter_post_date_add_condition
	*
	* AJAX callback to add a new post date condition*
	*
	* @since 1.8.0
	*/
	
	static function wpv_filter_post_date_add_condition() {
		// @todo add nonce here
		$default_conditions = array(
			'date_operator' => '=',
			'date_column' => 'post_date',
			'date_multiple_selected' => 'year',
			'year' => '',
			'month' => '',
			'week' => '',
			'day' => '',
			'dayofyear' => '',
			'dayofweek' => '',
			'hour' => '',
			'minute' => '',
			'second' => ''
		);
		WPV_Date_Filter::wpv_render_post_date_condition( $default_conditions );
		die();
	}

}
