<?php

add_action( 'view-editor-section-extra', 'add_view_content', 10, 2 );

function add_view_content( $view_settings, $view_id ) {
    /* This section will be visible if
     * - this is a View (not WPA) edit page and
     * - the 'filter-extra' section is displayed (not hidden).
     *
     * Apparently default behaviour for sections is to be visible unless
     * $view_settings['sections-show-hide'][ $section_name ] == 'off'
     *
     * Note that the container div has class js-wpv-settings-filter-extra, which will cause it to be shown or hidden
     * simultaneously with the filter-extra section when user changes the according option Screen options.
     */ 
	$is_section_hidden = false;

	// Hide if we're not editing a View
	if ( 
		isset( $view_settings['view-query-mode'] )
		&& 'normal' != $view_settings['view-query-mode'] )
	{
		$is_section_hidden = true;
	// Hide if 'filter-extra' section should be hidden.
	} else if ( 
		isset( $view_settings['sections-show-hide'] )
		&& isset( $view_settings['sections-show-hide']['filter-extra'] )
		&& 'off' == $view_settings['sections-show-hide']['filter-extra'] )
	{
		$is_section_hidden = true;
	}
	$hide_class = $is_section_hidden ? 'hidden' : '';
	$section_help_pointer = WPV_Admin_Messages::edit_section_help_pointer( 'complete_output' );
	?>
	<div class="wpv-setting-container wpv-setting-container-horizontal wpv-settings-complete-output js-wpv-settings-content js-wpv-settings-filter-extra <?php echo $hide_class; ?>">

		<div class="wpv-settings-header">
			<h3>
				<?php _e( 'Filter and Loop Output Integration', 'wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip" 
					data-header="<?php echo esc_attr( $section_help_pointer['title'] ); ?>" 
					data-content="<?php echo esc_attr( $section_help_pointer['content'] ); ?>">
				</i>
			</h3>
		</div>

		<div class="wpv-setting">
			<div class="js-code-editor code-editor content-editor" data-name="complete-output-editor">
				<?php
					$full_view = get_post( $view_id );
					$content = $full_view->post_content;
				?>
				<div class="code-editor-toolbar js-code-editor-toolbar">
					<ul>
						<?php
						do_action( 'wpv_views_fields_button', 'wpv_content' );
						do_action( 'wpv_cred_forms_button', 'wpv_content' );
						?>
						<li>
							<button class="button-secondary js-code-editor-toolbar-button js-wpv-media-manager" data-id="<?php echo esc_attr( $view_id );?>" data-content="wpv_content">
								<i class="icon-picture"></i>
								<span class="button-label"><?php _e('Media','wpv-views'); ?></span>
							</button>
						</li>
					</ul>
				</div>
				<textarea cols="30" rows="10" id="wpv_content" name="_wpv_settings[content]" autocomplete="off"><?php echo esc_textarea( $content ); ?></textarea>
				<?php
				wpv_formatting_help_combined_output();
				?>
			</div>
			<p class="update-button-wrap js-wpv-update-button-wrap">
				<span class="js-wpv-message-container"></span>
				<button data-success="<?php echo esc_attr( __('Content updated', 'wpv-views') ); ?>" data-unsaved="<?php echo esc_attr( __('Content not saved', 'wpv-views') ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_content_nonce' ); ?>" class="js-wpv-content-update button-secondary" disabled="disabled"><?php _e('Update', 'wpv-views'); ?></button>
			</p>
		</div>

	</div>
<?php }

// Content save callback function

add_action('wp_ajax_wpv_update_content', 'wpv_update_content_callback');

function wpv_update_content_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		$data = array(
			'type' => 'capability',
			'message' => __( 'You do not have permissions for that.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	if ( 
		! isset( $_POST["wpnonce"] )
		|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_content_nonce' ) 
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
	$content_post = get_post( $_POST["id"] );
	$content = $content_post->post_content;
	wpv_register_wpml_strings( $_POST["content"] );
	if ( $_POST["content"] != $content ) {
		$this_post = array();
		$this_post['ID'] = $_POST["id"];
		$this_post['post_content'] = $_POST["content"];
		wp_update_post( $this_post );
	}
	do_action( 'wpv_action_wpv_save_item', $_POST["id"] );
	$data = array(
		'id' => $_POST["id"],
		'message' => __( 'Combined Output saved', 'wpv-views' )
	);
	wp_send_json_success( $data );
}