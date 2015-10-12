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
        <div class="out" style="background-color: #008bc4;">
            <div class="in">
                <div class="cck tve_clearfix">
                    <div style="width: 165px;margin-top: 10px;margin-bottom: 10px;"
                         class="thrv_wrapper tve_image_caption ">
                        <span class="tve_image_frame">
                            <img class="tve_image"
                                 src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/elementary-logo.png' ?>"
                                 style="width: 165px"/>
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
        <div class="tve_center tve_content_inner" style="width: 690px;min-width:50px; min-height: 2em;margin-top: 100px;margin-bottom: 0;">
            <h1 style="color: #555555; font-size: 40px;margin-top: 0;margin-bottom: 10px;" class="tve_p_center rft">Discover the Fastest Way to Create Conversion-Boosting Landing Pages</h1>
        </div>
        <div class="tve_clear"></div>
    </div>
    <div class="thrv_wrapper thrv_content_container_shortcode">
        <div class="tve_clear"></div>
        <div class="tve_center tve_content_inner"
             style="width: 570px;min-width:50px; min-height: 2em;margin-bottom: 100px;">
            <div class="thrv_wrapper thrv_columns tve_clearfix">
                <div class="tve_colm tve_tth">
                    <p style="color: #545454; font-size: 18px;margin-top: 30px;margin-bottom: 30px;">
                        Get started now and you can have your first landing page published in 5 minutes or less! Works with any WordPress website.
                    </p>
                </div>
                <div class="tve_colm tve_oth tve_lst">
                    <div style="width: 173px;margin-top: 0;margin-bottom: 0;"
                         class="thrv_wrapper tve_image_caption aligncenter">
                        <span class="tve_image_frame">
                            <img class="tve_image"
                                 src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/elementary-product-small.png' ?>"
                                 style="width: 173px;"/>
                        </span>
                    </div>
                </div>
            </div>
            <div class="thrv_wrapper thrv_button_shortcode tve_fullwidthBtn" data-tve-style="1">
                <div class="tve_btn tve_btn3 tve_nb tve_blue tve_bigBtn" style="margin-top: 25px;">
                    <a class="tve_btnLink tve_evt_manager_listen" href="" data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                        <span class="tve_left tve_btn_im">
                            <i></i>
                            <span class="tve_btn_divider"></span>
                        </span>
                        <span class="tve_btn_txt">CLICK HERE TO GET STARTED</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="tve_clear"></div>
    </div>
</div>

<div class="tve_lp_footer tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1" style="">
        <div class="out" style="background-color: #fff;">
            <div class="in">
                <div class="cck tve_clearfix">
                    <p style="color: #959595; font-size: 13px;margin-top: 30px;margin-bottom: 30px;" class="tve_p_center">Copyright &copy; {tcb_current_year} <a href="#"><span class="underline_text">Legal Information</span></a></p>
                </div>
            </div>
        </div>
    </div>
</div>