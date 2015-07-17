( function( $ ) {
	
	/* Internal shorthand */
	var api = wp.customize;
	
	/**
	 * Helper class that contains data for showing and hiding controls.
	 * 
	 * @since 1.2.0
	 * @class FLCustomizerToggles
	 */
	FLCustomizerToggles = {
		
		'fl-layout-width': [{
			controls: [ 'fl-layout-spacing', 'fl-layout-shadow-size', 'fl-layout-shadow-color' ],
			callback: function( val ) { return 'boxed' == val; }
		}],
		
		'fl-body-bg-image': [{
			controls: [ 'fl-body-bg-repeat', 'fl-body-bg-position', 'fl-body-bg-attachment', 'fl-body-bg-size' ],
			callback: function( val ) { return '' != val; }
		}],
		
		'fl-topbar-layout': [{
			controls: [ 'fl-topbar-line1', 'fl-topbar-col1-layout' ],
			callback: function( val ) { 
				
				var col1Layout = api( 'fl-topbar-col1-layout' ).get(),
					col1Text   = api.control( 'fl-topbar-col1-text' ).container,
					col2Layout = api( 'fl-topbar-col2-layout' ).get(),
					col2Text   = api.control( 'fl-topbar-col2-text' ).container;
				
				col1Text.toggle( 'none' != val && 'text' == col1Layout );
				col2Text.toggle( '2-cols' == val && 'text' == col2Layout );
				
				return '1-col' == val || '2-cols' == val; 
			}
		},{
			controls: [ 'fl-topbar-line2', 'fl-topbar-col2-layout' ],
			callback: function( val ) { return '2-cols' == val;  }
		}],
		
		'fl-topbar-col1-layout': [{
			controls: [ 'fl-topbar-col1-text' ],
			callback: function( val ) { return 'none' != api( 'fl-topbar-layout' ).get() && ('text' == val || 'text-social' == val); }
		}],
		
		'fl-topbar-col2-layout': [{
			controls: [ 'fl-topbar-col2-text' ],
			callback: function( val ) { return '2-cols' == api( 'fl-topbar-layout' ).get() && ('text' == val || 'text-social' == val); }
		}],
		
		'fl-topbar-bg-color': [{
			controls: [ 'fl-topbar-bg-gradient' ],
			callback: function( val ) { return '' != val; }
		}],
		
		'fl-topbar-bg-image': [{
			controls: [ 'fl-topbar-bg-repeat', 'fl-topbar-bg-position', 'fl-topbar-bg-attachment', 'fl-topbar-bg-size' ],
			callback: function( val ) { return '' != val; }
		}],
		
		'fl-header-bg-color': [{
			controls: [ 'fl-header-bg-gradient' ],
			callback: function( val ) { return '' != val; }
		}],
		
		'fl-header-bg-image': [{
			controls: [ 'fl-header-bg-repeat', 'fl-header-bg-position', 'fl-header-bg-attachment', 'fl-header-bg-size' ],
			callback: function( val ) { return '' != val; }
		}],
		
		'fl-logo-type': [{
			controls: [ 'fl-logo-text', 'fl-logo-font-family', 'fl-logo-font-weight', 'fl-logo-font-size' ],
			callback: function( val ) { return 'text' == val; }
		},{
			controls: [ 'fl-logo-image', 'fl-logo-image-retina' ],
			callback: function( val ) { return 'image' == val; }
		}],
		
		'fl-header-layout': [{
			controls: [ 'fl-header-padding', 'fl-fixed-header' ],
			callback: function( val ) { return 'none' != val; }
		},{
			controls: [ 'fl-nav-bg-color', 'fl-nav-bg-gradient', 'fl-nav-bg-image', 'fl-nav-bg-repeat', 'fl-nav-bg-position', 'fl-nav-bg-attachment', 'fl-nav-bg-size', 'fl-nav-link-color', 'fl-nav-hover-color' ],
			callback: function( val ) { return 'right' != val; }
		},{
			controls: [ 'fl-header-line1', 'fl-header-content-layout' ],
			callback: function( val ) {
				
				var layout = api( 'fl-header-content-layout' ).get(),
					text   = api.control( 'fl-header-content-text' ).container;
				
				text.toggle( 'bottom' == val && ('text' == layout || 'social-text' == layout) );
				
				return 'bottom' == val; 
			}
		}],
		
		'fl-header-content-layout': [{
			controls: [ 'fl-header-content-text' ],
			callback: function( val ) { 
				return 'bottom' == api( 'fl-header-layout' ).get() && ('text' == val || 'social-text' == val); 
			}
		}],
		
		'fl-nav-bg-color': [{
			controls: [ 'fl-nav-bg-gradient' ],
			callback: function( val ) {
				return 'right' != api( 'fl-header-layout' ).get() && '' != val; 
			}
		}],
		
		'fl-nav-bg-image': [{
			controls: [ 'fl-nav-bg-repeat', 'fl-nav-bg-position', 'fl-nav-bg-attachment', 'fl-nav-bg-size' ],
			callback: function( val ) { return 'right' != api( 'fl-header-layout' ).get() && '' != val; }
		}],
		
		'fl-content-bg-image': [{
			controls: [ 'fl-content-bg-repeat', 'fl-content-bg-position', 'fl-content-bg-attachment', 'fl-content-bg-size' ],
			callback: function( val ) { return '' != val; }
		}],
		
		'fl-blog-layout': [{
			controls: [ 'fl-blog-sidebar-size', 'fl-blog-sidebar-display' ],
			callback: function( val ) { return 'no-sidebar' != val; }
		}],
		
		'fl-archive-show-full': [{
			controls: [ 'fl-archive-readmore-text' ],
			callback: function( val ) { return '0' == val; }
		}],
		
		'fl-woo-layout': [{
			controls: [ 'fl-woo-sidebar-size', 'fl-woo-sidebar-display' ],
			callback: function( val ) { return 'no-sidebar' != val; }
		}],
		
		'fl-footer-widgets-bg-color': [{
			controls: [ 'fl-footer-widgets-bg-gradient' ],
			callback: function( val ) { return '' != val; }
		}],
		
		'fl-footer-widgets-bg-image': [{
			controls: [ 'fl-footer-widgets-bg-repeat', 'fl-footer-widgets-bg-position', 'fl-footer-widgets-bg-attachment', 'fl-footer-widgets-bg-size' ],
			callback: function( val ) { return '' != val; }
		}],
		
		'fl-footer-layout': [{
			controls: [ 'fl-footer-line1', 'fl-footer-col1-layout' ],
			callback: function( val ) { 
				
				var col1Layout = api( 'fl-footer-col1-layout' ).get(),
					col1Text   = api.control( 'fl-footer-col1-text' ).container,
					col2Layout = api( 'fl-footer-col2-layout' ).get(),
					col2Text   = api.control( 'fl-footer-col2-text' ).container;
				
				col1Text.toggle( 'none' != val && ('text' == col1Layout || 'social-text' == col1Layout) );
				col2Text.toggle( '2-cols' == val && ('text' == col2Layout || 'social-text' == col2Layout) );
				
				return '1-col' == val || '2-cols' == val; 
			}
		},{
			controls: [ 'fl-footer-line2', 'fl-footer-col2-layout' ],
			callback: function( val ) { return '2-cols' == val;  }
		}],
		
		'fl-footer-col1-layout': [{
			controls: [ 'fl-footer-col1-text' ],
			callback: function( val ) { 
				return 'none' != api( 'fl-footer-layout' ).get() && ('text' == val || 'social-text' == val); 
			}
		}],
		
		'fl-footer-col2-layout': [{
			controls: [ 'fl-footer-col2-text' ],
			callback: function( val ) { 
				return '2-cols' == api( 'fl-footer-layout' ).get() && ('text' == val || 'social-text' == val); 
			}
		}],
		
		'fl-footer-bg-color': [{
			controls: [ 'fl-footer-bg-gradient' ],
			callback: function( val ) { return '' != val; }
		}],
		
		'fl-footer-bg-image': [{
			controls: [ 'fl-footer-bg-repeat', 'fl-footer-bg-position', 'fl-footer-bg-attachment', 'fl-footer-bg-size' ],
			callback: function( val ) { return '' != val; }
		}]
	};
	
})( jQuery );