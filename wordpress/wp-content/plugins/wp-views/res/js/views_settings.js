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

    // FIXME: Nonce required!
    // TODO: Add ajax errors handling
	
	var wpv_theme_debug_settings = $('.js-debug-settings-form :input').serialize();
	
	$(document).on('change cut click paste keyup', '.js-debug-settings-form :input', function() {
		if ( wpv_theme_debug_settings == $('.js-debug-settings-form :input').serialize() ) {
			$('.js-save-debug-settings').removeClass('button-primary').addClass('button-secondary').prop('disabled', true);
		} else {
			$('.js-save-debug-settings').addClass('button-primary').removeClass('button-secondary').prop('disabled', false);
		}
	});
	

    // Save debug options
    $('.js-save-debug-settings').on('click', function(e) {
        e.preventDefault();

        var $spinner = $('.js-debug-spinner');
        var data;
        var $thiz = $(this);

        $spinner.css('display','inline-block');
        $thiz
            .prop('disabled', true)
            .removeClass('button-primary')
            .addClass('button-secondary');
		wpv_theme_debug_settings = $('.js-debug-settings-form :input').serialize();

        data = $('.js-debug-settings-form :input').serialize();
        data += '&action=wpv_save_theme_debug_settings';

        $.ajax({
            async:false,
            type:"POST",
            url:ajaxurl,
            data:data,
            success:function(response){
                if ( (typeof(response) !== 'undefined') ) {
                    if (response == 'ok') {
                        $('.js-debug-update-message').fadeIn('fast',function(){
							$(this).delay(1000).fadeOut('fast');
						});
                    }
                    else {
                        //console.log( "Error: WordPress AJAX returned ", response );
                    }
                }
                else {
                    //console.log( "Error: AJAX returned ", response );
                }
            },
            error: function (ajaxContext) {
				//console.log( "Error: ", ajaxContext.responseText );
            },
            complete: function() {
				$spinner.hide();
            }
        });

        return false;
    });
	
	var wpv_wpml_settings = $('.js-wpml-settings-form :input').serialize();
	
	$(document).on('change cut click paste keyup', '.js-wpml-settings-form :input', function() {
		if ( wpv_wpml_settings == $('.js-wpml-settings-form :input').serialize() ) {
			$('.js-save-wpml-settings').removeClass('button-primary').addClass('button-secondary').prop('disabled', true);
		} else {
			$('.js-save-wpml-settings').addClass('button-primary').removeClass('button-secondary').prop('disabled', false);
		}
	});

    // Save WPML options
    $('.js-save-wpml-settings').on('click', function(e) {
        e.preventDefault();

        var $spinner = $('.js-wpml-spinner');
        var data;
        var $thiz = $(this);

        $spinner.css('display','inline-block');
        $thiz
            .prop('disabled', true)
            .removeClass('button-primary')
            .addClass('button-secondary');
		wpv_wpml_settings = $('.js-wpml-settings-form :input').serialize();

        data = $('.js-wpml-settings-form :input').serialize();
        data += '&action=wpv_save_wpml_settings';

        $.ajax({
            async:false,
            type:"POST",
            url:ajaxurl,
            data:data,
            success:function(response){
                if ( (typeof(response) !== 'undefined') ) {
                    if (response == 'ok') {
                        $('.js-wpml-update-message').fadeIn('fast',function(){
							$(this).delay(1000).fadeOut('fast');
						});
                    }
                    else {
                       // console.log( "Error: WordPress AJAX returned ", response );
                    }
                }
                else {
                    //console.log( "Error: AJAX returned ", response );
                }
            },
            error: function (ajaxContext) {
             //   console.log( "Error: ", ajaxContext.responseText );
            },
            complete: function() {
                $spinner.hide();
            }
        });

        return false;
    });

    // Save custom inner shortcodes options

	$(document).on('keyup input cut paste', '.js-custom-inner-shortcode-newname', function(){
		$('.js-wpv-cs-error, .js-wpv-cs-dup, .js-wpv-cs-ajaxfail').hide();
		if ( $(this).val() != '' ) {
			$('.js-custom-inner-shortcodes-add').addClass('button-primary').removeClass('button-secondary').prop('disabled', false);
		} else {
			$('.js-custom-inner-shortcodes-add').removeClass('button-primary').addClass('button-secondary').prop('disabled', true);
		}
	});

	$('.js-custom-inner-shortcodes-form-add').submit(function(e){
		e.preventDefault();
		$('.js-custom-inner-shortcodes-add').click();
		return false;
	});

	$('.js-custom-inner-shortcodes-add').on('click', function(e){
		e.preventDefault();
		$('.js-wpv-cs-error, .js-wpv-cs-dup, .js-wpv-cs-ajaxfail').hide();
		var thiz = $(this),
		newshortcode = $('.js-custom-inner-shortcode-newname'),
		shortcode_pattern = /^[a-z0-9\-\_]+$/;
		if (shortcode_pattern.test(newshortcode.val()) == false) {
			$('.js-wpv-cs-error').show();
		} else if ( $('.js-' + newshortcode.val() + '-item').length > 0 ) {
			$('.js-wpv-cs-dup').show();
		} else {
			var spinnerContainer = $('<div class="spinner ajax-loader">').insertAfter($(this)).show();
			$('.js-custom-inner-shortcodes-add').removeClass('button-primary').addClass('button-secondary').prop('disabled', true);
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
							$('.js-custom-shortcode-list').append('<li class="js-' + newshortcode.val() + '-item"><span class="">[' + newshortcode.val() + ']</span> <i class="icon-remove-sign js-custom-shortcode-delete" data-target="' + newshortcode.val() + '"></i></li>');
							newshortcode.val('');
						}
						else {
							$('.js-wpv-cs-ajaxfail').show();
							console.log( "Error: WordPress AJAX returned ", response );
						}
					}
					else {
						$('.js-wpv-cs-ajaxfail').show();
						console.log( "Error: AJAX returned ", response );
					}
				},
				error: function (ajaxContext) {
					$('.js-wpv-cs-ajaxfail').show();
					console.log( "Error: ", ajaxContext.responseText );
				},
				complete: function() {
					spinnerContainer.remove();
				}
			});
		}
		return false;
	});

	$(document).on('click', '.js-custom-shortcode-delete', function(e){
		e.preventDefault();
		var thiz = $(this).data('target'),
		spinnerContainer = $('<div class="spinner ajax-loader">').insertAfter($('.js-custom-inner-shortcodes-add')).show();
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
						$('li.js-' + thiz + '-item').fadeOut( 'fast', function() { $(this).remove(); });
					}
					else {
						$('.js-wpv-cs-ajaxfail').show();
						console.log( "Error: WordPress AJAX returned ", response );
					}
				}
				else {
					$('.js-wpv-cs-ajaxfail').show();
					console.log( "Error: AJAX returned ", response );
				}
			},
			error: function (ajaxContext) {
				$('.js-wpv-cs-ajaxfail').show();
				console.log( "Error: ", ajaxContext.responseText );
			},
			complete: function() {
				spinnerContainer.remove();
			}
		});

		return false;
	});

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
	
	var wpv_map_plugin_state = $('.js-wpv-map-plugin').prop('checked');
	
	$('.js-wpv-map-plugin').on('change', function(e){
		if ( wpv_map_plugin_state == $('.js-wpv-map-plugin').prop('checked') ) {
			$('.js-wpv-map-plugin-settings-save').removeClass('button-primary').addClass('button-secondary').prop('disabled', true);
		} else {
			$('.js-wpv-map-plugin-settings-save').addClass('button-primary').removeClass('button-secondary').prop('disabled', false);
		}
	});
	
	//Save Map plugin status
	$('.js-wpv-map-plugin-settings-save').on('click', function(e){
		e.preventDefault();
		var thiz = $(this);
		wpv_map_plugin_status = '';
		if( $('.js-wpv-map-plugin').prop('checked') ){
			wpv_map_plugin_status = 1; 
		}
		wpv_map_plugin_state = $('.js-wpv-map-plugin').prop('checked');
			var spinnerContainer = $('<div class="spinner ajax-loader">').insertBefore($(this)).show();
			var data = {
				action: 'wpv_update_map_plugin_status',
				wpv_map_plugin_status: wpv_map_plugin_status,
				wpv_map_plugin_nonce: $('#wpv_map_plugin_nonce').val()
			};

			$.ajax({
				async:false,
				type:"POST",
				url:ajaxurl,
				data:data,
				success:function(response){
					if ( (typeof(response) !== 'undefined') ) {
						if (response == 'ok') {
							$('.js-wpv-map-plugin-update-message').fadeIn('fast',function(){
								$(this).delay(1000).fadeOut('fast');
							});
							$('.js-wpv-map-plugin-settings-save').removeClass('button-primary').addClass('button-secondary').prop('disabled', true);
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
					spinnerContainer.remove();
				}
			});
		return false;
	});
	
	//'Edit view' links
	var wpv_show_edit_view_link_state = $('.js-wpv-show-edit-view-link').prop('checked');
	
	$('.js-wpv-show-edit-view-link').on('change', function(e){
		if ( wpv_show_edit_view_link_state == $('.js-wpv-show-edit-view-link').prop('checked') ) {
			$('.js-wpv-show-edit-view-link-settings-save').removeClass('button-primary').addClass('button-secondary').prop('disabled', true);
		} else {
			$('.js-wpv-show-edit-view-link-settings-save').addClass('button-primary').removeClass('button-secondary').prop('disabled', false);
		}
	});
	
	//Save 'Edit view' links option status
	$('.js-wpv-show-edit-view-link-settings-save').on('click', function(e){
		e.preventDefault();
		var thiz = $(this);
		
			var spinnerContainer = $('<div class="spinner ajax-loader">').insertBefore($(this)).show();
			wpv_show_edit_view_link_state = $('.js-wpv-show-edit-view-link').prop('checked');
			var status = 0;
			if ( wpv_show_edit_view_link_state === true ){
				status = 1;	
			}
			var data = {
				action: 'wpv_update_show_edit_view_link_status',
				wpv_show_edit_view_link_status: status,
				wpv_show_edit_view_link_nonce: $('#wpv_show_edit_view_link_nonce').val()
			};

			$.ajax({
				async:false,
				type:"POST",
				url:ajaxurl,
				data:data,
				success:function(response){
					if ( (typeof(response) !== 'undefined') ) {
						if (response == 'ok') {
							$('.js-wpv-show-edit-view-link-update-message').fadeIn('fast',function(){
								$(this).delay(1000).fadeOut('fast');
							});
							$('.js-wpv-show-edit-view-link-settings-save').removeClass('button-primary').addClass('button-secondary').prop('disabled', true);
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
					spinnerContainer.remove();
				}
			});
		return false;
	});
	
	//Bootstrap version
	var wpv_bootstrap_version_state = $('.js-wpv-bootstrap-version:checked').val();
	
	$('.js-wpv-bootstrap-version').on('change', function(e){
		if ( wpv_bootstrap_version_state == $('.js-wpv-bootstrap-version:checked').val() ) {
			$('.js-wpv-bootstrap-version-settings-save').removeClass('button-primary').addClass('button-secondary').prop('disabled', true);
		} else {
			$('.js-wpv-bootstrap-version-settings-save').addClass('button-primary').removeClass('button-secondary').prop('disabled', false);
		}
	});
	
	//Save bootstrap version status
	$('.js-wpv-bootstrap-version-settings-save').on('click', function(e){
		e.preventDefault();
		var thiz = $(this);
		
			var spinnerContainer = $('<div class="spinner ajax-loader">').insertBefore($(this)).show();
			wpv_bootstrap_version_state = $('.js-wpv-bootstrap-version:checked').val();
			
			var data = {
				action: 'wpv_update_bootstrap_version_status',
				wpv_bootstrap_version_status: $('.js-wpv-bootstrap-version:checked').val(),
				wpv_bootstrap_version_nonce: $('#wpv_bootstrap_version_nonce').val()
			};

			$.ajax({
				async:false,
				type:"POST",
				url:ajaxurl,
				data:data,
				success:function(response){
					if ( (typeof(response) !== 'undefined') ) {
						if (response == 'ok') {
							$('.js-wpv-bootstrap-version-update-message').fadeIn('fast',function(){
								$(this).delay(1000).fadeOut('fast');
							});
							$('.js-wpv-bootstrap-version-settings-save').removeClass('button-primary').addClass('button-secondary').prop('disabled', true);
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
					spinnerContainer.remove();
				}
			});
		return false;
	});
	
	var wpv_debug_mode_state = $('.js-debug-mode-form input').serialize();
	
	$('.js-wpv-debug-mode, .js-wpv-debug-mode-type').on('change', function(e){
		if( $('.js-wpv-debug-mode').prop('checked') ){
			$('.js-wpv-debug-additional-options').fadeIn('fast');
		}
		else{
			$('.js-wpv-debug-additional-options').hide();
		}
		if ( wpv_debug_mode_state == $('.js-debug-mode-form input').serialize() ) {
			$('.js-save-debug-mode-settings').removeClass('button-primary').addClass('button-secondary').prop('disabled', true);
			$('.js-wpv-debug-checker').show();
		} else {
			$('.js-save-debug-mode-settings').addClass('button-primary').removeClass('button-secondary').prop('disabled', false);
			$('.js-wpv-debug-checker').hide();
		}
	});
	
	//Save Debug mode status
	$('.js-save-debug-mode-settings').on('click', function(e){
		e.preventDefault();
		$('.js-debug-mode-update-message').hide();
		var thiz = $(this);
		debug_status = '';
		if( $('.js-wpv-debug-mode').prop('checked') ){
			debug_status = 1; 
		}
		wpv_debug_mode_state = $('.js-debug-mode-form input').serialize();
			var spinnerContainer = $('<div class="spinner ajax-loader">').insertBefore($(this)).show();
			var data = {
				action: 'wpv_update_debug_mode_status',
				debug_status: debug_status,
				wpv_dembug_mode_type: $('input[name=wpv-debug-mode-type]:radio:checked').val(),
				wpv_debug_mode_option: $('#wpv_debug_mode_option').val()
				
			};

			$.ajax({
				async:false,
				type:"POST",
				url:ajaxurl,
				data:data,
				success:function(response){
					if ( (typeof(response) !== 'undefined') ) {
						if (response == 'ok') {
							$('.js-debug-mode-update-message').fadeIn('fast',function(){
								$(this).delay(1000).fadeOut('fast');
							});
							$('.js-save-debug-mode-settings').removeClass('button-primary').addClass('button-secondary').prop('disabled', true);
							if( $('.js-wpv-debug-mode').prop('checked') ){
								$('.js-wpv-debug-checker').show();
								if ( ! $('.js-wpv-debug-checker-enabler').is(':visible') ) {
									$('.js-wpv-debug-checker-before').show();
									$('.js-wpv-debug-checker-actions').show();
									$('.js-wpv-debug-checker-results').hide();
								}
							}
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
					spinnerContainer.remove();
				}
			});
		return false;
	});
	
	$(document).on('click', '.js-wpv-debug-checker-action', function() {
		var target = $(this).data('target');
		window.location = target;
	});
	
	$(document).on('click', '.js-wpv-debug-checker-dismiss', function(e) {
		e.preventDefault();
		var data = {
			action: 'wpv_switch_debug_check',
			result: 'dismiss',
			wpnonce: $('#wpv_debug_mode_option').val()
		};
		var spinnerContainer = $('<div class="spinner ajax-loader">').insertAfter($(this)).show();
		$.ajax({
            async:false,
            type:"POST",
            url:ajaxurl,
            data:data,
            success:function(response){
                if ( (typeof(response) !== 'undefined') ) {
                    if (response == 'ok') {
						$('.js-wpv-debug-checker-results').hide();
						$('.js-wpv-debug-checker-after').hide();
						$('.js-wpv-debug-checker-before').hide();
						$('.js-wpv-debug-checker-actions').hide();
						$('.js-wpv-debug-checker-enabler').show();
                    }
                    else {
                        //console.log( "Error: WordPress AJAX returned ", response );
                    }
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
	
	$(document).on('click', '.js-wpv-debug-checker-recover', function(e) {
		e.preventDefault();
		var data = {
			action: 'wpv_switch_debug_check',
			result: 'recover',
			wpnonce: $('#wpv_debug_mode_option').val()
		};
		var spinnerContainer = $('<div class="spinner ajax-loader">').insertAfter($(this)).show();
		$.ajax({
            async:false,
            type:"POST",
            url:ajaxurl,
            data:data,
            success:function(response){
                if ( (typeof(response) !== 'undefined') ) {
                    if (response == 'ok') {
						$('.js-wpv-debug-checker-results').hide();
						$('.js-wpv-debug-checker-after').hide();
						$('.js-wpv-debug-checker-before').show();
						$('.js-wpv-debug-checker-actions').show();
						$('.js-wpv-debug-checker-enabler').hide();
                    }
                    else {
                        //console.log( "Error: WordPress AJAX returned ", response );
                    }
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
	
	$(document).on('click', '.js-wpv-debug-checker-success', function(e) {
		e.preventDefault();
		$('.js-wpv-debug-checker-results').addClass('hidden');
		$('.js-wpv-debug-checker-message-success').fadeIn('fast', function(){
			$('.js-wpv-debug-checker-dismiss').click();
		});
	});
	
	$(document).on('click', '.js-wpv-debug-checker-failure', function(e) {
		e.preventDefault();
		$('.js-wpv-debug-checker-results').addClass('hidden');
		$('.js-wpv-debug-checker-message-failure').fadeIn('fast');
		$('.js-wpv-debug-checker-actions').fadeIn('fast');
	});

});