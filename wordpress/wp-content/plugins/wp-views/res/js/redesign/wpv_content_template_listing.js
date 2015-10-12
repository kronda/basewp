var WPViews = WPViews || {};

WPViews.CTListingScreen = function( $ ) {
	
	var self = this;
	
	self.shortcodeDialogSpinnerContent = $(
        '<div style="min-height: 150px;">' +
            '<div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; ">' +
                '<div class="wpv-spinner ajax-loader"></div>' +
                '<p>' + ct_listing_texts.loading_options + '</p>' +
            '</div>' +
        '</div>'
    );
	
	self.duplicating_id = 0;
	self.duplicating_title = '';
	
	self.trashing_id = 0;
	
	self.bulk_trashing_target = [];
	
	self.bulktrashdel_action = '';
	self.bulktrashdel_target = [];
	self.bulktrashdel_nonce = '';
	
	self.change_usage_id = 0;
	
	self.bind_single_id = 0;
	self.bind_single_type = '';
	
	self.unlink_single_type = '';
	self.unlink_single_label = '';
	self.unlink_single_number = '';
	
	self.change_ct_assigned_to_what = '';
	
	/**
	* -----------------
	* Utils
	* -----------------
	*/
	
	$( document ).on( 'click', '.js-wpv-content-template-open', function( e ) {
		e.preventDefault();
		var thiz = $( this ),
		$dropdownList = thiz.parent().next('.js-wpv-content-template-dropdown-list');
		$dropdownList.toggle('fast',function(){
			if ( $dropdownList.is(':hidden') ) {
				$(this).prev('p').find('[class^="icon-"]')
					.removeClass('icon-caret-up')
					.addClass('icon-caret-down');
			} else {
				$(this).prev('p').find('[class^="icon-"]')
					.removeClass('icon-caret-down')
					.addClass('icon-caret-up');
			}
		});
		return false;
	});
	
	/**
	* -----------------
	* Search and pagination
	* -----------------
	*/
	
	$( '#posts-filter' ).submit( function( e ) {
		e.preventDefault();
		var url_params = decodeURIParams( $(this ).serialize());
		if (
			typeof( url_params['s'] ) !== 'undefined' 
			&& url_params['s'] == ''
		) {
			url_params['s'] = null;
		}
		navigateWithURIParams( url_params );
		return false;
	});
	
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
	
	/**
	* -----------------
	* Add CT dialog
	* -----------------
	*/
	
	$( document ).on( 'change keyup input cut paste', '.js-wpv-new-content-template-name', function( e ) {
		$( '#js-wpv-create-ct-form-dialog .toolset-alert' ).remove();
		if ( $(this).val() === "" ) {
			disablePrimaryButton( $( '.js-wpv-create-new-template' ) );
		} else {
			enablePrimaryButton( $( '.js-wpv-create-new-template' ) );
		}
	});
	
	$( document ).on( 'click', '.js-add-new-content-template', function( e ) {
		e.preventDefault();
		var thiz = $( this );
		showSpinnerAfter( thiz );
		
		var dialog_height = $( window ).height() - 100;
		self.dialog_create_ct.dialog( 'open' ).dialog({
            width: 770,
            maxHeight: dialog_height,
            draggable: false,
            resizable: false,
			position: { my: "center top+50", at: "center top", of: window }
        });
		
		self.dialog_create_ct.html( self.shortcodeDialogSpinnerContent );
		
		// Do AJAX call to generate popup code
		var data = {
			action: 'wpv_ct_create_new_render_popup',
			wpnonce : $( '#work_view_template' ).val(),
			view_action: self.bulktrashdel_action
		};
		
		$.ajax({
			async: false,
			type: "GET",
			dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( response.success ) {
					self.dialog_create_ct.html( response.data.dialog_content );
					$( '.js-wpv-new-content-template-name' ).focus();
				}
				// We're waiting on user input - hide the spinner shown at the start of bulk action
				hideSpinner();
			},
			error: function( ajaxContext ) {
				//console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() { }
		});
		
		return false;
	});
	
	$( document ).on('change','.js-wpv-dialog-add-new-content-template input[type=checkbox]',function( e ) {
		var $dontAssignInput = $('.js-dont-assign'),
		$allCheckboxes = $('.js-wpv-dialog-add-new-content-template input[type=checkbox]');
		if ( $(this).is(':checked') ) {
			if ( $(e.target).is($dontAssignInput) ) {
				$allCheckboxes.not(this).prop('checked',false)
			}
			else {
				$dontAssignInput.prop('checked',false)
			}
		}
	});
	
	$( document ).on( 'click','.js-wpv-create-new-template', function( e ) {
		e.preventDefault();
		var thiz = $( this ),
		thiz_container = $( '.js-wpv-dialog-add-new-content-template' ),
		thiz_message_container = thiz_container.find( '.js-wpv-error-container' ),
		title = thiz_container.find( '.js-wpv-new-content-template-name' ).val(),
		type = [];
		showSpinnerAfter( thiz );
		$( ".js-wpv-dialog-add-new-content-template input[name='wpv-new-content-template-post-type[]']:checked").each( function() {
			type.push( $( this ).val() );
		});
		var data = {
			action : 'wpv_ct_create_new_save',
			wpnonce : $( '#work_view_template' ).attr( 'value' ),
			type : type,
			title: title
		};
		thiz.prop( 'disabled', true );
		$.post( ajaxurl, data, function( response ) {
			response = $.parseJSON( response );
			// console.log(response);
			if ( ( typeof( response ) !== 'undefined' ) ) {
				if ( response[0] == 'error' ) {
					thiz_message_container
						.wpvToolsetMessage({
							text: response[1],
							stay: true,
							close: false,
							type: 'error'
						});
				 	thiz.prop( 'disabled', false );
					hideSpinner();
				} else {
				 	// console.log('Content Template Created');
                    // todo use l10n and pass ct-editor page name instead of hardcoding it
                    document.location.href = 'admin.php?page=ct-editor&ct_id=' + response[0];
				}
			} else {
				//	console.log( "Error: AJAX returned " + response );
			}
		})
		.fail(function(jqXHR, textStatus, errorThrown) {
			//	console.log( "Error: ", textStatus, errorThrown );
		})
		.always(function() {

		});
		return false;
	});
	
	/**
	* -----------------
	* Duplicate CT dialog
	* -----------------
	*/
	
	/**
	 * This happens when user clicks on the Duplicate action link.
	 *
	 * @since unknown
	 */
	$( document ).on( 'click','.js-list-ct-action-duplicate', function( e ) {
		e.preventDefault();
		
		var thiz = $( this ),
		dialog_height = $( window ).height() - 100;
		
		self.duplicating_id = thiz.data( 'ct-id' );
		self.duplicating_title = thiz.data( 'ct-name' );
		
		$('.js-wpv-duplicate-error-container .toolset-alert').remove();
		disablePrimaryButton( $('.js-wpv-duplicate-ct') );
		
		self.dialog_duplicate_ct.dialog( "open" ).dialog({
			maxHeight: dialog_height,
			draggable: false,
			resizable: false,
			position: { my: "center top+50", at: "center top", of: window }
		});
		
	});
	
	$( document ).on( 'change input cut paste', '.js-wpv-duplicated-title', function() {
		$( '#js-wpv-duplicate-ct-dialogg .toolset-alert').remove();
		if ( $( this ).val().length !== 0 ) {
			enablePrimaryButton( $('.js-wpv-duplicate-ct') );
		} else {
			disablePrimaryButton( $('.js-wpv-duplicate-ct') );
		}
	});

	/**
	 * Duplicate a Content Template.
	 *
	 * @since unknown
	 */ 
	$( document ).on( 'click', '.js-wpv-duplicate-ct', function ( e ) {
		e.preventDefault();

		var thiz = $( this ),
		newName = $( '.js-wpv-duplicated-title' ).val();
		
		showSpinnerAfter( thiz );
		disablePrimaryButton( thiz );
		
		if ( newName.length !== 0 ) {

			var data = {
				action: 'wpv_duplicate_ct',
				id: self.duplicating_id,
				wpnonce : $( '#work_view_template' ).val(),
				title: newName
			};
			
			$.ajax({
				async: false,
				type: "POST",
				dataType: "json",
				url: ajaxurl,
				data: data,
				success: function( response ) {
					if ( response.success ) {
						navigateWithURIParams( decodeURIParams() );
					} else {
						$('#js-wpv-duplicate-ct-dialog .js-wpv-error-container').wpvToolsetMessage({
							text: response.data.message,
							stay: true,
							type: 'error'
						});
						hideSpinner();
					}
				}
			});
			
		}
	});
	
	/**
	* -----------------
	* Change CT usage
	* -----------------
	*/
	
	$( document ).on( 'click', '.js-wpv-ct-change-usage-popup', function( e ) {
		e.preventDefault();
		
		var thiz = $( this ),
		dialog_height = $( window ).height() - 100,
		data = {
			action: 'wpv_change_ct_usage_popup',
			wpnonce: $('#work_view_template').val(),
			id: thiz.data( 'ct-id' )
		};
		
		self.change_usage_id = thiz.data( 'ct-id' );
		
		self.dialog_change_ct_usage.dialog( 'open' ).dialog({
            width: 770,
            maxHeight: dialog_height,
            draggable: false,
            resizable: false,
			position: { my: "center top+50", at: "center top", of: window }
        });
		
		self.dialog_change_ct_usage.html( self.shortcodeDialogSpinnerContent );
		
		$.ajax({
			async: false,
			type: "GET",
			dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( response.success ) {
					self.dialog_change_ct_usage.html( response.data.dialog_content );
				}
			},
			error: function( ajaxContext ) {
				//console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() { }
		});

	});
	
	$( document ).on( 'click', '.js-wpv-change-ct-usage', function( e ) {
		e.preventDefault();
		
		var thiz = $( this ),
		type = [];
		
		showSpinnerAfter( thiz );
		disablePrimaryButton( thiz );
		
        $( "input[name='wpv-new-content-template-post-type[]']:checked" ).each( function() {
			type.push( $(this).val() );
		});
		
        var data = {
            action: 'wpv_change_ct_usage',
            view_template_id: self.change_usage_id,
            wpnonce: $('#work_view_template').attr('value'),
            type: type
        };
		
		$.ajax({
			async: false,
			type: "POST",
			dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( response.success ) {
					navigateWithURIParams(decodeURIParams());
				}

			},
			error: function (ajaxContext) {
				
			},
			complete: function() {

			}
		});
		return false;
   });
   
   /**
	* -----------------
	* Apply CT to single posts on a post type
	* -----------------
	*/
	
	$( document ).on( 'click','.js-wpv-apply-ct-to-all-cpt-single-dialog', function( e ) {
	    e.preventDefault();
	    var thiz = $( this ),
		wpnonce = thiz.data( 'nonce' ),
		dialog_height = $( window ).height() - 100;
		
		self.bind_single_id = thiz.data( 'id' );
		self.bind_single_type = thiz.data( 'type' );
		
		self.dialog_bind_ct.dialog( 'open' ).dialog({
            width: 770,
            maxHeight: dialog_height,
            draggable: false,
            resizable: false,
			position: { my: "center top+50", at: "center top", of: window }
        });

        self.dialog_bind_ct.html( self.shortcodeDialogSpinnerContent );
		
		var data = {
			action: 'wpv_apply_ct_to_cpt_posts_popup',
			id: self.bind_single_id,
			type: self.bind_single_type,
			wpnonce : wpnonce,
		};

		$.ajax({
			async: false,
			type: "GET",
			dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( response.success ) {
					self.dialog_bind_ct.html( response.data.dialog_content );
				}
			},
			error: function( ajaxContext ) {
				//console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() { }
		});
		
	    return false;
	});
   
	$( document ).on( 'click', '.js-wpv-apply-ct-to-all-cpt-single', function() {
		var thiz = $( this ),
		data = {
			action : 'wpv_apply_ct_to_cpt_posts',
			id : self.bind_single_id,
			type : self.bind_single_type,
			wpnonce : $('#work_view_template').val()
		};
		
		showSpinnerAfter( thiz );
		disablePrimaryButton( thiz );
		
		$.ajax({
			async: false,
			type: "POST",
			dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( response.success ) {
					$( '.js-wpv-apply-ct-to-cpt-single-' + self.bind_single_type )
						.html( '' )
						.wpvToolsetMessage({
							text: ct_listing_texts.update_completed,
							inline: true,
							stay: false,
							close: false,
							type: 'success'
						});
					self.dialog_bind_ct.dialog( "close" );
				}
			},
			error: function( ajaxContext ) {
				
			},
			complete: function() {

			}
		});
		
	});	
	
	/**
	* -----------------
	* Unlink CT from single posts
	* -----------------
	*/

	$( document ).on( 'click', '.js-wpv-clear-cpt-from-ct-popup', function( e ) {
		e.preventDefault();
		
		var thiz = $( this ),
		dialog_height = $( window ).height() - 100;
		
		self.unlink_single_type = thiz.data('slug');
		self.unlink_single_label = thiz.data('label');
		self.unlink_single_number = thiz.data('unclear');
		
		self.dialog_unlink_ct.dialog( "open" ).dialog({
			maxHeight: dialog_height,
			draggable: false,
			resizable: false,
			position: { my: "center top+50", at: "center top", of: window }
		});
		return false;

	});

	$( document ).on( 'click', '.js-wpv-clear-cpt', function( e ) {
		e.preventDefault();
		
		var thiz = $( this ),
		data = {
			action: 'wpv_clear_cpt_from_ct',
			wpnonce: ct_listing_texts.dialog_unlink_nonce,
			slug: self.unlink_single_type
		};
		
		showSpinnerAfter( thiz );
		disablePrimaryButton( thiz );
		
		$.ajax({
			async: false,
			type: "POST",
			dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( response.success ) {
					var url_params = decodeURIParams();
					navigateWithURIParams(url_params);
				}

			},
			error: function (ajaxContext) {
				
			},
			complete: function() {

			}
		});

	});
	
	/**
	* -----------------
	* Change CT assigned to a single CPT
	* -----------------
	*/
	
	$( document ).on( 'click','.js-wpv-change-ct-assigned-to-something-popup', function( e ) {
		e.preventDefault();
		
		var thiz = $( this ),
		dialog_height = $( window ).height() - 100;
		
		self.change_ct_assigned_to_what = thiz.data('pt');
				
		self.dialog_change_ct_assigned_to_something.dialog( 'open' ).dialog({
            width: 770,
            maxHeight: dialog_height,
            draggable: false,
            resizable: false,
			position: { my: "center top+50", at: "center top", of: window }
        });

        self.dialog_change_ct_assigned_to_something.html( self.shortcodeDialogSpinnerContent );
		
		var data = {
			action: 'wpv_change_ct_assigned_to_something_dialog',
			pt: self.change_ct_assigned_to_what,
			wpnonce : $('#work_view_template').val()
		};

		$.ajax({
			async: false,
			type: "GET",
			dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( response.success ) {
					self.dialog_change_ct_assigned_to_something.html( response.data.dialog_content );
				}
			},
			error: function( ajaxContext ) {
				//console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() { }
		});
	});
	
    $( document ).on( 'click', '.js-wpv-change-ct-assigned-to-this', function( e ) {
     	e.preventDefault();
		
		var thiz = $( this ),
		data = {
	        action: 'wpv_change_ct_assigned_to_something',
	        pt: self.change_ct_assigned_to_what,
	        value: $( '#js-wpv-change-ct-assigned-to-something .js-wpv-content-template-name:checked' ).val(),
	        wpnonce: $('#work_view_template').val()
	    };
		
		showSpinnerAfter( thiz );
		disablePrimaryButton( thiz );
		
		$.ajax({
			async: false,
			type: "POST",
			dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( response.success ) {
					var url_params = decodeURIParams();
					navigateWithURIParams(url_params);
				}
			},
			error: function( ajaxContext ) {
				//console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() { }
		});
	    
	    return false;
	});
	
	/**
	* -----------------
	* Action links
	* -----------------
	*/

	$( document ).on( 'click', '.js-wpv-ct-action-trash', function( e ) {
		e.preventDefault();
		
		var thiz = $(this);
		var ctId = thiz.data( 'ct-id' );

		thiz.closest( '.js-wpv-ct-list-row' ).find( 'h3' ).append( ' <div class="wpv-spinner ajax-loader"></div>' );
		showSpinner();

        var onCancelCallback = function() {
            hideSpinner();
        };

        self.trashCTs([ctId], ct_listing_texts.action_nonce, onCancelCallback);

	});



	$( document ).on( 'click', '.js-wpv-ct-action-restore-from-trash', function( e ) {
		e.preventDefault();
		
		var thiz = $( this ),
		ct_id = thiz.data( 'ct-id' );

		thiz
			.closest('.js-wpv-ct-list-row')
				.find('h3')
				.append(' <div class="wpv-spinner ajax-loader"></div>');
		$('.subsubsub').append('<div class="wpv-spinner ajax-loader"></div>');
		
		var data = {
			action: 'wpv_view_change_status',
			id: ct_id,
			newstatus: 'publish',
			wpnonce : ct_listing_texts.action_nonce
		};
		
		$.ajax({
			async: false,
			type: "POST",
			//dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( (typeof(response) !== 'undefined') && (response == data.id)) {
					var url_params = decodeURIParams();
					url_params['paged'] = updatePagedParameter( url_params, 1, '.js-wpv-ct-list-row' );
					url_params['untrashed'] = 1;
					navigateWithURIParams(url_params);
				} else {
					//console.log( "Error: AJAX returned ", response );
				}
			},
			error: function (ajaxContext) {
				//console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() {	}
		});
	});
	
	/**
	 * Undo "trash" action when user clicks on the Undo link.
	 */ 
	$( document ).on( 'click', '.js-wpv-untrash', function( e ) {
		e.preventDefault();
		
		var thiz = $( this ),
		nonce = thiz.data( 'nonce' ),
		ctIDs = decodeURIComponent( thiz.data( 'ids' ) ).split( ',' );
		
		showSpinnerAfter( thiz );

		self.untrashCTs( ctIDs, nonce );
	});
	
	/**
	 * This happens on "Delete" action for a single content template.
	 *
	 * We'll show the same confirmation dialog as for bulk action.
	 *
	 * @since unknown
	 */
	$( document ).on( 'click','.js-list-ct-action-delete', function( e ) {
		e.preventDefault();
		showSpinner();

		var thiz = $( this ),
		ctID = thiz.data( 'ct-id' );

		// Show confirmation, act like if it's a bulk action.
		self.deleteCTsConfirmation( [ ctID ], ct_listing_texts.action_nonce );
	});
	
	/**
	* -----------------
	* Bulk management
	* -----------------
	*/
	
	/**
	 * Bulk action.
	 *
	 * Fires when user hits the Apply button near bulk action select field.
	 *
	 * @since 1.7
	 */
	$( '.js-wpv-ct-listing-bulk-action-submit' ).on( 'click', function( e ) {
		e.preventDefault();

		showSpinner();

		// Get an array of checked View IDs.
		var checkedCTs = $('.wpv-admin-listing-col-bulkactions input:checkbox:checked').map(function() {
			var value = $(this).val();
			// Filter out values of checkboxes in table header and footer rows.
			if( $.isNumeric( value ) ) {
				return value;
			}
		}).get();

		// If there are no items selected, do nothing.
		if ( checkedCTs.length == 0 ) {
			hideSpinner();
			return;
		}

		// nonce
		var nonce = ct_listing_texts.action_nonce;

		// Get a position. That's important to determine which select field is relevant for us.
		var selectPosition = $(this).data('position');

		// Launch appropriate bulk action
		var action = $('.js-wpv-ct-listing-bulk-action-select.position-' + selectPosition).val();
		switch ( action ) {
			case 'trash':
				self.trashCTs( checkedCTs, nonce );
				break;
			case 'restore-from-trash':
				self.untrashCTs( checkedCTs, nonce );
				break;
			case 'delete':
				self.deleteCTsConfirmation( checkedCTs, nonce );
				break;
			default:
				// do nothing
				hideSpinner();
				return;
		}
	});


    /**
     * Invokes wpv_bulk_content_templates_move_to_trash, which:
     * (a) trashes given CTs directly if they aren't used anywhere, in which case we reload the page with 'trashed'
     *     message, or
     * (b) renders HTML for a dialog which we'll show (and the action continues with user clicking
     *     on '.js-ct-bulk-replace-usage').
     *
     * @since 1.7
     *
     * @see wpv_bulk_content_templates_move_to_trash_callback()
     *
     * @param {[{int}]} ctIDs An array of Content Template IDs that should be untrashed.
     * @param {string} nonce A valid wpv_view_listing_actions_nonce.
     * @param {function|undefined} onCancelCallback Callback function for when the trashing is cancelled or fails.
     */
    self.trashCTs = function(ctIDs, nonce, onCancelCallback) {

        var onCancelCallbackFinal = function() {
            hideSpinner();
            if(typeof(onCancelCallback) != 'undefined') {
                onCancelCallback();
            }
        };

        // Callbacks
        var afterTrashing = function() {
            // CTs have been trashed. Redirect to CT listing page and show a message.
            var url_params = decodeURIParams();
            var affectedItemCount = ctIDs.length;
            url_params['paged'] = updatePagedParameter( url_params, affectedItemCount, '.js-wpv-ct-list-row' );
            url_params['trashed'] = affectedItemCount;
            url_params['affected'] = ctIDs;
            navigateWithURIParams(url_params);
        };

        var dialog = new WPViews.ct_dialogs.TrashContentTemplatesDialog(nonce, afterTrashing, onCancelCallbackFinal, onCancelCallbackFinal);
        dialog.trashContentTemplates(ctIDs);
	};

	
	/**
	 * Restore Content Templates from trash.
	 *
	 * Invokes wpv_view_bulk_change_status, changing post status of given templates to 'publish'.
	 * Afterwards reloads the page with an additional parameter for the 'untrashed' message.
	 *
	 * @since 1.7
	 *
	 * @param {[]} ctIDs An array of Content Template IDs that should be untrashed.
	 * @param {string} nonce A valid wpv_view_listing_actions_nonce.
	 */
	self.untrashCTs = function( ctIDs, nonce ) {
		var data = {
			action: 'wpv_view_bulk_change_status',
			ids: ctIDs,
			newstatus: 'publish',
			wpnonce : nonce
		};
		
		$.ajax({
			async: false,
			type: "POST",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				// response == 1 indicates success
				if ( (typeof(response) !== 'undefined') && ( response == 1 ) ) {
					// reload the page with "untrashed" message
					var url_params = decodeURIParams();
					var affectedItemCount = ctIDs.length;
					url_params['paged'] = updatePagedParameter( url_params, affectedItemCount, '.js-wpv-ct-list-row' );
					url_params['untrashed'] = affectedItemCount;
					navigateWithURIParams( url_params );
				} else {
					console.log( "Error: AJAX returned ", response );
				}
			},
			error: function( ajaxContext ) {
				console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() { }
		});
	};
	
	/**
	 * Show a confirmation dialog before bulk deleting content templates.
	 *
	 * Content Templates are deleted after clicking on the .js-bulk-remove-templates-permanent button.
	 *
	 * @since 1.7
	 *
	 * @param array ctIDs An array of Content Template IDs that should be permanently deleted.
	 * @param string nonce A valid wpv_view_listing_actions_nonce.
	 */ 
	self.deleteCTsConfirmation = function( ctIDs, nonce ) {
	
		var ctCount = ctIDs.length,
		data = {
			action: 'wpv_ct_bulk_count_usage',
			ids: ctIDs,
			wpnonce : nonce
		};

		$.ajax({
			async: false,
			type: "GET",
			dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( response.success ) {
					
					self.bulk_trashing_target = ctIDs;
					var dialog_height = $( window ).height() - 100;
					self.dialog_bulk_delete_warning_ct.dialog( "open" ).dialog({
						width: 770,
						title: ( ctCount == 1 ) ? ct_listing_texts.dialog_bulkdel_dialog_title : ct_listing_texts.dialog_bulkdel_dialog_title_plural,
						maxHeight: dialog_height,
						draggable: false,
						resizable: false,
						position: { my: "center top+50", at: "center top", of: window }
					});
					
					if ( ctCount == 1 ) {
						$('.js-singular').show();
						$('.js-plural').hide();
					} else {
						$('.js-plural').show();
						$('.js-singular').hide();
					}
					
					var postsUsingCTs = response.data.total_usage;
					if( postsUsingCTs > 0 ) {
						// Insert total count of posts that use any of CTs that are to be deleted.
						$('.js-ct-single-postcount').html( postsUsingCTs );
					} else {
						$('.js-ct-single-postcount-message-usage').hide();
						$('.js-ct-single-postcount-message-ays-nonzero').hide();
						$('.js-ct-single-postcount-message-ays-zero').show();
					}
					
					hideSpinner();
				}
			},
			error: function( ajaxContext ) {
				//console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() { }
		});
	};
	
	/**
	 * Bulk delete content templates after confirmation.
	 *
	 * Invokes the wpv_ct_bulk_delete() function by AJAX.
	 *
	 * @since 1.7
	 */  
	$( document ).on( 'click', '.js-bulk-remove-templates-permanent', function( e ) {
		e.preventDefault();
		
		var thiz = $( this ),
		data = {
			action: 'wpv_ct_bulk_delete',
			ids: self.bulk_trashing_target,
			wpnonce : ct_listing_texts.action_nonce
		};
		
		showSpinnerAfter( thiz );
		disablePrimaryButton( thiz );

		$.ajax({
			async: false,
			type: "POST",
			dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( response.success ) {
					var url_params = decodeURIParams();
					var affectedItemCount = self.bulk_trashing_target.length;
					url_params['paged'] = updatePagedParameter( url_params, affectedItemCount, '.js-wpv-ct-list-row' );
					url_params['deleted'] = affectedItemCount;
					navigateWithURIParams( url_params );
				}
			},
			error: function (ajaxContext) {
				//console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() {	}
		});
	});
	
	/**
	* -----------------
	* Init dialogs
	* -----------------
	*/
	
	self.init_dialogs = function() {
		
		$( 'body' ).append( '<div id="js-wpv-create-ct-form-dialog" class="toolset-shortcode-gui-dialog-container wpv-shortcode-gui-dialog-container js-wpv-shortcode-gui-dialog-container"></div>' );
		
		self.dialog_create_ct = $( "#js-wpv-create-ct-form-dialog" ).dialog({
			autoOpen: false,
			modal: true,
			title: ct_listing_texts.dialog_create_dialog_title,
			minWidth: 600,
			show: { 
				effect: "blind", 
				duration: 800 
			},
			open: function( event, ui ) {
				$( 'body' ).addClass( 'modal-open' );
				hideSpinner();
				disablePrimaryButton( $('.js-wpv-create-new-template') );
				$( '#js-wpv-create-ct-form-dialog .toolset-alert' ).remove();
			},
			close: function( event, ui ) {
				$( 'body' ).removeClass( 'modal-open' );
			},
			buttons:[
				{
					class: 'button-secondary',
					text: ct_listing_texts.dialog_cancel,
					click: function() {
						$( this ).dialog( "close" );
					}
				},
				{
					class: 'button-primary js-wpv-create-new-template',
					text: ct_listing_texts.dialog_create_action,
					click: function() {

					}
				}
			]
		});
		
		self.dialog_duplicate_ct = $( "#js-wpv-duplicate-ct-dialog" ).dialog({
			autoOpen: false,
			modal: true,
			title: ct_listing_texts.dialog_duplicate_dialog_title,
			minWidth: 600,
			show: { 
				effect: "blind", 
				duration: 800 
			},
			open: function( event, ui ) {
				$( 'body' ).addClass( 'modal-open' );
				$('.js-duplicate-origin-title').html( self.duplicating_title );
				$('.js-wpv-duplicated-title').focus().val('');
				disablePrimaryButton( $( '.js-wpv-duplicate-view' ) );
				$( '#js-wpv-duplicate-ct-dialog .toolset-alert').remove();
			},
			close: function( event, ui ) {
				$( 'body' ).removeClass( 'modal-open' );
				self.duplicating_id = 0;
				self.duplicating_title = '';
			},
			buttons:[
				{
					class: 'button-secondary',
					text: ct_listing_texts.dialog_cancel,
					click: function() {
						$( this ).dialog( "close" );
					}
				},
				{
					class: 'button-primary js-wpv-duplicate-ct',
					text: ct_listing_texts.dialog_duplicate_action,
					click: function() {

					}
				}
			]
		});
		
		$( 'body' ).append( '<div id="js-wpv-dialog-trash-warning-ct" class="toolset-shortcode-gui-dialog-container wpv-shortcode-gui-dialog-container js-wpv-shortcode-gui-dialog-container"></div>' );
		
		self.dialog_trash_warning_ct = $( '#js-wpv-dialog-trash-warning-ct' ).dialog({
			autoOpen: false,
			modal: true,
			title: ct_listing_texts.dialog_trash_warning_dialog_title,
			minWidth: 600,
			show: { 
				effect: "blind", 
				duration: 800 
			},
			open: function( event, ui ) {
				$( 'body' ).addClass( 'modal-open' );
			},
			close: function( event, ui ) {
				$( 'body' ).removeClass( 'modal-open' );
				self.trashing_id = 0;
			},
			buttons:[
				{
					class: 'button-secondary',
					text: ct_listing_texts.dialog_cancel,
					click: function() {
						$( this ).dialog( "close" );
					}
				},
				{
					class: 'button-primary js-wpv-ct-trash-and-replace-usage',
					text: ct_listing_texts.dialog_trash_warning_action,
					click: function() {

					}
				}
			]
		});

		
		self.dialog_bulk_delete_warning_ct = $( '#js-bulk-remove-content-templates-dialog' ).dialog({
			autoOpen: false,
			modal: true,
			title: ct_listing_texts.dialog_bulkdel_dialog_title,
			minWidth: 600,
			show: { 
				effect: "blind", 
				duration: 800 
			},
			open: function( event, ui ) {
				$( 'body' ).addClass( 'modal-open' );
			},
			close: function( event, ui ) {
				$( 'body' ).removeClass( 'modal-open' );
				self.trashing_id = 0;
			},
			buttons:[
				{
					class: 'button-secondary',
					text: ct_listing_texts.dialog_cancel,
					click: function() {
						$( this ).dialog( "close" );
					}
				},
				{
					class: 'button-primary js-bulk-remove-templates-permanent',
					text: ct_listing_texts.dialog_bulkdel_action,
					click: function() {

					}
				}
			]
		});
		
		$( 'body' ).append( '<div id="js-wpv-dialog-change-ct-usage" class="toolset-shortcode-gui-dialog-container wpv-shortcode-gui-dialog-container js-wpv-shortcode-gui-dialog-container"></div>' );
		
		self.dialog_change_ct_usage = $( '#js-wpv-dialog-change-ct-usage' ).dialog({
			autoOpen: false,
			modal: true,
			title: ct_listing_texts.dialog_change_ct_usage_dialog_title,
			minWidth: 600,
			show: { 
				effect: "blind", 
				duration: 800 
			},
			open: function( event, ui ) {
				$( 'body' ).addClass( 'modal-open' );
			},
			close: function( event, ui ) {
				$( 'body' ).removeClass( 'modal-open' );
				self.change_usage_id = 0;
			},
			buttons:[
				{
					class: 'button-secondary',
					text: ct_listing_texts.dialog_cancel,
					click: function() {
						$( this ).dialog( "close" );
					}
				},
				{
					class: 'button-primary js-wpv-change-ct-usage',
					text: ct_listing_texts.dialog_change_ct_usage_action,
					click: function() {

					}
				}
			]
		});
		
		$( 'body' ).append( '<div id="js-wpv-dialog-bind-ct" class="toolset-shortcode-gui-dialog-container wpv-shortcode-gui-dialog-container js-wpv-shortcode-gui-dialog-container"></div>' );
		
		self.dialog_bind_ct = $( '#js-wpv-dialog-bind-ct' ).dialog({
			autoOpen: false,
			modal: true,
			title: ct_listing_texts.dialog_bind_ct_dialog_title,
			minWidth: 600,
			show: { 
				effect: "blind", 
				duration: 800 
			},
			open: function( event, ui ) {
				$( 'body' ).addClass( 'modal-open' );
			},
			close: function( event, ui ) {
				$( 'body' ).removeClass( 'modal-open' );
				self.bind_single_id = 0;
				self.bind_single_type = '';
			},
			buttons:[
				{
					class: 'button-secondary',
					text: ct_listing_texts.dialog_cancel,
					click: function() {
						$( this ).dialog( "close" );
					}
				},
				{
					class: 'button-primary js-wpv-apply-ct-to-all-cpt-single',
					text: ct_listing_texts.dialog_update,
					click: function() {

					}
				}
			]
		});
		
		self.dialog_unlink_ct = $( '#js-wpv-clear-all-cpt-from-ct' ).dialog({
			autoOpen: false,
			modal: true,
			title: ct_listing_texts.dialog_unlink_dialog_title,
			minWidth: 600,
			show: { 
				effect: "blind", 
				duration: 800 
			},
			open: function( event, ui ) {
				$( 'body' ).addClass( 'modal-open' );
				$( '.js-single-unlink-label' ).each( function() {
					$(this).html( self.unlink_single_label );
				});
				$('.js-single-unlink-number').each(function(){
					$(this).html( self.unlink_single_number );
				});
			},
			close: function( event, ui ) {
				$( 'body' ).removeClass( 'modal-open' );
				self.unlink_single_type = '';
				self.unlink_single_label = '';
				self.unlink_single_number = '';
			},
			buttons:[
				{
					class: 'button-secondary',
					text: ct_listing_texts.dialog_cancel,
					click: function() {
						$( this ).dialog( "close" );
					}
				},
				{
					class: 'button-primary js-wpv-clear-cpt',
					text: ct_listing_texts.dialog_unlink_action,
					click: function() {

					}
				}
			]
		});
		
		$( 'body' ).append( '<div id="js-wpv-change-ct-assigned-to-something" class="toolset-shortcode-gui-dialog-container wpv-shortcode-gui-dialog-container js-wpv-shortcode-gui-dialog-container"></div>' );
		
		self.dialog_change_ct_assigned_to_something = $( '#js-wpv-change-ct-assigned-to-something' ).dialog({
			autoOpen: false,
			modal: true,
			title: ct_listing_texts.dialog_change_ct_assigned_to_sth_dialog_title,
			minWidth: 600,
			show: { 
				effect: "blind", 
				duration: 800 
			},
			open: function( event, ui ) {
				$( 'body' ).addClass( 'modal-open' );
			},
			close: function( event, ui ) {
				$( 'body' ).removeClass( 'modal-open' );
				self.change_ct_assigned_to_what = '';
			},
			buttons:[
				{
					class: 'button-secondary',
					text: ct_listing_texts.dialog_cancel,
					click: function() {
						$( this ).dialog( "close" );
					}
				},
				{
					class: 'button-primary js-wpv-change-ct-assigned-to-this',
					text: ct_listing_texts.dialog_update,
					click: function() {

					}
				}
			]
		});
		
		
	};
	
	self.init = function() {
		self.init_dialogs();
	};
	
	self.init();

};

jQuery( document ).ready( function( $ ) {
    WPViews.ct_listing_screen = new WPViews.CTListingScreen( $ );
});