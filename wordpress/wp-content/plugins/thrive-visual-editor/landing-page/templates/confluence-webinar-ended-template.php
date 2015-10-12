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
        <div class="out" style="background-color: #fff;">
            <div class="in darkSec" style="padding-top: 10px;padding-bottom: 10px;">
                <div class="cck clearfix">
                    <div style="width: 255px;margin-top: 0;margin-bottom: 0;"
                         class="thrv_wrapper tve_image_caption aligncenter">
                        <span class="tve_image_frame">
                            <img class="tve_image"
                                 src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/confluence_logo.png' ?>"
                                 style="width: 255px;"/>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1" style="" id="confluence_header_ps">
        <div class="pswr out" style="background-color: #fff;">
            <div class="in lightSec pddbg" data-width="1920" data-height="403"
                 style="box-sizing: border-box; max-width: 1902px; box-shadow: none; background-image: url('<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/confluence_page_section_bg.png' ?>');">
                <div class="cck clearfix">
                    <h1 class="tve_p_center rft" style="color: #fff; font-size: 44px;margin-top: 50px;margin-bottom: 20px;">
                        Sorry, You’ve Missed the Deadline for this Event...
                    </h1>

                    <div class="thrv_wrapper thrv_content_container_shortcode">
                        <div class="tve_clear"></div>
                        <div class="tve_center tve_content_inner" style="width: 700px;min-width:50px; min-height: 2em;">
                            <p class="tve_p_center"
                               style="color: #fff; font-size: 18px;margin-top: 0;margin-bottom: 20px;">
                                The webinar you were trying to sign up for has already taken place. However, you can
                                still get access to the recording!
                            </p>
                        </div>
                        <div class="tve_clear"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="tve_lp_content tve_editor_main_content tve_empty_dropzone tve_content_width">
    <h4 class="tve_p_center rft" style="color: #666; font-size: 30px;margin-top: 10px; margin-bottom: 10px;">
        Get Access to the Webinar Recording for:
    </h4>
    <div class="thrv_wrapper thrv_content_container_shortcode">
        <div class="tve_clear"></div>
        <div class="tve_center tve_content_inner" style="width: 770px;min-width:50px; min-height: 2em;">
            <h2 class="tve_p_center rft" style="color: #2f6e92; font-size: 44px;margin-top: 25px;margin-bottom: 10px;">
                Landing Page Marketing Strategies
                for <font color="#ffe60d">Better</font> User Engagement
            </h2>
        </div>
        <div class="tve_clear"></div>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1" style="margin-top: 60px;margin-bottom: -64px;">
        <div class="out" style="background-color: #eaeaea;">
            <div class="in darkSec" style="padding-top: 10px;padding-bottom: 10px;">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_button_shortcode tve_centerBtn confluence_top_button"
                         data-tve-style="1" style="margin-bottom: 0;">
                        <div class="tve_btn tve_orange tve_bigBtn tve_btn1 tve_nb">
                            <a class="tve_btnLink tve_evt_manager_listen" href="" data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                                <span class="tve_left tve_btn_im">
                                    <i></i>
                                </span>
                                <span class="tve_btn_txt">CLICK HERE TO GAIN ACCESS</span>
                            </a>
                        </div>
                    </div>
                    <div class="thrv_wrapper thrv_content_container_shortcode">
                        <div class="tve_clear"></div>
                        <div class="tve_center tve_content_inner"
                             style="width: 800px;min-width:50px; min-height: 2em;margin-top: -30px;">
                            <h3 class="tve_p_center rft"
                                style="color: #2f6e92;font-size: 38px;margin-top: 0;margin-bottom: 40px;">
                                What You'll Discover in this Replay:
                            </h3>
                            <ol class="thrv_wrapper" style="margin-bottom: 0;">
                                <li>Sed dolor sem, consectetuer gravida, ultrices sed, tristique sit amet, dui.
                                    Suspendisse
                                    posuere
                                    ipsum vehicula urna.
                                </li>
                                <li>Ut bibendum venenatis ante. Etiam eget libero. Pellentesque vel eros. Nam accumsan
                                    metus
                                    cursus
                                    purus.
                                </li>
                                <li>Maecenas sit amet turpis. Ut sollicitudin pede sed nunc. Ut iaculis nulla vulputate
                                    magna.
                                    Nulla
                                    enim. Integer elit.
                                </li>
                            </ol>
                        </div>
                        <div class="tve_clear"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_button_shortcode tve_centerBtn confluence_bottom_button" data-tve-style="1">
        <div class="tve_btn tve_orange tve_bigBtn tve_btn1 tve_nb">
            <a class="tve_btnLink tve_evt_manager_listen" href="" data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                <span class="tve_left tve_btn_im">
                    <i></i>
                </span>
                <span class="tve_btn_txt">CLICK HERE TO GAIN ACCESS</span>
            </a>
        </div>
    </div>
</div>

<div class="tve_lp_footer tve_empty_dropzone">
    <div class="thrv_wrapper thrv_page_section tve_no_drag" data-tve-style="1">
        <div class="out" style="background: #515151;">
            <div class="in lightSec">
                <div class="cck tve_clearfix">
                    <p class="tve_p_center">
                        &copy {tcb_current_year} by Thrive Themes - <a href="">Disclaimer</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>