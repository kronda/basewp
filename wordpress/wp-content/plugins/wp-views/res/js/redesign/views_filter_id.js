// ID Close and save

var wpv_filter_id_selected = jQuery('.js-filter-id-list input, .js-filter-id-list select').serialize();

jQuery(document).on('click', '.js-wpv-filter-id-edit-open', function(){ // rebuild the list of the current checked values
	wpv_filter_id_selected = jQuery('.js-filter-id-list input, .js-filter-id-list select').serialize();
});

jQuery(document).on('change keyup input cut paste', '.js-filter-id-list input, .js-filter-id-list select', function() { // watch on inputs change
	jQuery(this).removeClass('filter-input-error');
	jQuery('.js-wpv-filter-id-edit-ok').prop('disabled', false);
	wpv_clear_validate_messages('.js-filter-id');
	if ( wpv_filter_id_selected != jQuery('.js-filter-id-list input, .js-filter-id-list select').serialize() ) {
		jQuery('.js-wpv-filter-id-edit-ok').removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-id-edit-ok').data('save'));
		setConfirmUnload(true);
	} else {
		jQuery('.js-wpv-filter-id-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-id-edit-ok').data('close'));
		jQuery('.js-wpv-filter-id-edit-ok').parent().find('.unsaved').remove();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	}
});

jQuery(document).on('click', '.js-wpv-filter-id-edit-ok', function() {
	jQuery(this).parent().find('.unsaved').remove();
	if (wpv_filter_id_selected == jQuery('.js-filter-id-list input, .js-filter-id-list select').serialize() ) {
		wpv_close_filter_row('.js-filter-id');
	} else {
		var valid = wpv_validate_filter_inputs('.js-filter-id');
		if (valid) {
			var update_message = jQuery(this).data('success'),
				unsaved_message = jQuery(this).data('unsaved'),
				nonce = jQuery(this).data('nonce'),
				spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(jQuery(this)).show(),
				data_view_id = jQuery('.js-post_ID').val();
			wpv_filter_id_selected = jQuery('.js-filter-id-list input, .js-filter-id-list select').serialize();
			var data = {
				action: 'wpv_filter_id_update',
				id: data_view_id,
				filter_id: wpv_filter_id_selected,
				wpnonce: nonce
			};
			jQuery.post(ajaxurl, data, function(response) {
				if ( (typeof(response) !== 'undefined') ) {
					if (response != 0) {
						jQuery('.js-wpv-filter-id-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-id-edit-ok').data('close'));
						if (jQuery('.js-wpv-section-unsaved').length < 1) {
							setConfirmUnload(false);
						}
						jQuery('.js-wpv-filter-id-summary').html(response);
						jQuery('.js-wpv-filter-id-summary').append('<span class="updated toolset-alert toolset-alert-success"><i class="icon-check"></i> ' + update_message + '</span>');
						setTimeout(function(){
							jQuery('.js-wpv-filter-id-summary .updated').fadeOut('fast');
						}, 2000);
						wpv_close_filter_row('.js-filter-id');
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
	}
});

jQuery(document).on('click', '.js-filter-id .js-filter-remove', function(){
	wpv_filter_id_selected = '';
});