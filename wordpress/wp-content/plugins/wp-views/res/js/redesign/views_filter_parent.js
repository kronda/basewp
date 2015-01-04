// Parent Close and save

var wpv_filter_parent_mode_selected = jQuery('.js-parent-mode:checked').val();
var wpv_filter_parent_post_type_selected = jQuery('.js-parent-post-type').val();
var wpv_filter_parent_id_selected = jQuery('select[name=parent_id]').val();
var wpv_filter_parent_changed = false;

jQuery(document).on('click', '.js-wpv-filter-parent-edit-open', function(){ // rebuild the list of the current checked values
	wpv_filter_parent_mode_selected = jQuery('.js-parent-mode:checked').val();
	wpv_filter_parent_post_type_selected = jQuery('.js-parent-post-type').val();
	wpv_filter_parent_id_selected = jQuery('select[name=parent_id]').val();
	wpv_filter_parent_changed = false;
});

jQuery(document).on('change', '.js-parent-mode, .js-parent-post-type, select[name=parent_id]', function() { // watch on input change
	wpv_filter_parent_changed = false;
	if ( wpv_filter_parent_mode_selected != jQuery('.js-parent-mode:checked').val() ) {
		wpv_filter_parent_changed = true;
	}
	if ( wpv_filter_parent_post_type_selected != jQuery('.js-parent-post-type').val() ) {
		wpv_filter_parent_changed = true;
	}
	if ( wpv_filter_parent_id_selected != jQuery('select[name=parent_id]').val()
		&& typeof(wpv_filter_parent_id_selected) !== 'undefined'
		&& typeof(jQuery('select[name=parent_id]').val()) !== 'undefined'
	) {
		wpv_filter_parent_changed = true;
	}
	wpv_filter_parent_changed_helper();
});

function wpv_filter_parent_changed_helper() {
	if (wpv_filter_parent_changed) {
		jQuery('.js-wpv-filter-parent-edit-ok').removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-parent-edit-ok').data('save'));
		setConfirmUnload(true);
	} else {
		jQuery('.js-wpv-filter-parent-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-parent-edit-ok').data('close'));
		jQuery('.js-wpv-filter-parent-edit-ok').parent().find('.unsaved').hide();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	}
}

jQuery(document).on('click', '.js-wpv-filter-parent-edit-ok', function() {
	jQuery(this).parent().find('.unsaved').remove();
	if ( wpv_filter_parent_mode_selected == jQuery('.js-parent-mode:checked').val()
		&& wpv_filter_parent_post_type_selected == jQuery('.js-parent-post-type').val()
		&& wpv_filter_parent_id_selected == jQuery('select[name=parent_id]').val()
	) {
		wpv_close_filter_row('.js-filter-parent');
	} else {
		var update_message = jQuery(this).data('success');
		var unsaved_message = jQuery(this).data('unsaved');
		var nonce = jQuery(this).data('nonce');
		wpv_filter_parent_mode_selected = jQuery('.js-parent-mode:checked').val();
		wpv_filter_parent_post_type_selected = jQuery('.js-parent-post-type').val();
		wpv_filter_parent_id_selected = jQuery('select[name=parent_id]').val();
		var spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(jQuery(this)).show();
		var data_view_id = jQuery('.js-post_ID').val();
		var data = {
			action: 'wpv_filter_parent_update',
			id: data_view_id,
			parent_mode: wpv_filter_parent_mode_selected,
			parent_id: wpv_filter_parent_id_selected,
			wpnonce: nonce
		}
		jQuery.post(ajaxurl, data, function(response) {
			if ( (typeof(response) !== 'undefined') ) {
				if (response != 0) {
					jQuery('.js-wpv-filter-parent-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-parent-edit-ok').data('close'));
					if (jQuery('.js-wpv-section-unsaved').length < 1) {
						setConfirmUnload(false);
					}
					jQuery('.js-wpv-filter-parent-summary').html(response);
					jQuery('.js-wpv-filter-parent-summary').append('<span class="updated toolset-alert toolset-alert-success"><i class="icon-check"></i> ' + update_message + '</span>');
					setTimeout(function(){
						jQuery('.js-wpv-filter-parent-summary .updated').fadeOut('fast');
					}, 2000);
					wpv_close_filter_row('.js-filter-parent');
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

// Parent update posts selector

jQuery(document).on('change', '.js-parent-post-type', function(){
	var data = {
		action : 'wpv_get_parent_post_select',
		post_type : jQuery('.js-parent-post-type').val(),
		wpnonce : jQuery('.js-parent-post-type').data('nonce')
	};
	jQuery('select[name=parent_id]').remove();
	var spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(jQuery(this)).show();
	jQuery.post(ajaxurl, data, function(response) {
		if ( typeof(response) !== 'undefined') {
			if (response != 0) {
				jQuery('.js-parent-post-type').after(response);
				if ( wpv_filter_parent_id_selected != jQuery('select[name=parent_id]').val() ) {
					wpv_filter_parent_changed = true;
				}
				wpv_filter_parent_changed_helper();
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
});

// Validation for hierarchical Content Selection

jQuery(document).on('click', '.js-wpv-query-type-update, .js-wpv-filter-parent-edit-ok', function(){
	wpv_validate_hierarchical_post_types();
});

jQuery(document).ready(function(){
	wpv_validate_hierarchical_post_types();
});

function wpv_validate_hierarchical_post_types() {
	var invalid_post_types = false;
	jQuery('.js-wpv-query-post-type:checked').each(function(){
		if (jQuery.inArray(jQuery(this).val(), wpv_hierarchical_post_types) == -1) {
			invalid_post_types = true;
		}
	});
	if (invalid_post_types) {
		// TODO add a function to remove this filter from the offering popup
		wpv_filter_parent_type_not_hierarchical.insertAfter('.js-wpv-filter-parent-summary').show();
	} else {
		wpv_filter_parent_type_not_hierarchical.remove();
	}
}

jQuery(document).on('click', '.js-filter-parent .js-filter-remove', function(){
	wpv_filter_parent_mode_selected = '';
	wpv_filter_parent_post_type_selected = '';
	wpv_filter_parent_id_selected = '';
	wpv_filter_parent_changed = false;
});

// Taxonomy Parent Close and save

var wpv_filter_taxonomy_parent_mode_selected = jQuery('.js-taxonomy-parent-mode:checked').val();
var wpv_filter_taxonomy_parent_id_selected = jQuery('select[name=wpv_taxonomy_parent_id]').val();

jQuery(document).on('click', '.js-wpv-filter-parent-edit-open', function() { // rebuild the list of the current checked values
	wpv_filter_taxonomy_parent_mode_selected = jQuery('.js-taxonomy-parent-mode:checked').val();
	wpv_filter_taxonomy_parent_id_selected = jQuery('select[name=wpv_taxonomy_parent_id]').val();
});

jQuery(document).on('change', '.js-taxonomy-parent-mode, select[name=wpv_taxonomy_parent_id]', function() { // watch on input change
	var wpv_filter_taxonomy_parent_changed = false;
	if ( wpv_filter_taxonomy_parent_mode_selected != jQuery('.js-taxonomy-parent-mode:checked').val() ) {
		wpv_filter_taxonomy_parent_changed = true;
	}
	if ( wpv_filter_taxonomy_parent_id_selected != jQuery('select[name=wpv_taxonomy_parent_id]').val() ) {
		wpv_filter_taxonomy_parent_changed = true;
	}
	if (wpv_filter_taxonomy_parent_changed) {
		jQuery('.js-wpv-filter-taxonomy-parent-edit-ok').removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-taxonomy-parent-edit-ok').data('save'));
		setConfirmUnload(true);
	} else {
		jQuery('.js-wpv-filter-taxonomy-parent-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-taxonomy-parent-edit-ok').data('close'));
		jQuery('.js-wpv-filter-taxonomy-parent-edit-ok').parent().find('.unsaved').remove();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	}
});

jQuery(document).on('click', '.js-wpv-filter-taxonomy-parent-edit-ok', function() {
	jQuery(this).parent().find('.unsaved').remove();
	if ( wpv_filter_taxonomy_parent_mode_selected == jQuery('.js-taxonomy-parent-mode:checked').val()
		&& wpv_filter_taxonomy_parent_id_selected == jQuery('select[name=wpv_taxonomy_parent_id]').val()
	) {
		wpv_close_filter_row('.js-filter-taxonomy-parent');
	} else {
		var update_message = jQuery(this).data('success');
		var unsaved_message = jQuery(this).data('unsaved');
		var nonce = jQuery(this).data('nonce');
		wpv_filter_taxonomy_parent_mode_selected = jQuery('.js-taxonomy-parent-mode:checked').val();
		wpv_filter_taxonomy_parent_id_selected = jQuery('select[name=wpv_taxonomy_parent_id]').val();
		var spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(jQuery(this)).show();
		var data_view_id = jQuery('.js-post_ID').val();
		var data = {
			action: 'wpv_filter_taxonomy_parent_update',
		    id: data_view_id,
		    tax_parent_mode: wpv_filter_taxonomy_parent_mode_selected,
		    tax_parent_id: wpv_filter_taxonomy_parent_id_selected,
		    tax_type: jQuery('.js-wpv-query-taxonomy-type:checked').val(),
		    wpnonce: nonce
		}
		jQuery.post(ajaxurl, data, function(response) {
			if ( (typeof(response) !== 'undefined') ) {
				if (response != 0) {
					jQuery('.js-wpv-filter-taxonomy-parent-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-taxonomy-parent-edit-ok').data('close'));
					if (jQuery('.js-wpv-section-unsaved').length < 1) {
						setConfirmUnload(false);
					}
					jQuery('.js-wpv-filter-taxonomy-parent-summary').html(response);
					jQuery('.js-wpv-filter-taxonomy-parent-summary').append('<span class="updated toolset-alert toolset-alert-success"><i class="icon-check"></i> ' + update_message + '</span>');
					setTimeout(function(){
						jQuery('.js-wpv-filter-taxonomy-parent-summary .updated').fadeOut('fast');
					}, 2000);
					wpv_close_filter_row('.js-filter-taxonomy-parent');
					wpv_filter_taxonomy_parent_changed.remove();
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

function wpv_validate_filter_taxonomy_parent() {
	if (jQuery('.js-wpv-filter-taxonomy-parent-edit-ok').length) {
		var wpv_query_post_items = [];
		jQuery('.js-wpv-query-post-type:checked').each(function(){
			wpv_query_post_items.push(jQuery(this).val());
		});
		var nonce = jQuery('.js-wpv-filter-taxonomy-parent-edit-ok').data('nonce');
		var data = {
			action: 'wpv_filter_taxonomy_parent_test',
			tax_parent_id: wpv_filter_taxonomy_parent_id_selected,
			tax_type: jQuery('.js-wpv-query-taxonomy-type:checked').val(),
			wpnonce: nonce
		}
		jQuery.post(ajaxurl, data, function(response) {
			if ( (typeof(response) !== 'undefined')) {
				if (response != 0) {
					if (response == 'bad') {
						wpv_filter_taxonomy_parent_changed.insertAfter('.js-wpv-filter-taxonomy-parent-summary').show();
					} else {
						wpv_filter_taxonomy_parent_changed.remove();
					}
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

		});
	}
}

jQuery(document).on('click', '.js-filter-taxonomy-parent .js-filter-remove', function(){
	wpv_filter_taxonomy_parent_mode_selected = '';
	wpv_filter_taxonomy_parent_id_selected = '';
});