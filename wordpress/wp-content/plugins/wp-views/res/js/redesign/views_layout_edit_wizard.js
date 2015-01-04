var wpv_wizard_popup, wpv_wizard_popup_item, wpv_wizard_popup_item_parent, wpv_wizard_popup_style, wpv_all_selected; // TODO please review wpv_ prefixes: they whoud be wpv_

// Settings to remember the state of the wizard for later use.
var wpv_layout_settings_from_wizard = null;
var wpv_layout_wizard_inital_settings = null;
var wpv_layout_wizard_add_field_ui = null;

jQuery(document).ready(function($){

	// Load the initial settings on document ready so they are available
	// straight away for the layout wizard.
	wpv_layout_wizard_fetch_inital_settings();
	
	$('.js-open-meta-html-wizard').on('click',function() {

		if (wpv_layout_wizard_inital_settings) {
			// We have a previous setting that we can use.
			show_layout_wizard_popup(wpv_layout_wizard_inital_settings);
		} else {
			// fetch and load the popup via ajax.
			var data = {
				action: 'wpv_layout_wizard',
				view_id: $('#post_ID').val()
			};
			$.post(ajaxurl, data, function(response){
	
				if (response.length <=0) {
					$('.js-wpv-settings-layout-extra .js-code-editor-toolbar:first').wpvToolsetMessage({
						text: $('.js-wvp-wizard-loc').data('loc-error2'),
						type:'error',
						inline:true,
						stay:false
					});
					return;
				}
	
				// save intial dialog settings so we can re-use.
				wpv_layout_wizard_inital_settings = $.parseJSON(response);
				
				show_layout_wizard_popup(wpv_layout_wizard_inital_settings);
				
			});
		}
	});

	function wpv_layout_wizard_fetch_inital_settings() {
		var data = {
				action: 'wpv_layout_wizard',
				view_id: $('#post_ID').val()
			};
		$.post(ajaxurl, data, function(response){

			if (response.length <=0) {
				return;
			}

			// save intial dialog settings so we can re-use.
			wpv_layout_wizard_inital_settings = $.parseJSON(response);
		});
	}

	function show_layout_wizard_popup(response) {
		
		$.colorbox({
			html: response.dialog,
			onComplete: function() {
				$('.js-insert-layout').addClass('button-secondary').removeClass('button-primary');
				$(document).on('mousedown','.js-wpv-dialog-layout-wizard, #cboxOverlay',function(e){
					if ( $(e.target).parents('.js-select2').length === 0 ) {
						$('select.js-select2').select2('close');
					}
				});
				
				$(document).on('select2-opening','.js-select2',function(e){
					$('select.js-select2').select2('close');
				});

				$('.js-layout-wizard-tab').not(':first-child').hide();
				
				data = wpv_merge_with_local_copy(response.settings);

				if (typeof(data.style) != 'undefined') {
					$('input[value="'+data.style+'"]').click();
				}

				if (typeof(data.table_cols) != 'undefined') {
					$('select[name="table_cols"]').val(data.table_cols);
				}
				
				/*if (typeof(data.bootstrap_grid_style) != 'undefined') {
					$('select[name="bootstrap_grid_style"]').val(data.bootstrap_grid_style);
				}
				
				if (typeof(data.bootstrap_grid_cols_width) != 'undefined') {
					$('select[name="bootstrap_grid_cols_width"]').val(data.bootstrap_grid_cols_width);
				}
				*/
				
				if (typeof(data.wpv_bootstrap_version) != 'undefined') {
					if ( data.wpv_bootstrap_version == '1' ) {
						$( '#layout-wizard-style-bootstrap-grid' ).attr( 'disabled', true );
						$( 'label[for=layout-wizard-style-bootstrap-grid]' ).css({ opacity: 0.5 });
						$( '.js-wpv-bootstrap-disabled' ).show();
					} else {
						$( '#layout-wizard-style-bootstrap-grid' ).attr( 'disabled', false );
						$( 'label[for=layout-wizard-style-bootstrap-grid]' ).css({ opacity: 1 });
						$( '.js-wpv-bootstrap-disabled' ).hide();
					}
				}
				
				grid_options = '';
				$('[name="bootstrap_grid_cols"] option').remove();
				grid_options	+= '<option value="1">1</option>';
				grid_options	+= '<option value="2">2</option>';
				grid_options	+= '<option value="3">3</option>';
				grid_options	+= '<option value="4">4</option>';
				grid_options	+= '<option value="6">6</option>';
				grid_options	+= '<option value="12">12</option>';
				$('[name="bootstrap_grid_cols"]').append(grid_options);
				if (typeof(data.bootstrap_grid_cols) != 'undefined') {
					$('select[name="bootstrap_grid_cols"]').val(data.bootstrap_grid_cols);
				}
				
				if (typeof(data.bootstrap_grid_container) != 'undefined' && data.bootstrap_grid_container === 'true' ) {
					$('input[name="bootstrap_grid_container"]').prop('checked',true);
				}
				$('#bootstrap_grid_individual_yes').prop('checked',true);
				if (typeof(data.bootstrap_grid_individual) != 'undefined' ) {
					$('input[name="bootstrap_grid_individual"]').prop('checked',false);
					if ( data.bootstrap_grid_individual == ''){
						$('#bootstrap_grid_individual_yes').prop('checked',true);
					}else{
						$('#bootstrap_grid_individual_no').prop('checked',true);	
					}
					
				}

				if (typeof(data.insert_at) != 'undefined') {
					$('input[value="'+data.insert_at+'"]').attr('checked', true);
				} else {
					$('input[value="insert_replace"]').attr('checked', true);
				}

				$('input[name="include_field_names"]').attr('checked', data.include_field_names === 'true');

				if (typeof(data.fields) != 'undefined') {
					
					var ii=0, vcount=0;
					var flist = [];
					for(s in data.fields) {
						flist[ii] = data.fields[s];
						ii++;
						
						if (ii % 6 ==0) {
							vcount++;
						}
					}
					
					if (vcount==0) return;
					
					for (j=0; j<vcount; j+=1) {
						$('.layout-wizard-layout-fields').append('<div class="spacer_'+j+'"></div>');
					}
					data_view_id = $('.js-post_ID').val();

					for (i=0; i<vcount; i+=1) {
						
						var sel = '';
						if (typeof(data.real_fields) != 'undefined') {
							sel = data.real_fields[i];
						} else {
							
							if ((flist[(i*6)+1]) === 'types-field')
								sel = flist[(i*6)+3];
							else
								sel = '['+flist[(i*6)+1]+']';
						}
						
						var ajaxdata = {
							action: 'layout_wizard_add_field',
							id: i,
							wpnonce : $('#layout_wizard_nonce').attr('value'),
							selected: sel,
							view_id: data_view_id
						};
						/* One day I will switch it to ajax
						 $.ajax({
							 async:false,
		type:"POST",
		url:ajaxurl,
		data:ajaxdata,
		success:function(response){
							 if (response.length <=0) {
							 $.colorbox.close();
						 $('.js-wpv-settings-layout-extra .js-code-editor-toolbar:first').wpvToolsetMessage({
							 text: $('.js-wvp-wizard-loc').data('loc-error2'),
							  type:'error',
		inline:true,
		stay:false
						 });
						 return;
						 }
						 
						 response = $.parseJSON(response);
						 
						 if (response.selected_found === true) {
							 var field_html = response.html;
							 field = field_html.match(/(layout-wizard-style_)[0-9]+/);
						 key = field[0].match(/[0-9]+/);
						 $(field_html).insertAfter($('.layout-wizard-layout-fields div.spacer_'+key));
						 validate_insert_layout_pages(0);
						 $.each($('.js-wpv-dialog-layout-wizard select.js-select2'),function(){
							 if ( !$(this).hasClass('select2-offscreen') ) {
							 $(this).select2();
						 }
						 });
						 }
						 },
		error: function (ajaxContext) {
							 console.log( "Error: ", ajaxContext.responseText );
						 },
		complete: function() {
							 if (typeof key !== 'undefined') {
							 $('div.spacer_'+key).remove();
						 }
						 }
						 });
						 /*						*/
						$.post(ajaxurl, ajaxdata, function(response){
							if (response.length <=0) {
								$.colorbox.close();
								$('.js-wpv-settings-layout-extra .js-code-editor-toolbar:first').wpvToolsetMessage({
									text: $('.js-wvp-wizard-loc').data('loc-error2'),
									type:'error',
									inline:true,
									stay:false
								});
								return;
							}  //
							
							response = $.parseJSON(response);
							
							if (response.selected_found === true) {
								var field_html = response.html;
								field = field_html.match(/(layout-wizard-style_)[0-9]+/);
								key = field[0].match(/[0-9]+/);
								$(field_html).insertAfter($('.layout-wizard-layout-fields div.spacer_'+key));
								validate_insert_layout_pages(0);
								$.each($('.js-wpv-dialog-layout-wizard select.js-select2'),function(){
									if ( !$(this).hasClass('select2-offscreen') ) {
										$(this).select2();
									}
								});
							}
							
						}, 'html' )
						.done(function(){
							if (typeof key !== 'undefined') {
								$('div.spacer_'+key).remove();
							}
						});
						$('.layout-wizard-layout-fields').sortable({ handle: ".js-layout-wizard-move-field" });
					}
				}
			}
		});
	}
	
	function wpv_merge_with_local_copy(data) {
		
		if (wpv_layout_settings_from_wizard) {
			data.style = wpv_layout_settings_from_wizard.style;
			data.insert_at = wpv_layout_settings_from_wizard.insert_at;
			data.table_cols = wpv_layout_settings_from_wizard.table_cols;
			data.include_field_names = wpv_layout_settings_from_wizard.include_field_names;
			data.fields = wpv_layout_settings_from_wizard.fields;
			data.real_fields = wpv_layout_settings_from_wizard.real_fields;
			//data.bootstrap_grid_style = wpv_layout_settings_from_wizard.bootstrap_grid_style;
			data.bootstrap_grid_cols = wpv_layout_settings_from_wizard.bootstrap_grid_cols;
			data.bootstrap_grid_container = wpv_layout_settings_from_wizard.bootstrap_grid_container;
			data.bootstrap_grid_individual = wpv_layout_settings_from_wizard.bootstrap_grid_individual;
			//data.bootstrap_grid_cols_width = wpv_layout_settings_from_wizard.bootstrap_grid_cols_width;
			
		}
		return data;
	}
	
	function change_tab(backward) {
		count = $('.wpv-dialog-nav-tab').index( $('li').has('.active') );

		$('.wpv-dialog-nav-tab a').removeClass('active');
		$('.wpv-dialog-content-tab').hide();

		if (backward) {
			count--;
		} else {
			count++;
		}
		
		$('.wpv-dialog-nav-tab a').eq(count).addClass('active');
		$('.wpv-dialog-content-tab').eq(count).fadeIn('fast');

		validate_insert_layout_pages(count);
	}

	function validate_insert_layout_pages(page_id) {
		var valid = false;

		$('.js-dialog-prev').css('display', (page_id === 0) ? 'none' : 'inline');

	    $('.js-insert-layout').text( (page_id === 2) ? $('.js-wvp-wizard-loc').data('loc-insert') : $('.js-wvp-wizard-loc').data('loc-next') );

		switch (page_id) {
			case 0:
				next_enable = $('[name="layout-wizard-style"]:checked').val();
				break;
			case 1:
				next_enable = $('.layout-wizard-layout-fields [name="layout-wizard-style"]').val();
				break;
			case 2:
				next_enable = $('[name="layout-wizard-insert"]:checked').val();
				break;
		}
		$('.js-insert-layout').prop('disabled', (next_enable === undefined) ? true : false );

		if( next_enable === undefined )
		{
			$('.js-insert-layout').removeClass('button-primary').addClass('button-secondary')
		}
		else
		{
			$('.js-insert-layout').addClass('button-primary').removeClass('button-secondary')
		}

		return valid;
	}
	
	
	/*$(document).on('change', '[name="bootstrap_grid_style"]', function() {
		var grid_options = '';
		$('[name="bootstrap_grid_cols"] option').remove();
		if ( $(this).val() === 'fixed' ){
			var col_width = $('select[name="bootstrap_grid_cols_width"]').val();
			for(i=1;i<13;i++){
				if ( (i%col_width) == 0){
				   grid_options	+= '<option value="'+(i/col_width)+'">'+(i/col_width)+'</option>';
				}   
			}
			$('.js-layout-wizard-bootstrap-grid-col-width').show();	
		}
		if ( $(this).val() === 'fluid' ){
			grid_options	+= '<option value="1">1</option>';
			grid_options	+= '<option value="2">2</option>';
			grid_options	+= '<option value="3">3</option>';
			grid_options	+= '<option value="4">4</option>';
			grid_options	+= '<option value="6">6</option>';
			grid_options	+= '<option value="12">12</option>';	
			$('.js-layout-wizard-bootstrap-grid-col-width').hide();	
		}
		
		$('[name="bootstrap_grid_cols"]').append(grid_options);	
	});*/
	
	$(document).on('change', '[name="bootstrap_grid_cols_width"]', function() {
		var grid_options = '';
		$('[name="bootstrap_grid_cols"] option').remove();
		var col_width = $(this).val();
		for(i=1;i<13;i++){
			if ( (i%col_width) == 0){
			   grid_options	+= '<option value="'+(i/col_width)+'">'+(i/col_width)+'</option>';
			}   
		}
		$('[name="bootstrap_grid_cols"]').append(grid_options);	
	});
	
	$(document).on('click', '.js-insert-layout', function(e) {
		index = $('.wpv-dialog-nav-tab').index( $('li').has('.active') );

		if (index === 2) {
			layout_style = $('[name=layout-wizard-style]:checked').val();
			number_of_columns = $('[name="table_cols"]').val();
			bootstrap_grid_cols = $('[name="bootstrap_grid_cols"]').val();
			//bootstrap_grid_style = $('[name="bootstrap_grid_style"]').val();
			//bootstrap_grid_cols_width = $('[name="bootstrap_grid_cols_width"]').val();
			bootstrap_grid_container = $('[name="bootstrap_grid_container"]').prop('checked');
			bootstrap_grid_individual = $('[name="bootstrap_grid_individual"]:checked').val();
			include_headers = ($('[name="include_field_names"]').attr('checked')) ? true : false;
			$(this).prop('disabled', true).removeClass('button-primary').addClass('button-secondary');
			spinnerContainer = $('<div class="spinner ajax-loader">').insertBefore($(this)).show();

			//on_generate_wpv_layout(false);
			var fields = [];
			$.each($('select[name="layout-wizard-style"]'), function(index) {
				value = $(this).val();
				headname = $('[value="'+value+'"]').data('headename');
				rowtitle = $('[value="'+value+'"]').data('rowtitle');
				fields[index] = Array( '', editor_decode64(value), '', rowtitle, headname, rowtitle );
			});

			insert_to_view = $('#js-layout-wizard-insert [name="layout-wizard-insert"]:checked').val();

			var data = '';
			switch (layout_style) {
				case "table":
					var cols_selected = $('select[name="table_cols"]').val();
					data = wpv_render_table_layout(fields, cols_selected);
					break;
				
				case "bootstrap-grid":
					data = wpv_render_bootstrap_grid_layout(fields, bootstrap_grid_cols, bootstrap_grid_container, bootstrap_grid_individual);
					break;
				
				case "table_of_fields":
					data = wpv_render_table_of_fields_layout(fields);
					break;

				case "ordered_list":
					data = wpv_render_ordered_list_layout(fields);
					break;

				case "un_ordered_list":
					data = wpv_render_un_ordered_list_layout(fields);
					break;

				default:
					// unformatted
					data = wpv_render_unformatted_layout(fields);
					break;
			}

			var wpv_loc_error = $('.js-wvp-wizard-loc').data('loc-error');

			var codemirror_views = icl_editor.codemirrorGet('wpv_layout_meta_html_content');
			var c = codemirror_views.getValue();

			if (insert_to_view === 'insert_cursor') {
				var current_cursor = codemirror_views.getCursor(true);
				var text_before = codemirror_views.getRange({line:0,ch:0}, current_cursor);
				var text_after = codemirror_views.getRange(current_cursor, {line:codemirror_views.lastLine(),ch:null});

				total_count =            c.match(/(\[|\<)\/?[a-z\-\s\=\"]+(\]|\>)/gi);
				before_count = text_before.match(/(\[|\<)\/?[a-z\-\s\=\"]+(\]|\>)/gi);
				after_count =   text_after.match(/(\[|\<)\/?[a-z\-\s\=\"]+(\]|\>)/gi);

				before_count = (before_count !== null) ? before_count.length : 0;
				after_count =  (after_count !== null)  ? after_count.length : 0;

				if (total_count.length !== before_count + after_count) {
					$('.js-wpv-settings-layout-extra .js-code-editor-toolbar:first').wpvToolsetMessage({
						text: wpv_loc_error,
						type:'error',
						inline:true,
						stay:false
					});
				} else {
					codemirror_views.replaceRange(data, current_cursor, current_cursor);
				}

			} else {
				c = add_wpv_layout_data_to_content(c, data);
				codemirror_views.setValue(c);
			}

			// save the layout settings to a variable
			// These will be then be saved to the DB when we update the
			// Layout section or they'll be used when we open the wizard again.
			
			var data = {
				action: 'wpv_convert_layout_settings',
				view_id: $('.js-post_ID').val(),
				layout_style: layout_style,
				fields: fields,
				insert_to_view: insert_to_view,
				numcol: number_of_columns,
				//bootstrap_grid_style: bootstrap_grid_style,
				bootstrap_grid_cols: bootstrap_grid_cols,
				//bootstrap_grid_cols_width: bootstrap_grid_cols_width,
				bootstrap_grid_container: bootstrap_grid_container,
				bootstrap_grid_individual: bootstrap_grid_individual,
				inc_headers: include_headers,
				layout_content: codemirror_views.getValue()
			};
			$.ajax({
				async:false,
				type:"POST",
				url:ajaxurl,
				data:data,
				success:function(response){
					wpv_layout_settings_from_wizard = $.parseJSON(response);
				},
				error: function (ajaxContext) {
					console.log( "Error: ", ajaxContext.responseText );
				},
				complete: function() {
					spinnerContainer.remove();
				}
			});
			//	$.post(ajaxurl, data, function(response){
			//		wpv_layout_settings_from_wizard = $.parseJSON(response);
			//	});
			
			$.colorbox.close();
			codemirror_views.refresh();
			codemirror_views.focus();

			$('.js-wpv-layout-extra-update').prop('disabled', false).addClass('js-wpv-section-unsaved').addClass('button-primary');
			/*jQuery('.js-wpv-layout-extra-update').prop('disabled', true).removeClass('button-primary').addClass('button-secondary').removeClass('js-wpv-section-unsaved');*/
			$('.js-wpv-show-hide-layout-extra').parent().find('.toolset-alert').remove();
			if ($('.js-wpv-section-unsaved').length <=0 ) {
				setConfirmUnload(false);
			}
	
			wpv_layout_wizard_hint();
		} //end if (index === 2)

		index++;
		$('.wpv-dialog-nav-tab a').eq(index).removeClass('js-tab-not-visited');
	//	$('.wpv-dialog-nav-tab a').eq(index).click();
		change_tab(false);
	});
	
	$(document).on('click', '.wpv-dialog-nav-tab a', function(e) {
		e.preventDefault();
		var thiz = $(this),
			thiz_tab = thiz.parents('.wpv-dialog-nav-tab'),
			thiz_index = $('.wpv-dialog-nav-tab').index( thiz_tab );
		if ( !thiz.hasClass( 'js-tab-not-visited' ) && !thiz.hasClass( 'active') ) {
			wpv_layout_wizard_go_to_tab(thiz_index);
		}
	});
	
	function wpv_layout_wizard_go_to_tab(thiz_index) {
		$('.wpv-dialog-nav-tab a').removeClass('active');
		$('.wpv-dialog-content-tab').hide();
		$('.wpv-dialog-nav-tab a').eq(thiz_index).addClass('active');
		$('.wpv-dialog-content-tab').eq(thiz_index).fadeIn('fast');
		validate_insert_layout_pages(thiz_index);
	}

	$(document).on('click', '.js-layout-wizard-add-field', function(e) {

		if (wpv_layout_wizard_add_field_ui) {
			layout_wizard_add_field_ui(wpv_layout_wizard_add_field_ui);
		} else {
			data_view_id = $('.js-post_ID').val();
			var data = {
				action: 'layout_wizard_add_field',
				id: '__wpv_layout_count_placeholder__',
				wpnonce : $('#layout_wizard_nonce').attr('value'),
				view_id: data_view_id
			};
	
			$.post(ajaxurl, data, function(response){
				if (response.length <=0) {
					$.colorbox.close();
					$('.js-wpv-settings-layout-extra .js-code-editor-toolbar:first').wpvToolsetMessage({
						text: $('.js-wvp-wizard-loc').data('loc-error2'),
						type:'error',
						inline:true,
						stay:false
					});
					return;
				}  //
				
				wpv_layout_wizard_add_field_ui = $.parseJSON(response).html;
				layout_wizard_add_field_ui(wpv_layout_wizard_add_field_ui);
				
			}, 'html');
		}


		function layout_wizard_add_field_ui(field_html, count) {
			count = $('li[id*="layout-wizard-style_"]').size();
	
			field_html = field_html.replace('__wpv_layout_count_placeholder__', count + 1);
			
			$('.layout-wizard-layout-fields').append(field_html);
			$('.layout-wizard-layout-fields').sortable();
			validate_insert_layout_pages(1);
			$.each($('.js-wpv-dialog-layout-wizard select.js-select2'),function(){
				if ( !$(this).hasClass('select2-offscreen') ) {
					$(this).select2();
				}
			});
		}
	});

	$(document).on('click', '.js-wpv-query-type-update', function(){
		// query type has changed, so delete the add field ui we have saved.
		wpv_layout_wizard_add_field_ui = null;
	});
	
	$(document).on('click', '.js-layout-wizard-remove-field', function(e){
		$(this).parent().remove();
		validate_insert_layout_pages(1);
	});

	$(document).on('change', '[name="layout-wizard-style"], [name="layout-wizard-insert"]', function(){
		$('.js-insert-layout').prop('disabled', false).removeClass('button-secondary').addClass('button-primary');
	});
	
	$(document).on('change', '.js-wpv-layout-wizard-style', function() {
		var style_selected = $(this).val();
		if (style_selected === 'table') {
			$('.js-layout-wizard-num-columns').fadeIn('fast');
		} else {
			$('.js-layout-wizard-num-columns').hide();
		}
		if (style_selected === 'table_of_fields') {
			$('.js-layout-wizard-include-fields-names').fadeIn('fast');
		} else {
			$('.js-layout-wizard-include-fields-names').hide();
		}
		if (style_selected === 'bootstrap-grid') {
			$('.js-layout-wizard-bootstrap-grid-box').fadeIn('fast');
		} else {
			$('.js-layout-wizard-bootstrap-grid-box').hide();
		}
	});

	// DEPRECATED
	// There is no select[name="layout-wizard-style"] at all
	$(document).on('change', 'select[name="layout-wizard-style"]', function(){
		//	value = $(this).val();alert(value);
		//	var option = $('[value="'+value+'"]');
		var option = $(this).find(':selected');
		if ( option.data('rowtitle') === 'Body') {
			$(this).parent().find('.js-body-content-template').fadeIn('fast');
			$(this).parent().find('select[name="layout-wizard-body-template"]').fadeIn('fast');
			$(this).parent().find('.js-layout-wizard-body-template-text').show();
			$(this).parent().find('.js-custom-types-fields').hide();
		}
		else if (option.data('istype')==1) {
			$(this).parent().find('.js-custom-types-fields').attr('rel', option.data('typename'));
			$(this).parent().find('.js-custom-types-fields').show();
			$(this).parent().find('.js-layout-wizard-body-template-text').hide();
			$(this).parent().find('select[name="layout-wizard-body-template"]').fadeOut('fast');
		} else {
			$(this).parent().find('.js-body-content-template').fadeOut('fast');
			$(this).parent().find('select[name="layout-wizard-body-template"]').fadeOut('fast');
			$(this).parent().find('.js-layout-wizard-body-template-text').hide();
			$(this).parent().find('.js-custom-types-fields').hide();
		}
	});

	$(document).on('click', '.js-custom-types-fields', function(){
		wpv_all_selected = [];
		$i = 0;
		$('select.js-layout-wizard-item').each(function() {
			wpv_all_selected[$i++] = $(this).find(':selected').val();
		});

		$.each($('.js-wpv-dialog-layout-wizard select.js-select2'),function(){
				$(this).select2('destroy');
		});
		
		wpv_wizard_popup = $('#colorbox').clone();
		wpv_wizard_popup_item = $(this).parent().find('[name=layout-wizard-style]').find(':selected');


		wpv_wizard_popup_item_parent = $(this).parent();
		wpv_wizard_popup_style = $('input[name="layout-wizard-style"]:checked').val();

		var current = Base64.decode(wpv_wizard_popup_item.val());
		var metatype = current.search(/types.*?field=/g) == -1 ? 'usermeta' : 'postmeta';
		if ( typeof($(this).data('type')) !== 'undefined') {
			metatype = $(this).data('type');
		}
		typesWPViews.wizardEditShortcode($(this).attr('rel'), metatype, -1, current);
	});


	$(document).on('click', '.js-dialog-prev', function() {
		change_tab(true);
	});


    function add_wpv_layout_data_to_content(c, data) {
        if (c.search(/<!-- wpv-loop-start -->[\s\S]*\<!-- wpv-loop-end -->/g) == -1) {
            // not there so we need to add.
            c += data;
        } else {
            c = c.replace(/<!-- wpv-loop-start -->[\s\S]*\<!-- wpv-loop-end -->/g, "<!-- wpv-loop-start -->\n"+data+"	<!-- wpv-loop-end -->");
        }
        return c;
    }

    //render functions
    //fields array
    //0 - prefix, text before [shortcode]
    //1 - [shortcode]
    //2 - suffix, text after [shortcode]
    //3 - field name
    //4 - header name
    //5 - row title <TH>
    //0,2 maybe not used in v1.3

    function wpv_render_unformatted_layout(fields) {
        var body = "";
        for ( var i = 0; i < fields.length; i++ ) {
            body += fields[i][0];
            body += fields[i][1];
            body += fields[i][2];
        }

        var output = "   <wpv-loop>\n";
        output += "      " + body + "\n";
        output += "   </wpv-loop>\n";

        return output;
    }

    function wpv_render_un_ordered_list_layout(fields) {
        var body = "";
        for ( var i = 0; i < fields.length; i++ ) {
            body += fields[i][0];
            body += fields[i][1];
            body += fields[i][2];
        }

        var output = "   <ul>\n";
        output += "      <wpv-loop>\n";
        output += "         <li>" + body + "</li>\n";
        output += "      </wpv-loop>\n";
        output += "   </ul>\n";

        return output;
    }

    function wpv_render_ordered_list_layout(fields) {
        var body = "";
        for ( var i = 0; i < fields.length; i++ ) {
            body += fields[i][0];
            body += fields[i][1];
            body += fields[i][2];
        }

        var output = "   <ol>\n";
        output += "      <wpv-loop>\n";
        output += "         <li>" + body + "</li>\n";
        output += "      </wpv-loop>\n";
        output += "   </ol>\n";

        return output;
    }

    function wpv_render_table_of_fields_layout(fields) {
        var output = "   <table width=\"100%\">\n";
        if ( $('#include_field_names').attr('checked') ) {
            output += "            <thead><tr>\n";
            for ( var i = 0; i < fields.length; i++ ) {
                output += "               <th>[wpv-heading name=\"" + fields[i][4] + "\"]" + fields[i][5] + "[/wpv-heading]</th>\n";
            }
            output += "            </tr></thead>\n";
        }

        output += "      <tbody>\n";
        output += "      <wpv-loop>\n";

        output += "            <tr>\n";
        for ( var i = 0; i < fields.length; i++ ) {
            var body = fields[i][0];
            body += fields[i][1];
            body += fields[i][2];

            output += "               <td>" + body + "</td>\n";
        }
        output += "            </tr>\n";
        output += "      </wpv-loop>\n   </tbody>\n   </table>\n";
        return output;
    }
	
	/*
	 * Render Table based grid
	 */
    function wpv_render_table_layout(fields, cols) {
        var body = "";
        for ( var i = 0; i < fields.length; i++ ) {
            body += fields[i][0];
            body += fields[i][1];
            body += fields[i][2];
        }

        var output = "   <table width=\"100%\">\n      <wpv-loop wrap=\"" + cols + "\" pad=\"true\">\n";
        output += "         [wpv-item index=1]\n";
        output += "            <tr><td>" + body + "</td>\n";
        output += "         [wpv-item index=other]\n";
        output += "            <td>" + body + "</td>\n";
        output += "         [wpv-item index=" + cols + "]\n";
        output += "            <td>" + body + "</td></tr>\n";
        output += "         [wpv-item index=pad]\n";
        output += "            <td></td>\n";
        output += "         [wpv-item index=pad-last]\n";
        output += "            <td></td></tr>\n";
        output += "      </wpv-loop>\n   </table>\n";

        return output;

    }
    
    /*
     * Render Bootstrap grid
     */
    function wpv_render_bootstrap_grid_layout(fields, cols, container, individual) {
        var body = "";
        for ( var i = 0; i < fields.length; i++ ) {
            body += fields[i][0];
            body += fields[i][1];
            body += fields[i][2];
        }
		
		col_num = 12/cols;	
		var output = '';
		var row_style = '';
		var col_style = 'col-sm-';
		//Row style and cols class for bootstrap 2.0
		if ( data.wpv_bootstrap_version == 2){
			row_style = ' row-fluid';
			var col_style = 'span';
		}	
		if ( container === true ){
			output += "   <div class=\"container\">\n";	
		}
		output += "   <wpv-loop wrap=\"" + cols + "\" pad=\"true\">\n";
		ifone = '';
        // 
        if ( cols == 1){
        	ifone = '</div>';	
        }
        if ( individual == 1 ){
        	output += "         [wpv-item index=1]\n";
        	output += "            <div class=\"row"+row_style+"\"><div class=\""+ col_style + col_num +"\">" + body + "</div>"+ifone+"\n";
        	for(i=2;i<cols;i++){
        		output += "         [wpv-item index="+i+"]\n";
        		output += "           <div class=\""+ col_style + col_num +"\">" + body + "</div>\n";	
        	}
        }
        //
        else{
        	output += "         [wpv-item index=1]\n";
        	output += "            <div class=\"row"+row_style+"\"><div class=\""+ col_style + col_num +"\">" + body + "</div>"+ifone+"\n";
	        output += "         [wpv-item index=other]\n";
	        output += "            <div class=\""+ col_style + col_num +"\">" + body + "</div>\n";
	    }
	    if ( cols > 1){
	        output += "         [wpv-item index=" + cols + "]\n";
	        output += "            <div class=\""+ col_style + col_num +"\">" + body + "</div></div>\n";
        }
        output += "         [wpv-item index=pad]\n";
        output += "            <div class=\""+ col_style + col_num +"\"></div>\n";
        output += "         [wpv-item index=pad-last]\n";
        output += "            </div>\n";
        output += "    </wpv-loop>\n";
        if ( container === true ){
			output += "    </div>\n";	
		}
        
        return output;

    }
    //end render functions

    $(document).on('click', '.js-body-content-template', function() {
        $(this).parent().find('select[name="layout-wizard-body-template"]').fadeIn('fast');
    });

    $(document).on('change', 'select[name="layout-wizard-body-template"]', function(){
        $(this).parent().find('[name="layout-wizard-style"] [data-rowtitle="Body"]').val( $(this).val() );
    });
});

function wpv_restore_wizard_popup(shortcode) {
    jQuery.colorbox({
         html: jQuery(wpv_wizard_popup).find('#cboxLoadedContent').html(),
         onComplete: function() {
            var select = jQuery('#'+wpv_wizard_popup_item_parent.prop('id')+' select option[value="'+wpv_wizard_popup_item.val()+'"]');

            jQuery('#'+wpv_wizard_popup_item_parent.prop('id')+' select').val(wpv_wizard_popup_item.val());


            $i = 0;
            jQuery('select.js-layout-wizard-item').each(function() {
                /*jQuery(this).find('[value="'+wpv_all_selected[$i]+'"]').click();*/
                jQuery(this).val(wpv_all_selected[$i]);
                $i++;
            });

            select.val(Base64.encode(shortcode));

            jQuery('input[name=layout-wizard-style][value='+wpv_wizard_popup_style+']').click();

            jQuery.each(jQuery('.js-wpv-dialog-layout-wizard select.js-select2'),function(){
                    jQuery(this).select2();
            });

         }
     });
}

function wpv_cancel_wizard_popup() {
    jQuery.colorbox({
         html: jQuery(wpv_wizard_popup).find('#cboxLoadedContent').html(),
         onComplete: function() {
            var select = jQuery('#'+wpv_wizard_popup_item_parent.prop('id')+' select option[value="'+wpv_wizard_popup_item.val()+'"]');

            jQuery('#'+wpv_wizard_popup_item_parent.prop('id')+' select').val(wpv_wizard_popup_item.val());

            $i = 0;
            jQuery('select.js-layout-wizard-item').each(function() {
                jQuery(this).val(wpv_all_selected[$i]);
                $i++;
            });

            jQuery('input[name=layout-wizard-style][value='+wpv_wizard_popup_style+']').click();

            jQuery.each(jQuery('.js-wpv-dialog-layout-wizard select.js-select2'),function(){
                    jQuery(this).select2();
            });

         }
     });
}


var Base64 = {

	// private property
	_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
	
	// public method for encoding
	encode : function (input) {
		var output = "";
		var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
		var i = 0;
	
		input = Base64._utf8_encode(input);
	
		while (i < input.length) {
	
			chr1 = input.charCodeAt(i++);
			chr2 = input.charCodeAt(i++);
			chr3 = input.charCodeAt(i++);
	
			enc1 = chr1 >> 2;
			enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
			enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
			enc4 = chr3 & 63;
	
			if (isNaN(chr2)) {
				enc3 = enc4 = 64;
			} else if (isNaN(chr3)) {
				enc4 = 64;
			}
	
			output = output +
			this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
			this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
	
		}
	
		return output;
	},
	
	// public method for decoding
	decode : function (input) {
		var output = "";
		var chr1, chr2, chr3;
		var enc1, enc2, enc3, enc4;
		var i = 0;
	
		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
	
		while (i < input.length) {
	
			enc1 = this._keyStr.indexOf(input.charAt(i++));
			enc2 = this._keyStr.indexOf(input.charAt(i++));
			enc3 = this._keyStr.indexOf(input.charAt(i++));
			enc4 = this._keyStr.indexOf(input.charAt(i++));
	
			chr1 = (enc1 << 2) | (enc2 >> 4);
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
			chr3 = ((enc3 & 3) << 6) | enc4;
	
			output = output + String.fromCharCode(chr1);
	
			if (enc3 != 64) {
				output = output + String.fromCharCode(chr2);
			}
			if (enc4 != 64) {
				output = output + String.fromCharCode(chr3);
			}
	
		}
	
		output = Base64._utf8_decode(output);
	
		return output;
	
	},
	
	// private method for UTF-8 encoding
	_utf8_encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";
	
		for (var n = 0; n < string.length; n++) {
	
			var c = string.charCodeAt(n);
	
			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}
	
		}
	
		return utftext;
	},
	
	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;
	
		while ( i < utftext.length ) {
	
			c = utftext.charCodeAt(i);
	
			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}
	
		}
	
		return string;
	}

}