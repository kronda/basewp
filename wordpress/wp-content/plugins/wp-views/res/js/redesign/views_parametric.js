var jqp = jQuery, WPV_Parametric, WPV_parametric_local = {};

WPV_parametric_local.message = {};
WPV_parametric_local.message.fadeOutLong = 300;
WPV_parametric_local.message.fadeOutShort = 400;

(function($){

	// overrides binding handlers to support option grouping
	ko.bindingHandlers.option = {
		update: function(element, valueAccessor, allBindingsAccessor ) {
			var value = ko.utils.unwrapObservable(valueAccessor()), allBindings = allBindingsAccessor();
			ko.selectExtensions.writeValue(element, value);
		}
	};

	jQuery( function() {
		// Init generic data for toolbar buttons
		WPV_parametric_local.generic_button = new WPV_ParametricGenericButtonUtils();
		// This order is mandatory for dependencies and button display
		WPV_parametric_local.add_search = new WPV_ParametricSearchButton();
		WPV_parametric_local.add_spinner = new WPV_ParametricSpinnerButton();
		WPV_parametric_local.add_reset = new WPV_ParametricResetButton();
		WPV_parametric_local.add_submit = new WPV_ParametricSubmitButton();
		WPV_parametric_local.pwindow = new WPV_ParametricFilterWindow();
	});

})(jQuery);


var WPV_ParametricFilterWindow = function() {

	//some local vars
	var self = this,
	buttonAdd = jqp( '.js-button_parametric_filter_create' ),
	buttonEdit = jqp( '.js-button_parametric_filter_edit' ),
	proxy = new WPV_ParametricJsonStore(),
	parametricViewModel,
	dialog = null,
	parser,
	buttons_visible = false;

	//statics
	WPV_ParametricFilterWindow.has_error_displayed = false;
	WPV_ParametricFilterWindow.errorPlaceHolder = null;
	WPV_ParametricFilterWindow.buttonEdit = buttonEdit;

	//constants
	self.MAIN_TEMPLATE = '/inc/redesign/templates/wpv-parametric-form.tpl.php';
	WPV_ParametricFilterWindow.FADE = 'slow';
	WPV_ParametricFilterWindow.FADE_FAST = 'fast';

	//public members
	self.editor = icl_editor ? icl_editor : undefined;
	self.text_area = jqp('#wpv_filter_meta_html_content');
	self.dialog = null;

	parser = new ShortCodeParser( self.text_area );

	self.WIDTH = 300;
	self.HEIGHT = 300;

	self.short_tag_fields = [
		"field", "values", "display_values", "auto_fill", "auto_fill_default", "auto_fill_sort", // fields filters
		"title", // checkbox field filter
		"default_label", "taxonomy_order", "taxonomy_orderby", "hide_empty", "format", // taxonomy is not included because it is not a variable field by itself??
		"ancestors", "force_zero", // relationship filters
		"type", "url_param"
	];

	self.is_edit = false;

	self.short_code_editable = null;
	self.fieldRawEditable = null;

	self.init = function() {
		self.init_dialogs();
		button_hide();
		self.addButtons();
		return this;
	};
	
	self.init_dialogs = function() {
		var dialog_height = jqp( window ).height() - 100;
		jqp( 'body' ).append( '<div id="js-parametric-form-dialog-external-container" class="toolset-shortcode-gui-dialog-container wpv-shortcode-gui-dialog-container"></div>' );
		self.dialog = jqp( "#js-parametric-form-dialog-external-container" ).dialog({
			autoOpen: false,
			modal: true,
			title: WPV_Parametric.dialog_title_create,
			minWidth: 550,
			maxHeight: dialog_height,
			draggable: false,
			resizable: false,
			position: { my: "center top+50", at: "center top", of: window },
			show: { 
				effect: "blind", 
				duration: 800 
			},
			open: function( event, ui ) {
				jqp( 'body' ).addClass( 'modal-open' );
			},
			close: function( event, ui ) {
				jqp( 'body' ).removeClass( 'modal-open' );
			},
			buttons:[
				{
					class: 'button-secondary',
					text: WPV_Parametric.cancel,
					click: function() {
						jqp( this ).dialog( "close" );
						WPV_parametric_local.generic_button.codemirror_views.focus();
					}
				},
				{
					class: 'button-primary',
					id: 'js_parametric_form_button',
					text: WPV_Parametric.insert,
					click: function() {
						
					}
				}
			],
			close: function( event, ui ) {
				
			}
		});
	};
	
	var button_hide = function() {
		var add_hide = buttonAdd.parent(),
		edit_hide = buttonEdit.parent(),
		search_hide = WPV_ParametricSearchButton.button.parent(),
		spinner_hide = WPV_ParametricSpinnerButton.button.parent(),
		reset_hide = WPV_ParametricResetButton.button.parent(),
		submit_hide = WPV_ParametricSubmitButton.button.parent(),
		fields_and_views_hide = jqp('.js-wpv-filter-edit-toolbar .js-code-editor-toolbar-button-v-icon'),
		cred_hide = jqp('.js-wpv-filter-edit-toolbar .js-code-editor-toolbar-button-cred-icon').parent().parent(),
		toolbar = jqp('.js-wpv-filter-extra-section .wpv-setting'),
		pag_hide = [
			fields_and_views_hide,
			cred_hide
		],
		slide_hide = [
			add_hide,
			edit_hide,
			fields_and_views_hide,
			search_hide,
			submit_hide,
			reset_hide,
			spinner_hide,
			cred_hide
		],
		cats_hide = [
			edit_hide,
			add_hide,
			search_hide,
			submit_hide,
			reset_hide,
			spinner_hide
		],
		button = jqp('<button class="button-secondary js-toggle-settings-button-viz-link"></button>');

		switch( WPV_Parametric.view_purpose ) {
			case 'full':
			case 'parametric':
			break;
			case 'pagination':
				jqp.each( pag_hide, function( i, v ) {
					v.hide();
				});
				toogle_buttons_visibility( pag_hide, toolbar, true );
				break;
			case 'slider':
				jqp.each( slide_hide, function( i, v ) {
					v.hide();
				});
				toogle_buttons_visibility( slide_hide, toolbar, true );
			break;
		}
		button_hide_if_tax_view( cats_hide, button, toolbar );
	};

	var button_hide_if_tax_view = function( to_hide, button, toolbar ) {
		var select = jqp('input:radio.js-wpv-query-type'),
		view_type = select.filter( ':checked' ).val();

		if ( 
			view_type == 'taxonomy' 
			|| view_type == 'users' 
		) {
			jqp.each( to_hide, function( i, v ) {
				jqp.data( v, 'is_visible', true );
				v.hide();
			});
		} else if ( view_type == 'posts' ) {
			jqp.each( to_hide, function( i, v ) {
				jqp.data( v, 'is_visible', false );
			});
		}

		select.on( 'change', function( event ) {
			if ( 
				jqp( 'input:radio.js-wpv-query-type:checked' ).val() == 'taxonomy' 
				|| jqp( 'input:radio.js-wpv-query-type:checked' ).val() == 'users' 
			) {
				jqp.each( to_hide, function( i, v ) {
					jqp.data( v, 'is_visible', true );
					v.hide();
				});
			} else if( jqp( 'input:radio.js-wpv-query-type:checked' ).val() == 'posts' ) {
				jqp.each( to_hide, function( i, v ) {
					jqp.data( v, 'is_visible', false );
					if ( 
						WPV_Parametric.view_purpose == 'full' 
						|| WPV_Parametric.view_purpose == 'parametric'
					) {
						v.show();
					} else if( buttons_visible ) {
						v.show();
					}
				});
			}
		});
	};

	var toogle_buttons_visibility = function( buttons, t, append ) {
		var toolbar = t,
		cont = jqp('<p class="more-controls-container" />'),
		button = jqp('<button class="button-secondary js-toggle-settings-button-viz-link"></button>');

		button.empty().append('<i class="icon-expand-alt"></i> More controls');
		if ( append ) {
			cont.append(button);
			toolbar.prepend(cont);
		}
		button.on( 'click', function( event ) {
			jqp.each( buttons, function( i, v ) {
				if ( ! buttons_visible ) {
					if ( ! jqp.data( v, 'is_visible') ) {
						v.fadeIn('fast');
					}
				} else if ( buttons_visible ) {
					if ( ! jqp.data( v, 'is_visible') ) {
						v.hide();
					}
				}
			});
			if ( buttons_visible ) {
				buttons_visible = false;
				jqp( this ).empty().append('<i class="icon-expand-alt"></i> More controls');
			} else if ( ! buttons_visible ) {
				buttons_visible = true;
				jqp( this ).empty().append('<i class="icon-collapse-alt"></i> Less controls');
			}
		});
	};
	
	self.addButtons = function() {
		WPV_parametric_local.message.container = jqp( ".js-wpv-filter-extra-section .wpv-setting .js-wpv-parametric-error-container" );
		self.createFilterAction();
		self.editFilterAction();
	};
	
	self.createFilterAction = function() {
		//on a click event to the button and make ajax call on click
		buttonAdd.on( 'mouseup', function( e ) {
			//build the data object to be sent to the server
			if ( 
				! self.move_cursor_if_no_content_within() 
				&& ! self.editor.cursorWithin( self.text_area, 'wpv-filter-controls', '/wpv-filter-controls' ) 
			) {
				WPViews.view_edit_screen.codemirror_panel( codemirror_views_query, WPV_Parametric.place_cursor_inside_wpv_controls, true, 'error' );
				return false;
			}

			var sendData = {
				action:'set_parametric_filter_create',
				wpv_parametric_create_nonce: WPV_Parametric.wpv_parametric_create_nonce,
				post_types:self.handleDataRequest().join( ',' )
			};

			if ( sendData.post_types ) {
				proxy.loader.loadShow( jqp( this ).parent().parent() );
				jqp( this ).prop( 'disabled', true );
				proxy.ajaxCall( sendData, ajaxurl, 'post', ajaxCreateCallback, [ jqp( this ) ] );
 			} else {
				WPViews.view_edit_screen.codemirror_panel( codemirror_views_query, WPV_Parametric.select_post_types, true, 'error' );
			}
			return false;
		});
	};
	
	self.editFilterAction = function() {
		buttonEdit.on( 'mouseup', function( e ) {
			var obj = getShortCodes(), 
			params = {};
			/* See WP_Views_plugin::view_parametric_create() in inc/wpv-plugin.class.php and
			 * Editor_addon_parametric::__construct() in inc/filters/editor-addon-parametric.class.php in order
			 * to understand where this ajax call is handled. */
			params.action = 'set_parametric_filter_edit';
			params.edit_field = obj;
			params.post_types = self.handleDataRequest().join( ',' );
			params.wpv_parametric_create_nonce = WPV_Parametric.wpv_parametric_create_nonce;
			// make the field 'wpcf-' if is a types field
			// NOTE this is never executed, params.fields is not set at all... addressing this on PHP callback, where we can surely check whether it is a Types field
			// Also, never trust wpcf- to be the prefix for all Types fields: what about fields under Types control?
			if ( 
				params.edit_field 
				&& params.edit_field.field 
				&& params.fields 
				&& params.fields.is_types 
			) {
				params.edit_field.field = 'wpcf-'+params.edit_field.field
			}
			if ( 
				params.edit_field 
				&& params.edit_field.ancestors 
			) {
				params.edit_field.relationship = 'relationship';
			}
			if ( null != obj ) {
				if ( params.post_types ) {
					if (
						params.edit_field.field
						|| params.edit_field.taxonomy
						|| params.edit_field.ancestors
					) {
						proxy.loader.loadShow( jqp( this ).parent().parent() );
						jqp( this ).prop( 'disabled', true );
						proxy.ajaxCall( params, ajaxurl, 'post', ajaxCreateCallback, [ jqp( this ) ] );
					} else {
						WPViews.view_edit_screen.codemirror_panel( codemirror_views_query, WPV_Parametric.editing_manual_filter, true, 'error' );
					}
				} else {
					WPViews.view_edit_screen.codemirror_panel( codemirror_views_query, WPV_Parametric.select_post_types, true, 'error' );
				}
			} else {
				//if(  WPV_Parametric.debug ) console.log("problems in getting the shortcode")
			}
			return false;
		});
	};

	self.setModelDataToBeSent = function()
	{
		var send = {}, data = ko.toJS( parametricViewModel ), values;

		if( !data || typeof data.fieldRaw == 'undefined' || !data.fieldRaw )
		{
			jqp('.js-wpv-parametric-dialog-toolset-messages') .wpvToolsetMessage({
				text:WPV_Parametric.make_valid_selection,
				stay:true, fadeOut:WPV_parametric_local.message.fadeOutLong,
				close:true
			});
			return null;
		}

		try{
			if( data.fieldRaw.use_user_values )
			{
				values = self.setUserValuesIfAny( data.fieldRaw.user_values );
				data.fieldRaw.values = values.values;
				data.fieldRaw.display_values = values.display_values;
			}
		}
		catch(e)
		{
			console.error(e.message)
		}


		return data.fieldRaw;
	};


	self.setUserValuesIfAny = function( user_values ) {
		var ret = {
				values: [],
				display_values: [] };

		if( !user_values ) {
			return ret;
		}

		jqp.each( user_values, function( i, v ) {
			if( v ) {
				try	{
					var actualValue = v.values ? v.values.replace(",", "\\\\,") : '';
					ret.values.push( actualValue );

					var actualDisplayValue = v.display_values ? v.display_values.replace(",", "\\\\,") : '';
					ret.display_values.push( actualDisplayValue );
				} catch( e ) {
					//if(  WPV_Parametric.debug ) console.log( e.message )
				}
			}
		});
		return ret;
	};


	self.insertShortCode = function(area, fields) {
		var open = '[wpv-control',
			close = ']',
			string = [],
			string_aux = [],
			attrs = fields,
			txt_area = area,
			insert = '',
			nicename = '',
			code = [],
			label = '',
			openset = '[wpv-control-set',
			closeset = '[/wpv-control-set]',
			openitem = '[wpv-control-item',
			ancestors_tree = '',
			codemirror_views = WPV_parametric_local.pwindow.editor.codemirrorGet('wpv_filter_meta_html_content'),
			codemirror_highlight_options = {
				className: 'wpv-codemirror-highlight'
			};

		try	{
			jqp.each( attrs.url_param, function( index, param ) {
				string[ index ] = '';
				string_aux[ index ] = '';
				jqp.each( attrs, function( i, v ) {
					nicename = attrs['name'];
					//FIXME: since we have some problem with the model
					if( attrs['type'] == 'textfield'
						//	attrs['type'] == 'radio' ||
						|| attrs['type'] == 'checkbox'
						|| attrs['type'] == 'datepicker'
						|| attrs['type'] == 'date' )
					{
						attrs['auto_fill'] = 0;
					}
					if( attrs['type'] == 'Types auto style' ) {
						attrs['auto_fill'] = 0;
						attrs['type'] = false;
					}

					if( attrs['auto_fill'] && attrs['is_types'] && attrs['auto_fill'].indexOf('wpcf-') == -1 ) {
						attrs['auto_fill'] = 'wpcf-'+attrs['auto_fill'];
					}

					if( !attrs['auto_fill'] ) {
						attrs['auto_fill_sort'] = false;
					}

					if ( attrs['force_zero'] && ( attrs['force_zero'] == true || attrs['force_zero'] == 1 || attrs['force_zero'] == "1" ) ) {
						attrs['force_zero'] = 'true';
					}

					if ( attrs['type'] != 'checkbox' || ( attrs['force_zero'] && ( attrs['force_zero'] == false || attrs['force_zero'] == 0 || attrs['force_zero'] == "0" ) ) ) {
						attrs['force_zero'] = '';
					}

					if( ~jqp.inArray(i, self.short_tag_fields) ) {
						if( v && v.length > 0 ) {
							if( i == 'field' )	{
								i = attrs['kind'];
								if ( i == 'relationship' )	{
									i = false;
								} else if( v.indexOf('wpcf-') != -1 ) {
									v = v.split('wpcf-')[1];
								}
							}

							if( i == 'url_param' ) {
								v = param.value;
							}

							if ( attrs['type'] != 'checkbox' && i == 'title' ) {
								i = false;
							}

							if ( attrs['kind'] == 'field'
								&& ( i == 'hide_empty' || i == 'taxonomy_order' || i == 'taxonomy_orderby' ) )
							{
								i = false;
							}

							if( i )	{
								//if( WPV_Parametric.debug ) console.log( "prop ", i, " val ", v )
								if ( attrs['kind'] == 'relationship' ) {
									if ( i == 'ancestors' || i == 'url_param' || i == 'format' ) {
										string[ index ] += ' ' + i + '="' + v + '"';
									} else {
										string_aux[ index ] += ' ' + i + '="' + v + '"';
									}
									if ( i == 'ancestors' ) {
										ancestors_tree = v;
									}
								} else {
									string[ index ] += ' ' + i + '="' + v + '"';
								}
							}
						}
					}
				});

				label = ( attrs['field_type_switch'] == 'checkbox' )
						? ''
						: '[wpml-string context="wpv-views"]' + nicename + ':[/wpml-string] ';

				if ( attrs['kind'] == 'relationship' ) {
					insert += openset + string[ index ] + close + "\n";
					ancestors_tree_items = ancestors_tree.split('>');
					for ( i = 0, l = ancestors_tree_items.length; i < l; i++ ) {
						insert += '\t' + openitem + string_aux[ index ] + ' ancestor_type="' + ancestors_tree_items[ i ] + '" default_label=""' + close + '\n';
					}
					insert += closeset + '\n';
				} else {
					insert += label + open + string[ index ] + close + "\n";
				}

				//if( WPV_Parametric.debug ) console.log( "STRING ", string[index] );
				code.push( string[ index ] );

			});
		} catch( e ) {
			console.error(e.message);
			return false;
		}

		if( !self.is_edit ) {
			var current_cursor = codemirror_views.getCursor( true );
			self.editor.InsertAtCursor( area, insert );
			var end_cursor = codemirror_views.getCursor( true ),
			filter_shortcode_marker = codemirror_views.markText( current_cursor, end_cursor, codemirror_highlight_options );
			setTimeout( function() {
				  filter_shortcode_marker.clear();
			}, 3000);
		} else {
			console.log( nicename, code, area, attrs );
			return self.edit_the_field( nicename, code, area, attrs );
		}

		return insert;
	};


	self.get_rid_of_between = function( code, params, con, nicename, area )
	{

		var actual = ko.toJS(parametricViewModel.fieldRaw), prev = params, short_code = code, tag = 'wpv-control', open = '[', close = ']', rpl = null, content = con, sc_obj = parser.getShortCodeObject(), tmp = '', wpmlo = '', wpmlc = '';

		wpmlo = '[wpml-string context="wpv-views"]';
		wpmlc = ':[/wpml-string]';

		if( short_code.length > 1 && prev.url_param.length > 1 )

		{
			jqp.each( prev.url_param, function( i, v ){
				try
				{

					tmp = self.short_code_editable.replace( sc_obj.url_param, v.value );
					content = parser.replace_tag_content( tag, short_code[i], nicename, content, tmp, self.fieldRawEditable.name );
				}
				catch( e )
				{
					console.error( e.message );
					content = null;
				}

			});
		}
		else if( short_code.length > 1 && prev.url_param.length == 1 )
		{
			var append = '', replace = '';
			jqp.each( short_code, function( i, v ){
				try
				{
					if( i == 0 )
					{
						tmp = self.short_code_editable.replace( sc_obj.url_param, prev.url_param[0].value );
						content = parser.replace_tag_content( tag, v, nicename, content, tmp, self.fieldRawEditable.name );
						replace = '['+tag+v+']';
					}
					else if( i == 1)
					{
						append = wpmlo + nicename + wpmlc +'\n'+ '['+tag+v+']\n';
						parser.append_tag_content( replace, append, content )
					}

				}
				catch( e )
				{
					console.error( e.message );
					content = null;
				}

			});
		}
		else if( short_code.length == 1 )
		{
			jqp.each( prev.url_param, function( i, v ){
				try
				{
					tmp = self.short_code_editable.replace( sc_obj.url_param, v.value );

					if( i == 0 )
					{
						content = parser.replace_tag_content( tag, short_code[i], nicename, content, tmp, self.fieldRawEditable.name );
					}
					else if( i == 1 )
					{
						content = parser.replace_tag_content( '', '', '', content, open+tmp+close, wpmlo+self.fieldRawEditable.name+wpmlc );
					}

				}
				catch( e )
				{
					console.error( e.message );
					content = null;
				}

			});

		}
		return content;
	};

	self.edit_the_field = function( nicename, code, area, current )
	{
		var content = WPV_parametric_local.generic_button.get_text_area_content(),
		rpl,
		tag = parser.shortCodeGetTagName(),
		cm,
		short_code = code,
		params = self.fieldRawEditable,
		newField = current,
		pieces = [];

		if( ~content.indexOf( self.short_code_editable ) )
		{
			try
			{
				parser.parse( area );

				if( params.url_param.length > 1 )
				{
					//if(  WPV_Parametric.debug ) console.log( "should enter self.get_rid_of_between ONE::: ",  params.url_param.length );
					rpl = self.get_rid_of_between( code, params, content, nicename, area );
				}
				else if( params.url_param.length == 1 && newField.url_param.length > 1 )
				{
				//	if(  WPV_Parametric.debug ) console.log( "should enter self.get_rid_of_between TWO::: ",  newField.url_param.length );
					rpl = self.get_rid_of_between( code, params, content, nicename, area );
				}
				else if( params.url_param.length == 1 && newField.url_param.length == 1 )
				{
					//if(  WPV_Parametric.debug ) console.log( "should enter parser.replace_tag_content THREE::: ",  newField.url_param.length );
					if ( newField.field == 'relationship' ) {
						pieces = content.split('wpv-control-set');
						if ( pieces[1] ) {
							content = content.replace(/wpv-control-item type="select"/gi,'wpv-control-item type="' + newField.type + '"');
							content = content.replace(/wpv-control-item type="multi-select"/gi,'wpv-control-item type="' + newField.type + '"');
							content = content.replace(/wpv-control-item type="checkboxes"/gi,'wpv-control-item type="' + newField.type + '"');
							content = content.replace(/wpv-control-item type="radios"/gi,'wpv-control-item type="' + newField.type + '"');
							if ( newField.ancestors ) {
								var ancestors = newField.ancestors.split('>');
								jqp.each(ancestors, function(i,v){
									if ( !~pieces[1].indexOf('ancestor_type="' + v + '"') ) {
										var insert_missing = '\t[wpv-control-item type="' + newField.type + '" ancestor_type="' + v + '" default_label=""]\n';
										content = content.replace('[/wpv-control-set',insert_missing + '[/wpv-control-set');
									}
								});
							}
						}
					}
					rpl = parser.replace_tag_content( tag, short_code[0], nicename, content, self.short_code_editable, self.fieldRawEditable.name );
				}
				return rpl;

			}
			catch(e)
			{
				if(  WPV_Parametric.debug ) console.log( e.message );

				return null;
			}

		}
		else
		{
			jqp('.js-wpv-parametric-dialog-toolset-messages').wpvToolsetMessage({
				text:problems_inserting_new_shortcode,
				stay:true, fadeOut:WPV_parametric_local.message.fadeOutLong,
				close:true
			});
		}

		return null;
	};


	jqp( document ).on( 'mouseup', "#js_parametric_form_button", function( event ) {
		var fields = self.setModelDataToBeSent(), sendData, shortcode,
		form = jqp( '#js-parametric-form' ), 
		button = jqp( "#js_parametric_form_button" );

		if( parametricViewModel.userValuesVisible() && !self.check_user_values_on_submit() ) return false;


		if( validateFieldsAgainstReservedWordAndCheckIfEmpty( fields ) ) return false;

		if( !WPV_ParametricFilterWindow.has_error_displayed && fields !== null )
		{
			sendData = {
				action:'get_parametric_filter_create',
				wpv_parametric_submit_create_nonce : WPV_Parametric.wpv_parametric_submit_create_nonce,
				fields:fields
			};

			if( self.is_edit )
			{
				if( self.fieldRawEditable.kind == 'field' )
				{
					sendData.edit_field = {index:self.fieldRawEditable.index.valueOf(), field:self.fieldRawEditable.field};
				}
				else if( self.fieldRawEditable.kind == 'taxonomy' )
				{
					sendData.edit_field = {index:self.fieldRawEditable.index.valueOf(), taxonomy:self.fieldRawEditable.field};
				}
				else if( self.fieldRawEditable.kind == 'relationship' )
				{
					sendData.edit_field = {index:self.fieldRawEditable.index.valueOf(), relationship:self.fieldRawEditable.field};
				}

			}

			shortcode = self.insertShortCode( self.text_area, fields );

			//if(  WPV_Parametric.debug ) console.log( "DATA TO BE SENT TO SAVE IN SETTINGS:::  ", sendData );
			//	return;

			if( !shortcode ) {
				jqp('.js-wpv-parametric-dialog-toolset-messages').wpvToolsetMessage({
					text:WPV_Parametric.something_bad,
					type:'error',
					stay:true, fadeOut:WPV_parametric_local.message.fadeOutLong,
					close:true
				});

				return false;
			}

			proxy.ajaxCall( sendData, ajaxurl, 'post', ajaxCreateInsertCallback, [] );

			self.dialog.dialog( 'close' );
		}
		return false;
	});


	/**
	 * Check custom (user) field options when submitting the form.
	 *
	 * If custom field options are being used, check whether they are valid. If not, highlight the problematic
	 * input field and show an wpvToolsetMessage.
	 *
	 * The validation is successful if:
	 * - values don't repeat themselves
	 * - no value is only whitespace
	 * - no display value is empty/whitespace
	 *
	 * @todo describe exactly what "valid" means
	 *
	 * @return bool True if all field option values and display values are valid.
	 *
	 * @since unknown
	 */
	self.check_user_values_on_submit = function()
	{
		var field = typeof parametricViewModel.fieldRaw() != 'undefined' ? parametricViewModel.fieldRaw() : undefined,
			options,
			isEverythingValid = true,
			tmp = [],
			badValuesIndices = [],
			repeatingValuesIndices = [],
			badDisplayValuesIndices = [];

		try
		{
			if( parametricViewModel.userValuesVisible( ) )
			{

				/*jqp('.js-user-values').each(function( i ){
					if( jqp(this).val() && jqp(this).val() != " " )
					{
						tmp.push( jqp(this).val() );
					}
					else
					{
						index.push( i );
					}
				});*/

				// Get all values into an array
				var userValues = [];
				jqp('.js-user-values').each( function( i ) {
					userValues[ i ] = jqp( this ).val();
				});

				// Check for blank (whitespace) values
				jqp.each( userValues, function( index, value ) {
					if( ( value.length != 0 ) && ( value.trim() == '' ) ) {
						badValuesIndices.push( index );
						isEverythingValid = false;
					}
				});

				// Check for repeating values
				var repeatingTmp = [];
				jqp.each( userValues, function( firstIndex, firstValue ) {
					jqp.each( userValues, function( secondIndex, secondValue ) {
						if( firstIndex < secondIndex
							&& firstValue.trim() == secondValue.trim()
							// this will exclude comparing empty string with whitespaces
							&& jqp.inArray( firstValue, badValuesIndices ) === -1
							&& jqp.inArray( secondValue, badValuesIndices ) === -1 ) {
							repeatingTmp.push( secondIndex );
							isEverythingValid = false;
						}
					});
				});
				jqp.each( repeatingTmp, function( indexIndex, index ) {
					// values of repeatingTmp are indices of userValues
					if( jqp.inArray( index, repeatingValuesIndices ) === -1 ) {
						repeatingValuesIndices.push( index );
					}
				});

				// Check for empty/blank display values
				jqp('.js-user-display-values').each( function( index ) {
					var displayValue = jqp( this ).val();
					if( displayValue.trim() == '' ) {
						badDisplayValuesIndices.push( index );
						isEverythingValid = false;
					}
				});

			}
		}
		catch( e )
		{
			// We probably don't have the "field" defined.
			return false;
		}

		if( ! isEverythingValid ) {

			// Highlight problematic fields
			jqp('.js-user-values').each( function( fieldIndex ) {
				if( jqp.inArray( fieldIndex, badValuesIndices ) !== -1
					|| jqp.inArray( fieldIndex, repeatingValuesIndices ) !== -1 )
				{
					jqp( this ).css( 'border-color', 'red' );
				}

			});

			jqp('.js-user-display-values').each( function( fieldIndex ) {
				if( jqp.inArray( fieldIndex, badDisplayValuesIndices ) !== -1 ) {
					jqp( this ).css( 'border-color', 'red' );
				}
			});

			// Show error message
			WPV_ParametricFilterWindow.errorPlaceHolder = jqp( '.js-user-value-errors' ).wpvToolsetMessage({
				text: WPV_Parametric.check_values_and_values_labels,
				close: close,
				stay: true,
				fadeOut: WPV_parametric_local.message.fadeOutLong
			});
		}

		/*if( tmp.length == 0 )
		{
			jqp('.js-user-values').each( function( i ) {
				// Highlight the problematic field
				if( ( index.length == 1 ) || ( index.length > 1 && i > 0 ) ) {
					jqp(this).css('border-color', 'red');
				}
			});

			isEverythingValid = false;
			WPV_ParametricFilterWindow.errorPlaceHolder = jqp('.js-errors').wpvToolsetMessage({
				text:WPV_Parametric.check_values_and_values_labels
				, close: close
				, stay:true
				, fadeOut:WPV_parametric_local.message.fadeOutLong
			});
		}*/

		return isEverythingValid;
	};

	var validateFieldsAgainstReservedWordAndCheckIfEmpty = function( field )
	{
		var against = wpv_forbidden_parameters, has_problems = false, message = '', type = '', close = false, stay = false, field_with_problems = '';

		jqp.each(against, function(index, value) {
			try
			{
				jqp.each( field.url_param, function(i, p) {

					if( !p.value ){
						message = WPV_Parametric.field_mandatory;
						has_problems = true;
						field_with_problems = '#url_param';
						close = true;
						stay = true;
					}
					else if( ~jqp.inArray( p.value, value ) )
					{

						message =   '"'+ p.value + WPV_Parametric.reserved_word + index + '. ' + WPV_Parametric.avoid_conflicts;
						has_problems = true;
						field_with_problems = '#url_param';
						close = true;
						stay = true;
						WPV_ParametricFilterWindow.has_error_displayed = true;
						if( !Advanced_visible.viz )
						Advanced_visible.toggle_slide_up_down_advanced( );
					}
				});
				
				if ( field.kind == 'relationship' ) {
			if ( !field.ancestors || field.ancestors == 0 ) {
				message = WPV_Parametric.relationship_tree_mandatory;
				has_problems = true;
				field_with_problems = '#ancestors';
				close = true;
				stay = true;
			} else {
				var data = {
					action: 'validate_post_relationship_tree',
					id: jqp('.js-post_ID').val(),
					local_tree: field.ancestors,
					wpnonce: WPV_Parametric.wpv_parametric_validate_post_relationship_tree
				};
				jqp.ajax({
					async:false,
					type:"POST",
					url:ajaxurl,
					data:data,
					success:function(response){
						if ( (typeof(response) !== 'undefined') ){
							if (response != 'OK') {
								message = response;
								has_problems = true;
								field_with_problems = '#ancestors';
								close = true;
								stay = true;
							}
						}
					},
					error: function (ajaxContext) {

					},
					complete: function() {

					}
				});
			}

		}

			}
			catch(e)
			{
				//if(  WPV_Parametric.debug ) console.log( e.message );
				type = 'error';
				has_problems = true;
				field_with_problems = '#url_param';
				message = e.message;
			}
		});

		if( has_problems )
		{
			WPV_ParametricFilterWindow.errorPlaceHolder = jqp( "<p></p>" ).insertAfter( field_with_problems ).wpvToolsetMessage({
				text:message
				, type: type
				, close: close
				, stay:stay
				, fadeOut:WPV_parametric_local.message.fadeOutLong
			});
			fieldHasProblems(field_with_problems);

		}

		return has_problems;
	};

	var fieldHasProblems = function( field_with_problems )
	{
		jqp(field_with_problems).css('border-color', 'red');

		jqp(field_with_problems).on('change keyup input cut paste', function(e){
			jqp(this).css('border-color', '#dfdfdf');
			WPV_ParametricFilterWindow.has_error_displayed = false;
		});
	};

	var ajaxCreateInsertCallback = function( args )
	{
		try
		{
			if( args.Data.insert )
			{

				jqp( '.js-wpv-missing-filter-container, .js-wpv-no-filters-container' ).hide();

				WPViews.view_edit_screen.codemirror_panel( codemirror_views_query, args.Data.insert, false, 'success' );

				//tell filters section we created a new filter.
				var params = {
					action: 'wpv_filter_update_filters_list',
					id: WPV_Parametric.view_id,
					query_type: jQuery('input:radio.js-wpv-query-type:checked').val(),
					nonce: jQuery('.js-wpv-filter-update-filters-list-nonce').val()
				}
				jQuery.post(ajaxurl, params, function(response) {

					if ( (typeof(response) !== 'undefined') ) {
						decoded_response = jQuery.parseJSON(response);
						if ( decoded_response.success == params.id ) {
							jQuery('.js-filter-list').html(decoded_response.wpv_filter_update_filters_list);
							jQuery( document ).trigger( 'js_event_wpv_query_filter_created', [ 'parametric-all' ] );
						}
					} else {
						//if(  WPV_Parametric.debug ) console.log( WPV_Parametric.ajax_error, response );
					}
				})
				.fail(function(jqXHR, textStatus, errorThrown) {
					//if(  WPV_Parametric.debug ) console.log( WPV_Parametric.error_generic, textStatus, errorThrown );
				})
				.always(function() {

				});

			} else if( args.Data.error ) {
				WPViews.view_edit_screen.codemirror_panel( codemirror_views_query, WPV_Parametric.db_insert_problem + args.Data.error, true, 'error' );
				console.error( args.Data.error );
			}
		} catch( e ) {
			console.error( e.message );
			WPViews.view_edit_screen.codemirror_panel( codemirror_views_query, e.message, true, 'error' );
		}
	};

	self.handleDataRequest = function()
	{
		//	var post_types_checks = jqp('input[name="_wpv_settings[post_type][]"]:checked'), post_types = [];

		var post_types_checks = jqp('input.js-wpv-query-post-type:checked'), post_types = [];

		post_types_checks.each(function(i){
			post_types.push( jqp(this).val() );
		});
		return post_types;
	};

	var getShortCodes = function() {
		try {
			var short_code = parser.parse(self.text_area), ret;
			if ( ! short_code ) {
				WPViews.view_edit_screen.codemirror_panel( codemirror_views_query, WPV_Parametric.place_cursor_inside_valid_control_shortcodes, true, 'error' );
				return null;
			}

			var current_place = parser.shortCodeGetTagName();

			if ( current_place == 'wpv-control' ) {
				self.short_code_editable = parser.getShortCodeRawString();
				if ( ~self.short_code_editable.indexOf(']') ) {
					WPViews.view_edit_screen.codemirror_panel( codemirror_views_query, WPV_Parametric.place_cursor_inside_valid_control_shortcodes, true, 'error' );
					return null;
				} else {
					ret = parser.getShortCodeObject();
					return ret;
				}
			} else if ( current_place == 'wpv-control-set' ) {
				self.short_code_editable = parser.getShortCodeRawString();
				if ( ~self.short_code_editable.indexOf(']') ) {
					WPViews.view_edit_screen.codemirror_panel( codemirror_views_query, WPV_Parametric.place_cursor_inside_valid_control_shortcodes, true, 'error' );
					return null;
				} else {
					ret = parser.getShortCodeObject();
					return ret;
				}
			} else if ( current_place == 'wpv-control-item' ) {
				self.short_code_editable = parser.getShortCodeRawString();
				if ( ~self.short_code_editable.indexOf(']') ) {
					WPViews.view_edit_screen.codemirror_panel( codemirror_views_query, WPV_Parametric.place_cursor_inside_valid_control_shortcodes, true, 'error' );
				} else {
					WPViews.view_edit_screen.codemirror_panel( codemirror_views_query, WPV_Parametric.place_cursor_inside_wpv_control_set, true, 'error' );
				}
				return null;
			} else {
				WPViews.view_edit_screen.codemirror_panel( codemirror_views_query, WPV_Parametric.place_cursor_inside_valid_control_shortcodes, true, 'error' );
				return null;
			}

		} catch( e ) {
			console.error( e.message )
		}

		return null;
	};
	
	var openDialogCallback = function( data, button ) {
		var params = {
			action:'create_parametric_dialog',
			wpv_parametric_create_dialog_nonce: WPV_Parametric.wpv_parametric_create_dialog_nonce
		};
		jqp.post( ajaxurl, params, function( response ) {
			if ( response ) {
				self.dialog
					.html( response )
					.dialog({
						open: function( event, ui ) {
							var extra_query = '';
							parametricViewModel = new WPV_ParametricViewModel();
							ko.applyBindings( parametricViewModel );
							self.populateFields( parametricViewModel, data );
							if ( data.edit_field ) {
								self.is_edit = true;
								//keep track of previous obj state
								self.fieldRawEditable = jqp.extend({}, set_default_field( data.edit_field ), true );
								self.fieldRawEditable.index = data.edit_field.index;
								self.fieldRawEditable.can_force_zero = data.edit_field.can_force_zero;
								
								jqp( this ).dialog( "option", "title", WPV_Parametric.dialog_title_edit );
								jqp( "#js_parametric_form_button span" ).text( WPV_Parametric.update_input );
								extra_query = '&field=' + data.edit_field.field;								
							} else {
								self.is_edit = false
								jqp( "#js_parametric_form_button" )
									.removeClass( 'button-primary' )
									.addClass( 'button-secondary' )
									.prop( 'disabled', true );
							}
							toggle_fieldset_hidden_viz();
						},
						beforeClose: function( event, ui ) {
							ko.cleanNode( jqp( '#js-parametric-form-dialog-container' )[0] );
						},
						close: function( event, ui ) {
							WPV_ParametricFilterWindow.has_error_displayed = false;
							WPV_ParametricFilterWindow.errorPlaceHolder = null;
							Advanced_visible.viz = false;
							self.short_code_editable = null;
							self.fieldRawEditable = null;
							self.is_edit = false;
							button.prop( 'disabled', false );
						}
					})
					.dialog('open');
			}
		})
		.fail(function(jqXHR, textStatus, errorThrown){
			if(  WPV_Parametric.debug ) console.log(jqXHR, textStatus, errorThrown)
		})
		.always(function(){
			//
		});
	};

	var toggle_fieldset_hidden_viz = function() {
		jqp( '#js-toggle-advanced-paramentric-form-fields' ).on( 'click', function( event ) {
			Advanced_visible.toggle_slide_up_down_advanced( jqp( this ) );
		});
	};

	var ajaxCreateCallback = function(args, status, additional_args )
	{
		var data = {}, button = additional_args[0];

		if( args.Data && args.Data.error )
		{
			WPViews.view_edit_screen.codemirror_panel( codemirror_views_query, args.Data.error, true, 'error' );
			return false;
		}

		try
		{
			//open the dialog if we have something to show - a Data object as property of args
			data = args.Data;
			openDialogCallback( data, button );

		}
		catch(e)
		{
			console.error(WPV_Parametric.ajax_error, e.message );
			WPViews.view_edit_screen.codemirror_panel( codemirror_views_query, WPV_Parametric.data_loading_problem + e.message, true, 'error' );
		}
	};

	self.populateFields = function( model, data )
	{
		var group;

		self.groups = [];
		jqp.each(data, function( index, value ) {
			switch( index )
			{
				//i am using this only to exlcude unwanted data
				case 'view_id':
				case 'settings':
				case 'edit_field':
				case 'excludes':
				break;
				default:
				group = self.buildWPV_ParametricFields( index, value )
				if( group ) self.groups.push( group  );
				break;
			}
		});
		self.populateField( parametricViewModel, 'selectFilter',  self.groups );

		var myval = jqp('.js-flatten-types-relation-tree').val().split(',');
		myval.unshift({title:WPV_Parametric.relationship_select_tree,value:0});
		self.populateField( parametricViewModel, 'ancestors_array_func', myval );
	};

	self.filter_param_exists = function( url_param )
	{
		var control = url_param, content;
		if( !control ) return false;
		parser.parse( self.text_area );
		content = parser.getContent();
		if( ~control.indexOf(",") )
		{
			control = control.split(",")[0];
		}
		return content.indexOf( control ) != -1;
	};

	self.buildWPV_ParametricFields = function( group, data )
	{
		var prefix = group == 'taxonomy' ? 'wpv-' : '', ars = {}, groups, kind = '';

		ars[group] = new Array();

		kind = group == 'taxonomy' ? 'taxonomy' : 'field';
		kind = group == 'basic_filters' ? 'basic_filters' : kind;

		jqp.each(data, function( index, value ){
			try
			{

				if ( kind == 'basic_filters' ) {
					kind = value.basic_filter_type;
				}
				//if(  WPV_Parametric.debug ) console.log( "INDEX::::::::: ", value.index )
				if( !self.filter_param_exists( value.control ) )
				{
					var field = new WPV_ParametricField(
						value.control,
						group,
						kind,
						value.name,
						index,
						value.id,
						prefix,
						value.type,
						value.data_type,
						value.relation,
						value.custom,
						value.url_param,
						value.compare,
						value.is_types ? value.is_types : false,
						undefined,
						typeof value.index != 'undefined' ? value.index : -1,
						undefined,
						value.auto_fill,
						value.auto_fill_default,
						value.taxonomy_order,
						value.taxonomy_orderby,
						value.title,
						value.auto_fill_sort,
						value.hide_empty,
						value.ancestors,
						value.can_force_zero,
						value.force_zero,
						value.format
					);
					ars[group].push(field);

				//	console.log( "args", ars, "\n", "group", group,"\n", "current g", ars[group],"\n", 'field', field, "name", field.name, '\n\n' );
				}

			}
			catch(e)
			{
				console.log(WPV_Parametric.model_build_problem, e.message);
			}

		});

		if( ars[group].length > 0 )
		{
			//	//if(  WPV_Parametric.debug ) console.log( "GROUP:::: ", group, ars[group].length )
			groups = new WPV_GroupOption(group, ars[group] );
		}


		return groups;
	};

	//Populates the model with the edit field
	var set_default_field = function( f )
	{
		var raw = null, prefix = '', values = {}, valuesValues = [], field = f, url_params, urls = [], check_group;
		try
		{
			parametricViewModel.is_populating = true;

			url_params = field.url_param.split(',');

			prefix =  field.kind == 'taxonomy' ? 'wpv-' : '';

			values = jQuery.parseJSON(field.values);

			if( values && values.values && values.values.length > 0 && values.constructor.name == 'Object' )
			{

				if( field.display_values === undefined && values.values[0].length === 2 )
				{
					jqp.each(values.values, function(i, v){
						var display = v[1].replace(/\\\\\\\\,/g, "|" );
						valuesValues.push( new parametricViewModel.WPV_Value( v[0], display.replace("|", ",") ) );
					});
				}
				else
				{
					field.display_values = field.display_values.replace(/\\\\\\\\,/g, "|" );
					var display = field.display_values.split(",");
					jqp.each(values.values, function(i, v){

						valuesValues.push( new parametricViewModel.WPV_Value( v, display[i].replace("|", ",") ) );
					});
				}
			}
			// this is for retrocompatibility
			else if( values && values.length && values.constructor.name == 'Array' )
			{
				field.display_values = field.display_values.replace(/\\\\\\\\,/g, "|" );

				var display = field.display_values.split(",");

				jqp.each(values, function(i, v){
					valuesValues.push( new parametricViewModel.WPV_Value( v[0], display[i].replace("|", ",") ) );
				});
			}

			jqp.each(url_params, function(i,v){
				urls.push( new parametricViewModel.WPV_QS( v.trim() ) );
			});

			if( field.type === 'false' )
			{
				field.type = "Types auto style";
			}

			raw = new WPV_ParametricField(
				undefined,
				field.group,
				field.kind,
				field.name,
				field.field,
				field.id,
				prefix,
				field.type,
				field.data_type,
				field.relation,
				'',
				urls,
				field.compare,
				field.is_types,
				field.default_label,
				field.index,
				valuesValues.length > 0 ? valuesValues : undefined,
				values && values.auto_fill ? values.auto_fill : 0,
				values ? values.auto_fill_default : '',
				values.taxonomy_order,
				values.taxonomy_orderby,
				field.title,
				values.auto_fill_sort,
				values.hide_empty,
				field.ancestors,
				field.can_force_zero ? field.can_force_zero : 0,
				values.force_zero ? values.force_zero : 0,
				field.format
			);

			check_group = _.filter(self.groups, function( v, i , l ){
				return v.name() == field.group;
			});

			if( check_group.length === 0 )
			{
				self.groups.push( new WPV_GroupOption(field.group, [raw] ) );
			}
			else
			{
				jqp.each( self.groups, function(i, val ){
					if( val.name() == field.group )
					{
						val.children.unshift( raw );
					}
				});
			}

			if( values.taxonomy_order ) parametricViewModel.taxonomy_order( values.taxonomy_order );
			if( values.taxonomy_orderby ) parametricViewModel.taxonomy_orderby( values.taxonomy_orderby );
			if( values.hide_empty ) parametricViewModel.hide_empty( values.hide_empty );
			if ( field.format) parametricViewModel.format( field.format );

			if( field.default_label )
			{
				parametricViewModel.default_label( field.default_label  );
			}

			if( field.ancestors )
			{
				parametricViewModel.ancestors( field.ancestors );
			}


			if( field.is_types && parametricViewModel.selectInputKind()[0] != "Types auto style" && field.type ==  "Types auto style" && field.index != null )
			{
				parametricViewModel.selectInputKind.unshift( "Types auto style" );
				parametricViewModel.type( "Types auto style" );
			}

			parametricViewModel.compare( field.compare );

			parametricViewModel.fieldRaw( raw );

			if( values )
			{
				parametricViewModel.auto_fill_default( values.auto_fill_default );


				if( valuesValues && valuesValues.length )
				{
					parametricViewModel.values( valuesValues );
					parametricViewModel.userValuesVisible( true );
				}

				if( field.auto_fill )
				{
					parametricViewModel.auto_fill( 1 );
					parametricViewModel.fieldRaw().auto_fill( field.auto_fill );
				}
				else
				{
					parametricViewModel.auto_fill( !valuesValues && !valuesValues.length ? 1 : 0 );
					parametricViewModel.auto_fill_default_visible( !valuesValues && !valuesValues.length ? true : false );
				}
				if( ( field.type == 'checkbox' || field.type == 'radio' || field.kind == 'relationship' ) && valuesValues.length == 0 )
				{
					parametricViewModel.title( field.title );
					parametricViewModel.auto_fill( 1 );
				}

				if( field.force_zero )
				{
					parametricViewModel.force_zero( 1 );
					parametricViewModel.fieldRaw().force_zero( 1 );
				}
				else
				{
					parametricViewModel.force_zero( 0 );
					parametricViewModel.fieldRaw().force_zero( 0 );
				//	parametricViewModel.auto_fill_default_visible( !valuesValues && !valuesValues.length ? true : false );
				}

				if ( field.can_force_zero )
				{
					parametricViewModel.can_force_zero = 1;
					parametricViewModel.fieldRaw().can_force_zero = 1;
				}
				else{
					parametricViewModel.can_force_zero = 0;
					parametricViewModel.fieldRaw().can_force_zero = 0;
				}

				if( values.auto_fill_sort ) parametricViewModel.auto_fill_sort( values.auto_fill_sort );

				if( values.taxonomy_order ) parametricViewModel.taxonomy_order( values.taxonomy_order );
				if( values.taxonomy_orderby ) parametricViewModel.taxonomy_orderby( values.taxonomy_orderby );
				if( values.hide_empty ) parametricViewModel.hide_empty( values.hide_empty );
				if( field.format ) parametricViewModel.format( field.format );
			}
		}
		catch( e )
		{
			console.log( e.message);
			return null;
		}

		parametricViewModel.is_populating = false;

		return ko.toJS( parametricViewModel.fieldRaw );

	};

	//geerically populates a given flat field in a given model
	self.populateField = function( model, field, data ) {
		try {
			model[field](data);
		} catch( e ) {
			console.error( e.message, model, field, data );
		}
	};

	// an object to handle advanced options visibility
	var Advanced_visible = {
		viz:false,
		toggle_slide_up_down_advanced:function(  )
		{

			var button = jqp('#js-toggle-advanced-paramentric-form-fields'),
			caret = button.find('i[class^="icon-caret-"]');

			if( !Advanced_visible.viz )
			{
				jqp('.js-hidden-fields-container').slideDown(WPV_ParametricFilterWindow.FADE_FAST, function(){
					jqp("#js-dialog-form-visibles-hidden").removeClass('dialog-form-hidden').addClass('dialog-form-hidden-shown');
					caret
					.removeClass('icon-caret-down')
					.addClass('icon-caret-up');
					Advanced_visible.viz = true;
					button.get(0).firstChild.nodeValue = WPV_Parametric.expand_button_hide +' ';
				});
			}
			else if( Advanced_visible.viz )
			{
				jqp('.js-hidden-fields-container').slideUp(WPV_ParametricFilterWindow.FADE_FAST, function(){
					jqp("#js-dialog-form-visibles-hidden").removeClass('dialog-form-hidden-shown').addClass('dialog-form-hidden');
					caret
					.removeClass('icon-caret-up')
					.addClass('icon-caret-down');
					Advanced_visible.viz = false;
					button.get(0).firstChild.nodeValue = WPV_Parametric.expand_button_expand +' ';
				});
			}
		}
	};

	self.move_cursor_if_no_content_within = function( area ) {
		var content = '', match, cm;
		parser.parse( area || self.area );
		content = parser.getContent();
		match = content.match(/\[wpv-filter-controls\](W?)\[\/wpv-filter-controls\]/);

		if ( 
			match != null 
			&& !match[1] 
		) {
			try {
				parser.cm.setCursor( parser.cm.posFromIndex( match.index + '[wpv-filter-controls]'.length ) );
				return true;
			} catch( e ) {
				console.log( e.message );
				return false;
			}
		}
		return false;
	};
	
	self.init();

	return self;
};




/* //////// MODELS ///////*/
////THE FILTER MODEL /////////////////////////////////
var WPV_ParametricField = function( control, group, kind, name, value, id, prefix, type, data_type, relation, custom, url_param, compare, is_types, default_label, index, values, auto_fill, auto_fill_default, taxonomy_order, taxonomy_orderby, title, auto_fill_sort, hide_empty, ancestors, can_force_zero, force_zero, format )
{
	var self = this;

	self.control = control;

	self.group = group;

	self.index = index;

	self.kind = kind;

	self.prefix = prefix;

	self.id = ko.observable(id);

	self.field = ko.observable( value );

	self.name = name;

	self.url_param = ko.observableArray( url_param ? url_param : [] );

	self.type = ko.observable( type );

	self.data_type = ko.observable( data_type );

	self.user_values = ko.observableArray( values );

	self.default_label = ko.observable( default_label ? default_label : null );

	self.ancestors = ko.observable( ancestors ? ancestors : null );

	self.fieldDbName = ko.computed(function(){
		if( !self.field() ) return '';
		return self.kind == 'field' ? 'custom-' + self.kind + '-' + self.field()  : self.kind + '-' + self.field();
	});

	self.relation = relation;

	self.custom = custom;

	//needed as a switch to avoid conflicts in knockout
	self.field_type_switch = type;

	self.auto_fill_default = ko.observable( auto_fill_default ? auto_fill_default : '' );

	self.auto_fill = ko.observable( auto_fill ? auto_fill : 0 ) ;

	self.can_force_zero = can_force_zero;
	self.force_zero = ko.observable( force_zero ? force_zero : 0 );

	self.is_types = is_types;

	self.mode = ( kind == 'field' ) ? 'cf' : 'slug';
	self.mode = ( kind == 'relationship' ) ? 'rel' : self.mode;

	self.taxonomy_order = ko.observable(taxonomy_order);

	self.taxonomy_orderby = ko.observable(taxonomy_orderby);

	self.hide_empty = ko.observable(hide_empty);

	self.format = ko.observable(format);

	self.compare = ko.observable( compare ? compare : '' );

	self.use_user_values = ko.observable( values && values.length > 0 ? true : false);

	self.title = ko.observable( title );

	self.auto_fill_sort = ko.observable( auto_fill_sort );
};

/**
 * Compund Object to store fields by field Kind (eg. Taxonomies, Custom Fields, ...).
 *
 */
var WPV_GroupOption = function( label, children ) {

	this.name = ko.observable( label );

	this.label = ko.computed( function(){
		var $return = this.name();
		if ( $return == 'taxonomy' ) {
			$return = WPV_Parametric.taxonomy;
		}
		if ( $return == 'basic_filters' ) {
			$return = WPV_Parametric.basic_filters;
		}
		return $return;
	}, this);

	this.children = ko.observableArray(children);
};


/* ****************************************************************************\
 *		View Model
\* ****************************************************************************/

var WPV_ParametricViewModel = function() {

	// TODO:remove json store
	var self = this,
		meta_data_root = jqp.extend( true, {}, WPV_TypesFieldsType.Data ),
		store = new WPV_ParametricJsonStore();

	self.is_populating = false;

	// url parameter observable object
	self.WPV_QS = function(i){
		this.value = ko.observable( i );
	};


	self.WPV_Value = function(value, display) {

		var wpv_value = this;
		this.values = ko.observable( value );
		this.display_values = ko.observable( display );

		this.values.subscribe(function( val ){
			if( val ) {
				if( WPV_ParametricFilterWindow.errorPlaceHolder ) {
					//WPV_ParametricFilterWindow.errorPlaceHolder.wpvMessageRemove();
				}
				// remove field highlighting when this value changes
				jqp('.js-user-values').css('border-color', '#dfdfdf');
			}
		});


		this.display_values.subscribe( function ( newValue ) {
			if( newValue ) {
				// remove field highlighting when this value changes
				jqp('.js-user-display-values').css( 'border-color', '#dfdfdf' );
			}
		});


		this.values_raw = ko.computed( function() {
			if( wpv_value.values() && wpv_value.values() != " " ) {
				try	{
					wpv_value.values( wpv_value.values().trim() );
				} catch( e ) {
					wpv_value.values( wpv_value.values()[0].trim() );
				}
			}
		});


		wpv_value.display_values_raw = ko.computed(function(){
			if( wpv_value.display_values() && wpv_value.display_values() != " " )
			{
				try	{
					wpv_value.display_values( wpv_value.display_values().trim() );
				} catch( e ) {
					console.error( e.message );
				}
			}
		});

	};


	self.numeric_operators = ["NUMERIC", "DECIMAL", "SIGNED", "UNSIGNED"];

	// the actual field selected and all its data
	self.fieldRaw = ko.observable();

	// store here all our self.WPV_QS objects
	self.url_param = ko.observableArray();

	//scalar place holders in model
	self.field_data_type = ko.observable();
	self.auto_fill_default = ko.observable( '' );
	self.type = ko.observable();
	self.compare = ko.observable();
	self.title = ko.observable();

	self.values = ko.observableArray();

	self.userValuesVisible = ko.observable(false);
	self.auto_fill_default_visible = ko.observable(false);

	// data place holders
	self.selectFilter = ko.observableArray( );
	self.selectCompare = ko.observableArray( ['=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'AND', 'BETWEEN', 'NOT BETWEEN'] );
	self.selectDataType = ko.observableArray( ['CHAR', 'NUMERIC', 'BINARY', 'DATE', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED']);
	self.selectInputKind = ko.observableArray(["textfield", "select", "multi-select", "checkboxes", "checkbox", "radios"]);

	self.auto_fill = ko.observable(0);
	self.default_label = ko.observable('');

	self.force_zero = ko.observable(0);
	self.can_force_zero = true;

	self.ancestors_array = [ {
			title: WPV_Parametric.relationship_select_tree,
			value : 0 } ];

	// fill self.ancestors_array
	var myval = jqp('.js-flatten-types-relation-tree').val().split(',');
	for (i = 0, l = myval.length; i < l; i++) {
		self.ancestors_array.push( {
				title: myval[ i ],
				value: myval[ i ] } );
	}

	// TODO this does the same as above - remove duplicity
	self.ancestors_array_func = function( data ) {
		var myinnerval = jqp('.js-flatten-types-relation-tree').val().split(',');
		self.ancestors_array = [ {
				title: WPV_Parametric.relationship_select_tree,
				value: 0 } ];
		for (i = 0, l = myinnerval.length; i < l; i++) {
			self.ancestors_array.push( {
					title: myinnerval[ i ],
					value: myinnerval[ i ]});
		}
	};


	self.ancestors = ko.observable('');

	self.showPreview = ko.observable("");

	self.taxonomyOrder = [
			{value:"ASC", title:"Ascending"},
			{value:"DESC", title:"Descending"} ];

	self.taxonomyOrderBy = [
			{value:"name", title:"Name"},
			{value:"id", title:"ID"},
			{value:"count", title:"Post count"},
			{value:"slug", title:"Slug"},
			{value:"term_group", title:"Term group"},
			{value:"none", title:"None"} ];

	self.taxonomy_order = ko.observable();
	self.taxonomy_orderby = ko.observable();

	/*	self.hideEmptyOptions = [
		{value:"false", title:"Include - list terms even if no post uses them"},
		{value:"true", title:"Exclude - only list terms which are assigned to posts"}
	];
	self.hide_empty_array = ko.observableArray([ 'false','true' ]);	*/

	self.hide_empty = ko.observable();
	self.format = ko.observable();
	self.show_remove_user_values_box = ko.observable(true);
	self.checkbox_title_visible = ko.observable(false);
	self.checkbox_force_zero_visible = ko.observable(true);

	self.selectAutoFillSort = [
			{value:"asc", title:"Ascending"},
			{value:"desc", title:"Descendig"},
			{value:"ascnum", title:"Ascending Numeric"},
			{value:"descnum", title:"Descending Numeric"},
			{value:"none", title:"No sorting"} ];

	self.auto_fill_sort = ko.observable();


	// === Subscriptions to observables and computed properties ===

	self.fieldRaw.subscribe( function( field ) {

		try	{
			if ( typeof field.kind == 'undefined' ) {
				jqp( "#js_parametric_form_button" )
					.prop( 'disabled', true )
					.addClass( 'button-secondary' )
					.removeClass( 'button-primary' );
			} else {
				jqp( "#js_parametric_form_button" )
					.prop( 'disabled', false )
					.addClass( 'button-primary' )
					.removeClass( 'button-secondary' );

				if( field.kind == 'field'
					&& ( self.type() != 'textfield' || self.type() != 'checkbox' )
					&& self.values().length == 0  )
				{
					field.auto_fill( field.field() );
				} else {
					field.auto_fill( 0 );
				}

				var extra_query = '&field=' + field.field() + '&type=' + self.type();
				jqp('.js-wpv-auto-fill-default').unbind('keydown').suggest(
						ajaxurl + '?action=wpv_suggest_auto_fill_default' + extra_query,
						{
							minchars: 1,
							maxCacheSize: 0,
							max_size: 0,
							onSelect: function() {
								thevalue = this.value;
								jqp('.js-wpv-auto-fill-default').val( thevalue );
							}
						});
			}
		}
		catch( e )
		{
			//if(  WPV_Parametric.debug ) console.log( e.message, field )
		}
		//if(  WPV_Parametric.debug ) console.log("\n\n______________________________\n\n");
		//if(  WPV_Parametric.debug ) console.log("self.fieldRaw:: ", ko.toJS( field ) );
		return field;
	});


	self.title.subscribe( function( newVal ) {

		var field = typeof self.fieldRaw() != 'undefined' ? self.fieldRaw() : undefined;

		try	{
			field.title( newVal );
		} catch(e) {
			//	if(  WPV_Parametric.debug ) console.log( e.message );
		}
		return newVal;
	});


	self.ancestors_visible = ko.computed(function(){

		var field = typeof self.fieldRaw() != 'undefined' ? self.fieldRaw() : undefined,
			bool = false;

		try	{
			if( field.kind == 'relationship' )	{
				bool = true;
			} else	{
				bool = false;
				field.ancestors( null );
			}
		} catch( e )	{
			//if(  WPV_Parametric.debug ) console.log(e.message);
		}
		//if(  WPV_Parametric.debug ) console.log("self.default_label_visible:: ", bool );
		return bool;
	});


	self.ancestors.subscribe(function(value){

		var field = typeof self.fieldRaw() != 'undefined' ? self.fieldRaw() : undefined;

		try	{
			field.ancestors(value)
		} catch( e ) {
			//if(  WPV_Parametric.debug ) console.log( e.message );
		}
		//if(  WPV_Parametric.debug ) console.log("self.default_label.subscribe:: ", value );
		return value;
	});


	self.default_label_visible = ko.computed( function() {

		var field = typeof self.fieldRaw() != 'undefined' ? self.fieldRaw() : undefined,
			bool = false;
		try	{
			if( ( field.type() == 'select' || field.type() == 'radios' )
				&& ( field.kind == 'taxonomy' ) )
			{
				bool = true;
			} else	{
				bool = false;
				field.default_label( null );
			}
		} catch( e ) {
			//if(  WPV_Parametric.debug ) console.log(e.message);
		}
		//if(  WPV_Parametric.debug ) console.log("self.default_label_visible:: ", bool );
		return bool;
	});


	self.default_label.subscribe( function( value ){

		var field = typeof self.fieldRaw() != 'undefined' ? self.fieldRaw() : undefined;

		try	{
			field.default_label(value)
		} catch( e ) {
			//if(  WPV_Parametric.debug ) console.log( e.message );
		}
		//if(  WPV_Parametric.debug ) console.log("self.default_label.subscribe:: ", value );
		return value;
	});


	self.ancestors.subscribe( function( value ) {

		var field = typeof self.fieldRaw() != 'undefined' ? self.fieldRaw() : undefined;

		try	{
			field.ancestors(value)
		} catch( e ) {
			//if(  WPV_Parametric.debug ) console.log( e.message );
		}
		//if(  WPV_Parametric.debug ) console.log("self.default_label.subscribe:: ", value );
		return value;
	});


	/**
	 * Computes whether settings for input values should be visible.
	 *
	 * That means the section under "Options for this input", and settings like "Format the options", etc.
	 * While computing it also updates other properties related to dialog element visibility.
	 *
	 * @return true when Value settings should be visible, false if they shouldn't.
	 */
	self.show_values_settings = ko.computed( function() {

		var types_with_values = ['select', 'checkboxes', 'radios','radio', "multi-select"],
			field = ( typeof self.fieldRaw() != 'undefined' ) ? self.fieldRaw() : undefined,
			bool = false;

		try	{
			if( field && ( field.kind == "taxonomy" || field.kind == 'relationship' ) )	{

				self.userValuesVisible( false );
				self.auto_fill_default_visible( false );
				self.checkbox_title_visible( false );
				self.checkbox_force_zero_visible( false );

			} else if( ~types_with_values.indexOf( self.type() ) && field.kind == 'field' ) {
				// We're editing filter for a field with defined possible values.

				if( !self.is_populating ) {

					self.auto_fill_default_visible(
							field.type() == "multi-select" || field.type() == "checkboxes" || field.type() == 'select' || field.type() == 'radio' || field.type() == 'radios' );
					self.checkbox_title_visible( field.type() == "checkbox" );
					self.checkbox_force_zero_visible( field.type() == "checkbox" && field.can_force_zero );

				} else if( self.is_populating ) {

					self.auto_fill_default_visible( field.type() == "multi-select" || field.type() == "checkboxes" || field.type() == 'select' || field.type() == 'radio' || field.type() == 'radios'  );
					self.checkbox_title_visible( field.type() == "checkbox" );
					self.checkbox_force_zero_visible( field.type() == "checkbox" && field.can_force_zero );
				}

				// Initialize array of values with a blank one.
				self.values( [ new self.WPV_Value( "", "Select" ) ] );
				bool = true;

			} else {

				self.auto_fill_default_visible( false );
				self.checkbox_title_visible( false );
				//self.checkbox_force_zero_visible( false );

				if( !self.is_populating ) {

					self.auto_fill_default_visible( field.type() == "multi-select" || field.type() == "checkboxes" || field.type() == 'select' || field.type() == 'radio' || field.type() == 'radios' );
					self.checkbox_title_visible( field.type() == "checkbox" );
					self.checkbox_force_zero_visible( field.type() == "checkbox" && field.can_force_zero );

				} else if( self.is_populating ) {

					self.auto_fill_default_visible( field.type() == "multi-select" || field.type() == "checkboxes" || field.type() == 'select' || field.type() == 'radio' || field.type() == 'radios' );
					self.checkbox_title_visible( field.type() == "checkbox" );
					self.checkbox_force_zero_visible( field.type() == "checkbox" && field.can_force_zero );

				}

				self.auto_fill( 1 );
				self.values();
				bool = false;
			}
		} catch( e ) {
			//if(  WPV_Parametric.debug ) console.log( e.message );
		}
		//if(  WPV_Parametric.debug ) console.log("self.show_values_settings:: ", bool );
		return bool;
	});


	// FIXME: we have a big problem here we're patching
	self.auto_fill.subscribe( function( val ) {

		var field = typeof self.fieldRaw() != 'undefined' ? self.fieldRaw() : undefined;

		if( !self.is_populating ) {

			try {
				if( self.type() == 'textfield' || self.type() == 'checkbox' ) {
					val = 0;
				}

				if( field.kind == "taxonomy" || field.kind == 'relationship' ) {

					self.userValuesVisible( false );
					self.auto_fill_default_visible( false );
					self.checkbox_title_visible( false );
					self.checkbox_force_zero_visible( false );
					field.auto_fill( 0 );

				} else {

					self.userValuesVisible( val == 1 || ( self.type() == 'textfield' || field.type() == "checkbox" ) ? false : true );

					self.auto_fill_default_visible(
							( field.type() == "multi-select" || field.type() == "checkboxes" || field.type() == 'select' || field.type() == 'radio' || field.type() == 'radios')
							&& val == 1 );
					self.checkbox_title_visible( field.type() == "checkbox" && val == 1 );
					self.checkbox_force_zero_visible( field.type() == "checkbox" && field.can_force_zero );

					//FIXME: make sure the commented part is not needed
					if( val == 1 && self.values().length == 0 ) {
						field.auto_fill( field.field() );
						self.auto_fill_default_visible( false );
						self.checkbox_title_visible( false );
					} else {
						field.auto_fill( 0 );
					}
				}

				field.use_user_values( self.userValuesVisible() );

				field.auto_fill( field.use_user_values() ? 0 : field.field() );

				self.auto_fill_sort_visible( val == 1 ? true : false );

				field.auto_fill_sort = ( val == 1 ) ? self.auto_fill_sort() : undefined;

			} catch( e ) {
				//if(  WPV_Parametric.debug ) console.log( e.message );
			}

		} else if( self.is_populating && field && ( field.type() == 'checkbox' || field.type() == 'radio' ) ) {
			val = 1;
		}

		//	if(  WPV_Parametric.debug ) console.log("self.auto_fill.subscribe:: ", val );
		return val;
	});


	self.auto_fill_default.subscribe( function( val ) {

		var field = typeof self.fieldRaw() != 'undefined' ? self.fieldRaw() : undefined;

		try{
			field.auto_fill_default( val );
		} catch(e) {
			//if(  WPV_Parametric.debug ) console.log( e.message );
		}
		//if(  WPV_Parametric.debug ) console.log("self.auto_fill_default.subscribe:: ", val );
		return val;
	});


	self.auto_fill_default_visible.subscribe( function( val ) {
		try {
			if( !val ) {
				self.auto_fill_default('');
			}
		} catch( e ) {
			//if(  WPV_Parametric.debug ) console.log( e.message );
		}
		return val;
	});


	// field string representation in viewmodel
	self.field = ko.computed( function() {
		var field = ( typeof self.fieldRaw() != 'undefined' ) ? self.fieldRaw() : undefined;

		if( !field ) {
			return '';
		}

		if( !field.is_types && self.selectInputKind()[0] == "Types auto style" ) {
			self.selectInputKind.shift();
		}
		//if(  WPV_Parametric.debug ) console.log("self.field:: ", field.field() ? field.field() : 'NO FIELD' );
		return field.field() ? field.field() : '';
	});


	self.selectInputKind.subscribe( function( val ) {
		return val;
	});


	self.values.subscribe( function( val ) {
		var field = ( typeof self.fieldRaw() == 'object' ) ? self.fieldRaw() : undefined,
			count = 0;

		try{
			if( ko.toJS( val ).value != '' ) {
				field.user_values( val );
			}
		} catch(e) {
			//if(  WPV_Parametric.debug ) console.log(e.message);
		}
		//if(  WPV_Parametric.debug ) console.log("self.values.subscribe:: ", val );
		return val;
	});


	self.add_user_values_box = function() {
		var field = ( typeof self.fieldRaw() == 'object' ) ? self.fieldRaw() : undefined,
			count = 0;

		var newValue = new self.WPV_Value(" ", " ");
		self.values.push( newValue );

		self.show_remove_user_values_box( ( self.values().length > 0 ) ? true : false );
		//if(  WPV_Parametric.debug ) console.log("self.add_user_values_box:: ", self.values().length > 0 ? true : false  );
	};


	self.remove_user_values_box = function( place ) {
		var field = ( typeof self.fieldRaw() == 'object' ) ? self.fieldRaw() : undefined, count = 0;
		self.values.remove( place );
		self.show_remove_user_values_box( ( self.values().length > 0 ) ? true : false );
		//if(  WPV_Parametric.debug ) console.log("self.remove_user_values_box:: ", self.values().length > 0 ? true : false );
	};

	self.url_param_raw = ko.computed({
		read: function() {
			var field = ( typeof self.fieldRaw() == 'object' ) ? self.fieldRaw() : undefined,
				ret = '';

			try	{
				//if(  WPV_Parametric.debug ) console.log( "URLPARAM:::::::::::", ko.toJS( self.url_param ), ko.toJS( field.url_param ) );
				if( field && field.control ) {
					if( ~field.control.indexOf(',') ) {
						field.url_param( [
								new self.WPV_QS( field.control.split( ',' )[0].trim() ),
								new self.WPV_QS( field.control.split( ',' )[1].trim() ) ] );
						self.url_param( [
								new self.WPV_QS( field.control.split( ',' )[0].trim() ),
								new self.WPV_QS( field.control.split( ',' )[1].trim() ) ] );
					} else {
						field.url_param( [ new self.WPV_QS( field.control ) ] );
						self.url_param( [ new self.WPV_QS( field.control ) ] );
					}
					//if(  WPV_Parametric.debug ) console.log("ENTERS FIRST ", ko.toJS(self.url_param), ko.toJS(field.url_param) );
				} else if ( field && self.is_populating == true ) {
					self.url_param( field.url_param() );
					//if(  WPV_Parametric.debug ) console.log("ENTERS SECOND ", ko.toJS(self.url_param), ko.toJS(field.url_param) );
				} else if( field.url_param().length == 0 ) {
					ret = field.prefix + field.id();

					field.url_param( [ new self.WPV_QS( ret ) ] );

					self.url_param( field.url_param() );

					//if(  WPV_Parametric.debug ) console.log("ENTERS THIRD ", ko.toJS(self.url_param), ko.toJS(field.url_param) );
				} else if( self.url_param() != field.url_param() ) {
					field.url_param( self.url_param() );

					//if(  WPV_Parametric.debug ) console.log("ENTERS FoURTH ", ko.toJS(self.url_param), ko.toJS(field.url_param) );
				}
				//if(  WPV_Parametric.debug ) console.log( "::::::::::::: self.url_param_raw ::::::::::::::::", ko.toJS(self.url_param), ko.toJS(field.url_param) );
			} catch( e ) {
				//if(  WPV_Parametric.debug ) console.log( e.message );
			}
			return ret;
		}
	});


	//see if our current object in array is modified and update selected object with new value
	self.url_param.subscribe( function( val ) {
		var field = ( typeof self.fieldRaw() == 'object' ) ? self.fieldRaw() : undefined,
			count = 0;
		try{
			field.url_param( val );
		} catch(e) {
			//if(  WPV_Parametric.debug ) console.log(e.message);
		}
		//if(  WPV_Parametric.debug ) console.log("self.url_param.subscribe:: " , ko.toJS(val) );
		return val;
	});


	//FIXME: we have a big problem here we're patching
	self.field_data_type_raw = ko.computed( function() {
		var field = ( typeof self.fieldRaw() == 'object' ) ? self.fieldRaw() : undefined,
			newValue = 'CHAR';
		try	{
			newValue = field.data_type();
			//if(  WPV_Parametric.debug ) console.log( "Data type ", newValue, field.data_type(), jqp.inArray( newValue, self.numeric_operators ) )
			if( ~jqp.inArray( newValue, self.numeric_operators ) ) {
				self.selectCompare( meta_data_root[ field.kind ]['numeric']['compare'] );
			} else {
				self.selectDataType( meta_data_root[ field.kind ][ field.field_type_switch ]['data_type'] );
			}
		} catch(e) {
			//if(  WPV_Parametric.debug ) console.log(e.message);
		}

		self.field_data_type( newValue );
		//if(  WPV_Parametric.debug ) console.log("self.field_data_type_raw:: ", newValue );
		return newValue;
	});


	self.field_data_type.subscribe( function( val ) {
		var field = ( typeof self.fieldRaw() == 'object' ) ? self.fieldRaw() : undefined,
			newVal = val;
		try {
			field.data_type( newVal );
		} catch(e) {
			//if(  WPV_Parametric.debug ) console.log(e.message);
		}
		//if(  WPV_Parametric.debug ) console.log("self.field_data_type.subscribe:: ", newVal );
		return newVal;
	});


	self.type_raw = ko.computed( function(){
		var field = ( typeof self.fieldRaw() == 'object' ) ? self.fieldRaw() : undefined,
			newValue = 'textfield';

		try	{
			if( self.is_populating && field.field_type_switch == "Types auto style" ) {
				newValue = field.field_type_switch;
				self.type( newValue );
				return newValue;
			}

			if( field.is_types && meta_data_root[ field.kind ][ field.field_type_switch ]['type'][0] != "Types auto style" ) {

				meta_data_root[ field.kind ][ field.field_type_switch ]['type'].unshift( "Types auto style" );

			} else if( !field.is_types && meta_data_root[ field.kind ][ field.field_type_switch ]['type'][0] == "Types auto style" ) {

				meta_data_root[ field.kind ][ field.field_type_switch ]['type'].shift();
			}

			if( !self.is_populating ) {
				newValue = meta_data_root[ field.kind ][ field.field_type_switch ]['type'][0];
				field.type( newValue );
			} else {
				newValue = field.field_type_switch;
			}

			if( newValue != 'textfield' ) {
				field.auto_fill( field.field() );
			}

			self.selectInputKind( meta_data_root[ field.kind ][ field.field_type_switch ]['type'] );
		} catch(e) {
			//	if(  WPV_Parametric.debug ) console.log(e.message);
		}

		self.type( newValue );
		//if(  WPV_Parametric.debug ) console.log("self.type_raw:: ", newValue );
		return newValue;
	});


	self.compare_raw = ko.computed( function() {
		var field = ( typeof self.fieldRaw() == 'object' ) ? self.fieldRaw() : undefined,
			newValue = '=',
			tmp;
		try	{
			tmp = ( field.field_type_switch == 'Types auto style' ) ? 'textfield' : field.field_type_switch;

			if( ~jqp.inArray( field.data_type(), self.numeric_operators ) ) {
				self.selectCompare( meta_data_root[field.kind]['numeric']['compare'] );
			} else {
				self.selectCompare( meta_data_root[ field.kind ][ tmp ]['compare'] );
			}

			if( !self.is_populating && !field.compare() ) {
				newValue = jqp.inArray( self.compare(), meta_data_root[ field.kind ][ tmp ] )
						? self.compare()
						: meta_data_root[ field.kind ][ tmp ]['compare'][0];
				field.compare( newValue );
			} else {
				newValue = field.compare();
			}
		} catch(e) {
			//if(  WPV_Parametric.debug ) console.log("In compare computed", e.message);
		}
		//TODO:figure out if we need this one
		//	if( self.compare() != newValue ) self.compare( newValue );
		return newValue;
	});


	self.type.subscribe( function( val ) {
		var field = typeof self.fieldRaw() == 'object' ? self.fieldRaw() : undefined, newVal = val, tmp, hide_user_values = ["textfield", "checkbox", "radios", "radio"];

		try	{
			newVal = ( typeof newVal == 'undefined' ) ? field.type() : newVal;
			tmp = ( newVal == 'Types auto style' ) ? 'textfield' : newVal;
			field.type( newVal );

			if( field.kind == 'field' && self.type() != 'textfield' && !self.is_populating ) {
				field.auto_fill( field.field() );
				self.auto_fill( 1 );
			} else {
				field.auto_fill( 0 );
			}

			self.selectCompare( meta_data_root[ field.kind ][ tmp ]['compare'] );

			if( !self.is_populating && !field.compare() ) {
				field.compare( self.compare() );
			}

			if( self.is_populating && field && ( field.type() == 'checkbox' || field.type() == 'radio' ) )	{
				self.auto_fill( 1 );
			}

			if( ~jqp.inArray( newVal, hide_user_values ) ) {
				self.userValuesVisible(false);
				field.use_user_values(false);
			}

			var extra_query = '&field=' + field.field() + '&type=' + field.type();
			jqp('.js-wpv-auto-fill-default').unbind('keydown').suggest(
					ajaxurl + '?action=wpv_suggest_auto_fill_default' + extra_query,
					{
						minchars: 1,
						maxCacheSize: 0,
						max_size: 0,
						onSelect: function() {
							thevalue = this.value;
							thevalue = thevalue.split(' #');
							jqp('.js-wpv-auto-fill-default').val( thevalue[0] );
						}
					});

		} catch(e) {
			//if(  WPV_Parametric.debug ) console.log(e.message);
		}

		return newVal;
	});


	self.compare.subscribe( function( val ) {
		var field = ( typeof self.fieldRaw() == 'object' ) ? self.fieldRaw() : undefined,
			newVal = val;

		try{
			//if(  WPV_Parametric.debug ) console.log( "CHECK IT:: ", field, field.control)
			if( !self.is_populating && ( typeof field.control == 'undefined' ) ) {
				field.compare( newVal );

				if( ko.toJS(field.compare) == 'BETWEEN' || ko.toJS(field.compare) == 'NOT BETWEEN' ) {

					self.url_param( [
							new self.WPV_QS( field.prefix + field.id() + '_min' ),
							new self.WPV_QS( field.prefix + field.id()  + '_max' ) ] );

				} else {
					if( self.url_param().length > 1 ) {
						self.url_param.pop();
						self.url_param( [
								new self.WPV_QS( field.prefix + field.id() ) ] );
					}
				}
			}
		} catch(e)	{
			//if(  WPV_Parametric.debug ) console.log(e.message);
		}
		return newVal;
	});


	//not used now
	self.setPreview = ko.computed({
		read:function()
		{
			var field = typeof self.fieldRaw() == 'object' ? self.fieldRaw() : undefined, str = '';

			if( !field ) return WPV_Parametric.preview_text_default;

			try
			{
				if( field.kind == 'field' )
				{

					var len = field.url_param().length;

					str += field.field() + ' ' + field.compare() + ' ';

					self.check_validation_for_url_param( field.url_param() );

					for(var i = 0; i < len; i++)
					{
						str += 'URL_PARAM('+field.url_param()[i].value()+')';
						str += i == len - 1 ? '' : ', ';
					}
					return str;
				}
				else if( field.kind == 'taxonomy' )
				{
					str += WPV_Parametric.select_taxonomy_alert+' <strong>'+field.field()+'</strong> '+WPV_Parametric.select_taxonomy_alert_2+' <strong>"'+field.url_param()[0].value()+'"</strong> eg. http://www.example.com/page/?<strong>'+field.url_param()[0].value()+'</strong>="xxxx"';
				}
				return str;

			}
			catch( e )
			{
				console.error( e.message );
				return WPV_Parametric.error_building_filter + e.message;
			}
			//if(  WPV_Parametric.debug ) console.log("self.setPreview :: " );
			return str;
		}

	});


	self.check_validation_for_url_param = function( val )
	{
		if( WPV_ParametricFilterWindow.has_error_displayed == true )
		{
			var urls = val, against = wpv_forbidden_parameters, problems = false;

			jqp.each(against, function(index, value) {
				try
				{
					jqp.each( urls, function(i, p) {
						if( ~jqp.inArray( p.value(), value ) )
						{
							problems = true;
							WPV_ParametricFilterWindow.has_error_displayed = true;
							jqp('#url_param').css('border-color', 'red');
							return false;
						}
						else
						{
							jqp('#url_param').css('border-color', '#dfdfdf');
							WPV_ParametricFilterWindow.has_error_displayed = false;
							//WPV_ParametricFilterWindow.errorPlaceHolder.wpvMessageRemove();
							problems = false;
						}
					});

					if( problems ) return false;
				}
				catch( e )
				{
					//if(  WPV_Parametric.debug ) console.log( e.message );
				}
			});
		}
	};


	self.taxonomy_order_visible = ko.computed(function(){
		var field = typeof self.fieldRaw() != 'undefined' ? self.fieldRaw() : undefined, bool = false;
		try
		{
			if( field.kind == 'taxonomy' )
			{
				bool = true;
			}
			else
			{
				bool = false;
				field.taxonomy_order( '' );
				field.taxonomy_orderby( '' );
				field.hide_empty( '' );
			}

			if( field.control ) field.control = undefined;

		}
		catch( e )
		{
			//if(  WPV_Parametric.debug ) console.log(e.message);
		}
		//if(  WPV_Parametric.debug ) console.log("self.taxonomy_order_visible :: ", field ? field.control : "NO FIELD" );
		return bool;
	});


	self.auto_fill_sort_visible = ko.observable(false);


	self.auto_fill_sort_visible_computed = ko.computed(function(){

		var field = typeof self.fieldRaw() != 'undefined' ? self.fieldRaw() : undefined;

		if( typeof field == 'undefined') self.auto_fill_sort_visible(false);

		try
		{

			if(
				field.kind == "field"
				&& ( field.type() == "multi-select" ||
				field.type() == "select" ||
				field.type() == "checkboxes" ||
				field.type() == "radios"
				) )
				{
					self.auto_fill_sort_visible( true );
					if( !self.is_populating ){
						field.auto_fill_sort = self.auto_fill_sort();
					}
					else
					{
						self.auto_fill_sort( field.auto_fill_sort );
					}
				}
				else
				{
					self.auto_fill_sort_visible( false );
					field.auto_fill_sort = undefined;
				}

		}
		catch( e )
		{
			//if(  WPV_Parametric.debug ) console.log( e.message );
		}
	});


	self.auto_fill_sort.subscribe(function(value){
		var field = typeof self.fieldRaw() != 'undefined' ? self.fieldRaw() : undefined;
		try
		{
			field.auto_fill_sort = value;
		}
		catch( e )
		{
			//if(  WPV_Parametric.debug ) console.log(e.message)
		}
		return value;
	});


	self.taxonomy_order.subscribe(function(value){
		var field = typeof self.fieldRaw() != 'undefined' ? self.fieldRaw() : undefined;

		try
		{
			field.taxonomy_order(value)
		}
		catch( e )
		{
			//if(  WPV_Parametric.debug ) console.log( e.message );
		}

		return value;
	});


	self.taxonomy_orderby.subscribe(function(value){
		var field = typeof self.fieldRaw() != 'undefined' ? self.fieldRaw() : undefined;

		try
		{
			field.taxonomy_orderby(value)
		}
		catch( e )
		{
			//if(  WPV_Parametric.debug ) console.log( e.message );
		}

		return value;
	});


	self.hide_empty.subscribe(function(value){
		var field = typeof self.fieldRaw() != 'undefined' ? self.fieldRaw() : undefined;

		try
		{
			field.hide_empty(value)
		}
		catch( e )
		{
			//if(  WPV_Parametric.debug ) console.log( e.message );
		}

		return value;
	});


	self.format.subscribe(function(value){
		var field = typeof self.fieldRaw() != 'undefined' ? self.fieldRaw() : undefined;

		try
		{
			field.format(value)
		}
		catch( e )
		{
			//if(  WPV_Parametric.debug ) console.log( e.message );
		}

		return value;
	});


	self.force_zero.subscribe(function(value){
		var field = typeof self.fieldRaw() != 'undefined' ? self.fieldRaw() : undefined;

		try
		{
			field.force_zero(value)
		}
		catch( e )
		{
			//if(  WPV_Parametric.debug ) console.log( e.message );
		}

		return value;
	});

};

var WPV_TypesFieldsType = {
	"Data" : {
		"relationship" : {
			"textfield" :{
				"type":["select", "multi-select", "checkboxes", "radios"],
				"data_type" : ["CHAR"],
				//	"compare" : ["IN", "=", "NOT IN", "!=", "LIKE", "NOT LIKE"]
				"compare" : ["IN"]
			},
			"select" :{
				"type":["select", "multi-select", "checkboxes", "radios"],
				"data_type" : ["CHAR"],
				//	"compare" : ["IN", "=", "NOT IN", "!=", "LIKE", "NOT LIKE"]
				"compare" : ["IN"]
			},
			"multi-select" :{
				"type":["multi-select", "select", "checkboxes", "radios"],
				"data_type" : ["CHAR"],
				//"compare" : ["IN", "NOT IN"]
				"compare" : ["IN"]
			},
			"checkboxes" :{
				"type":["checkboxes", "select", "multi-select", "radios"],
				"data_type" : ["CHAR"],
				//	"compare" : ["IN", "=", "NOT IN", "!=", "LIKE", "NOT LIKE"]
				"compare" : ["IN"]
			},
			"radios" :{
				"type":[ "radios", "select", "multi-select", "checkboxes"],
				"data_type" : ["CHAR"],
				//	"compare" : ["IN", "=", "NOT IN", "!=", "LIKE", "NOT LIKE"]
				"compare" : ["IN"]
			},
		},
		"taxonomy" : {
			"textfield" :{
				"type":["select", "multi-select", "checkboxes", "radios"],
				"data_type" : ["CHAR"],
				//	"compare" : ["IN", "=", "NOT IN", "!=", "LIKE", "NOT LIKE"]
				"compare" : ["IN", "NOT IN", "AND"]
			},
			"select" :{
				"type":["select", "multi-select", "checkboxes", "radios"],
				"data_type" : ["CHAR"],
				//	"compare" : ["IN", "=", "NOT IN", "!=", "LIKE", "NOT LIKE"]
				"compare" : ["IN", "NOT IN", "AND"]
			},
			"multi-select" :{
				"type":["multi-select", "select", "checkboxes", "radios"],
				"data_type" : ["CHAR"],
				//"compare" : ["IN", "NOT IN"]
				"compare" : ["IN", "NOT IN", "AND"]
			},
			"checkboxes" :{
				"type":["checkboxes", "select", "multi-select", "radios"],
				"data_type" : ["CHAR"],
				//	"compare" : ["IN", "=", "NOT IN", "!=", "LIKE", "NOT LIKE"]
				"compare" : ["IN", "NOT IN","AND"]
			},
			"radios" :{
				"type":[ "radios", "select", "multi-select", "checkboxes"],
				"data_type" : ["CHAR"],
				//	"compare" : ["IN", "=", "NOT IN", "!=", "LIKE", "NOT LIKE"]
				"compare" : ["IN", "NOT IN", "AND"]
			},
		},
		"field" : {
			"textfield" :{
				"type":["textfield", "select", "multi-select", "checkboxes", "checkbox", "radios", "date", "datepicker"],
				"data_type" : ["CHAR", "NUMERIC", "DATE", "DATETIME", "TIME"],
				"compare" : ["=", "!=", ">", ">=", "<", "<=", "LIKE", "NOT LIKE", "IN", "NOT IN", "BETWEEN", "NOT BETWEEN"]
			},
			"multi-select" :{
				"type":["multi-select", "checkboxes", "select", "textfield", "checkbox", "radios"],
				"data_type" : ["CHAR", "NUMERIC"],
				"compare" : ["IN", "NOT IN"]
			},
			"textarea" :{
				"type":["textfield", "select", "multi-select", "checkboxes", "checkbox", "radios"],
				"data_type" : ["CHAR"],
				"compare" : ["LIKE", "NOT LIKE"]
			},
			"date" :{
				"type":["date", "datepicker", "textfield"],
				"data_type" : ["NUMERIC"],
				"compare" : ["=", "!=", ">", "<", "BETWEEN", "NOT BETWEEN" ]
			},
			"datepicker" :{
				"type":["datepicker", "date", "textfield"],
				"data_type" : ["NUMERIC"],
				"compare" : ["=", "!=", ">", "<", "BETWEEN", "NOT BETWEEN" ]
			},
			"email" :{
				"type":["textfield","select", "multi-select", "checkboxes", "checkbox", "radios"],
				"data_type" : ["CHAR"],
				"compare" : ["=", "!=", "LIKE", "NOT LIKE"]
			},
			"file" :{
				"type":["textfield", "select", "radios"],
				"data_type" : ["CHAR"],
				"compare" : ["=", "!=", "LIKE", "NOT LIKE"]
			},
			"image" :{
				"type":["textfield", "select", "radios"],
				"data_type" : ["CHAR"],
				"compare" : ["=", "LIKE", "!=", "NOT LIKE"]
			},
			"numeric" :{
				"type":["textfield", "select", "multi-select", "checkboxes", "checkbox", "radios"],
				"data_type" : ["NUMERIC", "DECIMAL", "SIGNED", "UNSIGNED"],
				"compare" : ["=", "!=", ">", ">=", "<", "<=", "BETWEEN",  "NOT BETWEEN", "LIKE", "NOT LIKE"]
			},
			"phone" :{
				"type":["textfield", "select",  "hidden"],
				"data_type" : ["CHAR"],
				"compare" : ["=", "!=", "LIKE", "NOT LIKE"]
			},
			"select" :{
				"type":["select", "multi-select", "checkboxes", "radios", "textfield"],
				"data_type" : ["CHAR", "BOOLEAN", "NUMERIC"],
				"compare" : ["=", "!=", ">", ">=", "<", "<=", "LIKE", "NOT LIKE", "BETWEEN", "NOT BETWEEN"]
			},
			"skype" :{
				"type":["textfield", "select", "radios"],
				"data_type" : ["CHAR"],
				"compare" : ["=", "!=", "LIKE", "NOT LIKE"]
			},
			"url" :{
				"type":["textfield", "select", "radios"],
				"data_type" : ["CHAR"],
				"compare" : ["=", "!=", "LIKE", "NOT LIKE"]
			},
			"checkbox" :{
				"type":["checkbox", "select", "textfield", "multi-select", "checkboxes", "radios"],
				"data_type" : ["CHAR", "BOOLEAN", "NUMERIC"],
				"compare" : ["=", "!=", ">", ">=", "<", "<=", "LIKE", "NOT LIKE"]
			},
			"checkboxes" :{
				"type":["checkboxes", "multi-select", "radios", "select", "textfield", "checkbox"],
				"data_type" : ["CHAR", "NUMERIC"],
				"compare" : ["IN", "NOT IN", "=", "!=", ">", ">=", "<", "<="]
			},
			"radios" :{
				"type":[ "radios", "select", "multi-select", "checkboxes", "textfield", "checkbox"],
				"data_type" : ["CHAR", "NUMERIC"],
				"compare" : ["IN", "=", "NOT IN", "!=", "LIKE", "NOT LIKE", ">", ">=", "<", "<="]
			},
			"wysiwyg" :{
				"type":["textfield", "select", "multi-select", "checkboxes", "radios", "checkbox"],
				"data_type" : ["CHAR"],
				"compare" : ["LIKE", "NOT LIKE"]
			}
		}
	}
};

///////////////MODELS END ////////////

/*
* -------------------------------------
* ShortCodeParser
* -------------------------------------
*/

var ShortCodeParser = function( area ) {

	var self = this;

	self.area = area;
	self.editor = icl_editor ? icl_editor : undefined;

	self.isCM = false;
	self.isTiny = false;
	self.isFlat = false;

	self.NULL_CHAR = 0;
	self.TAG_OPEN = 91;
	self.TAG_CLOSE = 93;
	self.SLASH = 47;

	self.currentIndex = 0;
	self.moveRight = true;
	self.strLen = 0;

	self.shortCodeObject = null;
	self.shortcode = null;
	self.shortCodeRaw = null;

	self.cursor_was = 0;

	self.area_content = '';

	self.parse = function( txtarea ) {
		var area = txtarea ? txtarea : self.area;
		//each time we call this one we reset all vars
		self.shortcode = null;
		self.shortCodeObject = null;
		self.shortCodeRaw = null;

		self.currentIndex = 0;
		self.moveRight = true;
		self.strLen = 0;

		self.cursor_was = 0;

		self.area_content = '';
		self.area = area; /*overrides constructor settings if you want to use methods Statically*/

		if ( area ) {
			try {
				self.cm = self.editor.isCodeMirror( area );
				self.tiny = self.editor.isTinyMce( area );
			} catch( e ) {
				throw {
					name:        "Missing Dependency",
					message:     "Error detected. You are probably missing dependency with icl_editor object."
				};
			}

			if ( self.tiny ) {
				self.isTiny = true;
				self.manage_tiny();
			} else if ( self.cm ) {
				self.isCM = true;
				self.shortcode = self.manage_cm();
			} else {
				self.isFlat = true;
				self.manage_flat();
			}
		} else {
			throw {
				name:        "Missing Argument",
				message:     "Error detected. You should pass a valid textarea DOM object to constructor or init methods."
			};
		}
		return self.shortcode;
	}
};

ShortCodeParser.prototype.manage_cm = function() {
	var cm = this.cm,
	string = '',
	cursor,
	text_before = '',
	text_after = '',
	index = 0,
	content = '',
	strlen = 0;

	cm.focus();

	cursor = this.cmGetCursor();
	this.cursor_was = cursor;
	index = this.cmCursorIndex( cursor );
	content = this.getCmAreaContent( cursor );

	self.area_content = content;

	string = this.getStringShortCode( content, content.length,  index );

	if ( ! string ) {
		return null;
	}

	return string;
};

ShortCodeParser.prototype.getContent = function() {
	return self.content;
};

ShortCodeParser.prototype.getCursorWas = function() {
	return this.cursor_was;
};

ShortCodeParser.prototype.cmGetCursor = function() {
	var cm = this.cm;
	return cm.getCursor(false);
};

ShortCodeParser.prototype.cmCursorIndex = function( cursor ) {
	var cm = this.cm;
	index = cm.indexFromPos(cursor);
	return index;
};

ShortCodeParser.prototype.getCmAreaContent = function( cursor ) {
	var cm = this.cm;
	text_before = cm.getRange({line:0,ch:0}, cursor)
	text_after = cm.getRange(cursor, {line:cm.lastLine(),ch:null})
	content = text_before+text_after;
	return content;
};

ShortCodeParser.prototype.manage_tiny = function() {
	////if(  WPV_Parametric.debug ) console.log('is a tinyMCE area ', this.tiny);
};

ShortCodeParser.prototype.manage_flat = function() {
	////if(  WPV_Parametric.debug ) console.log('is default area ', this.area);
	////if(  WPV_Parametric.debug ) console.log( this.area.caret() );
};

ShortCodeParser.prototype.shortCodeGetTagName = function() {
	var ret = '';

	try {
		ret = this.shortcode.split(" ")[0];
	} catch( e ) {
		//if(  WPV_Parametric.debug ) console.log( e.message );
	}
	return ret;
};

ShortCodeParser.prototype.getShortCodeObject = function() {
	var obj = null, self = this, tmp = '', prev = '';

	try {
		obj = {};
		tmp = self.shortCodeRaw;
		tmp = tmp.replace( self.shortCodeGetTagName(), '' );

		jQuery.each( tmp.split('" '), function( i, v ) {
			try {
				var prop = v.split("=");
				jQuery.each( prop, function( index, value ) {
					if ( typeof prop[1] == 'undefined' /*&& ( prev == 'values' || prev == 'display_values' )*/ ) {
						obj[prev] = value.replace(/"/g, '');
					} else {
						obj[prop[0].trim()] = prop[1].replace(/"/g, '');
					}
					prev = prop[0].trim();
				});
			} catch( e ) {
				if(  WPV_Parametric.debug ) console.log( "Catches error", e.message );
				obj = null;
			}
		});
	} catch( e ) {
		//if(  WPV_Parametric.debug ) console.log( e.message );
		obj = null;
	}
	//if(  WPV_Parametric.debug ) console.log("ShortCodeParser.prototype.getShortCodeObject:: ", obj);
	return obj;
};

ShortCodeParser.prototype.getShortCodeRawString = function() {
	return this.shortCodeRaw;
};

ShortCodeParser.prototype.getStringShortCode = function( str, len, start ) {
	var charCodesArray, chr, prev_char, tmp = '';
	this.strLen = len;
	
	if ( str.charAt(start) == '[' ) {
		this.currentIndex = start-2;
		this.moveRight = false;
	} else {
		this.currentIndex = start;
	}

	charCodesArray = this.getTagLeft( str );

	tmp += charCodesArray.reverse( ).join('');

	if ( this.moveRight ) {
		this.currentIndex = start + 1;
		charCodesArray = this.getTagRight( str );
		tmp += charCodesArray.join('');
		//if( WPV_Parametric.debug ) console.log( "chr arr", charCodesArray, " chr str", tmp );
	}

	this.shortCodeRaw = tmp;

	tmp = tmp.replace(/"/g, '');

	return ( ~tmp.indexOf( ']' ) ) ? tmp.split( ']' )[0].removeExtraWhiteSpaces() : tmp.removeExtraWhiteSpaces();
};

ShortCodeParser.prototype.getPrevChar = function( str ) {
	return ( this.currentIndex >= 0 && str.charCodeAt( this.currentIndex  ) != this.TAG_OPEN ) ? str.charCodeAt( this.currentIndex-- ) : this.NULL_CHAR;
};

ShortCodeParser.prototype.getNextChar = function( str ) {
	return ( this.currentIndex < this.strLen && str.charCodeAt( this.currentIndex  ) != this.TAG_CLOSE) ? str.charCodeAt( this.currentIndex++ ) : this.NULL_CHAR;
};

ShortCodeParser.prototype.goLeft = function( str ) {
	return ( this.currentIndex >= 0 ) ? str.charCodeAt( this.currentIndex-- ) : this.NULL_CHAR;
};

ShortCodeParser.prototype.getCloseLeft = function( str ) {
	//
};

ShortCodeParser.prototype.getCloseTagIndex = function( str ) {
	//
};

ShortCodeParser.prototype.cursorInside = function() {
	var cursor = this.cmGetCursor(),
	index = this.cmCursorIndex( cursor ),
	content = this.getCmAreaContent( cursor ),
	chr;

	this.currentIndex = index-1;

	while ( chr = this.goLeft( content ) ) {
		if ( chr == this.TAG_CLOSE ) {
			return false;
		}
		if ( chr == this.TAG_OPEN ) {
			return true;
		}
	}
	return false;
};

ShortCodeParser.prototype.getTagLeft = function( str ) {
	var chr, charCodesArray = [];
	//get the left part of the string
	while ( chr = this.getPrevChar(str) ) {
		if ( chr == 47 ) {
			this.currentIndex = this.currentIndex - 2;
			charCodesArray = [];
			this.moveRight = false;
			continue;
		}
		charCodesArray.push( String.fromCharCode( chr ) );
	}
	return charCodesArray;
};

ShortCodeParser.prototype.getTagRight = function( str ) {
	var chr, charCodesArray = [];
	//get the left part of the string
	while ( chr = this.getNextChar( str ) ) {
		charCodesArray.push( String.fromCharCode( chr ) );
	}
	return charCodesArray;
};

ShortCodeParser.prototype.replace_tag_content = function( tag, short_code, nicename, content, editable, name ) {
	var rpl = '';

	try {
		//console.log( "Tag ", tag, " \nnew", short_code, " \nnice ", nicename, " \ncont", content, "\old ", editable, "\nname", name );
		rpl = content.replace( editable, tag+short_code );
		rpl = rpl.replace( name, nicename );
		this.cm.setSelection({ line:0, ch:0 }, { line:this.cm.lastLine(), ch:null } );
		this.cm.replaceSelection( rpl );
		this.cm.setSelection( this.getCursorWas() );
	} catch( e ) {
		//if(  WPV_Parametric.debug ) console.log( e.message );
	}
	return rpl;
};

ShortCodeParser.prototype.append_tag_content = function( editable, replace, content ) {
	var append = '';

	try {
		append = content.replace( editable, editable+replace );
		var control = content.indexOf(editable);
		this.cm.setSelection({ line:0, ch:0 }, { line:this.cm.lastLine(), ch:null } );
		this.cm.replaceSelection( append );
		this.cm.setSelection( this.getCursorWas() );
	} catch( e ) {
		//if(  WPV_Parametric.debug ) console.log( e.message )
	}
	return append;
};


/*
* -------------------------------------
* Editor extra buttons
* -------------------------------------
*/

// WPV_parametric_local.generic_button
var WPV_ParametricGenericButtonUtils = function() {
	
	var self = this;
	
	self.textarea = jqp( "#wpv_filter_meta_html_content" );
	self.toolbar = jqp('.js-wpv-filter-edit-toolbar');
	self.editor = icl_editor ? icl_editor : undefined;
	
	self.codemirror_highlight_options = {
		className: 'wpv-codemirror-highlight'
	};
	
	self.get_text_area_content = function() {
		var c = '';
		c = self.codemirror_views.getValue();
		return c;
	};
	
	self.cursorInside = function( area, start, end ) {
		try {
			return self.editor.cursorWithin( self.textarea, 'wpv-filter-controls', '/wpv-filter-controls' );
		} catch( e ) {
			
		}
		return false;
	};
	
	self.insert_shortcode_and_highlight = function( shortcode ) {
		var current_cursor = self.codemirror_views.getCursor( true );
		self.editor.InsertAtCursor( self.textarea, shortcode );
		var end_cursor = self.codemirror_views.getCursor( true ),
		marker = self.codemirror_views.markText( current_cursor, end_cursor, self.codemirror_highlight_options );
		setTimeout( function() {
			  marker.clear();
		}, 3000);
	};
	
	self.init = function() {
		self.codemirror_views = self.editor.codemirrorGet( 'wpv_filter_meta_html_content' );
		self.codemirror_views.on( 'change', function() {
			WPV_parametric_local.add_search.handle_flags();
			WPV_parametric_local.add_spinner.handle_flags();
			WPV_parametric_local.add_reset.handle_flags();
			WPV_parametric_local.add_submit.handle_flags();
		});
	};
	
	self.init();
	
	return this;
};

var WPV_ParametricSearchButton = function() {
	var self = this,
	button = jqp('<button class="button-secondary js-code-editor-toolbar-button js-parametric-add-search-short-tag" />'),
	icon = jqp('<i class="icon-search" />'),
	title = WPV_Parametric.add_toolbar_search_button_title,
	icon_complete = jqp('<i class="icon-bookmark flow-complete js-ps-button-complete" style="display:none" />'),
	title_complete = WPV_Parametric.add_toolbar_search_button_title_complete,
	icon_filter_missing = jqp('<i class="icon-bookmark flow-warning js-ps-button-filter-missing" style="display:none" />'),
	title_filter_missing = WPV_Parametric.add_toolbar_search_button_title_missing,
	title_filter_wrong = WPV_Parametric.add_toolbar_search_button_title_wrong,
	label = jqp('<span class="button-label" />'),
	list = jqp('<li class="js-editor-addon-button-wrapper" />'),
	filter_box = jqp('#js-row-post_search');

	self.search_where = 'full_content';
	//static reference
	WPV_ParametricSearchButton.button = button;

	self.init = function() {
		self.init_dialogs();
		self.pwin = WPV_parametric_local.pwindow;
		self.createButton();
		self.handle_flags();
		self.create_search();
	};
	
	self.init_dialogs = function() {
		var dialog_height = jqp( window ).height() - 100;
		self.dialog_insert = jqp( "#js-wpv-parametric-search-dialogs .js-dialog-search-box-button" ).dialog({
			autoOpen: false,
			modal: true,
			title: WPV_Parametric.add_search_shortcode_dialog_title,
			minWidth: 550,
			maxHeight: dialog_height,
			draggable: false,
			resizable: false,
			position: { my: "center top+50", at: "center top", of: window },
			show: { 
				effect: "blind", 
				duration: 800 
			},
			open: function( event, ui ) {
				jqp( 'body' ).addClass( 'modal-open' );
			},
			close: function( event, ui ) {
				jqp( 'body' ).removeClass( 'modal-open' );
			},
			buttons:[
				{
					class: 'button-secondary',
					text: WPV_Parametric.cancel,
					click: function() {
						jqp( this ).dialog( "close" );
						WPV_parametric_local.generic_button.codemirror_views.focus();
					}
				},
				{
					class: 'button-primary js-parametric-add-search-box',
					text: WPV_Parametric.insert,
					click: function() {
						var thiz = jqp( '.js-parametric-add-search-box' ),
						data = {
							style: jqp( '.js-wpv-search-box-style' ).val(),
							class: jqp( '.js-wpv-search-box-class' ).val()
						};
						thiz
							.addClass( 'button-secondary' )
							.removeClass( 'button-primary' )
							.prop( 'disabled', true );
						var thiz_spinner = jqp( '<div class="wpv-spinner ajax-loader">' ).insertAfter( thiz ).show();
						self.search_where = jqp( '.js-wpv-post-search-options-dialog:checked' ).val();
						self.create_and_insert( jqp( this ), data, thiz_spinner );
					}
				}
			]
		});
		
		self.dialog_override = jqp( "#js-wpv-parametric-search-dialogs .js-dialog-search-override-button" ).dialog({
			autoOpen: false,
			modal: true,
			title: WPV_Parametric.add_search_shortcode_dialog_title_override,
			minWidth: 550,
			maxHeight: dialog_height,
			draggable: false,
			resizable: false,
			position: { my: "center top+50", at: "center top", of: window },
			show: { 
				effect: "blind", 
				duration: 800 
			},
			open: function( event, ui ) {
				jqp( 'body' ).addClass( 'modal-open' );
			},
			close: function( event, ui ) {
				jqp( 'body' ).removeClass( 'modal-open' );
			},
			buttons:[
				{
					class: 'button-secondary',
					text: WPV_Parametric.cancel,
					click: function() {
						jqp( this ).dialog( "close" );
						WPV_parametric_local.generic_button.codemirror_views.focus();
					}
				},
				{
					class: 'button-primary js-parametric-add-search-override',
					text: WPV_Parametric.insert,
					click: function() {
						var thiz = jqp( '.js-parametric-add-search-override' ),
						data = {
							style: jqp( '.js-wpv-search-override-style' ).val(),
							class: jqp( '.js-wpv-search-override-class' ).val()
						};
						thiz
							.addClass( 'button-secondary' )
							.removeClass( 'button-primary' )
							.prop( 'disabled', true );
						var thiz_spinner = jqp( '<div class="wpv-spinner ajax-loader">' ).insertAfter( thiz ).show();
						self.search_where = jqp( '.js-wpv-post-search-override-dialog:checked' ).val();
						self.create_and_insert( jqp( this ), data, thiz_spinner );
					}
				}
			]
		});
	};
	
	self.createButton = function() {
		label.text( WPV_Parametric.add_search_shortcode_button );
		list.append( button );
		button.append( icon, label, icon_complete, icon_filter_missing );
		if ( WPV_parametric_local.generic_button.toolbar.find( 'button.js-button_parametric_filter_edit' ) ) {
			WPV_parametric_local.generic_button.toolbar
				.find( 'button.js-button_parametric_filter_edit' )
					.closest( '.js-editor-addon-button-wrapper' )
						.after( list );
		} else{
			WPV_parametric_local.generic_button.toolbar.append( list );
		}
		list.toolsetTooltip();
	};
	
	self.handle_flags = function() {
		button
			.removeClass( 'wpv-button-flagged' )
			.find( '.js-ps-button-complete, .js-ps-button-filter-missing' )
				.hide();
		if ( self.is_button_disabled() ) {
			button
				.addClass( 'disabled' )
				.addClass( 'wpv-button-flagged' )
				.find( '.js-ps-button-complete' )
					.show();
			list.data( 'tooltip-text', title_complete );
		} else {
			button
				.removeClass( 'disabled' );
			list.data( 'tooltip-text', title );
			if ( self.has_search( WPV_parametric_local.generic_button.get_text_area_content() ) ) {
				if ( ! self.has_filter() ) {
					button
						.addClass( 'wpv-button-flagged' )
						.find( '.js-ps-button-filter-missing' )
							.show();
					list.data( 'tooltip-text', title_filter_missing );
				} else if ( self.has_specific_filter() ) {
					button
						.addClass( 'wpv-button-flagged' )
						.find( '.js-ps-button-filter-missing' )
							.show();
					list.data( 'tooltip-text', title_filter_wrong );
				}
			}
		}
	};
	
	self.create_search = function() {
		button.on( 'click', function( event ) {
			event.stopImmediatePropagation();
			if ( button.hasClass( 'disabled' ) ) {
				return false;
			}
			if ( self.has_search( WPV_parametric_local.generic_button.get_text_area_content() ) ) {
				self.open_override_specific_dialog();
			} else if ( ! WPV_parametric_local.generic_button.cursorInside() ) {
				WPViews.view_edit_screen.codemirror_panel( WPV_parametric_local.generic_button.codemirror_views, WPV_Parametric.place_cursor_inside_wpv_controls, true, 'error' );
				return false;
			} else if( self.has_filter() ) {
				self.open_override_specific_dialog();
			} else {
				self.open_set_options_dialog();
			}
			return false;
		});
	};

	self.is_button_disabled = function() {
		return (
			self.has_search( WPV_parametric_local.generic_button.get_text_area_content() ) 
			&& self.has_filter() 
			&& ! self.has_specific_filter()
		);
	};

	self.has_filter = function() {
		return ( jqp('.js-wpv-filter-post-search-options' ).length > 0 );
	};

	self.has_specific_filter = function() {
		return ( jqp( '.js-wpv-filter-post-search-options input#wpv-search-mode-specific' ).prop( 'checked' ) );
	};

	self.has_search = function( area ) {
		return ( 
			area.search( /\[wpv-filter-search-box/ ) == -1 
			&& area.search( /\url_param=\"wpv_post_search\"/ ) == -1 
		) ? false : true ;
	};
	
	self.open_set_options_dialog = function() {
		self.dialog_insert.dialog( "open" );
		jqp( '.js-parametric-add-search-box' )
			.addClass( 'button-primary' )
			.removeClass( 'button-secondary' )
			.prop( 'disabled', false );
		jqp( '.js-wpv-search-box-style, .js-wpv-search-box-class' ).val( '' );
	};

	self.open_override_specific_dialog = function() {
		self.dialog_override.dialog( "open" );
		jqp( '.js-wpv-search-filter-override-var' ).hide();
		jqp( '.js-wpv-post-search-override-dialog' ).attr( 'checked', false );
		jqp( '#search-override-full' ).attr( 'checked', true );
		jqp( '.js-parametric-add-search-override' )
			.addClass( 'button-primary' )
			.removeClass( 'button-secondary' )
			.prop( 'disabled', false );
		jqp( '.js-wpv-search-override-style, .js-wpv-search-override-class' ).val( '' );
		if ( self.has_filter() ) {
			if ( self.has_specific_filter() ) {
				jqp( '.js-wpv-search-filter-override-specific' ).show();
			} else {
				jqp( '.js-wpv-search-filter-override-valid' ).show();
			}
		} else{
			jqp( '.js-wpv-search-filter-override-missing' ).show();
		}
	};

	self.create_and_insert = function( dialog, data, spinner ) {
		var ret = false;
		if ( ! self.has_search( WPV_parametric_local.generic_button.get_text_area_content() ) ) {
			self.create_filter( function() {
				self.insert_search_shortcode( data );
				jqp( '.js-wpv-missing-filter-container, .js-wpv-no-filters-container' ).hide();
				spinner.remove();
				dialog.dialog( "close" );
			});
			ret = true;
		} else {
			self.create_filter( function() {
				jqp( '.js-wpv-missing-filter-container, .js-wpv-no-filters-container' ).hide();
				spinner.remove();
				dialog.dialog( "close" );
			});
			ret = true;
		}
		return ret;
	};

	self.create_filter = function( callback, args ) {
		var params = {
			action:'wpv_filter_post_search_update',
			id: WPV_Parametric.view_id,
			filter_options: 'filter_by_search=1&post_search_value=&search_mode%5B%5D=manual&post_search_content=' + self.search_where,
			wpnonce:WPV_Parametric.wpv_view_filter_search_nonce
		};
		jqp.post( ajaxurl, params, function( response ) {
			var prms = {
				action: 'wpv_filter_update_filters_list',
				id: WPV_Parametric.view_id,
				query_type: jQuery('input:radio.js-wpv-query-type:checked').val(),
				nonce: jQuery('.js-wpv-filter-update-filters-list-nonce').val()
			};
			jQuery.post( ajaxurl, prms, function( response ) {
				if ( ( typeof( response ) !== 'undefined' ) ) {
					decoded_response = jQuery.parseJSON( response );
					if ( decoded_response.success === prms.id ) {
						jQuery( '.js-filter-list' ).html( decoded_response.wpv_filter_update_filters_list );
						if ( typeof callback == 'function' ) {
							callback.apply( self, args ? args : [] )
						}
					}
					jQuery( document ).trigger( 'js_event_wpv_query_filter_created', [ 'post_search' ] );
				} else {
					//if(  WPV_Parametric.debug ) console.log( WPV_Parametric.ajax_error, response );
				}
			})
			.fail(function(jqXHR, textStatus, errorThrown) {
				//if(  WPV_Parametric.debug ) console.log( WPV_Parametric.error_generic, textStatus, errorThrown );
			})
			.always(function() {
				self.handle_flags();
			});

		})
		.fail(function(jqXHR, textStatus, errorThrown){
			//if(  WPV_Parametric.debug ) console.log(jqXHR, textStatus, errorThrown)
		})
		.always(function(){
			//
		});

	};

	self.insert_search_shortcode = function( data ) {
		var shortcode = '[wpv-filter-search-box';
		if ( data.class != '' ) {
			shortcode += ' class="' + data.class + '"';
		}
		if ( data.style != '' ) {
			shortcode += ' style="' + data.style + '"';
		}
		shortcode += ']';
		WPV_parametric_local.generic_button.insert_shortcode_and_highlight( shortcode );
	};
	
	self.init();

	return this;

};

var WPV_ParametricSpinnerButton = function() {
	var self = this,
	button = jqp('<button class="button-secondary js-code-editor-toolbar-button js-parametric-add-spinner-short-tag" />'),
	icon = jqp('<i class="icon-spinner" />'),
	title = WPV_Parametric.add_toolbar_spinner_button_title,
	icon_flag = jqp('<i class="icon-bookmark flow-complete js-ps-button-flag" style="display:none" />'),
	title_complete = WPV_Parametric.add_toolbar_spinner_button_title_complete,
	title_useless = WPV_Parametric.add_toolbar_spinner_button_title_useless,
	label = jqp('<span class="button-label" />'),
	list = jqp('<li class="js-editor-addon-button-wrapper" />');
	
	self.defaults = {
		container: 'div',
		class: '',
		style: '',
		spinner_position: 'before',
		content_text: '',
		spinner: ''
	};

	//export button for external use
	WPV_ParametricSpinnerButton.button = button;
	
	self.init = function() {
		var dialog_height = jqp( window ).height() - 100;
		self.dialog = jqp( "#js-wpv-parametric-search-dialogs .js-wpv-dialog-spinner-button" ).dialog({
			autoOpen: false,
			modal: true,
			title: WPV_Parametric.add_spinner_shortcode_dialog_title,
			minWidth: 550,
			maxHeight: dialog_height,
			draggable: false,
			resizable: false,
			position: { my: "center top+50", at: "center top", of: window },
			show: { 
				effect: "blind", 
				duration: 800 
			},
			open: function( event, ui ) {
				jqp( 'body' ).addClass( 'modal-open' );
			},
			close: function( event, ui ) {
				jqp( 'body' ).removeClass( 'modal-open' );
			},
			buttons:[
				{
					class: 'button-secondary',
					text: WPV_Parametric.cancel,
					click: function() {
						jqp( this ).dialog( "close" );
						WPV_parametric_local.generic_button.codemirror_views.focus();
					}
				},
				{
					class: 'button-primary js-parametric-add-spinner-short-tag-label',
					text: WPV_Parametric.insert,
					click: function() {
						var data_live = {
							container: jqp('.js-wpv-dialog-spinner-button #spinner_shortcode_container_type').val(),
							class: jqp('.js-wpv-dialog-spinner-button #spinner_shortcode_container_classname').val(),
							style: jqp('.js-wpv-dialog-spinner-button #spinner_shortcode_container_style').val(),
							spinner_position: jqp('.js-wpv-dialog-spinner-button #spinner_shortcode_spinner_position').val(),
							content_text: jqp('.js-wpv-dialog-spinner-button #spinner_shortcode_content').val(),
							spinner: jqp('.js-wpv-dialog-spinner-button .js-wpv-ps-spinner-image:checked').val()
						},
						data = jqp.extend( {}, self.defaults, data_live );
						if ( self.insert_spinner_shortcode( data ) ) {
							jqp( this ).dialog( "close" );
							WPV_parametric_local.generic_button.codemirror_views.focus();
						}
					}
				}
			]
		});
		
		self.pwin = WPV_parametric_local.pwindow;
		self.createButton();
		self.create_dialog();
		self.handle_flags();
	};

	self.createButton = function() {
		label.text( WPV_Parametric.add_spinner_shortcode_button );
		list.append(button);
		button.append(icon, label, icon_flag);
		if ( WPV_parametric_local.generic_button.toolbar.find( 'button.js-parametric-add-search-short-tag' ) ) {
			WPV_parametric_local.generic_button.toolbar
				.find( 'button.js-parametric-add-search-short-tag' )
					.closest( '.js-editor-addon-button-wrapper' )
						.after( list );
		} else {
			WPV_parametric_local.generic_button.toolbar.append( list );
		}
		list.toolsetTooltip();
	};

	self.create_dialog = function() {
		button.on('click', function( event ) {
			event.stopImmediatePropagation();
			if ( button.hasClass( 'disabled' ) ) {
				return false;
			}
			if ( ! WPV_parametric_local.generic_button.cursorInside() ) {
				WPViews.view_edit_screen.codemirror_panel( WPV_parametric_local.generic_button.codemirror_views, WPV_Parametric.place_cursor_inside_wpv_controls, true, 'error' );
				return false;
			} else {
				self.openDialog();
			}
		});
	};

	self.handle_flags = function() {
		var update_mode = jqp( '.js-wpv-dps-ajax-results:checked' ).val(),
		update_action = jqp( '.js-wpv-ajax-results-submit:checked' ).val(),
		dependency_mode = jqp( '.js-wpv-dps-enable:checked' ).val();

		if (
			dependency_mode != 'disable'
			|| ( 
				update_mode != 'disable' 
				|| ( 
					update_mode == 'disable' 
					&& update_action != 'reload' 
				) 
			)
		) {
			button.removeClass('disabled');
			if( self.has_spinner( WPV_parametric_local.generic_button.get_text_area_content() ) ) {
				button
					.addClass( 'wpv-button-flagged' )
					.find( '.js-ps-button-flag' )
						.show();
				list.data('tooltip-text', title_complete );
			} else {
				button
					.removeClass( 'wpv-button-flagged' )
					.find( '.js-ps-button-flag' )
						.hide();
				list.data('tooltip-text', title );
			}
		} else {
			button
				.addClass('disabled')
				.removeClass( 'wpv-button-flagged' )
				.find( '.js-ps-button-flag' )
				.hide();
			list.data('tooltip-text', title_useless );
		}
	};

	self.has_spinner = function( area ) {
		return ( area.search(/\[wpv-filter-spinner/) == -1 ) ? false : true ;
	};

	self.insert_spinner_shortcode = function( data ) {
		var attributes = '',
		content ='',
		shortcode = '';
		if ( data.class != '') {
			attributes += ' class="' + data.class + '"';
		}
		if ( data.style != '') {
			attributes += ' style="' + data.style + '"';
		}
		if ( data.spinner != '') {
			attributes += ' spinner="' + data.spinner + '"'
		}
		if ( data.content_text != '' ) {
			content = '[wpml-string context="wpv-views"]' + data.content_text + '[/wpml-string]';
		}
		shortcode = '[wpv-filter-spinner container="' + data.container + '" position="' + data.spinner_position + '"' + attributes + ']' + content + '[/wpv-filter-spinner]';
		WPV_parametric_local.generic_button.insert_shortcode_and_highlight( shortcode );
		return shortcode;
	};

	self.openDialog = function() {
		if ( WPV_parametric_local.generic_button.cursorInside() ) {
			self.dialog.dialog( 'open' );
			jqp( '#spinner_shortcode_container_type', '.js-wpv-dialog-spinner-button' ).val( 'div' );
			jqp( '#spinner_shortcode_container_classname, #spinner_shortcode_container_style, #spinner_shortcode_content', '.js-wpv-dialog-spinner-button' ).val( '' );
			jqp( '#spinner_shortcode_spinner_position', '.js-wpv-dialog-spinner-button' ).val( 'before' );
		} else {
			WPViews.view_edit_screen.codemirror_panel( WPV_parametric_local.generic_button.codemirror_views, WPV_Parametric.place_cursor_inside_wpv_filter, true, 'error' );
		}
	};
	
	self.init();
	
	return this;
};

var WPV_ParametricResetButton = function() {
	var self = this,
	button = jqp('<button class="button-secondary js-code-editor-toolbar-button js-parametric-add-reset-short-tag" />'),
	icon = jqp('<i class="icon-eraser" />'),
	title = WPV_Parametric.add_toolbar_reset_button_title,
	icon_complete = jqp('<i class="icon-bookmark flow-complete js-ps-button-complete" style="display:none" />'),
	title_complete = WPV_Parametric.add_toolbar_reset_button_title_complete,
	title_incomplete = WPV_Parametric.add_toolbar_reset_button_title_incomplete,
	label = jqp('<span class="button-label" />'),
	list = jqp('<li class="js-editor-addon-button-wrapper" />');
	
	self.defaults = {
		label: '',
		class: '',
		style: '',
		tag: ''
	};

	//export button for external use
	WPV_ParametricResetButton.button = button;

	self.init = function() {
		var dialog_height = jqp( window ).height() - 100;
		self.dialog = jqp( "#js-wpv-parametric-search-dialogs .js-wpv-dialog-reset-button" ).dialog({
			autoOpen: false,
			modal: true,
			title: WPV_Parametric.add_reset_shortcode_dialog_title,
			minWidth: 550,
			maxHeight: dialog_height,
			draggable: false,
			resizable: false,
			position: { my: "center top+50", at: "center top", of: window },
			show: { 
				effect: "blind", 
				duration: 800 
			},
			open: function( event, ui ) {
				jqp( 'body' ).addClass( 'modal-open' );
			},
			close: function( event, ui ) {
				jqp( 'body' ).removeClass( 'modal-open' );
			},
			buttons:[
				{
					class: 'button-secondary',
					text: WPV_Parametric.cancel,
					click: function() {
						jqp( this ).dialog( "close" );
						WPV_parametric_local.generic_button.codemirror_views.focus();
					}
				},
				{
					class: 'button-primary js-parametric-add-reset-short-tag-label',
					text: WPV_Parametric.insert,
					click: function() {
						if( jqp('.js-wpv-dialog-reset-button #reset_shortcode_label').val() != '') {
							var data_live = {
								label: jqp('.js-wpv-dialog-reset-button #reset_shortcode_label').val(),
								class: jqp('.js-wpv-dialog-reset-button #reset_shortcode_button_classname').val(),
								style: jqp('.js-wpv-dialog-reset-button #reset_shortcode_button_style').val(),
								tag: jqp('.js-wpv-dialog-reset-button #reset_shortcode_button_tag').val()
							},
							data = jqp.extend( {}, self.defaults, data_live );
							if ( self.insert_reset_shortcode( data ) ) {
								button.prop('disabled',false);
								jqp( this ).dialog( "close" );
								WPV_parametric_local.generic_button.codemirror_views.focus();
							}
						} else {
							jqp('.js-wpv-dialog-reset-button .js-errors-in-parametric-box').wpvToolsetMessage({
								text:WPV_Parametric.consider_adding_label_to_button_shortcode
							});
						}
					}
				}
			]
		});
		
		self.pwin = WPV_parametric_local.pwindow;
		self.createButton();
		button.prop( 'disabled', false );
		self.create_dialog();
		self.handle_flags();
	};
	
	self.createButton = function() {
		label.text( WPV_Parametric.add_reset_shortcode_button );
		list.append( button );
		button.append( icon, label, icon_complete );
		if ( WPV_parametric_local.generic_button.toolbar.find( 'button.js-parametric-add-spinner-short-tag' ) ) {
			WPV_parametric_local.generic_button.toolbar
				.find( 'button.js-parametric-add-spinner-short-tag' )
					.closest( '.js-editor-addon-button-wrapper' )
						.after( list );
		} else {
			WPV_parametric_local.generic_button.toolbar.append( list );
		}
		list.toolsetTooltip();
	};
	
	self.create_dialog = function() {
		button.on('click', function( event ) {
			event.stopImmediatePropagation();
			if ( ! WPV_parametric_local.generic_button.cursorInside() ) {
				WPViews.view_edit_screen.codemirror_panel( WPV_parametric_local.generic_button.codemirror_views, WPV_Parametric.place_cursor_inside_wpv_controls, true, 'error' );
				return false;
			} else {
				self.openDialog();
			}
		});
	};

	self.handle_flags = function() {
		if( self.has_reset( WPV_parametric_local.generic_button.get_text_area_content() ) ) {
			button
				.addClass( 'wpv-button-flagged' )
				.find( '.js-ps-button-complete' )
					.show();
			list.data('tooltip-text',title_complete);
		} else {
			button
				.removeClass( 'wpv-button-flagged' )
				.find( '.js-ps-button-complete' )
					.hide();
			list.data('tooltip-text',title);
		}
	};

	self.has_reset = function( area ) {
		return ( area.search(/\[wpv-filter-reset/) == -1 ) ? false : true ;
	};

	self.insert_reset_shortcode = function( data ) {
		var attributes = '',
		shortcode = '';
		if ( data.class != '' ) {
			attributes += ' class="' + data.class + '"';
		}
		if ( data.style != '' ) {
			attributes += ' style="' + data.style + '"';
		}
		if ( data.tag == 'button' ) {
			attributes += ' type="button"';
		} else {
			attributes += ' type="input"';
		}
		shortcode = '[wpv-filter-reset reset_label="' + data.label + '"' + attributes + ']';
		WPV_parametric_local.generic_button.insert_shortcode_and_highlight( shortcode );
		return shortcode;
	};

	self.openDialog = function() {
		if ( WPV_parametric_local.generic_button.cursorInside() ) {
			self.dialog.dialog( 'open' );
			jqp( '#reset_shortcode_button_classname, #reset_shortcode_button_style', '.js-wpv-dialog-reset-button' ).val( '' );
			jqp( '#reset_shortcode_label', '.js-wpv-dialog-reset-button' ).val( WPV_Parametric.add_reset_shortcode_dialog_label_default );
			jqp( '#reset_shortcode_button_tag', '.js-wpv-dialog-reset-button' ).val( 'input' );
		} else {
			WPViews.view_edit_screen.codemirror_panel( WPV_parametric_local.generic_button.codemirror_views, WPV_Parametric.place_cursor_inside_wpv_controls, true, 'error' );
		}
	};
	
	self.init();
	
	return this;
};

var WPV_ParametricSubmitButton = function() {
	var self = this,
	button = jqp('<button class="button-secondary js-code-editor-toolbar-button js-parametric-add-submit-short-tag" />'),
	icon = jqp('<i class="icon-chevron-right" />'),
	title = WPV_Parametric.add_toolbar_submit_button_title,
	icon_complete = jqp('<i class="icon-bookmark flow-complete js-ps-button-complete" style="display:none" />'),
	title_complete = WPV_Parametric.add_toolbar_submit_button_title_complete,
	icon_incomplete = jqp('<i class="icon-bookmark flow-warning js-ps-button-incomplete" style="display:none" />'),
	title_incomplete = WPV_Parametric.add_toolbar_submit_button_title_incomplete,
	icon_irrelevant = jqp('<i class="icon-bookmark flow-info js-ps-button-irrelevant" style="display:none" />'),
	title_irrelevant = WPV_Parametric.add_toolbar_submit_button_title_irrelevant,
	title_irrelevant_but_added = WPV_Parametric.add_toolbar_submit_button_title_irrelevant_added,
	label = jqp('<span class="button-label" />'),
	list = jqp('<li class="js-editor-addon-button-wrapper" />');
	
	self.defaults = {
		label: '',
		class: '',
		style: '',
		tag: ''
	};

	//export button for external use
	WPV_ParametricSubmitButton.button = button;

	self.init = function() {
		var dialog_height = jqp( window ).height() - 100;
		self.dialog = jqp( "#js-wpv-parametric-search-dialogs .js-wpv-dialog-submit-button" ).dialog({
			autoOpen: false,
			modal: true,
			title: WPV_Parametric.add_submit_shortcode_dialog_title,
			minWidth: 550,
			maxHeight: dialog_height,
			draggable: false,
			resizable: false,
			position: { my: "center top+50", at: "center top", of: window },
			show: { 
				effect: "blind", 
				duration: 800 
			},
			open: function( event, ui ) {
				jqp( 'body' ).addClass( 'modal-open' );
			},
			close: function( event, ui ) {
				jqp( 'body' ).removeClass( 'modal-open' );
			},
			buttons:[
				{
					class: 'button-secondary',
					text: WPV_Parametric.cancel,
					click: function() {
						jqp( this ).dialog( "close" );
						WPV_parametric_local.generic_button.codemirror_views.focus();
					}
				},
				{
					class: 'button-primary js-parametric-add-submit-short-tag-label',
					text: WPV_Parametric.insert,
					click: function() {
						if ( jqp('.js-wpv-dialog-submit-button #submit_shortcode_label').val() != '') {
							var data_live = {
								label: jqp('.js-wpv-dialog-submit-button #submit_shortcode_label').val(),
								class: jqp('.js-wpv-dialog-submit-button #submit_shortcode_button_classname').val(),
								style: jqp('.js-wpv-dialog-submit-button #submit_shortcode_button_style').val(),
								tag: jqp('.js-wpv-dialog-submit-button #submit_shortcode_button_tag').val()
							},
							data = jqp.extend( {}, self.defaults, data_live );
							if ( self.insert_submit_shortcode( data ) ) {
								jqp( this ).dialog( "close" );
								WPV_parametric_local.generic_button.codemirror_views.focus();
							}
						} else {
							jqp('.js-wpv-dialog-submit-button .js-errors-in-parametric-box').wpvToolsetMessage({
								text:WPV_Parametric.consider_adding_label_to_button_shortcode
							});
						}
					}
				}
			]
		});
		
		self.pwin = WPV_parametric_local.pwindow;
		self.createButton();
		self.handle_flags();
		self.create_dialog();
	};
	
	self.createButton = function() {
		label.text( WPV_Parametric.add_submit_shortcode_button );
		list.append(button);
		button.append(icon, label, icon_complete, icon_incomplete, icon_irrelevant);
		if ( WPV_parametric_local.generic_button.toolbar.find( 'button.js-parametric-add-reset-short-tag' ) ) {
			WPV_parametric_local.generic_button.toolbar
				.find( 'button.js-parametric-add-reset-short-tag' )
					.closest( '.js-editor-addon-button-wrapper' )
						.after( list );
		} else{
			WPV_parametric_local.generic_button.toolbar.append(list);
		}
		list.toolsetTooltip();
	};

	self.handle_flags = function() {
		var update_mode = jqp( '.js-wpv-dps-ajax-results:checked' ).val();
		button.addClass( 'wpv-button-flagged' );
		if ( update_mode == 'enable' ) {
			button
				.find( '.js-ps-button-irrelevant' )
					.show();
			button
				.find( '.js-ps-button-incomplete, .js-ps-button-complete' )
					.hide();
			if( self.has_submit( WPV_parametric_local.generic_button.get_text_area_content() ) ) {
				list.data('tooltip-text',title_irrelevant_but_added);
			} else {
				list.data('tooltip-text',title_irrelevant);
			}
		} else {
			button
				.find( '.js-ps-button-irrelevant, .js-ps-button-incomplete, .js-ps-button-complete' )
					.hide();
			if( self.has_submit( WPV_parametric_local.generic_button.get_text_area_content() ) ) {
				button.find( '.js-ps-button-complete' ).show();
				list.data('tooltip-text',title_complete);
			} else {
				button.find( '.js-ps-button-incomplete' ).show();
				list.data('tooltip-text',title_incomplete);
			}
		}
	};
	
	self.create_dialog = function() {
		button.on( 'click', function( event ){
			event.stopImmediatePropagation();
			if ( button.hasClass( 'disabled' ) ) {
				return false;
			}
			if ( ! WPV_parametric_local.generic_button.cursorInside() ) {
				WPViews.view_edit_screen.codemirror_panel( WPV_parametric_local.generic_button.codemirror_views, WPV_Parametric.place_cursor_inside_wpv_controls, true, 'error' );
				return false;
			}
			self.openDialog();
		});
	};

	self.has_submit = function( area ) {
		return ( area.search(/\[wpv-filter-submit/) == -1 ) ? false : true ;
	};

	self.insert_submit_shortcode = function( data ) {
		var attributes = '',
		shortcode = '';
		if ( data.class != '') {
			attributes += ' class="' + data.class + '"';
		}
		if ( data.style != '') {
			attributes += ' style="' + data.style + '"';
		}
		if ( data.tag == 'button' ) {
			attributes += ' type="button"';
		} else {
			attributes += ' type="input"';
		}
		shortcode = '[wpv-filter-submit name="' + data.label + '"' + attributes + ']';
		WPV_parametric_local.generic_button.insert_shortcode_and_highlight( shortcode );

		return shortcode;
	};

	self.openDialog = function() {
		if ( WPV_parametric_local.generic_button.cursorInside() ) {
			self.dialog.dialog( 'open' );
			jqp( '#submit_shortcode_button_classname, #submit_shortcode_button_style', '.js-wpv-dialog-submit-button' ).val( '' );
			jqp( '#submit_shortcode_label', '.js-wpv-dialog-submit-button' ).val( WPV_Parametric.add_submit_shortcode_dialog_label_default );
			jqp( '#submit_shortcode_button_tag', '.js-wpv-dialog-submit-button' ).val( 'input' );
		} else {
			WPViews.view_edit_screen.codemirror_panel( WPV_parametric_local.generic_button.codemirror_views, WPV_Parametric.place_cursor_inside_wpv_controls, true, 'error' );
		}
	};
	
	self.init();
	
	return this;
};

/*
* -------------------------------------
* Auxiliar methods
* -------------------------------------
*/

// A generic JSON store
var WPV_ParametricJsonStore = function() {
	var self = this;
	self.loading = false;

	self.loader = {
		loader: jqp( '<li><div class="wpv-spinner ajax-loader"><div></li>' ),
		loadShow: function( el ) {
			self.loading = true;
			self.loader.loader.appendTo( el ).show();
		},
		loadHide: function() {
			self.loader.loader.fadeOut(200, function(){
				self.loading = false;
				jqp(this).remove();
			});
		}
	};

	self.ajaxCall = function( data, url, method, callback, args, object ) {
		var URI = url ? url : ajaxurl, 
		obj = data, 
		type = method ? method : 'post';

		if ( obj ) {
			jqp.ajax({
				type: type,
				url: URI ,
				data: obj,
				dataType: 'json',
				error: function( XMLHttpRequest, textStatus, errorThrown ) {
					if ( self.loading ) {
						self.loader.loadHide();
					}
					WPViews.view_edit_screen.codemirror_panel( codemirror_views_query, "Error: " + textStatus + " " + errorThrown, true, 'error' );
					console.error( "Error: ", textStatus, errorThrown );
					return false;
				},
				beforeSend: function( XMLHttpRequest, Obj ) {
					try {
						//always attach view_id when you send data
						Obj.data += '&view_id='+WPV_Parametric.view_id

						if ( XMLHttpRequest && XMLHttpRequest.overrideMimeType ) {
							XMLHttpRequest.overrideMimeType("application/j-son;charset=UTF-8");
						}
					} catch( e ) {
						//if(  WPV_Parametric.debug ) console.log(e.message);
						return false;
					}
				},
				success: function( data, textStatus, jqXHR ) {
					if ( 
						data.Data 
						&& data.Data.error 
					) {
						//console.error( data.Data.error );
						WPViews.view_edit_screen.codemirror_panel( codemirror_views_query, data.Data.error, true, 'error' );
					} else if ( 
						data.Data 
						&& data.Data.message 
					) {
						WPViews.view_edit_screen.codemirror_panel( codemirror_views_query, data.Data.message, true, 'success' );
					} else if ( 
						data.Data 
						&& ! data.Data.error
					) {
						if ( 
							callback 
							&& typeof callback == 'function' 
						) {
							args = [data, textStatus, args];
							callback.apply( object ? object : self, args ? args : [] );
						} else {
							WPViews.view_edit_screen.codemirror_panel( codemirror_views_query, WPV_Parametric.ajax_callback_undefined, true, 'error' );
						}
					}
				},
				complete: function( data, textStatus ) {
					if ( self.loading ) {
						self.loader.loadHide();
					}
				}
			});
		}
	}
};

/*get set caret position http://stackoverflow.com/questions/1891444/how-can-i-get-cursor-position-in-a-textarea */
jqp.fn.caret = function ( begin, end ) {
	if ( this.length == 0 ) {
		return false;
	}
	if ( typeof begin == 'number' ) {
		end = (typeof end == 'number') ? end : begin;
		return this.each(function () {
			if (this.setSelectionRange) {
				this.setSelectionRange(begin, end);
			} else if (this.createTextRange) {
				var range = this.createTextRange();
				range.collapse(true);
				range.moveEnd('character', end);
				range.moveStart('character', begin);
				try { range.select(); } catch (ex) { }
			}
		});
	} else {
		if ( this[0].setSelectionRange ) {
			begin = this[0].selectionStart;
			end = this[0].selectionEnd;
		} else if ( document.selection && document.selection.createRange ) {
			var range = document.selection.createRange();
			begin = 0 - range.duplicate().moveStart('character', -100000);
			end = begin + range.text.length;
		}
		return { begin: begin, end: end };
	}
};

//fallback to Array.indexOf() for IE
if ( ! Array.prototype.indexOf ) {
	Array.prototype.indexOf = function( obj, start ) {
		for ( var i = (start || 0), j = this.length; i < j; i++) {
			if ( this[i] === obj ) {
				return i;
			}
		}
		return -1;
	};
}

if ( ! String.prototype.removeExtraWhiteSpaces ) {
	String.prototype.removeExtraWhiteSpaces = function() {
		return this.replace(/^(\s*)|(\s*)$/g, '').replace(/\s+/g, ' ');
	};
}

// if we forget a //// console.log somewhere IE will not bother
if ( ! console ) {
	var console = {
		log: function( args ) {
			//alert(args);
		},
		error: function( args ) {
			//alert(args);
		}
	};
}

if ( ! String.prototype.trim ) {
	String.prototype.trim = function () {
		return this.replace(/^\s+|\s+$/g,'');
	};
}

(function( $ ) {

	$.ucfirst = function(str) {
		var text = str,
		parts = text.split(' '),
		len = parts.length,
		i, 
		words = [];
		for ( i = 0; i < len; i++ ) {
			var part = parts[i];
			var first = part[0].toUpperCase();
			var rest = part.substring( 1, part.length );
			var word = first + rest;
			words.push( word );
		}
		return words.join(' ');
	};
})(jQuery);
