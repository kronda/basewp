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

WPViews.SearchFilterGUI = function( $ ) {
	
	var self = this;
	
	self.view_id = $('.js-post_ID').val();
	
	self.icon_edit = '<i class="icon-chevron-up"></i>&nbsp;&nbsp;';
	self.icon_save = '<i class="icon-ok"></i>&nbsp;&nbsp;';
	self.spinner = '<span class="wpv-spinner ajax-loader"></span>&nbsp;&nbsp;';
	
	self.post_row = '.js-wpv-filter-row-post-search';
	self.post_options_container_selector = '.js-wpv-filter-post-search-options';
	self.post_summary_container_selector = '.js-wpv-filter-post-search-summary';
	self.post_edit_open_selector = '.js-wpv-filter-post-search-edit-open';
	self.post_close_save_selector = '.js-wpv-filter-post-search-edit-ok';
	
	self.post_current_options = $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize();
	
	self.tax_row = '.js-wpv-filter-row-taxonomy-search';
	self.tax_options_container_selector = '.js-wpv-filter-taxonomy-search-options';
	self.tax_summary_container_selector = '.js-wpv-filter-taxonomy-search-summary';
	self.tax_edit_open_selector = '.js-wpv-filter-taxonomy-search-edit-open';
	self.tax_close_save_selector = '.js-wpv-filter-taxonomy-search-edit-ok';
	
	self.tax_current_options = $( self.tax_options_container_selector + ' input, ' + self.tax_options_container_selector + ' select').serialize();
	
	//--------------------
	// Events for search
	//--------------------
	
	// Open the edit box and rebuild the current values; show the close/save button-primary
	// TODO maybe the show() could go to the general file
	
	$( document ).on( 'click', self.post_edit_open_selector, function() {
		self.post_current_options = $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize();
		$( self.post_close_save_selector ).show();
		$( self.post_row ).addClass( 'wpv-filter-row-current' );
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
					WPV_parametric_local.add_search.handle_flags();
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
	
	// Remove search filter
	
	$( document ).on( 'click', self.post_row + ' .js-wpv-filter-remove', function() {
		self.post_current_options = '';
	});
	
	$( document ).on( 'js_event_wpv_query_filter_created', function( event, filter_type ) {
		if ( filter_type == 'post_search' ) {
			self.post_current_options = $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize();
		}
	});
	
	//--------------------
	// Events for taxonomy search
	//--------------------
	
	// Open the edit box and rebuild the current values; show the close/save button-primary
	// TODO maybe the show() could go to the general file
	
	$( document ).on( 'click', self.tax_edit_open_selector, function() {
		self.tax_current_options = $( self.tax_options_container_selector + ' input, ' + self.tax_options_container_selector + ' select' ).serialize();
		$( self.tax_close_save_selector ).show();
		$( self.tax_row ).addClass( 'wpv-filter-row-current' );
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
	
	// Remove tax search filter
	
	$( document ).on( 'click', self.tax_row + ' .js-wpv-filter-remove', function() {
		self.tax_current_options = '';
	});
	
	//--------------------
	// Init
	//--------------------
	
	self.init = function() {
		
	};
	
	self.init();

};

jQuery( document ).ready( function( $ ) {
    WPViews.search_filter_gui = new WPViews.SearchFilterGUI( $ );
});