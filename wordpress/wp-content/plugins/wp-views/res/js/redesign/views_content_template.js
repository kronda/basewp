var CSSEditor = CodeMirror.fromTextArea(document.getElementById("_wpv_view_template_extra_css"), {mode: "css", tabMode: "indent", lineWrapping: true, lineNumbers: true});
var JSEditor = CodeMirror.fromTextArea(document.getElementById("_wpv_view_template_extra_js"), {mode: "javascript", tabMode: "indent", lineWrapping: true, lineNumbers: true});

var WPViews = WPViews || {};

WPViews.CTEditScreen = function( $ ) {
	
	var self = this;
	self.qt_syntax_highlight = null;
	self.highlight_state = $( '.js-wpv-ct-syntax-highlight-on' ).val();
	self.help_box_state = $( '.js-wpv-ct-show-help' ).val();
	self.submit_form = false;
	self.extra_full_icon = '<i class="icon-reorder js-wpv-textarea-full" style="margin-left: 5px; color: green;"></i>';
	
	// ---------------------------------
	// Content Template assignment management
	// ---------------------------------
	
	// @todo review this
	
	$( document ).on( 'click', '.js-wpv-content-template-open',  function( e ) {
		e.preventDefault();
		var dropdownList = $( this ).parent().next( '.js-wpv-content-template-dropdown-list' );
		dropdownList.toggle( 'fast', function() {
			if ( dropdownList.is(':hidden') ) {
				$( this ).prev( 'p' ).find( '[class^="icon-"]' )
					.removeClass( 'icon-caret-up' )
					.addClass( 'icon-caret-down' );
				dropdownList.find( 'input[type=hidden]' ).val( '0' );	
			} else {
				$( this ).prev( 'p' ).find( '[class^="icon-"]' )
					.removeClass( 'icon-caret-down' )
					.addClass( 'icon-caret-up' );
				dropdownList.find( 'input[type=hidden]' ).val( '1' );
			}
		});
		return false;
	});
	
	// @todo review this
	
	$( '.js-wpv-content-template-alert' ).colorbox({
		inline: false,
		onComplete:function(){
			$( '#cboxClose' ).html( '' );
		}
	});
	
	// @todo review this
	
	$( '.js-wpv-check-for-icon' ).change( function() {
		if ( $(this).attr('checked') != 'checked' ) {
			$(this).parent().find('.js-wpv-content-template-alert').hide();
		}else{
			$(this).parent().find('.js-wpv-content-template-alert').show();
		}
	});
	
	// ---------------------------------
	// Syntax highlighting
	// ---------------------------------
		
	self.content_template_syntax_highlight_toggle = function() {
		if ( self.qt_syntax_highlight.hasClass( 'js-wpv-qt-codemirror-on' ) ) {
			self.qt_syntax_highlight
				.removeClass( 'js-wpv-qt-codemirror-on' )
				.addClass( 'js-wpv-qt-codemirror-off' )
				.attr( 'title', wpv_ct_edit_texts.syntax_highlight_enable )
				.val( wpv_ct_edit_texts.syntax_highlight_enable );
			icl_editor.toggleCodeMirror( 'content', false );
			$( '.js-wpv-ct-syntax-highlight-on' ).val( '1' );
			$( '.ed_button, .insert-media.add_media, #content-resize-handle' ).show();
		} else {
			self.qt_syntax_highlight
				.addClass( 'js-wpv-qt-codemirror-on' )
				.removeClass( 'js-wpv-qt-codemirror-off' )
				.attr( 'title', wpv_ct_edit_texts.syntax_highlight_disable )
				.val( wpv_ct_edit_texts.syntax_highlight_disable );
			icl_editor.toggleCodeMirror( 'content', true );
			$( '.js-wpv-ct-syntax-highlight-on' ).val( '0' );
			$( '.insert-media.add_media, #content-resize-handle' ).hide();
			$( '.ed_button' ).not( '#qt_content_wpv_ct_syntax_highlight' ).hide();
		}
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
	
	/*
	// This is only available on WordPress 4.1
	$( document ).on( 'quicktags-init', function() {
		self.qt_syntax_highlight = $( '#qt_content_wpv_ct_syntax_highlight' );
		if ( self.highlight_state == 1 ) {
			self.qt_syntax_highlight
				.removeClass( 'js-wpv-qt-codemirror-off' )
				.addClass( 'js-wpv-qt-codemirror-on' );
		} else {
			self.qt_syntax_highlight
				.removeClass( 'js-wpv-qt-codemirror-on' )
				.addClass( 'js-wpv-qt-codemirror-off' );
		}
		self.content_template_syntax_highlight_toggle();
	});
	// Until then we can only rely on wondow.load
	*/
	
	$( window ).load( function () {
		if ( self.qt_syntax_highlight == null ) {
			self.qt_syntax_highlight = $( '#qt_content_wpv_ct_syntax_highlight' );
			if ( self.highlight_state == 1 ) {
				self.qt_syntax_highlight
					.removeClass( 'js-wpv-qt-codemirror-off' )
					.addClass( 'js-wpv-qt-codemirror-on' );
			} else {
				self.qt_syntax_highlight
					.removeClass( 'js-wpv-qt-codemirror-on' )
					.addClass( 'js-wpv-qt-codemirror-off' );
			}
			self.content_template_syntax_highlight_toggle();
		}
	});
	
	// @todo review this
	
	$( document ).on( 'click','.js-wpv-content-template-update-posts-process', function() {
		var type = $(this).data('type');
		var tid = $(this).data('id');
		var data = {
			action : 'set_view_template',
			view_template_id : tid,
			wpnonce : $('#set_view_template').attr('value'),
			type : type,
			lang : ''
		};
		var updateMessage = $.post( ajaxurl, data, function( response ) {
			$.colorbox({
                 html: response,
                 onClose: function() {

                 }
             });
			$( '#wpv-content-template-alert-link-' + type ).hide();
		});
		updateMessage.always(function(){
			//console.log('ajax request complete');
		});
		updateMessage.fail(function(){
			//console.log('fail');
		});
	});
	
	// ---------------------------------
	// Save submit management
	// ---------------------------------
	
	$( document ).on( 'submit','#post', function( e ) {
		if ( self.submit_form == false ) {
			var data = {
				action : 'wpv_ct_check_name_exists',
				wpnonce : $( '#set_view_template' ).attr( 'value' ),
				title: $( '#title' ).val(),
				id: wpv_ct_edit_texts.template_id
			};
			$.post( ajaxurl, data, function( response ) {
				if ( response == 'wpv_error_ct_title_in_use' ) {
					$('.js-wpv-content-template-toolset-messages').wpvToolsetMessage({
						text: wpv_ct_edit_texts.save_error_title_in_use,
						stay: true,
						close: false,
					});
					$( '#publish' ).removeClass( 'button-primary-disabled disabled' );
					$( '.spinner' ).hide();
					return false;
				} else if ( response == 'wpv_error_ct_title_empty' ) {
					$('.js-wpv-content-template-toolset-messages').wpvToolsetMessage({
						text: wpv_ct_edit_texts.save_error_title_empty,
						stay: true,
						close: false,
					});
					$( '#publish' ).removeClass( 'button-primary-disabled disabled' );
					$( '.spinner' ).hide();
					return false;
				} else {
					self.submit_form = true;
					$( '#post' ).submit();
				}
			});
			return false;
		}
	});
	
	// ---------------------------------
	// Main help box management
	// ---------------------------------
	
	$( document ).on( 'click', '.js-wpv-content-template-edit-help-box .js-toolset-help-close-main', function( e ) {
		e.preventDefault();
		$( '#content-template-show-help' ).prop( 'checked', false );
		$( '.js-wpv-ct-show-help' ).val( '0' );
		$( '.js-wpv-content-template-edit-help-box' ).hide();
	});
	
	$( document ).on( 'click','.js-wpv-show-hide-template-help', function() {
		if ( $( this ).prop( 'checked' ) == false ) {
			$( '.js-wpv-content-template-edit-help-box' ).hide();
			$( '.js-wpv-ct-show-help' ).val( '0' );
		} else {
			$( '.js-wpv-content-template-edit-help-box' ).fadeIn( 'fast' );
			$( '.js-wpv-ct-show-help' ).val( '1' );
		}
    });
	
	$( document ).on( 'click', '.js-wpv-ct-description-button', function( e ) {
		e.preventDefault();
		$( this ).hide();
		$( '.js-wpv-ct-description-button-div' ).fadeIn( 'fast' );
	});
	
	// ---------------------------------
	// CSS and JS textareas
	// ---------------------------------
	
	self.editor_needs_flag = function( instance ) {
		var full = false;
		if ( instance == 'content-template-css-editor' ) {
			full = ( CSSEditor.getValue() != '' );
		} else if ( instance == 'content-template-js-editor' ) {
			full = ( JSEditor.getValue() != '' );
		}
		return full;
	};
	
	self.refresh_codemirror = function( instance ) {
		if ( instance === 'all' ) {
			CSSEditor.refresh();
			JSEditor.refresh();
		} else {
			if ( instance == 'content-template-css-editor' ) {
				CSSEditor.refresh();
			} else if ( instance == 'content-template-js-editor' ) {
				JSEditor.refresh();
			}
		}
	};
	
	$( document ).on( 'click', '.js-wpv-code-editor-toggler', function( e ) {
		e.preventDefault();
		var thiz = $( this ),
		thiz_kind = thiz.data( 'kind' ),
		thiz_text_holder = thiz.find( 'span.js-wpv-text-holder' ),
		thiz_container = thiz.parents( 'li' ),
		thiz_state = $( '#js-wpv-content-template-editor-state-' + thiz_kind ),
		thiz_flag = thiz_container.find( '.js-wpv-textarea-full' ),
		thiz_target = thiz_container.find( '.js-wpv-code-editor' ),
		thiz_target_id = thiz.data( 'target' );
		thiz_flag.hide();
		thiz_target
			.toggleClass('js-wpv-code-editor-closed');
		if ( thiz_target.hasClass( 'js-wpv-code-editor-closed' ) ) {
			if ( thiz_kind == 'css' ) {
				thiz_text_holder.text( wpv_ct_edit_texts.button_css_open );
			} else if ( thiz_kind == 'js' ) {
				thiz_text_holder.text( wpv_ct_edit_texts.button_js_open );
			}
			if ( self.editor_needs_flag( thiz_target_id ) ) {
				thiz_flag.animate( {width: 'toggle'}, 200 );
			}
			thiz_state.val( 'off' );
		} else {
			if ( thiz_kind == 'css' ) {
				thiz_text_holder.text( wpv_ct_edit_texts.button_css_close );
			} else if ( thiz_kind == 'js' ) {
				thiz_text_holder.text( wpv_ct_edit_texts.button_js_close );
			}
			thiz_state.val( 'on' );
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
	// Title management
	// ---------------------------------
	
	$( document ).on( 'keyup input change cut paste', '#title', function( e ) {
		$( '.js-wpv-content-template-toolset-messages .toolset-alert' ).remove();
		$( '#publish' ).removeClass( 'disabled' );
    });
	
	// ---------------------------------
	// Output mode help pointer
	// ---------------------------------
	
	$( document ).on( 'click', '.js-wpv-content-template-mode-tip', function() {
		var thiz = $(this);
		$('.wp-pointer').fadeOut(100);

		thiz.pointer({
			pointerClass: 'wp-toolset-pointer wp-toolset-views-pointer',
			content: '<h3>'+ $(this).attr('title') +'</h3><p>'+ thiz.data('pointer-content-firstp') +'</p><p>'+ thiz.data('pointer-content-secondp') +'</p>',
			position: {
				edge: 'right',
				align: 'left',
				offset: '-5 0'
			},
			buttons: function( event, t ) {
				var button_close = $('<button class="button button-primary-toolset alignright js-wpv-close-this">' + wpv_ct_edit_texts.pointer_close + '</button>');
				button_close.bind( 'click.pointer', function( e ) {
					e.preventDefault();
					t.element.pointer('close');
				});
				return button_close;
			}
		}).pointer( 'open' );
	});
	
	// ---------------------------------
	// Init
	// ---------------------------------
	
	self.init = function() {
		QTags.addButton( 'wpv_ct_syntax_highlight', wpv_ct_edit_texts.syntax_highlight_disable, self.content_template_syntax_highlight_toggle );
		
		$( '.wp-editor-tools' ).css( { "z-index": "7" } );
		// @todo move this to a PHP action...
		$( '<div class="wpv-content-template-help-message"></div>' ).insertAfter( '#titlediv' );
		// remove the "Save Draft" and "Preview" buttons.
		$( '#minor-publishing-actions, #misc-publishing-actions' ).hide();
		$('#publishing-action input[name=publish]').val( wpv_ct_edit_texts.action_save );
		$( '#views_template_html_extra' ).removeClass("closed");
		// Assignment initialization
		var dropdownLists = $( '.js-wpv-content-template-dropdown-list:visible' );
		$.each( dropdownLists, function() {
			$( this ).prev( 'p' ).find( '[class^="icon-"]' )
				.removeClass( 'icon-caret-down' )
				.addClass( 'icon-caret-up' );
		});
		// Compatibility
		// Note that this should be fixed in the latest version of Post Type Switcher
		// Instead of making it fail by changing the nonce, it is adding the current post type to the dropdown as the selected value
		// https://wordpress.org/support/topic/dont-show-for-non-public-post-types?replies=4#post-5849287
		if ( $( '#pts-nonce-select' ).length > 0 ) {
			$( '#pts-nonce-select' ).val( 'make-it-fail' );
		}
		
		// @pass $show_option by translation
		// Also, the checkbox label...
		var ct_help_screen_options_checked = '';
		if ( self.help_box_state == 1 ) {
			ct_help_screen_options_checked = ' checked="checked"';
		}
		var ct_help_screen_options_checkbox = '<label for="content-template-show-help">';
		ct_help_screen_options_checkbox += '<input class="js-wpv-show-hide-template-help" ' + ct_help_screen_options_checked + ' name="content-template-show-help" type="checkbox" id="content-template-show-help" value="1"  />';
		ct_help_screen_options_checkbox += 'Content Template help';
		ct_help_screen_options_checkbox += '</label>';
		$('.metabox-prefs')
			.eq(1)
			.append( ct_help_screen_options_checkbox );
		
		
		
	};
	
	self.init();

};

jQuery( document ).ready( function( $ ) {
    WPViews.content_template_edit_screen = new WPViews.CTEditScreen( $ );
});