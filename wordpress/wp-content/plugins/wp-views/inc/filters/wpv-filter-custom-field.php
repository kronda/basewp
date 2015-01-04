<?php

if(is_admin()){

	add_action('wpv_add_filter_list_item', 'wpv_add_filter_custom_field_list_item', 1, 1);
	add_filter('wpv_filters_add_filter', 'wpv_filters_add_filter_custom_field', 20, 2);
	
	// TODO check what happens with all the _compare, _type and _value when the meta key has a space AND an underscore?

	function wpv_filters_add_filter_custom_field($filters) {
		global $WP_Views;
		$meta_keys = $WP_Views->get_meta_keys();
		$all_types_fields = get_option( 'wpcf-fields', array() );
		foreach ($meta_keys as $key) {
			$key_nicename = '';
			if (stripos($key, 'wpcf-') === 0) {
				if ( isset( $all_types_fields[substr( $key, 5 )] ) && isset( $all_types_fields[substr( $key, 5 )]['name'] ) ) {
					$key_nicename = $all_types_fields[substr( $key, 5 )]['name'];
				} else {
					$key_nicename = $key;
				}
			} else if (stripos($key, 'views_woo_') === 0) {
				if ( isset( $all_types_fields[$key] ) && isset( $all_types_fields[$key]['name'] ) ) {
					$key_nicename = $all_types_fields[$key]['name'];
				} else {
					$key_nicename = $key;
				}
			} else {
				$key_nicename = $key;
			}
			// Check if the field is in a Types group - if not, register with the full $key
			if( function_exists('wpcf_admin_fields_get_groups_by_field') ) {
				$g = '';
				foreach( wpcf_admin_fields_get_groups_by_field( $key_nicename ) as $gs ) {
					$g = $gs['name'];
				}
				$key_nicename = $g ? $key_nicename : $key;
			}
			$filters['custom-field-' . str_replace(' ', '_', $key)] = array('name' => sprintf(__('Custom field - %s', 'wpv-views'), $key_nicename),
										'present' => 'custom-field-' . $key . '_compare',
										'callback' => 'wpv_add_new_filter_custom_field_list_item',
										'args' => array('name' =>'custom-field-' . $key));
		}

		return $filters;
	}

	function wpv_add_new_filter_custom_field_list_item($args) {
		echo wpv_get_list_item_ui_post_custom_field($args['name'],null, array());
	}

	function wpv_add_filter_custom_field_list_item($view_settings) {
		if (!isset($view_settings['custom_fields_relationship'])) {
			$view_settings['custom_fields_relationship'] = 'AND';
		}

		// Find any custom fields

		$summary = '';
		$td = '';
		$count = 0;

		foreach (array_keys($view_settings) as $key) {
			if (strpos($key, 'custom-field-') === 0 && strpos($key, '_compare') === strlen($key) - strlen('_compare')) {
				$name = substr($key, 0, strlen($key) - strlen('_compare'));

				$td .= wpv_get_list_item_ui_post_custom_field($name, null, $view_settings);
				$count++;

				if ($summary != '') {
					if ($view_settings['custom_fields_relationship'] == 'OR') {
						$summary .= __(' OR', 'wpv-views');
					} else {
						$summary .= __(' AND', 'wpv-views');
					}
				}

				$summary .= wpv_get_custom_field_summary($name, $view_settings);

			}
		}


		if ($td != '') { ?>
			<li id='js-row-custom-field' class='filter-row-multiple js-filter-row js-filter-row-multiple js-filter-for-posts js-filter-custom-field js-filter-row-custom-field'>
				<p class='edit-filter js-wpv-filter-edit-controls'>
					<i class='button-secondary icon-edit icon-large edit-trigger js-wpv-filter-edit-open' title='<?php echo esc_attr( __('Edit this filter','wpv-views') ); ?>'></i>
					<i class='button-secondary icon-trash icon-large js-filter-custom-field-row-remove' title='<?php echo esc_attr( __('Delete this filter','wpv-views') ); ?>' data-nonce='<?php echo wp_create_nonce( 'wpv_view_filter_custom_field_row_delete_nonce' ); ?>'></i>
				</p>
				<?php if ($summary != '') { ?>
					<p class='wpv-filter-edit-summary wpv-filter-custom-field-edit-summary js-wpv-filter-summary js-wpv-filter-custom-field-summary'>
					<?php _e('Select posts with custom field: ', 'wpv-views');
					echo $summary; ?>
					</p>
				<?php } ?>
				<div id="wpv-filter-custom-field-edit" class="wpv-filter-edit js-filter-custom-field-edit js-wpv-filter-edit">
				<?php echo $td; ?>
					<div class="wpv-filter-custom-field-relationship js-wpv-filter-custom-field-relationship-container">
						<p><strong><?php _e('Custom field relationship:', 'wpv-views') ?></strong></p>
						<p>
							<?php _e('Relationship to use when querying with multiple custom fields:', 'wpv-views'); ?>
							<select name="custom_fields_relationship" class="js-wpv-filter-custom-fields-relationship">
								<option value="AND"><?php _e('AND', 'wpv-views'); ?>&nbsp;</option>
								<?php $selected = $view_settings['custom_fields_relationship']=='OR' ? ' selected="selected"' : ''; ?>
								<option value="OR"<?php echo $selected ?>><?php _e('OR', 'wpv-views'); ?>&nbsp;</option>
							</select>
						</p>
					</div>
					<p>
						<input class="button-secondary js-wpv-filter-edit-ok js-wpv-filter-custom-field-edit-ok" type="button" value="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-save="<?php echo htmlentities( __('Save', 'wpv-views'), ENT_QUOTES ); ?>" data-close="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-success="<?php echo htmlentities( __('Updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_custom_field_nonce' ); ?>" />
					</p>
					<p class="wpv-custom-fields-help">
						<?php echo sprintf(__('%sLearn about filtering by custom fields%s', 'wpv-views'),
										'<a class="wpv-help-link" href="' . WPV_FILTER_BY_CUSTOM_FIELD_LINK . '" target="_blank">',
										' &raquo;</a>'
										); ?>
					</p>
				</div>
			</li>
		<?php }
	}

	function wpv_get_list_item_ui_post_custom_field($type, $custom_field, $view_settings = array()) {
		$field_name = substr($type, strlen('custom-field-'));
		$args = array('name' => $field_name);

		if (sizeof($view_settings) == 0) {
			$view_settings[$type . '_compare'] = $custom_field['compare'];
			$view_settings[$type . '_type'] = $custom_field['type'];
			$view_settings[$type . '_value'] = $custom_field['value'];
		}
		
		$all_types_fields = get_option( 'wpcf-fields', array() );
		$field_nicename = '';
		if (stripos($field_name, 'wpcf-') === 0) {
			if ( isset( $all_types_fields[substr( $field_name, 5 )] ) && isset( $all_types_fields[substr( $field_name, 5 )]['name'] ) ) {
				$field_nicename = $all_types_fields[substr( $field_name, 5 )]['name'];
			} else {
				$field_nicename = $field_name;
			}
		} else if (stripos($field_name, 'views_woo_') === 0) {
			if ( isset( $all_types_fields[$field_name] ) && isset( $all_types_fields[$field_name]['name'] ) ) {
				$field_nicename = $all_types_fields[$field_name]['name'];
			} else {
				$field_nicename = $field_name;
			}
		} else {
			$field_nicename = $field_name;
		}
		
		// Check if the field is in a Types group - if not, register with the full $key
		if( function_exists('wpcf_admin_fields_get_groups_by_field') ) {
			$g = '';
			foreach( wpcf_admin_fields_get_groups_by_field( $field_nicename ) as $gs ) {
				$g = $gs['name'];
			}
			$field_nicename = $g ? $field_nicename : $field_name;
		}

		ob_start();

		?>
		<div class="wpv-custom-field-edit-row wpv-filter-row-multiple-element js-filter-row-multiple-element js-filter-row-custom-field-<?php echo $field_name; ?>" data-field="<?php echo $field_name; ?>"><p class="edit-filter js-wpv-filter-custom-field-controls"><i class="button-secondary icon-trash icon-large js-filter-remove" title="<?php _e('Delete this filter','wpv-views'); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_custom_field_delete_nonce' );?>"></i></p>
			<p><strong><?php echo __('Custom field', 'wpv_views') . ' - ' . $field_nicename; ?>:</strong></p>
			<?php wpv_render_custom_field_options($args, $view_settings); ?>
		</div>
		<?php

		$buffer = ob_get_clean();

		return $buffer;
	}

	function wpv_render_custom_field_options($args, $view_settings = null) {

		$compare = array('=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN');
		$types = array('CHAR', 'NUMERIC', 'BINARY', 'DATE', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED');

		if($view_settings === null) {
			$value = '';
			$compare_selected = '';
			$type_selected = '';
			$name = 'custom-field-' . str_replace(' ', '_', $args['name']) . '%s';
			$parts = array($value);
		} else {
			$value = $view_settings['custom-field-' . str_replace(' ', '_', $args['name']) . '_value'];

			$value = _wpv_encode_date($value);

			$compare_selected = $view_settings['custom-field-' . str_replace(' ', '_', $args['name']) . '_compare'];
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

			$type_selected = $view_settings['custom-field-' . str_replace(' ', '_', $args['name']) . '_type'];
			$name = 'custom-field-' . str_replace(' ', '_', $args['name']) . '%s';
		}


		?>
			<?php _e('Comparison function:', 'wpv-views'); ?>
			<p>
				<select name="<?php echo sprintf($name, '_compare'); ?>" class="wpv_custom_field_compare_select js-wpv-custom-field-compare-select">
					<?php
						foreach($compare as $com) {
							$selected = $compare_selected == $com ? ' selected="selected"' : '';
							echo '<option value="'. $com . '" '. $selected . '>' . $com . '&nbsp;</option>';
						}
					?>
				</select>
				<select name="<?php echo sprintf($name, '_type'); ?>" class="js-wpv-custom-field-type-select">
					<?php
						foreach($types as $type) {
							$selected = $type_selected == $type ? ' selected="selected"' : '';
							echo '<option value="'. $type . '" '. $selected . '>' . $type . '&nbsp;</option>';
						}
					?>
				</select>
			</p>

			<div class="js-wpv-custom-field-values">

				<?php // This is where we store the actual value derived from the follow controls ?>
				<input type="hidden" class="js-wpv-custom-field-values-real" name="<?php echo sprintf($name, '_value'); ?>" value="<?php echo $value; ?>" />

				<?php

					for ($i = 0; $i < count($parts); $i++) {

						echo '<div class="wpv_custom_field_value_div js-wpv-custom-field-value-div">';


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
								'#name' => 'wpv_custom_field_compare_mode-' . $args['name'] . $i ,
								'#type' => 'select',
								'#attributes' => array('style' => '',
								'class' => 'wpv_custom_field_compare_mode js-wpv-custom-field-compare-mode'),
								'#inline' => true,
								'#options' => $options,
								'#default_value' => $function_value['function'],
						)));

						echo '<input type="text" class="js-wpv-custom-field-value-text js-wpv-custom-field-' . $args['name'] . '-value-text" value="' . $function_value['value'] . '" data-class="js-wpv-custom-field-' . $args['name'] . '-value-text" data-type="none" name="js-wpv-custom-field-' . $args['name'] . '-value-text" />';

						// Add controls for entering the date.
						_wpv_custom_field_date_controls($function_value['function'], $function_value['value']);

						?><input type="button" class="button-secondary js-wpv-custom-field-remove-value" value="<?php echo __('Remove', 'wpv-views'); ?>">
						<?php

						echo '</div>';

					}
				?>
				<p>
					<input type="button" class="button-secondary js-wpv-custom-field-add-value" value="<?php echo __('Add another value', 'wpv-views'); ?>"/>
				</p>

			</div>

	<?php
	}

	function _wpv_custom_field_date_controls($function, $value) {

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

		echo '<span class="js-wpv-custom-field-date js">' . "\n";

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

	add_action('wp_ajax_wpv_filter_custom_field_update', 'wpv_filter_custom_field_update_callback');

	function wpv_filter_custom_field_update_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_custom_field_nonce') ) die("Security check");
		$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
		$change = false;
		$summary = '';
		$filter_custom_fields = json_decode(stripslashes($_POST["filter_custom_fields"]), true);
		foreach ($filter_custom_fields as $filter_key => $filter_data) {
			if ( !isset( $view_array[$filter_key] ) || $filter_data != $view_array[$filter_key] ) {
				$change = true;
				$view_array[$filter_key] = $filter_data;
			}
		}
		if (!isset($_POST['filter_custom_fields_relationship'])) $_POST['filter_custom_fields_relationship'] = 'OR';
		if (!isset($view_array['custom_fields_relationship']) || $view_array['custom_fields_relationship'] != $_POST['filter_custom_fields_relationship']) {
			$view_array['custom_fields_relationship'] = $_POST['filter_custom_fields_relationship'];
			$change = true;
		}
		if ( $change ) {
			$result = update_post_meta($_POST["id"], '_wpv_settings', $view_array);
		}
		foreach (array_keys($view_array) as $key) {
			if (strpos($key, 'custom-field-') === 0 && strpos($key, '_compare') === strlen($key) - strlen('_compare')) {
				$name = substr($key, 0, strlen($key) - strlen('_compare'));
				if ($summary != '') {
					if ($view_array['custom_fields_relationship'] == 'OR') {
						$summary .= __(' OR', 'wpv-views');
					} else {
						$summary .= __(' AND', 'wpv-views');
					}
				}
				$summary .= wpv_get_custom_field_summary($name, $view_array);
			}
		}
		_e('Select posts with custom field: ', 'wpv-views');
		echo $summary;
		die();
	}

	add_action('wp_ajax_wpv_filter_custom_field_delete', 'wpv_filter_custom_field_delete_callback');

	function wpv_filter_custom_field_delete_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_custom_field_delete_nonce') ) die("Security check");
		$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
		$field = $_POST['field'];
		$to_delete = array(
			'custom-field-' . $field . '_compare',
			'custom-field-' . $field . '_type',
			'custom-field-' . $field . '_value'
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

function wpv_get_custom_field_summary($type, $view_settings = array()) {
	$field_name = substr($type, strlen('custom-field-'));
	$args = array('name' => $field_name);
	$all_types_fields = get_option( 'wpcf-fields', array() );
	$field_nicename = '';
	if (stripos($field_name, 'wpcf-') === 0) {
		if ( isset( $all_types_fields[substr( $field_name, 5 )] ) && isset( $all_types_fields[substr( $field_name, 5 )]['name'] ) ) {
			$field_nicename = $all_types_fields[substr( $field_name, 5 )]['name'];
		} else {
			$field_nicename = $field_name;
		}
	} else if (stripos($field_name, 'views_woo_') === 0) {
		if ( isset( $all_types_fields[$field_name] ) && isset( $all_types_fields[$field_name]['name'] ) ) {
			$field_nicename = $all_types_fields[$field_name]['name'];
		} else {
			$field_nicename = $field_name;
		}
	} else {
		$field_nicename = $field_name;
	}
	
	// Check if the field is in a Types group - if not, register with the full $key
	if( function_exists('wpcf_admin_fields_get_groups_by_field') ) {
		$g = '';
		foreach( wpcf_admin_fields_get_groups_by_field( $field_nicename ) as $gs ) {
			$g = $gs['name'];
		}
		$field_nicename = $g ? $field_nicename : $field_name;
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

function _wpv_encode_date($value) {
	if (preg_match_all('/DATE\(([\\d,-]*)\)/', $value, $matches)) {
        foreach($matches[0] as $match) {
			$value = str_replace($match, str_replace(',', '####coma####', $match), $value);
		}		
	}
	
	return $value;
}

function _wpv_unencode_date($value) {
	return str_replace('####coma####', ',', $value);
}

function _wpv_get_custom_filter_function_and_value($value) {
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

add_filter('wpv-view-get-summary', 'wpv_custom_field_summary_filter', 7, 3);

function wpv_custom_field_summary_filter($summary, $post_id, $view_settings) {
	$result = '';
	if(isset($view_settings['query_type']) && $view_settings['query_type'][0] == 'posts') {
		$count = 0;
		foreach (array_keys($view_settings) as $key) {
			if (strpos($key, 'custom-field-') === 0 && strpos($key, '_compare') === strlen($key) - strlen('_compare')) {
				$name = substr($key, 0, strlen($key) - strlen('_compare'));
	
				$count++;
					
				if ($result != '') {
					if (isset($view_settings['custom_fields_relationship']) && $view_settings['custom_fields_relationship'] == 'OR') {
						$result .= __(' OR', 'wpv-views');
					} else {
						$result .= __(' AND', 'wpv-views');
					}
				}
					
				$result .= wpv_get_custom_field_summary($name, $view_settings);
						
			}
		}
	}

	if ($result != '' && $summary != '') {
		$summary .= '<br />';
	}
	$summary .= $result;
	return $summary;
}


function wpv_custom_fields_get_url_params($view_settings) {
	global $WP_Views;

	$pattern = '/URL_PARAM\(([^(]*?)\)/siU';
	$meta_keys = $WP_Views->get_meta_keys();
	
	$results = array();

	foreach (array_keys($view_settings) as $key) {
		if (strpos($key, 'custom-field-') === 0 && strpos($key, '_compare') === strlen($key) - strlen('_compare')) {
			$name = substr($key, 0, strlen($key) - strlen('_compare'));
			$name = substr($name, strlen('custom-field-'));
			
			$meta_name = $name;
			if (!in_array($meta_name, $meta_keys)) {
				$meta_name = str_replace('_', ' ', $meta_name);
			}

			$value = $view_settings['custom-field-' . $name . '_value'];
			
			if(preg_match_all($pattern, $value, $matches, PREG_SET_ORDER)) {
				foreach($matches as $match) {
					$results[] = array('name' => $name, 'param' => $match[1], 'mode' => 'cf');
				}
			}
		}
	}
	
	return $results;
}
