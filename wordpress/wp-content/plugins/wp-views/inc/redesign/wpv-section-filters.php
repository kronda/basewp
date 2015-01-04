<?php

/*
* We can enable this to hide the Filters section
*/

// add_filter('wpv_sections_query_show_hide', 'wpv_show_hide_content_filter', 1,1);

function wpv_show_hide_content_filter($sections) {
	$sections['content-filter'] = array(
		'name'		=> __('Filter the results', 'wpv-views'),
		);
	return $sections;
}

add_action('view-editor-section-query', 'add_view_filters', 50, 2);

function add_view_filters($view_settings, $view_id) {
    global $views_edit_help;
	$hide = '';
	if (isset($view_settings['sections-show-hide']) && isset($view_settings['sections-show-hide']['content-filter']) && 'off' == $view_settings['sections-show-hide']['content-filter']) {
		$hide = ' hidden';
	}?>
	<div class="wpv-setting-container wpv-settings-content-filter js-wpv-settings-content-filter<?php echo $hide; ?>">
		<div class="wpv-settings-header">
			<h3>
				<?php _e( 'Query filter', 'wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip" data-header="<?php echo $views_edit_help['filter_the_results']['title']; ?>" data-content="<?php echo $views_edit_help['filter_the_results']['content']; ?>"></i>
			</h3>
		</div>
		<div class="wpv-setting">
			<p class="js-no-filters hidden"><?php _e( 'No filters set', 'wpv-views' ) ?></p>
			<ul class="filter-list js-filter-list hidden">
				<?php
				if (isset($view_settings['query_type']) && isset($view_settings['query_type'][0])) {
					wpv_display_filters_list( $view_settings['query_type'][0], $view_settings );
				}
				?>
			</ul>
			<input type="hidden" class="js-wpv-filter-update-filters-list-nonce" value="<?php echo wp_create_nonce( 'wpv_view_filter_update_filters_list_nonce' ); ?>" />
			<ul class="js-filter-placeholder hidden">
				<li id='js-row-taxonomy' class='filter-row-multiple js-filter-row js-filter-row-multiple js-filter-for-posts js-filter-taxonomy js-filter-row-taxonomy'>
					<p class='edit-filter js-wpv-filter-edit-controls'>
						<i class='button-secondary icon-edit icon-large edit-trigger js-wpv-filter-edit-open' title='<?php echo esc_attr( __('Edit this filter', 'wpv-views') ); ?>'></i>
						<i class='button-secondary icon-trash icon-large js-filter-taxonomy-row-remove' title='<?php echo esc_attr( __('Delete this filter', 'wpv-views') ); ?>' data-nonce='<?php echo wp_create_nonce( 'wpv_view_filter_taxonomy_row_delete_nonce' ); ?>'></i>
					</p>
					<div id="wpv-filter-taxonomy-edit" class="wpv-filter-edit js-filter-taxonomy-edit js-wpv-filter-edit">
						<div class="wpv-filter-taxonomy-relationship js-wpv-filter-taxonomy-relationship">

							<h4><?php _e('Taxonomy relationship:', 'wpv-views') ?></h4>
							<p>
								<?php _e('Relationship to use when querying with multiple taxonomies:', 'wpv-views'); ?>
								<select name="taxonomy_relationship">
									<option value="AND"><?php _e('AND', 'wpv-views'); ?>&nbsp;</option>
									<?php
									if (!isset($view_settings['taxonomy_relationship'])) {
										$view_settings['taxonomy_relationship'] = 'AND';
									}
									$selected = $view_settings['taxonomy_relationship']=='OR' ? ' selected="selected"' : '';
									?>
									<option value="OR"<?php echo $selected ?>><?php _e('OR', 'wpv-views'); ?>&nbsp;</option>
								</select>
							</p>
						</div>
						<p>
							<input class="button-secondary js-wpv-filter-edit-ok js-wpv-filter-taxonomy-edit-ok" type="button" value="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-save="<?php echo htmlentities( __('Save', 'wpv-views'), ENT_QUOTES ); ?>" data-close="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-success="<?php echo htmlentities( __('Updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_taxonomy_nonce' ); ?>" />
						</p>
						<p class="wpv-taxonomy-help">
						<?php echo sprintf(__('%sLearn about filtering by taxonomy%s', 'wpv-views'),
										'<a class="wpv-help-link" href="' . WPV_FILTER_BY_TAXONOMY_LINK . '" target="_blank">',
										' &raquo;</a>'
										); ?>
						</p>
					</div>
					<p class='wpv-filter-edit-summary wpv-filter-taxonomy-edit-summary js-wpv-filter-summary js-wpv-filter-taxonomy-summary'>
						<?php _e('Select posts with taxonomy: ', 'wpv-views');?>
					</p>
				</li>
				<li id='js-row-custom-field' class='filter-row-multiple js-filter-row js-filter-row-multiple js-filter-for-posts js-filter-custom-field js-filter-row-custom-field'>
					<p class='edit-filter js-wpv-filter-edit-controls'>
						<i class="button-secondary icon-edit icon-large edit-trigger js-wpv-filter-edit-open" title="<?php echo esc_attr( __('Edit this filter','wpv-views') ); ?>"></i>
						<i class='button-secondary icon-trash icon-large js-filter-custom-field-row-remove' title="<?php echo esc_attr( __('Delete this filter','wpv-views') ); ?>" data-nonce='<?php echo wp_create_nonce( 'wpv_view_filter_custom_field_row_delete_nonce' ); ?>'></i>
					</p>
					<div id="wpv-filter-custom-field-edit" class="wpv-filter-edit js-filter-custom-field-edit js-wpv-filter-edit">
						<div class="wpv-filter-custom-field-relationship js-wpv-filter-custom-field-relationship-container">

							<p><strong><?php _e('Custom field relationship:', 'wpv-views') ?></strong></p>
							<p>
								<?php _e('Relationship to use when querying with multiple custom fields:', 'wpv-views'); ?>
								<select name="custom_fields_relationship" class="js-wpv-filter-custom-fields-relationship">
									<option value="AND"><?php _e('AND', 'wpv-views'); ?>&nbsp;</option>
									<?php
									if (!isset($view_settings['custom_fields_relationship'])) {
										$view_settings['custom_fields_relationship'] = 'AND';
									}
									$selected = $view_settings['custom_fields_relationship']=='OR' ? ' selected="selected"' : ''; ?>
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
					<p class='wpv-filter-edit-summary wpv-filter-custom-field-edit-summary js-wpv-filter-summary js-wpv-filter-custom-field-summary'>
						<?php _e('Select posts with custom field: ', 'wpv-views');?>
					</p>
				</li>
				<li id='js-row-usermeta-field' class='filter-row-multiple js-filter-row js-filter-row-multiple js-filter-for-posts js-filter-usermeta-field js-filter-row-usermeta-field'>
					<p class='edit-filter js-wpv-filter-edit-controls'>
						<i class="button-secondary icon-edit icon-large edit-trigger js-wpv-filter-edit-open" title="<?php echo esc_attr( __('Edit this filter','wpv-views') ); ?>"></i>
						<i class='button-secondary icon-trash icon-large js-filter-usermeta-field-row-remove' title="<?php echo esc_attr( __('Delete this filter','wpv-views') ); ?>" data-nonce='<?php echo wp_create_nonce( 'wpv_view_filter_usermeta_field_row_delete_nonce' ); ?>'></i>
					</p>
					<div id="wpv-filter-usermeta-field-edit" class="wpv-filter-edit js-filter-usermeta-field-edit js-wpv-filter-edit">
						<div class="wpv-filter-usermeta-field-relationship js-wpv-filter-usermeta-field-relationship-container">

							<p><strong><?php _e('Usermeta field relationship:', 'wpv-views') ?></strong></p>
							<p>
								<?php _e('Relationship to use when querying with multiple usermeta fields:', 'wpv-views'); ?>
								<select name="usermeta_fields_relationship" class="js-wpv-filter-usermeta-fields-relationship">
									<option value="AND"><?php _e('AND', 'wpv-views'); ?>&nbsp;</option>
									<?php
									if (!isset($view_settings['usermeta_fields_relationship'])) {
										$view_settings['usermeta_fields_relationship'] = 'AND';
									}
									$selected = $view_settings['usermeta_fields_relationship']=='OR' ? ' selected="selected"' : ''; ?>
									<option value="OR"<?php echo $selected ?>><?php _e('OR', 'wpv-views'); ?>&nbsp;</option>
								</select>
							</p>
						</div>
						<p>
							<input class="button-secondary js-wpv-filter-edit-ok js-wpv-filter-usermeta-field-edit-ok" type="button" value="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-save="<?php echo htmlentities( __('Save', 'wpv-views'), ENT_QUOTES ); ?>" data-close="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-success="<?php echo htmlentities( __('Updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_usermeta_field_nonce' ); ?>" />
						</p>
						<p class="wpv-custom-fields-help">
							<?php echo sprintf(__('%sLearn about filtering by usermeta fields%s', 'wpv-views'),
											'<a class="wpv-help-link" href="' . WPV_FILTER_BY_USER_FIELDS_LINK . '" target="_blank">',
											' &raquo;</a>'
											); ?>
						</p>
					</div>
					<p class='wpv-filter-edit-summary wpv-filter-usermeta-field-edit-summary js-wpv-filter-summary js-wpv-filter-usermeta-field-summary'>
						<?php _e('Select users with usermeta field: ', 'wpv-views');?>
					</p>
				</li>
			</ul>
			<p>
				<button class="button-secondary js-wpv-filter-add-filter" type="button" data-empty="<?php echo htmlentities( __('Add a filter', 'wpv-views'), ENT_QUOTES ); ?>" data-nonempty="<?php echo htmlentities( __('Add another filter', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_add_filter' ); ?>">
					<i class="icon-plus"></i> <?php echo htmlentities( __('Add a filter', 'wpv-views'), ENT_QUOTES ); ?>
				</button>
				<?php
			// TODO all the following alerts should be added using javascript
				?>
			</p>
			<p class="toolset-alert toolset-alert-error js-filter-error js-wpv-param-missing hidden"><?php echo __('This field can not be empty', 'wpv-views'); ?></p>
			<p class="toolset-alert toolset-alert-error js-filter-error js-wpv-param-url-ilegal hidden"><?php echo __('Only lowercase letters, numbers, hyphens and underscores allowed as URL parameters', 'wpv-views'); ?></p>
			<p class="toolset-alert toolset-alert-error js-filter-error js-wpv-param-shortcode-ilegal hidden"><?php echo __('Only lowercase letters and numbers allowed as shortcode attributes', 'wpv-views'); ?></p>
			<p class="toolset-alert toolset-alert-error js-filter-error js-wpv-param-forbidden-wordpress hidden"><?php echo __('This is a word reserved by WordPress', 'wpv-views'); ?></p>
			<p class="toolset-alert toolset-alert-error js-filter-error js-wpv-param-forbidden-toolset hidden"><?php echo __('This is a word reserved by any of the ToolSet plugins', 'wpv-views'); ?></p>
			<p class="toolset-alert toolset-alert-error js-filter-error js-wpv-param-forbidden-post-type hidden"><?php echo __('There is a post type named like that', 'wpv-views'); ?></p>
			<p class="toolset-alert toolset-alert-error js-filter-error js-wpv-param-forbidden-taxonomy hidden"><?php echo __('There is a taxonomy named like that', 'wpv-views'); ?></p>
			<p class="toolset-alert js-filter-info js-wpv-filter-parent-type-not-hierarchical hidden"><?php echo '<i class="icon-warning-sign"></i> ' . __('The posts you want to display are not hierarchical, so this filter will not work', 'wpv-views'); ?></p>
			<p class="toolset-alert js-filter-info js-wpv-filter-taxonomy-parent-changed hidden"><?php echo '<i class="icon-warning-sign"></i> ' . __('The taxonomy you want to display has changed, so this filter needs some action', 'wpv-views'); ?></p>
			<p class="toolset-alert js-filter-info js-wpv-filter-taxonomy-term-changed hidden"><?php echo '<i class="icon-warning-sign"></i> ' . __('The taxonomy you want to display has changed, so this filter needs some action', 'wpv-views'); ?></p>
		<?php
	//	echo '<pre>';print_r($view_settings);echo '</pre>';
		?>
		</div>
	</div>

	<div class="popup-window-container"> <!-- Use this element as a container for all popup windows. This element is hidden. -->

		<div class="wpv-dialog js-filter-add-filter-form-dialog">
			<div class="wpv-dialog-header">
				<h2><?php _e('Add a filter','wpv-views') ?></h2>
				<i class="icon-remove js-dialog-close"></i>
			</div>
			<div class="wpv-dialog-content">

				<strong><?php _e('Select what to filter by:', 'wpv-views'); ?></strong>

				<?php wpv_filters_add_filter_select($view_settings); ?>

			</div>
			<div class="wpv-dialog-footer">
				<button class="button js-dialog-close js-filters-cancel-filter"><?php _e('Cancel','wpv-views') ?></button>
				<button class="button button-primary js-filters-insert-filter" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filters_add_filter_nonce' ); ?>"><?php _e('Add filter','wpv-views') ?></button>
			</div>
		</div>

		<div class="wpv-dialog js-filter-taxonomy-delete-filter-row-dialog">
			<div class="wpv-dialog-header">
				<h2><?php _e('Delete taxonomy filters','wpv-views') ?></h2>
				<i class="icon-remove js-dialog-close"></i>
			</div>
			<div class="wpv-dialog-content">

				<p>
					<strong><?php _e('There are more than one taxonomy filters. What would you like to do?', 'wpv-views'); ?></strong>
				</p>

			</div>
			<div class="wpv-dialog-footer">
				<button class="button js-dialog-close js-filters-cancel-filter"><?php _e('Cancel','wpv-views') ?></button>
				<button class="button button-primary js-filters-taxonomy-delete-filter-row" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_taxonomy_row_delete_nonce' ); ?>"><?php _e('Delete all taxonomy filters','wpv-views') ?></button>
				<p><?php echo sprintf(__('or %sEdit the filter and delete specific taxonomy filters%s', 'wpv-views'), '<a href="#" class="js-filter-taxonomy-edit-filter-row">', '</a>'); ?></p>
			</div>
		</div>

		<div class="wpv-dialog js-filter-custom-field-delete-filter-row-dialog">
			<div class="wpv-dialog-header">
				<h2><?php _e('Delete custom field filters','wpv-views') ?></h2>
				<i class="icon-remove js-dialog-close"></i>
			</div>
			<div class="wpv-dialog-content">
				<p>
					<strong><?php _e('There are more than one custom field filters. What would you like to do?', 'wpv-views'); ?></strong>
				</p>
			</div>
			<div class="wpv-dialog-footer">
				<button class="button js-dialog-close js-filters-cancel-filter"><?php _e('Cancel','wpv-views') ?></button>
				<button class="button button-primary js-filters-custom-field-delete-filter-row" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_custom_field_row_delete_nonce' ); ?>"><?php _e('Delete all custom field filters','wpv-views') ?></button>
				<p><?php echo sprintf(__('or %sEdit the filter and delete specific custom field filters%s', 'wpv-views'), '<a href="#" class="js-filter-custom-field-edit-filter-row">', '</a>'); ?></p>
			</div>
		</div>
		<div class="wpv-dialog js-filter-usermeta-field-delete-filter-row-dialog">
			<div class="wpv-dialog-header">
				<h2><?php _e('Delete usermeta field filters','wpv-views') ?></h2>
				<i class="icon-remove js-dialog-close"></i>
			</div>
			<div class="wpv-dialog-content">
				<p>
					<strong><?php _e('There are more than one usermeta field filters. What would you like to do?', 'wpv-views'); ?></strong>
				</p>
			</div>
			<div class="wpv-dialog-footer">
				<button class="button js-dialog-close js-filters-cancel-filter"><?php _e('Cancel','wpv-views') ?></button>
				<button class="button button-primary js-filters-usermeta-field-delete-filter-row" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_usermeta_field_row_delete_nonce' ); ?>"><?php _e('Delete all usermeta field filters','wpv-views') ?></button>
				<p><?php echo sprintf(__('or %sEdit the filter and delete specific usermeta field filters%s', 'wpv-views'), '<a href="#" class="js-filter-usermeta-field-edit-filter-row">', '</a>'); ?></p>
			</div>
		</div>

	</div>
<?php }

add_action('admin_head', 'wpv_filter_url_check_js');

function wpv_filter_url_check_js() {

	$reserved_list = array(
		'attachment', 'attachment_id', 'author', 'author_name', 'calendar', 'cat', 'category', 'category__and', 'category__in',
		'category__not_in', 'category_name', 'comments_per_page', 'comments_popup', 'customize_messenger_channel',
		'customized', 'cpage', 'day', 'debug', 'error', 'exact', 'feed', 'hour', 'link_category', 'm', 'minute',
		'monthnum', 'more', 'name', 'nav_menu', 'nonce', 'nopaging', 'offset', 'order', 'orderby', 'p', 'page', 'page_id',
		'paged', 'pagename', 'pb', 'perm', 'post', 'post__in', 'post__not_in', 'post_format', 'post_mime_type', 'post_status',
		'post_tag', 'post_type', 'posts', 'posts_per_archive_page', 'posts_per_page', 'preview', 'robots', 's', 'search',
		'second', 'sentence', 'showposts', 'static', 'subpost', 'subpost_id', 'tag', 'tag__and', 'tag__in', 'tag__not_in',
		'tag_id', 'tag_slug__and', 'tag_slug__in', 'taxonomy', 'tb', 'term', 'theme', 'type', 'w', 'withcomments', 'withoutcomments',
		'year '
	);

	$toolset_reserved_words = array(
		'wpv_column_sort_id', 'wpv_column_sort_dir', 'wpv_paged_preload_reach', 'wpv_view_count', 'wpv_filter_submit', 'wpv_post_search'
	);
	$toolset_reserved_words = apply_filters('wpv_toolset_reserved_words', $toolset_reserved_words);

	global $wp_post_types;
    	$reserved_post_types = array_keys( $wp_post_types );

    	$wpv_taxes = get_taxonomies();
    	$reserved_taxonomies = array_keys( $wpv_taxes );

    	$wpv_forbidden_parameters = array(
		'wordpress' => $reserved_list,
		'toolset' => $toolset_reserved_words,
		'post_type' => $reserved_post_types,
		'taxonomy' => $reserved_taxonomies,
    	);

    	$hierarchical_post_names = array();
    	$hierarchical_post_types = get_post_types( array( 'hierarchical' => true ), 'objects');
    	foreach ($hierarchical_post_types as $post_type) {
		$hierarchical_post_names[] = $post_type->name;
    	}

	?>
    <script type="text/javascript">
		var wpv_forbidden_parameters = <?php echo json_encode($wpv_forbidden_parameters); ?>;
		var wpv_hierarchical_post_types = <?php echo json_encode($hierarchical_post_names); ?>;
	</script>
	<?php
}

add_action('wp_ajax_wpv_filters_add_filter_row', 'wpv_filters_add_filter_row_callback');

function wpv_filters_add_filter_row_callback() {
	$nonce = $_POST["wpnonce"];
	if (! wp_verify_nonce($nonce, 'wpv_view_filters_add_filter_nonce') ) die("Security check");
	if ( empty( $_POST['filter_type'] ) ) {
		echo $_POST['id'];
		die();
	}
	$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
	if (!isset($view_array['taxonomy_type']) || empty($view_array['taxonomy_type'])) {
		$view_array['taxonomy_type'] = array('category');
	}
	if (!isset($view_array['roles_type']) || empty($view_array['roles_type'])) {
		$view_array['roles_type'] = array('administrator');
	}
	if (!isset($view_array['post_type']) || empty($view_array['post_type'])) {
		$view_array['post_type'] = array();
	}
	$filters = array();
	$filters = apply_filters('wpv_filters_add_filter', $filters, $view_array['post_type']);
	$filters = apply_filters('wpv_taxonomy_filters_add_filter', $filters, $view_array['taxonomy_type'][0]);
	$filters = apply_filters('wpv_users_filters_add_filter', $filters, $view_array['roles_type'][0]);
	if (isset($filters[$_POST['filter_type']])) {
		if (isset($filters[$_POST['filter_type']]['args'])) {
			call_user_func($filters[$_POST['filter_type']]['callback'], $filters[$_POST['filter_type']]['args']);
		} else {
			call_user_func($filters[$_POST['filter_type']]['callback']);
		}
	}
	die();
}


function give_group_to_field( $filters )
{
	$generics = array( 'post_author', 'post_status', 'post_search', 'post_parent', 'post_relationship', 'post_id' );
	$users_filters = array( 'users_filter', 'usermeta_filter');
	$groups = array();

	foreach( $filters as $type => $filter )
	{
		if( in_array( $type,  $generics) )
		{
			$groups["Post filters"][$type] = $filter;
		}
		else if( $type == 'post_category' || strpos($type, 'tax_input') !== false )
		{

			$groups['Taxonomy'][$type] = $filter;
		}
		else if( strpos($type, 'custom-field-wpcf-') !== false )
		{
				$g = '';
				$nice_name = explode('custom-field-wpcf-', $type);
				$id = ( isset($nice_name[1] ) ) ? $nice_name[1] : $type;
				if( function_exists('wpcf_admin_fields_get_groups_by_field') )
				{
					foreach( wpcf_admin_fields_get_groups_by_field( $id ) as $gs )
					{
						$g = $gs['name'];
					}
				}
				$gr = $g ? $g : "Custom fields";

				$groups[$gr][$type] = $filter;
		}
		else if( strpos($type, 'custom-field-views_woo_') !== false )
		{
			$g = '';
			$nice_name = explode('custom-field-', $type);
	    		$id = ( isset($nice_name[1] ) ) ? $nice_name[1] : $type;
			if( function_exists('wpcf_admin_fields_get_groups_by_field') )
			{
				foreach( wpcf_admin_fields_get_groups_by_field( $id ) as $gs )
				{
					$g = $gs['name'];
				}
			}
			$gr = $g ? $g : "WooCommerce Views filter fields";

			$groups[$gr][$type] = $filter;
		}
        
        else if( strpos($type, 'usermeta-field-basic-') !== false )
        {
            $gr = "Basic fields";
            $groups[$gr][$type] = $filter;
        }
        else if( strpos($type, 'usermeta-field-wpcf-') !== false )
        {
                $g = '';
                $nice_name = explode('usermeta-field-wpcf-', $type);
                $id = ( isset($nice_name[1] ) ) ? $nice_name[1] : $type;
                if( function_exists('wpcf_admin_fields_get_groups_by_field') )
                {
                    foreach( wpcf_admin_fields_get_groups_by_field( $id, 'wp-types-user-group' ) as $gs )
                    {
                        $g = $gs['name'];
                    }
                }
                $gr = $g ? $g : "Users fields";

                $groups[$gr][$type] = $filter;
        }
        else if( strpos($type, 'usermeta-field-') !== false &&  strpos($type, 'usermeta-field-basic-') === false &&  strpos($type, 'usermeta-field-wpcf-') === false )
        {
                $gr = "User fields";
                $groups[$gr][$type] = $filter;
        }
		else if( in_array( $type,  $users_filters) ){
			$groups["Users filters"][$type] = $filter;
		}
		else
		{

			$groups['Custom fields'][$type] = $filter;
		}
	}
	return $groups;
}


function wpv_filters_add_filter_select($view_settings) {
	$filters = array();
	if (!isset($view_settings['post_type'])) $view_settings['post_type'] = array();
	if (!isset($view_settings['taxonomy_type'])) $view_settings['taxonomy_type'] = array('category');
	if (!isset($view_settings['roles_type'])) $view_settings['roles_type'] = array('users');
	if (isset($view_settings['query_type']) && isset($view_settings['query_type'][0])) {
		switch ($view_settings['query_type'][0]) {
			case 'posts':
				$filters = apply_filters('wpv_filters_add_filter', $filters, $view_settings['post_type']);
				break;
			case 'taxonomy':
				$filters = apply_filters('wpv_taxonomy_filters_add_filter', $filters, $view_settings['taxonomy_type'][0]);
				break;
			case 'users':
				$filters = apply_filters('wpv_users_filters_add_filter', $filters, $view_settings['roles_type'][0]);
				break;	
		}
	}
	?>
	
	<select id="filter-add-select" class="js-filter-add-select">
	<option value="-1"><?php echo __('--- Please select ---', 'wpv-views'); ?></option>
	<?php

	foreach( give_group_to_field( $filters ) as $group => $f )
	{
		if( $f && !empty( $f ) ):
		?>
		<optgroup label="<?php echo $group?>">
		<?php
		foreach($f as $type => $filter) {
			if (!isset($view_settings[$filter['present']])) {
				?>
				<option value="<?php echo $type; ?>"><?php echo $filter['name']; ?></option>

				<?php
			}
		}
		?>
		</optgroup>
		<?php
		endif;
	}
	?>
	</select>
<?php }

add_action('wp_ajax_wpv_filters_upate_filters_select', 'wpv_filters_upate_filters_select_callback');

function wpv_filters_upate_filters_select_callback() {
	$nonce = $_POST["wpnonce"];
	if (! wp_verify_nonce($nonce, 'wpv_view_filter_add_filter') ) die("Security check");
	$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
	wpv_filters_add_filter_select($view_array);
	die();
}

add_action('wp_ajax_wpv_filter_update_filters_list', 'wpv_filter_update_filters_list_callback');

function wpv_filter_update_filters_list_callback() {
	$nonce = $_POST["nonce"];
	if (! wp_verify_nonce($nonce, 'wpv_view_filter_update_filters_list_nonce') ) die("Security check");
	$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
	$return_result = array();
	// Filters list
	$filters_list = '';
	ob_start();
	wpv_display_filters_list( $view_array['query_type'][0], $view_array );
	$filters_list = ob_get_contents();
	ob_end_clean();
	$return_result['wpv_filter_update_filters_list'] = $filters_list;
	// Now, the dependent parametric search structure
	$dps_structure = '';
	ob_start();
	wpv_dps_settings_structure( $view_array, $_POST["id"] );
	$dps_structure = ob_get_contents();
	ob_end_clean();
	$return_result['wpv_dps_settings_structure'] = $dps_structure;
	$return_result['success'] = $_POST['id'];
	echo json_encode( $return_result );
	die();
}

add_action('wp_ajax_wpv_filter_make_intersection_filters', 'wpv_filter_make_intersection_filters');

function wpv_filter_make_intersection_filters() { // TODO this is undone still
	$nonce = $_POST["nonce"];
	if (! wp_verify_nonce( $nonce, 'wpv_view_make_intersection_filters' ) ) die( "Security check" );
	$view_array = get_post_meta( $_POST["id"], '_wpv_settings', true );
	$view_array['taxonomy_relationship'] = 'AND';
	$view_array['custom_fields_relationship'] = 'AND';
	$view_array['usermeta_fields_relationship'] = 'AND';
	update_post_meta( $_POST["id"], '_wpv_settings', $view_array );
//	wpv_display_filters_list( $_POST["query_type"], $view_array );
	$return_result = array();
	// Filters list
	$filters_list = '';
	ob_start();
	wpv_display_filters_list( $view_array['query_type'][0], $view_array );
	$filters_list = ob_get_contents();
	ob_end_clean();
	$return_result['wpv_filter_update_filters_list'] = $filters_list;
	// Now, the dependent parametric search structure
	$dps_structure = '';
	ob_start();
	wpv_dps_settings_structure( $view_array, $_POST["id"] );
	$dps_structure = ob_get_contents();
	ob_end_clean();
	$return_result['wpv_dps_settings_structure'] = $dps_structure;
	$return_result['success'] = $_POST['id'];
	echo json_encode( $return_result );
	die();
}

function wpv_display_filters_list( $query_type, $view_settings ) {
	switch ( $query_type ) {
		case 'posts':
			do_action('wpv_add_filter_list_item', $view_settings);
			break;
		case 'taxonomy':
			do_action('wpv_add_taxonomy_filter_list_item', $view_settings);
			break;
		case 'users':
			do_action('wpv_add_users_filter_list_item', $view_settings);
			break;
	}
}