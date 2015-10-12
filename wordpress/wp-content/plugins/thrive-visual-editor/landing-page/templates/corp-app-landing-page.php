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
<div class="tve_lp_header tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1" style="">
        <div class="out ">
            <div class="in lightSec pddbg"
                 style="background-color: #121313;">
                <div class="cck clearfix">
                    <div style="width: 200px;margin-top: 0;margin-bottom: 0;"
                         class="thrv_wrapper tve_image_caption aligncenter">
                        <span class="tve_image_frame">
                            <a href="">
                                <img class="tve_image"
                                     src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/corp_logo.png' ?>"
                                     style="width: 200px;"/>
                            </a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="tve_lp_content tve_editor_main_content tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1" style="">
        <div class="out pswr">
            <div class="in lightSec pddbg"
                 style="background-image: url('<?php echo TVE_LANDING_PAGE_TEMPLATE . "/css/images/corp_pagesection_bg.jpg" ?>');
                     box-shadow: none; box-sizing: border-box;max-width: 100vw;" data-width="1980" data-height="592">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_columns">
                        <div class="tve_colm tve_twc">
                            <h1 class="rft" style="color: #fff; font-size: 54px; margin-top: 0;margin-bottom: 30px;">Get <span class="bold_text">Early Access</span> to Our New App!</h1>
                            <h4 style="color: #fff; font-size: 20px; margin-top: 0;margin-bottom: 40px;">Beta access available for iOS and Android platforms. Access keys are limites, so sign up
                                today to reserve your spot!</h4>

                            <div class="thrv_wrapper thrv_button_shortcode tve_fullwidthBtn" data-tve-style="1">
                                <div class="tve_btn tve_btn1 tve_nb tve_teal tve_normalBtn">
                                    <a class="tve_btnLink tve_evt_manager_listen" href="" data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                                        <span class="tve_left tve_btn_im">
                                            <i></i>
                                            <span class="tve_btn_divider"></span>
                                        </span>
                                        <span class="tve_btn_txt">REQUEST EARLY ACCESS</span>
                                    </a>
                                </div>
                            </div>
                            <div class="thrv_wrapper thrv_button_shortcode tve_fullwidthBtn" data-tve-style="1">
                                <div class="tve_btn tve_btn1 tve_nb tve_white tve_normalBtn">
                                    <a href="" class="tve_btnLink">
                                        <span class="tve_left tve_btn_im">
                                            <i></i>
                                            <span class="tve_btn_divider"></span>
                                        </span>
                                        <span class="tve_btn_txt">LEARN MORE</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="tve_colm tve_twc tve_lst">
                            <div style="width: 558px;margin-top: 70px;margin-bottom: -45px;"
                                 class="thrv_wrapper tve_image_caption aligncenter">
                                <span class="tve_image_frame">
                                    <img class="tve_image"
                                         src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/corp_phones.png' ?>"
                                         style="width: 558px;"/>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-top: 90px;">
        <div class="tve_colm tve_oth">
            <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="symbol">
                <div class="tve_cb tve_cb_symbol tve_white">
                    <div class="thrv_wrapper thrv_icon aligncenter" style="font-size: 48px;">
                        <span data-tve-icon="corp-icon-clock" class="tve_sc_icon corp-icon-clock tve_teal"></span>
                    </div>
                    <div class="tve_cb_cnt">
                        <h4 style="color: #2b2b2b; font-size: 20px;margin-top: 0;margin-bottom: 10px;"><span class="bold_text">Feature Highlight 1</span></h4>
                        <p style="color: #4a4a4a; font-size: 18px;margin-top: 0;margin-bottom: 0;">Use this list to describe the most important features and benefits of your offer.</p>
                    </div>
                </div>
            </div>
            <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="symbol">
                <div class="tve_cb tve_cb_symbol tve_white">
                    <div class="thrv_wrapper thrv_icon aligncenter" style="font-size: 48px;">
                        <span data-tve-icon="corp-icon-hat" class="tve_sc_icon corp-icon-hat tve_teal"></span>
                    </div>
                    <div class="tve_cb_cnt">
                        <h4 style="color: #2b2b2b; font-size: 20px;margin-top: 0;margin-bottom: 10px;"><span class="bold_text">Feature Highlight 3</span></h4>
                        <p style="color: #4a4a4a; font-size: 18px;margin-top: 0;margin-bottom: 0;">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Molestiae, officia? </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="tve_colm tve_oth">
            <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="symbol">
                <div class="tve_cb tve_cb_symbol tve_white">
                    <div class="thrv_wrapper thrv_icon aligncenter" style="font-size: 48px;">
                        <span data-tve-icon="corp-icon-leaf" class="tve_sc_icon corp-icon-leaf tve_teal"></span>
                    </div>
                    <div class="tve_cb_cnt">
                        <h4 style="color: #2b2b2b; font-size: 20px;margin-top: 0;margin-bottom: 10px;"><span class="bold_text">Feature Highlight 2</span></h4>
                        <p style="color: #4a4a4a; font-size: 18px;margin-top: 0;margin-bottom: 0;">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Molestiae, officia? </p>
                    </div>
                </div>
            </div>
            <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="symbol">
                <div class="tve_cb tve_cb_symbol tve_white">
                    <div class="thrv_wrapper thrv_icon aligncenter" style="font-size: 48px;">
                        <span data-tve-icon="corp-icon-ribbon" class="tve_sc_icon corp-icon-ribbon tve_teal"></span>
                    </div>
                    <div class="tve_cb_cnt">
                        <h4 style="color: #2b2b2b; font-size: 20px;margin-top: 0;margin-bottom: 10px;"><span class="bold_text">Feature Highlight 4</span></h4>
                        <p style="color: #4a4a4a; font-size: 18px;margin-top: 0;margin-bottom: 0;">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Molestiae, officia? </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="tve_colm tve_thc tve_lst">
            <div class="thrv_wrapper thrv_testimonial_shortcode" data-tve-style="8">
                <div class="tve_ts tve_ts3 tve_white tve_np">
                    <div class="tve_ts_o">
                    <span>
                        <b>John Doe</b>
                        UI/UX Designer
                    </span>
                    </div>
                    <div class="tve_ts_cn">
                        <span class="tve_ts_ql"></span>
                        <p style="color: #4a4a4a; font-size: 18px;margin-top: 0;margin-bottom: 10px;">
                            Add a testimonial or a quote about your product here. For an upcoming app, this could be a short vision statement about what you want to accomplish.
                        </p>
                        <span class="tve_ts_qr"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tve_lp_footer tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_columns">
        <div class="tve_colm tve_twc">
            <p class="tve_p_left" style="margin: 0; padding: 0; color: #898989; font-size: 14px">&copy; {tcb_current_year} ACME Inc.</p>
        </div>
        <div class="tve_colm tve_twc tve_lst">
            <p class="tve_p_right" style="margin: 0; padding: 0; color: #898989; font-size: 14px">
                <a href="#"><span class="underline_text">Disclaimer</span></a>
            </p>
        </div>
    </div>
</div>