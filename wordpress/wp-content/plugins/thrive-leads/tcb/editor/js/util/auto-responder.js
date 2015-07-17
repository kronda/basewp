/**
 * Created by radu on 07.04.2015.
 */
var TVE_Content_Builder = TVE_Content_Builder || {};
(function ($) {
    /**
     * handle all ajax calls to the server
     */
    function ajax(data, ajax_params) {

        var post_data = $.extend({
                post_id: tve_path_params.post_id,
                action: 'tve_api_editor_actions'
            }, tve_path_params.custom_post_data, data),

            params = $.extend({
                url: tve_path_params.ajax_url,
                data: post_data,
                type: 'post'
            }, ajax_params);

        return $.ajax(params);
    }

    /**
     * the tve_lead_generation() plugin  wrapped over (.edit_mode) element
     */
    var lead_generation, $lb;

    /**
     * static id to use for labels (checkboxes + radio buttons)
     * @type {number}
     */
    var static_id = Math.floor(Math.random() * 238);

    TVE_Content_Builder.auto_responder = {
        init: function () {

            lead_generation = $('.edit_mode').tve_lead_generation();
            $lb = $('#tve_lightbox_frame');

            return this;
        },
        /**
         * open lightbox (first step)
         */
        dashboard: function ($btn) {
            TVE_Editor_Page.overlay();
            var _type = lead_generation.getConnectionType(),
                edit = $btn && $btn.attr('data-edit-custom') ? '1' : 0;
            delete lead_generation.edit_api;
            ajax({
                route: 'dashboard',
                connection_type: _type,
                edit_custom_html: edit,
                connections: lead_generation.getApiConnections(),
                connections_str: lead_generation.api_config_str,
                thank_you_url: lead_generation.api_form_data.thank_you_url,
                submit_option: lead_generation.api_form_data.submit_option,
                api_fields: lead_generation.api_form_data.elements || null,
                api_fields_order: lead_generation.api_form_data.element_order || null
            }).done(function (response) {
                load_lightbox_content(response.lb_html);
                var html = lead_generation.customFormCode();
                show_lightbox();
                if (_type == 'custom-html' && html && edit) {
                    $lb.find('textarea[name="tve_lead_generation_code"]').val(html);
                    jQuery(".tve_lead_generate_fields").trigger('click');
                } else if (_type == 'api' && response.elements) {
                    lead_generation.setApiData(response);
                }
                $lb.find('.tve_lightbox_buttons').hide();

                TVE_Editor_Page.overlay(true);
            });
        },
        /**
         * generate form fields
         */
        generate_fields: function () {
            TVE_Editor_Page.overlay();
            var $textarea = $lb.find('textarea[name="tve_lead_generation_code"]');

            var _data = {
                autoresponder_code: $textarea.val(),
                custom: tve_path_params.custom_post_data,
                route: 'generateFields'
            };

            ajax(_data, {dataType: 'json'}).done(function (response) {
                jQuery("#generated_inputs_container").html(response.html);
                $textarea.val(response.stripped_code);
                lead_generation.setParsedResponse(response);
                TVE_Editor_Page.overlay('close');
            });
        },
        /**
         * save custom HTML form code into the page
         * @param $btn
         */
        save_custom_code: function ($btn) {
            lead_generation.generateForm().applyStyle();
            TVE_Content_Builder.controls.lb_close();
        },
        /**
         * saves the API connection configuration and renders the form
         */
        save_api_connection: function () {
            /**
             * add the thank you url as a hidden input
             */
            lead_generation.api_form_data.elements._back_url = {
                type: 'hidden',
                name: '_back_url',
                value: lead_generation.api_form_data.thank_you_url || ''
            };
            lead_generation.api_form_data.elements._submit_option = {
                type: 'hidden',
                name: '_submit_option',
                value: lead_generation.api_form_data.submit_option || ''
            };
            $.each(lead_generation.api_form_data.extra, function (n, item) {
                /**
                 * append any extra individual settings for each autoresponder
                 */
                if (lead_generation.api_config[n]) {
                    $.each(item, function (key, value) {
                        lead_generation.api_form_data.elements[n + '_' + key] = {
                            type: 'hidden',
                            className: 'tve-api-extra',
                            name: n + '_' + key,
                            value: value
                        };
                    });
                }
            });
            lead_generation.generateForm().applyStyle();
            TVE_Content_Builder.controls.lb_close();
        },
        /**
         * show the icons container to enable individual icon selections
         * @param $btn
         */
        open_icon_picker: function ($btn) {
            var container = $btn.parent(),
                $icons = $('#tve_lg_icon_list').show();

            $icons.find('.icomoon-icon').removeClass('tve_selected');
            if (container.find('.icomoon-icon').length) {
                var icon_cls = container.find('.icomoon-icon').data('cls');
                $icons.find('[data-cls="' + icon_cls + '"]').addClass('tve_selected');
            }

            this.icon_container = container;
        },
        /**
         * choose an icon to be used for a field
         * @param $icon
         */
        choose_icon: function ($icon) {
            $('#tve_lg_icon_list').hide();
            if (this.icon_container) {
                var _field = this.icon_container.find('.tve_lg_icon_picker').attr('data-field');
                this.icon_container.find('.icomoon-icon').remove();
                var icon = $icon.clone().removeClass('tve_selected tve_click').removeAttr('data-ctrl');
                icon.find('.tve-ic-checkmark').remove();
                this.icon_container.append(icon);
                if (lead_generation.getConnectionType() == 'api') {
                    /**
                     * update elements related to the api form fields
                     */
                    if (lead_generation.api_form_data.elements && lead_generation.api_form_data.elements[_field]) {
                        lead_generation.api_form_data.elements[_field].icon = icon.attr('data-cls');
                    }
                }

            }
        },
        /**
         * remove custom html code
         */
        remove_custom_html: function () {
            lead_generation.customFormCode('');
            lead_generation.connection_type = '';
            this.dashboard();
        },
        /**
         * show options for step 1 of creating a new connection
         */
        connection_form: function ($btn) {
            TVE_Editor_Page.overlay();
            var connection_type = $btn.attr('data-step2') ? $('#connection-type').val() : $btn.attr('data-connection-type');
            lead_generation.edit_api = $btn.attr('data-key') ? $btn.attr('data-key') : '';
            ajax({
                route: 'form',
                connections: lead_generation.getApiConnections(),
                api_fields: lead_generation.api_form_data.elements || null,
                connection_type: connection_type,
                edit: lead_generation.edit_api,
                extra: lead_generation.api_form_data && lead_generation.api_form_data.extra ? lead_generation.api_form_data.extra : {}
            }).done(function (response) {
                load_lightbox_content(response.lb_html);
                lead_generation.connection_type = connection_type;
                show_lightbox();
                TVE_Editor_Page.overlay(true);
            });
        },
        /**
         * actions related to API connections
         */
        api: {
            readExtraSettings: function ($container) {
                var _settings = lead_generation && lead_generation.api_form_data && lead_generation.api_form_data.extra ? lead_generation.api_form_data.extra : {};
                $container.find('.tve-api-extra').each(function () {
                    var $this = $(this),
                        _parts = $this.attr('name').split('_'),
                        _main = _parts.shift(),
                        _field = _parts.join('_');

                    _settings[_main] = _settings[_main] || {};
                    _settings[_main][_field] = $this.val();
                });

                return _settings;
            },
            _ajax: function (data) {
                TVE_Editor_Page.overlay();
                data = $.extend({
                    action: 'tve_api_editor_actions',
                    connections: lead_generation.getApiConnections(),
                    api_fields: lead_generation.api_form_data.elements || null,
                    ajax_load: ''
                }, data);

                return ajax(data).done(function () {
                    TVE_Editor_Page.overlay('close');
                });
            },
            /**
             * re-fetch the API connections list (as a html select)
             */
            reload_apis: function () {
                $('#thrive-api-list').remove();
                this._ajax({
                    route: 'apiSelect',
                    force_fetch: '1',
                    edit: lead_generation.edit_api,
                    extra: lead_generation.api_form_data && lead_generation.api_form_data.extra ? lead_generation.api_form_data.extra : {}
                }).done(function (response) {
                    $('#thrive-api-connections').replaceWith(response);
                });
            },
            /**
             * get all lists from an API connection
             * @param $api
             * @param force_fetch
             */
            api_get_lists: function ($api, force_fetch) {
                this._ajax({
                    route: 'apiLists',
                    api: $api.jquery ? $api.val() : $api,
                    force_fetch: typeof force_fetch !== 'undefined' && force_fetch === true ? '1' : 0,
                    extra: lead_generation.api_form_data && lead_generation.api_form_data.extra ? lead_generation.api_form_data.extra : {}
                }).done(function (response) {
                    $('#thrive-api-list').replaceWith(response);
                });
            },
            /**
             * reload Lists from an API connection
             * @param $link
             */
            reload_lists: function ($link) {
                this.api_get_lists($link.attr('data-api'), true);
            },
            /**
             * save the current API connection and the selected list
             */
            save: function ($btn) {
                var $api = $('#thrive-api-connections-select'),
                    $list = $('#thrive-api-list-select');

                if (!$api.val() || !$list.val()) {
                    alert('Please choose an existing API connection and a mailing list!');
                    return true;
                }
                /**
                 * if edit form <-> remove the current selection
                 */
                if ($btn.attr('data-edit') && $btn.attr('data-edit') != $api.val()) {
                    lead_generation.removeApiConnection($btn.attr('data-edit'));
                }
                lead_generation.setApiConnection($api.val(), $list.val());
                TVE_Content_Builder.auto_responder.dashboard();

                lead_generation.api_form_data.extra = this.readExtraSettings($lb);
            },
            /**
             * remove an API connection
             * @param $link
             */
            remove: function ($link) {
                lead_generation.removeApiConnection($link.attr('data-key'));
                TVE_Content_Builder.auto_responder.dashboard();
            },
            /**
             * set the thank you url
             * @param $input
             */
            thank_you_url: function ($input) {
                var url = $input.val();
                if (!url.match(/^http(s)?/) && !url.match(/^\/\//)) {
                    url = 'http://' + url;
                    $input.val(url);
                }
                lead_generation.api_form_data.thank_you_url = url;
            },
            /**
             *
             * @param $select
             */
            submit_option_changed: function ($select) {
                var option = $select.val(),
                    $url = $select.next('input').hide();
                if (option == 'redirect') {
                    $url.show();
                }
                lead_generation.api_form_data.submit_option = option;
            }
        }
    };

    jQuery.fn.extend({
        /**
         * Called on .edit_mode element
         * @returns {*}
         */
        tve_lead_generation: function () {

            var $element = this;

            var model = {

                code_separator: '__CONFIG_lead_generation_code__',
                $formContainer: $element.find('.thrv_lead_generation_container'),
                jsonFields: null,
                form_code: '',
                api_config: {},
                connection_type: '',
                api_form_data: {},

                init: function () {
                    if ($element.data('tve-version') !== '1') {
                        this.upgradeHtml();
                    }
                    this.readCustomFormCode();
                    this.connection_type = $element.attr('data-connection');
                    if (!this.connection_type) {
                        this.connection_type = this.customFormCode().length ? 'custom-html' : '';
                    }

                    if (this.connection_type == 'api') {
                        this.readApiElements();
                    }

                    return this;
                },

                /**
                 * returns the way this form is currently connected : via API, or via custom HTML code
                 */
                getConnectionType: function () {
                    return this.connection_type;
                },

                /**
                 * record a new api connection or change the list_id for an existing one
                 * @param api
                 * @param list_id
                 */
                setApiConnection: function (api, list_id) {
                    this.api_config[api] = list_id;
                    this.connection_type = 'api';
                },

                /**
                 * get existing connections
                 * @returns {*}
                 */
                getApiConnections: function () {
                    return this.api_config;
                },

                /**
                 * remove an API connection
                 * @param api
                 */
                removeApiConnection: function (api) {
                    delete this.api_config[api];
                    if (jQuery.isEmptyObject(this.api_config)) {
                        this.connection_type = '';
                    }
                },

                setParsedResponse: function (response) {
                    this.jsonFields = response;
                    this.customFormCode(response.stripped_code);
                    this.persistentData();
                },

                readCustomFormCode: function () {
                    this.form_code = $element.find('.thrv_lead_generation_code').text().replace(new RegExp(this.code_separator, 'g'), '');
                    return this;
                },

                writeCustomFormCode: function () {
                    if (this.connection_type != 'api') {
                        $element.find('.thrv_lead_generation_code').text(this.code_separator + this.form_code + this.code_separator);
                    } else {
                        $element.find('.thrv_lead_generation_code').text('');
                    }
                    this.config('connection', this.connection_type);
                    return this;
                },

                /**
                 * getter / setter for the custom HTML form code
                 * saves / reads from the hidden div containing the code
                 */
                customFormCode: function () {
                    if (arguments.length) {
                        this.form_code = arguments[0];
                        return this;
                    }
                    return this.form_code;
                },

                /**
                 * upgrade old html to newer version of extended lead generation element
                 */
                upgradeHtml: function () {
                    $element.attr('data-tve-version', 1);
                    $element.find('input[type="text"], input[type="email"]').parent().addClass('tve_lg_input');
                    $element.find('textarea').parent().addClass('tve_lg_textarea');
                    $element.find('select').parent().addClass('tve_lg_dropdown');
                    $element.find('input[type="radio"]').parents('.tve_lg_input_container ').first().addClass('tve_lg_radio');
                    $element.find('input[type="checkbox"]').parents('.tve_lg_input_container ').first().addClass('tve_lg_checkbox');
                    $element.find('button').parent().addClass('tve_lg_submit');
                },

                /**
                 * Foreach input,select,textarea in lead generation read properties and send them to lightbox
                 * to have persistent lightbox
                 * @returns {model}
                 */
                persistentData: function () {
                    var self = this,
                        element_found = false;
                    if (this.connection_type == 'api') {
                        /**
                         * mark everything as "not displayed"
                         */
                        jQuery('#generated_inputs_container .tve-lg-display-elem').prop('checked', false).trigger('change');
                    }
                    $element.find('input, select, textarea').each(function () {

                        var _input = jQuery(this),
                            _inputIcon = _input.parent().find('.thrv_icon'),
                            elem = _input.data('field'),
                            $label = jQuery('#txt_label_' + (elem ? elem : ''));

                        element_found = true;
                        if (elem && ($label.length || _input.is('select'))) {
                            if (_input.data('placeholder')) {
                                $label.val(_input.data('placeholder')).trigger('change');
                            }
                            jQuery('#validation_' + elem).val(_input.data('validation')).trigger('change');
                            if (_input.attr('type') === 'radio') {
                                jQuery('#required_' + _input.data('parent-field')).prop('checked', _input.data('required') ? true : false).trigger('change');
                            } else {
                                jQuery('#required_' + elem).prop('checked', _input.data('required') ? true : false).trigger('change');
                            }

                            if (_inputIcon.length) {
                                var _icon = _inputIcon.find('.tve_sc_icon').data('tve-icon');
                                jQuery('#icon_' + elem).prop('checked', true)
                                    .parent().append('<span data-cls="' + _icon + '" class="icomoon-icon"><span class="' + _icon + '"></span></span>');

                                if (self.connection_type == 'api') {
                                    // set these values also in the element's config
                                    self.api_form_data.elements[elem].icon = _icon;
                                    self.api_form_data.elements[elem].show_icon = 1;
                                }
                            }

                            if (self.connection_type == 'api') {
                                jQuery('#elem_display_' + elem).prop('checked', true).trigger('change');
                            }

                        }
                    });
                    return this;
                },

                config: function (what, value) {
                    if (typeof what === 'undefined' | what.length === 0) {
                        throw "Invalid option";
                    }
                    if (typeof value === 'undefined') {
                        return this.get(what);
                    }
                    return this.set(what, value);
                },

                set: function (what, value) {
                    $element.attr('data-' + what, value);
                    return this;
                },

                get: function (what) {
                    return $element.attr('data-' + what);
                },

                /**
                 * generate a container for a form field
                 *
                 * @param input_name encoded input name
                 * @param container_class extra css class to add to the container
                 */
                generateContainer: function (input_name, container_class) {
                    var wrapper = $element.find('[data-field="' + input_name + '"]').parents('.tve_lg_input_container').first(),
                        container = jQuery('<div class="tve_lg_input_container ' + container_class + '"></div>');
                    if (!wrapper.length) {
                        return container;
                    }
                    return container.attr({
                        'class': wrapper.attr('class'),
                        'id': wrapper.attr('id'),
                        'style': wrapper.attr('style')
                    });
                },
                /**
                 * move icon styles from the (possibly existing) previous icon to the newly added one
                 * @param $source
                 * @param $dest
                 * @returns {*}
                 */
                copyIconStyles: function ($source, $dest) {
                    if (!$source.length) {
                        $dest.find('span').addClass('tve_white');
                        return;
                    }

                    //copy styles and classes for parent
                    $dest.attr({
                        'style': $source.attr('style'),
                        'class': $source.attr('class')
                    });

                    //manage icon classes
                    var source_classes = $source.find('.tve_sc_icon').attr('class').split(" ");
                    for (var i = 0, source_class; source_class = source_classes[i++];) {
                        if (source_class.indexOf('icon-') === -1) {
                            $dest.find('.tve_sc_icon').addClass(source_class);
                        }
                    }

                    //manage icon styles
                    $dest.find('.tve_sc_icon').attr('style', $source.find('.tve_sc_icon').attr('style'));

                    //manage custom colors
                    if ($source.find('.tve_sc_icon').attr('data-tve-custom-colour')) {
                        $dest.find('.tve_sc_icon').attr('data-tve-custom-colour', $source.find('.tve_sc_icon').attr('data-tve-custom-colour'));
                    }
                    return $dest;
                },

                /**
                 * if there is an icon set as parameter the we have to set the right padding for its input
                 * if not then we set the right padding for input the same with left padding
                 * @param $input
                 * @param $icon
                 */
                setInputPaddingForIcon: function ($input, $icon) {
                    setTimeout(function () {
                        if (typeof $icon === 'undefined') {
                            $input.css('padding-right', $input.css('padding-left'));
                            return;
                        }
                        var _icon_width = $icon.width();
                        if (_icon_width) {
                            $input.tve_css('padding-right', (2 * _icon_width) + 'px', 'important');
                        }
                    }, 0);
                },

                /**
                 * read a string property for a form element from the generated inputs table
                 *
                 * @param input
                 * @param type
                 * @private
                 */
                _readStringProp: function (input, type) {
                    return jQuery('#' + type + '_' + input).val();
                },

                /**
                 * read a boolean (checkbox) property for a form element from the generated inputs table
                 *
                 * @param input
                 * @param type
                 * @private
                 */
                _readBoolProp: function (input, type) {
                    return jQuery('#' + type + '_' + input).is(':checked') ? 1 : 0;
                },

                /**
                 * copy element styles (custom colors, class etc) from the old form element to the new one
                 * @param $newElem jQuery
                 * @param $oldElem jQuery
                 * @private
                 */
                _copyElementStyles: function ($newElem, $oldElem) {
                    if (!$oldElem.length) {
                        return $newElem;
                    }
                    $newElem.attr('style', $oldElem.attr('style'));
                    $newElem.attr('class', $oldElem.attr('class'));
                    if ($oldElem.attr('data-tve-custom-colour')) {
                        $newElem.attr('data-tve-custom-colour', $oldElem.attr('data-tve-custom-colour'));
                    }

                    return $newElem;
                },

                /**
                 * insert icon inside the $input.parent() div and copy over styles from the previous used icon, if any
                 *
                 * @param $input
                 * @param $oldInput
                 * @private
                 */
                _insertIcon: function ($input, $oldInput) {
                    var $inputContainer = $input.parent(),
                        icon = jQuery('<div class="thrv_wrapper thrv_icon tve_brdr_solid"><span></span></div>'),
                        selected = jQuery("#icon_" + $input.attr('data-field')).parent().find('.icomoon-icon');

                    if (!selected.length) {
                        return;
                    }

                    icon.find('span').attr('class', 'tve_sc_icon').addClass(selected.attr('data-cls'))
                        .attr('data-tve-icon', selected.attr('data-cls'));

                    this.copyIconStyles($oldInput.parent().find('.thrv_icon'), icon);

                    $inputContainer.prepend(icon);
                    this.setInputPaddingForIcon($input, icon);
                },

                /**
                 * render a text input
                 * @param input encoded input name
                 * @param element the data about the input
                 * @returns {*}
                 */
                renderTextInput: function (input, element) {

                    var first_input = $element.find('.tve_lead_generated_inputs_container').find('input[type="text"],input[type="email"]').filter(':visible').first(),
                        placeholder = this._readStringProp(input, 'txt_label'),
                        $input = jQuery('<input type="text" data-field="' + input + '" data-required="' + this._readBoolProp(input, 'required') +
                            '" data-validation="' + this._readStringProp(input, 'validation') + '" name="' + element.name +
                            '" placeholder="' + placeholder + '" data-placeholder="' + placeholder + '" />'),
                        $oldInput = $element.find('input[data-field="' + input + '"]');

                    this._copyElementStyles($input, first_input);

                    var $inputContainer = this.generateContainer(input, 'tve_lg_input').append($input);

                    if (!this._readBoolProp(input, 'icon')) {
                        this.setInputPaddingForIcon($input);
                        return $inputContainer;
                    }

                    this._insertIcon($input, $oldInput);

                    return $inputContainer;
                },
                /**
                 * render a dropdown (select) element
                 * @param drop_down the encoded html select name
                 * @param element data about the select element
                 * @returns {*}
                 */
                renderDropdown: function (drop_down, element) {

                    var placeholder = this._readStringProp(drop_down, 'txt_label'),
                        $dropDown = jQuery('<select data-field="' + drop_down + '" data-required="' + this._readBoolProp(drop_down, 'required') +
                            '" name="' + element.name + '" data-placeholder="' + placeholder + '"></select>'),

                        $oldDropDown = $element.find('select[data-field="' + drop_down + '"]');

                    this._copyElementStyles($dropDown, $oldDropDown);

                    var $inputContainer = this.generateContainer(drop_down, 'tve_lg_dropdown tve_lg_select_container').append($dropDown);
                    jQuery.each(element.options, function (i, option) {
                        if (option.value === '' && placeholder) {
                            option.label = placeholder;
                        }
                        $dropDown.append(jQuery('<option></option>').val(option.value).html(option.label));
                    });

                    if (!this._readBoolProp(drop_down, 'icon')) {
                        this.setInputPaddingForIcon($dropDown);
                        return $inputContainer;
                    }

                    this._insertIcon($dropDown, $oldDropDown);

                    return $inputContainer;
                },
                /**
                 * render a suite of radio input elements
                 *
                 * @param radio original input name
                 * @param element data about the element being rendered
                 * @returns {*}
                 */
                renderRadioInput: function (radio, element) {

                    var $inputContainer = this.generateContainer(radio, 'tve_lg_radio tve_clearfix'),
                        radio_required = this._readBoolProp(radio, 'required'),
                        self = this;

                    jQuery.each(this.jsonFields.elements[radio].options, function (encoded_value, radio_value) {
                        var radio_id = radio + "_" + encoded_value,
                            label_for = radio_id + '_' + (static_id++),
                            radio_placeholder = self._readStringProp(radio_id, 'txt_label'),
                            $radio_wrapper = jQuery('<div class="tve_lg_radio_wrapper"></div>'),
                            $radioInput = jQuery('<input data-parent-field="' + radio + '" data-required="' + radio_required +
                                '" data-field="' + radio_id + '" data-placeholder="' + radio_placeholder + '" id="' + label_for + '" type="radio" name="' +
                                element.name + '" value="' + radio_value + '" />'),
                            $radioLabel = jQuery('<label for="' + label_for + '">' + radio_placeholder + '</label>'),
                            old_custom_color = jQuery($element.find('label[for="' + radio_id + '"]')).attr('data-tve-custom-colour');

                        if (old_custom_color) {
                            $radioLabel.attr('data-tve-custom-colour', old_custom_color);
                        }

                        $radio_wrapper.append($radioInput).append($radioLabel);
                        $inputContainer.append($radio_wrapper);
                    });

                    return $inputContainer;
                },
                /**
                 * render a textarea element
                 * @param textarea the encoded textarea name
                 * @param element data about textarea
                 * @returns {*}
                 */
                renderTextarea: function (textarea, element) {

                    var textarea_placeholder = this._readStringProp(textarea, 'txt_label'),
                        $textarea = jQuery('<textarea data-field="' + textarea + '" data-required="' + this._readBoolProp(textarea, 'required') +
                            '" data-validation="' + this._readStringProp(textarea, 'validation') + '" name="' + element.name + '" placeholder="' +
                            textarea_placeholder + '" data-placeholder="' + textarea_placeholder + '"></textarea>'),
                        $oldTextarea = $element.find('textarea[data-field="' + textarea + '"]');

                    this._copyElementStyles($textarea, $oldTextarea);

                    var $inputContainer = this.generateContainer(textarea, 'tve_lg_textarea tve_clearfix').append($textarea);

                    if (!this._readBoolProp(textarea, 'icon')) {
                        this.setInputPaddingForIcon($textarea);
                        return $inputContainer;
                    }

                    this._insertIcon($textarea, $oldTextarea);

                    return $inputContainer;
                },
                /**
                 * render a checkbox input element
                 * @param checkbox_id the encoded checkbox name
                 * @param element data about the checkbox being rendered
                 * @returns {*}
                 */
                renderCheckboxInput: function (checkbox_id, element) {

                    var $inputContainer = this.generateContainer(checkbox_id, 'tve_lg_checkbox tve_clearfix'),
                        checkbox_placeholder = this._readStringProp(checkbox_id, 'txt_label'),
                        label_for = checkbox_id + '_' + (static_id++),
                        $checkbox_wrapper = jQuery('<div class="tve_lg_checkbox_wrapper"></div>'),
                        $checkboxInput = jQuery('<input data-required="' + this._readBoolProp(checkbox_id, 'required') + '" data-field="' + checkbox_id + '" data-placeholder="' + checkbox_placeholder + '" id="' + label_for + '" type="checkbox" name="' + element.name + '" value="' + element.value + '" />'),
                        $checkboxLabel = jQuery('<label for="' + label_for + '">' + checkbox_placeholder + '</label>'),
                        old_custom_color = jQuery($element.find('label[for="' + checkbox_id + '"]')).attr('data-tve-custom-colour');

                    if (old_custom_color) {
                        $checkboxLabel.attr('data-tve-custom-colour', old_custom_color);
                    }

                    $checkbox_wrapper.append($checkboxInput).append($checkboxLabel);

                    return $inputContainer.append($checkbox_wrapper);
                },
                /**
                 * render a text node, e.g. a shortcode included in the autoresponder code
                 * @param element
                 */
                renderTextNode: function (element) {
                    return '<span>' + element.value + '</span>';
                },
                /**
                 * render a hidden input element
                 * @param element
                 */
                renderHiddenInput: function (element) {
                    return '<input id="' + element.name + '" type="hidden" name="' + element.name + '"' + (element.className ? ' class="' + element.className + '"' : '') + ' value="' + element.value + '">';
                },
                /**
                 * generate the form and insert it in the editor page
                 * @returns {model}
                 */
                generateForm: function () {
                    var form_data = this.connection_type == 'api' ? this.api_form_data : this.jsonFields;
                    var inputs_count = 1,
                        $form = jQuery('<form method="' + form_data.form_method + '" action="' + form_data.form_action + '"></form>'),
                        $inputsContainer = jQuery('<div class="tve_lead_generated_inputs_container tve_clearfix"><div class="tve_lead_fields_overlay"></div></div>'),
                        self = this;

                    function appendElement(encoded_name) {
                        var elem_data = form_data.elements[encoded_name];
                        elem_data.processed = 1;
                        if (!elem_data.display && elem_data.type != 'hidden') {
                            return; // skip elements that have been marked as hidden
                        }
                        var element;
                        switch (elem_data.type) {
                            case 'shortcode':
                                element = self.renderTextNode(elem_data);
                                break;
                            case 'text':
                                element = self.renderTextInput(encoded_name, elem_data);
                                break;
                            case 'radio':
                                element = self.renderRadioInput(encoded_name, elem_data);
                                break;
                            case 'checkbox':
                                element = self.renderCheckboxInput(encoded_name, elem_data);
                                break;
                            case 'select':
                                element = self.renderDropdown(encoded_name, elem_data);
                                break;
                            case 'textarea':
                                element = self.renderTextarea(encoded_name, elem_data);
                                break;
                            case 'hidden':
                                element = self.renderHiddenInput(elem_data);
                                break;
                        }
                        if (element) {
                            $inputsContainer.append(element);
                            if (elem_data.type != 'shortcode') {
                                inputs_count++;
                            }
                        }
                    }

                    for (var i = 0, encoded_name; encoded_name = form_data.element_order[i++];) {
                        if (!form_data.elements[encoded_name]) {
                            continue;
                        }
                        appendElement(encoded_name);
                    }

                    /**
                     * it's possible that some fields are not in the element_order array
                     */
                    jQuery.each(form_data.elements, function (encoded_name, elem_data) {
                        if (elem_data.processed) {
                            return true;
                        }
                        appendElement(encoded_name);
                    });

                    $form.append(form_data.hidden_inputs);
                    $form.append(form_data.not_visible_inputs);

                    $inputsContainer.append('<div style="display: none">' + form_data.additional_fields + '</div>');
                    $inputsContainer.append($element.find('.tve_submit_container'));
                    $form.append($inputsContainer);

                    /* HOT FIX for TCB-545 - Icon dissapears when autoresponder code changed */
                    /* TODO: we need to find a way to properly manage icons on the submit button */
                    if (this.$formContainer.find('.tve_submit_container .thrv_icon').length) {
                        $inputsContainer.find('.tve_submit_container').prepend(this.$formContainer.find('.thrv_icon'));
                    }
                    this.$formContainer.html('').append($form);

                    $element.removeClass(function (index, class_name) {
                        return (class_name.match(/\btve_\d+/g) || []).join(' ');
                    });
                    $element.attr('data-inputs-count', inputs_count);
                    $element.addClass('tve_' + inputs_count);

                    this.writeCustomFormCode();

                    return this;
                },

                convertButtonToImage: function () {
                    var $submit = $element.find('.tve_submit_container');
                    if (!$submit.length) {
                        return;
                    }
                    $submit.removeClass('tve_lg_submit').addClass('tve_lg_image_submit');
                    $submit.html('<div class="image_placeholder"><a id="tve_lead_pick_img" class="tve_click tve_green_button clearfix" href="#" target="_self"><i class="tve_icm tve-ic-upload"></i><span>Add Media</span></a></div>');
                    hide_control_panel_menu();
                },

                convertImageToButton: function () {
                    var $image = $element.find('.tve_submit_container');
                    if (!$image.length) {
                        return;
                    }
                    $image.removeClass('tve_lg_image_submit').addClass('tve_lg_submit');
                    $image.html('<button type="submit">Submit</button>');
                    hide_control_panel_menu();
                },

                /**
                 * apply a vertical / horizontal style for the form
                 *
                 * @param style
                 */
                applyStyle: function (style) {

                    if (!style) {
                        style = $element.is('.thrv_lead_generation_vertical') ? 'thrv_lead_generation_vertical' : 'thrv_lead_generation_horizontal';
                    }

                    $element.removeClass('thrv_lead_generation_vertical thrv_lead_generation_horizontal').addClass(style)
                        .find('.tve_lg_input_container').removeClass('tve_lg_2 tve_lg_3');

                    var totalInputs = $element.find('.tve_lg_input,.tve_lg_radio,.tve_lg_checkbox,.tve_lg_textarea').length;

                    if (totalInputs === 1 || totalInputs === 3) {
                        $element.find('.tve_lg_input_container').addClass('tve_lg_2');
                    } else if (totalInputs >= 2) {
                        $element.find('.tve_lg_input_container').addClass('tve_lg_3');
                    }
                },

                /**
                 * read and build the API elements JSON from the existing fields in the form
                 */
                readApiElements: function () {
                    this.api_form_data.thank_you_url = $element.find('input#_back_url').val() || '';
                    this.api_form_data.submit_option = $element.find('input#_submit_option').val() || 'reload';
                    this.api_config_str = $element.find('#__tcb_lg_fc').val();
                    this.api_form_data.element_order = [];
                    var self = this;

                    $element.find('input,select,textarea').each(function () {
                        var field = jQuery(this).attr('data-field');
                        if (field) {
                            self.api_form_data.element_order.push(jQuery(this).attr('data-field'));
                        }
                    });
                    this.api_form_data.extra = TVE_Content_Builder.auto_responder.api.readExtraSettings($element);
                },
                /**
                 * set the received API data
                 * @param response
                 */
                setApiData: function (response) {
                    var self = this;
                    jQuery.each(response, function (k, v) {
                        if (k != 'lb_html' && k != 'elements') {
                            self.api_form_data[k] = v;
                        }
                    });

                    this.api_config = response.connections;

                    jQuery('#generated_inputs_container').on('change', 'input,select', function () {
                        var $this = jQuery(this),
                            element_id = this.id.replace(/txt_label_|validation_|required_|icon_|elem_display_/, '');

                        self.api_form_data.elements[element_id][$this.attr('data-elem-field')] = $this.is('input[type=checkbox]') ? ($this.is(':checked') ? 1 : 0) : $this.val();
                    });

                    if (!this.api_form_data.elements) {
                        this.api_form_data.elements = response.elements;
                        this.api_form_data.element_order = response.element_order;
                        this.persistentData();
                    } else {
                        /**
                         * read out any elements that might have been changed server-side - usually, hidden elements
                         */
                        jQuery.each(response.elements, function (element_name, element) {
                            /**
                             * if the element does not exist, we need to add it
                             */
                            if (!self.api_form_data.elements[element_name]) {
                                self.api_form_data.elements[element_name] = element;
                                return true; // continue
                            }
                            self.api_form_data.elements[element_name].value = element.value;
                        });
                        /**
                         * read the configuration from the previously stored elements
                         */
                        jQuery.each(this.api_form_data.elements, function (element_name, element) {
                            if (element.label) {
                                jQuery('#txt_label_' + element_name).val(element.label);
                            }
                            if (element.validation) {
                                jQuery('#validation_' + element_name).val(element.validation);
                            }
                            jQuery('#required_' + element_name).prop('checked', element.required ? true : false);
                            jQuery('#elem_display_' + element_name).prop('checked', element.display ? true : false);
                            var $icon = jQuery('#icon_' + element_name).prop('checked', element.show_icon ? true : false);
                            if (element.icon) {
                                $icon.parent().append('<span data-cls="' + element.icon + '" class="icomoon-icon"><span class="' + element.icon + '"></span></span>');
                            }
                        });
                    }

                    var $sortable = jQuery('#generated_inputs_container table tbody').sortable({
                        handle: '.tve-drag-handle',
                        axis: 'y',
                        helper: function (e, tr) {
                            var $new = tr.clone().empty();
                            tr.find('td').each (function () {
                                var $this = $(this);
                                $new.append($this.clone().css('width', $this.width() + 'px').css('height', $this.height() + 'px'));
                            });
                            return $new;
                        },
                        update: function () {
                            self.api_form_data.element_order = [];
                            $sortable.find('.lg_elem_field').each(function () {
                                self.api_form_data.element_order.push(this.value);
                            });
                        }
                    });

                }

            };

            return model.init();
        }
    });
})(jQuery);