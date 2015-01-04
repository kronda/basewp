/*  This is a temporary file, we should probably have separate JS for each admin page */

var WpvPaginationOverlay, WpvPagination, WPV_Toolset = {};

if (!window.console) {var console = {};}
if (!console.log) {console.log = function() {};}

jQuery(function($) {

    $.extend($.colorbox.settings, { // override some Colorbox defaults
        transition: 'fade',
        opacity: 0.3,
        speed: 150,
        fadeOut : 0,
        closeButton: false,
        trapFocus: false
    });
	
	$.extend($.fn.select2.defaults, { // override Select2 defaults
		dropdownAutoWidth: true
    });

    // Add .colorbox-active to the body element when colorbox is active
    $(document).on('cbox_complete', function() {

        if ( $('#colorbox .no-scrollbar').length === 0 ) {
            $('body').addClass('disable-scrollbar');
        }

        // trigger .button-primary to click when ENTER key is pressed and colorbox popup is opened
        $(document).on('keypress.colorbox', function(e) {
            var keycode = parseInt((e.keyCode ? e.keyCode : e.which),10);
            if ( keycode === 13 ) { // 13 is for ENTER key
                $('#cboxContent .wpv-dialog-footer .button-primary').click(); // trigger click event on the currently opened popup
            }
        });

    });

    $(document).on('cbox_cleanup', function() {
         $('body').removeClass('disable-scrollbar');
    });

    $(document).on('cbox_closed', function() {
         $('.js-select2').select2('destroy');
         $(document).off('keypress.colorbox'); // unbind the keypress event
    });

    // Bind close event to .js-dialog-close classes
    $(document).on('click', '.js-dialog-close', function(e) {
        e.preventDefault();
        $.colorbox.close();
        return false;
    });

    $(document).on('click','.js-wpv-dialog-change li',function(){
//        $('.js-wpv-dialog-change i').removeClass('icon-check-sign').addClass('icon-check-empty');
        $(this).find('i').addClass('icon-check-sign');
    });

    // Paginations overlay

    WpvPaginationOverlay = {
        tableBody : function() {
            return $('.js-wpv-views-listing-body');
        },
        overlayElem: $('.js-table-loader-overlay'),
        showOverlay : function() {
            var tableHeight = this.tableBody().height();
            var tableWidth = this.tableBody().width();
            this.tableBody().fadeTo(0,'.15');
            this.overlayElem
                .css({
                    height: tableHeight + 'px',
                    width: tableWidth + 'px',
                    top: this.tableBody().position().top,
                    left: this.tableBody().position().left
                })
                .fadeIn(100);

        },
        hideOverlay : function() {
            this.overlayElem.fadeOut('fast');
            this.tableBody().fadeTo(0,1);
        }
    };

    WpvPagination = {
        thiz : this,
        ajaxData : {},
        totalPages : $('.js-wpv-listing-pagination-nav').length - 2, // number of pagination links - prev and next arrows
        goToPage: function(num) {
            WpvPaginationOverlay.showOverlay();

            this.ajaxData.page = num;
            //console.log(this.ajaxData);

            var thiz = this;
            var request = $.post(ajaxurl, thiz.ajaxData , function(result) {
                $('.js-wpv-views-listing-body')
                    .empty()
                    .append($(result));
                thiz.setActivePage(num);
            });

            request.always(function(result){ // Hide pagination overlay when AJAX request is done.
                console.log(result);
                WpvPaginationOverlay.hideOverlay();
            });

            request.fail(function(result){
                // console.log(result);
                // if ajax request fails
            });

        },
        getActivePage : function() {
            return $('.js-active.js-wpv-listing-pagination-nav').data('page-num');
        },
        setActivePage : function(num) {
            $('.js-wpv-listing-pagination-nav').removeClass('js-active active');
            $('.js-wpv-listing-pagination-nav').eq(num).addClass('js-active active');

            $('.js-wpv-listing-pagination-nav').show();
            if (this.getActivePage() === 1) {
                $('.js-wpv-listing-pagination-nav-prev').hide();
            }
            else if (this.getActivePage() === this.totalPages) {
                $('.js-wpv-listing-pagination-nav-next').hide();
            }

        }
    };

    $('.wpv-listing-pagination').on('click','.js-wpv-listing-pagination-nav',function(e){
        e.preventDefault();

        if ( !$(this).is('.js-active') ) {
            var pagenum;
            if ( $(this).is('.js-wpv-listing-pagination-nav-prev') ) { // for the previous link
                pagenum = WpvPagination.getActivePage() - 1;
            }
            else if ( $(this).is('.js-wpv-listing-pagination-nav-next') ) { // for the next link
                pagenum = WpvPagination.getActivePage() + 1;
            }
            else { // for all other links
                pagenum = $(this).data('page-num');
            }

            if ( typeof(pagenum) !== 'undefined' ) {
                if ( pagenum !== 0 && pagenum <= WpvPagination.totalPages ) { // Check if pagination is not out of range
                    WpvPagination.goToPage(pagenum);
                }
            }
        }

        return false;
    });

    // Remove toolset alerts
    $(document).on('click', '.js-toolset-alert-remove', function(){
        $(this).closest('.toolset-alert').fadeOut('fast',function(){
            $(this).remove();
        });
    });

    // Hide toolset alerts (but don't remove)
    $(document).on('click', '.js-toolset-alert-hide', function(){
        $(this).closest('.toolset-alert').fadeOut('fast');
    });

    // Run wpvToolsetHelp on .js-show-toolset-message elements
    $.each($('.js-show-toolset-message'), function(){
        $(this)
            .show()
            .wpvToolsetHelp();
    });

});

WPV_Toolset.message = {};
WPV_Toolset.message.container = null;

/* Validation messages */
(function($){

	var has_stay = false, is_open = false, prev = null, FADE_FAST = 200;
	$.fn.wpvToolsetMessage = function( options )
	{
		var prms = $.extend( {
				text : "Enter a customized text to be displayed",
				type: '',
				inline:false,
				header: false,
				headerText: false,
				close: false,
				use_this: true,
				fadeIn: 200,
				fadeOut: 1000,
				stay: false,
				onClose: false,
				args:[],
				referTo: null,
				offestX: -20,
				offsetY: 0,
                classname: ''
			}, options ),
            box = null,
            header = null,
            container = this,
            remove = null,
			tag = prms.inline ? 'span' : 'p'
			bool = false;

			if( container.children().length > 0 )
			{
				container.children().each(function(i){
					if( $(this).text() == prms.text )
					{
						bool = true;
					}
				});
			}

			if( bool ) return;

			if( has_stay )
			{
				if(prev)
				{
					var rem = prev;
					prev = null;
					prev_text = '';
					has_stay = false;
					is_open = false;
					rem.fadeTo( 0, 0, function(){
						rem.remove();
						rem = null;
					});
				}
			}

			this.wpvMessageRemove = function()
			{
				if( box )
				{
					box.fadeTo( prms.fadeOut, 0, function(){
						is_open = false;
						prev = null;
						prev_text = '';
						has_stay = false;
						if( prms.onClose && typeof prms.onClose == 'function' )
						{
							prms.onClose.apply( container, prms.args );
						}
						$( this ).remove();
					});
				}

				return this;
			};

			if( prms.header && prms.headerText )
			{
				box = $('<div class="toolset-alert toolset-alert-'+prms.type+' '+prms.classname+'" />');
				header = $('<h2 class="toolset-alert-header" />');
				box.append(header);
				header.text(prms.headerText);
				box.append('<'+tag+'></'+tag+'>');
				box.find(tag).html( prms.text );
			}
			else
			{
				box = $('<'+tag+' class="toolset-alert toolset-alert-'+prms.type+' '+prms.classname+'" />');
				box.html( prms.text );
			}

			if( prms.close ){
                remove = $('<i class="toolset-alert-close icon-remove-sign js-icon-remove-sign"></i>');
				box.append( remove );
				remove.on('click', function(event){
					container.wpvMessageRemove();
				});
			}


				if( is_open ) this.wpvMessageRemove();
				container.append( box );
				box.hide();

				if( null !== prms.referTo )
				{
	                box.css({
						"position":"absolute",
						"z-index":10000,
						"top": prms.referTo.position().top + prms.offestY + "px",
						"left": prms.referTo.position().left + prms.referTo.width() + prms.offestX + "px"
					});
				}


				box.fadeTo( null != prev ? 0 : prms.fadeIn, 1, function(){
					prev = $(this);
					prev_text = prms.text;
					is_open = true;
					if( prms.stay ){
						has_stay = true;
					}
					else
					{
						container.wpvMessageRemove();
					}
				});

			return this;
		};

})(jQuery);


/* Help messages */
(function($){

    $.fn.wpvToolsetHelp = function( options ) {

        var thiz = this;

        var $container = this;
        var prms = $.extend( {
            content : ( thiz.contents().length !== 0 ) ? thiz.contents() : "Enter a customized text to be displayed",
            tutorialButtonText : ( typeof(thiz.data('tutorial-button-text' )) !== 'undefined' ) ? thiz.data('tutorial-button-text') : null,
            tutorialButtonURL : ( typeof(thiz.data('tutorial-button-url' )) !== 'undefined' ) ? thiz.data('tutorial-button-url') : null,
            linkText : ( typeof(thiz.data('link-text')) !== 'undefined' ) ? thiz.data('link-text') : null,
            linkURL : ( typeof(thiz.data('link-url')) !== 'undefined' ) ? thiz.data('link-url') : null,
            footer : ( typeof(thiz.data('footer')) !== 'undefined' ) ? thiz.data('footer') : false,
            classname : ( typeof(thiz.data('classname')) !== 'undefined' ) ? thiz.data('classname') : '',
            close: ( typeof(thiz.data('close')) !== 'undefined' ) ? thiz.data('close') : true,
            hidden: ( typeof(thiz.data('hidden')) !== 'undefined' ) ? thiz.data('hidden') : false,
            onClose: false,
            args:[]
        }, options );

        if ( $.type(prms.content) === 'string' ) {
            prms.content = $('<p>' + prms.content + '</p>');
        }

        var $box = $('<div class="toolset-help ' + prms.classname + '"><div class="toolset-help-content"></div><div class="toolset-help-sidebar"><div class="toolset-help-sidebar-ico"></div></div></div>');

	var $footer = $('<div class="toolset-help-footer"><button class="js-toolset-help-close js-toolset-help-close-forever button-secondary">'+ wpv_help_box_texts.wpv_dont_show_it_again +'</button><button class="js-toolset-help-close js-toolset-help-close-once button-primary">'+ wpv_help_box_texts.wpv_close +'</button></div>');

        if (prms.footer === true) {
            $footer.appendTo($box);
        }

        prms.content.appendTo($box.find('.toolset-help-content'));

        this.wpvHelpRemove = function() {
            if( $box )
            $box.fadeOut('fast', function(){
            //    $(this).remove();
                if ( prms.onClose && typeof prms.onClose === 'function' ) {
                    prms.onClose.apply( $container, prms.args );
                }
            });
            return this;
        };

        if ( (prms.tutorialButtonText && prms.tutorialButtonURL) || (prms.linkText && prms.linkURL) ) {
            var $toolbar = $('<p class="toolset-help-content-toolbar"></p>');
            $toolbar.appendTo($box.find('.toolset-help-content'));
            if (prms.tutorialButtonText && prms.tutorialButtonURL) {
                $('<a href="' + prms.tutorialButtonURL + '" class="btn">' + prms.tutorialButtonText + '</a>').appendTo($toolbar);
            }
            if (prms.linkText && prms.linkURL) {
                $('<a href="' + prms.linkURL + '">' + prms.linkText + '</a>').appendTo($toolbar);
            }
        }

        if (prms.close === true) {
            $('<i class="icon-remove-sign js-toolset-help-close js-toolset-help-close-main"></i>').appendTo($box);
        }

        // bind close event to all close buttons
        var $closeButtons = $box.find('.js-toolset-help-close');
        if ( $closeButtons.length !== 0 ) {
            $closeButtons.on('click',function(){
                $container.wpvHelpRemove();
            });
        }

        $box.appendTo($container).hide();
        if ($container.hasClass('js-show-toolset-message')) {
            $box.unwrap();
        }
        if (prms.hidden === false) {
            $box.fadeIn('fast');
        }

        return this;
    };


$(document).on('click', '.update-button-wrap button', function(e){
    $(this).prop('disabled', true);
});

})(jQuery);

// http://stackoverflow.com/questions/1950038/jquery-fire-event-if-css-class-changed
(function(){
    // Your base, I'm in it!
    var originalPropMethod = jQuery.fn.prop, originalAttrMethod = jQuery.fn.attr;


	jQuery.fn.prop = function()
	{
		var result = originalPropMethod.apply( this, arguments );

		jQuery(this).trigger( "propertyChanged", arguments);

        // return the original result
        return result;
	};
	jQuery.fn.attr = function()
	{
		var result = originalAttrMethod.apply( this, arguments );

		jQuery(this).trigger( "attributeChanged", arguments);

        // return the original result
        return result;
	};

})();