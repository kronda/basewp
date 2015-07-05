jQuery(function($) {
	// @todo transform this to OOP model
	// @todo FGS, please avoid using those awfull globals!!!!
	var query = '';
	var data_ct_id = null;


	// Process: assign selected content template to all posts types
	$(document).on('click','.js-wpv-content-template-update-posts-process', function() {
		var type = $(this).data('type');
		var tid = $(this).data('id');
		var data = {
			action : 'set_view_template_listing',
			view_template_id : tid,
			wpnonce : $('#work_view_template').attr('value'),
			type : type,
			lang : ''
		};
		$.post(ajaxurl, data, function(response) {
			$.colorbox({
                 html: response,
                 onClose: function() {

                 }
             });
			$('.js-alret-icon-hide-'+type).hide()
		});
	});


	// Proccess change post type for template
	$(document).on('click','.js-ct-change-type-process', function(e) {
		e.preventDefault();
		showSpinnerBefore( $(this) );
		disablePrimaryButton( $(this) );
        var type = [];
        $("input[name='wpv-new-content-template-post-type[]']:checked").each(function(){type.push($(this).val());});
        var id = $(this).data('id');
        var data = {
             action : 'ct_change_types_process',
             view_template_id : id,
             wpnonce : $('#work_view_template').attr('value'),
             type : type
        };
          $.post(ajaxurl, data, function(response) {
		if ( (typeof(response) !== 'undefined') ) {
			if ( response == 'ok' ) {
				  navigateWithURIParams(decodeURIParams());
			} else {
				  console.log( "Error: AJAX returned ", response );
			}
		} else {
			console.log( "Error: AJAX returned ", response );
		}
          });
          return false;
   });

	// Search box action
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
	 * Bulk action.
	 *
	 * Fires when user hits the Apply button near bulk action select field.
	 *
	 * @since 1.7
	 */
	$('.js-wpv-ct-listing-bulk-action-submit').on('click', function(e) {
		e.preventDefault();

		showSpinner();

		// Get an array of checked View IDs.
		var checkedCTs = $('.wpv-admin-listing-col-bulkactions input:checkbox:checked').map(function() {
			var value = $(this).val();
			// Filter out values of checkboxes in table header and footer rows.
			if($.isNumeric(value)) {
				return value;
			}
		}).get();

		// If there are no items selected, do nothing.
		if( checkedCTs.length == 0 ) {
			hideSpinner();
			return;
		}

		// nonce
		var nonce = $(this).data('viewactionnonce');

		// Get a position. That's important to determine which select field is relevant for us.
		var selectPosition = $(this).data('position');

		// Launch appropriate bulk action
		var action = $('.js-wpv-ct-listing-bulk-action-select.position-' + selectPosition).val();
		switch(action) {
			case 'trash':
				trashCTs( checkedCTs, nonce );
				break;
			case 'restore-from-trash':
				untrashCTs( checkedCTs, nonce );
				break;
			case 'delete':
				deleteCTsConfirmation( checkedCTs, nonce );
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
	 * @param array ctIDs An array of Content Template IDs that should be untrashed.
	 * @param string nonce A valid wpv_view_listing_actions_nonce.	 
	 */ 
	function trashCTs( ctIDs, nonce ) {
	
		var data = {
			action: 'wpv_bulk_content_templates_move_to_trash',
			ids: ctIDs,
			wpnonce : nonce
		};

		$.ajax({
			async: false,
			type: "POST",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( typeof(response) !== 'undefined' ) {
					response = jQuery.parseJSON( response );
					if( 'action' in response ) {
						if( 'trashed' == response.action ) {
							// CTs have been trashed. Reload the page (with a message).
							var url_params = decodeURIParams();
							var affectedItemCount = response.all_ids.length;
							url_params['paged'] = updatePagedParameter( url_params, affectedItemCount, '.js-wpv-ct-list-row' );
							url_params['trashed'] = affectedItemCount;
							url_params['affected'] = response.all_ids;
							navigateWithURIParams( url_params );
							return; // no error
						} else if( 'confirm' == response.action ) {
							// Confirmation needed. Show the popup which we've been given in response.
							hideSpinner();
							$.colorbox({
								html: response.popup_content,
								onClose: function() { },
								onComplete: function() {
									// because we're going to enable it after user inputs all neccessary data.
									disablePrimaryButton( $('.js-ct-bulk-replace-usage') );
								}
							});
							return; // no error
						}
					}
				}
				console.log( "Error: AJAX returned ", response );
			},
			error: function( ajaxContext ) {
				console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() { }
		});
	}


	/**
	 * This happen when in "bulk replace content templates" dialog, user focuses the select field with
	 * replacement content templates. We'll appropriately select the option to replace the template with existing one.
	 *
	 * @since 1.7
	 */ 
	$(document).on( 'focus', '.js-wpv-bulk-ct-list-for-replace', function(e) {
		var templateId = $(this).data( 'template-id' );
		$('.js-wpv-bulk-existing-posts-ct-replace-to-selected-ct[name=wpv-content-template-replace-' + templateId + '-to]').prop( 'checked', true );
		checkBulkTrashWithReplaceDialogInput();
	});


	/**
	 * This happens when in "bulk replace content templates" dialog, user changes a selected replacement template.
	 * 
	 * @since 1.7
	 */ 
	$(document).on( 'change', '.js-wpv-bulk-ct-list-for-replace', function(e) {
		checkBulkTrashWithReplaceDialogInput();
	});


	/**
	 * This happens when in "bulk replace content templates" dialog, user changes the desired action for a content
	 * template (replace with existing template / set none).
	 *
	 * @since 1.7
	 */
	$(document).on('change','.js-wpv-bulk-existing-posts-ct-replace-to', function(e) {
		checkBulkTrashWithReplaceDialogInput();
	});
	
	

	/**
	 * In "bulk replace content templates" dialog, checks that all inputs are set. That means:
	 * - user has to choose action for each template that is being used and
	 * - if "replace with existing template" is selected, a template also must be chosen.
	 *
	 * If these conditions are met, submit button is enabled, otherwise it is disabled.
	 *
	 * @since 1.7
	 */ 
	function checkBulkTrashWithReplaceDialogInput() {
		var submitButton = $('.js-ct-bulk-replace-usage');
		disablePrimaryButton( submitButton );

		// Content templates that are used and should be replaced.
		var replaceIDs = decodeURIComponent( submitButton.data( 'replace-ids' ) ).split( ',' );

		var isAllSet = true;

		// Check that all required inputs are set
		for( var i = 0; i < replaceIDs.length && isAllSet; ++i ) {
			var templateId = replaceIDs[ i ];
			var action = $('input[name=wpv-content-template-replace-' + templateId + '-to]:checked').val();
			if( 'different_template' == action ) {
				// do we have a template selected?
				var replacementTemplateId = $('#wpv-ct-list-for-replace-' + templateId).val();
				if( '' == replacementTemplateId ) {
					// template not selected
					isAllSet = false;
				}
			} else if( 'no_template' == action ) {
				// ok
			} else {
				// value not set
				isAllSet = false;
			}
		}

		if( isAllSet ) {
			enablePrimaryButton( submitButton );
		}
	}


	/**
	 * This happens when in "bulk replace content templates" dialog user clicks on the submit button.
	 *
	 * Collects input from the dialog and invokes wpv_ct_bulk_trash_with_replace, passing
	 * - ids: an array of IDs of all templates that should be trashed
	 * - toreplace: dtto, templates that should be replaced
	 * - replacements: dtto, replacement templates (same lenght and order as toreplace)
	 *
	 * Expected AJAX response is '1', after which the page will be reloaded with a 'trashed' message.
	 *
	 * @see wpv_ct_bulk_trash_with_replace_callback()
	 *
	 * @since 1.7
	 */
	$(document).on( 'click', '.js-ct-bulk-replace-usage', function(e) {

		showSpinnerBefore( $(this) );
		
		var nonce = $(this).data( 'nonce' );
		
		// All content templates that are going to be trashed
		var ctIDs = decodeURIComponent( $(this).data( 'ct-ids' ) ).split( ',' );

		// Content templates that are used and should be replaced.
		var replaceIDs = decodeURIComponent( $(this).data( 'replace-ids' ) ).split( ',' );

		/* This will hold IDs of template replacements (in the same order as in replaceIDs). Value 0 indicates
		 * 'don't use any content template'. */
		var replacements = [];

		for( var i = 0; i < replaceIDs.length; ++i ) {
			var templateId = replaceIDs[ i ];
			var action = $('input[name=wpv-content-template-replace-' + templateId + '-to]:checked').val();
			if( 'different_template' == action ) {
				// user has selected to replace this template with another one
				var replacementTemplateId = $('#wpv-ct-list-for-replace-' + templateId).val();
				replacements[ i ] = replacementTemplateId;
			} else {
				// user has selected not to replace this template with anything
				replacements[ i ] = 0;
			}
		}

		var data = {
			action: 'wpv_ct_bulk_trash_with_replace',
			ids: ctIDs,
			wpnonce : nonce,
			toreplace: replaceIDs,
			replacements: replacements
		};

		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: data,
			success: function(response) {
				if ( (typeof(response) !== 'undefined') && (response == 1) ) {
					// response == 1 means success
					var url_params = decodeURIParams();
					var affectedItemCount = ctIDs.length;
					url_params['paged'] = updatePagedParameter( url_params, affectedItemCount, '.js-wpv-ct-list-row' );
					url_params['trashed'] = affectedItemCount;
					url_params['affected'] = ctIDs;
					navigateWithURIParams( url_params );
				} else {
					console.log( "Error: AJAX returned ", response );
				}
			},
			error: function (ajaxContext) {
				console.log( "Error: ", ajaxContext.responseText );
			}
		});
	});

	

	/**
	 * Restore Content Templates from trash.
	 *
	 * Invokes wpv_view_bulk_change_status, changing post status of given templates to 'publish'.
	 * Afterwards reloads the page with an additional parameter for the 'untrashed' message.
	 *
	 * @since 1.7
	 *
	 * @param array ctIDs An array of Content Template IDs that should be untrashed.
	 * @param string nonce A valid wpv_view_listing_actions_nonce.
	 */
	function untrashCTs( ctIDs, nonce ) {
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
	}


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
	function deleteCTsConfirmation( ctIDs, nonce ) {
	
		var data = {
			action: 'wpv_ct_bulk_count_usage',
			ids: ctIDs,
			wpnonce : nonce
		};


		// Display singular or plural message.
		// We're adjusting this here, so that colorbox shows in a size which corresponds to actually visible content.
		var ctCount = ctIDs.length;
		if( ctCount == 1 ) {
			$('.js-singular').show();
			$('.js-plural').hide();
		} else {
			$('.js-plural').show();
			$('.js-singular').hide();
		}


		$.ajax({
			async: false,
			type: "POST",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				response = $.parseJSON( response )
				if( 0 in response ) {
					$.colorbox({
						href: '.js-bulk-remove-content-templates-dialog',
						inline: true,
						onComplete: function() {
							// Adjust the dialog
							var postsUsingCTs = response['0'];
							if( postsUsingCTs > 0 ) {
								// Insert total count of posts that use any of CTs that are to be deleted.
								$('.js-ct-single-postcount').html( postsUsingCTs );
							} else {
								$('.js-ct-single-postcount-message-usage').hide();
								$('.js-ct-single-postcount-message-ays-nonzero').hide();
								$('.js-ct-single-postcount-message-ays-zero').show();
							}
							// Insert nonce and CT IDs into the colorbox.
							$('.js-bulk-remove-templates-permanent').attr( 'data-ct-ids', encodeURIComponent( ctIDs.join() ) );
							$('.js-bulk-remove-templates-permanent').attr( 'data-viewactionnonce', nonce );

							hideSpinner();
						}
					});
				} else {
					console.log( "Error: invalid output", response );
				}
			},
			error: function( ajaxContext ) {
				console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() { }
		});
	}


	/**
	 * Bulk delete content templates after confirmation.
	 *
	 * Invokes the wpv_ct_bulk_delete() function by AJAX.
	 *
	 * @since 1.7
	 */  
	$(document).on('click','.js-bulk-remove-templates-permanent', function(e) {
		e.preventDefault();
		showSpinnerAfter( $(this) );
		disablePrimaryButton( $(this) );

		ctIDs = decodeURIComponent( $(this).data( 'ct-ids' ) ).split( ',' );
		nonce = $(this).data( 'viewactionnonce' );

		var data = {
			action: 'wpv_ct_bulk_delete',
			ids: ctIDs,
			wpnonce : nonce
		};

		$.ajax({
			async: false,
			type: "POST",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( (typeof( response ) !== 'undefined') && ( response == 1 ) ) {
					var url_params = decodeURIParams();
					var affectedItemCount = ctIDs.length;
					url_params['paged'] = updatePagedParameter( url_params, affectedItemCount, '.js-wpv-ct-list-row' );
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
	 * This happens on "Delete" action for a single content template.
	 *
	 * We'll show the same confirmation dialog as for bulk action.
	 *
	 * @since unknown
	 */
	$(document).on('click','.js-list-ct-action-delete', function(e) {
		e.preventDefault();
		showSpinner();

		var ctID = $(this).attr('data-ct-id');
		var nonce = $(this).data('viewactionnonce');

		// Show confirmation, act like if it's a bulk action.
		deleteCTsConfirmation( [ ctID ], nonce );
	});


	/**
	 * This happens when user clicks on the Duplicate action link.
	 *
	 * @since unknown
	 */
	$( document ).on( 'click','.js-list-ct-action-duplicate', function( e ) {
		e.preventDefault();
		
		var ctId = $(this).data( 'ct-id' );
		var originalTitle = $(this).data( 'ct-name' );
		
		$('.js-wpv-duplicate-error-container .toolset-alert').remove();
		disablePrimaryButton( $('.js-wpv-duplicate-ct') );
		
		$.colorbox({
			href: '.js-wpv-duplicate-ct-dialog',
			inline: true,
			onComplete: function() {

				// Show name of the original
				$('.js-duplicate-origin-title').append( originalTitle );

				// Store Content Template ID in a confirm button attribute
				$('.js-wpv-duplicate-ct').data( 'ct-id', ctId );

				$('.js-wpv-duplicated-title').focus().val('');
				
				$('.js-wpv-duplicated-title').keyup(function() {
					$('.js-wpv-duplicate-error-container .toolset-alert').remove();
					if ( $(this).val().length !== 0 ) {
						enablePrimaryButton( $('.js-wpv-duplicate-ct') );
					} else {
						disablePrimaryButton( $('.js-wpv-duplicate-ct') );
					}
				});
			}
		});
	});


	/**
	 * Duplicate a Content Template.
	 *
	 * @since unknown
	 */ 
	$( document ).on( 'click','.js-wpv-duplicate-ct', function ( e ) {
		e.preventDefault();

		showSpinnerBefore( $(this) );
		disablePrimaryButton( $(this) );
		
		var newName = $( '.js-wpv-duplicated-title' ).val();
		var nonce = $('#work_view_template').attr('value');
		var ctID = $(this).data('ct-id');
		
		if ( newName.length !== 0 ) {

			var data = {
				action: 'wpv_duplicate_ct',
				id: ctID,
				wpnonce : nonce,
				title: newName
			};
			
			$.ajax({
				async: false,
				type: "POST",
				url: ajaxurl,
				data: data,
				success: function( response ) {
					if ( ( typeof( response ) !== 'undefined' ) ) {
						response = $.parseJSON( response );
						if ( response[0] == 'error' ) {
							$('.js-wpv-duplicate-error-container').wpvToolsetMessage({
								text: response[1],
								stay: true,
								close: false,
								type: ''
							});
							hideSpinner();
						}
						else if ( response[0] == 'ok' ) {
							navigateWithURIParams( decodeURIParams() );
						} else {
							console.log( "Error: AJAX returned ", response );
						}
					} else {
						console.log( "Error: AJAX returned ", response );
					}
				}
			});
		}
	});


	/** Load list of Post Types/Taxonomies for assing to template */
	$(document).on('click','.js-list-ct-action-change', function(e) {
		e.preventDefault();
		data_ct_id = $(this).attr('data-ct-id');
		view_listing_action_nonce = $(this).data('viewactionnonce');

		e.preventDefault();
		var $thiz = $(this);
		$.colorbox({
			href: ajaxurl+'?wpnonce='+$('#work_view_template').attr('value')+'&action=ct_change_types&id='+data_ct_id,
			inline : false,
			onComplete: function() { }
		});
	});


	/** Move to trash and reload the page. */
	$(document).on('click','.js-list-ct-action-trash', function(e) {
		e.preventDefault();
		data_ct_id = $(this).attr('data-ct-id');
		view_listing_action_nonce = $(this).data('viewactionnonce');

		$(this).parents('.js-wpv-ct-list-row').find('h3').append(' <div class="spinner ajax-loader"></div>');
		$('.subsubsub').append('<div class="spinner ajax-loader"></div>');

		var data = {
			action: 'wpv_content_template_move_to_trash',
			id: data_ct_id,
			newstatus: 'trash',
			wpnonce : view_listing_action_nonce
		};

		$.ajax({
			async:false,
			type:"POST",
			url:ajaxurl,
			data:data,
			success:function(response){
				if ( (typeof(response) !== 'undefined')) {
					response = jQuery.parseJSON(response);

					if ( response[0] == 'move' ){
						// Move CT to trash
						// response[1] is ID of the CT
						var url_params = decodeURIParams();
						url_params['paged'] = updatePagedParameter( url_params, 1, '.js-wpv-ct-list-row' );
						url_params['trashed'] = 1;
						url_params['affected'] = response[1];
						navigateWithURIParams(url_params);
					} else{
						// response[0] == "show" and response[1] is html for colorbox
						// Show popup
						$('.ajax-loader').remove();
						$.colorbox({
							html: response[1],
							onClose: function() { },
							onComplete: function() {
								$('.js-ct-replace-usage').prop('disabled',true).removeClass('button-primary').addClass('button-secondary');
								$('.js-ct-replace-usage').data('view_listing_action_nonce',view_listing_action_nonce);
							}
						});
					}
				} else {
					console.log( "Error: AJAX returned ", response );
				}
			},
			error: function (ajaxContext) {
				console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() { }
		},'json');
	});


	/** Restore content template from trash. */
	$(document).on('click','.js-list-ct-action-restore-from-trash', function(e) {
		e.preventDefault();
		data_ct_id = $(this).attr('data-ct-id');
		view_listing_action_nonce = $(this).data('viewactionnonce');

		$(this).parents('.js-wpv-ct-list-row').find('h3').append(' <div class="spinner ajax-loader"></div>');
		$('.subsubsub').append('<div class="spinner ajax-loader"></div>');
		var data = {
			action: 'wpv_view_change_status',
			id: data_ct_id,
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
					url_params['paged'] = updatePagedParameter( url_params, 1, '.js-wpv-ct-list-row' );
					url_params['untrashed'] = 1;
					navigateWithURIParams(url_params);
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


	/** Assign Content template to Post type/Taxonomy */
	$(document).on('click','.js-list-ct-action-change-pt', function(e) {
		e.preventDefault();
		data_pt = $(this).attr('data-pt');
		data_msg = $(this).attr('data-msg');
		data_sort = $(this).attr('data-sort');

		e.preventDefault();
		$.colorbox({
			href: ajaxurl
				+'?wpnonce='+$('#work_view_template').attr('value')
				+'&action=ct_change_types_pt&pt='+data_pt
				+'&msg='+data_msg
				+'&sort='+data_sort,
			inline : false,
			onComplete: function() { }
		});
	});


	/**
	 * Undo "trash" action when user clicks on the Undo link.
	 */ 
	$(document).on('click', '.js-wpv-untrash', function(e){
		e.preventDefault();
		showSpinnerAfter( $(this) );

		var nonce = $(this).data( 'nonce' );
		var ctIDs = decodeURIComponent( $(this).data( 'ids' ) ).split( ',' );

		untrashCTs( ctIDs, nonce );
	});


    //Proccess Post type/Taxonomy assign to template
    $(document).on('click','.js-ct-change-types-pt-process', function(e){
     	e.preventDefault();
		showSpinnerAfter( $(this) );
		disablePrimaryButton( $(this) );
		
	    var $thiz = $(this);
	    var sort = $thiz.data('sort');
	    var pt = $thiz.data('pt');
	    var value = $('input[name=wpv-new-post-type-content-template]:checked').val();
	
	    var data = {
	        action : 'ct_change_types_pt_process',
	        sort : sort,
	        pt: pt,
	        value : value,
	        wpnonce : $('#work_view_template').attr('value')
	    };
	    $.post(ajaxurl, data, function(response) {
	            $.colorbox.close();
	            $('.js-wpv-views-listing-body tr.js-wpv-ct-list-row').remove();
	            $('.js-wpv-views-listing-body').prepend(response);
	            $("html, body").animate({ scrollTop: 0 }, "slow");
	    });
	    return false;
	});

	//Unlink Content Template for existing single posts - popup

	$(document).on('click', '.js-single-unlink-template-open-dialog', function(e){
		e.preventDefault();
		var singletype = $(this).data('slug'),
		       singlelabel = $(this).data('label'),
		       singlenumber = $(this).data('unclear');
		$.colorbox({
			href: $('.js-single-unlink-template-dialog'),
			inline : true,
			onComplete: function() {
				$('.js-single-unlink-label').each(function(){
					$(this).html(singlelabel);
				});
				$('.js-single-unlink-number').each(function(){
					$(this).html(singlenumber);
				});
				$('.js-single-unlink-template-ok').data('slug', singletype);
			}
		});
		return false;

	});

	// Unlink Contnt Template for existing single posts - action

	$(document).on('click', '.js-single-unlink-template-ok', function(e){
		e.preventDefault();
		showSpinnerAfter( $(this) );
		var singletype = $(this).data('slug'),
		       nonce = $(this).data('nonce');
		disablePrimaryButton( $(this) );
		var data = {
			action: 'wpv_single_unlink_template',
			wpnonce: nonce,
			slug: singletype
		};
		$.ajax({
			async:false,
			type:"POST",
			url:ajaxurl,
			data:data,
			success:function(response){
				if ( (typeof(response) !== 'undefined') && (response == 'ok')) {
					var url_params = decodeURIParams();
					navigateWithURIParams(url_params);
				} else {
					console.log( "Error: AJAX returned ", response );
				}
			},
			error: function (ajaxContext) {
				hideSpinner();
				$(this).addClass('button-primary').removeClass('button-secondary').prop('disabled', false);
				console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() {

			}
		});

	});


	//Open popup create new content template
	$(document).on('click','.js-add-new-content-template', function(e){
		e.preventDefault();
		showSpinnerAfter(  $(this) );
		var $thiz = $(this);
		$.colorbox({
			href: $thiz.data('target') +'&wpnonce='+$('#work_view_template').attr('value'),
			inline : false,
			reposition: true,
			onComplete: function() {
				 hideSpinner();
				 if ( !$thiz.data('disabled') ){
		  			$('.js-wpv-create-new-template').prop('disabled', true).addClass('button-secondary').removeClass('button-primary');
				 }
				$('.js-wpv-create-new-template').live("propertyChanged", function(event, args, val ){

					if( args == 'disabled' && val == true )
					{
						$(event.target).addClass('button-secondary').removeClass('button-primary');
					}
					else if( args == 'disabled' && val == false )
					{
						$(event.target).removeClass('button-secondary').addClass('button-primary');
					}
				});
			}
		});
		return false;
	});

	$(document).on('click','.js-ct-change-types-pt,.js-apply-for-all-posts', function(e){
	    e.preventDefault();
	    var $thiz = $(this);
	    $.colorbox({
	        href: $thiz.data('target') + '&wpnonce='+$('#work_view_template').attr('value'),
	        inline : false,
	        onComplete: function() {

	        }
	    });
	    return false;
	});


	$(document).on('input','.js-wpv-new-content-template-name', function(e){
		$('.js-error-container .toolset-alert').remove();
		if ($(this).val() === ""){
		 $('.js-wpv-create-new-template').prop('disabled',true);
		}
		else{
		 $('.js-wpv-create-new-template').prop('disabled',false);
		}
	});


	$(document).on('change','.js-wpv-dialog-add-new-content-template input[type=checkbox]',function(e){
		var $dontAssignInput = $('.js-dont-assign');
		var $allCheckboxes = $('.js-wpv-dialog-add-new-content-template input[type=checkbox]');
		if ( $(this).is(':checked') ) {
			if ( $(e.target).is($dontAssignInput) ) {
				$allCheckboxes.not(this).prop('checked',false)
			}
			else {
				$dontAssignInput.prop('checked',false)
			}
		}
	});

	//Dropdown for Post Types/Taxonomies
	$(document).on('click','.js-wpv-content-template-open', function(e) {
		e.preventDefault();
		var $dropdownList = $(this).parent().next('.js-wpv-content-template-dropdown-list');
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
			$.colorbox.resize();
		});
		return false;
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
							type: ''
						});
				 	thiz.prop( 'disabled', false );
					hideSpinner();
				} else {
				 	// console.log('Content Template Created');
				 	document.location.href = 'post.php?post=' + response[0] + '&action=edit';
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

	
	$(document).on('focus','.js-wpv-ct-list-for-replace', function(e) {
		$('.js-wpv-existing-posts-ct-replace-to-selected-ct').prop('checked',true);
	});

	$(document).on('change','.js-wpv-ct-list-for-replace', function(e) {
		$('.js-ct-replace-usage').prop('disabled',true).removeClass('button-primary').addClass('button-secondary');
		if ( $('.js-wpv-ct-list-for-replace').val() !== ''){
			$('.js-ct-replace-usage').prop('disabled',false).addClass('button-primary').removeClass('button-secondary');
		}
	});

	$(document).on('change','.js-wpv-existing-posts-ct-replace-to', function(e) {
		$('.js-ct-replace-usage').prop('disabled',true).removeClass('button-primary').addClass('button-secondary');
		if ( $('.js-wpv-existing-posts-ct-replace-to:checked').val() == 0 ){
			if ( $('.js-wpv-ct-list-for-replace').val() !== ''){
				$('.js-ct-replace-usage').prop('disabled',false).addClass('button-primary').removeClass('button-secondary');
			}
		}else{
			$('.js-ct-replace-usage').prop('disabled',false).addClass('button-primary').removeClass('button-secondary');
		}
	});

	$(document).on('click','.js-ct-replace-usage', function(e) {
		showSpinnerBefore( $(this) );
		var data = {
			action: 'wpv_ct_move_with_replace',
			ct_id:  $(this).data('ct_id'),
			wpnonce : $(this).data('view_listing_action_nonce'),
			replace_to: $('.js-wpv-existing-posts-ct-replace-to:checked').val(),
			replace_ct: $('.js-wpv-ct-list-for-replace').val(),
			id: data_ct_id // get global data_ct_id
		};

		$.ajax({
			type:"POST",
			url:ajaxurl,
			data:data,
			success:function(response){
				if ( (typeof(response) !== 'undefined') && (response == data_ct_id)) {
					var url_params = decodeURIParams();
					url_params['paged'] = updatePagedParameter( url_params, 1, '.js-wpv-ct-list-row' );
					url_params['trashed'] = 1;
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

		return false;
	});



});
