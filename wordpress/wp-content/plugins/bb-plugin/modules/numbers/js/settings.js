(function($){

	FLBuilder.registerModuleHelper('numbers', {
		
		init: function()
		{
			var form = $('.fl-builder-settings');
			
			this._toggleMaxNumber();
			
			form.find('select[name=layout]').on('change', this._toggleMaxNumber);
			form.find('select[name=number_type]').on('change', this._toggleMaxNumber);
		},
		
		_toggleMaxNumber: function()
		{
			var form        = $('.fl-builder-settings'),
				layout  	= form.find('select[name=layout]').val(),
				numberType  = form.find('select[name=number_type]').val(),
				maxNumber   = form.find('#fl-field-max_number'); 
			
			if ( 'default' == layout ) {
				maxNumber.hide();
			}
			else if ( 'standard' == numberType ) {
				maxNumber.show();
			}
			else {
				maxNumber.hide();
			}
		}
	});

})(jQuery);