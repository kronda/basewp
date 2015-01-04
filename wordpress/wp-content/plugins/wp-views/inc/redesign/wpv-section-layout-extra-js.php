<?php

/*
* We can enable this to hide the Layout Additional JS section
*/

add_filter('wpv_sections_layout_show_hide', 'wpv_show_hide_layout_extra_js', 1,1);

function wpv_show_hide_layout_extra_js($sections) {
	$sections['layout-settings-extra-js'] = array(
		'name'		=> __('Aditional Javascript files', 'wpv-views'),
		);
	return $sections;
}

add_action('view-editor-section-layout', 'add_view_layout_extra_js', 50, 3);

function add_view_layout_extra_js($view_settings, $view_layout_settings, $view_id) {
	$hide = '';
	$show = true;
	$js = isset($view_layout_settings['additional_js']) ? strval($view_layout_settings['additional_js']) : '';
	if (isset($view_settings['sections-show-hide']) && isset($view_settings['sections-show-hide']['layout-settings-extra-js'])) {
		if ('off' == $view_settings['sections-show-hide']['layout-settings-extra-js']) {
			$hide = ' hidden';
		}
	}
	elseif (''== $js) {
		$show = false;
	}
	if ($show) {
	?>
	<div class="wpv-setting-container wpv-settings-output-extra-js js-wpv-settings-layout-settings-extra-js<?php echo $hide; ?>">
		<div class="wpv-settings-header">
			<h3>
				<?php _e( 'Additional Javascript files','wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip" data-header="<?php _e('Pointer header','wpv-views') ?>" data-content="<?php _e('Tooltip content. Lorem ipsum dolor et umni der lanos','wpv-views') ?>"></i>
			</h3>
		</div>
		<div class="wpv-setting">
			<p>
				<label for="wpv-layout-settings-extra-js"><?php _e( 'Additional Javascript files to be loaded with this View (comma separated): ', 'wpv-views' ) ?></label>
				<input type="text" id="wpv-layout-settings-extra-js" class="js-wpv-layout-settings-extra-js" name="_wpv_layout_settings[additional_js]" value="<?php echo $js; ?>" style="width:100%;" />
			</p>
			<p class="update-button-wrap">
				<button data-success="<?php echo htmlentities( __('Data saved', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Data not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_layout_settings_extra_js_nonce' ); ?>" class="js-wpv-layout-settings-extra-js-update button-secondary" disabled="disabled"><?php _e('Update', 'wpv-views'); ?></button>
			</p>
		</div>
	</div>
	<?php } ?>
<?php }