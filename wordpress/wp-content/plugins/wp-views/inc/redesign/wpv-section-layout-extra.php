<?php


add_action( 'view-editor-section-layout', 'add_view_layout_extra', 20, 3 );

function add_view_layout_extra( $view_settings, $view_layout_settings, $view_id ) {
    //Get loop content template
    $loop_content_template = get_post_meta( $view_id, '_view_loop_template', true );
    if ( ! empty( $loop_content_template ) ) {
        $loop_template = get_post( $loop_content_template );
        $loop_content_template_name = $loop_template->post_title;
    } else {
		$loop_content_template = ''; // Sometimes this can be zero
		$loop_content_template_name = '';
	}
	// What kind of view are we showing?
	if ( 
		! isset( $view_settings['view-query-mode'] )
		|| ( 'normal' == $view_settings['view-query-mode'] ) 
	) {
		$view_kind = 'view';
	} else {
		// we assume 'archive' or 'layouts-loop'
		$view_kind = 'wpa';
	}
	$section_help_pointer = WPV_Admin_Messages::edit_section_help_pointer( 'layout_html_css_js' );
	?>
	<div class="wpv-setting-container wpv-setting-container-horizontal wpv-settings-layout-markup js-wpv-settings-layout-extra">

		<div class="wpv-settings-header">
			<h3>
				<?php _e( 'Loop Output', 'wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip"
						data-header="<?php echo esc_attr( $section_help_pointer['title'] ); ?>"
						data-content="<?php echo esc_attr( $section_help_pointer['content'] ); ?>">
				</i>
			</h3>
		</div>
		<div class="wpv-setting">
            <input type="hidden" value="<?php echo esc_attr( $loop_content_template ); ?>" id="js-loop-content-template" />
            <input type="hidden" value="<?php echo esc_attr( $loop_content_template_name ); ?>" id="js-loop-content-template-name" />
			<div class="js-error-container js-wpv-error-container js-wpv-toolset-messages"></div>
			<div class="js-code-editor code-editor layout-html-editor" data-name="layout-html-editor">
				<div class="code-editor-toolbar js-code-editor-toolbar">
					<ul class="js-wpv-layout-edit-toolbar">
						<li>
							<button class="button-secondary js-code-editor-toolbar-button js-open-meta-html-wizard">
								<i class="icon-th"></i>
								<span class="button-label"><?php _e( 'Loop Wizard','wpv-views' ); ?></span>
							</button>
						</li>
						<?php
							do_action( 'wpv_views_fields_button', 'wpv_layout_meta_html_content' );
						?>
						<li>
							<button class="button-secondary js-code-editor-toolbar-button js-wpv-ct-assign-to-view" data-id="<?php echo esc_attr( $view_id ); ?>">
								<i class="icon-paste"></i>
								<span class="button-label"><?php _e('Content Template','wpv-views'); ?></span>
							</button>
						</li>
						<?php
						
							if ( 'view' == $view_kind ) {
								// we only add the pagination button to the Layout box if the View is not a WPA
								?>
								<li class="js-editor-pagination-button-wrapper">
									<button class="button-secondary js-code-editor-toolbar-button js-wpv-pagination-popup" data-content="wpv_layout_meta_html_content">
										<i class="icon-pagination"></i>
										<span class="button-label"><?php _e('Pagination controls','wpv-views'); ?></span>
									</button>
								</li>
								<?php
							} else if( 'wpa' == $view_kind ) {
								// Button to the Archive pagination controls popup for WPAs
								?>
								<li>
									<button class="button-secondary js-code-editor-toolbar-button js-wpv-archive-pagination-popup"
											data-content="wpv_layout_meta_html_content">
										<i class="icon-pagination"></i>
										<span class="button-label"><?php _e( 'Pagination controls', 'wpv-views' ); ?></span>
									</button>
								</li>
								<?php
							}

							do_action( 'wpv_cred_forms_button', 'wpv_layout_meta_html_content' );

						?>
						<li>
							<button class="button-secondary js-code-editor-toolbar-button js-wpv-media-manager"
									data-id="<?php echo esc_attr( $view_id ); ?>"
									data-content="wpv_layout_meta_html_content">
								<i class="icon-picture"></i>
								<span class="button-label"><?php _e('Media','wpv-views'); ?></span>
							</button>
						</li>
					</ul>
				</div>

				<textarea cols="30" rows="10" id="wpv_layout_meta_html_content" autocomplete="off" name="_wpv_layout_settings[layout_meta_html]"><?php echo ( isset( $view_layout_settings['layout_meta_html'] ) ) ? esc_textarea( $view_layout_settings['layout_meta_html'] ) : ''; ?></textarea>
				<?php 
				$layout_extra_css = isset( $view_settings['layout_meta_html_css'] ) ? $view_settings['layout_meta_html_css'] : '';
				$layout_extra_js = isset( $view_settings['layout_meta_html_js'] ) ? $view_settings['layout_meta_html_js'] : '';
				?>
				<div class="wpv-editor-metadata-toggle js-wpv-editor-metadata-toggle js-wpv-assets-editor-toggle" data-instance="layout-css-editor" data-target="js-wpv-assets-layout-css-editor" data-type="css">
					<span class="wpv-toggle-toggler-icon js-wpv-toggle-toggler-icon">
						<i class="icon-caret-down icon-large"></i>
					</span>
					<i class="icon-pushpin js-wpv-textarea-full" style="<?php echo ( empty( $layout_extra_css ) ) ? 'display:none;' : ''; ?>"></i>
					<strong><?php _e( 'CSS editor', 'wpv-views' ); ?></strong>
				</div>
				<div id="wpv-assets-layout-css-editor" class="wpv-assets-editor hidden js-wpv-assets-layout-css-editor">
					<textarea cols="30" rows="10" id="wpv_layout_meta_html_css" autocomplete="off" name="_wpv_settings[layout_meta_html_css]"><?php echo esc_textarea( $layout_extra_css ); ?></textarea>
				</div>
				
				<div class="wpv-editor-metadata-toggle js-wpv-editor-metadata-toggle js-wpv-assets-editor-toggle" data-instance="layout-js-editor" data-target="js-wpv-assets-layout-js-editor" data-type="js">
					<span class="wpv-toggle-toggler-icon js-wpv-toggle-toggler-icon">
						<i class="icon-caret-down icon-large"></i>
					</span>
					<i class="icon-pushpin js-wpv-textarea-full" style="<?php echo ( empty( $layout_extra_js ) ) ? 'display:none;' : ''; ?>"></i>
					<strong><?php _e( 'JS editor', 'wpv-views' ); ?></strong>
				</div>
				<div id="wpv-assets-layout-js-editor" class="wpv-assets-editor hidden js-wpv-assets-layout-js-editor">
					<textarea cols="30" rows="10" id="wpv_layout_meta_html_js" autocomplete="off" name="_wpv_settings[layout_meta_html_js]"><?php echo esc_textarea( $layout_extra_js ); ?></textarea>
				</div>
				<?php
					wpv_formatting_help_layout();
				?>
			</div>
			<p class="update-button-wrap js-wpv-update-button-wrap">
				<span class="js-wpv-message-container"></span>
				<button data-success="<?php echo esc_attr( __( 'Data updated', 'wpv-views' ) ); ?>"
						data-unsaved="<?php echo esc_attr( __( 'Data not saved', 'wpv-views' ) ); ?>"
						data-nonce="<?php echo wp_create_nonce( 'wpv_view_layout_extra_nonce' ); ?>"
						class="js-wpv-layout-extra-update button-secondary" disabled="disabled">
					<?php _e( 'Update', 'wpv-views' ); ?>
				</button>
			</p>
		</div>
	</div>
	<?php
}

// Layout Extra save callback function

add_action( 'wp_ajax_wpv_update_layout_extra', 'wpv_update_layout_extra_callback' );

function wpv_update_layout_extra_callback() {

    // Authentication
	if ( ! current_user_can( 'manage_options' ) ) {
		$data = array(
			'type' => 'capability',
			'message' => __( 'You do not have permissions for that.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	if ( 
		! isset( $_POST["wpnonce"] )
		|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_layout_extra_nonce' ) 
	) {
		$data = array(
			'type' => 'nonce',
			'message' => __( 'Your security credentials have expired. Please reload the page to get new ones.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}

    $view_id = (int) wpv_getpost( 'id', 0 );

    // This will give us a View, a WPA or null.
    $view = WPV_View_Base::get_instance( $view_id );

    if ( $view_id < 1 || ( null == $view ) ) {
		$data = array(
			'type' => 'id',
			'message' => __( 'Wrong or missing ID.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}

    try {

        // We're updating multiple properties at once.
        $view->defer_after_update_actions();

        // Actually we're changing only View settings and loop settings here.
        // If any of those changes fails, the database will not be updated.
        $view->begin_modifying_view_settings();
        $view->begin_modifying_loop_settings();

        $view->css = wpv_getpost( 'layout_css_val' );
        $view->js = wpv_getpost( 'layout_js_val' );

        $view->loop_meta_html = wpv_getpost( 'layout_val' );

        // Save the wizard settings
        if ( isset( $_POST['include_wizard_data'] ) ) {

            $view->loop_style = wpv_getpost( 'style' );
            $view->loop_table_column_count = wpv_getpost( 'table_cols' );
            $view->loop_bs_column_count = wpv_getpost( 'bootstrap_grid_cols' );
            $view->loop_bs_grid_container = wpv_getpost( 'bootstrap_grid_container' );
            $view->loop_row_class = wpv_getpost( 'bootstrap_grid_row_class' );
            $view->loop_bs_individual = wpv_getpost( 'bootstrap_grid_individual' );
            $view->loop_include_field_names = wpv_getpost( 'include_field_names' );
            $view->loop_fields = wpv_getpost( 'fields' ); // @todo sanitize this
            $view->loop_real_fields = wpv_getpost( 'real_fields' ); // @todo sanitize this

            // Remove unused Content Template
            $ct_to_delete = (int) wpv_getpost( 'delete_view_loop_template', 0 );
            if( $ct_to_delete > 0 ) {
                $view->delete_unused_loop_template( $ct_to_delete );
            }

        }

        // Now store changes.
        $view->finish_modifying_view_settings();
        $view->finish_modifying_loop_settings();
        $view->resume_after_update_actions();


    } catch ( WPV_RuntimeExceptionWithMessage $e ) {

        // Validation errors go here.
        wp_send_json_error( array( 'type' => 'update', 'message' => $e->getUserMessage() ) );

    } catch ( Exception $e ) {

        wp_send_json_error( array( 'type' => 'update', 'message' => __( 'An unexpected error ocurred.', 'wpv-views' ) ) );
    }

    // Success!
    $data = array(
        'id' => $view_id,
        'message' => __( 'Loop Output saved', 'wpv-views' )
    );
	wp_send_json_success( $data );
}