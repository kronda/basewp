jQuery(document).ready(function($) {

	// @todo transform this to OOP model
	
    $('.js-list-views-action option').removeAttr('selected');

    /*
     * Sorting by usage
     *
     */

    /**
     * Create Archive for loop popup.
     *
     * @since unknown
     */ 
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


    /**
     * Create Archive for loop action.
     *
     * @since unknown
     */ 
    $(document).on('click', '.js-wpv-add-wp-archive-for-loop', function(e){
	    e.preventDefault();
	    $('.js-wpv-add-archive').addClass('button-secondary').removeClass('button-primary');

	    showSpinnerBefore( $(this) );
	    
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
				$('.js-wp-archive-create-error')
					.wpvToolsetMessage({
						text: error,
						stay: true,
						close: false,
						type: ''
					});
				hideSpinner();
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


    /**
     * This happens when user clicks on the "Change WordPress Archive" action link on the "listing by usage" page.
     *
     * Calls wpv_show_views_for_loop by AJAX and shows result of the call as a popup. When user confirms it,
     * they click on 'js-update-archive-for-loop'.
     */
    $(document).on('click','.js-list-views-usage-action-change-usage', function (e) {
        e.preventDefault();
        // This is actually a slug of the loop.
		var data_view_id = $(this).data('view-id');

		var data = {
			action: 'wpv_show_views_for_loop',
			id: data_view_id,
			wpnonce : $('#wpv_wp_archive_arrange_usage').attr('value')
		};
		$.colorbox({
			href: ajaxurl,
			data: data,
			onComplete: function() { },
			onClosed : function() { }
		});
    });


	/**
	 * This happens when user confirms updating assigned WordPress Archive for a loop.
	 */ 
	$(document).on('click','.js-update-archive-for-loop', function (e) {
		e.preventDefault();
		disablePrimaryButton( $(this) );
		showSpinnerBefore( $(this) );
		
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

	/* ************************************************************************** *\
	 * Sort by name
	\* ************************************************************************** */


	/**
	 * Search box action.
	 */ 
	$('#posts-filter').submit(function(e) {
		e.preventDefault();
		var url_params = decodeURIParams($(this).serialize());
		if (typeof(url_params['s']) !== 'undefined' && url_params['s'] == '') {
			url_params['s'] = null;
		}
		navigateWithURIParams(url_params);
		return false;
	});


	/**
	 * Fires when user clicks on "delete" action.
	 */
	$(document).on('click','.js-list-views-action-delete', function (e) {
		e.preventDefault();
		var data_view_id = $(this).data('view-id');
		var view_listing_action_nonce = $(this).data('viewactionnonce');

		// Show confirmation dialog.
		// confirm --> js-remove-archive-permanent click
		$.colorbox({
			html:
				'<div class="wpv-dialog">'+
					'<div class="wpv-dialog-header">'+
						'<h2>Delete Archive</h2>'+
					'</div>'+
					'<div class="wpv-dialog-content"><p>Are you sure want delete this Archive?</p></div>'+
					'<div class="wpv-dialog-footer">'+
						'<button class="button js-dialog-close">Cancel</button> '+
						'<button class="button button-primary js-remove-archive-permanent" data-id="'+data_view_id+'">Delete</button>'+
					'</div>'+
				'</div>',
			onComplete: function() { },
			onClosed : function() { }
		});
	});


	/**
	 * Fires when user clicks on "change" action.
	 */
	$(document).on('click','.js-list-views-action-change', function (e) {
		e.preventDefault();
		var data_view_id = $(this).data('view-id');
		var view_listing_action_nonce = $(this).data('viewactionnonce');

		var data = {
			action: 'wpv_archive_change_usage_popup',
			id: data_view_id,
			wpnonce : $('#work_views_listing').attr('value')
		};
		$.colorbox({
			href: ajaxurl,
			data: data,
			onComplete: function() { },
			onClosed : function() { }
		});
	});


	/**
	 * Fires when user clicks on "trash" action.
	 *
	 * @since unknown
	 */
	$(document).on('click','.js-list-views-action-trash', function (e) {
		e.preventDefault();
		showSpinner();

		var wpaId = $(this).data( 'view-id' );
		var nonce = $(this).data( 'viewactionnonce' );

		// Just act as if this was a bulk action.
		maybeTrashWPAs( [ wpaId ], nonce );
	});


	/**
	 * Fires when user clicks on "restore-from-trash" action.
	 */
	$(document).on('click','.js-list-views-action-restore-from-trash', function (e) {
		e.preventDefault();
		var data_view_id = $(this).data('view-id');
		var view_listing_action_nonce = $(this).data('viewactionnonce');
		
		showSpinner();
		
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
	 * @since unknown
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
	 * Delete action
	 */ 
	$(document).on('click','.js-remove-archive-permanent', function (e) {
		e.preventDefault();
		showSpinnerBefore( $(this) );
		$(this).prop('disabled', true).addClass('button-secondary').removeClass('button-primary');
		

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
					var url_params = decodeURIParams();
					url_params['paged'] = updatePagedParameter( url_params, 1 );
					url_params['deleted'] = 1;
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
	 * Change usage action
	 */ 
	$(document).on('click', '.js-wpv-update-archive', function(e){
		e.preventDefault();
		$(this).attr('disabled', true).addClass('button-secondary').removeClass('button-primary');
		showSpinnerBefore( $(this) );
		
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


	/**
	 * Bulk action.
	 *
	 * Fires when user hits the Apply button near bulk action select field.
	 *
	 * @since 1.7
	 */
	$('.js-wpv-wpa-listing-bulk-action-submit').on('click', function(e) {
		e.preventDefault();

		showSpinner();

		// Get an array of checked WPA IDs.
		var checkedWPAs = $('.wpv-admin-listing-col-bulkactions input:checkbox:checked').map(function() {
			var value = $(this).val();
			// Filter out values of checkboxes in table header and footer rows.
			if($.isNumeric(value)) {
				return value;
			}
		}).get();

		// If there are no items selected, do nothing.
		if( checkedWPAs.length == 0 ) {
			hideSpinner();
			return;
		}

		// nonce
		var nonce = $(this).data('viewactionnonce');

		// Get a position. That's important to determine which select field is relevant for us.
		var selectPosition = $(this).data('position');

		// Launch appropriate bulk action
		var action = $('.js-wpv-wpa-listing-bulk-action-select.position-' + selectPosition).val();
		switch(action) {
			case 'trash':
				maybeTrashWPAs( checkedWPAs, nonce );
				break;
			case 'restore-from-trash':
				untrashViews( checkedWPAs, nonce );
				break;
			case 'delete':
				deleteWPAConfirmation( checkedWPAs, nonce );
				break;
			default:
				// do nothing
				hideSpinner();
				return;
		}
	});


	/**
	 * Check whether WPAs are in use. If they are, show a confirmation before trashing them, otherwise trash them right away.
	 *
	 * @param array wpaIDs Array of WPA IDs that should be trashed.
	 * @param string nonce A valid nonce for the trashing action.
	 * 
	 * @since 1.7
	 */
	function maybeTrashWPAs( wpaIDs, nonce ) {

		var data = {
			action: 'wpv_archive_check_usage',
			ids: wpaIDs,
			wpnonce : nonce
		};

		$.ajax({
			async: false,
			type: "POST",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				response = JSON.parse( response );
				if( ( 'used_wpa_ids' in response ) && ( 'colorbox_html' in response ) ) {
					var usedWPAs = response['used_wpa_ids'];
					if( usedWPAs.length == 0 ) {
						// no WPAs are used, we can trash them right away
						trashViews( wpaIDs, nonce, true );
					} else {
						// some WPAs are used, show prepared colorbox
						$.colorbox({
							html: response['colorbox_html'],
							onComplete: function() { }
						});
						// We're waiting on user input - hide the spinner shown at the start of bulk action
						hideSpinner();
					}
				} else {
					console.log( "Error: unexpected output: ", response );
				}
			},
			error: function( ajaxContext ) {
				console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() { }
		});
	}


	/**
	 * Trash archives (and unassign them from archive loops) after confirmation.
	 *
	 * @since 1.7
	 *
	 * @todo comment
	 */ 
	$(document).on( 'click', '.js-bulk-trash-archives-confirm', function(e) {
		e.preventDefault();
		// Disable "Trash all" button
		$(this).prop( 'disabled', true ).addClass( 'button-secondary' ).removeClass( 'button-primary' );
		showSpinnerAfter( $(this) );
		
		var wpaIDs = decodeURIComponent( $(this).data( 'archive-ids' ) ).split( ',' );
		var nonce = $(this).data( 'nonce' );

		trashViews( wpaIDs, nonce, true );
	});


	/**
	 * Show a popup with confirmation message. 
	 *
	 * Archives are deleted after clicking on .js-bulk-remove-archives-permanent.
	 *
	 * @since 1.7
	 */
	function deleteWPAConfirmation( wpaIDs, nonce ) {
	
		// Do AJAX call to generate popup code
		var data = {
			action: 'wpv_archive_bulk_delete_render_popup',
			ids: wpaIDs,
			wpnonce : nonce
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
	 * Permanently delete given Archives and redirect to current page with 'deleted' message.
	 *
	 * @since 1.7
	 */
	$(document).on( 'click', '.js-bulk-remove-archives-permanent', function(e) {
		e.preventDefault();
		// Disable "Delete all" button
		$(this).prop( 'disabled', true ).addClass( 'button-secondary' ).removeClass( 'button-primary' );
		showSpinnerAfter( $(this) );
		
		var wpaIDs = decodeURIComponent( $(this).data( 'archive-ids' ) ).split( ',' );
		var nonce = $(this).data( 'nonce' );

		var data = {
			action: 'wpv_bulk_delete_views_permanent',
			ids: wpaIDs,
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
					var affectedItemCount = wpaIDs.length;
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

	

	/* ****************************************************************************\
	 * Common
	\* ****************************************************************************/

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

    $( document ).on( 'click', '.js-wpv-add-archive', function( e ) {
	    e.preventDefault();
		var thiz = $( this ),
		thiz_container = thiz.parents( '.js-wpv-dialog-wpa-manager' ),
		thiz_message_container = thiz_container.find( '.js-wpv-error-container' ),
		error_message = thiz.data('error');
	    thiz.attr('disabled', true).addClass('button-secondary').removeClass('button-primary');
	    showSpinnerBefore( $(this) );
	    thiz_container.find('.toolset-alert').remove();
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
			success: function( response ) {
				if ( ( typeof( response ) !== 'undefined' ) && ( response == 'error' ) ) {
					thiz_message_container
						.wpvToolsetMessage({
							text: error_message,
							stay: true,
							close: false,
							type: ''
						});
					hideSpinner();
				} else if ( ( typeof(response) !== 'undefined' ) && ( typeof( response ) == 'string' ) ) {
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

});
