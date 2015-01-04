<?php

if(is_admin()){
	
	/**
	* Add the search filter to the list and to the popup select
	*/
	
	add_action('wpv_add_filter_list_item', 'wpv_add_filter_search_list_item', 1, 1);
	add_filter('wpv_filters_add_filter', 'wpv_filters_add_filter_search', 1, 1);

	function wpv_filters_add_filter_search($filters) {
		$filters['post_search'] = array('name' => __('Post search', 'wpv-views'),
						'present' => 'search_mode',
						'callback' => 'wpv_add_new_filter_search_list_item'
						);

		return $filters;
	}
	
	/**
	* Create search filter callback
	*/

	function wpv_add_new_filter_search_list_item() {
		$args = array(
			'search_mode' => array('specific')
		);
		wpv_add_filter_search_list_item($args);
	}
	
	/**
	* Render search filter item in the filters list
	*/

	function wpv_add_filter_search_list_item($view_settings) {
		if (isset($view_settings['search_mode'])) {
			$li = wpv_get_list_item_ui_post_search('', $view_settings);
			echo '<li id="js-row-post_search" class="js-filter-row js-filter-row-simple js-filter-for-posts js-filter-search js-filter-row-post_search">' . $li . '</li>';
		}
	}
	
	/**
	* Render search filter item content in the filters list
	*/

	function wpv_get_list_item_ui_post_search( $selected, $view_settings = array() ) {

		if ( isset( $view_settings['search_mode'] ) && is_array( $view_settings['search_mode'] ) ) {
			$view_settings['search_mode'] = $view_settings['search_mode'][0];
		}
		if ( !isset( $view_settings['post_search_value'] ) ) {
			$view_settings['post_search_value'] = '';
		}
		ob_start();
		?>
		<p class='wpv-filter-search-summary js-wpv-filter-summary js-wpv-filter-search-summary'>
			<?php echo wpv_get_filter_search_summary_txt( $view_settings ); ?>
		</p>
		<p class='edit-filter js-wpv-filter-edit-controls'>
			<i class='button-secondary icon-edit icon-large js-wpv-filter-edit-open js-wpv-filter-search-edit-open' title='<?php echo esc_attr( __('Edit this filter','wpv-views') ); ?>'></i>
			<i class='button-secondary icon-trash icon-large js-filter-remove' title='<?php echo esc_attr( __('Delete this filter', 'wpv-views') ); ?>' data-nonce='<?php echo wp_create_nonce( 'wpv_view_filter_search_delete_nonce' ); ?>'></i>
		</p>
		<div id="wpv-filter-search-edit" class="wpv-filter-edit js-wpv-filter-edit">
			<fieldset>
				<p><strong><?php echo __('Post search', 'wpv-views'); ?>:</strong></p>
				<div id="wpv-filter-search" class="js-filter-search-list">
					<?php wpv_render_search_options( array( 'mode' => 'edit', 'view_settings' => $view_settings ) ); ?>
				</div>
			</fieldset>
			<p>
				<input class="button-secondary js-wpv-filter-edit-ok js-wpv-filter-search-edit-ok" type="button" value="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-save="<?php echo htmlentities( __('Save', 'wpv-views'), ENT_QUOTES ); ?>" data-close="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-success="<?php echo htmlentities( __('Updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_search_nonce' ); ?>" />
			</p>
			<p class="wpv-custom-fields-help">
				<?php echo sprintf(__('%sLearn about filtering for a specific text string%s', 'wpv-views'),
					'<a class="wpv-help-link" href="' . WPV_FILTER_BY_SPECIFIC_TEXT_LINK . '" target="_blank">',
					' &raquo;</a>'
				); ?>
			</p>
		</div>
		<?php
		$res = ob_get_clean();
		return $res;
		/*
		ob_start();
		wpv_render_search_options(array('mode' => 'edit',
				'view_settings' => $view_settings));
		$data = ob_get_clean();

		$td = "<p class='wpv-filter-search-summary js-wpv-filter-summary js-wpv-filter-search-summary'>\n";
		$td .= wpv_get_filter_search_summary_txt($view_settings);
		$td .= "</p>\n<p class='edit-filter js-wpv-filter-edit-controls'>\n<i class='button-secondary icon-edit icon-large js-wpv-filter-edit-open js-wpv-filter-search-edit-open' title='". __('Edit this filter','wpv-views') ."'></i>\n<i class='button-secondary icon-trash icon-large js-filter-remove' title='" . esc_attr( __('Delete this filter', 'wpv-views') ) . "' data-nonce='". wp_create_nonce( 'wpv_view_filter_search_delete_nonce' ) . "'></i>\n</p>";
		$td .= "<div id=\"wpv-filter-search-edit\" class=\"wpv-filter-edit js-wpv-filter-edit\">\n";
		$td .= '<fieldset>';
		$td .= '<p><strong>' . __('Post search', 'wpv-views') . ':</strong></p>';
		$td .= '<div id="wpv-filter-search" class="js-filter-search-list">' . $data . '</div>';
		$td .= '</fieldset>';
		ob_start();
		?>
		<p>
			<input class="button-secondary js-wpv-filter-edit-ok js-wpv-filter-search-edit-ok" type="button" value="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-save="<?php echo htmlentities( __('Save', 'wpv-views'), ENT_QUOTES ); ?>" data-close="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-success="<?php echo htmlentities( __('Updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_search_nonce' ); ?>" />
		</p>
		<p class="wpv-custom-fields-help">
                        <?php echo sprintf(__('%sLearn about filtering for a specific text string%s', 'wpv-views'),
                                        '<a class="wpv-help-link" href="' . WPV_FILTER_BY_SPECIFIC_TEXT_LINK . '" target="_blank">',
                                        ' &raquo;</a>'
                                        ); ?>
        </p>
		<?php
		$td .= ob_get_clean();
		$td .= '</div>';

		return $td;
		*/
	}

	/**
	* Update search filter callback
	*/

	add_action('wp_ajax_wpv_filter_search_update', 'wpv_filter_search_update_callback');

	function wpv_filter_search_update_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_search_nonce') ) die("Security check");
		if ( empty( $_POST['filter_search'] ) ) {
			echo $_POST['id'];
			die();
		}
		parse_str($_POST['filter_search'], $filter_search);
		$change = false;
		$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
		if (!isset($filter_search['post_search_value'])) $filter_search['post_search_value'] = '';
		if ( !isset( $view_array['search_mode'] ) || $filter_search['search_mode'] != $view_array['search_mode'] ) {
			$change = true;
			$view_array['search_mode'] = $filter_search['search_mode'];
		}
		if ( !isset( $view_array['post_search_value'] ) || sanitize_text_field($filter_search['post_search_value']) != $view_array['post_search_value'] ) {
			$change = true;
			$view_array['post_search_value'] = sanitize_text_field($filter_search['post_search_value']);
		}
		if ( !isset( $view_array['post_search_content'] ) || $filter_search['post_search_content'] != $view_array['post_search_content'] ) {
			$change = true;
			$view_array['post_search_content'] = $filter_search['post_search_content'];
		}
		if ( $change ) {
			$result = update_post_meta($_POST["id"], '_wpv_settings', $view_array);
		}
		$filter_search['search_mode'] = $filter_search['search_mode'][0];
		echo wpv_get_filter_search_summary_txt($filter_search);
		die();
	}
	
	/**
	* Update search filter summary callback
	*/

	// TODO This might not be needed here, maybe for summary filter
	add_action('wp_ajax_wpv_filter_search_sumary_update', 'wpv_filter_search_sumary_update_callback');

	function wpv_filter_search_sumary_update_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_search_nonce') ) die("Security check");
		parse_str($_POST['filter_search'], $filter_search);
		$filter_search['search_mode'] = $filter_search['search_mode'][0];
		if (!isset($filter_search['post_search_value'])) $filter_search['post_search_value'] = '';
		echo wpv_get_filter_search_summary_txt($filter_search);
		die();
	}
	
	/**
	* Delete search filter callback
	*/

	add_action('wp_ajax_wpv_filter_post_search_delete', 'wpv_filter_search_delete_callback');

	function wpv_filter_search_delete_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_search_delete_nonce') ) die("Security check");
		$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
		if ( isset( $view_array['search_mode'] ) ) {
			unset( $view_array['search_mode'] );
		}
		if ( isset( $view_array['post_search_value'] ) ) {
			unset( $view_array['post_search_value'] );
		}
		if ( isset( $view_array['post_search_content'] ) ) {
			unset( $view_array['post_search_content'] );
		}
		update_post_meta($_POST["id"], '_wpv_settings', $view_array);
		echo $_POST['id'];
		die();

	}

	/**
	* Add the taxonomy search filter to the list and to the popup select
	*/
	
	add_action('wpv_add_taxonomy_filter_list_item', 'wpv_add_filter_taxonomy_search_list_item', 1, 1);
	add_filter('wpv_taxonomy_filters_add_filter', 'wpv_filters_add_filter_search_taxonomy', 1, 1);

	function wpv_filters_add_filter_search_taxonomy($filters) {
		$filters['taxonomy_search'] = array('name' => __('Taxonomy search', 'wpv-views'),
						'present' => 'taxonomy_search_mode',
						'callback' => 'wpv_add_new_filter_taxonomy_search_list_item'
						);

		return $filters;
	}
	
	/**
	* Create taxonomy search filter callback
	*/

	function wpv_add_new_filter_taxonomy_search_list_item() {
		$args = array(
			'taxonomy_search_mode' => array('specific')
		);
		wpv_add_filter_taxonomy_search_list_item($args);
	}
	
	/**
	* Render taxonomy search filter item in the filters list
	*/

	function wpv_add_filter_taxonomy_search_list_item($view_settings) {
		if (isset($view_settings['taxonomy_search_mode'])) {
			$td = wpv_get_list_item_ui_taxonomy_search('', $view_settings);
			echo '<li id="js-row-taxonomy_search" class="js-filter-row js-filter-row-simple js-filter-for-taxonomies js-filter-taxonomy-search js-filter-row-taxonomy_search">' . $td . '</li>';
		}
	}
	
	/**
	* Render taxonomy search filter item content in the filters list
	*/

	function wpv_get_list_item_ui_taxonomy_search( $selected, $view_settings = array() ) {

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
		<p class='edit-filter js-wpv-filter-edit-controls'>
			<i class='button-secondary icon-edit icon-large js-wpv-filter-edit-open js-wpv-filter-taxonomy-search-edit-open' title='<?php echo esc_attr( __('Edit this filter','wpv-views') );?>'></i>
			<i class='button-secondary icon-trash icon-large js-filter-remove' title='<?php echo esc_attr( __('Delete this filter','wpv-views') ); ?>' data-nonce='<?php echo wp_create_nonce( 'wpv_view_filter_taxonomy_search_delete_nonce' ); ?>'></i>
		</p>
		<div id="wpv-filter-taxonomy-search-edit" class="wpv-filter-edit js-wpv-filter-edit">
			<fieldset>
				<legend><strong><?php echo __('Taxonomy search', 'wpv-views'); ?>:</strong></legend>
				<div id="wpv-filter-taxonomy-search" class="js-filter-taxonomy-search-list">
					<?php wpv_render_taxonomy_search_options( array( 'mode' => 'edit', 'view_settings' => $view_settings ) ); ?>
				</div>
			</fieldset>
			<p>
				<input class="button-secondary js-wpv-filter-edit-ok js-wpv-filter-taxonomy-search-edit-ok" type="button" value="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-save="<?php echo htmlentities( __('Save', 'wpv-views'), ENT_QUOTES ); ?>" data-close="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-success="<?php echo htmlentities( __('Updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_taxonomy_search_nonce' ); ?>" />
			</p>
		</div>
		<?php
		$res = ob_get_clean();
		return $res;
		/*
		ob_start();
		wpv_render_taxonomy_search_options(array('mode' => 'edit',
				'view_settings' => $view_settings));
		$data = ob_get_clean();

		$td = "<p class='wpv-filter-taxonomy-search-summary js-wpv-filter-summary js-wpv-filter-taxonomy-search-summary'>\n";
		$td .= wpv_get_filter_taxonomy_search_summary_txt($view_settings);
		$td .= "</p>\n<p class='edit-filter js-wpv-filter-edit-controls'>\n<i class='button-secondary icon-edit icon-large js-wpv-filter-edit-open js-wpv-filter-taxonomy-search-edit-open' title='". __('Edit this filter','wpv-views') ."'></i>\n<i class='button-secondary icon-trash icon-large js-filter-remove' title='". __('Delete this filter','wpv-views') ."' data-nonce='". wp_create_nonce( 'wpv_view_filter_taxonomy_search_delete_nonce' ) . "'></i>\n</p>";
		$td .= "<div id=\"wpv-filter-taxonomy-search-edit\" class=\"wpv-filter-edit js-wpv-filter-edit\">\n";
		$td .= '<fieldset>';
		$td .= '<legend><strong>' . __('Taxonomy search', 'wpv-views') . ':</strong></legend>';
		$td .= '<div id="wpv-filter-taxonomy-search" class="js-filter-taxonomy-search-list">' . $data . '</div>';
		$td .= '</fieldset>';
		ob_start();
		?>
		<p>
			<input class="button-secondary js-wpv-filter-edit-ok js-wpv-filter-taxonomy-search-edit-ok" type="button" value="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-save="<?php echo htmlentities( __('Save', 'wpv-views'), ENT_QUOTES ); ?>" data-close="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-success="<?php echo htmlentities( __('Updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_taxonomy_search_nonce' ); ?>" />
		</p>
		<?php
		$td .= ob_get_clean();
		$td .= '</div>';

		return $td;
		*/
	}

	/**
	* Update taxonomy search filter callback
	*/

	add_action('wp_ajax_wpv_filter_taxonomy_search_update', 'wpv_filter_taxonomy_search_update_callback');

	function wpv_filter_taxonomy_search_update_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_taxonomy_search_nonce') ) die("Security check");
		if ( empty( $_POST['tax_filter_search'] ) ) {
			echo $_POST['id'];
			die();
		}
		parse_str($_POST['tax_filter_search'], $filter_search);
		$change = false;
		$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
		if (!isset($filter_search['taxonomy_search_value'])) $filter_search['taxonomy_search_value'] = '';
		if ( !isset( $view_array['taxonomy_search_mode'] ) || $filter_search['taxonomy_search_mode'] != $view_array['taxonomy_search_mode'] ) {
			$change = true;
			$view_array['taxonomy_search_mode'] = $filter_search['taxonomy_search_mode'];
		}
		if ( !isset( $view_array['taxonomy_search_value'] ) || sanitize_text_field($filter_search['taxonomy_search_value']) != $view_array['taxonomy_search_value'] ) {
			$change = true;
			$view_array['taxonomy_search_value'] = sanitize_text_field($filter_search['taxonomy_search_value']);
		}
		if ( $change ) {
			$result = update_post_meta($_POST["id"], '_wpv_settings', $view_array);
		}
		$filter_search['taxonomy_search_mode'] = $filter_search['taxonomy_search_mode'][0];
		echo wpv_get_filter_taxonomy_search_summary_txt($filter_search);
		die();
	}
	
	/**
	* Update taxonomy search filter summary callback
	*/

	// TODO This might not be needed here, maybe for summary filter
	add_action('wp_ajax_wpv_filter_taxonomy_search_sumary_update', 'wpv_filter_taxonomy_search_sumary_update_callback');

	function wpv_filter_taxonomy_search_sumary_update_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_taxonomy_search_nonce') ) die("Security check");
		parse_str($_POST['tax_filter_search'], $filter_search);
		$filter_search['taxonomy_search_mode'] = $filter_search['taxonomy_search_mode'][0];
		if (!isset($filter_search['taxonomy_search_value'])) $filter_search['taxonomy_search_value'] = '';
		echo wpv_get_filter_taxonomy_search_summary_txt($filter_search);
		die();
	}
	
	/**
	* Delete taxonomy search filter callback
	*/

	add_action('wp_ajax_wpv_filter_taxonomy_search_delete', 'wpv_filter_taxonomy_search_delete_callback');

	function wpv_filter_taxonomy_search_delete_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_taxonomy_search_delete_nonce') ) die("Security check");
		$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
		if ( isset( $view_array['taxonomy_search_mode'] ) ) {
			unset( $view_array['taxonomy_search_mode'] );
		}
		if ( isset( $view_array['taxonomy_search_value'] ) ) {
			unset( $view_array['taxonomy_search_value'] );
		}
		update_post_meta($_POST["id"], '_wpv_settings', $view_array);
		echo $_POST['id'];
		die();

	}
	
	/**
	* Add a filter to show the filter on the summary
	* NOTE we may need something like that for taxonomy search
	*/
    
	add_filter('wpv-view-get-summary', 'wpv_search_summary_filter', 5, 3);

	function wpv_search_summary_filter($summary, $post_id, $view_settings) {
		if(isset($view_settings['query_type']) && $view_settings['query_type'][0] == 'posts' && isset($view_settings['search_mode'])) {
			
			$view_settings['search_mode'] = $view_settings['search_mode'][0];

			$result = wpv_get_filter_search_summary_txt($view_settings, true);
			if ($result != '' && $summary != '') {
				$summary .= '<br />';
			}
			$summary .= $result;
		}
		
		return $summary;
	}
	
}

/**
* Render search filter options
*/

function wpv_render_search_options($args) {

    $edit = isset($args['mode']) && $args['mode'] == 'edit';
    $view_settings = isset($args['view_settings']) ? $args['view_settings'] : array();

    $defaults = array('search_mode' => 'specific',
                      'post_search_value' => '',
                      'post_search_content' => 'full_content');
    $view_settings = wp_parse_args($view_settings, $defaults);


	?>

        <ul>
            <li>
                <?php $checked = $view_settings['search_mode'] == 'specific' ? 'checked="checked"' : ''; ?>
                <label><input type="radio" name="search_mode[]" value="specific" <?php echo $checked; ?> />&nbsp;<?php _e('Search for a specific text:', 'wpv-views'); ?></label>
                <?php if ($edit): ?>
                    <input type="hidden" name="filter_by_search" value="1"/>
                <?php endif; ?>
                <input type='text' name="post_search_value" value="<?php echo $view_settings['post_search_value']; ?>" />
            </li>
            <li>
                <?php $checked = ( $view_settings['search_mode'] == 'manual' || $view_settings['search_mode'] == 'visitor' ) ? 'checked="checked"' : ''; ?>
                <label><input type="radio" name="search_mode[]" value="manual" <?php echo $checked; ?> />&nbsp;<?php _e('I’ll add the search box to the HTML manually', 'wpv-views'); ?></label>
            </li>
        </ul>

        <div class="search-content">
	        <label><?php echo __('Where to search: ', 'wpv-views'); ?></label>
	        <select name="post_search_content">
			<option value="full_content"<?php if ($view_settings['post_search_content'] == 'full_content') echo ' selected="selected"';?>><?php echo __('Post content and title', 'wpv-views'); ?></option>
			<option value="just_title"<?php if ($view_settings['post_search_content'] == 'just_title') echo ' selected="selected"';?>><?php echo __('Just post titles', 'wpv-views'); ?></option>
	        </select>
        </div>

	<?php
}

/**
* Render search filter summary text
*/

function wpv_get_filter_search_summary_txt($view_settings, $short=false) {

	ob_start();

	switch ($view_settings['search_mode']) {
		case 'specific':
			$term = $view_settings['post_search_value'];
			if ($term == '') {
				$term = '<i>' . __('None set', 'wpv-views') . '</i>';
			}
			if($short) {
				echo sprintf(__('Filter by <strong>search</strong> term: <strong>%s</strong>', 'wpv-views'), $term);
			} else {
				echo sprintf(__('Filter by this search term: <strong>%s</strong>.', 'wpv-views'), $term);
			}
			break;

		case 'visitor':
			if ($short) {
				echo __('Show a <strong>search box</strong> for visitors', 'wpv-views');
			} else {
				echo __('Show a <strong>search box</strong> for visitors.', 'wpv-views');
			}
			break;

		case 'manual':
			if ($short) {
				echo __('Filter by <strong>search box</strong>', 'wpv-views');
			} else {
				echo __('The search box will be added <strong>manually</strong>.<br /><code>eg. [wpv-filter-search-box]</code>', 'wpv-views');
			}
			break;
	}
	$data = ob_get_clean();

	return $data;

}

/**
* Render taxonomy search filter options
*/

function wpv_render_taxonomy_search_options($args) {

    $edit = isset($args['mode']) && $args['mode'] == 'edit';
    $view_settings = isset($args['view_settings']) ? $args['view_settings'] : array();

    $defaults = array('taxonomy_search_mode' => 'specific',
                      'taxonomy_search_value' => '');
    $view_settings = wp_parse_args($view_settings, $defaults);


	?>

        <ul>
            <li>
                <?php $checked = $view_settings['taxonomy_search_mode'] == 'specific' ? 'checked="checked"' : ''; ?>
                <label><input type="radio" name="taxonomy_search_mode[]" value="specific" <?php echo $checked; ?> />&nbsp;<?php _e('Search for a specific text:', 'wpv-views'); ?></label>
                <input type='text' name="taxonomy_search_value" value="<?php echo $view_settings['taxonomy_search_value']; ?>" />
            </li>
            <li>
                <?php $checked = ( $view_settings['taxonomy_search_mode'] == 'manual' || $view_settings['taxonomy_search_mode'] == 'visitor' ) ? 'checked="checked"' : ''; ?>
                <label><input type="radio" name="taxonomy_search_mode[]" value="manual" <?php echo $checked; ?> />&nbsp;<?php _e('I’ll add the search box to the HTML manually', 'wpv-views'); ?></label>
            </li>
        </ul>

	<?php
}

/**
* Render taxonomy search filter summary text
*/

function wpv_get_filter_taxonomy_search_summary_txt($view_settings) {
	ob_start();

	switch ($view_settings['taxonomy_search_mode']) {
	case 'specific':
		$term = $view_settings['taxonomy_search_value'];
		if ($term == '') {
		$term = '<i>' . __('None set', 'wpv-views') . '</i>';
		}
		echo sprintf(__('Filter by this search term: <strong>%s</strong>.', 'wpv-views'), $term);
		break;

	case 'visitor':
		echo __('Show a <strong>search box</strong> for visitors.', 'wpv-views');
		break;

	case 'manual':
		echo __('The search box will be added <strong>manually</strong>.<br /><code>eg. [wpv-filter-search-box]</code>', 'wpv-views');
		break;
	}

	$data = ob_get_clean();

	return $data;
}

/**
*
* NOTE this seems to be used only in the old Filter Controls table initialization, should be handledby Riccardo's code y now
*/

function wpv_search_get_url_params($view_settings) {
	if (isset($view_settings['search_mode'][0]) && $view_settings['search_mode'][0] == 'visitor') {
		return array(array('name' => __('Search' , 'wpv-views'), 'param' => 'wpv_post_search', 'mode' => 'search'));
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