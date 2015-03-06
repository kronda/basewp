/* global jQuery, ttfmpStyleKitData */
(function($) {
	var api = wp.customize,
		ttfmpStyleKits = {
			/**
			 * Cache for jQuery element selections and other data.
			 *
			 * @since 1.1.0.
			 */
			cache: {
				originals: {}
			},

			/**
			 * Boolean switch to determine status of the Reset button
			 *
			 * @since 1.4.6.
			 */
			resettable: false,

			/**
			 * Populate cache, bind events
			 *
			 * @since 1.1.0.
			 *
			 * @return void
			 */
			init: function() {
				// Cache elements
				ttfmpStyleKits.cache.$select = $('select', '#customize-control-ttfmake_stylekit-dropdown');
				ttfmpStyleKits.cache.$buttons = $('#customize-control-ttfmake_stylekit-buttons');
				ttfmpStyleKits.cache.$load = $('.load-design', ttfmpStyleKits.cache.$buttons);
				ttfmpStyleKits.cache.$reset = $('.reset-design', ttfmpStyleKits.cache.$buttons);

				// Cache the reset values
				ttfmpStyleKits.cacheOriginals();

				// Prevent default action on buttons
				ttfmpStyleKits.cache.$buttons.find('.load-design, .reset-design').on('click', function(e) {
					e.preventDefault();
				});

				// Disable buttons until preview pane is loaded
				ttfmpStyleKits.disableButtons();

				// Re-cache the reset values when Customizer saves
				api.bind('saved', function() {
					ttfmpStyleKits.cacheOriginals();
					ttfmpStyleKits.cache.$reset.off('click.ttfmpStyleKits').addClass('disabled');
					ttfmpStyleKits.resettable = false;
				});

				// Detect when the Preview pane finishes loading
				$(document).on('preview-ready', function() {
					ttfmpStyleKits.enableLoad(ttfmpStyleKits.cache.$load);
					if (true === ttfmpStyleKits.resettable) {
						ttfmpStyleKits.enableReset(ttfmpStyleKits.cache.$reset);
					}
					ttfmpStyleKits.hideSpinner();
				});
			},

			/**
			 * Store the option values from the last save, in case of reset.
			 *
			 * @since 1.1.0.
			 *
			 * @return void
			 */
			cacheOriginals: function() {
				$.each(ttfmpStyleKitData.defaults, function( settingId, v ) {
					api( settingId, function( setting ) {
						ttfmpStyleKits.cache.originals[settingId] = setting();
					});
				});
			},

			/**
			 * Bind a namespaced click event to the Load button.
			 *
			 * @since 1.4.6.
			 *
			 * @param  $el     jQuery selection
			 * @return void
			 */
			enableLoad: function($el) {
				$el.off('click.ttfmpStyleKits').on('click.ttfmpStyleKits', function() {
					var pack = ttfmpStyleKits.cache.$select.val();
					if ('undefined' !== typeof ttfmpStyleKitData[pack]) {
						ttfmpStyleKits.load( ttfmpStyleKitData[pack]['definitions'] );
					}
				}).removeClass('disabled');
			},

			/**
			 * Bind a namespaced click event to the Reset button.
			 *
			 * @since 1.4.6.
			 *
			 * @param  $el     jQuery selection
			 * @return void
			 */
			enableReset: function($el) {
				$el.off('click.ttfmpStyleKits').on('click.ttfmpStyleKits', function() {
					ttfmpStyleKits.reset();
				}).removeClass('disabled');
			},

			/**
			 * Unbind the namespaced click event from Style Kits-related buttons.
			 *
			 * @since 1.4.6.
			 *
			 * @return void
			 */
			disableButtons: function() {
				var $buttons = ttfmpStyleKits.cache.$load.add(ttfmpStyleKits.cache.$reset);
				$buttons.each(function() {
					$(this).off('click.ttfmpStyleKits').addClass('disabled');
				});
			},

			/**
			 * Load the option values from the selected style kit.
			 *
			 * @since 1.1.0.
			 *
			 * @param  object    data    The option values.
			 * @return void
			 */
			load: function( data ) {
				ttfmpStyleKits.showSpinner();
				ttfmpStyleKits.disableButtons();

				$.each(data, function( settingId, v ) {
					api( settingId, function( setting ) {
						// Prevent endless spinner in the case where no settings have actually changed
						setting.set('');

						// Now actually set the value
						setting.set(v);

						// Manually update certain controls
						api.control('ttfmake_' + settingId, function(control) {
							ttfmpStyleKits.manualUpdate(control, v);
						});
					});
				});

				ttfmpStyleKits.resettable = true;
			},

			/**
			 * Load the cached option values from the last save.
			 *
			 * @since 1.1.0.
			 *
			 * @return void
			 */
			reset: function() {
				ttfmpStyleKits.showSpinner();
				ttfmpStyleKits.disableButtons();

				$.each(ttfmpStyleKits.cache.originals, function( settingId, v ) {
					api( settingId, function( setting ) {
						// Prevent endless spinner in the case where no settings have actually changed
						setting.set('');

						// Now actually set the value
						setting.set(v);

						// Manually update certain controls
						api.control('ttfmake_' + settingId, function(control) {
							ttfmpStyleKits.manualUpdate(control, v);
						});
					});
				});

				ttfmpStyleKits.resettable = false;
			},

			/**
			 * Manually update controls that don't auto-detect changes.
			 *
			 * @since 1.5.0.
			 *
			 * @param  control    The Customizer control object
			 * @param  v          The value to set to the control
			 * @return void
			 */
			manualUpdate: function(control, v) {
				var $colorpicker  = $(control.selector).find('.color-picker-hex'),
					$chosenselect = $(control.selector).find('.chosen-container').siblings('select'),
					$uiSlider     = $(control.selector).find('.ttfmake-control-range'),
					$uiButtonset  = $(control.selector).find('.ttfmake-control-buttonset, .ttfmake-control-image');

				// Color picker
				if ($colorpicker.length > 0) {
					$colorpicker.wpColorPicker('color', v);
				}

				// Chosen select
				if ($chosenselect.length > 0) {
					$chosenselect.trigger('chosen:updated');
				}

				// jQuery UI Slider
				if ($uiSlider.length > 0) {
					$uiSlider.trigger('slidechange');
				}

				// jQuery UI Buttonset
				if ($uiButtonset.length > 0) {
					$uiButtonset.trigger('change');
				}
			},

			/**
			 * Add a spinner element.
			 *
			 * @since 1.1.0.
			 *
			 * @return void
			 */
			showSpinner: function() {
				ttfmpStyleKits.cache.$buttons.find('p').append('<span class="spinner"></span>');
			},

			/**
			 * Remove the spinner element.
			 *
			 * @since 1.1.0.
			 *
			 * @return void
			 */
			hideSpinner: function() {
				$('.spinner', ttfmpStyleKits.cache.$buttons).remove();
			}
		};

	// Wait until Customizer initialization is complete.
	$(document).ready(function() {
		ttfmpStyleKits.init();
	});
})(jQuery);

/**
 * Trigger an event when the Preview pane is ready
 *
 * This function needs to be in the global namespace so that the preview child frame can access it.
 */
var ttfmpDetectPreview = function() {
	jQuery(document).trigger('preview-ready');
};
