/* global jQuery */
var oneApp = oneApp || {}, $oneApp = $oneApp || jQuery(oneApp);

(function($, $oneApp) {
	'use strict';

	var textColumnLayout = {
		init: function() {
			var self = this,
				$stage = $('.ttfmake-stage');

			// Add change event for columns select
			$stage.on('change', '.ttfmake-text-columns', function() {
				var $this = $(this);
				self.changeLayoutOptions($this);
			});

			$stage.on('change', '.ttfmp-text-column-layout-select', function(){
				var $this = $(this);
				self.changeColumnSizes($this);
			});

			$oneApp.on('ttfOverlayOpened', function(e, sectionType, $section){
				if ('text' === sectionType) {
					var currentValue = $('.ttfmp-text-column-layout-select', $section).val();
					self.changeLayoutOptions($('.ttfmake-text-columns', $section), currentValue);
				}
			});
		},

		changeLayoutOptions: function($columnSelect, currentValue) {
			var self = this,
				postRefresh = $('.ttfmp-text-column-layout-select-wrap').length > 0,
				layoutSelectClassWrap = ( postRefresh ) ? '.ttfmp-text-column-layout-select-wrap' : 'ttfmake-text-column-layout-select',
				$section = $columnSelect.parents('.ttfmake-section'),
				val = parseInt($columnSelect.val(), 10),
				$selectWrap = $(layoutSelectClassWrap, $section),
				$select = $('.ttfmp-text-column-layout-select', $selectWrap),
				$columnsStage = $('.ttfmake-text-columns-stage', $section),
				$columns = $('.ttfmake-text-column', $columnsStage),
				$sizes = $('.ttfmp-column-size-input', $columnsStage),
				options= ttfmpTextColumnLayout['layouts'];

			currentValue = currentValue || '';

			if (1 === val || 4 === val) {
				// Disable the select
				$select.prop('disabled', true);

				// Remove all options
				$select.find('option').remove();

				// Add the dummy value
				$select.append(
					$('<option></option>').attr('value', 'none').text('----')
				);

				// Set the value to a blank value
				$sizes.val('');

				// Reset the classes
				self.removeClasses($columns, 'ttfmake-column-width-');
			} else if (2 === val || 3 === val) {
				// Remove the options
				$select.find('option').remove();

				// Add the options
				$.each(options[val], function(key, value) {
					$select.append(
						$('<option></option>').attr('value', key).text(value)
					);
				});

				if ('' !== currentValue && $('option[value="' + currentValue + '"]', $select).length > 0) {
					$select.val(currentValue);
				} else {
					$select.val($select.find('option:first').attr('value'));
				}

				// Show the input if not already shown
				$select.prop('disabled', false);

				// Set the new values
				self.setNewValues($select.val(), $sizes, $columns);
			}

			// Update the classes
			self.removeClasses($columnsStage, 'ttfmake-text-layout-');
		},

		changeColumnSizes: function($layoutSelect) {
			var self = this,
				$section = $layoutSelect.parents('.ttfmake-section'),
				val = $layoutSelect.val(),
				$columnsStage = $('.ttfmake-text-columns-stage', $section),
				$columns = $('.ttfmake-text-column', $columnsStage),
				$sizes = $('.ttfmp-column-size-input', $columnsStage);

			// Update the columns classes
			self.removeClasses($columnsStage, 'ttfmake-text-layout-');
			$columnsStage.addClass('ttfmake-text-layout-' + val);

			// Set the new values
			self.setNewValues(val, $sizes, $columns);
		},

		removeClasses: function($el, classStub) {
			return $el.removeClass(function (index, css) {
				var regex = new RegExp('(^|\\s)' + classStub + '\\S+', 'g');
				return (css.match(regex) || []).join(' ');
			});
		},

		setNewValues: function(choice, $inputs, $columns) {
			var self = this;

			// Reset the size values
			$inputs.val('');
			self.removeClasses($columns, 'ttfmake-column-width-');

			// Set the new values
			switch(choice) {
				case 'two-thirds':
					$inputs.eq(0).val('two-thirds');
					$inputs.eq(1).val('one-third');

					$columns.eq(0).addClass('ttfmake-column-width-two-thirds');
					$columns.eq(1).addClass('ttfmake-column-width-one-third');

					break;
				case 'three-fourths':
					$inputs.eq(0).val('three-fourths');
					$inputs.eq(1).val('one-fourth');

					$columns.eq(0).addClass('ttfmake-column-width-three-fourths');
					$columns.eq(1).addClass('ttfmake-column-width-one-fourth');

					break;
				case 'two-fourths':
					$inputs.eq(0).val('one-half');
					$inputs.eq(1).val('one-fourth');
					$inputs.eq(2).val('one-fourth');

					$columns.eq(0).addClass('ttfmake-column-width-one-half');
					$columns.eq(1).addClass('ttfmake-column-width-one-fourth');
					$columns.eq(2).addClass('ttfmake-column-width-one-fourth');

					break;
			}
		}
	};

	textColumnLayout.init();
})(jQuery, $oneApp);