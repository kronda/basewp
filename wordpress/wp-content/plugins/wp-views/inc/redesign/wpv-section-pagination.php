<?php

/*
* We can enable this to hide the Pagination section
*/

add_filter('wpv_sections_filter_show_hide', 'wpv_show_hide_pagination', 1,1);

function wpv_show_hide_pagination($sections) {
	$sections['pagination'] = array(
		'name'		=> __('Pagination and Sliders settings', 'wpv-views'),
		);
	return $sections;
}

add_action('view-editor-section-filter', 'add_view_pagination', 10, 2);

function add_view_pagination($view_settings, $view_id) { //TODO review that default values are set before we display any of this
    global $views_edit_help;
	$rollover_effects = array(
		'fade' => __('Fade', 'wpv-views'),
	//	'fadefast' => __('Fade fast', 'wpv-views'),
	//	'fadeslow' => __('Fade slow', 'wpv-views'),
		'slideleft' => __('Slide Left', 'wpv-views'),
		'slideright' => __('Slide Right', 'wpv-views'),
		'slideup' => __('Slide Up', 'wpv-views'),
		'slidedown' => __('Slide Down', 'wpv-views'),
	);
	$ajax_effects = array(
		'fade' => __('Fade', 'wpv-views'),
		'fadefast' => __('Fade fast', 'wpv-views'),
		'fadeslow' => __('Fade slow', 'wpv-views'),
		'slideh' => __('Slide horizontally', 'wpv-views'),
		'slidev' => __('Slide vertically', 'wpv-views'),
	);
	$hide = '';
	if (isset($view_settings['sections-show-hide']) && isset($view_settings['sections-show-hide']['pagination']) && 'off' == $view_settings['sections-show-hide']['pagination']) {
		$hide = ' hidden';
	}?>
	<div class="wpv-setting-container wpv-settings-pagination js-wpv-settings-pagination<?php echo $hide; ?>">
		<div class="wpv-settings-header">
			<h3>
				<?php _e( 'Pagination and Sliders settings', 'wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip" data-header="<?php echo $views_edit_help['pagination_and_sliders_settings']['title']; ?>" data-content="<?php echo $views_edit_help['pagination_and_sliders_settings']['content']; ?>"></i>
			</h3>
		</div>
		<div class="wpv-setting">
			<form class="js-pagination-settings-form">
				<?php
				if ( !isset( $view_settings['pagination'][0] ) ) $view_settings['pagination'][0] = 'disable';
				if ( !isset( $view_settings['pagination']['mode'] ) ) $view_settings['pagination']['mode'] = 'none';
				?>
				<input type="hidden" class="js-pagination-zero" name="pagination[]" value="<?php echo $view_settings['pagination'][0]; ?>" />
				<ul>
					<li>
						<?php $checked = $view_settings['pagination'][0]=='disable' ? ' checked="checked"' : ''; ?>
						<input type="radio" id="wpv-settings-no-pagination" name="pagination[mode]" value="none"<?php echo $checked; ?> />
						<label for="wpv-settings-no-pagination"><strong><?php _e('No pagination','wpv-views') ?></strong> - <?php _e('All query results will display.','wpv-views') ?></label>
					</li>
					<li>
						<?php $checked = ( $view_settings['pagination'][0]=='enable' && $view_settings['pagination']['mode']=='paged' ) ? ' checked="checked"' : ''; ?>
						<input type="radio" id="wpv-settings-manual-pagination" name="pagination[mode]" value="paged"<?php echo $checked; ?> />
						<label for="wpv-settings-manual-pagination"><strong><?php _e( 'Pagination enabled with manual transition', 'wpv-views' ) ?></strong> - <?php _e( 'The query results will display in pages, which visitors will switch.', 'wpv-views' ) ?></label>
					</li>
					<li>
						<?php $checked = $view_settings['pagination']['mode']=='rollover' ? ' checked="checked"' : ''; ?>
						<input type="radio" id="wpv-settings-ajax-pagination" name="pagination[mode]" value="rollover"<?php echo $checked; ?> />
						<label for="wpv-settings-ajax-pagination"><strong><?php _e( 'Pagination enabled with automatic transition', 'wpv-views' ) ?></strong> - <?php _e( 'The query results will display in pages, which will switch automatically (good for sliders).', 'wpv-views' ) ?></label>
					</li>
				</ul>

				<div class="wpv-setting-options-box wpv-pagination-options-box">

					<h3 class="wpv-pagination-paged"><?php _e('Options for manual pagination','wpv-views'); ?></h3>
					<ul class="wpv-pagination-paged">
						<li>
							<label><?php _e('Number of items per page:', 'wpv-views')?></label>
							<select name="posts_per_page">
								<?php if ( !isset( $view_settings['posts_per_page'] ) ) $view_settings['posts_per_page'] = '10'; ?>
								<?php
								for($i = 1; $i < 50; $i++) {
									$selected = $view_settings['posts_per_page']==(string)$i ? ' selected="selected"' : '';
									echo '<option value="' . $i . '"' . $selected . '>'. $i . '</option>';
								}
								?>
							</select>
						</li>
						<li>
							<?php $checked = ( isset( $view_settings['ajax_pagination'][0] ) && $view_settings['ajax_pagination'][0] == 'disable') ? ' checked="checked"' : ''; ?>
							<input type="radio" id="wpv-settings-ajax-pagination-disabled" value="disable" name="ajax_pagination[]"<?php echo $checked; ?> />
							<label for="wpv-settings-ajax-pagination-disabled"><?php _e('Pagination updates the entire page', 'wpv-views'); ?></label>
						</li>
						<li>
							<?php $checked = ( isset( $view_settings['ajax_pagination'][0] ) && $view_settings['ajax_pagination'][0] == 'enable') ? ' checked="checked"' : ''; ?>
							<input type="radio" id="wpv-settings-ajax-pagination-enabled" value="enable" name="ajax_pagination[]"<?php echo $checked; ?> />
							<label for="wpv-settings-ajax-pagination-enabled"><?php _e('Pagination updates only the view (use AJAX)', 'wpv-views'); ?></label>
						</li>
					</ul>

					<ul class="wpv-pagination-paged-ajax" style="margin-bottom:0;">
						<li>
							<p>
								<label><?php _e('Transition effect:', 'wpv-views')?></label>
								<select name="ajax_pagination[style]">
									<?php if ( !isset( $view_settings['ajax_pagination']['style'] ) ) $view_settings['ajax_pagination']['style'] = 'fade'; ?>
									<option value="fade"<?php if ($view_settings['ajax_pagination']['style'] == 'fade' || $view_settings['ajax_pagination']['style'] == 'fadefast' || $view_settings['ajax_pagination']['style'] == 'fadeslow') { echo ' selected="selected"'; } ?>><?php _e('Fade',  'wpv-views'); ?></option>
									<option value="slideh"<?php if ($view_settings['ajax_pagination']['style'] == 'slideh') { echo ' selected="selected"'; } ?>><?php _e('Slide horizontally',  'wpv-views'); ?></option>
									<option value="slidev"<?php if ($view_settings['ajax_pagination']['style'] == 'slidev') { echo ' selected="selected"'; } ?>><?php _e('Slide vertically',  'wpv-views'); ?></option>
								</select>

								<label>
									<?php _e('with duration',  'wpv-views'); ?>
									<?php if ( !isset( $view_settings['ajax_pagination']['duration'] ) ) $view_settings['ajax_pagination']['duration'] = 500;
										if ($view_settings['ajax_pagination']['style'] == 'fadefast') $view_settings['ajax_pagination']['duration'] = 1;
										if ($view_settings['ajax_pagination']['style'] == 'fadeslow') $view_settings['ajax_pagination']['duration'] = 1500;
									?>
									<input type="text" class="transition-duration" name="ajax_pagination[duration]" value="<?php echo $view_settings['ajax_pagination']['duration']; ?>" size="5">
								</label>
								<?php _e('miliseconds', 'wpv-views'); ?>
								<span class="duration-error" style="color:red;display:none;">&larr; <?php _e('Please add a numeric value', 'wpv-views'); ?></span>
							</p>
							<p>
								<button class="js-pagination-advanced button-secondary" type="button" data-closed="<?php echo esc_attr( __( 'Advanced options', 'wpv-views' ) ); ?>" data-opened="<?php echo esc_attr( __( 'Close advanced options', 'wpv-views' ) ); ?>" data-section="ajax_pagination" data-state="closed"><?php _e( 'Advanced options', 'wpv-views' ); ?></button>
							</p>
						</li>
						<li class="wpv-pagination-advanced wpv-advanced-setting hidden" style="padding-top:10px;">
							<?php $checked = (isset($view_settings['pagination']['preload_images']) && $view_settings['pagination']['preload_images']) ? ' checked="checked"' : '';?>
							<label>
								<input type="checkbox" name="pagination[preload_images]" value="1"<?php echo $checked; ?> />
								<?php _e('Preload images before transition',  'wpv-views'); ?>
							</label>
						</li>
					</ul>

					<h3 class="wpv-pagination-rollover"><?php _e('Options for automatic pagination', 'wpv-views')?></h3>
					<ul class="wpv-pagination-rollover" style="margin-bottom:0;">
						<li>
							<label for="rollover[posts_per_page]"><?php _e('Number of items per page:', 'wpv-views'); ?></label>
							<select name="rollover[posts_per_page]">
								<?php if ( !isset( $view_settings['rollover']['posts_per_page'] ) ) $view_settings['rollover']['posts_per_page'] = '10'; ?>
								<?php
								for($i = 1; $i < 50; $i++) {
									$selected = $view_settings['rollover']['posts_per_page']==(string)$i ? ' selected="selected"' : '';
									echo '<option value="' . $i . '"' . $selected . '>'. $i . '</option>';
								}
								?>
							</select>
						</li>
						<li>
							<label><?php _e('Show each page for:', 'wpv-views')?></label>
							<select name="rollover[speed]">
								<?php if ( !isset( $view_settings['rollover']['speed'] ) ) $view_settings['rollover']['speed'] = '5'; ?>
								<?php
								for($i = 1; $i < 20; $i++) {
									$selected = $view_settings['rollover']['speed']==(string)$i ? ' selected="selected"' : '';
									echo '<option value="' . $i . '"' . $selected . '>'. $i . '</option>';
								}
								?>
							</select>&nbsp;<?php _e('seconds', 'wpv-views')?>
						</li>
						<li>
							<label><?php _e('Transition effect:', 'wpv-views')?></label>
							<select name="rollover[effect]">
								<?php
								if ( !isset( $view_settings['rollover']['effect'] ) ) $view_settings['rollover']['effect'] = 'fade';
								foreach($rollover_effects as $i => $title) {
									$selected = $view_settings['rollover']['effect']==(string)$i ? ' selected="selected"' : '';
									echo '<option value="' . $i . '"' . $selected . '>'. $title . '</option>';
								}
								?>
							</select>
							<label><?php _e('with duration',  'wpv-views'); ?></label>
								<?php if ( !isset( $view_settings['rollover']['duration'] ) ) $view_settings['rollover']['duration'] = 500;
								?>
								<input type="text" class="transition-duration" name="rollover[duration]" value="<?php echo $view_settings['rollover']['duration']; ?>" size="5">
							<?php _e('miliseconds', 'wpv-views'); ?>
							<span class="duration-error" style="color:red;display:none;"><?php _e(' <- Please add a numeric value', 'wpv-views'); ?></span>
							<p>
								<button class="js-pagination-advanced button-secondary" type="button" data-closed="<?php _e( 'Advanced options', 'wpv-views' ) ?>" data-opened="<?php _e( 'Close advanced options', 'wpv-views' ) ?>" data-section="rollover" data-state="closed"><?php _e( 'Advanced options', 'wpv-views' ) ?></button>
							</p>
						</li>
						<li class="wpv-pagination-advanced wpv-advanced-setting hidden" style="padding-top:10px;">
							<?php $checked = (isset($view_settings['rollover']['preload_images']) && $view_settings['rollover']['preload_images']) ? ' checked="checked"' : '';?>
							<label>
								<input type="checkbox" name="rollover[preload_images]" value="1"<?php echo $checked; ?> />
								<?php _e('Preload images before transition',  'wpv-views'); ?>
							</label>
						</li>
					</ul>

					<ul class="wpv-pagination-paged wpv-pagination-rollover wpv-pagination-shared wpv-pagination-advanced wpv-advanced-setting hidden" style="padding-bottom:10px;">
						<li>
							<?php $checked = (isset($view_settings['pagination']['cache_pages']) && $view_settings['pagination']['cache_pages']) ? ' checked="checked"' : '';?>
							<p>
								<label>
									<input type="checkbox" name="pagination[cache_pages]" value="1"<?php echo $checked; ?> />
									<?php _e('Cache pages',  'wpv-views'); ?>
								</label>
							</p>
						</li>
						<li>
							<?php $checked = (isset($view_settings['pagination']['preload_pages']) && $view_settings['pagination']['preload_pages']) ? ' checked="checked"' : '';?>
							<p>
								<label>
									<input type="checkbox" name="pagination[preload_pages]" value="1"<?php echo $checked; ?> />
									<?php _e('Pre-load the next and previous pages - avoids loading delays when users move between pages',  'wpv-views'); ?>
								</label>
							</p>

							<p>
								<label><?php _e('Pages to pre-load: ',  'wpv-views'); ?></label>
								<select name="pagination[pre_reach]">
								<?php if ( !isset( $view_settings['pagination']['pre_reach'] ) ) $view_settings['pagination']['pre_reach'] = 1;
									for($i = 1; $i < 20; $i++) {
										$selected = $view_settings['pagination']['pre_reach']== $i ? ' selected="selected"' : '';
										echo '<option value="' . $i . '"' . $selected . '>'. $i . '</option>';
									}
									?>
								</select>
							</p>
						</li>
						<li>
							<h4><?php _e('Spinners',  'wpv-views'); ?></h4>
							<?php $checked = ( isset($view_settings['pagination']['spinner']) && $view_settings['pagination']['spinner'] == 'default' ) ? ' checked="checked"' : '';?>


							<p>
								<label>
									<input type="radio" name="pagination[spinner]" value="default"<?php echo $checked; ?> />
									<?php _e('Spinner graphics from Views', 'wpv-views'); ?>
								</label>
							</p>

							<ul id="wpv-spinner-default" class="wpv-spinner-selection">
								<?php
								if ( isset( $view_settings['pagination']['spinner_image'] ) ) {
									$spinner_image = $view_settings['pagination']['spinner_image'];
								} else {
									$spinner_image = '';
								}
								foreach (glob(WPV_PATH_EMBEDDED . "/res/img/ajax-loader*") as $file) {
								$filename = WPV_URL_EMBEDDED . '/res/img/' . basename($file);
								$filename2 = WPV_URL . '/res/img/' . basename($file);
								?>
								<li>
									<label>
										<input type="radio" name="pagination[spinner_image]" value="<?php echo $filename; ?>"<?php if ( $spinner_image == $filename || $spinner_image == $filename2) { echo ' checked="checked"'; } ?> />
										<img style="background-color: #FFFFFF;" src="<?php echo $filename; ?>" title="<?php echo $filename; ?>" />
									</label>
								</li>
								<?php } ?>
							</ul>

							<?php $checked = ( isset($view_settings['pagination']['spinner']) && $view_settings['pagination']['spinner'] == 'uploaded' ) ? ' checked="checked"' : '';?>
							<p>
								<label>
									<input type="radio" name="pagination[spinner]" value="uploaded"<?php echo $checked; ?> />
									<?php _e('My custom spinner graphics', 'wpv-views'); ?>
								</label>
							</p>

							<p id="wpv-spinner-uploaded" class="wpv-spinner-selection">
								<input id="wpv-pagination-spinner-image" class="js-wpv-pagination-spinner-image" type="text" name="pagination[spinner_image_uploaded]" value="<?php echo isset( $view_settings['pagination']['spinner_image_uploaded'] ) ? $view_settings['pagination']['spinner_image_uploaded'] : ''; ?>" />
								<button class="button-secondary js-code-editor-toolbar-button js-wpv-media-manager" data-content="wpv-pagination-spinner-image" data-id="<?php echo $view_id; ?>"><?php _e('Upload Image', 'wpv-views'); ?></button>
								<?php if ( isset( $view_settings['pagination']['spinner_image_uploaded'] ) && !empty( $view_settings['pagination']['spinner_image_uploaded'] ) ): ?>
									<img id="wpv-pagination-spinner-image-preview" class="js-wpv-pagination-spinner-image-preview" src="<?php echo $view_settings['pagination']['spinner_image_uploaded']; ?>" height="16" />
								<?php endif; ?>
							</p>

							<?php $checked = ( isset($view_settings['pagination']['spinner']) &&  $view_settings['pagination']['spinner'] == 'no' ) ? ' checked="checked"' : '';?>
							<p>
								<label>
									<input type="radio" name="pagination[spinner]" value="no"<?php echo $checked; ?> />
									<?php _e('No spinner graphics', 'wpv-views'); ?>
								</label>
							</p>
						</li>
						<li>
							<h4><?php _e('Callback function', 'wpv-views'); ?></h4>
							<p><?php _e('Javascript function to execute after the pagination transition has been completed:', 'wpv-views'); ?> <input id="wpv-pagination-callback-next" class="js-wpv-pagination-callback-next" type="text" name="pagination[callback_next]" value="<?php echo isset( $view_settings['pagination']['callback_next'] ) ? $view_settings['pagination']['callback_next'] : ''; ?>" /></p>
						</li>
					</ul>
				</div> <!-- .ggwpv-pagination-options-box -->

			</form>

			<p class="update-button-wrap">
				<button data-success="<?php echo htmlentities( __('Pagination settings updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Pagination settings not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_pagination_nonce' ); ?>" class="js-wpv-pagination-update button-secondary" disabled="disabled"><?php _e('Update', 'wpv-views'); ?></button>
			</p>
		</div>

	<?php
		wpv_get_view_pagination_hint_data();
		$data = wpv_get_view_pagination_hint_result_data();
		wpv_toolset_help_box($data);
	?>

	</div>


	<div class="popup-window-container"> <!-- Use this element as a container for all popup windows. This element is hidden. -->

		<div class="wpv-dialog wpv-dialog-pagination-wizard js-pagination-form-dialog">

			<div class="wpv-dialog-header">
				<h2><?php _e('Would you like to insert transition controls for the pagination?','wpv-views') ?></h2>
				<i class="icon-remove js-dialog-close"></i>
			</div>

			<div class="wpv-dialog-sidebar filter-preview">
				<?php include('wpv-section-pagination-popup-preview.php'); ?>
				<?php // TODO @Juan. I don't know is it a correct way of including re-usable parts of code. ?>
			</div>

			<div class="wpv-dialog-content">

				<h3><?php _e('Pagination controls', 'wpv-views'); ?></h3>
				<p>
					<input type="radio" name="pagination_control" id="pagination-include-page-num" value="page_num" data-target="current-page-number" />
					<label for="pagination-include-page-num"><?php _e('Current page number','wpv-views'); ?></label>
				</p>
				<p>
					<input type="radio" name="pagination_control" id="pagination-include-page-total" value="page_total" data-target="total-pages" />
					<label for="pagination-include-page-total"><?php _e('Number of pages','wpv-views'); ?></label>
				</p>
				<p>
					<input type="radio" name="pagination_control" id="pagination-include-page-selector" value="page_selector" data-target="page-selector"/>
					<label for="pagination-include-page-selector"><?php _e('Page selector using','wpv-views'); ?></label>
					<select name="pagination_controls_type" id="pagination-controls-type" class="js-pagination-control-type">
						<option value="drop_down"><?php _e('dropdown','wpv-views') ?></option>
						<option value="link"><?php _e('links','wpv-views') ?></option>
					</select>
				</p>
				<p>
					<input type="radio" name="pagination_control" id="pagination-include-controls" value="page_controls" data-target="next-previous-controls" />
					<label for="pagination-include-controls"><?php _e('Next and previous page controls','wpv-views'); ?></label>
				</p>

				<h3><?php _e('Pagination display', 'wpv-views'); ?></h3>
				<p>
					<input type="checkbox" name="pagination_display" id="pagination-include-wrapper" />
					<label for="pagination-include-wrapper"><?php _e('Don\'t show pagination controls if there is only one page','wpv-views'); ?></label>
				</p>

			</div>

			<div class="wpv-dialog-footer">
				<button class="button js-dialog-close"><?php _e('Cancel','wpv-views') ?></button>
				<button class="button button-primary js-insert-pagination"><?php _e('Insert pagination','wpv-views') ?></button>
			</div>

		</div> <!-- .wpv-dialog-pagination-wizard -->

		<div class="wpv-dialog wpv-dialog-pagination-wizard js-pagination-form-hint">
			<div class="wpv-dialog-header">
				<h2><?php _e('What pagination controls would you like to insert?','wpv-views') ?></h2>
				<i class="icon-remove js-dialog-close"></i>
			</div>
			<div class="wpv-dialog-sidebar filter-preview">
				<?php include('wpv-section-pagination-popup-preview.php'); ?>
			</div>
			<div class="wpv-dialog-content">
				<p>
					<input type="checkbox" checked="checked" id="pagination-include-page-num-hint" name="pagination-include-page-num" class="js-pagination-control-hint" value="page_num" data-target="current-page-number"  />
					<label for="pagination-include-page-num-hint"><?php _e('Current page number','wpv-views'); ?></label>
				</p>
				<p>
					<input type="checkbox" checked="checked" id="pagination-include-page-total-hint" name="pagination-include-page-total" class="js-pagination-control-hint" value="page_total" data-target="total-pages" />
					<label for="pagination-include-page-total-hint"><?php _e('Number of pages','wpv-views'); ?></label>
				</p>
				<p>
					<input type="checkbox" checked="checked" id="pagination-include-page-selector-hint" name="pagination-include-page-selector" class="js-pagination-control-hint" value="page_selector" data-target="page-selector" />
					<label for="pagination-include-page-selector-hint"><?php _e('Page selector using','wpv-views'); ?></label>
					<select name="pagination_controls_type_hint" id="pagination-controls-type-hint" class="js-pagination-control-hint-type">
						<option value="drop_down"><?php _e('dropdown','wpv-views') ?></option>
						<option value="link"><?php _e('links','wpv-views') ?></option>
					</select>
				</p>
				<p>
					<input type="checkbox" checked="checked" id="pagination-include-controls-hint" name="pagination-include-controls" class="js-pagination-control-hint" value="page_controls" data-target="next-previous-controls" />
					<label for="pagination-include-controls-hint"><?php _e('Next and previous page controls','wpv-views'); ?></label>

				</p>
			</div>
			<div class="wpv-dialog-footer">
				<button class="button js-dialog-close"><?php _e('Cancel','wpv-views') ?></button>
				<button class="button button-primary js-insert-pagination-from-hint" data-content="wpv_filter_meta_html_content"><?php _e('Insert pagination','wpv-views') ?></button>
			</div>
		</div> <!-- wpv-dialog-pagination-wizard -->

	</div>
<?php }

/**
* wpv_pagination_summary_filter
*
* Returns the pagination part when building the summary for a View
*
* @param $summary
* @param $post_id
* @param $view_settings
*
* @returns (string) $summary
*
* @since unknown
*/

add_filter( 'wpv-view-get-content-summary', 'wpv_pagination_summary_filter', 6, 3 );

function wpv_pagination_summary_filter($summary, $post_id, $view_settings) {
	if ( isset( $view_settings['pagination'] ) && isset( $view_settings['pagination'][0] ) && $view_settings['pagination'][0] != 'disable' ) {
		if ( isset( $view_settings['pagination']['mode'] ) && $view_settings['pagination']['mode'] == 'paged' ) {
			if ( isset( $view_settings['ajax_pagination'] ) && isset( $view_settings['ajax_pagination'][0] ) && $view_settings['ajax_pagination'][0] == 'enable' ) {
				$summary .= ', ' . sprintf( _n( '1 item per page with manual AJAX', '%s items per page with manual AJAX', $view_settings['posts_per_page'], 'wpv-views' ), $view_settings['posts_per_page'] );
			} else {
				$summary .= ', ' . sprintf( _n( '1 item per page', '%s items per page', $view_settings['posts_per_page'], 'wpv-views' ), $view_settings['posts_per_page'] );
			}
		} else if ( isset( $view_settings['pagination']['mode'] ) && $view_settings['pagination']['mode'] == 'rollover' && isset( $view_settings['rollover'] ) && isset( $view_settings['rollover']['posts_per_page'] ) ) {
			$summary .= ', ' . sprintf( _n( '1 item per page with automatic AJAX', '%s items per page with automatic AJAX', $view_settings['rollover']['posts_per_page'], 'wpv-views' ), $view_settings['rollover']['posts_per_page'] );
		}
	}
    return $summary;
}