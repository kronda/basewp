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
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1" id="phonic-page-section">
        <div class="out" style="background-color: #1399bc;">
            <div class="in lightSec">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="5">
                        <div class="tve_cb tve_cb5 tve_white">
                            <div class="tve_cb_cnt">
                                <div class="thrv_wrapper thrv_content_container_shortcode">
                                    <div class="tve_clear"></div>
                                    <div class="tve_center tve_content_inner"
                                         style="width: 938px;min-width:50px; min-height: 2em;">
                                        <h1 style="color: #fff; font-size: 60px;margin-top: 0;margin-bottom: 0;" class="rft">
                                            Get Our <span class="bold_text"><font color="#fdd657">Bonus
                                                    Episode</font></span> to Discover
                                            Our Best Kept <span class="bold_text">Conversion Secrets!</span>
                                        </h1>
                                    </div>
                                    <div class="tve_clear"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_content_container_shortcode">
        <div class="tve_clear"></div>
        <div class="tve_center tve_content_inner"
             style="width: 850px;min-width:50px; min-height: 2em;margin-top: -100px;">
            <div class="thrv_responsive_video thrv_wrapper" data-type="youtube" data-rel="0"
                 data-controls="0" data-showinfo="0" data-url="//www.youtube.com/embed/J7LqWhHnxOY"
                 data-embeded-url="//www.youtube.com/embed/J7LqWhHnxOY"
                 style="margin-top: 0;margin-bottom: 50px;">
                <div style="display: block;" class="tve_responsive_video_container">
                    <div class="video_overlay"></div>
                    <iframe frameborder="0" allowfullscreen=""
                            src="//www.youtube.com/embed/J7LqWhHnxOY?rel=0&amp;modestbranding=0&amp;controls=0&amp;showinfo=0&amp;autoplay=0&amp;fs=1&amp;wmode=transparent"></iframe>
                </div>
            </div>
        </div>
        <div class="tve_clear"></div>
    </div>
    <p class="tve_p_center" style="color: #666; font-size: 20px;margin-top: 0;margin-bottom: 30px;">
        We've recorded a <span class="bold_text">bonus episode</span> that is <span class="bold_text">available exclusively to our subscribers</span>
        - you can
        <span class="bold_text"><a href="//thrivethemes.com">sign up right here</a></span> to
        get instant access. Plus, you'll get notified whenever we release a new episode, so you'll never miss out.
    </p>

    <div class="thrv_wrapper thrv_button_shortcode tve_centerBtn" data-tve-style="1">
        <div class="tve_btn tve_btn1 tve_nb tve_orange tve_normalBtn" style="margin-bottom: 70px;">
            <a class="tve_btnLink tve_evt_manager_listen" href="" data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                                        <span class="tve_left tve_btn_im">
                                            <i></i>
                                            <span class="tve_btn_divider"></span>
                                        </span>
                <span class="tve_btn_txt">YES, I WANT THE BONUS EPISODE!</span>
            </a>
        </div>
    </div>
    <div class="thrv_wrapper thrv_icon aligncenter" style="margin-bottom: -25px;">
                        <span style="color: #fff;font-size: 25px;"
                              class="tve_blue tve_sc_icon phonic-icon-arrow"
                              data-tve-icon="phonic-icon-arrow"></span>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #1399bc;">
            <div class="in lightSec">
                <div class="cck clearfix">
                    <h2 class="tve_p_center rft" style="color: #fff; font-size: 42px;margin-top: 60px;margin-bottom: 30px;">
                        “Best Part from the Quote Below...”
                    </h2>

                    <div class="thrv_wrapper thrv_testimonial_shortcode" data-tve-style="1" style="margin-bottom: 50px;">
                        <div class="tve_ts tve_ts1 tve_blue">
                            <div class="tve_ts_t">
                                <span class="tve_ts_ql"></span>

                                <p class="tve_p_center">“Aenean sollicitudin, <span class="italic_text bold_text">lorem quis bibendum</span> auctor, nisi elit consequat ipsum, nec
                                    sagittis sem nibh id elit. Duis sed odio sit amet nibh vulputate cursus a sit amet
                                    mauris. <span class="italic_text bold_text">Morbi accumsan ipsum velit.</span> Nam nec tellus a odio tincidunt auctor a ornare
                                    odio.” </p>
                            </div>
                            <div class="tve_ts_o">
                                <img src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/shanemelaugh3.jpg' ?>"
                                     alt=""/>
                                            <span>
                                                <b>Shane Melaugh,</b>
                                                <br/>
                                                Thrive Themes
                                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tve_lp_footer tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #111111;">
            <div class="in darkSec">
                <div class="cck clearfix">
                    <p style="font-size: 16px;color: #666666;margin-top: 0;margin-bottom: 0;" class="tve_p_center">
                        &copy {tcb_current_year} Thrive  Landing Pages |  <a href="#">Disclaimer</a>  |  <a href="#">Privacy Policy</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>