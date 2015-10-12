var WPViews = WPViews || {};

var wpv_stop_rollover = {};
window.wpvPaginationAjaxLoaded = {};
window.wpvPaginationAnimationFinished = {};
window.wpvPaginationQueue = {};

// ------------------------------------
// Clone
// ------------------------------------

// Textarea and select clone() bug workaround | Spencer Tipping
// Licensed under the terms of the MIT source code license
// Motivation.
// jQuery's clone() method works in most cases, but it fails to copy the value of textareas and select elements. This patch replaces jQuery's clone() method with a wrapper that fills in the
// values after the fact.
// An interesting error case submitted by Piotr Przybyl: If two <select> options had the same value, the clone() method would select the wrong one in the cloned box. The fix, suggested by Piotr
// and implemented here, is to use the selectedIndex property on the <select> box itself rather than relying on jQuery's value-based val().

;(function (original) {
	jQuery.fn.clone = function () {
		var result = original.apply(this, arguments),
		my_textareas = this.find('textarea').add(this.filter('textarea')),
		result_textareas = result.find('textarea').add(result.filter('textarea')),
		my_selects = this.find('select').add(this.filter('select')),
		result_selects = result.find('select').add(result.filter('select'));
		for (var i = 0, l = my_textareas.length; i < l; ++i) {
			$(result_textareas[i]).val($(my_textareas[i]).val());
		}
		for (var i = 0, l = my_selects.length; i < l; ++i) {
			for (var j = 0, m = my_selects[i].options.length; j < m; ++j) {
				if (my_selects[i].options[j].selected === true) {
					result_selects[i].options[j].selected = true;
				} else {
					result_selects[i].options[j].selected = false;
				}
			}
		}
		return result;
	};
})(jQuery.fn.clone);

// ------------------------------------
// Rollover
// ------------------------------------

jQuery.fn.wpvRollover = function() {
	var args = arguments[0] || {id: 1, effect: "fade", speed: 5, page: 1, count: 1},
	id = args.id,
	effect = args.effect,
	speed = args.speed*1000,
	page = args.page,
	count = args.count,
	cache_pages = args.cache_pages,
	preload_pages = args.preload_pages,
	spinner = args.spinner,
	spinner_image = args.spinner_image,
	callback_next = args.callback_next,
	wpvInfiniteLoop;
	if ( count > 1 ) {
		if ( window.wpvPaginationAjaxLoaded.hasOwnProperty( id ) && window.wpvPaginationAjaxLoaded[id] === false ) {
			setTimeout( 'jQuery(this).wpvRollover({id: "' + id + '", effect:\'' + effect + '\', speed:' + ( speed/1000 ) + ', page:' + page + ', count:' + count + ', cache_pages:' + cache_pages + ', preload_pages:' + preload_pages + ', spinner:\'' + spinner + '\', spinner_image:\'' + spinner_image + '\', callback_next:\'' + callback_next + '\'})', 100 );
			return false;
		}
		window.wpvPaginationAjaxLoaded[id] = false;
		wpvInfiniteLoop = setTimeout( function() {
			if ( effect === 'slideright' || effect === 'slidedown' ) {
				if ( page <= 1 ) {
					page = count;
				} else {
					page--;
				}
			} else {
				if ( page === count ) {
					page = 1;
				} else {
					page++;
				}
			}
			if ( !wpv_stop_rollover.hasOwnProperty( id ) ) {
				WPViews.view_pagination.pagination_replace_view( id, page, true, effect, count, cache_pages, preload_pages, spinner, spinner_image, callback_next, false );
				jQuery( this ).wpvRollover( {
					id:id,
					effect:effect,
					speed:speed/1000,
					page:page,
					count:count,
					cache_pages:cache_pages,
					preload_pages:preload_pages,
					spinner:spinner,
					spinner_image:spinner_image,
					callback_next:callback_next
				} );
			}
		}, speed);
	}
};


////////////////////////////////////////////////////
// Table sorting head click
////////////////////////////////////////////////////

// TODO create a table sorting object to wrap all related code

jQuery( document ).on( 'click', '.js-wpv-column-header-click', function( e ) {
	e.preventDefault();
	var thiz = jQuery( this ),
	view_number = thiz.data( 'viewnumber' ),
	name = thiz.data( 'name' ),
	direction = thiz.data( 'direction' ),
	innerthis;
	jQuery( 'form[name="wpv-filter-' + view_number + '"]' ).each( function() {
		innerthis = jQuery( this );
		innerthis.find( '#wpv_column_sort_id' ).val( name );
		innerthis.find( '#wpv_column_sort_dir' ).val( direction );
		WPViews.view_frontend_utils.add_url_controls_for_column_sort( innerthis );
	});
	jQuery( 'form[name="wpv-filter-' + view_number + '"]' ).submit();
	return false;
});

WPViews.ViewFrontendUtils = function( $ ) {
	
	// ------------------------------------
	// Constants and variables
	// ------------------------------------
	
	var self = this;
	
	// ------------------------------------
	// Methods
	// ------------------------------------
	
	/**
	* extract_url_query_parameters
	*
	* Extracts parameters from a query string, managing arrays, and returns an array of pairs key => value
	*
	* @param string query_string
	*
	* @return array
	*
	* @note ##URLARRAYVALHACK## is a hacky constant
	*
	* @uses decodeURIComponent
	*
	* @since 1.9.0
	*/
	
	self.extract_url_query_parameters = function( query_string ) {
		var query_string_pairs = {};
		if ( query_string == "" ) {
			return query_string_pairs;
		}
		var query_string_split = query_string.split( '&' ),
		query_string_split_length = query_string_split.length;
		for ( var i = 0; i < query_string_split_length; ++i ) {
			var qs_part = query_string_split[i].split( '=' );
			if ( qs_part.length != 2 ) {
				continue;
			};
			var thiz_key = qs_part[0],
			thiz_val = decodeURIComponent( qs_part[1].replace( /\+/g, " " ) );
			// Adjust thiz_key to work with POSTed arrays
			thiz_key = thiz_key.replace( "[]", "" );
			thiz_key = thiz_key.replace( "%5B%5D", "" );
			if ( query_string_pairs.hasOwnProperty( thiz_key ) ) {
				if ( query_string_pairs[thiz_key] != thiz_val ) {
					// @hack alert!! WE can not avoid using this :-(
					query_string_pairs[thiz_key] += '##URLARRAYVALHACK##' + thiz_val;
				} else {
					query_string_pairs[thiz_key] = thiz_val;
				}
			} else {
				query_string_pairs[thiz_key] = thiz_val;
			}
		}
		return query_string_pairs;
	};
	
	/**
	* add_url_query_parameters
	*
	* Adds the current URL query parameters to the data array, on the get_params key
	*
	* @param array data
	*
	* @return array
	*
	* @uses self.extract_url_query_parameters
	*
	* @since 1.9.0
	*/

	self.add_url_query_parameters = function( data ) {
		var query_s = self.extract_url_query_parameters( window.location.search.substr( 1 ) );
		data['get_params'] = {};
		for ( var prop in query_s ) {
			if ( 
				query_s.hasOwnProperty( prop ) 
				&& ! data.hasOwnProperty( prop )
			) {
				data['get_params'][prop] = query_s[prop];
			}
		}
		return data;
	};
	
	/**
	* add_url_controls_for_column_sort
	*
	* @param object form
	*
	* @since 1.9
	*/
	
	self.add_url_controls_for_column_sort = function( form ) {
		var data = {},
		data = self.add_url_query_parameters( data );
		$.each( data['get_params'], function( key, value ) {
			if ( form.find( '[name=' + key + '], [name=' + key + '\\[\\]]' ).length === 0 ) {
				// @hack alert!! WE can not avoid this :-(
				var pieces = value.split( '##URLARRAYVALHACK##' ),
				pieces_length = pieces.length;
				if ( pieces_length < 2 ) {
					$( '<input>' ).attr({
						type: 'hidden',
						name: key,
						value: value
					})
					.appendTo( form );
				} else {
					for ( var iter = 0; iter < pieces_length; iter++ ) {
						$( '<input>' ).attr({
							type: 'hidden',
							name: key + "[]",
							value: pieces[iter]
						})
						.appendTo( form );
					}
				}
			}
		});
	};
	
	/**
	* utf8_encode
	*
	* @param string argString
	*
	* @return string
	*
	* @since 1.9.0
	*
	* @author Webtoolkit.info (http://www.webtoolkit.info/)
	* @improved Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	* @improved sowberry
	* @tweaked Jack
	* @bugfixed Onno Marsman
	* @improved Yves Sucaet
	* @bugfixed Onno Marsman
	* @bugfixed Ulrich
	* @bugfixed Rafal Kukawski
	* @improved kirilloid
	*/
	
	self.utf8_encode = function( argString ) {
		if (
			argString === null 
			|| typeof argString === "undefined"
		) {
			return "";
		}
		var string = ( argString + '' ),
		utftext = '',
		start = 0, 
		end = 0, 
		stringl = string.length;
		for ( var n = 0; n < stringl; n++ ) {
			var c1 = string.charCodeAt( n ),
			enc = null;
			if ( c1 < 128 ) {
				end++;
			} else if ( c1 > 127 && c1 < 2048 ) {
				enc = String.fromCharCode( ( c1 >> 6 ) | 192, ( c1 & 63 ) | 128 );
			} else {
				enc = String.fromCharCode( ( c1 >> 12 ) | 224, ( ( c1 >> 6 ) & 63 ) | 128, ( c1 & 63 ) | 128 );
			}
			if ( enc !== null ) {
				if ( end > start ) {
					utftext += string.slice( start, end );
				}
				utftext += enc;
				start = end = n + 1;
			}
		}
		if ( end > start ) {
			utftext += string.slice( start, stringl );
		}
		return utftext;
	};
	
	/**
	* encodeToHex
	*
	* Converts the given data structure to a JSON string.
	*
	* @param string str
	*
	* @return string
	*
	* @since 1.9.0
	*/
	
	self.encodeToHex = function( str ) {
		var r="",
		e = str.length,
		c = 0,
		h;
		while( c < e ) {
			h = str.charCodeAt( c++ ).toString( 16 );
			while( h.length < 2 ) {
				h= "0" + h;
			}
			r += h;
		}
		return r;
	};
	
	/**
	* array2json
	*
	* Converts the given data structure to a JSON string
	*
	* @param array arr
	*
	* @return string
	*
	* @since 1.9.0
	*
	* @uses self.array2json
	*
	* @url http://www.openjs.com/scripts/data/json_encode.php
	*
	* @example var json_string = array2json(['e', {pluribus: 'unum'}]);
	* @example var json = array2json({"success":"Sweet","failure":false,"empty_array":[],"numbers":[1,2,3],"info":{"name":"Binny","site":"http:\/\/www.openjs.com\/"}});
	*/
	self.array2json = function( arr ) {
		var parts = [],
		is_list = ( Object.prototype.toString.apply( arr ) === '[object Array]' );
		for( var key in arr ) {
			var value = arr[key];
			if ( typeof value == "object" ) { //Custom handling for arrays
				if ( is_list ) {
					parts.push( self.array2json( value ) ); /* :RECURSION: */
				} else {
					parts.push( '"' + key + '":' + self.array2json( value ) ); /* :RECURSION: */
				}
			} else {
				var str = "";
				if ( ! is_list ) {
					str = '"' + key + '":';
				}
				//Custom handling for multiple data types
				if ( typeof value == "number" ) {
					str += value; //Numbers
				} else if ( value === false ) {
					str += 'false'; //The booleans
				} else if ( value === true ) {
					str += 'true';
				} else {
					str += '"' + self.utf8_encode( value ) + '"'; //All other things
				}
				// :TODO: Is there any more datatype we should be in the lookout for? (Functions?)
				parts.push( str );
			}
		}
		var json = parts.join( "," );
		if ( is_list ) {
			return '[' + json + ']';//Return numerical JSON
		}
		return '{' + json + '}';//Return associative JSON
	};
	
	/**
	* serialize_array
	*
	* @param array data
	*
	* @return string
	*
	* @since 1.9.0
	*
	* @uses self.encodeToHex
	* @uses self.array2json
	*/
	
	self.serialize_array = function( data ) {
		return self.encodeToHex( self.array2json( data ) );
	};
	
	/**
	* render_frontend_datepicker
	*
	* Adds a datepicker to a selector but only if it has not been added before.
	*
	* Fired on document.ready, after AJAX pagination and after AJAX parametric search events.
	*
	* @since 1.9
	*/
	
	self.render_frontend_datepicker = function() {
		$( '.js-wpv-frontend-datepicker:not(.js-wpv-frontend-datepicker-inited)' ).each( function() {
			var thiz = $( this );
			thiz
				.addClass( 'js-wpv-frontend-datepicker-inited' )
				.datepicker({
					onSelect: function( dateText, inst ) {
						var url_param = thiz.data( 'param' ),
						data = 'date=' + dateText,
						form = thiz.closest( 'form' );
						data += '&date-format=' + $( '.js-wpv-date-param-' + url_param + '-format' ).val();
						data += '&action=wpv_format_date';
						$.post( wpv_pagination_local.front_ajaxurl, data, function( response ) {
							response = $.parseJSON( response );
							form.find('.js-wpv-date-param-' + url_param ).html( response['display'] );
							form.find('.js-wpv-date-front-end-clear-' + url_param ).show();
							form.find('.js-wpv-date-param-' + url_param + '-value' ).val( response['timestamp'] ).trigger( 'change' );
						});
					},
					dateFormat: 'ddmmyy',
					minDate: wpv_pagination_local.datepicker_min_date,
					maxDate: wpv_pagination_local.datepicker_max_date,
					showOn: "button",
					buttonImage: wpv_pagination_local.calendar_image,
					buttonText: wpv_pagination_local.calendar_text,
					buttonImageOnly: true,
					changeMonth: true,
					changeYear: true
				});
		});
	};
	
	/**
	* clone_form
	*
	* Clones a form using the fixed clone() method that covers select and textarea elements
	*
	* @param object fil
	* @param array targets
	*
	* @since 1.9
	*/
	
	self.clone_form = function( fil, targets ) {
		var cloned = fil.clone();
		targets.each( function() {
			$( this ).replaceWith( cloned );
		});
	};
	
	/**
	* render_frontend_media_shortcodes
	*
	* Render the WordPress media players for items inside a container.
	*
	* @param object container
	*
	* @since 1.9
	*/
	
	self.render_frontend_media_shortcodes = function( container ) {
		container.find( '.wp-audio-shortcode, .wp-video-shortcode' ).each( function() {
			var thiz = $( this );
			thiz.mediaelementplayer();
		});
		container.find( '.wp-playlist' ).each( function() {
			var thiz = $( this );
			return new WPPlaylistView({ el: this });
		});
	};
	
	// ------------------------------------
	// Events
	// ------------------------------------
	
	/**
	* Window resize event
	*
	* Make Views layouts responsive
	*
	* @since 1.9
	*/
	
	$( window ).resize( function() {
		var thiz,
		width;
		$( '.js-wpv-layout-responsive' ).each( function() {
			thiz = $( this );
			width = thiz.parent().width();
			thiz.css( 'width', width );
		});
	});
	
	// ------------------------------------
	// Init
	// ------------------------------------
	
	self.init = function() {
		self.render_frontend_datepicker();
	};
	
	self.init();

};

WPViews.ViewPagination = function( $ ) {
	
	// ------------------------------------
	// Constants and variables
	// ------------------------------------
	
	var self = this;
	
	self.did_stop_rollover = {};
	self.pagination_queue = {};
	
	/*
	PATTERN TO SET DEFAULTS; useful if I pla to pass objects as in pagination_replace_view_links instead a bunch of variables
	var defaults = { validate: false, limit: 5, name: "foo" };
	var options = { validate: true, name: "bar" };
	 
	// Merge defaults and options, without modifying defaults
	var settings = $.extend( {}, defaults, options );
	*/
	
	// ------------------------------------
	// Methods
	// ------------------------------------
	
	/**
	* get_ajax_pagination_url
	*
	* Build the pagination URL to get the given page based on data
	*
	* @param object data
	*
	* @since 1.9
	*/
	
	self.get_ajax_pagination_url = function( data ) {
		var url;
		if ( wpv_pagination_local.ajax_pagination_url.slice( -'.php'.length ) === '.php' ) {
			url = wpv_pagination_local.ajax_pagination_url + '?wpv-ajax-pagination=' + WPViews.view_frontend_utils.serialize_array( data );
		} else {
			url = wpv_pagination_local.ajax_pagination_url + WPViews.view_frontend_utils.serialize_array( data );
		}
		return url;
	};
	
	/**
	* add_view_parameters
	*
	* Add several information to the data used to get pagination pages.
	* For example, add column sorting data, parametric search data and parent View data.
	*
	* @since 1.9
	*/
	
	self.add_view_parameters = function( data, page, view_number ) {
		data['action'] = 'wpv_get_page';
		data['page'] = page;
		data['view_number'] = view_number;
		var this_form = $( 'form.js-wpv-filter-form-' + view_number );
		data['wpv_column_sort_id'] = this_form.find( 'input[name=wpv_column_sort_id]' ).val();
		data['wpv_column_sort_dir'] = this_form.find( 'input[name=wpv_column_sort_dir]' ).val();
		data['wpv_view_widget_id'] = $( '#wpv_widget_view-' + view_number ).val();
		data['view_hash'] = $( '#wpv_view_hash-' + view_number ).val();
		if ( this_form.find( 'input[name=wpv_post_id]' ).length > 0 ) {
			data['post_id'] = this_form.find( 'input[name=wpv_post_id]' ).val();
		}
		if ( this_form.find( 'input[name=wpv_aux_parent_term_id]' ).length > 0 ) {
			data['wpv_aux_parent_term_id'] = this_form.find( 'input[name=wpv_aux_parent_term_id]' ).val();
		}
		if ( this_form.find( 'input[name=wpv_aux_parent_user_id]' ).length > 0 ) {
			data['wpv_aux_parent_user_id'] = this_form.find( 'input[name=wpv_aux_parent_user_id]' ).val();
		}
		data['dps_pr'] = {};
		data['dps_general'] = {};
		var this_prelements = this_form.find( '.js-wpv-post-relationship-update' );
		if ( this_prelements.length ) {
			data['dps_pr'] = this_prelements.serializeArray();
		}
		if ( this_form.hasClass( 'js-wpv-dps-enabled' ) || this_form.hasClass( 'js-wpv-ajax-results-enabled' ) ) {
			data['dps_general'] = this_form.find( '.js-wpv-filter-trigger, .js-wpv-filter-trigger-delayed' ).serializeArray();
		}
		return data;
	};
	
	/**
	* pagination_init_preload_images
	*
	* Init-preload images.
	*
	* @since 1.9
	*/
	
	self.pagination_init_preload_images = function() {
		$( '.wpv-pagination-preload-images' ).each( function() {
			var preloadedImages = [],
			element = $( this ),
			images = element.find( 'img' );
			if ( images.length < 1 ) {
				element.css( 'visibility', 'visible' );
			} else {
				images.one( 'load', function() {
					preloadedImages.push( $( this ).attr( 'src' ) );
					if ( preloadedImages.length === images.length ) {
						element.css( 'visibility', 'visible' );
					}
				}).each( function() {
					if( this.complete ) {
						$( this ).load();
					}
				});
				setTimeout( function() {
					element.css( 'visibility', 'visible' );
				}, 3000 );
			}
		});
	};
	
	/**
	* pagination_preload_pages
	*
	* Preload pages to a reach.
	*
	* @param
	*
	* @since 1.9
	*/
	
	self.pagination_preload_pages = function( view_number, page, max_pages, cache_pages, preload_pages, reach_max ) {
		page = parseInt( page, 10 );
		max_pages = parseInt( max_pages, 10 );
		reach_max = parseInt( reach_max, 10 );
		if ( preload_pages ) {
			var reach = 1;
			while ( reach < reach_max ) {
				self.pagination_load_next_page( view_number, page, max_pages, reach );
				self.pagination_load_previous_page( view_number, page, max_pages, reach );
				reach++;
			}
		}
		if ( cache_pages ) {
			self.pagination_cache_current_page( view_number, page );
		}
	};
	
	/**
	* pagination_cache_current_page
	*
	* Cache current page.
	*
	* @param string view_number
	* @param int page
	*
	* @since 1.9
	*/
	
	self.pagination_cache_current_page = function( view_number, page ) {
		window.wpvCachedPages = ( typeof window.wpvCachedPages == 'undefined' ) ? [] : window.wpvCachedPages;
		window.wpvCachedImages = ( typeof window.wpvCachedImages == 'undefined' ) ? [] : window.wpvCachedImages;
		window.wpvCachedPages[view_number] = ( typeof window.wpvCachedPages[view_number] == 'undefined' ) ? [] : window.wpvCachedPages[view_number];
		var dataCurrent = {},
		content;
		icl_lang = ( typeof icl_lang == 'undefined' ) ? false : icl_lang;
		if ( ! window.wpvCachedPages[view_number].hasOwnProperty( page ) ) {
			dataCurrent = self.add_view_parameters( dataCurrent, page, view_number );
			dataCurrent = WPViews.view_frontend_utils.add_url_query_parameters( dataCurrent );
			if ( icl_lang !== false ) {
				dataCurrent['lang'] = icl_lang;
			}
			$.get( self.get_ajax_pagination_url( dataCurrent ), '', function( response ) {
				window.wpvCachedPages[view_number][page] = response;
				content = $( response ).find( 'img' );
				content.each( function() {
					window.wpvCachedImages.push( this.src );
				});
			});
		}
	};
	
	/**
	* pagination_load_next_page
	*
	* Load the next page, or the next one counting "reach" pages.
	*
	* @param string view_number
	* @param int page
	* @param int max_pages
	* @param int reach
	*
	* @since 1.9
	*/
	
	self.pagination_load_next_page = function( view_number, page, max_pages, reach ) {
		window.wpvCachedPages = ( typeof window.wpvCachedPages == 'undefined' ) ? [] : window.wpvCachedPages;
		window.wpvCachedImages = ( typeof window.wpvCachedImages == 'undefined' ) ? [] : window.wpvCachedImages;
		window.wpvCachedPages[view_number] = ( typeof window.wpvCachedPages[view_number] == 'undefined' ) ? [] : window.wpvCachedPages[view_number];
		var next_page = page + reach;
		icl_lang = ( typeof icl_lang == 'undefined' ) ? false : icl_lang;
		if ( ! window.wpvCachedPages[view_number].hasOwnProperty( next_page ) ) {
			if ( ( next_page - 1 ) < max_pages ) {
				var dataNext = {};
				dataNext = self.add_view_parameters( dataNext, next_page, view_number );
				dataNext = WPViews.view_frontend_utils.add_url_query_parameters( dataNext );
				if ( icl_lang !== false ) {
					dataNext['lang'] = icl_lang;
				}
				$.get( self.get_ajax_pagination_url( dataNext ), '', function( response ) {
					window.wpvCachedPages[view_number][next_page] = response;
					var content = $( response ).find( 'img' );
					content.each( function() {
						window.wpvCachedImages.push( this.src );
					});
				});
			}
		}
	};
	
	/**
	* pagination_load_previous_page
	*
	* Load the previous page, or the previous one counting "reach" pages.
	*
	* @param string view_number
	* @param int page
	* @param int max_pages
	* @param int reach
	*
	* @since 1.9
	*/
	
	self.pagination_load_previous_page = function(view_number, page, max_pages, reach) {
		window.wpvCachedPages = ( typeof window.wpvCachedPages == 'undefined' ) ? [] : window.wpvCachedPages;
		window.wpvCachedImages = ( typeof window.wpvCachedImages == 'undefined' ) ? [] : window.wpvCachedImages;
		window.wpvCachedPages[view_number] = ( typeof window.wpvCachedPages[view_number] == 'undefined' ) ? [] : window.wpvCachedPages[view_number];
		var previous_page = page - reach,
		dataPrevious = {},
		content;
		icl_lang = ( typeof icl_lang == 'undefined' ) ? false : icl_lang;
		if ( ! window.wpvCachedPages[view_number].hasOwnProperty( previous_page ) ) {
			// LOAD PREVIOUS
			if ( ( previous_page + 1 ) > 1 ) {
				dataPrevious = self.add_view_parameters( dataPrevious, previous_page, view_number );
				dataPrevious = WPViews.view_frontend_utils.add_url_query_parameters( dataPrevious );
				if ( icl_lang !== false ) {
					dataPrevious['lang'] = icl_lang;
				}
				$.get( self.get_ajax_pagination_url( dataPrevious ), '', function( response ) {
				window.wpvCachedPages[view_number][previous_page] = response;
				content = $( response ).find( 'img' );
					content.each( function() {
						window.wpvCachedImages.push( this.src );
					});
				});
			} else if ( (previous_page + 1 ) === 1 ) { // LOAD LAST PAGE IF ON FIRST PAGE
				dataPrevious = self.add_view_parameters( dataPrevious, max_pages, view_number );
				dataPrevious = WPViews.view_frontend_utils.add_url_query_parameters( dataPrevious );
				if ( icl_lang !== false ) {
					dataPrevious['lang'] = icl_lang;
				}
				$.get( self.get_ajax_pagination_url( dataPrevious ), '', function( response ) {
					window.wpvCachedPages[view_number][max_pages] = response;
					window.wpvCachedPages[view_number][0] = response;
					content = $( response ).find( 'img' );
					content.each( function() {
						window.wpvCachedImages.push( this.src );
					});
				});
			}
		}
	};
	
	/**
	* pagination_get_page
	*
	* Replace the layout and form with the new page, and init all related data.
	*
	* @param string view_number
	* @param bool next
	* @param string effect
	* @param int speed
	* @param string response
	* @param object wpvPaginatorLayout
	* @param object wpvPaginatorFilter
	* @param string callback_next
	*
	* @since 1.9
	*/
	
	self.pagination_get_page = function( view_number, next, effect, speed, response, wpvPaginatorLayout, wpvPaginatorFilter, callback_next ) {
		var width = wpvPaginatorLayout.width(),
		outer_width = wpvPaginatorLayout.outerWidth(),
		height = wpvPaginatorLayout.height(),
		outer_height = wpvPaginatorLayout.outerHeight(),
		responseObj = $( '<div></div>' ).append( response ),
		responseView = responseObj.find( '#wpv-view-layout-' + view_number ),
		responseFilter = responseObj.find( 'form[name=wpv-filter-' + view_number + ']' ).html(),
		preloadedImages,
		images,
		parent;
		wpvPaginatorLayout.attr( 'id', 'wpv-view-layout-' + view_number + '-response' ).wrap( '<div class="wpv_slide_remove" style="width:' + outer_width + 'px;height:' + outer_height + 'px;overflow:hidden;" />' ).css( 'width', width );
		responseView.attr( 'id', 'wpv-view-layout-' + view_number ).css( {'visibility': 'hidden', 'width': width} );
		if ( wpvPaginatorLayout.hasClass( 'wpv-pagination-preload-images' ) ) {
			preloadedImages = [];
			images = responseView.find( 'img' );
			if ( images.length < 1 ) {
				self.pagination_slide( view_number, width, height, next, effect, speed, responseView, wpvPaginatorLayout, wpvPaginatorFilter, callback_next );
			} else {
				images.one( 'load', function() {
					preloadedImages.push( $( this ).attr( 'src' ) );
					if ( preloadedImages.length === images.length ) {
						self.pagination_slide( view_number, width, height, next, effect, speed, responseView, wpvPaginatorLayout, wpvPaginatorFilter, callback_next );
					}
				}).each( function() {
					$( this ).load();
				});
			}
			// Fix nner nested Views with AJAX pagination:
			// The inner View, when preloading mages, is rendered with visibility:hidden by default
			responseView
				.find( '.wpv-pagination-preload-images' )
					.css( 'visibility', 'visible' );
		} else {
			self.pagination_slide( view_number, width, height, next, effect, speed, responseView, wpvPaginatorLayout, wpvPaginatorFilter, callback_next );
		}
		wpvPaginatorFilter.html( responseFilter );
		WPViews.view_frontend_utils.render_frontend_datepicker();
		// Move the wpv_view_hash, wpv_paged_max and wpv_widget_view_id from the forms as it's only needed during ajax pagination
		$( 'input[id=wpv_view_hash-' + view_number + '], input[id=wpv_paged_max-' + view_number + '], input[id=wpv_widget_view-' + view_number + ']' ).each( function(index) {
			parent = $( this ).parent();
			if ( ! parent.is( 'form' ) ) {
				$( this ).remove();
			} else {
				parent.after( this );
			}
		});
	};
	
	/**
	* pagination_replace_view
	*
	* Manage the View pagination.
	* 	- Trigger the related form submit when pagination is not AJAX-ed
	* 	- Trigger pagination_replace_view when pagination is AJAX-ed
	*
	* @param
	*
	* @since 1.9
	*/
	
	self.pagination_replace_view = function( view_number, page, ajax, effect, max_pages, cache_pages, preload_pages, spinner, spinner_image, callback_next, stop_rollover ) {
		if ( ! window.wpvPaginationAnimationFinished.hasOwnProperty( view_number ) ) {
			window.wpvPaginationAnimationFinished[view_number] = false;
		} else if ( window.wpvPaginationAnimationFinished[view_number] !== true ) {
			if ( ! window.wpvPaginationQueue.hasOwnProperty( view_number ) ) {
				window.wpvPaginationQueue[view_number] = [];
			}
			window.wpvPaginationQueue[view_number].push( arguments );
			return false;
		}
		window.wpvPaginationAnimationFinished[view_number] = false;
		if ( stop_rollover ) {
			wpv_stop_rollover[view_number] = true;
		}
		var data = {},
		wpvPaginatorLayout = $( '#wpv-view-layout-' + view_number ),
		wpvPaginatorFilter = $( 'form[name=wpv-filter-' + view_number + ']' ),
		speed = 500,
		next = true,
		max_reach = 1,
		img;
		if ( ajax !== true ) {
			// Add elements for the current url parameters
			// so any views that filter by url parameters will still work
			// We do not need to add the shortcode attributes
			// because basically passing them in the view_hash
			// @todo why not use wpv_add_url_controls_for_column_sort()
			data = {};
			data = WPViews.view_frontend_utils.add_url_query_parameters( data );
			$.each( data['get_params'], function( key, value ) {
				if ( wpvPaginatorFilter.find( '[name=' + key + '], [name=' + key + '\\[\\]]' ).length === 0 ) {
					var pieces = value.split( '##URLARRAYVALHACK##' ),
					pieces_length = pieces.length;
					if ( pieces_length < 2 ) {
						$( '<input>' ).attr({
							type: 'hidden',
							name: key,
							value: value
						})
						.appendTo( wpvPaginatorFilter );
					} else {
						for ( var iter = 0; iter < pieces_length; iter++ ) {
							$( '<input>' ).attr({
								type: 'hidden',
								name: key + "[]",
								value: pieces[iter]
							})
							.appendTo( wpvPaginatorFilter );
						}
					}
				}
			});
			// Adjust the wpv_paged hidden input to the page that we want to show
			if ( $( 'input[name=wpv_paged]' ).length > 0 ) {
				$( 'input[name=wpv_paged]' ).attr( 'value', page );
			} else {
				$( '<input>').attr({
						type: 'hidden',
						name: 'wpv_paged',
						value: page
					})
					.appendTo( wpvPaginatorFilter );
			}
			wpvPaginatorFilter[0].submit();
			return false;
		}
		window.wpvPaginationAjaxLoaded[view_number] = false;
		this.historyP = ( typeof this.historyP == 'undefined' ) ? [] : this.historyP;
		window.wpvCachedPages = ( typeof window.wpvCachedPages == 'undefined' ) ? [] : window.wpvCachedPages;
		window.wpvCachedPages[view_number] = ( typeof window.wpvCachedPages[view_number] == 'undefined' ) ? [] : window.wpvCachedPages[view_number];
		if ( effect === 'fadeslow' ) {
			speed = 1500; 
		} else if (effect === 'fadefast') {
			speed = 1;
		}
		if ( wpvPaginatorLayout.data( 'duration' ) ) {
			if ( wpvPaginatorLayout.data('duration') !== "" && $.isNumeric( wpvPaginatorLayout.data( 'duration' ) ) ) {
				speed = wpvPaginatorLayout.data( 'duration' );
				speed = parseFloat( speed );
			}
		}
		if ( this.historyP.hasOwnProperty( view_number ) ) {
			next = ( this.historyP[view_number] < page ) ? true : false;
		}
		if ( $( '#wpv_paged_preload_reach-'+view_number ).val() ) {
			max_reach = $( '#wpv_paged_preload_reach-'+view_number ).val();
		}
		max_reach++;
		if ( max_reach > max_pages ) {
			max_reach = max_pages;
		}
		if ( ( cache_pages || preload_pages ) && window.wpvCachedPages[view_number].hasOwnProperty( page ) ) {
			self.pagination_get_page( view_number, next, effect, speed, window.wpvCachedPages[view_number][page], wpvPaginatorLayout, wpvPaginatorFilter, callback_next );
			self.pagination_preload_pages( view_number, page, max_pages, cache_pages, preload_pages, max_reach );
		} else {
			// Set loading class
			if ( spinner !== 'no' ) {
				img = new Image();
				img.src = spinner_image;
				img.onload = function() {
					var wpvPaginatorLayoutOffset = wpvPaginatorLayout.position();
					wpvPaginatorLayout
					.before( '<div style="width:' + img.width + 'px;height:' + img.height + 'px;border:none;background:transparent 50% 50% no-repeat url(' + spinner_image + ');position:absolute;z-index:99;top:' + ( Math.round( wpvPaginatorLayoutOffset.top ) + ( Math.round( wpvPaginatorLayout.height()/2 ) ) - ( Math.round( img.height/2 ) ) ) + 'px; left:' + ( Math.round( wpvPaginatorLayoutOffset.left ) + ( Math.round( wpvPaginatorLayout.width()/2 ) ) - ( Math.round( img.width/2 ) ) ) + 'px;" id="wpv_slide_loading_img_' + view_number + '" class="wpv_slide_loading_img"></div>' )
					.animate( {opacity:0.5}, 300 );
				};
			}
			data = self.add_view_parameters( data, page, view_number );
			// add url sorting parameters to allow custom sorting using ajax and table sorting parameters
			// @why not use wpv_add_url_controls_for_column_sort()
			data = WPViews.view_frontend_utils.add_url_query_parameters( data );
			$.each( data['get_params'], function( key, value ) {
				if ( wpvPaginatorFilter.find( '[name=' + key + '], [name=' + key + '\\[\\]]' ).length === 0 ) {
					var pieces = value.split( '##URLARRAYVALHACK##' ),
					pieces_length = pieces.length;
					if ( pieces_length < 2 ) {
						$( '<input>' ).attr({
							type: 'hidden',
							name: key,
							value: value
						})
						.appendTo( wpvPaginatorFilter );
					} else {
						for ( var iter = 0; iter < pieces_length; iter++ ) {
							$( '<input>' ).attr({
								type: 'hidden',
								name: key + "[]",
								value: pieces[iter]
							})
							.appendTo( wpvPaginatorFilter );
						}
					}
				}
			});
			icl_lang = ( typeof icl_lang == 'undefined' ) ? false : icl_lang;
			if ( icl_lang !== false ) {
					data['lang'] = icl_lang;
				}
			$.get( self.get_ajax_pagination_url( data ), '', function(response) {
				self.pagination_get_page( view_number, next, effect, speed, response, wpvPaginatorLayout, wpvPaginatorFilter, callback_next );
			});
			self.pagination_preload_pages( view_number, page, max_pages, cache_pages, preload_pages, max_reach );
		}
		this.historyP[view_number] = page;
		return false;
	};
	
	/**
	* pagination_slide
	*
	* Control the pagination replacement effects.
	*
	* @param
	*
	* @since 1.9
	*/
	
	self.pagination_slide = function( view_number, width, height, next, effect, speed, responseView, wpvPaginatorLayout, wpvPaginatorFilter, callback_next ) {
		// !TODO clean the with/height parameters as at least one is not needed
		var old_height,
		new_height,
		is_callback = false,
		data_for_events = {};
		if ( callback_next !== '' ) {
			var callback_next_func = window[callback_next];
			if ( typeof callback_next_func === "function" ) {
				is_callback = true;
			}
		}
		data_for_events.view_unique_id = view_number;
		data_for_events.effect = effect;
		data_for_events.speed = speed;
		data_for_events.layout = responseView;
		// @todo we need here the orig_page and the current_page too
		
		if ( effect === 'slideh' || effect === 'slideleft' || effect === 'slideright' ) {
			if (effect === 'slideleft') {
				next = true;
			} else if ( effect === 'slideright' ) {
				next = false;
			}
			if ( next === true ) {
				//responseView.css('position', 'relative').css('margin-left', width+'px').css('margin-top', '-'+height+'px').css('visibility', 'visible');
				wpvPaginatorLayout.css( 'float', 'left' );
				responseView.css( {"float": "left", "visibility": "visible"} );
				wpvPaginatorLayout.after( responseView ).parent().children().wrapAll( '<div style="width:5000px;" />' );
				$( '#wpv_slide_loading_img_'+view_number ).fadeOut(function() {
					$( this ).remove();
				});
				old_height = wpvPaginatorLayout.outerHeight();
				new_height = responseView.outerHeight();
				if ( old_height === new_height ) {
					wpvPaginatorLayout.parent().animate( {marginLeft: '-'+wpvPaginatorLayout.outerWidth()+'px'}, speed+500, function() {
						responseView.css( {'position': 'static', 'float': 'none'} );
						wpvPaginatorLayout.unwrap().unwrap().remove();
						window.wpvPaginationAjaxLoaded[view_number] = true;
						window.wpvPaginationAnimationFinished[view_number] = true;
						if ( is_callback ) {
							callback_next_func();
						}
						$( document ).trigger( 'js_event_wpv_pagination_completed', [ data_for_events ] );
						self.pagination_queue_trigger( view_number, next, wpvPaginatorFilter );
					});
				} else if ( old_height > new_height ) {
					wpvPaginatorLayout.parent().animate( {marginLeft: '-'+wpvPaginatorLayout.outerWidth()+'px'}, speed+500, function() {
						wpvPaginatorLayout.parent().parent().animate( {height: responseView.outerHeight()+'px'}, speed/2, function() {
							responseView.css( {'position': 'static', 'float': 'none'} );
							wpvPaginatorLayout.unwrap().unwrap().remove();
							window.wpvPaginationAjaxLoaded[view_number] = true;
							window.wpvPaginationAnimationFinished[view_number] = true;
							if ( is_callback ) {
								callback_next_func();
							}
							$( document ).trigger( 'js_event_wpv_pagination_completed', [ data_for_events ] );
							self.pagination_queue_trigger( view_number, next, wpvPaginatorFilter );
						});
					});
				} else {
					wpvPaginatorLayout.parent().parent().animate( {height: responseView.outerHeight()+'px'}, speed/2, function() {
						wpvPaginatorLayout.parent().animate( {marginLeft: '-'+wpvPaginatorLayout.outerWidth()+'px'}, speed+500, function() {
							responseView.css( {'position': 'static', 'float': 'none'} );
							wpvPaginatorLayout.unwrap().unwrap().remove();
							window.wpvPaginationAjaxLoaded[view_number] = true;
							window.wpvPaginationAnimationFinished[view_number] = true;
							if ( is_callback ) {
								callback_next_func();
							}
							$( document ).trigger( 'js_event_wpv_pagination_completed', [ data_for_events ] );
							self.pagination_queue_trigger( view_number, next, wpvPaginatorFilter );
						});
					});
				}
			} else {
				wpvPaginatorLayout.css( 'float', 'right' );
				responseView.css( {'float': 'right', 'visibility': 'visible'} );
				wpvPaginatorLayout.after( responseView ).parent().children().wrapAll( '<div style="height:' + height +  ';width:' + ( responseView.outerWidth() + wpvPaginatorLayout.outerWidth() ) + 'px; margin-left:-' + ( wpvPaginatorLayout.outerWidth() ) + 'px;" />' );
				$( '#wpv_slide_loading_img_' + view_number ).fadeOut( function() {
					$( this ).remove();
				});
				old_height = wpvPaginatorLayout.outerHeight();
				new_height = responseView.outerHeight();
				if ( old_height === new_height ) {
					wpvPaginatorLayout.parent().animate( {marginLeft: '0px'}, speed+500, function() {
						responseView.css( {'position': 'static', 'margin': '0px', 'float': 'none'} );
						wpvPaginatorLayout.unwrap().unwrap().remove();
						window.wpvPaginationAjaxLoaded[view_number] = true;
						window.wpvPaginationAnimationFinished[view_number] = true;
						if ( is_callback ) {
							callback_next_func();
						}
						$( document ).trigger( 'js_event_wpv_pagination_completed', [ data_for_events ] );
						self.pagination_queue_trigger( view_number, next, wpvPaginatorFilter );
					});
				} else if ( old_height > new_height ) {
					wpvPaginatorLayout.parent().animate( {marginLeft: '0px'}, speed+500, function() {
						wpvPaginatorLayout.parent().parent().animate( {height: responseView.outerHeight()+'px'}, speed/2, function() {
							responseView.css( {'position': 'static', 'margin': '0px', 'float': 'none'} );
							wpvPaginatorLayout.unwrap().unwrap().remove();
							window.wpvPaginationAjaxLoaded[view_number] = true;
							window.wpvPaginationAnimationFinished[view_number] = true;
							if ( is_callback ) {
								callback_next_func();
							}
							$( document ).trigger( 'js_event_wpv_pagination_completed', [ data_for_events ] );
							self.pagination_queue_trigger( view_number, next, wpvPaginatorFilter );
						});
					});
				} else {
					wpvPaginatorLayout.parent().parent().animate( {height: responseView.outerHeight()+'px'}, speed/2, function() {
						wpvPaginatorLayout.parent().animate( {marginLeft: '0px'}, speed+500, function() {
							responseView.css( {'position': 'static', 'margin': '0px', 'float': 'none'} );
							wpvPaginatorLayout.unwrap().unwrap().remove();
							window.wpvPaginationAjaxLoaded[view_number] = true;
							window.wpvPaginationAnimationFinished[view_number] = true;
							if ( is_callback ) {
								callback_next_func();
							}
							$( document ).trigger( 'js_event_wpv_pagination_completed', [ data_for_events ] );
							self.pagination_queue_trigger( view_number, next, wpvPaginatorFilter );
						});
					});
				}
			}
		} else if ( effect === 'slidev' || effect === 'slideup' || effect === 'slidedown' ) {
			if ( effect === 'slidedown' ) {
				next = false;
			} else if ( effect === 'slideup' ) {
				next = true;
			}
			if ( next === true ) {
				responseView.css( 'visibility', 'visible' );
				wpvPaginatorLayout.after( responseView ).parent().children().wrapAll( '<div />' );
				$( '#wpv_slide_loading_img_' + view_number ).fadeOut( function(){
					$( this ).remove();
				});
				old_height = wpvPaginatorLayout.outerHeight();
				new_height = responseView.outerHeight();
				if ( old_height === new_height ) {
					wpvPaginatorLayout.parent().animate( {marginTop: '-'+responseView.outerHeight()+'px'}, speed+500, function() {
						responseView.css( {'position': 'static', 'margin': '0px'} );
						wpvPaginatorLayout.unwrap().unwrap().remove();
						window.wpvPaginationAjaxLoaded[view_number] = true;
						window.wpvPaginationAnimationFinished[view_number] = true;
						if ( is_callback ) {
							callback_next_func();
						}
						$( document ).trigger( 'js_event_wpv_pagination_completed', [ data_for_events ] );
						self.pagination_queue_trigger( view_number, next, wpvPaginatorFilter );
					});
				} else if ( old_height > new_height ) {
					wpvPaginatorLayout.parent().animate( {marginTop: '-'+old_height+'px'}, speed+500, function() {
						wpvPaginatorLayout.parent().parent().animate( {height: responseView.outerHeight()+'px'}, speed/2, function() {
							responseView.css( {'position': 'static', 'margin': '0px'} );
							wpvPaginatorLayout.unwrap().unwrap().remove();
							window.wpvPaginationAjaxLoaded[view_number] = true;
							window.wpvPaginationAnimationFinished[view_number] = true;
							if ( is_callback ) {
								callback_next_func();
							}
							$( document ).trigger( 'js_event_wpv_pagination_completed', [ data_for_events ] );
							self.pagination_queue_trigger( view_number, next, wpvPaginatorFilter );
						});
					});
				} else {
					wpvPaginatorLayout.parent().parent().animate( {height: responseView.outerHeight()+'px'}, speed/2, function() {
						wpvPaginatorLayout.parent().animate( {marginTop: '-'+old_height+'px'}, speed+500, function() {
							responseView.css( {'position': 'static', 'margin': '0px'} );
							wpvPaginatorLayout.unwrap().unwrap().remove();
							window.wpvPaginationAjaxLoaded[view_number] = true;
							window.wpvPaginationAnimationFinished[view_number] = true;
							if ( is_callback ) {
								callback_next_func();
							}
							$( document ).trigger( 'js_event_wpv_pagination_completed', [ data_for_events ] );
							self.pagination_queue_trigger( view_number, next, wpvPaginatorFilter );
						});
					});
				}
			} else {
				responseView.css( 'visibility', 'visible' );
				wpvPaginatorLayout.before( responseView ).parent().children().wrapAll( '<div />' );
				$( '#wpv_slide_loading_img_' + view_number ).fadeOut( function() {
					$( this ).remove();
				});
				old_height = wpvPaginatorLayout.outerHeight();
				new_height = responseView.outerHeight();
				wpvPaginatorLayout.parent().css( {'position': 'relative', 'margin-top': '-' + responseView.outerHeight() + 'px'} );
				if ( old_height === new_height ) {
					wpvPaginatorLayout.parent().animate( {marginTop: '0px'}, speed+500, function() {
						responseView.css( {'position': 'static', 'margin': '0px'} );
						wpvPaginatorLayout.unwrap().unwrap().remove();
						window.wpvPaginationAjaxLoaded[view_number] = true;
						window.wpvPaginationAnimationFinished[view_number] = true;
						if ( is_callback ) {
							callback_next_func();
						}
						$( document ).trigger( 'js_event_wpv_pagination_completed', [ data_for_events ] );
						self.pagination_queue_trigger( view_number, next, wpvPaginatorFilter );
					});
				} else if ( old_height > new_height ) {
					wpvPaginatorLayout.parent().animate( {marginTop: '0px'}, speed+500, function() {
						wpvPaginatorLayout.parent().parent().animate( {height: responseView.outerHeight()+'px'}, speed/2, function() {
							responseView.css( {'position': 'static', 'margin': '0px'} );
							wpvPaginatorLayout.unwrap().unwrap().remove();
							window.wpvPaginationAjaxLoaded[view_number] = true;
							window.wpvPaginationAnimationFinished[view_number] = true;
							if ( is_callback ) {
								callback_next_func();
							}
							$( document ).trigger( 'js_event_wpv_pagination_completed', [ data_for_events ] );
							self.pagination_queue_trigger( view_number, next, wpvPaginatorFilter );
						});
					});
				} else {
					wpvPaginatorLayout.parent().parent().animate( {height: responseView.outerHeight()+'px'}, speed/2, function() {
						wpvPaginatorLayout.parent().animate( {marginTop: '0px'}, speed+500, function() {
							responseView.css( {'position': 'static', 'margin': '0px'} );
							wpvPaginatorLayout.unwrap().unwrap().remove();
							window.wpvPaginationAjaxLoaded[view_number] = true;
							window.wpvPaginationAnimationFinished[view_number] = true;
							if ( is_callback ) {
								callback_next_func();
							}
							$( document ).trigger( 'js_event_wpv_pagination_completed', [ data_for_events ] );
							self.pagination_queue_trigger( view_number, next, wpvPaginatorFilter );
						});
					});
				}
			}
		} else { // Fade
			$( '#wpv_slide_loading_img_' + view_number ).fadeOut( function() {
				$( this ).remove();
			});
			wpvPaginatorLayout.css( {'position': 'absolute', 'z-index': '5'} ).after( responseView ).next().css( 'position', 'static' );
			old_height = wpvPaginatorLayout.outerHeight();
			new_height = responseView.outerHeight();
			if ( old_height === new_height ) {
				wpvPaginatorLayout.fadeOut( speed, function(){
					wpvPaginatorLayout.unwrap().remove();
					window.wpvPaginationAjaxLoaded[view_number] = true;
					window.wpvPaginationAnimationFinished[view_number] = true;
					if ( is_callback ) {
						callback_next_func();
					}
					$( document ).trigger( 'js_event_wpv_pagination_completed', [ data_for_events ] );
					self.pagination_queue_trigger( view_number, next, wpvPaginatorFilter );
				});
				responseView.hide().css( 'visibility', 'visible' ).fadeIn( speed );
			} else {
				wpvPaginatorLayout.fadeOut( speed, function() {
					wpvPaginatorLayout.parent().animate( {height: new_height+'px'}, speed, function() {
						wpvPaginatorLayout.unwrap().remove();
						window.wpvPaginationAjaxLoaded[view_number] = true;
						window.wpvPaginationAnimationFinished[view_number] = true;
						if ( is_callback ) {
							callback_next_func();
						}
						$( document ).trigger( 'js_event_wpv_pagination_completed', [ data_for_events ] );
						self.pagination_queue_trigger( view_number, next, wpvPaginatorFilter );
						responseView.hide().css( 'visibility', 'visible' ).fadeIn( speed );
					});
				});
			}
		}
	};
	
	/**
	* pagination_replace_view_links
	*
	* Replace a View layout when triggered from a pagination link.
	*
	* @param
	*
	* @since 1.9
	*/
	
	self.pagination_replace_view_links = function( data_collected ) {
		var i;// TODO this can be improved: we should not need a loop here at all
		for ( i = 1; i <= data_collected.max_pages; i++ ) {
			if ( i === data_collected.page ) {
				$( '#wpv-page-link-' + data_collected.view_number + '-' + i ).addClass( 'wpv_page_current' );
			} else {
				$( '#wpv-page-link-' + data_collected.view_number + '-' + i ).removeClass( 'wpv_page_current' );
			}
			
		}
		self.pagination_replace_view( 
			data_collected.view_number, 
			data_collected.page, 
			data_collected.ajax, 
			data_collected.effect, 
			data_collected.max_pages, 
			data_collected.cache_pages, 
			data_collected.preload_pages, 
			data_collected.spinner, 
			data_collected.spinner_image, 
			data_collected.callback_next, 
			data_collected.stop_rollover 
		);
	};
	/**
	* pagination_queue_trigger
	*
	* Manage multiple and fast pagination requests.
	*
	* @param
	*
	* @since 1.9
	*/
	
	self.pagination_queue_trigger = function( view_number, next, wpvPaginatorFilter ) {
		var args,
		page,
		max_pages;
		if ( window.wpvPaginationQueue.hasOwnProperty( view_number ) && window.wpvPaginationQueue[view_number].length > 0 ) {
		// when double clicking,we have set window.wpvPaginationQueue[view_number][1] and maybe we could tweak it to change the page number. Maybe checkin historyP
			window.wpvPaginationQueue[view_number].sort();
			args = window.wpvPaginationQueue[view_number][0];
			window.wpvPaginationQueue[view_number].splice(0, 1);
			page = args[1];
			max_pages = args[4];
			if ( page > max_pages ) {
				page = 1;
			} else if ( page < 1 ) {
				page = max_pages;
			}
			self.pagination_replace_view( view_number, page, args[2], args[3], args[4], args[5], args[6], args[7], args[8], args[10] );
		}
	};
	
	// ------------------------------------
	// Events
	// ------------------------------------
	
	/**
	* Manage pagination triggered from prev/next links
	*
	* @since 1.9
	*/
	
	$( document ).on( 'click', '.js-wpv-pagination-next-link, .js-wpv-pagination-previous-link', function( e ) {
		e.preventDefault();
		var thiz = $( this ),
		view_number = thiz.data( 'viewnumber' ),
		page = thiz.data( 'page' ),
		ajax = thiz.data( 'ajax' ),
		effect = thiz.data( 'effect' ),
		max_pages = thiz.data( 'maxpages' ),
		cache_pages = thiz.data( 'cachepages' ),
		preload_pages = thiz.data( 'preloadimages' ),
		spinner = thiz.data( 'spinner' ),
		spinner_image = thiz.data( 'spinnerimage' ),
		callback_next = thiz.data( 'callbacknext' ),
		stop_rollover = thiz.data( 'stoprollover' );
		return self.pagination_replace_view( 
			view_number, 
			page, 
			ajax, 
			effect, 
			max_pages, 
			cache_pages, 
			preload_pages, 
			spinner, 
			spinner_image, 
			callback_next, 
			stop_rollover 
		);
	});
	
	/**
	* Manage pagination triggered by a change in the page selector dropdown
	*
	* @since 1.9
	*/
	
	$( document ).on( 'change', '.js-wpv-page-selector', function( e ) {
		e.preventDefault();
		var thiz = $( this ),
		view_number = thiz.data( 'viewnumber' ),
		page = thiz.val(),
		ajax = thiz.data( 'ajax' ),
		effect = thiz.data( 'effect' ),
		max_pages = thiz.data( 'maxpages' ),
		cache_pages = thiz.data( 'cachepages' ),
		preload_pages = thiz.data( 'preloadimages' ),
		spinner = thiz.data( 'spinner' ),
		spinner_image = thiz.data( 'spinnerimage' ),
		callback_next = thiz.data( 'callbacknext' ),
		stop_rollover = thiz.data( 'stoprollover' );
		return self.pagination_replace_view( 
			view_number, 
			page, 
			ajax, 
			effect, 
			max_pages, 
			cache_pages, 
			preload_pages, 
			spinner, 
			spinner_image, 
			callback_next, 
			stop_rollover 
		);
	});
	
	/**
	* Manage pagination triggered by a click on a pagination link.
	*
	* @since 1.9
	*
	* @note Safari on iOS might need to also listen to the touchstart event. Investigate this!
	*/
	
	$( document ).on( 'click', '.js-wpv-pagination-link', function( e ) {
		e.preventDefault();
		var thiz = $( this ),
		data_collected = {};
		data_collected.view_number = thiz.data( 'viewnumber');
		data_collected.page = thiz.data( 'page' );
		data_collected.ajax = thiz.data( 'ajax' );
		data_collected.effect = thiz.data( 'effect' );
		data_collected.max_pages = thiz.data( 'maxpages' );
		data_collected.cache_pages = thiz.data( 'cachepages' );
		data_collected.preload_pages = thiz.data( 'preloadimages' );
		data_collected.spinner = thiz.data( 'spinner' );
		data_collected.spinner_image = thiz.data( 'spinnerimage' );
		data_collected.callback_next = thiz.data( 'callbacknext' );
		data_collected.stop_rollover = thiz.data( 'stoprollover' );
		return self.pagination_replace_view_links( data_collected );
	});
	
	// ------------------------------------
	// Custom events
	// ------------------------------------
	
	/**
	* js_event_wpv_pagination_completed
	*
	* Event fired after a pagination transition has been completed
	*
	* @param data
	* 	- view_unique_id
	* 	- effect
	* 	- speed
	* 	- layout
	*
	* @since 1.9
	*/
	
	$( document ).on( 'js_event_wpv_pagination_completed', function( event, data ) {
		WPViews.view_frontend_utils.render_frontend_media_shortcodes( data.layout );
	});
	
	// ------------------------------------
	// Init
	// ------------------------------------
	
	self.init = function() {
		// Preload images in paginator initially
		$('.wpv-pagination-preload-images').css('visibility', 'hidden'); // TODO move it to the CSS file and test
		self.pagination_init_preload_images();
		$('.wpv-pagination-preload-pages').each(function(){
			var view_number = parseInt($(this).attr('id').substring(16), 10);
			var max_pages = parseInt($('#wpv_paged_max-'+view_number).val(),10);
			var max_reach = 1;
			if ($('#wpv_paged_preload_reach-'+view_number).val()) {
				max_reach = parseInt($('#wpv_paged_preload_reach-'+view_number).val(), 10);
			}
			max_reach++;
			if (max_reach > max_pages) {
				max_reach = max_pages;
			}
			self.pagination_preload_pages(view_number, 1, max_pages, false, true, max_reach);
		});
		// Move the wpv_view_hash, wpv_paged_max and wpv_widget_view_id from the forms as it's only needed during ajax pagination
		$('input[name=wpv_view_hash], input[name=wpv_paged_max], input[name=wpv_widget_view_id]').each( function() {
			$(this).parent().after(this);
		});
		// Datepicker initailizing
		//WPViews.view_frontend_utils.render_frontend_datepicker();
	}
	
	self.init();

};

WPViews.ViewParametricSearch = function( $ ) {
	
	// ------------------------------------
	// Constants and variables
	// ------------------------------------
	
	var self = this;
	
	// ------------------------------------
	// Methods
	// ------------------------------------
	
	/**
	* manage_update_form
	*
	*
	*
	* @since 1.9
	*
	* @todo we are not handling 3rd party URL parameters here
	*/

	self.manage_update_form = function( fil, ajax_get ) {
		var view_num = fil.data( 'viewnumber' ),
		view_id = fil.data( 'viewid' ),
		aux_fil,
		data = {
			action: 'wpv_update_parametric_search',
			valz: fil.serializeArray(),
			viewid: view_id,
			getthis: ajax_get
		},
		attr_data = fil.find('.js-wpv-view-attributes');
		wpv_stop_rollover[view_num] = true;
		if ( attr_data.length > 0 ) {
			data['attributes'] = attr_data.data();
		}
		
		if ( fil.attr( 'data-targetid' ) ) {
			data.targetid = fil.data( 'targetid' );
		} else if ( ajax_get == 'both' ) {
			aux_fil = $( '.js-wpv-form-only.js-wpv-filter-form-' + view_num );
			data.targetid = aux_fil.data( 'targetid' );
		}
		
		return $.ajax({
			type: "POST",
			url: wpv_pagination_local.front_ajaxurl,
			data: data
		});
	};
	
	self.manage_update_results = function( lay, new_lay, view_num, ajax_before, ajax_after ) {
		if ( ajax_before !== '' ) {
			var ajax_before_func = window[ajax_before];
			if ( typeof ajax_before_func === "function" ) {
				ajax_before_func( view_num );
			}
		}
		var data_for_events = {};
		data_for_events.view_unique_id = view_num;
		lay.fadeOut( 200, function() {
			lay.html( new_lay ).fadeIn( 'fast', function() {
				var ajax_after_func = window[ajax_after];
				if ( typeof ajax_after_func === "function" ) {
					ajax_after_func( view_num );
				}
				data_for_events.layout = lay;
				$( document ).trigger( 'js_event_wpv_parametric_search_results_updated', [ data_for_events ] );
			});		
		});
	};
	
	/**
	* manage_changed_form
	*
	* @param
	*
	* @since 1.9
	*/
	
	self.manage_changed_form = function( fil, force_form_update, force_results_update ) {
		var view_num = fil.data( 'viewnumber' ),
		lay = $( '#wpv-view-layout-' + view_num ),
		full_data = fil.find( '.js-wpv-filter-data-for-this-form' ),
		ajax_pre_before = full_data.data( 'ajaxprebefore' ),
		ajax_before = full_data.data( 'ajaxbefore' ),
		ajax_after = full_data.data( 'ajaxafter' ),
		view_type = 'full',
		additional_forms = $( '.js-wpv-filter-form-' + view_num ).not( fil ),
		additional_forms_only,
		additional_forms_full,
		ajax_get = 'both',
		new_content_form,
		new_content_form_filter,
		new_content_full,
		new_content_full_filter,
		new_content_full_layout,
		spinnerContainer = fil.find( '.js-wpv-dps-spinner' ).add( additional_forms.find( '.js-wpv-dps-spinner' ) ),//TODO maybe add a view_num here to select all spinnerContainers
		spinnerItems = spinnerContainer.length
		data_for_events = {};
		data_for_events.view_unique_id = view_num;
		if ( fil.hasClass( 'js-wpv-form-only' ) ) {
			view_type = 'form';
		}
		if ( fil.hasClass( 'js-wpv-dps-enabled' ) || force_form_update === true ) {
			if ( additional_forms.length > 0 ) {
				additional_forms_only = additional_forms.not( '.js-wpv-form-full' );
				additional_forms_full = additional_forms.not( '.js-wpv-form-only' );
				if ( view_type == 'form' ) {
					if ( additional_forms_full.length > 0 || ( fil.hasClass( 'js-wpv-ajax-results-enabled' ) && lay.length > 0 ) ) {
						ajax_get = 'both';					
					} else {
						ajax_get = 'form';
					}
					if (
						( fil.hasClass( 'js-wpv-ajax-results-enabled' ) && lay.length > 0 )
						|| force_results_update
					) {
						if ( ajax_pre_before !== '' ) {
							var ajax_pre_before_func = window[ajax_pre_before];
							if ( typeof ajax_pre_before_func === "function" ) {
								ajax_pre_before_func( view_num );
							}
						}
						$( document ).trigger( 'js_event_wpv_parametric_search_started', [ data_for_events ] );
					}
					if ( spinnerItems ) {// TODO maybe only when updating results
						$( spinnerContainer ).fadeIn( 'fast' );
					}
					self.manage_update_form( fil, ajax_get ).done(function(result) {
						decoded_response = $.parseJSON(result);
						new_content_form = $( '<div></div>' ).append( decoded_response.form );
						new_content_full = $( '<div></div>' ).append( decoded_response.full );
						new_content_form_filter = new_content_form.find( '.js-wpv-filter-form' ).html();
						new_content_full_filter = new_content_full.find( '.js-wpv-filter-form' ).html();
						new_content_full_layout = new_content_full.find( '.js-wpv-view-layout' ).html();
						
						fil.html( new_content_form_filter );
						$( ".js-wpv-frontend-datepicker" )
							.removeClass( 'js-wpv-frontend-datepicker-inited' )
							.datepicker( "destroy" );
						WPViews.view_frontend_utils.clone_form( fil, additional_forms_only );
						additional_forms_full.each( function() {
							$( this ).html( new_content_full_filter );
						});
						data_for_events.view_changed_form = fil;
						data_for_events.view_changed_form_additional_forms_only = additional_forms_only;
						data_for_events.view_changed_form_additional_forms_full = additional_forms_full;
						$( document ).trigger( 'js_event_wpv_parametric_search_form_updated', [ data_for_events ] );
						if (
							( fil.hasClass( 'js-wpv-ajax-results-enabled' ) && lay.length > 0 )
							|| force_results_update
						) {
							self.manage_update_results( lay, new_content_full_layout, view_num, ajax_before, ajax_after );
						}
						spinnerContainer.hide();
					}).fail(function() {
						// an error occurred
					});
				} else {
					if ( additional_forms_only.length > 0 ) {
						ajax_get = 'both';
					} else {
						ajax_get = 'full';
					}
					if (
						( fil.hasClass( 'js-wpv-ajax-results-enabled' ) && lay.length > 0 )
						|| force_results_update
					) {
						if ( ajax_pre_before !== '' ) {
							var ajax_pre_before_func = window[ajax_pre_before];
							if ( typeof ajax_pre_before_func === "function" ) {
								ajax_pre_before_func( view_num );
							}
						}
						$( document ).trigger( 'js_event_wpv_parametric_search_started', [ data_for_events ] );
					}
					if ( spinnerItems ) {// TODO maybe only when updating results
						$( spinnerContainer ).fadeIn( 'fast' );
					}
					self.manage_update_form( fil, ajax_get ).done(function(result) {
						decoded_response = $.parseJSON(result);
						new_content_form = $( '<div></div>' ).append( decoded_response.form );
						new_content_full = $( '<div></div>' ).append( decoded_response.full );
						new_content_form_filter = new_content_form.find( '.js-wpv-filter-form' ).html();
						new_content_full_filter = new_content_full.find( '.js-wpv-filter-form' ).html();
						new_content_full_layout = new_content_full.find( '.js-wpv-view-layout' ).html();
						
						fil.html( new_content_full_filter );
						$( ".js-wpv-frontend-datepicker" )
							.removeClass( 'js-wpv-frontend-datepicker-inited' )
							.datepicker( "destroy" );
						WPViews.view_frontend_utils.clone_form( fil, additional_forms_full );
						additional_forms_only.each( function() {
							$( this ).html( new_content_form_filter );
						});
						data_for_events.view_changed_form = fil;
						data_for_events.view_changed_form_additional_forms_only = additional_forms_only;
						data_for_events.view_changed_form_additional_forms_full = additional_forms_full;
						$( document ).trigger( 'js_event_wpv_parametric_search_form_updated', [ data_for_events ] );
						if (
							( fil.hasClass( 'js-wpv-ajax-results-enabled' ) && lay.length > 0 )
							|| force_results_update
						) {
							self.manage_update_results( lay, new_content_full_layout, view_num, ajax_before, ajax_after );
						}
						spinnerContainer.hide();
					}).fail(function() {
						// an error occurred
					});
				}
			} else {
				if ( view_type == 'form' ) {
					if ( fil.hasClass( 'js-wpv-ajax-results-enabled' ) && lay.length > 0 ) {
						ajax_get = 'both';
						// NOTE this should never happen:
						// If change is done on an only-form and there is no extra form, there is no full form thus there is no layout
						// WARNING this can be executed on an only-form form from a View with automatic results
						// I might want to avoid this branch completely
						// NOTE-2 might be a good idea to keep-on-clear// As we might be displaying the layout in non-standard ways
						// So keeping the check for lay.length should suffice
						
					} else {
						ajax_get = 'form';
					}
					
					if (
						( fil.hasClass( 'js-wpv-ajax-results-enabled' ) && lay.length > 0 )
						|| force_results_update
					) {
						if ( ajax_pre_before !== '' ) {
							var ajax_pre_before_func = window[ajax_pre_before];
							if ( typeof ajax_pre_before_func === "function" ) {
								ajax_pre_before_func( view_num );
							}
						}
						$( document ).trigger( 'js_event_wpv_parametric_search_started', [ data_for_events ] );
					}
					if ( spinnerItems ) {// TODO maybe only when updating results
						$( spinnerContainer ).fadeIn( 'fast' );
					}
					self.manage_update_form( fil, ajax_get ).done(function(result) {
						decoded_response = $.parseJSON(result);
						new_content_form = $( '<div></div>' ).append( decoded_response.form );
						new_content_full = $( '<div></div>' ).append( decoded_response.full );
						new_content_form_filter = new_content_form.find( '.js-wpv-filter-form' ).html();
						//new_content_full_filter = new_content_full.find( '.js-wpv-filter-form' ).html();
						new_content_full_layout = new_content_full.find( '.js-wpv-view-layout' ).html();
						fil.html( new_content_form_filter );
						data_for_events.view_changed_form = fil;
						data_for_events.view_changed_form_additional_forms_only = additional_forms_only;
						data_for_events.view_changed_form_additional_forms_full = additional_forms_full;
						$( document ).trigger( 'js_event_wpv_parametric_search_form_updated', [ data_for_events ] );
						if (
							( fil.hasClass( 'js-wpv-ajax-results-enabled' ) && lay.length > 0 )
							|| force_results_update
						) {
							self.manage_update_results( lay, new_content_full_layout, view_num, ajax_before, ajax_after );
						}
						spinnerContainer.hide();
					}).fail(function() {
						// an error occurred
					});
				} else {
					if (
						( fil.hasClass( 'js-wpv-ajax-results-enabled' ) && lay.length > 0 )
						|| force_results_update
					) {
						if ( ajax_pre_before !== '' ) {
							var ajax_pre_before_func = window[ajax_pre_before];
							if ( typeof ajax_pre_before_func === "function" ) {
								ajax_pre_before_func( view_num );
							}
						}
						$( document ).trigger( 'js_event_wpv_parametric_search_started', [ data_for_events ] );
					}
					if ( spinnerItems ) {// TODO maybe only when updating results
						$( spinnerContainer ).fadeIn( 'fast' );
					}
					self.manage_update_form( fil, 'full' ).done(function(result) {
						decoded_response = $.parseJSON(result);
						//new_content_form = $( '<div></div>' ).append( ajax_result.form );
						new_content_full = $( '<div></div>' ).append( decoded_response.full );
						//new_content_form_filter = new_content_form.find( '.js-wpv-filter-form' ).html();
						new_content_full_filter = new_content_full.find( '.js-wpv-filter-form' ).html();
						new_content_full_layout = new_content_full.find( '.js-wpv-view-layout' ).html();
						fil.html( new_content_full_filter );
						data_for_events.view_changed_form = fil;
						data_for_events.view_changed_form_additional_forms_only = additional_forms_only;
						data_for_events.view_changed_form_additional_forms_full = additional_forms_full;
						$( document ).trigger( 'js_event_wpv_parametric_search_form_updated', [ data_for_events ] );
						if (
							( fil.hasClass( 'js-wpv-ajax-results-enabled' ) && lay.length > 0 )
							|| force_results_update
						) {
							self.manage_update_results( lay, new_content_full_layout, view_num, ajax_before, ajax_after );
						}
						spinnerContainer.hide();
					}).fail(function() {
						// an error occurred
					});
				}
			}
		} else {
			if ( additional_forms.length > 0 ) {
				additional_forms_only = additional_forms.not( '.js-wpv-form-full' );
				additional_forms_full = additional_forms.not( '.js-wpv-form-only' );
				if ( view_type == 'form' ) {
					$( ".js-wpv-frontend-datepicker" )
						.removeClass( 'js-wpv-frontend-datepicker-inited' )
						.datepicker( "destroy" );
					WPViews.view_frontend_utils.clone_form( fil, additional_forms_only );
					if ( additional_forms_full.length > 0 || ( fil.hasClass( 'js-wpv-ajax-results-enabled' ) && lay.length > 0 ) ) {
						if (
							( fil.hasClass( 'js-wpv-ajax-results-enabled' ) && lay.length > 0 )
							|| force_results_update
						) {
							if ( ajax_pre_before !== '' ) {
								var ajax_pre_before_func = window[ajax_pre_before];
								if ( typeof ajax_pre_before_func === "function" ) {
									ajax_pre_before_func( view_num );
								}
							}
							$( document ).trigger( 'js_event_wpv_parametric_search_started', [ data_for_events ] );
						}
						if ( spinnerItems ) {// TODO maybe only when updating results
							$( spinnerContainer ).fadeIn( 'fast' );
						}
						self.manage_update_form( fil, 'full' ).done(function(result) {
							decoded_response = $.parseJSON(result);
							//new_content_form = $( '<div></div>' ).append( decoded_response.form );
							new_content_full = $( '<div></div>' ).append( decoded_response.full );
							//new_content_form_filter = new_content_form.find( '.js-wpv-filter-form' ).html();
							new_content_full_filter = new_content_full.find( '.js-wpv-filter-form' ).html();
							new_content_full_layout = new_content_full.find( '.js-wpv-view-layout' ).html();
							
							additional_forms_full.each( function() {
								$( this ).html( new_content_full_filter );
							});
							data_for_events.view_changed_form = fil;
							data_for_events.view_changed_form_additional_forms_only = additional_forms_only;
							data_for_events.view_changed_form_additional_forms_full = additional_forms_full;
							$( document ).trigger( 'js_event_wpv_parametric_search_form_updated', [ data_for_events ] );
							if (
								( fil.hasClass( 'js-wpv-ajax-results-enabled' ) && lay.length > 0 )
								|| force_results_update
							) {
								self.manage_update_results( lay, new_content_full_layout, view_num, ajax_before, ajax_after );
							}
							spinnerContainer.hide();
						}).fail(function() {
							// an error occurred
						});
					} else {
						data_for_events.view_changed_form = fil;
						data_for_events.view_changed_form_additional_forms_only = additional_forms_only;
						data_for_events.view_changed_form_additional_forms_full = additional_forms_full;
						$( document ).trigger( 'js_event_wpv_parametric_search_form_updated', [ data_for_events ] );
					}
				} else {
					$( ".js-wpv-frontend-datepicker" )
						.removeClass( 'js-wpv-frontend-datepicker-inited' )
						.datepicker( "destroy" );
					WPViews.view_frontend_utils.clone_form( fil, additional_forms_full );
					WPViews.view_frontend_utils.render_frontend_datepicker();
					if ( additional_forms_only.length > 0 || ( fil.hasClass( 'js-wpv-ajax-results-enabled' ) && lay.length > 0 ) ) {
						if ( additional_forms_only.length > 0 ) {
							ajax_get = 'both';
						} else {
							ajax_get = 'full';
						}
						
						if (
							( fil.hasClass( 'js-wpv-ajax-results-enabled' ) && lay.length > 0 )
							|| force_results_update
						) {
							if ( ajax_pre_before !== '' ) {
								var ajax_pre_before_func = window[ajax_pre_before];
								if ( typeof ajax_pre_before_func === "function" ) {
									ajax_pre_before_func( view_num );
								}
							}
							$( document ).trigger( 'js_event_wpv_parametric_search_started', [ data_for_events ] );
						}
						if ( spinnerItems ) {// TODO maybe only when updating results
							$( spinnerContainer ).fadeIn( 'fast' );
						}
						self.manage_update_form( fil, ajax_get ).done(function(result) {
							decoded_response = $.parseJSON(result);
							new_content_form = $( '<div></div>' ).append( decoded_response.form );
							new_content_full = $( '<div></div>' ).append( decoded_response.full );
							new_content_form_filter = new_content_form.find( '.js-wpv-filter-form' ).html();
							//new_content_full_filter = new_content_full.find( '.js-wpv-filter-form' ).html();
							new_content_full_layout = new_content_full.find( '.js-wpv-view-layout' ).html();
							additional_forms_only.each( function() {
								$( this ).html( new_content_form_filter );
							});
							data_for_events.view_changed_form = fil;
							data_for_events.view_changed_form_additional_forms_only = additional_forms_only;
							data_for_events.view_changed_form_additional_forms_full = additional_forms_full;
							$( document ).trigger( 'js_event_wpv_parametric_search_form_updated', [ data_for_events ] );
							if (
								( fil.hasClass( 'js-wpv-ajax-results-enabled' ) && lay.length > 0 )
								|| force_results_update
							) {
								self.manage_update_results( lay, new_content_full_layout, view_num, ajax_before, ajax_after );
							}
							spinnerContainer.hide();
						}).fail(function() {
							// an error occurred
						});
					}
				}
			} else {
				if (
					( fil.hasClass( 'js-wpv-ajax-results-enabled' ) && lay.length > 0 )
					|| force_results_update
				) {
					if ( ajax_pre_before !== '' ) {
						var ajax_pre_before_func = window[ajax_pre_before];
						if ( typeof ajax_pre_before_func === "function" ) {
							ajax_pre_before_func( view_num );
						}
					}
					$( document ).trigger( 'js_event_wpv_parametric_search_started', [ data_for_events ] );
					if ( spinnerItems ) {// TODO maybe only when updating results
						$( spinnerContainer ).fadeIn( 'fast' );
					}
					self.manage_update_form( fil, 'full' ).done(function(result) {
						decoded_response = $.parseJSON(result);
						//new_content_form = $( '<div></div>' ).append( decoded_response.form );
						new_content_full = $( '<div></div>' ).append( decoded_response.full );
						//new_content_form_filter = new_content_form.find( '.js-wpv-filter-form' ).html();
						//new_content_full_filter = new_content_full.find( '.js-wpv-filter-form' ).html();
						new_content_full_layout = new_content_full.find( '.js-wpv-view-layout' ).html();
						self.manage_update_results( lay, new_content_full_layout, view_num, ajax_before, ajax_after );
						spinnerContainer.hide();
					}).fail(function() {
						// an error occurred
					});
				}
			}
		}
	};
	
	/**
	* dependant_form_clear_pagination_cache
	*
	*
	* @param
	*
	* @since 1.9
	*/
	
	self.dependant_form_clear_pagination_cache = function( view_number ) {
		window.wpvCachedPages = ( typeof window.wpvCachedPages == 'undefined' ) ? [] : window.wpvCachedPages;
		window.wpvCachedPages[view_number] = [];
	};
	
	// ------------------------------------
	// Events
	// ------------------------------------
	
	// Show datepicker on date string click
	$( document ).on( 'click', '.js-wpv-date-display', function() {
		var url_param = $( this ).data( 'param' );
		$( '.js-wpv-date-front-end-' + url_param ).datepicker( 'show' );
	});

	// Remove current selected date
	$( document ).on( 'click', '.js-wpv-date-front-end-clear', function(e) {
		e.preventDefault();
		var thiz = $( this ),
		url_param = thiz.data( 'param' ),
		form = thiz.closest( 'form' );
		form.find( '.js-wpv-date-param-' + url_param ).html( '' );
		form.find( '.js-wpv-date-front-end-' + url_param ).val( '' );
		thiz.hide();
		form.find('.js-wpv-date-param-' + url_param + '-value' )
			.val( '' )
			.trigger( 'change' );
	});
	
	$( document ).on( 'change', '.js-wpv-post-relationship-update', function() {
		var thiz = $( this ),
		fil = thiz.closest( 'form' ),
		view_number = fil.data( 'viewnumber' ),
		additional_forms = $( '.js-wpv-filter-form-' + view_number ).not( fil ),
		currentposttype = thiz.data( 'currentposttype' ),
		watchers = fil.find( '.js-wpv-' + currentposttype + '-watch' ).add( additional_forms.find( '.js-wpv-' + currentposttype + '-watch' ) ),
		watcherslength = watchers.length,
		i;
		if ( watcherslength ) {
			for( i = 0; i < watcherslength; i++ ) {
				$( watchers[i] )
					.attr( 'disabled', true )
					.removeAttr( 'checked' )
					.removeAttr( 'selected' )
					.not( ':button, :submit, :reset, :hidden, :radio, :checkbox' )
					.val( '0' );
			}
		}
		$( document ).trigger( 'js_event_wpv_parametric_search_triggered', [ { view_unique_id: view_number, form: fil, force_form_update: true } ] );
	});

	$( document ).on( 'change', '.js-wpv-filter-trigger', function() {
		var thiz = $( this ),
		fil = thiz.closest( 'form' ),
		view_number = fil.data( 'viewnumber' );
		$( document ).trigger( 'js_event_wpv_parametric_search_triggered', [ { view_unique_id: view_number, form: fil } ] );
	});

	$( document ).on( 'click', '.js-wpv-ajax-results-enabled .js-wpv-submit-trigger, .js-wpv-ajax-results-submit-enabled .js-wpv-submit-trigger', function( e ) {
		e.preventDefault();
		var thiz = $( this ),
		fil = thiz.closest( 'form' ),
		view_number = fil.data( 'viewnumber' );
		$( document ).trigger( 'js_event_wpv_parametric_search_triggered', [ { view_unique_id: view_number, form: fil, force_form_update: false, force_results_update: true } ] );
	});

	$( document).on( 'keypress', '.js-wpv-ajax-results-enabled .js-wpv-filter-trigger-delayed, .js-wpv-ajax-results-submit-enabled .js-wpv-filter-trigger-delayed', function( e ) {
		// Enter pressed?
		if ( e.which == 13 ) {
			e.preventDefault();
			var thiz = $( this ),
			fil = thiz.closest( 'form' ),
			view_number = fil.data( 'viewnumber' );
			$( document ).trigger( 'js_event_wpv_parametric_search_triggered', [ { view_unique_id: view_number, form: fil } ] );
		}
	});

	$( document ).on( 'click', '.js-wpv-reset-trigger', function( e ) {
		e.preventDefault();
		var thiz = $( this ),
		fil = thiz.closest( 'form' ),
		view_number = fil.data( 'viewnumber' ),
		additional_forms = $( '.js-wpv-filter-form-' + view_number ).not( fil ),
		watchers,
		watcherslength,
		i,
		target = fil.attr( 'action' );
		if ( fil.hasClass( 'js-wpv-ajax-results-enabled' ) || fil.hasClass( 'js-wpv-ajax-results-submit-enabled' ) ) {
			watchers = fil.find( 'input, select' ).add( additional_forms.find( 'input, select' ) );
			watcherslength = watchers.length;
			if ( watcherslength ) {
				for ( i = 0; i < watcherslength; i++ ) {
					if ( ! $( watchers[i] ).hasClass( 'js-wpv-keep-on-clear' ) ) {
						$( watchers[i] )
							.attr( 'disabled', true )
							.removeAttr( 'checked' )
							.removeAttr( 'selected' )
							.not( ':button, :submit, :reset, :hidden, :radio, :checkbox' )
							.val( '' );
					}
				}
			}
			$( document ).trigger( 'js_event_wpv_parametric_search_triggered', [ { view_unique_id: view_number, form: fil, force_form_update: true, force_results_update: true } ] );
		} else {
			window.location.href = target;
		}
	});
	
	$( document ).on( 'js_event_wpv_parametric_search_triggered', function( event, data ) {
		var defaults = { 
			force_form_update: false, 
			force_results_update: false
		},
		settings = $.extend( {}, defaults, data );
		self.manage_changed_form( settings.form, settings.force_form_update, settings.force_results_update );
	});

	// Also, stop the rollover if we do any modification on the parametric search form

	$( document ).on( 'change', '.js-wpv-filter-trigger, .js-wpv-filter-trigger-delayed', function() {
		var thiz = $( this ),
		fil = thiz.closest( 'form' ),
		view_num = fil.data( 'viewnumber' );
		wpv_stop_rollover[view_num] = true;
		self.dependant_form_clear_pagination_cache( view_num );
	});
	
	// ------------------------------------
	// Custom events
	// ------------------------------------
	
	/**
	* js_event_wpv_parametric_search_started
	*
	* Event fired before updating the parametric search forms and results.
	*
	* @param data
	* 	- view_unique_id
	*
	* @since 1.9
	*/
	
	$( document ).on( 'js_event_wpv_parametric_search_started', function( event, data ) {
		
	});
	
	
	/**
	* js_event_wpv_parametric_search_form_updated
	*
	* Event fired after updating the parametric search forms.
	*
	* @param data
	* 	- view_unique_id
	* 	- view_changed_form
	* 	- view_changed_form_additional_forms_only
	* 	- view_changed_form_additional_forms_full
	*
	* @since 1.9
	*/
	
	$( document ).on( 'js_event_wpv_parametric_search_form_updated', function( event, data ) {
		WPViews.view_frontend_utils.render_frontend_datepicker();
	});
	
	/**
	* js_event_wpv_parametric_search_results_updated
	*
	* Event fired after updating the parametric search results.
	*
	* @param data
	* 	- view_unique_id
	* 	- layout
	*
	* @since 1.9
	*/
	
	$( document ).on( 'js_event_wpv_parametric_search_results_updated', function( event, data ) {
		WPViews.view_frontend_utils.render_frontend_media_shortcodes( data.layout );
	});
	
	// ------------------------------------
	// Init
	// ------------------------------------
	
	self.init = function() {
		
	}
	
	self.init();

};

jQuery( document ).ready( function( $ ) {
	WPViews.view_frontend_utils = new WPViews.ViewFrontendUtils( $ );
	WPViews.view_pagination = new WPViews.ViewPagination( $ );
    WPViews.view_parametric_search = new WPViews.ViewParametricSearch( $ );
});