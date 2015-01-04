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
        add_action('wp_ajax_set_view_template', array($this, 'set_view_template_callback'));
        add_filter('the_content', array($this, 'the_content'), 1, 1);
        add_filter('the_content', array($this, 'restore_wpautop'), 999, 1);

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
            add_action('admin_head', array($this,'include_admin_css'));

            add_action('wp_ajax_wpv_get_archive_view_template_taxonomy_summary', array($this, '_ajax_get_taxonomy_loop_summary'));
            add_action('wp_ajax_wpv_get_archive_view_template_post_type_summary', array($this, '_ajax_get_post_type_loop_summary'));
            add_action('wp_ajax_wpv_get_archive_view_template_post_type_edit', array($this, '_ajax_get_post_type_loop_edit'));

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

    function post_edit_template_options(){

		global $post;

		$post_object = get_post_type_object($post->post_type);

		if ($post_object->publicly_queryable || $post_object->public) {
			// Add meta box so that a Content Template can be set for a post
			add_meta_box('views_template', __('Content Template', 'wpv-views'), array($this,'meta_box'), $post->post_type, 'side', 'high');
		} else if ($post_object->name == 'view-template') {
			// add a meta box for the views template settings
			$this->add_view_template_settings();
		}
    }

	function add_view_template_settings() {
		// Don't add to embedded version.
	}

	/**
	 * Add admin css to the Content Template edit page
	 *
	 */

    function include_admin_css() {
		// do nothing in theme version.
    }

	/**
	 * Add a meta box to other post types so the template can be selected
	 * for the post that's being edited
	 *
	 */

    function meta_box($post) {

        global $wpdb, $WP_Views;
		global $sitepress;

        $view_tempates_available = $wpdb->get_results("SELECT ID, post_name, post_title FROM {$wpdb->posts} WHERE post_type='view-template' AND post_status in ('publish')");
        if (isset($_GET['post'])) {
            $template_selected = get_post_meta($_GET['post'], '_views_template', true);
        } else {
            $template_selected = 0;
            global $pagenow, $post_type;
            if ($pagenow == 'post-new.php') {

				if (isset($_GET['trid'])) {
					// we are creating a translated post
					if (isset($sitepress)) {
						$translations = $sitepress->get_element_translations($_GET['trid'], 'post_' . $post->post_type);

						if (isset($translations[$_GET['source_lang']])) {
							$template_selected = get_post_meta($translations[$_GET['source_lang']]->element_id, '_views_template', true);
							if ($template_selected) {
								// use a translsation if we have one.
								$template_selected = icl_object_id($template_selected, 'view-template', true);
							}
						}
					}
				}

				if ($template_selected == 0) {

					// see if we have specified what template to use for this post type

					$options = $WP_Views->get_options();

					if (isset($options['views_template_for_' . $post_type])) {
						$template_selected = $options['views_template_for_' . $post_type];
						if ($template_selected != 0 && function_exists('icl_object_id')) {
							// use a translsation if we have one.
							$template_selected = icl_object_id($template_selected, 'view-template', true);
						}
					}

				}

            }
        }

        $view_template = '';
        //$view_template .= '<p><strong>' . __('Views template', 'wpv-views') . '</strong></p>';
        $view_template .= '<select name="views_template[' . $post->ID .  ']" id="views_template">';

        // Add a "None" type to the list.
        $none = new stdClass();
        $none->ID = '0';
        $none->post_title = __('None', 'wpv-views');
        array_unshift($view_tempates_available, $none);

        if (function_exists('icl_object_id')) {
            $template_selected = icl_object_id($template_selected, 'view-template', true);
        }

        foreach($view_tempates_available as $template) {

			if (isset($sitepress)) {
				// See if we should only display the one for the correct lanuage.
				$lang_details = $sitepress->get_element_language_details($template->ID, 'post_view-template');
				if ($lang_details) {
					$translations = $sitepress->get_element_translations($lang_details->trid, 'post_view-template');
					if (count($translations) > 1) {
						$lang = $sitepress->get_current_language();
						if (isset($translations[$lang])) {
							// Only display the one in this language.
							if ($template->ID != $translations[$lang]->element_id) {
								continue;
							}
						}
					}
				}
			}

            if ($template_selected == $template->ID)
                $selected = ' selected="selected"';
            else
                $selected = '';

			if ($template->post_title != '') {
				$view_template .= '<option value="' . $template->ID . '"' . $selected . '>' . $template->post_title . '</option>';
			} else {
				$view_template .= '<option value="' . $template->ID . '"' . $selected . '>' . $template->post_name . '</option>';
			}
        }
        $view_template .= '</select>';

        echo $view_template;

    }

	/**
	 * Save the meta box data when a post is saved
	 *
	 */

    function save_post_actions($pidd, $post){
		global $wpdb, $WP_Views;

        if (isset($_POST['views_template'])) {
			// make sure we only update this for the current post.
	        if (isset($_POST['post_ID']) && $_POST['post_ID'] == $pidd && isset($_POST['views_template'][$pidd])) {

				$template_selected = $_POST['views_template'][$pidd];

				update_post_meta($pidd, '_views_template', $template_selected);
			}
		} elseif (isset($_POST['wpcf_post_relationship'])) {
			// handle Types post relationships

			if (isset($_POST['wpcf_post_relationship'][$pidd]['post_type'])) {
				// Saving an existing child post
				$post_type = $_POST['wpcf_post_relationship'][$pidd]['post_type'];
			} else {
				// Saving a new child post
				$post_type = $wpdb->get_var("SELECT post_type FROM {$wpdb->posts} WHERE ID={$pidd}");
			}

			// set the Content Template if one hasn't been set.
            $template_selected = get_post_meta($pidd, '_views_template', true);
			if ($template_selected == '') {
				$options = $WP_Views->get_options();

				if (isset($options['views_template_for_' . $post_type])) {
					$template_selected = $options['views_template_for_' . $post_type];
					update_post_meta($pidd, '_views_template', $template_selected);
				}
			}

		}
    }

	/**
	 * get the template id from the name and include caching.
	 *
	 */

	function get_template_id($template_name) {
		global $wpdb;

		static $templates = array();
		if (!isset($templates[$template_name])) {
			$templates[$template_name] = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_type='view-template' AND post_name='{$template_name}'");
		}
		if (!isset($templates[$template_name])) {
			$templates[$template_name] = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_type='view-template' AND post_title='{$template_name}'");
		}

		return $templates[$template_name];
	}

	/**
	 *	get the template content and include caching
	 *
	 */

	function get_template_content( $template_id ) {
		global $wpdb;

		static $view_templates = array();
		if (!isset($view_templates[$template_id])) {
			$template_full = get_post( $template_id );
			// If there is no post with that ID
			if ( is_null( $template_full ) || !is_object( $template_full ) ) {
				return null;
			}
			if ( isset( $template_full->post_type ) && $template_full->post_type == 'view-template' ) {
				$view_templates[$template_id] = $template_full->post_content;
			} else {
				// If there is a post with that ID but it is not a Content Template
				return null;
			}
		//	$view_templates[$template_id] = $wpdb->get_var("SELECT post_content FROM {$wpdb->posts} WHERE ID=" . $template_id);
		}

		return $view_templates[$template_id];

	}

	/**
	 * apply the filter to the content/body of a post.
	 * Checks to see if a view-template is set for the post
	 * and renders using the view_template if on exists
	 */

    function the_content($content) {
        global $id, $post, $wpdb, $WP_Views, $wp_query, $wplogger, $WPVDebug;

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

		if (!$function_ok) {

			$options = $WP_Views->get_options();
			if (isset($options['wpv-theme-function'])) {
				if (in_array($db[3]['function'], explode(',', str_replace(', ', ',', $options['wpv-theme-function'])))) {
					$function_ok = true;
				}
			}

			if (!$function_ok) {
				// We don't except calls from the calling function.
				if (current_user_can('administrator')) {
					if (isset($options['wpv-theme-function-debug']) && $options['wpv-theme-function-debug']) {
						$content = sprintf(__('<strong>Content template debug: </strong>Calling function is <strong>%s</strong>', 'wpv-views'), $db[3]['function']) . '<br />' . $content;
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

		static $view_options = null;
		static $taxonomy_loop = null;
		static $archive_loop = null;
		if (!$view_options) {
			$view_options = $WP_Views->get_options();

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
				if (isset($view_options[$taxonomy_loop]) && $view_options[$taxonomy_loop] > 0) {
					if (!isset($post->view_template_override_loop_setting)) {
						$template_selected = $view_options[$taxonomy_loop];
						$post->view_template_override_loop_setting = true;
					}
				}
			} else if ($archive_loop) {
				if (isset($view_options[$archive_loop]) && $view_options[$archive_loop] > 0) {
					if (!isset($post->view_template_override_loop_setting)) {
						$template_selected = $view_options[$archive_loop];
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

		$WPVDebug->update_template_id($template_selected);

        if ($template_selected) {
			if (function_exists('icl_object_id')) {
				$template_selected = icl_object_id($template_selected, 'view-template', true);
			}
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
		remove_filter('the_excerpt', 'wpautop');
		remove_filter('the_excerpt', 'shortcode_unautop');

		$this->wpautop_removed = true;
	}

	function restore_wpautop($content) {
		if ($this->wpautop_removed) {
			add_filter('the_content', 'wpautop');
			add_filter('the_content', 'shortcode_unautop');
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
		global $WP_Views, $post, $wp_query;

		static $view_options = null;
		static $taxonomy_loop = null;
		static $archive_loop = null;
		if (!$view_options) {
			$view_options = $WP_Views->get_options();

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

		if ($taxonomy_loop && isset($view_options[$taxonomy_loop])) {
			// this is a taxonomy loop. Get the Content Template using the_content function
			$content = do_shortcode($this->the_content($content));
		} else if ($archive_loop && isset($view_options[$archive_loop])) {
			// this is an archive loop. Get the Content Template using the_content function
			$content = do_shortcode($this->the_content($content));
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
                                                   WPV_URL . '/res/img/bw_icon16.png');

            add_short_codes_to_js(array('post', 'view', 'view-form','body-view-templates'), $this->editor_addon);
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
	 */

	function get_view_template_select_box($row, $template_selected) {
		global $wpdb;

        $view_tempates_available = $wpdb->get_results("SELECT ID, post_title, post_name FROM {$wpdb->posts} WHERE post_type='view-template' AND post_status in ('publish')");

        $view_template = '';
        //$view_template .= '<p><strong>' . __('Views template', 'wpv-views') . '</strong></p>';
		if ($row === '') {
			$view_template .= '<select class="views_template_select" name="views_template" id="views_template">';
		} else {
			$view_template .= '<select class="views_template_select" name="views_template_' . $row . '" id="views_template_' . $row . '">';
		}

        // Add a "None" type to the list.
        $none = new stdClass();
        $none->ID = '0';
        $none->post_title = __('None', 'wpv-views');
        array_unshift($view_tempates_available, $none);

//         echo "<pre><code class="language-php">";
//         var_dump($view_tempates_available);
//         echo "</pre>";
//         var_dump($template_selected);

        foreach($view_tempates_available as $template) {
            if ($template_selected == $template->ID)
                $selected = ' selected="selected"';
            else
                $selected = '';

			if ($template->post_title) {
				$view_template .= '<option value="' . $template->ID . '"' . $selected . '>' . $template->post_title . '</option>';
			} else {
				$view_template .= '<option value="' . $template->ID . '"' . $selected . '>' . $template->post_name . '</option>';
			}
        }
        $view_template .= '</select>';

        return $view_template;
	}

	function get_view_template_titles() {
        global $wpdb;

        static $view_templates_available = null;

		if ($view_templates_available === null) {

			$view_templates_available = array();
			$view_templates = $wpdb->get_results("SELECT ID, post_title, post_name FROM {$wpdb->posts} WHERE post_type='view-template'");
			foreach ($view_templates as $view_template) {
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
		global $WP_Views;

		if (!isset($_POST['views_template'])) {

			// set the content template if one hasn't been set.
			$template_selected = get_post_meta($pidd, '_views_template', true);
			if ($template_selected == '') {
				$options = $WP_Views->get_options();

				if (isset($options['views_template_for_' . $post->post_type])) {
					$template_selected = $options['views_template_for_' . $post->post_type];
					update_post_meta($pidd, '_views_template', $template_selected);
				}
			}

		}

	}

	function set_template_for_attachments($pidd) {
		global $WP_Views;

		if (!isset($_POST['views_template'])) {

			// set the content template if one hasn't been set.
			$template_selected = get_post_meta($pidd, '_views_template', true);
			if ($template_selected == '') {
				$options = $WP_Views->get_options();

				if (isset($options['views_template_for_attachment'])) {
					$template_selected = $options['views_template_for_attachment'];
					update_post_meta($pidd, '_views_template', $template_selected);
				}
			}

		} elseif ( isset( $_POST['views_template'][$pidd] ) )  {
			update_post_meta($pidd, '_views_template', $_POST['views_template'][$pidd]);
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

/**
 * render_view_template
 *
 * Returns the content of a Content Template applied to a Post
 *
 * @param $view_template_id (integer) ID of the relevant Content Template
 * @param $post_in (post object) post to apply the Content Template to
 * @param $current_user_in (optional) (user object) sets the global $current_user
 *
 * @return (string) Content Template content applied to the Post
 *
 * @usage:  <?php echo render_view_template(80, $mypost)); ?>
 *
 * @note we need to set the global $authordata to the right user
 *
 * @since unknown
 */

function render_view_template($view_template_id, $post_in = null, $current_user_in = null) {
	global $WPV_templates, $post, $current_user, $authordata;
	if ( $post_in ) {
		$post = $post_in;
		$authordata = new WP_User($post->post_author);
	}
	if ( $current_user_in ) {
		$current_user = $current_user_in;
	}
	if (function_exists('icl_object_id')) {
		$view_template_id = icl_object_id($view_template_id, 'view-template', true);
	}
	$content = $WPV_templates->get_template_content($view_template_id);
	// If this function returns null, $view_template_id does not exist or is not a Content Template
	if ( is_null( $content ) ) {
		return '';
	}
	$WPV_templates->view_template_used_ids[] = $view_template_id;
	$output_mode = get_post_meta($view_template_id, '_wpv_view_template_mode', true);
	if ($output_mode == 'raw_mode') {
		$WPV_templates->remove_wpautop();
	}
	$content = wpml_content_fix_links_to_translated_content($content);
	$content = apply_filters('the_content', $content);
	return $content;
}