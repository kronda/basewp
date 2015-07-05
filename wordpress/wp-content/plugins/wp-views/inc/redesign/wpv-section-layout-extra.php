<?php


add_action( 'view-editor-section-layout', 'add_view_layout_extra', 20, 3 );

function add_view_layout_extra( $view_settings, $view_layout_settings, $view_id ) {
    //Get loop content template
    $loop_content_template = get_post_meta( $view_id, '_view_loop_template', true );
    $loop_content_template_name = '';
    if ( ! empty( $loop_content_template ) ){
        $loop_template = get_post( $loop_content_template );
        $loop_content_template_name = $loop_template->post_title;
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

				<textarea cols="30" rows="10" id="wpv_layout_meta_html_content" autocomplete="off"
						name="_wpv_layout_settings[layout_meta_html]"><?php
					echo ( isset( $view_layout_settings['layout_meta_html'] ) ) ? esc_textarea( $view_layout_settings['layout_meta_html'] ) : '';
				?></textarea>
				
				<?php
					wpv_formatting_help_layout();
				?>
			</div>
			
			<ul id="wpv-layout-meta-html-extra" class="wpv-layout-meta-html-extra js-wpv-layout-meta-html-extra">
				<li class="wpv-has-itembox-header js-wpv-layout-meta-html-extra-item js-wpv-layout-meta-html-extra-css">
					<?php 
					$layout_extra_css = isset( $view_settings['layout_meta_html_css'] ) ? $view_settings['layout_meta_html_css'] : '';
					?>
					<div class="wpv-layout-meta-html-extra-header wpv-itembox-header">
						<strong>
							<?php
							_e( 'CSS editor', 'wpv-views' );
							?>
						</strong>
						<button class="button button-secondary button-small wpv-code-editor-toggler js-wpv-code-editor-toggler" data-kind="css" data-target="layout-css-editor">
							<i class="icon-pushpin js-wpv-textarea-full" style="<?php if ( empty( $layout_extra_css ) ) { echo ' display:none;'; } ?>"></i>
							<span class="js-wpv-text-holder"><?php _e( 'Open CSS editor', 'wpv-views' ) ?></span>
						</button>
					</div>
					<div class="code-editor layout-css-editor wpv-code-editor-closed js-wpv-code-editor js-wpv-layout-css-editor js-wpv-code-editor-closed hidden">
						<div class="code-editor-toolbar js-code-editor-toolbar">
							<ul>
								<li>
									<button class="button-secondary js-code-editor-toolbar-button js-wpv-media-manager" data-id="<?php echo esc_attr( $view_id ); ?>" data-content="wpv_layout_meta_html_css">
										<i class="icon-picture"></i>
										<span class="button-label"><?php _e('Media','wpv-views'); ?></span>
									</button>
								</li>
							</ul>
						</div>
						<textarea cols="30" rows="10" id="wpv_layout_meta_html_css" autocomplete="off" name="_wpv_settings[layout_meta_html_css]"><?php echo esc_textarea( $layout_extra_css ); ?></textarea>
						<?php
						wpv_formatting_help_extra_css( 'layout' );
						?>
					</div>
				</li>
				<li class="wpv-has-itembox-header js-wpv-layout-meta-html-extra-item js-wpv-layout-meta-html-extra-js">
					<?php 
					$layout_extra_js = isset( $view_settings['layout_meta_html_js'] ) ? $view_settings['layout_meta_html_js'] : '';
					?>
					<div class="wpv-layout-meta-html-extra-header wpv-itembox-header">
						<strong>
							<?php
							_e( 'JS editor', 'wpv-views' );
							?>
						</strong>
						<button class="button button-secondary button-small wpv-code-editor-toggler js-wpv-code-editor-toggler" data-kind="js" data-target="layout-js-editor">
							<i class="icon-pushpin js-wpv-textarea-full" style="<?php if ( empty( $layout_extra_js ) ) { echo ' display:none;'; } ?>"></i>
							<span class="js-wpv-text-holder"><?php _e( 'Open JS editor', 'wpv-views' ) ?></span>
						</button>
					</div>
					<div class="code-editor layout-js-editor wpv-code-editor-closed js-wpv-code-editor js-wpv-layout-js-editor js-wpv-code-editor-closed hidden">
						<div class="code-editor-toolbar js-code-editor-toolbar">
							<ul>
								<li>
									<button class="button-secondary js-code-editor-toolbar-button js-wpv-media-manager" data-id="<?php echo esc_attr( $view_id ); ?>" data-content="wpv_layout_meta_html_js">
										<i class="icon-picture"></i>
										<span class="button-label"><?php _e('Media','wpv-views'); ?></span>
									</button>
								</li>
							</ul>
						</div>
						<textarea cols="30" rows="10" id="wpv_layout_meta_html_js" autocomplete="off" name="_wpv_settings[layout_meta_html_js]"><?php echo esc_textarea( $layout_extra_js ); ?></textarea>
						<?php
						wpv_formatting_help_extra_js( 'layout' );
						?>
					</div>
				</li>
			</ul>

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
	// Get View settings and layout settings
	$view_array = get_post_meta( $_POST["id"], '_wpv_settings', true );
    $view_layout_array = get_post_meta( $_POST["id"], '_wpv_layout_settings', true );
    // Save the wizard settings
    if ( isset( $_POST['style'] ) ) {
        $view_layout_array['style'] = sanitize_text_field( $_POST['style'] );
        $view_layout_array['table_cols'] = sanitize_text_field( $_POST['table_cols'] );
		$view_layout_array['bootstrap_grid_cols'] = sanitize_text_field( $_POST['bootstrap_grid_cols'] );
		$view_layout_array['bootstrap_grid_container'] = sanitize_text_field( $_POST['bootstrap_grid_container'] );
		$view_layout_array['bootstrap_grid_row_class'] = sanitize_text_field( $_POST['bootstrap_grid_row_class'] );
		$view_layout_array['bootstrap_grid_individual'] = sanitize_text_field( $_POST['bootstrap_grid_individual'] );
        $view_layout_array['include_field_names'] = sanitize_text_field( $_POST['include_field_names'] );
        $view_layout_array['fields'] = $_POST['fields'];// @todo sanitize this
        $view_layout_array['real_fields'] = $_POST['real_fields'];// @todo sanitize this
        //Remove unused Content Template
        if ( 
			isset( $_POST['delete_view_loop_template'] ) 
			&& ! empty( $_POST['delete_view_loop_template'] ) 
		) {
            wp_delete_post( intval( $_POST['delete_view_loop_template'] ), true );
            delete_post_meta( intval( $_POST["id"] ), '_view_loop_template' );
            if ( isset( $view_layout_array['included_ct_ids'] ) ) {
                $reg_templates = array();
                $reg_templates = explode( ',', $view_layout_array['included_ct_ids'] );
                if ( in_array( $_POST['delete_view_loop_template'], $reg_templates ) ) {
                    $delete_key = array_search( $_POST['delete_view_loop_template'], $reg_templates );
					unset( $reg_templates[$delete_key] );
                    $view_layout_array['included_ct_ids'] = implode( ',', $reg_templates );
                }
            }
        }        
    }
	$view_layout_array['layout_meta_html'] = $_POST["layout_val"];
	wpv_register_wpml_strings( $_POST["layout_val"] );
	$view_array['layout_meta_html_css'] = $_POST["layout_css_val"];
	$view_array['layout_meta_html_js'] = $_POST["layout_js_val"];
	update_post_meta( $_POST["id"], '_wpv_settings', $view_array );
	update_post_meta( $_POST["id"], '_wpv_layout_settings', $view_layout_array );
	do_action( 'wpv_action_wpv_save_item', $_POST["id"] );
	$data = array(
		'id' => $_POST["id"],
		'message' => __( 'Loop Output saved', 'wpv-views' )
	);
	wp_send_json_success( $data );
}