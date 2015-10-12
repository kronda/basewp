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
        <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="5">
            <div class="tve_cb tve_cb5 tve_white">
                <div class="tve_cb_cnt">
                    <h1 class="tve_p_center" style="margin-bottom: 10px;">Style Your Future With A Fashion Career</h1>

                    <div class="thrv_wrapper" style="margin-bottom: 0;">
                        <hr class="tve_sep tve_sep1"/>
                    </div>
                    <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-top: 0;">
                        <div class="tve_colm tve_oth">
                            <div style="width: 258px;" class="thrv_wrapper tve_image_caption aligncenter img_style_lifted_style2">
                            <span class="tve_image_frame">
                                <img class="tve_image"
                                     src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/fame_shane.png' ?>"
                                     style="width: 258px"/>
                            </span>

                                <p class="wp-caption-text"><a href="http://thrivethemes.com" target="_blank"><span class="underline_text">Free Fashion Career Guide</span></a></p>
                            </div>
                        </div>
                        <div class="tve_colm tve_tth tve_lst">
                            <h4 class="custom_lh" style="line-height: 50px;margin-bottom: 40px;margin-top: 20px;"><span class="bold_text"><font color="#f0bd0d">The 3 Things You’ll Discover</font></span> in this free
                                guide to starting your fashion career :</h4>
                            <ol class="thrv_wrapper">
                                <li><span class="tve_custom_font_size  rft bold_text" style="font-size: 30px;">50%</span> Duis sed odio sit amet nibh vulputate cursus a sit amet</li>
                                <li>Aenean sollicitudin, lorem <span class="bold_text">quis bibendum auctor</span>, nisi elit
                                    consequat ipsum, nec sagittis sem nibh id elit.
                                </li>
                                <li>Velit auctor aliquet. Aenean sollicitudin, lorem quis bibendum
                                    auctor, nibh id elit.
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="6">
            <div class="tve_cb tve_cb6 tve_orange" style="background: #f0bd0d">
                <div class="tve_cb_cnt">
                    <div class="thrv_wrapper thrv_button_shortcode tve_fullwidthBtn" data-tve-style="1">
                        <div class="tve_btn tve_btn7 tve_purple tve_normalBtn tve_nb" style="">
                            <a class="tve_btnLink tve_evt_manager_listen" href="" data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                                <span class="tve_left tve_btn_im">
                                    <i></i>
                                </span>
                                <span class="tve_btn_txt">YES, I WANT TO GET STARTED RIGHT AWAY!</span>
                            </a>
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
                            &copy; {tcb_current_year} Landing Page. All rights Reserved.
                            <a href="http://thrivethemes.com" target="_blank">
                                <font color="#fff">
                                    <span class="underline_text">Disclaimer</span>
                                </font>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="tve_clear"></div>
    </div>
</div>