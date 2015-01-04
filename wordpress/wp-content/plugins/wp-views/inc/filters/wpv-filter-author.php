<?php

if(is_admin()){

	/**
	* Add the author filter to the list and to the popup select
	*/

	add_action('wpv_add_filter_list_item', 'wpv_add_filter_author_list_item', 1, 1);
	add_filter('wpv_filters_add_filter', 'wpv_filters_add_filter_author', 1,1);

	function wpv_filters_add_filter_author($filters) {
		$filters['post_author'] = array('name' => __('Post author', 'wpv-views'),
						'present' => 'author_mode',
						'callback' => 'wpv_add_new_filter_author_list_item'
						);

		return $filters;
	}
	
	/**
	* Create author filter callback
	*/

	function wpv_add_new_filter_author_list_item() {
		$args = array(
			'author_mode' => array('current_user')
		);
		wpv_add_filter_author_list_item($args);
	}
	
	/**
	* Render author filter item in the filters list
	*/

	function wpv_add_filter_author_list_item($view_settings) {
		if (isset($view_settings['author_mode'][0])) {
			$li = wpv_get_list_item_ui_post_author(null, $view_settings);
			echo '<li id="js-row-post_author" class="js-filter-row js-filter-row-simple js-filter-for-posts js-filter-author js-filter-row-post_author">' . $li . '</li>';
		}
	}
	
	/**
	* Render author filter item content in the filters list
	*/

	function wpv_get_list_item_ui_post_author( $selected, $view_settings = null ) {

		if ( isset( $view_settings['author_mode'] ) && is_array( $view_settings['author_mode'] ) ) {
			$view_settings['author_mode'] = $view_settings['author_mode'][0];
		}
		ob_start();
		?>
		<p class='wpv-filter-author-edit-summary js-wpv-filter-summary js-wpv-filter-author-summary'>
			<?php echo wpv_get_filter_author_summary_txt( $view_settings ); ?>
		</p>
		<p class='edit-filter js-wpv-filter-edit-controls'>
			<i class='button-secondary icon-edit icon-large js-wpv-filter-edit-open js-wpv-filter-author-edit-open' title='<?php echo esc_attr( __('Edit this filter','wpv-views') ); ?>'></i>
			<i class='button-secondary icon-trash icon-large js-filter-remove' title='<?php echo esc_attr( __('Delete this filter','wpv-views') ); ?>' data-nonce='<?php echo wp_create_nonce( 'wpv_view_filter_author_delete_nonce' ); ?>'></i>
		</p>
		<div id="wpv-filter-author-edit" class="wpv-filter-edit js-wpv-filter-edit">
			<fieldset>
				<p><strong><?php echo __('Post Author', 'wpv-views'); ?>:</strong></p>
				<div id="wpv-filter-author" class="js-filter-author-list">
					<?php wpv_render_author_options( array( 'mode' => 'edit', 'view_settings' => $view_settings ) ); ?>
				</div>
			</fieldset>
			<p>
				<input class="button-secondary js-wpv-filter-edit-ok js-wpv-filter-author-edit-ok" type="button" value="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-save="<?php echo htmlentities( __('Save', 'wpv-views'), ENT_QUOTES ); ?>" data-close="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-success="<?php echo htmlentities( __('Updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_author_nonce' ); ?>" />
			</p>
			<p class="wpv-custom-fields-help">
				<?php echo sprintf(__('%sLearn about filtering by Post Author%s', 'wpv-views'),
					'<a class="wpv-help-link" href="' . WPV_FILTER_BY_AUTHOR_LINK . '" target="_blank">',
					' &raquo;</a>'
				); ?>
			</p>
		</div>
		<?php
		$res = ob_get_clean();
		return $res;
		
		
		/*
		ob_start();
		wpv_render_author_options(array('mode' => 'edit', 'view_settings' => $view_settings));
		$data = ob_get_clean();
		$td = "<p class='wpv-filter-author-edit-summary js-wpv-filter-summary js-wpv-filter-author-summary'>\n";
		$td .= wpv_get_filter_author_summary_txt($view_settings);
		$td .= "</p>\n<p class='edit-filter js-wpv-filter-edit-controls'>\n<i class='button-secondary icon-edit icon-large js-wpv-filter-edit-open js-wpv-filter-author-edit-open' title='". __('Edit this filter','wpv-views') ."'></i>\n<i class='button-secondary icon-trash icon-large js-filter-remove' title='". __('Delete this filter','wpv-views') ."' data-nonce='". wp_create_nonce( 'wpv_view_filter_author_delete_nonce' ) . "'></i>\n</p>";
		$td .= "<div id=\"wpv-filter-author-edit\" class=\"wpv-filter-edit js-wpv-filter-edit\">\n";
		$td .= '<fieldset>';
		$td .= '<p><strong>' . __('Post Author', 'wpv-views') . ':</strong></p>';
		$td .= '<div id="wpv-filter-author" class="js-filter-author-list">' . $data . '</div>';
		$td .= '</fieldset>';
		ob_start();
		?>
		<p>
			<input class="button-secondary js-wpv-filter-edit-ok js-wpv-filter-author-edit-ok" type="button" value="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-save="<?php echo htmlentities( __('Save', 'wpv-views'), ENT_QUOTES ); ?>" data-close="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-success="<?php echo htmlentities( __('Updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_author_nonce' ); ?>" />
		</p>
		<p class="wpv-custom-fields-help">
                        <?php echo sprintf(__('%sLearn about filtering by Post Author%s', 'wpv-views'),
                                        '<a class="wpv-help-link" href="' . WPV_FILTER_BY_AUTHOR_LINK . '" target="_blank">',
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
	* Update author filter callback
	*/

	add_action('wp_ajax_wpv_filter_author_update', 'wpv_filter_author_update_callback');

	function wpv_filter_author_update_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_author_nonce') ) die("Security check");
		if ( empty( $_POST['filter_author'] ) ) {
			echo $_POST['id'];
			die();
		}
		parse_str($_POST['filter_author'], $filter_author);
		$change = false;
		$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
		if ( !isset( $filter_author['author_name'] ) || '' == $filter_author['author_name'] ) {
			$filter_author['author_name'] = '';
			$filter_author['author_id'] = 0;
		}
		if ( !isset( $view_array['author_mode'] ) || $filter_author['author_mode'] != $view_array['author_mode'] ) {
			$change = true;
			$view_array['author_mode'] = $filter_author['author_mode'];
		}
		if ( !isset( $view_array['author_name'] ) || $filter_author['author_name'] != $view_array['author_name'] ) {
			$change = true;
			$view_array['author_name'] = $filter_author['author_name'];
		}
		if ( !isset( $view_array['author_id'] ) || $filter_author['author_id'] != $view_array['author_id'] ) {
			$change = true;
			$view_array['author_id'] = $filter_author['author_id'];
		}
		if ( !isset( $view_array['author_url_type'] ) || $filter_author['author_url_type'] != $view_array['author_url_type'] ) {
			$change = true;
			$view_array['author_url_type'] = $filter_author['author_url_type'];
		}
		if ( !isset( $view_array['author_url'] ) || sanitize_text_field($filter_author['author_url']) != $view_array['author_url'] ) {
			$change = true;
			$view_array['author_url'] = sanitize_text_field($filter_author['author_url']);
		}
		if ( !isset( $view_array['author_shortcode_type'] ) || $filter_author['author_shortcode_type'] != $view_array['author_shortcode_type'] ) {
			$change = true;
			$view_array['author_shortcode_type'] = $filter_author['author_shortcode_type'];
		}
		if ( !isset( $view_array['author_shortcode'] ) || sanitize_text_field($filter_author['author_shortcode']) != $view_array['author_shortcode'] ) {
			$change = true;
			$view_array['author_shortcode'] = sanitize_text_field($filter_author['author_shortcode']);
		}
		if ( $change ) {
			$result = update_post_meta($_POST["id"], '_wpv_settings', $view_array);
		}
		$filter_author['author_mode'] = $filter_author['author_mode'][0];
		echo wpv_get_filter_author_summary_txt($filter_author);
		die();
	}
	
	/**
	* Update author filter summary callback
	*/

	// TODO This might not be needed here, maybe for summary filter
	add_action('wp_ajax_wpv_filter_author_sumary_update', 'wpv_filter_author_sumary_update_callback');

	function wpv_filter_author_sumary_update_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_author_nonce') ) die("Security check");
		parse_str($_POST['filter_author'], $filter_author);
		$filter_author['author_mode'] = $filter_author['author_mode'][0];
		echo wpv_get_filter_author_summary_txt($filter_author);
		die();
	}
	
	/**
	* Delete author filter callback
	*/

	add_action('wp_ajax_wpv_filter_post_author_delete', 'wpv_filter_author_delete_callback');

	function wpv_filter_author_delete_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_author_delete_nonce') ) die("Security check");
		$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
		if ( isset( $view_array['author_mode'] ) ) {
			unset( $view_array['author_mode'] );
		}
		if ( isset( $view_array['author_name'] ) ) {
			unset( $view_array['author_name'] );
		}
		if ( isset( $view_array['author_id'] ) ) {
			unset( $view_array['author_id'] );
		}
		if ( isset( $view_array['author_url_type'] ) ) {
			unset( $view_array['author_url_type'] );
		}
		if ( isset( $view_array['author_url'] ) ) {
			unset( $view_array['author_url'] );
		}
		if ( isset( $view_array['author_shortcode_type'] ) ) {
			unset( $view_array['author_shortcode_type'] );
		}
		if ( isset( $view_array['author_shortcode'] ) ) {
			unset( $view_array['author_shortcode'] );
		}
		update_post_meta($_POST["id"], '_wpv_settings', $view_array);
		echo $_POST['id'];
		die();

	}

	/**
	* Add a filter to show the filter on the summary
	*/

	add_filter('wpv-view-get-summary', 'wpv_author_summary_filter', 5, 3);

	function wpv_author_summary_filter($summary, $post_id, $view_settings) {
		if(isset($view_settings['query_type']) && $view_settings['query_type'][0] == 'posts' && isset($view_settings['author_mode'])) {
			$view_settings['author_mode'] = $view_settings['author_mode'][0];

			$result = wpv_get_filter_author_summary_txt($view_settings, true);
			if ($result != '' && $summary != '') {
				$summary .= '<br />';
			}
			$summary .= $result;
		}
		return $summary;
	}
	
	/**
	* Add author suggest functionality
	*/

	add_action('wp_ajax_wpv_suggest_author', 'wpv_suggest_author');
	add_action('wp_ajax_nopriv_wpv_suggest_author', 'wpv_suggest_author');

	function wpv_suggest_author() {
		global $wpdb;
		$user = esc_sql(like_escape($_REQUEST['q']));
		$sql="SELECT DISTINCT ID, display_name FROM {$wpdb->users} INNER JOIN {$wpdb->usermeta} WHERE display_name LIKE '%$user%' ORDER BY display_name LIMIT 0, 20";
			$results=$wpdb->get_results($sql);
		foreach ($results as $row) {
			echo $row->display_name . ' # userID: ' . $row->ID . "\n";
		}
		die();
	}

}

/**
* Render author filter options
*/

function wpv_render_author_options($args) {
	global $wpdb;

	$edit = isset($args['mode']) && $args['mode'] == 'edit';

	$view_settings = isset($args['view_settings']) ? $args['view_settings'] : array();

	$defaults = array('author_mode' => 'current_user',
				'author_name' =>'',
				'author_id' => 0,
				'author_url' => 'author-filter',
				'author_url_type' => '',
				'author_shortcode' => 'author',
				'author_shortcode_type' => '');
	$view_settings = wp_parse_args($view_settings, $defaults);
		?>
		<ul>
		<?php $radio_name = $edit ? '_wpv_settings[author_mode][]' : 'author_mode[]' ?>
		<li>
			<?php $checked = $view_settings['author_mode'] == 'current_user' ? 'checked="checked"' : ''; ?>
			<label><input type="radio" name="author_mode[]" value="current_user" <?php echo $checked; ?> />&nbsp;<?php _e('Post author is the same as the logged in user', 'wpv-views'); ?></label>
			<?php if ($edit): // only one instance of this filter by view ?>
			<input type="hidden" name="filter_by_author" value="1"/>
			<?php endif; ?>
		</li>
		<li>
			<?php $checked = $view_settings['author_mode'] == 'this_user' ? 'checked="checked"' : ''; ?>
			<label><input type="radio" name="author_mode[]" value="this_user" <?php echo $checked; ?> />&nbsp;<?php _e('Post author is ', 'wpv-views'); ?></label>

			<?php $author_display_name = $view_settings['author_name'];
			if ( 0 != $view_settings['author_id'] && '' == $author_display_name) {
			$user_info = get_userdata($view_settings['author_id']);
			$author_display_name = $user_info->display_name;
			} ?>

			<input id="wpv_author_name" class="author_suggest js-author-suggest" type='text' name="author_name" value="<?php echo htmlentities( $author_display_name, ENT_QUOTES ); ?>" size="15" />
			<input id="wpv_author" class="author_suggest_id js-author-suggest-id" type='hidden' name="author_id" value="<?php echo $view_settings['author_id']; ?>" size="10" />

			<img id="wpv_update_author" src="<?php echo WPV_URL; ?>/res/img/ajax-loader.gif" width="16" height="16" style="display:none" alt="<?php echo htmlentities( __('Loading', 'wpv-views'), ENT_QUOTES ); ?>" />

		</li>
		<li>
			<?php $checked = $view_settings['author_mode'] == 'parent_view' ? 'checked="checked"' : ''; ?>
			<label><input type="radio" name="author_mode[]" value="parent_view" <?php echo $checked; ?> />&nbsp;<?php _e('Post author is set by the parent View', 'wpv-views'); ?></label>
		</li>
		<li>
			<?php $checked = $view_settings['author_mode'] == 'by_url' ? 'checked="checked"' : ''; ?>
			<label><input type="radio" name="author_mode[]" value="by_url" <?php echo $checked; ?> />&nbsp;<?php _e('Post author\'s ', 'wpv-views'); ?></label>
			<select id="wpv_author_url_type" name="author_url_type">
				<?php
				$selected_type = $view_settings['author_url_type'] == 'id' ? ' selected="selected"' : '';
				echo '<option value="id"' . $selected_type . '>ID</option>';
				$selected_type = $view_settings['author_url_type'] == 'username' ? ' selected="selected"' : '';
				echo '<option value="username"' . $selected_type . '>username</option>';
				?>
			</select>
			<label><?php _e(' is set by this URL parameter: ', 'wpv-views'); ?></label>
			<input type='text' class="js-wpv-filter-author-url js-wpv-filter-validate" data-type="url" data-class="js-wpv-filter-author-url" name="author_url" value="<?php echo $view_settings['author_url']; ?>" size="10" />
		</li>
		<li>
			<?php $checked = $view_settings['author_mode'] == 'shortcode' ? 'checked="checked"' : ''; ?>
			<label><input type="radio" name="author_mode[]" value="shortcode" <?php echo $checked; ?>>&nbsp;<?php _e('Post author\'s ', 'wpv-views'); ?></label>
			<select id="wpv_author_shortcode_type" name="author_shortcode_type">
			<?php
			$selected_type = $view_settings['author_shortcode_type'] == 'id' ? ' selected="selected"' : '';
			echo '<option value="id"' . $selected_type . '>ID</option>';
			$selected_type = $view_settings['author_shortcode_type'] == 'username' ? ' selected="selected"' : '';
			echo '<option value="username"' . $selected_type . '>username</option>';
			?>
			</select>
			<label><?php _e(' is set by this View shortcode attribute: ', 'wpv-views'); ?></label>
			<input type='text' class="js-wpv-filter-author-shortcode js-wpv-filter-validate" data-type="shortcode" data-class="js-wpv-filter-author-shortcode" name="author_shortcode" value="<?php echo $view_settings['author_shortcode']; ?>" size="10" />
		</li>
		</ul>
		<div class="filter-helper js-wpv-author-helper"></div>
		<?php
}

/**
* Render author filter summary text
*/

function wpv_get_filter_author_summary_txt($view_settings, $short=false) {
	global $wpdb;
	if (isset($_GET['post'])) {$view_name = get_the_title( $_GET['post']);} else {
		if (isset($_GET['view_id'])) {$view_name = get_the_title( $_GET['view_id']);} else {$view_name = 'view-name';}
	}
	ob_start();

	switch ($view_settings['author_mode']) {

	case 'current_user':
		_e('Select posts with the <strong>author</strong> the same as the <strong>current logged in user</strong>.', 'wpv-views');
		break;
	case 'this_user':
		if (isset($view_settings['author_id']) && $view_settings['author_id'] > 0) {
			$selected_author = $wpdb->get_var($wpdb->prepare("SELECT display_name FROM $wpdb->users WHERE ID=%d", $view_settings['author_id']));
		} else {
			$selected_author = 'None';
		}
		echo sprintf(__('Select posts with <strong>%s</strong> as the <strong>author</strong>.', 'wpv-views'), $selected_author);
		break;
	case 'parent_view':
		_e('Select posts with the <strong>author set by the parent View</strong>.', 'wpv-views');
		break;
	case 'by_url':
		if (isset($view_settings['author_url']) && '' != $view_settings['author_url']){
			$url_author = $view_settings['author_url'];
		} else {
			$url_author = '<i>' . __('None set', 'wpv-views') . '</i>';
		}
		if (isset($view_settings['author_url_type']) && '' != $view_settings['author_url_type']){
			$url_author_type = $view_settings['author_url_type'];
			switch ($url_author_type) {
				case 'id':
					$example = '1';
					break;
				case 'username':
					$example = 'admin';
					break;
			}
		} else {
			$url_author_type = '<i>' . __('None set', 'wpv-views') . '</i>';
			$example = '';
		}
		echo sprintf(__('Select posts with the author\'s <strong>%s</strong> determined by the URL parameter <strong>"%s"</strong>', 'wpv-views'), $url_author_type, $url_author);
		if ('' != $example) echo sprintf(__(' eg. yoursite/page-with-this-view/?<strong>%s</strong>=%s', 'wpv-views'), $url_author, $example);
		break;
	case 'shortcode':
		if (isset($view_settings['author_shortcode']) && '' != $view_settings['author_shortcode']) {
			$auth_short = $view_settings['author_shortcode'];
		} else {
			$auth_short = 'None';
		}
		if (isset($view_settings['author_shortcode_type']) && '' != $view_settings['author_shortcode_type']){
			$shortcode_author_type = $view_settings['author_shortcode_type'];
			switch ($shortcode_author_type) {
				case 'id':
					$example = '1';
					break;
				case 'username':
					$example = 'admin';
					break;
			}
		} else {
			$shortcode_author_type = '<i>' . __('None set', 'wpv-views') . '</i>';
			$example = '';
		}
		echo sprintf(__('Select posts which author\'s <strong>%s</strong> is set by the View shortcode attribute <strong>"%s"</strong>', 'wpv-views'), $shortcode_author_type, $auth_short);
		if ('' != $example) {
			echo sprintf(__(' eg. [wpv-view name="%s" <strong>%s</strong>="%s"]', 'wpv-views'), $view_name, $auth_short, $example);
		}
		break;
	}

	$data = ob_get_clean();

	if ($short) {
		// this happens on the Views table under Filter column
		if (substr($data, -1) == '.') {
			$data = substr($data, 0, -1);
		}
	}

	return $data;

}

// TODO check the wpv-view-get-summary filter and the suggest actions

