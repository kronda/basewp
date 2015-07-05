jQuery(document).ready(function($)  {

    // Store the view ID
    var data_view_id = null;


	/**
	 * Duplicate View action after getting the new name.
	 *
	 * @since unknown
	 */ 
	$(document).on('click','.js-duplicate-view', function () {

		var newName = $('.js-wpv-duplicated-title').val();
		var nonce = $(this).data('nonce');
		var viewId = $(this).data('view-id');

		disablePrimaryButton( $('.js-duplicate-view') );
		showSpinnerBefore( $(this) );

		if ( newName.length !== 0 ) {

			var data = {
				action: 'wpv_duplicate_this_view',
				id: viewId, 
				name: newName,
				wpnonce : nonce
			};
			
			var error = $(this).data('error');

			$.ajax({
				async: false,
				type: "POST",
				url: ajaxurl,
				data: data,
				success: function( response ) {
					
					if ( ( typeof(response) !== 'undefined' ) && ( response == data.id ) ) {
						
						navigateWithURIParams( decodeURIParams() );
						
					} else if ( ( typeof(response) !== 'undefined' ) && ( response == 'error' ) ) {
						
						$('.js-wpv-duplicate-error-container').wpvToolsetMessage({
							text: error,
							stay: true,
							close: false,
							type: ''
						});
						
						hideSpinner();
						
					} else {
						console.log( "Error: AJAX returned ", response );
					}
				},
				error: function (ajaxContext) {
					console.log( "Error: ", ajaxContext.responseText );
				}
			});
		}
	});


	/**
	 * Delete action. Show the confirmation popup.
	 *
	 * @since unknown
	 */
	$('.js-views-actions-delete').on('click', function(e) {
		e.preventDefault();

        var viewId = $(this).data('view-id');
		var nonce = $(this).data('viewactionnonce');

		// Act as if this was a bulk action.
		trashdelViewsConfirmation( [ viewId ], nonce, 'delete' );
	});


	/**
	 * Duplicate action. Show the popup to give it a title.
	 *
	 * @since unknown
	 */
	$('.js-views-actions-duplicate').on('click', function(e) {
		e.preventDefault();

        var viewID = $(this).data('view-id');
        var originalTitle = $(this).data('view-title');

		$('.js-wpv-duplicate-error-container .toolset-alert').remove();

		$.colorbox({
			href: '.js-duplicate-view-dialog',
			inline: true,
			onComplete: function() {

				// Show name of the original
				$('.js-duplicate-origin-title').append( originalTitle );


				// Store View ID in a confirm button attribute
				$('.js-duplicate-view').data( 'view-id', viewID );

				$('.js-wpv-duplicated-title').focus().val('');

				$('.js-wpv-duplicated-title').keyup(function() {
					$('.js-wpv-duplicate-error-container .toolset-alert').remove();
					if ( $(this).val().length !== 0 ) {
						enablePrimaryButton( $('.js-duplicate-view') );
					} else {
						disablePrimaryButton( $('.js-duplicate-view') );
					}
				});
				
			}
		});
	});


	/**
	 * Trash action. Move to trash and reload the page
	 *
	 * @since unknown
	 */
	$('.js-views-actions-trash').on('click', function(e) {
		e.preventDefault();

        var viewId = $(this).data('view-id');
		var nonce = $(this).data('viewactionnonce');

		// Act as if this was a bulk action.
		trashdelViewsConfirmation( [ viewId ], nonce, 'trash' )
	});


	/** "restore-from-trash" action. */
	$('.js-views-actions-restore-from-trash').on('click', function(e) {
		e.preventDefault();
        data_view_id = $(this).data('view-id');
		view_listing_action_nonce = $(this).data('viewactionnonce');

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
					url_params['paged'] = updatePagedParameter( url_params, 1 );
					url_params['untrashed'] = 1;
					navigateWithURIParams(url_params);
				} else {
					console.log( "Error: AJAX returned ", response );
				}
			},
			error: function (ajaxContext) {
				console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() { }
		});
	});


	/**
	 * Undo "trash" action when user clicks on the Undo link.
	 *
	 * @see wpv_admin_view_listing_message_undo() in wpv-views-listing-page.php
	 */ 
	$(document).on('click', '.js-wpv-untrash', function(e) {
		e.preventDefault();
		showSpinnerAfter( $(this) );

		var nonce = $(this).data( 'nonce' );
		var viewIDs = decodeURIComponent( $(this).data( 'ids' ) ).split( ',' );

		untrashViews( viewIDs, nonce );
	});


	/**
	 * Bulk action.
	 *
	 * Fires when user hits the Apply button near bulk action select field.
	 *
	 * @since 1.7
	 */
	$('.js-wpv-views-listing-bulk-action-submit').on('click', function(e) {
		e.preventDefault();

		showSpinner();

		// Get an array of checked View IDs.
		var checkedViews = $('.wpv-admin-listing-col-bulkactions input:checkbox:checked').map(function() {
			var value = $(this).val();
			// Filter out values of checkboxes in table header and footer rows.
			if($.isNumeric(value)) {
				return value;
			}
		}).get();

		// If there are no items selected, do nothing.
		if( checkedViews.length == 0 ) {
			hideSpinner();
			return;
		}

		// nonce
		var nonce = $(this).data('viewactionnonce');

		// Get a position. That's important to determine which select field is relevant for us.
		var selectPosition = $(this).data('position');

		// Launch appropriate bulk action
		var action = $('.js-wpv-views-listing-bulk-action-select.position-' + selectPosition).val();
		switch(action) {
			case 'trash':
				trashdelViewsConfirmation( checkedViews, nonce, 'trash' );
				break;
			case 'restore-from-trash':
				untrashViews( checkedViews, nonce );
				break;
			case 'delete':
				trashdelViewsConfirmation( checkedViews, nonce, 'delete' );
				break;
			default:
				// do nothing
				hideSpinner();
				return;
		}
	});


	/**
	 * Show a popup with confirmation message and a table of Views to be deleted or trashed, each View
	 * with a "Scan" button with the same function as in the listing (see .js-scan-button).
	 *
	 * Views are deleted after clicking on .js-bulk-remove-view-permanent/trashed after clicking
	 * on .js-bulk-confirm-view-trash.
	 *
	 * @param string view_action Type of action user should confirm. Can be 'delete' or 'trash'.
	 *
	 * @since 1.7
	 */
	function trashdelViewsConfirmation( viewIDs, nonce, view_action ) {
	
		// Do AJAX call to generate popup code
		var data = {
			action: 'wpv_view_bulk_trashdel_render_popup',
			ids: viewIDs,
			wpnonce : nonce,
			view_action: view_action
		};

		$.ajax({
			async: false,
			type: "POST",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				// Show a colorbox with recieved content.
				$.colorbox({
					html: response,
					onComplete: function() { }
				});
				// We're waiting on user input - hide the spinner shown at the start of bulk action
				hideSpinner();
			},
			error: function( ajaxContext ) {
				console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() { }
		});
	}


	/**
	 * Permanently delete given Views and redirect to current page with 'deleted' message.
	 *
	 * @since 1.7
	 */
	$(document).on( 'click', '.js-bulk-remove-view-permanent', function(e) {
		e.preventDefault();
		// Disable "Delete all" button
		$(this).prop( 'disabled', true ).addClass( 'button-secondary' ).removeClass( 'button-primary' );
		showSpinnerAfter( $(this) );
		
		var viewIDs = decodeURIComponent( $(this).data( 'view-ids' ) ).split( ',' );
		var nonce = $(this).data( 'nonce' );

		var data = {
			action: 'wpv_bulk_delete_views_permanent',
			ids: viewIDs,
			wpnonce : nonce
		};
		$.ajax({
			async: false,
			type: "POST",
			url: ajaxurl,
			data: data,
			success: function( response ){
				// response == 1 indicates success
				if ( ( typeof( response ) !== 'undefined' ) && ( 1 == response ) ) {
					// reload the page with "deleted" message
					var url_params = decodeURIParams();
					var affectedItemCount = viewIDs.length;
					url_params['paged'] = updatePagedParameter( url_params, affectedItemCount );
					url_params['deleted'] = affectedItemCount;
					navigateWithURIParams( url_params );
				} else {
					console.log( "Error: AJAX returned ", response );
				}
			},
			error: function (ajaxContext) {
				console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() {	}
		});
        
	});


	/**
	 * Trash given Views and redirect to current page with 'trashed' message.
	 *
	 * @since 1.8
	 */
	$(document).on( 'click', '.js-bulk-confirm-view-trash', function(e) {
		e.preventDefault();
		
		disablePrimaryButton( $(this) );
		showSpinnerAfter( $(this) );
		
		var viewIDs = decodeURIComponent( $(this).data( 'view-ids' ) ).split( ',' );
		var nonce = $(this).data( 'nonce' );

		trashViews( viewIDs, nonce );        
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
	    if (typeof(url_params['s']) !== 'undefined' && url_params['s'] == '') {
		    url_params['s'] = null;
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
		var thiz_message_container = $( this ).parents( '.js-create-view-form-dialog' ).find( '.js-wpv-error-container' );
	    if ('' != $('input.js-new-post_title').val() && 0 < $('input.js-view-purpose:checked').length) {
		    $('.js-create-new-view').prop('disabled', false).addClass('button-primary').removeClass('button-secondary');
	    } else {
		    $('.js-create-new-view').prop('disabled', true).removeClass('button-primary').addClass('button-secondary');
	    }
	    if ('' == $('.js-new-post_title').val()) {
			thiz_message_container
				.wpvToolsetMessage({
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
		var $thiz = $(this),
		thiz_message_container = $thiz.parents( '.js-create-view-form-dialog' ).find( '.js-wpv-error-container' );
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
					thiz_message_container
						.wpvToolsetMessage({
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

});
