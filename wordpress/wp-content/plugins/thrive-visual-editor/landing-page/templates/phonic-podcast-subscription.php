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
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
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
                                            Simply <span class="bold_text"><font color="#fdd657">the Best</font></span> Podcast About
                                            <span class="bold_text">Conversion Optimization</span>
                                        </h1>
                                        <p style="color: #fff; font-size: 20px;margin-top: 0;margin-bottom: 0;" class="podcast_over_text">
                                            <span style="background: #1399bc;">
                                                Listen to our latest episode here:
                                            </span>
                                        </p>
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
    <div class="thrv_wrapper thrv_icon aligncenter" style="margin-top: -25px;">
                        <span style="color: #fff;font-size: 25px;"
                              class="tve_blue tve_sc_icon phonic-icon-arrow"
                              data-tve-icon="phonic-icon-arrow"></span>
    </div>
    <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="6">
        <div class="tve_cb tve_cb6 tve_white" style="margin-top: 40px;margin-bottom: 40px;">
            <div class="tve_cb_cnt">
                <div class="thrv_wrapper tve_draggable thrv_custom_html_shortcode edit_mode"
                     style="margin-top: 0;margin-bottom: 0;">
                    <iframe width="100%" height="166" scrolling="no" frameborder="no"
                            src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/215492675&amp;color=ff5500&amp;auto_play=false&amp;hide_related=false&amp;show_comments=true&amp;show_user=true&amp;show_reposts=false"></iframe>
                    <div class="tve_iframe_cover"></div>
                </div>
            </div>
        </div>
    </div>
    <h3 class="tve_p_center rft" style="color: #1399bc; font-size: 30px;margin-top: 0;margin-bottom: 30px;">
        SUBSCRIBE & REVIEW:
    </h3>
    <p class="tve_p_center" style="color: #333; font-size: 20px;margin-top: 0;margin-bottom: 40px;">
        <span class="bold_text">Make sure you never miss an episode</span> of [Show Name] by subscribing via your favorite podcast app.
        Plus, scroll down to get our <span class="bold_text">10-step conversion boosting guide</span> for free!
    </p>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #1399bc;">
            <div class="in lightSec">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_content_container_shortcode">
                        <div class="tve_clear"></div>
                        <div class="tve_center tve_content_inner" style="width: 931px;min-width:50px; min-height: 2em;">
                            <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="symbol">
                                <div class="tve_cb tve_cb_symbol tve_white" style="margin-top: 30px;">
                                    <div class="thrv_wrapper thrv_icon thrv_cb_text aligncenter tve_no_drag tve_no_icons" style="font-size: 40px;">
                                        <span class="tve_sc_text tve_sc_icon tve_blue">1.</span>
                                    </div>
                                    <div class="tve_cb_cnt">
                                        <div style="width: 206px;margin-top: 2px;margin-bottom: 0;" class="thrv_wrapper tve_image_caption alignleft">
                                                    <span class="tve_image_frame">
                                                        <img class="tve_image"
                                                             src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/phonic_itunes_btn_big.png' ?>"
                                                             style="width: 206px;"/>
                                                    </span>
                                        </div>
                                        <div class="thrv_wrapper thrv_button_shortcode tve_leftBtn" data-tve-style="1">
                                            <div class="tve_btn tve_btn1 tve_nb tve_orange tve_smallBtn">
                                                <a href="" class="tve_btnLink">
                                                    <span class="tve_left tve_btn_im">
                                                        <i></i>
                                                        <span class="tve_btn_divider"></span>
                                                    </span>
                                                    <span class="tve_btn_txt">CLICK HERE TO LEAVE A REVIEW</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="symbol">
                                <div class="tve_cb tve_cb_symbol tve_white">
                                    <div class="thrv_wrapper thrv_icon thrv_cb_text aligncenter tve_no_drag tve_no_icons" style="font-size: 40px;">
                                        <span class="tve_sc_text tve_sc_icon tve_blue">2.</span>
                                    </div>
                                    <div class="tve_cb_cnt">
                                        <div style="width: 206px;margin-top: 2px;margin-bottom: 0;" class="thrv_wrapper tve_image_caption alignleft">
                                                    <span class="tve_image_frame">
                                                        <img class="tve_image"
                                                             src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/phonic_stitcher_btn_big.png' ?>"
                                                             style="width: 206px;"/>
                                                    </span>
                                        </div>
                                        <div class="thrv_wrapper thrv_button_shortcode tve_leftBtn" data-tve-style="1">
                                            <div class="tve_btn tve_btn1 tve_nb tve_orange tve_smallBtn">
                                                <a href="" class="tve_btnLink">
                                                    <span class="tve_left tve_btn_im">
                                                        <i></i>
                                                        <span class="tve_btn_divider"></span>
                                                    </span>
                                                    <span class="tve_btn_txt">CLICK HERE TO LEAVE A REVIEW</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="symbol">
                                <div class="tve_cb tve_cb_symbol tve_white" style="margin-bottom: 15px;">
                                    <div class="thrv_wrapper thrv_icon thrv_cb_text aligncenter tve_no_drag tve_no_icons" style="font-size: 40px;">
                                        <span class="tve_sc_text tve_sc_icon tve_blue">3.</span>
                                    </div>
                                    <div class="tve_cb_cnt">
                                        <div style="width: 267px;margin-top: 3px;margin-bottom: 0;margin-right: 0;" class="thrv_wrapper tve_image_caption alignleft">
                                                    <span class="tve_image_frame">
                                                        <img class="tve_image"
                                                             src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/phonic_soundcloud_btn_big.png' ?>"
                                                             style="width: 267px;"/>
                                                    </span>
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
    <div class="thrv_wrapper thrv_icon aligncenter" style="margin-top: -25px;">
                        <span style="color: #fff;font-size: 25px;"
                              class="tve_blue tve_sc_icon phonic-icon-arrow"
                              data-tve-icon="phonic-icon-arrow"></span>
    </div>
    <div class="thrv_wrapper thrv_content_container_shortcode">
        <div class="tve_clear"></div>
        <div class="tve_center tve_content_inner" style="width: 931px;min-width:50px; min-height: 2em;">
            <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-top: 50px;">
                <div class="tve_colm tve_oth">
                    <div style="width: 278px;margin-top: 0;margin-bottom: 0;" class="thrv_wrapper tve_image_caption alignleft">
                        <span class="tve_image_frame">
                            <img class="tve_image"
                                 src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/phonic_podcast_icon.png' ?>"
                                 style="width: 278px;"/>
                        </span>
                    </div>
                </div>
                <div class="tve_colm tve_tth tve_lst">
                    <h3 style="color: #292929; font-size: 36px;margin-top: 0;margin-bottom: 20px;" class="rft">
                        Don't use any of the apps above?
                    </h3>
                    <p style="color: #333; font-size: 20px;margin-top: 0;margin-bottom: 30px;">
                        Subscribe Here to get every new episode sent straight
                        to your inbox!
                    </p>
                    <div class="thrv_wrapper thrv_button_shortcode" data-tve-style="1">
                        <div class="tve_btn tve_btn1 tve_nb tve_blue tve_smallBtn">
                            <a class="tve_btnLink tve_evt_manager_listen" href="" data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                                                    <span class="tve_left tve_btn_im">
                                                        <i></i>
                                                        <span class="tve_btn_divider"></span>
                                                    </span>
                                <span class="tve_btn_txt">SUBSCRIBE NOW</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tve_clear"></div>
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