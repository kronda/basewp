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

WPViews.TaxonomyTermFilterGUI = function( $ ) {
	
	var self = this;
	
	self.view_id = $('.js-post_ID').val();
	
	self.icon_edit = '<i class="icon-chevron-up"></i>&nbsp;&nbsp;';
	self.icon_save = '<i class="icon-ok"></i>&nbsp;&nbsp;';
	self.spinner = '<span class="spinner ajax-loader"></span>&nbsp;&nbsp;';
	
	self.tax_row = '.js-wpv-filter-row-taxonomy-term';
	self.tax_options_container_selector = '.js-wpv-filter-taxonomy-term-options';
	self.tax_summary_container_selector = '.js-wpv-filter-taxonomy-term-summary';
	self.tax_edit_open_selector = '.js-wpv-filter-taxonomy-term-edit-open';
	self.tax_close_save_selector = '.js-wpv-filter-taxonomy-term-edit-ok';
	
	self.tax_current_options = $( self.tax_options_container_selector + ' input, ' + self.tax_options_container_selector + ' select').serialize();
	
	//--------------------
	// Functions for taxonomy term
	//--------------------
	
	self.show_hide_term_list = function() {
		var mode = $( '.js-wpv-taxonomy-term-mode:checked' ).val();
		if ( mode == 'THESE' ) {
			$( '.js-taxonomy-term-checklist' ).show();
		} else {
			$( '.js-taxonomy-term-checklist' ).hide();
		}
	};
	
	//--------------------
	// Events for taxonomy term
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
			}
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
	
	// Show or hide the terms listStyleType
	$( document ).on( 'change', '.js-wpv-taxonomy-term-mode', function() {
		self.show_hide_term_list();
	});
	
	//--------------------
	// Init
	//--------------------
	
	self.init = function() {
		self.show_hide_term_list();
	};
	
	self.init();

};

jQuery( document ).ready( function( $ ) {
    WPViews.taxonomy_term_filter_gui = new WPViews.TaxonomyTermFilterGUI( $ );
});