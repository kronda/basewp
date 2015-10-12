/**
* Views Users Filter GUI - script
*
* Adds basic interaction for the users Filter
*
* @package Views
*
* @since 1.7.0
*/


var WPViews = WPViews || {};

WPViews.UsersFilterGUI = function( $ ) {
	
	var self = this;
	
	self.view_id = $('.js-post_ID').val();
	
	self.icon_edit = '<i class="icon-chevron-up"></i>&nbsp;&nbsp;';
	self.icon_save = '<i class="icon-ok"></i>&nbsp;&nbsp;';
	self.spinner = '<span class="wpv-spinner ajax-loader"></span>&nbsp;&nbsp;';
	
	self.user_row = '.js-wpv-filter-row-users';
	self.user_options_container_selector = '.js-wpv-filter-users-options';
	self.user_summary_container_selector = '.js-wpv-filter-users-summary';
	self.user_edit_open_selector = '.js-wpv-filter-users-edit-open';
	self.user_close_save_selector = '.js-wpv-filter-users-edit-ok';
	
	self.user_current_options = $( self.user_options_container_selector + ' input, ' + self.user_options_container_selector + ' select' ).serialize();
	
	//--------------------
	// Functions for users
	//--------------------
	
	// @todo make this use select2
	
	self.wpv_users_suggest = function() {
		var text_noresult = $('.js-wpv-user-suggest-values').data('noresult'),
		text_hint = $('.js-wpv-user-suggest-values').data('hinttext'),
		text_search = $('.js-wpv-user-suggest-values').data('search'),
		users = $('.js-wpv-user-suggest-values').data('users');
		$(".js-users-suggest-id").tokenInput(ajaxurl + '?action=wpv_suggest_users&view_id='+self.view_id, {
			theme: "wpv",
			preventDuplicates: true,
			hintText: text_hint,
			noResultsText: text_noresult,
			searchingText: text_search,
			prePopulate: users,
			onAdd: function (item) {
				var tokens = $(this).tokenInput('get');
				var user_val = '';
				$.each(tokens, function (index, value) {
					user_val += value.name+', ';
				});
				user_val = user_val.substr(0,(user_val.length - 2));
				$('.js-users-suggest').val(user_val);

			 },
			 onDelete: function (item) {
				var tokens = $(this).tokenInput('get');
				var user_val = '';
				$.each(tokens, function (index, value) {
					user_val += value.name+', ';
				});
				user_val = user_val.substr(0,(user_val.length - 2));
				$('.js-users-suggest').val(user_val);
			 }
		});
	}
	
	//--------------------
	// Events for users
	//--------------------
	
	// Open the edit box and rebuild the current values; show the close/save button-primary
	// TODO maybe the show() could go to the general file
	
	$( document ).on( 'click', self.user_edit_open_selector, function() {
		self.post_current_options = $( self.user_options_container_selector + ' input, ' + self.user_options_container_selector + ' select' ).serialize();
		$( self.user_close_save_selector ).show();
		$( self.user_row ).addClass( 'wpv-filter-row-current' );
	});
	
	// Track changes in options
	
	$( document ).on( 'change keyup input cut paste', self.user_options_container_selector + ' input, ' + self.user_options_container_selector + ' select', function() {
		$( this ).removeClass( 'filter-input-error' );
		$( self.user_close_save_selector ).prop( 'disabled', false );
		WPViews.query_filters.clear_validate_messages( self.user_row );
		if ( self.user_current_options != $( self.user_options_container_selector + ' input, ' + self.user_options_container_selector + ' select' ).serialize() ) {
			$( self.user_close_save_selector )
				.addClass('button-primary js-wpv-section-unsaved')
				.removeClass('button-secondary')
				.html(
					self.icon_save + $( self.user_close_save_selector ).data('save')
				);
			setConfirmUnload( true );
		} else {
			$( self.user_close_save_selector )
				.addClass('button-secondary')
				.removeClass('button-primary js-wpv-section-unsaved')
				.html(
					self.icon_edit + $( self.user_close_save_selector ).data('close')
				);
			$( self.user_close_save_selector )
				.parent()
					.find( '.unsaved' )
					.remove();
			if ( $( '.js-wpv-section-unsaved' ).length < 1 ) {
				setConfirmUnload( false );
			}
		}
	});
	
	// Save filter options
	
	$( document ).on( 'click', self.user_close_save_selector, function() {
		var thiz = $( this );
		WPViews.query_filters.clear_validate_messages( self.user_row );
		if ( self.user_current_options == $( self.user_options_container_selector + ' input, ' + self.user_options_container_selector + ' select' ).serialize() ) {
			WPViews.query_filters.close_filter_row( self.user_row );
			thiz.hide();
		} else {
			var valid = WPViews.query_filters.validate_filter_options( '.js-filter-users' );
			if ( valid ) {
				// update_message = thiz.data('success');
				// unsaved_message = thiz.data('unsaved');
				var action = thiz.data( 'saveaction' ),
				nonce = thiz.data('nonce'),
				spinnerContainer = $( self.spinner ).insertBefore( thiz ).show(),
				error_container = thiz
					.closest( '.js-filter-row' )
						.find( '.js-wpv-filter-toolset-messages' );
				self.user_current_options = $( self.user_options_container_selector + ' input, ' + self.user_options_container_selector + ' select' ).serialize();
				var data = {
					action: action,
					id: self.view_id,
					filter_options: self.user_current_options,
					wpnonce: nonce
				};
				$.post( ajaxurl, data, function( response ) {
					if ( response.success ) {
						$( self.user_close_save_selector )
							.addClass('button-secondary')
							.removeClass('button-primary js-wpv-section-unsaved')
							.html( 
								self.icon_edit + $( self.user_close_save_selector ).data( 'close' )
							);
						if ( $( '.js-wpv-section-unsaved' ).length < 1 ) {
							setConfirmUnload( false );
						}
						$( self.user_summary_container_selector ).html( response.data.summary );
						WPViews.query_filters.close_and_glow_filter_row( self.user_row, 'wpv-filter-saved' );
					} else {
						WPViews.view_edit_screen.manage_ajax_fail( response.data, error_container );
					}
				}, 'json' )
				.fail( function( jqXHR, textStatus, errorThrown ) {
					console.log( "Error: ", textStatus, errorThrown );
				})
				.always( function() {
					spinnerContainer.remove();
					thiz.hide();
				});
			}
		}
	});
	
	// Remove ID filter
	
	$( document ).on( 'click', self.user_row + ' .js-wpv-filter-remove', function() {
		self.user_current_options = '';
	});
	
	// Initialize suggest if needed
	
	$( document ).on( 'click', self.user_edit_open_selector, function() {
		if ( typeof( $( '.token-input-list-wpv' ).html() ) === 'undefined' ) {
			self.wpv_users_suggest();
		}

	});
	
	// Content selection section saved event
	
	$( document ).on( 'js_event_wpv_query_filter_created', function( event, filter_type ) {
		self.wpv_users_suggest();
	});
	
	//--------------------
	// Init
	//--------------------
	
	self.init = function() {
		self.wpv_users_suggest();
	};
	
	self.init();

};

jQuery( document ).ready( function( $ ) {
    WPViews.users_filter_gui = new WPViews.UsersFilterGUI( $ );
});