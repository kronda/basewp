var WPViews = WPViews || {};

WPViews.WPAListingScreen = function( $ ) {

    var self = this;
	
	self.dialog_create_or_change_usage = '';
	self.deleting_id = 0;
	self.creating_archive_loop_title = '';
	self.creating_archive_loop = '';
	self.bulk_trashing_ids = [];
	self.bulk_deleting_ids = [];
	
	self.shortcodeDialogSpinnerContent = $(
        '<div style="min-height: 150px;">' +
            '<div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; ">' +
                '<div class="wpv-spinner ajax-loader"></div>' +
                '<p>' + wpa_listing_texts.loading_options + '</p>' +
            '</div>' +
        '</div>'
    );
	
	/**
	* -----------------
	* Search and pagination
	* -----------------
	*/
	
	$( '#posts-filter' ).submit( function( e ) {
		e.preventDefault();
		var url_params = decodeURIParams( $( this ).serialize() );
		if (
			typeof( url_params['s'] ) !== 'undefined' 
			&& url_params['s'] == ''
		) {
			url_params['s'] = null;
		}
		navigateWithURIParams( url_params );
		return false;
	});
	
	$( document ).on( 'change', '.js-items-per-page', function() {
	    navigateWithURIParams(decodeURIParams('paged=1&items_per_page=' + $(this).val()));
    });

    $( document ).on( 'click', '.js-wpv-display-all-items', function(e){
	    e.preventDefault();
	    navigateWithURIParams(decodeURIParams('paged=1&items_per_page=-1'));
    });

    $( document ).on( 'click', '.js-wpv-display-default-items', function(e){
	    e.preventDefault();
	    navigateWithURIParams(decodeURIParams('paged=1&items_per_page=20'));
    });
	
	/* ****************************************************************************\
            Action links
    \* ****************************************************************************/
	
	/**
	 * Fires when user clicks on "trash" action.
	 *
	 * @since unknown
	 */
	$( document ).on( 'click','.js-list-views-action-trash', function( e ) {
		e.preventDefault();
		showSpinner();

		var thiz = $( this ),
		wpaId = thiz.data( 'view-id' ),
		nonce = thiz.data( 'viewactionnonce' );

		// Just act as if this was a bulk action.
		self.maybeTrashWPAs( [ wpaId ], nonce );
	});


	/**
	 * Fires when user clicks on "restore-from-trash" action.
	 */
	$( document ).on( 'click','.js-list-views-action-restore-from-trash', function( e ) {
		e.preventDefault();
		
		var thiz = $( this ),
		data_view_id = thiz.data('view-id'),
		view_listing_action_nonce = thiz.data('viewactionnonce'),
		data = {
			action: 'wpv_view_change_status',
			id: data_view_id,
			newstatus: 'publish',
			wpnonce : view_listing_action_nonce
		};
		
		showSpinner();
		
		$.ajax({
			async: false,
			type: "POST",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( (typeof(response) !== 'undefined') && (response == data.id)) {
					var url_params = decodeURIParams();
					url_params['paged'] = updatePagedParameter( url_params, 1 );
					url_params['untrashed'] = 1;
					navigateWithURIParams(url_params);
				} else {
					//console.log( "Error: AJAX returned ", response );
				}
			},
			error: function (ajaxContext) {
				//console.log( "Error: ", ajaxContext.responseText );
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
	$( document ).on( 'click', '.js-wpv-untrash', function( e ) {
		e.preventDefault();
		
		var thiz = $( this ),
		nonce = thiz.data( 'nonce' ),
		viewIDs = decodeURIComponent( thiz.data( 'ids' ) ).split( ',' );
		
		showSpinnerAfter( thiz );

		untrashViews( viewIDs, nonce );
	});
	
	/**
	 * Fires when user clicks on "delete" action.
	 */
	$( document ).on( 'click', '.js-list-views-action-delete', function( e ) {
		e.preventDefault();
		
		var thiz = $( this ),
		data_view_id = thiz.data( 'view-id' ),
		dialog_height = $( window ).height() - 100;
		
		self.deleting_id = data_view_id;
		
		self.dialog_delete_wpa.dialog( "open" ).dialog({
            width: 770,
            maxHeight: dialog_height,
            draggable: false,
            resizable: false,
			position: { my: "center top+50", at: "center top", of: window }
        });

	});
	
	/**
	 * Delete action
	 */ 
	$( document ).on( 'click', '.js-wpv-remove-wpa-permanent', function( e ) {
		e.preventDefault();
		
		var thiz = $( this ),
		data = {
			action: 'wpv_delete_wpa_permanent',
			id: self.deleting_id,
			wpnonce : $('#wpv_remove_view_permanent_nonce').val()
        };
		
		showSpinnerBefore( thiz );
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
					url_params['paged'] = updatePagedParameter( url_params, 1 );
					url_params['deleted'] = 1;
					navigateWithURIParams( url_params );
				}
			},
			error: function (ajaxContext) {
				//console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() {	}
		});
	});


    /* ****************************************************************************\
            Create new WPA dialog
    \* ****************************************************************************/


    /**
     * When a dialog for creating new WPA is open, this indicates whether user
     * has changed WPA name field in any way.
     *
     * If they didn't we feel free to suggest WPA name based on selected usage.
     *
     * @type {boolean}
     *
     * @since 1.9
     */
    var isWPANameCustomized = false;


    /**
     * WPA name field has changed.
     *
     * @since 1.9
     */
    $(document).on('change', '.js-wpv-new-archive-name', function() {
        isWPANameCustomized = true;
    });


    /**
     * Some WPA usage checkbox value has been changed.
     *
     * Update WPA name suggestion if applicable.
     *
     * @since 1.9
     */
    $(document).on('change', '.js-wpv-create-wpa-usage-checkbox', function() {

        if(!isWPANameCustomized) {

            // Collect display names of selected loops.
            var selectedLoops = [];
            $('.js-wpv-create-wpa-usage-checkbox').each(function() {
                var checkbox = $(this);
                if(checkbox.is(':checked')) {
                    selectedLoops.push(checkbox.data('loop-name'));
                }
            });

            // Suggest a WPA name by concatenating loop names.
            var wpaNameField = $('.js-wpv-new-archive-name');
            wpaNameField.val(selectedLoops.join(', '));

            // This will update button availability.
            wpaNameField.change();

            // User still didn't make any customizations.
            isWPANameCustomized = false;
        }

    });
	
	$( document ).on( 'click', '.js-wpv-views-archive-add-new, .js-wpv-views-archive-create-new', function( e ) {
        e.preventDefault();
		
        var thiz = $( this ),
		dialog_height = $( window ).height() - 100,
        data = {
			action: 'wpv_create_wp_archive_popup',
            wpnonce: $('#work_views_listing').val()
        };
		
		self.dialog_create_or_change_usage = 'create';
		
		self.dialog_create_wpa.dialog( 'open' ).dialog({
            width: 770,
			title: wpa_listing_texts.dialog_create_dialog_title,
            maxHeight: dialog_height,
            draggable: false,
            resizable: false,
			position: { my: "center top+50", at: "center top", of: window }
        });
		
		self.dialog_create_wpa.html( self.shortcodeDialogSpinnerContent );
		
		$.ajax({
			async: false,
			type: "GET",
			dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( response.success ) {
					self.dialog_create_wpa.html( response.data.dialog_content );
					$( '.js-wpv-new-archive-name' ).focus();
					disablePrimaryButton( $( '.js-wpv-create-new-wpa' ) );
				}
			},
			error: function( ajaxContext ) {
				//console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() { }
		});

    });
	
	$(document).on( 'keypress','.js-wpv-new-archive-name', function(event){
        if ( event.which == 13 ) {
            event.preventDefault();
        }
    });

    /**
     * Create Archive from the create dialog
     *
     * @since unknown
     */
    $( document ).on( 'click', '.js-wpv-create-new-wpa', function( e ) {
        e.preventDefault();
        var thiz = $( this ),
        thiz_container = thiz.closest( '.js-wpv-dialog-wpa-manager' ),
        thiz_message_container = thiz_container.find( '.js-wpv-error-container' ),
		data;
		
		disablePrimaryButton( thiz );
        showSpinnerAfter( thiz );
        thiz_container.find('.toolset-alert').remove();
		
		if ( self.dialog_create_or_change_usage == 'create' ) {
			data = {
				action: 'wpv_wp_archive_create_new',
				form: $('#wpv-create-archive-view-form').serialize(),
				wpnonce : $('#work_views_listing').val()
			};
		} else if ( self.dialog_create_or_change_usage == 'change' ) {
			data = {
				action: 'wpv_wp_archive_change_usage',
				form: $('#wpv-create-archive-view-form').serialize(),
				wpnonce : $('#work_views_listing').attr('value')
			};
		}
        $.ajax({
            async: false,
            type: "POST",
			dataType: "json",
            url: ajaxurl,
            data: data,
            success: function( response ) {
				if ( response.success ) {
					if ( self.dialog_create_or_change_usage == 'create' ) {
						$( location ).attr( 'href', wpa_listing_texts.edit_url + response.data.id );
					} else if ( self.dialog_create_or_change_usage == 'change' ) {
						navigateWithURIParams(decodeURIParams());
					}
				} else {
					thiz_message_container
                        .wpvToolsetMessage({
                            text: response.data.message,
                            stay: true,
                            close: false,
                            type: 'error'
                        });
                    hideSpinner();
				}
            },
            error: function (ajaxContext) {
                //console.log( "Error: ", ajaxContext.responseText );
            },
            complete: function() {

            }
        });
    });

    /**
     * Controls the buttons in WP Archive creation popup
     *
     * @since unknown
     */
    $( document ).on( 'change input cut paste','.js-wpv-new-archive-name', function() {
        if ( $( this ).val() === "" ) {
			disablePrimaryButton( $( '.js-wpv-create-new-wpa' ) );
        } else {
			enablePrimaryButton( $( '.js-wpv-create-new-wpa' ) );
        }
    });

    /**
     * Create Archive for loop popup.
     *
     * @since unknown
     */
    $( document ).on( 'click', '.js-wpv-create-wpa-for-archive-loop', function( e ) {
        e.preventDefault();
		
		var thiz = $( this ),
		dialog_height = $( window ).height() - 100;
		
		self.creating_archive_loop_title = thiz.data( 'forwhomtitle' );
		self.creating_archive_loop = thiz.data( 'forwhomloop' );
		
		self.dialog_create_wpa_for_archive_loop.dialog( "open" ).dialog({
            width: 770,
            maxHeight: dialog_height,
            draggable: false,
            resizable: false,
			position: { my: "center top+50", at: "center top", of: window }
        });
		
    });
	
	$( document ).on( 'change input cut paste', '.js-wpv-create-wpa-for-archive-loop-title', function() {
		var thiz = $( this ),
		thiz_button = $( '.js-wpv-add-wp-archive-for-loop' );
		if ( thiz.val() == '' ) {
			disablePrimaryButton( thiz_button );
		} else {
			enablePrimaryButton( thiz_button );
		}
	});
	
	$( document ).on( 'click', '.js-wpv-add-wp-archive-for-loop', function( e ) {
		e.preventDefault();
		
		var thiz = $( this ),
		thiz_container = thiz.closest( '.js-wpv-create-wpa-for-archive-loop' ),
        thiz_message_container = thiz_container.find( '.js-wpv-error-container' ),
		data = {
            action: 'wpv_create_wpa_for_archive_loop',
			title: $( '.js-wpv-create-wpa-for-archive-loop-title' ).val(),
			loop: self.creating_archive_loop,
            wpnonce : $('#work_views_listing').val()
        };

        showSpinnerBefore( thiz );
		disablePrimaryButton( thiz );
		thiz_message_container.html( '' );

        $.ajax({
            async: false,
            type: "POST",
			dataType: "json",
            url: ajaxurl,
            data: data,
            success: function( response ) {
				if ( response.success ) {
					$( location ).attr( 'href', wpa_listing_texts.edit_url + response.data.id );
				} else {
					thiz_message_container
						.wpvToolsetMessage({
                            text: response.data.message,
                            stay: true,
                            close: false,
                            type: 'error'
                        });
				}
            },
            error: function( ajaxContext ) {
                //console.log( "Error: ", ajaxContext.responseText );
            },
            complete: function() {

            }
        });
		
	});
	
	
	/**
	* -----------------
	* Change WPA usage
	* -----------------
	*/
	
	/**
	 * Fires when user clicks on "change" action.
	 */
	$( document ).on( 'click','.js-list-views-action-change', function( e ) {
		e.preventDefault();
		
		var thiz = $( this ),
		dialog_height = $( window ).height() - 100,
        data = {
			action: 'wpv_change_wp_archive_usage_popup',
			id: thiz.data('view-id'),
            wpnonce: $('#work_views_listing').val()
        };
		
		self.dialog_create_or_change_usage = 'change';
		
		self.dialog_create_wpa.dialog( 'open' ).dialog({
            width: 770,
			title: wpa_listing_texts.dialog_change_usage_dialog_title,
            maxHeight: dialog_height,
            draggable: false,
            resizable: false,
			position: { my: "center top+50", at: "center top", of: window }
        });
		
		self.dialog_create_wpa.html( self.shortcodeDialogSpinnerContent );
		
		$.ajax({
			async: false,
			type: "GET",
			dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( response.success ) {
					self.dialog_create_wpa.html( response.data.dialog_content );
					enablePrimaryButton( $( '.js-wpv-create-new-wpa' ) );
				}
			},
			error: function( ajaxContext ) {
				//console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() { }
		});
		
	});
	
	self.manage_dialog_create_wpa_button_labels = function() {
		if ( self.dialog_create_or_change_usage == 'create' ) {
			$( '.js-wpv-create-new-wpa .ui-button-text' ).html( wpa_listing_texts.dialog_create_action );
		} else if ( self.dialog_create_or_change_usage == 'change' ) {
			$( '.js-wpv-create-new-wpa .ui-button-text' ).html( wpa_listing_texts.dialog_change_usage_action );
		}
	};
	
	/**
     * This happens when user clicks on the "Change WordPress Archive" action link on the "listing by usage" page.
     *
     */
    $( document ).on( 'click', '.js-wpv-wpa-usage-action-change-usage', function( e ) {
        e.preventDefault();
        
		var thiz = $( this ),
		data_view_id = thiz.data( 'view-id' ),// This is actually a slug of the loop.
		dialog_height = $( window ).height() - 100,
		data = {
			action: 'wpv_change_wpa_for_archive_loop_popup',
			id: data_view_id,
			wpnonce : $('#wpv_wp_archive_arrange_usage').val()
		};
		
		self.dialog_change_wpa_for_archive_loop.dialog( "open" ).dialog({
            width: 770,
            maxHeight: dialog_height,
            draggable: false,
            resizable: false,
			position: { my: "center top+50", at: "center top", of: window }
        });
		
		self.dialog_change_wpa_for_archive_loop.html( self.shortcodeDialogSpinnerContent );
		
		$.ajax({
			async: false,
			type: "GET",
			dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( response.success ) {
					self.dialog_change_wpa_for_archive_loop.html( response.data.dialog_content );
				}
			},
			error: function( ajaxContext ) {
				//console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() { }
		});
		
    });
	
	/**
	 * This happens when user confirms updating assigned WordPress Archive for a loop.
	 */ 
	$( document ).on( 'click', '.js-wpv-change-wpa-for-archive-loop', function( e ) {
		e.preventDefault();
		
		var thiz = $( this ),
		data = {
			action: 'wpv_change_wpa_for_archive_loop',
			selected: $( 'input[name=wpv-view-loop-archive]:checked', '#js-wpv-change-wpa-for-archive-loop-list' ).val(),
			loop: $( '#js-wpv-change-wpa-for-archive-loop-key' ).val(),
			wpnonce : $( '#wpv_wp_archive_arrange_usage' ).val()
		};
		
		disablePrimaryButton( thiz );
		showSpinnerBefore( thiz );
		
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
			error: function( ajaxContext ) {
				//console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() {

			}
		});
	});
	
	/**
	* -----------------
	* Bulk actions
	* -----------------
	*/
	
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
				self.maybeTrashWPAs( checkedWPAs, nonce );
				break;
			case 'restore-from-trash':
				untrashViews( checkedWPAs, nonce );
				break;
			case 'delete':
				self.deleteWPAConfirmation( checkedWPAs, nonce );
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
	self.maybeTrashWPAs = function( wpaIDs, nonce ) {

		var data = {
			action: 'wpv_archive_check_usage',
			ids: wpaIDs,
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
					if ( response.data.used_wpa_ids.length == 0 ) {
						trashViews( wpaIDs, nonce, true );
					} else {
						var dialog_height = $( window ).height() - 100;
						self.bulk_trashing_ids = wpaIDs;
						self.dialog_bulk_trash_wpa.dialog( "open" ).dialog({
							width: 770,
							maxHeight: dialog_height,
							draggable: false,
							resizable: false,
							position: { my: "center top+50", at: "center top", of: window }
						});
						self.dialog_bulk_trash_wpa.html( response.data.dialog_content )
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
	 * Trash archives (and unassign them from archive loops) after confirmation.
	 *
	 * @since 1.7
	 *
	 * @todo comment
	 */ 
	$( document ).on( 'click', '.js-bulk-trash-wpa-confirm', function( e ) {
		e.preventDefault();
		
		var thiz = $( this );
		
		disablePrimaryButton( thiz );
		showSpinnerBefore( thiz );
		
		var wpaIDs = self.bulk_trashing_ids;
		var nonce = wpa_listing_texts.dialog_bulktrash_nonce;

		trashViews( wpaIDs, nonce, true );
	});


	/**
	 * Show a popup with confirmation message. 
	 *
	 * Archives are deleted after clicking on .js-wpv-bulk-remove-wpa-permanent.
	 *
	 * @since 1.7
	 */
	self.deleteWPAConfirmation = function( wpaIDs, nonce ) {
	
		// Do AJAX call to generate popup code
		/*
		var data = {
			action: 'wpv_archive_bulk_delete_render_popup',
			ids: wpaIDs,
			wpnonce : nonce
		};
		*/
		var dialog_height = $( window ).height() - 100;
		
		self.bulk_deleting_ids = wpaIDs;
		
		self.dialog_bulk_delete_wpa.dialog( "open" ).dialog({
            maxHeight: dialog_height,
            draggable: false,
            resizable: false,
			position: { my: "center top+50", at: "center top", of: window }
        });

	};


	/**
	 * Permanently delete given Archives and redirect to current page with 'deleted' message.
	 *
	 * @since 1.7
	 */
	$( document ).on( 'click', '.js-wpv-bulk-remove-wpa-permanent', function( e ) {
		e.preventDefault();
		
		var thiz = $( this ),
		data = {
			action: 'wpv_bulk_delete_views_permanent',
			ids: self.bulk_deleting_ids,
			wpnonce : wpa_listing_texts.dialog_bulkdel_nonce
		};
		
		disablePrimaryButton( thiz );
		showSpinnerBefore( thiz );

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
					var affectedItemCount = self.bulk_deleting_ids.length;
					url_params['paged'] = updatePagedParameter( url_params, affectedItemCount );
					url_params['deleted'] = affectedItemCount;
					navigateWithURIParams( url_params );
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
	* -----------------
	* Init dialogs
	* -----------------
	*/
	
	self.init_dialogs = function() {
		
		$( 'body' ).append( '<div id="js-wpv-create-wpa-form-dialog" class="toolset-shortcode-gui-dialog-container wpv-shortcode-gui-dialog-container js-wpv-shortcode-gui-dialog-container"></div>' );
		
		self.dialog_create_wpa = $( "#js-wpv-create-wpa-form-dialog" ).dialog({
			autoOpen: false,
			modal: true,
			title: wpa_listing_texts.dialog_create_dialog_title,
			minWidth: 600,
			show: { 
				effect: "blind", 
				duration: 800 
			},
			open: function( event, ui ) {
				$( 'body' ).addClass( 'modal-open' );
				self.manage_dialog_create_wpa_button_labels();
				disablePrimaryButton( $( '.js-wpv-create-new-wpa' ) );
			},
			close: function( event, ui ) {
				$( 'body' ).removeClass( 'modal-open' );
				self.dialog_create_or_change_usage = '';
			},
			buttons:[
				{
					class: 'button-secondary',
					text: wpa_listing_texts.dialog_cancel,
					click: function() {
						$( this ).dialog( "close" );
					}
				},
				{
					class: 'button-primary js-wpv-create-new-wpa',
					text: wpa_listing_texts.dialog_create_action,
					click: function() {

					}
				}
			]
		});
		
		self.dialog_delete_wpa = $( "#js-wpv-delete-wpa-dialog" ).dialog({
			autoOpen: false,
			modal: true,
			title: wpa_listing_texts.dialog_delete_dialog_title,
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
				self.deleting_id = 0;
			},
			buttons:[
				{
					class: 'button-secondary',
					text: wpa_listing_texts.dialog_cancel,
					click: function() {
						$( this ).dialog( "close" );
					}
				},
				{
					class: 'button-primary js-wpv-remove-wpa-permanent',
					text: wpa_listing_texts.dialog_delete_action,
					click: function() {

					}
				}
			]
		});
		
		self.dialog_create_wpa_for_archive_loop = $( "#js-wpv-create-wpa-for-archive-loop-dialog" ).dialog({
			autoOpen: false,
			modal: true,
			title: wpa_listing_texts.dialog_create_wpa_for_archive_loop_dialog_title,
			minWidth: 600,
			show: { 
				effect: "blind", 
				duration: 800 
			},
			open: function( event, ui ) {
				$( 'body' ).addClass( 'modal-open' );
				$( '.js-wpv-create-wpa-for-archive-loop-hint' ).html( self.creating_archive_loop_title );
				$( '.js-wpv-create-wpa-for-archive-loop-title' ).val( self.creating_archive_loop_title );
			},
			close: function( event, ui ) {
				$( 'body' ).removeClass( 'modal-open' );
				self.creating_archive_loop_title = '';
				self.creating_archive_loop = '';
				
			},
			buttons:[
				{
					class: 'button-secondary',
					text: wpa_listing_texts.dialog_cancel,
					click: function() {
						$( this ).dialog( "close" );
					}
				},
				{
					class: 'button-primary js-wpv-add-wp-archive-for-loop',
					text: wpa_listing_texts.dialog_create_wpa_for_archive_loop_action,
					click: function() {

					}
				}
			]
		});
		
		$( 'body' ).append( '<div id="js-wpv-change-wpa-for-archive-loop-dialog" class="toolset-shortcode-gui-dialog-container wpv-shortcode-gui-dialog-container js-wpv-shortcode-gui-dialog-container"></div>' );
		
		self.dialog_change_wpa_for_archive_loop = $( "#js-wpv-change-wpa-for-archive-loop-dialog" ).dialog({
			autoOpen: false,
			modal: true,
			title: wpa_listing_texts.dialog_change_wpa_for_archive_loop_dialog_title,
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
			},
			buttons:[
				{
					class: 'button-secondary',
					text: wpa_listing_texts.dialog_cancel,
					click: function() {
						$( this ).dialog( "close" );
					}
				},
				{
					class: 'button-primary js-wpv-change-wpa-for-archive-loop',
					text: wpa_listing_texts.dialog_change_wpa_for_archive_loop_action,
					click: function() {

					}
				}
			]
		});
		
		$( 'body' ).append( '<div id="js-wpv-bulk-trash-wpa-dialog" class="toolset-shortcode-gui-dialog-container wpv-shortcode-gui-dialog-container js-wpv-shortcode-gui-dialog-container"></div>' );
		
		self.dialog_bulk_trash_wpa = $( "#js-wpv-bulk-trash-wpa-dialog" ).dialog({
			autoOpen: false,
			modal: true,
			title: wpa_listing_texts.dialog_bulk_trash_dialog_title,
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
				self.bulk_trashing_ids = [];
			},
			buttons:[
				{
					class: 'button-secondary',
					text: wpa_listing_texts.dialog_cancel,
					click: function() {
						$( this ).dialog( "close" );
					}
				},
				{
					class: 'button-primary js-bulk-trash-wpa-confirm',
					text: wpa_listing_texts.dialog_bulk_trash_action,
					click: function() {

					}
				}
			]
		});
		
		self.dialog_bulk_delete_wpa = $( "#js-wpv-bulk-delete-wpa-dialog" ).dialog({
			autoOpen: false,
			modal: true,
			title: wpa_listing_texts.dialog_bulk_delete_dialog_title,
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
				self.bulk_deleting_ids = [];
			},
			buttons:[
				{
					class: 'button-secondary',
					text: wpa_listing_texts.dialog_cancel,
					click: function() {
						$( this ).dialog( "close" );
					}
				},
				{
					class: 'button-primary js-wpv-bulk-remove-wpa-permanent',
					text: wpa_listing_texts.dialog_delete_action,
					click: function() {

					}
				}
			]
		});
		
	};
	
	self.init = function() {
		$('.js-list-views-action option').removeAttr('selected');
		self.init_dialogs();
	};
	
	self.init();
	
};

jQuery( document ).ready( function( $ ) {

    WPViews.wpa_listing_screen = new WPViews.WPAListingScreen( $ );

});
