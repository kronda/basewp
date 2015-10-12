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
    "posts_per_page" => "4",
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
        "read_more" => "true"
    ),
));
?>
<div class="tve_lp_header tve_content_width tve_empty_dropzone">
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #224f86;">
            <div class="in lightSec">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_columns" style="margin-top: 0;margin-bottom: 0;">
                        <div class="tve_colm tve_foc tve_df tve_ofo ">
                            <div style="margin-top: 0;margin-bottom: 0;width: 232px;"
                                 class="thrv_wrapper tve_image_caption aligncenter">
                                <span class="tve_image_frame">
                                    <img class="tve_image"
                                         src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/hybrid-homepage2-logo.png' ?>"
                                         style="width: 232px"/>
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
                </div>
            </div>
        </div>
    </div>
</div>
<div class="tve_lp_content tve_editor_main_content tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #224f86;">
            <div class="in lightSec">
                <div class="cck clearfix">
                    <h1 class="tve_p_center rft" style="color: #fff;font-size: 40px;margin-top: 0;margin-bottom: 20px;">
                        Supercharge Your Website With Lead Generating Content
                        To <font color="#f4aa3f">Improve User Flow</font> and <font color="#f4aa3f">Generate More
                            Sales</font>
                    </h1>

                    <div class="thrv_wrapper thrv_content_container_shortcode">
                        <div class="tve_clear"></div>
                        <div class="tve_left tve_content_inner" style="width: 980px;min-width:50px; min-height: 2em;">
                            <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-bottom: 0;">
                                <div class="tve_colm tve_oth">
                                    <div style="margin-top: 0;margin-bottom: -140px;width: 289px;"
                                         class="thrv_wrapper tve_image_caption aligncenter">
                                <span class="tve_image_frame">
                                    <img class="tve_image"
                                         src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/hybrid-homepage2-product.png' ?>"
                                         style="width: 289px"/>
                                </span>
                                    </div>
                                </div>
                                <div class="tve_colm tve_tth tve_lst">
                                    <p style="color: #fff; font-size: 19px;margin-top: 10px;margin-bottom: 30px;">
                                        After two years of writing articles about content optimization we’re proud to
                                        share our
                                        <span class="bold_text"><font color="#f4aa3f">free ebook</font></span>
                                        containing the <span class="bold_text">10
                                        Most Important Tips for Creating Engaging Content.</span>
                                    </p>
                                    <h4 style="color: #f6a941; font-size: 26px;margin-top: 0;margin-bottom: 10px;">
                                        Sign up below to get your copy
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="tve_clear"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #39669e;">
            <div class="in darkSec" style="padding-bottom: 0;">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_content_container_shortcode">
                        <div class="tve_clear"></div>
                        <div class="tve_left tve_content_inner" style="width: 980px;min-width:50px; min-height: 2em;">
                            <div class="thrv_wrapper thrv_columns tve_clearfix"
                                 style="margin-top: -60px;margin-bottom: 0;">
                                <div class="tve_colm tve_oth">
                                    <div style="margin-top: 90px;margin-bottom: 0;width: 75px;"
                                         class="thrv_wrapper tve_image_caption alignright">
                                <span class="tve_image_frame">
                                    <img class="tve_image hybrid-homepage-arrow"
                                         src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/hybrid-homepage2-arrow.png' ?>"
                                         style="width: 75px"/>
                                </span>
                                    </div>
                                </div>
                                <div class="tve_colm tve_tth tve_lst">
                                    <div
                                        class="thrv_wrapper thrv_lead_generation tve_clearfix tve_orange tve_3 thrv_lead_generation_horizontal"
                                        data-inputs-count="3" data-tve-style="1">
                                        <div class="thrv_lead_generation_code" style="display: none;"></div>
                                        <div class="thrv_lead_generation_container tve_clearfix">
                                            <div class="tve_lead_generated_inputs_container tve_clearfix">
                                                <div class="tve_lead_fields_overlay"></div>
                                                <div class=" tve_lg_input_container tve_lg_3 tve_lg_input">
                                                    <input type="text" data-placeholder="Your Name"
                                                           placeholder="Your Name"
                                                           value=""
                                                           name="first_name"/>
                                                </div>
                                                <div class=" tve_lg_input_container tve_lg_3 tve_lg_input">
                                                    <input type="text" data-placeholder="Your E-mail Adress"
                                                           placeholder="E-mail"
                                                           value=""
                                                           name="email"/>
                                                </div>
                                                <div
                                                    class="tve_lg_input_container tve_submit_container tve_lg_3 tve_lg_submit">
                                                    <button type="Submit">Send Me the Free Report</button>
                                                </div>
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
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #fff;">
            <div class="in darkSec">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-top: 40px;">
                        <div class="tve_colm tve_oth">
                            <div class="thrv_wrapper thrv_icon aligncenter" style="margin-bottom: 40px;">
                            <span style="font-size: 55px;"
                                  class="tve_white tve_sc_icon hybrid-icon-chart"
                                  data-tve-icon="hybrid-icon-chart"></span>
                            </div>
                            <h3 class="tve_p_center"
                                style="color: #f6a941; font-size: 26px;margin-top: 0;margin-bottom: 20px;">
                                Proven Efficency
                            </h3>

                            <p class="tve_p_center"
                               style="color: #636363; font-size: 16px;margin-top: 0;margin-bottom: 20px;">
                                Praesent tempor nibh ut ipsum ornare, in ornare nisi convallis.
                                Sed quis orci pharetra, vestibulum dolor vitae, malesuada lacus. Aliquam vel
                                convallis
                                tellus.
                            </p>
                        </div>
                        <div class="tve_colm tve_oth">
                            <div class="thrv_wrapper thrv_icon aligncenter" style="margin-bottom: 40px;">
                        <span style="font-size: 55px;"
                              class="tve_white tve_sc_icon hybrid-icon-speaker"
                              data-tve-icon="hybrid-icon-speaker"></span>
                            </div>
                            <h3 class="tve_p_center"
                                style="color: #f6a941; font-size: 26px;margin-top: 0;margin-bottom: 20px;">
                                Over 3000 readers
                            </h3>

                            <p class="tve_p_center"
                               style="color: #636363; font-size: 16px;margin-top: 0;margin-bottom: 20px;">
                                Sed quis orci pharetra, vestibulum dolor vitae, malesuada lacus. Aliquam vel
                                convallis
                                tellus.
                            </p>
                        </div>
                        <div class="tve_colm tve_thc tve_lst">
                            <div class="thrv_wrapper thrv_icon aligncenter" style="margin-bottom: 40px;">
                        <span style="font-size: 55px;"
                              class="tve_white tve_sc_icon hybrid-icon-ribbon"
                              data-tve-icon="hybrid-icon-ribbon"></span>
                            </div>
                            <h3 class="tve_p_center"
                                style="color: #f6a941; font-size: 26px;margin-top: 0;margin-bottom: 20px;">
                                10 awards and counting
                            </h3>

                            <p class="tve_p_center"
                               style="color: #636363; font-size: 16px;margin-top: 0;margin-bottom: 20px;">
                                Phasellus est lacus, congue sodales urna tristique, vehicula vulputate ligula.
                                Phasellus
                                leo
                                dui, adipiscing.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #eaeaea;">
            <div class="in darkSec">
                <div class="cck clearfix">
                    <h4 class="tve_p_center" style="color: #224f86; font-size: 26px;margin-top: 0;margin-bottom: 30px;">
                        Check out the latest posts below
                    </h4>

                    <div class="thrv_wrapper thrv_post_grid">
                        <div class="thrive-shortcode-config" style="display: none !important">__CONFIG_post_grid__<?php echo $config_post_grid; ?>__CONFIG_post_grid__</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #8db3e4;">
            <div class="in darkSec">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_content_container_shortcode">
                        <div class="tve_clear"></div>
                        <div class="tve_left tve_content_inner" style="width: 980px;min-width:50px; min-height: 2em;">
                            <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-bottom: -22px;">
                                <div class="tve_colm tve_oth">
                                    <div style="margin-top: 0;margin-bottom: 0;width: 189px;"
                                         class="thrv_wrapper tve_image_caption aligncenter">
                                <span class="tve_image_frame">
                                    <img class="tve_image"
                                         src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/hybrid-homepage2-guy.png' ?>"
                                         style="width: 189px"/>
                                </span>
                                    </div>
                                </div>
                                <div class="tve_colm tve_tth tve_lst">
                                    <h3 style="color: #000000; font-size: 30px;margin-top: 20px;margin-bottom: 10px;" class="rft">
                                        About the Author
                                    </h3>

                                    <p style="color: #000000; font-size: 18px;margin-top: 0;margin-bottom: 0;">
                                        My entrepreneurial journey started in 2006, when I dropped out of university.
                                        I went from offline business in the hardware market to ecommerce to affiliate
                                        niche
                                        sites and from hungry and desperate to well-fed and successful.
                                    </p>

                                    <div class="thrv_wrapper thrv_button_shortcode"
                                         data-tve-style="1">
                                        <div class="tve_btn tve_black tve_normalBtn tve_btn2 tve_nb">
                                            <a class="tve_btnLink " href="">
                                        <span class="tve_left tve_btn_im">
                                            <i></i>
                                        </span>
                                                <span class="tve_btn_txt">Learn more</span>
                                            </a>
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
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #fff;">
            <div class="in darkSec">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_content_container_shortcode">
                        <div class="tve_clear"></div>
                        <div class="tve_center tve_content_inner"
                             style="width: 850px;min-width:50px; min-height: 2em;margin-top: 50px;">
                            <div class="thrv_wrapper thrv_testimonial_shortcode" data-tve-style="5" style="margin-bottom: 50px;">
                                <div class="tve_ts tve_ts9 tve_orange">
                                    <div class="tve_ts_t">
                                        <span class="tve_ts_c tve_right"></span>

                                        <div class="tve_ts_cn tve_right">
                                            <span class="tve_ts_ql"></span>

                                            <p>
                                                Excepteur sint occaecat cupidatat non proident, sunt in culpa qui
                                                officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde
                                                omnis iste natus error sit
                                            <h6 class="tve_p_right" style=" font-size: 16px;margin-top: 20px;margin-bottom: 0;">
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
                                            <b>John Doe</b>
                                            UI/UX Designer
                                        </span>
                                    </div>
                                    <div class="tve_clear"></div>
                                </div>
                            </div>
                            <div class="thrv_wrapper thrv_testimonial_shortcode" data-tve-style="5" style="margin-bottom: 80px;">
                                <div class="tve_ts tve_ts9 tve_orange">
                                    <div class="tve_ts_t">
                                        <span class="tve_ts_c tve_right"></span>

                                        <div class="tve_ts_cn tve_right">
                                            <span class="tve_ts_ql"></span>

                                            <p>
                                                In in quam dignissim, volutpat ante nec, scelerisque eros. Vestibulum eu
                                                faucibus lacus. Proin est lectus, vulputate in ornare et, vehicula vel
                                                felis. Sed non posuere
                                            </p>
                                            <h6 class="tve_p_right" style=" font-size: 16px;margin-top: 20px;margin-bottom: 0;">
                                                Jane Doe
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
                                            <b>John Doe</b>
                                            UI/UX Designer
                                        </span>
                                    </div>
                                    <div class="tve_clear"></div>
                                </div>
                            </div>
                        </div>
                        <div class="tve_clear"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #224f86;">
            <div class="in darkSec">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-bottom: 0;">
                        <div class="tve_colm tve_oth">
                            <div style="margin-top: -70px;margin-bottom: 0;width: 289px;"
                                 class="thrv_wrapper tve_image_caption aligncenter">
                                <span class="tve_image_frame">
                                    <img class="tve_image"
                                         src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/hybrid-homepage2-product.png' ?>"
                                         style="width: 289px"/>
                                </span>
                            </div>
                        </div>
                        <div class="tve_colm tve_tth tve_lst">
                            <h4 style="color: #ffffff; font-size: 26px;margin-top: 0;margin-bottom: 0;">
                                Sign up below to get your copy of the FREE REPORT
                            </h4>

                            <div
                                class="thrv_wrapper thrv_lead_generation tve_clearfix tve_orange tve_3 thrv_lead_generation_horizontal"
                                data-inputs-count="3" data-tve-style="1" style="margin-top: 10px;">
                                <div class="thrv_lead_generation_code" style="display: none;"></div>
                                <div class="thrv_lead_generation_container tve_clearfix">
                                    <div class="tve_lead_generated_inputs_container tve_clearfix">
                                        <div class="tve_lead_fields_overlay"></div>
                                        <div class=" tve_lg_input_container tve_lg_3 tve_lg_input">
                                            <input type="text" data-placeholder="Your Name"
                                                   placeholder="Your Name"
                                                   value=""
                                                   name="first_name"/>
                                        </div>
                                        <div class=" tve_lg_input_container tve_lg_3 tve_lg_input">
                                            <input type="text" data-placeholder="Your E-mail Adress"
                                                   placeholder="E-mail"
                                                   value=""
                                                   name="email"/>
                                        </div>
                                        <div
                                            class="tve_lg_input_container tve_submit_container tve_lg_3 tve_lg_submit">
                                            <button type="Submit">Send Me the Free Report</button>
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

<div class="tve_lp_footer tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_social thrv_social_custom tve_centerBtn" style="margin-top: 40px;">
        <div class="tve_social_items tve_social_custom tve_style_5 tve_social_medium tve_social_ib">
            <div class="tve_s_item tve_s_fb_share" data-s="fb_share" data-href="{tcb_post_url}">
                <a href="javascript:void(0)" class="tve_s_link"><span class="tve_s_icon"></span><span
                        class="tve_s_text">Share</span><span class="tve_s_count">0</span></a>
            </div>
            <div class="tve_s_item tve_s_t_share" data-s="t_share" data-href="{tcb_post_url}">
                <a href="javascript:void(0)" class="tve_s_link"><span class="tve_s_icon"></span><span
                        class="tve_s_text">Tweet</span><span class="tve_s_count">0</span></a>
            </div>
            <div class="tve_s_item tve_s_in_share" data-s="in_share" data-href="{tcb_post_url}">
                <a href="javascript:void(0)" class="tve_s_link"><span class="tve_s_icon"></span><span
                        class="tve_s_text">Share</span><span class="tve_s_count">0</span></a>
            </div>
            <div class="tve_s_item tve_s_g_share" data-s="g_share" data-href="{tcb_post_url}">
                <a href="javascript:void(0)" class="tve_s_link"><span class="tve_s_icon"></span><span
                        class="tve_s_text">Share +1</span><span
                        class="tve_s_count">0</span></a>
            </div>
            <div class="tve_s_item tve_s_pin_share" data-s="pin_share" data-href="{tcb_post_url}">
                <a href="javascript:void(0)" class="tve_s_link"><span class="tve_s_icon"></span><span
                        class="tve_s_text">Pin</span><span
                        class="tve_s_count">0</span></a>
            </div>
        </div>
        <div class="tve_social_overlay"></div>
    </div>

    <div class="thrv_wrapper thrv_columns">
        <div class="tve_colm tve_twc">
            <p style="color: #3d3a3a; font-size: 18px;margin-top: 0;margin-bottom: 0;">
                Copyright {tcb_current_year} by Company Name
            </p>
        </div>
        <div class="tve_colm tve_twc tve_lst">
            <p style="color: #f4aa3f; font-size: 20px;margin-top: 0;margin-bottom: 0;"
               class="tve_p_right">
                <a href="#">Disclaimer</a> - <a href="#">Contact</a>
            </p>
        </div>
    </div>
</div>