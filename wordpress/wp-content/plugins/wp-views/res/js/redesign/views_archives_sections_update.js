// Error handling

jQuery(document).ready(function(){
	jQuery.ajaxSetup({
		error:function(x,e){
			if(x.status==0){
				console.log('You are offline!!n Please Check Your Network.');
			}else if(x.status==404){
				console.log('Requested URL not found.');
			}else if(x.status==500){
				console.log('Internel Server Error.');
			}else if(e=='timeout'){
				console.log('Request Time out.');
			}else {
				console.log('Unknow Error.n'+x.responseText);
			}
		}
	});
});


// Collection of previous values

var view_settings = [];
view_settings['.js-wpv-title'] = jQuery('.js-title').val();
view_settings['.js-wpv-description'] = jQuery('.js-wpv-description').val();
view_settings['.js-wpv-slug'] = jQuery('.js-wpv-slug').val();
view_settings['.js-wpv-layout-settings-extra-js'] = jQuery('.js-wpv-layout-settings-extra-js').val();
view_settings['.js-wpv-loop-selection'] = jQuery('.js-loop-selection-form').serialize();

var codemirror_views_layout = icl_editor.codemirror('wpv_layout_meta_html_content', true),
codemirror_views_layout_val = codemirror_views_layout.getValue(),
codemirror_views_layout_css = icl_editor.codemirror('wpv_layout_meta_html_css', true, 'css'),
codemirror_views_layout_css_val = codemirror_views_layout_css.getValue(),
codemirror_views_layout_js = icl_editor.codemirror('wpv_layout_meta_html_js', true, 'javascript'),
codemirror_views_layout_js_val = codemirror_views_layout_js.getValue(),
codemirror_views_content = icl_editor.codemirror('wpv_content', true),
codemirror_views_content_val = codemirror_views_content.getValue();

// Description update

jQuery(document).on('keyup input cut paste', '.js-wpv-description, .js-title, .js-wpv-slug', function(){
	jQuery('.js-wpv-title-description-update').parent().find('.toolset-alert-error').remove();
	if (view_settings['.js-wpv-description'] != jQuery('.js-wpv-description').val() || view_settings['.js-wpv-title'] != jQuery('.js-title').val() || view_settings['.js-wpv-slug'] != jQuery('.js-wpv-slug').val()) {
		jQuery('.js-wpv-title-description-update').prop('disabled', false).removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved');
		setConfirmUnload(true);
	} else {
		jQuery('.js-wpv-title-description-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved');
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	}
});

jQuery(document).on('click', '.js-wpv-title-description-update', function(e){
	e.preventDefault();
	var update_message = jQuery(this).data('success'),
		unsaved_message = jQuery(this).data('unsaved'),
		nonce = jQuery(this).data('nonce'),
		spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertBefore(jQuery(this)).show(),
		data_view_id = jQuery('.js-post_ID').val();
	jQuery(this).parent().find('.toolset-alert-error').remove();
	jQuery('.js-wpv-title-description-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary');
	var data = {
		action: 'wpv_update_title_description',
		id: data_view_id,
		description: jQuery('.js-wpv-description').val(),
		title: jQuery('.js-title').val(),
		slug: jQuery('.js-wpv-slug').val(),
		wpnonce: nonce
	};
	jQuery.ajax({
		async:false,
		type:"POST",
		url:ajaxurl,
		data:data,
		success:function(response){
			//Check if name already exists
			temp_res = jQuery.parseJSON(response);
			if ( (typeof(response) !== 'undefined') && temp_res[0] == 'error' ){
				jQuery('.js-wpv-title-description-update').parent().wpvToolsetMessage({
					text:temp_res[1],
					type:'',
					inline:true,
					stay:true
				});
				return false;
			}
			// If all is fine, response is post_ID
			if ( (typeof(response) !== 'undefined') && (response === data.id)) {
				jQuery('.js-wpv-title-description-update').removeClass('js-wpv-section-unsaved');
				view_settings['.js-wpv-description'] = jQuery('.js-wpv-description').val();
				view_settings['.js-wpv-title'] = jQuery('.js-title').val();
				view_settings['.js-wpv-slug'] = jQuery('.js-wpv-slug').val();
				if (jQuery('.js-wpv-section-unsaved').length < 1) {
					setConfirmUnload(false);
				}
				jQuery('.js-wpv-title-description-update').parent().wpvToolsetMessage({
					text:update_message,
					type:'success',
					inline:true,
					stay:false
				});
			} else {
				jQuery('.js-wpv-title-description-update').parent().wpvToolsetMessage({
					text:unsaved_message,
					type:'error',
					inline:true,
					stay:true
				});
				console.log( "Error: AJAX returned ", response );
			}
		},
		error: function (ajaxContext) {
			jQuery('.js-wpv-title-description-update').parent().wpvToolsetMessage({
				text:unsaved_message,
				type:'error',
				inline:true,
				stay:true
			});
			console.log( "Error: ", ajaxContext.responseText );
		},
		complete: function() {
			spinnerContainer.remove();
		}
	});
});

// Loop Selection update

jQuery(document).on('change', '.js-loop-selection-form input', function(){
	jQuery('.js-wpv-loop-selection-update').parent().find('.unsaved').remove();
	var newchecked = jQuery('.js-loop-selection-form').serialize();
	if (view_settings['.js-wpv-loop-selection'] != newchecked) {
		jQuery('.js-wpv-loop-selection-update').prop('disabled', false).removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved');
		setConfirmUnload(true);
	} else {
		jQuery('.js-wpv-loop-selection-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved');
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	}
});

jQuery(document).on('click', '.js-wpv-loop-selection-update', function(e){
	e.preventDefault();
	view_settings['.js-wpv-loop-selection'] = jQuery('.js-loop-selection-form').serialize();
	var update_message = jQuery(this).data('success'),
		    unsaved_message = jQuery(this).data('unsaved'),
		    nonce = jQuery(this).data('nonce'),
		    spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertBefore(jQuery(this)).show(),
	data_view_id = jQuery('.js-post_ID').val();
	jQuery(this).parent().find('.unsaved').remove();
	jQuery(this).parent().find('.toolset-alert-error').remove();
	jQuery('.js-wpv-loop-selection-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary');
	var data = {
		action: 'wpv_update_loop_selection',
		id: data_view_id,
		form: jQuery('.js-loop-selection-form').serialize(),
		wpnonce: nonce
	};
	jQuery.ajax({
		async:false,
		type:"POST",
		url:ajaxurl,
		data:data,
		success:function(response){
			decoded_response = jQuery.parseJSON(response);
			if ( decoded_response.success === data.id ) {
				jQuery('.js-loop-selection-form').html(decoded_response.wpv_settings_archive_loops);
				jQuery('.js-wpv-loop-selection-update').removeClass('js-wpv-section-unsaved');
				if (jQuery('.js-wpv-section-unsaved').length < 1) {
					setConfirmUnload(false);
				}
				jQuery('<span class="updated toolset-alert toolset-alert-success"><i class="icon-check"></i> ' + update_message + '</span>').insertAfter(jQuery('.js-wpv-loop-selection-update')).hide().fadeIn(500).delay(1000).fadeOut(500, function(){
					jQuery(this).remove();
				});
			} else {
				jQuery('<span class="unsaved toolset-alert toolset-alert-error"><i class="icon-warning-sign"></i> ' + unsaved_message + '</span>').insertAfter(jQuery('.js-wpv-loop-selection-update')).show();
				console.log( "Error: AJAX returned ", response );
			}
		},
		error: function (ajaxContext) {
			jQuery('<span class="unsaved toolset-alert toolset-alert-error"><i class="icon-warning-sign"></i> ' + unsaved_message + '</span>').insertAfter(jQuery('.js-wpv-loop-selection-update')).show();
			console.log( "Error: ", ajaxContext.responseText );
		},
		complete: function() {
			spinnerContainer.remove();
		}
	});
});

// Layout Extra update

codemirror_views_layout.on('change', function(){
	jQuery('.js-wpv-layout-extra-update').parent().find('.toolset-alert-error').remove();
	if (codemirror_views_layout_val != codemirror_views_layout.getValue()
		|| codemirror_views_layout_css_val != codemirror_views_layout_css.getValue()
		|| codemirror_views_layout_js_val != codemirror_views_layout_js.getValue()
	) {
		jQuery('.js-wpv-layout-extra-update').prop('disabled', false).removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved');
		setConfirmUnload(true);
	} else {
		jQuery('.js-wpv-layout-extra-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved');
		jQuery('.js-screen-options').find('.toolset-alert').remove();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	}
});
codemirror_views_layout_css.on('change', function(){
	jQuery('.js-wpv-layout-extra-update').parent().find('.toolset-alert-error').remove();
	if (codemirror_views_layout_val != codemirror_views_layout.getValue()
		|| codemirror_views_layout_css_val != codemirror_views_layout_css.getValue()
		|| codemirror_views_layout_js_val != codemirror_views_layout_js.getValue()
	) {
		jQuery('.js-wpv-layout-extra-update').prop('disabled', false).removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved');
		setConfirmUnload(true);
	} else {
		jQuery('.js-wpv-layout-extra-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved');
		jQuery('.js-screen-options').find('.toolset-alert').remove();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	}
});
codemirror_views_layout_js.on('change', function(){
	jQuery('.js-wpv-layout-extra-update').parent().find('.toolset-alert-error').remove();
	if (codemirror_views_layout_val != codemirror_views_layout.getValue()
		|| codemirror_views_layout_css_val != codemirror_views_layout_css.getValue()
		|| codemirror_views_layout_js_val != codemirror_views_layout_js.getValue()
	) {
		jQuery('.js-wpv-layout-extra-update').prop('disabled', false).removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved');
		setConfirmUnload(true);
	} else {
		jQuery('.js-wpv-layout-extra-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved');
		jQuery('.js-screen-options').find('.toolset-alert').remove();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	}
});

jQuery(document).on('click', '.js-wpv-layout-extra-update', function(e){
	e.preventDefault();
	codemirror_views_layout_val = codemirror_views_layout.getValue();
	codemirror_views_layout_css_val = codemirror_views_layout_css.getValue();
	codemirror_views_layout_js_val = codemirror_views_layout_js.getValue();
	var update_message = jQuery(this).data('success'),
		    unsaved_message = jQuery(this).data('unsaved'),
		    nonce = jQuery(this).data('nonce'),
		    spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertBefore(jQuery(this)).show(),
	data_view_id = jQuery('.js-post_ID').val();
	jQuery(this).parent().find('.toolset-alert-error').remove();
	jQuery('.js-wpv-layout-extra-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary');
	var data = {
		action: 'wpv_update_layout_extra',
		id: data_view_id,
		layout_val: codemirror_views_layout_val,
		layout_css_val: codemirror_views_layout_css_val,
		layout_js_val: codemirror_views_layout_js_val,
		wpnonce: nonce
	};

	// Include the wizard settings.
	if (wpv_layout_settings_from_wizard) {
		for (var attr_name in wpv_layout_settings_from_wizard) {
			data[attr_name] = wpv_layout_settings_from_wizard[attr_name];
		}
	}
	
	jQuery.ajax({
		async:false,
		type:"POST",
		url:ajaxurl,
		data:data,
		success:function(response){
			if ( (typeof(response) !== 'undefined') && (response === data.id)) {
				jQuery('.js-wpv-layout-extra-update').removeClass('js-wpv-section-unsaved');
				jQuery('.js-screen-options').find('.toolset-alert').remove();
				if (jQuery('.js-wpv-section-unsaved').length < 1) {
					setConfirmUnload(false);
				}
				jQuery('.js-wpv-layout-extra-update').parent().wpvToolsetMessage({
					text:update_message,
					type:'success',
					inline:true,
					stay:false
				});
			} else {
				jQuery('.js-wpv-layout-extra-update').parent().wpvToolsetMessage({
					text:unsaved_message,
					type:'error',
					inline:true,
					stay:true
				});
				console.log( "Error: AJAX returned ", response );
			}
		},
		error: function (ajaxContext) {
			jQuery('.js-wpv-layout-extra-update').parent().wpvToolsetMessage({
				text:unsaved_message,
				type:'error',
				inline:true,
				stay:true
			});
			console.log( "Error: ", ajaxContext.responseText );
		},
		complete: function() {
			spinnerContainer.remove();
		}
	});
	
});

// Layout aditional JS update

jQuery(document).on('keyup input cut paste', '.js-wpv-layout-settings-extra-js', function(){
	jQuery('.js-wpv-layout-settings-extra-js-update').parent().find('.toolset-alert-error').remove();
	if (view_settings['.js-wpv-layout-settings-extra-js'] != jQuery(this).val()) {
		jQuery('.js-wpv-layout-settings-extra-js-update').prop('disabled', false).removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved');
		setConfirmUnload(true);
	} else {
		jQuery('.js-wpv-layout-settings-extra-js-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved');
		jQuery('.js-screen-options').find('.toolset-alert').remove();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	}
});

jQuery(document).on('click', '.js-wpv-layout-settings-extra-js-update', function(e){
	e.preventDefault();
	view_settings['.js-wpv-layout-settings-extra-js'] = jQuery('.js-wpv-layout-settings-extra-js').val();
	var update_message = jQuery(this).data('success'),
		    unsaved_message = jQuery(this).data('unsaved'),
		    nonce = jQuery(this).data('nonce'),
		    spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertBefore(jQuery(this)).show(),
	data_view_id = jQuery('.js-post_ID').val();
	jQuery(this).parent().find('.toolset-alert-error').remove();
	jQuery('.js-wpv-layout-settings-extra-js-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary');
	var data = {
		action: 'wpv_update_layout_extra_js',
		id: data_view_id,
		value: view_settings['.js-wpv-layout-settings-extra-js'],
		wpnonce: nonce
	};
	jQuery.ajax({
		async:false,
		type:"POST",
		url:ajaxurl,
		data:data,
		success:function(response){
			if ( (typeof(response) !== 'undefined') && (response === data.id)) {
				jQuery('.js-wpv-layout-settings-extra-js-update').removeClass('js-wpv-section-unsaved');
				jQuery('.js-screen-options').find('.toolset-alert').remove();
				if (jQuery('.js-wpv-section-unsaved').length < 1) {
					setConfirmUnload(false);
				}
				jQuery('.js-wpv-layout-settings-extra-js-update').parent().wpvToolsetMessage({
					text:update_message,
					type:'success',
					inline:true,
					stay:false
				});
			} else {
				jQuery('.js-wpv-layout-settings-extra-js-update').parent().wpvToolsetMessage({
					text:unsaved_message,
					type:'error',
					inline:true,
					stay:true
				});
				console.log( "Error: AJAX returned ", response );
			}
		},
		error: function (ajaxContext) {
			jQuery('.js-wpv-layout-settings-extra-js-update').parent().wpvToolsetMessage({
				text:unsaved_message,
				type:'error',
				inline:true,
				stay:true
			});
			console.log( "Error: ", ajaxContext.responseText );
		},
		complete: function() {
			spinnerContainer.remove();
		}
	});
});

// Content update

codemirror_views_content.on('change', function(){
	jQuery('.js-wpv-content-update').parent().find('.toolset-alert-error').remove();
	if (codemirror_views_content_val != codemirror_views_content.getValue()) {
		jQuery('.js-wpv-content-update').prop('disabled', false).removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved');
		setConfirmUnload(true);
	} else {
		jQuery('.js-wpv-content-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved');
		jQuery('.js-screen-options').find('.toolset-alert').remove();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	}
});

jQuery(document).on('click', '.js-wpv-content-update', function(e){
	e.preventDefault();
	codemirror_views_content_val = codemirror_views_content.getValue();
	var update_message = jQuery(this).data('success'),
		    unsaved_message = jQuery(this).data('unsaved'),
		    nonce = jQuery(this).data('nonce'),
		    spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertBefore(jQuery(this)).show(),
	data_view_id = jQuery('.js-post_ID').val();
	jQuery(this).parent().find('.toolset-alert-error').remove();
	jQuery('.js-wpv-content-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary');
	var data = {
		action: 'wpv_update_content',
		id: data_view_id,
		content: codemirror_views_content_val,
		wpnonce: nonce
	};
	jQuery.ajax({
		async:false,
		type:"POST",
		url:ajaxurl,
		data:data,
		success:function(response){
			if ( (typeof(response) !== 'undefined') && (response === data.id)) {
				jQuery('.js-wpv-content-update').removeClass('js-wpv-section-unsaved');
				jQuery('.js-screen-options').find('.toolset-alert').remove();
				if (jQuery('.js-wpv-section-unsaved').length < 1) {
					setConfirmUnload(false);
				}
				jQuery('.js-wpv-content-update').parent().wpvToolsetMessage({
					text:update_message,
					type:'success',
					inline:true,
					stay:false
				});
			} else {
				jQuery('.js-wpv-content-update').parent().wpvToolsetMessage({
					text:unsaved_message,
					type:'error',
					inline:true,
					stay:true
				});
				console.log( "Error: AJAX returned ", response );
			}
		},
		error:function(ajaxContext){
			jQuery('.js-wpv-content-update').parent().wpvToolsetMessage({
				text:unsaved_message,
				type:'error',
				inline:true,
				stay:true
			});
			console.log( "Error: ", ajaxContext.responseText );
		},
		complete:function(){
			spinnerContainer.remove();
		}
	});
});

// Save all

jQuery('.js-wpv-view-save-all').click(function(e){
	jQuery(this).prop('disabled', true).removeClass('button-primary').addClass('button-secondary');
	e.preventDefault();
	var spinnerContainerAll = jQuery('<div class="spinner ajax-loader">').insertBefore(jQuery(this)).show(),
		update_message = jQuery(this).data('success'),
		unsaved_message = jQuery(this).data('unsaved');
	jQuery('.js-wpv-section-unsaved').each(function(){
		jQuery(this).click();
	});
	spinnerContainerAll.remove();
	jQuery(this).parent().wpvToolsetMessage({
		text:update_message,
		type:'success',
		inline:true,
		stay:false
	});
});

// Confirmation dialog - prevent users to navigate way if there is unsaved data

function setConfirmUnload(on) {
	if (on && jQuery('.js-wpv-section-unsaved').length > 0) {
		window.onbeforeunload = function(e) {
			jQuery('.js-wpv-section-unsaved').each(function(){
				var unsaved_message = jQuery(this).data('unsaved');
				if (jQuery(this).parent().find('.toolset-alert-error').length < 1) {
					jQuery(this).parent().wpvToolsetMessage({
						text:unsaved_message,
					     type:'error',
					     inline:true,
					     stay:true
					});
				}
			});
			var message = 'You have entered new data on this page.';
			// For IE and Firefox prior to version 4
			if (e) {
				e.returnValue = message;
			}
			// For Safari
			//	var e = event || window.event;
			return message;
		}
		jQuery('.js-wpv-view-save-all').prop('disabled', false).removeClass('button-secondary').addClass('button-primary');
	} else {
		window.onbeforeunload = null;
		jQuery('.js-wpv-view-save-all').prop('disabled', true).removeClass('button-primary').addClass('button-secondary');
	}
}

// Console log safe

if( !console )
{
	var console = {
		log:function(){}
	};
}