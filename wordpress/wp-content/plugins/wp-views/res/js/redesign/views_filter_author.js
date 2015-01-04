// Author Suggest

jQuery(document).ready(function(){
	wpv_author_suggest();
});

jQuery(document).on('focus', '.js-author-suggest', function(){
	wpv_author_suggest();
});

function wpv_author_suggest() {
	jQuery('.js-author-suggest').suggest(ajaxurl + '?action=wpv_suggest_author', {
		onSelect: function() {
			thevalue = this.value;
			thevalue = thevalue.split(' #');
			jQuery('.js-author-suggest').val(thevalue[0]);
			jQuery('.js-author-suggest-id').val(thevalue[1].substring(8).trim());
		}
	});
}

// Author Close and save

var wpv_filter_author_selected = jQuery('.js-filter-author-list input, .js-filter-author-list select').serialize();

jQuery(document).on('click', '.js-wpv-filter-author-edit-open', function(){ // rebuild the list of the current checked values
	wpv_filter_author_selected = jQuery('.js-filter-author-list input, .js-filter-author-list select').serialize();
});

jQuery(document).on('change keyup input cut paste', '.js-filter-author-list input, .js-filter-author-list select', function() { // watch on inputs change
	jQuery(this).removeClass('filter-input-error');
	jQuery('.js-wpv-filter-author-edit-ok').prop('disabled', false);
	wpv_clear_validate_messages('.js-filter-author');
	if ( wpv_filter_author_selected != jQuery('.js-filter-author-list input, .js-filter-author-list select').serialize() ) {
		jQuery('.js-wpv-filter-author-edit-ok').removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-author-edit-ok').data('save'));
		setConfirmUnload(true);
	} else {
		jQuery('.js-wpv-filter-author-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-author-edit-ok').data('close'));
		jQuery('.js-wpv-filter-author-edit-ok').parent().find('.unsaved').remove();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	}
});

jQuery(document).on('click', '.js-wpv-filter-author-edit-ok', function() {
	jQuery(this).parent().find('.unsaved').remove();
	if (wpv_filter_author_selected == jQuery('.js-filter-author-list input, .js-filter-author-list select').serialize() ) {
		wpv_close_filter_row('.js-filter-author');
	} else {
		var valid = wpv_validate_filter_inputs('.js-filter-author');
		if (valid) {
			var update_message = jQuery(this).data('success');
			var unsaved_message = jQuery(this).data('unsaved');
			var nonce = jQuery(this).data('nonce');
			wpv_filter_author_selected = jQuery('.js-filter-author-list input, .js-filter-author-list select').serialize();
			var spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(jQuery(this)).show();
			var data_view_id = jQuery('.js-post_ID').val();
			var data = {
				action: 'wpv_filter_author_update',
				id: data_view_id,
				filter_author: wpv_filter_author_selected,
				wpnonce: nonce
			};
			jQuery.post(ajaxurl, data, function(response) {
				if ( (typeof(response) !== 'undefined') ) {
					if (response != 0) {
						jQuery('.js-wpv-filter-author-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-author-edit-ok').data('close'));
						if (jQuery('.js-wpv-section-unsaved').length < 1) {
							setConfirmUnload(false);
						}
						jQuery('.js-wpv-filter-author-summary').html(response);
						jQuery('.js-wpv-filter-author-summary').append('<span class="updated toolset-alert toolset-alert-success"><i class="icon-check"></i> ' + update_message + '</span>');
						setTimeout(function(){
							jQuery('.js-wpv-filter-author-summary .updated').fadeOut('fast');
						}, 2000);
						wpv_close_filter_row('.js-filter-author');
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

jQuery(document).on('click', '.js-filter-author .js-filter-remove', function(){
	wpv_filter_author_selected = '';
});