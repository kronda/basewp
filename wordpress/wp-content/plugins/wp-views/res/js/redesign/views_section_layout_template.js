var WPViews = WPViews || {};

WPViews.ViewEditScreenInlineCT = function( $ ) {
	
	var self = this;
	
	self.view_id = $('.js-post_ID').val();
	self.current_ct_id = 0;
	self.current_ct_container = null;
	
	self.codemirror_highlight_options = {
		className: 'wpv-codemirror-highlight'
	};
	self.spinner = '<span class="wpv-spinner ajax-loader"></span>&nbsp;&nbsp;';
	
	self.edit_screen = ( typeof WPViews.view_edit_screen != 'undefined' ) ? WPViews.view_edit_screen : WPViews.wpa_edit_screen;
	
	self.shortcodeDialogSpinnerContent = $(
        '<div style="min-height: 150px;">' +
            '<div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; ">' +
                '<div class="wpv-spinner ajax-loader"></div>' +
                '<p>' + wpv_inline_templates_strings.loading_options + '</p>' +
            '</div>' +
        '</div>'
    );
	
	// ---------------------------------
	// Inline Content Template add dialog management
	// ---------------------------------
	
	// Open dialog
	
	$( document ).on( 'click', '.js-wpv-ct-assign-to-view', function() {
		var dialog_height = $( window ).height() - 100;
		self.dialog_assign_ct.dialog( "open" ).dialog({
			maxHeight: dialog_height,
			draggable: false,
			resizable: false,
			position: { my: "center top+50", at: "center top", of: window }
		});
		self.dialog_assign_ct.html( self.shortcodeDialogSpinnerContent );
		
		var data = {
			action : 'wpv_assign_ct_to_view',
			view_id : $( this ).data('id'),
			wpnonce : $( '#wpv_inline_content_template' ).attr( 'value' )
		};
		$.ajax({
			type: "POST",
			dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( response.success ) {
					self.dialog_assign_ct.html( response.data.dialog_content );
					$( '.js-wpv-assign-ct-already, .js-wpv-assign-ct-existing, .js-wpv-assign-ct-new' ).hide();
					$( '.js-wpv-inline-template-type' )
						.first()
							.trigger( 'click' );
					$( '.js-wpv-assign-inline-content-template' )
						.prop( 'disabled', true )
						.addClass( 'button-secondary' )
						.removeClass( 'button-primary' );
					}
				},
			error: function ( ajaxContext ) {
				//console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() {
				
			}
		});
	});
	
	// Manage changes
	
	$( document ).on( 'change', '.js-wpv-inline-template-type', function() {
		var thiz = $( this ),
		thiz_val = thiz.val();
		$( '.js-wpv-assign-ct-already, .js-wpv-assign-ct-existing, .js-wpv-assign-ct-new' ).hide();
		$( '.js-wpv-assign-ct-' + thiz_val ).fadeIn( 'fast' );
		if ( thiz_val == 'already' ) {
			if ( $( '.js-wpv-inline-template-assigned-select' ).val() == 0 ) {
				$( '.js-wpv-assign-inline-content-template' )
					.prop( 'disabled', true )
					.addClass( 'button-secondary' )
					.removeClass( 'button-primary' );
			} else {
				$( '.js-wpv-assign-inline-content-template' )
					.prop( 'disabled', false )
					.removeClass( 'button-secondary' )
					.addClass( 'button-primary' );
			}
			$( '.js-wpv-inline-template-insert' ).hide();

		} else if ( thiz_val == 'existing' ) {
			if ( $( '.js-wpv-inline-template-existing-select').val() == 0 ) {
				$( '.js-wpv-assign-inline-content-template' )
					.prop( 'disabled', true )
					.addClass( 'button-secondary' )
					.removeClass( 'button-primary' );
			} else {
				$( '.js-wpv-assign-inline-content-template' )
					.prop( 'disabled', false )
					.removeClass( 'button-secondary' )
					.addClass( 'button-primary' );
			}
			$( '.js-wpv-inline-template-insert' ).show();
		} else if ( thiz_val == 'new' ) {
			if ( $( '.js-wpv-inline-template-new-name' ).val() == '' ) {
				$( '.js-wpv-assign-inline-content-template' )
					.prop( 'disabled', true )
					.addClass( 'button-secondary' )
					.removeClass( 'button-primary' );
			} else {
				$('.js-wpv-assign-inline-content-template')
					.prop( 'disabled', false )
					.removeClass( 'button-secondary' )
					.addClass( 'button-primary' );
			}
			$( '.js-wpv-inline-template-insert' ).show();
		}
	});
	
	$( document ).on( 'change', '.js-wpv-inline-template-assigned-select', function() {
		if ( $( '.js-wpv-inline-template-assigned-select' ).val() == 0 ) {
			$( '.js-wpv-assign-inline-content-template' )
				.prop( 'disabled', true )
				.addClass( 'button-secondary' )
				.removeClass( 'button-primary' );
		} else {
			$( '.js-wpv-assign-inline-content-template' )
				.prop( 'disabled', false )
				.removeClass( 'button-secondary' )
				.addClass( 'button-primary' );
		}
	});
	
	$( document ).on( 'change', '.js-wpv-inline-template-existing-select', function() {
		if ( $( '.js-wpv-inline-template-existing-select').val() == 0 ) {
			$( '.js-wpv-assign-inline-content-template' )
				.prop( 'disabled', true )
				.addClass( 'button-secondary' )
				.removeClass( 'button-primary' );
		} else {
			$( '.js-wpv-assign-inline-content-template' )
				.prop( 'disabled', false )
				.removeClass( 'button-secondary' )
				.addClass( 'button-primary' );
		}
	});
	
	$( document ).on( 'change keyup input cut paste', '.js-wpv-inline-template-new-name', function() {
		$( '.js-wpv-add-new-ct-name-error-container .toolset-alert' ).remove();
		if ( $( '.js-wpv-inline-template-new-name' ).val() == '' ) {
			$( '.js-wpv-assign-inline-content-template' )
				.prop( 'disabled', true )
				.addClass( 'button-secondary' )
				.removeClass( 'button-primary' );
		} else {
			$('.js-wpv-assign-inline-content-template')
				.prop( 'disabled', false )
				.removeClass( 'button-secondary' )
				.addClass( 'button-primary' );
		}
	});
	
	// Submit
	
	$( document ).on( 'click','.js-wpv-assign-inline-content-template', function() {
		// On AJAX, both #wpv_inline_content_template and #wpv-ct-inline-edit are valid nonces
		var thiz = $( this ),
		send_ajax = true,
		template_id = false,
		template_name = '',
		type = $( '.js-wpv-inline-template-type:checked' ).val(),
		spinnerContainer = $('<div class="wpv-spinner ajax-loader auto-update">').insertAfter( thiz ).show();
		thiz
			.prop( 'disabled', true )
			.removeClass( 'button-primary' )
			.addClass( 'button-secondary' );
		if ( type == 'existing' ) {
			if ( $( '.js-wpv-inline-template-existing-select' ).val() == '' ) {
				return;
			}
			template_id = $( '.js-wpv-inline-template-existing-select' ).val();
			template_name = $( '.js-wpv-inline-template-existing-select option:selected' ).text();
			data = {
				action : 'wpv_add_view_template',
				view_id : $( '.js-wpv-ct-assign-to-view' ).data( 'id' ),
				template_id : template_id,
				wpnonce : $( '#wpv_inline_content_template' ).attr( 'value' )
			};
		} else if ( type == 'new' ) {
			if ( $( '.js-wpv-inline-template-new-name' ).val() == '' ) {
				return;
			}
			template_name = $( '.js-wpv-inline-template-new-name' ).val();
			data = {
				action : 'wpv_add_view_template',
				view_id : $('.js-wpv-ct-assign-to-view').data('id'),
				template_name : template_name,
				wpnonce : $('#wpv-ct-inline-edit').attr('value')
			};
		} else if ( type == 'already' ) {
			send_ajax = false;
			template_id = $( '.js-wpv-inline-template-assigned-select' ).val();
			template_name = $( '.js-wpv-inline-template-assigned-select option:selected' ).text();
		}
		if ( send_ajax ) {
			$.post( ajaxurl, data, function( response ) {
				if ( response == 'error' ) {
					console.log('Error: Content template not found in database');
					$('.wpv_ct_inline_message').remove();
					return false;
				} else if ( response == 'error_name' ) {
					$( '.js-wpv-add-new-ct-name-error-container' ).wpvToolsetMessage({
						text: wpv_inline_templates_strings.new_template_name_in_use,
						stay: true,
						close: false,
						type: ''
				 	});
				 	$( '.wpv_ct_inline_message' ).remove();
				 	return false;
				} else {
					$( '.js-wpv-settings-inline-templates' ).show();
					if ( template_id && $('#wpv-ct-listing-' + template_id ).html() ) {
						$( '#wpv-ct-listing-' + template_id )
							.removeClass( 'hidden' );
					} else {
						$( '.js-wpv-content-template-view-list > ul' )
							.first()
								.append( response );
						template_id = $( '.js-wpv-content-template-view-list > ul > li' )
							.last()
								.data( 'id' );
					}
					self.add_content_template_shortcode( template_name, template_id );
					$( '.wpv_ct_inline_message' ).remove();
					self.dialog_assign_ct.dialog( "close" );
				}
			})
			.fail( function( jqXHR, textStatus, errorThrown ) {
				//console.log( "Error: ", textStatus, errorThrown );
			})
			.always( function() {
				spinnerContainer.remove();
			});
		} else {
			self.add_content_template_shortcode( template_name, template_id );
			$( '.wpv_ct_inline_message' ).remove();
			self.dialog_assign_ct.dialog( "close" );
		}
		return false;
	});
	
	// Insert shortcode into textarea
	
	self.add_content_template_shortcode = function( template_name, template_id ) {
		if ( $( '.js-wpv-add-to-editor-check' ).prop('checked') == true || $( '.js-wpv-inline-template-type:checked' ).val() == 'already' ) {
			var content = '[wpv-post-body view_template="' + template_name + '"]',
			current_cursor = codemirror_views_layout.getCursor( true );
            codemirror_views_layout.setSelection( current_cursor, current_cursor );
            codemirror_views_layout.replaceSelection( content, 'end' );
			var end_cursor = codemirror_views_layout.getCursor( true ),
			content_template_marker = codemirror_views_layout.markText( current_cursor, end_cursor, self.codemirror_highlight_options ),
			pointer_content = $( '#js-wpv-inline-content-templates-dialogs .js-wpv-inserted-inline-content-template-pointer' );
			if ( pointer_content.hasClass( 'js-wpv-pointer-dismissed' ) ) {
				setTimeout( function() {
					  content_template_marker.clear();
				}, 2000);
			} else {
				var content_template_pointer = $('.layout-html-editor  .wpv-codemirror-highlight').first().pointer({
					pointerClass: 'wp-toolset-pointer wp-toolset-views-pointer',
					pointerWidth: 400,
					content: pointer_content.html(),
					position: {
						edge: 'bottom',
						align: 'left'
					},
					show: function( event, t ) {
						t.pointer.show();
						t.opened();
						var button_scroll = $('<button class="button button-primary-toolset alignright js-wpv-scroll-this">' + wpv_inline_templates_strings.pointer_scroll_to_template + '</button>');
						button_scroll.bind( 'click.pointer', function(e) {//We need to scroll there down
							e.preventDefault();
							content_template_marker.clear();
							if ( t.pointer.find( '.js-wpv-dismiss-pointer:checked' ).length > 0 ) {
								var pointer_name = t.pointer.find( '.js-wpv-dismiss-pointer:checked' ).data( 'pointer' );
								$( document ).trigger( 'js_event_wpv_dismiss_pointer', [ pointer_name ] );
							}
							t.element.pointer('close');
							if ( template_id ) {
								$('html, body').animate({
									scrollTop: $( '#wpv-ct-listing-' + template_id ).offset().top - 100
								}, 1000);
							}
						});
						button_scroll.insertAfter(  t.pointer.find('.wp-pointer-buttons .js-wpv-close-this') );
					},
					buttons: function( event, t ) {
						var button_close = $('<button class="button button-secondary alignleft js-wpv-close-this">' + wpv_inline_templates_strings.pointer_close + '</button>');
						button_close.bind( 'click.pointer', function( e ) {
							e.preventDefault();
							content_template_marker.clear();
							if ( t.pointer.find( '.js-wpv-dismiss-pointer:checked' ).length > 0 ) {
								var pointer_name = t.pointer.find( '.js-wpv-dismiss-pointer:checked' ).data( 'pointer' );
								$( document ).trigger( 'js_event_wpv_dismiss_pointer', [ pointer_name ] );
							}
							t.element.pointer('close');
							codemirror_views_layout.focus();
						});
						return button_close;
					}
				});
				content_template_pointer.pointer('open');
			}
		}
	};
	
	// ---------------------------------
	// Inline Content Template change and update management
	// ---------------------------------
	
	// Open
	
	$( document ).on( 'click', '.js-wpv-content-template-open', function( e ) {
		e.preventDefault();
		var thiz = $( this ),
		template_id = thiz.data( 'target' ),
		li_container = $( '.js-wpv-inline-editor-container-' + template_id ),
		arrow = thiz.find( '.js-wpv-open-close-arrow' );
		li_container.slideToggle( 400 ,function() {
			arrow
				.toggleClass( 'icon-caret-down icon-caret-up' );
			if ( ! li_container.is(':hidden') ) {
				if ( ! window["wpv_ct_inline_editor_" + template_id] ) {
					// First time we open the inline CT, so we must get it
					var $spinnerContainer = $( '<div class="wpv-spinner ajax-loader">' ).insertAfter( thiz ).show();
					data = {
						action : 'wpv_ct_loader_inline',
						id : template_id,
						include_instructions : 'inline_content_template',
						wpnonce : $( '#wpv-ct-inline-edit' ).attr( 'value' )
					};
					$.post( ajaxurl, data, function( response ) {
						if ( response == 'error' ) {
							console.log('Error, Content Template not found.');
						} else {
							$( '.js-wpv-inline-editor-container-' + template_id ).html( response );
							if ( typeof cred_cred != 'undefined' ) {
								cred_cred.posts();// this should be an event!!!
							}
							// Content editor
							window["wpv_ct_inline_editor_" + template_id] = icl_editor.codemirror( 'wpv-ct-inline-editor-' + template_id, true );
							window["wpv_ct_inline_editor_" + template_id].refresh();
							window["wpv_ct_inline_editor_val_" + template_id] = window["wpv_ct_inline_editor_" + template_id].getValue();
							//Add quicktags toolbar
							var wpv_inline_editor_qt = quicktags( { id: 'wpv-ct-inline-editor-'+template_id, buttons: 'strong,em,link,block,del,ins,img,ul,ol,li,code,close' } );
							WPV_Toolset.CodeMirror_instance['wpv_ct_inline_editor_' + template_id] = window["wpv_ct_inline_editor_" + template_id];
							WPV_Toolset.add_qt_editor_buttons( wpv_inline_editor_qt, WPV_Toolset.CodeMirror_instance['wpv_ct_inline_editor_' + template_id] );
							
							// Extra assets editors
							window["wpv_ct_assets_inline_css_editor_" + template_id] = icl_editor.codemirror( 'wpv-ct-assets-inline-css-editor-' + template_id, true, 'css' );
							window["wpv_ct_assets_inline_css_editor_" + template_id].setSize( "100%", 250 );
							WPViews.view_codemirror_utils.codemirror_panel( window["wpv_ct_assets_inline_css_editor_" + template_id], wpv_inline_templates_strings.panel_title_css, 'permanent', 'title' );
							window["wpv_ct_assets_inline_css_editor_val_" + template_id] = window["wpv_ct_assets_inline_css_editor_" + template_id].getValue();
							WPV_Toolset.CodeMirror_instance['wpv_ct_assets_inline_css_editor_' + template_id] = window["wpv_ct_assets_inline_css_editor_" + template_id];
							window["wpv_ct_assets_inline_js_editor_" + template_id] = icl_editor.codemirror( 'wpv-ct-assets-inline-js-editor-' + template_id, true, 'javascript' );
							window["wpv_ct_assets_inline_js_editor_" + template_id].setSize( "100%", 250 );
							WPViews.view_codemirror_utils.codemirror_panel( window["wpv_ct_assets_inline_js_editor_" + template_id], wpv_inline_templates_strings.panel_title_js, 'permanent', 'title' );
							window["wpv_ct_assets_inline_js_editor_val_" + template_id] = window["wpv_ct_assets_inline_js_editor_" + template_id].getValue();
							WPV_Toolset.CodeMirror_instance['wpv_ct_assets_inline_js_editor_val_' + template_id] = window["wpv_ct_assets_inline_js_editor_val_" + template_id];
							
							$( '.js-wpv-ct-update-inline-' + template_id ).prop( 'disabled', true );
							
							window["wpv_ct_inline_editor_" + template_id].on( 'change', function() {
								if ( 
									window["wpv_ct_inline_editor_val_" + template_id] !=  window["wpv_ct_inline_editor_" + template_id].getValue() 
									|| window["wpv_ct_assets_inline_css_editor_val_" + template_id] !=  window["wpv_ct_assets_inline_css_editor_" + template_id].getValue() 
									|| window["wpv_ct_assets_inline_js_editor_val_" + template_id] !=  window["wpv_ct_assets_inline_js_editor_" + template_id].getValue() 
								) {
									$( '.js-wpv-ct-update-inline-' + template_id )
										.removeClass('button-secondary')
										.addClass( 'button-primary js-wpv-section-unsaved' )
										.prop( 'disabled', false );
									setConfirmUnload( true );
								} else {
									$( '.js-wpv-ct-update-inline-' + template_id )
										.removeClass( 'button-primary js-wpv-section-unsaved' )
										.addClass( 'button-secondary' )
										.prop( 'disabled', true );
									$( '.js-wpv-ct-update-inline-' + template_id )
										.parent()
											.find( '.toolset-alert-error' )
												.remove();
									if ( $( '.js-wpv-section-unsaved' ).length < 1 ) {
										setConfirmUnload( false );
									}
								}
							});
							
							window["wpv_ct_assets_inline_css_editor_" + template_id].on( 'change', function() {
								if ( 
									window["wpv_ct_inline_editor_val_" + template_id] !=  window["wpv_ct_inline_editor_" + template_id].getValue() 
									|| window["wpv_ct_assets_inline_css_editor_val_" + template_id] !=  window["wpv_ct_assets_inline_css_editor_" + template_id].getValue() 
									|| window["wpv_ct_assets_inline_js_editor_val_" + template_id] !=  window["wpv_ct_assets_inline_js_editor_" + template_id].getValue() 
								) {
									$( '.js-wpv-ct-update-inline-' + template_id )
										.removeClass('button-secondary')
										.addClass( 'button-primary js-wpv-section-unsaved' )
										.prop( 'disabled', false );
									setConfirmUnload( true );
								} else {
									$( '.js-wpv-ct-update-inline-' + template_id )
										.removeClass( 'button-primary js-wpv-section-unsaved' )
										.addClass( 'button-secondary' )
										.prop( 'disabled', true );
									$( '.js-wpv-ct-update-inline-' + template_id )
										.parent()
											.find( '.toolset-alert-error' )
												.remove();
									if ( $( '.js-wpv-section-unsaved' ).length < 1 ) {
										setConfirmUnload( false );
									}
								}
							});
							
							window["wpv_ct_assets_inline_js_editor_" + template_id].on( 'change', function() {
								if ( 
									window["wpv_ct_inline_editor_val_" + template_id] !=  window["wpv_ct_inline_editor_" + template_id].getValue() 
									|| window["wpv_ct_assets_inline_css_editor_val_" + template_id] !=  window["wpv_ct_assets_inline_css_editor_" + template_id].getValue() 
									|| window["wpv_ct_assets_inline_js_editor_val_" + template_id] !=  window["wpv_ct_assets_inline_js_editor_" + template_id].getValue() 
								) {
									$( '.js-wpv-ct-update-inline-' + template_id )
										.removeClass('button-secondary')
										.addClass( 'button-primary js-wpv-section-unsaved' )
										.prop( 'disabled', false );
									setConfirmUnload( true );
								} else {
									$( '.js-wpv-ct-update-inline-' + template_id )
										.removeClass( 'button-primary js-wpv-section-unsaved' )
										.addClass( 'button-secondary' )
										.prop( 'disabled', true );
									$( '.js-wpv-ct-update-inline-' + template_id )
										.parent()
											.find( '.toolset-alert-error' )
												.remove();
									if ( $( '.js-wpv-section-unsaved' ).length < 1 ) {
										setConfirmUnload( false );
									}
								}
							});
						}
						$spinnerContainer.remove();
					});
				} else {
					window["wpv_ct_inline_editor_" + template_id].refresh();
					window["wpv_ct_assets_inline_css_editor_" + template_id].refresh();
					window["wpv_ct_assets_inline_js_editor_" + template_id].refresh();
				}
			}
		});
		return false;
	});
	
	// Update

	$( document ).on( 'click', '.js-wpv-ct-update-inline', function() {
		var thiz = $( this ),
		thiz_container = thiz.closest('.js-wpv-ct-listing' ),
		messages_container = thiz_container
			.closest( '.js-wpv-content-template-view-list' )
				.find( '.js-wpv-message-container' ),
		ct_id = thiz.data( 'id' ),
		ct_value = window["wpv_ct_inline_editor_" + ct_id].getValue(),
		ct_css_value = window["wpv_ct_assets_inline_css_editor_" + ct_id].getValue(),
		ct_js_value = window["wpv_ct_assets_inline_js_editor_" + ct_id].getValue(),
		spinnerContainer = $( self.spinner ).insertBefore( thiz ).show(),
		data = {
			action : 'wpv_ct_update_inline',
			ct_value : ct_value,
			ct_css_value: ct_css_value,
			ct_js_value: ct_js_value,
			ct_id : ct_id,
			wpnonce : $( '#wpv_inline_content_template' ).attr( 'value' )
		};
		$.post( ajaxurl, data, function( response ) {
			if ( response.success ) {
				$( '.js-wpv-ct-update-inline-'+ ct_id )
					.parent()
						.find('.toolset-alert-error')
							.remove();
				$( '.js-wpv-ct-update-inline-' + ct_id )
					.prop( 'disabled', true )
					.removeClass( 'button-primary js-wpv-section-unsaved' )
					.addClass( 'button-secondary' );
				thiz_container.addClass( 'wpv-inline-content-template-saved' );
				setTimeout( function () {
					thiz_container.removeClass( 'wpv-inline-content-template-saved' );
				}, 500 );
				window["wpv_ct_inline_editor_val_" + ct_id] = ct_value;
				window["wpv_ct_assets_inline_css_editor_val_" + ct_id] = ct_css_value;
				window["wpv_ct_assets_inline_js_editor_val_" + ct_id] = ct_js_value;
				if ( $( '.js-wpv-section-unsaved' ).length < 1 ) {
					setConfirmUnload( false );
				}
			} else {
				self.edit_screen.manage_ajax_fail( response.data, messages_container );
			}
		}, 'json' )
		.fail( function( jqXHR, textStatus, errorThrown ) {
			//console.log( "Error: ", textStatus, errorThrown );
		})
		.always( function() {
			spinnerContainer.remove();
		});
	});
	
	// Remove dialog
	
	$( document ).on( 'click', '.js-wpv-ct-remove-from-view', function( e ) {
		e.preventDefault();
		var thiz = $( this ),
		messages_container = thiz.closest( '.js-wpv-content-template-view-list' ).find( '.js-wpv-message-container' );
		self.current_ct_container = thiz.parents('.js-wpv-ct-listing' );
		self.current_ct_id = self.current_ct_container.data( 'id' );
		if ( $( '#js-wpv-dialog-remove-content-template-from-view-dialog' ).hasClass( 'js-wpv-dialog-dismissed' ) ) {
			data = {
				action : 'wpv_remove_content_template_from_view',
				view_id : self.view_id,
				template_id : self.current_ct_id,
				dismiss : 'true',
				wpnonce : $('#wpv_inline_content_template').attr( 'value' )
			};
			$.post( ajaxurl, data, function( response ) {
				if ( response.success ) {
					self.remove_inline_content_template( self.current_ct_id, self.current_ct_container );
				} else {
					self.edit_screen.manage_ajax_fail( response.data, messages_container );
				}
				self.current_ct_container = null;
				self.current_ct_id = 0;
			});
		} else {
			var dialog_height = $( window ).height() - 100;
			self.dialog_unassign_ct.dialog( "open" ).dialog({
				maxHeight: dialog_height,
				draggable: false,
				resizable: false,
				position: { my: "center top+50", at: "center top", of: window }
			});
		}
		return false;
	});
	
	self.remove_inline_content_template = function( template_id, template_container ) {
		if ( 
			template_id == 0 
			|| template_container == null
		) {
			return;
		}
		template_container
			.addClass( 'wpv-inline-content-template-deleted' )
			.animate({
			  height: "toggle",
			  opacity: "toggle"
			}, 400, function() {
				if ( typeof window["wpv_ct_inline_editor_" + template_id] !== 'undefined' ) {
					window["wpv_ct_inline_editor_" + template_id].focus();
					delete window["wpv_ct_inline_editor_" + template_id];
					delete window["wpv_ct_inline_editor_val_" + template_id];
					// We also need to delete it from the iclCodeMirror collection
					delete window.iclCodemirror["wpv-ct-inline-editor-" + template_id];
				}
				if ( typeof window["wpv_ct_assets_inline_css_editor_" + template_id] !== 'undefined' ) {
					window["wpv_ct_assets_inline_css_editor_" + template_id].focus();
					delete window["wpv_ct_assets_inline_css_editor_" + template_id];
					delete window["wpv_ct_assets_inline_css_editor_val_" + template_id];
					// We also need to delete it from the iclCodeMirror collection
					delete window.iclCodemirror["wpv-ct-assets-inline-css-editor-" + template_id];
				}
				if ( typeof window["wpv_ct_assets_inline_js_editor_" + template_id] !== 'undefined' ) {
					window["wpv_ct_assets_inline_js_editor_" + template_id].focus();
					delete window["wpv_ct_assets_inline_js_editor_" + template_id];
					delete window["wpv_ct_assets_inline_js_editor_val_" + template_id];
					// We also need to delete it from the iclCodeMirror collection
					delete window.iclCodemirror["wpv-ct-assets-inline-js-editor-" + template_id];
				}
				$( this ).remove();
				if ( $( "ul.js-wpv-inline-content-template-listing > li" ).length < 1 ) {
					$( '.js-wpv-settings-inline-templates' ).hide();
				}
			});
	};
	
	// Manage pushpin for inline CT assets
	
	$( document ).on( 'js_event_wpv_editor_metadata_toggle_toggled', function( event, toggler ) {
		if ( toggler.hasClass( 'js-wpv-ct-assets-inline-editor-toggle' ) ) {
			var ct_inline_id = toggler.data( 'id' ),
			thiz_type = toggler.data( 'type' ),
			thiz_flag = toggler.find( '.js-wpv-textarea-full' ),
			this_toggler_icon = toggler.find( '.js-wpv-toggle-toggler-icon i' );
			thiz_flag.hide();
			if ( ! toggler.hasClass( 'js-wpv-ct-assets-inline-editor-toggle-refreshed' ) ) {
				window["wpv_ct_assets_inline_" + thiz_type + "_editor_" + ct_inline_id].refresh();
				toggler.addClass( 'js-wpv-ct-assets-inline-editor-toggle-refreshed' );
			}
			if ( 
				this_toggler_icon.hasClass( 'icon-caret-down' ) 
				&& self.asset_needs_flag( ct_inline_id, thiz_type ) 
			) {
				thiz_flag.animate( {width: 'toggle'}, 200 );
			}
		}
	});
	
	self.asset_needs_flag = function( ct_id, type ) {
		var needed = false;
		if ( window["wpv_ct_assets_inline_" + type + "_editor_" + ct_id].getValue() != '' ) {
			needed = true;
		}
		return needed;
	};
	
	self.init_dialogs = function() {
		$( 'body' ).append( '<div id="js-wpv-dialog-assign-content-template-to-view-dialog" class="toolset-shortcode-gui-dialog-container wpv-shortcode-gui-dialog-container js-wpv-shortcode-gui-dialog-container"></div>' );
		self.dialog_assign_ct = $( "#js-wpv-dialog-assign-content-template-to-view-dialog" ).dialog({
			autoOpen: false,
			modal: true,
			title: wpv_inline_templates_strings.dialog_assign_ct_title,
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
					text: wpv_inline_templates_strings.dialog_cancel,
					click: function() {
						$( this ).dialog( "close" );
					}
				},
				{
					class: 'button-primary js-wpv-assign-inline-content-template',
					text: wpv_inline_templates_strings.dialog_assign_ct_assign,
					click: function() {

					}
				}
			]
		});
		
		self.dialog_unassign_ct = $( "#js-wpv-dialog-remove-content-template-from-view-dialog" ).dialog({
			autoOpen: false,
			modal: true,
			title: wpv_inline_templates_strings.dialog_unassign_ct_title,
			minWidth: 600,
			show: { 
				effect: "blind", 
				duration: 800 
			},
			open: function( event, ui ) {
				$( 'body' ).addClass( 'modal-open' );
				$( '.js-wpv-remove-template-from-view' )
					.addClass( 'button-primary' )
					.removeClass( 'button-secondary' )
					.prop( 'disabled', false );
			},
			close: function( event, ui ) {
				$( 'body' ).removeClass( 'modal-open' );
			},
			buttons:[
				{
					class: 'button-secondary',
					text: wpv_inline_templates_strings.dialog_cancel,
					click: function() {
						self.current_ct_container = null;
						self.current_ct_id = 0;
						$( this ).dialog( "close" );
					}
				},
				{
					class: 'button-primary js-wpv-remove-template-from-view',
					text: wpv_inline_templates_strings.dialog_unassign_ct_remove,
					click: function() {
						var thiz = $( '.js-wpv-remove-template-from-view' ),
						thiz_dialog = $( this ),
						dismiss = 'false',
						spinnerContainer = $('<div class="wpv-spinner ajax-loader auto-update">').insertAfter( thiz ).show(),
						messages_container = $( '.js-wpv-content-template-view-list' ).find( '.js-wpv-message-container' );
						thiz
							.addClass( 'button-secondary' )
							.removeClass( 'button-primary' )
							.prop( 'disabled', true );
						if ( $( '.js-wpv-dettach-inline-content-template-dismiss' ).prop('checked') ) {
							dismiss = 'true';
						}
						var data = {
							action : 'wpv_remove_content_template_from_view',
							view_id : self.view_id,
							template_id : self.current_ct_id,
							dismiss : dismiss,
							wpnonce : $('#wpv_inline_content_template').attr( 'value' )
						};
						$.post( ajaxurl, data, function( response ) {
							if ( response.success ) {
								self.remove_inline_content_template( self.current_ct_id, self.current_ct_container );
								if ( dismiss == 'true' ) {
									$( '#js-wpv-dialog-remove-content-template-from-view-dialog' ).addClass( 'js-wpv-dialog-dismissed' );
								}
							} else {
								self.edit_screen.manage_ajax_fail( response.data, messages_container );
							}
							self.current_ct_container = null;
							self.current_ct_id = 0;
						})
						.fail( function( jqXHR, textStatus, errorThrown ) {
							//console.log( "Error: ", textStatus, errorThrown );
						})
						.always( function() {
							spinnerContainer.remove();
							thiz_dialog.dialog( "close" );
						});
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
    WPViews.view_edit_screen_inline_content_templates = new WPViews.ViewEditScreenInlineCT( $ );
});