<?php

if(is_admin()){
	
	/**
	* Add the parent filter to the list and to the popup select
	*/
	
	add_action('wpv_add_filter_list_item', 'wpv_add_filter_parent_list_item', 1, 1);
	add_filter('wpv_filters_add_filter', 'wpv_filters_add_filter_parent', 1,1);

	function wpv_filters_add_filter_parent($filters) {
		$filters['post_parent'] = array('name' => __('Post parent', 'wpv-views'),
						'present' => 'parent_mode',
						'callback' => 'wpv_add_new_filter_parent_list_item'
						);

		return $filters;
	}
	
	/**
	* Create parent filter callback
	*/

	function wpv_add_new_filter_parent_list_item() {
		$args = array(
			'parent_mode' => array('current_page')
		);
		wpv_add_filter_parent_list_item($args);
	}
	
	/**
	* Render parent filter item in the filters list
	*/

	function wpv_add_filter_parent_list_item($view_settings) {
		if (isset($view_settings['parent_mode'][0])) {
			$li = wpv_get_list_item_ui_post_parent(null, $view_settings);
			echo '<li id="js-row-post_parent" class="js-filter-row js-filter-row-simple js-filter-for-posts js-filter-parent js-filter-row-post_parent">' . $li . '</li>';
		}
	}
	
	/**
	* Render parent filter item content in the filters list
	*/

	function wpv_get_list_item_ui_post_parent( $selected, $view_settings = null ) {
		global $wpdb;
		if ( isset( $view_settings['parent_mode'] ) && is_array( $view_settings['parent_mode'] ) ) {
			$view_settings['parent_mode'] = $view_settings['parent_mode'][0];
		}
		
		if ( function_exists('icl_object_id') && isset( $view_settings['parent_id'] ) && !empty( $view_settings['parent_id'] ) ) {
			// Adjust for WPML support
			$target_post_type = $wpdb->get_var("SELECT post_type FROM {$wpdb->posts} WHERE ID='{$view_settings['parent_id']}'");
			if ( $target_post_type ) {
				$view_settings['parent_id'] = icl_object_id( $view_settings['parent_id'], $target_post_type, true );
			}
		}
		
		ob_start();
		?>
		<p class='wpv-filter-parent-edit-summary js-wpv-filter-summary js-wpv-filter-parent-summary'>
			<?php echo wpv_get_filter_parent_summary_txt( $view_settings ); ?>
		</p>
		<p class='edit-filter js-wpv-filter-edit-controls'>
			<i class='button-secondary icon-edit icon-large js-wpv-filter-edit-open js-wpv-filter-parent-edit-open' title='<?php echo esc_attr( __('Edit this filter','wpv-views') ); ?>'></i>
			<i class='button-secondary icon-trash icon-large js-filter-remove' title='<?php echo esc_attr( __('Delete this filter','wpv-views') ); ?>' data-nonce='<?php echo wp_create_nonce( 'wpv_view_filter_parent_delete_nonce' ); ?>'></i>
		</p>
		<div id="wpv-filter-parent-edit" class="wpv-filter-edit js-wpv-filter-edit">
			<fieldset>
				<p><strong><?php echo __('Post parent', 'wpv-views'); ?>:</strong></p>
				<div id="wpv-filter-parent" class="js-filter-parent-list">
					<?php wpv_render_parent_options( array( 'view_settings' => $view_settings ) ); ?>
				</div>
			</fieldset>
			<p>
				<input class="button-secondary js-wpv-filter-edit-ok js-wpv-filter-parent-edit-ok" type="button" value="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-save="<?php echo htmlentities( __('Save', 'wpv-views'), ENT_QUOTES ); ?>" data-close="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-success="<?php echo htmlentities( __('Updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_parent_nonce' ); ?>" />
			</p>
			<!-- <p>
				<?php echo sprintf(__('%sLearn about filtering by Post Parent%s', 'wpv-views'),
					'<a class="wpv-help-link" href="' . WPV_FILTER_BY_POST_PARENT_LINK . '" target="_blank">',
					' &raquo;</a>'
				); ?>
			</p> -->
		</div>
		<?php
		$res = ob_get_clean();
		return $res;
		/*
		ob_start();
		wpv_render_parent_options(array('view_settings' => $view_settings));
		$data = ob_get_clean();

		$td = "<p class='wpv-filter-parent-edit-summary js-wpv-filter-summary js-wpv-filter-parent-summary'>\n";
		$td .= wpv_get_filter_parent_summary_txt($view_settings);
		$td .= "</p>\n<p class='edit-filter js-wpv-filter-edit-controls'>\n<i class='button-secondary icon-edit icon-large js-wpv-filter-edit-open js-wpv-filter-parent-edit-open' title='". __('Edit','wpv-views') ."'></i>\n<i class='button-secondary icon-trash icon-large js-filter-remove' title='". __('Delete this filter','wpv-views') ."' data-nonce='". wp_create_nonce( 'wpv_view_filter_parent_delete_nonce' ) . "'></i>\n</p>";
		$td .= "<div id=\"wpv-filter-parent-edit\" class=\"wpv-filter-edit js-wpv-filter-edit\">\n";
		$td .= '<fieldset>';
		$td .= '<p><strong>' . __('Post parent', 'wpv-views') . ':</strong></p>';
		$td .= '<div id="wpv-filter-parent" class="js-filter-parent-list">' . $data . '</div>';
		$td .= '</fieldset>';
		ob_start();
		?>
		<p>
			<input class="button-secondary js-wpv-filter-edit-ok js-wpv-filter-parent-edit-ok" type="button" value="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-save="<?php echo htmlentities( __('Save', 'wpv-views'), ENT_QUOTES ); ?>" data-close="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-success="<?php echo htmlentities( __('Updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_parent_nonce' ); ?>" />
		</p>
		<p class="wpv-custom-fields-help">
                        <?php echo sprintf(__('%sLearn about filtering by Post Parent%s', 'wpv-views'),
                                        '<a class="wpv-help-link" href="' . WPV_FILTER_BY_POST_PARENT_LINK . '" target="_blank">',
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
	* Update parent list select when changing the parent post type callback
	*/


	add_action('wp_ajax_wpv_get_parent_post_select', 'wpv_get_parent_post_select_callback');

	function wpv_get_parent_post_select_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_parent_post_type_nonce') ) die("Security check");
		wpv_show_posts_dropdown($_POST['post_type'], 'parent_id');
		die();
	}
	
	/**
	* Update parent filter callback
	*/

	add_action('wp_ajax_wpv_filter_parent_update', 'wpv_filter_parent_update_callback');

	function wpv_filter_parent_update_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_parent_nonce') ) die("Security check");
		if ( empty( $_POST['parent_mode'] ) ) {
			echo $_POST['id'];
			die();
		}
		$change = false;
		$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
		if (!isset($_POST['parent_id'])) $_POST['parent_id'] = 0;
		if ( !isset( $view_array['parent_mode'] ) || array( $_POST['parent_mode'] ) != $view_array['parent_mode'] ) {
			$change = true;
			$view_array['parent_mode'] = array( $_POST['parent_mode'] );
		}
		if ( !isset( $view_array['parent_id'] ) || $_POST['parent_id'] != $view_array['parent_id'] ) {
			$change = true;
			$view_array['parent_id'] = $_POST['parent_id'];
		}
		if ( $change ) {
			$result = update_post_meta($_POST["id"], '_wpv_settings', $view_array);
		}
		echo wpv_get_filter_parent_summary_txt(
			array(
				'parent_mode'	=> $_POST['parent_mode'],
				'parent_id'	=> $_POST['parent_id']
			)
		);
		die();
	}
	
	/**
	* Update parent filter summary callback
	*/

	// TODO This might not be needed here, maybe for summary filter
	add_action('wp_ajax_wpv_filter_parent_sumary_update', 'wpv_filter_parent_sumary_update_callback');

	function wpv_filter_parent_sumary_update_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_parent_nonce') ) die("Security check");
		if (!isset($_POST['parent_id'])) $_POST['parent_id'] = 0;
		echo wpv_get_filter_parent_summary_txt(
			array(
				'parent_mode'	=> $_POST['parent_mode'],
				'parent_id'	=> $_POST['parent_id']
			)
		);
		die();
	}
	
	/**
	* Delete parent filter callback
	*/

	add_action('wp_ajax_wpv_filter_post_parent_delete', 'wpv_filter_parent_delete_callback');

	function wpv_filter_parent_delete_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_parent_delete_nonce') ) die("Security check");
		$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
		if ( isset( $view_array['parent_mode'] ) ) {
			unset( $view_array['parent_mode'] );
		}
		if ( isset( $view_array['parent_id'] ) ) {
			unset( $view_array['parent_id'] );
		}
		update_post_meta($_POST["id"], '_wpv_settings', $view_array);
		echo $_POST['id'];
		die();

	}
	
	/**
	* Add the taxonomy parent filter to the list and to the popup select
	*/

	add_action('wpv_add_taxonomy_filter_list_item', 'wpv_add_filter_taxonomy_parent_list_item', 1, 1);
	add_filter('wpv_taxonomy_filters_add_filter', 'wpv_filters_add_filter_parent_taxonomy', 1, 2);

	function wpv_filters_add_filter_parent_taxonomy($filters, $taxonomy_type) {
		$filters['taxonomy_parent'] = array('name' => __('Taxonomy parent', 'wpv-views'),
						'present' => 'taxonomy_parent_mode',
						'callback' => 'wpv_add_new_filter_taxonomy_parent_list_item',
						'args' => $taxonomy_type
						);

		return $filters;
	}
	
	/**
	* Create taxonomy parent filter callback
	*/

	function wpv_add_new_filter_taxonomy_parent_list_item($taxonomy_type) {
		$args = array(
			'taxonomy_parent_mode' => array('current_view'),
			'taxonomy_type' => $taxonomy_type
		);
		wpv_add_filter_taxonomy_parent_list_item($args);
	}
	
	/**
	* Render taxonomy parent filter item in the filters list
	*/

	function wpv_add_filter_taxonomy_parent_list_item($view_settings) {
		if (isset($view_settings['taxonomy_parent_mode'][0]) && $view_settings['taxonomy_parent_mode'][0] != '') {
			$li = wpv_get_list_item_ui_taxonomy_parent(null, $view_settings);
			echo '<li id="js-row-taxonomy_parent" class="js-filter-row js-filter-row-simple js-filter-for-taxonomies js-filter-taxonomy-parent js-filter-row-taxonomy_parent">' . $li . '</li>';
		}
	}
	
	/**
	* Render taxonomy parent filter item content in the filters list
	*/

	function wpv_get_list_item_ui_taxonomy_parent($selected, $view_settings = null) {
		if (isset($view_settings['taxonomy_parent_mode']) && is_array($view_settings['taxonomy_parent_mode'])) {
			$view_settings['taxonomy_parent_mode'] = $view_settings['taxonomy_parent_mode'][0];
		}
		if (isset($view_settings['taxonomy_type']) && is_array($view_settings['taxonomy_type']) && sizeof($view_settings['taxonomy_type']) > 0 ) {
			$view_settings['taxonomy_type'] = $view_settings['taxonomy_type'][0];
		}
		
		if ( function_exists('icl_object_id') && isset( $view_settings['taxonomy_type'] ) && isset( $view_settings['taxonomy_parent_id'] ) && !empty( $view_settings['taxonomy_parent_id'] ) ) {
			// Adjust for WPML support
			$view_settings['taxonomy_parent_id'] = icl_object_id( $view_settings['taxonomy_parent_id'], $view_settings['taxonomy_type'], true );
		}
		
		ob_start();
		?>
		<p class='wpv-filter-taxonomy-parent-edit-summary js-wpv-filter-summary js-wpv-filter-taxonomy-parent-summary'>
			<?php echo wpv_get_filter_taxonomy_parent_summary_txt( $view_settings ); ?>
		</p>
		<p class='edit-filter js-wpv-filter-edit-controls'>
			<i class='button-secondary icon-edit icon-large js-wpv-filter-edit-open js-wpv-filter-taxonomy-parent-edit-open' title='<?php echo esc_attr( __('Edit this filter','wpv-views')); ?>'></i>
			<i class='button-secondary icon-trash icon-large js-filter-remove' title='<?php echo esc_attr( __('Delete this filter','wpv-views') ); ?>' data-nonce='<?php echo wp_create_nonce( 'wpv_view_filter_taxonomy_parent_delete_nonce' ); ?>'></i>
		</p>
		<div id="wpv-filter-taxonomy-parent-edit" class="wpv-filter-edit js-wpv-filter-edit">
			<fieldset>
				<legend><strong><?php echo __('Taxonomy parent', 'wpv-views'); ?>:</strong></legend>
				<div id="wpv-filter-taxonomy-parent" class="js-filter-taxonomy-parent-list">
					<?php wpv_render_taxonomy_parent_options( array( 'view_settings' => $view_settings ) ); ?>
				</div>
			</fieldset>
			<p>
				<input class="button-secondary js-wpv-filter-edit-ok js-wpv-filter-taxonomy-parent-edit-ok" type="button" value="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-save="<?php echo htmlentities( __('Save', 'wpv-views'), ENT_QUOTES ); ?>" data-close="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-success="<?php echo htmlentities( __('Updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_taxonomy_parent_nonce' ); ?>" />
			</p>
		</div>
		<?php
		$res = ob_get_clean();
		return $res;
		/*
		ob_start();
		wpv_render_taxonomy_parent_options(array('view_settings' => $view_settings));
		$data = ob_get_clean();

		$td = "<p class='wpv-filter-taxonomy-parent-edit-summary js-wpv-filter-summary js-wpv-filter-taxonomy-parent-summary'>\n";
		$td .= wpv_get_filter_taxonomy_parent_summary_txt($view_settings);
		$td .= "</p>\n<p class='edit-filter js-wpv-filter-edit-controls'>\n<i class='button-secondary icon-edit icon-large js-wpv-filter-edit-open js-wpv-filter-taxonomy-parent-edit-open' title='". __('Edit','wpv-views') ."'></i>\n<i class='button-secondary icon-trash icon-large js-filter-remove' title='". __('Delete this filter','wpv-views') ."' data-nonce='". wp_create_nonce( 'wpv_view_filter_taxonomy_parent_delete_nonce' ) . "'></i>\n</p>";
		$td .= "<div id=\"wpv-filter-taxonomy-parent-edit\" class=\"wpv-filter-edit js-wpv-filter-edit\">\n";
		$td .= '<fieldset>';
		$td .= '<legend><strong>' . __('Taxonomy parent', 'wpv-views') . ':</strong></legend>';
		$td .= '<div id="wpv-filter-taxonomy-parent" class="js-filter-taxonomy-parent-list">' . $data . '</div>';
		$td .= '</fieldset>';
		ob_start();
		?>
		<p>
			<input class="button-secondary js-wpv-filter-edit-ok js-wpv-filter-taxonomy-parent-edit-ok" type="button" value="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-save="<?php echo htmlentities( __('Save', 'wpv-views'), ENT_QUOTES ); ?>" data-close="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-success="<?php echo htmlentities( __('Updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_taxonomy_parent_nonce' ); ?>" />
		</p>
		<?php
		$td .= ob_get_clean();
		$td .= '</div>';

		return $td;
		*/
	}
	
	/**
	* Check that chosen term belongs to taxonomy when updating taxonomy in Content selection callback
	*/

	add_action('wp_ajax_wpv_filter_taxonomy_parent_test', 'wpv_filter_taxonomy_parent_test_callback');

	function wpv_filter_taxonomy_parent_test_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_taxonomy_parent_nonce') ) die("Security check");
		if ($_POST['tax_parent_id'] == '0') {
			echo $_POST['tax_parent_id'];
		} else {
			echo wpv_get_tax_relationship_test($_POST['tax_parent_id'], $_POST['tax_type']);
		}
		die();
	}
	
	/**
	* Update taxonomy parent filter callback
	*/

	add_action('wp_ajax_wpv_filter_taxonomy_parent_update', 'wpv_filter_taxonomy_parent_update_callback');

	function wpv_filter_taxonomy_parent_update_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_taxonomy_parent_nonce') ) die("Security check");
		if ( empty( $_POST['tax_parent_mode'] ) ) {
			echo $_POST['id'];
			die();
		}
		$change = false;
		$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
		if (!isset($_POST['tax_parent_id'])) $_POST['tax_parent_id'] = 0;
		if ( !isset( $view_array['taxonomy_parent_mode'] ) || array( $_POST['tax_parent_mode'] ) != $view_array['taxonomy_parent_mode'] ) {
			$change = true;
			$view_array['taxonomy_parent_mode'] = array($_POST['tax_parent_mode']);
		}
		if ( !isset( $view_array['taxonomy_parent_id'] ) || $_POST['tax_parent_id'] != $view_array['taxonomy_parent_id'] ) {
			$change = true;
			$view_array['taxonomy_parent_id'] = $_POST['tax_parent_id'];
		}
		if ( $change ) {
			$result = update_post_meta($_POST["id"], '_wpv_settings', $view_array);
		}
		echo wpv_get_filter_taxonomy_parent_summary_txt(
			array(
				'taxonomy_parent_mode'	=> $_POST['tax_parent_mode'],
				'taxonomy_parent_id'	=> $_POST['tax_parent_id'],
				'taxonomy_type'		=> $_POST['tax_type']
			)
		);
		die();
	}
	
	/**
	* Update taxonomy parent filter summary callback
	*/

	add_action('wp_ajax_wpv_filter_taxonomy_parent_sumary_update', 'wpv_filter_taxonomy_parent_sumary_update_callback');

	// TODO This might not be needed here, maybe for summary filter
	function wpv_filter_taxonomy_parent_sumary_update_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_taxonomy_parent_nonce') ) die("Security check");
		if (!isset($_POST['tax_parent_id'])) $_POST['tax_parent_id'] = 0;
		echo wpv_get_filter_taxonomy_parent_summary_txt(
			array(
				'taxonomy_parent_mode'	=> $_POST['tax_parent_mode'],
				'taxonomy_parent_id'	=> $_POST['tax_parent_id'],
				'taxonomy_type'		=> $_POST['tax_type']
			)
		);
		die();
	}
	
	/**
	* Delete taxonomy parent filter callback
	*/

	add_action('wp_ajax_wpv_filter_taxonomy_parent_delete', 'wpv_filter_taxonomy_parent_delete_callback');

	function wpv_filter_taxonomy_parent_delete_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_taxonomy_parent_delete_nonce') ) die("Security check");
		$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
		if ( isset( $view_array['taxonomy_parent_mode'] ) ) {
			unset( $view_array['taxonomy_parent_mode'] );
		}
		if ( isset( $view_array['taxonomy_parent_id'] ) ) {
			unset( $view_array['taxonomy_parent_id'] );
		}
		update_post_meta($_POST["id"], '_wpv_settings', $view_array);
		echo $_POST['id'];
		die();

	}
	
	/**
	* Add a filter to show the filter on the summary
	* NOTE we may need something like that for taxonomy parent
	*/
    
	add_filter('wpv-view-get-summary', 'wpv_parent_summary_filter', 5, 3);

	function wpv_parent_summary_filter($summary, $post_id, $view_settings) {
		if(isset($view_settings['query_type']) && $view_settings['query_type'][0] == 'posts' && isset($view_settings['parent_mode'][0])) {
		$view_settings['parent_mode'] = $view_settings['parent_mode'][0];
			$result = wpv_get_filter_parent_summary_txt($view_settings, true);
			if ($result != '' && $summary != '') {
				$summary .= '<br />';
			}
			$summary .= $result;
		}
		return $summary;
	}

}

/**
* Render parent filter options
*/

function wpv_render_parent_options($args) {

    global $wpdb;

    $view_settings = isset($args['view_settings']) ? $args['view_settings'] : array();

    $defaults = array('parent_mode' => 'current_page',
                      'parent_id' => 0);
    $view_settings = wp_parse_args($view_settings, $defaults);

	?>
        <ul>
            <li>
                <?php $checked = $view_settings['parent_mode'] == 'current_page' ? 'checked="checked"' : ''; ?>
                <label><input type="radio" class="js-parent-mode" name="parent_mode[]" value="current_page" <?php echo $checked; ?> />&nbsp;<?php _e('Parent is the current page', 'wpv-views'); ?></label>
            </li>

            <li>
                <?php $checked = $view_settings['parent_mode'] == 'this_page' ? 'checked="checked"' : ''; ?>
                <label><input type="radio" class="js-parent-mode" name="parent_mode[]" value="this_page" <?php echo $checked; ?> />&nbsp;<?php _e('Parent is:', 'wpv-views'); ?></label>

                <select id="wpv_parent_post_type" class="js-parent-post-type" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_parent_post_type_nonce' ); ?>">
                <?php
                    $hierarchical_post_types = get_post_types( array( 'hierarchical' => true ), 'objects');
                    if ($view_settings['parent_id'] == 0) {
                        $selected_type = 'page';
                    } else {
                        $selected_type = $wpdb->get_var($wpdb->prepare("
                                SELECT post_type FROM {$wpdb->prefix}posts WHERE ID=%d", $view_settings['parent_id']));
                        if (!$selected_type) {
                            $selected_type = 'page';
                        }
                    }
                    foreach ($hierarchical_post_types as $post_type) {
                        $selected = $selected_type == $post_type->name ? ' selected="selected"' : '';
                        echo '<option value="' . $post_type->name . '"' . $selected . '>' . $post_type->labels->singular_name . '</option>';
                    }
                ?>
                </select>

                <?php wp_dropdown_pages(array('name'=>'parent_id', 'selected'=>$view_settings['parent_id'], 'post_type'=> $selected_type, 'show_option_none' => __('None', 'wpv-views'))); ?>

            </li>
        </ul>
	<?php
}

/**
* Render parent filter summary text
*/

function wpv_get_filter_parent_summary_txt($view_settings, $short = false) {
	global $wpdb;

	ob_start();

	if ($view_settings['parent_mode'] == 'current_page') {
		if ($short) {
			_e('parent is the <strong>current page</strong>', 'wpv-views');
		} else {
			_e('Select posts whose parent is the <strong>current page</strong>.', 'wpv-views');
		}
	} else {
		if (isset($view_settings['parent_id']) && $view_settings['parent_id'] > 0) {
			$selected_title = $wpdb->get_var($wpdb->prepare("
			SELECT post_title FROM {$wpdb->prefix}posts WHERE ID=%d", $view_settings['parent_id']));
		} else {
			$selected_title = 'None';
		}
		if ($short) {
			echo sprintf(__('parent is <strong>%s</strong>', 'wpv-views'), $selected_title);
				} else {
			echo sprintf(__('Select posts whose parent is <strong>%s</strong>.', 'wpv-views'), $selected_title);
		}
	}

	$data = ob_get_clean();

	return $data;
}

/**
* Render taxonomy parent filter options
*/

function wpv_render_taxonomy_parent_options($args) {

    global $wpdb;

    $view_settings = isset($args['view_settings']) ? $args['view_settings'] : array();

    $defaults = array('taxonomy_parent_mode' => 'current_view',
                      'taxonomy_parent_id' => 0);
    $view_settings = wp_parse_args($view_settings, $defaults);

	?>

        <ul>
            <li>
                <?php $checked = $view_settings['taxonomy_parent_mode'] == 'current_view' ? 'checked="checked"' : ''; ?>
                <label><input type="radio" class="js-taxonomy-parent-mode"  name="taxonomy_parent_mode[]" value="current_view" <?php echo $checked; ?> />&nbsp;<?php _e('Parent is the taxonomy selected by the <strong>parent view</strong>', 'wpv-views'); ?></label>
            </li>

            <li>
                <?php $checked = $view_settings['taxonomy_parent_mode'] == 'this_parent' ? 'checked="checked"' : ''; ?>
                <label><input type="radio" class="js-taxonomy-parent-mode"  name="taxonomy_parent_mode[]" value="this_parent" <?php echo $checked; ?> />&nbsp;<?php _e('Parent is:', 'wpv-views'); ?></label>

				<?php
					if (isset($view_settings['taxonomy_type']) && $view_settings['taxonomy_type'] != '') {
						$taxonomy = $view_settings['taxonomy_type'];
					} else {
						$taxonomy = 'category';
					}

				?>
				<input type="hidden" id="wpv-current-taxonomy-parent" value="<?php echo $taxonomy; ?>" />

				<select name="wpv_taxonomy_parent_id" class="js-taxonomy-parent-id" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_taxonomy_parent_id_nonce' ); ?>">
					<option value="0"><?php echo __('None', 'wpv-views'); ?></option>
					<?php $my_walker = new Walker_Category_id_select($view_settings['taxonomy_parent_id']);


					echo wp_terms_checklist(0, array('taxonomy' => $taxonomy, 'walker' => $my_walker));
					?>
				</select>
            </li>
        </ul>
	<?php
}

/**
* Render taxonomy parent filter summary text
*/

function wpv_get_filter_taxonomy_parent_summary_txt($view_settings) {
	global $wpdb;

	ob_start();

	if ($view_settings['taxonomy_parent_mode'] == 'current_view') {
		_e('Select taxonomy whose parent is the value set by the <strong>parent view</strong>.', 'wpv-views');
	} else {
		if (isset($view_settings['taxonomy_parent_id']) && $view_settings['taxonomy_parent_id'] > 0) {
			$selected_taxonomy = get_term($view_settings['taxonomy_parent_id'], $view_settings['taxonomy_type']);
			if (null ==  $selected_taxonomy) { // TODO Review this
				$selected_taxonomy = __('None', 'wpv-views');
			} else {
				$selected_taxonomy = $selected_taxonomy->name;
			}
		} else {
			$selected_taxonomy = __('None', 'wpv-views');
		}
		echo sprintf(__('Select taxonomy whose parent is <strong>%s</strong>.', 'wpv-views'), $selected_taxonomy);
	}

	$data = ob_get_clean();

	return $data;

}

/**
* Check if $term belongs to $taxonomy
*/

function wpv_get_tax_relationship_test($term, $taxonomy) {
	$term = (int)$term;
	$term_check = term_exists($term, $taxonomy);
	if ($term_check !== 0 && $term_check !== null) {
		return 'good';
	} else {
		return 'bad';
	}
}

/**
* DEPRECATED test
*/

function wpv_get_posts_select() {
    if (wp_verify_nonce($_POST['wpv_nonce'], 'wpv_get_posts_select_nonce')) {
		wpv_show_posts_dropdown($_POST['post_type']);
    }
    die();
}

/**
* Renders a select with the given $post_type posts as options, $name as name and $selected as selected option
*/

function wpv_show_posts_dropdown($post_type, $name = '_wpv_settings[parent_id]', $selected = 0) {

	$hierarchical_post_types = get_post_types( array( 'hierarchical' => true ) );
	
	$hierarchical = in_array($post_type, $hierarchical_post_types) ? 1 : 0;
	
	$attr = array('name'=> $name,
				  'post_type' => $post_type,
				  'show_option_none' => __('None', 'wpv-views'),
				  'selected' => $selected);
	
	if ($hierarchical) {
		wp_dropdown_pages($attr);
	} else {
		$defaults = array(
			'depth' => 0, 'child_of' => 0,
			'selected' => $selected, 'echo' => 1,
			'name' => 'page_id', 'id' => '',
			'show_option_none' => '', 'show_option_no_change' => '',
			'option_none_value' => ''
		);
		$r = wp_parse_args( $attr, $defaults );
		extract( $r, EXTR_SKIP );
		
		$pages = get_posts(array('numberposts' => -1, 'post_type' => $post_type, 'suppress_filters' => false));
		$output = '';
		// Back-compat with old system where both id and name were based on $name argument
		if ( empty($id) )
			$id = $name;
	
		if ( ! empty($pages) ) {
			$output = "<select name='" . esc_attr( $name ) . "' id='" . esc_attr( $id ) . "'>\n";
			if ( $show_option_no_change )
				$output .= "\t<option value=\"-1\">$show_option_no_change</option>";
			if ( $show_option_none )
				$output .= "\t<option value=\"" . esc_attr($option_none_value) . "\">$show_option_none</option>\n";
			$output .= walk_page_dropdown_tree($pages, $depth, $r);
			$output .= "</select>\n";
		}
	
		echo $output;	
	}
}

/**
* DEPRECATED test
*/

function wpv_get_taxonomy_parents_select() {
    if (wp_verify_nonce($_POST['wpv_nonce'], 'wpv_get_taxonomy_select_nonce')) {
		?>
		<select name="wpv_taxonomy_parent_id">
			<option selected="selected" value="0"><?php echo __('None', 'wpv-views'); ?></option>
			<?php $my_walker = new Walker_Category_id_select(0);
			
			$taxonomy = $_POST['taxonomy'];
	
			echo wp_terms_checklist(0, array('taxonomy' => $taxonomy, 'walker' => $my_walker));
		?>
		</select>
		<?php
    }
    die();
	
}