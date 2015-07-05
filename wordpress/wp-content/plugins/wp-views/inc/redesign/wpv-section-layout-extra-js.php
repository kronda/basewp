<?php

/*
* We can enable this to hide the Layout Additional JS section
*/

add_filter( 'wpv_sections_layout_show_hide', 'wpv_show_hide_layout_extra_js', 1, 1 );

function wpv_show_hide_layout_extra_js( $sections ) {
	$sections['layout-settings-extra-js'] = array(
		'name' => __( 'Aditional Javascript files', 'wpv-views' ),
	);
	return $sections;
}

add_action( 'view-editor-section-layout', 'add_view_layout_extra_js', 50, 3 );

function add_view_layout_extra_js( $view_settings, $view_layout_settings, $view_id ) {
	$hide = '';
	$js = isset( $view_layout_settings['additional_js'] ) ? strval( $view_layout_settings['additional_js'] ) : '';
	if (
		isset( $view_settings['sections-show-hide'] ) 
		&& isset( $view_settings['sections-show-hide']['layout-settings-extra-js'] ) 
	) {
		if ( 'off' == $view_settings['sections-show-hide']['layout-settings-extra-js'] ) {
			$hide = ' hidden';
		}
	} elseif ( ''== $js ) {
		$hide = ' hidden';
	}
	$section_help_pointer = WPV_Admin_Messages::edit_section_help_pointer( 'layout_extra_js' );
	?>
	<div class="wpv-setting-container wpv-settings-output-extra-js js-wpv-settings-layout-settings-extra-js<?php echo $hide; ?>">
		<div class="wpv-settings-header">
			<h3>
				<?php _e( 'Additional Javascript files','wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip" 
					data-header="<?php echo esc_attr( $section_help_pointer['title'] ); ?>" 
					data-content="<?php echo esc_attr( $section_help_pointer['content'] ); ?>">
				</i>
			</h3>
		</div>
		<div class="wpv-setting">
			<p>
				<label for="wpv-layout-settings-extra-js"><?php _e( 'Additional Javascript files to be loaded with this View (comma separated): ', 'wpv-views' ) ?></label>
				<input type="text" id="wpv-layout-settings-extra-js" autocomplete="off" class="js-wpv-layout-settings-extra-js" name="_wpv_layout_settings[additional_js]" value="<?php echo esc_attr( $js ); ?>" style="width:100%;" />
			</p>
			<p class="update-button-wrap js-wpv-update-button-wrap">
				<span class="js-wpv-message-container"></span>
				<button data-success="<?php echo esc_attr( __('Data saved', 'wpv-views') ); ?>" data-unsaved="<?php echo esc_attr( __('Data not saved', 'wpv-views') ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_layout_settings_extra_js_nonce' ); ?>" class="js-wpv-layout-settings-extra-js-update button-secondary" disabled="disabled"><?php _e('Update', 'wpv-views'); ?></button>
			</p>
		</div>
	</div>
<?php }

// Layout Extra JS save callback function

add_action( 'wp_ajax_wpv_update_layout_extra_js', 'wpv_update_layout_extra_js_callback' );

function wpv_update_layout_extra_js_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		$data = array(
			'type' => 'capability',
			'message' => __( 'You do not have permissions for that.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	if ( 
		! isset( $_POST["wpnonce"] )
		|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_layout_settings_extra_js_nonce' ) 
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
	$view_array = get_post_meta( $_POST["id"], '_wpv_layout_settings', true );
	$view_array['additional_js'] = sanitize_text_field( $_POST["value"] );
	update_post_meta( $_POST["id"], '_wpv_layout_settings', $view_array );
	do_action( 'wpv_action_wpv_save_item', $_POST["id"] );
	$data = array(
		'id' => $_POST["id"],
		'message' => __( 'Additional Javascript saved', 'wpv-views' )
	);
	wp_send_json_success( $data );
}