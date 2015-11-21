/**
 * jQuery Plugin to obtain touch gestures from iPhone, iPod Touch and iPad, should also work with Android mobile phones (not tested yet!)
 * Common usage: wipe images (left and right to show the previous or next image)
 *
 * @author Andreas Waltl, netCU Internetagentur (http://www.netcu.de)
 * @version 1.1.1 (9th December 2010) - fix bug (older IE's had problems)
 * @version 1.1 (1st September 2010) - support wipe up and wipe down
 * @version 1.0 (15th July 2010)
 */
(function($) {
    $.fn.touchwipe = function(settings) {
        var config = {
            min_move_x: 20,
            min_move_y: 20,
            wipeLeft: function() { },
            wipeRight: function() { },
            wipeUp: function() { },
            wipeDown: function() { },
            onTouchDown: function() {},
            preventDefaultEvents: true,
            pe: window.PointerEvent,
            mspe: window.MSPointerEvent
        };

        if (settings) $.extend(config, settings);

        this.each(function() {
            var startX;
            var startY;
            var isMoving = false;

            function cancelTouch() {
                this.removeEventListener('touchmove', onTouchMove);
                this.removeEventListener('MSPointerMove', onTouchMove);
                this.removeEventListener('pointermove', onTouchMove);
                startX = null;
                isMoving = false;
            }

            function onTouchMove(e) {
                if(config.preventDefaultEvents) {
                    if (e.preventDefault) {
                        e.preventDefault();
                    } else {
                        e.returnValue = false;
                        if (e.stopPropagation) {
                            e.stopPropagation();
                        }
                    }
                }
                if (isMoving) {
                    var x = config.pe || config.mspe ? e.clientX : e.touches[0].pageX;
                    var y = config.pe || config.mspe ? e.clientY : e.touches[0].pageY;
                    var dx = startX - x;
                    var dy = startY - y;

                    if(Math.abs(dx) >= config.min_move_x) {
                        cancelTouch();
                        if(dx > 0) {
                            config.wipeLeft();
                        }
                        else {
                            config.wipeRight();
                        }
                    } else if(Math.abs(dy) >= config.min_move_y) {
                        cancelTouch();
                        if(dy > 0) {
                            config.wipeDown();
                        }
                        else {
                            config.wipeUp();
                        }
                    } else {
                        config.onTouchDown();
                    }
                }
            }

            function onTouchStart(e)
            {
                if (config.pe || config.mspe || (e.touches && e.touches.length == 1)) {
                    startX = config.pe || config.mspe ? e.clientX : e.touches[0].pageX;
                    startY = config.pe || config.mspe ? e.clientY : e.touches[0].pageY;
                    isMoving = true;
                    if (config.pe) {
                        this.addEventListener('pointermove', onTouchMove, false);
                    } else if (config.mspe) {
                        this.addEventListener('MSPointerMove', onTouchMove, false);
                    } else {
                        this.addEventListener('touchmove', onTouchMove, false);
                    }
                }
                config.onTouchDown.call(this, e);
            }
            if ('ontouchstart' in document.documentElement) {
                this.addEventListener('touchstart', onTouchStart, false);
            } else if (config.pe) {
                this.addEventListener('pointerdown', onTouchStart, false);
            } else if (config.mspe) {
                this.addEventListener('MSPointerDown', onTouchStart, false);
            }
        });

        return this;
    };

})(jQuery);