<?php
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
<div class="tve_lp_header tve_empty_dropzone"></div>
<div class="tve_lp_content tve_editor_main_content tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1" style="">
        <div class="out" style="">
            <div class="in lightSec pddbg"
                 style="background-image: url('<?php echo TVE_LANDING_PAGE_TEMPLATE . "/css/images/video-course-ps-bg.jpg" ?>');
                     box-shadow: none; box-sizing: border-box;max-width: 100vw;" data-width="1600" data-height="526">
                <div class="cck tve_clearfix">
                    <div style="width: 158px;margin-top: 0;margin-bottom: 0;"
                         class="thrv_wrapper tve_image_caption aligncenter">
                            <span class="tve_image_frame">
                                <a href="">
                                    <img class="tve_image"
                                         src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/videou-course-logo.png' ?>"
                                         style="width: 158px"/>
                                </a>
                            </span>
                    </div>
                    <div class="thrv_wrapper" style="margin-top: 0;margin-bottom: 0;">
                        <hr class="tve_sep tve_sep1"/>
                    </div>
                    <h1 style="color: #fff; font-size: 54px;margin-top: 0;margin-bottom: 0;"
                        class="tve_p_center rft">
                        How to <span class="bold_text">Attract Customers</span> and <span
                            class="bold_text">Make Money</span>
                        with Online Video (4-Part Course)
                    </h1>

                    <div class="thrv_wrapper" style="margin-top: 0;margin-bottom: 0;">
                        <hr class="tve_sep tve_sep1"/>
                    </div>
                    <div class="thrv_wrapper thrv_columns" style="margin-top: 40px;margin-bottom: 0;">
                        <div class="tve_colm tve_tth"><p>&nbsp;</p></div>
                        <div class="tve_colm tve_oth tve_lst">
                            <h4 style="color: #fff; font-size: 25px;margin-top: 0;margin-bottom: 10px;">
                                <span class="bold_text">Crush it With Online Video!</span>
                            </h4>

                            <p style="color: #fff; font-size: 18px;margin-top: 0;margin-bottom: 20px;">
                                Video is the most captivating and most effective medium for communicating online. Now
                                it's time for you to benefit from it.
                            </p>

                            <div style="margin-bottom: -42px;" class="thrv_wrapper thrv_icon aligncenter">
                                        <span style="color: #2d3445;font-size: 20px;"
                                              class="tve_sc_icon video-course-icon-arrow tve_white"
                                              data-tve-icon="video-course-icon-arrow"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #e3e3e3;">
            <div class="in darkSec">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_columns">
                        <div class="tve_colm tve_tth">
                            <div class="thrv_responsive_video thrv_wrapper" data-type="youtube" data-rel="0"
                                 data-controls="0" data-showinfo="0" data-url="//www.youtube.com/embed/XzwqYMzRWpo"
                                 data-embeded-url="//www.youtube.com/embed/XzwqYMzRWpo"
                                 style="margin-top: -215px;">
                                <div style="display: block;" class="tve_responsive_video_container">
                                    <div class="video_overlay"></div>
                                    <iframe frameborder="0" allowfullscreen=""
                                            src="//www.youtube.com/embed/XzwqYMzRWpo?rel=0&amp;modestbranding=0&amp;controls=0&amp;showinfo=0&amp;autoplay=0&amp;fs=1&amp;wmode=transparent"></iframe>
                                </div>
                            </div>
                        </div>
                        <div class="tve_colm tve_oth tve_lst">
                            <h6 class="center_heading"
                                style="color: #333; font-size: 20px;margin-top: 0;margin-bottom: 10px;">Click the button
                                below and
                                <font color="#2d3445"><span class="underline_text bold_text">get started</span> </font>
                                for FREE right away!
                            </h6>

                            <div class="thrv_wrapper thrv_button_shortcode center_button" data-tve-style="1">
                                <div class="tve_btn tve_btn1 tve_nb tve_red tve_bigBtn">
                                    <a class="tve_btnLink tve_evt_manager_listen" href=""
                                       data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                                        <span class="tve_left tve_btn_im">
                                            <i></i>
                                            <span class="tve_btn_divider"></span>
                                        </span>
                                        <span class="tve_btn_txt">START THE FREE COURSE &raquo;</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #c5c6c9;">
            <div class="in darkSec">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-bottom: 0;margin-top: 0;">
                        <div class="tve_colm tve_foc">
                            <div class="thrv_wrapper thrv_contentbox_shortcode center_heading" data-tve-style="6">
                                <div class="tve_cb tve_cb6 tve_white">
                                    <div class="tve_cb_cnt">
                                        <div class="thrv_wrapper thrv_columns tve_clearfix"
                                             style="margin-top: 10px;margin-bottom: 0;">
                                            <div class="tve_colm tve_oth">
                                                <div class="thrv_wrapper thrv_icon aligncenter video-course-play-icon">
                                                    <span style="color: #fff;font-size: 13px;"
                                                          class="tve_sc_icon video-course-icon-play tve_red"
                                                          data-tve-icon="video-course-icon-play"></span>
                                                </div>
                                            </div>
                                            <div class="tve_colm tve_tth tve_lst">
                                                <p style="color: #333333;font-size: 24px;margin-top: 0;margin-bottom: 10px;">
                                                    <span class="bold_text">VIDEO #1</span>
                                                </p>
                                            </div>
                                        </div>
                                        <p style="color: #666666; font-size: 18px;margin-top: 0;margin-bottom: 33px;"
                                           class="tve_p_center">Building a Sales Page</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tve_colm tve_foc">
                            <div class="thrv_wrapper thrv_contentbox_shortcode center_heading" data-tve-style="6">
                                <div class="tve_cb tve_cb6 tve_white">
                                    <div class="tve_cb_cnt">
                                        <div class="thrv_wrapper thrv_columns tve_clearfix"
                                             style="margin-top: 10px;margin-bottom: 0;">
                                            <div class="tve_colm tve_oth">
                                                <div class="thrv_wrapper thrv_icon aligncenter video-course-play-icon">
                                                    <span style="color: #fff;font-size: 13px;"
                                                          class="tve_sc_icon video-course-icon-play tve_red"
                                                          data-tve-icon="video-course-icon-play"></span>
                                                </div>
                                            </div>
                                            <div class="tve_colm tve_tth tve_lst">
                                                <p style="color: #333333;font-size: 24px;margin-top: 0;margin-bottom: 10px;">
                                                    <span class="bold_text">VIDEO #2</span>
                                                </p>
                                            </div>
                                        </div>
                                        <p style="color: #666666; font-size: 18px;margin-top: 0;margin-bottom: 10px;"
                                           class="tve_p_center">How the Rapid System Works</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tve_colm tve_foc">
                            <div class="thrv_wrapper thrv_contentbox_shortcode center_heading" data-tve-style="6">
                                <div class="tve_cb tve_cb6 tve_white">
                                    <div class="tve_cb_cnt">
                                        <div class="thrv_wrapper thrv_columns tve_clearfix"
                                             style="margin-top: 10px;margin-bottom: 0;">
                                            <div class="tve_colm tve_oth">
                                                <div class="thrv_wrapper thrv_icon aligncenter video-course-play-icon">
                                                    <span style="color: #fff;font-size: 13px;"
                                                          class="tve_sc_icon video-course-icon-play tve_red"
                                                          data-tve-icon="video-course-icon-play"></span>
                                                </div>
                                            </div>
                                            <div class="tve_colm tve_tth tve_lst">
                                                <p style="color: #333333;font-size: 24px;margin-top: 0;margin-bottom: 10px;">
                                                    <span class="bold_text">VIDEO #3</span>
                                                </p>
                                            </div>
                                        </div>
                                        <p style="color: #666666; font-size: 18px;margin-top: 0;margin-bottom: 33px;"
                                           class="tve_p_center">Building an Opt-In Page</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tve_colm tve_foc tve_lst">
                            <div class="thrv_wrapper thrv_contentbox_shortcode center_heading" data-tve-style="6">
                                <div class="tve_cb tve_cb6 tve_white">
                                    <div class="tve_cb_cnt">
                                        <div class="thrv_wrapper thrv_columns tve_clearfix"
                                             style="margin-top: 10px;margin-bottom: 0;">
                                            <div class="tve_colm tve_oth">
                                                <div class="thrv_wrapper thrv_icon aligncenter video-course-play-icon">
                                                    <span style="color: #fff;font-size: 13px;"
                                                          class="tve_sc_icon video-course-icon-play tve_red"
                                                          data-tve-icon="video-course-icon-play"></span>
                                                </div>
                                            </div>
                                            <div class="tve_colm tve_tth tve_lst">
                                                <p style="color: #333333;font-size: 24px;margin-top: 0;margin-bottom: 10px;">
                                                    <span class="bold_text">VIDEO #4</span>
                                                </p>
                                            </div>
                                        </div>
                                        <p style="color: #666666; font-size: 18px;margin-top: 0;margin-bottom: 33px;"
                                           class="tve_p_center">5 Important Tips</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <h2 class="tve_p_center rft" style="color: #333; font-size: 42px;margin-top: 80px;margin-bottom: 60px;">The Fastest
        Way for You to Profit With Videos!</h2>

    <p class="tve_p_center" style="color: #666; font-size: 18px;margin-top: 0;margin-bottom: 40px;">
        Many people are intimidated by the idea of creating marketing videos... and maybe you feel the same way. Isn't
        it too complicated, too technical, too time consuming? It's time to realize that this represents a <span class="bold_text">massive
        advantage for you</span>, if you learn the simple techniques for creating awesome online videos and gain an edge over
        your competition.
    </p>
    <h4 style="color: #333333; font-size: 26px;margin-top: 0;margin-bottom: 20px;" class="tve_p_center">What You'll Discover in this 4-Part Course:</h4>

    <div class="thrv_wrapper thrv_columns" style="margin-bottom: 0;">
        <div class="tve_colm tve_twc">
            <div class="thrv_wrapper thrv_bullets_shortcode">
                <ul class="tve_ul tve_ul1 tve_red">
                    <li>
                        <span class="bold_text tve_custom_font_size"
                              style="font-size: 22px;">The Best Video Tools</span><br/>
                        Learn how to create 100% professional looking marketing videos, using free tools only.
                    </li>
                    <li>
                        <span class="bold_text tve_custom_font_size" style="font-size: 22px;">
                            The Forgotten Key
                        </span><br/>
                        Learn about the one aspect of video that is extremely important, but overlooked by your competitors.
                    </li>
                </ul>
            </div>
        </div>
        <div class="tve_colm tve_twc tve_lst">
            <div class="thrv_wrapper thrv_bullets_shortcode">
                <ul class="tve_ul tve_ul1 tve_red">
                    <li>
                        <span class="bold_text tve_custom_font_size" style="font-size: 22px;">The Recipe </span><br/>
                        Copy our exact recipe for creating conversion boosting videos for any purpose!
                    </li>
                    <li>
                        <span class="bold_text tve_custom_font_size" style="font-size: 22px;">
                            The Traffic Advantage
                        </span><br/>
                        See how you can use each video you make to get traffic from multiple sources.
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper" style="margin-bottom: 50px;">
        <hr class="tve_sep tve_sep2"/>
    </div>
    <h2 style="color: #333; font-size: 42px;margin-top: 0;margin-bottom: 50px;" class="tve_p_center rft">What Others Are Saying:</h2>

    <div style="margin-bottom: -42px;" class="thrv_wrapper thrv_icon aligncenter">
                                        <span style="color: #fff;font-size: 100px;"
                                              class="tve_sc_icon video-course-icon-quote tve_white"
                                              data-tve-icon="video-course-icon-quote"></span>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #f1f1f1">
            <div class="in darkSec">
                <div class="cck tve_clearfix">
                    <div class="thrv_wrapper thrv_columns tve_clearfix">
                        <div class="tve_colm tve_oth">
                            <div class="thrv_wrapper thrv_testimonial_shortcode" data-tve-style="2">
                                <div class="tve_ts tve_ts2 tve_red">
                                    <div class="tve_ts_o">
                                        <div class="tve_ts_imc">
                                            <img class="tve_image"
                                                 src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/shane_melaugh.jpg' ?>"
                                                 alt=""/>
                                        </div>
                                            <span>
                                                <b>Shane Melaugh</b>
                                                UI/UX Designer
                                            </span>
                                    </div>
                                    <div class="tve_ts_t">
                                        <div class="tve_ts_cn">
                                            <span class="tve_ts_ql"></span>

                                            <p class="tve_p_center">
                                                "Duis sed odio sit amet nibh vulputate cursus a sit amet mauris. Morbi
                                                accumsan ipsum velit."
                                            </p>
                                            <span class="tve_ts_qr"></span>
                                        </div>
                                    </div>
                                    <div class="tve_clear"></div>
                                </div>
                            </div>
                        </div>
                        <div class="tve_colm tve_oth">
                            <div class="thrv_wrapper thrv_testimonial_shortcode" data-tve-style="2">
                                <div class="tve_ts tve_ts2 tve_red">
                                    <div class="tve_ts_o">
                                        <div class="tve_ts_imc">
                                            <img class="tve_image"
                                                 src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/f3_small.jpg' ?>"
                                                 alt=""/>
                                        </div>
                                            <span>
                                                <b>Jane Doe</b>
                                                UI/UX Designer
                                            </span>
                                    </div>
                                    <div class="tve_ts_t">
                                        <div class="tve_ts_cn">
                                            <span class="tve_ts_ql"></span>

                                            <p class="tve_p_center">
                                                "Duis sed odio sit amet nibh vulputate cursus a sit amet mauris. Morbi
                                                accumsan ipsum velit."
                                            </p>
                                            <span class="tve_ts_qr"></span>
                                        </div>
                                    </div>
                                    <div class="tve_clear"></div>
                                </div>
                            </div>
                        </div>
                        <div class="tve_colm tve_thc tve_lst">
                            <div class="thrv_wrapper thrv_testimonial_shortcode" data-tve-style="2">
                                <div class="tve_ts tve_ts2 tve_red">
                                    <div class="tve_ts_o">
                                        <div class="tve_ts_imc">
                                            <img class="tve_image"
                                                 src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/m2_small.jpg' ?>"
                                                 alt=""/>
                                        </div>
                                            <span>
                                                <b>John Doe</b>
                                                UI/UX Designer
                                            </span>
                                    </div>
                                    <div class="tve_ts_t">
                                        <div class="tve_ts_cn">
                                            <span class="tve_ts_ql"></span>

                                            <p class="tve_p_center">
                                                "Duis sed odio sit amet nibh vulputate cursus a sit amet mauris. Morbi
                                                accumsan ipsum velit."
                                            </p>
                                            <span class="tve_ts_qr"></span>
                                        </div>
                                    </div>
                                    <div class="tve_clear"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1" style="">
        <div class="out" style="">
            <div class="in lightSec pddbg"
                 style="background-image: url('<?php echo TVE_LANDING_PAGE_TEMPLATE . "/css/images/video-course-triangle-pattern.jpg" ?>');
                     box-shadow: none; box-sizing: border-box;max-width: 100vw;" data-width="1600" data-height="526">
                <div class="cck tve_clearfix">
                    <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="5">
                        <div class="tve_cb tve_cb5 tve_black">
                            <div class="tve_cb_cnt">
                                <h2 style="color: #fff; font-size: 40px;margin-top: 20px;margin-bottom: 30px;"
                                    class="rft tve_p_center">Ready to Get Started? Click the for Lesson 1:</h2>

                                <div class="thrv_wrapper thrv_button_shortcode tve_centerBtn" data-tve-style="1">
                                    <div class="tve_btn tve_btn1 tve_nb tve_red tve_bigBtn">
                                        <a class="tve_btnLink tve_evt_manager_listen" href=""
                                           data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                                        <span class="tve_left tve_btn_im">
                                            <i></i>
                                            <span class="tve_btn_divider"></span>
                                        </span>
                                            <span class="tve_btn_txt">START WITH LESSON 1 &raquo;</span>
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
    <!--aici-->
</div>

<div class="tve_lp_footer tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #333333;">
            <div class="in darkSec">
                <div class="cck clearfix">
                    <p style="font-size: 16px;color: #999;margin-top: 0;margin-bottom: 0;" class="tve_p_center">{tcb_current_year} Thrive Landing Pages. All rights Reserved | <a href=""> Disclaimer</a></p>
                </div>
            </div>
        </div>
    </div>
</div>