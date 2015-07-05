/**
* Views Taxonomy Filter GUI - script
*
* Adds basic interaction for the Taxonomy Filter
*
* @package Views
*
* @since 1.7.0
*/


var WPViews = WPViews || {};

WPViews.TaxonomyFilterGUI = function( $ ) {
	
	var self = this;
	
	self.view_id = $('.js-post_ID').val();
	
	self.icon_edit = '<i class="icon-chevron-up"></i>&nbsp;&nbsp;';
	self.icon_save = '<i class="icon-ok"></i>&nbsp;&nbsp;';
	self.spinner = '<span class="spinner ajax-loader">';
	
	self.post_row = '.js-wpv-filter-row-taxonomy';
	self.post_options_container_selector = '.js-wpv-filter-row-taxonomy .js-wpv-filter-edit';
	self.post_edit_open_selector = '.js-wpv-filter-taxonomy-edit-open';
	self.post_close_save_selector = '.js-wpv-filter-taxonomy-edit-ok';
	
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
	
	self.manage_taxonomy_mode = function( select ) {
		var single_element = $( select ).closest( '.js-wpv-filter-multiple-element' ),
		mode_value = $( select ).val();
		WPViews.query_filters.clear_validate_messages( self.post_row );
		single_element.find( '.js-taxonomy-checklist, .js-taxonomy-parameter, .js-taxonomy-framework' ).hide();
		if ( mode_value == 'FROM ATTRIBUTE' ) {
			single_element.find('.js-taxonomy-parameter').fadeIn();
			single_element.find('.js-taxonomy-param-label')
				.html(
					single_element
						.find('.js-taxonomy-param-label')
							.data('attribute')
				);
			single_element.find('.js-taxonomy-param').data('type', 'shortcode');
		} else if ( mode_value == 'FROM URL' ) {
			single_element.find('.js-taxonomy-parameter').fadeIn();
			single_element.find('.js-taxonomy-param-label')
				.html(
					single_element
						.find('.js-taxonomy-param-label')
							.data('parameter')
				);
			single_element.find('.js-taxonomy-param').data('type', 'url');
		} else if ( mode_value == 'FROM PAGE' || mode_value == 'FROM PARENT VIEW' ) {
		} else if ( mode_value == 'framework' ) {
			single_element.find( '.js-taxonomy-framework' ).fadeIn();
		} else if (
			mode_value == 'IN'
			|| mode_value == 'NOT IN'
			|| mode_value == 'AND'
		) {
			single_element.find('.js-taxonomy-checklist').fadeIn();
		}
	};
	
	self.remove_taxonomy_filters = function() {
		$( self.post_close_save_selector ).removeClass( 'js-wpv-section-unsaved' );
		var nonce = $( '.js-wpv-filter-remove-taxonomy' ).data( 'nonce' ),
		taxonomy = [],
		spinnerContainer = $( self.spinner ).insertBefore( $( '.js-wpv-filter-remove-taxonomy' ) ).show(),
		error_container = $( self.post_row ).find( '.js-wpv-filter-multiple-toolset-messages' );
		$('.js-wpv-filter-taxonomy-multiple-element .js-filter-remove').each( function() {
			taxonomy.push( $( this ).data( 'taxonomy' ) );
		});
		var data = {
			action: 'wpv_filter_taxonomy_delete',
			id: self.view_id,
			taxonomy: taxonomy,
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
							$( document ).trigger( 'js_event_wpv_query_filter_deleted', [ 'taxonomy' ] );
							self.manage_taxonomy_relationship();
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
	
	self.manage_taxonomy_relationship = function() {
		var items = $( '.js-wpv-taxonomy-relationship' ).length;
		if ( items > 1 ) {
			$( '.js-wpv-filter-taxonomy-relationship' ).show();
		} else if ( items == 0 ) {
			$( '.js-wpv-filter-row-taxonomy' ).remove();
			if ( $( '.js-wpv-section-unsaved' ).length < 1 ) {
				setConfirmUnload( false );
			}
		} else {
			$( '.js-wpv-filter-taxonomy-relationship' ).hide();
		}
	};
	
	//--------------------
	// Events
	//--------------------
	
	$( document ).on( 'change keyup input cut paste', self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select', function() {
		self.manage_filter_changes();
	});
	
	// Close and save
	
	$( document ).on( 'click', self.post_close_save_selector, function() {
		var thiz = $( this );
		WPViews.query_filters.clear_validate_messages( self.post_row );
		if ( self.post_current_options == $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize() ) {
			WPViews.query_filters.close_filter_row('.js-wpv-filter-row-taxonomy');
		} else {
			var valid = true;
			$( '.js-wpv-taxonomy-relationship' ).each( function() {
				var thiz_inner = $( this ),
				this_valid = true,
				tax_row = thiz_inner.parents('.js-wpv-filter-multiple-element').data('taxonomy');
				if ( thiz_inner.val() == 'FROM ATTRIBUTE' || thiz_inner.val() == 'FROM URL' ) {
					this_valid = WPViews.query_filters.validate_filter_options( '.js-wpv-filter-row-taxonomy-' + tax_row );
				} else {
					this_valid = WPViews.query_filters.validate_filter_options_value( 'select', thiz_inner );
					if ( this_valid == false ) {
						thiz_inner.addClass( 'filter-input-error' );
					}
				}
				if ( this_valid == false ) {
					valid = false;
				}
			});
			if ( valid ) {
				self.post_current_options = $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize();
				var nonce = thiz.data( 'nonce' ),
				spinnerContainer = $( self.spinner ).insertBefore( thiz ),
				error_container = $( self.post_row ).find( '.js-wpv-filter-multiple-toolset-messages' ),
				data = {
					action: 'wpv_filter_taxonomy_update',
					id: self.view_id,
					filter_taxonomy: self.post_current_options,
					wpnonce: nonce
				};
				$.post( ajaxurl, data, function( response ) {
					if ( response.success ) {
						$( '.js-post_ID' ).trigger( 'wpv_trigger_dps_existence_intersection_missing' );
						$( document ).trigger( 'js_event_wpv_query_filter_saved', [ 'taxonomy' ] );
						$( '.js-wpv-filter-taxonomy-summary' ).html( response.data.summary );
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
	
	// Delete single items
	
	$( document ).on( 'click', '.js-wpv-filter-taxonomy-multiple-element .js-filter-remove', function() {
		var thiz = $( this ),
		row = thiz.parents( '.js-wpv-filter-taxonomy-multiple-element' ),
		li_item = thiz.parents( self.post_row ),
		taxonomy = thiz.data('taxonomy'),
		nonce = thiz.data('nonce'),
		spinnerContainer = $( self.spinner ).insertBefore( thiz ).hide(),
		error_container = row.find( '.js-wpv-filter-toolset-messages' ),
		data = {
			action: 'wpv_filter_taxonomy_delete',
			id: self.view_id,
			taxonomy: taxonomy,
			wpnonce: nonce,
		};
		if ( li_item.find( '.js-wpv-filter-taxonomy-multiple-element' ).length == 1 ) {
			self.remove_taxonomy_filters();
		} else {
			spinnerContainer.show();
			$.post( ajaxurl, data, function( response ) {
				if ( response.success ) {
					row
						.addClass( 'wpv-filter-multiple-element-removed' )
						.fadeOut( 500, function() {
							$( this ).remove();
							$( document ).trigger( 'js_event_wpv_query_filter_deleted', [ 'taxonomy' ] );
							self.manage_taxonomy_relationship();
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
	
	// Delete all items - open dialog if needed
	
	$( document ).on( 'click', '.js-wpv-filter-remove-taxonomy', function() {
		if ( $( self.post_row ).find( '.js-wpv-filter-taxonomy-multiple-element' ).length > 1 ) {
			$.colorbox({
				inline: true,
				href:'.js-filter-taxonomy-delete-filter-row-dialog',
				open: true
			});
		} else {
			self.remove_taxonomy_filters();
		}
	});
	
	// Delete all items - manage dialog interaction
	
	$( document ).on( 'click', '.js-wpv-filter-taxonomy-edit-filter-row', function() {
		$.colorbox.close();
		WPViews.query_filters.open_filter_row( $( self.post_row ) );
	})

	$( document ).on( 'click', '.js-wpv-filters-taxonomy-delete-filter-row', function() {
		var spinnerContainer = $( self.spinner ).insertBefore( $( this ) ).show();
		self.remove_taxonomy_filters();
		spinnerContainer.remove();
		$.colorbox.close();
	});
	
	$( document ).on( 'change', '.js-wpv-taxonomy-relationship', function() {
		self.manage_taxonomy_mode( this );
	});
	
	$( document ).on( 'js_event_wpv_query_filter_created', function( event, filter_type ) {
		if ( filter_type == 'taxonomy' || filter_type == 'post_category' || filter_type.substr(0, 9) == 'tax_input' || filter_type == 'all' ) {
			self.manage_filter_changes();
			self.manage_taxonomy_relationship();
		}
		if ( filter_type == 'parametric-all' ) {
			self.post_current_options = $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize();
			self.manage_filter_changes();
			self.manage_taxonomy_relationship();
		}
		WPViews.query_filters.filters_exist();
	});
	
	$( document ).on( 'js_event_wpv_query_filter_saved', function( event, filter_type ) {
		if ( filter_type == 'taxonomy' || filter_type == 'post_category' || filter_type.substr(0, 9) == 'tax_input' || filter_type == 'all' ) {
			self.post_current_options = $( self.post_options_container_selector + ' input, ' + self.post_options_container_selector + ' select' ).serialize();
			self.manage_filter_changes();
			self.manage_taxonomy_relationship();
		}
		WPViews.query_filters.filters_exist();
	});
	
	$( document ).on( 'js_event_wpv_query_filter_deleted', function( event, filter_type ) {
		if ( filter_type == 'taxonomy' || filter_type == 'post_category' || filter_type.substr(0, 9) == 'tax_input' || filter_type == 'all' ) {
			self.manage_filter_changes();
			self.manage_taxonomy_relationship();
		}
		WPViews.query_filters.filters_exist();
	});
	
	//--------------------
	// Init
	//--------------------
	
	self.init = function() {
		self.manage_taxonomy_relationship();
	};
	
	self.init();

};

jQuery( document ).ready( function( $ ) {
    WPViews.taxonomy_filter_gui = new WPViews.TaxonomyFilterGUI( $ );
});