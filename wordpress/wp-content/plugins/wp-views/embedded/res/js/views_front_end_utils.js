(function($){
	$(function(){
		manage_checkboxes_and_multi();
	});
	
	function WPV_ManageDefaultValueForCheckBoxes( container )
	{
		var self = this;
		self.element = null;
		self.container = container;
		self.others = null;
			
		self.init = function()
		{
			self.element = self.element_is_place_holder();
			
			if( !self.element ){
				
				return false;
			} 
			
			self.others = self.getOthers();
			
			self.manage_clicks();
		};
		
		self.getOthers = function()
		{
			var all = self.container.find("input.wpcf-form-checkbox"), others = [];
			
			$.each(all, function( i, v ){
				if( i > 0 )
				{
					others.push( v );
				}
			});
			
			return others;
		};
		
		self.getCheckedLen = function()
		{
			var count = 0;
			
			$.each(self.others, function( i, v ){
				if( $(v).prop('checked') == true )
				{
					count++;
				}
			});
			return count;
		};
		
		self.element_is_place_holder = function()
		{
			var check_cont = self.container.children('.wpcf-form-item-checkbox')[0], check = $(check_cont).find('input');
			
			if( $(check).val() ) return false; 
			
			return $(check);
		};
		
		self.manage_clicks = function()
		{
			$.each(self.others, function( i, v ){
				$(v).on('click', function(event) {					
					if( $(event.target).prop('checked') ){
						self.element.prop( 'checked', false );
					}
					else if( self.container.find( "input:checked" ).length === 0 )
					{
						self.element.prop( 'checked', true );
					}
				});
			});	
			
			self.element.on('click', function(event){
				if( $(event.target).prop('checked') )
				{
					$.each(self.others, function( i, v ){
						$(v).prop( 'checked', false );
					});
				}
			});
			
		};
		
		self.init();
	};
	
	function WPV_ManageDefaultValueForMultiple( select )
	{
		var self = this;
		self.element = null;
		self.select = select;
		self.others = null;
		
		self.init = function()
		{
			self.element = self.element_is_place_holder();

			if( !self.element ){
				if( !self.is_query() )
				{
					$( self.select.find('option')[0] ).prop("selected", false);
				}
				return false;
			} 

			self.others = self.getOthers();

			self.manage_clicks();
		};

		self.getOthers = function()
		{
			var all = self.select.find("option"), others = [];

			$.each(all, function( i, v ){
				if( i > 0 )
				{
					others.push( v );
				}
			});

			return others;
		};

		self.getCheckedLen = function()
		{
			var count = 0;

			$.each(self.others, function( i, v ){
				if( $(v).prop('selected') == true )
				{
					count++;
				}
			});
			return count;
		};

		self.element_is_place_holder = function()
		{
			var opt = self.select.find('option');
			
			if( $( opt[0] ).val() ) return false; 

			return $( opt[0] );
		};

		self.manage_clicks = function()
		{
			$.each(self.others, function( i, v ){
				$(v).on('click', function(event) {					
					if( $(event.target).prop('selected') ) {
						self.element.prop( 'selected', false );
					}
					else if( self.select.find('option:selected').length === 0 )
					{
						self.element.prop( 'selected', true );
					}
				});
			});	

			self.element.on('click', function(event){
				if( $(event.target).prop('selected') )
				{
					$.each(self.others, function( i, v ){
						$(v).prop( 'selected', false );
					});
				}
			});

		};
		
		self.is_query = function()
		{
			var sel_name = self.select.prop("name").split('[]')[0], bool = false, qs = location.search;
			return ~qs.indexOf(sel_name) ? true : false;
		};

		self.init();
	};
	
	function manage_checkboxes_and_multi()
	{
		var checkboxes_groups = [], multiselects = [];
		//Do not execute if there's not a view form, and at least 2 check boxes
		if( 
			$('.wpv-filter-form').is('form') && 
			$('.wpcf-form-checkbox').is('input') && 
			$('.wpv-filter-form').find('input.wpcf-form-checkbox').length > 1 )
		{
			$('div.wpcf-checboxes-group').each(function(i, element){
				checkboxes_groups.push( new WPV_ManageDefaultValueForCheckBoxes( $(element) ) );
			});
			
		}
		if( $('.wpv-filter-form').is('form') && $('select.wpcf-form-select') && $('select.wpcf-form-select').prop('multiple') )
		{
			$('select.wpcf-form-select').each(function( i, element ) {
				multiselects.push( new WPV_ManageDefaultValueForMultiple( $(element)  ) );
			});
		}
	}
	
	
})(jQuery)
