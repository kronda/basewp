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
view_settings['.js-wpv-query-type'] = jQuery('input:radio.js-wpv-query-type:checked').val();
view_settings['.js-wpv-query-post-items'] = [];
jQuery('.js-wpv-query-post-type:checked, .js-wpv-query-taxonomy-type:checked, .js-wpv-query-users-type:checked').each(function(){
	view_settings['.js-wpv-query-post-items'].push(jQuery(this).val());
});
view_settings['.js-wpv-query-options-post-type-dont'] = jQuery('.js-wpv-query-options-post-type-dont:checked').length;
view_settings['.js-wpv-query-options-taxonomy-hide-empty'] = jQuery('.js-wpv-query-options-taxonomy-hide-empty:checked').length;
view_settings['.js-wpv-query-options-taxonomy-non-empty-decendants'] = jQuery('.js-wpv-query-options-taxonomy-non-empty-decendants:checked').length;
view_settings['.js-wpv-query-options-taxonomy-pad-counts'] = jQuery('.js-wpv-query-options-taxonomy-pad-counts:checked').length;
view_settings['.js-wpv-query-options-users-show-current'] = jQuery('.js-wpv-query-options-users-show-current:checked').length;
//view_settings['.js-wpv-query-options-users-show-multisite'] = jQuery('.js-wpv-query-options-users-show-multisite:checked').val();
view_settings['.js-wpv-posts-orderby'] = jQuery('.js-wpv-posts-orderby').val();
view_settings['.js-wpv-posts-order'] = jQuery('.js-wpv-posts-order').val();
view_settings['.js-wpv-taxonomy-orderby'] = jQuery('.js-wpv-taxonomy-orderby').val();
view_settings['.js-wpv-taxonomy-order'] = jQuery('.js-wpv-taxonomy-order').val();
view_settings['.js-wpv-users-orderby'] = jQuery('.js-wpv-users-orderby').val();
view_settings['.js-wpv-users-order'] = jQuery('.js-wpv-users-order').val();
view_settings['.js-wpv-limit'] = jQuery('.js-wpv-limit').val();
view_settings['.js-wpv-offset'] = jQuery('.js-wpv-offset').val();
view_settings['.js-wpv-taxonomy-limit'] = jQuery('.js-wpv-taxonomy-limit').val();
view_settings['.js-wpv-taxonomy-offset'] = jQuery('.js-wpv-taxonomy-offset').val();
view_settings['.js-wpv-users-limit'] = jQuery('.js-wpv-users-limit').val();
view_settings['.js-wpv-users-offset'] = jQuery('.js-wpv-users-offset').val();
view_settings['.js-wpv-filter-dps'] = jQuery('.js-wpv-dps-settings input, .js-wpv-dps-settings select').serialize();

var codemirror_views_query = icl_editor.codemirror('wpv_filter_meta_html_content', true),
codemirror_views_query_val = codemirror_views_query.getValue(),
codemirror_views_query_css = icl_editor.codemirror('wpv_filter_meta_html_css', true, 'css'),
codemirror_views_query_css_val = codemirror_views_query_css.getValue(),
codemirror_views_query_js = icl_editor.codemirror('wpv_filter_meta_html_js', true, 'javascript'),
codemirror_views_query_js_val = codemirror_views_query_js.getValue(),
codemirror_views_layout = icl_editor.codemirror('wpv_layout_meta_html_content', true),
codemirror_views_layout_val = codemirror_views_layout.getValue(),
codemirror_views_layout_css = icl_editor.codemirror('wpv_layout_meta_html_css', true, 'css'),
codemirror_views_layout_css_val = codemirror_views_layout_css.getValue(),
codemirror_views_layout_js = icl_editor.codemirror('wpv_layout_meta_html_js', true, 'javascript'),
codemirror_views_layout_js_val = codemirror_views_layout_js.getValue(),
codemirror_views_content = icl_editor.codemirror('wpv_content', true),
codemirror_views_content_val = codemirror_views_content.getValue();

// Description update

jQuery(document).on('keyup input cut paste', '.js-wpv-description, .js-title, .js-wpv-slug', function(){
	jQuery('.js-wpv-title-description-update').parent().find('.toolset-alert').remove();
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
	jQuery(this).parent().find('.toolset-alert').remove();
	jQuery('.js-wpv-title-description-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary');
	var data = {
		action: 'wpv_update_title_description',
		id: data_view_id,
		description: jQuery('.js-wpv-description').val(),
		title: jQuery('.js-title').val(),
		slug: jQuery('.js-wpv-slug').val(),
		edit: 'View',
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

// Content selection update

jQuery(document).on('change', '.js-wpv-query-type', function(){
	jQuery('.js-wpv-query-type-update').parent().find('.toolset-alert-error').remove();
	if (view_settings['.js-wpv-query-type'] != jQuery('input:radio.js-wpv-query-type:checked').val()) {
		jQuery('.js-wpv-query-type-update').prop('disabled', false).removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved');
		setConfirmUnload(true);
	} else {
		var wpv_query_post_items = [];
		jQuery('.js-wpv-query-post-type:checked, .js-wpv-query-taxonomy-type:checked, .js-wpv-query-users-type:checked').each(function(){
			wpv_query_post_items.push(jQuery(this).val());
		});
		if (jQuery(view_settings['.js-wpv-query-post-items']).not(wpv_query_post_items).length == 0 && jQuery(wpv_query_post_items).not(view_settings['.js-wpv-query-post-items']).length == 0) {
			jQuery('.js-wpv-query-type-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved');
			jQuery('.js-screen-options').find('.toolset-alert').remove();
			if (jQuery('.js-wpv-section-unsaved').length < 1) {
				setConfirmUnload(false);
			}
		} else {
			jQuery('.js-wpv-query-type-update').prop('disabled', false).removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved');
			setConfirmUnload(true);
		}
	}
});
jQuery(document).on('change', '.js-wpv-query-post-type, .js-wpv-query-taxonomy-type, .js-wpv-query-users-type', function(){
	jQuery('.js-wpv-query-type-update').parent().find('.toolset-alert-error').remove();
	var wpv_query_post_items = [];
	jQuery('.js-wpv-query-post-type:checked, .js-wpv-query-taxonomy-type:checked, .js-wpv-query-users-type:checked').each(function(){
		wpv_query_post_items.push(jQuery(this).val());
	});
	if (jQuery(view_settings['.js-wpv-query-post-items']).not(wpv_query_post_items).length == 0 && jQuery(wpv_query_post_items).not(view_settings['.js-wpv-query-post-items']).length == 0) {
		if (view_settings['.js-wpv-query-type'] != jQuery('input:radio.js-wpv-query-type:checked').val()) {
			jQuery('.js-wpv-query-type-update').prop('disabled', false).removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved');
			setConfirmUnload(true);
		} else {
			jQuery('.js-wpv-query-type-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved');
			jQuery('.js-screen-options').find('.toolset-alert').remove();
			if (jQuery('.js-wpv-section-unsaved').length < 1) {
				setConfirmUnload(false);
			}
		}
	} else {
		jQuery('.js-wpv-query-type-update').prop('disabled', false).removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved');
		setConfirmUnload(true);
	}
});

jQuery(document).on('click', '.js-wpv-query-type-update', function(e){
	e.preventDefault();
	var update_message = jQuery(this).data('success'),
		unsaved_message = jQuery(this).data('unsaved'),
		nonce = jQuery(this).data('nonce'),
		wpv_query_post_items = [],
		wpv_query_taxonomy_items = [],
		wpv_query_users_items = [],
		spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertBefore(jQuery(this)).show(),
		data_view_id = jQuery('.js-post_ID').val();
	view_settings['.js-wpv-query-type'] = jQuery('input:radio.js-wpv-query-type:checked').val();
	view_settings['.js-wpv-query-post-items'] = [];
	jQuery('.js-wpv-query-post-type:checked').each(function(){
		wpv_query_post_items.push(jQuery(this).val());
		view_settings['.js-wpv-query-post-items'].push(jQuery(this).val());
	});
	jQuery('.js-wpv-query-taxonomy-type:checked').each(function(){
		wpv_query_taxonomy_items.push(jQuery(this).val());
		view_settings['.js-wpv-query-post-items'].push(jQuery(this).val());
	});
	jQuery('.js-wpv-query-users-type:checked').each(function(){
		wpv_query_users_items.push(jQuery(this).val());
		view_settings['.js-wpv-query-post-items'].push(jQuery(this).val());
	});
	jQuery(this).parent().find('.toolset-alert-error').remove();
	jQuery('.js-wpv-query-type-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary');
	var data = {
		action: 'wpv_update_query_type',
		id: data_view_id,
		query_type: view_settings['.js-wpv-query-type'],
		post_types: wpv_query_post_items,
		taxonomies: wpv_query_taxonomy_items,
		users: wpv_query_users_items,
		wpnonce: nonce
	};
	jQuery.ajax({
		async:false,
		type:"POST",
		url:ajaxurl,
		data:data,
		success:function(response){
			if ( (typeof(response) !== 'undefined') ) {
				decoded_response = jQuery.parseJSON(response);
				if ( decoded_response.success === data.id ) {
					jQuery('.js-wpv-query-type-update').removeClass('js-wpv-section-unsaved');
					jQuery('.js-screen-options').find('.toolset-alert').remove();
					jQuery('.js-filter-list').html(decoded_response.wpv_filter_update_filters_list);
					wpv_after_update_filters_list();
					if ( decoded_response.wpv_update_flatten_types_relationship_tree == 'NONE' ) {
						jQuery('.js-flatten-types-relation-tree').val('NONE');
					} else {
						jQuery('.js-flatten-types-relation-tree').val(decoded_response.wpv_update_flatten_types_relationship_tree);
					}
					jQuery('.js-wpv-dps-settings').html(decoded_response.wpv_dps_settings_structure);
					view_settings['.js-wpv-filter-dps'] = jQuery('.js-wpv-dps-settings input, .js-wpv-dps-settings select').serialize();
					jQuery('.js-wpv-query-type-update').parent().wpvToolsetMessage({
						text:update_message,
						type:'success',
						inline:true,
						stay:false
					});
					if (jQuery('.js-wpv-section-unsaved').length < 1) {
						setConfirmUnload(false);
					}
				}
			} else {
				jQuery('.js-wpv-query-type-update').parent().wpvToolsetMessage({
					text:unsaved_message,
					type:'error',
					inline:true,
					stay:true
				});
				console.log( "Error: AJAX returned ", response );
			}
		},
		error: function (ajaxContext) {
			jQuery('.js-wpv-query-type-update').parent().wpvToolsetMessage({
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

function wpv_after_update_filters_list() {
	wpv_filters_colapse();
	wpv_filters_exist();
	wpv_validate_hierarchical_post_types();
	wpv_validate_filter_taxonomy_parent();
	wpv_taxonomy_mode();
	wpv_taxonomy_relationship();
	wpv_custom_field_initialize_compare();
	wpv_custom_field_initialize_compare_mode();
	wpv_custom_field_initialize_relationship();
	wpv_usermeta_field_initialize_compare();
	wpv_usermeta_field_initialize_compare_mode();
	wpv_usermeta_field_initialize_relationship();
	view_settings['.js-wpv-filter-dps'] = jQuery('.js-wpv-dps-settings input, .js-wpv-dps-settings select').serialize();
	wpv_filter_taxonomy_selected = jQuery('.js-filter-list .js-filter-taxonomy input, .js-filter-list .js-filter-taxonomy select').serialize();
	wpv_filter_custom_field_selected = jQuery('.js-filter-list .js-filter-custom-field input, .js-filter-list .js-filter-custom-field select').serialize();
	wpv_filter_usermeta_field_selected = jQuery('.js-filter-list .js-filter-usermeta-field input, .js-filter-list .js-filter-usermeta-field select').serialize();
}

// Query options update
//.js-wpv-query-options-users-show-multisite,
jQuery(document).on('change', '.js-wpv-query-options-users-show-current, .js-wpv-query-options-post-type-dont, .js-wpv-query-options-taxonomy-hide-empty, .js-wpv-query-options-taxonomy-non-empty-decendants, .js-wpv-query-options-taxonomy-pad-counts', function(){
	jQuery('.js-wpv-query-options-update').parent().find('.toolset-alert-error').remove();
	var dont = 0,
		hide = 0,
		empty = 0,
		pad = 0;
		uhide = 0;
		//smulti = jQuery('.js-wpv-query-options-users-show-multisite:checked').val();
	if (jQuery('.js-wpv-query-options-post-type-dont').attr('checked')) {
		dont = 1;
	}
	if (jQuery('.js-wpv-query-options-taxonomy-hide-empty').attr('checked')) {
		hide = 1;
	}
	if (jQuery('.js-wpv-query-options-taxonomy-non-empty-decendants').attr('checked')) {
		empty = 1;
	}
	if (jQuery('.js-wpv-query-options-taxonomy-pad-counts').attr('checked')) {
		pad = 1;
	}
	if (jQuery('.js-wpv-query-options-users-show-current').attr('checked')) {
		uhide = 1;
	}
	//|| smulti != view_settings['.js-wpv-query-options-users-show-multisite']
	if (dont != view_settings['.js-wpv-query-options-post-type-dont']
		|| hide != view_settings['.js-wpv-query-options-taxonomy-hide-empty']
		|| empty != view_settings['.js-wpv-query-options-taxonomy-non-empty-decendants']
		|| pad != view_settings['.js-wpv-query-options-taxonomy-pad-counts']
		|| uhide != view_settings['.js-wpv-query-options-users-show-current']
		
	) {
		jQuery('.js-wpv-query-options-update').prop('disabled', false).removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved');
		setConfirmUnload(true);
	} else {
		jQuery('.js-wpv-query-options-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved');
		jQuery('.js-screen-options').find('.toolset-alert').remove();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	}
});

jQuery(document).on('click', '.js-wpv-query-options-update', function(e){
	e.preventDefault();
	view_settings['.js-wpv-query-options-post-type-dont'] = jQuery('.js-wpv-query-options-post-type-dont:checked').length;
	view_settings['.js-wpv-query-options-taxonomy-hide-empty'] = jQuery('.js-wpv-query-options-taxonomy-hide-empty:checked').length;
	view_settings['.js-wpv-query-options-taxonomy-non-empty-decendants'] = jQuery('.js-wpv-query-options-taxonomy-non-empty-decendants:checked').length;
	view_settings['.js-wpv-query-options-taxonomy-pad-counts'] = jQuery('.js-wpv-query-options-taxonomy-pad-counts:checked').length;
	view_settings['.js-wpv-query-options-users-show-current'] = jQuery('.js-wpv-query-options-users-show-current:checked').length;
	//view_settings['.js-wpv-query-options-users-show-multisite'] = jQuery('.js-wpv-query-options-users-show-multisite:checked').val();
	
	var update_message = jQuery(this).data('success'),
		unsaved_message = jQuery(this).data('unsaved'),
		nonce = jQuery(this).data('nonce'),
		spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertBefore(jQuery(this)).show(),
		data_view_id = jQuery('.js-post_ID').val();
	jQuery(this).parent().find('.toolset-alert-error').remove();
	jQuery('.js-wpv-query-options-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary');
	var data = {
		action: 'wpv_update_query_options',
		id: data_view_id,
		dont: view_settings['.js-wpv-query-options-post-type-dont'],
		hide: view_settings['.js-wpv-query-options-taxonomy-hide-empty'],
		empty: view_settings['.js-wpv-query-options-taxonomy-non-empty-decendants'],
		pad: view_settings['.js-wpv-query-options-taxonomy-pad-counts'],
		uhide : view_settings['.js-wpv-query-options-users-show-current'],
		wpnonce: nonce
	};
	//smulti : view_settings['.js-wpv-query-options-users-show-multisite'],
	jQuery.ajax({
		async:false,
		type:"POST",
		url:ajaxurl,
		data:data,
		success:function(response){
			if ( (typeof(response) !== 'undefined') && (response === data.id)) {
				jQuery('.js-wpv-query-options-update').removeClass('js-wpv-section-unsaved');
				jQuery('.js-screen-options').find('.toolset-alert').remove();
				if (jQuery('.js-wpv-section-unsaved').length < 1) {
					setConfirmUnload(false);
				}
				jQuery('.js-wpv-query-options-update').parent().wpvToolsetMessage({
					text:update_message,
					type:'success',
					inline:true,
					stay:false
				});
			} else {
				jQuery('.js-wpv-query-options-update').parent().wpvToolsetMessage({
					text:unsaved_message,
					type:'error',
					inline:true,
					stay:true
				});
				console.log( "Error: AJAX returned ", response );
			}
		},
		error: function (ajaxContext) {
			jQuery('.js-wpv-query-options-update').parent().wpvToolsetMessage({
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


jQuery(document).on('change', '.js-wpv-posts-orderby', function(){
	jQuery('.js-wpv-settings-posts-order .toolset-alert, .js-wpv-settings-pagination .js-pagination-settings-form .toolset-alert').remove();
	if (jQuery('.js-wpv-posts-orderby').val() == 'rand'){
		jQuery('.js-wpv-settings-posts-order, .js-wpv-settings-pagination .js-pagination-settings-form').wpvToolsetMessage({
			text: jQuery('.js-wpv-posts-orderby').data('rand'),
			stay: true,
			close: false,
			type: ''
		});
	}	
});
// Sorting update
jQuery(document).on('change', '.js-wpv-posts-orderby, .js-wpv-posts-order, .js-wpv-taxonomy-orderby, .js-wpv-taxonomy-order, .js-wpv-users-orderby, .js-wpv-users-order', function(){
	jQuery('.js-wpv-ordering-update').parent().find('.toolset-alert-error').remove();
	if (jQuery('.js-wpv-posts-orderby').val() != view_settings['.js-wpv-posts-orderby']
		|| jQuery('.js-wpv-posts-order').val() != view_settings['.js-wpv-posts-order']
		|| jQuery('.js-wpv-taxonomy-orderby').val() != view_settings['.js-wpv-taxonomy-orderby']
		|| jQuery('.js-wpv-taxonomy-order').val() != view_settings['.js-wpv-taxonomy-order']
		| jQuery('.js-wpv-users-orderby').val() != view_settings['.js-wpv-users-orderby']
		|| jQuery('.js-wpv-users-order').val() != view_settings['.js-wpv-users-order']
	) {
		jQuery('.js-wpv-ordering-update').prop('disabled', false).removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved');
		setConfirmUnload(true);
	} else {
		jQuery('.js-wpv-ordering-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved');
		jQuery('.js-screen-options').find('.toolset-alert').remove();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	}
	
	
});

jQuery(document).on('click', '.js-wpv-ordering-update', function(e){
	e.preventDefault();
	view_settings['.js-wpv-posts-orderby'] = jQuery('.js-wpv-posts-orderby').val();
	view_settings['.js-wpv-posts-order'] = jQuery('.js-wpv-posts-order').val();
	view_settings['.js-wpv-taxonomy-orderby'] = jQuery('.js-wpv-taxonomy-orderby').val();
	view_settings['.js-wpv-taxonomy-order'] = jQuery('.js-wpv-taxonomy-order').val();
	view_settings['.js-wpv-users-orderby'] = jQuery('.js-wpv-users-orderby').val();
	view_settings['.js-wpv-users-order'] = jQuery('.js-wpv-users-order').val();
	var update_message = jQuery(this).data('success'),
		unsaved_message = jQuery(this).data('unsaved'),
		nonce = jQuery(this).data('nonce'),
		spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertBefore(jQuery(this)).show(),
		data_view_id = jQuery('.js-post_ID').val();
	jQuery(this).parent().find('.toolset-alert-error').remove();
	jQuery('.js-wpv-ordering-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary');
	var data = {
		action: 'wpv_update_sorting',
		id: data_view_id,
		orderby: view_settings['.js-wpv-posts-orderby'],
		order: view_settings['.js-wpv-posts-order'],
		taxonomy_orderby: view_settings['.js-wpv-taxonomy-orderby'],
		taxonomy_order: view_settings['.js-wpv-taxonomy-order'],
		users_orderby: view_settings['.js-wpv-users-orderby'],
		users_order: view_settings['.js-wpv-users-order'],
		wpnonce: nonce
	};
	jQuery.ajax({
		async:false,
		type:"POST",
		url:ajaxurl,
		data:data,
		success:function(response){
			if ( (typeof(response) !== 'undefined') && (response === data.id)) {
				jQuery('.js-wpv-ordering-update').removeClass('js-wpv-section-unsaved');
				jQuery('.js-screen-options').find('.toolset-alert').remove();
				if (jQuery('.js-wpv-section-unsaved').length < 1) {
					setConfirmUnload(false);
				}
				jQuery('.js-wpv-ordering-update').parent().wpvToolsetMessage({
					text:update_message,
					type:'success',
					inline:true,
					stay:false
				});
			} else {
				jQuery('.js-wpv-ordering-update').parent().wpvToolsetMessage({
					text:unsaved_message,
					type:'error',
					inline:true,
					stay:true
				});
				console.log( "Error: AJAX returned ", response );
			}
		},
		error: function (ajaxContext) {
			jQuery('.js-wpv-ordering-update').parent().wpvToolsetMessage({
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

// Limit & Offset update

jQuery(document).on('change', '.js-wpv-limit, .js-wpv-offset, .js-wpv-taxonomy-limit, .js-wpv-taxonomy-offset, .js-wpv-users-limit, .js-wpv-users-offset', function(){
	jQuery('.js-wpv-limit-offset-update').parent().find('.toolset-alert-error').remove();
	if (jQuery('.js-wpv-limit').val() != view_settings['.js-wpv-limit']
		|| jQuery('.js-wpv-offset').val() != view_settings['.js-wpv-offset']
		|| jQuery('.js-wpv-taxonomy-limit').val() != view_settings['.js-wpv-taxonomy-limit']
		|| jQuery('.js-wpv-taxonomy-offset').val() != view_settings['.js-wpv-taxonomy-offset']
		|| jQuery('.js-wpv-users-limit').val() != view_settings['.js-wpv-users-limit']
		|| jQuery('.js-wpv-users-offset').val() != view_settings['.js-wpv-users-offset']
	) {
		jQuery('.js-wpv-limit-offset-update').prop('disabled', false).removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved');
		setConfirmUnload(true);
	} else {
		jQuery('.js-wpv-limit-offset-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved');
		jQuery('.js-screen-options').find('.toolset-alert').remove();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	}
});

jQuery(document).on('click', '.js-wpv-limit-offset-update', function(e){
	e.preventDefault();
	view_settings['.js-wpv-limit'] = jQuery('.js-wpv-limit').val();
	view_settings['.js-wpv-offset'] = jQuery('.js-wpv-offset').val();
	view_settings['.js-wpv-taxonomy-limit'] = jQuery('.js-wpv-taxonomy-limit').val();
	view_settings['.js-wpv-taxonomy-offset'] = jQuery('.js-wpv-taxonomy-offset').val();
	view_settings['.js-wpv-users-limit'] = jQuery('.js-wpv-users-limit').val();
	view_settings['.js-wpv-users-offset'] = jQuery('.js-wpv-users-offset').val();
	var update_message = jQuery(this).data('success'),
		unsaved_message = jQuery(this).data('unsaved'),
		nonce = jQuery(this).data('nonce'),
		spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertBefore(jQuery(this)).show(),
		data_view_id = jQuery('.js-post_ID').val();
	jQuery(this).parent().find('.toolset-alert-error').remove();
	jQuery('.js-wpv-limit-offset-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary');
	var data = {
		action: 'wpv_update_limit_offset',
		id: data_view_id,
		limit: view_settings['.js-wpv-limit'],
		offset: view_settings['.js-wpv-offset'],
		taxonomy_limit: view_settings['.js-wpv-taxonomy-limit'],
		taxonomy_offset: view_settings['.js-wpv-taxonomy-offset'],
		users_limit: view_settings['.js-wpv-users-limit'],
		users_offset: view_settings['.js-wpv-users-offset'],
		wpnonce: nonce
	};
	jQuery.ajax({
		async:false,
		type:"POST",
		url:ajaxurl,
		data:data,
		success:function(response){
			if ( (typeof(response) !== 'undefined') && (response === data.id)) {
				jQuery('.js-wpv-limit-offset-update').removeClass('js-wpv-section-unsaved');
				jQuery('.js-screen-options').find('.toolset-alert').remove();
				if (jQuery('.js-wpv-section-unsaved').length < 1) {
					setConfirmUnload(false);
				}
				jQuery('.js-wpv-limit-offset-update').parent().wpvToolsetMessage({
					text:update_message,
					type:'success',
					inline:true,
					stay:false
				});
			} else {
				jQuery('.js-wpv-limit-offset-update').parent().wpvToolsetMessage({
					text:unsaved_message,
					type:'error',
					inline:true,
					stay:true
				});
				console.log( "Error: AJAX returned ", response );
			}
		},
		error: function (ajaxContext) {
			jQuery('.js-wpv-limit-offset-update').parent().wpvToolsetMessage({
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

// Pagination update

view_settings['.js-wpv-pagination'] = jQuery('.js-pagination-settings-form').serialize();

jQuery(document).on('change keyup input cut paste', '.js-pagination-settings-form input, .js-pagination-settings-form select', function(){
	jQuery('.js-wpv-pagination-update').parent().find('.toolset-alert-error').remove();
	var newchecked = jQuery('.js-pagination-settings-form').serialize();
	if (view_settings['.js-wpv-pagination'] != newchecked) {
		jQuery('.js-wpv-pagination-update').prop('disabled', false).removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved');
		setConfirmUnload(true);
	} else {
		jQuery('.js-wpv-pagination-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved');
		jQuery('.js-screen-options').find('.toolset-alert').remove();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	}
});

jQuery(document).on('click', '.js-wpv-pagination-update', function(e){
	e.preventDefault();
	view_settings['.js-wpv-pagination'] = jQuery('.js-pagination-settings-form').serialize();
	var update_message = jQuery(this).data('success'),
		unsaved_message = jQuery(this).data('unsaved'),
		nonce = jQuery(this).data('nonce'),
		spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertBefore(jQuery(this)).show(),
		data_view_id = jQuery('.js-post_ID').val(),
		settings = jQuery('.js-pagination-settings-form').serialize(),
		show_hint = jQuery(this).data('showhint');
	jQuery(this).parent().find('.toolset-alert-error').remove();
	jQuery('.js-wpv-pagination-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary');
	var data = {
		action: 'wpv_update_pagination',
		id: data_view_id,
		settings : settings,
		wpnonce: nonce
	};
	jQuery.ajax({
		async:false,
		type:"POST",
		url:ajaxurl,
		data:data,
		success:function(response){
			if ( (typeof(response) !== 'undefined') && (response === data.id)) {
				jQuery('.js-wpv-pagination-update').removeClass('js-wpv-section-unsaved');
				jQuery('.js-screen-options').find('.toolset-alert').remove();
				if (jQuery('.js-wpv-section-unsaved').length < 1) {
					setConfirmUnload(false);
				}
				jQuery('.js-wpv-pagination-update').parent().wpvToolsetMessage({
					text:update_message,
					type:'success',
					inline:true,
					stay:false
				});
				wpv_pagination_button_state();
				wpv_pagination_insert_hint();
			} else {
				jQuery('.js-wpv-pagination-update').parent().wpvToolsetMessage({
					text:unsaved_message,
					type:'error',
					inline:true,
					stay:true
				});
				console.log( "Error: AJAX returned ", response );
			}
		},
		error: function (ajaxContext) {
			jQuery('.js-wpv-pagination-update').parent().wpvToolsetMessage({
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

// Filter Extra update

codemirror_views_query.on('change', function(){
	jQuery('.js-wpv-filter-extra-update').parent().find('.toolset-alert-error').remove();
	if (codemirror_views_query_val != codemirror_views_query.getValue()
		|| codemirror_views_query_css_val != codemirror_views_query_css.getValue()
		|| codemirror_views_query_js_val != codemirror_views_query_js.getValue()
	) {
		jQuery('.js-wpv-filter-extra-update').prop('disabled', false).removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved');
		setConfirmUnload(true);
	} else {
		jQuery('.js-wpv-filter-extra-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved');
		jQuery('.js-screen-options').find('.toolset-alert').remove();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	}
});
codemirror_views_query_css.on('change', function(){
	jQuery('.js-wpv-filter-extra-update').parent().find('.toolset-alert-error').remove();
	if (codemirror_views_query_val != codemirror_views_query.getValue()
		|| codemirror_views_query_css_val != codemirror_views_query_css.getValue()
		|| codemirror_views_query_js_val != codemirror_views_query_js.getValue()
	) {
		jQuery('.js-wpv-filter-extra-update').prop('disabled', false).removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved');
		setConfirmUnload(true);
	} else {
		jQuery('.js-wpv-filter-extra-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved');
		jQuery('.js-screen-options').find('.toolset-alert').remove();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	}
});
codemirror_views_query_js.on('change', function(){
	jQuery('.js-wpv-filter-extra-update').parent().find('.toolset-alert-error').remove();
	if (codemirror_views_query_val != codemirror_views_query.getValue()
		|| codemirror_views_query_css_val != codemirror_views_query_css.getValue()
		|| codemirror_views_query_js_val != codemirror_views_query_js.getValue()
	) {
		jQuery('.js-wpv-filter-extra-update').prop('disabled', false).removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved');
		setConfirmUnload(true);
	} else {
		jQuery('.js-wpv-filter-extra-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved');
		jQuery('.js-screen-options').find('.toolset-alert').remove();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	}
});

jQuery(document).on('click', '.js-wpv-filter-extra-update', function(e) {
	e.preventDefault();
	codemirror_views_query_val = codemirror_views_query.getValue();
	codemirror_views_query_css_val = codemirror_views_query_css.getValue();
	codemirror_views_query_js_val = codemirror_views_query_js.getValue();
	var update_message = jQuery(this).data('success'),
		unsaved_message = jQuery(this).data('unsaved'),
		nonce = jQuery(this).data('nonce'),
		spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertBefore(jQuery(this)).show(),
		data_view_id = jQuery('.js-post_ID').val();
	jQuery(this).parent().find('.toolset-alert-error').remove();
	jQuery('.js-wpv-filter-extra-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary');
	var data = {
		action: 'wpv_update_filter_extra',
		id: data_view_id,
		query_val: codemirror_views_query_val,
		query_css_val: codemirror_views_query_css_val,
		query_js_val: codemirror_views_query_js_val,
		wpnonce: nonce
	};
	jQuery.ajax({
		async:false,
		type:"POST",
		url:ajaxurl,
		data:data,
		success:function(response){
			if ( (typeof(response) !== 'undefined') ) {
				decoded_response = jQuery.parseJSON(response);
				if ( decoded_response.success === data.id ) {
					jQuery('.js-wpv-filter-extra-update').removeClass('js-wpv-section-unsaved');
					jQuery('.js-wpv-dps-settings').html(decoded_response.wpv_dps_settings_structure);
					view_settings['.js-wpv-filter-dps'] = jQuery('.js-wpv-dps-settings input, .js-wpv-dps-settings select').serialize();
					jQuery('.js-screen-options').find('.toolset-alert').remove();
					if (jQuery('.js-wpv-section-unsaved').length < 1) {
						setConfirmUnload(false);
					}
					jQuery('.js-wpv-filter-extra-update').parent().wpvToolsetMessage({
						text:update_message,
						type:'success',
						inline:true,
						stay:false
					});
				}
			} else {
				jQuery('.js-wpv-filter-extra-update').parent().wpvToolsetMessage({
					text:unsaved_message,
					type:'error',
					inline:true,
					stay:true
				});
				console.log( "Error: AJAX returned ", response );
			}
		},
		error:function(ajaxContext){
			jQuery('.js-wpv-filter-extra-update').parent().wpvToolsetMessage({
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

// Parametric Search update

jQuery(document).on('change keyup input cut paste', '.js-wpv-dps-settings input, .js-wpv-dps-settings select', function(){
	wpv_dps_change_check();
});

function wpv_dps_change_check() {
	jQuery('.js-wpv-filter-dps-update').parent().find('.toolset-alert-error').remove();
	var newdps = jQuery('.js-wpv-dps-settings input, .js-wpv-dps-settings select').serialize();
	if (view_settings['.js-wpv-filter-dps'] != newdps) {
		jQuery('.js-wpv-filter-dps-update').prop('disabled', false).removeClass('button-secondary').addClass('button-primary').addClass('js-wpv-section-unsaved');
		setConfirmUnload(true);
	} else {
		jQuery('.js-wpv-filter-dps-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved');
	//	jQuery('.js-screen-options').find('.toolset-alert').remove();
		if (jQuery('.js-wpv-section-unsaved').length < 1) {
			setConfirmUnload(false);
		}
	}
}

jQuery(document).on('click', '.js-wpv-filter-dps-update', function(){
	view_settings['.js-wpv-filter-dps'] = jQuery('.js-wpv-dps-settings input, .js-wpv-dps-settings select').serialize();
	var nonce = jQuery(this).data('nonce'),
	data_view_id = jQuery('.js-post_ID').val(),
	spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertBefore(jQuery(this)).show(),
	update_message = jQuery(this).data('success'),
	unsaved_message = jQuery(this).data('unsaved');
	jQuery(this).parent().find('.toolset-alert-error').remove();
	jQuery('.js-wpv-filter-dps-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary');
	var params = {
		action: 'wpv_filter_update_dps_settings',
		id: data_view_id,
		dpsdata: view_settings['.js-wpv-filter-dps'],
		nonce: nonce
	}
	jQuery.ajax({
		async:false,
		type:"POST",
		url:ajaxurl,
		data:params,
		success:function(response){
			if ( (typeof(response) !== 'undefined') && (response === params.id)) {
				jQuery('.js-wpv-filter-dps-update').removeClass('js-wpv-section-unsaved');
				if (jQuery('.js-wpv-section-unsaved').length < 1) {
					setConfirmUnload(false);
				}
				jQuery('.js-wpv-filter-dps-update').parent().wpvToolsetMessage({
					text:update_message,
					type:'success',
					inline:true,
					stay:false
				});
			} else {
				jQuery('.js-wpv-filter-dps-update').parent().wpvToolsetMessage({
					text:unsaved_message,
					type:'error',
					inline:true,
					stay:true
				});
				console.log( "Error: AJAX returned ", response );
			}
		},
		error:function(ajaxContext){
			jQuery('.js-wpv-filter-dps-update').parent().wpvToolsetMessage({
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

// NOTE this is not used anymore, maybe needed at some point in the future NOTE well this is used on every update/delete filter call, so keep it

function wpv_update_parametric_search_section() {
	var data_view_id = jQuery('.js-post_ID').val(),
	nonce = jQuery('.js-post_ID').data('nonce'),
	data = {
		action: 'wpv_update_parametric_search_section',
		id: data_view_id,
		nonce: nonce
	};
	jQuery.ajax({
		async:false,
		type:"POST",
		url:ajaxurl,
		data:data,
		success:function(response){
			if ( (typeof(response) !== 'undefined') ) {
					jQuery('.js-wpv-dps-settings').html(response);
					if (jQuery('.js-wpv-section-unsaved').length < 1) {
						view_settings['.js-wpv-filter-dps'] = jQuery('.js-wpv-dps-settings input, .js-wpv-dps-settings select').serialize();
						setConfirmUnload(false);
					}
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
}

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

// Confirmation dialog - prevent users to navigate away if there is unsaved data

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