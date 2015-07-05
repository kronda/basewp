/**
* Views Usermeta Field Filter GUI - script
*
* Adds basic interaction for the Usermeta Field Filter
*
* @package Views
*
* @since 1.7.0
*/


var WPViews = WPViews || {};

WPViews.UsermetaFieldFilterGUI = function( $ ) {
	
	var self = this;
	
	self.view_id = $('.js-post_ID').val();
	
	self.icon_edit = '<i class="icon-chevron-up"></i>&nbsp;&nbsp;';
	self.icon_save = '<i class="icon-ok"></i>&nbsp;&nbsp;';
	self.spinner = '<span class="spinner ajax-loader">';
	
	self.post_row = '.js-wpv-filter-row-usermeta-field';
	self.post_options_container_selector = '.js-wpv-filter-row-usermeta-field .js-wpv-filter-edit';
	self.post_edit_open_selector = '.js-wpv-filter-usermeta-field-edit-open';
	self.post_close_save_selector = '.js-wpv-filter-usermeta-field-edit-ok';
	
	self.post_current_options = $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize();
	
	//--------------------
	// Functions
	//--------------------
	
	self.manage_filter_changes = function() {
		WPViews.query_filters.clear_validate_messages( self.post_row );
		if ( self.post_current_options != $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize() ) {
			$( self.post_close_save_selector )
				.removeClass( 'button-secondary' )
				.addClass( 'button-primary js-wpv-section-unsaved')
				.html(
					self.icon_save + $( self.post_close_save_selector ).data('save')
				);
			setConfirmUnload( true );
		} else {
			$( self.post_close_save_selector )
			.addClass( 'button-secondary' )
			.removeClass( 'button-primary js-wpv-section-unsaved' )
			.html(
				self.icon_edit + $( self.post_close_save_selector ).data('close')
			);
			$( self.post_row  ).find('.unsaved').remove();
			if ( $( '.js-wpv-section-unsaved' ).length < 1 ) {
				setConfirmUnload( false );
			}
		}
	};
	
	self.usermeta_field_initialize_compare = function() {
		var wpv_allowed_values = 0;
		WPViews.query_filters.clear_validate_messages( self.post_row );
		$( '.js-wpv-usermeta-field-compare-select' ).each( function() {
			var wpv_single_row = $( this ).parents( '.js-wpv-filter-multiple-element' ),
			thiz_inner = $( this ),
			thiz_inner_item = thiz_inner.parents( '.js-wpv-filter-multiple-element' );
			if ( thiz_inner.val() == 'BETWEEN' || thiz_inner.val() == 'NOT BETWEEN' ) {
				wpv_allowed_values = 2;
				thiz_inner_item
					.find( '.js-wpv-usermeta-field-add-value, .js-wpv-usermeta-field-remove-value' )
					.hide();
				divs = thiz_inner_item.find('.js-wpv-usermeta-field-value-div');
				if ( divs.length < 2 ) {
					// add another one.
					var clone = $( divs[0] ).clone();
					clone.find( '.js-wpv-usermeta-field-value-text' ).val('');
					clone.insertAfter( divs[0] );
					self.usermeta_field_initialize_compare_mode();
				}
			} else if ( thiz_inner.val() == 'IN' || thiz_inner.val() == 'NOT IN' ) {
				wpv_allowed_values = 100000;
				thiz_inner_item
					.find( '.js-wpv-usermeta-field-add-value' )
					.show();
				thiz_inner_item
					.find( '.js-wpv-usermeta-field-value-div' )
						.each( function( index ) {
							if ( index > 0 ) {
								$( this )
									.find( '.js-wpv-usermeta-field-remove-value' )
									.show();
							} else {
								$( this )
									.find( '.js-wpv-usermeta-field-remove-value' )
									.hide();
							}
						});
			} else {
				wpv_allowed_values = 1;
				thiz_inner_item
					.find( '.js-wpv-usermeta-field-add-value, .js-wpv-usermeta-field-remove-value' )
					.hide();
			}
			thiz_inner_item
				.find( '.js-wpv-usermeta-field-value-div' )
					.each( function() {
						if ( wpv_allowed_values > 0 ) {
							$( this ).show();
						} else {
							$( this ).remove();
						}
						wpv_allowed_values--;
					});
		});
	};
	
	self.usermeta_field_initialize_compare_mode = function() {
		$( '.js-wpv-usermeta-field-compare-mode' ).each( function() {
			self.usermeta_field_adjust_value_controls( this );
		});
	};
	
	self.usermeta_field_adjust_value_controls = function( item ) {
		// Show the text control depending on the compare function.
		var mode = $( item ).val(),
		value_div = $( item ).parents( '.js-wpv-usermeta-field-value-div' ),
		value_input = value_div.find('.js-wpv-usermeta-field-value-text');
		value_input
			.removeClass( 'js-wpv-filter-validate' )
			.data('type', 'none');
		value_div
			.find( '.js-wpv-usermeta-field-value-combo-input, .js-wpv-usermeta-field-value-combo-date, .js-wpv-usermeta-field-value-combo-framework' )
				.hide();
		switch( mode ) {
			case 'constant':
			case 'future_day':
			case 'past_day':
			case 'future_month':
			case 'past_month':
			case 'future_year':
			case 'past_year':
			case 'seconds_from_now':
			case 'months_from_now':
			case 'years_from_now':
				value_div
					.find( '.js-wpv-usermeta-field-value-combo-input' )
						.show();
				break;
			case 'url':
				value_div
					.find( '.js-wpv-usermeta-field-value-combo-input' )
						.show();
				value_input
					.addClass( 'js-wpv-filter-validate' )
					.data('type', 'url');
				break;
			case 'attribute':
				value_div
					.find( '.js-wpv-usermeta-field-value-combo-input' )
						.show();
				value_input
					.addClass( 'js-wpv-filter-validate' )
					.data('type', 'shortcode');
				break;
			case 'date':
				value_div
					.find( '.js-wpv-usermeta-field-value-combo-date' )
						.show();
				break;
			case 'framework':
				value_div
					.find( '.js-wpv-usermeta-field-value-combo-framework' )
						.show();
				break;
			default:
				
				break;
		}
	};
	
	self.usermeta_field_initialize_relationship = function() {
		if ( $( '.js-wpv-usermeta-field-compare-select' ).length > 1 ) {
			$( '.js-wpv-filter-usermeta-field-relationship-container' ).show();
		} else if ( $( '.js-wpv-usermeta-field-compare-select' ).length == 0 ) {
			$( '.js-filter-usermeta-field' ).remove();
			if ( $( '.js-wpv-section-unsaved' ).length < 1 ) {
				setConfirmUnload( false );
			}
		} else {
			$( '.js-wpv-filter-usermeta-field-relationship-container' ).hide();
		}
	};
	
	self.resolve_usermeta_field_value = function() {
		$( '.js-wpv-usermeta-field-values' ).each( function( index ) {
			var text_box = $( this ).find( '.js-wpv-usermeta-field-values-real' ),
			resolved_value = '';
			$( this ).find( '.js-wpv-usermeta-field-value-div' ).each( function( index ) {
				if ( resolved_value != '' ) {
					resolved_value += ',';
				}
				var value = $( this ).find( '.js-wpv-usermeta-field-value-text' ).val(),
				framework_value = $( this ).find( '.js-wpv-usermeta-field-framework-value' ).val(),
				mode = $( this ).find( '.js-wpv-usermeta-field-compare-mode' ).val();
				switch ( mode ) {
					case 'url':
						value = 'URL_PARAM(' + value + ')';
						break;
					case 'attribute':
						value = 'VIEW_PARAM(' + value + ')';
						break;
					case 'framework':
						value = 'FRAME_KEY(' + framework_value + ')';
						break;
					case 'now':
						value = 'NOW()';
						break;
					case 'today':
						value = 'TODAY()';
						break;
					case 'future_day':
						value = 'FUTURE_DAY(' + value + ')';
						break;
					case 'past_day':
						value = 'PAST_DAY(' + value + ')';
						break;
					case 'this_month':
						value = 'THIS_MONTH()';
						break;
					case 'future_month':
						value = 'FUTURE_MONTH(' + value + ')';
						break;
					case 'past_month':
						value = 'PAST_MONTH(' + value + ')';
						break;
					case 'this_year':
						value = 'THIS_YEAR()';
						break;
					case 'future_year':
						value = 'FUTURE_YEAR(' + value + ')';
						break;
					case 'past_year':
						value = 'PAST_YEAR(' + value + ')';
						break;
					case 'seconds_from_now':
						value = 'SECONDS_FROM_NOW(' + value + ')';
						break;
					case 'months_from_now':
						value = 'MONTHS_FROM_NOW(' + value + ')';
						break;
					case 'years_from_now':
						value = 'YEARS_FROM_NOW(' + value + ')';
						break;
					case 'date':
						var month = $( this ).find( '.js-wpv-usermeta-field-date select' ),
						mm = month.val(),
						jj = month.next().val(),
						aa = month.next().next().val();
						value = 'DATE(' + jj + ',' + mm + ',' + aa + ')';
						break;
				}
				resolved_value += value;
			})
			text_box.val( resolved_value );
		});
	};
	
	self.remove_usermeta_field_filters = function() {
		$( self.post_close_save_selector ).removeClass( 'js-wpv-section-unsaved' );
		var nonce = $( '.js-wpv-filter-remove-usermeta-field' ).data( 'nonce' ),
		usermeta_field = [],
		spinnerContainer = $( self.spinner ).insertBefore( $( '.js-wpv-filter-remove-usermeta-field' ) ).show(),
		error_container = $( self.post_row ).find( '.js-wpv-filter-multiple-toolset-messages' );
		$('.js-wpv-filter-usermeta-field-multiple-element .js-filter-remove').each( function() {
			usermeta_field.push( $( this ).data( 'field' ) );
		});
		var data = {
			action: 'wpv_filter_usermeta_field_delete',
			id: self.view_id,
			field: usermeta_field,
			wpnonce: nonce,
		};
		$.ajax({
			type: "POST",
			dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( response.success ) {
					$( self.post_row )
						.addClass( 'wpv-filter-deleted' )
						.animate({
						  height: "toggle",
						  opacity: "toggle"
						}, 400, function() {
							$( this ).remove();
							self.post_current_options = $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize();
							$( document ).trigger( 'js_event_wpv_query_filter_deleted', [ 'usermeta-field' ] );
							self.usermeta_field_initialize_relationship();
						});
				} else {
					WPViews.view_edit_screen.manage_ajax_fail( response.data, error_container );
				}
			 },
			 error: function ( ajaxContext ) {
				 console.log( "Error: ", ajaxContext.responseText );
			 },
			 complete: function() {
				 spinnerContainer.remove();
			 }
		});
		$( '.js-post_ID' ).trigger( 'wpv_trigger_dps_existence_intersection_missing' );
	};
	
	//--------------------
	// Events
	//--------------------
	
	$( document ).on( 'change', '.js-wpv-usermeta-field-compare-select', function() {
		self.usermeta_field_initialize_compare();
	});
	
	$( document ).on( 'change', '.js-wpv-usermeta-field-compare-mode', function() {
		self.usermeta_field_adjust_value_controls( this )
	});
	
	// Add another value

	$( document ).on( 'click', '.js-wpv-usermeta-field-add-value', function() {
		var thiz_parent_item = $( this ).parents( '.js-wpv-filter-multiple-element' ),
		clone = thiz_parent_item
			.find( '.js-wpv-usermeta-field-value-div:last' )
				.clone();
		clone
			.find( '.js-wpv-usermeta-field-value-text' )
				.val('');
		clone
			.find( '.js-wpv-usermeta-field-compare-mode' )
				.val( 'constant' );
		clone
			.find( '.js-wpv-usermeta-field-remove-value' )
				.show();
		clone
			.insertAfter(
				thiz_parent_item
					.find( '.js-wpv-usermeta-field-value-div:last' )
			);
		self.usermeta_field_initialize_compare();
		self.usermeta_field_initialize_compare_mode();
		self.manage_filter_changes();
	});

	// Remove value

	$( document ).on( 'click', '.js-wpv-usermeta-field-remove-value', function() {
		$( this )
			.parents( '.js-wpv-usermeta-field-value-div' )
				.remove();
		self.manage_filter_changes();
	});
	
	// Watch changes
	
	$( document ).on( 'change keyup input cut paste', self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select', function() {
		self.manage_filter_changes();
	});
	
	$( document ).on( 'click', self.post_close_save_selector, function() {
		WPViews.query_filters.clear_validate_messages( self.post_row );
		if ( self.post_current_options == $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize() ) {
			WPViews.query_filters.close_filter_row('.js-filter-usermeta-field');
		} else {
			var valid = true;
			valid = WPViews.query_filters.validate_filter_options( self.post_row );
			if ( valid ) {
				self.resolve_usermeta_field_value();
				var nonce = $( this ).data('nonce'),				
				spinnerContainer = $( self.spinner ).insertBefore( $( this ) ).show(),
				error_container = $( self.post_row ).find( '.js-wpv-filter-multiple-toolset-messages' ),
				data = {
					action: 'wpv_filter_usermeta_field_update',
					id: self.view_id,
					filter_usermeta_fields: $('.js-filter-usermeta-field input, .js-filter-usermeta-field select').not( '.js-wpv-element-not-serialize' ).serialize(),
					wpnonce: nonce
				};
				self.post_current_options = $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize();
				$.post( ajaxurl, data, function( response ) {
					if ( response.success ) {
						$( '.js-post_ID' ).trigger( 'wpv_trigger_dps_existence_intersection_missing' );
						$( document ).trigger( 'js_event_wpv_query_filter_saved', [ 'usermeta-field' ] );
						$( '.js-wpv-filter-usermeta-field-summary' ).html( response.data.summary );
						WPViews.query_filters.close_and_glow_filter_row( self.post_row, 'wpv-filter-saved' );
					} else {
						WPViews.view_edit_screen.manage_ajax_fail( response.data, error_container );
					}
				}, 'json' )
				.fail(function(jqXHR, textStatus, errorThrown) {
					console.log( "Error: %s %s", textStatus, errorThrown );
				})
				.always(function() {
					spinnerContainer.remove();
				});
			}
		}
	});
	
	$( document ).on( 'click', '.js-wpv-filter-usermeta-field-multiple-element .js-filter-remove', function() {
		var thiz = $( this ),
		row = thiz.parents('.js-wpv-filter-usermeta-field-multiple-element'),
		li_item = thiz.parents( self.post_row ),
		field = thiz.data('field'),
		nonce = thiz.data('nonce'),
		spinnerContainer = $('<div class="spinner ajax-loader">').insertBefore( thiz ).hide(),
		error_container = row.find( '.js-wpv-filter-toolset-messages' ),
		data = {
			action: 'wpv_filter_usermeta_field_delete',
			id: self.view_id,
			field: field,
			wpnonce: nonce,
		};
		if ( li_item.find( '.js-wpv-filter-usermeta-field-multiple-element' ).length == 1 ) {
			self.remove_usermeta_field_filters();
		} else {
			spinnerContainer.show();
			$.post( ajaxurl, data, function( response ) {
				if ( response.success ) {
					row
						.addClass( 'wpv-filter-multiple-element-removed' )
						.fadeOut( 500, function() {
							$( this ).remove();
							$( document ).trigger( 'js_event_wpv_query_filter_deleted', [ 'usermeta-field' ] );
							self.usermeta_field_initialize_relationship();
						});
					$( '.js-post_ID' ).trigger( 'wpv_trigger_dps_existence_intersection_missing' );
				} else {
					WPViews.view_edit_screen.manage_ajax_fail( response.data, error_container );
				}
			}, 'json' )
			.fail( function( jqXHR, textStatus, errorThrown ) {
				console.log( "Error: ", textStatus, errorThrown );
			})
			.always( function() {
				
			});
		}
	});
	
	$( document ).on('click', '.js-filter-usermeta-field .js-wpv-filter-remove-usermeta-field', function(e) {
		if ( $( self.post_row ).find( '.js-wpv-filter-usermeta-field-multiple-element' ).length > 1 ) {
			$.colorbox({
				inline: true,
				href:'.js-filter-usermeta-field-delete-filter-row-dialog',
				open: true
			});
		} else {
			self.remove_usermeta_field_filters();
		}
	});
	
	$( document ).on( 'click', '.js-filter-usermeta-field-edit-filter-row', function() {
		$.colorbox.close();
		WPViews.query_filters.open_filter_row( $( self.post_row ) );
	})

	$( document ).on( 'click', '.js-filters-usermeta-field-delete-filter-row', function() {
		var spinnerContainer = $( self.spinner ).insertBefore( $( this ) ).show();
		self.remove_usermeta_field_filters();
		spinnerContainer.remove();
		$.colorbox.close();
	});
	
	// Created, saved and deleted
	
	$( document ).on( 'js_event_wpv_query_filter_created', function( event, filter_type ) {
		if ( filter_type == 'usermeta-field' || filter_type.substr( 0, 14 ) == 'usermeta-field' || filter_type == 'all' ) {
			self.manage_filter_changes();
			self.usermeta_field_initialize_compare();
			self.usermeta_field_initialize_compare_mode();// Might not be needed here
			self.usermeta_field_initialize_relationship();
		}
		WPViews.query_filters.filters_exist();
	});
	
	$( document ).on( 'js_event_wpv_query_filter_saved', function( event, filter_type ) {
		if ( filter_type == 'usermeta-field' || filter_type.substr( 0, 14 ) == 'usermeta-field' || filter_type == 'all' ) {
			self.post_current_options = $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize();
			self.manage_filter_changes();
			self.usermeta_field_initialize_compare();
			self.usermeta_field_initialize_compare_mode();// Might not be needed here
			self.usermeta_field_initialize_relationship();
		}
		WPViews.query_filters.filters_exist();
	});
	
	$( document ).on( 'js_event_wpv_query_filter_deleted', function( event, filter_type ) {
		if ( filter_type == 'usermeta-field' || filter_type.substr( 0, 14 ) == 'usermeta-field' || filter_type == 'all' ) {
			self.manage_filter_changes();
			self.usermeta_field_initialize_compare();
			self.usermeta_field_initialize_compare_mode();// Might not be needed here
			self.usermeta_field_initialize_relationship();
		}
		WPViews.query_filters.filters_exist();
	});
	
	self.init = function() {
		self.usermeta_field_initialize_compare();
		//self.usermeta_field_initialize_compare_mode();
		self.usermeta_field_initialize_relationship();
	};
	
	self.init();

};

jQuery( document ).ready( function( $ ) {
    WPViews.usermeta_field_filter_gui = new WPViews.UsermetaFieldFilterGUI( $ );
});