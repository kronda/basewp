<?php

/*
* We can enable this to hide the Content selection section
*/

add_filter('wpv_sections_layout_show_hide', 'wpv_show_hide_layout_extra', 1,1);

function wpv_show_hide_layout_extra($sections) {
	$sections['layout-extra'] = array(
		'name'		=> __('Layout HTML/CSS/JS', 'wpv-views'),
		);
	return $sections;
}

add_action('view-editor-section-layout', 'add_view_layout_extra', 20, 3);

function add_view_layout_extra($view_settings, $view_layout_settings, $view_id) {
    global $views_edit_help;
	$hide = '';
	if (isset($view_settings['sections-show-hide']) && isset($view_settings['sections-show-hide']['layout-extra']) && 'off' == $view_settings['sections-show-hide']['layout-extra']) {
		$hide = ' hidden';
	}?>
	<div class="wpv-setting-container wpv-setting-container-horizontal wpv-settings-layout-markup js-wpv-settings-layout-extra<?php echo $hide; ?>">

		<div class="wpv-settings-header">
			<h3>
				<?php _e( 'Layout HTML/CSS/JS', 'wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip" data-header="<?php echo $views_edit_help['layout_html_css_js']['title']; ?>" data-content="<?php echo $views_edit_help['layout_html_css_js']['content']; ?>"></i>
			</h3>
		</div>
		<?php
		$data = wpv_get_view_layout_wizard_hint_data();
		wpv_toolset_help_box($data);
		$data = wpv_get_view_content_template_hint_data();
		wpv_toolset_help_box($data);
		?>

		<div class="wpv-setting">
			<div class="js-error-container"></div>
			<div class="js-code-editor code-editor layout-html-editor" data-name="layout-html-editor">
				<div class="code-editor-toolbar js-code-editor-toolbar">
					<ul class="js-wpv-layout-edit-toolbar">
						<li class="wpv-vicon-codemirror-button">
							<?php
							wpv_add_v_icon_to_codemirror( 'wpv_layout_meta_html_content' );
							?>
						</li>
						<?php
						wpv_add_cred_to_codemirror('wpv_layout_meta_html_content', 'li');
						?>
						<?php if ( isset( $view_settings['view-query-mode'] ) && $view_settings['view-query-mode'] == 'normal' ) {
						// we only add the pagination button to the Layout box if the View is not a WPA ?>
						<li class="js-editor-pagination-button-wrapper">
							<button class="button-secondary js-code-editor-toolbar-button js-wpv-pagination-popup" data-content="wpv_layout_meta_html_content">
								<i class="icon-pagination"></i>
								<span class="button-label"><?php _e('Pagination','wpv-views'); ?></span>
							</button>
						</li>
						<?php } ?>
						<li>
							<button class="button-secondary js-code-editor-toolbar-button js-open-meta-html-wizard">
								<i class="icon-th"></i>
								<span class="button-label"><?php _e('Layout wizard','wpv-views'); ?></span>
							</button>
						</li>
						<li>
							<button class="button-secondary js-code-editor-toolbar-button js-wpv-ct-assign-to-view" data-id="<?php echo $view_id;?>">
								<i class="icon-paste"></i>
								<span class="button-label"><?php _e('Content Template','wpv-views'); ?></span>
							</button>
						</li>
						<li>
							<button class="button-secondary js-code-editor-toolbar-button js-wpv-media-manager" data-id="<?php echo $view_id;?>" data-content="wpv_layout_meta_html_content">
								<i class="icon-picture"></i>
								<span class="button-label"><?php _e('Media','wpv-views'); ?></span>
							</button>
						</li>
					</ul>
				</div>
				<textarea cols="30" rows="10" id="wpv_layout_meta_html_content" name="_wpv_layout_settings[layout_meta_html]"><?php echo ( isset( $view_layout_settings['layout_meta_html'] ) ) ? $view_layout_settings['layout_meta_html'] : ''; ?></textarea>
			</div>
			
			<?php
			$layout_extra_css = isset( $view_settings['layout_meta_html_css'] ) ? $view_settings['layout_meta_html_css'] : '';
			if ( empty( $layout_extra_css ) ) {
				$aux_class = ' code-editor-textarea-empty';
			} else {
				$aux_class = ' code-editor-textarea-full';
			}
			?>

			<p class="js-wpv-layout-css-editor-old-place">
				<input type="hidden" name="_wpv_settings[layout_meta_html_state][css]" id="wpv_layout_meta_html_extra_css_state" value="<?php echo isset($view_settings['layout_meta_html_state']['css']) ? $view_settings['layout_meta_html_state']['css'] : 'off'; ?>" />
				<button class="button-secondary js-code-editor-button layout-css-editor-button<?php echo $aux_class; ?>" data-target="layout-css-editor" data-state="closed" data-closed="<?php echo htmlentities( __( 'Open CSS editor', 'wpv-views' ), ENT_QUOTES ); ?>" data-opened="<?php echo htmlentities( __( 'Close CSS editor', 'wpv-views' ), ENT_QUOTES ); ?>">
					<?php _e( 'Open CSS editor', 'wpv-views' ) ?>
				</button>
			</p>

			<div class="js-code-editor code-editor layout-css-editor closed" data-name="layout-css-editor">
				<div class="code-editor-toolbar js-code-editor-toolbar">
					<ul>
						<li>
							<button class="button-secondary js-code-editor-toolbar-button js-wpv-media-manager" data-id="<?php echo $view_id;?>" data-content="wpv_layout_meta_html_css">
								<i class="icon-picture"></i>
								<span class="button-label"><?php _e('Media','wpv-views'); ?></span>
							</button>
						</li>
					</ul>
				</div>
				<textarea cols="30" rows="10" id="wpv_layout_meta_html_css" name="_wpv_settings[layout_meta_html_css]"><?php echo $layout_extra_css; ?></textarea>
			</div>
			
			<?php
			$layout_extra_js = isset( $view_settings['layout_meta_html_js'] ) ? $view_settings['layout_meta_html_js'] : '';
			if ( empty( $layout_extra_js ) ) {
				$aux_class = ' code-editor-textarea-empty';
			} else {
				$aux_class = ' code-editor-textarea-full';
			}
			?>

			<p class="js-wpv-layout-js-editor-old-place">
				<input type="hidden" name="_wpv_settings[layout_meta_html_state][js]" id="wpv_layout_meta_html_extra_js_state" value="<?php echo isset($view_settings['layout_meta_html_state']['js']) ? $view_settings['layout_meta_html_state']['js'] : 'off'; ?>" />
				<button class="button-secondary js-code-editor-button layout-js-code-editor-button<?php echo $aux_class; ?>" data-target="layout-js-editor" data-state="closed" data-closed="<?php echo htmlentities( __( 'Open JS editor', 'wpv-views' ), ENT_QUOTES ); ?>" data-opened="<?php echo htmlentities( __( 'Close JS editor', 'wpv-views' ), ENT_QUOTES ); ?>">
					<?php _e( 'Open JS editor', 'wpv-views' ) ?>
				</button>
			</p>

			<div class="js-code-editor code-editor layout-js-editor closed" data-name="layout-js-editor">
				<div class="code-editor-toolbar js-code-editor-toolbar">
					<ul>
						<li>
							<button class="button-secondary js-code-editor-toolbar-button js-wpv-media-manager" data-id="<?php echo $view_id;?>" data-content="wpv_layout_meta_html_js">
								<i class="icon-picture"></i>
								<span class="button-label"><?php _e('Media','wpv-views'); ?></span>
							</button>
						</li>
					</ul>
				</div>
				<textarea cols="30" rows="10" id="wpv_layout_meta_html_js" name="_wpv_settings[layout_meta_html_js]"><?php echo $layout_extra_js; ?></textarea>
			</div>

			<p class="update-button-wrap">
				<button data-success="<?php echo htmlentities( __('Data updated', 'wpv-views'),ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Data not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_layout_extra_nonce' ); ?>" class="js-wpv-layout-extra-update button-secondary" disabled="disabled">
					<?php _e('Update', 'wpv-views'); ?>
				</button>
			</p>

		</div>

	</div>
<?php }