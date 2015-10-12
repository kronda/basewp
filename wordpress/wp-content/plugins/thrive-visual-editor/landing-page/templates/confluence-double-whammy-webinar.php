<?php
$timezone_offset = get_option('gmt_offset');
$sign = ($timezone_offset < 0 ? '-' : '+');
$min = abs($timezone_offset) * 60;
$hour = floor($min / 60);
$tzd = $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($min % 60, 2, '0', STR_PAD_LEFT);
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
                    <h5 class="tve_p_center" style="color: #fff; font-size: 22px;margin-top: 0;margin-bottom: 20px;">
                        LIVE TRAINING WEBINAR
                    </h5>

                    <h1 class="tve_p_center rft" style="color: #fff; font-size: 44px;margin-top: 0;margin-bottom: 20px;">
                        Marketing secrets for<br/>
                        <font color="#ffe60d">Highly profitable</font> online small businesses
                    </h1>

                    <div class="thrv_wrapper thrv_columns" style="margin-top: 0;margin-bottom: 0;">
                        <div class="tve_colm tve_twc">
                            <div class="thrv_wrapper thrv_columns" style="margin-top: 0;margin-bottom: 0;">
                                <div class="tve_colm tve_twc">
                                    <h3 style="color: #fff; font-size: 30px;margin-top: 0;margin-bottom: 0;" class="tve_p_center rft">
                                        LIVE TRAINING WEBINAR
                                    </h3>
                                </div>
                                <div class="tve_colm tve_twc tve_lst">
                                    <div style="width: 201px;margin-top: 0;margin-bottom: 0;"
                                         class="thrv_wrapper tve_image_caption aligncenter">
                                        <span class="tve_image_frame">
                                            <img class="tve_image"
                                                 src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/confluence-whammy-image1.png' ?>"
                                                 style="width: 201px;"/>
                                        </span>
                                    </div>
                                    <div style="width: 30px;margin-top: 0;margin-bottom: 0;"
                                         class="thrv_wrapper tve_image_caption aligncenter confluence_plus_icon">
                                        <span class="tve_image_frame">
                                            <img class="tve_image"
                                                 src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/confluence-plus.png' ?>"
                                                 style="width: 30px;"/>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tve_colm tve_twc tve_lst">
                            <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-top: 0;margin-bottom: 0;">
                                <div class="tve_colm tve_oth">
                                    <div style="width: 127px;margin-top: 0;margin-bottom: 0;"
                                         class="thrv_wrapper tve_image_caption aligncenter">
                                            <span class="tve_image_frame">
                                                <img class="tve_image"
                                                     src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/confluence-whammy-image2.png' ?>"
                                                     style="width: 127px;"/>
                                            </span>
                                    </div>
                                </div>
                                <div class="tve_colm tve_tth tve_lst">
                                    <h3 class="tve_p_center rft" style="color: #fff; font-size: 30px;margin-top: 0;margin-bottom: 0;">
                                        "10 TIPS FOR BETTER LANDING PAGES"
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="tve_lp_content tve_editor_main_content tve_empty_dropzone tve_content_width">
    <h4 class="tve_p_center" style="color: #2f6e92; font-size: 24px;margin-top: 0; margin-bottom: 10px;">
        Register your spot on the webinar today <br>
        and you'll receive instant access to our free landing page optimization guide.
    </h4>

    <p class="tve_p_center" style="color: #666666; font-size: 18px;margin-top: 0;margin-bottom: 20px;">
        In the guide, you'll find simple tweaks that you can apply to boost your conversion rates immediately and it's
        the perfect intro to the more advanced techniques you'll learn about on the live webinar.
    </p>

    <div class="thrv_wrapper thrv_page_section" data-tve-style="1" style="margin-top: 60px;">
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
                                <span class="tve_btn_txt">CLICK HERE TO RESERVE YOUR SPOT</span>
                            </a>
                        </div>
                    </div>
                    <div class="thrv_wrapper thrv_content_container_shortcode">
                        <div class="tve_clear"></div>
                        <div class="tve_center tve_content_inner" style="width: 775px;min-width:50px; min-height: 2em;margin-top: -30px;">
                            <h2 class="tve_p_center rft" style="color: #2f6e92;font-size: 38px;margin-top: 0;margin-bottom: 40px;">
                                What You'll Discover On this Live Webinar:
                            </h2>
                            <ol class="thrv_wrapper" style="margin-bottom: 0;">
                                <li>Sed dolor sem, consectetuer gravida, ultrices sed, tristique sit amet, dui. Suspendisse
                                    posuere
                                    ipsum vehicula urna.
                                </li>
                                <li>Ut bibendum venenatis ante. Etiam eget libero. Pellentesque vel eros. Nam accumsan metus
                                    cursus
                                    purus.
                                </li>
                                <li>Maecenas sit amet turpis. Ut sollicitudin pede sed nunc. Ut iaculis nulla vulputate magna.
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
    <div class="thrv_wrapper thrv_content_container_shortcode">
        <div class="tve_clear"></div>
        <div class="tve_center tve_content_inner" style="width: 500px;min-width:50px; min-height: 2em;">
            <div class="thrv_wrapper thrv_columns">
                <div class="tve_colm tve_twc">
                    <p class="tve_p_center" style="color: #2f6e92; font-size: 18px;margin-top: 0;margin-bottom: 30px;">
                        <span class="bold_text">
                            PRESENTED BY
                        </span>
                    </p>

                    <div class="thrv_wrapper tve_image_caption aligncenter img_style_circle" style="width: 165px;">
                        <span class="tve_image_frame">
                            <img class="tve_image"
                                 src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/shane_melaugh2.jpg' ?>"
                                 style="width: 165px;"/>
                        </span>
                    </div>
                    <h5 class="tve_p_center" style="color: #2f6e92; font-size: 22px;margin-top: 20px;margin-bottom: 0;">
                        <span class="bold_text">
                            Shane Melaugh
                        </span>
                    </h5>
                    <h5 class="tve_p_center" style="color: #666666; font-size: 22px;margin-top: 0;margin-bottom: 15px;">
                        Thrive Themes
                    </h5>
                </div>
                <div class="tve_colm tve_twc tve_lst">
                    <p class="tve_p_center" style="color: #2f6e92; font-size: 18px;margin-top: 0;margin-bottom: 0;">
                        <span class="bold_text">
                            WEBINAR TIME &DATE
                        </span>
                    </p>

                    <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="1">
                        <div class="tve_cb tve_cb1 tve_blue">
                            <div class="tve_hd tve_cb_cnt">
                                <h3 class="tve_p_center">
                                    AUGUST 15
                                </h3>
                                <span></span>
                            </div>
                            <div class="tve_cb_cnt">
                                <p style="color: #666; font-size: 20px; margin-top: 0;margin-bottom: 0;" class="tve_p_center">
                                    Saturday
                                </p>

                                <p class="tve_p_center"
                                   style="color: #2f6e92; font-size: 20px;margin-top: 0;margin-bottom: 0;">
                            <span class="bold_text">
                                10:00 PM <br>
                                GMT
                            </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <p class="tve_p_center" style="color: #2f6e92; font-size: 20px;margin-top: 0;margin-bottom: 0;">
                        TIME LEFT
                    </p>
                    <div class="thrv_wrapper thrv_countdown_timer tve_cd_timer_plain tve_clearfix init_done tve_blue tve_countdown_1"
                         data-date="<?php echo date('Y-m-d', strtotime('+9 days')) ?>"
                         data-hour="<?php echo date('h') ?>"
                         data-min="<?php echo date('m') ?>"
                         data-timezone="<?php echo $tzd ?>">
                        <div class="sc_timer_content tve_clearfix tve_block_center" style="">
                            <div class="tve_t_day tve_t_part">
                                <div class="t-digits"></div>
                                <div class="t-caption">DAYS</div>
                            </div>
                            <div class="tve_t_hour tve_t_part">
                                <div class="t-digits"></div>
                                <div class="t-caption">HOURS</div>
                            </div>
                            <div class="tve_t_min tve_t_part">
                                <div class="t-digits"></div>
                                <div class="t-caption">MIN</div>
                            </div>
                            <div class="tve_t_sec tve_t_part">
                                <div class="t-digits"></div>
                                <div class="t-caption">SEC</div>
                            </div>
                            <div class="tve_t_text"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tve_clear"></div>
    </div>
    <div class="thrv_wrapper thrv_button_shortcode tve_centerBtn confluence_bottom_button" data-tve-style="1">
        <div class="tve_btn tve_orange tve_bigBtn tve_btn1 tve_nb">
            <a class="tve_btnLink tve_evt_manager_listen" href="" data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                <span class="tve_left tve_btn_im">
                    <i></i>
                </span>
                <span class="tve_btn_txt">CLICK HERE TO RESERVE TOUR SPOT</span>
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