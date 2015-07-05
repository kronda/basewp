<?php

require_once( WPV_PATH_EMBEDDED . '/common/visual-editor/editor-addon.class.php');

class WPV_template{

    function __construct(){

        add_action('init', array($this, 'init'));
        add_filter('icl_cf_translate_state', array($this, 'custom_field_translate_state'), 10, 2);

		$this->wpautop_removed = false;

	$this->view_template_used_ids = array();
    }

    function __destruct(){

    }

    function init(){

        wpv_register_type_view_template();
        add_action( 'wp_ajax_set_view_template', array( $this, 'set_view_template_callback' ) );
        add_filter( 'the_content', array( $this, 'the_content' ), 1, 1 );
        add_filter( 'the_content', array( $this, 'restore_wpautop' ), 999, 1 );
		
		/**
		* Recreate the the_content filters, when doing a wpv-post-body shortcode with suppress_filters="true"
		*
		* @since 1.8.0
		*/
		
		if ( function_exists( 'WPV_wpcf_record_post_relationship_belongs' ) ) {
			add_filter( 'wpv_filter_wpv_the_content_suppressed', 'WPV_wpcf_record_post_relationship_belongs', 0, 1 );
		}
		add_filter( 'wpv_filter_wpv_the_content_suppressed', array( $this, 'the_content' ), 1, 1 );
		if ( isset( $GLOBALS['wp_embed'] ) ) {
			add_filter( 'wpv_filter_wpv_the_content_suppressed', array( $GLOBALS['wp_embed'], 'run_shortcode' ), 8 );
			add_filter( 'wpv_filter_wpv_the_content_suppressed', array( $GLOBALS['wp_embed'], 'autoembed' ), 8 );
		}
		add_filter( 'wpv_filter_wpv_the_content_suppressed', 'wpv_resolve_internal_shortcodes', 9 );
		add_filter( 'wpv_filter_wpv_the_content_suppressed', 'wpv_resolve_wpv_if_shortcodes', 9 );
		add_filter( 'wpv_filter_wpv_the_content_suppressed', 'convert_smilies', 10 );
		add_filter( 'wpv_filter_wpv_the_content_suppressed', 'prepend_attachment', 10 );
		add_filter( 'wpv_filter_wpv_the_content_suppressed', 'capital_P_dangit', 11 );
		add_filter( 'wpv_filter_wpv_the_content_suppressed', 'do_shortcode', 11 );
		add_filter( 'wpv_filter_wpv_the_content_suppressed', array( $this, 'restore_wpautop' ), 999, 1 );

        add_filter('the_excerpt', array($this, 'the_excerpt_for_archives'), 1, 1);

        if(is_admin()){
            global $pagenow;

            // Post/page language box
            if($pagenow == 'post.php' || $pagenow == 'post-new.php'){
                add_action('admin_head', array($this,'post_edit_template_options'));
                add_action('admin_head', array($this,'post_edit_tinymce'));
	            add_action('admin_footer', array($this, 'hide_view_template_author'));

				add_action('admin_notices', array($this, 'show_admin_messages'));

				// Post/page save actions
				add_action('save_post', array($this,'save_post_actions'), 10, 2);

				add_filter('user_can_richedit', array($this, 'disable_rich_edit_for_views'));

            } elseif ($pagenow == 'admin-ajax.php') {
				// For when Types saves a child post
				add_action('save_post', array($this,'save_post_actions'), 10, 2);
			}
			add_action( 'admin_enqueue_scripts', array( $this, 'wpv_ct_admin_enqueue_scripts' ) );

        } else {
			add_filter('edit_post_link', array($this, 'edit_post_link'), 10, 2);
                        add_filter('body_class', array($this, 'body_class'), 10, 2);
                        add_action('wp_footer', array($this, 'wpv_meta_html_extra_css'), 5); // Set priority lower than 20, so we load the CSS before the footer scripts and avoid the bottleneck
                        add_action('wp_footer', array($this, 'wpv_meta_html_extra_js'), 25); // Set priority higher than 20, when all the footer scripts are loaded
        }

		add_action('add_attachment', array($this, 'set_template_for_attachments'), 10, 1);
		add_action('edit_attachment', array($this, 'set_template_for_attachments'), 10, 1);

		add_action('save_post', array($this, 'set_default_template'), 10, 2);

    }

    function body_class($body_class){
	if (!is_singular()) {return $body_class;}
        $template_selected = get_post_meta(get_the_ID(), '_views_template', true);
        if (isset($_GET['view-template']) && $_GET['view-template'] != '') {
		$template_selected = $this->get_template_id($_GET['view-template']);
	}
        if($template_selected == 0){ return $body_class;}
        $template_class_title = preg_replace("/[^A-Za-z_ ]/",'', get_the_title($template_selected));
        $template_class = 'views-template-'.strtolower(str_replace(' ', '-', $template_class_title));
        $body_class[]=$template_class;
        return $body_class;
    }

    function custom_field_translate_state($state, $field_name) {
        switch($field_name) {
            case '_views_template':
			case '_views_template_new_type':
                return 'ignore';

            default:
                return $state;
        }
    }

	/**
	 * Add metaboxes to the post edit pages as required
	 *
	 */

    function post_edit_template_options() {
		global $post;
		$post_object = get_post_type_object( $post->post_type );

		if ( $post_object->publicly_queryable || $post_object->public ) {
			// Add meta box so that a Content Template can be set for a post
			add_meta_box( 'views_template', __( 'Content Template', 'wpv-views' ), array( $this, 'content_template_select_meta_box' ), $post->post_type, 'side', 'high' );
		} else if ( $post_object->name == 'view-template' ) {
			// add a meta box for the views template settings
			$this->add_view_template_settings();
		}
    }

	function add_view_template_settings() {
		// Don't add to embedded version.
	}

	/**
	* wpv_ct_admin_enqueue_scripts
	*
	* Properly register and enqueue scripts for Content Template edit pages
	*
	* @since 1.7
	*/
	
	function wpv_ct_admin_enqueue_scripts( $hook ) {
		if ( ( $hook == 'post.php' || $hook == 'post-new.php' ) ) {
			global $post;
			if ( 'view-template' === $post->post_type ) {
				//wp_register_style( 'views-admin-alt-css' , WPV_URL . '/res/css/wpv-views.css', array(), WPV_VERSION );
				//wp_enqueue_style( 'views-admin-alt-css' );
			}
		}
	}

	/**
	* content_template_select_meta_box
	*
	* Add a meta box to public and publicly_queryable post types to set the Content Template to be used for that single item being edited
	*
	* @param $post (object) the post being edited
	*
	* @return echo the meta box
	*
	* @since unknown
	* @note 1.7 added a link to edit the Content Template
	* @note 1.7 removed loop Templates from the dropdown
	*/

    function content_template_select_meta_box( $post ) {
        global $wpdb, $WP_Views, $sitepress;
		
		$values_to_prepare = array();
		
		$wpml_join = $wpml_where = "";
		if (
			isset( $sitepress ) 
			&& function_exists( 'icl_object_id' )
		) {
			$content_templates_translatable = $sitepress->is_translated_post_type( 'view-template' );
			if ( $content_templates_translatable ) {
				$wpml_current_language = $sitepress->get_current_language();
				$wpml_join = " JOIN {$wpdb->prefix}icl_translations t ";
				$wpml_where = " AND p.ID = t.element_id AND t.language_code = %s ";
				$values_to_prepare[] = $wpml_current_language;
			}
		}
		
		
		$exclude_loop_templates = '';
		$exclude_loop_templates_ids = $wpdb->get_col( 
			"SELECT meta_value FROM {$wpdb->postmeta} 
			WHERE meta_key='_view_loop_template'" 
		);
		if ( count( $exclude_loop_templates_ids ) > 0 ) {
			$exclude_loop_templates_ids_sanitized = array_map( 'esc_attr', $exclude_loop_templates_ids );
			$exclude_loop_templates_ids_sanitized = array_map( 'trim', $exclude_loop_templates_ids_sanitized );
			// is_numeric + intval does sanitization
			$exclude_loop_templates_ids_sanitized = array_filter( $exclude_loop_templates_ids_sanitized, 'is_numeric' );
			$exclude_loop_templates_ids_sanitized = array_map( 'intval', $exclude_loop_templates_ids_sanitized );
			if ( count( $exclude_loop_templates_ids_sanitized ) > 0 ) {
				$exclude_loop_templates = " AND p.ID NOT IN ('" . implode( "','" , $exclude_loop_templates_ids_sanitized ) . "') ";
			}
		}
		$values_to_prepare[] = 'view-template';
        $view_tempates_available = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT p.ID, p.post_name, p.post_title 
				FROM {$wpdb->posts} p {$wpml_join} 
				WHERE p.post_status = 'publish' 
				{$wpml_where} 
				AND p.post_type = %s 			
				{$exclude_loop_templates}
				ORDER BY p.post_title",
				$values_to_prepare
			)
		);
        if ( 
			isset( $_GET['post'] ) 
			&& intval( $_GET['post'] ) > 0
		) {
            $template_selected = get_post_meta( (int) $_GET['post'], '_views_template', true );
			if ( empty( $template_selected ) ) {
				$template_selected = 0;
			}
        } else {
            $template_selected = 0;
            global $pagenow, $post_type;
            if ( $pagenow == 'post-new.php' ) {
				if ( 
					isset( $_GET['trid'] ) 
					&& isset( $_GET['source_lang'] )
				) {
					$sp_trid = sanitize_text_field( $_GET['trid'] );
					$sp_source_lang = sanitize_text_field( $_GET['source_lang'] );
					// we are creating a translated post
					if ( isset( $sitepress ) && function_exists( 'icl_object_id' ) ) {
						$translations = $sitepress->get_element_translations( $sp_trid, 'post_' . $post->post_type );
						if ( isset( $translations[$sp_source_lang] ) ) {
							$template_selected = get_post_meta( $translations[$sp_source_lang]->element_id, '_views_template', true );
							if ( empty( $template_selected ) ) {
								$template_selected = 0;
							}
						}
					}
				}
				if ( $template_selected == 0 ) {
					// see if we have specified what template to use for this post type
					global $WPV_settings;
					if ( isset( $WPV_settings['views_template_for_' . $post_type] ) ) {
						$template_selected = $WPV_settings['views_template_for_' . $post_type];						
					}
				}
            }
        }
		?>
		<select name="views_template[<?php echo esc_attr( $post->ID ); ?>]" id="views_template" class="widefat js-wpv-edit-post-select-ct">
		<?php
        // Add a "None" type to the list.
        $none = new stdClass();
        $none->ID = '0';
        $none->post_title = __( 'None', 'wpv-views' );
        array_unshift( $view_tempates_available, $none );
		if ( $template_selected != 0 ) {
			// Adjust for WPML support
			$template_selected = apply_filters( 'translate_object_id', $template_selected, 'view-template', true, null );
		}
        foreach( $view_tempates_available as $template ) {
			if ( $template->post_title != '' ) {
				?>
				<option value="<?php echo esc_attr( $template->ID ); ?>" <?php selected( $template_selected, $template->ID ); ?>><?php echo $template->post_title; ?></option>
				<?php
			} else {
				?>
				<option value="<?php echo esc_attr( $template->ID ); ?>" <?php selected( $template_selected, $template->ID ); ?>><?php echo $template->post_name; ?></option>
				<?php
			}
        }
		?>
		</select>
		<?php
        $edit_link = '';
        $edit_link_visible = ' hidden';
        if ( ! empty( $template_selected ) && $template_selected !== 0 ) {
            $edit_link = 'post.php?post=' . $template_selected . '&action=edit';
            $edit_link_visible = '';
        }
        ?>
		<div class="js-wpv-edit-post-edit-ct-link-container<?php echo esc_attr( $edit_link_visible ); ?>" style="margin-top:10px;padding-top:10px;border-top:solid 1px #ccc;">
			<a href="<?php echo esc_url( $edit_link ); ?>" class="button button-secondary js-wpv-edit-post-edit-ct-link" target="_blank"><?php _e( 'Edit this Content Template', 'wpv-views' ); ?> <i class="icon-chevron-right"></i></a>
		</div>
		<script type="text/javascript">
			jQuery( function( $ ) {
				// Warning! We need to take care of Layouts compatibility: if there is a select for Layouts AND has a value, we need to hide the thiz_wpv_ct_link_container
				var thiz_wpv_layout_select = $( document.getElementById( 'js-layout-template-name' ) ),
				thiz_wpv_ct_select = $( '.js-wpv-edit-post-select-ct' ),
				thiz_wpv_ct_link = $( '.js-wpv-edit-post-edit-ct-link' ),
				thiz_wpv_ct_link_container = $( '.js-wpv-edit-post-edit-ct-link-container' ),
				thiz_wpv_ct_select_manager = function() {
					if ( thiz_wpv_ct_select.val() != 0 ) {
						thiz_wpv_ct_link.attr( 'href','post.php?post=' + thiz_wpv_ct_select.val() + '&action=edit' );
						thiz_wpv_ct_link_container.fadeIn( 'fast' );
					} else {
						thiz_wpv_ct_link_container.hide();
					}
				};
				if ( thiz_wpv_layout_select.length > 0 ) {
					if ( thiz_wpv_layout_select.val() != '0' ) {
						thiz_wpv_ct_link_container.hide();
					} else {
						thiz_wpv_ct_select_manager();
					}
					thiz_wpv_layout_select.on( 'change', function() {
						if ( thiz_wpv_layout_select.val() != '0' ) {
							thiz_wpv_ct_link_container.hide();
						} else {
							thiz_wpv_ct_select_manager();
						}
					});
				}
				thiz_wpv_ct_select.on( 'change', thiz_wpv_ct_select_manager );
			});
		</script>
        <?php
    }

	/**
	 * Save the meta box data when a post is saved
	 *
	 */

    function save_post_actions( $pidd, $post ) {
		global $wpdb, $WP_Views;
        if ( isset( $_POST['views_template'] ) ) {
			// make sure we only update this for the current post.
	        if (
				isset( $_POST['post_ID'] ) 
				&& $_POST['post_ID'] == $pidd 
				&& isset( $_POST['views_template'][$pidd] )
			) {
				$template_selected = $_POST['views_template'][$pidd];
				update_post_meta( $pidd, '_views_template', $template_selected );
			}
		} elseif ( isset( $_POST['wpcf_post_relationship'] ) ) {
			// handle Types post relationships
			if ( isset( $_POST['wpcf_post_relationship'][$pidd]['post_type'] ) ) {
				// Saving an existing child post
				$post_type = $_POST['wpcf_post_relationship'][$pidd]['post_type'];
			} else {
				// Saving a new child post
				$post_type = $wpdb->get_var( 
					$wpdb->prepare(
						"SELECT post_type FROM {$wpdb->posts} 
						WHERE ID = %d 
						LIMIT 1",
						$pidd
					)
				);
			}
			// set the Content Template if one hasn't been set.
            $template_selected = get_post_meta( $pidd, '_views_template', true );
			if ( $template_selected == '' ) {
				global $WPV_settings;
				if ( isset( $WPV_settings['views_template_for_' . $post_type] ) ) {
					$template_selected = $WPV_settings['views_template_for_' . $post_type];
					update_post_meta( $pidd, '_views_template', $template_selected );
				}
			}
		}
    }

	/**
	 * get the template id from the name and include caching.
	 *
	 */

	function get_template_id( $template_name ) {
		global $wpdb;
		static $templates = array();
		if ( ! isset( $templates[$template_name] ) ) {
			$templates[$template_name] = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT ID FROM {$wpdb->posts} 
					WHERE post_type = 'view-template' 
					AND post_name = %s 
					LIMIT 1",
					$template_name
				)
			);
		}
		if ( ! isset( $templates[$template_name] ) ) {
			$templates[$template_name] = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT ID FROM {$wpdb->posts} 
					WHERE post_type = 'view-template' 
					AND post_title = %s 
					LIMIT 1",
					$template_name
				)
			);
		}
		return $templates[$template_name];
	}

	/**
	 *	get the template content and include caching
	 *
	 */

	function get_template_content( $template_id ) {
		static $view_templates = array();
		if ( ! isset( $view_templates[$template_id] ) ) {
            $status = get_post_status( $template_id );
            
            // If the templated is not 'publish'ed
            if( $status != 'publish' ) {
                return null;
            }
            
            //FIXME: Check user has permission to see this content template
            
			$template_full = get_post( $template_id );
			// If there is no post with that ID
			if ( 
				is_null( $template_full ) 
				|| ! is_object( $template_full ) 
			) {
				return null;
			}
			if (
				isset( $template_full->post_type ) 
				&& $template_full->post_type == 'view-template' 
			) {
				$view_templates[$template_id] = $template_full->post_content;
			} else {
				// If there is a post with that ID but it is not a Content Template
				return null;
			}

		}
		return $view_templates[$template_id];
	}

	/**
	 * apply the filter to the content/body of a post.
	 * Checks to see if a view-template is set for the post
	 * and renders using the view_template if on exists
	 */

    function the_content($content) {
        global $id, $post, $wpdb, $WP_Views, $WPV_settings, $wp_query, $wplogger, $WPVDebug;

		$post = get_post( $post );
		if ( is_null( $post ) ) {
			return $content;
		}
		
		if ( post_password_required($post) ) {
			return $content;
		}

		// core functions that we except calls from.
		static $the_content_core = array('the_content', 'wpv_shortcode_wpv_post_body');
		// known theme functions that we except calls from.
		static $the_content_themes = array('wptouch_the_content');

        $db = debug_backtrace();

		if (!isset($db[3]['function'])) {
			return $content;
		}

        // FIXME: This should be rearranged to improve legibility
		$function_ok = false;
		if ($db[1]['function'] == 'the_excerpt_for_archives') {
			$function_ok = true;
		}

		if (!$function_ok) {

			if (in_array($db[3]['function'], $the_content_core)) {
				$function_ok = true;
			}
		}

		if (!$function_ok) {

			if (in_array($db[3]['function'], $the_content_themes)) {
				$function_ok = true;
			}
		}

		if ( ! $function_ok ) {
			if ( isset( $WPV_settings->wpv_theme_function ) ) {
                if ( in_array( $db[3]['function'], explode( ',', str_replace( ', ', ',', $WPV_settings->wpv_theme_function ) ) ) ) {
                    $function_ok = true;
                }
            }

            if ( ! $function_ok ) {
                // We don't except calls from the calling function.
                if ( current_user_can( 'administrator' ) ) {
                    if ( isset( $WPV_settings->wpv_theme_function_debug ) && $WPV_settings->wpv_theme_function_debug ) {
                        $content = sprintf( __( '<strong>Content template debug: </strong>Calling function is <strong>%s</strong>', 'wpv-views' ), $db[3]['function'] ) . '<br />' . $content;
                    }
                }
                return $content;
            }
        }


		// If it's in progress then just return the un-filtered content
		// to avoid recursion
		static $in_progress = array();
		if (in_array($id, array_keys($in_progress))) {
			return $content;
		}

		// Here we will store what kind of place this template is being used on: single-{post-type}, singular, archive-{post-type}, archive-{taxonomy}, archive, listing-{post-type}
		$kind = '';
		if ( is_singular() ) {
			$current_page = $WP_Views->get_top_current_page();
			if ( is_null( $current_page ) || !isset( $current_page->ID ) ) {
				$current_id = 0;
			} else {
				$current_id = $current_page->ID;
			}
			if ( isset( $post->post_type ) ) {
				if ( $current_id == $post->ID ) {
					$kind = 'single-' . $post->post_type;
				} else {
					$kind = 'listing-' . $post->post_type;
				}
			} else {
				$kind = 'singular';
			}
		}

		$in_progress[$id] = true;

        // FIXME: I understand that static variables are being used here to provide a temporal cache.
        // If so, please document.
		static $archive_type_has_been_checked = false;
		static $taxonomy_loop = null;
		static $archive_loop = null;
		if ( ! $archive_type_has_been_checked ) {
            $archive_type_has_been_checked = true;
			if ( is_archive() ) {

				/* Taxonomy archives. */

				if ( is_tax() || is_category() || is_tag() ) {

					$term = $wp_query->get_queried_object();
					$taxonomy_loop = 'views_template_loop_' . $term->taxonomy;
					$kind = 'archive-' . $term->taxonomy;

				} else if (is_post_type_archive($post->post_type)) {
					$archive_loop = 'views_template_archive_for_' . $post->post_type;
					$kind = 'archive-' . $post->post_type;
				} else if ('post' == $post->post_type) {
					$archive_loop = 'views_template_archive_for_post';
					$kind = 'archive-post';
				} else { // if on an archive page but not a tax one and not on the archive for the displayed $post, then it's a generic archive
					$kind = 'archive';
				}
			}
		}

		$template_selected = 0;
		$template_apply_filter = true;
		if (isset($_GET['view-template']) && $_GET['view-template'] != '') {
			if (!isset($post->view_template_override_get)) {
				$template_selected = $this->get_template_id($_GET['view-template']);
				$post->view_template_override_get = true;
			}
		} else if (isset($_GET['cred-edit-form']) && $_GET['cred-edit-form'] != '') { // special case when displaying CRED forms
			if ( isset($post->view_template_override) && strtolower($post->view_template_override) != 'none' ) { // if displaying CRED form and this template is inside the form in a wpv-post-body shortcode, apply it
				$template_selected = $this->get_template_id($post->view_template_override);
			} else { // if this template is being used for the post type being edited by a CRED form, do not apply it
				$template_selected = 0;
				$template_apply_filter = false;
			}
		} else {
			if (isset($post->view_template_override)) {
				if (strtolower($post->view_template_override) == 'none') {
					$template_selected = 0;
				} else {
					$template_selected = $this->get_template_id($post->view_template_override);
				}
			} else if ($taxonomy_loop) {
				if (isset($WPV_settings[$taxonomy_loop]) && $WPV_settings[$taxonomy_loop] > 0) {
					if (!isset($post->view_template_override_loop_setting)) {
						$template_selected = $WPV_settings[$taxonomy_loop];
						$post->view_template_override_loop_setting = true;
					}
				}
			} else if ($archive_loop) {
				if (isset($WPV_settings[$archive_loop]) && $WPV_settings[$archive_loop] > 0) {
					if (!isset($post->view_template_override_loop_setting)) {
						$template_selected = $WPV_settings[$archive_loop];
						$post->view_template_override_loop_setting = true;
					}
				}
			} else {
				$template_selected = get_post_meta($id, '_views_template', true);
			}
		}

		if ( $template_apply_filter ) {

			/**
			* Filter wpv_filter_force_template
			*
			* @param $template_selected the template ID we want to overwrite
			* @param $id the post the template is being applied to
			* @param $kind the kind of place where this template is being used: single-{post-type}, singular, archive-{post-type}, archive-{taxonomy}, archive, listing-{post-type}
			*
			* @return $template_selected
			*
			* Since 1.4
			*
			*/

			$old_template_selected = $template_selected;
			$template_selected = apply_filters( 'wpv_filter_force_template', $template_selected, $id, $kind );
		//	$WPVDebug->add_log( 'info' , "wpv_filter_force_template\n" . __('ID: ', 'wpv-views') . $id . "\n" . __('Content Template: from ', 'wpv-views') . $old_template_selected . __(' to ', 'wpv-views') . $template_selected, 'filters', 'Filter: wpv_filter_force_template' );
		//	The debug is not being added, I will need a better way to show this
		}

		$WPVDebug->update_template_id( $template_selected );

        if ( $template_selected ) {
			// Adjust for WPML support
			$template_selected = apply_filters( 'translate_object_id', $template_selected, 'view-template', true, null );
			$this->view_template_used_ids[] = $template_selected;
			$wplogger->log('Using Content Template: ' . $template_selected . ' on post: ' . $post->ID);

            $content_aux = $this->get_template_content( $template_selected );
			// If this function returns null, $template_selected does not exist or is not a Content Template
			if ( is_null( $content_aux ) ) {
				unset( $in_progress[$id] );
				return $content;
			} else {
				$content = $content_aux;
			}

			$output_mode = get_post_meta($template_selected, '_wpv_view_template_mode', true);
			if ($output_mode == 'raw_mode') {

				$this->remove_wpautop();
			}
        }

		$content = wpml_content_fix_links_to_translated_content($content);
		$WPVDebug->add_log_item( 'shortcodes', $content );
		
		/**
		* Filter wpv_filter_content_template_output
		*
		* @param $content the content to be displayed, whether it's the real post content or the one coming from a Content Template
		* @param $template_selected the template ID being applied, can be 0 when there is none
		* @param $id the post the template is being applied to
		* @param $kind the kind of place where this template is being used: single-{post-type}, singular, archive-{post-type}, archive-{taxonomy}, archive, listing-{post-type}
		*
		* @return $content
		*
		* Since 1.5.1
		*
		*/

		$content = apply_filters( 'wpv_filter_content_template_output', $content, $template_selected, $id, $kind );
		
		unset($in_progress[$id]);

		return $content;

    }

	function is_wpautop_removed() {
		return $this->wpautop_removed;
	}

	function remove_wpautop() {
		remove_filter('the_content', 'wpautop');
		remove_filter('the_content', 'shortcode_unautop');
		remove_filter( 'wpv_filter_wpv_the_content_suppressed', 'wpautop' );
		remove_filter( 'wpv_filter_wpv_the_content_suppressed', 'shortcode_unautop' );
		remove_filter('the_excerpt', 'wpautop');
		remove_filter('the_excerpt', 'shortcode_unautop');

		$this->wpautop_removed = true;
	}

	function restore_wpautop( $content ) {
		if ( $this->wpautop_removed ) {
			add_filter('the_content', 'wpautop');
			add_filter('the_content', 'shortcode_unautop');
			add_filter( 'wpv_filter_wpv_the_content_suppressed', 'wpautop' );
			add_filter( 'wpv_filter_wpv_the_content_suppressed', 'shortcode_unautop' );
			add_filter('the_excerpt', 'wpautop');
			add_filter('the_excerpt', 'shortcode_unautop');
			$this->wpautop_removed = false;
		}

		return $content;
	}

	/**
	 * Handle the_excerpt filter when it's used in an archive loop
	 *
	 */

	function the_excerpt_for_archives($content) {
		global $WPV_settings, $post, $wp_query;

		static $archive_type_has_been_checked = false;
		static $taxonomy_loop = null;
		static $archive_loop = null;
		if ( !$archive_type_has_been_checked ) {
            $archive_type_has_been_checked = true;

            if ( is_archive() ) {

				/* Taxonomy archives. */

				if ( is_tax() || is_category() || is_tag() ) {

					$term = $wp_query->get_queried_object();
					$taxonomy_loop = 'views_template_loop_' . $term->taxonomy;

				} else if (is_post_type_archive($post->post_type)) {
					$archive_loop = 'views_template_archive_for_' . $post->post_type;
				}
			}
		}

		if ( $taxonomy_loop && isset( $WPV_settings[$taxonomy_loop] ) ) {
            // this is a taxonomy loop. Get the Content Template using the_content function
            $content = do_shortcode( $this->the_content( $content ) );
        } else if ( $archive_loop && isset( $WPV_settings[$archive_loop] ) ) {
            // this is an archive loop. Get the Content Template using the_content function
            $content = do_shortcode( $this->the_content( $content ) );
        }

        return $content;
	}

	/**
	 * If the post has a Content Template
	 * add an Content Template edit link to post.
	 */

	function edit_post_link($link, $post_id) {

		// do nothing for theme version.

		return $link;
	}

	/**
	 * Add a view toolbar button to the editor of the content template
	 *
	 */

    function post_edit_tinymce() {
        global $post;

        if ($post->post_type == 'view-template') {

            $this->editor_addon = new Editor_addon('wpv-views',
                                                   __('Insert Views Shortcode', 'wpv-views'),
                                                   WPV_URL . '/res/js/views_editor_plugin.js',
                                                    '', true, 'icon-views-logo ont-icon-18');

            add_short_codes_to_js(array('post', 'view','body-view-templates'), $this->editor_addon);
        }
    }

	/**
	 * Ajax function to set the current content template to posts of a type
	 * set in $_POST['type']
	 *
	 */

    function set_view_template_callback() {
		// do nothing in the theme version.

        die(); // this is required to return a proper result
    }
	
	// @todo deprecate this, and handle it on place, with proper preparation

	function _get_wpml_sql($type, $lang = null) {
		global $wpdb, $sitepress;

		$join = '';
		$cond = '';

		if (isset($sitepress)) {

			if ($sitepress->is_translated_post_type($type)) {
				if (!$lang) {
					$lang = $sitepress->get_current_language();
				}
				if ($lang) {
					$join = " JOIN {$wpdb->prefix}icl_translations t ON {$wpdb->posts}.ID = t.element_id ".
								" AND t.element_type = 'post_{$type}' JOIN {$wpdb->prefix}icl_languages l ON t.language_code=l.code AND l.active=1";
					$cond = " AND t.language_code='". esc_sql($lang) ."'";
				}
			}
		}

		return array($join, $cond);
	}

	/**
	 * Get content templates in a select box
	 *
	 * @todo the $row attribute is deprecated... in fact all the method is deprecated
	 *
	 */

	function get_view_template_select_box( $row, $template_selected ) {
		global $wpdb;
        $view_tempates_available = $wpdb->get_results( 
			"SELECT ID, post_title, post_name FROM {$wpdb->posts} 
			WHERE post_type='view-template' 
			AND post_status in ('publish')" 
		);
        $view_template_select_box = '';
		if ( $row === '' ) {
			$view_template_select_box .= '<select class="views_template_select" name="views_template" id="views_template">';
		} else {
			$view_template_select_box .= '<select class="views_template_select" name="views_template_' . $row . '" id="views_template_' . $row . '">';
		}
        // Add a "None" type to the list.
        $none = new stdClass();
        $none->ID = '0';
        $none->post_title = __('None', 'wpv-views');
        array_unshift( $view_tempates_available, $none );
        foreach( $view_tempates_available as $template ) {
			if ( $template->post_title ) {
				$view_template_select_box .= '<option value="' . $template->ID . '" ' . selected( $template_selected, $template->ID, false ) . '>' . $template->post_title . '</option>';
			} else {
				$view_template_select_box .= '<option value="' . $template->ID . '" ' . selected( $template_selected, $template->ID, false ) . '>' . $template->post_name . '</option>';
			}
        }
        $view_template_select_box .= '</select>';
        return $view_template_select_box;
	}

	function get_view_template_titles() {
        global $wpdb;

        static $view_templates_available = null;

		if ($view_templates_available === null) {

			$view_templates_available = array();
			$view_templates = $wpdb->get_results( 
				"SELECT ID, post_title FROM {$wpdb->posts} 
				WHERE post_type = 'view-template'" 
			);
			foreach ( $view_templates as $view_template ) {
				$view_templates_available[$view_template->ID] = $view_template->post_title;
			}
		}
		return $view_templates_available;
	}

	function hide_view_template_author() {

	}

	function show_admin_messages() {

	}

	function disable_rich_edit_for_views($state) {
		return $state;
	}

	function set_default_template($pidd, $post) {
		global $WPV_settings;

		if (!isset($_POST['views_template'])) {

			// set the content template if one hasn't been set.
			$template_selected = get_post_meta($pidd, '_views_template', true);
			if ($template_selected == '') {
				if ( isset( $WPV_settings['views_template_for_' . $post->post_type] ) ) {
                    $template_selected = $WPV_settings['views_template_for_' . $post->post_type];
					update_post_meta($pidd, '_views_template', $template_selected);
				}
			}

		}

	}

	function set_template_for_attachments( $pidd ) {
        global $WPV_settings;

        if ( !isset( $_POST['views_template'] ) ) {
            // set the content template if one hasn't been set.
            $template_selected = get_post_meta( $pidd, '_views_template', true );
            if ( $template_selected == '' ) {
                if ( isset( $WPV_settings['views_template_for_attachment'] ) ) {
                    $template_selected = $WPV_settings['views_template_for_attachment'];
                    update_post_meta( $pidd, '_views_template', $template_selected );
                }
            }
        } elseif ( isset( $_POST['views_template'][$pidd] ) ) {
            update_post_meta( $pidd, '_views_template', $_POST['views_template'][$pidd] );
        }
    }

    /**
	* Add extra CSS and javascript to wp_footer
	* This renders CSS and JS added by the user on the Content Template settings
	*/
	
	function wpv_meta_html_extra_css() {
		$view_templates_ids = array_unique( $this->view_template_used_ids );
		$cssout = '';
		foreach ( $view_templates_ids as $view_template_id ) {
			$extra_css = get_post_meta($view_template_id, '_wpv_view_template_extra_css', true);
			if ( isset( $extra_css ) ) {
				$cssout .= $extra_css;
			}
		}
		if ( '' != $cssout ) {
			echo "\n<style type=\"text/css\" media=\"screen\">\n$cssout\n</style>\n";
		}
	}

	function wpv_meta_html_extra_js() {
		$view_templates_ids = array_unique( $this->view_template_used_ids );
		$jsout = '';
		foreach ( $view_templates_ids as $view_template_id ) {
			$extra_js = get_post_meta($view_template_id, '_wpv_view_template_extra_js', true);
			if ( isset( $extra_js ) ) {
				$jsout .= $extra_js;
			}
		}
		if ( '' != $jsout ) {
			echo "\n<script type=\"text/javascript\">\n$jsout\n</script>\n";
		}
	}
}
