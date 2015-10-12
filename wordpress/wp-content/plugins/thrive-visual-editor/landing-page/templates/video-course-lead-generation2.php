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
                        with Online Video (Free Course)
                    </h1>

                    <div class="thrv_wrapper" style="margin-top: 0;margin-bottom: 220px;">
                        <hr class="tve_sep tve_sep1"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #e3e3e3">
            <div class="in darkSec">
                <div class="cck tve_clearfix">
                    <div class="thrv_wrapper thrv_content_container_shortcode">
                        <div class="tve_clear"></div>
                        <div class="tve_center tve_content_inner"
                             style="width: 860px;min-width:50px; min-height: 2em;margin-top: -200px;">
                            <div class="thrv_responsive_video thrv_wrapper" data-type="youtube" data-rel="0"
                                 data-controls="0" data-showinfo="0" data-url="//www.youtube.com/embed/XzwqYMzRWpo"
                                 data-embeded-url="//www.youtube.com/embed/XzwqYMzRWpo"
                                 style="margin: 0;">
                                <div style="display: block;" class="tve_responsive_video_container">
                                    <div class="video_overlay"></div>
                                    <iframe frameborder="0" allowfullscreen=""
                                            src="//www.youtube.com/embed/XzwqYMzRWpo?rel=0&amp;modestbranding=0&amp;controls=0&amp;showinfo=0&amp;autoplay=0&amp;fs=1&amp;wmode=transparent"></iframe>
                                </div>
                            </div>
                            <div class="thrv_wrapper thrv_button_shortcode tve_fullwidthBtn" data-tve-style="1">
                                <div class="tve_btn tve_btn1 tve_nb tve_red tve_bigBtn" style="margin-top: 5px;">
                                    <a class="tve_btnLink tve_evt_manager_listen" href=""
                                       data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                                        <span class="tve_left tve_btn_im">
                                            <i></i>
                                            <span class="tve_btn_divider"></span>
                                        </span>
                                        <span class="tve_btn_txt">START CRUSHING IT WITH ONLINE VIDEO</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="tve_clear"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_content_container_shortcode">
        <div class="tve_clear"></div>
        <div class="tve_center tve_content_inner" style="width: 860px;min-width:50px; min-height: 2em;">
            <h4 style="color: #333333; font-size: 26px;margin-top: 50px;margin-bottom: 20px;" class="tve_p_center">How
                this 4-part course will boost your online business:</h4>

            <div class="thrv_wrapper thrv_columns">
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
                                Learn about the one aspect of video that is extremely important, but overlooked by your
                                competitors.
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tve_colm tve_twc tve_lst">
                    <div class="thrv_wrapper thrv_bullets_shortcode">
                        <ul class="tve_ul tve_ul1 tve_red">
                            <li>
                                <span class="bold_text tve_custom_font_size"
                                      style="font-size: 22px;">The Recipe</span><br/>
                                Copy our exact step-by-step recipe for creating conversion boosting videos (for any purpose)!
                            </li>
                            <li>
                        <span class="bold_text tve_custom_font_size" style="font-size: 22px;">
                            The Traffic Advantage
                        </span><br/>
                                Discover the simple strategy for using each video you make to get more traffic from multiple sources.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="tve_clear"></div>
    </div>
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