<?php

/*
* We can enable this to hide the Ordering section
*/

// add_filter('wpv_sections_query_show_hide', 'wpv_show_hide_ordering', 1,1)

function wpv_show_hide_ordering($sections) {
	$sections['ordering'] = array(
		'name'		=> __('Ordering', 'wpv-views'),
		);
	return $sections;
}

add_action('view-editor-section-query', 'add_view_ordering', 30, 2);

function add_view_ordering($view_settings, $view_id) {
    global $views_edit_help, $WP_Views;
	$hide = '';
	if (isset($view_settings['sections-show-hide']) && isset($view_settings['sections-show-hide']['ordering']) && 'off' == $view_settings['sections-show-hide']['ordering']) {
		$hide = ' hidden';
	}?>
	<div class="wpv-setting-container wpv-settings-ordering js-wpv-settings-ordering<?php echo $hide; ?>">
		<div class="wpv-settings-header">
			<h3>
				<?php _e( 'Ordering', 'wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip" data-header="<?php echo $views_edit_help['ordering']['title']; ?>" data-content="<?php echo $views_edit_help['ordering']['content']; ?>"></i>
			</h3>
		</div>
		<div class="wpv-setting">
			<p class="wpv-settings-query-type-posts js-wpv-settings-posts-order"<?php echo $view_settings['query_type'][0]!='posts' ? ' style="display: none;"' : ''; ?>>
				<?php $view_settings = wpv_order_by_default_settings($view_settings); // TODO this should not be needed ?>
				<label for="wpv-settings-orderby"><?php _e( 'Order by: ', 'wpv-views' ) ?></label>
				<select id="wpv-settings-orderby" class="js-wpv-posts-orderby" name="_wpv_settings[orderby]" data-rand="<?php _e('Pagination and random ordering do not work together and would produce unexpected results. Please disable pagination or random ordering.', 'wpv-views'); ?>">
					<option value="post_date"><?php _e('post date', 'wpv-views'); ?></option>
					<?php $selected = $view_settings['orderby']=='post_title' ? ' selected="selected"' : ''; ?>
					<option value="post_title" <?php echo $selected ?>><?php _e('post title', 'wpv-views'); ?></option>
					<?php $selected = $view_settings['orderby']=='ID' ? ' selected="selected"' : ''; ?>
					<option value="ID" <?php echo $selected ?>><?php _e('post id', 'wpv-views'); ?></option>
					<?php $selected = $view_settings['orderby']=='modified' ? ' selected="selected"' : ''; ?>
					<option value="modified" <?php echo $selected ?>><?php _e('last modified', 'wpv-views'); ?></option>
					<?php $selected = $view_settings['orderby']=='menu_order' ? ' selected="selected"' : ''; ?>
					<option value="menu_order" <?php echo $selected ?>><?php _e('menu order', 'wpv-views'); ?></option>
					<?php $selected = $view_settings['orderby']=='rand' ? ' selected="selected"' : ''; ?>
					<option value="rand" <?php echo $selected ?>><?php _e('random order', 'wpv-views'); ?></option>
					<?php
						$all_types_fields = get_option( 'wpcf-fields', array() );
						$cf_keys = $WP_Views->get_meta_keys();
						foreach ($cf_keys as $key) {
						$selected = $view_settings['orderby'] == "field-" . $key ? ' selected="selected"' : '';
						$option = '<option value="field-' . $key . '"' . $selected . '>';
						if (stripos($key, 'wpcf-') === 0) {
							if ( isset( $all_types_fields[substr( $key, 5 )] ) && isset( $all_types_fields[substr( $key, 5 )]['name'] ) ) {
								$option .= sprintf(__('Field - %s', 'wpv-views'), $all_types_fields[substr( $key, 5 )]['name']);
							} else {
								$option .= sprintf(__('Field - %s', 'wpv-views'), $key);
							}
						} else {
							$option .= sprintf(__('Field - %s', 'wpv-views'), $key);
						}
						$option .= '</option>';
						echo $option;
						}
					?>
				</select>
				<select name="_wpv_settings[order]" class="js-wpv-posts-order">
					<option value="DESC"><?php _e( 'Descending', 'wpv-views' ) ?></option>
					<?php $selected = $view_settings['order']=='ASC' ? ' selected="selected"' : ''; ?>
					<option value="ASC"<?php echo $selected ?>><?php _e( 'Ascending', 'wpv-views' ) ?></option>
				</select>
			</p>
			<p class="wpv-settings-query-type-taxonomy"<?php echo $view_settings['query_type'][0]!='taxonomy' ? ' style="display: none;"' : ''; ?>>
				<?php $view_settings = wpv_taxonomy_order_by_default_settings($view_settings);
				$taxonomy_order_by = array(
					'id' => __('ID'),
					'count' => __('Count'),
					'name' => __('Name'),
					'slug' => __('Slug'),
					'term_group' => __('Term_group'),
					'none' => __('None')
				);
				?>
				<label for="wpv-settings-orderby"><?php _e( 'Order by: ', 'wpv-views' ) ?></label>
				<select id="wpv-settings-orderby" class="js-wpv-taxonomy-orderby" name="_wpv_settings[taxonomy_orderby]">
					<?php
						foreach($taxonomy_order_by as $id => $text) {
						$selected = $view_settings['taxonomy_orderby']==$id ? ' selected="selected"' : '';
						?>
							<option value="<?php echo $id; ?>" <?php echo $selected ?>><?php echo $text; ?></option>
						<?php

						}
					?>
				</select>
				<select name="_wpv_settings[taxonomy_order]" class="js-wpv-taxonomy-order">
					<option value="DESC"><?php _e( 'Descending', 'wpv-views' ) ?></option>
					<?php $selected = $view_settings['taxonomy_order']=='ASC' ? ' selected="selected"' : ''; ?>
					<option value="ASC"<?php echo $selected ?>><?php _e( 'Ascending', 'wpv-views' ) ?></option>
				</select>
			</p>
			<p class="wpv-settings-query-type-users"<?php echo $view_settings['query_type'][0]!='users' ? ' style="display: none;"' : ''; ?>>
				<?php 
				$users_order_by = array(
					'user_login' => __('user login'),
					'ID' => __('user id'),
					'user_name' => __('user name'),
					'display_name' => __('user display name'),
					'user_nicename' => __('user nicename'),
					'user_email' => __('user email'),
					'user_url' => __('user url'),
					'user_registered' => __('user registered date'),
					'post_count' => __('user post count')
				);
				if ( !isset($view_settings['users_orderby']) ){
					$view_settings['users_orderby'] = 'user_login';
				}
				if ( !isset($view_settings['users_order']) ){
					$view_settings['users_order'] = 'DESC';
				}
				?>
				<label for="wpv-settings-orderby"><?php _e( 'Order by: ', 'wpv-views' ) ?></label>
				<select id="wpv-settings-orderby" class="js-wpv-users-orderby" name="_wpv_settings[users_orderby]">
					<?php
						foreach($users_order_by as $id => $text) {
						$selected = $view_settings['users_orderby']==$id ? ' selected="selected"' : '';
						?>
							<option value="<?php echo $id; ?>" <?php echo $selected ?>><?php echo $text; ?></option>
						<?php

						}
					?>
				</select>
				<select name="_wpv_settings[users_order]" class="js-wpv-users-order">
					<?php $selected = $view_settings['users_order']=='DESC' ? ' selected="selected"' : ''; ?>
					<option value="DESC"<?php echo $selected ?>><?php _e( 'Descending', 'wpv-views' ) ?></option>
					<?php $selected = $view_settings['users_order']=='ASC' ? ' selected="selected"' : ''; ?>
					<option value="ASC"<?php echo $selected ?>><?php _e( 'Ascending', 'wpv-views' ) ?></option>
				</select>
			</p>
			<p class="update-button-wrap">
				<button data-success="<?php echo htmlentities( __('Sorting options updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Sorting options not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_ordering_nonce' ); ?>" class="js-wpv-ordering-update button-secondary" disabled="disabled"><?php _e('Update', 'wpv-views'); ?></button>
			</p>
		</div>
	</div>
<?php }

/**
* wpv_ordering_summary_filter
*
* Returns the sorting part when building the summary for a View
*
* @param $summary
* @param $post_id
* @param $view_settings
*
* @returns (string) $summary
*
* @since 1.6.0
*/

add_filter('wpv-view-get-content-summary', 'wpv_ordering_summary_filter', 5, 3);

function wpv_ordering_summary_filter( $summary, $post_id, $view_settings ) {
	$summary .= wpv_get_ordering_summary( $view_settings );
	return $summary;
}

/**
* wpv_get_ordering_summary
*
* Returns the sorting summary for a View
*
* @param $view_settings
*
* @returns (string) $summary
*
* @since 1.6.0
*/

function wpv_get_ordering_summary( $view_settings ) {
	$view_settings = wpv_order_by_default_settings($view_settings);
	$view_settings = wpv_taxonomy_order_by_default_settings($view_settings);
	$view_settings = wpv_users_order_by_default_settings( $view_settings );
	$return = '';
	
	if ( !isset( $view_settings['query_type'] ) || ( isset($view_settings['query_type'] ) && $view_settings['query_type'][0] == 'posts' ) ) {
		switch( $view_settings['orderby'] ) {
			case 'post_date':
				$order_by = __('post date', 'wpv-views');
				break;
			case 'post_title':
				$order_by = __('post title', 'wpv-views');
				break;
			case 'ID':
				$order_by = __('post ID', 'wpv-views');
				break;
			case 'menu_order':
				$order_by = __('menu order', 'wpv-views');
				break;
			case 'rand':
				$order_by = __('random order', 'wpv-views');
				break;
			default:
				$order_by = str_replace('field-', '', $view_settings['orderby']);
				$order_by = sprintf(__('Field - %s', 'wpv-views'), $order_by);
				break;
		}
		$order = __('descending', 'wpv-views');
		if ( $view_settings['order'] == 'ASC' ) {
			$order = __( 'ascending', 'wpv-views' );
		}
		$return .= sprintf( __( ' ordered by %s, %s', 'wpv-views' ), $order_by, $order );
    }
    if ( isset( $view_settings['query_type'] ) && $view_settings['query_type'][0] == 'taxonomy' ) {
		$order_by = '';
		switch( $view_settings['taxonomy_orderby'] ) {
			case 'count':
				$order_by = __('term count', 'wpv-views');
				break;
			case 'name':
				$order_by = __('term name', 'wpv-views');
				break;
			case 'slug':
				$order_by = __('term slug', 'wpv-views');
				break;
			case 'term_group':
				$order_by = __('term group', 'wpv-views');
				break;
			case 'none':
				$order_by = __('no specific criteria', 'wpv-views');
				break;
		}
		$order = __('descending', 'wpv-views');
		if ( $view_settings['taxonomy_order'] == 'ASC' ) {
			$order = __( 'ascending', 'wpv-views' );
		}
		$return .= sprintf( __( ' ordered by %s, %s', 'wpv-views' ), $order_by, $order );
    }
    if ( isset( $view_settings['query_type'] ) && $view_settings['query_type'][0] == 'users' ) {
		$order_by = '';
		switch( $view_settings['users_orderby'] ) {
			case 'user_login':
				$order_by = __('user login', 'wpv-views');
				break;
			case 'ID':
				$order_by = __('user ID', 'wpv-views');
				break;
			case 'user_name':
				$order_by = __('user name', 'wpv-views');
				break;
			case 'display_name':
				$order_by = __('display name', 'wpv-views');
				break;
			case 'user_nicename':
				$order_by = __('user nicename', 'wpv-views');
				break;
			case 'user_email':
				$order_by = __('user email', 'wpv-views');
				break;
			case 'user_url':
				$order_by = __('user url', 'wpv-views');
				break;
			case 'user_registered':
				$order_by = __('user registered date', 'wpv-views');
				break;
			case 'post_count':
				$order_by = __('user post count', 'wpv-views');
				break;
		}
		$order = __('descending', 'wpv-views');
		if ( $view_settings['users_order'] == 'ASC' ) {
			$order = __( 'ascending', 'wpv-views' );
		}
		$return .= sprintf( __( ' ordered by %s, %s', 'wpv-views' ), $order_by, $order );
	}
	return $return;
}