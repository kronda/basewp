/*  This is a temporary file, we should probably have separate JS for each admin page */

if ( typeof WPV_Toolset === 'undefined' ) {
	var WPV_Toolset = {};
	WPV_Toolset.message = {};
	WPV_Toolset.message.container = null;
}

if (!window.console) {var console = {};}
if (!console.log) {console.log = function() {};}

jQuery(function($) {
	
	/**
	* COLORBOX START
	*
	* On deprecate route
	*/

    $.extend($.colorbox.settings, { // override some Colorbox defaults
        transition: 'fade',
        opacity: 0.3,
        speed: 150,
        fadeOut : 0,
        closeButton: false,
        trapFocus: false
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

    // Bind close event to .js-dialog-close classes
	// @todo remove when colorbox is also removed, that classname will never be used anymore
    $(document).on('click', '.js-dialog-close', function(e) {
        e.preventDefault();
        $.colorbox.close();
        return false;
    });
	
	/**
	* COLORBOX END
	*/

	/*
	* Remove toolset alerts
	* Hide toolset alerts (but don't remove)
	*
	* @deprecated - seems not used anywhere
    $(document).on('click', '.js-toolset-alert-remove', function(){
        $(this).closest('.toolset-alert').fadeOut('fast',function(){
            $(this).remove();
        });
    });

    $(document).on('click', '.js-toolset-alert-hide', function(){
        $(this).closest('.toolset-alert').fadeOut('fast');
    });
	*/

    // Run wpvToolsetHelp on .js-show-toolset-message elements
	// @todo this should be moved to the common utils.js file if needed, CHECK
    $.each( $( '.js-show-toolset-message:not(.js-show-toolset-message-inited)' ), function() {
        $( this )
			.addClass( 'js-show-toolset-message-inited' )
            .show()
            .wpvToolsetHelp();
    });

});

// @todo This is already in common util.js, keep just for backwards compatibility until Views 1.12

if ( typeof jQuery.fn.wpvToolsetHelp === 'undefined' ) {

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

			var $box = $('<div class="toolset-help ' + prms.classname + '"><div class="toolset-help-content"></div><div class="toolset-help-sidebar"></div></div>');

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
				$('<i class="icon-remove js-toolset-help-close js-toolset-help-close-main"></i>').appendTo($box);
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

	})(jQuery);

}