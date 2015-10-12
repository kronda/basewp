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
$config_post_grid = json_encode(array(
    "tve_lb_type" => "tve_post_grid",
    "text_type" => "summary",
    "post_types" => array(
        "post" => "true",
        "page" => "false",
        "attachment" => "false",
        "product" => "false",
        "product_variation" => "false",
        "shop_order" => "false",
        "shop_order_refund" => "false",
        "shop_coupon" => "false",
        "shop_webhook" => "false"),
    "posts_per_page" => "8",
    "posts_start" => 0,
    "orderby" => "date",
    "order" => "DESC",
    "recent_days" => "0",
    "filters" => array("category" => "", "tag" => "", "tax" => "", "author" => "", "posts" => ""),
    "columns" => "4",
    "display" => "grid",
    "layout" => array("featured_image", "title", "text"),
    "action" => "tve_do_post_grid_shortcode",
    "teaser_layout" => array(
        "featured_image" => "true",
        "title" => "true",
        "text" => "true",
        "read_more" => "false"
    ),
));
?>
<div class="tve_lp_header tve_content_width tve_empty_dropzone">
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1" id="contentfocused-ps">
        <div class="out" style="background-color: #449bbe;">
            <div class="in lightSec">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_columns" style="margin-top: 0;margin-bottom: 40px;">
                        <div class="tve_colm tve_foc tve_df tve_ofo ">
                            <div style="margin-top: 0;margin-bottom: 0;width: 252px;"
                                 class="thrv_wrapper tve_image_caption aligncenter">
                                    <span class="tve_image_frame">
                                        <img class="tve_image"
                                             src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/content-focus-logo.png' ?>"
                                             style="width: 252px"/>
                                    </span>
                            </div>
                        </div>
                        <div class="tve_colm tve_tfo tve_df tve_lst">
                            <p class="tve_p_right hidden-on-mobile"
                               style="color: #fff; font-size: 18px;margin-top: 20px;margin-bottom: 0;">
                                <span class="bold_text">
                                    <a class="tve_evt_manager_listen" href="" data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                                        Subscribe
                                    </a>
                                    <a href="//imimpact.com" target="_blank">&nbsp;&nbsp;&nbsp; &nbsp;Blog</a>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="thrv_wrapper thrv_content_container_shortcode">
                        <div class="tve_clear"></div>
                        <div class="tve_center tve_content_inner" style="width: 950px;min-width:50px; min-height: 2em;">
                            <h1 class="tve_p_center"
                                style="color: #fff; font-size: 44px;margin-top: 0;margin-bottom: 20px;">
                                Unique Writing Tips for <font color="#fda443">More Engaging</font>
                                Website Content
                            </h1>

                            <p class="tve_p_center"
                               style="color: #fff; font-size: 20px;margin-top: 0;margin-bottom: 25px;">
                                In this above-the-fold section, your aim should be to communicate the main purpose and
                                focus of
                                your website as succinctly as possible.
                            </p>

                            <div class="thrv_wrapper thrv_button_shortcode tve_centerBtn"
                                 data-tve-style="1">
                                <div class="tve_btn tve_orange tve_normalBtn tve_btn5 tve_nb">
                                    <a class="tve_btnLink tve_evt_manager_listen" href="" data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                                        <span class="tve_left tve_btn_im">
                                            <i class="tve_sc_icon contentfocused-icon-speaker"
                                               data-tve-icon="contentfocused-icon-speaker"
                                               style="background-image: none; font-size: 30px;"></i>
                                        </span>
                                        <span class="tve_btn_txt">GET ACCESS TO THE LATEST ARTICLES</span>
                                    </a>
                                </div>
                            </div>
                            <p class="tve_p_center"
                               style="color: #fff; font-size: 20px;margin-top: 0;margin-bottom: 0;">
                                Check out Our Latest Posts:
                            </p>
                        </div>
                        <div class="tve_clear"></div>
                    </div>
                    <div class="thrv_wrapper" style="margin-top: 0;">
                        <hr class="tve_sep tve_sep2"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper" style="margin-top: 0;margin-bottom: 0;">
        <hr class="tve_sep tve_sep1"/>
    </div>
</div>
<div class="tve_lp_content tve_editor_main_content tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_post_grid">
        <div class="thrive-shortcode-config" style="display: none !important">__CONFIG_post_grid__<?php echo $config_post_grid; ?>__CONFIG_post_grid__</div>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #e3e3e3;">
            <div class="in darkSec" style="padding: 40px;">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_columns tve_clearfix">
                        <div class="tve_colm tve_oth">
                            <div style="width: 178px;margin-top: 20px;"
                                 class="thrv_wrapper tve_image_caption aligncenter img_style_circle">
                                        <span class="tve_image_frame">
                                            <img class="tve_image"
                                                 src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/shane_melaugh2.jpg' ?>"
                                                 style="width: 178px"/>
                                        </span>
                            </div>
                            <h5 class="tve_p_center"
                                style="color: #449bbe; font-size: 22px;margin-top: 0;margin-bottom: 0;">
                                Shane Melaugh
                            </h5>
                            <h5 class="tve_p_center"
                                style="color: #313131; font-size: 22px;margin-top: 0;margin-bottom: 0;">
                                Thrive Themes
                            </h5>
                        </div>
                        <div class="tve_colm tve_tth tve_lst">
                            <h3 style="color: #449bbe; font-size: 30px;margin-top: 0;margin-bottom: 20px;">
                                About the Author
                            </h3>

                            <p style="color: #313131; font-size: 18px;line-height: 1.833em;margin-top: 0;margin-bottom: 0;">
                                This is a brief section about the site’s owner or main author. On this homepage
                                template, we’re providing a one-page overview over what the site is about, so a brief
                                introduction of the author is a good fit. Remember that an introduction like this should
                                have a personal touch and also convey the authority of the author. Use this space to
                                explain why the author is worth paying attention to and why he or she is an authority in
                                this space.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_content_container_shortcode">
        <div class="tve_clear"></div>
        <div class="tve_center tve_content_inner" style="width: 850px;min-width:50px; min-height: 2em;">
            <div class="thrv_wrapper thrv_testimonial_shortcode" data-tve-style="5" style="margin-top: 60px;">
                <div class="tve_ts tve_ts9 tve_blue">
                    <div class="tve_ts_t">
                        <span class="tve_ts_c tve_right"></span>

                        <div class="tve_ts_cn tve_right">
                            <span class="tve_ts_ql"></span>

                            <p>
                                “Testimonials are always a great way to warm new readers up to a website, person or product. Add
                                some of your testimonials to this section for some social proof.”
                            </p>
                            <h6 class="tve_p_right">
                                Jane Smith
                            </h6>
                        </div>
                        <div class="tve_clear"></div>
                    </div>
                    <div class="tve_ts_o">
                        <div class="tve_ts_imc">
                            <img
                                src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/girl-reversed.jpg' ?>"
                                alt=""/>
                        </div>
                <span>
                    <b>Jane Smith</b>
                </span>
                    </div>
                    <div class="tve_clear"></div>
                </div>
            </div>
            <div class="thrv_wrapper thrv_testimonial_shortcode" data-tve-style="5">
                <div class="tve_ts tve_ts9 tve_blue">
                    <div class="tve_ts_t">
                        <span class="tve_ts_c tve_right"></span>

                        <div class="tve_ts_cn tve_right">
                            <span class="tve_ts_ql"></span>

                            <p>
                                “The testimonials can be about the author, about the site in general or about a product or
                                service you provide. You can also harvest some of your most positive blog comments for
                                testimonials.”
                            </p>
                            <h6 class="tve_p_right">
                                Shane Melaugh
                            </h6>
                        </div>
                        <div class="tve_clear"></div>
                    </div>
                    <div class="tve_ts_o">
                        <div class="tve_ts_imc">
                            <img
                                src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/shanemelaugh2.jpg' ?>"
                                alt=""/>
                        </div>
                            <span>
                                <b>Shane Melaugh</b>
                            </span>
                    </div>
                    <div class="tve_clear"></div>
                </div>
            </div>
            <div class="thrv_wrapper thrv_columns tve_clearfix">
                <div class="tve_colm tve_tth">
                    <div class="thrv_wrapper thrv_button_shortcode tve_fullwidthBtn"
                         data-tve-style="1">
                        <div class="tve_btn tve_orange tve_normalBtn tve_btn5 tve_nb">
                            <a class="tve_btnLink tve_evt_manager_listen" href=""
                               data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                                <span class="tve_left tve_btn_im">
                                    <i class="tve_sc_icon contentfocused-icon-speaker"
                                       data-tve-icon="contentfocused-icon-speaker"
                                       style="background-image: none;font-size: 30px;"></i>
                                </span>
                                <span class="tve_btn_txt">SUBSCRIBE TO NEVER MISS A POST</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="tve_colm tve_oth tve_lst">
                    <div class="thrv_wrapper thrv_button_shortcode tve_fullwidthBtn"
                         data-tve-style="1">
                        <div class="tve_btn tve_black tve_normalBtn tve_btn5 tve_nb">
                            <a class="tve_btnLink" href="" style="padding-left: 5px;padding-right: 5px;">
                                <span class="tve_left tve_btn_im">
                                    <i class="tve_sc_icon contentfocused-icon-arrow"
                                       data-tve-icon="contentfocused-icon-arrow"
                                       style="background-image: none;font-size: 30px;"></i>
                                </span>
                                <span class="tve_btn_txt">GO TO THE BLOG</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tve_clear"></div>
    </div>
</div>

<div class="tve_lp_footer tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_columns">
        <div class="tve_colm tve_twc">
            <p style="color: #3d3a3a; font-size: 20px;margin-top: 0;margin-bottom: 0;">
                Copyright {tcb_current_year} by Company Name
            </p>
        </div>
        <div class="tve_colm tve_twc tve_lst">
            <p style="color: #449bbe; font-size: 20px;margin-top: 0;margin-bottom: 0;"
               class="tve_p_right">
                <a href="#">Disclaimer</a> - <a href="#">Contact</a>
            </p>
        </div>
    </div>
</div>