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

<div class="tve_lp_header tve_empty_dropzone">
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1" style="">
        <div class="out" style="">
            <div class="in darkSec">
                <div class="cck clearfix">
                    <div style="width: 267px;" class="thrv_wrapper tve_image_caption aligncenter">
                        <span class="tve_image_frame">
                        <img class="tve_image"
                             src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/minimal_video_logo.png' ?>"
                             style="width: 267px">
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tve_lp_content tve_editor_main_content tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="pswr out">
            <div class="in darkSec pddbg" style="
                background-image: url('<?php echo TVE_LANDING_PAGE_TEMPLATE . "/css/images/minimal_video_offer_bg.jpg" ?>');
                box-shadow: none; box-sizing: border-box;max-width: 100vw;" data-width="1680" data-height="702">
                <div class="cck tve_clearfix">
                    <div class="thrv_wrapper thrv_content_container_shortcode">
                        <div class="tve_clear"></div>
                        <div class="tve_content_inner tve_center"
                             style="width: 900px; min-width: 50px; min-height: 2em;">
                            <h1 class="tve_p_center" style="font-size: 44px; margin-top: 60px; margin-bottom: 30px;">
                                Controversial Experiment Reveals How You Can <span class="bold_text">Double</span> Your
                                Site's Conversion Rate</h1>
                        </div>
                        <div class="tve_clear"></div>
                    </div>
                    <div class="thrv_wrapper thrv_content_container_shortcode">
                        <div class="tve_clear"></div>
                        <div class="tve_content_inner tve_center"
                             style="width: 750px; min-width: 50px; min-height: 2em;">
                            <div class="thrv_responsive_video thrv_wrapper" data-type="youtube" data-rel="0"
                                 data-controls="0" data-showinfo="0" data-url="//www.youtube.com/watch?v=O7bRXd5PmPg"
                                 data-embeded-url="//www.youtube.com/embed/O7bRXd5PmPg" style="margin-top: 0px;">
                                <div style="display: block;" class="tve_responsive_video_container">
                                    <div class="video_overlay"></div>
                                    <iframe frameborder="0" allowfullscreen=""
                                            src="//www.youtube.com/embed/O7bRXd5PmPg?rel=0&amp;modestbranding=0&amp;controls=0&amp;showinfo=0&amp;autoplay=0&amp;fs=1&amp;wmode=transparent"></iframe>
                                </div>
                            </div>
                            <div style="width: 477px;" class="thrv_wrapper tve_image_caption aligncenter">
                                <span class="tve_image_frame">
                                <img class="tve_image" alt=""
                                     src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/serene-video-shadow.png' ?>"
                                     style="width: 477px">
                                </span>
                            </div>
                        </div>
                        <div class="tve_clear"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1" style="padding-bottom: 0px; margin-bottom: 30px;">
        <div class="out" style="background-color: #f4f4f4">
            <div class="in darkSec">
                <div class="cck tve_clearfix">
                    <h2 class="tve_p_center tve_special"
                        style="font-size: 34px; margin-top: 40px; margin-bottom: 40px;;"><span>Click the Button Below to Get Started</span>
                    </h2>

                    <div class="thrv_wrapper thrv_button_shortcode tve_centerBtn" data-tve-style="1">
                        <div class="tve_btn tve_normalBtn tve_btn1 tve_black tve_nb" style="margin-bottom: 30px;">
                            <a class="tve_btnLink tve_evt_manager_listen" href=""
                               data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                                    <span class="tve_left tve_btn_im">
                                    <i></i>
                                    </span>
                                <span class="tve_btn_txt">YES, SIGN ME UP!</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="tve_lp_footer tve_empty_dropzone">
    <div class="thrv_wrapper thrv_page_section tve_no_drag" data-tve-style="1">
        <div class="out">
            <div class="in darkSec">
                <div class="cck tve_clearfix">
                    <div style="width: 52px;" class="thrv_wrapper tve_image_caption alignleft">
                        <span class="tve_image_frame">
                        <img class="tve_image"
                             src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/logo_minimal_video2.png' ?>"
                             style="width: 52px">
                        </span>
                    </div>
                    <p class="float-left tve_p_left"
                       style="margin: 0; padding: 0; color: #a2a2a2; font-size: 18px; font-weight: 300; line-height: 52px;">
                        2014 © 2014. All rights reserved</p>

                    <p class="float-right tve_p_right"
                       style="margin: 0; padding: 0; color: #a2a2a2; font-size: 18px; font-weight: 300; line-height: 52px;">
                        <a href="#">Disclaimer</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
