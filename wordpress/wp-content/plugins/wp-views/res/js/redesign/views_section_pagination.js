jQuery(function($){

	// pagination

	view_pagination_mode();

	// hints

	wpv_pagination_insert_hint();

	$('input[name="pagination\\[mode\\]"]').click(function(e){
		$('.js-pagination-advanced').each(function(){
			$(this).data('state','closed');
			$(this).text($(this).data('closed'));
		});
		view_pagination_mode();
	});

	view_pagination_ajax();

	$('input[name="ajax_pagination\\[\\]"]').click(function(e){
		$('.js-pagination-advanced').each(function(){
			$(this).data('state','closed');
			$(this).text($(this).data('closed'));
		});
		view_pagination_ajax();
	});

	$('.js-pagination-advanced').click(function(){
		var state = $(this).data('state');
		var text = '';
		if (state == 'closed') {
			$(this).data('state','opened');
			$(this).text($(this).data('opened'));
			$('.wpv-pagination-advanced').fadeIn('fast');
		}
		else if (state == 'opened') {
			$(this).data('state','closed');
			$(this).text($(this).data('closed'));
			$('.wpv-pagination-advanced').hide();
		}
	});

	$(document).on('click','.js-disable-events',function(e){
		e.preventDefault();
		return false;
	});

});

function view_pagination_mode(){
	jQuery('.wpv-pagination-paged').hide();
	jQuery('.wpv-pagination-rollover').hide();
	jQuery('.wpv-pagination-advanced').hide();
	var pag_mode = jQuery('input[name="pagination\\[mode\\]"]:checked').val();
	if ('paged' == pag_mode) {
		jQuery('.wpv-pagination-rollover').fadeOut('fast');
		jQuery('.wpv-pagination-shared').hide();
		jQuery('.wpv-pagination-paged-ajax').hide();
		jQuery('.wpv-pagination-advanced').hide();
		jQuery('.wpv-pagination-paged').fadeIn('fast');
		jQuery('.wpv-pagination-options-box').fadeIn('fast');
		jQuery('.js-pagination-zero').val('enable');
		view_pagination_ajax();
		//	jQuery().hide();
	} else if ('rollover' == pag_mode) {
		jQuery('.wpv-pagination-paged').fadeOut('fast');
		jQuery('.wpv-pagination-rollover').fadeIn('fast');
		// jQuery('.wpv-pagination-shared').fadeIn('fast');
		jQuery('.wpv-pagination-paged-ajax').hide();
		jQuery('.wpv-pagination-advanced').hide();
		jQuery('.wpv-pagination-options-box').fadeIn('fast');
		jQuery('.js-pagination-zero').val('enable');
		//	jQuery().fadeIn('fast');
	} else {
		jQuery('.wpv-pagination-options-box').hide();
		jQuery('.wpv-pagination-paged').fadeOut('fast');
		jQuery('.wpv-pagination-rollover').fadeOut('fast');
		jQuery('.wpv-pagination-shared').fadeOut('fast');
		jQuery('.js-pagination-zero').val('disable');
	}
}

function view_pagination_ajax(){
	jQuery('.wpv-pagination-advanced').hide();
	var paged_mode = jQuery('input[name="ajax_pagination\\[\\]"]:checked').val();
	if ('disable' == paged_mode || undefined === paged_mode) {
		jQuery('.wpv-pagination-shared').hide();
		jQuery('.wpv-pagination-paged-ajax').hide();
		jQuery('.wpv-pagination-advanced').hide();
		jQuery('[data-section="ajax_pagination"]').hide();
	} else {
		var pag_mode = jQuery('input[name="pagination\\[mode\\]"]:checked').val();
		if ('rollover' != pag_mode) {
			jQuery('.wpv-pagination-paged-ajax:not(.wpv-pagination-advanced)').fadeIn('fast');
		}
		jQuery('.wpv-pagination-shared').hide();
		jQuery('.wpv-pagination-advanced').hide();
		jQuery('[data-section="ajax_pagination"]').show();
	}
}

// Enable/disable pagination button in the MetaHTML textarea

function wpv_pagination_button_state() { // TODO review this and show an explanation popup when disabled telling why
	var pag_mode = jQuery('input[name="pagination\\[mode\\]"]:checked').val();
	if (pag_mode == 'none') {
		jQuery('.js-wpv-pagination-popup').prop('disabled', true).addClass('hidden');
	} else {
		jQuery('.js-wpv-pagination-popup').prop('disabled', false).removeClass('hidden');
	}
}

// Pagination help once we enable pagination in the Pagination section and only of there are no pagination shortcodes already added

function wpv_pagination_insert_hint() {
	jQuery('.js-wpv-pagination-hint-message').hide();
	var pag_mode = jQuery('input[name="pagination\\[mode\\]"]:checked').val();
	var filter_html = codemirror_views_query.getValue();
	if (filter_html.search('wpv-pager-current-page') == -1 && filter_html.search('wpv-pager-prev-page') == -1 && filter_html.search('wpv-pager-next-page') == -1) {
		if (pag_mode != 'none') {
			jQuery('.js-wpv-pagination-hint-message-for-' + pag_mode).fadeIn('fast');
		}
	}
}

jQuery(function($){

	jQuery(document).on('click', '.js-wpv-close-pagination-hint', function(){
		jQuery('.js-wpv-pagination-hint-message').hide();
	});

	jQuery(document).on('click', '.js-wpv-open-pagination-hint-popup', function(){
		jQuery.colorbox({
			inline: true,
			href:'.js-pagination-form-hint',
			open: true,
			onComplete: function(){
				$('.js-pagination-form-hint input[type="checkbox"]').prop('checked', true);
				$('.js-pagination-preview-element[data-type="page-selector-link"]').hide();
				$('.js-pagination-preview-element[data-type="page-selector-select"]').show();
				$('.js-pagination-control-hint-type').val('drop_down');
				$('.js-insert-pagination-from-hint').prop('disabled', false).removeClass('button-secondary').addClass('button-primary');
				$paginationPreview.show();
				$paginationPreviewMessage.hide();
				$paginationPreviewElements.removeClass('disabled');
			}
		});
	});

	jQuery(document).on('change', '.js-pagination-form-hint input', function(){
		var pag_control = jQuery('.js-pagination-form-hint input:checked').serializeArray();
		if (pag_control.length < 1) {
			jQuery('.js-insert-pagination-from-hint').prop('disabled', true).removeClass('button-primary').addClass('button-secondary');
		} else {
			jQuery('.js-insert-pagination-from-hint').prop('disabled', false).removeClass('button-secondary').addClass('button-primary');
		}
	});

	jQuery(document).on('click', '.js-insert-pagination-from-hint', function() {
		var active_textarea = jQuery(this).data('content');
		window.wpcfActiveEditor = active_textarea;
		var pag_control = jQuery('.js-pagination-form-hint input:checked').serializeArray();
		var pag_selector_mode = jQuery('select.js-pagination-control-hint-type').val();
		var pag_preview = wpv_get_pagination_preview(pag_control, pag_selector_mode);
		var pag_shortcode = wpv_get_pagination_shortcode(pag_control, pag_selector_mode);
		pag_shortcode = '[wpv-pagination]' +  pag_shortcode + '[/wpv-pagination]';
		var filter_html = codemirror_views_query.getValue();
		var new_value = filter_html.replace("[wpv-filter-end]", pag_shortcode + "\n[wpv-filter-end]");
		codemirror_views_query.setValue(new_value);
		jQuery.colorbox.close();
		jQuery('.js-wpv-pagination-hint-message').hide();
		if ( !jQuery('.js-wpv-pagination-hint-message-result').hasClass('js-toolset-help-dismissed') ) {
			jQuery('.js-wpv-pagination-hint-shortcode-meaning').html(wpv_get_pagination_shortcode_explanation(pag_control, pag_selector_mode));
			jQuery('.js-wpv-pagination-hint-message-result').fadeIn('fast');
		}
	});

	jQuery(document).on('click', '.js-wpv-pagination-hint-message-result .toolset-help-footer .js-toolset-help-close-forever', function(){
		var data = {
			action: 'wpv_pagination_hint_result_disable',
			wpnonce: jQuery('.js-wpv-pagination-hint-result-dismiss').data('nonce')
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
			jQuery('.js-wpv-pagination-hint-message-result').addClass('js-toolset-help-dismissed').hide();
		});
	});

	// Pagination button


	// Pagination popup defaults
	$('.js-insert-pagination').prop('disabled', true).removeClass('button-primary').addClass('button-secondary');

	var $paginationControls = $('input[name="pagination_control"], .js-pagination-control-hint');
	var $paginationPreviewMessage = $('.js-choose-pagination-type');
	var $paginationPreviewElements =  $('.js-pagination-preview-element');
	var $paginationPreview = $('.js-pagination-preview');

	$paginationControls.change(function(){

		var target = $(this).data('target');
		var type = $(this).prop('type');
		var is_selected = false;
		var $targetElement = $paginationPreviewElements.filter('[data-name="' + target + '"]');

		$paginationPreview.fadeIn();
		$paginationPreviewMessage.hide();
		$('.js-insert-pagination')
			.prop('disabled', false)
			.addClass('button-primary')
			.removeClass('button-secondary');

		if ( $(this).prop('checked') ) {
			is_selected = true;
		}

		if ( type === 'radio' ) {

			if ( is_selected ) {
				$paginationPreviewElements.addClass('disabled');
				$targetElement.removeClass('disabled');
			}
		}

		else if ( type === 'checkbox' ) {

			if ( is_selected ) {
				$targetElement.removeClass('disabled');
			}

			else {
				$targetElement.addClass('disabled');
			}

		}

	});

	$('.js-pagination-control-type, .js-pagination-control-hint-type').on('change', function(){
		val = $(this).val();
		var $pageSelectors = $('.js-pagination-preview-element').filter('[data-name="page-selector"]');
		if ( val === 'link' ) {
			$pageSelectors.hide();
			$pageSelectors.filter('[data-type*="page-selector-link"]').show();
		}
		if ( val === 'drop_down' ) {
			$pageSelectors.hide();
			$pageSelectors.filter('[data-type*="page-selector-select"]').show();
		}
	});

	wpv_pagination_button_state();

	jQuery(document).on('click', '.js-wpv-pagination-popup', function(){
		var active_textarea = jQuery(this).data('content');
		window.wpcfActiveEditor = active_textarea;
		var open_popup = false;
		if ( active_textarea == 'wpv_filter_meta_html_content' ) {
			var current_cursor = codemirror_views_query.getCursor(true);
			var text_before = codemirror_views_query.getRange({line:0,ch:0}, current_cursor);
			var text_after = codemirror_views_query.getRange(current_cursor, {line:codemirror_views_query.lastLine(),ch:null});
			if (text_before.search(/\[wpv-filter-start.*?\]/g) != -1 && text_after.search(/\[wpv-filter-end.*?\]/g) != -1) {
				open_popup = true;
			} else {
				jQuery('.js-wpv-settings-filter-extra .js-error-container').wpvToolsetMessage({
					text:wpv_pagination_texts.wpv_filter_insert_wrong_cursor_position,
					stay:true,
					close:true,
					fadeOut:300
				});
			}
		}
		if ( active_textarea == 'wpv_layout_meta_html_content' ) {
			var current_cursor = codemirror_views_layout.getCursor(true);
			var text_before = codemirror_views_layout.getRange({line:0,ch:0}, current_cursor);
			var text_after = codemirror_views_layout.getRange(current_cursor, {line:codemirror_views_layout.lastLine(),ch:null});
			if (text_before.search(/\[wpv-layout-start.*?\]/g) != -1 && text_after.search(/\[wpv-layout-end.*?\]/g) != -1) {
				open_popup = true;
			} else {
				jQuery('.js-wpv-settings-layout-extra .js-error-container').wpvToolsetMessage({
					text:wpv_pagination_texts.wpv_layout_insert_wrong_cursor_position,
					stay:true,
					close:true,
					fadeOut:300
				});
			}
		}
		if ( open_popup ) {
			jQuery.colorbox({
				inline: true,
				href: '.js-pagination-form-dialog',
				open: true,
				onComplete : function() {
					$('.js-pagination-form-dialog input[type="radio"]').prop('checked', false);
					$('.js-pagination-preview-element[data-type="page-selector-link"]').hide();
					$('.js-pagination-preview-element[data-type="page-selector-select"]').show();
					$('.js-pagination-control-type').val('drop_down');
					$paginationPreview.hide();
					$paginationPreviewMessage.show();
					$paginationPreviewElements.addClass('disabled');
				}
			});
		}
	});

	jQuery(document).on('click', '.js-insert-pagination', function(){
		var pag_control = jQuery('input[name="pagination_control"]:checked').serializeArray();
		var pag_selector_mode = jQuery('select[name="pagination_controls_type"]').val();
		var pag_wrap = jQuery('input[name="pagination_display"]').prop('checked');
		var pag_preview = wpv_get_pagination_preview(pag_control, pag_selector_mode);
		var pag_shortcode = wpv_get_pagination_shortcode(pag_control, pag_selector_mode);
		if ( pag_wrap ) {
			pag_shortcode = '[wpv-pagination]' +  pag_shortcode + '[/wpv-pagination]';
		}
		icl_editor.insert(pag_shortcode);
		jQuery.colorbox.close();
	});

});
// Pagination preview & shortcodes functions

function wpv_get_pagination_preview(pag_control, pag_selector_mode) {
	var output = '';
	if (pag_control == 'page_num') {

	} else if (pag_control == 'page_selector') {

	} else if (pag_control == 'page_controls') {

	}
	return output;
}

function wpv_get_pagination_shortcode(pag_control, pag_selector_mode) {
	var output = '';
	jQuery.each(pag_control, function(name, value){
		if (value.value == 'page_num') {
			output += '[wpv-pager-current-page]';
		} else if (value.value == 'page_total') {
			output += '[wpv-pager-num-page]';
		} else if (value.value == 'page_selector') {
			output += '[wpv-pager-current-page style="' + pag_selector_mode + '"]';
		} else if (value.value == 'page_controls') {
			output += '[wpv-pager-prev-page][wpml-string context="wpv-views"]Previous[/wpml-string][/wpv-pager-prev-page][wpv-pager-next-page][wpml-string context="wpv-views"]Next[/wpml-string][/wpv-pager-next-page]';
		}
	});
	return output;
}

function wpv_get_pagination_shortcode_explanation(pag_control, pag_selector_mode) {
	var output = '<dt><strong>[wpv-pagination][/wpv-pagination]</strong></dt>';
	output += '<dd>' + wpv_pagination_texts.wpv_page_pagination_shortcode_definition + '</dd>';
	jQuery.each(pag_control, function(name, value){
		if (value.value == 'page_num') {
			output += '<dt><strong>[wpv-pager-current-page]</strong></dt>';
			output += '<dd>' + wpv_pagination_texts.wpv_page_num_shortcode_definition + '</dd>';
		} else if (value.value == 'page_total') {
			output += '<dt><strong>[wpv-pager-num-page]</strong></dt>';
			output += '<dd>' + wpv_pagination_texts.wpv_page_total_shortcode_definition + '</dd>';
		} else if (value.value == 'page_selector') {
			output += '<dt><strong>[wpv-pager-current-page style="' + pag_selector_mode + '"]</strong></dt>';
			output += '<dd>' + wpv_pagination_texts.wpv_page_selector_shortcode_definition + '</dd>';
		} else if (value.value == 'page_controls') {
			output += '<dt><strong>[wpv-pager-prev-page][wpml-string context="wpv-views"]</strong>Previous<strong>[/wpml-string][/wpv-pager-prev-page]</strong></dt>';
			output += '<dd>' + wpv_pagination_texts.wpv_page_pre_shortcode_definition + '</dd>';
			output += '<dt><strong>[wpv-pager-next-page][wpml-string context="wpv-views"]</strong>Next<strong>[/wpml-string][/wpv-pager-next-page]</strong></dt>';
			output += '<dd>' + wpv_pagination_texts.wpv_page_next_shortcode_definition + '</dd>';
		}
	});
	return output;
}
