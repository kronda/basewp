var WPV_Toolset = WPV_Toolset  || {};
if ( typeof WPV_Toolset.CodeMirror_instance === "undefined" ) {
	WPV_Toolset.CodeMirror_instance = [];
}

var codemirror_views_layout = icl_editor.codemirror('wpv_layout_meta_html_content', true),
codemirror_views_layout_val = codemirror_views_layout.getValue(),
wpv_layout_meta_html_content_qt = quicktags( { id: 'wpv_layout_meta_html_content', buttons: 'strong,em,link,block,del,ins,img,ul,ol,li,code,close' } ),
codemirror_views_layout_css = icl_editor.codemirror('wpv_layout_meta_html_css', true, 'css'),
codemirror_views_layout_css_val = codemirror_views_layout_css.getValue(),
codemirror_views_layout_js = icl_editor.codemirror('wpv_layout_meta_html_js', true, 'javascript'),
codemirror_views_layout_js_val = codemirror_views_layout_js.getValue(),
codemirror_views_content = icl_editor.codemirror('wpv_content', true),
codemirror_views_content_val = codemirror_views_content.getValue(),
wpv_content_qt = quicktags( { id: 'wpv_content', buttons: 'strong,em,link,block,del,ins,img,ul,ol,li,code,close' } );

WPV_Toolset.CodeMirror_instance['wpv_layout_meta_html_content'] =  codemirror_views_layout;
WPV_Toolset.CodeMirror_instance['wpv_layout_meta_html_css'] =  codemirror_views_layout_css;
WPV_Toolset.CodeMirror_instance['wpv_layout_meta_html_js'] =  codemirror_views_layout_js;
WPV_Toolset.CodeMirror_instance['wpv_content'] =  codemirror_views_content;

// Define 'save' command in CodeMirror object
// This automatically adds Ctrl+S (Cmd+S in Mac) keyboard shortcut for saving
// in every CodeMirror instance.
// This code is duplicated in views_editor.js
jQuery(document).ready(function ($) {
    CodeMirror.commands.save = function (cm) {
        // Prevent Firefox trigger Save Dialog
        var keypress_handler = function (cm, event) {
            if (event.which == 115 && (event.ctrlKey || event.metaKey) || (event.which == 19)) {
                event.preventDefault();
                return false;
            }
            return true;
        };
        CodeMirror.off(cm.getWrapperElement(), 'keypress', keypress_handler);
        cm.on('keypress', keypress_handler);
        
        var textarea_id = cm.getTextArea().id;
        if (
                textarea_id === 'wpv_layout_meta_html_content' ||
                textarea_id === 'wpv_layout_meta_html_css' ||
                textarea_id === 'wpv_layout_meta_html_js'
                ) {
            /* Loop Output */
            jQuery('.js-wpv-layout-extra-update').click();
        } else if (
                textarea_id === 'wpv_content'
                ) {
            /* Filter and Loop Output Integration */
            jQuery('.js-wpv-content-update').click();
        }
    };
});

var WPViews = WPViews || {};

WPViews.WPAEditScreen = function( $ ) {
	
	var self = this;
	self.view_id = $('.js-post_ID').val();
	self.show_hide_sections =  $( '.js-wpv-show-hide-container' ).find('.js-wpv-show-hide-value').serialize();
	self.show_hide_metasections_help =  $( '.js-wpv-show-hide-container' ).find('.js-wpv-show-hide-help-value').serialize();
	
	self.action_bar = $( '#js-wpv-general-actions-bar' );
	self.action_bar_message_container = $( '#js-wpv-general-actions-bar .js-wpv-message-container' );
	self.html = $( 'html' );
	
	self.overlay_container = $("<div class='wpv-setting-overlay js-wpv-setting-overlay'><div class='wpv-transparency'></div><i class='icon-lock'></i></div>");
	
	if ( self.action_bar && self.action_bar.offset() ) {
		var toolbarPos = self.action_bar.offset().top,
		adminBarHeight = 0,
		adminBarWidth = $( '.wpv-title-section .wpv-setting-container' ).width();
		if ( $('#wpadminbar').length !== 0 ) {
			adminBarHeight = $('#wpadminbar').height();
			self.action_bar.width( adminBarWidth );
		}
		self.set_toolbar_pos = function() {
			if ( toolbarPos <= $(window).scrollTop() + adminBarHeight + 5) {
				self.html.addClass('wpv-general-actions-bar-fixed');
			}
			else {
				self.html.removeClass('wpv-general-actions-bar-fixed');
			}
		};

		$( window ).on( 'scroll', function() {
			self.set_toolbar_pos();
		});
		
		$( window ).on( 'resize', function() {
			var adminBarWidth = $( '.wpv-title-section .wpv-setting-container' ).width();
			self.action_bar.width( adminBarWidth );
		});

		self.set_toolbar_pos();
	}
	
	// ---------------------------------
	// Save actions: errors and successes
	// ---------------------------------
	
	self.manage_ajax_fail = function( data, message_container ) {
		if ( data.type ) {
			switch ( data.type ) {
				case 'nonce':
				case 'id':
				case 'capability':
					self.manage_action_bar_error( data );
					setConfirmUnload( false );
					$( '.wpv-setting-container:not(.js-wpv-general-actions-bar)' ).prepend( self.overlay_container );
					break;
				default:
					if ( data.message ) {
						message_container
							.wpvToolsetMessage({
								text: data.message,
								type: 'error',
								inline: true,
								stay: true
							});
					}
					break;
			}
		} else {
			if ( data.message ) {
				message_container
					.wpvToolsetMessage({
						text: data.message,
						type: 'error',
						inline: true,
						stay: true
					});
			}
		}
	};
	
	self.manage_ajax_success = function( data, message_container ) {
		if ( data.message ) {
			message_container
				.wpvToolsetMessage({
					text: data.message,
					type: 'success',
					inline: true,
					stay: false
				});
		}
	};

    self.manage_action_bar_success = function( data ) {
        if ( data.message ) {
            self.dialog.dialog( 'close' );
            self.action_bar_message_container
                .wpvToolsetMessage({
                    text: data.message,
                    type: 'success',
                    inline: false,
                    stay: false
                });
        }
    };
	
	self.manage_action_bar_error = function( data ) {
		if ( data.message ) {
            var stay = (typeof(data.stay) != 'undefined') ? data.stay : true;
			self.dialog.dialog( 'close' );
			self.action_bar_message_container
				.wpvToolsetMessage({
					text: data.message,
					type: 'error',
					inline: false,
					stay: stay
				});
		}
	};
	
	// ---------------------------------
	// Screen options
	// ---------------------------------
	
	// Screen options - position fix
	
	self.screen_options_fix = function() {
		var views_screen_options_container = $('#js-screen-meta-dup > div#js-screen-options-wrap-dup');
		$('#screen-options-wrap')
			.addClass( 'js-wpv-show-hide-container' )
			.html( views_screen_options_container.html() );
		views_screen_options_container.remove();
	};
	
	// Screen options - show/hide metasections
	
	self.show_hide_metasections_init = function() {
		$( '.js-wpv-show-hide-section' ).each( function() {
			var metasection = $( this ).data( 'metasection' );
			if (
				0 == $( this ).find( '.js-wpv-show-hide:checked' ).length &&
				$( '.' + metasection ).find( '.wpv-setting-container' ).length == $( this ).find( '.js-wpv-show-hide' ).length
			) {
				$( '.' + metasection ).hide();
			}
		});
	};
	
	// Screen options - help boxes for purposes
	
	self.show_hide_help_init = function() {
		$('.js-wpv-show-hide-help').each(function(){
			var metasection = $( this ).data( 'metasection' ),
			state = $( this ).attr( 'checked' );
			if ( 'checked' == state ) {
				jQuery( '.js-metasection-help-' + metasection ).show();
			} else {
				jQuery( '.js-metasection-help-' + metasection ).hide();
			}
		});
	};
	
	// Screen options - update automatically
	
	self.save_wpa_screen_options = function() {
		var container = $( '.js-wpv-show-hide-container' ),
		wpv_show_hide_sections = container.find('.js-wpv-show-hide-value').serialize(),
		wpv_show_hide_metasections_help = container.find('.js-wpv-show-hide-help-value').serialize();
		container.find('.toolset-alert').remove();
		
		if ( self.show_hide_sections == wpv_show_hide_sections
			&& self.show_hide_metasections_help == wpv_show_hide_metasections_help
		) {
			
		} else {
			var manager = container.find( '.js-wpv-show-hide-update' ),
			nonce = manager.data( 'nonce' ),
			data_view_id = self.view_id,
			data = {
				action: 'wpv_save_screen_options',
				id: data_view_id,
				settings: wpv_show_hide_sections,
				helpboxes: wpv_show_hide_metasections_help,
				wpnonce: nonce
			};
			$.post( ajaxurl, data, function( response ) {
				if ( response.success ) {
					$( document ).trigger( 'js_event_wpv_screen_options_saved' );
				} else {
					self.manage_action_bar_error( response.data );
				}
			}, 'json' )
			.fail( function( jqXHR, textStatus, errorThrown ) {
				console.log( "Error: ", textStatus, errorThrown );
			})
			.always( function() {
				self.show_hide_sections = wpv_show_hide_sections;
				self.show_hide_metasections_help = wpv_show_hide_metasections_help;
			});
		}
		
	};
	
	self.screen_options_debounce_update = _.debounce( self.save_wpa_screen_options, 1000 );
	
	// Screen options - events
	
	$( document ).on( 'click', '#screen-meta-links #contextual-help-link', function() {
		// Fix when opening Help section
		// This is caused because we are adding our Screen Options in an artificial way
		// so when opening the Help tab it displays all elements inside the tab container
		$( '.metabox-prefs .js-wpv-show-hide-container' ).hide();
	});
	
	$( document ).on( 'change', '.js-wpv-show-hide-container .js-wpv-show-hide, .js-wpv-show-hide-container .js-wpv-show-hide-help', function() {
		self.screen_options_debounce_update();
	});
	
	// ---------------------------------
	// Title and description
	// ---------------------------------
	
	// Title placeholder
	
	self.title_placeholder = function() {
		$( '.js-title' ).each( function() {
			var thiz = $( this );
			if ( '' == thiz.val() ) {
				thiz
					.parents( '.js-wpv-titlewrap' )
						.find( '.js-title-reader' )
							.removeClass( 'screen-reader-text' );
			}
			thiz.focus( function() {
				thiz
					.parents( '.js-wpv-titlewrap' )
						.find( '.js-title-reader' )
							.addClass( 'screen-reader-text' );
			});
			thiz.blur( function() {
				if ( '' == thiz.val() ) {
					thiz
						.parents( '.js-wpv-titlewrap' )
							.find( '.js-title-reader' )
								.removeClass( 'screen-reader-text' );
				}
			});
		});
	};
	
	// Description events
	
	$( '.js-wpv-description-toggle' ).on( 'click', function() {
		$( this ).hide();
		$( '.js-wpv-description-container' ).fadeIn( 'fast' );
		$( '#wpv-description' ).focus();
	});
	
	// Change status

	$( document ).on( 'click', '.js-wpv-change-view-status', function( e ) {
		e.preventDefault();
		var thiz = $( this ),
		newstatus = thiz.data( 'statusto' ),
		spinnerContainer = $( '<div class="wpv-spinner ajax-loader">' ).insertAfter( thiz ).show(),
		update_message = thiz.data( 'success' ),
		error_message = thiz.data( 'unsaved' ),
		redirect_url = thiz.data( 'redirect' ),
		message_where = $( '.js-wpv-settings-title-and-desc .js-wpv-message-container' );
		thiz
			.prop( 'disabled', true )
			.removeClass( 'button-primary' )
			.addClass( 'button-secondary' );
		var data = {
			action: 'wpv_view_change_status',
			id: self.view_id,
			newstatus: newstatus,
			cleararchives: ( newstatus == 'trash' ) ? 1 : 0,
			wpnonce : thiz.data( 'nonce' )
		};
		$.ajax({
			async:false,
			type:"POST",
			url:ajaxurl,
			data:data,
			success: function( response ) {
				if ( ( typeof( response ) !== 'undefined' ) && ( response == data.id ) ) {
					if ( newstatus == 'trash' ) {
						setConfirmUnload( false );
						$( location ).attr( 'href', redirect_url );
					}
				} else {
					message_where.wpvToolsetMessage({
						text:error_message,
						type:'error',
						inline:true,
						stay:true
					});
					console.log( "Error: AJAX returned ", response );
				}
			},
			error: function( ajaxContext ) {
				thiz.prop( 'disabled', false );
				spinnerContainer.remove();
				message_where.wpvToolsetMessage({
					text:error_message,
					type:'error',
					inline:true,
					stay:true
				});
			  console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() {
			  
			}
		});
	});
	
	// ---------------------------------
	// Loop selection
	// ---------------------------------
	
	// Loop selection - save automatically
	
	self.save_wpa_loop_selection_options = function() {
		//view_settings['.js-wpv-loop-selection'] = jQuery('.js-loop-selection-form').serialize();
		
		var dataholder = $( '.js-wpv-loop-selection-update' ),
		messages_container = dataholder.parents( '.js-wpv-update-action-wrap' ).find( '.js-wpv-message-container' ),
		section_container = $( '.js-wpv-settings-archive-loop' ),
		unsaved_message = dataholder.data('unsaved'),
		nonce = dataholder.data('nonce'),
		spinnerContainer,
		view_id = self.view_id;
		section_container
			.addClass( 'wpv-setting-replacing' )
			.find( '.wpv-spinner.ajax-loader' )
				.remove();
		messages_container.find('.toolset-alert-error').remove();
		spinnerContainer = $('<div class="wpv-spinner ajax-loader">').insertBefore( dataholder ).show();
		var data = {
			action: 'wpv_update_loop_selection',
			id: view_id,
			form: $('.js-loop-selection-form').serialize(),
			wpnonce: nonce
		};
		$('.js-loop-selection-form input').prop( 'disabled', true );
		$.ajax({
			type: "POST",
			dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( response.success ) {
					$('.js-loop-selection-form').html( response.data.updated_archive_loops );
				} else {
					self.manage_ajax_fail( response.data, messages_container );
				}
			},
			error: function (ajaxContext) {
				messages_container
					.wpvToolsetMessage({
						text:unsaved_message,
						type:'error',
						inline:true,
						stay:true
					});
				console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() {
				spinnerContainer.remove();
				section_container.removeClass( 'wpv-setting-replacing' );
				$('.js-loop-selection-form input').prop( 'disabled', false );
			}
		});
	};
	
	self.loop_selection_debounce_update = _.debounce( self.save_wpa_loop_selection_options, 1000 );
	
	// Loop selection - events
	
	$( document ).on( 'change', '.js-loop-selection-form input', function() {
		self.loop_selection_debounce_update();
	});
	
	// ---------------------------------
	// Archive pagination button
	// ---------------------------------
	
	/**
	 * This happens when user clicks on the "Pagination controls" button in the Layout HTML/CSS/JS section.
	 *
	 * A dialog for selecting controls to insert ("js-wpv-archive-pagination-dialog") is displayed. The process then
	 * continues with clicking on a button with class "js-wpv-insert-archive-pagination".
	 *
	 * @since 1.7
	 */ 
	$(document).on( 'click', '.js-wpv-archive-pagination-popup', function( e ) {
		e.preventDefault();
		self.dialog.dialog( 'open' );
	});


	/**
	 * Insert archive pagination controls on cursor position into layout editor.
	 *
	 * This happens when user clicks on the submit button in "js-wpv-archive-pagination-dialog" dialog.
	 *
	 * @since 1.7
	 */  
	$(document).on( 'click', '.js-wpv-insert-archive-pagination', function( e ) {

		// Generate shortcodes
		var insertPrevLink = $('input[name=archive_pagination_insert_prev]').prop('checked');
		var insertNextLink = $('input[name=archive_pagination_insert_next]').prop('checked');
		
		var paginationShortcodes = "";
		if( insertPrevLink ) {
			paginationShortcodes = '[wpv-archive-pager-prev-page][wpml-string context="wpv-views"]Older posts[/wpml-string][/wpv-archive-pager-prev-page]';
		}

		if( insertNextLink ) {
			paginationShortcodes += '[wpv-archive-pager-next-page][wpml-string context="wpv-views"]Newer posts[/wpml-string][/wpv-archive-pager-next-page]';
		}
		
		// Insert pagination shortcodes at cursor position in the Layout editor
		var codemirror = codemirror_views_layout;
		var current_cursor = codemirror.getCursor( true );
		var text_before = codemirror.getRange( { line: 0, ch: 0 }, current_cursor );
		var text_after = codemirror.getRange( current_cursor, { line: codemirror.lastLine(), ch: null } );
		codemirror.replaceRange( paginationShortcodes, current_cursor, current_cursor );
		self.dialog.dialog( 'close' );
		codemirror.refresh();
		codemirror.focus();
	});


	/**
	 * Enable or disable the submit button in "js-wpv-archive-pagination-dialog" dialog depending on the input validity.
	 *
	 * @since 1.7
	 */ 
	$(document).on( 'change', '.js-wpv-archive-pagination-option', function( e ) {
		var insertPrevLink = $('input[name=archive_pagination_insert_prev]').prop('checked');
		var insertNextLink = $('input[name=archive_pagination_insert_next]').prop('checked');
		var isSomethingChecked = insertPrevLink || insertNextLink;

		var submitButton = $('.js-wpv-insert-archive-pagination');
		if( isSomethingChecked ) {
			submitButton.prop( 'disabled', false ).addClass( 'button-primary' ).removeClass( 'button-secondary' );
		} else {
			submitButton.prop( 'disabled', true ).removeClass( 'button-primary' ).addClass( 'button-secondary' );
		}
	});
	
	// ---------------------------------
	// CodeMirror
	// ---------------------------------
	
	// Refresh CodeMirror instances on init, after init of everything else
	// @todo use WPV_Toolset.CodeMirror_instance here to get rid of my globals
	
	self.refresh_codemirror = function( instance ) {
		if ( instance === 'all' ) {
			codemirror_views_layout.refresh();
			codemirror_views_layout_css.refresh();
			codemirror_views_layout_js.refresh();
			codemirror_views_content.refresh();
		} else {
			if ( instance == 'layout-css-editor' ) {
				codemirror_views_layout_css.refresh();
			} else if ( instance == 'layout-js-editor' ) {
				codemirror_views_layout_js.refresh();
			}
		}
	};
	
	// ---------------------------------
	// Quicktags
	// ---------------------------------
	
	// Add quicktags to the default editors
	
	self.add_quicktags = function() {
		WPV_Toolset.add_qt_editor_buttons( wpv_layout_meta_html_content_qt, WPV_Toolset.CodeMirror_instance['wpv_layout_meta_html_content'] );
		WPV_Toolset.add_qt_editor_buttons( wpv_content_qt, WPV_Toolset.CodeMirror_instance['wpv_content'] );
	};
	
	// ---------------------------------
	// Toggle boxes
	// ---------------------------------
	
	self.show_hide_toggle = function( thiz ) {
		$( '.' + thiz.data( 'target' ) ).slideToggle( 400, function() {
			thiz
				.find( '.js-wpv-toggle-toggler-icon i' )
					.toggleClass( 'icon-caret-down icon-caret-up' );
			$( document ).trigger( 'js_event_wpv_editor_metadata_toggle_toggled', [ thiz ] );
		});
	};
	
	$( document ).on( 'js_event_wpv_editor_metadata_toggle_toggled', function( event, toggler ) {
		var thiz_instance = toggler.data( 'instance' ),
		thiz_flag = toggler.find( '.js-wpv-textarea-full' ),
		this_toggler_icon = toggler.find( '.js-wpv-toggle-toggler-icon i' );
		thiz_flag.hide();
		if ( toggler.hasClass( 'js-wpv-assets-editor-toggle' ) ) {
			if ( ! toggler.hasClass( 'js-wpv-assets-editor-toggle-refreshed' ) ) {
				self.refresh_codemirror( thiz_instance );
				toggler.addClass( 'js-wpv-assets-editor-toggle-refreshed' );
			}
			if ( 
				this_toggler_icon.hasClass( 'icon-caret-down' ) 
				&& self.asset_needs_flag( thiz_instance ) 
			) {
				thiz_flag.animate( {width: 'toggle'}, 200 );
			}
		}
	});
	
	this.asset_needs_flag = function( instance ) {
		if ( instance == 'layout-css-editor' ) {
			return ( codemirror_views_layout_css.getValue() != '' );
		} else if ( instance == 'layout-js-editor' ) {
			return ( codemirror_views_layout_js.getValue() != '' );
		}
	};
	
	$( document ).on( 'click', '.js-wpv-editor-instructions-toggle, .js-wpv-editor-metadata-toggle', function() {
		var thiz = $( this );
		self.show_hide_toggle( thiz );
	});
	
	// ---------------------------------
	// Sections help pointers
	// ---------------------------------
	
	$('.js-display-tooltip').click(function(){
		var thiz = $( this );
		// hide this pointer if other pointer is opened.
		$( '.wp-toolset-pointer' ).fadeOut( 100 );
		thiz.pointer({
			pointerClass: 'wp-toolset-pointer wp-toolset-views-pointer',
			pointerWidth: 400,
			content: '<h3>'+thiz.data('header')+'</h3><p>'+thiz.data('content')+'</p>',
			position: {
				edge: 'left',
				align: 'center',
				offset: '15 0'
			},
			buttons: function( event, t ) {
				var button_close = $('<button class="button button-primary-toolset alignright js-wpv-close-this">Close</button>');
				button_close.bind( 'click.pointer', function( e ) {
					e.preventDefault();
					t.element.pointer('close');
				});
				return button_close;
			}
		}).pointer('open');
	});
	
	// ---------------------------------
	// Dismiss pointers
	// ---------------------------------
	
	$( document ).on( 'js_event_wpv_dismiss_pointer', function( event, pointer_name ) {
		var data = {
			action: 'wpv_dismiss_pointer',
			name: pointer_name
			//wpnonce : $(this).data('nonce')
		};
		$.ajax({
			type : "POST",
			url : ajaxurl,
			data : data,
			success : function( response ) {
				
			},
			error: function ( ajaxContext ) {
				console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() {
				$( '.js-wpv-' + pointer_name + '-pointer' ).addClass( 'js-wpv-pointer-dismissed' );
			}
		});
	});
	
	// ---------------------------------
	// Toolset compatibility
	// ---------------------------------
	
	self.toolset_compatibility = function() {
		// CRED plugin
		if ( typeof cred_cred != 'undefined' ) {
			cred_cred.posts();
		}
		// Layouts plugin
		if ( $( '.js-wpv-display-in-iframe' ).length == 1 ) {
			if ( $( '.js-wpv-display-in-iframe' ).val() == 'yes' ) {
				$( '.toolset-help a, .wpv-setting a' ).attr( "target", "_blank" );
			}
		}
	};
	
	// ---------------------------------
	// Dialogs
	// ---------------------------------
	
	self.init_dialogs = function() {
		var dialog_height = $( window ).height() - 100;
		self.dialog = $( "#js-hidden-messages-boxes-pointers-container .js-wpv-archive-pagination-dialog" ).dialog({
			autoOpen: false,
			modal: true,
			title: wpv_editor_strings.add_archive_pagination_dialog_title,
			minWidth: 550,
			maxHeight: dialog_height,
			draggable: false,
			resizable: false,
			position: { my: "center top+50", at: "center top", of: window },
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
					text: wpv_editor_strings.add_archive_pagination_dialog_cancel,
					click: function() {
						$( this ).dialog( "close" );
					}
				},
				{
					class: 'button-primary js-wpv-insert-archive-pagination',
					text: wpv_editor_strings.add_archive_pagination_dialog_insert,
					click: function() {

					}
				}
			],
			open: function() {
				$( '.js-wpv-archive-pagination-option' ).prop( 'checked', true );
			}
		});
	};
	
	// ---------------------------------
	// Init
	// ---------------------------------
	
	self.init = function(){ // public method
		// Screen options fix - move to the right place in DOM
		self.screen_options_fix();
		// Show or hide metasections in page load, based on screen options
		self.show_hide_metasections_init();
		// Show or hide section help boxes based on purpose
		self.show_hide_help_init();
		// Title placeholder
		self.title_placeholder();
		// Refresh CodeMirror instances
		self.refresh_codemirror( 'all' );
		// Add quicktags to the right textareas
		self.add_quicktags();
		// Toolset compatibility
		self.toolset_compatibility();
		// Init dialogs
		self.init_dialogs();
	};
	
	self.init(); // call the init method

};

jQuery( document ).ready( function( $ ) {
    WPViews.wpa_edit_screen = new WPViews.WPAEditScreen( $ );
});

/*
 * Screen options
 */


// Screen options - manage sections checkboxes click

jQuery(document).on('change', '.js-wpv-show-hide', function(){
	wpv_show_hide_section_change(jQuery(this));
});

// Based on the screen option checkbox, show or hide the section

function wpv_show_hide_section_change(checkbox) {
	checkbox.parents('.js-wpv-show-hide-container').find('.toolset-alert').remove();
	var section = checkbox.data('section');
	var state = checkbox.attr('checked');
	var input_value = checkbox.parents('.js-wpv-screen-pref').find('.js-wpv-show-hide-value');
	var section_changed = wpv_editor_strings.screen_options.can_not_hide;
	if ('checked' == state) {
		var metasection = checkbox.parents('.js-wpv-show-hide-section').data('metasection');
		jQuery('.' + metasection).show();
		jQuery('.js-wpv-settings-' + section).fadeIn('fast');
		input_value.val('on');

		/* We're no longer displaying Combined Output on WPA edit page (ever). But if we did, this should happen
		 * when showing/hiding it:
		 * 
	     * codemirror_views_content.refresh();
		 */
		 
	} else {
		if(jQuery('.js-wpv-settings-' + section).find('.js-wpv-section-unsaved').length > 0) {
			checkbox.attr('checked', 'checked');
			jQuery('.js-wpv-show-hide-container .js-wpv-toolset-messages')
				.wpvToolsetMessage({
					text:section_changed,
					type:'error',
					inline:true,
					stay:true
				});
		} else {
			jQuery('.js-wpv-settings-' + section).hide();
			var metasection = checkbox.parents('.js-wpv-show-hide-section').data('metasection');
			if (
				0 == checkbox.parents('.js-wpv-show-hide-section').find('.js-wpv-show-hide:checked').length &&
				jQuery('.' + metasection).find('.wpv-setting-container').length == checkbox.parents('.js-wpv-show-hide-section').find('.js-wpv-show-hide').length
			) {
				jQuery('.' + metasection).hide();
			}
			input_value.val('off');
		}
	}
}

// Message boxes display

jQuery(document).on('click', '.js-metasection-help-query .js-toolset-help-close-main', function(){
	jQuery('.js-wpv-show-hide-query-help').prop('checked', false);
	jQuery('.js-wpv-show-hide-query-help-value').val('off');
});

jQuery(document).on('click', '.js-metasection-help-layout .js-toolset-help-close-main', function(){
	jQuery('.js-wpv-show-hide-layout-help').prop('checked', false);
	jQuery('.js-wpv-show-hide-layout-help-value').val('off');
});

jQuery(document).on('change', '.js-wpv-show-hide-help', function(){
	var state = jQuery(this).attr('checked'),
		    metasection = jQuery(this).data('metasection');
	if ('checked' == state) {
		jQuery('.js-metasection-help-' + metasection).show();
		jQuery('.js-wpv-show-hide-' + metasection + '-help-value').val('on');
	} else {
		jQuery('.js-metasection-help-' + metasection).hide();
		jQuery('.js-wpv-show-hide-' + metasection + '-help-value').val('off');
	}
});

/**
* Quicktags custom implementation fallback
*/

if ( typeof WPV_Toolset.add_qt_editor_buttons !== 'function' ) {
    WPV_Toolset.add_qt_editor_buttons = function( qt_instance, editor_instance ) {
		var activeUrlEditor;
        QTags._buttonsInit();
		if ( typeof WPV_Toolset.CodeMirror_instance[qt_instance.id] === "undefined" ) {
			WPV_Toolset.CodeMirror_instance[qt_instance.id] = editor_instance;
		}
        for ( var button_name in qt_instance.theButtons ) {
			if ( qt_instance.theButtons.hasOwnProperty( button_name ) ) {
				qt_instance.theButtons[button_name].old_callback = qt_instance.theButtons[button_name].callback;
                if ( qt_instance.theButtons[button_name].id == 'img' ){
                    qt_instance.theButtons[button_name].callback = function( element, canvas, ed ) {
						var t = this,
						id = jQuery( canvas ).attr( 'id' ),
						selection = WPV_Toolset.CodeMirror_instance[id].getSelection(),
						e = "http://",
						g = prompt( quicktagsL10n.enterImageURL, e ),
						f = prompt( quicktagsL10n.enterImageDescription, "" );
						t.tagStart = '<img src="' + g + '" alt="' + f + '" />';
						selection = t.tagStart;
						t.closeTag( element, ed );
						WPV_Toolset.CodeMirror_instance[id].replaceSelection( selection, 'end' );
						WPV_Toolset.CodeMirror_instance[id].focus();
                    }
                } else if ( qt_instance.theButtons[button_name].id == 'wpv_conditional' ) {
                    qt_instance.theButtons[button_name].callback = function ( e, c, ed ) {                     
                        WPV_Toolset.activeUrlEditor = ed;                        
						var id = jQuery( c ).attr( 'id' ),
                        t = this;
                        window.wpcfActiveEditor = id;
                        WPV_Toolset.CodeMirror_instance[id].focus();
                        selection = WPV_Toolset.CodeMirror_instance[id].getSelection();
						var current_editor_object = {};
						if ( selection ) {
						   //When texty selected
						   current_editor_object = {'e' : e, 'c' : c, 'ed' : ed, 't' : t, 'post_id' : '', 'close_tag' : true, 'codemirror' : id};
						   WPViews.shortcodes_gui.wpv_insert_popup_conditional('wpv-conditional', wpv_shortcodes_gui_texts.wpv_insert_conditional_shortcode, {}, wpv_shortcodes_gui_texts.wpv_editor_callback_nonce, current_editor_object );
						} else if ( ed.openTags ) {
							// if we have an open tag, see if it's ours
							var ret = false, i = 0, t = this;
							while ( i < ed.openTags.length ) {
								ret = ed.openTags[i] == t.id ? i : false;
								i ++;
							}
							if ( ret === false ) {
								t.tagStart = '';
								t.tagEnd = false;                
								if ( ! ed.openTags ) {
									ed.openTags = [];
								}
								ed.openTags.push(t.id);
								e.value = '/' + e.value;
								current_editor_object = {'e' : e, 'c' : c, 'ed' : ed, 't' : t, 'post_id' : '', 'close_tag' : false, 'codemirror' : id};
								WPViews.shortcodes_gui.wpv_insert_popup_conditional('wpv-conditional', wpv_shortcodes_gui_texts.wpv_insert_conditional_shortcode, {}, wpv_shortcodes_gui_texts.wpv_editor_callback_nonce, current_editor_object );
							} else {
								// close tag
								ed.openTags.splice(ret, 1);
								t.tagStart = '[/wpv-conditional]';
								e.value = t.display;
								window.icl_editor.insert( t.tagStart );
							}
						} else {
							// last resort, no selection and no open tags
							// so prompt for input and just open the tag           
							t.tagStart = '';
							t.tagEnd = false;
							if ( ! ed.openTags ) {
								ed.openTags = [];
							}
							ed.openTags.push(t.id);
							e.value = '/' + e.value;
							current_editor_object = {'e' : e, 'c' : c, 'ed' : ed, 't' : t, 'post_id' : '', 'close_tag' : false, 'codemirror' : id};
							WPViews.shortcodes_gui.wpv_insert_popup_conditional('wpv-conditional', wpv_shortcodes_gui_texts.wpv_insert_conditional_shortcode, {}, wpv_shortcodes_gui_texts.wpv_editor_callback_nonce, current_editor_object );
						}
					}
                } else if ( qt_instance.theButtons[button_name].id == 'close' ) {
                    
                } else if ( qt_instance.theButtons[button_name].id == 'link' ) {
					var t = this;
					qt_instance.theButtons[button_name].callback = function ( b, c, d, e ) {
						activeUrlEditor = c;var f,g=this;return"undefined"!=typeof wpLink?void wpLink.open(d.id):(e||(e="http://"),void(g.isOpen(d)===!1?(f=prompt(quicktagsL10n.enterURL,e),f&&(g.tagStart='<a href="'+f+'">',a.TagButton.prototype.callback.call(g,b,c,d))):a.TagButton.prototype.callback.call(g,b,c,d)))
					};
					jQuery( '#wp-link-submit' ).off();
					jQuery( '#wp-link-submit' ).on( 'click', function( event ) {
						event.preventDefault();
						var id = jQuery( activeUrlEditor ).attr('id'),
						selection = WPV_Toolset.CodeMirror_instance[id].getSelection(),
						inputs = {},
						attrs, text, title, html;
						inputs.wrap = jQuery('#wp-link-wrap');
						inputs.backdrop = jQuery( '#wp-link-backdrop' );
						if ( jQuery( '#link-target-checkbox' ).length > 0 ) {
							// Backwards compatibility - before WordPress 4.2
							inputs.text = jQuery( '#link-title-field' );
							attrs = wpLink.getAttrs();
							text = inputs.text.val();
							if ( ! attrs.href ) {
								return;
							}
							// Build HTML
							html = '<a href="' + attrs.href + '"';
							if ( attrs.target ) {
								html += ' target="' + attrs.target + '"';
							}
							if ( text ) {
								title = text.replace( /</g, '&lt;' ).replace( />/g, '&gt;' ).replace( /"/g, '&quot;' );
								html += ' title="' + title + '"';
							}
							html += '>';
							html += text || selection;
							html += '</a>';
							t.tagStart = html;
							selection = t.tagStart;
						} else {
							// WordPress 4.2+
							inputs.text = jQuery( '#wp-link-text' );
							attrs = wpLink.getAttrs();
							text = inputs.text.val();
							if ( ! attrs.href ) {
								return;
							}
							// Build HTML
							html = '<a href="' + attrs.href + '"';
							if ( attrs.target ) {
								html += ' target="' + attrs.target + '"';
							}
							html += '>';
							html += text || selection;
							html += '</a>';
							selection = html;
						}
						jQuery( document.body ).removeClass( 'modal-open' );
						inputs.backdrop.hide();
						inputs.wrap.hide();
						jQuery( document ).trigger( 'wplink-close', inputs.wrap );
						WPV_Toolset.CodeMirror_instance[id].replaceSelection( selection, 'end' );
						WPV_Toolset.CodeMirror_instance[id].focus();
						return false;
                    });
                } else {
                    qt_instance.theButtons[button_name].callback = function( element, canvas, ed ) {                    
                        var id = jQuery( canvas ).attr( 'id' ),
                        t = this,
                        selection = WPV_Toolset.CodeMirror_instance[id].getSelection();
						if ( selection.length > 0 ) { 
							if ( !t.tagEnd ) {
								selection = selection + t.tagStart;
							} else {
								selection = t.tagStart + selection + t.tagEnd;
							}
						} else {
							if ( !t.tagEnd ) {
								selection = t.tagStart;
							} else if ( t.isOpen( ed ) === false ) {
								selection = t.tagStart;
								t.openTag( element, ed );
							} else {
								selection = t.tagEnd;
								t.closeTag( element, ed );
							}
						}
                        WPV_Toolset.CodeMirror_instance[id].replaceSelection(selection, 'end');
                        WPV_Toolset.CodeMirror_instance[id].focus();
                    }
                }
			}
		}
    }
}