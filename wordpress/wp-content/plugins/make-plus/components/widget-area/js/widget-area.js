/* global jQuery */
(function($) {
	'use strict';

	var widgetArea = {
		init: function() {
			var $stage = $('.ttfmake-stage');
			$stage.on('click', '.ttfmp-create-widget-area', function(evt) {
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

					if ('undefined' === ttfmakeBuilderData.postRefresh) {
						$this.html(ttfmpWidgetArea.widgetAreaString);
					}

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

					if ('undefined' === ttfmakeBuilderData.postRefresh) {
						$this.html(ttfmpWidgetArea.textColumnString);
					}

					$overlay
						.css({
							zIndex: 1111
						})
						.animate({
							opacity: 1
						}, 300);

					// Focus on the label input
					$titleInput.focus();
				}
			});

			$stage.on('click', '.remove-widget-link', function(evt) {
				var $this = $(this),
					$widgetSection = $this.parents('.ttfmp-widget-area-display'),
					$widgetOrderInput = $('.widgets', $widgetSection),
					$widget = $this.parents('li'),
					widgetID = $widget.attr('data-id');

				evt.preventDefault();

				// Remove the element from the DOM
				$widget.animate({
						opacity: 'toggle',
						height: 'toggle'
					},
					oneApp.options.closeSpeed, function() {
						$widget.remove();
					}
				);

				// Remove the ID from the section order
				oneApp.removeOrderValue(widgetID, $widgetOrderInput);
			});

			widgetArea.initSortables();
		},

		initSortables: function() {
			$('.ttfmp-widget-list').sortable({
				handle: '.ttfmake-sortable-handle',
				placeholder: 'sortable-placeholder',
				forcePlaceholderSizeType: true,
				distance: 2,
				tolerance: 'pointer',
				stop: function(event, ui) {
					var $this = $(this),
						$item = $(ui.item.get(0)),
						$widgetSection = $item.parents('.ttfmp-widget-area-display'),
						$widgetOrderInput = $('.widgets', $widgetSection),
						order = $this.sortable('toArray', {attribute: 'data-id'});

					// Set the val of the input
					oneApp.setOrder(order, $widgetOrderInput);
				}
			});
		}
	};

	widgetArea.init();
})(jQuery);