jQuery(document).ready(function($) {

    $('.js-list-views-action option').removeAttr('selected');
    
    /*
     * Sorting by usage
     * 
     */
    
    // Create Archive for loop popup
    
    $(document).on('click', '.js-create-view-for-archive', function(e) {
	    e.preventDefault();
	    var data = {
		    wpnonce: $('#wpv_wp_archive_arrange_usage').val(),
		       action: 'wpv_create_usage_archive_view_popup',
		       for_whom: $(this).data('forwhom')
	    }
	    
	    $.colorbox({
		    href: ajaxurl,
		inline : false,
		data: data,
		onComplete: function() {
			$('.wpv-dialog-content').append('<div class="js-error-container"></div>');
		}
	    });
	    return false;
    });
    
    // Create Archive for loop action
    
    $(document).on('click', '.js-wpv-add-wp-archive-for-loop', function(e){
	    e.preventDefault();
	    $('.js-wpv-add-archive').addClass('button-secondary').removeClass('button-primary');
	    var spinnerContainer = $('<div class="spinner ajax-loader">').insertBefore($(this)).show();
	    var error = $(this).data('error');
	    $('.toolset-alert').remove();
	    $(this).prop('disabled',true).addClass('button-secondary').removeClass('button-primary');
	    var data = {
		    action: 'wpv_create_usage_archive_view',
		    form: $('#wpv-add-wp-archive-for-loop-form').serialize(),
			    wpnonce : $('#work_views_listing').attr('value')
	    };
	    $.ajax({
		async:false,
		type:"POST",
		url:ajaxurl,
		data:data,
		success:function(response){
			if ( ( typeof(response) !== 'undefined' ) && ( response == 'error' ) ) {
				$('.js-wp-archive-create-error').wpvToolsetMessage({
					text: error,
					stay: true,
					close: false,
					type: ''
				});
				spinnerContainer.remove();
			} else if ( ( typeof(response) !== 'undefined' ) && ( typeof(response) == 'string' ) ) {
				$(location).attr('href', $('.js-wpv-add-wp-archive-for-loop').data('url') + response );
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
    
    // Change usage select popup

    $(document).on('change','.js-list-views-usage-action', function () {
        var data_view_id = $(this).data('view-id');

        if($(this).val() === 'change_usage') {
            var thiz = $(this);
            var data = {
                action: 'wpv_show_views_for_loop',
                id: data_view_id,
		wpnonce : $('#wpv_wp_archive_arrange_usage').attr('value')
            };
            $.colorbox({
                href: ajaxurl,
                data: data,
                onComplete: function() {
               //     thiz.prop('disabled',true);
                },
                onClosed : function() {
			
                }
            });
        }
        
        $('.js-list-views-usage-action').val('0');

    });
    
    // Change usage action

	$(document).on('click','.js-update-archive-for-loop', function (e) {
		e.preventDefault();
		var thiz = $(this);
		thiz.attr('disabled', 'true').addClass('button-secondary').removeClass('button-primary');
		var spinnerContainer = $('<div class="spinner ajax-loader">').insertBefore($(this)).show();
		var data = {
			action: 'wpv_update_archive_for_view',
			selected: $('input[name=wpv-view-loop-archive]:checked', '#wpv-archive-view-form-for-loop').val(),
			loop: $('[name=wpv-archive-loop-key]').val(),
			wpnonce : $('#wpv_wp_archive_arrange_usage').attr('value')
		};
		
		$.ajax({
			async:false,
			type:"POST",
			url:ajaxurl,
			data:data,
			success:function(response){
				if ( ( typeof(response) !== 'undefined' ) && ( response == 'ok' ) ) {
					navigateWithURIParams(decodeURIParams());
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
	
	/*
	 * Sort by name
	 * 
	 */
	
	// Search box action
	
	$('#posts-filter').submit(function(e) {
		e.preventDefault();
		var url_params = decodeURIParams($(this).serialize());
		if (typeof(url_params['search']) !== 'undefined' && url_params['search'] == '') {
			url_params['search'] = null;
		}
		navigateWithURIParams(url_params);
		
		return false;
	});
	
	// Change actions select popup TODO
	
	$(document).on('change','.js-list-views-action', function () {
		var data_view_id = $(this).data('view-id');
		var view_listing_action_nonce = $(this).data('viewactionnonce');
		
		if($(this).val() === 'delete') {
			$.colorbox({
				html: '<div class="wpv-dialog">'+
				'<div class="wpv-dialog-header">'+
				'<h2>Delete Archive</h2>'+
				'</div>'+
				'<div class="wpv-dialog-content"><p>Are you sure want delete this Archive?</p></div>'+
				'<div class="wpv-dialog-footer">'+
				'<button class="button js-dialog-close">Cancel</button> '+
				'<button class="button button-primary js-remove-archive-permanent" data-id="'+data_view_id+'">Delete</button>'+
				'</div></div>',
	      //inline : false,
	      onComplete: function() {
		      
	      },
	      onClosed : function() {
		      
	      }
			});
	}
	
	if($(this).val() === 'change') {
		var thiz = $(this);
		var data = {
			action: 'wpv_archive_change_usage_popup',
    id: data_view_id,
    wpnonce : $('#work_views_listing').attr('value')
		};
		$.colorbox({
			href: ajaxurl,
	     data: data,
	     onComplete: function() {
		     //    thiz.prop('disabled',true);
	     },
	     onClosed : function() {
		     
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

	$('.js-list-views-action').val('0');
	$('#list_views_action_'+data_view_id).val($('#list_views_action_'+data_view_id+' option:first').val());
	
	$('.js-list-views-action:selected').removeAttr('selected');

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

	// Delete action
	
	$(document).on('click','.js-remove-archive-permanent', function (e) {
		e.preventDefault();
		var spinnerContainer = $('<div class="spinner ajax-loader">').insertBefore($(this)).show();
		$(this).prop('disabled', true).addClass('button-secondary').removeClass('button-primary');
		var url_params = decodeURIParams(),
		       url_page = null;
		if ( typeof(url_params['paged']) !== 'undefined' && url_params['paged'] > 1 ) {
			if ( $('.js-wpv-view-list-row').length == 1) {
				url_page = 'paged=' + ( url_params['paged'] - 1 );
			}
		}
		var data_view_id = $(this).data('id');
		var data = {
			action: 'wpv_delete_view_permanent',
			id: data_view_id,
			wpnonce : $('#wpv_remove_view_permanent_nonce').attr('value')
                };

		$.ajax({
			async:false,
			type:"POST",
			url:ajaxurl,
			data:data,
			success:function(response){
				if ( (typeof(response) !== 'undefined') && (response == data.id)) {
					navigateWithURIParams(decodeURIParams(url_page));
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
	
	// Change usage action
	
	$(document).on('click', '.js-wpv-update-archive', function(e){
		e.preventDefault();
		$(this).attr('disabled', true).addClass('button-secondary').removeClass('button-primary');
		var spinnerContainer = $('<div class="spinner ajax-loader">').insertBefore($(this)).show();
		var error = $(this).data('error');
		$('.toolset-alert').remove();
		var data = {
			action: 'wpv_archive_change_usage',
			form: $('#wpv-create-archive-view-form').serialize(),
			wpnonce : $('#work_views_listing').attr('value')
		};
		$.ajax({
			async:false,
			type:"POST",
			url:ajaxurl,
			data:data,
			success:function(response){
				if ( ( typeof(response) !== 'undefined' ) && ( response == 'ok' ) ) {
					navigateWithURIParams(decodeURIParams());
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

	/*
	 * Common
	 * 
	 */
	
	// Create Archive up & down popup

    $(document).on('click', '.js-wpv-views-archive-add-new-top', function(e) {
	    e.preventDefault();
        $('.js-wpv-views-archive-add-new').click();
        return false;
    });

    $(document).on('click', '.js-wpv-views-archive-add-new', function(e) {
	    e.preventDefault();
        var $thiz = $(this);
        var data = {
            wpnonce: $('#work_views_listing').val()
        }

        $.colorbox({
            href: $thiz.data('target'),
            inline : false,
            data: data,
            onComplete: function() {
		    if ( $('.js-wpv-add-archive').prop('disabled') ) {
			    $('.js-wpv-add-archive').addClass('button-secondary').removeClass('button-primary');
		    }
                if ( !$thiz.data('disabled') ){
                    $('.js-create-new-temlate').prop('disabled',true).addClass('button-secondary').removeClass('button-primary');
                }
            }
        });
        return false;
    });
    
    // Creation of first Archive TODO merge with the above
    
    $(document).on('click', '.js-wpv-views-archive-create-new', function(e) {
	    e.preventDefault();
	    var data = {
		    wpnonce : $('#work_views_listing').attr('value')
	    }
	    
	    $.colorbox({
		    href: $(this).data('target'),
				       inline: false,
			 data: data,
			 onComplete: function() {
				 if ( $('.js-wpv-add-archive').prop('disabled') ) {
					 $('.js-wpv-add-archive').addClass('button-secondary').removeClass('button-primary');
				 }
			 }
	    });
	 return false;
    });
    
    // Create Archive up, down, first action
    
    $(document).on('click', '.js-wpv-add-archive', function(e){
	    e.preventDefault();
	    $(this).attr('disabled', true).addClass('button-secondary').removeClass('button-primary');
	    var spinnerContainer = $('<div class="spinner ajax-loader">').insertBefore($(this)).show();
	    var error = $(this).data('error');
	    $('.toolset-alert').remove();
	    var data = {
			action: 'wpv_create_archive_view',
			form: $('#wpv-create-archive-view-form').serialize(),
			wpnonce : $('#work_views_listing').attr('value')
	    };
	    $.ajax({
			async:false,
			type:"POST",
			url:ajaxurl,
			data:data,
			success:function(response){
				if ( ( typeof(response) !== 'undefined' ) && ( response == 'error' ) ) {
					$('.js-error-container').wpvToolsetMessage({
						text: error,
						stay: true,
						close: false,
						type: ''
					});
					spinnerContainer.remove();
				} else if ( ( typeof(response) !== 'undefined' ) && ( typeof(response) == 'string' ) ) {
					$(location).attr('href', $('.js-wpv-add-archive').data('url') + response );
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

    // Controls the buttons in WP Archive creation popup (for usage arrange maybe shared)
    
    $(document).on('input','.js-wpv-new-archive-name', function(e){
        $('.toolset-alert').remove();
	if ($(this).val() === ""){
			$('.js-wpv-add-archive').prop('disabled',true).addClass('button-secondary').removeClass('button-primary');
			$('.js-wpv-add-wp-archive-for-loop').prop('disabled',true).addClass('button-secondary').removeClass('button-primary');
		}
        else{
			$('.js-wpv-add-archive').prop('disabled',false).removeClass('button-secondary').addClass('button-primary');
			$('.js-wpv-add-wp-archive-for-loop').prop('disabled',false).removeClass('button-secondary').addClass('button-primary');
		}
    });

    $(document).on('keypress','.js-wpv-new-archive-name', function(event){
        if ( event.which == 13 ) {
            event.preventDefault();
        }
    });
    
    // Change pagination items per page
    
    $(document).on('change', '.js-items-per-page', function() {
	    navigateWithURIParams(decodeURIParams('paged=1&items_per_page=' + $(this).val()));
    });
    
    $(document).on('click', '.js-wpv-display-all-items', function(e){
	    e.preventDefault();
	    navigateWithURIParams(decodeURIParams('paged=1&items_per_page=-1'));
    });
    
    $(document).on('click', '.js-wpv-display-default-items', function(e){
	    e.preventDefault();
	    navigateWithURIParams(decodeURIParams('paged=1&items_per_page=20'));
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