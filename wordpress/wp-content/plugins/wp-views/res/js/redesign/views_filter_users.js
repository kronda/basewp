// users Suggest

jQuery(document).ready(function(){
	wpv_users_suggest();
});

jQuery(document).on('click', '.js-wpv-filter-users-edit-open', function(){
	if ( typeof(jQuery('.token-input-list-wpv').html()) === 'undefined' ){
		wpv_users_suggest();
	}

});

function wpv_users_suggest(){
	text_noresult = jQuery('.js-wpv-user-suggest-values').data('noresult');
	text_hint = jQuery('.js-wpv-user-suggest-values').data('hinttext');
	text_search = jQuery('.js-wpv-user-suggest-values').data('search');
	view_id = jQuery('.js-wpv-user-suggest-values').data('viewid');
	users = jQuery('.js-wpv-user-suggest-values').data('users');
	jQuery(".js-users-suggest-id").tokenInput(ajaxurl + '?action=wpv_suggest_users&view_id='+view_id, {
			        theme: "wpv",
			        preventDuplicates: true,
			        hintText: text_hint,
	                noResultsText: text_noresult,
	                searchingText: text_search,
	                prePopulate: users,
	                onAdd: function (item) {
                     	var tokens = jQuery(this).tokenInput('get');
  	                    var user_val = '';
	                    jQuery.each(tokens, function (index, value) {
		                    user_val += value.name+', ';
		                });
		                user_val = user_val.substr(0,(user_val.length - 2));
		                jQuery('.js-users-suggest').val(user_val);

	                 },
	                 onDelete: function (item) {
	                    var tokens = jQuery(this).tokenInput('get');
  	                    var user_val = '';
	                    jQuery.each(tokens, function (index, value) {
		                    user_val += value.name+', ';
		                });
		                user_val = user_val.substr(0,(user_val.length - 2));
		                jQuery('.js-users-suggest').val(user_val);
	                 }
			    });

}
// users Close and save

var wpv_filter_users_selected = jQuery('.js-filter-users-list input, .js-filter-users-list select').serialize();

jQuery(document).on('click', '.js-wpv-filter-users-edit-open', function(){ // rebuild the list of the current checked values
	wpv_filter_users_selected = jQuery('.js-filter-users-list input, .js-filter-users-list select').serialize();
});

jQuery(document).on('change keyup input cut paste', '.js-filter-users-list input, .js-filter-users-list select', function() { // watch on inputs change
	jQuery(this).removeClass('filter-input-error');
	jQuery('.js-wpv-filter-users-edit-ok').prop('disabled', false);
	wpv_clear_validate_messages('.js-filter-users');
	if ( wpv_filter_users_selected != jQuery('.js-filter-users-list input, .js-filter-users-list select').serialize() ) {
		jQuery('.js-wpv-filter-users-edit-ok').removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-users-edit-ok').data('save'));
		setConfirmUnload(true);
	} else {
		jQuery('.js-wpv-filter-users-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-users-edit-ok').data('close'));
		jQuery('.js-wpv-filter-users-edit-ok').parent().find('.unsaved').remove();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	}
});

jQuery(document).on('click', '.js-wpv-filter-users-edit-ok', function() {
	jQuery(this).parent().find('.unsaved').remove();
	if (wpv_filter_users_selected == jQuery('.js-filter-users-list input, .js-filter-users-list select').serialize() ) {
		wpv_close_filter_row('.js-filter-users');
	} else {
		var valid = wpv_validate_filter_inputs('.js-filter-users');
		if (valid) {
			var update_message = jQuery(this).data('success');
			var unsaved_message = jQuery(this).data('unsaved');
			var nonce = jQuery(this).data('nonce');
			wpv_filter_users_selected = jQuery('.js-filter-users-list input, .js-filter-users-list select').serialize();
			var spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(jQuery(this)).show();
			var data_view_id = jQuery('.js-post_ID').val();
			var data = {
				action: 'wpv_filter_users_update',
				id: data_view_id,
				filter_users: wpv_filter_users_selected,
				wpnonce: nonce
			};
			jQuery.post(ajaxurl, data, function(response) {
				if ( (typeof(response) !== 'undefined') ) {
					if (response != 0) {
						jQuery('.js-wpv-filter-users-edit-ok').removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved').val(jQuery('.js-wpv-filter-users-edit-ok').data('close'));
						if (jQuery('.js-wpv-section-unsaved').length < 1) {
							setConfirmUnload(false);
						}
						jQuery('.js-wpv-filter-users-summary').html(response);
						jQuery('.js-wpv-filter-users-summary').append('<span class="updated toolset-alert toolset-alert-success"><i class="icon-check"></i> ' + update_message + '</span>');
						setTimeout(function(){
							jQuery('.js-wpv-filter-users-summary .updated').fadeOut('fast');
						}, 2000);
						wpv_close_filter_row('.js-filter-users');
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

jQuery(document).on('click', '.js-filter-users .js-filter-remove', function(){
	wpv_filter_users_selected = '';
});