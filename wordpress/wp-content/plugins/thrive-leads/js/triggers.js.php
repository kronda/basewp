<?php
/**
 * Trigger handlers - listen to the events sent out by triggers and show the correct forms
 */
?>
<script type = "text/javascript" id = "tve-main-js" >
var TL_Front = TL_Front || {};
/* minor hackery to ensure we have this available */
var ThriveGlobal = ThriveGlobal || {$j: jQuery.noConflict()};

/**
 * Ajax call to increment impressions for all forms that might be displayed on the page
 */
TL_Front.do_impression = function () {
    var data = TL_Front.impressions_data;
    if (data === undefined) {
        console.log("No form to register impression for !");
        return;
    }

    var ajax_data = {
        security: TL_Const.security,
        action: TL_Const.action_impression,
        tl_data: data,
        current_screen: TL_Const.current_screen
    };
    ThriveGlobal.$j.each(TL_Const.custom_post_data, function (k, v) {
        ajax_data[k] = v;
    });

    ThriveGlobal.$j.post(TL_Const.ajax_url, ajax_data).done(function () {
        //TODO: do something here if required
    });
};

TL_Front.switch_lightbox_state = function ($state_target, $current_state) {
    $state_target.find('.tve_p_lb_overlay').css('opacity', '0.8');
    $state_target.find('.tve_p_lb_content').css('top', $current_state.find('.tve_p_lb_content').css('top')).addClass('tve-leads-triggered');
    window.tve_lb_switch_state = true;
    return TL_Front.open_lightbox($state_target.find('.tl-lb-target'));
};

TL_Front.close_lightbox = function () {
    var $body = ThriveGlobal.$j('body'),
        $html = ThriveGlobal.$j('html'),
        overflow_hidden = 'tve-o-hidden tve-l-open tve-hide-overflow',
        $lightbox = arguments[0] || $body.data('tl-open-lightbox');
    if (!$lightbox || !$lightbox.length) {
        return;
    }
    $lightbox.find('.tve-tl-anim').removeClass('tve-leads-triggered');
    if (!window.tve_lb_switch_state) {
        $body.removeClass(overflow_hidden).css('padding-right', '');
        $html.removeClass(overflow_hidden).removeClass($html.data('tl-anim-class'));
    }
    window.tve_lb_switch_state = false;
    setTimeout(function () {
        $lightbox.addClass('tve_lb_closing');
        //$lightbox.removeClass('tve_lb_open tve_lb_opening tve_lb_closing tve_p_lb_background').css('display', 'none').find('tve_p_lb_content').trigger('tve.lightbox-close');
        $lightbox.removeClass('tve_lb_open tve_lb_opening tve_lb_closing tve_p_lb_background').css({
            'visibility': 'hidden',
            'position': 'fixed',
            'left': '-9000px'
        }).find('tve_p_lb_content').trigger('tve.lightbox-close');
    }, 200);

    $lightbox.find('.thrv_responsive_video iframe, .thrv_custom_html_shortcode iframe').each(function () {
        var $this = ThriveGlobal.$j(this);
        $this.attr('data-src', $this.attr('src'));
        $this.attr('src', '');
    });
    /**
     * close any error messages that might have been displayed on forms inside the lightbox
     */
    ThriveGlobal.$j('#tve-lg-error-container').hide();
};

TL_Front.open_lightbox = function ($target, TargetEvent) {

    function getBrowserScrollSize() {
        var $ = ThriveGlobal.$j;
        var css = {
            "border": "none",
            "height": "200px",
            "margin": "0",
            "padding": "0",
            "width": "200px"
        };

        var inner = $("<div>").css($.extend({}, css));
        var outer = $("<div>").css($.extend({
            "left": "-1000px",
            "overflow": "scroll",
            "position": "absolute",
            "top": "-1000px"
        }, css)).append(inner).appendTo("body")
            .scrollLeft(1000)
            .scrollTop(1000);

        var scrollSize = {
            "height": (outer.offset().top - inner.offset().top) || 0,
            "width": (outer.offset().left - inner.offset().left) || 0
        };

        outer.remove();
        return scrollSize;
    }

    var is_switch_state = window.tve_lb_switch_state;

    ThriveGlobal.$j.fn.thrive_iphone_placeholder && $target.find('input[placeholder]').thrive_iphone_placeholder();

    /* close any other opened lightboxes */
    TL_Front.close_lightbox(ThriveGlobal.$j('.tve_p_lb_background.tve_lb_open'));

    //$target.css('display', '').parents('.tl-style').css('display', '');
    $target.css({
        'visibility': '',
        'position': '',
        'left': '',
        'display': ''
    }).parents('.tl-style').css({
        'visibility': '',
        'position': '',
        'left': '',
        'display': ''
    });

    var $body = ThriveGlobal.$j('body'),
        $html = ThriveGlobal.$j('html'),
        overflow_hidden = 'tve-o-hidden tve-l-open tve-hide-overflow',
        scroll_width = getBrowserScrollSize().width,
        oPadding = parseInt($body.css('paddingRight')),
        has_lb_open = ThriveGlobal.$j('.tve_p_lb_background.tve_lb_open').length;

    if (isNaN(oPadding)) {
        oPadding = 0;
    }

    ThriveGlobal.$j('.tve_p_lb_close').off().on("click", function () {
        TL_Front.close_lightbox();
    });

    $body.off('keyup.tve_lb_close').on('keyup.tve_lb_close', function (e) {
        if (e.which == 27) {
            TL_Front.close_lightbox();
        }
    });

    ThriveGlobal.$j('.tve_p_lb_overlay').off('click.tve_lb_close').on('click.tve_lb_close', function () {
        TL_Front.close_lightbox();
    });

    $body.data('tl-open-lightbox', $target);

    $target.addClass('tve_p_lb_background');

    $body.addClass(overflow_hidden);
    $html.addClass(overflow_hidden);

    var wHeight = ThriveGlobal.$j(window).height(),
        page_has_scroll = wHeight < ThriveGlobal.$j(document).height();

    if (!is_switch_state && page_has_scroll) {
        $body.css('padding-right', (oPadding + scroll_width) + 'px');
    }

    //load the responsive video iframes
    $target.find('.thrv_responsive_video iframe, .thrv_custom_html_shortcode iframe').each(function () {
        var $this = jQuery(this);
        if ($this.attr('data-src')) {
            $this.attr('src', $this.attr('data-src'));
        }
    });

    var cls = '';
    /* append the animation class to the html element */
    ThriveGlobal.$j.each($target.parents('.tl-states-root').attr('class').split(' '), function (i, c) {
        if (c.indexOf('tl-anim') === 0) {
            cls = c;
            return false;
        }
    });
    $html.addClass(cls).data('tl-anim-class', cls);

    setTimeout(function () {

        setTimeout(function () {
            $target.addClass('tve_lb_opening');
        }, 0);

        /* reload any iframe that might be in there, this was causing issues with google maps embeds in hidden tabs */
        $target.find('iframe').not('.thrv_social_default iframe').each(function () {
            var $this = ThriveGlobal.$j(this);
            if ($this.data('tve_ifr_loaded') || !$this.attr('data-src')) {
                return;
            }
            $this.data('tve_ifr_loaded', 1).attr('src', $this.attr('data-src'));
        });

        function position_it() {
            var cHeight = $target.find('.tve_p_lb_content').outerHeight(true) + (2 * parseInt($target.css('padding-top'))),
                $lContent = $target.find('.tve_p_lb_content'),
                wHeight = ThriveGlobal.$j(window).height(),
                top = (wHeight - cHeight) / 2;

            $target.find('.tve_p_lb_overlay').css({
                height: (cHeight + 80) + 'px',
                'min-height': wHeight + 'px'
            });
            if (has_lb_open) {
                $lContent.animate({
                    top: (top < 40 ? 40 : top)
                }, 100);
            } else {
                $lContent.css('top', (top < 40 ? 40 : top) + 'px');
            }
            if (cHeight + 40 > wHeight) {
                $target.addClass('tve-scroll');
            }
        }

        position_it();
        ThriveGlobal.$j(window).on('resize', position_it);

    }, 20);

    setTimeout(function () {
        $target.removeClass('tve_lb_opening').addClass('tve_lb_open').find('.tve_p_lb_content').trigger('tve.lightbox-open');
        ThriveGlobal.$j(window).trigger('scroll');
    }, 300);

    // called to stop other events that might interact in the bad way with the lightbox, like a button that redirects the page.
    if (TargetEvent && TargetEvent.preventDefault) {
        TargetEvent.preventDefault();
        TargetEvent.stopPropagation();
    }
    $target.parents('.tl-states-root').off('switchstate').on('switchstate', function (e, $state) {
        var args = Array.prototype.slice.call(arguments, 1);
        TL_Front.switch_lightbox_state.apply(TL_Front, args);
    });
};
TL_Front.open_two_step_lightbox = TL_Front.open_lightbox;


<?php if (!empty($GLOBALS['tl_triggers']['ribbon'])) : ?>


TL_Front.open_ribbon = function ($target) {

    /**
     * used when there is also a Scroll Mat on the page, this will make sure the ribbon is opened after the greedy ribbon
     */
    function open_it() {
        var position = $target.attr('data-position') || 'top'; 

        if (position === 'top') {
            $target.css('top', ThriveGlobal.$j('#wpadminbar').length ? '32px' : '0px');
        } else if (position === 'bottom') {
            $target.css('bottom', '0px');
            $target.css('top', 'auto');
        }

        $target.find('.tve-ribbon-close').on('click', function () {
            $target.find('.thrv_responsive_video iframe, .thrv_custom_html_shortcode iframe').each(function () {
                var $this = ThriveGlobal.$j(this);
                $this.attr('data-src', $this.attr('src'));
                $this.attr('src', '');
            });
            $target.removeClass('tve-leads-triggered');
            if (position === 'top') {
                ThriveGlobal.$j('body').animate({'margin-top': 0 + 'px'}, 200);
            } else if (position === 'bottom') {
                ThriveGlobal.$j('body').animate({'margin-bottom': 0 + 'px'}, 200);
            }

            setTimeout(function () {
                $target.css(position, '');
            }, 400);
            /**
             * close any error messages that might have been displayed on forms inside the lightbox
             */
            ThriveGlobal.$j('#tve-lg-error-container').hide();
        });
        /**
         * Mozilla is really slow at applying the loaded css. we need this workaround to have it work in mozilla.
         */
        var iterations = 0,
            initial_height = $target.outerHeight(),
            ii = setInterval(function () {
                iterations++;
                var _h = $target.outerHeight();
                if (_h != initial_height || iterations == 10) {
                    clearInterval(ii);
                }
                if (position === 'top') {
                    ThriveGlobal.$j('body').animate({'margin-top': _h + 'px'}, 200);
                } else if (position === 'bottom') {
                    ThriveGlobal.$j('body').animate({'margin-bottom': _h + 'px'}, 200);
                }
            }, 100);

        $target.off('switchstate').on('switchstate', function (e, $target) {
            var args = Array.prototype.slice.call(arguments, 1);
            TL_Front.switch_ribbon_state.apply(TL_Front, args);
        });
    }

    if (TL_Const.forms.greedy_ribbon) {
        TL_Const.close_callbacks = TL_Const.close_callbacks || {};
        TL_Const.close_callbacks.greedy_ribbon = [open_it];
    } else {
        open_it();
    }
};


TL_Front.switch_ribbon_state = function ($target) {
    var h = $target.outerHeight(true),
        prop = $target.parent().attr('data-position') === 'top' ? 'margin-top' : 'margin-bottom',
        animation = {};
    animation[prop] = h + 'px';

    ThriveGlobal.$j('body').animate(animation, 200);

};
<?php endif ?>

<?php if (!empty($GLOBALS['tl_triggers']['greedy_ribbon'])) : ?>

TL_Front.open_greedy_ribbon = function ($target) {
    var $body =  ThriveGlobal.$j('body');
    $body.scrollTop(0);
    $target.css('top', ThriveGlobal.$j('#wpadminbar').length ? '32px' : '0px');

    var wHeight = ThriveGlobal.$j(window).height();
    $body.animate({'margin-top': wHeight + 'px'}, 600, 'linear');
    var greedyCondition = 1;
    ThriveGlobal.$j(window).scroll(function () {
        if (greedyCondition === 1) {
            var browserScroll = jQuery(document).scrollTop();
            if (browserScroll > wHeight) {
                $target.addClass('tve-no-animation');
                var greedyScroll = browserScroll - wHeight;
                $target.removeClass('tve-leads-triggered');
                $body.css('margin-top', '0px').scrollTop(greedyScroll);
                $target.removeClass('tve-no-animation');
                greedyCondition = 0;
                TL_Front.form_closed('greedy_ribbon');
            }
        }
    });
    $target.off('switchstate').on('switchstate', function (e, $target) {
    });
};
<?php endif ?>

TL_Front.open_screen_filler = function ($target) {
    var overflow_hidden = 'tve-so-hidden tve-sl-open tve-s-hide-overflow',
        html_body = ThriveGlobal.$j('html,body'),
        $html = ThriveGlobal.$j('html');

    ThriveGlobal.$j.fn.thrive_iphone_placeholder && $target.find('input[placeholder]').thrive_iphone_placeholder();

    $target.css('top', ThriveGlobal.$j('#wpadminbar').length ? '32px' : '0px').css('visibility', '');
    $target.find('.tve-screen-filler-close').on('click', function () {
        close_it($target);
    });

    html_body.addClass(overflow_hidden);

    var cls = '';
    /* append the animation class to the html element */
    ThriveGlobal.$j.each($target.attr('class').split(' '), function (i, c) {
        if (c.indexOf('tl-anim') === 0) {
            cls = c;
            return false;
        }
    });
    $html.addClass(cls).data('tl-s-anim-class', cls);

    function close_it($screen_filler) {

        $screen_filler.find('.thrv_responsive_video iframe, .thrv_custom_html_shortcode iframe').each(function () {
            var $this = ThriveGlobal.$j(this);
            $this.attr('data-src', $this.attr('src'));
            $this.attr('src', '');
        });

        $screen_filler.removeClass('tve-leads-triggered');
        ThriveGlobal.$j(document).off('keyup.close-screenfiller');
        ThriveGlobal.$j('body').animate({'margin-top': 0 + 'px'}, 200);
        html_body.removeClass(overflow_hidden);
        $html.removeClass($html.data('tl-s-anim-class'));
        setTimeout(function () {
            $screen_filler.css('top', '').hide();
        }, 400);
        /**
         * close any error messages that might have been displayed on forms inside the lightbox
         */
        ThriveGlobal.$j('#tve-lg-error-container').hide();
    }

    ThriveGlobal.$j(document).off('keyup.close-screenfiller').on('keyup.close-screenfiller', function (e) {
        if (e.which == 27) {
            close_it($target);
        }
    });
};

<?php if (!empty($GLOBALS['tl_triggers']['slide_in'])) : ?>
TL_Front.switch_slide_in_state = function ($state) {
    TL_Front.slide_in_position($state.find('.thrv-leads-slide-in'));
};
TL_Front.slide_in_position = function ($lContent) {
    if (ThriveGlobal.$j(window).width() <= 782) {
        var overflow_hidden = 'tve-o-hidden tve-l-open tve-hide-overflow',
            wHeight = ThriveGlobal.$j(window).height(),
            $body = ThriveGlobal.$j('body'),
            $html = ThriveGlobal.$j('html');

        setTimeout(function () {
            var elHeight = $lContent.outerHeight(),
                top = (wHeight - elHeight) / 2;
            $body.addClass(overflow_hidden);
            $html.addClass(overflow_hidden);
            $lContent.parents('.tve-leads-conversion-object').first().css({
                height: (elHeight + 80) + 'px',
                'min-height': wHeight + 'px'
            });
            $lContent.css('top', (top < 40 ? 40 : top) + 'px');
            if (elHeight + 40 > wHeight) {
                $lContent.parents('.tve-leads-slide-in').css('overflow-y', 'scroll');
            }
        }, 0);
    }
};
TL_Front.open_slide_in = function ($target) {
    var overflow_hidden = 'tve-o-hidden tve-l-open tve-hide-overflow',
        $body = ThriveGlobal.$j('body'),
        $html = ThriveGlobal.$j('html');

    ThriveGlobal.$j.fn.thrive_iphone_placeholder && $target.find('input[placeholder]').thrive_iphone_placeholder();

    TL_Front.slide_in_position($target.find('.thrv-leads-slide-in').filter(':visible'));

    function close_it($slidein) {
        $slidein.removeClass('tve-leads-triggered');
        ThriveGlobal.$j(document).off('keyup.close-slidein');
        $body.removeClass(overflow_hidden);
        $html.removeClass(overflow_hidden);
        $slidein.find('.thrv_responsive_video iframe, .thrv_custom_html_shortcode iframe').each(function () {
            var $this = ThriveGlobal.$j(this);
            $this.attr('data-src', $this.attr('src'));
            $this.attr('src', '');
        });
        /**
         * close any error messages that might have been displayed on forms inside the lightbox
         */
        ThriveGlobal.$j('#tve-lg-error-container').hide();
    }

    $target.off().on('click', '.tve-leads-close', function () {
        close_it($target);
    });
    $target.on('switchstate', function (e, $state) {
        var args = Array.prototype.slice.call(arguments, 1);
        TL_Front.switch_slide_in_state.apply(TL_Front, args);
    });
    ThriveGlobal.$j(document).off('keyup.close-slidein').on('keyup.close-slidein', function (e) {
        if (e.which == 27) {
            close_it($target);
        }
    });
};
<?php endif ?>

TL_Front.close_form = function (element, trigger, action, config) {
    var $element = ThriveGlobal.$j(element),
        $parent = $element.parents('.tve-leads-triggered'), //every form has this class on its wrapper
        type = $parent.attr('data-tl-type'); //some of them have its type in data-tl-type

    //if no type then identify it from class
    if (type === undefined && $parent.hasClass('tve-leads-widget')) {
        type = 'widget';
    } else if (type === undefined && $parent.hasClass('tve-leads-post-footer')) {
        type = 'post-footer';
    } else if (type === undefined && $parent.hasClass('tve-leads-slide-in')) {
        type = 'slide-in';
    } else if (type === undefined && $parent.hasClass('tve-leads-in-content')) {
        type = 'in-content';
    } else if (type === undefined && $parent.hasClass('tve-leads-shortcode')) {
        type = 'shortcode';
    } else if (type === undefined && $parent.hasClass('tve-leads-greedy_ribbon')) {
        type = 'greedy_ribbon';
    }

    //remove this class just because it is added dynamically and maybe we want to trigger it again
    $parent.removeClass('tve-leads-triggered');

    switch (type) {
        case 'ribbon':
            $parent.find('.tve-ribbon-close').trigger('click');//there already exists a bind for close
            break;
        case 'slide-in':
            $parent.find('.tve-leads-close').trigger('click');//there already exists a bind for close
            break;
        case 'post-footer'://case able for PHP Insert form too
        case 'in-content':
        case 'shortcode':
            $parent.fadeOut(200, function () {
                TL_Front.form_closed(type);
            });
            break;
        case 'widget':
            $parent.parent().slideUp(200);//its parent is a section tag; see the function thrive_dynamic_sidebar_params()
            break;
        case 'greedy_ribbon':
            ThriveGlobal.$j('body').animate({'margin-top': 0 + 'px'}, 600, 'linear', function () {
                TL_Front.form_closed(type);
            });
            break;
    }
};

TL_Front.form_closed = function (type) {
    if (TL_Const.close_callbacks && TL_Const.close_callbacks[type]) {
        ThriveGlobal.$j.each(TL_Const.close_callbacks[type], function (i, f) {
            if (ThriveGlobal.$j.isFunction(f)) {
                f();
            }
        });
    }
}

ThriveGlobal.$j(function () {
    ThriveGlobal.$j('.tve-leads-screen-filler iframe, .tve-leads-ribbon iframe').not('.thrv_social_default iframe').not('.tcb-dr-done').each(function () {
        var $frame = ThriveGlobal.$j(this).addClass('tcb-dr-done');
        if ($frame.attr('src')) {
            $frame.attr('data-src', $frame.attr('src'));
        }
        $frame.attr('src', '');
    });
    ThriveGlobal.$j(TL_Front).on('showform.thriveleads', function (event, data) {
        var $target = data.$target ? data.$target : ThriveGlobal.$j('.' + data.form_id),
            $anim_target;
        if (!$target.length) {
            return;
        }
        if ($target.attr('data-s-state')) {
            /**
             * find the already subscribed state and show it
             */
            $target = $target.closest('.tl-states-root').find('[data-state="' + $target.attr('data-s-state') + '"] .tl-lb-target');
        }
        $anim_target = $target.hasClass('tve-tl-anim') ? $target : $target.find('.tve-tl-anim');
        $target.css('display', '');
        setTimeout(function () {
            $anim_target.addClass('tve-leads-triggered');
            TCB_Front.postGridLayout();
        }, 0);

        if (typeof TL_Front['open_' + data.form_type] === 'function') {
            TL_Front['open_' + data.form_type]($target, data.TargetEvent);
        } else {
            $target.show();
        }
        setTimeout(function () {
            $target.find('.thrv_responsive_video iframe, .thrv_custom_html_shortcode iframe').each(function () {
                var $this = ThriveGlobal.$j(this);
                if ($this.attr('data-src')) {
                    $this.attr('src', $this.attr('data-src'));
                }
            });
        }, 200);
    });
    if (!TL_Const.ajax_load) {
        setTimeout(TL_Front.do_impression, 2000);
    }
});
</script >