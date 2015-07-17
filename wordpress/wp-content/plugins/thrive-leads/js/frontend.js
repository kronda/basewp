/**
 * Frontend javascript functionalities handling the display of forms
 */
var TL_Front = TL_Front || {};

/* minor hackery to ensure we have this available */
var ThriveGlobal = ThriveGlobal || {$j: jQuery.noConflict()};

TL_Front.add_page_css = function (stylesheets) {
    ThriveGlobal.$j.each(stylesheets, function (_id, href) {
        _id += '-css';
        if (!ThriveGlobal.$j('#' + _id).length) {
            ThriveGlobal.$j('<link rel="stylesheet" id="' + _id + '" type="text/css" href="' + href + '"/>').appendTo('head');
        }
    });
};

ThriveGlobal.$j(function() {

    function init() {
        if (!TL_Const.forms) {
            return false;
        }

        /**
         * try to detect the email field from a form and get its value
         *
         * @param $form
         */
        function try_getting_email($form) {
            /* if there is a field which requires email validation, use that */
            if ($form.find('[data-validation="email"]').length) {
                return $form.find('[data-validation="email"]').val();
            }
            var maybe_email = '';
            /* try search for 'email' in the input name */
            $form.find('input').each(function () {
                if (this.name && this.name.match(/email/i)) {
                    maybe_email = this.value;
                    return false;
                }
            });

            return maybe_email;
        }

        /**
         * listen for the forms submission, and send tracking data requests
         * the submit listener is delegated just to be sure we can track everything
         */
        ThriveGlobal.$j('body').on('submit', '.tve-leads-conversion-object form', function () {
            var $form = ThriveGlobal.$j(this),
                type = $form.parents('.tve-leads-conversion-object').first().attr('data-tl-type');

            if ($form.data('tl-do-submit') || !type || !TL_Const.forms[type]) {
                return true;
            }

            ThriveGlobal.$j.ajax({
                url: TL_Const.ajax_url,
                data: {
                    security: TL_Const.security,
                    action: TL_Const.action_conversion,
                    type: type,
                    tl_data: TL_Const.forms[type],
                    email: try_getting_email($form)
                },
                type: 'post',
                async: false
            });

            return true;
        });

        /**
         * event listener that allows setting custom post data in forms created with TCB and connected to an API
         */
        ThriveGlobal.$j('body').on('form_conversion.tcb', '.tve-leads-conversion-object form', function (event) {
            var $form = ThriveGlobal.$j(this),
                type = $form.parents('.tve-leads-conversion-object').first().attr('data-tl-type');

            if (!type || !TL_Const.forms[type]) {
                return true;
            }

            event.post_data = event.post_data || {};
            event.post_data.thrive_leads = {
                type: type,
                tl_data: TL_Const.forms[type]
            };
        }).on('lead_conversion_success.tcb', '.tve_lead_lock_shortcode form', function (event) {
            var $form = ThriveGlobal.$j(this),
                $container = $form.parents('.tve_content_lock');

            $container.removeClass('tve_lead_lock').find('.tve_lead_lock_shortcode').remove();

            event.content_unlocked = true;
        });
    }

    if (TL_Const.ajax_load) {

        /**
         * ajax load all the forms that are to be displayed on this page
         */
        var ajax_data = {
            main_group_id: TL_Const.main_group_id,
            shortcode_ids: TL_Const.shortcode_ids,
            two_step_ids: TL_Const.two_step_ids,
            action: 'tve_leads_ajax_load_forms',
            security: TL_Const.security,
            display_options: TL_Const.display_options
        };
        ThriveGlobal.$j.each(TL_Const.custom_post_data, function (k, v) {
            ajax_data[k] = v;
        });
        ThriveGlobal.$j.ajax({
            url: TL_Const.ajax_url,
            type: 'post',
            dataType: 'json',
            data: ajax_data,
            xhrFields: {
                withCredentials: true
            }
        }).done(function (response) {
            if (!response || !response.res || !response.js || !response.html) {
                return;
            }
            TL_Front.add_page_css(response.res.css);
            TL_Front.add_page_css(response.res.fonts);

            if (response.html) {
                if (!response.html.widget) {
                    ThriveGlobal.$j('.tl-widget-container').remove();
                }
                ThriveGlobal.$j.each(response.html, function (elem_type, html) {
                    if (elem_type === 'in_content') {
                        // move the placeholder after the nth paragraph
                        var fn = 'after',
                            post = ThriveGlobal.$j('.tve-tl-cnt-wrap'),
                            p = post.find('p');
                        if (p.length === 0 && response.in_content_pos == 0) {
                            ThriveGlobal.$j('.tve-tl-cnt-wrap').prepend(html);
                        } else {
                            if (response.in_content_pos == 0) {
                                response.in_content_pos = 1;
                                fn = 'before';
                            }
                            p.eq(parseInt(response.in_content_pos) - 1)[fn](html);
                        }
                    } else {
                        var $placeholder = ThriveGlobal.$j('.tl-placeholder-f-type-' + elem_type);
                        if (response.js[elem_type] && response.js[elem_type].content_locking) {
                            /**
                             * content locking shortcode - add the blur class if this is the case
                             * or show the locked content if the user has a conversion registered
                             */
                            var $parent = $placeholder.parents('.tve_content_lock').first();

                            if (response.js[elem_type].has_conversion) {
                                $parent.removeClass('tve_lock_hide');
                                return true;
                            }

                            if (response.js[elem_type].lock == 'tve_lock_blur') {
                                $parent.removeClass('tve_lock_hide').addClass(response.js[elem_type].lock);
                            }
                        }
                        $placeholder.replaceWith(html);
                        if (elem_type === 'widget') {
                            ThriveGlobal.$j('.tl-widget-container').children().unwrap();
                        }
                    }
                });
            }
            if (response.body_end) {
                ThriveGlobal.$j('body').append(response.body_end);
            }
            /**
             * rebind the TCB event listeners
             */
            setTimeout(function () {
                TCB_Front.event_triggers(ThriveGlobal.$j('body'));
                TCB_Front.onDOMReady();
            }, 10);
            TL_Const.forms = response.js;
            init();
        });
        return;
    }
    /**
     * if there is no ajax_load setting, init stuff as usual
     */
    init();
});