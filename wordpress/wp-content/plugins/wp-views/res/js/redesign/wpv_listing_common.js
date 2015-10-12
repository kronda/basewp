jQuery(function($) {

	/* Please note that functions here are (and should be) defined in the form
	 *
	 *     functionName = function( ... ) { ... }
	 *
	 * because they are referenced from another function scopes.
	 * For more information see http://www.learningjquery.com/2006/09/multiple-document-ready/#comment-76559
	 */


	/**
	 * Recalculate current page number after removing some items from the current listing.
	 *
	 * Goes one page back when we've been on the last page and removed all items. Otherwise doesn't change the
	 * page number.
	 *
	 * @since 1.7
	 *
	 * @param array urlParams Array of URL parameters of current page.
	 * @param int affectedItemCount Amount of items that have been removed.
	 * @param int rowSelector Selector used to determine the amount of rows in a listing (number of items before
	 *     the action was performed). Defaults to '.js-wpv-view-list-row', a value for Views and WPA listing.
	 *
	 * @return int Updated page number.
	 */ 
	updatePagedParameter = function( urlParams, affectedItemCount, rowSelector ) {
		rowSelector = ( typeof rowSelector === 'undefined' ) ? '.js-wpv-view-list-row' : rowSelector;
		var currentPage = ( typeof( urlParams['paged'] ) == 'undefined' ) ? 1 : urlParams['paged'];
		var isLastPage = ( ( typeof( urlParams['last_page'] ) !== 'undefined' ) && ( urlParams['last_page'] == 1 ) );
		
		
		if( currentPage > 1 && isLastPage ) {
			// If there are more pages and we are on the last one
			var itemsOnPage = $( rowSelector ).length;
			if( affectedItemCount < itemsOnPage ) {
				// Less items have been affected (removed) than how many remain on the page. We will do nothing.
				return currentPage;
			} else {
				// All items on the page have been removed. Redirect to previous page.
				return currentPage - 1;
			}
		} else {
			return 1;
		}
	}


	/**
	 * Redirection functions for search, delete and duplicate.
	 *
	 * @since unknown
	 *
	 * @todo comment
	 */ 
	decodeURIParams = function( query ) {
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
		result['untrashed'] = null;
		result['trashed'] = null;
		result['deleted'] = null;
		return result;
	}


	/** TODO comment */
	encodeURIParams = function( params, addQuestionMark ) {
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
	}


	/**
	 * Reload a page with given URL parameters.
	 *
	 * @param array newParams Associative array with parameter names and their values.
	 *
	 * @since unknown
	 */ 
	navigateWithURIParams = function( newParams ) {
		var newURI = encodeURIParams($.extend(decodeURIParams(), newParams), true);
		var oldURI = window.location.search;

		window.location.search = newURI;

		/* Because if window.location.search == newURI and window.location.href ends with '#',
		 * only assigning to window.location.search doesn't reload the page. */
		if( newURI == oldURI ) {
			window.location.reload(true);
		}
	}


	/**
	 * Show a spiner on a listing page.
	 *
	 * @since 1.7
	 */
	showSpinner = function(selector) {
		selector = ( typeof selector === 'undefined' ) ? '.subsubsub' : selector;
		$(selector).append('<div class="wpv-spinner ajax-loader"></div>');
	}


	/**
	 * Show a spinner after a given element.
	 *
	 * @since 1.7
	 */ 
	showSpinnerAfter = function(what) {
		$('<div class="wpv-spinner ajax-loader">').insertAfter( what ).show();
	}


	/**
	 * Show a spinner before a given element.
	 *
	 * @since 1.7
	 */ 
	showSpinnerBefore = function(what) {
		$('<div class="wpv-spinner ajax-loader">').insertBefore( what ).show();
	}


	/**
	 * Hide a spinner from a listing page.
	 *
	 * @since 1.7
	 */
	hideSpinner = function() {
		$('.wpv-spinner.ajax-loader').hide();
	}


	/**
	 * Disable a given primary button.
	 *
	 * That also includes changing it to secondary button.
	 *
	 * @since 1.7
	 */ 
	disablePrimaryButton = function( btn ) {
		btn.prop( 'disabled', true ).addClass( 'button-secondary' ).removeClass( 'button-primary' );
	}


	/**
	 * Enable a button and make it primary.
	 *
	 * @since 1.7
	 */ 
	enablePrimaryButton = function( btn ) {
		btn.prop( 'disabled', false ).addClass( 'button-primary' ).removeClass( 'button-secondary' );
	}


	/**
	 * Trash Views of given IDs.
	 *
	 * Invokes wpv_view_bulk_change_status AJAX action, changing post status of given Views to 'trash'. On success it
	 * reloads current page with additional "trashed" URL parameter containing IDs of trashed Views separated by commas.
	 *
	 * This function is currently being used for WordPress Archives as well.
	 *
	 * @since 1.7
	 * 
	 * @param array viewIDs Array of View/WPA IDs that should be trashed.
	 * @param string nonce Nonce that will be passed to the AJAX action.
	 * @param bool clearArchives Whether to unassign given WPAs from archive loops. Default is false.
	 */ 
	trashViews = function( viewIDs, nonce, clearArchives ) {
		clearArchives = ( typeof clearArchives === 'undefined' ) ? false : clearArchives;
		
		var data = {
			action: 'wpv_view_bulk_change_status',
			ids: viewIDs,
			newstatus: 'trash',
			wpnonce: nonce,
			cleararchives: clearArchives ? 1 : 0
		};

		$.ajax({
			async: false,
			type: "POST",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				// response == 1 indicates success
				if ( ( typeof( response ) !== 'undefined') && ( response == 1 ) ) {
					var url_params = decodeURIParams();
					var affectedItemCount = viewIDs.length;
					url_params['paged'] = updatePagedParameter( url_params, affectedItemCount );
					url_params['trashed'] = affectedItemCount;
					url_params['affected'] = viewIDs;
					navigateWithURIParams( url_params );
				} else {
					console.log( "Error: AJAX returned ", response );
				}
			},
			error: function( ajaxContext ) {
				console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() {	}
		});
	}


	/**
	 * Restore Views of given IDs from trash.
	 *
	 * Invokes wpv_view_bulk_change_status AJAX action, changing post status of given Views to 'publish'. On success it
	 * reloads current page with additional "untrashed" URL parameter.
	 *
	 * This function is currently being used for WordPress Archives as well.
	 *
	 * @since 1.7
	 *
	 * @param array viewIDs Array of View IDs that should be trashed.
	 * @param nonce Nonce that will be passed to the AJAX action.
	 */ 
	untrashViews = function( viewIDs, nonce ) {

		var data = {
			action: 'wpv_view_bulk_change_status',
			ids: viewIDs,
			newstatus: 'publish',
			wpnonce : nonce
		};
		
		$.ajax({
			async: false,
			type: "POST",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				// response == 1 indicates success
				if ( (typeof(response) !== 'undefined') && ( response == 1 ) ) {
					// reload the page with "untrashed" message
					var url_params = decodeURIParams();
					var affectedItemCount = viewIDs.length;
					url_params['paged'] = updatePagedParameter( url_params, affectedItemCount );
					url_params['untrashed'] = affectedItemCount;
					navigateWithURIParams( url_params );
				} else {
					console.log( "Error: AJAX returned ", response );
				}
			},
			error: function( ajaxContext ) {
				console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() { }
		});
	}

});