<?php
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
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1" style="" id="copy-2-lead-generation-ps">
        <div class="out" style="background-color: #0c81bf;">
            <div class="in lightSec">
                <div class="cck clearfix">
                    <h1 class="tve_p_center" style="color: #fff; font-size: 70px;margin-top: 0;margin-bottom: 0;">
                        The <span class="bold_text">"Copy-Hacking"</span> System for High-Converting Content
                    </h1>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="tve_lp_content tve_editor_main_content tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_content_container_shortcode">
        <div class="tve_clear"></div>
        <div class="tve_center tve_content_inner" style="width: 930px;min-width:50px; min-height: 2em;">
            <p style="color: #434343; font-size: 22px;margin-top: 10px;margin-bottom: 50px;">
                So, you've got a great product to sell, but you really hate copywriting? You're not alone. That's why
                we've created this free report to teach you the pain-free copywriting tricks for non-writers.
            </p>

            <div class="thrv_wrapper thrv_columns tve_clearfix">
                <div class="tve_colm tve_oth">
                    <div style="width: 202px;margin-top: 0;margin-bottom: 0;"
                         class="thrv_wrapper tve_image_caption aligncenter">
                        <span class="tve_image_frame">
                            <img class="tve_image"
                                 src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/copy2-product-launch-image.png' ?>"
                                 style="width: 202px;"/>
                        </span>
                    </div>
                </div>
                <div class="tve_colm tve_tth tve_lst">
                    <ul class="thrv_wrapper">
                        <li>
                            Discover the simple trick that will eliminate writer's block completely. No more staring at that blank screen!
                        </li>
                        <li>
                            Simple, fill-in-the-blanks recipes that you can use to write great headlines, blog post titles and more!
                        </li>
                        <li>
                            A (surprisingly short) check-list you can use to audit any piece of content you write and transform it from "meh" to "wow!"
                        </li>
                    </ul>
                </div>
            </div>
            <div class="thrv_wrapper thrv_button_shortcode tve_fullwidthBtn" data-tve-style="1">
                <div class="tve_btn tve_btn3 tve_nb tve_orange tve_bigBtn">
                    <a class="tve_btnLink tve_evt_manager_listen" href=""
                       data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                        <span class="tve_left tve_btn_im">
                            <i></i>
                            <span class="tve_btn_divider"></span>
                        </span>
                        <span class="tve_btn_txt">Yes, I Want to Start Writing Awesome Copy!</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="tve_clear"></div>
    </div>
</div>

<div class="tve_lp_footer tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_columns">
        <div class="tve_colm tve_twc">
            <p class="tve_p_left" style="margin: 0; padding: 0; color: #333; font-size: 22px">Copyright - Company Name
                Inc.</p>
        </div>
        <div class="tve_colm tve_twc tve_lst">
            <p class="tve_p_right" style="margin: 0; padding: 0; color: #666; font-size:22px">
                <a href="#">Disclaimer</a>
                <a href="#">Contact</a>
            </p>
        </div>
    </div>
</div>