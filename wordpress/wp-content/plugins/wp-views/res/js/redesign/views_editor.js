jQuery(document).ready(function($){

	// Screen options fix

	wpv_screen_options();

	wpv_show_hide_metasections_init();

	// Help boxes initialization

	wpv_show_hide_purpose_help_init();

	// description toggle

	$('.js-wpv-description-toggle').on('click', function() {
		$(this).hide();
		$('.js-wpv-description-container').fadeIn('fast');
		$('#wpv-description').focus();
	});


	// title placeholder
	$('.js-title').each(function(){
		if ('' === $(this).val()) {
			$(this).parent().find('.js-title-reader').removeClass('screen-reader-text');
		}
		$(this).focus(function(){
			$(this).parent().find('.js-title-reader').addClass('screen-reader-text');
		});
		$(this).blur(function(){
			if ('' === $(this).val()) {
				$(this).parent().find('.js-title-reader').removeClass('screen-reader-text');
			}
		});
	});


	// query option - on change show or hide relevant parts of some sections and toolbar buttons

	$('input:radio.js-wpv-query-type').change(function(e){
		view_query_type_options();
	});

	//editor buttons
	$('.js-code-editor-button').click(function(e){
		e.preventDefault();

		var $this = $(this);
		var state = $(this).data('state');
		var $editor = $('.js-code-editor').filter(function() {
			return $this.data('target') ===  $(this).data('name');
		});
		
		$this.removeClass('code-editor-textarea-full code-editor-textarea-empty');

		$editor.toggleClass('closed');

		if ( $this.data('target') == 'filter-css-editor' || $this.data('target') == 'filter-js-editor'
			|| $this.data('target') == 'layout-js-editor' || $this.data('target') == 'layout-css-editor' ){
			var z_el = $this.data('target');
			var $elem = $this.detach();
			if (state == 'closed') {
				$editor.find('.js-code-editor-toolbar ul').append('<li class="wpv-'+ z_el +'-button-moved close-editor"></li>');
				$('.wpv-'+ z_el +'-button-moved').append($elem);
			}
			else{
				$('.js-wpv-'+ z_el +'-old-place').append($elem);
				$editor.find('.js-code-editor-toolbar ul li.wpv-'+ z_el +'-button-moved').remove();
				if ( wpv_extra_textarea_toggle_flag(z_el) ) {
					$this.addClass('code-editor-textarea-full');
				} else {
					$this.addClass('code-editor-textarea-empty');
				}
			}
		}


		if (state == 'closed') {
			$(this).data('state','opened');
			$(this).text($(this).data('opened'));
			$(this).prev('input').val('on');
		}
		else if (state == 'opened') {
			$(this).data('state','closed');
			$(this).text($(this).data('closed'));
			$(this).prev('input').val('off');
		}

		return false;
	});

	if ('on' == $('#wpv_filter_meta_html_state').val()) {
		$('.filter-html-editor').removeClass('closed');
		$('.filter-html-editor-button')
			.data('state','opened')
			.text($('.filter-html-editor-button').data('opened'));
	}
	if ('' != $('#wpv_filter_meta_html_css').val() && 'on' == $('#wpv_filter_meta_html_extra_css_state').val()) {
		$('.filter-css-editor').removeClass('closed');
		$('.filter-css-editor-button')
			.data('state','opened')
			.text($('.filter-css-editor-button').data('opened'));
	}
	if ('' != $('#wpv_filter_meta_html_js').val() && 'on' == $('#wpv_filter_meta_html_extra_js_state').val()) {
		$('.filter-js-editor').removeClass('closed');
		$('.filter-js-editor-button')
			.data('state','opened')
			.text($('.filter-js-editor-button').data('opened'));
	}
	if ('on' == $('#wpv_layout_meta_html_state').val()) {
		$('.layout-html-editor').removeClass('closed');
		$('.layout-html-editor-button')
			.data('state','opened')
			.text($('.layout-html-editor-button').data('opened'));
	}
	if ('' != $('#wpv_layout_meta_html_css').val() && 'on' == $('#wpv_layout_meta_html_extra_css_state').val()) {
		$('.layout-css-editor').removeClass('closed');
		$('.layout-css-editor-button')
			.data('state','opened')
			.text($('.layout-css-editor-button').data('opened'));
	}
	if ('' != $('#wpv_layout_meta_html_js').val() && 'on' == $('#wpv_layout_meta_html_extra_js_state').val()) {
		$('.layout-js-editor').removeClass('closed');
		$('.layout-js-editor-button')
			.data('state','opened')
			.text($('.layout-js-editor-button').data('opened'));
	}

	// wp-pointers

	$('.js-display-tooltip').click(function(){
		var $thiz = $(this);

		// hide this pointer if other pointer is opened.
		$('.wp-pointer').fadeOut(100);

		$(this).pointer({
			content: '<h3>'+$thiz.data('header')+'</h3><p>'+$thiz.data('content')+'</p>',
			position: {
				edge: 'left',
				align: 'center',
				offset: '15 0'
			}
		}).pointer('open');
	});

	// CodeMirror
/*	var codemirror_views_query = icl_editor.codemirror('wpv_filter_meta_html_content', true);
	var codemirror_views_query_css = icl_editor.codemirror('wpv_filter_meta_html_css', true);
	var codemirror_views_query_js = icl_editor.codemirror('wpv_filter_meta_html_js', true);
	var codemirror_views_layout = icl_editor.codemirror('wpv_layout_meta_html_content', true);
	var codemirror_views_layout_css = icl_editor.codemirror('wpv_layout_meta_html_css', true);
	var codemirror_views_layout_js = icl_editor.codemirror('wpv_layout_meta_html_js', true);
	var codemirror_views_content = icl_editor.codemirror('wpv_content', true);
*/
	if( typeof cred_cred != 'undefined'){
		cred_cred.posts();
	}
});

function wpv_extra_textarea_toggle_flag(element) {
	var full = false;
	if ( element == 'filter-css-editor' ) {
		full = ( codemirror_views_query_css.getValue() != '' );
	} else if ( element == 'filter-js-editor' ) {
		full = ( codemirror_views_query_js.getValue() != '' );
	} else if ( element == 'layout-css-editor' ) {
		full = ( codemirror_views_layout_css.getValue() != '' );
	} else if ( element == 'layout-js-editor' ) {
		full = ( codemirror_views_layout_js.getValue() != '' );
	}
	return full;
}

// Change status

jQuery(document).on('click', '.js-wpv-change-view-status', function(e){
	e.preventDefault();
	var newstatus = jQuery(this).data('statusto'),
		    spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(jQuery(this)).show(),
		    thiz = jQuery(this),
		    update_message = jQuery(this).data('success'),
		    error_message = jQuery(this).data('unsaved'),
		    redirect_url = jQuery(this).data('redirect');
		    thiz.prop('disabled', true).removeClass('button-primary').addClass('button-secondary');
		    if (newstatus == 'trash') {
			    message_where = jQuery('.js-wpv-slug-container');
		    } else {
			    message_where = thiz.parent();
		    }
		    var data = {
			    action: 'wpv_view_change_status',
			    id: jQuery('.js-post_ID').val(),
			    newstatus: newstatus,
			    wpnonce : jQuery(this).data('nonce')
		    };
		    jQuery.ajax({
			    async:false,
		  type:"POST",
		  url:ajaxurl,
		  data:data,
		  success:function(response){
			  if ( (typeof(response) !== 'undefined') && (response == data.id)) {
				  if (newstatus == 'trash') {
					  jQuery(location).attr('href',redirect_url);
				  }
			  } else {
				  message_where.wpvToolsetMessage({
					  text:error_message,
				      type:'error',
				      inline:true,
				      stay:true
				  });
				  console.log( "Error: AJAX returned ", response );
			  }
		  },
		  error: function (ajaxContext) {
			  thiz.prop('disabled', false);
			  spinnerContainer.remove();
			  message_where.wpvToolsetMessage({
				  text:error_message,
				  type:'error',
				  inline:true,
				  stay:true
			  });
			  console.log( "Error: ", ajaxContext.responseText );
		  },
		  complete: function() {
			  
		  }
		    });
});

function view_query_type_options(){
	var query_type_selected = jQuery('input:radio.js-wpv-query-type:checked').val();
	if ('posts' == query_type_selected) {
		jQuery('.wpv-settings-query-type-posts').fadeIn('fast');
		jQuery('.wpv-settings-query-type-taxonomy').hide();
		jQuery('.wpv-settings-query-type-users').hide();
		jQuery('.wpv-vicon-for-posts').removeClass('hidden');
		jQuery('.wpv-vicon-for-users').addClass('hidden');
		jQuery('.wpv-vicon-for-taxonomy').addClass('hidden');
	}
	if ('taxonomy' == query_type_selected) {
		jQuery('.wpv-settings-query-type-posts').hide();
		jQuery('.wpv-settings-query-type-users').hide();
		jQuery('.wpv-settings-query-type-taxonomy').fadeIn('fast');
		jQuery('.wpv-vicon-for-taxonomy').removeClass('hidden');
		jQuery('.wpv-vicon-for-posts').addClass('hidden');
		jQuery('.wpv-vicon-for-users').addClass('hidden');
	}
	if ('users' == query_type_selected) {
		jQuery('.wpv-settings-query-type-users').fadeIn('fast');
		jQuery('.wpv-settings-query-type-posts').hide();
		jQuery('.wpv-settings-query-type-taxonomy').hide();
		jQuery('.wpv-vicon-for-taxonomy').addClass('hidden');
		jQuery('.wpv-vicon-for-posts').addClass('hidden');
		jQuery('.wpv-vicon-for-users').removeClass('hidden');	
	}
}

/*
 * Screen options
 */

// Fix when opening Help section
// This is caused because we are adding our Screen Options in an artificial way
// so when opening the Help tab it displays all elements inside the tab container

jQuery(document).on('click', '#screen-meta-links #contextual-help-link', function() {
	jQuery('.metabox-prefs .js-wpv-show-hide-container').hide();
});

// Screen options - move to the right place in DOM

function wpv_screen_options() {
	var views_screen_options = jQuery('.js-screen-meta-links-dup > div');
	var views_screen_options_container = jQuery('.js-screen-meta-dup > div');
	jQuery('#screen-meta-links').append(views_screen_options);
	jQuery('#screen-meta').append(views_screen_options_container);
}

// Screen options - hide metasections on page load if needed

function wpv_show_hide_metasections_init() {
	jQuery('.js-wpv-show-hide-section').each(function(){
		var metasection = jQuery(this).data('metasection');
		if (
			0 == jQuery(this).find('.js-wpv-show-hide:checked').length &&
			jQuery('.' + metasection).find('.wpv-setting-container').length == jQuery(this).find('.js-wpv-show-hide').length
		) {
			jQuery('.' + metasection).hide();
		}
	});
}

// Screen options - manage sections checkboxes click
// TODO make saving automatic

jQuery(document).on('change', '.js-wpv-show-hide', function(){
	wpv_show_hide_section_change(jQuery(this));
});

// Based on the screen option checkbox, show or hide the section

function wpv_show_hide_section_change(checkbox) {
	checkbox.parents('.js-wpv-show-hide-container').find('.toolset-alert').remove();
	var section = checkbox.data('section');
	var state = checkbox.attr('checked');
	var input_value = checkbox.parents('.js-wpv-screen-pref').find('.js-wpv-show-hide-value');
	var section_changed = jQuery('.js-wpv-show-hide-container').data('unclickable');
	if ('checked' == state) {
		var metasection = checkbox.parents('.js-wpv-show-hide-section').data('metasection');
		jQuery('.' + metasection).show();
		jQuery('.js-wpv-settings-' + section).fadeIn('fast');
		if ( 'filter-extra' == section ) {
			jQuery('.js-wpv-settings-container-dps-filter').fadeIn('fast');
		}
		input_value.val('on');
		if ('filter-extra' == section) {
			codemirror_views_query.refresh();
			codemirror_views_query_css.refresh();
			codemirror_views_query_js.refresh();
		}
		if ('content' == section) {
			codemirror_views_content.refresh();
		}
		if ('layout-extra' == section) {
			codemirror_views_layout.refresh();
			codemirror_views_layout_css.refresh();
			codemirror_views_layout_js.refresh();
		}
		if ('pagination' == section) {
			if ('checked' != jQuery('.js-wpv-show-hide-filter-extra').attr('checked')) {
				jQuery('.js-wpv-show-hide-filter-extra').trigger('click');
				jQuery('.js-wpv-show-hide-update').parent().wpvToolsetMessage({
					text:jQuery('.js-wpv-show-hide-container').data('pagneedsfilter'),
											      type:'info',
								  inline:true,
								  stay:true
				});
			}
		}
	} else {
		if(jQuery('.js-wpv-settings-' + section).find('.js-wpv-section-unsaved').length > 0) {
			checkbox.attr('checked', 'checked');
			jQuery('.js-wpv-show-hide-update').parent().wpvToolsetMessage({
				text:section_changed,
				type:'error',
				inline:true,
				stay:true
			});
		} else if ('filter-extra' == section && 'checked' == jQuery('.js-wpv-show-hide-pagination').attr('checked')) {
			jQuery('.js-wpv-show-hide-filter-extra').attr('checked', true);
			jQuery('.js-wpv-show-hide-update').parent().wpvToolsetMessage({
				text:jQuery('.js-wpv-show-hide-container').data('pagneedsfilter'),
										      type:'info',
								 inline:true,
								 stay:true
			});
		} else if ('filter-extra' == section && jQuery('.js-wpv-settings-container-dps-filter').find('.js-wpv-section-unsaved').length > 0 ) {
			jQuery('.js-wpv-show-hide-filter-extra').attr('checked', true);
			jQuery('.js-wpv-show-hide-update').parent().wpvToolsetMessage({
				text:jQuery('.js-wpv-show-hide-container').data('dpsneedsfilter'),
										      type:'info',
								 inline:true,
								 stay:true
			});
		} else {
			jQuery('.js-wpv-settings-' + section).hide();
			if ( 'filter-extra' == section ) {
				jQuery('.js-wpv-settings-container-dps-filter').hide();
			}
			var metasection = checkbox.parents('.js-wpv-show-hide-section').data('metasection');
			if (
				0 == checkbox.parents('.js-wpv-show-hide-section').find('.js-wpv-show-hide:checked').length &&
				jQuery('.' + metasection).find('.wpv-setting-container').length == checkbox.parents('.js-wpv-show-hide-section').find('.js-wpv-show-hide').length
			) {
				jQuery('.' + metasection).hide();
			}
			input_value.val('off');
		}
	}
}

jQuery(document).on('click', '.js-wpv-show-hide-update', function(){  // TODO this should be DEPRECATED
	jQuery(this).parents('.js-wpv-show-hide-container').find('.toolset-alert').remove();
	var update_message = jQuery(this).data('success'),
		unsaved_message = jQuery(this).data('unsaved'),
		nonce = jQuery(this).data('nonce'),
		spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertBefore(jQuery(this)).show(),
		data_view_id = jQuery('.js-post_ID').val(),
		wpv_show_hide_sections = jQuery('.js-wpv-show-hide-value').serialize(),
		wpv_show_hide_metasections_help = jQuery('.js-wpv-show-hide-help-value').serialize(),
		purpose = jQuery('.js-view-purpose').val();
	var data = {
		action: 'wpv_save_screen_options',
		id: data_view_id,
		settings: wpv_show_hide_sections,
		helpboxes: wpv_show_hide_metasections_help,
		purpose: purpose,
		wpnonce: nonce
	};
	jQuery.post(ajaxurl, data, function(response) {
		if ( (typeof(response) !== 'undefined') ) {
			if (0 != response) {
				jQuery('.js-wpv-show-hide-update').parent().wpvToolsetMessage({
					text:update_message,
					type:'success',
					inline:true,
					stay:false
				});
			} else {
				jQuery('.js-wpv-show-hide-update').parent().wpvToolsetMessage({
					text:unsaved_message,
					type:'error',
					inline:true,
					stay:true
				});
				console.log( "Error: WordPress AJAX returned ", response );
			}
		} else {
			jQuery('.js-wpv-show-hide-update').parent().wpvToolsetMessage({
				text:unsaved_message,
				type:'error',
				inline:true,
				stay:true
			});
			console.log( "Error: AJAX returned ", response );
		}
	})
	.fail(function(jqXHR, textStatus, errorThrown) {
		jQuery('.js-wpv-show-hide-update').parent().wpvToolsetMessage({
			text:unsaved_message,
			type:'error',
			inline:true,
			stay:true
		});
		console.log( "Error: ", textStatus, errorThrown );
	})
	.always(function() {
		spinnerContainer.remove();
	});
});

// Message boxes display

function wpv_show_hide_purpose_help_init() {
	var purpose = jQuery('.js-view-purpose').val();
	jQuery('.js-wpv-show-hide-help').each(function(){
		var metasection = jQuery(this).data('metasection'),
					      state = jQuery(this).attr('checked');
					      if ('checked' == state) {
		jQuery('.js-metasection-help-' + metasection + '.js-for-view-purpose-' + purpose).show();
					      } else {
						      jQuery('.js-metasection-help-' + metasection + '.js-for-view-purpose-' + purpose).hide();
					      }
	});
}

jQuery(document).on('click', '.js-metasection-help-query .js-toolset-help-close-main', function(){
	jQuery('.js-wpv-show-hide-query-help').prop('checked', false);
	jQuery('.js-wpv-show-hide-query-help-value').val('off');
});

jQuery(document).on('click', '.js-metasection-help-filter .js-toolset-help-close-main', function(){
	jQuery('.js-wpv-show-hide-filter-help').prop('checked', false);
	jQuery('.js-wpv-show-hide-filter-help-value').val('off');
});

jQuery(document).on('click', '.js-metasection-help-layout .js-toolset-help-close-main', function(){
	jQuery('.js-wpv-show-hide-layout-help').prop('checked', false);
	jQuery('.js-wpv-show-hide-layout-help-value').val('off');
});

jQuery(document).on('change', '.js-wpv-show-hide-help', function(){
	var state = jQuery(this).attr('checked'),
		    metasection = jQuery(this).data('metasection'),
		    purpose = jQuery('.js-view-purpose').val();
		    if ('checked' == state) {
			    jQuery('.js-for-view-purpose-' + purpose + '.js-metasection-help-' + metasection).show();
			    jQuery('.js-wpv-show-hide-' + metasection + '-help-value').val('on');
		    } else {
			    jQuery('.js-metasection-help-' + metasection).hide();
			    jQuery('.js-wpv-show-hide-' + metasection + '-help-value').val('off');
		    }
});

// Change View purpose

jQuery(document).on('change', '.js-view-purpose', function(){
	var purpose = jQuery(this).val();
	jQuery('.js-wpv-show-hide-help').each(function(){
		var state = jQuery(this).attr('checked'),
			metasection = jQuery(this).data('metasection');
	
		jQuery('.js-metasection-help-' + metasection).hide();
		if ('checked' == state) {
			jQuery('.js-for-view-purpose-' + purpose + '.js-metasection-help-' + metasection).show();
		}
	});
	wpv_set_sections_for_view_purpose(purpose);
});

// Given a View purpose, set the open and closed sections

function wpv_set_sections_for_view_purpose(purpose) {
	var all_sections = Array('query-options', 'limit-offset', 'content', 'pagination', 'filter-extra', 'layout-extra', 'pagination');
	var hide_sections = Array();
	if ('all' == purpose) {
		hide_sections = Array('pagination', 'filter-extra');
	} else if ('pagination' == purpose) {
		hide_sections = Array('limit-offset');
	} else if ('slider' == purpose) {
		hide_sections = Array('limit-offset');
	} else if ('parametric' == purpose) {
		hide_sections = Array('pagination');
	} else if ('bootstrap-grid' == purpose) {
		hide_sections = Array('layout-extra', 'content');
	} else if ('full' == purpose) {
	}

	var sections_length = all_sections.length;
	for (var i = 0; i < sections_length; i++) {
		var found = false;
		var hide_length = hide_sections.length;
		
		for (j = 0; j < hide_length; j++) {
			if (all_sections[i] == hide_sections[j]) {
				found = true;
			}
		}

		var item = jQuery('.js-wpv-show-hide-' + all_sections[i]);
		item.attr('checked', !found);
		wpv_show_hide_section_change(item);
	}
}


// Show or hide layout hint extra text

jQuery(document).on('click', '.js-wpv-layout-help-extra-show', function(e){
	e.preventDefault();
	jQuery('.js-wpv-layout-help-extra').fadeIn('fast');
	jQuery(this).parent().hide();
	return false;
});

jQuery(document).on('click', '.js-wpv-layout-help-extra-hide', function(e){
	e.preventDefault();
	jQuery('.js-wpv-layout-help-extra').hide();
	jQuery('.js-wpv-layout-help-extra-show').parent().show();
	return false;
});

// Layout wizard help

function wpv_layout_wizard_hint() {
	if ( !jQuery('.js-wpv-layout-wizard-hint').hasClass('js-toolset-help-dismissed') ) {
		jQuery('.js-wpv-layout-wizard-hint').fadeIn('fast');
	}
}

jQuery(document).on('click', '.js-wpv-layout-wizard-hint .toolset-help-footer .js-toolset-help-close-forever', function(){
	var data = {
		action: 'wpv_layout_wizard_hint_disable',
		wpnonce: jQuery('.js-wpv-layout-wizard-dismiss').data('nonce')
	};
	jQuery.post(ajaxurl, data, function(response) {
		if ( (typeof(response) !== 'undefined')) {
			if (response == 0) {
				console.log( "Error: WordPress AJAX returned ", response );
			}
		} else {
			console.log( "Error: AJAX returned ", response );
		}
	})
	.fail(function(jqXHR, textStatus, errorThrown) {
		console.log( "Error: ", textStatus, errorThrown );
	})
	.always(function() {
		jQuery('.js-wpv-layout-wizard-hint').addClass('js-toolset-help-dismissed').hide();
	});
});

// Inline CT help

function wpv_inline_ct_hint() {
	if ( !jQuery('.js-wpv-content-template-hint').hasClass('js-toolset-help-dismissed') ) {
		if ( jQuery('#wpv-ct-add-to-editor-btn').prop('checked') == true || jQuery('input[name=wpv-ct-type]:checked').val() == 2 ){
			jQuery('.js-wpv-content-template-hint').find('.js-wpv-ct-was-not-inserted').addClass('hidden');
			jQuery('.js-wpv-content-template-hint').find('.js-wpv-ct-was-inserted').removeClass('hidden');
		} else {
			jQuery('.js-wpv-content-template-hint').find('.js-wpv-ct-was-inserted').addClass('hidden');
			jQuery('.js-wpv-content-template-hint').find('.js-wpv-ct-was-not-inserted').removeClass('hidden');
		}
		jQuery('.js-wpv-content-template-hint').fadeIn('fast');
	}
}

jQuery(document).on('click', '.js-wpv-content-template-hint .toolset-help-footer .js-toolset-help-close-forever', function(){
	var data = {
		action: 'wpv_content_template_hint_disable',
		wpnonce: jQuery('.js-wpv-content-template-dismiss').data('nonce')
	};
	jQuery.post(ajaxurl, data, function(response) {
		if ( (typeof(response) !== 'undefined')) {
			if (response == 0) {
				console.log( "Error: WordPress AJAX returned ", response );
			}
		} else {
			console.log( "Error: AJAX returned ", response );
		}
	})
	.fail(function(jqXHR, textStatus, errorThrown) {
		console.log( "Error: ", textStatus, errorThrown );
	})
	.always(function() {
		jQuery('.js-wpv-content-template-hint').addClass('js-toolset-help-dismissed').hide();
	});
});