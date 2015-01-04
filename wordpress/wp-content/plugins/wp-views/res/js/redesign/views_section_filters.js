// Document ready

jQuery(document).ready(function(){
	wpv_filters_exist();
	wpv_filters_colapse();
	jQuery('.js-filters-insert-filter').removeClass('button-primary').addClass('button-secondary').prop('disabled', true);
});

function wpv_filters_colapse() { // hide edition by default
	jQuery('.js-wpv-filter-edit').hide();
}

function wpv_filters_exist() {
	var thiz_button = jQuery('.js-wpv-filter-add-filter'),
	empty = thiz_button.data('empty'),
	nonempty = thiz_button.data('nonempty'),
	thiz_list = jQuery('.js-filter-list');
	if (0 == thiz_list.find('.js-filter-row').length) {
		thiz_list.hide();
		jQuery('.js-no-filters').show();
		thiz_button.val(empty);
	} else {
		jQuery('.js-no-filters').hide();
		thiz_list.show();
		thiz_button.val(nonempty);
	}
}

// Gerenal interaction: open and close filters edition

jQuery(document).on('click', '.js-wpv-filter-edit-open', function(e){ // open filters editor - common for all filters
	e.preventDefault();
	var thiz = jQuery(this),
	thiz_parentrow = thiz.parents('.js-filter-row');
	thiz.attr('disabled', true).hide();
	thiz_parentrow.find('.js-wpv-filter-summary').hide();
	thiz_parentrow.find('.js-wpv-filter-edit').fadeIn('fast');
	if ( thiz_parentrow.hasClass('js-filter-row-multiple') ) {
		thiz_parentrow.find('.js-wpv-filter-edit-controls').hide();
	}
});

function wpv_close_filter_row(row) { // general close filters editor - just aesthetic changes & no actions
	var thiz_row = jQuery(row);
	thiz_row.find('.js-wpv-filter-edit').hide();
	thiz_row.find('.js-wpv-filter-summary').show();
	thiz_row.find('.js-wpv-filter-edit-open').attr('disabled', false).show();
	if ( thiz_row.hasClass('js-filter-row-multiple') ) {
		thiz_row.find('.js-wpv-filter-edit-controls').show();
	}
//	jQuery('html,body').animate({scrollTop:jQuery('.js-wpv-settings-content-filter').offset().top-25}, 500);
}

// General validation

function wpv_validate_filter_inputs(row) {
	var valid = true,
	thiz,
	type,
	input_valid;
	jQuery(jQuery(row).find('.js-wpv-filter-validate').get().reverse()).each(function(){
		thiz = jQuery(this);
		thiz.removeClass('filter-input-error');
		type = thiz.data('type'),
		input_valid = wpv_filter_validate_param(type, thiz);
		if (input_valid == false ) {
			thiz.addClass('filter-input-error');
			valid = false;
		}
	});
	return valid;
}

var wpv_param_missing = jQuery('.js-wpv-param-missing').hide(),
wpv_param_url_ilegal = jQuery('.js-wpv-param-url-ilegal').hide(),
wpv_param_shortcode_ilegal = jQuery('.js-wpv-param-shortcode-ilegal').hide(),
wpv_param_forbidden_wp = jQuery('.js-wpv-param-forbidden-wordpress').hide(),
wpv_param_forbidden_ts = jQuery('.js-wpv-param-forbidden-toolset').hide(),
wpv_param_forbidden_pt = jQuery('.js-wpv-param-forbidden-post-type').hide(),
wpv_param_forbidden_tax = jQuery('.js-wpv-param-forbidden-taxonomy').hide(),
wpv_filter_parent_type_not_hierarchical = jQuery('.js-wpv-filter-parent-type-not-hierarchical').hide(),
wpv_filter_taxonomy_parent_changed = jQuery('.js-wpv-filter-taxonomy-parent-changed').hide(),
wpv_filter_taxonomy_term_changed = jQuery('.js-wpv-filter-taxonomy-term-changed').hide(),
wpv_url_pattern = /^[a-z0-9\-\_]+$/,
wpv_shortcode_pattern = /^[a-z0-9]+$/;

function wpv_filter_validate_param(type, selector, value) {
	var input_valid = true,
		value = selector.val(),
		save_button = selector.parents('.js-filter-row').find('.js-wpv-filter-edit-ok');
	if (type == 'url') {
		wpv_param_missing.remove();
		wpv_param_url_ilegal.remove();
		wpv_param_forbidden_wp.remove();
		wpv_param_forbidden_ts.remove();
		wpv_param_forbidden_pt.remove();
		wpv_param_forbidden_tax.remove();
		if (selector.val() == '') {
			wpv_param_missing.clone().insertAfter(save_button).show();
			input_valid = false;
		} else if (wpv_url_pattern.test(value) == false) {
			wpv_param_url_ilegal.clone().insertAfter(save_button).show();
			input_valid = false;
		} else if (jQuery.inArray(value, wpv_forbidden_parameters.wordpress) > -1) {
			wpv_param_forbidden_wp.clone().insertAfter(save_button).show();
			input_valid = false;
		} else if (jQuery.inArray(value, wpv_forbidden_parameters.toolset) > -1) {
			wpv_param_forbidden_ts.clone().insertAfter(save_button).show();
			input_valid = false;
		} else if (jQuery.inArray(value, wpv_forbidden_parameters.post_type) > -1) {
			wpv_param_forbidden_pt.clone().insertAfter(save_button).show();
			input_valid = false;
		} else if (jQuery.inArray(value, wpv_forbidden_parameters.taxonomy) > -1) {
			wpv_param_forbidden_tax.clone().insertAfter(save_button).show();
			input_valid = false;
		}
	}
	if (type == 'shortcode') {
		wpv_param_missing.remove();
		wpv_param_shortcode_ilegal.remove();
		if (selector.val() == '') {
			wpv_param_missing.clone().insertAfter(save_button).show();
			input_valid = false;
		} else if (wpv_shortcode_pattern.test(value) == false) {
			wpv_param_shortcode_ilegal.clone().insertAfter(save_button).show();
			input_valid = false;
		}
	}
	if (selector.parents('.js-filter-row').find('.js-filter-error').length < 1) {
		save_button.prop('disabled', false);
	} else {
		save_button.prop('disabled', true);
	}
	return input_valid;
}

function wpv_clear_validate_messages(row){
	jQuery(row).find('.toolset-alert-error').each(function(){
		jQuery(this).remove();
	});
}

// Add Filter popup

jQuery(document).on('change', '.js-filter-add-select', function(){
	if (jQuery(this).val() != '-1') {
		jQuery('.js-filters-insert-filter').addClass('button-primary').removeClass('button-secondary').prop('disabled', false);
	} else {
		jQuery('.js-filters-insert-filter').removeClass('button-primary').addClass('button-secondary').prop('disabled', true);
	}
});

jQuery(document).on('click', '.js-wpv-filter-add-filter', function(){
	jQuery(this).attr('disabled', true);
	jQuery('.js-filter-add-select').val('-1');
	jQuery('.js-filters-insert-filter').removeClass('button-primary').addClass('button-secondary').prop('disabled', true);
	var nonce = jQuery(this).data('nonce');
	wpv_update_filters_select(nonce, true);
});

function wpv_update_filters_select(nonce, openpopup){
	var data = {
		action: 'wpv_filters_upate_filters_select',
		id: jQuery('.js-post_ID').val(),
		wpnonce: nonce,
	};
	jQuery.post(ajaxurl, data, function(response) {
		if ( (typeof(response) !== 'undefined') ) {
			jQuery('.js-filter-add-select').replaceWith(response);
			if (openpopup) {
				wpv_open_filters_popup();
			}
		} else {
			console.log( "Error: AJAX returned ", response );
		}
	})
	.fail(function(jqXHR, textStatus, errorThrown) {
		console.log( "Error: ", textStatus, errorThrown );
	})
	.always(function() {
		jQuery('.js-wpv-filter-add-filter').attr('disabled', false);
	});
}

function wpv_open_filters_popup() {
	jQuery.colorbox({
		inline: true,
		href:'.js-filter-add-filter-form-dialog',
		open: true,
		onComplete: function() {
			var group = jQuery(".js-filter-add-select").find("optgroup");
			jQuery.each( group, function( i, v ) {
				if ( jQuery(v).children().length === 0 ) {
					jQuery(this).remove();
				}
			});
		}
	});
};

jQuery(document).on('click', '.js-filters-cancel-filter', function(){
	jQuery('.js-filter-add-select').val('-1');
});

jQuery(document).on('click','.js-filters-insert-filter', function(){
	var thiz = jQuery(this),
	filter_type = jQuery('.js-filter-add-select').val(),
	nonce = thiz.data('nonce'),
	spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(thiz).show(),
	data = {
		action: 'wpv_filters_add_filter_row',
		id: jQuery('.js-post_ID').val(),
		wpnonce: nonce,
		filter_type: filter_type
	};
	jQuery.post(ajaxurl, data, function(response) {
		if ( (typeof(response) !== 'undefined') ) {
			if (filter_type == 'post_category' || filter_type.substr(0, 9) == 'tax_input') {
				if (jQuery('.js-filter-list .js-filter-taxonomy').length > 0) {
					var filter_type_fixed = filter_type.replace('[', '_').replace(']', '');
					if (jQuery('.js-filter-list .js-filter-taxonomy .js-filter-row-tax-' + filter_type_fixed).length > 0) {
						jQuery('.js-filter-list .js-filter-taxonomy .js-filter-row-tax-' + filter_type_fixed).remove();
					}
					var responseRow = jQuery('.js-filter-list .js-filter-taxonomy-edit').prepend(response);
					jQuery('.js-filter-list .js-filter-taxonomy-edit').show();
					jQuery('.js-filter-list .js-wpv-filter-taxonomy-summary').hide();
				} else {
					var tax_dummy_row = jQuery('.js-filter-placeholder .js-filter-taxonomy').clone();
					jQuery('.js-filter-list').show().append(tax_dummy_row);
					var responseRow = jQuery('.js-filter-list .js-filter-taxonomy-edit').prepend(response);
					jQuery('.js-filter-list .js-filter-taxonomy-edit').show();
					jQuery('.js-filter-list .js-wpv-filter-taxonomy-summary').hide();
				}
				jQuery('.js-filter-list .js-filter-taxonomy').find('.js-wpv-filter-edit-controls').hide();
				var save_text = jQuery('.js-filter-list .js-filter-taxonomy').find('.js-wpv-filter-edit-ok').data('save');
				jQuery('.js-filter-list .js-filter-taxonomy').find('.js-wpv-filter-edit-ok').val(save_text).addClass('button-primary').removeClass('button-secundary').addClass('js-wpv-section-unsaved');
				wpv_taxonomy_mode();
				wpv_taxonomy_relationship();
			} else if (filter_type.substr(0, 12) == 'custom-field') {
				if (jQuery('.js-filter-list .js-filter-custom-field').length > 0) {
					if (jQuery('.js-filter-list .js-filter-custom-field .js-filter-row-' + filter_type).length > 0) {
						jQuery('.js-filter-list .js-filter-custom-field .js-filter-row-' + filter_type).remove();
					}
					var responseRow = jQuery('.js-filter-list .js-filter-custom-field-edit').prepend(response);
					jQuery('.js-filter-list .js-filter-custom-field-edit').show();
					jQuery('.js-filter-list .js-wpv-filter-custom-field-summary').hide();
				} else {
					var tax_dummy_row = jQuery('.js-filter-placeholder .js-filter-custom-field').clone();
					jQuery('.js-filter-list').show().append(tax_dummy_row);
					var responseRow = jQuery('.js-filter-list .js-filter-custom-field-edit').prepend(response);
					jQuery('.js-filter-list .js-filter-custom-field-edit').show();
					jQuery('.js-filter-list .js-wpv-filter-custom-field-summary').hide();
				}
				jQuery('.js-filter-list .js-filter-custom-field').find('.js-wpv-filter-edit-controls').hide();
				var save_text = jQuery('.js-filter-list .js-filter-custom-field').find('.js-wpv-filter-edit-ok').data('save');
				jQuery('.js-filter-list .js-filter-custom-field').find('.js-wpv-filter-edit-ok').val(save_text).addClass('button-primary').removeClass('button-secundary').addClass('js-wpv-section-unsaved');
				wpv_custom_field_initialize_compare();
				wpv_custom_field_initialize_compare_mode();
				wpv_custom_field_initialize_relationship();
				
			} else if (filter_type.substr(0, 14) == 'usermeta-field') {
				
				if (jQuery('.js-filter-list .js-filter-usermeta-field').length > 0) {
					if (jQuery('.js-filter-list .js-filter-usermeta-field .js-filter-row-' + filter_type).length > 0) {
						jQuery('.js-filter-list .js-filter-usermeta-field .js-filter-row-' + filter_type).remove();
					}
					var responseRow = jQuery('.js-filter-list .js-filter-usermeta-field-edit').prepend(response);
					jQuery('.js-filter-list .js-filter-usermeta-field-edit').show();
					jQuery('.js-filter-list .js-wpv-filter-usermeta-field-summary').hide();
				} else {
					var tax_dummy_row = jQuery('.js-filter-placeholder .js-filter-usermeta-field').clone();
					jQuery('.js-filter-list').show().append(tax_dummy_row);
					var responseRow = jQuery('.js-filter-list .js-filter-usermeta-field-edit').prepend(response);
					jQuery('.js-filter-list .js-filter-usermeta-field-edit').show();
					jQuery('.js-filter-list .js-wpv-filter-usermeta-field-summary').hide();
				}
				jQuery('.js-filter-list .js-filter-usermeta-field').find('.js-wpv-filter-edit-controls').hide();
				var save_text = jQuery('.js-filter-list .js-filter-usermeta-field').find('.js-wpv-filter-edit-ok').data('save');
				jQuery('.js-filter-list .js-filter-usermeta-field').find('.js-wpv-filter-edit-ok').val(save_text).addClass('button-primary').removeClass('button-secundary').addClass('js-wpv-section-unsaved');
				wpv_usermeta_field_initialize_compare();
				wpv_usermeta_field_initialize_compare_mode();
				wpv_usermeta_field_initialize_relationship();
				
			}else {
				jQuery('.js-filter-list .js-filter-row-' + filter_type).remove();
				var responseRow = jQuery('.js-filter-list').append(response);
				responseRow.find('.js-filter-row-' + filter_type + ' .js-wpv-filter-edit-open').attr('disabled', true).hide();
				responseRow.find('.js-filter-row-' + filter_type + ' .js-wpv-filter-edit-controls');
				responseRow.find('.js-filter-row-' + filter_type + ' .js-wpv-filter-summary').hide();
				var save_text = responseRow.find('.js-filter-row-' + filter_type + ' .js-wpv-filter-edit-ok').data('save');
				responseRow.find('.js-filter-row-' + filter_type + ' .js-wpv-filter-edit-ok').val(save_text).addClass('button-primary').removeClass('button-secundary').addClass('js-wpv-section-unsaved');
				wpv_users_suggest();
			}
			setConfirmUnload(true);
		//	jQuery('html,body').animate({scrollTop:jQuery('.js-filter-list').offset().top-25}, 500);
		} else {
			console.log( "Error: AJAX returned ", response );
		}

	})
	.fail(function(jqXHR, textStatus, errorThrown) {
		console.log( "Error: ", textStatus, errorThrown );
	})
	.always(function() {
		spinnerContainer.remove();
		jQuery.colorbox.close();
		jQuery('.js-filter-add-select').val('-1');
		wpv_taxonomy_relationship();
		wpv_filters_exist();
	});
});

// Remove filter

jQuery(document).on('click', '.js-filter-row-simple .js-filter-remove', function(){
	var thiz = jQuery(this),
	data_view_id = jQuery('.js-post_ID').val(),
	row = thiz.parents('li.js-filter-row'),
	filter = row.attr('id').substring(7),
	nonce = thiz.data('nonce'),
	action = 'wpv_filter_' + filter + '_delete',
	spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(thiz).show(),
	data = {
		action: action,
		id: data_view_id,
		wpnonce: nonce,
	};
	jQuery.post(ajaxurl, data, function(response) {
		if ( (typeof(response) !== 'undefined') ) {
			jQuery(row).find('.js-wpv-filter-edit-ok').removeClass('js-wpv-section-unsaved');
			if (jQuery('.js-wpv-section-unsaved').length < 1) {
				setConfirmUnload(false);
			}
			jQuery(row).fadeOut(500, function(){
				jQuery(this).remove();
				wpv_filters_exist();
			});
			jQuery('.js-filter-add-select').val('-1');
			wpv_update_parametric_search_section();
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

jQuery(document).on('click', '.js-wpv-filter-taxonomy-controls .js-filter-remove', function() {
	var thiz = jQuery(this),
	data_view_id = jQuery('.js-post_ID').val(),
	row = thiz.parents('.js-filter-row-multiple-element'),
	taxonomy = row.data('taxonomy'),
	nonce = thiz.data('nonce'),
	spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(thiz).show(),
	data = {
		action: 'wpv_filter_taxonomy_delete',
		id: data_view_id,
		taxonomy: taxonomy,
		wpnonce: nonce,
	};
	jQuery.post(ajaxurl, data, function(response) {
		if ( (typeof(response) !== 'undefined') && (response === data.id) ) {
			if ( !jQuery('.js-filter-list .js-filter-taxonomy .js-filter-taxonomy-row-remove').hasClass('js-multiple-items') ) {
				jQuery('.js-filter-list .js-filter-taxonomy').find('.js-wpv-filter-edit-ok').removeClass('js-wpv-section-unsaved');
				if (jQuery('.js-wpv-section-unsaved').length < 1) {
					setConfirmUnload(false);
				}
			}
			jQuery(row).fadeOut(500, function(){
				jQuery(this).remove();
				wpv_taxonomy_relationship();
				wpv_filters_exist();
			});
			jQuery('.js-filter-add-select').val('-1');
			wpv_update_parametric_search_section();
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

jQuery(document).on('click', '.js-filter-taxonomy .js-filter-taxonomy-row-remove', function(e) {
	if (jQuery(this).hasClass('js-multiple-items')) {
		jQuery.colorbox({
			inline: true,
			href:'.js-filter-taxonomy-delete-filter-row-dialog',
			open: true
		});
	} else {
		var spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(jQuery(this)).show();
		wpv_remove_taxonomy_filters();
		spinnerContainer.remove();
	}
});

function wpv_remove_taxonomy_filters() {
	jQuery('.js-filter-list .js-filter-taxonomy').find('.js-wpv-filter-edit-ok').removeClass('js-wpv-section-unsaved');
	if (jQuery('.js-wpv-section-unsaved').length < 1) {
		setConfirmUnload(false);
	}
	jQuery('.js-filter-list .js-filter-taxonomy .js-filter-remove').each(function() {
		var data_view_id = jQuery('.js-post_ID').val(),
		row = jQuery(this).parents('.js-filter-row-multiple-element'),
		taxonomy = row.data('taxonomy'),
		nonce = jQuery(this).data('nonce');
		var data = {
			action: 'wpv_filter_taxonomy_delete',
			id: data_view_id,
			taxonomy: taxonomy,
			wpnonce: nonce,
		};
		jQuery.ajax({
			async:false,
			type:"POST",
			url:ajaxurl,
			data:data,
			success:function(response){
				if ( (typeof(response) !== 'undefined') && (response === data.id) ) {
					jQuery(row).fadeOut(500, function(){
						jQuery(this).remove();
						wpv_taxonomy_relationship();
						wpv_filters_exist();
					});
					jQuery('.js-filter-add-select').val('-1');
				} else {
					console.log( "Error: AJAX returned ", response );
				}
			},
			error: function (ajaxContext) {
				console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() {
				
			}
		});
	});
	wpv_update_parametric_search_section();
}

jQuery(document).on('click', '.js-filter-taxonomy-edit-filter-row', function(e) {
	e.preventDefault();
	jQuery('.js-filter-list .js-filter-taxonomy .js-wpv-filter-edit-open').trigger('click');
	jQuery('.js-filter-taxonomy .js-filter-taxonomy-row-remove').colorbox.close();
})

jQuery(document).on('click', '.js-filters-taxonomy-delete-filter-row', function() {
	var spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(jQuery(this)).show();
	wpv_remove_taxonomy_filters();
	spinnerContainer.remove();
	jQuery('.js-filter-taxonomy .js-filter-taxonomy-row-remove').colorbox.close();
});

jQuery(document).on('click', '.js-wpv-filter-custom-field-controls .js-filter-remove', function() {
	var thiz = jQuery(this),
	data_view_id = jQuery('.js-post_ID').val(),
	row = thiz.parents('.js-filter-row-multiple-element'),
	field = row.data('field'),
	nonce = thiz.data('nonce'),
	spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(thiz).show(),
	data = {
		action: 'wpv_filter_custom_field_delete',
		id: data_view_id,
		field: field,
		wpnonce: nonce,
	};
	jQuery.post(ajaxurl, data, function(response) {
		if ( (typeof(response) !== 'undefined') && (response === data.id) ) {
			if ( !jQuery('.js-filter-list .js-filter-custom-field .js-filter-custom-field-row-remove').hasClass('js-multiple-items') ) {
				jQuery('.js-filter-list .js-filter-custom-field').find('.js-wpv-filter-edit-ok').removeClass('js-wpv-section-unsaved');
				if (jQuery('.js-wpv-section-unsaved').length < 1) {
					setConfirmUnload(false);
				}
			}
			jQuery(row).fadeOut(500, function(){
				jQuery(this).remove();
				wpv_custom_field_initialize_relationship();
				wpv_filters_exist();
			});
			jQuery('.js-filter-add-select').val('-1');
			wpv_update_parametric_search_section();
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

jQuery(document).on('click', '.js-filter-custom-field .js-filter-custom-field-row-remove', function(e) {
	if (jQuery(this).hasClass('js-multiple-items')) {
		jQuery.colorbox({
			inline: true,
			href:'.js-filter-custom-field-delete-filter-row-dialog',
			open: true
		});
	} else {
		var spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(jQuery(this)).show();
		wpv_remove_custom_field_filters();
		spinnerContainer.remove();
	}
});


function wpv_remove_custom_field_filters() {
	jQuery('.js-filter-list .js-filter-custom-field').find('.js-wpv-filter-edit-ok').removeClass('js-wpv-section-unsaved');
	if (jQuery('.js-wpv-section-unsaved').length < 1) {
		setConfirmUnload(false);
	}
	jQuery('.js-filter-list .js-filter-custom-field .js-filter-remove').each(function() {
		var data_view_id = jQuery('.js-post_ID').val(),
		row = jQuery(this).parents('.js-filter-row-multiple-element'),
		field = row.data('field'),
		nonce = jQuery(this).data('nonce');
		var data = {
			action: 'wpv_filter_custom_field_delete',
			id: data_view_id,
			field: field,
			wpnonce: nonce,
		};
		jQuery.ajax({
			async:false,
			type:"POST",
			url:ajaxurl,
			data:data,
			success:function(response){
				if ( (typeof(response) !== 'undefined') && (response === data.id) ) {
				     jQuery(row).fadeOut(500, function(){
					     jQuery(this).remove();
					     wpv_custom_field_initialize_relationship();
					     wpv_filters_exist();
				     });
				     jQuery('.js-filter-add-select').val('-1');
				} else {
				     console.log( "Error: AJAX returned ", response );
				}
		     },
		     error: function (ajaxContext) {
			     console.log( "Error: ", ajaxContext.responseText );
		     },
		     complete: function() {
			     
		     }
		});
	});
	wpv_update_parametric_search_section();
}

jQuery(document).on('click', '.js-filter-custom-field-edit-filter-row', function(e) {
	e.preventDefault();
	jQuery('.js-filter-list .js-filter-custom-field .js-wpv-filter-edit-open').trigger('click');
	jQuery('.js-filter-custom-field .js-filter-custom-field-row-remove').colorbox.close();
})

jQuery(document).on('click', '.js-filters-custom-field-delete-filter-row', function() {
	var spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(jQuery(this)).show();
	wpv_remove_custom_field_filters();
	spinnerContainer.remove();
	jQuery('.js-filter-custom-field .js-filter-custom-field-row-remove').colorbox.close();
});


jQuery(document).on('click', '.js-wpv-filter-usermeta-field-controls .js-filter-remove', function() {
	var thiz = jQuery(this),
	data_view_id = jQuery('.js-post_ID').val(),
	row = thiz.parents('.js-filter-row-multiple-element'),
	field = row.data('field'),
	nonce = thiz.data('nonce'),
	spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(thiz).show(),
	data = {
		action: 'wpv_filter_usermeta_field_delete',
		id: data_view_id,
		field: field,
		wpnonce: nonce,
	};
	jQuery.post(ajaxurl, data, function(response) {
		if ( (typeof(response) !== 'undefined') && (response === data.id) ) {
			if ( !jQuery('.js-filter-list .js-filter-usermeta-field .js-filter-usermeta-field-row-remove').hasClass('js-multiple-items') ) {
				jQuery('.js-filter-list .js-filter-usermeta-field').find('.js-wpv-filter-edit-ok').removeClass('js-wpv-section-unsaved');
				if (jQuery('.js-wpv-section-unsaved').length < 1) {
					setConfirmUnload(false);
				}
			}
			jQuery(row).fadeOut(500, function(){
				jQuery(this).remove();
				wpv_usermeta_field_initialize_relationship();
				wpv_filters_exist();
			});
			jQuery('.js-filter-add-select').val('-1');
			wpv_update_parametric_search_section();
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
jQuery(document).on('click', '.js-filter-usermeta-field .js-filter-usermeta-field-row-remove', function(e) {
	if (jQuery(this).hasClass('js-multiple-items')) {
		jQuery.colorbox({
			inline: true,
			href:'.js-filter-usermeta-field-delete-filter-row-dialog',
			open: true
		});
	} else {
		wpv_remove_usermeta_field_filters();
	}
});
function wpv_remove_usermeta_field_filters() {
	jQuery('.js-filter-list .js-filter-usermeta-field').find('.js-wpv-filter-edit-ok').removeClass('js-wpv-section-unsaved');
	if (jQuery('.js-wpv-section-unsaved').length < 1) {
		setConfirmUnload(false);
	}
	jQuery('.js-filter-list .js-filter-usermeta-field .js-filter-remove').each(function() {
		var data_view_id = jQuery('.js-post_ID').val(),
		row = jQuery(this).parents('.js-filter-row-multiple-element'),
		field = row.data('field'),
		nonce = jQuery(this).data('nonce');
	//	console.log(row);
		var data = {
			action: 'wpv_filter_usermeta_field_delete',
			id: data_view_id,
			field: field,
			wpnonce: nonce
		};
		jQuery.ajax({
			async:false,
			type:"POST",
			url:ajaxurl,
			data:data,
			success:function(response){
				
				if ( (typeof(response) !== 'undefined') && (response === data.id) ) {
				     jQuery(row).fadeOut(500, function(){
					     jQuery(this).remove();
					     wpv_usermeta_field_initialize_relationship();
					     wpv_filters_exist();
				     });
				     jQuery('.js-filter-add-select').val('-1');
				} else {
				     console.log( "Error: AJAX returned ", response );
				}
		     },
		     error: function (ajaxContext) {
			     console.log( "Error: ", ajaxContext.responseText );
		     },
		     complete: function() {
			     
		     }
		});
	});
	wpv_update_parametric_search_section();
}

jQuery(document).on('click', '.js-filter-usermeta-field-edit-filter-row', function(e) {
	e.preventDefault();
	jQuery('.js-filter-list .js-filter-usermeta-field .js-wpv-filter-edit-open').trigger('click');
	jQuery('.js-filter-usermeta-field .js-filter-usermeta-field-row-remove').colorbox.close();
})

jQuery(document).on('click', '.js-filters-usermeta-field-delete-filter-row', function() {
	var spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(jQuery(this)).show();
	wpv_remove_usermeta_field_filters();
	spinnerContainer.remove();
	jQuery('.js-filter-usermeta-field .js-filter-usermeta-field-row-remove').colorbox.close();
});

/*
 * Parametric search and dependency
 */

jQuery(document).on('click', '.js-make-intersection-filters', function( e ) { // TODO finish this, for god sake
	e.preventDefault();
	var thiz = jQuery(this),
	spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(thiz).show(),
	data = {
		action: 'wpv_filter_make_intersection_filters',
		id: jQuery('.js-post_ID').val(),
		nonce: thiz.data('nonce')
	};
	jQuery.post(ajaxurl, data, function(response) {
		if ( (typeof(response) !== 'undefined') ) {
			decoded_response = jQuery.parseJSON(response);
			if ( decoded_response.success === data.id ) {
				jQuery('.js-filter-list').html(decoded_response.wpv_filter_update_filters_list);
				jQuery('.js-wpv-dps-settings').html(decoded_response.wpv_dps_settings_structure);
				wpv_after_update_filters_list();
			}
		} else {
			//if(  WPV_Parametric.debug ) console.log( WPV_Parametric.ajax_error, response );
		}
	})
	.fail(function(jqXHR, textStatus, errorThrown) {
		//if(  WPV_Parametric.debug ) console.log( WPV_Parametric.error_generic, textStatus, errorThrown );
	})
	.always(function() {
		spinnerContainer.remove();
	});
});

jQuery(document).on('change', '.js-wpv-dps-toggle-container', function() {
	var toggle_target = jQuery(this).data('toggletarget'),
	container = jQuery( '.' + toggle_target );
	if ( jQuery(this).val() == 'enable' ) {
		container.fadeIn('fast');
	} else {
		container.fadeOut('fast');
	}
});

jQuery(document).on('change', '.js-wpv-dps-spinner', function() {
	// Make sections data show or hide based on value
	// First, filter by the $this that is checked!! IMPORTANT
	var thiz = jQuery(this).filter(':checked'),
	toggle_target = thiz.data('toggletarget'),
	container = jQuery( '.' + toggle_target );
	jQuery('.js-wpv-dps-spinner-extra').hide();
	container.fadeIn('fast');
});

jQuery(document).on('click', '.js-wpv-dps-advanced-toggle', function(e) {
	e.preventDefault();
	var thiz = jQuery(this),
	state = thiz.data('state'),
	text = '';
	if (state == 'closed') {
		thiz.data('state','opened');
		thiz.text(thiz.data('opened'));
		jQuery('.js-wpv-dps-advanced').fadeIn('fast');
	}
	else if (state == 'opened') {
		thiz.data('state','closed');
		thiz.text(thiz.data('closed'));
		jQuery('.js-wpv-dps-advanced').hide();
	}
});