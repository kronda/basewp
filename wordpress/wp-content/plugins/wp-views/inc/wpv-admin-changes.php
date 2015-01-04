<?php


function wpv_manage_views_columns($columns) { // DEPRECATED
	$columns['wpv_query'] = __('Content to load', 'wpv-views');
	$columns['wpv_filter'] = __('Filter', 'wpv-views');
	$columns['wpv_display'] = __('Display', 'wpv-views');

	return $columns;
}

add_filter('manage_edit-view_columns', 'wpv_manage_views_columns');


function wpv_manage_view_templates_columns($columns) {
	$columns['wpv_fields'] = __('Fields used', 'wpv-views');
	$columns['wpv_default'] = __('How this Content Template is used', 'wpv-views');

	return $columns;
}

add_filter('manage_edit-view-template_columns', 'wpv_manage_view_templates_columns');

function wpv_manage_views_table_row($column_name, $post_id) {
	static $quick_edit_removed = false;

	$wpv_options = get_option('wpv_options');

	switch($column_name) {
		case 'wpv_query': // DEPRECATED
			$summary = wpv_create_content_summary_for_listing($post_id);
			echo $summary;
			break;

		case 'wpv_filter': // DEPRECATED
			//$wpv_layout_settings = get_post_meta($post_id, '_wpv_layout_settings', true);
			// var_dump($wpv_layout_settings);
			$summary = wpv_create_summary_for_listing($post_id);
			echo $summary; break;
		case 'wpv_display': // DEPRECATED
			$wpv_layout_settings = get_post_meta($post_id, '_wpv_layout_settings', true);
			echo wpv_get_layout_label_by_slug($wpv_layout_settings) ; break;
		case 'wpv_fields':
			echo wpv_get_view_template_fields_list($post_id);
			break;
		case 'wpv_default':
			echo wpv_get_view_template_defaults($wpv_options, $post_id);
			break;
		default:
	}

	switch($column_name) { // DEPRECATED
		case 'wpv_query':
		case 'wpv_fields':

			// Let's disable the quick edit at this point as well.
			// This should probaby be done in a JS file but I don't think
			// we have one for the Views list page.
			if (!$quick_edit_removed) {
				?>
					<script type="text/javascript">
						jQuery(document).ready(function(){
							jQuery('.editinline').parent().hide();
						});

					</script>
				<?php

				$quick_edit_removed = true;
			}
	}

}

add_filter('manage_posts_custom_column', 'wpv_manage_views_table_row', 10, 2);

/**
 *
 * Helper function returning the layout label by slug
 * @param label $wpv_layout_settings
 */
function wpv_get_layout_label_by_slug($wpv_layout_settings) { // DEPRECATED test because Victor can be using something from here
	$style = isset($wpv_layout_settings['style']) ? $wpv_layout_settings['style'] : 'unformatted';

	$layout_box = '<div class="view_layout_box"><table><tr><td colspan="2" style="border-bottom-color:#FFFFFF;margin-left:10px;">';
	switch($style) {
		case 'unformatted': $layout_box .= __('Unformatted', 'wpv-views'); break;
		case 'ordered_list': $layout_box .= __('Ordered List', 'wpv-views'); break;
		case 'un_ordered_list': $layout_box .= __('Unordered List', 'wpv-views'); break;
		case 'table': $layout_box .= __('Grid', 'wpv-views'); break;
		case 'table_of_fields': $layout_box .= __('Table', 'wpv-views'); break;
	}

	$layout_box .= '</td></tr><tr><td width="34" style="border-bottom-color:#FFFFFF;">';
	switch($style) {
		case 'unformatted': $layout_box .= '<img src="' . WPV_URL . '/res/img/layout-unformated-48.png" />&nbsp;'; break;
		case 'ordered_list': $layout_box .= '<img src="' . WPV_URL . '/res/img/layout-ordered-list-48.png" />&nbsp;'; break;
		case 'un_ordered_list': $layout_box .= '<img src="' . WPV_URL . '/res/img/layout-un-ordered-list-48.png" />&nbsp;'; break;
		case 'table': $layout_box .= '<img src="' . WPV_URL . '/res/img/layout-grid-48.png" />&nbsp;'; break;
		case 'table_of_fields': $layout_box .= '<img align="left" src="' . WPV_URL . '/res/img/layout-table-48.png" />&nbsp;'; break;
	}

	$layout_box .= '</td><td style="border-bottom-color:#FFFFFF;">';

	if (isset($wpv_layout_settings['layout_meta_html'])) {
		$shortcode_expression = "/\\[(wpv-post|wpv-tax|types).*?\\]/i";

		// search for shortcodes
		$counts = preg_match_all($shortcode_expression, $wpv_layout_settings['layout_meta_html'], $matches);

		// iterate 0-level shortcode elements
		if($counts > 0) {
			$added = array();
			foreach($matches[0] as $match) {
				if (sizeof($added)) {
					$layout_box .= '<br />';
				}
				if (!in_array($match, $added)) {
					$layout_box .= $match;
					$added[] = $match;
				}

			}
		}
	}

	$layout_box .= '</td></tr></table>';
	$layout_box .= '</div>';
	return $layout_box;
}

function wpv_get_view_template_defaults($wpv_options, $post_id) { // TODO check where is this used, maybe DEPRECATED

	$result = '';

	if ($wpv_options) {
		foreach($wpv_options as $option=>$value) {
			if($value == $post_id) {
				if(strpos($option, 'views_template_for_') !== false) {
					$term = substr($option, 19);
					$result .= __('Single view for ', 'wpv-views'). '<span class="view_template_term">' . $term .  '</span>. <br />';
				} else if(strpos($option, 'views_template_archive_for_') !== false) {
					$term = substr($option, 27);
					$result .= __('Archive view for ', 'wpv-views'). '<span class="view_template_term">' . $term .  '</span>. <br />';
				} else if(strpos($option, 'views_template_loop_') !== false) {
					$term = substr($option, 20);
					$result .= __('Loop view for ', 'wpv-views'). '<span class="view_template_term">' . $term .  '</span>. <br />';
				}
			}
		}
	}

	if ($result != '') {
		$result = '<div class="view_template_default_box">' . $result . '</div>';
	}

	return $result;
}

function wpv_create_content_summary_for_listing($post_id) { // TODO check if e want to use this or next filter, DEPRECATED for the other NOTE ModuleManager is using one

	$view_settings = get_post_meta($post_id, '_wpv_settings', true);

	if (!isset($view_settings['view-query-mode'])) {
		$view_settings['view-query-mode'] = 'normal';
	}
	
	$summary = '';

	switch ($view_settings['view-query-mode']) {
		case 'normal':
			$summary .= apply_filters('wpv-view-get-content-summary', '', $post_id, $view_settings);
			break;

		case 'archive':
			$summary .= __('This View displays results for an', 'wpv-views') . ' <strong>' . __('existing WordPress query', 'wpv-views') . '</strong>';
			break;
	}

	return $summary;
}

function wpv_create_summary_for_listing($post_id) { // TODO check if e want to use this or previous filter, DEPRECATED for the other NOTE ModuleManager is using one

	$view_settings = get_post_meta($post_id, '_wpv_settings', true);

	$filter_summary = apply_filters('wpv-view-get-summary', '', $post_id, $view_settings);
	$summary = '';

	if ($filter_summary == '') {
		$filter_summary = __('No filters selected.', 'wpv-views');
	}
	//$filter_summary = str_replace('<strong>', '', $filter_summary);
	//$filter_summary = str_replace('</strong>', '', $filter_summary);

	$summary .= $filter_summary;

	return $summary;
}

function wpv_get_view_template_fields_list($post_id) { // DEPRECATED Victor could be using something from here
	$view_template_fields = get_post_meta($post_id, '_wpv_view_template_fields', true);
	if(empty($view_template_fields)) {
		wpv_view_template_update_field_values($post_id);
		$view_template_fields = get_post_meta($post_id, '_wpv_view_template_fields', true);
	}

	$view_template_fields = unserialize($view_template_fields);

	$fields_list = '<div class="view_template_fields_box">';
	if(is_array($view_template_fields)) {
		foreach($view_template_fields as $field) {
			$fields_list .=  $field . '<br />';
		}
	}

	$fields_list .= '</div>';

	return $fields_list;

}

add_filter('admin_init', 'wpv_remove_unnecessary_columns');

function wpv_remove_unnecessary_columns() {
    add_filter( 'manage_edit-view_columns', 'wpv_view_columns_filter', 10, 1 ); // DEPRECATED
    add_filter( 'manage_edit-view-template_columns', 'wpv_view_template_columns_filter', 10, 1 );
}

function wpv_view_columns_filter( $columns ) { // DEPRECATED
    unset($columns['author']);
	unset($columns['date']);
    return $columns;
}

function wpv_view_template_columns_filter( $columns ) {
	unset($columns['author']);
	unset($columns['date']);
    return $columns;
}

function views_redesign_function() { // function placeholder while the admin pages are designed // DEPRECATED ?>
	<div class="wrap">
	<div id="icon-edit" class="icon32 icon32-posts-view"><br /></div>
	<h2>this is a test page</h2>
	</div>
<?php }

// highlight the proper top level menu - needed when embedding WordPress custom post types edit pages in custom menus
function wpv_view_templates_menu_fix($parent_file) { // TODO test deprecating this
	global $current_screen;
	if ('view-template' == $current_screen->post_type) $parent_file = 'views';
	return $parent_file;
}
add_action('parent_file', 'wpv_view_templates_menu_fix');

