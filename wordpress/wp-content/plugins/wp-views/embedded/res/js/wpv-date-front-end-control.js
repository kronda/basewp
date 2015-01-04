jQuery(document).ready(function() {
	try {
		jQuery(".wpv-date-front-end").datepicker({
			onSelect: function(dateText, inst) {
				var control = this,
				url_param = jQuery(this).data('param');
				data = 'date=' + dateText;
				data += '&date-format=' + jQuery('.js-wpv-date-param-' + url_param + '-format').val();
				data += '&action=wpv_format_date';
				jQuery.post(front_ajaxurl, data, function(response) {
					response = jQuery.parseJSON(response);
					jQuery('.js-wpv-date-param-' + url_param + '-value').val(response['timestamp']);
					jQuery('.js-wpv-date-param-' + url_param).html(response['display']);
				});
			},
			dateFormat : 'ddmmyy',
			showOn: "button",
			buttonImage: wpv_calendar_image,
			buttonText: wpv_calendar_text,
			buttonImageOnly: true,
			changeMonth: true,
			changeYear: true
		});
	}
	catch (e) {
		
	}
	
	// TODO move this style to a unique frontend CSS file for Views
	jQuery("div.ui-datepicker").css('font-size', '12px');
	
	jQuery(document).on('click', '.js-wpv-date-display', function(){
		var url_param = jQuery(this).data('param');
		jQuery('.js-wpv-date-front-end-' + url_param).datepicker('show');
	});
	
});