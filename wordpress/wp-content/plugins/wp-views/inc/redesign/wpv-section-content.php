<?php

add_filter('wpv_sections_layout_show_hide', 'wpv_show_hide_content', 1,1);

function wpv_show_hide_content($sections) {
	$sections['content'] = array(
		'name'		=> __('Combined Output', 'wpv-views'),
		);
	return $sections;
}

add_action('view-editor-section-extra', 'add_view_content', 10, 2);

function add_view_content($view_settings, $view_id) {
    global $views_edit_help;
	$hide = '';
	if (isset($view_settings['sections-show-hide']) && isset($view_settings['sections-show-hide']['content']) && 'off' == $view_settings['sections-show-hide']['content']) {
		$hide = ' hidden';
	}
	?>
	<div class="wpv-setting-container wpv-setting-container-horizontal wpv-settings-complete-output js-wpv-settings-content<?php echo $hide; ?>">

		<div class="wpv-settings-header">
			<h3>
				<?php _e( 'Combined Output', 'wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip" data-header="<?php echo $views_edit_help['complete_output']['title']; ?>" data-content="<?php echo $views_edit_help['complete_output']['content']; ?>"></i>
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
						<li class="wpv-vicon-codemirror-button">
							<?php wpv_add_v_icon_to_codemirror( 'wpv_content' ); ?>
						</li>
						<?php wpv_add_cred_to_codemirror('wpv_content', 'li'); ?>
						<li>
							<button class="button-secondary js-code-editor-toolbar-button js-wpv-media-manager" data-id="<?php echo $view_id;?>" data-content="wpv_content">
								<i class="icon-picture"></i>
								<span class="button-label"><?php _e('Media','wpv-views'); ?></span>
							</button>
						</li>
					</ul>
				</div>
				<textarea cols="30" rows="10" id="wpv_content" name="_wpv_settings[content]"><?php echo $content; ?></textarea>
			</div>
			<p class="update-button-wrap">
				<button data-success="<?php echo htmlentities( __('Content updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Content not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_content_nonce' ); ?>" class="js-wpv-content-update button-secondary" disabled="disabled"><?php _e('Update', 'wpv-views'); ?></button>
			</p>
		</div>

	</div>
<?php }