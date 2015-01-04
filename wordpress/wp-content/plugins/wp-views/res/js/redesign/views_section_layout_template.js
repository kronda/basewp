jQuery(function($) { // TODO - IMPORTANT avoid non js- prefixed selectors and IDs as selectors
	//Show popup.. add new content to view
	$(document).on('click','.js-wpv-ct-assign-to-view', function() {
		var tid = $(this).data('id');
		var data = {
			action : 'wpv_assign_ct_to_view',
			view_id : tid,
			wpnonce : $('#wpv-ct-inline-edit').attr('value')
		};
		$.colorbox({
			href: ajaxurl,
			data: data,
			onComplete:function(){
				$('.js-create-new-template').prop('disabled',true).addClass('button-secondary').removeClass('button-primary');
			}
		});
	});

	$(document).on('click','.js-wpv-ct-update-inline', function() {
		var ct_id = $(this).data('id');
		var ct_value = window["wpv_ct_inline_editor_" + ct_id].getValue();
		var data = {
			action : 'wpv_ct_update_inline',
			ct_value : ct_value,
			ct_id : ct_id,
			wpnonce : $('#wpv-ct-inline-edit').attr('value')
		};
		$.post(ajaxurl, data, function(response) {
			 if ( response == 0){
			 	console.log('Content Template not found');
			 }else{
			 $('.js-wpv-ct-update-inline-'+ ct_id).parent().find('.toolset-alert-error').remove();
			 $('.js-wpv-ct-update-inline-' + ct_id).removeClass('button-primary').addClass('button-secondary');
			 window["wpv_ct_inline_editor_val_" + ct_id] = window["wpv_ct_inline_editor_" + ct_id].getValue();

			 $('.js-wpv-ct-update-inline-'+ ct_id).prop('disabled',true);
			 $('.js-wpv-ct-update-inline-'+ ct_id).removeClass('js-wpv-section-unsaved');
			 if ( $('.js-wpv-section-unsaved').length <= 0){
			 	setConfirmUnload(false);
			 }
			 $('.js-wpv-content-template-section-errors').wpvToolsetMessage({
				text:wpv_view_ct_msg4,
				stay:false,
				close:false,
				fadeOut:2000,
				fadeIn: 1000,
				type: 'success'
			 });
			 }
		});
	});

	$(document).on('change','input[name=wpv-ct-type]', function() {
		$('#js-wpv-add-to-editor-line').slideDown("slow");
		if ( $(this).val() == 0 ){
			if( $('#js-wpv-ct-add-id').val() == 0){
				$('#js-wpv-ct-add-id').focus();
				$('.js-create-new-template').prop('disabled',true).addClass('button-secondary').removeClass('button-primary');
			}
			else{
				$('.js-create-new-template').prop('disabled',false).removeClass('button-secondary').addClass('button-primary');
			}
		}
		if ( $(this).val() == 2 ){
			if( $('#js-wpv-ct-add-id-assigned').val() == 0){
				$('#js-wpv-ct-add-id-assigned').focus();
				$('.js-create-new-template').prop('disabled',true).addClass('button-secondary').removeClass('button-primary');
			}
			else{
				$('.js-create-new-template').prop('disabled',false).removeClass('button-secondary').addClass('button-primary');
			}
			$('#js-wpv-add-to-editor-line').hide();

		}
		if ($(this).val() == 1 ){
			if ($('#js-wpv-ct-type-new-name').val() == ''){
				$('#js-wpv-ct-type-new-name').focus();
				$('.js-create-new-template').prop('disabled',true).addClass('button-secondary').removeClass('button-primary');
			}
			else{
				$('.js-create-new-template').prop('disabled',false).removeClass('button-secondary').addClass('button-primary');
			}
		}
	});

	$(document).on('focus input','#js-wpv-ct-type-new-name', function() {
		$('.js-add-new-ct-error-container .toolset-alert').remove();
		$('input[name=wpv-ct-type]').prop('checked',false);
		$('#js-wpv-ct-type-new').prop('checked',true);
		if ( $(this).val() == ''){
			$('.js-create-new-template').prop('disabled',true).addClass('button-secondary').removeClass('button-primary');
		}
		else{
			$('.js-create-new-template').prop('disabled',false).removeClass('button-secondary').addClass('button-primary');
		}
		$('#js-wpv-add-to-editor-line').slideDown("slow");
	});

	$(document).on('focus change','#js-wpv-ct-add-id', function() {
		$('input[name=wpv-ct-type]').prop('checked',false);
		$('#js-wpv-ct-type-existing').prop('checked',true);
		if ( $(this).val() == 0){
			$('.js-create-new-template').prop('disabled',true).addClass('button-secondary').removeClass('button-primary');
		}
		else{
			$('.js-create-new-template').prop('disabled',false).removeClass('button-secondary').addClass('button-primary');
		}
		$('#js-wpv-add-to-editor-line').slideDown("slow");
	});

	$(document).on('focus change','#js-wpv-ct-add-id-assigned', function() {
		$('input[name=wpv-ct-type]').prop('checked',false);
		$('#js-wpv-ct-type-existing-asigned').prop('checked',true);
		if ( $(this).val() == 0){
			$('.js-create-new-template').prop('disabled',true).addClass('button-secondary').removeClass('button-primary');
		}
		else{
			$('.js-create-new-template').prop('disabled',false).removeClass('button-secondary').addClass('button-primary');
		}
		//$('#js-wpv-add-to-editor-line').css('display','none');
		$('#js-wpv-add-to-editor-line').hide();
	});

	//Submit add new content template to view
	$(document).on('submit','#wpv-add-new-content-template-form', function() {
		//add existing template
		send_ajax = true;
		open_inline = false;
		var check_exists = false;
		$('#wpv-no-ct-assigned').remove();
		$('.js-create-new-template').prop('disabled',true);
		if ( $('input[name=wpv-ct-type]:checked').val() == 0 ){
			if ($('#js-wpv-ct-add-id').val() == ''){
				return;
			}
			data = {
				action : 'wpv_add_view_template',
				view_id : $('.js-wpv-ct-assign-to-view').data('id'),
				template_id : $('#js-wpv-ct-add-id').val(),
				wpnonce : $('#wpv-ct-inline-edit').attr('value')
			};
			templateName = $("#js-wpv-ct-add-id option:selected").text();
			check_exists = 	$('#js-wpv-ct-add-id').val();
			open_inline = true;
		}
		//create new template
		if( $('input[name=wpv-ct-type]:checked').val() == 1 ){
			if ($('#js-wpv-ct-type-new-name').val() == ''){
				return;
			}
			data = {
				action : 'wpv_add_view_template',
				view_id : $('.js-wpv-ct-assign-to-view').data('id'),
				template_name : $('#js-wpv-ct-type-new-name').val(),
				wpnonce : $('#wpv-ct-inline-edit').attr('value')
			};
			templateName = $('#js-wpv-ct-type-new-name').val();
			open_inline = true;
		}
		if( $('input[name=wpv-ct-type]:checked').val() == 2 ){
			send_ajax = false;
			templateName = $("#js-wpv-ct-add-id-assigned option:selected").text();
		}
		if ( send_ajax ){
			$.post(ajaxurl, data, function(response) {
				if ( response == 'error' ){
					console.log('Error: Content template not found in database');
					$('.wpv_ct_inline_message').remove();
					return false;
				}
				else if( response == 'error_name' ){
					$('.js-add-new-ct-error-container').wpvToolsetMessage({
					text: wpv_view_ct_msg8,
					stay: true,
					close: false,
					type: ''
				 	});
				 	$('.wpv_ct_inline_message').remove();
				 	$('.js-create-new-template').prop('disabled',false);
				 	return false;
					//
				}
				else{
					$('.wpv-settings-templates').show();
					if ( check_exists && $('#wpv-ct-listing-'+check_exists).html()){
						$('#wpv-ct-listing-'+check_exists).addClass('js-wpv-ct-listing-show').removeClass('js-wpv-ct-listing-delete').removeClass('hidden');
					}
					else{
						$('.js-wpv-content-template-view-list ul').eq(0).append(response);
					}

					$('.js-wpv-content-template-section-errors').wpvToolsetMessage({
						text:wpv_view_ct_msg1,
						stay:false,
						close:false,
						fadeOut:2000,
						fadeIn: 1000,
						type: 'success'
					});


					if (open_inline){
							$('.js-wpv-ct-listing').last().find('.js-wpv-content-template-open').click();
					}
				}
				wpv_inline_ct_hint();
				wpv_ct_add_shortcode(templateName);
				$('.wpv_ct_inline_message').remove();
				$.colorbox.close();

			});
		}
		else{
			wpv_inline_ct_hint();
			wpv_ct_add_shortcode(templateName);
			$('.wpv_ct_inline_message').remove();
			$.colorbox.close();

		}
		return false;
	});

	function wpv_ct_add_shortcode(content){
		if ( $('#js-wpv-ct-add-to-editor-btn').prop('checked') == true || $('input[name=wpv-ct-type]:checked').val() == 2 ){
			content = '[wpv-post-body view_template="'+ content +'"]';
			var current_cursor=codemirror_views_layout.getCursor(true);
            codemirror_views_layout.setSelection(current_cursor, current_cursor);
            codemirror_views_layout.replaceSelection(content, 'end');
		}
	}

	//Show popup to confirm
	$(document).on('click','.js-wpv-content-template-open', function(e) {

		e.preventDefault();

		var $this = $(this);
		var id = $this.data('target');
		var viewID =  $this.data('viewid');
		var $inlineEditor = $('.js-wpv-ct-inline-edit ').filter('[data-template-id='+ id +']');
		var $arrowIcon = $this.find('[class^="icon-"]');

		$inlineEditor.toggle( 0 ,function() {
			if ( $inlineEditor.is(':hidden') ) {
				$arrowIcon
					.removeClass('icon-caret-up')
					.addClass('icon-caret-down');


			} else {
				
				$arrowIcon
					.removeClass('icon-caret-down')
					.addClass('icon-caret-up');
				if (!window["wpv_ct_inline_editor_" + id]){
					var $spinnerContainer = $('<div class="spinner ajax-loader">').insertAfter($(this)).show();
					data = {
						action : 'wpv_ct_loader_inline',
						id : id,
						view_id: viewID,
						wpnonce : $('#wpv-ct-inline-edit').attr('value')
					};

					$.post(ajaxurl, data, function(response) {

						if ( response == 'error' ){
							console.log('Error, Content Template not found.');

						}
						else{
							$('#wpv-ct-listing-'+id).find('.js-wpv-ct-inline-edit').html(response);
							if( typeof cred_cred != 'undefined'){
								cred_cred.posts();
							}
							window["wpv_ct_inline_editor_" + id] = icl_editor.codemirror('wpv-ct-inline-editor-'+id, true);
							window["wpv_ct_inline_editor_val_" + id] = window["wpv_ct_inline_editor_" + id].getValue();
							$('.js-wpv-ct-update-inline-'+ id).prop('disabled',true);
							window["wpv_ct_inline_editor_" + id].on('change', function(){
								if( window["wpv_ct_inline_editor_val_" + id] !=  window["wpv_ct_inline_editor_" + id].getValue()){
									$('.js-wpv-ct-update-inline-'+ id).addClass('js-wpv-section-unsaved');
									$('.js-wpv-ct-update-inline-'+ id).prop('disabled',false);
									setConfirmUnload(true);
									$('.js-wpv-ct-update-inline-' + id).removeClass('button-secondary').addClass('button-primary');
								}
								else{
									$('.js-wpv-ct-update-inline-'+ id).removeClass('js-wpv-section-unsaved');
									$('.js-wpv-ct-update-inline-'+ id).parent().find('.toolset-alert-error').remove();
									$('.js-wpv-ct-update-inline-'+ id).prop('disabled',true);
									if ( $('.js-wpv-section-unsaved').length <= 0){
									 	setConfirmUnload(false);
									}
									$('.js-wpv-ct-update-inline-' + id).removeClass('button-primary').addClass('button-secondary');
								}
							});


						}
						$spinnerContainer.remove();
					});

				}


			}
		});

		return false;

	});

	// Confirmation for the "Remove" button
	$(document).on('click','.js-wpv-ct-remove-from-view', function(e) {

		e.preventDefault();

		var $this = $(this);
		view_id = $this.parent().parent().data('viewid');
		id = $this.parent().parent().data('id');
		data = {
			action : 'wpv_remove_content_template_from_view',
			view_id : view_id,
			id: id,
			wpnonce : $('#wpv-ct-inline-edit').attr('value')
		};

		$.colorbox({
			href: ajaxurl,
			data: data
		});

		return false;

	});

	// Unassign  content template from View
	$(document).on('click','.js-remove-template-from-view', function(e) {

		e.preventDefault();

		var data = {
			action : 'wpv_remove_content_template_from_view_process',
			view_id : $(this).data('viewid'),
			id : $(this).data('id'),
			wpnonce : $('#wpv-ct-inline-edit').attr('value')
		};
		var request = $.post( ajaxurl, data, function(respond) {
			$.colorbox.close();
			console.log(respond);
			$('#wpv-ct-listing-'+id).removeClass('js-wpv-ct-listing-show').addClass('js-wpv-ct-listing-delete').addClass('hidden');
			if ( $(".js-wpv-content-template-view-list ul li.js-wpv-ct-listing-show").size() < 1 ){
				$('.js-wpv-content-template-section-errors').wpvToolsetMessage({
					type: 'info',
					stay: true,
					classname: 'wpv_ct_inline_message',
					text : wpv_view_ct_msg7,
					close: true,
					onClose: function() {
						$('.wpv-settings-templates').hide();
					}
				});
			}
			$('.js-wpv-content-template-section-errors').wpvToolsetMessage({
				text:wpv_view_ct_msg3,
				stay:false,
				close:true,
				fadeOut:2000,
				fadeIn: 1000,
				type: 'success'
			});
		});
		return false;
	});

});