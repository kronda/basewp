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
    <div class="thrv_wrapper thrv_columns" style="margin: 20px 0; padding: 0">
        <div class="tve_colm tve_twc">
            <div style="width: 421px;" class="thrv_wrapper tve_image_caption aligncenter">
                <span class="tve_image_frame">
                    <img class="tve_image" src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/landing_page_3_book.png' ?>" style="width: 421px"/>
                </span>
            </div>
        </div>
        <div class="tve_colm tve_twc tve_lst">
            <h1 class="" style="font-size: 57px; margin: 0;">5 Minutes to <span class="bold_text">Higher Conversion Rates</span></h1>
            <p class="" style="font-size: 18px; margin: 20px 0; ">Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ad ipsum, nec sagittis sem nibh id elit. Duis sed odio sit amet nibh vulputate a sit ad mauris. Morbi accumsan ipsum velit.</p>
            <div data-tve-style="1" class="thrv_wrapper thrv_button_shortcode tve_fullwidthBtn tve_nb">
                <div class="tve_btn tve_btn3 tve_white tve_nb">
                    <a style="padding: 20px" class="tve_btnLink tve_evt_manager_listen" href="" data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                        <span class="tve_left tve_btn_im">
                            <i></i>
                        </span>
                        <span class="tve_btn_txt">Download the Free Report</span>
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
                    <p class="tve_p_center" style="margin: 0; padding: 0; color: #999999; font-size: 16px">
                        &copy; 2014 by ACME Inc. | <a href="#"><span class="underline_text">Disclaimer</span></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>