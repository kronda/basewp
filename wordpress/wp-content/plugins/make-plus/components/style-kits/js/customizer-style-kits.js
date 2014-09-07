/* global jQuery, ttfmpStyleKitData */
(function($) {
	var api = wp.customize,
		ttfmpStyleKits = {
			cache: {
				originals: {}
			},

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

				// Bind functionality to Load button
				ttfmpStyleKits.cache.$load.on('click', function(e) {
					e.preventDefault();
					var pack = ttfmpStyleKits.cache.$select.val();
					if ('undefined' !== typeof pack) {
						ttfmpStyleKits.load( ttfmpStyleKitData[pack]['definitions'] );
					}
				});

				// Bind functionality to Reset button
				ttfmpStyleKits.cache.$reset.on('click', function(e) {
					e.preventDefault();
					ttfmpStyleKits.reset();
				});

				// Re-cache the reset values when Customizer saves
				api.bind('saved', function() {
					ttfmpStyleKits.cacheOriginals();
				});

				// Detect when the Preview pane finishes loading
				$(document).on('preview-ready', function() {
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
			 * Load the option values from the selected style kit.
			 *
			 * @since 1.1.0.
			 *
			 * @param  object    data    The option values.
			 * @return void
			 */
			load: function( data ) {
				ttfmpStyleKits.showSpinner();

				$.each(data, function( settingId, v ) {
					api( settingId, function( setting ) {
						setting.set(v);

						// Manually update the color pickers
						var $picker = $('li[id$="' + settingId + '"] .color-picker-hex');

						if ($picker.length > 0) {
							$picker.wpColorPicker('color', v);
						}
					});
				});
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

				$.each(ttfmpStyleKits.cache.originals, function( settingId, v ) {
					api( settingId, function( setting ) {
						setting.set(v);

						// Manually update the color pickers
						var $picker = $('li[id$="' + settingId + '"] .color-picker-hex');

						if ($picker.length > 0) {
							$picker.wpColorPicker('color', v);
						}
					});
				});
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

	ttfmpStyleKits.init();
})(jQuery);

/**
 * Trigger an event when the Preview pane is ready
 *
 * This function needs to be in the global namespace so that the preview child frame can access it.
 */
var ttfmpDetectPreview = function() {
	jQuery(document).trigger('preview-ready');
};
