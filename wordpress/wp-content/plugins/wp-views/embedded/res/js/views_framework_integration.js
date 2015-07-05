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
	
	$( document ).on('input cut paste', '.js-wpv-add-item-settings-form-newname', function( e ){
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
			var spinnerContainer = $( '<div class="spinner ajax-loader">' ).insertAfter( thiz ).show();
			thiz
				.removeClass( 'button-primary' )
				.addClass( 'button-secondary' )
				.prop( 'disabled', true );
			var data = {
				action: 'wpv_update_framework_integration_keys',
				csaction: 'add',
				cstarget: new_slug.val(),
				wpv_framework_integration_nonce: $( '#wpv_framework_integration_nonce' ).val()
			};
			$.ajax({
				async:false,
				type:"POST",
				url:ajaxurl,
				data:data,
				success:function( response ) {
					if ( ( typeof( response ) !== 'undefined' ) ) {
						if ( response == 'ok' ) {
							$( '.js-wpv-add-item-settings-list', parent_container )
								.append( '<li class="js-' + new_slug.val() + '-item"><span class="">' + new_slug.val() + '</span> <i class="icon-remove-sign js-wpv-framework-slug-delete" data-target="' + new_slug.val() + '"></i></li>' );
							new_slug.val( '' );
						} else {
							$( '.js-wpv-cs-ajaxfail', parent_form ).show();
							console.log( "Error: WordPress AJAX returned ", response );
						}
					} else {
						$( '.js-wpv-cs-ajaxfail', parent_form ).show();
						console.log( "Error: AJAX returned ", response );
					}
				},
				error: function ( ajaxContext ) {
					$( '.js-wpv-cs-ajaxfail', parent_form ).show();
					console.log( "Error: ", ajaxContext.responseText );
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
		spinnerContainer = $( '<div class="spinner ajax-loader">' ).insertAfter( self.button_declare_add ).show();
		var data = {
			action: 'wpv_update_framework_integration_keys',
			csaction: 'delete',
			cstarget: thiz,
			wpv_framework_integration_nonce: $( '#wpv_framework_integration_nonce' ).val()
		};
		$.ajax({
			async:false,
			type:"POST",
			url:ajaxurl,
			data:data,
			success:function( response ) {
				if ( ( typeof( response ) !== 'undefined' ) ) {
					if ( response == 'ok' ) {
						$( 'li.js-' + thiz + '-item', parent_container )
							.addClass( 'remove' )
							.fadeOut( 'fast', function() { 
								$( this ).remove(); 
							});
					} else {
						$( '.js-wpv-cs-ajaxfail', parent_container ).show();
						console.log( "Error: WordPress AJAX returned ", response );
					}
				} else {
					$( '.js-wpv-cs-ajaxfail', parent_container ).show();
					console.log( "Error: AJAX returned ", response );
				}
			},
			error: function ( ajaxContext ) {
				$( '.js-wpv-cs-ajaxfail', parent_container ).show();
				console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() {
				spinnerContainer.remove();
			}
		});
		return false;
	});
	
	self.init = function() {
		
	};
	
	self.init();

};

jQuery( document ).ready( function( $ ) {
    WPViews.framework_integration = new WPViews.FrameworkIntegration( $ );
});