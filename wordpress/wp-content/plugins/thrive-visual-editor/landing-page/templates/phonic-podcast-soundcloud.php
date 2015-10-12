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
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1" id="phonic-page-section">
        <div class="out" style="background-color: #1399bc;">
            <div class="in lightSec">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="5">
                        <div class="tve_cb tve_cb5 tve_white">
                            <div class="tve_cb_cnt">
                                <div class="thrv_wrapper thrv_content_container_shortcode">
                                    <div class="tve_clear"></div>
                                    <div class="tve_center tve_content_inner"
                                         style="width: 955px;min-width:50px; min-height: 2em;">
                                        <h1 style="color: #fff; font-size: 60px;margin-top: 0;margin-bottom: 0;"
                                            class="tve_p_center rft">
                                            Get Our <span class="bold_text"><font color="#fdd657">Bonus Episode</font></span> to Discover
                                            Our Best Kept <span class="bold_text">Conversion Secrets!</span>
                                        </h1>
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
    <div class="thrv_wrapper thrv_content_container_shortcode">
        <div class="tve_clear"></div>
        <div class="tve_center tve_content_inner"
             style="width: 850px;min-width:50px; min-height: 2em;margin-top: -100px;margin-bottom: 30px;">
            <div class="thrv_responsive_video thrv_wrapper" data-type="youtube" data-rel="0"
                 data-controls="0" data-showinfo="0" data-url="//www.youtube.com/embed/J7LqWhHnxOY"
                 data-embeded-url="//www.youtube.com/embed/J7LqWhHnxOY"
                 style="margin-top: 0;margin-bottom: 50px;">
                <div style="display: block;" class="tve_responsive_video_container">
                    <div class="video_overlay"></div>
                    <iframe frameborder="0" allowfullscreen=""
                            src="//www.youtube.com/embed/J7LqWhHnxOY?rel=0&amp;modestbranding=0&amp;controls=0&amp;showinfo=0&amp;autoplay=0&amp;fs=1&amp;wmode=transparent"></iframe>
                </div>
            </div>
            <h5 class="tve_p_center" style="color: #666; font-size: 26px;margin-top: 0;margin-bottom: 10px;">
                <span class="bold_text">Follow the steps below</span>  to never miss an episode and get our
                <span class="bold_text"><font color="#ff3300">10-step conversion guide for free!</font></span>
            </h5>
        </div>
        <div class="tve_clear"></div>
    </div>

    <div class="thrv_wrapper thrv_icon aligncenter" style="margin-bottom: -25px;">
                        <span style="color: #fff;font-size: 25px;"
                              class="tve_blue tve_sc_icon phonic-icon-arrow"
                              data-tve-icon="phonic-icon-arrow"></span>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #1399bc;">
            <div class="in darkSec">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_content_container_shortcode">
                        <div class="tve_clear"></div>
                        <div class="tve_center tve_content_inner" style="width: 931px;min-width:50px; min-height: 2em;">
                            <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="symbol">
                                <div class="tve_cb tve_cb_symbol tve_white">
                                    <div class="thrv_wrapper thrv_icon thrv_cb_text aligncenter tve_no_drag tve_no_icons" style="font-size: 40px;">
                                        <span class="tve_sc_text tve_sc_icon tve_blue">Step 1.</span>
                                    </div>
                                    <div class="tve_cb_cnt">
                                        <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-top: 0;margin-bottom: 0;">
                                            <div class="tve_colm tve_twc">
                                                <p style="color: #666666; font-size: 24px;margin-top: 0;margin-bottom: 0;">
                                                    Follow us on SoundCloud
                                                    to get future episodes!
                                                </p>
                                            </div>
                                            <div class="tve_colm tve_twc tve_lst">
                                                <div style="width: 267px;margin: 0;" class="thrv_wrapper tve_image_caption alignright">
                                                    <span class="tve_image_frame">
                                                        <img class="tve_image"
                                                             src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/phonic_soundcloud_btn_big.png' ?>"
                                                             style="width: 267px;"/>
                                                    </span>
                                                </div>
                                                <div style="width: 56px;margin: 10px 16px 0 0;" class="thrv_wrapper tve_image_caption alignright">
                                                    <span class="tve_image_frame">
                                                        <img class="tve_image phonic-arrow-pointer"
                                                             src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/phonic_arrows.png' ?>"
                                                             style="width: 70px;"/>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="width: 675px;margin-top: 20px;" class="thrv_wrapper tve_image_caption aligncenter">
                                            <span class="tve_image_frame">
                                                <img class="tve_image"
                                                     src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/phonic_soundcloud_image.png' ?>"
                                                     style="width: 675px;"/>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="symbol">
                                <div class="tve_cb tve_cb_symbol tve_white">
                                    <div class="thrv_wrapper thrv_icon thrv_cb_text aligncenter tve_no_drag tve_no_icons" style="font-size: 40px;">
                                        <span class="tve_sc_text tve_sc_icon tve_blue">Step 2.</span>
                                    </div>
                                    <div class="tve_cb_cnt">
                                        <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-top: 0;margin-bottom: 0;">
                                            <div class="tve_colm tve_foc tve_df tve_ofo ">
                                                <div style="width: 110px;" class="thrv_wrapper tve_image_caption aligncenter">
                                                    <span class="tve_image_frame">
                                                        <img class="tve_image"
                                                             src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/phonic_small_book.png' ?>"
                                                             style="width: 110px;"/>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="tve_colm tve_tfo tve_df tve_lst">
                                                <h4 style="color: #666; font-size: 28px;margin-top: 20px;margin-bottom: 20px;">
                                                    Get the <span class="bold_text">10-step</span> conversion guide.
                                                </h4>
                                                <div class="thrv_wrapper thrv_button_shortcode" data-tve-style="1">
                                                    <div class="tve_btn tve_btn5 tve_nb tve_orange tve_smallBtn" style="margin-top: -2px;margin-bottom: -2px;">
                                                        <a class="tve_btnLink tve_evt_manager_listen" href="" data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                                                            <span class="tve_left tve_btn_im">
                                                                <i class="tve_sc_icon phonic-icon-arrowright" data-tve-icon="phonic-icon-arrowright" style="background-image: none; color: rgba(0, 0, 0 , .34); font-size: 50px;"></i>
                                                            </span>
                                                            <span class="tve_btn_divider"></span>
                                                            <span class="tve_btn_txt">GET INSTANT ACCESS</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
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
</div>

<div class="tve_lp_footer tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #111111;">
            <div class="in darkSec">
                <div class="cck clearfix">
                    <p style="font-size: 16px;color: #666666;margin-top: 0;margin-bottom: 0;" class="tve_p_center">
                        &copy {tcb_current_year} Thrive  Landing Pages |  <a href="#">Disclaimer</a>  |  <a href="#">Privacy Policy</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>