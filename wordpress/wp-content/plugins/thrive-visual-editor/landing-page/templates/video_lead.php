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
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #FFFFFF">
            <div class="in darkSec">
                <div class="cck clearfix tve_content_width">
                    <div style="width: 132px;" class="thrv_wrapper tve_image_caption aligncenter">
                        <span class="tve_image_frame">
                            <a href="">
                                <img class="tve_image" src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/video-lead-logo.png' ?>" style="width: 132px"/>
                            </a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tve_lp_content tve_editor_main_content tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_content_container_shortcode">
        <div class="tve_clear"></div>
        <div class="tve_center tve_content_inner" style="width: 900px;min-width:50px; min-height: 2em;margin-top:30px;">
            <h1 class="tve_p_center" style="color: #ffffff;">
                "Discover the <font color="#ffd02c"><span class="bold_text">Easiest Way</span></font> to Create Flexible,
                High-Conversion Landing Pages... and
                <font color="#ffd02c"><span class="bold_text">Get More Leads</span></font> than Ever!"
            </h1>
        </div>
        <div class="tve_clear"></div>
    </div>

    <div class="thrv_wrapper thrv_columns tve_clearfix">
        <div class="tve_colm tve_tth">
            <div class="thrv_responsive_video thrv_wrapper" data-type="youtube" data-rel="0" data-controls="0" data-showinfo="0" data-url="https://www.youtube.com/watch?v=3ZIpF609vIM" data-embeded-url="//www.youtube.com/embed/3ZIpF609vIM">
                <div style="display: block;" class="tve_responsive_video_container">
                    <div class="video_overlay"></div>
                    <iframe frameborder="0" allowfullscreen="" src="//www.youtube.com/embed/3ZIpF609vIM?rel=0&amp;modestbranding=0&amp;controls=0&amp;showinfo=0&amp;autoplay=0&amp;fs=1&wmode=transparent"></iframe>
                </div>
            </div>
            <p style="color: #ffffff" class="tve_p_center"><span class="italic_text">Watch the video to see how this report will change the way you build landing pages.</span></p>
        </div>
        <div class="tve_colm tve_oth tve_lst">
            <h3 class="tve_p_center bold_text" style="color: #ffffff;margin-bottom:25px;">Download Your Free Report:</h3>

            <div style="width: 228px;" class="thrv_wrapper tve_image_caption aligncenter">
                <span class="tve_image_frame">
                    <img class="tve_image" src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/video-lead-product.png' ?>"
                         style="width: 228px;"/>
                </span>
            </div>

            <div data-tve-style="1" class="thrv_wrapper thrv_button_shortcode tve_centerBtn tve_fullwidthBtn" style="margin-top: 20px">
                <div class="tve_btn tve_btn7 tve_orange tve_normalBtn tve_nb">
                    <a class="tve_btnLink tve_evt_manager_listen" href="" data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                        <span class="tve_left tve_btn_im">
                            <i></i>
                        </span>
                        <span class="tve_btn_txt">Get the Free Report &raquo;</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tve_lp_footer tve_empty_dropzone">
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out">
            <div class="in darkSec">
                <div class="cck clearfix">
                    <p class="tve_p_center" style="margin: 0; padding: 0; color: #ffffff; font-size: 16px">&copy; {tcb_current_year} by ACME Inc.</p>
                    <p class="tve_p_center" style="margin: 0; padding: 0; color: #ffffff; font-size: 16px">
                        <a href="#"><span class="underline_text">Disclaimer</span></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>