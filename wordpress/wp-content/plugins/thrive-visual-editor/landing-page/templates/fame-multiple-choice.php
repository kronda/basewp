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
    <div style="width: 310px;" class="thrv_wrapper tve_image_caption aligncenter">
        <span class="tve_image_frame">
            <img class="tve_image"
                 src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/fame_logo.png' ?>"
                 style="width: 310px"/>
        </span>
    </div>
</div>
<div class="tve_lp_content_bg tve_no_drag tve_lp_content" style="margin: 0">
    <div class="tve_editor_main_content tve_empty_dropzone tve_content_width">
        <div class="thrv_wrapper thrv_contentbox_shortcode shadow_cb" data-tve-style="5">
            <div class="tve_cb tve_cb5 tve_white">
                <div class="tve_cb_cnt">
                    <h4 style="font-size: 32px;margin-bottom: 0;" class="tve_p_center">Free Download:</h4>

                    <div class="thrv_wrapper" style="margin-bottom: 0;margin-top: 0;">
                        <hr class="tve_sep tve_sep1"/>
                    </div>

                    <h1 class="tve_p_center" style="font-size: 60px;margin-bottom: 30px;">Choose Your Marketing Experience Level</h1>

                    <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="6">
                        <div class="tve_cb tve_cb6 tve_orange" style="background: #f0bd0d; margin: 0 -20px -40px;">
                            <div class="tve_cb_cnt">
                                <div class="thrv_wrapper thrv_columns" style="margin: 0;">
                                    <div class="tve_colm tve_twc">
                                        <div class="thrv_wrapper thrv_button_shortcode beginner_button" data-tve-style="1">
                                            <div class="tve_btn tve_btn5 tve_purple tve_normalBtn tve_nb" style="">
                                                <a class="tve_btnLink tve_evt_manager_listen" href="" data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                                                    <span class="tve_left tve_btn_im">
                                                        <i style="background-image: url('<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/fame_beginner.png' ?>'); background-size: cover;"></i>
                                                    </span>
                                                    <span class="tve_btn_txt"><b>Beginner</b><br/>I'm Only Just Starting Out</span>
                                                </a>
                                            </div>
                                            <p style="color: #432842; font-size: 36px;" class="ttfm-lp ">or</p>
                                        </div>
                                    </div>
                                    <div class="tve_colm tve_twc tve_lst">
                                        <div class="thrv_wrapper thrv_button_shortcode tve_fullwidthBtn" data-tve-style="1">
                                            <div class="tve_btn tve_btn5 tve_purple tve_normalBtn tve_nb" style="">
                                                <a class="tve_btnLink tve_evt_manager_listen" href="" data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                                                    <span class="tve_left tve_btn_im">
                                                        <i style="background-image: url('<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/fame_advanced.png' ?>'); background-size: cover;"></i>
                                                    </span>
                                                    <span class="tve_btn_txt"><b>Advanced</b><br/> I Have a Business Up & Running</span>
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
</div>

<div class="tve_lp_footer tve_empty_dropzone tve_drop_constraint">
    <div class="thrv_wrapper thrv_content_container_shortcode">
        <div class="tve_clear"></div>
        <div class="tve_center tve_content_inner" style="width: 400px;min-width:50px; min-height: 2em;">
            <div class="thrv_wrapper thrv_contentbox_shortcode footer_cb" data-tve-style="4">
                <div class="tve_cb tve_cb4 tve_orange" style="background: transparent;border-radius: 200px;">
                    <div class="tve_cb_cnt">
                        <p class="tve_p_center" style="margin: 0; padding: 0; font-size: 16px; color: #fff;font-weight: 100;">
                            &copy; 2014 Landing Page. All rights Reserved. <a href="http://thrivethemes.com" target="_blank"><span class="underline_text">Disclaimer</span></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="tve_clear"></div>
    </div>
</div>