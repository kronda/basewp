/**
* Views Search Filter GUI - script
*
* Adds basic interaction for the Search Filter
*
* @package Views
*
* @since 1.7.0
*/


var WPViews = WPViews || {};

WPViews.ParentFilterGUI = function( $ ) {
	
	var self = this;
	
	self.view_id = $('.js-post_ID').val();
	
	self.icon_edit = '<i class="icon-chevron-up"></i>&nbsp;&nbsp;';
	self.icon_save = '<i class="icon-ok"></i>&nbsp;&nbsp;';
	self.spinner = '<span class="spinner ajax-loader"></span>&nbsp;&nbsp;';
	
	self.post_row = '.js-wpv-filter-row-post-parent';
	self.post_options_container_selector = '.js-wpv-filter-post-parent-options';
	self.post_summary_container_selector = '.js-wpv-filter-post-parent-summary';
	self.post_messages_container_selector = '.js-wpv-filter-row-post-parent .js-wpv-filter-toolset-messages';
	self.post_edit_open_selector = '.js-wpv-filter-post-parent-edit-open';
	self.post_close_save_selector = '.js-wpv-filter-post-parent-edit-ok';
	
	self.post_current_options = $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize();
	
	self.post_type_select = {};
	
	self.tax_row = '.js-wpv-filter-row-taxonomy-parent';
	self.tax_options_container_selector = '.js-wpv-filter-taxonomy-parent-options';
	self.tax_summary_container_selector = '.js-wpv-filter-taxonomy-parent-summary';
	self.tax_messages_container_selector = '.js-wpv-filter-row-taxonomy-parent .js-wpv-filter-toolset-messages';
	self.tax_edit_open_selector = '.js-wpv-filter-taxonomy-parent-edit-open';
	self.tax_close_save_selector = '.js-wpv-filter-taxonomy-parent-edit-ok';
	
	self.tax_current_options = $( self.tax_options_container_selector + ' input, ' + self.tax_options_container_selector + ' select').serialize();
	
	//--------------------
	// Functions for parent
	//--------------------
	
	// Show an hide notices related to the content selection section
	
	self.show_hide_post_parent_notice = function() {
		var show = false,
		post_parent_message = '',
		list = '';
		if ( $('.js-wpv-query-post-type:checked').length ) {
			$('.js-wpv-query-post-type:checked').each( function() {
				if ( $( this ).data( 'hierarchical' ) == 'no' ) {
					show = true;
					if ( post_parent_message == '' ) {
						post_parent_message = wpv_parent_strings.post_type_flat;
					}
					if ( list != '' ) {
						list += ',';
					}
					list += ' ' + $( this ).parent( 'li' ).find( 'label' ).html();
				}
			});
			if ( list != '' ) {
				post_parent_message += list;
			}
		} else {
			show = true;
			post_parent_message = wpv_parent_strings.post_type_missing;
		}
		if ( show ) {
			$( '.js-wpv-filter-post-parent-notice' ).show();
			$( self.post_messages_container_selector ).wpvToolsetMessage({
				text:post_parent_message,
				type:'error',
				classname:'js-wpv-filter-post-parent-info',
				inline:false,
				stay:true,
				fadeIn: 10,
				fadeOut: 10
			});
		} else {
			$( '.js-wpv-filter-post-parent-notice' ).hide();
			$( self.post_row ).find( '.js-wpv-filter-post-parent-info' ).remove();
		}
	};
	
	//--------------------
	// Events for parent
	//--------------------
	
	// Open the edit box and rebuild the current values; show the close/save button-primary
	// TODO maybe the show() could go to the general file
	
	$( document ).on( 'click', self.post_edit_open_selector, function() {
		self.post_current_options = $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize();
		$( self.post_close_save_selector ).show();
		$( self.post_row ).addClass( 'wpv-filter-row-current' );
		self.show_hide_post_parent_notice();
	});
	
	// Track changes in options
	
	$( document ).on( 'change keyup input cut paste', self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select', function() { // watch on inputs change
		WPViews.query_filters.clear_validate_messages( self.post_row );
		if ( self.post_current_options != $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize() ) {
			$( self.post_close_save_selector )
				.addClass('button-primary js-wpv-section-unsaved')
				.removeClass('button-secondary')
				.html(
					self.icon_save + $( self.post_close_save_selector ).data('save')
				);
			setConfirmUnload( true );
		} else {
			$( self.post_close_save_selector )
				.addClass('button-secondary')
				.removeClass('button-primary js-wpv-section-unsaved')
				.html(
					self.icon_edit + $( self.post_close_save_selector ).data('close')
				);
			$( self.post_close_save_selector )
				.parent()
					.find( '.unsaved' )
					.remove();
			if ( $( '.js-wpv-section-unsaved' ).length < 1 ) {
				setConfirmUnload( false );
			}
		}
	});
	
	// Save filter options
	
	$( document ).on( 'click', self.post_close_save_selector, function() {
		var thiz = $( this );
		WPViews.query_filters.clear_validate_messages( self.post_row );
		if ( self.post_current_options == $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize() ) {
			WPViews.query_filters.close_filter_row( self.post_row );
			thiz.hide();
		} else {
			var valid = WPViews.query_filters.validate_filter_options( '.js-filter-post-parent' );
			if ( valid ) {
				// update_message = thiz.data('success');
				// unsaved_message = thiz.data('unsaved');
				var action = thiz.data( 'saveaction' ),
				nonce = thiz.data('nonce'),
				spinnerContainer = $( self.spinner ).insertBefore( thiz ).show(),
				error_container = thiz
					.closest( '.js-filter-row' )
						.find( '.js-wpv-filter-toolset-messages' );
				self.post_current_options = $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize();
				var data = {
					action: action,
					id: self.view_id,
					filter_options: self.post_current_options,
					wpnonce: nonce
				};
				$.post( ajaxurl, data, function( response ) {
					if ( response.success ) {
						$( self.post_close_save_selector )
							.addClass('button-secondary')
							.removeClass('button-primary js-wpv-section-unsaved')
							.html( 
								self.icon_edit + $( self.post_close_save_selector ).data( 'close' )
							);
						if ( $( '.js-wpv-section-unsaved' ).length < 1 ) {
							setConfirmUnload( false );
						}
						$( self.post_summary_container_selector ).html( response.data.summary );
						WPViews.query_filters.close_and_glow_filter_row( self.post_row, 'wpv-filter-saved' );
					} else {
						WPViews.view_edit_screen.manage_ajax_fail( response.data, error_container );
					}
				}, 'json' )
				.fail( function( jqXHR, textStatus, errorThrown ) {
					console.log( "Error: ", textStatus, errorThrown );
				})
				.always( function() {
					spinnerContainer.remove();
					thiz.hide();
				});
			}
		}
	});
	
	// Remove parent filter
	
	$( document ).on( 'click', self.post_row + ' .js-wpv-filter-remove', function() {
		self.post_current_options = '';
	});
	
	// Update posts selector when changing the specific option post type
	// Cache options to prevent multiple AJAX calls for the same post type

	$( document ).on( 'change', '.js-post-parent-post-type', function() {
		// Update the parents for the selected type.
		var post_type = $('.js-post-parent-post-type').val();
		$( 'select#post_parent_id' ).remove();
		if ( typeof self.post_type_select[post_type] == "undefined" ) {
			var data = {
				action : 'wpv_get_post_parent_post_select',
				post_type : post_type,
				wpnonce : $('.js-post-parent-post-type').data('nonce')
			};
			var spinnerContainer = $( self.spinner ).insertAfter( $(this) ).show();
			$.post( ajaxurl, data, function( response ) {
				if ( typeof( response ) !== 'undefined' ) {
					if ( response != 0 ) {
						self.post_type_select[post_type] = response;
						$( '.js-post-parent-post-type' ).after( self.post_type_select[post_type] );
						$( '#post_parent_id' ).trigger( 'change' );
					} else {
						console.log( "Error: WordPress AJAX returned " + response );
					}
				} else {
					console.log( "Error: AJAX returned ", response );
				}
			})
			.fail( function( jqXHR, textStatus, errorThrown ) {
				console.log( "Error: ", textStatus, errorThrown );
			})
			.always( function() {
				spinnerContainer.hide();
			});
		} else {
			$( '.js-post-parent-post-type' ).after( self.post_type_select[post_type] );
			$( '#post_parent_id' ).trigger( 'change' );
		}
	});
	
	//--------------------
	// Functions for taxonomy parent
	//--------------------
	
	// Show an hide notices related to the content selection section
	
	self.show_hide_tax_parent_notice = function() {
		var show = false,
		tax_parent_message = '',
		list = '';
		if ( $( '.js-wpv-query-taxonomy-type:checked' ).length ) {
			$( '.js-wpv-query-taxonomy-type:checked' ).each( function() {
				if ( $( this ).data( 'hierarchical' ) == 'no' ) {
					show = true;
					if ( tax_parent_message == '' ) {
						tax_parent_message = wpv_parent_strings.taxonomy_flat;
					}
					if ( list != '' ) {
						list += ',';
					}
					list += ' ' + $( this ).parent( 'li' ).find( 'label' ).html();
				}
			});
			if ( list != '' ) {
				tax_parent_message += list;
			}
		} else {
			show = true;
			tax_parent_message = wpv_parent_strings.taxonomy_missing;
		}
		if ( show ) {
			$( '.js-wpv-filter-taxonomy-parent-notice' ).show();
			$( self.tax_messages_container_selector ).wpvToolsetMessage({
				text:tax_parent_message,
				type:'error',
				classname:'js-wpv-filter-taxonomy-parent-info',
				inline:false,
				stay:true,
				fadeIn: 10,
				fadeOut: 10
			});
		} else {
			$( '.js-wpv-filter-taxonomy-parent-notice' ).hide();
			$( self.tax_row ).find( '.js-wpv-filter-taxonomy-parent-info' ).remove();
		}
		self.update_taxonomy_parent_id_dropdown();
	};
	
	// Update the taxonomy_parent_id select dropdown when there are relevant changes in the Content Selection section
	
	self.update_taxonomy_parent_id_dropdown = function() {
		var taxonomy_parent_select = $( '.js-taxonomy-parent-id' ),
		old_taxonomy = taxonomy_parent_select.data( 'taxonomy' );
		if ( taxonomy_parent_select.length > 0 ) {
			var current_taxonomy = $( '.js-wpv-query-taxonomy-type:checked' ).val(),
			nonce = taxonomy_parent_select.data( 'nonce' );
			if ( old_taxonomy != current_taxonomy ) {
				var data = {
					action: 'update_taxonomy_parent_id_dropdown',
					taxonomy: current_taxonomy,
					wpnonce: nonce
				},
				spinnerContainer = $( self.spinner ).insertAfter( taxonomy_parent_select ).show();
				$.post( ajaxurl, data, function( response ) {
					if ( ( typeof( response ) !== 'undefined' ) ) {
						if ( response != 0 ) {
							$( taxonomy_parent_select ).replaceWith( response ).val( '0' ).trigger( 'change' );
							$( self.tax_messages_container_selector ).wpvToolsetMessage({
								text: wpv_parent_strings.taxonomy_changed,
								type: 'error',
								classname:'js-wpv-filter-taxonomy-parent-changed-info',
								inline: false,
								stay: true
							});
							$( '.js-wpv-filter-taxonomy-parent-notice' ).show();
						} else {
							console.log( "Error: WordPress AJAX returned " + response );
						}
					} else {
						console.log( "Error: AJAX returned ", response );
					}
				})
				.fail( function( jqXHR, textStatus, errorThrown ) {
					console.log( "Error: ", textStatus, errorThrown );
				})
				.always( function() {
					spinnerContainer.remove();
				});
			}
		}
	};
	
	//--------------------
	// Events for taxonomy parent
	//--------------------
	
	// Open the edit box and rebuild the current values; show the close/save button-primary
	// TODO maybe the show() could go to the general file
	
	$( document ).on( 'click', self.tax_edit_open_selector, function() {
		self.tax_current_options = $( self.tax_options_container_selector + ' input, ' + self.tax_options_container_selector + ' select' ).serialize();
		$( self.tax_close_save_selector ).show();
		$( self.tax_row ).addClass( 'wpv-filter-row-current' );
		self.show_hide_tax_parent_notice();
	});
	
	// Track changes
	
	$( document ).on( 'change keyup input cut paste', self.tax_options_container_selector + ' input, ' + self.tax_options_container_selector + ' select', function() {
		WPViews.query_filters.clear_validate_messages( self.tax_row );
		if ( self.tax_current_options != $( self.tax_options_container_selector + ' input, ' + self.tax_options_container_selector + ' select' ).serialize() ) {
			$( self.tax_close_save_selector )
				.addClass( 'button-primary js-wpv-section-unsaved' )
				.removeClass( 'button-secondary' )
				.html(
					self.icon_save + $( self.tax_close_save_selector ).data('save')
				);
			setConfirmUnload( true );
		} else {
			$( self.tax_close_save_selector )
				.addClass( 'button-secondary' )
				.removeClass('button-primary js-wpv-section-unsaved')
				.html(
					self.icon_edit + $( self.tax_close_save_selector ).data('close')
				);
			$( self.tax_close_save_selector )
				.parent()
					.find( '.unsaved' )
					.remove();
			if ( $( '.js-wpv-section-unsaved' ).length < 1 ) {
				setConfirmUnload( false );
			}
		}
	});
	
	// Save options
	
	$( document ).on( 'click', self.tax_close_save_selector, function() {
		var thiz = $( this );
		WPViews.query_filters.clear_validate_messages( self.tax_row );
		if ( self.tax_current_options == $( self.tax_options_container_selector + ' input, ' + self.tax_options_container_selector + ' select' ).serialize() ) {
			WPViews.query_filters.close_filter_row( self.tax_row );
			thiz.hide();
		} else {
			// update_message = thiz.data('success');
			// unsaved_message = thiz.data('unsaved');
			var action = thiz.data( 'saveaction' ),
			nonce = thiz.data('nonce'),
			spinnerContainer = $( self.spinner ).insertBefore( thiz ).show(),
			error_container = thiz
				.closest( '.js-filter-row' )
					.find( '.js-wpv-filter-toolset-messages' );
			self.tax_current_options = $( self.tax_options_container_selector + ' input, ' + self.tax_options_container_selector + ' select' ).serialize();
			var data = {
				action: action,
				id: self.view_id,
				filter_options: self.tax_current_options,
				wpnonce: nonce
			};
			$.post( ajaxurl, data, function( response ) {
				if ( response.success ) {
					$( self.tax_close_save_selector )
						.addClass( 'button-secondary' )
						.removeClass( 'button-primary js-wpv-section-unsaved' )
						.html(
							self.icon_edit + $( self.tax_close_save_selector ).data( 'close' )
						);
					if ( $( '.js-wpv-section-unsaved' ).length < 1 ) {
						setConfirmUnload( false );
					}
					$( self.tax_summary_container_selector ).html( response.data.summary );
					WPViews.query_filters.close_and_glow_filter_row( self.tax_row, 'wpv-filter-saved' );
				} else {
					WPViews.view_edit_screen.manage_ajax_fail( response.data, error_container );
				}
			}, 'json' )
			.fail( function( jqXHR, textStatus, errorThrown ) {
				console.log( "Error: ", textStatus, errorThrown );
			})
			.always( function() {
				spinnerContainer.remove();
				thiz.hide();
			});
		}
	});
	
	// Remove tax parent filter
	
	$( document ).on( 'click', self.tax_row + ' .js-wpv-filter-remove', function() {
		self.tax_current_options = '';
	});
	
	// Content selection section saved event
	
	$( document ).on( 'js_event_wpv_query_type_options_saved', '.js-wpv-query-type-update', function( event, query_type ) {
		self.show_hide_post_parent_notice();
		self.show_hide_tax_parent_notice();
	});
	
	// Filter creation event
	
	$( document ).on( 'js_event_wpv_query_filter_created', function( event, filter_type ) {
		if ( filter_type == 'post_parent' ) {
			self.show_hide_post_parent_notice();
		}
		if ( filter_type == 'taxonomy_parent' ) {
			self.show_hide_tax_parent_notice();
		}
	});
	
	//--------------------
	// Init
	//--------------------
	
	self.init = function() {
		self.show_hide_post_parent_notice();
		self.show_hide_tax_parent_notice();
	};
	
	self.init();

};

jQuery( document ).ready( function( $ ) {
    WPViews.parent_filter_gui = new WPViews.ParentFilterGUI( $ );
});