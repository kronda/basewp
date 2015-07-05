<div class="wpv-dialog wpv-dialog-parametric-filter" id="js-parametric-form-dialog-container">

	<div class="wpv-dialog-header">
		<h2 id="parametric-box-title"><?php _e('Insert a filter field', 'wpv-views');?></h2>
		<i class="icon-remove js-dialog-close"></i>
	</div>

	<?php /* <div class="wpv-dialog-sidebar filter-preview">
		<p><?php _e('Preview', 'wpv-views');?></p>
		<p class="toolset-alert toolset-alert-info" id="js_wpv_prm_preview" data-bind="html:setPreview">

			<?php _e('Select what to filter to see the preview here', 'wpv-views');?>
		</p>

	</div> */ ?>

	<div id="js-parametric-form-container-inner" class="wpv-dialog-content">

		<form id="js-parametric-form" >

			<fieldset id="js-dialog-form-visible-parametric" class="fieldset-no-border">

				<?php /* <legend id="js_legend_default"><?php _e('Defaults', 'wpv-views');?></legend> */ ?>

				<p>
					<label for="selectFilter" id="js_select_parametric_filter" class="label-alignleft"><?php _e('Select what to filter by :', 'wpv-views');?></label>

					<!-- Main select. We do it this way to group values with <optgroup> not supported otherwise-->
					<select data-bind="foreach: selectFilter, value:fieldRaw" class="js-wpv-select-filter">
					<!-- Set a caption for the select only once-->
					<!-- ko ifnot: $index -->
					   <option value="" id="js-parametric-option-default"><?php _e('--- Please select ---', 'wpv-views');?></option>
					<!-- /ko -->
					    <optgroup data-bind="attr: {label: label}, foreach: children">
					        <option data-bind="text: name,
											   option: $data">
							</option>
					    </optgroup>
					</select>


				</p>

				<hr style="color:#ededed;background:#ededed" />

				<p data-bind="visible:fieldRaw">
					<label for="selectInputKind" id="js-parametric-input-default" class="label-alignleft"><?php _e('Use this kind of input:', 'wpv-views'); ?></label>
					<select data-bind="options: selectInputKind, value:type, optionsCaption: 'Choose input type:'" id="selectInputKind"></select>
					<span class="wpv-helper-text"><?php _e( 'You can use any of those form elements.', 'wpv-views' ); ?></span>
				</p>

				<div data-bind="visible:fieldRaw">

					<div data-bind="visible:ancestors_visible">
						<label for="ancestors" id="js_parametric_ancestors" class="label-alignleft"><?php _e('Post relationship:', 'wpv-views');?></label>
						<select data-bind="foreach: ancestors_array, value:ancestors" id="ancestors">
							<option data-bind="text: title,
											option: value">
							</option>
						</select>
						<span class="wpv-helper-text"><?php _e( 'Select which ancestors should be part of this filter.', 'wpv-views' ); ?></span>
					</div>

					<p data-bind="visible:checkbox_title_visible">
						<label for="title" id="js_parametric_checkbox_title" class="label-alignleft"><?php _e('Checkbox label:', 'wpv-views');?></label>
						<input data-bind='value: title, valueUpdate:"afterkeydown"' id="title" type="text" />
					</p>

					<div data-bind="visible:checkbox_force_zero_visible">
						<?php _e( 'When the checkbox is not checked:', 'wpv-views'); ?>
						<ul>
							<li>
								<input data-bind='checked:force_zero' value='0' id="force_zero_false" type="radio" name="force_zero" />
								<label for="force_zero_false" id="js_parametric_checkbox_force_zero_false"><?php _e('Return all results', 'wpv-views');?></label>
							</li>
							<li>
								<input data-bind='checked:force_zero' value='1' id="force_zero_true" type="radio" name="force_zero" />
								<label for="force_zero_true" id="js_parametric_checkbox_force_zero_true"><?php _e('Return only results with this field unchecked', 'wpv-views');?></label>
							</li>
						</ul>
					</div>

					<p data-bind="visible:default_label_visible">
							<label for="default_label" id="js_parametric_default_label" class="label-alignleft"><?php _e('Label for \'not selected\' option:', 'wpv-views');?></label>
							<input data-bind='value: default_label, valueUpdate:"afterkeydown"' id="default_label" type="text" />
							<span class="wpv-helper-text"><?php _e( 'Besides the list of terms, an additional option will show to not select any term.', 'wpv-views' ); ?></span>
							<span class="wpv-helper-text"><?php _e( 'Choose the label for that option.', 'wpv-views' ); ?></span>
					</p>

					<div data-bind="visible:show_values_settings">

						<p id="js_default_values_from"><?php _e('Options for this input:', 'wpv-views');?></p>

						<ul>
							<li>
								<input type="radio" value="1" data-bind="checked:auto_fill" id="auto_fill_yes" />
								<label for="auto_fill_yes" id="js_parametric_label_existing"><?php _e('Use existing custom field values', 'wpv-views');?> </label>
							</li>

							<li>
								<input type="radio" value="0" data-bind="checked:auto_fill" id="auto_fill_no"/>
								<label for="auto_fill_no" id="js_parametric_manually_entered"><?php _e('Use manually entered values', 'wpv-views');?></label>
							</li>
						</ul>

					</div>

					<table data-bind="visible:userValuesVisible">
						<thead>
							<tr>
								<th></th>
								<th id="js_parametric_values"><?php _e('Values:', 'wpv-views');?></th>
								<th id="js_parametric_possible_values"><?php _e('Display values:', 'wpv-views');?> </th>

							</tr>
						</thead>

						<!--
							"sortable" replaces the foreach binding, making nested elements sortable (obviously) by jquery.sortable.
							See the knockout-sortable script for more information.
						-->
						<tbody data-bind="sortable: { data: values, options: { handle: '.sortable-handle' } }">
							<tr>
								<td>
									<i class="icon-move sortable-handle"></i>
								</td>
								<td>
									<input data-bind='value: $data.values, valueUpdate:"afterkeydown"' class="values js-user-values" type="text" />
								</td>
								<td>
									<input data-bind='value: $data.display_values, valueUpdate:"change"' class="display_values js-user-display-values" type="text" />
								</td>
								<td class="remove-sign-wrap">
									<i class="icon-remove-sign js-action-name" data-bind="click:$root.remove_user_values_box"></i>
								</td>
							</tr>
						</tbody>
					</table>

					<div data-bind="visible: userValuesVisible" class="alignleft">
						<span class="wpv-helper-text"><?php _e( 'A blank value means "all results".', 'wpv-views' ); ?></span>
						<div class="js-user-value-errors errors-in-parametric-box"></div>
					</div>

					<p data-bind="visible:userValuesVisible" class="prm-button-holder alignright" >
						<button data-bind="visible:userValuesVisible, click:add_user_values_box" class="button-secondary"><i class="icon-plus" id="js_parametric_another_value"></i> <?php _e('Add another value', 'wpv-views');?></button>
					</p>

					<div style="clear:both;"></div>

					<p data-bind="visible:auto_fill_default_visible">
						<label for="values" id="js_parametric_possibile_inputs" class="label-alignleft"><?php _e('Default value:', 'wpv-views');?></label>
						<input data-bind='value: auto_fill_default, valueUpdate:"afterkeydown"' id="auto_fill_default" class="js-wpv-auto-fill-default" type="text" placeholder="Please type" />
						<span class="wpv-helper-text"><?php _e( 'Leave blank for no default.', 'wpv-views' ); ?></span>
					</p>

				</div>

				<div id="format_attribute" data-bind="visible: show_values_settings() || taxonomy_order_visible() || ancestors_visible() ">
					<p>
						<label for="format" id="js_format_attribute" class="label-alignleft"><?php _e('Format of the options:', 'wpv-views');?></label>
						<input type="text" id="format" placeholder="%%NAME%%" data-bind='value: format, valueUpdate:"afterkeydown"'>
						<span class="wpv-helper-text"><?php _e( 'By default, the names of the items will be displayed.', 'wpv-views' ); ?></span>
						<span class="wpv-helper-text"><?php _e( 'You can edit and add your text and the count of posts in each item.', 'wpv-views' ); ?></span>
						<span class="wpv-helper-text"><?php _e( 'You can use %%NAME%% and %%COUNT%% as placeholders', 'wpv-views' ); ?></span>
					</p>
				</div>

				<div id="auto_fill_sort_container" data-bind="visible:auto_fill_sort_visible">
					<p>
						<label for="auto_fill_sort" id="auto_fill_sort_label" class="label-alignleft"><?php _e('Order direction for the options:', 'wpv-views');?></label>
						<select data-bind="foreach: selectAutoFillSort, value:auto_fill_sort" id="auto_fill_sort">
							<option data-bind="text: title,
											   option: value">
							</option>
						</select>
					</p>
				</div>

				<div data-bind="visible:taxonomy_order_visible">
					<p>
							<label for="taxonomy_orderby" id="js_parametric_taxonomy_order_by" class="label-alignleft"><?php _e('Order the options by:', 'wpv-views');?></label>
							<select data-bind='foreach: taxonomyOrderBy, value:taxonomy_orderby' id="taxonomy_orderby" type="text">
								<option data-bind="text: title,
											   option: value">
								</option>
							</select>
					</p>

					<p>
							<label for="taxonomy_order" id="js_parametric_taxonomy_order" class="label-alignleft"><?php _e('Order direction:', 'wpv-views');?></label>
							<select data-bind='foreach: taxonomyOrder, value:taxonomy_order' id="taxonomy_order" type="text">
								<option data-bind="text: title,
											   option: value">
								</option>
							</select>
					</p>

					<label id="js_parametric_taxonomy_hide_empty" class="label-alignleft"><?php _e('Terms without any post:', 'wpv-views');?></label>
					<div>
					<input type="radio" value="false" data-bind="checked:hide_empty" id="hide_empty_false" />
					<label for="hide_empty_false" id="js_parametric_label_existing"><?php _e('Include - list terms even if no post uses them', 'wpv-views');?> </label>
					<br />
					<input type="radio" value="true" data-bind="checked:hide_empty" id="hide_empty_true"/>
					<label for="hide_empty_true" id="js_parametric_manually_entered"><?php _e('Exclude - only list terms which are assigned to posts', 'wpv-views');?></label>
					</div>
					<span class="wpv-helper-text"><?php _e( 'Do not show taxonomy terms with no posts attached', 'wpv-views' ); ?></span>
				</div>

				<div id="comparison_function" data-bind="visible: fieldRaw ">
					<p>
						<label for="selectCompare" id="js_parametric_comparison" class="label-alignleft"><?php _e('Comparison function:', 'wpv-views');?></label>
						<select data-bind="options: selectCompare, value: compare" id="selectCompare"></select>
						<!--<span class="wpv-helper-text">IN - results match to search</span>
						<span class="wpv-helper-text">NOT IN - results don’t match the search</span>
						<span class="wpv-helper-text">AND - What does this actually do ????</span>-->
					</p>
				</div>

			</fieldset>

			<fieldset id="js-dialog-form-visibles-hidden" class="dialog-form-hidden" data-bind="visible:fieldRaw">

				<legend class="is-toggle-viz-legend">
					<span id="js_parametric_advanced"><?php _e('Advanced:', 'wpv-views');?></span>
					<span id="js-toggle-advanced-paramentric-form-fields" class="toggle-visibility-fieldset">
						<?php _e('Expand', 'wpv-views'); ?>
						<i class="icon-caret-down"></i>
					</span>
				</legend>

				<div class="hidden js-hidden-fields-container">

					<div data-bind="foreach:url_param">
						<p>
							<label for="field" id="js_parametric_url_param" class="label-alignleft"><?php _e( 'Refer to this field as:', 'wpv-views' );?></label>
							<input data-bind='value: $data.value, valueUpdate:"afterkeydown"' id="url_param" type="text">
							<span class="wpv-helper-text"><?php _e( 'This is the URL parameter that will be used to post the filter values.', 'wpv-views' ); ?></span>
						</p>
					</div>
					<p>
						<label for="selectDataType" id="js_parametric_compare_as" class="label-alignleft"><?php _e('Compare this values as:', 'wpv-views');?></label>
						<select data-bind="options: selectDataType, value:field_data_type" id="selectDataType"></select>
						<span class="wpv-helper-text"><?php _e( 'Compare the posted values with the existing ones as this type of data.', 'wpv-views' ); ?></span>
					</p>
				</div>

			</fieldset>

		</form>

		<div class="js-errors js-wpv-parametric-dialog-toolset-messages errors-in-parametric-box"></div>

	</div> <!-- .wpv-dialog-content -->

    <div class="wpv-dialog-footer wp-core-ui">
        <button class="button js-dialog-close" id="js_parametric_cancel"><?php _e('Cancel', 'wpv-views');?></button>
        <button class="button button-primary" id="js_parametric_form_button" disabled><?php _e('Insert input', 'wpv-views');?></button>
    </div>

</div>  <!-- .wpv-dialog -->
