<?php

if(is_admin()){
	
	add_action('wpv_add_filter_list_item', 'wpv_add_filter_category_list_item', 1, 1);
	add_filter('wpv_filters_add_filter', 'wpv_filters_add_filter_category', 20, 2);

	function wpv_filters_add_filter_category($filters, $post_type) {
		$taxonomies = get_taxonomies('', 'objects');
		$taxonomies_valid = get_object_taxonomies( $post_type, 'names' );
		$exclude_tax_slugs = array();
		$exclude_tax_slugs = apply_filters( 'wpv_admin_exclude_tax_slugs', $exclude_tax_slugs );
		foreach ($taxonomies as $category_slug => $category) {
			if ( in_array($category_slug, $exclude_tax_slugs) ) {
				continue;
			}
			if ( !$category->show_ui ) {
				continue; // Only show taxonomies with show_ui set to TRUE
			}

			if ( !in_array($category_slug, $taxonomies_valid) ) {
				continue;
			}

			$taxonomy = $category->name;
			$name = ( $taxonomy == 'category' ) ? 'post_category' : 'tax_input[' . $taxonomy . ']';

			$filters[$name] = array('name' => $category->label,
						'present' => 'tax_' . $taxonomy . '_relationship',
						'callback' => 'wpv_add_new_filter_taxonomy_list_item',
						'args' => array('name' => $name, 'taxonomy' => $taxonomy));
		}

		// add a nonce field here.

		return $filters;
	}

	function wpv_add_new_filter_taxonomy_list_item($args) {
		echo wpv_get_list_item_ui_post_category($args['name'],array());
	}

	function wpv_add_filter_category_list_item($view_settings) {
		
		if (!isset($view_settings['taxonomy_relationship'])) {
			$view_settings['taxonomy_relationship'] = 'AND';
		}

		// Find any taxonomy

		$summary = '';
		$td = '';
		$count = 0;
		if (!isset( $view_settings['post_type'] ) ) $view_settings['post_type'] = array();
		$taxonomies_valid = get_object_taxonomies( $view_settings['post_type'], 'names' );
		$toolset_alert = '';

		$taxonomies = get_taxonomies('', 'objects');
		foreach ($taxonomies as $category_slug => $category) {
			$save_name = ( $category->name == 'category' ) ? 'post_category' : 'tax_input_' . $category->name;
			$relationship_name = ( $category->name == 'category' ) ? 'tax_category_relationship' : 'tax_' . $category->name . '_relationship';

			if (isset($view_settings[$relationship_name])) {

				if (!isset($view_settings[$save_name])) {
					$view_settings[$save_name] = array();
				}
				
				if (function_exists('icl_object_id')) {
					// Adjust for WPML support
					$trans_term_ids = array();
					foreach ( $view_settings[$save_name] as $untrans_term_id ) {
						$trans_term_ids[] = icl_object_id( $untrans_term_id, $category->name, true );
					}
					$view_settings[$save_name] = $trans_term_ids;
				}

				$name = ( $category->name == 'category' ) ? 'post_category' : 'tax_input[' . $category->name . ']';
				$td .= wpv_get_list_item_ui_post_category($name, $view_settings[$save_name], $view_settings);
				$count++;

				if ($summary != '') {
					if ($view_settings['taxonomy_relationship'] == 'OR') {
						$summary .= __(' OR ', 'wpv-views');
					} else {
						$summary .= __(' AND ', 'wpv-views');
					}
				}

				$summary .= wpv_get_taxonomy_summary($name, $view_settings, $view_settings[$save_name]);
				if ( !in_array($category->name, $taxonomies_valid) ) { // TODO this message should be handled using javascript
					$toolset_alert .= '<span class="toolset-alert js-filter-info"><i class="icon-warning-sign"></i> ' . $category->label . __(' is not a valid taxonomy for the post types you are trying to display.', 'wpv-views') . '</span>';
				}

			}
		}

		if ($td != '') {
			?>
			<li id='js-row-taxonomy' class='filter-row-multiple js-filter-row js-filter-row-multiple js-filter-for-posts js-filter-taxonomy js-filter-row-taxonomy'>
				<p class='edit-filter js-wpv-filter-edit-controls'>
					<i class='button-secondary icon-edit icon-large edit-trigger js-wpv-filter-edit-open' title='<?php echo esc_attr( __('Edit this filter', 'wpv-views') ); ?>'></i>
					<i class='button-secondary icon-trash icon-large js-filter-taxonomy-row-remove' title='<?php echo esc_attr( __('Delete this filter', 'wpv-views') ); ?>' data-nonce='<?php echo wp_create_nonce( 'wpv_view_filter_taxonomy_row_delete_nonce' ); ?>'></i>
				</p>
				<?php if ($summary != '') { ?>
					<p class='wpv-filter-edit-summary wpv-filter-taxonomy-edit-summary js-wpv-filter-summary js-wpv-filter-taxonomy-summary'>
					<?php _e('Select posts with taxonomy: ', 'wpv-views');
					echo $summary; ?>
					</p>
				<?php }
				echo $toolset_alert; ?>
				<div id="wpv-filter-taxonomy-edit" class="wpv-filter-edit js-filter-taxonomy-edit js-wpv-filter-edit">
				<?php echo $td;?>
					<div class="wpv-filter-taxonomy-relationship js-wpv-filter-taxonomy-relationship">
						<h4><?php _e('Taxonomy relationship:', 'wpv-views') ?></h4>
						<p>
							<?php _e('Relationship to use when querying with multiple taxonomies:', 'wpv-views'); ?>
							<select name="taxonomy_relationship">
								<option value="AND"><?php _e('AND', 'wpv-views'); ?>&nbsp;</option>
								<?php $selected = $view_settings['taxonomy_relationship']=='OR' ? ' selected="selected"' : ''; ?>
								<option value="OR"<?php echo $selected ?>><?php _e('OR', 'wpv-views'); ?>&nbsp;</option>
							</select>
						</p>
					</div>
					<p>
						<input class="button-secondary js-wpv-filter-edit-ok js-wpv-filter-taxonomy-edit-ok" type="button" value="<?php echo esc_attr( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-save="<?php echo esc_attr( __('Save', 'wpv-views'), ENT_QUOTES ); ?>" data-close="<?php echo esc_attr( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-success="<?php echo esc_attr( __('Updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo esc_attr( __('Not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_taxonomy_nonce' ); ?>" />
					</p>
					<p class="wpv-taxonomy-help">
					<?php echo sprintf(__('%sLearn about filtering by taxonomy%s', 'wpv-views'),
									'<a class="wpv-help-link" href="' . WPV_FILTER_BY_TAXONOMY_LINK . '" target="_blank">',
									' &raquo;</a>'
									); ?>
					</p>
				</div>
			</li>
	<?php }
	}

	function wpv_get_list_item_ui_post_category($type, $cats_selected, $view_settings = array()) {

		// find the matching category/taxonomy
		$taxonomy = 'category';
		$taxonomy_name = __('Categories', 'wpv-views');
		$taxonomies = get_taxonomies('', 'objects');
		foreach ($taxonomies as $category_slug => $category) {
			$name = ( $category->name == 'category' ) ? 'post_category' : 'tax_input[' . $category->name . ']';

			if ($name == $type) {
				// it's a category type.
				$taxonomy = $category->name;
				$taxonomy_name = $category->label;
				break;
			}
		}

		if (!isset($view_settings['tax_' . $taxonomy . '_relationship'])) {
			$view_settings['tax_' . $taxonomy . '_relationship'] = 'IN';
		}

		if (!isset($view_settings['taxonomy-' . $taxonomy . '-attribute-url']) || empty($view_settings['taxonomy-' . $taxonomy . '-attribute-url'])) {
			$view_settings['taxonomy-' . $taxonomy . '-attribute-url'] = 'wpv' . $taxonomy;
		}

		if (isset($view_settings['taxonomy-' . $taxonomy . '-attribute-url-format']) && is_array($view_settings['taxonomy-' . $taxonomy . '-attribute-url-format'])) {
			$view_settings['taxonomy-' . $taxonomy . '-attribute-url-format'] = $view_settings['taxonomy-' . $taxonomy . '-attribute-url-format'][0];
		}

		ob_start();
		?>
			<div class="wpv-taxonomy-edit-row wpv-filter-row-multiple-element js-filter-row-multiple-element js-filter-row-taxonomy-<?php echo $taxonomy; ?> js-filter-row-tax-<?php echo $type; ?>" data-taxonomy="<?php echo $taxonomy; ?>">

				<p class="edit-filter js-wpv-filter-taxonomy-controls">
					<i class="button-secondary icon-trash icon-large js-filter-remove" title="<?php echo esc_attr( sprintf( __('Delete this filter by %s', 'wpv-views'), $taxonomy_name ) ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_taxonomy_delete_nonce' );?>"></i>
				</p>

				<h4><?php echo __('Taxonomy', 'wpv-views') . ' - ' . $taxonomy_name; ?>:</h4>

				<?php _e('Taxonomy is:', 'wpv-views'); ?>
				<select class="wpv_taxonomy_relationship js-wpv-taxonomy-relationship" name="tax_<?php echo $taxonomy; ?>_relationship">
					<option value="IN"><?php _e('Any of the following', 'wpv-views'); ?>&nbsp;</option>
					<?php $selected = $view_settings['tax_' . $taxonomy . '_relationship']=='NOT IN' ? ' selected="selected"' : ''; ?>
					<option value="NOT IN" <?php echo $selected ?>><?php _e('NOT one of the following', 'wpv-views'); ?>&nbsp;</option>
					<?php $selected = $view_settings['tax_' . $taxonomy . '_relationship']=='AND' ? ' selected="selected"' : ''; ?>
					<option value="AND" <?php echo $selected ?>><?php _e('All of the following', 'wpv-views'); ?>&nbsp;</option>
					<?php $selected = $view_settings['tax_' . $taxonomy . '_relationship']=='FROM PAGE' ? ' selected="selected"' : ''; ?>
					<option value="FROM PAGE" <?php echo $selected ?>><?php _e('Value set by the current page', 'wpv-views'); ?>&nbsp;</option>
					<?php $selected = $view_settings['tax_' . $taxonomy . '_relationship']=='FROM ATTRIBUTE' ? ' selected="selected"' : ''; ?>
					<option value="FROM ATTRIBUTE" <?php echo $selected ?>><?php _e('Value set by View shortcode attribute', 'wpv-views'); ?>&nbsp;</option>
					<?php $selected = $view_settings['tax_' . $taxonomy . '_relationship']=='FROM URL' ? ' selected="selected"' : ''; ?>
					<option value="FROM URL" <?php echo $selected ?>><?php _e('Value set by URL parameter', 'wpv-views'); ?>&nbsp;</option>
					<?php $selected = $view_settings['tax_' . $taxonomy . '_relationship']=='FROM PARENT VIEW' ? ' selected="selected"' : ''; ?>
					<option value="FROM PARENT VIEW" <?php echo $selected ?>><?php _e('Value set by parent view', 'wpv-views'); ?>&nbsp;</option>
				</select>

				<ul id="taxonomy-<?php echo $taxonomy; ?>" class="categorychecklist form-no-clear wpv-filter-row-multiple-element-optionlist js-taxonomy-checklist">
					<?php wp_terms_checklist(0, array('taxonomy' => $taxonomy, 'selected_cats' => $cats_selected)) ?>
				</ul>

				<ul id="taxonomy-<?php echo $taxonomy; ?>-attribute-url" class="wpv-filter-row-multiple-element-optionlist js-taxonomy-parameter">
					<li>
						<label><?php echo __('Value: ');?></label>
						<?php
							if (!isset($view_settings['taxonomy-' . $taxonomy . '-attribute-url-format'])) {
								$view_settings['taxonomy-' . $taxonomy . '-attribute-url-format'] = 'slug';
							}
						?>
						<?php $checked = $view_settings['taxonomy-' . $taxonomy . '-attribute-url-format'] == 'name' ? 'checked="checked"' : ''; ?>
						<label><input type="radio" name="taxonomy-<?php echo $taxonomy; ?>-attribute-url-format[]" value="name" <?php echo $checked;?> /><?php echo __('Taxonomy name', 'wpv-views');?></label>
						<?php $checked = $view_settings['taxonomy-' . $taxonomy . '-attribute-url-format'] == 'slug' ? 'checked="checked"' : ''; ?>
						<label><input type="radio" name="taxonomy-<?php echo $taxonomy; ?>-attribute-url-format[]" value="slug" <?php echo $checked;?> /><?php echo __('Taxonomy slug', 'wpv-views');?></label>
					</li>
					<li>
						<label class="js-taxonomy-param-label" data-attribute="<?php echo __('Shortcode attribute', 'wpv-views');?>" data-parameter="<?php echo __('URL parameter', 'wpv-views');?>"><?php echo __('Shortcode attribute', 'wpv-views');?></label>:
						<input type="text" data-class="js-taxonomy-<?php echo $taxonomy; ?>-param" data-type="url" class="wpv_taxonomy_param js-taxonomy-param js-taxonomy-<?php echo $taxonomy; ?>-param js-wpv-filter-validate" name="taxonomy-<?php echo $taxonomy; ?>-attribute-url" value="<?php echo esc_attr($view_settings['taxonomy-' . $taxonomy . '-attribute-url']); ?>" />
					</li>
					<li>
						<?php
							if (!isset($view_settings['taxonomy-' . $taxonomy . '-attribute-operator'])) {
								$view_settings['taxonomy-' . $taxonomy . '-attribute-operator'] = 'IN';
							}
						?>
						<label for="taxonomy-<?php echo $taxonomy; ?>-attribute-operator"><?php echo __('Operator', 'wpv-views'); ?></label>
						<select name="taxonomy-<?php echo $taxonomy; ?>-attribute-operator" id="taxonomy-<?php echo $taxonomy; ?>-attribute-operator" class="js-taxonomy-<?php echo $taxonomy; ?>-attribute-operator">
							<option value="IN"<?php if ( $view_settings['taxonomy-' . $taxonomy . '-attribute-operator'] == 'IN' ) echo ' selected="selected"'; ?>><?php echo __('IN', 'wpv-views'); ?></option>
							<option value="NOT IN"<?php if ( $view_settings['taxonomy-' . $taxonomy . '-attribute-operator'] == 'NOT IN' ) echo ' selected="selected"'; ?>><?php echo __('NOT IN', 'wpv-views'); ?></option>
							<option value="AND"<?php if ( $view_settings['taxonomy-' . $taxonomy . '-attribute-operator'] == 'AND' ) echo ' selected="selected"'; ?>><?php echo __('AND', 'wpv-views'); ?></option>
						</select>
					</li>
				</ul>
			</div>

		<?php

		$buffer = ob_get_clean();

		$buffer = str_replace('tax_input[' . $category->name . ']', 'tax_input_' . $category->name, $buffer);

		return $buffer;
	}

	add_action('wp_ajax_wpv_filter_taxonomy_update', 'wpv_filter_taxonomy_update_callback');

	function wpv_filter_taxonomy_update_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_taxonomy_nonce') ) die("Security check");
		$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
		$change = false;
		parse_str($_POST['filter_taxonomy'], $filter_taxonomy);
		foreach ($filter_taxonomy as $filter_key => $filter_data) {
			if ( !isset( $view_array[$filter_key] ) || $filter_data != $view_array[$filter_key] ) {
				$change = true;
				$view_array[$filter_key] = $filter_data;
			}
		}
		if ( $change ) {
			$result = update_post_meta($_POST["id"], '_wpv_settings', $view_array);
		}
		$summary = __('Select posts with taxonomy: ', 'wpv-views');
		$result = '';
		$taxonomies = get_taxonomies('', 'objects');
		foreach ($taxonomies as $category_slug => $category) {
			$save_name = ( $category->name == 'category' ) ? 'post_category' : 'tax_input_' . $category->name;
			$relationship_name = ( $category->name == 'category' ) ? 'tax_category_relationship' : 'tax_' . $category->name . '_relationship';

			if (isset($view_array[$relationship_name])) {

				if (!isset($view_array[$save_name])) {
					$view_array[$save_name] = array();
				}

				$name = ( $category->name == 'category' ) ? 'post_category' : 'tax_input[' . $category->name . ']';
				if ($result != '') {
					if ($view_array['taxonomy_relationship'] == 'OR') {
						$result .= __(' OR ', 'wpv-views');
					} else {
						$result .= __(' AND ', 'wpv-views');
					}
				}

				$result .= wpv_get_taxonomy_summary($name, $view_array, $view_array[$save_name]);

			}
		}
		$summary .= $result;
		echo $summary;
		die();
	}

	// TODO This might not be needed here, maybe for summary filter
	add_action('wp_ajax_wpv_filter_taxonomy_sumary_update', 'wpv_filter_taxonomy_sumary_update_callback');

	function wpv_filter_taxonomy_sumary_update_callback() {
		parse_str($_POST['filter_taxonomy'], $view_settings);
		$summary = __('Select posts with taxonomy: ', 'wpv-views');
		$result = '';
		$taxonomies = get_taxonomies('', 'objects');
		foreach ($taxonomies as $category_slug => $category) {
			$save_name = ( $category->name == 'category' ) ? 'post_category' : 'tax_input_' . $category->name;
			$relationship_name = ( $category->name == 'category' ) ? 'tax_category_relationship' : 'tax_' . $category->name . '_relationship';

			if (isset($view_settings[$relationship_name])) {

				if (!isset($view_settings[$save_name])) {
					$view_settings[$save_name] = array();
				}

				$name = ( $category->name == 'category' ) ? 'post_category' : 'tax_input[' . $category->name . ']';
				if ($result != '') {
					if ($view_settings['taxonomy_relationship'] == 'OR') {
						$result .= __(' OR ', 'wpv-views');
					} else {
						$result .= __(' AND ', 'wpv-views');
					}
				}

				$result .= wpv_get_taxonomy_summary($name, $view_settings, $view_settings[$save_name]);

			}
		}

		$summary .= $result;

		echo $summary;
		die();
	}

	add_action('wp_ajax_wpv_filter_taxonomy_delete', 'wpv_filter_taxonomy_delete_callback');

	function wpv_filter_taxonomy_delete_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_taxonomy_delete_nonce') ) die("Security check");
		$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
		$taxonomy = $_POST['taxonomy'];
		$to_delete = array(
			'tax_' . $taxonomy . '_relationship',
			'taxonomy-' . $taxonomy . '-attribute-url',
			'taxonomy-' . $taxonomy . '-attribute-url-format',
			'taxonomy-' . $taxonomy . '-attribute-operator',
		);
		if ('category' == $taxonomy) {
			$to_delete[] = 'post_category';
		} else {
			$to_delete[] = 'tax_input_' . $taxonomy;
		}
		foreach ($to_delete as $index) {
			if ( isset( $view_array[$index] ) ) {
				unset( $view_array[$index] );
			}
		}

		$len = isset( $view_array['filter_controls_field_name'] ) ? count( $view_array['filter_controls_field_name'] ) : 0;
		$splice = false;
		for( $i = 0; $i < $len; $i++ )
		{
			if( strpos( $view_array['filter_controls_field_name'][$i], $taxonomy ) !== false ){
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

function wpv_get_taxonomy_summary($type, $view_settings, $category_selected) {
	// find the matching category/taxonomy
	$taxonomy = 'category';
	$taxonomy_name = __('Categories', 'wpv-views');
	$taxonomies = get_taxonomies('', 'objects');
	foreach ($taxonomies as $category_slug => $category) {
		$name = ( $category->name == 'category' ) ? 'post_category' : 'tax_input[' . $category->name . ']';
		
		if ($name == $type) {
			// it's a category type.
			$taxonomy = $category->name;
			$taxonomy_name = $category->label;
			break;
		}
	}
	
	if (!isset($view_settings['tax_' . $taxonomy . '_relationship'])) {
		$view_settings['tax_' . $taxonomy . '_relationship'] = 'IN';
	}
	if (!isset($view_settings['taxonomy-' . $taxonomy . '-attribute-url'])) {
		$view_settings['taxonomy-' . $taxonomy . '-attribute-url'] = '';
	}
	if (!isset($view_settings['taxonomy-' . $taxonomy . '-attribute-operator'])) {
		$view_settings['taxonomy-' . $taxonomy . '-attribute-operator'] = 'IN';
	}
	
	$relationship = __('is <strong>One</strong> of these', 'wpv-views');
	switch($view_settings['tax_' . $taxonomy . '_relationship']) {
		case "AND":
			$relationship = __('is <strong>All</strong> of these', 'wpv-views');
			break;
		
		case "NOT IN":
			$relationship = __('is <strong>Not one</strong> of these', 'wpv-views');
			break;

		case "FROM PAGE":
			$relationship = __('the same as the <strong>current page</strong>', 'wpv-views');
			break;

		case "FROM ATTRIBUTE":
			$relationship = __('set by the View shortcode attribute ', 'wpv-views');
			break;

		case "FROM URL":
			$relationship = __('set by the URL parameter ', 'wpv-views');
			break;

		case "FROM PARENT VIEW":
			$relationship = ', ' . __('set by the parent view.', 'wpv-views');
			break;
	}
	
	ob_start();
	echo '<span class="wpv-filter-multiple-summary-item">';
	if ($view_settings['tax_' . $taxonomy . '_relationship'] == "FROM PAGE") {
		echo '<strong>' . $taxonomy_name . ' </strong>' . $relationship;
	} else if ($view_settings['tax_' . $taxonomy . '_relationship'] == "FROM ATTRIBUTE" || $view_settings['tax_' . $taxonomy . '_relationship'] == "FROM URL") {
		echo '<strong>' . $taxonomy_name . ' </strong>' . $relationship;
		echo '<strong>"' . $view_settings['taxonomy-' . $taxonomy . '-attribute-url'] . '"</strong> ';
		echo __('using the operator', 'wpv-views') . ' <strong>' . $view_settings['taxonomy-' . $taxonomy . '-attribute-operator'] .  '</strong> ';
		if ($view_settings['tax_' . $taxonomy . '_relationship'] == "FROM ATTRIBUTE") {
			echo '<br /><code>' . sprintf(__('eg. [wpv-view name="view-name" <strong>%s="xxxx"</strong>]', 'wpv-views'), $view_settings['taxonomy-' . $taxonomy . '-attribute-url']) . '</code>';
		} else {
			echo '<br /><code>' . sprintf(__('eg. http://www.example.com/page/?<strong>%s=xxxx</strong>', 'wpv-views'), $view_settings['taxonomy-' . $taxonomy . '-attribute-url']) . '</code>';
		}
	} else if ($view_settings['tax_' . $taxonomy . '_relationship'] == "FROM PARENT VIEW") {
		echo '<strong>' . $taxonomy_name . ' </strong>' . $relationship;
	} else {
		?>
		<strong><?php echo $taxonomy_name . ' </strong>' . $relationship . ' <strong>(';
		$cat_text = '';
		foreach($category_selected as $cat) {
			$term = get_term($cat, $taxonomy);
			if ($term) {
				if ($cat_text != '') {
					$cat_text .= ', ';
				}
				$cat_text .= $term->name;
			}
		}
		echo $cat_text;
		?>)</strong>
		
		<?php
	}
	echo '</span>';
	$buffer = ob_get_clean();
	
	return $buffer;
}


add_filter('wpv-view-get-summary', 'wpv_category_summary_filter', 6, 3);

function wpv_category_summary_filter($summary, $post_id, $view_settings) {
	$result = '';
	$taxonomies = get_taxonomies('', 'objects');
	foreach ($taxonomies as $category_slug => $category) {
		$save_name = ( $category->name == 'category' ) ? 'post_category' : 'tax_input_' . $category->name;
		$relationship_name = ( $category->name == 'category' ) ? 'tax_category_relationship' : 'tax_' . $category->name . '_relationship';
		
		if (isset($view_settings[$relationship_name])) {
			
			if (!isset($view_settings[$save_name])) {
				$view_settings[$save_name] = array();
			}
	
			$name = ( $category->name == 'category' ) ? 'post_category' : 'tax_input[' . $category->name . ']';
			if ($result != '') {
				if ($view_settings['taxonomy_relationship'] == 'OR') {
					$result .= __(' OR ', 'wpv-views');
				} else {
					$result .= __(' AND ', 'wpv-views');
				}
			}
			
			$result .= wpv_get_taxonomy_summary($name, $view_settings, $view_settings[$save_name]);
				
		}
	}

	if ($result != '' && $summary != '') {
		$summary .= '<br />';
	}
	$summary .= $result;
	
	return $summary;
}


function wpv_taxonomy_get_url_params($view_settings) {
	$results = array();
	
	$taxonomies = get_taxonomies('', 'objects');
	foreach ($taxonomies as $category_slug => $category) {
		$save_name = ( $category->name == 'category' ) ? 'post_category' : 'tax_input_' . $category->name;
		$relationship_name = ( $category->name == 'category' ) ? 'tax_category_relationship' : 'tax_' . $category->name . '_relationship';
		
		if (isset($view_settings[$relationship_name]) && $view_settings[$relationship_name] == 'FROM URL') {

			$url_parameter = $view_settings['taxonomy-' . $category->name . '-attribute-url'];
			
			$results[] = array('name' => $category->name, 'param' => $url_parameter, 'mode' => 'tax', 'cat' => $category);

		}
	}
	
	return $results;
}
