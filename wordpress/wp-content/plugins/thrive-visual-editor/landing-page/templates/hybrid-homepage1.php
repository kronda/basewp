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
        <div class="out" style="background-color: #ffffff;">
            <div class="in darkSec">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_columns" style="margin-top: 0;margin-bottom: 0;">
                        <div class="tve_colm tve_foc tve_df tve_ofo ">
                            <div style="margin-top: 0;margin-bottom: 0;width: 240px;"
                                 class="thrv_wrapper tve_image_caption aligncenter">
                                <span class="tve_image_frame">
                                    <img class="tve_image"
                                         src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/hybrid-homepage-logo1.png' ?>"
                                         style="width: 240px"/>
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
    <div class="thrv_wrapper" style="margin-top: 0;margin-bottom: 0;">
        <hr class="tve_sep tve_sep1"/>
    </div>
</div>
<div class="tve_lp_content tve_editor_main_content tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_content_container_shortcode">
        <div class="tve_clear"></div>
        <div class="tve_center tve_content_inner" style="width: 950px;min-width:50px; min-height: 2em;">
            <h1 class="tve_p_center rft" style="color: #095fbb; font-size: 35px;margin-top: 0;margin-bottom: 10px;">
                Supercharge Your Website With Brilliant Content
                to <font color="#89b023">Improve User Flow</font> and <font color="#89b023">Generate More Sales</font>
            </h1>
        </div>
        <div class="tve_clear"></div>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out pswr" style="background-color: #e3e3e3;">
            <div class="in darkSec pddbg pdfbg"
                 style="max-width: 1078px; box-shadow: none; min-height: 283px; box-sizing: border-box; background-image: url('<?php echo TVE_LANDING_PAGE_TEMPLATE . "/css/images/hybrid-homepage1-ps.jpg" ?>');"
                 data-width="1078" data-height="283">
                <div class="cck clearfix">
                    &nbsp;
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #1c5a9c;">
            <div class="in lightSec">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_content_container_shortcode">
                        <div class="tve_clear"></div>
                        <div class="tve_center tve_content_inner" style="width: 980px;min-width:50px; min-height: 2em;">
                            <p class="tve_p_center"
                               style="color: #fff; font-size: 20px;margin-top: 0;margin-bottom: 10px;">
                                This blog is aimed at helping you <span class="bold_text">increase the number of visitors</span>
                                and convince them to stay.
                                <br>
                                I will show you how to communicate the main benefits of your product and how to build
                                your
                                layouts in order to achieve an <span class="bold_text">engaging presentation that puts your product first.</span>
                            </p>
                        </div>
                        <div class="tve_clear"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="thrv_wrapper thrv_button_shortcode tve_centerBtn hybrid-homepage-btn"
         data-tve-style="1">
        <div class="tve_btn tve_green tve_normalBtn tve_btn8 tve_nb">
            <a class="tve_btnLink tve_evt_manager_listen" href=""
               data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                <span class="tve_left tve_btn_im">
                    <i></i>
                </span>
                <span class="tve_btn_txt">GET ACCESS TO THE LATEST ARTICLES</span>
            </a>
        </div>
    </div>
    <div class="thrv_wrapper thrv_columns tve_clearfix">
        <div class="tve_colm tve_oth">
            <div class="thrv_wrapper thrv_icon aligncenter" style="margin-bottom: 40px;">
                            <span style="font-size: 55px;"
                                  class="tve_black tve_sc_icon hybrid-icon-chart"
                                  data-tve-icon="hybrid-icon-chart"></span>
            </div>
            <p class="tve_p_center" style="color: #636363; font-size: 16px;margin-top: 0;margin-bottom: 20px;">
                Praesent tempor nibh ut ipsum ornare, in ornare nisi convallis.
                Sed quis orci pharetra, vestibulum dolor vitae, malesuada lacus. Aliquam vel convallis tellus.
            </p>
        </div>
        <div class="tve_colm tve_oth">
            <div class="thrv_wrapper thrv_icon aligncenter" style="margin-bottom: 40px;">
                        <span style="font-size: 55px;"
                              class="tve_black tve_sc_icon hybrid-icon-speaker"
                              data-tve-icon="hybrid-icon-speaker"></span>
            </div>
            <p class="tve_p_center" style="color: #636363; font-size: 16px;margin-top: 0;margin-bottom: 20px;">
                Sed quis orci pharetra, vestibulum dolor vitae, malesuada lacus. Aliquam vel convallis tellus.
            </p>
        </div>
        <div class="tve_colm tve_thc tve_lst">
            <div class="thrv_wrapper thrv_icon aligncenter" style="margin-bottom: 40px;">
                        <span style="font-size: 55px;"
                              class="tve_black tve_sc_icon hybrid-icon-ribbon"
                              data-tve-icon="hybrid-icon-ribbon"></span>
            </div>
            <p class="tve_p_center" style="color: #636363; font-size: 16px;margin-top: 0;margin-bottom: 20px;">
                Phasellus est lacus, congue sodales urna tristique, vehicula vulputate ligula. Phasellus leo
                dui, adipiscing.
            </p>
        </div>
    </div>
    <h2 class="tve_p_center rft" style="color: #095fbb; font-size: 30px;margin-top: 0;margin-bottom: 0;">
        Recent Articles
    </h2>

    <div class="thrv_wrapper" style="margin-left: -31px;margin-right: -31px;margin-top: 5px;">
        <hr class="tve_sep tve_sep2"/>
    </div>
    <div class="thrv_wrapper thrv_post_grid">
        <div class="thrive-shortcode-config" style="display: none !important">__CONFIG_post_grid__<?php echo $config_post_grid; ?>__CONFIG_post_grid__</div>
    </div>

    <div class="thrv_wrapper" style="margin-left: -31px;margin-right: -31px;margin-bottom: -23px;">
        <hr class="tve_sep tve_sep2"/>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #e9e9e9;">
            <div class="in darkSec" style="padding: 40px;">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_columns tve_clearfix">
                        <div class="tve_colm tve_oth">
                            <div style="width: 178px;margin-top: 0;margin-bottom: 0;"
                                 class="thrv_wrapper tve_image_caption aligncenter img_style_circle">
                                        <span class="tve_image_frame">
                                            <img class="tve_image tve_brdr_solid"
                                                 src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/shane_melaugh2.jpg' ?>"
                                                 style="border: 3px solid #dddddd; width: 178px"/>
                                        </span>
                            </div>
                            <h2 class="tve_p_center rft"
                                style="color: #095fbb; font-size: 30px;margin-top: 0;margin-bottom: 0;">
                                Shane Melaugh
                            </h2>

                            <h3 class="tve_p_center"
                                style="color: #333333; font-size: 24px;margin-top: 0;margin-bottom: 0;">
                                Thrive Themes
                            </h3>
                        </div>
                        <div class="tve_colm tve_tth tve_lst">
                            <h3 style="color: #095fbb; font-size: 30px;margin-top: 0;margin-bottom: 20px;" class="rft">
                                About the Author
                            </h3>

                            <p style="color: #000000; font-size: 18px;line-height: 1.833em;margin-top: 0;margin-bottom: 0;">
                                My entrepreneurial journey started in 2006, when I dropped out of university. I went
                                from offline business in the hardware market to ecommerce to affiliate niche sites and
                                from hungry and desperate to well-fed and successful.
                            </p>

                            <div class="thrv_wrapper thrv_button_shortcode"
                                 data-tve-style="1">
                                <div class="tve_btn tve_blue tve_normalBtn tve_btn2 tve_nb">
                                    <a class="tve_btnLink " href="">
                                        <span class="tve_left tve_btn_im">
                                            <i></i>
                                        </span>
                                        <span class="tve_btn_txt">Read more</span>
                                    </a>
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
        <div class="tve_center tve_content_inner" style="width: 850px;min-width:50px; min-height: 2em;margin-top: 50px;">
            <div class="thrv_wrapper thrv_testimonial_shortcode" data-tve-style="4">
                <div class="tve_ts tve_ts4 tve_blue">
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
                    <div class="tve_ts_t">
                        <span class="tve_ts_c tve_left"></span>

                        <div class="tve_ts_cn tve_left">
                            <span class="tve_ts_ql"></span>

                            <p>
                                Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt
                                mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit
                                voluptatem accusantium doloremque laudantium.
                            </p>
                            <h5 class="tve_p_right">
                                Jane Doe
                            </h5>
                        </div>
                        <div class="tve_clear"></div>
                    </div>
                    <div class="tve_clear"></div>
                </div>
            </div>
            <div class="thrv_wrapper thrv_testimonial_shortcode" data-tve-style="4">
                <div class="tve_ts tve_ts4 tve_blue">
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
                    <div class="tve_ts_t">
                        <span class="tve_ts_c tve_left"></span>

                        <div class="tve_ts_cn tve_left">
                            <span class="tve_ts_ql"></span>

                            <p>
                                In in quam dignissim, volutpat ante nec, scelerisque eros. Vestibulum eu faucibus lacus.
                                Proin est lectus, vulputate in ornare et, vehicula vel felis. Sed non posuere odio. Nam
                                vel eros sit amet arcu eleifend ultricies sit amet et ipsum.
                            </p>
                            <h5 class="tve_p_right">
                                Shane Melaugh
                            </h5>
                        </div>
                        <div class="tve_clear"></div>
                    </div>
                    <div class="tve_clear"></div>
                </div>
            </div>
        </div>
        <div class="tve_clear"></div>
    </div>
    <div class="thrv_wrapper thrv_button_shortcode tve_centerBtn hybrid-homepage-btn-down"
         data-tve-style="1">
        <div class="tve_btn tve_green tve_normalBtn tve_btn8 tve_nb">
            <a class="tve_btnLink tve_evt_manager_listen" href=""
               data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                <span class="tve_left tve_btn_im">
                    <i></i>
                </span>
                <span class="tve_btn_txt">GET ACCESS TO THE LATEST ARTICLES</span>
            </a>
        </div>
    </div>
</div>

<div class="tve_lp_footer tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_social thrv_social_custom tve_centerBtn" style="margin-top: 100px;">
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
                <a href="javascript:void(0)" class="tve_s_link"><span class="tve_s_icon"></span><span class="tve_s_text">Share +1</span><span
                        class="tve_s_count">0</span></a>
            </div>
            <div class="tve_s_item tve_s_pin_share" data-s="pin_share" data-href="{tcb_post_url}">
                <a href="javascript:void(0)" class="tve_s_link"><span class="tve_s_icon"></span><span
                        class="tve_s_text">Pin</span><span
                        class="tve_s_count">0</span></a>
            </div>
            <div class="tve_s_item tve_s_xing_share" data-s="xing_share" data-href="{tcb_post_url}">
                <a href="javascript:void(0)" class="tve_s_link"><span class="tve_s_icon"></span><span class="tve_s_text">Share</span><span
                        class="tve_s_count">0</span></a>
            </div>
        </div>
        <div class="tve_social_overlay"></div>
    </div>

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