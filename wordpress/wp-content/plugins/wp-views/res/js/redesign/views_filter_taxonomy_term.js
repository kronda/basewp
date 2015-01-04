var wpv_filter_tax_term_mode_selected = jQuery('.js-wpv-taxonomy-term-mode').val();
var wpv_filter_tax_term_selected = jQuery('.js-taxonomy-term-checklist input').serialize();
var wpv_filter_tax_term_change = false;

jQuery(document).on('click', '.js-wpv-filter-taxonomy-term-edit-open', function(){ // rebuild the list of the current checked values
	wpv_filter_tax_term_mode_selected = jQuery('.js-wpv-taxonomy-term-mode').val();
	wpv_filter_tax_term_selected = jQuery('.js-taxonomy-term-checklist input').serialize();
});

jQuery(document).on('change', '.js-wpv-taxonomy-term-mode, .js-taxonomy-term-checklist input', function() { // watch on inputs change
	wpv_filter_tax_term_change = false;
	if ( wpv_filter_tax_term_mode_selected != jQuery('.js-wpv-taxonomy-term-mode').val() ) {
		wpv_filter_tax_term_change = true;
	}
	if ( wpv_filter_tax_term_selected != jQuery('.js-taxonomy-term-checklist input').serialize() ) {
		wpv_filter_tax_term_change = true;
	}
	if (wpv_filter_tax_term_change) {
		jQuery('.js-wpv-filter-taxonomy-term-edit-ok').removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-taxonomy-term-edit-ok').data('save'));
		setConfirmUnload(true);
	} else {
		jQuery('.js-wpv-filter-taxonomy-term-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-taxonomy-term-edit-ok').data('close'));
		jQuery('.js-wpv-filter-taxonomy-term-edit-ok').parent().find('.unsaved').remove();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	}
	wpv_filter_taxonomy_term_mode();
});

jQuery(document).ready(function(){
	wpv_filter_taxonomy_term_mode();
});

function wpv_filter_taxonomy_term_mode() {
	if (jQuery('.js-filter-taxonomy-term').length) {
		var tax_term_mode = jQuery('.js-wpv-taxonomy-term-mode').val();
		if (tax_term_mode == 'CURRENT_PAGE') {
			jQuery('.js-taxonomy-term-checklist').hide();
		} else {
			jQuery('.js-taxonomy-term-checklist').fadeIn(500);
		}
	}
}

jQuery(document).on('click', '.js-wpv-filter-taxonomy-term-edit-ok', function() { // save new settings if needed and close edit box
	jQuery(this).parent().find('.unsaved').remove();
	if ( wpv_filter_tax_term_mode_selected == jQuery('.js-wpv-taxonomy-term-mode').val()
		&& wpv_filter_tax_term_selected == jQuery('.js-taxonomy-term-checklist input').serialize()
	) {
		wpv_close_filter_row('.js-filter-taxonomy-term');
		jQuery('.js-wpv-filter-taxonomy-term-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-taxonomy-term-edit-ok').data('close'));
	} else {
		var update_message = jQuery(this).data('success');
		var unsaved_message = jQuery(this).data('unsaved');
		var nonce = jQuery(this).data('nonce');
		wpv_filter_tax_term_mode_selected = jQuery('.js-wpv-taxonomy-term-mode').val();
		wpv_filter_tax_term_selected = jQuery('.js-taxonomy-term-checklist input').serialize();
		var spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(jQuery(this)).show();
		var data_view_id = jQuery('.js-post_ID').val();
		var wpv_filter_tax_term_tax_type = jQuery('.js-wpv-query-taxonomy-type:checked').val();
		var data = {
			action: 'wpv_filter_taxonomy_term_update',
		    id: data_view_id,
		    tax_term_mode: wpv_filter_tax_term_mode_selected,
		    tax_term_list: wpv_filter_tax_term_selected,
		    tax_term_tax_type: wpv_filter_tax_term_tax_type,
		    wpnonce: nonce
		};
		jQuery.post(ajaxurl, data, function(response) {
			if ( (typeof(response) !== 'undefined') ) {
				if (response != 0) {
					jQuery('.js-wpv-filter-taxonomy-term-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-taxonomy-term-edit-ok').data('close'));
					if (jQuery('.js-wpv-section-unsaved').length < 1) {
						setConfirmUnload(false);
					}
					jQuery('.js-wpv-filter-taxonomy-term-summary').html(response);
					jQuery('.js-wpv-filter-taxonomy-term-summary').append('<span class="updated toolset-alert toolset-alert-success"><i class="icon-check"></i> ' + update_message + '</span>');
					setTimeout(function(){
						jQuery('.js-wpv-filter-taxonomy-term-summary .updated').fadeOut('fast');
					}, 2000);
					wpv_close_filter_row('.js-filter-taxonomy-term');
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
/*
function wpv_validate_filter_taxonomy_term() {
	if (jQuery('.js-wpv-filter-taxonomy-term-edit-ok').length) {
		var nonce = jQuery('.js-wpv-filter-taxonomy-term-edit-ok').data('nonce');
		var data = {
			action: 'wpv_filter_taxonomy_term_test',
			tax_term_list: jQuery('.js-taxonomy-term-checklist input').serialize(),
			tax_type: jQuery('.js-wpv-query-taxonomy-type:checked').val(),
			wpnonce: nonce
		}
		alert(jQuery('.js-taxonomy-term-checklist input').serialize());
		jQuery.post(ajaxurl, data, function(response) {alert(response);
			if ( (typeof(response) !== 'undefined')) {
				if (response == 'bad') {
					wpv_filter_taxonomy_term_changed.insertAfter('.js-wpv-filter-taxonomy-term-summary').show();
				} else {
					wpv_filter_taxonomy_term_changed.remove();
				}
			} else {
				console.log( "Error: AJAX returned ", response );
			}

		})
		.fail(function(jqXHR, textStatus, errorThrown) {
			console.log( "Error: ", textStatus, errorThrown );
		});
	}
}
*/
jQuery(document).on('click', '.js-filter-taxonomy-term .js-filter-remove', function(){
	wpv_filter_tax_term_mode_selected = '';
	wpv_filter_tax_term_selected = '';
	wpv_filter_tax_term_change = false;
});