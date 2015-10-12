/*
*
*/

jQuery(document).on( 'click', '.js-views-scan', function(e) {
	e.preventDefault();
	var thiz = jQuery( this ),
	spinnerContainer = jQuery( '<div class="wpv-spinner ajax-loader">' ).insertBefore( thiz ).show(),
	resultsContainer = jQuery( '.js-wpv-views-scan-results' ),
    data = {
		action: 'wpv_scan_wpv_if',
		wpnonce : jQuery( '#views_update_help_wpv_if_nonce' ).val()
	};
	thiz.removeClass( 'button-primary' ).addClass( 'button-secondary' ).prop( 'disabled', true );
	jQuery.post( ajaxurl, data, function( response ) {
		if ( ( typeof( response ) !== 'undefined' ) ) {
			decoded_response = jQuery.parseJSON( response );
			var postsList = jQuery( '<ul class="posts-list">' );
            postsList.appendTo( resultsContainer );
			jQuery.each( decoded_response, function( index, value ) {
				jQuery.each( value, function( subindex, link ) {
					jQuery('<li>' + index + ': ' + link + '</li>').appendTo( postsList );
				});
			});
		} else {
			//if(  WPV_Parametric.debug ) console.log( WPV_Parametric.ajax_error, response );
		}
	})
	.fail(function(jqXHR, textStatus, errorThrown) {
		//if(  WPV_Parametric.debug ) console.log( WPV_Parametric.error_generic, textStatus, errorThrown );
	})
	.always(function() {
		spinnerContainer.remove();
		thiz.addClass( 'button-primary' ).removeClass( 'button-secondary' ).prop( 'disabled', false );
	});
});