<?php
$timezone_offset = get_option('gmt_offset');
$sign = ($timezone_offset < 0 ? '-' : '+');
$min = abs($timezone_offset) * 60;
$hour = floor($min / 60);
$tzd = $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($min % 60, 2, '0', STR_PAD_LEFT);

$tve_globals = tve_get_post_meta(get_the_ID(), 'tve_globals', true);
$events_config = array(
    array(
        't' => 'click',
        'a' => 'thrive_lightbox',
        'config' => array(
            'l_id' => empty($tve_globals['lightbox_id']) ? '' : $tve_globals['lightbox_id'],
            'l_anim' => 'slide_top'
        )
    )
);
?>

<div class="tve_lp_header tve_empty_dropzone tve_content_width">
</div>

<div class="tve_lp_content tve_editor_main_content tve_empty_dropzone thrv_wrapper tve_no_drag"
     style="margin-top: 50px; max-width: 1180px; background-attachment: scroll;">
    <h1 class="tve_p_center" style="color: #333; font-size: 63px;margin-top: 0;">Author Reveals... <font color="#f58000">5 Simple
            Strategies</font> to Boost Your Productivity </h1>

    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #459285">
            <div class="in lightSec">
                <div class="cck tve_clearfix">
                    <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="6">
                        <div class="tve_cb tve_cb6 tve_teal">
                            <div class="tve_cb_cnt">
                                <div class="thrv_wrapper thrv_columns" style="margin: 0;">
                                    <div class="tve_colm tve_twc">
                                        <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="6">
                                            <div class="tve_cb tve_cb6 tve_white">
                                                <div class="tve_cb_cnt">
                                                    <div class="thrv_wrapper thrv_columns" style="margin: 0;">
                                                        <div class="tve_colm tve_twc">
                                                            <div style="width: 191px; margin-top: 0; margin-bottom: 0;"
                                                                 class="thrv_wrapper tve_image_caption img_style_framed aligncenter">
                                                            <span class="tve_image_frame">
                                                                <img class="tve_image steve_blake"
                                                                     src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/vibrant_big_shane.jpg' ?>"
                                                                     style="width: 191px"/>
                                                            </span>
                                                            </div>
                                                        </div>
                                                        <div class="tve_colm tve_twc tve_lst">
                                                            <h4 class="vibrant_webinar_heading"
                                                                style="font-size: 30px; margin-top: 10px;margin-bottom:0px;">
                                                                <font color="#f58000">*FREE LIVE*</font>
                                                            </h4>
                                                            <h4 class="vibrant_webinar_heading"
                                                                style="color: #333333;font-size: 30px; margin-top: 0px;margin-bottom: 40px;">
                                                                &nbsp;&nbsp;WEBINAR</h4>

                                                            <p class="vibrant_webinar_heading"
                                                               style="color: #333333;margin-left: 5px;margin-bottom: 20px;">
                                                                With <span class="bold_text">Shane Melaugh</span></p>

                                                            <div style="width: 35px;margin: 0 0 0 40px;"
                                                                 class="thrvvi_wrapper tve_image_caption alignleft">
                                                                    <span class="tve_image_frame">
                                                                        <img class="tve_image"
                                                                             src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/vibrant_twitter.png' ?>"
                                                                             style="width: 35px"/>
                                                                    </span>
                                                            </div>
                                                            <div style="width: 35px; margin: 0 0 0 10px;"
                                                                 class="thrv_wrapper tve_image_caption alignleft">
                                                                    <span class="tve_image_frame">
                                                                        <img class="tve_image"
                                                                             src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/vibrant_facebook.png' ?>"
                                                                             style="width: 35px"/>
                                                                    </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tve_colm tve_twc tve_lst">
                                        <p style="color: #fff; font-size: 32px;margin-top: 25px;line-height: 32px;padding-bottom: 0;">
                                            <span class="bold_text">"Reserve Your Seat Now,</span> for this
                                            webinar training that will <span class="bold_text">change
                                        the way you think about working smart..."</span></p>

                                        <div class="thrv_wrapper thrv_button_shortcode tve_fullwidthBtn"
                                             data-tve-style="1">
                                            <div class="tve_btn tve_btn3 tve_nb tve_orange tve_bigBtn"
                                                 style="margin-top: 0px;margin-bottom: -20px;">
                                                <a class="tve_btnLink tve_evt_manager_listen" href=""
                                                   data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                                                <span class="tve_left tve_btn_im">
                                                    <i></i>
                                                    <span class="tve_btn_divider"></span>
                                                </span>
                                                    <span class="tve_btn_txt">Reserve My Seat &GT;&GT;</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #f2f2f2">
            <div class="in darkSec">
                <div class="cck tve_clearfix">
                    <div class="thrv_wrapper thrv_content_container_shortcode">
                        <div class="tve_clear"></div>
                        <div class="tve_center tve_content_inner" style="width: 950px;min-width:50px; min-height: 2em;">
                            <div class="thrv_wrapper thrv_columns" style="margin-top: 0;margin-bottom: 0;">
                                <div class="tve_colm tve_twc">
                                    <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-top: 0;">
                                        <div class="tve_colm tve_oth">
                                            <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="1">
                                                <div class="tve_cb tve_cb1 tve_teal" style="margin-top: 10px;">
                                                    <div class="tve_hd tve_cb_cnt">
                                                        <h3 style="color: #fff;">
                                                            October
                                                        </h3>
                                                        <span></span>
                                                    </div>
                                                    <div class="tve_cb_cnt">
                                                        <p class="tve_p_center"
                                                           style="color: #333333; font-size: 72px;margin-bottom: 0;margin-top: 0;padding-bottom: 0;">
                                                            <span class="bold_text">10</span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tve_colm tve_tth tve_lst">
                                            <p style="color: #333; font-size: 24px; margin-top: 20px; margin-bottom: 20px; line-height: 30px;padding-bottom: 0;">
                                                It’s
                                                happening <span class="bold_text">THIS Friday <br/>(October 10th) at...</span>
                                            </p>

                                            <div style="font-size: 16px; color: #333333;"
                                                 class="thrv_wrapper thrv_icon alignleft">
                                        <span style="margin-top: 5px; margin-right: 5px;"
                                              class="tve_sc_icon vibrant_whammy_icon_clock"
                                              data-tve-icon="vibrant_whammy_icon_clock"></span>
                                            </div>
                                            <p style="color: #333333; font-size: 18px;padding-bottom: 0;"><span
                                                    class="bold_text">at 12pm GMT</span> (London)</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="tve_colm tve_twc tve_lst">
                                    <p style="color: #333333; font-size: 24px; margin-top: 10px;margin-bottom: 0; padding-bottom: 0;">
                                        Time
                                        left:</p>

                                    <div class="thrv_wrapper thrv_countdown_timer tve_cd_timer_plain tve_clearfix init_done"
                                         data-date="<?php echo date('Y-m-d', strtotime('+9 days')) ?>"
                                         data-hour="<?php echo date('h') ?>"
                                         data-min="<?php echo date('m') ?>"
                                         data-timezone="<?php echo $tzd ?>">
                                        <div class="sc_timer_content tve_clearfix" style="margin-top: -10px;">
                                            <div class="tve_t_day tve_t_part">
                                                <div class="t-digits"></div>
                                                <div class="t-caption">Days</div>
                                            </div>
                                            <div class="tve_t_hour tve_t_part">
                                                <div class="t-digits"></div>
                                                <div class="t-caption">Hours</div>
                                            </div>
                                            <div class="tve_t_min tve_t_part">
                                                <div class="t-digits"></div>
                                                <div class="t-caption">Minutes</div>
                                            </div>
                                            <div class="tve_t_sec tve_t_part">
                                                <div class="t-digits"></div>
                                                <div class="t-caption">Seconds</div>
                                            </div>
                                            <div class="tve_t_text"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tve_clear"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_page_section separator_ps" data-tve-style="1">
        <div class="out" style="">
            <div class="in lightSec pddbg"
                 style="background-image: url('<?php echo TVE_LANDING_PAGE_TEMPLATE . "/css/images/whammy_sep.png" ?>');
                     box-shadow: none; box-sizing: border-box;max-width: 100vw;" data-width="1170" data-height="15">
                <div class="cck tve_clearfix">
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_content_container_shortcode">
        <div class="tve_clear"></div>
        <div class="tve_center tve_content_inner" style="width: 950px;min-width:50px; min-height: 2em;">
            <h2 class="tve_p_center" style="color: #333333; font-size: 52px;margin-top: 50px;">What You'll Learn on the
                Live Webinar:</h2>
            <ol class="thrv_wrapper" style="margin-top: 50px;">
                <li class="">Discover the truth about why almost all of the mainstream productivity advice is actually
                    detrimental and not meant to make you more productive in the first place.
                </li>
                <li class="">Why 'momentum' is one of the most important principles for having a productive day (and
                    what you can do to generate more momentum whenever you need it).
                </li>
                <li class="">Step-by-step strategies you can implement in your life for immediate results - backed up by
                    case studies and scientific evidence!
                </li>
            </ol>
        </div>
        <div class="tve_clear"></div>
    </div>

    <div class="thrv_wrapper thrv_button_shortcode tve_fullwidthBtn" data-tve-style="1">
        <div class="tve_btn tve_btn3 tve_nb tve_orange tve_bigBtn" style="margin-top: 0px;">
            <a class="tve_btnLink tve_evt_manager_listen" href=""
               data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                                <span class="tve_left tve_btn_im">
                                    <i></i>
                                    <span class="tve_btn_divider"></span>
                                </span>
                <span class="tve_btn_txt">Claim Your Spot On My Free Webinar Now! &GT;&GT;</span>
            </a>
        </div>
    </div>
    <div class="thrv_wrapper thrv_page_section tve_corner_ps" data-tve-style="1">
        <div class="out" style="background-color: #58a396">
            <div class="in lightSec">
                <div class="cck tve_clearfix">
                    <p class="tve_p_center"
                       style="color: #fff; font-size: 24px;line-height: 26px;margin-bottom: 0;margin-top: 10px;padding-bottom: 0;">
                        *
                        <font color="#275850">
                            <span class="italic_text">Important Note:</span>
                        </font>
                        due to technical restrictions the available places on this webinar
                    </p>

                    <p class="tve_p_center"
                       style="color: #fff; font-size: 24px;line-height: 26px;margin-bottom: 10px;padding-bottom: 0;">
                        <span class="underline_text"><span class="bold_text">are strictly limited!</span></span>
                        Reserve your spot now and sign in to the webinar early to ensure your participation.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="tve_lp_footer tve_empty_dropzone tve_drop_constraint" data-forbid=".thrv_page_section,.sc_page_section">
    <p class="tve_p_center" style="color: #333333;font-size: 17px;margin-top: 10px;">© {tcb_current_year} Webinar Landing Page. All
        rights Reserved | Disclaimer</p>
</div>