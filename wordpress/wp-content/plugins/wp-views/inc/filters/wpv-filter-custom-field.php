<?php

/**
* Custom Field filter
*
* @package Views
*
* @since unknown
*/

WPV_Custom_Field_Filter::on_load();

/**
* WPV_Custom_Field_Filter
*
* Views Custom Field Filter Class
*
* @since 1.7.0
*/

class WPV_Custom_Field_Filter {

    static function on_load() {
        add_action( 'init', array( 'WPV_Custom_Field_Filter', 'init' ) );
		add_action( 'admin_init', array( 'WPV_Custom_Field_Filter', 'admin_init' ) );
    }

    static function init() {
		
    }
	
	static function admin_init() {
		// Register filters in lists and dialogs
		add_filter( 'wpv_filters_add_filter', array( 'WPV_Custom_Field_Filter', 'wpv_filters_add_filter_custom_field' ), 20, 2 );
		add_action( 'wpv_add_filter_list_item', array( 'WPV_Custom_Field_Filter', 'wpv_add_filter_custom_field_list_item' ), 1, 1 );
		//AJAX callbakcks
		add_action( 'wp_ajax_wpv_filter_custom_field_update', array( 'WPV_Custom_Field_Filter', 'wpv_filter_custom_field_update_callback' ) );
		add_action( 'wp_ajax_wpv_filter_custom_field_delete', array( 'WPV_Custom_Field_Filter', 'wpv_filter_custom_field_delete_callback' ) );
		add_filter( 'wpv-view-get-summary', array( 'WPV_Custom_Field_Filter', 'wpv_custom_field_summary_filter' ), 7, 3 );
		// Register scripts
		wp_register_script( 'views-filter-custom-field-js', ( WPV_URL . "/res/js/redesign/views_filter_custom_field.js" ), array( 'views-filters-js'), WPV_VERSION, true );
		$filter_texts = array(
			'dialog_title'		=> __( 'Delete custom field filters', 'wpv-views' ),
			'cancel'			=> __( 'Cancel', 'wpv-views' ),
			'edit_filters'		=> __( 'Edit the custom field filters', 'wpv-views' ),
			'delete_filters'	=> __( 'Delete all custom field filters', 'wpv-views' )
		);
		wp_localize_script( 'views-filter-custom-field-js', 'wpv_custom_field_filter_texts', $filter_texts );
		add_action( 'admin_enqueue_scripts', array( 'WPV_Custom_Field_Filter','admin_enqueue_scripts' ), 20 );
	}
	
	/**
	* admin_enqueue_scripts
	*
	* Register the needed script for this filter
	*
	* @since 1.7
	*/
	
	static function admin_enqueue_scripts( $hook ) {
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'views-editor' ) {
			wp_enqueue_script( 'views-filter-custom-field-js' );
		}
	}
	
	// TODO check what happens with all the _compare, _type and _value when the meta key has a space AND an underscore?

	static function wpv_filters_add_filter_custom_field( $filters ) {
		global $WP_Views;
		$meta_keys = $WP_Views->get_meta_keys();
		$all_types_fields = get_option( 'wpcf-fields', array() );
		foreach ( $meta_keys as $key ) {
			$key_nicename = wpv_types_get_field_name( $key );
			$filters['custom-field-' . str_replace( ' ', '_', $key )] = array(
				'name' => sprintf( __( 'Custom field - %s', 'wpv-views' ), $key_nicename ),
				'present' => 'custom-field-' . $key . '_compare',
				'callback' => array( 'WPV_Custom_Field_Filter', 'wpv_add_new_filter_custom_field_list_item' ),
				'args' => array( 'name' =>'custom-field-' . $key )
			);
		}
		return $filters;
	}

	static function wpv_add_new_filter_custom_field_list_item( $args ) {
		$new_cf_filter_settings = array(
			$args['name'] . '_compare' => '=',
			$args['name'] . '_type' => 'CHAR',
			$args['name'] . '_value' => '',
		);
		WPV_Custom_Field_Filter::wpv_add_filter_custom_field_list_item( $new_cf_filter_settings );
	}

	static function wpv_add_filter_custom_field_list_item( $view_settings ) {
		if ( ! isset($view_settings['custom_fields_relationship'] ) ) {
			$view_settings['custom_fields_relationship'] = 'AND';
		}
		$summary = '';
		$td = '';
		$count = 0;
		foreach ( array_keys( $view_settings ) as $key ) {
			if ( 
				strpos( $key, 'custom-field-' ) === 0 
				&& strpos( $key, '_compare' ) === strlen( $key ) - strlen( '_compare' ) 
			) {
				$name = substr( $key, 0, strlen( $key ) - strlen( '_compare' ) );
				$td .= WPV_Custom_Field_Filter::wpv_get_list_item_ui_post_custom_field( $name, $view_settings );
				$count++;
				if ( $summary != '' ) {
					if ( $view_settings['custom_fields_relationship'] == 'OR' ) {
						$summary .= __( ' OR', 'wpv-views' );
					} else {
						$summary .= __( ' AND', 'wpv-views' );
					}
				}
				$summary .= wpv_get_custom_field_summary( $name, $view_settings );
			}
		}
		if ( $count > 0 ) {
			ob_start();
			WPV_Filter_Item::filter_list_item_buttons( 'custom-field', 'wpv_filter_custom_field_update', wp_create_nonce( 'wpv_view_filter_custom_field_nonce' ), 'wpv_filter_custom_field_delete', wp_create_nonce( 'wpv_view_filter_custom_field_delete_nonce' ) );
			?>
				<?php if ( $summary != '' ) { ?>
					<p class='wpv-filter-custom-field-edit-summary js-wpv-filter-summary js-wpv-filter-custom-field-summary'>
					<?php
					_e('Select posts with custom field: ', 'wpv-views');
					echo $summary;
					?>
					</p>
				<?php } ?>
				<div id="wpv-filter-custom-field-edit" class="wpv-filter-edit js-wpv-filter-edit js-wpv-filter-custom-field-edit js-wpv-filter-options" style="padding-bottom:28px;">
				<?php echo $td; ?>
					<div class="wpv-filter-custom-field-relationship wpv-filter-multiple-element js-wpv-filter-custom-field-relationship-container">
						<h4><?php _e( 'Custom field relationship:', 'wpv-views' ) ?></h4>
						<div class="wpv-filter-multiple-element-options">
							<?php _e( 'Relationship to use when querying with multiple custom fields:', 'wpv-views' ); ?>
							<select name="custom_fields_relationship" class="js-wpv-filter-custom-fields-relationship" autocomplete="off">
								<option value="AND" <?php echo selected( $view_settings['custom_fields_relationship'], 'AND' ); ?>><?php _e('AND', 'wpv-views'); ?>&nbsp;</option>
								<option value="OR" <?php echo selected( $view_settings['custom_fields_relationship'], 'OR' ); ?>><?php _e('OR', 'wpv-views'); ?>&nbsp;</option>
							</select>
						</div>
					</div>
					<div class="js-wpv-filter-multiple-toolset-messages"></div>
					<span class="filter-doc-help">
						<?php 
						echo sprintf(
							__( '%sLearn about filtering by custom fields%s', 'wpv-views' ),
							'<a class="wpv-help-link" href="' . WPV_FILTER_BY_CUSTOM_FIELD_LINK . '" target="_blank">',
							' &raquo;</a>'
						); ?>
					</span>
				</div>
		<?php 
			$li_content = ob_get_clean();
			WPV_Filter_Item::multiple_filter_list_item( 'custom-field', 'posts', __( 'Custom field filter', 'wpv-views' ), $li_content );
		}
	}

	static function wpv_get_list_item_ui_post_custom_field( $type, $view_settings = array() ) {
		$field_name = substr( $type, strlen( 'custom-field-' ) );
		$args = array( 'name' => $field_name );
		if ( ! isset( $view_settings[$type . '_compare'] ) ) {
			$view_settings[$type . '_compare'] = '=';
		}
		if ( ! isset( $view_settings[$type . '_type'] ) ) {
			$view_settings[$type . '_type'] = 'CHAR';
		}
		if ( ! isset( $view_settings[$type . '_value'] ) ) {
			$view_settings[$type . '_value'] = '';
		}
		$field_nicename = wpv_types_get_field_name( $field_name );
		$args['nicename'] = $field_nicename;
		ob_start();
		?>
		<div class="wpv-filter-multiple-element js-wpv-filter-multiple-element js-wpv-filter-custom-field-multiple-element js-filter-row-custom-field-<?php echo esc_attr( $field_name ); ?>" data-field="<?php echo esc_attr( $field_name ); ?>">
			<h4><?php echo __('Custom field', 'wpv_views') . ' - ' . $field_nicename; ?></h4>
			<span class="wpv-filter-multiple-element-delete">
				<button class="button button-secondary button-small js-filter-remove" data-field="<?php echo esc_attr( $field_name ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_custom_field_delete_nonce' );?>">
					<i class="icon-trash"></i>&nbsp;<?php _e( 'Delete', 'wpv-views' ); ?>
				</button>
			</span>
			<div class="wpv-filter-multiple-element-options">
			<?php WPV_Custom_Field_Filter::wpv_render_custom_field_options( $args, $view_settings ); ?>
			</div>
			<div class="js-wpv-filter-toolset-messages"></div>
		</div>
		<?php
		$buffer = ob_get_clean();
		return $buffer;
	}

	static function wpv_render_custom_field_options( $args, $view_settings = array() ) {
		global $WP_Views_fapi;
		$compare = array( 
			'=' => __( 'equal to', 'wpv-views' ),
			'!=' => __( 'different from', 'wpv-views' ),
			'>' => __( 'greater than', 'wpv-views' ),
			'>=' => __( 'greater than or equal', 'wpv-views' ),
			'<' => __( 'lower than', 'wpv-views' ),
			'<=' => __( 'lower than or equal', 'wpv-views' ),
			'LIKE' => __( 'like', 'wpv-views' ),
			'NOT LIKE' => __( 'not like', 'wpv-views' ),
			'IN' => __( 'in', 'wpv-views' ),
			'NOT IN' => __( 'not in', 'wpv-views' ),
			'BETWEEN' => __( 'between', 'wpv-views' ),
			'NOT BETWEEN' => __( 'not between', 'wpv-views' )
		);
		$types = array( 
			'CHAR' => __( 'string', 'wpv-views' ), 
			'NUMERIC' => __( 'number', 'wpv-views' ),
			'BINARY' => __( 'boolean', 'wpv-views' ),
			'DECIMAL' => 'DECIMAL',
			'DATE' => 'DATE',
			'DATETIME' => 'DATETIME',
			'TIME' => 'TIME',
			'SIGNED' => 'SIGNED',
			'UNSIGNED' => 'UNSIGNED'
		);
		$options = array(
			__( 'Constant', 'wpv-views' ) => 'constant',
			__( 'URL parameter', 'wpv-views' ) => 'url',
			__( 'Shortcode attribute', 'wpv-views' ) => 'attribute',
			'NOW' => 'now',
			'TODAY' => 'today',
			'FUTURE_DAY' => 'future_day',
			'PAST_DAY' => 'past_day',
			'THIS_MONTH' => 'this_month',
			'FUTURE_MONTH' => 'future_month',
			'PAST_MONTH' => 'past_month',
			'THIS_YEAR' => 'this_year',
			'FUTURE_YEAR' => 'future_year',
			'PAST_YEAR' => 'past_year',
			'SECONDS_FROM_NOW' => 'seconds_from_now',
			'MONTHS_FROM_NOW' => 'months_from_now',
			'YEARS_FROM_NOW' => 'years_from_now',
			'DATE' => 'date'
		);
		$options_with_framework = array(
			__( 'Constant', 'wpv-views' ) => 'constant',
			__( 'URL parameter', 'wpv-views' ) => 'url',
			__( 'Shortcode attribute', 'wpv-views' ) => 'attribute',
			__( 'Framework value', 'wpv-views' ) => 'framework',
			'NOW' => 'now',
			'TODAY' => 'today',
			'FUTURE_DAY' => 'future_day',
			'PAST_DAY' => 'past_day',
			'THIS_MONTH' => 'this_month',
			'FUTURE_MONTH' => 'future_month',
			'PAST_MONTH' => 'past_month',
			'THIS_YEAR' => 'this_year',
			'FUTURE_YEAR' => 'future_year',
			'PAST_YEAR' => 'past_year',
			'SECONDS_FROM_NOW' => 'seconds_from_now',
			'MONTHS_FROM_NOW' => 'months_from_now',
			'YEARS_FROM_NOW' => 'years_from_now',
			'DATE' => 'date'
		);
		$options_with_framework_broken = array(
			__( 'Select one option...', 'wpv-views' ) => '',
			__( 'Constant', 'wpv-views' ) => 'constant',
			__( 'URL parameter', 'wpv-views' ) => 'url',
			__( 'Shortcode attribute', 'wpv-views' ) => 'attribute',
			'NOW' => 'now',
			'TODAY' => 'today',
			'FUTURE_DAY' => 'future_day',
			'PAST_DAY' => 'past_day',
			'THIS_MONTH' => 'this_month',
			'FUTURE_MONTH' => 'future_month',
			'PAST_MONTH' => 'past_month',
			'THIS_YEAR' => 'this_year',
			'FUTURE_YEAR' => 'future_year',
			'PAST_YEAR' => 'past_year',
			'SECONDS_FROM_NOW' => 'seconds_from_now',
			'MONTHS_FROM_NOW' => 'months_from_now',
			'YEARS_FROM_NOW' => 'years_from_now',
			'DATE' => 'date'
		);
		$fw_key_options = array();
		$fw_key_options = apply_filters( 'wpv_filter_extend_framework_options_for_custom_field', $fw_key_options );
		$name_sanitized = str_replace( ' ', '_', $args['name'] );
		if ( isset( $view_settings['custom-field-' . $name_sanitized . '_value'] ) ) {
			$value = $view_settings['custom-field-' . $name_sanitized . '_value'];
		} else {
			$value = '';
		}
		$parts = array( $value );
		$value = WPV_Filter_Item::encode_date( $value );
		if ( isset( $view_settings['custom-field-' . $name_sanitized . '_compare'] ) ) {
			$compare_selected = $view_settings['custom-field-' . $name_sanitized . '_compare'];
		} else {
			$compare_selected = '=';
		}
		if ( isset( $view_settings['custom-field-' . $name_sanitized . '_type'] ) ) {
			$type_selected = $view_settings['custom-field-' . $name_sanitized . '_type'];
		} else {
			$type_selected = 'CHAR';
		}
		$name = 'custom-field-' . $name_sanitized . '%s';
		switch ( $compare_selected ) {
			case 'BETWEEN':
			case 'NOT BETWEEN':
				$parts = explode( ',', $value );
				// Make sure we have only 2 items
				while ( count( $parts ) < 2 ) {
					$parts[] = '';
				}
				while ( count( $parts ) > 2 ) {
					array_pop($parts);
				}
				break;
			case 'IN':
			case 'NOT IN':
				$parts = explode( ',', $value );
				if ( count( $parts ) < 1 ) {
					$parts = array( $value );
				}
				break;
		}
		$value = WPV_Filter_Item::unencode_date( $value );
		?>
			<?php echo sprintf( __( 'The custom field %s is a', 'wpv-views' ), $args['nicename'] ); ?>
			<select name="<?php echo esc_attr( sprintf( $name, '_type' ) ); ?>" class="js-wpv-custom-field-type-select" autocomplete="off">
				<?php
				foreach ( $types as $type_key => $type_val ) {
				?>
				<option value="<?php echo esc_attr( $type_key ); ?>" <?php selected( $type_selected, $type_key ); ?>><?php echo $type_val; ?></option>
				<?php
				}
				?>
			</select>
			<?php _e( 'that is', 'wpv-views' ); ?>
			<select name="<?php echo esc_attr( sprintf( $name, '_compare' ) ); ?>" class="wpv_custom_field_compare_select js-wpv-custom-field-compare-select" autocomplete="off">
				<?php
				foreach ( $compare as $com_key => $com_val ) {
				?>
				<option value="<?php echo esc_attr( $com_key ); ?>" <?php selected( $compare_selected, $com_key ); ?>><?php echo $com_val; ?></option>
				<?php
				}
				?>
			</select>
			<div class="wpv-filter-multiple-element-options-mode js-wpv-custom-field-values">
				<input type="hidden" class="js-wpv-custom-field-values-real" name="<?php echo esc_attr( sprintf( $name, '_value' ) ); ?>" value="<?php echo esc_attr( $value ); ?>" autocomplete="off" />
				<?php
				foreach ( $parts as $i => $value_part ) {
					?>
					<div class="wpv_custom_field_value_div js-wpv-custom-field-value-div">
						<?php _e( 'the', 'wpv-views' ); ?>
						<?php
						$function_value = WPV_Filter_Item::get_custom_filter_function_and_value( $value_part );
						$selected_function = $function_value['function'];
						$options_to_pass = $options;
						if ( $WP_Views_fapi->framework_valid ) {
							$options_to_pass = $options_with_framework;
						} else if ( $selected_function == 'framework' ) {
							$options_to_pass = $options_with_framework_broken;
						}
						echo wpv_form_control( 
							array(
								'field' => array(
									'#name' => 'wpv_custom_field_compare_mode-' . $name_sanitized . $i ,
									'#type' => 'select',
									'#attributes' => array(
										'style' => '',
										'class' => 'wpv_custom_field_compare_mode js-wpv-custom-field-compare-mode js-wpv-element-not-serialize js-wpv-filter-validate',
										'data-type' => 'select',
										'autocomplete' => 'off'
									),
									'#inline' => true,
									'#options' => $options_to_pass,
									'#default_value' => $selected_function,
								)
							)
						);
						$validate_class = '';
						$validate_type = 'none';
						$hidden_input = '';
						$hidden_date = '';
						$hidden_framework_select = '';
						switch ( $selected_function ) {
							case 'constant':
							case 'future_day':
							case 'past_day':
							case 'future_month':
							case 'past_month':
							case 'future_year':
							case 'past_year':
							case 'seconds_from_now':
							case 'months_from_now':
							case 'years_from_now':
								$hidden_date = ' style="display:none"';
								$hidden_framework_select = ' style="display:none"';
								break;
							case 'url':
								$validate_class = 'js-wpv-filter-validate';
								$validate_type = 'url';
								$hidden_date = ' style="display:none"';
								$hidden_framework_select = ' style="display:none"';
								break;
							case 'attribute':
								$validate_class = 'js-wpv-filter-validate';
								$validate_type = 'shortcode';
								$hidden_date = ' style="display:none"';
								$hidden_framework_select = ' style="display:none"';
								break;
							case 'date':
								$hidden_input = ' style="display:none"';
								$hidden_framework_select = ' style="display:none"';
								break;
							case 'framework':
								$hidden_input = ' style="display:none"';
								$hidden_date = ' style="display:none"';
								break;
							default:
								$hidden_input = ' style="display:none"';
								$hidden_date = ' style="display:none"';
								$hidden_framework_select = ' style="display:none"';
								break;
						}
						?>
						<span class="js-wpv-custom-field-value-combo-input" <?php echo $hidden_input; ?>>
						<input type="text" class="js-wpv-custom-field-value-text js-wpv-element-not-serialize <?php echo $validate_class; ?>" value="<?php echo esc_attr( $function_value['value'] ); ?>" data-class="js-wpv-custom-field-<?php echo esc_attr( $args['name'] ); ?>-value-text" data-type="<?php echo esc_attr( $validate_type ); ?>" name="wpv-custom-field-<?php echo esc_attr( $args['name'] ); ?>-value-text" autocomplete="off" />
						</span>
						<span class="js-wpv-custom-field-value-combo-framework" <?php echo $hidden_framework_select; ?>>
						<?php
						if ( $WP_Views_fapi->framework_valid ) {
							?>
							<select class="js-wpv-custom-field-framework-value js-wpv-custom-field-framework-value-text js-wpv-element-not-serialize" name="wpv-custom-field-<?php echo esc_attr( $args['name'] ); ?>-framework-value-text" autocomplete="off">
								<option value=""><?php _e( 'Select a key', 'wpv-views' ); ?></option>
								<?php
								foreach ( $fw_key_options as $index => $value ) {
								?>
								<option value="<?php echo esc_attr( $index ); ?>" <?php selected( $function_value['value'], $index ); ?>><?php echo $value; ?></option>
								<?php
								}
								?>
							</select>
							<?php
						} else {
							?>
							<span class="wpv-combo">
							<input type="hidden" class="js-wpv-custom-field-framework-value js-wpv-custom-field-framework-value-text js-wpv-element-not-serialize" value="" autocomplete="off" />
							<?php
							$WP_Views_fapi->framework_missing_message_for_filters( false, false );
							?>
							</span>
							<?php
						}
						?>
						</span>
						<span class="js-wpv-custom-field-value-combo-date" <?php echo $hidden_date; ?>>
						<?php
						WPV_Filter_Item::date_field_controls( $function_value['function'], $function_value['value'] );
						?>
						</span>
						<button class="button-secondary js-wpv-custom-field-remove-value"><i class="icon-remove"></i> <?php echo __( 'Remove', 'wpv-views' ); ?></button>
					</div>
					<?php
				}
				?>
				<button class="button button-secondary js-wpv-custom-field-add-value" style="margin-top:10px;"><i class="icon-plus"></i> <?php echo __( 'Add another value', 'wpv-views' ); ?></button>
			</div>
	<?php
	}

	static function wpv_filter_custom_field_update_callback() {
		if ( ! current_user_can( 'manage_options' ) ) {
			$data = array(
				'type' => 'capability',
				'message' => __( 'You do not have permissions for that.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		if ( 
			! isset( $_POST["wpnonce"] )
			|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_filter_custom_field_nonce' ) 
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
		if ( empty( $_POST['filter_custom_fields'] ) ) {
			$data = array(
				'type' => 'data_missing',
				'message' => __( 'Wrong or missing data.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		$change = false;
		$view_id = $_POST['id'];
		parse_str( $_POST['filter_custom_fields'], $filter_custom_fields );
		$view_array = get_post_meta( $view_id, '_wpv_settings', true );
		$summary = __( 'Select posts with custom field: ', 'wpv-views' );
		$result = '';
		foreach ( $filter_custom_fields as $filter_key => $filter_data ) {
			if ( 
				! isset( $view_array[$filter_key] ) 
				|| $filter_data != $view_array[$filter_key] 
			) {
				if ( is_array( $filter_data ) ) {
					$filter_data = array_map( 'sanitize_text_field', $filter_data );
					$filter_data = array_map( array( 'WPV_Custom_Field_Filter', 'fix_lower_saving' ), $filter_data );
				} else {
					$filter_data = sanitize_text_field( $filter_data );
					$filter_data = WPV_Custom_Field_Filter::fix_lower_saving( $filter_data );
				}
				$change = true;
				$view_array[$filter_key] = $filter_data;
			}
		}
		if ( ! isset( $view_array['custom_fields_relationship'] ) ) {
			$view_array['custom_fields_relationship'] = 'AND';
			$change = true;
		}
		if ( $change ) {
			update_post_meta( $view_id, '_wpv_settings', $view_array );
			do_action( 'wpv_action_wpv_save_item', $view_id );
		}
		foreach ( array_keys( $view_array ) as $key ) {
			if ( strpos( $key, 'custom-field-' ) === 0 && strpos( $key, '_compare' ) === strlen( $key ) - strlen( '_compare' ) ) {
				$name = substr( $key, 0, strlen( $key ) - strlen( '_compare' ) );
				if ( $result != '' ) {
					if ( $view_array['custom_fields_relationship'] == 'OR' ) {
						$result .= __( ' OR', 'wpv-views' );
					} else {
						$result .= __( ' AND', 'wpv-views' );
					}
				}
				$result .= wpv_get_custom_field_summary( $name, $view_array );
			}
		}
		$summary .= $result;
		$data = array(
			'id' => $view_id,
			'message' => __( 'Custom field filter saved', 'wpv-views' ),
			'summary' => $summary
		);
		wp_send_json_success( $data );
	}

	static function wpv_filter_custom_field_delete_callback() {
		if ( ! current_user_can( 'manage_options' ) ) {
			$data = array(
				'type' => 'capability',
				'message' => __( 'You do not have permissions for that.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		if ( 
			! isset( $_POST["wpnonce"] )
			|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_filter_custom_field_delete_nonce' ) 
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
		$fields = is_array( $_POST['field'] ) ? $_POST['field'] : array( $_POST['field'] );
		foreach ( $fields as $field ) {
			$to_delete = array(
				'custom-field-' . $field . '_compare',
				'custom-field-' . $field . '_type',
				'custom-field-' . $field . '_value'
			);
			foreach ( $to_delete as $index ) {
				if ( isset( $view_array[$index] ) ) {
					unset( $view_array[$index] );
				}
			}
			$len = isset( $view_array['filter_controls_field_name'] ) ? count( $view_array['filter_controls_field_name'] ) : 0;
			$splice = false;
			for ( $i = 0; $i < $len; $i++ ) {
				if ( strpos( $view_array['filter_controls_field_name'][$i], $field ) !== false ) {
					$splice = $i;
				}
			}
			
			if ( $splice !== false ) {
				foreach ( Editor_addon_parametric::$prm_db_fields as $dbf ) {
					array_splice($view_array[$dbf], $splice, 1);
				}
			}
		}
		update_post_meta( $_POST["id"], '_wpv_settings', $view_array );
		do_action( 'wpv_action_wpv_save_item', $_POST["id"] );
		$data = array(
			'id' => $_POST["id"],
			'message' => __( 'Custom field filter deleted', 'wpv-views' )
		);
		wp_send_json_success( $data );
	}
	
	

	static function wpv_custom_field_summary_filter( $summary, $post_id, $view_settings ) {
		$result = '';
		$result = wpv_get_filter_custom_field_summary_txt( $view_settings );
		if ( $result != '' && $summary != '' ) {
			$summary .= '<br />';
		}
		$summary .= $result;
		return $summary;
	}
	
	/**
	* fix_lower_saving
	*
	* Fix saving of "lower than" and "lower or equal to" comparisons, which get HTML-encoded when passed through sanitize_text_field
	*
	* @param $data string
	*
	* @return string
	*
	* @since 1.8.10
	*/
	
	static function fix_lower_saving( $data ) {
		if (
			'&lt;' == $data 
			|| '&lt;=' == $data
		) {
			$data = str_replace( '&lt;', '<', $data );
		}
		return $data;
	}
    
}

function wpv_custom_fields_get_url_params( $view_settings ) {
	$pattern = '/URL_PARAM\(([^(]*?)\)/siU';
	$results = array();
	foreach ( array_keys( $view_settings ) as $key ) {
		if ( strpos( $key, 'custom-field-' ) === 0 && strpos( $key, '_compare' ) === strlen( $key ) - strlen( '_compare' ) ) {
			$name = substr( $key, 0, strlen( $key ) - strlen( '_compare' ) );
			$name = substr( $name, strlen( 'custom-field-' ) );
			$value = $view_settings['custom-field-' . $name . '_value'];
			if ( preg_match_all( $pattern, $value, $matches, PREG_SET_ORDER ) )  {
				foreach ( $matches as $match ) {
					$results[] = array( 'name' => $name, 'param' => $match[1], 'mode' => 'cf' );
				}
			}
		}
	}
	return $results;
}