<?php

if(is_admin()){

	/**
	* Add the User filter to the list and to the popup select
	*/
    
	add_action('wpv_add_users_filter_list_item', 'wpv_add_filter_users_list_item', 1, 1);
	add_filter('wpv_users_filters_add_filter', 'wpv_filters_add_filter_users', 1,1);

	function wpv_filters_add_filter_users($filters) {
		$filters['users_filter'] = array('name' => __('Specific users', 'wpv-views'),
						'present' => 'users_mode',
						'callback' => 'wpv_add_new_filter_users_list_item'
						);

		return $filters;
	}

	/**
	* Create users filter callback
	*/

	function wpv_add_new_filter_users_list_item() {
		$args = array(
			'users_mode' => array('this_user')
		);
		wpv_add_filter_users_list_item($args);
	}

	/**
	* Render users filter item in the filters list
	*/

	function wpv_add_filter_users_list_item($view_settings) {
		if (isset($view_settings['users_mode'][0])) {
			$li = wpv_get_list_item_ui_users(null, $view_settings);
			echo '<li id="js-row-users" class="js-filter-row js-filter-row-simple js-filter-for-users js-filter-users js-filter-row-users_filter">' . $li . '</li>';
		}
	}

	/**
	* Render users filter item content in the filters list
	*/

	function wpv_get_list_item_ui_users( $selected, $view_settings = null ) {

		if ( isset( $view_settings['users_mode'] ) && is_array( $view_settings['users_mode'] ) ) {
			$view_settings['users_mode'] = $view_settings['users_mode'][0];
		}
		
		ob_start();
		?>
		<p class='wpv-filter-users-edit-summary js-wpv-filter-summary js-wpv-filter-users-summary'>
			<?php echo wpv_get_filter_users_summary_txt ($view_settings ); ?>
		</p>
		<p class='edit-filter js-wpv-filter-edit-controls'>
			<i class='button-secondary icon-edit icon-large js-wpv-filter-edit-open js-wpv-filter-users-edit-open' title='<?php echo esc_attr( __('Edit this filter','wpv-views') ); ?>'></i>
			<i class='button-secondary icon-trash icon-large js-filter-remove' title='<?php echo esc_attr( __('Delete this filter','wpv-views') ); ?>' data-nonce='<?php echo wp_create_nonce( 'wpv_view_filter_users_delete_nonce' ); ?>'></i>
		</p>
		<div id="wpv-filter-users-edit" class="wpv-filter-users-edit wpv-filter-edit js-wpv-filter-edit">
			<fieldset>
				<p><strong><?php echo __('Specific users', 'wpv-views'); ?>:</strong></p>
				<div id="wpv-filter-users" class="js-filter-users-list">
					<?php wpv_render_users_options( array( 'mode' => 'edit', 'view_settings' => $view_settings ) ); ?>
				</div>
			</fieldset>
			<p>
				<input class="button-secondary js-wpv-filter-edit-ok js-wpv-filter-users-edit-ok" type="button" value="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-save="<?php echo htmlentities( __('Save', 'wpv-views'), ENT_QUOTES ); ?>" data-close="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-success="<?php echo htmlentities( __('Updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_users_nonce' ); ?>" />
			</p>
		</div>
		<?php
		$res = ob_get_clean();
		return $res;
		/*
		ob_start();
		wpv_render_users_options(array('mode' => 'edit', 'view_settings' => $view_settings));
		$data = ob_get_clean();
		$td = "<p class='wpv-filter-users-edit-summary js-wpv-filter-summary js-wpv-filter-users-summary'>\n";
		$td .= wpv_get_filter_users_summary_txt($view_settings);
		$td .= "</p>\n<p class='edit-filter js-wpv-filter-edit-controls'>\n<i class='button-secondary icon-edit icon-large js-wpv-filter-edit-open js-wpv-filter-users-edit-open' title='".
		__('Edit','wpv-views') ."'></i>\n<i class='button-secondary icon-trash icon-large js-filter-remove' title='". __('Delete this filter','wpv-views') ."' data-nonce='". wp_create_nonce( 'wpv_view_filter_users_delete_nonce' )
		. "'></i>\n</p>";
		$td .= "<div id=\"wpv-filter-users-edit\" class=\"wpv-filter-users-edit wpv-filter-edit js-wpv-filter-edit\">\n";
		$td .= '<fieldset>';
		$td .= '<p><strong>' . __('Specific users', 'wpv-views') . ':</strong></p>';
		$td .= '<div id="wpv-filter-users" class="js-filter-users-list">' . $data . '</div>';
		$td .= '</fieldset>';
		ob_start();
		?>
		<p>
			<input class="button-secondary js-wpv-filter-edit-ok js-wpv-filter-users-edit-ok" type="button" value="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-save="<?php echo htmlentities( __('Save', 'wpv-views'), ENT_QUOTES ); ?>" data-close="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-success="<?php echo htmlentities( __('Updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_users_nonce' ); ?>" />
		</p>
		<p class="wpv-custom-fields-help">
                        <?php echo sprintf(__('%sLearn about filtering by users%s', 'wpv-views'),
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
	* Update users filter callback
	*/

	add_action('wp_ajax_wpv_filter_users_update', 'wpv_filter_users_update_callback');

	function wpv_filter_users_update_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_users_nonce') ) die("Security check");
		if ( empty( $_POST['filter_users'] ) ) {
			echo $_POST['id'];
			die();
		}

		parse_str($_POST['filter_users'], $filter_users);
		$change = false;
		$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
		if ( !isset( $filter_users['users_name'] ) || '' == $filter_users['users_name'] ) {
			$filter_users['users_name'] = '';
			$filter_users['users_id'] = 0;
		}
		if ( !isset( $view_array['users_query_in'] ) || $filter_users['users_query_in'] != $view_array['users_query_in'] ) {
			$change = true;
			$view_array['users_query_in'] = $filter_users['users_query_in'];
		}
		if ( !isset( $view_array['users_mode'] ) || $filter_users['users_mode'] != $view_array['users_mode'] ) {
			$change = true;
			$view_array['users_mode'] = $filter_users['users_mode'];
		}
		if ( !isset( $view_array['users_name'] ) || $filter_users['users_name'] != $view_array['users_name'] ) {
			$change = true;
			$view_array['users_name'] = $filter_users['users_name'];
		}
		if ( !isset( $view_array['users_id'] ) || $filter_users['users_id'] != $view_array['users_id'] ) {
			$change = true;
			$view_array['users_id'] = $filter_users['users_id'];
		}
		if ( !isset( $view_array['users_url_type'] ) || $filter_users['users_url_type'] != $view_array['users_url_type'] ) {
			$change = true;
			$view_array['users_url_type'] = $filter_users['users_url_type'];
		}
		if ( !isset( $view_array['users_url'] ) || sanitize_text_field($filter_users['users_url']) != $view_array['users_url'] ) {
			$change = true;
			$view_array['users_url'] = sanitize_text_field($filter_users['users_url']);
		}
		if ( !isset( $view_array['users_shortcode_type'] ) || $filter_users['users_shortcode_type'] != $view_array['users_shortcode_type'] ) {
			$change = true;
			$view_array['users_shortcode_type'] = $filter_users['users_shortcode_type'];
		}
		if ( !isset( $view_array['users_shortcode'] ) || sanitize_text_field($filter_users['users_shortcode']) != $view_array['users_shortcode'] ) {
			$change = true;
			$view_array['users_shortcode'] = sanitize_text_field($filter_users['users_shortcode']);
		}
		if ( $change ) {
			$result = update_post_meta($_POST["id"], '_wpv_settings', $view_array);
		}

		$filter_users['users_mode'] = $filter_users['users_mode'][0];
		echo wpv_get_filter_users_summary_txt($filter_users);
		die();
	}

	/**
	* Update users filter summary callback
	*/

	// TODO This might not be needed here, maybe for summary filter
	add_action('wp_ajax_wpv_filter_users_sumary_update', 'wpv_filter_users_sumary_update_callback');

	function wpv_filter_users_sumary_update_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_users_nonce') ) die("Security check");
		parse_str($_POST['filter_users'], $filter_users);
		$filter_users['users_mode'] = $filter_users['users_mode'][0];
		echo wpv_get_filter_users_summary_txt($filter_users);
		die();
	}

	/**
	* Delete users filter callback
	*/

	add_action('wp_ajax_wpv_filter_users_delete', 'wpv_filter_users_delete_callback');

	function wpv_filter_users_delete_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_users_delete_nonce') ) die("Security check");
		$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
		if ( isset( $view_array['users_query_in'] ) ) {
			unset( $view_array['users_query_in'] );
		}
		if ( isset( $view_array['users_mode'] ) ) {
			unset( $view_array['users_mode'] );
		}
		if ( isset( $view_array['users_name'] ) ) {
			unset( $view_array['users_name'] );
		}
		if ( isset( $view_array['users_id'] ) ) {
			unset( $view_array['users_id'] );
		}
		if ( isset( $view_array['users_url_type'] ) ) {
			unset( $view_array['users_url_type'] );
		}
		if ( isset( $view_array['users_url'] ) ) {
			unset( $view_array['users_url'] );
		}
		if ( isset( $view_array['users_shortcode_type'] ) ) {
			unset( $view_array['users_shortcode_type'] );
		}
		if ( isset( $view_array['users_shortcode'] ) ) {
			unset( $view_array['users_shortcode'] );
		}
		update_post_meta($_POST["id"], '_wpv_settings', $view_array);
		echo $_POST['id'];
		die();

	}

	/**
	* Add a filter to show the filter on the summary
	*/

	add_filter('wpv-view-get-summary', 'wpv_users_summary_filter', 5, 3);

	function wpv_users_summary_filter($summary, $post_id, $view_settings) {
		if(isset($view_settings['query_type']) && $view_settings['query_type'][0] == 'users' && isset($view_settings['users_mode'])) {
			$view_settings['users_mode'] = $view_settings['users_mode'][0];

			$result = wpv_get_filter_users_summary_txt($view_settings, true);
			if ($result != '' && $summary != '') {
				$summary .= '<br />';
			}
			$summary .= $result;
		}
		return $summary;
	}
    

	/**
	* Add users suggest functionality
	*/

	add_action('wp_ajax_wpv_suggest_users', 'wpv_suggest_users');
	add_action('wp_ajax_nopriv_wpv_suggest_users', 'wpv_suggest_users');

	function wpv_suggest_users() {
		global $wpdb; // TODO this global is not needed anymore, it seems
		$_view_settings = get_post_meta($_GET['view_id'], '_wpv_settings', true);
		$query_type = 'administrator';
		if ( isset( $_view_settings['roles_type'][0]) ){
			$query_type = $_view_settings['roles_type'][0];
		}
		$user = '*'.esc_sql(like_escape($_REQUEST['q'])).'*';
		$response = array();
		$args = array(
			'search'         => $user,
			'search_columns' => array( 'user_login', 'user_email' ),
			'role' => $query_type,
			'number' => 20
		);
		$user_query = new WP_User_Query( $args );
		if ( ! empty( $user_query->results ) ) {
			foreach ( $user_query->results as $user ) {
				$response[] = array('id'=> $user->ID, 'name'=> $user->display_name );
			}
		}
		$json_response = json_encode($response);
		echo $json_response;
		die();
	}

}

/**
* Render users filter options
*/

function wpv_render_users_options($args) {
	global $wpdb; // TODO this global seems not used anymore

	$edit = isset($args['mode']) && $args['mode'] == 'edit';

	$view_settings = isset($args['view_settings']) ? $args['view_settings'] : array();

	$defaults = array('users_query_in' => 'include',
				'users_mode' => 'this_user',
				'users_name' =>'',
				'users_id' => 0,
				'users_url' => 'users-filter',
				'users_url_type' => '',
				'users_shortcode' => 'users',
				'users_shortcode_type' => '');
	$view_settings = wp_parse_args($view_settings, $defaults);
	$view_id = '';
	if ( isset($_GET['view_id']) ){
		$view_id = $_GET['view_id'];
	}
	if ( isset($_POST['view_id']) ){
		$view_id = $_POST['view_id'];
	}
		?>
		<p>
			<label for="users_filter_type"><?php _e('This View displays', 'wpv-views'); ?></label>:
			<select id="users_filter_type" name="users_query_in">
				<?php $selected = $view_settings['users_query_in'] == 'include' ? ' selected="selected"' : ''; ?>
				<option value="include"<?php echo $selected; ?>>only those users</option>
				<?php $selected = $view_settings['users_query_in'] == 'exclude' ? ' selected="selected"' : ''; ?>
				<option value="exclude"<?php echo $selected; ?>>all users but those</option>
			</select>
		</p>
	<!--	<ul>
			<?php //$radio_name = $edit ? '_wpv_settings[users_mode][]' : 'users_mode[]' ?>
			<li>
				<?php //$checked = $view_settings['users_query_in'] == 'include' ? 'checked="checked"' : ''; ?>
				<label>
					<input type="radio" name="users_query_in" value="include" <?php //echo $checked; ?> />
					<?php //_e('Specific users list', 'wpv-views'); ?>
				</label>
			</li>
				<?php //$checked = $view_settings['users_query_in'] == 'exclude' ? 'checked="checked"' : ''; ?>
				<label>
					<input type="radio" name="users_query_in" value="exclude" <?php //echo $checked; ?> />
					<?php //_e('All users except a specific list of users', 'wpv-views'); ?>
				</label>
			</li>
		</ul>-->
		<ul>
			<li>
			<?php $checked = $view_settings['users_mode'] == 'this_user' ? 'checked="checked"' : ''; ?>
				<label>
					<input type="radio" name="users_mode[]" value="this_user" <?php echo $checked; ?> />
					<?php _e('Users with this display name ', 'wpv-views'); ?>
				</label>
			</li>
			<li>
				<input id="wpv_users_name" class="users_suggest js-users-suggest" type='hidden' name="users_name" value="<?php echo $view_settings['users_name']; ?>" size="15" />
				<input id="wpv_users" class="users_suggest_id js-users-suggest-id" type='text' name="users_id" value="<?php echo $view_settings['users_id']; ?>" size="10" />
			</li>
			<li>
				<?php $checked = $view_settings['users_mode'] == 'by_url' ? 'checked="checked"' : ''; ?>
				<label><input type="radio" name="users_mode[]" value="by_url" <?php echo $checked; ?> />&nbsp;<?php _e('Users with ', 'wpv-views'); ?></label>
				<select id="wpv_users_url_type" name="users_url_type">
					<?php
					$selected_type = $view_settings['users_url_type'] == 'id' ? ' selected="selected"' : '';
					echo '<option value="id"' . $selected_type . '>' . __('ID', 'wpv-views') . '</option>';
					$selected_type = $view_settings['users_url_type'] == 'username' ? ' selected="selected"' : '';
					echo '<option value="username"' . $selected_type . '>' . __('username', 'wpv-views') . '</option>';
					?>
				</select>
				<label><?php _e(' set by this URL parameter: ', 'wpv-views'); ?></label>
				<input type='text' class="js-wpv-filter-users-url js-wpv-filter-validate" data-type="url" data-class="js-wpv-filter-users-url" name="users_url" value="<?php echo $view_settings['users_url']; ?>" size="10" />
			</li>
			<li>
				<?php $checked = $view_settings['users_mode'] == 'shortcode' ? 'checked="checked"' : ''; ?>
				<label><input type="radio" name="users_mode[]" value="shortcode" <?php echo $checked; ?>>&nbsp;<?php _e('Users with ', 'wpv-views'); ?></label>
				<select id="wpv_users_shortcode_type" name="users_shortcode_type">
				<?php
				$selected_type = $view_settings['users_shortcode_type'] == 'id' ? ' selected="selected"' : '';
				echo '<option value="id"' . $selected_type . '>' . __('ID', 'wpv-views') . '</option>';
				$selected_type = $view_settings['users_shortcode_type'] == 'username' ? ' selected="selected"' : '';
				echo '<option value="username"' . $selected_type . '>' . __('username', 'wpv-views') . '</option>';
				?>
				</select>
				<label><?php _e(' set by this View shortcode attribute: ', 'wpv-views'); ?></label>
				<input type='text' class="js-wpv-filter-users-shortcode js-wpv-filter-validate" data-type="shortcode" data-class="js-wpv-filter-users-shortcode" name="users_shortcode" value="<?php echo $view_settings['users_shortcode']; ?>" size="10" />
			</li>
		</ul>
		<?php
			$users = array();
			$ids = explode( ',', $view_settings['users_id']);
					if ( count( $ids ) !== 0){

                    	$names = explode( ',', $view_settings['users_name']);
                    	for ( $i=0; $i<count($ids); $i++){
                    		if ($ids[$i] != 0){
                    		$users[] =array('id'=>$ids[$i],'name'=>$names[$i]);
							}
						}

			}

		?>
		<input type="hidden" value="" class="js-wpv-user-suggest-values" data-hinttext="<?php _e('Type for seach users', 'wpv-views'); ?>..."
		data-noresult="<?php _e('No users matched your criteria', 'wpv-views'); ?>"
		data-search="<?php _e('Searching', 'wpv-views'); ?>..."
		data-viewid="<?php echo $view_id;?>"
		data-users = '<?php echo json_encode($users)?>'
		/>
		<div class="filter-helper js-wpv-users-helper"></div>
		<?php
}

/**
* Render users filter summary text
*/

function wpv_get_filter_users_summary_txt($view_settings, $short=false, $post_id='') {
	if ( isset( $_GET['post'] ) ) {
		$view_name = get_the_title( $_GET['post'] );
	} else {
		if ( isset( $_GET['view_id'] ) ) {
			$view_name = get_the_title( $_GET['view_id'] );
		} else {
			$view_name = 'view-name';
		}
	}
	if ( !isset( $view_settings['users_mode'] ) ) {
        return;
    }
	ob_start();
	if ( isset($_GET['view_id']) ){
		$_view_settings = get_post_meta($_GET['view_id'], '_wpv_settings', true);
	}
	if ( isset($_POST['id']) ){
		$_view_settings = get_post_meta($_POST["id"], '_wpv_settings', true);
	}
    if ( !isset($_view_settings) && !empty($post_id) ){
        $_view_settings = get_post_meta($post_id, '_wpv_settings', true);
    }
	if ( isset($view_settings['roles_type'][0]) ){
		$user_role = $view_settings['roles_type'][0];
	}
	else{
		$user_role = $_view_settings['roles_type'][0];
	}
	if ( !isset($user_role) ){
		$user_role = 'administrator';
	}
    if ( is_array($view_settings['users_mode']) ){
        $view_settings['users_mode'] = $view_settings['users_mode'][0];
    }

   
	switch ($view_settings['users_mode']) {

	case 'this_user':
		if (isset($view_settings['users_id']) && $view_settings['users_id'] > 0) {
			if ( $view_settings['users_query_in'] == 'include' ){
				echo sprintf(__('Select users <strong>(%s)</strong> who have role <strong>%s</strong>', 'wpv-views'), $_view_settings['users_name'], $user_role);
			}else{
				echo sprintf(__('Select all users with role <strong>%s</strong>, except of <strong>(%s)</strong>', 'wpv-views'), $user_role , $_view_settings['users_name']);
			}
		} else {
			echo sprintf(__('Select all users with role <strong>%s</strong>', 'wpv-views'), $user_role);
		}
		break;
	case 'by_url':
		if (isset($view_settings['users_url']) && '' != $view_settings['users_url']){
			$url_users = $view_settings['users_url'];
		} else {
			$url_users = '<i>' . __('None set', 'wpv-views') . '</i>';
		}
		if (isset($view_settings['users_url_type']) && '' != $view_settings['users_url_type']){
			$url_users_type = $view_settings['users_url_type'];
			switch ($url_users_type) {
				case 'id':
					$example = '1';
					break;
				case 'username':
					$example = 'admin';
					break;
			}
		} else {
			$url_users_type = '<i>' . __('None set', 'wpv-views') . '</i>';
			$example = '';
		}

		if ( $view_settings['users_query_in'] == 'include' ){
			echo sprintf(__('Select users with the <strong>%s</strong> determined by the URL parameter <strong>"%s"</strong> and with role <strong>"%s"</strong>', 'wpv-views'), $url_users_type, $url_users, $user_role);
		}
		else{
			echo sprintf(__('Select all users with role <strong>%s</strong>, except of <strong>%s</strong> determined by the URL parameter <strong>"%s"</strong>', 'wpv-views'), $user_role, $url_users_type, $url_users);
		}
		if ('' != $example) echo '<br /><code>' . sprintf(__(' eg. yoursite/page-with-this-view/?<strong>%s</strong>=%s', 'wpv-views'), $url_users, $example) . '</code>';
		break;
	case 'shortcode':
		if (isset($view_settings['users_shortcode']) && '' != $view_settings['users_shortcode']) {
			$auth_short = $view_settings['users_shortcode'];
		} else {
			$auth_short = 'None';
		}
		if (isset($view_settings['users_shortcode_type']) && '' != $view_settings['users_shortcode_type']){
			$shortcode_users_type = $view_settings['users_shortcode_type'];
			switch ($shortcode_users_type) {
				case 'id':
					$example = '1';
					break;
				case 'username':
					$example = 'admin';
					break;
			}
		} else {
			$shortcode_users_type = '<i>' . __('None set', 'wpv-views') . '</i>';
			$example = '';
		}
		if ( $view_settings['users_query_in'] == 'include' ){
			echo sprintf(__('Select users with <strong>%s</strong> set by the View shortcode attribute <strong>"%s"</strong> and with role <strong>"%s"</strong>', 'wpv-views'), $shortcode_users_type, $auth_short, $user_role);
		}
		else{
			echo sprintf(__('Select all users with role <strong>%s</strong>, except of <strong>%s</strong> set by the View shortcode attribute <strong>"%s"</strong>', 'wpv-views'), $user_role, $shortcode_users_type, $auth_short);
		}
		if ('' != $example) {
			echo '<br /><code>' . sprintf(__(' eg. [wpv-view name="%s" <strong>%s</strong>="%s"]', 'wpv-views'), $view_name, $auth_short, $example) . '</code>';
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

// DEPRECATED
// New filter in the wpv-sections-query-type.php file
// Also, too much information here: create specific filters for that

// add_filter('wpv-view-get-content-summary', 'wpv_users_content_summary_filter', 5, 3);

function wpv_users_content_summary_filter($summary, $post_id, $view_settings) {
    $summary = '';
    $result = '';
    $result1 = '';
    if(!isset($view_settings['query_type']) || (isset($view_settings['query_type']) && $view_settings['query_type'][0] == 'users')) {
    
            $user_role = '';
            $result = wpv_get_filter_users_summary_txt($view_settings , false, $post_id);
            if ( empty($result) ){
                if ( isset($view_settings['roles_type'][0]) ){
                    $user_role = $view_settings['roles_type'][0];
                }
                $result = sprintf(__('Select all users with role <strong>%s</strong>', 'wpv-views'),  $user_role);
            }
            $result1 = wpv_get_filter_users_summary_txt_addon( $view_settings );
            $summary = $result . $result1;
	
    }

    return $summary;
}

function wpv_get_filter_users_summary_txt_addon( $view_settings ){
    
    $output = '';
    $summary = '';
    foreach (array_keys($view_settings) as $key) {
            if (strpos($key, 'usermeta-field-') === 0 && strpos($key, '_compare') === strlen($key) - strlen('_compare')) {
                $name = substr($key, 0, strlen($key) - strlen('_compare'));
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
    if ( !empty($summary) ){
        $output .= __(' and ', 'wpv-views'). $summary;
    }
    if ( isset($view_settings['users_orderby']) ){
    	$output .=  __(' ordered by ', 'wpv-views'). $view_settings['users_orderby'];
	}
    $order = __('descending', 'wpv-views');
    if ( isset($view_settings['users_order']) && $view_settings['users_order'] == 'ASC') {
        $order = __('ascending', 'wpv-views');
    }
    $output .= ', '.$order;
    if ( isset($view_settings['users_limit']) && intval($view_settings['users_limit']) != -1 ) {
            if (intval($view_settings['users_limit']) == 1) {
                $output .= __(', limit to 1 item', 'wpv-views');
            } else {
                $output .= sprintf(__(', limit to %d items', 'wpv-views'),
                        intval($view_settings['users_limit']));
            }
    }
    if ( isset($view_settings['users_limit']) && intval($view_settings['users_offset']) != 0 ) {
            if (intval($view_settings['users_limit']) == 1) {
                $output .= __(', skip first item', 'wpv-views');
            } else {
                $output .= sprintf(__(', skip %d items', 'wpv-views'),
                        intval($view_settings['users_offset']));
            }
    }
    return $output;    
}
