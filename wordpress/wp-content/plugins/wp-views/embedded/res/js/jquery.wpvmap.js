/*
 *  Project: Plot Views results on a Google Map 
 *  Author:  OnTheGoSystems  http://wp-types.com/
 *  Version: Pre-release V1.0 - Updated 11/12/2013
 */
/*
 * Adds Views results to a Google Map.  Will take as input to plot:-
 * 1. A views listing (the most normal output)
 * 2. A views js array output (where your View does not generate visible output)
 * 3. A single plot item on the page (normally for Content templates)
 *
 * To use:
 * Create a <div id="myMapContainer"></div> on your page where the map 
 * is to be displayed. Give it an id.
 * Add the following javascript to your page, you can add this to the 
 * js section of your View or Content Template.
 *
 * 		$( document ).ready(function() {
 *			$("#myMapContainer").wpvmap();
 *		});
 *
 * You can change any of the defaults below, by setting them when you 
 * call the plugin. 
 *
 * 		$("#myMapContainer").wpvmap( {
 *			iconPath : "/wp-content/themes/toolset-bootstrap-child/markers/",
 *			iconImage : "icon.png",
 *			iconImageHover : "icon-selected.png"
 * 		});
 * 
 * ***** More information *****
 * 
 * jQuery plugin template, based on - http://jqueryboilerplate.com/ - thanks guys!
 * Detailed documentation : http://wp-types.com/documentation/user-guides/map-wordpress-posts/
 *
 */
 ;(function ( $, window, document, undefined ) {
	var pluginName = "wpvmap",
		defaults = {
			generalMapZoom: 8,								        // Zoom level for map views
			centerLat: 0,centerLon: 0,						    // Default map centre
			mapType : google.maps.MapTypeId.ROADMAP,	// Default map type - ROADMAP
			fitBounds : true,								          // Fit to bounds on display of multiple points map
			centerMarker: true,								        // Center on marker for single point map														
			singleMapZoom : 14,  							        // Zoom level for single map views
			hideBusinessListings : true,					    // Hide Google business listings on map (points of interest layer) */
			infoWindow : ".js-map-infowindow-html",		// Element selector for infoWindow hmtl content	
			centerMapLink : ".js-map-fitbounds",			// Element selector for center map link/button	
			singleContainer: "#js-map-element",				// ID selector of the Single element container
			listContainer : "#js-map-elements", 			// ID selector of the Views container - by default we expect an ol or ul 
			listItem : "li",								          // Item container - this contains the item to plot on the map
			locateOnMapLink : ".js-map-center-map",		// Element selector for "zoom to this items marker" link/button.
			viewsArray : "otgLocations",					    // Name of the Views js array		
			notPlottedClass : "map-not-plotted",			// If co-ordinates are not valid, this class will be added to DOM	
			iconPath : "",									          // Absolute or relative paths to marker icons						
			iconImage : "", 								          // Marker icon filename (defaults to google default marker)
			iconImageHover : "",							        // Marker icon to display on hover over list item - hover disabled if not set
			hoverClass : "map-dom-hover",					    // Class added to list items on hover
			mapPlotType : 0									          // 0 = default, 1=Map with View elements, 2=Map with single element, 3=Map with JavaScript array
															                  // normally this only needs to be set if there are multiple maps on the page. 
		};


	// The actual plugin constructor
	function Plugin ( element, options ) {
		this.element = element;
		this.settings = $.extend( {}, defaults, options );	// Extend defaults with user supply settings
		this._defaults = defaults;
		this._name = pluginName;
		this.init();
	}
	Plugin.prototype = {
		init: function () {					
			var map = null,
				gmarkers=[],
				bounds = new google.maps.LatLngBounds(),
				infowindow = new google.maps.InfoWindow(),
				$domItems,
				$singleElement,
				centerMode,cBounds="bounds",cCenter="center",cSingle="single",
				settings=this.settings,
				$mapContainer=$('#'+this.element.id);
			/**
			  * *** Create map  ***
			  * We won't display anything just yet . this will happen after we plot points
			**/
			function createMap() {
				var mapOptions = {
						zoom: settings.generalMapZoom,
						center: new google.maps.LatLng(settings.centerLat, settings.centerLon), 
						mapTypeId: settings.mapType
					};
				map = new google.maps.Map($mapContainer[0], mapOptions);
				// We don't want the business layer to be displayed (this can be change in options)
				if (settings.hideBusinessListings) {
					map.setOptions({styles: [{featureType: "poi.business", stylers: [{ visibility: "off" }] }]});	
				}
			}	
			/**
			  * *** Check numeric ***
			  * http://stackoverflow.com/questions/18082/validate-numbers-in-javascript-isnumeric
			  *
			**/
			function isNumber(n) {
			  return !isNaN(parseFloat(n)) && isFinite(n);
			}
			/**
			  * *** Get point data and plot  ***
			  * One of the following is passed as points:
			  * 1. jQUery object array of DOM elements with data-* attributes
			  * 2. Single jQuery object DOM element as above
			  * 3. Javascript array of point objects
			**/			
			function getPointData (points,pointArray) {  // pointArray is only defined if we have js array of points
				var pointData,title,html,latlng,infoWindowHTML;			
				$.each(points,function(index) { 
					//If we are processing the DOM - retrieve all data-* attributes from the element
					//If we are processing an array, then all points are on the object
					pointData =(pointArray !== undefined) ? pointData=this	: pointData=$(this).data();	
					// Only plot if lat, lon are valid https://developers.google.com/maps/documentation/javascript/reference?hl=en#LatLng
					if ((isNumber(pointData.markerLat) && pointData.markerLat >= -90 && pointData.markerLat <= 90) && 
						(isNumber(pointData.markerLon) && pointData.markerLon >= -180 && pointData.markerLon <= 180)) {  						
						//Set the infoWindow HTML from a data attribute or from the contents of a specified DOM element
						infoWindowHTML=(pointData.markerHtml===undefined) ? $(settings.infoWindow,this).html() : pointData.markerHtml;
						plotPoint(pointData,infoWindowHTML);
					} else {
						// No point to plot - if this is not an array of points, add a class to the dom element to indicate not plotted
						$(this).addClass(settings.notPlottedClass);
					}
				});
			}
			/**
			  * *** Plot point on map  ***
			  * Create marker, set infoWindow listener
			**/
			function plotPoint(pointData,infoWindowHTML) {
				var latlng=new google.maps.LatLng(pointData.markerLat, pointData.markerLon);
				var markerOptions={
					map: map,
					position: latlng
				};
				if (pointData.markerTitle!==undefined) {markerOptions.title=pointData.markerTitle;}
				if (settings.iconImage.length>0) {markerOptions.icon=settings.iconPath+settings.iconImage;}
				var marker= new google.maps.Marker(markerOptions);
				//If no ID is available then we are processing a single marker - save the marker in the 0 index of the array
				//We'll use this later for centering the map
				var index=(pointData.markerId===undefined)? 0 : pointData.markerId;
				gmarkers[index]=marker; /* Store markers in an array indexed by property post id */
				if (infoWindowHTML!==undefined) {
					/* Add listener to display marker info window on click */
					google.maps.event.addListener(marker, 'click', function() {
						infowindow.setContent(infoWindowHTML); 
						infowindow.open(map,marker);
					});		
				}
				/* Create a bounds for the map to allow zoom and fit all markers */
				if(settings.fitBounds) {bounds.extend(latlng);}
			}					
			/**
			  * *** Center map  ***
			**/
			function centerMap(mode) {
				switch (mode) {
					case cBounds:									//Fit to bounds (zoom to display all markers)
						if (!bounds.isEmpty()){						//If no bounds then fall through to default
							map.fitBounds(bounds);
						}
						break;
					case cSingle:										// Center on the single marker
						if ( gmarkers.length > 0 ) {					// If there are markers, first check if not all are empty because they are added to the marker-id index
							var real_markers = jQuery.grep(gmarkers,function(n){ return(n) });
							if (real_markers[0]!==undefined) {					//No marker!! Fall through to default
								map.setCenter(real_markers[0].getPosition());  
							}
						}
						break;
				}
			}
			/**
			  * *** Set events for Views list items  ***
			  * Set class on hover over list item
			  * Change marker color on hover
			  * Bind center on item link to list item
			**/			
			function setListItemEvents($listItems) {
				if (settings.iconImageHover.length > 0) {
				/* Change property icon (color) when user hovers over list item */
					$listItems.hover(
						function() {
							var id=$(this).data("marker-id"),
								zindex;
							if (id!==undefined) {
								if (!$(this).hasClass(settings.notPlottedClass)) {  //We don't have marker for this list item
									gmarkers[id].setZIndex(google.maps.Marker.MAX_ZINDEX + 1);
									gmarkers[id].setIcon(settings.iconPath+settings.iconImageHover);
								}
								$(this).addClass(settings.hoverClass);
							}
						}, function() {
							var id=$(this).data("marker-id");
							if (id!==undefined) {
								if (!$(this).hasClass(settings.notPlottedClass)) {  //We don't have marker for this list item
									gmarkers[id].setIcon(settings.iconPath+settings.iconImage);
								}
								$(this).removeClass(settings.hoverClass);
							}			
						}
					);
				}
				/* Center map on selected property and zoom in */
				/* Center link must be inside $domElementContainer */
				if (settings.locateOnMapLink.length > 0 && $listItems.find(settings.locateOnMapLink).length > 0) {
					$listItems.find(settings.locateOnMapLink).click(function(event) {
						event.preventDefault();
						var $itemContainer=$(this).parents(settings.listContainer+" "+settings.listItem);  //Get the parent container (with our data- and classes)
						if (!$itemContainer.hasClass(settings.notPlottedClass)) { //We don't have marker for this list item
							var id=$itemContainer.data("marker-id");  
							if (id!==undefined) {		
								map.setCenter(gmarkers[id].getPosition());
								map.setZoom(settings.singleMapZoom);
							}
						}
					});
				}
			}		
			// If there is a center link on the page - then bind a center map event to it
			function centerEvent(mode) {
				var centerLink=$(settings.centerMapLink);
				if (centerLink.length>0) {
					centerLink.click(function(event) {
						event.preventDefault();
						centerMap(mode);
					});
				}
			}
			/**
			  * *** Main processing  ***
			  * There are three types of data we can plot
			  * Data associated with a View item in the DOM
			  * A single point (normally for a detail view)
			  * A JavaScript array of points
			  *
			  * We can normally recognise which type we have by looking for the container on the page
			  * however if there are multiple maps on the page, there exists the option to pass a variable 
			  * mapPlotType (see in defaults above for details) to specify the map type
			**/
			// Are there are view items to map?
			$domItems=$(settings.listContainer+" "+settings.listItem);
			if ($domItems.length!==0 && (settings.mapPlotType == 0 || settings.mapPlotType == 1 )) {
				createMap();										// Setup the map object
				getPointData($domItems);							// Process points and plot markers
				centerMode=(settings.fitBounds)?cBounds:cCenter;	
				centerMap(centerMode);				
				setListItemEvents($domItems);
				centerEvent(centerMode);
			} else {
				//Is there are a single view item to map?
				$singleElement=$(settings.singleContainer);
				if ($singleElement.length!==0 && (settings.mapPlotType == 0 || settings.mapPlotType == 2 )) {
					createMap();
					getPointData($singleElement);
					centerMode=(settings.centerMarker)? cSingle:cCenter;
					centerMap(centerMode);
				} else {
					//Is there a JavaScript array of values to map?
					if ((typeof settings.viewsArray !== undefined && typeof window[settings.viewsArray] !== undefined && window[settings.viewsArray].length>0 )&& (settings.mapPlotType == 0 || settings.mapPlotType == 3 )) {
						createMap();
						getPointData(window[settings.viewsArray],true);   //window[] - converts string name to a variable.
						centerMode=(settings.fitBounds)? cBounds : cCenter;
						centerMap(centerMode);
						centerEvent(centerMode);						
					} else {
						//We didn't find anything to map on the page, just create a map in the map container
						createMap();	
						centerMap(cCenter);	
					}					
				}
			}
			return this;
		}
	};
	// A really lightweight plugin wrapper around the constructor,
	// preventing against multiple instantiations
	$.fn[ pluginName ] = function ( options ) {
		return this.each(function() {
			if ( !$.data( this, "plugin_" + pluginName ) ) {
				$.data( this, "plugin_" + pluginName, new Plugin( this, options ) );
			}
		});
	};
})( jQuery, window, document ); 