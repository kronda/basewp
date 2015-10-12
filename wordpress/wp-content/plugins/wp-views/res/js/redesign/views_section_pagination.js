var WPViews = WPViews || {};

WPViews.ViewEditScreenPaginationUX = function( $ ) {
	
	var self = this;
	self.pagination_pointer = null;
	self.pagination_insert_newline = false;
	self.codemirror_highlight_options = {
		className: 'wpv-codemirror-highlight'
	};
	
	self.dialog = null;
	
	// ---------------------------------
	// Dialogs
	// ---------------------------------
	
	self.init_dialogs = function() {
		var dialog_height = $( window ).height() - 100;
		self.dialog = $( "#js-hidden-messages-boxes-pointers-container .js-wpv-pagination-form-dialog" ).dialog({
			autoOpen: false,
			modal: true,
			title: wpv_pagination_texts.add_pagination_dialog_title,
			minWidth: 650,
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
					text: wpv_pagination_texts.add_pagination_dialog_cancel,
					click: function() {
						$( this ).dialog( "close" );
					}
				},
				{
					class: 'button-primary js-wpv-insert-pagination',
					text: wpv_pagination_texts.add_pagination_dialog_insert,
					click: function() {

					}
				}
			],
			open: function() {
				$( '.js-wpv-insert-pagination' )
					.prop( 'disabled', true )
					.removeClass( 'button-primary' )
					.addClass( 'button-secondary' );
				$( 'input.js-wpv-pagination-dialog-control, input.js-wpv-pagination-dialog-display' ).prop( 'checked', false );
				$( '.js-pagination-control-type.js-pagination-control-type-dropdown' )
					.prop( 'checked', true )
					.trigger( 'change' );
				$( '.js-pagination-control-type' ).prop( 'disabled', true );
				$( '.js-pagination-preview-element' ).addClass( 'disabled' );
			}
		});
	};
	
	// ---------------------------------
	// Functions
	// ---------------------------------
	
	self.pagination_insert_pointer = function() {
		self.pagination_pointer.pointer('close');
		if ( ! $( '.js-wpv-enabled-view-pagination-pointer' ).hasClass( 'js-wpv-pointer-dismissed' ) ) {
			var filter_html = codemirror_views_query.getValue();
			if (
				$( '.js-wpv-pagination-mode:checked' ).val() != 'none'
				&& filter_html.search('wpv-pager-current-page') == -1
				&& filter_html.search('wpv-pager-prev-page') == -1
				&& filter_html.search('wpv-pager-next-page') == -1
			) {
				self.pagination_pointer.pointer('open');
				self.pagination_pointer.pointer('reposition');
			}
		}
	};

	self.get_pagination_shortcode = function() {
		var output = '';
		$.each( $( 'input.js-wpv-pagination-dialog-control:checked' ), function() {
			var thiz = $( this ),
			value = thiz.val();
			if ( value == 'page_num' ) {
				output += '[wpv-pager-current-page]';
			} else if ( value == 'page_total' ) {
				output += '[wpv-pager-num-page]';
			} else if ( value == 'page_selector' ) {
				output += '[wpv-pager-current-page style="' + $( '.js-pagination-control-type:checked' ).val() + '"]';
			} else if ( value == 'page_controls' ) {
				output += '[wpv-pager-prev-page][wpml-string context="wpv-views"]Previous[/wpml-string][/wpv-pager-prev-page][wpv-pager-next-page][wpml-string context="wpv-views"]Next[/wpml-string][/wpv-pager-next-page]';
			}
		});
		return output;
	};
	
	// ---------------------------------
	// Events
	// ---------------------------------
	
	$( document ).on( 'change', '.js-wpv-pagination-mode', function() {
		self.pagination_insert_pointer();
	});
	
	// Insert pagination shortcode
	
	$( document ).on( 'click', '.js-wpv-pagination-popup', function() {
		var thiz = $( this ),
		active_textarea = thiz.data( 'content' ),
		current_cursor,
		text_before,
		text_after,
		insert_position;
		window.wpcfActiveEditor = active_textarea;
		self.pagination_pointer.pointer('close');
		if ( active_textarea == 'wpv_filter_meta_html_content' ) {
			current_cursor = codemirror_views_query.getCursor(true);
			text_before = codemirror_views_query.getRange({line:0,ch:0}, current_cursor);
			text_after = codemirror_views_query.getRange(current_cursor, {line:codemirror_views_query.lastLine(),ch:null});
			if ( 
				text_before.search(/\[wpv-filter-start.*?\]/g) == -1 
				|| text_after.search(/\[wpv-filter-end.*?\]/g) == -1 
			) {
				// Set the cursor at the end and open popup
				insert_position = codemirror_views_query.getSearchCursor( '[wpv-filter-end]', false );
				insert_position.findNext();
				codemirror_views_query.setSelection( insert_position.from(), insert_position.from() );
				self.pagination_insert_newline = true;
			}
		}
		if ( active_textarea == 'wpv_layout_meta_html_content' ) {
			current_cursor = codemirror_views_layout.getCursor(true);
			text_before = codemirror_views_layout.getRange({line:0,ch:0}, current_cursor);
			text_after = codemirror_views_layout.getRange(current_cursor, {line:codemirror_views_layout.lastLine(),ch:null});
			if ( 
				text_before.search(/\[wpv-layout-start.*?\]/g) == -1 
				|| text_after.search(/\[wpv-layout-end.*?\]/g) == -1 
			) {
				// Set the cursor at the end and open popup
				insert_position = codemirror_views_layout.getSearchCursor( '[wpv-layout-end]', false );
				insert_position.findNext();
				codemirror_views_layout.setSelection( insert_position.from(), insert_position.from() );
				self.pagination_insert_newline = true;
			}
		}
		self.dialog.dialog( 'open' );
	});
	
	$( document ).on( 'change', 'input.js-wpv-pagination-dialog-control', function() {
		var options_checked = $( '.js-wpv-pagination-dialog-control:checked' ),
		preview_elements = $( '.js-pagination-preview-element' );
		if ( options_checked.length > 0 ) {
			preview_elements.addClass('disabled');
			$.each( options_checked, function() {
				var thiz = $( this ),
				target = thiz.data( 'target' );
				preview_elements
					.filter('[data-name="' + target + '"]')
						.removeClass('disabled');
			});
			$( '.js-wpv-insert-pagination' )
				.prop( 'disabled', false )
				.addClass( 'button-primary' )
				.removeClass( 'button-secondary' );
		} else {
			preview_elements.addClass('disabled');
			$( '.js-wpv-insert-pagination' )
				.prop( 'disabled', true )
				.addClass( 'button-secondary' )
				.removeClass( 'button-primary' );
		}
		if ( $( '#pagination-include-page-selector:checked' ).length > 0 ) {
			$( '.js-pagination-control-type' ).prop( 'disabled', false );
		} else {
			$( '.js-pagination-control-type' ).prop( 'disabled', true );
		}
	});
	
	$( document ).on( 'change', '.js-pagination-control-type', function() {
		var page_selectors = $('.js-pagination-preview-element').filter('[data-name="page-selector"]'),
		val = $( '.js-pagination-control-type:checked' ).val();
		if ( val === 'link' ) {
			page_selectors.hide();
			page_selectors.filter('[data-type*="page-selector-link"]').show();
		}
		if ( val === 'drop_down' ) {
			page_selectors.hide();
			page_selectors.filter('[data-type*="page-selector-select"]').show();
		}
	});
	
	$( document ).on( 'click', '.js-wpv-insert-pagination', function() {
		var shortcode = '',
		wrap = $( 'input.js-wpv-pagination-dialog-display' ).prop('checked'),
		current_cursor,
		end_cursor,
		pagination_marker;
		shortcode = self.get_pagination_shortcode();
		if ( wrap ) {
			shortcode = '[wpv-pagination]' +  shortcode + '[/wpv-pagination]';
		}
		if ( self.pagination_insert_newline ) {
			shortcode += '\n';
			self.pagination_insert_newline = false;
		}
		if ( window.wpcfActiveEditor == 'wpv_filter_meta_html_content' ) {
			current_cursor = codemirror_views_query.getCursor( true );
			codemirror_views_query.setSelection( current_cursor, current_cursor );
			codemirror_views_query.replaceSelection( shortcode, 'end' );
			end_cursor = codemirror_views_query.getCursor( true );
			pagination_marker = codemirror_views_query.markText( current_cursor, end_cursor, self.codemirror_highlight_options );
			self.dialog.dialog( 'close' );
			codemirror_views_query.focus();
			setTimeout( function() {
				  pagination_marker.clear();
			}, 2000);
		}
		if ( window.wpcfActiveEditor == 'wpv_layout_meta_html_content' ) {
			current_cursor = codemirror_views_layout.getCursor( true );
			codemirror_views_layout.setSelection( current_cursor, current_cursor );
			codemirror_views_layout.replaceSelection( shortcode, 'end' );
			end_cursor = codemirror_views_layout.getCursor( true );
			pagination_marker = codemirror_views_layout.markText( current_cursor, end_cursor, self.codemirror_highlight_options );
			self.dialog.dialog( 'close' );
			codemirror_views_layout.focus();
			setTimeout( function() {
				  pagination_marker.clear();
			}, 2000);
		}
	});
	
	// Helper
	
	$( document ).on( 'click','.js-disable-events', function( e ) {
		e.preventDefault();
		return false;
	});
	
	// ---------------------------------
	// Init
	// ---------------------------------
	
	self.init = function() {
		self.pagination_pointer = $('.filter-html-editor .js-wpv-pagination-popup').first().pointer({
			pointerClass: 'wp-toolset-pointer wp-toolset-views-pointer',
			pointerWidth: 400,
			content: $( '.js-wpv-enabled-view-pagination-pointer' ).html(),
			position: {
				edge: 'bottom',
				align: 'left'
			},
			buttons: function( event, t ) {
				var button_close = $('<button class="button button-primary-toolset alignright js-wpv-close-this">' + wpv_pagination_texts.close + '</button>');
				button_close.bind( 'click.pointer', function( e ) {
					e.preventDefault();
					if ( t.pointer.find( '.js-wpv-dismiss-pointer:checked' ).length > 0 ) {
						var pointer_name = t.pointer.find( '.js-wpv-dismiss-pointer:checked' ).data( 'pointer' );
						$( document ).trigger( 'js_event_wpv_dismiss_pointer', [ pointer_name ] );
					}
					t.element.pointer('close');
					codemirror_views_query.focus();
				});
				return button_close;
			}
		});
		// Init pagination insert pointer
		// Ugly solution, but otherwise we can not be sure it will be added to the right position
		// due to some sections being shown/hidden on document.ready too, after this one
		setTimeout( function() {
			self.pagination_insert_pointer();
		}, 3000);
		self.init_dialogs();
	};
	
	self.init();

};

jQuery( document ).ready( function( $ ) {
    WPViews.view_edit_screen_pagination_ux = new WPViews.ViewEditScreenPaginationUX( $ );
});