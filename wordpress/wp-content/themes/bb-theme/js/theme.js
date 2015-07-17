(function($){
	
	/**
	 * Helper class for frontend theme logic.
	 * 
	 * @since 1.0
	 * @class FLTheme
	 */
	var FLTheme = {
		
		/**
		 * Initializes all frontend theme logic.
		 *
		 * @since 1.0
		 * @method init
		 */
		init: function()
		{
			this._bind();
			this._initRetinaImages();
		},
		
		/**
		 * Initializes and binds all frontend events.
		 *
		 * @since 1.0
		 * @access private
		 * @method _bind
		 */
		_bind: function()
		{
			// Fixed header
			if($('.fl-page-header-fixed').length != 0) {
				$(window).on('resize', $.throttle(500, this._enableFixedHeader));
				this._enableFixedHeader();
			} 
			
			// Top Nav Drop Downs
			if($('.fl-page-bar-nav ul.sub-menu').length != 0) {
				this._setupDropDowns();
				this._enableTopNavDropDowns();
			} 
			
			// Page Nav Drop downs
			if($('.fl-page-nav ul.sub-menu').length != 0) {
				$(window).on('resize', $.throttle(500, this._enablePageNavDropDowns));
				this._setupDropDowns();
				this._enablePageNavDropDowns();
			} 
			
			// Nav Search
			if($('.fl-page-nav-search').length != 0) {
				$('.fl-page-nav-search a.fa-search').on('click', this._toggleNavSearch);
			} 
			
			// Lightbox
			if(typeof $('body').magnificPopup != 'undefined') {
				this._enableLightbox();
			}
		},
		
		/**
		 * Enables the fixed header if the window is wide enough.
		 *
		 * @since 1.0
		 * @access private
		 * @method _enableFixedHeader
		 */
		_enableFixedHeader: function()
		{
			var win = $(window);
			
			if(win.width() < 992) {
				win.off('scroll.fl-theme');
				$('.fl-page-header-fixed').hide();
			}
			else {
				win.on('scroll.fl-theme', FLTheme._toggleFixedHeader);
			}
		},
		
		/**
		 * Shows or hides the fixed header based on the 
		 * window's scroll position.
		 *
		 * @since 1.0
		 * @access private
		 * @method _toggleFixedHeader
		 */
		_toggleFixedHeader: function()
		{
			var win             = $(window),
				fixed           = $('.fl-page-header-fixed'),
				fixedVisible    = fixed.is(':visible'),
				header          = $('.fl-page-header-primary'),
				headerHidden    = false;
				
			if ( 0 === header.length ) {
				headerHidden = win.scrollTop() > 200;
			}
			else {
				headerHidden = win.scrollTop() > header.height() + header.offset().top;
			}
			
			if(headerHidden && !fixedVisible) {
				fixed.stop().fadeIn(200);
			}
			else if(!headerHidden && fixedVisible) {
				fixed.stop().hide();
			}
		},
		
		/**
		 * Initializes drop down nav logic.
		 *
		 * @since 1.0
		 * @access private
		 * @method _setupDropDowns
		 */
		_setupDropDowns: function()
		{
			$('ul.sub-menu').each(function(){
				$(this).closest('li').attr('aria-haspopup', 'true');
			});
		},
		
		/**
		 * Initializes drop down menu logic for top bar navs.
		 *
		 * @since 1.0
		 * @access private
		 * @method _enableTopNavDropDowns
		 */
		_enableTopNavDropDowns: function()
		{
			var nav      = $('.fl-page-bar-nav'),
				navItems = nav.find(' > li');
			
			navItems.hover(FLTheme._navItemMouseover, FLTheme._navItemMouseout);
		},
		
		/**
		 * Initializes drop down menu logic for the main nav.
		 *
		 * @since 1.0
		 * @access private
		 * @method _enablePageNavDropDowns
		 */
		_enablePageNavDropDowns: function()
		{
			var win      = $(window),
				nav      = $('.fl-page-nav .fl-page-nav-collapse'),
				navItems = nav.find('ul li'),
				subMenus = navItems.find('ul.sub-menu');
			
			if(win.width() < 768) {
				navItems.off('mouseenter mouseleave');
				nav.find('> ul > li').has('ul.sub-menu').find('> a').on('click', FLTheme._navItemClickMobile);
			}
			else {
				nav.find('a').off('click', FLTheme._navItemClickMobile);
				nav.removeClass('in').addClass('collapse');
				navItems.removeClass('fl-mobile-sub-menu-open');
				navItems.find('a').width(0).width('auto');
				navItems.hover(FLTheme._navItemMouseover, FLTheme._navItemMouseout);
			}
		},
		
		/**
		 * Callback for when an item in a nav is clicked on mobile.
		 *
		 * @since 1.0
		 * @access private
		 * @method _navItemClickMobile
		 * @param {Object} e The event object.
		 */
		_navItemClickMobile: function(e)
		{
			var parent = $(this).parent();

			if(!parent.hasClass('fl-mobile-sub-menu-open')) {
				e.preventDefault(); 
				parent.addClass('fl-mobile-sub-menu-open');
			}
		},
		
		/**
		 * Callback for when the mouse leaves an item
		 * in a nav at desktop sizes.
		 *
		 * @since 1.0
		 * @access private
		 * @method _navItemMouseover
		 */
		_navItemMouseover: function()
		{
			if($(this).find('ul.sub-menu').length === 0) {
				return;
			} 
			
			var li              = $(this),
				parent          = li.parent(),
				subMenu         = li.find('ul.sub-menu'),
				subMenuWidth    = subMenu.width(),
				subMenuPos      = 0,
				winWidth        = $(window).width();
			
			if(li.closest('.fl-sub-menu-right').length !== 0) {
				li.addClass('fl-sub-menu-right');
			}
			else if($('body').hasClass('rtl')) {
				
				subMenuPos = parent.is('ul.sub-menu') ?
							 parent.offset().left - subMenuWidth: 
							 li.offset().left - subMenuWidth;
				
				if(subMenuPos <= 0) {
					li.addClass('fl-sub-menu-right');
				}
			}
			else {
				
				subMenuPos = parent.is('ul.sub-menu') ?
							 parent.offset().left + (subMenuWidth * 2) : 
							 li.offset().left + subMenuWidth;
				
				if(subMenuPos > winWidth) {
					li.addClass('fl-sub-menu-right');
				}
			}
			
			li.addClass('fl-sub-menu-open');
			subMenu.hide();
			subMenu.stop().fadeIn(200);
			FLTheme._hideNavSearch();
		},
		
		/**
		 * Callback for when the mouse leaves an item 
		 * in a nav at desktop sizes.
		 *
		 * @since 1.0
		 * @access private
		 * @method _navItemMouseout
		 */
		_navItemMouseout: function()
		{
			var li      = $(this),
				subMenu = li.find('ul.sub-menu');
			
			subMenu.stop().fadeOut({
				duration: 200, 
				done: FLTheme._navItemMouseoutComplete
			});
		},
		
		/**
		 * Callback for when the mouse finishes leaving an item 
		 * in a nav at desktop sizes.
		 *
		 * @since 1.0
		 * @access private
		 * @method _navItemMouseoutComplete
		 */
		_navItemMouseoutComplete: function()
		{
			var li = $(this).parent();
			
			li.removeClass('fl-sub-menu-open');
			li.removeClass('fl-sub-menu-right');
			
			$(this).show();
		},
		
		/**
		 * Shows or hides the nav search form.
		 *
		 * @since 1.0
		 * @access private
		 * @method _toggleNavSearch
		 */
		_toggleNavSearch: function()
		{
			var form = $('.fl-page-nav-search form');
			
			if(form.is(':visible')) {
				form.stop().fadeOut(200);
			}
			else {
				form.stop().fadeIn(200);
				$('body').on('click.fl-theme', FLTheme._hideNavSearch);
			}
		},
		
		/**
		 * Hides the nav search form.
		 *
		 * @since 1.0
		 * @access private
		 * @method _hideNavSearch
		 * @param {Object} e (Optional) An event object.
		 */
		_hideNavSearch: function(e)
		{
			var form = $('.fl-page-nav-search form');
			
			if(e !== undefined) {
				if($(e.target).closest('.fl-page-nav-search').length > 0) {
					return;
				}
			}
			
			form.stop().fadeOut(200);
			
			$('body').off('click.fl-theme');
		},
		
		/**
		 * Initializes the lightbox.
		 *
		 * @since 1.0
		 * @access private
		 * @method _enableLightbox
		 */
		_enableLightbox: function()
		{
			var body = $('body');
			
			if(!body.hasClass('fl-builder') && !body.hasClass('woocommerce')) {
				
				$('.fl-content').find('a[href*=".jpg"], a[href*=".jpeg"], a[href*=".png"], a[href*=".gif"]').magnificPopup({
					closeBtnInside: false,
					type: 'image',
					gallery: {
						enabled: true
					}
				});
			}
		},
		
		/**
		 * Initializes retina images.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initRetinaImages
		 */
		_initRetinaImages: function()
		{
			var pixelRatio = !!window.devicePixelRatio ? window.devicePixelRatio : 1;
		
			if ( pixelRatio > 1 ) {
				$( 'img[data-retina]' ).each( FLTheme._convertImageToRetina );
			}
		},
		
		/**
		 * Converts an image to retina.
		 *
		 * @since 1.0
		 * @access private
		 * @method _convertImageToRetina
		 */
		_convertImageToRetina: function()
		{
			var image       = $( this ),
				tmpImage    = new Image(),
				src         = image.attr( 'src' ),
				retinaSrc   = image.data( 'retina' );
				
			if ( '' != retinaSrc ) {
			
				tmpImage.onload = function() {
					image.height( tmpImage.height );
					image.width( tmpImage.width );
					image.attr( 'src', retinaSrc );
				};
				
				tmpImage.src = src; 
			}
		}
	};
	
	$(function(){
		FLTheme.init();
	});
	
})(jQuery);