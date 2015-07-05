var WPViews = WPViews || {};

WPViews.ViewEditScreenPaginationUX = function( $ ) {
	
	var self = this;
	self.pag_mode = $( '.js-wpv-pagination-mode:checked' ).val();
	self.pagination_pointer = null;
	self.pagination_insert_newline = false;
	self.codemirror_highlight_options = {
		className: 'wpv-codemirror-highlight'
	};
	
	// ---------------------------------
	// Functions
	// ---------------------------------
	
	self.toolbar_pagination_button_state = function() {
		if ( self.pag_mode == 'none' ) {
			$( '.js-editor-pagination-button-wrapper' )
				.addClass( 'hidden' );
		} else {
			$( '.js-editor-pagination-button-wrapper' )
				.removeClass( 'hidden' );
		}
	};
	
	self.pagination_insert_pointer = function() {
		self.pagination_pointer.pointer('close');
		if ( ! $( '.js-wpv-enabled-view-pagination-pointer' ).hasClass( 'js-wpv-pointer-dismissed' ) ) {
			var filter_html = codemirror_views_query.getValue();
			if (
				self.pag_mode != 'none'
				&& filter_html.search('wpv-pager-current-page') == -1
				&& filter_html.search('wpv-pager-prev-page') == -1
				&& filter_html.search('wpv-pager-next-page') == -1
			) {
				self.pagination_pointer.pointer('open');
				self.pagination_pointer.pointer('reposition');
			}
		}
	};
	
	self.get_pagination_preview = function(pag_control, pag_selector_mode) {
		var output = '';
		if (pag_control == 'page_num') {
			
		} else if (pag_control == 'page_selector') {
			
		} else if (pag_control == 'page_controls') {
			
		}
		return output;
	};

	self.get_pagination_shortcode = function( pag_control, pag_selector_mode ) {
		var output = '';
		$.each( pag_control, function( name, value ) {
			if ( value.value == 'page_num' ) {
				output += '[wpv-pager-current-page]';
			} else if ( value.value == 'page_total' ) {
				output += '[wpv-pager-num-page]';
			} else if ( value.value == 'page_selector' ) {
				output += '[wpv-pager-current-page style="' + pag_selector_mode + '"]';
			} else if ( value.value == 'page_controls' ) {
				output += '[wpv-pager-prev-page][wpml-string context="wpv-views"]Previous[/wpml-string][/wpv-pager-prev-page][wpv-pager-next-page][wpml-string context="wpv-views"]Next[/wpml-string][/wpv-pager-next-page]';
			}
		});
		return output;
	};
	
	// ---------------------------------
	// Events
	// ---------------------------------
	
	$( document ).on( 'change', '.js-wpv-pagination-mode', function() {
		self.pag_mode = $( '.js-wpv-pagination-mode:checked' ).val();
		self.toolbar_pagination_button_state();
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
			if ( text_before.search(/\[wpv-filter-start.*?\]/g) != -1 && text_after.search(/\[wpv-filter-end.*?\]/g) != -1 ) {
				
			} else {
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
			if ( text_before.search(/\[wpv-layout-start.*?\]/g) != -1 && text_after.search(/\[wpv-layout-end.*?\]/g) != -1 ) {
				
			} else {
				// Set the cursor at the end and open popup
				insert_position = codemirror_views_layout.getSearchCursor( '[wpv-layout-end]', false );
				insert_position.findNext();
				codemirror_views_layout.setSelection( insert_position.from(), insert_position.from() );
				self.pagination_insert_newline = true;
			}
		}
		jQuery.colorbox({
			inline: true,
			href: '#js-hidden-messages-boxes-pointers-container .js-wpv-pagination-form-dialog',
			open: true,
			onComplete : function() {
				self.initialize_pagination_dialog();
			}
		});
	});
	
	self.initialize_pagination_dialog = function() {
		var thiz_dialog = $( '.js-wpv-pagination-form-dialog' );
		thiz_dialog
			.find( '.js-wpv-insert-pagination' )
				.prop( 'disabled', true )
				.removeClass( 'button-primary' )
				.addClass( 'button-secondary' );
		thiz_dialog
			.find( 'input.js-wpv-pagination-dialog-control, input.js-wpv-pagination-dialog-display' )
				.prop( 'checked', false );
		thiz_dialog
			.find( '.js-pagination-preview-element[data-type="page-selector-link"]' )
				.hide();
		thiz_dialog
			.find( '.js-pagination-preview-element[data-type="page-selector-select"]' )
				.show();
		thiz_dialog
			.find( '.js-pagination-control-type' )
				.val( 'drop_down' );
		thiz_dialog
			.find( '.js-pagination-preview-element' )
				.addClass( 'disabled' );
	};
	
	$( document ).on( 'change', 'input.js-wpv-pagination-dialog-control', function() {
		var thiz = $( this ),
		target = thiz.data( 'target' ),
		preview_elements = $( '.js-pagination-preview-element' ),
		target_element = preview_elements.filter('[data-name="' + target + '"]');
		$( '.js-wpv-insert-pagination' )
			.prop( 'disabled', false )
			.addClass( 'button-primary' )
			.removeClass( 'button-secondary' );
		preview_elements.addClass('disabled');
		target_element.removeClass('disabled');
	});
	
	$( document ).on( 'change', '.js-pagination-control-type', function() {
		var page_selectors = $('.js-pagination-preview-element').filter('[data-name="page-selector"]'),
		val = $( this ).val();
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
		var pag_control = $( 'input.js-wpv-pagination-dialog-control:checked' ).serializeArray(),
		pag_selector_mode = $( 'select.js-pagination-control-type' ).val(),
		pag_wrap = $( 'input.js-wpv-pagination-dialog-display' ).prop('checked'),
		pag_preview = self.get_pagination_preview( pag_control, pag_selector_mode ),
		pag_shortcode = self.get_pagination_shortcode( pag_control, pag_selector_mode );
		if ( pag_wrap ) {
			pag_shortcode = '[wpv-pagination]' +  pag_shortcode + '[/wpv-pagination]';
		}
		if ( self.pagination_insert_newline ) {
			pag_shortcode += '\n';
			self.pagination_insert_newline = false;
		}
		if ( window.wpcfActiveEditor == 'wpv_filter_meta_html_content' ) {
			var current_cursor = codemirror_views_query.getCursor( true );
			codemirror_views_query.setSelection( current_cursor, current_cursor );
			codemirror_views_query.replaceSelection( pag_shortcode, 'end' );
			var end_cursor = codemirror_views_query.getCursor( true ),
			pagination_marker = codemirror_views_query.markText( current_cursor, end_cursor, self.codemirror_highlight_options );
			codemirror_views_query.focus();
			setTimeout( function() {
				  pagination_marker.clear();
			}, 2000);
		}
		if ( window.wpcfActiveEditor == 'wpv_layout_meta_html_content' ) {
			var current_cursor = codemirror_views_layout.getCursor( true );
			codemirror_views_layout.setSelection( current_cursor, current_cursor );
			codemirror_views_layout.replaceSelection( pag_shortcode, 'end' );
			var end_cursor = codemirror_views_layout.getCursor( true ),
			pagination_marker = codemirror_views_layout.markText( current_cursor, end_cursor, self.codemirror_highlight_options );
			codemirror_views_layout.focus();
			setTimeout( function() {
				  pagination_marker.clear();
			}, 2000);
		}
		$.colorbox.close();
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
		// Init toolbar pagination button
		self.toolbar_pagination_button_state();
		// Init pagination insert pointer
		// Ugly solution, but otherwise we can not be sure it will be added to the right position
		// due to some sections being shown/hidden on document.ready too, after this one
		setTimeout( function() {
			self.pagination_insert_pointer();
		}, 3000);
	};
	
	self.init();

};

jQuery( document ).ready( function( $ ) {
    WPViews.view_edit_screen_pagination_ux = new WPViews.ViewEditScreenPaginationUX( $ );
});