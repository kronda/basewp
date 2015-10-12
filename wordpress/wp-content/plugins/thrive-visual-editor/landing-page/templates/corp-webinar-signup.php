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

$timezone_offset = get_option('gmt_offset');
$sign = ($timezone_offset < 0 ? '-' : '+');
$min = abs($timezone_offset) * 60;
$hour = floor($min / 60);
$tzd = $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($min % 60, 2, '0', STR_PAD_LEFT);
?>
<div class="tve_lp_header tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1" style="">
        <div class="out">
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
        <div class="out pswr ">
            <div class="in lightSec pddbg"
                 style="background-image: url('<?php echo TVE_LANDING_PAGE_TEMPLATE . "/css/images/corp_pagesection_bg.jpg" ?>');
                     box-shadow: none; box-sizing: border-box;max-width: 100vw;" data-width="1980" data-height="592">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_content_container_shortcode">
                        <div class="tve_clear"></div>
                        <div class="tve_center tve_content_inner" style="width: 840px;min-width:50px; min-height: 2em;">
                            <h1 class="rft tve_p_center"
                                style="color: #fff; font-size: 54px; margin-top: 40px;margin-bottom: 20px;">
                                <span class="bold_text">2 Simple Steps</span> to increase your sales
                            </h1>
                            <h4 style="color: #fff; font-size: 22px;margin-top: 0;margin-bottom: 0;"
                                class="tve_p_center">
                                Join us for this live webinar event & discover how a simple strategy can double your
                                sales this
                                year - no matter what market you are in.</h4>
                        </div>
                        <div class="tve_clear"></div>
                    </div>
                    <div class="thrv_wrapper thrv_content_container_shortcode">
                        <div class="tve_clear"></div>
                        <div class="tve_center tve_content_inner" style="width: 550px;min-width:50px; min-height: 2em;">
                            <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="6">
                                <div class="tve_cb tve_cb6 tve_black" style="margin-bottom: -42px;">
                                    <div class="tve_cb_cnt">
                                        <p class="tve_p_center rft"
                                           style="color: #fff; font-size: 48px;line-height: 52px;margin-top: 0;margin-bottom: 0;">
                                            <span class="bold_text">Monday, November 11 4:00</span> PM Eastern Time
                                        </p>

                                        <div class="thrv_wrapper thrv_button_shortcode tve_centerBtn"
                                             data-tve-style="1">
                                            <div class="tve_btn tve_btn2 tve_nb tve_teal tve_normalBtn">
                                                <a class="tve_btnLink tve_evt_manager_listen" href="" data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                                                    <span class="tve_left tve_btn_im">
                                                        <i></i>
                                                        <span class="tve_btn_divider"></span>
                                                    </span>
                                                    <span class="tve_btn_txt">SIGN ME UP</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tve_clear"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-top: 60px;margin-bottom: 60px;">
        <div class="tve_colm tve_foc tve_df tve_ofo ">
            <p class="tve_p_left rft"
               style="color: #333; font-size: 52px;line-height: 52px;margin-top: 0;margin-bottom: 0;">
                <span class="bold_text">
                    The Webinar
                </span>
            </p>
        </div>
        <div class="tve_colm tve_tfo tve_df tve_lst">
            <p style="color: #4a4a4a; font-size: 22px;margin-top: 0;margin-bottom: 0;">
                Add a brief description of your webinar here. Keep it short and emphasize the main benefit or benefits
                your webinar attendees will get from taking part. In your description, leave one or several "curiosity
                gaps", so that readers will want to know more...
            </p>
        </div>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1" style="">
        <div class="out" style="background-color: #3e3e3e;">
            <div class="in lightSec">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_columns">
                        <div class="tve_colm tve_twc">
                            <div class="thrv_wrapper thrv_columns">
                                <div class="tve_colm tve_twc">
                                    <h3 style="color: #fff; font-size: 26px;margin-top: 50px;margin-bottom: 0;">
                                        <span class="bold_text">Sam Smith</span>
                                    </h3>

                                    <p style="color: #fff; font-size: 16px;margin-top: 0;margin-bottom: 20px;">CEO,
                                        Co-Founder</p>

                                    <p style="color: #fff; font-size: 19px;margin-top: 0;margin-bottom: 0;">
                                        Ex vim labore molestiae voluptatum, dolores singulis intellegebat te cum. Semper
                                        hendrerit quo te, his at soluta
                                    </p>
                                </div>
                                <div class="tve_colm tve_twc tve_lst">
                                    <div style="width: 282px;margin-top: 0;margin-bottom: 0;"
                                         class="thrv_wrapper tve_image_caption img_style_circle alignright">
                                        <span class="tve_image_frame">
                                            <img class="tve_image"
                                                 src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/corp_guy1.png' ?>"
                                                 style="width: 282px;"/>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tve_colm tve_twc tve_lst">
                            <div class="thrv_wrapper thrv_columns">
                                <div class="tve_colm tve_twc">
                                    <div style="width: 282px;margin-top: 0;margin-bottom: 0;"
                                         class="thrv_wrapper tve_image_caption img_style_circle">
                                        <span class="tve_image_frame">
                                            <img class="tve_image"
                                                 src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/corp_guy2.png' ?>"
                                                 style="width: 282px;"/>
                                        </span>
                                    </div>
                                </div>
                                <div class="tve_colm tve_twc tve_lst">
                                    <h3 style="color: #fff; font-size: 26px;margin-top: 50px;margin-bottom: 0;"
                                        class="tve_p_right">
                                        <span class="bold_text">John Doe</span>
                                    </h3>

                                    <p style="color: #fff; font-size: 16px;margin-top: 0;margin-bottom: 20px;"
                                       class="tve_p_right">Guest Expert</p>

                                    <p class="tve_p_right"
                                       style="color: #fff; font-size: 19px;margin-top: 0;margin-bottom: 0;">
                                        Ex vim labore molestiae voluptatum, dolores singulis intellegebat te cum. Semper
                                        hendrerit quo te, his at soluta
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <h2 class="tve_p_center rft" style="color: #333; font-size: 42px; margin-bottom: 0;margin-top: 60px;">Registration
        <span class="bold_text">Closes</span> in:</h2>

    <div class="thrv_wrapper thrv_countdown_timer tve_cd_timer_plain tve_clearfix init_done"
         data-date="<?php echo date('Y-m-d', strtotime('+9 days')) ?>"
         data-hour="<?php echo date('h') ?>"
         data-min="<?php echo date('m') ?>"
         data-timezone="<?php echo $tzd ?>">
        <div class="sc_timer_content tve_clearfix tve_block_center" style="margin-top: 30px; margin-bottom: 60px;">
            <div class="tve_t_day tve_t_part">
                <div class="t-digits"></div>
                <div class="t-caption">days</div>
            </div>
            <div class="tve_t_hour tve_t_part">
                <div class="t-digits"></div>
                <div class="t-caption">hours</div>
            </div>
            <div class="tve_t_min tve_t_part">
                <div class="t-digits"></div>
                <div class="t-caption">minutes</div>
            </div>
            <div class="tve_t_sec tve_t_part">
                <div class="t-digits"></div>
                <div class="t-caption">seconds</div>
            </div>
            <div class="tve_t_text"></div>
        </div>
    </div>
    <h2 style="color: #333; font-size: 42px; margin-top: 0;margin-bottom: 0;" class="tve_p_center rft">Get this <span
            class="bold_text">Exclusive</span> Training</h2>

    <div class="thrv_wrapper thrv_button_shortcode tve_centerBtn"
         data-tve-style="1">
        <div class="tve_btn tve_btn2 tve_nb tve_teal tve_normalBtn">
            <a class="tve_btnLink tve_evt_manager_listen" href="" data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                <span class="tve_left tve_btn_im">
                    <i></i>
                    <span class="tve_btn_divider"></span>
                </span>
                <span class="tve_btn_txt">SIGN ME UP</span>
            </a>
        </div>
    </div>
</div>

<div class="tve_lp_footer tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_columns">
        <div class="tve_colm tve_twc">
            <p class="tve_p_left" style="margin: 0; padding: 0; color: #898989; font-size: 14px;">&copy; {tcb_current_year} ACME
                Inc.</p>
        </div>
        <div class="tve_colm tve_twc tve_lst">
            <p class="tve_p_right" style="margin: 0; padding: 0; color: #898989; font-size: 14px;">
                <a href="#"><span class="underline_text">Disclaimer</span></a>
            </p>
        </div>
    </div>
</div>