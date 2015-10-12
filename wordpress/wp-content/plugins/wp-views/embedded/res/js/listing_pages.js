

var WPViews = WPViews || {};


WPViews.ListingPagesEmbedded = function( $ ) {

    var self = this;
	
	$( '#posts-filter' ).submit( function( e ) {
		e.preventDefault();
		var url_params = self.decodeURIParams( $( this ).serialize() );
		if (
			typeof( url_params['search'] ) !== 'undefined' 
			&& url_params['search'] == ''
		) {
			url_params['search'] = null;
		}
		self.navigateWithURIParams( url_params );
		return false;
	});

    /**
     * Reload a page with given URL parameters.
     *
     * @param array newParams Associative array with parameter names and their values.
     *
     * @since unknown
     */
    self.navigateWithURIParams = function( newParams ) {
        var newURI = self.encodeURIParams( $.extend( self.decodeURIParams(), newParams), true );
        var oldURI = window.location.search;

        window.location.search = newURI;

        /* Because if window.location.search == newURI and window.location.href ends with '#',
         * only assigning to window.location.search doesn't reload the page. */
        if( newURI == oldURI ) {
            window.location.reload( true );
        }
    };


    self.encodeURIParams = function( params, addQuestionMark ) {
        var pairs = [];
        for (var key in params) if (params.hasOwnProperty(key)) {
            var value = params[key];
            if (value != null) /* matches null and undefined */ {
                pairs.push(encodeURIComponent(key) + '=' + encodeURIComponent(value))
            }
        }
        if (pairs.length == 0)
            return '';
        return (addQuestionMark ? '?' : '') + pairs.join('&');
    };


    self.decodeURIParams = function( query ) {
        if (query == null)
            query = window.location.search;
        if (query[0] == '?')
            query = query.substring(1);

        var params = query.split('&');
        var result = {};
        for (var i = 0; i < params.length; i++) {
            var param = params[i];
            var pos = param.indexOf('=');
            if (pos >= 0) {
                var key = decodeURIComponent(param.substring(0, pos));
                var val = decodeURIComponent(param.substring(pos + 1));
                result[key] = val;
            } else {
                var key = decodeURIComponent(param);
                result[key] = true;
            }
        }
        return result;
    };


    // Change pagination items per page
    $(document).on( 'change', '.js-items-per-page', function() {
        self.navigateWithURIParams( self.decodeURIParams( 'paged=1&items_per_page=' + $(this).val() ) );
    });

    $(document).on( 'click', '.js-wpv-display-all-items', function( e ) {
        e.preventDefault();
        self.navigateWithURIParams( self.decodeURIParams( 'paged=1&items_per_page=-1' ) );
    });

    $(document).on( 'click', '.js-wpv-display-default-items', function( e ) {
        e.preventDefault();
        self.navigateWithURIParams( self.decodeURIParams( 'paged=1&items_per_page=20' ) );
    });


};


jQuery( document ).ready( function( $ ) {
    WPViews.listing_pages_embedded = new WPViews.ListingPagesEmbedded( $ );
});