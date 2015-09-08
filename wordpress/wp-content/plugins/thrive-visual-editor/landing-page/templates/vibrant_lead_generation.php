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
</div>

<div class="tve_lp_content tve_editor_main_content tve_empty_dropzone thrv_wrapper tve_no_drag"
     style="margin-top: 50px; max-width: 1180px; background-attachment: scroll;">
    <h1 class="tve_p_center" style="font-size: 62px;margin-bottom: 50px; margin-top: 0;">
        <font color="#f58000">
            5 Simple Steps
        </font>
        to Boosting Your Website’s Conversion Rate.
    </h1>

    <div class="thrv_wrapper thrv_page_section" data-tve-style="1" style="margin-bottom: 40px;">
        <div class="out" style="background-color: #459285">
            <div class="in lightSec">
                <div class="cck tve_clearfix">
                    <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="5">
                        <div class="tve_cb tve_cb5 tve_teal">
                            <div class="tve_cb_cnt">
                                <div class="thrv_wrapper thrv_columns tve_clearfix">
                                    <div class="tve_colm tve_oth">
                                        <div style="width: 383px;" class="thrv_wrapper tve_image_caption aligncenter">
                                            <span class="tve_image_frame">
                                                <img class="tve_image tve_lead_product"
                                                     src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/vibrant_lead_product.png' ?>"
                                                     style="width: 383px"/>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="tve_colm tve_tth tve_lst">
                                        <h3 style="line-height: 35px;">
                                            <span class="bold_text">
                                                <font color="#1f4640">FREE VIDEO REVEALS ...</font> how easy it is to
                                                increase the value of every visitor you get.
                                            </span>
                                        </h3>
                                        <ul class="thrv_wrapper">
                                            <li>This is Photoshop's version of Lorem Ipsum. Proin gravida nibh
                                                vel velit auctor aliquet.
                                            </li>
                                            <li>Aenean sollicitudin, lorem quis bibendum auctor, nisi elit ipsum, nec
                                                sagittis sem nibh id elit.
                                            </li>
                                            <li>
                                                Nam nec tellus a odio tincidunt auctor a ornare odio.
                                            </li>
                                        </ul>
                                        <div class="thrv_wrapper" style="margin-top: 0;">
                                            <hr class="tve_sep tve_sep1"/>
                                        </div>
                                        <div class="thrv_wrapper thrv_button_shortcode tve_fullwidthBtn" data-tve-style="1">
                                            <div class="tve_btn tve_btn3 tve_nb tve_orange tve_bigBtn" style="margin-top: -20px;">
                                                <a class="tve_btnLink tve_evt_manager_listen" href="" data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                                                    <span class="tve_left tve_btn_im">
                                                        <i></i>
                                                        <span class="tve_btn_divider"></span>
                                                    </span>
                                                    <span class="tve_btn_txt">YES! Send me my free video lesson &GT;&GT;</span>
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
    <p class="tve_p_center" style="font-size: 20px; color: #cccccc; margin-bottom: 20px;">
        <span class="bold_text">As seen on:</span>
    </p>

    <div class="thrv_wrapper thrv_page_section tve_corner_ps" data-tve-style="1">
        <div class="out" style="background-color: #f2f2f2">
            <div class="in lightSec">
                <div class="cck tve_clearfix">
                    <div style="width: 1086px;" class="thrv_wrapper tve_image_caption aligncenter">
                        <span class="tve_image_frame">
                            <img class="tve_image"
                                 src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/logos.png' ?>"
                                 style="width: 1086px"/>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="tve_lp_footer tve_empty_dropzone tve_drop_constraint" data-forbid=".thrv_page_section,.sc_page_section">
    <p class="tve_p_center" style="color: #333333;font-size: 17px;margin-top: 10px;">© 2014 Webinar Landing Page. All
        rights Reserved | Disclaimer</p>
</div>