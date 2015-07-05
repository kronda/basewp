<?php
require WPV_PATH_EMBEDDED . '/inc/views-templates/wpv-template.class.php';

class WPV_template_plugin extends WPV_template
{

    private $wpml_original_post_id;
    
    function init() {
		parent::init();
		
		add_action( 'edit_form_after_title', array( $this, 'insert_content_template_toolset_messages' ), 5 );
		add_action( 'edit_form_after_title', array( $this, 'insert_content_template_description_section' ), 5 );
		add_action( 'edit_form_after_title', array( $this, 'insert_content_template_edit_help_box' ) );
		
		add_filter( 'wp_editor_expand', array( $this, 'disable_wp_editor_expand_for_content_templates' ), 99, 2 );
    }
	
	function insert_content_template_toolset_messages( $post ) {
		if (
			is_object( $post )
			&& isset( $post->post_type )
			&& $post->post_type == 'view-template'
		) {
			?>
			<div class="js-wpv-content-template-toolset-messages"></div>
			<?php
		}
	}
	
	function insert_content_template_description_section( $post ) {
		if (
			is_object( $post )
			&& isset( $post->post_type )
			&& $post->post_type == 'view-template'
		) {
			$wpv_content_template_decription  = get_post_meta( $post->ID, '_wpv-content-template-decription', true );
			?>
			<div class="wpv-content-template-description" style="margin-top:20px">
				<button class="js-wpv-ct-description-button button-secondary"<?php echo ( $wpv_content_template_decription != '' ) ? ' style="display:none"' : ''; ?>><?php _e( "Add description", 'wpv-views' ); ?></button>
				<div class="js-wpv-ct-description-button-div<?php echo ( $wpv_content_template_decription == '' ) ? ' hidden' : ''; ?>">
					<p><?php _e( "Describe this Content Template", 'wpv-views' ); ?></p>
					<textarea id="wpv-ct-description" class="js-wpv-ct-description" name="_wpv-content-template-decription" cols="72" rows="4" style="width: 100%"><?php echo esc_textarea( $wpv_content_template_decription ); ?></textarea>
				</div>
			</div>
			<?php
		}
	}
	
	function insert_content_template_edit_help_box( $post ) {
		if (
			is_object( $post )
			&& isset( $post->post_type )
			&& $post->post_type == 'view-template'
		) {
			global $WPV_settings;
			$wpv_list_of_types = array();
			$first_p = '';
			$show_help = get_option('wpv_content_template_show_help');
            $post_types = get_post_types( array('public' => true), 'objects' );
            $taxonomies = get_taxonomies( '', 'objects' );
            $exclude_tax_slugs = array();
			$exclude_tax_slugs = apply_filters( 'wpv_admin_exclude_tax_slugs', $exclude_tax_slugs );
            foreach ( $post_types as $post_type ) {
                $type = $post_type->name;
                if ( isset( $WPV_settings['views_template_for_' . $type] ) && $WPV_settings['views_template_for_' . $type] == $post->ID  ) {
                    $wpv_list_of_types[] = __( 'Single ', 'wpv-views' ) . $post_type->label;
                }
                if ( isset(  $WPV_settings['views_template_archive_for_' . $type] ) &&  $WPV_settings['views_template_archive_for_' . $type] == $post->ID  ) {
                    $wpv_list_of_types[] = __( 'Archive ', 'wpv-views' ) . $post_type->label;
                }
            }
            foreach ( $taxonomies as $category_slug => $category ) {
                if ( in_array( $category_slug, $exclude_tax_slugs ) || ! $category->show_ui ) {
                    continue;
                }
                $type = $category->name;
                if ( isset( $WPV_settings['views_template_loop_' . $type] ) && $WPV_settings['views_template_loop_' . $type] == $post->ID ) {
                    $wpv_list_of_types[] = $category->label;
                }
            }
            $wpv_list_of_types = implode( ', ', $wpv_list_of_types );
            $wpv_list_of_types = preg_replace("/\, /",' and ',$wpv_list_of_types, -1);
            if ( $wpv_list_of_types != '' ) {
				$wpv_list_of_types .= '.';
				$first_p = sprintf( __( 'This Content Template will replace the content of %s', 'wpv-views' ), $wpv_list_of_types );
			} else {
				$first_p = __( 'This Content Template will replace the content of the elements you assign it to.', 'wpv-views' );
			}
			if ( $show_help == 1 ) {
				$hidden = 'false';
			} else {
				$hidden = 'true';
			}
			
			$data_def = array(
				'text'			=> '<p>'
								. $first_p
								. '</p><p>'
								. __( 'It starts empty and you should add fields to it. To add fields, click on the V icon below.', 'wpv-views' )
								. WPV_MESSAGE_SPACE_CHAR
								. __( 'You can add HTML and CSS to style the fields and design the page template.', 'wpv-views' ) 
								. '</p>',
				'close'			=> 'true',
				'hidden'		=> $hidden,
				'classname'		=> 'js-wpv-content-template-edit-help-box'
			);
			wpv_toolset_help_box( $data_def );
		}
	}
	
	/**
	* disable_wp_editor_expand_for_content_templates
	*
	* Remove the Distraction Free Writing button from the toolbar of the Content Template edit screen
	*
	* This DFW mode was added in WordPress 4.0 and the $post_type parameter was added in WordPress 4.1
	* Ref: https://developer.wordpress.org/reference/hooks/wp_editor_expand/
	* 
	* @since 1.7
	*/
	
	function disable_wp_editor_expand_for_content_templates( $return, $post_type = 'post' ) {
		if ( $post_type == 'view-template' ) {
			$return = false;
		}
		return $return;
	}

    function add_view_template_settings() {
        global $post;
        $this->wpml_original_post_id = null;
        if ( is_object($post) ){
            global $post;
            $this->initialize_if_wpml_copy();
        }
        
        ?>
        <?php // TODO: Move this code to correct JS file ?>
        <script type="text/javascript">
         <?php
            if ($this->wpml_original_post_id) {
                $original_post = get_post($this->wpml_original_post_id);
                global $pagenow, $sitepress;

            ?>
                jQuery(document).ready(function($){
                    <?php if ($pagenow == 'post-new.php') {
                        // initialize the title and body by copying from the original post.
                        ?>
                        jQuery('#title').val('<?php echo esc_js($original_post->post_title . ' - ' . $sitepress->get_display_language_name($_GET['lang'], $_GET['lang'])); ?>');
                        jQuery('#content').val(editor_decode64('<?php echo base64_encode($original_post->post_content); ?>'));
                    <?php } else {
                        // Editing an existing post
                        ?>

                    <?php }

                    ?>
                    jQuery('#views_template input[type=checkbox]').prop('disabled', 'true');
                });
            <?php }
            ?>

        </script>

        <?php
        add_meta_box( 'views_template',
                __( 'Content Template Settings', 'wpv-views' ),
                array($this, 'view_settings_meta_box'), $post->post_type,
                'side', 'high' );
        add_meta_box( 'views_template_html_extra',
                __( 'Content Template CSS and JS', 'wpv-views' ),
                array($this, 'view_settings_meta_html_extra'), $post->post_type,
                'normal', 'high' );

    }

    function view_settings_meta_html_extra() {
        /**
         * Add admin metabox for custom CSS and javascript
         */
        global $post;
		// @todo this should not be here at all... maybe on the other metabox, or somewhere else
        $user_id = get_current_user_id();
        $show_hightlight = get_user_meta( $user_id, 'show_highlight', true );
        if (
			! isset( $show_hightlight ) 
			|| $show_hightlight == '' ) {
            $show_hightlight = 1;
        }
		$show_help = get_option( 'wpv_content_template_show_help' );
		?>
		<input type="hidden" name="show_highlight" class="js-wpv-ct-syntax-highlight-on" value="<?php echo esc_attr( $show_hightlight ); ?>" />
		<input type="hidden" name="show_help" class="js-wpv-ct-show-help" value="<?php echo esc_attr( $show_help ); ?>" />
		<?php
        $template_extra_css = get_post_meta( $post->ID, '_wpv_view_template_extra_css', true );
        $template_extra_js = get_post_meta( $post->ID, '_wpv_view_template_extra_js', true );
        $template_extra_state = get_post_meta( $post->ID, '_wpv_view_template_extra_state', true );
		if ( empty( $template_extra_state ) ) {
			$template_extra_state = array();
		}
		$css_editor_state = 'off';
		$js_editor_state = 'off';
		if ( isset( $template_extra_state['css'] ) ) {
			if ( $template_extra_state['css'] === 'on' ) {
				$css_editor_state = 'on';
			}
		}
		if ( isset( $template_extra_state['js'] ) ) {
			if ( $template_extra_state['js'] === 'on' ) {
				$js_editor_state = 'on';
			}
		}
        ?>
		<input type="hidden" name="_wpv_view_template_extra_state[css]" id="js-wpv-content-template-editor-state-css" value="<?php echo esc_attr( $css_editor_state ); ?>" />
        <input type="hidden" name="_wpv_view_template_extra_state[js]" id="js-wpv-content-template-editor-state-js" value="<?php echo esc_attr( $js_editor_state ); ?>" />
        <p>
            <?php _e( 'Here you can modify specific CSS and javascript to be used with this Content Template.','wpv-views' ); ?>
        </p>
		<ul id="wpv-content-template-extra" class="wpv-content-template-extra js-wpv-content-template-extra">
			<li class="wpv-has-itembox-header js-wpv-content-template-extra-item js-wpv-content-template-extra-css">
				<div class="wpv-content-template-extra-header wpv-itembox-header">
					<strong>
						<?php
						_e( 'CSS editor - add custom CSS to this Content Template', 'wpv-views' );
						?>
					</strong>
					<button class="button button-secondary button-small wpv-code-editor-toggler js-wpv-code-editor-toggler" data-kind="css" data-target="content-template-css-editor">
						<i class="icon-pushpin js-wpv-textarea-full" style="<?php if ( empty( $template_extra_css ) || $css_editor_state == 'on' ) { echo ' display:none;'; } ?>"></i>
						<span class="js-wpv-text-holder">
							<?php
							if ( $css_editor_state != 'on' || empty( $template_extra_css ) ) {
								_e( 'Open CSS editor', 'wpv-views' );
							} else {
								_e( 'Close CSS editor', 'wpv-views' );
							}
							?>
						</span>
					</button>
				</div>
				<div class="code-editor content-template-css-editor wpv-code-editor-closed js-wpv-code-editor js-wpv-content-template-css-editor<?php if ( $css_editor_state != 'on' || empty( $template_extra_css ) ) { echo ' js-wpv-code-editor-closed hidden'; } ?>">
					<textarea name="_wpv_view_template_extra_css" id="_wpv_view_template_extra_css" cols="97" rows="10"><?php echo esc_textarea( $template_extra_css ); ?></textarea>
					<?php wpv_formatting_help_extra_css( '' ); ?>
				</div>
			</li>
			
			<li class="wpv-has-itembox-header js-wpv-content-template-extra-item js-wpv-content-template-extra-js">
				<div class="wpv-content-template-extra-header wpv-itembox-header">
					<strong>
						<?php
						_e( 'JS editor - add custom javascript to this Content Template', 'wpv-views' );
						?>
					</strong>
					<button class="button button-secondary button-small wpv-code-editor-toggler js-wpv-code-editor-toggler" data-kind="js" data-target="content-template-js-editor">
						<i class="icon-pushpin js-wpv-textarea-full" style="<?php if ( empty( $template_extra_js ) || $js_editor_state == 'on' ) { echo ' display:none;'; } ?>"></i>
						<span class="js-wpv-text-holder">
							<?php
							if ( $js_editor_state != 'on' || empty( $template_extra_js ) ) {
								_e( 'Open JS editor', 'wpv-views' );
							} else {
								_e( 'Close JS editor', 'wpv-views' );
							}
							?>
						</span>
					</button>
				</div>
				<div class="code-editor content-template-js-editor wpv-code-editor-closed js-wpv-code-editor js-wpv-content-template-js-editor<?php if ( $js_editor_state != 'on' || empty( $template_extra_js ) ) { echo ' js-wpv-code-editor-closed hidden'; } ?>">
					<textarea name="_wpv_view_template_extra_js" id="_wpv_view_template_extra_js" cols="97" rows="10"><?php echo esc_textarea( $template_extra_js ); ?></textarea>
					<?php wpv_formatting_help_extra_js( '' ); ?>
				</div>
			</li>
		</ul>
    <?php }

    function initialize_if_wpml_copy() {

        global $pagenow, $sitepress, $post;

        // check for the new post page that has translation params
        if ( $sitepress && $pagenow == 'post-new.php' && isset($_GET['trid']) && isset($_GET['source_lang'])) {

            // Get the original ID
            $translations = $sitepress->get_element_translations($_GET['trid'], 'view-template');
            $this->wpml_original_post_id = $translations[$_GET['source_lang']]->element_id;

            // Copy extra CSS and JS as required.
            $template_extra_css = get_post_meta( $post->ID, '_wpv_view_template_extra_css', true );
            if ($template_extra_css == '') {
                $template_extra_css = get_post_meta( $this->wpml_original_post_id, '_wpv_view_template_extra_css', true );
                update_post_meta( $post->ID, '_wpv_view_template_extra_css', $template_extra_css);
            }

            $template_extra_js = get_post_meta( $post->ID, '_wpv_view_template_extra_js', true );
            if ($template_extra_js == '') {
                $template_extra_js = get_post_meta( $this->wpml_original_post_id, '_wpv_view_template_extra_js', true );
                update_post_meta( $post->ID, '_wpv_view_template_extra_js', $template_extra_js);
            }

            $template_extra_state = get_post_meta( $post->ID, '_wpv_view_template_extra_state', true );
            if ($template_extra_state == '') {
                $template_extra_state = get_post_meta( $this->wpml_original_post_id, '_wpv_view_template_extra_state', true );
                update_post_meta( $post->ID, '_wpv_view_template_extra_state', $template_extra_state);
            }

            // Copy the description as required
            $template_description = get_post_meta( $post->ID, '_wpv-content-template-decription', true );
            if ($template_description == '') {
                $template_description = get_post_meta( $this->wpml_original_post_id, '_wpv-content-template-decription', true );
                update_post_meta( $post->ID, '_wpv-content-template-decription', $template_description);
            }

            // Copy output mode as required.
            $output_mode = get_post_meta( $post->ID, '_wpv_view_template_mode', true );
            if ( !$output_mode ) {
                $output_mode = get_post_meta( $this->wpml_original_post_id, '_wpv_view_template_mode', true );
                if ($output_mode) {
                    update_post_meta( $post->ID, '_wpv_view_template_mode', $output_mode);
                }
            }

        }

        if ( $sitepress && $pagenow == 'post.php') {

            // Get the original ID
            $trid = $sitepress->get_element_trid($post->ID, 'post_view-template');

            $translations = $sitepress->get_element_translations($trid, 'view-template');
            foreach ($translations as $lang => $details) {
                if ($details->original) {
                    if ($post->ID != $translations[$lang]->element_id) {
                        $this->wpml_original_post_id = $translations[$lang]->element_id;
                    }
                    break;
                }
            }
        }

    }

    function view_settings_meta_box() {

        global $post;

        $output_mode = get_post_meta( $post->ID, '_wpv_view_template_mode', true );
		$layout_loop_template_for_view_id = get_post_meta( $post->ID, '_view_loop_id', true );
		
        if ( ! $output_mode ) {
            $output_mode = 'WP_mode';
        }

        ?>

        <p>
            <label for="_wpv_view_template_mode[]"><?php _e( 'Output mode', 'wpv-views' ); ?></label>:
            <select name="_wpv_view_template_mode[]" class="wpv_content_template_output_mode">
                <option value="WP_mode" <?php selected( $output_mode, 'WP_mode' ); ?>>
                    <?php _e( 'Auto-insert paragraphs', 'wpv-views' ); ?>
                </option>
                <option value="raw_mode" <?php selected( $output_mode, 'raw_mode' ); ?>>
                    <?php _e( 'Manual paragraphs', 'wpv-views' ); ?>
                </option>
            </select>
            <i class="icon-question-sign js-wpv-content-template-mode-tip" title="<?php echo esc_attr( __( 'Output mode', 'wpv-views' ) ); ?>"
                data-pointer-content-firstp="<?php echo esc_attr( __( 'Automaticaly paragraph (Normal WordPress output) - add paragraphs an breaks and resolve shortcodes', 'wpv-views' ) );?>"
                data-pointer-content-secondp="<?php echo esc_attr( __( 'Manual paragraph (Raw output) - only resolve shortcodes without adding line breaks or paragraphs', 'wpv-views' ) ); ?> "
                >
            </i>
        </p>

        <?php if ($this->wpml_original_post_id) {
            ?>
            <p>
                <strong><?php _e('Note','wpv-views'); ?>:</strong>
                <?php _e('These settings are synced with the post in the origin language and you can\'t change them here. They can only be changed on the post in the original language.', 'wpv-views'); ?></p>
            <?php
        }
        ?>

        <h4><?php _e( 'Use this Content Template for:', 'wpv-views' ); ?></h4>
		<?php
		if ( ! empty( $layout_loop_template_for_view_id ) ) {
			global $WP_Views;
			$view_loop_title = get_the_title( $layout_loop_template_for_view_id );
			$view_loop_status = get_post_status( $layout_loop_template_for_view_id );
			$view_loop_type_text = __( 'View', 'wpv-views' );
			if ( $WP_Views->is_archive_view( $layout_loop_template_for_view_id ) ) {
				$view_loop_type_text = __( 'WordPress Archive', 'wpv-views' );
			}
			$layout_loop_template_notice = '';
			if ( $view_loop_status == 'publish' ) {
				$view_loop_link = get_admin_url()."admin.php?page=views-editor&view_id=" . $layout_loop_template_for_view_id;
				$layout_loop_template_notice = sprintf( __( 'This Content Template is used as the loop block for the %s <a href="%s" target="_blank">%s</a>', 'wpv-views' ), $view_loop_type_text, $view_loop_link, $view_loop_title );
			} else {
				$layout_loop_template_notice = sprintf( __( 'This Content Template is used as the loop block for the trashed %s <strong>%s</strong>', 'wpv-views' ), $view_loop_type_text, $view_loop_title );
			}
			echo '<p class="toolset-alert toolset-alert-info">'
				. $layout_loop_template_notice
				. '</p>';
		} else {
			$this->_display_sidebar_content_template_settings();
		}
    }

    function view_settings_help() { ?>
            <p>
                <a class="wpv-help-link" target="_blank" href="http://wp-types.com/documentation/user-guides/view-templates/?utm_source=viewsplugin&utm_campaign=views&utm_medium=content-template-help-link-what-is-ct&utm_term=What is a Content Template"><?php_e( 'What is a Content Template', 'wpv-views' ); ?> &raquo;</a>
            </p>
            <p>
                <a class="wpv-help-link" target="_blank" href="http://wp-types.com/documentation/user-guides/editing-view-templates/?utm_source=viewsplugin&utm_campaign=views&utm_medium=content-template-help-link-editing-instructions&utm_term=Editing instructions"><?php _e( 'Editing instructions', 'wpv-views' )?>  &raquo;</a></p>
            <p>
                <a class="wpv-help-link" target="_blank" href="http://wp-types.com/documentation/user-guides/setting-view-templates-for-single-pages/?utm_source=viewsplugin&utm_campaign=views&utm_medium=content-template-help-link-how-to-apply&utm_term=How to apply Content Templates to content"><?php_e( 'How to apply Content Templates to content', 'wpv-views' ); ?>  &raquo;</a>
            </p>
        <?php
    }

	/**
	* wpv_ct_admin_enqueue_scripts
	*
	* Properly register and enqueue scripts for Content Template edit pages
	*
	* @since 1.7
	*/
	
	function wpv_ct_admin_enqueue_scripts( $hook ) {
		
		parent::wpv_ct_admin_enqueue_scripts( $hook );
		
		if ( ( $hook == 'post.php' || $hook == 'post-new.php' ) ) {
			global $post;
			if ( 'view-template' === $post->post_type ) {
				
				// Views admin alt stylesheet, maybe we should merge...
				wp_register_style( 'views-admin-alt-css' , WPV_URL . '/res/css/wpv-views.css', array(), WPV_VERSION );
				wp_enqueue_style( 'views-admin-alt-css' );
				
				// The rest of the scripts are added in Views code to all post.php and post-new.php screens

				// CodeMirror

				wp_enqueue_script( 'views-codemirror-script' );
				wp_enqueue_script( 'views-codemirror-overlay-script' );
				wp_enqueue_script( 'views-codemirror-xml-script' );
				wp_enqueue_script( 'views-codemirror-css-script' );
				wp_enqueue_script( 'views-codemirror-js-script' );
				wp_enqueue_script( 'views-codemirror-conf-script' );
				
				// Content Template main script
				$template_id = $post->ID;
				
				$ct_edit_translations = array(
					'template_id' => $template_id,
					'template_used_on' => __("Whatever", 'wpv-views'),
					'syntax_highlight_disable' => __( 'Disable Syntax Highlighting', 'wpv-views' ),
					'syntax_highlight_enable' => __( 'Enable Syntax Highlighting', 'wpv-views' ),
					'action_save' => __( 'Save', 'wpv-views' ),
					'pointer_close' => __( 'Close', 'wpv-views' ),
					'button_css_open' => __( 'Open CSS editor', 'wpv-views' ),
					'button_css_close' => __( 'Close CSS editor', 'wpv-views' ),
					'button_js_open' => __( 'Open JS editor', 'wpv-views' ),
					'button_js_close' => __( 'Close JS editor', 'wpv-views' ),
					'save_error_title_in_use' => __( 'A Content Template with that title already exists. Please use another title.', 'wpv-views' ),
					'save_error_title_empty' => __( 'You can not save a Content Template without title. Please fill in the title.', 'wpv-views' )
				);				
				wp_enqueue_script( 'views-content-template-js' );
				wp_localize_script( 'views-content-template-js', 'wpv_ct_edit_texts', $ct_edit_translations );
				
				// WordPress 4.0 compatibility: remove all the new fancy editor enhancements that break the highlighting and toolbars
				wp_dequeue_script( 'editor-expand' );

				wp_enqueue_style( 'views-codemirror-css' );
				wp_enqueue_style( 'toolset-font-awesome' );
				wp_enqueue_style( 'views-admin-css' );
				
			}
		}
	}

    function save_post_actions( $pidd, $post ) {

        if ( 
			$post->post_type == 'view-template' 
			&& $post->post_status == 'draft'
		) {
            // force the publish state.
            global $wpdb;
			$wpdb->query( 
				$wpdb->prepare(
					"UPDATE {$wpdb->posts} 
					SET post_status = 'publish' 
					WHERE ID = %d",
					$pidd 
				)
			);
            $post_name = $wpdb->get_var( 
				$wpdb->prepare(
					"SELECT post_name FROM {$wpdb->posts} 
					WHERE ID = %d 
					LIMIT 1",
					$pidd
				)
			);
            if ( 
				! isset( $post_name ) 
				|| empty( $post_name ) 
			) {
                $candidate_slug = sanitize_title( $_POST['post_title'] );
				$slug = wp_unique_post_slug( $candidate_slug, $pidd, 'publish', 'view-template', 0 );
				$wpdb->query( 
					$wpdb->prepare(
						"UPDATE {$wpdb->posts} 
						SET post_name = %s' 
						WHERE ID = %d",
						$slug,
						$pidd
					)
				);
            }
        }

        // Make sure it's on the Content Template editor page before saving info.
        // Otherwise inline CT editor will delete info it shouldn't.
        if ( 
			$post->post_type == 'view-template' 
			&& isset( $_POST['_wpv_view_template_mode'] )
		) {

            if ( isset( $_POST['_wpv-content-template-decription'] ) ) {
               update_post_meta( $pidd, '_wpv-content-template-decription', $_POST['_wpv-content-template-decription'] );
            } else {
				update_post_meta( $pidd, '_wpv-content-template-decription', '' );
			}
			if ( isset( $_POST['_wpv_view_template_mode'][0] ) ) {
                update_post_meta( $pidd, '_wpv_view_template_mode', $_POST['_wpv_view_template_mode'][0] );
				// @todo maybe DEPRECATE this
                wpv_view_template_update_field_values( $pidd );
            }
            if ( isset( $_POST['_wpv_view_template_extra_css'] ) ) {
                update_post_meta( $pidd, '_wpv_view_template_extra_css', $_POST['_wpv_view_template_extra_css'] );
            }
            if ( isset( $_POST['_wpv_view_template_extra_js'] ) ) {
                update_post_meta( $pidd, '_wpv_view_template_extra_js', $_POST['_wpv_view_template_extra_js'] );
            }
            $template_meta_html_state = array();
            if ( isset( $_POST['_wpv_view_template_extra_state']['css'] ) ) {
                $template_meta_html_state['css'] = $_POST['_wpv_view_template_extra_state']['css'];
            }
            if ( isset( $_POST['_wpv_view_template_extra_state']['js'] ) ) {
                $template_meta_html_state['js'] = $_POST['_wpv_view_template_extra_state']['js'];
            }
            if ( ! empty( $template_meta_html_state ) ) {
                update_post_meta( $pidd, '_wpv_view_template_extra_state', $template_meta_html_state );
            }
            if ( isset( $_POST['show_highlight'] ) ) {
                $user_id = get_current_user_id();
				$show_highlight = esc_attr( $_POST['show_highlight']  );
                update_user_meta( $user_id, 'show_highlight', $show_highlight );
            }
			
			if ( isset( $_POST['show_help'] ) ) {
				$show_help = esc_attr( $_POST['show_help'] );
				update_option( 'wpv_content_template_show_help', $show_help );
			}

            //Save settings toggle status
            if ( isset( $_POST['_wpv_content_template_settings_toggle_single'] ) ) {
               update_post_meta( $pidd, '_wpv_content_template_settings_toggle_single', $_POST['_wpv_content_template_settings_toggle_single'] );
            }
            if ( isset( $_POST['_wpv_content_template_settings_toggle_posts'] ) ) {
               update_post_meta( $pidd, '_wpv_content_template_settings_toggle_posts', $_POST['_wpv_content_template_settings_toggle_posts'] );
            }
            if ( isset( $_POST['_wpv_content_template_settings_toggle_taxonomy'] ) ) {
               update_post_meta( $pidd, '_wpv_content_template_settings_toggle_taxonomy', $_POST['_wpv_content_template_settings_toggle_taxonomy'] );
            }

            //Save settings
            global $WPV_settings;
            $this->clear_legacy_view_settings();
            // clear all options that have this template id
            foreach ( $WPV_settings as $key => $value ) {
                if ( $value == $pidd ) {
                   $WPV_settings[$key] = 0;
                }
            }
            foreach ( $_POST as $index => $value ) {
                if ( strpos( $index, 'views_template_loop_' ) === 0 ) {
                    $WPV_settings[$index] = $pidd;
                }
                if ( strpos( $index, 'views_template_for_' ) === 0 ) {
                    $WPV_settings[$index] = $pidd;
                }
                if ( strpos( $index, 'views_template_archive_for_' ) === 0 ) {
                    $WPV_settings[$index] = $pidd;
                }
            }
            $WPV_settings->save();
			do_action( 'wpv_action_wpv_save_item', $pidd );
        }
        
        if ( $post->post_type == 'view-template' ) {
			wpv_register_wpml_strings( $post->post_content );
        }

        // pass to the base class.
        parent::save_post_actions( $pidd, $post );
    }

    /**
     * If the post has a Content template
     * add an Content template edit link to post.
     */
    function edit_post_link( $link, $post_id ) {
		
        global $WPV_settings;
		
		if ( $WPV_settings->wpv_show_edit_view_link == 1 ){
			$template_selected = get_post_meta( $post_id, '_views_template', true );

			if ( !current_user_can( 'manage_options' ) )
				return $link;

			if ( $template_selected ) {
				remove_filter( 'edit_post_link', array( $this, 'edit_post_link'), 10, 2 );
				$template_selected_title = get_the_title( $template_selected );
				ob_start();

				edit_post_link( 
					sprintf( __( 'Edit Content Template %s', 'wpv-views' ), $template_selected_title ),
					'',
					'',
					$template_selected 
				);

				$template_edit_link = ob_get_clean();

				$template_edit_link = apply_filters( 'wpv_edit_view_link', $template_edit_link );

				if ( 
					isset( $template_edit_link ) 
					&& ! empty( $template_edit_link ) 
				) {
					$link = $link . ' ' . $template_edit_link;
				}

				add_filter( 'edit_post_link', array( $this, 'edit_post_link' ), 10, 2 );
			}
		}
        return $link;
    }

    /**
     * Ajax function to set the current Content template to posts of a type
     * set in $_POST['type']
     *
     */
    function set_view_template_callback() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			die( "Untrusted user" );
		}
		if ( 
			! isset( $_POST["wpnonce"] )
			|| ! wp_verify_nonce( $_POST["wpnonce"], 'set_view_template' ) 
			|| ! isset( $_POST['view_template_id'] )
			|| ! isset( $_POST['type'] )
		) {
			die( "Undefined nonce" );
		}
        $view_template_id = $_POST['view_template_id'];
		$type = $_POST['type'];
		wpv_update_dissident_posts_from_template( $view_template_id, $type);
        die();
    }

    
    // FIXME: Move to WPV_Settings class
    function clear_legacy_view_settings() {
        global $wpdb;

        $wpdb->query( 
			$wpdb->prepare(
				"DELETE FROM {$wpdb->postmeta} 
				WHERE meta_key = %s",
				'_views_template_new_type'
			)
		);
    }

    // FIXME: Move to Settings class
    function legacy_view_settings( $options ) {
        global $wpdb;

        $view_tempates_new = $wpdb->get_results( 
			$wpdb->prepare(
				"SELECT post_id, meta_value FROM {$wpdb->postmeta} 
				WHERE meta_key = %s",
				'_views_template_new_type'
			)
		);

        foreach ( $view_tempates_new as $template_for_new ) {
            $value = unserialize( $template_for_new->meta_value );
            if ( $value ) {
                foreach ( $value as $type => $status ) {
                    if ( $status ) {
                        $options['views_template_for_' . $type] = $template_for_new->post_id;
                    }
                }
            }
        }

        return $options;
    }

    function submit( $options ) {
        $this->clear_legacy_view_settings();

        foreach ( $_POST as $index => $value ) {
            if ( strpos( $index, 'views_template_loop_' ) === 0 ) {
                $options[$index] = $value;
            }
            if ( strpos( $index, 'views_template_for_' ) === 0 ) {
                $options[$index] = $value;
            }
            if ( strpos( $index, 'views_template_archive_for_' ) === 0 ) {
                $options[$index] = $value;
            }
        }

        if ( isset( $_POST['wpv_theme_function'] ) ) {
            $options['wpv_theme_function'] = $_POST['wpv_theme_function'];
            $options['wpv_theme_function_debug'] = isset( $_POST['wpv_theme_function_debug'] ) && $_POST['wpv_theme_function_debug'];
        }

        return $options;
    }

    function hide_view_template_author() {
        global $pagenow, $post;
        if ( ($pagenow == 'post-new.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'view-template') ||
                ($pagenow == 'post.php' && isset( $_GET['action'] ) && $_GET['action'] == 'edit') ) {

            $post_type = $post->post_type;

            if ( $pagenow == 'post.php' && $post_type != 'view-template' ) {
                return;
            }

            ?>
            <script type="text/javascript">
                jQuery('#authordiv').hide();
            </script>
            <?php
        }

    }

    function show_admin_messages() {
        global $pagenow, $post;

        if ( $pagenow == 'post.php' && isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) {

            $post_type = $post->post_type;

            if ( $pagenow == 'post.php' && $post_type != 'view-template' ) {
                return;
            }

            $open_tags = substr_count( $post->post_content, '[types' );
            $close_tags = substr_count( $post->post_content, '[/types' );
            if ( $close_tags < $open_tags ) {
                echo '<div id="message" class="error">';
                echo sprintf( __( '<strong>This template includes single-ended shortcodes</strong>. Please close all shortcodes to avoid processing errors. %sRead more%s',
                                'wpv-views' ),
                        '<a href="http://wp-types.com/faq/why-do-types-shortcodes-have-to-be-closed/?utm_source=viewsplugin&utm_campaign=views&utm_medium=edit-content-template-not-closed-shortcodes-message&utm_term=Read more" target="_blank">',
                        ' &raquo;</a>' );
                echo '</div>';
            }
        }
    }

    function disable_rich_edit_for_views( $state ) {
        global $pagenow, $post;
        if ( $state ) {
            if ( $pagenow == 'post.php' && isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) {

                if ( isset( $post ) && isset( $post->post_type ) ) {
					$post_type = $post->post_type;
				} else if ( isset( $_GET['post'] ) && is_numeric( $_GET['post'] ) ) {
					$post_id = intval( $_GET['post'] );
					$post_type = get_post_type( $post_id );
				}
                if ( $post_type != 'view-template' && $post_type != 'view' ) {
                    return $state;
                }
                $state = 0;
            }

            if ( $pagenow == 'post-new.php' && isset( $_GET['post_type'] ) && ($_GET['post_type'] == 'view-template' || $_GET['post_type'] == 'view') ) {
                $state = 0;
            }
        }
        return $state;
    }

	/**
	* _display_sidebar_content_template_settings
	*
	* Show the checkboxes groups for Single, CPT Archives and Taxonomy Archives in the Content Template edit screen metabox
	*
	* @since unknown
	*/
    function _display_sidebar_content_template_settings() {
        global $WPV_settings, $post, $wpdb, $pagenow;

        $toggle_taxonomy = $toggle_posts = 0;
        $toggle_single = 1;
        $id = -1;
        $asterisk = ' <span style="color:red;">*</span>';
        $show_asterisk_explanation = false;
        $post_types = get_post_types( array('public' => true), 'objects' );
        $taxonomies = get_taxonomies( '', 'objects' );
        $exclude_tax_slugs = array();
		$exclude_tax_slugs = apply_filters( 'wpv_admin_exclude_tax_slugs', $exclude_tax_slugs );
        wp_nonce_field( 'set_view_template', 'set_view_template' );
	
        if ( isset( $_GET['toggle'] ) ) {
             $toggle = explode( ',', $_GET['toggle'] );
             $toggle_taxonomy = $toggle[2];
             $toggle_posts = $toggle[1];
             $toggle_single = $toggle[0];
        } else if ( isset( $post ) && $pagenow != 'post-new.php' ) {
            if ( $this->wpml_original_post_id ) {
                $id = $this->wpml_original_post_id;
            } else {
                $id = $post->ID;
            }
            $template_options = get_post_meta( $id );
            // NOTE we are not exporting those _wpv_content_template_settings_toggle_* settings
            if ( isset( $template_options['_wpv_content_template_settings_toggle_single'] ) && isset( $template_options['_wpv_content_template_settings_toggle_single'][0] ) ) {
                $toggle_single = $template_options['_wpv_content_template_settings_toggle_single'][0];
            } else {
                $toggle_single = 0;
                foreach ( $WPV_settings as $key => $val ) {
					if ( strpos( $key, 'views_template_for_' ) === 0 && $WPV_settings[$key] == $id ) {
						$toggle_single = 1;
						break;
					}
                }
            }
            if ( isset( $template_options['_wpv_content_template_settings_toggle_posts'] ) ){
                $toggle_posts = $template_options['_wpv_content_template_settings_toggle_posts'][0];
            } else {
                $toggle_posts = 0;
                foreach ( $WPV_settings as $key => $val ) {
					if ( strpos( $key, 'views_template_archive_for_' ) === 0 && $WPV_settings[$key] == $id ) {
						$toggle_posts = 1;
						break;
					}
                }
            }
            if ( isset($template_options['_wpv_content_template_settings_toggle_taxonomy']) ){
                $toggle_taxonomy = $template_options['_wpv_content_template_settings_toggle_taxonomy'][0];
            } else {
                $toggle_taxonomy = 0;
				foreach ( $WPV_settings as $key => $val ) {
					if ( strpos( $key, 'views_template_loop_' ) === 0 && $WPV_settings[$key] == $id ) {
						$toggle_taxonomy = 1;
						break;
					}
                }
            }
        }
        $ct_selected = '';
        if ( isset( $_GET['ct_selected'] ) ) {
            $ct_selected = $_GET['ct_selected'];
        }
        ?>
        <p>
            <span class="js-wpv-content-template-open wpv-content-template-open" title="<?php echo esc_attr( __( "Click to toggle", 'wpv-views' ) ); ?>">
                <?php _e('Single pages:','wpv-views'); ?>
                <i class="icon-caret-down"></i>
            </span>
        </p>
        <div class="js-wpv-content-template-dropdown-list wpv-content-template-dropdown-list<?php echo $toggle_single == 0 ? ' hidden' : ''; ?>" id="wpv-content-template-dropdown-single-list">
            <ul>
                <li class="hidden">
                    <input type="hidden" name="_wpv_content_template_settings_toggle_single" value="<?php echo esc_attr( $toggle_single ); ?>">
                </li>
              <?php
                 foreach ( $post_types as $post_type ) {
                    $type = $post_type->name;
                    if ( ! isset( $WPV_settings['views_template_for_' . $type] ) ) {
                         $WPV_settings['views_template_for_' . $type] = 0;
                    }
					if ( ! in_array( $WPV_settings['views_template_for_' . $type], array( 0, $id ) ) ) {
						$show_asterisk_explanation = true;
					}
                    ?>
                    <li>
                        <input type="checkbox" value="1" id="views_template_for_<?php echo esc_attr( $type ); ?>" name="views_template_for_<?php echo esc_attr( $type ); ?>" class="js-wpv-check-for-icon"<?php echo ( $WPV_settings['views_template_for_' . $type] == $id || 'views_template_for_' . $type == $ct_selected ) ? ' checked="checked"':''?>>
                        <label for="views_template_for_<?php echo esc_attr( $type ); ?>"><?php  echo $post_type->label; echo ( ! in_array( $WPV_settings['views_template_for_' . $type], array( 0, $id ) ) ) ? $asterisk : ''; ?></label>
                    <?php
                        if ( $WPV_settings['views_template_for_' . $type] == $id ) {
                            $posts = $wpdb->get_col( 
								$wpdb->prepare(
									"SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} 
									WHERE post_type = %s
									AND post_status != 'auto-draft'",
									$type
								)
							);
                            $count = sizeof( $posts );
                            if ( $count > 0 ) {
                                $posts = "'" . implode( "','", $posts ) . "'";
                                $set_count = $wpdb->get_var( 
									$wpdb->prepare(
										"SELECT COUNT(post_id) FROM {$wpdb->postmeta} 
										WHERE meta_key = '_views_template' 
										AND meta_value = %s 
										AND post_id IN ({$posts}) 
										LIMIT %d",
										$WPV_settings['views_template_for_' . $type],
										$count
									)
								);
                                if ( ( $count - $set_count ) > 0 && !$this->wpml_original_post_id){
                                    ?>
                                    <a class="button button-leveled button-small icon-warning-sign js-wpv-content-template-alert" id="wpv-content-template-alert-link-<?php echo esc_attr( $type ); ?>"
                                    data-type="<?php echo esc_attr( $type ); ?>" data-tid="<?php echo esc_attr( $id ); ?>"
                                    href="<?php echo admin_url('admin-ajax.php'); ?>?action=wpv_ct_update_posts&amp;type=<?php echo esc_attr( $type ); ?>&amp;tid=<?php echo esc_attr( $id ); ?>&amp;wpnonce=<?php echo wp_create_nonce( 'work_view_template' )?>">
                                        <?php echo sprintf( __( 'Bind %u %s ', 'wpv-views' ), $count - $set_count, $post_type->label ); ?>
                                    </a>
                                    <?php
                                }
                            }
                        }
                    ?>
                    </li>
                    <?php
                 }
              ?>
            </ul>
        </div>
		<div class="popup-window-container">

			<div class="wpv-dialog js-wpv-dialog-ct-apply-success">
				<div class="wpv-dialog-header">
					<h2><?php _e('Templates updated','wpv-views'); ?></h2>
				</div>
				<div class="wpv-dialog-content">
					<p><?php echo sprintf( __( 'All %ss were successfully updated',
													'wpv-views' ),
											'<span class="js-wpv-ct-type-updated"></span>'); ?></p>
				</div>
				<div class="wpv-dialog-footer">
					<button class="button-secondary js-dialog-close"><?php _e('Cancel','wpv-views'); ?></button>
				</div>
			</div>
        </div>

        <p>
            <span class="js-wpv-content-template-open wpv-content-template-open" title="<?php echo esc_attr( __( "Click to toggle", 'wpv-views' ) ); ?>">
                <?php _e('Post archives:','wpv-views'); ?>
                <i class="icon-caret-down"></i>
            </span>
        </p>
        <div class="js-wpv-content-template-dropdown-list wpv-content-template-dropdown-list<?php echo $toggle_posts == 0 ? ' hidden' : ''; ?>" id="wpv-content-template-dropdown-posts-list">
            <ul>
                <li class="hidden">
                    <input type="hidden" name="_wpv_content_template_settings_toggle_posts" value="<?php echo esc_attr( $toggle_posts ); ?>">
                </li>
                  <?php
                  $custom_post_types_exist = false;
                     foreach ( $post_types as $post_type ) {
                        $type = $post_type->name;
                        if (!in_array($post_type->name, array('post', 'page', 'attachment')) && $post_type->has_archive) {
							if ( ! isset( $WPV_settings['views_template_archive_for_' . $type] ) ) {
								$WPV_settings['views_template_archive_for_' . $type] = 0;
							}
						$custom_post_types_exist = true;
						if ( ! in_array( $WPV_settings['views_template_archive_for_' . $type], array( 0, $id ) ) ) {
							$show_asterisk_explanation = true;
						}
				?>
				<li>
					<input type="checkbox" value="1" id="views_template_archive_for_<?php echo esc_attr( $type ); ?>" name="views_template_archive_for_<?php echo esc_attr( $type ); ?>"<?php echo ( $WPV_settings['views_template_archive_for_' . $type] == $id || 'views_template_archive_for_' . $type == $ct_selected ) ? ' checked="checked"':''?>>
					<label for="views_template_archive_for_<?php echo esc_attr( $type ); ?>"><?php  echo $post_type->label; echo ( !in_array( $WPV_settings['views_template_archive_for_' . $type], array( 0, $id ) ) ) ? $asterisk : ''; ?></label>
				</li>
                        <?php }
                     }
                     if ( !$custom_post_types_exist ) { ?>
				<li><?php _e('There are no custom post types archives', 'wpv-views'); ?></li>
                     <?php }
                  ?>
              </ul>
        </div>

        <p>
            <span class="js-wpv-content-template-open wpv-content-template-open" title="<?php echo esc_attr( __( "Click to toggle", 'wpv-views' ) ); ?>">
                <?php _e('Taxonomy archives:','wpv-views'); ?>
                <i class="icon-caret-down"></i>
            </span>
        </p>
        <div class="js-wpv-content-template-dropdown-list wpv-content-template-dropdown-list<?php echo $toggle_taxonomy == 0 ? ' hidden': '' ;?>">
            <ul>
                <li class="hidden">
                    <input type="hidden" name="_wpv_content_template_settings_toggle_taxonomy" value="<?php echo esc_attr( $toggle_taxonomy ); ?>">
                </li>
                  <?php
					$selected = '';
					foreach ( $taxonomies as $category_slug => $category ) {
						if ( in_array( $category_slug, $exclude_tax_slugs ) ) {
							continue;
						}
						if ( !$category->show_ui ) {
							continue; // Only show taxonomies with show_ui set to TRUE
						}
						$type = $category->name;
						if ( ! isset( $WPV_settings['views_template_loop_' . $type] ) ) {
                            $WPV_settings['views_template_loop_' . $type] = 0;
						}
						if ( !in_array( $WPV_settings['views_template_loop_' . $type], array( 0, $id ) ) ) {
							$show_asterisk_explanation = true;
						}
                    ?>
				<li>
					<input type="checkbox" value="1" id="views_template_loop_<?php echo esc_attr( $type ); ?>" name="views_template_loop_<?php echo esc_attr( $type ); ?>"<?php checked( $WPV_settings['views_template_loop_' . $type] == $id || 'views_template_loop_' . $type == $ct_selected ); ?>>
					<label for="views_template_loop_<?php echo esc_attr( $type ); ?>"><?php  echo $category->labels->name; echo ( ! in_array( $WPV_settings['views_template_loop_' . $type], array( 0, $id ) ) ) ? $asterisk : ''; ?></label>
				</li>
				<?php }
                ?>
            </ul>
        </div>
        <?php if ( $show_asterisk_explanation ) { ?>
        <hr />
		<span><?php _e( '<span style="color:red">*</span> A different Content Template is already assigned to this item', 'wpv-views' ); ?></span>
		<?php } ?>
        <?php
    }
}

/**
 * Update custom fields array for Content template on save
 * @param unknown_type $pidd post ID
 * @param unknown_type $post post reference
 */
function wpv_view_template_update_field_values( $pidd, $post = null ) {
    if ( $post == null ) {
        $post = get_post( $pidd );
    }
    $content = $post->post_content;
    $shortcode_expression = "/\\[(wpv-|types).*?\\]/i";

    // search for shortcodes
    $counts = preg_match_all( $shortcode_expression, $content, $matches );

    // iterate 0-level shortcode elements
    if ( $counts > 0 ) {
        $_wpv_view_template_fields = serialize( $matches[0] );
        update_post_meta( $pidd, '_wpv_view_template_fields',
                $_wpv_view_template_fields );
    }
}

