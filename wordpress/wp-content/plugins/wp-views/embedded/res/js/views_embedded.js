/**
* Views Embedded read-only screens - script
*
* @package Views
*
* @since unknown
*/

var WPViews = WPViews || {};

WPViews.ViewEmbeddedScreen = function( $ ) {
	
	var self = this;
	self.view_id = $( '.js-post_ID' ).val();
	self.view_purpose = $( '.js-wpv-view-purpose' ).val();
	
	self.action_bar = $( '#js-wpv-general-actions-bar' );
	self.html = $( 'html' );
	
	self.filter_html_embedded = null;
	self.layout_html_embedded = null;
	self.combined_output_embedded = null;
	
	if ( self.action_bar && self.action_bar.offset() ) {
		var toolbarPos = self.action_bar.offset().top,
		adminBarHeight = 0,
		adminBarWidth = $( '.wpv-title-section .wpv-setting-container' ).width();
		if ( $('#wpadminbar').length !== 0 ) {
			adminBarHeight = $('#wpadminbar').height();
			self.action_bar.width( adminBarWidth + 30 );
		}
		self.set_toolbar_pos = function() {
			if ( toolbarPos <= $(window).scrollTop() + adminBarHeight + 20 ) {
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
			self.action_bar.width( adminBarWidth + 30 );
		});

		self.set_toolbar_pos();
	}
	
	self.initialize_embedded_editors = function() {
		if ( $( document.getElementById( 'wpv_filter_meta_html_content' ) ).length ) {
			self.filter_html_embedded = CodeMirror.fromTextArea( document.getElementById( "wpv_filter_meta_html_content" ), {
				mode: "myshortcodes",
				lineNumbers: true,
				lineWrapping: true,
				readOnly: "nocursor"
			});
		}
		if ( $( document.getElementById( 'wpv_layout_meta_html_content' ) ).length ) {
			self.layout_html_embedded = CodeMirror.fromTextArea( document.getElementById( "wpv_layout_meta_html_content" ), {
				mode: "myshortcodes",
				lineNumbers: true,
				lineWrapping: true,
				readOnly: "nocursor"
			});
		}
		if ( $( document.getElementById( 'wpv_content' ) ).length ) {
			self.combined_output_embedded = CodeMirror.fromTextArea( document.getElementById( "wpv_content" ), {
				mode: "myshortcodes",
				lineNumbers: true,
				lineWrapping: true,
				readOnly: "nocursor"
			});
		}
	};
	
	self.target_blank_links = function() {
		if ( $( '.js-wpv-display-in-iframe' ).length == 1 ) {
			if ( $( '.js-wpv-display-in-iframe' ).val() == 'yes' ) {
				$( '.toolset-help a, .wpv-setting a' ).attr( "target", "_blank" );
			}
		}
	};
	
	// Toolset pointers

	$('.js-display-tooltip').click(function(){
		var thiz = $( this );
		// hide this pointer if other pointer is opened.
		$( '.wp-pointer' ).fadeOut( 100 );
		$( this ).pointer({
			pointerClass: 'wp-toolset-pointer wp-toolset-views-pointer',
			pointerWidth: 400,
			content: '<h3>' + thiz.data( 'header' ) + '</h3><p>' + thiz.data( 'content' ) + '</p>',
			position: {
				edge: 'left',
				align: 'center',
				offset: '15 0'
			},
			buttons: function( event, t ) {
				var button_close = $( '<button class="button button-primary-toolset alignright js-wpv-close-this">Close</button>' );
				button_close.bind( 'click.pointer', function( e ) {
					e.preventDefault();
					t.element.pointer( 'close' );
				});
				return button_close;
			}
		}).pointer( 'open' );
	});
	
	self.init = function() {
		// Adjust admin menu link
		$( '.wp-has-current-submenu li.current a' ).attr( 'href', $( '.wp-has-current-submenu li.current a' ).attr( 'href' ) + '&view_id=' + self.view_id );
		// Adjust purpose display
		$( '.toolset-help.js-for-view-purpose-' + self.view_purpose ).show();
		// Initialize embedded editors
		self.initialize_embedded_editors();
		// Set links to open in a new window when used inside a Layouts iframe
		self.target_blank_links();
	};
	
	self.init();

};

jQuery( document ).ready( function( $ ) {
    WPViews.view_embedded_screen = new WPViews.ViewEmbeddedScreen( $ );
});