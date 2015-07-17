(function($){

	FLBuilder.registerModuleHelper('post-grid', {

		rules: {
			posts_per_page: {
				required: true,
				number: true
			},
			offset: {
				required: true,
				number: true
			}
		}
	});

})(jQuery);