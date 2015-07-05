/**
* Views Widgets GUI - script
*
* Adds basic interaction for setting the Views Form widget
*
* @package Views
*
* @since 1.7.0
*/


var WPViews = WPViews || {};

WPViews.WidgetsGUI = function( $ ) {
	
	var self = this;
	self.widget_container = '';
	self.widget_title = '';
	self.widget_button = '';
	self.view_id = '';
	self.target_id = '';
	self.errorbox = '';
	self.targetbox = '';
	self.editlink = '';
	
	
	//--------------------
	// Functions
	//--------------------
	
	// Set the current widget data given the .widget selector
	
	self.set_form_widget_data = function( container, callback ) {
        self.widget_container = container;
		self.widget_title = container.find( '.widget-title h4');
		self.widget_button = container.find( '.widget-control-save' );
		self.view_id = container.find( '.js-wpv-view-form-id' );
		self.target_id = container.find( '.js-wpv-widget-form-target-id' );
		self.errorbox = container.find( '.js-wpv-incomplete-setup-box' );
		self.targetbox = container.find( '.js-wpv-check-target-setup-box' );
		self.editlink = container.find( '.js-wpv-check-target-setup-link' );
		
        if ( typeof callback == "function" ) {
			callback();
		}
    };
	
	// Check incomplete Views Filter widgets
	
	self.check_incomplete_view_forms = function() {
		$('#widgets-right [id*=_wp_views_filter]').each( function() {
			var thiz_button = $( this ).find( '.widget-control-save' ),
			thiz_target_id = $( this ).find( '.js-wpv-widget-form-target-id' ),
			thiz_widget_title = $( this ).find( '.widget-title h4'),
			thiz_errorbox = $( this ).find( '.js-wpv-incomplete-setup-box' );
			thiz_widget_title.find( '.icon-exclamation-sign' ).remove();
			if ( thiz_target_id.val() == '' || thiz_target_id.val() == '0' ) {
				thiz_button
					.addClass( 'button-secondary' )
					.removeClass( 'button-primary' )
					.prop( 'disabled', true );
				thiz_widget_title
					.append( " <i class='icon-exclamation-sign' style='color:red;margin-top:-2px;font-size:0.85em'></i>" );
				thiz_errorbox
					.fadeIn();
			}
		});
	};
	
	// Initialize suggest on the already existing Views Filter widgets
	
	self.initialize_suggest = function() {
		$('#widgets-right .js-wpv-widget-form-target-suggest-title:not(.js-wpv-suggest-on)').suggest( ajaxurl + '?action=wpv_suggest_form_targets', {
			onSelect: function() {
				var t_value = this.value,
				t_container = $( this ).closest( '.widget' ),
				t_split_point = t_value.lastIndexOf(' ['),
				t_title = t_value.substr( 0, t_split_point ),
				t_extra = t_value.substr( t_split_point ).split('#'),
				t_id = t_extra[1].replace(']', ''),
				thiz_target_id = t_container.find( '.js-wpv-widget-form-target-id' ),
				thiz_button = t_container.find( '.widget-control-save' ),
				thiz_errorbox = t_container.find( '.js-wpv-incomplete-setup-box' ),
				thiz_targetbox = t_container.find( '.js-wpv-check-target-setup-box' ),
				thiz_editlink_url = t_container.find( '.js-wpv-check-target-setup-link' ).data( 'editurl' ),
				thiz_editlink_view = t_container.find( '.js-wpv-view-form-id' ).val();
				
				t_container.find( '.icon-exclamation-sign' ).remove();
				t_container.find( '.js-wpv-widget-form-target-suggest-title' ).val( t_title );
				thiz_target_id.val( t_id );
				thiz_errorbox
					.hide();
				thiz_targetbox
					.fadeIn()
					.find('.js-wpv-check-target-setup-link')
							.attr( 'href', thiz_editlink_url + t_id + '&action=edit&completeview=' + thiz_editlink_view + '&origid=widget' );
				
			}
		});
		$('#widgets-right .js-wpv-widget-form-target-suggest-title:not(.js-wpv-suggest-on)').addClass( 'js-wpv-suggest-on' );
	};
	
	//--------------------
	// Events
	//--------------------
	
	// Reload init things after an AJAX event
	// Check incomplete Views Filter widgets
	// Initialize suggest on newly added Views Filter widgets
	
	$( document ).ajaxStop( function() {
		self.check_incomplete_view_forms();
        self.initialize_suggest();
    });
	
	// Manage changes to the title input:
	// - clean the target ID value
	// - dsable save button
	// - add hint to widget title
	// - manage target and error boxes
	
	$( document ).on( 'change input cut paste', '#widgets-right .js-wpv-widget-form-target-suggest-title', function ( e ) {
		var container = $( this ).closest( '.widget' );
		self.set_form_widget_data( container, function() {
			container.find( '.icon-exclamation-sign' ).remove();
			self.target_id.val( '' );
			self.widget_button
				.addClass( 'button-secondary' )
				.removeClass( 'button-primary' )
				.prop( 'disabled', true );
			self.widget_title
				.append( " <i class='icon-exclamation-sign' style='color:red;margin-top:-2px;font-size:0.85em'></i>" );
			self.targetbox
				.hide();
			self.errorbox
				.fadeIn();
		});	
	});
	
	// Manage save button and target/error boxes when changing the used View
	
	$( document ).on( 'change', '#widgets-right  .js-wpv-view-form-id', function() {
		var container = $( this ).closest( '.widget' );
		self.set_form_widget_data( container, function() {
			if ( self.target_id.val() != '' ) {
				self.widget_button
						.addClass( 'button-secondary' )
						.removeClass( 'button-primary' )
						.prop( 'disabled', true );
				self.targetbox
					.fadeIn()
					.find('.js-wpv-check-target-setup-link')
						.attr( 'href', self.editlink.data( 'editurl' ) + self.target_id.val() + '&action=edit&completeview=' + self.view_id.val() + '&origid=widget' );
			}
		});
	});
	
	// Click on discard inside target box
	
	$( document ).on( 'click', '#widgets-right .js-wpv-discard-target-setup-link', function( e ) {
		e.preventDefault();
		var container = $( this ).closest( '.widget' );
		self.set_form_widget_data( container, function() {
			self.targetbox
				.hide();
			self.widget_button
				.addClass( 'button-primary' )
				.removeClass( 'button-secondary' )
				.prop( 'disabled', false );
		});
	});
	
	// Click on complete inside target box
	
	$( document ).on( 'click', '#widgets-right .js-wpv-check-target-setup-link', function() {
		var container = $( this ).closest( '.widget' );
		self.set_form_widget_data( container, function() {
			self.targetbox
				.hide();
			self.widget_button
				.addClass( 'button-primary' )
				.removeClass( 'button-secondary' )
				.prop( 'disabled', false );
		});
	});
	
	// Change the name of a dummy hidden input inside the widget to be sure the Customizer will display an "Apply" button instead of automatically saving
	
	$( document ).on( 'change input cut paste', '#widgets-right [id*=_wp_views_filter] input, #widgets-right [id*=_wp_views_filter] select', function() {
		var container = $( this ).closest( '.widget' ),
		content = container.find( '.widget-content' ),
		helper = content.find( '.js-wpv-target-customizer-helper' ),
		randomnumber = Math.floor( Math.random() * 11 );
		helper.attr( 'name', helper.attr( 'name' ) + randomnumber );
	});
	
	//--------------------
	// Init
	//--------------------
	
	self.init = function() {
		self.check_incomplete_view_forms();
		self.initialize_suggest();
	};
	
	self.init();

};

jQuery( document ).ready( function( $ ) {
    WPViews.widgets_gui = new WPViews.WidgetsGUI( $ );
});

//-------------------------------------
// Compatibility with Layouts Widget cell
//-------------------------------------

WPViews.LayoutsWidgetsGUI = function( $ ) {
	
	var self = this;
	self.container_selector = '.ddl-form.widget-cell';
	self.widget_container = '';
	self.widget_button = '';
	self.view_id = '';
	self.target_id = '';
	self.errorbox = '';
	self.targetbox = '';
	self.editlink = '';
	
	
	//--------------------
	// Functions
	//--------------------
	
	// Set the current widget data given the .ddl-form.widget-cell selector
	
	self.set_form_widget_data = function( container, callback ) {
        self.widget_container = container;
		self.widget_button = container.closest( '.ddl-dialog' ).find( '.js-dialog-footer .js-dialog-edit-save' );
		self.view_id = container.find( '.js-wpv-view-form-id' );
		self.target_id = container.find( '.js-wpv-widget-form-target-id' );
		self.errorbox = container.find( '.js-wpv-incomplete-setup-box' );
		self.targetbox = container.find( '.js-wpv-check-target-setup-box' );
		self.editlink = container.find( '.js-wpv-check-target-setup-link' );
		
        if ( typeof callback == "function" ) {
			callback();
		}
    };
	
	// Initialize suggest on the already existing Views Filter widgets
	
	self.initialize_suggest = function() {
		$( self.container_selector + ' .js-wpv-widget-form-target-suggest-title:not(.js-wpv-suggest-on)' ).suggest( ajaxurl + '?action=wpv_suggest_form_targets', {
			onSelect: function() {
				var t_value = this.value,
				t_container = $( this ).closest( self.container_selector ),
				t_split_point = t_value.lastIndexOf(' ['),
				t_title = t_value.substr( 0, t_split_point ),
				t_extra = t_value.substr( t_split_point ).split('#'),
				t_id = t_extra[1].replace(']', ''),
				thiz_target_id = t_container.find( '.js-wpv-widget-form-target-id' ),
				thiz_button = t_container.closest( '.ddl-dialog' ).find( '.js-dialog-footer .js-dialog-edit-save' ),
				thiz_errorbox = t_container.find( '.js-wpv-incomplete-setup-box' ),
				thiz_targetbox = t_container.find( '.js-wpv-check-target-setup-box' ),
				thiz_editlink_url = t_container.find( '.js-wpv-check-target-setup-link' ).data( 'editurl' ),
				thiz_editlink_view = t_container.find( '.js-wpv-view-form-id' ).val();
				
				t_container.find( '.js-wpv-widget-form-target-suggest-title' ).val( t_title );
				thiz_target_id.val( t_id );
				thiz_errorbox
					.hide();
				thiz_targetbox
					.fadeIn()
					.find('.js-wpv-check-target-setup-link')
							.attr( 'href', thiz_editlink_url + t_id + '&action=edit&completeview=' + thiz_editlink_view + '&origid=widget' );
				
			}
		});
		$( self.container_selector + ' .js-wpv-widget-form-target-suggest-title:not(.js-wpv-suggest-on)' ).addClass( 'js-wpv-suggest-on' );
	};
	
	//--------------------
	// Events
	//--------------------
	
	// Manage changes to the title input:
	// - clean the target ID value
	// - dsable save button
	// - manage target and error boxes
	
	$( document ).on( 'change input cut paste', self.container_selector + ' .js-wpv-widget-form-target-suggest-title', function ( e ) {
		var container = $( this ).closest( self.container_selector );
		self.set_form_widget_data( container, function() {
			self.target_id.val( '' );
			self.widget_button.each( function() {
				$( this )
					.addClass( 'button-secondary' )
					.removeClass( 'button-primary' )
					.prop( 'disabled', true );
			});
			self.targetbox
				.hide();
			self.errorbox
				.fadeIn();
		});	
	});
	
	// Manage save button and target/error boxes when changing the used View
	
	$( document ).on( 'change', self.container_selector + '  .js-wpv-view-form-id', function() {
		var container = $( this ).closest( self.container_selector );
		self.set_form_widget_data( container, function() {
			if ( self.target_id.val() != '' ) {
				self.widget_button.each( function() {
					$( this )
						.addClass( 'button-secondary' )
						.removeClass( 'button-primary' )
						.prop( 'disabled', true );
				});
				self.targetbox
					.fadeIn()
					.find('.js-wpv-check-target-setup-link')
						.attr( 'href', self.editlink.data( 'editurl' ) + self.target_id.val() + '&action=edit&completeview=' + self.view_id.val() + '&origid=widget' );
			}
		});
	});
	
	// Click on discard inside target box
	
	$( document ).on( 'click', self.container_selector + ' .js-wpv-discard-target-setup-link', function( e ) {
		e.preventDefault();
		var container = $( this ).closest( self.container_selector );
		self.set_form_widget_data( container, function() {
			self.targetbox
				.hide();
			self.widget_button.each( function() {
				$( this )
					.addClass( 'button-primary' )
					.removeClass( 'button-secondary' )
					.prop( 'disabled', false );
			});
		});
	});
	
	// Click on complete inside target box
	
	$( document ).on( 'click', self.container_selector + ' .js-wpv-check-target-setup-link', function() {
		var container = $( this ).closest( self.container_selector );
		self.set_form_widget_data( container, function() {
			self.targetbox
				.hide();
			self.widget_button.each( function() {
				$( this )
					.addClass( 'button-primary' )
					.removeClass( 'button-secondary' )
					.prop( 'disabled', false );
			});
		});
	});
	
	// Manage selected widget changes
	
	$( document ).on( 'change keyup input cut paste', self.container_selector + ' .js-wpv-widget-form-target-suggest-title:not(.js-wpv-suggest-on)', function() {
		self.initialize_suggest();
	});
	
	$( document ).on( 'js_event_ddl_widget_cell_widget_type_changed', 'select[name="ddl-layout-widget_type"]', function( event, widget_id ) {
		var container = $( this ).closest( self.container_selector );
		self.set_form_widget_data( container, function() {
			if ( widget_id == 'widget_wp_views_filter' && (container.find('.js-wpv-view-form-id').val() === '' || container.find('.js-wpv-widget-form-target-id').val() === '') ) {
				self.initialize_suggest();
				self.widget_button.each( function() {
					$( this )
						.addClass( 'button-secondary' )
						.removeClass( 'button-primary' )
						.prop( 'disabled', true );
				});
			} else {
				self.widget_button.each( function() {
					$( this )
						.addClass( 'button-primary' )
						.removeClass( 'button-secondary' )
						.prop( 'disabled', false );
				});
			}
		});
		
		
		
	});
	
	//--------------------
	// Init
	//--------------------
	
	self.init = function() {
		self.initialize_suggest();
	};
	
	self.init();

};

jQuery( document ).ready( function( $ ) {
    WPViews.layouts_widgets_gui = new WPViews.LayoutsWidgetsGUI( $ );
});