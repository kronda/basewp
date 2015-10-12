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
                        with Online Video (FREE Course)
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
                                style="color: #333; font-size: 20px;margin-top: 0;margin-bottom: 10px;">Click the button below and
                                <font color="#2d3445"><span class="underline_text bold_text">get started for FREE</span> </font>
                                right away!
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