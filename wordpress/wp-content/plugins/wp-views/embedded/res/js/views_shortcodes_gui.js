/**
* views_shortcode_gui.js
*
* Contains helper functions for the popup GUI used to set Views shortcode attributes
*
* @since 1.7
* @package Views
*/

var WPViews = WPViews || {};

WPViews.ShortcodesGUI = function( $ ) {
	var self = this;

	self.propertyOne = 1000; // this is a public variable
	self.colorbox_popup = '#colorbox';
	
	// Parametric search
	self.ps_view_id = 0;
	self.ps_orig_id = 0;
	self.ps_nonce = '';
	self.ps_action_button = '.js-wpv-insert-view-form-action';
	self.ps_prev_button = '.js-wpv-insert-view-form-prev';
	self.ps_display_container = '.js-wpv-insert-view-form-display-container';
	self.ps_target_container = '.js-wpv-insert-view-form-target-container';
	self.ps_target_set_container = '.js-wpv-insert-view-form-target-set-container';
	self.ps_set_existing_container = '.js-wpv-insert-view-form-target-set-existing-extra';
	self.ps_set_new_container = '.js-wpv-insert-view-form-target-set-create-extra';
	self.ps_create_target_button = '.js-wpv-insert-view-form-target-set-create-action';
	self.ps_create_target_title = '.js-wpv-insert-view-form-target-set-create-title';
	self.ps_existing_target_title = '.js-wpv-insert-view-form-target-set-existing-title';
	self.ps_existing_target_id = '.js-wpv-insert-view-form-target-set-existing-id';
	

	self.init = function() {
		
	};
	
	//-----------------------------------------
	// Parametric search open popup and initialize data
	//-----------------------------------------
	
	self.wpv_insert_view_form_popup = function( view_id, nonce, orig_id ) {
		self.ps_view_id = view_id;
		self.ps_orig_id = orig_id;
		$.colorbox({
			href: ajaxurl + '?_wpnonce=' + nonce + '&action=wpv_view_form_popup&view_id=' + view_id + '&orig_id=' + orig_id,
			inline : false,
			onComplete: function() {
				self.colorbox_popup = $( '#colorbox' );
				self.ps_action_button = self.colorbox_popup.find( '.js-wpv-insert-view-form-action' );
				self.ps_prev_button = self.colorbox_popup.find( '.js-wpv-insert-view-form-prev' );
				self.ps_display_container = self.colorbox_popup.find( '.js-wpv-insert-view-form-display-container' );
				self.ps_target_container = self.colorbox_popup.find( '.js-wpv-insert-view-form-target-container' );
				self.ps_target_set_container = self.colorbox_popup.find( '.js-wpv-insert-view-form-target-set-container' );
				self.ps_set_existing_container = self.colorbox_popup.find( '.js-wpv-insert-view-form-target-set-existing-extra' );
				self.ps_set_new_container = self.colorbox_popup.find( '.js-wpv-insert-view-form-target-set-create-extra' );
				self.ps_create_target_button = self.colorbox_popup.find( '.js-wpv-insert-view-form-target-set-create-action' );
				self.ps_create_target_title = self.colorbox_popup.find( '.js-wpv-insert-view-form-target-set-create-title' );
				self.ps_existing_target_title = self.colorbox_popup.find( '.js-wpv-insert-view-form-target-set-existing-title' );
				self.ps_existing_target_id = self.colorbox_popup.find( '.js-wpv-insert-view-form-target-set-existing-id' );
			}
		});
	}
	
	//-----------------------------------------
	// Parametric search events
	//-----------------------------------------
	
	/*
	* Adjust the action button text copy based on the action to perform
	*/
	
	$( document ).on( 'change', '.js-wpv-insert-view-form-display', function() {
		var display = self.colorbox_popup.find( '.js-wpv-insert-view-form-display:checked' ).val();
		if ( display == 'form' ) {
			self.ps_action_button.html( self.ps_action_button.data( 'forthlabel' ) );
		} else {
			self.ps_action_button.html( self.ps_action_button.data( 'insertlabel' ) );
		}
	});
	
	/*
	* Control the back button in the two-step setup
	*/
	
	$( document ).on( 'click', self.ps_prev_button, function() {
		self.ps_target_container.hide();
		self.ps_display_container.show();
		self.ps_action_button
			.removeClass( 'js-wpv-insert-view-form-dialog-steptwo button-secondary' )
			.addClass( 'button-primary' )
			.prop( 'disabled', false )
			.html( self.ps_action_button.data( 'forthlabel' ) );
		self.ps_prev_button.hide();
	});
	
	/*
	* Adjust the GUI when inserting just the form, based on the target options - target this or other page
	*/
	
	$( document ).on( 'change', '.js-wpv-insert-view-form-target', function() {
		var target = self.colorbox_popup.find( '.js-wpv-insert-view-form-target:checked' ).val(),
		set_target = self.colorbox_popup.find( '.js-wpv-insert-view-form-target-set:checked' ).val();
		if ( target == 'self' ) {
			self.ps_target_set_container.hide();
			self.ps_action_button
				.addClass( 'button-primary' )
				.prop( 'disabled', false );
		} else if ( target == 'other' ) {
			self.ps_target_set_container.fadeIn( 'fast' );
			if ( set_target == 'existing' && self.ps_existing_target_id.val() != '' ) {
				self.colorbox_popup
					.find( '.js-wpv-insert-view-form-target-set-actions' )
						.show();
			}
			self.ps_action_button
				.removeClass( 'button-primary' )
				.addClass( 'button-secondary' )
				.prop( 'disabled', true );
		}
	});
	
	$( document ).on( 'click', '.js-wpv-insert-view-form-target-set-discard', function( e ) {
		e.preventDefault();
		self.ps_action_button
			.addClass( 'button-primary' )
			.removeClass( 'button-secondary' )
			.prop( 'disabled', false );
		self.colorbox_popup
			.find( '.js-wpv-insert-view-form-target-set-actions' )
				.hide();
	});
	
	$( document ).on( 'click', '.js-wpv-insert-view-form-target-set-existing-link', function() {
		self.ps_action_button
			.addClass( 'button-primary' )
			.removeClass( 'button-secondary' )
			.prop( 'disabled', false );
		self.colorbox_popup
			.find( '.js-wpv-insert-view-form-target-set-actions' )
				.hide();
	});
	
	/*
	* Adjust the GUI when inserting just the form and targeting another page, based on the target options - target existing or new page
	*/
	
	$( document ).on( 'change', '.js-wpv-insert-view-form-target-set', function() {
		var set_target = self.colorbox_popup.find( '.js-wpv-insert-view-form-target-set:checked' ).val();
		if ( set_target == 'create' ) {
			self.ps_set_existing_container.hide();
			self.ps_set_new_container.fadeIn( 'fast' );
			self.ps_action_button
				.removeClass( 'button-primary' )
				.addClass( 'button-secondary' )
				.prop( 'disabled', true );
		} else if ( set_target == 'existing' ) {
			self.ps_set_new_container.hide();
			self.ps_set_existing_container.fadeIn( 'fast' );
			self.ps_action_button
				.removeClass( 'button-primary' )
				.addClass( 'button-secondary' )
				.prop( 'disabled', true );
			if ( self.ps_existing_target_id.val() != '' ) {
				self.colorbox_popup
					.find( '.js-wpv-insert-view-form-target-set-actions' )
						.show();
			}
		}
	});
	
	/*
	* Adjust values when editing the target page title - clean data and mark this as unfinished
	*/
	
	$( document ).on('change input cut paste', self.ps_existing_target_title, function() {
		self.colorbox_popup
			.find( '.js-wpv-insert-view-form-target-set-actions' )
				.hide();
		self.colorbox_popup
			.find( '.js-wpv-insert-view-form-target-set-existing-link' )
				.attr( 'data-targetid', '' );
		self.colorbox_popup
			.find('.js-wpv-insert-view-form-target-set-existing-id')
				.val( '' )
				.trigger( 'manchange' );
	});
	
	/*
	* Disable the insert button when doing any change in the existing title textfield
	*
	* We use a custom event 'manchange' as in "manual change"
	*/
	
	$( document ).on( 'manchange', '.js-wpv-insert-view-form-target-set-existing-id', function() {
		self.ps_action_button
			.removeClass( 'button-primary' )
			.addClass( 'button-secondary' )
			.prop( 'disabled', true );
	});
	
	/*
	* Adjust GUI when creating a target page, based on the title value
	*/
	
	$( document ).on( 'change input cut paste', self.ps_create_target_title, function() {
		if ( self.ps_create_target_title.val() == '' ) {
			self.ps_create_target_button
				.prop( 'disabled', true )
				.addClass( 'button-secondary' )
				.removeClass( 'button-primary' );
		} else {
			self.ps_create_target_button
				.prop( 'disabled', false )
				.addClass( 'button-primary' )
				.removeClass( 'button-secondary' );
		}
	});
	
	/*
	* AJAX action to create a new target page
	*/

	$( document ).on( 'click', '.js-wpv-insert-view-form-target-set-create-action', function() {
		var thiz = $( this ),
		thiz_existing_radio = self.colorbox_popup.find( '.js-wpv-insert-view-form-target-set[value="existing"]' ),
		spinnerContainer = $('<div class="spinner ajax-loader">').insertAfter( thiz ).show();
		data = {
			action: 'wpv_create_form_target_page',
			post_title: self.ps_create_target_title.val(),
			_wpnonce: thiz.data( 'nonce' )
		};
		$.ajax({
			url:ajaxurl,
			data:data,
			success:function( response ) {
				decoded_response = $.parseJSON( response );
				if ( decoded_response.result == 'error' ) {
					
				} else {
					self.ps_existing_target_title.val( decoded_response.page_title );
					self.ps_existing_target_id.val( decoded_response.page_id );
					t_edit_link = self.colorbox_popup
						.find('.js-wpv-insert-view-form-target-set-existing-link')
							.data( 'editurl' );
					self.colorbox_popup
						.find('.js-wpv-insert-view-form-target-set-existing-link')
							.attr( 'href', t_edit_link + decoded_response.page_id + '&action=edit&completeview=' + self.ps_view_id + '&origid=' + self.ps_orig_id );
					thiz_existing_radio
						.prop( 'checked', true )
						.trigger( 'change' );
					self.colorbox_popup
						.find( '.js-wpv-insert-view-form-target-set-actions' )
							.show();
				}
			},
			error: function ( ajaxContext ) {
				
			},
			complete: function() {
				spinnerContainer.remove();
			}
		});
	});
	
	// Close the finished help boxes
	
	$( document ).on( 'click', '.js-wpv-insert-form-workflow-help-box-close', function( e ) {
		e.preventDefault();
		$( this ).parents( '.js-wpv-insert-form-workflow-help-box, .js-wpv-insert-form-workflow-help-box-after' ).hide();
	});
	
	//-----------------------------------------
	// Search term open popup
	//-----------------------------------------

	self.wpv_insert_search_term_popup = function( nonce ) {
		$.colorbox({
			href: ajaxurl + '?_wpnonce=' + nonce + '&action=wpv_search_term_popup',
			inline : false,
			onComplete: function() {
				
			}
		});
	}
	
	//-----------------------------------------
	// Translatable string open popup
	//-----------------------------------------
	
	self.wpv_insert_translatable_string_popup = function( nonce ) {
		$.colorbox({
			href: ajaxurl + '?_wpnonce=' + nonce + '&action=wpv_translatable_string_popup',
			inline : false,
			onComplete: function() {
				$( '.js-wpv-insert-translatable-string-shortcode' )
					.addClass( 'button-secondary' )
					.removeClass( 'button-primary' )
					.attr( 'disabled', true );
			}
		});
	}
	
	//-----------------------------------------
	// Translatable string events
	//-----------------------------------------
	
	$( document ).on( 'change keyup input cut paste', '.js-wpv-translatable-string-value, .js-wpv-translatable-string-context', function() {
		if ( $( '.js-wpv-translatable-string-value' ).val() != '' && $( '.js-wpv-translatable-string-context' ).val() != '' ) {
			$( '.js-wpv-insert-translatable-string-shortcode' )
				.addClass( 'button-primary' )
				.removeClass( 'button-secondary' )
				.attr( 'disabled', false );
		} else {
			$( '.js-wpv-insert-translatable-string-shortcode' )
				.addClass( 'button-secondary' )
				.removeClass( 'button-primary' )
				.attr( 'disabled', true );
		}
	});

	self.init(); // call the init method

};

jQuery( document ).ready( function( $ ) {
    WPViews.shortcodes_gui = new WPViews.ShortcodesGUI( $ );
});

var wpcfFieldsEditorCallback_redirect = null;

function wpcfFieldsEditorCallback_set_redirect(function_name, params) {
	wpcfFieldsEditorCallback_redirect = {'function' : function_name, 'params' : params};
}