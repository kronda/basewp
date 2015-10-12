/**
* Views Post Relationship Filter GUI - script
*
* Adds basic interaction for the Post Relationship Filter
*
* @package Views
*
* @since 1.7.0
*/


var WPViews = WPViews || {};

WPViews.PostRelationshipFilterGUI = function( $ ) {
	
	var self = this;
	
	self.view_id = $('.js-post_ID').val();
	
	self.icon_edit = '<i class="icon-chevron-up"></i>&nbsp;&nbsp;';
	self.icon_save = '<i class="icon-ok"></i>&nbsp;&nbsp;';
	self.spinner = '<span class="wpv-spinner ajax-loader"></span>&nbsp;&nbsp;';
	
	self.post_row = '.js-wpv-filter-row-post-relationship';
	self.post_options_container_selector = '.js-wpv-filter-post-relationship-options';
	self.post_summary_container_selector = '.js-wpv-filter-post-relationship-summary';
	self.post_messages_container_selector = '.js-wpv-filter-row-post-relationship .js-wpv-filter-toolset-messages';
	self.post_edit_open_selector = '.js-wpv-filter-post-relationship-edit-open';
	self.post_close_save_selector = '.js-wpv-filter-post-relationship-edit-ok';
	
	self.post_type_select = {};
	
	self.post_current_options = $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize();
	
	//--------------------
	// Functions for post relationship
	//--------------------
	
	// Show an hide notices related to the content selection section
	// @todo watch out! it is cleared when opening the edit fast after save, seems to be because the show/hide of the success message!!!!!!!!!!!!!!!!!!!!!!!!!!
	
	self.show_hide_post_relationship_notice = function() {
		var show = false,
		rel_message = '',
		list = '';
		if ( $('.js-wpv-query-post-type:checked').length ) {
			$('.js-wpv-query-post-type:checked').each( function() {
				if ( $( this ).data( 'typeschild' ) == 'no' ) {
					show = true;
					if ( rel_message == '' ) {
						rel_message = wpv_pr_strings.post_type_orphan;
					}
					if ( list != '' ) {
						list += ',';
					}
					list += ' ' + $( this ).parent( 'li' ).find( 'label' ).html();
				}
			});
			if ( list != '' ) {
				rel_message += list;
			}
		} else {
			show = true;
			rel_message = wpv_pr_strings.post_type_missing;
		}
		if ( show ) {
			$( '.js-wpv-filter-post-relationship-notice' ).show();
			$( self.post_messages_container_selector ).wpvToolsetMessage({
				text:rel_message,
				type:'error',
				classname:'js-wpv-filter-post-relationship-info js-wpv-permanent-alert-error',
				inline:false,
				stay:true,
				fadeIn: 10,
				fadeOut: 10
			});
		} else {
			$( '.js-wpv-filter-post-relationship-notice' ).hide();
			$( self.post_row ).find( '.js-wpv-filter-post-relationship-info' ).remove();
		}
	}
	
	//--------------------
	// Events for post relationship
	//--------------------
	
	// Open the edit box and rebuild the current values; show the close/save button-primary
	// TODO maybe the show() could go to the general file
	
	$( document ).on( 'click', self.post_edit_open_selector, function() {
		self.post_current_options = $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize();
		$( self.post_close_save_selector ).show();
		$( self.post_row ).addClass( 'wpv-filter-row-current' );
		self.show_hide_post_relationship_notice();
	});
	
	// Track changes in options
	
	$( document ).on( 'change keyup input cut paste', self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select', function() {
		$( this ).removeClass( 'filter-input-error' );
		$( self.post_close_save_selector ).prop( 'disabled', false );
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
			var valid = WPViews.query_filters.validate_filter_options( '.js-filter-post-relationship' );
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
					thiz
						.prop( 'disabled', false )
						.hide();
				});
			}
		}
	});
	
	// Remove  filter
	
	$( document ).on( 'click', self.post_row + ' .js-wpv-filter-remove', function() {
		self.post_current_options = '';
	});
	
	// Update posts selector when changing the specific option post type
	// Cache options to prevent multiple AJAX calls for the same post type

	$( document ).on( 'change', '.js-post-relationship-post-type', function() {
		// Update the parents for the selected type.
		var post_type = $('.js-post-relationship-post-type').val();
		$( 'select#post_relationship_id' ).remove();
		if ( typeof self.post_type_select[post_type] == "undefined" ) {
			var data = {
				action : 'wpv_get_post_relationship_post_select',
				post_type : post_type,
				wpnonce : $('.js-post-relationship-post-type').data('nonce')
			};
			var spinnerContainer = $( self.spinner ).insertAfter( $(this) ).show();
			$.post( ajaxurl, data, function( response ) {
				if ( typeof( response ) !== 'undefined' ) {
					if ( response != 0 ) {
						self.post_type_select[post_type] = response;
						$( '.js-post-relationship-post-type' ).after( self.post_type_select[post_type] );
						$( '.js-post-relationship-shortcode-attribute' ).trigger( 'change' );
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
			$( '.js-post-relationship-post-type' ).after( self.post_type_select[post_type] );
			$( '.js-post-relationship-shortcode-attribute' ).trigger( 'change' );
		}
	});
	
	// Content selection section saved event
	
	$( document ).on( 'js_event_wpv_query_type_options_saved', '.js-wpv-query-type-update', function( event, query_type ) {
		self.show_hide_post_relationship_notice();
	});
	
	// Filter creation event
	
	$( document ).on( 'js_event_wpv_query_filter_created', function( event, filter_type ) {
		if ( filter_type == 'post_relationship' ) {
			self.show_hide_post_relationship_notice();
		}
		if ( filter_type == 'parametric-all' ) {
			self.post_current_options = $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize();
		}
	});
	
	//--------------------
	// Init
	//--------------------
	
	self.init = function() {
		self.show_hide_post_relationship_notice();
	};
	
	self.init();

};

jQuery( document ).ready( function( $ ) {
    WPViews.post_relationship_filter_gui = new WPViews.PostRelationshipFilterGUI( $ );
});