/* global jQuery */
var oneApp = oneApp || {}, $oneApp = $oneApp || jQuery(oneApp);

(function($, $oneApp) {
	'use strict';

	var layoutTemplates = {
		init: function() {
			var $messageBox = $('.ttfmp-import-message'),
				$select = $('#ttfmp-import-content', $messageBox),
				$title = $('#title');

			$messageBox.on('click', '#ttfmp-import-link', function(evt){
				evt.preventDefault();

				// Generate the link and submit
				var val = $select.val(),
					url = ttfmpLayoutTemplates.base,
					title = $title.val();

				// Construct the URL
				url += '?ttfmp_template_nonce=' + ttfmpLayoutTemplates.nonce + '&ttfmp_template=' + val + '&ttfmp_post_id=' + ttfmpLayoutTemplates.postID + '&ttfmp_title=' + encodeURIComponent(title);

				// Goto the page
				window.location.href = url;
			});

			$oneApp.on('afterSectionViewAdded', function() {
				$messageBox.addClass('ttfmp-import-message-hide');
			});

			$oneApp.on('afterSectionViewRemoved', function() {
				if ($('.ttfmake-section').length < 1) {
					$messageBox.removeClass('ttfmp-import-message-hide');
				}
			});
		}
	};

	layoutTemplates.init();
})(jQuery, $oneApp);