/**
* views_framework_integration.js
*
* Contains helper functions for the Views framework integration settings page
*
* @since 1.8.0
* @package Views
*/

var WPViews = WPViews || {};

WPViews.FrameworkIntegration = function( $ ) {
	
	var self = this;
	self.button_declare_add = $( '.js-wpv-framework-integration-form-add .js-wpv-add-item-settings-form-button' );
	self.slug_pattern = /^[a-z0-9\-\_]+$/;
	
	self.auto_detected_selected = $( '.js-wpv-framework-auto:checked' ).val();
	self.auto_detected_save_button = $( '#js-wpv-framework-auto-save' );
	
	$( document ).on('input cut paste', '.js-wpv-add-item-settings-form-newname', function( e ) {
		var parent_form = $( this ).closest( '.js-wpv-add-item-settings-form' );
		$( '.js-wpv-cs-error, .js-wpv-cs-dup, .js-wpv-cs-ajaxfail', parent_form ).hide();
		if ( $( this ).val() != '' ) {
			self.button_declare_add
				.addClass( 'button-primary' )
				.removeClass( 'button-secondary' )
				.prop( 'disabled', false );
		} else {
			self.button_declare_add
				.removeClass( 'button-primary' )
				.addClass( 'button-secondary' )
				.prop( 'disabled', true );
		}
	});
	
	$( '.js-wpv-add-item-settings-form' ).submit( function( e ) {
		var thiz = $( this );
		e.preventDefault();
		self.button_declare_add.click();
		return false;
	});
	
	$( '.js-wpv-framework-slug-add' ).on( 'click', function( e ) {
		e.preventDefault();
		var thiz = $( this ),
		parent_form = $( this ).closest( '.js-wpv-add-item-settings-form' ),
		parent_container = $( this ).closest( '.js-wpv-add-item-settings-wrapper' ),
		new_slug = $( '.js-wpv-add-item-settings-form-newname', parent_form );
		$( '.js-wpv-cs-error, .js-wpv-cs-dup, .js-wpv-cs-ajaxfail', parent_form ).hide();
		if ( self.slug_pattern.test( new_slug.val() ) == false ) {
			$( '.js-wpv-cs-error', parent_form ).show();
		} else if ( $( '.js-' + new_slug.val() + '-item', parent_container ).length > 0 ) {
			$( '.js-wpv-cs-dup', parent_form ).show();
		} else {
			var spinnerContainer = $( '<div class="wpv-spinner ajax-loader">' ).insertAfter( thiz ).show();
			thiz
				.removeClass( 'button-primary' )
				.addClass( 'button-secondary' )
				.prop( 'disabled', true );
			var data = {
				action: 'wpv_update_framework_integration_keys',
				update_action: 'add',
				update_tag: new_slug.val(),
				wpv_framework_integration_nonce: $( '#wpv_framework_integration_nonce' ).val()
			};
			$.ajax({
				async:false,
				type:"POST",
				dataType: "json",
				url:ajaxurl,
				data:data,
				success:function( response ) {
					if ( response.success ) {
						$( '.js-wpv-add-item-settings-list', parent_container )
							.append( '<li class="js-' + new_slug.val() + '-item"><span class="">' + new_slug.val() + '</span> <i class="icon-remove-sign js-wpv-framework-slug-delete" data-target="' + new_slug.val() + '"></i></li>' );
						new_slug.val( '' );
					} else {
						$( '.js-wpv-cs-ajaxfail', parent_form ).show();
					}
				},
				error: function ( ajaxContext ) {
					$( '.js-wpv-cs-ajaxfail', parent_form ).show();
				},
				complete: function() {
					spinnerContainer.remove();
				}
			});
		}
		return false;
	});
	
	// Delete additional inner shortcodes

	$( document ).on( 'click', '.js-wpv-framework-slug-delete', function( e ) {
		e.preventDefault();
		var thiz = $( this ).data( 'target' ),
		parent_container = $( this ).closest( '.js-wpv-add-item-settings-wrapper' ),
		spinnerContainer = $( '<div class="wpv-spinner ajax-loader">' ).insertAfter( self.button_declare_add ).show();
		var data = {
			action: 'wpv_update_framework_integration_keys',
			update_action: 'delete',
			update_tag: thiz,
			wpv_framework_integration_nonce: $( '#wpv_framework_integration_nonce' ).val()
		};
		$.ajax({
			async:false,
			type:"POST",
			dataType: "json",
			url:ajaxurl,
			data:data,
			success:function( response ) {
				if ( response.success ) {
					$( 'li.js-' + thiz + '-item', parent_container )
						.addClass( 'remove' )
						.fadeOut( 'fast', function() { 
							$( this ).remove(); 
						});
				} else {
					$( '.js-wpv-cs-ajaxfail', parent_container ).show();
				}
			},
			error: function ( ajaxContext ) {
				$( '.js-wpv-cs-ajaxfail', parent_container ).show();
			},
			complete: function() {
				spinnerContainer.remove();
			}
		});
		return false;
	});
	
	$( document ).on( 'change', '.js-wpv-framework-auto', function() {
		self.manage_auto_detect_change();
	});
	
	self.manage_auto_detect_change = function() {
		var thiz = $( '.js-wpv-framework-auto:checked' ),
		message_container = $( '.js-wpv-framework-auto-detect-selection .js-wpv-message-container' );
		if ( thiz.length < 1 ) {
			return;
		}
		if ( thiz.val() != self.auto_detected_selected ) {
			self.auto_detected_save_button
				.addClass( 'button-primary' )
				.removeClass( 'button-secondary' )
				.prop( 'disabled', false );
			if ( '' != self.auto_detected_selected ) {
				message_container
					.wpvToolsetMessage({
						text: views_framework_integration_texts.warning_change,
						type: 'error',
						inline: true,
						stay: true
					});
			}
		} else {
			self.auto_detected_save_button
				.addClass( 'button-secondary' )
				.removeClass( 'button-primary' )
				.prop( 'disabled', true );
			message_container.html( '' );
		}
	};
	
	self.auto_detected_save_button.on( 'click', function() {
		var thiz = $( '.js-wpv-framework-auto:checked' );
		if ( thiz.length < 1 ) {
			return;
		}
		spinnerContainer = $( '<div class="wpv-spinner ajax-loader">' ).insertAfter( self.auto_detected_save_button ).show();
		self.auto_detected_save_button
			.addClass( 'button-secondary' )
			.removeClass( 'button-primary' )
			.prop( 'disabled', true );
		var data = {
			action: 'wpv_register_auto_detected_framework',
			framework: thiz.val(),
			wpv_framework_integration_nonce: $( '#wpv_framework_integration_nonce' ).val()
		};
		$.ajax({
			async:false,
			type:"POST",
			dataType: "json",
			url:ajaxurl,
			data:data,
			success:function( response ) {
				if ( response.success ) {
					self.auto_detected_selected = thiz.val();
					window.location.reload(true);
					/*
					$( '.wpv-register-framework-automatically-response' )
						.fadeOut( 'fast' )
						.html( response.data.management_structure )
						.fadeIn( 'fast' );
					*/
				} else {
					
				}
			},
			error: function ( ajaxContext ) {
				self.auto_detected_save_button
					.addClass( 'button-primary' )
					.removeClass( 'button-secondary' )
					.prop( 'disabled', false );
			},
			complete: function() {
				
			}
		});
	});
	
	self.init = function() {
		
	};
	
	self.init();

};

jQuery( document ).ready( function( $ ) {
    WPViews.framework_integration = new WPViews.FrameworkIntegration( $ );
});