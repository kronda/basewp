<?php

if(is_admin()){
	
	/**
	* Add the taxonomy term filter to the list and to the popup select
	*/
	
	add_action('wpv_add_taxonomy_filter_list_item', 'wpv_add_filter_taxonomy_term_list_item', 1, 1);
	add_filter('wpv_taxonomy_filters_add_filter', 'wpv_filters_add_filter_taxonomy_term', 1, 2);


	function wpv_filters_add_filter_taxonomy_term($filters, $taxonomy_type) {
		$filters['taxonomy_term'] = array('name' => __('Taxonomy term', 'wpv-views'),
						'present' => 'taxonomy_terms_mode',
						'callback' => 'wpv_add_new_filter_taxonomy_term_list_item',
						'args' => $taxonomy_type
						);

		return $filters;
	}
	
	/**
	* Create taxonomy term filter callback
	*/

	function wpv_add_new_filter_taxonomy_term_list_item($taxonomy_type) {
		$args = array(
			'taxonomy_terms_mode' => 'THESE',
			'taxonomy_type' => $taxonomy_type
		);
		wpv_add_filter_taxonomy_term_list_item($args);
	}
	
	/**
	* Render taxonomy term filter item in the filters list
	*/

	function wpv_add_filter_taxonomy_term_list_item($view_settings) {
		if (isset($view_settings['taxonomy_terms_mode'])) {
			$td = wpv_get_list_item_ui_taxonomy_term('', $view_settings);
			echo '<li id="js-row-taxonomy_term" class="js-filter-row js-filter-row-simple js-filter-for-taxonomies js-filter-taxonomy-term js-filter-row-taxonomy_term">' . $td . '</li>';
		}
	}
	
	/**
	* Render taxonomy term filter item content in the filters list
	*/

	function wpv_get_list_item_ui_taxonomy_term( $selected, $view_settings = null ) {

        if ( isset( $view_settings['taxonomy_type'] ) && is_array( $view_settings['taxonomy_type'] ) ) {
            $view_settings['taxonomy_type'] = $view_settings['taxonomy_type'][0];
        }
        if ( !isset( $view_settings['taxonomy_terms_mode'] ) ) {
			$view_settings['taxonomy_terms_mode'] = 'THESE';
		}
        if ( !isset( $view_settings['taxonomy_terms'] ) ) {
			$view_settings['taxonomy_terms'] = array();
        }
        
        if ( function_exists('icl_object_id') && !empty( $view_settings['taxonomy_terms'] ) ) {
		// Adjust for WPML support
			$trans_term_ids = array();
			foreach ( $view_settings['taxonomy_terms'] as $untrans_term_id ) {
				$trans_term_ids[] = icl_object_id( $untrans_term_id, $view_settings['taxonomy_type'], true );
			}
			$view_settings['taxonomy_terms'] = $trans_term_ids;
		}
		
		ob_start()
		?>
		<p class='wpv-filter-taxonomy-term-summary js-wpv-filter-summary js-wpv-filter-taxonomy-term-summary'>
			<?php echo wpv_get_filter_taxonomy_term_summary_txt( $view_settings ); ?>
		</p>
		<p class='edit-filter js-wpv-filter-edit-controls'>
			<i class='button-secondary icon-edit icon-large js-wpv-filter-edit-open js-wpv-filter-taxonomy-term-edit-open' title='<?php echo esc_attr( __('Edit this filter','wpv-views') ); ?>'></i>
			<i class='button-secondary icon-trash icon-large js-filter-remove' title='<?php echo esc_attr( __('Remove this filter','wpv-views') ); ?>' data-nonce='<?php echo wp_create_nonce( 'wpv_view_filter_taxonomy_term_delete_nonce' ); ?>'></i>
		</p>
		<div id="wpv-filter-taxonomy-term-edit" class="wpv-filter-edit js-wpv-filter-edit">
			<fieldset>
				<legend><strong><?php echo __('Taxonomy term', 'wpv-views'); ?>:</strong></legend>
				<div id="wpv-filter-taxonomy-term" class="js-filter-taxonomy-term-list">
					<?php wpv_render_taxonomy_term_options( array( 'mode' => 'edit', 'view_settings' => $view_settings ) ); ?>
				</div>
			</fieldset>
			<p>
				<input class="button-secondary js-wpv-filter-edit-ok js-wpv-filter-taxonomy-term-edit-ok" type="button" value="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-save="<?php echo htmlentities( __('Save', 'wpv-views'), ENT_QUOTES ); ?>" data-close="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-success="<?php echo htmlentities( __('Updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_taxonomy_term_nonce' ); ?>" />
			</p>
		</div>
		<?php
		$res = ob_get_clean();
		return $res;
        /*
        ob_start();
        wpv_render_taxonomy_term_options(array('mode' => 'edit',
                             'view_settings' => $view_settings));
        $data = ob_get_clean();

        $td = "<p class='wpv-filter-taxonomy-term-summary js-wpv-filter-summary js-wpv-filter-taxonomy-term-summary'>\n";
	$td .= wpv_get_filter_taxonomy_term_summary_txt($view_settings);
	$td .= "</p>\n<p class='edit-filter js-wpv-filter-edit-controls'>\n<i class='button-secondary icon-edit icon-large js-wpv-filter-edit-open js-wpv-filter-taxonomy-term-edit-open' title='". __('Edit','wpv-views') ."'></i>\n<i class='button-secondary icon-trash icon-large js-filter-remove' title='". __('Remove this filter','wpv-views') ."' data-nonce='". wp_create_nonce( 'wpv_view_filter_taxonomy_term_delete_nonce' ) . "'></i>\n</p>";
	$td .= "<div id=\"wpv-filter-taxonomy-term-edit\" class=\"wpv-filter-edit js-wpv-filter-edit\">\n";
	$td .= '<fieldset>';
	$td .= '<legend><strong>' . __('Taxonomy term', 'wpv-views') . ':</strong></legend>';
	$td .= '<div id="wpv-filter-taxonomy-term" class="js-filter-taxonomy-term-list">' . $data . '</div>';
	$td .= '</fieldset>';
	ob_start();
	?>
	<p>
		<input class="button-secondary js-wpv-filter-edit-ok js-wpv-filter-taxonomy-term-edit-ok" type="button" value="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-save="<?php echo htmlentities( __('Save', 'wpv-views'), ENT_QUOTES ); ?>" data-close="<?php echo htmlentities( __('Close', 'wpv-views'), ENT_QUOTES ); ?>" data-success="<?php echo htmlentities( __('Updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_taxonomy_term_nonce' ); ?>" />
	</p>
	<?php
	$td .= ob_get_clean();
	$td .= '</div>';

	return $td;
	*/
    }

	/**
	* Update taxonomy term filter callback
	*/

	add_action('wp_ajax_wpv_filter_taxonomy_term_update', 'wpv_filter_taxonomy_term_update_callback');

	function wpv_filter_taxonomy_term_update_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_taxonomy_term_nonce') ) die("Security check");
		if ( empty( $_POST['tax_term_mode'] ) ) {
			echo $_POST['id'];
			die();
		}
		$change = false;
		$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
		$filter_data['taxonomy_terms'] = array();
		if (isset($_POST['tax_term_list']) && !empty($_POST['tax_term_list'])) {
			parse_str($_POST['tax_term_list'], $terms_list);
			$filter_data['taxonomy_terms'] = $terms_list['taxonomy_terms'];
		}
		$filter_data['taxonomy_type'] = $_POST['tax_term_tax_type'];
		$filter_data['taxonomy_terms_mode'] = $_POST['tax_term_mode'];
		if ( !isset( $view_array['taxonomy_terms_mode'] ) || $_POST['tax_term_mode'] != $view_array['taxonomy_terms_mode'] ) {
			$change = true;
			$view_array['taxonomy_terms_mode'] = $_POST['tax_term_mode'];
		}
		if ( !isset( $view_array['taxonomy_terms'] ) || $filter_data['taxonomy_terms'] != $view_array['taxonomy_terms'] ) {
			$change = true;
			$view_array['taxonomy_terms'] = $filter_data['taxonomy_terms'];
		}
		if ( $change ) {
			$result = update_post_meta($_POST["id"], '_wpv_settings', $view_array);
		}
		echo wpv_get_filter_taxonomy_term_summary_txt($filter_data);
		die();
	}
	
	/**
	* Update taxonomy term filter summary callback
	*/

	// TODO This might not be needed here, maybe for summary filter
	add_action('wp_ajax_wpv_filter_taxonomy_term_sumary_update', 'wpv_filter_taxonomy_term_sumary_update_callback');

	function wpv_filter_taxonomy_term_sumary_update_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_taxonomy_term_nonce') ) die("Security check");
		$filter_data['taxonomy_terms_mode'] = $_POST['tax_term_mode'];
		$filter_data['taxonomy_terms'] = array();
		if (isset($_POST['tax_term_list']) && !empty($_POST['tax_term_list'])) {
			parse_str($_POST['tax_term_list'], $terms_list);
			$filter_data['taxonomy_terms'] = $terms_list['taxonomy_terms'];
		}
		$filter_data['taxonomy_type'] = $_POST['tax_term_tax_type'];
		echo wpv_get_filter_taxonomy_term_summary_txt($filter_data);
		die();
	}
	
	/**
	* Delete taxonomy term filter callback
	*/

	add_action('wp_ajax_wpv_filter_taxonomy_term_delete', 'wpv_filter_taxonomy_term_delete_callback');

	function wpv_filter_taxonomy_term_delete_callback() {
		$nonce = $_POST["wpnonce"];
		if (! wp_verify_nonce($nonce, 'wpv_view_filter_taxonomy_term_delete_nonce') ) die("Security check");
		$view_array = get_post_meta($_POST["id"], '_wpv_settings', true);
		if ( isset( $view_array['taxonomy_terms_mode'] ) ) {
			unset( $view_array['taxonomy_terms_mode'] );
		}
		if ( isset( $view_array['taxonomy_terms'] ) ) {
			unset( $view_array['taxonomy_terms'] );
		}
		update_post_meta($_POST["id"], '_wpv_settings', $view_array);
		echo $_POST['id'];
		die();

	}
	
	/**
	* Add a filter to show the filter on the summary
	*/
    
	add_filter('wpv-view-get-summary', 'wpv_taxonomy_term_summary_filter', 5, 3);

	function wpv_taxonomy_term_summary_filter( $summary, $post_id, $view_settings ) {
		if ( isset( $view_settings['query_type'] ) && $view_settings['query_type'][0] == 'taxomomy' && isset( $view_settings['taxonomy_terms_mode'] ) ) {
			$summary .= wpv_get_filter_taxonomy_term_summary_txt($view_settings);
		}
		return $summary;
	}
	
}

/**
* Render taxonomy term filter options
*/

function wpv_render_taxonomy_term_options($args) {

    global $wpdb;

    $edit = isset($args['mode']) && $args['mode'] == 'edit';

    $view_settings = isset($args['view_settings']) ? $args['view_settings'] : array();

    $defaults = array('taxonomy_terms' => array(),
					  'taxonomy_terms_mode' => 'THESE');
    $view_settings = wp_parse_args($view_settings, $defaults);


	?>


        <?php
            if (isset($view_settings['taxonomy_type']) && $view_settings['taxonomy_type'] != '') {
                $taxonomy = $view_settings['taxonomy_type'];
            } else {
                $taxonomy = 'category';
            }

        ?>
		<p><?php echo __('Taxonomy term: ', 'wpv-views'); ?>
			<select class="taxonomy_terms_mode js-wpv-taxonomy-term-mode" name="taxonomy_terms_mode">
				<?php if($view_settings['taxonomy_terms_mode'] == 'THESE') {$selected = ' selected="selected"';} else {$selected = '';} ?>
				<option value="THESE"<?php echo $selected; ?>><?php echo __('is one of these', 'wpv-views'); ?></option>
				<?php if($view_settings['taxonomy_terms_mode'] == 'CURRENT_PAGE') {$selected = ' selected="selected"';} else {$selected = '';}  ?>
				<option value="CURRENT_PAGE"<?php echo $selected; ?>><?php echo __('set by the current page', 'wpv-views'); ?></option>
			</select>
		</p>

			<ul class="categorychecklist js-taxonomy-term-checklist">
					<?php
					ob_start();
					wp_terms_checklist(0, array('taxonomy' => $taxonomy, 'selected_cats' => $view_settings['taxonomy_terms']));

					$checklist = ob_get_clean();

					if ($edit) {
						if ($taxonomy == 'category') {
						$checklist = str_replace('post_category[]', 'taxonomy_terms[]', $checklist);
						} else {
						$checklist = str_replace('tax_input[' . $taxonomy . '][]', 'taxonomy_terms[]', $checklist);
						}
					}
					echo $checklist;
					?>
			</ul>
			
        <?php
}

/**
* Render taxonomy term filter summary text
*/

function wpv_get_filter_taxonomy_term_summary_txt($view_settings) {
	global $wpdb; // TODO is this wpdb really needed here?

	ob_start();

		if ($view_settings['taxonomy_terms_mode'] == 'THESE') {
			echo __('Taxonomy is <strong>One</strong> of these', 'wpv-views');
			echo '<strong> (';
			$cat_text = '';
			$category_selected = $view_settings['taxonomy_terms'];
			$taxonomy = $view_settings['taxonomy_type'];

			foreach($category_selected as $cat) {
				$term_check = term_exists((int)$cat, $taxonomy);
				if ($term_check !== 0 && $term_check !== null) {
					$term = get_term($cat, $taxonomy);
					if ($cat_text != '') {
						$cat_text .= ', ';
					}
					$cat_text .= $term->name;
				}
			}
			echo $cat_text;
			echo ')</strong>';
		} else if ($view_settings['taxonomy_terms_mode'] == 'CURRENT_PAGE') {
			echo __('Taxonomy is set by the current page', 'wpv-views');
		}

	$data = ob_get_clean();

	return $data;

}