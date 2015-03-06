(function($) {
	var api = wp.customize,
		ttfmpTypekit = {
		cache: {},

		init: function() {
			// Cache elements
			ttfmpTypekit.cache.$textInput = $('input', '#customize-control-ttfmp-typekit-id, #customize-control-ttfmake_typekit-id');
			ttfmpTypekit.cache.$wrapper = $('#accordion-section-ttfmake_font, #accordion-section-ttfmake_font-typekit');
			ttfmpTypekit.cache.$descriptionText = $('p', '#customize-control-ttfmp-typekit-load-fonts, #customize-control-ttfmake_typekit-load-fonts');
			ttfmpTypekit.cache.$reset = $('a:nth-child(1)', ttfmpTypekit.cache.$descriptionText);
			ttfmpTypekit.cache.$load = $('a:nth-child(2)', ttfmpTypekit.cache.$descriptionText);

			// Cache the font family controls
			ttfmpTypekit.cache.options = {};
			$.each(ttfmpTypekitData.optionKeys, function(index, key) {
				ttfmpTypekit.cache.options[key] = $('select', '#customize-control-ttfmake_' + key);
			});

			// Denote which items are Typekit fonts
			ttfmpTypekit.markTypekitChoices();

			// Add classes to elements
			ttfmpTypekit.cache.$reset.addClass('button reset-fonts');
			ttfmpTypekit.cache.$load.addClass('button load-fonts');

			// Load Fonts
			ttfmpTypekit.cache.$wrapper.on('click', '.load-fonts', function(evt) {
				evt.preventDefault();

				// Add the loading status
				ttfmpTypekit.showSpinner();

				// Remove errors
				ttfmpTypekit.removeMessages();

				if ('' !== ttfmpTypekit.cache.$textInput.val()) {
					ttfmpTypekit.makeRequest(ttfmpTypekit.cache.$textInput.val(), ttfmpTypekitData.nonce);
				} else {
					ttfmpTypekit.addError(ttfmpTypekitData.noInputError);
					ttfmpTypekit.hideSpinner();
				}
			});

			// Reset
			ttfmpTypekit.cache.$wrapper.on('click', '.reset-fonts', function(evt) {
				evt.preventDefault();

				// Add the loading status
				ttfmpTypekit.showSpinner();

				// Remove errors
				ttfmpTypekit.removeMessages();

				ttfmpTypekit.resetRequest(ttfmpTypekitData.nonce);
			});
		},

		markTypekitChoices: function() {
			_.each(ttfmpTypekitData.typekitChoices, function(value) {
				$.each(ttfmpTypekit.cache.options, function(key, element) {
					$('option[value="' + value +'"]', element).addClass('ttfmp-typekit-choice');
				});
			});

			// Mark the header as a choice
			if (ttfmpTypekitData.typekitChoices.length > 0) {
				$.each(ttfmpTypekit.cache.options, function(key, element) {
					$('.ttfmp-typekit-choice', element).first().prev().addClass('ttfmp-typekit-choice');
				});
			}
		},

		makeRequest: function(id, nonce) {
			wp.ajax.send(
				'ttfmp_get_typekit_fonts', {
					success: ttfmpTypekit.handleSuccess,
					error: ttfmpTypekit.handleError,
					data: {
						nonce: nonce,
						id: id
					}
				}
			);
		},

		handleSuccess: function(data) {
			var optionsHTML = ttfmpTypekit.buildOption(0, '--- ' + ttfmpTypekitData.headerLabel + ' ---', true),
				optionsVal = {};
				$.each(ttfmpTypekit.cache.options, function(key, element) {
					optionsVal[key] = element.val();
				});

			$.each(data, function(index, value) {
				optionsHTML += ttfmpTypekit.buildOption(index, value, false);
			});

			// Remove the previous fonts
			ttfmpTypekit.removeFonts();

			// Prepend the new options
			ttfmpTypekit.prependFonts(optionsHTML);

			// Set the correct current vals
			$.each(ttfmpTypekit.cache.options, function(key, element) {
				element.val( optionsVal[key] ).trigger('chosen:updated');
			});

			// Add success message
			ttfmpTypekit.addSuccess(ttfmpTypekitData.success);

			// Remove the loading indicator
			ttfmpTypekit.hideSpinner();
		},

		buildOption: function(index, value, disabled) {
			disabled = (true === disabled) ? ' disabled="disabled"' : '';
			return '<option value="' + index + '" class="ttfmp-typekit-choice"' + disabled + '>' + value + '</option>';
		},

		prependFonts: function(optionsHTML) {
			$.each(ttfmpTypekit.cache.options, function(key, element) {
				element.prepend(optionsHTML).trigger('chosen:updated');
			});
		},

		removeFonts: function() {
			$.each(ttfmpTypekit.cache.options, function(key, element) {
				$('.ttfmp-typekit-choice', element).trigger('chosen:updated');
			});
		},

		handleError: function(data) {
			ttfmpTypekit.addError(ttfmpTypekitData.ajaxError);
			ttfmpTypekit.hideSpinner();
		},

		showSpinner: function() {
			ttfmpTypekit.cache.$descriptionText.append('<span class="spinner"></span>');
		},

		hideSpinner: function() {
			$('.spinner', ttfmpTypekit.cache.$descriptionText).remove();
		},

		addError: function(message) {
			ttfmpTypekit.removeMessages();
			ttfmpTypekit.cache.$descriptionText.prepend('<span class="error">' + message + '<br /></span>');
		},

		addSuccess: function(message) {
			ttfmpTypekit.removeMessages();
			ttfmpTypekit.cache.$descriptionText.prepend('<span class="success">' + message + '<br /></span>');
		},

		removeMessages: function() {
			$('.error, .success', ttfmpTypekit.cache.$descriptionText).remove();
		},

		resetRequest: function(nonce) {
			wp.ajax.send(
				'ttfmp_reset_preview', {
					success: function(data) {
						ttfmpTypekit.removeFonts();
						ttfmpTypekit.resetFontOptions(data);
						ttfmpTypekit.addSuccess(ttfmpTypekitData.resetSuccess);
						ttfmpTypekit.hideSpinner();
					},
					error: ttfmpTypekit.handleError,
					data: {
						nonce: nonce
					}
				}
			);
		},

		resetFontOptions: function(data) {
			$.each(data, function(index, value) {
				api(index, function(setting) {
					setting.set(value);
					api.control('ttfmake_'+index, function(control) {
						var $element = $('select', control.selector);
						$element.val(value).trigger('chosen:updated');
					});
				});
			});
		}
	};

	$(document).ready(function() {
		ttfmpTypekit.init();
	});
})(jQuery);