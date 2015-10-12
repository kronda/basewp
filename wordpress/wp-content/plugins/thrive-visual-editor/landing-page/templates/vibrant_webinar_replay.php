<?php
$timezone_offset = get_option('gmt_offset');
$sign = ($timezone_offset < 0 ? '-' : '+');
$min = abs($timezone_offset) * 60;
$hour = floor($min / 60);
$tzd = $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($min % 60, 2, '0', STR_PAD_LEFT);
?>

<div class="tve_lp_header tve_empty_dropzone tve_content_width">
</div>

<div class="tve_lp_content tve_editor_main_content tve_empty_dropzone thrv_wrapper tve_no_drag"
     style="margin-top: 50px; max-width: 1180px; background-attachment: scroll;">
    <p style="margin-bottom: 0;" class="tve_p_center">Time before this webinar replay is removed.</p>

    <div class="thrv_wrapper thrv_countdown_timer tve_cd_timer_plain tve_clearfix init_done"
         data-date="<?php echo date('Y-m-d', strtotime('+9 days')) ?>"
         data-hour="<?php echo date('h') ?>"
         data-min="<?php echo date('m') ?>"
         data-timezone="<?php echo $tzd ?>">
        <div class="sc_timer_content tve_clearfix tve_block_center" style="margin-bottom: 30px;">
            <div class="tve_t_day tve_t_part">
                <div class="t-digits"></div>
                <div class="t-caption">Days</div>
            </div>
            <div class="tve_t_hour tve_t_part">
                <div class="t-digits"></div>
                <div class="t-caption">Hours</div>
            </div>
            <div class="tve_t_min tve_t_part">
                <div class="t-digits"></div>
                <div class="t-caption">Minutes</div>
            </div>
            <div class="tve_t_sec tve_t_part">
                <div class="t-digits"></div>
                <div class="t-caption">Seconds</div>
            </div>
            <div class="tve_t_text"></div>
        </div>
    </div>
    <h1 class="tve_p_center" style="font-size: 52px;margin-bottom: 50px; margin-top: 0;">
        Why Almost Everything You Read About
        Content Marketing
        <font color="#f58000">
            is Dead Wrong...
        </font>
    </h1>

    <div class="thrv_wrapper thrv_content_container_shortcode">
        <div class="tve_clear"></div>
        <div class="tve_center tve_content_inner" style="width: 900px;min-width:50px; min-height: 2em;">
            <div class="thrv_responsive_video thrv_wrapper" data-type="youtube" data-rel="0"
                 data-controls="0" data-showinfo="0" data-url="//www.youtube.com/embed/XzwqYMzRWpo"
                 data-embeded-url="//www.youtube.com/embed/XzwqYMzRWpo"
                 style="margin-top: 0px; margin-bottom: 25px;">
                <div style="display: block;" class="tve_responsive_video_container">
                    <div class="video_overlay"></div>
                    <iframe frameborder="0" allowfullscreen=""
                            src="//www.youtube.com/embed/XzwqYMzRWpo?rel=0&amp;modestbranding=0&amp;controls=0&amp;showinfo=0&amp;autoplay=0&amp;fs=1&amp;wmode=transparent"></iframe>
                </div>
            </div>
            <div class="thrv_wrapper thrv_button_shortcode tve_fullwidthBtn" data-tve-style="1">
                <div class="tve_btn tve_btn3 tve_nb tve_orange tve_bigBtn" style="margin-top: 0px;">
                    <a class="tve_btnLink" href="">
                                <span class="tve_left tve_btn_im">
                                    <i></i>
                                    <span class="tve_btn_divider"></span>
                                </span>
                        <span class="tve_btn_txt">Click here to get the special webinar offer! &GT;&GT;</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="tve_clear"></div>
    </div>

    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #d5e8e5">
            <div class="in darkSec">
                <div class="cck tve_clearfix">
                    <h3 class="tve_p_center" style="font-size: 36px;margin-bottom: 30px;">
                        <span class="bold_text">Revealed: the more effective way to do content marketing!</span>
                    </h3>

                    <p class="tve_p_center" style="font-size: 20px;">
                        Watch the full replay video above to see exactly how we've turned the typical content marketing
                        approach on its head... and more than doubled sales and traffic as a result! Please note that
                        this replay will only be available for a limited time, so make sure to watch it today, before
                        you miss out.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <p class="tve_p_center" style="font-size: 20px; color: #cccccc; margin-top: 30px; margin-bottom: 30px;">
        <span class="bold_text">OUR HOSTS</span>
    </p>

    <div class="thrv_wrapper thrv_content_container_shortcode">
        <div class="tve_clear"></div>
        <div class="tve_center tve_content_inner" style="width: 900px;min-width:50px; min-height: 2em;">
            <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="4">
                <div class="tve_cb tve_cb4 tve_white">
                    <div class="tve_hd">
                        <span></span>
                    </div>
                    <div class="tve_cb_cnt">
                        <div class="thrv_wrapper thrv_columns" style="margin: 0;">
                            <div class="tve_colm tve_twc">
                                <div class="thrv_wrapper thrv_columns tve_clearfix">
                                    <div class="tve_colm tve_tth">
                                        <div class="thrv_wrapper thrv_columns" style="margin: 20px 0;">
                                            <div class="tve_colm tve_twc">
                                                <div style="width: 35px; margin: 0 0 0 10px;"
                                                     class="thrv_wrapper tve_image_caption alignright">
                                            <span class="tve_image_frame">
                                                <img class="tve_image"
                                                     src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/vibrant_facebook.png' ?>"
                                                     style="width: 35px"/>
                                            </span>
                                                </div>
                                                <div style="width: 35px;margin: 0;"
                                                     class="thrv_wrapper tve_image_caption alignright">
                                            <span class="tve_image_frame">
                                                <img class="tve_image"
                                                     src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/vibrant_twitter.png' ?>"
                                                     style="width: 35px"/>
                                            </span>
                                                </div>
                                            </div>
                                            <div class="tve_colm tve_twc tve_lst">
                                                <p style="color: #333333; font-size: 20px;margin-bottom: 0;margin-top: 0;padding-bottom: 0;"
                                                   class="tve_p_right">
                                                    <span class="bold_text">Shane M.</span>
                                                </p>
                                            </div>
                                        </div>
                                        <p style="font-size: 16px;" class="tve_p_right">Duis sed odio sit amet nibh
                                            vulputate
                                            cursus a sit mauris. Morbi
                                            adec accumsan ipsum velit.</p>
                                    </div>
                                    <div class="tve_colm tve_oth tve_lst">
                                        <div style="width: 135px;"
                                             class="thrv_wrapper tve_image_caption aligncenter img_style_circle">
                                    <span class="tve_image_frame">
                                        <img class="tve_image"
                                             src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/shanemelaugh2.jpg' ?>"
                                             style="width: 135px"/>
                                    </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tve_colm tve_twc tve_lst">
                                <div class="thrv_wrapper thrv_columns tve_clearfix">
                                    <div class="tve_colm tve_oth">
                                        <div style="width: 135px;"
                                             class="thrv_wrapper tve_image_caption aligncenter img_style_circle">
                                    <span class="tve_image_frame">
                                        <img class="tve_image"
                                             src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/girl.jpg' ?>"
                                             style="width: 135px"/>
                                    </span>
                                        </div>
                                    </div>
                                    <div class="tve_colm tve_tth tve_lst">
                                        <div class="thrv_wrapper thrv_columns" style="margin: 20px 0;">
                                            <div class="tve_colm tve_twc">
                                                <p style="color: #333333; font-size: 20px;margin-bottom: 0;margin-top: 0;padding-bottom: 0;"
                                                   class="tve_p_left">
                                                    <span class="bold_text">Anna S.</span>
                                                </p>
                                            </div>
                                            <div class="tve_colm tve_twc tve_lst">
                                                <div style="width: 35px; margin: 0 10px 0 0;"
                                                     class="thrv_wrapper tve_image_caption alignleft">
                                            <span class="tve_image_frame">
                                                <img class="tve_image"
                                                     src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/vibrant_twitter.png' ?>"
                                                     style="width: 35px"/>
                                            </span>
                                                </div>
                                                <div style="width: 35px; margin: 0;"
                                                     class="thrv_wrapper tve_image_caption alignleft">
                                            <span class="tve_image_frame">
                                                <img class="tve_image"
                                                     src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/vibrant_facebook.png' ?>"
                                                     style="width: 35px"/>
                                            </span>
                                                </div>
                                            </div>
                                        </div>
                                        <p style="font-size: 16px;">Duis sed odio sit amet nibh vulputate cursus a sit
                                            mauris.
                                            Morbi
                                            adec accumsan ipsum velit.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="thrv_wrapper thrv_button_shortcode tve_fullwidthBtn" data-tve-style="1">
                <div class="tve_btn tve_btn3 tve_nb tve_orange tve_bigBtn" style="margin-top: 0px;">
                    <a class="tve_btnLink" href="">
                                <span class="tve_left tve_btn_im">
                                    <i></i>
                                    <span class="tve_btn_divider"></span>
                                </span>
                        <span class="tve_btn_txt">Click here to get the special webinar offer! &GT;&GT;</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="tve_lp_footer tve_empty_dropzone tve_drop_constraint" data-forbid=".thrv_page_section,.sc_page_section">
    <p class="tve_p_center" style="color: #333333;font-size: 17px;margin-top: 10px;">© {tcb_current_year} Webinar Landing Page.
        All
        rights Reserved | Disclaimer</p>
</div>