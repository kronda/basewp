/**
* views_shortcode_gui.js
*
* Contains helper functions for the popup GUI used to set Views shortcode attributes
*
* @since 1.7
* @package Views
*/

var WPViews = WPViews || {};

WPViews.ShortcodesGUI = function( $ ) {
	var self = this;
	
	// Parametric search
	self.ps_view_id = 0;
	self.ps_orig_id = 0;
	
	self.dialog_insert_view = null;
	self.dialog_insert_shortcode = null;
    self.dialog_insert_views_conditional = null;
	
	self.suggest_cache = {};
	self.shortcode_gui_insert = true;
	self.shortcode_gui_insert_count = 0;
    
    self.views_conditional_qtags_opened = false;
	
	self.post_field_section = false;
	
	self.views_conditional_use_gui = true;
		
	self.numeric_natural_pattern = /^[0-9]+$/;
	self.numeric_natural_list_pattern = /^\d+(?:,\d+)*$/;
	self.numeric_natural_extended_pattern = /^(-1|[0-9]+)$/;
	self.year_pattern = /^([0-9]{4})$/;
	self.month_pattern = /^([1-9]|1[0-2])$/;
	self.week_pattern = /^([1-9]|[1234][0-9]|5[0-3])$/;
	self.day_pattern = /^([1-9]|[12][0-9]|3[0-1])$/;
	self.hour_pattern = /^([0-9]|[1][0-9]|2[0-3])$/;
	self.minute_pattern = /^([0-9]|[1234][0-9]|5[0-9])$/;
	self.second_pattern = /^([0-9]|[1234][0-9]|5[0-9])$/;
	self.dayofyear_pattern = /^([1-9]|[1-9][0-9]|[12][0-9][0-9]|3[0-6][0-6])$/;
	self.dayofweek_pattern = /^[1-7]+$/;
	self.url_patern = /^(https?):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i;
	
	/**
     * Temporary dialog content to be displayed while the actual content is loading.
     *
     * It contains a simple spinner in the centre. I decided to implement styling directly, it will not be reused and
     * it would only bloat views-admin.css (jan).
     *
     * @type {HTMLElement}
     * @since 1.9
     */
    self.shortcodeDialogSpinnerContent = $(
        '<div style="min-height: 150px;">' +
            '<div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; ">' +
                '<div class="wpv-spinner ajax-loader"></div>' +
                '<p>' + wpv_shortcodes_gui_texts.loading_options + '</p>' +
            '</div>' +
        '</div>'
    );

	self.init = function() {
		// Initialize dialogs
		if ( ! $('#js-wpv-shortcode-gui-dialog-container').length ) {
			$( 'body' ).append( '<div id="js-wpv-shortcode-gui-dialog-container" class="toolset-shortcode-gui-dialog-container wpv-shortcode-gui-dialog-container js-wpv-shortcode-gui-dialog-container"></div>' );
			self.dialog_insert_shortcode = $( "#js-wpv-shortcode-gui-dialog-container" ).dialog({
				autoOpen: false,
				modal: true,
				minWidth: 450,
				show: { 
					effect: "blind", 
					duration: 800 
				},
				open: function( event, ui ) {
					$( 'body' ).addClass( 'modal-open' );
					$( '.js-wpv-shortcode-gui-insert' )
						.addClass( 'button-secondary' )
						.removeClass( 'button-primary ui-button-disabled ui-state-disabled' )
						.prop( 'disabled', true );
				},
				close: function( event, ui ) {                    
					$( 'body' ).removeClass( 'modal-open' );
				},
				buttons:[
					{
						class: 'button-secondary js-wpv-shortcode-gui-close',
						text: wpv_shortcodes_gui_texts.wpv_close,
						click: function() {
                            $( this ).dialog( "close" );
						}
					},
					{
						class: 'button-secondary js-wpv-shortcode-gui-insert',
						text: wpv_shortcodes_gui_texts.wpv_insert_shortcode,
						disabled: 'disabled',
						click: function() {
							self.wpv_insert_shortcode();
						}
					}
				]
			});
			
			$( 'body' ).append( '<div id="js-wpv-view-shortcode-gui-dialog-container" class="toolset-shortcode-gui-dialog-container wpv-shortcode-gui-dialog-container js-wpv-shortcode-gui-dialog-container"></div>' );
			self.dialog_insert_view = $( "#js-wpv-view-shortcode-gui-dialog-container" ).dialog({
				autoOpen: false,
				modal: true,
				minWidth: 450,
				show: { 
					effect: "blind", 
					duration: 800 
				},
				open: function( event, ui ) {
					$( 'body' ).addClass( 'modal-open' );
					$( '.js-wpv-insert-view-form-action' )
						.addClass( 'button-secondary' )
						.removeClass( 'button-primary ui-button-disabled ui-state-disabled' )
						.prop( 'disabled', true );
					$( '.js-wpv-insert-view-form-action .ui-button-text' ).html( wpv_shortcodes_gui_texts.wpv_insert_view_shortcode );
				},
				close: function( event, ui ) {
					$( 'body' ).removeClass( 'modal-open' );
				},
				buttons:[
					{
						class: 'button-secondary',
						text: wpv_shortcodes_gui_texts.wpv_close,
						click: function() {
							$( this ).dialog( "close" );
						}
					},
					{
						class: 'button-secondary js-wpv-insert-view-form-prev',
						text: wpv_shortcodes_gui_texts.wpv_previous,
						click: function() {
							self.wpv_insert_view_dialog_prev();
						}
					},
					{
						class: 'button-secondary js-wpv-insert-view-form-action',
						text: wpv_shortcodes_gui_texts.wpv_insert_view_shortcode,
						disabled: 'disabled',
						click: function() {
							self.wpv_insert_view_shortcode_to_editor();
						}
					}
				]
			});
            
            $( 'body' ).append( '<div id="js-wpv-views-conditional-shortcode-gui-dialog-container" class="toolset-shortcode-gui-dialog-container wpv-shortcode-gui-dialog-container js-wpv-shortcode-gui-dialog-container"></div>' );
            self.dialog_insert_views_conditional = $( "#js-wpv-views-conditional-shortcode-gui-dialog-container" ).dialog({
				autoOpen: false,
				modal: true,
				minWidth: 450,
				show: { 
					effect: "blind", 
					duration: 800 
				},
				open: function( event, ui ) {
					$( 'body' ).addClass( 'modal-open' );
					$( ".ui-dialog-titlebar-close" ).hide();
					self.views_conditional_use_gui = true;
					$( '.js-wpv-shortcode-gui-insert' )
						.addClass( 'button-secondary' )
						.removeClass( 'button-primary ui-button-disabled ui-state-disabled' )
						.prop( 'disabled', true );
				},
				close: function( event, ui ) {
                    if (  !self.views_conditional_qtags_opened && typeof self.wpv_conditional_object.ed.openTags !== 'undefined' ){
                        var ed = self.wpv_conditional_object.ed, ret = false, i = 0;
                        self.views_conditional_qtags_opened = false;
						while ( i < ed.openTags.length ) {
							ret = ed.openTags[i] == self.wpv_conditional_object.t.id ? i : false;
							i ++;
						}
						ed.openTags.splice(ret, 1);
						self.wpv_conditional_object.e.value = self.wpv_conditional_object.t.display;
                    }
					$( 'body' ).removeClass( 'modal-open' );
				},
				buttons:[
					{
						class: 'button-secondary js-wpv-shortcode-gui-close',
						text: wpv_shortcodes_gui_texts.wpv_close,
						click: function() {
                            // remove wpv-conditional from QTags:opened tags
							self.wpv_conditional_close = false;
							self.views_conditional_qtags_opened = false;
                            if ( !self.views_conditional_qtags_opened && typeof self.wpv_conditional_object.openTags !== 'undefined' ) {
								var ed = self.wpv_conditional_object.ed, ret = false, i = 0;
								while ( i < ed.openTags.length ) {
									ret = ed.openTags[i] == self.wpv_conditional_object.t.id ? i : false;
									i ++;
								}
								ed.openTags.splice(ret, 1);
								self.wpv_conditional_object.e.value = self.wpv_conditional_object.t.display;
                            }
							$( this ).dialog( "close" );
						}
					},
					{
						class: 'button-secondary js-wpv-shortcode-gui-insert',
						text: wpv_shortcodes_gui_texts.wpv_insert_shortcode,
						disabled: 'disabled',
						click: function() {
							self.wpv_insert_view_conditional_shortcode();
						}
					}
				]
			});
		}
	};
	
	//-----------------------------------------
	// Parametric search
	//-----------------------------------------
	
	self.wpv_insert_view_shortcode_dialog = function( view_id, view_title, orig_id, nonce ) {
		self.ps_view_id = view_id;
		self.ps_orig_id = orig_id;
		
		//
        // Build AJAX url for displaying the dialog
        //
        var url = ajaxurl + '?';
		
        url += '_wpnonce=' + nonce;
        url += '&action=wpv_view_form_popup';
        url += '&view_id=' + view_id;
		url += '&orig_id=' + orig_id;
		url += '&view_title=' + view_title;

        //
        // Calculate height
        //
        var dialog_height = $(window).height() - 100;
		
		// Show the "empty" dialog with a spinner while loading dialog content
        self.dialog_insert_view.dialog('open').dialog({
            title: view_title,
            minWidth: 650,
            maxHeight: dialog_height,
            draggable: false,
            resizable: false,
			position: { my: "center top+50", at: "center top", of: window }
        });

		$( '.js-wpv-insert-view-form-prev' ).hide();
        self.dialog_insert_view.html( self.shortcodeDialogSpinnerContent );
		
		//
        // Do AJAX call
        //
        $.ajax({
            url: url,
            success: function( data ) {
                self.dialog_insert_view.html( data );
				$( '.js-wpv-insert-view-form-action' )
					.addClass( 'button-primary' )
					.removeClass( 'button-secondary' )
					.prop( 'disabled', false );
            }
        });
	};
	
	self.wpv_get_view_override_values = function() {
		var override_container = $( '#js-wpv-insert-view-override-container' ),
		override_values = '';
		if ( $( '.js-wpv-insert-view-shortcode-limit', override_container ).val() != '' ) {
			override_values += ' limit="' + $( '.js-wpv-insert-view-shortcode-limit', override_container ).val() + '"';
		}
		if ( $( '.js-wpv-insert-view-shortcode-offset', override_container ).val() != '' ) {
			override_values += ' offset="' + $( '.js-wpv-insert-view-shortcode-offset', override_container ).val() + '"';
		}
		if ( $( '.js-wpv-insert-view-shortcode-orderby', override_container ).val() != '' ) {
			override_values += ' orderby="' + $( '.js-wpv-insert-view-shortcode-orderby', override_container ).val() + '"';
		}
		if ( $( '.js-wpv-insert-view-shortcode-order', override_container ).val() != '' ) {
			override_values += ' order="' + $( '.js-wpv-insert-view-shortcode-order', override_container ).val() + '"';
		}
		return override_values;
	};
	
	self.wpv_get_view_extra_values = function() {
		var extra_container = $( '#js-wpv-insert-view-extra-attributes-container' ),
		extra_values = '';
		if ( extra_container.length > 0 ) {
			$( '.js-wpv-insert-view-shortcode-extra-attribute', extra_container ).each( function() {
				var thiz = $( this );
				if ( thiz.val() != '' ) {
					extra_values += ' ' + thiz.data( 'attribute' ) + '="' + thiz.val() + '"';
				}
			});
		}
		return extra_values;
	};
	
	self.wpv_get_view_cache_values = function() {
		var cache_container = $( '#js-wpv-insert-view-cache-attributes-container' ),
		cache_values = '';
		if ( cache_container.length > 0 ) {
			var use_cache = $( '.js-wpv-insert-view-shortcode-cache:checked', cache_container ).val();
			if ( 'off' == use_cache ) {
				cache_values = ' cached="off"';
			}
		}
		return cache_values;
	};
	
	self.wpv_insert_view_shortcode_to_editor = function() {
		var form_name = $( '#js-wpv-view-shortcode-gui-dialog-view-title' ).val(),
		override_values = self.wpv_get_view_override_values(),
		extra_values = self.wpv_get_view_extra_values(),
		cache_values = self.wpv_get_view_cache_values(),
		valid = self.validate_shortcode_attributes( $( '#js-wpv-view-shortcode-gui-dialog-container' ), $( '#js-wpv-view-shortcode-gui-dialog-container' ), $( '#js-wpv-view-shortcode-gui-dialog-container' ).find( '.js-wpv-filter-toolset-messages' ) );
		
		if ( ! valid ) {
			return;
		}
		
		if ( $( '#js-wpv-insert-view-parametric-search-container' ).length > 0 ) {
			
			var display = $( '.js-wpv-insert-view-form-display:checked' ).val(),
			target = $( '.js-wpv-insert-view-form-target:checked' ).val(),
			set_target = $( '.js-wpv-insert-view-form-target-set:checked' ).val(),
			set_target_id = $( '.js-wpv-insert-view-form-target-set-existing-id' ).val(),
			results_helper_container = $( '.js-wpv-insert-form-workflow-help-box' ),
			results_helper_container_after = $( '.js-wpv-insert-form-workflow-help-box-after' );
			
			if ( display == 'both' ) {
				window.icl_editor.insert('[wpv-view name="' + form_name + '"' + override_values + extra_values + cache_values + ']');
				if ( 
					results_helper_container.length > 0 
					&& results_helper_container.hasClass( 'js-wpv-insert-form-workflow-help-box-for-' + self.ps_view_id ) 
				) {
					results_helper_container.fadeOut( 'fast' );
				}
				if ( 
					results_helper_container_after.length > 0 
					&& results_helper_container_after.hasClass( 'js-wpv-insert-form-workflow-help-box-for-after-' + self.ps_view_id ) 
				) {
					results_helper_container_after.show();
				}
				self.dialog_insert_view.dialog('close');
			} else if ( display == 'results' ) {
				window.icl_editor.insert('[wpv-view name="' + form_name + '" view_display="layout"' + override_values + extra_values + cache_values + ']');
				if ( 
					results_helper_container.length > 0 
					&& results_helper_container.hasClass( 'js-wpv-insert-form-workflow-help-box-for-' + self.ps_view_id ) 
				) {
					results_helper_container.fadeOut( 'fast' );
				}
				if ( 
					results_helper_container_after.length > 0 
					&& results_helper_container_after.hasClass( 'js-wpv-insert-form-workflow-help-box-for-after-' + self.ps_view_id ) 
				) {
					results_helper_container_after.show();
				}
				self.dialog_insert_view.dialog('close');
			} else if ( display == 'form' ) {
				if ( $( '.js-wpv-insert-view-form-action' ).hasClass( 'js-wpv-insert-view-form-dialog-steptwo' ) ) {
					if ( target == 'self' ) {
						window.icl_editor.insert('[wpv-form-view name="' + form_name + '" target_id="self"]');
						if ( results_helper_container.length > 0 ) {
							var results_shortcode = '<code>[wpv-view name="' + form_name + '" view_display=layout"]</code>';
							results_helper_container.find( '.js-wpv-insert-view-form-results-helper-name' ).html( form_name );
							results_helper_container.find( '.js-wpv-insert-view-form-results-helper-shortcode' ).html( results_shortcode );
							results_helper_container.addClass( 'js-wpv-insert-form-workflow-help-box-for-' + self.ps_view_id ).fadeIn( 'fast' );
						}
					} else {
						window.icl_editor.insert('[wpv-form-view name="' + form_name + '" target_id="' + set_target_id + '"' + override_values + extra_values + cache_values + ']');
					}
					$( '.js-wpv-insert-view-form-action' ).removeClass( 'js-wpv-insert-view-form-dialog-steptwo' );
					self.dialog_insert_view.dialog('close');
				} else {
					$( '.js-wpv-insert-view-form-action' ).addClass( 'js-wpv-insert-view-form-dialog-steptwo' );
					$( '.js-wpv-insert-view-form-action .ui-button-text' ).html( wpv_shortcodes_gui_texts.wpv_insert_view_shortcode );
					$( '.js-wpv-insert-view-form-prev' ).show();
					$( '.js-wpv-insert-view-form-display-container' ).hide();
					$( '.js-wpv-insert-view-form-target-container' ).show();
					if ( target == 'self' ) {
						$( '.js-wpv-insert-view-form-action' ).addClass( 'button-primary' ).removeClass( 'button-secondary' ).prop( 'disabled', false );
					} else {
						if ( set_target == 'existing' && set_target_id != '' ) {
							$( '.js-wpv-insert-view-form-target-set-actions' ).show();
						}
						$( '.js-wpv-insert-view-form-action' ).removeClass( 'button-primary' ).addClass( 'button-secondary' ).prop( 'disabled', true );
					}
				}
			}
		
		} else {
			
			window.icl_editor.insert('[wpv-view name="' + form_name + '"' + override_values + extra_values + cache_values + ']');
			self.dialog_insert_view.dialog('close');
			
		}
	};
	
	/**
	* Suggest for parametric search target
	*/
	
	$( document ).on( 'focus', '.js-wpv-insert-view-form-target-set-existing-title:not(.js-wpv-shortcode-gui-suggest-inited)', function() {
		var thiz = $( this );
		thiz
			.addClass( 'js-wpv-shortcode-gui-suggest-inited' )
			.suggest(ajaxurl + '?action=wpv_suggest_form_targets', {
				resultsClass: 'ac_results wpv-suggest-results',
				onSelect: function() {
					var t_value = this.value,
					t_split_point = t_value.lastIndexOf(' ['),
					t_title = t_value.substr( 0, t_split_point ),
					t_extra = t_value.substr( t_split_point ).split('#'),
					t_id = t_extra[1].replace(']', '');
					$( '.js-wpv-filter-form-help' ).hide();
					$('.js-wpv-insert-view-form-target-set-existing-title').val( t_title );
					t_edit_link = $('.js-wpv-insert-view-form-target-set-existing-link').data( 'editurl' );
					t_view_id = $('.js-wpv-insert-view-form-target-set-existing-link').data( 'viewid' );
					t_orig_id = $('.js-wpv-insert-view-form-target-set-existing-link').data('origid');
					$( '.js-wpv-insert-view-form-target-set-existing-link' ).attr( 'href', t_edit_link + t_id + '&action=edit&completeview=' + t_view_id + '&origid=' + t_orig_id );
					$( '.js-wpv-insert-view-form-target-set-existing-id' ).val( t_id ).trigger( 'change' );
					$( '.js-wpv-insert-view-form-target-set-actions' ).show();
				}
			});
	});
	
	/*
	* Adjust the action button text copy based on the action to perform
	*/
	
	$( document ).on( 'change', '.js-wpv-insert-view-form-display', function() {
		var display = $( '#js-wpv-view-shortcode-gui-dialog-container .js-wpv-insert-view-form-display:checked' ).val();
		if ( display == 'form' ) {
			$( '.js-wpv-insert-view-form-action .ui-button-text' ).html( wpv_shortcodes_gui_texts.wpv_next );
		} else {
			$( '.js-wpv-insert-view-form-action .ui-button-text' ).html( wpv_shortcodes_gui_texts.wpv_insert_view_shortcode );
		}
	});
	
	/*
	* Control the back button in the two-step setup
	*/
	
	self.wpv_insert_view_dialog_prev = function() {
		$( '.js-wpv-insert-view-form-target-container' ).hide();
		$( '.js-wpv-insert-view-form-display-container' ).show();
		$( '.js-wpv-insert-view-form-action' )
			.removeClass( 'js-wpv-insert-view-form-dialog-steptwo button-secondary' )
			.addClass( 'button-primary' )
			.prop( 'disabled', false );
		$( '.js-wpv-insert-view-form-action .ui-button-text' ).html( wpv_shortcodes_gui_texts.wpv_next );
		$( '.js-wpv-insert-view-form-prev' ).hide();
	};
	
	/*
	* Adjust the GUI when inserting just the form, based on the target options - target this or other page
	*/
	
	$( document ).on( 'change', '.js-wpv-insert-view-form-target', function() {
		var target = $( '.js-wpv-insert-view-form-target:checked' ).val(),
		set_target = $( '.js-wpv-insert-view-form-target-set:checked' ).val();
		if ( target == 'self' ) {
			$( '.js-wpv-insert-view-form-target-set-container' ).hide();
			$( '.js-wpv-insert-view-form-action' )
				.addClass( 'button-primary' )
				.prop( 'disabled', false );
		} else if ( target == 'other' ) {
			$( '.js-wpv-insert-view-form-target-set-container' ).fadeIn( 'fast' );
			if ( 
				set_target == 'existing' 
				&& $( '.js-wpv-insert-view-form-target-set-existing-id' ).val() != '' 
			) {
				$( '.js-wpv-insert-view-form-target-set-actions' ).show();
			}
			$( '.js-wpv-insert-view-form-action' )
				.removeClass( 'button-primary' )
				.addClass( 'button-secondary' )
				.prop( 'disabled', true );
		}
	});
	
	$( document ).on( 'click', '.js-wpv-insert-view-form-target-set-discard', function( e ) {
		e.preventDefault();
		$( '.js-wpv-insert-view-form-action' )
			.addClass( 'button-primary' )
			.removeClass( 'button-secondary' )
			.prop( 'disabled', false );
		$( '.js-wpv-insert-view-form-target-set-actions' ).hide();
	});
	
	$( document ).on( 'click', '.js-wpv-insert-view-form-target-set-existing-link', function() {
		$( '.js-wpv-insert-view-form-action' )
			.addClass( 'button-primary' )
			.removeClass( 'button-secondary' )
			.prop( 'disabled', false );
		$( '.js-wpv-insert-view-form-target-set-actions' ).hide();
	});
	
	/*
	* Adjust the GUI when inserting just the form and targeting another page, based on the target options - target existing or new page
	*/
	
	$( document ).on( 'change', '.js-wpv-insert-view-form-target-set', function() {
		var set_target = $( '.js-wpv-insert-view-form-target-set:checked' ).val();
		if ( set_target == 'create' ) {
			$( '.js-wpv-insert-view-form-target-set-existing-extra' ).hide();
			$( '.js-wpv-insert-view-form-target-set-create-extra' ).fadeIn( 'fast' );
			$( '.js-wpv-insert-view-form-action' )
				.removeClass( 'button-primary' )
				.addClass( 'button-secondary' )
				.prop( 'disabled', true );
		} else if ( set_target == 'existing' ) {
			$( '.js-wpv-insert-view-form-target-set-create-extra' ).hide();
			$( '.js-wpv-insert-view-form-target-set-existing-extra' ).fadeIn( 'fast' );
			$( '.js-wpv-insert-view-form-action' )
				.removeClass( 'button-primary' )
				.addClass( 'button-secondary' )
				.prop( 'disabled', true );
			if ( $( '.js-wpv-insert-view-form-target-set-existing-id' ).val() != '' ) {
				$( '.js-wpv-insert-view-form-target-set-actions' ).show();
			}
		}
	});
	
	/*
	* Adjust values when editing the target page title - clean data and mark this as unfinished
	*/
	
	$( document ).on('change input cut paste', '.js-wpv-insert-view-form-target-set-existing-title', function() {
		$( '.js-wpv-insert-view-form-target-set-actions' ).hide();
		$( '.js-wpv-insert-view-form-target-set-existing-link' ).attr( 'data-targetid', '' );
		$('.js-wpv-insert-view-form-target-set-existing-id')
			.val( '' )
			.trigger( 'manchange' );
	});
	
	/*
	* Disable the insert button when doing any change in the existing title textfield
	*
	* We use a custom event 'manchange' as in "manual change"
	*/
	
	$( document ).on( 'manchange', '.js-wpv-insert-view-form-target-set-existing-id', function() {
		$( '.js-wpv-insert-view-form-action' )
			.removeClass( 'button-primary' )
			.addClass( 'button-secondary' )
			.prop( 'disabled', true );
	});
	
	/*
	* Adjust GUI when creating a target page, based on the title value
	*/
	
	$( document ).on( 'change input cut paste', '.js-wpv-insert-view-form-target-set-create-title', function() {
		if ( $( '.js-wpv-insert-view-form-target-set-create-title' ).val() == '' ) {
			$( '.js-wpv-insert-view-form-target-set-create-action' )
				.prop( 'disabled', true )
				.addClass( 'button-secondary' )
				.removeClass( 'button-primary' );
		} else {
			$( '.js-wpv-insert-view-form-target-set-create-action' )
				.prop( 'disabled', false )
				.addClass( 'button-primary' )
				.removeClass( 'button-secondary' );
		}
	});
	
	/*
	* AJAX action to create a new target page
	*/

	$( document ).on( 'click', '.js-wpv-insert-view-form-target-set-create-action', function() {
		var thiz = $( this ),
		thiz_existing_radio = $( '.js-wpv-insert-view-form-target-set[value="existing"]' ),
		spinnerContainer = $('<div class="wpv-spinner ajax-loader">').insertAfter( thiz ).show();
		data = {
			action: 'wpv_create_form_target_page',
			post_title: $( '.js-wpv-insert-view-form-target-set-create-title' ).val(),
			_wpnonce: thiz.data( 'nonce' )
		};
		$.ajax({
			url:ajaxurl,
			data:data,
			success:function( response ) {
				decoded_response = $.parseJSON( response );
				if ( decoded_response.result == 'error' ) {
					
				} else {
					$( '.js-wpv-insert-view-form-target-set-existing-title' ).val( decoded_response.page_title );
					$( '.js-wpv-insert-view-form-target-set-existing-id' ).val( decoded_response.page_id );
					t_edit_link = $('.js-wpv-insert-view-form-target-set-existing-link').data( 'editurl' );
					$('.js-wpv-insert-view-form-target-set-existing-link')
							.attr( 'href', t_edit_link + decoded_response.page_id + '&action=edit&completeview=' + self.ps_view_id + '&origid=' + self.ps_orig_id );
					thiz_existing_radio
						.prop( 'checked', true )
						.trigger( 'change' );
					$( '.js-wpv-insert-view-form-target-set-actions' ).show();
				}
			},
			error: function ( ajaxContext ) {
				
			},
			complete: function() {
				spinnerContainer.remove();
			}
		});
	});
	
	// Close the finished help boxes
	
	$( document ).on( 'click', '.js-wpv-insert-form-workflow-help-box-close', function( e ) {
		e.preventDefault();
		$( this ).closest( '.js-wpv-insert-form-workflow-help-box, .js-wpv-insert-form-workflow-help-box-after' ).hide();
	});
	
    
    /**
	* wpv_insert_popup_conditional
	*
	* @since 1.9
	*/

	self.wpv_insert_popup_conditional = function( shortcode, title, params, nonce, object ) {
		/**
		* Build AJAX url
		*/
        
		var url = ajaxurl + '?';
		url += '_wpnonce=' + nonce;
		url += '&action=wpv_shortcode_gui_dialog_conditional_create';
		url += '&post_id=' + parseInt( object.post_id );
        
		/**
		* Calculate height
		*/
		var dialog_height = $( window ).height() - 100;
		
		self.dialog_insert_views_conditional.dialog('open').dialog({
            title: title,
            width: 770,
            maxHeight: dialog_height,
            draggable: false,
            resizable: false,
			position: { my: "center top+50", at: "center top", of: window }
        });
		
        self.dialog_insert_views_conditional.html( self.shortcodeDialogSpinnerContent );
		/**
		* Do AJAX call
		*/
		$.ajax({
			url: url,
			success: function( data ) {
				self.dialog_insert_views_conditional.html( data ).dialog( 'open' );
				$( '.js-wpv-shortcode-gui-insert' )
					.addClass( 'button-primary' )
					.removeClass( 'button-secondary' )
					.prop( 'disabled', false );
					/*
				$('.js-wpv-shortcode-gui-tabs')
					.tabs()
					.addClass('ui-tabs-vertical ui-helper-clearfix')
					.removeClass('ui-corner-top ui-corner-right ui-corner-bottom ui-corner-left ui-corner-all');
				$('.js-wpv-shortcode-gui-tabs ul, .js-wpv-shortcode-gui-tabs li').removeClass('ui-corner-top ui-corner-right ui-corner-bottom ui-corner-left ui-corner-all');
				*/
				
                self.wpv_conditional_editor = object.codemirror;
                self.wpv_conditional_object = object;
                if ( object.codemirror == '' ) {
                    if ( typeof object.ed.canvas !== 'undefined' ) {
                        self.wpv_conditional_text = object.ed.canvas.value.substring(object.ed.canvas.selectionStart, object.ed.canvas.selectionEnd);
                    } else {
                        self.wpv_conditional_text = object.ed.selection.getContent();
                        
                    }
                } else {
                    self.wpv_conditional_text = WPV_Toolset.CodeMirror_instance[object.codemirror].getSelection();
                }
                self.wpv_conditional_close = object.close_tag;
                
				
				/**
				 *
				 */
				self.wpv_conditional_add_row($('#js-wpv-conditionals'));
				
			}
		});
        
        
	};
    
    $(document).on('click', '.js-wpv-views-conditional-add-term', function(e) {
        self.wpv_conditional_add_row($('#js-wpv-conditionals'));
    });
        
    /**
	* bind type
	*/
	$( document ).on( 'click', '#js-wpv-views-conditional-shortcode-gui-dialog-container .js-wpv-shortcode-expression-switcher', function() {
		var thiz = $( this );
		if ( self.views_conditional_use_gui ) {
			thiz.fadeOut( 'fast' );
			$( '.js-wpv-shortcode-gui-attribute-wrapper-for-if' ).fadeOut( 400, function() {
				self.views_conditional_use_gui = false;
				$('#wpv-conditional-custom-expressions')
					.val( self.wpv_conditional_create_if_attribute('multiline') )
					.data( 'edited', false );
				thiz.html( wpv_shortcodes_gui_texts.conditional_enter_conditions_gui );
				$('.js-wpv-shortcode-gui-attribute-wrapper-for-custom-expressions, .js-wpv-shortcode-expression-switcher').fadeIn( 400, function() {
					
				});
			});
		} else {
			/**
		   * check editor if was edited, ask user
		   */
		   if ( $('#wpv-conditional-custom-expressions').data( 'edited' ) ) {
			   if ( ! confirm( wpv_shortcodes_gui_texts.conditional_switch_alert ) ) {
				   return;
			   }
		   }
		   thiz.fadeOut( 'fast' );
		   $( '.js-wpv-shortcode-gui-attribute-wrapper-for-custom-expressions' ).fadeOut( 400, function() {
			   self.views_conditional_use_gui = true;
			   thiz.html( wpv_shortcodes_gui_texts.conditional_enter_conditions_manually );
			   $( '.js-wpv-shortcode-gui-attribute-wrapper-for-if, .js-wpv-shortcode-expression-switcher' ).fadeIn( 400, function() {
				   
			   });
		   });
		}
	});
	
    /**
	* add wpv-conditional-custom-expressions
	*/
	$(document).on('keyup', '#wpv-conditional-custom-expressions', function() {
		if ( !$(this).data('edited') ) {
			$(this).data('edited', true);
		}
	});
    

	self.wpv_conditional_add_row = function ( container ) {
		if ( 'unfinished' == typeof wpv_conditional_data ) {
			return false;
		}
		html = '<tr class="js-wpv-views-condtional-item">';
		/**
		 * type
		 */
		html += '<td class="js-wpv-views-conditional-origin">';
		html += '<span class="js-wpv-views-condtional-type" style="display:inline-block;width:150px;"><select><option value="">'+wpv_conditional_data.labels.select_choose+'</option>';
		$.each(wpv_conditional_data.fields, function(key, field) {
			html += '<option value="'+field.slug+'">'+field.label+'</option>';
		});
		html += '</select></span>';
		/**
		 * field
		 */
		html += '<span class="js-wpv-views-condtional-field" style="display:inline-block;width:150px;"><select disabled="disabled">';
		html += '</select></td>';
		html += '</select></span>';
		html += '</td>';
		/**
		 * operator
		 */
		html += '<td class="js-wpv-views-condtional-operator">';
		html += '<select>';
		html += '<option value="eq">=</option>';
		html += '<option value="ne">!=</option>';
		html += '<option value="gt">&gt;</option>';
		html += '<option value="lt">&lt;</option>';
		html += '<option value="gte">&gt;=</option>';
		html += '<option value="lte">&lt;=</option>';
		html += '</select>';
		html += '</td>';
		html += '</select></td>';
		/**
		 * value
		 */
		html += '<td class="js-wpv-views-condtional-value">';
		html += '<input type="text">';
		html += '</td>';
		html += '<td class="js-wpv-views-condtional-connect">';
		html += '<select>';
		html += '<option value="AND">AND</option>';
		html += '<option value="OR">OR</option>';
		html += '</select>';
		html += '</td>';
		html += '</select></td>';
		/**
		 * action
		 */
		html += '<td style="text-align:right">';
		html += '<button class="button js-wpv-views-condtional-remove"><i class="icon-remove"></i></button>';
		html += '</td></tr>';
		$('.js-wpv-views-conditional-body').append(html);
		/**
		 * remove operator for first row
		 */
		self.wpv_conditional_row_remove_trash_from_first();		
		
		
		return false;
	}
    
    /**
	* bind remove
	*/
	$(document).on('click', '.js-wpv-views-condtional-remove', function() {
		var row = $(this).closest('tr');
		$( '.js-wpv-views-condtional-remove', '#js-wpv-conditionals' ).prop( 'disabled', true );
		row.addClass( 'wpv-condition-deleted' );
		row.fadeOut( 400, function() {
			row.remove();
			self.wpv_conditional_row_remove_trash_from_first();
			$( '.js-wpv-views-condtional-remove', '#js-wpv-conditionals' ).prop( 'disabled', false );
		});
	});
        
    /**
	* bind type
	*/
	$(document).on('change', '.js-wpv-views-condtional-type select', function() {
		var wpv_type = $(':selected', $(this)).val();
        if ( wpv_type === '' ){
            $('.js-wpv-views-condtional-field select', $(this).closest('tr')).html('').prop( 'disabled', true );
            return;
        }
		var html = '';
		$.each(wpv_conditional_data.fields[wpv_type].fields, function(key, field) {
			html += '<option value="'+field.slug+'" ';
			html += 'data-field-type="'+field.type+'" ';
			html += 'data-view-type="'+wpv_type+'" ';
			html += '>'+field.label+'</option>';
		});
		$('.js-wpv-views-condtional-field select', $(this).closest('tr')).html(html).prop( 'disabled', false );
	});

	/**
	 * remove operator for first row
	 */
	self.wpv_conditional_row_remove_trash_from_first = function(container) {
		if ( $('.js-wpv-views-condtional-item').length == 1) {
            $('.js-wpv-views-condtional-remove').hide();
        } else {
            $('.js-wpv-views-condtional-remove').show();
        }
        $('.js-wpv-views-conditional-body .js-wpv-views-condtional-item:first-child .js-wpv-views-condtional-connect', container).html('&nbsp;');
	}
    
	//-----------------------------------------
	// Generic shortcodes API GUI
	//-----------------------------------------

	/**
	 * Display a dialog for inserting a specific Views shortcode.
	 *
     * todo explain parameters
     * @param shortcode
     * @param {string} title Dialog title.
     * @param params
     * @param nonce
     * @param object
     *
	 * @since 1.9
	 */
	self.wpv_insert_popup = function( shortcode, title, params, nonce, object ) {

        //
        // Build AJAX url for displaying the dialog
        //
        var url = ajaxurl + '?',
		url_extra_data = '';
		
        url += '_wpnonce=' + nonce;
        url += '&action=wpv_shortcode_gui_dialog_create';
        url += '&shortcode=' + shortcode;
        url += '&post_id=' + parseInt($(object).data('post-id'));
		
		url_extra_data = self.filter_dialog_ajax_data( shortcode );
		
		url += url_extra_data;

        //
        // Calculate height
        //
        var dialog_height = $(window).height() - 100;


        // Show the "empty" dialog with a spinner while loading dialog content
        self.dialog_insert_shortcode.dialog('open').dialog({
            title: title,
            width: 770,
            maxHeight: dialog_height,
            draggable: false,
            resizable: false,
			position: { my: "center top+50", at: "center top", of: window }
        });
		
		self.manage_dialog_button_labels();

        self.dialog_insert_shortcode.html( self.shortcodeDialogSpinnerContent );
		
        //
        // Do AJAX call
        //
        $.ajax({
			url: url,
			success: function( data ) {
				/**
				* Load dialog data
				*/
				self.dialog_insert_shortcode.html(data);
				$( '.js-wpv-shortcode-gui-insert' )
					.addClass( 'button-primary' )
					.removeClass( 'button-secondary' )
					.prop( 'disabled', false );

				/**
				* Init dialog tabs
				*/
                $('.js-wpv-shortcode-gui-tabs')
                    .tabs({
						beforeActivate: function( event, ui ) {
							var valid = self.validate_shortcode_attributes( $( '#js-wpv-shortcode-gui-dialog-container' ), ui.oldPanel, $( '#js-wpv-shortcode-gui-dialog-container' ).find( '.js-wpv-filter-toolset-messages' ) );
							if ( ! valid ) {
								event.preventDefault();
								ui.oldTab.focus();
							}
						}
					})
                    .addClass('ui-tabs-vertical ui-helper-clearfix')
                    .removeClass('ui-corner-top ui-corner-right ui-corner-bottom ui-corner-left ui-corner-all');
				$('#js-wpv-shortcode-gui-dialog-tabs ul, #js-wpv-shortcode-gui-dialog-tabs li').removeClass('ui-corner-top ui-corner-right ui-corner-bottom ui-corner-left ui-corner-all');

                /**
                * After open dialog
                */
                self.after_open_dialog(shortcode, title, params, nonce, object);

                /**
                * Custom combo management
                */
				self.custom_combo_management();
			}
		});
	};
    
    /**
	 * Custom combo management
	 */
	self.custom_combo_management = function () {
		$( '.js-wpv-shortcode-gui-attribute-custom-combo').each( function() {
			var combo_parent = $( this ).closest( '.js-wpv-shortcode-gui-attribute-wrapper' ),
			combo_target = $( '.js-wpv-shortcode-gui-attribute-custom-combo-target', combo_parent );
			if ( $( '[value=custom-combo]:checked', combo_parent ).length) {
				$combo_target.show();
			}
			$( '[type=radio]', combo_parent ).on( 'change', function() {
				var thiz_radio = $( this );
				if (
					thiz_radio.is( ':checked' )
					&& 'custom-combo' == thiz_radio.val()
				   ) {
					   combo_target.slideDown( 'fast' );
				   } else {
					   combo_target.slideUp( 'fast' );
				   }
			});
		});
	}
	
	/**
	* filter_dialog_ajax_data
	*
	* Filter the empty extra string added to the request to create the dialog GUI, so we can pass additional parameters for some shortcodes.
	*
	* @param shortcode The shortcode to which the dialog is being created.
	*
	* @return ajax_extra_data
	*
	* @since 1.9
	*/
	
	self.filter_dialog_ajax_data = function( shortcode ) {
		var ajax_extra_data = '';
		switch( shortcode ) {
			case 'wpv-post-body':
				if ( 
					typeof WPViews.ct_edit_screen != 'undefined' 
					&& typeof WPViews.ct_edit_screen.ct_data != 'undefined'
					&& typeof WPViews.ct_edit_screen.ct_data.id != 'undefined'
				) {
					ajax_extra_data = '&wpv_suggest_wpv_post_body_view_template_exclude=' + WPViews.ct_edit_screen.ct_data.id;
				}
				break;
		}
		return ajax_extra_data;
	};


	/**
	* after_open_dialog
	*
	* @since 1.9
	*/
	self.after_open_dialog = function( shortcode, title, params, nonce, object ) {
		self.manage_fixed_initial_params( params );
		self.manage_special_cases( shortcode );
		self.manage_suggest_cache();
	};
	
	/**
	* manage_dialog_button_labels
	*
	* Adjusts the dialog button labels for usage on Fields and Views or Loop Wizard scenarios.
	*
	* @since 1.9
	*/
	
	self.manage_dialog_button_labels = function() {
		if ( self.shortcode_gui_insert ) {
			$( '.js-wpv-shortcode-gui-close .ui-button-text' ).html( wpv_shortcodes_gui_texts.wpv_close );
			$( '.js-wpv-shortcode-gui-insert .ui-button-text' ).html( wpv_shortcodes_gui_texts.wpv_insert_shortcode );
		} else {
			$( '.js-wpv-shortcode-gui-close .ui-button-text' ).html( wpv_shortcodes_gui_texts.wpv_cancel );
			$( '.js-wpv-shortcode-gui-insert .ui-button-text' ).html( wpv_shortcodes_gui_texts.wpv_save_settings );
		}
	};
	
	/**
	* manage_fixed_initial_params
	*
	* @since 1.9
	*/
	
	self.manage_fixed_initial_params = function( params ) {
		for ( var item in params ) {
			$( '.wpv-dialog' ).prepend( '<span class="wpv-shortcode-gui-attribute-wrapper js-wpv-shortcode-gui-attribute-wrapper" data-attribute="' + item + '" data-type="param"><input type="hidden" name="' + item + '" value="' + params[ item ].value + '" disabled="disabled" /></span>' );
		}
	};
	
	/**
	* manage_special_cases
	*
	* @since 1.9
	*/
	
	self.manage_special_cases = function( shortcode ) {
		switch ( shortcode ) {
			case 'wpv-post-author':
				self.manage_wpv_post_author_format_show_relation();
				break;
			case 'wpv-post-taxonomy':
				self.manage_wpv_post_taxonomy_format_show_relation();
				break;
			case 'wpv-post-featured-image':
				self.manage_wpv_post_featured_image_output_show_class();
				break;
		}
	};
	
	/**
	* manage_suggest_cache
	*
	* Populate suggest fields from cache if available
	*
	* @since 1.9
	*/
	
	self.manage_suggest_cache = function() {
		$( '.js-wpv-shortcode-gui-suggest' ).each( function() {
			var thiz_inner = $( this ),
			action_inner = '';
			if ( thiz_inner.data('action') != '' ) {
				action_inner = thiz_inner.data('action');
				if ( self.suggest_cache.hasOwnProperty( action_inner ) ) {
					thiz_inner
						.val( self.suggest_cache[action_inner] )
						.trigger( 'change' );
				}
			}
		});
	};
	
	/**
	* Init suggest on suggest attributes
	*
	* @since 1.9
	*/
	
	$( document ).on( 'focus', '.js-wpv-shortcode-gui-suggest:not(.js-wpv-shortcode-gui-suggest-inited)', function() {
		var thiz = $( this ),
		action = '';
		if ( thiz.data('action') != '' ) {
			action = thiz.data('action');
			ajax_extra_data = self.filter_suggest_ajax_data( action );
			thiz
				.addClass( 'js-wpv-shortcode-gui-suggest-inited' )
				.suggest(ajaxurl + '?action=' + action + ajax_extra_data, {
					resultsClass: 'ac_results wpv-suggest-results',
					onSelect: function() {
						self.suggest_cache[action] = this.value;
					}
				});
		}
	});
	
	/**
	* filter_suggest_ajax_data
	*
	* Filter the empty extra string added to the suggest request, so we can pass additional parameters for some shortcodes.
	*
	* @param action The suggest action to perform.
	*
	* @return ajax_extra_data
	*
	* @since 1.9
	*/
	
	self.filter_suggest_ajax_data = function( action ) {
		var ajax_extra_data = '';
		switch( action ) {
			case 'wpv_suggest_wpv_post_body_view_template':
				if ( 
					typeof WPViews.ct_edit_screen != 'undefined' 
					&& typeof WPViews.ct_edit_screen.ct_data != 'undefined'
					&& typeof WPViews.ct_edit_screen.ct_data.id != 'undefined'
				) {
					ajax_extra_data = '&wpv_suggest_wpv_post_body_view_template_exclude=' + WPViews.ct_edit_screen.ct_data.id;
				}
				break;
		}
		return ajax_extra_data;
	};
	
	/**
	* Manage item selector GUI
	*
	* @since 1.9
	*/
	
	$( document ).on( 'change', 'input.js-wpv-shortcode-gui-item-selector', function() {
		var thiz = $( this ),
		checked = thiz.val();
		$('.js-wpv-shortcode-gui-item-selector-has-related').each( function() {
			var thiz_inner = $( this );
			if ( $( 'input.js-wpv-shortcode-gui-item-selector:checked', thiz_inner ).val() == checked ) {
				$( '.js-wpv-shortcode-gui-item-selector-is-related', thiz_inner ).slideDown( 'fast' );
			} else {
				$( '.js-wpv-shortcode-gui-item-selector-is-related', thiz_inner ).slideUp( 'fast' );
			}
		});
	});
	
	/**
	* Manage placeholders: should be removed when focusing on a textfield, added back on blur
	*
	* @since 1.9
	*/
	
	$( document )
		.on( 'focus', '.js-wpv-shortcode-gui-attribute-has-placeholder, .js-wpv-has-placeholder', function() {
			var thiz = $( this );
			thiz.attr( 'placeholder', '' );
		})
		.on( 'blur', '.js-wpv-shortcode-gui-attribute-has-placeholder, .js-wpv-has-placeholder', function() {
			var thiz = $( this );
			if ( thiz.data( 'placeholder' ) ) {
				thiz.attr( 'placeholder', thiz.data( 'placeholder' ) );
			}
		});
	
	/**
	* validate_shortcode_attributes
	*
	* Validate method
	*
	* @since 1.9
	*/
	
	self.validate_shortcode_attributes = function( container, evaluate_container, error_container ) {
		self.clear_validate_messages( container );
		var valid = true;
		valid = self.manage_required_attributes( evaluate_container, error_container );
		evaluate_container.find( 'input:text' ).each( function() {
			var thiz = $( this ),
			thiz_val = thiz.val(),
			thiz_type = thiz.data( 'type' ),
			thiz_message = '',
			thiz_valid = true;
			if ( ! thiz.hasClass( 'js-toolset-shortcode-gui-invalid-attr' ) ) {
				switch ( thiz_type ) {
					case 'number':
						if ( 
							self.numeric_natural_pattern.test( thiz_val ) == false
							&& thiz_val != ''
						) {
							thiz_valid = false;
							thiz.addClass( 'toolset-shortcode-gui-invalid-attr js-toolset-shortcode-gui-invalid-attr' );
							thiz_message = wpv_shortcodes_gui_texts.attr_number_invalid;
						}
						break;
					case 'numberextended':
						if ( 
							self.numeric_natural_extended_pattern.test( thiz_val ) == false
							&& thiz_val != ''
						) {
							thiz_valid = false;
							thiz.addClass( 'toolset-shortcode-gui-invalid-attr js-toolset-shortcode-gui-invalid-attr' );
							thiz_message = wpv_shortcodes_gui_texts.attr_number_invalid;
						}
						break;
					case 'numberlist':
						if ( 
							self.numeric_natural_list_pattern.test( thiz_val.replace(/\s+/g, '') ) == false
							&& thiz_val != ''
						) {
							thiz_valid = false;
							thiz.addClass( 'toolset-shortcode-gui-invalid-attr js-toolset-shortcode-gui-invalid-attr' );
							thiz_message = wpv_shortcodes_gui_texts.attr_numberlist_invalid;
						}
						break;
					case 'year':
						if ( 
							self.year_pattern.test( thiz_val ) == false 
							&& thiz_val != ''
						) {
							thiz_valid = false;
							thiz.addClass( 'toolset-shortcode-gui-invalid-attr js-toolset-shortcode-gui-invalid-attr' );
							thiz_message = wpv_shortcodes_gui_texts.attr_year_invalid;
						}
						break;
					case 'month':
						if ( 
							self.month_pattern.test( thiz_val ) == false 
							&& thiz_val != ''
						) {
							thiz_valid = false;
							thiz.addClass( 'toolset-shortcode-gui-invalid-attr js-toolset-shortcode-gui-invalid-attr' );
							thiz_message = wpv_shortcodes_gui_texts.attr_month_invalid;
						}
						break;
					case 'week':
						if ( 
							self.week_pattern.test( thiz_val ) == false 
							&& thiz_val != ''
						) {
							thiz_valid = false;
							thiz.addClass( 'toolset-shortcode-gui-invalid-attr js-toolset-shortcode-gui-invalid-attr' );
							thiz_message = wpv_shortcodes_gui_texts.attr_week_invalid;
						}
						break;
					case 'day':
						if ( 
							self.day_pattern.test( thiz_val ) == false 
							&& thiz_val != ''
						) {
							thiz_valid = false;
							thiz.addClass( 'toolset-shortcode-gui-invalid-attr js-toolset-shortcode-gui-invalid-attr' );
							thiz_message = wpv_shortcodes_gui_texts.attr_day_invalid;
						}
						break;
					case 'hour':
						if ( 
							self.hour_pattern.test( thiz_val ) == false 
							&& thiz_val != ''
						) {
							thiz_valid = false;
							thiz.addClass( 'toolset-shortcode-gui-invalid-attr js-toolset-shortcode-gui-invalid-attr' );
							thiz_message = wpv_shortcodes_gui_texts.attr_hour_invalid;
						}
						break;
					case 'minute':
						if ( 
							self.minute_pattern.test( thiz_val ) == false 
							&& thiz_val != ''
						) {
							thiz_valid = false;
							thiz.addClass( 'toolset-shortcode-gui-invalid-attr js-toolset-shortcode-gui-invalid-attr' );
							thiz_message = wpv_shortcodes_gui_texts.attr_minute_invalid;
						}
						break;
					case 'second':
						if ( 
							self.second_pattern.test( thiz_val ) == false 
							&& thiz_val != ''
						) {
							thiz_valid = false;
							thiz.addClass( 'toolset-shortcode-gui-invalid-attr js-toolset-shortcode-gui-invalid-attr' );
							thiz_message = wpv_shortcodes_gui_texts.attr_second_invalid;
						}
						break;
					case 'dayofyear':
						if ( 
							self.dayofyear_pattern.test( thiz_val ) == false 
							&& thiz_val != ''
						) {
							thiz_valid = false;
							thiz.addClass( 'toolset-shortcode-gui-invalid-attr js-toolset-shortcode-gui-invalid-attr' );
							thiz_message = wpv_shortcodes_gui_texts.attr_dayofyear_invalid;
						}
						break;
					case 'dayofweek':
						if ( 
							self.dayofweek_pattern.test( thiz_val ) == false 
							&& thiz_val != ''
						) {
							thiz_valid = false;
							thiz.addClass( 'toolset-shortcode-gui-invalid-attr js-toolset-shortcode-gui-invalid-attr' );
							thiz_message = wpv_shortcodes_gui_texts.attr_dayofweek_invalid;
						}
						break;
					case 'url':
						if ( 
							self.url_patern.test( thiz_val ) == false
							&& thiz_val != ''
						) {
							thiz_valid = false;
							thiz.addClass( 'toolset-shortcode-gui-invalid-attr js-toolset-shortcode-gui-invalid-attr' );
							thiz_message = wpv_shortcodes_gui_texts.attr_url_invalid;
						}
						break;
				}
				if ( ! thiz_valid ) {
					valid = false;
					error_container
						.wpvToolsetMessage({
							text: thiz_message,
							type: 'error',
							inline: false,
							stay: true
						});
					// Hack to allow more than one error message per filter
					error_container
						.data( 'message-box', null )
						.data( 'has_message', false );
				}
			}
		});
		// Special case: item selector tab
		if (
			$( '.js-wpv-shortcode-gui-item-selector:checked', evaluate_container ).length > 0 
			&& 'item_id' == $( '.js-wpv-shortcode-gui-item-selector:checked', evaluate_container ).val() 
		) {
			var item_selection = $( '[name=specific_item_id]', evaluate_container ),
			item_selection_id = item_selection.val(),
			item_selection_valid = true,
			item_selection_message = '';
			if ( '' == item_selection_id ) {
				item_selection_valid = false;
				item_selection.addClass( 'toolset-shortcode-gui-invalid-attr js-toolset-shortcode-gui-invalid-attr' );
				item_selection_message = wpv_shortcodes_gui_texts.attr_empty;
			} else if ( self.numeric_natural_pattern.test( item_selection_id ) == false ) {
				item_selection_valid = false;
				item_selection.addClass( 'toolset-shortcode-gui-invalid-attr js-toolset-shortcode-gui-invalid-attr' );
				item_selection_message = wpv_shortcodes_gui_texts.attr_number_invalid;
			}
			if ( ! item_selection_valid ) {
				valid = false;
				error_container
					.wpvToolsetMessage({
						text: item_selection_message,
						type: 'error',
						inline: false,
						stay: true
					});
				// Hack to allow more than one error message per filter
				error_container
					.data( 'message-box', null )
					.data( 'has_message', false );
			}
		}
		return valid;
	};
	
	$( document ).on( 'change keyup input cut paste', '.js-wpv-shortcode-gui-dialog-container input, .js-wpv-shortcode-gui-dialog-container select', function() {
		var thiz = $( this );
		thiz.removeClass( 'toolset-shortcode-gui-invalid-attr js-toolset-shortcode-gui-invalid-attr' );
		thiz
			.closest( '.js-wpv-shortcode-gui-dialog-container' )
				.find('.toolset-alert-error').not( '.js-wpv-permanent-alert-error' )
				.each( function() {
					$( this ).remove();
				});
	});
	
	self.clear_validate_messages = function( container ) {
		container
			.find('.toolset-alert-error').not( '.js-wpv-permanent-alert-error' )
			.each( function() {
				$( this ).remove();
			});
		container
			.find( '.js-toolset-shortcode-gui-invalid-attr' )
			.removeClass( 'toolset-shortcode-gui-invalid-attr js-toolset-shortcode-gui-invalid-attr' );
	};
	
	/**
	* manage_required_attributes
	*
	* @since 1.9
	*/
	
	self.manage_required_attributes = function( evaluate_container, error_container ) {
		var valid = true,
		error_container = $( '#js-wpv-shortcode-gui-dialog-container' ).find( '.js-wpv-filter-toolset-messages' );
		evaluate_container.find( '.js-shortcode-gui-field.js-wpv-shortcode-gui-required' ).each( function() {
			var thiz = $( this ),
			thiz_valid = true,
			thiz_parent = thiz.closest('.js-wpv-shortcode-gui-attribute-custom-combo');
			if ( thiz_parent.length ) {
				if ( 
					$( '[value=custom-combo]:checked', thiz_parent ).length 
					&& thiz.val() == ''
				) {
					thiz.addClass( 'toolset-shortcode-gui-invalid-attr js-toolset-shortcode-gui-invalid-attr' );
					thiz_valid = false;
				}
			} else {
				if ( thiz.val() == '' ) {
					thiz.addClass( 'toolset-shortcode-gui-invalid-attr js-toolset-shortcode-gui-invalid-attr' );
					thiz_valid = false;
				}
			}
			if ( ! thiz_valid ) {
				valid = false;
				error_container
					.wpvToolsetMessage({
						text: wpv_shortcodes_gui_texts.attr_empty,
						type: 'error',
						inline: false,
						stay: true
					});
				// Hack to allow more than one error message per filter
				error_container
					.data( 'message-box', null )
					.data( 'has_message', false );
			}
		});
		return valid;
	};

	/**
	* wpv_insert_shortcode
	*
	* Insert shortcode to active editor
	*
	* @since 1.9
	*/

	self.wpv_insert_shortcode = function() {
		var shortcode_name = $('.js-wpv-shortcode-gui-shortcode-name').val(),
		shortcode_attribute_key,
		shortcode_attribute_value,
		shortcode_attribute_default_value,
		shortcode_attribute_string = '',
		shortcode_attribute_values = {},
		shortcode_content = '',
		shortcode_to_insert = '',
		shortcode_valid = self.validate_shortcode_attributes( $( '#js-wpv-shortcode-gui-dialog-container' ), $( '#js-wpv-shortcode-gui-dialog-container' ), $( '#js-wpv-shortcode-gui-dialog-container' ).find( '.js-wpv-filter-toolset-messages' ) );		
		if ( ! shortcode_valid ) {
			return;
		}
		$( '.js-wpv-shortcode-gui-attribute-wrapper', '#js-wpv-shortcode-gui-dialog-container' ).each( function() {
			var thiz_attribute_wrapper = $( this ),
			shortcode_attribute_key = thiz_attribute_wrapper.data('attribute');
			switch ( thiz_attribute_wrapper.data('type') ) {
				case 'post':
				case 'user':
					shortcode_attribute_value = $( '.js-wpv-shortcode-gui-item-selector:checked', thiz_attribute_wrapper ).val();
					switch( shortcode_attribute_value ) {
						case 'current':
							shortcode_attribute_value = false;
							break;
						case 'parent':
							if ( shortcode_attribute_value ) {
								shortcode_attribute_value = '$' + shortcode_attribute_value;
							}
							break;
						case 'related':
							shortcode_attribute_value = $( '[name=related_post]:checked', thiz_attribute_wrapper ).val();
							if ( shortcode_attribute_value ) {
								shortcode_attribute_value = '$' + shortcode_attribute_value;
							}
							break;
						case 'item_id':
							shortcode_attribute_value = $( '[name=specific_item_id]', thiz_attribute_wrapper ).val();
						default:
					}
					break;
				case 'select':
					shortcode_attribute_value = $('option:checked', thiz_attribute_wrapper ).val();
					break;
				case 'radio':
					shortcode_attribute_value = $('input:checked', thiz_attribute_wrapper ).val();
					if ( 'custom-combo' == shortcode_attribute_value ) {
						shortcode_attribute_value = $('.js-wpv-shortcode-gui-attribute-custom-combo-target', $('input:checked', thiz_attribute_wrapper ).closest('.js-wpv-shortcode-gui-attribute-custom-combo')).val();
					}
					break;
				case 'checkbox':
					shortcode_attribute_value = $('input:checked', thiz_attribute_wrapper ).val();
					break;
				default:
					shortcode_attribute_value = $('input', thiz_attribute_wrapper ).val();
			}
			
			shortcode_attribute_default_value = thiz_attribute_wrapper.data('default');
			/**
			* Fix true/false from data attribute for shortcode_attribute_default_value
			*/
			if ( 'boolean' == typeof shortcode_attribute_default_value ) {
				shortcode_attribute_default_value = shortcode_attribute_default_value ? 'true' :'false';
			}
			/**
			* Filter value
			*/
			shortcode_attribute_value = self.filter_computed_attribute_value( shortcode_name, shortcode_attribute_key, shortcode_attribute_value );
			/**
			* Add to the shortcode_attribute_string string
			*/
			if ( 
				shortcode_attribute_value 
				&& shortcode_attribute_value != shortcode_attribute_default_value 
			) {
				shortcode_attribute_string += ' ' + shortcode_attribute_key + '="' + shortcode_attribute_value + '"';
				shortcode_attribute_values[shortcode_attribute_key] = shortcode_attribute_value;
			}
		});
		shortcode_to_insert = '[' + shortcode_name + shortcode_attribute_string + ']';
		/**
		* Shortcodes with content
		*/       
		if ( $( '.js-wpv-shortcode-gui-content' ).length > 0 ) {
            shortcode_content = $( '.js-wpv-shortcode-gui-content' ).val();
            /**
                * Filter shortcode content
            */
            shortcode_content = self.filter_computed_content( shortcode_name, shortcode_content, shortcode_attribute_values );
			shortcode_to_insert += shortcode_content;
            shortcode_to_insert += '[/' + shortcode_name + ']';
		}
		/**
		* Close, insert if needed and fire custom event
		*/
		self.dialog_insert_shortcode.dialog('close');
		if ( self.shortcode_gui_insert ) {
			window.icl_editor.insert( shortcode_to_insert );
		}
		$( document ).trigger( 'js_event_wpv_shortcode_inserted', [ shortcode_name, shortcode_content, shortcode_attribute_values, shortcode_to_insert ] );
	};
	
	$( document ).on( 'js_event_wpv_shortcode_inserted', function() {
		self.shortcode_gui_insert_count = self.shortcode_gui_insert_count + 1;
	});
    
    /**
	* wpv_insert_view_conditional_shortcode
	*
	* Insert Views conditional shortcode to active editor
	*
	* @since 1.10
	*/

	self.wpv_insert_view_conditional_shortcode = function() {
		var shortcode_name = $('.js-wpv-views-conditional-shortcode-gui-dialog-name').val(),
		shortcode_attribute_key,
		shortcode_attribute_value,
		shortcode_attribute_default_value,
		shortcode_attribute_string = '',
		shortcode_attribute_values = {},
		shortcode_content = '',
		shortcode_to_insert = '',
		shortcode_valid = self.validate_shortcode_attributes( $( '#js-wpv-views-conditional-shortcode-gui-dialog-container' ), $( '#js-wpv-views-conditional-shortcode-gui-dialog-container' ), $( '#js-wpv-views-conditional-shortcode-gui-dialog-container' ).find( '.js-wpv-filter-toolset-messages' ) );		
		if ( ! shortcode_valid ) {
			return;
		}
		$( '.js-wpv-shortcode-gui-attribute-wrapper', '#js-wpv-views-conditional-shortcode-gui-dialog-container' ).each( function() {
			var thiz_attribute_wrapper = $( this ),
			shortcode_attribute_key = thiz_attribute_wrapper.data('attribute');
			switch ( thiz_attribute_wrapper.data('type') ) {
				case 'radio':
					shortcode_attribute_value = $('input:checked', thiz_attribute_wrapper ).val();
					if ( 'custom-combo' == shortcode_attribute_value ) {
						shortcode_attribute_value = $('.js-wpv-shortcode-gui-attribute-custom-combo-target', $('input:checked', thiz_attribute_wrapper ).closest('.js-wpv-shortcode-gui-attribute-custom-combo')).val();
					}
					break;				
				default:
					shortcode_attribute_value = $('input', thiz_attribute_wrapper ).val();
			}
			
			shortcode_attribute_default_value = thiz_attribute_wrapper.data('default');
			/**
			* Fix true/false from data attribute for shortcode_attribute_default_value
			*/
			if ( 'boolean' == typeof shortcode_attribute_default_value ) {
				shortcode_attribute_default_value = shortcode_attribute_default_value ? 'true' :'false';
			}
			/**
			* Filter value
			*/
            shortcode_attribute_value = self.filter_computed_attribute_value( shortcode_name, shortcode_attribute_key, shortcode_attribute_value );
			/**
			* Add to the shortcode_attribute_string string
			*/
			if ( 
				shortcode_attribute_value 
				&& shortcode_attribute_value != shortcode_attribute_default_value 
			) {
				shortcode_attribute_string += ' ' + shortcode_attribute_key + '="' + shortcode_attribute_value + '"';
				shortcode_attribute_values[shortcode_attribute_key] = shortcode_attribute_value;
			}
		});
        
		shortcode_to_insert = '[' + shortcode_name + shortcode_attribute_string + ']';
		/**
		* Shortcodes with content
		*/       
		if ( $( '.js-wpv-shortcode-gui-content' ).length > 0 ) {
            shortcode_content = $( '.js-wpv-shortcode-gui-content' ).val();
            /**
                * Filter shortcode content
            */
            shortcode_content = self.filter_computed_content( shortcode_name, shortcode_content, shortcode_attribute_values );
			var selected_text = self.wpv_conditional_text;
            if ( self.wpv_conditional_close ) {
                shortcode_to_insert += selected_text;
                shortcode_to_insert += '[/' + shortcode_name + ']';
                self.views_conditional_qtags_opened = false;
            } else {
                self.views_conditional_qtags_opened = true;
            }
		}
		/**
		* Close, insert if needed and fire custom event
		*/
        
		self.dialog_insert_views_conditional.dialog('close');
        
		if ( self.shortcode_gui_insert ) {
			window.icl_editor.insert( shortcode_to_insert );
		}
		$( document ).trigger( 'js_event_wpv_shortcode_inserted', [ shortcode_name, shortcode_content, shortcode_attribute_values, shortcode_to_insert ] );
	};
	
	//--------------------------------
	// Special cases
	//--------------------------------
	
	/**
	* wpv-post-author management
	* Handle the change in format that shows/hides the show attribute
	*
	* @since 1.9
	*/
	
	$( document ).on( 'change', '#wpv-post-author-format .js-shortcode-gui-field', function() {
		self.manage_wpv_post_author_format_show_relation();
	});
	
	self.manage_wpv_post_author_format_show_relation = function() {
		if ( $( '#wpv-post-author-format' ).length ) {
			if ( 'meta' == $( '.js-shortcode-gui-field:checked', '#wpv-post-author-format' ).val() ) {
				$( '.js-wpv-shortcode-gui-attribute-wrapper-for-meta', '#wpv-post-author-display-options' ).slideDown( 'fast' );
			} else {
				$( '.js-wpv-shortcode-gui-attribute-wrapper-for-meta', '#wpv-post-author-display-options' ).hide();
			}
		}
	};
	
	/**
	* wpv-post-taxonomy management
	* Handle the change in format that shows/hides the show attribute
	*
	* @since 1.9
	*/
	
	$( document ).on( 'change', '#wpv-post-taxonomy-format .js-shortcode-gui-field', function() {
		self.manage_wpv_post_taxonomy_format_show_relation();
	});
	
	self.manage_wpv_post_taxonomy_format_show_relation = function() {
		if ( $( '#wpv-post-taxonomy-format' ).length ) {
			if ( 'link' == $( '.js-shortcode-gui-field:checked', '#wpv-post-taxonomy-format' ).val() ) {
				$( '.js-wpv-shortcode-gui-attribute-wrapper-for-show', '#wpv-post-taxonomy-display-options' ).slideDown( 'fast' );
			} else {
				$( '.js-wpv-shortcode-gui-attribute-wrapper-for-show', '#wpv-post-taxonomy-display-options' ).slideUp( 'fast' );
			}
		}
	};
	
	/**
	* wpv-post-featured-image management
	* Handle the change in output that shows/hides the class attribute
	*
	* @since 1.9
	*/
	
	$( document ).on( 'change', '#wpv-post-featured-image-output.js-shortcode-gui-field', function() {
		self.manage_wpv_post_featured_image_output_show_class();
	});
	
	self.manage_wpv_post_featured_image_output_show_class = function() {
		if ( $( '#wpv-post-featured-image-output' ).length ) {
			if ( 'img' == $( '#wpv-post-featured-image-output.js-shortcode-gui-field' ).val() ) {
				$( '.js-wpv-shortcode-gui-attribute-wrapper-for-class', '#wpv-post-featured-image-display-options' ).slideDown( 'fast' );
			} else {
				$( '.js-wpv-shortcode-gui-attribute-wrapper-for-class', '#wpv-post-featured-image-display-options' ).slideUp( 'fast' );
			}
		}
	};
	
	/**
	* filter_computed_attribute_value
	*
	* @since 1.9
	*/
	
	self.filter_computed_attribute_value = function( shortcode, attribute, value ) {
		switch ( shortcode ) {
			case 'wpv-post-author':
				if (
					'meta' == attribute
					&& 'meta' != $( '.js-shortcode-gui-field:checked', '#wpv-post-author-format' ).val() 
				) {
					value = false;
				}
				break;
			case 'wpv-post-taxonomy':
				if (
					'show' == attribute 
					&& 'link' != $( '.js-shortcode-gui-field:checked', '#wpv-post-taxonomy-format' ).val()
				) {
					value = false;
				}
				break;
			case 'wpv-post-featured-image':
				if (
					'class' == attribute
					&& 'img' != $( '#wpv-post-featured-image-output.js-shortcode-gui-field' ).val()
				) {
					value = false;
				}
				break;
			case 'wpv-conditional':
				switch( attribute ) {
					case 'if':
						if ( self.views_conditional_use_gui ) {
							value = self.wpv_conditional_create_if_attribute( 'singleline' );
						} else {
							value = $('#wpv-conditional-custom-expressions').val();
						}
						if ( value == '' ) {
							value = "('1' eq '1')";
						}
						break;
					case 'custom-expressions':
						value = false;
				}
			break;
		}
		return value;
	};
	
    /**
	* wpv_conditional_create_if_attribute
	*
	* @since 1.9
	*/
	self.wpv_conditional_create_if_attribute = function( mode ) {
		var value = '';
		$('.js-wpv-views-condtional-item').each( function() {
			var tr = $(this);
			if ( $('.js-wpv-views-condtional-field :selected', tr).val() ) {
				if ( value ) {
					if ( 'multiline' == mode ) {
						value += "\n";
					}
					value += ' '+$('.js-wpv-views-condtional-connect :checked', tr).val()+' ';
					if ( 'multiline' == mode ) {
						value += "\n";
					}
				}
				value += '( ';
				value += $('.js-wpv-views-condtional-field :selected', tr).val();
				value += ' ';
				value += $('.js-wpv-views-condtional-operator :selected', tr).val();
				value += ' \'';
				value += $('.js-wpv-views-condtional-value input', tr).val();
				value += '\' ';
				value += ')';
			}
		});
		return value;
	}
    
	/**
	* filter_computed_content
	*
	* @since 1.9
	*/
	
	self.filter_computed_content = function( shortcode, content, values ) {
		switch ( shortcode ) {
			case 'wpv-for-each':
				if ( values.hasOwnProperty( 'field' ) ) {
					content = '[wpv-post-field name="' + values.field + '"]';
				}
				break;
		}
		return content;
	};
	
	/**
	* load_post_field_section_on_demand
	*
	* Load the Post field section on the shortcodes GUI on demand
	* Used to load non-Types custom fields only when needed
	*
	* @since 1.10
	*/
	
	self.load_post_field_section_on_demand = function( event, object ) {
		event.stopPropagation();
		var thiz = $( object );
		if ( self.post_field_section ) {
			var thiz_group_list = thiz.closest( '.js-wpv-shortcode-gui-group-list' );
			thiz_group_list
				.fadeOut( 'fast', function() {
					thiz_group_list
						.html( response.data.section )
						.fadeIn( 'fast' );
				});
		} else {
			var url = ajaxurl + '?action=wpv_shortcodes_gui_load_post_field_section_on_demand';
			$.ajax({
				url: url,
				success: function( response ) {
					self.post_field_section = response.data.section;
					$( '.js-wpv-shortcode-gui-group-list-post-field-section' ).each( function() {
						var thiz_instance = $( this );
						thiz_instance
							.fadeOut( 'fast', function() {
								thiz_instance
									.html( response.data.section )
									.fadeIn( 'fast' );
							});
					});
				}
			});
		}
	};
	
	/**
	* Insert wpv-post-field shortcodes after generating the section on the GUI on demand
	*
	* @since 1.10
	*/
	
	$( document ).on( 'click', '.js-wpv-shortcode-gui-post-field-section-item', function() {
		var thiz = $( this ),
		thiz_fieldkey = thiz.data( 'fieldkey' ),
		thiz_group_list = thiz.closest( '.js-wpv-shortcode-gui-group-list' ),
		thiz_editor = thiz_group_list.data( 'editor' ),
		thiz_shortcode = '[wpv-post-field name="' + thiz_fieldkey + '"]';
		window.wpcfActiveEditor = thiz_editor;
		icl_editor.insert( thiz_shortcode );
		return false;
	});

	self.init();

};

jQuery( document ).ready( function( $ ) {
	WPViews.shortcodes_gui = new WPViews.ShortcodesGUI( $ );
});

var wpcfFieldsEditorCallback_redirect = null;

function wpcfFieldsEditorCallback_set_redirect(function_name, params) {
	wpcfFieldsEditorCallback_redirect = {'function' : function_name, 'params' : params};
}

/*
* wpv-conditional shortcode QTags callback
*/
function wpv_add_conditional_quicktag_function(e, c, ed) {
    var  t = this;
    /*
        !Important fix. If shortcode added from quicktags and not closed and we chage mode from text to visual, JS will generate error that closeTag = undefined.        
    */
    t.closeTag = function(el, event) {
        var ret = false, i = 0;
        while ( i < event.openTags.length ) {
              ret = event.openTags[i] == this.id ? i : false;
              el.value = this.display;
              i ++;
       }
       ed.openTags.splice(ret, 1);
    };
    window.wpcfActiveEditor = ed.id;
    var current_editor_object = {};
    if ( ed.canvas.selectionStart !== ed.canvas.selectionEnd ) {
        //When texty selected
        current_editor_object = {'e' : e, 'c' : c, 'ed' : ed, 't' : t, 'post_id' : '', 'close_tag' : true, 'codemirror' : ''};
        WPViews.shortcodes_gui.wpv_insert_popup_conditional('wpv-conditional', icl_editor_localization_texts.wpv_insert_conditional_shortcode, {}, icl_editor_localization_texts.wpv_editor_callback_nonce, current_editor_object );
    } else if ( ed.openTags ) {
        // if we have an open tag, see if it's ours
        var ret = false, i = 0, t = this;
        while ( i < ed.openTags.length ) {
            ret = ed.openTags[i] == t.id ? i : false;
             i ++;
        }
        if ( ret === false ) {
            t.tagStart = '';
            t.tagEnd = false;                
            if ( ! ed.openTags ) {
                ed.openTags = [];
            }
            ed.openTags.push(t.id);
            e.value = '/' + e.value;
            current_editor_object = {'e' : e, 'c' : c, 'ed' : ed, 't' : t, 'post_id' : '', 'close_tag' : false, 'codemirror' : ''};                
            WPViews.shortcodes_gui.wpv_insert_popup_conditional('wpv-conditional', icl_editor_localization_texts.wpv_insert_conditional_shortcode, {}, icl_editor_localization_texts.wpv_editor_callback_nonce, current_editor_object );
        } else {
            // close tag
            ed.openTags.splice(ret, 1);
            WPViews.shortcodes_gui.views_conditional_qtags_opened = false;
            t.tagStart = '[/wpv-conditional]';
            e.value = t.display;
            QTags.TagButton.prototype.callback.call(t, e, c, ed);
       }
    } else {
        // last resort, no selection and no open tags
        // so prompt for input and just open the tag           
        t.tagStart = '';
        t.tagEnd = false;
        if ( ! ed.openTags ) {
            ed.openTags = [];
        }
        ed.openTags.push(t.id);
        e.value = '/' + e.value;       
        current_editor_object = {'e' : e, 'c' : c, 'ed' : ed, 't' : t, 'post_id' : '', 'close_tag' : false, 'codemirror' : ''};
        WPViews.shortcodes_gui.wpv_insert_popup_conditional('wpv-conditional', icl_editor_localization_texts.wpv_insert_conditional_shortcode, {}, icl_editor_localization_texts.wpv_editor_callback_nonce, current_editor_object );
    }
}
