<?php

/**
* Usermeta Field filter
*
* @package Views
*
* @since unknown
*/

WPV_Usermeta_Field_Filter::on_load();

/**
* WPV_Custom_Field_Filter
*
* Views Custom Field Filter Class
*
* @since 1.7.0
*/

class WPV_Usermeta_Field_Filter {

    static function on_load() {
        add_action( 'init', array( 'WPV_Usermeta_Field_Filter', 'init' ) );
		add_action( 'admin_init', array( 'WPV_Usermeta_Field_Filter', 'admin_init' ) );
    }

    static function init() {
		
    }
	
	static function admin_init() {
		// Register filters in lists and dialogs
		add_filter( 'wpv_users_filters_add_filter', array( 'WPV_Usermeta_Field_Filter', 'wpv_filters_add_filter_usermeta_field' ), 20, 2 );
		add_action( 'wpv_add_users_filter_list_item', array( 'WPV_Usermeta_Field_Filter', 'wpv_add_filter_usermeta_field_list_item' ), 1, 1 );
		//AJAX callbakcks
		add_action( 'wp_ajax_wpv_filter_usermeta_field_update', array( 'WPV_Usermeta_Field_Filter', 'wpv_filter_usermeta_field_update_callback' ) );
		add_action( 'wp_ajax_wpv_filter_usermeta_field_delete', array( 'WPV_Usermeta_Field_Filter', 'wpv_filter_usermeta_field_delete_callback' ) );
		add_filter( 'wpv-view-get-summary', array( 'WPV_Usermeta_Field_Filter', 'wpv_usermeta_field_summary_filter' ), 7, 3 );
		// Register scripts
		add_action( 'admin_enqueue_scripts', array( 'WPV_Usermeta_Field_Filter','admin_enqueue_scripts' ), 20 );
	}
	
	/**
	* admin_enqueue_scripts
	*
	* Register the needed script for this filter
	*
	* @since 1.7
	*/
	
	static function admin_enqueue_scripts( $hook ) {
		wp_register_script( 'views-filter-usermeta-field-js', ( WPV_URL . "/res/js/redesign/views_filter_usermeta_field.js" ), array( 'views-filters-js'), WPV_VERSION, true );
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'views-editor' ) {
			wp_enqueue_script( 'views-filter-usermeta-field-js' );
		}
	}

	static function wpv_filters_add_filter_usermeta_field( $filters ) {
        $basic = array( 
            array( __( 'First Name', 'wpv-views' ), 'first_name','Basic','' ),
            array( __( 'Last Name', 'wpv-views' ), 'last_name','Basic','' ),
            array( __( 'Nickname', 'wpv-views' ), 'nickname','Basic','' ),
            array( __( 'Description', 'wpv-views' ), 'description','Basic','' ),
            array( __( 'Yahoo IM', 'wpv-views' ), 'yim','Basic','' ),
            array( __( 'Jabber', 'wpv-views' ), 'jabber','Basic','' ),
            array( __( 'AIM', 'wpv-views' ), 'aim','Basic','' ),
			//array('Email', 'user_email','Basic',''),
            //array('Username', 'user_login','Basic',''),
            //array('Display Name', 'display_name','Basic',''),
            //array('User Url', 'user_url','Basic','')
        );
        foreach ( $basic as $b_filter ) {
			$filters['usermeta-field-basic-' . str_replace(' ', '_', $b_filter[1])] = array(
				'name' => sprintf( __( 'User field - %s', 'wpv-views' ), $b_filter[0]),
                'present' => 'usermeta-field-' . $b_filter[1] . '_compare',
                'callback' => array( 'WPV_Usermeta_Field_Filter', 'wpv_add_new_filter_usermeta_field_list_item' ),
                'args' => array( 'name' =>'usermeta-field-' . $b_filter[1] ),
				'group' => __( 'User data', 'wpv-views' )
			);
		}
		// @todo review this for gods sake!!!!!!!!!!!!!!!!!!!!!!!!
        if ( function_exists( 'wpcf_admin_fields_get_groups' ) ) {
            $groups = wpcf_admin_fields_get_groups( 'wp-types-user-group' );            
            $user_id = wpcf_usermeta_get_user();
            $add = array();
            if ( ! empty( $groups ) ) {
                foreach ( $groups as $group_id => $group ) {
                    if ( empty( $group['is_active'] ) ) {
                        continue;
                    }
                    $fields = wpcf_admin_fields_get_fields_by_group(
						$group['id'],
                        'slug',
						true,
						false,
						true,
						'wp-types-user-group',
                        'wpcf-usermeta' 
					);
                    if ( ! empty( $fields ) ) {
                        foreach ( $fields as $field_id => $field ) {
                            $add[] = $field['meta_key'];
                            $filters['usermeta-field-' . str_replace( ' ', '_', $field['meta_key'] )] = array(
								'name' => sprintf( __( 'User field - %s', 'wpv-views' ), $field['name'] ),
                                'present' => 'usermeta-field-' . $field['meta_key'] . '_compare',
                                'callback' => array( 'WPV_Usermeta_Field_Filter', 'wpv_add_new_filter_usermeta_field_list_item' ),
                                'args' => array( 'name' =>'usermeta-field-' . $field['meta_key'] )
							);
                        }
                    }
                }
            }
            $cf_types = wpcf_admin_fields_get_fields( true, true, false, 'wpcf-usermeta' );
            foreach ( $cf_types as $cf_id => $cf ) {
                 if ( !in_array( $cf['meta_key'], $add) ){
                     $filters['usermeta-field-' . str_replace( ' ', '_', $cf['meta_key'] )] = array(
						'name' => sprintf( __( 'User field - %s', 'wpv-views' ), $cf['name'] ),
                        'present' => 'usermeta-field-' . $cf['meta_key'] . '_compare',
                        'callback' => array( 'WPV_Usermeta_Field_Filter', 'wpv_add_new_filter_usermeta_field_list_item' ),
                        'args' => array( 'name' =>'usermeta-field-' . $cf['meta_key'] )
					);
                 }
            }
        }
        $meta_keys = get_user_meta_keys();
        foreach ( $meta_keys as $key ) {
            $key_nicename = '';
            if ( stripos( $key, 'wpcf-' ) === 0 ) {
                if ( function_exists( 'wpcf_admin_fields_get_groups' ) ) {    
                continue;    
                }
            } else {
                $key_nicename = $key;
            }
            $filters['usermeta-field-' . str_replace( ' ', '_', $key )] = array(
				'name' => sprintf( __( 'User field - %s', 'wpv-views' ), $key_nicename ),
                'present' => 'usermeta-field-' . $key . '_compare',
                'callback' => array( 'WPV_Usermeta_Field_Filter', 'wpv_add_new_filter_usermeta_field_list_item' ),
                'args' => array( 'name' =>'usermeta-field-' . $key )
			);
        }
		return $filters;
	}
	
	static function wpv_add_new_filter_usermeta_field_list_item( $args ) {
		$new_cf_filter_settings = array(
			$args['name'] . '_compare' => '=',
			$args['name'] . '_type' => 'CHAR',
			$args['name'] . '_value' => '',
		);
		WPV_Usermeta_Field_Filter::wpv_add_filter_usermeta_field_list_item( $new_cf_filter_settings );
	}

	static function wpv_add_filter_usermeta_field_list_item( $view_settings ) {
		if ( ! isset( $view_settings['usermeta_fields_relationship'] ) ) {
			$view_settings['usermeta_fields_relationship'] = 'AND';
		}
		$summary = '';
		$td = '';
		$count = 0;
		foreach ( array_keys( $view_settings ) as $key ) {
			if ( 
				strpos( $key, 'usermeta-field-' ) === 0 
				&& strpos( $key, '_compare' ) === strlen( $key ) - strlen( '_compare' ) 
			) {
				$name = substr( $key, 0, strlen( $key ) - strlen( '_compare' ) );
				$td .= WPV_Usermeta_Field_Filter::wpv_get_list_item_ui_post_usermeta_field( $name, $view_settings );
				$count++;
				if ( $summary != '' ) {
					if ( $view_settings['usermeta_fields_relationship'] == 'OR' ) {
						$summary .= __( ' OR', 'wpv-views' );
					} else {
						$summary .= __( ' AND', 'wpv-views' );
					}
				}
				$summary .= wpv_get_usermeta_field_summary( $name, $view_settings );
			}
		}
		if ( $count > 0 ) {
			ob_start();
			WPV_Filter_Item::filter_list_item_buttons( 'usermeta-field', 'wpv_filter_usermeta_field_update', wp_create_nonce( 'wpv_view_filter_usermeta_field_nonce' ), 'wpv_filter_usermeta_field_delete', wp_create_nonce( 'wpv_view_filter_usermeta_field_delete_nonce' ) );
			?>
				<?php if ($summary != '') { ?>
					<p class='wpv-filter-usermeta-field-edit-summary js-wpv-filter-summary js-wpv-filter-usermeta-field-summary'>
					<?php _e('Select users with usermeta field: ', 'wpv-views');
					echo $summary; ?>
					</p>
				<?php } ?>
				<div id="wpv-filter-usermeta-field-edit" class="wpv-filter-edit js-filter-usermeta-field-edit js-wpv-filter-usermeta-field-edit js-wpv-filter-edit js-wpv-filter-options" style="padding-bottom:28px;">
				<?php echo $td; ?>
					<div class="wpv-filter-usermeta-field-relationship wpv-filter-multiple-element js-wpv-filter-usermeta-field-relationship-container">
						<h4><?php _e( 'Usermeta field relationship:', 'wpv-views' ) ?></h4>
						<div class="wpv-filter-multiple-element-options">
							<?php _e( 'Relationship to use when querying with multiple user fields:', 'wpv-views' ); ?>
							<select name="usermeta_fields_relationship" class="js-wpv-filter-usermeta-fields-relationship" autocomplete="off">
								<option value="AND" <?php selected( $view_settings['usermeta_fields_relationship'], 'AND' ); ?>><?php _e('AND', 'wpv-views'); ?></option>
								<option value="OR" <?php selected( $view_settings['usermeta_fields_relationship'], 'OR' ); ?>><?php _e('OR', 'wpv-views'); ?></option>
							</select>
						</div>
					</div>
					<div class="js-wpv-filter-multiple-toolset-messages"></div>
					<span class="filter-doc-help">
						<?php echo sprintf(
							__( '%sLearn about filtering by user fields%s', 'wpv-views' ),
							'<a class="wpv-help-link" href="' . WPV_FILTER_BY_USER_FIELDS_LINK . '" target="_blank">',
							' &raquo;</a>'
						); ?>
					</span>
				</div>
		<?php
			$li_content = ob_get_clean();
			WPV_Filter_Item::multiple_filter_list_item( 'usermeta-field', 'posts', __( 'Usermeta field filter', 'wpv-views' ), $li_content );
		}
	}

	static function wpv_get_list_item_ui_post_usermeta_field( $type, $view_settings = array() ) {
		$field_name = substr( $type, strlen( 'usermeta-field-' ) );
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
		<div class="wpv-filter-multiple-element js-wpv-filter-multiple-element js-wpv-filter-usermeta-field-multiple-element js-filter-row-usermeta-field-<?php echo esc_attr( $field_name ); ?>" data-field="<?php echo esc_attr( $field_name ); ?>">
			<h4><?php echo __('Usermeta field', 'wpv_views') . ' - ' . $field_nicename; ?></h4>
			<span class="wpv-filter-multiple-element-delete">
				<button class="button button-secondary button-small js-filter-remove" data-field="<?php echo esc_attr( $field_name ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_usermeta_field_delete_nonce' );?>">
					<i class="icon-trash"></i>&nbsp;<?php _e( 'Delete', 'wpv-views' ); ?>
				</button>
			</span>
			<div class="wpv-filter-multiple-element-options">
			<?php WPV_Usermeta_Field_Filter::wpv_render_usermeta_field_options( $args, $view_settings ); ?>
			</div>
			<div class="js-wpv-filter-toolset-messages"></div>
		</div>
		<?php
		$buffer = ob_get_clean();
		return $buffer;
	}

	static function wpv_render_usermeta_field_options( $args, $view_settings = array() ) {
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
		$fw_key_options = apply_filters( 'wpv_filter_extend_framework_options_for_usermeta_field', $fw_key_options );
		$name_sanitized = str_replace( ' ', '_', $args['name'] );
		if ( isset( $view_settings['usermeta-field-' . $name_sanitized . '_value'] ) ) {
			$value = $view_settings['usermeta-field-' . $name_sanitized . '_value'];
		} else {
			$value = '';
		}
		$parts = array( $value );
		$value = WPV_Filter_Item::encode_date( $value );
		if ( isset( $view_settings['usermeta-field-' . $name_sanitized . '_compare'] ) ) {
			$compare_selected = $view_settings['usermeta-field-' . $name_sanitized . '_compare'];
		} else {
			$compare_selected = '=';
		}
		if ( isset( $view_settings['usermeta-field-' . $name_sanitized . '_type'] ) ) {
			$type_selected = $view_settings['usermeta-field-' . $name_sanitized . '_type'];
		} else {
			$type_selected = 'CHAR';
		}
		$name = 'usermeta-field-' . $name_sanitized . '%s';
		switch ( $compare_selected ) {
			case 'BETWEEN':
			case 'NOT BETWEEN':
				$parts = explode( ',', $value );
				// Make sure we have only 2 items
				while ( count( $parts ) < 2 ) {
					$parts[] = '';
				}
				while ( count( $parts ) > 2 ) {
					array_pop( $parts );
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
		$value = WPV_Filter_Item::unencode_date($value);
		?>
			<?php echo sprintf( __( 'The usermeta field %s is a', 'wpv-views' ), $args['nicename'] ); ?>
			<select name="<?php echo esc_attr( sprintf( $name, '_type' ) ); ?>" class="js-wpv-usermeta-field-type-select" autocomplete="off">
				<?php
				foreach ( $types as $type_key => $type_val ) {
				?>
				<option value="<?php echo esc_attr( $type_key ); ?>" <?php selected( $type_selected, $type_key ); ?>><?php echo $type_val; ?></option>
				<?php
				}
				?>
			</select>
			<?php _e( 'that is', 'wpv-views' ); ?>
			<select name="<?php echo esc_attr( sprintf( $name, '_compare' ) ); ?>" class="wpv_usermeta_field_compare_select js-wpv-usermeta-field-compare-select" autocomplete="off">
				<?php
				foreach ( $compare as $com_key => $com_val ) {
				?>
				<option value="<?php echo esc_attr( $com_key ); ?>" <?php selected( $compare_selected, $com_key ); ?>><?php echo $com_val; ?></option>
				<?php
				}
				?>
			</select>
			<div class="wpv-filter-multiple-element-options-mode js-wpv-usermeta-field-values">
				<input type="hidden" class="js-wpv-usermeta-field-values-real" name="<?php echo esc_attr( sprintf( $name, '_value' ) ); ?>" value="<?php echo esc_attr( $value ); ?>" autocomplete="off" />
				<?php
				foreach ( $parts as $i => $value_part ) {
					?>
					<div class="wpv_usermeta_field_value_div js-wpv-usermeta-field-value-div">
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
									'#name' => 'wpv_usermeta_field_compare_mode-' . $name_sanitized . $i ,
									'#type' => 'select',
									'#attributes' => array(
										'style' => '',
										'class' => 'wpv_usermeta_field_compare_mode js-wpv-usermeta-field-compare-mode js-wpv-element-not-serialize js-wpv-filter-validate',
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
						<span class="js-wpv-usermeta-field-value-combo-input" <?php echo $hidden_input; ?>>
						<input type="text" class="js-wpv-usermeta-field-value-text js-wpv-element-not-serialize <?php echo $validate_class; ?>" value="<?php echo esc_attr( $function_value['value'] ); ?>" data-class="js-wpv-usermeta-field-<?php echo esc_attr( $args['name'] ); ?>-value-text" data-type="none" name="wpv-usermeta-field-<?php echo esc_attr( $args['name'] ); ?>-value-text" autocomplete="off" />
						</span>
						<span class="js-wpv-usermeta-field-value-combo-framework" <?php echo $hidden_framework_select; ?>>
						<?php
						if ( $WP_Views_fapi->framework_valid ) {
							?>
							<select class="js-wpv-usermeta-field-framework-value js-wpv-usermeta-field-framework-value-text js-wpv-element-not-serialize" name="wpv-usermeta-field-<?php echo esc_attr( $args['name'] ); ?>-framework-value-text" autocomplete="off">
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
							<input type="hidden" class="js-wpv-usermeta-field-framework-value js-wpv-usermeta-field-framework-value-text js-wpv-element-not-serialize" value="" autocomplete="off" />
							<?php
							$WP_Views_fapi->framework_missing_message_for_filters( false, false );
							?>
							</span>
							<?php
						}
						?>
						</span>
						<span class="js-wpv-usermeta-field-value-combo-date" <?php echo $hidden_date; ?>>
						<?php
						WPV_Filter_Item::date_field_controls( $function_value['function'], $function_value['value'] );
						?>
						</span>
						<button class="button-secondary js-wpv-usermeta-field-remove-value"><i class="icon-remove"></i> <?php echo __( 'Remove', 'wpv-views' ); ?></button>
					</div>
					<?php
				}
				?>
				<button class="button-secondary js-wpv-usermeta-field-add-value" style="margin-top:10px;"><i class="icon-plus"></i> <?php echo __( 'Add another value', 'wpv-views' ); ?></button>
			</div>
	<?php
	}

	static function wpv_filter_usermeta_field_update_callback() {
		if ( ! current_user_can( 'manage_options' ) ) {
			$data = array(
				'type' => 'capability',
				'message' => __( 'You do not have permissions for that.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		if ( 
			! isset( $_POST["wpnonce"] )
			|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_filter_usermeta_field_nonce' ) 
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
		if ( empty( $_POST['filter_usermeta_fields'] ) ) {
			$data = array(
				'type' => 'data_missing',
				'message' => __( 'Wrong or missing data.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		$change = false;
		$view_id = $_POST['id'];
		parse_str( $_POST['filter_usermeta_fields'], $filter_usermeta_fields );
		$view_array = get_post_meta( $view_id, '_wpv_settings', true );
		$summary = __( 'Select users with usermeta field: ', 'wpv-views' );
		$result = '';
		foreach ( $filter_usermeta_fields as $filter_key => $filter_data ) {
			if ( 
				! isset( $view_array[$filter_key] ) 
				|| $filter_data != $view_array[$filter_key] 
			) {
				if ( is_array( $filter_data ) ) {
					$filter_data = array_map( 'sanitize_text_field', $filter_data );
				} else {
					$filter_data = sanitize_text_field( $filter_data );
				}
				$change = true;
				$view_array[$filter_key] = $filter_data;
			}
		}
		if ( ! isset( $view_array['usermeta_fields_relationship'] ) ) {
			$view_array['usermeta_fields_relationship'] = 'AND';
			$change = true;
		}
		if ( $change ) {
			update_post_meta( $view_id, '_wpv_settings', $view_array );
			do_action( 'wpv_action_wpv_save_item', $view_id );
		}
		foreach ( array_keys( $view_array ) as $key ) {
			if ( strpos( $key, 'usermeta-field-' ) === 0 && strpos( $key, '_compare' ) === strlen( $key ) - strlen( '_compare' ) ) {
				$name = substr( $key, 0, strlen( $key ) - strlen( '_compare' ) );
				if ( $result != '' ) {
					if ( $view_array['usermeta_fields_relationship'] == 'OR' ) {
						$result .= __( ' OR', 'wpv-views' );
					} else {
						$result .= __( ' AND', 'wpv-views' );
					}
				}
				$result .= wpv_get_usermeta_field_summary( $name, $view_array );
			}
		}
		$summary .= $result;
		$data = array(
			'id' => $view_id,
			'message' => __( 'Usermeta field filter saved', 'wpv-views' ),
			'summary' => $summary
		);
		wp_send_json_success( $data );
	}	

	static function wpv_filter_usermeta_field_delete_callback() {
		if ( ! current_user_can( 'manage_options' ) ) {
			$data = array(
				'type' => 'capability',
				'message' => __( 'You do not have permissions for that.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		if ( 
			! isset( $_POST["wpnonce"] )
			|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_filter_usermeta_field_delete_nonce' ) 
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
				'usermeta-field-' . $field . '_compare',
				'usermeta-field-' . $field . '_type',
				'usermeta-field-' . $field . '_value'
			);
			foreach ($to_delete as $index) {
				if ( isset( $view_array[$index] ) ) {
					unset( $view_array[$index] );
				}
			}
		}
		update_post_meta( $_POST["id"], '_wpv_settings', $view_array );
		do_action( 'wpv_action_wpv_save_item', $_POST["id"] );
		$data = array(
			'id' => $_POST["id"],
			'message' => __( 'Usermeta field filter deleted', 'wpv-views' )
		);
		wp_send_json_success( $data );
	}
	

	static function wpv_usermeta_field_summary_filter( $summary, $post_id, $view_settings ) {
		$result = '';
		$result = wpv_get_filter_usermeta_field_summary_txt( $view_settings );
		if ( $result != '' && $summary != '' ) {
			$summary .= '<br />';
		}
		$summary .= $result;
		return $summary;
	}
    
}

// @todo maybe it is better to do a larger query and then remove the unwanted values, instead of crafting a wrong list of them

function get_user_meta_keys( $include_hidden = false ) {
	global $wpdb;
	$values_to_prepare = array();
	//static $cf_keys = null;
	$umf_mulsitise_string = " 1 = 1 ";
	if ( is_multisite() ) {
		global $blog_id;
		$umf_mulsitise_string = " ( meta_key NOT REGEXP '^{$wpdb->base_prefix}[0-9]_' OR meta_key REGEXP '^{$wpdb->base_prefix}%d_' ) ";
		$values_to_prepare[] = $blog_id;
	}
	$umf_hidden = " 1 = 1 ";
	if ( ! $include_hidden ) {
		$hidden_usermeta = array('first_name','last_name','name','nickname','description','yim','jabber','aim',
		'rich_editing','comment_shortcuts','admin_color','use_ssl','show_admin_bar_front',
		'capabilities','user_level','user-settings',
		'dismissed_wp_pointers','show_welcome_panel',
		'dashboard_quick_press_last_post_id','managenav-menuscolumnshidden',
		'primary_blog','source_domain',
		'closedpostboxes','metaboxhidden','meta-box-order_dashboard','meta-box-order','nav_menu_recently_edited',
		'new_date','show_highlight','language_pairs',
		'module-manager',
		'screen_layout');
	//	$umf_hidden = " ( meta_key NOT REGEXP '" . implode("|", $hidden_usermeta) . "' AND meta_key NOT REGEXP '^_' ) "; // NOTE this one make sites with large usermeta tables to fall
		$umf_hidden = " ( meta_key NOT IN ('" . implode("','", $hidden_usermeta) . "') AND meta_key NOT REGEXP '^_' ) ";
	}
	$where = " WHERE {$umf_mulsitise_string} AND {$umf_hidden} ";
	$values_to_prepare[] = 100;
	$usermeta_keys = $wpdb->get_col( 
		$wpdb->prepare(
			"SELECT DISTINCT meta_key FROM {$wpdb->usermeta} 
			{$where} 
			LIMIT 0, %d",
			$values_to_prepare
		)
	);
	if ( ! empty( $usermeta_keys ) ) {
		natcasesort( $usermeta_keys );
	}
	return $usermeta_keys;
}


function wpv_usermeta_fields_get_url_params($view_settings) {
	global $WP_Views;

	$pattern = '/URL_PARAM\(([^(]*?)\)/siU';
	$meta_keys = $WP_Views->get_meta_keys();
	
	$results = array();

	foreach (array_keys($view_settings) as $key) {
		if (strpos($key, 'usermeta-field-') === 0 && strpos($key, '_compare') === strlen($key) - strlen('_compare')) {
			$name = substr($key, 0, strlen($key) - strlen('_compare'));
			$name = substr($name, strlen('usermeta-field-'));
			
			$meta_name = $name;
			if (!in_array($meta_name, $meta_keys)) {
				$meta_name = str_replace('_', ' ', $meta_name);
			}

			$value = $view_settings['usermeta-field-' . $name . '_value'];
			
			if(preg_match_all($pattern, $value, $matches, PREG_SET_ORDER)) {
				foreach($matches as $match) {
					$results[] = array('name' => $name, 'param' => $match[1], 'mode' => 'cf');
				}
			}
		}
	}
	
	return $results;
}
