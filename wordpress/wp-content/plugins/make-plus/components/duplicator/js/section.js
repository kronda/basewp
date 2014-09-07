/* global jQuery */
var oneApp = oneApp || {}, ttfmakeMCE = ttfmakeMCE || '';

(function($) {
	'use strict';

	var duplicatorSection = {
		init: function() {
			$('.ttfmake-stage').on('click', '.ttfmp-duplicate-section', function(evt) {
				evt.preventDefault();

				var $this = $(this),
					$el = $this.parents('.ttfmake-section'),
					sectionType = $el.attr('data-section-type'),
					$stage = $('.ttfmake-stage'),
					$spinner = $('<span class="spinner"></span>'),
					ttfmakeMCEBackup = ttfmakeMCE,
					$appendedSection, view;

				// Activate the spinner
				$this.after($spinner);

				// Update the TinyMCE value property before serializing the data
				$('.wp-editor-wrap', $el).each(function(index, el) {
					var $el = $(el),
						editorID = $el.attr('id').replace('wp-', '').replace('-wrap', ''),
						editor = tinymce.get(editorID),
						$editorEl = $('#' + editorID),
						mode = ($el.hasClass('html-active')) ? 'text' : 'tmce';

					// Only if the TinyMCE instance is available, get the content
					if (null !== editor && 'tmce' === mode) {
						$editorEl.val(editor.getContent());
					}
				});

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

							// Denotes if the TinyMCE init process should init the editor or not
							ttfmakeMCE = getUserSetting('editor');

							// Init TinyMCE
							if ('banner' === sectionType) {
								// Banner slides have editors and need to init'd separately
								$('.wp-editor-wrap', view.$el).each(function(index, el) {
									var $el = $(el),
										id = $el.parents('.ttfmake-banner-slide').attr('data-id'),
										editorID = $el.attr('id').replace('wp-', '').replace('-wrap', ''),
										tempEditorID = editorID.replace(id, '') + 'temp';

									oneApp.initEditor(editorID, tempEditorID);
								});
							} else {
								// Init TinyMCE
								oneApp.initAllEditors(view.$el.attr('id'), view.model);
							}

							ttfmakeMCE = ttfmakeMCEBackup;

							// Initiate sortables
							if ('text' === sectionType) {
								oneApp.initializeTextColumnSortables(view);
							}
						} else {
							duplicatorSection.handleError(data, $this);
						}

						// Remove the spinner
						$spinner.remove();
					},
					error: function(data) {
						duplicatorSection.handleError(data, $this);

						// Remove the spinner
						$spinner.remove();
					},
					data: {
						nonce: ttfmpDuplicateSection.nonce,
						data: $('#post').serialize(),
						sectionType: sectionType,
						id: $el.attr('data-id')
					}
				});
			});
		},

		handleError: function(data, $link) {
			var message = (data.message && '' !== data.message) ? data.message : ttfmpDuplicateSection.defaultError,
				$html = $('<span class="ttfmp-duplicator-error">&nbsp;' + message + '</span>');

			// Append the error message, then fade it out and remove it after 3s
			$link.after($html.delay(3000).fadeOut(function(){
				$html.remove();
			}));

		}
	};

	duplicatorSection.init();
})(jQuery);
