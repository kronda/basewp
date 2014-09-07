
(function($){
	// Toggle 'disabled' attribute of settings based on their overrides
	$('.ttfmp_per-page_override').on('change', function() {
		var checked = $(this).prop('checked'),
			setting = $(this).parent().parent().find('.ttfmp_per-page_setting');

		if (checked) {
			setting.prop('disabled', '');
		} else {
			setting.prop('disabled', 'disabled');
		}
	});

	// Toggle visibility of some settings based on other settings
	$.each({
		'featured-images' : {
			callback : function(v) { return ('post-header' === v); }
		},
		'post-date' : {
			callback : function(v) { return ('none' !== v); }
		},
		'post-author' : {
			callback : function(v) { return ('none' !== v); }
		},
		'comment-count' : {
			callback : function(v) { return ('none' !== v); }
		}
	}, function(id, op) {
		$('#ttfmp_per-page_settings\\[' + id + '\\]').not(':hidden').on('change', function() {
			var selection = $(this).val(),
				dependents = $('.' + id + '-dependent'),
				match = op.callback(selection);

			if (true === match) {
				dependents.show();
			} else {
				dependents.hide();
			}
		}).trigger('change');
	});
}(jQuery));