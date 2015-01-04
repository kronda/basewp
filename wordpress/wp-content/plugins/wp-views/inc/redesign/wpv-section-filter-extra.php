<?php

/*
* We can enable this to hide the Filter HTML/CSS/JS section
* TODO if we enable this and a user enables pagination with this section hidden there can be problems
*/

add_filter('wpv_sections_filter_show_hide', 'wpv_show_hide_filter_extra', 1,1);

function wpv_show_hide_filter_extra($sections) {
	$sections['filter-extra'] = array(
		'name'		=> __('Filter HTML/CSS/JS', 'wpv-views'),
		);
	return $sections;
}

add_action('view-editor-section-filter', 'add_view_filter_extra', 30, 2);

function add_view_filter_extra($view_settings, $view_id) {
    global $views_edit_help;
	$hide = '';
	if (isset($view_settings['sections-show-hide']) && isset($view_settings['sections-show-hide']['filter-extra']) && 'off' == $view_settings['sections-show-hide']['filter-extra']) {
		$hide = ' hidden';
	}?>
	<div class="wpv-setting-container wpv-setting-container-horizontal wpv-settings-filter-markup js-wpv-settings-filter-extra<?php echo $hide; ?>">

		<div class="wpv-settings-header">
			<h3>
				<?php _e( 'Filter HTML/CSS/JS', 'wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip" data-header="<?php echo $views_edit_help['filters_html_css_js']['title']; ?>" data-content="<?php echo $views_edit_help['filters_html_css_js']['content']; ?>"></i>
			</h3>
		</div>

		<div class="wpv-setting">

			<!-- <div class="js-error-container"></div> -->
			<div class="code-editor js-code-editor filter-html-editor" data-name="filter-html-editor" >
				<div class="code-editor-toolbar js-code-editor-toolbar">
					<ul class="js-wpv-filter-edit-toolbar">
						<li class="wpv-vicon-codemirror-button">
							<?php wpv_add_v_icon_to_codemirror( 'wpv_filter_meta_html_content' ); ?>
						</li>
						<?php
						wpv_add_cred_to_codemirror('wpv_filter_meta_html_content', 'li');
						?>
						<li class="js-editor-pagination-button-wrapper">
							<button class="button-secondary js-code-editor-toolbar-button js-wpv-pagination-popup" data-content="wpv_filter_meta_html_content">
								<i class="icon-pagination"></i>
								<span class="button-label"><?php _e('Pagination','wpv-views'); ?></span>
							</button>
						</li>
						<?php echo apply_filters('wpv_meta_html_add_form_button_new', '', '#wpv_filter_meta_html_content'); ?>
						<li>
							<button class="button-secondary js-code-editor-toolbar-button js-wpv-media-manager" data-id="<?php echo $view_id;?>" data-content="wpv_filter_meta_html_content">
								<i class="icon-picture"></i>
								<span class="button-label"><?php _e('Media','wpv-views'); ?></span>
							</button>
						</li>
					</ul>

				</div>

				<textarea cols="30" rows="10" id="wpv_filter_meta_html_content" name="_wpv_settings[filter_meta_html]"><?php echo ( isset( $view_settings['filter_meta_html'] ) ) ? $view_settings['filter_meta_html'] : ''; ?></textarea>
				
			</div>
			
			<?php 
			$filter_extra_css = isset( $view_settings['filter_meta_html_css'] ) ? $view_settings['filter_meta_html_css'] : '';
			if ( empty( $filter_extra_css ) ) {
				$aux_class = ' code-editor-textarea-empty';
			} else {
				$aux_class = ' code-editor-textarea-full';
			}
			?>

			<p class="js-wpv-filter-css-editor-old-place">
				<input type="hidden" name="_wpv_settings[filter_meta_html_state][css]" id="wpv_filter_meta_html_extra_css_state" value="<?php echo isset($view_settings['filter_meta_html_state']['css']) ? $view_settings['filter_meta_html_state']['css'] : 'off'; ?>" />
				<button class="button-secondary js-code-editor-button filter-css-editor-button<?php echo $aux_class; ?>" data-target="filter-css-editor" data-state="closed" data-closed="<?php echo htmlentities( __( 'Open CSS editor', 'wpv-views' ), ENT_QUOTES ); ?>" data-opened="<?php echo htmlentities( __( 'Close CSS editor', 'wpv-views' ), ENT_QUOTES ); ?>">
					<?php _e( 'Open CSS editor', 'wpv-views' ) ?>
				</button>
			</p>

			<div class="js-code-editor code-editor filter-css-editor closed" data-name="filter-css-editor">
				<div class="code-editor-toolbar js-code-editor-toolbar">
					<ul>
						<li>
							<button class="button-secondary js-code-editor-toolbar-button js-wpv-media-manager" data-id="<?php echo $view_id;?>" data-content="wpv_filter_meta_html_css">
								<i class="icon-picture"></i>
								<span class="button-label"><?php _e('Media','wpv-views'); ?></span>
							</button>
						</li>
					</ul>
				</div>
				<textarea cols="30" rows="10" id="wpv_filter_meta_html_css" name="_wpv_settings[filter_meta_html_css]"><?php echo $filter_extra_css; ?></textarea>
			</div>
			
			<?php 
			$filter_extra_js = isset( $view_settings['filter_meta_html_js'] ) ? $view_settings['filter_meta_html_js'] : '';
			if ( empty( $filter_extra_js ) ) {
				$aux_class = ' code-editor-textarea-empty';
			} else {
				$aux_class = ' code-editor-textarea-full';
			}
			?>

			<p class="js-wpv-filter-js-editor-old-place">
				<input type="hidden" name="_wpv_settings[filter_meta_html_state][js]" id="wpv_filter_meta_html_extra_js_state" value="<?php echo isset($view_settings['filter_meta_html_state']['js']) ? $view_settings['filter_meta_html_state']['js'] : 'off'; ?>" />
				<button class="button-secondary js-code-editor-button filter-js-editor-button<?php echo $aux_class; ?>" data-target="filter-js-editor"  data-state="closed" data-closed="<?php echo htmlentities( __( 'Open JS editor', 'wpv-views' ), ENT_QUOTES ); ?>" data-opened="<?php echo htmlentities( __( 'Close JS editor', 'wpv-views' ), ENT_QUOTES ); ?>">
					<?php _e( 'Open JS editor', 'wpv-views' ) ?>
				</button>
			</p>

			<div class="js-code-editor code-editor filter-js-editor closed" data-name="filter-js-editor" >
				<div class="code-editor-toolbar js-code-editor-toolbar">
					<ul>
						<li>
							<button class="button-secondary js-code-editor-toolbar-button js-wpv-media-manager" data-id="<?php echo $view_id;?>" data-content="wpv_filter_meta_html_js">
								<i class="icon-picture"></i>
								<span class="button-label"><?php _e('Media','wpv-views'); ?></span>
							</button>
						</li>
					</ul>
				</div>
				<textarea cols="30" rows="10" id="wpv_filter_meta_html_js" name="_wpv_settings[filter_meta_html_js]"><?php echo $filter_extra_js; ?></textarea>
			</div>

			<p class="update-button-wrap">
				<button data-success="<?php echo htmlentities( __('Data updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Data not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_extra_nonce' ); ?>" class="js-wpv-filter-extra-update button-secondary" disabled="disabled"><?php _e('Update', 'wpv-views'); ?></button>
			</p>
		</div>

	</div>
	
	<div class="wpv-setting-container js-wpv-settings-container-dps-filter<?php echo $hide; ?>">
	
		<div class="wpv-settings-header">
			<h3>
				<?php _e( 'Parametric search settings', 'wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip" data-header="<?php echo $views_edit_help['parametric_search']['title']; ?>" data-content="<?php echo $views_edit_help['parametric_search']['content']; ?>"></i>
			</h3>
		</div>

		<div class="wpv-setting js-wpv-dps-settings">
			<?php wpv_dps_settings_structure( $view_settings, $view_id ); ?>
		</div><!-- end .js-wpv-dps-settings -->
	</div>

	
	<div class="popup-window-container"> <!-- Use this element as a container for all popup windows. This element is hidden. -->

		<div class="wpv-dialog wpv-dialog-dependant-wizard js-dependant-form-dialog"> <!-- Popup when the dependant parametric search is allowed --><!-- DEPRECATED -->
			<div class="wpv-dialog-header">
				<h2><?php _e('Would you like to make this parametric search a dependant one?','wpv-views') ?></h2>
				<i class="icon-remove js-dialog-close"></i>
			</div>
		</div>
		
		<div class="wpv-dialog wpv-dialog-submit-button js-submit-button-dialog"> <!-- Popup for the submit button addition -->
			
			<div class="wpv-dialog-header">
				<h2><?php _e( 'Create a submit button for this parametric search.', 'wpv-views' ); ?></h2>
				<i class="icon-remove js-dialog-close"></i>
			</div>
			
			<div class="js-submit_shortcode_label-wrap wpv-dialog-content">
				<p>
					<label for="submit_shortcode_label"><?php _e('Button label:', 'wpv-views'); ?></label>
					<input value="<?php echo esc_attr( __('Search', 'wpv-views') ); ?>" id="submit_shortcode_label" type="text">
				</p>
				<p>
					<label for="submit_shortcode_button_classname"><?php _e('Button classname:', 'wpv-views'); ?></label>
					<input value="" id="submit_shortcode_button_classname" type="text">
				</p>
			</div>
			
			<div class="js-errors-in-parametric-box"></div>
			
			<div class="wpv-dialog-footer">
				<button class="button js-dialog-close" id="js_parametric_cancel"><?php _e('Cancel','wpv-views') ?></button>
				<button class="button-primary js-code-editor-toolbar-button js-parametric-add-submit-short-tag-label"><?php _e('Insert submit button','wpv-views') ?></button>
			</div>
			
		</div> <!-- End of popup for the submit button addition -->
		
		<div class="wpv-dialog wpv-dialog-reset-button js-reset-button-dialog"> <!-- Popup for the reset button addition -->
			
			<div class="wpv-dialog-header">
				<h2><?php _e( 'Create a reset button for this parametric search.', 'wpv-views' ); ?></h2>
				<i class="icon-remove js-dialog-close"></i>
			</div>
			
			<div class="js-reset_shortcode_label-wrap wpv-dialog-content">
				<p>
					<label for="reset_shortcode_label"><?php _e('Button label:', 'wpv-views'); ?></label>
					<input value="<?php echo esc_attr( __('Reset', 'wpv-views') ); ?>" id="reset_shortcode_label" type="text">
				</p>
				<p>
					<label for="reset_shortcode_button_classname"><?php _e('Button classname:', 'wpv-views'); ?></label>
					<input value="" id="reset_shortcode_button_classname" type="text">
				</p>
			</div>
			
			<div class="js-errors-in-parametric-box"></div>
			
			<div class="wpv-dialog-footer">
				<button class="button js-dialog-close" id="js_parametric_cancel"><?php _e('Cancel','wpv-views') ?></button>
				<button class="button-primary js-code-editor-toolbar-button js-parametric-add-reset-short-tag-label"><?php _e('Insert clear form button','wpv-views') ?></button>
			</div>
			
		</div> <!-- End of popup for the reset button addition -->
		
		<div class="wpv-dialog wpv-dialog-spinner-button js-spinner-button-dialog"> <!-- Popup for the spinner button addition -->
			
			<div class="wpv-dialog-header">
				<h2><?php _e( 'Create a spinner container for this parametric search.', 'wpv-views' ); ?></h2>
				<i class="icon-remove js-dialog-close"></i>
			</div>
			
			<div class="js-spinner_shortcode_label-wrap wpv-dialog-content">
				<p>
					<label for="spinner_shortcode_container_type"><?php _e('Container type:', 'wpv-views'); ?></label>
					<select id="spinner_shortcode_container_type">
						<option value="div"><?php _e('Division', 'wpv-views'); ?></option>
						<option value="p"><?php _e('Paragraph', 'wpv-views'); ?></option>
						<option value="span"><?php _e('Span', 'wpv-views'); ?></option>
					</select>
				</p>
				<p>
					<label for="spinner_shortcode_container_classname"><?php _e('Container classname:', 'wpv-views'); ?></label>
					<input value="" id="spinner_shortcode_container_classname" type="text">
				</p>
				<p>
					<label for="spinner_shortcode_spinner_position"><?php _e('Spinner:', 'wpv-views'); ?></label>
					<select id="spinner_shortcode_spinner_position">
						<option value="none"><?php _e('Do not show the spinner', 'wpv-views'); ?></option>
						<option value="before"><?php _e('Show the spinner before the text', 'wpv-views'); ?></option>
						<option value="after"><?php _e('Show the spinner after the text', 'wpv-views'); ?></option>
					</select>
				</p>
				<p>
					<label for="spinner_shortcode_content"><?php _e('Content:','wpv-views'); ?></button>
					<textarea id="spinner_shortcode_content"></textarea>
				</p>
			</div>
			
			<div class="js-errors-in-parametric-box"></div>
			
			<div class="wpv-dialog-footer">
				<button class="button js-dialog-close" id="js_parametric_cancel"><?php _e('Cancel','wpv-views') ?></button>
				<button class="button-primary js-code-editor-toolbar-button js-parametric-add-spinner-short-tag-label"><?php _e('Insert spinner container','wpv-views') ?></button>
			</div>
			
		</div> <!-- End of popup for the spinner button addition -->
	
	</div><!-- popup-window-container end -->
<?php }

function wpv_dps_settings_structure( $view_settings, $view_id ) {
	
	if ( !isset( $view_settings['query_type'] ) || !is_array( $view_settings['query_type'] ) || !in_array( 'posts', $view_settings['query_type'] ) ) {
		_e('Only Views listing posts can have parametric search inputs.', 'wpv-views');
		return;
	}
	
	if ( !isset( $view_settings['dps'] ) ) {
			$view_settings['dps'] = array();
		}
		$controls_per_kind = wpv_count_filter_controls( $view_settings );
		$controls_count = 0;
		$no_intersection = array();
		
		if ( !isset( $controls_per_kind['error'] ) ) {
		//	$controls_count = array_sum( $controls_per_kind );
			$controls_count = $controls_per_kind['cf'] + $controls_per_kind['tax'] + $controls_per_kind['pr'];
			if ( isset( $controls_per_kind['cf'] ) && $controls_per_kind['cf'] > 1 && ( !isset( $view_settings['custom_fields_relationship'] ) || $view_settings['custom_fields_relationship'] != 'AND' ) ) {
				$no_intersection[] = __( 'custom field', 'wpv-views' );
			}
			if ( isset( $controls_per_kind['tax'] ) && $controls_per_kind['tax'] > 1 && ( !isset( $view_settings['taxonomy_relationship'] ) || $view_settings['taxonomy_relationship'] != 'AND' ) ) {
				$no_intersection[] = __( 'taxonomy', 'wpv-views' );
			}
		}
		
		if ( isset( $controls_per_kind['warning'] ) ) {
			?>
			<p class="toolset-alert toolset-alert-info">
				<?php echo $controls_per_kind['warning']; ?>
			</p>
			<?php
		}
		
		if ( $controls_count > 0 ) {
			if ( !isset( $view_settings['dps'] ) ) {
				$view_settings['dps'] = array();
			} ?>
			<h4><?php _e('When to update the Views results', 'wpv-views'); ?></h4>
				<ul>
					<li>
						<?php
						if ( !isset( $view_settings['dps']['ajax_results'] ) ) {
							$view_settings['dps']['ajax_results'] = 'disable';
						}
						$enabled_checked = ( $view_settings['dps']['ajax_results'] == 'disable' ) ? ' checked="checked"' : '';
						?>
						<input type="radio" value="disable" id="wpv-dps-ajax-results-disable" class="js-wpv-dps-ajax-results js-wpv-dps-toggle-container" name="wpv-dps-ajax-results"<?php echo $enabled_checked; ?> data-toggletarget="js-wpv-ajax-results-details" />
						<label for="wpv-dps-ajax-results-disable"><?php _e('Update the View results only when submitting the form', 'wpv-views'); ?></label>
					</li>
					<li>
						<?php
						$enabled_checked = ( $view_settings['dps']['ajax_results'] == 'enable' ) ? ' checked="checked"' : '';
						?>
						<input type="radio" value="enable" id="wpv-dps-ajax-results-enable" class="js-wpv-dps-ajax-results js-wpv-dps-toggle-container" name="wpv-dps-ajax-results"<?php echo $enabled_checked; ?> data-toggletarget="js-wpv-ajax-results-details" />
						<label for="wpv-dps-ajax-results-enable"><?php _e('Update the View results every time an input changes', 'wpv-views'); ?></label>
					</li>
				</ul>
			<h4><?php _e('Which options to display in the form inputs', 'wpv-views'); ?></h4>
			<div class="wpv-dps-settings">
				<?php
				if ( !isset( $view_settings['dps']['enable_dependency'] ) ) {
					$view_settings['dps']['enable_dependency'] = 'disable';
				}
				$checked = ( $view_settings['dps']['enable_dependency'] == 'enable' ) ? ' checked="checked"' : '';
				$dps_disabled = false;
				if ( count( $no_intersection ) > 0 ) {
					$dps_disabled = true;
				}
				?>
				<?php if ( $dps_disabled ) { ?>
				<p class="toolset-alert toolset-alert-info">
					<?php
					$glue = __( ' and ', 'wpv-views' );
					$no_intersection_text = implode( $glue , $no_intersection );
					echo sprintf( __( 'Your %s filters are using an internal "OR" kind of relationship, and dependant parametric search for that filters needs "AND" relationships.', 'wpv-views' ), $no_intersection_text );
					?>
					<br /><br />
					<button class="button-secondary js-make-intersection-filters" data-nonce="<?php echo wp_create_nonce( 'wpv_view_make_intersection_filters' ); ?>" data-cf="<?php echo ( in_array( 'cf', $no_intersection ) ) ? 'true' : 'false'; ?>" data-tax="<?php echo ( in_array( 'tax', $no_intersection ) ) ? 'true' : 'false'; ?>">
						<?php _e('Fix filters relationship', 'wpv-views'); ?>
					</button>
				</p>
				<?php } else {
				//	echo '<pre>';print_r($view_settings);echo '</pre>';
				?>
				<ul>
					<li>
						<?php $checked = ( $view_settings['dps']['enable_dependency'] == 'disable' ) ? ' checked="checked"' : ''; ?>
						<input type="radio" value="disable" id="wpv-dps-enable-disabled" class="js-wpv-dps-enable js-wpv-dps-toggle-container" name="wpv-dps-enable"<?php echo $checked; ?> data-toggletarget="js-wpv-dps-crossed-details" />
						<label for="wpv-dps-enable-disabled"><?php _e('Always show all values for inputs', 'wpv-views'); ?></label>
					</li>
					<li>
						<?php $checked = ( $view_settings['dps']['enable_dependency'] == 'enable' ) ? ' checked="checked"' : ''; ?>
						<input type="radio" value="enable" id="wpv-dps-enable-enabled" class="js-wpv-dps-enable js-wpv-dps-toggle-container" name="wpv-dps-enable"<?php echo $checked; ?> data-toggletarget="js-wpv-dps-crossed-details" />
						<label for="wpv-dps-enable-enabled"><?php _e('Show only available options for each input', 'wpv-views'); ?></label>
					</li>
				</ul>
				<div class="wpv-dps-crossed-details js-wpv-dps-crossed-details<?php echo ( empty( $checked ) ) ? ' hidden' : ''; ?>" style="margin-top:10px;">
					<p>
						<?php _e('Choose if you want to hide or disable irrelevant options for inputs:', 'wpv-views'); ?>
					</p>
					<table class="widefat">
						<thead>
							<tr>
								<th>
									<?php _e('Input type', 'wpv-views'); ?>
								</th>
								<th>
									<?php _e('Disable / Hide', 'wpv-views'); ?>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr class="alternate">
								<td>
									<?php _e('Select dropdown', 'wpv-views'); ?>
								</td>
								<td>
									<?php 
									$checked = '';
									if ( !isset( $view_settings['dps']['empty_select'] ) || $view_settings['dps']['empty_select'] == 'disable' ) {
										$checked = ' checked="checked"';
									}
									?>
									<input type="radio" id="wpv-dps-empty-select-disable" value="disable" class="js-wpv-dps-empty-select" name="wpv-dps-empty-select"<?php echo $checked; ?> />
									<label for="wpv-dps-empty-select-disable"><?php _e('Disable', 'wpv-views'); ?></label>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<?php
									if ( isset( $view_settings['dps']['empty_select'] ) && $view_settings['dps']['empty_select'] == 'hide' ) {
										$checked = ' checked="checked"';
									} else {
										$checked = '';
									}
									?>
									<input type="radio" id="wpv-dps-empty-select-hide" value="hide" class="js-wpv-dps-empty-select" name="wpv-dps-empty-select"<?php echo $checked; ?> />
									<label for="wpv-dps-empty-select-hide"><?php _e('Hide', 'wpv-views'); ?></label>
								</td>
							</tr>
							<tr>
								<td>
									<?php _e('Multi-select', 'wpv-views'); ?>
								</td>
								<td>
									<?php 
									$checked = '';
									if ( !isset( $view_settings['dps']['empty_multi_select'] ) || $view_settings['dps']['empty_multi_select'] == 'disable' ) {
										$checked = ' checked="checked"';
									}
									?>
									<input type="radio" id="wpv-dps-empty-multi-select-disable" value="disable" class="js-wpv-dps-empty-multi-select" name="wpv-dps-empty-multi-select"<?php echo $checked; ?> />
									<label for="wpv-dps-empty-multi-select-disable"><?php _e('Disable', 'wpv-views'); ?></label>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<?php
									if ( isset( $view_settings['dps']['empty_multi_select'] ) && $view_settings['dps']['empty_multi_select'] == 'hide' ) {
										$checked = ' checked="checked"';
									} else {
										$checked = '';
									}
									?>
									<input type="radio" id="wpv-dps-empty-multi-select-hide" value="hide" class="js-wpv-dps-empty-multi-select" name="wpv-dps-empty-multi-select"<?php echo $checked; ?> />
									<label for="wpv-dps-empty-multi-select-hide"><?php _e('Hide', 'wpv-views'); ?></label>
								</td>
							</tr>
							<tr class="alternate">
								<td>
									<?php _e('Radio inputs', 'wpv-views'); ?>
								</td>
								<td>
									<?php 
									$checked = '';
									if ( !isset( $view_settings['dps']['empty_radios'] ) || $view_settings['dps']['empty_radios'] == 'disable' ) {
										$checked = ' checked="checked"';
									}
									?>
									<input type="radio" id="wpv-dps-empty-radios-disable" value="disable" class="js-wpv-dps-empty-radios" name="wpv-dps-empty-radios"<?php echo $checked; ?> />
									<label for="wpv-dps-empty-radios-disable"><?php _e('Disable', 'wpv-views'); ?></label>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<?php
									if ( isset( $view_settings['dps']['empty_radios'] ) && $view_settings['dps']['empty_radios'] == 'hide' ) {
										$checked = ' checked="checked"';
									} else {
										$checked = '';
									}
									?>
									<input type="radio" id="wpv-dps-empty-radios-hide" value="hide" class="js-wpv-dps-empty-radios" name="wpv-dps-empty-radios"<?php echo $checked; ?> />
									<label for="wpv-dps-empty-radios-hide"><?php _e('Hide', 'wpv-views'); ?></label>
								</td>
							</tr>
							<tr>
								<td>
									<?php _e('Checkboxes', 'wpv-views'); ?>
								</td>
								<td>
									<?php 
									$checked = '';
									if ( !isset( $view_settings['dps']['empty_checkboxes'] ) || $view_settings['dps']['empty_checkboxes'] == 'disable' ) {
										$checked = ' checked="checked"';
									}
									?>
									<input type="radio" id="wpv-dps-empty-checkboxes-disable" value="disable" class="js-wpv-dps-empty-checkboxes" name="wpv-dps-empty-checkboxes"<?php echo $checked; ?> />
									<label for="wpv-dps-empty-checkboxes-disable"><?php _e('Disable', 'wpv-views'); ?></label>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<?php
									if ( isset( $view_settings['dps']['empty_checkboxes'] ) && $view_settings['dps']['empty_checkboxes'] == 'hide' ) {
										$checked = ' checked="checked"';
									} else {
										$checked = '';
									}
									?>
									<input type="radio" id="wpv-dps-empty-checkboxes-hide" value="hide" class="js-wpv-dps-empty-checkboxes" name="wpv-dps-empty-checkboxes"<?php echo $checked; ?> />
									<label for="wpv-dps-empty-checkboxes-hide"><?php _e('Hide', 'wpv-views'); ?></label>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<?php } ?>
			</div>
			<p>
				<button class="js-wpv-dps-advanced-toggle button-secondary" data-state="closed" data-section="js-wpv-dps-advanced" data-opened="<?php echo esc_attr( __('Close advanced options', 'wpv-views') ); ?>" data-closed="<?php echo esc_attr( __('Advanced options', 'wpv-views') ); ?>" type="button"><?php _e('Advanced options', 'wpv-views'); ?></button>
			</p>
			<div class="js-wpv-dps-advanced hidden wpv-advanced-setting">
				<h4><?php _e('Spinners settings', 'wpv-views'); ?></h4>
				<p>
					<?php _e( 'You can display a spinner when the View results update', 'wpv-views' ); ?>
				</p>
				<?php if ( !isset( $view_settings['dps']['spinner'] ) ) {
					$view_settings['dps']['spinner'] = 'none';
				}
				?>
				<ul>
					<li>
						<?php $checked = ( $view_settings['dps']['spinner'] == 'none' ) ? ' checked="checked"' : ''; ?>
						<input type="radio" id="wpv-dps-spinner-none" class="js-wpv-dps-spinner" name="wpv-dps-spinner" value="none"<?php echo $checked; ?> />
						<label for="wpv-dps-spinner-none"><?php _e('No spinner graphics', 'wpv-views'); ?></label>
					</li>
					<li>
						<?php $checked = ( $view_settings['dps']['spinner'] == 'custom' ) ? ' checked="checked"' : ''; ?>
						<input type="radio" id="wpv-dps-spinner-custom" class="js-wpv-dps-spinner" name="wpv-dps-spinner" value="custom" data-toggletarget="js-wpv-dps-spinner-extra-custom"<?php echo $checked; ?> />
						<label for="wpv-dps-spinner-custom"><?php _e('Custom spinner graphics', 'wpv-views'); ?></label>
						<span class="js-wpv-dps-spinner-extra js-wpv-dps-spinner-extra-custom<?php echo empty( $checked ) ? ' hidden' : ''; ?>">
							<input id="wpv-dps-spinner-image" class="js-wpv-dps-spinner-image" type="text" name="wpv-dps-spinner-image-uploaded" value="<?php echo isset( $view_settings['dps']['spinner_image_uploaded'] ) ? esc_url( $view_settings['dps']['spinner_image_uploaded'] ) : ''; ?>" />
							<button class="button-secondary js-code-editor-toolbar-button js-wpv-media-manager" data-content="wpv-dps-spinner-image" data-id="<?php echo $view_id; ?>"><?php _e('Upload Image', 'wpv-views'); ?></button>
							<?php if ( isset( $view_settings['dps']['spinner_image_uploaded'] ) && !empty( $view_settings['dps']['spinner_image_uploaded'] ) ): ?>
								<img id="wpv-dps-spinner-image-preview" class="js-wpv-dps-spinner-image-preview" src="<?php echo $view_settings['dps']['spinner_image_uploaded']; ?>" height="16" />
							<?php endif; ?>
						</span>
					</li>
					<li>
						<?php $checked = ( $view_settings['dps']['spinner'] == 'inhouse' ) ? ' checked="checked"' : ''; ?>
						<input type="radio" id="wpv-dps-spinner-inhouse" class="js-wpv-dps-spinner" name="wpv-dps-spinner" value="inhouse" data-toggletarget="js-wpv-dps-spinner-extra-inhouse"<?php echo $checked; ?> />
						<label for="wpv-dps-spinner-inhouse"><?php _e('Built in Views spinner graphics', 'wpv-views'); ?></label>
						<span class="js-wpv-dps-spinner-extra js-wpv-dps-spinner-extra-inhouse<?php echo empty( $checked ) ? ' hidden' : ''; ?>">
							<ul id="wpv-spinner-default" class="wpv-spinner-selection" style="overflow:hidden;margin-left:30px;margin-top:5px;">
								<?php
								if ( isset( $view_settings['dps']['spinner_image'] ) ) {
									$spinner_image = $view_settings['dps']['spinner_image'];
								} else {
									$spinner_image = '';
								}
								foreach (glob(WPV_PATH_EMBEDDED . "/res/img/ajax-loader*") as $file) {
								$filename = WPV_URL_EMBEDDED . '/res/img/' . basename($file);
								$filename2 = WPV_URL . '/res/img/' . basename($file);
								?>
								<li style="float:left;">
									<label>
										<input type="radio" name="wpv-dps-spinner-image" value="<?php echo $filename; ?>"<?php if ( $spinner_image == $filename || $spinner_image == $filename2) { echo ' checked="checked"'; } ?> />
										<img style="background-color: #FFFFFF;" src="<?php echo $filename; ?>" title="<?php echo $filename; ?>" />
									</label>
								</li>
								<?php } ?>
							</ul>
						</span>
					</li>
				</ul>
				<div class="wpv-ajax-results-details js-wpv-ajax-results-details<?php echo ( empty( $enabled_checked ) ) ? ' hidden' : ''; ?>">
					<h4><?php _e('Javascript settings', 'wpv-views'); ?></h4>
					<p>
						<?php _e('You can execute the following javascript callbacks before and after the View results are updated.', 'wpv-views'); ?>
					</p>
					<ul>
						<li>
							<label for="wpv-dps-ajax-results-before"><?php _e('Javascript function to execute before every AJAX update:', 'wpv-views'); ?></label>
							<input type="text" id="wpv-dps-ajax-results-before" class="js-wpv-dps-ajax-results-before" name="wpv-dps-ajax-results-before" value="<?php echo ( isset( $view_settings['dps']['ajax_results_before'] ) ) ? esc_attr( $view_settings['dps']['ajax_results_before'] ) : ''; ?>" />
						</li>
						<li>
							<label for="wpv-dps-ajax-results-after"><?php _e('Javascript function to execute after every AJAX update:', 'wpv-views'); ?></label>
							<input type="text" id="wpv-dps-ajax-results-after" class="js-wpv-dps-ajax-results-after" name="wpv-dps-ajax-results-after" value="<?php echo ( isset( $view_settings['dps']['ajax_results_after'] ) ) ? esc_attr( $view_settings['dps']['ajax_results_after'] ) : ''; ?>" />
						</li>
					</ul>
					<h4><?php _e('Submit button settings', 'wpv-views'); ?></h4>
					<p>
						<?php _e('When the parametric search updates the results on the fly, how should the submit button work?', 'wpv-views'); ?>
					</p>
					<ul>
						<?php
						if ( !isset( $view_settings['dps']['ajax_results_submit'] ) ) {
							$view_settings['dps']['ajax_results_submit'] = 'ajaxed';
						}//TODO what to do with the reset button?
						?>
						<li>
							<input type="radio" name="wpv-dps-ajax-results-submit" id="wpv-ajax-results-submit-ajaxed" value="ajaxed" class=""<?php if ( $view_settings['dps']['ajax_results_submit'] == 'ajaxed' ) { echo ' checked="checked"'; } ?> />
							<label for="wpv-ajax-results-submit-ajaxed"><?php _e('Submit the form without reloading the page', 'wpv-views'); ?></label>
						</li>
						<li>
							<input type="radio" name="wpv-dps-ajax-results-submit" id="wpv-ajax-results-submit-reload" value="reload" class=""<?php if ( $view_settings['dps']['ajax_results_submit'] == 'reload' ) { echo ' checked="checked"'; } ?> />
							<label for="wpv-ajax-results-submit-reload"><?php _e('Submit the form and reload the page', 'wpv-views'); ?></label>
						</li>
						<li>
							<input type="radio" name="wpv-dps-ajax-results-submit" id="wpv-ajax-results-submit-hidden" value="hidden" class=""<?php if ( $view_settings['dps']['ajax_results_submit'] == 'hidden' ) { echo ' checked="checked"'; } ?> />
							<label for="wpv-ajax-results-submit-hidden"><?php _e('Do not show this button', 'wpv-views'); ?></label>
						</li>
					</ul>
				</div>
			</div>
		<?php } else { ?>
			<p>
				<?php 
				if ( isset( $controls_per_kind['error'] ) ) {
					echo $controls_per_kind['error'];
				} else {
					_e('There are no parametric search inputs in this View.', 'wpv-views');
				}
				?>
			</p>
		<?php } ?>
			<p>
				<a href="http://wp-types.com/documentation/user-guides/front-page-filters/"><?php _e('Learn more about parametric search', 'wpv-views'); ?></a>
			</p>
			<p class="update-button-wrap">
				<button data-success="<?php echo esc_attr( __('Data updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo esc_attr( __('Data not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_dps_nonce' ); ?>" class="js-wpv-filter-dps-update button-secondary" disabled="disabled"><?php _e('Update', 'wpv-views'); ?></button>
			</p>
	<?php
}

add_action('wp_ajax_wpv_filter_update_dps_settings', 'wpv_filter_update_dps_settings');

function wpv_filter_update_dps_settings() {
	$nonce = $_POST["nonce"];
	if (! wp_verify_nonce( $nonce, 'wpv_view_filter_dps_nonce' ) ) die( "Security check" );
	$view_array = get_post_meta( $_POST["id"], '_wpv_settings', true );
	if ( !isset( $view_array['dps'] ) ) {
		$view_array['dps'] = array();
	}
	if ( isset( $_POST['dpsdata'] ) ) {
		$passed_data = wp_parse_args( $_POST['dpsdata'] );
		// AJAX update View results
		if ( isset( $passed_data['wpv-dps-ajax-results'] ) && $passed_data['wpv-dps-ajax-results'] == 'enable' ) {
			$view_array['dps']['ajax_results'] = 'enable';
		} else {
			$view_array['dps']['ajax_results'] = 'disable';
		}
		if ( isset( $passed_data['wpv-dps-ajax-results-before'] ) ) {
			$view_array['dps']['ajax_results_before'] = esc_attr( $passed_data['wpv-dps-ajax-results-before'] );
		} else {
			$view_array['dps']['ajax_results_before'] = '';
		}
		if ( isset( $passed_data['wpv-dps-ajax-results-after'] ) ) {
			$view_array['dps']['ajax_results_after'] = esc_attr( $passed_data['wpv-dps-ajax-results-after'] );
		} else {
			$view_array['dps']['ajax_results_after'] = '';
		}
		if ( isset( $passed_data['wpv-dps-ajax-results-submit'] ) ) {
			$view_array['dps']['ajax_results_submit'] = esc_attr( $passed_data['wpv-dps-ajax-results-submit'] );
		} else {
			$view_array['dps']['ajax_results_submit'] = 'reload';
		}
		// Enable dependency and data
		if ( isset( $passed_data['wpv-dps-enable'] ) && $passed_data['wpv-dps-enable'] == 'enable' ) {
			$view_array['dps']['enable_dependency'] = 'enable';
		} else {
			$view_array['dps']['enable_dependency'] = 'disable';
		}
		if ( isset( $passed_data['wpv-dps-empty-select'] ) && $passed_data['wpv-dps-empty-select'] == 'hide' ) {
			$view_array['dps']['empty_select'] = 'hide';
		} else {
			$view_array['dps']['empty_select'] = 'disable';
		}
		if ( isset( $passed_data['wpv-dps-empty-multi-select'] ) && $passed_data['wpv-dps-empty-multi-select'] == 'hide' ) {
			$view_array['dps']['empty_multi_select'] = 'hide';
		} else {
			$view_array['dps']['empty_multi_select'] = 'disable';
		}
		if ( isset( $passed_data['wpv-dps-empty-radios'] ) && $passed_data['wpv-dps-empty-radios'] == 'hide' ) {
			$view_array['dps']['empty_radios'] = 'hide';
		} else {
			$view_array['dps']['empty_radios'] = 'disable';
		}
		if ( isset( $passed_data['wpv-dps-empty-checkboxes'] ) && $passed_data['wpv-dps-empty-checkboxes'] == 'hide' ) {
			$view_array['dps']['empty_checkboxes'] = 'hide';
		} else {
			$view_array['dps']['empty_checkboxes'] = 'disable';
		}
		// Spinners
		if ( isset( $passed_data['wpv-dps-spinner'] ) ) {
			$view_array['dps']['spinner'] = esc_attr( $passed_data['wpv-dps-spinner'] );
		} else {
			$view_array['dps']['spinner'] = 'none';
		}
		if ( isset( $passed_data['wpv-dps-spinner-image-uploaded'] ) ) {
			$view_array['dps']['spinner_image_uploaded'] = esc_url( $passed_data['wpv-dps-spinner-image-uploaded'] );
		} else {
			$view_array['dps']['spinner_image_uploaded'] = '';
		}
		if ( isset( $passed_data['wpv-dps-spinner-image'] ) ) {
			$view_array['dps']['spinner_image'] = $passed_data['wpv-dps-spinner-image'];
		} else {
			$view_array['dps']['spinner_image'] = '';
		}
	} else {
		
	}
	update_post_meta( $_POST["id"], '_wpv_settings', $view_array );
	echo $_POST['id'];
	die();
}

add_action('wp_ajax_wpv_update_parametric_search_section', 'wpv_update_parametric_search_section');

function wpv_update_parametric_search_section() {
	$nonce = $_POST["nonce"];
	if (! wp_verify_nonce( $nonce, 'wpv_view_edit_general_nonce' ) ) die( "Security check" );
	$view_array = get_post_meta( $_POST["id"], '_wpv_settings', true );
	wpv_dps_settings_structure( $view_array, $_POST["id"] );
	die();
}