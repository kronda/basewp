<?php

add_action( 'view-editor-section-layout', 'add_view_layout_template', 40, 4 );

function add_view_layout_template( $view_settings, $view_layout_settings, $view_id, $user_id ) {
	$dismissed_pointers = get_user_meta( $user_id, '_wpv_dismissed_pointers', true );
	if ( ! is_array( $dismissed_pointers ) || empty( $dismissed_pointers ) ) {
		$dismissed_pointers = array();
	}
	$dismissed_dialogs = get_user_meta( $user_id, '_wpv_dismissed_dialogs', true );
	if ( ! is_array( $dismissed_dialogs ) || empty( $dismissed_dialogs ) ) {
		$dismissed_dialogs = array();
	}
    wp_nonce_field( 'wpv-ct-inline-edit', 'wpv-ct-inline-edit' );
	wp_nonce_field( 'wpv_inline_content_template', 'wpv_inline_content_template' );
    $templates = array();
    $valid_templates = array();
    $first_time = get_post_meta( $view_id, '_wpv_first_time_load', true );
    if ( isset( $view_layout_settings['included_ct_ids'] ) ) {
        $templates = explode( ',', $view_layout_settings['included_ct_ids'] );
        $valid_templates = $templates;
    }
    if ( count( $templates ) > 0 ) {
		$attached_templates = count( $templates );
		foreach ( $templates as $key => $template_id ) {
			if ( is_numeric( $template_id ) ) {
				$template_post = get_post( $template_id );
				if ( 
					is_object( $template_post )
					&& $template_post->post_status  == 'publish'
					&& $template_post->post_type == 'view-template' 
				) {
				} else {
					unset( $valid_templates[$key] ); // remove Templates that might have been deleted or are missing
				}
            } else {
				unset( $valid_templates[$key] ); // remove Templates that might have been deleted or are missing
            }
        }
        if ( count( $templates ) != count( $valid_templates ) ) {
			$view_layout_settings['included_ct_ids'] = implode( ',', $valid_templates );
			update_post_meta( $view_id, '_wpv_layout_settings', $view_layout_settings );
			do_action( 'wpv_action_wpv_save_item', $view_id );
        }
    }
	$section_help_pointer = WPV_Admin_Messages::edit_section_help_pointer( 'templates_for_view' );
    ?>
	<div id="attached-content-templates" class="wpv-settings-templates wpv-setting-container wpv-setting-container-horizontal wpv-settings-layout-markup js-wpv-settings-inline-templates"<?php echo ( count( $valid_templates ) < 1 ) ? ' style="display:none;"':'' ?>>
		<div class="wpv-settings-header">
			<h3><?php _e('Templates for this View', 'wpv-views') ?>
				<i class="icon-question-sign js-display-tooltip" 
					data-header="<?php echo esc_attr( $section_help_pointer['title'] ); ?>" 
					data-content="<?php echo esc_attr( $section_help_pointer['content'] ); ?>">
				</i>
			</h3>
		</div>
		<?php
		if ( $first_time == 'on') {
			$purpose = $view_settings['view_purpose'];
			if ( $purpose == 'slider' ) {
				wpv_get_view_ct_slider_introduction_data();
			}
		}
		?>
		<div class="js-wpv-content-template-view-list wpv-content-template-view-list wpv-setting">
			<ul class="wpv-inline-content-template-listing js-wpv-inline-content-template-listing">
				<?php
				if ( count( $valid_templates ) > 0 ) {
					$opened = false;
					if ( count( $valid_templates ) == 1 ) {
						$opened = true;
					}
					foreach ( $valid_templates as $valid_ct_id ) {
						// This is cached so it is OK to do that again
						$valid_ct_post = get_post( $valid_ct_id );
						wpv_list_view_ct_item( $valid_ct_post, $valid_ct_id, $view_id, $opened );
					}
				}
				?>
			</ul>
			<div class="js-wpv-message-container js-wpv-content-template-section-errors"></div>
		</div>		
	</div>
	
	<!-- @todo: move this to the view-editor-section-hidden action -->
	<div id="js-wpv-inline-content-templates-dialogs" class="popup-window-container">
	
		<!-- Colorbox dialogs -->
		
		<?php
		$dismissed_classname = '';
		if ( isset( $dismissed_dialogs['remove-content-template-from-view'] ) ) {
			$dismissed_classname = ' js-wpv-dialog-dismissed';
		}
		?>
		
		<div class="wpv-dialog js-wpv-dialog-remove-content-template-from-view<?php echo $dismissed_classname; ?>">
            <div class="wpv-dialog-header">
                <h2><?php _e('Remove the Content Template from the view','wpv-views') ?></h2>
                <i class="icon-remove js-dialog-close"></i>
            </div>
            <div class="wpv-dialog-content">
                <p>
                    <?php _e("This will remove the link between your view and the Content Template.  The Content Template will not be deleted.") ?>
                </p>
				<p>
					<label for="wpv-dettach-inline-content-template-dismiss">
						<input type="checkbox" id="wpv-dettach-inline-content-template-dismiss" class="js-wpv-dettach-inline-content-template-dismiss" />
						<?php _e("Don't show this message again",'wpv-views') ?>
					</label>
            	</p>
            </div>
            <div class="wpv-dialog-footer">
                <button class="button js-dialog-close"><?php _e('Cancel','wpv-views') ?></button>
                <button class="button button-primary js-wpv-remove-template-from-view"><?php _e('Remove','wpv-views') ?></button>
            </div>
		</div>
		
		<!-- Pointers -->
		
		<?php
		$dismissed_classname = '';
		if ( isset( $dismissed_pointers['inserted-inline-content-template'] ) ) {
			$dismissed_classname = ' js-wpv-pointer-dismissed';
		}
		?>
		<div class="js-wpv-inserted-inline-content-template-pointer<?php echo $dismissed_classname; ?>">
			<h3><?php _e( 'Content Template inserted in the layout', 'wpv-views' ); ?></h3>
			<p>
				<?php
				_e('A Content Template works like a subroutine.', 'wpv-views');
				echo WPV_MESSAGE_SPACE_CHAR;
				_e('You can edit its content in one place and use it in several places in the View.', 'wpv-views');
				?>
			</p>
			<p>
				<label>
					<input type="checkbox" class="js-wpv-dismiss-pointer" data-pointer="inserted-inline-content-template" id="wpv-dismiss-inserted-inline-content-template-pointer" />
					<?php _e( 'Don\'t show this again', 'wpv-views' ); ?>
				</label>
			</p>
		</div>
	
	
	</div><!-- end of .popup-window-container -->
<?php 
delete_post_meta( $view_id, '_wpv_first_time_load' );
}

function wpv_list_view_ct_item( $template, $ct_id, $view_id, $opened = false ) {
    ?>
    <li id="wpv-ct-listing-<?php echo esc_attr( $ct_id ); ?>" class="js-wpv-ct-listing js-wpv-ct-listing-show js-wpv-ct-listing-<?php echo esc_attr( $ct_id ); ?> layout-html-editor" data-id="<?php echo esc_attr( $ct_id ); ?>" data-viewid="<?php echo esc_attr( $view_id ); ?>">
        <span class="wpv-inline-content-template-title" style="display:block;">
			<button class="button button-secondary button-small js-wpv-content-template-open wpv-content-template-open" data-target="<?php echo esc_attr( $ct_id ); ?>" data-viewid="<?php echo esc_attr( $view_id ); ?>">
				<i class="js-wpv-open-close-arrow icon-caret-<?php if ( $opened ) { echo 'up'; } else { echo 'down'; } ?>"> </i>
            </button>
			<strong><?php echo $template->post_title; ?></strong>
			<span class="wpv-inline-content-template-action-buttons">
				<button class="button button-secondary button-small js-wpv-ct-update-inline js-wpv-ct-update-inline-<?php echo esc_attr( $ct_id ); ?>" disabled="disabled" data-unsaved="<?php echo esc_attr( __('Not saved', 'wpv-views') ); ?>" data-id="<?php echo esc_attr( $ct_id ); ?>"><?php _e('Update','wpv-views'); ?></button>
				<button class="button button-secondary button-small js-wpv-ct-remove-from-view"><i class="icon-remove"></i> <?php _e('Remove','wpv-views'); ?></button>
			</span>
		</span>
        <div class="js-wpv-ct-inline-edit wpv-ct-inline-edit wpv-ct-inline-edit js-wpv-inline-editor-container-<?php echo esc_attr( $ct_id ); ?> <?php if ( ! $opened ) { echo 'hidden'; } ?>" data-template-id="<?php echo esc_attr( $ct_id ); ?>">
			<?php if ( $opened ) { ?>
			<div class="code-editor-toolbar js-code-editor-toolbar">
			   <ul class="js-wpv-v-icon js-wpv-v-icon-<?php echo esc_attr( $ct_id ); ?>">
					<?php
					do_action( 'wpv_views_fields_button', 'wpv-ct-inline-editor-' . $ct_id );
					do_action( 'wpv_cred_forms_button', 'wpv-ct-inline-editor-' . $ct_id );
					?>
					<li>
						<button class="button-secondary js-code-editor-toolbar-button js-wpv-media-manager" data-id="<?php echo esc_attr( $ct_id ); ?>" data-content="wpv-ct-inline-editor-<?php echo esc_attr( $ct_id ); ?>">
							<i class="icon-picture"></i>
							<span class="button-label"><?php _e('Media','wpv-views'); ?></span>
						</button>
					</li>
			   </ul>
			</div>
			<textarea name="name" rows="10" class="js-wpv-ct-inline-editor-textarea" id="wpv-ct-inline-editor-<?php echo esc_attr( $ct_id ); ?>" data-id="<?php echo esc_attr( $ct_id ); ?>"><?php echo esc_textarea( $template->post_content ); ?></textarea>
			<?php
			wpv_formatting_help_inline_content_template( $template );
			?>
			<?php } ?>
		</div>
		<?php 
		if ( $opened ) {
		?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				if ( typeof window["wpv_ct_inline_editor_<?php echo esc_js( $ct_id ); ?>"] === "undefined" ) {
					window["wpv_ct_inline_editor_<?php echo esc_js( $ct_id ); ?>"] = icl_editor.codemirror('wpv-ct-inline-editor-<?php echo esc_js( $ct_id ); ?>', true);
					window["wpv_ct_inline_editor_val_<?php echo esc_js( $ct_id ); ?>"] = window["wpv_ct_inline_editor_<?php echo esc_js( $ct_id ); ?>"].getValue();
					
					var wpv_inline_editor_qt = quicktags( { id: 'wpv-ct-inline-editor-<?php echo esc_js( $ct_id ); ?>', buttons: 'strong,em,link,block,del,ins,img,ul,ol,li,code,close' } );
					WPV_Toolset.CodeMirror_instance['wpv_ct_inline_editor_<?php echo esc_js( $ct_id ); ?>'] = window["wpv_ct_inline_editor_<?php echo esc_js( $ct_id ); ?>"];
					WPV_Toolset.add_qt_editor_buttons( wpv_inline_editor_qt, WPV_Toolset.CodeMirror_instance['wpv_ct_inline_editor_<?php echo esc_js( $ct_id ); ?>'] );
					
					window["wpv_ct_inline_editor_<?php echo esc_js( $ct_id ); ?>"].on('change', function() {
						if( window["wpv_ct_inline_editor_val_<?php echo esc_js( $ct_id ); ?>"] !=  window["wpv_ct_inline_editor_<?php echo esc_js( $ct_id ); ?>"].getValue()){
							$('.js-wpv-ct-update-inline-<?php echo esc_js( $ct_id ); ?>')
								.removeClass('button-secondary')
								.addClass('button-primary js-wpv-section-unsaved')
								.prop( 'disabled', false );
							setConfirmUnload( true );
						}
						else{
							$('.js-wpv-ct-update-inline-<?php echo esc_js( $ct_id ); ?>')
								.removeClass('button-primary js-wpv-section-unsaved')
								.addClass('button-secondary')
								.prop( 'disabled', true );
							$('.js-wpv-ct-update-inline-<?php echo esc_js( $ct_id ); ?>').parent().find('.toolset-alert-error').remove();
							if ($('.js-wpv-section-unsaved').length < 1) {
								setConfirmUnload(false);
							}
						}
					});
				}
			});

		</script>
		<?php } ?>
	</li>
    <?php
}

/**
* wpv_assign_ct_to_view_callback
*
* Dialog to assign a Content Template as an inline one to a View, created by the event of clicking on the Content Template button in the Layout toolbar
*
* As we need to update the list of already assigned Content Templates along with the one of existing but not assigned, we need to do this on an AJAX call
*
* @since unknown
*
* @todo add proper wp_send_json_error/wp_send_json_success management here
*/

add_action( 'wp_ajax_wpv_assign_ct_to_view', 'wpv_assign_ct_to_view_callback' );

function wpv_assign_ct_to_view_callback() {
    if ( ! current_user_can( 'manage_options' ) ) {
		die( "Untrusted user" );
	}
	if (
		! isset( $_POST["wpnonce"] )
		|| (
			! wp_verify_nonce( $_POST["wpnonce"], 'wpv_inline_content_template' ) 
			&& ! wp_verify_nonce( $_POST["wpnonce"], 'wpv-ct-inline-edit' )
		)	// Keep this for backwards compat and also for Layouts
	) {
		die( "Undefined Nonce" );
	}
	if ( 
		! isset( $_POST['view_id'] ) 
		|| ! is_numeric( $_POST["view_id"] )
		|| intval( $_POST['view_id'] ) < 1 
	) {
		die( "Untrusted data" );
	}
	global $wpdb;
	$view_id = $_POST['view_id'];
	$layout_settings = get_post_meta( $view_id, '_wpv_layout_settings', true);
	$assigned_templates = array();
	if ( isset( $layout_settings['included_ct_ids'] ) && $layout_settings['included_ct_ids'] != '' ) {
		$assigned_templates = explode( ',', $layout_settings['included_ct_ids'] );
	}
	?>
	<div class="wpv-dialog js-wpv-dialog-add-new-content-template">
		<div class="wpv-dialog-header">
			<h2><?php _e('Assign a Content Template to this View','wpv-views') ?></h2>
			<i class="icon-remove js-dialog-close"></i>
		</div>
		<div class="wpv-dialog-content">
			<p>
				<?php
				_e( 'Use Content Templates as chunks of content that will be repeated in each element of the View loop.', 'wpv-views' );
				?>
			</p>
			<?php
			$not_in = '';
			$view_loop_template_key = '_view_loop_template';
			$not_in_array = $wpdb->get_col( 
				$wpdb->prepare( 
					"SELECT meta_value FROM {$wpdb->postmeta} 
					WHERE meta_key = %s 
					ORDER BY post_id",
					$view_loop_template_key
				)
			); 
			$query_args = array(
				'post_type' => 'view-template',
				'orderby' => 'title', 
				'order' => 'ASC',
				'posts_per_page' => '-1'
			);
			if ( count( $assigned_templates ) > 0 ) {
			?>
				<h4><?php _e( 'This View has some Content Templates already assigned', 'wpv-views' ); ?></h4>
				<div style="margin-left:20px;">
					<input type="radio" name="wpv-ct-type" value="already" class="js-wpv-inline-template-type" id="js-wpv-ct-type-existing-asigned">
					<label for="js-wpv-ct-type-existing-asigned"><?php _e( 'Insert a Content Template already assigned into the View', 'wpv-views' ) ?></label>
					<div class="js-wpv-assign-ct-already" style="margin-left:20px;">
						<select class="js-wpv-inline-template-assigned-select" id="js-wpv-ct-add-id-assigned">
							<option value="0"><?php _e( 'Select a Content Template','wpv-views' ) ?>&hellip;</option>
							<?php
							foreach ( $assigned_templates as $assigned_temp ) {
							 if ( is_numeric( $assigned_temp ) ) {
								// This is cached so it is OK to load the whole post
								$template_post = get_post( $assigned_temp );
								if ( 
									is_object( $template_post ) 
									&& $template_post->post_status  == 'publish'
									&& $template_post->post_type  == 'view-template'
								) {
									$not_in_array[] =  $template_post->ID;
									echo '<option value="' . esc_attr( $template_post->ID ) . '">' . $template_post->post_title . '</option>';
								}
							 }
							}
							?>
						</select>
					</div>
				</div>
				<h4><?php _e( 'Assign other Content Template to the View', 'wpv-views' ); ?></h4>
			<?php
			} else {
			?>
				<h4><?php _e( 'Assign a Content Template to the View', 'wpv-views' ); ?></h4>
			<?php
			}
			// @todo transform this in a suggest text input
			// limit the query to just one, as we are OK with just that
			// also, it should return just IDs for performance
			if ( ! empty( $not_in_array ) ) {
				$not_in = implode( ',', $not_in_array );
				$query_args['exclude'] = $not_in;
			}
			$query = get_posts( $query_args );
			if ( count( $query ) > 0 ) {
			?>
				<div style="margin:0 0 10px 20px;">
					<input type="radio" name="wpv-ct-type" class="js-wpv-inline-template-type" value="existing" id="js-wpv-ct-type-existing">
					<label for="js-wpv-ct-type-existing"><?php _e( 'Assign an existing Content template to this View','wpv-views' ) ?></label>
					<div class="js-wpv-assign-ct-existing" style="margin-left:20px;">
						<select class="js-wpv-inline-template-existing-select" id="js-wpv-ct-add-id">
							<option value="0"><?php _e( 'Select a Content Template','wpv-views' ) ?>&hellip;</option>
							<?php
							foreach( $query as $temp_post ) {
                                echo '<option value="' . esc_attr( $temp_post->ID ) .'">' . $temp_post->post_title .'</option>';
							}
							?>
						</select>
					</div>
				</div>
			<?php
			}
			?>
			<div style="margin:0 0 10px 20px;">
				<input type="radio" name="wpv-ct-type" class="js-wpv-inline-template-type" value="new" id="js-wpv-ct-type-new">
				<label for="js-wpv-ct-type-new"><?php _e('Create a new Content Template and assign it to this View','wpv-views') ?></label>
				<div style="margin-left:20px;" class="js-wpv-assign-ct-new">
					<input type="text" class="js-wpv-inline-template-new-name" id="js-wpv-ct-type-new-name" placeholder="<?php echo esc_attr( __( 'Type a name', 'wpv-views' ) ); ?>">
					<div class="js-wpv-add-new-ct-name-error-container"></div>
				</div>
			</div>
			<div class="js-wpv-inline-template-insert" id="js-wpv-add-to-editor-line" style="margin:10px 0 10px 20px;">
				<hr />
				<input type="checkbox" class="js-wpv-add-to-editor-check" name="wpv-ct-add-to-editor" id="js-wpv-ct-add-to-editor-btn" checked="checked">
				<label for="js-wpv-ct-add-to-editor-btn"><?php _e('Insert the Content Template shortcode to editor','wpv-views') ?></label>
			</div>
		</div>
		<div class="wpv-dialog-footer">
			<button class="button button-secondary js-dialog-close"><?php _e('Cancel','wpv-views') ?></button>
			<button class="button button-primary js-wpv-assign-inline-content-template"><?php _e('Assign Content Template','wpv-views') ?></button>
		</div>
	</div>
	<?php
    die();
}

// Create a new or assign an existing Content template as an inline Template for a View or WPA
// @todo add proper wp_send_json_error/wp_send_json_success management here

add_action( 'wp_ajax_wpv_add_view_template', 'wpv_add_view_template_callback' );

function wpv_add_view_template_callback() {
    if ( ! current_user_can( 'manage_options' ) ) {
		die( "Security check" );
	}
    if (
		! isset( $_POST["wpnonce"] )
		|| (
			! wp_verify_nonce( $_POST["wpnonce"], 'wpv_inline_content_template' ) 
			&& ! wp_verify_nonce( $_POST["wpnonce"], 'wpv-ct-inline-edit' )
		)	// Keep this for backwards compatibility and also for Layouts
	) {
		die( "Security check" );
	}
	if ( 
		! isset( $_POST['view_id'] ) 
		|| ! is_numeric( $_POST["view_id"] )
		|| intval( $_POST['view_id'] ) < 1 
	) {
		echo 'error';
		die();
	}
	$ct_post_id = 0;
	$view_id = sanitize_text_field( $_POST['view_id'] );
    if ( isset( $_POST['template_name'] ) ) {
        // We need to create a new Content Template based on the POSTed template_name
		$template_name = sanitize_text_field( $_POST['template_name'] );
		$response = wpv_create_content_template( $template_name, '', false, '' );
		if ( isset( $response['error'] ) ) {
			// Another Content Template with that title or name already exists
			echo 'error_name';
			die();
		} else if ( isset( $response['success'] ) ) {
			// Everything went well
			$ct_post_id = $response['success'];
		}
    } else if ( isset( $_POST['template_id'] ) ) {
       $ct_post_id = sanitize_text_field( $_POST['template_id'] );
    }
    $ct_post_object = get_post( $ct_post_id );
    if ( ! is_object( $ct_post_object ) ) {
        echo 'error';
		die();
    }
    $meta = get_post_meta( $view_id, '_wpv_layout_settings', true );
    $reg_templates = array();
    if ( isset( $meta['included_ct_ids'] ) ) {
        $reg_templates = explode( ',', $meta['included_ct_ids'] );
	}
	if ( in_array( $ct_post_id, $reg_templates ) ) {
		// The Content Template was already on the inline list
		echo 'wp_success';
	} else {
		// Add the Content Template to the inline list and save it
		$reg_templates[] = $ct_post_id;
        $meta['included_ct_ids'] = implode( ',', $reg_templates );
        update_post_meta( $view_id, '_wpv_layout_settings', $meta );
		do_action( 'wpv_action_wpv_save_item', $view_id );
        wpv_list_view_ct_item( $ct_post_object, $ct_post_id, $view_id, true );
	}
    die();
}

// Load CT editor (inline - inside View editor page) TODO check nonce and, god's sake, error handling

/**
* wpv_ct_loader_inline_callback
*
* Load a Content Template in the View or WPA layout section
*
* Displays the textarea with toolbars, and optionally the formatting instructions
*
* @note used by Layouts too
*
* @since unknown
*/

add_action( 'wp_ajax_wpv_ct_loader_inline', 'wpv_ct_loader_inline_callback' );

function wpv_ct_loader_inline_callback() {
    if ( ! current_user_can( 'manage_options' ) ) {
		die( "Undefined Nonce." );
	}
	if (
		! isset( $_POST["wpnonce"] )
		|| (
			! wp_verify_nonce( $_POST["wpnonce"], 'wpv_inline_content_template' ) 
			&& ! wp_verify_nonce( $_POST["wpnonce"], 'wpv-ct-inline-edit' )
		)	// Keep this for backwards compat and also for Layouts
	) {
		die( "Undefined Nonce." );
	}
	// @todo check why the hell this is here
    do_action('views_ct_inline_editor');
	if (
		! isset( $_POST["id"] )
		|| ! is_numeric( $_POST["id"] )
		|| intval( $_POST['id'] ) < 1 
	) {
		echo 'error';
		die();
	}
    $template = get_post( $_POST['id'] );
    // @todo check what the hell is that constant
	// This is for the CRED button and icon!!
	define("CT_INLINE", "1");
    if ( 
		is_object( $template ) 
		&& isset( $template->ID ) 
		&& isset( $template->post_type ) 
		&& $template->post_type == 'view-template'
	) {
        $ct_id = $template->ID;
    ?>
       	<div class="code-editor-toolbar js-code-editor-toolbar">
	       <ul class="js-wpv-v-icon js-wpv-v-icon-<?php echo esc_attr( $ct_id ); ?>">
	            <?php
				do_action( 'wpv_views_fields_button', 'wpv-ct-inline-editor-' . $ct_id );
				do_action( 'wpv_cred_forms_button', 'wpv-ct-inline-editor-' . $ct_id );
				?>
				<li>
					<button class="button-secondary js-code-editor-toolbar-button js-wpv-media-manager" data-id="<?php echo esc_attr( $ct_id ); ?>" data-content="wpv-ct-inline-editor-<?php echo esc_attr( $ct_id ); ?>">
						<i class="icon-picture"></i>
						<span class="button-label"><?php _e('Media','wpv-views'); ?></span>
					</button>
				</li>
	       </ul>
      	</div>
		<textarea name="name" rows="10" class="js-wpv-ct-inline-editor-textarea" data-id="<?php echo esc_attr( $ct_id ); ?>" id="wpv-ct-inline-editor-<?php echo esc_attr( $ct_id ); ?>"><?php echo esc_textarea( $template->post_content ); ?></textarea>
		<?php
		if ( isset( $_POST['include_instructions'] ) ) {
			if ( $_POST['include_instructions'] == 'inline_content_template' ) {
				wpv_formatting_help_inline_content_template( $template );
			}
			if ( $_POST['include_instructions'] == 'layouts_content_cell' ) {
				wpv_formatting_help_layouts_content_template_cell( $template );
			}
		}
		?>
    <?php
    } else {
       echo 'error';
    }
    die();
}

/**
* wpv_ct_update_inline_callback
*
* Updates one inline Content Template in a layout section of a View or WPA
*
* @since unknown
*/

add_action( 'wp_ajax_wpv_ct_update_inline', 'wpv_ct_update_inline_callback' );

function wpv_ct_update_inline_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		$data = array(
			'type' => 'capability',
			'message' => __( 'You do not have permissions for that.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	if ( 
		! isset( $_POST["wpnonce"] )
		|| (
			! wp_verify_nonce( $_POST["wpnonce"], 'wpv_inline_content_template' ) 
			&& ! wp_verify_nonce( $_POST["wpnonce"], 'wpv-ct-inline-edit' )
		)
	) {
		$data = array(
			'type' => 'nonce',
			'message' => __( 'Your security credentials have expired. Please reload the page to get new ones.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	if (
		! isset( $_POST["ct_id"] )
		|| ! is_numeric( $_POST["ct_id"] )
		|| intval( $_POST['ct_id'] ) < 1 
	) {
		$data = array(
			'type' => 'id',
			'message' => __( 'Wrong or missing ID.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
    $my_post = array();
    $my_post['ID'] = $_POST['ct_id'];
    $my_post['post_content'] = $_POST['ct_value'];
	if ( isset( $_POST['ct_title'] ) ) {
		$my_post['post_title'] = $_POST['ct_title'];
	}
    $result = wp_update_post( $my_post );
	do_action( 'wpv_action_wpv_save_item', $_POST['ct_id'] );
	$data = array(
		'id' => $_POST["ct_id"],
		'message' => __( 'Inline Content Template saved', 'wpv-views' )
	);
	wp_send_json_success( $data );
}

/**
* wpv_remove_content_template_from_view_callback
*
* Removes a Content Template from the list of inline Templates of a View
*
* @since unknown
*/

add_action( 'wp_ajax_wpv_remove_content_template_from_view', 'wpv_remove_content_template_from_view_callback' );

function wpv_remove_content_template_from_view_callback() {
    if ( ! current_user_can( 'manage_options' ) ) {
		$data = array(
			'type' => 'capability',
			'message' => __( 'You do not have permissions for that.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	if (
		! isset( $_POST["wpnonce"] )
		|| (
			! wp_verify_nonce( $_POST["wpnonce"], 'wpv_inline_content_template' ) 
			&& ! wp_verify_nonce( $_POST["wpnonce"], 'wpv-ct-inline-edit' )
		)	// Keep this for backwards compat and also for Layouts, but it has been deleted from the VIews script
	) {
		$data = array(
			'type' => 'nonce',
			'message' => __( 'Your security credentials have expired. Please reload the page to get new ones.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	if (
		! isset( $_POST["view_id"] )
		|| ! is_numeric( $_POST["view_id"] )
		|| intval( $_POST['view_id'] ) < 1 
		|| ! isset( $_POST["template_id"] )
		|| ! is_numeric( $_POST["template_id"] )
		|| intval( $_POST['template_id'] ) < 1
	) {
		$data = array(
			'type' => 'id',
			'message' => __( 'Wrong or missing ID.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
    $view_id = $_POST['view_id'];
    $template_id = $_POST['template_id'];
    $meta = get_post_meta( $view_id, '_wpv_layout_settings', true );
    $templates = '';
    if ( isset( $meta['included_ct_ids'] ) ) {
		$reg_templates = explode( ',', $meta['included_ct_ids'] );
		if ( in_array( $template_id, $reg_templates ) ) {
			$id_key = array_search( $template_id, $reg_templates );
			unset( $reg_templates[$id_key] );
		}
		$templates = implode( ',', $reg_templates );
    }
    $meta['included_ct_ids'] = $templates;
	update_post_meta( $view_id, '_wpv_layout_settings', $meta );
	do_action( 'wpv_action_wpv_save_item', $view_id );
	if ( 
		isset( $_POST['dismiss'] ) 
		&& $_POST['dismiss'] == 'true' 
	) {
		wpv_dismiss_dialog( 'remove-content-template-from-view' );
	}
	$data = array(
		'id' => $_POST["id"],
		'message' => __( 'Inline Content Template removed', 'wpv-views' )
	);
	wp_send_json_success( $data );
}