<?php

/**
* Taxonomy term filter
*
* @package Views
*
* @since unknown
*/

WPV_Taxonomy_Term_Filter::on_load();

/**
* WPV_Post_Relationship_Filter
*
* Views Taxonomy Term Filter Class
*
* @since 1.7.0
*/

class WPV_Taxonomy_Term_Filter {

    static function on_load() {
        add_action( 'init', array( 'WPV_Taxonomy_Term_Filter', 'init' ) );
		add_action( 'admin_init', array( 'WPV_Taxonomy_Term_Filter', 'admin_init' ) );
    }

    static function init() {
		
    }
	
	static function admin_init() {
		// Register filter in lists and dialogs
		add_filter( 'wpv_taxonomy_filters_add_filter', array( 'WPV_Taxonomy_Term_Filter', 'wpv_filters_add_filter_taxonomy_term' ), 1, 2 );
		add_action( 'wpv_add_taxonomy_filter_list_item', array( 'WPV_Taxonomy_Term_Filter', 'wpv_add_filter_taxonomy_term_list_item' ), 1, 1 );
		// AJAX calbacks
		add_action( 'wp_ajax_wpv_filter_taxonomy_term_update', array( 'WPV_Taxonomy_Term_Filter', 'wpv_filter_taxonomy_term_update_callback' ) );
			// TODO This might not be needed here, maybe for summary filter
			add_action( 'wp_ajax_wpv_filter_taxonomy_term_sumary_update', array( 'WPV_Taxonomy_Term_Filter', 'wpv_filter_taxonomy_term_sumary_update_callback' ) );
		add_action( 'wp_ajax_wpv_filter_taxonomy_term_delete', array( 'WPV_Taxonomy_Term_Filter', 'wpv_filter_taxonomy_term_delete_callback' ) );
		add_filter( 'wpv-view-get-summary', array( 'WPV_Taxonomy_Term_Filter', 'wpv_taxonomy_term_summary_filter' ), 5, 3 );
		// Register scripts
		add_action( 'admin_enqueue_scripts', array( 'WPV_Taxonomy_Term_Filter','admin_enqueue_scripts' ), 20 );
	}
	
	/**
	* admin_enqueue_scripts
	*
	* Register the needed script for this filter
	*
	* @since 1.7
	*/
	
	static function admin_enqueue_scripts( $hook ) {
		wp_register_script( 'views-filter-taxonomy-term-js', ( WPV_URL . "/res/js/redesign/views_filter_taxonomy_term.js" ), array( 'views-filters-js'), WPV_VERSION, true );
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'views-editor' ) {
			wp_enqueue_script( 'views-filter-taxonomy-term-js' );
		}
	}
	
	/**
	* wpv_filters_add_filter_taxonomy_term
	*
	* Register the taxonomy term filter in the popup dialog
	*
	* @param $filters
	*
	* @since unknown
	*/

	static function wpv_filters_add_filter_taxonomy_term( $filters, $taxonomy_type ) {
		$filters['taxonomy_term'] = array(
			'name' => __( 'Taxonomy term', 'wpv-views' ),
			'present' => 'taxonomy_terms_mode',
			'callback' => array( 'WPV_Taxonomy_Term_Filter', 'wpv_add_new_filter_taxonomy_term_list_item' ),
			'args' => $taxonomy_type,
			'group' => __( 'Taxonomy filters', 'wpv-views' )
		);
		return $filters;
	}
	
	/**
	* wpv_add_new_filter_taxonomy_term_list_item
	*
	* Register the taxonomy term filter in the filters list
	*
	* @param $taxonomy_type array
	*
	* @since unknown
	*/

	static function wpv_add_new_filter_taxonomy_term_list_item( $taxonomy_type ) {
		$args = array(
			'taxonomy_terms_mode' => 'THESE',
			'taxonomy_type' => $taxonomy_type
		);
		WPV_Taxonomy_Term_Filter::wpv_add_filter_taxonomy_term_list_item( $args );
	}
	
	/**
	* wpv_add_filter_taxonomy_term_list_item
	*
	* Render taxonomy term filter item in the filters list
	*
	* @param $view_settings
	*
	* @since unknown
	*/

	static function wpv_add_filter_taxonomy_term_list_item( $view_settings ) {
		if ( isset( $view_settings['taxonomy_terms_mode'] ) ) {
			$li = WPV_Taxonomy_Term_Filter::wpv_get_list_item_ui_taxonomy_term( $view_settings );
			WPV_Filter_Item::simple_filter_list_item( 'taxonomy_term', 'taxonomies', 'taxonomy-term', __( 'Taxonomy term filter', 'wpv-views' ), $li );
		}
	}
	
	/**
	* wpv_get_list_item_ui_taxonomy_term
	*
	* Render taxonomy term filter item content in the filters list
	*
	* @param $view_settings
	*
	* @since unknown
	*/

	static function wpv_get_list_item_ui_taxonomy_term( $view_settings = array() ) {
		if ( isset( $view_settings['taxonomy_type'] ) && is_array( $view_settings['taxonomy_type'] ) ) {
            $view_settings['taxonomy_type'] = $view_settings['taxonomy_type'][0];
        }
        if ( ! isset( $view_settings['taxonomy_terms_mode'] ) ) {
			$view_settings['taxonomy_terms_mode'] = 'THESE';
		}
        if ( ! isset( $view_settings['taxonomy_terms'] ) ) {
			$view_settings['taxonomy_terms'] = array();
        }
		if ( ! empty( $view_settings['taxonomy_terms'] ) ) {
			$adjusted_term_ids = array();
			foreach ( $view_settings['taxonomy_terms'] as $candidate_term_id ) {
				// WordPress 4.2 compatibility - split terms
				$candidate_term_id_splitted = wpv_compat_get_split_term( $candidate_term_id, $view_settings['taxonomy_type'] );
				if ( $candidate_term_id_splitted ) {
					$candidate_term_id = $candidate_term_id_splitted;
				}
				// WPML support
				$candidate_term_id = apply_filters( 'translate_object_id', $candidate_term_id, $view_settings['taxonomy_type'], true, null );
				$adjusted_term_ids[] = $candidate_term_id;
			}
			$view_settings['taxonomy_terms'] = $adjusted_term_ids;
		}
		ob_start()
		?>
		<p class='wpv-filter-taxonomy-term-summary js-wpv-filter-summary js-wpv-filter-taxonomy-term-summary'>
			<?php echo wpv_get_filter_taxonomy_term_summary_txt( $view_settings ); ?>
		</p>
		<?php
		WPV_Filter_Item::simple_filter_list_item_buttons( 'taxonomy-term', 'wpv_filter_taxonomy_term_update', wp_create_nonce( 'wpv_view_filter_taxonomy_term_nonce' ), 'wpv_filter_taxonomy_term_delete', wp_create_nonce( 'wpv_view_filter_taxonomy_term_delete_nonce' ) );
		?>
		<div id="wpv-filter-taxonomy-term-edit" class="wpv-filter-edit js-wpv-filter-edit">
			<div id="wpv-filter-taxonomy-term" class="js-wpv-filter-options js-wpv-filter-taxonomy-term-options">
				<?php WPV_Taxonomy_Term_Filter::wpv_render_taxonomy_term_options( $view_settings ); ?>
			</div>
			<div class="js-wpv-filter-toolset-messages"></div>
		</div>
		<?php
		$res = ob_get_clean();
		return $res;
    }
	
	/**
	* wpv_filter_taxonomy_term_update_callback
	*
	* Update taxonomy term filter callback
	*
	* @since unknown
	*/

	static function wpv_filter_taxonomy_term_update_callback() {
		if ( ! current_user_can( 'manage_options' ) ) {
			$data = array(
				'type' => 'capability',
				'message' => __( 'You do not have permissions for that.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		if ( 
			! isset( $_POST["wpnonce"] )
			|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_filter_taxonomy_term_nonce' ) 
		) {
			$data = array(
				'type' => 'nonce',
				'message' => __( 'Your security credentials have expired. Please reload the page to get new ones.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		if (
			! isset( $_POST["id"] )
			|| ! is_numeric( $_POST["id"] )
			|| intval( $_POST['id'] ) < 1 
		) {
			$data = array(
				'type' => 'id',
				'message' => __( 'Wrong or missing ID.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		if ( empty( $_POST['filter_options'] ) ) {
			$data = array(
				'type' => 'data_missing',
				'message' => __( 'Wrong or missing data.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		$change = false;
		$view_id = intval( $_POST['id'] );
		parse_str( $_POST['filter_options'], $filter_taxonomy_term );
		if ( ! isset( $filter_taxonomy_term['taxonomy_terms'] ) ) {
			$filter_taxonomy_term['taxonomy_terms'] = array();
		}
		$view_array = get_post_meta( $view_id, '_wpv_settings', true );
		$settings_to_check = array(
			'taxonomy_terms_mode',
			'taxonomy_terms',
			'taxonomy_terms_framework'
		);
		foreach ( $settings_to_check as $set ) {
			if ( 
				isset( $filter_taxonomy_term[$set] ) 
				&& (
					! isset( $view_array[$set] ) 
					|| $filter_taxonomy_term[$set] != $view_array[$set] 
				)
			) {
				if ( is_array( $filter_taxonomy_term[$set] ) ) {
					$filter_taxonomy_term[$set] = array_map( 'sanitize_text_field', $filter_taxonomy_term[$set] );
				} else {
					$filter_taxonomy_term[$set] = sanitize_text_field( $filter_taxonomy_term[$set] );
				}
				$change = true;
				$view_array[$set] = $filter_taxonomy_term[$set];
			}
		}
		if ( $change ) {
			$result = update_post_meta( $view_id, '_wpv_settings', $view_array );
			do_action( 'wpv_action_wpv_save_item', $view_id );
		}
		$data = array(
			'id' => $view_id,
			'message' => __( 'Taxonomy term filter saved', 'wpv-views' ),
			'summary' => wpv_get_filter_taxonomy_term_summary_txt( $view_array )
		);
		wp_send_json_success( $data );
	}

	static function wpv_filter_taxonomy_term_sumary_update_callback() {
		$nonce = $_POST["wpnonce"];
		if ( ! wp_verify_nonce( $nonce, 'wpv_view_filter_taxonomy_term_nonce' ) ) {
			die( "Security check" );
		}
		$filter_data['taxonomy_terms_mode'] = $_POST['tax_term_mode'];
		$filter_data['taxonomy_terms'] = array();
		if ( isset( $_POST['tax_term_list'] ) && ! empty($_POST['tax_term_list'] ) ) {
			parse_str( $_POST['tax_term_list'], $terms_list );
			$filter_data['taxonomy_terms'] = $terms_list['taxonomy_terms'];
		}
		$filter_data['taxonomy_type'] = $_POST['tax_term_tax_type'];
		echo wpv_get_filter_taxonomy_term_summary_txt( $filter_data );
		die();
	}
	
	/**
	* wpv_filter_taxonomy_term_delete_callback
	*
	* Delete taxonomy term filter callback
	*
	* @since unknown
	*/
	
	static function wpv_filter_taxonomy_term_delete_callback() {
		if ( ! current_user_can( 'manage_options' ) ) {
			$data = array(
				'type' => 'capability',
				'message' => __( 'You do not have permissions for that.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		if ( 
			! isset( $_POST["wpnonce"] )
			|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_filter_taxonomy_term_delete_nonce' ) 
		) {
			$data = array(
				'type' => 'nonce',
				'message' => __( 'Your security credentials have expired. Please reload the page to get new ones.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		if (
			! isset( $_POST["id"] )
			|| ! is_numeric( $_POST["id"] )
			|| intval( $_POST['id'] ) < 1 
		) {
			$data = array(
				'type' => 'id',
				'message' => __( 'Wrong or missing ID.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		$view_array = get_post_meta( $_POST["id"], '_wpv_settings', true );
		$to_delete = array(
			'taxonomy_terms_mode',
			'taxonomy_terms',
			'taxonomy_terms_framework'
		);
		foreach ( $to_delete as $index ) {
			if ( isset( $view_array[$index] ) ) {
				unset( $view_array[$index] );
			}
		}
		update_post_meta( $_POST["id"], '_wpv_settings', $view_array );
		do_action( 'wpv_action_wpv_save_item', $_POST["id"] );
		$data = array(
			'id' => $_POST["id"],
			'message' => __( 'Taxonomy term filter deleted', 'wpv-views' )
		);
		wp_send_json_success( $data );
	}
	
	/**
	* wpv_taxonomy_term_summary_filter
	
	* Show the taxonomy term filter on the View summary
	*
	* @since unknown
	*/

	static function wpv_taxonomy_term_summary_filter( $summary, $post_id, $view_settings ) {
		if ( isset( $view_settings['query_type'] ) && $view_settings['query_type'][0] == 'taxomomy' && isset( $view_settings['taxonomy_terms_mode'] ) ) {
			$summary .= wpv_get_filter_taxonomy_term_summary_txt( $view_settings );
		}
		return $summary;
	}
	
	/**
	* wpv_render_taxonomy_term_options
	*
	* Render taxonomy term filter options
	*
	* @param $view_settings
	*
	* @since unknown
	*/

	static function wpv_render_taxonomy_term_options( $view_settings = array() ) {
		$defaults = array(
			'taxonomy_terms' => array(),
			'taxonomy_terms_mode' => 'THESE',
			'taxonomy_terms_framework' => ''
		);
		$view_settings = wp_parse_args( $view_settings, $defaults );
		if ( isset( $view_settings['taxonomy_type'] ) && $view_settings['taxonomy_type'] != '' ) {
			$taxonomy = $view_settings['taxonomy_type'];
		} else {
			$taxonomy = 'category';
		}
		?>
		<h4><?php  _e( 'List the following terms', 'wpv-views' ); ?></h4>
		<ul class="wpv-filter-options-set">
			<li>
				<input type="radio" id="taxonomy-terms-mode-current" name="taxonomy_terms_mode" <?php checked( $view_settings['taxonomy_terms_mode'], 'CURRENT_PAGE' ); ?> class="taxonomy-terms-mode js-wpv-taxonomy-term-mode" value="CURRENT_PAGE" autocomplete="off" />
				<label for="taxonomy-terms-mode-current"><?php _e('Set by the current post', 'wpv-views'); ?></label>
			</li>
			<li>
				<input type="radio" id="taxonomy-terms-mode-these" name="taxonomy_terms_mode" <?php checked( $view_settings['taxonomy_terms_mode'], 'THESE' ); ?> class="taxonomy-terms-mode js-wpv-taxonomy-term-mode" value="THESE" autocomplete="off" />
				<label for="taxonomy-terms-mode-these"><?php echo __('One of these', 'wpv-views'); ?></label>
			<?php 
			if ( taxonomy_exists( $taxonomy ) ) {
			?>
				<ul class="wpv-mightlong-list js-taxonomy-term-checklist<?php if ( $view_settings['taxonomy_terms_mode'] == 'CURRENT_PAGE' ) { echo ' hidden'; } ?>">
						<?php
						ob_start();
						$my_walker = new WPV_Walker_Taxonomy_Checkboxes_Flat();
						wp_terms_checklist( 0, array( 'taxonomy' => $taxonomy, 'selected_cats' => $view_settings['taxonomy_terms'], 'walker' => $my_walker ) );

						$checklist = ob_get_clean();

							if ($taxonomy == 'category') {
							$checklist = str_replace('post_category[]', 'taxonomy_terms[]', $checklist);
							} else {
							$checklist = str_replace( 'tax_input[' . $taxonomy . '][]', 'taxonomy_terms[]', $checklist );
							}
						
						echo $checklist;
						?>
				</ul>
			<?php
			}
			?>
			</li>
			<?php
			global $WP_Views_fapi;
			if ( $WP_Views_fapi->framework_valid ) {
				$framework_data = $WP_Views_fapi->framework_data
			?>
			<li>
				<input type="radio" id="taxonomy-terms-mode-framework" name="taxonomy_terms_mode"  class="taxonomy-terms-mode js-wpv-taxonomy-term-mode" value="framework" <?php checked( $view_settings['taxonomy_terms_mode'], 'framework' ); ?> autocomplete="off" />
				<label for="taxonomy-terms-mode-framework"><?php echo sprintf( __( 'Set by the %s key: ', 'wpv-views'), sanitize_text_field( $framework_data['name'] ) ); ?></label>
				<select name="taxonomy_terms_framework" autocomplete="off">
					<option value=""><?php _e( 'Select a key', 'wpv-views' ); ?></option>
					<?php
					$fw_key_options = array();
					$fw_key_options = apply_filters( 'wpv_filter_extend_framework_options_for_taxonomy_term', $fw_key_options );
					foreach ( $fw_key_options as $index => $value ) {
						?>
						<option value="<?php echo esc_attr( $index ); ?>" <?php selected( $view_settings['taxonomy_terms_framework'], $index ); ?>><?php echo $value; ?></option>
						<?php
					}
					?>
				</select>
			</li>
			<?php
			}
			?>
		</ul>
		<?php
	}

}

/**
* WPV_Walker_Taxonomy_Checkboxes_Flat
*
* Walker to output an unordered list of category checkbox <input> elements, without children structure
*
* @since 1.7
*/
 
class WPV_Walker_Taxonomy_Checkboxes_Flat extends Walker {
	public $tree_type = 'category';
	public $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); //TODO: decouple this

	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker:start_lvl()
	 *
	 * @since 1.7
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of category. Used for tab indentation.
	 * @param array  $args   An array of arguments. @see wp_terms_checklist()
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @see Walker::end_lvl()
	 *
	 * @since 1.7
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of category. Used for tab indentation.
	 * @param array  $args   An array of arguments. @see wp_terms_checklist()
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		
	}

	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @since 1.7
	 *
	 * @param string $output   Passed by reference. Used to append additional content.
	 * @param object $category The current term object.
	 * @param int    $depth    Depth of the term in reference to parents. Default 0.
	 * @param array  $args     An array of arguments. @see wp_terms_checklist()
	 * @param int    $id       ID of the current term.
	 */
	public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		if ( empty( $args['taxonomy'] ) ) {
			$taxonomy = 'category';
		} else {
			$taxonomy = $args['taxonomy'];
		}

		if ( $taxonomy == 'category' ) {
			$name = 'post_category';
		} else {
			$name = 'tax_input[' . $taxonomy . ']';
		}
		$args['popular_cats'] = empty( $args['popular_cats'] ) ? array() : $args['popular_cats'];
		$class = in_array( $category->term_id, $args['popular_cats'] ) ? ' class="popular-category"' : '';

		$args['selected_cats'] = empty( $args['selected_cats'] ) ? array() : $args['selected_cats'];

		/** This filter is documented in wp-includes/category-template.php */
		$output .= "\n<li id='" . esc_attr( $taxonomy ) . "-" . esc_attr( $category->term_id ) . esc_attr( $class ) . "'>" .
			'<label class="selectit"><input value="' . esc_attr( $category->term_id ) . '" type="checkbox" name="' . esc_attr( $name ) . '[]" id="in-' . esc_attr( $taxonomy ) . '-' . esc_attr( $category->term_id ) . '" ' .
			checked( in_array( $category->term_id, $args['selected_cats'] ), true, false ) . " " .
			disabled( empty( $args['disabled'] ), false, false ) . ' /> ' .
			esc_html( apply_filters( 'the_category', $category->name ) ) . '</label>';
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @see Walker::end_el()
	 *
	 * @since 1.7
	 *
	 * @param string $output   Passed by reference. Used to append additional content.
	 * @param object $category The current term object.
	 * @param int    $depth    Depth of the term in reference to parents. Default 0.
	 * @param array  $args     An array of arguments. @see wp_terms_checklist()
	 */
	public function end_el( &$output, $category, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}
}