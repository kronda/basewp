var wpv_filter_status_selected = jQuery('.js-filter-status-list input').serialize();

jQuery(document).on('click', '.js-wpv-filter-status-edit-open', function(){ // rebuild the list of the current checked values
	wpv_filter_status_selected = jQuery('.js-filter-status-list input').serialize();
});

jQuery(document).on('change', '.js-filter-status-list input', function() { // watch on inputs change
	if ( wpv_filter_status_selected != jQuery('.js-filter-status-list input').serialize() ) {
		jQuery('.js-wpv-filter-status-edit-ok').removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-status-edit-ok').data('save'));
		setConfirmUnload(true);
	} else {
		jQuery('.js-wpv-filter-status-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-status-edit-ok').data('close'));
		jQuery('.js-wpv-filter-status-edit-ok').parent().find('.unsaved').hide();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	}
});

jQuery(document).on('click', '.js-wpv-filter-status-edit-ok', function() { // save new settings if needed and close edit box
	jQuery(this).parent().find('.unsaved').remove();
	if (wpv_filter_status_selected == jQuery('.js-filter-status-list input').serialize() ) {
		wpv_close_filter_row('.js-filter-status');
		jQuery('.js-wpv-filter-status-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-status-edit-ok').data('close'));
	} else {
		var update_message = jQuery(this).data('success');
		var unsaved_message = jQuery(this).data('unsaved');
		var nonce = jQuery(this).data('nonce');
		wpv_filter_status_selected = jQuery('.js-filter-status-list input').serialize();
		var spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(jQuery(this)).show();
		var data_view_id = jQuery('.js-post_ID').val();
		var data = {
			action: 'wpv_filter_status_update',
			id: data_view_id,
			filter_status: wpv_filter_status_selected,
			wpnonce: nonce
		};
		jQuery.post(ajaxurl, data, function(response) {
			if ( (typeof(response) !== 'undefined') ) {
				if (response != 0) {
					jQuery('.js-wpv-filter-status-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-status-edit-ok').data('close'));
					if (jQuery('.js-wpv-section-unsaved').length < 1) {
						setConfirmUnload(false);
					}
					jQuery('.js-wpv-filter-status-summary').html(response);
					jQuery('.js-wpv-filter-status-summary').append('<span class="updated toolset-alert toolset-alert-success"><i class="icon-check"></i> ' + update_message + '</span>');
					setTimeout(function(){
						jQuery('.js-wpv-filter-status-summary .updated').fadeOut('fast');
					}, 2000);
					wpv_close_filter_row('.js-filter-status');
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
});

jQuery(document).on('click', '.js-filter-status .js-filter-remove', function(){
	wpv_filter_status_selected = '';
});