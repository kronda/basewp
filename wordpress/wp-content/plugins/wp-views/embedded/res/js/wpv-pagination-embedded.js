var wpv_stop_rollover = {};
window.wpvPaginationAjaxLoaded = {};
window.wpvPaginationAnimationFinished = {};
window.wpvPaginationQueue = {};

//////////////////////////////////////////////////////////////////////////
// General helper functions
//////////////////////////////////////////////////////////////////////////

function add_url_query_parameters( data ) {
	var qs = ( function( a ) {
		if ( a === "" ) {
			return {};
		}
		var b = {},
		alength = a.length,
		i = 0,
		p = '';
		for ( i = 0; i < alength; ++i ) {
			p = a[i].split( '=' );
			if ( p.length !== 2 ) {
				continue;
			}
			p[0] = p[0].replace( "[]", "" ); // needed for pagination on the author filter to work
			if ( b.hasOwnProperty( p[0] ) ) {
				if ( b[p[0]] !== decodeURIComponent( p[1].replace( /\+/g, " " ) ) ) {
					b[p[0]] += ',' + decodeURIComponent( p[1].replace( /\+/g, " " ) );
				} else {
					b[p[0]] = decodeURIComponent( p[1].replace( /\+/g, " " ) );
				}
			} else {
				b[p[0]] = decodeURIComponent( p[1].replace( /\+/g, " " ) );
			}
		}
		return b;
	}( window.location.search.substr( 1 ).split( '&' ) ) );
	data['get_params'] = {};
	var prop = '',
	prop2 = '',
	qslength = qs.length,
	j = 0;
	//for ( var prop in qs ) {
	for ( j = 0; j < qslength; ++j ) {
		prop = qs[j];
		if ( qs.hasOwnProperty( prop ) ) {
			if ( !data.hasOwnProperty( prop ) ) {
				prop2 = prop.replace( "%5B%5D", "" );
				data['get_params'][prop2] = qs[prop];
			}
		}
	}
    return data;
}

function utf8_encode(argString) {
	// http://kevin.vanzonneveld.net
	// +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   improved by: sowberry
	// +    tweaked by: Jack
	// +   bugfixed by: Onno Marsman
	// +   improved by: Yves Sucaet
	// +   bugfixed by: Onno Marsman
	// +   bugfixed by: Ulrich
	// +   bugfixed by: Rafal Kukawski
	// +   improved by: kirilloid
	// *     example 1: utf8_encode('Kevin van Zonneveld');
	// *     returns 1: 'Kevin van Zonneveld'
	
	if (argString === null || typeof argString === "undefined") {
		return "";
	}
	
	var string = (argString + ''); // .replace(/\r\n/g, "\n").replace(/\r/g, "\n");
	var utftext = '',
	start, end, stringl = 0;
	
	start = end = 0;
	stringl = string.length;
	for (var n = 0; n < stringl; n++) {
		var c1 = string.charCodeAt(n);
		var enc = null;
		
		if (c1 < 128) {
			end++;
		} else if (c1 > 127 && c1 < 2048) {
			enc = String.fromCharCode((c1 >> 6) | 192, (c1 & 63) | 128);
		} else {
			enc = String.fromCharCode((c1 >> 12) | 224, ((c1 >> 6) & 63) | 128, (c1 & 63) | 128);
		}
		if (enc !== null) {
			if (end > start) {
				utftext += string.slice(start, end);
			}
			utftext += enc;
			start = end = n + 1;
		}
	}
	
	if (end > start) {
		utftext += string.slice(start, stringl);
	}
	
	return utftext;
}

/**
 * Converts the given data structure to a JSON string.
 * Argument: arr - The data structure that must be converted to JSON
 * Example: var json_string = array2json(['e', {pluribus: 'unum'}]);
 * 			var json = array2json({"success":"Sweet","failure":false,"empty_array":[],"numbers":[1,2,3],"info":{"name":"Binny","site":"http:\/\/www.openjs.com\/"}});
 * http://www.openjs.com/scripts/data/json_encode.php
 */
function encodeToHex(str){
    var r="";
    var e=str.length;
    var c=0;
    var h;
    while(c<e){
        h=str.charCodeAt(c++).toString(16);
        while(h.length<2) h="0"+h;
        r+=h;
    }
    return r;
}

/**
 * Converts the given data structure to a JSON string.
 * Argument: arr - The data structure that must be converted to JSON
 * Example: var json_string = array2json(['e', {pluribus: 'unum'}]);
 * 			var json = array2json({"success":"Sweet","failure":false,"empty_array":[],"numbers":[1,2,3],"info":{"name":"Binny","site":"http:\/\/www.openjs.com\/"}});
 * http://www.openjs.com/scripts/data/json_encode.php
 */
function array2json(arr) {
    var parts = [];
    var is_list = (Object.prototype.toString.apply(arr) === '[object Array]');

    for(var key in arr) {
    	var value = arr[key];
        if(typeof value == "object") { //Custom handling for arrays
            if(is_list) parts.push(array2json(value)); /* :RECURSION: */
            else parts.push('"' + key + '":' + array2json(value)); /* :RECURSION: */
        } else {
            var str = "";
            if(!is_list) str = '"' + key + '":';

            //Custom handling for multiple data types
            if(typeof value == "number") str += value; //Numbers
            else if(value === false) str += 'false'; //The booleans
            else if(value === true) str += 'true';
	    else str += '"' + utf8_encode(value) + '"'; //All other things
            // :TODO: Is there any more datatype we should be in the lookout for? (Functions?)

            parts.push(str);
        }
    }
    var json = parts.join(",");
    
    if(is_list) return '[' + json + ']';//Return numerical JSON
    return '{' + json + '}';//Return associative JSON
}


function wpv_serialize_array(data) {
	
    return encodeToHex(array2json(data));
}

//////////////////////////////////////////////////////////////////////////
// Datepicker script
//////////////////////////////////////////////////////////////////////////

function wpv_render_frontend_datepicker() {
	try {
		jQuery( ".wpv-date-front-end" ).datepicker({
			onSelect: function( dateText, inst ) {
			//	var control = this,
				var url_param = jQuery( this ).data( 'param' ),
				data = 'date=' + dateText;
				data += '&date-format=' + jQuery( '.js-wpv-date-param-' + url_param + '-format' ).val();
				data += '&action=wpv_format_date';
				jQuery.post( front_ajaxurl, data, function( response ) {
					response = jQuery.parseJSON( response );
					jQuery( '.js-wpv-date-param-' + url_param + '-value' ).val( response['timestamp'] ).trigger( 'change' );
					jQuery( '.js-wpv-date-param-' + url_param ).html( response['display'] );
				});
			},
			dateFormat: 'ddmmyy',
			showOn: "button",
			buttonImage: wpv_calendar_image,
			buttonText: wpv_calendar_text,
			buttonImageOnly: true,
			changeMonth: true,
			changeYear: true
		});
	}
	catch ( ignore ) {
		
	}
}

jQuery( document ).on( 'click', '.js-wpv-date-display', function() {
	var url_param = jQuery( this ).data( 'param' );
	jQuery( '.js-wpv-date-front-end-' + url_param ).datepicker( 'show' );
});

//////////////////////////////////////////////////////////////////////////
// Helper functions for pagination
//////////////////////////////////////////////////////////////////////////

function wpv_get_ajax_pagination_url( data ) {
	var url;
	if ( wpv_ajax_pagination_url.slice( -'.php'.length ) === '.php' ) {
		url = wpv_ajax_pagination_url + '?wpv-ajax-pagination=' + wpv_serialize_array( data );
	} else {
		url = wpv_ajax_pagination_url + wpv_serialize_array( data );
	}
	return url;
}

function add_view_parameters( data, page, view_number ) {
	data['action'] = 'wpv_get_page';
	data['page'] = page;
	data['view_number'] = view_number;
	var this_form = jQuery( 'form.js-wpv-filter-form-' + view_number );
	data['wpv_column_sort_id'] = this_form.find( 'input[name=wpv_column_sort_id]' ).val();
	data['wpv_column_sort_dir'] = this_form.find( 'input[name=wpv_column_sort_dir]' ).val();
	data['wpv_view_widget_id'] = jQuery( '#wpv_widget_view-' + view_number ).val();
	data['view_hash'] = jQuery( '#wpv_view_hash-' + view_number ).val();
	if ( this_form.find( 'input[name=wpv_post_id]' ).length > 0 ) {
		data['post_id'] = this_form.find( 'input[name=wpv_post_id]' ).val();
	}
	data['dps_pr'] = {};
	data['dps_general'] = {};
	var this_prelements = this_form.find( '.js-wpv-post-relationship-update' );
	if ( this_prelements.length ) {
		data['dps_pr'] = this_prelements.serializeArray();
	}
	if ( this_form.hasClass( 'js-wpv-dps-enabled' ) || this_form.hasClass( 'js-wpv-ajax-results-enabled' ) ) {
		data['dps_general'] = this_form.find( '.js-wpv-filter-trigger' ).serializeArray();
	}
	return data;
}

//////////////////////////////////////////////////////////////////////////
// Functions for pagination
//////////////////////////////////////////////////////////////////////////

function wpv_pagination_replace_view( view_number, page, ajax, effect, max_pages, cache_pages, preload_pages, spinner, spinner_image, callback_next, stop_rollover ) {
	if ( ! window.wpvPaginationAnimationFinished.hasOwnProperty( view_number ) ) {
//	if (!(view_number in window.wpvPaginationAnimationFinished)) {
		window.wpvPaginationAnimationFinished[view_number] = false;
	} else if ( window.wpvPaginationAnimationFinished[view_number] !== true ) {
		if ( ! window.wpvPaginationQueue.hasOwnProperty( view_number ) ) {
	//	if (!(view_number in window.wpvPaginationQueue)) {
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
	datalength,
	count,
	prop,
	wpvPaginatorLayout = jQuery( '#wpv-view-layout-' + view_number ),
	wpvPaginatorFilter = jQuery( 'form[name=wpv-filter-' + view_number + ']' ),
	speed = 500,
	next = true,
	max_reach = 1,
	img;
	if ( ajax !== true ) {
		// add elements for the current url parameters
		// So any views that filter by url parameters will still work.
		data = {};
		data = add_url_query_parameters( data );
		datalength = data['get_params'].length;
		for ( count = 0; count < datalength; count++ ) {
	//	for (var prop in data['get_params']) {
			prop = data['get_params'][count];
			if ( ! ( jQuery( 'form[name=wpv-filter-' + view_number + '] > input[name=' + prop + ']' ).length > 0 ) ) {
				jQuery( '<input>' ).attr({
						type: 'hidden',
						name: prop,
						value: data['get_params'][prop]
					})
					.appendTo( 'form[name=wpv-filter-' + view_number + ']' );
			}
		}
		if ( jQuery( 'input[name=wpv_paged]' ).length > 0 ) {
			jQuery( 'input[name=wpv_paged]' ).attr( 'value', page );
		} else {
			jQuery( '<input>').attr({
					type: 'hidden',
					name: 'wpv_paged',
					value: page
				})
				.appendTo( 'form[name=wpv-filter-' + view_number + ']' );
		}
		wpvPaginatorFilter[0].submit();
		return false;
	}
	// add url sorting parameters to allow custom sorting using ajax and table sorting parameters
	data = add_url_query_parameters( data );
	datalength = data['get_params'].length;
	for ( count = 0; count < datalength; count++ ) {
//	for (var prop in data['get_params']) {
		prop = data['get_params'][count];
		if ( ! ( jQuery( 'form[name=wpv-filter-' + view_number + '] > input[name=' + prop + ']' ).length > 0 ) ) {
			jQuery( '<input>' ).attr({
					type: 'hidden',
					name: prop,
					value: data['get_params'][prop]
				})
			.appendTo( 'form[name=wpv-filter-' + view_number + ']' );
		}
	}
	window.wpvPaginationAjaxLoaded[view_number] = false;
//    if (typeof this.historyP == 'undefined' ) {
//       this.historyP = [];
//  }
//    if (typeof window.wpvCachedPages == 'undefined' ) {
//       window.wpvCachedPages = new Array();
//  }
//    if (typeof window.wpvCachedPages[view_number] == 'undefined' ) {
//       window.wpvCachedPages[view_number] = new Array();
//  }
	this.historyP = ( typeof this.historyP == 'undefined' ) ? [] : this.historyP;
	window.wpvCachedPages = ( typeof window.wpvCachedPages == 'undefined' ) ? [] : window.wpvCachedPages;
	window.wpvCachedPages[view_number] = ( typeof window.wpvCachedPages[view_number] == 'undefined' ) ? [] : window.wpvCachedPages[view_number];
	if ( effect === 'fadeslow' ) {
		speed = 1500; 
	} else if (effect === 'fadefast') {
		speed = 1;
	}
	if ( wpvPaginatorLayout.data( 'duration' ) ) {
		if ( wpvPaginatorLayout.data('duration') !== "" && jQuery.isNumeric( wpvPaginatorLayout.data( 'duration' ) ) ) {
			speed = wpvPaginatorLayout.data( 'duration' );
			speed = parseFloat( speed );
		}
	}
	if ( this.historyP.hasOwnProperty( view_number ) ) {
		next = ( this.historyP[view_number] < page ) ? true : false;
	}
	if ( jQuery( '#wpv_paged_preload_reach-'+view_number ).val() ) {
		max_reach = jQuery( '#wpv_paged_preload_reach-'+view_number ).val();
	}
	max_reach++;
	if ( max_reach > max_pages ) {
		max_reach = max_pages;
	}
	if ( ( cache_pages || preload_pages ) && window.wpvCachedPages[view_number].hasOwnProperty( page ) ) {
		wpv_pagination_get_page( view_number, next, effect, speed, window.wpvCachedPages[view_number][page], wpvPaginatorLayout, wpvPaginatorFilter, callback_next );
		wpv_pagination_preload_pages( view_number, page, max_pages, cache_pages, preload_pages, max_reach );
	} else {
		// Set loading class
		if ( spinner !== 'no' ) {
			img = new Image();
			img.src = spinner_image;
			img.onload = function() {
				var wpvPaginatorLayoutOffset = wpvPaginatorLayout.position();
				wpvPaginatorLayout
				.before( '<div style="width:32px;height:32px;border:1px solid #6D6D6D;background:#FFFFFF 50% 50% no-repeat url(' + spinner_image + ');position:absolute;z-index:99;top:' + ( Math.round( wpvPaginatorLayoutOffset.top ) + ( Math.round( wpvPaginatorLayout.height()/2 ) ) - img.height ) + 'px; left:' + ( Math.round( wpvPaginatorLayoutOffset.left ) + ( Math.round( wpvPaginatorLayout.width()/2 ) ) -img.width ) + 'px;" id="wpv_slide_loading_img_' + view_number + '" class="wpv_slide_loading_img"></div>' )
				.animate( {opacity:0.5}, 300 );
			};
		}
		data = add_view_parameters( data, page, view_number );
		data = add_url_query_parameters( data );
		icl_lang = ( typeof icl_lang == 'undefined' ) ? false : icl_lang;
		if ( icl_lang !== false ) {
				data['lang'] = icl_lang;
			}
		jQuery.get( wpv_get_ajax_pagination_url( data ), function(response) {
			wpv_pagination_get_page( view_number, next, effect, speed, response, wpvPaginatorLayout, wpvPaginatorFilter, callback_next );
		});
		wpv_pagination_preload_pages( view_number, page, max_pages, cache_pages, preload_pages, max_reach );
	}
	this.historyP[view_number] = page;
	return false;
}

function wpv_pagination_replace_view_links( view_number, page, ajax, effect, max_pages, cache_pages, preload_pages, spinner, spinner_image, callback_next, stop_rollover ) {
	var i;// TODO this can be improved: we should not need a loop here at all
	for ( i = 1; i <= max_pages; i++ ) {
		if ( i === page ) {
			jQuery( '#wpv-page-link-' + view_number + '-' + i ).addClass( 'wpv_page_current' );
		} else {
			jQuery( '#wpv-page-link-' + view_number + '-' + i ).removeClass( 'wpv_page_current' );
		}
		
	}
	wpv_pagination_replace_view( view_number, page, ajax, effect, max_pages, cache_pages, preload_pages, spinner, spinner_image, callback_next, stop_rollover );
}

function wpvPaginationQueueTrigger( view_number, next, wpvPaginatorFilter ) {
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
		wpv_pagination_replace_view( view_number, page, args[2], args[3], args[4], args[5], args[6], args[7], args[8], args[10] );
	}
}

function wpv_pagination_slide( view_number, width, height, next, effect, speed, responseView, wpvPaginatorLayout, wpvPaginatorFilter, callback_next ) {
	// !TODO clean the with/height parameters as at least one is not needed
	var old_height,
	new_height,
	is_callback = false;
	if ( callback_next !== '' ) {
		if ( eval( 'typeof(' + callback_next + ') === \'function\'' ) ) {
			is_callback = true;
		}
	}
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
			jQuery( '#wpv_slide_loading_img_'+view_number ).fadeOut(function() {
				jQuery( this ).remove();
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
						eval( callback_next + '();' );
					}
					wpvPaginationQueueTrigger( view_number, next, wpvPaginatorFilter );
				});
			} else if ( old_height > new_height ) {
				wpvPaginatorLayout.parent().animate( {marginLeft: '-'+wpvPaginatorLayout.outerWidth()+'px'}, speed+500, function() {
					wpvPaginatorLayout.parent().parent().animate( {height: responseView.outerHeight()+'px'}, speed/2, function() {
						responseView.css( {'position': 'static', 'float': 'none'} );
						wpvPaginatorLayout.unwrap().unwrap().remove();
						window.wpvPaginationAjaxLoaded[view_number] = true;
						window.wpvPaginationAnimationFinished[view_number] = true;
						if ( is_callback ) {
							eval( callback_next + '();' );
						}
						wpvPaginationQueueTrigger( view_number, next, wpvPaginatorFilter );
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
							eval( callback_next + '();' );
						}
						wpvPaginationQueueTrigger( view_number, next, wpvPaginatorFilter );
					});
				});
			}
		} else {
			wpvPaginatorLayout.css( 'float', 'right' );
			responseView.css( {'float': 'right', 'visibility': 'visible'} );
			wpvPaginatorLayout.after( responseView ).parent().children().wrapAll( '<div style="height:' + height +  ';width:' + ( responseView.outerWidth() + wpvPaginatorLayout.outerWidth() ) + 'px; margin-left:-' + ( wpvPaginatorLayout.outerWidth() ) + 'px;" />' );
			jQuery( '#wpv_slide_loading_img_' + view_number ).fadeOut( function() {
				jQuery( this ).remove();
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
						eval( callback_next + '();' );
					}
					wpvPaginationQueueTrigger( view_number, next, wpvPaginatorFilter );
				});
			} else if ( old_height > new_height ) {
				wpvPaginatorLayout.parent().animate( {marginLeft: '0px'}, speed+500, function() {
					wpvPaginatorLayout.parent().parent().animate( {height: responseView.outerHeight()+'px'}, speed/2, function() {
						responseView.css( {'position': 'static', 'margin': '0px', 'float': 'none'} );
						wpvPaginatorLayout.unwrap().unwrap().remove();
						window.wpvPaginationAjaxLoaded[view_number] = true;
						window.wpvPaginationAnimationFinished[view_number] = true;
						if ( is_callback ) {
							eval( callback_next + '();' );
						}
						wpvPaginationQueueTrigger( view_number, next, wpvPaginatorFilter );
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
							eval( callback_next + '();' );
						}
						wpvPaginationQueueTrigger( view_number, next, wpvPaginatorFilter );
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
			jQuery( '#wpv_slide_loading_img_' + view_number ).fadeOut( function(){
				jQuery( this ).remove();
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
						eval( callback_next + '();' );
					}
					wpvPaginationQueueTrigger( view_number, next, wpvPaginatorFilter );
				});
			} else if ( old_height > new_height ) {
				wpvPaginatorLayout.parent().animate( {marginTop: '-'+old_height+'px'}, speed+500, function() {
					wpvPaginatorLayout.parent().parent().animate( {height: responseView.outerHeight()+'px'}, speed/2, function() {
						responseView.css( {'position': 'static', 'margin': '0px'} );
						wpvPaginatorLayout.unwrap().unwrap().remove();
						window.wpvPaginationAjaxLoaded[view_number] = true;
						window.wpvPaginationAnimationFinished[view_number] = true;
						if ( is_callback ) {
							eval( callback_next + '();' );
						}
						wpvPaginationQueueTrigger( view_number, next, wpvPaginatorFilter );
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
							eval( callback_next + '();' );
						}
						wpvPaginationQueueTrigger( view_number, next, wpvPaginatorFilter );
					});
				});
			}
		} else {
			responseView.css( 'visibility', 'visible' );
			wpvPaginatorLayout.before( responseView ).parent().children().wrapAll( '<div />' );
			jQuery( '#wpv_slide_loading_img_' + view_number ).fadeOut( function() {
				jQuery( this ).remove();
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
						eval( callback_next + '();' );
					}
					wpvPaginationQueueTrigger( view_number, next, wpvPaginatorFilter );
				});
			} else if ( old_height > new_height ) {
				wpvPaginatorLayout.parent().animate( {marginTop: '0px'}, speed+500, function() {
					wpvPaginatorLayout.parent().parent().animate( {height: responseView.outerHeight()+'px'}, speed/2, function() {
						responseView.css( {'position': 'static', 'margin': '0px'} );
						wpvPaginatorLayout.unwrap().unwrap().remove();
						window.wpvPaginationAjaxLoaded[view_number] = true;
						window.wpvPaginationAnimationFinished[view_number] = true;
						if ( is_callback ) {
							eval( callback_next + '();' );
						}
						wpvPaginationQueueTrigger( view_number, next, wpvPaginatorFilter );
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
							eval( callback_next + '();' );
						}
						wpvPaginationQueueTrigger( view_number, next, wpvPaginatorFilter );
					});
				});
			}
		}
	} else { // Fade
		jQuery( '#wpv_slide_loading_img_' + view_number ).fadeOut( function() {
			jQuery( this ).remove();
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
					eval( callback_next + '();' );
				}
				wpvPaginationQueueTrigger( view_number, next, wpvPaginatorFilter );
			});
			responseView.hide().css( 'visibility', 'visible' ).fadeIn( speed );
		} else {
			wpvPaginatorLayout.fadeOut( speed, function() {
				wpvPaginatorLayout.parent().animate( {height: new_height+'px'}, speed, function() {
					wpvPaginatorLayout.unwrap().remove();
					window.wpvPaginationAjaxLoaded[view_number] = true;
					window.wpvPaginationAnimationFinished[view_number] = true;
					if ( is_callback ) {
						eval( callback_next + '();' );
					}
					wpvPaginationQueueTrigger( view_number, next, wpvPaginatorFilter );
					responseView.hide().css( 'visibility', 'visible' ).fadeIn( speed );
				});
			});
		}
	}
}

function wpv_pagination_load_next_page( view_number, page, max_pages, reach ) {
//	if ( typeof window.wpvCachedPages == 'undefined' ) {
//		window.wpvCachedPages = [];
//	}
	window.wpvCachedPages = ( typeof window.wpvCachedPages == 'undefined' ) ? [] : window.wpvCachedPages;
//	if ( typeof window.wpvCachedImages === 'undefined' ) {
//		window.wpvCachedImages = [];
//	}
	window.wpvCachedImages = ( typeof window.wpvCachedImages == 'undefined' ) ? [] : window.wpvCachedImages;
//	if ( typeof window.wpvCachedPages[view_number] === 'undefined' ) {
//		window.wpvCachedPages[view_number] = [];
//	}
	window.wpvCachedPages[view_number] = ( typeof window.wpvCachedPages[view_number] == 'undefined' ) ? [] : window.wpvCachedPages[view_number];
	var next_page = page + reach;
	icl_lang = ( typeof icl_lang == 'undefined' ) ? false : icl_lang;
//	if ( next_page in window.wpvCachedPages[view_number] ) {
	if ( ! window.wpvCachedPages[view_number].hasOwnProperty( next_page ) ) {
//     return false;
//	} else {
	// LOAD NEXT
		if ( ( next_page - 1 ) < max_pages ) {
			var dataNext = {};
			dataNext = add_view_parameters( dataNext, next_page, view_number );
			dataNext = add_url_query_parameters( dataNext );
		//	if ( typeof( icl_lang ) !== 'undefined' ) {
		//		dataNext['lang'] = icl_lang;
		//	}
			if ( icl_lang !== false ) {
				dataNext['lang'] = icl_lang;
			}
			jQuery.get( wpv_get_ajax_pagination_url( dataNext ), function( response ) {
				window.wpvCachedPages[view_number][next_page] = response;
				var content = jQuery( response ).find( 'img' );
				content.each( function() {
					window.wpvCachedImages.push( this.src );
				});
			});
		}
	}
}

function wpv_pagination_load_previous_page(view_number, page, max_pages, reach) {
//	if ( typeof window.wpvCachedPages == 'undefined' ) {
//		window.wpvCachedPages = [];
//	}
	window.wpvCachedPages = ( typeof window.wpvCachedPages == 'undefined' ) ? [] : window.wpvCachedPages;
//	if ( typeof window.wpvCachedImages === 'undefined' ) {
//		window.wpvCachedImages = [];
//	}
	window.wpvCachedImages = ( typeof window.wpvCachedImages == 'undefined' ) ? [] : window.wpvCachedImages;
//	if ( typeof window.wpvCachedPages[view_number] === 'undefined' ) {
//		window.wpvCachedPages[view_number] = [];
//	}
	window.wpvCachedPages[view_number] = ( typeof window.wpvCachedPages[view_number] == 'undefined' ) ? [] : window.wpvCachedPages[view_number];
    var previous_page = page - reach,
	dataPrevious = {},
	content;
	icl_lang = ( typeof icl_lang == 'undefined' ) ? false : icl_lang;
//    if (previous_page in window.wpvCachedPages[view_number]) {
 //       return false;
//    } else {
	if ( ! window.wpvCachedPages[view_number].hasOwnProperty( previous_page ) ) {
		// LOAD PREVIOUS
		if ( ( previous_page + 1 ) > 1 ) {
			dataPrevious = add_view_parameters( dataPrevious, previous_page, view_number );
			dataPrevious = add_url_query_parameters( dataPrevious );
			if ( icl_lang !== false ) {
				dataPrevious['lang'] = icl_lang;
			}
			jQuery.get( wpv_get_ajax_pagination_url( dataPrevious ), function( response ) {
			window.wpvCachedPages[view_number][previous_page] = response;
			content = jQuery( response ).find( 'img' );
				content.each( function() {
					window.wpvCachedImages.push( this.src );
				});
			});
		} else if ( (previous_page + 1 ) === 1 ) { // LOAD LAST PAGE IF ON FIRST PAGE
			dataPrevious = add_view_parameters( dataPrevious, max_pages, view_number );
			dataPrevious = add_url_query_parameters( dataPrevious );
			if ( icl_lang !== false ) {
				dataPrevious['lang'] = icl_lang;
			}
			jQuery.get( wpv_get_ajax_pagination_url( dataPrevious ), function( response ) {
				window.wpvCachedPages[view_number][max_pages] = response;
				window.wpvCachedPages[view_number][0] = response;
				content = jQuery( response ).find( 'img' );
				content.each( function() {
					window.wpvCachedImages.push( this.src );
				});
			});
		}
	}
}

function wpv_pagination_get_page( view_number, next, effect, speed, response, wpvPaginatorLayout, wpvPaginatorFilter, callback_next ) {
	var width = wpvPaginatorLayout.width(),
	outer_width = wpvPaginatorLayout.outerWidth(),
	height = wpvPaginatorLayout.height(),
	outer_height = wpvPaginatorLayout.outerHeight(),
	responseObj = jQuery( '<div></div>' ).append( response ),
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
			wpv_pagination_slide( view_number, width, height, next, effect, speed, responseView, wpvPaginatorLayout, wpvPaginatorFilter, callback_next );
		} else {
			images.one( 'load', function() {
				preloadedImages.push( jQuery( this ).attr( 'src' ) );
				if ( preloadedImages.length === images.length ) {
					wpv_pagination_slide( view_number, width, height, next, effect, speed, responseView, wpvPaginatorLayout, wpvPaginatorFilter, callback_next );
				}
			}).each( function() {
				jQuery( this ).load();
			});
		}
	} else {
		wpv_pagination_slide( view_number, width, height, next, effect, speed, responseView, wpvPaginatorLayout, wpvPaginatorFilter, callback_next );
	}
	wpvPaginatorFilter.html( responseFilter );
	wpv_render_frontend_datepicker();
	// Move the wpv_view_hash, wpv_paged_max and wpv_widget_view_id from the forms as it's only needed during ajax pagination
	jQuery( 'input[id=wpv_view_hash-' + view_number + '], input[id=wpv_paged_max-' + view_number + '], input[id=wpv_widget_view-' + view_number + ']' ).each( function(index) {
		parent = jQuery( this ).parent();
		if ( !parent.is( 'form' ) ) {
			jQuery( this ).remove();
		} else {
			parent.after( this );
		}
	});
}

//////////////////////////////////////////////////////////////////////////
// Preload for pagination
//////////////////////////////////////////////////////////////////////////

function wpv_pagination_init_preload_images() {
	jQuery( '.wpv-pagination-preload-images' ).each( function() {
		var preloadedImages = [],
		element = jQuery( this ),
		images = element.find( 'img' );
		if ( images.length < 1 ) {
			element.css( 'visibility', 'visible' );
		} else {
			images.one( 'load', function() {
				preloadedImages.push( jQuery( this ).attr( 'src' ) );
				if ( preloadedImages.length === images.length ) {
					element.css( 'visibility', 'visible' );
				}
			}).each( function() {
				if( this.complete ) {
					jQuery( this ).load();
				}
			});
			setTimeout( function() {
				element.css( 'visibility', 'visible' );
			}, 3000 );
		}
	});
}

function wpv_pagination_cache_current_page( view_number, page ) {
//	if ( typeof window.wpvCachedPages == 'undefined' ) {
//		window.wpvCachedPages = [];
//	}
	window.wpvCachedPages = ( typeof window.wpvCachedPages == 'undefined' ) ? [] : window.wpvCachedPages;
//	if ( typeof window.wpvCachedImages === 'undefined' ) {
//		window.wpvCachedImages = [];
//	}
	window.wpvCachedImages = ( typeof window.wpvCachedImages == 'undefined' ) ? [] : window.wpvCachedImages;
//	if ( typeof window.wpvCachedPages[view_number] === 'undefined' ) {
//		window.wpvCachedPages[view_number] = [];
//	}
	window.wpvCachedPages[view_number] = ( typeof window.wpvCachedPages[view_number] == 'undefined' ) ? [] : window.wpvCachedPages[view_number];
	var dataCurrent = {},
	content;
	icl_lang = ( typeof icl_lang == 'undefined' ) ? false : icl_lang;
	if ( ! window.wpvCachedPages[view_number].hasOwnProperty( page ) ) {
//        return false;
//    }
	// Cache current page
//    if (page in window.wpvCachedPages[view_number] == false) {
		dataCurrent = add_view_parameters( dataCurrent, page, view_number );
		dataCurrent = add_url_query_parameters( dataCurrent );
		if ( icl_lang !== false ) {
			dataCurrent['lang'] = icl_lang;
		}
		jQuery.get( wpv_get_ajax_pagination_url( dataCurrent ), function( response ) {
			window.wpvCachedPages[view_number][page] = response;
			content = jQuery( response ).find( 'img' );
			content.each( function() {
				window.wpvCachedImages.push( this.src );
			});
		});
	}
}

function wpv_pagination_preload_pages( view_number, page, max_pages, cache_pages, preload_pages, reach_max ) {
	page = parseInt( page, 10 );
	max_pages = parseInt( max_pages, 10 );
	reach_max = parseInt( reach_max, 10 );
	if ( preload_pages ) {
		var reach = 1;
		while ( reach < reach_max ) {
			wpv_pagination_load_next_page( view_number, page, max_pages, reach );
			wpv_pagination_load_previous_page( view_number, page, max_pages, reach );
		reach++;
		}
	}
	if ( cache_pages ) {
		wpv_pagination_cache_current_page( view_number, page );
	}
}

//////////////////////////////////////////////////////////////////////////
// Adjust URL params for tablecolumn sort
//////////////////////////////////////////////////////////////////////////

function wpv_add_url_controls_for_column_sort( form ) {
	var data = [],
	param,
	datalength,
	l = 0;
	data = add_url_query_parameters( data );
	datalength = data['get_params'].length;
//	for ( var param in data['get_params'] ) {
	for ( l = 0; l < datalength; l++ ) {
		param = data['get_params'][l];
		if ( data['get_params'].hasOwnProperty( param ) ) {
			if ( form.find( 'input[name=' + param + ']' ).length === 0 ) {
				// we need to add an input element
				form.append( '<input type="hidden" name="' + param + '" value="' + data['get_params'][param] + '" />' );
			}
		}
	}
}

/////////////////////////////////////////////////////////////////////

jQuery(document).ready(function(){
    // Preload images in paginator initially
    jQuery('.wpv-pagination-preload-images').css('visibility', 'hidden'); // TODO move it to the CSS file and test
	wpv_pagination_init_preload_images();
    jQuery('.wpv-pagination-preload-pages').each(function(){
        var view_number = parseInt(jQuery(this).attr('id').substring(16), 10);
        var max_pages = parseInt(jQuery('#wpv_paged_max-'+view_number).val(),10);
		var max_reach = 1;
		if (jQuery('#wpv_paged_preload_reach-'+view_number).val()) {
			max_reach = parseInt(jQuery('#wpv_paged_preload_reach-'+view_number).val(), 10);
		}
		max_reach++;
		if (max_reach > max_pages) {
			max_reach = max_pages;
		}
		wpv_pagination_preload_pages(view_number, 1, max_pages, false, true, max_reach);
    });
	
	// Move the wpv_view_hash, wpv_paged_max and wpv_widget_view_id from the forms as it's only needed during ajax pagination
	jQuery('input[name=wpv_view_hash], input[name=wpv_paged_max], input[name=wpv_widget_view_id]').each( function() {
		jQuery(this).parent().after(this);
	});
	// Datepicker initailizing
	wpv_render_frontend_datepicker();
});

//////////////////////////////////////////////////////////////////////////
// Pagination handlers
//////////////////////////////////////////////////////////////////////////

jQuery( document ).on( 'click', '.js-wpv-pagination-next-link, .js-wpv-pagination-previous-link', function( e ) {
	e.preventDefault();
	var thiz = jQuery( this ),
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
	return wpv_pagination_replace_view( view_number, page, ajax, effect, max_pages, cache_pages, preload_pages, spinner, spinner_image, callback_next, stop_rollover );
});

jQuery( document ).on( 'change', '.js-wpv-page-selector', function( e ) {
	e.preventDefault();
	var thiz = jQuery( this ),
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
	return wpv_pagination_replace_view( view_number, page, ajax, effect, max_pages, cache_pages, preload_pages, spinner, spinner_image, callback_next, stop_rollover );
});

jQuery( document ).on( 'click', '.js-wpv-pagination-link', function( e ) {
	e.preventDefault();
	var thiz = jQuery( this ),
	view_number = thiz.data( 'viewnumber'),
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
	return wpv_pagination_replace_view_links( view_number, page, ajax, effect, max_pages, cache_pages, preload_pages, spinner, spinner_image, callback_next, stop_rollover );
});

////////////////////////////////////////////////////////////////
// Rollover
////////////////////////////////////////////////////////////////

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
			setTimeout( 'jQuery(this).wpvRollover({id:' + id + ', effect:\'' + effect + '\', speed:' + ( speed/1000 ) + ', page:' + page + ', count:' + count + ', cache_pages:' + cache_pages + ', preload_pages:' + preload_pages + ', spinner:\'' + spinner + '\', spinner_image:\'' + spinner_image + '\', callback_next:\'' + callback_next + '\'})', 100 );
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
				wpv_pagination_replace_view( id, page, true, effect, count, cache_pages, preload_pages, spinner, spinner_image, callback_next, false );
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

////////////////////////////////////////////////////////////////
// Responsive layouts onresize
////////////////////////////////////////////////////////////////

jQuery( window ).resize( function() {
	var thiz,
	width;
	jQuery( '.js-wpv-pagination-responsive' ).each( function() {
		thiz = jQuery( this );
		width = thiz.parent().width();
		thiz.css( 'width', width );
	});
});

////////////////////////////////////////////////////
// Table sorting head click
////////////////////////////////////////////////////

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
		wpv_add_url_controls_for_column_sort( innerthis );
	});
	jQuery( 'form[name="wpv-filter-' + view_number + '"]' ).submit();
	return false;
});

////////////////////////////////////////////////////
// Dependant parametric search
////////////////////////////////////////////////////

// TODO add the caching for the form results after cleaning this caching if needed, next

function wpv_dependant_form_clear_pagination_cache( view_number ) {
	window.wpvCachedPages = ( typeof window.wpvCachedPages == 'undefined' ) ? [] : window.wpvCachedPages;
	window.wpvCachedPages[view_number] = [];
}

function wpv_manage_advanced_form( thiz, force_filter_update, force_layout_update ) {
	var fil = thiz.closest( 'form' ),
	view_num = fil.data( 'viewnumber' ),
	view_id = fil.data( 'viewid' ),
	lay = jQuery( '#wpv-view-layout-' + view_num ),
	full_data = fil.find( '.js-wpv-filter-data-for-this-form' ),
	spinnerContainer = fil.find( '.js-wpv-dps-spinner' ),
	ajax_before = full_data.data( 'ajaxbefore' ),
	ajax_after = full_data.data( 'ajaxafter' ),
	data = {
		action: 'wpv_update_filter_form',
		valz: fil.serializeArray(),
		viewid: view_id
	},
	spinnerItems = spinnerContainer.length,
	spinnerImage,
	i;
	
	if ( full_data.data( 'spinnerimage' ) ) {
		spinnerImage = '<img src="' +  full_data.data( 'spinnerimage' ) + '" />';
	} else {
		spinnerImage = '';
	}
	if ( spinnerItems ) {
		for ( i = 0; i < spinnerItems; i++ ) {
			if ( jQuery( spinnerContainer[i] ).hasClass( 'js-wpv-dps-spinner-after' ) ) {
				jQuery( spinnerContainer[i] ).append( spinnerImage ).fadeIn( 'fast' );
			} else if ( jQuery( spinnerContainer[i] ).hasClass( 'js-wpv-dps-spinner-before' ) ) {
				jQuery( spinnerContainer[i] ).prepend( spinnerImage ).fadeIn( 'fast' );
			} else {
				jQuery( spinnerContainer[i] ).fadeIn( 'fast' );
			}
		}
	}
	
	if ( fil.hasClass( 'js-wpv-dps-only-form' ) ) {
		data.targetid = fil.data( 'targetid' );
	}
	
	jQuery.ajax({
		type: "POST",
		url: front_ajaxurl,
		data: data,
		success: function( response ) {
			var response_content = jQuery( '<div></div>' ).append( response ),
			new_filter_content = response_content.find( '.js-wpv-filter-form' ).html(),
			new_layout_content = response_content.find( '.js-wpv-view-layout' ).html();
			if ( force_filter_update || fil.hasClass( 'js-wpv-dps-enabled' ) || fil.hasClass( 'js-wpv-ajax-results-enabled' ) ) {
				fil.html( new_filter_content );
				wpv_render_frontend_datepicker();
				wpv_dependant_form_clear_pagination_cache( view_num );
			}
			fil.find( '[data-viewnumber]' ).each( function() {
				jQuery(this).attr( 'data-viewnumber', view_num );
			});
			// Adjust hidden inputs
			fil.find( 'input[name="wpv_paged_max"]' ).attr( 'id', 'wpv_paged_max-' + view_num );
			fil.find( 'input[name="wpv_paged_preload_reach"]' ).attr( 'id', 'wpv_paged_preload_reach-' + view_num );
			fil.find( 'input[name="wpv_widget_view_id"]' ).attr( 'id', 'wpv_widget_view-' + view_num );
			fil.find( 'input[name="wpv_view_hash"]' ).attr( 'id', 'wpv_view_hash-' + view_num );
			fil.find( 'input[name="wpv_view_count"]' ).attr( 'id', 'wpv_view_count-' + view_num ).val( view_num );
			if ( fil.hasClass( 'js-wpv-ajax-results-enabled' ) ) {
				if ( ajax_before !== '' ) {
					if ( eval( 'typeof(' + ajax_before + ' ) === \'function\'' ) ) {
						eval( ajax_before + '(' + view_num + ');' );
					}
				}
				lay.fadeOut( 200, function() {
					lay.html( new_layout_content ).fadeIn( 'fast', function() {
						lay.find( '[data-viewnumber]' ).each( function() {
							jQuery(this).attr( 'data-viewnumber', view_num );
						});
						if ( ajax_after !== '' ) {
							if ( eval( 'typeof(' + ajax_after + ' ) === \'function\'' ) ) {
								eval( ajax_after + '(' + view_num + ');' );
							}
						}
					});
				});
			}
		},
		error: function ( ajaxContext ) {
		//	i = 2;
		},
		complete: function() {
			spinnerContainer.hide();
		}
	});
}

// Dependant post relationship

jQuery( document ).on( 'change', '.js-wpv-post-relationship-update', function() {
	var thiz = jQuery( this ),
	fil = thiz.closest( 'form' ),
	currentposttype = thiz.data( 'currentposttype' ),
	watchers = fil.find( '.js-wpv-' + currentposttype + '-watch' ),
	watcherslength = watchers.length,
	i;
	if ( watcherslength ) {
		for( i = 0; i < watcherslength; i++ ) {
			jQuery( watchers[i] )
				.attr( 'disabled', true )
				.removeAttr( 'checked' )
				.removeAttr( 'selected' )
				.not( ':button, :submit, :reset, :hidden, :radio, :checkbox' )
				.val( '0' );
		}
	}
	wpv_manage_advanced_form( thiz, true, false );
});

jQuery( document ).on( 'change', '.js-wpv-dps-enabled .js-wpv-filter-trigger, .js-wpv-ajax-results-enabled .js-wpv-filter-trigger', function() {
	var thiz = jQuery( this );
	wpv_manage_advanced_form( thiz, false, false );
});

jQuery( document ).on( 'click', '.js-wpv-ajax-results-enabled .js-wpv-submit-trigger', function( e ) {
	e.preventDefault();
	var thiz = jQuery( this );
	wpv_manage_advanced_form( thiz, false, true );
});

jQuery( document ).on( 'click', '.js-wpv-reset-trigger', function( e ) {
	e.preventDefault();
	var thiz = jQuery( this ),
	fil = thiz.closest( 'form' ),
	watchers = fil.find('input, select'),
	watcherslength = watchers.length,
	i,
	target = fil.attr( 'action' );
	if ( fil.hasClass( 'js-wpv-ajax-results-enabled' ) || fil.hasClass( 'js-wpv-dps-only-form' ) ) {
		if ( watcherslength ) {
			for ( i = 0; i < watcherslength; i++ ) {
				jQuery( watchers[i] )
					.attr( 'disabled', true )
					.removeAttr( 'checked' )
					.removeAttr( 'selected' )
					.not( ':button, :submit, :reset, :hidden, :radio, :checkbox' )
					.val( '' );
			}
		}
		wpv_manage_advanced_form( thiz, false, false );
	} else {
		window.location.href = target;
	}
});

////////////////////////////////////////////////////
// Front end utils
////////////////////////////////////////////////////

( function( $ ) {
	$( function() {
		manage_checkboxes_and_multi();
	});
	
	function WPV_ManageDefaultValueForCheckBoxes( container ) {
		var self = this;
		self.element = null;
		self.container = container;
		self.others = null;
		
		self.init = function() {
			self.element = self.element_is_place_holder();
			if ( !self.element ) {
				return false;
			}
			self.others = self.getOthers();
			self.manage_clicks();
		};
		
		self.getOthers = function() {
			var all = self.container.find( "input.wpcf-form-checkbox" ),
			others = [];
			$.each( all, function( i, v ) {
				if ( i > 0 ) {
					others.push( v );
				}
			});
			return others;
		};
		
		self.getCheckedLen = function() {
			var count = 0;
			$.each( self.others, function( i, v ) { 
				if ( $( v ).prop( 'checked' ) === true ) {
					count++;
				}
			});
			return count;
		};
		
		self.element_is_place_holder = function() {
			var check_cont = self.container.children( '.wpcf-form-item-checkbox' )[0],
			check = $( check_cont ).find( 'input' );
			if ( $(check).val() ) {
				return false; 
			}
			return $( check );
		};
		
		self.manage_clicks = function() {
			$.each( self.others, function( i, v ) {
				$( v ).on( 'click', function( event ) {
					if ( $( event.target ).prop( 'checked' ) ) {
						self.element.prop( 'checked', false );
					} else if ( self.container.find( "input:checked" ).length === 0 ) {
						self.element.prop( 'checked', true );
					}
				});
			});
			self.element.on( 'click', function( event ) {
				if( $( event.target ).prop( 'checked' ) ) {
					$.each( self.others, function( i, v ) {
						$( v ).prop( 'checked', false );
					});
				}
			});
		};
		
		self.init();
	}
	
	function WPV_ManageDefaultValueForMultiple( select ) {
		var self = this;
		self.element = null;
		self.select = select;
		self.others = null;
		
		self.init = function() {
			self.element = self.element_is_place_holder();
			if ( !self.element ) {
				if ( !self.is_query() ) {
					$( self.select.find( 'option' )[0] ).prop( "selected", false );
				}
				return false;
			}
			self.others = self.getOthers();
			self.manage_clicks();
		};

		self.getOthers = function() {
			var all = self.select.find( "option" ),
			others = [];
			$.each( all, function( i, v ) {
				if ( i > 0 ) {
					others.push( v );
				}
			});
			return others;
		};

		self.getCheckedLen = function() {
			var count = 0;
			$.each( self.others, function( i, v ) {
				if( $( v ).prop( 'selected' ) === true ) {
					count++;
				}
			});
			return count;
		};

		self.element_is_place_holder = function() {
			var opt = self.select.find( 'option' );
			if ( $( opt[0] ).val() ) {
				return false;
			}
			return $( opt[0] );
		};

		self.manage_clicks = function() {
			$.each( self.others, function( i, v ) {
				$( v ).on( 'click', function( event ) {
					if( $( event.target ).prop( 'selected' ) ) {
						self.element.prop( 'selected', false );
					} else if( self.select.find( 'option:selected' ).length === 0 ) {
						self.element.prop( 'selected', true );
					}
				});
			});
			self.element.on( 'click', function( event ) {
				if( $( event.target ).prop( 'selected' ) ) {
					$.each( self.others, function( i, v ) {
						$( v ).prop( 'selected', false );
					});
				}
			});
		};
		
		self.is_query = function() {
			var sel_name = self.select.prop( "name" ).split( '[]' )[0],
			qs = location.search;
			if ( ~qs.indexOf( sel_name ) === true ) {
				return true;
			} else {
				return false;
			}
		};

		self.init();
	}
	
	function manage_checkboxes_and_multi() {
		var checkboxes_groups = [],
		multiselects = [];
		//Do not execute if there's not a view form, and at least 2 check boxes
		if ($( '.wpv-filter-form' ).is( 'form' ) && 
			$( '.wpcf-form-checkbox' ).is( 'input' ) && 
			$( '.wpv-filter-form' ).find( 'input.wpcf-form-checkbox' ).length > 1 ) {
			$( 'div.wpcf-checboxes-group' ).each( function( i, element ) {
				checkboxes_groups.push( new WPV_ManageDefaultValueForCheckBoxes( $( element ) ) );
			});
		}
		if( $( '.wpv-filter-form' ).is( 'form' ) && $( 'select.wpcf-form-select' ) && $( 'select.wpcf-form-select' ).prop( 'multiple' ) ) {
			$( 'select.wpcf-form-select' ).each( function( i, element ) {
				multiselects.push( new WPV_ManageDefaultValueForMultiple( $( element ) ) );
			});
		}
	}
}( jQuery ) );