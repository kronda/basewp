jQuery(document).ready(function(){
	// filter compare: init and change
	wpv_usermeta_field_initialize_compare();

	// filter compare mode: init and change
	wpv_usermeta_field_initialize_compare_mode();

	// filter relationship
	wpv_usermeta_field_initialize_relationship();
})

function wpv_usermeta_field_initialize_compare() {
	var wpv_allowed_values = 0;
	jQuery('.js-filter-list .js-wpv-usermeta-field-compare-select').each(function(){
		var wpv_single_row = jQuery(this).parents('.js-filter-row-multiple-element');
		/*
		 / /T*his section commented before we found better way filter by usermeta and wp_users fields.
		 if ( jQuery(wpv_single_row).data('field') == 'user_email' ||
			 jQuery(wpv_single_row).data('field') == 'user_url' ||
			 jQuery(wpv_single_row).data('field') == 'user_login' || jQuery(wpv_single_row).data('field') == 'display_name' ){
		jQuery(wpv_single_row).find('.js-wpv-usermeta-comare-box').hide();
		jQuery(wpv_single_row).find('.js-wpv-usermeta-field-compare-mode option').each(function(){
			if ( jQuery(this).val() != 'constant' && jQuery(this).val() != 'url' && jQuery(this).val() != 'attribute' ){
				jQuery(this).remove();
			 }
			 
			 });
			 }*/
		
		wpv_clear_validate_messages('.js-filter-list .js-filter-usermeta-field');
		jQuery(this).parents('.js-filter-usermeta-field').find('.filter-input-error').removeClass('filter-input-error');
		if (jQuery(this).val() == 'BETWEEN' || jQuery(this).val() == 'NOT BETWEEN') {
			wpv_allowed_values = 2;
			jQuery(this).parents('.js-filter-row-multiple-element').find('.js-wpv-usermeta-field-add-value').addClass('hidden');
			jQuery(this).parents('.js-filter-row-multiple-element').find('.js-wpv-usermeta-field-remove-value').addClass('hidden');
			divs = jQuery(this).parents('.js-filter-row-multiple-element').find('.js-wpv-usermeta-field-value-div');
			if (divs.length < 2) {
				// add another one.
				var clone = jQuery(divs[0]).clone();
				clone.find('.js-wpv-usermeta-field-value-text').val('');
				clone.insertAfter(divs[0]);
				wpv_usermeta_field_initialize_compare_mode();
			}
		} else if (jQuery(this).val() == 'IN' || jQuery(this).val() == 'NOT IN') {
			wpv_allowed_values = 100000;
			jQuery(this).parents('.js-filter-row-multiple-element').find('.js-wpv-usermeta-field-add-value').removeClass('hidden');
			jQuery(this).parents('.js-filter-row-multiple-element').find('.js-wpv-usermeta-field-value-div').each(function(index) {
				if (index > 0) {
					jQuery(this).find('.js-wpv-usermeta-field-remove-value').removeClass('hidden');
				} else {
					jQuery(this).find('.js-wpv-usermeta-field-remove-value').addClass('hidden');
				}
			});
		} else {
			wpv_allowed_values = 1;
			jQuery(this).parents('.js-filter-row-multiple-element').find('.js-wpv-usermeta-field-add-value').addClass('hidden');
			jQuery(this).parents('.js-filter-row-multiple-element').find('.js-wpv-usermeta-field-remove-value').addClass('hidden');
		}
		jQuery(this).parents('.js-filter-row-multiple-element').find('.js-wpv-usermeta-field-value-div').each(function() {
			if (wpv_allowed_values > 0) {
				jQuery(this).removeClass('hidden');
			} else {
				jQuery(this).remove();
			}
			wpv_allowed_values--;
		});
	});
}

jQuery(document).on('change', '.js-filter-list .js-wpv-usermeta-field-compare-select', function() {
	wpv_usermeta_field_initialize_compare();
});

// Inputs based on filter compare mode

function wpv_usermeta_field_initialize_compare_mode() {
	jQuery('.js-filter-list .js-wpv-usermeta-field-compare-mode').each(function() {
		wpv_usermeta_field_show_hide_text_date_controls(this);
	});
	jQuery(document).on('change', '.js-filter-list .js-wpv-usermeta-field-compare-mode', function() {
		wpv_usermeta_field_show_hide_text_date_controls(this)
	});
}

function wpv_usermeta_field_show_hide_text_date_controls(item) {
	// Show the text control depending on the compare function.
	var mode = jQuery(item).val();
	jQuery(item).parents('.js-wpv-usermeta-field-value-div').find('.js-wpv-usermeta-field-value-text').removeClass('js-wpv-filter-validate');
	jQuery(item).parents('.js-wpv-usermeta-field-value-div').find('.js-wpv-usermeta-field-value-text').data('type', 'none');
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
			jQuery(item).parents('.js-wpv-usermeta-field-value-div').find('.js-wpv-usermeta-field-value-text').removeClass('hidden');
			jQuery(item).parents('.js-wpv-usermeta-field-value-div').find('.js-wpv-usermeta-field-date').addClass('hidden');
			break;
		case 'url':
			jQuery(item).parents('.js-wpv-usermeta-field-value-div').find('.js-wpv-usermeta-field-value-text').addClass('js-wpv-filter-validate');
			jQuery(item).parents('.js-wpv-usermeta-field-value-div').find('.js-wpv-usermeta-field-value-text').data('type', 'url');
			jQuery(item).parents('.js-wpv-usermeta-field-value-div').find('.js-wpv-usermeta-field-value-text').removeClass('hidden');
			jQuery(item).parents('.js-wpv-usermeta-field-value-div').find('.js-wpv-usermeta-field-date').addClass('hidden');
			break;
		case 'attribute':
			jQuery(item).parents('.js-wpv-usermeta-field-value-div').find('.js-wpv-usermeta-field-value-text').addClass('js-wpv-filter-validate');
			jQuery(item).parents('.js-wpv-usermeta-field-value-div').find('.js-wpv-usermeta-field-value-text').data('type', 'shortcode');
			jQuery(item).parents('.js-wpv-usermeta-field-value-div').find('.js-wpv-usermeta-field-value-text').removeClass('hidden');
			jQuery(item).parents('.js-wpv-usermeta-field-value-div').find('.js-wpv-usermeta-field-date').addClass('hidden');
			break;
		case 'date':
			jQuery(item).parents('.js-wpv-usermeta-field-value-div').find('.js-wpv-usermeta-field-value-text').addClass('hidden');
			jQuery(item).parents('.js-wpv-usermeta-field-value-div').find('.js-wpv-usermeta-field-date').removeClass('hidden');
			break;
		default:
			jQuery(item).parents('.js-wpv-usermeta-field-value-div').find('.js-wpv-usermeta-field-value-text').addClass('hidden');
			jQuery(item).parents('.js-wpv-usermeta-field-value-div').find('.js-wpv-usermeta-field-date').addClass('hidden');
			break;

	}
}

function wpv_usermeta_field_initialize_relationship() {
	if (jQuery('.js-filter-list .js-wpv-usermeta-field-compare-select').length > 1) {
		jQuery('.js-filter-list .js-wpv-filter-usermeta-field-relationship-container').removeClass('hidden');
		jQuery('.js-filter-list .js-filter-usermeta-field-row-remove').addClass('js-multiple-items');
	} else if (jQuery('.js-filter-list .js-wpv-usermeta-field-compare-select').length == 0) {
		jQuery('.js-filter-list .js-filter-usermeta-field').remove();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	} else {
		jQuery('.js-filter-list .js-wpv-filter-usermeta-field-relationship-container').addClass('hidden');
		jQuery('.js-filter-list .js-filter-usermeta-field-row-remove').removeClass('js-multiple-items');
	}
}

// Add another value

jQuery(document).on('click', '.js-filter-list .js-wpv-usermeta-field-add-value', function() {
	var clone = jQuery(this).parents('.js-filter-row-multiple-element').find('.js-wpv-usermeta-field-value-div:last').clone();
	
	clone.find('.js-wpv-usermeta-field-value-text').val('');
	clone.find('.js-wpv-usermeta-field-remove-value').removeClass('hidden');

	clone.insertAfter(jQuery(this).parents('.js-filter-row-multiple-element').find('.js-wpv-usermeta-field-value-div:last'));
	
	wpv_usermeta_field_initialize_compare();
	wpv_usermeta_field_initialize_compare_mode();
	wpv_filter_usermeta_field_watch_changes();
});

// Remove value

jQuery(document).on('click', '.js-filter-list .js-wpv-usermeta-field-remove-value', function() {
	jQuery(this).parents('.js-wpv-usermeta-field-value-div').remove();
	wpv_filter_usermeta_field_watch_changes();
});

// Get value

function _wpv_resolve_usermeta_field_value() {
	// Calculate the actual value to be saved using the
	// settings from the mode and text boxes.
	jQuery('.js-filter-list .js-wpv-usermeta-field-values').each(function(index) {
		var text_box = jQuery(this).find('.js-wpv-usermeta-field-values-real');
		var _wpv_resolve_usermeta_field_value_output = '';
		jQuery(this).find('.js-wpv-usermeta-field-value-div').each(function(index) {
			var text_control = jQuery(this).find('.js-wpv-usermeta-field-value-text')
			if (_wpv_resolve_usermeta_field_value_output != '') {
				_wpv_resolve_usermeta_field_value_output += ',';
			}
			var value = text_control.val();

			var mode = jQuery(this).children('.js-wpv-usermeta-field-compare-mode').val();
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
					var date_div = jQuery(this).find('.js-wpv-usermeta-field-date');
					var month = jQuery(date_div).find('select');

					var mm = month.val();
					var jj = month.next().val();
					var aa = month.next().next().val();

					value = 'DATE(' + jj + ',' + mm + ',' + aa + ')';
					break;

			}

			_wpv_resolve_usermeta_field_value_output += value;
		})


		text_box.val(_wpv_resolve_usermeta_field_value_output);
	});
}

var wpv_filter_usermeta_field_selected = jQuery('.js-filter-list .js-filter-usermeta-field input, .js-filter-list .js-filter-usermeta-field select').serialize();

// Watch changes

jQuery(document).on('change keyup input cut paste', '.js-filter-list .js-filter-usermeta-field input, .js-filter-list .js-filter-usermeta-field select', function() {
	jQuery(this).parents('.js-filter-usermeta-field').find('.filter-input-error').removeClass('filter-input-error');
	wpv_filter_usermeta_field_watch_changes();
});

function wpv_filter_usermeta_field_watch_changes() {
	jQuery('.js-filter-list .js-wpv-filter-usermeta-field-edit-ok').prop('disabled', false);
	wpv_clear_validate_messages('.js-filter-list .js-filter-usermeta-field');
	if ( wpv_filter_usermeta_field_selected != jQuery('.js-filter-list .js-filter-usermeta-field input, .js-filter-list .js-filter-usermeta-field select').serialize() ) {
		jQuery('.js-filter-list .js-wpv-filter-usermeta-field-edit-ok').removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved').val(jQuery('.js-filter-list .js-wpv-filter-usermeta-field-edit-ok').data('save'));
		setConfirmUnload(true);
	} else {
		jQuery('.js-filter-list .js-wpv-filter-usermeta-field-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-filter-list .js-wpv-filter-usermeta-field-edit-ok').data('close'));
		jQuery('.js-filter-list .js-wpv-filter-usermeta-field-edit-ok').parent().find('.unsaved').remove();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	}
}

// Close and save

jQuery(document).on('click', '.js-filter-list .js-wpv-filter-usermeta-field-edit-ok', function() {
	jQuery(this).parent().find('.unsaved').remove();
	if (wpv_filter_usermeta_field_selected == jQuery('.js-filter-list .js-filter-usermeta-field input, .js-filter-list .js-filter-usermeta-field select').serialize() ) {
		wpv_close_filter_row('.js-filter-list .js-filter-usermeta-field');
	} else {
		var valid = true;
		var tax_row = jQuery('.js-filter-list .js-wpv-usermeta-field-compare-mode').parents('.js-filter-row-multiple-element').data('field');
		valid = wpv_validate_filter_inputs('.js-filter-list .js-filter-row-usermeta-field-' + tax_row);
		if (valid) {
			_wpv_resolve_usermeta_field_value();
			var update_message = jQuery(this).data('success');
			var unsaved_message = jQuery(this).data('unsaved');
			var nonce = jQuery(this).data('nonce');
			wpv_filter_usermeta_field_selected = jQuery('.js-filter-list .js-filter-usermeta-field input, .js-filter-list .js-filter-usermeta-field select').serialize();
			var wpv_usermeta_fields_values = {};
			jQuery('.js-filter-list .js-filter-usermeta-field .js-filter-row-multiple-element').each(function() {
				var wpv_usermeta_field = "usermeta-field-" + jQuery(this).data('field');
				wpv_usermeta_fields_values[wpv_usermeta_field + "_compare"] = jQuery(this).find('.js-wpv-usermeta-field-compare-select').val();
				wpv_usermeta_fields_values[wpv_usermeta_field + "_type"] = jQuery(this).find('.js-wpv-usermeta-field-type-select').val();
				wpv_usermeta_fields_values[wpv_usermeta_field + "_value"] = jQuery(this).find('.js-wpv-usermeta-field-values-real').val();
			});
			var filter_usermeta_fields = JSON.stringify(wpv_usermeta_fields_values);
			var spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(jQuery(this)).removeClass('hidden');
			var data_view_id = jQuery('.js-post_ID').val();
			var data = {
				action: 'wpv_filter_usermeta_field_update',
				id: data_view_id,
				filter_usermeta_fields: filter_usermeta_fields,
				filter_usermeta_fields_relationship: jQuery('.js-filter-list .js-wpv-filter-usermeta-fields-relationship').val(),
				wpnonce: nonce
			};
			jQuery.post(ajaxurl, data, function(response) {
				if ( (typeof(response) !== 'undefined')) {
					if (response != 0) {
						jQuery('.js-filter-list .js-wpv-filter-usermeta-field-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-filter-list .js-wpv-filter-usermeta-field-edit-ok').data('close'));
						if (jQuery('.js-wpv-section-unsaved').length < 1) {
							setConfirmUnload(false);
						}
						jQuery('.js-filter-list .js-wpv-filter-usermeta-field-summary').html(response);
						jQuery('.js-filter-list .js-wpv-filter-usermeta-field-summary').append('<span class="updated toolset-alert toolset-alert-success"><i class="icon-check"></i> ' + update_message + '</span>');
						setTimeout(function(){
							jQuery('.js-filter-list .js-wpv-filter-usermeta-field-summary .updated').fadeOut('fast');
						}, 2000);
						wpv_close_filter_row('.js-filter-list .js-filter-usermeta-field');
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