// Search Close and save

var wpv_filter_search_selected = jQuery('.js-filter-search-list input, .js-filter-search-list select').serialize();

jQuery(document).on('click', '.js-wpv-filter-search-edit-open', function(){ // rebuild the list of the current checked values
	wpv_filter_search_selected = jQuery('.js-filter-search-list input, .js-filter-search-list select').serialize();
});

jQuery(document).on('change keyup input cut paste', '.js-filter-search-list input, .js-filter-search-list select', function() { // watch on inputs change
	if ( wpv_filter_search_selected != jQuery('.js-filter-search-list input, .js-filter-search-list select').serialize() ) {
		jQuery('.js-wpv-filter-search-edit-ok').removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-search-edit-ok').data('save'));
		setConfirmUnload(true);
	} else {
		jQuery('.js-wpv-filter-search-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-search-edit-ok').data('close'));
		jQuery('.js-wpv-filter-search-edit-ok').parent().find('.unsaved').remove();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	}
});


jQuery(document).on('click', '.js-wpv-filter-search-edit-ok', function() {
	//needed to work along with toolbar button
	jQuery(this).trigger( 'search_box_created' );
	jQuery(this).parent().find('.unsaved').remove();
	if (wpv_filter_search_selected == jQuery('.js-filter-search-list input, .js-filter-search-list select').serialize() ) {
		wpv_close_filter_row('.js-filter-search');
	} else {
		var update_message = jQuery(this).data('success');
		var unsaved_message = jQuery(this).data('unsaved');
		var nonce = jQuery(this).data('nonce');
		wpv_filter_search_selected = jQuery('.js-filter-search-list input, .js-filter-search-list select').serialize();
		var spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(jQuery(this)).show();
		var data_view_id = jQuery('.js-post_ID').val();
		var data = {
			action: 'wpv_filter_search_update',
		    id: data_view_id,
		    filter_search: wpv_filter_search_selected,
		    wpnonce: nonce
		};
		jQuery.post(ajaxurl, data, function(response) {
			if ( (typeof(response) !== 'undefined') ) {
				if (response != 0) {
					jQuery('.js-wpv-filter-search-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-search-edit-ok').data('close'));
					if (jQuery('.js-wpv-section-unsaved').length < 1) {
						setConfirmUnload(false);
					}
					jQuery('.js-wpv-filter-search-summary').html(response);
					jQuery('.js-wpv-filter-search-summary').append('<span class="updated toolset-alert toolset-alert-success"><i class="icon-check"></i> ' + update_message + '</span>');
					setTimeout(function(){
						jQuery('.js-wpv-filter-search-summary .updated').fadeOut('fast');
					}, 2000);
					wpv_close_filter_row('.js-filter-search');
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
	//	alert(wpv_filter_search_selected);
	}
});

jQuery(document).on('click', '.js-filter-search .js-filter-remove', function(){
	//this is needed to make it work with toolbar button
	jQuery(this).trigger( 'search_box_removed', jQuery(this) );
	wpv_filter_search_selected = '';
});

// Taxonomy search

var wpv_filter_taxonomy_search_selected = jQuery('.js-filter-taxonomy-search-list input, .js-filter-taxonomy-search-list select').serialize();

jQuery(document).on('click', '.js-wpv-filter-taxonomy-search-edit-open', function(){ // rebuild the list of the current checked values
wpv_filter_taxonomy_search_selected = jQuery('.js-filter-taxonomy-search-list input, .js-filter-taxonomy-search-list select').serialize();
});

jQuery(document).on('change keyup input cut paste', '.js-filter-taxonomy-search-list input, .js-filter-taxonomy-search-list select', function() { // watch on inputs change
if ( wpv_filter_taxonomy_search_selected != jQuery('.js-filter-taxonomy-search-list input, .js-filter-taxonomy-search-list select').serialize() ) {
	jQuery('.js-wpv-filter-taxonomy-search-edit-ok').removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-taxonomy-search-edit-ok').data('save'));
	setConfirmUnload(true);
} else {
	jQuery('.js-wpv-filter-taxonomy-search-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-taxonomy-search-edit-ok').data('close'));
	jQuery('.js-wpv-filter-taxonomy-search-edit-ok').parent().find('.unsaved').remove();
	if (jQuery('.js-wpv-section-unsaved').length < 1) {
		setConfirmUnload(false);
	}
}
});

jQuery(document).on('click', '.js-wpv-filter-taxonomy-search-edit-ok', function() {
	jQuery(this).parent().find('.unsaved').remove();
	if (wpv_filter_taxonomy_search_selected == jQuery('.js-filter-taxonomy-search-list input, .js-filter-taxonomy-search-list select').serialize() ) {
		wpv_close_filter_row('.js-filter-taxonomy-search');
	} else {
		var update_message = jQuery(this).data('success');
		var unsaved_message = jQuery(this).data('unsaved');
		var nonce = jQuery(this).data('nonce');
		wpv_filter_taxonomy_search_selected = jQuery('.js-filter-taxonomy-search-list input, .js-filter-taxonomy-search-list select').serialize();
		var spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(jQuery(this)).show();
		var data_view_id = jQuery('.js-post_ID').val();
		var data = {
			action: 'wpv_filter_taxonomy_search_update',
		    id: data_view_id,
		    tax_filter_search: wpv_filter_taxonomy_search_selected,
		    wpnonce: nonce
		};
		jQuery.post(ajaxurl, data, function(response) {
			if ( (typeof(response) !== 'undefined') ) {
				if (response != 0) {
					jQuery('.js-wpv-filter-taxonomy-search-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-taxonomy-search-edit-ok').data('close'));
					if (jQuery('.js-wpv-section-unsaved').length < 1) {
						setConfirmUnload(false);
					}
					jQuery('.js-wpv-filter-taxonomy-search-summary').html(response);
					jQuery('.js-wpv-filter-taxonomy-search-summary').append('<span class="updated toolset-alert toolset-alert-success"><i class="icon-check"></i> ' + update_message + '</span>');
					setTimeout(function(){
						jQuery('.js-wpv-filter-taxonomy-search-summary .updated').fadeOut('fast');
					}, 2000);
					wpv_close_filter_row('.js-filter-taxonomy-search');
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

jQuery(document).on('click', '.js-filter-taxonomy-search .js-filter-remove', function(){
	wpv_filter_taxonomy_search_selected = '';
});