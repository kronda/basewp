/* global jQuery */
(function($) {
	'use strict';

	var textColumnLayout = {
		init: function() {
			var self = this,
				$stage = $('.ttfmake-stage');

			// Add change event for columns select
			$stage.on('change', '.ttfmake-text-columns', function() {
				var $this = $(this),
					$section = $this.parents('.ttfmake-section'),
					val = parseInt($this.val(), 10),
					$selectWrap = $('.ttfmake-text-column-layout-select', $section),
					$select = $('.ttfmp-text-column-layout-select', $selectWrap),
					$columnsStage = $('.ttfmake-text-columns-stage', $section),
					$columns = $('.ttfmake-text-column', $columnsStage),
					$sizes = $('.ttfmp-column-size-input', $columnsStage),
					options= ttfmpTextColumnLayout['layouts'];

				if (1 === val || 4 === val) {
					$selectWrap.hide();
					$sizes.val('');
					self.removeClasses($columns, 'ttfmake-column-width-');
				} else if (2 === val || 3 === val) {
					// Empty the options
					$select.empty();

					// Add the options
					$.each(options[val], function(key, value) {
						$select.append(
							$('<option></option>').attr('value', key).text(value)
						);
					});

					// Show the input if not already shown
					$selectWrap.show();

					// Set the new values
					self.setNewValues($select.val(), $sizes, $columns);
				}

				// Update the classes
				self.removeClasses($columnsStage, 'ttfmake-text-layout-');
			});

			$stage.on('change', '.ttfmp-text-column-layout-select', function() {
				var $this = $(this),
					$section = $this.parents('.ttfmake-section'),
					val = $this.val(),
					$columnsStage = $('.ttfmake-text-columns-stage', $section),
					$columns = $('.ttfmake-text-column', $columnsStage),
					$sizes = $('.ttfmp-column-size-input', $columnsStage);

				// Update the columns classes
				self.removeClasses($columnsStage, 'ttfmake-text-layout-');
				$columnsStage.addClass('ttfmake-text-layout-' + val);

				// Set the new values
				self.setNewValues(val, $sizes, $columns);
			});
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
})(jQuery);