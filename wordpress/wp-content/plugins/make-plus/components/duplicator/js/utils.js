/* global jQuery */
var oneApp = oneApp || {};

(function($) {
	'use strict';

	oneApp.initAllViews = function($el) {
		var sectionType = $el.attr('data-section-type');

		if ('banner' === sectionType) {
			// Reset the color picker
			$('.ttfmake-banner-slide', $el).each(function(index, element){
				var $subEl = $(element),
					$input = $('.ttfmake-banner-slide-background-color', $subEl),
					$picker = $('.wp-picker-container', $subEl);

				$picker.replaceWith($input);
			});

			// Initialize the view
			oneApp.initBannerSlideViews($el);
		}

		if ('gallery' === sectionType) {
			// Reset the color picker
			var $input = $('.ttfmake-gallery-background-color', $el),
				$picker = $('.wp-picker-container', $el);

			$picker.replaceWith($input);

			// Initialize the view
			oneApp.initGalleryItemViews($el);
		}

		return oneApp.initView($el);
	};

	oneApp.initView = function($el) {
		var sectionType = $el.attr('data-section-type'),
			id = $el.attr('data-id'),
			sectionModel, modelViewName, view, viewName;

		// Build the model
		sectionModel = new oneApp.SectionModel({
			sectionType: sectionType,
			id: id
		});

		// Ensure that a view exists for the section, otherwise use the base view
		modelViewName = sectionModel.get('viewName') + 'View';
		viewName      = (true === oneApp.hasOwnProperty(modelViewName)) ? modelViewName : 'SectionView';

		// Create view
		view = new oneApp[viewName]({
			model: sectionModel,
			el: $('#ttfmake-section-' + id),
			serverRendered: true
		});

		return view;
	};
})(jQuery);
