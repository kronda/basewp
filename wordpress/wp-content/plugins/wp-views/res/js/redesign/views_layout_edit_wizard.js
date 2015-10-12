/**
* Views Layout Wizard - script
*
* Controls the Layout Wizard interaction and output
*
* @package Views
*
* @since unknown
*/

var WPViews = WPViews || {};

WPViews.LayoutWizard = function( $ ) {
	
	var self = this;
	
	self.view_id = $('.js-post_ID').val();
	
	self.initial_settings = null;
	self.settings_from_wizard = null;
	self.add_field_ui = null;
	self.use_loop_template = false;
	self.use_loop_template_id = '';
	self.use_loop_template_title = '';
	
	// Types compatibility
	// @todo merge this into just one variable
	// @todo store too the style options and loop template state
	
	self.wizard_dialog = null;
	self.wizard_dialog_item = null;
	self.wizard_dialog_item_parent = null;
	self.wizard_dialog_style = null;
	self.wizard_dialog_fields = null;
	
	self.wizard_dialog_data = null;
	
	self.saved_fields_html = null;
		
	self.edit_screen = ( typeof WPViews.view_edit_screen != 'undefined' ) ? WPViews.view_edit_screen : WPViews.wpa_edit_screen;
		
	self.doing_shortcode_gui = false;
	self.doing_shortcode_gui_for = null;
	self.doing_shortcode_gui_for_selected = null;
	
	// ---------------------------------
	// Functions
	// ---------------------------------
	
	// Fetch initial settings - on document ready, from variables added directly to the page, see wpv_loop_wizard_add_data_to_js
	
	self.fetch_initial_settings = function() {
		self.initial_settings = {};
		self.initial_settings.dialog = WPViews.layout_wizard_saved_dialog;
		self.initial_settings.settings = WPViews.layout_wizard_saved_settings;
	};
	
	// Render the dialog, and apply the existing settings
	
	self.render_dialog = function( response ) {
		var layout_value = codemirror_views_layout.getValue(),
		loop_start_tag = layout_value.indexOf( '<wpv-loop' ),// This tag can contain wrap and pad attributes
		loop_start = 0,
		loop_end = layout_value.indexOf( '</wpv-loop>' ),
		layout_loop_content = '';
		if ( loop_start_tag >= 0 ) {
			loop_start = layout_value.indexOf( '>', loop_start_tag );
		}
		// Merge response with local settings
		self.wizard_dialog_data = self.merge_with_saved_settings( response.settings );
		$.colorbox({
			html: response.dialog,
			onOpen: function() {
				WPViews.shortcodes_gui.shortcode_gui_insert = false;
			},
			onLoad: function() {
				
			},
			onClosed: function() {
				WPViews.shortcodes_gui.shortcode_gui_insert = true;
			},
			onComplete: function() {
				$( '.js-insert-layout' )
					.addClass( 'button-secondary' )
					.removeClass( 'button-primary' );
				// Set the first tab as active
				$( '.js-layout-wizard-tab' ).not( ':first-child' ).hide();
				// Show the overwrite notice when needed
				if ( ( loop_start >= 0 ) && ( loop_end >= 0 ) ) {
					layout_loop_content = layout_value.slice( loop_start, loop_end ).replace(/\s+/g, '');
					if ( layout_loop_content != '>' ) {
						$( '.js-wpv-layout-wizard-overwrite-notice' ).show();
					}
				}
				// Set incoming values, if any
				if ( typeof( self.wizard_dialog_data.style ) != 'undefined' ) {
					$( 'input.js-wpv-layout-wizard-style[value="' + self.wizard_dialog_data.style + '"]' ).click();
				}
				if ( typeof( self.wizard_dialog_data.table_cols ) != 'undefined' ) {
					$( 'select.js-wpv-layout-wizard-table-cols[name="table_cols"]' ).val( self.wizard_dialog_data.table_cols );
				}

                // Determine used bootstrap version and:
                // - if it's not set, disable the bootstrap option, show a message with link to Views settings
                // - if it's set to "no bootstrap", disable the option and show a different message
                // - for bootstrap 2 or 3, adjust other related fields and show message with version number
                var bootstrapVersion = 1; // meaning "not set"
				if ( typeof( self.wizard_dialog_data.wpv_bootstrap_version ) != 'undefined' ) {
                    bootstrapVersion = parseInt( self.wizard_dialog_data.wpv_bootstrap_version );
                }

                // Decide what needs to be done about bootstrap version
                var isBootstrapOptionDisabled = false;
                var bootstrapOptionMessage = null;

                switch( bootstrapVersion ) {
                    case 2:
                        if ( typeof( self.wizard_dialog_data.bootstrap_grid_row_class ) != 'undefined' && self.wizard_dialog_data.bootstrap_grid_row_class === 'true' ) {
                            $( 'input[name="bootstrap_grid_row_class"]' ).prop( 'checked', true );
                        }
                        $( 'input[name="bootstrap_grid_row_class"]' ).prop( 'disabled', false );
                        bootstrapOptionMessage = 'bootstrap_2';
                        break;
                    case 3:
                        $( 'input[name="bootstrap_grid_row_class"]' ).prop( 'disabled', true );
                        bootstrapOptionMessage = 'bootstrap_3';
                        break;
                    case -1:
                        isBootstrapOptionDisabled = true;
                        bootstrapOptionMessage = 'bootstrap_not_used';
                        break;
                    default:
                    case 1:
                        isBootstrapOptionDisabled = true;
                        bootstrapOptionMessage = 'bootstrap_not_set';
                        break;
                }

                // Disable or enable the option
                $( '#layout-wizard-style-bootstrap-grid' ).attr( 'disabled', isBootstrapOptionDisabled );
                $( 'label[for=layout-wizard-style-bootstrap-grid]' ).css({ opacity: (isBootstrapOptionDisabled ? 0.5 : 1) });

                // Display the selected translated message
                var bootstrapOptionMessagePlaceholder = $('.js-wpv-bootstrap-message');
                if(bootstrapOptionMessage != null) {
                    bootstrapOptionMessagePlaceholder.show();
                    bootstrapOptionMessagePlaceholder.html(wpv_layout_wizard_strings[ bootstrapOptionMessage ]);
                } else {
                    bootstrapOptionMessagePlaceholder.hide();
                }

				if ( typeof( self.wizard_dialog_data.bootstrap_grid_cols ) != 'undefined' ) {
					$( 'select.js-wpv-layout-wizard-bootstrap-grid-cols[name="bootstrap_grid_cols"]' ).val( self.wizard_dialog_data.bootstrap_grid_cols );
				}
				if ( typeof( self.wizard_dialog_data.bootstrap_grid_container ) != 'undefined' && self.wizard_dialog_data.bootstrap_grid_container === 'true' ) {
					$( 'input[name="bootstrap_grid_container"]' ).prop( 'checked', true );
				}
				$( '#bootstrap_grid_individual_yes' ).prop( 'checked', true );
				if ( typeof( self.wizard_dialog_data.bootstrap_grid_individual ) != 'undefined' && self.wizard_dialog_data.bootstrap_grid_individual != '' ) {
					$( '#bootstrap_grid_individual_yes' ).prop( 'checked', false );
					$( '#bootstrap_grid_individual_no' ).prop( 'checked', true );	
				}
				if ( typeof( self.wizard_dialog_data.include_field_names ) != 'undefined' ) {
					$('input[name="include_field_names"]').attr('checked', self.wizard_dialog_data.include_field_names === 'true');
				}
				
				if ( self.use_loop_template ) {
					$( 'input#js-wpv-use-view-loop-ct' ).prop( 'checked', true );
				}
			}
		});
	};
	
	// Merge settings with existing ones
	
	self.merge_with_saved_settings = function( data ) {
		if ( self.settings_from_wizard ) {
			data.style = self.settings_from_wizard.style;
			data.table_cols = self.settings_from_wizard.table_cols;
			data.include_field_names = self.settings_from_wizard.include_field_names;
			data.fields = self.settings_from_wizard.fields;
			data.real_fields = self.settings_from_wizard.real_fields;
			data.bootstrap_grid_cols = self.settings_from_wizard.bootstrap_grid_cols;
			data.bootstrap_grid_container = self.settings_from_wizard.bootstrap_grid_container;
			data.bootstrap_grid_row_class = self.settings_from_wizard.bootstrap_grid_row_class;
			data.bootstrap_grid_individual = self.settings_from_wizard.bootstrap_grid_individual;
		}
		return data;
	};
	
	// Change tab
	
	self.change_tab = function( backward ) {
		var count = $( '.wpv-dialog-nav-tab' ).index( $( 'li' ).has( '.active' ) );
		$( '.wpv-dialog-nav-tab a' ).removeClass('active');
		$( '.wpv-dialog-content-tab' ).hide();
		if ( backward ) {
			count--;
		} else {
			count++;
		}
		$( '.wpv-dialog-nav-tab a' )
			.eq( count )
				.addClass( 'active' );
		$( '.wpv-dialog-content-tab' )
			.eq( count )
				.fadeIn( 'fast' );
		self.manage_dialog_buttons( count );
	};
	
	// Navigate to a tab
	
	self.go_to_tab = function( tab_index ) {
		$( '.wpv-dialog-nav-tab a' ).removeClass( 'active' );
		$( '.wpv-dialog-content-tab' ).hide();
		$( '.wpv-dialog-nav-tab a' ).eq( tab_index ).addClass( 'active' );
		$( '.wpv-dialog-content-tab' ).eq( tab_index ).fadeIn( 'fast' );
		self.manage_dialog_buttons( tab_index );
	}
	
	// Update buttons
	
	self.manage_dialog_buttons = function( page_id ) {
		var next_enable = false,
		insert_button = $('.js-insert-layout'),
		prev_button = $('.js-dialog-prev');
		switch ( page_id ) {
			case 0:
				next_enable = ( $( '[name="layout-wizard-style"]:checked' ).length > 0 );
				insert_button.text( wpv_layout_wizard_strings.button_next );
				prev_button.hide();
				break;
			case 1:
				next_enable = ( $( '.js-wpv-layout-wizard-layout-fields > .js-wpv-loop-wizard-item-container' ).length > 0 );
				insert_button.text( wpv_layout_wizard_strings.button_insert );
				prev_button.show();
				break;
		}
		if ( next_enable ) {
			insert_button
				.removeClass( 'button-secondary' )
				.addClass( 'button-primary' )
				.prop( 'disabled', false );
		} else {
			insert_button
				.removeClass( 'button-primary' )
				.addClass( 'button-secondary' )
				.prop( 'disabled', true );
		}
	};
	
	// Add field UI
	
	self.add_field_ui_callback = function( field_html ) {
		var count = $('.js-wpv-loop-wizard-item-container').size(),
		field_html = field_html.replace( '__wpv_layout_count_placeholder__', count + 1 );
		//$( '.js-wpv-layout-wizard-layout-fields' ).sortable( "destroy" );
		$( '.js-wpv-layout-wizard-layout-fields' ).append( field_html );
		$( '.js-wpv-layout-wizard-layout-fields' ).sortable({
			handle: ".js-layout-wizard-move-field",
			axis: 'y',
			containment: ".js-wpv-layout-wizard-layout-fields",
			helper: 'clone',
			tolerance: "pointer"
		});
		self.manage_dialog_buttons( 1 );
		$.each( $('.js-wpv-dialog-layout-wizard select.js-wpv-select2' ),function() {
			if ( ! $( this ).hasClass( 'select2-offscreen' ) ) {
				$( this ).select2().trigger( 'change' );
			}
		});
	};

	
	/**
	 * Replace the content of wpv-loop in loop output with new one.
	 *
	 * Tries to replace string within "<!-- wpv-loop-start -->" and "<!-- wpv-loop-end -->" (included) by new one. If
	 * those tags aren't found, it appends the new string at the end.
	 *
	 * @param {string} content The loop output.
	 * @param {string} data New string which should replace wpv-loop.
	 *
	 * @return string The result.
	 *
	 * @since unknown
	 */ 
    self.replace_layout_loop_content = function( content, data ) {
        if ( content.search(/<!-- wpv-loop-start -->[\s\S]*\<!-- wpv-loop-end -->/g) == -1 ) {
            content += data;
        } else {
            content = content.replace(/<!-- wpv-loop-start -->[\s\S]*<!-- wpv-loop-end -->/g, "<!-- wpv-loop-start -->\n" + data + "<!-- wpv-loop-end -->");
        }
        return content;
    };
    
	
	//Check if fields is just one content template
	
	self.check_for_only_content_template_field = function( fields ) {
		var out = false,
		fields_count = fields.length;
		if ( fields_count == 1 ) {
			for ( var i = 0; i < fields_count; i++ ) {
				if ( fields[i][4] == 'post-body' ) {
					out = true;
				}
			}
		}
	   return out;
	};

	
	/**
	 * Process dialog data.
	 *
	 * - Reads user input from the dialog.
	 * - Makes the wpv_generate_view_loop_output AJAX call and obtains loop output settings as well as rendered.
	 *   loop output and content template (if used).
	 * - Updates the content of editor(s).
	 * - Highlights the modified text in Loop Output editor.
	 * - Shows an appropriate pointer.
	 * - Calls the callback, if it is a function.
	 *
	 * @todo comment properly
	 *
	 * @since unknown
	 */ 
	self.process_layout_wizard_data = function( fields, callback ) {
		
		// Parse dialog input
		var layout_style = $( '[name=layout-wizard-style]:checked' ).val();

		var layout_args = {
			include_field_names: ($('[name="include_field_names"]').attr('checked')) ? true : false,
			tab_column_count: $('[name="table_cols"]').val(),
			bootstrap_column_count: $('[name="bootstrap_grid_cols"]').val(),
			// @todo Request for comment: Where does this 'data' come from?
			bootstrap_version: self.wizard_dialog_data.wpv_bootstrap_version,
			add_container: $('[name="bootstrap_grid_container"]').prop('checked'),
			add_row_class: $('[name="bootstrap_grid_row_class"]').prop('checked'),
			render_individual_columns: $('[name="bootstrap_grid_individual"]:checked').val(),
			use_loop_template: self.use_loop_template,
			loop_template_title: self.use_loop_template_title,
			render_only_wpv_loop: true
		}		
		// Content of the Loop Output.
		// Content of a Content Template to be created (if self.use_loop_template is true).
		var loop_output = '',
		ct_content = '',
		current_dialog = $( '.js-wpv-dialog-layout-wizard' ),
		messages_container = current_dialog.find( '.js-wpv-message-container' ),
		halt_execution = false;
		
		$.ajax({
			async: false,
			type: "POST",
			url: ajaxurl,
			dataType: 'json',
			data: {
				action: 'wpv_generate_view_loop_output',
				wpnonce: $('#layout_wizard_nonce').attr('value'),
				view_id: self.view_id,
				style: layout_style,
				fields: JSON.stringify( fields ),
				args: JSON.stringify( layout_args )
			},
			success: function( response ) {
				if ( response.success ) {
					self.settings_from_wizard = response.data.loop_output_settings;
					loop_output = response.data.loop_output_settings.layout_meta_html;
					ct_content = response.data.ct_content;
				} else {
					self.edit_screen.manage_ajax_fail( response.data, messages_container );
					current_dialog.find('.wpv-spinner.ajax-loader' ).remove();
					halt_execution = true;
				}
			},
			error: function( ajaxContext ) {
				console.log( "Error: ", ajaxContext.responseText );
			}
		});
		
		if ( halt_execution ) {
			return;
		}

		// Current Loop Output content
		var c = codemirror_views_layout.getValue();
		
		codemirror_highlight_options = {
			className: 'wpv-codemirror-highlight'
		};
		
		if ( self.use_loop_template ) {
			// User chose to use Loop Template. 
			
			var show_layout_template_loop_pointer_content = $( '.js-wpv-inserted-layout-loop-content-template-pointer' );
			
			// Make sure that the Loop Template is opened
			if ( $('.js-wpv-ct-listing-' + self.use_loop_template_id + ' .js-wpv-open-close-arrow').hasClass('icon-caret-down') ) {
				$('.js-wpv-ct-listing-' + self.use_loop_template_id + ' .js-wpv-content-template-open').click();
			}
			
			// Update the Loop Output content
			c_new = self.replace_layout_loop_content( c, loop_output );
			if ( c == c_new ) {
				// We are updating the loop output, but the layout content did not change
				// So the layout will not need to be updated, which would trigger the loop wizard data saving
				// So we need to save the loop wizard data manually
				self.force_save_loop_wizard_data();
			} else {
				codemirror_views_layout.setValue( c_new );
			}
			
			// Update the Loop Template content
			var ct_inline_editor_index = "wpv-ct-inline-editor-" + self.use_loop_template_id;
			window.iclCodemirror[ ct_inline_editor_index ].setValue( ct_content );
			
			// Highlight the Loop Template content
			var loop_template_ends = {
				'line': window.iclCodemirror[ ct_inline_editor_index ].lineCount(),
				'ch': 0 };
			var content_template_marker = window.iclCodemirror[ ct_inline_editor_index ].markText( 
					{ 'line': 0, 'ch': 0 }, 
					loop_template_ends, 
					codemirror_highlight_options );
			setTimeout( function() { content_template_marker.clear(); }, 2000);
			
			// Highlight replace existing loop and add pointer
			var layout_loop_starts = codemirror_views_layout.getSearchCursor( '<!-- wpv-loop-start -->', false );
			var layout_loop_ends = codemirror_views_layout.getSearchCursor( '<!-- wpv-loop-end -->', false );
			
			if ( layout_loop_starts.findNext() && layout_loop_ends.findNext() ) {
				// We found the wpv-loop tag, now highlight it.
				var layout_loop_marker = codemirror_views_layout.markText( 
						layout_loop_starts.from(), 
						layout_loop_ends.to(), 
						codemirror_highlight_options );
						
				if ( show_layout_template_loop_pointer_content.hasClass( 'js-wpv-pointer-dismissed' ) ) {
					// The pointer is dismissed, we will not be showing it.
					
					// Clear the marker in two seconds.
					setTimeout( function() { layout_loop_marker.clear(); }, 2000 );
					
				} else {
					
					// Show the pointer
					var layout_template_loop_pointer = $('.layout-html-editor .wpv-codemirror-highlight').first().pointer({
						pointerClass: 'wp-toolset-pointer wp-toolset-views-pointer',
						pointerWidth: 400,
						content: show_layout_template_loop_pointer_content.html(),
						position: {
							edge: 'bottom',
							align: 'left'
						},
						show: function( event, t ) {
							t.pointer.show();
							t.opened();
							
							// Create a button to scroll down to the Loop Template
							var button_scroll = $('<button class="button button-primary-toolset alignright js-wpv-scroll-this">Scroll to the Content Template</button>');
							button_scroll.bind( 'click.pointer', function(e) {
								// We need to scroll there down
								e.preventDefault();
								layout_loop_marker.clear();
								if ( t.pointer.find( '.js-wpv-dismiss-pointer:checked' ).length > 0 ) {
									var pointer_name = t.pointer.find( '.js-wpv-dismiss-pointer:checked' ).data( 'pointer' );
									$( document ).trigger( 'js_event_wpv_dismiss_pointer', [ pointer_name ] );
								}
								t.element.pointer('close');
								if ( self.use_loop_template_id != '' ) {
									$('html, body').animate({
										scrollTop: $( '#wpv-ct-listing-' + self.use_loop_template_id ).offset().top - 100
									}, 1000);
								}
							});
							button_scroll.insertAfter(  t.pointer.find('.wp-pointer-buttons .js-wpv-close-this') );
						},
						buttons: function( event, t ) {
							
							// Add Close button
							var button_close = $('<button class="button button-secondary alignleft js-wpv-close-this">Close</button>');
							button_close.bind( 'click.pointer', function( e ) {
								e.preventDefault();
								layout_loop_marker.clear();
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
					layout_template_loop_pointer.pointer('open');
				}
			}
			
		} else {
			// User chose not to use Loop Template. We will only update Loop Output and ct_content (which should be
			// empty) will be ignored.
			
			// Update Loop Output
			c = self.replace_layout_loop_content( c, loop_output );
			codemirror_views_layout.setValue( c );
			
			// Highlight and add pointer to the loop
			var show_layout_loop_pointer_content = $( '.js-wpv-inserted-layout-loop-pointer' ),
			layout_loop_starts = codemirror_views_layout.getSearchCursor( '<!-- wpv-loop-start -->', false );
			layout_loop_ends = codemirror_views_layout.getSearchCursor( '<!-- wpv-loop-end -->', false );
			if ( layout_loop_starts.findNext() && layout_loop_ends.findNext() ) {
				var layout_loop_marker = codemirror_views_layout.markText( layout_loop_starts.from(), layout_loop_ends.to(), codemirror_highlight_options );
				if ( show_layout_loop_pointer_content.hasClass( 'js-wpv-pointer-dismissed' ) ) {
					setTimeout( function() {
						  layout_loop_marker.clear();
					}, 2000);
				} else {
					// Show the pointer
					var layout_loop_pointer = $('.layout-html-editor .wpv-codemirror-highlight').first().pointer({
						pointerClass: 'wp-toolset-pointer wp-toolset-views-pointer',
						pointerWidth: 400,
						content: show_layout_loop_pointer_content.html(),
						position: {
							edge: 'bottom',
							align: 'left'
						},
						buttons: function( event, t ) {
							var button_close = $('<button class="button button-primary-toolset alignright js-wpv-close-this">Close</button>');
							button_close.bind( 'click.pointer', function( e ) {
								e.preventDefault();
								layout_loop_marker.clear();
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
					layout_loop_pointer.pointer('open');
				}
			}
		}
		
		if ( callback && typeof callback === 'function' ) {
			callback();
		}
    };
	
	/**
	* force_save_loop_wizard_data
	*
	* Force saving the loop wizard data
	* When updating the fields, if the layout output is not updated, we need to trigger this saving manually
	* It happens when only changing the included fields but keep using a loop template
	*
	* @since 1.9
	*/
	
	self.force_save_loop_wizard_data = function() {
		var data = {
			action: 'wpv_update_loop_wizard_data',
			id: self.view_id,
			wpnonce: $( '.js-wpv-layout-extra-update' ).data( 'nonce' )
		};
		if ( self.settings_from_wizard ) {
			data.include_wizard_data = 'true';
			for (var attr_name in self.settings_from_wizard) {
				data[attr_name] = self.settings_from_wizard[attr_name];
			}
		}
		$.ajax({
			//async: false,
			type: "POST",
			dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				
			},
			error: function (ajaxContext) {
				
			},
			complete: function() {
				
			}
		});
	};

	// ---------------------------------
	// Events
	// ---------------------------------
	
	// Open layout wizard dialog
	
	$( document ).on( 'click', '.js-open-meta-html-wizard', function() {
		if ( self.initial_settings ) {
			// We have a previous setting that we can use.
			self.render_dialog( self.initial_settings );
		} else {
			// fetch and load the popup via ajax.
			var data = {
			action: 'wpv_layout_wizard',
			view_id: self.view_id
			};
			$.ajax({
				type: "POST",
				dataType: "json",
				url: ajaxurl,
				data: data,
				success: function( response ) {
					if ( response.success ) {
						self.initial_settings = response.data;
						self.render_dialog( self.initial_settings );
					}
				}
			});
		}
	});
	
	// Navigate clicking on the tabs
	
	$( document ).on( 'click', '.wpv-dialog-nav-tab a', function( e ) {
		e.preventDefault();
		var thiz = $( this ),
		thiz_tab = thiz.parents( '.wpv-dialog-nav-tab' ),
		thiz_index = $( '.wpv-dialog-nav-tab' ).index( thiz_tab );
		if ( 
			! thiz.hasClass( 'js-tab-not-visited' ) 
			&& ! thiz.hasClass( 'active') 
		) {
			self.go_to_tab( thiz_index );
		}
	});
	
	// Go back
	
	$( document ).on( 'click', '.js-dialog-prev', function() {
		self.change_tab( true );
	});
	
	// Clear the dialog ui once the query type options have changed
	
	$( document ).on( 'js_event_wpv_query_type_options_saved', '.js-wpv-query-type-update', function() {
		self.add_field_ui = null;
	});
	
	// Remove a field
	
	$( document ).on( 'click', '.js-layout-wizard-remove-field', function( e ) {
		var row_to_delete = $( this ).closest( '.js-wpv-loop-wizard-item-container' );
		row_to_delete.addClass( 'wpv-layout-wizard-field-deleted' );
		setTimeout( function () {
			row_to_delete.remove();
			self.manage_dialog_buttons( 1 );
		}, 500 );
	});
	
	// Change the Loop output style - display extra options for some styles
	
	$( document ).on( 'change', '.js-wpv-layout-wizard-style', function() {
		var style_selected = $( '.js-wpv-layout-wizard-style:checked' ).val(),
		style_container = $( '.js-wpv-layout-wizard-layout-style' ),
		style_settings_container = $( '.js-wpv-layout-wizard-layout-style-options' ),
		dialog_pointer = $( '<div class="wpv-dialog-arrow-left js-wpv-dialog-arrow-left"></div>' );
		
		if ( ! self.use_loop_template ) {
			if ( 
				style_selected === 'bootstrap-grid' 
				|| style_selected === 'table_of_fields'
				|| style_selected === 'table' 
			) {
				$( '#js-wpv-use-view-loop-ct' ).prop( 'checked', true );
			} else {
				$( '#js-wpv-use-view-loop-ct' ).prop( 'checked', false );
			}
		}
		
		
		$( '.js-insert-layout' )
			.prop( 'disabled', false )
			.removeClass( 'button-secondary' )
			.addClass( 'button-primary' );
		$( '.js-layout-wizard-num-columns, .js-layout-wizard-include-fields-names, .js-layout-wizard-bootstrap-grid-box' ).hide();
		$( '.js-wpv-dialog-arrow-left' ).remove();
		style_container.removeClass( 'wpv-layout-wizard-layout-style-has-settings' );
		if ( style_settings_container.find( '.js-wpv-layout-wizard-layout-style-options-' + style_selected ).length > 0 ) {
			style_settings_container.find( '.js-wpv-layout-wizard-layout-style-options-' + style_selected ).show();
			style_container.addClass( 'wpv-layout-wizard-layout-style-has-settings' );
			$( 'input[value=' + style_selected + ']' )
				.parents( 'li' )
					.append( dialog_pointer );
		} else if ( style_container.find( '.js-wpv-layout-wizard-layout-style-options-' + style_selected ).length > 0 ) {
			style_container.find( '.js-wpv-layout-wizard-layout-style-options-' + style_selected ).show();
		}
	});
	
	// Add a field
	
	$( document ).on( 'click', '.js-layout-wizard-add-field', function( e ) {
		if ( self.add_field_ui ) {
			self.add_field_ui_callback( self.add_field_ui );
		} else {
			var data = {
				action: 'wpv_loop_wizard_add_field',
				id: '__wpv_layout_count_placeholder__',
				wpnonce : wpv_layout_wizard_strings.wpnonce,
				view_id: self.view_id
			};
			$.ajax({
				type: "POST",
				dataType: "json",
				url: ajaxurl,
				data: data,
				async: false,
				success: function( response ) {
					if ( response.success ) {
						self.add_field_ui = response.data.html;
						self.add_field_ui_callback( self.add_field_ui );
					}
				},
				complete: function() {
					
				}
			});
		}
	});
	
	// Shows or hides the Content Template dropdown when selecting a field
	
	$( document ).on( 'change', 'select.js-wpv-layout-wizard-item', function() {
		var thiz = $( this ),
		thiz_container = thiz.closest( '.js-wpv-loop-wizard-item-container' );
		option = thiz.find( ':selected' );
		if ( option.data( 'shortcodehandle' ) === 'wpv-post-body' ) {
			thiz_container.find('.js-layout-wizard-body-template-text').show();
			thiz_container.find('.js-custom-types-fields').hide();
			thiz_container.find('.js-wpv-loop-wizard-shortcode-ui').hide();
		} else if ( option.data( 'istype' ) == 1 ) {
			thiz_container.find('.js-layout-wizard-body-template-text').hide();
			thiz_container.find('.js-custom-types-fields')
				.attr( 'rel', option.data( 'typename' ) )
				.show();
			thiz_container.find('.js-wpv-loop-wizard-shortcode-ui').hide();
		} else if ( option.data( 'hasattributesgui' ) == 1 ) {
			thiz_container.find('.js-layout-wizard-body-template-text').hide();
			thiz_container.find('.js-custom-types-fields').hide();
			thiz_container.find('.js-wpv-loop-wizard-shortcode-ui')
				.attr( 'rel', option.data( 'shortcodehandle' ) )
				.attr( 'onclick', option.data( 'onclick' ) )
				.show();
		} else {
			thiz_container.find('.js-layout-wizard-body-template-text, .js-custom-types-fields, .js-wpv-loop-wizard-shortcode-ui').hide();
		}
	});
	
	// Set the Content Template when using a Body field
	
	$( document ).on( 'change', 'select.js-wpv-layout-wizard-body-template', function() {
		var thiz = $( this ),
		thiz_container = thiz.closest( '.js-wpv-loop-wizard-item-container' );
        thiz_container.find('select.js-wpv-layout-wizard-item [data-shortcodehandle="wpv-post-body"]').val( thiz.val() );
    });
	
	// Insert layout
	
	$( document ).on( 'click', '.js-insert-layout', function( e ) {
		var thiz = $( this ),
		index = $('.wpv-dialog-nav-tab').index( $('li').has('.active') );
		if ( index === 0 ) {
			// Load existing fields
			// @todo this needs a hard review
			if ( 
				! $( '.js-wpv-layout-wizard-layout-fields' ).hasClass( 'js-wpv-layout-wizard-layout-fields-loaded' )
				&& typeof( self.wizard_dialog_data.fields ) != 'undefined' 
			) {
				if ( self.saved_fields_html != null ) {
					$( self.saved_fields_html ).appendTo( '.js-wpv-layout-wizard-layout-fields' );
				} else if ( self.wizard_dialog_data.fields.length > 0 ) {
					var ii = 0,
					vcount = 0,
					flist = [],
					spinnerContainer = $( '<div class="wpv-spinner ajax-loader">' ).prependTo( '.js-wpv-layout-wizard-dialog-footer' ).show();
					$( '.js-wpv-layout-wizard-layout-fields-feedback' ).show();
					thiz
						.prop( 'disabled', true )
						.removeClass( 'button-primary' )
						.addClass( 'button-secondary' );
					
					for( s in self.wizard_dialog_data.fields ) {
						flist[ii] = self.wizard_dialog_data.fields[s];
						ii++;
						if ( ii % 6 == 0 ) {
							vcount++;
						}
					}
					if ( vcount == 0 ) {
						return;
					}
					var selected_fields = [];
					for ( i = 0; i < vcount; i+=1 ) {
						var sel = '';
						if ( typeof( self.wizard_dialog_data.real_fields) != 'undefined' ) {
							selected_fields.push( self.wizard_dialog_data.real_fields[i] );
						} else {
							if ( ( flist[( i*6 ) + 1 ] ) === 'types-field' ) {
								selected_fields.push( flist[( i*6 ) + 3] );
							} else {
								selected_fields.push( '[' + flist[( i*6 ) + 1] + ']' );
							}
						}
					}
					
					var data = {
						action: 'wpv_loop_wizard_load_saved_fields',
						wpnonce : wpv_layout_wizard_strings.wpnonce,
						selected_fields: selected_fields,
						view_id: self.view_id
					};
					$.ajax({
						type: "POST",
						dataType: "json",
						url: ajaxurl,
						data: data,
						async: false,
						success: function( response ) {
							if ( response.success ) {
								self.saved_fields_html = response.data.html;
								$( response.data.html ).appendTo( '.js-wpv-layout-wizard-layout-fields' );
							}
						},
						complete: function() {
							spinnerContainer.remove();
							$( '.js-wpv-layout-wizard-layout-fields-feedback' ).hide();
							thiz
								.prop( 'disabled', false )
								.addClass( 'button-primary' )
								.removeClass( 'button-secondary' );
						}
					});
				
				}
				
				$.each( $('.js-wpv-dialog-layout-wizard select.js-wpv-select2' ),function() {
					if ( ! $( this ).hasClass( 'select2-offscreen' ) ) {
						$( this ).select2();
					}
				});
				$( '.js-wpv-layout-wizard-layout-fields' )
					.addClass( 'js-wpv-layout-wizard-layout-fields-loaded' )
					.sortable({
						handle: ".js-layout-wizard-move-field",
						axis: 'y',
						containment: ".js-wpv-layout-wizard-layout-fields",
						helper: 'clone',
						tolerance: "pointer"
					});
			}
        } else if ( index === 1 ) {
			self.saved_fields_html = null;
            var fields = [],
			spinnerContainer = $( '<div class="wpv-spinner ajax-loader">' ).prependTo( '.js-wpv-layout-wizard-dialog-footer' ).show();			
			$.each( $( 'select.js-wpv-layout-wizard-item' ), function( index ) {
				value = $(this).val();
				headname = $('[value="'+value+'"]').data('headename');
				rowtitle = $('[value="'+value+'"]').data('rowtitle');
				fields[index] = Array( '', editor_decode64(value), '', rowtitle, headname, rowtitle );
			});
            if ( 
				$( '#js-wpv-use-view-loop-ct' ).prop( 'checked' ) 
				&& self.check_for_only_content_template_field( fields ) === false 
			) {
                self.use_loop_template = true;
            } else {
                self.use_loop_template = false;
            }
			thiz
				.prop( 'disabled', true )
				.removeClass( 'button-primary' )
				.addClass( 'button-secondary' );
            if (
				self.use_loop_template_id == ''
				&& self.use_loop_template 
			) {
                var data = {
                    wpnonce : $('#layout_wizard_nonce').attr('value'),
                    action: 'wpv_create_layout_content_template',
                    view_id: self.view_id,
                    view_name: $('.js-title').val()
                },
				messages_container = thiz.closest( '.js-wpv-dialog-layout-wizard' ).find( '.js-wpv-message-container' );
                $.ajax({
                    async: false,
                    type: "POST",
                    url: ajaxurl,
                    data: data,
                    dataType: 'json',
                    success: function( response ) {
						if ( response.success ) {
							self.use_loop_template_id = response.data.template_id;
							self.use_loop_template_title = response.data.template_title;
							$( '.js-wpv-settings-inline-templates' ).show();
							if ( 
								response.data.template_id 
								&& $( '#wpv-ct-listing-' + response.data.template_id ).html() 
							) {
								$( '#wpv-ct-listing-' + response.data.template_id ).removeClass( 'hidden' );
							} else {
								$( '.js-wpv-content-template-view-list > ul' )
									.first()
										.prepend( response.data.template_html );
							}
							$('.js-wpv-ct-listing-' + response.data.template_id + ' .js-wpv-ct-remove-from-view').prop( 'disabled', true );
							self.process_layout_wizard_data( fields, function() {
								spinnerContainer.remove();
								$.colorbox.close();
								codemirror_views_layout.refresh();
								codemirror_views_layout.focus();
								if ( $( '.js-wpv-section-unsaved' ).length <= 0 ) {
									setConfirmUnload(false);
								}
							});
						} else {
							self.use_loop_template_id = '';
							self.use_loop_template_title = '';
							spinnerContainer.remove();
							self.edit_screen.manage_ajax_fail( response.data, messages_container );
						}
                    },
                    error: function( ajaxContext ) {
                        console.log( "Error: ", ajaxContext.responseText );
                    },
                    complete: function() {
                        
                    }
                });
            } else {
                if ( $( '.js-wpv-ct-listing-' + self.use_loop_template_id ).html() !== '' ) {
                    if ( ! self.use_loop_template ) {
                        $( '.js-wpv-ct-listing-' + self.use_loop_template_id ).hide();
                    } else {
                        $( '.js-wpv-ct-listing-' + self.use_loop_template_id ).show();
                    }
                 }
                self.process_layout_wizard_data( fields, function() {
					spinnerContainer.remove();
					$.colorbox.close();
					codemirror_views_layout.refresh();
					codemirror_views_layout.focus();
					if ( $( '.js-wpv-section-unsaved' ).length <= 0 ) {
						setConfirmUnload(false);
					}
				});
            }
		}
		index++;
		$( '.wpv-dialog-nav-tab a' ).eq( index ).removeClass( 'js-tab-not-visited' );
		self.change_tab( false );
	});
	
	// Select2 behaviour
	
	// Close select2 when clicking outside the dropdowns
	$( document ).on( 'mousedown','.js-wpv-dialog-layout-wizard, #cboxOverlay',function( e ) {
		if ( $( e.target ).parents( '.js-wpv-select2' ).length === 0 ) {
			$( 'select.js-wpv-select2' ).each( function() {
				$( this ).select2( 'close' );
			});
		}
	});
	
	// Close select2 when opening a new one
	$( document ).on( 'select2-opening', '.js-wpv-select2', function( e ) {
		$( 'select.js-wpv-select2' ).each( function() {
			$( this ).select2( 'close' );
		});
	});
	
	// @todo review if this is used anywhere outside Views, and kill it with fire!
    $( document ).on( 'cbox_closed', function() {
         $( '.js-wpv-select2' ).select2( 'destroy' );
         $( document ).off( 'keypress.colorbox' ); // unbind the keypress event
    });
	
	// Shortcodes GUI management
	
	$( document ).on( 'click', '.js-wpv-loop-wizard-shortcode-ui', function() {
		var thiz = $( this );
		self.doing_shortcode_gui = true;
		self.doing_shortcode_gui_for = thiz.closest( '.js-wpv-loop-wizard-item-container' ).prop( 'id' );
		self.doing_shortcode_gui_for_selected = thiz.closest( '.js-wpv-loop-wizard-item-container' ).find( 'select.js-wpv-layout-wizard-item' ).find( ':selected' );
	});
	
	$( document ).on( 'js_event_wpv_shortcode_inserted', function( event, shortcode, content, attrs, shortcode_complete ) {
		if ( self.doing_shortcode_gui = true ) {
			$( '#' + self.doing_shortcode_gui_for )
				.find( 'select.js-wpv-layout-wizard-item' )
					.find( ':selected' )
						.val( Base64.encode( shortcode_complete ) );
			self.doing_shortcode_gui = false;
			self.doing_shortcode_gui_for = null;
			self.doing_shortcode_gui_for_selected = null;
		}
	});
	
	// ---------------------------------
	// Init
	// ---------------------------------
	
	self.init = function() {
		self.fetch_initial_settings();
		if ( 
			$('#js-loop-content-template').val() !== '' 
			&& $('#js-loop-content-template').val() !== '0' // Sometimes this can be zero
		) {
			self.use_loop_template = true;
			self.use_loop_template_id = $( '#js-loop-content-template' ).val();
			self.use_loop_template_title = $( '#js-loop-content-template-name' ).val();
			$( '.js-wpv-ct-listing-' + self.use_loop_template_id + ' .js-wpv-ct-remove-from-view' ).prop( 'disabled', true );
			if ( $('.js-wpv-ct-listing-' + self.use_loop_template_id + ' .js-wpv-open-close-arrow').hasClass('icon-caret-down') ) {
				$( '.js-wpv-ct-listing-' + self.use_loop_template_id + ' .js-wpv-content-template-open' ).click();
			}
		}
	};
	
	self.init();

};

jQuery( document ).ready( function( $ ) {
    WPViews.layout_wizard = new WPViews.LayoutWizard( $ );
});

/**
* Types fields compatibility
*/

// Types compatibility
	jQuery(document).on('click', '.js-custom-types-fields', function(){
		WPViews.layout_wizard.wizard_dialog_fields = [];
		$i = 0;
		jQuery('select.js-layout-wizard-item').each(function() {
			WPViews.layout_wizard.wizard_dialog_fields[$i++] = jQuery(this).find(':selected').val();
		});

		jQuery.each(jQuery('.js-wpv-dialog-layout-wizard select.js-wpv-select2'),function(){
				jQuery(this).select2('destroy');
		});
		
		WPViews.layout_wizard.wizard_dialog = jQuery('#colorbox').clone();
		WPViews.layout_wizard.wizard_dialog_item = jQuery(this).parent().find('[name=layout-wizard-style]').find(':selected');


		WPViews.layout_wizard.wizard_dialog_item_parent = jQuery(this).parent();
		
		// Collect dialog data to be restored
		WPViews.layout_wizard.wizard_dialog_style = jQuery('input[name="layout-wizard-style"]:checked').val();
		WPViews.layout_wizard.wizard_dialog_table_cols = jQuery( 'select.js-wpv-layout-wizard-table-cols[name="table_cols"]' ).val();
		WPViews.layout_wizard.wizard_dialog_include_field_names = jQuery('input[name="include_field_names"]').prop( 'checked' );
		WPViews.layout_wizard.wizard_dialog_bootstrap_row_class = jQuery( 'input[name="bootstrap_grid_row_class"]' ).prop( 'checked' );
		WPViews.layout_wizard.wizard_dialog_bootstrap_grid_cols = jQuery( 'select.js-wpv-layout-wizard-bootstrap-grid-cols[name="bootstrap_grid_cols"]' ).val();
		WPViews.layout_wizard.wizard_dialog_bootstrap_grid_container = jQuery( 'input[name="bootstrap_grid_container"]' ).prop( 'checked' );
		WPViews.layout_wizard.wizard_dialog_bootstrap_grid_individual_yes = jQuery( '#bootstrap_grid_individual_yes' ).prop( 'checked' );
		WPViews.layout_wizard.wizard_dialog_bootstrap_grid_individual_no = jQuery( '#bootstrap_grid_individual_no' ).prop( 'checked' );
		WPViews.layout_wizard.wizard_dialog_use_content_template = jQuery( 'input#js-wpv-use-view-loop-ct' ).prop( 'checked' );

		var current = Base64.decode(WPViews.layout_wizard.wizard_dialog_item.val());
		var metatype = current.search(/types.*?field=/g) == -1 ? 'usermeta' : 'postmeta';
		if ( typeof(jQuery(this).data('type')) !== 'undefined') {
			metatype = jQuery(this).data('type');
		}
		typesWPViews.wizardEditShortcode(jQuery(this).attr('rel'), metatype, -1, current);
	});

function wpv_restore_wizard_popup(shortcode) {
    jQuery.colorbox({
         html: jQuery(WPViews.layout_wizard.wizard_dialog).find('#cboxLoadedContent').html(),
         onComplete: function() {
            var select = jQuery('#'+WPViews.layout_wizard.wizard_dialog_item_parent.prop('id')+' select option[value="'+WPViews.layout_wizard.wizard_dialog_item.val()+'"]');

            jQuery('#'+WPViews.layout_wizard.wizard_dialog_item_parent.prop('id')+' select').val(WPViews.layout_wizard.wizard_dialog_item.val());


            $i = 0;
            jQuery('select.js-layout-wizard-item').each(function() {
                /*jQuery(this).find('[value="'+wpv_all_selected[$i]+'"]').click();*/
                jQuery(this).val(WPViews.layout_wizard.wizard_dialog_fields[$i]);
                $i++;
            });

            select.val(Base64.encode(shortcode));

            jQuery('input[name=layout-wizard-style][value='+WPViews.layout_wizard.wizard_dialog_style+']').click();
			jQuery( 'select.js-wpv-layout-wizard-table-cols[name="table_cols"]' ).val( WPViews.layout_wizard.wizard_dialog_table_cols );
			jQuery('input[name="include_field_names"]').prop( 'checked', WPViews.layout_wizard.wizard_dialog_include_field_names );
			jQuery( 'input[name="bootstrap_grid_row_class"]' ).prop( 'checked', WPViews.layout_wizard.wizard_dialog_bootstrap_row_class );
			jQuery( 'select.js-wpv-layout-wizard-bootstrap-grid-cols[name="bootstrap_grid_cols"]' ).val( WPViews.layout_wizard.wizard_dialog_bootstrap_grid_cols );
			jQuery( 'input[name="bootstrap_grid_container"]' ).prop( 'checked', WPViews.layout_wizard.wizard_dialog_bootstrap_grid_container );
			jQuery( '#bootstrap_grid_individual_yes' ).prop( 'checked', WPViews.layout_wizard.wizard_dialog_bootstrap_grid_individual_yes );
			jQuery( '#bootstrap_grid_individual_no' ).prop( 'checked', WPViews.layout_wizard.wizard_dialog_bootstrap_grid_individual_no );
			jQuery( 'input#js-wpv-use-view-loop-ct' ).prop( 'checked', WPViews.layout_wizard.wizard_dialog_use_content_template );
			
			
			jQuery.each(jQuery('.js-wpv-dialog-layout-wizard select.js-wpv-select2'),function(){
                    jQuery(this).select2();
            });

         }
     });
}

function wpv_cancel_wizard_popup() {
    jQuery.colorbox({
         html: jQuery(WPViews.layout_wizard.wizard_dialog).find('#cboxLoadedContent').html(),
         onComplete: function() {
            var select = jQuery('#'+WPViews.layout_wizard.wizard_dialog_item_parent.prop('id')+' select option[value="'+WPViews.layout_wizard.wizard_dialog_item.val()+'"]');

            jQuery('#'+WPViews.layout_wizard.wizard_dialog_item_parent.prop('id')+' select').val(WPViews.layout_wizard.wizard_dialog_item.val());

            $i = 0;
            jQuery('select.js-layout-wizard-item').each(function() {
                jQuery(this).val(WPViews.layout_wizard.wizard_dialog_fields[$i]);
                $i++;
            });

            jQuery('input[name=layout-wizard-style][value='+WPViews.layout_wizard.wizard_dialog_style+']').click();
			jQuery( 'select.js-wpv-layout-wizard-table-cols[name="table_cols"]' ).val( WPViews.layout_wizard.wizard_dialog_table_cols );
			jQuery('input[name="include_field_names"]').prop( 'checked', WPViews.layout_wizard.wizard_dialog_include_field_names );
			jQuery( 'input[name="bootstrap_grid_row_class"]' ).prop( 'checked', WPViews.layout_wizard.wizard_dialog_bootstrap_row_class );
			jQuery( 'select.js-wpv-layout-wizard-bootstrap-grid-cols[name="bootstrap_grid_cols"]' ).val( WPViews.layout_wizard.wizard_dialog_bootstrap_grid_cols );
			jQuery( 'input[name="bootstrap_grid_container"]' ).prop( 'checked', WPViews.layout_wizard.wizard_dialog_bootstrap_grid_container );
			jQuery( '#bootstrap_grid_individual_yes' ).prop( 'checked', WPViews.layout_wizard.wizard_dialog_bootstrap_grid_individual_yes );
			jQuery( '#bootstrap_grid_individual_no' ).prop( 'checked', WPViews.layout_wizard.wizard_dialog_bootstrap_grid_individual_no );
			jQuery( 'input#js-wpv-use-view-loop-ct' ).prop( 'checked', WPViews.layout_wizard.wizard_dialog_use_content_template );

            jQuery.each(jQuery('.js-wpv-dialog-layout-wizard select.js-wpv-select2'),function(){
                    jQuery(this).select2();
            });

         }
     });
}


var Base64 = {

	// private property
	_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
	
	// public method for encoding
	encode : function (input) {
		var output = "";
		var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
		var i = 0;
	
		input = Base64._utf8_encode(input);
	
		while (i < input.length) {
	
			chr1 = input.charCodeAt(i++);
			chr2 = input.charCodeAt(i++);
			chr3 = input.charCodeAt(i++);
	
			enc1 = chr1 >> 2;
			enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
			enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
			enc4 = chr3 & 63;
	
			if (isNaN(chr2)) {
				enc3 = enc4 = 64;
			} else if (isNaN(chr3)) {
				enc4 = 64;
			}
	
			output = output +
			this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
			this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
	
		}
	
		return output;
	},
	
	// public method for decoding
	decode : function (input) {
		var output = "";
		var chr1, chr2, chr3;
		var enc1, enc2, enc3, enc4;
		var i = 0;
	
		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
	
		while (i < input.length) {
	
			enc1 = this._keyStr.indexOf(input.charAt(i++));
			enc2 = this._keyStr.indexOf(input.charAt(i++));
			enc3 = this._keyStr.indexOf(input.charAt(i++));
			enc4 = this._keyStr.indexOf(input.charAt(i++));
	
			chr1 = (enc1 << 2) | (enc2 >> 4);
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
			chr3 = ((enc3 & 3) << 6) | enc4;
	
			output = output + String.fromCharCode(chr1);
	
			if (enc3 != 64) {
				output = output + String.fromCharCode(chr2);
			}
			if (enc4 != 64) {
				output = output + String.fromCharCode(chr3);
			}
	
		}
	
		output = Base64._utf8_decode(output);
	
		return output;
	
	},
	
	// private method for UTF-8 encoding
	_utf8_encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";
	
		for (var n = 0; n < string.length; n++) {
	
			var c = string.charCodeAt(n);
	
			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}
	
		}
	
		return utftext;
	},
	
	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;
	
		while ( i < utftext.length ) {
	
			c = utftext.charCodeAt(i);
	
			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}
	
		}
	
		return string;
	}

}
