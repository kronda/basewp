<?php

require_once( WPV_PATH_EMBEDDED . '/common/visual-editor/editor-addon.class.php' );
require_once( WPV_PATH_EMBEDDED . '/common/visual-editor/views-editor-addon.class.php' );

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
		
		$this->view_ids = array();
		$this->current_view = null;
		$this->CCK_types = array();// @deprecated maybe
		$this->widget_view_id = 0;
		$this->view_depth = 0;
		$this->view_count = array();
		$this->set_view_counts = array();
		$this->view_shortcode_attributes = array();
		$this->view_used_ids = array();
		$this->rendering_views_form_in_progress = false;

		$this->post_query = null;
		$this->post_query_stack = array();// @deprecated maybe
		$this->top_current_page = null;
		$this->current_page = array();

		$this->taxonomy_data = array();
		$this->parent_taxonomy = 0;

		$this->users_data = array();
		$this->parent_user = 0;

		$this->variables = array();

		$this->force_disable_dependant_parametric_search = false;
		$this->returned_ids_for_parametric_search = array();
		
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		
		add_action( 'init', array( $this, 'wpv_register_assets' ) );
        
	}


	function __destruct() { }


	function init() {

		$this->wpv_register_type_view();
		
		/*
		* ----------------------------
		* Assets
		* ----------------------------
		*/
		add_action( 'admin_enqueue_scripts', array( $this,'wpv_admin_enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wpv_frontend_enqueue_scripts' ) );
		
		add_action( 'wp_ajax_wpv_pagination', 'wpv_ajax_pagination' );// @deprecated - maybe - to check and delete

		/*
		* ----------------------------
		* AJAX calls for date filters
		* @todo this might be better in the filter file
		* ----------------------------
		*/
		add_action('wp_ajax_wpv_format_date', array( $this, 'wpv_format_date' ) );
		add_action('wp_ajax_nopriv_wpv_format_date', array( $this, 'wpv_format_date' ) );
		
		/*
		* ----------------------------
		* Basic fallback values for get_view_settings and get_view_layout_settings
		* ----------------------------
		*/
		add_filter( 'wpv_view_settings', array( $this, 'wpv_view_settings_set_fallbacks' ), 5, 2 );
		add_filter( 'wpv_view_layout_settings', array( $this, 'wpv_view_layout_settings_set_fallbacks' ), 5, 2 );
		
		/*
		* ----------------------------
		* Workflows actions
		* ----------------------------
		*/
		
		// Exclude some taxonomies from different pieces of the GUI
		add_filter( 'wpv_admin_exclude_tax_slugs', 'wpv_admin_exclude_tax_slugs' );
		// Exclude some post types from different pieces of the GUI
		add_filter( 'wpv_admin_exclude_post_type_slugs', 'wpv_admin_exclude_post_type_slugs' );
		// List the default spinners available for pagination and parametric search
		add_filter( 'wpv_admin_available_spinners', 'wpv_admin_available_spinners', 5 );
		
		// Delete the current user data after using it on a View listing users loop
		add_action( 'wpv-after-display-user', array( $this, 'clean_current_loop_user' ), 99 );
		
		// Delete the meta keys transients on post and postmeta create/update/delete
		add_action( 'save_post', array( $this, 'delete_transient_meta_keys' ) );
		add_action( 'delete_post', array( $this, 'delete_transient_meta_keys' ) );
		add_action( 'added_post_meta', array( $this, 'delete_transient_meta_keys' ) );
		add_action( 'updated_post_meta', array( $this, 'delete_transient_meta_keys' ) );
		add_action( 'deleted_post_meta', array( $this, 'delete_transient_meta_keys' ) );
		// Delete the meta keys transients on user and usermeta create/update/delete
		add_action( 'user_register', array( $this, 'delete_transient_usermeta_keys' ) );
		add_action( 'profile_update', array( $this, 'delete_transient_usermeta_keys' ) );
		add_action( 'delete_user', array( $this, 'delete_transient_usermeta_keys' ) );
		add_action( 'added_user_meta', array( $this, 'delete_transient_usermeta_keys' ) );
		add_action( 'updated_user_meta', array( $this, 'delete_transient_usermeta_keys' ) );
		add_action( 'deleted_user_meta', array( $this, 'delete_transient_usermeta_keys' ) );
		// Delete the meta and usermeta keys transients on Types groups create/update/delete
		// This covers create and update, delete triggers the delete_post action above
		add_action( 'wpcf_save_group', array( $this, 'delete_transient_meta_keys' ) );
		add_action( 'wpcf_save_group', array( $this, 'delete_transient_usermeta_keys' ) );
		// Custom action
		add_action( 'wpv_action_wpv_delete_transient_meta_keys', array( $this, 'delete_transient_meta_keys' ) );
		add_action( 'wpv_action_wpv_delete_transient_usermeta_keys', array( $this, 'delete_transient_usermeta_keys' ) );
		
		// Manage the _toolset_edit_last postmeta on Views objects
		add_action( 'wpv_action_wpv_save_item', array( $this, 'after_save_item' ) );
		add_action( 'wpv_action_wpv_import_item', array( $this, 'after_import_item' ) );
		
		// Edit post links
		add_filter( 'edit_post_link', array( $this, 'edit_post_link' ), 10, 2 );
		
		// Set priority lower than 20, so we load the CSS before the footer scripts and avoid the bottleneck
		add_action( 'wp_footer', array( $this, 'wpv_meta_html_extra_css' ), 5 );
		// Set priority higher than 20, when all the footer scripts are loaded
		add_action( 'wp_footer', array( $this, 'wpv_meta_html_extra_js' ), 25 );
		// Set priority higher than 20, when all footer scripts are loaded, but before 25, when custom javascript is added
		add_action( 'wp_footer', array( $this, 'wpv_additional_js_files' ), 21 );
		
		/*
		* ----------------------------
		* Compatibility
		* ----------------------------
		*/
		
		// WooCommerce
		add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'wpv_woocommerce_product_add_to_cart_url' ), 10, 2 );
		// Gravity Forms
		add_filter( 'gform_form_tag', array( $this, 'wpv_gravityforms_fix_form_action_on_ajax' ), 10, 2 );

		if ( is_admin() ) {
			
			global $pagenow, $wp_version;

			add_action( 'admin_enqueue_scripts', array( $this,'wpv_admin_enqueue_scripts' ) );

			add_action( 'admin_menu', array( $this, 'admin_menu' ), 20 );
			add_action( 'admin_head', array( $this, 'settings_box_load' ) );// @deprecate - watch out full plugin mode callback!

            // ct-editor-deprecate: why are these hooks included on post.php or post-new.php? is there another reason
            // besides old CT edit page?
			if ( 'post.php' == $pagenow
				|| 'post-new.php' == $pagenow
				|| ( 
					'admin.php' == $pagenow 
					&& isset( $_GET['page'] ) 
					&& 'dd_layouts_edit' == $_GET['page'] 
				) 
			) {
				add_action( 'admin_head', array( $this, 'add_dialog_to_editors' ) );
			}

			if ( version_compare( $wp_version, '3.3', '<' ) ) {
				add_filter( 'contextual_help', array( $this, 'admin_plugin_help' ), 10, 3 );
			}

		}

		/*
		* ----------------------------
		* Shortcodes
		* ----------------------------
		*/
		add_shortcode( 'wpv-view', array( $this, 'short_tag_wpv_view' ) );
		add_shortcode( 'wpv-form-view', array( $this, 'short_tag_wpv_view_form' ) );
		
		add_filter( 'wpv_filter_wpv_view_shortcode_output', array( $this, 'remove_html_comments_from_shortcode_output' ) );

		$this->init_wpml_integration();

        /* 
         * 
         * Invalidate Views cache on these actions 
         * 
         */
        // Invalidation on post and postmeta changes
        add_action( 'transition_post_status', array( $this, 'invalidate_views_cache' ) );
        add_action( 'save_post', array( $this, 'invalidate_views_cache' ) );
        add_action( 'delete_post', array( $this, 'invalidate_views_cache' ) );
        add_action( 'added_post_meta', array( $this, 'invalidate_views_cache' ) );
        add_action( 'updated_post_meta', array( $this, 'invalidate_views_cache' ) );
        add_action( 'deleted_post_meta', array( $this, 'invalidate_views_cache' ) );
        
        // Invalidation on term changes
        add_action( 'create_term', array( $this, 'invalidate_views_cache' ) );
        add_action( 'edit_terms', array( $this, 'invalidate_views_cache' ) );
        add_action( 'delete_term', array( $this, 'invalidate_views_cache' ) );
        
        // Invalidation on user and usermeta changes
        add_action( 'user_register', array( $this, 'invalidate_views_cache' ) );
        add_action( 'profile_update', array( $this, 'invalidate_views_cache' ) );
        add_action( 'delete_user', array( $this, 'invalidate_views_cache' ) );
        add_action( 'added_user_meta', array( $this, 'invalidate_views_cache' ) );
        add_action( 'updated_user_meta', array( $this, 'invalidate_views_cache' ) );
        add_action( 'deleted_user_meta', array( $this, 'invalidate_views_cache' ) );
        
        // Invalidation on Types-related events
        add_action( 'wpcf_save_group', array( $this, 'invalidate_views_cache' ) );
        
        // Invalidation on Views-related events
        add_action( 'wpv_action_wpv_save_item', array( $this, 'invalidate_views_cache' ) );
        add_action( 'wpv_action_wpv_import_item', array( $this, 'invalidate_views_cache' ) );
        
	}


	/**
	 * Register the post type of View.
	 *
	 * @since unknown
	 */
	function wpv_register_type_view() {
        $labels = array(
            'name' => _x( 'Views', 'post type general name' ),
            'singular_name' => _x( 'View', 'post type singular name' ),
            'add_new' => _x( 'Add New View', 'book' ),
            'add_new_item' => __( 'Add New View', 'wpv-views' ),
            'edit_item' => __( 'Edit View', 'wpv-views' ),
            'new_item' => __( 'New View', 'wpv-views' ),
            'view_item' => __( 'View Views', 'wpv-views' ),
            'search_items' => __( 'Search Views', 'wpv-views' ),
            'not_found' =>  __( 'No views found', 'wpv-views' ),
            'not_found_in_trash' => __( 'No views found in Trash', 'wpv-views' ),
            'parent_item_colon' => '',
            'menu_name' => 'Views'
        );
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
            'supports' => array( 'title', 'editor', 'author' )
        );
        register_post_type( 'view', $args );
	}

	// Add WPML sync options.
	function language_options() {
		// not needed for theme version.
	}


	protected function init_wpml_integration() {
		WPV_WPML_Integration_Embedded::init();
	}
	
	/*
	* ----------------------------
	* Compatibility
	* ----------------------------
	*/
	
	/**
	* WooCommerce
	*
	* Fix malformed add to cart URL in Views AJAX pagination and automatic results in a parametric search.
	*
	* @see https://icanlocalize.basecamphq.com/projects/11629195-toolset-peripheral-work/todo_items/186738278/comments
	*/
	function wpv_woocommerce_product_add_to_cart_url( $add_to_cart_url, $wc_prod_object ) {
		if ( 
			strpos( $add_to_cart_url, 'wpv-ajax-pagination' ) !== false
			|| ( 
				defined( 'DOING_AJAX' )
				&& DOING_AJAX
				&& isset( $_REQUEST['action'] )
				&& $_REQUEST['action'] == 'wpv_update_parametric_search'
			) 
		) {
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
					$add_to_cart_url = esc_url( remove_query_arg( 'added-to-cart', add_query_arg( $query_args_to_add, $base_url ) ) );
				}
			}
		}
		return $add_to_cart_url;
	}
	
	/**
	* Gravity Forms
	*
	* wpv_gravityforms_fix_form_action_on_ajax
	*
	* Fix the action attribute for Gravity Forms loaded on Views AJAX calls
	*
	* @since 1.10
	*/
	
	function wpv_gravityforms_fix_form_action_on_ajax( $form_tag, $form ) {
		if ( preg_match( "|action='(.*?)'|", $form_tag, $matches ) ) {
			$form_action = $matches[1];
			if ( 
				strpos( $form_action, 'wpv-ajax-pagination' ) !== false
				|| ( 
					defined( 'DOING_AJAX' )
					&& DOING_AJAX
					&& isset( $_REQUEST['action'] )
					&& $_REQUEST['action'] == 'wpv_update_parametric_search'
				) 
			) {
				$base_url = "action='" . esc_url( wp_get_referer() ) . "'";
				$form_tag = preg_replace( "|action='(.*?)'|", $base_url, $form_tag );
			}
		}
		return $form_tag;
	}

	/**
	 * Register Views widgets
	 *
	 * @since unknown
	 */
	function widgets_init() {
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
				add_filter( 'screen_options_show_screen', '__return_false', 99 );
			}
            add_submenu_page( 'embedded-views', __( 'Content Templates', 'wpv-views' ), __( 'Content Templates', 'wpv-views' ), $capability, 'embedded-views-templates', 'wpv_admin_menu_embedded_views_templates_listing_page' );
			if ( 
				isset( $_GET['page'] ) 
				&& 'view-templates-embedded' == $_GET['page'] 
			) {
				add_submenu_page( 'embedded-views', __( 'Embedded Content Template', 'wpv-views' ), __( 'Embedded Content Template', 'wpv-views' ), $capability, 'view-templates-embedded', 'content_templates_embedded_html');
				add_filter( 'screen_options_show_screen', '__return_false', 99 );
			}
            add_submenu_page( 'embedded-views', __( 'WordPress Archives', 'wpv-views' ), __( 'WordPress Archives', 'wpv-views' ), $capability, 'embedded-views-archives', 'wpv_admin_menu_embedded_views_archives_listing_page' );
			if ( 
				isset( $_GET['page'] ) 
				&& 'view-archives-embedded' == $_GET['page'] 
			) {
				add_submenu_page( 'embedded-views', __( 'Embedded WordPress Archive', 'wpv-views' ), __( 'Embedded WordPress Archive', 'wpv-views' ), $capability, 'view-archives-embedded', 'view_archives_embedded_html');
				add_filter( 'screen_options_show_screen', '__return_false', 99 );
			}
        }
    }

	// @deprecate
	// enqueue this correctly
	function settings_box_load() {
		global $pagenow;
		if ( $pagenow == 'options-general.php' && isset( $_GET['page'] ) && $_GET['page'] == 'wpv-import-theme' ) {
			$this->include_admin_css();
		}
	}


	function include_admin_css() {
		printf(
				'<link rel="stylesheet" href="%s" type="text/css" media="all" />',
				esc_url( add_query_arg( array( 'v' => WPV_VERSION ), WPV_URL . '/res/css/wpv-views.css' ) ) );
	}
	
	/*
	* after_save_item
	* after_import_item
	*
	* Manage the _toolset_edit_last postmeta on Views objects
	*/
	
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
		extract( 
			shortcode_atts(
				array(
					'id'	=> false,
					'name'  => false 
				),
				$atts 
			) 
		);

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
 	 * Process the View shortcode.
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

        // Shall we look up in the cache?
        $is_cacheable = $this->is_cacheable( $id );
        $cache_id = $this->get_cache_id( $id );
        if ( $is_cacheable ) {
            
            // Is it cached?
            $cached_output_index = get_option( 'wpv_transient_view_index', array() );
            $is_cached = isset( $cached_output_index[$cache_id] ) && $cached_output_index[$cache_id];
            
            if( $is_cached ) {
                
                $trasient = 'wpv_transient_view_'.$cache_id; // strlen() <= 45
                $cached_output = get_transient( $trasient );
                if( $cached_output !== false ) {
                    array_pop( $this->view_shortcode_attributes );
					return apply_filters( 'wpv_filter_wpv_view_shortcode_output', $cached_output, $id );
                }
                // We should not reach this line if everything is working fine
                // But if not, let's continue
            }
            
        }
        
		$out = $this->render_view_ex( $id, md5( serialize( $atts ) ) );
		$out = apply_filters( 'wpv_filter_wpv_view_shortcode_output', $out, $id );
		array_pop( $this->view_shortcode_attributes );
        
        // Update Views cache if applicable
        if ( $is_cacheable ) {
            
            // Update Views Loop Output Content Cache
            $trasient = 'wpv_transient_view_'.$cache_id;
            $is_stored = set_transient( $trasient, $out, DAY_IN_SECONDS );
            
            // Update Views Loop Output Index
            if( $is_stored === true ) {
                $cached_output_index = get_option( 'wpv_transient_view_index', array() );
                $cached_output_index[$cache_id] = true;
                update_option( 'wpv_transient_view_index', $cached_output_index );
            }
        }
        
		return $out;

	}


	/**
	 * Process the View form shortcode.
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
			if ( 
				strpos( $url, 'wpv-ajax-pagination' ) !== false
				|| ( 
					defined('DOING_AJAX')
					&& DOING_AJAX
					&& isset( $_REQUEST['action'] )
					&& $_REQUEST['action'] == 'wpv_update_parametric_search'
				) 
			) {
				if ( wp_get_referer() ) {
					$url = wp_get_referer();
				}
			}
		} else {
			if ( is_numeric( $target_id ) ) {
				// Adjust for WPML support
				$target_id = apply_filters( 'translate_object_id', $target_id, 'page', true, null );
				$url = get_permalink( $target_id );
			} else {
				return sprintf( '<!- %s ->', __( 'target_id not valid', 'wpv-views' ) );
			}
		}

		$this->view_used_ids[] = $id;
		
        array_push( $this->view_shortcode_attributes, $atts );
        
        // Shall we look up in the cache? Is this a Parametric Search View?
        $is_cacheable = $this->is_cacheable( $id ) && $this->does_view_have_form_controls( $id );
        $cache_id = $this->get_cache_id( $id );
        $target_id = $atts['target_id']; // int, > 0
        if( $is_cacheable ) {
            
            // Is it cached?
            $cached_filter_index = get_option( 'wpv_transient_viewform_index', array() );
            $is_cached = isset( $cached_filter_index[$cache_id] ) && $cached_filter_index[$cache_id];
            
            if( $is_cached ) {
                
                $trasient = 'wpv_transient_viewform_'.$cache_id.'_'.$target_id; // strlen() <= 45
                $cached_filter = get_transient( $trasient );
                if( $cached_filter !== false ) {
                    array_pop( $this->view_shortcode_attributes );
                    return $cached_filter;
                }
                // We should not reach this line if everything is working fine
                // But if not, let's continue
            }
            
        }
        

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
				
				// @todo this is not needed anymore, we already translate the $url above
				// on the only case it is a permalink to a given post ID
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
			
			$requires_current_page = false;
			/**
			* wpv_filter_requires_current_page
			*
			* Whether the current View requires the current page for any filter
			*
			* @param $requires_current_page boolean
			* @param $view_settings
			*
			* @since unknown
			*/
			$requires_current_page = apply_filters('wpv_filter_requires_current_page', $requires_current_page, $view_settings);
			if ( $requires_current_page ) {
				$current_post = $this->get_top_current_page();
				if (
					$current_post 
					&& isset( $current_post->ID ) 
				) {
					$out .= '<input id="wpv_post_id-' . esc_attr( $this->get_view_count() ) . '" type="hidden" name="wpv_post_id" value="' . esc_attr( $current_post->ID ) . '" class="js-wpv-keep-on-clear" />';
				}
			}
			
			$requires_parent_term = false;
			/**
			* wpv_filter_requires_parent_term
			*
			* Whether the current View is nested and requires the parent term for any filter
			*
			* @param $requires_parent_term boolean
			* @param $view_settings
			*
			* @since unknown
			*/
			$requires_parent_term = apply_filters( 'wpv_filter_requires_parent_term', $requires_parent_term, $view_settings );
			if ( $requires_parent_term ) {
				$parent_term_id = $this->get_parent_view_taxonomy();
				if ( $parent_term_id ) {
					$out .= '<input id="wpv_aux_parent_term_id-' . esc_attr( $this->get_view_count() ) . '" type="hidden" name="wpv_aux_parent_term_id" value="' . esc_attr( $parent_term_id ) . '" class="js-wpv-keep-on-clear" />';
				}
			}
			
			$requires_parent_user = false;
			/**
			* wpv_filter_requires_parent_user
			*
			* Whether the current View is nested and requires the parent user for any filter
			*
			* @param $requires_parent_user boolean
			* @param $view_settings
			*
			* @since unknown
			*/
			$requires_parent_user = apply_filters( 'wpv_filter_requires_parent_user', $requires_parent_user, $view_settings );
			if ( $requires_parent_user ) {
				$parent_user_id = $this->get_parent_view_user();
				if ( $parent_user_id ) {
					$out .= '<input id="wpv_aux_parent_user_id-' . esc_attr( $this->get_view_count() ) . '" type="hidden" name="wpv_aux_parent_user_id" value="' . esc_attr( $parent_user_id ) . '" class="js-wpv-keep-on-clear" />';
				}
			}
			
			$out .= '</form>';

			$this->current_view = array_pop( $this->view_ids );
			if ( $this->current_view == null ) {
				$this->current_view = $id;
			}
			$this->view_depth--;

            // Update Views cache if applicable
            if( $is_cacheable ) {

                // Update Views Filter Content Cache
                $trasient = 'wpv_transient_viewform_'.$cache_id;
                $is_stored = set_transient( $trasient, $out, DAY_IN_SECONDS );

                // Update Views Filter Index
                if( $is_stored === true ) {
                    $cached_filter_index = get_option( 'wpv_transient_viewform_index', array() );
                    $cached_filter_index = $cached_filter_index === false ? array() : $cached_filter_index;
                    $cached_filter_index[$cache_id] = true;
                    update_option( 'wpv_transient_viewform_index', $cached_filter_index );
                }
            }
            
		}

		array_pop( $this->view_shortcode_attributes );

		//$this->returned_ids_for_parametric_search = array();
		$this->rendering_views_form_in_progress = false;

		return $out;
	}
	
	function remove_html_comments_from_shortcode_output( $out ) {
		$out = str_replace('<!-- wpv-loop-start -->', '', $out);
		$out = str_replace('<!-- wpv-loop-end -->', '', $out);
		return $out;
	}

    /**
     * Invalidate Views first page cache if necessary
     *  
     * @since 1.10
     * @param any $p is anything being sent by the action
     */
    function invalidate_views_cache( $p ) {
        // Invalidate Views Cache when
        // - A (any post-type) Post is created/updated/trashed/deleted...
        // - A Taxonomy Term has been created/updated/...
        // - An User has been created/updated
        // - A View has been updated
        
        // Remove both [wpv-view] and [wpv-form-view] caches
        $cached_output_index = get_option( 'wpv_transient_view_index', array() );
		foreach( $cached_output_index as $cache_id => $v ) {
			$trasient = 'wpv_transient_view_'.$cache_id;
			delete_transient( $trasient );
		}
        delete_option( 'wpv_transient_view_index' );
        
        $cached_filter_index = get_option( 'wpv_transient_viewform_index', array() );
		foreach( $cached_filter_index as $cache_id => $v ) {
			$trasient = 'wpv_transient_viewform_'.$cache_id;
			delete_transient( $trasient );
		}
        delete_option( 'wpv_transient_viewform_index' );
    }
   
    /**
     * Can we use cache for this View?
     * @param int $view_id View ID
     * @return boolean
     */
    function is_cacheable( $view_id ) {
        
        global $WPV_settings, $WPVDebug, $sitepress;
        
        // For the sake of performance, we should check lightest issues first
        
        /* Rule 0: Only the default first page can be cached */
        $is_first_page = ! isset( $_GET['wpv_view_count'] ); // Absence of this parameter means first page
        if( ! $is_first_page ) {
            return false;
        }

        /* Rule 1: Cache cannot be used while the user is debugging Views */
        $is_debug_mode_on = isset( $WPV_settings->wpv_debug_mode ) && ! empty( $WPV_settings->wpv_debug_mode );
        $current_user_can_debug = $WPVDebug->user_can_debug();       
        if( $is_debug_mode_on && $current_user_can_debug ) {
            return false;
        }

        /* Rule 2: Only "name" and/or "id" attributes are allowed (exception: "target_id" in some Parametric Search Views) */
        $view_attributes = $this->get_view_shortcodes_attributes();
		$accepted_attributes = array( 'name', 'id' );
        if( $this->does_view_have_form_controls( $view_id ) ) {
            $accepted_attributes[] = 'target_id';
        }
        // If target_id is not numeric and nor do be bigger than zero, does not qualify for caching
        if( isset( $view_attributes['target_id'] ) && ( $view_attributes['target_id'] === 'self' || intval( $view_attributes['target_id'] ) == 0 ) ) {
            return false;
        }
        // Remove accepted attributes
		foreach ( $accepted_attributes as $attribute_name ) {
			if ( isset( $view_attributes[ $attribute_name ] ) ) {
				unset( $view_attributes[ $attribute_name ] );
			}
		}
        // If there are still attributes, something is wrong
		if ( ! empty( $view_attributes ) ) {
			return false;
		}
        
        $view_settings = $this->get_view_settings( $view_id );
        
        /* Rule 3: Random-sorted Views cannot be cached, obviously */
        if( isset( $view_settings['orderby'] ) && 'rand' === $view_settings['orderby'] ) {
            return false;
        }
        
        /* Rule 4: Environment-bound Views cannot be cached (at this time) */
        $requirement_list = array(
            'wpv_filter_requires_current_page', // Depends on the current page (bound by Page ID)?
            'wpv_filter_requires_current_archive', // Depends on the current archive page?
            'wpv_filter_requires_current_user', // Depends on the current author?
            'wpv_filter_requires_parent_user', // Is it nested? Depends on the current author?
            'wpv_filter_requires_parent_term' // Is it nested? Depends on the current term?
            );

        foreach($requirement_list as $requirement) {
            $requirement_result = apply_filters( $requirement, false, $view_settings );
            if( $requirement_result ) {
                return false;
            }
        }
        
        /* Rule 5: Themes that modify the query disallow caching */ 
        $view_settings_no_override = $this->get_view_settings( $view_id, null, true );
        $requirement_result = apply_filters( 'wpv_filter_requires_framework_values', false, $view_settings_no_override );
        if( $requirement_result ) {
            return false;
        }
        
        /* Rule 6: Manually-disabled caching. View explicitly demanded not to be cached */
        if( isset( $view_settings['disable_caching'] ) && true === $view_settings['disable_caching'] ) {
            return false;
        }
        
        /* Rule 7: Filter-dependent caching. Apply filter on $view_id, settings($view_id), attributes($view_id) */
        $view_attributes = $this->get_view_shortcodes_attributes();
        /**
		* wpv_filter_disable_caching
		*
		* Disable caching if certains conditions are met
		*
		* @param $view_id
		* @param $view_settings
		* @param $view_attributes
		*
		* @since unknown
		*/
        $requirement_result = apply_filters( 'wpv_filter_disable_caching', false, $view_id, $view_settings, $view_attributes );
        if( $requirement_result ) {
            return false;
        }
        
        /* Rule 8: WPML is active */
        if( is_object( $sitepress ) ) {
            return false;
        }
        
        /* Rule 9: Views that retrieve values through URL parameters (Non-Parametric Search Views) */
        // TODO: Define methods 
        
        /* Rule N: Guess caching can be allow at this point */
        return true;
    }
    
    /**
     * Generates an unique ID for the cache
     * @param type $id
     * @return string | strlen(string) <= 20
     */
    function get_cache_id( $id ) {
        // FIXME: Instead of using $id, we could use a hash function 
        // and take into account parameters and other environmental variables
        
        return $id;
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
		$attr_attr = '';
		$attr = $this->get_view_shortcodes_attributes();
		$ignore = array(
			'name',
			'id',
			'target_id',
			'view_display',
			'limit',
			'offset',
			'orderby',
			'order'
		);
		foreach ( $ignore as $ig_key ) {
			if ( isset( $attr[ $ig_key ] ) ) {
				unset( $attr[ $ig_key ] );
			}
		}
		if ( ! empty( $attr ) ) {
			ksort( $attr );
			$attr_attr = 'CATTR' . md5( serialize( $attr ) );
		}
		
		$view_settings = $this->get_view_settings();
		$attr_post = '';
		$requires_current_page = false;
		/**
		* wpv_filter_requires_current_page
		*
		* Whether the current View requires the current page for any filter
		*
		* @param $requires_current_page boolean
		* @param $view_settings
		*
		* @since unknown
		*/
        $requires_current_page = apply_filters('wpv_filter_requires_current_page', $requires_current_page, $view_settings);
			if ( $requires_current_page ) {
			$current_post = $this->get_top_current_page();
			if (
				$current_post 
				&& isset( $current_post->ID )
			) {
				$attr_post = 'CPID' . intval( $current_post->ID );
			}
		}
		$attr_term = '';
		$requires_parent_term = false;
		/**
		* wpv_filter_requires_parent_term
		*
		* Whether the current View is nested and requires the parent term for any filter
		*
		* @param $requires_parent_term boolean
		* @param $view_settings
		*
		* @since unknown
		*/
		$requires_parent_term = apply_filters( 'wpv_filter_requires_parent_term', $requires_parent_term, $view_settings );
			if ( $requires_parent_term ) {
			if ( $this->get_parent_view_taxonomy() ) {
				$attr_term = 'CTID' . intval( $this->get_parent_view_taxonomy() );
			}
		}
		$attr_user = '';
		$requires_parent_user = false;
		/**
		* wpv_filter_requires_parent_user
		*
		* Whether the current View is nested and requires the parent user for any filter
		*
		* @param $requires_parent_user boolean
		* @param $view_settings
		*
		* @since unknown
		*/
		$requires_parent_user = apply_filters( 'wpv_filter_requires_parent_user', $requires_parent_user, $view_settings );
		if ( $requires_parent_user ) {
			if ( $this->get_parent_view_user() ) {
				$attr_user = 'CUID' . intval( $this->get_parent_view_user() );
			}
		}

		return $this->current_view . '-' . $attr_attr . $attr_post . $attr_term . $attr_user;
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
	 * @param boolean $disable_override avoid applying 'wpv_filter_override_view_settings' filter
     * 
	 * @return array View's settings.
	 *
	 * @since unknown
	 */
	 
	function get_view_settings( $view_id = null, $post_meta = null, $disable_override = false ) {
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
		* Only used to set default values that need to be there on the returned array, but may not be there for legacy reasons
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
		
        if( ! $disable_override ) {
            $view_settings = apply_filters( 'wpv_filter_override_view_settings', $view_settings, $view_id );
        }
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
		* Only used to set default values that need to be there on the returned array, but may not be there for legacy reasons
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
	* wpv_view_layout_settings_set_fallbacks
	*
	* Callback hooked into the wpv_view_settings filter to set default values
	* that should be in the _wpv_settings postmeta but might be missing somehow
	*
	* @param $view_settings (array)
	* @param $view_id (integer)
	*
	* @return $view_settings (array)
	*
	* @since 1.8.1
	*/
	
	function wpv_view_layout_settings_set_fallbacks( $view_layout_settings, $view_id ) {
		if ( ! is_array( $view_layout_settings ) ) {
			$view_layout_settings = array();
		}
		return $view_layout_settings;
	}
	
	/**
	* clean_current_loop_user
	*
	* Clean the global data for the current user on a loop for a View listing users right after rendering it.
	*
	* This is useful and needed to avoid data leaking caused for persistance of this global values, related to the last rendered user.
	* Without this, the wpv-user shortcode used on a View listing users but ourside the loop, or after the View has been rendered
	* will reurn values related to the last rendered user, instead to the current user as default.
	*
	* @since 1.10
	*/

	function clean_current_loop_user() {
		$this->users_data['term'] = null;
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
			if (
				$this->parent_taxonomy 
				&& isset( $_GET['wpv_aux_parent_term_id'] ) 
				&& is_numeric( $_GET['wpv_aux_parent_term_id'] ) 
				&& $_GET['wpv_aux_parent_term_id'] == $this->parent_taxonomy
			) {
				$this->parent_taxonomy = intval( $_GET['wpv_aux_parent_term_id'] );
			} else {
				$this->parent_taxonomy = 0;
			}
		}
		$tmp_taxonomy_data = $this->taxonomy_data;

		// save original users if any
		$tmp_parent_user = $this->parent_user;
		if ( isset( $this->users_data['term'] ) ) {
			$this->parent_user = $this->users_data['term']->ID;
		} else {
			if (
				$this->parent_user 
				&& isset( $_GET['wpv_aux_parent_user_id'] ) 
				&& is_numeric( $_GET['wpv_aux_parent_user_id'] )
				&& $_GET['wpv_aux_parent_user_id'] == $this->parent_user
			) {
				$this->parent_user = intval( $_GET['wpv_aux_parent_user_id'] );
			} else {
				$this->parent_user = 0;
			}
		}
		$tmp_users_data = $this->users_data;

		$out =  $this->render_view( $id, $hash );

		if (
			$post_exists
			&& $this->is_archive_view( $id )
		) {
			/**
			* On WPAs, the global $post is valid inside the <wpv-loop></wpv-loop> loop, since each post sets its global,
			* but outside that loop, the global $post was defaulting to the first post in the global $wp_query.
			*
			* It caused that Views used outside the loop with "Don't include current page in query result" turned on
			* were not including the first result, when they should.
			*
			* So we need to temporarily unset the global $post when expanding shortcodes outside the loop on WPA.
			*
			* @since 1.10
			*/
			$temp_post = $post;
			$post = null;
		}
		
		
		$out = wpv_do_shortcode( $out );
		
		if (
			$post_exists
			&& $this->is_archive_view( $id )
		) {
			/**
			* Restore back the current global $post.
			*
			* Not sure this is needed at all, but better keep it just in case.
			*
			* @since 1.10
			*/
			$post = $temp_post;
			$temp_post = null;
		}

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
	* add_dialog_to_editors
	*
	* Add the Fields and Views button to the edit pages
	*
	* @since unknown
	*/
	function add_dialog_to_editors() {
		global $post;

		if ( is_object( $post ) === false ) {
			return;
		}

		$this->editor_addon = new WPV_Editor_addon(
			'wpv-views',
			__( 'Insert Views Shortcodes', 'wpv-views' ),
			WPV_URL . '/res/js/views_editor_plugin.js',
			'',
			true,
			'icon-views-logo ont-icon-18 ont-color-gray' 
		);

		add_short_codes_to_js( 
			array( 
				'post', 					// wpv-post-** shortcodes plus non-Types custom fields
				'post-extended',			// generic shortcodes extended in the Basic section
				'post-fields-placeholder',	// non-Types fields on demand
				'types-post',				// Types custom fields
				'types-post-usermeta',		// Types usermeta fields
				'user',						// basic user data
				'post-view',				// all available Views listing posts
				'taxonomy-view',			// all available Views listing terms
				'user-view',				// all available Views listing users
				'body-view-templates',		// all available CT
				'wpml'						// WPML-related shortcodes
			), 
			$this->editor_addon 
		);
			
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

	// @deprecated - to delete - not used anywhere in Views
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
	* Get visible custom field keys and hidden custom field keys declared as such
	*
	* @param int $cf_keys_limit maximum number of keys retrievable from database. Greater than 0.
	*
	* @since unknown
	*/
    function get_meta_keys( $cf_keys_limit = 512 ) {
        
        return $this->_get_meta_keys_internal( true, $cf_keys_limit );
        
    }
    
    /**
	* Get hidden custom field keys from database and Types
	*
	* @param int $cf_keys_limit maximum number of keys retrievable from database. Greater than 0.
	*
	* @since 1.10
	*/
    function get_hidden_meta_keys( $cf_keys_limit = 512 ) {
        
        return $this->_get_meta_keys_internal( false, $cf_keys_limit );
        
    }
    
    /**
	* Is this custom field visible?
	*
	* @param string $custom_field_key
	*
	* @return bool hidden fields declared as visible return true.
	*
	* @since 1.10
	*/
    private function custom_field_is_visible( $custom_field_key ) {
        
        static $cf_hidden_declared_visible = array();
        
        if( empty( $cf_hidden_declared_visible ) ) {
         
            global $WPV_settings;
            if( isset( $WPV_settings->wpv_show_hidden_fields ) && is_string( $WPV_settings->wpv_show_hidden_fields ) ) {
                $cf_hidden_declared_visible = explode( ',', $WPV_settings->wpv_show_hidden_fields );
            }
        }
        
        return substr( $custom_field_key, 0, 1 ) != '_' || in_array( $custom_field_key, $cf_hidden_declared_visible );
    }
    
    /**
	* Is this custom field hidden?
	*
	* @param string $custom_field_key name of the custom field.
	*
	* @return bool hidden fields declared as visible return true.
	*
	* @since 1.10
	*/
    private function custom_field_is_hidden( $custom_field_key ) {
        return substr( $custom_field_key, 0, 1 ) == '_';
    }
    
    /**
	* Retrieve custom fields.
	*
	* @param bool $is_visible
	*
	* @param int $cf_keys_limit limit database results
	*
	* @return array custom field keys
	*
	* @since 1.10
	*/
    private function _get_meta_keys_internal( $is_visible = true, $cf_keys_limit = 512 ) {
        
        if ( $is_visible ) {
            $predicate_function_name = 'custom_field_is_visible';
            $wpv_filter_keys_limit = 'wpv_filter_wpv_get_postmeta_keys_limit';
            $wpv_filter_keys_result = 'wpv_filter_wpv_get_postmeta_keys';
        } else {
            $predicate_function_name = 'custom_field_is_hidden';
            $wpv_filter_keys_limit = 'wpv_filter_wpv_get_hidden_postmeta_keys_limit';
            $wpv_filter_keys_result = 'wpv_filter_wpv_get_hidden_postmeta_keys';
        }
        
        $cf_keys = array();
        
        // Filter limit. Allow 3rd parties increase or decrease the limit.
        $cf_keys_limit = apply_filters( $wpv_filter_keys_limit, $cf_keys_limit );
        
        // Verify it is still a number or revert to default
        if( ! is_int( $cf_keys_limit ) || $cf_keys_limit <= 0 ) {
            $cf_keys_limit = 512;
        }
        
        // Cache var
        // f(request_signature:string):array = request:array
        static $cf_keys_request_cache = array();
        $cf_request_signature = ( $is_visible ? 'visible' : 'hidden' ) . $cf_keys_limit;
		

		// We hard-cache default limit for visible and hidden fields
		if ( $cf_keys_limit == 512 ) {
			$wpv_transient_meta_keys = get_transient( 'wpv_transient_meta_keys_' . $cf_request_signature );
			if ( $wpv_transient_meta_keys !== false ) {
				$cf_keys_request_cache[$cf_request_signature] = $wpv_transient_meta_keys;
			}
		}

        // Retrieve from db if keys request cache is empty or contains zero elements
        if ( 
			empty( $cf_keys_request_cache ) 
			|| ! isset( $cf_keys_request_cache[$cf_request_signature] ) 
			|| count( $cf_keys_request_cache[$cf_request_signature] ) == 0 
		) {
            
            // Retrieve keys from postmeta (unsorted)
            // If meta_key starts with underscore, it is a hidden field
            // It's limited because DISTINCT queries are slow            
            global $wpdb, $WPV_settings;
            $cf_keys_request = $wpdb->get_col( 
                    $is_visible 
                    ?
                            /* visible keys and hidden ones declared as such */
                            $wpdb->prepare(
                                "SELECT DISTINCT meta_key
                                FROM {$wpdb->postmeta}
                                WHERE "
                                .(isset( $WPV_settings->wpv_show_hidden_fields ) 
                                        && is_string( $WPV_settings->wpv_show_hidden_fields ) 
                                        && strlen( $WPV_settings->wpv_show_hidden_fields ) > 0
                                        ? "meta_key IN ('" . implode( "','", explode( ',', $WPV_settings->wpv_show_hidden_fields ) ) . "') OR " : "" ).
                                " /* hidden declared as visible */
                                LEFT(meta_key, 1) <> '_' /* visible */
                                LIMIT %d",
                                $cf_keys_limit 
                            )
                                
                    :
                            /* hidden keys only */
                            $wpdb->prepare(
                                "SELECT DISTINCT meta_key
                                FROM {$wpdb->postmeta}
                                WHERE LEFT(meta_key, 1) = '_' /* hidden */
                                LIMIT %d",
                                $cf_keys_limit 
                            )
			);

            // Retrieve keys from Types (unsorted)
            if ( function_exists( 'wpcf_get_post_meta_field_names' ) ) {
                
				$types_fields = wpcf_get_post_meta_field_names();
                $types_fields_filtered = array_filter( $types_fields, array( $this, $predicate_function_name ) );
                $cf_keys_from_types = array_unique( $types_fields_filtered );
                $cf_keys_request = array_merge( $cf_keys_request, $cf_keys_from_types );
                
            }
            
            // Exclude there keys
            $cf_keys_exceptions = array(
                '_edit_last', '_edit_lock', '_wp_page_template', '_wp_attachment_metadata', '_icl_translator_note', '_alp_processed',
                '_icl_translation', '_thumbnail_id', '_views_template', '_wpml_media_duplicate', '_wpml_media_featured',
                '_top_nav_excluded', '_cms_nav_minihome',
                'wpml_media_duplicate_of', 'wpml_media_lang', 'wpml_media_processed',
                '_wpv_settings', '_wpv_layout_settings', '_wpv_view_sync',
                '_wpv_view_template_fields', // DEPRECATED
				'_wpv_view_template_mode',
                'dd_layouts_settings' );
			$cf_keys_request = array_diff( $cf_keys_request, $cf_keys_exceptions );
            
            // Update cache
			if ( $cf_keys_limit == 512 ) {
				set_transient( 'wpv_transient_meta_keys_' . $cf_request_signature, $cf_keys_request, WEEK_IN_SECONDS );
			}
            $cf_keys_request_cache[$cf_request_signature] = $cf_keys_request;
            
        } else {
            
            $cf_keys_request = $cf_keys_request_cache[$cf_request_signature];
            
        }
        
        // Filter result. Allow third-party developers add or remove elements.
        $cf_keys = apply_filters( $wpv_filter_keys_result, $cf_keys_request );
        
        // Remove duplicates and sort result naturally.
        $cf_keys = array_unique( $cf_keys );
        // FIXME: Why is sorting done inside the method? (Legacy)
        if ( $cf_keys && is_array( $cf_keys ) ) {
            natcasesort( $cf_keys );
        }
        
        return $cf_keys;
        
    }
	
	/**
	* delete_transient_meta_keys
	*
	* Invalidate wpv_transient_meta_keys_*** cache when:
	* 	creating, updating or deleting a post
	* 	creating, updating or deleting a postmeta
	* 	creating, updating or deleting a Types field group
	*
	* @since 1.10
	*/
	
	function delete_transient_meta_keys() {
		delete_transient( 'wpv_transient_meta_keys_visible512' );
		delete_transient( 'wpv_transient_meta_keys_hidden512' );
	}
	
	/**
	* Get visible usermeta field keys
	*
	* @param int $usermeta_keys_limit maximum number of keys retrievable from database. Greater than 0.
	*
	* @since unknown
	*/
    function get_usermeta_keys( $usermeta_keys_limit = 512 ) {
        
        return $this->_get_usermeta_keys_internal( true, $usermeta_keys_limit );
        
    }
    
    /**
	* Get hidden usermeta field keys
	*
	* @param int $usermeta_keys_limit maximum number of keys retrievable from database. Greater than 0.
	*
	* @since 1.10
	*/
    function get_hidden_usermeta_keys( $usermeta_keys_limit = 512 ) {
        
        return $this->_get_usermeta_keys_internal( false, $usermeta_keys_limit );
        
    }
	
	/**
	* Is this custom field visible?
	*
	* @param string $usermeta_field_key
	*
	* @return bool hidden fields declared as visible return true.
	*
	* @since 1.10
	*/
    private function usermeta_field_is_visible( $usermeta_field_key ) {
        return substr( $usermeta_field_key, 0, 1 ) != '_';
    }
    
    /**
	* Is this custom field hidden?
	*
	* @param string $usermeta_field_key name of the custom field.
	*
	* @return bool hidden fields declared as visible return true.
	*
	* @since 1.10
	*/
    private function usermeta_field_is_hidden( $usermeta_field_key ) {
        return substr( $usermeta_field_key, 0, 1 ) == '_';
    }
	
	/**
	* Is this custom field hidden?
	*
	* @param string $usermeta_field_key name of the custom field.
	*
	* @return bool hidden fields declared as visible return true.
	*
	* @since 1.10
	*/
    private function usermeta_field_is_skipped( $usermeta_field_key ) {
		$return = true;
		// Exclude these keys
		$hidden_usermeta = array(
			'first_name', 'last_name', 'name', 'nickname', 'description', 'yim', 'jabber', 'aim',
			'rich_editing', 'comment_shortcuts', 'admin_color', 'use_ssl', 'show_admin_bar_front',
			'capabilities', 'user_level', 'user-settings',
			'dismissed_wp_pointers','show_welcome_panel',
			'dashboard_quick_press_last_post_id', 'managenav-menuscolumnshidden',
			'primary_blog', 'source_domain',
			'closedpostboxes', 'metaboxhidden', 'meta-box-order_dashboard', 'meta-box-order', 'nav_menu_recently_edited',
			'new_date', 'show_highlight', 'language_pairs',
			'module-manager',
			'screen_layout', 'session_tokens',
			'hide_wpcf_welcome_panel'
		);
		if ( in_array( $usermeta_field_key, $hidden_usermeta ) ) {
			$return = false;
		}
		
        return $return;
    }
	
	/**
	* Retrieve custom fields.
	*
	* @param bool $is_visible
	*
	* @param int $usermeta_keys_limit limit database results
	*
	* @return array custom field keys
	*
	* @since 1.10
	*/
    private function _get_usermeta_keys_internal( $is_visible = true, $usermeta_keys_limit = 512 ) {
        
        if ( $is_visible ) {
            $predicate_function_name = 'usermeta_field_is_visible';
            $wpv_filter_keys_limit = 'wpv_filter_wpv_get_usermeta_keys_limit';
            $wpv_filter_keys_result = 'wpv_filter_wpv_get_usermeta_keys';
        } else {
            $predicate_function_name = 'usermeta_field_is_hidden';
            $wpv_filter_keys_limit = 'wpv_filter_wpv_get_hidden_usermeta_keys_limit';
            $wpv_filter_keys_result = 'wpv_filter_wpv_get_hidden_usermeta_keys';
        }
        
        $cf_keys = array();
        
        // Filter limit. Allow 3rd parties increase or decrease the limit.
        $usermeta_keys_limit = apply_filters( $wpv_filter_keys_limit, $usermeta_keys_limit );
        
        // Verify it is still a number or revert to default
        if( ! is_int( $usermeta_keys_limit ) || $usermeta_keys_limit <= 0 ) {
            $usermeta_keys_limit = 512;
        }
        
        // Cache var
        // f(request_signature:string):array = request:array
        static $usermeta_keys_request_cache = array();
        $usermeta_request_signature = ( $is_visible ? 'visible' : 'hidden' ) . $usermeta_keys_limit;
		

		// We hard-cache default limit for visible and hidden fields
		if ( $usermeta_keys_limit == 512 ) {
			$wpv_transient_meta_keys = get_transient( 'wpv_transient_usermeta_keys_' . $usermeta_request_signature );
			if ( $wpv_transient_meta_keys !== false ) {
				$usermeta_keys_request_cache[$usermeta_request_signature] = $wpv_transient_meta_keys;
			}
		}

        // Retrieve from db if keys request cache is empty or contains zero elements
        if ( 
			empty( $usermeta_keys_request_cache ) 
			|| ! isset( $usermeta_keys_request_cache[$usermeta_request_signature] ) 
			|| count( $usermeta_keys_request_cache[$usermeta_request_signature] ) == 0 
		) {
            
            // Retrieve keys from usermeta (unsorted)
            // If meta_key starts with underscore, it is a hidden field
            // It's limited because DISTINCT queries are slow            
            global $wpdb;
			$values_to_prepare = array();
			$umf_mulsitise_string = "";
			if ( is_multisite() ) {
				global $blog_id;
				$umf_mulsitise_string = " AND ( meta_key NOT REGEXP '^{$wpdb->base_prefix}[0-9]_' OR meta_key REGEXP '^{$wpdb->base_prefix}%d_' ) ";
				$values_to_prepare[] = $blog_id;
			}
			$values_to_prepare[] = $usermeta_keys_limit;
			$usermeta_keys = $wpdb->get_col( 
					$is_visible 
                    ?
						$wpdb->prepare(
							"SELECT DISTINCT meta_key 
							FROM {$wpdb->usermeta} 
							WHERE LEFT(meta_key, 1) <> '_' /* visible */
							{$umf_mulsitise_string}
							LIMIT %d",
							$values_to_prepare
						)
					:
						$wpdb->prepare(
							"SELECT DISTINCT meta_key FROM {$wpdb->usermeta} 
							WHERE LEFT(meta_key, 1) == '_' /* hidden */
							{$umf_mulsitise_string}
							LIMIT %d",
							$values_to_prepare
						)
			);

            // Retrieve keys from Types (unsorted) @todo
			/*
            if ( function_exists( 'wpcf_get_post_meta_field_names' ) ) {
                
				$types_fields = wpcf_get_post_meta_field_names();
                $types_fields_filtered = array_filter( $types_fields, array( $this, $predicate_function_name ) );
                $cf_keys_from_types = array_unique( $types_fields_filtered );
                $cf_keys_request = array_merge( $cf_keys_request, $cf_keys_from_types );
                
            }
			*/
			
			$usermeta_keys = array_filter( $usermeta_keys, array( $this, 'usermeta_field_is_skipped' ) );
            
            // Update cache
			if ( $usermeta_keys_limit == 512 ) {
				set_transient( 'wpv_transient_usermeta_keys_' . $usermeta_request_signature, $usermeta_keys, WEEK_IN_SECONDS );
			}
            $usermeta_keys_request_cache[$usermeta_request_signature] = $usermeta_keys;
            
        } else {
            
            $usermeta_keys = $usermeta_keys_request_cache[$usermeta_request_signature];
            
        }
        
        // Filter result. Allow third-party developers add or remove elements.
        $um_keys = apply_filters( $wpv_filter_keys_result, $usermeta_keys );
        
        // Remove duplicates and sort result naturally.
        $um_keys = array_unique( $um_keys );
        // FIXME: Why is sorting done inside the method? (Legacy)
        if ( $um_keys && is_array( $um_keys ) ) {
            natcasesort( $um_keys );
        }
        
        return $um_keys;
        
    }
	
	/**
	* delete_transient_usermeta_keys
	*
	* Invalidate wpv_transient_meta_keys_*** cache when:
	* 	creating, updating or deleting a post
	* 	creating, updating or deleting a postmeta
	* 	creating, updating or deleting a Types field group
	*
	* @since 1.10
	*/
	
	function delete_transient_usermeta_keys() {
		delete_transient( 'wpv_transient_usermeta_keys_visible512' );
		delete_transient( 'wpv_transient_usermeta_keys_hidden512' );
	}
    
	/**
	 * If the post has a view, add an view edit link to post.
	 */
	function edit_post_link( $link, $post_id ) {
		// do nothing for theme version.
		return $link;
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

	/**
	 * See if a view has any enabled from controls.
	 */
	function does_view_have_form_controls( $view_id ) {
		$view_settings = $this->get_view_settings( $view_id );

		/*
		// @todo this seems broken - when deleting all parametric search items, we still have this available :-O
		// So... trust just actual filter shortcodes
		if ( isset( $view_settings['filter_controls_enable'] ) && is_array( $view_settings['filter_controls_enable'] ) ) {
			foreach( $view_settings['filter_controls_enable'] as $enable ) {
				if ( $enable ) {
					return true;
				}
			}
		}
		*/

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
			$is_wpa = $this->is_archive_view( $view_id );
			$cssout_item = '';
			if ( 
				isset( $meta['filter_meta_html_css'] ) 
				&& '' != $meta['filter_meta_html_css']
			) {
				$cssout_item .= $meta["filter_meta_html_css"] . "\n";
			}
			if ( 
				isset( $meta['layout_meta_html_css'] ) 
				&& '' != $meta['layout_meta_html_css']
			) {
				$cssout_item .= $meta["layout_meta_html_css"] . "\n";
			}
			if ( '' != $cssout_item ) {
				$cssout_item_title = get_the_title( $view_id );
				$cssout .= "/* ----------------------------------------- */\n";
				if ( $is_wpa ) {
					$cssout .= "/* " . sprintf( __( 'WordPress Archive: %s - start', 'wpv-views' ), $cssout_item_title ) . " */\n";
				} else {
					$cssout .= "/* " . sprintf( __( 'View: %s - start', 'wpv-views' ), $cssout_item_title ) . " */\n";
				}
				$cssout .= "/* ----------------------------------------- */\n";
				$cssout .= $cssout_item;
				$cssout .= "/* ----------------------------------------- */\n";
				if ( $is_wpa ) {
					$cssout .= "/* " . sprintf( __( 'WordPress Archive: %s - end', 'wpv-views' ), $cssout_item_title ) . " */\n";
				} else {
					$cssout .= "/* " . sprintf( __( 'View: %s - end', 'wpv-views' ), $cssout_item_title ) . " */\n";
				}
				$cssout .= "/* ----------------------------------------- */\n";
			}
		}
		if ( '' != $cssout ) {
			echo "\n<style type=\"text/css\" media=\"screen\">\n" . $cssout . "</style>\n";
		}
	}

	function wpv_meta_html_extra_js() {
		$view_ids = array_unique( $this->view_used_ids );
		$jsout = '';
		foreach ( $view_ids as $view_id ) {
			$meta = $this->get_view_settings( $view_id );
			$is_wpa = $this->is_archive_view( $view_id );
			$jsout_item = '';
			if ( 
				isset( $meta['filter_meta_html_js'] ) 
				&& '' != $meta['filter_meta_html_js']
			) {
				$jsout_item .= $meta["filter_meta_html_js"] . "\n";
			}
			if ( 
				isset( $meta['layout_meta_html_js'] ) 
				&& '' != $meta['layout_meta_html_js']
			) {
				$jsout_item .= $meta["layout_meta_html_js"] . "\n";
			}
			if ( '' != $jsout_item ) {
				$jsout_item_title = get_the_title( $view_id );
				$jsout .= "//-----------------------------------------\n";
				if ( $is_wpa ) {
					$jsout .= "// " . sprintf( __( 'WordPress Archive: %s - start', 'wpv-views' ), $jsout_item_title ) . "\n";
				} else {
					$jsout .= "// " . sprintf( __( 'View: %s - start', 'wpv-views' ), $jsout_item_title ) . "\n";
				}
				$jsout .= "//-----------------------------------------\n";
				$jsout .= $jsout_item;
				$jsout .= "//-----------------------------------------\n";
				if ( $is_wpa ) {
					$jsout .= "// " . sprintf( __( 'WordPress Archive: %s - end', 'wpv-views' ), $jsout_item_title ) . "\n";
				} else {
					$jsout .= "// " . sprintf( __( 'View: %s - end', 'wpv-views' ), $jsout_item_title ) . "\n";
				}
				$jsout .= "//-----------------------------------------\n";
			}
		}
		if ( '' != $jsout ) {
			echo "\n<script type=\"text/javascript\">\n" . $jsout . "</script>\n";
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
	
	function wpv_register_assets() {

		/* ---------------------------- /*
		/* BACKEND SCRIPTS
		/* ---------------------------- */
		
		/* @todo MOVE TO COMMON */
			
		// Colorbox
		// @todo move to common
		wp_deregister_script( 'toolset-colorbox' );
		wp_register_script( 'toolset-colorbox', WPV_URL_EMBEDDED . '/res/js/lib/jquery.colorbox-min.js', array( 'jquery' ), WPV_VERSION	);

        // URI.js
        // @todo move to common
        if( ! wp_script_is( 'toolset-uri-js', 'registered' ) ) {
            wp_register_script( 'toolset-uri-js', WPV_URL_EMBEDDED . '/res/js/uri-js/URI.min.js', array(), WPV_VERSION );
        }
        if( ! wp_script_is( 'toolset-uri-js-jquery-plugin', 'registered' ) ) {
            wp_register_script( 'toolset-uri-js-jquery-plugin', WPV_URL_EMBEDDED . '/res/js/uri-js/jquery.URI.min.js', array( 'jquery', 'toolset-uri-js' ), WPV_VERSION );
        }

		// Select2 script
		// @todo move to common
		if ( ! wp_script_is( 'select2', 'registered' ) ) {
			wp_register_script( 'select2', WPV_URL_EMBEDDED . '/common/utility/js/select2.min.js', array( 'jquery' ), WPV_VERSION );
		}
		// Toolset utils script
		// @todo make a diff and move to common
		if ( ! wp_script_is( 'toolset-utils', 'registered' ) ) {
			wp_register_script( 'toolset-utils', WPV_URL_EMBEDDED . "/common/utility/js/utils.js", array( 'jquery', 'underscore', 'backbone'), '1.0', true );
		}
		// CodeMirror
		// @todo move to common
		wp_register_script( 'views-codemirror-script', WPV_URL_EMBEDDED . '/res/js/codemirror/lib/codemirror.js', array(), WPV_VERSION, false);
		wp_register_script( 'views-codemirror-overlay-script', WPV_URL_EMBEDDED . '/res/js/codemirror/addon/mode/overlay.js', array( 'views-codemirror-script' ), WPV_VERSION, false);
		wp_register_script( 'views-codemirror-xml-script', WPV_URL_EMBEDDED . '/res/js/codemirror/mode/xml/xml.js', array( 'views-codemirror-overlay-script' ), WPV_VERSION, false);
		wp_register_script( 'views-codemirror-css-script', WPV_URL_EMBEDDED . '/res/js/codemirror/mode/css/css.js', array( 'views-codemirror-overlay-script' ), WPV_VERSION, false);
		wp_register_script( 'views-codemirror-js-script', WPV_URL_EMBEDDED . '/res/js/codemirror/mode/javascript/javascript.js', array( 'views-codemirror-overlay-script' ), WPV_VERSION, false);
		wp_register_script( 'views-codemirror-addon-searchcursor-script', WPV_URL_EMBEDDED . '/res/js/codemirror/addon/search/searchcursor.js', array( 'views-codemirror-script' ), WPV_VERSION, false);
		wp_register_script( 'views-codemirror-addon-panel-script', WPV_URL_EMBEDDED . '/res/js/codemirror/addon/display/panel.js', array( 'views-codemirror-script' ), WPV_VERSION, false);
		wp_register_script( 
			'views-codemirror-conf-script', 
			WPV_URL_EMBEDDED . '/res/js/views_codemirror_conf.js', 
			array( 
				'jquery', 
				'views-codemirror-script', 
				'views-codemirror-overlay-script', 
				'views-codemirror-xml-script', 'views-codemirror-css-script', 'views-codemirror-js-script', 
				'views-codemirror-addon-searchcursor-script', 
				'views-codemirror-addon-panel-script' 
			), 
			WPV_VERSION, 
			false
		);
		
		// DEPRECATED
		// Keep views-select2-script because the installed version of other plugin might be using it - just register, never enqueue
		wp_register_script( 'views-select2-script', WPV_URL_EMBEDDED . '/common/utility/js/select2.min.js', array( 'jquery' ), WPV_VERSION );
			
		// Views utils script
		// @todo diff with toolset-utils, this might be redundant once we ditch Colorbox
		wp_register_script( 'views-utils-script', WPV_URL_EMBEDDED . '/res/js/lib/utils.js', array( 'jquery','toolset-colorbox', 'select2', 'toolset-utils' ), WPV_VERSION );
		$help_box_translations = array(
				'wpv_dont_show_it_again' => __( "Got it! Don't show this message again", 'wpv-views'),
				'wpv_close' => __( 'Close', 'wpv-views') );
		wp_localize_script( 'views-utils-script', 'wpv_help_box_texts', $help_box_translations );
		
		// Shortcodes GUI script
		wp_register_script( 'views-shortcodes-gui-script', WPV_URL_EMBEDDED . '/res/js/views_shortcodes_gui.js', array( 'jquery', 'suggest', 'jquery-ui-dialog', 'jquery-ui-tabs', 'views-utils-script', 'quicktags' ), WPV_VERSION );
		$shortcodes_gui_translations = array(
			'wpv_insert_shortcode'				=> __( 'Insert shortcode', 'wpv-views'),
			'wpv_insert_view_shortcode'			=> __( 'Insert shortcode', 'wpv-views' ),
			'wpv_close'							=> __( 'Close', 'wpv-views'),
			'wpv_save_settings'					=> __( 'Save settings', 'wpv-views' ),
			'wpv_cancel'						=> __( 'Cancel', 'wpv-views' ),
			'wpv_previous'						=> __( 'Previous', 'wpv-views' ),
			'wpv_next'							=> __( 'Next', 'wpv-views' ),
			'loading_options'					=> __( 'Loading...', 'wpv-views' ),
			'attr_number_invalid'				=> __( 'Please enter a valid number', 'wpv-views' ),
			'attr_numberlist_invalid'			=> __( 'Please enter a valid comma separated number list', 'wpv-views' ),
			'attr_year_invalid'					=> __( 'Please enter a valid four-digits year, like 2015', 'wpv-views' ),
			'attr_month_invalid'				=> __( 'Please enter a valid month number (1-12)', 'wpv-views' ),
			'attr_week_invalid'					=> __( 'Please enter a valid week number (1-53)', 'wpv-views' ),
			'attr_day_invalid'					=> __( 'Please enter a valid day number (1-31)', 'wpv-views' ),
			'attr_hour_invalid'					=> __( 'Please enter a valid hour (0-23)', 'wpv-views' ),
			'attr_minute_invalid'				=> __( 'Please enter a valid minute (0-59)', 'wpv-views' ),
			'attr_second_invalid'				=> __( 'Please enter a valid second (0-59)', 'wpv-views' ),
			'attr_dayofyear_invalid'			=> __( 'Please enter a valid day of the year (1-366)', 'wpv-views' ),
			'attr_dayofweek_invalid'			=> __( 'Please enter a valid day of the week (1-7)', 'wpv-views' ),
			'attr_url_invalid'					=> __( 'Please enter a valid URL', 'wpv-views' ),
			'attr_empty'						=> __( 'This option is mandatory ', 'wpv-views' ),
            'wpv_conditional_button'			=> __( 'conditional output', 'wpv-views' ),
			'conditional_enter_conditions_manually'		=> __('Edit conditions manually', 'wpv-views'),
			'conditional_enter_conditions_gui'			=> __('Edit conditions using the GUI', 'wpv-views'),
			'conditional_switch_alert'					=> __('Your custom conditions will be lost if you switch back to GUI editing.', 'wpv-views'),
			
            'wpv_editor_callback_nonce'         => wp_create_nonce('wpv_editor_callback')
		);
        
		wp_localize_script( 'views-shortcodes-gui-script', 'wpv_shortcodes_gui_texts', $shortcodes_gui_translations );
			
		// Views widget script
		wp_register_script( 'views-widgets-gui-script', WPV_URL_EMBEDDED . '/res/js/views_widgets_gui.js', array( 'jquery', 'suggest' ), WPV_VERSION );

		// Views embedded script
		wp_register_script( 'views-embedded-listing-pages-script', WPV_URL_EMBEDDED . '/res/js/listing_pages.js', array( 'jquery' ), WPV_VERSION, true );
		wp_register_script( 'views-embedded-script', WPV_URL_EMBEDDED . '/res/js/views_embedded.js', array( 'jquery', 'wp-pointer', 'views-codemirror-conf-script' ), WPV_VERSION, true );
			
		/* ---------------------------- /*
		/* BACKEND STYLES
		/* ---------------------------- */
		
		/* @todo MOVE TO COMMON */
			
		// FontAwesome styles
		// @todo move to common
		wp_deregister_style( 'toolset-font-awesome' );
		wp_register_style( 'toolset-font-awesome', WPV_URL_EMBEDDED . '/res/css/font-awesome/css/font-awesome.min.css', array(), WPV_VERSION );
		// Colorbox styles
		// @todo deprecate and move to common
		wp_deregister_style( 'toolset-colorbox' );
		wp_register_style( 'toolset-colorbox', WPV_URL_EMBEDDED . '/res/css/colorbox.css', array(), WPV_VERSION );
		// Select2 style
		// @todo move to common and update
		if( ! wp_style_is( 'select2', 'registered' ) ) {
			wp_register_style( 'select2', WPV_URL_EMBEDDED . '/common/utility/css/select2/select2.css', array(), WPV_VERSION );
		}
		// Notifications styles
		// @todo notifications seems to be spread, needs to go to common after a diff
		wp_register_style( 'views-notifications-css', WPV_URL_EMBEDDED . '/res/css/notifications.css', array(), WPV_VERSION );
		// CodeMirror style
		// @todo move to common
		// Note that this is the default codemirror stylesheet
		wp_register_style( 'views-codemirror-css' , WPV_URL_EMBEDDED . '/res/js/codemirror/lib/codemirror.css', array(), WPV_VERSION );
			
		// Dialogs styles
		// @todo maybe move to common too
		// Depends on:
		// 		- wp-jquery-ui-dialog
		wp_register_style( 'views-admin-dialogs-css', WPV_URL_EMBEDDED . '/res/css/dialogs.css', array( 'wp-jquery-ui-dialog' ), WPV_VERSION );

		// General Views admin style
		// Depends on:
		// 		- wp-pointer
		// 		- toolset-font-awesome
		// 		- toolset-colorbox - want to deprecate
		// 		- views-notifications-css
		// 		- views-admin-dialogs-css
		// 		- select2
		// @todo make this also dependant of the common 'editor_addon_menu' and 'editor_addon_menu_scroll'
		wp_register_style( 'views-admin-css', WPV_URL_EMBEDDED . '/res/css/views-admin.css', array( 'wp-pointer', 'toolset-font-awesome', 'toolset-colorbox', 'views-notifications-css', 'views-admin-dialogs-css', 'select2' ), WPV_VERSION );
			
		/* ---------------------------- /*
		/* FRONTEND SCRIPTS
		/* ---------------------------- */
		
		// Datepicker localization
		// Depends on:
		// 		- jquery
		// 		- jquery-ui-core
		// 		- jquery-ui-datepicker
		// @todo integrate this with WPML lang
		$lang = get_locale();
		$lang = str_replace( '_', '-', $lang );
		if ( file_exists( WPV_PATH_EMBEDDED . '/res/js/i18n/jquery.ui.datepicker-' . $lang . '.js' ) ) {
			if ( ! wp_script_is( 'jquery-ui-datepicker-local', 'registered' ) ) {
				wp_register_script( 'jquery-ui-datepicker-local', WPV_URL_EMBEDDED_FRONTEND . '/res/js/i18n/jquery.ui.datepicker-' . $lang . '.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ), WPV_VERSION, true );
			}
		} else {
			$lang = substr( $lang, 0, 2 );
			if ( file_exists( WPV_PATH_EMBEDDED . '/res/js/i18n/jquery.ui.datepicker-' . $lang . '.js' ) ) {
				if ( !wp_script_is( 'jquery-ui-datepicker-local', 'registered' ) ) {
					wp_register_script( 'jquery-ui-datepicker-local', WPV_URL_EMBEDDED_FRONTEND . '/res/js/i18n/jquery.ui.datepicker-' . $lang . '.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ), WPV_VERSION, true );
				}
			}
		}
		
		// Pagination script and style
		// Depends on:
		// 		- jquery
		// 		- jquery-ui-datepicker
		// 		- wp-mediaelement
		// 		- wp-playlist
		wp_register_script( 'views-pagination-script', WPV_URL_EMBEDDED_FRONTEND . '/res/js/wpv-pagination-embedded.js', array( 'jquery', 'jquery-ui-datepicker', 'wp-mediaelement', 'wp-playlist' ), WPV_VERSION, true );
		// Determine AJAX URL
		$ajax_url = trailingslashit( home_url() );
		$permalink_structure = get_option( 'permalink_structure' );
		if ( 
			$permalink_structure != ''
			&& strpos( $ajax_url, '?' ) === false // This happens on WPML when using the language as URL parameter
		) {
			$ajax_url .= 'wpv-ajax-pagination/';
		} else {
			$ajax_url = plugins_url( 'wpv-ajax-pagination-default.php', __FILE__ );
		}
		// Workaround for IIS servers - See: https://icanlocalize.basecamphq.com/projects/7393061-toolset/todo_items/166116657/comments
		if ( 
			isset( $_SERVER ['SERVER_SOFTWARE'] ) 
			&& ( strpos( strtolower ( $_SERVER ['SERVER_SOFTWARE'] ), 'iis' ) !== false ) 
		) {
			$ajax_url = plugins_url( 'wpv-ajax-pagination-default.php', __FILE__ );
		}
		// Only check is_ssl() because the pagination URL must have the same origin as the frontend page requesting it
		if ( is_ssl() ) {
			$ajax_url = str_replace( 'http://', 'https://', $ajax_url );
		}
		$calendar_image = WPV_URL_EMBEDDED_FRONTEND . '/res/img/calendar.gif';
		$calendar_image = apply_filters( 'wpv_filter_wpv_calendar_image', $calendar_image );
		$calendar_image = apply_filters( 'wptoolset_filter_wptoolset_calendar_image', $calendar_image );
		/**
		 * Set minimum and maximum selectable date for the datepicker rendered by Views in front-end.
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
		 * @see http://api.jqueryui.com/datepicker/#option-maxDate
		 */
		$datepicker_min_date = apply_filters( 'wpv_filter_wpv_datepicker_min_date', null );
		$datepicker_max_date = apply_filters( 'wpv_filter_wpv_datepicker_max_date', null );
		$wpv_pagination_localization = array(
				'front_ajaxurl' => admin_url( 'admin-ajax.php', null ),
				'ajax_pagination_url' => $ajax_url,
				'calendar_image' => $calendar_image,
				'calendar_text' => esc_js( __( 'Select date', 'wpv-views') ),
				'datepicker_min_date' => $datepicker_min_date,
				'datepicker_max_date' => $datepicker_max_date );
		wp_localize_script( 'views-pagination-script', 'wpv_pagination_local', $wpv_pagination_localization );
		
		// Map script
		// Depends on:
		// 		- google-maps
		if ( ! wp_script_is( 'google-maps', 'registered' ) ) {
			if ( 
				is_ssl() 
				/* This only happens on frontend...
				|| ( 
					defined( 'FORCE_SSL_ADMIN' ) 
					&& FORCE_SSL_ADMIN 
				) 
				*/
			) {
				$protocol = 'https';
			} else {
				$protocol = 'http';
			}
			wp_register_script( 'google-maps', $protocol . '://maps.googleapis.com/maps/api/js?sensor=false&ver=3.5.2', array(), null, true );
		}
		wp_register_script( 'views-map-script', WPV_URL_EMBEDDED_FRONTEND . '/res/js/jquery.wpvmap.js', array( 'google-maps', 'jquery' ), WPV_VERSION, true );
			
		/* ---------------------------- /*
		/* FRONTEND STYLES
		/* ---------------------------- */
			
		// Datepicker styles
		wp_deregister_style( 'wptoolset-field-datepicker' );
		wp_register_style( 'wptoolset-field-datepicker', WPV_URL_EMBEDDED_FRONTEND . '/common/toolset-forms/css/wpt-jquery-ui/datepicker.css', array(), WPV_VERSION );
			
		// Pagination styles - includes table styles
		// Depends on:
		// 		- wptoolset-field-datepicker - datepicker styles
		// 		- mediaelement
		// 		- wp-mediaelement
		wp_register_style( 'views-pagination-style', WPV_URL_EMBEDDED_FRONTEND . '/res/css/wpv-pagination.css', array( 'wptoolset-field-datepicker', 'mediaelement', 'wp-mediaelement' ), WPV_VERSION );
			
		// We need to add the WordPress playlist templates on the frontend
		// Just in case there is a playlist on responses of AJAXed related events
		add_action( 'wp_footer', 'wp_underscore_playlist_templates', 0 );
	}
	
	/**
	 * Add the frontend styles and scripts.
	 */
	function wpv_frontend_enqueue_scripts() {

		// Pagination
		// Note that both jquery-ui-datepicker-local and views-pagination-script have jquery-ui-datepicker as dependency
		if ( 
			wp_script_is( 'jquery-ui-datepicker-local', 'registered' ) 
			&& ! wp_script_is( 'jquery-ui-datepicker-local' )
		) {
			wp_enqueue_script( 'jquery-ui-datepicker-local' );
		}
		if ( ! wp_style_is( 'wptoolset-field-datepicker' ) ) {
			wp_enqueue_style( 'wptoolset-field-datepicker' );
		}
		if ( ! wp_script_is( 'views-pagination-script' ) ) {
			wp_enqueue_script( 'views-pagination-script' );
		}
		if ( ! wp_style_is( 'views-pagination-style' ) ) {
			wp_enqueue_style( 'views-pagination-style' );
		}

		// Maps
		global $WPV_settings;
		if ( 
			isset( $WPV_settings->wpv_map_plugin ) 
			&& $WPV_settings->wpv_map_plugin != '' 
			&& ! wp_script_is( 'views-map-script' )
		) {
			wp_enqueue_script( 'views-map-script' );
		}
	}

	function wpv_admin_enqueue_scripts( $hook ) {
		
		$page = wpv_getget( 'page' );
		
		// Assets for the shortcodes GUI
		$force_load_shortcodes_gui_assets = array( 'dd_layouts_edit' );
		$force_load_shortcodes_gui_assets = apply_filters( 'wpv_filter_wpv_force_load_shortcodes_gui_assets', $force_load_shortcodes_gui_assets );
		if ( 
			$hook == 'post.php' 
			|| $hook == 'post-new.php' 
			|| in_array( $page, $force_load_shortcodes_gui_assets )
		) {
			if ( ! wp_script_is( 'views-shortcodes-gui-script' ) ) {
				wp_enqueue_script( 'views-shortcodes-gui-script' );
			}
            if ( ! wp_script_is( 'jquery-ui-resizable' ) ) {
				wp_enqueue_script('jquery-ui-resizable');
			}
			if ( ! wp_style_is( 'views-admin-css' ) ) {
				wp_enqueue_style( 'views-admin-css' );
			}
		}
		
		if ( $page == 'dd_layouts_edit' ) {
			if ( ! wp_script_is( 'views-codemirror-conf-script' ) ) {
				wp_enqueue_script( 'views-codemirror-conf-script' );
			}
			if ( ! wp_style_is( 'views-codemirror-css' ) ) {
				wp_enqueue_style( 'views-codemirror-css' );
			}
		}

        // Assets for embedded listing pages
        if( in_array( $hook, array( 'toplevel_page_embedded-views', 'views_page_embedded-views-templates', 'views_page_embedded-views-archives' ) ) ) {
            if ( ! wp_script_is( 'views-embedded-listing-pages-script' ) ) {
				wp_enqueue_script( 'views-embedded-listing-pages-script' );
			}
			if ( ! wp_style_is( 'views-admin-css' ) ) {
				wp_enqueue_style( 'views-admin-css' );
			}
		}

		// Assets for embedded edit pages
        if ( in_array( $page, array( 'views-embedded', 'view-templates-embedded', 'view-archives-embedded', 'ModuleManager_Modules' ) ) ) {
			if ( ! wp_script_is( 'views-codemirror-conf-script' ) ) {
				wp_enqueue_script( 'views-codemirror-conf-script' );
			}
			if ( ! wp_style_is( 'views-codemirror-css' ) ) {
				wp_enqueue_style( 'views-codemirror-css' );
			}
			if ( ! wp_script_is( 'views-embedded-script' ) ) {
				wp_enqueue_script( 'views-embedded-script' );
			}
			if ( ! wp_script_is( 'views-utils-script' ) ) {
				wp_enqueue_script( 'views-utils-script' );
			}
			if ( ! wp_style_is( 'views-admin-css' ) ) {
				wp_enqueue_style( 'views-admin-css' );
			}
		}
		
		// Assets for the Widgets page
		if ( $hook == 'widgets.php' ) {
			if ( ! wp_script_is( 'views-widgets-gui-script' ) ) {
				wp_enqueue_script( 'views-widgets-gui-script' );
			}
			if ( ! wp_style_is( 'views-admin-css' ) ) {
				wp_enqueue_style( 'views-admin-css' );
			}
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
			esc_url_raw( add_query_arg(
				array( 'page' => WPV_FOLDER .'/menu/help.php' ),
				admin_url( 'admin.php' ) 
			) )
		);
		if ( $redirect ) {
			exit;
		}
	}
}


function wpv_views_plugin_action_links( $links, $plugin_file ) {
	$this_plugin = basename( WPV_PATH ) . '/wp-views.php';
	if ( $plugin_file == $this_plugin ) {
		$links[] = sprintf(
				'<a href="%s">%s</a>',
				esc_url( 
					add_query_arg(
						array( 'page' => basename( WPV_PATH ) . '/menu/help.php' ),
						admin_url( 'admin.php' ) 
					) 
				),
				__( 'Getting started', 'wpv-views' ) 
			);
	}
	return $links;
}

function wpv_views_plugin_plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
	$this_plugin = basename( WPV_PATH ) . '/wp-views.php';
	if ( $plugin_file == $this_plugin ) {
		$plugin_meta[] = sprintf(
				'<a href="%s" target="_blank">%s</a>',
				'https://wp-types.com/version/views-1-10/?utm_source=viewsplugin&utm_campaign=views&utm_medium=release-notes-admin-notice&utm_term=Views 1.10 release notes',
				__( 'Views 1.10 release notes', 'wpv-views' ) 
			);
	}
	return $plugin_meta;
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
			'title'	=> __( 'Spinner #1', 'wpv-views' ),
			'url'	=> WPV_URL_EMBEDDED . '/res/img/ajax-loader.gif'
		),
		array(
			'title'	=> __( 'Spinner #2', 'wpv-views' ),
			'url'	=> WPV_URL_EMBEDDED . '/res/img/ajax-loader2.gif'
		),
		array(
			'title'	=> __( 'Spinner #3', 'wpv-views' ),
			'url'	=> WPV_URL_EMBEDDED . '/res/img/ajax-loader3.gif'
		),
		array(
			'title'	=> __( 'Spinner #4', 'wpv-views' ),
			'url'	=> WPV_URL_EMBEDDED . '/res/img/ajax-loader4.gif'
		),
		array(
			'title'	=> __( 'Spinner #5', 'wpv-views' ),
			'url'	=> WPV_URL_EMBEDDED . '/res/img/ajax-loader-overlay.gif'
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
 * Usage example:  <?php print_r( get_view_allowed_attributes( 80 ) ); ?>
 *
 * @todo review the 'value' entry
 */
function get_view_allowed_attributes( $view_id ) {
	$attributes = array();
	if ( empty( $view_id ) ){
		return;
	}
	global $WP_Views;
	$view_settings = $WP_Views->get_view_settings( $view_id );
	if ( 
		is_array( $view_settings ) 
		&& isset( $view_settings['view-query-mode'] )
		&& $view_settings['view-query-mode'] == 'normal'
		&& isset( $view_settings['query_type'][0] )
	) {
		$query_type = $view_settings['query_type'][0];
		$attributes = apply_filters( 'wpv_filter_register_shortcode_attributes_for_' . $query_type, $attributes, $view_settings );
		// Post View
		if ( $view_settings['query_type'][0] == 'posts' ) {
			foreach ( $view_settings as $key => $value ) {
				// Taxonomy
				if ( 
					preg_match( "/tax_(.*)_relationship/", $key, $res ) 
					&& $value == 'FROM ATTRIBUTE' 
				) {
					$taxonomy = $res[1];
					if ( taxonomy_exists( $taxonomy ) ) {
						$attributes[] = array(
							'query_type'	=> $view_settings['query_type'][0],
							'filter_type'	=> 'post_taxonomy_' . $taxonomy,
							'filter_label'	=> sprintf( __( 'Post taxonomy - %s', 'wpv-views' ), $taxonomy ),
							'value'			=> $view_settings[ 'taxonomy-' . $taxonomy . '-attribute-url-format' ][0],
							'attribute'		=> $view_settings[ 'taxonomy-' . $taxonomy . '-attribute-url' ],
							'expected'		=> 'string',
							'placeholder'	=> ( $view_settings[ 'taxonomy-' . $taxonomy . '-attribute-url-format' ][0] == 'slug' ) ? 'cat1' : 'Cat 1',
							'description'	=> ( $view_settings[ 'taxonomy-' . $taxonomy . '-attribute-url-format' ][0] == 'slug' ) ? __( 'Please type a comma separated list of term slugs', 'wpv-views' ) : __( 'Please type a comma separated list of term names', 'wpv-views' )
						);
					}
				}
				// Custom fields
				if ( 
					preg_match( "/custom-field-(.*)_value/", $key, $res )
					&& preg_match( "/VIEW_PARAM\(([^\)]+)\)/", $value, $shortcode ) 
				) {
					$expected_input_data_type = in_array( $view_settings[ 'custom-field-' . $res[1] . '_type' ], array( 'NUMERIC', 'DATE', 'DATETIME', 'TIME' ) )
							? 'integer'
							: ( ( $view_settings[ 'custom-field-' . $res[1] . '_type' ] == 'DECIMAL' ) ? 'decimal' : 'string' );
					$attributes[] = array(
						'query_type'	=> $view_settings['query_type'][0],
						'filter_type'	=> 'post_custom_field_'. $res[1],
						'filter_label'	=> sprintf( __( 'Custom field - %s', 'wpv-views' ), $res[1] ),
						'value'			=> 'custom_field_value',
						'attribute'		=> $shortcode[1],
						'expected'		=> $expected_input_data_type,
						'placeholder'	=> 'value',
						'description'	=> __( 'Please type a custom field value', 'wpv-views' )
					);
				}
			}
		}

		// User View
		if ( $view_settings['query_type'][0] == 'users' ) {
			foreach ( $view_settings as $key => $value ) {
				// Usermeta fields
				if ( 
					preg_match( "/usermeta-field-(.*)_value/", $key, $res )
					&& preg_match( "/VIEW_PARAM\(([^\)]+)\)/", $value, $shortcode ) 
				) {
					$expected_input_data_type = in_array( $view_settings[ 'usermeta-field-' . $res[1] . '_type' ], array('NUMERIC','DATE','DATETIME','TIME') )
							? 'integer'
							: ( ( $view_settings[ 'usermeta-field-' . $res[1] . '_type' ] == 'DECIMAL' ) ? 'decimal' : 'string' );
					$attributes[] = array(
						'query_type'	=> $view_settings['query_type'][0],
						'filter_type'	=> 'user_usermeta_field_'. $res[1],
						'filter_label'	=> sprintf( __( 'Usermeta field - %s', 'wpv-views' ), $res[1] ),
						'value'			=> 'usermeta_field_value',
						'attribute'		=> $shortcode[1],
						'expected'		=> $expected_input_data_type,
						'placeholder'	=> 'value',
						'description'	=> __( 'Please type an username field value', 'wpv-views' )
					);
				}
			}
		}
	}

	return $attributes;
}
