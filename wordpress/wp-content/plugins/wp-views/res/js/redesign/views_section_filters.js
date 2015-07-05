
var WPViews = WPViews || {};

WPViews.QueryFilters = function( $ ) {
	
	var self = this;
	
	self.view_id = $( '.js-post_ID' ).val();
	
	self.icon_edit = '<i class="icon-chevron-up"></i>&nbsp;&nbsp;';
	self.icon_save = '<i class="icon-ok"></i>&nbsp;&nbsp;';
	
	self.add_filter_button = $( '.js-wpv-filter-add-filter' );
	self.filters_list = $( '.js-filter-list' );
	self.filters_count = self.filters_list.find( '.js-filter-row' ).length;
	self.no_filters = $( '.js-no-filters' );
	
	self.add_filter_select_selector = '.js-filter-add-select';
	self.insert_filter_selector = '.js-filters-insert-filter';
	
	self.selector_delete_simple_filter = '.js-filter-row-simple .js-filter-remove';
	self.selector_delete_multiple_filter_one = '';
	self.selector_delete_multiple_filter_all = '';
	
	self.url_pattern = /^[a-z0-9\-\_]+$/;
	self.shortcode_pattern = /^[a-z0-9]+$/;
	self.year_pattern = /^([0-9]{4})$/;
	self.month_pattern = /^([1-9]|1[0-2])$/;
	self.week_pattern = /^([1-9]|[1234][0-9]|5[0-3])$/;
	self.day_pattern = /^([1-9]|[12][0-9]|3[0-1])$/;
	self.hour_pattern = /^([0-9]|[1][0-9]|2[0-3])$/;
	self.minute_pattern = /^([0-9]|[1234][0-9]|5[0-9])$/;
	self.second_pattern = /^([0-9]|[1234][0-9]|5[0-9])$/;
	self.dayofyear_pattern = /^([1-9]|[1-9][0-9]|[12][0-9][0-9]|3[0-6][0-6])$/;
	self.dayofweek_pattern = /^[1-7]+$/;
	self.numeric_natural_pattern = /^[0-9]+$/;
	
	// ---------------------------------
	// Functions
	// ---------------------------------
	
	self.update_filters_select = function( nonce, openpopup ) {
		var data = {
			action: 'wpv_filters_upate_filters_select',
			id: self.view_id,
			wpnonce: nonce,
		};
		$.post( ajaxurl, data, function( response ) {
			if ( ( typeof( response ) !== 'undefined' ) ) {
				$( '.js-filter-add-select' ).replaceWith( response );
				if ( openpopup ) {
					self.open_filters_popup();
				}
			} else {
				//console.log( "Error: AJAX returned ", response );
			}
		})
		.fail( function( jqXHR, textStatus, errorThrown ) {
			//console.log( "Error: ", textStatus, errorThrown );
		})
		.always( function() {
			$( '.js-wpv-filter-add-filter' ).prop( 'disabled', false );
		});
	};
	
	self.open_filters_popup = function() {
		$.colorbox({
			inline: true,
			href:'.js-filter-add-filter-form-dialog',
			open: true,
			onComplete: function() {
				var group = $(".js-filter-add-select").find("optgroup");
				$.each( group, function( i, v ) {
					if ( $( v ).children().length === 0 ) {
						$( this ).remove();
					}
				});
			}
		});
	};
	
	self.filters_exist = function() {
		self.filters_count = self.filters_list.find( '.js-filter-row' ).length;
		if ( 0 == self.filters_count ) {
			self.filters_list.hide();
			self.no_filters.show();
			self.add_filter_button.val( self.add_filter_button.data( 'empty' ) );
		} else {
			self.filters_list.show();
			self.no_filters.hide();
			self.add_filter_button.val( self.add_filter_button.data( 'nonempty' ) );
		}
	};
	
	self.open_filter_row = function( row ) {
		row.find( '.js-wpv-filter-edit-open' ).hide();
		row.find( '.js-wpv-filter-summary' ).hide();
		row.find( '.js-wpv-filter-edit' ).fadeIn('fast');
		row.find( '.js-wpv-filter-edit-ok' ).show();
		row.addClass( 'wpv-filter-row-current' );
	};
	
	self.first_open_filter_row = function( row ) {
		var thiz_row = $( row ),
		save_text = thiz_row.find( '.js-wpv-filter-edit-ok' ).data( 'save' );
		thiz_row.find( '.js-wpv-filter-edit' ).show();
		thiz_row.find( '.js-wpv-filter-summary, .js-wpv-filter-edit-open' ).hide();
		thiz_row.find( '.js-wpv-filter-edit-ok' )
			.show()
			.html( self.icon_save + save_text )
			.addClass('button-primary js-wpv-section-unsaved')
			.removeClass('button-secundary');
		setConfirmUnload( true );
		thiz_row.addClass( 'wpv-filter-row-current' );
	};
	
	self.close_filter_row = function( row ) { // general close filters editor - just aesthetic changes & no actions
		var thiz_row = $( row );
		thiz_row.find( '.js-wpv-filter-edit, .js-wpv-filter-edit-ok' ).hide();
		thiz_row.find( '.js-wpv-filter-summary, .js-wpv-filter-edit-open' ).show();
		thiz_row.removeClass( 'wpv-filter-row-current' );
	};
	
	self.glow_filter_row = function( row, reason ) {
		$( row ).addClass( reason );
		setTimeout( function () {
			$( row ).removeClass( reason );
		}, 500 );
	};
	
	self.close_and_glow_filter_row = function( row, reason ) {
		self.close_filter_row( row );
		self.glow_filter_row( row, reason );
	};
	
	self.validate_filter_options = function( row ) {
		var valid = true,
		thiz,
		filter_options_values = $( row ).find( '.js-wpv-filter-validate' );
		$( filter_options_values ).each( function() {
			thiz = $( this );
			thiz.removeClass( 'filter-input-error' );
			if ( ! self.validate_filter_options_value( thiz.data( 'type' ), thiz ) ) {
				thiz.addClass( 'filter-input-error' );
				valid = false;
			}
		});

		return valid;
	};
	
	self.validate_filter_options_value = function( type, selector ) {
		var input_valid = true,
		value = selector.val(),
		message = '',
		filter_error_container = selector.closest( '.js-wpv-filter-multiple-element, .js-filter-row' ).find( '.js-wpv-filter-toolset-messages' );
		if ( type == 'select' ) {
			if ( value == '' ) {
				message = wpv_filters_strings.select_empty;
				input_valid = false;
			}
		} else if ( type == 'url' ) {
			if ( value == '' ) {
				message = wpv_filters_strings.param_missing;
				input_valid = false;
			} else if ( self.url_pattern.test( value ) == false ) {
				message = wpv_filters_strings.param_url_ilegal;
				input_valid = false;
			} else if ( $.inArray( value, wpv_forbidden_parameters.wordpress ) > -1 ) {
				message = wpv_filters_strings.param_forbidden_wordpress;
				input_valid = false;
			} else if ( $.inArray( value, wpv_forbidden_parameters.toolset ) > -1 ) {
				message = wpv_filters_strings.param_forbidden_toolset;
				input_valid = false;
			} else if ( $.inArray( value, wpv_forbidden_parameters.post_type ) > -1 ) {
				message = wpv_filters_strings.param_forbidden_post_type;
				input_valid = false;
			} else if ( $.inArray( value, wpv_forbidden_parameters.taxonomy ) > -1 ) {
				message = wpv_filters_strings.param_forbidden_taxonomy;
				input_valid = false;
			}
		} else if ( type == 'shortcode' ) {
			if ( value == '' ) {
				message = wpv_filters_strings.param_missing;
				input_valid = false;
			} else if ( self.shortcode_pattern.test( value ) == false ) {
				message = wpv_filters_strings.param_shortcode_ilegal;
				input_valid = false;
			} else if ( $.inArray( value, wpv_forbidden_parameters.toolset_attr ) > -1 ) {
				message = wpv_filters_strings.param_forbidden_toolset_attr;
				input_valid = false;
			}
		} else if ( type == 'year' ) {
			if ( self.year_pattern.test( value ) == false ) {
				message = wpv_filters_strings.param_year_ilegal;;
				input_valid = false;
			}
		} else if ( type == 'month' ) {
			if ( self.month_pattern.test( value ) == false ) {
				message = wpv_filters_strings.param_month_ilegal;;
				input_valid = false;
			}
		} else if ( type == 'week' ) {
			if ( self.week_pattern.test( value ) == false ) {
				message = wpv_filters_strings.param_week_ilegal;;
				input_valid = false;
			}
		} else if ( type == 'day' ) {
			if ( self.day_pattern.test( value ) == false ) {
				message = wpv_filters_strings.param_day_ilegal;;
				input_valid = false;
			}
		} else if ( type == 'hour' ) {
			if ( self.hour_pattern.test( value ) == false ) {
				message = wpv_filters_strings.param_hour_ilegal;;
				input_valid = false;
			}
		} else if ( type == 'minute' ) {
			if ( self.minute_pattern.test( value ) == false ) {
				message = wpv_filters_strings.param_minute_ilegal;;
				input_valid = false;
			}
		} else if ( type == 'second' ) {
			if ( self.second_pattern.test( value ) == false ) {
				message = wpv_filters_strings.param_second_ilegal;;
				input_valid = false;
			}
		} else if ( type == 'dayofyear' ) {
			if ( self.dayofyear_pattern.test( value ) == false ) {
				message = wpv_filters_strings.param_dayofyear_ilegal;
				input_valid = false;
			}
		} else if ( type == 'dayofweek' ) {
			if ( self.dayofweek_pattern.test( value ) == false ) {
				message = wpv_filters_strings.param_dayofweek_ilegal;;
				input_valid = false;
			}
		} else if ( type == 'numeric_natural' ) {
			if ( self.numeric_natural_pattern.test( value ) == false ) {
				message = wpv_filters_strings.param_numeric_natural_ilegal;
				input_valid = false;
			}
		}
		if ( ! input_valid ) {
			filter_error_container
				.wpvToolsetMessage({
					text: message,
					type: 'error',
					inline: false,
					stay: true
				});
			// Hack to allow more than one error message per filter
			filter_error_container
				.data( 'message-box', null )
				.data( 'has_message', false );
		}
		return input_valid;
	};
	
	self.clear_validate_messages = function( row ) {
		$( row )
			.find('.toolset-alert-error').not( '.js-wpv-permanent-alert-error' )
			.each( function() {
				$( this ).remove();
			});
		$( row )
			.find( '.filter-input-error' )
			.each( function() {
				$( this ).removeClass( 'filter-input-error' );
			});
	};
	
	self.reset_filter_select = function() {
		$( self.add_filter_select_selector ).val( '-1' );
	};
	
	self.manage_filter_insert_button = function( state ) {
		if ( state ) {
			$( self.insert_filter_selector )
				.addClass( 'button-primary' )
				.removeClass( 'button-secondary' )
				.prop( 'disabled', false );
		} else {
			$( self.insert_filter_selector )
				.addClass( 'button-secondary' )
				.removeClass( 'button-primary' )
				.prop( 'disabled', true );
		}
	};
	
	// ---------------------------------
	// Events
	// ---------------------------------
	
	// Adding a filter
	
	$( document ).on( 'click', '.js-filters-cancel-filter', function() {
		self.reset_filter_select();
	});
	
	$( document ).on( 'change', self.add_filter_select_selector, function() {
		self.manage_filter_insert_button( $( this ).val() != '-1' );
	});
	
	$( document ).on( 'click', '.js-wpv-filter-add-filter', function() {
		var thiz = $( this );
		thiz.prop( 'disabled', true );
		self.reset_filter_select();
		$( '.js-filters-insert-filter' )
			.addClass( 'button-secondary' )
			.removeClass( 'button-primary' )
			.prop( 'disabled', true );
		self.update_filters_select( thiz.data( 'nonce' ), true );
	});
	
	$( document ).on( 'click','.js-filters-insert-filter', function() {
		var thiz = $( this ),
		filter_type = $( '.js-filter-add-select' ).val(),
		nonce = thiz.data( 'nonce' ),
		spinnerContainer = $( '<div class="spinner ajax-loader">' ).insertBefore( thiz ).show(),
		data = {
			action: 'wpv_filters_add_filter_row',
			id: self.view_id,
			wpnonce: nonce,
			filter_type: filter_type
		};
		thiz
			.addClass( 'button-secondary' )
			.removeClass( 'button-primary' )
			.prop( 'disabled', true );
		$.post( ajaxurl, data, function( response ) {
			if ( ( typeof( response ) !== 'undefined' ) ) {
				if ( filter_type == 'post_category' || filter_type.substr( 0, 9 ) == 'tax_input' ) {
					if ( $( '.js-wpv-filter-row-taxonomy' ).length > 0 ) {
						var filter_type_fixed = filter_type.replace( '[', '_' ).replace( ']', '' ),
						responseRow = $( '<div></div>' ).append( response ),
						responseUsable = responseRow.find( '.js-wpv-filter-taxonomy-multiple-element' );
						$( '.js-wpv-filter-row-taxonomy .js-wpv-filter-row-tax-' + filter_type_fixed ).remove();
						$('.js-wpv-filter-taxonomy-edit').prepend( responseUsable );
					} else {
						var responseRow = $('.js-filter-list').append( response );
					}
					self.first_open_filter_row( '.js-wpv-filter-row-taxonomy' );
				} else if (filter_type.substr(0, 12) == 'custom-field') {
					if ( $( '.js-wpv-filter-row-custom-field' ).length > 0 ) {
						var responseRow = $( '<div></div>' ).append( response ),
						responseUsable = responseRow.find( '.js-wpv-filter-custom-field-multiple-element' );
						$( '.js-wpv-filter-row-custom-field .js-wpv-filter-row-' + filter_type ).remove();
						$('.js-wpv-filter-custom-field-edit').prepend( responseUsable );
					} else {
						var responseRow = $('.js-filter-list').append( response );
					}
					self.first_open_filter_row( '.js-wpv-filter-row-custom-field' );				
				} else if (filter_type.substr(0, 14) == 'usermeta-field') {
					if ( $( '.js-wpv-filter-row-usermeta-field' ).length > 0 ) {
						var responseRow = $( '<div></div>' ).append( response ),
						responseUsable = responseRow.find( '.js-wpv-filter-usermeta-field-multiple-element' );
						$( '.js-wpv-filter-row-usermeta-field .js-wpv-filter-row-' + filter_type ).remove();
						$('.js-wpv-filter-usermeta-field-edit').prepend( responseUsable );
					} else {
						var responseRow = $('.js-filter-list').append( response );
					}
					self.first_open_filter_row( '.js-wpv-filter-row-usermeta-field' );
				} else if ( filter_type == 'post_date' ) {
					$( '.js-filter-list .js-filter-row-post-date' ).remove();
					var responseRow = $( '.js-filter-list' ).append( response );
					self.first_open_filter_row( '.js-filter-list .js-filter-row-post-date' );
				} else {
					$( '.js-filter-list .js-filter-row-' + filter_type ).remove();
					var responseRow = $( '.js-filter-list' ).append( response );
					self.first_open_filter_row( '.js-filter-list .js-filter-row-' + filter_type );
					//wpv_users_suggest();
				}
			} else {
				console.log( "Error: AJAX returned ", response );
			}
		})
		.fail( function( jqXHR, textStatus, errorThrown ) {
			console.log( "Error: ", textStatus, errorThrown );
		})
		.always( function() {
			spinnerContainer.remove();
			$.colorbox.close();
			self.reset_filter_select();
			$( document ).trigger( 'js_event_wpv_query_filter_created', [ filter_type ] );
			if ( filter_type == 'post_search' ) {
				WPV_parametric_local.add_search.handle_flags();
			}
		});
	});
	
	// Count filters

	$( document ).on( 'js_event_wpv_query_filter_created js_event_wpv_query_filter_saved js_event_wpv_query_filter_deleted', function( event, filter_type ) {
		self.filters_exist();
		self.reset_filter_select();
		if ( $( '.js-wpv-section-unsaved' ).length < 1 ) {
			setConfirmUnload( false );
		}
	});
	
	// Remove simple filter

	$( document ).on( 'click', self.selector_delete_simple_filter, function() {
		var thiz = $( this ),
		row = thiz.closest( 'li.js-filter-row' ),
		filter = row.attr( 'id' ).substring( 7 ),
		nonce = thiz.data( 'nonce' ),
		action = 'wpv_filter_' + filter + '_delete',
		spinnerContainer = $( '<div class="spinner ajax-loader">' ).insertBefore( thiz ).show(),
		error_container = row.find( '.js-wpv-filter-toolset-messages' );
		data = {
			action: action,
			id: self.view_id,
			wpnonce: nonce,
		};
		$.post( ajaxurl, data, function( response ) {
			if ( response.success ) {
				row.find( '.js-wpv-filter-edit-ok' ).removeClass( 'js-wpv-section-unsaved' );
				row
					.addClass( 'wpv-filter-deleted' )
					.fadeOut( 500, function() {
						$( this ).remove();
						$( document ).trigger( 'js_event_wpv_query_filter_deleted', [ filter ] );
						if ( filter == 'post_search' ) {
							WPV_parametric_local.add_search.handle_flags();
						}
					});
				$( '.js-filter-add-select' ).val( '-1' );
				$( '.js-post_ID' ).trigger( 'wpv_trigger_dps_existence_intersection_missing' );
			} else {
				WPViews.view_edit_screen.manage_ajax_fail( response.data, error_container );
			}
		}, 'json' )
		.fail( function( jqXHR, textStatus, errorThrown ) {
			console.log( "Error: ", textStatus, errorThrown );
		})
		.always( function() {
			spinnerContainer.remove();
		});
	});
	
	$( document ).on( 'click', '.js-wpv-filter-edit-open', function() { // open filters editor - common for all filters
		var thiz = $( this ),
		row = thiz.parents( '.js-filter-row' );
		self.open_filter_row( row );
	});
	
	$( document ).on( 'js_event_wpv_query_type_options_saved', '.js-wpv-query-type-update', function() {
		self.filters_exist();
	});
	
	// ---------------------------------
	// Init
	// ---------------------------------
	
	self.init = function() {
		self.filters_exist();
		self.manage_filter_insert_button( false );
	};
	
	self.init();

};

jQuery( document ).ready( function( $ ) {
    WPViews.query_filters = new WPViews.QueryFilters( $ );
});

//----------------------------
// Parametric search and results update settings
//----------------------------

WPViews.ParametricSearchSectionGUI = function( $ ) {
	var self = this; // this is a private variable which scopes the full class to reference the Object in every scope
	
	// Parametric search data
	self.helper_mode_val = $( '.js-wpv-dps-mode-helper:checked' ).val(),
	self.update_mode_val = $( '.js-wpv-dps-ajax-results:checked' ).val(),
	self.update_submit_action_val = $( '.js-wpv-ajax-results-submit:checked' ).val(),
	self.dps_mode_val = $( '.js-wpv-dps-enable:checked' ).val();
	
	$( document ).on( 'change', '.js-wpv-dps-mode-helper', function() {
		self.helper_mode_val = $( '.js-wpv-dps-mode-helper:checked' ).val();
		if ( self.helper_mode_val == 'custom' ) {
			$( '.js-wpv-ps-settings-custom' ).fadeIn();
		} else {
			$( '.js-wpv-ps-settings-custom' ).hide();
			self.wpv_dps_adjust_settings_by_mode( self.helper_mode_val );
		}
	});
	
	$( document ).on( 'change', '.js-wpv-dps-ajax-results', function() {
		self.update_mode_val = $( '.js-wpv-dps-ajax-results:checked' ).val();
		self.wpv_dps_showhide_javascript_settings();
		WPV_parametric_local.add_spinner.handle_flags();
		WPV_parametric_local.add_submit.handle_flags();
		$( '.js-wpv-dps-ajax-results-extra' ).hide();
		if ( self.update_mode_val == 'disable' ) {
			$( '.js-wpv-dps-ajax-results-extra-disable' ).fadeIn();
		}
	});
	
	$( document ).on( 'change', '.js-wpv-ajax-results-submit', function() {
		self.update_submit_action_val = $( '.js-wpv-ajax-results-submit:checked' ).val();
		self.wpv_dps_showhide_javascript_settings();
		WPV_parametric_local.add_spinner.handle_flags();
		WPV_parametric_local.add_submit.handle_flags();
	});
	
	$( document).on( 'change', '.js-wpv-dps-enable', function() {
		self.dps_mode_val = $( '.js-wpv-dps-enable:checked' ).val()
		WPV_parametric_local.add_spinner.handle_flags();
		if ( self.dps_mode_val == 'disable' ) {
			$( '.js-wpv-dps-crossed-details' ).hide();
		} else {
			$( '.js-wpv-dps-crossed-details' ).fadeIn();
		}
	});
	
	$( document ).on( 'click', '.js-make-intersection-filters', function( e ) {
		e.preventDefault();
		var thiz = $( this ),
		spinnerContainer = $( '<div class="spinner ajax-loader">' ).insertBefore( thiz ).show(),
		data = {
			action: 'wpv_filter_make_intersection_filters',
			id: $( '.js-post_ID' ).val(),
			nonce: thiz.data( 'nonce' )
		};
		$.post( ajaxurl, data, function( response ) {
			if ( ( typeof( response ) !== 'undefined' ) ) {
				decoded_response = $.parseJSON( response );
				if ( decoded_response.success === data.id ) {
					$( '.js-filter-list' ).html( decoded_response.wpv_filter_update_filters_list );
					$( '.js-wpv-dps-intersection-fail' ).hide();
					$( '.js-wpv-dps-intersection-ok' ).show();
					$( document ).trigger( 'js_event_wpv_query_filter_saved', [ 'all' ] );
				}
			} else {
				
			}
		})
		.fail(function(jqXHR, textStatus, errorThrown) {
			
		})
		.always(function() {
			spinnerContainer.remove();
		});
	});
	
	self.wpv_dps_adjust_settings_by_mode = function( mode ) {
		if ( mode == 'fullrefreshonsubmit' ) {
			// Only update on submit, reload page on submit, enable dependency
			$( '.js-wpv-dps-ajax-results-disable, .js-wpv-ajax-results-submit-reload, .js-wpv-dps-enable-enable' ).trigger( 'click' );
			// Show only available options for each input - enable dependency
			$( '.js-wpv-dps-empty-select, .js-wpv-dps-empty-multi-select, .js-wpv-dps-empty-radios, .js-wpv-dps-empty-checkboxes' ).prop( 'checked', true );
		} else if ( mode == 'ajaxrefreshonsubmit' ) {
			// Only update on submit, AJAX on submit, enable dependency
			$( '.js-wpv-dps-ajax-results-disable, .js-wpv-ajax-results-submit-ajaxed, .js-wpv-dps-enable-enable' ).trigger( 'click' );
			// Show only available options for each input - enable dependency
			$( '.js-wpv-dps-empty-select, .js-wpv-dps-empty-multi-select, .js-wpv-dps-empty-radios, .js-wpv-dps-empty-checkboxes' ).prop( 'checked', true );
		} else if ( mode == 'ajaxrefreshonchange' ) {
			// Update on change, enable dependency - do not care about submit as it will be hidden
			$( '.js-wpv-dps-ajax-results-enable, .js-wpv-dps-enable-enable' ).trigger( 'click' );
			// Show only available options for each input
			$( '.js-wpv-dps-empty-select, .js-wpv-dps-empty-multi-select, .js-wpv-dps-empty-radios, .js-wpv-dps-empty-checkboxes' ).prop( 'checked', true );
		}
	};
	
	self.wpv_dps_showhide_javascript_settings = function() {
		if ( self.update_mode_val == 'enable' || self.update_submit_action_val == 'ajaxed' ) {
			$( '.js-wpv-ajax-extra-callbacks' ).fadeIn();
		} else {
			$( '.js-wpv-ajax-extra-callbacks' ).hide();
		}
	}

	self.init = function(){ // public method
		
	};
	
	self.init(); // call the init method

};

jQuery( document ).ready( function( $ ) {
    WPViews.parametric_search_section_gui = new WPViews.ParametricSearchSectionGUI( $ );
});

jQuery( document ).on( 'click', '.js-wpv-filter-missing-delete', function(e) {
	e.preventDefault();
	var thiz = jQuery( this ),
	spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertBefore( thiz ).show(),
	missing_cf = [],
	missing_tax = [],
	missing_rel = [],
	missing_search = [],
	thiz_type,
	thiz_name;
	jQuery( '.js-wpv-filter-missing' ).find( 'li' ).each( function() {
		thiz_type = jQuery( this ).data( 'type' );
		this_name = jQuery( this ).data( 'name' );
		if ( thiz_type == 'cf' ) {
			missing_cf.push( this_name );
		} else if ( thiz_type == 'tax' ) {
			missing_tax.push( this_name );
		} else if ( thiz_type == 'rel' ) {
			missing_rel.push( this_name );
		} else if ( thiz_type == 'search' ) {
			missing_search.push( this_name );
		}
	});
	data = {
		action: 'wpv_remove_filter_missing',
		id: jQuery('.js-post_ID').val(),
		cf: missing_cf,
		tax: missing_tax,
		rel: missing_rel,
		search: missing_search,
		wpnonce: thiz.data('nonce')
	};
	jQuery.post( ajaxurl, data, function( response ) {
		if ( response.success ) {
			jQuery( '.js-filter-list' ).html( response.data.updated_filters_list );
			jQuery( document ).trigger( 'js_event_wpv_query_filter_deleted', [ 'all' ] );
			thiz.closest( '.js-wpv-missing-filter-container' ).html( '' ).hide();
			jQuery( '.js-post_ID' ).trigger( 'wpv_trigger_dps_existence_intersection_missing' );
		} else {
			
		}
	}, 'json' )
	.fail(function(jqXHR, textStatus, errorThrown) {
		//if(  WPV_Parametric.debug ) console.log( WPV_Parametric.error_generic, textStatus, errorThrown );
	})
	.always(function() {
		spinnerContainer.remove();
	});
});

jQuery(document).on( 'click', '.js-wpv-filter-missing-close', function(e) {
	e.preventDefault();
	var thiz = jQuery(this);
	thiz.parents( '.js-wpv-missing-filter-container' ).html('').hide();
});

jQuery( document ).on( 'wpv_trigger_dps_existence_intersection_missing', '.js-post_ID', function() {
	wpv_dps_existence_intersection_missing();
});

function wpv_dps_existence_intersection_missing() {
	var mandy = jQuery( '.js-post_ID' ),
	view_id = mandy.val(),
	nonce = mandy.data( 'nonce' ),
	existence_container = jQuery( '.js-wpv-no-filters-container' ),
	intersection_container = jQuery( '.js-wpv-dps-intersection-fail' ),
	intersection_container_ok = jQuery( '.js-wpv-dps-intersection-ok' ),
	missing_container = jQuery( '.js-wpv-missing-filter-container' );
	data = {
		action: 'wpv_get_dps_related',
		id: view_id,
		nonce: nonce
	};
	jQuery.ajax( {
		type: "POST",
		async: false,
		url: ajaxurl,
		data: data,
		success: function( response ) {
			if ( typeof( response ) !== 'undefined' ) {
				decoded_response = jQuery.parseJSON( response );
				if ( decoded_response.existence != '' ) {
					existence_container.html( decoded_response.existence );
					if ( WPViews.view_edit_screen.query_type === 'posts' && WPViews.view_edit_screen.purpose === 'parametric' ) {
						existence_container.fadeIn( 'fast' );
					} else {
						existence_container.hide();
					}
				} else {
					existence_container.hide();
				}
				if ( decoded_response.intersection != '' ) {
					intersection_container.html( decoded_response.intersection );
					if ( WPViews.view_edit_screen.query_type === 'posts' ) {
						intersection_container.fadeIn( 'fast' );
						intersection_container_ok.hide();
					}
				} else {
					intersection_container.hide();
					intersection_container_ok.fadeIn( 'fast' );
				}
				if ( decoded_response.missing != '' ) {
					missing_container.html( decoded_response.missing );
					if ( WPViews.view_edit_screen.query_type === 'posts' ) {
						missing_container.fadeIn( 'fast' );
					}
				} else {
					missing_container.hide();
				}
			} else {
				console.log( "Error: AJAX returned ", response );
			}
		},
		error: function (ajaxContext) {
			console.log( "Error: ", ajaxContext.responseText );
		},
		complete: function() {
			
		}
	});
}

jQuery( document ).on( 'js_event_wpv_screen_options_saved js_event_wpv_query_type_saved', function() {
	wpv_dps_existence_intersection_missing();
});