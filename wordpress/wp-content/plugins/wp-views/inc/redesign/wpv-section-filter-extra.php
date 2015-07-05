<?php

/*
* We can enable this to hide the Filter section
* TODO if we enable this and a user enables pagination with this section hidden there can be problems
*/

add_filter( 'wpv_sections_filter_show_hide', 'wpv_show_hide_filter_extra', 1, 1 );

function wpv_show_hide_filter_extra( $sections ) {
	$sections['filter-extra-parametric'] = array(
		'name' => __( 'Parametric Search Settings', 'wpv-views' ),
	);
	$sections['filter-extra'] = array(
		'name' => __( 'Filter', 'wpv-views' ),
	);
	return $sections;
}

add_action( 'view-editor-section-filter', 'add_view_filter_parametric_search', 30, 2 );

function add_view_filter_parametric_search( $view_settings, $view_id ) {
	$is_section_hidden = false;
	if ( isset( $view_settings['sections-show-hide'] )
		&& isset( $view_settings['sections-show-hide']['filter-extra-parametric'] )
		&& 'off' == $view_settings['sections-show-hide']['filter-extra-parametric'] )
	{
		$is_section_hidden = true;
	}
	$hidden_class = $is_section_hidden ? 'hidden' : '';
	$section_help_pointer = WPV_Admin_Messages::edit_section_help_pointer( 'parametric_search' );
	?>
	<div class="wpv-setting-container js-wpv-settings-filter-extra-parametric <?php echo $hidden_class; ?>">
	
		<div class="wpv-settings-header">
			<h3>
				<?php _e( 'Parametric Search Settings', 'wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip" 
					data-header="<?php echo esc_attr( $section_help_pointer['title'] ); ?>" 
					data-content="<?php echo esc_attr( $section_help_pointer['content'] ); ?>">
				</i>
			</h3>
		</div>

		<div class="wpv-setting js-wpv-dps-settings">
			<?php
			$listing = '';
			if ( 
				isset( $view_settings['query_type'] ) 
				&& is_array( $view_settings['query_type'] ) 
				&& in_array( 'posts', $view_settings['query_type'] ) 
			) {
				$listing = 'posts';
			}
			?>			
			<p class="toolset-alert toolset-alert-info wpv-settings-query-type-taxonomy wpv-settings-query-type-users<?php echo $listing == 'posts' ? ' hidden' : ''; ?>">
				<?php _e('Only Views listing posts can have parametric search inputs.', 'wpv-views'); ?>
			</p>
			<div class="wpv-settings-query-type-posts<?php echo $listing == 'posts' ? '' : ' hidden'; ?>">
				<?php
				$controls_per_kind = wpv_count_filter_controls( $view_settings );
				$no_intersection = array();
				$controls_count = $controls_per_kind['cf'] + $controls_per_kind['tax'] + $controls_per_kind['pr'] + $controls_per_kind['search'];
				
				if ( 
					isset( $controls_per_kind['cf'] ) 
					&& $controls_per_kind['cf'] > 1 
					&& ( 
						! isset( $view_settings['custom_fields_relationship'] ) 
						|| $view_settings['custom_fields_relationship'] != 'AND' 
					) 
				) {
					$no_intersection[] = __( 'custom field', 'wpv-views' );
				}
				if ( 
					isset( $controls_per_kind['tax'] ) 
					&& $controls_per_kind['tax'] > 1 
					&& ( 
						! isset( $view_settings['taxonomy_relationship'] ) 
						|| $view_settings['taxonomy_relationship'] != 'AND' 
					) 
				) {
					$no_intersection[] = __( 'taxonomy', 'wpv-views' );
				}
				
				if ( isset( $controls_per_kind['warning'] ) ) {
					?>
					<!--<p class="toolset-alert toolset-alert-info js-wpv-mismatch-parametric-search-count">
						<?php echo $controls_per_kind['warning']; ?>
					</p>-->
					<?php
				}
				
				if ( isset( $controls_per_kind['error'] ) ) {
					echo $controls_per_kind['error'];
				}
				
				if ( ! isset( $view_settings['dps'] ) ) {
					$view_settings['dps'] = array();
					$view_settings['dps']['mode_helper'] = '';
				} else {
					if ( !isset( $view_settings['dps']['mode_helper'] ) ) {
						$view_settings['dps']['mode_helper'] = 'custom';
					}
				}
				?>
				<h3><?php _e( 'How do you want to update the results?', 'wpv-views' ); ?></h3>
				<ul>
					<li>
						<input type="radio" <?php checked( $view_settings['dps']['mode_helper'], 'fullrefreshonsubmit' ); ?> class="js-wpv-dps-mode-helper js-wpv-dps-mode-helper-fullrefreshonsubmit" name="wpv-dps-mode-helper" id="wpv-dps-mode-helper-fullrefreshonsubmit" value="fullrefreshonsubmit" autocomplete="off" />
						<label for="wpv-dps-mode-helper-fullrefreshonsubmit"><?php _e( 'Full page refresh when visitors click on the search button', 'wpv-views' ); ?></label>
					</li>
					<li>
						<input type="radio" <?php checked( $view_settings['dps']['mode_helper'], 'ajaxrefreshonsubmit' ); ?> class="js-wpv-dps-mode-helper js-wpv-dps-mode-helper-ajaxrefreshonsubmit" name="wpv-dps-mode-helper" id="wpv-dps-mode-helper-ajaxrefreshonsubmit" value="ajaxrefreshonsubmit" autocomplete="off" />
						<label for="wpv-dps-mode-helper-ajaxrefreshonsubmit"><?php _e( 'AJAX results update when visitors click on the search button', 'wpv-views' ); ?></label>
					</li>
					<li>
						<input type="radio" <?php checked( $view_settings['dps']['mode_helper'], 'ajaxrefreshonchange' ); ?> class="js-wpv-dps-mode-helper js-wpv-dps-mode-helper-ajaxrefreshonchange" name="wpv-dps-mode-helper" id="wpv-dps-mode-helper-ajaxrefreshonchange" value="ajaxrefreshonchange" autocomplete="off" />
						<label for="wpv-dps-mode-helper-ajaxrefreshonchange"><?php _e( 'AJAX results update when visitors change any filter values', 'wpv-views' ); ?></label>
					</li>
					<li>
						<input type="radio" <?php checked( $view_settings['dps']['mode_helper'], 'custom' ); ?> class="js-wpv-dps-mode-helper js-wpv-dps-mode-helper-custom" name="wpv-dps-mode-helper" id="wpv-dps-mode-helper-custom" value="custom" autocomplete="off" />
						<label for="wpv-dps-mode-helper-custom"><?php _e( 'Let me choose individual settings manually', 'wpv-views' ); ?></label>
					</li>
				</ul>
				<div class="wpv-advanced-setting js-wpv-ps-settings-custom"<?php if ( $view_settings['dps']['mode_helper'] != 'custom' ) { echo ' style="display:none"'; } ?>>
					<h4><?php _e('When to update the Views results', 'wpv-views'); ?></h4>
					<ul>
						<?php
						if ( ! isset( $view_settings['dps']['ajax_results'] ) ) {
							$view_settings['dps']['ajax_results'] = '';
						}
						?>
						<li>
							<input type="radio" <?php checked( $view_settings['dps']['ajax_results'], 'disable' ); ?> value="disable" id="wpv-dps-ajax-results-disable" class="js-wpv-dps-ajax-results js-wpv-dps-ajax-results-disable" name="wpv-dps-ajax-results" autocomplete="off" />
							<label for="wpv-dps-ajax-results-disable"><?php _e('Update the View results only when clicking on the search button', 'wpv-views'); ?></label>
							<div class="wpv-setting-extra js-wpv-dps-ajax-results-extra js-wpv-dps-ajax-results-extra-disable"<?php if ( $view_settings['dps']['ajax_results'] != 'disable' ) { echo 'style="display:none"'; } ?>>
								<?php
								if ( !isset( $view_settings['dps']['ajax_results_submit'] ) ) {
									$view_settings['dps']['ajax_results_submit'] = '';
								}
								?>
								<p>
								<ul>
									<li>
										<input type="radio" <?php checked( $view_settings['dps']['ajax_results_submit'], 'ajaxed' ); ?> name="wpv-dps-ajax-results-submit" id="wpv-ajax-results-submit-ajaxed" class="js-wpv-ajax-results-submit js-wpv-ajax-results-submit-ajaxed" value="ajaxed" autocomplete="off" />
										<label for="wpv-ajax-results-submit-ajaxed"><?php _e('Update the Views results without reloading the page', 'wpv-views'); ?></label>
									</li>
									<li>
										<input type="radio" <?php checked( $view_settings['dps']['ajax_results_submit'], 'reload' ); ?> name="wpv-dps-ajax-results-submit" id="wpv-ajax-results-submit-reload" class="js-wpv-ajax-results-submit js-wpv-ajax-results-submit-reload" value="reload" autocomplete="off" />
										<label for="wpv-ajax-results-submit-reload"><?php _e('Reload the page to update the View results', 'wpv-views'); ?></label>
									</li>
								</ul>
								</p>
							</div>
						</li>
						<li>
							<input type="radio" <?php checked( $view_settings['dps']['ajax_results'], 'enable' ); ?> value="enable" id="wpv-dps-ajax-results-enable" class="js-wpv-dps-ajax-results js-wpv-dps-ajax-results-enable" name="wpv-dps-ajax-results" autocomplete="off" />
							<label for="wpv-dps-ajax-results-enable"><?php _e('Update the View results every time an input changes', 'wpv-views'); ?></label>
						</li>
					</ul>
					<div class="wpv-ajax-results-details js-wpv-ajax-extra-callbacks"<?php if ( $view_settings['dps']['ajax_results'] != 'enable' && $view_settings['dps']['ajax_results_submit'] == 'reload' ) { echo ' style="display:none"'; } ?>>
						<h4><?php _e('Javascript settings', 'wpv-views'); ?></h4>
						<p>
							<?php _e('You can execute custom javascript functions before and after the View results are updated:', 'wpv-views'); ?>
						</p>
						<ul>
							<li>
								<input type="text" id="wpv-dps-ajax-results-pre-before" class="js-wpv-dps-ajax-results-pre-before" name="wpv-dps-ajax-results-pre-before" value="<?php echo ( isset( $view_settings['dps']['ajax_results_pre_before'] ) ) ? esc_attr( $view_settings['dps']['ajax_results_pre_before'] ) : ''; ?>" autocomplete="off" />
								<label for="wpv-dps-ajax-results-pre-before"><?php _e('will run before getting the new results', 'wpv-views'); ?></label>
							</li>
							<li>
								<input type="text" id="wpv-dps-ajax-results-before" class="js-wpv-dps-ajax-results-before" name="wpv-dps-ajax-results-before" value="<?php echo ( isset( $view_settings['dps']['ajax_results_before'] ) ) ? esc_attr( $view_settings['dps']['ajax_results_before'] ) : ''; ?>" autocomplete="off" />
								<label for="wpv-dps-ajax-results-before"><?php _e('will run after getting the new results, but before updating them', 'wpv-views'); ?></label>
							</li>
							<li>
								<input type="text" id="wpv-dps-ajax-results-after" class="js-wpv-dps-ajax-results-after" name="wpv-dps-ajax-results-after" value="<?php echo ( isset( $view_settings['dps']['ajax_results_after'] ) ) ? esc_attr( $view_settings['dps']['ajax_results_after'] ) : ''; ?>" autocomplete="off" />
								<label for="wpv-dps-ajax-results-after"><?php _e('will run after updating the results', 'wpv-views'); ?></label>
							</li>
						</ul>
						
					</div>
					<h4><?php _e('Which options to display in the form inputs', 'wpv-views'); ?></h4>
					<?php
					if ( ! isset( $view_settings['dps']['enable_dependency'] ) ) {
						$view_settings['dps']['enable_dependency'] = '';
					}
					?>
					<p class="toolset-alert toolset-alert-info js-wpv-dps-intersection-fail<?php if ( count( $no_intersection ) == 0 ) echo ' hidden'; ?>">
						<?php
						$glue = __( ' and ', 'wpv-views' );
						$no_intersection_text = implode( $glue , $no_intersection );
						echo sprintf( __( 'Your %s filters are using an internal "OR" kind of relationship, and dependant parametric search for those filters needs "AND" relationships.', 'wpv-views' ), $no_intersection_text );
						?>
						<br /><br />
						<button class="button-secondary js-make-intersection-filters" data-nonce="<?php echo wp_create_nonce( 'wpv_view_make_intersection_filters' ); ?>" data-cf="<?php echo ( in_array( 'cf', $no_intersection ) ) ? 'true' : 'false'; ?>" data-tax="<?php echo ( in_array( 'tax', $no_intersection ) ) ? 'true' : 'false'; ?>">
							<?php _e('Fix filters relationship', 'wpv-views'); ?>
						</button>
					</p>
					<div class="js-wpv-dps-intersection-ok<?php if ( count( $no_intersection ) > 0 ) echo ' hidden'; ?>">
						<ul>
							<li>
								<input type="radio" <?php checked( $view_settings['dps']['enable_dependency'], 'disable' ); ?> value="disable" id="wpv-dps-enable-disable" class="js-wpv-dps-enable js-wpv-dps-enable-disable" name="wpv-dps-enable" autocomplete="off" />
								<label for="wpv-dps-enable-disable"><?php _e('Always show all values for inputs', 'wpv-views'); ?></label>
							</li>
							<li>
								<input type="radio" <?php checked( $view_settings['dps']['enable_dependency'], 'enable' ); ?> value="enable" id="wpv-dps-enable-enable" class="js-wpv-dps-enable js-wpv-dps-enable-enable" name="wpv-dps-enable" autocomplete="off" />
								<label for="wpv-dps-enable-enable"><?php _e('Show only available options for each input', 'wpv-views'); ?></label>
							</li>
						</ul>
						<div class="wpv-dps-crossed-details js-wpv-dps-crossed-details"<?php if ( $view_settings['dps']['enable_dependency'] != 'enable' ) { echo ' style="display:none"'; } ?>>
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
										<?php 
										if ( ! isset( $view_settings['dps']['empty_select'] ) ) {
											$view_settings['dps']['empty_select'] = '';
										}
										?>
										<td>
											<?php _e('Select dropdown', 'wpv-views'); ?>
										</td>
										<td>
											<input type="radio" <?php checked( $view_settings['dps']['empty_select'], 'disable' ); ?> id="wpv-dps-empty-select-disable" value="disable" class="js-wpv-dps-empty-select" name="wpv-dps-empty-select" autocomplete="off" />
											<label for="wpv-dps-empty-select-disable"><?php _e('Disable', 'wpv-views'); ?></label>
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<input type="radio" <?php checked( $view_settings['dps']['empty_select'], 'hide' ); ?> id="wpv-dps-empty-select-hide" value="hide" class="js-wpv-dps-empty-select" name="wpv-dps-empty-select" autocomplete="off" />
											<label for="wpv-dps-empty-select-hide"><?php _e('Hide', 'wpv-views'); ?></label>
										</td>
									</tr>
									<tr>
										<?php 
										if ( ! isset( $view_settings['dps']['empty_multi_select'] ) ) {
											$view_settings['dps']['empty_multi_select'] = '';
										}
										?>
										<td>
											<?php _e('Multi-select', 'wpv-views'); ?>
										</td>
										<td>
											<input type="radio" <?php checked( $view_settings['dps']['empty_multi_select'], 'disable' ); ?> id="wpv-dps-empty-multi-select-disable" value="disable" class="js-wpv-dps-empty-multi-select" name="wpv-dps-empty-multi-select" autocomplete="off" />
											<label for="wpv-dps-empty-multi-select-disable"><?php _e('Disable', 'wpv-views'); ?></label>
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<input type="radio" <?php checked( $view_settings['dps']['empty_multi_select'], 'hide' ); ?> id="wpv-dps-empty-multi-select-hide" value="hide" class="js-wpv-dps-empty-multi-select" name="wpv-dps-empty-multi-select" autocomplete="off" />
											<label for="wpv-dps-empty-multi-select-hide"><?php _e('Hide', 'wpv-views'); ?></label>
										</td>
									</tr>
									<tr class="alternate">
										<?php 
										if ( ! isset( $view_settings['dps']['empty_radios'] ) ) {
											$view_settings['dps']['empty_radios'] = '';
										}
										?>
										<td>
											<?php _e('Radio inputs', 'wpv-views'); ?>
										</td>
										<td>
											<input type="radio" <?php checked( $view_settings['dps']['empty_radios'], 'disable' ); ?> id="wpv-dps-empty-radios-disable" value="disable" class="js-wpv-dps-empty-radios" name="wpv-dps-empty-radios" autocomplete="off" />
											<label for="wpv-dps-empty-radios-disable"><?php _e('Disable', 'wpv-views'); ?></label>
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<input type="radio" <?php checked( $view_settings['dps']['empty_radios'], 'hide' ); ?> id="wpv-dps-empty-radios-hide" value="hide" class="js-wpv-dps-empty-radios" name="wpv-dps-empty-radios" autocomplete="off" />
											<label for="wpv-dps-empty-radios-hide"><?php _e('Hide', 'wpv-views'); ?></label>
										</td>
									</tr>
									<tr>
										<?php 
										if ( ! isset( $view_settings['dps']['empty_checkboxes'] ) ) {
											$view_settings['dps']['empty_checkboxes'] = '';
										}
										?>
										<td>
											<?php _e('Checkboxes', 'wpv-views'); ?>
										</td>
										<td>
											<input type="radio" <?php checked( $view_settings['dps']['empty_checkboxes'], 'disable' ); ?> id="wpv-dps-empty-checkboxes-disable" value="disable" class="js-wpv-dps-empty-checkboxes" name="wpv-dps-empty-checkboxes" autocomplete="off" />
											<label for="wpv-dps-empty-checkboxes-disable"><?php _e('Disable', 'wpv-views'); ?></label>
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<input type="radio" <?php checked( $view_settings['dps']['empty_checkboxes'], 'hide' ); ?> id="wpv-dps-empty-checkboxes-hide" value="hide" class="js-wpv-dps-empty-checkboxes" name="wpv-dps-empty-checkboxes" autocomplete="off" />
											<label for="wpv-dps-empty-checkboxes-hide"><?php _e('Hide', 'wpv-views'); ?></label>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div><!-- end .js-wpv-dps-settings -->
		<span class="update-action-wrap auto-update js-wpv-update-action-wrap">
			<span class="js-wpv-message-container"></span>
			<input type="hidden" data-success="<?php echo esc_attr( __('Updated', 'wpv-views') ); ?>" data-unsaved="<?php echo esc_attr( __('Not saved', 'wpv-views') ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_dps_nonce' ); ?>" class="js-wpv-filter-dps-update">
		</span>
	</div>
	<?php
}

add_action( 'view-editor-section-filter', 'add_view_filter_extra', 35, 2 );

function add_view_filter_extra( $view_settings, $view_id ) {
	$is_section_hidden = false;
	if ( isset( $view_settings['sections-show-hide'] )
		&& isset( $view_settings['sections-show-hide']['filter-extra'] )
		&& 'off' == $view_settings['sections-show-hide']['filter-extra'] )
	{
		$is_section_hidden = true;
	}
	$hidden_class = $is_section_hidden ? 'hidden' : '';

	/* An additional class js-wpv-filter-extra-section was added to the container div, so we can be sure we can
	 * distinguish it in JS.
	 *
	 * Since 1.7 we're showing the 'content' (Filter and Loop Output Integration, see add_view_content()) section on View
	 * edit page at the same time as this one, so they have to share the "js-wpv-settings-filter-extra" class (because
	 * in Screen options we're changing visibility of elements with "js-wpv-settings-{$section_name}").
	 *
	 * So, in case you need to select this particular element in JS, please use the "js-wpv-filter-extra-section" class,
	 * which is unique.
	 */ 
	$section_help_pointer = WPV_Admin_Messages::edit_section_help_pointer( 'filters_html_css_js' );
	?>
	<div class="wpv-setting-container wpv-setting-container-horizontal wpv-settings-filter-markup js-wpv-settings-filter-extra js-wpv-filter-extra-section <?php echo $hidden_class; ?>">

		<div class="wpv-settings-header">
			<h3>
				<?php _e( 'Filter', 'wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip" 
					data-header="<?php echo esc_attr( $section_help_pointer['title'] ); ?>" 
					data-content="<?php echo esc_attr( $section_help_pointer['content'] ); ?>">
				</i>
			</h3>
		</div>
		<?php
		$listing = '';
		$purpose = 'full';
		if ( 
			isset( $view_settings['query_type'] ) 
			&& is_array( $view_settings['query_type'] ) 
			&& in_array( 'posts', $view_settings['query_type'] ) 
		) {
			$listing = 'posts';
		}
		if ( isset( $view_settings['view_purpose'] ) ) {
			$purpose = $view_settings['view_purpose'];
		}
		
		$controls_per_kind = wpv_count_filter_controls( $view_settings );
		if ( 
			isset( $controls_per_kind['missing'] ) 
			&& is_array( $controls_per_kind['missing'] ) 
			&& ! empty( $controls_per_kind['missing'] ) 
		) {
		?>
		<div class="toolset-help js-wpv-missing-filter-container"<?php echo $listing == 'posts' ? '' : ' style="display:none"'; ?>">
			<div class="toolset-help-content">
				<?php
				_e( 'This View has some query filters that are missing from the form. Maybe you have removed them:', 'wpv-views' );
				?>
				<ul class="js-wpv-filter-missing">
				<?php
				foreach ( $controls_per_kind['missing'] as $missed ) {
					?>
					<li class="js-wpv-missing-filter" data-type="<?php echo esc_attr( $missed['type'] ); ?>" data-name="<?php echo esc_attr( $missed['name'] ); ?>">
						<?php
						echo sprintf( __( 'Filter by <strong>%s</strong>', 'wpv-views' ), $missed['name'] );
						?>
					</li>
					<?php
				}
				?>
				</ul>
				<?php
				_e( 'Can they also be removed from the query filtering?', 'wpv-views' );
				?>
				<p>
					<a href="#" class="button button-primary js-wpv-filter-missing-delete" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_missing_delete' ); ?>"><?php _e( 'Yes (recommended)', 'wpv-views' ); ?></a> <a href="#" class="button button-secondary js-wpv-filter-missing-close"><?php _e( 'No', 'wpv-views' ); ?></a>
				</p>
			</div>
			<div class="toolset-help-sidebar">
				<div class="toolset-help-sidebar-ico"></div>
			</div>
		</div>
		<?php
		} else {
		?>
		<div class="toolset-help js-wpv-missing-filter-container" style="display:none"></div>
		<?php
		}
		
		$controls_count = 0;
		$controls_count = $controls_per_kind['cf'] + $controls_per_kind['tax'] + $controls_per_kind['pr'] + $controls_per_kind['search'];
		?>
		<div class="toolset-alert js-wpv-no-filters-container"<?php if ( $listing == 'posts' && $purpose == 'parametric' ) { if ( $controls_count != 0 ) { echo ' style="display:none"'; } } else { echo ' style="display:none"'; } ?>">
			<p>
				<?php _e('Remember to add filters here. Right now, this parametric search has no filter items.', 'wpv-views'); ?>
			</p>
		</div>

		<div class="wpv-setting">

			<div class="js-error-container js-wpv-parametric-error-container"></div>
			<div class="code-editor js-code-editor filter-html-editor" data-name="filter-html-editor" >
				<div class="code-editor-toolbar js-code-editor-toolbar">
					<ul class="js-wpv-filter-edit-toolbar">
						<?php 
						do_action( 'wpv_parametric_search_buttons', 'wpv_filter_meta_html_content' );
						do_action( 'wpv_views_fields_button', 'wpv_filter_meta_html_content' );
						?>
						<li class="js-editor-pagination-button-wrapper">
							<button class="button-secondary js-code-editor-toolbar-button js-wpv-pagination-popup" data-content="wpv_filter_meta_html_content">
								<i class="icon-pagination"></i>
								<span class="button-label"><?php _e('Pagination controls','wpv-views'); ?></span>
							</button>
						</li>
						<li>
							<button class="button-secondary js-code-editor-toolbar-button js-wpv-media-manager" data-id="<?php echo esc_attr( $view_id ); ?>" data-content="wpv_filter_meta_html_content">
								<i class="icon-picture"></i>
								<span class="button-label"><?php _e('Media','wpv-views'); ?></span>
							</button>
						</li>
					</ul>
				</div>
				<textarea cols="30" rows="10" id="wpv_filter_meta_html_content" autocomplete="off" name="_wpv_settings[filter_meta_html]"><?php echo ( isset( $view_settings['filter_meta_html'] ) ) ? esc_textarea( $view_settings['filter_meta_html'] ) : ''; ?></textarea>
				<?php
				wpv_formatting_help_filter();
				?>
			</div>
			
			<ul id="wpv-filter-meta-html-extra" class="wpv-filter-meta-html-extra js-wpv-filter-meta-html-extra">
				<li class="wpv-has-itembox-header js-wpv-filter-meta-html-extra-item js-wpv-filter-meta-html-extra-css">
					<?php 
					$filter_extra_css = isset( $view_settings['filter_meta_html_css'] ) ? $view_settings['filter_meta_html_css'] : '';
					?>
					<div class="wpv-filter-meta-html-extra-header wpv-itembox-header">
						<strong>
							<?php
							_e( 'CSS editor', 'wpv-views' );
							?>
						</strong>
						<button class="button button-secondary button-small wpv-code-editor-toggler js-wpv-code-editor-toggler" data-kind="css" data-target="filter-css-editor">
							<i class="icon-pushpin js-wpv-textarea-full" style="<?php if ( empty( $filter_extra_css ) ) { echo ' display:none;'; } ?>"></i>
							<span class="js-wpv-text-holder"><?php _e( 'Open CSS editor', 'wpv-views' ) ?></span>
						</button>
					</div>
					<div class="code-editor filter-css-editor wpv-code-editor-closed js-wpv-code-editor js-wpv-filter-css-editor js-wpv-code-editor-closed hidden">
						<div class="code-editor-toolbar js-code-editor-toolbar">
							<ul>
								<li>
									<button class="button-secondary js-code-editor-toolbar-button js-wpv-media-manager" data-id="<?php echo esc_attr( $view_id ); ?>" data-content="wpv_filter_meta_html_css">
										<i class="icon-picture"></i>
										<span class="button-label"><?php _e('Media','wpv-views'); ?></span>
									</button>
								</li>
							</ul>
						</div>
						<textarea cols="30" rows="10" id="wpv_filter_meta_html_css" autocomplete="off" name="_wpv_settings[filter_meta_html_css]"><?php echo esc_textarea( $filter_extra_css ); ?></textarea>
						<?php
						wpv_formatting_help_extra_css( 'filter' );
						?>
					</div>
				</li>
				<li class="wpv-has-itembox-header js-wpv-filter-meta-html-extra-item js-wpv-filter-meta-html-extra-js">
					<?php 
					$filter_extra_js = isset( $view_settings['filter_meta_html_js'] ) ? $view_settings['filter_meta_html_js'] : '';
					?>
					<div class="wpv-filter-meta-html-extra-header wpv-itembox-header">
						<strong>
							<?php
							_e( 'JS editor', 'wpv-views' );
							?>
						</strong>
						<button class="button button-secondary button-small wpv-code-editor-toggler js-wpv-code-editor-toggler" data-kind="js" data-target="filter-js-editor">
							<i class="icon-pushpin js-wpv-textarea-full" style="<?php if ( empty( $filter_extra_js ) ) { echo ' display:none;'; } ?>"></i>
							<span class="js-wpv-text-holder"><?php _e( 'Open JS editor', 'wpv-views' ) ?></span>
						</button>
					</div>
					<div class="code-editor filter-js-editor wpv-code-editor-closed js-wpv-code-editor js-wpv-filter-js-editor js-wpv-code-editor-closed hidden">
						<div class="code-editor-toolbar js-code-editor-toolbar">
							<ul>
								<li>
									<button class="button-secondary js-code-editor-toolbar-button js-wpv-media-manager" data-id="<?php echo esc_attr( $view_id ); ?>" data-content="wpv_filter_meta_html_js">
										<i class="icon-picture"></i>
										<span class="button-label"><?php _e('Media','wpv-views'); ?></span>
									</button>
								</li>
							</ul>
						</div>
						<textarea cols="30" rows="10" id="wpv_filter_meta_html_js" autocomplete="off" name="_wpv_settings[filter_meta_html_js]"><?php echo esc_textarea( $filter_extra_js ); ?></textarea>
						<?php
						wpv_formatting_help_extra_js( 'filter' );
						?>
					</div>
				</li>
			</ul>

			<p class="update-button-wrap js-wpv-update-button-wrap">
				<span class="js-wpv-message-container"></span>
				<button data-success="<?php echo esc_attr( __('Updated', 'wpv-views') ); ?>" data-unsaved="<?php echo esc_attr( __('Not saved', 'wpv-views') ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_extra_nonce' ); ?>" class="js-wpv-filter-extra-update button-secondary" disabled="disabled"><?php _e('Update', 'wpv-views'); ?></button>
			</p>
		</div>

	</div>
	
	<div id="js-wpv-parametric-search-dialogs" class="popup-window-container"> <!-- Use this element as a container for all popup windows. This element is hidden. -->
		
		<!-- Dialogs for settings when inserting shortcodes -->
		
		<div class="wpv-dialog wpv-dialog-parametric-filter wpv-dialog-search-box js-search-box-dialog"> <!-- Popup for the search box addition -->
			
			<div class="wpv-dialog-header">
				<h2><?php _e( 'Create a search box for this parametric search', 'wpv-views' ); ?></h2>
				<i class="icon-remove js-dialog-close"></i>
			</div>
			
			<div class="js-search_shortcode_label-wrap wpv-dialog-content">
				<h3>
					<?php _e( 'Where to search', 'wpv-views' ); ?>
				</h3>
				<p>
					<input value="full_content" name="wpv-post-search-options-dialog" class="js-wpv-post-search-options-dialog" checked="checked" id="search-shortcode-full" type="radio">
					<label for="search-shortcode-full" class="label-alignleft"><?php _e('Post content and title', 'wpv-views'); ?></label>
				</p>
				<p>
					<input value="just_title" name="wpv-post-search-options-dialog" class="js-wpv-post-search-options-dialog" id="search-shortcode-title" type="radio">
					<label for="search-shortcode-title" class="label-alignleft"><?php _e('Just post title', 'wpv-views'); ?></label>
				</p>
			</div>
			
			<div class="js-errors-in-parametric-box"></div>
			
			<div class="wpv-dialog-footer">
				<button class="button js-dialog-close" id="js_parametric_cancel"><?php _e('Cancel','wpv-views') ?></button>
				<button class="button-primary js-code-editor-toolbar-button js-parametric-add-search-box"><?php _e('Insert search box','wpv-views') ?></button>
			</div>
			
		</div> <!-- End of popup for the search box addition -->
		
		<div class="wpv-dialog wpv-dialog-parametric-filter wpv-dialog-search-override js-search-override-dialog"> <!-- Popup for the search override -->
			<div class="wpv-dialog-header">
				<h2><?php _e( 'Complete the search filter for this parametric search', 'wpv-views' ); ?></h2>
				<i class="icon-remove js-dialog-close"></i>
			</div>
			
			<div class="js-search_shortcode_override_label-wrap wpv-dialog-content">
				<h3><?php _e( 'Complete the search filter', 'wpv-views' ); ?></h3>
				<p class="js-wpv-search-filter-override-var js-wpv-search-filter-override-valid">
				<?php _e('This View already has a valid search filter, but it is missing the search box.', 'wpv-views' ); ?>
				</p>
				<p class="js-wpv-search-filter-override-var js-wpv-search-filter-override-valid">
				<?php _e( 'You can override the filter settings and add the search box here.', 'wpv-views'); ?>
				</p>
				<p class="js-wpv-search-filter-override-var js-wpv-search-filter-override-specific">
				<?php _e('This View already has a filter set to filter by a specific string.', 'wpv-views' ); ?>
				</p>
				<p class="js-wpv-search-filter-override-var js-wpv-search-filter-override-specific">
				<?php _e( 'You can fix this filter here.', 'wpv-views'); ?>
				</p>
				<p class="js-wpv-search-filter-override-var js-wpv-search-filter-override-missing">
				<?php _e('This View already has a content search box, but the relevant filter is missing.', 'wpv-views' ); ?>
				</p>
				<p class="js-wpv-search-filter-override-var js-wpv-search-filter-override-missing">
				<?php _e( 'You can add this filter here.', 'wpv-views'); ?>
				</p>
				
				<h3>
					<?php _e( 'Where to search', 'wpv-views' ); ?>
				</h3>
				<p>
					<input value="full_content" name="wpv-post-search-override-dialog" class="js-wpv-post-search-override-dialog" checked="checked" id="search-override-full" type="radio">
					<label for="search-override-full" class="label-alignleft"><?php _e('Post content and title', 'wpv-views'); ?></label>
				</p>
				<p>
					<input value="just_title" name="wpv-post-search-override-dialog" class="js-wpv-post-search-override-dialog" id="search-override-title" type="radio">
					<label for="search-override-title" class="label-alignleft"><?php _e('Just post title', 'wpv-views'); ?></label>
				</p>
			</div>
			
			<div class="wpv-dialog-footer">
				<button class="button js-dialog-close" id="js_parametric_cancel"><?php _e('Cancel','wpv-views') ?></button>
				<button class="button-primary js-code-editor-toolbar-button js-parametric-add-search-override"><?php _e('Create search filter','wpv-views') ?></button>
			</div>
		</div> <!-- End of popup for the search override -->
		
		<div class="wpv-dialog wpv-dialog-parametric-filter wpv-dialog-submit-button js-submit-button-dialog"> <!-- Popup for the submit button addition -->
			
			<div class="wpv-dialog-header">
				<h2><?php _e( 'Create a submit button for this parametric search.', 'wpv-views' ); ?></h2>
				<i class="icon-remove js-dialog-close"></i>
			</div>
			
			<div class="js-submit_shortcode_label-wrap wpv-dialog-content">
				<p>
					<label for="submit_shortcode_label" class="label-alignleft"><?php _e('Button label:', 'wpv-views'); ?></label>
					<input value="<?php echo esc_attr( __('Search', 'wpv-views') ); ?>" id="submit_shortcode_label" type="text">
					<!--<span class="wpv-helper-text">lorem</span>-->
				</p>
				<p>
					<label for="submit_shortcode_button_classname" class="label-alignleft"><?php _e('Button classname:', 'wpv-views'); ?></label>
					<input value="" id="submit_shortcode_button_classname" type="text">
					<span class="wpv-helper-text"><?php _e( 'Use this to add your own styling', 'wpv-views' ); ?></span>
				</p>
				<p>
					<label for="submit_shortcode_button_tag" class="label-alignleft"><?php _e('Button HTML tag:', 'wpv-views'); ?></label>
					<select id="submit_shortcode_button_tag">
						<option selected=·selected· value="input"><?php _e( 'Input', 'wpv-views' ); ?></option>
						<option value="button"><?php _e( 'Button', 'wpv-views' ); ?></option>
					</select>
					<span class="wpv-helper-text"><?php _e( 'You can use an input or a button', 'wpv-views' ); ?></span>
				</p>
			</div>
			
			<div class="js-errors-in-parametric-box"></div>
			
			<div class="wpv-dialog-footer">
				<button class="button js-dialog-close" id="js_parametric_cancel"><?php _e('Cancel','wpv-views') ?></button>
				<button class="button-primary js-code-editor-toolbar-button js-parametric-add-submit-short-tag-label"><?php _e('Insert submit button','wpv-views') ?></button>
			</div>
			
		</div> <!-- End of popup for the submit button addition -->
		
		<div class="wpv-dialog wpv-dialog-parametric-filter wpv-dialog-reset-button js-reset-button-dialog"> <!-- Popup for the reset button addition -->
			
			<div class="wpv-dialog-header">
				<h2><?php _e( 'Create a reset button for this parametric search.', 'wpv-views' ); ?></h2>
				<i class="icon-remove js-dialog-close"></i>
			</div>
			
			<div class="js-reset_shortcode_label-wrap wpv-dialog-content">
				<p>
					<label for="reset_shortcode_label" class="label-alignleft"><?php _e('Button label:', 'wpv-views'); ?></label>
					<input value="<?php echo esc_attr( __('Reset', 'wpv-views') ); ?>" id="reset_shortcode_label" type="text">
					<!--<span class="wpv-helper-text">lorem</span>-->
				</p>
				<p>
					<label for="reset_shortcode_button_classname" class="label-alignleft"><?php _e('Button classname:', 'wpv-views'); ?></label>
					<input value="" id="reset_shortcode_button_classname" type="text">
					<span class="wpv-helper-text"><?php _e( 'Use this to add your own styling', 'wpv-views' ); ?></span>
				</p>
				<p>
					<label for="reset_shortcode_button_tag" class="label-alignleft"><?php _e('Button HTML tag:', 'wpv-views'); ?></label>
					<select id="reset_shortcode_button_tag">
						<option selected=·selected· value="input"><?php _e( 'Input', 'wpv-views' ); ?></option>
						<option value="button"><?php _e( 'Button', 'wpv-views' ); ?></option>
					</select>
					<span class="wpv-helper-text"><?php _e( 'You can use an input or a button', 'wpv-views' ); ?></span>
				</p>
			</div>
			
			<div class="js-errors-in-parametric-box"></div>
			
			<div class="wpv-dialog-footer">
				<button class="button js-dialog-close" id="js_parametric_cancel"><?php _e('Cancel','wpv-views') ?></button>
				<button class="button-primary js-code-editor-toolbar-button js-parametric-add-reset-short-tag-label"><?php _e('Insert clear form button','wpv-views') ?></button>
			</div>
			
		</div> <!-- End of popup for the reset button addition -->
		
		<div class="wpv-dialog wpv-dialog-parametric-filter wpv-dialog-spinner-button js-spinner-button-dialog"> <!-- Popup for the spinner button addition -->
			
			<div class="wpv-dialog-header">
				<h2><?php _e( 'Create a spinner container for this parametric search.', 'wpv-views' ); ?></h2>
				<i class="icon-remove js-dialog-close"></i>
			</div>
			
			<div class="js-spinner_shortcode_label-wrap wpv-dialog-content">
				<p>
					<label for="spinner_shortcode_container_type" class="label-alignleft"><?php _e('Container type:', 'wpv-views'); ?></label>
					<select id="spinner_shortcode_container_type">
						<option value="div"><?php _e('Division', 'wpv-views'); ?></option>
						<option value="p"><?php _e('Paragraph', 'wpv-views'); ?></option>
						<option value="span"><?php _e('Span', 'wpv-views'); ?></option>
					</select>
					<span class="wpv-helper-text"><?php _e( 'You can display your spinner inside different kind of HTML elements', 'wpv-views' ); ?></span>
				</p>
				<p>
					<label for="spinner_shortcode_container_classname" class="label-alignleft"><?php _e('Container classname:', 'wpv-views'); ?></label>
					<input value="" id="spinner_shortcode_container_classname" type="text">
					<span class="wpv-helper-text"><?php _e( 'Use this to add your own styling', 'wpv-views' ); ?></span>

					<label for="spinner_shortcode_spinner_position" class="label-alignleft"><?php _e('Spinner placement:', 'wpv-views'); ?></label>
					<select id="spinner_shortcode_spinner_position">
						<option value="none"><?php _e('Do not show the spinner', 'wpv-views'); ?></option>
						<option value="before"><?php _e('Before the text', 'wpv-views'); ?></option>
						<option value="after"><?php _e('After the text', 'wpv-views'); ?></option>
					</select>
					<span class="wpv-helper-text"><?php _e( 'Whether the spinner should be added at the beginning or the end of the container', 'wpv-views' ); ?></span>
				</p>
				<p>
					<label for="spinner_shortcode_spinner_image" class="label-alignleft"><?php _e('Spinner image:', 'wpv-views'); ?></label>
					<ul style="overflow:hidden">
					<?php
					$has_spinner_image_checked = false;
					$available_spinners = array();
					$available_spinners = apply_filters( 'wpv_admin_available_spinners', $available_spinners );
					foreach ( $available_spinners as $av_spinner ) {
						?>
						<li style="min-width:49%;float:left;">
							<label>
								<input type="radio" class="js-wpv-ps-spinner-image" name="wpv-dps-spinner-image" value="<?php echo esc_url( $av_spinner['url'] ); ?>" <?php if ( ! $has_spinner_image_checked ) { echo ' checked="checked"'; } ?> />
								<img src="<?php echo esc_url( $av_spinner['url'] ); ?>" title="<?php echo esc_attr( $av_spinner['title'] ); ?>" />
							</label>
						</li>
						<?php 
						$has_spinner_image_checked = true;
					};
					?>
					</ul>
					<!--<span class="wpv-helper-text">lorem</span>-->
				</p>
				<p>
					<label for="spinner_shortcode_content" class="label-alignleft"><?php _e('Container text:','wpv-views'); ?></label>
					<textarea id="spinner_shortcode_content"></textarea>
					<span class="wpv-helper-text"><?php _e( 'This will be shown inside the container and along with the spinner', 'wpv-views' ); ?></span>
				</p>
			</div>
			
			<div class="js-errors-in-parametric-box"></div>
			
			<div class="wpv-dialog-footer">
				<button class="button js-dialog-close" id="js_parametric_cancel"><?php _e('Cancel','wpv-views') ?></button>
				<button class="button-primary js-code-editor-toolbar-button js-parametric-add-spinner-short-tag-label"><?php _e('Insert spinner container','wpv-views') ?></button>
			</div>
			
		</div> <!-- End of popup for the spinner button addition -->
		
		<!-- Pointer contents -->
		
		
	
	</div><!-- popup-window-container end -->
<?php }

add_action( 'wp_ajax_wpv_filter_update_dps_settings', 'wpv_filter_update_dps_settings' );

function wpv_filter_update_dps_settings() {
	if ( ! current_user_can( 'manage_options' ) ) {
		$data = array(
			'type' => 'capability',
			'message' => __( 'You do not have permissions for that.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	if ( 
		! isset( $_POST["wpnonce"] )
		|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_filter_dps_nonce' ) 
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
	$view_array = get_post_meta( $_POST["id"], '_wpv_settings', true );
	if ( ! isset( $view_array['dps'] ) ) {
		$view_array['dps'] = array();
	}
	if ( isset( $_POST['dpsdata'] ) ) {
		$passed_data = wp_parse_args( $_POST['dpsdata'] );
		// Helper mode
		if ( 
			isset( $passed_data['wpv-dps-mode-helper'] ) 
			&& in_array( $passed_data['wpv-dps-mode-helper'], array( 'fullrefreshonsubmit', 'ajaxrefreshonsubmit', 'ajaxrefreshonchange', 'custom' ) ) 
		) {
			$view_array['dps']['mode_helper'] = $passed_data['wpv-dps-mode-helper'];
		}
		// AJAX update View results
		if ( 
			isset( $passed_data['wpv-dps-ajax-results'] ) 
			&& $passed_data['wpv-dps-ajax-results'] == 'enable' 
		) {
			$view_array['dps']['ajax_results'] = 'enable';
		} else {
			$view_array['dps']['ajax_results'] = 'disable';
		}
		if ( isset( $passed_data['wpv-dps-ajax-results-pre-before'] ) ) {
			$view_array['dps']['ajax_results_pre_before'] = esc_attr( $passed_data['wpv-dps-ajax-results-pre-before'] );
		} else {
			$view_array['dps']['ajax_results_pre_before'] = '';
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
		if ( 
			isset( $passed_data['wpv-dps-ajax-results-submit'] ) 
			&& in_array( $passed_data['wpv-dps-ajax-results-submit'], array( 'ajaxed', 'reload' ) ) 
		) {
			$view_array['dps']['ajax_results_submit'] = $passed_data['wpv-dps-ajax-results-submit'];
		} else {
			$view_array['dps']['ajax_results_submit'] = 'reload';
		}
		// Enable dependency and input defaults
		if ( 
			isset( $passed_data['wpv-dps-enable'] ) 
			&& $passed_data['wpv-dps-enable'] == 'disable' 
		) {
			$view_array['dps']['enable_dependency'] = 'disable';
		} else {
			$view_array['dps']['enable_dependency'] = 'enable';
		}
		if ( 
			isset( $passed_data['wpv-dps-empty-select'] ) 
			&& $passed_data['wpv-dps-empty-select'] == 'disable' 
		) {
			$view_array['dps']['empty_select'] = 'disable';
		} else {
			$view_array['dps']['empty_select'] = 'hide';
		}
		if ( 
			isset( $passed_data['wpv-dps-empty-multi-select'] ) 
			&& $passed_data['wpv-dps-empty-multi-select'] == 'disable' 
		) {
			$view_array['dps']['empty_multi_select'] = 'disable';
		} else {
			$view_array['dps']['empty_multi_select'] = 'hide';
		}
		if ( 
			isset( $passed_data['wpv-dps-empty-radios'] ) 
			&& $passed_data['wpv-dps-empty-radios'] == 'disable' 
		) {
			$view_array['dps']['empty_radios'] = 'disable';
		} else {
			$view_array['dps']['empty_radios'] = 'hide';
		}
		if ( 
			isset( $passed_data['wpv-dps-empty-checkboxes'] ) 
			&& $passed_data['wpv-dps-empty-checkboxes'] == 'disable' 
		) {
			$view_array['dps']['empty_checkboxes'] = 'disable';
		} else {
			$view_array['dps']['empty_checkboxes'] = 'hide';
		}
		/*
		Spinners - DEPRECATED, so we might want to clean; keep it for now for backwards compatibility
		$view_array['dps']['spinner'] = 'none';
		$view_array['dps']['spinner_image_uploaded'] = '';
		$view_array['dps']['spinner_image'] = '';
		*/
	} else {
		
	}
	update_post_meta( $_POST["id"], '_wpv_settings', $view_array );
	do_action( 'wpv_action_wpv_save_item', $_POST["id"] );
	$data = array(
		'id' => $_POST["id"],
		'message' => __( 'Parametric Search Settings saved', 'wpv-views' )
	);
	wp_send_json_success( $data );
}

// @todo add proper wp_send_json_error/wp_send_json_success management here

add_action( 'wp_ajax_wpv_get_dps_related', 'wpv_get_dps_related' );

function wpv_get_dps_related() {
	if ( ! current_user_can( 'manage_options' ) ) {
		die( "Security check" );
	}
	if ( ! wp_verify_nonce( $_POST["nonce"], 'wpv_view_edit_general_nonce' ) ) {
		die( "Security check" );
	}
	$return_result = array(
		'existence' => '',
		'intersection' => '',
		'missing' => ''
	);
	if ( isset( $_POST['id'] ) ) {
		global $WP_Views;
		$view_id = (int) $_POST['id'];
		$view_settings = $WP_Views->get_view_settings( $view_id );
		$controls_per_kind = wpv_count_filter_controls( $view_settings );
		$controls_count = $controls_per_kind['cf'] + $controls_per_kind['tax'] + $controls_per_kind['pr'] + $controls_per_kind['search'];
		$no_intersection = array();				
		if ( 
			isset( $controls_per_kind['cf'] ) 
			&& $controls_per_kind['cf'] > 1 
			&& (
				! isset( $view_settings['custom_fields_relationship'] ) 
				|| $view_settings['custom_fields_relationship'] != 'AND' 
			) 
		) {
			$no_intersection[] = __( 'custom field', 'wpv-views' );
		}
		if ( 
			isset( $controls_per_kind['tax'] ) 
			&& $controls_per_kind['tax'] > 1 && ( 
				! isset( $view_settings['taxonomy_relationship'] ) 
				|| $view_settings['taxonomy_relationship'] != 'AND' 
			) 
		) {
			$no_intersection[] = __( 'taxonomy', 'wpv-views' );
		}
		// Existence
		if ( $controls_count == 0 ) {
			$return_result['existence'] = '<p>' . __('Remember to add filters here. Right now, this parametric search has no filter items.', 'wpv-views') . '</p>';
		}
		// Intersection
		if ( count( $no_intersection ) > 0 ) {
			$glue = __( ' and ', 'wpv-views' );
			$no_intersection_text = implode( $glue , $no_intersection );
			$return_result['intersection'] = sprintf( __( 'Your %s filters are using an internal "OR" kind of relationship, and dependant parametric search for those filters needs "AND" relationships.', 'wpv-views' ), $no_intersection_text );
			$return_result['intersection'] .= '<br /><br />';
			$return_result['intersection'] .= '<button class="button-secondary js-make-intersection-filters" data-nonce="' . wp_create_nonce( 'wpv_view_make_intersection_filters' ) .'"';
			if ( in_array( 'cf', $no_intersection ) ) {
				$return_result['intersection'] .= ' data-cf="true"';
			} else {
				$return_result['intersection'] .= ' data-cf="false"';
			}
			if ( in_array( 'tax', $no_intersection ) ) {
				$return_result['intersection'] .= ' data-tax="true"';
			} else {
				$return_result['intersection'] .= ' data-tax="false"';
			}
			$return_result['intersection'] .= '>';
				$return_result['intersection'] .= __('Fix filters relationship', 'wpv-views');
			$return_result['intersection'] .= '</button>';
		}
		// Missing
		if ( 
			isset( $controls_per_kind['missing'] ) 
			&& is_array( $controls_per_kind['missing'] ) 
			&& ! empty( $controls_per_kind['missing'] ) 
		) {
			$return_result['missing'] = '<div class="toolset-help-content">';
			$return_result['missing'] .= __( 'This View has some query filters that are missing from the form. Maybe you have removed them:', 'wpv-views' );
			$return_result['missing'] .= '<ul class="js-wpv-filter-missing">';
			foreach ( $controls_per_kind['missing'] as $missed ) {
				$return_result['missing'] .= '<li class="js-wpv-missing-filter" data-type="' . $missed['type'] . '" data-name="' . $missed['name'] . '">';
				$return_result['missing'] .= sprintf( __( 'Filter by <strong>%s</strong>', 'wpv-views' ), $missed['name'] );
				$return_result['missing'] .= '</li>';
			}
			$return_result['missing'] .= '</ul>';
			$return_result['missing'] .= __( 'Can they also be removed from the query filtering?', 'wpv-views' );
			$return_result['missing'] .= '<p>';
				$return_result['missing'] .= '<a href="#" class="button button-primary js-wpv-filter-missing-delete" data-nonce="' . wp_create_nonce( 'wpv_view_filter_missing_delete' ) . '">' . __( 'Yes (recommended)', 'wpv-views' ) . '</a> <a href="#" class="button button-secondary js-wpv-filter-missing-close">' . __( 'No', 'wpv-views' ) . '</a>';
			$return_result['missing'] .= '</p>';
			$return_result['missing'] .= '</div>';
			$return_result['missing'] .= '<div class="toolset-help-sidebar"><div class="toolset-help-sidebar-ico"></div></div>';
		}
	}
	echo json_encode( $return_result );
	die();
}

// Filter Extra save callback function - only for Views

add_action( 'wp_ajax_wpv_update_filter_extra', 'wpv_update_filter_extra_callback' );

function wpv_update_filter_extra_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		$data = array(
			'type' => 'capability',
			'message' => __( 'You do not have permissions for that.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	if ( 
		! isset( $_POST["wpnonce"] )
		|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_filter_extra_nonce' ) 
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
	$view_array = get_post_meta( $_POST["id"], '_wpv_settings', true );
	if (
		isset( $_POST["query_val"] )
		&& (
			! isset( $view_array['filter_meta_html'] ) 
			|| $_POST["query_val"] != $view_array['filter_meta_html']
		)
	) {
		$view_array['filter_meta_html'] = $_POST["query_val"];
		wpv_add_controls_labels_to_translation( $_POST["query_val"], $_POST["id"] );
	}
	wpv_register_wpml_strings( $_POST["query_val"] );
	$view_array['filter_meta_html_css'] = $_POST["query_css_val"];
	$view_array['filter_meta_html_js'] = $_POST["query_js_val"];
	if ( isset( $view_array['filter_meta_html_state'] ) ) {
		unset( $view_array['filter_meta_html_state'] );
	}
	update_post_meta( $_POST["id"], '_wpv_settings', $view_array );
	do_action( 'wpv_action_wpv_save_item', $_POST["id"] );
	$data = array(
		'id' => $_POST["id"],
		'message' => __( 'Filter saved', 'wpv-views' )
	);
	wp_send_json_success( $data );
}

// Remove missing filters

add_action( 'wp_ajax_wpv_remove_filter_missing', 'wpv_remove_filter_missing_callback' );

function wpv_remove_filter_missing_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		$data = array(
			'type' => 'capability',
			'message' => __( 'You do not have permissions for that.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	if ( 
		! isset( $_POST["wpnonce"] )
		|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_filter_missing_delete' ) 
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
	$view_array = get_post_meta( $_POST["id"], '_wpv_settings', true );
	if ( 
		isset( $_POST['cf'] ) 
		&& is_array( $_POST['cf'] ) 
	) {
		foreach ( $_POST['cf'] as $field ) {
			$field = sanitize_text_field( $field );
			$to_delete = array(
				'custom-field-' . $field . '_compare',
				'custom-field-' . $field . '_type',
				'custom-field-' . $field . '_value',
				'custom-field-' . $field . '_relationship'
			);
			foreach ( $to_delete as $slug ) {
				if ( isset( $view_array[$slug] ) ) {
					unset( $view_array[$slug] );
				}
			}
		}
	}
	if ( 
		isset( $_POST['tax'] ) 
		&& is_array( $_POST['tax'] ) 
	) {
		foreach ( $_POST['tax'] as $tax_name ) {
			$tax_name = sanitize_text_field( $tax_name );
			$to_delete = array(
					'tax_'.$tax_name.'_relationship' ,
					'taxonomy-'.$tax_name.'-attribute-url',
				//	'taxonomy-'.$tax_name.'-attribute-url-format',
				);
			foreach ( $to_delete as $slug ) {
				if ( isset( $view_array[$slug] ) ) {
					unset( $view_array[$slug] );
				}
			}
		}
	}
	if ( 
		isset( $_POST['rel'] ) 
		&& is_array( $_POST['rel'] ) 
		&& ! empty( $_POST['rel'] ) 
	) {
		$to_delete = array(
			'post_relationship_mode',
			'post_relationship_shortcode_attribute',
			'post_relationship_url_parameter',
			'post_relationship_id',
			'post_relationship_url_tree',
		);

		foreach ( $to_delete as $slug ) {
			if ( isset( $view_array[$slug] ) ) {
				unset( $view_array[$slug] );
			}
		}
	}
	if ( 
		isset( $_POST['search'] ) 
		&& is_array( $_POST['search'] ) 
		&& ! empty( $_POST['search'] ) 
	) {
		$to_delete = array(
			'search_mode',
			'post_search_value',
			'post_search_content',
		);

		foreach ( $to_delete as $slug ) {
			if ( isset( $view_array[$slug] ) ) {
				unset( $view_array[$slug] );
			}
		}
	}
	update_post_meta( $_POST["id"], '_wpv_settings', $view_array );
	do_action( 'wpv_action_wpv_save_item', $_POST["id"] );
	// Filters list
	ob_start();
	wpv_display_filters_list( $view_array['query_type'][0], $view_array );
	$filters_list = ob_get_contents();
	ob_end_clean();
	$data = array(
		'id' => $_POST["id"],
		'updated_filters_list' => $filters_list,
		'message' => __( 'Success', 'wpv-views' )
	);
	wp_send_json_success( $data );
}