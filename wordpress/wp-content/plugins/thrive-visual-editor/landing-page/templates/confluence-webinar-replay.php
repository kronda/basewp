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
                    <h6 class="tve_p_center">Time before this webinar recording is removed:</h6>

                    <div class="thrv_wrapper thrv_countdown_timer tve_cd_timer_plain tve_clearfix init_done tve_white"
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
                    <h1 class="tve_p_center rft" style="color: #fff; font-size: 44px;margin-top: 20px;margin-bottom: 30px;">
                        MARKETING SECRETS FOR <br>
                        <font color="#ffe60d">HIGHLY PROFITABLE</font> ONLINE SMALL BUSINESSES
                    </h1>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="tve_lp_content tve_editor_main_content tve_empty_dropzone tve_content_width">
    <div class="thrv_responsive_video thrv_wrapper" data-type="youtube" data-rel="0" data-controls="0" data-showinfo="0"
         data-url="https://www.youtube.com/watch?v=sYMqHaWErZY&amp;list=UUuL6ZEN9ZF-8C5s8HlcdoyQ"
         data-embeded-url="//www.youtube.com/embed/sYMqHaWErZY"
         data-modestbranding="1" style="margin-top: 0;">
        <div style="display: block;" class="tve_responsive_video_container">
            <div class="video_overlay"></div>
            <iframe frameborder="0" allowfullscreen=""
                    src="//www.youtube.com/embed/sYMqHaWErZY?rel=0&amp;modestbranding=1&amp;controls=0&amp;showinfo=0&amp;autoplay=0&amp;fs=1&amp;wmode=transparent"></iframe>
        </div>
    </div>
    <div class="thrv_wrapper thrv_button_shortcode tve_centerBtn" data-tve-style="1">
        <div class="tve_btn tve_orange tve_bigBtn tve_btn1 tve_nb" style="margin-top: 20px;margin-bottom: 20px;">
            <a class="tve_btnLink tve_evt_manager_listen" href="" data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                <span class="tve_left tve_btn_im">
                    <i></i>
                </span>
                <span class="tve_btn_txt">CLICK HERE TO GET THE SPECIAL WEBINAR OFFER</span>
            </a>
        </div>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #eaeaea;">
            <div class="in darkSec">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_content_container_shortcode">
                        <div class="tve_clear"></div>
                        <div class="tve_center tve_content_inner" style="width: 775px;min-width:50px; min-height: 2em;">
                            <h2 class="tve_p_center rft"
                                style="color: #2f6e92;font-size: 38px;margin-top: 20px;margin-bottom: 40px;">
                                Topics covered in this webinar:
                            </h2>
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