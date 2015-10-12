/**
* Views Status Filter GUI - script
*
* Adds basic interaction for the Status Filter
*
* @package Views
*
* @since 1.7.0
*/


var WPViews = WPViews || {};

WPViews.StatusFilterGUI = function( $ ) {
	
	var self = this;
	
	self.view_id = $('.js-post_ID').val();
	
	self.icon_edit = '<i class="icon-chevron-up"></i>&nbsp;&nbsp;';
	self.icon_save = '<i class="icon-ok"></i>&nbsp;&nbsp;';
	self.spinner = '<span class="wpv-spinner ajax-loader"></span>&nbsp;&nbsp;';
	
	self.post_row = '.js-wpv-filter-row-post-status';
	self.post_options_container_selector = '.js-wpv-filter-post-status-options';
	self.post_summary_container_selector = '.js-wpv-filter-post-status-summary';
	self.post_edit_open_selector = '.js-wpv-filter-post-status-edit-open';
	self.post_close_save_selector = '.js-wpv-filter-post-status-edit-ok';
	
	self.post_current_options = $( self.post_options_container_selector + ' input' ).serialize();
	
	//--------------------
	// Events for status
	//--------------------
	
	// Open the edit box and rebuild the current values; show the close/save button-primary
	// TODO maybe the show() could go to the general file
	
	$( document ).on( 'click', self.post_edit_open_selector, function() {
		self.post_current_options = $( self.post_options_container_selector + ' input' ).serialize();
		$( self.post_close_save_selector ).show();
		$( self.post_row ).addClass( 'wpv-filter-row-current' );
	});
	
	// Track changes in options
	
	$( document ).on( 'change keyup input cut paste', self.post_options_container_selector + ' input', function() { // watch on inputs change
		WPViews.query_filters.clear_validate_messages( self.post_row );
		if ( self.post_current_options != $( self.post_options_container_selector + ' input' ).serialize() ) {
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
		if ( self.post_current_options == $( self.post_options_container_selector + ' input' ).serialize() ) {
			WPViews.query_filters.close_filter_row( self.post_row );
			thiz.hide();
			// We need to set the actio button to "Edit" because on newly added filters and no status selected there is no changes in options, hence no saving
			$( self.post_close_save_selector )
				.addClass('button-secondary')
				.removeClass('button-primary js-wpv-section-unsaved')
				.html(
					self.icon_edit + $( self.post_close_save_selector ).data('close')
				);
			if ( $( '.js-wpv-section-unsaved' ).length < 1 ) {
				setConfirmUnload( false );
			}
		} else {
			// update_message = thiz.data('success');
			// unsaved_message = thiz.data('unsaved');
			var action = thiz.data( 'saveaction' ),
			nonce = thiz.data('nonce'),
			spinnerContainer = $( self.spinner ).insertBefore( thiz ).show(),
			error_container = thiz
					.closest( '.js-filter-row' )
						.find( '.js-wpv-filter-toolset-messages' );
			self.post_current_options = $( self.post_options_container_selector + ' input' ).serialize();
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
	});
	
	// Remove status filter
	
	$( document ).on( 'click', self.post_row + ' .js-wpv-filter-remove', function() {
		self.post_current_options = '';
	});
	
	//--------------------
	// Init
	//--------------------
	
	self.init = function() {
		
	};
	
	self.init();

};

jQuery( document ).ready( function( $ ) {
    WPViews.status_filter_gui = new WPViews.StatusFilterGUI( $ );
});