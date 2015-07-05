<?php

require_once( WPV_PATH_EMBEDDED . '/common/visual-editor/editor-addon.class.php' );

if ( !defined( 'WPT_LOCALIZATION' ) ) {
	require_once( WPV_PATH_EMBEDDED . '/common/localization/wpt-localization.php' );
}

if ( !defined( 'ADODB_DATE_VERSION' ) ) {
	if ( defined( 'WPTOOLSET_FORMS_ABSPATH' ) ) {
		require_once WPTOOLSET_FORMS_ABSPATH . '/lib/adodb-time.inc.php';
	}
}

require WPV_PATH_EMBEDDED . '/inc/wpv-filter-query.php';
require WPV_PATH_EMBEDDED . '/inc/listing/listing.php';


class WP_Views {

	function __construct() {

		add_action( 'init', array( $this, 'init' ) );
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );

		$this->view_ids = array();
		$this->current_view = null;
		$this->CCK_types = array();
		$this->widget_view_id = 0;
		$this->view_depth = 0;
		$this->view_count = array();
		$this->set_view_counts = array();
		$this->view_shortcode_attributes = array();
		$this->view_used_ids = array();
		$this->rendering_views_form_in_progress = false;

		$this->post_query = null;
		$this->post_query_stack = array();
		$this->top_current_page = null;
		$this->current_page = array();

		$this->taxonomy_data = array();
		$this->parent_taxonomy = 0;

		$this->users_data = array();
		$this->parent_user = 0;

		$this->variables = array();

		$this->force_disable_dependant_parametric_search = false;
		$this->returned_ids_for_parametric_search = array();

		/*
		* Compatibility
		*/

		// WPML
		add_filter( 'icl_cf_translate_state', array( $this, 'custom_field_translate_state' ), 10, 2 );

		// WooCommerce
		add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'wpv_woocommerce_product_add_to_cart_url' ), 10, 2 );
	}


	function __destruct() { }


	function init(){

		$this->wpv_register_type_view();

		add_action( 'wp_ajax_wpv_get_type_filter_summary', 'wpv_ajax_get_type_filter_summary' );
		add_action( 'wp_ajax_wpv_get_table_row_ui', array( $this, 'ajax_get_table_row_ui' ) );
		add_action( 'wp_ajax_wpv_add_custom_field', 'wpv_ajax_add_custom_field' );
		add_action( 'wp_ajax_wpv_add_taxonomy', 'wpv_ajax_add_taxonomy' );
		add_action( 'wp_ajax_wpv_pagination', 'wpv_ajax_pagination' );
		add_action( 'wp_ajax_wpv_views_editor_height', array( $this, 'save_editor_height' ) );

		// add_action( 'wp_ajax_wpv_get_post_relationship_info', 'wpv_ajax_wpv_get_post_relationship_info' );CHECK DEPRECATED, maybe used in MM?

		// @todo those should go to the shortcodes GUI file, remember those belong to is_admin()
		// AJAX calls to insert View forms shortcode
		add_action( 'wp_ajax_wpv_view_form_popup', 'wpv_ajax_wpv_view_form_popup' );
		// AJAX calls to insert View Search term shortcode
		add_action( 'wp_ajax_wpv_search_term_popup', 'wpv_ajax_wpv_search_term_popup' );
		// AJAX calls to insert Translatable String shortcode
		add_action( 'wp_ajax_wpv_translatable_string_popup', 'wpv_ajax_wpv_translatable_string_popup' );

		// AJAX calls for date filters
		add_action('wp_ajax_wpv_format_date', array( $this, 'wpv_format_date' ) );
		add_action('wp_ajax_nopriv_wpv_format_date', array( $this, 'wpv_format_date' ) );
		
		// Basic values when get_view_settings
		
		add_filter( 'wpv_view_settings', array( $this, 'wpv_view_settings_set_fallbacks' ), 10, 2 );

		if ( is_admin() ) {
			
			global $pagenow, $wp_version;

			add_action( 'admin_enqueue_scripts', array( $this,'wpv_admin_enqueue_scripts' ) );

			add_action( 'admin_menu', array( $this, 'admin_menu' ), 20 );
			add_action( 'admin_head', array( $this, 'settings_box_load' ) );
			add_action( 'save_post', array( $this, 'save_view_settings' ) );// DEPRECATE!!!!
			add_action( 'wpv_action_wpv_save_item', array( $this, 'after_save_item' ) );
			add_action( 'wpv_action_wpv_import_item', array( $this, 'after_import_item' ) );

			if ( 'post.php' == $pagenow
				|| 'post-new.php' == $pagenow
				|| ( 
					'admin.php' == $pagenow 
					&& isset( $_GET['page'] ) 
					&& 'dd_layouts_edit' == $_GET['page'] 
				) 
			) {
				add_action( 'admin_head', array( $this, 'post_edit_tinymce' ) );
				add_action( 'icl_post_languages_options_after', array( $this, 'language_options' ) );
				add_action( 'admin_head', array( $this, 'set_editor_height' ) );
			}

			if ( version_compare( $wp_version, '3.3', '<' ) ) {
				add_filter( 'contextual_help', array( $this, 'admin_plugin_help' ), 10, 3 );
			}

			// Exclude some taxonomies from different pieces of the GUI
			add_filter( 'wpv_admin_exclude_tax_slugs', 'wpv_admin_exclude_tax_slugs' );
			// Exclude some post types from different pieces of the GUI
			add_filter( 'wpv_admin_exclude_post_type_slugs', 'wpv_admin_exclude_post_type_slugs' );
			// List the default spinners available for pagination and parametric search
			add_filter( 'wpv_admin_available_spinners', 'wpv_admin_available_spinners', 5 );

		} else {
			// !is_admin()

			// Add scripts and styles to the frontend
			add_action( 'wp_enqueue_scripts', array( $this, 'wpv_frontend_enqueue_scripts' ) );
			// Set priority lower than 20, so we load the CSS before the footer scripts and avoid the bottleneck
			add_action( 'wp_footer', array( $this, 'wpv_meta_html_extra_css' ), 5 );
			// Set priority higher than 20, when all the footer scripts are loaded
			add_action( 'wp_footer', array( $this, 'wpv_meta_html_extra_js' ), 25 );
			// Set priority higher than 20, when all footer scripts are loaded, but before 25, when custom javascript is added
			add_action( 'wp_footer', array( $this, 'wpv_additional_js_files' ), 21 );

		}

		// Shortcodes
		add_shortcode( 'wpv-view', array( $this, 'short_tag_wpv_view' ) );
		add_shortcode( 'wpv-form-view', array( $this, 'short_tag_wpv_view_form' ) );

		add_filter( 'edit_post_link', array( $this, 'edit_post_link' ), 10, 2 );

	}


	function wpv_register_type_view() {
		$labels = array(
				'name' => _x( 'Views', 'post type general name' ),
				'singular_name' => _x( 'View', 'post type singular name' ),
				'add_new' => _x( 'Add New', 'book' ),
				'add_new_item' => __( 'Add New View', 'wpv-views' ),
				'edit_item' => __( 'Edit View', 'wpv-views' ),
				'new_item' => __( 'New View', 'wpv-views' ),
				'view_item' => __( 'View Views', 'wpv-views' ),
				'search_items' => __( 'Search Views', 'wpv-views' ),
				'not_found' =>  __( 'No views found', 'wpv-views' ),
				'not_found_in_trash' => __( 'No views found in Trash', 'wpv-views' ),
				'parent_item_colon' => '',
				'menu_name' => 'Views' );
		$args = array(
				'labels' => $labels,
				'public' => false,
				'publicly_queryable' => false,
				'show_ui' => false,
				'show_in_menu' => false,
				'query_var' => false,
				'rewrite' => false,
				'can_export' => false,
				'capability_type' => 'post',
				'has_archive' => false,
				'hierarchical' => false,
				'menu_position' => 80,
				'supports' => array( 'title', 'editor', 'author' ) );
		register_post_type( 'view', $args );
	}


	function custom_field_translate_state( $state, $field_name ) {
		switch( $field_name ) {
			case '_wpv_settings':
			case '_wpv_layout_settings':
			case '_wpv_view_sync':
				return 'ignore';

			default:
				return $state;
		}
	}


	// Add WPML sync options.
	function language_options() {
		// not needed for theme version.
	}


	/**
	 * Fix malformed add to cart URL in Views AJAX pagination and automatic results in a parametric search.
	 *
	 * @see https://icanlocalize.basecamphq.com/projects/11629195-toolset-peripheral-work/todo_items/186738278/comments
	 */
	function wpv_woocommerce_product_add_to_cart_url( $add_to_cart_url, $wc_prod_object ) {
		if ( strpos( $add_to_cart_url, 'wpv-ajax-pagination' ) !== false
			|| ( defined( 'DOING_AJAX' )
				&& DOING_AJAX
				&& isset( $_REQUEST['action'] )
				&& $_REQUEST['action']  == 'wpv_update_filter_form' ) )
		{
			$parsed = array();
			$parsed = parse_url( $add_to_cart_url );
			if ( isset( $parsed['query'] ) ) {
				wp_parse_str( $parsed['query'], $parsed_query );

				// Only alter the URL if it already contains a numeric add-to-cart parameter
				if ( isset( $parsed_query['add-to-cart'] ) && is_numeric( $parsed_query['add-to-cart'] ) ) {

					// If the product is a variation, we need to handle the variation_id parameter too
					if ( isset( $wc_prod_object->product_type )
						&& $wc_prod_object->product_type == 'variation'
						&& isset( $wc_prod_object->variation_id )
						&& isset( $wc_prod_object->variation_data )
					) {
						$query_args_to_add = array_merge( array( 'variation_id' => $wc_prod_object->variation_id ), $wc_prod_object->variation_data );
					} else {
						$query_args_to_add = array();
					}

					// Build the base URL, it should have a referrer being the actual current page
					if ( wp_get_referer() ) {
						$base_url = wp_get_referer();
					} else {
						$base_url = get_home_url();
					}

					// Modify the URL
					$query_args_to_add['add-to-cart'] = $parsed_query['add-to-cart'];
					$add_to_cart_url = remove_query_arg( 'added-to-cart', add_query_arg( $query_args_to_add, $base_url ) );
				}
			}
		}
		return $add_to_cart_url;
	}


	function widgets_init(){
		register_widget( 'WPV_Widget' );
		register_widget( 'WPV_Widget_filter' );
	}


	function register_CCK_type( $type ) {
		$this->CCK_types[] = $type;
	}


	function can_include_type($type) {
		return !in_array( $type, $this->CCK_types );
	}


	/**
	* Creates the admin menus and submenus items when using the embedded version.
	*
	* @since unknown
	*/
	function admin_menu() {
		if ( $this->is_embedded() ) {
            $capability = 'manage_options';
            add_menu_page( __( 'Views', 'wpv-views' ), __( 'Views', 'wpv-views' ), $capability, 'embedded-views', 'wpv_admin_menu_embedded_views_listing_page', 'none' );
            add_submenu_page( 'embedded-views', __( 'Views', 'wpv-views' ), __( 'Views', 'wpv-views' ), $capability, 'embedded-views', 'wpv_admin_menu_embedded_views_listing_page' );
			if ( 
				isset( $_GET['page'] ) 
				&& 'views-embedded' == $_GET['page'] 
			) {
				add_submenu_page( 'embedded-views', __( 'Embedded View', 'wpv-views' ), __( 'Embedded View', 'wpv-views' ), $capability, 'views-embedded', 'views_embedded_html' );
			}
            add_submenu_page( 'embedded-views', __( 'Content Templates', 'wpv-views' ), __( 'Content Templates', 'wpv-views' ), $capability, 'embedded-views-templates', 'wpv_admin_menu_embedded_views_templates_listing_page' );
			if ( 
				isset( $_GET['page'] ) 
				&& 'view-templates-embedded' == $_GET['page'] 
			) {
				add_submenu_page( 'embedded-views', __( 'Embedded Content Template', 'wpv-views' ), __( 'Embedded Content Template', 'wpv-views' ), $capability, 'view-templates-embedded', 'content_templates_embedded_html');
			}
            add_submenu_page( 'embedded-views', __( 'WordPress Archives', 'wpv-views' ), __( 'WordPress Archives', 'wpv-views' ), $capability, 'embedded-views-archives', 'wpv_admin_menu_embedded_views_archives_listing_page' );
			if ( 
				isset( $_GET['page'] ) 
				&& 'view-archives-embedded' == $_GET['page'] 
			) {
				add_submenu_page( 'embedded-views', __( 'Embedded WordPress Archive', 'wpv-views' ), __( 'Embedded WordPress Archive', 'wpv-views' ), $capability, 'view-archives-embedded', 'view_archives_embedded_html');
			}
        }
    }

	function settings_box_load() {
		global $pagenow;
		if ( $pagenow == 'options-general.php' && isset( $_GET['page'] ) && $_GET['page'] == 'wpv-import-theme' ) {
			$this->include_admin_css();
		}
	}


	function include_admin_css() {
		printf(
				'<link rel="stylesheet" href="%s" type="text/css" media="all" />',
				add_query_arg( array( 'v' => WPV_VERSION ), WPV_URL . '/res/css/wpv-views.css' ) );
	}


	function save_view_settings( $post_id ){
		// do nothing in the theme version.
	}
	
	function after_save_item( $item_id ) {
		// do nothing in the embedded version
	}
	
	function after_import_item( $item_id ) {
		if (
			! is_numeric( $item_id )
			|| intval( $item_id ) < 1
		) {
			return;
		}
        delete_post_meta( $item_id, '_toolset_edit_last' );
	}

    /**
     * Return View ID given the slug or the name
     * @param type $atts shortcode attributes
     * @return type int View ID
     */
	function get_view_id( $atts ) {
		global $wpdb;
		extract( shortcode_atts(
				array(
						'id'	=> false,
						'name'  => false ),
				$atts ) );

		if ( 
			empty( $id ) 
			&& ! empty( $name ) 
		) {
			// lookup by post title first
			$id = $wpdb->get_var( 
				$wpdb->prepare( 
					"SELECT ID FROM {$wpdb->posts} 
					WHERE post_type = 'view' 
					AND post_title = %s 
					LIMIT 1",
					$name 
				) 
			);
			if ( ! $id ) {
				// try the post name
				$id = $wpdb->get_var( 
					$wpdb->prepare( 
						"SELECT ID FROM {$wpdb->posts} 
						WHERE post_type = 'view' 
						AND post_name = %s 
						LIMIT 1",
						$name 
					) 
				);
			}
		}
        
		return $id;
	}


	/**
	 * Process the view shortcode.
	 *
	 * eg. [wpv-view name='my-view']
	 */
	function short_tag_wpv_view( $atts ){

		global $wplogger, $WPVDebug;
		$wplogger->log( $atts );

		apply_filters( 'wpv_shortcode_debug', 'wpv-view', json_encode($atts), '' , 'Output shown in the Nested elements section' );

		$id = $this->get_view_id( $atts );

		if( empty( $id ) ) {
			return sprintf( '<!- %s ->', __( 'View not found', 'wpv-views' ) );
		}

		$this->view_used_ids[] = $id;

		array_push( $this->view_shortcode_attributes, $atts );
		$out = $this->render_view_ex( $id, md5( serialize( $atts ) ) );
		array_pop( $this->view_shortcode_attributes );
		return $out;

	}


	/**
	 * Process the view shortcode.
	 *
	 * eg. [wpv-form-view name='my-view' target_id='xx']
	 */
	function short_tag_wpv_view_form( $atts ) {
		global $sitepress;

		global $wplogger;
		$wplogger->log( $atts );

		apply_filters( 'wpv_shortcode_debug', 'wpv-form-view', json_encode($atts), '', 'Output shown in the Nested elements section' );

		extract( shortcode_atts(
			array(
					'id' => false,
					'name' => false,
					'target_id' => 'self'
			),
			$atts ) 
		);

		$id = $this->get_view_id( $atts );

		if( empty( $id ) ) {
			return sprintf( '<!- %s ->', __( 'View not found', 'wpv-views' ) );
		}

		if ( empty( $target_id ) || $target_id == 'self' ) {
			$target_id = 'self';
			$url = $_SERVER['REQUEST_URI'];
			if ( strpos( $url, 'wpv-ajax-pagination' ) !== false
				|| ( defined('DOING_AJAX')
					&& DOING_AJAX
					&& isset( $_REQUEST['action'] )
					&& $_REQUEST['action']  == 'wpv_update_filter_form' ) )
			{
				if ( wp_get_referer() ) {
					$url = wp_get_referer();
				}
			}
		} else {
			if ( is_numeric( $target_id ) ) {
				$url = get_permalink( $target_id );
			} else {
				return sprintf( '<!- %s ->', __( 'target_id not valid', 'wpv-views' ) );
			}
		}

		$this->view_used_ids[] = $id;
		array_push( $this->view_shortcode_attributes, $atts );

		//$this->returned_ids_for_parametric_search = array();

		$this->rendering_views_form_in_progress = true;

		$out = '';

		$view_settings = $this->get_view_settings( $id );
		if ( isset( $view_settings['filter_meta_html'] ) ) {

			$this->view_depth++;
			array_push( $this->view_ids, $this->current_view );
			$this->current_view = $id;

			// increment the view count.
			if ( !isset( $this->view_count[ $this->view_depth ] ) ) {
				$this->view_count[ $this->view_depth ] = 0;
			}
			$this->view_count[ $this->view_depth ]++;

			$form_class = array( 'js-wpv-form-only' );

			// Dependant stuff
			$dps_enabled = false;
			$counters_enabled = false;
			if ( !isset( $view_settings['dps'] ) || !is_array( $view_settings['dps'] ) ) {
				$view_settings['dps'] = array();
			}
			if ( isset( $view_settings['dps']['enable_dependency'] ) && $view_settings['dps']['enable_dependency'] == 'enable' ) {
				$dps_enabled = true;
				$controls_per_kind = wpv_count_filter_controls( $view_settings );
				$controls_count = 0;
				$no_intersection = array();
				if ( !isset( $controls_per_kind['error'] ) ) {
					// $controls_count = array_sum( $controls_per_kind );
					$controls_count = $controls_per_kind['cf'] + $controls_per_kind['tax'] + $controls_per_kind['pr'] + $controls_per_kind['search'];
					if ( $controls_per_kind['cf'] > 1 && ( !isset( $view_settings['custom_fields_relationship'] ) || $view_settings['custom_fields_relationship'] != 'AND' ) ) {
						$no_intersection[] = __( 'custom field', 'wpv-views' );
					}
					if ( $controls_per_kind['tax'] > 1 && ( !isset( $view_settings['taxonomy_relationship'] ) || $view_settings['taxonomy_relationship'] != 'AND' ) ) {
						$no_intersection[] = __( 'taxonomy', 'wpv-views' );
					}
				} else {
					$dps_enabled = false;
				}
				if ( $controls_count > 0 ) {
					if ( count( $no_intersection ) > 0 ) {
						$dps_enabled = false;
					}
				} else {
					$dps_enabled = false;
				}
			}
			if ( !isset( $view_settings['filter_meta_html'] ) ) {
				$view_settings['filter_meta_html'] = '';
			}
			if ( strpos( $view_settings['filter_meta_html'], '%%COUNT%%' ) !== false ) {
				$counters_enabled = true;
			}
			if ( $dps_enabled || $counters_enabled ) {
				// TODO review this, makes little sense
				if ( $dps_enabled ) {
					$form_class[] = 'js-wpv-dps-enabled';
				}
				wpv_filter_extend_query_for_parametric_and_counters( array(), $view_settings, $id );
			} else {
				// Set the force value
				$this->set_force_disable_dependant_parametric_search( true );
			}

			if ( ! isset( $view_settings['dps']['ajax_results'] ) ) {
				$view_settings['dps']['ajax_results'] = 'disable';
			}
			if ( ! isset( $view_settings['dps']['ajax_results_submit'] ) ) {
				$view_settings['dps']['ajax_results_submit'] = 'reload';
			}
			$ajax = $view_settings['dps']['ajax_results'];
			$ajax_submit = $view_settings['dps']['ajax_results_submit'];
			// Disable AJAX results when the target page is set and is not the current one, since there should be no results here whatsoever
			// (and if there are, they belong to a page that should not be targeted by this form)
			$current_page = $this->get_top_current_page();
			if (
				$target_id != 'self'
				&& (
					! $current_page
					|| $current_page->ID != $target_id
				)
			) {
				$ajax = 'disable';
				$ajax_submit = 'reload';
			}

			if ( $ajax == 'enable' ) {
				$form_class[] = 'js-wpv-ajax-results-enabled';
			} else if ( $ajax == 'disable' && $ajax_submit == 'ajaxed' ) {
				$form_class[] = 'js-wpv-ajax-results-submit-enabled';
			}

			$page = 1;

			$effect = 'fade';
			$ajax_pre_before = '';
			if ( isset( $view_settings['dps']['ajax_results_pre_before'] ) ) {
				$ajax_pre_before = esc_attr( $view_settings['dps']['ajax_results_pre_before'] );
			}
			$ajax_before = '';
			if ( isset( $view_settings['dps']['ajax_results_before'] ) ) {
				$ajax_before = esc_attr( $view_settings['dps']['ajax_results_before'] );
			}
			$ajax_after = '';
			if ( isset( $view_settings['dps']['ajax_results_after'] ) ) {
				$ajax_after = esc_attr( $view_settings['dps']['ajax_results_after'] );
			}

			//$url = get_permalink($target_id);
			if( isset( $sitepress ) ) {
				// Dirty hack to be able to use the wpml_content_fix_links_to_translated_content() function
				// It will take a string, parse its links based on <a> tag and return the translated link
				// TODO use the icl_object_id function instead
				$url = '<a href="' . $url . '"></a>';
				$url = wpml_content_fix_links_to_translated_content($url);
				$url = substr( $url, 9, -6 );
			}

			$out .= '<form autocomplete="off" action="' . $url . '" method="get" class="wpv-filter-form js-wpv-filter-form js-wpv-filter-form-' . $this->get_view_count() . ' ' . implode( ' ', $form_class ) . '" data-viewnumber="' . $this->get_view_count() . '" data-targetid="' . $target_id . '" data-viewid="' . $id . '">';

			$out .= '<input type="hidden" class="js-wpv-dps-filter-data js-wpv-filter-data-for-this-form" data-action="' . $url . '" data-page="' . $page . '" data-ajax="disable" data-effect="' . $effect . '" data-ajaxprebefore="' . $ajax_pre_before . '" data-ajaxbefore="' . $ajax_before . '" data-ajaxafter="' . $ajax_after . '" />';

			// Set a hidden input for the View attributes, so we can pass them if needed
			$view_attrs = $atts;
			if ( isset( $view_attrs['name'] ) ) {
				unset( $view_attrs['name'] );
			}
			if ( isset( $view_attrs['target_id'] ) ) {
				unset( $view_attrs['target_id'] );
			}
			if ( !empty( $view_attrs ) && is_array( $view_attrs ) ) {
				$att_data = '';
				foreach ( $view_attrs as $att_key => $att_val ) {
					$att_data .= ' data-' . $att_key . '="' . esc_attr( $att_val ) . '"';
				}
				$out .= '<input type="hidden" class="js-wpv-view-attributes"' . $att_data . ' />';
			}

			// add hidden inputs for any url parameters.
			// We need these for when the form is submitted.
			$url_query = parse_url( $url, PHP_URL_QUERY );
			if ( $url_query != '' ) {
				$query_parts = explode( '&', $url_query );
				foreach( $query_parts as $param ) {
					$item = explode( '=', $param );
					if ( strpos( $item[0], 'wpv_' ) !== 0 ) {
						$out .= '<input id="wpv_param_' . $item[0] . '" type="hidden" name="' . $item[0] . '" value="' . $item[1] . '" />' . "\n";
					}
				}
			}

			$meta_html = $view_settings['filter_meta_html'];
			$fixmatches = '';

			if(	preg_match( '#\\[wpv-filter-start.*?\](.*?)\\[\wpv-filter-end\\]#is', $meta_html, $matches ) ) {

				$fixmatches = str_replace( ' hide="true"', '', $matches[1] );

			} else if( preg_match( '#\\[wpv-filter-controls\\](.*?)\\[\/wpv-filter-controls\\]#is', $meta_html, $matches ) ) {

				$fixmatches = str_replace( ' hide="true"', '', $matches[0] );

			} elseif( preg_match( '#\\[wpv-control.*?\\]#is', $meta_html ) || preg_match( '#\\[wpv-filter-search-box.*?\]#is', $meta_html ) ) {

				if(	preg_match( '#\\[wpv-filter-start.*?\](.*?)\\[\wpv-filter-end\\]#is', $meta_html, $matches ) ) {
					$fixmatches = str_replace( ' hide="true"', '', $matches[1] );
				}
			}

			$out .= wpv_do_shortcode( $fixmatches );
			$out .= '</form>';

			$this->current_view = array_pop( $this->view_ids );
			if ( $this->current_view == null ) {
				$this->current_view = $id;
			}
			$this->view_depth--;

		}

		array_pop( $this->view_shortcode_attributes );

		//$this->returned_ids_for_parametric_search = array();
		$this->rendering_views_form_in_progress = false;

		return $out;
	}


	function rendering_views_form() {
		return $this->rendering_views_form_in_progress;
	}


	function get_current_page() {
		$aux_array = $this->current_page;
		return end( $aux_array );
	}


	function get_view_shortcodes_attributes() {
		$aux_array = $this->view_shortcode_attributes;
		return end( $aux_array );
	}


	function get_top_current_page() {
		if ( is_single() || is_page() ) {
			// In this case, check directly the current page - needed to make the post_type_dont_include_current_page setting work in AJAX pagination
			global $wp_query;
			if ( isset( $wp_query->posts[0] ) ) {
				$current_post = $wp_query->posts[0];
				return $current_post;
			} else {
				return $this->top_current_page;
			}
		} else {
			return $this->top_current_page;
		}
	}


	/**
	 * Get the current view we are processing.
	 */
	function get_current_view() {
		return $this->current_view;
	}


	/**
	 * Get the current view count.
	 */
	function get_view_count() {
		$attr = $this->get_view_shortcodes_attributes();
		$ignore = array(
				'name',
				'id',
				'target_id',
				'view_display' );
		foreach ( $ignore as $ig_key ) {
			if ( isset( $attr[ $ig_key ] ) ) {
				unset( $attr[ $ig_key ] );
			}
		}

		return $this->current_view . '-' . md5( serialize( $attr ) );

		/*
		if (isset($this->set_view_counts[$this->current_view])) {
			return $this->set_view_counts[$this->current_view];
		} else {
			return 10000 * ($this->view_depth - 1) + $this->view_count[$this->view_depth];
		}
		*/
	}


	function set_view_count( $count, $view_id ) {
		if ( $view_id ) {
			$this->set_view_counts[ $view_id ] = $count;
		} else {
			$this->view_count[ $this->view_depth ] = $count;
		}
	}


	/**
	 * Get the view settings for a given or the current View.
	 *
	 * @param integer $view_id View post ID.
	 * @param array|null $post_meta If not null, this value will be used instead of querying the '_wpv_settings'
	 *	 postmeta of given View. Please refer to wpv_prepare_view_listing_query() to understand why it is necessary
	 *	- usually because we already got the _wpv_settings postmeta for the View and just want to normalize  and filter the output
	 *
	 * @return array View's settings.
	 *
	 * @since unknown
	 */
	 
	function get_view_settings( $view_id = null, $post_meta = null ) {
		if ( is_null( $view_id ) ) {
			$view_id = $this->get_current_view();
		}
		// Normalize _wpv_settings postmeta if we got that earlier
		if ( null == $post_meta ) {
			$post_meta = (array) get_post_meta( $view_id, '_wpv_settings', true );
		}
		
		/**
		* wpv_view_settings
		*
		* Internal filter to set some View settings that will overwrite the ones existing in the _wpv_settings postmeta
		* Usually used to set default values that need to be there on the returned array
		* Use wpv_filter_override_view_settings to override View settings - like on the Theme Frameworks integration
		*
		* @param $post_meta (array) Unserialized array of the _wpv_settings postmeta
		* @param $view_id (integer) The View ID
		*
		* @return $view_settings (array) The View settings
		*
		* @since unknown
		*/
		
		$view_settings = apply_filters( 'wpv_view_settings', $post_meta, $view_id );
		
		/**
		* wpv_filter_override_view_settings
		*
		* Public filter to set some View settings that will overwrite the ones existing in the _wpv_settings postmeta
		* For example, on the Theme Frameworks integration
		*
		* @param $view_settings (array) The View settings
		* @param $view_id (integer) The View ID
		*
		* @return $view_settings (array) The View settings
		*
		* @since 1.8.0
		*/
		
		$view_settings = apply_filters( 'wpv_filter_override_view_settings', $view_settings, $view_id );

		return $view_settings;
	}
	
	/**
	* wpv_view_settings_set_fallbacks
	*
	* Callback hooked into the wpv_view_settings filter to set default values
	* that should be in the _wpv_settings postmeta but might be missing somehow
	*
	* @param $view_settings (array)
	* @param $view_id (integer)
	*
	* @return $view_settings (array)
	*
	* @since 1.8.0
	*/
	
	function wpv_view_settings_set_fallbacks( $view_settings, $view_id ) {
		if ( ! is_array( $view_settings ) ) {
			$view_settings = array();
		}
		// Query mode
		if ( ! isset( $view_settings['view-query-mode'] ) ) {
			$view_settings['view-query-mode'] = 'normal';
		}
		return $view_settings;
	}


	/**
	 * Get the view layout settings for a given or the current View.
	 *
	 * @param integer $view_id View post ID.
	 * @param array|null $post_meta If not null, this value will be used instead of querying the '_wpv_layout_settings'
	 *	 postmeta of given View. Please refer to wpv_prepare_view_listing_query() to understand why it is necessary
	 *	- usually because we already got the _wpv_layout_settings postmeta for the View and just want to normalize  and filter the output
	 *
	 * @return array View's settings.
	 *
	 * @since unknown
	 */
	 
	function get_view_layout_settings( $view_id = null, $post_meta = null ) {
		if ( is_null( $view_id ) ) {
			$view_id = $this->get_current_view();
		}
		// Normalize _wpv_layout_settings postmeta if we got that earlier
		if ( null == $post_meta ) {
			$post_meta = (array) get_post_meta( $view_id, '_wpv_layout_settings', true );
		}
		
		/**
		* wpv_view_layout_settings
		*
		* Internal filter to set some View layout settings that will overwrite the ones existing in the _wpv_layout_settings postmeta
		* Usually used to set default values that need to be there on the returned array
		* Use wpv_filter_override_view_layout_settings to override View layout settings
		*
		* @param $post_meta (array) Unserialized array of the _wpv_layout_settings postmeta
		* @param $view_id (integer) The View ID
		*
		* @return $view_layout_settings (array) The View layout settings
		*
		* @since 1.8.0
		*/
		
		$view_layout_settings = apply_filters( 'wpv_view_layout_settings', $post_meta, $view_id );
		
		/**
		* wpv_filter_override_view_layout_settings
		*
		* Public filter to set some View layout settings that will overwrite the ones existing in the _wpv_layout_settings postmeta
		*
		* @param $view_layout_settings (array) The View layout settings
		* @param $view_id (integer) The View ID
		*
		* @return $view_layout_settings (array) The View layout settings
		*
		* @since 1.8.0
		*/
		
		$view_layout_settings = apply_filters( 'wpv_filter_override_view_layout_settings', $view_layout_settings, $view_id );
		
		return $view_layout_settings;
	}


	/**
	 * Keep track of the current view and render the view.
	 */
	function render_view_ex( $id, $hash ){

		global $post, $WPVDebug;

		$this->view_depth++;
		$WPVDebug->wpv_debug_start( $id, $this->view_shortcode_attributes );
		//$this->returned_ids_for_parametric_search = array();

        $post_exists = ( isset( $post ) && $post instanceof WP_Post );

		if ( $this->top_current_page == null ) {
			$this->top_current_page = ( $post_exists ? clone $post : null );
		}

		array_push( $this->current_page, $post_exists ? clone $post : null );

		array_push( $this->view_ids, $this->current_view );
		
		// Adjust for WPML support
		// Although Views are not translatable anymore, keep for backwards compatibility
		$id = apply_filters( 'translate_object_id', $id, 'view', true, null );

		$this->current_view = $id;

		array_push( $this->post_query_stack, $this->post_query );

		// save original taxonomy term if any
		$tmp_parent_taxonomy = $this->parent_taxonomy;
		if ( isset( $this->taxonomy_data['term'] ) ) {
			$this->parent_taxonomy = $this->taxonomy_data['term']->term_id;
		} else {
			$this->parent_taxonomy = 0;
		}
		$tmp_taxonomy_data = $this->taxonomy_data;

		// save original users if any
		$tmp_parent_user = $this->parent_user;
		if ( isset( $this->users_data['term'] ) ) {
			$this->parent_user = $this->users_data['term']->ID;
		} else {
			$this->parent_user = 0;
		}
		$tmp_users_data = $this->users_data;

		$out =  $this->render_view( $id, $hash );

		$out = wpv_do_shortcode( $out );

		$this->taxonomy_data = $tmp_taxonomy_data;
		$this->parent_taxonomy = $tmp_parent_taxonomy;

		$this->users_data = $tmp_users_data;
		$this->parent_user = $tmp_parent_user;

		$this->current_view = array_pop( $this->view_ids );
		if ( $this->current_view == null ) {
			$this->current_view = $id;
		}

		array_pop( $this->current_page );

		$this->post_query = array_pop( $this->post_query_stack );

		$this->view_depth--;
		$WPVDebug->wpv_debug_end();

		//$this->returned_ids_for_parametric_search = array();
		return $out;
	}


	/**
	 * Render the view and loops through the found posts
	 */
	function render_view( $view_id, $hash ){

		global $post, $WPVDebug, $wplogger;

		static $processed_views = array();

		// increment the view count.
		// TODO this code is duplicated, maybe create function for it?
		if ( !isset( $this->view_count[ $this->view_depth ] ) ) {
			$this->view_count[ $this->view_depth ] = 0;
		}
		$this->view_count[ $this->view_depth ]++;

		$view = get_post( $view_id );
		$this->view_used_ids[] = $view_id;

		$out = '';

		$view_caller_id = ( isset( $post ) && isset( $post->ID ) ) ? get_the_ID() : 0; // post or widget

		if( !isset( $processed_views[ $view_caller_id ][ $hash ] ) || 0 === $view_caller_id ) {
			//$processed_views[$view_caller_id][$hash] = true; // mark view as processed for this post

            $status = get_post_status( $view_id );

            // Views should be 'publish'ed to be allowed to produce an output
            // FIXME: Check also that user has permissions to render this view
			if( !empty( $view ) && $status == 'publish' ) {

				$post_content = $view->post_content;

				// apply the layout meta html if we have some.
				$view_layout_settings = $this->get_view_layout_settings();

				if ( isset( $view_layout_settings['layout_meta_html'] ) ) {
					$post_content = str_replace('[wpv-layout-meta-html]', $view_layout_settings['layout_meta_html'], $post_content );
				}

				$post_content = wpml_content_fix_links_to_translated_content( $post_content );

				$view_settings = $this->get_view_settings();

				// find the loop
				if( preg_match( '#\<wpv-loop(.*?)\>(.*)</wpv-loop>#is', $post_content, $matches ) ) {
					// get the loop arguments.
					$args = $matches[1];
					$exp = array_map( 'trim', explode( ' ', $args ) );
					$args = array();
					foreach( $exp as $e ){
						$kv = explode( '=', $e );
						if ( sizeof( $kv ) == 2 ) {
							$args[ $kv[0] ] = trim( $kv[1] ,'\'"');
						}
					}
					if ( isset( $args[ 'wrap' ] ) ) {
						$args['wrap'] = intval( $args['wrap'] );
					}
					if ( isset( $args['pad'] ) ) {
						$args['pad'] = $args['pad'] == 'true';
					} else {
						$args['pad'] = false;
					}

					// Get templates for items (differentiated by their indices, see [wpv-item] documentation).
					$tmpl = $matches[2];
					$item_indexes = $this->_get_item_indexes( $tmpl );

					if ( $view_settings['query_type'][0] == 'posts' ) {
						// get the posts using the query settings for this view.

						$archive_query = null;
						if ( $view_settings['view-query-mode'] == 'archive' ) {

							// check for an archive loop
							global $WPV_view_archive_loop;
							if ( isset( $WPV_view_archive_loop ) ) {
								$archive_query = $WPV_view_archive_loop->get_archive_loop_query();
							}

						} else if( $view_settings['view-query-mode'] == 'layouts-loop' ) {
							global $wp_query;
							$archive_query = ( isset( $wp_query ) && $wp_query instanceof WP_Query ) ? clone $wp_query : null;
						}

						if ( $archive_query ) {
							$this->post_query = $archive_query;
						} else {
							$this->post_query = wpv_filter_get_posts( $view_id );
						}
						$items = $this->post_query->posts;

						$wplogger->log( 'Found '. count( $items ) . ' posts' );

						if ( $wplogger->isMsgVisible( WPLOG_DEBUG ) ) {
							// simplify the output
							$out_items = array();
							foreach( $items as $item ) {
								$out_items[] = array( 'ID' => $item->ID, 'post_title' => $item->post_title );
							}
							$wplogger->log( $out_items, WPLOG_DEBUG );
						}

					}

					// save original post
					global $post, $authordata, $id;
					$tmp_post = ( isset( $post ) && $post instanceof WP_Post ) ? clone $post : null;
					$tmp_authordata = ( isset( $authordata ) && is_object( $authordata ) ) ? clone $authordata : null;
					$tmp_id = $id;

					if ( $view_settings['query_type'][0] == 'taxonomy') {
						$items = $this->taxonomy_query( $view_settings );
						$wplogger->log( $items, WPLOG_DEBUG );
						// taxonomy views can be recursive so remove from the processed array
						//unset($processed_views[$view_caller_id][$hash]);
					} else if ( $view_settings['query_type'][0] == 'users') {
						$items = $this->users_query( $view_settings );
						$wplogger->log( $items, WPLOG_DEBUG );
					}

                    global $WPV_settings;
					if ( isset( $WPV_settings->wpv_debug_mode ) && !empty( $WPV_settings->wpv_debug_mode ) ) {
						$WPVDebug->add_log( 'items_count', count( $items ) );
					}

					// The actual loop - render all items
					$loop = '';
					for( $i = 0; $i < count( $items ); $i++) {
						$WPVDebug->set_index();
						$index = $i;

						if ( isset( $args['wrap'] ) ) {
							$index %= $args['wrap'];
						}

						// [wpv-item index=xx] uses base 1
						$index++;
						$index = strval( $index );

						if ( $view_settings['query_type'][0] == 'posts' ) {

							$post = clone $items[ $i ];
							$authordata = new WP_User( $post->post_author );
							$id = $post->ID;
							$temp_variables = $this->variables;
							$this->variables = array();
							do_action( 'wpv-before-display-post', $post, $view_id );

						} elseif ( $view_settings['query_type'][0] == 'taxonomy' ) {

							$this->taxonomy_data['term'] = $items[ $i ];
							do_action( 'wpv-before-display-taxonomy', $items[ $i ], $view_id );

						} elseif ( $view_settings['query_type'][0] == 'users' ) {

							$user_id = $items[ $i ]->ID;
							$user_meta = get_user_meta( $user_id );
							$items[ $i ]->meta = $user_meta;
							$this->users_data['term'] = $items[ $i ];
							do_action( 'wpv-before-display-user', $items[ $i ], $view_id );
						}
						$WPVDebug->add_log( $view_settings['query_type'][0] , $items[ $i ] );

						// first output the "all" index.
						$shortcodes_output = wpv_do_shortcode( $item_indexes['all'] );
						$loop .= $shortcodes_output;
						$WPVDebug->add_log_item( 'shortcodes', $item_indexes['all'] );
						$WPVDebug->add_log_item( 'output', $shortcodes_output );

						/* Select a template for this item based on it's index.
						 * Note: It is possible that we won't be rendering this item's content if the index 'other'
						 * isn't set and there is no other match. */
						$selected_index = null;
						if ( isset( $item_indexes[ $index ] ) ) {
							$selected_index = $index;
						} elseif ( isset( $item_indexes['odd'] ) && ( $index % 2 == 1 ) ) {
							$selected_index = 'odd';
						} elseif ( isset( $item_indexes['even'] ) && ( $index % 2 == 0 ) ) {
							$selected_index = 'even';
						} elseif ( isset( $item_indexes['other'] ) ) {
							$selected_index = 'other';
						}

						// Output the item with appropriate template (if we found one)
						if( null !== $selected_index ) {
							$shortcodes_output = wpv_do_shortcode( $item_indexes[ $selected_index ] );
							$loop .= $shortcodes_output;
							$WPVDebug->add_log_item( 'shortcodes', $item_indexes[ $selected_index ] );
							$WPVDebug->add_log_item( 'output', $shortcodes_output );
						}

						// Do wpv-after-display-* action after displaying the item
						if ( $view_settings['query_type'][0] == 'posts' ) {
							do_action( 'wpv-after-display-post', $post, $view_id );
							$this->variables = $temp_variables;
						} elseif ( $view_settings['query_type'][0] == 'taxonomy' ) {
							do_action( 'wpv-after-display-taxonomy', $items[ $i ], $view_id );
						} elseif ( $view_settings['query_type'][0] == 'users' ) {
							do_action( 'wpv-after-display-user', $items[ $i ], $view_id );
						}

					}

					// see if we should pad the remaining items.
					if ( isset( $args['wrap'] ) && isset( $args['pad'] ) ) {
						while ( ( $i % $args['wrap'] ) && $args['pad'] ) {
							$index = $i;
							$index %= $args['wrap'];

							if( $index == $args['wrap'] - 1 ) {
								$loop .= wpv_do_shortcode( $item_indexes['pad-last'] );
							} else {
								$loop .= wpv_do_shortcode( $item_indexes['pad'] );
							}

							$i++;
						}
					}

					$WPVDebug->clean_index();

					$out .= str_replace( $matches[0], $loop, $post_content );

					// restore original $post
					$post = ( isset( $tmp_post ) && ( $tmp_post instanceof WP_Post ) ) ? clone $tmp_post : null;
					$authordata = ( isset( $tmp_authordata ) && is_object( $tmp_authordata ) ) ? clone $tmp_authordata : null;
					$id = $tmp_id;

				}

			} else {
				$out .= sprintf( '<!- %s ->', __( 'View not found', 'wpv-views' ) );
			}

		} else {

			if( $processed_views[ $view_caller_id ][ $hash ] !== true ) {
				// use output from cache
				$out .= $processed_views[ $view_caller_id ][ $hash ];
			}

		}

		return $out;
	}


	/**
	 * Get the html for each of the wpv-item index.
	 *
	 * <wpv-loop wrap=8 pad=true>
	 * Output for all items
	 * [wpv-item index=1]
	 * Output for item 1
	 * [wpv-item index=4]
	 * Output for item 4
	 * [wpv-item index=8]
	 * Output for item 8
	 * [wpv-item index=odd]
	 * Output for odd items (if they have no output defined by their order)
	 * [wpv-item index=even]
	 * Output for even items (if they have no output defined by their order)
	 * [wpv-item index=others]
	 * Output for other items
	 * [wpv-item index=pad]
	 * Output for when padding is required
	 * [wpv-item index=pad-last]
	 * Output for the last item when padding is required
	 * </wpv-loop>
	 *
	 * Will return an array with the output for each index.
	 *
	 * e.g. array('all' => 'Output for all items',
	 *		  '1' => 'Output for item 1',
	 *		  '4' => 'Output for item 4',
	 *		  '8' => 'Output for item 8',
	 *		  'other' => 'Output for other items',
	 *		  )
	 *
	 */
	function _get_item_indexes( $template ) {
		$indexes = array();
		$indexes['all'] = '';
		$indexes['pad'] = '';
		$indexes['pad-last'] = '';

		// search for the [wpv-item index=xx] shortcode
		$found = false;
		$last_index = -1;

		while( preg_match( '#\\[wpv-item index=([^\[]+)\]#is', $template, $matches ) ) {

			$pos = strpos( $template, $matches[0] );

			if ( !$found ) {
				// found the first one.
				// use all the stuff before for the all index.
				$indexes['all'] = substr( $template, 0, $pos );
				$found = true;
			} else if ( $last_index != -1 ) {
				// All the stuff before belongs to the previous index
				$indexes[ $last_index ] = substr( $template, 0, $pos );
			}

			$template = substr( $template, $pos + strlen( $matches[0] ) );

			$last_index = $matches[1];

		}

		if ( !$found ) {
			$indexes['all'] = $template;
		} else {
			$indexes[ $last_index ] = $template;
		}

		return $indexes;
	}


	/**
	 * Get the current post query.
	 */
	function get_query() {
		return $this->post_query;
	}


	/**
	 * Add the view button to the toolbar of required edit pages.
	 *
	 * Also force the view editor to be in HTML mode.
	 */
	function post_edit_tinymce() {
		global $post;

		if( is_object( $post ) === false ) {
			return;
		}

		if ( $post->post_type != 'view-template' ) {
			$this->editor_addon = new Editor_addon(
					'wpv-views',
					__( 'Insert Views Shortcodes', 'wpv-views' ),
					WPV_URL . '/res/js/views_editor_plugin.js',
					'',
					true,
					'icon-views-logo ont-icon-18 ont-color-gray' );

			// add tool bar to other edit pages so they can insert the view shortcodes.
			add_short_codes_to_js( array( 'view', 'wpml','body-view-templates-posts' ), $this->editor_addon, 'add-basics' );
		}
	}


	/**
	 * Get all the views that have been created.
	 */
	function get_views() {
		$views = get_posts( array(
				'post_type' => 'view',
				'post_status' => 'publish',
				'numberposts' => -1 ) );
		return $views;
	}


	/**
	 * New method to get Content templates for module manager.
	 */
	function get_view_templates() {
		$view_templates = get_posts( array(
				'post_type' => 'view-template',
				'post_status' => 'publish',
				'numberposts' => -1 ) );
		return $view_templates;
	}


	function get_view_titles() {
		global $wpdb;
		static $views_available = null;
		if ( $views_available === null ) {
			$views_available = array();
			$views = $wpdb->get_results( "SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type='view'" );
			foreach ( $views as $view ) {
				$views_available[ $view->ID ] = $view->post_title;
			}
		}
		return $views_available;
	}


	/**
	 * Add the frontend styles and scripts.
	 */
	function wpv_frontend_enqueue_scripts() {

		// Datepicker script bundled with WordPress
		wp_enqueue_script( 'jquery-ui-datepicker' );

		// Datepicker localization
		$lang = get_locale();
		$lang = str_replace( '_', '-', $lang );
		$real_lang = false;

		// TODO integrate this with WPML lang
		if ( file_exists( WPV_PATH_EMBEDDED . '/res/js/i18n/jquery.ui.datepicker-' . $lang . '.js' ) ) {
			if ( !wp_script_is( 'jquery-ui-datepicker-local-' . $lang, 'registered' ) ) {
				wp_register_script(
						'jquery-ui-datepicker-local-' . $lang,
						WPV_URL_EMBEDDED_FRONTEND . '/res/js/i18n/jquery.ui.datepicker-' . $lang . '.js',
						array('jquery-ui-core', 'jquery', 'jquery-ui-datepicker'),
						WPV_VERSION,
						true );
			}
			if ( !wp_script_is( 'jquery-ui-datepicker-local-' . $lang ) ) {
				wp_enqueue_script( 'jquery-ui-datepicker-local-' . $lang );
			}
			$real_lang = $lang;
		} else {
			$lang = substr( $lang, 0, 2 );
			if ( file_exists( WPV_PATH_EMBEDDED . '/res/js/i18n/jquery.ui.datepicker-' . $lang . '.js' ) ) {
				if ( !wp_script_is( 'jquery-ui-datepicker-local-' . $lang, 'registered' ) ) {
					wp_register_script(
							'jquery-ui-datepicker-local-' . $lang,
							WPV_URL_EMBEDDED_FRONTEND . '/res/js/i18n/jquery.ui.datepicker-' . $lang . '.js',
							array('jquery-ui-core', 'jquery', 'jquery-ui-datepicker'),
							WPV_VERSION,
							true );
				}
				if ( !wp_script_is( 'jquery-ui-datepicker-local-' . $lang ) ) {
					wp_enqueue_script( 'jquery-ui-datepicker-local-' . $lang );
				}
			}
			$real_lang = $lang;
		}

		wp_deregister_style( 'wptoolset-field-datepicker' );
		wp_register_style( 'wptoolset-field-datepicker', WPV_URL_EMBEDDED_FRONTEND . '/common/toolset-forms/css/wpt-jquery-ui/datepicker.css', array(), WPV_VERSION );
		if ( !wp_style_is( 'wptoolset-field-datepicker' ) ) {
			wp_enqueue_style( 'wptoolset-field-datepicker' );
		}

		// Pagination script and style
		wp_register_script( 'views-pagination-script', WPV_URL_EMBEDDED_FRONTEND . '/res/js/wpv-pagination-embedded.js', array( 'jquery', 'jquery-ui-datepicker' ), WPV_VERSION, true );
		wp_enqueue_script( 'views-pagination-script' );

		// Determine AJAX URL
		$ajax_url = trailingslashit( home_url() );

		$permalink_structure = get_option( 'permalink_structure' );

		if ( $permalink_structure != '' ) {
			$ajax_url .= 'wpv-ajax-pagination/';
		} else {
			$ajax_url = plugins_url( 'wpv-ajax-pagination-default.php', __FILE__ );
		}

		// Workaround for IIS servers - See: https://icanlocalize.basecamphq.com/projects/7393061-toolset/todo_items/166116657/comments
		if ( isset( $_SERVER ['SERVER_SOFTWARE'] ) && ( strpos( strtolower ( $_SERVER ['SERVER_SOFTWARE'] ), 'iis' ) !== false ) ) {
			$ajax_url = plugins_url( 'wpv-ajax-pagination-default.php', __FILE__ );
		}

		// NOTE fix possible SSL problems below
		/*
		Only check is_ssl() because the pagination URL must have the same origin as the frontend page requesting it
		if ( ( defined( 'FORCE_SSL_ADMIN' ) && FORCE_SSL_ADMIN ) || is_ssl() ) {
			$ajax_url = str_replace( 'http://', 'https://', $ajax_url );
		}
		*/
		if ( is_ssl() ) {
			$ajax_url = str_replace( 'http://', 'https://', $ajax_url );
		}

		// Localize views-pagination-script.
		$calendar_image = WPV_URL_EMBEDDED_FRONTEND . '/res/img/calendar.gif';
		$calendar_image = apply_filters( 'wpv_filter_wpv_calendar_image', $calendar_image );
		$calendar_image = apply_filters( 'wptoolset_filter_wptoolset_calendar_image', $calendar_image );

		/**
		 * Set minimum selectable date for the datepicker rendered by Views in front-end.
		 *
		 * Please note that using this will influenece all datepicker elements in the frontend page.
		 *
		 * @since 1.7
		 *
		 * @param mixed $minDate Minimum date value which will be passed to datepicker constructor. Following types are supported:
		 *	 - number: A number of days from today.
		 *	 - string: A string in the format of 'ddmmyy' or a relative date.
		 *	 - null: Default value. No minimum date is defined.
		 *
		 * @see http://api.jqueryui.com/datepicker/#option-minDate
		 */
		$datepicker_min_date = apply_filters( 'wpv_filter_wpv_datepicker_min_date', null );

		/**
		 * Set maximum selectable date for the datepicker rendered by Views in front-end.
		 *
		 * See wpv_filter_wpv_datepicker_min_date documentation for more details.
		 */
		$datepicker_max_date = apply_filters( 'wpv_filter_wpv_datepicker_max_date', null );

		$wpv_pagination_localization = array(
				'regional' => $real_lang,
				'front_ajaxurl' => admin_url( 'admin-ajax.php', null ),
				'ajax_pagination_url' => $ajax_url,
				'calendar_image' => $calendar_image,
				'calendar_text' => esc_js( __( 'Select date', 'wpv-views') ),
				'datepicker_min_date' => $datepicker_min_date,
				'datepicker_max_date' => $datepicker_max_date );
		wp_localize_script( 'views-pagination-script', 'wpv_pagination_local', $wpv_pagination_localization );

		wp_register_style( 'views-pagination-style', WPV_URL_EMBEDDED_FRONTEND . '/res/css/wpv-pagination.css', array(), WPV_VERSION );
		wp_enqueue_style( 'views-pagination-style' );

		// Map script - only load it if the Setting is enabled
		if ( ! wp_script_is( 'google-maps', 'registered' ) ) {
			if ( ( defined( 'FORCE_SSL_ADMIN' ) && FORCE_SSL_ADMIN ) || is_ssl() ) {
				$protocol = 'https';
			} else {
				$protocol = 'http';
			}
			wp_register_script( 'google-maps', $protocol . '://maps.googleapis.com/maps/api/js?sensor=false&ver=3.5.2', array(), null, true );
		}
		wp_register_script( 'views-map-script', WPV_URL_EMBEDDED_FRONTEND . '/res/js/jquery.wpvmap.js', array('google-maps', 'jquery'), WPV_VERSION, true );
		global $WPV_settings;
		if ( isset( $WPV_settings->wpv_map_plugin ) && $WPV_settings->wpv_map_plugin != '' ) {
			wp_enqueue_script( 'views-map-script' );
		}
	}


	/**
	 * Called when adding a filter to the view query
	 *
	 * This function will return the html elements for the type of
	 * query that is being added
	 *
	 * DEPRECATED
	 */
	function ajax_get_table_row_ui() {

		if (wp_verify_nonce($_POST['wpv_nonce'], 'wpv_get_table_row_ui_nonce')) {
			$type = apply_filters('wpv_get_table_row_ui_type', $_POST['type_data']);

			$checkboxes = array();
			if (isset($_POST['checkboxes'])) {
				$checkboxes = $_POST['checkboxes'];
			}

			echo call_user_func('wpv_get_table_row_ui_' . $type, $_POST['row'], $_POST['type_data'], $checkboxes, array());
		}

		die();
	}


	/**
	 * Get all the meta keys used in all the posts.
	 *
	 * @return array
	 */
	function get_meta_keys( $include_hidden = false ) {
		global $wpdb;
		static $cf_keys = null;

		if ( $cf_keys == null ) {

			// get the custom field keys
			$cf_keys_limit = 1000; // jic
			$cf_keys = $wpdb->get_col( 
				$wpdb->prepare(
					"SELECT DISTINCT meta_key
					FROM {$wpdb->postmeta}
					ORDER BY meta_key
					LIMIT %d",
					$cf_keys_limit 
				) 
			);

			if ( function_exists( 'wpcf_get_post_meta_field_names' ) ) {
				$types_fields = wpcf_get_post_meta_field_names();
				foreach( $types_fields as $field ) {
					if ( !in_array( $field, $cf_keys ) ) {
						$cf_keys[] = $field;
					}
				}
			}

			// exclude these keys.
			$cf_keys_exceptions = array(
					'_edit_last', '_edit_lock', '_wp_page_template', '_wp_attachment_metadata', '_icl_translator_note', '_alp_processed',
					'_icl_translation', '_thumbnail_id', '_views_template', '_wpml_media_duplicate', '_wpml_media_featured',
					'_top_nav_excluded', '_cms_nav_minihome',
					'wpml_media_duplicate_of', 'wpml_media_lang', 'wpml_media_processed',
					'_wpv_settings', '_wpv_layout_settings', '_wpv_view_sync',
					'_wpv_view_template_fields', '_wpv_view_template_mode',
					'dd_layouts_settings');

			$cf_keys = array_diff( $cf_keys, $cf_keys_exceptions );

			if ( !$include_hidden ) {
                global $WPV_settings;

				if ( isset( $WPV_settings->wpv_show_hidden_fields )  && $WPV_settings->wpv_show_hidden_fields != '' ) {
					$include_these_hidden = explode( ',', $WPV_settings->wpv_show_hidden_fields );
				} else {
					$include_these_hidden = array();
				}

				// exclude hidden fields (starting with an underscore)
				foreach ( $cf_keys as $index => $field ) {
					if ( strpos($field, '_') === 0 ) {
						if ( !in_array( $field, $include_these_hidden ) ) {
							unset( $cf_keys[ $index ] );
						}
					}
				}
			}

			if ( $cf_keys ) {
				natcasesort( $cf_keys );
			}
		}

		return $cf_keys;
	}


	/**
	 * If the post has a view, add an view edit link to post.
	 */
	function edit_post_link( $link, $post_id ) {
		// do nothing for theme version.
		return $link;
	}


	/**
	 * Saves View editor height
	 *
	 * DEPRECATED
	 */
	function save_editor_height() {
		if ( isset( $_POST['height'] ) ) {
			$type = 'view-template';
			setcookie( 'wpv_views_editor_height_' . strval( $type ), intval( $_POST['height'] ), time() + 60*60*24*30, COOKIEPATH, COOKIE_DOMAIN );
		}
	}


	/**
	 * Sets View editor height
	 *
	 * DEPRECATED
	 */
	function set_editor_height() {
		$post_type = get_post_type();
		if (in_array($post_type, array('view-template'))) {
			add_action('admin_footer', array($this, 'editor_height_js'));
		}
	}


	// DEPRECATED
	function editor_height_js() {
		echo '
<script type="text/javascript">
//<![CDATA[
function wpv_views_editor_resize_init() {
		jQuery("#editorcontainer").resizable({
			handles: "s",
			alsoResize: "#content",
			stop: function(event, ui) {
				jQuery.post(ajaxurl, {
					action: "wpv_views_editor_height",
					height: jQuery(this).height()
				});
				jQuery(this).css("width", "100%").find("#content").css("width", "100%");
			}';
		if (isset($_COOKIE['wpv_views_editor_height_' . get_post_type()])) {
			$height = intval($_COOKIE['wpv_views_editor_height_' . get_post_type()]);
			if ($height < 200) {
				$height = 200;
			}
			echo ',
					create: function(event, ui) {
						jQuery("#editorcontainer, #content").css("height", "' . $height . 'px").height(' . $height . ');
					}';
		}
		echo '
		});
	}';
echo '
jQuery(document).ready(function(){
	var timeoutWpvViewsEditorResize = window.setTimeout("wpv_views_editor_resize_init()", 1000);
});

//]]>
</script>
';
	}


    /**
     * Retrieve $WPV_Settings (array-like)
     * @deprecated since version 1.8
     * @return \WPV_Settings $WPV_settings
     */
	function get_options() {
        global $WPV_settings;
        return $WPV_settings;
    }

    /**
     * Bulk set settings and save
     * @deprecated since version 1.8
     * @param array $options
     */
    function save_options( $options ) {
        global $WPV_settings;
        if ( is_array( $options ) ) {
            $WPV_settings->set( $options );
        }
        $WPV_settings->save();
    }

    /**
	* Adds help on admin pages.
	*
	* @param type $contextual_help
	* @param type $screen_id
	* @param type $screen
	* @return type
	*/
	function admin_plugin_help( $contextual_help, $screen_id, $screen ) {
		return $contextual_help;
	}

	function is_embedded() {
		return true;
	}

	function get_current_taxonomy_term() {
		if ( isset( $this->taxonomy_data['term'] ) ) {
			return $this->taxonomy_data['term'];
		} else {
			return null;
		}
	}


	function taxonomy_query( $view_settings ) {
		$items = get_taxonomy_query( $view_settings );

		$this->taxonomy_data['item_count'] = sizeof( $items );

		if ( $view_settings['pagination'][0] == 'disable' ) {
			$this->taxonomy_data['max_num_pages'] = 1;
			$this->taxonomy_data['item_count_this_page'] = $this->taxonomy_data['item_count'];
		} else {
			// calculate the number of pages.
			$posts_per_page = $view_settings['posts_per_page'];
			if ( isset( $view_settings['pagination']['mode']) && $view_settings['pagination']['mode'] == 'rollover') {
				$posts_per_page = $view_settings['rollover']['posts_per_page'];
			}

			$this->taxonomy_data['max_num_pages'] = ceil( $this->taxonomy_data['item_count'] / $posts_per_page );

			if ( $this->taxonomy_data['item_count'] > $posts_per_page ) {
				// return the paged result
				$page = 1;
				if ( isset( $_GET['wpv_paged'] ) ) {
					$page = intval( $_GET['wpv_paged'] );
				}

				$this->taxonomy_data['page_number'] = $page;

				// only return 1 page of items.
				$items = array_slice( $items, ($page - 1) * $posts_per_page, $posts_per_page );
			}
		}
		$this->taxonomy_data['item_count_this_page'] = sizeof( $items );
		return $items;
	}


	/**
	 * Get Users query,
	 */
	function users_query( $view_settings ) {
		$items = get_users_query( $view_settings );

		$this->users_data['item_count'] = sizeof( $items );

		if ( $view_settings['pagination'][0] == 'disable' ) {
			$this->users_data['item_count_this_page'] = $this->users_data['item_count'];
			$this->users_data['max_num_pages'] = 1;
		} else {
			// calculate the number of pages.
			$posts_per_page = $view_settings['posts_per_page'];
			if ( isset( $view_settings['pagination']['mode'] ) && $view_settings['pagination']['mode'] == 'rollover') {
				$posts_per_page = $view_settings['rollover']['posts_per_page'];
			}

			$this->users_data['max_num_pages'] = ceil( $this->users_data['item_count'] / $posts_per_page );

			if ( $this->users_data['item_count'] > $posts_per_page ) {
				// return the paged result

				$page = 1;
				if ( isset( $_GET['wpv_paged'] ) ) {
					$page = intval( $_GET['wpv_paged'] );
				}

				$this->users_data['page_number'] = $page;

				// only return 1 page of items.
				$items = array_slice( $items, ($page - 1) * $posts_per_page, $posts_per_page );

			}
		}
		$this->users_data['item_count_this_page'] = sizeof( $items );
		return $items;
	}


	function get_current_page_number() {
		if ( $this->post_query ) {
			return intval( $this->post_query->query_vars['paged'] );
		} elseif ( isset( $this->users_data ) && isset( $this->users_data['page_number'] ) ) {
			return $this->users_data['page_number'];
		} elseif ( isset( $this->taxonomy_data ) && isset( $this->taxonomy_data['page_number'] ) ) {
			// Taxonomy query
			return $this->taxonomy_data['page_number'];
		}
		return 1;
	}


	function get_max_pages() {
		if ( $this->post_query ) {
			return $this->post_query->max_num_pages;
		} elseif ( isset( $this->users_data ) && isset( $this->users_data['max_num_pages'] ) ) {
			return $this->users_data['max_num_pages'];
		} elseif ( isset( $this->taxonomy_data ) && isset( $this->taxonomy_data['max_num_pages'] ) ) {
			// Taxonomy query
			return $this->taxonomy_data['max_num_pages'];
		}
		return 1;
	}


	function get_taxonomy_found_count() {
		if ( isset( $this->taxonomy_data['item_count'] ) ) {
			return $this->taxonomy_data['item_count'];
		} else {
			return 0;
		}
	}


	function get_users_found_count() {
		if ( isset( $this->users_data['item_count'] ) ) {
			return $this->users_data['item_count'];
		} else {
			return 0;
		}
	}


	function get_parent_view_taxonomy() {
		return $this->parent_taxonomy;
	}

	function get_parent_view_user() {
		return $this->parent_user;
	}


	/**
	 * @param string $type Should be 'taxonomy' or 'post'.
	 *
	 * DEPRECATED, but might be useful to display select dropdown for Views based on what they query
	 */
	function get_add_field_view_select_box( $row, $view_selected, $type ) {
		global $wpdb;

		$views_available = $wpdb->get_results(
			"SELECT ID, post_title FROM {$wpdb->posts} 
			WHERE post_type = 'view' 
			AND post_status in ('publish')" 
		);

		$view_select_box = '';
		if ( $row === '' ) {
			$view_select_box .= '<select class="' . $type . '_view_select" name="' . $type . '_view" id="' . $type . '_view">';
		} else {
			$view_select_box .= '<select class="' . $type . '_view_select" name="' . $type . '_view_' . $row . '" id="' . $type . '_view_' . $row . '">';
		}

		foreach( $views_available as $view ) {
			if ( $view_selected == $view->ID ) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}

			$view_settings = $this->get_view_settings( $view->ID );
			$title = $view->post_title . ' - ' . __( 'Post View', 'wpv-views' );
			if ( isset( $view_settings['query_type'][0] ) && $view_settings['query_type'][0] == 'taxonomy' ) {
				$title = $view->post_title . ' - ' . __( 'Taxonomy View', 'wpv-views' );
			}

			$view_select_box .= '<option value="' . $view->ID . '"' . $selected . '>' . $title . '</option>';
		}
		$view_select_box .= '</select>';

		return $view_select_box;
	}


	function set_widget_view_id( $id ) {
		$this->widget_view_id = $id;
	}


	function get_widget_view_id() {
		return $this->widget_view_id;
	}


	function set_variable( $name, $value ) {
		$this->variables[ $name ] = $value;
	}


	function get_variable( $name ) {
		if ( strpos( $name, '$' ) === 0 ) {
			$name = substr( $name, 1 );

			if ( isset( $this->variables[ $name ] ) ) {
				return $this->variables[ $name ];
			}
		}
		return null;
	}


	function get_view_shortcode_params( $view_id ) {
		$settings = $this->get_view_settings( $view_id );

		$params = wpv_get_custom_field_view_params( $settings );
		$params = array_merge( $params, wpv_get_taxonomy_view_params( $settings ) );

		return $params;
	}


	function hide_view_template_author() {	}


	/**
	 * See if a view has any enabled from controls.
	 */
	function does_view_have_form_controls( $view_id ) {
		$view_settings = $this->get_view_settings( $view_id );

		if ( isset( $view_settings['filter_controls_enable'] ) && is_array( $view_settings['filter_controls_enable'] ) ) {
			foreach( $view_settings['filter_controls_enable'] as $enable ) {
				if ( $enable ) {
					return true;
				}
			}
		}

		// Sometimes, the above check is not enough because the filters have been deleted => search for the actual controls shortcodes
		if ( isset( $view_settings['filter_meta_html'] ) ) {
			if ( strpos( $view_settings['filter_meta_html'], "[wpv-control" )
				|| strpos( $view_settings['filter_meta_html'], "[wpv-filter-search-box" )
				|| strpos( $view_settings['filter_meta_html'], "[wpv-filter-submit" ) )
			{
				return true;
			}
		}

		return false;
	}

	/**
	* does_view_have_form_control_with_submit
	*
	* See if a view has any enabled from controls and packs a submit button
	*
	* @param $view_id integer
	*
	* @return boolean
	*
	* @since 1.7.0
	*/

	function does_view_have_form_control_with_submit( $view_id ) {
		$view_settings = $this->get_view_settings( $view_id );

		if ( isset( $view_settings['filter_meta_html'] ) ) {
			if (
				(
					strpos( $view_settings['filter_meta_html'], "[wpv-control" )
					|| strpos( $view_settings['filter_meta_html'], "[wpv-filter-search-box" )
					|| strpos( $view_settings['filter_meta_html'], "[wpv-filter-submit" )
				)
				&& strpos( $view_settings['filter_meta_html'], '[wpv-filter-submit' )
			) {
				return true;
			}
		}

		return false;
	}


	/**
	 *	See if a view is used for an archive.
	 */
	function is_archive_view( $view_id ) {
		$view_settings = $this->get_view_settings( $view_id );
		if ( ! isset( $view_settings['view-query-mode'] ) ) {
			$view_settings['view-query-mode'] = 'normal';
		}
		$archive_query_modes = array( 'archive', 'layouts-loop' );
		
		/**
		* wpv_filter_allowed_archive_query_modes
		*
		* Filter the array of valid WPA view-query-mode values
		*
		* @param (array) The array of valid values
		*
		* @since 1.7
		*/
		
		$archive_query_modes = apply_filters( 'wpv_filter_allowed_archive_query_modes', $archive_query_modes );
		return ( in_array( $view_settings['view-query-mode'], $archive_query_modes ) );
	}


	function wpv_format_date() {
		$date_format = $_POST['date-format'];
		if ( $date_format == '' ) {
			$date_format = get_option( 'date_format' );
		}
		// this is needed to escape characters in the date_i18n function
		$date_format = str_replace( '\\\\', '\\', $date_format );
		$date = $_POST['date'];
		// We can not be sure that the adodb_xxx functions are available, so we do different things whether they exist or not
		if ( defined( 'ADODB_DATE_VERSION' ) ) {
			$date = adodb_mktime( 0, 0, 0, substr( $date, 2, 2 ), substr( $date, 0, 2 ), substr( $date, 4, 4 ) );
			echo json_encode( array(
					'display' => adodb_date( $date_format, $date ),
					'timestamp' => $date ) );
		} else {
			$date = mktime( 0, 0, 0, substr( $date, 2, 2 ), substr( $date, 0, 2 ), substr( $date, 4, 4 ) );
			echo json_encode( array(
					'display' => date_i18n( $date_format, intval( $date ) ),
					'timestamp' => $date ) );
		}

		die();
	}


	function wpv_meta_html_extra_css() {
		$view_ids = array_unique( $this->view_used_ids );
		$cssout = '';
		foreach ( $view_ids as $view_id ) {
			$meta = $this->get_view_settings( $view_id );
			if ( isset( $meta['filter_meta_html_css'] ) ) {
				$cssout .= $meta["filter_meta_html_css"];
			}
			if ( isset( $meta['layout_meta_html_css'] ) ) {
				$cssout .= $meta["layout_meta_html_css"];
			}
		}
		if ( '' != $cssout ) {
			echo "\n<style type=\"text/css\" media=\"screen\">\n$cssout\n</style>\n";
		}
	}

	function wpv_meta_html_extra_js() {
		$view_ids = array_unique( $this->view_used_ids );
		$jsout = '';
		foreach ( $view_ids as $view_id ) {
			$meta = $this->get_view_settings( $view_id );
			if ( isset( $meta['filter_meta_html_js'] ) ) {
				$jsout .= $meta["filter_meta_html_js"];
			}
			if ( isset( $meta['layout_meta_html_js'] ) ) {
				$jsout .= $meta["layout_meta_html_js"];
			}
		}
		if ( '' != $jsout ) {
			echo "\n<script type=\"text/javascript\">\n$jsout\n</script>\n";
		}
	}
	
	/**
	* wpv_additional_js_files
	*
	* Add custom script URLs from the View layout settings into the wp_footer action
	*
	* @since 1.8.0
	*/
	
	function wpv_additional_js_files() {
		$view_ids = array_unique( $this->view_used_ids );
		foreach ( $view_ids as $view_id ) {
			$meta = $this->get_view_layout_settings( $view_id );
			if (
				isset( $meta['additional_js'] ) 
				&& ! empty( $meta['additional_js'] )
			) {
				$scripts = explode( ',', $meta['additional_js'] );
				foreach ( $scripts as $script ) {
					if ( strpos( $script, '[theme]' ) === 0 ) {
						$script = str_replace( '[theme]', get_stylesheet_directory_uri(), $script );
					}
					echo "\n";
					?>
					<script type="text/javascript" src="<?php echo esc_url( $script ); ?>"></script>
					<?php
					echo "\n";
				}
			}
		}
	}

	function wpv_admin_enqueue_scripts( $hook ) {

		// Register general scripts needed in the embedded version

		wp_deregister_script( 'toolset-colorbox' );
		wp_register_script( 'toolset-colorbox' , WPV_URL_EMBEDDED . '/res/js/lib/jquery.colorbox-min.js', array('jquery'), WPV_VERSION);

		// DEPRECATED
		// Keep this views-select2-script handler just because the installed version of other plugins might be using it
		// Just register, never enqueue
		wp_register_script( 'views-select2-script' , WPV_URL_EMBEDDED . '/common/utility/js/select2.min.js', array('jquery'), WPV_VERSION);

		if ( ! wp_script_is( 'select2', 'registered' ) ) {
			wp_register_script( 'select2' , WPV_URL_EMBEDDED . '/common/utility/js/select2.min.js', array('jquery'), WPV_VERSION);
		}

		if ( ! wp_script_is( 'toolset-utils', 'registered' ) ) {
			wp_register_script( 'toolset-utils', ( WPV_URL_EMBEDDED . "/common/utility/js/utils.js" ), array( 'jquery', 'underscore', 'backbone'), '1.0', true );
		}

		wp_register_script( 'views-utils-script' , WPV_URL_EMBEDDED . '/res/js/lib/utils.js', array('jquery','toolset-colorbox', 'select2', 'toolset-utils'), WPV_VERSION);
		wp_register_script( 'views-shortcodes-gui-script' , WPV_URL_EMBEDDED . '/res/js/views_shortcodes_gui.js', array('jquery','toolset-colorbox', 'suggest', 'views-utils-script'), WPV_VERSION);

		wp_register_script( 'views-widgets-gui-script' , WPV_URL_EMBEDDED . '/res/js/views_widgets_gui.js', array('jquery','suggest'), WPV_VERSION);

		// CodeMirror

		wp_register_script( 'views-codemirror-script' , WPV_URL_EMBEDDED . '/res/js/codemirror/lib/codemirror.js', array(), WPV_VERSION, false);
		wp_register_script( 'views-codemirror-overlay-script' , WPV_URL_EMBEDDED . '/res/js/codemirror/addon/mode/overlay.js', array('views-codemirror-script'), WPV_VERSION, false);
		wp_register_script( 'views-codemirror-xml-script' , WPV_URL_EMBEDDED . '/res/js/codemirror/mode/xml/xml.js', array('views-codemirror-overlay-script'), WPV_VERSION, false);
		wp_register_script( 'views-codemirror-css-script' , WPV_URL_EMBEDDED . '/res/js/codemirror/mode/css/css.js', array('views-codemirror-overlay-script'), WPV_VERSION, false);
		wp_register_script( 'views-codemirror-js-script' , WPV_URL_EMBEDDED . '/res/js/codemirror/mode/javascript/javascript.js', array('views-codemirror-overlay-script'), WPV_VERSION, false);
		wp_register_script( 'views-codemirror-addon-searchcursor-script', WPV_URL_EMBEDDED . '/res/js/codemirror/addon/search/searchcursor.js', array('views-codemirror-script'), WPV_VERSION, false);
		wp_register_script( 'views-codemirror-addon-panel-script' , WPV_URL_EMBEDDED . '/res/js/codemirror/addon/display/panel.js', array('views-codemirror-script'), WPV_VERSION, false);
		wp_register_script( 'views-codemirror-conf-script' , WPV_URL_EMBEDDED . '/res/js/views_codemirror_conf.js', array('jquery','views-codemirror-script'), WPV_VERSION, false);
		wp_register_script( 'views-embedded-script',  WPV_URL_EMBEDDED . '/res/js/views_embedded.js', array('jquery','views-codemirror-overlay-script'), WPV_VERSION, true);


        // Register general CSS needed in the embedded version

		wp_deregister_style( 'toolset-font-awesome' );
		wp_register_style( 'toolset-font-awesome', WPV_URL_EMBEDDED . '/res/css/font-awesome/css/font-awesome.min.css', array(), WPV_VERSION );

		wp_deregister_style( 'toolset-colorbox' );
		wp_register_style( 'toolset-colorbox', WPV_URL_EMBEDDED . '/res/css/colorbox.css', array(), WPV_VERSION );

		wp_register_style( 'views-notifications-css', WPV_URL_EMBEDDED . '/res/css/notifications.css', array(), WPV_VERSION );
		wp_register_style( 'views-dialogs-css', WPV_URL_EMBEDDED . '/res/css/dialogs.css', array(), WPV_VERSION );

        // Select2 style
        if( ! wp_style_is( 'select2', 'registered' ) ) {
            wp_register_style( 'select2', WPV_URL_EMBEDDED . '/common/utility/css/select2/select2.css', array(), WPV_VERSION );
        }

		// CodeMirror style

		wp_register_style( 'views-codemirror-css' , WPV_URL_EMBEDDED . '/res/js/codemirror/lib/codemirror.css', array(), WPV_VERSION);

		// General Views redesign style

		wp_register_style(
				'views-admin-css',
				WPV_URL_EMBEDDED . '/res/css/views-admin.css',
				array( 'toolset-font-awesome', 'toolset-colorbox', 'views-notifications-css', 'views-dialogs-css', 'select2' ),
				WPV_VERSION );

		// Enqueue scripts and styles needed in the embedded version

		if ( ( $hook == 'post.php' || $hook == 'post-new.php' ) ) {
			// This is to show the Views form popup
			if ( !wp_script_is( 'views-utils-script' ) ) {
				wp_enqueue_script( 'views-utils-script');
				$help_box_translations = array(
						'wpv_dont_show_it_again' => __( "Got it! Don't show this message again", 'wpv-views'),
						'wpv_close' => __( 'Close', 'wpv-views') );
				wp_localize_script( 'views-utils-script', 'wpv_help_box_texts', $help_box_translations );
			}
			if ( !wp_script_is( 'suggest' ) ) {
				// Maybe not needed as is a dependence of the next one
				wp_enqueue_script( 'suggest' );
			}
			if ( !wp_script_is( 'views-shortcodes-gui-script' ) ) {
				wp_enqueue_script( 'views-shortcodes-gui-script' );
			}
			if ( !wp_style_is( 'toolset-font-awesome' ) ) {
				wp_enqueue_style( 'toolset-font-awesome' );
			}
			if ( !wp_style_is( 'toolset-colorbox' ) ) {
				wp_enqueue_style( 'toolset-colorbox' );
			}
			if ( !wp_style_is( 'views-notifications-css' ) ) {
				wp_enqueue_style( 'views-notifications-css' );
			}
			if ( !wp_style_is( 'views-dialogs-css' ) ) {
				wp_enqueue_style( 'views-dialogs-css' );
			}
			if ( !wp_script_is( 'jquery-ui-resizable' ) ) {
				wp_enqueue_script('jquery-ui-resizable');
			}
			wp_enqueue_style( 'views-admin-css' );
		}


        // Script for listing pages
        if( in_array( $hook, array( 'toplevel_page_embedded-views', 'views_page_embedded-views-templates', 'views_page_embedded-views-archives' ) ) ) {
            wp_enqueue_script( 'views-embedded-listing-pages-script',  WPV_URL_EMBEDDED . '/res/js/listing_pages.js', array( 'jquery' ), WPV_VERSION, true );
            wp_enqueue_style( 'views-admin-css' );
        }


        if ( isset( $_GET['page'] )
			&& ( $_GET['page'] == 'views-embedded' || $_GET['page'] == 'view-templates-embedded' || $_GET['page'] == 'view-archives-embedded' || $_GET['page'] == 'ModuleManager_Modules' ) )
		{
			wp_enqueue_script('wp-pointer');
			wp_enqueue_style('wp-pointer');
			wp_enqueue_script('views-codemirror-script');
			wp_enqueue_script('views-codemirror-overlay-script');
			wp_enqueue_script('views-codemirror-xml-script');
			wp_enqueue_script('views-codemirror-css-script');
			wp_enqueue_script('views-codemirror-js-script');
			wp_enqueue_script('views-codemirror-conf-script');
			wp_enqueue_script('views-embedded-script');
			if ( !wp_script_is( 'views-utils-script' ) ) {
				wp_enqueue_script( 'views-utils-script');
				$help_box_translations = array(
					'wpv_dont_show_it_again' => __( "Got it! Don't show this message again", 'wpv-views' ),
					'wpv_close' => __( 'Close', 'wpv-views')
				);
				wp_localize_script( 'views-utils-script', 'wpv_help_box_texts', $help_box_translations );
			}
			wp_enqueue_style( 'views-codemirror-css' );
			wp_enqueue_style( 'views-admin-css' );
		}

		if ( $hook == 'widgets.php' ) {
			if ( !wp_script_is( 'suggest' ) ) {
				// Maybe not needed as is a dependence of the next one
				wp_enqueue_script( 'suggest' );
			}
			wp_enqueue_script( 'views-widgets-gui-script' );
			wp_enqueue_style( 'views-admin-css' );
		}

	}



	function get_force_disable_dependant_parametric_search() {
		return $this->force_disable_dependant_parametric_search;
	}

	function check_force_disable_dependant_parametric_search() {
		$force_disable = false;
		$view_settings = $this->get_view_settings();
		if ( isset( $view_settings['dps'] )
			&& isset( $view_settings['dps']['enable_dependency'] )
			&& $view_settings['dps']['enable_dependency'] == 'enable' )
		{
			$controls_per_kind = wpv_count_filter_controls( $view_settings );
			$controls_count = 0;
			$no_intersection = array();

			if ( !isset( $controls_per_kind['error'] ) ) {
				// $controls_count = array_sum( $controls_per_kind );
				$controls_count = $controls_per_kind['cf'] + $controls_per_kind['tax'] + $controls_per_kind['pr'] + $controls_per_kind['search'];

				if ( $controls_per_kind['cf'] > 1
					&& ( !isset( $view_settings['custom_fields_relationship'] ) || $view_settings['custom_fields_relationship'] != 'AND' ) )
				{
					$no_intersection[] = __( 'custom field', 'wpv-views' );
				}

				if ( $controls_per_kind['tax'] > 1
					&& ( !isset( $view_settings['taxonomy_relationship'] ) || $view_settings['taxonomy_relationship'] != 'AND' ) )
				{
					$no_intersection[] = __( 'taxonomy', 'wpv-views' );
				}
			} else {
				$force_disable = true;
			}

			if ( $controls_count > 0 ) {
				if ( count( $no_intersection ) > 0 ) {
					$force_disable = true;
				}
			} else {
				$force_disable = true;
			}
		}
		$this->set_force_disable_dependant_parametric_search( $force_disable );
		return $force_disable;
	}

	function set_force_disable_dependant_parametric_search( $bool = false ) {
		$this->force_disable_dependant_parametric_search = $bool;
	}

	/**
	 * wpv_get_view_url_params TODO
	 *
	 */
	function wpv_get_view_url_params( $id = null ) {
		$view_settings = $this->get_view_settings( $view_id );

	}

}


function wpv_views_plugin_activate() {
	add_option( 'wpv_views_plugin_do_activation_redirect', true );
}


function wpv_views_plugin_deactivate() {
	delete_option( 'wpv_views_plugin_do_activation_redirect', true );
}


function wpv_views_plugin_redirect() {
	if ( get_option( 'wpv_views_plugin_do_activation_redirect', false ) ) {
		delete_option( 'wpv_views_plugin_do_activation_redirect' );
		$redirect = wp_redirect( 
			add_query_arg(
				array( 'page' => WPV_FOLDER .'/menu/help.php' ),
				admin_url( 'admin.php' ) 
			)
		);
		if ( $redirect ) {
			exit;
		}
	}
}


function wpv_views_plugin_action_links( $links, $file ) {
	$this_plugin = basename( WPV_PATH ) . '/wp-views.php';
	if( $file == $this_plugin ) {
		$links[] = sprintf(
				'<a href="%s">%s</a>',
				add_query_arg(
						array( 'page' => basename( WPV_PATH ) . '/menu/help.php' ),
						admin_url( 'admin.php' ) ),
				__( 'Getting started', 'wpv-views' ) );
	}
	return $links;
}


/**
 * WPML translate call.
 *
 * @param type $name
 * @param type $string
 * @return type
 *
 * @todo maybe move to the WPML file
 */
function wpv_translate( $name, $string, $register = false, $context = 'plugin Views' ) {
	if ( !function_exists( 'icl_t' ) ) {
		return $string;
	}

	if ( $register ) {
		icl_register_string( $context, $name, $string );
	}

	return icl_t( $context, $name, stripslashes( $string ) );
}


/**
* wpv_admin_exclude_tax_slugs
*
* Applied in the filter wpv_admin_exclude_tax_slugs, returns an array of taxonomy slugs that are left out in Views taxonomy-related View loops admin GUIs.
*
* We take out taxonomies with show_ui set to false by default, but some custom taxonomies declared for internal use
* by some plugins do not use it. If that is the case and no custom labels are provided, the custom taxonomy hijacks
* Categories or Post Tags in some Views taxonomy-related View loops admin GUIs that rely on the labels.
* This filter takes those internal taxonomies out of our loops.
*
* @param $exclude_tax_slugs (array) The slugs to be excluded.
*
* @return $exclude_tax_slugs
*
* @since unknown
*/

function wpv_admin_exclude_tax_slugs( $exclude_tax_slugs ) {

	// first we exclude the three built-in taxonomies that we want to leave out_items
	if ( ! in_array( 'post_format', $exclude_tax_slugs ) ) {
		$exclude_tax_slugs[] = 'post_format';
	}
	if ( ! in_array( 'link_category', $exclude_tax_slugs ) ) {
		$exclude_tax_slugs[] = 'link_category';
	}
	if ( ! in_array( 'nav_menu', $exclude_tax_slugs ) ) {
		$exclude_tax_slugs[] = 'nav_menu';
	}

	// WP RSS Aggregator issue: https://icanlocalize.basecamphq.com/projects/7393061-toolset/todo_items/171941369/comments
	// Filtering out an internal custom taxonomy with slug wp_log_type

	if ( ! in_array( 'wp_log_type', $exclude_tax_slugs ) ) {
		$exclude_tax_slugs[] = 'wp_log_type';
	}

	return $exclude_tax_slugs;
}

/**
* wpv_admin_exclude_post_type_slugs
*
* Applied in the filter wpv_admin_exclude_post_type_slugs, returns an array of post type slugs that are left out in some database calls.
*
* We are using this, for example, in the target suggest script for parametric search, as we do not want to offer some post types as available targets.
*
* @param $exclude_post_type_slugs (array) The slugs to be excluded.
*
* @return $exclude_post_type_slugs
*
* @since 1.7
*/

function wpv_admin_exclude_post_type_slugs( $exclude_post_type_slugs ) {
	// Exclude al non-public post types
	$exclude_args = array(
	   'public'   => false
	);
	$exclude_output = 'names';
	$exclude_post_types = get_post_types( $exclude_args, $exclude_output );
	foreach ( $exclude_post_types as $exclude_p_t ) {
		if ( ! in_array( $exclude_p_t, $exclude_post_type_slugs ) ) {
			$exclude_post_type_slugs[] = $exclude_p_t;
		}
	}
	// Leave out all the Toolset post types - the above one takes out the Types field groups ones
	if ( ! in_array( 'view', $exclude_post_type_slugs ) ) {
		$exclude_post_type_slugs[] = 'view';
	}
	if ( ! in_array( 'view-template', $exclude_post_type_slugs ) ) {
		$exclude_post_type_slugs[] = 'view-template';
	}
	if ( ! in_array( 'cred-form', $exclude_post_type_slugs ) ) {
		$exclude_post_type_slugs[] = 'cred-form';
	}
	if ( ! in_array( 'dd_layouts', $exclude_post_type_slugs ) ) {
		$exclude_post_type_slugs[] = 'dd_layouts';
	}
	// Also leave out revisions
	if ( ! in_array( 'revision', $exclude_post_type_slugs ) ) {
		$exclude_post_type_slugs[] = 'revision';
	}
	return $exclude_post_type_slugs;
}

/**
 * wpv_admin_available_spinners
 *
 * Applied in the filter wpv_admin_available_spinners, returns an array of default available spinners used in pagination and parametric search.
 *
 * Note that this filter is hooked at priority 5 and sets the basic existing spinners, so further spinners should be added at a later priority.
 *
 * @param $available_spinners (array) The spinners to be offered.
 *
 * @return array $available_spinners
 *
 * @since 1.7
 */
function wpv_admin_available_spinners( $available_spinners ) {
	$available_spinners = array(
		array(
			'title' => __( 'Spinner #1', 'wpv-views' ),
			'url' => WPV_URL_EMBEDDED . '/res/img/ajax-loader.gif'
		),
		array(
			'title' => __( 'Spinner #2', 'wpv-views' ),
			'url' => WPV_URL_EMBEDDED . '/res/img/ajax-loader2.gif'
		),
		array(
			'title' => __( 'Spinner #3', 'wpv-views' ),
			'url' => WPV_URL_EMBEDDED . '/res/img/ajax-loader3.gif'
		),
		array(
			'title' => __( 'Spinner #4', 'wpv-views' ),
			'url' => WPV_URL_EMBEDDED . '/res/img/ajax-loader4.gif'
		),
		array(
			'title' => __( 'Spinner #5', 'wpv-views' ),
			'url' => WPV_URL_EMBEDDED . '/res/img/ajax-loader-overlay.gif'
		)
	);
	return $available_spinners;
}


/**
 * Return array of possible attributes for view shortcode
 *
 * @param $view_id The ID of the relevant View.
 *
 * @return Numeric array of possible attributes for $view_id.
 *
 * Output example:
 * 			'query_type' => posts|taxonomy|users
 * 			'filter_type' => filter that this attribute is used on (post_id, post_author, etc..)
 * 			'value' => filter from where attribute getting data
 * 			'attribute' => the actual shortcode attribute
 * 			'expected' => input data type integer|string|numeric
 *
 * Usage example:  <?php echo get_view_allowed_attributes(80)); ?>
 */
function get_view_allowed_attributes( $view_id ) {
	$output = array();

	if ( empty( $view_id ) ){
		return;
	}

	global $WP_Views;
	$view_settings = $WP_Views->get_view_settings( $view_id );
	if ( is_array( $view_settings ) ){

		// Post types attributes
		if ( isset( $view_settings['query_type'][0] ) && $view_settings['query_type'][0] == 'posts' ) {

			// author shortcode
			if ( isset( $view_settings['author_mode'][0] ) && $view_settings['author_mode'][0] == 'shortcode' ){
				$output[] = array(
						'query_type' => $view_settings['query_type'][0],
						'filter_type' => 'post_author',
						'value' => $view_settings['author_shortcode_type'],
						'attribute' => $view_settings['author_shortcode'],
						'expected' => ( $view_settings['author_shortcode_type'] == 'id' ) ? 'integer' : 'string' );
			}

			// post id shortcode
			if ( isset( $view_settings['id_mode'][0] ) && $view_settings['id_mode'][0] == 'shortcode' ) {
				$output[] = array (
						'query_type' => $view_settings['query_type'][0],
						'filter_type' => 'post_id',
						'value' => 'post_id',
						'attribute' => $view_settings['post_ids_shortcode'],
						'expected' => 'integer'	);
			}

			// taxonomies
			$taxonomies = get_taxonomies( array( 'public' => true ), 'names' );
			foreach ( $taxonomies as $taxonomy ) {
				if ( isset( $view_settings[ 'tax_' . $taxonomy . '_relationship' ] )
					&& $view_settings[ 'tax_' . $taxonomy . '_relationship' ] == 'FROM ATTRIBUTE' )
				{
			  		$output[] = array(
							'query_type' => $view_settings['query_type'][0],
							'filter_type' => 'taxonomy_' . $taxonomy,
							'value' => $view_settings[ 'taxonomy-' . $taxonomy . '-attribute-url-format' ][0],
							'attribute' => $view_settings[ 'taxonomy-' . $taxonomy . '-attribute-url' ],
							'expected' => 'string' );
				}
			}

			// post relationship
			if ( isset( $view_settings['post_relationship_mode'][0] )
				&& $view_settings['post_relationship_mode'][0] == 'shortcode_attribute' )
			{
				$output[] = array(
						'query_type' => $view_settings['query_type'][0],
						'filter_type' => 'post_relationship',
						'value' => 'ancestor_id',
						'attribute' => $view_settings['post_relationship_shortcode_attribute'],
						'expected' => 'integer' );
			}

			// custom fields
			foreach( $view_settings as $key => $value ){
				if ( preg_match( "/custom-field-(.*)_value/", $key, $res )
					&& preg_match( "/VIEW_PARAM\(([^\)]+)\)/", $value, $shortcode ) )
				{
					$expected_input_data_type = in_array( $view_settings[ 'custom-field-' . $res[1] . '_type' ], array('NUMERIC','DATE','DATETIME','TIME') )
							? 'integer'
							: ( ( $view_settings[ 'custom-field-' . $res[1] . '_type' ] == 'DECIMAL' ) ? 'decimal' : 'string' );

					$output[] = array(
							'query_type' => $view_settings['query_type'][0],
							'filter_type' => 'custom-field_'. $res[1],
							'value' => 'custom_field_value',
							'attribute' => $shortcode[1],
							'expected' => $expected_input_data_type );
				}
			}
		}

		// Users attributes
		if ( isset( $view_settings['query_type'][0] ) && $view_settings['query_type'][0] == 'users' ) {

			// users shortocde
			if ( isset( $view_settings['users_mode'][0] ) && $view_settings['users_mode'][0] == 'shortcode' ) {
				$output[] = array(
						'query_type' => $view_settings['query_type'][0],
						'filter_type' => 'user',
						'value' => $view_settings['users_shortcode_type'],
						'attribute' => $view_settings['users_shortcode'],
						'expected' => ( $view_settings['users_shortcode_type'] == 'id' ) ? 'integer' : 'string' );
			}

			// usermeta fields
			foreach( $view_settings as $key => $value ) {
				if ( preg_match( "/usermeta-field-(.*)_value/", $key, $res )
					&& preg_match( "/VIEW_PARAM\(([^\)]+)\)/", $value, $shortcode ) )
				{
					$expected_input_data_type = in_array( $view_settings[ 'usermeta-field-' . $res[1] . '_type' ], array('NUMERIC','DATE','DATETIME','TIME') )
							? 'integer'
							: ( ( $view_settings[ 'usermeta-field-' . $res[1] . '_type' ] == 'DECIMAL' ) ? 'decimal' : 'string' );

					$output[] = array(
							'query_type' => $view_settings['query_type'][0],
							'filter_type' => 'usermeta-field_'. $res[1],
							'value' => 'usermeta_field_value',
							'attribute' => $shortcode[1],
							'expected' => $expected_input_data_type	);
				}
			}
		}
	}

	return $output;
}
