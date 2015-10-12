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
                                 style="width: 267px"/>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="tve_lp_content tve_editor_main_content tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out pswr">
            <div class="in darkSec pddbg" style="
                background-image: url('<?php echo TVE_LANDING_PAGE_TEMPLATE . "/css/images/minimal_video_offer_bg.jpg" ?>');
                box-shadow: none; box-sizing: border-box;max-width: 100vw;" data-width="1680" data-height="702">
                <div class="cck tve_clearfix">
                    <div class="thrv_wrapper thrv_columns">
                        <div class="tve_colm tve_twc">
                            <div style="width: 460px;" class="thrv_wrapper tve_image_caption alignleft">
                                <span class="tve_image_frame">
                                    <img class="tve_image"
                                         src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/serene_lead_generation_book.png' ?>"
                                         style="width: 460px;"/>
                                </span>
                            </div>
                        </div>
                        <div class="tve_colm tve_twc tve_lst">
                            <h1 style="font-size: 44px;">Steps to <span class="bold_text">Doubling</span> Your Conversion Rate</h1>

                            <div class="thrv_wrapper thrv_lead_generation tve_clearfix thrv_lead_generation_vertical tve_black" data-tve-style="1">
                                <div class="thrv_lead_generation_code" style="display: none;"></div>
                                <div class="thrv_lead_generation_container tve_clearfix">
                                    <div class="tve_lead_generated_inputs_container tve_clearfix">
                                        <div class="tve_lead_fields_overlay"></div>
                                        <div class=" tve_lg_input_container ">
                                            <input type="text" data-placeholder="" value="" name="name" placeholder="Name"/>
                                        </div>
                                        <div class="tve_lg_input_container">
                                            <input type="text" data-placeholder="" value="" name="email" placeholder="Email"/>
                                        </div>
                                        <div class="tve_lg_input_container">
                                            <input type="text" data-placeholder="" value="" name="website" placeholder="Website"/>
                                        </div>
                                        <div class="tve_lg_input_container tve_submit_container">
                                            <div style="font-size: 20px; color: #305584; line-height: 20px;" class="thrv_wrapper thrv_icon aligncenter tve_draggable" draggable="true">
                                                <span style="" class="tve_sc_icon serene-leadgeneration-download" data-tve-icon="serene-leadgeneration-download"></span>
                                            </div>
                                            <button type="Submit">DOWNLOAD THE FREE REPORT</button>
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
<div class="tve_lp_footer tve_empty_dropzone">
    <div class="thrv_wrapper thrv_page_section tve_no_drag" data-tve-style="1">
        <div class="out">
            <div class="in darkSec">
                <div class="cck tve_clearfix">
                    <div style="width: 52px;" class="thrv_wrapper tve_image_caption alignleft">
                        <span class="tve_image_frame">
                            <img class="tve_image"
                                 src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/logo_minimal_video2.png' ?>"
                                 style="width: 52px"/>
                        </span>
                    </div>
                    <p class="float-left tve_p_left"
                       style="margin: 0; padding: 0; color: #a2a2a2; font-size: 18px; font-weight: 300; line-height: 52px;">
                        &copy {tcb_current_year}. All rights reserved</p>

                    <p class="float-right tve_p_right"
                       style="margin: 0; padding: 0; color: #a2a2a2; font-size: 18px; font-weight: 300; line-height: 52px;">
                        <a href="#">Disclaimer</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>