var WPV_Toolset = WPV_Toolset  || {};
if ( typeof WPV_Toolset.CodeMirror_instance === "undefined" ) {
	WPV_Toolset.CodeMirror_instance = [];
}

var codemirror_views_query = icl_editor.codemirror('wpv_filter_meta_html_content', true),
codemirror_views_query_val = codemirror_views_query.getValue(),
wpv_filter_meta_html_content_qt = quicktags( { id: 'wpv_filter_meta_html_content', buttons: 'strong,em,link,block,del,ins,img,ul,ol,li,code,close' } ),
codemirror_views_query_css = icl_editor.codemirror('wpv_filter_meta_html_css', true, 'css'),
codemirror_views_query_css_val = codemirror_views_query_css.getValue(),
codemirror_views_query_js = icl_editor.codemirror('wpv_filter_meta_html_js', true, 'javascript'),
codemirror_views_query_js_val = codemirror_views_query_js.getValue(),
codemirror_views_layout = icl_editor.codemirror('wpv_layout_meta_html_content', true),
codemirror_views_layout_val = codemirror_views_layout.getValue(),
wpv_layout_meta_html_content_qt = quicktags( { id: 'wpv_layout_meta_html_content', buttons: 'strong,em,link,block,del,ins,img,ul,ol,li,code,close' } ),
codemirror_views_layout_css = icl_editor.codemirror('wpv_layout_meta_html_css', true, 'css'),
codemirror_views_layout_css_val = codemirror_views_layout_css.getValue(),
codemirror_views_layout_js = icl_editor.codemirror('wpv_layout_meta_html_js', true, 'javascript'),
codemirror_views_layout_js_val = codemirror_views_layout_js.getValue(),
codemirror_views_content = icl_editor.codemirror('wpv_content', true),
codemirror_views_content_val = codemirror_views_content.getValue(),
wpv_content_qt = quicktags( { id: 'wpv_content', buttons: 'strong,em,link,block,del,ins,img,ul,ol,li,code,close' } );

WPV_Toolset.CodeMirror_instance['wpv_filter_meta_html_content'] =  codemirror_views_query;
WPV_Toolset.CodeMirror_instance['wpv_filter_meta_html_css'] =  codemirror_views_query_css;
WPV_Toolset.CodeMirror_instance['wpv_filter_meta_html_js'] =  codemirror_views_query_js;
WPV_Toolset.CodeMirror_instance['wpv_layout_meta_html_content'] =  codemirror_views_layout;
WPV_Toolset.CodeMirror_instance['wpv_layout_meta_html_css'] =  codemirror_views_layout_css;
WPV_Toolset.CodeMirror_instance['wpv_layout_meta_html_js'] =  codemirror_views_layout_js;
WPV_Toolset.CodeMirror_instance['wpv_content'] =  codemirror_views_content;

// Define 'save' command in CodeMirror object
// This automatically adds Ctrl+S (Cmd+S in Mac) keyboard shortcut for saving
// in every CodeMirror instance.
// This code is partly duplicated in views_archive_editor.js
CodeMirror.commands.save = function(cm) {
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
            textarea_id === 'wpv_filter_meta_html_content' ||
            textarea_id === 'wpv_filter_meta_html_css' ||
            textarea_id === 'wpv_filter_meta_html_js'
            ) {
        /* Filter */
        jQuery('.js-wpv-filter-extra-update').click();
    } else if (
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

var WPViews = WPViews || {};

WPViews.ViewEditScreen = function( $ ) {
	var self = this;
	self.view_id = $('.js-post_ID').val();
	self.show_hide_sections =  $( '.js-wpv-show-hide-container' ).find('.js-wpv-show-hide-value').serialize();
	self.show_hide_metasections_help =  $( '.js-wpv-show-hide-container' ).find('.js-wpv-show-hide-help-value').serialize();
	self.purpose = $('.js-view-purpose').val();
	self.purpose_extra_settings = '.js-wpv-display-for-purpose';
	self.query_type = $('.js-wpv-query-type:checked').val();
	self.pag_mode = $( '.js-wpv-pagination-mode:checked' ).val();
	self.pag_instructions_selector = '.js-wpv-editor-instructions-for-pagination';
		
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
	
	self.manage_action_bar_error = function( data ) {
		if ( data.message ) {
			$.colorbox.close();
			self.action_bar_message_container
				.wpvToolsetMessage({
					text: data.message,
					type: 'error',
					inline: false,
					stay: true
				});
		}
	};
	
	// ---------------------------------
	// Screen options and View purpose
	// ---------------------------------
	
	// Screen options - position fix
	
	self.screen_options_fix = function() {
		var views_screen_options = $('.js-screen-meta-links-dup > div'),
		views_screen_options_container = $('.js-screen-meta-dup > div');
		$('#screen-meta-links').append(views_screen_options);
		$('#screen-meta').append(views_screen_options_container);
	};
	
	// Screen options - display help for purpose
	
	self.display_view_howto_help_box = function( purpose ) {
		$( '.js-display-view-howto' ).hide();
		$( '.js-display-view-howto.js-for-view-purpose-' + purpose ).show();
	};
	
	// Screen options - show/hide metasections
	
	self.show_hide_metasections_init = function() {
		$( '.js-wpv-show-hide-section' ).each(function(){
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
	
	self.show_hide_purpose_help_init = function() {
		$( '.js-wpv-show-hide-help' ).each( function() {
			var metasection = $( this ).data( 'metasection' ),
			state = $( this ).attr( 'checked' );
			if ('checked' == state) {
				$( '.js-metasection-help-' + metasection + '.js-for-view-purpose-' + self.purpose ).show();
			} else {
				$( '.js-metasection-help-' + metasection + '.js-for-view-purpose-' + self.purpose ).hide();
			}
		});
	};
	
	// Screen options - update automatically
	
	self.save_view_screen_options = function() {
		var container = $( '.js-wpv-show-hide-container' ),
		wpv_show_hide_sections = container.find('.js-wpv-show-hide-value').serialize(),
		wpv_show_hide_metasections_help = container.find('.js-wpv-show-hide-help-value').serialize(),
		purpose = container.find('.js-view-purpose').val();
		container.find('.toolset-alert').remove();
		
		if ( self.show_hide_sections == wpv_show_hide_sections
			&& self.show_hide_metasections_help == wpv_show_hide_metasections_help
			&& self.purpose == purpose
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
				purpose: purpose,
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
				self.purpose = purpose;
			});
		}
		
	};
	
	self.screen_options_debounce_update = _.debounce( self.save_view_screen_options, 1000 );
	
	// Screen options - events
	
	$( document ).on( 'change', '.js-view-purpose', function() {
		self.purpose = $( this ).val();
		self.display_view_howto_help_box( self.purpose );
		self.manage_purpose_dependent();
	});
	
	$( document ).on( 'click', '#screen-meta-links #contextual-help-link', function() {
		// Fix when opening Help section
		// This is caused because we are adding our Screen Options in an artificial way
		// so when opening the Help tab it displays all elements inside the tab container
		$( '.metabox-prefs .js-wpv-show-hide-container' ).hide();
	});
	
	$( document ).on( 'change', '.js-wpv-show-hide-container .js-wpv-show-hide, .js-wpv-show-hide-container .js-wpv-show-hide-help, .js-wpv-show-hide-container .js-view-purpose', function() {
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
		spinnerContainer = $('<div class="spinner ajax-loader">').insertAfter( thiz ).show(),
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
	// Content selection
	// ---------------------------------
	
	// Content selection - mandatory selection
	
	self.content_selection_mandatory = function() {
		if (
			( $('.js-wpv-query-post-type:checked').length == 0 && self.query_type == 'posts' )
			|| ( $('.js-wpv-query-taxonomy-type:checked').length == 0 && self.query_type == 'taxonomy' )
			|| ( $('.js-wpv-query-users-type:checked').length == 0 && self.query_type == 'users' )
		) {	
			// Show the warning message
			$( '.js-wpv-content-selection-mandatory-warning' ).show();
			// Disable further Views editing
			$( '.wpv-setting-container:not(.js-wpv-no-lock)' ).prepend( self.overlay_container );
			// Add glow to inputs
			$( '.js-wpv-query-post-type, .js-wpv-query-taxonomy-type, .js-wpv-query-users-type' ).css( {'box-shadow': '0 0 5px 1px #f6921e'} );
		} else {
			// Hide the warning message
			$( '.js-wpv-content-selection-mandatory-warning' ).hide();
			// Enable further Views editing
			$( '.js-wpv-setting-overlay' ).fadeOut( 500, function() {
				$( '.js-wpv-setting-overlay' ).remove();
			});
			// Remove glow from inputs
			$( '.js-wpv-query-post-type, .js-wpv-query-taxonomy-type, .js-wpv-query-users-type' ).css( {'box-shadow': 'none'} );
		}
	};
	
	// Content selection - change sections based on query type
	
	self.query_type_sections = function() {
		if ('posts' == self.query_type) {
			$( '.wpv-settings-query-type-taxonomy, .wpv-settings-query-type-users' ).hide();
			$( '.wpv-settings-query-type-posts' ).fadeIn( 'fast' );
			$( '.wpv-vicon-for-taxonomy, .wpv-vicon-for-users' ).addClass( 'hidden' );
			$( '.wpv-vicon-for-posts').removeClass( 'hidden' );
		} else if ('taxonomy' == self.query_type) {
			$( '.wpv-settings-query-type-posts, .wpv-settings-query-type-users' ).hide();
			$( '.wpv-settings-query-type-taxonomy' ).fadeIn( 'fast' );
			$( '.wpv-vicon-for-posts, .wpv-vicon-for-users' ).addClass( 'hidden' );
			$( '.wpv-vicon-for-taxonomy' ).removeClass( 'hidden' );
		} else if ('users' == self.query_type) {
			$( '.wpv-settings-query-type-posts, .wpv-settings-query-type-taxonomy' ).hide();
			$( '.wpv-settings-query-type-users' ).fadeIn( 'fast' );
			$( '.wpv-vicon-for-posts, .wpv-vicon-for-taxonomy' ).addClass( 'hidden' );
			$( '.wpv-vicon-for-users' ).removeClass( 'hidden' );
		}
	};
	
	// Content selection - update automatically
	
	self.save_view_query_type_options = function() {
		var dataholder = $( '.js-wpv-query-type-update' ),
		section_container = $( '.js-wpv-settings-content-selection' ),
		messages_container = dataholder.parents( '.js-wpv-update-action-wrap' ).find( '.js-wpv-message-container' ),
		unsaved_message = dataholder.data('unsaved'),
		nonce = dataholder.data('nonce'),
		wpv_query_post_items = [],
		wpv_query_taxonomy_items = [],
		wpv_query_users_items = [],
		spinnerContainer,
		query_type = $('input:radio.js-wpv-query-type:checked').val();
		section_container.find( '.spinner.ajax-loader' ).remove();
		messages_container.find('.toolset-alert-error').remove();
		spinnerContainer = $('<div class="spinner ajax-loader">').insertBefore( dataholder ).show();
		$('.js-wpv-query-post-type:checked').each( function() {
			wpv_query_post_items.push( $(this).val() );
		});
		$('.js-wpv-query-taxonomy-type:checked').each( function() {
			wpv_query_taxonomy_items.push( $(this).val() );
		});
		$('.js-wpv-query-users-type:checked').each( function() {
			wpv_query_users_items.push( $(this).val() );
		});
		var data = {
			action: 'wpv_update_query_type',
			id: self.view_id,
			query_type: query_type,
			post_type_slugs: wpv_query_post_items,
			taxonomy_type_slugs: wpv_query_taxonomy_items,
			roles_type_slugs: wpv_query_users_items,
			wpnonce: nonce
		};
		$.ajax({
			type: "POST",
			dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( response.success ) {
					$('.js-screen-options').find('.toolset-alert').remove();
						if ( response.data.updated_filters_list != 'no_change' ) {
							$( '.js-filter-list' ).html( response.data.updated_filters_list );
						}
						if ( response.data.updated_flatten_types_relationship_tree == 'NONE' ) {
							$('.js-flatten-types-relation-tree').val( 'NONE' );
						} else {
							$('.js-flatten-types-relation-tree').val( response.data.updated_flatten_types_relationship_tree );
						}
						$( document ).trigger( 'js_event_wpv_query_type_saved' );
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
				dataholder.trigger( 'js_event_wpv_query_type_options_saved', [ query_type ] );
			}
		});
	};
	
	self.content_selection_debounce_update = _.debounce( self.save_view_query_type_options, 1000 );
	
	// Content selection - events
	
	$( document ).on( 'change', '.js-wpv-query-type', function() {
		self.query_type = $('.js-wpv-query-type:checked').val();
		self.query_type_sections();
		self.content_selection_mandatory();
		self.content_selection_debounce_update();
	});
	
	$( document ).on('change', '.js-wpv-query-post-type, .js-wpv-query-taxonomy-type, .js-wpv-query-users-type', function(){
		self.content_selection_mandatory();
		self.content_selection_debounce_update();
	});
	
	// ---------------------------------
	// Query options
	// ---------------------------------
	
	// Query options - update automatically
	
	self.save_view_query_options = function() {
		var dataholder = $( '.js-wpv-query-options-update' ),
		messages_container = dataholder.parents( '.js-wpv-update-action-wrap' ).find( '.js-wpv-message-container' ),
		section_container = $( '.js-wpv-settings-query-options' ),
		unsaved_message = dataholder.data('unsaved'),
		nonce = dataholder.data('nonce'),
		spinnerContainer,
		view_id = self.view_id;
		section_container.find( '.spinner.ajax-loader' ).remove();
		messages_container.find('.toolset-alert-error').remove();
		spinnerContainer = $('<div class="spinner ajax-loader">').insertBefore( dataholder ).show();
		var data = {
			action: 'wpv_update_query_options',
			id: view_id,
			post_type_dont_include_current_page: $('.js-wpv-query-options-post-type-dont:checked').length,
			taxonomy_hide_empty: $('.js-wpv-query-options-taxonomy-hide-empty:checked').length,
			taxonomy_include_non_empty_decendants: $('.js-wpv-query-options-taxonomy-non-empty-decendants:checked').length,
			taxonomy_pad_counts: $('.js-wpv-query-options-taxonomy-pad-counts:checked').length,
			uhide : $('.js-wpv-query-options-users-show-current:checked').length,
			wpnonce: nonce
		};
		$.ajax({
			type: "POST",
			dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( response.success ) {
					$('.js-screen-options').find('.toolset-alert').remove();
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
			}
		});
	};
	
	self.query_options_debounce_update = _.debounce( self.save_view_query_options, 2000 );
	
	// Query options - events
	
	$( document ).on( 'change', '.js-wpv-query-options-users-show-current, .js-wpv-query-options-post-type-dont, .js-wpv-query-options-taxonomy-hide-empty, .js-wpv-query-options-taxonomy-non-empty-decendants, .js-wpv-query-options-taxonomy-pad-counts', function() {
		self.query_options_debounce_update();
	});
	
	// ---------------------------------
	// Sorting
	// ---------------------------------
	
	// Sorting - update automatically
	
	self.save_view_sorting_options = function() {
		var dataholder = $( '.js-wpv-ordering-update' ),
		messages_container = dataholder.parents( '.js-wpv-update-action-wrap' ).find( '.js-wpv-message-container' ),
		section_container = $( '.js-wpv-settings-ordering' ),
		unsaved_message = dataholder.data( 'unsaved' ),
		nonce = dataholder.data( 'nonce' ),
		spinnerContainer,
		view_id = self.view_id;
		section_container.find( '.spinner.ajax-loader' ).remove();
		messages_container.find('.toolset-alert-error').remove();
		spinnerContainer = $('<div class="spinner ajax-loader">').insertBefore( dataholder ).show();
		var data = {
			action: 'wpv_update_sorting',
			id: view_id,
			orderby: $('.js-wpv-posts-orderby').val(),
			order: $('.js-wpv-posts-order').val(),
			taxonomy_orderby: $('.js-wpv-taxonomy-orderby').val(),
			taxonomy_order: $('.js-wpv-taxonomy-order').val(),
			users_orderby: $('.js-wpv-users-orderby').val(),
			users_order: $('.js-wpv-users-order').val(),
			wpnonce: nonce
		};
		$.ajax({
			type: "POST",
			dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( response.success ) {
					$('.js-screen-options').find('.toolset-alert').remove();
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
			}
		});
	};
	
	self.sorting_debounce_update = _.debounce( self.save_view_sorting_options, 2000 );
	
	// Sorting - rand and pagination do not work well together
	
	self.sorting_random_and_pagination = function() {
		$('.js-wpv-settings-posts-order .toolset-alert, .js-wpv-settings-pagination .js-pagination-settings-form .toolset-alert').remove();
		if ( $( '.js-wpv-posts-orderby' ).val() == 'rand' && $( '.js-wpv-pagination-mode:checked' ).val() != 'none' ) {
			$('.js-wpv-settings-ordering .js-wpv-toolset-messages, .js-wpv-settings-pagination .js-wpv-toolset-messages' )
				.wpvToolsetMessage({
					text: $( '.js-wpv-posts-orderby' ).data( 'rand' ),
					stay: true,
					close: false,
					type: ''
				});
		}
	};
	
	// Sorting - events
	
	$( document ).on( 'change', '.js-wpv-posts-orderby, .js-wpv-posts-order, .js-wpv-taxonomy-orderby, .js-wpv-taxonomy-order, .js-wpv-users-orderby, .js-wpv-users-order', function() {
		self.sorting_random_and_pagination();
		self.sorting_debounce_update();
	});
	
	// ---------------------------------
	// Limit and offset
	// ---------------------------------
	
	// Limit and offset - update automatically
	
	self.save_view_limit_offset_options = function() {
		var dataholder = $( '.js-wpv-limit-offset-update' ),
		messages_container = dataholder.parents( '.js-wpv-update-action-wrap' ).find( '.js-wpv-message-container' ),
		section_container = $( '.js-wpv-settings-limit-offset' ),
		unsaved_message = dataholder.data('unsaved'),
		nonce = dataholder.data('nonce'),
		spinnerContainer,
		view_id = self.view_id;
		section_container.find( '.spinner.ajax-loader' ).remove();
		messages_container.find('.toolset-alert-error').remove();
		spinnerContainer = $('<div class="spinner ajax-loader">').insertBefore( dataholder ).show();
		var data = {
			action: 'wpv_update_limit_offset',
			id: view_id,
			limit: $( '.js-wpv-limit' ).val(),
			offset: $( '.js-wpv-offset' ).val(),
			taxonomy_limit: $( '.js-wpv-taxonomy-limit' ).val(),
			taxonomy_offset: $( '.js-wpv-taxonomy-offset' ).val(),
			users_limit: $( '.js-wpv-users-limit' ).val(),
			users_offset: $( '.js-wpv-users-offset' ).val(),
			wpnonce: nonce
		};
		$.ajax({
			type: "POST",
			dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( response.success ) {
					$('.js-screen-options').find('.toolset-alert').remove();
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
			}
		});
	};
	
	self.limit_offset_debounce_update = _.debounce( self.save_view_limit_offset_options, 2000 );
	
	// Limit and offset - events
	
	$( document ).on( 'change', '.js-wpv-limit, .js-wpv-offset, .js-wpv-taxonomy-limit, .js-wpv-taxonomy-offset, .js-wpv-users-limit, .js-wpv-users-offset', function() {
		self.limit_offset_debounce_update();
	});
	
	// ---------------------------------
	// Pagination
	// ---------------------------------
	
	// Pagination - init and change pagination mode
	
	self.pagination_mode = function() {
		$( '.wpv-pagination-paged, .wpv-pagination-rollover, .wpv-pagination-advanced' ).hide();
		if ('paged' == self.pag_mode ) {
			$('.wpv-pagination-rollover, .wpv-pagination-shared, .wpv-pagination-paged-ajax, .wpv-pagination-advanced').hide();
			$('.wpv-pagination-paged, .wpv-pagination-options-box').fadeIn('fast');
			$('.js-pagination-zero').val('enable');
			self.pagination_ajax();
		} else if ('rollover' == self.pag_mode ) {
			$('.wpv-pagination-paged').hide();
			$('.wpv-pagination-rollover').fadeIn('fast');
			$('.wpv-pagination-paged-ajax, .wpv-pagination-advanced').hide();
			$('.wpv-pagination-options-box').fadeIn('fast');
			$('.js-pagination-zero').val('enable');
		} else {
			$('.wpv-pagination-options-box, .wpv-pagination-paged, .wpv-pagination-rollover, .wpv-pagination-shared').hide();
			$('.js-pagination-zero').val('disable');
		}
	};
	
	// Pagination - init and change pagination AJAX settings (show/hide further AJAX settings based on AJAX mode)
	
	self.pagination_ajax = function() {
		$( '.wpv-pagination-advanced' ).hide();
		var paged_mode = $('.js-wpv-ajax_pagination:checked').val();
		if ( 'disable' == paged_mode || undefined === paged_mode ) {
			$( '.wpv-pagination-shared, .wpv-pagination-paged-ajax, .wpv-pagination-advanced, [data-section="ajax_pagination"]' ).hide();
		} else {
			var pag_mode = $( 'input[name="pagination\\[mode\\]"]:checked' ).val();
			if ( 'rollover' != pag_mode ) {
				$('.wpv-pagination-paged-ajax:not(.wpv-pagination-advanced)' ).fadeIn( 'fast' );
			}
			$( '.wpv-pagination-shared, .wpv-pagination-advanced' ).hide();
			$( '[data-section="ajax_pagination"]' ).show();
		}
	};
	
	// Pagination - init and change pagination spinners (show/hide further spinner settings based on spinner mode)
	
	self.pagination_spinners = function() {
		var pagination_spinner_setting = $( '.js-wpv-pagination-spinner:checked' ).val();
		$( '.js-wpv-pagination-spinner-default, .js-wpv-pagination-spinner-uploaded' ).hide();
		if ( pagination_spinner_setting == 'default' || pagination_spinner_setting == 'uploaded' ) {
			$( '.js-wpv-pagination-spinner-' + pagination_spinner_setting ).fadeIn();
		}
	};
	
	// Pagination - update automatically
	
	self.save_view_pagination_options = function() {
		var dataholder = $( '.js-wpv-pagination-update' ),
		messages_container = dataholder.parents( '.js-wpv-update-action-wrap' ).find( '.js-wpv-message-container' ),
		section_container = $( '.js-wpv-settings-pagination' ),
		unsaved_message = dataholder.data( 'unsaved' ),
		nonce = dataholder.data('nonce'),
		spinnerContainer,
		view_id = self.view_id,
		settings = $('.js-pagination-settings-form').serialize();
		section_container.find( '.spinner.ajax-loader' ).remove();
		messages_container.find('.toolset-alert-error').remove();
		spinnerContainer = $('<div class="spinner ajax-loader">').insertBefore( dataholder ).show();
		var data = {
			action: 'wpv_update_pagination',
			id: view_id,
			settings : settings,
			wpnonce: nonce
		};
		$.ajax({
			async:false,
			type: "POST",
			dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( response.success ) {
					$('.js-screen-options').find('.toolset-alert').remove();
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
			}
		});
	};
	
	self.pagination_debounce_update = _.debounce( self.save_view_pagination_options, 2000 );
	
	// Pagination - events
	
	$( document ).on( 'change', '.js-wpv-pagination-mode', function() {
		self.pag_mode = $( '.js-wpv-pagination-mode:checked' ).val();
		$( '.js-pagination-advanced' ).each( function() {
			$( this ).data('state','closed').text( $(this).data( 'closed' ) );
		});
		self.pagination_mode();
		self.manage_pagination_instructions();
	});
	
	$( document ).on( 'change', '.js-wpv-ajax_pagination', function() {
		$( '.js-pagination-advanced' ).each( function() {
			$( this ).data( 'state','closed' ).text( $( this ).data( 'closed' ) );
		});
		self.pagination_ajax();
	});
	
	$( document ).on( 'click', '.js-pagination-advanced', function() {
		var state = $(this).data('state'),
		text = '';
		if ( state == 'closed' ) {
			$( this ).data( 'state','opened' ).text( $( this ).data( 'opened' ) );
			$( '.wpv-pagination-advanced' ).fadeIn( 'fast' );
		} else if ( state == 'opened' ) {
			$( this ).data( 'state','closed' ).text( $( this ).data( 'closed' ) );
			$( '.wpv-pagination-advanced' ).hide();
		}
	});
	
	$( document ).on( 'change', '.js-wpv-pagination-spinner', function() {
		self.pagination_spinners();
	});
	
	$( document ).on( 'change keyup input cut paste', '.js-pagination-settings-form input, .js-pagination-settings-form select', function() {
		self.pagination_debounce_update();
	});
	
	// ---------------------------------
	// Parametric search
	// ---------------------------------
	
	// Parametric search - update automatically
	
	self.save_view_parametric_search_options = function() {
		var dataholder = $( '.js-wpv-filter-dps-update' ),
		messages_container = dataholder.parents( '.js-wpv-update-action-wrap' ).find( '.js-wpv-message-container' ),
		section_container = $( '.js-wpv-settings-filter-extra-parametric' ),
		nonce = dataholder.data('nonce'),
		view_id = self.view_id,
		spinnerContainer,
		unsaved_message = dataholder.data('unsaved'),
		dps_data = $('.js-wpv-dps-settings input, .js-wpv-dps-settings select').serialize();
		section_container.find( '.spinner.ajax-loader' ).remove();
		messages_container.find('.toolset-alert-error').remove();
		spinnerContainer = $('<div class="spinner ajax-loader">').insertBefore( dataholder ).show();
		var params = {
			action: 'wpv_filter_update_dps_settings',
			id: view_id,
			dpsdata: dps_data,
			wpnonce: nonce
		}
		$.ajax({
			type: "POST",
			dataType: "json",
			url: ajaxurl,
			data: params,
			success: function( response ) {
				if ( response.success ) {
					
				} else {
					self.manage_ajax_fail( response.data, messages_container );
				}
			},
			error:function(ajaxContext){
				messages_container
					.wpvToolsetMessage({
						 text:unsaved_message,
						 type:'error',
						 inline:true,
						 stay:true
					});
				console.log( "Error: ", ajaxContext.responseText );
			},
			complete:function(){
				spinnerContainer.remove();
			}
		});
	};
	
	self.parametric_search_debounce_update = _.debounce( self.save_view_parametric_search_options, 1000 );
	
	// Parametric search - events
	
	$( document ).on( 'change keypress keyup input cut paste', '.js-wpv-dps-settings input', function() {
		self.parametric_search_debounce_update();
	});
	
	// ---------------------------------
	// CodeMirror
	// ---------------------------------
	
	// Refresh CodeMirror instances on init, after init of everything else
	// @todo use WPV_Toolset.CodeMirror_instance here to get rid of my globals
	
	self.refresh_codemirror = function( instance ) {
		if ( instance === 'all' ) {
			codemirror_views_query.refresh();
			codemirror_views_query_css.refresh();
			codemirror_views_query_js.refresh();
			codemirror_views_layout.refresh();
			codemirror_views_layout_css.refresh();
			codemirror_views_layout_js.refresh();
			codemirror_views_content.refresh();
		} else {
			if ( instance == 'filter-css-editor' ) {
				codemirror_views_query_css.refresh();
			} else if ( instance == 'filter-js-editor' ) {
				codemirror_views_query_js.refresh();
			} else if ( instance == 'layout-css-editor' ) {
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
		WPV_Toolset.add_qt_editor_buttons( wpv_filter_meta_html_content_qt, WPV_Toolset.CodeMirror_instance['wpv_filter_meta_html_content'] );
		WPV_Toolset.add_qt_editor_buttons( wpv_layout_meta_html_content_qt, WPV_Toolset.CodeMirror_instance['wpv_layout_meta_html_content'] );
		WPV_Toolset.add_qt_editor_buttons( wpv_content_qt, WPV_Toolset.CodeMirror_instance['wpv_content'] );
	};
	
	// ---------------------------------
	// Formatting help boxes
	// ---------------------------------
	
	self.show_hide_formatting_help = function( thiz ) {
		$( '.' + thiz.data( 'target' ) ).slideToggle( 400, function() {
			thiz
				.find( '.js-wpv-toggle-toggler-icon i' )
					.toggleClass( 'icon-caret-down icon-caret-up' );
		});
	};
	
	$( document ).on( 'click', '.js-wpv-editor-instructions-toggle', function() {
		var thiz = $( this );
		self.show_hide_formatting_help( thiz );
	});
	
	self.manage_purpose_dependent = function() {
		$ (self.purpose_extra_settings ).hide();
		$( self.purpose_extra_settings + '-' + self.purpose ).show();
	};
	
	self.manage_pagination_instructions = function() {
		if ( self.pag_mode == 'paged' || self.pag_mode == 'rollover' ) {
			$( self.pag_instructions_selector ).show();
		} else {
			$( self.pag_instructions_selector ).hide();
		}
	};
	
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
	// CSS and JS textareas
	// ---------------------------------
	
	self.editor_needs_flag = function( instance ) {
		var full = false;
		if ( instance == 'filter-css-editor' ) {
			full = ( codemirror_views_query_css.getValue() != '' );
		} else if ( instance == 'filter-js-editor' ) {
			full = ( codemirror_views_query_js.getValue() != '' );
		} else if ( instance == 'layout-css-editor' ) {
			full = ( codemirror_views_layout_css.getValue() != '' );
		} else if ( instance == 'layout-js-editor' ) {
			full = ( codemirror_views_layout_js.getValue() != '' );
		}
		return full;
	};
	
	$( document ).on( 'click', '.js-wpv-code-editor-toggler', function() {
		var thiz = $( this ),
		thiz_kind = thiz.data( 'kind' ),
		thiz_text_holder = thiz.find( 'span.js-wpv-text-holder' ),
		thiz_container = thiz.parents( 'li' ),
		thiz_flag = thiz_container.find( '.js-wpv-textarea-full' ),
		thiz_target = thiz_container.find( '.js-wpv-code-editor' ),
		thiz_target_id = thiz.data( 'target' );
		thiz_flag.hide();
		thiz_target
			.toggleClass('js-wpv-code-editor-closed');
		if ( thiz_target.hasClass( 'js-wpv-code-editor-closed' ) ) {
			if ( thiz_kind == 'css' ) {
				thiz_text_holder.text( wpv_editor_strings.meta_html_extra_css_open );
			} else if ( thiz_kind == 'js' ) {
				thiz_text_holder.text( wpv_editor_strings.meta_html_extra_js_open );
			}
			if ( self.editor_needs_flag( thiz_target_id ) ) {
				thiz_flag.animate( {width: 'toggle'}, 200 );
			}
		} else {
			if ( thiz_kind == 'css' ) {
				thiz_text_holder.text( wpv_editor_strings.meta_html_extra_css_close );
			} else if ( thiz_kind == 'js' ) {
				thiz_text_holder.text( wpv_editor_strings.meta_html_extra_js_close );
			}
		}
		thiz_target
			.slideToggle( 200, function() {
				if ( 
					! thiz_target.hasClass( 'js-wpv-code-editor-closed' ) 
					&& ! thiz_target.hasClass( 'js-wpv-code-editor-refreshed' ) 
				) {
					self.refresh_codemirror( thiz_target_id );
					thiz_target.addClass( 'js-wpv-code-editor-refreshed' ) 
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
	// CodeMirror panels
	// ---------------------------------
	
	self.codemirror_panel = function( instance, content, keep, type ) {
		
		var filter_editor_panel = document.createElement( "div" ),
		filter_editor_panel_content,
		filter_editor_panel_close,
		filter_editor_panel_close_feedback,
		filter_editor_panel_instance;
		
		filter_editor_panel.className = "wpv-codemirror-panel";
		filter_editor_panel.className += " wpv-codemirror-panel-" + type;
		
		filter_editor_panel_content = filter_editor_panel.appendChild( document.createElement( "span" ) );
		filter_editor_panel_content.textContent = content;
		
		if ( keep ) {
			filter_editor_panel_close = filter_editor_panel.appendChild( document.createElement( "i" ) );
			filter_editor_panel_close.className = "icon-remove-sign js-wpv-codemirror-panel-close";
		} else {
			filter_editor_panel_close_feedback = filter_editor_panel.appendChild(document.createElement("div"));
			filter_editor_panel_close_feedback.className = "wpv-codemirror-panel-close-feedback";
		}
		
		filter_editor_panel_instance = instance.addPanel( filter_editor_panel );
		
		if ( keep ) {
			CodeMirror.on(filter_editor_panel_close, "click", function() { filter_editor_panel_instance.clear(); });
		} else {
			setTimeout( function() {
				filter_editor_panel_instance.clear();
			}, 3000);
		}
		
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
		self.show_hide_purpose_help_init();
		// Manage editor instructions
		self.manage_purpose_dependent();
		self.manage_pagination_instructions();
		// Show or hide display help box based on purpose
		self.display_view_howto_help_box( self.purpose );
		// Title placeholder
		self.title_placeholder();
		// Content selector section is mandatory
		self.content_selection_mandatory();
		// Random order and pagination incompatible
		self.sorting_random_and_pagination();
		// Init pagination mode
		self.pagination_mode();
		// Init pagination ajax
		self.pagination_ajax();
		// Init pagination spinners
		self.pagination_spinners();
		// Refresh CodeMirror instances
		self.refresh_codemirror( 'all' );
		// Add quicktags to the right textareas
		self.add_quicktags();
		// Toolset compatibility
		self.toolset_compatibility();
	};
	
	self.init(); // call the init method

};

jQuery( document ).ready( function( $ ) {
    WPViews.view_edit_screen = new WPViews.ViewEditScreen( $ );
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
	var section_changed = jQuery('.js-wpv-show-hide-container').data('unclickable');
	if ('checked' == state) {
		var metasection = checkbox.parents('.js-wpv-show-hide-section').data('metasection');
		jQuery('.' + metasection).show();
		jQuery('.js-wpv-settings-' + section).fadeIn('fast');
		input_value.val('on');
		if ('filter-extra' == section) {
			// @todo use WPV_Toolset.CodeMirror_instance here to get rid of my globals
			codemirror_views_query.refresh();
			codemirror_views_query_css.refresh();
			codemirror_views_query_js.refresh();
			
			/* Also refresh "Combined output" editor, which was previously part of the 'content' section, but
			 * it's visibility depends on 'filter-extra' now. */
			codemirror_views_content.refresh();
		}
		if ('pagination' == section || 'filter-extra-parametric' == section) {
			if ('checked' != jQuery('.js-wpv-show-hide-filter-extra').attr('checked')) {
				jQuery('.js-wpv-show-hide-filter-extra').trigger('click');
				jQuery('.js-wpv-show-hide-container .js-wpv-toolset-messages')
					.wpvToolsetMessage({
						text:jQuery('.js-wpv-show-hide-container').data('pagneedsfilter'),
						type:'info',
						inline:true,
						stay:true
					});
			}
		}
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
		} else if ('filter-extra' == section && ( 'checked' == jQuery('.js-wpv-show-hide-pagination').attr('checked') || 'checked' == jQuery('.js-wpv-show-hide-filter-extra-parametric').attr('checked') ) ) {
			jQuery('.js-wpv-show-hide-filter-extra').attr('checked', true);
			jQuery('.js-wpv-show-hide-container .js-wpv-toolset-messages')
				.wpvToolsetMessage({
					text:jQuery('.js-wpv-show-hide-container').data('pagneedsfilter'),
					type:'info',
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

jQuery(document).on('click', '.js-metasection-help-query .js-toolset-help-close-main', function(){
	jQuery('.js-wpv-show-hide-query-help').prop('checked', false);
	jQuery('.js-wpv-show-hide-query-help-value').val('off');
});

jQuery(document).on('click', '.js-metasection-help-filter .js-toolset-help-close-main', function(){
	jQuery('.js-wpv-show-hide-filter-help').prop('checked', false);
	jQuery('.js-wpv-show-hide-filter-help-value').val('off');
});

jQuery(document).on('click', '.js-metasection-help-layout .js-toolset-help-close-main', function(){
	jQuery('.js-wpv-show-hide-layout-help').prop('checked', false);
	jQuery('.js-wpv-show-hide-layout-help-value').val('off');
});

jQuery(document).on('change', '.js-wpv-show-hide-help', function(){
	var state = jQuery(this).attr('checked'),
		    metasection = jQuery(this).data('metasection'),
		    purpose = jQuery('.js-view-purpose').val();
		    if ('checked' == state) {
			    jQuery('.js-for-view-purpose-' + purpose + '.js-metasection-help-' + metasection).show();
			    jQuery('.js-wpv-show-hide-' + metasection + '-help-value').val('on');
		    } else {
			    jQuery('.js-metasection-help-' + metasection).hide();
			    jQuery('.js-wpv-show-hide-' + metasection + '-help-value').val('off');
		    }
});

// Change View purpose

jQuery(document).on('change', '.js-view-purpose', function(){
	var purpose = jQuery(this).val();
	jQuery('.js-wpv-show-hide-help').each(function(){
		var state = jQuery(this).attr('checked'),
			metasection = jQuery(this).data('metasection');
	
		jQuery('.js-metasection-help-' + metasection).hide();
		if ('checked' == state) {
			jQuery('.js-for-view-purpose-' + purpose + '.js-metasection-help-' + metasection).show();
		}
	});
	wpv_set_sections_for_view_purpose(purpose);
});

// Given a View purpose, set the open and closed sections

function wpv_set_sections_for_view_purpose(purpose) {
	var all_sections = Array('query-options', 'limit-offset', 'pagination', 'filter-extra-parametric', 'filter-extra', 'layout-extra', 'pagination', 'content-filter');
	var hide_sections = Array();
	if ('all' == purpose) {
		hide_sections = Array('pagination', 'filter-extra-parametric', 'filter-extra');
	} else if ('pagination' == purpose) {
		hide_sections = Array('limit-offset');
	} else if ('slider' == purpose) {
		hide_sections = Array('limit-offset');
	} else if ('parametric' == purpose) {
		hide_sections = Array('query-options', 'limit-offset', 'pagination', 'content-filter');
	} else if ('full' == purpose) {
		
	}

	var sections_length = all_sections.length;
	for ( var i = 0; i < sections_length; i++ ) {
		var found = false,
		hide_length = hide_sections.length;
		for ( j = 0; j < hide_length; j++ ) {
			if ( all_sections[i] == hide_sections[j] ) {
				found = true;
			}
		}

		var item = jQuery( '.js-wpv-show-hide-' + all_sections[i] );
		item.attr( 'checked', !found ).css( {'box-shadow': '0 0 5px 1px #f6921e'} );
		wpv_show_hide_section_change( item );
	}
	setTimeout( function () {
		jQuery( '.js-wpv-show-hide' ).css( {'box-shadow': 'none'} );
	}, 1000 );
}

/**
* Quicktags custom implementation fallback
*/

if ( WPV_Toolset.add_qt_editor_buttons !== 'function' ) {
    WPV_Toolset.add_qt_editor_buttons = function( qt_instance, editor_instance ) {
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
                    t.tagStart = '<img src="'+g+'" alt="'+f+'" />';
                    selection = t.tagStart;
                    t.closeTag( element, ed );
                    WPV_Toolset.CodeMirror_instance[id].replaceSelection( selection, 'end' );
                    WPV_Toolset.CodeMirror_instance[id].focus();
                    }
                } else if ( qt_instance.theButtons[button_name].id == 'close' ) {
                    
                } else if ( qt_instance.theButtons[button_name].id == 'link' ) {
					var t = this;
					qt_instance.theButtons[button_name].callback = 
                        function ( b, c, d, e ) {
							activeUrlEditor = c;var f,g=this;return"undefined"!=typeof wpLink?void wpLink.open(d.id):(e||(e="http://"),void(g.isOpen(d)===!1?(f=prompt(quicktagsL10n.enterURL,e),f&&(g.tagStart='<a href="'+f+'">',a.TagButton.prototype.callback.call(g,b,c,d))):a.TagButton.prototype.callback.call(g,b,c,d)))
						} 
					;
					jQuery( '#wp-link-submit' ).off();
					jQuery( '#wp-link-submit' ).on( 'click', function() {
						var id = jQuery( activeUrlEditor ).attr('id'),
						selection = WPV_Toolset.CodeMirror_instance[id].getSelection(),
						target = '';
						if ( jQuery( '#link-target-checkbox' ).prop('checked') ) {
						  target = '_blank';
						}
						html = '<a href="' + jQuery('#url-field').val() + '"';
						title = '';
						if ( jQuery( '#link-title-field' ).val() ) {
							title = jQuery( '#link-title-field' ).val().replace( /</g, '&lt;' ).replace( />/g, '&gt;' ).replace( /"/g, '&quot;' );
							html += ' title="' + title + '"';
						}
						if ( target ) {
							html += ' target="' + target + '"';
						}
						html += '>';
						if ( selection === '' ) {
							html += title;
						} else {
							html += selection;
						}
						html += '</a>';
						t.tagStart = html;
						selection = t.tagStart;
						WPV_Toolset.CodeMirror_instance[id].replaceSelection( selection, 'end' );
						WPV_Toolset.CodeMirror_instance[id].focus();
						jQuery( '#wp-link-backdrop,#wp-link-wrap' ).hide();
						jQuery( document.body ).removeClass( 'modal-open' );
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