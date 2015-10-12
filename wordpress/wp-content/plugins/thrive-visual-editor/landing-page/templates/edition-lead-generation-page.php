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
</div>
<div class="tve_lp_content_bg thrv_wrapper tve_no_drag tve_lp_content" style="margin: 0">
    <div class="tve_editor_main_content tve_empty_dropzone tve_content_width">
        <div class="thrv_wrapper thrv_page_section" data-tve-style="1" style="" id="edition-booklp-ps">
            <div class="out">
                <div class="in darkSec pddbg"
                     style="background-image: url('<?php echo TVE_LANDING_PAGE_TEMPLATE . "/css/images/edition-curly-thing.png" ?>');
                         box-shadow: none; box-sizing: border-box;max-width: 100vw;">
                    <div class="cck clearfix">
                        <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-top: 50px;">
                            <div class="tve_colm tve_oth">
                                <p>&nbsp;</p>
                            </div>
                            <div class="tve_colm tve_tth tve_lst">
                                <h1 class="rft"
                                    style="color: #333745; font-size: 52px;margin-top: 20px;margin-bottom: 10px;">
                                    <font color="#58ae7c"><span class="bold_text">Free Chapter:</span></font>
                                    Ultimate
                                    Guide to <span class="bold_text">Landing Page Optimization</span>
                                </h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="thrv_wrapper thrv_columns tve_clearfix">
            <div class="tve_colm tve_oth">
                <div style="width: 322px;margin-top: -200px;" class="thrv_wrapper tve_image_caption aligncenter">
                    <span class="tve_image_frame">
                        <img class="tve_image"
                             src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/edition-big-book.png' ?>"
                             style="width: 322px;"/>
                    </span>
                </div>
            </div>
            <div class="tve_colm tve_tth tve_lst">
                <h5 style="color: #666;font-size: 20px;margin-top: 20px;margin-bottom: 30px;"><font color="#333">A
                        fully updated guide</font> to make your landing pages profitable:</h5>
                <ul class="thrv_wrapper">
                    <li class="" style="line-height: 23px;"><span class="bold_text">Surprising case studies:</span>&nbsp;landing
                        page optimization case studies with unexpected outcomes - and how your business can benefit from
                        them.
                    </li>
                    <li class="" style="line-height: 23px;"><span class="bold_text">Tips, Tricks &amp; Tools:</span>
                        discover the best tools for uncovering the right words and images to use on your landing pages
                        &amp; learn how to test your way to groundbreaking conversion rates.
                    </li>
                    <li class="" style="line-height: 23px;">
                        <span class="bold_text">Easy to follow along:</span>&nbsp;simple, step-by-step recipes will help
                        you create pages that boost your business like never before.
                    </li>
                </ul>
            </div>
        </div>
        <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="5">
            <div class="tve_cb tve_cb5 tve_blue" style="margin-bottom: 30px;">
                <div class="tve_cb_cnt">
                    <div class="thrv_wrapper thrv_columns" style="margin-top: 0;margin-bottom: 0;">
                        <div class="tve_colm tve_twc">
                            <h4 style="color: #333333; font-size: 24px;margin-top: 20px;margin-bottom: 0;">
                                <span class="bold_text"><font color="#408c60">Read the first chapter for FREE:</font>
                            </h4>
                        </div>
                        <div class="tve_colm tve_twc tve_lst">
                            <div style="width: 47px;margin-bottom: 0;margin-top: 20px;"
                                 class="thrv_wrapper tve_image_caption alignleft">
                                <span class="tve_image_frame">
                                    <img class="tve_image edition-arrows"
                                         src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/edition-arrows.png' ?>"
                                         style="width: 47px;"/>
                                </span>
                            </div>
                            <div class="thrv_wrapper thrv_button_shortcode tve_rightBtn" data-tve-style="1">
                                <div class="tve_btn tve_btn1 tve_nb tve_blue tve_normalBtn">
                                    <a class="tve_btnLink tve_evt_manager_listen" href=""
                                       data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                                    <span class="tve_left tve_btn_im">
                                        <i></i>
                                        <span class="tve_btn_divider"></span>
                                    </span>
                                        <span class="tve_btn_txt">GET THE FREE CHAPTER</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="thrv_wrapper thrv_page_section" data-tve-style="1" style="" id="edition-page-section-shadow">
            <div class="out" style="background: #fff;">
                <div class="in darkSec"
                     style="">
                    <div class="cck clearfix">
                        <h2 style="color: #333333; font-size: 36px;margin-top: 45px;margin-bottom: 60px;"
                            class="tve_p_center edition-quotes-heading rft">
                            <font color="#333333">What People Are Saying</font>
                        </h2>

                        <div class="thrv_wrapper thrv_columns" style="margin-bottom:50px;">
                            <div class="tve_colm tve_twc">
                                <div class="thrv_wrapper thrv_testimonial_shortcode" data-tve-style="4">
                                    <div class="tve_ts tve_ts4 tve_green">
                                        <div class="tve_ts_o">
                                            <div class="tve_ts_imc">
                                                <img
                                                    src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/shanemelaugh3.jpg' ?>"
                                                    alt=""/>
                                            </div>
                                    <span>
                                        <b>Shane Melaugh,</b>
                                        Thrive Themes
                                    </span>
                                        </div>
                                        <div class="tve_ts_t">
                                            <span class="tve_ts_c tve_left"></span>

                                            <div class="tve_ts_cn tve_left">
                                                <span class="tve_ts_ql"></span>

                                                <p style="color: #666666; font-size: 18px;line-height: 24px;margin-top: 0;margin-bottom: 10px;">
                                                    “Aenean sollicitudin, lorem quis velitis auctor, nisi elit consequat
                                                    ipsum, nec sagittis sem nibh id elit. ”
                                                </p>

                                                <div class="thrv_wrapper thrv_star_rating">
                                            <span class="tve_rating_stars tve_style_star" data-value="5" data-max="5"
                                                  title="5 / 5" style="width:120px">
                                                <span style="width:120px"></span>
                                            </span>
                                                </div>
                                            </div>
                                            <div class="tve_clear"></div>
                                        </div>
                                        <div class="tve_clear"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="tve_colm tve_twc tve_lst">
                                <div class="thrv_wrapper thrv_testimonial_shortcode" data-tve-style="4">
                                    <div class="tve_ts tve_ts4 tve_green">
                                        <div class="tve_ts_o">
                                            <div class="tve_ts_imc">
                                                <img
                                                    src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/f3_small.jpg' ?>"
                                                    alt=""/>
                                            </div>
                                    <span>
                                        <b>Anna Smith,</b>
                                        CEO
                                    </span>
                                        </div>
                                        <div class="tve_ts_t">
                                            <span class="tve_ts_c tve_left"></span>

                                            <div class="tve_ts_cn tve_left">
                                                <span class="tve_ts_ql"></span>

                                                <p style="color: #666666; font-size: 18px;line-height: 24px;margin-top: 0;margin-bottom: 10px;">
                                                    “Aenean sollicitudin, lorem quis velitis auctor, nisi elit consequat
                                                    ipsum, nec sagittis sem nibh id elit -
                                                    <font color="#333">
                                        <span class="italic_text bold_text">
                                           ut nunc elit duiusnam inceptus.
                                        </span>
                                                    </font>”
                                                </p>

                                                <div class="thrv_wrapper thrv_star_rating">
                                            <span class="tve_rating_stars tve_style_star" data-value="5" data-max="5"
                                                  title="5 / 5" style="width:120px">
                                                <span style="width:120px"></span>
                                            </span>
                                                </div>
                                            </div>
                                            <div class="tve_clear"></div>
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
        <div style="width: 1110px;margin-bottom: 10px;margin-top: 10px;"
             class="thrv_wrapper tve_image_caption">
            <span class="tve_image_frame">
                <img class="tve_image"
                     src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/edition-logos.png' ?>"
                     style="width: 1110px;"/>
            </span>
        </div>
        <!--aici-->
    </div>
</div>
<div class="tve_lp_footer tve_empty_dropzone">
    <div class="thrv_wrapper thrv_page_section tve_no_drag" data-tve-style="1">
        <div class="out" style="background: #2c2d32;">
            <div class="in darkSec">
                <div class="cck tve_clearfix">
                    <div class="thrv_wrapper thrv_columns">
                        <div class="tve_colm tve_twc">
                            <p class="tve_p_left"
                               style="margin: 0; padding: 0; color: #6e757c; font-size: 16px;">
                                &copy; {tcb_current_year} Thrive Landing Pages. All rights Reserved
                            </p>
                        </div>
                        <div class="tve_colm tve_twc tve_lst">
                            <p class="tve_p_right"
                               style="margin: 0; padding: 0; color: #6e757c; font-size: 16px;">
                                <a href="#">Disclaimer</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>