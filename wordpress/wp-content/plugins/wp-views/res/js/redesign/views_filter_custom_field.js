jQuery(document).ready(function(){
	// filter compare: init and change
	wpv_custom_field_initialize_compare();

	// filter compare mode: init and change
	wpv_custom_field_initialize_compare_mode();

	// filter relationship
	wpv_custom_field_initialize_relationship();
})

function wpv_custom_field_initialize_compare() {
	var wpv_allowed_values = 0;
	jQuery('.js-filter-list .js-wpv-custom-field-compare-select').each(function(){
		var wpv_single_row = jQuery(this).parents('.js-filter-row-multiple-element');
		wpv_clear_validate_messages('.js-filter-list .js-filter-custom-field');
		jQuery(this).parents('.js-filter-custom-field').find('.filter-input-error').removeClass('filter-input-error');
		if (jQuery(this).val() == 'BETWEEN' || jQuery(this).val() == 'NOT BETWEEN') {
			wpv_allowed_values = 2;
			jQuery(this).parents('.js-filter-row-multiple-element').find('.js-wpv-custom-field-add-value').addClass('hidden');
			jQuery(this).parents('.js-filter-row-multiple-element').find('.js-wpv-custom-field-remove-value').addClass('hidden');
			divs = jQuery(this).parents('.js-filter-row-multiple-element').find('.js-wpv-custom-field-value-div');
			if (divs.length < 2) {
				// add another one.
				var clone = jQuery(divs[0]).clone();
				clone.find('.js-wpv-custom-field-value-text').val('');
				clone.insertAfter(divs[0]);
				wpv_custom_field_initialize_compare_mode();
			}
		} else if (jQuery(this).val() == 'IN' || jQuery(this).val() == 'NOT IN') {
			wpv_allowed_values = 100000;
			jQuery(this).parents('.js-filter-row-multiple-element').find('.js-wpv-custom-field-add-value').removeClass('hidden');
			jQuery(this).parents('.js-filter-row-multiple-element').find('.js-wpv-custom-field-value-div').each(function(index) {
				if (index > 0) {
					jQuery(this).find('.js-wpv-custom-field-remove-value').removeClass('hidden');
				} else {
					jQuery(this).find('.js-wpv-custom-field-remove-value').addClass('hidden');
				}
			});
		} else {
			wpv_allowed_values = 1;
			jQuery(this).parents('.js-filter-row-multiple-element').find('.js-wpv-custom-field-add-value').addClass('hidden');
			jQuery(this).parents('.js-filter-row-multiple-element').find('.js-wpv-custom-field-remove-value').addClass('hidden');
		}
		jQuery(this).parents('.js-filter-row-multiple-element').find('.js-wpv-custom-field-value-div').each(function() {
			if (wpv_allowed_values > 0) {
				jQuery(this).removeClass('hidden');
			} else {
				jQuery(this).remove();
			}
			wpv_allowed_values--;
		});
	});
}

jQuery(document).on('change', '.js-filter-list .js-wpv-custom-field-compare-select', function() {
	wpv_custom_field_initialize_compare();
});

// Inputs based on filter compare mode

function wpv_custom_field_initialize_compare_mode() {
	jQuery('.js-filter-list .js-wpv-custom-field-compare-mode').each(function() {
		wpv_custom_field_show_hide_text_date_controls(this);
	});
	jQuery(document).on('change', '.js-filter-list .js-wpv-custom-field-compare-mode', function() {
		wpv_custom_field_show_hide_text_date_controls(this)
	});
}

function wpv_custom_field_show_hide_text_date_controls(item) {
	// Show the text control depending on the compare function.
	var mode = jQuery(item).val();
	jQuery(item).parents('.js-wpv-custom-field-value-div').find('.js-wpv-custom-field-value-text').removeClass('js-wpv-filter-validate');
	jQuery(item).parents('.js-wpv-custom-field-value-div').find('.js-wpv-custom-field-value-text').data('type', 'none');
	switch(mode) {
		case 'constant':
			//	case 'url':
			//	case 'attribute':
		case 'future_day':
		case 'past_day':
		case 'future_month':
		case 'past_month':
		case 'future_year':
		case 'past_year':
		case 'seconds_from_now':
		case 'months_from_now':
		case 'years_from_now':
			jQuery(item).parents('.js-wpv-custom-field-value-div').find('.js-wpv-custom-field-value-text').removeClass('hidden');
			jQuery(item).parents('.js-wpv-custom-field-value-div').find('.js-wpv-custom-field-date').addClass('hidden');
			break;
		case 'url':
			jQuery(item).parents('.js-wpv-custom-field-value-div').find('.js-wpv-custom-field-value-text').addClass('js-wpv-filter-validate');
			jQuery(item).parents('.js-wpv-custom-field-value-div').find('.js-wpv-custom-field-value-text').data('type', 'url');
			jQuery(item).parents('.js-wpv-custom-field-value-div').find('.js-wpv-custom-field-value-text').removeClass('hidden');
			jQuery(item).parents('.js-wpv-custom-field-value-div').find('.js-wpv-custom-field-date').addClass('hidden');
			break;
		case 'attribute':
			jQuery(item).parents('.js-wpv-custom-field-value-div').find('.js-wpv-custom-field-value-text').addClass('js-wpv-filter-validate');
			jQuery(item).parents('.js-wpv-custom-field-value-div').find('.js-wpv-custom-field-value-text').data('type', 'shortcode');
			jQuery(item).parents('.js-wpv-custom-field-value-div').find('.js-wpv-custom-field-value-text').removeClass('hidden');
			jQuery(item).parents('.js-wpv-custom-field-value-div').find('.js-wpv-custom-field-date').addClass('hidden');
			break;
		case 'date':
			jQuery(item).parents('.js-wpv-custom-field-value-div').find('.js-wpv-custom-field-value-text').addClass('hidden');
			jQuery(item).parents('.js-wpv-custom-field-value-div').find('.js-wpv-custom-field-date').removeClass('hidden');
			break;
		default:
			jQuery(item).parents('.js-wpv-custom-field-value-div').find('.js-wpv-custom-field-value-text').addClass('hidden');
			jQuery(item).parents('.js-wpv-custom-field-value-div').find('.js-wpv-custom-field-date').addClass('hidden');
			break;

	}
}

function wpv_custom_field_initialize_relationship() {
	if (jQuery('.js-filter-list .js-wpv-custom-field-compare-select').length > 1) {
		jQuery('.js-filter-list .js-wpv-filter-custom-field-relationship-container').removeClass('hidden');
		jQuery('.js-filter-list .js-filter-custom-field-row-remove').addClass('js-multiple-items');
	} else if (jQuery('.js-filter-list .js-wpv-custom-field-compare-select').length == 0) {
		jQuery('.js-filter-list .js-filter-custom-field').remove();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	} else {
		jQuery('.js-filter-list .js-wpv-filter-custom-field-relationship-container').addClass('hidden');
		jQuery('.js-filter-list .js-filter-custom-field-row-remove').removeClass('js-multiple-items');
	}
}

// Add another value

jQuery(document).on('click', '.js-filter-list .js-wpv-custom-field-add-value', function() {
	var clone = jQuery(this).parents('.js-filter-row-multiple-element').find('.js-wpv-custom-field-value-div:last').clone();
	
	clone.find('.js-wpv-custom-field-value-text').val('');
	clone.find('.js-wpv-custom-field-remove-value').removeClass('hidden');

	clone.insertAfter(jQuery(this).parents('.js-filter-row-multiple-element').find('.js-wpv-custom-field-value-div:last'));
	
	wpv_custom_field_initialize_compare();
	wpv_custom_field_initialize_compare_mode();
	wpv_filter_custom_field_watch_changes();
});

// Remove value

jQuery(document).on('click', '.js-filter-list .js-wpv-custom-field-remove-value', function() {
	jQuery(this).parents('.js-wpv-custom-field-value-div').remove();
	wpv_filter_custom_field_watch_changes();
});

// Get value

function _wpv_resolve_custom_field_value() {
	// Calculate the actual value to be saved using the
	// settings from the mode and text boxes.
	jQuery('.js-filter-list .js-wpv-custom-field-values').each(function(index) {
		var text_box = jQuery(this).find('.js-wpv-custom-field-values-real');
		var _wpv_resolve_custom_field_value_output = '';
		jQuery(this).find('.js-wpv-custom-field-value-div').each(function(index) {
			var text_control = jQuery(this).find('.js-wpv-custom-field-value-text')
			if (_wpv_resolve_custom_field_value_output != '') {
				_wpv_resolve_custom_field_value_output += ',';
			}
			var value = text_control.val();

			var mode = jQuery(this).children('.js-wpv-custom-field-compare-mode').val();
			switch(mode) {
				case 'url':
					value = 'URL_PARAM(' + value + ')';
					break;

				case 'attribute':
					value = 'VIEW_PARAM(' + value + ')';
					break;

				case 'now':
					value = 'NOW()';
					break;

				case 'today':
					value = 'TODAY()';
					break;

				case 'future_day':
					value = 'FUTURE_DAY(' + value + ')';
					break;

				case 'past_day':
					value = 'PAST_DAY(' + value + ')';
					break;

				case 'this_month':
					value = 'THIS_MONTH()';
					break;

				case 'future_month':
					value = 'FUTURE_MONTH(' + value + ')';
					break;

				case 'past_month':
					value = 'PAST_MONTH(' + value + ')';
					break;

				case 'this_year':
					value = 'THIS_YEAR()';
					break;

				case 'future_year':
					value = 'FUTURE_YEAR(' + value + ')';
					break;

				case 'past_year':
					value = 'PAST_YEAR(' + value + ')';
					break;

				case 'seconds_from_now':
					value = 'SECONDS_FROM_NOW(' + value + ')';
					break;

				case 'months_from_now':
					value = 'MONTHS_FROM_NOW(' + value + ')';
					break;

				case 'years_from_now':
					value = 'YEARS_FROM_NOW(' + value + ')';
					break;

				case 'date':
					var date_div = jQuery(this).find('.js-wpv-custom-field-date');
					var month = jQuery(date_div).find('select');

					var mm = month.val();
					var jj = month.next().val();
					var aa = month.next().next().val();

					value = 'DATE(' + jj + ',' + mm + ',' + aa + ')';
					break;

			}

			_wpv_resolve_custom_field_value_output += value;
		})


		text_box.val(_wpv_resolve_custom_field_value_output);
	});
}

var wpv_filter_custom_field_selected = jQuery('.js-filter-list .js-filter-custom-field input, .js-filter-list .js-filter-custom-field select').serialize();

// Watch changes

jQuery(document).on('change keyup input cut paste', '.js-filter-list .js-filter-custom-field input, .js-filter-list .js-filter-custom-field select', function() {
	jQuery(this).parents('.js-filter-custom-field').find('.filter-input-error').removeClass('filter-input-error');
	wpv_filter_custom_field_watch_changes();
});

function wpv_filter_custom_field_watch_changes() {
	jQuery('.js-filter-list .js-wpv-filter-custom-field-edit-ok').prop('disabled', false);
	wpv_clear_validate_messages('.js-filter-list .js-filter-custom-field');
	if ( wpv_filter_custom_field_selected != jQuery('.js-filter-list .js-filter-custom-field input, .js-filter-list .js-filter-custom-field select').serialize() ) {
		jQuery('.js-filter-list .js-wpv-filter-custom-field-edit-ok').removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved').val(jQuery('.js-filter-list .js-wpv-filter-custom-field-edit-ok').data('save'));
		setConfirmUnload(true);
	} else {
		jQuery('.js-filter-list .js-wpv-filter-custom-field-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-filter-list .js-wpv-filter-custom-field-edit-ok').data('close'));
		jQuery('.js-filter-list .js-wpv-filter-custom-field-edit-ok').parent().find('.unsaved').remove();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	}
}

// Close and save

jQuery(document).on('click', '.js-filter-list .js-wpv-filter-custom-field-edit-ok', function() {
	jQuery(this).parent().find('.unsaved').remove();
	if (wpv_filter_custom_field_selected == jQuery('.js-filter-list .js-filter-custom-field input, .js-filter-list .js-filter-custom-field select').serialize() ) {
		wpv_close_filter_row('.js-filter-list .js-filter-custom-field');
	} else {
		var valid = true;
		var tax_row = jQuery('.js-filter-list .js-wpv-custom-field-compare-mode').parents('.js-filter-row-multiple-element').data('field');
		valid = wpv_validate_filter_inputs('.js-filter-list .js-filter-row-custom-field-' + tax_row);
		if (valid) {
			_wpv_resolve_custom_field_value();
			var update_message = jQuery(this).data('success');
			var unsaved_message = jQuery(this).data('unsaved');
			var nonce = jQuery(this).data('nonce');
			wpv_filter_custom_field_selected = jQuery('.js-filter-list .js-filter-custom-field input, .js-filter-list .js-filter-custom-field select').serialize();
			var wpv_custom_fields_values = {};
			jQuery('.js-filter-list .js-filter-custom-field .js-filter-row-multiple-element').each(function() {
				var wpv_custom_field = "custom-field-" + jQuery(this).data('field');
				wpv_custom_fields_values[wpv_custom_field + "_compare"] = jQuery(this).find('.js-wpv-custom-field-compare-select').val();
				wpv_custom_fields_values[wpv_custom_field + "_type"] = jQuery(this).find('.js-wpv-custom-field-type-select').val();
				wpv_custom_fields_values[wpv_custom_field + "_value"] = jQuery(this).find('.js-wpv-custom-field-values-real').val();
			});
			var filter_custom_fields = JSON.stringify(wpv_custom_fields_values);
			var spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(jQuery(this)).removeClass('hidden');
			var data_view_id = jQuery('.js-post_ID').val();
			var data = {
				action: 'wpv_filter_custom_field_update',
				id: data_view_id,
				filter_custom_fields: filter_custom_fields,
				filter_custom_fields_relationship: jQuery('.js-filter-list .js-wpv-filter-custom-fields-relationship').val(),
				wpnonce: nonce
			};
			jQuery.post(ajaxurl, data, function(response) {
				if ( (typeof(response) !== 'undefined')) {
					if (response != 0) {
						jQuery('.js-filter-list .js-wpv-filter-custom-field-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-filter-list .js-wpv-filter-custom-field-edit-ok').data('close'));
						if (jQuery('.js-wpv-section-unsaved').length < 1) {
							setConfirmUnload(false);
						}
						jQuery('.js-filter-list .js-wpv-filter-custom-field-summary').html(response);
						jQuery('.js-filter-list .js-wpv-filter-custom-field-summary').append('<span class="updated toolset-alert toolset-alert-success"><i class="icon-check"></i> ' + update_message + '</span>');
						setTimeout(function(){
							jQuery('.js-filter-list .js-wpv-filter-custom-field-summary .updated').fadeOut('fast');
						}, 2000);
						wpv_close_filter_row('.js-filter-list .js-filter-custom-field');
						wpv_update_parametric_search_section();
					} else {
						console.log( "Error: WordPress AJAX returned " + response );
					}
				} else {
					console.log( "Error: AJAX returned " + response );
				}

			})
			.fail(function(jqXHR, textStatus, errorThrown) {
				console.log( "Error: %s %s", textStatus, errorThrown );
			})
			.always(function() {
				spinnerContainer.remove();
			});
		}
	}
});