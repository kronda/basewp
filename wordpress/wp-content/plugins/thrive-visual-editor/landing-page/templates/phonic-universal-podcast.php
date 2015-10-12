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
    "posts_per_page" => "6",
    "posts_start" => 0,
    "orderby" => "date",
    "order" => "DESC",
    "recent_days" => "0",
    "filters" => array("category" => "", "tag" => "", "tax" => "", "author" => "", "posts" => ""),
    "columns" => "3",
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

<div class="tve_lp_header tve_empty_dropzone"></div>
<div class="tve_lp_content tve_editor_main_content tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #1399bc;">
            <div class="in lightSec">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="5">
                        <div class="tve_cb tve_cb5 tve_white" style="margin-top: 50px;margin-bottom: 50px;">
                            <div class="tve_cb_cnt">
                                <div class="thrv_wrapper thrv_content_container_shortcode">
                                    <div class="tve_clear"></div>
                                    <div class="tve_center tve_content_inner"
                                         style="width: 938px;min-width:50px; min-height: 2em;">
                                        <h1 class="tve_p_center rft"
                                            style="color: #fff; font-size: 60px;margin-top: 0;margin-bottom: 0;">
                                            The One <span class="bold_text">Conversion Optimization</span>
                                            Podcast You Can’t Afford to Miss!
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
    <div class="thrv_wrapper thrv_icon aligncenter phonic-arrow-pointer" style="margin-top: -25px;">
        <span style="color: #fff;font-size: 25px;"
              class="tve_blue tve_sc_icon phonic-icon-arrow"
              data-tve-icon="phonic-icon-arrow"></span>
    </div>
    <div class="thrv_wrapper thrv_columns tve_clearfix">
        <div class="tve_colm tve_oth">
            <div style="width: 315px;margin-top: -100px;margin-bottom: 0;"
                 class="thrv_wrapper tve_image_caption aligncenter">
                        <span class="tve_image_frame">
                            <img class="tve_image"
                                 src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/phonic_download1.png' ?>"
                                 style="width: 315px;"/>
                        </span>
            </div>
        </div>
        <div class="tve_colm tve_tth tve_lst">
            <h4 style="color: #333; font-size: 28px;margin-top: 0;margin-bottom: 35px;">
                Get <span class="bold_text">every new episode</span> of our podcast sent straight to your inbox + get
                instant access to our <span class="bold_text">“10-step
                Conversion</span> Optimization Guide” completely <span class="bold_text"><font color="#f24c19">for
                        free!</font></span>
            </h4>

            <div class="thrv_wrapper thrv_button_shortcode" data-tve-style="1">
                <div class="tve_btn tve_btn1 tve_nb tve_orange tve_smallBtn">
                    <a class="tve_btnLink tve_evt_manager_listen" href=""
                       data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                        <span class="tve_left tve_btn_im">
                            <i></i>
                            <span class="tve_btn_divider"></span>
                        </span>
                        <span class="tve_btn_txt">GET INSTANT ACCESS</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1" id="phonic-page-section">
        <div class="out" style="background-color: #1399bc;">
            <div class="in lightSec">
                <div class="cck clearfix">
                    <h6 class="tve_p_center" style="color: #fff; font-size: 20px;margin-top: 40px;margin-bottom: 0;">
                        Subscribe to the Best CRO Podcast on the Net:
                    </h6>

                    <div class="thrv_wrapper thrv_content_container_shortcode">
                        <div class="tve_clear"></div>
                        <div class="tve_center tve_content_inner" style="width: 733px;min-width:50px; min-height: 2em;">
                            <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="6">
                                <div class="tve_cb tve_cb6 tve_blue">
                                    <div class="tve_cb_cnt">
                                        <div style="width: 206px;margin-top: 0;margin-bottom: 0;margin-right: 10px;"
                                             class="thrv_wrapper tve_image_caption alignleft">
                                            <span class="tve_image_frame">
                                                <a href="#">
                                                    <img class="tve_image phonic-image-btn"
                                                         src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/phonic_itunes_btn.png' ?>"
                                                         style="width: 206px;"/>
                                                </a>
                                            </span>
                                        </div>
                                        <div style="width: 206px;margin-top: 0;margin-bottom: 0;margin-right: 10px;"
                                             class="thrv_wrapper tve_image_caption alignleft">
                                            <span class="tve_image_frame">
                                                <a href="#">
                                                    <img class="tve_image phonic-image-btn"
                                                         src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/phonic_stitcher_btn.png' ?>"
                                                         style="width: 206px;"/>
                                                </a>
                                            </span>
                                        </div>
                                        <div style="width: 267px;margin-top: 0;margin-bottom: 0;margin-right: 0;"
                                             class="thrv_wrapper tve_image_caption alignleft">
                                            <span class="tve_image_frame">
                                                <a href="#">
                                                    <img class="tve_image phonic-image-btn"
                                                         src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/phonic_soundcloud_btn.png' ?>"
                                                         style="width: 267px;"/>
                                                </a>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tve_clear"></div>
                    </div>
                    <div class="thrv_wrapper">
                        <hr class="tve_sep tve_sep1"/>
                    </div>
                    <h5 class="tve_p_center" style="color: #fff; font-size: 28px;margin-top: 0;margin-bottom: 20px;">
                        Check Out the Latest Podcast Episodes:
                    </h5>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #fff;">
            <div class="in lightSec">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_post_grid" style="margin-top: -125px;">
                        <div class="thrive-shortcode-config" style="display: none !important">__CONFIG_post_grid__<?php echo $config_post_grid; ?>__CONFIG_post_grid__</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_icon aligncenter phonic-arrow-pointer"
         style="margin-bottom: -25px;margin-top: -25px;">
        <span style="color: #fff;font-size: 25px;"
              class="tve_blue tve_sc_icon phonic-icon-arrow"
              data-tve-icon="phonic-icon-arrow"></span>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #1399bc;">
            <div class="in lightSec">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="4">
                        <div class="tve_cb tve_cb4 tve_blue" style="margin-top: 50px;">
                            <div class="tve_hd">
                                <span></span>
                            </div>
                            <div class="tve_cb_cnt">
                                <div class="thrv_wrapper thrv_columns tve_clearfix">
                                    <div class="tve_colm tve_foc tve_df tve_ofo ">
                                        <div style="width: 200px;margin-top: 0;margin-bottom: 0;"
                                             class="thrv_wrapper tve_image_caption img_style_framed aligncenter">
                                            <span class="tve_image_frame">
                                                <img class="tve_image"
                                                     src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/edition-girl.JPG' ?>"
                                                     style="width: 200px;"/>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="tve_colm tve_tfo tve_df tve_lst">
                                        <h5 style="color: #fff; font-size: 28px;margin-top: 10px;margin-bottom: 20px;">
                                            About the Author
                                        </h5>

                                        <p style="color: #fff; font-size: 20px;margin-top: 0;margin-bottom: 0;">
                                            Aenean sollicitudin, <span
                                                class="bold_text italic_text">lorem quis bibendum</span> auctor, nisi
                                            elit
                                            consequat ipsum,
                                            ut nec sagittis sem nibh id elit. Duis sed odio sit amete nibhi vulputate
                                            cursus
                                            a sit amet mauris. <span class="bold_text italic_text">Morbi accumsan ipsum velit.</span>
                                            Nam nec tellus a odio auctor a ornare odio.
                                        </p>

                                        <p style="color: #fff; font-size: 20px;margin-top: 0;margin-bottom: 0;">
                                            <span class="bold_text">
                                                <font color="#fdd657">
                                                    Read More &rsaquo;
                                                </font>
                                            </span>
                                        </p>
                                    </div>
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
        <div class="tve_center tve_content_inner" style="width: 510px;min-width:50px; min-height: 2em;">
            <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="6">
                <div class="tve_cb tve_cb6 tve_white" style="">
                    <div class="tve_cb_cnt">
                        <div data-elem="sc_social_custom">
                            <div class="thrv_wrapper thrv_social thrv_social_custom"
                                 style="margin-top: 0;margin-bottom: 0;">
                                <div
                                    class="tve_social_items tve_social_custom tve_style_1 tve_social_medium tve_social_itb">
                                    <div class="tve_s_item tve_s_t_share" data-s="t_share" data-href="{tcb_post_url}">
                                        <a href="javascript:void(0)" class="tve_s_link"><span
                                                class="tve_s_icon"></span><span
                                                class="tve_s_text">Tweet</span><span class="tve_s_count">0</span></a>
                                    </div>
                                    <div class="tve_s_item tve_s_fb_share" data-s="fb_share" data-href="{tcb_post_url}">
                                        <a href="javascript:void(0)" class="tve_s_link"><span
                                                class="tve_s_icon"></span><span
                                                class="tve_s_text">Share</span><span class="tve_s_count">0</span></a>
                                    </div>
                                    <div class="tve_s_item tve_s_g_share" data-s="g_share" data-href="{tcb_post_url}">
                                        <a href="javascript:void(0)" class="tve_s_link"><span
                                                class="tve_s_icon"></span><span
                                                class="tve_s_text">Share</span><span class="tve_s_count">0</span></a>
                                    </div>
                                </div>
                                <div class="tve_social_overlay"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tve_clear"></div>
    </div>
</div>
<div class="tve_lp_footer tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #111111;">
            <div class="in darkSec">
                <div class="cck clearfix">
                    <p style="font-size: 16px;color: #666666;margin-top: 0;margin-bottom: 0;" class="tve_p_center">
                        &copy {tcb_current_year} Thrive Landing Pages | <a href="#">Disclaimer</a> | <a href="#">Privacy Policy</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>