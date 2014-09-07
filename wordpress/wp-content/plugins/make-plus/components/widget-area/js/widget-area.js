/* global jQuery */
(function($) {
	'use strict';

	var widgetArea = {
		init: function() {
			$('.ttfmake-stage').on('click', '.ttfmp-create-widget-area', function(evt) {
				var $this = $(this),
					$thisColumn = $this.parents('.ttfmake-text-column'),
					$overlay = $('.ttfmp-widget-area-overlay', $thisColumn),
					$titleInput = $('.ttfmake-title', $thisColumn),
					$input = $('.ttfmp-text-widget-area', $thisColumn),
					currentVal = parseInt($input.val(), 10);

				evt.preventDefault();

				// Toggle the value
				if ( 1 === currentVal ) {
					$input.val(0);
					$this.html(ttfmpWidgetArea.widgetAreaString);

					$overlay.animate({
						opacity: 0
					}, {
						duration: 300,
						complete: function() {
							$overlay.css({
								zIndex: -10
							});
						}
					});
				} else {
					$input.val(1);
					$this.html(ttfmpWidgetArea.textColumnString);

					$overlay
						.css({
							zIndex: 9999
						})
						.animate({
							opacity: 1
						}, 300);

					// Focus on the label input
					$titleInput.focus();
				}
			});
		}
	};

	widgetArea.init();
})(jQuery);