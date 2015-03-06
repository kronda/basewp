/* global jQuery */
var oneApp = oneApp || {};

(function($) {
	'use strict';

	var disable = false,
		duplicatorSection = {
		init: function() {
			$('.ttfmake-stage').on('click', '.ttfmp-duplicate-section', function(evt) {
				evt.preventDefault();

				// Only proceed if duplication is not currently disabled
				if (false === disable) {
					var $this = $(this),
						$el = $this.parents('.ttfmake-section'),
						sectionType = $el.attr('data-section-type'),
						$stage = $('.ttfmake-stage'),
						$appendedSection, view;

					// Activate the spinner
					$this.addClass('ttfmp-spinner');
					disable = true;

					wp.ajax.send( 'ttf_duplicate_section', {
						success: function(data) {
							if (data.result && 'success' === data.result && data.section) {
								$appendedSection = $(data.section);
								$appendedSection.appendTo($stage);

								// Init the views
								view = oneApp.initAllViews($appendedSection);

								// Scroll to the content
								oneApp.scrollToAddedView(view);

								// Register the section with the sortable order field
								oneApp.addOrderValue(view.model.get('id'), oneApp.cache.$sectionOrder);

								// Initiate sortables
								if ('text' === sectionType) {
									oneApp.initializeTextColumnSortables(view);
									duplicatorSection.initFrames(view);
								}
							} else {
								duplicatorSection.handleError(data, $this);
							}

							// Remove the spinner
							$this.removeClass('ttfmp-spinner');
							disable = false;
						},
						error: function(data) {
							duplicatorSection.handleError(data, $this);

							// Remove the spinner
							$this.removeClass('ttfmp-spinner');
							disable = false;
						},
						data: {
							nonce: ttfmpDuplicateSection.nonce,
							data: $('#post').serialize(),
							sectionType: sectionType,
							id: $el.attr('data-id')
						}
					});
				}
			});
		},

		handleError: function(data, $link) {
			var message = (data.message && '' !== data.message) ? data.message : ttfmpDuplicateSection.defaultError,
				$html = $('<span class="ttfmp-duplicator-error">&nbsp;' + message + '</span>');

			// Append the error message, then fade it out and remove it after 3s
			$link.after($html.delay(3000).fadeOut(function(){
				$html.remove();
			}));
		},

		initFrames: function(view) {
			var $frames = $('iframe', view.$el),
				link = oneApp.getFrameHeadLinks(),
				id, $this;

			$.each($frames, function() {
				$this = $(this);
				id = $this.attr('id').replace('ttfmake-iframe-', '');
				oneApp.initFrame(id, link);
			});
		}
	};

	duplicatorSection.init();
})(jQuery);
