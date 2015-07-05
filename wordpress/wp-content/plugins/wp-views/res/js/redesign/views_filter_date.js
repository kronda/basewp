/**
* Views Date Filter GUI - script
*
* Adds basic interaction for the Date Filter
*
* @package Views
*
* @since 1.8.0
*/


var WPViews = WPViews || {};

WPViews.DateFilterGUI = function( $ ) {
	
	var self = this;
	
	self.view_id = $('.js-post_ID').val();
	
	self.icon_edit = '<i class="icon-chevron-up"></i>&nbsp;&nbsp;';
	self.icon_save = '<i class="icon-ok"></i>&nbsp;&nbsp;';
	self.spinner = '<span class="spinner ajax-loader">';
	
	self.post_row = '.js-wpv-filter-row-post-date';
	self.post_options_container_selector = '.js-wpv-filter-row-post-date .js-wpv-filter-edit';
	self.post_edit_open_selector = '.js-wpv-filter-post-date-edit-open';
	self.post_close_save_selector = '.js-wpv-filter-post-date-edit-ok';
	
	self.post_current_options = $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize();
	
	self.condition_template = false;
	
	self.single_operators = [ '=', '!=', '<', '<=', '>', '>=' ];
	self.group_operators = [ 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' ];
	self.group_operators_with_buttons = [ 'IN', 'NOT IN' ];
	self.group_operators_without_buttons = [ 'BETWEEN', 'NOT BETWEEN' ];
	
	//--------------------
	// Functions
	//--------------------
	
	// Track filter changes
	
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
	
	// Manage extra buttons for grouped conditions - remove and add another value buttons
	
	self.date_condition_operator_show_settings = function( condition_row ) {
		var thiz_operator = condition_row.find( '.js-wpv-date-condition-operator' ).val(),
		thiz_single_settings = condition_row.find( '.js-wpv-date-condition-single' ),
		thiz_group_settings = condition_row.find( '.js-wpv-date-condition-group' );
		if ( $.inArray( thiz_operator, self.single_operators ) !== -1 ) {
			thiz_single_settings.fadeIn();
			thiz_group_settings.hide();
		} else if ( $.inArray( thiz_operator, self.group_operators ) !== -1 ) {
			thiz_single_settings.hide();
			thiz_group_settings.fadeIn();
			var thiz_group_buttons_add = condition_row.find( '.js-wpv-date-condition-group-value-add' ),
			thiz_group_buttons_delete = condition_row.find( '.js-wpv-date-condition-group-value-delete' );
			if ( $.inArray( thiz_operator, self.group_operators_with_buttons ) !== -1 ) {
				thiz_group_buttons_add.show();
				thiz_group_buttons_delete.prop( 'disable', false ).show();
				$( thiz_group_buttons_delete[0] ).hide();
			} else {
				thiz_group_buttons_add.hide();
				thiz_group_buttons_delete.hide();
				var cond_group_values = condition_row.find( '.js-wpv-filter-date-condition-group-value' ),
				cond_group_values_length = cond_group_values.length,
				cond_group_values_max = 2;
				if ( cond_group_values_length == 1 ) {
					var clone = $( cond_group_values[0] ).clone();
					clone.find( 'select.js-wpv-filter-date-origin' ).val( 'constant' );
					clone.find( 'input.js-wpv-filter-date-data' ).val( '' ).show();
					clone.insertAfter( cond_group_values[0] );
				} else if ( cond_group_values_length > 2 ) {
					cond_group_values.each( function() {
						if ( cond_group_values_max > 0 ) {
							$( this ).show();
						} else {
							$( this ).remove();
						}
						cond_group_values_max--;
					});
				}
			}
		}
	};
	
	// Remove post date filter
	
	self.remove_post_date_filter = function() {
		$( self.post_close_save_selector ).removeClass( 'js-wpv-section-unsaved' );
		var nonce = $( '.js-wpv-filter-remove-post-date' ).data( 'nonce' ),
		taxonomy = [],
		spinnerContainer = $( self.spinner ).insertBefore( $( '.js-wpv-filter-remove-post-date' ) ).show(),
		error_container = $( self.post_row ).find( '.js-wpv-filter-multiple-toolset-messages' ),
		data = {
			action: 'wpv_filter_post_date_delete',
			id: self.view_id,
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
							$( document ).trigger( 'js_event_wpv_query_filter_deleted', [ 'date' ] );
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
	};
	
	// Manage show/hide the date conditions relationship box
	
	self.manage_date_relationship = function() {
		var items = $( '.js-wpv-filter-post-date-options .js-wpv-date-condition' ).length;
		if ( items > 1 ) {
			$( '.js-wpv-filter-post-date-relationship' ).show();
		} else if ( items == 0 ) {
			$( '.js-wpv-filter-row-post-date' ).remove();
			if ( $( '.js-wpv-section-unsaved' ).length < 1 ) {
				setConfirmUnload( false );
			}
		} else {
			$( '.js-wpv-filter-post-date-relationship' ).hide();
		}
	};
	
	// Resolve date condition values into a hidden field
	
	self.resolve_date_condition_values = function( condition_row ) {
		condition_row.find( '.js-wpv-date-condition-single .js-wpv-filter-date-condition-combo-value' ).each( function() {
			var thiz = $( this ),
			value_holder = thiz.find( 'input.js-wpv-filter-date-data' ),
			real_value_holder = thiz.find( '.js-wpv-filter-date-data-real' ),
			mode_holder = thiz.find( 'select.js-wpv-filter-date-origin' ),
			type = value_holder.data( 'combotype' ),
			value = value_holder.val();
			switch ( mode_holder.val() ) {
				case 'url':
					value = 'URL_PARAM(' + value + ')';
					break;
				case 'attribute':
					value = 'VIEW_PARAM(' + value + ')';
					break;
				case 'current_one':
					value = 'CURRENT_ONE()';
					break;
				case 'future_one':
					value = 'FUTURE_ONE(' + value + ')';
					break;
				case 'past_one':
					value = 'PAST_ONE(' + value + ')';
					break;
			}
			real_value_holder.val( value );
		});
		var group_selected = condition_row.find( '.js-wpv-date-condition-group-selected' ).val(),
		group_resolved_value = '',
		group_real_value_holder = condition_row.find( '.js-wpv-date-condition-group .js-wpv-filter-date-data-real' );
		condition_row.find( '.js-wpv-date-condition-group .js-wpv-filter-date-condition-combo-value' ).each( function() {
			var thiz = $( this ),
			value = thiz.find( 'input.js-wpv-filter-date-data' ).val(),
			mode = thiz.find( 'select.js-wpv-filter-date-origin' ).val();
			switch ( mode ) {
				case 'url':
					value = 'URL_PARAM(' + value + ')';
					break;
				case 'attribute':
					value = 'VIEW_PARAM(' + value + ')';
					break;
				case 'current_one':
					value = 'CURRENT_ONE()';
					break;
				case 'future_one':
					value = 'FUTURE_ONE(' + value + ')';
					break;
				case 'past_one':
					value = 'PAST_ONE(' + value + ')';
					break;
			}
			if ( group_resolved_value != '' ) {
				group_resolved_value += ',';
			}
			group_resolved_value += value;
		});
		group_real_value_holder.val( group_resolved_value );
	};
	
	// Manage validation flags for combo rows
	
	self.manage_combo_validation_flags = function( item ) {
		var thiz_combo = $( item ),
		thiz_combo_select = thiz_combo.find( 'select.js-wpv-filter-date-origin' ),
		thiz_combo_select_val = thiz_combo_select.val(),
		thiz_combo_input = thiz_combo.find( 'input.js-wpv-filter-date-data' ),
		thiz_combo_type = thiz_combo_input.data( 'combotype' );
		if ( thiz_combo_select_val == 'current_one' ) {
			thiz_combo_input.removeClass( 'js-wpv-filter-validate' );
		} else if ( thiz_combo_select_val == 'url' ) {
			thiz_combo_input
				.addClass( 'js-wpv-filter-validate' )
				.data('type', 'url');
		} else if ( thiz_combo_select_val == 'attribute' ) {
			thiz_combo_input
				.addClass( 'js-wpv-filter-validate' )
				.data('type', 'shortcode');
		} else if ( thiz_combo_select_val == 'constant' ) {
			if ( thiz_combo_type != 'group' ) {
				if ( thiz_combo_input.val() == '' ) {
					thiz_combo_input.removeClass( 'js-wpv-filter-validate' );
				} else {
					thiz_combo_input
						.addClass( 'js-wpv-filter-validate' )
						.data('type', thiz_combo_type);
				}
			} else {
				thiz_combo_type = thiz_combo.closest( '.js-wpv-date-condition' ).find( '.js-wpv-date-condition-group-selected' ).val();
				thiz_combo_input
					.addClass( 'js-wpv-filter-validate' )
					.data('type', thiz_combo_type);
			}
		} else {
			thiz_combo_input
				.addClass( 'js-wpv-filter-validate' )
				.data('type', 'numeric_natural');
		}
	};
	
	//--------------------
	// Events
	//--------------------
	
	// Watch changes
	
	$( document ).on( 'change keyup input cut paste', self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select', function() {
		self.manage_filter_changes();
	});
	
	// Add condition
	
	$( document ).on( 'click', '.js-wpv-date-filter-add-condition', function() {
		var thiz = $( this ),
		spinnerContainer = $( '<div class="spinner ajax-loader">' ).insertAfter( thiz ).show(),
		data = {
			action: 'wpv_filter_post_date_add_condition',
			//id: self.view_id,
			//wpnonce: nonce,
		};
		thiz.prop( 'disabled', true );
		if ( self.condition_template ) {
			$( self.condition_template ).insertBefore( thiz );
			thiz.prop( 'disabled', false );
			spinnerContainer.remove();
			self.manage_filter_changes();
			self.manage_date_relationship();
			return;
		}
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( typeof( response ) !== 'undefined' ) {
					self.condition_template = response;
					$( response ).insertBefore( thiz );
					self.manage_filter_changes();
					self.manage_date_relationship();
				} else {
					console.log( "Error: AJAX returned ", response );
				}
			},
			error: function ( ajaxContext ) {
				console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() {
				thiz.prop( 'disabled', false );
				spinnerContainer.remove();
			}
		});
	});
	
	// Change condition operator
	
	$( document ).on( 'change', '.js-wpv-date-condition-operator', function() {
		var thiz = $( this ),
		thiz_condition = thiz.closest( '.js-wpv-date-condition' );
		self.date_condition_operator_show_settings( thiz_condition );
	});
	
	// Change origin select
	
	$( document ).on( 'change', '.js-wpv-filter-date-condition-combo-value select.js-wpv-filter-date-origin', function() {
		var thiz = $( this ),
		thiz_val = thiz.val(),
		thiz_val_kind = thiz.find( ':selected' ).data( 'group' ),
		thiz_combo = thiz.closest( '.js-wpv-filter-date-condition-combo-value' ),
		thiz_input = thiz_combo.find( 'input.js-wpv-filter-date-data' );
		if ( thiz_val == 'current_one' ) {
			thiz_input.hide();
		} else {
			thiz_input.show();
		}
	});
	
	// Add group value
	
	$( document ).on( 'click', '.js-wpv-date-condition-group-value-add', function() {
		var thiz = $( this ),
		thiz_condition_group = thiz.parents( '.js-wpv-date-condition-group' ),
		cond_group_values = thiz_condition_group.find( '.js-wpv-filter-date-condition-group-value' ),
		clone = $( cond_group_values[0] ).clone();
		clone.find( 'select.js-wpv-filter-date-origin' ).val( 'constant' );
		clone.find( 'input.js-wpv-filter-date-data' ).val( '' ).show();
		clone.find( '.js-wpv-date-condition-group-value-delete' ).prop( 'disable', false ).show();
		clone.insertBefore( thiz );
		self.manage_filter_changes();
	});
	
	// Remove group value
	
	$( document ).on( 'click', '.js-wpv-date-condition-group-value-delete', function() {
		var thiz = $( this ),
		thiz_value_div = thiz.parents( '.js-wpv-filter-date-condition-group-value' );
		thiz_value_div.fadeOut( 400, function() {
			$( this ).remove();
			self.manage_filter_changes();
		});
	});
	
	// Save filter data
	
	$( document ).on( 'click', self.post_close_save_selector, function() {
		var thiz = $( this );
		WPViews.query_filters.clear_validate_messages( self.post_row );
		if ( self.post_current_options == $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize() ) {
			WPViews.query_filters.close_filter_row( '.js-wpv-filter-row-post-date' );
		} else {
			var valid = true,
			filter_data = [];
			$( self.post_options_container_selector ).find( '.js-wpv-date-condition' ).each( function() {
				var thiz_condition = $( this ),
				thiz_condition_operator = thiz_condition.find( '.js-wpv-date-condition-operator' ).val(),
				thiz_condition_validating = false,
				thiz_valid = true,
				thiz_condition_kind = 'single';
				if ( $.inArray( thiz_condition_operator, self.single_operators ) !== -1 ) {
					thiz_condition_validating = thiz_condition.find( '.js-wpv-date-condition-single' );
				} else if ( $.inArray( thiz_condition_operator, self.group_operators ) !== -1 ) {
					thiz_condition_validating = thiz_condition.find( '.js-wpv-date-condition-group' );
					thiz_condition_kind = 'group';
				}
				if ( thiz_condition_validating ) {
					thiz_condition_validating.find( '.js-wpv-filter-date-condition-combo-value' ).each( function() {
						self.manage_combo_validation_flags( this );
					});
					thiz_valid = WPViews.query_filters.validate_filter_options( thiz_condition_validating );
				}
				if ( thiz_valid == false ) {
					valid = false;
				} else {
					self.resolve_date_condition_values( thiz_condition );
					var thiz_options = thiz_condition.find( 'select, input' ).not( '.js-wpv-element-not-serialize' ).serialize();
					filter_data.push( thiz_options );
				}
			});
			if ( valid ) {
				self.post_current_options = $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize();
				var nonce = thiz.data( 'nonce' ),
				spinnerContainer = $( self.spinner ).insertBefore( thiz ),
				error_container = $( self.post_row ).find( '.js-wpv-filter-multiple-toolset-messages' ),
				data = {
					action: 'wpv_filter_post_date_update',
					id: self.view_id,
					date_filter: filter_data,
					date_relation: $( '#js-wpv-filter-date-relation' ).val(),
					wpnonce: nonce
				};
				$.post( ajaxurl, data, function( response ) {
					if ( response.success ) {
						$( document ).trigger( 'js_event_wpv_query_filter_saved', [ 'post_date' ] );
						$( '.js-wpv-filter-post-date-summary' ).html( response.data.summary );
						WPViews.query_filters.close_and_glow_filter_row( self.post_row, 'wpv-filter-saved' );
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
			}
		}
	});
	
	// Delete all items - @tofo open dialog if needed
	
	$( document ).on( 'click', '.js-wpv-filter-remove-post-date', function() {
		//if ( $( self.post_row ).find( '.js-wpv-filter-taxonomy-multiple-element' ).length > 1 ) {
		//	$.colorbox({
		//		inline: true,
		//		href:'.js-filter-taxonomy-delete-filter-row-dialog',
		//		open: true
		//	});
		//} else {
			self.remove_post_date_filter();
		//}
	});
	
	// Delete a single date condition
	
	$( document ).on( 'click', '.js-wpv-date-condition-remove', function() {
		if ( $( self.post_row ).find( '.js-wpv-date-condition' ).length > 1 ) {
			var thiz_condition = $( this ).parents( 'div.js-wpv-date-condition' );
			thiz_condition
				.addClass( 'wpv-filter-multiple-element-removed' )
				.animate({
					height: "toggle",
					opacity: "toggle"
				}, 400, function() {
					thiz_condition.remove();
					self.manage_filter_changes();
					self.manage_date_relationship();
				});
		} else {
			self.remove_post_date_filter();
		}
	});
	
	// On filter save
	
	$( document ).on( 'js_event_wpv_query_filter_saved', function( event, filter_type ) {
		if ( filter_type == 'post_date' ) {
			self.post_current_options = $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize();
			self.manage_filter_changes();
		}
		WPViews.query_filters.filters_exist();
	});
	
	
	//--------------------
	// Init
	//--------------------
	
	self.init = function() {
		
	};
	
	self.init();

};

jQuery( document ).ready( function( $ ) {
    WPViews.date_filter_gui = new WPViews.DateFilterGUI( $ );
});