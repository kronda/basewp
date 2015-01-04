// Relationship Close and save

var wpv_filter_post_relationship_mode_selected = jQuery('.js-post-relationship-mode:checked').val();
var wpv_filter_post_relationship_post_type_selected = jQuery('.js-post-relationship-post-type').val();
var wpv_filter_post_relationship_shortcode_attribute_selected = jQuery('.js-post-relationship-shortcode-attribute').val();
var wpv_filter_post_relationship_url_parameter_selected = jQuery('.js-post-relationship-url-parameter').val();
var wpv_filter_post_relationship_id_selected = jQuery('select[name=post_relationship_id]').val();
var wpv_filter_post_relationship_changed = false;

jQuery(document).on('click', '.js-wpv-filter-parent-edit-open', function(){ // rebuild the list of the current checked values
wpv_filter_post_relationship_mode_selected = jQuery('.js-post-relationship-mode:checked').val();
	wpv_filter_post_relationship_post_type_selected = jQuery('.js-post-relationship-post-type').val();
	wpv_filter_post_relationship_shortcode_attribute_selected = jQuery('.js-post-relationship-shortcode-attribute').val();
	wpv_filter_post_relationship_url_parameter_selected = jQuery('.js-post-relationship-url-parameter').val();
	wpv_filter_post_relationship_id_selected = jQuery('select[name=post_relationship_id]').val();
	wpv_filter_post_relationship_changed = false;
});

jQuery(document).on('change keyup input cut paste', '.js-post-relationship-mode, .js-post-relationship-post-type, select[name=post_relationship_id], .js-post-relationship-shortcode-attribute, .js-post-relationship-url-parameter', function() { // watch on input change
	wpv_filter_post_relationship_changed = false;
	jQuery(this).removeClass('filter-input-error');
	jQuery('.js-wpv-filter-post-relationship-edit-ok').prop('disabled', false);
	wpv_clear_validate_messages('.js-filter-post-relationship');
	if ( wpv_filter_post_relationship_mode_selected != jQuery('.js-post-relationship-mode:checked').val() ) {
		wpv_filter_post_relationship_changed = true;
	}
	if ( wpv_filter_post_relationship_post_type_selected != jQuery('.js-post-relationship-post-type').val() ) {
		wpv_filter_post_relationship_changed = true;
	}
	if ( wpv_filter_post_relationship_shortcode_attribute_selected != jQuery('.js-post-relationship-shortcode-attribute').val() ) {
		wpv_filter_post_relationship_changed = true;
	}
	if ( wpv_filter_post_relationship_url_parameter_selected != jQuery('.js-post-relationship-url-parameter').val() ) {
		wpv_filter_post_relationship_changed = true;
	}
	if ( wpv_filter_post_relationship_id_selected != jQuery('select[name=post_relationship_id]').val()
		&& typeof(wpv_filter_post_relationship_id_selected) !== 'undefined'
		&& typeof(jQuery('select[name=post_relationship_id]').val()) !== 'undefined'
	) {
		wpv_filter_post_relationship_changed = true;
	}
	wpv_filter_post_relationship_changed_helper();
});

function wpv_filter_post_relationship_changed_helper() {
	if (wpv_filter_post_relationship_changed) {
		jQuery('.js-wpv-filter-post-relationship-edit-ok').removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-post-relationship-edit-ok').data('save'));
		setConfirmUnload(true);
	} else {
		jQuery('.js-wpv-filter-post-relationship-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-post-relationship-edit-ok').data('close'));
		jQuery('.js-wpv-filter-post-relationship-edit-ok').parent().find('.unsaved').hide();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	}
}

jQuery(document).on('click', '.js-wpv-filter-post-relationship-edit-ok', function() {
	jQuery(this).parent().find('.unsaved').remove();
	if ( wpv_filter_post_relationship_mode_selected == jQuery('.js-post-relationship-mode:checked').val()
		&& wpv_filter_post_relationship_post_type_selected == jQuery('.js-post-relationship-post-type').val()
		&& wpv_filter_post_relationship_id_selected == jQuery('select[name=post_relationship_id]').val()
		&& wpv_filter_post_relationship_shortcode_attribute_selected == jQuery('.js-post-relationship-shortcode-attribute').val()
		&& wpv_filter_post_relationship_url_parameter_selected == jQuery('.js-post-relationship-url-parameter').val()
	) {
		wpv_close_filter_row('.js-filter-post-relationship');
	} else {
		var valid = wpv_validate_filter_inputs('.js-filter-post-relationship');
		if (valid) {
			var update_message = jQuery(this).data('success');
			var unsaved_message = jQuery(this).data('unsaved');
			var nonce = jQuery(this).data('nonce');
			wpv_filter_post_relationship_mode_selected = jQuery('.js-post-relationship-mode:checked').val();
			wpv_filter_post_relationship_post_type_selected = jQuery('.js-post-relationship-post-type').val();
			wpv_filter_post_relationship_shortcode_attribute_selected = jQuery('.js-post-relationship-shortcode-attribute').val();
			wpv_filter_post_relationship_url_parameter_selected = jQuery('.js-post-relationship-url-parameter').val();
			wpv_filter_post_relationship_id_selected = jQuery('select[name=post_relationship_id]').val();
			var spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(jQuery(this)).show();
			var data_view_id = jQuery('.js-post_ID').val();
			var data = {
				action: 'wpv_filter_post_relationship_update',
				id: data_view_id,
				post_relationship_mode: wpv_filter_post_relationship_mode_selected,
				post_relationship_shortcode_attribute: wpv_filter_post_relationship_shortcode_attribute_selected,
				post_relationship_url_parameter: wpv_filter_post_relationship_url_parameter_selected,
				post_relationship_id: wpv_filter_post_relationship_id_selected,
				wpnonce: nonce
			}
			jQuery.post(ajaxurl, data, function(response) {
				if ( (typeof(response) !== 'undefined') ) {
					if (response != 0) {
						jQuery('.js-wpv-filter-post-relationship-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-post-relationship-edit-ok').data('close'));
						if (jQuery('.js-wpv-section-unsaved').length < 1) {
							setConfirmUnload(false);
						}
						jQuery('.js-wpv-filter-post-relationship-summary').html(response);
						jQuery('.js-wpv-filter-post-relationship-summary').append('<span class="updated toolset-alert toolset-alert-success"><i class="icon-check"></i> ' + update_message + '</span>');
						setTimeout(function(){
							jQuery('.js-wpv-filter-post-relationship-summary .updated').fadeOut('fast');
						}, 2000);
						wpv_close_filter_row('.js-filter-post-relationship');
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
	}
});

// Relationship update posts selector

jQuery(document).on('change', '.js-post-relationship-post-type', function(){
	// Update the parents for the selected type.
	var data = {
		action : 'wpv_get_post_relationship_post_select',
		post_type : jQuery('.js-post-relationship-post-type').val(),
		wpnonce : jQuery('.js-post-relationship-post-type').data('nonce')
	};
	jQuery('select[name=post_relationship_id]').remove();
	var spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(jQuery(this)).show();
	jQuery.post(ajaxurl, data, function(response) {
		if ( typeof(response) !== 'undefined') {
			if (response != 0) {
				jQuery('.js-post-relationship-post-type').after(response);
				if ( wpv_filter_post_relationship_id_selected != jQuery('select[name=post_relationship_id]').val() ) {
					wpv_filter_post_relationship_changed = true;
				}
				wpv_filter_post_relationship_changed_helper();
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
		spinnerContainer.hide();
	});
});

// Validation for Content Selection

jQuery(document).on('click', '.js-wpv-query-type-update', function(){
	wpv_validate_post_relationship_post_types();
});

jQuery(document).ready(function(){
	wpv_validate_post_relationship_post_types();
});

function wpv_validate_post_relationship_post_types() {
	if (jQuery('.js-wpv-filter-post-relationship-edit-ok').length) {
		var wpv_query_post_items = [];
		jQuery('.js-wpv-query-post-type:checked').each(function(){
			wpv_query_post_items.push(jQuery(this).val());
		});
		var nonce = jQuery('.js-wpv-filter-post-relationship-edit-ok').data('nonce');
		var data = {
			action: 'wpv_update_post_relationship_test',
			post_types: wpv_query_post_items,
			wpnonce: nonce
		}
		jQuery.post(ajaxurl, data, function(response) {
			if ( (typeof(response) !== 'undefined')) {
				if (response != 0) {
					jQuery('.filter-info-post-relationship').remove();
					jQuery(response).insertAfter('.js-wpv-filter-post-relationship-summary');
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

jQuery(document).on('click', '.js-filter-post-relationship .js-filter-remove', function(){
	wpv_filter_post_relationship_mode_selected = '';
	wpv_filter_post_relationship_post_type_selected = '';
	wpv_filter_post_relationship_shortcode_attribute_selected = '';
	wpv_filter_post_relationship_url_parameter_selected = '';
	wpv_filter_post_relationship_id_selected = '';
	wpv_filter_post_relationship_changed = false;
});