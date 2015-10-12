jQuery(function($){

    var $cfList = $('.js-cf-toggle'),
    $cfSummary = $('.js-cf-summary');

    $('.js-show-cf-list').on('click', function(e) {
        e.preventDefault();
        $cfList.fadeIn('fast');
        $cfSummary.hide();
        return false;
    });

    $('.js-hide-cf-list').on('click', function(e) {
        e.preventDefault();
        $cfList.hide();
        $cfSummary.fadeIn('fast');
        return false;
    });

    // Save CF options
    $('.js-save-cf-list').on('click', function(e) {
        e.preventDefault();

        var $spinner = $('.js-cf-spinner').css('display','inline-block'),
        $selectedCfList = $('.js-selected-cf-list'),
        $checked = $('.js-all-cf-list :checked'),
        $cfExistsMessage = $('.js-cf-exists-message'),
        $cfNotExistsMessage = $('.js-no-cf-message'),
        data;

        data = $('.js-all-cf-list input[type="checkbox"]').serialize();
        data += '&action=wpv_get_show_hidden_custom_fields';
        data += '&wpv_show_hidden_custom_fields_nonce=' + $('#wpv_show_hidden_custom_fields_nonce').val();

        $.ajax({
            async:false,
            type:"POST",
            url:ajaxurl,
            data:data,
            success:function(response){
                if ( (typeof(response) !== 'undefined') ) {

                    $selectedCfList.empty();

                    if ( $checked.length !== 0 ) {

                        $cfExistsMessage.fadeIn('fast');
                        $cfNotExistsMessage.hide();
                        $selectedCfList.fadeIn('fast');

                        $.each( $checked, function() {
                            $selectedCfList.append('<li>' + $(this).next('label').text() + '</li>');
                        });

                    }

                    else {

                        $cfExistsMessage.hide();
                        $cfNotExistsMessage.fadeIn('fast');
                        $selectedCfList.hide();

                    }

                    $cfSummary.fadeIn('fast');
                    $cfList.hide();
                    $('.js-cf-update-message').show().fadeOut('slow');
                }
                else {
                    console.log( "Error: AJAX returned ", response );
                }
            },
            error: function (ajaxContext) {
                console.log( "Error: ", ajaxContext.responseText );
            },
            complete: function() {
				$spinner.hide();
            }
        });

        return false;
    });

    // Save custom inner shortcodes and conditional functions options

	$(document).on('input cut paste', '.js-wpv-add-item-settings-form-newname', function(e){
		var parent_form = $( this ).closest( '.js-wpv-add-item-settings-form' );
		$('.js-wpv-cs-error, .js-wpv-cs-dup, .js-wpv-cs-ajaxfail', parent_form).hide();
		if ( $(this).val() != '' ) {
			$('.js-wpv-add-item-settings-form-button', parent_form).addClass('button-primary').removeClass('button-secondary').prop('disabled', false);
		} else {
			$('.js-wpv-add-item-settings-form-button', parent_form).removeClass('button-primary').addClass('button-secondary').prop('disabled', true);
		}
	});

	$('.js-wpv-add-item-settings-form').submit(function(e){
		var thiz = $( this );
		e.preventDefault();
		$('.js-wpv-add-item-settings-form-button', thiz).click();
		return false;
	});
	
	// Add additional inner shortcodes

	$('.js-custom-inner-shortcodes-add').on('click', function(e){
		e.preventDefault();
		var thiz = $(this),
		shortcode_pattern = /^[a-z0-9\-\_]+$/,
		parent_form = $( this ).closest( '.js-wpv-add-item-settings-form' ),
		parent_container = $( this ).closest('.js-wpv-add-item-settings-wrapper'),
		newshortcode = $('.js-wpv-add-item-settings-form-newname', parent_form);
		$('.js-wpv-cs-error, .js-wpv-cs-dup, .js-wpv-cs-ajaxfail', parent_form).hide();
		if (shortcode_pattern.test(newshortcode.val()) == false) {
			$('.js-wpv-cs-error', parent_form).show();
		} else if ( $('.js-' + newshortcode.val() + '-item', parent_container).length > 0 ) {
			$('.js-wpv-cs-dup', parent_form).show();
		} else {
			var spinnerContainer = $('<div class="wpv-spinner ajax-loader">').insertAfter($(this)).show();
			thiz.removeClass('button-primary').addClass('button-secondary').prop('disabled', true);
			var data = {
				action: 'wpv_update_custom_inner_shortcodes',
				csaction: 'add',
				cstarget: newshortcode.val(),
				wpv_custom_inner_shortcodes_nonce: $('#wpv_custom_inner_shortcodes_nonce').val()
			};

			$.ajax({
				async:false,
				type:"POST",
				url:ajaxurl,
				data:data,
				success:function(response){
					if ( (typeof(response) !== 'undefined') ) {
						if (response == 'ok') {
							$('.js-wpv-add-item-settings-list', parent_container).append('<li class="js-' + newshortcode.val() + '-item"><span class="">[' + newshortcode.val() + ']</span> <i class="icon-remove-sign js-custom-shortcode-delete" data-target="' + newshortcode.val() + '"></i></li>');
							newshortcode.val('');
						}
						else {
							$('.js-wpv-cs-ajaxfail', parent_form).show();
							console.log( "Error: WordPress AJAX returned ", response );
						}
					}
					else {
						$('.js-wpv-cs-ajaxfail', parent_form).show();
						console.log( "Error: AJAX returned ", response );
					}
				},
				error: function (ajaxContext) {
					$('.js-wpv-cs-ajaxfail', parent_form).show();
					console.log( "Error: ", ajaxContext.responseText );
				},
				complete: function() {
					spinnerContainer.remove();
				}
			});
		}
		return false;
	});
	
	// Delete additional inner shortcodes

	$(document).on('click', '.js-custom-shortcode-delete', function(e){
		e.preventDefault();
		var thiz = $(this).data('target'),
		parent_container = $( this ).closest('.js-wpv-add-item-settings-wrapper'),
		spinnerContainer = $('<div class="wpv-spinner ajax-loader">').insertAfter($('.js-custom-inner-shortcodes-add')).show();
		var data = {
			action: 'wpv_update_custom_inner_shortcodes',
			csaction: 'delete',
			cstarget: thiz,
			wpv_custom_inner_shortcodes_nonce: $('#wpv_custom_inner_shortcodes_nonce').val()
		};

		$.ajax({
			async:false,
			type:"POST",
			url:ajaxurl,
			data:data,
			success:function(response){
				if ( (typeof(response) !== 'undefined') ) {
					if (response == 'ok') {
						$( 'li.js-' + thiz + '-item', parent_container )
							.addClass( 'remove' )
							.fadeOut( 'fast', function() { 
								$( this ).remove(); 
							});
					}
					else {
						$('.js-wpv-cs-ajaxfail', parent_container).show();
						console.log( "Error: WordPress AJAX returned ", response );
					}
				}
				else {
					$('.js-wpv-cs-ajaxfail', parent_container).show();
					console.log( "Error: AJAX returned ", response );
				}
			},
			error: function (ajaxContext) {
				$('.js-wpv-cs-ajaxfail', parent_container).show();
				console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() {
				spinnerContainer.remove();
			}
		});

		return false;
	});
	
	// Add custom conditional functions
	
	$('.js-custom-conditional-function-add').on('click', function(e){
		e.preventDefault();
		var thiz = $(this),
		shortcode_pattern = /^[a-zA-Z0-9\:\-\_]+$/,
		parent_form = $( this ).closest( '.js-wpv-add-item-settings-form' ),
		parent_container = $( this ).closest('.js-wpv-add-item-settings-wrapper'),
		newshortcode = $('.js-wpv-add-item-settings-form-newname', parent_form),
		sanitized_val = newshortcode.val().replace( '::', '-_paamayim_-' );
		$('.js-wpv-cs-error, .js-wpv-cs-dup, .js-wpv-cs-ajaxfail', parent_form).hide();
		if (shortcode_pattern.test(newshortcode.val()) == false) {
			$('.js-wpv-cs-error', parent_form).show();
		} else if ( $('.js-' + sanitized_val + '-item', parent_container).length > 0 ) {
			$('.js-wpv-cs-dup', parent_form).show();
		} else {
			var spinnerContainer = $('<div class="wpv-spinner ajax-loader">').insertAfter($(this)).show();
			thiz.removeClass('button-primary').addClass('button-secondary').prop('disabled', true);
			var data = {
				action: 'wpv_update_custom_conditional_functions',
				csaction: 'add',
				cstarget: newshortcode.val(),
				wpv_custom_conditional_functions_nonce: $('#wpv_custom_conditional_functions_nonce').val()
			};

			$.ajax({
				async:false,
				type:"POST",
				url:ajaxurl,
				data:data,
				success:function(response){
					if ( (typeof(response) !== 'undefined') ) {
						if (response == 'ok') {
							$('.js-wpv-add-item-settings-list', parent_container).append('<li class="js-' + sanitized_val + '-item"><span class="">' + newshortcode.val() + '</span> <i class="icon-remove-sign js-custom-function-delete" data-target="' + sanitized_val + '"></i></li>');
							newshortcode.val('');
						}
						else {
							$('.js-wpv-cs-ajaxfail', parent_form).show();
							console.log( "Error: WordPress AJAX returned ", response );
						}
					}
					else {
						$('.js-wpv-cs-ajaxfail', parent_form).show();
						console.log( "Error: AJAX returned ", response );
					}
				},
				error: function (ajaxContext) {
					$('.js-wpv-cs-ajaxfail', parent_form).show();
					console.log( "Error: ", ajaxContext.responseText );
				},
				complete: function() {
					spinnerContainer.remove();
				}
			});
		}
		return false;
	});
	
	// Delete custom conditional functions
	
	$(document).on('click', '.js-custom-function-delete', function(e){
		e.preventDefault();
		var thiz = $(this).data('target'),
		parent_container = $( this ).closest('.js-wpv-add-item-settings-wrapper'),
		spinnerContainer = $('<div class="wpv-spinner ajax-loader">').insertAfter($('.js-custom-conditional-function-add')).show();
		var data = {
			action: 'wpv_update_custom_conditional_functions',
			csaction: 'delete',
			cstarget: thiz,
			wpv_custom_conditional_functions_nonce: $('#wpv_custom_conditional_functions_nonce').val()
		};

		$.ajax({
			async:false,
			type:"POST",
			url:ajaxurl,
			data:data,
			success:function(response){
				if ( (typeof(response) !== 'undefined') ) {
					if (response == 'ok') {
						$( 'li.js-' + thiz + '-item', parent_container )
							.addClass( 'remove' )
							.fadeOut( 'fast', function() { 
								$( this ).remove(); 
							});
					}
					else {
						$('.js-wpv-cs-ajaxfail', parent_container).show();
						console.log( "Error: WordPress AJAX returned ", response );
					}
				}
				else {
					$('.js-wpv-cs-ajaxfail', parent_container).show();
					console.log( "Error: AJAX returned ", response );
				}
			},
			error: function (ajaxContext) {
				$('.js-wpv-cs-ajaxfail', parent_container).show();
				console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() {
				spinnerContainer.remove();
			}
		});

		return false;
	});

	// Maybe DEPRECATED ??
	$('.js-custom-inner-shortcodes-save').on('click', function(e) {
		e.preventDefault();

		var $spinner = $('.js-cis-spinner');
		var $thiz = $(this);

		$spinner.css('display','inline-block');
		$thiz
		.prop('disabled', true)
		.removeClass('button-primary')
		.addClass('button-secondary');

		var data = {
			action: 'wpv_save_custom_inner_shortcodes',
			wpv_custom_inner_shortcodes: $('.js-wpv-custom-inner-shortcodes').val(),
			wpv_custom_inner_shortcodes_nonce: $('#wpv_custom_inner_shortcodes_nonce').val()
		};

		$.ajax({
			async:false,
			type:"POST",
			url:ajaxurl,
			data:data,
			success:function(response){
				if ( (typeof(response) !== 'undefined') ) {
					if (response == 'ok') {
						$('.js-cis-update-message').show().fadeOut('slow');
					}
					else {
						console.log( "Error: WordPress AJAX returned ", response );
					}
				}
				else {
					console.log( "Error: AJAX returned ", response );
				}
			},
			error: function (ajaxContext) {
				console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() {
				$spinner.hide();
				$thiz
				.prop('disabled', false)
				.removeClass('button-secondary')
				.addClass('button-primary');
			}
		});

		return false;
	});

});

var WPViews = WPViews || {};

WPViews.ViewsSettingsScreen = function( $ ) {
	
	var self = this;
	
	/**
	* --------------------
	* Map plugin
	* --------------------
	*/
	
	self.map_plugin_state = ( $( '.js-wpv-map-plugin' ).length > 0 ) ? $( '.js-wpv-map-plugin' ).prop( 'checked' ) : false;
	
	$( '.js-wpv-map-plugin' ).on( 'change', function( e ){
		if ( self.map_plugin_state == $('.js-wpv-map-plugin').prop('checked') ) {
			$( '.js-wpv-map-plugin-settings-save' )
				.removeClass( 'button-primary' )
				.addClass( 'button-secondary' )
				.prop( 'disabled', true );
		} else {
			$( '.js-wpv-map-plugin-settings-save' )
				.addClass( 'button-primary' )
				.removeClass( 'button-secondary' )
				.prop( 'disabled', false );
		}
	});
	
	//Save Map plugin status
	$( '.js-wpv-map-plugin-settings-save' ).on( 'click', function( e ){
		e.preventDefault();
		var thiz = $( this ),
		spinnerContainer = $('<div class="wpv-spinner ajax-loader">').insertBefore( thiz ).show(),
		thiz_container = thiz.closest( '.js-wpv-setting-container' ),
		thiz_messages_container = thiz_container.find( '.js-wpv-messages' ),
		data = {
			action: 'wpv_update_map_plugin_status',
			status: $( '.js-wpv-map-plugin' ).prop( 'checked' ),
			wpnonce: $('#wpv_map_plugin_nonce').val()
		};

		$.ajax({
			async: false,
			type: "POST",
			dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( response.success ) {
					self.map_plugin_state = $( '.js-wpv-map-plugin' ).prop( 'checked' );
					thiz
						.removeClass( 'button-primary' )
						.addClass( 'button-secondary' )
						.prop( 'disabled', true );
					thiz_messages_container
						.wpvToolsetMessage({
							text: wpv_settings_texts.setting_saved,
							type: 'success',
							inline: true,
							stay: false
						});
				}
				else {
					//console.log( "Error: AJAX returned ", response );
				}
			},
			error: function (ajaxContext) {
				//console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() {
				spinnerContainer.remove();
			}
		});
	});
	
	/**
	* --------------------
	* Toolset Admin Bar Menu
	* --------------------
	*/
	
	self.toolset_admin_bar_menu_state = ( $( '#js-wpv-toolset-admin-bar-menu' ).length > 0 ) ? $( '#js-wpv-toolset-admin-bar-menu' ).prop( 'checked' ) : false;
	
	$( '#js-wpv-toolset-admin-bar-menu' ).on( 'change', function() {
		var thiz = $( this ),
		thiz_container = thiz.closest( '.js-wpv-setting-container' ),
		thiz_save_button = thiz_container.find( '.js-wpv-toolset-admin-bar-menu-settings-save' );
		if ( thiz.prop( 'checked' ) == self.toolset_admin_bar_menu_state ) {
			thiz_save_button
				.addClass( 'button-secondary' )
				.removeClass( 'button-primary' )
				.prop( 'disabled', true );
		} else {
			thiz_save_button
				.addClass( 'button-primary' )
				.removeClass( 'button-secondary' )
				.prop( 'disabled', false );
		}
	});
	
	$( '.js-wpv-toolset-admin-bar-menu-settings-save' ).on( 'click', function( e ) {
		e.preventDefault();
		var thiz = $( this ),
		spinnerContainer = $('<div class="wpv-spinner ajax-loader">').insertBefore( thiz ).show(),
		thiz_container = thiz.closest( '.js-wpv-setting-container' ),
		thiz_messages_container = thiz_container.find( '.js-wpv-messages' ),
		data = {
			action: 'wpv_update_toolset_admin_bar_menu_status',
			status: $( '#js-wpv-toolset-admin-bar-menu' ).prop( 'checked' ),
			wpnonce: $('#wpv_toolset_admin_bar_menu_nonce').val()
		};
		$.ajax({
            async: false,
            type: "POST",
			dataType: "json",
            url: ajaxurl,
            data: data,
            success: function( response ) {
				if ( response.success ) {
					self.toolset_admin_bar_menu_state = $( '#js-wpv-toolset-admin-bar-menu' ).prop( 'checked' );
					thiz
						.addClass( 'button-secondary' )
						.removeClass( 'button-primary' )
						.prop( 'disabled', true );
					thiz_messages_container
						.wpvToolsetMessage({
							text: wpv_settings_texts.setting_saved,
							type: 'success',
							inline: true,
							stay: false
						});
				}
            },
            error: function (ajaxContext) {
				//console.log( "Error: ", ajaxContext.responseText );
            },
            complete: function() {
				spinnerContainer.remove();
            }
        });
	});
	
	/**
	* --------------------
	* Bootstrap
	* --------------------
	*/
	
	self.bootstrap_version_state = ( $('.js-wpv-bootstrap-version:checked').length > 0 ) ? $('.js-wpv-bootstrap-version:checked').val() : false;
	
	$( '.js-wpv-bootstrap-version' ).on( 'change', function( e ) {
		if ( self.bootstrap_version_state == $( '.js-wpv-bootstrap-version:checked' ).val() ) {
			$( '.js-wpv-bootstrap-version-settings-save' )
				.removeClass( 'button-primary' )
				.addClass( 'button-secondary' )
				.prop( 'disabled', true );
		} else {
			$( '.js-wpv-bootstrap-version-settings-save' )
				.addClass( 'button-primary' )
				.removeClass( 'button-secondary' )
				.prop( 'disabled', false );
		}
	});
	
	$( '.js-wpv-bootstrap-version-settings-save' ).on( 'click', function( e ) {
		e.preventDefault();
		var thiz = $( this ),
		spinnerContainer = $('<div class="wpv-spinner ajax-loader">').insertBefore( thiz ).show(),
		thiz_container = thiz.closest( '.js-wpv-setting-container' ),
		thiz_messages_container = thiz_container.find( '.js-wpv-messages' ),
		data = {
			action: 'wpv_update_bootstrap_version_status',
			status: $('.js-wpv-bootstrap-version:checked').val(),
			wpnonce: $('#wpv_bootstrap_version_nonce').val()
		};

		$.ajax({
			async: false,
			type: "POST",
			dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( response.success ) {
					self.bootstrap_version_state = $('.js-wpv-bootstrap-version:checked').val();
					thiz
						.addClass( 'button-secondary' )
						.removeClass( 'button-primary' )
						.prop( 'disabled', true );
					thiz_messages_container
						.wpvToolsetMessage({
							text: wpv_settings_texts.setting_saved,
							type: 'success',
							inline: true,
							stay: false
						});
				}
			},
			error: function (ajaxContext) {
				//console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() {
				spinnerContainer.remove();
			}
		});
	});
	
	/**
	* --------------------
	* WPML
	* --------------------
	*/
	
	self.wpml_translation_settings = ( $( '.js-wpv-content-template-translation:checked' ).length > 0 ) ? $( '.js-wpv-content-template-translation:checked' ).val() : 0;
	
	$( document ).on( 'change', '.js-wpv-content-template-translation', function() {
		if ( self.wpml_translation_settings == $( '.js-wpv-content-template-translation:checked' ).val() ) {
			$( '.js-wpv-save-wpml-settings' )
				.removeClass( 'button-primary' )
				.addClass( 'button-secondary' )
				.prop( 'disabled', true );
		} else {
			$( '.js-wpv-save-wpml-settings' )
				.addClass( 'button-primary' )
				.removeClass( 'button-secondary' )
				.prop( 'disabled', false );
		}
	});

    $( '.js-wpv-save-wpml-settings' ).on( 'click', function( e ) {
        e.preventDefault();
		var thiz = $( this ),
		spinnerContainer = $('<div class="wpv-spinner ajax-loader">').insertBefore( thiz ).show(),
		thiz_container = thiz.closest( '.js-wpv-setting-container' ),
		thiz_messages_container = thiz_container.find( '.js-wpv-messages' ),
		data = {
			action: 'wpv_update_wpml_settings',
			status: $( '.js-wpv-content-template-translation:checked' ).val(),
			wpnonce: $('#wpv_wpml_settings_nonce').val()
		};

        $.ajax({
            async: false,
            type: "POST",
			dataType: "json",
            url: ajaxurl,
            data: data,
            success: function( response ) {
                if ( response.success ) {
					self.wpml_translation_settings = $( '.js-wpv-content-template-translation:checked' ).val();
					thiz
						.addClass( 'button-secondary' )
						.removeClass( 'button-primary' )
						.prop( 'disabled', true );
					thiz_messages_container
						.wpvToolsetMessage({
							text: wpv_settings_texts.setting_saved,
							type: 'success',
							inline: true,
							stay: false
						});
                }
            },
            error: function ( ajaxContext ) {
             //   console.log( "Error: ", ajaxContext.responseText );
            },
            complete: function() {
                spinnerContainer.hide();
            }
        });

        return false;
    });
	
	/**
	* --------------------
	* Frontend edit links
	* --------------------
	*/
	
	self.show_edit_view_link_state = ( $( '.js-wpv-show-edit-view-link' ).length > 0 ) ? $( '.js-wpv-show-edit-view-link' ).prop( 'checked' ) : false;
	
	$( '.js-wpv-show-edit-view-link' ).on( 'change', function( e ) {
		if ( self.show_edit_view_link_state == $( '.js-wpv-show-edit-view-link' ).prop( 'checked' ) ) {
			$( '.js-wpv-show-edit-view-link-settings-save' )
				.removeClass( 'button-primary' )
				.addClass( 'button-secondary' )
				.prop( 'disabled', true );
		} else {
			$( '.js-wpv-show-edit-view-link-settings-save' )
				.addClass( 'button-primary' )
				.removeClass( 'button-secondary' )
				.prop( 'disabled', false );
		}
	});
	
	$( '.js-wpv-show-edit-view-link-settings-save' ).on( 'click', function( e ) {
		e.preventDefault();
		var thiz = $( this ),
		spinnerContainer = $('<div class="wpv-spinner ajax-loader">').insertBefore( thiz ).show();
		thiz_container = thiz.closest( '.js-wpv-setting-container' ),
		thiz_messages_container = thiz_container.find( '.js-wpv-messages' ),
		data = {
			action: 'wpv_update_show_edit_view_link_status',
			status: $( '.js-wpv-show-edit-view-link' ).prop( 'checked' ),
			wpnonce: $('#wpv_show_edit_view_link_nonce').val()
		};

		$.ajax({
			async: false,
			type: "POST",
			dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( response.success ) {
					self.show_edit_view_link_state = $( '.js-wpv-show-edit-view-link' ).prop( 'checked' );
					thiz
						.addClass( 'button-secondary' )
						.removeClass( 'button-primary' )
						.prop( 'disabled', true );
					thiz_messages_container
						.wpvToolsetMessage({
							text: wpv_settings_texts.setting_saved,
							type: 'success',
							inline: true,
							stay: false
						});
				}
			},
			error: function (ajaxContext) {
				//console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() {
				spinnerContainer.remove();
			}
		});
		return false;
	});
	
	/**
	* --------------------
	* Theme debug
	* --------------------
	*/
	
	self.theme_debug_settings = $( '.js-debug-settings-form :input' ).serialize();
	
	$( document ).on( 'change cut click paste keyup', '.js-debug-settings-form :input', function() {
		if ( self.theme_debug_settings == $( '.js-debug-settings-form :input' ).serialize() ) {
			$( '.js-wpv-save-theme-debug-settings' )
				.removeClass( 'button-primary' )
				.addClass( 'button-secondary' )
				.prop( 'disabled', true );
		} else {
			$( '.js-wpv-save-theme-debug-settings' )
				.addClass( 'button-primary' )
				.removeClass( 'button-secondary' )
				.prop( 'disabled', false );
		}
	});
	
    $( '.js-wpv-save-theme-debug-settings' ).on( 'click', function( e ) {
        e.preventDefault();
		var thiz = $( this ),
		spinnerContainer = $('<div class="wpv-spinner ajax-loader">').insertBefore( thiz ).show(),
		thiz_container = thiz.closest( '.js-wpv-setting-container' ),
		thiz_messages_container = thiz_container.find( '.js-wpv-messages' ),
		data = {
			action: 'wpv_save_theme_debug_settings',
			wpv_theme_function: $( '.js-wpv-debug-theme-function' ).val(),
			wpv_theme_function_debug: $( '.js-wpv-debug-theme-function-enable-debug' ).prop( 'checked' ),
			wpnonce: $('#wpv_view_templates_theme_support').val()
		};

        $.ajax({
            async: false,
            type: "POST",
            url: ajaxurl,
            data: data,
            success: function( response ) {
                if ( response.success ) {
                    self.theme_debug_settings = $('.js-debug-settings-form :input').serialize();
					thiz
						.addClass( 'button-secondary' )
						.removeClass( 'button-primary' )
						.prop( 'disabled', true );
					thiz_messages_container
						.wpvToolsetMessage({
							text: wpv_settings_texts.setting_saved,
							type: 'success',
							inline: true,
							stay: false
						});
                }
            },
            error: function ( ajaxContext ) {
				//console.log( "Error: ", ajaxContext.responseText );
            },
            complete: function() {
				spinnerContainer.remove();
            }
        });

        return false;
    });
	
	/**
	* --------------------
	* Debug
	* --------------------
	*/
	
	self.debug_mode_state = $('.js-debug-mode-form input').serialize();
	
	$( '.js-wpv-debug-mode, .js-wpv-debug-mode-type' ).on( 'change', function( e ) {
		if ( $( '.js-wpv-debug-mode' ).prop( 'checked' ) ) {
			$( '.js-wpv-debug-additional-options' ).fadeIn( 'fast' );
		} else {
			$( '.js-wpv-debug-additional-options' ).hide();
		}
		if ( self.debug_mode_state == $('.js-debug-mode-form input').serialize() ) {
			$( '.js-wpv-save-debug-mode-settings' )
				.removeClass( 'button-primary' )
				.addClass( 'button-secondary' )
				.prop( 'disabled', true );
			$( '.js-wpv-debug-checker' ).show();
		} else {
			$( '.js-wpv-save-debug-mode-settings' )
				.addClass( 'button-primary' )
				.removeClass( 'button-secondary' )
				.prop( 'disabled', false );
			$( '.js-wpv-debug-checker' ).hide();
		}
	});
	
	$( '.js-wpv-save-debug-mode-settings' ).on( 'click', function( e ) {
		e.preventDefault();
		var thiz = $( this ),
		spinnerContainer = $('<div class="wpv-spinner ajax-loader">').insertBefore( thiz ).show(),
		thiz_container = thiz.closest( '.js-wpv-setting-container' ),
		thiz_messages_container = thiz_container.find( '.js-wpv-messages' ),
		data = {
			action: 'wpv_update_debug_mode_status',
			debug_status: ( $( '.js-wpv-debug-mode' ).prop( 'checked' ) ) ? 1 : 0,
			debug_mode_type: $( 'input[name=wpv_debug_mode_type]:radio:checked' ).val(),
			wpnonce: $('#wpv_debug_tool_nonce').val()
		};
		$( '.js-debug-mode-update-message' ).hide();

		$.ajax({
			async: false,
			type: "POST",
			dataType: "json",
			url: ajaxurl,
			data: data,
			success: function( response ) {
				if ( response.success ) {
					self.debug_mode_state = $('.js-debug-mode-form input').serialize();
					thiz
						.addClass( 'button-secondary' )
						.removeClass( 'button-primary' )
						.prop( 'disabled', true );
					thiz_messages_container
						.wpvToolsetMessage({
							text: wpv_settings_texts.setting_saved,
							type: 'success',
							inline: true,
							stay: false
						});
					if ( $( '.js-wpv-debug-mode' ).prop( 'checked' ) ) {
						$( '.js-wpv-debug-checker' ).show();
						if ( ! $( '.js-wpv-debug-checker-enabler' ).is( ':visible' ) ) {
							$( '.js-wpv-debug-checker-before, .js-wpv-debug-checker-actions' ).show();
							$( '.js-wpv-debug-checker-results' ).hide();
						}
					}
				}
			},
			error: function (ajaxContext) {
				//console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() {
				spinnerContainer.remove();
			}
		});
		return false;
	});
	
	$( document ).on( 'click', '.js-wpv-debug-checker-action', function() {
		var target = $( this ).data( 'target' );
		window.location = target;
	});
	
	$( document ).on( 'click', '.js-wpv-debug-checker-dismiss', function( e ) {
		e.preventDefault();
		var thiz = $( this ),
		spinnerContainer = $('<div class="wpv-spinner ajax-loader">').insertAfter( thiz ).show(),
		data = {
			action: 'wpv_switch_debug_check',
			result: 'dismiss',
			wpnonce: $('#wpv_debug_tool_nonce').val()
		};
		$.ajax({
            async: false,
            type: "POST",
			dataType: "json",
            url: ajaxurl,
            data: data,
            success: function( response ) {
                if ( response.success ) {
					$( '.js-wpv-debug-checker-results, .js-wpv-debug-checker-after, .js-wpv-debug-checker-before, .js-wpv-debug-checker-actions' ).hide();
					$( '.js-wpv-debug-checker-enabler' ).show();
                }
            },
            error: function (ajaxContext) {
				//console.log( "Error: ", ajaxContext.responseText );
            },
            complete: function() {
				spinnerContainer.remove();
            }
        });
	});
	
	$( document ).on( 'click', '.js-wpv-debug-checker-recover', function( e ) {
		e.preventDefault();
		var thiz = $( this ),
		spinnerContainer = $('<div class="wpv-spinner ajax-loader">').insertAfter( thiz ).show(),
		data = {
			action: 'wpv_switch_debug_check',
			result: 'recover',
			wpnonce: $('#wpv_debug_tool_nonce').val()
		};
		$.ajax({
            async: false,
            type: "POST",
			dataType: "json",
            url: ajaxurl,
            data: data,
            success: function( response ) {
                if ( response.success ) {
					$( '.js-wpv-debug-checker-results, .js-wpv-debug-checker-after, .js-wpv-debug-checker-enabler' ).hide();
					$( '.js-wpv-debug-checker-before, .js-wpv-debug-checker-actions' ).show();
                }
            },
            error: function (ajaxContext) {
				//console.log( "Error: ", ajaxContext.responseText );
            },
            complete: function() {
				spinnerContainer.remove();
            }
        });
	});
	
	$( document ).on( 'click', '.js-wpv-debug-checker-success', function( e ) {
		e.preventDefault();
		$( '.js-wpv-debug-checker-results' ).addClass( 'hidden' );
		$( '.js-wpv-debug-checker-message-success' ).fadeIn( 'fast', function() {
			$( '.js-wpv-debug-checker-dismiss' ).click();
		});
	});
	
	$( document ).on( 'click', '.js-wpv-debug-checker-failure', function( e ) {
		e.preventDefault();
		$( '.js-wpv-debug-checker-results' ).addClass( 'hidden' );
		$( '.js-wpv-debug-checker-message-failure' ).fadeIn( 'fast' );
		$( '.js-wpv-debug-checker-actions' ).fadeIn( 'fast' );
	});
	
	self.init = function() {
		
	};
	
	self.init();

};

jQuery( document ).ready( function( $ ) {
    WPViews.views_settings_screen = new WPViews.ViewsSettingsScreen( $ );
    if( /#toolset-admin-bar-settings$/.test( window.location.href ) ) {
        $( '#toolset-admin-bar-settings' ).parent().css( 'background-color', '#ffffca' );
    }
});