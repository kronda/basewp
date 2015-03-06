/**
 * @package Make
 */

/* global jQuery, ttfmakeCustomizerL10n */
( function( $ ) {
	var api = wp.customize,
		fontChoices, customControls,
		upgrade;

	/**
	 * Font Choices
	 */
	fontChoices = {
		cache: {},

		init: function() {
			// Cache elements
			fontChoices.cache.rtl = $('body').hasClass('rtl');
			fontChoices.cache.options = {};
			$.each(ttfmakeCustomizerL10n.fontOptions, function(index, key) {
				fontChoices.cache.options[key] = $('select', '#customize-control-ttfmake_' + key);
			});

			// Build
			fontChoices.buildChoices();

			// Insert
			fontChoices.insertChoices();
		},

		// Compile the choices from the JSON object into HTML
		buildChoices: function() {
			fontChoices.cache.choices = '';
			$.each(ttfmakeCustomizerL10n.allFontChoices, function(index, choice) {
				var value = choice.k,
					label = choice.l,
					disabled = (!isNaN(parseFloat(+value)) && isFinite(value)) ? ' disabled="disabled"' : '';
				fontChoices.cache.choices += '<option value="' + value + '"' + disabled + '>' + label + '</option>';
			});
		},

		// Insert the HTML into each font family select
		insertChoices: function() {
			$.each(fontChoices.cache.options, function(key, element) {
				if (fontChoices.cache.rtl) {
					element.addClass('chosen-rtl');
				}
				api( key, function( setting ) {
					element.on('chosen:ready', function() {
						var v = setting.get();
						$(this)
							.html(fontChoices.cache.choices)
							.val( v )
							.trigger('chosen:updated');
					});
					element.chosen({
						no_results_text          : ttfmakeCustomizerL10n.chosen_no_results_fonts,
						search_contains          : true,
						width                    : '100%'
					});
				} );
			});
		}
	};

	/**
	 *
	 */
	customControls = {
		cache: {},

		//
		init: function() {
			// Populate cache
			this.cache.$buttonset  = $('.ttfmake-control-buttonset, .ttfmake-control-image');
			this.cache.$bgposition = $('.ttfmake-control-background-position');
			this.cache.$range      = $('.ttfmake-control-range');

			// Initialize Button sets
			if (this.cache.$buttonset.length > 0) {
				this.buttonset();
			}

			// Initialize Background Position
			if (this.cache.$bgposition.length > 0) {
				this.bgposition();
			}

			// Initialize ranges
			if (this.cache.$range.length > 0) {
				this.range();
			}
		},

		//
		buttonset: function() {
			this.cache.$buttonset.buttonset();
		},

		//
		bgposition: function() {
			// Initialize button sets
			this.cache.$bgposition.buttonset({
				create : function(event) {
					var $control = $(event.target),
						$positionButton = $control.find('label'),
						$caption = $control.parent().find('.background-position-caption');

					$positionButton.on('click', function() {
						var label = $(this).data('label');
						$caption.text(label);
					});
				}
			});
		},

		//
		range: function() {
			this.cache.$range.each(function() {
				var $input = $(this),
					$slider = $input.parent().find('.ttfmake-range-slider'),
					value = parseFloat( $input.val() ),
					min = parseFloat( $input.attr('min') ),
					max = parseFloat( $input.attr('max') ),
					step = parseFloat( $input.attr('step') );

				$slider.slider({
					value : value,
					min   : min,
					max   : max,
					step  : step,
					slide : function(e, ui) {
						$input.val(ui.value).keyup().trigger('change');
					}
				});
				$input.val( $slider.slider('value') );
			});
		}
	};

	// Load font choices after Customizer initialization is complete.
	$(document).ready(function() {
		fontChoices.init();
		customControls.init();
	});

	/**
	 * Visibility toggling for some controls
	 */
	$.each({
		'general-layout': {
			controls: [ 'ttfmake_background-info' ],
			callback: function( to ) { return 'full-width' === to; }
		},
		'main-background-color-transparent': {
			controls: [ 'ttfmake_main-background-color' ],
			callback: function( to ) { return ! to; }
		},
		'header-background-transparent': {
			controls: [ 'ttfmake_header-background-color' ],
			callback: function( to ) { return ! to; }
		},
		'header-bar-background-transparent': {
			controls: [ 'ttfmake_header-bar-background-color' ],
			callback: function( to ) { return ! to; }
		},
		'footer-background-transparent': {
			controls: [ 'ttfmake_footer-background-color' ],
			callback: function( to ) { return ! to; }
		},
		'background_image': {
			controls: [ 'ttfmake_background_position_x', 'ttfmake_background_attachment', 'ttfmake_background_size' ],
			callback: function( to ) { return !! to; }
		},
		'header-background-image': {
			controls: [ 'ttfmake_header-background-repeat', 'ttfmake_header-background-position', 'ttfmake_header-background-attachment', 'ttfmake_header-background-size' ],
			callback: function( to ) { return !! to; }
		},
		'main-background-image': {
			controls: [ 'ttfmake_main-background-repeat', 'ttfmake_main-background-position', 'ttfmake_main-background-attachment', 'ttfmake_main-background-size' ],
			callback: function( to ) { return !! to; }
		},
		'footer-background-image': {
			controls: [ 'ttfmake_footer-background-repeat', 'ttfmake_footer-background-position', 'ttfmake_footer-background-attachment', 'ttfmake_footer-background-size' ],
			callback: function( to ) { return !! to; }
		},
		'header-layout': {
			controls: [ 'ttfmake_header-branding-position' ],
			callback: function( to ) { return ( '1' == to || '3' == to ); }
		},
		'header-show-social': {
			controls: [ 'ttfmake_font-size-header-bar-icon' ],
			callback: function( to ) { return !! to; }
		},
		'footer-show-social': {
			controls: [ 'ttfmake_font-size-footer-icon' ],
			callback: function( to ) { return !! to; }
		},
		'layout-blog-featured-images': {
			controls: [ 'ttfmake_layout-blog-featured-images-alignment' ],
			callback: function( to ) { return ( 'post-header' === to ); }
		},
		'layout-archive-featured-images': {
			controls: [ 'ttfmake_layout-archive-featured-images-alignment' ],
			callback: function( to ) { return ( 'post-header' === to ); }
		},
		'layout-search-featured-images': {
			controls: [ 'ttfmake_layout-search-featured-images-alignment' ],
			callback: function( to ) { return ( 'post-header' === to ); }
		},
		'layout-post-featured-images': {
			controls: [ 'ttfmake_layout-post-featured-images-alignment' ],
			callback: function( to ) { return ( 'post-header' === to ); }
		},
		'layout-page-featured-images': {
			controls: [ 'ttfmake_layout-page-featured-images-alignment' ],
			callback: function( to ) { return ( 'post-header' === to ); }
		},
		'layout-blog-post-date': {
			controls: [ 'ttfmake_layout-blog-post-date-location' ],
			callback: function( to ) { return ( 'none' !== to ); }
		},
		'layout-archive-post-date': {
			controls: [ 'ttfmake_layout-archive-post-date-location' ],
			callback: function( to ) { return ( 'none' !== to ); }
		},
		'layout-search-post-date': {
			controls: [ 'ttfmake_layout-search-post-date-location' ],
			callback: function( to ) { return ( 'none' !== to ); }
		},
		'layout-post-post-date': {
			controls: [ 'ttfmake_layout-post-post-date-location' ],
			callback: function( to ) { return ( 'none' !== to ); }
		},
		'layout-page-post-date': {
			controls: [ 'ttfmake_layout-page-post-date-location' ],
			callback: function( to ) { return ( 'none' !== to ); }
		},
		'layout-blog-post-author': {
			controls: [ 'ttfmake_layout-blog-post-author-location' ],
			callback: function( to ) { return ( 'none' !== to ); }
		},
		'layout-archive-post-author': {
			controls: [ 'ttfmake_layout-archive-post-author-location' ],
			callback: function( to ) { return ( 'none' !== to ); }
		},
		'layout-search-post-author': {
			controls: [ 'ttfmake_layout-search-post-author-location' ],
			callback: function( to ) { return ( 'none' !== to ); }
		},
		'layout-post-post-author': {
			controls: [ 'ttfmake_layout-post-post-author-location' ],
			callback: function( to ) { return ( 'none' !== to ); }
		},
		'layout-page-post-author': {
			controls: [ 'ttfmake_layout-page-post-author-location' ],
			callback: function( to ) { return ( 'none' !== to ); }
		},
		'layout-blog-comment-count': {
			controls: [ 'ttfmake_layout-blog-comment-count-location' ],
			callback: function( to ) { return ( 'none' !== to ); }
		},
		'layout-archive-comment-count': {
			controls: [ 'ttfmake_layout-archive-comment-count-location' ],
			callback: function( to ) { return ( 'none' !== to ); }
		},
		'layout-search-comment-count': {
			controls: [ 'ttfmake_layout-search-comment-count-location' ],
			callback: function( to ) { return ( 'none' !== to ); }
		},
		'layout-post-comment-count': {
			controls: [ 'ttfmake_layout-post-comment-count-location' ],
			callback: function( to ) { return ( 'none' !== to ); }
		},
		'layout-page-comment-count': {
			controls: [ 'ttfmake_layout-page-comment-count-location' ],
			callback: function( to ) { return ( 'none' !== to ); }
		}
	}, function( settingId, o ) {
		api( settingId, function( setting ) {
			$.each( o.controls, function( i, controlId ) {
				api.control( controlId, function( control ) {
					var visibility = function( to ) {
						control.container.toggle( o.callback( to ) );
					};

					visibility( setting.get() );
					setting.bind( visibility );
				});
			});
		});
	});

	// Add Make Plus message
	if ('undefined' !== typeof ttfmakeCustomizerL10n) {
		upgrade = $('<a class="ttfmake-customize-plus"></a>')
			.attr('href', ttfmakeCustomizerL10n.plusURL)
			.attr('target', '_blank')
			.text(ttfmakeCustomizerL10n.plusLabel)
		;
		$('.preview-notice').append(upgrade);
		// Remove accordion click event
		$('.ttfmake-customize-plus').on('click', function(e) {
			e.stopPropagation();
		});
	}
} )( jQuery );
