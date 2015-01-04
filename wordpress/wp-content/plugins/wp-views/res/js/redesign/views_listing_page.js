jQuery(document).ready(function($)  {

    $('.js-views-actions option').removeAttr('selected');

    // Store the view ID
    var data_view_id = null;

	// Delete View action after confirmation

	$(document).on('click','.js-remove-view-permanent', function () {
		var spinnerContainer = $('<div class="spinner ajax-loader">').insertBefore($(this)).show();
		$(this).prop('disabled', true).addClass('button-secondary').removeClass('button-primary');
		
		var data = {
			action: 'wpv_delete_view_permanent',
			id: data_view_id,
			wpnonce : $(this).data('nonce')
		};
		$.ajax({
			async:false,
			type:"POST",
			url:ajaxurl,
			data:data,
			success:function(response){
				if ( (typeof(response) !== 'undefined') && (response == data.id)) {
					var url_params = decodeURIParams();
					if ( typeof(url_params['paged']) !== 'undefined' && url_params['paged'] > 1 ) {
						if ( $('.js-wpv-view-list-row').length == 1) {
							url_params['paged'] = ( url_params['paged'] - 1 );
						}
					}
					url_params['deleted'] = 1;
					navigateWithURIParams(url_params);
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

	// Duplicate View action after getting the new name

	$(document).on('click','.js-duplicate-view', function () {

		var newname = $('.js-duplicated-view-name').val();

		$('.js-duplicate-view').prop('disabled',true).addClass('button-secondary').removeClass('button-primary');
		var spinnerContainer = $('<div class="spinner ajax-loader">').insertBefore($(this)).show();

		if ( newname.length !== 0 ) {

			var data = {
				action: 'wpv_duplicate_this_view',
				id: data_view_id, // read the global data_view_id variable
				name: newname,
				wpnonce :  $(this).data('nonce')
			};
			var error = $(this).data('error');

			$.ajax({
				async:false,
				type:"POST",
				url:ajaxurl,
				data:data,
				success:function(response){
					if ( ( typeof(response) !== 'undefined' ) && ( response == data.id ) ) {
						navigateWithURIParams(decodeURIParams());
					} else if ( ( typeof(response) !== 'undefined' ) && ( response == 'error' ) ) {
						$('.js-view-duplicate-error').wpvToolsetMessage({
							text: error,
							stay: true,
							close: false,
							type: ''
						});
						spinnerContainer.remove();
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
	});
	
	// Manage the View listing screen actions dropdown

	$('.js-views-actions').on('change keyup', function() {

        data_view_id = $(this).data('view-id');
	view_listing_action_nonce = $(this).data('viewactionnonce');
	
	// If action is delete, fire the confirmation popup

        if ( $(this).val() === 'delete' ) {

            $.colorbox({
                 href: '.js-delete-view-dialog',
                 inline: true,
                 onComplete: function() {
                 }
             });

        }
        
        // If action is duplicate, fire the popup to give it a title

        else if ( $(this).val() === 'duplicate' ) {
			$('.js-view-duplicate-error .toolset-alert').remove();
            $.colorbox({
                 href: '.js-duplicate-view-dialog',
                 inline: true,
                 onComplete: function() {

                     var $input = $('.js-duplicated-view-name');
                     var $submitButton = $('.js-duplicate-view');

                     $input.focus().val('');

                     $input.keyup(function(){
                     	$('.js-view-duplicate-error .toolset-alert').remove();
                        if ( $(this).val().length !== 0 ) {
                            $submitButton
                                .prop('disabled', false)
                                .removeClass('button-secondary')
                                .addClass('button-primary');
                        } else {
                            $submitButton
                                .prop('disabled', true)
                                .removeClass('button-primary')
                                .addClass('button-secondary');
                        }
                     });
                 }
             });

	}
	
	// If action is trash, move to trash and reload the page
	
	else if ( $(this).val() === 'trash' ) {
		$(this).parents('.js-wpv-view-list-row').find('h3').append(' <div class="spinner ajax-loader"></div>');
		$('.subsubsub').append('<div class="spinner ajax-loader"></div>');
		var data = {
			action: 'wpv_view_change_status',
			id: data_view_id,
			newstatus: 'trash',
			wpnonce : view_listing_action_nonce
		};
		$.ajax({
			async:false,
			type:"POST",
			url:ajaxurl,
			data:data,
			success:function(response){
				if ( (typeof(response) !== 'undefined') && (response == data.id)) {
					var url_params = decodeURIParams();
					if ( typeof(url_params['paged']) !== 'undefined' && url_params['paged'] > 1 ) {
						if ( $('.js-wpv-view-list-row').length == 1) {
							url_params['paged'] = ( url_params['paged'] - 1 );
						}
					}
					url_params['trashed'] = response;
					navigateWithURIParams(url_params);
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
	
	else if ( $(this).val() === 'restore-from-trash' ) {
		$(this).parents('.js-wpv-view-list-row').find('h3').append(' <div class="spinner ajax-loader"></div>');
		$('.subsubsub').append('<div class="spinner ajax-loader"></div>');
		var data = {
			action: 'wpv_view_change_status',
			id: data_view_id,
			newstatus: 'publish',
			wpnonce : view_listing_action_nonce
		};
		$.ajax({
			async:false,
			type:"POST",
			url:ajaxurl,
			data:data,
			success:function(response){
				if ( (typeof(response) !== 'undefined') && (response == data.id)) {
					var url_params = decodeURIParams();
					if ( typeof(url_params['paged']) !== 'undefined' && url_params['paged'] > 1 ) {
						if ( $('.js-wpv-view-list-row').length == 1) {
							url_params['paged'] = ( url_params['paged'] - 1 );
						}
					}
					url_params['untrashed'] = 1;
					navigateWithURIParams(url_params);
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
        
        // Reset the actions dropdown

        $('.js-views-actions option').removeAttr('selected');
        $('#list_views_action_'+data_view_id).val($('#list_views_action_'+data_view_id+' option:first').val());

    });
	
	// Untrash action
	
	$(document).on('click', '.js-wpv-untrash', function(e){
		e.preventDefault();
		var spinnerContainer = $('<div class="spinner ajax-loader">').insertAfter($(this)).show();
		var data = {
			action: 'wpv_view_change_status',
			id: $(this).data('id'),
			newstatus: 'publish',
			wpnonce : $(this).data('nonce')
		};
		$.ajax({
			async:false,
			type:"POST",
			url:ajaxurl,
			data:data,
			success:function(response){
				if ( (typeof(response) !== 'undefined') && (response == data.id)) {
					var url_params = decodeURIParams();
					url_params['untrashed'] = 1;
					navigateWithURIParams(url_params);
				} else {
					spinnerContainer.remove();
					console.log( "Error: AJAX returned ", response );
				}
			},
			error: function (ajaxContext) {
				spinnerContainer.remove();
				console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() {
				
			}
		});
	});
    
    // Scan button functionality

    $(document).on('click', '.js-scan-button', function() {

        if ( !$(this).data('loading') ) {

            var data_view_id = $(this).attr('data-view-id');
            var thiz = $(this);
            var $cellParent = thiz.parent();

            thiz
                .data('loading',true)
                .attr('disabled');

            var $spinnerContainer = $('<div class="spinner ajax-loader">').insertAfter($(this)).show();
            var data = {

                    action: 'wpv_scan_view',
                    id: data_view_id,
                    wpnonce : $('#work_views_listing').attr('value')
            };

            var loadPosts = $.post( ajaxurl , data, function(responseData) {


                if ( (typeof(responseData) !== 'undefined') && responseData !== null) {

                    thiz
                        .data('loading',false)
                        .remove();
                    $spinnerContainer.remove();

                    var $postsList = $('<ul class="posts-list">');
                    $postsList.appendTo($cellParent);

                    $.each(responseData,function(index, value){
                        $('<li><a target="_blank" href="'+value.link+'">'+value.post_title+'</a></li>').appendTo($postsList);
                    });


                } else {
                    thiz.parent().find('.js-nothing-message').show();
                    thiz.remove();

                }

             }, "json");

            loadPosts.fail(function(){ // function executed when ajax call fails
                //
            });

            loadPosts.always(function(){ // function executed when ajax request us complete. Makes no difference if success or not.
                thiz
                    .data('loading',false)
                    .removeAttr('disabled');
                $spinnerContainer.remove();
            });

        }

    });

    // Search function

    $('#posts-filter').submit(function(e) {
	    e.preventDefault();
	    var url_params = decodeURIParams($(this).serialize());
	    if (typeof(url_params['search']) !== 'undefined' && url_params['search'] == '') {
		    url_params['search'] = null;
	    }
	    navigateWithURIParams(url_params);
        return false;
    });

    // Change pagination items per page

    $(document).on('change', '.js-items-per-page', function() {
	    var url_params = decodeURIParams('paged=1&items_per_page=' + $(this).val());
	    navigateWithURIParams(url_params);
    });

    $(document).on('click', '.js-wpv-display-all-items', function(e){
	    e.preventDefault();
	    var url_params = decodeURIParams('paged=1&items_per_page=-1');
	    navigateWithURIParams(url_params);
    });

    $(document).on('click', '.js-wpv-display-default-items', function(e){
	    e.preventDefault();
	    var url_params = decodeURIParams('paged=1&items_per_page=20');
	    navigateWithURIParams(url_params);
    });

    // add new View - open popup

    $(document).on('click', '.js-wpv-views-add-new-top, .js-wpv-views-add-new, .js-wpv-views-add-first', function(e) {
	    e.preventDefault();
	    $.colorbox({
		    inline:true,
		href: '.js-create-view-form-dialog',
		open:true,
		onComplete: function() {
			$('.js-create-new-view').prop('disabled', true).removeClass('button-primary').addClass('button-secondary');
			if (0 < $('input.js-view-purpose:checked').length) {
				$('input.js-view-purpose:checked').prop('checked', false);
				$('.js-create-view-form-dialog').find('.toolset-alert').remove();
			}
			//	thiz.prop('disabled',true);
		},
		onClosed : function() {
			//	thiz.prop('disabled',false);
		}
	    })
    });
    
	// Add new View - popup behaviour

	$(document).on('change keyup input cut paste', '.js-view-purpose, .js-new-post_title', function(){
	    $('.js-create-view-form-dialog').find('.toolset-alert').remove();
	    if ('' != $('input.js-new-post_title').val() && 0 < $('input.js-view-purpose:checked').length) {
		    $('.js-create-new-view').prop('disabled', false).addClass('button-primary').removeClass('button-secondary');
	    } else {
		    $('.js-create-new-view').prop('disabled', true).removeClass('button-primary').addClass('button-secondary');
	    }
	    if ('' == $('.js-new-post_title').val()) {
		    $('.js-new-post_title').focus().parent().wpvToolsetMessage({
			    text:$('input.js-new-post_title').data('highlight'),
			    type:'info',
			    stay: true
		    });
	    }
	});
	
	// Add new View - action

	$(document).on('click', '.js-create-new-view', function(e){
		e.preventDefault();
		$('.js-create-new-view').addClass('button-secondary').removeClass('button-primary');
		$thiz = $(this);
		var spinnerContainer = $('<div class="spinner ajax-loader">').insertAfter($(this)).show();
		var title = $('.js-new-post_title').val();
		var purpose = $('input.js-view-purpose:checked').val();
		var data = {
			action: 'wpv_create_view',
			title: title,
			purpose: purpose,
			wpnonce : $('#wp_nonce_create_view').attr('value')
		};
		$thiz.prop('disabled',true);
		$.post(ajaxurl, data, function(response) {
			if ( (typeof(response) !== 'undefined') ) {
				temp_res = jQuery.parseJSON(response);

				if ( temp_res.error == 'error' ){
					console.log(temp_res.error_message);
					$('.js-error-container').wpvToolsetMessage({
						text:temp_res.error_message,
						type: '',
						stay: true
					});
					$thiz.prop('disabled',false);
					spinnerContainer.remove();
					$('.js-create-new-view').addClass('button-primary').removeClass('button-secondary');
					return false;
				}
				if (response != 0) {
					var url = $('.js-view-new-redirect').val();
					$(location).attr('href',url + response);
				} else {
					console.log( "Error: WordPress AJAX returned ", response );
				}
			} else {
				$('<span class="updated">error</span>').insertAfter($('.js-create-new-view')).hide().fadeIn(500).delay(1500).fadeOut(500, function(){
					$(this).remove();
				});
				console.log( "Error: AJAX returned ", response );
			}
		})
		.fail(function(jqXHR, textStatus, errorThrown){
			if ($('.js-create-view').parent().find('.unsaved').length < 1) {
				$('<span class="message unsaved"><i class="icon-warning-sign"></i> error</span>').insertAfter($('.js-create-new-view')).show();
			}
			console.log( "Error: ", textStatus, errorThrown );
		})
		.always(function() {

		});
	});

	// Redirection functions for search, delete and duplicate

	function decodeURIParams(query) {
		if (query == null)
			query = window.location.search;
		if (query[0] == '?')
			query = query.substring(1);

		var params = query.split('&');
		var result = {};
		for (var i = 0; i < params.length; i++) {
			var param = params[i];
			var pos = param.indexOf('=');
			if (pos >= 0) {
				var key = decodeURIComponent(param.substring(0, pos));
				var val = decodeURIComponent(param.substring(pos + 1));
				result[key] = val;
			} else {
				var key = decodeURIComponent(param);
				result[key] = true;
			}
		}
		result['untrashed'] = null;
		result['trashed'] = null;
		result['deleted'] = null;
		return result;
	}

	function encodeURIParams(params, addQuestionMark) {
		var pairs = [];
		for (var key in params) if (params.hasOwnProperty(key)) {
			var value = params[key];
			if (value != null) /* matches null and undefined */ {
				pairs.push(encodeURIComponent(key) + '=' + encodeURIComponent(value))
			}
		}
		if (pairs.length == 0)
			return '';
		return (addQuestionMark ? '?' : '') + pairs.join('&');
	}

	function navigateWithURIParams(newParams) {
		window.location.search = encodeURIParams($.extend(decodeURIParams(), newParams), true);
	}

});
