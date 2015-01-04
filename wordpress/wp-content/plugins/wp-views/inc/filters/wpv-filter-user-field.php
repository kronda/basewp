<?php

if(is_admin()){

	add_action('wpv_add_users_filter_list_item', 'wpv_add_filter_usermeta_field_list_item', 1, 1);
	add_filter('wpv_users_filters_add_filter', 'wpv_filters_add_filter_usermeta_field', 20, 2);

	function wpv_filters_add_filter_usermeta_field($filters) {
		global $WP_Views;
		
        
        
        
        $basic = array( 
                //array('Email', 'user_email','Basic',''),
                //array('Username', 'user_login','Basic',''),
                array('First Name', 'first_name','Basic',''),
                array('Last Name', 'last_name','Basic',''),
                array('Nickname', 'nickname','Basic',''),
                //array('Display Name', 'display_name','Basic',''),
                array('Description', 'description','Basic',''),
                array('Yahoo IM', 'yim','Basic',''),
                array('Jabber', 'jabber','Basic',''),
                array('AIM', 'aim','Basic',''),
                //array('User Url', 'user_url','Basic','')
                );
        
        for ( $i=0; $i<count($basic); $i++){
            $filters['usermeta-field-basic-' . str_replace(' ', '_', $basic[$i][1])] = array('name' => sprintf(__('User field - %s', 'wpv-views'), $basic[$i][0]),
                                        'present' => 'usermeta-field-' . $basic[$i][1] . '_compare',
                                        'callback' => 'wpv_add_new_filter_usermeta_field_list_item',
                                        'args' => array('name' =>'usermeta-field-' . $basic[$i][1]));    
        }
        
        
        if ( function_exists('wpcf_admin_fields_get_groups') ){
            $groups = wpcf_admin_fields_get_groups( 'wp-types-user-group' );            
            $user_id = wpcf_usermeta_get_user();
            $add = array();
            if ( !empty( $groups ) ) {
                foreach ( $groups as $group_id => $group ) {
                    if ( empty( $group['is_active'] ) ) {
                        continue;
                    }
                    $fields = wpcf_admin_fields_get_fields_by_group( $group['id'],
                            'slug', true, false, true, 'wp-types-user-group',
                            'wpcf-usermeta' );
        
                    if ( !empty( $fields ) ) {
                        foreach ( $fields as $field_id => $field ) {
                            $add[] = $field['meta_key'];
                            $filters['usermeta-field-' . str_replace(' ', '_', $field['meta_key'])] = array('name' => sprintf(__('User field - %s', 'wpv-views'), $field['name']),
                                        'present' => 'usermeta-field-' . $field['meta_key'] . '_compare',
                                        'callback' => 'wpv_add_new_filter_usermeta_field_list_item',
                                        'args' => array('name' =>'usermeta-field-' . $field['meta_key']));    
                          
                        }
                    }
                }
            }

            $cf_types = wpcf_admin_fields_get_fields( true, true, false, 'wpcf-usermeta' );
            foreach ( $cf_types as $cf_id => $cf ) {
                 if ( !in_array( $cf['meta_key'], $add) ){
                     $filters['usermeta-field-' . str_replace(' ', '_', $cf['meta_key'])] = array('name' => sprintf(__('User field - %s', 'wpv-views'), $cf['name']),
                                            'present' => 'usermeta-field-' . $cf['meta_key'] . '_compare',
                                            'callback' => 'wpv_add_new_filter_usermeta_field_list_item',
                                            'args' => array('name' =>'usermeta-field-' . $cf['meta_key']));
                 }
            }
        }
        
        
        
        
        $meta_keys = get_user_meta_keys();
        foreach ($meta_keys as $key) {
            $key_nicename = '';
            if (stripos($key, 'wpcf-') === 0) {
                if ( function_exists('wpcf_admin_fields_get_groups') ){    
                continue;    
                }
            } else {
                $key_nicename = $key;
            }
            $filters['usermeta-field-' . str_replace(' ', '_', $key)] = array('name' => sprintf(__('User field - %s', 'wpv-views'), $key_nicename),
                                        'present' => 'usermeta-field-' . $key . '_compare',
                                        'callback' => 'wpv_add_new_filter_usermeta_field_list_item',
                                        'args' => array('name' =>'usermeta-field-' . $key));
        }        
            
                    
        
		return $filters;
	}
	
	function get_user_meta_keys($include_hidden = false) {
        global $wpdb;
		//static $cf_keys = null;
        
		$umf_mulsitise_string = " 1 = 1 ";
		if ( is_multisite() ) {
			global $blog_id;
			$umf_mulsitise_string = " ( meta_key NOT REGEXP '^{$wpdb->base_prefix}[0-9]_' OR meta_key REGEXP '^{$wpdb->base_prefix}" . $blog_id . "_' ) ";
		}
		
		$umf_hidden = " 1 = 1 ";
		if ( !$include_hidden ) {
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
		
		$usermeta_keys = array();
		$usermeta_keys = $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$wpdb->usermeta} {$where} LIMIT 0, 100" );
		
		//echo '<pre>';print_r($usermeta_keys);echo '</pre>';echo $wpdb->base_prefix;
		
			if ( !empty( $usermeta_keys ) ) {
			natcasesort($usermeta_keys);
		}
		return $usermeta_keys;
    }
	
	function wpv_add_new_filter_usermeta_field_list_item($args) {
		echo wpv_get_list_item_ui_post_usermeta_field($args['name'],null, array());
	}

	function wpv_add_filter_usermeta_field_list_item($view_settings) {
		if (!isset($view_settings['usermeta_fields_relationship'])) {
			$view_settings['usermeta_fields_relationship'] = 'AND';
		}

		// Find any custom fields

		$summary = '';
		$td = '';
		$count = 0;

		foreach (array_keys($view_settings) as $key) {
			if (strpos($key, 'usermeta-field-') === 0 && strpos($key, '_compare') === strlen($key) - strlen('_compare')) {
				$name = substr($key, 0, strlen($key) - strlen('_compare'));

				$td .= wpv_get_list_item_ui_post_usermeta_field($name, null, $view_settings);
				$count++;

				if ($summary != '') {
					if ($view_settings['usermeta_fields_relationship'] == 'OR') {
						$summary .= __(' OR', 'wpv-views');
					} else {
						$summary .= __(' AND', 'wpv-views');
					}
				}

				$summary .= wpv_get_usermeta_field_summary($name, $view_settings);

			}
		}


		if ($td != '') { ?>
			<li id='js-row-usermeta-field' class='filter-row-multiple js-filter-row js-filter-row-multiple js-filter-for-posts js-filter-usermeta-field js-filter-row-usermeta-field'>
				<p class='edit-filter js-wpv-filter-edit-controls'>
					<i class='button-secondary icon-edit icon-large edit-trigger js-wpv-filter-edit-open' title='<?php echo esc_attr( __('Edit this filter','wpv-views') ); ?>'></i>
					<i class='button-secondary icon-trash icon-large js-filter-usermeta-field-row-remove' title='<?php echo esc_attr( __('Delete this filter','wpv-views') );?>' data-nonce='<?php echo wp_create_nonce( 'wpv_view_filter_usermeta_field_row_delete_nonce' ); ?>'></i>
				</p>
				<?php if ($summary != '') { ?>
					<p class='wpv-filter-edit-summary wpv-filter-usermeta-field-edit-summary js-wpv-filter-summary js-wpv-filter-usermeta-field-summary'>
					<?php _e('Select users with usermeta field: ', 'wpv-views');
					echo $summary; ?>
					</p>
				<?php } ?>
				<div id="wpv-filter-usermeta-field-edit" class="wpv-filter-edit js-filter-usermeta-field-edit js-wpv-filter-edit">
				<?php echo $td; ?>
					<div class="wpv-filter-usermeta-field-relationship js-wpv-filter-usermeta-field-relationship-container">
						<p><strong><?php _e('Usermeta field relationship:', 'wpv-views') ?></strong></p>
						<p>
							<?php _e('Relationship to use when querying with multiple user fields:', 'wpv-views'); ?>
							<select name="usermeta_fields_relationship" class="js-wpv-filter-usermeta-fields-relationship">
								<option value="AND"><?php _e('AND', 'wpv-views'); ?>&nbsp;</option>
								<?php $selected = $view_settings['usermeta_fields_relationship']=='OR' ? ' selected="selected"' : ''; ?>
								<option value="OR" <?php echo $selected ?>><?php _e('OR', 'wpv-views'); ?>&nbsp;</option>
							</select>
						</p>
					</div>
					<p>
						<input class="button-secondary js-wpv-filter-edit-ok js-wpv-filter-usermeta-field-edit-ok" type="button" value="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-save="<?php echo htmlentities( __('Save', 'wpv-views'), ENT_QUOTES ); ?>" data-close="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-success="<?php echo htmlentities( __('Updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_usermeta_field_nonce' ); ?>" />
					</p>
					<p class="wpv-usermeta-fields-help">
						<?php echo sprintf(__('%sLearn about filtering by user fields%s', 'wpv-views'),
										'<a class="wpv-help-link" href="' . WPV_FILTER_BY_USER_FIELDS_LINK . '" target="_blank">',
										' &raquo;</a>'
										); ?>
					</p>
				</div>
			</li>
		<?php }
	}

	function wpv_get_list_item_ui_post_usermeta_field($type, $usermeta_field, $view_settings = array()) {
		$field_name = substr($type, strlen('usermeta-field-'));
		$args = array('name' => $field_name);

		if (sizeof($view_settings) == 0) {
			$view_settings[$type . '_compare'] = $usermeta_field['compare'];
			$view_settings[$type . '_type'] = $usermeta_field['type'];
			$view_settings[$type . '_value'] = $usermeta_field['value'];
		}
		
		$all_types_fields = get_option( 'wpcf-fields', array() );
		$field_nicename = '';
		if (stripos($field_name, 'wpcf-') === 0) {
			if ( isset( $all_types_fields[substr( $field_name, 5 )] ) && isset( $all_types_fields[substr( $field_name, 5 )]['name'] ) ) {
				$field_nicename = $all_types_fields[substr( $field_name, 5 )]['name'];
			} else {
				$field_nicename = $field_name;
			}
		} else {
			$field_nicename = $field_name;
		}

		ob_start();

		?>
		<fieldset class="wpv-usermeta-field-edit-row wpv-filter-row-multiple-element js-filter-row-multiple-element js-filter-row-usermeta-field-<?php echo $field_name; ?>" data-field="<?php echo $field_name; ?>"><p class="edit-filter js-wpv-filter-usermeta-field-controls"><i class="icon-remove-sign js-filter-remove" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_usermeta_field_delete_nonce' );?>"></i></p>
			<p><strong><?php echo __('User field', 'wpv_views') . ' - ' . $field_nicename; ?>:</strong></p>
			<?php wpv_render_usermeta_field_options($args, $view_settings); ?>
		</fieldset>
		<?php

		$buffer = ob_get_clean();

		return $buffer;
	}

	function wpv_render_usermeta_field_options($args, $view_settings = null) {

		$compare = array('=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN');
		$types = array('CHAR', 'NUMERIC', 'BINARY', 'DATE', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED');

		if($view_settings === null) {
			$value = '';
			$compare_selected = '';
			$type_selected = '';
			$name = 'usermeta-field-' . str_replace(' ', '_', $args['name']) . '%s';
			$parts = array($value);
		} else {
			$value = $view_settings['usermeta-field-' . str_replace(' ', '_', $args['name']) . '_value'];

			$value = _wpv_encode_date($value);

			$compare_selected = $view_settings['usermeta-field-' . str_replace(' ', '_', $args['name']) . '_compare'];
			$compare_count = 1;
			$parts = array($value);
			switch($compare_selected) {
				case 'BETWEEN':
				case 'NOT BETWEEN':
					$compare_count = 2;
					$parts = explode(',', $value);

					// Make sure we have only 2 items.
					while (count($parts) < 2) {
						$parts[] = '';
					}
					while (count($parts) > 2) {
						array_pop($parts);
					}
					break;

				case 'IN':
				case 'NOT IN':
					$parts = explode(',', $value);
					$compare_count = count($parts);
					if ($compare_count < 1) {
						$compare_count = 1;
						$parts = array($value);
					}
					break;

			}

			$value = _wpv_unencode_date($value);

			$type_selected = $view_settings['usermeta-field-' . str_replace(' ', '_', $args['name']) . '_type'];
			$name = 'usermeta-field-' . str_replace(' ', '_', $args['name']) . '%s';
		}


		?>
			<p class="js-wpv-usermeta-comare-box"><?php _e('Comparison function:', 'wpv-views'); ?></p>
			<p class="js-wpv-usermeta-comare-box">
				<select name="<?php echo sprintf($name, '_compare'); ?>" class="wpv_usermeta_field_compare_select js-wpv-usermeta-field-compare-select">
					<?php
						foreach($compare as $com) {
							$selected = $compare_selected == $com ? ' selected="selected"' : '';
							echo '<option value="'. $com . '" '. $selected . '>' . $com . '&nbsp;</option>';
						}
					?>
				</select>
				<select name="<?php echo sprintf($name, '_type'); ?>" class="js-wpv-usermeta-field-type-select">
					<?php
						foreach($types as $type) {
							$selected = $type_selected == $type ? ' selected="selected"' : '';
							echo '<option value="'. $type . '" '. $selected . '>' . $type . '&nbsp;</option>';
						}
					?>
				</select>
			</p>

			<div class="js-wpv-usermeta-field-values">

				<?php // This is where we store the actual value derived from the follow controls ?>
				<input type="hidden" class="js-wpv-usermeta-field-values-real" name="<?php echo sprintf($name, '_value'); ?>" value="<?php echo $value; ?>" />

				<?php

					for ($i = 0; $i < count($parts); $i++) {

						echo '<div class="wpv_usermeta_field_value_div js-wpv-usermeta-field-value-div">';


						$options = array();
						$options[__('Constant', 'wpv-views') . '&nbsp'] = 'constant';
						$options[__('URL parameter', 'wpv-views') . '&nbsp'] = 'url';
						$options[__('Shortcode attribute', 'wpv-views') . '&nbsp'] = 'attribute';
						$options['NOW&nbsp'] = 'now';
						$options['TODAY&nbsp;'] = 'today';
						$options['FUTURE_DAY&nbsp;'] = 'future_day';
						$options['PAST_DAY&nbsp;'] = 'past_day';
						$options['THIS_MONTH&nbsp;'] = 'this_month';
						$options['FUTURE_MONTH&nbsp;'] = 'future_month';
						$options['PAST_MONTH&nbsp;'] = 'past_month';
						$options['THIS_YEAR&nbsp;'] = 'this_year';
						$options['FUTURE_YEAR&nbsp;'] = 'future_year';
						$options['PAST_YEAR&nbsp;'] = 'past_year';
						$options['SECONDS_FROM_NOW&nbsp;'] = 'seconds_from_now';
						$options['MONTHS_FROM_NOW&nbsp;'] = 'months_from_now';
						$options['YEARS_FROM_NOW&nbsp;'] = 'years_from_now';
						$options['DATE&nbsp;'] = 'date';

						$function_value = _wpv_get_custom_filter_function_and_value($parts[$i]);

						echo wpv_form_control(array('field' => array(
								'#name' => 'wpv_usermeta_field_compare_mode-' . $args['name'] . $i ,
								'#type' => 'select',
								'#attributes' => array('style' => '',
								'class' => 'wpv_usermeta_field_compare_mode js-wpv-usermeta-field-compare-mode'),
								'#inline' => true,
								'#options' => $options,
								'#default_value' => $function_value['function'],
						)));

						echo '<input type="text" class="js-wpv-usermeta-field-value-text js-wpv-usermeta-field-' . $args['name'] . '-value-text" value="' . $function_value['value'] . '" data-class="js-wpv-usermeta-field-' . $args['name'] . '-value-text" data-type="none" name="js-wpv-usermeta-field-' . $args['name'] . '-value-text" />';

						// Add controls for entering the date.
						_wpv_usermeta_field_date_controls($function_value['function'], $function_value['value']);

						?><input type="button" class="button-secondary js-wpv-usermeta-field-remove-value" value="<?php echo __('Remove', 'wpv-views'); ?>">
						<?php

						echo '</div>';

					}
				?>
				<p>
					<input type="button" class="button-secondary js-wpv-usermeta-field-add-value" value="<?php echo __('Add another value', 'wpv-views'); ?>"/>
				</p>

			</div>

	<?php
	}

	function _wpv_usermeta_field_date_controls($function, $value) {

		global $wp_locale;

		if ($function == 'date') {
			$date_parts = explode(',', $value);
			$time_adj = mktime(0, 0, 0, $date_parts[1], $date_parts[0], $date_parts[2]);
		} else {
			$time_adj = current_time('timestamp');
		}
		$jj = gmdate( 'd', $time_adj );
		$mm = gmdate( 'm', $time_adj );
		$aa = gmdate( 'Y', $time_adj );

		echo '<span class="js-wpv-usermeta-field-date js">' . "\n";

		$month = "<select >\n";
		for ( $i = 1; $i < 13; $i = $i +1 ) {
			$monthnum = zeroise($i, 2);
			$month .= '<option value="' . $monthnum . '"';
			if ( $i == $mm )
				$month .= ' selected="selected"';
			$month .= '>' . $monthnum . '-' . $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ) . "</option>\n";
		}
		$month .= '</select>';

		$day = '<input type="text" value="' . $jj . '" size="2" maxlength="2" autocomplete="off" />';
		$year = '<input type="text" value="' . $aa . '" size="4" maxlength="4" autocomplete="off" />';

		printf(__('%1$s%2$s, %3$s'), $month, $day, $year);

		echo "</span>\n";
	}

	add_action('wp_ajax_wpv_filter_usermeta_field_update', 'wpv_filter_usermeta_field_update_callback');

	function wpv_filter_usermeta_field_update_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_usermeta_field_nonce') ) die("Security check");
		$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
		$change = false;
		$summary = '';
		$filter_usermeta_fields = json_decode(stripslashes($_POST["filter_usermeta_fields"]), true);
		foreach ($filter_usermeta_fields as $filter_key => $filter_data) {
			if ( !isset( $view_array[$filter_key] ) || $filter_data != $view_array[$filter_key] ) {
				$change = true;
				$view_array[$filter_key] = $filter_data;
			}
		}
		if (!isset($_POST['filter_usermeta_fields_relationship'])) $_POST['filter_usermeta_fields_relationship'] = 'OR';
		if (!isset($view_array['usermeta_fields_relationship']) || $view_array['usermeta_fields_relationship'] != $_POST['filter_usermeta_fields_relationship']) {
			$view_array['usermeta_fields_relationship'] = $_POST['filter_usermeta_fields_relationship'];
			$change = true;
		}
		if ( $change ) {
			$result = update_post_meta($_POST["id"], '_wpv_settings', $view_array);
		}
		foreach (array_keys($view_array) as $key) {
			if (strpos($key, 'usermeta-field-') === 0 && strpos($key, '_compare') === strlen($key) - strlen('_compare')) {
				$name = substr($key, 0, strlen($key) - strlen('_compare'));
				if ($summary != '') {
					if ($view_array['usermeta_fields_relationship'] == 'OR') {
						$summary .= __(' OR', 'wpv-views');
					} else {
						$summary .= __(' AND', 'wpv-views');
					}
				}
				$summary .= wpv_get_usermeta_field_summary($name, $view_array);
			}
		}
		_e('Select users with usermeta field: ', 'wpv-views');
		echo $summary;
		die();
	}

	add_action('wp_ajax_wpv_filter_usermeta_field_delete', 'wpv_filter_usermeta_field_delete_callback');

	function wpv_filter_usermeta_field_delete_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_usermeta_field_delete_nonce') ) die("Security check");
		$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
		$field = $_POST['field'];
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
		$len = isset( $view_array['filter_controls_field_name'] ) ? count( $view_array['filter_controls_field_name'] ) : 0;
		$splice = false;
		for( $i = 0; $i < $len; $i++ )
		{
			if( strpos( $view_array['filter_controls_field_name'][$i], $field ) !== false ){
				$splice = $i;
			}
		}
		
		if( $splice !== false )
		{
			foreach( Editor_addon_parametric::$prm_db_fields as $dbf )
			{

				array_splice($view_array[$dbf], $splice, 1);
			}
		}
		

		update_post_meta($_POST["id"], '_wpv_settings', $view_array);
		echo $_POST['id'];
		die();
	}
    
}

function wpv_get_usermeta_field_summary($type, $view_settings = array()) {
	$field_name = substr($type, strlen('usermeta-field-'));
	$args = array('name' => $field_name);
	$all_types_fields = get_option( 'wpcf-fields', array() );
	$field_nicename = '';
	if (stripos($field_name, 'wpcf-') === 0) {
		if ( isset( $all_types_fields[substr( $field_name, 5 )] ) && isset( $all_types_fields[substr( $field_name, 5 )]['name'] ) ) {
			$field_nicename = $all_types_fields[substr( $field_name, 5 )]['name'];
		} else {
			$field_nicename = $field_name;
		}
	} else {
		$field_nicename = $field_name;
	}
	ob_start();
	
	?>
	<span class="wpv-filter-multiple-summary-item">
	<strong><?php echo $field_nicename . ' ' . $view_settings[$type . '_compare'] . ' ' . str_replace( ',', ', ', $view_settings[$type . '_value'] ); ?></strong>
	</span>
	<?php
	
	$buffer = ob_get_clean();
	
	return $buffer;
}

function _wpv_encode_date2($value) {
	if (preg_match_all('/DATE\(([\\d,-]*)\)/', $value, $matches)) {
        foreach($matches[0] as $match) {
			$value = str_replace($match, str_replace(',', '####coma####', $match), $value);
		}		
	}
	
	return $value;
}

function _wpv_unencode_date2($value) {
	return str_replace('####coma####', ',', $value);
}

function _wpv_get_custom_filter_function_and_value2($value) {
	$trim = trim($value);
	$function = 'constant';
	$return_val = $value;
	$text_boxes = 1;
	
	$singles = array('url' => '/^URL_PARAM\((.*?)\)/',
					 'attribute' => '/^VIEW_PARAM\((.*?)\)/',
					 'future_day' => '/^FUTURE_DAY\((.*?)\)/',
					 'past_day' => '/^PAST_DAY\((.*?)\)/',
					 'future_month' => '/^FUTURE_MONTH\((.*?)\)/',
					 'past_month' => '/^PAST_MONTH\((.*?)\)/',
					 'future_year' => '/^FUTURE_YEAR\((.*?)\)/',
					 'past_year' => '/^PAST_YEAR\((.*?)\)/',
					 'seconds_from_now' => '/^SECONDS_FROM_NOW\((.*?)\)/',
					 'months_from_now' => '/^MONTHS_FROM_NOW\((.*?)\)/',
					 'years_from_now' => '/^YEARS_FROM_NOW\((.*?)\)/',
					 'date' => '/^DATE\((.*?)\)/');
					 
	
	foreach($singles as $code => $pattern) {
		if (preg_match($pattern, $trim, $matches) == 1) {
			$function = $code;
			$return_val = $matches[1];
			break;
		}
	}

	$zeros = array('now' => '/^NOW\((.*?)\)/',
				   'today' => '/^TODAY\((.*?)\)/',
				   'this_month' => '/^THIS_MONTH\((.*?)\)/',
				   'this_year' => '/^THIS_YEAR\((.*?)\)/');

	foreach($zeros as $code => $pattern) {
		if (preg_match($pattern, $trim, $matches) == 1) {
			$function = $code;
			$return_val = '';
			$text_boxes = 0;
			break;
		}
	}
	
	$return_val = str_replace('####coma####', ',', $return_val);

	return array('function' => $function, 'value' => $return_val, 'text_boxes' => $text_boxes);
}

add_filter('wpv-view-get-summary', 'wpv_usermeta_field_summary_filter', 7, 3);

function wpv_usermeta_field_summary_filter($summary, $post_id, $view_settings) {
	$result = '';
	if(isset($view_settings['query_type']) && $view_settings['query_type'][0] == 'posts') {
		$count = 0;
		foreach (array_keys($view_settings) as $key) {
			if (strpos($key, 'usermeta-field-') === 0 && strpos($key, '_compare') === strlen($key) - strlen('_compare')) {
				$name = substr($key, 0, strlen($key) - strlen('_compare'));
	
				$count++;
					
				if ($result != '') {
					if (isset($view_settings['usermeta_fields_relationship']) && $view_settings['usermeta_fields_relationship'] == 'OR') {
						$result .= __(' OR', 'wpv-views');
					} else {
						$result .= __(' AND', 'wpv-views');
					}
				}
					
				$result .= wpv_get_usermeta_field_summary($name, $view_settings);
						
			}
		}
	}

	if ($result != '' && $summary != '') {
		$summary .= '<br />';
	}
	$summary .= $result;
	return $summary;
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
