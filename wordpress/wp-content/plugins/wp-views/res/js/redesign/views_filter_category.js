// Start with taxonomies checkboxes

jQuery(document).ready(function() {
	wpv_taxonomy_mode();
	wpv_taxonomy_relationship();
});

function wpv_taxonomy_mode() {
	jQuery('.js-filter-list .js-wpv-taxonomy-relationship').each(function(){
		var wpv_single_row = jQuery(this).parents('.js-filter-row-multiple-element');
		wpv_clear_validate_messages('.js-filter-list .js-filter-taxonomy');
		jQuery('.js-filter-list .js-taxonomy-param').removeClass('filter-input-error');
		if (jQuery(this).val() == 'FROM ATTRIBUTE') {
			wpv_single_row.find('.js-taxonomy-parameter').removeClass('hidden');
			wpv_single_row.find('.js-taxonomy-checklist').addClass('hidden');
			wpv_single_row.find('.js-taxonomy-param-label').html(wpv_single_row.find('.js-taxonomy-param-label').data('attribute'));
			wpv_single_row.find('.js-taxonomy-param').data('type', 'shortcode');
		} else if (jQuery(this).val() == 'FROM URL') {
			wpv_single_row.find('.js-taxonomy-parameter').removeClass('hidden');
			wpv_single_row.find('.js-taxonomy-checklist').addClass('hidden');
			wpv_single_row.find('.js-taxonomy-param-label').html(wpv_single_row.find('.js-taxonomy-param-label').data('parameter'));
			wpv_single_row.find('.js-taxonomy-param').data('type', 'url');
		}else if (jQuery(this).val() == 'FROM PAGE'
			|| jQuery(this).val() == 'FROM PARENT VIEW') {
			wpv_single_row.find('.js-taxonomy-checklist').addClass('hidden');
			wpv_single_row.find('.js-taxonomy-parameter').addClass('hidden');
			} else {
			wpv_single_row.find('.js-taxonomy-checklist').removeClass('hidden');
			wpv_single_row.find('.js-taxonomy-parameter').addClass('hidden');
		}
	});
}

function wpv_taxonomy_relationship() {
	if (jQuery('.js-filter-list .js-wpv-taxonomy-relationship').length > 1) {
		jQuery('.js-filter-list .js-wpv-filter-taxonomy-relationship').removeClass('hidden');
		jQuery('.js-filter-list .js-filter-taxonomy-row-remove').addClass('js-multiple-items');
	} else if (jQuery('.js-filter-list .js-wpv-taxonomy-relationship').length == 0) {
		jQuery('.js-filter-list .js-filter-taxonomy').remove();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	} else {
		jQuery('.js-filter-list .js-wpv-filter-taxonomy-relationship').addClass('hidden');
		jQuery('.js-filter-list .js-filter-taxonomy-row-remove').removeClass('js-multiple-items');
	}
}

jQuery(document).on('change', '.js-filter-list .js-wpv-taxonomy-relationship', function() {
	wpv_taxonomy_mode();
});

var wpv_filter_taxonomy_selected = jQuery('.js-filter-list .js-filter-taxonomy input, .js-filter-list .js-filter-taxonomy select').serialize();

// Watch changes

jQuery(document).on('change keyup input cut paste', '.js-filter-list .js-filter-taxonomy input, .js-filter-list .js-filter-taxonomy select', function() {
	jQuery(this).removeClass('filter-input-error');
	jQuery('.js-filter-list .js-wpv-filter-taxonomy-edit-ok').prop('disabled', false);
	wpv_clear_validate_messages('.js-filter-list .js-filter-taxonomy');
	if ( wpv_filter_taxonomy_selected != jQuery('.js-filter-list .js-filter-taxonomy input, .js-filter-list .js-filter-taxonomy select').serialize() ) {
		jQuery('.js-filter-list .js-wpv-filter-taxonomy-edit-ok').removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved').val(jQuery('.js-filter-list .js-wpv-filter-taxonomy-edit-ok').data('save'));
		setConfirmUnload(true);
	} else {
		jQuery('.js-filter-list .js-wpv-filter-taxonomy-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-filter-list .js-wpv-filter-taxonomy-edit-ok').data('close'));
		jQuery('.js-filter-list .js-wpv-filter-taxonomy-edit-ok').parent().find('.unsaved').remove();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	}
});

// Close and save

jQuery(document).on('click', '.js-filter-list .js-wpv-filter-taxonomy-edit-ok', function() {
	jQuery(this).parent().find('.unsaved').remove();
	if (wpv_filter_taxonomy_selected == jQuery('.js-filter-list .js-filter-taxonomy input, .js-filter-list .js-filter-taxonomy select').serialize() ) {
		wpv_close_filter_row('.js-filter-list .js-filter-taxonomy');
	} else {
		var valid = true;
		jQuery('.js-filter-list .js-wpv-taxonomy-relationship').each(function(){
			var tax_row = jQuery(this).parents('.js-filter-row-multiple-element').data('taxonomy');
			if (jQuery(this).val() == 'FROM ATTRIBUTE' || jQuery(this).val() == 'FROM URL') {
				var this_valid = wpv_validate_filter_inputs('.js-filter-list .js-filter-row-taxonomy-' + tax_row);
			}
			if (this_valid == false ) {
				valid = false;
			}
		});
	//	alert(jQuery('.js-filter-taxonomy input, .js-filter-taxonomy select').serialize());
		if (valid) {
			var update_message = jQuery(this).data('success');
			var unsaved_message = jQuery(this).data('unsaved');
			var nonce = jQuery(this).data('nonce');
			wpv_filter_taxonomy_selected = jQuery('.js-filter-list .js-filter-taxonomy input, .js-filter-list .js-filter-taxonomy select').serialize();
			var spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(jQuery(this)).removeClass('hidden');
			var data_view_id = jQuery('.js-post_ID').val();
			var data = {
				action: 'wpv_filter_taxonomy_update',
				id: data_view_id,
				filter_taxonomy: wpv_filter_taxonomy_selected,
				wpnonce: nonce
			};
			jQuery.post(ajaxurl, data, function(response) {
				if ( (typeof(response) !== 'undefined') ) {
					if (response != 0) {
						jQuery('.js-filter-list .js-wpv-filter-taxonomy-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-filter-list .js-wpv-filter-taxonomy-edit-ok').data('close'));
						if (jQuery('.js-wpv-section-unsaved').length < 1) {
							setConfirmUnload(false);
						}
						jQuery('.js-filter-list .js-wpv-filter-taxonomy-summary').html(response);
						jQuery('.js-filter-list .js-wpv-filter-taxonomy-summary').append('<span class="updated toolset-alert toolset-alert-success"><i class="icon-check"></i> ' + update_message + '</span>');
						setTimeout(function(){
							jQuery('.js-filter-list .js-wpv-filter-taxonomy-summary .updated').fadeOut('fast');
						}, 2000);
						wpv_close_filter_row('.js-filter-list .js-filter-taxonomy');
						// Run a function to reload the DPS section
						wpv_update_parametric_search_section();
					} else {
						console.log( "Error: WordPress AJAX returned " + response );
					}
				} else {
					console.log( "Error: AJAX returned ", response );
				}

			})
			.fail(function(jqXHR, textStatus, errorThrown) {
				console.log( "Error: ", textStatus, errorThrown );
			})
			.always(function() {
				spinnerContainer.remove();
			});
		}
	//	wpv_close_filter_row('.js-filter-taxonomy');
	//	wpv_validate_filter_inputs('.js-filter-taxonomy');
	}
});

jQuery(document).on('click', '.js-filter-list .js-wpv-filter-taxonomy-controls .js-filter-remove', function(){
	wpv_filter_taxonomy_selected = '';
});